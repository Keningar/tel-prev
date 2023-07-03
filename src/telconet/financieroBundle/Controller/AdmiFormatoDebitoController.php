<?php

namespace telconet\financieroBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiFormatoDebito;
use telconet\schemaBundle\Form\AdmiFormatoDebitoType;
use telconet\schemaBundle\Form\InfoDebitoRespuestaType;
use telconet\schemaBundle\Entity\AdmiValidacionFormato;
use telconet\schemaBundle\Entity\InfoDebitoRespuesta;
use telconet\schemaBundle\Entity\InfoPagoCab;
use telconet\schemaBundle\Entity\InfoPagoDet;
use telconet\schemaBundle\Entity\InfoDocumentoHistorial;
use telconet\schemaBundle\Entity\InfoDebitoGeneralHistorial;
use telconet\schemaBundle\Entity\InfoDebitoGeneral;
use telconet\schemaBundle\Entity\InfoProcesoMasivoCab;
use telconet\schemaBundle\Entity\InfoProcesoMasivoDet;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoDocumentoFinancieroCab;
use Symfony\Component\HttpFoundation\Response;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\JsonResponse;
use \telconet\schemaBundle\Entity\ReturnResponse;

use \PHPExcel;
use \PHPExcel_IOFactory;
use \PHPExcel_CachedObjectStorageFactory;
use \PHPExcel_Settings;

/**
 * AdmiFormatoDebito controller.
 *
 */
class AdmiFormatoDebitoController extends Controller implements TokenAuthenticatedController
{  
     /**
     * Lists all AdmiFormatoDebito entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager('telconet_financiero');
        return $this->render('financieroBundle:AdmiFormatoDebito:index.html.twig', array());
    }

    
    public function gridAction() {
        $request = $this->getRequest();
        $estado = '';
        $fechaDesde = explode('T', $request->get("fechaDesde"));
        $fechaHasta = explode('T', $request->get("fechaHasta"));
        $estado = $request->get("estado");
        $nombre = $request->get("nombre");
        $limit = $request->get("limit");
        $page = $request->get("page");
        $start = $request->get("start");
        //$user = $this->get('security.context')->getToken()->getUser();
        $idEmpresa = $request->getSession()->get('idEmpresa');

        $em = $this->get('doctrine')->getManager('telconet_general');
        $em1 = $this->get('doctrine')->getManager('telconet_general');
        if ((!$fechaDesde[0]) && (!$fechaHasta[0]) && !$estado && !$nombre ){
            //Cuando sea inicio puedo sacar los 30 registros
            $estado = 'Activo';
            $resultado = $em->getRepository('schemaBundle:AdmiFormatoDebito')->findFormatosDebitoParaGrid($limit,$page,$start);
            $datos = $resultado['registros'];
            $total = $resultado['total'];
        } else {
            $resultado = $em->getRepository('schemaBundle:AdmiFormatoDebito')->ffindFormatosDebitoParaGrid($limit,$page,$start);
            $datos = $resultado['registros'];
            $total = $resultado['total'];
        }
        foreach ($datos as $datos):
            $urlVer = $this->generateUrl('admiformatodebito_show', array('id' => $datos->getId()));
            $urlEditar = $this->generateUrl('admiformatodebito_edit', array('id' => $datos->getId()));
            $urlEliminar = $this->generateUrl('admiformatodebito_delete', array('id' => $datos->getId()));
            $entityBanco=$em1->getRepository('schemaBundle:AdmiBanco')->find($datos->getBancoId()->getId());
            $entityTipoCuenta=$em1->getRepository('schemaBundle:AdmiTipoCuenta')->find($datos->getTipoCuentaId()->getId());
            $linkVer = $urlVer;
            if ($datos->getEstado() != "Inactivo")
                $linkEditar = $urlEditar;
            else
                $linkEditar = "#";
            $linkEliminar = $urlEliminar;
            $arreglo[] = array(
                'idBancoTipoCuenta' => $datos->getId(),
                'banco' => $entityBanco->getDescripcionBanco(),
                'tipoCuenta' => $entityTipoCuenta->getDescripcionCuenta(),
                'fechaCreacion' => strval(date_format($datos->getFeCreacion(), "d/m/Y G:i")),
                'usuarioCreacion' => $datos->getUsrCreacion(),
                'estado' => $datos->getEstado(),
                'linkVer' => $linkVer,
                'linkEditar' => $linkEditar,
                'linkEliminar' => $linkEliminar
            );
        endforeach;
        if (!empty($arreglo))
            $response = new Response(json_encode(array('total' => $total, 'clientes' => $arreglo)));
        else {
            $arreglo[] = array();
            $response = new Response(json_encode(array('total' => $total, 'clientes' => $arreglo)));
        }
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }     
    
    /**
     * Finds and displays a AdmiFormatoDebito entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('schemaBundle:AdmiBancoTipoCuenta')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AdmiFormatoDebito entity.');
        }
        $deleteForm = $this->createDeleteForm($id);
        return $this->render('financieroBundle:AdmiFormatoDebito:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView()
        ));
    }


    public function gridShowAction($id) {
        $request = $this->getRequest();
        $em = $this->get('doctrine')->getManager('telconet_financiero');
        $idEmpresa = $request->getSession()->get('idEmpresa');
        $datos = $em->getRepository('schemaBundle:AdmiFormatoDebito')->findFormatosDebitoPorEstadoPorId($id,'Activo',$idEmpresa);
        
        foreach ($datos as $datos):
		if($datos->getVariableFormatoId()){
			$variable=$datos->getVariableFormatoId()->getDescripcion();
			$variableId=$datos->getVariableFormatoId()->getId();
		}else{
			$variable='';
			$variableId='';
		}	
		$entityValidaciones=$em->getRepository('schemaBundle:AdmiFormatoDebito')->findValidacionesPorFormatoDebitoId($datos->getId());
		//print_r($entityValidaciones);
		if($entityValidaciones)
			$tieneValidacion='S';
		else
			$tieneValidacion='N';
			
		if ($datos->getTipoCampo()=='V') $tipoCampo="Variable";
		if ($datos->getTipoCampo()=='S') $tipoCampo="Secuencial";
		if ($datos->getTipoCampo()=='F') $tipoCampo="Fijo";

		if ($datos->getTipoDato()=='N') $tipoDato="Numerico";
		if ($datos->getTipoDato()=='A') $tipoDato="Alfanumerico";
		

		if ($datos->getOrientacionCaracterRelleno()=='D') $orientacion='Derecha';
		if ($datos->getOrientacionCaracterRelleno()=='I') $orientacion='Izquierda';
		
		if($datos->getCaracterRelleno()=='') $caracterRelleno='Sin relleno';
		elseif($datos->getCaracterRelleno()==' ')$caracterRelleno='Espacio en Blanco';
		elseif($datos->getCaracterRelleno()=='0')$caracterRelleno='Ceros';
		else $caracterRelleno=$datos->getCaracterRelleno();
		
        $arreglo[] = array(
                'id' => $datos->getId(),
                'descripcion' => $datos->getDescripcion(),
                'longitud' => $datos->getLongitud(),
                'caracterRelleno' => $caracterRelleno,
                'tipoCampo' => $tipoCampo,
				'tipoCampoId' => $datos->getTipoCampo(),
                'contenido'=>$datos->getContenido(),
				'posicion'=>$datos->getPosicion(),
				'requiereValidacion'=>$datos->getRequiereValidacion(),
                'orientacionCaracter'=>$orientacion,
				'variable' => $variable,
				'variableId' => $variableId,
				'tieneValidacion'=>$tieneValidacion,
				'tipoDato'=>$tipoDato,
				'tipoDatoId'=>$datos->getTipoDato()
            );
        endforeach;
		//die;
        if (!empty($arreglo))
            $response = new Response(json_encode(array('detalles' => $arreglo)));
        else {
            $arreglo[] = array();
            $response = new Response(json_encode(array('detalles' => $arreglo)));
        }
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }         
    
    /**
     * Displays a form to create a new AdmiFormatoDebito entity.
     *
     */
    public function newAction()
    {
		$em = $this->getDoctrine()->getManager('telconet_financiero');
        $entity = new AdmiFormatoDebito();
        $form   = $this->createForm(new AdmiFormatoDebitoType(), $entity);
		$entityVariables=$em->getRepository('schemaBundle:AdmiVariableFormatoDebito')->findByEstado('Activo');
        return $this->render('financieroBundle:AdmiFormatoDebito:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
			'variables'=>$entityVariables
        ));
    }

    public function createAction()
    {
        $request = $this->getRequest();
        $usuario=$request->getSession()->get('user');        
        $datos_form_extra=$request->request->get('admiformatodebitoextratype');
		//print_r($datos_form_extra);die;
        $detalles_arr=  explode('|', $datos_form_extra['detalles']);       
        $em = $this->getDoctrine()->getManager('telconet_financiero');
		$em1 = $this->getDoctrine()->getManager('telconet');
        $entityFormatoDebito  = new AdmiFormatoDebito();
        $form = $this->createForm(new AdmiFormatoDebitoType(), $entityFormatoDebito);
        $em->getConnection()->beginTransaction();
		$em1->getConnection()->beginTransaction();
        try{
            
            $entityBancoTipoCuenta=$em1->getRepository('schemaBundle:AdmiBancoTipoCuenta')
                ->findBancoTipoCuentaPorBancoPorTipoCuenta($datos_form_extra['bancoId'],$datos_form_extra['tipoCuentaId']); 
										//print_r($entityBancoTipoCuenta);die;				
            for($i=0;$i<count($detalles_arr);$i++){
                if($detalles_arr[$i]){
                    $pos = strpos($detalles_arr[$i], ',');
                    if($pos==0)$detalles_arr[$i]=  substr_replace($detalles_arr[$i],'', $pos, 1);
                    $entity  = new AdmiFormatoDebito();						
                    $detalles=explode(',',$detalles_arr[$i]);
                    $entity->setBancoTipoCuentaId($entityBancoTipoCuenta->getId());					
                    $entity->setDescripcion($detalles[0]);
                    $entity->setLongitud($detalles[1]); 
                    $entity->setCaracterRelleno($detalles[14]);
                    $entity->setTipoCampo($detalles[4]);
                    $entity->setContenido($detalles[5]);
                    $entity->setOrientacionCaracterRelleno($detalles[9]);
					$entity->setRequiereValidacion($detalles[10]);
					$entity->setPosicion($detalles[11]);
					$entity->setTipoDato($detalles[13]);
					//SI TIENE VARIABLE LA GRABA
					if($detalles[8]){
						$entityAdmiVariableFormato=$em
						->getRepository('schemaBundle:AdmiVariableFormatoDebito')
						->find($detalles[8]);
						$entity->setVariableFormatoId($entityAdmiVariableFormato);
					}				
                    $entity->setEstado('Activo');
                    $entity->setUsrCreacion($usuario);
                    $entity->setFeCreacion(new \DateTime('now'));
                    $em->persist($entity);
                    $em->flush();

					//GRABA EL NOMBRE Y EL TIPO DEL ARCHIVO CON EL QUE SE GENERA DEBITO
					$entityBancoTipoCuenta->setNombreArchivoFormato($datos_form_extra['nombreArchivo']);
					$entityBancoTipoCuenta->setTipoArchivoFormato($datos_form_extra['tipoArchivo']);
					$entityBancoTipoCuenta->setSeparadorColumna($datos_form_extra['separadorColumna']);
					$entityBancoTipoCuenta->setConsultarPor($datos_form_extra['consultarPor']);
                    $em1->persist($entityBancoTipoCuenta);
                    $em1->flush();					
                }
            }
            $em->getConnection()->commit();
			$em1->getConnection()->commit();
            return $this->redirect($this->generateUrl('admiformatodebito', array()));
        }
        catch (\Exception $e) {
                $em->getConnection()->rollback();
                $em->getConnection()->close();
                $em1->getConnection()->rollback();
                $em1->getConnection()->close();  				
		$em = $this->getDoctrine()->getManager('telconet_financiero');
        $entity = new AdmiFormatoDebito();
		$entityVariables=$em->getRepository('schemaBundle:AdmiVariableFormatoDebito')->findByEstado('Activo');				
              return $this->render('financieroBundle:AdmiFormatoDebito:new.html.twig', array(
                  'entity' => $entity,
                  'form'   => $form->createView(),
                  'error' => $e->getMessage(),
				  'variables'=>$entityVariables
              ));           
          }
    }


    public function editAction($id)
    {
		$emfn = $this->getDoctrine()->getManager('telconet_financiero');
		$em = $this->getDoctrine()->getManager('telconet');
        $entity = new AdmiFormatoDebito();
        $form   = $this->createForm(new AdmiFormatoDebitoType(), $entity);
		$entityVariables=$emfn->getRepository('schemaBundle:AdmiVariableFormatoDebito')->findByEstado('Activo');
        $entity = $em->getRepository('schemaBundle:AdmiBancoTipoCuenta')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AdmiFormatoDebito entity.');
        }
        $deleteForm = $this->createDeleteForm($id);
        return $this->render(
			'financieroBundle:AdmiFormatoDebito:edit.html.twig', array(
            'entity'      => $entity,
			'form'   => $form->createView(),
			'variables'=>$entityVariables,
            'delete_form' => $deleteForm->createView(),
			'variables'=>$entityVariables			
			));
    }



    public function updateAction($id)
    {
        $request = $this->getRequest();
        $usuario=$request->getSession()->get('user');        
        $datos_form_extra=$request->request->get('admiformatodebitoextratype');
        $detalles_arr=  explode('|', $datos_form_extra['detalles']);       
        $em = $this->getDoctrine()->getManager('telconet_financiero');
		$em1 = $this->getDoctrine()->getManager('telconet');
        $entityFormatoDebito  = new AdmiFormatoDebito();
        $form = $this->createForm(new AdmiFormatoDebitoType(), $entityFormatoDebito);
        $em->getConnection()->beginTransaction();
		$em1->getConnection()->beginTransaction();
        try{
            $entityBancoTipoCuenta=$em1->getRepository('schemaBundle:AdmiBancoTipoCuenta')
                ->findBancoTipoCuentaPorBancoPorTipoCuenta($datos_form_extra['bancoId'],$datos_form_extra['tipoCuentaId']); 	
			//ELIMINA LOS FORMATOS ACTUALES DEL BANCO TIPO CUENTA	
			$entityFormatos=$em->getRepository('schemaBundle:AdmiFormatoDebito')
			->findByBancoTipoCuentaId($entityBancoTipoCuenta->getId());	
			foreach($entityFormatos as $formato):
				$formato->setEstado('Inactivo');
				$formato->setUsrUltMod($usuario);
				$formato->setFeCreacion(new \DateTime('now'));				
                $em->persist($formato);
                $em->flush();
			endforeach;
			//print_r($detalles_arr);die;
			//INGRESA LOS NUEVOS FORAMTOS DE BANCO TIPO CUENTA
            for($i=0;$i<count($detalles_arr);$i++){
                if($detalles_arr[$i]){
                    $pos = strpos($detalles_arr[$i], ',');
                    if($pos==0)$detalles_arr[$i]=  substr_replace($detalles_arr[$i],'', $pos, 1);
                    $entity  = new AdmiFormatoDebito();						
                    $detalles=explode(',',$detalles_arr[$i]);
                    $entity->setBancoTipoCuentaId($entityBancoTipoCuenta->getId());					
                    $entity->setDescripcion($detalles[1]);
                    $entity->setLongitud($detalles[2]); 
                    $entity->setCaracterRelleno($detalles[15]);
                    $entity->setTipoCampo($detalles[5]);
                    $entity->setContenido($detalles[6]);
					if ($detalles[7]=='Derecha')$orientacion='D';
					if ($detalles[7]=='Izquierda')$orientacion='I';
                    $entity->setOrientacionCaracterRelleno($orientacion);
					//SI TIENE VARIABLE LA GRABA
					if($detalles[9]){
						$entityAdmiVariableFormato=$em
						->getRepository('schemaBundle:AdmiVariableFormatoDebito')
						->find($detalles[9]);
						$entity->setVariableFormatoId($entityAdmiVariableFormato);
					}	
					$entity->setRequiereValidacion($detalles[11]);
					$entity->setPosicion($detalles[12]);
					$entity->setTipoDato($detalles[14]);					
                    $entity->setEstado('Activo');
                    $entity->setUsrCreacion($usuario);
                    $entity->setFeCreacion(new \DateTime('now'));
                    $em->persist($entity);
                    $em->flush();					
                }
            }
            $em->getConnection()->commit();
			$em1->getConnection()->commit();
            return $this->redirect($this->generateUrl('admiformatodebito', array()));
        }
        catch (\Exception $e) {
                $em->getConnection()->rollback();
                $em->getConnection()->close();
                $em1->getConnection()->rollback();
                $em1->getConnection()->close();  				
		$emfn = $this->getDoctrine()->getManager('telconet_financiero');
		$em = $this->getDoctrine()->getManager('telconet');
		$entity = $em->getRepository('schemaBundle:AdmiBancoTipoCuenta')->find($id);
		$entityVariables=$emfn->getRepository('schemaBundle:AdmiVariableFormatoDebito')->findByEstado('Activo');				
              return $this->render('financieroBundle:AdmiFormatoDebito:edit.html.twig', array(
                  'entity' => $entity,
                  'form'   => $form->createView(),
                  'error' => $e->getMessage(),
				  'variables'=>$entityVariables
              ));           
          }
    }


    /**
     * Deletes a AdmiFormatoDebito entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('schemaBundle:AdmiFormatoDebito')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AdmiFormatoDebito entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admiformatodebito'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }

    public function estadosAction()
    {
                $arreglo[]= array('idEstado'=>'Procesado','codigo'=> 'PRO','descripcion'=> 'Procesado');
                $arreglo[]= array('idEstado'=>'Pendiente','codigo'=> 'PEN','descripcion'=> 'Pendiente');
				$arreglo[]= array('idEstado'=>'Rechazado','codigo'=> 'REC','descripcion'=> 'Rechazado');       
                $response = new Response(json_encode(array('estados'=>$arreglo)));
		$response->headers->set('Content-type', 'text/json');
		return $response;
		
    }
	
    public function getListadoBancosAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');    
        $peticion = $this->get('request');        
         $es_tarjeta = $peticion->query->get('es_tarjeta');
        $em = $this->getDoctrine()->getManager("telconet");
        $bancos = $em->getRepository('schemaBundle:AdmiBancoTipoCuenta')->findBancosParaDebitos();

        if($bancos && count($bancos)>0)
        {
            $num = count($bancos);           
            $arr_encontrados[]=array('id_banco' =>0, 'descripcion_banco' =>"Seleccion un Banco");
            foreach($bancos as $key => $banco)
            {                
                $arr_encontrados[]=array('id_banco' =>$banco["id"],
                                         'descripcion_banco' =>trim($banco["descripcionBanco"]));
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_banco' => 0 , 'descripcion_banco' => 'Ninguno','banco_id' => 0 , 'banco_descripcion' => 'Ninguno', 'estado' => 'Ninguno'));
                $objJson = json_encode( $resultado);
            }
            else
            {
                $data=json_encode($arr_encontrados);
                $objJson= '{"total":"'.$num.'","encontrados":'.$data.'}';
            }
        }
        else
        {
            $objJson= '{"total":"0","encontrados":[]}';
        }
        
        $respuesta->setContent($objJson);
        return $respuesta;
    }	

	public function getListadoTiposCuentaAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $peticion = $this->get('request');
        $id_banco = $peticion->query->get('id_banco');
        $em = $this->getDoctrine()->getManager("telconet");
        if($id_banco!=0)
        {
            $items = $em->getRepository('schemaBundle:AdmiBancoTipoCuenta')
			->findTiposCuentaPorBancoPorVisibleFormatoParaDebitos($id_banco);
            if($items && count($items)>0)
            {
                $num = count($items);
                $arr_encontrados[]=array('id_cuenta' =>0, 'descripcion_cuenta' =>"Seleccion un Tipo de Cuenta");
                foreach($items as $key => $item)
                {
					if(strtoupper($item["descripcionCuenta"])=='CORRIENTE' || strtoupper($item["descripcionCuenta"])=='AHORRO')
						$descripcion='CUENTA';
					else
						$descripcion=$item["descripcionCuenta"];	
                    $arr_encontrados[]=array('id_cuenta' =>$item["id"],
                                            'descripcion_cuenta' =>trim($descripcion));
                }
                if($num == 0)
                {
                    $resultado= array('total' => 1 ,
                                    'encontrados' => array('id_cuenta' => 0 , 'descripcion_cuenta' => 'Ninguno','cuenta_id' => 0 , 'cuenta_descripcion' => 'Ninguno', 'estado' => 'Ninguno'));
                    $objJson = json_encode( $resultado);
                }
                else
                {
                    $data=json_encode($arr_encontrados);
                    $objJson= '{"total":"'.$num.'","encontrados":'.$data.'}';
                }
            }
            else
            {
                $objJson= '{"total":"0","encontrados":[]}';
            }
        }
        else
        {
            $objJson= '{"total":"0","encontrados":[]}';
        }
        $respuesta->setContent($objJson);
        return $respuesta;
    }

    /**
    * Documentación para funcion 'getListadoBancosTipoCtaDebitosAction'.
    * obtiene el listado de los bancos.
    * @author <amontero@telconet.ec>
    * @since 27/03/2015
    * @return objeto - response
    */   
    public function getListadoBancosTipoCtaDebitosAction()
    {
        $respuesta  = new Response();
        $em         = $this->getDoctrine()->getManager("telconet");
        $bancos     = $em->getRepository('schemaBundle:AdmiBancoTipoCuenta')->findBancosTipoCuentaParaDebitos();        
        $respuesta->headers->set('Content-Type', 'text/json');    
        if($bancos && count($bancos)>0)
        {
            $num               = count($bancos);           
            $arr_encontrados[] = array('id_banco' =>0, 'descripcion_banco' =>"Seleccion un Banco");
            foreach($bancos as $banco)
            {                
				if(trim(strtoupper($banco->getBancoId()->getDescripcionBanco()))=='TARJETAS')
                {
					$nombrebanco=$banco->getTipoCuentaId()->getDescripcionCuenta();
				}
				else
				{
					$nombrebanco=str_replace('BANCO','',$banco->getBancoId()->getDescripcionBanco())." ".
                        str_replace('TARJETA','',$banco->getTipoCuentaId()->getDescripcionCuenta());
				}
                $arr_encontrados[]=array('id_banco' =>$banco->getId(),'descripcion_banco' =>$nombrebanco);
            }

            if($num == 0)
            {
                $resultado = array('total' => 1 ,'encontrados' => array());
                $objJson   = json_encode( $resultado);
            }
            else
            {
                $data    = json_encode($arr_encontrados);
                $objJson = '{"total":"'.$num.'","encontrados":'.$data.'}';
            }
        }
        else
        {
            $objJson = '{"total":"0","encontrados":[]}';
        }
        $respuesta->setContent($objJson);
        return $respuesta;
    }
	
	
	public function getListadoCamposAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
		$em_financiero = $this->getDoctrine()->getManager("telconet_financiero");
		$em_general = $this->getDoctrine()->getManager("telconet");
        $peticion = $this->get('request');
        $variableId = $peticion->query->get('variable');
		$arreglo=array();
		$entityVariable=$em_financiero->getRepository('schemaBundle:AdmiVariableFormatoDebito')->find($variableId);

			$tabla=$entityVariable->getTabla();
			$datos=$em_general->getRepository("schemaBundle:$tabla")->findByEstado('Activo');
			//print_r($datos);die;
			$campo=$entityVariable->getCampo();
			foreach ($datos as $datos):
			$arreglo[] = array(
					'id' => $datos->getId(),
					'descripcion' => $datos->$campo()
				);
			endforeach;
		
        if (!empty($arreglo))
            $response = new Response(json_encode(array('campos' => $arreglo)));
        else {
            $arreglo[] = array();
            $response = new Response(json_encode(array('campos' => $arreglo)));
        }		
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }

    /**
    * Documentación para funcion 'generarDebitosAction'.
    * funcion que muestra los bancos que pueden generar archivos txt para debitos
    * @author <amontero@telconet.ec>
    * @return objeto - json
    
    * @version 1.1
    * @author <amontero@telconet.ec> 
    * Actualizacion: Se agrega prefijo empresa para poder 
    * presentar en el combo las oficinas segun la empresa en sesion
    *
    * @author Luis Cabrera <lcabrera@telconet.ec>
    * @version 1.2 27-11-2017 - Se agrega obtiene información de la empresa para determinar si aplica a ciclos de facturación.
    * 
    * @author Alex Arreaga <atarreaga@telconet.ec>
    * @version 1.3 06-09-2022 - Se agrega modificación para obtener el parámetro del flujo de generación de débitos.
    */    
    /**
    * @Secure(roles="ROLE_87-4177")
    */    
    public function generarDebitosAction()
    {
        $objSession         = $this->getRequest()->getSession();
        $strPrefijoEmpresa  = $objSession->get('prefijoEmpresa');
        $entity             = new AdmiFormatoDebito();
        $objForm            = $this->createForm(new AdmiFormatoDebitoType(), $entity);
        $entityOficinas     = '';
        $emGeneral         = $this->getDoctrine()->getManager('telconet_general');
        $strCodEmpresa     = $objSession->get('idEmpresa');  
        /* @var $serviceComercial \telconet\comercialBundle\Service\ComercialService */
        $serviceComercial   = $this->get('comercial.Comercial');
        $arrayParametros    = array('strEmpresaCod'     => $objSession->get('idEmpresa'),
                                    'strPrefijoEmpresa' => $strPrefijoEmpresa);
        $strAplicaCiclosFac = $serviceComercial->aplicaCicloFacturacion($arrayParametros);
        
        //Se agrega llamado a flujo por empresa para las opciones integradas en la pantalla de generación de débitos.
        $arrayFlujoGenDebito = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                             ->getOne('PARAM_GENERACION_DEBITOS',
                                                      'FINANCIERO','','FLUJO_GENERACION_DEBITO','',
                                                      '','','','', $strCodEmpresa);
        
        $strFlujoGeneracionDebito = $arrayFlujoGenDebito['valor1'] ? $arrayFlujoGenDebito['valor1'] : 'NO';

        return $this->render('financieroBundle:AdmiFormatoDebito:generarDebitos.html.twig', 
            array(
                'prefijoEmpresa'             => $strPrefijoEmpresa,
                'entity'                     => $entity,
                'form'                       => $objForm->createView(),
                'oficinas'                   => $entityOficinas,
                'strAplicaCiclosFacturacion' => $strAplicaCiclosFac,
                'strFlujoGeneracionDebito'   => $strFlujoGeneracionDebito
            )
        );
    }	
    
    
    /**
     * Documentación para funcion 'getImpuestos'.
     * 
     * Funcion que retorna los impuestos dependiendo de los criterios enviados por el usuario en formato JSON
     * 
     * @return Response $objResponse
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0
     * @since 16-06-2016
     */     
    public function getImpuestosAction() 
    {
        $objResponse = new Response();
        $emGeneral   = $this->get('doctrine')->getManager('telconet_general');
        
        $jsonImpuestos = $emGeneral->getRepository('schemaBundle:AdmiImpuesto')->getJSONImpuestosByCriterios(array('strTipoImpuesto' => 'IVA'));
    
        $objResponse->setContent($jsonImpuestos);
        
        return $objResponse;
    }
    
    
    /**
    * Documentación para funcion 'gridGenerarDebitosAction'.
    * funcion que muestra los bancos que pueden geenerar archivos txt para debitos
    * @author <amontero@telconet.ec>
    * @return objeto - json
    * @version 1.1 
    * @author Edson Franco <efranco@telconet.ec>
    * @version 1.2 27-10-2016 - Se añade parámetro 'tipoGrupo' a la consulta de los débitos que se encuentran en la pestaña 'Debitos Generales'
    */     
    public function gridGenerarDebitosAction() 
    {
        $request   = $this->getRequest();
        $idEmpresa = $request->getSession()->get('idEmpresa');
        $em        = $this->get('doctrine')->getManager('telconet_financiero');
        $datos     = $em->getRepository('schemaBundle:AdmiGrupoArchivoDebitoCab')
                        ->findBy( array("empresaCod" => $idEmpresa, "estado" => "Activo", 'tipoGrupo' => 'NORMAL') );
        
        foreach ($datos as $datos)
        {    
            $strDetallesGrupo="";
            $arrDetalleGrupo=$em->getRepository('schemaBundle:AdmiGrupoArchivoDebitoDet')->findByGrupoDebitoId($datos->getId());
            for($indiceDetalle=0;$indiceDetalle<count($arrDetalleGrupo);$indiceDetalle++)
            {
                $strDetallesGrupo.=$arrDetalleGrupo[$indiceDetalle]->getBancoTipoCuentaId()->getBancoId()->getDescripcionBanco()." ".
                    $arrDetalleGrupo[$indiceDetalle]->getBancoTipoCuentaId()->getTipoCuentaId()->getDescripcionCuenta();
                if($indiceDetalle!=(count($arrDetalleGrupo)-1))
                {
                    $strDetallesGrupo.=" - ";
                }
                $arrCadenasAbuscar   = array('TARJETAS','TARJETA','BANCOS','BANCO');
                $arrCadenasReemplazo = array('','','','');
                $strDetallesGrupo    = str_replace($arrCadenasAbuscar, $arrCadenasReemplazo, strtoupper($strDetallesGrupo));
            }
            
            $arreglo[] = array(
                'id' => $datos->getId(),
                'banco' => $datos->getNombreGrupo(),
                'tipoCuentaTarjeta' => $strDetallesGrupo
            );
        }
        if (!empty($arreglo))
            $response = new Response(json_encode(array('detalles' => $arreglo)));
        else {
            $arreglo[] = array();
            $response = new Response(json_encode(array('detalles' => $arreglo)));
        }
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }
    
    /**
     * Documentación para funcion 'creaArchivoAction'.
     * funcion que realiza llamada a script que genera los archivos txt para debitos
     * En la ultima actualizacion se agrega la oficina en los parametros para buscar clientes segun oficina
     * @author <amontero@telconet.ec>
     * @version 1.2
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.3 - Se envía el impuesto al llamar al JAR para generar el débito correspondiente.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.4 - Se envia el valor de cero '0' cuando no seleccionan el impuesto con el que van a generar el débito.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.5 25-08-2016 - Se cambia la función para enviar al script de debitos el idFormato con el cual van a generar el debito, y los id de
     *                           detalles de los debitos que se desean generar, y el nombre del tab activo por el usuario al momento de enviar a
     *                           generar los débitos.
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.6 24-11-2017 - Se agrega el cicloId como parámetro a la generación de Débitos.
     * 
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.6 15-05-2020 - Se agrega el Tipo de Escenario y Filtro del Escenario como parámetro a la generación de Débitos.
     *                         - ESCENARIO_BASE: Escenario basado en la generación de los débitos de los clientes 
     *                                           con saldo pendiente de sus facturas activas.               
     *                         - ESCENARIO_1: Escenario basado en la generación de los débitos de los clientes que 
     *                                        tengan un saldo pendiente de su factura recurrente mensual emitida de acuerdo con cada ciclo.
     * 
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.7 25-05-2020 -Se agrega validación para concatenar el filtro fecha del Escenario1(Generación de Debitos con Filtro de Fecha),
     * con el día de inicio del ciclo correspondiente, para la generación de los débitos.
     * 
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 1.8 06-09-2022 - Se agrega lógica para el flujo de opciones en la generacion de débitos. Las opciones se presentan si el flujo
     *                           cumple con la variable strFlujoGeneracionDebito por la empresa. 
     *                           Cuando no se envíe información de alguna opción ó el flujo por empresa no corresponda, se guarda y envía en
     *                           las variables valor por defecto de las opciones para la continuación del flujo de generación de los débitos.
     *                           Opciones: subir arhcivo excel de clientes, estados de servicio, fechas activación, motivos de rechazo.
     *                           Los valores de las opciones también se agregan y envían como parámetros para los débitos los planificados.
     * 
     * @return objeto - render (Renderiza una vista)
     */   
    public function creaArchivoAction()
    {
        $path_telcos           = $this->container->getParameter('path_telcos');
        $host_scripts          = $this->container->getParameter('host_scripts');    
        $strUploadPath         = $path_telcos.'telcos/web/public/uploads/debitos/';
        $emFinanciero          = $this->get('doctrine')->getManager('telconet_financiero');
        $objRequest            = $this->getRequest();
        $strIdEmpresa          = $objRequest->getSession()->get('idEmpresa');
        $strPrefijoEmpresa     = $objRequest->getSession()->get('prefijoEmpresa');
        $intIdOficina          = $objRequest->getSession()->get('idOficina');
        $intIdOficinaClientes  = $objRequest->get('oficinaId');
        $intCicloId            = $objRequest->get('intCicloId');
        $strIp                 = $objRequest->getClientIp();
        $strUser               = $objRequest->getSession()->get('user');
        $strBanco              = "";
        $strBancosTipoCuentaId = "";
        $arrayBancos           = explode("|",$objRequest->get('debitos'));
        $intIdImpuesto         = $objRequest->get('impuestoId') ? $objRequest->get('impuestoId') : 0;
        $strTabActivo          = $objRequest->get('strTabActivo') ? $objRequest->get('strTabActivo') : '';
        $intIdGrupoDebitoCab   = $objRequest->get('intIdGrupoDebitoCab') ? $objRequest->get('intIdGrupoDebitoCab') : 0;
        $strIdsGrupoDebitoDet  = $objRequest->get('strIdsGrupoDebitoDet') ? $objRequest->get('strIdsGrupoDebitoDet') : '';
        $intIdFormato          = $objRequest->get('intIdFormato') ? $objRequest->get('intIdFormato') : 0;
        $strMensaje            = "";
        $serviceInfoDebitoCab  = $this->get('financiero.InfoDebitoCab');
        $strEscenarioDebito    = $objRequest->get('strEscenarioDebito');
        $strFiltroEscenario    = $objRequest->get('strFiltroEscenario');
        $strCheckPlanificado   = $objRequest->get('strCheckPlanificado');
        $strDatePlanificado    = $objRequest->get('strDatePlanificado');
        $strEstadoPlanificado  = "Planificado";
        $intIdDebitoPlanificado = "0";
        //getParametrosByCriterios
        /* @var $serviceComercial \telconet\comercialBundle\Service\ComercialService */
        $serviceComercial      = $this->get('comercial.Comercial');
        $arrayParametros       = array('strEmpresaCod'     => $strIdEmpresa,
                                       'strPrefijoEmpresa' => $strPrefijoEmpresa);
        $strAplicaCiclosFac    = $serviceComercial->aplicaCicloFacturacion($arrayParametros);
        $strPathJava = $this->container->getParameter('path_java_soporte');
        $strScriptPathJava = $this->container->getParameter('path_script_java_soporte');
        
        $emGeneral           = $this->getDoctrine()->getManager('telconet_general'); 
        $strPathArchivo      = "SIN_ARCHIVO_CLIENTES";
        $strEstadosServicio  = "SIN_ESTADOS_OS";
        $strFechaDesdeAct    = "SIN_FECHA_DESDE_ACT";
        $strFechaHastaAct    = "SIN_FECHA_HASTA_ACT";
        $strMotivosRechazo   = "SIN_MOTIVOS_RECHAZO";
        
        //Se agrega llamado a flujo por empresa para las opciones integradas en la pantalla de generación de débitos.
        $arrayFlujoGenDebito = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                             ->getOne('PARAM_GENERACION_DEBITOS','FINANCIERO','','FLUJO_GENERACION_DEBITO',
                                                      '','','','','', $strIdEmpresa);
        
        $strFlujoGeneracionDebito = $arrayFlujoGenDebito['valor1'] ? $arrayFlujoGenDebito['valor1'] : 'NO';
        
        if($strFlujoGeneracionDebito == 'SI')
        {
            //Opción archivo excel
            $objServiceUtil   = $this->get('schema.Util');
            $strArchivoBase64 = $objRequest->get('strArchivoClientes');
            $strFechaActual   = strval(date_format(new \DateTime('now'), "dmY_Gis"));
            
            if( !empty($strArchivoBase64) )
            {
                $strNombreArchivoCl = $objRequest->get('strNombreArchivoCl');
                
                //Se agrega al nombre del archivo el idCiclo, usuario, fecha actual
                $arrayArchivo     = explode('.', $strNombreArchivoCl);
                $intCountArray    = count($arrayArchivo);
                $strNombreArchivo = $arrayArchivo[0];
                $strExtArchivo    = $arrayArchivo[$intCountArray - 1];
                $strNuevoNombre   = $strNombreArchivo."_".$intCicloId."_".$strUser."_".$strFechaActual.".".trim($strExtArchivo);
                
                //Llamada al parámetro de NFS
                $objAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                    ->findOneBy(array('nombreParametro' => 'PARAM_GENERACION_DEBITOS', 
                                                                      'estado'          => 'Activo'));
                
                if(is_object($objAdmiParametroCab))
                {
                    $objAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                                          'descripcion' => 'CONFIGURACION_NFS',
                                                                          'empresaCod'  => $strIdEmpresa,
                                                                          'estado'      => 'Activo'));
                    
                    if(is_object($objAdmiParametroDet))
                    {
                        $strPathAdicional  = $objAdmiParametroDet->getValor1();
                        $strApp            = $objAdmiParametroDet->getValor2();
                        $strSubModulo      = $objAdmiParametroDet->getValor3();

                        $arrayPathAdicional[] = array('key' => $strPathAdicional);

                        $arrayParamNfs = array('prefijoEmpresa'       => $strPrefijoEmpresa,
                                               'strApp'               => $strApp,  
                                               'strSubModulo'         => $strSubModulo,  
                                               'arrayPathAdicional'   => $arrayPathAdicional,
                                               'strBase64'            => $strArchivoBase64,
                                               'strNombreArchivo'     => $strNuevoNombre,
                                               'strUsrCreacion'       => $strUser);

                        $arrayResponseNfs = $objServiceUtil->guardarArchivosNfs($arrayParamNfs);
                        
                        if($arrayResponseNfs['intStatus'] == 200 )
                        { 
                            $strPathArchivo = $arrayResponseNfs['strUrlArchivo'];
                        }
                        else
                        {
                            $strMensaje = "Error al guardar archivo excel de clientes al NFS.";
                            $this->get('session')->getFlashBag()->add('notice', $strMensaje);
                            $strTabActivo = null;
                        }
                    }
                    else
                    {
                        $strMensaje = "Error, no existe la configuración requerida para PATH ADICIONAL"
                                      . "al cargar archivo de exclusión clientes.";
                        $this->get('session')->getFlashBag()->add('notice', $strMensaje);
                        $strTabActivo = null;  
                    }              
                }
            }
            else
            {
                $strPathArchivo = "SIN_ARCHIVO_CLIENTES"; 
            }
                 
            //Opción de estados servicios
            $strEstadosServicio = $objRequest->get('strEstadosServicio');
            if(empty($strEstadosServicio))
            {
                $strEstadosServicio = "SIN_ESTADOS_OS";
            }
            
            //Opción Activación: fecha desde y fecha hasta
            $strFechaDesdeAct = $objRequest->get('strFechaActDesde');
            $strFechaHastaAct = $objRequest->get('strFechaActHasta');
            if(empty($strFechaDesdeAct) || empty($strFechaHastaAct))
            {
                $strFechaDesdeAct = "SIN_FECHA_DESDE_ACT";
                $strFechaHastaAct = "SIN_FECHA_HASTA_ACT";
            }
            
            //Opción motivos rechazo
            $strMotivosRechazo = $objRequest->get('strMotivosRechazo');
            if(empty($strMotivosRechazo))
            {
                $strMotivosRechazo = "SIN_MOTIVOS_RECHAZO";
            } 
            
        }

        if($strAplicaCiclosFac == 'S' && !$intCicloId)
        {
            $strMensaje = "Es obligatorio seleccionar el CICLO a debitar.";
            $this->get('session')->getFlashBag()->add('notice', $strMensaje);
            $strTabActivo = null;
        }
        if( !empty($strTabActivo) )
        {
            if( $strTabActivo == "debitosEspeciales" )
            {
                $strBancosTipoCuentaId = $intIdGrupoDebitoCab;
                $strIdsGrupoDebitoDet  = str_replace('|', '-', $strIdsGrupoDebitoDet);
                $arrayGrupoDebitoDet   = $strIdsGrupoDebitoDet ? explode("-",$strIdsGrupoDebitoDet) : array();
                
                foreach( $arrayGrupoDebitoDet as $intIdGrupoDet )
                {
                    $entityGrupoDebitoDet = $emFinanciero->getRepository('schemaBundle:AdmiGrupoArchivoDebitoDet')->find($intIdGrupoDet);
                    
                    if( !empty($entityGrupoDebitoDet) )
                    {
                        $objBancoTipoCuentaId = $entityGrupoDebitoDet->getBancoTipoCuentaId();

                        if( !empty($objBancoTipoCuentaId) )
                        {
                            $objBancoId      = $objBancoTipoCuentaId->getBancoId();
                            $objTipoCuentaId = $objBancoTipoCuentaId->getTipoCuentaId();

                            if( !empty($objBancoId) && !empty($objTipoCuentaId) )
                            {
                                $strDescripcionDetalle = $objBancoId->getDescripcionBanco()." ".$objTipoCuentaId->getDescripcionCuenta();
                                $strDescripcionDetalle = $serviceInfoDebitoCab->reemplazarPalabrasEnCadena($strDescripcionDetalle, 
                                                                                                           'CADENA_PALABRAS_REEMPLAZAR');

                                $strBanco .= $strDescripcionDetalle.", ";
                            }//( !empty($objBancoId) && !empty($objTipoCuentaId) )
                        }//( !empty($objBancoTipoCuentaId) )
                    }//( !empty($entityGrupoDebitoDet) )
                }//foreach( $arrayGrupoDebitoDet as $intIdGrupoDet )
            }//( $strTabActivo == "debitosEspeciales" )
            else
            {
                //recorre los bancos que el usuario escogio para generar debitos
                foreach( $arrayBancos as $intBanco )
                {
                    $entityGrupoDebito      = $emFinanciero->getRepository('schemaBundle:AdmiGrupoArchivoDebitoCab')->find($intBanco);
                    
                    if( !empty($entityGrupoDebito) )
                    {
                        $strBanco              .= $entityGrupoDebito->getNombreGrupo().", ";
                        $strBancosTipoCuentaId .= $entityGrupoDebito->getId()."-";
                    }//( !empty($entityGrupoDebito) )
                }
            }//( $strTabActivo == "debitosNormales" )
                        
            //Declara script que genera debitos
            $strScript = '/home/scripts-telcos/md/financiero/sources/generacion-archivos-debitos/dist/generacionArchivosDebitos.jar';
            
            $intCicloId = $intCicloId ? $intCicloId : 0;
            if ($strEscenarioDebito=='ESCENARIO_1')
            {
                $entityAdmiCiclo = $emFinanciero->getRepository('schemaBundle:AdmiCiclo')->find($intCicloId);
                if(!empty($entityAdmiCiclo))
                {
                    $strDiaInicioCiclo  = $entityAdmiCiclo->getFeInicio()->format("d");
                    $strFiltroEscenario = $strDiaInicioCiclo.'/'.$strFiltroEscenario;
                }   
            }
            //Se declaran los parametros del script que genera los debitos
            $strParametros = $host_scripts . "|".$path_telcos."telcos/app/config/parameters.yml|" . 
                             "/home/scripts-telcos/md/financiero/logs/generacion-archivos-debitos/" . "|" . $strPrefijoEmpresa.
                             "-generacion-debitos-"."|".$strIdEmpresa."|".$intIdOficina."|".$intIdOficinaClientes."|".$strUser."|".
                             $strBancosTipoCuentaId."|".$strIp."|".$strUploadPath."|".$intIdImpuesto."|".$strIdsGrupoDebitoDet."|".$intIdFormato.
                             "|" . $strTabActivo . "|" . $intCicloId. "|" . $strEscenarioDebito. "|" . $strFiltroEscenario. "|" .
                             $intIdDebitoPlanificado."|".$strPathArchivo."|".$strEstadosServicio."|".$strFechaDesdeAct."|".$strFechaHastaAct."|".
                             $strMotivosRechazo;
                        
            //Se declara el comando que se encarga de ejecutar el script que genera los debitos
            $strComando = "nohup ".$strPathJava." -jar -Djava.security.egd=file:/dev/./urandom ".$path_telcos.
                          "telcos/app/Resources/scripts/TelcosComunicacionScripts.jar '".$strScript ."' '".$strParametros."' 'NO' '".$host_scripts. 
                          "' '".$strScriptPathJava."' >> ".$path_telcos."telcos/app/Resources/scripts/log/log.txt &";         
            //Ejecuta comando
            if($strCheckPlanificado)
            {   $arrayBancosTipoCuentaId=explode("-",$strBancosTipoCuentaId);
                foreach( $arrayBancosTipoCuentaId as $intBancosTipoCuentaId )
                {
                    if($intBancosTipoCuentaId!="")
                    {
                    // actualizo los parametros para debito planificados
                    $strParametros = $host_scripts . "|".$path_telcos."telcos/app/config/parameters.yml|" . 
                    "/home/scripts-telcos/md/financiero/logs/generacion-archivos-debitos/" . "|" . $strPrefijoEmpresa.
                    "-generacion-debitos-"."|".$strIdEmpresa."|".$intIdOficina."|".$intIdOficinaClientes."|".$strUser."|".
                    $intBancosTipoCuentaId."-"."|".$strIp."|".$strUploadPath."|".$intIdImpuesto."|".$strIdsGrupoDebitoDet."|".$intIdFormato.
                    "|" . $strTabActivo . "|" . $intCicloId. "|" . $strEscenarioDebito. "|" . $strFiltroEscenario. "|" .
                    $intIdDebitoPlanificado."|".$strPathArchivo."|".$strEstadosServicio."|".$strFechaDesdeAct."|".$strFechaHastaAct."|".
                    $strMotivosRechazo;
                    //crea regi()stro debito planificado
                    $entityInfoDebitoGeneral = new InfoDebitoGeneral();
                    $entityInfoDebitoGeneral->setOficinaId($intIdOficinaClientes);
                    $entityInfoDebitoGeneral->setEstado($strEstadoPlanificado);
                    $entityInfoDebitoGeneral->setFeCreacion(new \DateTime($strDatePlanificado));
                    $entityInfoDebitoGeneral->setUsrCreacion($strUser);
                    $entityInfoDebitoGeneral->setIpCreacion($strIp);
                    $entityInfoDebitoGeneral->setGrupoDebitoId($intBancosTipoCuentaId);
                    $entityInfoDebitoGeneral->setEjecutando("N");
                    $entityInfoDebitoGeneral->setPlanificado("S");
                    $entityInfoDebitoGeneral->setFePlanificado(new \DateTime($strDatePlanificado));
                    $entityInfoDebitoGeneral->setParametrosPlanificado($strParametros);
                    $emFinanciero->persist($entityInfoDebitoGeneral);
                    $emFinanciero->flush();
                    }
                }
            }else
            {
                shell_exec($strComando);
            }
            
            
            $strMensaje = "Los debitos de $strBanco se estan procesando. Llegara un correo notificando cuando termine el proceso.";
            $this->get('session')->getFlashBag()->add('subida', $strMensaje);
        }//( !empty($strTabActivo) )
        else
        {
            $strMensaje = "No se pudieron procesar los débitos porque no se enviaron los parámetros correspondientes. Por favor volver a intentarlo.";
            $this->get('session')->getFlashBag()->add('notice', $strMensaje);
        }//( empty($strTabActivo) )
        
        return $this->redirect($this->generateUrl('respuestadebitos_list_debitos_general', array()));       
    }

	public function obtieneSaldoCliente($idPersona,$idPersonaEmpresaRol, $idEmpresa)
	{
		$emfn = $this->get('doctrine')->getManager('telconet_financiero');
		$ingresos=0;$egresos=0;
		$arrValorTotalFacturas=array();$arrValorTotalFacturasProporcionales=array();
		$arrValorTotalPagos=array();$arrValorTotalAnticipos=array();
		$arrValorTotalNotasDebito=array();$arrValorTotalNotasCredito=array();
		//CONSULTA FACTURAS DEL CLIENTE Y SUMA AL SALDO
		$arrValorTotalFacturas=$emfn->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
		->findValorTotalDocumentoPorPersonaPorOfiPorTipoDocPorEmp
		($idPersonaEmpresaRol, 'FAC');
		$arrValorTotalFacturasProporcionales=$emfn->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
		->findValorTotalDocumentoPorPersonaPorOfiPorTipoDocPorEmp
		($idPersonaEmpresaRol, 'FACP');
		//CONSULTA LOS PAGOS DEL CLIENTE y RESTAR AL SALDO
		$arrValorTotalPagos=$emfn->getRepository('schemaBundle:InfoPagoCab')
		->findTotalPagosPorClientePorTipoDocPorEmpresa($idPersonaEmpresaRol,'PAG');
		//CONSULTA LOS ANTICIPOS DEL CLIENTE y RESTAR AL SALDO
		$arrValorTotalAnticipos=$emfn->getRepository('schemaBundle:InfoPagoCab')
		->findTotalPagosPorClientePorTipoDocPorEmpresa($idPersonaEmpresaRol,'ANT');									
		//CONSULTA LAS NOTAS DE DEBITO Y LAS SUMA AL SALDO
		$arrValorTotalNotasDebito=$emfn->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
		->findValorTotalDocumentoPorPersonaPorOfiPorTipoDocPorEmp
		($idPersonaEmpresaRol, 'ND');									
		//CONSULTA LAS NOTAS DE CREDITO Y LAS RESTA AL SALDO
		$arrValorTotalNotasCredito=$emfn->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
		->findValorTotalDocumentoPorPersonaPorOfiPorTipoDocPorEmp
		($idPersonaEmpresaRol, 'NC');
		//CALCULO EL SALDO Y ENVIO COMO VALOR A DEBITAR
		//echo($arrValorTotalFacturas[0]['valorTotal']);die;
		if(count($arrValorTotalFacturas)>0)
			$ingresos+= $arrValorTotalFacturas[0]['valorTotal'];
		if(count($arrValorTotalFacturasProporcionales)>0)	
			$ingresos+= $arrValorTotalFacturasProporcionales[0]['valorTotal'];
		if(count($arrValorTotalNotasDebito)>0)	
		$ingresos+= $arrValorTotalNotasDebito[0]['valorTotal'];
		if(count($arrValorTotalPagos)>0)
			$egresos+= $arrValorTotalPagos[0]['valorTotal'];
		if(count($arrValorTotalAnticipos)>0)	
			$egresos+= $arrValorTotalAnticipos[0]['valorTotal'];
		if(count($arrValorTotalNotasCredito)>0)	
			$egresos+= $arrValorTotalNotasCredito[0]['valorTotal'];
			//echo "in:".$ingresos." out:".$egresos."<br>";
		return $datoObtenido = $ingresos - $egresos;
	}



	public function obtieneSaldoPorPunto($idPunto, $idEmpresa)
	{
		//echo "PUNTO:".$idPunto."<br>";
		$emfn = $this->get('doctrine')->getManager('telconet_financiero');
		$ingresos=0;$egresos=0;
		$arrValorTotalFacturas=array();$arrValorTotalFacturasProporcionales=array();
		$arrValorTotalPagos=array();$arrValorTotalAnticipos=array();$arrValorTotalAnticiposSinCliente=array();
		$arrValorTotalNotasDebito=array();$arrValorTotalNotasCredito=array();
		//CONSULTA FACTURAS DEL CLIENTE Y SUMA AL SALDO
		$arrValorTotalFacturas=$emfn->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
		->findValorTotalDocumentoPorPuntoPorOfiPorTipoDocPorEmp
		($idPunto, 'FAC');
		$arrValorTotalFacturasProporcionales=$emfn->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
		->findValorTotalDocumentoPorPuntoPorOfiPorTipoDocPorEmp
		($idPunto, 'FACP');
		//CONSULTA LOS PAGOS DEL CLIENTE y RESTAR AL SALDO
		$arrValorTotalPagos=$emfn->getRepository('schemaBundle:InfoPagoCab')
		->findTotalPagosPorPuntoPorTipoDocPorEmpresa($idPunto,'PAG');
		//CONSULTA LOS ANTICIPOS DEL CLIENTE y RESTAR AL SALDO
		$arrValorTotalAnticipos=$emfn->getRepository('schemaBundle:InfoPagoCab')
		->findTotalPagosPorPuntoPorTipoDocPorEmpresa($idPunto,'ANT');
		$arrValorTotalAnticiposSinCliente=$emfn->getRepository('schemaBundle:InfoPagoCab')
		->findTotalPagosPorPuntoPorTipoDocPorEmpresa($idPunto,'ANTS');	                
		//CONSULTA LAS NOTAS DE DEBITO Y LAS SUMA AL SALDO
		$arrValorTotalNotasDebito=$emfn->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
		->findValorTotalDocumentoPorPuntoPorOfiPorTipoDocPorEmp
		($idPunto, 'ND');									
		//CONSULTA LAS NOTAS DE CREDITO Y LAS RESTA AL SALDO
		$arrValorTotalNotasCredito=$emfn->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
		->findValorTotalDocumentoPorPuntoPorOfiPorTipoDocPorEmp
		($idPunto, 'NC');
		//CALCULO EL SALDO Y ENVIO COMO VALOR A DEBITAR
		//echo($arrValorTotalFacturas[0]['valorTotal']);
		//echo "FACTURAS:".$arrValorTotalFacturas[0]['valorTotal']."<br>";
		//echo "FACTURAS PROPORCIONALES:".$arrValorTotalFacturasProporcionales[0]['valorTotal']."<br>";
		//echo "ND:".$arrValorTotalNotasDebito[0]['valorTotal']."<br>";
		//echo "PAGOS:".$arrValorTotalPagos[0]['valorTotal']."<br>";
		//echo "ANTICIPOS:".$arrValorTotalAnticipos[0]['valorTotal']."<br>";
		//echo "NC:".$arrValorTotalNotasCredito[0]['valorTotal']."<br>";
		if(count($arrValorTotalFacturas)>0)
			$ingresos+= $arrValorTotalFacturas[0]['valorTotal'];
		if(count($arrValorTotalFacturasProporcionales)>0)	
			$ingresos+= $arrValorTotalFacturasProporcionales[0]['valorTotal'];
		if(count($arrValorTotalNotasDebito)>0)	
		$ingresos+= $arrValorTotalNotasDebito[0]['valorTotal'];
		if(count($arrValorTotalPagos)>0)
			$egresos+= $arrValorTotalPagos[0]['valorTotal'];
		if(count($arrValorTotalAnticipos)>0)	
			$egresos+= $arrValorTotalAnticipos[0]['valorTotal'];
		if(count($arrValorTotalAnticiposSinCliente)>0)	
			$egresos+= $arrValorTotalAnticiposSinCliente[0]['valorTotal'];                 
		if(count($arrValorTotalNotasCredito)>0)	
			$egresos+= $arrValorTotalNotasCredito[0]['valorTotal'];
			//echo "in:".$ingresos." out:".$egresos."<br>";
		//echo "INGRESOS:".$ingresos."<br>";
		//echo "EGRESOS:".$egresos."<br>";
		//echo "Resta:".	($ingresos - $egresos)."<br>";
		return $datoObtenido = $ingresos - $egresos;
		
	}	

    public function asignar_validacion_ajaxAction()
    {
        $request=$this->getRequest();
        $idEmpresa=$request->getSession()->get('idEmpresa');
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');        
        $respuesta->setContent("error del Form");  
        $em = $this->getDoctrine()->getManager('telconet');
		$emfn = $this->getDoctrine()->getManager('telconet_financiero');
        //Obtiene parametros enviados desde el ajax
        $peticion = $this->get('request');
        $arr_validaciones = $peticion->get('detalles');	
		$formatoId=$peticion->get('formatoId');
        $arr_val = explode(",",$arr_validaciones); 
        $a = 0;$x = 0;
        for ($i = 0; $i < count($arr_val); $i++) {
            if ($a == 4) {$a = 0;$x++;}
            if ($a == 0)
                $validaciones[$x]['formatoId'] = $arr_val[$i];			
            if ($a == 1)
                $validaciones[$x]['descripcion'] = $arr_val[$i];				
            if ($a == 2)
                $validaciones[$x]['campoTablaId'] = $arr_val[$i];				
            if ($a == 3)
                $validaciones[$x]['equivalencia'] = $arr_val[$i];				

				$a++;
        }
		//print_r($validaciones);die;
        $em->getConnection()->beginTransaction();
 	try{
			$entityFormato=$emfn->getRepository('schemaBundle:AdmiFormatoDebito')->findOneById($validaciones[$x]['formatoId']);
            for($i=0;$i<count($validaciones);$i++){
                $entity = new AdmiValidacionFormato();           
				$entity->setFormatoDebitoId($entityFormato);
				$entity->setCampoTablaId($validaciones[$i]['campoTablaId']);
				$entity->setEquivalencia($validaciones[$i]['equivalencia']);
				$entity->setUsrCreacion($request->getSession()->get('user'));
				$entity->setFeCreacion(new \DateTime('now'));
                $emfn->persist($entity);
                $emfn->flush();
            }
           $em->getConnection()->commit();
           //$respuesta->setContent("Se grabo el registro con exito.");
		              $response = new Response(json_encode(array('success' =>true )));
    }       
	catch (\Exception $e) {
            $em->getConnection()->rollback();
            $em->getConnection()->close();
                        $response = new Response(json_encode(array('success' =>false ,'errors'=>$e->getMessage())));           
	}
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }	
	
	
    /**
     * Documentación para funcion 'respuestaDebitosAction'.
     * funcion envia al twig los datos de los debitos para poder ser procesados 
     * En la ultima actualizacion se modifica la forma de leer el resultado de debitos, ahora retorna un arreglo.
     * Tambien agrega en el combo el nombre de la oficina a la que pertenecen los clientes del debito
     * @author <amontero@telconet.ec>
     * @return objeto - render (Renderiza una vista)
     * @version 1.2
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.3 - Se aumenta en la descripción de los débitos creados el porcentaje con el cual han sido creados los debitos
     * @since 17-06-2016
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.4 16-08-2017 - Se modifica la lógica para optimizar tiempos de ejecución.
     * 
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.5 05-09-2017 Se modifica para permitir el ingreso de varios archivos.
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.6 28-11-2017 - Presenta únicamente el débito enviado por parámetro vía POST.
     *                         - Se agrega el impuesto en la descripción del debito.
     *                         - Se agrega el ciclo en la descripción del débito.
     * 
     * @author Ricardo Robles <rrobles@telconet.ec>
     * @version 1.7 01-04-2019 Se modifica para que devuelva las cabeceras del 
     *                         débito sin preguntar por el estado 'Pendiente'.
     */ 	
    public function respuestaDebitosAction()
    {
        $request            = $this->getRequest();
        $emfn               = $this->getDoctrine()->getManager('telconet_financiero');
        $emGeneral          = $this->getDoctrine()->getManager('telconet_general');
        $entity             = new InfoDebitoRespuesta();
        $form               = $this->createForm(new InfoDebitoRespuestaType(array('validaFile'=>true)), $entity);
        $arrayDebitos       = array();
        $strFechaDebito     = null;
        $intVariosArchivos  = 0;
        $strNombreCiclo     = "";
        if($request->get('debitosGeneral'))
        {
            $objetoDebitosGeneral = $emfn->getRepository('schemaBundle:InfoDebitoGeneral')->find($request->get('debitosGeneral'));
            $objAdmiGrupoDebito   = $emfn->getRepository('schemaBundle:AdmiGrupoArchivoDebitoCab')->find($objetoDebitosGeneral->getGrupoDebitoId());
            $objImpuesto          = $emGeneral->getRepository('schemaBundle:AdmiImpuesto')->find($objetoDebitosGeneral->getImpuestoId());
            $strBancosSel         = $objAdmiGrupoDebito->getNombreGrupo();
            $strImpuesto          = " (" . $objImpuesto->getDescripcionImpuesto() . ") ";
            if($objetoDebitosGeneral->getCicloId())
            {
                $strNombreCiclo = " [" . $objetoDebitosGeneral->getCicloId()->getNombreCiclo() . "]";
                $strAplicaCiclo = 'S';
            }
            $strFechaDebito       = strval(date_format($objetoDebitosGeneral->getFeCreacion(), "d/m/Y G:i")) . " "
                                  . $strBancosSel . $strImpuesto . $strNombreCiclo;
            $arrayCabecerasDebito = $emfn->getRepository('schemaBundle:InfoDebitoCab')
                                         ->findBy(array('debitoGeneralId'=>$objetoDebitosGeneral->getId()));        
        }
        else
        {
            $strMensaje = "Es obligatorio seleccionar un débito para procesar su(s) archivo(s) de respuesta.";
            $this->get('session')->getFlashBag()->add('notice', $strMensaje);
            return $this->redirect($this->generateUrl('respuestadebitos_list_debitos_general', array()));
        }
        //SI ENCONTRO EL DEBITO GENERAL SELECCIONADO Y SI TIENE ALGUNA CABECERA QUE ESTE AUN PENDIENTE
        if ($objetoDebitosGeneral && count($arrayCabecerasDebito)>0)
        {
            $arrayDebitos[0]['idDebitoGeneral']   = $objetoDebitosGeneral->getId();
            $arrayDebitos[0]['bancoTipoCuentaId'] = $objAdmiGrupoDebito->getBancoTipoCuentaId();
            $arrayDebitos[0]['nombreGrupo']       = $objAdmiGrupoDebito->getNombreGrupo();
            //BUSCO SI TIENE LA CARACTERÍSTICA QUE PERMITE SUBIR VARIOS ARCHIVOS
            $arrayParametros = array(
                "strCodEmpresa"        => $objAdmiGrupoDebito->getEmpresaCod(),
                "intBancoTipoCuentaId" => $objAdmiGrupoDebito->getBancoTipoCuentaId(),
                "strProceso"           => "SUBIDA",
                "strCaracteristica"    => "SUBE_VARIOS_ARCHIVOS"
            );
            $arrayRespuesta            = $emfn->getRepository('schemaBundle:AdmiFormatoDebito')
                    ->obtieneValorCaracteristicaAdicional($arrayParametros);
            if ($arrayRespuesta && $arrayRespuesta[0]["valor"] == 'S')
            {
                $intVariosArchivos = 1;
            }
        }
        return $this->render('financieroBundle:AdmiFormatoDebito:respuestaDebitos.html.twig', array(
            'entity'             => $entity,
            'debitosGenSeleccion'=> $arrayDebitos,
            'fechaDebito'        => $strFechaDebito,
            'form'               => $form->createView(),
            'intVariosArchivos'  => $intVariosArchivos,
            'strAplicaCiclo'     => $strAplicaCiclo
        ));
    }

    /**
    * Documentación para funcion 'leeArchivoAction'.
    * funcion que permite enviar el archivo del debito al script de subida 
    * de respuesta de debito para que sea procesado
    * @author <amontero@telconet.ec>
    * @since 01/04/2015
    * @version 1.1
    * 
    * @author Luis Cabrera <lcabrera@telconet.ec>
    * @version 1.2 04-08-2017 Se implementa la lógica para leer varios archivos
    *
    * @author Luis Cabrera <lcabrera@telconet.ec>
    * @version 1.3 16-10-2017 Se cambia el campo en InfoProcesoMasivosCab que almacena el código del débito.
    *
    * @author Luis Lindao <llindao@telconet.ec>
    * @version 1.4 06-02-2018 se modifica para guardar cuenta bancaria (id cuenta contable) en debito general
    *
    * @author Luis Cabrera <lcabrera@telconet.ec>
    * @version 1.5 05-12-2017 Se agrega la validación del nombre del archivo en base al código de su ciclo.

    * @author Ivan Romero <icromero@telconet.ec>
    * @version 1.6 06-05-2021 Se agrega consumo de microservicio nfs para el guardado de los archivos.
    *
    * @return objeto - render (Renderiza una vista)
    */    
    public function leeArchivoAction()  
    {
        $emfn                 = $this->getDoctrine()->getManager('telconet_financiero');
        $emInfraestructura    = $this->get('doctrine')->getManager('telconet_infraestructura');
        $em                   = $this->getDoctrine()->getManager('telconet');
        $emGeneral            = $this->getDoctrine()->getManager('telconet_general');
        $objRequest           = $this->getRequest();
        $strIdEmpresa         = $objRequest->getSession()->get('idEmpresa');
        $intIdOficina         = $objRequest->getSession()->get('idOficina');
        $strIp                = $objRequest->getClientIp();
        $strUser              = $objRequest->getSession()->get('user');
        $arrayDatosFormFiles  = $objRequest->files->get('respuestadebitoextratype');
        $intKey               = key($arrayDatosFormFiles);
        $strFechaDebito       = $objRequest->request->get('respuestadebitoextratype_fecha'.$intKey);
        $strCodigoDebito      = $objRequest->request->get('respuestadebitoextratype_codigo'.$intKey);
        $intCuentaContableId  = $objRequest->request->get('respuestadebitoextratype_CuentaId');
        $entityDebitoGeneral  = $emfn->getRepository('schemaBundle:InfoDebitoGeneral')->find($intKey);
        $entityGrupoDebito    = $emfn->getRepository('schemaBundle:AdmiGrupoArchivoDebitoCab')->find($entityDebitoGeneral->getGrupoDebitoId());
        $objEmpresa           = $em->getRepository('schemaBundle:InfoEmpresaGrupo')->find($strIdEmpresa);
        $serviceUtil          = $this->get('schema.Util');
        $strTipoMensaje       = 'subida';
        /*@var $serviceInfoDebitoCab \telconet\financieroBundle\Service\InfoDebitoCabService*/
        $serviceInfoDebitoCab = $this->get('financiero.InfoDebitoCab');
        ini_set('upload_max_filesize', '3072M');
        $emfn->getConnection()->beginTransaction();
        $emInfraestructura->getConnection()->beginTransaction();
        $strSubModulo         =$this->container->getParameter('debitos_nfs_submodulo');
        $strApp               =$this->container->getParameter('debitos_nfs_app');
        $strPrefijoEmpresa       = $objRequest->getSession()->get('prefijoEmpresa');
        $strNombreParametroCab = 'DEBITOS_PLANIFICADOS';
        $intIdParametroCargo   = 0;
        $strValorSeleccionado ='Path_adicional 2';
        $strEstadoActivo = 'Activo';
        try
        {
            $objParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                         ->findOneBy( array('nombreParametro' => $strNombreParametroCab, 'estado' => $strEstadoActivo) );

            if($objParametroCab)
            {
                $intIdParametroCargo = $objParametroCab->getId();
            }

            $arrayResultados = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                        ->getParametrosByCriterios( array( 'estado'        => $strEstadoActivo, 
                                                                            'parametroId'   => $intIdParametroCargo,
                                                                            'strEmpresaCod' => $strIdEmpresa,
                                                                            'descripcion'        => $strValorSeleccionado ) );
            $strPathAdicional = $arrayResultados['registros'][0]['valor2'];
            $arrayPathAdicional[] = array('key' => $strPathAdicional);

            //BUSCO EL CODIGO DEL CICLO PARA VALIDAR EL NOMBRE DE LOS ARCHIVOS DE RESPUESTA
            $objDebitoGeneral = $emfn->getRepository('schemaBundle:InfoDebitoGeneral')->find($intKey);
            if(!$objDebitoGeneral)
            {
                throw new \Exception('No existe el débito a procesarse.');
            }
            $strCodigoCiclo   = strtolower($objDebitoGeneral->getCicloId()->getCodigo());

            //Escribo en INFO_PROCESO_MASIVO_CAB
            $entityProcesoMasivoCab = new InfoProcesoMasivoCab();
            $entityProcesoMasivoCab->setTipoProceso("subirRespuestaDebito");
            $entityProcesoMasivoCab->setEmpresaCod($strIdEmpresa); //EMPRESA_COD
            $entityProcesoMasivoCab->setEstado("Pendiente");
            $entityProcesoMasivoCab->setFechaEmisionFactura(new \DateTime($strFechaDebito)); //FECHA_SUBIDA
            $entityProcesoMasivoCab->setFeCreacion(new \DateTime('now'));
            $entityProcesoMasivoCab->setUsrCreacion($strUser);
            $entityProcesoMasivoCab->setIpCreacion($strIp);
            $entityProcesoMasivoCab->setPagoId($intKey); //ID_DEBITO_GENERAL
            $entityProcesoMasivoCab->setIdsOficinas($intIdOficina); //OFICINA_ID
            $strBancoNumeroOrden    = $entityGrupoDebito->getBancoTipoCuentaId()->getId() . '|' . $strCodigoDebito;
            //BANCO_TIPO_CUENTA_ID|CODIGO_DEBITO
            $entityProcesoMasivoCab->setIdsBancosTarjetas($strBancoNumeroOrden);
            $entityProcesoMasivoCab->setPlanValor($objEmpresa->getPrefijo());
            $emInfraestructura->persist($entityProcesoMasivoCab);
            $emInfraestructura->flush();
            foreach($arrayDatosFormFiles as $objArchivos)
            {
                foreach($objArchivos as $objValue)
                {
                    if($objValue)
                    {
                        $strNombreArchivo = strtolower($objValue-> getClientOriginalName());
                        $intPosicion      = strripos($strNombreArchivo, '.');
                        $strExtension     = substr($strNombreArchivo, $intPosicion);
                        if(isset($strCodigoCiclo) && strpos($strNombreArchivo, $strCodigoCiclo . $strExtension) === false)
                        {
                            $strMensaje = ': El nombre del archivo de respuesta no corresponde al ciclo.';
                            throw new \Exception($strMensaje);
                        }

                        $entityRespuestaDebito = new InfoDebitoRespuesta();
                        $entityRespuestaDebito->setNombreBanco(str_replace(" ", "_", $entityGrupoDebito->getNombreGrupo()));
                        $entityRespuestaDebito->setFile($objValue);
                        $entityRespuestaDebito->setEstado("Pendiente");
                        $entityRespuestaDebito->preUpload($objEmpresa->getPrefijo());
                        
                        $strArchivo       = file_get_contents($objValue);

                        $strFileBase64    = base64_encode($strArchivo);
                        
                            //####################################
                            //INICIO DE SUBIR ARCHIVO AL NFS >>>>>
                            //####################################
                           
                            $arrayParamNfs = array(
                                'prefijoEmpresa'       => $strPrefijoEmpresa,
                                'strApp'               => $strApp ,
                                'arrayPathAdicional'   => $arrayPathAdicional,
                                'strBase64'            => $strFileBase64,
                                'strNombreArchivo'     => $entityRespuestaDebito->getPath(),
                                'strUsrCreacion'       => $strUser,
                                'strSubModulo'         => $strSubModulo);
                            
                            $arrayRespNfs = $serviceUtil->guardarArchivosNfs($arrayParamNfs);
                            //##################################
                            //<<<<< FIN DE SUBIR ARCHIVO AL NFS
                            //##################################

                            if ($arrayRespNfs['intStatus'] == 200 )
                            {
                                
                                $entityRespuestaDebito->setPath($arrayRespNfs['strUrlArchivo']);
                            }
                            else
                            {
                                throw new \Exception('Ocurrio un error al subir archivo al servidor Nfs : '.$arrayRespNfs['strMensaje']);
                            }

                        
                        $entityRespuestaDebito->setDebitoGeneralId($intKey);
                        $emfn->persist($entityRespuestaDebito);
                        $emfn->flush();
                        //Permite que no se sobreescriban los archivos debido a que estos obtienen su nombre según la fecha y hora.
                        sleep(1);
                        //Inserto en INFO_PROCESO_MASIVO_DET
                        $entityProcesoMasivoDet = new InfoProcesoMasivoDet();
                        $entityProcesoMasivoDet->setEstado("Pendiente");
                        $entityProcesoMasivoDet->setUsrCreacion($strUser);
                        $entityProcesoMasivoDet->setFeCreacion(new \DateTime('now'));
                        $entityProcesoMasivoDet->setIpCreacion($strIp);
                        $entityProcesoMasivoDet->setObservacion("Se crea el registro");
                        $entityProcesoMasivoDet->setProcesoMasivoCabId($entityProcesoMasivoCab);
                        $entityProcesoMasivoDet->setSolicitudId($intKey); //ID_DEBITO_GENERAL
                        $entityProcesoMasivoDet->setPuntoId($entityRespuestaDebito->getId()); //ID_RESPUESTA_DEBITO
                        $emInfraestructura->persist($entityProcesoMasivoDet);
                        $emInfraestructura->flush();
                    }
                }
            }
            //ACTUALIZAR A PROCESADO LAS CABECERAS DEL DÉBITO GENERAL Y A PROCESANDO EL DÉBITO GENERAL
            $arrayParametros = array(
                "intDebitoGeneralId" => $intKey,
                "strUsrUltMod"       => $strUser,
                "strFechaUltMod"     => $entityProcesoMasivoCab->getFeCreacion()
            );
            $arrayRespuesta = $serviceInfoDebitoCab->procesaCabecerasDebitoGeneral($arrayParametros);
            $entityDebitoGeneral->setEjecutando("S");
            $entityDebitoGeneral->setCuentaContableId($intCuentaContableId);
            $emfn->persist($entityDebitoGeneral);
            $emfn->flush();
            if ($arrayRespuesta['intEstado'] == 0)
            {
                throw new \Exception($arrayRespuesta['strMensaje']);
            }
            $emInfraestructura->getConnection()->commit();
            $emfn->getConnection()->commit();
            $strMensaje = "Los archivos de respuesta se estan procesando. Llegara un correo notificando cuando termine el proceso.";
        }
        catch (\Exception $ex)
        {
            $emInfraestructura->getConnection()->rollback();
            $emfn->getConnection()->rollback();
            $serviceUtil->insertError('Telcos+', 'leeArchivoAction', $ex->getMessage(), $strUser, $strIp);
            $strTipoMensaje = 'notice';
            $strMensaje     = 'Ha ocurrido un error inesperado al subir los archivos de respuesta' . $strMensaje;
        }
        $this->get('session')->getFlashBag()->add($strTipoMensaje, $strMensaje);
        return $this->redirect($this->generateUrl('respuestadebitos_list_debitos_general', array()));
    }

    /**
    * Documentación para funcion 'listDebitosAction'.
    * funcion que permite visulizar el twig listDebitos
    * en el cual se mostrara los detalles de un debito
    * @author <amontero@telconet.ec>
    * @since 23/09/2014
    * 
    * @author <atarreaga@telconet.ec>
    * @version 1.1 24-07-2019 Se realiza la segmentación de Pendientes (Abonados) y Pendientes (No abonados). 
    * 
    * @param integer $idDebGen (Id del debito general)
    * @return objeto - render (Renderiza una vista)
    */
    public function listDebitosAction($idDebGen)
    {
        $em                   = $this->getDoctrine()->getManager('telconet_financiero');
        $request              = $this->getRequest();
        $session              = $request->getSession();
        $ptoCliente_sesion    = $session->get('ptoCliente');
        $cliente_sesion       = $session->get('cliente'); 
        $entityDebitoGen      = $em->getRepository('schemaBundle:InfoDebitoGeneral')->find($idDebGen);  
        $cabeceras            = $em->getRepository('schemaBundle:InfoDebitoCab')->findByDebitoGeneralId($idDebGen);
        $arrayBancos          = "";  
        $intIndice            = 0;
        $totalGeneral         = 0;
        $valorTotalGeneral    = 0;
        $totalPendientes      = 0;
        $valorTotalPendientes = 0;
        $totalRechazados      = 0;
        $valorTotalRechazados = 0;
        $totalProcesados      = 0;
        $valorTotalProcesados = 0;
        foreach($cabeceras as $cabecera)
        {
            $entityBancoTipoCuenta=$em->getRepository('schemaBundle:AdmiBancoTipoCuenta')->find($cabecera->getBancoTipoCuentaId());
            if(strtoupper($entityBancoTipoCuenta->getBancoId()->getDescripcionBanco())=='TARJETAS')
            {
                $strBancos=str_replace('TARJETA','',$entityBancoTipoCuenta->getTipoCuentaId()->getDescripcionCuenta());
            }
            else
            {
                $strBancos=str_replace('BANCO','',$entityBancoTipoCuenta->getBancoId()->getDescripcionBanco())." ".
                    $entityBancoTipoCuenta->getTipoCuentaId()->getDescripcionCuenta();
            }
            
            $arrayDebitosPendientes=$em->getRepository('schemaBundle:InfoDebitoCab')
                ->findCountDebitosPorDebitoCabIdPorEstado($cabecera->getId(),'Pendiente');
            $arrayDebitosProcesados=$em->getRepository('schemaBundle:InfoDebitoCab')
                ->findCountDebitosPorDebitoCabIdPorEstado($cabecera->getId(),'Procesado');
            $arrayDebitosRechazados=$em->getRepository('schemaBundle:InfoDebitoCab')
                ->findCountDebitosPorDebitoCabIdPorEstado($cabecera->getId(),'Rechazado');
            $arrayBancos[$intIndice]['banco']=$strBancos;
            $arrayBancos[$intIndice]['total']=
                $arrayDebitosPendientes[0]['total']+$arrayDebitosRechazados[0]['total']+$arrayDebitosProcesados[0]['total'];
            $totalGeneral+=$arrayBancos[$intIndice]['total'];
            $arrayBancos[$intIndice]['valor_total'] = $arrayDebitosPendientes[0]['recaudado'] + 
                $arrayDebitosProcesados[0]['recaudado'] + 
                $arrayDebitosRechazados[0]['recaudado'];
            $valorTotalGeneral+=$arrayBancos[$intIndice]['valor_total'];
            $arrayBancos[$intIndice]['pendientes']=$arrayDebitosPendientes[0]['total'];
            $totalPendientes+=$arrayBancos[$intIndice]['pendientes'];
            $arrayBancos[$intIndice]['pendientes_valor']=($arrayDebitosPendientes[0]['recaudado']) ? $arrayDebitosPendientes[0]['recaudado'] : 0;
            $valorTotalPendientes+=$arrayBancos[$intIndice]['pendientes_valor'];           
            //OBTENGO EL TOTAL DE REGISTROS QUE TIENEN LA CARACTERÍSTICA GUARDA_REFERENCIA_PARCIAL POR EL DÉBITO GENERAL.        
            $arrayCaracteristica = $em->getRepository('schemaBundle:InfoDebitoCab')
                                      ->getObtieneCaracteristica(array('idDebGen'=>$idDebGen,
                                                                       'strCaracteristica'=>'GUARDA_REFERENCIA_PARCIAL')); 
            if($arrayCaracteristica[0]['total'] == 1 )
            {
                $arrayDebitosPendientesAbonados   = $em->getRepository('schemaBundle:InfoDebitoCab')
                                                       ->findCountDebitosPendientesAbonados($cabecera->getId(),'Pendiente');
                $arrayDebitosPendientesNoAbonados = $em->getRepository('schemaBundle:InfoDebitoCab')
                                                       ->findCountDebitosPendientesNoAbonados($cabecera->getId(),'Pendiente');

                $arrayBancos[$intIndice]['pendientesAbonados_valor']   =
                    ($arrayDebitosPendientesAbonados[0]['recaudado']) ? $arrayDebitosPendientesAbonados[0]['recaudado'] : 0;
                $arrayValorTotalPendientesAbonados+=$arrayBancos[$intIndice]['pendientesAbonados_valor'];
             
                $arrayBancos[$intIndice]['pendientesNoAbonados_valor'] =
                    ($arrayDebitosPendientesNoAbonados[0]['recaudado']) ? $arrayDebitosPendientesNoAbonados[0]['recaudado'] : 0;
                $arrayValorTotalPendientesNoAbonados+=$arrayBancos[$intIndice]['pendientesNoAbonados_valor'];
            }
            $arrayBancos[$intIndice]['rechazados']=$arrayDebitosRechazados[0]['total'];
            $totalRechazados+=$arrayBancos[$intIndice]['rechazados'];
            $arrayBancos[$intIndice]['rechazados_valor']=($arrayDebitosRechazados[0]['recaudado']) ? $arrayDebitosRechazados[0]['recaudado'] : 0;
            $valorTotalRechazados+=$arrayBancos[$intIndice]['rechazados_valor'];
            $arrayBancos[$intIndice]['procesados']=$arrayDebitosProcesados[0]['total'];
            $totalProcesados+=$arrayBancos[$intIndice]['procesados'];
            $arrayBancos[$intIndice]['procesados_valor']=($arrayDebitosProcesados[0]['recaudado']) ? $arrayDebitosProcesados[0]['recaudado'] : 0;
            $valorTotalProcesados+=$arrayBancos[$intIndice]['procesados_valor'];
            $arrayBancos[$intIndice]['estado']=$cabecera->getEstado();
            $intIndice++;
        }                
        $arrayBancos[$intIndice]['banco']            = "TOTAL:";
        $arrayBancos[$intIndice]['total']            = $totalGeneral;
        $arrayBancos[$intIndice]['valor_total']      = $valorTotalGeneral;        
        $arrayBancos[$intIndice]['pendientes']       = $totalPendientes;
        $arrayBancos[$intIndice]['pendientes_valor'] = $valorTotalPendientes;         
        if($arrayCaracteristica[0]['total'] == 1 )
        {
          
            $arrayBancos[$intIndice]['pendientesAbonados_valor']   = $arrayValorTotalPendientesAbonados;
        
            $arrayBancos[$intIndice]['pendientesNoAbonados_valor'] = $arrayValorTotalPendientesNoAbonados;
        }
        $arrayBancos[$intIndice]['rechazados']       = $totalRechazados;
        $arrayBancos[$intIndice]['rechazados_valor'] = $valorTotalRechazados;   
        $arrayBancos[$intIndice]['procesados']       = $totalProcesados;
        $arrayBancos[$intIndice]['procesados_valor'] = $valorTotalProcesados; 
        $arrayBancos[$intIndice]['estado']           = ""; 
        return $this->render('financieroBundle:AdmiFormatoDebito:listDebitos.html.twig', array(
            'entityDebitoGen' => $entityDebitoGen,
            'ptoCliente' => $ptoCliente_sesion,
            'cliente' => $cliente_sesion,
            'bancos' => $arrayBancos,
            'caracteristica'  => $arrayCaracteristica[0]['total'],
            'totalRegistros'=>(count($arrayBancos)-1)
        ));
    }
    
    /**
    * Documentación para función 'saldoDebitosPendientesAction'.
    * Función que permite obtener el total del saldo de los débitos pendientes.
    * @author <hlozano@telconet.ec>
    * @since 07/02/2019
    * @param integer $intIdDebGen (Id del debito general)
    * @return objeto Json 
    */
    
    public function saldoDebitosPendientesAction($intIdDebGen)
    {
        $emFinanciero            = $this->getDoctrine()->getManager('telconet_financiero'); 
        $arrayCabeceras          = $emFinanciero->getRepository('schemaBundle:InfoDebitoCab')->findByDebitoGeneralId($intIdDebGen);
        $arrayBancos             = "";  
        $intIndice               = 0;
        $intValorTotalPendientes = 0;
        
        foreach($arrayCabeceras as $objCabecera)
        {            
            $arrayDebitosPendientes=$emFinanciero->getRepository('schemaBundle:InfoDebitoCab')
                                       ->findCountDebitosPorDebitoCabIdPorEstado($objCabecera->getId(),'Pendiente');           
            
            $arrayBancos[$intIndice]['pendientes_valor']=($arrayDebitosPendientes[0]['recaudado']) ? $arrayDebitosPendientes[0]['recaudado'] : 0;
            $intValorTotalPendientes+=$arrayBancos[$intIndice]['pendientes_valor'];
     
        }                
        
        $objResponse = new Response(json_encode(array('totalDebito' => $intValorTotalPendientes)));
        
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
           
    }


    public function listDebitosPendientesAction($idDebGen)
    {
        $em                = $this->getDoctrine()->getManager('telconet_financiero');
        $request           = $this->getRequest();
        $session           = $request->getSession();
        $ptoCliente_sesion = $session->get('ptoCliente');
        $cliente_sesion    = $session->get('cliente'); 
		$intEmpresaId      = $session->get('idEmpresa'); 
        $prefijoEmpresa    = $request->getSession()->get('prefijoEmpresa'); 
        $entityDebitoGen   = $em->getRepository('schemaBundle:InfoDebitoGeneral')->find($idDebGen);
        $entityGrupoDebito = $em->getRepository('schemaBundle:AdmiGrupoArchivoDebitoCab')->find($entityDebitoGen->getGrupoDebitoId());
		$bancos            = $entityGrupoDebito->getNombreGrupo();
        $porcentajeRetFte  = 0;
        $porcentajeRetIva  = 0;
        $arrayPorcentajeRetencionFte = $em->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getOne('PORCENTAJE RETENCION A LA FUENTE DEBITOS',
                                                         'FINANCIERO',
                                                         '',
                                                         '',
                                                         'PORCENTAJE_RETENCION_FUENTE_DEBITOS',
                                                         '',
                                                         '',
                                                         '',
                                                         '',
                                                         $intEmpresaId);        
        
        $arrayPorcentajeRetencionIva = $em->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getOne('PORCENTAJE RETENCION AL IVA DEBITOS',
                                                         'FINANCIERO',
                                                         '',
                                                         '',
                                                         'PORCENTAJE_RETENCION_IVA_DEBITOS',
                                                         '',
                                                         '',
                                                         '',
                                                         '',
                                                         $intEmpresaId);
        
        if (count($arrayPorcentajeRetencionFte)>0)
        {    
            $porcentajeRetFte=$arrayPorcentajeRetencionFte['valor2'];
        }
        if (count($arrayPorcentajeRetencionIva)>0)
        {    
            $porcentajeRetIva=$arrayPorcentajeRetencionIva['valor2'];
        }
        return $this->render('financieroBundle:AdmiFormatoDebito:listDebitosPendientes.html.twig', array(
            'entityDebitoGen' => $entityDebitoGen,
            'ptoCliente' => $ptoCliente_sesion,
            'cliente' => $cliente_sesion,
			'bancos' => $bancos,
            'porcentajeRetFte'=>$porcentajeRetFte,
            'porcentajeRetIva'=>$porcentajeRetIva,
            'prefijoEmpresa'  =>$prefijoEmpresa
        ));
    }	

    /**
    * Documentación para funcion 'gridDebitosPendientesAction'.
    * obtiene el detalles de los debitos segun debito general.
    * @author <amontero@telconet.ec>
    * @since 10/06/2015
    * @version 1.1
    * @return objeto - response
    */
    public function gridDebitosPendientesAction()
    {
        $request         = $this->getRequest();
        $debitoGeneralId = $request->get("debitoGeneralId");
        $limit           = $request->get("limit");
        $start           = $request->get("start");
        $fechaDesde      = explode('T', $request->get("fechaDesde"));
        $fechaHasta      = explode('T', $request->get("fechaHasta"));
        $banco           = $request->get('banco');
        $numeroCedula    = $request->get('numerocedula');
        $numeroCuenta    = $request->get('numerocuenta');
        $estado          = "Pendiente";	
        $em              = $this->get('doctrine')->getManager('telconet_financiero');
        $secret          = $this->container->getParameter('secret');
        $parametros = array (
                            'estado'       => $estado,
                            'debitoGenId'  => $debitoGeneralId,
                            'fechaDesde'   => $fechaDesde[0],
                            'fechaHasta'   => $fechaHasta[0],
                            'limit'        => $limit,
                            'start'        => $start,
                            'banco'        => $banco,
                            'numeroCuenta' => $numeroCuenta,
                            'numeroCedula' => trim($numeroCedula),
                            'secret'       => $secret
                    );
        $resultado = $em->getRepository('schemaBundle:InfoDebitoDet')->findDetallesDebitoPorDebitoGeneral($parametros);
        $datos     = $resultado['registros'];
        $total     = $resultado['total'];
        for($indiceDebitos=0;$indiceDebitos<count($datos);$indiceDebitos++)
        {
            $urlVer  = "#";
            $linkVer = $urlVer;
            $banco   = "";
            //Si el tipo cuenta es tarjeta entonces muestra Descripcion de cuenta
            //Si no lo es entonces muestra Descripcion del Banco
            if(strtoupper($datos[$indiceDebitos]['descripcionBanco']) == 'TARJETA')
            {
                $banco = str_replace(array('TARJETAS','TARJETA'),array('',''),$datos[$indiceDebitos]['descripcionCuenta']);
            }
            else
            {
                $banco = str_replace(array('BANCOS','BANCO'),array('',''),$datos[$indiceDebitos]['descripcionBanco'])." ".
                str_replace(array('TARJETAS','TARJETA'),array('',''),$datos[$indiceDebitos]['descripcionCuenta']);
            }
            $arreglo[] = array(
                'id'                  => $datos[$indiceDebitos]['idDebitoDet'],
                'banco'               => $banco,
                'cliente'             => $datos[$indiceDebitos]['razonSocial'] ? $datos[$indiceDebitos]['razonSocial'] :
                $datos[$indiceDebitos]['nombres']." ".$datos[$indiceDebitos]['apellidos'] ,
                'cedula'              => $datos[$indiceDebitos]['identificacionCliente'],
                'total'               => $datos[$indiceDebitos]['valorTotal'],
                'fechaCreacion'       => $datos[$indiceDebitos]['feCreacion'],
                'usuarioCreacion'     => $datos[$indiceDebitos]['usrCreacion'],
                'estado'              => $datos[$indiceDebitos]['estado'],
                'observacionRechazo'  => $datos[$indiceDebitos]['observacionRechazo'],
                'numerotarjetacuenta' => $datos[$indiceDebitos]['numeroTarjeta'],
                'linkVer'             => $linkVer
            );
        }
        if (!empty($arreglo))
        {
            $response = new Response(json_encode(array('total' => $total, 'pagos' => $arreglo)));
        }
        else
        {
            $arreglo[] = array();
            $response = new Response(json_encode(array('total' => $total, 'pagos' => $arreglo)));
        }
        $response->headers->set('Content-type', 'text/json');
        return $response;
    } 

    /**
    * Documentación para funcion 'marcarRechazadosAjaxAction'.
    * marcar como rechazados los debitos pendientes seleccionados  y envia notificacion al usuario del proceso realizado..
    * @author <amontero@telconet.ec>
    * @since 10/09/2015
    * @version 1.1
    * @return objeto - response
    */  
    public function marcarRechazadosAjaxAction()
    {
        $request        = $this->getRequest();
        $idEmpresa      = $request->getSession()->get('idEmpresa');
        $prefijoEmpresa = $request->getSession()->get('prefijoEmpresa');          
        $session        = $request->getSession();         
        $usrCreacion    = $session->get('user');		
        $respuesta      = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');        
        $respuesta->setContent("error del Form");  
        $emfn          = $this->getDoctrine()->getManager('telconet_financiero');
        $emcom         = $this->getDoctrine()->getManager('telconet');
        //Obtiene parametros enviados desde el ajax
        $peticion      = $this->get('request');
        $parametro     = $peticion->get('param');
        $motivoRechazo = $peticion->get('motivoRechazo');
        $array_valor   = explode("|",$parametro);       
        $fechaGenerado = $peticion->get("fechaGenerado");
        $nombreBanco   = $peticion->get("nombreBanco");        
        $emfn->getConnection()->beginTransaction();
        try
        {  
            foreach($array_valor as $id)
            {           
                $entityDebitoDet = $emfn->getRepository('schemaBundle:InfoDebitoDet')->find($id);	
                $entityDebitoDet->setEstado('Rechazado');
                $entityDebitoDet->setFeUltMod(new \DateTime('now'));
                $entityDebitoDet->setUsrUltMod($usrCreacion);
                $entityDebitoDet->setObservacionRechazo($motivoRechazo);
                $emfn->persist($entityDebitoDet);
                $emfn->flush();				

            } 
            $emfn->getConnection()->commit();   
            $respuesta->setContent("Se rechazaron los debitos con exito.");    
             //ENVIA NOTIFICACION AL USUARIO
            $formasContacto = $emcom->getRepository('schemaBundle:InfoPersona')
                ->getContactosByLoginPersonaAndFormaContacto($usrCreacion,'Correo Electronico');			    
            $to = array();
            if($formasContacto)
            {
                foreach($formasContacto as $formaContacto)
                {
                    $to[] = strtolower($formaContacto['valor']);
                }
            }            
             $parametros = array('nombreBanco'     => $nombreBanco,
                                 'feProceso'       => date("Y-m-d H:i:s") ,
                                 'feGenerado'      => $fechaGenerado,
                                 'usuarioCreacion' => $usrCreacion,
                                 'cantidad'        => count($array_valor),
                                 'empresa'         => $prefijoEmpresa,
                                 'tipoProceso'     => "Rechazo de debitos"
                                );
            $serviceEnvioPlantilla = $this->get('soporte.EnvioPlantilla');                
            $serviceEnvioPlantilla->generarEnvioPlantilla('Proceso Manual de debitos pendientes', 
                                                         $to, 
                                                         'PROCDEBP',
                                                         $parametros, 
                                                         $idEmpresa, 
                                                         '', 
                                                         ''
                                                        );           
        }  
        catch (\Swift_IoException $eswift)
        {
            $respuesta->setContent($respuesta->getContent()." (Se produjo un error al intentar enviar notificacion) "); 
        }        
        catch (\Exception $e) 
        {
            if ($emfn->getConnection()->isTransactionActive()) 
            {             
                $emfn->getConnection()->rollback();
            }
            $emfn->getConnection()->close();
            $respuesta->setContent($e->getMessage());            
        }
        return $respuesta;
    }    
    
    /**
    * Documentación para funcion 'generarPagosDebitosPendientesAjaxAction'.
    * genera pagos en base a los debitos pendientes seleccionados y envia notificacion al usuario del proceso realizado.
    * Actualizacion: se agrega proceso para contabilizar y se graba registro en tabla historial de debitos general 
    * @author <amontero@telconet.ec>
    * @version 1.2 amontero : 11/04/2016
    * @since 06/01/2015
    * @return objeto - response
    *
    * @author Germán Valenzuela <gvalenzuela@telconet.ec>
    * @version 1.3 : 05/09/2017
    * Se modifica el proceso para validar la generacion de debitos pendiente de forma automatica.
    *
    * @author Luis Lindao <llindao@telconet.ec>
    * @version 1.3 : 06-01-2018
    * Se modifica para agregar validación de contabilización solo para empresas con proceso INDIVIDUAL
    * 
    * @author Hector Lozano <hlozano@telconet.ec>
    * @version 1.4 : 27-05-2020
    * Se modifica función para obtener el Tipo de Escenario y Filtro para enviarlos a la funcion grabaPagoScript.
    * ESCENARIO_BASE: Escenario basado en la generación de los débitos de los clientes con saldo pendiente de sus facturas activas.               
    * ESCENARIO_1: Escenario basado en la generación de los débitos de los clientes que tengan un saldo pendiente de su factura recurrente mensual,
    *              emitida de acuerdo con cada ciclo.
    * 
    * @author Hector Lozano <hlozano@telconet.ec>
    * @version 1.5 : 08-07-2020 - Se modifica validación de los puntos que van hacer reactivados 
    *                             y se crea función para la consulta de los mismos.
    */     
    public function generarPagosDebitosPendientesAjaxAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');        
        $respuesta->setContent("error del Form");          
        $request              = $this->getRequest();
        $intIdEmpresa         = $request->getSession()->get('idEmpresa');
        $strPrefijoEmpresa    = $request->getSession()->get('prefijoEmpresa');
        $intOficinaId         = $request->getSession()->get('idOficina');
        $session              = $request->getSession();         
        $usrCreacion          = $session->get('user');		
        $em                   = $this->getDoctrine()->getManager('telconet');
        $objEmfn              = $this->getDoctrine()->getManager('telconet_financiero');
        $emcom                = $this->getDoctrine()->getManager('telconet');        
        //Obtiene parametros enviados desde el ajax
        $peticion             = $this->get('request');
        $parametro            = $peticion->get('param');
        $strCodigoDebito      = $peticion->get('codigo');
        $dateFechaProceso     = explode('T', $request->get("fechaProceso"));
        $fechaGenerado        = $request->get("fechaGenerado");
        $strNombreBanco       = $request->get("nombreBanco");
        $floatPorcComision    = $request->get("porcentajeComision");
        $floatValorComision   = $request->get("valorComision");
        $floatValorRetFte     = $request->get("valorRetencionFte");
        $floatValorRetIva     = $request->get("valorRetencionIva");
        $floatValorNeto       = $request->get("valorNeto");
        $intDebitoGeneralId   = $request->get("debitoGeneralId");
        $intCuentaContableId  = $request->get("cuentaContableId");
        $arrayParametroDet    = array();
        $objInfoDebitoGeneral = $objEmfn->getRepository("schemaBundle:InfoDebitoGeneral")->find($intDebitoGeneralId);
        $objAdmiCuentaContable= $objEmfn->getRepository("schemaBundle:AdmiCuentaContable")->find($intCuentaContableId);
        $strTipoOperacion     = $peticion->get('tipoOperacion');
        $array_valor          = explode("|",$parametro);       
        $arrayPuntosReactivar = array();
        $objEmfn->getConnection()->beginTransaction();
        $objInfraestructura   = $this->getDoctrine()->getManager('telconet_infraestructura');
        $arrayDebitosAuto     = array();
        try
        {  
            $debitoCabId=null;
            //Antes de procesar verifica si existe algun debito procesando
            $objDebitosEnProceso=$objEmfn->getRepository('schemaBundle:InfoDebitoGeneral')
                                         ->findDebitosGeneralProcesandoseHoy('S','Activo',$intIdEmpresa);

            if($strTipoOperacion === "AUTOMATICA")
            {
                try
                {
                    $arrayDebitosAuto["intIdEmpresa"]      = $intIdEmpresa;
                    $arrayDebitosAuto["strNombreBanco"]    = $strNombreBanco;
                    $arrayDebitosAuto["strEstado"]         = "Activo";
                    $arrayDebitosAuto["strCaracteristica"] = "CARGA_AUTOMATICA_DEBITO";

                    /* Se verifica si se tiene los privilegios para generar la operacion automatica de debitos */
                    $objAdmiFormatoDebitoCaract=$objEmfn->getRepository('schemaBundle:InfoDebitoCab')
                            ->findDebitosAutomaticos($arrayDebitosAuto);

                    if(!is_object($objAdmiFormatoDebitoCaract) || $objAdmiFormatoDebitoCaract->getValor() != "SI")
                    {
                        $respuesta->setContent(" | El banco seleccionado no cuenta con los privilegios para".
                                               " la generación automática.");

                        return $respuesta;
                    }

                    /* Verifica si existe un proceso masivo ejecutandose */
                    $arrayResultado = $objInfraestructura->getRepository('schemaBundle:InfoProcesoMasivoCab')
                                          ->findBy(array("estado"=>"Procesando","tipoProceso"=>"ProcesarDebito"));

                    if(!empty($arrayResultado))
                    {
                        $respuesta->setContent(" | Estimado(a) usuario en estos momentos se está ejecutando otro proceso ".
                                               "automático de debitos, por favor vuelva a intentarlo en unos minutos.");
                        return $respuesta;
                    }

                    //INSERTAMOS EN LAS ESTRUCTURAS DE PROCESOS MASIVOS

                    $objInfraestructura->getConnection()->beginTransaction();

                    //CABECERA

                    $objInfoProcesoMasivoCab = new InfoProcesoMasivoCab();
                    $objInfoProcesoMasivoCab->setTipoProceso("ProcesarDebito");
                    $objInfoProcesoMasivoCab->setEmpresaCod($intIdEmpresa);
                    $objInfoProcesoMasivoCab->setIdsOficinas($intOficinaId);
                    $objInfoProcesoMasivoCab->setEstado("Pendiente");
                    $objInfoProcesoMasivoCab->setFeCreacion(new \DateTime('now'));
                    $objInfoProcesoMasivoCab->setUsrCreacion($session->get('user'));
                    $objInfoProcesoMasivoCab->setIpCreacion($request->getClientIp());
                    $objInfraestructura->persist($objInfoProcesoMasivoCab);
                    $objInfraestructura->flush();

                    //DETALLE
                    $entityInfoProcesoMasivoDet = new InfoProcesoMasivoDet();
                    $entityInfoProcesoMasivoDet->setProcesoMasivoCabId($objInfoProcesoMasivoCab);
                    $entityInfoProcesoMasivoDet->setPuntoId($intDebitoGeneralId);
                    $entityInfoProcesoMasivoDet->setFeCreacion(new \DateTime('now'));
                    $entityInfoProcesoMasivoDet->setUsrCreacion($session->get('user'));
                    $entityInfoProcesoMasivoDet->setIpCreacion($request->getClientIp());
                    $entityInfoProcesoMasivoDet->setEstado("Pendiente");

                    $strObservacion = "El valor del campo PUNTO_ID es el ID del debito general.";
                    $strObservacion.= "|".$intCuentaContableId."|".$strCodigoDebito."|".$dateFechaProceso[0];
                    $strObservacion.= "|".$floatPorcComision."|".$floatValorComision;

                    if($floatValorRetFte > 0)
                    {
                        $strObservacion.="|S";
                    }
                    else
                    {
                        $strObservacion.="|N";
                    }
                    if($floatValorRetIva > 0)
                    {
                         $strObservacion.="|S";
                    }
                    else
                    {
                        $strObservacion.="|N";
                    }

                    $strObservacion.="|".$floatValorRetFte."|".$floatValorRetIva."|".$floatValorNeto."|".$strPrefijoEmpresa;

                    $entityInfoProcesoMasivoDet->setObservacion($strObservacion);
                    $objInfraestructura->persist($entityInfoProcesoMasivoDet);
                    $objInfraestructura->flush();

                    $objInfraestructura->getConnection()->commit();

                    $objEmfn->getRepository('schemaBundle:InfoDebitoCab')->ejecutarDebitosPendientes();

                    $respuesta->setContent(" | Estimado(a) usuario, al culminar el proceso automático se ".
                                           "enviará el correo respectivo..");
                }
                catch(\Exception $ex)
                {
                    if ($objInfraestructura->getConnection()->isTransactionActive())
                    {
                        $objInfraestructura->getConnection()->rollback();
                    }

                    if ($objEmfn->getConnection()->isTransactionActive())
                    {
                        $objEmfn->getConnection()->rollback();
                    }

                    $objEmfn->getConnection()->close();
                    $objInfraestructura->getConnection()->close();
                    error_log("AdmiFormatoDebitoController.generarPagosDebitosPendientesAjaxAction: ".$ex->getMessage());
                    $respuesta->setContent(" | El proceso automático no se ejecutó con éxito..");
                }

                return $respuesta;
            }


            if(!$objDebitosEnProceso)
            {    
                
                //CREA HISTORIAL DE DEBITO GENERAL
                $objInfoDebitoGeneralHistorial=new InfoDebitoGeneralHistorial();
                $objInfoDebitoGeneralHistorial->setDebitoGeneralId($objInfoDebitoGeneral);
                $objInfoDebitoGeneralHistorial->setCuentaContableId($objAdmiCuentaContable);
                $objInfoDebitoGeneralHistorial->setNumeroDocumento($strCodigoDebito);
                $objInfoDebitoGeneralHistorial->setFeDocumento(new \DateTime($dateFechaProceso[0]));
                $objInfoDebitoGeneralHistorial->setPorcentajeComisionBco($floatPorcComision);
                $objInfoDebitoGeneralHistorial->setValorComisionBco($floatValorComision);
                if($floatValorRetFte>0)
                {    
                    $objInfoDebitoGeneralHistorial->setContieneRetencionFte('S');
                }
                else
                {
                    $objInfoDebitoGeneralHistorial->setContieneRetencionFte('N');
                }    
                if($floatValorRetIva>0)
                {    
                    $objInfoDebitoGeneralHistorial->setContieneRetencionIva('S');
                }
                else
                {
                    $objInfoDebitoGeneralHistorial->setContieneRetencionIva('N');
                }    
                $objInfoDebitoGeneralHistorial->setValorRetencionFuente($floatValorRetFte);
                $objInfoDebitoGeneralHistorial->setValorRetencionIva($floatValorRetIva);
                $objInfoDebitoGeneralHistorial->setValorNeto($floatValorNeto);
                $objInfoDebitoGeneralHistorial->setObservacion("Se procesa debitos por debitos pendientes");
				$objInfoDebitoGeneralHistorial->setUsrCreacion($session->get('user'));
				$objInfoDebitoGeneralHistorial->setFeCreacion(new \DateTime('now'));
                $objInfoDebitoGeneralHistorial->setIpCreacion($request->getClientIp());
                $objInfoDebitoGeneralHistorial->setEstado('Activo');
                $objEmfn->persist($objInfoDebitoGeneralHistorial);
                $objEmfn->flush();
                
                ini_set('memory_limit', '3072M');
                set_time_limit(0);
                foreach($array_valor as $id)
                {           
                    $entityDebitoDet = $objEmfn->getRepository('schemaBundle:InfoDebitoDet')->find($id);
                    $debitoCabId     = $entityDebitoDet->getDebitoCabId();
                    if($entityDebitoDet->getEstado()=='Pendiente')
                    {		
                        $entityDebitoDet->setEstado('Procesado');
                        $entityDebitoDet->setFeUltMod(new \DateTime('now'));
                        $entityDebitoDet->setUsrUltMod($usrCreacion);
                        $entityDebitoDet->setValorDebitado($entityDebitoDet->getValorTotal());                    
                        $objEmfn->persist($entityDebitoDet);
                        $objEmfn->flush();
                        $entityDebitoCab       = $objEmfn->getRepository('schemaBundle:InfoDebitoCab')->find($debitoCabId);
                        $entityBancoTipoCuenta = $objEmfn->getRepository('schemaBundle:AdmiBancoTipoCuenta')
                                                      ->find($entityDebitoCab->getBancoTipoCuentaId());
                        
                        //Obtiene el Tipo de Escenario y su filtro correspondiente de la Generación del Débito.
                         /*@var $serviceInfoDebitoCab \telconet\financieroBundle\Service\InfoDebitoCabService*/
                        $serviceInfoDebitoCab = $this->get('financiero.InfoDebitoCab');
                        $arrayInfoDebitoCab = $serviceInfoDebitoCab->findTipoFiltroEscenarioDebito(array("intIdDebitoGeneral" => $intDebitoGeneralId,
                                                                                                         "intIdEmpresa"       => $intIdEmpresa));
                        
                        if(!empty($arrayInfoDebitoCab) || $arrayInfoDebitoCab != null)
                        {
                            $strTipoEscenario   = $arrayInfoDebitoCab[0]['tipoEscenario'];
                            $strFiltroEscenario = $arrayInfoDebitoCab[0]['filtroEscenario'];
                        }
                        else
                        {
                            throw new \Exception('No se encontró escenario y su filtro correspondiente.');
                        }
                        
                        //CREA EL PAGO PARA EL DETALLE DEL DEBITO
                        $arrayParametros = array();                        
                        $arrayParametros["stringEmpresaId"]            = $intIdEmpresa;
                        $arrayParametros["intOficinaId"]               = $intOficinaId;
                        $arrayParametros["stringUsuario"]              = $usrCreacion;
                        $arrayParametros["objEntityManagerComercial"]  = $em;
                        $arrayParametros["objEntityManagerFinanciero"] = $objEmfn;
                        $arrayParametros["objDebitoGeneralHistorial"]  = $objInfoDebitoGeneralHistorial;
                        $arrayParametros["objInfoDebitoDet"]           = $entityDebitoDet;
                        $arrayParametros["objAdmiBancoTipoCuenta"]     = $entityBancoTipoCuenta;
                        $arrayParametros["floatValorPagado"]           = $entityDebitoDet->getValorTotal();
                        $arrayParametros["stringNumeroOrden"]          = $strCodigoDebito;
                        $arrayParametros["stringfechaProceso"]         = $dateFechaProceso[0];
                        $arrayParametros["strTipoEscenario"]           = $strTipoEscenario;
                        $arrayParametros["strFiltroEscenario"]         = $strFiltroEscenario;
                        
                        $this->grabaPagoScript($arrayParametros);
                        
                        $arrayPagoCab = $objEmfn->getRepository("schemaBundle:InfoPagoCab")
                                                ->findByPuntosReactivar(array('intDebitoDetId'=>$entityDebitoDet->getId()));
                        if(!empty($arrayPagoCab))
                        {
                            for($intIndice=0;$intIndice<count($arrayPagoCab);$intIndice++)
                            {
                                $arrayPuntosReactivar[] = $arrayPagoCab[$intIndice]['id'];
                            } 
                        }                           
                    }                    
                } 
                $objEmfn->getConnection()->commit();
                $respuesta->setContent(" | Se generaron los pagos con exito.");  
          
                //CONTABILIZA EL DEBITO    
                $arrayParametroDet= $em->getRepository('schemaBundle:AdmiParametroDet')
                ->getOne("PROCESO CONTABILIZACION EMPRESA", "FINANCIERO", "", "", $strPrefijoEmpresa, "", "", "");
                
                //VERIFICA SI ESTA HABILITADA CONTABILIZACION EN LA EMPRESA
                if ($arrayParametroDet["valor2"]=="S" && $arrayParametroDet["valor3"]=="INDIVIDUAL")
                {   
                    $objParametros['serviceUtil'] = $this->get('schema.Util');

                    //CREA ASIENTOS CONTABLES
                    $strMensajeContabilidad=
                        $objEmfn->getRepository("schemaBundle:InfoDebitoGeneralHistorial")
                        ->contabilizarDebitosProcesoManual($intIdEmpresa,$objInfoDebitoGeneralHistorial->getId(), $objParametros);
                    
                        $respuesta->setContent($respuesta->getContent()." ".$strMensajeContabilidad);
                }                   
                
                //ENVIA NOTIFICACION AL USUARIO
                $formasContacto = $emcom->getRepository('schemaBundle:InfoPersona')
                    ->getContactosByLoginPersonaAndFormaContacto($usrCreacion,'Correo Electronico');			    
                $to = array();
                if($formasContacto)
                {
                    foreach($formasContacto as $formaContacto)
                    {
                        $to[] = strtolower($formaContacto['valor']);
                    }
                }                  
                $parametros = array('nombreBanco'     => $strNombreBanco,
                                    'feProceso'       => date("Y-m-d H:i:s") ,
                                    'feGenerado'      => $fechaGenerado,
                                    'usuarioCreacion' => $usrCreacion,
                                    'cantidad'        => count($array_valor),
                                    'empresa'         => $strPrefijoEmpresa,
                                    'tipoProceso'     => "Procesar debitos (Generacion de pagos)"                    
                                   );
                $serviceEnvioPlantilla = $this->get('soporte.EnvioPlantilla');                
                $strCodigoPlantilla    = '';
                if ($strPrefijoEmpresa=='MD')
                {
                    $strCodigoPlantilla    = 'PROCDEBP'; 
                }
                elseif($strPrefijoEmpresa=='TN')
                {
                    $strCodigoPlantilla    = 'PROCDEBPTN';
                }
                else
                {
                   $strCodigoPlantilla  = '';
                }
                if ($strCodigoPlantilla!='')
                {    
                    $serviceEnvioPlantilla->generarEnvioPlantilla('Proceso Manual de debitos pendientes', 
                                                                 $to, 
                                                                 $strCodigoPlantilla,
                                                                 $parametros, 
                                                                 $intIdEmpresa,
                                                                 '', 
                                                                 ''
                                                                );
                }
                //REACTIVA SERVICIOS
                $serviceProcesoMasivo = $this->get('tecnico.ProcesoMasivo');   
                $arrayParams=array(
                                    'puntos'          => $arrayPuntosReactivar,
                                    'prefijoEmpresa'  => $strPrefijoEmpresa,
                                    'empresaId'       => $intIdEmpresa,
                                    'oficinaId'       => $intOficinaId,
                                    'usuarioCreacion' => $usrCreacion,    
                                    'ip'              => $request->getClientIp(),
                                    'idPago'          => null,
                                    'debitoId'        => $debitoCabId
                );
                $string_msg=$serviceProcesoMasivo->reactivarServiciosPuntos($arrayParams);	                        
            }
            else
            {
                $respuesta->setContent(" | En este momento no se puede ejecutar este proceso ya que se esta ".
                    "ejecutando otro proceso masivo de debitos, por favor vuelva a intente en unos minutos."); 
            }
        }
        catch (\Swift_IoException $eswift)
        {
            $respuesta->setContent($respuesta->getContent()." (Se produjo un error al intentar enviar notificacion) "); 
        }
        catch (\Exception $e)
        {
            if ($objEmfn->getConnection()->isTransactionActive())
            {            
                $objEmfn->getConnection()->rollback();
            }            
            $objEmfn->getConnection()->close();
            $respuesta->setContent($respuesta->getContent().". (".$e->getMessage().")");            
        }
        return $respuesta;
    }


    /**
    * Documentación para funcion 'gridDebitosAction'.
    * obtiene el detalles de los debitos segun debito general.
    * @author <amontero@telconet.ec>
    * @since 23/09/2014
    * @version 1.1 
    * @return objeto - response
    */    
	public function gridDebitosAction()
    {
        $request         = $this->getRequest();
        $debitoGeneralId = $request->get("debitoGeneralId");
        $limit           = $request->get("limit");
        $start           = $request->get("start");
        $fechaDesde      = explode('T', $request->get("fechaDesde"));
        $fechaHasta      = explode('T', $request->get("fechaHasta"));
        $estado          = "";
        $secret          = $this->container->getParameter('secret');
        if($request->get("estado"))
        {
            $estado = $request->get("estado");
        }
        $banco        = $request->get('banco');
        $numeroCuenta = $request->get('numeroCuenta');
        $em           = $this->get('doctrine')->getManager('telconet_financiero');
        $parametros      = array (
            'estado'       => $estado,
            'debitoGenId'  => $debitoGeneralId,
            'fechaDesde'   => $fechaDesde[0],
            'fechaHasta'   => $fechaHasta[0],            
            'limit'        => $limit,
            'start'        => $start,
            'banco'        => $banco,
            'numeroCuenta' => $numeroCuenta,
            'numeroCedula' => '',
            'secret'       => $secret
        );        
        $resultado    = $em->getRepository('schemaBundle:InfoDebitoDet')->findDetallesDebitoPorDebitoGeneral($parametros);
        $datos = $resultado['registros'];
        $total = $resultado['total'];
        for($indiceDebitos=0;$indiceDebitos<count($datos);$indiceDebitos++)
        { 
            $urlVer  = "#";
            $linkVer = $urlVer;
            $banco   = "";
            //Si el tipo cuenta es tarjeta entonces muestra Descripcion de cuenta
            //Si no lo es entonces muestra Descripcion del Banco
            if(strtoupper($datos[$indiceDebitos]['descripcionBanco']) == 'TARJETA')
            {
                $banco = str_replace(array('TARJETAS','TARJETA'),array('',''),$datos[$indiceDebitos]['descripcionCuenta']);
            }
            else
            {
                $banco = str_replace(array('BANCOS','BANCO'),array('',''),$datos[$indiceDebitos]['descripcionBanco'])." ".
                    str_replace(array('TARJETAS','TARJETA'),array('',''),$datos[$indiceDebitos]['descripcionCuenta']);
            }
            

            $arreglo[] = array(
                'id'                 => $datos[$indiceDebitos]['idDebitoDet'],
                'banco'              => $banco,
                'cliente'            => $datos[$indiceDebitos]['razonSocial'] ? $datos[$indiceDebitos]['razonSocial'] :
                    $datos[$indiceDebitos]['nombres']." ".$datos[$indiceDebitos]['apellidos']  ,
                'identificacion'     => $datos[$indiceDebitos]['identificacionCliente'],
                'total'              => $datos[$indiceDebitos]['valorTotal'],
                'debitado'           => $datos[$indiceDebitos]['valorDebitado']?$datos[$indiceDebitos]['valorDebitado']:0,
                'fechaCreacion'      => $datos[$indiceDebitos]['feCreacion'],
                'numeroCuenta'       => $datos[$indiceDebitos]['numeroTarjeta'],
                'usuarioCreacion'    => $datos[$indiceDebitos]['usrCreacion'],
                'estado'             => $datos[$indiceDebitos]['estado'],
                'observacionRechazo' => $datos[$indiceDebitos]['observacionRechazo'],
                'referencia'         => $datos[$indiceDebitos]['referencia'],                    
                'linkVer'            => $linkVer
            );
  
        }
        if(!empty($arreglo))
        {    
            $response = new Response(json_encode(array('total' => $total, 'pagos' => $arreglo)));
        }
        else
        {
            $arreglo[] = array();
            $response = new Response(json_encode(array('total' => $total, 'pagos' => $arreglo)));
        }
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }

    public function listPagosDebitoAction($idDebGen)
    {
        $em = $this->getDoctrine()->getManager('telconet_financiero');
        $request = $this->getRequest();
        $session  = $request->getSession();
        $ptoCliente_sesion=$session->get('ptoCliente');
        $cliente_sesion=$session->get('cliente'); 
        $entityDebitoGen = $em->getRepository('schemaBundle:InfoDebitoGeneral')->find($idDebGen);        
        return $this->render('financieroBundle:AdmiFormatoDebito:listPagosDebito.html.twig', array(
            'entityDebitoGen' => $entityDebitoGen,
            'ptoCliente' => $ptoCliente_sesion,
            'cliente' => $cliente_sesion
        ));
    }	

    public function gridPagosDebitosAction() {
        $request = $this->getRequest();
        $session  = $request->getSession();
        $cliente_sesion=$session->get('cliente');
        $ptoCliente_sesion=$session->get('ptoCliente');
        $clienteId='';$puntoId='';
        $oficinaId=$request->getSession()->get('idOficina');
        if($cliente_sesion)
            $clienteId=$cliente_sesion['id_persona'];
        if($ptoCliente_sesion)
            $puntoId=$ptoCliente_sesion['id'];
        $debitoGeneralId = $request->get("debitoGeneralId");
        $limit = $request->get("limit");
        $page = $request->get("page");
        $start = $request->get("start");
        $fechaDesde = explode('T', $request->get("fechaDesde"));
        $fechaHasta = explode('T', $request->get("fechaHasta"));
        $estado = $request->get("estado");		
        $em = $this->get('doctrine')->getManager('telconet_financiero');
        $em1 = $this->get('doctrine')->getManager('telconet');
        $resultado = $em->getRepository('schemaBundle:InfoPagoCab')
		->findPagosPorDebitoGeneral($estado, $debitoGeneralId,$fechaDesde[0],$fechaHasta[0],$limit,$page,$start);
        $datos = $resultado['registros'];
        $total = $resultado['total'];

        foreach ($datos as $datos):
            if ($datos->getTipoDocumentoId()->getCodigoTipoDocumento()=='ANT')
                $urlVer = $this->generateUrl('anticipo_show', array('id' => $datos->getId()));
            elseif($datos->getTipoDocumentoId()->getCodigoTipoDocumento()=='PAG')
                $urlVer = $this->generateUrl('infopagocab_show', array('id' => $datos->getId()));
            elseif($datos->getTipoDocumentoId()->getCodigoTipoDocumento()=='ANTS')
                $urlVer = $this->generateUrl('anticipo_show', array('id' => $datos->getId()));            
            $linkVer = $urlVer;
            $entityInfoPunto=$em1->getRepository('schemaBundle:InfoPunto')->find($datos->getPuntoId());
			$entityDetalleDebito=$em->getRepository('schemaBundle:InfoDebitoDet')->find($datos->getDebitoDetId());
			$entityCabeceraDebito=$em->getRepository('schemaBundle:InfoDebitoCab')->find($entityDetalleDebito->getDebitoCabId());
			$entityBancoTipoCuenta=$em1->getRepository('schemaBundle:AdmiBancoTipoCuenta')->find($entityCabeceraDebito->getBancoTipoCuentaId());
			$banco="";
			if($entityBancoTipoCuenta->getTipoCuentaId()->getEsTarjeta()=='S'){
				$banco=$entityBancoTipoCuenta->getTipoCuentaId()->getDescripcionCuenta();
			}else{
				$banco=$entityBancoTipoCuenta->getBancoId()->getDescripcionBanco()." ".$entityBancoTipoCuenta->getTipoCuentaId()->getDescripcionCuenta();
			}
			
            $arreglo[] = array(
                'id' => $datos->getId(),
                'tipo' => $datos->getTipoDocumentoId()->getNombreTipoDocumento(),
                'numero' => $datos->getNumeroPago(),
				'banco' => $banco,
				'cliente'=> $entityInfoPunto->getPersonaEmpresaRolId()->getPersonaId()->getInformacionPersona(),
                'punto' => $entityInfoPunto->getLogin(),
                'total' => $datos->getValorTotal(),
                'fechaCreacion' => strval(date_format($datos->getFeCreacion(), "d/m/Y G:i")),
                'usuarioCreacion' => $datos->getUsrCreacion(),
                'estado' => $datos->getEstadoPago(),
                'linkVer' => $linkVer
            );
        endforeach;
        if (!empty($arreglo))
            $response = new Response(json_encode(array('total' => $total, 'pagos' => $arreglo)));
        else {
            $arreglo[] = array();
            $response = new Response(json_encode(array('total' => $total, 'pagos' => $arreglo)));
        }
        $response->headers->set('Content-type', 'text/json');
        return $response;
    } 

    /**
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.1 27-11-2017 - Se agrega el parámtro strAplicaCiclosFacturacion.
     * @since 1.0
     */
    public function listDebitosGeneralAction()
    {
        $objSession         = $this->getRequest()->getSession();
        /* @var $serviceComercial \telconet\comercialBundle\Service\ComercialService */
        $serviceComercial   = $this->get('comercial.Comercial');
        $arrayParametros    = array('strEmpresaCod'     => $objSession->get('idEmpresa'),
                                    'strPrefijoEmpresa' => $objSession->get('prefijoEmpresa'));
        $strAplicaCiclosFac = $serviceComercial->aplicaCicloFacturacion($arrayParametros);

        return $this->render('financieroBundle:AdmiFormatoDebito:listDebitosGeneral.html.twig',
                              array('strAplicaCiclosFacturacion' => $strAplicaCiclosFac));
    }	

    /**
     * Documentación para cuentaCabecerasAction
     *
     * Cuenta las cabeceras pendientes.
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0 28-11-2017 - Versión inicial.
     * 
     * @author Ricardo Robles <rrobles@telconet.ec>
     * @version 1.1 17-07-2019 - Se agrega validación para preguntar si el débito es tarjeta y permitir
     *                           la carga de archivos si tiene cabeceras con estado pendiente y no se reliza
     *                           la validación de característica 'DEBITOS PARCIALES DIARIOS' y la observación
     *                           'Cierre Final Manual'.
     * 
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 1.2 06-05-2020 - Se agrega validación para que cuando '$intTotal' sea 0, reutilizar el método 
     *                           'cuentaCabecerasParametroTarjeta' para que permita realizar una sola subida de archivo
     *                            al Banco Gye, debido a que se inactiva su característica asociada de 'DEBITOS PARCIALES DIARIOS'.  
     * 
     */
    public function cuentaCabecerasAction()
    {   
        $objRequest         = $this->getRequest();
        $intDebitoGeneralId = $objRequest->get("intDebitoGeneralId");
        $emFinanciero       = $this->get('doctrine')->getManager('telconet_financiero');
        $arrayParametros    = array('intDebitoGeneralId' => $intDebitoGeneralId,
                                    'strEstado'          => 'Pendiente');
        $arrayTarjeta = $emFinanciero->getRepository('schemaBundle:InfoDebitoCab')->getTipoCuentaTarjeta($arrayParametros);
        if($arrayTarjeta[0]['total'] == 0)
        {
            $arrayTotal = $emFinanciero->getRepository('schemaBundle:InfoDebitoCab')->cuentaCabecerasPorParametros($arrayParametros);
            $intTotal   = $arrayTotal[0]['total'] ? $arrayTotal[0]['total'] : 0;
            
            if($intTotal == 0)
            {
                $arrayTotal = $emFinanciero->getRepository('schemaBundle:InfoDebitoCab')->cuentaCabecerasParametroTarjeta($arrayParametros);
                $intTotal   = $arrayTotal[0]['total'] ? $arrayTotal[0]['total'] : 0;
            } 
        }
        else
        {
            $arrayTotal = $emFinanciero->getRepository('schemaBundle:InfoDebitoCab')->cuentaCabecerasParametroTarjeta($arrayParametros);
            $intTotal   = $arrayTotal[0]['total'] ? $arrayTotal[0]['total'] : 0;
        }
        
        return new Response($intTotal);
    }
    
    /**
     * Documentación para cuentaRegistroDebitoGeneralHistorialAction.
     * 
     * Cuenta los registros que tengan la observación Cierre Final Manual en la 
     * tabla INFO_DEBITO_GENERAL_HISTORIAL.
     * 
     * @author Ricardo Robles <rrobles@telconet.ec>
     * @version 1.0 21/03/2019 - Versión Inicial
     * 
     * @param array $arrayParametros[]                  
     *              'intDebitoGeneralId'  => Id del débito general
     *                    
     * @return Response total de registros con observación del Cierre Final Manual.
     */
    public function cuentaRegistroDebitoGeneralHistorialAction()
    {
        
        $objRequest         = $this->getRequest();
        $intDebitoGeneralId = $objRequest->get("intDebitoGeneralId");
        $emFinanciero       = $this->get('doctrine')->getManager('telconet_finaciero');
        
        $arrayParametros    = array('intDebitoGeneralId' => $intDebitoGeneralId);
        $arrayTotal         = $emFinanciero->getRepository('schemaBundle:InfoDebitoCab')
                                           ->getCuentaDebitoHistorialPorParametros($arrayParametros);

        $intTotal           = $arrayTotal[0]['total'] ? $arrayTotal[0]['total'] : 0;
        
        return new Response($intTotal);
    }

    /**
    *obtiene las cabeceras de los debitos segun debito general
    *En la ultima actualizacion se modifica la forma de leer los debitos retornados por la consulta
    *Ahora retorna un arreglo, tambien se agrega la columna nombre oficina en el grid de debitos  
    * @return object - response
    * @version 1.1
    * @author Andres Montero <amontero@telconet.ec> 
    * @since 2015-04-06 
    * @version 1.2
    * @author Edson Franco <efranco@telconet.ec> 
    * @since 16-06-2016 - Se agrega el impuesto con el cual se generó el débito 
    * @author Luis Cabrera <lcabrera@telconet.ec>
    * @version 1.3 22-11-2017 - Se agrega el parámetro cicloId para los ciclos de Facturación.
    *                           Se envían los parámetros por un array.
    *                           Se renombran los identificadores fuera del estándar.
    * 
    * @author Ricardo Robles <rrobles@telconet.ec>
    * @version 1.4 20-03-2019 - Se agrega la nueva opción Cierre Final Manual a los registros 
    * del grid que direcciona a la pantalla de cierre final manual.
    *
    *
    * @author Gustavo Narea <gnarea@telconet.ec>
    * @version 1.5 26-04-2021 - Se cambia el modo de descarga de archivo por motivos de migracion de archivos nfs.
    * Las url del los archivos de generacion y subida estan en las tablas respectivas.
    *
    * 
    * @author Ivan Romero <icromero@telconet.ec>
    * @version 1.5 06-05-2021 - Se cambia el modo de descarga de archivo por motivos de archivos almacenados en microservicio nfs.
    * Las url del los archivos de generacion y subida estan en las tablas respectivas.
    * se agrega logica para la generacion de debitos planificados
    *                         
    * @author Gustavo Narea <gnarea@telconet.ec>
    * @version 1.6 18-11-2021 - Se agrega validacion para presentar debitos de hasta ciertos dias atras.
    * 
    * @author Alex Arreaga <atarreaga@telconet.ec>
    * @version 1.7 14-09-2022 - Se agrega lógica para obtener el parámetro del flujo de generación de débitos por empresa. Se valida  
    *                           y obtiene la ruta nfs del archivo excel de clientes en caso de haber sido subido por la opción al
    *                           momento de la generación para presentar botón de descarga en el grid de listado de débitos generados.
    *
    */
	public function gridDebitosGeneralAction()
    {   
        $objRequest       = $this->getRequest();
        $objSession       = $objRequest->getSession();
        $strIdEmpresa     = $objSession->get('idEmpresa');
        $intLimit         = $objRequest->get("limit");
        $intPage          = $objRequest->get("page");
        $intStart         = $objRequest->get("start");
        $arrayFechaDesde  = explode('T', $objRequest->get("fechaDesde"));
        $arrayFechaHasta  = explode('T', $objRequest->get("fechaHasta"));
        $strEstado        = $objRequest->get("estado");
        $intCicloId       = $objRequest->get("cicloId");
        $floatValorTotal  = 0;
        $emGeneral            = $this->getDoctrine()->getManager('telconet_general');
        $intDiasAntesDefault  = 45;
        $emComercial          = $this->getDoctrine()->getManager('telconet');
        $strLinkArchivoNfsDeb = "";
        
        if($strEstado == "")
        {    
            $strEstado = "Activo";
        }    
        if(count($arrayFechaDesde)==1 && $arrayFechaDesde[0]=="" && count($arrayFechaHasta)==1 && $arrayFechaHasta[0]=="")
        {
            $arrayParametrosCab  =  array ('nombreParametro' => "LISTADO_DEBITOS");
            $objAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')->findOneBy($arrayParametrosCab);
            if (is_object($objAdmiParametroCab))
            {
                $intIdParametroCab = $objAdmiParametroCab->getId();
                $arrayParametrosDet  =  array ( 'valor1' => "DIAS_ATRAS_DEBITOS",
                                                'parametroId' => $intIdParametroCab, 
                                                "estado" => "Activo", "empresaCod" => $strIdEmpresa);
                $objAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findOneBy($arrayParametrosDet);
                try
                {
                    $intDiasAntes = intval($objAdmiParametroDet->getValor2());
                    if(!isset($intDiasAntes) || is_null($intDiasAntes))
                    {
                        $intDiasAntes = $intDiasAntesDefault;
                    }
                }
                catch(\Exception $e)
                {
                    error_log("No se encuentra el numero de dias anteriores para listarDebitos: ".$e->getMessage());
                    $intDiasAntes = $intDiasAntesDefault;
                }
                
                $objFechaActual = date("Y-m-d");                    
                $strDiasAntes = "- ".$intDiasAntes." days";
                $arrayFechaDesde[0] = date("Y-m-d",strtotime($objFechaActual.$strDiasAntes)); 
            }
        }
        
        $arrayParametros  = array ("strEstado"      => $strEstado,
                                   "strFechaDesde"  => $arrayFechaDesde[0],
                                   "strFechaHasta"  => $arrayFechaHasta[0],
                                   "intLimit"       => $intLimit,
                                   "intPage"        => $intPage,
                                   "intStart"       => $intStart,
                                   "strIdEmpresa"   => $strIdEmpresa,
                                   "intCicloId"     => $intCicloId);
        $em        = $this->get('doctrine')->getManager('telconet_financiero');
        $emGeneral = $this->get('doctrine')->getManager('telconet_general');
        $resultado = $em->getRepository('schemaBundle:InfoDebitoGeneral')
                        ->findDebitosGeneralPorCriterios($arrayParametros);
        $datos           = $resultado['registros'];
        $total           = $resultado['total'];       
        //RECORRE DÉBITOS OBTENIDOS
        for($indiceDebitos = 0;$indiceDebitos<count($datos);$indiceDebitos++)
        {  
            //OBTENGO EL TOTAL DE REGISTROS QUE TIENEN LA OBSERVACIÓN CIERRE FINAL MANUAL POR EL DÉBITO GENERAL        
            $arrayCaracteristica       = $em->getRepository('schemaBundle:InfoDebitoCab')
                                            ->getObtieneCaracteristica(array('idDebGen'=>$datos[$indiceDebitos]['id'],
                                                                             'strCaracteristica'=>'DEBITOS PARCIALES DIARIOS'));
            $arrayEstadoCierre         = $em->getRepository('schemaBundle:InfoDebitoCab')
                                            ->getValidaEstadoCierre(array('idDebGen'=>$datos[$indiceDebitos]['id']));  
           
            $arrayTotalDebitoHistorial = $em->getRepository('schemaBundle:InfoDebitoCab')
                                            ->getCuentaDebitoHistorialPorParametros(array("idDebGen"=>$datos[$indiceDebitos]['id']));
            $intTotalDebitoHistorial   = $arrayTotalDebitoHistorial[0]['total'] ? $arrayTotalDebitoHistorial[0]['total'] : 0;
            if($intTotalDebitoHistorial == 0)
            { 
                $intCaracteristica = $arrayCaracteristica[0]['total'];
            }
            else
            {
                $intCaracteristica = 0;
            }
          
            //OBTIENE RUTAS DE LAS ACCIONES (VER, VER PAGOS, DESCARGAR ZIP, VER RESPUESTAS)
            $urlVer = $this->generateUrl('respuestadebitos_list_debitos', array('idDebGen' => $datos[$indiceDebitos]['id']));
            $strUrlCierreFinal = $this->generateUrl('respuestadebitos_list_debitos_cierre_final_manual',
                                                    array('idDebGen' => $datos[$indiceDebitos]['id']));
            $urlPagos   = $this->generateUrl('admiformatodebito_excelpagos_por_debito_gen', array('idDebGen' => $datos[$indiceDebitos]['id']));
            $strUrlArchivo = '';
            if($datos[$indiceDebitos]['archivo'])
            {
                if (strpos(strtolower($datos[$indiceDebitos]['archivo']), '/') !== false) 
                { 
                    $strUrlArchivo = $datos[$indiceDebitos]['archivo'];
                }
                else
                {
                    $strUrlArchivo      = $this->generateUrl('admiformatodebito_download_archivo_generado', 
                                                      array('archivo' => $datos[$indiceDebitos]['archivo']));
                }
                $urlArchivoExcel = $this->generateUrl('admiformatodebito_exceldebitos_por_debito_gen', 
                                                      array('idDebGen' => $datos[$indiceDebitos]['id']));
            }
            $cabeceras = $em->getRepository('schemaBundle:InfoDebitoCab')
                            ->findDebitosPorDebitoGeneralIdNoInactivos($datos[$indiceDebitos]['id']);
            $strArchivos  = "";           
            $respuestas = $em->getRepository('schemaBundle:InfoDebitoRespuesta')->findByDebitoGeneralId($datos[$indiceDebitos]['id']);
            
            //OBTIENE CANTIDAD DE RESPUESTAS SUBIDAS
            foreach($respuestas as $respuesta)
            {
                if(($respuesta) && ($respuesta->getPathNoEncontrados()))
                {
                    $strNombreArchivoRespuestaModificado=  str_replace(array("_RESULTADO","_RESPUESTA",".XLS"), array("","",""), 
                        strtoupper($respuesta->getPathNoEncontrados()));
                    $respuesta->getPathNoEncontrados();
                    $strArchivo = $respuesta->getPathNoEncontrados();
                    
                    if (strpos(strtolower($strArchivo), '/') !== false) 
                    { 
                        $arrayNombres = explode('/',$strNombreArchivoRespuestaModificado);
                        $strNombreArchivoRespuestaModificado = $arrayNombres[count($arrayNombres)-1];
                    }
                    else
                    {
                        $strNombreArchivoRespuestaModificado = $this->generateUrl('admiformatodebito_download_archivo_respuesta', 
                                                                        array('archivo' => $respuesta->getPathNoEncontrados()));
                    }


                    $strArchivos = $strArchivos . "|" . $strArchivo . "*" . $strNombreArchivoRespuestaModificado;

                }
            }
            $linkVer                  = $urlVer;
            $strLinkCierreFinalManual = $strUrlCierreFinal;
            $linkArchivo              = '';
            if($strUrlArchivo)
            {    
                $linkArchivo = $strUrlArchivo;
            }
            //OBTIENE CANTIDAD DE DEBITOS POR ESTADOS
            $debitosPendientes = $em->getRepository('schemaBundle:InfoDebitoCab')
                ->findCountDebitosPendientesPorDebitoGeneralIdPorEstado($datos[$indiceDebitos]['id'], 'Pendiente');
            $debitosProcesados = $em->getRepository('schemaBundle:InfoDebitoCab')
                ->findCountDebitosPendientesPorDebitoGeneralIdPorEstado($datos[$indiceDebitos]['id'], 'Procesado');
            $debitosRechazados = $em->getRepository('schemaBundle:InfoDebitoCab')
                ->findCountDebitosPendientesPorDebitoGeneralIdPorEstado($datos[$indiceDebitos]['id'], 'Rechazado');
            $floatValorTotal       = $debitosPendientes[0]['recaudado'] + $debitosProcesados[0]['recaudado'] + $debitosRechazados[0]['recaudado'];
            $floatTotalDebitos = $debitosPendientes[0]['total'] + $debitosProcesados[0]['total'] + $debitosRechazados[0]['total'];
            
            /*
             * Bloque busca el porcentaje del impuesto aplicado al debito
             */
            $strDescripcionImpuesto = "12%";
            
            $objAdmiImpuesto = $emGeneral->getRepository('schemaBundle:AdmiImpuesto')->findOneById($datos[$indiceDebitos]['impuestoId']);
            
            if( $objAdmiImpuesto )
            {
                $strDescripcionImpuesto = $objAdmiImpuesto->getPorcentajeImpuesto().'%';
            }
            /*
             * Fin Bloque busca el porcentaje del impuesto aplicado al debito
             */
            
            //Se agrega llamado a flujo por empresa para validación y presentación del botón para descargar archivo excel del NFS 
            //de clientes débito en la pantalla del listado de débitos generados.
            $arrayFlujoGenDebito = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getOne('PARAM_GENERACION_DEBITOS','FINANCIERO','','FLUJO_GENERACION_DEBITO',
                                                         '','','','','', $strIdEmpresa);

            $strFlujoGeneracionDebito = $arrayFlujoGenDebito['valor1'] ? $arrayFlujoGenDebito['valor1'] : 'NO';
            
            if($strFlujoGeneracionDebito == 'SI')
            {
                $objAdmiCaract = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneBy( array('estado'                    => 'Activo',
                                                                   'descripcionCaracteristica' => 'RUTA_NFS_GENERACION_DEBITO') );
                
                $intIdCaractNfsDeb = is_object($objAdmiCaract) ? $objAdmiCaract->getId() : 0;
                
                $arrayRutaArchivoNfs = $em->getRepository('schemaBundle:InfoDebitoGeneralCaract')
                                        ->getRutaArchivoDebitoNfs(array('intDebitoGeneralId'  => $datos[$indiceDebitos]['id'],
                                                                        'intCaracteristicaId' => $intIdCaractNfsDeb,
                                                                        'strEstado'           => 'Activo') ); 

                $strLinkArchivoDebNfs   = $arrayRutaArchivoNfs[0]['valor'] ? $arrayRutaArchivoNfs[0]['valor'] : ""; 

            } //fin $strFlujoGeneracionDebito
            
            $arreglo[] = array(
                'id'                       => $datos[$indiceDebitos]['id'],
                'fechaCreacion'            => $datos[$indiceDebitos]['feCreacion'],
                'bancos'                   => $datos[$indiceDebitos]['nombreGrupo'],
                'totalRegistros'           => $floatTotalDebitos,
                'valorTotal'               => $floatValorTotal,
                'pendientes'               => $debitosPendientes[0]['total'],
                'valorPendientes'          => ($debitosPendientes[0]['recaudado']) ? $debitosPendientes[0]['recaudado'] : 0,
                'procesados'               => $debitosProcesados[0]['total'],
                'valorProcesados'          => ($debitosProcesados[0]['debitado']) ? $debitosProcesados[0]['debitado'] : 0 ,
                'rechazados'               => $debitosRechazados[0]['total'],
                'valorRechazados'          => ($debitosRechazados[0]['recaudado']) ? $debitosRechazados[0]['recaudado'] : 0,
                'usuarioCreacion'          => $datos[$indiceDebitos]['usrCreacion'],
                'estado'                   => $datos[$indiceDebitos]['estado'],
                'planificado'              => $datos[$indiceDebitos]['planificado'] ? 'SI':'NO',
                'linkArchivo'              => $linkArchivo,
                'linkArchivoExcel'         => $urlArchivoExcel,
                'linkVer'                  => $linkVer,
                'linkCierreFinalManual'    => $strLinkCierreFinalManual,
                'linkRespuestas'           => $strArchivos,
                'linkPagos'                => $urlPagos,
                'intCaracteristica'        => $intCaracteristica,
                'ejecutando'               => $datos[$indiceDebitos]['ejecutando'],
                'ejecutandoCierre'         => $arrayEstadoCierre[0]['estado'],
                'oficinaClientes'          => $datos[$indiceDebitos]['nombreOficina'],
                'descripcionImpuesto'      => $strDescripcionImpuesto,
                'nombreCiclo'              => $datos[$indiceDebitos]['nombreCiclo'],
                'linkArchivoDebNfs'        => $strLinkArchivoDebNfs
            );
        }
        if(!empty($arreglo))
        {
            $response = new Response(json_encode(array('total' => $total, 'pagos' => $arreglo)));
        }    
        else
        {
            $arreglo[] = array();
            $response  = new Response(json_encode(array('total' => $total, 'pagos' => $arreglo)));
        }
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }

    public function downloadArchivoDebitoGeneradoAction($archivo)
    {
        $path_telcos = $this->container->getParameter('path_telcos');
        $path =  $path_telcos.'telcos/web/public/uploads/debitos/'.$archivo;
        $content = file_get_contents($path);
        $response = new Response();
        //set headers
        $response->headers->set('Content-Type', 'mime/type');
        $response->headers->set('Content-Disposition', 'attachment;filename="'.$archivo);
        $response->setContent($content);
        return $response;
    }    

    /**
     * Documentación para obtieneCiclosPorEstadoAction
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0 21-11-2017 Se obtienen los ciclos según los estados enviados por parámetro.
     */
    public function obtieneCiclosPorEstadoAction()
    {
        $objSession         = $this->getRequest()->getSession();
        $strEmpresaCod      = $objSession->get('idEmpresa');
        $arrayCiclos        = null;
        $objRespuesta       = new JsonResponse();
        $emFinanciero       = $this->get('doctrine')->getManager('telconet_financiero');
        $arrayListAdmiCiclo = $emFinanciero->getRepository('schemaBundle:AdmiCiclo')
                                           ->findBy(array('estado'     => array('Activo','Inactivo'),
                                                          'empresaCod' => $strEmpresaCod));
        foreach($arrayListAdmiCiclo as $objCiclo)
        {
            $arrayCiclos[] = array('intIdCiclo' => $objCiclo->getId(), 'strNombreCiclo' => $objCiclo->getNombreCiclo());
        }

        if($arrayCiclos)
        {
            $intTotal = count($arrayCiclos);
            $objRespuesta->setData(array('intTotal' => $intTotal, 'arrayRegistros' => $arrayCiclos));
        }
        else
        {
            $arrayCiclos[] = array();
            $intTotal      = 0;
            $objRespuesta->setData(array('intTotal' => 0, 'arrayRegistros' => $arrayCiclos));
        }

        return $objRespuesta;
    }
    
    
    /**
     * Documentación para función obtieneEscenariosPorEstadoAction
     * 
     * Se obtienen los escenarios según los estados para la generación de los débitos.
     * 
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 13-05-2020 
     */
    public function obtieneEscenariosPorEstadoAction()
    {
        $objJsonResponse       = new JsonResponse();
        $serviceInfoDebitoCab  = $this->get('financiero.InfoDebitoCab');
        $objRequest            = $this->getRequest();
        $intIdEmpresa          = $objRequest->getSession()->get('idEmpresa');
        $arrayParametros['nombreParametro'] = 'ESCENARIOS_DEBITOS';
        $arrayParametros['estado']          = 'Activo';
        $arrayParametros['idEmpresa']       = $intIdEmpresa;

        $arrayEscenarios = $serviceInfoDebitoCab->obtenerEscenariosPorEstado($arrayParametros);
        $objJsonResponse->setData($arrayEscenarios);
        return $objJsonResponse;

    }
    
    /**
     * Documentación para función obtieneMontosEscenario2Action
     * 
     * Se obtienen los montos a debitar del escenario 2, para la generación de los débitos.
     * 
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 13-05-2020 
     */
    public function obtieneMontosEscenario2Action()
    {
        $objJsonResponse       = new JsonResponse();
        $serviceInfoDebitoCab  = $this->get('financiero.InfoDebitoCab');
        $arrayParametros['nombreParametro'] = 'MONTO_DEBITADO_ESCENARIO_2';
        $arrayParametros['estado']          = 'Activo';

        $arrayMontosEscenario2 = $serviceInfoDebitoCab->obtenerMontosEscenario2($arrayParametros);
        $objJsonResponse->setData($arrayMontosEscenario2);
        return $objJsonResponse;

    }
    
   /**
     * Documentación para función obtieneCuotasEscenario3Action
     * 
     * Se obtiene el número de cuotas de NDI a debitar del escenario 3, para la generación de los débitos.
     * 
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 17-06-2020 
     */
    public function obtieneCuotasEscenario3Action()
    {
        $objJsonResponse       = new JsonResponse();
        $serviceInfoDebitoCab  = $this->get('financiero.InfoDebitoCab');
        $arrayParametros['nombreParametro'] = 'CANT_CUOTA_NDI_ESCENARIO_3';
        $arrayParametros['estado']          = 'Activo';

        $arrayCuotasEscenario3 = $serviceInfoDebitoCab->obtenerCuotasEscenario3($arrayParametros);
        $objJsonResponse->setData($arrayCuotasEscenario3);
        return $objJsonResponse;

    }
    

    public function downloadArchivoDebitosNoEncontradosAction($archivo)
    {
        $path_telcos = $this->container->getParameter('path_telcos');
        $path =  $path_telcos.'telcos/web/public/uploads/respuesta_debitos/'.$archivo;
        $content = file_get_contents($path);
        $response = new Response();
        //set headers
        $response->headers->set('Content-Type', 'mime/type');
        $response->headers->set('Content-Disposition', 'attachment;filename="'.$archivo);
        $response->setContent($content);
        return $response;
    }
        
        
    /**
    * Documentación para funcion 'excelPagosPorDebitoGenAction'.
    * genera archivo de excel con los pagos de los debitos que se generan por banco
    * @param idDebGen - id del debito general 
    * @author <amontero@telconet.ec>
    * @since 06/01/2015
    * @return objeto para crear excel
    */ 
    /**
    * @Secure(roles="ROLE_87-2038")
    */    
	public function excelPagosPorDebitoGenAction($idDebGen)
    {

        $objPHPExcel = new PHPExcel();
        $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array(' memoryCacheSize ' => '1024MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        // Establecer propiedades
        $objPHPExcel->getProperties()
            ->setCreator("Telcos")
            ->setLastModifiedBy("Telcos")
            ->setTitle("Documento Excel de Pagos")
            ->setSubject("Documento Excel de Pagos")
            ->setDescription("")
            ->setKeywords("Excel Office 2007 openxml php")
            ->setCategory("Excel");

        $styleArray = array(
            'font' => array(
                'bold' => true
            )
        );

        $emfn = $this->get('doctrine')->getManager('telconet_financiero');
        $pagos = $emfn->getRepository('schemaBundle:InfoDebitoCab')->findPagosPorDebitoGeneral($idDebGen);
        $i = 2;
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'TIPO_PAGO')
                    ->setCellValue('B1', 'CLIENTE')
                    ->setCellValue('C1', 'LOGIN')
                    ->setCellValue('D1', 'NUMERO_PAGO')
                    ->setCellValue('E1', 'VALOR')
                    ->setCellValue('F1', 'BANCO')
                    ->setCellValue('G1', 'TIPO CUENTA_TARJETA')
                    ->setCellValue('H1', 'NUMERO_CUENTA_BANCO')
                    ->setCellValue('I1', 'FACTURA');
        
        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('D1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('E1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('F1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('G1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('H1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('I1')->applyFromArray($styleArray);


        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);

        foreach($pagos as $pago)
        {
            // Agregar Informacion
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $i, $pago['codigoTipoDocumento']);
            if($pago['puntoId'])
            {    
                $entityPunto = $emfn->getRepository('schemaBundle:InfoPunto')->find($pago['puntoId']);
            }    
            if($pago['referenciaId'])
            {    
                $entityFactura = $emfn->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($pago['referenciaId']);
            }    
            if($entityPunto)
            {
                if($entityPunto->getPersonaEmpresaRolId()->getPersonaId()->getRazonSocial())
                {
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('B' . $i, $entityPunto->getPersonaEmpresaRolId()->getPersonaId()->getRazonSocial());
                }
                else
                {
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('B' . $i, $entityPunto->getPersonaEmpresaRolId()->getPersonaId()->getNombres()
                            . " " . $entityPunto->getPersonaEmpresaRolId()->getPersonaId()->getApellidos());
                }
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $i, $entityPunto->getLogin());
            }
            //desencripta numero de cuenta
            /* @var $serviceCrypt \telconet\seguridadBundle\Service\CryptService */
            $serviceCrypt                     = $this->get('seguridad.Crypt');
            $strNumeroCtaTarjeta = $serviceCrypt->descencriptar($pago['numeroCuentaBanco']); 
            
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $i, $pago['numeroPago']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $i, $pago['valorTotal']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $i, $pago['descripcionBanco']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $i, $pago['descripcionCuenta']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $i, $strNumeroCtaTarjeta);

            if($entityFactura)
            {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I' . $i, $entityFactura->getNumeroFacturaSri());
            }    
            $i++;
        }
        // Renombrar Hoja
        $objPHPExcel->getActiveSheet()->setTitle('Pagos por Débito');
        // Establecer la hoja activa, para que cuando se abra el documento se muestre primero.
        $objPHPExcel->setActiveSheetIndex(0);
        // Se modifican los encabezados del HTTP para indicar que se envía un archivo de Excel.
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="reporte_pagos_por_debito.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        throw new Exception('Generar arhivo excel reporte pagos por débito.');
    }       

    
    /**
     * Documentación para función excelDiferenciaDebitosGenerados.
     * 
     * Genera y devuelve un archivo en excel con información del cliente y las diferencias 
     * entre pagos procesados y generados en telcos.
     * 
     * @author Ricardo Robles <rrobles@telconet.ec>
     * @version 1.0 09/04/2019
     *                    
     */
    public function excelDiferenciaDebitosGeneradosAction()
    {      
        $objRequest         = $this->get('request');
        $intDebitoCabId     = $objRequest->get('debitoCabId');
        $fltValorDiferencia = 0;
        $objPHPExcel        = new PHPExcel();
        $objCacheMethod     = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $objCacheSettings   = array(' memoryCacheSize ' => '1024MB');

        PHPExcel_Settings::setCacheStorageMethod($objCacheMethod, $objCacheSettings);
        //Establecer propiedades
        $objPHPExcel->getProperties()
                    ->setCreator("Telcos")
                    ->setLastModifiedBy("Telcos")
                    ->setTitle("Documento Excel de diferencia de débitos")
                    ->setSubject("Documento Excel diferencia débitos")
                    ->setDescription("")
                    ->setKeywords("Excel Office 2007 openxml php")
                    ->setCategory("Excel");

        $emFinaciero = $this->get('doctrine')->getManager('telconet_financiero');
        $arrayStyle  = array(
                             'font' => array(
                                             'bold' => true
                                            )
                            );
        $intContador = 2;
        
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'LOGIN')
                    ->setCellValue('B1', 'IDENTIFICACIÓN')
                    ->setCellValue('C1', 'CLIENTE')
                    ->setCellValue('D1', 'MONTO ENVIADÓ')
                    ->setCellValue('E1', 'MONTO DE RECAUDADO')
                    ->setCellValue('F1', 'FECHA ENVIO')
                    ->setCellValue('G1', 'DIFERENCIA')
                    ->setCellValue('H1', 'MOTIVO RECHAZO');

        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($arrayStyle);
        $objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($arrayStyle);
        $objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($arrayStyle);
        $objPHPExcel->getActiveSheet()->getStyle('D1')->applyFromArray($arrayStyle);
        $objPHPExcel->getActiveSheet()->getStyle('E1')->applyFromArray($arrayStyle);
        $objPHPExcel->getActiveSheet()->getStyle('F1')->applyFromArray($arrayStyle);
        $objPHPExcel->getActiveSheet()->getStyle('G1')->applyFromArray($arrayStyle);
        $objPHPExcel->getActiveSheet()->getStyle('H1')->applyFromArray($arrayStyle);

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutosize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutosize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);

         $arrayDebitosClientes=$emFinaciero->getRepository('schemaBundle:InfoDebitoCab')
                                           ->getDiferenciaDebitosClientes(array('debitoCabId' =>$intDebitoCabId));

        foreach($arrayDebitosClientes as $cliente)
        {   $fltValorDiferencia = 0;            
            $fltValorDiferencia = $cliente['total'] - $cliente['valor_debitado'];

            if($fltValorDiferencia != 0)
            {
                // Agregar Información
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $intContador, $cliente['login']);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $intContador, $cliente['identificacion_cliente']);

                //Si nombres y apellidos son nulos, seteo la razón social en la columna cliente
                if($cliente['nombres'] == null && $cliente['apellidos'] == null)
                {
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $intContador, $cliente['razon_social']); 
                }
                else
                {
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $intContador, $cliente['nombres'] ." ". $cliente['apellidos']);
                }

                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $intContador, $cliente['valor_total']);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $intContador, $cliente['total']);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $intContador, $cliente['fe_creacion']);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $intContador, $fltValorDiferencia);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $intContador, $cliente['observacion_rechazo']);

                $intContador++;

            }
        }

        // Renombrar Hoja
         $strNombreArchivo = "Diferencias de débitos";
         $objPHPExcel->getActiveSheet()->setTitle($strNombreArchivo);
         // Establecer la hoja activa, para que cuando se abra el documento se muestre primero.
         $objPHPExcel->setActiveSheetIndex(0);
         // Se modifican los encabezados del HTTP para indicar que se envía un archivo de Excel.
         header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
         header('Content-Disposition: attachment;filename="reporte_debitos_generados.xls"');
         header('Cache-Control: max-age=0');
         $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
         $objWriter->save('php://output');
         throw new Exception('Generar arhivo excel débitos generados.');

    }
      
    /**
     * Documentación para funcion 'grabaPagoScript'.
     * genera pago o anticipo si fuese necesario para un debito especifico
     * Actualizacion: Se registra el id del debito general historial en la cabecera del pago
     * @param $arrayParametros
     * stringEmpresaId             : id de la empresa
     * intOficinaId                : id de la oficina
     * stringUsuario               : usuario que crea el pago
     * objEntityManagerComercial   : entidad del esquema comercial
     * objEntityManagerFinanciero  : entidad del esquema financiero
     * objDebitoGeneralHistorial   : entidad del debito general historial
     * objInfoDebitoDet            : entidad del detalle del debito
     * objAdmiBancoTipoCuenta      : entidad del banco tipo cuenta
     * floatValorPagado            : valor del pago
     * stringNumeroOrden           : numero del debito
     * stringfechaProceso          : fecha de proceso
     * @author <amontero@telconet.ec>
     * @since 06/01/2015
     * @version 1.1 11/04/2016 amontero@telconet.ec
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 09-10-2016 - Se cambia la forma de consultar las facturas de los puntos que poseen saldo para que el método pague la factura
     *                           desde la más antigua hasta la más actual.
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 1.3 08-01-2020 - Se modifica para que obtenga el saldo de la factura por medio de la función de base 'F_SALDO_X_FACTURA'.
     *                           Se crea logs en la tabla INFO_ERROR para monitorear el proceso de débito por motivo que las facturas no se cierran. 
     *                           Se redondea a dos decimales el saldo de la factura y el valor pagado para corregir error que se genera al  
     *                           verificar si el saldo ya cubre el valor de la factura por motivo a que las facturas no se cierran.
     * 
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.4 28-05-2020 - Se modifica función para que procese los pagos de los débitos pendientes, 
     *                           en base a escenarios y filtros que se seleccionaron al momento de generar los débitos.
     *                         - ESCENARIO_BASE: Escenario basado en la generación de los débitos de los clientes 
     *                                           con saldo pendiente de sus facturas activas.               
     *                         - ESCENARIO_1: Escenario basado en la generación de los débitos de los clientes que 
     *                                        tengan un saldo pendiente de su factura recurrente mensual emitida de acuerdo con cada ciclo.
     * 
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.5 23-06-2020 - Se modifica función para que procese los pagos de los débitos pendientes, 
     *                           en base al escenario 3 que se seleccionó al momento de generar los débitos.              
     *                         - ESCENARIO_3: Escenario basado en la generación de los débitos de los clientes que 
     *                                        tengan un saldo pendiente de las NDI's Diferidas.
     *                         - Se añade función findPtoClienteActivoPadreFacturacion que obtiene el punto activo como padre de facturación, 
     *                           para agregar el anticipo generado.
     *
     */     
    function grabaPagoScript($arrayParametros)
    {
        $empresaId                 = $arrayParametros["stringEmpresaId"];
        $oficinaId                 = $arrayParametros["intOficinaId"];
        $user                      = $arrayParametros["stringUsuario"];
        $em                        = $arrayParametros["objEntityManagerComercial"];
        $emfn                      = $arrayParametros["objEntityManagerFinanciero"];
        $objDebitoGeneralHistorial = $arrayParametros["objDebitoGeneralHistorial"];
        $entityDebitoDet           = $arrayParametros["objInfoDebitoDet"];
        $entityBancoTipoCuenta     = $arrayParametros["objAdmiBancoTipoCuenta"];
        $valorPagado               = $arrayParametros["floatValorPagado"];
        $numeroOrden               = $arrayParametros["stringNumeroOrden"];
        $fechaProceso              = $arrayParametros["stringfechaProceso"];
        $strTipoEscenario          = $arrayParametros["strTipoEscenario"];
        $strFiltroEscenario        = $arrayParametros["strFiltroEscenario"];
        
        $serviceInfoPago = $this->get('financiero.InfoPago');
        $serviceUtil     = $this->get('schema.Util');
        if($numeroOrden)
        {        
            $comentarioAnticipo='Anticipo generado por debito. '.$numeroOrden;
        }    
        else
        {
            $comentarioAnticipo='Anticipo generado por debito.';		
        }
        
        if( is_object($entityDebitoDet) )
        {
            if($entityDebitoDet->getEstado()=='Procesado')
            {
                $arrayPuntos  = $em->getRepository('schemaBundle:InfoPunto')
                                   ->findBy( array('personaEmpresaRolId' => $entityDebitoDet->getPersonaEmpresaRolId()), array('id' => 'ASC') );
                
                $arrayIdPuntoConSaldo = array();
                $i                    = 0;
                $entityPunto          = null;

                foreach($arrayPuntos as $punto)
                {
                    $arraySaldo = $em->getRepository ( 'schemaBundle:InfoPagoCab' )->obtieneSaldoPorPunto ( $punto->getId () );

                    if( !empty($arraySaldo) )
                    {
                        $arrayTmpSaldo = ( !empty($arraySaldo[0]) ? $arraySaldo[0] : array() );
                        $floatSaldo    = ( isset($arrayTmpSaldo['saldo']) ? $arrayTmpSaldo['saldo'] : 0 );
                        $floatSaldo    = ( !empty($floatSaldo) ? round($floatSaldo, 2) : 0 );

                        if( floatval($floatSaldo) > 0 ) 
                        {
                            $arrayIdPuntoConSaldo[$i] = $punto->getId ();
                            $i ++;
                        }//( floatval($floatSaldo) > 0 ) 
                    }//( !empty($saldoarr) )                     
                }                    

                $entityFormaPago = $em->getRepository('schemaBundle:AdmiFormaPago')->findByCodigoFormaPago('DEB');

                if( !empty($arrayIdPuntoConSaldo) )
                {
                    //Bloque que obtiene las facturas abiertas ordenadas de la más antigua a la actual de los puntos con deuda pendiente
                    
                    if($strTipoEscenario == 'ESCENARIO_BASE' || $strTipoEscenario == 'ESCENARIO_2')
                    {
                        $arrayParametrosFacturas                       = array();
                        $arrayParametrosFacturas["strCodEmpresa"]      = $empresaId;
                        $arrayParametrosFacturas["arrayPuntos"]        = $arrayIdPuntoConSaldo;
                        $arrayParametrosFacturas["arrayTipoDocumento"] = array('FAC','FACP');
                        $arrayParametrosFacturas["arrayInEstados"]     = array('Activo', 'Activa', 'Courier');
                        $arrayParametrosFacturas["orderBy"]            = "feCreacionAsc";
                        
                        $arrayFacturas = $emfn->getRepository( 'schemaBundle:InfoDocumentoFinancieroCab' )
                                              ->findDocumentosFinancieros($arrayParametrosFacturas); 
                        
                        $arrayParamProcesaFactura                                = array();
                        $arrayParamProcesaFactura["arrayFacturas"]               = $arrayFacturas;
                        $arrayParamProcesaFactura["floatValorPagado"]            = $valorPagado;
                        $arrayParamProcesaFactura["objEntityManagerComercial"]   = $em;
                        $arrayParamProcesaFactura["objEntityManagerFinanciero"]  = $emfn;
                        $arrayParamProcesaFactura["strNumeroOrden"]              = $numeroOrden;
                        $arrayParamProcesaFactura["intOficinaId"]                = $oficinaId;
                        $arrayParamProcesaFactura["strEmpresaId"]                = $empresaId;
                        $arrayParamProcesaFactura["objInfoDebitoDet"]            = $entityDebitoDet;
                        $arrayParamProcesaFactura["strUsuario"]                  = $user;
                        $arrayParamProcesaFactura["objDebitoGeneralHistorial"]   = $objDebitoGeneralHistorial;
                        $arrayParamProcesaFactura["strfechaProceso"]             = $fechaProceso;
                        $arrayParamProcesaFactura["entityFormaPago"]             = $entityFormaPago;
                        $arrayParamProcesaFactura["entityBancoTipoCuenta"]       = $entityBancoTipoCuenta;

                        $valorPagado = $this->procesaDocumentosObtieneValorPagadoAction($arrayParamProcesaFactura);
                    }
                    
                    if($strTipoEscenario == 'ESCENARIO_1')
                    {                                             
                        foreach($arrayIdPuntoConSaldo as $intIdPunto)
                        {        
                            $arrayFacturas = array();
                            
                            $arrayParametrosFacturas["intIdPunto"]          = $intIdPunto;
                            $arrayParametrosFacturas["strFiltroEscenario"]  = $strFiltroEscenario;
                            $arrayParametrosFacturas["strIdEmpresa"]        = $empresaId;

                            $arrayFacturas = $emfn->getRepository( 'schemaBundle:InfoDocumentoFinancieroCab' )
                                                  ->findFacturasMensualesFiltroFecha($arrayParametrosFacturas);

                            if( !empty($arrayFacturas) )
                            {
                                
                                $arrayParamProcesaFactura                                = array();
                                $arrayParamProcesaFactura["arrayFacturas"]               = $arrayFacturas;
                                $arrayParamProcesaFactura["floatValorPagado"]            = $valorPagado;
                                $arrayParamProcesaFactura["objEntityManagerComercial"]   = $em;
                                $arrayParamProcesaFactura["objEntityManagerFinanciero"]  = $emfn;
                                $arrayParamProcesaFactura["strNumeroOrden"]              = $numeroOrden;
                                $arrayParamProcesaFactura["intOficinaId"]                = $oficinaId;
                                $arrayParamProcesaFactura["strEmpresaId"]                = $empresaId;
                                $arrayParamProcesaFactura["objInfoDebitoDet"]            = $entityDebitoDet;
                                $arrayParamProcesaFactura["strUsuario"]                  = $user;
                                $arrayParamProcesaFactura["objDebitoGeneralHistorial"]   = $objDebitoGeneralHistorial;
                                $arrayParamProcesaFactura["strfechaProceso"]             = $fechaProceso;
                                $arrayParamProcesaFactura["entityFormaPago"]             = $entityFormaPago;
                                $arrayParamProcesaFactura["entityBancoTipoCuenta"]       = $entityBancoTipoCuenta;

                                $valorPagado = $this->procesaDocumentosObtieneValorPagadoAction($arrayParamProcesaFactura);                        
                            }   
                        } 
                        
                        if(round($valorPagado,2)>0)
                        {
                            $arrayFacturas                                 = array();
                            $arrayParametrosFacturas                       = array();
                            $arrayParametrosFacturas["strCodEmpresa"]      = $empresaId;
                            $arrayParametrosFacturas["arrayPuntos"]        = $arrayIdPuntoConSaldo;
                            $arrayParametrosFacturas["arrayTipoDocumento"] = array('FAC','FACP');
                            $arrayParametrosFacturas["arrayInEstados"]     = array('Activo', 'Activa', 'Courier');
                            $arrayParametrosFacturas["orderBy"]            = "feCreacionAsc";

                            $arrayFacturas = $emfn->getRepository( 'schemaBundle:InfoDocumentoFinancieroCab' )
                                      ->findDocumentosFinancieros($arrayParametrosFacturas);
                            
                            $arrayParamProcesaFactura                                = array();
                            $arrayParamProcesaFactura["arrayFacturas"]               = $arrayFacturas;
                            $arrayParamProcesaFactura["floatValorPagado"]            = $valorPagado;
                            $arrayParamProcesaFactura["objEntityManagerComercial"]   = $em;
                            $arrayParamProcesaFactura["objEntityManagerFinanciero"]  = $emfn;
                            $arrayParamProcesaFactura["strNumeroOrden"]              = $numeroOrden;
                            $arrayParamProcesaFactura["intOficinaId"]                = $oficinaId;
                            $arrayParamProcesaFactura["strEmpresaId"]                = $empresaId;
                            $arrayParamProcesaFactura["objInfoDebitoDet"]            = $entityDebitoDet;
                            $arrayParamProcesaFactura["strUsuario"]                  = $user;
                            $arrayParamProcesaFactura["objDebitoGeneralHistorial"]   = $objDebitoGeneralHistorial;
                            $arrayParamProcesaFactura["strfechaProceso"]             = $fechaProceso;
                            $arrayParamProcesaFactura["entityFormaPago"]             = $entityFormaPago;
                            $arrayParamProcesaFactura["entityBancoTipoCuenta"]       = $entityBancoTipoCuenta;

                            $valorPagado = $this->procesaDocumentosObtieneValorPagadoAction($arrayParamProcesaFactura);
                        }   
                    }
                    
                    if($strTipoEscenario == 'ESCENARIO_3')
                    {    
                        //Inicializa valores para procesar las NDI Diferidas
                        $arrayParamProcesaDocNdi                                = array();
                        $arrayParamProcesaDocNdi["objEntityManagerComercial"]   = $em;
                        $arrayParamProcesaDocNdi["objEntityManagerFinanciero"]  = $emfn;
                        $arrayParamProcesaDocNdi["strNumeroOrden"]              = $numeroOrden;
                        $arrayParamProcesaDocNdi["intOficinaId"]                = $oficinaId;
                        $arrayParamProcesaDocNdi["strEmpresaId"]                = $empresaId;
                        $arrayParamProcesaDocNdi["objInfoDebitoDet"]            = $entityDebitoDet;
                        $arrayParamProcesaDocNdi["strUsuario"]                  = $user;
                        $arrayParamProcesaDocNdi["objDebitoGeneralHistorial"]   = $objDebitoGeneralHistorial;
                        $arrayParamProcesaDocNdi["strfechaProceso"]             = $fechaProceso;
                        $arrayParamProcesaDocNdi["entityFormaPago"]             = $entityFormaPago;
                        $arrayParamProcesaDocNdi["entityBancoTipoCuenta"]       = $entityBancoTipoCuenta;
                            
                        //Inicializa valores para consultar NDI Diferidas
                        $arrayParamConsultaNdiDif                        = array();
                        $arrayParamConsultaNdiDif["intIdPerEmpRol"]      = $entityDebitoDet->getPersonaEmpresaRolId();
                        $arrayParamConsultaNdiDif["strFiltroEscenario"]  = $strFiltroEscenario;
                        $arrayParamConsultaNdiDif["strIdEmpresa"]        = $empresaId;

                        $arrayNdiDifCuotas = $emfn->getRepository( 'schemaBundle:InfoDocumentoFinancieroCab' )
                                                  ->findNdiDiferidasFiltroCuotas($arrayParamConsultaNdiDif);

                        if( !empty($arrayNdiDifCuotas) )
                        {                               
                            $arrayParamProcesaDocNdi["arrayFacturas"]     = $arrayNdiDifCuotas;
                            $arrayParamProcesaDocNdi["floatValorPagado"]  = $valorPagado;
                            $valorPagado = $this->procesaDocumentosObtieneValorPagadoAction($arrayParamProcesaDocNdi); 
                        }   
                        
                        if(round($valorPagado,2)>0)
                        {
                            $arrayParamConsultaNdiDif                    = array();
                            $arrayParamConsultaNdiDif["intIdPerEmpRol"]  = $entityDebitoDet->getPersonaEmpresaRolId();
                            $arrayParamConsultaNdiDif["strIdEmpresa"]    = $empresaId;
                            $arrayNdiDif = $emfn->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                ->findNdiDiferidas($arrayParamConsultaNdiDif);
                            
                            if( !empty($arrayNdiDif) )
                            { 
                                $arrayParamProcesaDocNdi["arrayFacturas"]    = $arrayNdiDif;
                                $arrayParamProcesaDocNdi["floatValorPagado"] = $valorPagado;  
                                $valorPagado = $this->procesaDocumentosObtieneValorPagadoAction($arrayParamProcesaDocNdi);      
                            }                            
                        }   
                        
                        if(round($valorPagado,2)>0)
                        {
                            $arrayParamConsultaFactNdi                       = array();
                            $arrayParamConsultaFactNdi["strCodEmpresa"]      = $empresaId;
                            $arrayParamConsultaFactNdi["arrayPuntos"]        = $arrayIdPuntoConSaldo;
                            $arrayParamConsultaFactNdi["arrayTipoDocumento"] = array('FAC','FACP');
                            $arrayParamConsultaFactNdi["arrayInEstados"]     = array('Activo', 'Activa', 'Courier');
                            $arrayParamConsultaFactNdi["orderBy"]            = "feCreacionAsc";

                            $arrayFactNdi = $emfn->getRepository( 'schemaBundle:InfoDocumentoFinancieroCab' )
                                                  ->findDocumentosFinancieros($arrayParamConsultaFactNdi);
                            
                            if( !empty($arrayFactNdi) )
                            { 
                                $arrayParamProcesaDocNdi["arrayFacturas"]     = $arrayFactNdi;
                                $arrayParamProcesaDocNdi["floatValorPagado"]  = $valorPagado;
                                $valorPagado = $this->procesaDocumentosObtieneValorPagadoAction($arrayParamProcesaDocNdi);
                            }
                        }   
                    }
                }//( !empty($arrayIdPuntoConSaldo) )

                //CREA ANTICIPO SI ES NECESARIO
                //--****************************************************
                if(round($valorPagado,2)>0)
                {
                    //SE CREA LA CABECERA DEL ANTICIPO
                    $entityAnticipoCab  = new InfoPagoCab();			
                    $tipoDocumento='ANTS';

                    //Se busca el punto padre de facturación en estado 'Activo' y se le asigna al anticipo.
                    //Caso contrario se busca cualquier punto padre de facturación.
                    $arrayParametros                    = array();
                    $arrayParametros["strEstado"]       = 'Activo';
                    $arrayParametros["intIdPerEmpRol"]  = $entityDebitoDet->getPersonaEmpresaRolId();
                    $intIdPuntoPadreFact = $emfn->getRepository('schemaBundle:InfoPunto')->findPtoClienteActivoPadreFacturacion($arrayParametros);

                    if($intIdPuntoPadreFact > 0)
                    {
                        $tipoDocumento='ANT';
                        $entityAnticipoCab->setPuntoId($intIdPuntoPadreFact);
                    }
                    else
                    {
                        $entityPunto = $em->getRepository('schemaBundle:InfoPunto')
                                          ->findPrimerPtoClientePadreActivoPorPersonaEmpresaRolId($entityDebitoDet->getPersonaEmpresaRolId());

                        if($entityPunto)
                        {
                            $tipoDocumento='ANT';
                            $entityAnticipoCab->setPuntoId($entityPunto->getId());
                        }
                    }      
                 
                    //SI SE ENCONTRO PUNTO ENTONCES GRABA ANTICIPO
                    $entityAnticipoCab->setEmpresaId($empresaId);
                    $entityAnticipoCab->setEstadoPago('Pendiente');
                    $entityAnticipoCab->setFeCreacion(new \DateTime('now'));
                    $entityAnticipoCab->setDebitoDetId($entityDebitoDet->getId());					
                    //Obtener la numeracion de la tabla Admi_numeracion
                    $datosNumeracionAnticipo = $em->getRepository('schemaBundle:AdmiNumeracion')->findByEmpresaYOficina($empresaId,$oficinaId,"ADEB");
                    $strSecuenciaAsig    = '';
                    $strSecuenciaAsig    = str_pad($datosNumeracionAnticipo->getSecuencia(),7, "0", STR_PAD_LEFT); 
                    $intNumeroDeAnticipo = $datosNumeracionAnticipo->getNumeracionUno()."-".
                                           $datosNumeracionAnticipo->getNumeracionDos()."-".$strSecuenciaAsig;
                    //Actualizo la numeracion en la tabla
                    $intNumeroAct=($datosNumeracionAnticipo->getSecuencia()+1);
                    $datosNumeracionAnticipo->setSecuencia($intNumeroAct);
                    $em->persist($datosNumeracionAnticipo);
                    $em->flush();

                    $entityAdmiTipoDocumento=$emfn->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')
                    ->findOneByCodigoTipoDocumento($tipoDocumento);

                    $entityAnticipoCab->setTipoDocumentoId($entityAdmiTipoDocumento);
                    $entityAnticipoCab->setNumeroPago($intNumeroDeAnticipo);
                    $entityAnticipoCab->setOficinaId($oficinaId);
                    $entityAnticipoCab->setUsrCreacion($user);
                    $entityAnticipoCab->setValorTotal($valorPagado);
                    $entityAnticipoCab->setComentarioPago($comentarioAnticipo);
                    $entityAnticipoCab->setDebitoGeneralHistorialId($objDebitoGeneralHistorial);                
                    $emfn->persist($entityAnticipoCab);		
                    //CREA LOS DETALLES DEL ANTICIPO
                    $entityAnticipoDet= new InfoPagoDet();
                    $entityAnticipoDet->setEstado('Pendiente');
                    $entityAnticipoDet->setFeCreacion(new \DateTime('now'));
                    if($fechaProceso)
                            $entityAnticipoDet->setFeDeposito(new \DateTime($fechaProceso));
                    $entityAnticipoDet->setUsrCreacion($user);
                    $entityAnticipoDet->setValorPago($valorPagado);
                    $entityAnticipoDet->setBancoTipoCuentaId($entityBancoTipoCuenta->getId());
                    $entityAnticipoDet->setNumeroReferencia($numeroOrden);

                    $entityAnticipoDet->setNumeroCuentaBanco($entityDebitoDet->getNumeroTarjetaCuenta());					
                    $entityAnticipoDet->setComentario($comentarioAnticipo);
                    $entityAnticipoDet->setDepositado('N');
                    $entityAnticipoDet->setPagoId($entityAnticipoCab);
                    $entityAnticipoDet->setFormaPagoId($entityFormaPago[0]->getId());
                    $emfn->persist($entityAnticipoDet);
                    $emfn->flush(); 

                    //INGRESA HISTORIAL DE ANTICIPO
                    $serviceInfoPago->ingresaHistorialPago($entityAnticipoCab,'Pendiente',new \DateTime('now'),$user,null,$comentarioAnticipo);                
                }//(round($valorPagado,2)>0)
            }//($entityDebitoDet->getEstado()=='Procesado')
        }//( is_object($entityDebitoDet) )
    }    
        
    /**
     * Documentación para funcion 'procesaDocumentosObtieneValorPagadoAction'.
     * 
     * Función que se encarga de procesar las facturas (creación de pagos, cierre de facturas, creación de historiales) del cliente,
     * correspondientes al proceso de pagos por débitos pendientes.
     * 
     * @param $arrayParametros[
     *                         arrayFacturas                : arreglo de facturas
     *                         floatValorPagado             : valor pagado
     *                         objEntityManagerComercial    : conexión de Esquema Comercial
     *                         objEntityManagerFinanciero   : conexión de Esquema Financiero
     *                         strNumeroOrden               : numero de orden
     *                         intOficinaId                 : id Oficina
     *                         strEmpresaId                 : id Empresa
     *                         objInfoDebitoDet             : objeto InfoDebitoDet
     *                         strUsuario                   : usuario
     *                         objDebitoGeneralHistorial    : objeto DebitoGeneralHistorial
     *                         strfechaProceso              : fecha de proceso
     *                         entityFormaPago              : entidad Forma de Pago
     *                         entityBancoTipoCuenta        : entidad Banco Tipo Cuenta]
     * 
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 28-05-2020.
     */ 
    public function procesaDocumentosObtieneValorPagadoAction($arrayParamProcesaFactura) 
    {
                
        $arrayFacturas             = $arrayParamProcesaFactura["arrayFacturas"];
        $floatValorPagado          = $arrayParamProcesaFactura["floatValorPagado"];
        $emComercial               = $arrayParamProcesaFactura["objEntityManagerComercial"];
        $emFinanciero              = $arrayParamProcesaFactura["objEntityManagerFinanciero"];
        $strNumeroOrden            = $arrayParamProcesaFactura["strNumeroOrden"];
        $intOficinaId              = $arrayParamProcesaFactura["intOficinaId"];
        $strEmpresaId              = $arrayParamProcesaFactura["strEmpresaId"];
        $entityDebitoDet           = $arrayParamProcesaFactura["objInfoDebitoDet"];
        $strUser                   = $arrayParamProcesaFactura["strUsuario"];
        $objDebitoGeneralHistorial = $arrayParamProcesaFactura["objDebitoGeneralHistorial"];
        $strFechaProceso           = $arrayParamProcesaFactura["strfechaProceso"];
        $entityFormaPago           = $arrayParamProcesaFactura["entityFormaPago"];
        $entityBancoTipoCuenta     = $arrayParamProcesaFactura["entityBancoTipoCuenta"];
        $serviceUtil               = $this->get('schema.Util');
        $serviceInfoPago           = $this->get('financiero.InfoPago');
        
        
        if( !empty($arrayFacturas) )
        { 
            foreach($arrayFacturas as $entityFacturaAbierta)
            { 
                $floatValorDebito = ( !empty($floatValorPagado) ? round($floatValorPagado, 2) : 0 );

                if( floatval($floatValorDebito) > 0 )
                {
                    //Se modifica el saldo de factura para obtener el valor desde la función F_SALDO_X_FACTURA.
                    $floatSaldoFactura    = 0;
                    $arrayParametrosSaldo = array();
                    $arrayParametrosSaldo = array('intIdDocumento'     => $entityFacturaAbierta->getId (),
                                                  'strFeConsultaHasta' => '',
                                                  'strTipoConsulta'    => 'saldo');
                    $floatSaldoFactura    = $emFinanciero->getRepository("schemaBundle:InfoDocumentoFinancieroCab")
                                                              ->getSaldoXFactura($arrayParametrosSaldo); 

                    $floatSaldoFactura = round ($floatSaldoFactura,2);
                    $floatValorPagado  = round ($floatValorPagado, 2);

                    $floatValorCabeceraPago = 0;
                    $strTipoDoc             = "PAG";
                    $strEstadoPag           = "Cerrado";
                    $entityPunto            = $emComercial->getRepository('schemaBundle:InfoPunto')->find($entityFacturaAbierta->getPuntoId());
                   
                    if($strNumeroOrden)
                    {
                        $strComentarioPago='Pago generado por debito. '.$strNumeroOrden;
                    }
                    else
                    {
                        $strComentarioPago='Pago generado por debito.';
                    }
                    //CREA CABECERA DEL PAGO
                    //--*************************
                    $entityPagoCab = new InfoPagoCab();
                    $entityPagoCab->setPuntoId($entityPunto->getId());
                    $entityPagoCab->setOficinaId($intOficinaId);
                    $entityPagoCab->setEmpresaId($strEmpresaId);
                    $entityPagoCab->setDebitoDetId($entityDebitoDet->getId());	
                    
                    //Obtener la numeración de la tabla Admi_numeracion
                    $strDatosNumeracion = $emComercial->getRepository('schemaBundle:AdmiNumeracion')
                                                      ->findByEmpresaYOficina($strEmpresaId,$intOficinaId,'PDEB');
                    $strSecuenciaAsig   = str_pad($strDatosNumeracion->getSecuencia(),7, "0", STR_PAD_LEFT); 
                    $strNumeroDePago    = $strDatosNumeracion->getNumeracionUno()."-".$strDatosNumeracion->getNumeracionDos()."-".$strSecuenciaAsig;
                 
                    //Actualizo la numeración en la tabla
                    $strNumeroAct = ($strDatosNumeracion->getSecuencia()+1);
                    $strDatosNumeracion->setSecuencia($strNumeroAct);
                    $emComercial->persist($strDatosNumeracion);
                    $emComercial->flush();

                    $entityPagoCab->setNumeroPago($strNumeroDePago);
                    $entityPagoCab->setValorTotal($floatValorPagado);
                    $entityPagoCab->setEstadoPago($strEstadoPag);
                    $entityPagoCab->setComentarioPago($strComentarioPago);
                    $entityPagoCab->setFeCreacion(new \DateTime('now'));
                    $entityPagoCab->setUsrCreacion($strUser);
                    $entityAdmiTipoDocumento = $emFinanciero->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')
                                                            ->findOneByCodigoTipoDocumento($strTipoDoc);
                    $entityPagoCab->setTipoDocumentoId($entityAdmiTipoDocumento);
                    $entityPagoCab->setDebitoGeneralHistorialId($objDebitoGeneralHistorial);
                    $emFinanciero->persist($entityPagoCab);			

                    //SE VERIFICA SI EL PAGO YA CUBRE LA FACTURA y SI ES ASI CREA ARREGLO ANTICIPOs 
                    $intAccionProceso = 0;
                    
                    if($floatSaldoFactura == $floatValorPagado)
                    {
                        $entityFacturaAbierta->setEstadoImpresionFact('Cerrado');
                        $emFinanciero->persist($entityFacturaAbierta);
                        $floatValorPago   = $floatValorPagado;
                        $floatValorPagado = $floatValorPagado - $floatSaldoFactura;
                        $intAccionProceso = 1;
                    }
                    elseif($floatSaldoFactura < $floatValorPagado)
                    {
                        $entityFacturaAbierta->setEstadoImpresionFact('Cerrado');
                        $emFinanciero->persist($entityFacturaAbierta);
                        $floatValorPago   = $floatSaldoFactura;
                        $floatValorPagado =	$floatValorPagado - $floatSaldoFactura;
                        $intAccionProceso = 2;
                    }
                    else
                    {
                        $floatValorPago    = $floatValorPagado;
                        $floatValorPagoAux = $floatValorPago;
                        $floatValorPagado  = $floatValorPagoAux - $floatValorPago;
                        $intAccionProceso  = 3;
                    }		
                    
                    //Graba historial de la factura  
                    $serviceUtil->insertError('Telcos+',
                                              'AdmiFormatoDebitoController->grabaPagoScript', 
                                              'Se ingresa Historial por cierre de Documento ID_DOCUMENTO='.
                                              $entityFacturaAbierta->getId (). '  ESTADO_IMPRESION_FACT='.
                                              $entityFacturaAbierta->getEstadoImpresionFact ().
                                              '  SALDO_FACTURA='.$floatSaldoFactura.
                                              '  VALOR_PAGO='. $floatValorPago.
                                              '  SALDO_VALOR_PAGADO='.$floatValorPagado.
                                              '  BANDERA='.$intAccionProceso,
                                              $strUser, 
                                              '127.0.0.1'
                                             );

                    $entityHistorialFactura = new InfoDocumentoHistorial();
                    $entityHistorialFactura->setDocumentoId($entityFacturaAbierta);
                    $entityHistorialFactura->setEstado($entityFacturaAbierta->getEstadoImpresionFact());
                    $entityHistorialFactura->setFeCreacion(new \DateTime('now'));
                    $entityHistorialFactura->setUsrCreacion($strUser);
                    $emFinanciero->persist($entityHistorialFactura);

                    //CREA DETALLES DEL PAGO
                    $entityPagoDet          = new InfoPagoDet();
                    $floatValorCabeceraPago = $floatValorCabeceraPago + $floatValorPago;		
                    $entityPagoDet->setPagoId($entityPagoCab);
                    $entityPagoDet->setDepositado('N');
                    $entityPagoDet->setFeCreacion(new \DateTime('now'));
                    if($strFechaProceso)
                    {
                        $entityPagoDet->setFeDeposito(new \DateTime($strFechaProceso));
                    }
                    $entityPagoDet->setUsrCreacion($strUser);
                    $entityPagoDet->setFormaPagoId($entityFormaPago[0]->getId());
                    $entityPagoDet->setValorPago($floatValorPago);
                    $entityPagoDet->setBancoTipoCuentaId($entityBancoTipoCuenta->getId());
                    $entityPagoDet->setNumeroReferencia($strNumeroOrden);
                    $entityPagoDet->setNumeroCuentaBanco($entityDebitoDet->getNumeroTarjetaCuenta());					
                    $entityPagoDet->setComentario($strComentarioPago);
                    $entityPagoDet->setEstado($strEstadoPag);
                    $entityPagoDet->setReferenciaId($entityFacturaAbierta->getId());	
                    $emFinanciero->persist($entityPagoDet);
                    
                    //Se setea valor total de cabecera y hago persistencia
                    $entityPagoCab->setValorTotal($floatValorCabeceraPago);
                    $emFinanciero->persist($entityPagoCab);
                    $emFinanciero->flush(); 

                    //Ingresa historial para el pago
                    $serviceInfoPago->ingresaHistorialPago($entityPagoCab,$strEstadoPag,new \DateTime('now'),$strUser,null,$strComentarioPago);
                }//(round($floatValorPagado,2)>0)
            }//foreach($arrayFacturas as $entityFacturaAbierta)
        }//( !empty($arrayFacturas) )
        
        return $floatValorPagado;
    }

    
    public function gridDebitosPorPuntoAction() {
        $request = $this->getRequest();
        $session  = $request->getSession();
        $cliente_sesion=$session->get('cliente');
        $clienteId='';$puntoId='';
        $oficinaId=$request->getSession()->get('idOficina');
        if($cliente_sesion)
            $clienteId=$cliente_sesion['id_persona'];

        $limit = $request->get("limit");
        $page = $request->get("page");
        $start = $request->get("start");
        $puntoId=$request->get("idPunto");
        $em = $this->get('doctrine')->getManager('telconet_financiero');
        $em1 = $this->get('doctrine')->getManager('telconet');
        $resultado = $em->getRepository('schemaBundle:InfoDebitoDet')->findDebitosPorPuntoId( $puntoId,$limit,$start);
        $datos = $resultado['registros'];
        $total = $resultado['total'];

        foreach ($datos as $datos):
            $entityPersonaEmpresaRol=$em1->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($datos->getPersonaEmpresaRolId());
            $entityCabeceraDebito=$em->getRepository('schemaBundle:InfoDebitoCab')->find($datos->getDebitoCabId());
            if(!$datos->getFeUltMod()){
                $fechaProceso=strval(date_format($entityCabeceraDebito->getFeUltMod(), "d/m/Y G:i"));
                $usrProceso=$entityCabeceraDebito->getUsrUltMod();
            }else
            {
                $fechaProceso=strval(date_format($datos->getFeUltMod(), "d/m/Y G:i"));
                $usrProceso=$datos->getUsrUltMod();                
            }    
            $entityBancoTipoCuenta=$em1->getRepository('schemaBundle:AdmiBancoTipoCuenta')->find($entityCabeceraDebito->getBancoTipoCuentaId());
            $banco="";
            if($entityBancoTipoCuenta->getTipoCuentaId()->getEsTarjeta()=='S'){
                $banco=$entityBancoTipoCuenta->getTipoCuentaId()->getDescripcionCuenta();
            }else{
                $banco=$entityBancoTipoCuenta->getBancoId()->getDescripcionBanco()." ".$entityBancoTipoCuenta->getTipoCuentaId()->getDescripcionCuenta();
            }
			
            $arreglo[] = array(
                'id' => $datos->getId(),
		'banco' => $banco,
                'total' => $datos->getValorTotal(),
                'fechaCreacion' => strval(date_format($datos->getFeCreacion(), "d/m/Y G:i")),
                'usuarioCreacion' => $usrProceso,
                'estado' => $datos->getEstado(),
		'observacionRechazo' => $datos->getObservacionRechazo(),
                'fechaProceso'=>$fechaProceso
            );
        endforeach;
        if (!empty($arreglo))
            $response = new Response(json_encode(array('total' => $total, 'pagos' => $arreglo)));
        else {
            $arreglo[] = array();
            $response = new Response(json_encode(array('total' => $total, 'pagos' => $arreglo)));
        }
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }   

    /**
    * Documentación para funcion 'gridDebitosPorClienteAction'.
    * obtiene los debitos de un cliente por IdPersonaEmpresaRol
    * el cliente lo recibe por metodo request
    * Actualizacion: Se corrige que el banco del debito lo obtenga desde la cabecera del debito
    * y ya no desde el contrato del cliente  
    * @author <amontero@telconet.ec>
    * @since 23/09/2014
    * @version 1.1 03/05/2016 <amontero@telconet.ec>
    * @author <eholguin@telconet.ec>
    * @version 1.2 20/03/2017 <eholguin@telconet.ec> Se realiza llamada a función que retorna los débitos generales de un cliente
    *                                                según el array enviado como parámetro.
    * @return objeto - response
    */        
    public function gridDebitosPorClienteAction()
    {
        $request             = $this->getRequest();
        $intLimit            = $request->get("limit");
        $intStart            = $request->get("start");
        $personaEmpresaRolId = $request->get("idPer");
        $emFinanciero        = $this->get('doctrine')->getManager('telconet_financiero');
        
        $arrayParametros                            = array();
        $arrayParametros['intPersonaEmpresaRolId']  = $personaEmpresaRolId;
        $arrayParametros['intStart']                = $intStart;
        $arrayParametros['intLimit']                = $intLimit;   
        $arrayParametros['arrayEstados']            = array('Procesado','Rechazado');   
      
        $arrayResultado      = $emFinanciero->getRepository('schemaBundle:InfoDebitoDet')
                                            ->getDebitosGeneralesPorPersonaEmpresaRolId($arrayParametros);
        
        
        $arrayDatos             = $arrayResultado['arrayRegistros'];
        $intTotal               = $arrayResultado['intTotal'];
        foreach($arrayDatos as $arrayDatos)
        {
            $strBanco                = "";            
           
            if(!$arrayDatos['feUltModDet'])
            {
                $fechaProceso = strval(date_format($arrayDatos['feUltModCab'], "d/m/Y G:i"));
                $strUsrProceso   = $arrayDatos['usrUltModCab'];
            }
            else
            {
                $fechaProceso = strval(date_format($arrayDatos['feUltModDet'], "d/m/Y G:i"));
                $strUsrProceso   = $arrayDatos['usrUltModDet'];
            }
            $fltvalorDebito=0;
            //Si el estado es Procesado entonces
            //obtiene valor debitado porque existe posibilidad 
            //que no se haya debitado todo lo que se envio al banco.
            //Si no es Procesado entonces obtiene el valor total 
            //que es el valor que se envio a debitar.
            if($arrayDatos['estadoDebitoDet']=='Procesado')
            {
                $fltvalorDebito=$arrayDatos['valorDebitadoDet'];
            }
            else
            {
                $fltvalorDebito=$arrayDatos['valorTotalDet'];
            }
            
            if($arrayDatos['esTarjeta'] == 'S')
            {
                $strBanco = $arrayDatos['descripcionCuenta'];
            }
            else
            {
                $strBanco = $arrayDatos['descripcionBanco'];
            }            
            $arrayDebitos[] = array(
                'id'                 => $arrayDatos['idDebitoGeneral'],
                'banco'              => $strBanco,
                'total'              => $fltvalorDebito,
                'fechaCreacion'      => strval(date_format($arrayDatos['feCreacionDebitoDet'], "d/m/Y G:i")),
                'usuarioCreacion'    => $strUsrProceso,
                'estado'             => $arrayDatos['estadoDebitoDet'],
                'observacionRechazo' => $arrayDatos['observacionRechazo'],
                'fechaProceso'       => $fechaProceso
            );
        }   
        if(!empty($arrayDebitos))
        {
            $response = new Response(json_encode(array('total' => $intTotal, 'pagos' => $arrayDebitos)));
        }    
        else
        {
            $arrayDebitos[] = array();
            $response = new Response(json_encode(array('total' => $intTotal, 'pagos' => $arrayDebitos)));
        }
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }

        
    /**
    * Documentación para funcion 'excelDebitosGeneradosAction'.
    * genera archivo de excel con los debitos que se generan por banco
    * @param idDebGen - id del debito general 
    * @author <amontero@telconet.ec>
    * @since 06/01/2015
    * @return objeto para crear excel
    */      
    /**
    * @Secure(roles="ROLE_87-2037")
    */      
    public function excelDebitosGeneradosAction($idDebGen)
    {
        $db               = $this->container->getParameter('database_dsn');		
        $oci_con          = oci_connect($this->container->getParameter('user_financiero'),$this->container->getParameter('passwd_financiero'), $db);        
        $cursa            = oci_new_cursor($oci_con); 
        $claveDesencripta = $this->container->getParameter('secret');        
        $objPHPExcel      = new PHPExcel();
        $cacheMethod      = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings    = array(' memoryCacheSize ' => '1024MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        // Establecer propiedades
        $objPHPExcel->getProperties()
            ->setCreator("Telcos")
            ->setLastModifiedBy("Telcos")
            ->setTitle("Documento Excel de Pagos")
            ->setSubject("Documento Excel de Pagos")
            ->setDescription("")
            ->setKeywords("Excel Office 2007 openxml php")
            ->setCategory("Excel");

        $styleArray = array(
                'font' => array('bold' => true)
        );
        $request = $this->getRequest();

        $emfn = $this->get('doctrine')->getManager('telconet_financiero');
        $emcom = $this->get('doctrine')->getManager('telconet');
        $debitosCab = $emfn->getRepository('schemaBundle:InfoDebitoCab')->findByDebitoGeneralId($idDebGen);
        $i = 2;
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'CLIENTE')
            ->setCellValue('B1', 'OFICINA_CLIENTE')
            ->setCellValue('C1', 'NRO TARJETA/CUENTA')
            ->setCellValue('D1', 'FECHA_VENCIMIENTO')
            ->setCellValue('E1', 'CODIGO_SEGURIDAD')
            ->setCellValue('F1', 'VALOR')
            ->setCellValue('G1', 'ESTADO_CLIENTE');

        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('D1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('E1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('F1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('G1')->applyFromArray($styleArray);

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);

        foreach($debitosCab as $debitoCab)
        {            
            $arrayDebitosDet = $emfn->getRepository('schemaBundle:InfoDebitoDet')
                                    ->findDetallesDebitoPorCabecera($debitoCab->getEmpresaId(),$debitoCab->getId(),$claveDesencripta,$cursa,$oci_con);
            if (count($arrayDebitosDet)>0)
            {    
                for($indiceExcel=0;$indiceExcel < count($arrayDebitosDet); $indiceExcel++)
                {
                    if ( $arrayDebitosDet[$indiceExcel]['numero_cta_tarjeta'] )
                    {    
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $i, $arrayDebitosDet[$indiceExcel]['cliente']);                    
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $i, $arrayDebitosDet[$indiceExcel]['nombre_oficina']);
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit('C' . $i, $arrayDebitosDet[$indiceExcel]['numero_cta_tarjeta'],
                            \PHPExcel_Cell_DataType::TYPE_STRING);                    
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $i, $arrayDebitosDet[$indiceExcel]['anio_vencimiento'] . "-" . 
                                                                                     $arrayDebitosDet[$indiceExcel]['mes_vencimiento']);
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $i, $arrayDebitosDet[$indiceExcel]['codigo_verificacion']);
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $i, $arrayDebitosDet[$indiceExcel]['valor_total']);
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $i, $arrayDebitosDet[$indiceExcel]['estado']);
                        $i++;
                    }
                }
            }
        }
        // Renombrar Hoja
        $objPHPExcel->getActiveSheet()->setTitle('Pagos por Debito');
        // Establecer la hoja activa, para que cuando se abra el documento se muestre primero.
        $objPHPExcel->setActiveSheetIndex(0);
        // Se modifican los encabezados del HTTP para indicar que se envia un archivo de Excel.
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="reporte_debitos_generados.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }   

    /**
    * Documentación para funcion 'getGrupoDebitosPorDebitoGeneralAjax'.
    * Este método obtiene los detalles del grupo de debitos por debitoGeneralId
    *
    * @return object $response
    *
    * @author Andres Montero <amontero@telconet.ec>
    * @version 1.2 30-03-2015
    */
    public function getGrupoDebitosPorDebitoGeneralAjaxAction()
    {
        $objRequest                    = $this->getRequest();
        $intDebitoGeneralId            = $objRequest->get('debitoGeneralId');     
        $em                            = $this->get('doctrine')->getManager('telconet_financiero');
        $estados                       = array('Pendiente','Procesado');
        $objInfoDebitoGeneral          = $em->getRepository('schemaBundle:InfoDebitoGeneral')->findBy(array('id'=>$intDebitoGeneralId));
        $arrayResultado                = "";
        $response                      = "";
        $floatTotalCabecerasPendientes = 0;
        foreach ($objInfoDebitoGeneral as $datos)
        {
            $arrayCabecerasDebito = $em->getRepository('schemaBundle:InfoDebitoCab')->findByDebitoGeneralId($datos->getId());
            foreach($arrayCabecerasDebito as $cabecera)
            {
                if($cabecera->getEstado()=='Pendiente')
                {
                    $floatTotalCabecerasPendientes++;
                }    
            }    
            $objAdmiBancoTipoCuenta        = $em->getRepository('schemaBundle:AdmiGrupoArchivoDebitoCab')->find($datos->getGrupoDebitoId());
            $arrayDebitosPendientes        = $em->getRepository('schemaBundle:InfoDebitoCab')
                ->findCountDebitosPendientesPorDebitoGeneralIdPorEstado($datos->getId(),'Pendiente');
            $arrayDebitosProcesados        = $em->getRepository('schemaBundle:InfoDebitoCab')
                ->findCountDebitosPendientesPorDebitoGeneralIdPorEstado($datos->getId(),'Procesado');
            $arrayDebitosRechazados        = $em->getRepository('schemaBundle:InfoDebitoCab')
                ->findCountDebitosPendientesPorDebitoGeneralIdPorEstado($datos->getId(),'Rechazado');
            $arrayResultado[] = array(
                'idDebitoGeneral'          => $datos->getId(),
                'nombreGrupo'              => $objAdmiBancoTipoCuenta->getNombreGrupo(),  
                'estado'                   => $datos->getEstado(),
                'debitosPendientes'        => $arrayDebitosPendientes[0]['total'],
                'debitosProcesados'        => $arrayDebitosProcesados[0]['total'],
                'debitosRechazados'        => $arrayDebitosRechazados[0]['total'],
                'totalCabeceras'           => count($arrayCabecerasDebito),
                'totalCabecerasPendientes' => $floatTotalCabecerasPendientes               
            );
        }
        if (!empty($arrayResultado)){
            $response = new Response(json_encode(array('debitosCab' => $arrayResultado)));
        }    
        else{
            $arrayResultado[] = array();
            $response = new Response(json_encode(array('debitosCab' => $arrayResultado)));
        }  
        return $response;
    }
    
    /**
    * Documentación para funcion 'anulaReabreCabeceraDebito'.
    * Esta funcion anula cabeceras de debitos
    * @author <amontero@telconet.ec>
    * @since 15/08/2014
    * @return objeto response
    */
    /**
    * @Secure(roles="ROLE_87-1677")
    */    
    public function anulaCabeceraDebitoAction() {
        $request = $this->getRequest();
        $strDebitosCabId=$request->request->get('debitosCabId'); 
        $strUsuario=$request->getSession()->get('user');
        $arryDebitosCab=  explode('|', $strDebitosCabId);
        $serviceDebito = $this->get('financiero.InfoDebitoCab');
        $response = new Response();
        $response->headers->set('Content-type', 'text/json');
            $response->setContent(
                json_encode($serviceDebito->anularCabecerasDebito($arryDebitosCab,$strUsuario,new \DateTime('now'))));   
        return $response;
    }    

    /**
    * Documentación para funcion 'anulaReabreCabeceraDebito'.
    * Esta funcion reabre cabeceras de debitos
    * @author <amontero@telconet.ec>
    * @since 15/08/2014
    * @return objeto response
    */
    /**
    * @Secure(roles="ROLE_87-1678")
    */     
    public function reabreCabeceraDebitoAction() {
        $request = $this->getRequest();
        $strDebitosCabId=$request->request->get('debitosCabId'); 
        $strUsuario=$request->getSession()->get('user');
        $arryDebitosCab=  explode('|', $strDebitosCabId);
        $serviceDebito = $this->get('financiero.InfoDebitoCab');
        $response = new Response();
        $response->headers->set('Content-type', 'text/json');
        $response->setContent(json_encode($serviceDebito->reabreCabecerasDebito($arryDebitosCab,$strUsuario,new \DateTime('now'))));    
        return $response;
    }
    
    
    /**
     * Documentación para funcion 'getInformacionCombosDebitosAction'.
     * 
     * Esta función obtiene la información correspondiente que será mostrada en los combos de Tarjetas y Formatos en los débitos
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 23-08-2016
     * 
     * @author Ivan Romero <icromero@telconet.ec>
     * @version 1.1 22-03-2021 - Se agrega el parametro intIdGrupoCab  para agregar funcionalidad al combo Formatos
     *
     * 
     * @return objeto JsonResponse
     */
    public function getInformacionCombosDebitosAction()
    {
        $response              = new JsonResponse();
        $emGeneral             = $this->getDoctrine()->getManager('telconet_general');
        $intIdParametroCargo   = 0;
        $strEstadoActivo       = "Activo";
        $intTotal              = 0;
        $objRequest            = $this->get('request');
        $objSession            = $objRequest->getSession();
        $intIdEmpresaCod       = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $arrayEntidades        = array();
        $strNombreParametroCab = $objRequest->get('strNombreParametro') ? $objRequest->get('strNombreParametro') : '';
        //Variable que contiene el valor seleccionado por el usuario para obtener la información correspondiente a dicho valor
        $intValorSeleccionado  = $objRequest->get('intValor') ? $objRequest->get('intValor') : 0;
        
        if( !empty($strNombreParametroCab) )
        {
            $objParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                         ->findOneBy( array('nombreParametro' => $strNombreParametroCab, 'estado' => $strEstadoActivo) );

            if($objParametroCab)
            {
                $intIdParametroCargo = $objParametroCab->getId();
            }

            $arrayResultados = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                         ->getParametrosByCriterios( array( 'estado'        => $strEstadoActivo, 
                                                                            'parametroId'   => $intIdParametroCargo,
                                                                            'intEmpresaCod' => $intIdEmpresaCod,
                                                                            'valor2'        => $intValorSeleccionado ) );

            if( !empty($arrayResultados['registros']) )
            {
                $intTotal = $arrayResultados['total'];

                foreach($arrayResultados['registros'] as $arrayParametroDet)
                {
                    $arrayItem                   = array();
                    $arrayItem['intId']          = $arrayParametroDet['valor1'];
                    //Variable que contiene el valor configurado en la tabla de parametros, para el correcto funcionamiento del combo Formatos
                    $arrayItem['intIdGrupoCab']          = $arrayParametroDet['valor3'];
                    $arrayItem['strDescripcion'] = $arrayParametroDet['descripcion'];

                    $arrayEntidades[] = $arrayItem;
                }//foreach($arrayResultados['registros'] as $arrayDato)
            }//( !empty($arrayResultados['registros']) )
        }//( !empty($strNombreParametroCab) )
            
        $response->setData( array('total' => $intTotal, 'encontrados' => $arrayEntidades) );
        
        return $response;
    }

 /**
     * Documentación para funcion 'getMensajesDebitosAction'.
     * 
     * Esta función obtiene la información correspondiente a los mensajes utilizados en la nueva funcionliadad de Debitos Planificados
     * 
     * @author Ivan Romero <icromero@telconet.ec>
     * @version 1.0 14-04-2021 
     *
     * 
     * @return objeto JsonResponse
     */
    public function getMensajesDebitosAction()
    {
        $strResponse              = new JsonResponse();
        $emGeneral             = $this->getDoctrine()->getManager('telconet_general');
        $intIdParametroCargo   = 0;
        $strEstadoActivo       = "Activo";
        $strTipoParam          = "Mensaje";
        $intTotal              = 0;
        $objRequest            = $this->get('request');
        $objSession            = $objRequest->getSession();
        $intIdEmpresaCod       = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $arrayEntidades        = array();
        $strNombreParametroCab = $objRequest->get('strNombreParametro') ? $objRequest->get('strNombreParametro') : '';
        //Variable que contiene el valor seleccionado por el usuario para obtener la información correspondiente a dicho valor
        $intValorSeleccionado  = $objRequest->get('intValor') ? $objRequest->get('intValor') : 0;
        
        if( !empty($strNombreParametroCab) )
        {
            $objParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                         ->findOneBy( array('nombreParametro' => $strNombreParametroCab, 'estado' => $strEstadoActivo) );

            if($objParametroCab)
            {
                $intIdParametroCargo = $objParametroCab->getId();
            }

            $arrayResultados = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                         ->getParametrosByCriterios( array( 'estado'        => $strEstadoActivo, 
                                                                            'parametroId'   => $intIdParametroCargo,
                                                                            'strEmpresaCod' => $intIdEmpresaCod,
                                                                            'valor2'        => $strTipoParam ) );

            if( !empty($arrayResultados['registros']) )
            {
                $intTotal = $arrayResultados['total'];

                foreach($arrayResultados['registros'] as $arrayParametroDet)
                {
                    $arrayItem                   = array();
                    $arrayItem['strDescripcion'] = $arrayParametroDet['descripcion'];
                    $arrayItem['strMensaje']          = $arrayParametroDet['valor1'];

                    $arrayEntidades[] = $arrayItem;
                }//foreach($arrayResultados['registros'] as $arrayDato)
            }//( !empty($arrayResultados['registros']) )
        }//( !empty($strNombreParametroCab) )
            
        $strResponse->setData( array('total' => $intTotal, 'encontrados' => $arrayEntidades) );
        
        return $strResponse;
    }
    
    /**
     * Documentación para funcion 'getParametroFlujoPlanificadoAction'.
     * 
     * Esta función obtiene la información correspondiente a los mensajes utilizados en la nueva funcionliadad de Debitos Planificados
     * 
     * @author Ivan Romero <icromero@telconet.ec>
     * @version 1.0 14-04-2021 
     *
     * 
     * @return objeto JsonResponse
     */
    public function getParametroFlujoPlanificadoAction()
    {
        $strResponse              = new JsonResponse();
        $emGeneral             = $this->getDoctrine()->getManager('telconet_general');
        $intIdParametroCargo   = 0;
        $strEstadoActivo       = "Activo";
        $strTipoParam          = "Parametro";
        $strFlujoPlanificado   = "FlujoDebitoPlanificado";
        $intTotal              = 0;
        $objRequest            = $this->get('request');
        $objSession            = $objRequest->getSession();
        $intIdEmpresaCod       = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $arrayEntidades        = array();
        $strNombreParametroCab = $objRequest->get('strNombreParametro') ? $objRequest->get('strNombreParametro') : '';
        //Variable que contiene el valor seleccionado por el usuario para obtener la información correspondiente a dicho valor
        $intValorSeleccionado  = $objRequest->get('intValor') ? $objRequest->get('intValor') : 0;
        
        if( !empty($strNombreParametroCab) )
        {
            $objParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                         ->findOneBy( array('nombreParametro' => $strNombreParametroCab, 'estado' => $strEstadoActivo) );

            if($objParametroCab)
            {
                $intIdParametroCargo = $objParametroCab->getId();
            }

            $arrayResultados = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                         ->getParametrosByCriterios( array( 'estado'        => $strEstadoActivo, 
                                                                            'parametroId'   => $intIdParametroCargo,
                                                                            'strEmpresaCod' => $intIdEmpresaCod,
                                                                            'valor2'        => $strTipoParam,
                                                                            'descripcion'   => $strFlujoPlanificado ) );

            if( !empty($arrayResultados['registros']) )
            {
                $intTotal = $arrayResultados['total'];

                foreach($arrayResultados['registros'] as $arrayParametroDet)
                {
                    $arrayItem                   = array();
                    $arrayItem['strDescripcion'] = $arrayParametroDet['descripcion'];
                    $arrayItem['strValor']          = $arrayParametroDet['valor1'];

                    $arrayEntidades[] = $arrayItem;
                }//foreach($arrayResultados['registros'] as $arrayDato)
            }//( !empty($arrayResultados['registros']) )
        }//( !empty($strNombreParametroCab) )
            
        $strResponse->setData( array('total' => $intTotal, 'encontrados' => $arrayEntidades) );
        
        return $strResponse;
    }

    /**
     * Documentación para funcion 'gridGrupoDebitosDetAction'.
     * 
     * Función que retorna los detalles de un grupo de debito
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 24-08-2016
     * 
     * @return JsonResponse $jsonResponse
     */     
    public function gridGrupoDebitosDetAction() 
    {
        $jsonResponse            = new JsonResponse();
        $objRequest              = $this->getRequest();
        $emFinanciero            = $this->get('doctrine')->getManager('telconet_financiero');
        $intIdGrupoCab           = $objRequest->get('intIdGrupoCab') ? $objRequest->get('intIdGrupoCab') : 0;
        $objGrupoDebitoCab       = $emFinanciero->getRepository('schemaBundle:AdmiGrupoArchivoDebitoCab')->findOneById($intIdGrupoCab);
        $arrDetalleGrupo         = $emFinanciero->getRepository('schemaBundle:AdmiGrupoArchivoDebitoDet')->findByGrupoDebitoId($intIdGrupoCab);
        $intTotal                = 0;
        $arrayDetalles           = array();
        $intIdBancoTipoCuentaCab = 0;
        $serviceInfoDebitoCab    = $this->get('financiero.InfoDebitoCab');
        
        if( !empty($arrDetalleGrupo) )
        {
            if( !empty($objGrupoDebitoCab) )
            {
                $intIdBancoTipoCuentaCab = $objGrupoDebitoCab->getBancoTipoCuentaId() ? $objGrupoDebitoCab->getBancoTipoCuentaId()->getId() : 0;
                
                if( $intIdBancoTipoCuentaCab )
                {
                    foreach($arrDetalleGrupo as $objAdmiGrupoArchivoDebitoDet)
                    {
                        $objBancoTipoCuentaId = $objAdmiGrupoArchivoDebitoDet->getBancoTipoCuentaId();

                        if( !empty($objBancoTipoCuentaId) )
                        {
                            $objBancoId      = $objBancoTipoCuentaId->getBancoId();
                            $objTipoCuentaId = $objBancoTipoCuentaId->getTipoCuentaId();

                            if( !empty($objBancoId) && !empty($objTipoCuentaId) )
                            {
                                $intTotal++;

                                $strDescripcionDetalle = $objBancoId->getDescripcionBanco()." ".$objTipoCuentaId->getDescripcionCuenta();
                                $strDescripcionDetalle = $serviceInfoDebitoCab->reemplazarPalabrasEnCadena($strDescripcionDetalle, 
                                                                                                           'CADENA_PALABRAS_REEMPLAZAR');

                                $arrayItem                             = array();
                                $arrayItem['intIdGrupoCab']            = $intIdGrupoCab;
                                $arrayItem['intIdBancoTipoCuentaCab']  = $intIdBancoTipoCuentaCab;
                                $arrayItem['intIdGrupoDet']            = $objAdmiGrupoArchivoDebitoDet->getId();
                                $arrayItem['strDescripcion']           = $strDescripcionDetalle;

                                $arrayDetalles[] = $arrayItem;
                            }//( !empty($objBancoId) && !empty($objTipoCuentaId) )
                        }//( !empty($objBancoTipoCuentaId) )
                    }//foreach($arrDetalleGrupo as $objAdmiGrupoArchivoDebitoDet)
                }//( $intIdBancoTipoCuentaCab )
            }//( !empty($objGrupoDebitoCab) )
        }//( !empty($arrDetalleGrupo) )
            
        $jsonResponse->setData( array('total' => $intTotal, 'encontrados' => $arrayDetalles) );
        
        return $jsonResponse;
    }
    
    
    /**
     * Documentación para funcion 'validadorDebitoExistenteAction'.
     * 
     * Función que retorna si ya existe un debito en estado 'Pendiente' que está a la espera de ser procesado con la respuesta emitida por el banco
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 26-08-2016
     * 
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.1 05-12-2017 - Se agrega el parámetro request intCicloId.
     *
     * @return Response $objResponse
     */     
    public function validadorDebitoExistenteAction() 
    {
        setlocale(LC_TIME, "es_ES.UTF-8");
                
        $objResponse           = new Response();
        $objRequest            = $this->getRequest();
        $emFinanciero          = $this->get('doctrine')->getManager('telconet_financiero');
        $intIdEmpresa          = $objRequest->getSession()->get('idEmpresa');
        $strUsuarioSession     = $objRequest->getSession()->get('user');
        $strIpSession          = $objRequest->getClientIp();
        $strDebitos            = $objRequest->get('strDebitos') ? $objRequest->get('strDebitos') : '';
        $strTabActivo          = $objRequest->get('strTabActivo') ? $objRequest->get('strTabActivo') : '';
        $intIdGrupoDebitoCab   = $objRequest->get('intIdGrupoDebitoCab') ? $objRequest->get('intIdGrupoDebitoCab') : 0;
        $intCicloId            = $objRequest->get('intCicloId');
        $strMensaje            = "OK";
        $serviceUtil           = $this->get('schema.Util');
        $strEstado             = "Pendiente";
        $strBancosDebitados    = "";
        $arrayGrupoDebitoDet   = array();
        $arrayDebitoExistente  = array();
        $intContadorBancos     = 0;
        $strHeightDiv          = "auto";
        
        if( !empty($strTabActivo) )
        {
            try
            {
                if( $strTabActivo == "debitosEspeciales" )
                {
                    $arrayBancos[]       = $intIdGrupoDebitoCab;
                    $arrayGrupoDebitoDet = ( !empty($strDebitos) ) ? explode("|", $strDebitos) : array();
                }//( $strTabActivo == "debitosEspeciales" )
                else
                {
                    $arrayBancos = ( !empty($strDebitos) ) ? explode("|", $strDebitos) : array();
                }

                foreach( $arrayBancos as $intBancoTipoCuentaId )
                {
                    if( !empty($arrayGrupoDebitoDet) )
                    {
                        foreach($arrayGrupoDebitoDet as $intGrupoDebitoDet)
                        {
                            $arrayParametro = array('strEstado'            => $strEstado,
                                                    'intGrupoDebitoCab'    => $intBancoTipoCuentaId,
                                                    'intIdEmpresa'         => $intIdEmpresa,
                                                    'intIdGrupoDebitoDet'  => $intGrupoDebitoDet,
                                                    'intCicloId'           => $intCicloId);
                            $arrayResultado = $emFinanciero->getRepository('schemaBundle:InfoDebitoCab')
                                                           ->validadorDebitoCabExistente($arrayParametro);
                            if( !empty($arrayResultado['resultado']) )
                            {
                                $arrayDebitoExistente = array_merge($arrayDebitoExistente, $arrayResultado['resultado']);
                            }//( !empty($arrayResultado['resultado']) )
                        }//foreach($arrayGrupoDebitoDet as $intGrupoDebitoDet)
                    }//( !empty($arrayGrupoDebitoDet) )
                    else
                    {
                        $arrayParametro = array('strEstado'            => $strEstado,
                                                'intGrupoDebitoCab'    => $intBancoTipoCuentaId,
                                                'intIdEmpresa'         => $intIdEmpresa,
                                                'intIdGrupoDebitoDet'  => null,
                                                'intCicloId'           => $intCicloId);
                        $arrayResultado = $emFinanciero->getRepository('schemaBundle:InfoDebitoCab')
                                                       ->validadorDebitoCabExistente($arrayParametro);
                        if( !empty($arrayResultado['resultado']) )
                        {
                            $arrayDebitoExistente = array_merge($arrayDebitoExistente, $arrayResultado['resultado']);
                        }//( !empty($arrayResultado['resultado']) )
                    }//( empty($arrayGrupoDebitoDet) )
                }//foreach( $arrayBancos as $intBancoTipoCuentaId )
                
                if( !empty($arrayDebitoExistente) )
                {
                    foreach($arrayDebitoExistente as $arrayItemObtenido)
                    {
                        $strNombreBancoDebitado   = ( !empty($arrayItemObtenido['debito']) ) ? $arrayItemObtenido['debito'] : '';
                        $datetimeFeCreacionDebito = ( !empty($arrayItemObtenido['feCreacion']) ) 
                                                    ? new \DateTime($arrayItemObtenido['feCreacion']) : '';

                        if( !empty($strNombreBancoDebitado) && !empty($datetimeFeCreacionDebito) )
                        {
                            $intContadorBancos++;
                            
                            $strTmpFeCreacion = strftime("%d",$datetimeFeCreacionDebito->getTimestamp())." de ".
                                                ucfirst(strtolower(strftime("%B",$datetimeFeCreacionDebito->getTimestamp())))." del ".
                                                strftime("%Y",$datetimeFeCreacionDebito->getTimestamp()). " a las ".
                                                $datetimeFeCreacionDebito->format('H:i');

                            $strBancosDebitados .= '* '.$strNombreBancoDebitado.' '.$strTmpFeCreacion.'<br/>';
                        }//( !empty($strNombreBancoDebitado) && !empty($datetimeFeCreacionDebito) )
                        else
                        {
                            $strMensaje = "Error al validar los debitos existentes";
                            throw new \Exception($strMensaje." nombre banco debitado y/o fecha de creación en null");
                        }
                    }//foreach($arrayDebitoExistente as $arrayItemObtenido)
                }//( !empty($arrayDebitoExistente) )

                if( !empty($strBancosDebitados) )
                {
                    if( $intContadorBancos >= 10 )
                    {
                        $strHeightDiv = "300px";
                    }
                    
                    $strMensaje = "<div style='width:100%;height:".$strHeightDiv.";overflow:hidden;overflow-y:scroll'>Los siguientes Bancos/".
                                  "Tarjetas ya han sido debitados anteriormente y aún no tienen respuesta del banco:<br/>".$strBancosDebitados.
                                  "<br/> Desea continuar?</div>";
                }//( !empty($strBancosDebitados) )
            }
            catch(\Exception $e)
            {
                $serviceUtil->insertError('Telcos+', 'validadorDebitoCabExistenteAction', $e->getMessage(), $strUsuarioSession, $strIpSession);
            }
        }//( !empty($strTabActivo) )
        else
        {
            $strMensaje = "No se pudieron procesar los débitos porque no se enviaron los parámetros correspondientes (TAB ACTIVO). Por favor volver".
                          " a intentarlo.";
            $serviceUtil->insertError('Telcos+', 'validadorDebitoCabExistenteAction', $strMensaje, $strUsuarioSession, $strIpSession);
        }//( empty($strTabActivo) )
        
        $objResponse->setContent($strMensaje);
        
        return $objResponse; 
    }
    
    /**
    * @Secure(roles="ROLE_376-1")
    *
    * Documentación para el método 'administrarCargoReproceso'
    * Método que redirecciona a página de administración de parámetros de cargo por reproceso de débito.
    * 
    * @author : Edgar Holguin <eholguin@telconet.ec>
    * @version 1.0 21-10-2016.
    */      
    public function administrarCargoReprocesoAction()
    {       
        return $this->render('financieroBundle:AdmiFormatoDebito:administrarCargoReproceso.html.twig');
    }
    
    /**
     * getListadoParametrosCabAjaxAction, Obtiene los parámetros de reproceso de débito de la estructura ADMI_PARAMETRO_CAB 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.0 13-03-2017
     * 
     * @return json con el total de registros y un array formado con la data obtenida
     *
     * @Secure(roles="ROLE_376-1")
     */
    public function getListadoParametrosCabAjaxAction()
    { 
        $emGeneral                              = $this->getDoctrine()->getManager("telconet_general");
        $arrayParametros                        = array();
        $arrayParametros['strNombreParametro']  = 'CARGO REPROCESO DEBITO';
        $arrayParametros['strModulo']           = 'FINANCIERO';
        $arrayParametros['strProceso']          = 'CARGO REPROCESO DEBITO';
        
        //Obtiene los regsitros de la entidad AdmiParametroCab
        $arrayAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')->findParametrosCab($arrayParametros);
       
        //Valida que no tenga mensaje de error la consulta
        if(empty($arrayAdmiParametroCab['strMensajeError']))
        {
            //Itera el array de los datos obtenidos
            foreach($arrayAdmiParametroCab['arrayResultado'] as $arrayAdmiParamCab):
                $arrayAdmiParametroCabResult[] = array('intIdParametro'     => $arrayAdmiParamCab['intIdParametro'],
                                                       'strNombreParametro' => $arrayAdmiParamCab['strNombreParametro'],
                                                       'strDescripcion'     => $arrayAdmiParamCab['strDescripcion'],
                                                       'strModulo'          => $arrayAdmiParamCab['strModulo'],
                                                       'strProceso'         => $arrayAdmiParamCab['strProceso'],
                                                       'strEstado'          => $arrayAdmiParamCab['strEstado'],
                                                       'strFeCreacion'      => $arrayAdmiParamCab['strFeCreacion']->format('d-M-Y'),
                                                       'strUsrCreacion'     => $arrayAdmiParamCab['strUsrCreacion']);
            endforeach;
        }
        $objResponse = new Response(json_encode(array('jsonAdmiParametroCabResult' => $arrayAdmiParametroCabResult,
                                                      'intTotalParametros'         => $arrayAdmiParametroCab['intTotal'],
                                                      'strMensajeError'            => $arrayAdmiParametroCab['strMensajeError'])));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }//getListadoParametrosCabAjaxAction  
    
    
    /**
     * getListadoParametrosDetAjaxAction, Obtiene los parametros por reproceso de débito de la estructura ADMI_PARAMETRO_DET 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.0 15-03-2017
     * 
     * @return json con el total de registros y un array formado con la data obtenida
     *
     * @Secure(roles="ROLE_376-1")
     */
    public function getListadoParametrosDetAjaxAction()
    {
        $emGeneral                             = $this->getDoctrine()->getManager("telconet_general");
        $objRequest                            = $this->getRequest();
        $arrayParametros                       = array();
        $arrayParametros['strBuscaCabecera']   = "SI";
        $arrayParametros['strNombreParametro'] = 'CARGO REPROCESO DEBITO';
        $arrayParametros['strEmpresaCod']      = $objRequest->getSession()->get('idEmpresa');
        
        $arrayParametrosCab  =  array ('nombreParametro' => $arrayParametros['strNombreParametro']);
        $objAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')->findOneBy($arrayParametrosCab);
        if (is_object($objAdmiParametroCab))
        {
            $arrayParametros['intIdParametroCab'] = $objAdmiParametroCab->getId();
        }

        //Obtiene registros de la entidad AdmiParametroDet
        $arrayAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findParametrosDet($arrayParametros);

        //Valida que no tenga mensaje de error la consulta
        if(empty($arrayAdmiParametroDet['strMensajeError']))
        {
            //Itera el array obtenido en la consulta
            foreach($arrayAdmiParametroDet['arrayResultado'] as $arrayAdmiParamDet):
                $arrayAdmiParametroDetResult[] = array('intIdParametroDet' => $arrayAdmiParamDet['intIdParametroDet'],
                                                       'strDescripcionDet' => $arrayAdmiParamDet['strDescripcionDet'],
                                                       'strValor1'         => $arrayAdmiParamDet['strValor1'],
                                                       'strValor2'         => $arrayAdmiParamDet['strValor2'],
                                                       'strValor3'         => $arrayAdmiParamDet['strValor3'],
                                                       'strValor4'         => $arrayAdmiParamDet['strValor4'],
                                                       'strEstado'         => $arrayAdmiParamDet['strEstado'],
                                                       'strUsrCreacion'    => $arrayAdmiParamDet['strUsrCreacion'],
                                                       'strFeCreacion'     => $arrayAdmiParamDet['strFeCreacion']->format('d-M-Y'));
            endforeach;
        }
        $objResponse = new Response(json_encode(array('jsonAdmiParametroDetResult' => $arrayAdmiParametroDetResult,
                                                      'intTotalParametros'         => $arrayAdmiParametroDet['intTotal'],
                                                      'strMensajeError'            => $arrayAdmiParametroDet['strMensajeError'])));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }//getListadoParametrosDetAjaxAction  
    

    /**
     * activarInactivarCargoReprocesoAjaxAction, Activa e Inactiva el parámetro correspondiente a cargo por reproceso de débito, 
     *                                           además finaliza solicitudes de cargo por reproceso en estado pendiente.
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.0 15-03-2017
     * 
     * @return 
     *
     * @Secure(roles="ROLE_376-1")
     */
    public function activarInactivarCargoReprocesoAjaxAction()
    {
        $objRequest                                 = $this->getRequest();
        $objSession                                 = $objRequest->getSession();
        $objReturnResponse                          = new ReturnResponse();
        $emGeneral                                  = $this->getDoctrine()->getManager("telconet_general");
        $emComercial                                = $this->get('doctrine')->getManager('telconet');
        $emFinanciero                               = $this->getDoctrine()->getManager('telconet_financiero');    
        $intIdParametroCab                          = $objRequest->get('intIdParametro');
        $strEstado                                  = $objRequest->get('strEstado');   
        
        
        $emGeneral->getConnection()->beginTransaction();
        try
        {
            //Valida que $intIdParametroCab haya sido enviado en el request.
            if(!empty($intIdParametroCab))
            {
                $objAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')->find($intIdParametroCab);
                
                if(is_object($objAdmiParametroCab))
                {
                    // Se inactiva cabecera de parámetro cargo por reproceso
                    $objAdmiParametroCab->setUsrUltMod($objSession->get('user'));
                    $objAdmiParametroCab->setFeUltMod(new \DateTime('now'));
                    $objAdmiParametroCab->setEstado($strEstado);
                    $emGeneral->persist($objAdmiParametroCab);
                    $emGeneral->flush();
                    
                    $arrayAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findByParametroId($intIdParametroCab);
                    
                    foreach($arrayAdmiParametroDet as $objAdmiParametroDet)
                    {
                        // Se inactiva detalle de parámetro cargo por reproceso
                        $objAdmiParametroDet->setUsrUltMod($objSession->get('user'));
                        $objAdmiParametroDet->setFeUltMod(new \DateTime('now'));
                        $objAdmiParametroDet->setEstado($strEstado);
                        $emGeneral->persist($objAdmiParametroDet);
                        $emGeneral->flush();                        
                    }
                    
                    // Finalización de solicitudes de cargo por reproceso pendientes
                    if("Inactivo" === $strEstado)
                    {
                        $objTipoSolicitudCargoReproceso = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                                      ->findOneBy(array("descripcionSolicitud" => "SOLICITUD CARGO REPROCESO DEBITO",
                                                                                        "estado"               => "Activo"));
                        $arrayParametros                            = array();
                        $arrayParametros['strEstadoSol']            = "Pendiente";  
                        $arrayParametros['strEstadoSolActualizar']  = "Finalizada";
                        $arrayParametros['strObservacion']          = "Finalizacion de la solicitud";                
                        $arrayParametros['strUsuarioSesion']        = $objSession->get('user');
                        $arrayParametros['strIp']                   = "127.0.0.1";     
                        $arrayParametros['strMsjError']             = "";                         
                        

                        if(is_object($objTipoSolicitudCargoReproceso))
                        { 
                            $arrayParametros['intTipoSolicitudId']      = $objTipoSolicitudCargoReproceso->getId();
                        }
                        
                        $emFinanciero->getRepository('schemaBundle:InfoDebitoDet')->finalizarSolicitudPorTipo($arrayParametros); 
                    }
                    
                    $emGeneral->getConnection()->commit();
                    $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
                    $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);
                }
                else
                {
                    $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                    $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR. ' No se econtró el parámetro en la base.');
                }
            }
            else
            {
                $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR. ' No esta enviando el parámetro.');
            }
        }
        catch(\Exception $ex)
        {
            error_log("AdmiFormatoDebitoController->activarInactivarCargoReprocesoAjaxAction".
                      $objReturnResponse::MSN_ERROR.' '.$ex->getMessage());
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
            $objReturnResponse->setStrMessageStatus('Existio un error al '.$strEstado.' el proceso');
            $emGeneral->getConnection()->rollback();
            $emGeneral->getConnection()->close();
        }
        $objResponse = new Response();
        $objResponse->headers->set('Content-type', 'text/json');
        $objResponse->setContent(json_encode((array) $objReturnResponse));
        return $objResponse;
    }    
    
    
    
    /**
    * Documentación para función 'listDebitosCierreFinalManualAction'.
    * 
    * Función que permite visualizar el twig listDebitosCierreFinalManual en el cual se mostrará detalles de los débitos.
    * 
    * @author Ricardo Robles <rrobles@telconet.ec>
    * @version 1.0 24-02-2019
    * 
    * @return $objResponse - Renderiza una vista.
    */
    public function listDebitosCierreFinalManualAction()
    {
        $emFinaciero           = $this->getDoctrine()->getManager('telconet_financiero');
        $objRequest            = $this->getRequest();
        $strIdEmpresa          = $objRequest->getSession()->get('idEmpresa');
        $strIp                 = $objRequest->getClientIp();
        $strUser               = $objRequest->getSession()->get('user');
        $strPathTelcos         = $this->container->getParameter('path_telcos');
        $strHostScripts        = $this->container->getParameter('host_scripts'); 
        $objSession            = $objRequest->getSession();
        $strPtoClienteSesion   = $objSession->get('ptoCliente');
        $strClienteSesion      = $objSession->get('cliente'); 
        $intDebitoGeneralId    = $objRequest->get("idDebGen");
        $entityDebitoGen       = $emFinaciero->getRepository('schemaBundle:InfoDebitoGeneral')
                                             ->find($intDebitoGeneralId);  
        $entityCabeceras       = $emFinaciero->getRepository('schemaBundle:InfoDebitoCab')
                                             ->findByDebitoGeneralId($intDebitoGeneralId);
        $arrayEstadoCierre     = $emFinaciero->getRepository('schemaBundle:InfoDebitoCab')
                                             ->getValidaEstadoCierre(array('idDebGen'=>$intDebitoGeneralId));  
        $arrayDebitoRepuesta   = "";
        $arrayBancos           = "";  
        $intIndice             = 0;
        $arrayTotales          = "";
        $fltSumaValores        = 0;
        $fltValoresGenerados   = 0;
        $fltValoresAbonados   = 0;
        $fltSumaValoresArchivo = 0;
        $strAplicacion         = 'CIERREDEBITOS';
        
        $strParametros = $strHostScripts . "|" .
                         $strPathTelcos."telcos/app/config/parameters.yml". "|" . 
                         "/home/scripts-telcos/md/financiero/logs/ec.telconet.telcos.cierredebitos/"."|".
                         "DIFERENCIAS-CIERRE-DEBITOS". "|" . 
                         "/home/scripts-telcos/md/financiero/logs/subir-respuesta-debitos-archivos/". "|" .
                         $intDebitoGeneralId. "|" 
                         .$strIp. "|" 
                         .$strIdEmpresa. "|" .
                         $strUser;
                         
        $strComando    = "nohup java -jar -Djava.security.egd=file:/dev/./urandom " . $strPathTelcos .
                         "telcos/src/telconet/financieroBundle/batch/TelcosGestionScripts.jar '" . $strAplicacion . "' '" . $strParametros.
                         "' 'NO' '" . $strHostScripts . "' '" . $strPathTelcos . "'> " . $strPathTelcos .
                         "telcos/src/telconet/financieroBundle/batch/ejecucionApp.txt";
        
        
        if($arrayEstadoCierre[0]['estado']==null)
        {  
            $strTipoMensaje = 'subida';
            $strMensaje     = " El proceso de diferencias de débitos se ha iniciado,le llegará un archivo adjunto si existen diferencias";
            $this->get('session')->getFlashBag()->add($strTipoMensaje, $strMensaje);
            shell_exec($strComando);
        }
       
        foreach($entityCabeceras as $cabecera)
        {
            $entityBancoTipoCuenta = $emFinaciero->getRepository('schemaBundle:AdmiBancoTipoCuenta')
                                                 ->find($cabecera->getBancoTipoCuentaId());
            
            if(strtoupper($entityBancoTipoCuenta->getBancoId()->getDescripcionBanco()) == 'TARJETAS')
            {
                $strBancos = str_replace('TARJETA','',$entityBancoTipoCuenta->getTipoCuentaId()->getDescripcionCuenta());
            }
            else
            {
                $strBancos=str_replace('BANCO','',$entityBancoTipoCuenta->getBancoId()->getDescripcionBanco())." ".
                $entityBancoTipoCuenta->getTipoCuentaId()->getDescripcionCuenta();
            }
            
         
            //OBTENGO EL TOTAL DE REGISTROS QUE TIENEN LA CARACTERÍSTICA GUARDA_REFERENCIA_PARCIAL POR EL DÉBITO GENERAL.        
            $arrayCaracteristica = $emFinaciero->getRepository('schemaBundle:InfoDebitoCab')
                                      ->getObtieneCaracteristica(array('idDebGen'=>$intDebitoGeneralId,
                                                                       'strCaracteristica'=>'GUARDA_REFERENCIA_PARCIAL')); 
            if($arrayCaracteristica[0]['total'] == 1 )
            {  
                $arrayDebitosPendientesAbonados   = $emFinaciero->getRepository('schemaBundle:InfoDebitoCab')
                                                                ->findCountDebitosPendientesAbonados($cabecera->getId(),'Pendiente');          
                $arrayBancos[$intIndice]['pendientesAbonados_valor']   =
                ($arrayDebitosPendientesAbonados[0]['recaudado']) ? $arrayDebitosPendientesAbonados[0]['recaudado'] : 0;
               
                $fltValoresAbonados += $arrayBancos[$intIndice]['pendientesAbonados_valor'];
               
            }
            
            //Arreglo con Pagos procesados
            $arrayDebitosProcesados = $emFinaciero->getRepository('schemaBundle:InfoDebitoCab')
                                                  ->getSumaValorTotalProcesados(array('debitoCabId'=>$cabecera->getId(),'strEstado'=>'Procesado'));
            //Arreglo con Pagos generados
            $arrayDebitosGenerados = $emFinaciero->getRepository('schemaBundle:InfoDebitoCab')
                                                 ->getSumaValorTotalPagosGenerados(array('debitoCabId' => $cabecera->getId(), 
                                                                                         'strEstado'   => 'Procesado'));

            $strUrlExcelDiferencias = $this->generateUrl('admiformatodebito_excelDiferenciaDebitosGenerados',
                                                         array('debitoCabId'=>$cabecera->getId()));
            //Nombres de bancos 
            $arrayBancos[$intIndice]['banco'] = $strBancos;

            //Valores Procesados
            $arrayBancos[$intIndice]['valor_total'] = ($arrayDebitosProcesados[0]['suma']) ? $arrayDebitosProcesados[0]['suma']: 0; 
            $fltSumaValores += $arrayBancos[$intIndice]['valor_total'];
            //Valores Generados
            $arrayBancos[$intIndice]['procesados_valor'] = ($arrayDebitosGenerados[0]['suma']) ? $arrayDebitosGenerados[0]['suma'] : 0;
            $fltValoresGenerados += $arrayBancos[$intIndice]['procesados_valor'];
//          
            //Ruta Excel 
            $arrayBancos[$intIndice]['excel_diferencias'] = $strUrlExcelDiferencias;
            
            $arrayDebitoRepuesta = $emFinaciero->getRepository('schemaBundle:InfoDebitoRespuesta')
                                               ->getUltimaRespuestaDebito(array('intIdDebitoGeneral' => $intDebitoGeneralId));
             
            if($intIndice == 0)
            {
                $arrayBancos[$intIndice]['valor_archivo'] = $arrayDebitoRepuesta[0]['valor_archivo'];
                $fltSumaValoresArchivo                    = $arrayBancos[$intIndice]['valor_archivo'];

            }
            else
            {
                $arrayBancos[$intIndice]['valor_archivo'] = "";
            }
        
            $strUrlExcelDiferenciasExcelRespuesta = $this->generateUrl('admiformatodebito_excelDiferenciaArchivoRespuesta',  
                                                                       array('debitoGeneralId'=>$intDebitoGeneralId)); 
                
             //Ruta Excel Diferencias Archivo Respuestas 
            $arrayBancos[$intIndice]['excel_diferencias_respuesta'] = $strUrlExcelDiferenciasExcelRespuesta;
          
            $intIndice++;
        }    
       
        $arrayTotales[0]['total_generado']  = round($fltSumaValores,2);
        $arrayTotales[0]['total_archivo']   = round($fltSumaValoresArchivo,2);
        $arrayTotales[0]['total_generados'] = round($fltValoresGenerados,2);
        $arrayTotales[0]['total_abonados'] = round($fltValoresAbonados,2);

        return $this->render('financieroBundle:AdmiFormatoDebito:listDebitosCierreFinalManual.html.twig',
                             array('entityDebitoGen' => $entityDebitoGen,
                                   'ptoCliente'      => $strPtoClienteSesion,
                                   'cliente'         => $strClienteSesion,
                                   'bancos'          => $arrayBancos,
                                   'totales'         => $arrayTotales,
                                   'caracteristica'  => $arrayCaracteristica[0]['total'],
                                   'totalRegistros'  => (count($arrayBancos)-1)
                                  )
                            );
    }
    
    /**
    * Documentación para función 'generarCierreFinalManualAction'.
    * Genera el cierre final en la tabla INFO_DEBITO_GENERAL_HISTORIAL con la descripción 'CIERRE FINAL MANUAL'
    * en el campo OBSERVACIÓN y no permitirá subir nuevos archivos de respuestas de débitos en telcos.
    * 
    * @author Ricardo Robles <rrobles@telconet.ec> 
    * @version 1.0 rrobles : 20-03-2019
    * 
    * @return $objResponse - Renderiza una vista
    *
    */     
    public function generarCierreFinalManualAction()
    {  
        $objRespuesta         = new Response();   
        $objRequest           = $this->getRequest();
        $objSession           = $objRequest->getSession();        
        $objEmfn              = $this->getDoctrine()->getManager('telconet_financiero');    
        $objPeticion          = $this->get('request');
        $intDebitoGeneralId   = $objPeticion->get('debitoGeneralId');
        $strObservacionDesc   = $objPeticion->get('observacion_descuadre');
        $strReverso           = $objPeticion->get('reverso');
        $strSecret            = $this->container->getParameter('secret');
        $strTipoMensaje       = "";
        $strMensaje           = "";
        $strMensajeParciales  = "";     
        $strIdEmpresa         = $objRequest->getSession()->get('idEmpresa');
        $strIp                = $objRequest->getClientIp();
        $strUser              = $objRequest->getSession()->get('user');
        $strPathTelcos        = $this->container->getParameter('path_telcos');
        $strHostScripts       = $this->container->getParameter('host_scripts');
        $strAplicacion        = 'CIERREDEBITOS';
        $objEmfn->getConnection()->beginTransaction();
        $objRespuesta->setContent("Error del Form");
        
        try
        {           
            if($strReverso != null && $strReverso > 0 )
            {
                $arrayEstadoCierre      = $objEmfn->getRepository('schemaBundle:InfoDebitoCab')
                                                  ->getValidaEstadoCierre(array('idDebGen'=>$strReverso));       
                $objInfoDebitoRespuesta = $objEmfn->getRepository('schemaBundle:InfoDebitoRespuesta')->find($arrayEstadoCierre[0]['debito']);
                $objInfoDebitoRespuesta->setEstadoCierre(null);
                $objInfoDebitoRespuesta->setValorArchivo(null);
                $objEmfn->flush();
                $objEmfn->getConnection()->commit();
                              
                $strParametros = $strHostScripts . "|" .
                                 $strPathTelcos."telcos/app/config/parameters.yml". "|" . 
                                 "/home/scripts-telcos/md/financiero/logs/ec.telconet.telcos.cierredebitos/"."|".
                                 "DIFERENCIAS-CIERRE-DEBITOS". "|" . 
                                 "/home/scripts-telcos/md/financiero/logs/subir-respuesta-debitos-archivos/". "|" .
                                 $strReverso. "|" 
                                 .$strIp. "|" 
                                 .$strIdEmpresa. "|" .
                                 $strUser;
                         
                $strComando    = "nohup java -jar -Djava.security.egd=file:/dev/./urandom " . $strPathTelcos .
                                 "telcos/src/telconet/financieroBundle/batch/TelcosGestionScripts.jar '" . $strAplicacion . "' '" . $strParametros.
                                 "' 'NO' '" . $strHostScripts . "' '" . $strPathTelcos . "'> " . $strPathTelcos .
                                 "telcos/src/telconet/financieroBundle/batch/ejecucionApp.txt";
               
                $strTipoMensaje = 'reverso';
                $strMensaje     = " El proceso de diferencias de débitos se ha iniciado,le llegará un archivo adjunto si existen diferencias";
             
                shell_exec($strComando);
            }
            else
            {
                $objInfoDebitoGeneral      = $objEmfn->getRepository("schemaBundle:InfoDebitoGeneral")
                                                     ->find($intDebitoGeneralId);
                $arrayTotalDebitoHistorial = $objEmfn->getRepository('schemaBundle:InfoDebitoCab')
                                                     ->getCuentaDebitoHistorialPorParametros(array("idDebGen"=>$intDebitoGeneralId));
                $intTotalDebitoHistorial   = $arrayTotalDebitoHistorial[0]['total'] ? $arrayTotalDebitoHistorial[0]['total'] : 0;
                if($intTotalDebitoHistorial == 0)
                {   
                    $arrayParametros     = array('debitoGenId' => $intDebitoGeneralId,
                                                 'secret'      => $strSecret,
                                                 'limit'       => 50000,
                                                 'estado'      => 'Pendiente'); 
                    $arrayCaracteristica = $objEmfn->getRepository('schemaBundle:InfoDebitoCab')
                                                   ->getObtieneCaracteristica(array('idDebGen'=>$intDebitoGeneralId,
                                                                                    'strCaracteristica'=>'GUARDA_REFERENCIA_PARCIAL'));
                    $arrayDetalleDebitos = $objEmfn->getRepository('schemaBundle:InfoDebitoDet')
                                                   ->findDetallesDebitoPorDebitoGeneral($arrayParametros); 
                    
                    if($arrayCaracteristica[0]['total'] == 1)
                    {          
                        for($intIndice = 0;$intIndice < $arrayDetalleDebitos['total'];$intIndice++)
                        {                 
                            if($arrayDetalleDebitos['registros'][$intIndice]['estado'] == 'Pendiente'
                              && floatval($arrayDetalleDebitos['registros'][$intIndice]['valorDebitado']) > 0)
                            {            
                                $objDebitoDet = $objEmfn->getRepository('schemaBundle:InfoDebitoDet')
                                                        ->find($arrayDetalleDebitos
                                                               ['registros'][$intIndice]['idDebitoDet']);                                   
                                $objDebitoDet->setEstado('Procesado');
                                $objDebitoDet->setFeUltMod(new \DateTime('now'));
                                $objDebitoDet->setUsrUltMod($objSession->get('user'));                             
                            }
                            
                         }
                                $objEmfn->flush();
                                $objEmfn->getConnection()->commit(); 
                         
                        $strMensajeParciales = " Se cambió los débitos pendientes a procesados";
                    }
                  
                    $strTipoMensaje = 'subida';
                    $strMensaje     = "Se generó el cierre final manual con éxito.".$strMensajeParciales;
                    //CREA EL REGISTRO DE CIERRE FINAL MANUAL EN LA INTO_DEBITO_GENERAL_HISTORIAL
                    $objInfoDebitoGeneralHistorial = new InfoDebitoGeneralHistorial();
                    $objInfoDebitoGeneralHistorial->setDebitoGeneralId($objInfoDebitoGeneral); 
                    $objInfoDebitoGeneralHistorial->setContieneRetencionFte('N');
                    $objInfoDebitoGeneralHistorial->setContieneRetencionIva('N');                   
                    $objInfoDebitoGeneralHistorial->setUsrCreacion($objSession->get('user'));
                    $objInfoDebitoGeneralHistorial->setFeCreacion(new \DateTime('now'));
                    $objInfoDebitoGeneralHistorial->setIpCreacion($objRequest->getClientIp());
                    $objInfoDebitoGeneralHistorial->setEstado('Activo');    
                    if($arrayCaracteristica[0]['total'] == 1)
                    {   
                        $arrayParametroCierre = $objEmfn->getRepository('schemaBundle:InfoDebitoCab')
                                                        ->getParametroCierre(array("strDescripcion"=>'Cierre manual por debitos parciales'));
                        
                        $objInfoDebitoGeneralHistorial->setObservacion($arrayParametroCierre[0]['descripcion']);
                        $objInfoDebitoGeneralHistorial->setObservacionDescuadre($strObservacionDesc);
                    }
                    else
                    {
                        $arrayParametroCierre = $objEmfn->getRepository('schemaBundle:InfoDebitoCab')
                                                        ->getParametroCierre(array("strDescripcion"=>'Cierre final manual'));
                        
                        $objInfoDebitoGeneralHistorial->setObservacion($arrayParametroCierre[0]['descripcion']);         
                    }
                    
                    $objEmfn->persist($objInfoDebitoGeneralHistorial);
                    $objEmfn->flush();
                    $objEmfn->getConnection()->commit();
                }
                else
                {
                    $strTipoMensaje = 'subida';
                    $strMensaje     = "Ya existe un Cierre Final Manual.";
                }

            }
              
        }
        catch (\Exception $e)
        {
            if ($objEmfn->getConnection()->isTransactionActive())
            {            
                $objEmfn->getConnection()->rollback();
            }            
            $objEmfn     ->getConnection()->close();
            $objRespuesta->setContent($objRespuesta->getContent().". (".$e->getMessage().")");            
        }
        $this->get('session')->getFlashBag()->add($strTipoMensaje, $strMensaje);
        return $this->redirect($this->generateUrl('respuestadebitos_list_debitos_general', array()));
}

    /**
    * Documentación para función 'getGeneraArchivoDiferenciasAction'.
    * Obtiene el nombre del último archivo de respuesta de débitos cargado,añade la ruta y concatena 
    * al nombre del archivo de subida de respuesta el string '_resultado_diferencias' para mostrarlo cuando 
    * se de clic en el icono de excel de diferencias.
    *
    * @author Ricardo Robles <rrobles@telconet.ec>
    * @version 1.0 26/04/2019
    * 
    */     
    public function getGeneraArchivoDiferenciasAction()
    {     
        $emFinanciero              = $this->getDoctrine()->getManager('telconet_financiero');
        $objRequest                = $this->getRequest();
        $intDebitoGeneralId        = $objRequest->get('debitoGeneralId');
        $strRutaDiferencias        = $this->container->getParameter('ruta_archivo_diferencias');
        $arrayDebitoRepuesta       = $emFinanciero->getRepository('schemaBundle:InfoDebitoRespuesta')
                                                  ->getUltimaRespuestaDebito(array('intIdDebitoGeneral'=>$intDebitoGeneralId));
        $strRutaArchivoDiferencias = $arrayDebitoRepuesta[0]['archivo'];
        $arrayArchivo              = explode(".", $strRutaArchivoDiferencias);
        $strNombreArchivo          = $arrayArchivo[0]."_resultado_diferencias.".$arrayArchivo[1];         
        $strRutaArchivo            = $strRutaDiferencias."respuesta_debitos/";
        $objInputFileType          = PHPExcel_IOFactory::identify($strRutaArchivo.$strNombreArchivo);
        $objReader                 = PHPExcel_IOFactory::createReader($objInputFileType);
        $objPHPExcel               = $objReader->load($strRutaArchivo.$strNombreArchivo);
               
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="reporte_archivo_diferencias_debitos.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;  
    }
    
    /**
     * Documentación para función getValorDetParametrosAction
     * Función que obtiene los valores por detalle parametrizados para los estados de servicios en el flujo 
     * de generación de débitos.
     * 
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 1.0 06-09-2022  
     */
    public function getValorDetParametrosAction()
    {
        $objSession            = $this->getRequest()->getSession();
        $strEmpresaCod         = $objSession->get('idEmpresa');
        $objRespuesta          = new JsonResponse();
        $emGeneral             = $this->getDoctrine()->getManager('telconet_general');
        $strNombreParametroCab = 'PARAM_GENERACION_DEBITOS';
        $strEstadoActivo       = 'Activo';
        $intTotal              = 0;
        $objRequest            = $this->getRequest();
        $strDescripcionParam   = $objRequest->get('strDescParam');

        $objParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                        ->findOneBy(array('nombreParametro' => $strNombreParametroCab, 'estado' => $strEstadoActivo));
        
        if($objParametroCab)
        {
            $intIdParametroCab = $objParametroCab->getId();
        }

        $arrayValorDetParam = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->findBy(array('estado'      => array($strEstadoActivo),
                                                           'parametroId' => $intIdParametroCab,
                                                           'descripcion' => $strDescripcionParam,
                                                           'empresaCod'  => $strEmpresaCod));
        foreach($arrayValorDetParam as $objValorDetParam)
        {
            $arrayValores[] = array('intId' => $objValorDetParam->getId(), 'strValor' => $objValorDetParam->getValor1());
        }

        if($arrayValores)
        {
            $intTotal = count($arrayValores);
        }
        else
        {
            $arrayValores[] = array();
        }

        $objRespuesta->setData(array('intTotal' => $intTotal, 'arrayRegistros' => $arrayValores));
        
        return $objRespuesta;
    }
    
    /**
     * Documentación para función getValorMsjParamDebitoAction
     * Función que obtiene el valor por detalle parametrizado para mostrar mensaje en el flujo 
     * de generación de débitos.
     * 
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 1.0 08-09-2022  
     */
    public function getValorMsjParamDebitoAction()
    {
        $objSession          = $this->getRequest()->getSession();
        $strEmpresaCod       = $objSession->get('idEmpresa');
        $emGeneral           = $this->getDoctrine()->getManager('telconet_general');
        $objRequest          = $this->getRequest();
        $strNombreParamCab   = $objRequest->get('strNombreParamCab');
        $strDescripcionParam = $objRequest->get('strDescripcionParam');
        $strValor            = "";
        $strMsjError         = "";

        try
        {
            $arrayValor = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                    ->getOne($strNombreParamCab,'FINANCIERO','',$strDescripcionParam,'','','','','', $strEmpresaCod);

            $arrayResponse = array('strValor'    => $arrayValor['valor1'],
                                   'strMsjError' => $strMsjError);
        }
        catch(\Exception $e)
        {
            $strValor      = "";
            $strMsjError   = 'Error: No se pudo obtener el valor parametrizado. Consulte con el Administrador del Sistema';
            $arrayResponse = array('strValor'    => $strValor,
                                   'strMsjError' => $strMsjError);
        }

        $objResponse = new Response(json_encode($arrayResponse));
        $objResponse->headers->set('Content-type', 'text/json');
        
        return $objResponse;
    }
    
}
