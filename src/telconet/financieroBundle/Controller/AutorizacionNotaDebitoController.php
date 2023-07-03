<?php

namespace telconet\financieroBundle\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
//use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoDocumentoHistorial;
use telconet\schemaBundle\Form\InfoDetalleSolicitudType;
use Symfony\Component\HttpFoundation\Response;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;
/**
 * InfoDetalleSolicitud controller.
 *
 */
class AutorizacionNotaDebitoController extends Controller implements TokenAuthenticatedController
{
    
    public function indexAction()
    {
     return $this->render('financieroBundle:Autorizaciones:aprobarNotaDebito.html.twig', array());
    }

    /*
    * @Secure(roles="ROLE_")
    */
    public function gridAprobarNotaDebitoAction()
    {
        $request=$this->get('request');
        $session=$request->getSession();
        $idEmpresa=$session->get('idEmpresa');
        $idOficina=$session->get('idOficina');		
        $fechaDesde=explode('T',$request->get("fechaDesde"));
        $fechaHasta=explode('T',$request->get("fechaHasta"));
        $limit=$request->get("limit");
        $start=$request->get("start");
        $page=$request->get("page");
        $em = $this->get('doctrine')->getManager('telconet_financiero');
	$em_comercial = $this->getDoctrine()->getManager('telconet');                
        $arreglo=array();
        if ((!$fechaDesde[0])&&(!$fechaHasta[0]))
        {
            $arrdatos = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                        -> find30NDPorEmpresaPorEstado($idOficina,'Pendiente',$limit, $page, $start,'',$idEmpresa);
        }
        else
        {
            $arrdatos= $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                        ->findNDPorCriterios($idOficina,$fechaDesde[0],$fechaHasta[0],'Pendiente',$limit, $page, $start,'',$idEmpresa);
        }

        if($arrdatos['registros']){
            $datos=$arrdatos['registros'];
            foreach ($datos as $datos):	
                $strMotivo="";
                $punto=$datos->getPuntoId();
                $objPtoCliente=$em_comercial->getRepository('schemaBundle:InfoPunto')->find($punto);                            
                $arrInfoDocumentoFinancieroDet=$em->getRepository('schemaBundle:InfoDocumentoFinancieroDet')->findByDocumentoId($datos->getId());                                
                if($arrInfoDocumentoFinancieroDet){
                    $objAdmiMotivo=$em_comercial->getRepository('schemaBundle:AdmiMotivo')->find($arrInfoDocumentoFinancieroDet[0]->getMotivoId());                    
                    $strMotivo=$objAdmiMotivo->getNombreMotivo();
                }    
                $linkVer = $this->generateUrl('infodocumentonotadebito_show', array('id' => $datos->getId()));
                $arreglo[]= array(
                'id'=>$datos->getId(),
                'pto'=>$objPtoCliente->getLogin(),                    
                'numero'=>$datos->getNumeroFacturaSri(),
                'valorTotal'=> $datos->getValorTotal(),
                'estadoImpresionFact'=>$datos->getEstadoImpresionFact(),
                'feCreacion'=> strval(date_format($datos->getFeCreacion(),"d/m/Y G:i")),
                'usrCreacion'=> $datos->getUsrCreacion(),
                'observacion'=>$datos->getObservacion(),                    
                'motivo'=>$strMotivo,                    
                'linkVer'=> $linkVer
                 );    
            endforeach;
        }
        $total=$arrdatos['total'];

        if (!empty($arreglo))
            $response = new Response(json_encode(array('total' => $total, 'encontrados' => $arreglo)));
        else
        {
            $arreglo[]= array();
            $response = new Response(json_encode(array('total' => $total, 'encontrados' => $arreglo)));
        }		
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }


    /*
    * @Secure(roles="ROLE_")
    */
    public function aprobarNotaDebitoAjaxAction()
    {
        $request=$this->getRequest();        
		$session  = $request->getSession();         
        $usrCreacion=$session->get('user');	
        $idEmpresa=$request->getSession()->get('idEmpresa');
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');        
        $respuesta->setContent("error del Form");  
        $em = $this->getDoctrine()->getManager('telconet_financiero');

        //Obtiene parametros enviados desde el ajax
        $peticion = $this->get('request');
        $parametro = $peticion->get('param');
        $array_valor = explode("|",$parametro);       
        
        $em->getConnection()->beginTransaction();
 	try{  
            foreach($array_valor as $id){            
                $entity = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($id);
                if (!$entity) {
                        throw $this->createNotFoundException('No se encontro la solicitud buscada');
                }
                $entity->setEstadoImpresionFact('Activo');
                $em->persist($entity);
                $em->flush();

                //Grabamos en la tabla de historial de la solicitud
                $entityHistorial= new InfoDocumentoHistorial();
                $entityHistorial->setEstado('Activo');
                $entityHistorial->setDocumentoId($entity);
                $entityHistorial->setUsrCreacion($usrCreacion);
                $entityHistorial->setFeCreacion(new \DateTime('now'));
                $em->persist($entityHistorial);
                $em->flush();						
		//CIERRA ANTICIPOS
                $detallesND = $em->getRepository('schemaBundle:InfoDocumentoFinancieroDet')->findBy(array('documentoId'=>$id),array('pagoDetId'=>'asc'));
                $iPagosCab=0;
                $arrPagoCab=array();
                $pagoIdTemp=0;
                $tieneDiferentesPagos='N';
                $totalDetalles=0;
                foreach($detallesND as $detalleND){
                    $pagoDetNd=$em->getRepository('schemaBundle:InfoPagoDet')->find($detalleND->getPagoDetId());   
                    if($pagoIdTemp==0){
                        $pagoIdTemp=$pagoDetNd->getPagoId()->getId();
                        $totalDetalles=$totalDetalles+$pagoDetNd->getValorPago();
                        $arrPagoCab[$iPagosCab]['entityPago']=$pagoDetNd->getPagoId();                   
                    }
                    elseif($pagoIdTemp!=$pagoDetNd->getPagoId()->getId()){
                        $iPagosCab++;   
                        $totalDetalles=0;           
                        $tieneDiferentesPagos='S';
                        $totalDetalles=$totalDetalles+$pagoDetNd->getValorPago();
                        $pagoIdTemp=$pagoDetNd->getPagoId()->getId();    
                        $arrPagoCab[$iPagosCab]['entityPago']=$pagoDetNd->getPagoId();         
                    }
                    else{
                        $totalDetalles=$totalDetalles+$pagoDetNd->getValorPago();
                    }
                    $arrPagoCab[$iPagosCab]['totalDetalles']=$totalDetalles;
                }
                for($iPagosCab=0;$iPagosCab<count($arrPagoCab);$iPagosCab++){
                    $admiTipoDocumentoFinanciero=$em->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')->find($arrPagoCab[$iPagosCab]['entityPago']->getTipoDocumentoId()->getId());                    
                    if(($admiTipoDocumentoFinanciero->getCodigoTipoDocumento()==='ANT'|| $admiTipoDocumentoFinanciero->getCodigoTipoDocumento()==='ANTS')&&
                      round($arrPagoCab[$iPagosCab]['entityPago']->getValorTotal(),2)==round($arrPagoCab[$iPagosCab]['totalDetalles'],2))      
                    {     
                        $arrPagoCab[$iPagosCab]['entityPago']->setEstadoPago('Cerrado');
                        $comentarioPago=$arrPagoCab[$iPagosCab]['entityPago']->getComentarioPago().";Se Cierra Pago al autorizar ND #".$entity->getNumeroFacturaSri();
                        $arrPagoCab[$iPagosCab]['entityPago']->setComentarioPago($comentarioPago);
                        $em->persist($arrPagoCab[$iPagosCab]['entityPago']);
                        $em->flush(); 
                        $comentarioPagoDet="";
                        $pagoDet=$em->getRepository('schemaBundle:InfoPagoDet')->findByPagoId($arrPagoCab[$iPagosCab]['entityPago']->getId());                        
                        foreach($pagoDet as $detallePago){
                            $detallePago->setEstado('Cerrado');
                            $comentarioPagoDet=$detallePago->getComentario().";Se Cierra Pago al autorizar ND #".$entity->getNumeroFacturaSri();
                            $detallePago->setComentario($comentarioPagoDet);                       
                            $em->persist($detallePago);
                            $em->flush();                        
                        }   
                    }
                }               
                //FIN DE CIERRA ANTICIPOS
            }         
           $em->getConnection()->commit();   
           $respuesta->setContent("");            
       }       
	catch (\Exception $e) {
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            $respuesta->setContent($e->getMessage());            
	}
       

       return $respuesta;
    }

    /*
    * @Secure(roles="ROLE_")
    */
    public function rechazarNotaDebitoAjaxAction()
    {
        $request=$this->getRequest();        
		$session  = $request->getSession();         
        $usrCreacion=$session->get('user');	
        $idEmpresa=$request->getSession()->get('idEmpresa');
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');        
        $respuesta->setContent("error del Form");  
        $em = $this->getDoctrine()->getManager('telconet_financiero');

        //Obtiene parametros enviados desde el ajax
        $peticion = $this->get('request');
        $parametro = $peticion->get('param');
		$motivoId = $peticion->get('motivoId');
        $array_valor = explode("|",$parametro);       
        
        $em->getConnection()->beginTransaction();
 	try{  
            foreach($array_valor as $id):             
                $entity = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($id);
                if (!$entity) {
                        throw $this->createNotFoundException('No se encontro la solicitud buscada');
                }
                $entity->setEstadoImpresionFact('Rechazado');
                $em->persist($entity);
                $em->flush();

				//Grabamos en la tabla de historial de la solicitud
				$entityHistorial= new InfoDocumentoHistorial();
				$entityHistorial->setEstado('Rechazado');
				$entityHistorial->setMotivoId($motivoId);				
				$entityHistorial->setDocumentoId($entity);
				$entityHistorial->setUsrCreacion($usrCreacion);
				$entityHistorial->setFeCreacion(new \DateTime('now'));
                $em->persist($entityHistorial);
                $em->flush();						
				
           endforeach;
             
           $em->getConnection()->commit();   
           $respuesta->setContent("Se aprobaron las solicitudes con exito.");            
       }       
	catch (\Exception $e) {
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            $respuesta->setContent($e->getMessage());            
	}
       return $respuesta;
    }


    public function getMotivosRechazoNotaDebito_ajaxAction() {
        /* Modificacion a utilizacion de estados por modulos */
        //$session = $this->get('request')->getSession();
        //$modulo_activo=$session->get("modulo_activo");
        $em = $this->get('doctrine')->getManager('telconet');
        $datos = $em->getRepository('schemaBundle:AdmiMotivo')
		->findMotivosPorModuloPorItemMenuPorAccion('aprobacionnotadebito','AutorizacionNotaDebito','rechazarnotadebitoajax');
		$arreglo=array();
    //print_r($datos);die;
    foreach($datos as $valor):
        //print_r($entityAdmiTipoSolicitud[0]->getId());
            $arreglo[] = array(
                'idMotivo' => $valor->getId(),
                'descripcion' => $valor->getNombreMotivo(),
                'idRelacionSistema'=>$valor->getRelacionSistemaId()
            );
    endforeach;
    //die;

        $response = new Response(json_encode(array('motivos' => $arreglo)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }   

}
