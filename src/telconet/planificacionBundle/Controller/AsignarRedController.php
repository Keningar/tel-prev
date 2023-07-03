<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\planificacionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\comercialBundle\Service\InfoServicioService;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoServicioProdCaract;
use telconet\schemaBundle\Entity\InfoIp;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolCarac;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\JsonResponse;
use telconet\schemaBundle\Entity\ReturnResponse;


class AsignarRedController extends Controller
{
    /**
	* @Secure(roles="ROLE_189-1")
	*/
    public function indexAction()
    {

	$rolesPermitidos = array();
	if (true === $this->get('security.context')->isGranted('ROLE_139-111'))
		{
	$rolesPermitidos[] = 'ROLE_139-111';
	}
	if (true === $this->get('security.context')->isGranted('ROLE_139-112'))
		{
	$rolesPermitidos[] = 'ROLE_139-112';
	}
	if (true === $this->get('security.context')->isGranted('ROLE_135-94'))
		{
	$rolesPermitidos[] = 'ROLE_135-94';
	}
	if (true === $this->get('security.context')->isGranted('ROLE_135-95'))
		{
	$rolesPermitidos[] = 'ROLE_135-95';
	}

        $em = $this->getDoctrine()->getManager('telconet_general');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("189", "1");

        return $this->render('planificacionBundle:AsignarRed:index.html.twig', array(
             'item' => $entityItemMenu,
             'rolesPermitidos' => $rolesPermitidos
        ));
    }

    public function validarIpAction(){
	$request = $this->getRequest();
	$response = new Response();
	$emComercial = $this->getDoctrine()->getManager();
	$response->headers->set('Content-Type', 'text/plain');

// 	$tipoIp = 'IP '.strtoupper($request->get('tipo'));
// 	$ip = $request->get('ip');
// 	$idServicio = $request->get('idServicio');
//
// 	$productoInternetDedicado = $emComercial->getRepository('schemaBundle:AdmiProducto')->findOneBy(array( "descripcionProducto" => "INTERNET DEDICADO", "estado"=>"Activo"));
// 	$caractTipoIp = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')->findOneBy(array( "descripcionCaracteristica" => $tipoIp, "estado"=>"Activo"));
// 	$prodCaractTipoIp = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')->findOneBy(array( "productoId" => $productoInternetDedicado->getId(),"caracteristicaId" => $caractTipoIp->getId() , "estado"=>"Activo"));
// 	$servProdCaractTipoIp = $emComercial->getRepository('schemaBundle:InfoServicioProdCaract')->findOneBy(array( "valor" => $ip,"productoCaracterisiticaId" => $prodCaractTipoIp->getId() , "estado"=>"Activo"));
//
// 	if($servProdCaractTipoIp){
// 	    $servicio = $emComercial->getRepository('schemaBundle:InfoServicio')->find($servProdCaractTipoIp->getServicioId());
// 	    $login = $servicio->getPuntoId()->getLogin();
// 	    $producto = $servicio->getPlanId()->getNombrePlan();
// 	    $response->setContent('Ip ya asignada al servicio <b>'.$producto.'</b> del login <b>'.$login.'</b>');
// 	}else{
// 	    $servProdCaractTipoIp = $emComercial->getRepository('schemaBundle:InfoServicioProdCaract')->findOneBy(array( "valor" => $ip,"productoCaracterisiticaId" => $prodCaractTipoIp->getId() , "estado"=>"Reservada"));
//
// 	    if($servProdCaractTipoIp){
// 		$servicio = $emComercial->getRepository('schemaBundle:InfoServicio')->find($servProdCaractTipoIp->getServicioId());
// 		$login = $servicio->getPuntoId()->getLogin();
// 		$producto = $servicio->getPlanId()->getNombrePlan();
// 		$response->setContent('Ip ya asignada al servicio <b>'.$producto.'</b> del login <b>'.$login.'</b>');
// 	    }else{
// 		$response->setContent('No existe Ip');
// 	    }
// 	}
	$response->setContent('No existe Ip');
	return $response;
    }

    public function importarRecursosDeRedAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'application/json');
        $peticion = $this->get('request');
        $session = $peticion->getSession();
		$codEmpresa = $session->get('idEmpresa');
        
        $idServicioTrasladado = $peticion->get('idServicioTrasladado');
        $datosImportados = array();

        $datosImportados['caja'] = "";
	$datosImportados['splitter'] = "";
	$datosImportados['intSplitter'] ="";
        
        $em = $this->getDoctrine()->getManager();
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        $infoServicio = $em->getRepository("schemaBundle:InfoServicio")->find($idServicioTrasladado);
        $infoServicioTecnico = $em->getRepository("schemaBundle:InfoServicioTecnico")->findOneByServicioId($idServicioTrasladado);

        $interfaceElementoId = $infoServicioTecnico->getInterfaceElementoId();
	$elementoId = $infoServicioTecnico->getElementoId();

	$elemento = $emInfraestructura->find('schemaBundle:InfoElemento', $elementoId);
	$datosImportados['elemento'] = $elemento->getNombreElemento();

	$interfaceElemento = $emInfraestructura->find('schemaBundle:InfoInterfaceElemento', $interfaceElementoId);
	$datosImportados['interface'] = $interfaceElemento->getNombreInterfaceElemento();

	$TipoMedio = $emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')->find($infoServicioTecnico->getUltimaMillaId());
	if($TipoMedio->getNombreTipoMedio()=="Cobre"){
	    //----pop----//
	    $relacionElemento = $emInfraestructura->getRepository('schemaBundle:InfoRelacionElemento')->findOneBy(array( "elementoIdB" => $elementoId));
	    $popId = $relacionElemento->getElementoIdA();
	    $elementoPop = $emInfraestructura->find('schemaBundle:InfoElemento', $popId);
	    $datosImportados['pop'] = $elementoPop->getNombreElemento();

	    //----vci----//
	    $productoInternetDedicado = $em->getRepository('schemaBundle:AdmiProducto')->findOneBy(array( "empresaCod" => $codEmpresa,"descripcionProducto" => "INTERNET DEDICADO", "estado"=>"Activo"));
	    $entityVci = $em->getRepository('schemaBundle:AdmiCaracteristica')->findOneBy(array( "descripcionCaracteristica" => "VCI", "estado"=>"Activo"));
	    $prodCaractVci = $em->getRepository('schemaBundle:AdmiProductoCaracteristica')->findOneBy(array( "productoId" => $productoInternetDedicado->getId(),"caracteristicaId" => $entityVci->getId() , "estado"=>"Activo"));
	    $servProdCaractVci = $em->getRepository('schemaBundle:InfoServicioProdCaract')->findOneBy(array( "servicioId" => $infoServicio->getId(),"productoCaracterisiticaId" => $prodCaractVci->getId() , "estado"=>"Activo"));
	    if($servProdCaractVci){
		$datosImportados['vci'] = $servProdCaractVci->getValor();
	    }else{
		$datosImportados['vci'] = "Sin Vci";
	    }
	    
        }else if($TipoMedio->getNombreTipoMedio()=="Fibra Optica"){
	    $datosImportados['caja'] =  sprintf("%s",$emInfraestructura->find('schemaBundle:InfoElemento', $infoServicioTecnico->getElementoContenedorId()));
	    $datosImportados['splitter'] = sprintf("%s",$emInfraestructura->find('schemaBundle:InfoElemento', $infoServicioTecnico->getElementoConectorId()));
	    $datosImportados['intSplitter'] = sprintf("%s",$emInfraestructura->find('schemaBundle:InfoInterfaceElemento', $infoServicioTecnico->getInterfaceElementoConectorId()));
	    $datosImportados['pop'] ="";
	    $datosImportados['vci'] ="";

        }else{
	    $datosImportados['pop'] ="";
	    $datosImportados['vci'] ="";
        }
        $respuesta->setContent(json_encode($datosImportados));

        return $respuesta;
    }

    /**
     * 
     * Metodo encargado de consultar los registros para la asignacion de recursos de Red
     * 
     * @since 1.0
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 Se parametriza variables enviadas a consultar
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.2 Se envia a traves de parametros que productos pueden ser visualizados por departamento que gestione
     * 
     * @author Pablo Pin <email@email.com>
     * @version 1.3 - 20-11-2019 - Se agrega el servicio 'comercial.infoservicio' al arreglo de parametros para gestionar GPON.
     * 
     * @author Joel Muñoz <jrmunoz@telconet.com>
     * @version 1.4 - 01-12-2022 - Se agrega funcionalidad para obtener información relacionada con recursos de red y devolverla 
     * cuando sea una migración SDWAN 
     * 
     * @author Joel Muñoz <jrmunoz@telconet.com>
     * @version 1.5 - 21-04-2023 - Se corrije header ya que no tenía sintaxis correcta
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function gridAction()
    {
        $objRespuesta            = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $objRequest              = $this->get('request');
        $objSession              = $objRequest->getSession();
        $strCodEmpresa           = ($objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : "");
        $arrayFechaDesdePlanif   = explode('T',$objRequest->query->get('fechaDesdePlanif'));
        $arrayFechaHastaPlanif   = explode('T',$objRequest->query->get('fechaHastaPlanif'));
        $strLogin                = ($objRequest->query->get('login2') ? $objRequest->query->get('login2') : "");
        $strDescripcionPunto     = ($objRequest->query->get('descripcionPunto') ? $objRequest->query->get('descripcionPunto') : "");
        $strUsrVendedor          = ($objRequest->query->get('vendedor') ? $objRequest->query->get('vendedor') : "");
        $strCiudad               = ($objRequest->query->get('ciudad') ? $objRequest->query->get('ciudad') : "");
        $intNumOrdenServicio     = ($objRequest->query->get('numOrdenServicio') ? $objRequest->query->get('numOrdenServicio') : "");
        $strTipoSolicitud        = ($objRequest->query->get('tipoSolicitud') ? $objRequest->query->get('tipoSolicitud') : "");
        
        $intStart                = $objRequest->query->get('start');
        $intLimit                = $objRequest->query->get('limit');
        $intIdDepartamento       = $objSession->get('idDepartamento');
        
        /* @var $migracion InfoServicioTecnicoService */
        $serviceTecnico          = $this->get('tecnico.InfoServicioTecnico');
        $emInfraestructura       = $this->getDoctrine()->getManager("telconet_infraestructura");
        $emGeneral               = $this->getDoctrine()->getManager("telconet_general");
        $emComercial             = $this->getDoctrine()->getManager("telconet");
        
        $objDepartamento = $emGeneral->getRepository("schemaBundle:AdmiDepartamento")->find($intIdDepartamento);
        $arrayProductosExcepcion = array();
        $arrayProductosEspeciales= array();
        $strRegion               = '';
        
        if(is_object($objDepartamento))
        {
            $arrayInfoVisualizacion   =   $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->get('GESTION PRODUCTOS ESPECIALES POR DEPARTAMENTO', 
                                                          'COMERCIAL', 
                                                          '',
                                                          'RECURSOS DE RED',
                                                          '',
                                                          '',
                                                          $objDepartamento->getNombreDepartamento(),
                                                          '', 
                                                          '', 
                                                          $objSession->get('idEmpresa'));
            
            if(!empty($arrayInfoVisualizacion))
            {
                //Si no es enviado como parametro setea por default la oficina en sesion
                if(empty($strCiudad))
                {
                    $intIdOficina = $objSession->get('idOficina');

                    $objOficina   = $emComercial->getRepository("schemaBundle:InfoOficinaGrupo")->find($intIdOficina);

                    if(is_object($objOficina))
                    {
                        $objCanton = $emComercial->getRepository("schemaBundle:AdmiCanton")->find($objOficina->getCantonId());

                        if(is_object($objCanton))
                        {
                            $strRegion = $objCanton->getProvinciaId()->getRegionId()->getNombreRegion();
                        }
                    }
                }
        
                foreach($arrayInfoVisualizacion as $array)
                {
                    $arrayProductosEspeciales[] = array('producto'       => $array['valor1'],
                                                        'caracteristica' => $array['valor2'],
                                                        'visibilidad'    => $array['valor4']);
                }
            }
        }
        
        $arrayInfoNoVisualizacion   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->get('EXCEPCION DE PRODUCTOS EN FLUJOS NORMALES', 
                                                      'COMERCIAL', 
                                                      '',
                                                      'RECURSOS RED',
                                                      '',
                                                      '',
                                                      '',
                                                      '', 
                                                      '', 
                                                      $objSession->get('idEmpresa'));
        if(!empty($arrayInfoNoVisualizacion))
        {
            foreach($arrayInfoNoVisualizacion as $array)
            {
                $arrayProductosExcepcion[] = $array['valor1'];
            }
        }
        
        $arrayParametros                       = array();
        $arrayParametros['emInfraestructura']  = $emInfraestructura;
        $arrayParametros['intStart']           = $intStart;
        $arrayParametros['intLimit']           = $intLimit;
        $arrayParametros['strFechaDesde']      = $arrayFechaDesdePlanif[0];
        $arrayParametros['strFechaHasta']      = $arrayFechaHastaPlanif[0];
        $arrayParametros['strLogin']           = $strLogin;
        $arrayParametros['intSectorId']        = 0;
        $arrayParametros['strDescripcionPunto'] = $strDescripcionPunto;
        $arrayParametros['strUsrVendedor']     = $strUsrVendedor;
        $arrayParametros['intNumeroOrden']     = $intNumOrdenServicio;
        $arrayParametros['strCiudad']          = $strCiudad;
        $arrayParametros['strCodEmpresa']      = $strCodEmpresa;
        $arrayParametros['strTipoSolicitud']   = $strTipoSolicitud;
        $arrayParametros['serviceTecnico']     = $serviceTecnico;
        $arrayParametros['region']             = $strRegion;
        
        $arrayParametros['arrayDescripcionProducto']          = $arrayProductosEspeciales;
        $arrayParametros['arrayDescripcionProductoExcepcion'] = $arrayProductosExcepcion;

        $arrayParametros['serviceInfoServicio'] = $this->get('comercial.infoservicio');
        
        $objJson = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->generarJsonAsignarRed($arrayParametros);

        $objJsonDecode =  json_decode($objJson);

        if(isset($objJsonDecode->encontrados) 
            && is_array($objJsonDecode->encontrados)
            && count($objJsonDecode->encontrados) > 0
        )
        {
            foreach($objJsonDecode->encontrados as &$objServ)
            {
                if(is_object($objServ) &&  (trim($objServ->nombreTecnico === 'INTERNET SDWAN') || trim($objServ->nombreTecnico === 'L3MPLS SDWAN')))
                {
                    $booleanEsSDWAN =  true;
                    $booleanEsMigracionSDWAN =  false;

                    $objServicio = $emComercial->getRepository('schemaBundle:InfoServicio')->find($objServ->id_servicio);

                    $objCaracteristicasMigracionSDWAN = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                    ->findOneBy(array(
                        'descripcionCaracteristica' => 'Migración de Tecnología SDWAN',
                        'estado'                    => 'Activo'
                    ));

                    $objAdmiProdCaract = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                    ->findOneBy(array('caracteristicaId'=> is_object($objCaracteristicasMigracionSDWAN)?$objCaracteristicasMigracionSDWAN->getId():'',
                                        'productoId'    => is_object($objServicio)? $objServicio->getProductoId()->getId(): '',
                                        'estado'        => 'Activo'));

                    $objInfoServProdCaract = $emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                    ->findOneBy(array('servicioId'                  => is_object($objServicio) ? $objServicio->getId(): '',
                                        'estado'                    => 'Activo',
                                        'productoCaracterisiticaId' => is_object($objAdmiProdCaract) ? $objAdmiProdCaract->getId():''));

                    if(is_object($objInfoServProdCaract) && $objInfoServProdCaract->getValor() === 'S')
                    {
                        $booleanEsMigracionSDWAN =  true;
                    }

                    if($booleanEsSDWAN && $booleanEsMigracionSDWAN)
                    {
                        $objCaracteristicaServicioMigrado = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                        ->findOneBy(array(
                            'descripcionCaracteristica' => 'SERVICIO_MIGRADO_SDWAN',
                            'estado'                    => 'Activo',
                        ));

                        $objAdmiProdCaractServMigrado = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                        ->findOneBy(array('caracteristicaId'=> is_object($objCaracteristicaServicioMigrado)?
                                                                        $objCaracteristicaServicioMigrado->getId(): '',
                                          'productoId'      => is_object($objServicio)?$objServicio->getProductoId()->getId() : '',
                                          'estado'          => 'Activo'));

                        $objInfoServProdCaractServicioMigrado = $emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                        ->findOneBy(array('servicioId'              => is_object($objServicio) ? $objServicio->getId(): '',
                                         'estado'                   => 'Activo',
                                         'productoCaracterisiticaId'=>is_object($objAdmiProdCaractServMigrado) ?
                                                                         $objAdmiProdCaractServMigrado->getId():''));

                        if(is_object($objInfoServProdCaractServicioMigrado) 
                        && intval($objInfoServProdCaractServicioMigrado->getValor())>0)
                        {
                            $objInfoServicioPrincipal = $emComercial->getRepository('schemaBundle:InfoServicio')
                            ->find($objInfoServProdCaractServicioMigrado->getValor());

                            $objAdmiProducto = null;

                            if(is_object($objInfoServicioPrincipal) 
                            && is_object($objInfoServicioPrincipal->getProductoId()))
                            {
                                $objAdmiProducto = $objInfoServicioPrincipal->getProductoId();
                            }

                            $objIp = $emInfraestructura->getRepository('schemaBundle:InfoIp')
                            ->findOneBy(
                                array( "servicioId" => $objInfoServProdCaractServicioMigrado->getValor(),
                                       'estado'     => 'Activo'),
                                array('tipoIp'      => 'DESC')
                            ); 
    
                            $objSubred = $emInfraestructura->getRepository('schemaBundle:InfoSubred')
                                                                    ->find(is_object($objIp) ?$objIp->getSubredId():'');

                            $objCaracteristicaVRF = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                            ->findOneBy(array(
                                    'descripcionCaracteristica' => 'VRF',
                                    'estado'                    => 'Activo'
                                ));
            
                            $objAdmiProdCaractVRF = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                            ->findOneBy(array('caracteristicaId'=> is_object($objCaracteristicaVRF) ? $objCaracteristicaVRF->getId() : '',
                                              'productoId'      => is_object($objAdmiProducto) ? $objAdmiProducto->getId() : '',
                                              'estado'          => 'Activo'
                                            )
                                    );
            
                            $objInfoServProdCaractVRF = $emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                            ->findOneBy(array('servicioId'               =>$objInfoServProdCaractServicioMigrado->getValor(),
                                              'estado'                   => 'Activo',
                                              'productoCaracterisiticaId'=>is_object($objAdmiProdCaractVRF)?$objAdmiProdCaractVRF->getId():''));
        
                            $objVrfSDWAN = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                            ->find(is_object($objInfoServProdCaractVRF)?$objInfoServProdCaractVRF->getValor():'');

                            $objServ->InfoMigracionSDWAN = array(
                                'idServicioPrincipal'=> intval($objInfoServProdCaractServicioMigrado->getValor()),
                                'EsSDWAN'            => $booleanEsSDWAN,
                                'EsMigracionSDWAN'   => $booleanEsMigracionSDWAN,
                                'objIp'              =>  array(
                                    'ip'             => is_object($objIp)?$objIp->getIp():'',
                                    'tipoIp'         => is_object($objIp)?$objIp->getTipoIp():'',
                                    'subredId'       => is_object($objIp)?$objIp->getSubredId():'',
                                    'subred'         => is_object($objSubred)?$objSubred->getSubred():'',
                                    'vrf'            => is_object($objVrfSDWAN)?$objVrfSDWAN->getValor():'',
                                    'vrfId'          => is_object($objVrfSDWAN)?$objVrfSDWAN->getId():'',
                                )
                            );
                                                      

                        }

               
                    }

                }
            }

            $objJson = json_encode($objJsonDecode);
        }
        
        $objRespuesta->setContent($objJson);

        return $objRespuesta;
    }

    public function ajaxGetJsonInterfacesByElementoAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $peticion = $this->get('request');
        $idElemento = $peticion->get('idElemento');
        $interfaceSplitter = ($peticion->get('interfaceSplitter'))?$peticion->get('interfaceSplitter'):"";
        $estado = ($peticion->get('estado'))?$peticion->get('estado'):"not connect";
        
        if($idElemento){
            
                $objJson = $this->getDoctrine()
                    ->getManager("telconet_infraestructura")
                    ->getRepository('schemaBundle:InfoInterfaceElemento')
                    ->generarJsonInterfacesLibres($idElemento, $interfaceSplitter,$estado);

                $respuesta->setContent($objJson);
            
        }else{
	  $respuesta->setContent("No hay idElemento");
        }
        return $respuesta;
    }
    
    public function ajaxGetDetallePlanAction()
    {
        $request = $this->getRequest();
        $idPlan = $request->request->get("planId");
        $em = $this->getDoctrine()->getManager();
        $arreglo = array();
        $detallesPlan = $em->getRepository('schemaBundle:InfoPlanDet')->findByPlanId($idPlan);
        
        foreach($detallesPlan as $detallesPlan){
			$arreglo[] = array(
                                    "nombreProducto" => sprintf("%s",$em->getRepository('schemaBundle:AdmiProducto')->find($detallesPlan->getProductoId())),
                                    "cantidad" => $detallesPlan->getCantidadDetalle()
                    );
        }
        
        if ($arreglo)
        {
            $response = new Response(json_encode(array('msg' => 'ok', 'listado' => $arreglo)));
        }
        else
        {
            $response = new Response(json_encode(array('msg' => 'No existen datos')));
        }
        $response->headers->set('Content-type', 'text/json');		
        return $response;
    }

     /**
     * Documentación para el método 'trasladarRecursosDeRed'.
     *
     * Traslada la información de recursos de red de un servicio
     * @return response.
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 22-06-2015
     * @version 1.2 05-08-2015
     */
    public function trasladarRecursosDeRedAction()
    {
        $respuesta          = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $peticion           = $this->get('request');
        $session            = $peticion->getSession();
        $idEmpresa          = $peticion->getSession()->get('idEmpresa');
        $emComercial        = $this->getDoctrine()->getManager();
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        //se agrega variable para almacenar rastro de ip asignada
        $strListaIps        = '';

        $idDetalleSolicitud     = $peticion->get('idDetalleSolicitud');
        $idServicio             = $peticion->get('idServicio');
        $idServicioTrasladado   = $peticion->get('idServicioTrasladado');
        $nombreTecnico          = $peticion->get('nombreTecnico');

        $detalleSolicitud               = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->find($idDetalleSolicitud);
        $infoServicio                   = $emComercial->getRepository('schemaBundle:InfoServicio')->find($idServicio);
        $infoServicioTecnico            = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')->findOneByServicioId($idServicio);
        $infoServicioTrasladado         = $emComercial->getRepository('schemaBundle:InfoServicio')->find($idServicioTrasladado);
        $infoServicioTecnicoTrasladado  = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')->findOneByServicioId($idServicioTrasladado);

        $emComercial->getConnection()->beginTransaction();
        $emInfraestructura->getConnection()->beginTransaction();

        try
        {
            if($nombreTecnico == "IP")
            {   
                $perfil = $emComercial->getRepository("schemaBundle:InfoPlanCab")
                                      ->getPerfilByPlanIdAndPuntoId("no", null, $infoServicio->getPuntoId()->getId());
                if(strpos($perfil, 'Error') !== false)
                {
                    $respuesta->setContent($perfil);
                    return $respuesta;
                }
            }
            
            //se agrega codigo para liberar puerto de splitter asignado previamente
            if ($infoServicioTecnico->getInterfaceElementoConectorId())
            {
                if ( $infoServicioTecnico->getInterfaceElementoConectorId() != $infoServicioTecnicoTrasladado->getInterfaceElementoConectorId() )
                {
                    $entityInterfaceSplitter = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                 ->find($infoServicioTecnico->getInterfaceElementoConectorId());
                    $entityInterfaceSplitter->setEstado("not connect");
                    $emInfraestructura->persist($entityInterfaceSplitter);
                    $emInfraestructura->flush();              
                }              
            }
            
            $infoServicioTecnico->setElementoId($infoServicioTecnicoTrasladado->getElementoId());
            $infoServicioTecnico->setInterfaceElementoId($infoServicioTecnicoTrasladado->getInterfaceElementoId());
            $infoServicioTecnico->setElementoContenedorId($infoServicioTecnicoTrasladado->getElementoContenedorId());
            $infoServicioTecnico->setElementoConectorId($infoServicioTecnicoTrasladado->getElementoConectorId());
            $infoServicioTecnico->setInterfaceElementoConectorId($infoServicioTecnicoTrasladado->getInterfaceElementoConectorId());
            $emComercial->persist($infoServicioTecnico);
            $emComercial->flush();

            //copiar todas las caracteristicas del servicio trasladado
            $servicioProdCaracts = $emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                               ->findBy(array("servicioId" => $idServicioTrasladado, "estado" => 'Activo'));
            
            $producto = $emComercial->getRepository('schemaBundle:AdmiProducto')->findOneBy(array( "descripcionProducto" => "INTERNET DEDICADO",
                                                                                                   "empresaCod" => $idEmpresa));
            $carac = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                       ->findOneBy(array( "descripcionCaracteristica" => 'TRASLADO', "estado" => "Activo"));
            $prodCarac = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                           ->findOneBy(array( "productoId" => $producto->getId(),
                                                              "caracteristicaId"=>$carac->getId(), 
                                                              "estado" => "Activo"));
        
            foreach($servicioProdCaracts as $servicioProdCaract)
            {
                if ($prodCarac->getId()!= $servicioProdCaract->getProductoCaracterisiticaId())
                {
                    $servicioProdCaractCopy = new InfoServicioProdCaract();
                    $servicioProdCaractCopy = clone $servicioProdCaract;
                    $servicioProdCaractCopy->setServicioId($idServicio);
                    $servicioProdCaractCopy->setFeCreacion(new \DateTime('now'));
                    $servicioProdCaractCopy->setUsrCreacion($session->get('user'));

                    $emComercial->persist($servicioProdCaractCopy);
                    $emComercial->flush();
                }
            }
            //copiar todas las Ips
            $infoIps = $emInfraestructura->getRepository('schemaBundle:InfoIp')->findBy(array("servicioId" => $idServicioTrasladado, 
                                                                                              "estado" => 'Activo'));
            //se inicializa variable utilizada para llevar rastro de la ip asignada por el sistema Telco
            $strListaIps = '';
            foreach($infoIps as $infoIp)
            {
                $infoIpCopy = new InfoIp();
                $infoIpCopy = clone $infoIp;
                $infoIpCopy->setServicioId($idServicio);
                $infoIpCopy->setFeCreacion(new \DateTime('now'));
                $infoIpCopy->setUsrCreacion($peticion->getSession()->get('user'));
                $infoIpCopy->setIpCreacion($peticion->getClientIp());
                //se agrega codigo para llevar rastro de la ip asignada por el sistema Telcos
                $strListaIps = $strListaIps . ' ' . $infoIp->getIp();
                $emInfraestructura->persist($infoIpCopy);
                $emInfraestructura->flush();
                
                $infoIp->setEstado('Eliminado');
                $emInfraestructura->persist($infoIp);
                $emInfraestructura->flush();                
            }
            //se agrega codigo para llevar rastro de la ip asignada por el sistema Telcos
            $strListaIps = ($strListaIps ? ' Se clonaron las ips: ' . $strListaIps : "");
            //actualizo estados
            $detalleSolicitud->setEstado("Asignada");
            $emComercial->persist($detalleSolicitud);
            $emComercial->flush();

            //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
            $lastDetalleSolhist = $emComercial->getRepository('schemaBundle:InfoDetalleSolHist')
                                              ->findOneDetalleSolicitudHistorial($idDetalleSolicitud, 'Planificada');

            $entityDetalleSolHist = new InfoDetalleSolHist();
            $entityDetalleSolHist->setDetalleSolicitudId($detalleSolicitud);
            if($lastDetalleSolhist)
            {
                $entityDetalleSolHist->setFeIniPlan($lastDetalleSolhist->getFeIniPlan());
                $entityDetalleSolHist->setFeFinPlan($lastDetalleSolhist->getFeFinPlan());
                //se agrega codigo para llevar rastro de la ip asignada por el sistema Telcos
                $entityDetalleSolHist->setObservacion($lastDetalleSolhist->getObservacion() . $strListaIps);
            }
            $entityDetalleSolHist->setIpCreacion($peticion->getClientIp());
            $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
            $entityDetalleSolHist->setUsrCreacion($session->get('user'));
            $entityDetalleSolHist->setEstado('Asignada');

            $emComercial->persist($entityDetalleSolHist);
            $emComercial->flush();

            //SE ACTUALIZA EL ESTADO DEL SERVICIO
            $infoServicio->setEstado("Asignada");
            $emComercial->persist($infoServicio);
            $emComercial->flush();

            //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
            $entityServicioHist = new InfoServicioHistorial();
            $entityServicioHist->setServicioId($infoServicio);
            $entityServicioHist->setIpCreacion($peticion->getClientIp());
            $entityServicioHist->setFeCreacion(new \DateTime('now'));
            $entityServicioHist->setUsrCreacion($session->get('user'));
            $entityServicioHist->setEstado('Asignada');
            //se agrega codigo para llevar rastro de la ip asignada por el sistema Telcos
            $entityServicioHist->setObservacion('Se utilizaron los mismos recursos de red del servicio trasladado,' . $strListaIps);

            $emComercial->persist($entityServicioHist);
            $emComercial->flush();

            if($nombreTecnico == "IP")
            {
                
                //SE ACTUALIZA EL ESTADO DEL SERVICIO
                $infoServicio->setEstado("Activo");
                $emComercial->persist($infoServicio);
                
                //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                $entityServicioHist = new InfoServicioHistorial();
                $entityServicioHist->setServicioId($infoServicio);
                $entityServicioHist->setIpCreacion($peticion->getClientIp());
                $entityServicioHist->setFeCreacion(new \DateTime('now'));
                $entityServicioHist->setUsrCreacion($session->get('user'));
                $entityServicioHist->setEstado('Activo');
                //se agrega codigo para llevar rastro de la ip asignada por el sistema Telcos
                $entityServicioHist->setObservacion('Se utilizaron los mismos recursos de red del servicio trasladado, al ser un ' .
                                                    'servicio de IP paso a estado Activo directamente ' .
                                                    $strListaIps);
                $emComercial->persist($entityServicioHist);
                
                //SE ACTUALIZA EL ESTADO DEL SERVICIO
                $infoServicioTrasladado->setEstado("Trasladado");
                $emComercial->persist($infoServicioTrasladado);
                
                //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                $entityServicioHistAnt = new InfoServicioHistorial();
                $entityServicioHistAnt->setServicioId($infoServicioTrasladado);
                $entityServicioHistAnt->setIpCreacion($peticion->getClientIp());
                $entityServicioHistAnt->setFeCreacion(new \DateTime('now'));
                $entityServicioHistAnt->setUsrCreacion($session->get('user'));
                $entityServicioHistAnt->setEstado('Trasladado');
                //se agrega codigo para llevar rastro de la ip asignada por el sistema Telcos
                $entityServicioHistAnt->setObservacion('Se traslado el servicio');
                $emComercial->persist($entityServicioHistAnt);
                
                $emComercial->flush();
            }

            $emComercial->getConnection()->commit();
            $emInfraestructura->getConnection()->commit();
            $respuesta->setContent("Se trasladaron correctamente los Recursos de Red");
        }
        catch(\Exception $e)
        {
            $emComercial->getConnection()->rollback();
            $emInfraestructura->getConnection()->rollback();

            $mensajeError = "Error: " . $e->getMessage();
            error_log($mensajeError);
            $respuesta->setContent($mensajeError);
        }

        return $respuesta;
    }

    /**
     * Documentación para el método 'guardaRecursosDeRed'.
     *
     * Guarda la información de recursos de red de un servicio
     * @return response.
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @author Francisco Adum <fadum@telconet.ec>
     * @since 1.0 14-11-2014
     * @version 1.1 3-08-2015
     */
    public function guardaRecursosDeRedAction()
    {
        $respuesta              = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $peticion               = $this->get('request');

        /* @var $recursoRedService RecursosDeRedService */
        $recursoRedService = $this->get('planificacion.RecursosDeRed');
        
        $arrayPeticiones = array(
                                    'id'                    => $peticion->get('id'),
                                    'producto'              => $peticion->get('producto'),
                                    'nombreTecnico'         => $peticion->get('nombreTecnico'),
                                    'elementoId'            => $peticion->get('elemento_id'),
                                    'interfaceId'           => $peticion->get('interface_id'),
                                    'vci'                   => $peticion->get('vci'),
                                    'datosIps'              => $peticion->get('datosIps'),
                                    'tipoSolicitud'         => $peticion->get('tipoSolicitud'),
                                    'idSplitter'            => $peticion->get('splitter_id'),
                                    'idSplitterHuawei'      => $peticion->get('id_splitter'),
                                    'idOlt'                 => $peticion->get('id_olt'),
                                    'idInterfaceOlt'        => $peticion->get('id_interface_olt'),
                                    'interfaceSplitterId'   => $peticion->get('interface_splitter_id'),
                                    'marcaOlt'              => $peticion->get('marcaOlt'),
                                    'idEmpresa'             => $peticion->getSession()->get('idEmpresa'),
                                    'prefijoEmpresa'        => $peticion->getSession()->get('prefijoEmpresa'),
                                    'cantidadRegistrosIps'  => $peticion->get('cantidadRegistrosIps'),
                                    'esPlan'                => $peticion->get('esPlan'),
                                    'usrCreacion'           => $peticion->getSession()->get('user'),
                                    'ipCreacion'            => $peticion->getClientIp()
                                );
        
        
        $respuestaArray = $recursoRedService->asignarRecursosRed($arrayPeticiones);
        $mensaje        = $respuestaArray['mensaje'];
        
        $respuesta->setContent($mensaje);

        return $respuesta;
    }
    
    /**
     * Funcion ajax que sirve para asignar los recursos de red para el producto
     * Wifi
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 18-12-2015
     */
    public function ajaxAsignarRecursosRedWifiAction()
    {
        $respuesta = new Response();
        
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        $session  = $peticion->getSession();
        
        $arrayParametros = array(
                                    'idServicio'         => $peticion->get('servicioId'),
                                    'idDetalleSolicitud' => $peticion->get('idSolicitud'),
                                    'interfaceConectorId'=> $peticion->get('interfaceConectorId'),
                                    'empresaCod'         => $session->get('idEmpresa'),
                                    'usrCreacion'        => $session->get('user'),
                                    'ipCreacion'         => $peticion->getClientIp()
                                );
        
        /* @var $serviceRecursoRed RecursosDeRedService */
        $serviceRecursoRed = $this->get('planificacion.RecursosDeRed');
        
        $respuestaArray = $serviceRecursoRed->asignarRecursosWifi($arrayParametros);
        $mensaje        = $respuestaArray['mensaje'];
        
        $respuesta->setContent($mensaje);

        return $respuesta;
    }   

    /**
     * Obtienen y asigna IPs dependiendo del servicio solicitado (plan, perfil, olt)
     * @param type $nro
     * @param type $olt
     * @param type $plan
     * @param type $perfil
     * @return \Symfony\Component\HttpFoundation\Response json
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.2 25-11-2015 Se agrega validacion de tipo de aprovisionamiento para reserva de Ips
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.3 25-04-2016 Se corrige validacion de mensaje de error en asignacion de ips en MIGRACIONES
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.4 04-05-2016 Se agrega parametro en metodo que recupera Ips de pool para asignacion de recursos
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.5 29-07-2018 Se agrega filtro para procesar servicios con tecnología ZTE
     * @since 1.4
     */
    public function getIpsAction($nro, $id_elemento, $id_servicio, $id_punto, $esPlan, $id_plan, $marca_elemento) {

        /* @var $serviceInfoServicio \telconet\comercialBundle\Service\InfoServicioService */
        $RecursosDeRedService     = $this->get('planificacion.RecursosDeRed');
        $em_inf                   = $this->getDoctrine()->getManager("telconet_infraestructura");
        $em                       = $this->getDoctrine()->getManager("telconet");
        $respuesta                = new Response();
        $arrayResponse            = array();
        $strTipoAprovisionamiento = "";
        $respuesta->headers->set('Content-Type', 'text/json');
        $peticion                 = $this->get('request');
        $codEmpresa               = ($peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa') : "");
        try
        {
            /* @var $migracion InfoServicioTecnicoService */
            $tecnicoService = $this->get('tecnico.InfoServicioTecnico');

            $producto = $em->getRepository('schemaBundle:AdmiProducto')
                           ->findOneBy(array("descripcionProducto" => "INTERNET DEDICADO",
                                             "estado"              => "Activo",
                                             "empresaCod"          => $codEmpresa));

            $servicio = $em->getRepository('schemaBundle:InfoServicio')->find($id_servicio);

            //CARACTERISTICA TRASLADO
            $traslado        = $tecnicoService->getServicioProductoCaracteristica($servicio, "TRASLADO", $producto);
            $servicioTecnico = $em->getRepository('schemaBundle:InfoServicioTecnico')->findOneByServicioId($id_servicio);
            if($traslado)
            {
                $servicioAnteriorId = $traslado->getValor();
                
                $servicioTecnicoAnt = $em->getRepository('schemaBundle:InfoServicioTecnico')->findOneByServicioId($servicioAnteriorId);
                if($servicioTecnico->getElementoId() == $servicioTecnicoAnt->getElementoId())
                {
                    //copiar la Ip
                    $infoIp = $em_inf->getRepository('schemaBundle:InfoIp')->findOneBy(array("servicioId" => $servicioAnteriorId, 
                                                                                             "estado" => 'Activo'));
                    if ($infoIp)
                    {
                        //buscar scopes
                        $arrayScopeOlt = $em_inf->getRepository('schemaBundle:InfoSubred')
                                             ->getScopePorIpFija($infoIp->getIp(), $servicioTecnico->getElementoId());
                        if($arrayScopeOlt)
                        {
                            $scope = $arrayScopeOlt['NOMBRE_SCOPE'];
                        }
                        else
                        {
                            $scope = "";
                        }
                        $arrayResponse              = array();
                        $arrayResponse['ips']       = array();
                        $arrayResponse['error']     = null;
                        $arrayResponse['faltantes'] = 0;
                        $arrayResponse['elemento']  = $servicioTecnico->getElementoId();
                        $arrayIp                    = array();
                        $arrayIp['ip']              = $infoIp->getIp();
                        $arrayIp['tipo']            = 'FIJA';
                        $arrayIp['scope']           = $scope;
                        $arrayResponse['ips'][]     = $arrayIp;
                    }

                    $respuesta->setContent(json_encode($arrayResponse));
                    return $respuesta;
                }
            }
            //se valida marca del elemento,en caso de venir nula se recupera la marca del elemento del servicio de internet
            if ($marca_elemento == null || $marca_elemento == "" || $marca_elemento == 'null')
            {
                $id_elemento = $em->getRepository("schemaBundle:InfoElemento")->getElementoParaPerfil($id_servicio, $esPlan, $id_punto);
                if(strpos($id_elemento, 'Error') !== false)
                {
                    $arrayResponse['error'] = $id_elemento;
                    $respuesta->setContent(json_encode($arrayResponse));
                    return $respuesta;
                }
                else
                {
                    $entityElementoOlt = $em_inf->getRepository("schemaBundle:InfoElemento")->find($id_elemento);
                    $marca_elemento    = $entityElementoOlt->getModeloElementoId()->getMarcaElementoId()->getNombreMarcaElemento();
                }
            }
            if ($marca_elemento == 'TELLION')
            {
                $strTipoAprovisionamiento = $RecursosDeRedService->geTipoAprovisionamiento($id_elemento);
                if ($strTipoAprovisionamiento == 'POOL')
                {
                    $content = $RecursosDeRedService->getIpsDisponiblePoolOlt($nro, $id_elemento, $id_servicio, $id_punto, $esPlan, $id_plan, "SI");
                }
                else
                {
                    $content = $RecursosDeRedService->getIpsDisponibleScopeOlt($nro, $id_elemento, $id_servicio, $id_punto, $esPlan, $id_plan);
                }
            }
            else if ($marca_elemento == 'HUAWEI' || $marca_elemento == 'ZTE')
            {
                $content = $RecursosDeRedService->getIpsDisponibleScopeOlt($nro, $id_elemento, $id_servicio, $id_punto, $esPlan, $id_plan);
            }
            else
            {
                $content = $em_inf->getRepository('schemaBundle:InfoIp')->getIpsReservadasOlt($id_servicio);
                if (isset($content['error']))
                {
                    $strTipoAprovisionamiento = $RecursosDeRedService->geTipoAprovisionamiento($servicioTecnico->getElementoId());
                    if (strpos($content['error'],'No existen Ips Reservadas para este servicio') !== false && 
                        $strTipoAprovisionamiento == "CNR")
                    {
                        $content = $em_inf->getRepository('schemaBundle:InfoIp')->getIpsReservadasOlt($id_servicio, "Activo");
                        
                    }
                    else
                    {
                        $content = $RecursosDeRedService->getIpsDisponibleScopeOlt( $nro, 
                                                                                    $id_elemento, 
                                                                                    $id_servicio, 
                                                                                    $id_punto, 
                                                                                    $esPlan, 
                                                                                    $id_plan );
                    }
                }
                
            }
        }
        catch (\Exception $e)
        {
            $content = 'Error: <br>' . $e->getMessage();
        }
        
        $respuesta->setContent(json_encode($content));

        return $respuesta;
    }
    
    /********************************************************************/
    /********************FUNCIONES EMPRESA TN****************************/
    /********************************************************************/
    
    /**
     * Funcion ajax que sirve para obtener los datos tecnicos
     * del servicio y pre-cargar los datos a escoger
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 16-12-2015
     * 
     * @author Kenneth Jimenez <kjimenez@telconet.ec>    
     * @version 2.0 28-03-2016
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>  
     * @version 2.1 23-05-2016   Se agregan filtro para soportar servicios UM Radio
     * 
     * @author Eduardo Plua <eplua@telconet.ec>
     * @version 2.2 26-05-2016 - Se recupera elementoPe desde ws networking
     *
     * @author Jesus Bozada <jbozada@telconet.ec> 
     * @version 2.3 28-06-2016 - Se ajusta que para poder obtener datos de backbone para cambios de ultima milla TN
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 2.4 22-07-2016 - Se obtiene el tipo de ultima milla requerido para determinar la factibilidad
     * 
     * @author Jesus Bozada <jbozada@telconet.ec> 
     * @version 2.5 24-08-2016 - Se agregan validaciones para poder generar cambios de ultima milla Radio TN
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 2.6
     * @since 08-10-2016    Se realiza cambio para que siempre que sean servicios migrados y no tengan caracteristicas de factibilidad sean 
     *                      tratados como RUTA
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 2.7
     * @since 19-12-2016    Se añade escenario que soporte servicios Fibra Optica sin data de GIS para asignarle como Factibilidad DIRECTO
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 2.8
     * @since 27-01-2017   Se realiza cambio generar datos de factibilidad para servicios que dependan de un Pseudo Pe administrado por Cliente
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 2.9
     * @since 01-02-2017    Se realiza cambio indicando que cuando se trate de Servicio Concentrador BACKUP este obtenga la informacion
     *                      de capacidad de su Concentrador PRINCIPAL con el que se relaciona
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 2.9
     * @since 21-02-2017   Se corrige codigo repetido que afecta la consulta de los datos de nueva factibilidad
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 3.0
     * @since 09-06-2017   Se envia informacion de anillo y vlan para los productos que tenga UM Satelital
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 3.1
     * @since 20-06-2017   Se envia informacion de referencia de servicios VSAT creados por cliente
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 3.2
     * @since 19-09-2017   Se envia bandera que indica si el flujo a consultar info de BB es de DATACENTER o no
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 3.3
     * @since 09-03-2018  Se adapta metodo para que soporte flujos de multisolucion ( NxN ) para DC
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 3.4
     * @since 17-07-2018  Se enviá nuevo parámetro al método getPeBySwitch
     *
     * @author David Leon <mdleon@telconet.ec>
     * @version 3.5
     * @since 01-07-2019  Se valida el producto Internet Sdwan realice el flujo de Internet Dedicado.
     *
     * @author David Leon <mdleon@telconet.ec>
     * @version 3.6
     * @since 05-08-2019  Se inserta en la validación de búsqueda, que para el producto L3MPLS Sdwan sea por VLANS DATOS.
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 3.7
     * @since 30-08-2019  Por proyecto segmentacion de VLAN se agrega un parametro a la llamada de la funcion: getInfoBackboneByElemento
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 3.8 - 14-07-2020 - Se obtiene la data de los servicios de una solución DC, en base a las nuevas estructuras.
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 3.9
     * @version 4.12 13-09-2019 - Se agrega la variable: 'tipoRed', la cual va permitir identificar con que tipo de red se va crear el servicio:
     *                            MPLS o GPON
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 4.0 20-11-2019 - Se agrega logica para poder soportar los servicios GPON-TN.
     * 
     * @author Jonathan Montecé <jmontece@telconet.ec>
     * @version 5.0 12-08-2021 - Se agrega logica para validar anillo en provincias y presentar pantalla de gye-uio.
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 5.1 01-08-2022 - Se agrega lógica para soportar los servicios de Cámara VPN GPON.
     * 
     * @author Josue Valencia <ajvalencia@telconet.ec>
     * @version 5.2 28-11-2022 - Se agrega lógica para soportar los servicios de Clear Channel Punto a Punto.
     * 
     * @author Joel MUñoz <jrmunoz@telconet.ec>
     * @version 5.3 01-12-2022 - Se agrega funcionalidad para devolver data para ejecutar <<Asignacion de Recursos>> en SDWAN
     * 
     * @param $peticion ['idServicio']
     * @return $respuesta Response
     */
    public function ajaxGetDatosFactibilidadAction()
    {
        $respuesta          = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $emComercial        = $this->get('doctrine')->getManager('telconet');
        $emInfraestructura  = $this->getDoctrine()->getManager("telconet_infraestructura");  
        $emGeneral          = $this->getDoctrine()->getManager("telconet_general");  
                
        $peticion           = $this->get('request');
        $arrayParametrosWs  = array();
        $objSession         = $peticion->getSession();
        $objPuntoCliente    = $objSession->get('ptoCliente');
        $strEmpresaCod      = $objSession->get('idEmpresa');
        $idServicio         = $peticion->get('idServicio');
        $strUltimaMilla     = $peticion->get('ultimaMilla');
        $strTipoSolicutud   = $peticion->get('tipoSolicitud')?$peticion->get('tipoSolicitud'):"";
        $strEsPseudoPe      = $peticion->get('esPseudoPe')?$peticion->get('esPseudoPe'):"";
        $strEsDataCenter    = $peticion->get('esDataCenter')?$peticion->get('esDataCenter'):"NO";
        $strTipoRed         = $peticion->get('tipoRed')?$peticion->get('tipoRed'):"MPLS";

        //Determinar que tipo de Ultima Milla se trata
        $servicioTecnico = $this->get('tecnico.InfoServicioTecnico');
        $objServicio     = $emComercial->getRepository("schemaBundle:InfoServicio")->find($idServicio);
        
        //Verificar si el enlace esta atachado solamente a un IAAS
        if (is_object($objServicio) && is_object($objServicio->getProductoId())
                && strpos($objServicio->getProductoId()->getGrupo(),'DATACENTER') !== false)
        {
            $arrayRespuesta = $servicioTecnico->getArrayInformacionTipoSolucionPorPreferencial($objServicio);
        }

        $boolIsCloud  = false;
        //Si la solucion relacionada no contiene housing se ira por flujo de IAAS neto
        if(!$arrayRespuesta['boolContieneHousing'])
        {
            $boolIsCloud = true;
        }

        //Obtengo el Producto
        $objProducto  = $objServicio->getProductoId();
        //Obtengo la Descripcion del Producto Clear a Channel Parametrizado
        $arrayParDet= $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
           ->getOne('ESTADO_CLEAR_CHANNEL','COMERCIAL','','ESTADO_CLEAR_CHANNEL','','','','','',$strEmpresaCod);
        $strDescripProducto = $arrayParDet["valor1"];
        
        if($objProducto->getDescripcionProducto() != $strDescripProducto)
        {
            /*Obtengo el tipo de red para poder realizar validaciones en base a el tipo de red GPON.*/
            $strTipoRed = $servicioTecnico->getValorCaracteristicaServicio($objServicio, 'TIPO_RED', 'Activo');
            //se verifica si el servicio es tipo de red GPON
            $booleanTipoRedGpon = false;
        }
        
        if(!empty($strTipoRed))
        {
            $arrayParVerTipoRed = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->getOne('NUEVA_RED_GPON_TN',
                                                                                                    'COMERCIAL',
                                                                                                    '',
                                                                                                    'VERIFICAR TIPO RED',
                                                                                                    'VERIFICAR_GPON',
                                                                                                    $strTipoRed,
                                                                                                    '',
                                                                                                    '',
                                                                                                    '');
            if(isset($arrayParVerTipoRed) && !empty($arrayParVerTipoRed))
            {
                $booleanTipoRedGpon = true;
            }
        }
        //verificar camara vpn
        if(!$booleanTipoRedGpon && is_object($objServicio) && is_object($objServicio->getProductoId()))
        {
            $arrayParametrosDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->getOne('PARAMETROS PROYECTO GPON SAFECITY',
                                                     'INFRAESTRUCTURA',
                                                     'PARAMETROS',
                                                     'VALIDAR RELACION SERVICIO ADICIONAL CON DATOS SAFECITY',
                                                     $objServicio->getProductoId()->getId(),
                                                     '',
                                                     '',
                                                     '',
                                                     'CAMARAVPN',
                                                     $strEmpresaCod);
            if(!empty($arrayParametrosDet["valor1"]) && isset($arrayParametrosDet["valor1"]))
            {
                $booleanTipoRedGpon = true;
            }
        }

        $arrParametros      = array (
                                        'idServicio'    => $idServicio,
                                        'emComercial'   => $emComercial,
                                        'ultimaMilla'   => $strUltimaMilla,
                                        'esDataCenter'  => $strEsDataCenter,
                                        'isCloud'       => $boolIsCloud
                                    );
        
        $arrInfoFactibilidad = array();
        $objServicioTecnico  = null;
        
        //Verificar si es pseudope para generacion de factibilidad de cambio de ultima milla
        $boolEsPseudoPe = $emComercial->getRepository("schemaBundle:InfoServicio")->esServicioPseudoPe($objServicio);
        
        if($boolEsPseudoPe)
        {
            $strEsPseudoPe = 'S';
        }
           
        if(is_object($objServicio))
        {
            $objServicioTecnico = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')->findOneByServicioId($objServicio->getId());
        }             
        
        if(is_object($objServicioTecnico))
        {
            //Obtener la caracteristica TIPO_FACTIBILIDAD para discriminar que sea FIBRA DIRECTA o RUTA
            $objServProdCaractTipoFact = $servicioTecnico
                                              ->getServicioProductoCaracteristica($objServicio,'TIPO_FACTIBILIDAD',$objServicio->getProductoId());

            if($objServProdCaractTipoFact)
            {
                $arrParametros['tipoUM'] = $objServProdCaractTipoFact->getValor();
            }
            else
            {
                //Si es fibra optica y no tiene caracteristica se determina toda opcion de cambio de UM como RUTA dado que el usuario
                //escoge informacion de GIS para generar factibilidad ( para data migrada )
                if($strUltimaMilla == 'Fibra Optica')
                {
                    //Si es fibra y tiene data de GIS es RUTA, caso contrario es migrado y pasa a ser DIRECTO
                    if($objServicioTecnico->getInterfaceElementoConectorId())
                    {
                        $arrParametros['tipoUM'] = 'RUTA';
                    }
                    else
                    {
                        $arrParametros['tipoUM'] = 'DIRECTO';
                    }
                }
                /* Se valida si la variable $strTipoRed no esta vacia y si es igual a GPON. */
                elseif ($strUltimaMilla == 'FTTx' && $booleanTipoRedGpon)
                {
                    $arrParametros['tipoUM'] = 'RUTA';
                }
                else //Servicios cuya data tecnica de GIS no existe ( Radio o UTP )
                {
                    $arrParametros['tipoUM'] = 'DIRECTO';
                }
            }
        }       
         //Si es solicitud de cambio de ultima milla obtiene los valores tecnicos generados en la factibilidad de UM
        if($strTipoSolicutud && $strTipoSolicutud == "SOLICITUD CAMBIO ULTIMA MILLA")
        {
            if(!$boolEsPseudoPe)
            {
                $arrParametros['emInfraestructura'] = $emInfraestructura;
                $arrParametros['idSolicitud']       = $peticion->get('idSolicitud');
                if ($strUltimaMilla == "Radio")
                {
                    $arrInfoFactibilidad = $emInfraestructura->getRepository('schemaBundle:InfoServicioTecnico')
                                                             ->getDatosFactibilidadUltimaMillaRadio($arrParametros);
                }
                else
                {                        
                    $arrInfoFactibilidad = $emInfraestructura->getRepository('schemaBundle:InfoServicioTecnico')
                                                             ->getDatosFactibilidadUltimaMilla($arrParametros);
                }
            }
            else
            {
                $arrInfoFactibilidad    = $emInfraestructura->getRepository('schemaBundle:InfoServicioTecnico')
                                                            ->getDatosFactibilidadPseudoPe($arrParametros);
            }
        }
        else
        {                
            //Si no es Pseudo Pe ( Cliente ) obtiene datos de factibilidad de manera normal
            if($strEsPseudoPe=="")
            {
                $arrInfoFactibilidad    = $emInfraestructura->getRepository('schemaBundle:InfoServicioTecnico')
                                                            ->getDatosFactibilidad($arrParametros);
            }
            else if($objServicio->getProductoId()->getNombreTecnico() === 'DATOS FWA')
            {
                $strRegionServicio  = "";
                if(is_object($objServicio->getPuntoId()))
                {
                    $intIdOficinaServicio   = is_object($objServicio->getPuntoId()->getPuntoCoberturaId()) ? 
                                                        $objServicio->getPuntoId()->getPuntoCoberturaId()->getOficinaId() : 0;
                    $objOficinaServicio     = $emComercial->getRepository("schemaBundle:InfoOficinaGrupo")
                                                          ->find($intIdOficinaServicio);
                    if(is_object($objOficinaServicio))
                    {
                        $objCantonServicio = $emComercial->getRepository("schemaBundle:AdmiCanton")
                                                         ->find($objOficinaServicio->getCantonId());
                        if(is_object($objCantonServicio))
                        {
                            $strRegionServicio = $objCantonServicio->getRegion();
                        }
                    }
                }
                if(isset($strRegionServicio))
                {
                    //Consulto el ro que pertenece
                    $arrayInfoRo    =  $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                 ->getOne('ROUTER_FWA',
                                                          'PLANIFICACION',
                                                          '',
                                                          '',
                                                          '',
                                                          $strRegionServicio,
                                                          '',
                                                          '',
                                                          '',
                                                          $peticion->getSession()->get('idEmpresa'));
                    if(!empty($arrayInfoRo))
                    {
                        $strRo = $arrayInfoRo['valor1'];
                        $objElementoRo = $emInfraestructura->getRepository("schemaBundle:InfoElemento")->findOneByNombreElemento($strRo);
                    }
                    if(is_object($objElementoRo))
                    {
                        $arrInfoFactibilidad= array(
                                                    'status' => 'OK',
                                                    'data'   => array(
                                                                       'idServicioTecnico' => $objServicioTecnico->getId(),
                                                                       'idServicio'        => $objServicio->getId(),
                                                                       'idElemento'        => $objElementoRo->getId(),
                                                                       'nombreElemento'    => $objElementoRo->getNombreElemento()
                                                                     )
                                                    );
                    }
                    else
                    {
                        throw new \Exception("No se puede obtener el RO de telefónica");
                    }
                }
            }
            else
            {
                $arrInfoFactibilidad    = $emInfraestructura->getRepository('schemaBundle:InfoServicioTecnico')
                                                            ->getDatosFactibilidadPseudoPe($arrParametros);
            }
            
            //Se valida si es Concentrador BACKUP obtener las capacidades a partir de su Concentrador PRINCIPAL al cual se encuentre enlazado
            if($objServicioTecnico->getTipoEnlace() == 'BACKUP' && $objServicio->getProductoId()->getEsConcentrador() == 'SI')
            {
                $arrayCapacidadesBackup = $servicioTecnico->getArrayCapacidadesConcentradorBackup($objServicio);

                if(isset($arrayCapacidadesBackup['intCapacidadUno']))
                {
                    $arrInfoFactibilidad['data']['capacidad1'] = $arrayCapacidadesBackup['intCapacidadUno'];
                }

                if(isset($arrayCapacidadesBackup['intCapacidadDos']))
                {
                    $arrInfoFactibilidad['data']['capacidad2'] = $arrayCapacidadesBackup['intCapacidadDos'];
                }
            }
        }

        try
        {
            if($arrInfoFactibilidad['status']=='OK')
            {
                $arrayInfoFactibilidad = $arrInfoFactibilidad['data'];

                $arrayParametrosWs["intIdElemento"] = $arrayInfoFactibilidad['idElemento'];
                $arrayParametrosWs["intIdServicio"] = $idServicio;

                if($strEsDataCenter == 'SI')
                {
                    //Si no contiene HOUSING se obtienen datos parametrizados de Pe de Datacenter
                    if($boolIsCloud)
                    {
                        $strRegion    = '';
                    
                        $intIdOficina = $objServicio->getPuntoId()->getPuntoCoberturaId()->getOficinaId();

                        $objOficina   = $emComercial->getRepository("schemaBundle:InfoOficinaGrupo")->find($intIdOficina);

                        if(is_object($objOficina))
                        {
                            $objCanton = $emComercial->getRepository("schemaBundle:AdmiCanton")->find($objOficina->getCantonId());

                            if(is_object($objCanton))
                            {
                                $strRegion = $objCanton->getProvinciaId()->getRegionId()->getNombreRegion();
                            }
                        }

                        //Obtener el Pe parametrizado dado que no existe factibilidad a nivel de backbone
                        $arrayInfoPe   =  $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getOne('ROUTERS DC - HOSTING', 
                                                          'TECNICO', 
                                                          '',
                                                          $strRegion,
                                                          '',
                                                          '',
                                                          '',
                                                          '', 
                                                          '', 
                                                          $peticion->getSession()->get('idEmpresa'));
                        if(!empty($arrayInfoPe))
                        {
                            $strPe = $arrayInfoPe['valor1'];
                            $objElementoPe = $emInfraestructura->getRepository("schemaBundle:InfoElemento")->findOneByNombreElemento($strPe);
                        }
                    }
                    else
                    {
                        $objElementoPe = $this->get('tecnico.InfoServicioTecnico')->getPeBySwitch($arrayParametrosWs);
                    }
                }
                else
                {
                    if($booleanTipoRedGpon)
                    {
                        $objElementoPe  = $this->get('tecnico.InfoServicioTecnico')->getPeByOlt($arrayParametrosWs);
                    }
                    else
                    {
                        $objElementoPe  = $this->get('tecnico.InfoServicioTecnico')->getPeBySwitch($arrayParametrosWs);
                    }
                }

                $arrInfoBackbone    = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                        ->getInfoBackboneByElemento($arrayInfoFactibilidad['idElemento'],$objElementoPe,"N");
             
                $arrInfoBackbone['vlanMin']   = '';
                $arrInfoBackbone['vlanMax']   = '';
                //**************************************Se obtiene el valor de VLAN cuando es tipo de red GPON**************************//
                if($booleanTipoRedGpon)
                {
                    //seteo los parametros de las vlans
                    $strVlanPriGpon = "VLAN INTERNET GPON PRINCIPAL";
                    $strVlanBkGpon  = "VLAN INTERNET GPON BACKUP";
                    $strVlanSafeGpon = "VLAN SAFECITY GPON";
                    $arrayParametrosSubred = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->getOne('NUEVA_RED_GPON_TN',
                                                                                                            'COMERCIAL',
                                                                                                            '',
                                                                                                            'NOMBRES PARAMETROS SUBREDES Y VLANS',
                                                                                                            '',
                                                                                                            '',
                                                                                                            '',
                                                                                                            '',
                                                                                                            '');
                    if(isset($arrayParametrosSubred) && !empty($arrayParametrosSubred))
                    {
                        $strVlanPriGpon = isset($arrayParametrosSubred['valor4']) && !empty($arrayParametrosSubred['valor4'])
                                         ? $arrayParametrosSubred['valor4'] : $strVlanPriGpon;
                        $strVlanBkGpon  = isset($arrayParametrosSubred['valor5']) && !empty($arrayParametrosSubred['valor5'])
                                         ? $arrayParametrosSubred['valor5'] : $strVlanBkGpon;
                        $strVlanSafeGpon = isset($arrayParametrosSubred['valor6']) && !empty($arrayParametrosSubred['valor6'])
                                         ? $arrayParametrosSubred['valor6'] : $strVlanSafeGpon;
                    }
                    if($objServicio->getProductoId()->getNombreTecnico() === 'INTERNET' ||
                       $objServicio->getProductoId()->getNombreTecnico() === 'INTMPLS')
                    {
                        if($objServicioTecnico->getTipoEnlace() == 'PRINCIPAL')
                        {
                            $strNombreParametro = $strVlanPriGpon;
                        }
                        else if($objServicioTecnico->getTipoEnlace() == 'BACKUP')
                        {
                            $strNombreParametro = $strVlanBkGpon;
                        }
                        $objParametroVlanGpon = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                            ->findOneBy(array("elementoId"    => $arrayInfoFactibilidad['idElemento'],
                                                                              "detalleNombre" => $strNombreParametro,
                                                                              "estado"        => "Activo"));
                        if(is_object($objParametroVlanGpon))
                        {
                            $arrInfoBackbone['vlan'] = $objParametroVlanGpon->getDetalleValor();
                        }
                    }
                    else if($objServicio->getProductoId()->getNombreTecnico() === 'DATOS SAFECITY')
                    {
                        $strNombreParametro   = $strVlanSafeGpon;
                        $objParametroVlanGpon = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                            ->findOneBy(array("elementoId"    => $arrayInfoFactibilidad['idElemento'],
                                                                              "detalleNombre" => $strNombreParametro,
                                                                              "estado"        => "Activo"));
                        if(is_object($objParametroVlanGpon))
                        {
                            $arrInfoBackbone['vlan'] = $objParametroVlanGpon->getDetalleValor();
                        }
                    }
                    else if($objServicio->getProductoId()->getNombreTecnico() === 'L3MPLS')
                    {
                        $arrayParametrosVlanGpon = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                        ->getOne('NUEVA_RED_GPON_TN',
                                                                                'COMERCIAL',
                                                                                '',
                                                                                '',
                                                                                '',
                                                                                '',
                                                                                '',
                                                                                '',
                                                                                'CATALOGO_VLANS_DATOS',
                                                                                $peticion->getSession()->get('idEmpresa'));

                        if( isset($arrayParametrosVlanGpon) && !empty($arrayParametrosVlanGpon) &&
                            isset($arrayParametrosVlanGpon["valor1"]) && isset($arrayParametrosVlanGpon["valor2"]) )
                        {
                            $arrInfoBackbone['vlanMin'] = $arrayParametrosVlanGpon["valor1"];
                            $arrInfoBackbone['vlanMax'] = $arrayParametrosVlanGpon["valor2"];
                        }
                    }
                }
                //Informacion de Servicio relacionado VSAT
                $arrInfoBackbone['esNuevoVsat']   = 'S';
                $arrInfoBackbone['idVrfVsat']     = '';
                $arrInfoBackbone['vrfVsat']       = '';
                $arrInfoBackbone['vlanVsat']      = '';
                $arrInfoBackbone['protocoloVsat'] = '';
                $arrInfoBackbone['asPrivadoVsat'] = '';
                $arrInfoBackbone['subredBbVsat']  = '';
                //Si se trata de un Servicio VSAT SATELITAL se establece el Anillo 1 para INTERNET DEDICADO y sea
                //gestionado como INTERNET MPLS
                if($strUltimaMilla == 'SATELITAL')
                {
                    $arrInfoBackbone['vlan'] = '';
                    $strNombreTecnico        = $objServicio->getProductoId()->getNombreTecnico();
                    
                    $strDescripcionBusqueda = '';
                    
                    if($strNombreTecnico == 'L3MPLS' || $strNombreTecnico == 'L3MPLS SDWAN')
                    {
                        $strDescripcionBusqueda = 'VLANS DATOS';
                    }
                    else
                    {
                        $strDescripcionBusqueda = 'VLANS INTERNET MPLS';
                    }
                    
                    $arrayParametrosDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getOne("VLANS_PERMITIDAS_VSAT", 
                                                             "TECNICO", 
                                                             "", 
                                                             $strDescripcionBusqueda, 
                                                             "", 
                                                             "", 
                                                             "",
                                                             "",
                                                             "",
                                                             $objSession->get('idEmpresa')
                                                           );
                    if(!empty($arrayParametrosDet))
                    {
                        if($strNombreTecnico == 'INTERNET' || $strNombreTecnico == 'INTERNET SDWAN' )
                        {                            
                            $arrInfoBackbone['anillo'] = $arrayParametrosDet['valor3'];   
                            $arrInfoBackbone['vlan']   = $arrayParametrosDet['valor1'];                            
                        }
                        else//Datos
                        {
                            //obtener rango permitido de vlans para datos satelital para producto de Datos
                            $arrInfoBackbone['vlanMin']   = $arrayParametrosDet['valor1']; 
                            $arrInfoBackbone['vlanMax']   = $arrayParametrosDet['valor2'];
                        }
                    }
                    
                    //Obtener si existe informacion de VSAT existente relacionado al Servicio
                    $arrayParametrosVsat                  = array();
                    $arrayParametrosVsat['intIdServicio'] = $objServicio->getId();
                    $arrayInfoVsatExistente = $emComercial->getRepository("schemaBundle:InfoServicioTecnico")
                                                          ->getInfoVsatRelacionada($arrayParametrosVsat);
                    
                    //Si existe registro se replicara esta informacion para los Servicios nuevos a ser creados
                    if(!empty($arrayInfoVsatExistente))
                    {
                        $arrInfoBackbone['esNuevoVsat']   = 'N';                                                
                        $arrInfoBackbone = array_merge($arrInfoBackbone,$arrayInfoVsatExistente);
                    }
                }
                
                $arrInfoBackbone['arraySubredesPublicasCompartidas']   = array();
                $arrInfoBackbone['arrayFirewallsDC']                   = array();
                
                if($objServicio->getProductoId() && strpos($objServicio->getProductoId()->getGrupo(),'DATACENTER')!==false)
                {
                    $intIdCanton     = 0;
                    
                    $strNombreCanton = $servicioTecnico->getCiudadRelacionadaPorRegion($objServicio,$peticion->getSession()->get('idEmpresa'));
                                
                    if(!empty($strNombreCanton))
                    {
                        $objCanton = $emComercial->getRepository("schemaBundle:AdmiCanton")->findOneByNombreCanton($strNombreCanton);

                        if(is_object($objCanton))
                        {
                            $intIdCanton     = $objCanton->getId();
                        }
                    }
                    
                    //Se obtienen las subredes publicas por canton GYE o UIO a ser utilizadas en Recursos Compartidos
                    $arraySubredesPublicas = $emInfraestructura->getRepository("schemaBundle:InfoSubred")
                                                               ->findBy(array('estado'   => 'Activo',
                                                                              'uso'      => 'INTERNETDC',
                                                                              'cantonId' => $intIdCanton
                                                                             ));

                    if(!empty($arraySubredesPublicas))
                    {
                        $arraySubredes = array();
                        
                        foreach($arraySubredesPublicas as $objSubred)
                        {
                            $arraySubredes[] = array('idSubred' => $objSubred->getId(), 'subred' => $objSubred->getSubred());
                        }
                        
                        $arrInfoBackbone['arraySubredesPublicasCompartidas'] = $arraySubredes;
                    }
                    
                    //Traer la informacion de Firewalls por Ciudad
                    $arrayParametrosFirewalls =   $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->get("FIREWALLS DATA CENTER", 
                                                                     "TECNICO", 
                                                                     "", 
                                                                     "", 
                                                                     $strNombreCanton, 
                                                                     "", 
                                                                     "",
                                                                     "",
                                                                     "",
                                                                     $objSession->get('idEmpresa')
                                                                   );

                    if(!empty($arrayParametrosFirewalls))
                    {
                        $arrInfoBackbone['arrayFirewallsDC'] = $arrayParametrosFirewalls;
                    }
                    
                    $arrInfoBackbone['arrayServiciosRelacionados'] = array();
                                        
                    //Obtener el producto ligado a la solucion que sea de HOUSING o HOSTING
                    $objInfoSolucionDet = $emComercial->getRepository('schemaBundle:InfoSolucionDet')
                            ->findOneBy(array('servicioId' => $objServicio->getId(),'estado'=>'Activo'));
                    $objInfoSolucionCab = is_object($objInfoSolucionDet) ? $objInfoSolucionDet->getSolucionCabId() : null;

                    if (is_object($objInfoSolucionCab))
                    {
                        $strNumerSolucion = $objInfoSolucionCab->getNumeroSolucion();

                        $arrayRequest['arrayRequest'] = array('idServicio'  => $objServicio->getId(),
                                                              'estado'      => 'Activo');
                        $arrayRequest["ociCon"]       = array('userCom'     => $this->container->getParameter('user_comercial'),
                                                              'passCom'     => $this->container->getParameter('passwd_comercial'),
                                                              'databaseDsn' => $this->container->getParameter('database_dsn'));

                        //Determinar si los servicios se encuentran agrupados como una solución
                        $arrayResponse  = $emComercial->getRepository('schemaBundle:InfoSolucionCab')->listarDetalleSolucion($arrayRequest);
                        $arrayResultado = oci_fetch_array($arrayResponse['objCsrResult'], OCI_ASSOC + OCI_RETURN_NULLS);

                        if ($arrayResponse['status'] === 'OK' && !empty($arrayResultado))
                        {
                            $arrayCores = explode("|",$arrayResultado['CORESREFERENTES']);
                        }

                        if (!empty($arrayCores))
                        {
                            foreach($arrayCores as $strTipo)
                            {
                                $arrayServicios = $emComercial->getRepository("schemaBundle:InfoServicio")
                                        ->getArrayServiciosPorSolucionYTipoSolucion($strNumerSolucion,$strTipo);

                                foreach($arrayServicios as $objServiciosSubSolucion)
                                {
                                    $objProductoSubSolucion = $objServiciosSubSolucion->getProductoId();
                                    
                                    $boolEsPool    = $servicioTecnico->isContieneCaracteristica($objProductoSubSolucion,'ES_POOL_RECURSOS');
                                    $boolEsHousing = $servicioTecnico->isContieneCaracteristica($objProductoSubSolucion,'ES_HOUSING');
                                    
                                    if($boolEsPool || $boolEsHousing)
                                    {
                                        $arrayParametrosIdentificadores = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                                    ->getOne("GRUPO PRODUCTOS CON SUB TIPO SOLUCION", 
                                                                                             "COMERCIAL", 
                                                                                             "", 
                                                                                             "", 
                                                                                             $strTipo, 
                                                                                             "", 
                                                                                             "",
                                                                                             "",
                                                                                             "",
                                                                                             $objSession->get('idEmpresa')
                                                                                           );
                                        
                                        $strColor = $arrayParametrosIdentificadores['valor3'];
                                        
                                        $arrayServiciosRelacionados[] = array('servicio'     => $objProductoSubSolucion->getDescripcionProducto(),
                                                                              'tipoSolucion' => $strTipo,
                                                                              'identificador'=> '<i class="fa fa-square" aria-hidden="true" '
                                                                                              . 'style="color:'.$strColor.'"></i>',
                                                                              'nombreTecnico'=> $objProductoSubSolucion->getNombreTecnico()
                                                                             );
                                    }
                                }
                            }
                            
                            $arrInfoBackbone['arrayServiciosRelacionados'] = $arrayServiciosRelacionados;
                        }
                    }
                }

                //*******Se consulta,  si el servicio es adicional DATOS GPON: se valida que al DATOS ya se le haya asignado recursos de red******//
                $arrayMergeResult['status'] = "OK";

                if(is_object($objServicio) && is_object($objServicio->getProductoId()))
                {
                    //Validar si el producto esta configurado para escenarios CAMARAS-SAFECITY
                    $arrayParametrosDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getOne('PARAMETROS PROYECTO GPON SAFECITY',
                                                             'INFRAESTRUCTURA',
                                                             'PARAMETROS',
                                                             'VALIDAR RELACION SERVICIO ADICIONAL CON DATOS SAFECITY',
                                                             $objServicio->getProductoId()->getId(),
                                                             '',
                                                             '',
                                                             '',
                                                             '',
                                                             $strEmpresaCod);

                    if(!empty($arrayParametrosDet["valor1"]) && isset($arrayParametrosDet["valor1"]))
                    {
                        $strIdProductoAValidar     = $arrayParametrosDet["valor2"];
                        $strEstadoServicioAValidar = $arrayParametrosDet["valor3"];

                        //Consultar el objeto producto
                        $objAdmiProducto = $emComercial->getRepository('schemaBundle:AdmiProducto')
                                                       ->findOneBy(array('id'     => $strIdProductoAValidar,
                                                                         'estado' => "Activo"));

                        if($objPuntoCliente)
                        {
                            //Consultar el objeto punto
                            $objAdmiPunto = $emComercial->getRepository('schemaBundle:InfoPunto')->find($objPuntoCliente['id']);
                        }

                        if(is_object($objAdmiPunto) && is_object($objAdmiProducto))
                        {
                            //Consultar el estado actual del servicio preferencial
                            $objInfoServcio = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                          ->findOneBy(array('puntoId'    => $objAdmiPunto,
                                                                            'productoId' => $objAdmiProducto),
                                                                      array('id'         => 'DESC'));

                            if(is_object($objInfoServcio) && $objInfoServcio->getEstado() == $strEstadoServicioAValidar)
                            {
                                $arrayMergeResult['status'] = "ERROR";
                                $arrayMergeResult['msg']    = "Primero se debe generar Recursos de Red para el DATOS SAFECITY.";
                            }
                        }
                    }
                }
                                                                                  
                $objInternetDedicado = $emComercial->getRepository('schemaBundle:AdmiProducto')
                                                        ->findOneBy(array( "descripcionProducto" => "INTERNET DEDICADO", "estado"=>"Activo"));
                if($objInternetDedicado &&  ($arrInfoBackbone['anillo'] === "0"))
                {
                    $arrInfoBackbone['vlan'] = '1';
                }
                //Lógica para consultar si posee o no la característica para presentar la pantalla manual en asignación de recursos de red
                
                $arrInfoBackbone['banderaVentanaManualAsignarRecursosDeRed'] = 'N';
                if(is_object($objServicio))
                {
                    $strVentanaRecursosRedProvincias = $servicioTecnico->getValorCaracteristicaServicio($objServicio,
                                                                         'VENTANA_RECURSOS_RED_PROVINCIAS');
                    
                    if(!empty($strVentanaRecursosRedProvincias) && $strVentanaRecursosRedProvincias === 'S')
                    {
                        $arrInfoBackbone['banderaVentanaManualAsignarRecursosDeRed'] = 'S';
                    }   
                }

                //Lógica para consultar si el PE debe mostrar o no la pantalla manual de asignacion de recursos de red de Internet
                $arrInfoBackbone['banderaVentanaManualAsignarRecursosDeRedPorPe'] = "N";
                if($strEmpresaCod == "10" && is_object($objElementoPe))
                {
                    $intIdElementoPe = $objElementoPe->getId();
                    $arrayDetalles   = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                         ->getDetallesElementoByNombre($intIdElementoPe,'MOSTRAR_VENTANA_MANUAL_RECURSOS_RED_POR_PE');
                    if($arrayDetalles)
                    {
                        $arrInfoBackbone['banderaVentanaManualAsignarRecursosDeRedPorPe'] = $arrayDetalles[0]['detalleValor'];
                    }
                }
                
                //*******Se consulta,  si el servicio es CAMARA SAFECITY: se valida que al DATOS ya se le haya asignado recursos de red******//
                if($arrayMergeResult['status'] == "OK")
                {
                    $arrayMergeResult           = array_merge($arrayInfoFactibilidad,$arrInfoBackbone);
                    $arrayMergeResult['status'] = "OK";
                    /**
                     * Se busca los datos del concentrador fbermeo
                     */
                    $servicioTecnicoService = $this->get('tecnico.InfoServicioTecnico');

                    $objJsonOrigen   = $emComercial->getRepository("schemaBundle:InfoServicioProdCaract")
                                           ->getInfoEnlaceDatos($idServicio,$servicioTecnicoService,'ORIGEN');

                    $arrayMergeResult['concentrador'] = $objJsonOrigen;

                    $arrayMergeResult['esInterconexion'] = 'NO';
                    //Consultar si el producto requiere una ipLoopBack
                    if(is_object($objServicio->getProductoId()) && $objServicio->getProductoId()->getNombreTecnico() === "DATOS FWA")
                    {
                        /* @var $serviceRecursoRed RecursosDeRedService */
                        $serviceRecursoRed      = $this->get('planificacion.RecursosDeRed');
                        //Obtener la región del punto
                        $strRegionServicio      = "";
                        if(is_object($objServicio->getPuntoId()))
                        {
                            $intIdOficinaServicio   = is_object($objServicio->getPuntoId()->getPuntoCoberturaId()) ? 
                                                                $objServicio->getPuntoId()->getPuntoCoberturaId()->getOficinaId() : 0;
                            $objOficinaServicio     = $emComercial->getRepository("schemaBundle:InfoOficinaGrupo")
                                                                        ->find($intIdOficinaServicio);
                            if(is_object($objOficinaServicio))
                            {
                                $objCantonServicio = $emComercial->getRepository("schemaBundle:AdmiCanton")
                                                                       ->find($objOficinaServicio->getCantonId());
                                if(is_object($objCantonServicio))
                                {
                                    $strRegionServicio = $objCantonServicio->getRegion();
                                }
                            }
                        }

                        $arrayParametroAsignaIp = array('objServicio'           => $objServicio,
                                                        'strUsrCreacion'        => $objSession->get('user'),
                                                        'strIpCreacion'         => $peticion->getClientIp(),
                                                        'strNombreParametro'    => 'IP_LOOPBACK_FWA',
                                                        'strValor1'             => $strRegionServicio);
                        $arrayLoopBack          = $serviceRecursoRed->asignarIpFWA($arrayParametroAsignaIp);
                        if(isset($arrayLoopBack) && $arrayLoopBack['status'] == 'OK')
                        {
                            $arrayMergeResult['ipLoopBack'] = $arrayLoopBack['data']['strIpLoopBack'];
                        }
                        else
                        {
                            throw new \Exception($arrayLoopBack['data']['strMensaje']);
                        }
                    }

                    //Determinar si se trata de flujo de Intercinexion ( Para Concentradores )
                    if($objServicio->getProductoId()->getEsConcentrador() == 'SI')
                    {
                        $objServProdCaractTipoFact = $servicioTecnico
                                                     ->getServicioProductoCaracteristica($objServicio,
                                                                                         'INTERCONEXION_CLIENTES',
                                                                                         $objServicio->getProductoId()
                                                                                        );
                        if(is_object($objServProdCaractTipoFact) && $objServProdCaractTipoFact->getValor() == 'S')
                        {
                            $arrayMergeResult['esInterconexion'] = 'SI';
                        }
                    }
                }
            }
            else
            {
                $arrayMergeResult['status'] = "ERROR";
                $arrayMergeResult['msg']    = $arrInfoFactibilidad['msg'];
            }
        }
        catch(\Exception $e)
        {            
            $arrayMergeResult['status'] = "ERROR";
            $arrayMergeResult['msg']    = $e->getMessage();
        }

        

        // INICIO VALIDACIÓN PARA MIGRACION DE PRODUCTOS SDWAN **********************************
        if(is_object($objServicio) &&  
        ($objServicio->getProductoId()->getNombreTecnico() === 'INTERNET SDWAN' ||
        $objServicio->getProductoId()->getNombreTecnico() === 'L3MPLS SDWAN'))
        {
            $booleanEsSDWAN =  true;
            $booleanEsMigracionSDWAN =  false;


            $objCaracteristicasMigracionSDWAN = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
            ->findOneBy(array(
                'descripcionCaracteristica' => 'Migración de Tecnología SDWAN',
                'estado'                    => 'Activo'
            ));

            $objAdmiProdCaract = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
            ->findOneBy(array('caracteristicaId'=> $objCaracteristicasMigracionSDWAN->getId() ?: '',
                               'productoId'     => $objServicio->getProductoId()->getId() ?: '',
                               'estado'         => 'Activo'));
       


            $objInfoServProdCaract = $emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
            ->findOneBy(array('servicioId'=>$objServicio->getId(),
                                'estado' => 'Activo',
                                'productoCaracterisiticaId'=>$objAdmiProdCaract->getId()?:''));

            if(is_object($objInfoServProdCaract) && $objInfoServProdCaract->getValor() === 'S')
            {
                $booleanEsMigracionSDWAN =  true;
            }


            if($booleanEsSDWAN && $booleanEsMigracionSDWAN)
            {
                $objCaracteristicaServicioMigrado = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                ->findOneBy(array(
                    'descripcionCaracteristica' => 'SERVICIO_MIGRADO_SDWAN',
                    'estado'                    => 'Activo',
                ));

                $objAdmiProdCaractServMigrado = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                ->findOneBy(array('caracteristicaId'=> $objCaracteristicaServicioMigrado->getId() ?: '',
                                  'productoId'      => $objServicio->getProductoId()->getId() ?: '',
                                  'estado'          => 'Activo'));

                $objInfoServProdCaractServicioMigrado = $emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                ->findOneBy(array('servicioId'               =>$objServicio->getId(),
                                  'estado'                   => 'Activo',
                                  'productoCaracterisiticaId'=>$objAdmiProdCaractServMigrado->getId()?:''));

                if(is_object($objInfoServProdCaractServicioMigrado) 
                && intval($objInfoServProdCaractServicioMigrado->getValor())>0)
                {
                    $objInfoServicioPrincipal = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                ->find($objInfoServProdCaractServicioMigrado->getValor());

                    $objAdmiProducto = null;

                    if(is_object($objInfoServicioPrincipal) 
                    && is_object($objInfoServicioPrincipal->getProductoId()))
                    {
                        $objAdmiProducto = $objInfoServicioPrincipal->getProductoId();
                    }


                    $objIp = $emInfraestructura->getRepository('schemaBundle:InfoIp')
                    ->findOneBy(
                        array( "servicioId" => $objInfoServProdCaractServicioMigrado->getValor(),
                               'estado'     => 'Activo'),
                        array('tipoIp'      =>  'DESC')
                    ); 

                    if(is_object($objIp) && $objIp->getSubredId())
                    {
                        $objSubred = $emInfraestructura->getRepository('schemaBundle:InfoSubred')
                        ->find($objIp->getSubredId());
                    }

                    $objCaracteristicaVRF = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                    ->findOneBy(array(
                            'descripcionCaracteristica' => 'VRF',
                            'estado'                    => 'Activo'
                        ));
    
                    $objAdmiProdCaractVRF = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                    ->findOneBy(array('caracteristicaId'=> $objCaracteristicaVRF->getId() ?: '',
                                      'productoId'      => $objAdmiProducto->getId() ?: '',
                                      'estado'          => 'Activo'
                                    )
                            );
    
                    $objInfoServProdCaractVRF = $emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                    ->findOneBy(array('servicioId'               =>$objInfoServProdCaractServicioMigrado->getValor(),
                                      'estado'                   => 'Activo',
                                      'productoCaracterisiticaId'=>$objAdmiProdCaractVRF->getId()?:''));

                    $objVrfSDWAN = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                            ->find($objInfoServProdCaractVRF->getValor());
                }

                $arrayInfoMigracionSDWAN = array(
                    'EsSDWAN'          => $booleanEsSDWAN,
                    'EsMigracionSDWAN' => $booleanEsMigracionSDWAN,
                    'objIp'            =>  array(
                        'ip'           => is_object($objIp) ? $objIp->getIp(): '',
                        'tipoIp'       => is_object($objIp) ? $objIp->getTipoIp(): '',
                        'subredId'     => is_object($objIp) ? $objIp->getSubredId(): '',
                        'subred'       => is_object($objSubred) ? $objSubred->getSubred(): '',
                        'vrf'          => is_object($objVrfSDWAN) ? $objVrfSDWAN->getValor(): '',
                        'vrfId'        => is_object($objVrfSDWAN) ? $objVrfSDWAN->getId(): '',
                    )
                );

                $arrayMergeResult =  array_merge($arrayInfoMigracionSDWAN, $arrayMergeResult);
            }
        }
        // FIN VALIDACIÓN PARA MIGRACION DE PRODUCTOS SDWAN **********************************



        
        $objJson = json_encode($arrayMergeResult);

        $respuesta->setContent($objJson);

        return $respuesta;
    }
       
    /**
     * Documentacion para el método 'ajaxGetSubredesL3mplsDisponiblesAction'
     * 
     * Funcion ajax que sirve para obtener las subredes disponibles para aprovisionamiento un cliente por Elemento
     * 
     * 
     * @author Kenneth Jimenez <kjimenez@telconet.ec>
     * @version 1.0 28-03-2016
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 16-11-2016 - Obtener subredes para escenarios de servicios dependientes de pseudo Pe
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.2 27-02-2018 - Obtener subredes para escenarios de servicios con interconexion entre clientes ( solo mostrara las subredes que
     * tengan configurados este uso especial )
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.3 25-05-2021 - Se agrega el tipo de red y el uso de red GPON para las subredes.
     *
     * @author Manuel Carpio <mcarpio@telconet.ec>
     * @version 1.3.1 13-12-2022 - Se agrega el nombre tecnico L3MPLS SWAN al arreglo de envio
     *                             getJsonSubredesDisponiblesCliente.
     *
     * @return $respuesta JsonResponse
     */
    public function ajaxGetSubredesL3mplsDisponiblesAction()
    {
        $objJsonResponse  = new JsonResponse();
        $objRequest       = $this->get('request');
        $intIdServicio    = $objRequest->get('idServicio')?$objRequest->get('idServicio'):0;
        $emInfraestructura= $this->getDoctrine()->getManager('telconet_infraestructura');
        $emComercial      = $this->getDoctrine()->getManager('telconet');
        $emGeneral        = $this->getDoctrine()->getManager("telconet_general");
        $serviceTecnico   = $this->get('tecnico.InfoServicioTecnico');
        $arrayParametros  = array();
        $strNombreTecnico = '';
        $arrayParametros['boolEsInterconexion'] = false; 
        //Interconexiones
        $arrayParametros['strUsoSubred']        = 'DATOS-MIGRA-CLIENTES';
        $arrayParametros['strTipoRed']          = $objRequest->get('strTipoRed') ? $objRequest->get('strTipoRed') : "MPLS";
        //se verifica si el servicio es tipo de red GPON
        $booleanTipoRedGpon = false;
        $arrayParVerTipoRed = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->getOne('NUEVA_RED_GPON_TN',
                                                                                                'COMERCIAL',
                                                                                                '',
                                                                                                'VERIFICAR TIPO RED',
                                                                                                'VERIFICAR_GPON',
                                                                                                $arrayParametros['strTipoRed'],
                                                                                                '',
                                                                                                '',
                                                                                                '');
        if(isset($arrayParVerTipoRed) && !empty($arrayParVerTipoRed))
        {
            $booleanTipoRedGpon = true;
        }
        //Verificar si el servicio no tiene caracteristica de interconexion
        if($intIdServicio!=0)
        {
            $objServicio = $emComercial->getRepository("schemaBundle:InfoServicio")->find($intIdServicio);
            
            if(is_object($objServicio))
            {
                $objCaractInterconexion = $serviceTecnico->getServicioProductoCaracteristica($objServicio,
                                                                                             'INTERCONEXION_CLIENTES',
                                                                                             $objServicio->getProductoId());
                if(is_object($objCaractInterconexion) && $objCaractInterconexion->getValor()=='S')
                {
                    $arrayParametros['boolEsInterconexion'] = true; 
                }
                if($objServicio->getProductoId()->getNombreTecnico() == 'DATOS FWA')
                {
                    $arrayParametros    = array(
                                                'strUsoSubred'  => 'DATOS-FWA',
                                                'strEstado'     => 'Activo'
                                               );
                    $objJsonResult      = $emInfraestructura->getRepository('schemaBundle:InfoSubred')
                                                            ->getJsonSubredesDisponiblesClienteFWA($arrayParametros);

                    $objJsonResponse->setContent($objJsonResult);
                    return $objJsonResponse;
                }
                //seteo los parametros de las subredes y vlans
                $strSafeCityGpon = "SAFECITYGPON";
                $strDatosGpon = "DATOSGPON";
                $arrayParametrosSubred = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                        ->getOne('NUEVA_RED_GPON_TN',
                                                                                'COMERCIAL',
                                                                                '',
                                                                                'NOMBRES PARAMETROS SUBREDES Y VLANS',
                                                                                '',
                                                                                '',
                                                                                '',
                                                                                '',
                                                                                '');
                if(isset($arrayParametrosSubred) && !empty($arrayParametrosSubred))
                {
                    $strSafeCityGpon = isset($arrayParametrosSubred['valor3']) && !empty($arrayParametrosSubred['valor3'])
                                     ? $arrayParametrosSubred['valor3'] : $strSafeCityGpon;
                    $strDatosGpon = isset($arrayParametrosSubred['valor7']) && !empty($arrayParametrosSubred['valor7'])
                                     ? $arrayParametrosSubred['valor7'] : $strDatosGpon;
                }
                //agrego el uso de las subredes para tipo de red GPON
                if($booleanTipoRedGpon)
                {
                    $arrayParametros['strUsosGponSubred'] = array($strDatosGpon);
                }
                else
                {
                    $arrayParametros['strUsosGponSubred'] = array($strDatosGpon,$strSafeCityGpon);
                }
            }
        }
        
        //Se validad el el nombre tecnico es L3MPLS SDWAN se envia para busqueda de sub red
        if(is_object($objServicio))
        {
            $objProducto = $objServicio->getProductoId();
            if(is_object($objProducto))
            {
                $strNombreTecnico = ($objProducto->getNombreTecnico() === 'L3MPLS SDWAN') ? 
                          $objProducto->getNombreTecnico():$objRequest->get('nombreTecnico');
            }
        }
        else
        {
            $strNombreTecnico = $objRequest->get('nombreTecnico');
        }        
        $arrayParametros['intIdPersonaEmpresaRol'] = $objRequest->get('idPersonaEmpresaRol');
        $arrayParametros['strNombreElemento']      = $objRequest->get('nombreElemento');
        $arrayParametros['strNombreTecnico']       = $strNombreTecnico;
        $arrayParametros['strTipoEnlace']          = $objRequest->get('tipoEnlace');
        $arrayParametros['intAnillo']              = $objRequest->get('anillo');
        $arrayParametros['strEsPseudoPe']          = $objRequest->get('esPseudoPe');
        
        $objJsonResult           = $emInfraestructura->getRepository('schemaBundle:InfoSubred')
                                                     ->getJsonSubredesDisponiblesCliente($arrayParametros);
        
        $objJsonResponse->setContent($objJsonResult);

        return $objJsonResponse;
    }
    
    /**
     * Documentacion para el método 'ajaxGetInfoBackboneSubredL3mplsAction'
     * 
     * Funcion ajax que sirve para obtener las subredes disponibles para aprovisionamiento un cliente por Elemento
     * 
     * 
     * @author Kenneth Jimenez <kjimenez@telconet.ec>
     * @version 1.0 28-03-2016
     *
     * @return $respuesta JsonResponse
     */
    public function ajaxGetInfoBackboneSubredL3mplsAction()
    {
        $response            = new JsonResponse();
        
        $request             = $this->get('request');

        $idServicio          = $request->get('idServicio');
        $emComercial         = $this->getDoctrine()->getManager();
        $emInfraestructura   = $this->getDoctrine()->getManager('telconet_infraestructura');
        $tecnicoService      = $this->get('tecnico.InfoServicioTecnico');
        
        $objServicio         = $emComercial->getRepository("schemaBundle:InfoServicio")->find($idServicio);
        $objServicioTecnico  = $emComercial->getRepository("schemaBundle:InfoServicioTecnico")->findOneByServicioId($idServicio);
        
        $objResult           = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                           ->getJsonInfoBackboneL3mpls($objServicio,$objServicioTecnico,$emInfraestructura,$tecnicoService);
        
        $response->setContent($objResult);

        return $response;
    }
    
    /**
     * Documentacion para el método 'ajaxGetVlansDisponiblesAction'
     * 
     * Funcion ajax que sirve para obtener las Vlans de un cliente por Elemento
     * 
     * @author Kenneth Jimenez <kjimenez@telconet.ec>
     * @version 1.0 28-03-2016
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.1 2-06-2016
     * Se agrega la variable anillo, para validar que la vlan este en el rango correcto
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.2 31-08-2016
     * @since 1.1
     * Se modifica proceso para reautilización de codigo, se creo el metodo getVlansDisponiblesAction con
     * parte del codigo que se utilizaba en esta función 
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.3 27-09-2017 - Se envia parametro de tipo de vlan en caso de ser necesaria, puede ser valor LAN o WAN para flujos de DC
     * @since 1.2
     *
     * @return $respuesta JsonResponse
     */
    public function ajaxGetVlansDisponiblesAction()
    {
        ini_set('max_execution_time', 3000000);
        $response            = new JsonResponse();
        
        $request             = $this->get('request');
        
        $nombreElemento      = $request->get('nombreElemento');
        $idPersonaEmpresaRol = $request->get('idPersonaEmpresaRol');
        $anillo              = $request->get('anillo');
        $strCaractVlan       = $request->get('tipoVlan')?'VLAN_'.$request->get('tipoVlan'):'VLAN';
        
        $objResult           = $this->getVlansDisponiblesAction($idPersonaEmpresaRol, $nombreElemento, $anillo, $strCaractVlan);
        $resultado           = json_encode($objResult);
        $response->setContent($resultado);

        return $response;
    }
    
    /**
     * Documentacion para el método 'getVlansDisponiblesAction'
     * 
     * Funcion ajax que sirve para obtener array de Vlans de un cliente por Elemento
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 30-08-2016
     * @since 1.0 30-08-2016
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 27-09-2017 - Se agrega variable con caracteristica de vlan para soportar las usadas en flujo de DATACENTER
     *                         - Se parametriza envio de valores a la funcion de generacion de VLANS
     * @since 1.0 30-08-2016
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 27-08-2019 Se agrega el parámetro 'strMigracionVlan' a la llamada de la función getVpnsImportCliente, por proyecto de
     *                         segmentacion de vlan para Nedetel.
     *
     * @param $intIdPersonaEmpresaRol   identificador del rol de la persona en sesion del sistema
     * @param $strNombreElemento        nombre del elemento del cual se recuperara información
     * @param $intAnillo                id de anillo del elemento del cual se recuperar información
     * @param $strCaractVlan            valor de la caracteristica de vlan, la cual puede ser VLAN, VLAN_LAN, VLAN_WAN
     *
     * @return $arrayResult array [total, data]  total: Cantidad todal de registros de vlans recuperados
     *                                           data : Información de vlans recuperadas
     */
    public function getVlansDisponiblesAction($intIdPersonaEmpresaRol, $strNombreElemento, $intAnillo, $strCaractVlan = 'VLAN')
    {
        $arrayParametrosVlan                           = array();
        $arrayParametrosVlan['intIdPersonaEmpresaRol'] = $intIdPersonaEmpresaRol;
        $arrayParametrosVlan['intAnillo']              = $intAnillo;
        $arrayParametrosVlan['strNombre']              = $strNombreElemento;
        $arrayParametrosVlan['intVlan']                = '';
        $arrayParametrosVlan['strCaractVlan']          = $strCaractVlan;
        $arrayParametrosVlan['intStart']               = '';
        $arrayParametrosVlan['intLimit']               = '';
        
        $arrayResult         = $this->getDoctrine()
                                    ->getManager("telconet")
                                    ->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                    ->getVlansCliente($arrayParametrosVlan);
        
        $arrayVpnsImportadas = array();
        
        //Si es flujo STANDARD busca informacion de Vlans Importadas
        //La caracteristica manejada para flujo standard es VLAN, para flujos de DATACENTER
        //se utilizan VLAN_LAN o VLAN_WAN
        if($strCaractVlan == 'VLAN')
        {
            $strMigracionVlan      = "N";

            $arrayParametrosVpn = array(
                "intIdPersonaEmpresaRol" => $intIdPersonaEmpresaRol,
                "strMigracionVlan"       => $strMigracionVlan
            );
            $arrayVpnsImportadas   = $this->getDoctrine()
                                          ->getManager()
                                          ->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                          ->getVpnsImportCliente($arrayParametrosVpn);
        }
        
        if(!empty($arrayVpnsImportadas))
        {
            $arrayParametrosVlan['strCaractVlan']          = 'VLAN';
            
            foreach($arrayVpnsImportadas['data'] as $obj)
            {
                $arrayParametrosVlan['intIdPersonaEmpresaRol'] = $obj['personaEmpresaRolId'];
        
                $arrayResultImport         = $this->getDoctrine()
                                                  ->getManager("telconet")
                                                  ->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                  ->getVlansCliente($arrayParametrosVlan);
                if($arrayResultImport['total']>0)
                {
                    $arrayResult['data']      = array_merge($arrayResult['data'],$arrayResultImport['data']);
                    $arrayResult['total']     = $arrayResult['total'] + $arrayResultImport['total'];
                }
            }
        }
        
        return $arrayResult;
    }
    
/**
     * Documentacion para el método 'ajaxGetVlansDisponiblesClearCh'
     * 
     * Funcion ajax que sirve para obtener array de Vlans de un cliente por Elemento
     * 
     * @author Josue Valencia <ajvalencia@telconet.ec>
     * @version 1.0 15-12-2022
     * @since 1.0 15-12-2022
     * 
     *
     * @param $intIdPersonaEmpresaRol   identificador del rol de la persona en sesion del sistema
     * @param $strNombreElemento        nombre del elemento del cual se recuperara información
     * @param $intAnillo                id de anillo del elemento del cual se recuperar información
     * @param $strCaractVlan            valor de la caracteristica de vlan, la cual puede ser VLAN, VLAN_LAN, VLAN_WAN
     *
     * @return $respuesta JsonResponse
     * 
     */
    public function ajaxGetVlansDisponiblesClearChAction()
    {   
        ini_set('max_execution_time', 3000000);
        $objResponse            = new JsonResponse();
        $objRequest          = $this->get('request');
        $objSession          = $objRequest->getSession();
        $emGeneral           = $this->getDoctrine()->getManager("telconet_general");
        $strEmpresaCod      = $objSession->get('idEmpresa');
        $strNombreElemento      = $objRequest->get('idNombreElemento');
        $intIdPersonaEmpresaRol = $objRequest->get('idPersonaEmpresaRol');
        $intAnillo              = $objRequest->get('anillo');
        $strCaractVlan       = $objRequest->get('tipoVlan')?'VLAN_'.$objRequest->get('tipoVlan'):'VLAN';

        $arrayParametrosVlan                           = array();
        $arrayParametrosVlan['intIdPersonaEmpresaRol'] = $intIdPersonaEmpresaRol;
        $arrayParametrosVlan['intAnillo']              = $intAnillo;
        $arrayParametrosVlan['intIdElemento']          = $strNombreElemento;
        $arrayParametrosVlan['intVlan']                = '';
        $arrayParametrosVlan['strCaractVlan']          = $strCaractVlan;
        $arrayParametrosVlan['intStart']               = '';
        $arrayParametrosVlan['intLimit']               = '';

        
        $arrayResponse  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                              ->getOne('RANGO_VLANS_CH',
                                                       'TECNICO',
                                                       null,
                                                       'RANGO_VLANS_CLEAR_CHANNEL',
                                                       null,
                                                       null,
                                                       null,
                                                       null,
                                                       null,
                                                       $strEmpresaCod);
        
        if($arrayResponse["valor1"] =='WAN')
        {
            $arrayParametrosVlan['intStart'] = $arrayResponse["valor2"];
            $arrayParametrosVlan['intLimit'] = $arrayResponse["valor3"];
        }

        $arrayVlans         = $this->getDoctrine()
                                    ->getManager("telconet")
                                    ->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                    ->getVlansClienteClearChannel($arrayParametrosVlan);
        $arrayVpnsImportadas = array();

        if(!empty($arrayVlans))
        {
            foreach($arrayVlans['data'] as $objVlan)
            {
                $arrayVpnsImportadas[] = array('id'    => $objVlan['id_detalle_elemento'],
                                           'vlan' => $objVlan['vlan']);
            }
            
        }
        $objResultado = json_encode($arrayVpnsImportadas);
        $objResponse->setContent($objResultado);

        return $objResponse;
    }

    /**
     * Documentacion para el método 'ajaxGetVlansDisponiblesPorVrfAction'
     * 
     * Funcion ajax que sirve para obtener Vlans de un cliente por Elemento por Vrf seleccionada
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 30-08-2016
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.1 02-03-2017 Se quitó última validación (verificación de que la vlan pertenezca al mismo pe y al anillo), que se hace luego de que 
     *                         la VRF NO está asociada, por solicitud del usuario ya que se requiere que aparezcan las vlans importadas de los otros 
     *                         clientes.
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.2 25-10-2018 Se agrega la validación para cuando sea un producto CANAL TELEFONIA se muestre la VLAN correspondiente
     * 
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.3 20-11-2019 Se agrega lógica para enviar el tipo de red a la función getVlansCliente.
     *
     * @since 1.0 30-08-2016
     *
     * @return $objResult array
     */
    public function ajaxGetVlansDisponiblesPorVrfAction()
    {
        ini_set('max_execution_time', 3000000);
        $objResponse            = new JsonResponse();
        $objRequest             = $this->get('request');
        $objSession             = $objRequest->getSession();
        $strEmpresaCod          = $objSession->get('idEmpresa');
        $strNombreElemento      = $objRequest->get('nombreElemento');
        $intIdPersonaEmpresaRol = $objRequest->get('idPersonaEmpresaRol');
        $intAnillo              = $objRequest->get('anillo');
        $intIdVrf               = $objRequest->get('idVrf');
        $intIdServicio          = $objRequest->get('idServicio');
        $strMigracionManual     = $objRequest->get('strMigracionManual')?$objRequest->get('strMigracionManual'):"N";
        $strTipoRed             = $objRequest->get('strTipoRed') ? $objRequest->get('strTipoRed'):'MPLS';
        $arrayResult            = array();
        $arrayRegistrosVlan     = array();
        $arrayResultadoVlan     = null;
        $emGeneral              = $this->getDoctrine()->getManager('telconet_general');

        //se verifica si el servicio es tipo de red GPON
        $booleanTipoRedGpon = false;
        $arrayParVerTipoRed = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->getOne('NUEVA_RED_GPON_TN',
                                                                                                'COMERCIAL',
                                                                                                '',
                                                                                                'VERIFICAR TIPO RED',
                                                                                                'VERIFICAR_GPON',
                                                                                                $strTipoRed,
                                                                                                '',
                                                                                                '',
                                                                                                '');
        if(isset($arrayParVerTipoRed) && !empty($arrayParVerTipoRed))
        {
            $booleanTipoRedGpon = true;
        }
        //Parametro enviado por la herramienta de migracion individual de vlan, si es S se retornan solo las vlans mapeadas para Nedetel
        if($strMigracionManual == "S" && !$booleanTipoRedGpon)
        {
            $arrayParametrosManual["intPersonaEmpresaRol"]  = $intIdPersonaEmpresaRol;
            $arrayParametrosManual["strNombreElemento"]     = $strNombreElemento;
            $arrayParametrosManual["intAnillo"]             = $intAnillo;
            $arrayParametrosManual["intIdVrf"]              = $intIdVrf;

            $arrayResultado = $this->getVlansMigracionManual($arrayParametrosManual);
        }
        else if($booleanTipoRedGpon)
        {
            $arrayParametros = array('intIdPersonaEmpresaRol' => $intIdPersonaEmpresaRol,
                                     'strEmpresaCod'          => $strEmpresaCod,
                                     'strCaractVlan'          => 'VLAN',
                                     'strNombre'              => $strNombreElemento,
                                     'strTipoRed'             => $strTipoRed);
            $arrayResultado = $this->getDoctrine()->getManager("telconet")
                                   ->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')->getVlansCliente($arrayParametros);
        }
        else
        {
            $arrayResultado = $this->getVlansDisponiblesAction($intIdPersonaEmpresaRol, $strNombreElemento, $intAnillo);
        }

        $arrayVlans             = $arrayResultado["data"];

        foreach($arrayVlans as $objVlan)
        {
            $arrayResult = $this->getDoctrine()
                                ->getManager()
                                ->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                ->getValidaVrfsClientePorVlan($objVlan["id"], $strEmpresaCod);
            
            if ("SI" == $arrayResult["asociada"])
            {
                if ($arrayResult["id_vrf"] == $intIdVrf)
                {
                    $arrayRegistrosVlan [] = $objVlan;
                }
            }
            else
            {
                $arrayRegistrosVlan [] = $objVlan;
            }
        }
        
        //valido la vlan para telefonia TN
        $arrayVlanNetvoice = $this->validaVlanTelefonia(array(  'intIdPersonaEmpresaRol' => $intIdPersonaEmpresaRol, 
                                                                'intIdServicio'          => $intIdServicio, 
                                                                'intAnillo'              => $intAnillo,
                                                                'strIp'                  => $objRequest->getClientIp(),
                                                                'strNombreElemento'      => $strNombreElemento,
                                                                'strUser'                => $objSession->get('user')));
        if(is_array($arrayVlanNetvoice))
        {
            unset($arrayRegistrosVlan);
            $arrayRegistrosVlan [] = $arrayVlanNetvoice;
        }
        
        $arrayResultadoVlan ["data"]  = $arrayRegistrosVlan;
        $arrayResultadoVlan ["total"] = count($arrayRegistrosVlan);
        $objResponse->setContent(json_encode($arrayResultadoVlan));

        return $objResponse;
    }
    
    /**
     * Documentacion para el método 'getVlansMigracionManual'
     *
     * Funcion que retorna las vlans reservadas para la herramienta de mirgación de vlan
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 28-08-2019
     *
     * @param array $arrayParametros [ 'intPersonaEmpresaRol' => id persona empresa rol del cliente en sesion
     *                                 'intAnillo'            => numero del anillo
     *                                 'strNombreElemento'    => nombre del Pe
     *                                 'intIdVrf'             => id de la vrf ]
     *
     * @return array $arrayResultado
     */
    public function getVlansMigracionManual($arrayParametros)
    {
        $emComercial                                   = $this->getDoctrine()->getManager("telconet");
        $arrayParametrosVlan                           = array();
        $arrayResultado                                = array();

        $arrayParametrosVlan['intIdPersonaEmpresaRol'] = $arrayParametros["intPersonaEmpresaRol"];
        $arrayParametrosVlan['intAnillo']              = $arrayParametros["intAnillo"];
        $arrayParametrosVlan['strNombre']              = $arrayParametros["strNombreElemento"];
        $arrayParametrosVlan['intVlan']                = '';
        $arrayParametrosVlan['strCaractVlan']          = 'VLAN';
        $arrayParametrosVlan['intIdVrf']               = $arrayParametros["intIdVrf"];
        $arrayParametrosVlan['intStart']               = '';
        $arrayParametrosVlan['intLimit']               = '';

        $arrayResultado = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')->getVlansMigracionManual($arrayParametrosVlan);

        return $arrayResultado;
    }

    /**
     * validaVlanTelefonia
     * 
     * Funcion que verifica si el servicio es CANAL TELEFONIA y asigna la VLAN que le corresponde según lo indicado por el dept de networking
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 30-08-2016
     * 
     * 
     * @param intIdPersonaEmpresaRol   identificador del rol de la persona en sesion del sistema
     * @param intIdServicio            identificador del id servicio
     * @param intAnillo                id de anillo del elemento del cual se recuperar información
     * @param strNombreElemento        nombre del elemento del cual se recuperara información
     * @param strUser                  usuario que ejecuta
     * @param strIp                    ip desde donde se ejecuta
     *
     * @return $arrayResult array [vlan, id_elemento, elemento, fe_creacion, usr_creacion]
     */
  
    
    public function validaVlanTelefonia($arrayParam)
    {
        
        $intIdPersonaEmpresaRol = $arrayParam['intIdPersonaEmpresaRol'];
        $intIdServicio          = $arrayParam['intIdServicio'];
        $intAnillo              = $arrayParam['intAnillo'];
        $strNombreElemento      = $arrayParam['strNombreElemento'];
        $strUser                = $arrayParam['strUser'];
        $strIp                  = $arrayParam['strIp'];
        
        $emComercial            = $this->getDoctrine()->getManager('telconet');
        
        $arrayVlan = '';
        
        if($intIdServicio > 0)
        {
            $objServicio = $emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);


            if(is_object($objServicio) && $objServicio->getDescripcionPresentaFactura() == 'CANAL TELEFONIA')
            {
                switch($intAnillo)
                {
                    case '1':
                        $strVlan = '1949';
                        break;

                    case '2':
                        $strVlan = '1959';
                        break;

                    case '3':
                        $strVlan = '1969';
                        break;

                    case '4':
                        $strVlan = '1979';
                        break;

                    default:
                        $strVlan = '75';
                        break;
                }

                $objCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                 ->findOneBy(array('descripcionCaracteristica'=>'VLAN',
                                                                   'estado' => 'Activo')); 
                if(is_object($objCaracteristica))
                {
                    $objElemento = $emComercial->getRepository('schemaBundle:InfoElemento')
                                               ->findOneBy(array('nombreElemento'   => $strNombreElemento,
                                                                 'estado'           =>  'Activo'));                        

                    if(is_object($objElemento))
                    {
                        //primero consulto si la vlan no esta ingresada
                        $objDetalleElemento = $emComercial->getRepository('schemaBundle:InfoDetalleElemento')
                                                          ->findOneBy(array('elementoId'       =>  $objElemento->getId(),
                                                                            'detalleValor'     =>  $strVlan,
                                                                            'detalleNombre'    =>  'VLAN',
                                                                            'estado'           =>  'Activo'));

                        if(is_object($objDetalleElemento))
                        {
                            //primero consulto si la vlan no esta ingresada
                            $objPerc = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                   ->findOneBy(array('personaEmpresaRolId' =>  $intIdPersonaEmpresaRol,
                                                                     'caracteristicaId'    =>  $objCaracteristica->getId(),
                                                                     'valor'               =>  $objDetalleElemento->getId(),
                                                                     'estado'              =>  'Activo'));

                            if(!is_object($objPerc))
                            {

                                $objInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                        ->find($intIdPersonaEmpresaRol);

                                if(is_object($objInfoPersonaEmpresaRol))
                                {
                                    //ingreso la vlan en la infopersonaempresarol
                                    $objPersonaEmpresaRolCaracNew = new InfoPersonaEmpresaRolCarac();
                                    $objPersonaEmpresaRolCaracNew->setEstado('Activo');
                                    $objPersonaEmpresaRolCaracNew->setFeCreacion(new \DateTime('now'));
                                    $objPersonaEmpresaRolCaracNew->setIpCreacion($strIp);
                                    $objPersonaEmpresaRolCaracNew->setUsrCreacion($strUser);
                                    $objPersonaEmpresaRolCaracNew->setPersonaEmpresaRolId($objInfoPersonaEmpresaRol);
                                    $objPersonaEmpresaRolCaracNew->setCaracteristicaId($objCaracteristica);
                                    $objPersonaEmpresaRolCaracNew->setValor($objDetalleElemento->getId());
                                    $emComercial->persist($objPersonaEmpresaRolCaracNew);
                                    $emComercial->flush();

                                    $arrayVlan['id']            = $objPersonaEmpresaRolCaracNew->getId();
                                    $arrayVlan['vlan']          = $strVlan;
                                    $arrayVlan['id_elemento']   = $objElemento->getId();
                                    $arrayVlan['elemento']      = $strNombreElemento;
                                    $arrayVlan['fe_creacion']   = $objPersonaEmpresaRolCaracNew->getFeCreacion();
                                    $arrayVlan['usr_creacion']  = $objPersonaEmpresaRolCaracNew->getUsrCreacion();


                                }
                            }
                            else
                            {
                                $arrayVlan['id']            = $objPerc->getId();
                                $arrayVlan['vlan']          = $strVlan;
                                $arrayVlan['id_elemento']   = $objElemento->getId();
                                $arrayVlan['elemento']      = $strNombreElemento;
                                $arrayVlan['fe_creacion']   = $objPerc->getFeCreacion();
                                $arrayVlan['usr_creacion']  = $objPerc->getUsrCreacion();
                            }
                        }
                    }    
                }
            }
            
        }        
        return $arrayVlan;
    }

    /**
     * Documentacion para el método 'ajaxGetVrfsDisponiblesAction'
     * 
     * Funcion ajax que sirve para obtener las Vrfs de un cliente por Vlan
     * 
     * 
     * @author Kenneth Jimenez <kjimenez@telconet.ec>
     * @version 1.0 28-03-2016
     *
     * @author Duval Medina C. <dmedina@telconet.ec>
     * @version 1.1 2016-08-22 - Incluir el codigo de empresoa en la llamda de la función
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 26-08-2019 - Por proyecto migracion de vlan se agrega parametro 'strMigracionVlan' que permite identificar si se debe retornar
     *                           solo las vrf mapeadas Nedetel
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.3 10-09-2019 - Se realiza cambio en caliente, se define un valor por default al parametro: 'migracionVlan'
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.4 10-05-2021 - Se configura un solo arreglo de parámetros para el método y se agrega el filtro de las vpn para la red GPON.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.5 11-05-2022 - Se agrega el id del servicio para la validación de vrf de cámaras.
     *
     * @return $respuesta JsonResponse
     */
    public function ajaxGetVrfsDisponiblesAction()
    {
        $response       = new JsonResponse();

        $request          = $this->get('request');
        $session          = $this->get('session');
        $strEmpresaCod    = $session->get('idEmpresa');
        $strMigracionVlan = $request->get('migracionVlan')?$request->get('migracionVlan'):"N";

        $idPersonaEmpresaRol = $request->get('idPersonaEmpresaRol');
        $idVlan              = $request->get('idVlan');
        $strTipoRed          = $request->get('strTipoRed') ? $request->get('strTipoRed') : "MPLS";
        $intIdServicio       = $request->get('idServicio') ? $request->get('idServicio') : null;

        $arrayParametros     = array(
            "idPersonaEmpresaRol" => $idPersonaEmpresaRol,
            "idVlan"              => $idVlan,
            "strEmpresaCod"       => $strEmpresaCod,
            "strMigracionVlan"    => $strMigracionVlan,
            "strTipoRed"          => $strTipoRed,
            "intIdServicio"       => $intIdServicio,
        );
        $objResult      = $this->getDoctrine()
                               ->getManager()
                               ->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                               ->getJsonVrfsClientePorVlan($arrayParametros);
        
        $response->setContent($objResult);
        return $response;
    }
    
    /**
     * Funcion ajax que sirve para obtener los puertos y los hilos
     * disponibles donde se va a instalar el servicio del cliente
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 17-12-2015
     * 
     * @author Jesus Bozada   <jbozada@telconet.ec>
     * @version 1.1 05-07-2016   Se agregan estado de servicios de los cuales se puede tomar la información tecnica para
     *                           asignar factibilidad a los servicios
     * 
     * @author Jesus Bozada   <jbozada@telconet.ec>
     * @version 1.2 25-11-2016   Se agrega filtro de tipo de enlace a buscar en hilos disponibles del punto
     * 
     * @author Jesus Bozada   <jbozada@telconet.ec>
     * @version 1.3 25-11-2016   Se agrega filtro de estado de enlace a buscar en hilos disponibles del punto
     * 
     * @param $peticion ['idElemento','estadoInterface','estadoInterfaceNotConect']
     * @return $respuesta Response
     */
    public function ajaxGetHilosDisponiblesAction()
    {
        $respuesta          = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
                
        $peticion                       = $this->get('request');
        $session                        = $peticion->getSession();
        $emInfraestructura              = $this->getDoctrine()->getManager("telconet_infraestructura");
        $intIdElemento                  = $peticion->get('idElemento');
        $strEstadoInterface             = $peticion->get('estadoInterface');
        $strEstadoInterfaceNotConect    = $peticion->get('estadoInterfaceNotConect');
        $strEstadoInterfaceReserved     = $peticion->get('estadoInterfaceReserved');
        $strBuscaHilosServicios         = $peticion->get('strBuscaHilosServicios');
        $intIdPunto                     = $peticion->get('intIdPunto');
        $strTipoEnlace                  = $peticion->get('strTipoEnlace');
        $strEmpresaCod                  = $session->get('idEmpresa');
        $arrayEstados                   = array($strEstadoInterfaceNotConect, $strEstadoInterfaceReserved);
        
        $arrParametros      = array (
                                        'idElemento'                => $intIdElemento,
                                        'estadosInterfaces'         => $arrayEstados,
                                        'estadoInterfaceConect'     => $strEstadoInterface,
                                        'empresaCod'                => $strEmpresaCod
                                    );
        
        $arrResultado       = $emInfraestructura->getRepository('schemaBundle:InfoServicioTecnico')->getHilosDisponibles($arrParametros);

        //Entra para buscar los hilos asignados de los servicios de punto
        if("BUSCA_HILOS_SERVICIOS" === $strBuscaHilosServicios)
        {   //Envia a buscar siempre que se tenga el id del punto
            if($intIdPunto)
            {
                $arrayParametrosHilos                              = array();
                $arrayParametrosHilos['arrayServicio']             = ['arrayEstado'            => ['EnPruebas', 
                                                                                                   'Activo', 
                                                                                                   'In-Corte', 
                                                                                                   'PrePlanificada', 
                                                                                                   'Factible',
                                                                                                   'AsignadoTarea',
                                                                                                   'Asignada',
                                                                                                   'Planificada',
                                                                                                   'Replanificada',
                                                                                                   'Detenido'],
                                                                      'arrayEstadoComparador'  => 'IN'];
                $arrayParametrosHilos['arrayPunto']['arrayPunto']           = [$intIdPunto];
                //se agrega filtros de estados de enlaces para realizar busquedas de hilos de servicios del punto
                $arrayParametrosHilos['arrayEnlace']['arrayEstado']         = ['Activo'];
                $arrayParametrosHilos['arrayEnlaceIni']['arrayEstado']      = ['Activo'];
                //se agrega filtro de tipo de enlace para realizar busquedas de hilos de servicios del punto
                $arrayParametrosHilos['arrayTipoEnlace']['arrayTipoEnlace'] = [$strTipoEnlace];
                $objJsonHilosServicios = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                           ->getHilosServicios($arrayParametrosHilos);
                $arrayResultHilosServicios = array();
                //Itera los registros
                foreach($objJsonHilosServicios->getRegistros() as $arrayHilosServicios):
                    $arrayResultHilosServicios[] = array('idInterfaceElemento'          => $arrayHilosServicios['intIdInterfaceElementoPadre'],
                                                         'nombreInterfaceElemento'      => 'NONE',
                                                         'idHilo'                       => $arrayHilosServicios['intIdHilo'],
                                                         'colorHilo'                    => $arrayHilosServicios['strColorHilo'], 
                                                         'numeroHilo'                   => $arrayHilosServicios['intNumeroHilo'],
                                                         'numeroColorHilo'              => $arrayHilosServicios['intNumeroHilo']
                                                                                           . ' - ' . $arrayHilosServicios['strColorHilo'] . ' *',
                                                         'idInterfaceElementoOut'       => $arrayHilosServicios['intIdInterfaceElemento'],
                                                         'nombreInterfaceElementoOut'   => $arrayHilosServicios['strNombreInterfaceElemento']);
                endforeach;
                $arrResultado = array_merge($arrResultado, $arrayResultHilosServicios);
            }
        }
        $objJson            = json_encode($arrResultado);
        $resultado          = '{"encontrados":'.$objJson.'}';
        
        $respuesta->setContent($resultado);
        
        return $respuesta;
    }
    
    /**
     * Funcion ajax que sirve para obtener el puerto de un elemento por hilo
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 4-04-2016
     * @param $peticion ['idHilo','idInterfaceElementoConector']
     * @return $respuesta Response
     */
    public function ajaxGetPuertoSwitchByHiloAction()
    {
        $respuesta          = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
                
        $peticion                       = $this->get('request');
        $session                        = $peticion->getSession();
        $intIdInterfaceElementoConector = $peticion->get('idInterfaceElementoConector');
        $strEmpresaCod                  = $session->get('idEmpresa');
        $arrParametros      = array (
                                        'idInterfaceElementoConector'   => $intIdInterfaceElementoConector,
                                        'empresaCod'                    => $strEmpresaCod
                                    );
        
        $jsonResultado       = $this->getDoctrine()
                                   ->getManager("telconet_infraestructura")
                                   ->getRepository('schemaBundle:InfoServicioTecnico')
                                   ->getJsonPuertoSwitchByHilo($arrParametros);
        $respuesta->setContent($jsonResultado);
        return $respuesta;
    }
    
    /**
     * Funcion ajax que sirve para obtener el puerto de un elemento por hilo
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 22-06-2016
     * @param $peticion ['idInterfaceElementoConector']
     * @return $respuesta Response
     */
    public function ajaxJsonPuertoOdfByHiloAction()
    {
        $respuesta          = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
                
        $peticion                       = $this->get('request');
        $session                        = $peticion->getSession();
        $intIdInterfaceElementoConector = $peticion->get('idInterfaceElementoConector');
        $strEmpresaCod                  = $session->get('idEmpresa');
        $arrParametros      = array (
                                        'idInterfaceElementoConector'   => $intIdInterfaceElementoConector,
                                        'empresaCod'                    => $strEmpresaCod
                                    );
        
        $jsonResultado       = $this->getDoctrine()
                                    ->getManager("telconet_infraestructura")
                                    ->getRepository('schemaBundle:InfoServicioTecnico')
                                    ->getJsonPuertoOdfByHilo($arrParametros);
        $respuesta->setContent($jsonResultado);
        return $respuesta;
    }    
    
    /**
     * Funcion ajax que sirve para asignar los recursos de red para el producto
     * Tunel Ip
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 18-12-2015
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 23-05-2016     Se agregan filtro para soportar servicios UM Radio
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.2 28-06-2016     Se agregan filtro para soportar Cambio de Ultima Milla
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.3 20-04-2017     Se agrega variable referencia a si se trata de Servicio bajo esquema de PseudoPe
     */
    public function ajaxAsignarRecursosRedInternetDedicadoAction()
    {
        $respuesta = new Response();
        
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        $session  = $peticion->getSession();
        
        $arrayParametros = array(
                                    'idServicio'                => $peticion->get('idServicio'),
                                    'idDetalleSolicitud'        => $peticion->get('idDetalleSolicitud'),
                                    'tipoSolicitud'             => $peticion->get('tipoSolicitud')?$peticion->get('tipoSolicitud'):"",
                                    'hiloDisponible'            => $peticion->get('hiloDisponible'),
                                    'vlan'                      => $peticion->get('vlan'),
                                    'ipPublica'                 => $peticion->get('ipPublica'),
                                    'mascaraPublica'            => $peticion->get('mascaraPublica'),
                                    'idInterfaceElemento'       => $peticion->get('idInterfaceElemento'),
                                    'idElementoPadre'           => $peticion->get('idElementoPadre'),
                                    'ultimaMilla'               => $peticion->get('ultimaMilla'),
                                    'empresaCod'                => $session->get('idEmpresa'),
                                    'usrCreacion'               => $session->get('user'),
                                    'ipCreacion'                => $peticion->getClientIp(),
                                    'esPseudoPe'                => $peticion->get('esPseudoPe')=='S'?true:false
                                );
        
        /* @var $serviceRecursoRed RecursosDeRedService */
        $serviceRecursoRed = $this->get('planificacion.RecursosDeRed');
        
        $respuestaArray = $serviceRecursoRed->asignarRecursosRedInternetDedicado($arrayParametros);
        $mensaje        = $respuestaArray['mensaje'];
        
        $respuesta->setContent($mensaje);

        return $respuesta;
    }
    
    /**
     * 
     * Metodo encargado de asignar los recursos de red necesarios para activar servicios con Internet Dedicado para DataCenter
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 20-09-2017
     * 
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function ajaxAsignarRecursosRedInternetDCAction()
    {
        $objJsonResponse   = new JsonResponse();
        $objRequest        = $this->get('request');
        $objSession        = $objRequest->getSession();
        
        $arrayParametros = array(
                                    'intIdServicio'                => $objRequest->get('idServicio'),
                                    'intIdDetalleSolicitud'        => $objRequest->get('idDetalleSolicitud'),
                                    'strTipoSolicitud'             => $objRequest->get('tipoSolicitud'),
                                    'strUltimaMilla'               => $objRequest->get('ultimaMilla'),
                                    'jsonData'                     => $objRequest->get('jsonData'),
                                    'strTipoRecursos'              => $objRequest->get('tipoRecursos'),
                                    'empresaCod'                   => $objSession->get('idEmpresa'),
                                    'prefijoEmpresa'               => $objSession->get('prefijoEmpresa'),
                                    'strUsrCreacion'               => $objSession->get('user'),
                                    'strIpCreacion'                => $objRequest->getClientIp(),
                                    'intIdEmpresa'                 => $objSession->get('idEmpresa'),
                                    'intIdElementoPadre'           => $objRequest->get('idElementoPadre'),
                                    'strCiudad'                    => $objRequest->get('ciudad'),
                                    'strNombrePe'                  => $objRequest->get('nombrePe'),
                                    'strNombreSwitch'              => $objRequest->get('nombreSwitch'),
                                    'strNombrePuerto'              => $objRequest->get('nombrePuerto'),
                                    'intCapacidad1'                => $objRequest->get('capacidad1'),
                                    'intCapacidad2'                => $objRequest->get('capacidad2'),
                                    'strTipoSolucion'              => $objRequest->get('tipoSolucion'),
                                    'intIdPersonaRol'              => $objRequest->get('idPersonaRol')
                                );
        
        /* @var $serviceRecursoRed RecursosDeRedService */
        $serviceRecursoRed = $this->get('planificacion.RecursosDeRed');
        
        $arrayResultado    = $serviceRecursoRed->asignarRecursosRedInternetDC($arrayParametros);
        
        $objJsonResponse->setData($arrayResultado);
        
        return $objJsonResponse;
    }
    
    /**
     * 
     * Metodo encargado de asignar los recursos de red necesarios para activar servicios con Datos para DataCenter
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 18-04-2018
     * 
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function ajaxAsignarRecursosRedDatosDCAction()
    {
        $objJsonResponse   = new JsonResponse();
        $objRequest        = $this->get('request');
        $objSession        = $objRequest->getSession();
        
        $arrayParametros = array(
                                    'intIdServicio'                => $objRequest->get('idServicio'),
                                    'intIdDetalleSolicitud'        => $objRequest->get('idDetalleSolicitud'),
                                    'strTipoSolicitud'             => $objRequest->get('tipoSolicitud'),
                                    'strUltimaMilla'               => $objRequest->get('ultimaMilla'),
                                    'jsonData'                     => $objRequest->get('jsonData'),
                                    'strTipoRecursos'              => $objRequest->get('tipoRecursos'),
                                    'empresaCod'                   => $objSession->get('idEmpresa'),
                                    'prefijoEmpresa'               => $objSession->get('prefijoEmpresa'),
                                    'strUsrCreacion'               => $objSession->get('user'),
                                    'strIpCreacion'                => $objRequest->getClientIp(),
                                    'intIdEmpresa'                 => $objSession->get('idEmpresa'),
                                    'intIdElementoPadre'           => $objRequest->get('idElementoPadre'),
                                    'strCiudad'                    => $objRequest->get('ciudad'),
                                    'strNombrePe'                  => $objRequest->get('nombrePe'),
                                    'strNombreSwitch'              => $objRequest->get('nombreSwitch'),
                                    'strNombrePuerto'              => $objRequest->get('nombrePuerto'),
                                    'intCapacidad1'                => $objRequest->get('capacidad1'),
                                    'intCapacidad2'                => $objRequest->get('capacidad2'),
                                    'strTipoSolucion'              => $objRequest->get('tipoSolucion'),
                                    'intIdPersonaRol'              => $objRequest->get('idPersonaEmpresaRol')
                                );
        
        /* @var $serviceRecursoRed RecursosDeRedService */
        $serviceRecursoRed = $this->get('planificacion.RecursosDeRed');
        
        $arrayResultado    = $serviceRecursoRed->asignarRecursosRedDatosDC($arrayParametros);
        
        $objJsonResponse->setData($arrayResultado);
        
        return $objJsonResponse;
    }
    
    /**
     * Funcion ajax que sirve para asignar los recursos de red para el producto
     * Tunel Ip
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 18-12-2015
     */
    public function ajaxAsignarRecursosRedTunelAction()
    {
        $respuesta = new Response();
        
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        $session  = $peticion->getSession();
        
        $arrayParametros = array(
                                    'idServicio'        => $peticion->get('idServicio'),
                                    'idDetalleSolicitud'=> $peticion->get('idDetalleSolicitud'),
                                    'hiloDisponible'    => $peticion->get('hiloDisponible'),
                                    'vlan'              => $peticion->get('vlan'),
                                    'ipTunel'           => $peticion->get('ipTunel'),
                                    'mascaraTunel'      => $peticion->get('mascaraTunel'),
                                    'empresaCod'        => $session->get('idEmpresa'),
                                    'usrCreacion'       => $session->get('user'),
                                    'ipCreacion'        => $peticion->getClientIp()
                                );
        
        /* @var $serviceRecursoRed RecursosDeRedService */
        $serviceRecursoRed = $this->get('planificacion.RecursosDeRed');
        
        $respuestaArray = $serviceRecursoRed->asignarRecursosRedTunelIp($arrayParametros);
        $mensaje        = $respuestaArray['mensaje'];
        
        $respuesta->setContent($mensaje);

        return $respuesta;
    }
    
    /**
     * Documentacion para el método 'ajaxAsignarRecursosL3mplsAction'
     * 
     * Funcion ajax que sirve para asignar los recursos de red para un servicio L3mpls
     * 
     * 
     * @author Kenneth Jimenez <kjimenez@telconet.ec>
     * @version 1.0 28-03-2016
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 23-05-2016     Se agregan filtro para soportar servicios UM Radio
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.2 28-06-2016     Se agregan filtro para soportar Cambio de Ultima Milla
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.3 16-11-2016     Se agrega informacion adicional de pseudoPe para asiganr recursos de red bajo el escenario indicado
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.4 20-06-2017     Se agrega informacion necesaria para asignar red a servicios VSAT
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.5 22-02-2018     Se envia informacion de oficina al arreglo para asignacion de recursos de red
     * 
     * @author Joel Muñoz M <jrmunoz@telconet.ec>
     * @version 1.6 12-03-2018     Se agrega parámetro para migración SDWAN en servicios L3MPLS o INTERNET MPLS/DEDICADO
     *
     * @return $respuesta JsonResponse
     */
    public function ajaxAsignarRecursosL3mplsAction()
    {
        $respuesta = new JsonResponse();
        
        $request = $this->get('request');
        $session  = $request->getSession();
        
        $arrayParametros = array(
                                    'idServicio'          => $request->get('idServicio'),
                                    'idDetalleSolicitud'  => $request->get('idDetalleSolicitud'),
                                    'tipoSolicitud'       => $request->get('tipoSolicitud')?$request->get('tipoSolicitud'):"",
                                    'idElementoPadre'     => $request->get('idElementoPadre'),
                                    'hilo'                => $request->get('hilo'),
                                    'vlan'                => $request->get('vlan'),
                                    'vrf'                 => $request->get('vrf'),
                                    'protocolo'           => $request->get('protocolo'),
                                    'defaultGateway'      => $request->get('defaultGateway'),
                                    'asPrivado'           => $request->get('asPrivado'),
                                    'mascara'             => $request->get('mascara'),
                                    'personaEmpresaRolId' => $request->get('idPersonaEmpresaRol'),
                                    'idSubred'            => $request->get('idSubred'),
                                    'flagRecursos'        => $request->get('flagRecursos'),
                                    'ultimaMilla'         => $request->get('ultimaMilla'),
                                    'usrCreacion'         => $session->get('user'),
                                    'ipCreacion'          => $request->getClientIp(),
                                    'empresaCod'          => $session->get('idEmpresa'),
                                    'esPseudoPe'          => $request->get('esPseudoPe')?true:false,
                                    'flagTransaccion'     => true,
                                    'flagServicio'        => true,
                                    'ipLoopBack'          => $request->get('ipLoopBack'),
                                    'ipWanTelefonica'     => $request->get('ipWanTelefonica'),
                                    'mascaraCliente'      => $request->get('mascaraCliente'),
                                    'intIdOficina'        => $session->get('idOficina'),
                                    'esMigracionSDWAN'    => $request->get('migracionSDWAM')
                                );
        
        $serviceRecursoRed = $this->get('planificacion.RecursosDeRed');
        
        if($request->get('ultimaMilla') == 'SATELITAL')
        {
            $respuestaArray = $serviceRecursoRed->asignarRecursosL3mplsVsat($arrayParametros);
        }
        else
        {
            $respuestaArray = $serviceRecursoRed->asignarRecursosRedL3mpls($arrayParametros);
        }
        
        $respuesta->setContent(json_encode($respuestaArray));
        
        return $respuesta;
    }

     /**
     * eliminarRepetidosArrayDimen
     *
     * Función que valida registros repetidos en un arreglo multidimensional
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 04-01-2019
     *
     * @param $arrayParametros [ arreglo => arreglo a validar
     *                           campo   => nombre del campo id del array multidimensional ]
     *
     * @return array $arrayRespuesta
     */
    public function eliminarRepetidosArrayDimen($arrayParametros)
    {
        $arrayRespuesta = array();
        $intIdx = 0;
        $arrayTemporal = array();

        foreach($arrayParametros["arreglo"] as $idxVal)
        {
            if(!in_array($idxVal[$arrayParametros["campo"]], $arrayTemporal))
            {
                $arrayTemporal[$intIdx] = $idxVal[$arrayParametros["campo"]];
                $arrayRespuesta[$intIdx] = $idxVal;
            }
            $intIdx++;
        }
        return $arrayRespuesta;
    }

    /**
     * Funcion ajax que sirve para obtener las subredes disponibles
     * para asignar la IP al servicio del cliente
     * 
     * @author Juan Lafuente <jlafuente@telconet.ec>
     * @version 1.0 28-03-2016
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 04-01-2019, se incluye el concepto de agregar la posibilidad de obtener subredes especificas por cliente
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.2 26-04-2021, se obtienen subredes para tipo red GPON
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.3 19-07-2021 - Se valida tipo red por deafult MPLS
     *
     * @param $peticion [ idElemento , tipo , uso]
     * @return $respuesta Response
     */
    public function ajaxGetSubredDisponibleAction()
    {
        $respuesta      = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
                
        $peticion          = $this->get('request');
        $emComercial       = $this->getDoctrine()->getManager('telconet');
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emGeneral         = $this->getDoctrine()->getManager('telconet_general');
        $intIdElemento  = $peticion->get('idElemento');
        $intAnillo      = $peticion->get('anillo');
        $strTipo        = $peticion->get('tipo');
        $strUso         = $peticion->get('uso');
        $strLogin       = $peticion->get('login');
        $intIdServicio  = $peticion->get('idServicio');
        $intIdElementoOlt   = $peticion->get('idElementoOlt');
        $strTipoRed     = $peticion->get('tipoRed') ? $peticion->get('tipoRed') : "MPLS";
        $strSubredesCliente = "N";
        $intIdPersona       = "";
        $arrayResultado      = array();
        $arraySubredes      = array();

        //Obtener el id_persona, asociada al login enviado
        $objInfoPunto = $emComercial->getRepository('schemaBundle:InfoPunto')->findOneBy(array("login" => $strLogin));

        if(is_object($objInfoPunto))
        {
            $objInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                    ->find($objInfoPunto->getPersonaEmpresaRolId()->getId());

            if(is_object($objInfoPersonaEmpresaRol))
            {
                $intIdPersona = $objInfoPersonaEmpresaRol->getPersonaId()->getId();
            }
        }

        //Se valida si es un cliente registrado para obtener subredes
        $arrayValoresParametros = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->getOne("PARAMETROS PROYECTO SUBREDES BG",
                                                                                                     "INFRAESTRUCTURA",
                                                                                                     "ASIGNAR RECURSOS DE RED",
                                                                                                     "CLIENTE CONFIGURADO",
                                                                                                     '',
                                                                                                     $intIdPersona,
                                                                                                     '',
                                                                                                     '',
                                                                                                     '',
                                                                                                     '');

        if(isset($arrayValoresParametros["valor1"]) && !empty($arrayValoresParametros["valor1"]))
        {
            $strSubredesCliente = "S";
        }

        //se verifica si el servicio es tipo de red GPON
        $booleanTipoRedGpon = false;
        $arrayParVerTipoRed = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->getOne('NUEVA_RED_GPON_TN',
                                                                                                'COMERCIAL',
                                                                                                '',
                                                                                                'VERIFICAR TIPO RED',
                                                                                                'VERIFICAR_GPON',
                                                                                                $strTipoRed,
                                                                                                '',
                                                                                                '',
                                                                                                '');
        if(isset($arrayParVerTipoRed) && !empty($arrayParVerTipoRed))
        {
            $booleanTipoRedGpon = true;
        }
        if($booleanTipoRedGpon)
        {
            //seteo los parametros de las subredes y vlans
            $strIntPriGpon  = "INTPRIGPON";
            $strIntBkGpon   = "INTBKGPON";
            $arrayParametrosSubred = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->getOne('NUEVA_RED_GPON_TN',
                                                                                                    'COMERCIAL',
                                                                                                    '',
                                                                                                    'NOMBRES PARAMETROS SUBREDES Y VLANS',
                                                                                                    '',
                                                                                                    '',
                                                                                                    '',
                                                                                                    '',
                                                                                                    '');
            if(isset($arrayParametrosSubred) && !empty($arrayParametrosSubred))
            {
                $strIntPriGpon  = isset($arrayParametrosSubred['valor1']) && !empty($arrayParametrosSubred['valor1'])
                                 ? $arrayParametrosSubred['valor1'] : $strIntPriGpon;
                $strIntBkGpon   = isset($arrayParametrosSubred['valor2']) && !empty($arrayParametrosSubred['valor2'])
                                 ? $arrayParametrosSubred['valor2'] : $strIntBkGpon;
            }
            //se setea el id del elemento olt para obtener las subredes por olt
            $intIdElemento = $intIdElementoOlt;
            $intAnillo     = "";
            $strUso        = $strIntPriGpon;
            $objServicioTecnico = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')->findOneByServicioId($intIdServicio);
            if(is_object($objServicioTecnico) && $objServicioTecnico->getTipoEnlace() == 'BACKUP')
            {
                $strUso = $strIntBkGpon;
            }
        }

        $arrParametros  = array (
                                'idElemento'   => $intIdElemento,
                                'anillo'       => $intAnillo,
                                'tipo'         => $strTipo,
                                'uso'          => $strUso
                                );

        $arrayResultado = $emInfraestructura->getRepository('schemaBundle:InfoSubred')->getSubredByElementoTipoUso($arrParametros);

        if($strSubredesCliente == "S")
        {
            $arrParametros  = array (
                                    'idElemento'      => $intIdElemento,
                                    'anillo'          => $intAnillo,
                                    'tipo'            => $strTipo,
                                    'uso'             => $strUso,
                                    'subredesCliente' => $strSubredesCliente,
                                    'idPersona'       => $intIdPersona
                                    );

            $arraySubredes = $emInfraestructura->getRepository('schemaBundle:InfoSubred')->getSubredByCliente($arrParametros);

            //Mesclar subredes
            $arrayTotal = array_merge($arrayResultado, $arraySubredes);

            //Eliminar registros repetidos
            $arrayParametros["arreglo"] = $arrayTotal;
            $arrayParametros["campo"]   = "idSubred";

            $arrayTotal = $this->eliminarRepetidosArrayDimen($arrayParametros);
        }
        else
        {
            //Mesclar subredes
            $arrayTotal = array_merge($arrayResultado, $arraySubredes);
        }

        $objJson            = json_encode($arrayTotal);
        $resultado          = '{"encontrados":'.$objJson.'}';
        
        $respuesta->setContent($resultado);
        return $respuesta;
    }
    
    /**
     * Funcion ajax que sirve para asignar los recursos de red para el producto
     * Internet MPLS
     * 
     * @author Juan Lafuente <jlafuente@telconet.ec>
     * @version 1.0 28-03-2016
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 17-05-2016 - Se envían los parámetros numeroColorHiloSeleccionado,nombreElemento,nombreElementoContenedor para guardarlos en el
     *                           historial del servicio y de la solicitud
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.2 23-05-2016     Se agregan filtro para soportar servicios UM Radio
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.3 28-06-2016     Se agregan filtro para soportar Cambio de Ultima Milla
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.4 22-07-2016     Se envia flag indicando si realiza transaccion y cambia estado al servicio para poder ser activado
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.5 20-04-2017     Se envia variable determinando si el servicio se encuentra bajo esquema de pseudope
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.6 06-11-2019     Se agrega el concepto de tipo de red GPON
     */
    public function ajaxAsignarRecursosInternetMPLSAction()
    {
        $respuesta = new Response();

        $respuesta->headers->set('Content-Type', 'text/json');                
        
        $peticion = $this->get('request');
        $session  = $peticion->getSession();                        
        
        $arrayParametros = array(
                                    'idServicio'                => $peticion->get('idServicio'),
                                    'idDetalleSolicitud'        => $peticion->get('idDetalleSolicitud'),  
                                    'tipoSolicitud'             => $peticion->get('tipoSolicitud')?$peticion->get('tipoSolicitud'):"",
                                    'vrf'                       => $peticion->get('vrf'),
                                    'vlan'                      => $peticion->get('vlan'),
                                    'subred'                    => $peticion->get('subred'),
                                    'idElementoPadre'           => $peticion->get('idElementoPadre'),
                                    'tipoSubred'                => $peticion->get('tipoSubred'),
                                    'hiloSeleccionado'          => $peticion->get('hiloSeleccionado'),
                                    'nombreInterfaceElemento'   => $peticion->get('nombreInterfaceElemento'),
                                    'numeroColorHiloSeleccionado'=> $peticion->get('numeroColorHiloSeleccionado'),
                                    'nombreElemento'            => $peticion->get('nombreElemento'),
                                    'nombreElementoConector'    => $peticion->get('nombreElementoConector'),
                                    'strTipoRed'                => $peticion->get('tipoRed'),
                                    'anillo'                    => $peticion->get('anillo'),
                                    'ultimaMilla'               => $peticion->get('ultimaMilla'),
                                    //....
                                    'login'                     => $peticion->get('login'),
                                    'empresaCod'                => $session->get('idEmpresa'),
                                    'usrCreacion'               => $session->get('user'),
                                    'ipCreacion'                => $peticion->getClientIp()   ,
                                    'flagTransaccion'           => true,
                                    'flagServicio'              => true,
                                    'esPseudoPe'                => $peticion->get('esPseudoPe')=='S'?true:false,
                                    'esMigracionSDWAN'          => $peticion->get('migracionSDWAM')
                                );
                        
        /* @var $serviceRecursoRed RecursosDeRedService */
        $serviceRecursoRed = $this->get('planificacion.RecursosDeRed');
        
        $respuestaArray = $serviceRecursoRed->asignarRecursosRedInternetMPLS($arrayParametros);
        if ($respuestaArray['mensaje'] != 'OK')
        {
        $strMensaje        = $respuestaArray['mensaje'];
        $strStatus         = $respuestaArray['statusWebService'];
        $strConfirmacion         = $respuestaArray['confirmacionWs'];

        $respuesta->setContent(json_encode(array('strEstatus' => $strStatus,
                                                'strMensaje' =>  $strMensaje,
                                                'strConfirmacion' =>  $strConfirmacion)));
        }
        else if ($respuestaArray['mensaje'] === 'OK')
        {
            $mensaje        = $respuestaArray['mensaje'];
            $respuesta->setContent($mensaje);
        }
        

        return $respuesta;
    }

    /**
     * Obtener PE de Telconet Parametrizados
     * 
     * @author Josue Valencia <ajvalencia@telconet.ec>
     * @version 1.0 09-11-2022
     */
    public function ajaxGetObtenerPeAction()
    {
        $objRespuesta = new Response();

        $objRespuesta->headers->set('Content-Type', 'text/json');
        
        $objPeticion = $this->get('request');
        $objSession  = $objPeticion->getSession();
        $strEmpresaCod  = $objSession->get('idEmpresa');
        $emGeneral   = $this->getDoctrine()->getManager('telconet_general');
        $emInfraestructura= $this->getDoctrine()->getManager("telconet_infraestructura");
        
        $arrayResponse  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                              ->get('PE_TELCONET',
                                                       'TECNICO',
                                                       null,
                                                       'PE_TELCONET_ASIGNAR',
                                                       null,
                                                       null,
                                                       null,
                                                       null,
                                                       null,
                                                       $strEmpresaCod);
        $arrayPeEncontrados = array();
        
        if(count($arrayResponse)>0)
        {
            
            foreach($arrayResponse as $reg)
            {
                $arrayValidaPe = $emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                ->getValidarPeTelco($reg['valor1'],
                                                    $reg['valor3'],
                                                    $reg['valor4']);
                if($arrayValidaPe['status'] === 'OK')
                {
                    $arrayPeEncontrados[] = array('id'    => $reg['valor1'],
                                                  'valor' => $arrayValidaPe['result']);
                }
            } 
        }
        $objJson            = json_encode($arrayPeEncontrados);
        $objJsonResultado          = '{"encontrados":'.$objJson.'}';
        
        $objRespuesta->setContent($objJsonResultado);
        return $objRespuesta;
    }

    /**
     * Obtener PE de Clientes Parametrizados
     * 
     * @author Josue Valencia <ajvalencia@telconet.ec>
     * @version 1.0 09-11-2022
     */
    public function ajaxGetObtenerPeClienteAction()
    {
        $objRespuesta = new Response();

        $objRespuesta->headers->set('Content-Type', 'text/json');
        
        $objPeticion = $this->get('request');
        $objSession  = $objPeticion->getSession();
        $strEmpresaCod  = $objSession->get('idEmpresa');
        $emGeneral   = $this->getDoctrine()->getManager('telconet_general');
        $emInfraestructura= $this->getDoctrine()->getManager("telconet_infraestructura");
        
        $arrayResponse  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                              ->get('OTROS_PE',
                                                       'TECNICO',
                                                       null,
                                                       'OTROS_PE',
                                                       null,
                                                       null,
                                                       null,
                                                       null,
                                                       null,
                                                       $strEmpresaCod);
        $arrayPeEncontrados = array();
        
        if(count($arrayResponse)>0)
        {
            
            foreach($arrayResponse as $reg)
            {
                $arrayPeEncontrados[] = array('id'    => $reg['valor1'],
                                              'valor' => $reg['valor1']);
            } 
        }
        $objJson            = json_encode($arrayPeEncontrados);
        $objJsonResultado          = '{"encontrados":'.$objJson.'}';
        
        $objRespuesta->setContent($objJsonResultado);
        return $objRespuesta;
    }

    /**
     * Funcion ajax que sirve para asignar los recursos de red para el producto
     * Internet MPLS
     * 
     * @author Juan Lafuente <jlafuente@telconet.ec>
     * @version 1.0 28-03-2016
     */
    public function ajaxGetVrfInternetAction()
    {
        $respuesta = new Response();

        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        $session  = $peticion->getSession();
        
        $emGeneral   = $this->getDoctrine()->getManager('telconet_general');
        $emComercial = $this->getDoctrine()->getManager('telconet');
        
        //Se obtiene la configuracion de los paneles que se visualizaran de manera dinamica por tipo de caso escogido
        $arrayResponse = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                   ->get("VRF_INTERNET",'TECNICO',null,null,null,null,null,null);

        if(count($arrayResponse)>0)
        {
            $arrayVrfInternet = array();
            foreach($arrayResponse as $reg) {                
                $objInfoPersonaEmpresaRolCarac = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                             ->find($reg['valor1']);
                $arrayVrfInternet[] = array('id'    => $objInfoPersonaEmpresaRolCarac->getId(),
                                            'valor' => $objInfoPersonaEmpresaRolCarac->getValor());
            } 
        }
        
        $objJson            = json_encode($arrayVrfInternet);
        $resultado          = '{"encontrados":'.$objJson.'}';
        
        $respuesta->setContent($resultado);
        return $respuesta;
    }
    
    /**
     * Metodo encargado de obtener las subredes existentes para un cliente de tipo enviado como parametros
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 05-10-2017
     * 
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function ajaxGetSubredesDisponiblesInternetDCAction()
    {
        $objRequest       = $this->get('request');
        $intIdPersonaRol  = $objRequest->get('idPersonaRol');        
        $strUso           = $objRequest->get('uso');        
        $emInfraestructura= $this->getDoctrine()->getManager("telconet_infraestructura");
        
        $arrayParametros                           = array();
        $arrayParametros['intIdPersonaEmpresaRol'] = $intIdPersonaRol;
        $arrayParametros['strUso']                 = $strUso;
        $arraySubredes   = $emInfraestructura->getRepository("schemaBundle:InfoSubred")->getArraySubredesInternetDC($arrayParametros);
        
        $objJsonResponse = new JsonResponse();
        $objJsonResponse->setData($arraySubredes);
        return $objJsonResponse;
    }
    
    /**
     * 
     * Metodo encargado de validar si la subred publica para realizar el enrutamiento para internet DC esta aprobada por NW
     * para ser usada o no
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 - 06-10-2017
     * 
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function ajaxValidarSubredPublicaInternetDCAction()
    {
        $objRequest       = $this->get('request');
        $strSubred        = $objRequest->get('subred');        
        $strMascara       = $objRequest->get('mascara');
        $strLogin         = $objRequest->get('login');
        
        $serviceNetworking = $this->get('tecnico.NetworkingScripts');
        
        $arrayParametros['url']      = 'checkSubnet';
        $arrayParametros['accion']   = 'checkSubnet';
        $arrayParametros['servicio'] = 'INTERNETDC';
        $arrayParametros['subred']   = $strSubred;
        $arrayParametros['mask']     = $strMascara;
        $arrayParametros['login']    = $strLogin.'_dc';
        
        $arrayRespuesta = $serviceNetworking->callNetworkingWebService($arrayParametros);
        
        $objJsonResponse = new JsonResponse();
        $objJsonResponse->setData($arrayRespuesta);
        return $objJsonResponse;
    }
    
    /**
     * guardaRecursosDeRedInternetResidencialAction
     * 
     * Guarda la información de recursos de red del servicio Internet Residencial
     * @return response.
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 09-11-2018
     * @since 1.0
     */
    public function guardaRecursosDeRedInternetResidencialAction()
    {
        $objResponse    = new JsonResponse();
        $objRequest     = $this->get('request');
        $objSession     = $objRequest->getSession();

        /* @var $recursoRedService RecursosDeRedService */
        $serviceRecursosRed = $this->get('planificacion.RecursosDeRed');
        
        $arrayPeticiones = array(
                                    'idDetSolPlanif'        => $objRequest->get('idDetSolPlanif'),
                                    'idSplitter'            => $objRequest->get('idSplitter'),
                                    'idInterfaceSplitter'   => $objRequest->get('idInterfaceSplitter'),
                                    'marcaOlt'              => $objRequest->get('marcaOlt'),
                                    'idEmpresa'             => $objSession->get('idEmpresa'),
                                    'prefijoEmpresa'        => $objSession->get('prefijoEmpresa'),
                                    'usrCreacion'           => $objSession->get('user'),
                                    'ipCreacion'            => $objRequest->getClientIp()
                                );
        
        $arrayRespuesta = $serviceRecursosRed->asignarRecursosRedInternetResidencial($arrayPeticiones);
        $objResponse->setData($arrayRespuesta);
        return $objResponse;
    }
    
    /**
     * Documentación para el método 'guardaRecursosDeRed'.
     *
     * Guarda la información de recursos de red del servicio Internet Small Business
     * @return response.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 24-11-2017
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 24-04-2018 Se agrega el envío del nombre técnico como parámetro a la función que guarda recursos de red
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 26-06-2019 Se agrega el envío de los parámetros idOltNuevoMigracion y idInterfaceOltNuevoMigracion 
     *                          necesarios para realizar una migración de tecnología de servicios Small Business y TelcoHome
     */
    public function guardaRecursosDeRedInternetLiteAction()
    {
        $objResponse    = new JsonResponse();
        $objRequest     = $this->get('request');
        $objSession     = $objRequest->getSession();

        /* @var $recursoRedService RecursosDeRedService */
        $serviceRecursosRed = $this->get('planificacion.RecursosDeRed');
        
        $arrayPeticiones = array(
                                    'idDetSolPlanif'                => $objRequest->get('idDetSolPlanif'),
                                    'idSplitter'                    => $objRequest->get('idSplitter'),
                                    'idInterfaceSplitter'           => $objRequest->get('idInterfaceSplitter'),
                                    'datosIps'                      => $objRequest->get('datosIps'),
                                    'marcaOlt'                      => $objRequest->get('marcaOlt'),
                                    'nombreTecnico'                 => $objRequest->get('nombreTecnico'),
                                    'idOltNuevoMigracion'           => $objRequest->get('idOltNuevoMigracion'),
                                    'idInterfaceOltNuevoMigracion'  => $objRequest->get('idInterfaceOltNuevoMigracion'),
                                    'idEmpresa'                     => $objSession->get('idEmpresa'),
                                    'prefijoEmpresa'                => $objSession->get('prefijoEmpresa'),
                                    'usrCreacion'                   => $objSession->get('user'),
                                    'ipCreacion'                    => $objRequest->getClientIp()
                                );
        
        $arrayRespuesta = $serviceRecursosRed->asignarRecursosRedInternetLite($arrayPeticiones);
        $objResponse->setData($arrayRespuesta);
        return $objResponse;
    }

    /**
     * Documentación para el método 'guardaRecursosDeRedDatosGponAction'.
     *
     * Guarda la información de recursos de red del servicio Datos Safe City
     * @return response.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 26-05-2021
     */
    public function guardaRecursosDeRedDatosGponAction()
    {
        $objResponse    = new JsonResponse();
        $objRequest     = $this->get('request');
        $objSession     = $objRequest->getSession();

        $serviceRecursosRed = $this->get('planificacion.RecursosDeRed');
        $arrayPeticiones = array(
                                    'idDetalleSolicitud'  => $objRequest->get('idDetSolPlanif'),
                                    'idSplitter'          => $objRequest->get('idSplitter'),
                                    'idInterfaceSplitter' => $objRequest->get('idInterfaceSplitter'),
                                    'marcaOlt'            => $objRequest->get('marcaOlt'),
                                    'idEmpresa'           => $objSession->get('idEmpresa'),
                                    'prefijoEmpresa'      => $objSession->get('prefijoEmpresa'),
                                    'usrCreacion'         => $objSession->get('user'),
                                    'ipCreacion'          => $objRequest->getClientIp()
                                );

        $arrayRespuesta = $serviceRecursosRed->asignarRecursosRedDatosGpon($arrayPeticiones);
        $objResponse->setData($arrayRespuesta);
        return $objResponse;
    }

    /**
     * Documentación para el método 'guardaRecursosDeRedCamaraGponAction'.
     *
     * Guarda la información de recursos de red del servicio Camara Safe City
     * @return response.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 01-07-2021
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.1 05-09-2021 - Se valida los recursos de red para servicios WIFI
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.2 11-05-2022 - Se agregan parámetros para la asignación de los recursos de red para servicios Cámara
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.3 01-08-2022 - Se agregan parámetros para la asignación de los recursos de red para servicios Cámara VPN GPON
     */
    public function guardaRecursosDeRedCamaraGponAction()
    {
        $objResponse    = new JsonResponse();
        $objRequest     = $this->get('request');
        $objSession     = $objRequest->getSession();
        $serviceSoporte = $this->get('soporte.SoporteService');

        $serviceRecursosRed = $this->get('planificacion.RecursosDeRed');
        $arrayPeticiones = array(
                                    'idDetalleSolicitud'  => $objRequest->get('idDetalleSolicitud'),
                                    'idServicio'          => $objRequest->get('idServicio'),
                                    'intIdVrf'            => $objRequest->get('intIdVrf'),
                                    'intIdVlan'           => $objRequest->get('intIdVlan'),
                                    'idEmpresa'           => $objSession->get('idEmpresa'),
                                    'prefijoEmpresa'      => $objSession->get('prefijoEmpresa'),
                                    'strEsCamVpnGpon'     => $objRequest->get('strEsCamVpnGpon'),
                                    'peticion'            => $objRequest,
                                    'serviceSoporte'      => $serviceSoporte,
                                    'usrCreacion'         => $objSession->get('user'),
                                    'ipCreacion'          => $objRequest->getClientIp()
                                );

        $arrayRespuesta = $serviceRecursosRed->asignarRecursosRedServiciosGpon($arrayPeticiones);
        $objResponse->setData($arrayRespuesta);
        return $objResponse;
    }
    
    /**
     * Funcion ajax que sirve para obtener las subredes disponibles
     * para asignar la IP WAN/LAN al servicio Clear Channel Punto a Punto del cliente
     * 
     * @author Josue Valencia <ajvalencia@telconet.ec>
     * @version 1.0 22-12-2022
     *
     * @param $objPeticion [ idElemento , tipo , uso]
     * @return $objRespuesta Response
     */
    public function ajaxGetSubredDisponibleClearChannelAction()
    {
        $objRespuesta      = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
                
        $objPeticion          = $this->get('request');
        $emComercial       = $this->getDoctrine()->getManager('telconet');
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emGeneral         = $this->getDoctrine()->getManager('telconet_general');
        $intIdElemento     = $objPeticion->get('idElemento');
        $intAnillo         = $objPeticion->get('anillo');
        $strTipo           = $objPeticion->get('tipo');
        $strUso            = $objPeticion->get('uso');
        $strMascara        = $objPeticion->get('mascara');
        $strLogin          = $objPeticion->get('login');
        $intIdServicio     = $objPeticion->get('idServicio');
        $intIdElementoOlt  = $objPeticion->get('idElementoOlt');
        $strTipoRed     = $objPeticion->get('tipoRed') ? $objPeticion->get('tipoRed') : "MPLS";
        $strSubredesCliente = "N";
        $intIdPersona      = "";
        $arrayResultado    = array();
        $arraySubredes     = array();

        
        $arrayParametros  = array (
                                'idElemento'   => $intIdElemento,
                                'anillo'       => '',
                                'tipo'         => $strTipo,
                                'uso'          => $strUso,
                                'mascara'      => $strMascara,
                                );

        $arrayResultado = $emInfraestructura->getRepository('schemaBundle:InfoSubred')
                                ->getSubredByElementoTipoUso($arrayParametros);

        
        
        
        foreach($arrayResultado as $reg)
        {
            
            $arraySubredes[] = array('idSubred'    => $reg['idSubred'],
                                     'subred' => $reg['subred']);
            
        } 


        $objJson            = json_encode($arraySubredes);
        $objResultado       = '{"encontrados":'.$objJson.'}';
        
        $objRespuesta->setContent($objResultado);
        return $objRespuesta;
    }


    /**
     * Documentacion para el método 'ajaxAsignarRecursosClearChanelAction'
     * 
     * Funcion ajax que sirve para asignar los recursos de red para un servicio Clear Channel Punto a Punto
     * 
     * 
     * @author Josue Valencia <ajvalencia@telconet.ec>
     * @version 1.0 26-12-2022
     *
     * @return $respuesta JsonResponse
     */
    public function ajaxAsignarRecursosClearChannelAction()
    {
        $objResponse  = new JsonResponse();
        $objRequest = $this->get('request');
        $objSession  = $objRequest->getSession();
        
        $arrayParametros = array(
                                    'idServicio'          => $objRequest->get('idServicio'),
                                    'idDetalleSolicitud'  => $objRequest->get('idDetalleSolicitud'),
                                    'tipoSolicitud'       => $objRequest->get('tipoSolicitud'),
                                    'idElementoPadre'     => $objRequest->get('idElementoPadre'),
                                    'vlan'                => $objRequest->get('vlan'),
                                    'vrf'                 => $objRequest->get('vrf'),
                                    'protocolo'           => $objRequest->get('protocolo'),
                                    'mascaraPublica'      => $objRequest->get('mascaraPublica'),
                                    'personaEmpresaRolId' => $objRequest->get('idPersonaEmpresaRol'),
                                    'idSubred'            => $objRequest->get('idSubred'),
                                    'usrCreacion'         => $objSession->get('user'),
                                    'ipCreacion'          => $objRequest->getClientIp(),
                                    'empresaCod'          => $objSession->get('idEmpresa'),
                                    'intIdOficina'        => $objSession->get('idOficina'),
                                    'tipoIngreso'         => $objRequest->get('tipoIngreso')
                                );
        
        $serviceRecursoRed = $this->get('planificacion.RecursosDeRed');
        $arrayRespuesta = $serviceRecursoRed->asignarRecursosClearChannel($arrayParametros);
        
        $objResponse->setData($arrayRespuesta);
        return $objResponse;
    }


    /**
     * Funcion ajax que sirve para asignar los recursos de red para el producto
     * Clear Channel Punto a Punto     
     * 
     * @author Josue Valencia <ajvalencia@telconet.ec>
     * @version 1.0 15-02-2023
     */
    public function ajaxGetVrfInternetClearChannelAction()
    {
        $objRespuesta = new Response();

        $objRespuesta->headers->set('Content-Type', 'text/json');
        
        $objPeticion = $this->get('request');
        $objSession  = $objPeticion->getSession();
        
        $emGeneral   = $this->getDoctrine()->getManager('telconet_general');
        $emComercial = $this->getDoctrine()->getManager('telconet');
        
        //Se obtiene la configuracion de los paneles que se visualizaran de manera dinamica por tipo de caso escogido
        $arrayResponse = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                   ->get("VRF_INTERNET",'TECNICO',null,null,null,null,null,null);

        if(count($arrayResponse)>0)
        {
            $arrayVrfInternet = array();
            foreach($arrayResponse as $objReg)
            {                
                $objInfoPersonaEmpresaRolCarac = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                             ->find($objReg['valor1']);
                $arrayVrfInternet[] = array('id'    => $objInfoPersonaEmpresaRolCarac->getId(),
                                            'valor' => $objInfoPersonaEmpresaRolCarac->getValor());
            } 
        }
        
        $objJson            = json_encode($arrayVrfInternet);
        $objResultado          = '{"encontrados":'.$objJson.'}';
        
        $objRespuesta->setContent($objResultado);
        return $objRespuesta;
    }

}

