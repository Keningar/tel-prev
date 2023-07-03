<?php

namespace telconet\comercialBundle\Controller;

use \PHPExcel;
use \PHPExcel_Cell;
use \PHPExcel_Settings;
use \PHPExcel_IOFactory;
use \PHPExcel_Shared_Date;
use \PHPExcel_Style_NumberFormat;
use \PHPExcel_Worksheet_PageSetup;
use telconet\schemaBundle\Entity\InfoIp;
use \PHPExcel_CachedObjectStorageFactory;
use telconet\schemaBundle\Entity\InfoPunto;
use telconet\schemaBundle\Form\ClienteType;
use Symfony\Component\HttpFoundation\Request;
use telconet\schemaBundle\Entity\InfoAdendum;
use telconet\schemaBundle\Entity\InfoPersona;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Response;
use telconet\schemaBundle\Entity\InfoContrato;
use telconet\schemaBundle\Entity\InfoServicio;
use telconet\schemaBundle\Entity\AdmiFormaPago;
use telconet\schemaBundle\Entity\InfoDocumento;
use Doctrine\Common\Collections\ArrayCollection;
use telconet\schemaBundle\Entity\AdmiNumeracion;
use telconet\schemaBundle\Entity\ReturnResponse;
use telconet\schemaBundle\Form\InfoContratoType;
use telconet\schemaBundle\Form\InfoDocumentoType;
use Symfony\Component\HttpFoundation\JsonResponse;
use telconet\schemaBundle\Entity\AdmiTipoSolicitud;
use telconet\comercialBundle\Service\ClienteService;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoPersonaReferido;
use telconet\schemaBundle\Entity\InfoServicioTecnico;
use telconet\comercialBundle\Service\InfoPuntoService;
use telconet\schemaBundle\Entity\InfoDetalleSolCaract;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoProcesoMasivoCab;
use telconet\schemaBundle\Entity\InfoProcesoMasivoDet;
use telconet\schemaBundle\Entity\InfoReporteHistorial;
use telconet\schemaBundle\Entity\InfoServicioComision;
use telconet\schemaBundle\Entity\InfoContratoFormaPago;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRol;

use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoPuntoDatoAdicional;
use telconet\schemaBundle\Entity\InfoServicioProdCaract;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\comercialBundle\Service\InfoContratoService;
use telconet\schemaBundle\Form\InfoContratoFormaPagoType;
use telconet\schemaBundle\Entity\InfoPersonaFormaContacto;
use telconet\comercialBundle\Service\ConsumoKonibitService;
use telconet\schemaBundle\Entity\InfoContratoDatoAdicional;
use telconet\schemaBundle\Entity\InfoServicioComisionHisto;
use telconet\comercialBundle\Controller\InfoPuntoController;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolCarac;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolHisto;
use telconet\schemaBundle\Form\InfoContratoDatoAdicionalType;
use telconet\schemaBundle\Form\InfoContratoFormaPagoEditType;
use telconet\tecnicoBundle\Service\InfoServicioTecnicoService;
use telconet\financieroBundle\Controller\InfoPagoCabController;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
//

/**
 * InfoPersona controller.
 */
class ClienteController extends Controller implements TokenAuthenticatedController{

    /**
    * @Secure(roles="ROLE_8-1")
    */ 
    public function indexAction() {
       
        
        $em = $this->getDoctrine()->getManager('telconet');
        $request = $this->get('request');
        $session = $request->getSession();
		
        $em_seguridad = $this->getDoctrine()->getManager("telconet_seguridad");       
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("8", "1");    	
		$session->set('menu_modulo_activo', $entityItemMenu->getNombreItemMenu());
		$session->set('nombre_menu_modulo_activo', $entityItemMenu->getTitleHtml());
		$session->set('id_menu_modulo_activo', $entityItemMenu->getId());
		$session->set('imagen_menu_modulo_activo', $entityItemMenu->getUrlImagen());
		
        //$session->set('menu_modulo_activo',"prospectos");

        /* Para la carga de la imagen desde el default controller */
        //$em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        //$adminController = new DefaultController();
        //$img_opcion = $adminController->getImgOpcion($em_seguridad,'COM-PROS');

        /* Presentar acciones relacionada */
        //$acc_relacionadas=$adminController->getAccionesRelacionadas($em_seguridad,'COMPROS','index');

        return $this->render('comercialBundle:cliente:index.html.twig', array(
            'item' => $entityItemMenu            
                        //'img_opcion_menu'=>$img_opcion,
                        //'acc_relaciondas'=>$acc_relacionadas,
                ));
    }

    /**
    * @Secure(roles="ROLE_8-7")
    *
    * Documentacion para el método 'gridAction'
    * 
    * @author Unknow
    * @version 1.0 Unknow
    * 
    * @author : Anabelle Penaherrera <apenaherrera@telconet.ec>
    * @version 1.1 15-07-2016 Se agrega campo tipo_tributario, representante_legal al arreglo.
    *
    * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
    * @version 1.2 08-08-2016  Se agrega campo Oficina de Facturacion al arreglo.
    * 
    * @author : Edgar Holguin <eholguin@telconet.ec>
    * @version 1.3 21-10-2016 Se realiza cambio para quela consulta de clientes se realice a través de la llamada a una función que recibe un arreglo
    *                         con todos lo parámetros necesarios.
    * 
    * @author : Kevin Baque <kbaque@telconet.ec>
    * @version 1.4 22-11-2018 Se realiza cambio para quela consulta de clientes se realice a través de la persona en sesion, solo para Telconet
    *                         en caso de ser asistente solo tendrá acceso a los clientes de los vendedores asignados al asistente
    *                         en caso de ser vendedor solo tendrá acceso a sus clientes
    *                         en caso de ser subgerente solo tendrá acceso a clientes de los vendedores que reportan al subgerente
    *                         en caso de ser gerente u otro cargo no aplican los cambios
    *
    * @author Kevin Baque Puya <kbaque@telconet.ec>
    * @version 1.5 11-11-2020 - Se agrega campo Saldo Pendiente, solo para TN.
    *
    * @author Emilio Flores <eaflores@telconet.ec>
    * @version 1.6 19-04-2023 -Se parametriza el limite de dias por defecto en la bandeja comercial cliente.
    *
    */     
    public function gridAction() {
        $objRequest        = $this->getRequest();
        $objSession        = $objRequest->getSession();
        $strUsrCreacion    = $objSession->get('user');
        $dateFechaDesde    = explode('T', $objRequest->get("fechaDesde"));
        $dateFechaHasta    = explode('T', $objRequest->get("fechaHasta"));
        $strEstado         = $objRequest->get("estado");
        $strNombre         = $objRequest->get("nombre");
        $strApellido       = $objRequest->get("apellido");
        $strRazonSocial    = $objRequest->get("razonSocial");
        $intLimit          = $objRequest->get("limit");
        $intStart          = $objRequest->get("start");
        $intPage           = $objRequest->get("page");
        $strIdentificacion = $objRequest->get("identificacion");
        $strAccionBuscar   = $objRequest->get("accionBuscar");
        $intIdEmpresa      = $objRequest->getSession()->get('idEmpresa');
        $em                = $this->get('doctrine')->getManager('telconet');
        $strTipoPersonal   = 'Otros';
        $strPrefijoEmpresa = $objSession->get('prefijoEmpresa');
        $intIdPersonEmpresaRol = $objSession->get('idPersonaEmpresaRol');
        $strModulo         = 'Cliente';
        $emGeneral         = $this->getDoctrine()->getManager('telconet_general');

        $arrayAdmiParametroDet  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->getOne('N_DIAS_PRECEDENTES',
                                                     'COMERCIAL',
                                                     '',
                                                     "",
                                                     "",
                                                     "",
                                                     "",
                                                     "",
                                                     "",
                                                     "");
        if(isset($arrayAdmiParametroDet) && !empty($arrayAdmiParametroDet))
        {
            $strDiasRecuperados = $arrayAdmiParametroDet["valor1"];
        }
        /**
         * BLOQUE QUE VALIDA LA CARACTERISTICA 'CARGO_GRUPO_ROLES_PERSONAL','ASISTENTE_POR_CARGO' ASOCIADA A EL USUARIO LOGONEADO 
         */
        $arrayResultadoCaracteristicas = $em->getRepository('schemaBundle:InfoPersona')->getCargosPersonas($strUsrCreacion);
        if( !empty($arrayResultadoCaracteristicas) )
        {
            $arrayResultadoCaracteristicas = $arrayResultadoCaracteristicas[0];
            $strTipoPersonal               = $arrayResultadoCaracteristicas['STRCARGOPERSONAL'] ? $arrayResultadoCaracteristicas['STRCARGOPERSONAL'] : 'Otros';
        }
        $arrayParametros   = array();
        $arrayParametros['estado']         = $strEstado;
        $arrayParametros['idEmpresa']      = $intIdEmpresa;
        $arrayParametros['fechaDesde']     = $dateFechaDesde[0];
        $arrayParametros['fechaHasta']     = $dateFechaHasta[0];
        $arrayParametros['nombre']         = $strNombre;
        $arrayParametros['apellido']       = $strApellido;
        $arrayParametros['razon_social']   = $strRazonSocial;
        $arrayParametros['identificacion'] = $strIdentificacion;
        $arrayParametros['limit']          = $intLimit;
        $arrayParametros['page']           = $intPage;
        $arrayParametros['start']          = $intStart;
        $arrayParametros['tipo_persona']   = 'Cliente';
        $arrayParametros['usuario']        = '';
        $arrayParametros['strModulo']             = $strModulo;
        $arrayParametros['strPrefijoEmpresa']     = $strPrefijoEmpresa;
        $arrayParametros['strTipoPersonal']       = $strTipoPersonal;
        $arrayParametros['intIdPersonEmpresaRol'] = $intIdPersonEmpresaRol;
        if($strAccionBuscar != 'S')
        {
            $arrayParametros['strLimiteDias']     = $strDiasRecuperados;
        }
        $arrayResultado  = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findPersonasPorCriterios($arrayParametros);
        $arrayRegistros   = $arrayResultado['registros'];
        $intTotal         = $arrayResultado['total'];
        $intIdOficina     = 0;
        $strNombreOficina = '';          
        
        $arrayClientes = array();
        $intFila       = 1;
        foreach ($arrayRegistros as $arrayDatos):
            $strClase='';
            if($intFila % 2 == 0)
            {
                $strClase='k-alt';
            }
            
            $intPersonaId    = $arrayDatos['persona_id'];
            $strUrlVer       = $this->generateUrl('cliente_show', array('id' => $intPersonaId,'idper' =>$arrayDatos['id'] ));
            $strUrlEditar    = $this->generateUrl('cliente_edit', array('id' => $intPersonaId));
            $strUrlEliminar  = $this->generateUrl('cliente_delete_ajax', array('id' => $intPersonaId));
            $strLinkVer      = $strUrlVer;
            $arrayFechaEmision  = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->getUltimaFacturaPorPersonaEmpresaRol($arrayDatos['id']);
            $strFechaEmision = '';
            if( !empty($arrayFechaEmision) )
            {
                $arrayFechaEmision = $arrayFechaEmision[0];
                $strFechaEmision   = $arrayFechaEmision['fechaEmision'];
            }
            $strVendAsignado = 'N';
            if( !empty($arrayDatos['vendedorasignado']) && $strTipoPersonal !='Otros' && $strTipo !='GERENTE_VENTAS' )
            {
                $strVendAsignado = 'S';
            }
            $strEstado     = $arrayDatos['estado'];	
            
            if($strEstado!="Convertido")
            {
                $strLinkEditar = $strUrlEditar;
            }
            else
            {
                $strLinkEditar ="#";
            }
            
            $strLinkEliminar = $strUrlEliminar;

            if ($arrayDatos['razon_social'])
            {
                $strNombreCliente = $arrayDatos['razon_social'];
            }else
            {
                $strNombreCliente = $arrayDatos['nombres'].' '.$arrayDatos['apellidos'];
            }

            $intIdOficina     = '';
            $strNombreOficina = '';
            if( !empty($arrayDatos['oficina_id']) )
            {
                $objInfoOficinaGrupo = $em->getRepository('schemaBundle:InfoOficinaGrupo')->find($arrayDatos['oficina_id']);
                if(is_object($objInfoOficinaGrupo))
                {
                    $intIdOficina     = $objInfoOficinaGrupo->getId();
                    $strNombreOficina = $objInfoOficinaGrupo->getNombreOficina();
                }
            }
            $floatSaldoPendiente = 0;
            if(!empty($strPrefijoEmpresa) && $strPrefijoEmpresa == "TN")
            {
                $arraySaldoPendiente = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                          ->getSaldoPorCliente(array("intIdPersonEmpresaRol" => $arrayDatos['id'],
                                                                     "strPrefijoEmpresa"     => $strPrefijoEmpresa));
                if(empty($arraySaldoPendiente["error"]) && isset($arraySaldoPendiente["floatSaldoPendiente"]) && 
                  !empty($arraySaldoPendiente["floatSaldoPendiente"]))
                {
                    $floatSaldoPendiente = $arraySaldoPendiente["floatSaldoPendiente"];
                }
            }
            $arrayClientes[]= array(
                                    'idPersona'           => $intPersonaId,
                                    'idPersonaEmpresaRol' => $arrayDatos['id'],
                                    'idOficina'           => $intIdOficina,
                                    'nombreOficina'       => $strNombreOficina,
                                    'Nombre'              => $strNombreCliente,
                                    'Direccion'           => $arrayDatos['direccion_tributaria'],
                                    'fechaCreacion'       => strval(date_format($arrayDatos['fe_creacion'],"d/m/Y G:i")),
                                    'usrVendedor'         => $arrayDatos['vendedor'],
                                    'feEmision'           => $strFechaEmision,
                                    'strSaldoPendiente'   => $floatSaldoPendiente,
                                    'strVendAsignado'     => $strVendAsignado,
                                    'strTipoPersonal'     => $strTipoPersonal ? $strTipoPersonal :'Otros',
                                    'usuarioCreacion'     => $arrayDatos['usr_creacion'],
                                    'estado'              => $strEstado,
                                    'tipoEmpresa'         => $arrayDatos['tipo_empresa'],    
                                    'linkVer'             => $strLinkVer,
                                    'linkEditar'          => $strLinkEditar,
                                    'linkEliminar'        => $strLinkEliminar,
                                    'clase'               => $strClase,
                                    'tipoTributario'      => $arrayDatos['tipo_tributario'],
                                    'representanteLegal'  => $arrayDatos['representante_legal'],
                                    'boton'               => ""                              
                                   );          
                 
            $intFila++;     
        endforeach;

        $objResponse = new Response(json_encode(array('total'=>$intTotal,'clientes'=>$arrayClientes)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
	
    public function referidosAction() {
        $em = $this->getDoctrine()->getManager('telconet');
        $request = $this->get('request');
        $session = $request->getSession();
		
        $em_seguridad = $this->getDoctrine()->getManager("telconet_seguridad");       
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("8", "1");    	
		$session->set('menu_modulo_activo', $entityItemMenu->getNombreItemMenu());
		$session->set('nombre_menu_modulo_activo', $entityItemMenu->getTitleHtml());
		$session->set('id_menu_modulo_activo', $entityItemMenu->getId());
		$session->set('imagen_menu_modulo_activo', $entityItemMenu->getUrlImagen());


        return $this->render('comercialBundle:cliente:referidos.html.twig', array(
            'item' => $entityItemMenu
                        //'img_opcion_menu'=>$img_opcion,
                        //'acc_relaciondas'=>$acc_relacionadas,
                ));
    }	

    public function gridReferidosAction() {
        $request = $this->getRequest();
        $estado = '';
        $fechaDesde = explode('T', $request->get("fechaDesde"));
        $fechaHasta = explode('T', $request->get("fechaHasta"));
        $estado = $request->get("estado");
        $nombre = $request->get("nombre");
        $apellido = $request->get("apellido");
        $razonSocial = $request->get("razonSocial");		
        $limit = $request->get("limit");
        $page = $request->get("page");
        $start = $request->get("start");
        $idEmpresa = $request->getSession()->get('idEmpresa');
        $em = $this->get('doctrine')->getManager('telconet');
        $emfn = $this->get('doctrine')->getManager('telconet_financiero');

		$resultado = $em->getRepository('schemaBundle:InfoPersona')
		->findReferidosPorCriterios($estado, $idEmpresa, '', $fechaDesde[0], $fechaHasta[0], 
		$nombre, $apellido, $razonSocial, 'Cliente', $limit, $page, $start);
		
		$datos = $resultado['registros'];
		$total = $resultado['total'];

        $i = 1;

        foreach ($datos as $datos):
		
            $entityPersonaEmpresaRol=$this->obtieneIdPersonaEmpresaRolPorIdCliente($datos['idCliente'],$idEmpresa,$em);

            $entityPersonaEmpresaRolRef=$em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($datos['id']);

            $totalPagos=0;
                           
            if ($i % 2 == 0)
                $clase = 'k-alt';
            else
                $clase = '';


            $nombre='';

			$estado='';
			//Obtiene el ultimo estado de la persona
//			echo 'per:'.$datos->getId();die;	
			$ultimoEstado = $em->getRepository('schemaBundle:InfoPersonaEmpresaRolHisto')->findUltimoEstadoPorPersonaEmpresaRol($entityPersonaEmpresaRol->getId());        
			//print_r($ultimoEstado);die;	
			$idUltimoEstado=$ultimoEstado[0]['ultimo'];
			if($idUltimoEstado)
			{
				$entityUltimoEstado=$em->getRepository('schemaBundle:InfoPersonaEmpresaRolHisto')->find($idUltimoEstado);
				$estado=$entityUltimoEstado->getEstado();
			}
			else
			{
				$estado=$datos->getEstado();
			}			
			
            if($datos['razonSocial']){
                $nombre=$datos['razonSocial'];
            }else
            {
                $nombre=$datos['nombres'].' '.$datos['apellidos'];
            }    
            
            if($entityPersonaEmpresaRolRef->getPersonaId()->getRazonSocial()){
                $nombreReferido=$entityPersonaEmpresaRolRef->getPersonaId()->getRazonSocial();
            }else
            {
                $nombreReferido=$entityPersonaEmpresaRolRef->getPersonaId()->getNombres().' '.$entityPersonaEmpresaRolRef->getPersonaId()->getApellidos();
            }            
            
            if($datos['feCreacion'])
                    $fechaCreacion=strval(date_format($entityPersonaEmpresaRolRef->getFeCreacion(), "d/m/Y G:i"));
            else
                    $fechaCreacion='';
            
                $estadoFacturaNuevoCliente="";
                $numeroFacturaNuevoCliente="";
                $valorFacturaNuevoCliente="";
                $valorDescuento=0;
                $fechaEmisionFact="";            
            $entityFacturaNuevoCliente=$emfn->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                 ->findPrimeraFacturaValidaPorPersonaEmpresaRol($entityPersonaEmpresaRolRef->getId());
            if(count($entityFacturaNuevoCliente)>0){
                //print_r($entityFacturaNuevoCliente); 
                $idDocumentoFacturaNuevoCliente=$entityFacturaNuevoCliente[0]['id'];
                $estadoFacturaNuevoCliente=$entityFacturaNuevoCliente[0]['estadoImpresionFact'];
                $numeroFacturaNuevoCliente=$entityFacturaNuevoCliente[0]['numeroFacturaSri'];
                $valorFacturaNuevoCliente=$entityFacturaNuevoCliente[0]['valorTotal'];
                $valorDescuento=$valorFacturaNuevoCliente/2; //50% del valor facturado al nuevo cliente
                $fechaEmisionFact=strval(date_format($entityFacturaNuevoCliente[0]['feEmision'], "d/m/Y G:i"));
                
                $pagos=$emfn->getRepository('schemaBundle:InfoPagoDet')->findByReferenciaId($idDocumentoFacturaNuevoCliente);
                foreach($pagos as $pago){
                   $totalPagos=$totalPagos+$pago->getValorPago();                        
                }
                
            }
            $urlVer = $this->generateUrl('cliente_show', array('id' => $datos['idCliente'],'idper'=>$entityPersonaEmpresaRol->getId()));
            $linkVer = $urlVer;            
            $arreglo[] = array(
                'idPersona' => $datos['idCliente'],
                'idPerCliNew' => $entityPersonaEmpresaRolRef->getId(),
                'idPerCli' => $entityPersonaEmpresaRol->getId(),
                'Nombre' => $nombre,
                'Direccion' => $datos['direccion'],
                'fechaCreacion' => $fechaCreacion,
                'usuarioCreacion' => $datos['usrCreacion'],
                'referido' => $nombreReferido,
                'pagos' => $totalPagos,
                'valorDescuento' =>round($valorDescuento,2),
                'feEmisionFactura' => $fechaEmisionFact, 
                'numeroFactura' => $numeroFacturaNuevoCliente,
                'estadoFactura' =>$estadoFacturaNuevoCliente,
                'estado' => $estado,
                'linkVer' => $linkVer,
                'clase' => $clase,
                'boton' => ""
            );

            $i++;
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
	
//ESTA FUNCION ES LLAMADA DESDE INGRESO y EDICION DE CLIENTE,CONTACTO y PRE-CLIENTE
    public function formasContactoGridAction() {
        $request = $this->getRequest();
        $limit = $request->get("limit");
        $page = $request->get("page");
        $start = $request->get("start");
        $personaid = $request->get("personaid");
        /* @var $serviceCliente ClienteService */
        $serviceCliente = $this->get('comercial.Cliente');
        //Cuando sea inicio puedo sacar los 30 registros
        $resultado = $serviceCliente->obtenerFormasContactoPorPersona($personaid, $limit, $page, $start);
        $arreglo = $resultado['registros'];
        if (empty($arreglo))
        {
            $arreglo = array(array());
        }
        $response = new Response(json_encode(array('total' => $resultado['total'], 'personaFormasContacto' => $arreglo)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }
    
    /* combo estado llenado ajax */
        public function estadosAction() {
        $arreglo[] = array('idEstado' => 'Activo', 'codigo' => 'ACT', 'descripcion' => 'Activo');
        $arreglo[] = array('idEstado' => 'Inactivo', 'codigo' => 'ACT', 'descripcion' => 'Inactivo');
        $arreglo[] = array('idEstado' => 'Pendiente', 'codigo' => 'ACT', 'descripcion' => 'Pendiente');
        $arreglo[] = array('idEstado' => 'Cancelado', 'codigo' => 'ACT', 'descripcion' => 'Cancelado');
		$arreglo[] = array('idEstado' => 'Cancel', 'codigo' => 'ACT', 'descripcion' => 'Cancel');		

        $response = new Response(json_encode(array('estados' => $arreglo)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }

    /* combo estado llenado ajax */
//ESTA FUNCION ES LLAMADA DESDE INGRESO y EDICION DE CLIENTE,CONTACTO y PRE-CLIENTE
    
    /**
     * formasContactoAjaxAction, obtiene las formas de contacto.
     * 
     * @author  telcos
     * @version 1.0 
     * 
     * @author  Edgar Holguín <eholguin@telconet.ec>
     * @version 1.1 Se agrega llamada a función que consulta las formas de contacto por código.
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     */
    public function formasContactoAjaxAction() {
        $objSession          = $this->get('request')->getSession();
        $strEmpresaCod       = $objSession->get('idEmpresa');
        $strPrefijoEmpresa   = $objSession->get('prefijoEmpresa');
        $serviceCliente      = $this->get('comercial.Cliente');
        $emGeneral           = $this->getDoctrine()->getManager('telconet_general');
        $objReturnResponse   = new ReturnResponse();
        
        if($strPrefijoEmpresa === 'TNG')
        {
            $arrayAdmiParametroDet  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->get('COD_FORMA_CONTACTO', 
                                                      'COMERCIAL',
                                                      'COD_FORMA_CONTACTO', 
                                                      "", 
                                                      "",
                                                      "",
                                                      "", 
                                                      "", 
                                                      "", 
                                                      $strEmpresaCod);

            if($arrayAdmiParametroDet && count($arrayAdmiParametroDet) > 0)
            {
                $arrayCodFormasContacto = array();
                
                foreach($arrayAdmiParametroDet as $arrayParametro)
                {
                    $arrayCodFormasContacto[] = $arrayParametro['valor1'];
                }
            }
            else
            {
                $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . 'Error: No existen parametros configurados.');
                $objResponse        = new Response();
                $objResponse->headers->set('Content-Type', 'text/json');                
                $objResponse->setContent(json_encode((array) $objReturnResponse));
                return $objResponse;
            }             
            
            $arrayFormasContacto = $serviceCliente->getFormasContactoByCodigo($arrayCodFormasContacto);
        }
        else
        {
            $arrayFormasContacto = $serviceCliente->obtenerFormasContacto();
        }

        $objResponse         = new Response(json_encode(array('formasContacto' => $arrayFormasContacto)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
     * @Secure(roles="ROLE_8-6")
     * 
     * Documentación para el método 'showAction'.
     *  
     * Método que muestra la informacion del cliente, lista de puntos, historial de puntos y metraje
     * 
     * @param int $id    Id de la persona
     * @param int $idPer Id de la persona empresa rol.
     * 
     * @return Render Pantalla Show del Cliente.
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 08-12-2015
     * @since 1.0
     *
     * @author Alejandro Domínguez<adominguez@telconet.ec>       
     * @version 1.2 24-03-2016
     * Verificación del Cliente VIP
     * 
     * @author Alejandro Domínguez<adominguez@telconet.ec>       
     * @version 1.3 22-05-2016
     * Se envía el parámetro $strEsPrepago(S/N) a la vista.
     * 
     * @author Alejandro Domínguez<adominguez@telconet.ec>       
     * @version 1.4 06-05-2016
     * Se agrega 
     * 
     * @author Anabelle Penaherrera<apenaherrera@telconet.ec>       
     * @version 1.5 26-07-2016
     * Se agrega parametro CONTRIBUCION_SOLIDARIA S/N
     * 
     * @author Anabelle Penaherrera<apenaherrera@telconet.ec>       
     * @version 1.6 13-09-2016
     * Se obtiene Fecha de Finalizacion del Contrato
     * 
     * @author Anabelle Peñaherrera<apenaherrera@telconet.ec>
     * @version 1.7 02-10-2017
     * Se agrega que sea visible en el Show del Cliente la informacion de su ciclo de Facturacion si la empresa en sesion es MD
     *
     * @author Jorge Guerrero<jguerrerop@telconet.ec>
     * @version 1.8 01-12-2017
     * Se agrega el parametro por empresa configurado en la admi_parametro
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.9 18-04-2018 Se agrega seteo de variable de sesión 'cicloFacturacionCliente' 
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 2.0 16-01-2020 - Se inicializa seteo de variables de sesión 'contactosCliente', 'contactosPunto','clienteContactos'.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 2.1 23-01-2020 - Se agrega la marca VIP Técnico y la opción de actualizarla con su perfil
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 2.2 09-04-2020 - Se agrega el perfil de Generar Corte Masivo TN
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 2.3 08-05-2020 - Se agrega el perfil de Generar Reactivacion Masiva TN
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 2.4 10-06-2020 - Consulta de información del representante legal para persona jurídica de megadatos
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 2.5 11-11-2020 - Se agrega validación de saldo pendiente al momento de realizar un cambio de razón social, solo para TN.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 2.5 10-12-2020 - Se cambia la validación del perfil que permite consultar, registrar y actualizar la
     *                           información del representante legal por la acción.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 2.6 09-05-2021 - Se agrega validación para visualizar si el cliente en sesión es de tipo distribuidor.
     *
     * @author Jefferson Alexy Carrillo <jacarrillo@telconet.ec>
     * @version 1.0 10-08-2022 - Se elimina consumo de representante legal por nueva implemnetacion con ms
     *      
     *
     */
    public function showAction($id, $idper)
    {
        $verHistorial   = false;
        $strNombreCiclo = '';
        if(true === $this->get('security.context')->isGranted('ROLE_265-1797'))
        {
            $verHistorial = true;
        }

        $objSession     = $this->get('request')->getSession();
        $strEmpresaCod  = $objSession->get('idEmpresa');
        $em_seguridad   = $this->getDoctrine()->getManager("telconet_seguridad");
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("8", "1");
        $objSession->set('menu_modulo_activo', $entityItemMenu->getNombreItemMenu());
        $objSession->set('nombre_menu_modulo_activo', $entityItemMenu->getTitleHtml());
        $objSession->set('id_menu_modulo_activo', $entityItemMenu->getId());
        $objSession->set('imagen_menu_modulo_activo', $entityItemMenu->getUrlImagen());

        $em             = $this->getDoctrine()->getManager();
        $request        = $this->get('request');
        $strEmpresaCod     = $request->getSession()->get('idEmpresa');
        $strPrefijoEmpresa = $request->getSession()->get('prefijoEmpresa');
        $entity         = $em->getRepository('schemaBundle:InfoPersona')->find($id);
        
        $arrayParametros['strPrefijoEmpresa'] = $strPrefijoEmpresa;
        $arrayParametros['strEmpresaCod']     = $strEmpresaCod;

        $serviceComercial   = $this->get('comercial.Comercial');
        $strAplicaCiclosFac = $serviceComercial->aplicaCicloFacturacion($arrayParametros);
        $strEsDistribuidor  = "NO";
        if(!$entity)
        {
            throw $this->createNotFoundException('Unable to find InfoPersona entity.');
        }
        $entityContrato   = $em->getRepository('schemaBundle:InfoContrato')->findOneByPersonaEmpresaRolId($idper);
        //Se Obtiene Fecha de Finalizacion del Contrato y estado del Contrato
        if($entityContrato)
        {
            $strFeFinContrato  = strval(date_format($entityContrato->getFeFinContrato(), "Y-m-d"));
            $strEstadoContrato = $entityContrato->getEstado();
        }
        else
        {
            $strFeFinContrato  = "";
            $strEstadoContrato = "";
        }
        $deleteForm       = $this->createDeleteForm($id);
        $entityPersonaRef = $em->getRepository('schemaBundle:InfoPersonaReferido')->findOneBy(array('personaEmpresaRolId' => $idper, 
                                                                                                    'estado' => 'Activo'));
        $referido = null;
        if($entityPersonaRef)
        {
            if($entityPersonaRef->getRefPersonaEmpresaRolId())
            {
                $referido = $entityPersonaRef->getRefPersonaEmpresaRolId()->getPersonaId();
            }
        }

        if($strPrefijoEmpresa == 'MD' && $entity->getTipoTributario() == 'JUR')
        {
            //Acción IngresarRepresentanteLegal.
            $strEsCoordinadorMD     = true === $this->get('security.context')->isGranted('ROLE_13-7737') ? '1' : '0';
        }

        $cliente  = $em->getRepository('schemaBundle:InfoPersona')->getPersonaParaSession2($strEmpresaCod, $idper);
        
        $cliente['esRecontratacion'] = "";
       
        $objSession->set('cliente', $cliente);
        $objSession->set('ptoCliente', '');
        $objSession->set('clienteContacto', '');
        $objSession->set('puntoContactos', '');
        $objSession->set('numServicios', '');
        $objSession->set('serviciosPunto', '');
        $objSession->set('datosFinancierosPunto', '');
        $objSession->set('cicloFacturacionCliente', '');
        $objSession->set('contactosPunto', '');
        $objSession->set('contactosCliente', '');
        $objSession->set('clienteContactos', '');
        
        $strEsVip        = '';
        $strEsVipTecnico = 'No';

        if($strPrefijoEmpresa == 'TN')
        {
            $objCaractEsDisttribuidor = $em->getRepository("schemaBundle:AdmiCaracteristica")
                                           ->findOneBy(array("descripcionCaracteristica" => 'ES_DISTRIBUIDOR',
                                                             "estado"                    => 'Activo'));
            if(is_object($objCaractEsDisttribuidor) && !empty($objCaractEsDisttribuidor))
            {
                $objEmpresaRolCaracDist = $em->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                             ->findOneBy(array('personaEmpresaRolId' => $idper,
                                                               'caracteristicaId'    => $objCaractEsDisttribuidor->getId()));
                if(is_object($objEmpresaRolCaracDist) && !empty($objEmpresaRolCaracDist))
                {
                    $strEsDistribuidor = $objEmpresaRolCaracDist->getValor();
                }
            }
            // Buscamos en InfoContratoDatoAdicional para verificar que sea cliente VIP
            $arrayParams        = array('ID_PER'  => $idper, 
                                        'EMPRESA' =>  $strEmpresaCod, 
                                        'ESTADO'  => 'Activo');
            $entityContratoDato = $em->getRepository('schemaBundle:InfoContratoDatoAdicional')->getResultadoDatoAdicionalContrato($arrayParams);
            $strEsVip           = $entityContratoDato && $entityContratoDato->getEsVip() ? 'Sí' : 'No';

            // Buscamos en InfoPersonaEmpresaRolCarac para verificar que sea cliente VIP Técnico
            $objInfoPersonaEmpRol       = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($idper);
            $objAdmiCaracteristica      = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneByDescripcionCaracteristica('ID_VIP_TECNICO');
            if( is_object($objInfoPersonaEmpRol) && is_object($objAdmiCaracteristica) )
            {
                $objInfoPerEmpRolCarac  = $em->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                    ->findOneBy(array('personaEmpresaRolId' => $objInfoPersonaEmpRol->getId(),
                                                                      'caracteristicaId'    => $objAdmiCaracteristica->getId(),
                                                                      'estado'              => 'Activo'));
                if( is_object($objInfoPerEmpRolCarac) )
                {
                    $strEsVipTecnico    = $objInfoPerEmpRolCarac->getValor();
                }
            }
        }
        
        $objSession->set('esVIP', $strEsVip);
    
        //Obtiene el ultimo estado del cliente o pre-cliente
        $datosHistorial              = $this->obtieneUltimoEstadoClientePorPersonaEmpresaRol($idper, 'Cliente', $strEmpresaCod);
        $historial                   = $datosHistorial['historial'];
        $estado                      = $datosHistorial['estado'];
        $clienteCambioRazonSocial    = null;
        $idPersonaCR                 = null;
        $idPerCR                     = null;
        $entityInfoPersonaEmpresaRol = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($idper);
        
        $oficinaFacturacion = null;
        if($strPrefijoEmpresa == 'TN')
        {
            if($entityInfoPersonaEmpresaRol->getOficinaId())
            {
                $oficinaFacturacionId = $entityInfoPersonaEmpresaRol->getOficinaId(); 
                $oficinaFacturacion   = $em->getRepository('schemaBundle:InfoOficinaGrupo')->find($oficinaFacturacionId);
            }
        }
        if ($strAplicaCiclosFac == 'S' )
        {
            $arrayParam                    = array();
            $arrayParam['intIdPersonaRol'] = $entityInfoPersonaEmpresaRol->getId();
            //Obtengo Ciclo de Facturacion asignado en el Cliente
            $arrayPersEmpRolCaracCicloCliente = $em->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                      ->getCaractCicloFacturacion($arrayParam);
            if( isset($arrayPersEmpRolCaracCicloCliente['intIdPersonaEmpresaRolCaract']) 
                        && !empty($arrayPersEmpRolCaracCicloCliente['intIdPersonaEmpresaRolCaract']) )
            {
                $strNombreCiclo = $arrayPersEmpRolCaracCicloCliente['strNombreCiclo'];
                $objSession->set('cicloFacturacionCliente', $strNombreCiclo);
            }
        }
        if($entityInfoPersonaEmpresaRol->getPersonaEmpresaRolId())
        {
            $entityInfoPersonaEmpresaRolCR = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                ->find($entityInfoPersonaEmpresaRol->getPersonaEmpresaRolId());
            if($entityInfoPersonaEmpresaRolCR->getPersonaId()->getRazonSocial())
            {
                $clienteCambioRazonSocial = $entityInfoPersonaEmpresaRolCR->getPersonaId()->getRazonSocial();
            }
            else
            {
                $clienteCambioRazonSocial = $entityInfoPersonaEmpresaRolCR->getPersonaId()->getNombres() . " " .
                    $entityInfoPersonaEmpresaRolCR->getPersonaId()->getApellidos();
            }
            $idPerCR     = $entityInfoPersonaEmpresaRol->getPersonaEmpresaRolId();
            $idPersonaCR = $entityInfoPersonaEmpresaRolCR->getPersonaId()->getId();
        }

        // Se obtiene si es prepago(S) o No (N)
        $strEsPrepago = $entityInfoPersonaEmpresaRol->getEsPrepago();
        
        // Se obtiene si cliente tiene marcado Contribucion Solidaria (S:Si o N: No)
        $strContrSolidaria = 'N';
        $objContrSolidaria = $em->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')->getOneByCaracteristica($idper,'CONTRIBUCION_SOLIDARIA');
        if($objContrSolidaria)
        {
            $strContrSolidaria = $objContrSolidaria->getValor();
        }    
        //Obtiene formas de contacto
        $formasContacto = null;
        //SE LIMITO A QUE CONSULTE MAXIMO 6 FORMAS DE CONTACTO HASTA RESOLVER EL PROBLEMA		
        $arrformasContacto = $em->getRepository('schemaBundle:InfoPersonaFormaContacto')->findPorEstadoPorPersona($id, 'Activo', 6, 1, 0);
        if($arrformasContacto['registros'])
        {
            $formasContacto = $arrformasContacto['registros'];
        }
        
        //Recorre el historial y separa en arreglos cada estado
        $i          = 0;
        $creacion   = null;
        $convertido = null;
        $eliminado  = null;
        $ultMod     = null;
        foreach($historial as $dato)
        {
            if($i == 0)
            {
                $creacion = array('estado'      => $dato->getEstado(), 
                                  'usrCreacion' => $dato->getUsrCreacion(), 
                                  'feCreacion'  => $dato->getFeCreacion(), 
                                  'ipCreacion'  => $dato->getIpCreacion());
            }
            if($i > 0)
            {
                if($dato->getEstado() == 'Eliminado')
                {
                    $eliminado = array('estado'      => $dato->getEstado(), 
                                       'usrCreacion' => $dato->getUsrCreacion(), 
                                       'feCreacion'  => $dato->getFeCreacion(), 
                                       'ipCreacion'  => $dato->getIpCreacion());
                }
                else
                {
                    $ultMod = array('estado'      => $dato->getEstado(), 
                                    'usrCreacion' => $dato->getUsrCreacion(), 
                                    'feCreacion'  => $dato->getFeCreacion(), 
                                    'ipCreacion'  => $dato->getIpCreacion());
                }
            }
            $i++;
        }

        //se agrega control de roles permitidos
        $rolesPermitidos = array();
        //MODULO 13 - COMERCIAL/PUNTO
        if(true === $this->get('security.context')->isGranted('ROLE_13-1779'))
        {
            $rolesPermitidos[] = 'ROLE_13-1779'; //ANULAR PUNTO
        }
        //3257 -> Accion Ver metraje
        if(true === $this->get('security.context')->isGranted('ROLE_13-3257'))
        {
            $rolesPermitidos[] = 'ROLE_13-3257'; //Ver Metraje
        }
        if(true === $this->get('security.context')->isGranted('ROLE_9-3757'))
        {
            $rolesPermitidos[] = 'ROLE_9-3757'; // SHOW ASIGNAR EJECUTIVO DE COBRANZAS
        }
        if(true === $this->get('security.context')->isGranted('ROLE_151-3717'))
        {
            $rolesPermitidos[] = 'ROLE_151-3717'; // ACTUALIZAR INGENIERO VIP AL CLIENTE
        }
        if(true === $this->get('security.context')->isGranted('ROLE_151-3738'))
        {
            $rolesPermitidos[] = 'ROLE_151-3738'; // SHOW ASIGNAR INGENIERO VIP AL CLIENTE
        }
        if(true === $this->get('security.context')->isGranted('ROLE_151-4138'))
        {
            $rolesPermitidos[] = 'ROLE_151-4138'; // ACTUALIZAR DATOS FACTURACION DEL CLIENTE
        }
        if(true === $this->get('security.context')->isGranted('ROLE_151-4677'))
        {
            $rolesPermitidos[] = 'ROLE_151-4677'; // ACTUALIZAR FECHA FIN DE CONTRATO DEL CLIENTE
        }
        if(true === $this->get('security.context')->isGranted('ROLE_151-7177'))
        {
            $rolesPermitidos[] = 'ROLE_151-7177'; // ACTUALIZAR LA MARCA VIP TECNICO
        }
        if(true === $this->get('security.context')->isGranted('ROLE_151-7337'))
        {
            $rolesPermitidos[] = 'ROLE_151-7337'; // GENERAR CORTE MASIVO TN
        }
        if(true === $this->get('security.context')->isGranted('ROLE_151-7357'))
        {
            $rolesPermitidos[] = 'ROLE_151-7357'; // GENERAR REACTIVACION MASIVA TN
        }
        if($this->get('security.context')->isGranted('ROLE_9-6'))
        {
            $rolesPermitidos[] = 'ROLE_9-6'; //Datos del punto
        }        
        $floatSaldoPendiente = 0;
        if(!empty($strPrefijoEmpresa) && $strPrefijoEmpresa == "TN")
        {
            $arraySaldoPendiente = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                      ->getSaldoPorCliente(array("intIdPersonEmpresaRol" => $idper,
                                                                 "strPrefijoEmpresa"     => $strPrefijoEmpresa));
            if(empty($arraySaldoPendiente["error"]) && isset($arraySaldoPendiente["floatSaldoPendiente"]) && 
              !empty($arraySaldoPendiente["floatSaldoPendiente"]))
            {
                $floatSaldoPendiente = $arraySaldoPendiente["floatSaldoPendiente"];
            }
        }
        $floatSaldoPendiente = 0;
        return $this->render('comercialBundle:cliente:show.html.twig', array('item'                           => $entityItemMenu,
                                                                             'entity'                         => $entity,
                                                                             'delete_form'                    => $deleteForm->createView(),
                                                                             'referido'                       => $referido,
                                                                             'creacion'                       => $creacion,
                                                                             'ultMod'                         => $ultMod,
                                                                             'eliminado'                      => $eliminado,
                                                                             'contrato'                       => $entityContrato,
                                                                             'estado'                         => $estado,
                                                                             'formasContacto'                 => $formasContacto,
                                                                             'idper'                          => $idper,
                                                                             'nombreClienteCambioRazonSocial' => $clienteCambioRazonSocial,
                                                                             'idPersonaCR'                    => $idPersonaCR,
                                                                             'idPerCR'                        => $idPerCR,
                                                                             'prefijoEmpresa'                 => strtoupper($strPrefijoEmpresa),
                                                                             'rolesPermitidos'                => $rolesPermitidos,
                                                                             'esVip'                          => $strEsVip,
                                                                             'esVipTecnico'                   => $strEsVipTecnico,
                                                                             'esPrepago'                      => $strEsPrepago,
                                                                             'strContrSolidaria'              => $strContrSolidaria,
                                                                             'verHistorial'                   => $verHistorial,
                                                                             'oficinaFacturacion'             => $oficinaFacturacion,
                                                                             'strFeFinContrato'               => $strFeFinContrato,
                                                                             'strEstadoContrato'              => $strEstadoContrato,
                                                                             'strNombreCiclo'                 => $strNombreCiclo,
                                                                             'esCoordinadorMD'                => $strEsCoordinadorMD,
                                                                             'floatSaldoPendiente'            => $floatSaldoPendiente,
                                                                             "strEsDistribuidor"              => $strEsDistribuidor
        ));
    }

    /**
     * getRutaGeorreferencialAction, obtiene el metraje de un punto.
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 08-12-2015
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @Secure(roles="ROLE_13-3257")
     */
    public function getRutaGeorreferencialAction()
    {
        $arrayResponse               = array();
        $objRequest                  = $this->get('request');
        $intIdPunto                  = $objRequest->get('intIdPunto');
        $objResponse                 = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');
        $arrayResponse['strStatus']  = '000';
        $arrayResponse['strMensaje'] = 'No realizo la consulta';
        $emComercial                 = $this->getDoctrine()->getManager();

        try
        {
            //Termina el metodo cuando no se envia el ID del punto.
            if(empty($intIdPunto))
            {
                $arrayResponse['strStatus'] = '001';
                $arrayResponse['strMensaje'] = 'Error, no se esta enviando el Id Punto.';
                $objResponse->setContent(json_encode($arrayResponse));
                return $objResponse;
            }

            //Busca la caracteristica
            $entityAdmiCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                    ->findOneBy(array('descripcionCaracteristica' => 'Ruta Georreferencial', 'estado' => 'Activo'));

            //Termina el metodo cuando no encuentra la entidad AdmiCaracteristica.
            if(!$entityAdmiCaracteristica)
            {
                $arrayResponse['strStatus'] = '001';
                $arrayResponse['strMensaje'] = 'Error, no existe la caracteristica [Ruta Georreferencial].';
                $objResponse->setContent(json_encode($arrayResponse));
                return $objResponse;
            }

            //Busca la caracteristica Ruta Georeferencial por punto.
            $entityInfoPuntoCaracteristica = $emComercial->getRepository('schemaBundle:InfoPuntoCaracteristica')
                                                         ->findOneBy(array('puntoId'          => $intIdPunto, 
                                                                           'caracteristicaId' => $entityAdmiCaracteristica));
            
            //Termina el metodo cuando no encuentra la entidad InfoPuntoCaracteristica.
            if(!$entityInfoPuntoCaracteristica)
            {
                $arrayResponse['strStatus'] = '001';
                $arrayResponse['strMensaje'] = 'Error, no existe [Ruta Georreferencial] para este punto.';
                $objResponse->setContent(json_encode($arrayResponse));
                return $objResponse;
            }

            //Termina el metodo si no obtiene el campo valor(el metraje).
            if(!$entityInfoPuntoCaracteristica->getValor())
            {
                $arrayResponse['strStatus'] = '001';
                $arrayResponse['strMensaje'] = 'No existen rutas para este punto.';
                $objResponse->setContent(json_encode($arrayResponse));
                return $objResponse;
            }

            //Crea un objeto a partir del string en formato json
            $objRutaGeoreferencia      = json_decode($entityInfoPuntoCaracteristica->getValor());
            $intCountPuntosGps         = 0;
            $floatDistanciaUltimoPunto = 0;
            foreach($objRutaGeoreferencia->puntosGPS as $objPuntoGPS):

                $intCountPuntosGps         = $intCountPuntosGps + 1;
                $floatDistanciaUltimoPunto = $objPuntoGPS->distanciaUltimoPunto + $floatDistanciaUltimoPunto;
                $arrayPuntosGPS[] = array(
                    'lat'                     => $objPuntoGPS->latitude,
                    'lng'                     => $objPuntoGPS->longitud,
                    'strDistanciaUltimoPunto' => $objPuntoGPS->distanciaUltimoPunto,
                    'strDescripcionMarker'    => "<table>"
                                                    . "<tr><td><b>Latitud</b></td><td><b>:</b></td><td> " 
                                                    . $objPuntoGPS->latitude . " </td></tr>"
                                                    . "<tr><td><b>Longitud</b></td><td><b>:</b></td><td>" 
                                                    . $objPuntoGPS->longitud . "</td></tr>"
                                                    . "<tr><td><b>Distancia ultimo punto</b></td><td><b>:</b></td><td>" 
                                                    . $objPuntoGPS->distanciaUltimoPunto . "</td></tr>"
                                                . "</table>"
                );
            endforeach;

            //Si es diferente de 0 trata de obtener el punto medio de la ruta
            if(0 !== $intCountPuntosGps)
            {
                $intPuntoMedio           = round(($intCountPuntosGps / 2));
                $arrayResponse['strLat'] = $arrayPuntosGPS[$intPuntoMedio]['lat'];
                $arrayResponse['strLng'] = $arrayPuntosGPS[$intPuntoMedio]['lng'];
            }
            $arrayResponse['strEstado']               = $entityInfoPuntoCaracteristica->getEstado();
            $arrayResponse['strUsrCreacion']          = $entityInfoPuntoCaracteristica->getUsrCreacion();
            $arrayResponse['strFeCreacion']           = $entityInfoPuntoCaracteristica->getFeCreacion()->format('d-m-Y h:i:s');
            $arrayResponse['strFibraInicial']         = $objRutaGeoreferencia->fibraInicial;
            $arrayResponse['strFibraFinal']           = $objRutaGeoreferencia->fibraFinal;
            $arrayResponse['strFibraGeorreferencial'] = $objRutaGeoreferencia->fibraGeorreferencial;
            $arrayResponse['strFibraManual']          = $objRutaGeoreferencia->fibraManual;
            $arrayResponse['strDistancia']            = $floatDistanciaUltimoPunto;
            $arrayResponse['jsonPuntosGPS']           = json_encode($arrayPuntosGPS);
            $arrayResponse['strStatus']               = '100';
            $arrayResponse['strMensaje']              = 'Consulta con exito';
        }
        catch(\Exception $ex)
        {
            $arrayResponse['strStatus'] = '001';
            $arrayResponse['strMensaje'] = 'Error, ' . $ex->getMessage();
        }
        $objResponse->setContent(json_encode($arrayResponse));
        return $objResponse;
    } //getRutaGeorreferencialAction

    /**
    * @Secure(roles="ROLE_8-2")
    */
    public function newAction() {
        $session = $this->get('request')->getSession();
		
        $em_seguridad = $this->getDoctrine()->getManager("telconet_seguridad");       
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("8", "1");    	
		$session->set('menu_modulo_activo', $entityItemMenu->getNombreItemMenu());
		$session->set('nombre_menu_modulo_activo', $entityItemMenu->getTitleHtml());
		$session->set('id_menu_modulo_activo', $entityItemMenu->getId());
		$session->set('imagen_menu_modulo_activo', $entityItemMenu->getUrlImagen());
		
        $request=$this->getRequest();
        $entity = new InfoPersona();
        $form = $this->createForm(new ClienteType(), $entity);
        $idEmpresa = $request->getSession()->get('idEmpresa');
        $em = $this->get('doctrine')->getManager('telconet');
        return $this->render('comercialBundle:cliente:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
                ));
    }

    /**
    * @Secure(roles="ROLE_8-3")
    */
    public function createAction(Request $request) {
        $entity = new InfoPersona();
        $form = $this->createForm(new ClienteType(), $entity);
        $datos_form = $request->request->get('clientetype');
        //print_r($datos_form['fechaNacimiento']['year']);die;
        //print_r($request->request->all()); die(); 
        //$session  = $request->getSession();
        //$user = $this->get('security.context')->getToken()->getUser();
        //$usrCreacion=$user->getUsername();
        $array_formas_contacto = explode(",", $datos_form['formas_contacto']);
        $a = 0;
        $x = 0;
        for ($i = 0; $i < count($array_formas_contacto); $i++) {
            if ($a == 3) {
                $a = 0;
                $x++;
            }
            if ($a == 1)
                $formas_contacto[$x]['formaContacto'] = $array_formas_contacto[$i];
            if ($a == 2)
                $formas_contacto[$x]['valor'] = $array_formas_contacto[$i];
            $a++;
        }
        //print_r($formas_contacto);
        //die;
        $idEmpresa = $request->getSession()->get('idEmpresa');
        $intIdOficina = $request->getSession()->get('idOficina');
        $usrCreacion = $request->getSession()->get('user');
        $em = $this->getDoctrine()->getManager('telconet');
        $em->getConnection()->beginTransaction();
        try {
            $entity->setTipoIdentificacion($datos_form['tipoIdentificacion']);
            $entity->setIdentificacionCliente($datos_form['identificacionCliente']);
            $entity->setTipoEmpresa($datos_form['tipoEmpresa']);
            $entity->setTipoTributario($datos_form['tipoTributario']);
            $entity->setNombres($datos_form['nombres']);
            $entity->setApellidos($datos_form['apellidos']);
            $entity->setRazonSocial($datos_form['razonSocial']);
            $entity->setRepresentanteLegal($datos_form['representanteLegal']);
            $entity->setNacionalidad($datos_form['nacionalidad']);
            $entity->setGenero($datos_form['genero']);
            $entity->setEstadoCivil($datos_form['estadoCivil']);
            $entity->setDireccionTributaria($datos_form['direccionTributaria']);
            $entity->setFechaNacimiento(date_create($datos_form['fechaNacimiento']['year'] . '-' . $datos_form['fechaNacimiento']['month'] . '-' . $datos_form['fechaNacimiento']['day']));
            $entityAdmiTitulo = $em->getRepository('schemaBundle:AdmiTitulo')->find($datos_form['tituloId']);
            if ($entityAdmiTitulo)
                $entity->setTituloId($entityAdmiTitulo);
            $entity->setOrigenProspecto('N');
            $entity->setFeCreacion(new \DateTime('now'));
            $entity->setUsrCreacion($usrCreacion);
            $entity->setIpCreacion($request->getClientIp());
            $entity->setEstado('Pendiente');
            $em->persist($entity);
            $em->flush();
            //ASIGNA ROL DE PRE-CLIENTE A LA PERSONA
            $entityPersonaEmpresaRol = new InfoPersonaEmpresaRol();
            $entityEmpresaRol = $em->getRepository('schemaBundle:InfoEmpresaRol')->findPorNombreTipoRolPorEmpresa('Cliente', $idEmpresa);
            $entityPersonaEmpresaRol->setEmpresaRolId($entityEmpresaRol);
            $entityPersonaEmpresaRol->setPersonaId($entity);
            $entityOficina = $em->getRepository('schemaBundle:InfoOficinaGrupo')->find($intIdOficina);
            $entityPersonaEmpresaRol->setOficinaId($entityOficina);
            $entityPersonaEmpresaRol->setFeCreacion(new \DateTime('now'));
            $entityPersonaEmpresaRol->setUsrCreacion($usrCreacion);
            $entityPersonaEmpresaRol->setEstado('Activo');
            $em->persist($entityPersonaEmpresaRol);
            $em->flush();

            //REGISTRA EN LA TABLA DE PERSONA HISTORIAL           
            $entity_persona_historial = new InfoPersonaEmpresaRolHisto();
            $entity_persona_historial->setEstado($entity->getEstado());
            $entity_persona_historial->setFeCreacion(new \DateTime('now'));
            $entity_persona_historial->setIpCreacion($request->getClientIp());
            $entity_persona_historial->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
            $entity_persona_historial->setUsrCreacion($usrCreacion);
            $em->persist($entity_persona_historial);
            $em->flush();
            //ReGISTRA LAS FORMAS DE CONTACTO DEL CLIENTE
            for ($i=0;$i < count($formas_contacto);$i++){
                $entity_persona_forma_contacto = new InfoPersonaFormaContacto();
                $entity_persona_forma_contacto->setValor($formas_contacto[$i]["valor"]);
                $entity_persona_forma_contacto->setEstado("Activo");
                $entity_persona_forma_contacto->setFeCreacion(new \DateTime('now'));
                $entityAdmiFormaContacto = $em->getRepository('schemaBundle:AdmiFormaContacto')->findPorDescripcionFormaContacto($formas_contacto[$i]["formaContacto"]);
                $entity_persona_forma_contacto->setFormaContactoId($entityAdmiFormaContacto);
                $entity_persona_forma_contacto->setIpCreacion($request->getClientIp());
                $entity_persona_forma_contacto->setPersonaId($entity);
                $entity_persona_forma_contacto->setUsrCreacion($usrCreacion);
                $em->persist($entity_persona_forma_contacto);
                $em->flush();
            }
            
            $em->getConnection()->commit();
            return $this->redirect($this->generateUrl('cliente_show', array('id' => $entity->getId())));
        } catch (\Exception $e) {
            // Rollback the failed transaction attempt
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            //aqu? alg?n mensaje con la excepci?n concatenada
            return $this->render('comercialBundle:cliente:new.html.twig', array(
                        'entity' => $entity,
                        'form' => $form->createView(),
                        //'error' => 'Ocurrio un error interno al tratar de ingresar el cliente. Por favor comuniquese con el Administrador.'
                'error' => $e->getMessage()
                    ));
        }
    }
    
    /**
     * Determina la validez de una identificacion segun su tipo
     * @since 1.0
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.1 Se agrega validación para Panamá
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.2 20-09-2017 - Se modifica la llamada al service y se llama directamente al Repositorio a validarIdentificacionTipo.
     * 
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.3 10-01-2022 - Se agrega busqueda de codEmpresa al momento de validar la identificación
     */
    public function ajaxValidarIdentificacionTipoAction()
    {
        $objRequest               = $this->getRequest();
        $strTipoIdentificacion    = trim($objRequest->request->get('tipo'));
        $strIdentificacionCliente = trim($objRequest->request->get('identificacion'));
        $intIdPais                = $objRequest->getSession()->get('intIdPais');
        $intIdEmpresa             = $objRequest->getSession()->get('idEmpresa');
        $objManager               = $this->get('doctrine')->getManager('telconet');
        $objRepositorio           = $objManager->getRepository('schemaBundle:InfoPersona');
        $arrayParamValidaIdentifica = array(
                                                'strTipoIdentificacion'     => $strTipoIdentificacion,
                                                'strIdentificacionCliente'  => $strIdentificacionCliente,
                                                'intIdPais'                 => $intIdPais,
                                                'strCodEmpresa'             => $intIdEmpresa
                                            );
        return new Response($objRepositorio->validarIdentificacionTipo($arrayParamValidaIdentifica));
    }
    
    /**
     * Documentación para la función 'ajaxValidaIdentificacionAction'.
     * @author Version Original
     * @version 1.0
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.1 11-11-2020 - Se envía el valor de saldo pendiente solo para TN.
     * 
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 1.2 29-03-2021 - Se agrega la devolución de representante legal para MD
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.2 11-05-2021 - Se valida que la identificación ingresada pertenece a un Distribuidor, solo para TN.
     *
     * @author Jefferson Alexy Carrillo <jacarrillo@telconet.ec>
     * @version 1.0 10-08-2022 - Se elimina consumo de representante legal por nueva implemnetacion con ms
     *
     */
    public function ajaxValidaIdentificacionAction()
    {
        $request = $this->getRequest();
        $identificacion = trim($request->request->get("identificacion"));
        $codEmpresa = $request->getSession()->get('idEmpresa');
        $prefijoEmpresa = $request->getSession()->get('prefijoEmpresa');
        /* @var $serviceCliente ClienteService */
        $serviceCliente = $this->get('comercial.Cliente');
        $emComercial     = $this->getDoctrine()->getManager();
        $strDistribuidor = "";
        if(!empty($prefijoEmpresa) && $prefijoEmpresa == "TN")
        {
            $arrayDistribuidor = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                             ->getDistribuidor(array("strIdentificacion" => $identificacion,
                                                                     "strPrefijoEmpresa" => $prefijoEmpresa));
            if(empty($arrayDistribuidor["error"]) && isset($arrayDistribuidor["resultado"]) && 
                !empty($arrayDistribuidor["resultado"]))
            {
                foreach($arrayDistribuidor["resultado"] as $arrayItem)
                {
                    if($arrayItem["intCantidadServ"] > 0 && $arrayItem["intCantidadSolAprobada"] == 0)
                    {
                        $strDistribuidor = (!empty($arrayItem["strDistribuidor"]) && isset($arrayItem["strDistribuidor"]))?
                                            $arrayItem["strDistribuidor"]:"";
                    }
                }
                if(!empty($strDistribuidor))
                {
                    $arrayArreglo["strDistribuidor"] = $strDistribuidor;
                    return new Response(json_encode(array($arrayArreglo)));
                }
            }
        }
        $arreglo = $serviceCliente->obtenerDatosClientePorIdentificacion($codEmpresa, $identificacion, $prefijoEmpresa);
        if (!$arreglo)
        {
            return new Response("no");
        }
        else
        {
            $floatSaldoPendiente = 0;
            if(!empty($prefijoEmpresa) && $prefijoEmpresa == "TN")
            {
                $strDescRoles    = array ('Cliente');
                $strEstados      = array ('Cancelado');
                $arrayEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                               ->buscaClientesPorIdentificacionTipoRolEmpresaEstados($identificacion,
                                                                                                     $strDescRoles,
                                                                                                     $codEmpresa,
                                                                                                     $strEstados);
                if(!empty($arrayEmpresaRol) && is_array($arrayEmpresaRol))
                {
                    foreach($arrayEmpresaRol as $arrayItem)
                    {
                        $arraySaldoPendiente = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                           ->getSaldoPorCliente(array("intIdPersonEmpresaRol" => $arrayItem->getId(),
                                                                                      "strPrefijoEmpresa"     => $prefijoEmpresa));
                        if(empty($arraySaldoPendiente["error"]) && isset($arraySaldoPendiente["floatSaldoPendiente"]) && 
                          !empty($arraySaldoPendiente["floatSaldoPendiente"]))
                        {
                            $floatSaldoPendiente += $floatSaldoPendiente + $arraySaldoPendiente["floatSaldoPendiente"];
                        }
                    }
                }
            }


            $arreglo["floatSaldoPendiente"] = $floatSaldoPendiente;
            return new Response(json_encode(array($arreglo)));
		}
    }

    /**
    * @Secure(roles="ROLE_8-4")
    * Documentación para el método 'editAction'.
    * 
    * Obtiene la informacion del Cliente y sus formas de Contacto para edicion.
    *     
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>       
    * @version 1.1 07-11-2016
    * Obtiene y muestra la informacion del Cliente (Persona) y las formas de Contacto, se valida para MD lo siguiente:
    * Debemos permitir que se pueda guardar la data editada del cliente, sin necesidad de verificar el correo_electrónico, 
    * el cual actualmente es obligatorio. 
    * Permita guardar si no existe forma de contacto de correo electrónico. Se valide si ingresan alguna forma de contacto como se esta validando
    * en la actualidad.
    * Permitir editar la Dirección Tributaria del cliente.
    * Verificar que el valor de Dirección Tributaria se replique en el campo Dirección, de no ser así, realizar la modificación.
    * Agregar acceso directo a la "Edición del Cliente", desde el Show punto Cliente. 
    *  
    * @param integer $id   //Id de la Persona    
    * @return Renders a view.
    */
    public function editAction($id) {
        $session = $this->get('request')->getSession();
		
        $em_seguridad = $this->getDoctrine()->getManager("telconet_seguridad");       
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("8", "1");    	
		$session->set('menu_modulo_activo', $entityItemMenu->getNombreItemMenu());
		$session->set('nombre_menu_modulo_activo', $entityItemMenu->getTitleHtml());
		$session->set('id_menu_modulo_activo', $entityItemMenu->getId());
		$session->set('imagen_menu_modulo_activo', $entityItemMenu->getUrlImagen());
		
        $request=$this->getRequest();
        $em = $this->getDoctrine()->getManager('telconet');
        $idEmpresa = $request->getSession()->get('idEmpresa');
        $entity = $em->getRepository('schemaBundle:InfoPersona')->find($id);
        
        $strPrefijoEmpresa = $request->getSession()->get('prefijoEmpresa');
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoPersona entity.');
        }
        $editForm = $this->createForm(new ClienteType(), $entity);
        $deleteForm = $this->createDeleteForm($id);
        return $this->render('comercialBundle:cliente:edit.html.twig',
                             array(
                                   'item'              => $entityItemMenu,
                                   'entity'            => $entity,
                                   'edit_form'         => $editForm->createView(),
                                   'delete_form'       => $deleteForm->createView(),
                                   'strPrefijoEmpresa' => $strPrefijoEmpresa
                                  ));
    }
	
    /**
    * @Secure(roles="ROLE_8-5")
    * Documentación para el método 'updateAction'.
    *
    * Guarda formulario para editar informacion de las Formas de Contacto de una persona y su ROL, genera Historial de Registro Actualizado   
    * 
    * Consideracion: Se corrige para que tome el estado de la Info_Persona_Empresa_Rol del Ultimo registro del Rol de Cliente
    * y no el estado de la Info_Persona que actualmente toma para generar el Registro del Historico.
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>       
    * @version 1.1 05-06-2015            
    * @param request $request   
    * @param integer $id   //Id de la Persona 
    * @throws Exception    
    *
    * Descripcion: Se agrega metodo en service encargado de validar las formas de contactos ingresadas
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>          
    * @version 1.2 01-09-2015 
    * 
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>       
    * @version 1.3 07-11-2016
    * Para MD se controla lo siguiente:
    * Debemos permitir que se pueda guardar la data editada del cliente (Direccion_Tributaria), sin necesidad de verificar el correo_electrónico, 
    * el cual actualmente es obligatorio. 
    * Permita guardar si no existe forma de contacto de correo electrónico. Se valide si ingresan alguna forma de contacto como se esta validando
    * en la actualidad.
    * Permitir editar la Dirección Tributaria del cliente.
    * Verificar que el valor de Dirección Tributaria se replique en el campo Dirección en Info_Persona.
    * Agregar acceso directo a la "Edición del Cliente", desde el Show punto Cliente.      
    * 
    * @author Luis Cabrera <lcabrera@telconet.ec>
    * @version 1.4 03-07/2017
    * Se agregan las variables strNombrePais e intIdPais para determinar qué país se valida en la forma de contactos.
    *
    * Permitir editar el género y orígenes de ingreso a los usuarios con el perfil MD_delegado_datos.
    * @author Jessenia Piloso <jpiloso@telconet.ec>
    * @version 1.5 11-02-2023
    * 
    *  
    * @return a RedirectResponse to the given URL.
    */
   public function updateAction(Request $request, $id)
    {
        $request               = $this->getRequest();
        $objSession            = $request->getSession();
        $em                    = $this->getDoctrine()->getManager('telconet');
        $serviceUtil           = $this->get('schema.Util');
        $entity                = $em->getRepository('schemaBundle:InfoPersona')->find($id);        
        $datos_form            = $request->request->get('clientetype');
        $array_formas_contacto = explode(",", $datos_form['formas_contacto']);
        $a                     = 0;
        $x                     = 0;
        $formas_contacto       = array();
        for($i = 0; $i < count($array_formas_contacto); $i++)
        {
            if($a == 3)
            {
                $a = 0;
                $x++;
            }
            if($a == 1)
                $formas_contacto[$x]['formaContacto'] = $array_formas_contacto[$i];
            if($a == 2)
                $formas_contacto[$x]['valor'] = $array_formas_contacto[$i];
            $a++;
        }
        $idEmpresa     = $objSession->get('idEmpresa');
        $usrUltMod     = $objSession->get('user');
        $strNombrePais = $objSession->get('strNombrePais');
        $intIdPais     = $objSession->get('intIdPais');
        $estadoI       = 'Inactivo';
        if(!$entity)
        {
            throw $this->createNotFoundException('Unable to find InfoPersona entity.');
        }
        $strError          = '';
        $strPrefijoEmpresa = $request->getSession()->get('prefijoEmpresa');
                
        $em->getConnection()->beginTransaction();
        try
        {               
            $objAdmiMotivo = $em->getRepository('schemaBundle:AdmiMotivo')
                                ->findOneBy(array('nombreMotivo' => 'CAMBIO DATOS FACTURACION'));
            if(!is_object($objAdmiMotivo))
            {
                throw new \Exception("No se pudo editar el Cliente - No existe motivo registrado para la generación del Historial");
            }
            if(!isset($datos_form['direccionTributaria']) && empty($datos_form['direccionTributaria']))
            {
                throw new \Exception("No se pudo editar el Cliente - Debe ingresar una dirección tributaria válida");
            }
            
            $objPersonaEmpresaRol = $this->obtieneIdPersonaEmpresaRolPorIdCliente($id, $idEmpresa, $em);                 
            
            if(strtolower($datos_form['direccionTributaria']) != strtolower($entity->getDireccionTributaria()))
            {
                //Registro Historial de la Edicion de la Direccion Tributaria solo si esta es cambiada                
                $objPersonaEmpresaRolHistorial = new InfoPersonaEmpresaRolHisto();
                $objPersonaEmpresaRolHistorial->setEstado($objPersonaEmpresaRol->getEstado());
                $objPersonaEmpresaRolHistorial->setFeCreacion(new \DateTime('now'));
                $objPersonaEmpresaRolHistorial->setIpCreacion($request->getClientIp());
                $objPersonaEmpresaRolHistorial->setPersonaEmpresaRolId($objPersonaEmpresaRol);
                $objPersonaEmpresaRolHistorial->setUsrCreacion($usrUltMod);
                $objPersonaEmpresaRolHistorial->setObservacion("Dirección Trubutaria anterior: " . $entity->getDireccionTributaria());
                $objPersonaEmpresaRolHistorial->setMotivoId($objAdmiMotivo->getId());
                $em->persist($objPersonaEmpresaRolHistorial);
            }
            $entity->setTipoTributario($datos_form['tipoTributario']);
            $entity->setRepresentanteLegal($datos_form['representanteLegal']);
            $entity->setNacionalidad($datos_form['nacionalidad']);          
            $entity->setDireccionTributaria($datos_form['direccionTributaria']);   
            $entity->setDireccion($datos_form['direccionTributaria']);
            $entity->setGenero($datos_form['genero']);   
            $entity->setOrigenIngresos($datos_form['origenIngresos']); 
           
            if(!$datos_form['razonSocial'])
            {
                if($datos_form['tituloId'])
                {
                    $entityAdmiTitulo = $em->getRepository('schemaBundle:AdmiTitulo')->find($datos_form['tituloId']);
                    if($entityAdmiTitulo)
                        $entity->setTituloId($entityAdmiTitulo);
                }
            }
            $em->persist($entity);
           
            //REGISTRA EN LA TABLA DE PERSONA HISTORIAL            
            $entity_persona_historial = new InfoPersonaEmpresaRolHisto();
            $entity_persona_historial->setEstado($objPersonaEmpresaRol->getEstado());
            $entity_persona_historial->setFeCreacion(new \DateTime('now'));
            $entity_persona_historial->setIpCreacion($request->getClientIp());
            $entity_persona_historial->setPersonaEmpresaRolId($objPersonaEmpresaRol);
            $entity_persona_historial->setUsrCreacion($usrUltMod);
            $em->persist($entity_persona_historial);           

            //PONE ESTADO INACTIVO A TODOS LAS FORMAS DE CONTACTO DE LA PERSONA QUE tengan estado ACTIVO
            /* @var $serviceInfoPersonaFormaContacto \telconet\comercialBundle\Service\InfoPersonaFormaContactoService */
            $serviceInfoPersonaFormaContacto = $this->get('comercial.InfoPersonaFormaContacto');
            
            /* Se envia array de Parametros y se agrega strOpcionPermitida y strPrefijoEmpresa, Prefijo de empresa en sesion para validar
             *  que para empresa MD no se obligue el ingreso de al menos 1 correo */
            $arrayParamFormasContac                        = array ();
            $arrayParamFormasContac['strPrefijoEmpresa']   = $strPrefijoEmpresa;
            $arrayParamFormasContac['arrayFormasContacto'] = $formas_contacto;
            $arrayParamFormasContac['strOpcionPermitida']  = 'SI';
            $arrayParamFormasContac['strNombrePais']       = $strNombrePais;
            $arrayParamFormasContac['intIdPais']           = $intIdPais;
            $arrayValidaciones   = $serviceInfoPersonaFormaContacto->validarFormasContactos($arrayParamFormasContac);
            if($arrayValidaciones)
            {    
                foreach($arrayValidaciones as $key => $mensaje_validaciones)
                {
                    foreach($mensaje_validaciones as $key_msj => $value)
                    {                      
                        $strError = $strError.$value.".\n";                        
                    }
                }
                throw new \Exception("No se pudo editar el Cliente - " . $strError);
            } 
            
            $arrayFormaContacto = $em->getRepository('schemaBundle:InfoPersonaFormaContacto')->findBy(array('estado'    =>'Activo',
                                                                                                           'personaId' => $entity->getId()));
            
            $boolExiste = false;
            
            foreach($arrayFormaContacto as $objFormaContacto)
            {
                $boolExiste = false;
                    
                for($i = 0; $i < count($formas_contacto); $i++)
                {
                    if($objFormaContacto->getValor() == $formas_contacto[$i]["valor"])
                    {
                        $boolExiste = true;
                    }
                }
                
                if(!$boolExiste)
                {
                    $objFormaContacto->setEstado('Inactivo');
                    $objFormaContacto->setFeUltMod(new \DateTime('now'));
                    $objFormaContacto->setUsrUltMod($usrUltMod);
                    $em->persist($objFormaContacto);
                    $em->flush();
                }
            }

            //ReGISTRA LAS FORMAS DE CONTACTO DEL CLIENTE
            for($i = 0; $i < count($formas_contacto); $i++)
            {
                $objAdmiFormaContacto = $em->getRepository('schemaBundle:AdmiFormaContacto')
                                                        ->findPorDescripcionFormaContacto($formas_contacto[$i]["formaContacto"]);
                
                if(is_object($objAdmiFormaContacto))
                {
                    $objFormaContacto = $em->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                               ->findOneBy(array('personaId'       => $entity->getId(), 
                                                                                 'valor'           => $formas_contacto[$i]["valor"],
                                                                                 'estado'          => 'Activo',
                                                                                 'formaContactoId' => $objAdmiFormaContacto->getId()));
                
                    if(!is_object($objFormaContacto))
                    {                        
                        $objInfoPersonaFormaContacto = new InfoPersonaFormaContacto();
                        $objInfoPersonaFormaContacto->setValor($formas_contacto[$i]["valor"]);
                        $objInfoPersonaFormaContacto->setEstado("Activo");
                        $objInfoPersonaFormaContacto->setFeCreacion(new \DateTime('now'));                
                        $objInfoPersonaFormaContacto->setFormaContactoId($objAdmiFormaContacto);
                        $objInfoPersonaFormaContacto->setIpCreacion($request->getClientIp());
                        $objInfoPersonaFormaContacto->setPersonaId($entity);
                        $objInfoPersonaFormaContacto->setUsrCreacion($usrUltMod);
                        $em->persist($objInfoPersonaFormaContacto);  
                    }
                }
            }
            

            //Registrar Bitacora de los cambios realizados en los datos del cliente MD.
            if($strPrefijoEmpresa === "MD" || $strPrefijoEmpresa === "EN" )
            {    
                $arrayInfoPunto = $em->getRepository("schemaBundle:InfoPunto")
                                                ->findBy(array('personaEmpresaRolId' => $objPersonaEmpresaRol->getId(),
                                                            'estado' => 'Activo'));        
                if(!empty($arrayInfoPunto))
                { 
                    foreach($arrayInfoPunto as $objPunto)
                    {
                        $intPunto = $objPunto->getId();
                    }
                }else
                {
                    throw new \Exception('No existe punto del cliente');
                }
                //Obtener DatosContactoPunto
                $arrayContactosPunto = $em->getRepository('schemaBundle:InfoPuntoFormaContacto')
                                            ->getFormasContactoPunto($intPunto);


                //Obtener formas de Pago
                $entityContrato = $em->getRepository('schemaBundle:InfoContrato')->findOneByPersonaEmpresaRolId($objPersonaEmpresaRol->getId());

                if( is_object($entityContrato) && $entityContrato->getEstado() == 'Activo'
                && $entityContrato->getFormaPagoId())
                {
                    
                    $intTmpIdFormaPago = $entityContrato->getFormaPagoId();
                    $strDescripcionFormaPago = $intTmpIdFormaPago->getDescripcionFormaPago();

                }

                $arrayDiscapacidad = $em->getRepository('schemaBundle:InfoPersona')
                        ->getDiscapacidadByIdPersonaRol(['intIdServicio' => null,
                                                          'intIdPersonaRol' => $objPersonaEmpresaRol->getId()]);
                
                $arrayRespuesta =  $serviceUtil->guardarBitacora(array (
                    "strTipoIdentificacion"     => $entity->getTipoIdentificacion(),
                    "strIdentificacion"         => $entity->getIdentificacionCliente(),
                    "strNombres"                => $entity->getNombres(),
                    "strApellidos"              => $entity->getApellidos(),
                    "strGenero"                 => $datos_form['genero'],
                    "strOrigenIngresos"         => $datos_form['origenIngresos'],
                    "strDiscapacidad"           => $arrayDiscapacidad[0][DISCAPACIDAD],
                    "strRepresentanteLegal"     => $datos_form['representanteLegal'],
                    "arrayDatosContactoPersona" => $formas_contacto,
                    "arrayDatosContactoPunto"   => $arrayContactosPunto,
                    "strFormaDePago"            => $strDescripcionFormaPago,
                    "strUsuario"                => $usrUltMod ,
                    "strfechaHoraActualizacion" => date("Y-m-d").'T'.date("H:i:s"),
                    "strMetodo"                 => "ACTUALIZACIONYRECTIFICACION_CLIENTE"
                    ));
                    if ($arrayRespuesta['intStatus'] !== 0 )
                    {
                        throw new \Exception('Ocurrió un error al guardar la bitacora en la solicitud de ACTUALIZACION_Y_RECTIFICACION: '
                                            .$arrayRespuesta['strMensaje']);
                    }    
              
            }
            // El flush se agrega al final antes del commit. 
            $em->flush();
            $em->getConnection()->commit();
            return $this->redirect($this->generateUrl('cliente_show', array('id' => $entity->getId(), 'idper' => $objPersonaEmpresaRol->getId())));
        }
        catch(\Exception $e)
        {
            $em->getConnection()->rollback();
            $em->getConnection()->close();            
            $this->get('session')->getFlashBag()->add('notice', $e->getMessage());
            return $this->redirect($this->generateUrl('cliente_edit', array('id' => $entity->getId())));
        }
    }

    /**
    * @Secure(roles="ROLE_8-8")
    */
    public function deleteAction(Request $request, $id) {
        $idEmpresa = $request->getSession()->get('idEmpresa');
        $usrElimina = $request->getSession()->get('user');
        $form = $this->createDeleteForm($id);
        $em = $this->getDoctrine()->getManager('telconet');
        // $em->getConnection()->beginTransaction();   
        //try{
        $entity = $em->getRepository('schemaBundle:InfoPersona')->find($id);
        //INACTIVA LA PERSONA
        $entity->setEstado('Inactivo');
        $em->persist($entity);
        $em->flush();

        //INACTIVA PERSONA_EMPRESA_ROL
        $entityPersonaEmpRol = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->getPersonaEmpresaRolPorPersonaPorTipoRol($entity->getId(), 'Cliente', $idEmpresa);
        $entityPersonaEmpRol->setEstado('Inactivo');
        $em->persist($entityPersonaEmpRol);
        $em->flush();

        //REGISTRA EN LA TABLA DE PERSONA HISTORIAL
        $entity_persona_historial = new InfoPersonaEmpresaRolHisto();
        $entity_persona_historial->setEstado($entity->getEstado());
        $entity_persona_historial->setFeCreacion(new \DateTime('now'));
        $entity_persona_historial->setIpCreacion($request->getClientIp());
        $entity_persona_historial->setPersonaEmpresaRolId($entityPersonaEmpRol);
        $entity_persona_historial->setUsrCreacion($usrElimina);
        $em->persist($entity_persona_historial);
        $em->flush();

        /*    $em->getConnection()->commit();   

          }
          catch (\Exception $e) {
          $em->getConnection()->rollback();
          $em->getConnection()->close();
          return $this->redirect($this->generateUrl('cliente_show', array('id' => $entity->getId())));
          } */

        return $this->redirect($this->generateUrl('cliente'));
    }

    private function createDeleteForm($id) {
        return $this->createFormBuilder(array('id' => $id))
                        ->add('id', 'hidden')
                        ->getForm()
        ;
    }

    public function delete_ajaxAction() {
        $request=$this->getRequest();
        $idEmpresa = $request->getSession()->get('idEmpresa');
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        //echo $id;die;
        $respuesta->setContent("error del Form");
        $em = $this->getDoctrine()->getManager('telconet');

        //Obtiene parametros enviados desde el ajax
        $peticion = $this->get('request');
        $parametro = $peticion->get('param');
        $array_valor = explode("|", $parametro);

        $em->getConnection()->beginTransaction();
        try {
            foreach ($array_valor as $id):
                $entity = $em->getRepository('schemaBundle:InfoPersona')->find($id);
                if (!$entity) {
                    throw $this->createNotFoundException('No se encontro el prospecto buscado');
                }
                //INACTIVA LA PERSONA
                $entity->setEstado('Inactivo');
                $em->persist($entity);
                $em->flush();
                //INACTIVA PERSONA_EMPRESA_ROL
                $entityPersonaEmpRol = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->getPersonaEmpresaRolPorPersonaPorTipoRol($entity->getId(), 'Cliente', $idEmpresa);
                if ($entityPersonaEmpRol) {
                    $entityPersonaEmpRol->setEstado('Inactivo');
                    $em->persist($entityPersonaEmpRol);
                    $em->flush();
                    
                    //REGISTRA EN LA TABLA DE PERSONA HISTORIAL
                    $entity_persona_historial = new InfoPersonaEmpresaRolHisto();
                    $entity_persona_historial->setEstado($entity->getEstado());
                    $entity_persona_historial->setFeCreacion(new \DateTime('now'));
                    $entity_persona_historial->setIpCreacion($request->getClientIp());
                    $entity_persona_historial->setPersonaEmpresaRolId($entityPersonaEmpRol);
                    $entity_persona_historial->setUsrCreacion($usrElimina);
                    $em->persist($entity_persona_historial);
                    $em->flush();
                }
            endforeach;
            $em->getConnection()->commit();
            $respuesta->setContent("Se elimino el registro con exito.");
        } catch (\Exception $e) {
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            $respuesta->setContent("error al tratar de eliminar registro. Consulte con el Administrador.");
        }


        return $respuesta;
    }

    public function datosComercialesAction($idper) {
        $request=$this->getRequest();
        $em = $this->getDoctrine()->getManager();
        $estado="Activo";
        $idEmpresa=$request->getSession()->get('idEmpresa');
        $entity = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($idper);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoPersona entity.');
        }
        $formFormaPago=null;
        $deleteForm = $this->createDeleteForm($id);
        $entityContrato = $em->getRepository('schemaBundle:InfoContrato')->findOneBy(array( "personaEmpresaRolId" => $idper, "estado" => $estado));

        //print_r($entityContrato);die;
        if($entityContrato){
            if($entityContrato->getFormaPagoId()->getDescripcionFormaPago()!="Efectivo")
            {
                //Busco por id y por estado -- falta por estado
                $formFormaPago = $em->getRepository('schemaBundle:InfoContratoFormaPago')->findPorContratoIdYEstado($entityContrato->getId(),$estado);
            }        
        }
        return $this->render('comercialBundle:cliente:datosComerciales.html.twig', array(
                    'entity' => $entity->getPersonaId(),
                    'delete_form' => $deleteForm->createView(),
                    'contrato'=> $entityContrato,
                    'formFormaPago'=>$formFormaPago,
                    'idper'=>$idper
                ));
    }    

    public function ajaxServiciosAction($idCli) {
        $request = $this->getRequest();
        $limit = $request->get("limit");
        $page = $request->get("page");
        $start = $request->get("start");
        $nombre = $request->get("nombre");
        $nombrePadre = $request->get("nombrePadre");
        $criterioLoginPadre = $request->get("criterioLoginPadre");
        //$user = $this->get('security.context')->getToken()->getUser();
        $idEmpresa = $request->getSession()->get('idEmpresa');

        $em = $this->get('doctrine')->getManager('telconet');

        $resultado = $em->getRepository('schemaBundle:InfoServicio')->findServiciosPorEmpresaPorPersona($idEmpresa, $idCli, $limit, $page, $start,$nombre,$nombrePadre,$criterioLoginPadre);
        $datos = $resultado['registros'];
        $total = $resultado['total'];
        

        $i = 1;
        foreach ($datos as $dato):
            //print_r ();die;
            $idProducto='';
            $descripcionProducto='';
            if ($dato->getProductoId()){
                $idProducto=$dato->getProductoId()->getId();
                $descripcionProducto=$dato->getProductoId()->getDescripcionProducto();
                $tipo='producto';
            }elseif($dato->getPlanId())
            {
                $tipo='plan';
                $idProducto=$dato->getPlanId()->getId();
                $descripcionProducto=$dato->getPlanId()->getDescripcionPlan();                
            }
            $padre='';$loginPadre='';
            if($dato->getPuntoFacturacionId()){
                $padre=$dato->getPuntoFacturacionId()->getId();
                $loginPadre=$dato->getPuntoFacturacionId()->getLogin();
            }
            $arreglo[] = array(
                'idServicio' => $dato->getId(),
                'tipo'=>$tipo,
                'idPunto' => $dato->getPuntoId()->getId(),
                'descripcionPunto' => $dato->getPuntoId()->getLogin(),
                'loginPunto' => $dato->getPuntoId()->getLogin(),
                'idProducto' => $idProducto,
                'descripcionProducto' => $descripcionProducto,
                'cantidad' => $dato->getCantidad(),
                'fechaCreacion' => strval(date_format($dato->getFeCreacion(), "d/m/Y G:i")),
                'precioVenta' => $dato->getPrecioVenta(),
                'estado' => $dato->getEstado(),
                'padre' => $padre,
                'loginPadre' => $loginPadre,
                'esVenta' => $dato->getEsVenta()
            );

            $i++;
        endforeach;
        if (!empty($arreglo))
            $response = new Response(json_encode(array('total' => $total, 'servicios' => $arreglo)));
        else {
            $arreglo[] = array();
            $response = new Response(json_encode(array('total' => $total, 'servicios' => $arreglo)));
        }
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }

    
    public function ajaxServiciosPerAction($idper) {
        $request = $this->getRequest();
        $limit = $request->get("limit");
        $page = $request->get("page");
        $start = $request->get("start");
        $nombre = $request->get("nombre");
        $nombrePadre = $request->get("nombrePadre");
        $criterioLoginPadre = $request->get("criterioLoginPadre");
        //$user = $this->get('security.context')->getToken()->getUser();
        $idEmpresa = $request->getSession()->get('idEmpresa');

        $em = $this->get('doctrine')->getManager('telconet');

        $resultado = $em->getRepository('schemaBundle:InfoServicio')->findServiciosPorPersonaEmpresaRolId($idper, $limit, $page, $start,$nombre,$nombrePadre,$criterioLoginPadre);
        $datos = $resultado['registros'];
        $total = $resultado['total'];
        

        $i = 1;
        foreach ($datos as $dato):
            //print_r ();die;
            $idProducto='';
            $descripcionProducto='';
            if ($dato->getProductoId()){
                $idProducto=$dato->getProductoId()->getId();
                $descripcionProducto=$dato->getProductoId()->getDescripcionProducto();
                $tipo='producto';
            }elseif($dato->getPlanId())
            {
                $tipo='plan';
                $idProducto=$dato->getPlanId()->getId();
                $descripcionProducto=$dato->getPlanId()->getDescripcionPlan();                
            }
            $padre='';$loginPadre='';
            if($dato->getPuntoFacturacionId()){
                $padre=$dato->getPuntoFacturacionId()->getId();
                $loginPadre=$dato->getPuntoFacturacionId()->getLogin();
            }
            $arreglo[] = array(
                'idServicio' => $dato->getId(),
                'tipo'=>$tipo,
                'idPunto' => $dato->getPuntoId()->getId(),
                'descripcionPunto' => $dato->getPuntoId()->getLogin(),
                'loginPunto' => $dato->getPuntoId()->getLogin(),
                'idProducto' => $idProducto,
                'descripcionProducto' => $descripcionProducto,
                'cantidad' => $dato->getCantidad(),
                'fechaCreacion' => strval(date_format($dato->getFeCreacion(), "d/m/Y G:i")),
                'precioVenta' => $dato->getPrecioVenta(),
                'estado' => $dato->getEstado(),
                'padre' => $padre,
                'loginPadre' => $loginPadre
            );

            $i++;
        endforeach;
        if (!empty($arreglo))
            $response = new Response(json_encode(array('total' => $total, 'servicios' => $arreglo)));
        else {
            $arreglo[] = array();
            $response = new Response(json_encode(array('total' => $total, 'servicios' => $arreglo)));
        }
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }    
    
    //funcion para flujo de show cliente
    public function ajaxGetTotalContactosClienteAction($id)
    {
        $request = $this->getRequest();
        $idEmpresa = $request->getSession()->get('idEmpresa');        
        $em = $this->get('doctrine')->getManager('telconet');				
        $totalCtos=$em->getRepository('schemaBundle:InfoPersonaContacto')->findTotalContactosPorCliente($idEmpresa,$id);
        if($totalCtos){
            foreach($totalCtos as $dato){	
                    $arreglo[]= array('total'=> $dato['total']);
            }
        }
        else
        {
            $arreglo[]=array('total'=>0);
        }                
        $response = new Response(json_encode(array('total_ctos'=>$arreglo)));
        $response->headers->set('Content-type', 'text/json');
        return $response;	
    }      

    //funcion para flujo de show prospecto
    public function ajaxGetClienteConvertidoAction($id)
    {
        $request = $this->getRequest();
        $idEmpresa = $request->getSession()->get('idEmpresa');        
        $em = $this->get('doctrine')->getManager('telconet');
        $cliente=$em->getRepository('schemaBundle:InfoPersona')->find($id);
        $personaEmpresaRol=$em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                ->getPersonaEmpresaRolPorPersonaPorTipoRol($id,'Cliente',$idEmpresa);
        if($personaEmpresaRol)        
	    $em->getRepository('schemaBundle:InfoPersonaEmpresaRolHisto')->findUltimoEstadoPorPersonaEmpresaRol($personaEmpresaRol->getId());
	    
        $arreglo=array();
        if($cliente){

                    $arreglo[]= array(
                        'estado'=> $cliente->getEstado());

        }               
        $response = new Response(json_encode(array('cliente'=>$arreglo)));
        $response->headers->set('Content-type', 'text/json');
        return $response;	
    }
    /**
    * agregarPadreFacturacionAction, Muestra en el grid los padres de facturacion
    * @author Alexander Samaniego <awsamaniego@telconet.ec>
    * @version 1.1 11-12-2014
    * @since 1.0
    * 
    * @author Ricardo Coello Quezada <rcoello@telconet.ec>
    * @version 1.1 10-09-2017
    * @since 1.1
    * Manejo de parámetro para el máximo numero de correos y el máximo de números de teléfono en la ventana de datos de envio cuando sea padre de 
    * facturacion para TN.
    * 
    * @author Luis Cabrera <lcabrera@telconet.ec>
    * @version 1.2 05-07-2017
    * Se agrega la validación para Telconet Panamá. 
    * 
    */
    public function agregarPadreFacturacionAction($id, $idper)
    {
        $em                     = $this->getDoctrine()->getManager();
        $strEstado              = "Activo";
        $entityInfoPersona      = $em->getRepository('schemaBundle:InfoPersona')->find($id);
        $request                = $this->getRequest();
        $intIdEmpresa           = $request->getSession()->get('idEmpresa');
        $strPrefijoEmpresa      = $request->getSession()->get('prefijoEmpresa');
        $strNombreParametro     = 'DATOS_DE_ENVIO';
        $strModulo              = 'COMERCIAL';
        $strProceso             = 'CLIENTE';
        $intNumeroMaxCorreos    = 0;
        $intNumeroMaxTelefonos  = 0;
        $arrayParametrosCabDet  = array ();

        if(!$entityInfoPersona)
        {
            throw $this->createNotFoundException('Unable to find InfoPersona entity.');
        }
        $strFormaPago   = null;
        $objDeleteForm  = $this->createDeleteForm($id);
        $entityContrato = $em->getRepository('schemaBundle:InfoContrato')->findOneBy(array("personaEmpresaRolId" => $idper, "estado" => $strEstado));
        if($entityContrato)
        {
            if($entityContrato->getFormaPagoId()->getDescripcionFormaPago() != "Efectivo")
            {
                //Busco por id y por estado -- falta por estado
                $strFormaPago = $em->getRepository('schemaBundle:InfoContratoFormaPago')->findPorContratoIdYEstado($id, $strEstado);
            }
        }
        $rolesPermitidos = array();
        if(true === $this->get('security.context')->isGranted('ROLE_8-1977'))
        {
            $rolesPermitidos[] = 'ROLE_8-1977'; //PERMISO DE ACTUALIZAR GASTO ADMINISTRATIVO
        }
        
        if($strPrefijoEmpresa == 'TN' || $strPrefijoEmpresa == 'TNP' )
        {
            $intNumeroMaxCorreos                    = 2;
            $intNumeroMaxTelefonos                  = 2;
            
            $arrayParametrosCabDet['strNombreParametro']  = $strNombreParametro;
            $arrayParametrosCabDet['strModulo']           = $strModulo;
            $arrayParametrosCabDet['strProceso']          = $strProceso;
            $arrayParametrosCabDet['strPrefijoEmpresa']   = $strPrefijoEmpresa;
            $arrayParametrosCabDet['strIdEmpresa']        = $intIdEmpresa;
            
            /* @var $serviceInfoPunto \telconet\comercialBundle\Service\InfoPuntoService */
            $serviceInfoPunto                             = $this->get('comercial.InfoPunto');
            $arrayNumeroMaximosCorreoTelefono             = $serviceInfoPunto->obtenerNumeroMaximoDeCorreosTelefonos( $arrayParametrosCabDet );
            
            if($arrayNumeroMaximosCorreoTelefono && count($arrayNumeroMaximosCorreoTelefono) > 0)
            {
                $intNumeroMaxCorreos   = $arrayNumeroMaximosCorreoTelefono['intNumeroMaxCorreos'];
                $intNumeroMaxTelefonos = $arrayNumeroMaximosCorreoTelefono['intNumeroMaxTelefonos'];
            }
        }
        
        return $this->render('comercialBundle:cliente:agregarPadreFacturacion.html.twig', array(
                'entity'            => $entityInfoPersona,
                'delete_form'       => $objDeleteForm->createView(),
                'contrato'          => $entityContrato,
                'formFormaPago'     => $strFormaPago,
                'idper'             => $idper,
                'rolesPermitidos'   => $rolesPermitidos,
                'numeroMaxCorreos'  => $intNumeroMaxCorreos,
                'numeroMaxTelefonos'=> $intNumeroMaxTelefonos,
                'prefijoEmpresa'    => $strPrefijoEmpresa
        ));
    }
    
    /**
     * @Secure(roles="ROLE_9-3757")
     * 
     * Documentación para el método 'showAsignarEjecutivoCobranzasAction'.
     *
     * Renderiza la ventana para asignación del ejecutivo de cobranza a cada punto del cliente
     * 
     * @param int $id    primary key de la info_persona del cliente
     * @param int $idper primary key de la info_persona_empresa_rol del cliente
     * 
     * @return Render Renderización de a ventana para la asignación del ejecutivo de Cobranza
     * 
     * @author Alejandro Domínguez<adominguez@telconet.ec>       
     * @version 1.0 24-03-2016
     */
    public function showAsignarEjecutivoCobranzasAction($id, $idper)
    {
        $em                = $this->getDoctrine()->getManager();
        $entityInfoPersona = $em->getRepository('schemaBundle:InfoPersona')->find($id);
        $strEmpresaCod     = $this->getRequest()->getSession()->get('idEmpresa');
        
        if(!$entityInfoPersona)
        {
            throw $this->createNotFoundException('Unable to find InfoPersona entity.');
        }
        
        $rolesPermitidos = array();

        if($this->get('security.context')->isGranted('ROLE_9-3637'))
        {
            $rolesPermitidos[] = 'ROLE_9-3637'; // ASIGNAR EJECUTIVO DE VENTA AL PUNTO
        }
        if($this->get('security.context')->isGranted('ROLE_9-3758'))
        {
            $rolesPermitidos[] = 'ROLE_9-3758'; // OBTENER EJECUTIVOS DE COBRANZAS
        }
        
        return $this->render('comercialBundle:cliente:asignarEjecutivoCobranzas.html.twig', array('entity'          => $entityInfoPersona,
                                                                                                  'idper'           => $idper,
                                                                                                  'rolesPermitidos' => $rolesPermitidos));
    }
    
    /**
     * @Secure(roles="ROLE_151-6417")
     * 
     * Documentación para el método 'showCorteReactivacionTelcohomeAction'.
     *
     * Renderiza la ventana para corte o reactivación de manera masiva de los servicios TelcoHome
     * 
     * @param int $intIdPersona id de la persona de la info_persona del cliente
     * @param int $intIdPer id de la info_persona_empresa_rol del cliente
     * 
     * @return Render Renderización de a ventana para la asignación del ejecutivo de Cobranza
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>       
     * @version 1.0 20-03-2019
     */
    public function showCorteReactivacionTelcohomeAction($intIdPersona, $intIdPer)
    {
        $emComercial    = $this->getDoctrine()->getManager();
        $objPersona     = $emComercial->getRepository('schemaBundle:InfoPersona')->find($intIdPersona);
        if(!is_object($objPersona))
        {
            throw $this->createNotFoundException('Unable to find InfoPersona entity.');
        }
        
        $arrayRolesPermitidos = array();
        if($this->get('security.context')->isGranted('ROLE_151-6438'))
        {
            $arrayRolesPermitidos[] = 'ROLE_151-6438'; // Generar Corte o Reactivación Masiva de Servicios TelcoHome de un cliente
        }
        return $this->render('comercialBundle:cliente:showCorteReactivacionTelcohome.html.twig', array('objPersona'        => $objPersona,
                                                                                                       'intIdPer'          => $intIdPer,
                                                                                                       'rolesPermitidos'   => $arrayRolesPermitidos));
    }
    
    /**
    * agregarPadre_ajaxAction, Agrega el padre de facturacion
    * @author Edgar Holguin <eholguin@telconet.ec>
    * @version 1.3 28-09-2017 Se agrega ingreso de registro en la tabla INFO PUNTO DATO ADICIONAL en caso de que este no exista.    
    * @author Edgar Holguin <eholguin@telconet.ec>
    * @version 1.2 22-11-2016 Se agrega registro de historial de punto agregado como padre de facturación.
    * @author Alexander Samaniego <awsamaniego@telconet.ec>
    * @version 1.1 02-03-2015
    * @since 1.0
    */
    public function agregarPadre_ajaxAction() { 
        $request        = $this->getRequest();
        $respuesta      = new Response();
        $usuario        = $request->getSession()->get('user');
        $respuesta->headers->set('Content-Type', 'text/plain');
        $respuesta->setContent("error del Form");
        $em             = $this->getDoctrine()->getManager('telconet');

        //Obtiene parametros enviados desde el ajax
        $peticion          = $this->get('request');
        $parametro         = $peticion->get('param');
        $array_valor       = explode("|", $parametro);
        $strIpCreacion     = $request->getClientIp();       
        $serviceInfoPunto  = $this->get('comercial.InfoPunto');

        $em->getConnection()->beginTransaction();
        try {
            foreach ($array_valor as $id):
                
                $entity = $em->getRepository('schemaBundle:InfoPuntoDatoAdicional')->findOneByPuntoId($id);
            
                $objInfoPunto = $em->getRepository('schemaBundle:InfoPunto')->find($id);
                
                if (!$entity && is_object($objInfoPunto)) 
                {
                    
                    $entity = new InfoPuntoDatoAdicional();
                    $entity->setPuntoId($objInfoPunto);
                    $entity->setFeCreacion(new \DateTime('now'));
                    $entity->setUsrCreacion($usuario);
                    
                }
                $strEsPadreFactAnterior = '';
                
                if(!is_null($entity->getEsPadreFacturacion()))
                {
                    $strEsPadreFactAnterior = $entity->getEsPadreFacturacion();
                }                
                $entity->setEsPadreFacturacion('S');
                $entity->setGastoAdministrativo('N');
                $entity->setUsrUltMod($usuario);
                $entity->setFeUltMod(new \DateTime('now'));
                $em->persist($entity);
                $em->flush();
                                
                
                if(is_object($objInfoPunto))
                {
                    $arrayParametros   = array();
                    $arrayParametros['objInfoPunto']       = $objInfoPunto; 
                    $arrayParametros['strUsuarioCreacion'] = $usuario; 
                    $arrayParametros['strIpCreacion']      = $strIpCreacion; 
                    $arrayParametros['strValor']           = ' LOGIN: '.$objInfoPunto->getLogin().
                                                             ' ES_PADRE_FACTURACION ANTERIOR: '.$strEsPadreFactAnterior.
                                                             ' ACTUAL: '.$entity->getEsPadreFacturacion();
                    $arrayParametros['strAccion']          = 'agregarPadreFacturacion'; 
                
                   
                    $serviceInfoPunto->generarHistorialPunto($arrayParametros);
                }
            endforeach;
            $em->getConnection()->commit();
            $respuesta->setContent("Se agrego el registros con exito.");
        } catch (\Exception $e) {
            if($em->getConnection()->isTransactionActive())
            {
                $em->getConnection()->rollback();
            }
            $em->getConnection()->close();
            //$respuesta->setContent("error al tratar de agregar padres de facturacion registro. Consulte con el Administrador.");
            $respuesta->setContent($e->getMessage());
        }
        return $respuesta;
    }
 
    /**
    * quitarPadre_ajaxAction.
    * Setea con valor de N el valor ES_PADRE_FACTURACION de un punto determinado.
    * @author Edgar Holguin <eholguin@telconet.ec>
    * @version 1.1 22-11-2016 Se agrega registro de historial de punto que es eliminado como padre de facturación.
    * 
    * @author Edgar Holguin <eholguin@telconet.ec>
    * @version 1.2 16-08-2019 Se agrega registro de historial a servicios afectados al eliminar padre de facturación.
    * 
    * @since 1.0
    */   
    public function quitarPadre_ajaxAction() {
        $objRequest=$this->getRequest();
        $respuesta = new Response();
        $strUsuario=$objRequest->getSession()->get('user');
        $respuesta->headers->set('Content-Type', 'text/plain');
        $respuesta->setContent("error del Form");
        $em = $this->getDoctrine()->getManager('telconet');

        //Obtiene parametros enviados desde el ajax
        $peticion          = $this->get('request');
        $parametro         = $peticion->get('param');
        $array_valor       = explode("|", $parametro);       
        $strIpCreacion     = $objRequest->getClientIp();
        $serviceInfoPunto  = $this->get('comercial.InfoPunto');

        $em->getConnection()->beginTransaction();
        try {
            foreach ($array_valor as $id):
                $entity = $em->getRepository('schemaBundle:InfoPuntoDatoAdicional')->findOneByPuntoId($id);
                if (!$entity) {
                    throw $this->createNotFoundException('No se encontro el punto buscado');
                }
                
                $strEsPadreFactAnterior ='';
                if(!is_null($entity->getEsPadreFacturacion()))
                {
                    $strEsPadreFactAnterior = $entity->getEsPadreFacturacion();
                }
                $entity->setEsPadreFacturacion('N');
                $entity->setUsrUltMod($strUsuario);
                $entity->setFeUltMod(new \DateTime('now'));
                $em->persist($entity);
                $em->flush();
                
                $objInfoPunto = $em->getRepository('schemaBundle:InfoPunto')->find($id);
                
                if(is_object($objInfoPunto))
                {
                    $arrayParametros   = array();
                    $arrayParametros['objInfoPunto']       = $objInfoPunto; 
                    $arrayParametros['strUsuarioCreacion'] = $strUsuario; 
                    $arrayParametros['strIpCreacion']      = $strIpCreacion; 
                    $arrayParametros['strValor']           = ' LOGIN: '.$objInfoPunto->getLogin().
                                                             ' ES_PADRE_FACTURACION ANTERIOR: '.$strEsPadreFactAnterior.
                                                             ' ACTUAL: '.$entity->getEsPadreFacturacion();
                    $arrayParametros['strAccion']          = 'eliminarPadreFacturacion';                                        
                    $serviceInfoPunto->generarHistorialPunto($arrayParametros);
                }

                $arrayServiciosAfectados = $em->getRepository("schemaBundle:InfoServicio")->findByPuntoFacturacionId($id);
                if(count($arrayServiciosAfectados) > 0)
                {               
                    foreach($arrayServiciosAfectados as $objServicio)
                    {
                        $strHistorialMsg = " Se elimina punto : ".$objInfoPunto->getLogin()." como padre de facturación del servicio:";
                        $objServicioHist = new InfoServicioHistorial();
                        $objServicioHist->setServicioId($objServicio);
                        $objServicioHist->setObservacion($strHistorialMsg);
                        $objServicioHist->setIpCreacion($strIpCreacion);
                        $objServicioHist->setFeCreacion(new \DateTime('now'));
                        $objServicioHist->setUsrCreacion($strUsuario);
                        $objServicioHist->setEstado($objServicio->getEstado());
                        $em->persist($objServicioHist);
                        $em->flush();                    

                    }
                }                
            endforeach;
            $em->getConnection()->commit();
            $respuesta->setContent("Se agrego el registros con exito.");
        } catch (\Exception $e) {
            if($em->getConnection()->isTransactionActive())
            {
                $em->getConnection()->rollback();
            }
            $em->getConnection()->close();
            //$respuesta->setContent("error al tratar de agregar padres de facturacion registro. Consulte con el Administrador.");
            $respuesta->setContent($e->getMessage());
        }
        return $respuesta;
    }    

   
    /**
     * listaCiudades_ajaxAction
     *
     * Metodo encargado de listar las ciudades segun nombre recibido por paremtro
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 13-02-2015
     * @since 1.0
     * 
     * @return Response
     *
     * 
     * Se modifica funcion para enviar parametro intPaisId a la funcion getCantonesPorNombre
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 04-07-2017
     */
    public function listaCiudades_ajaxAction()
    {
        $request                       = $this->getRequest();
        $em                            = $this->get('doctrine')->getManager('telconet');
        $peticion                      = $this->get('request');
        
        $arrayParametros               = array();
        $arrayParametros['strNombre']  = $peticion->get('query');
        $arrayParametros['intIdPais']  = $request->getSession()->get('intIdPais');
        
        $ciudad                        = $em->getRepository('schemaBundle:AdmiCanton')->getCantonesPorNombre($arrayParametros);
        $arreglo                       = array();
        if($ciudad)
        {
            foreach ($ciudad as $dato):
                    $arreglo[] = array('id'=> $dato->getId(),'nombre'=>$dato->getNombreCanton());
            endforeach;
        }
        $response = new Response(json_encode(array('ciudades'=>$arreglo)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }
    
    public function listaParroquias_ajaxAction(){
        $request = $this->getRequest();
        $nombre = $request->get('query');
        $idcanton = $request->get('idcanton');

        /* @var $serviceCliente \telconet\comercialBundle\Service\ClienteService */
        $serviceCliente = $this->get('comercial.Cliente');
        $arreglo = $serviceCliente->obtenerParroquiasCanton($idcanton, $nombre);
        
        $response = new Response(json_encode(array('parroquias'=>$arreglo)));
        $response->headers->set('Content-type', 'text/json');
        return $response;	
        
    }   
    
    public function listaSectores_ajaxAction(){
        $request = $this->getRequest();
        $empresaCod = $request->getSession()->get('idEmpresa');
        $nombre = $request->get('query');
        $idparroquia = $request->get('idparroquia');
        
        /* @var $serviceCliente \telconet\comercialBundle\Service\ClienteService */
        $serviceCliente = $this->get('comercial.Cliente');
        $arreglo = $serviceCliente->obtenerSectoresParroquia($empresaCod,$idparroquia, $nombre);
        
        $response = new Response(json_encode(array('sectores'=>$arreglo)));
        $response->headers->set('Content-type', 'text/json');
        return $response;	
        
    }
    
    /**
    * @Secure(roles="ROLE_8-624")
    * 
    * Documentación para el método 'grabaDatosEnvio_ajaxAction'.
    * 
    *  @author Ricardo Coello Quezada <rcoello@telconet.ec>
    *  @version 1.1 14-06-2017 - Se modifica la función para procesar datos de envio de manera masiva,
    *                            enviando uno o muliples id de puntos separados por "|"
    *
    * @author Luis Cabrera <lcabrera@telconet.ec>
    * @version 1.2 - Se agrega el consumo del WS a comprobantes-electronicos para actualizar la información del usuario.
    * @since 05-04-2018
    * 
    * @author Hector Lozano <hlozano@telconet.ec>
    * @version 1.3 - Se agrega validación para clientes que no existen,
     *               o no se encuentran asociados a la empresa TN o MD para editar los datos de envío.
    * @since 29-06-2018
     * 
    * @author Jesus Banchen <jbanchen@telconet.ec>
    * @version 1.4 - Se agrega validación para el envío de notificaciones cuando sea modificada,
    *               el envio de datos,.
    * @since 23-07-2019
     * 
    */ 
    public function grabaDatosEnvio_ajaxAction() {
        $objRequest         = $this->get('request');
        $objSession         = $objRequest->getSession();
        $respuesta          = new Response();
        $strUsuario         = $objSession->get('user');
        $em                 = $this->getDoctrine()->getManager('telconet');
        $objEmpresa         = $em->getRepository("schemaBundle:InfoEmpresaGrupo")->find($objSession->get("idEmpresa"));
        $objService         = $this->get('financiero.InfoCompElectronico');
        $intPuntoIdAct      = 0;


        $respuesta->headers->set('Content-Type', 'text/plain');
        $respuesta->setContent("error del Form");

        //Obtiene parametros enviados desde el ajax
        $boolDatoEnvioMasivo = $objRequest->get('datoEnvioMasivo');
        $intIdPto            = $objRequest->get('idPto');
        $boolModifica        = $objRequest->get('modifica');
        
        $arrayIdPuntos       = explode("|", $intIdPto);
		
        $intActEnvio =0;
        $intVerifica=0;
        
        if(($boolModifica === "true") && (!$boolDatoEnvioMasivo))
        {
            $intSectorComboId = $objRequest->get('idsector') ? $objRequest->get('idsector') : "";
            $intAntIdSector   = $objRequest->get('ant_id_sector') ? $objRequest->get('ant_id_sector') : "";
            
            $intIdSector =  (is_numeric($intSectorComboId)) ? $intSectorComboId : $intAntIdSector;
		}
		else
		{
            $intIdSector = $objRequest->get('idsector');
		}
		
        $em->getConnection()->beginTransaction();
        
        try {
            foreach ($arrayIdPuntos as $intId):
                $entity = $em->getRepository('schemaBundle:InfoPuntoDatoAdicional')->findOneByPuntoId($intId);
                if (!$entity) {
                    throw $this->createNotFoundException('No se encontro el punto buscado');
                }
                $arrayDatosActual = array(  "nombreEnvio"      => $entity->getNombreEnvio(),
                                            "direccionEnvio"   => $entity->getDireccionEnvio(),
                                            "emailEnvio"       => $entity->getEmailEnvio(),
                                            "telefonoEnvio"    => $entity->getTelefonoEnvio(),
                                            "ant_id_ciudad"    => $objRequest->get('ant_id_ciudad'),
                                            "ant_id_parroquia" => $objRequest->get('ant_id_parroquia'),
                                            "idSectorAnterior" => $objRequest->get('ant_id_sector')
                );
                $entity->setDatosEnvio('S');
                $entity->setNombreEnvio($objRequest->get('nombre'));
                $entity->setDireccionEnvio($objRequest->get('direccion'));
                $entity->setEmailEnvio($objRequest->get('email'));
                $entity->setTelefonoEnvio($objRequest->get('telefono'));
                if($intIdSector){
                    $entitySector=$em->getRepository("schemaBundle:AdmiSector")->find($intIdSector);
                    $entity->setSectorId($entitySector);
                }
                $entity->setUsrUltMod($strUsuario);
                $entity->setFeUltMod(new \DateTime('now'));
                $em->persist($entity);
                $em->flush();
                $intPuntoIdAct = $intId;
            endforeach;
            //Se modifica el valor del registro en COMPROBANTES ELECTRONICOS.
            $objInfoPunto = $em->getRepository("schemaBundle:InfoPunto")->find($intPuntoIdAct);

            if(is_object($objInfoPunto) &&  $objInfoPunto->getEstado() == 'Activo')
            {
                $arrayParametros = array("rucEmpresa"            => $objEmpresa->getRuc(),
                                         "identificacionCliente" => $objSession->get("cliente")["identificacion"],
                                         "razonSocialCliente"    => "",
                                         "emailCliente"          => $entity->getEmailEnvio(),
                                         "direccionCliente"      => "",
                                         "telefonoCliente"       => "",
                                         "ciudadCliente"         => "",
                                         "numeroCliente"         => "",
                                         "formaPagoCliente"      => "",
                                         "loginCliente"          => $objInfoPunto->getLogin(),
                                         "contratoCliente"       => "");
                $arrayRespuesta = $objService->actualizarDatosContactoUsuario($arrayParametros);
                if(isset($arrayRespuesta))
                {
                    $infoAdicional=$arrayRespuesta["informacionAdicional"];
                    $usrAsociado="No se encuentra asociado a la empresa";
                    $usrNoExiste="Usuario No encontrado";
                    if($arrayRespuesta["estado"] != 1 && ((stripos($infoAdicional, $usrAsociado)!==false) && (stripos($infoAdicional, $usrNoExiste)!==false)))
                    {
                        throw new \Exception($arrayRespuesta["mensaje"]);
                    }
                }
                else
                {
                    throw new \Exception("Ocurrió un error al actualizar la información en Comprobantes Electrónicos");
                }
            }

            $intIdCiudad = (is_numeric($objRequest->get('idCiudad'))) ? $objRequest->get('idCiudad') : $objRequest->get('ant_id_ciudad');
            if ($intIdCiudad > 0)
            {
                $entityCiudad = $em->getRepository("schemaBundle:AdmiCanton")->find($intIdCiudad);
            }
            $intIdParroquia = (is_numeric($objRequest->get('idParroquia'))) ? $objRequest->get('idParroquia') : $objRequest->get('ant_id_parroquia');
            if ($intIdParroquia > 0)
            {
                $entityParroquia = $em->getRepository("schemaBundle:AdmiParroquia")->find($intIdParroquia);
            }
            $intIdSector = (is_numeric($objRequest->get('idsector'))) ? $objRequest->get('idsector') : $objRequest->get('ant_id_sector');
            if ($intIdSector > 0)
            {
                $entitySector = $em->getRepository("schemaBundle:AdmiSector")->find($intIdSector);
            }
            $objPersona = $em->getRepository('schemaBundle:InfoPersona')->findOneBy(array('login' => $strUsuario));
            if ($arrayDatosActual["ant_id_ciudad"] > 0 && $arrayDatosActual["ant_id_parroquia"] > 0)
            {
                $intVerifica = 10;
            }
            if ($arrayDatosActual["idSectorAnterior"] > 0 && $intVerifica == 10)
            {
                $entityCiudadAnt = $em->getRepository("schemaBundle:AdmiCanton")->find($arrayDatosActual["ant_id_ciudad"]);
                $entityParroquiaAnt = $em->getRepository("schemaBundle:AdmiParroquia")->find($arrayDatosActual["ant_id_parroquia"]);
                $entitySectorAnt = $em->getRepository("schemaBundle:AdmiSector")->find($arrayDatosActual["idSectorAnterior"]);
            }

            if ((is_object($entityCiudad) && is_object($entityParroquia) ) && ( is_object($entitySector) && is_object($objPersona) ))
            {
                $intVerifica = 2;
            }
            if ((is_object($entityCiudadAnt) && is_object($entitySectorAnt) ) && (is_object($entityParroquiaAnt) && $intVerifica == 2))
            {

                $arrayParametrosEnvioNew = array(
                    "nombreEnvio" => '',
                    "nombreEnvioAnt" => '',
                    "direccionEnvio" => '',
                    "direccionEnvioAnt" => '',
                    "emailEnvio" => '',
                    "emailEnvioAnt" => '',
                    "telefonoEnvio" => '',
                    "telefonoEnvioAnt" => '',
                    "actCiudad" => '',
                    "antCiudad" => '',
                    "actParroquia" => '',
                    "antParroquia" => '',
                    "actSector" => '',
                    "antSector" => ''
                );

                if (strtolower(trim($entity->getNombreEnvio())) != strtolower(trim($arrayDatosActual["nombreEnvio"])))
                {
                    $arrayParametrosEnvioNew["nombreEnvio"] = $entity->getNombreEnvio();
                    $arrayParametrosEnvioNew["nombreEnvioAnt"] = $arrayDatosActual["nombreEnvio"];
                    $intActEnvio = 1;
                }

                if (strtolower(trim($entity->getDireccionEnvio())) != strtolower(trim($arrayDatosActual["direccionEnvio"])))
                {
                    $arrayParametrosEnvioNew["direccionEnvio"] = $entity->getDireccionEnvio();
                    $arrayParametrosEnvioNew["direccionEnvioAnt"] = $arrayDatosActual["direccionEnvio"];
                    $intActEnvio = 1;
                }

                if (strtolower(trim($entity->getEmailEnvio())) != strtolower(trim($arrayDatosActual["emailEnvio"])))
                {
                    $arrayParametrosEnvioNew["emailEnvio"] = $entity->getEmailEnvio();
                    $arrayParametrosEnvioNew["emailEnvioAnt"] = $arrayDatosActual["emailEnvio"];
                    $intActEnvio = 1;
                }

                if (strtolower(trim($entity->getTelefonoEnvio())) != strtolower(trim($arrayDatosActual["telefonoEnvio"])))
                {
                    $arrayParametrosEnvioNew["telefonoEnvio"] = $entity->getTelefonoEnvio();
                    $arrayParametrosEnvioNew["telefonoEnvioAnt"] = $arrayDatosActual["telefonoEnvio"];
                    $intActEnvio = 1;
                }

                if ($intIdCiudad != $arrayDatosActual["ant_id_ciudad"])
                {
                    $arrayParametrosEnvioNew["actCiudad"] = $entityCiudad->getNombreCanton();
                    $arrayParametrosEnvioNew["antCiudad"] = $entityCiudadAnt->getNombreCanton();
                    $intActEnvio = 1;
                }

                if ($intIdParroquia != $arrayDatosActual["ant_id_parroquia"])
                {
                    $arrayParametrosEnvioNew["actParroquia"] = $entityParroquia->getNombreParroquia();
                    $arrayParametrosEnvioNew["antParroquia"] = $entityParroquiaAnt->getNombreParroquia();
                    $intActEnvio = 1;
                }

                if ($intIdSector != $arrayDatosActual["idSectorAnterior"])
                {
                    $arrayParametrosEnvioNew["actSector"] = $entitySector->getNombreSector();
                    $arrayParametrosEnvioNew["antSector"] = $entitySectorAnt->getNombreSector();
                    $intActEnvio = 1;
                }

                if ($intActEnvio == 1)
                {
                    $arrayParametrosEnvioNew["nombreUsuario"] = $objPersona->getNombres() . " " . $objPersona->getApellidos();
                    $arrayParametrosEnvioNew["horaFechaMod"] = strval(date_format($entity->getFeUltMod(), "d-m-Y"));
                    $arrayParametrosEnvioNew["empresa"] = $objEmpresa->getPrefijo();
                    $strRazonSocial = $objSession->get('cliente')["razon_social"];
                    $strNombreCompleto = $objSession->get('cliente')["nombres"] . ' ' . $objSession->get('cliente')["apellidos"];
                    $arrayParametrosEnvioNew["nombreClienteRazonSocial"] = ($strRazonSocial) ? $strRazonSocial : $strNombreCompleto;

                    $strAsunto = "Notificación de modificación de datos de envío del cliente : " . $objInfoPunto->getLogin();
                    
                    $arrayDestinatarios = array();

                    $entityInfoPersona = $em->getRepository('schemaBundle:InfoPersona')
                            ->getContactosByLoginPersonaAndFormaContacto($objInfoPunto->getUsrVendedor(), 'Correo Electronico');

                    if ($entityInfoPersona)
                    {
                        foreach ($entityInfoPersona as $arrayPersonaFormaContato)
                        {
                            if (!empty($arrayPersonaFormaContato['valor']))
                            {
                                $arrayDestinatarios[] = $arrayPersonaFormaContato['valor'];
                            }
                        }
                    }

                    $arrayParametrosCab = array();
                    $arrayParametrosCab['strNombreParametroCab'] = 'MODI_DATO_ENV_FACT';
                    $arrayParametrosCab['strEstado'] = 'Activo';

                    $arrayAdmiParametroDet = $em->getRepository('schemaBundle:AdmiParametroDet')->findParametrosDet($arrayParametrosCab);

                    if ($arrayAdmiParametroDet)
                    {
                        foreach ($arrayAdmiParametroDet['arrayResultado'] as $arrayAdmiParamDet):
                            $arrayEncontrados[] = array('intIdParametroDet' => $arrayAdmiParamDet['intIdParametroDet'],
                                'strDescripcionDet' => $arrayAdmiParamDet['strDescripcionDet'],
                                'strValor1' => $arrayAdmiParamDet['strValor1'],
                                'strValor2' => $arrayAdmiParamDet['strValor2']
                            );
                        endforeach;
                    }

                    $objCanton = $em->getRepository('schemaBundle:AdmiCanton')
                            ->findOneById($objInfoPunto->getSectorId()->getParroquiaId()->getCantonId()->getId());

                    if (is_object($objCanton))
                    {
                        $intNum = count($arrayEncontrados);

                        if ($intNum > 0)
                        {
                            for ($intIndice = 0; $intIndice < $intNum; $intIndice++)
                            {
                                if ($objCanton->getRegion() == $arrayEncontrados[$intIndice]["strValor1"] && $objCanton->getEstado() == 'Activo')
                                {
                                    $arrayDestinatarios[] = $arrayEncontrados[$intIndice]["strValor2"];
                                }
                            }
                        }
                    }

                    $objEnvioPlantilla = $this->get('soporte.EnvioPlantilla');
                    $objEnvioPlantilla->generarEnvioPlantilla($strAsunto, $arrayDestinatarios, 'ACT-INFORMACION', 
                                             $arrayParametrosEnvioNew, '', '', '');
                }
            }
            

            $em->getConnection()->commit();
            $respuesta->setContent("Se agrego el registros con exito.");
            $response = new Response(json_encode(array('success'=>true)));
            $response->headers->set('Content-type', 'text/json');
            return $response;             
        } catch (\Exception $e) {
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            //$respuesta->setContent("error al tratar de agregar padres de facturacion registro. Consulte con el Administrador.");
            $respuesta->setContent($e->getMessage());
            $response = new Response(json_encode(array('success'=>false,    'errors' =>array('error' => $e->getMessage()))));
            $response->headers->set('Content-type', 'text/json');
            return $response;              
        }


        return $respuesta;
    }

    /**
     * Documentación para el método 'obtieneUltimoEstadoCliente'.
     *
     * Método que lista el historial del cliente
     *
     * @param int $id         ID de la Persona_Empresa_Rol
     * @param int $tipoRol    ID del Tipo de Rol
     * @param int $codEmpresa ID de la Empresa
     * 
     * @return Array Historial y Estado.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 11-08-2016
     * @since   1.0
     * Se reorganiza el código.
     * Se valida que los objetos existan para acceder a sus propiedades
     */
    public function obtieneUltimoEstadoCliente($id, $tipoRol, $codEmpresa)
    {
        $strEstado      = "";
        $arrayHistorial = array();
        $emComercial    = $this->getDoctrine()->getManager();
        
        //Obtiene el historial del prospecto(pre-cliente)
        $objPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                            ->getPersonaEmpresaRolPorPersonaPorTipoRolActivosPendientes($id, $tipoRol, $codEmpresa);
        if($objPersonaEmpresaRol)
        {
            $objInfoPerEmpRolHisto = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolHisto');
            $arrayHistorial        = $objInfoPerEmpRolHisto->findHistorialPorPersonaEmpresaRol($objPersonaEmpresaRol->getId());
            $arrayUltimoEstado     = $objInfoPerEmpRolHisto->findUltimoEstadoPorPersonaEmpresaRol($objPersonaEmpresaRol->getId());
            
            if($arrayUltimoEstado && count($arrayUltimoEstado) > 0)
            {
                $entityUltimoEstado = $objInfoPerEmpRolHisto->find($arrayUltimoEstado[0]['ultimo']);
                $strEstado          = $entityUltimoEstado->getEstado();
            }
        }
        
        return array('historial' => $arrayHistorial, 'estado' => $strEstado);
    }

    public function obtieneUltimoEstadoClientePorPersonaEmpresaRol($idPersonaEmpresaRol,$tipoRol,$codEmpresa){
		$estado="";
		$historial="";
		$em = $this->getDoctrine()->getManager();
        $historial = $em->getRepository('schemaBundle:InfoPersonaEmpresaRolHisto')->findByPersonaEmpresaRolId($idPersonaEmpresaRol);
        $ultimoEstado = $em->getRepository('schemaBundle:InfoPersonaEmpresaRolHisto')->findUltimoEstadoPorPersonaEmpresaRol($idPersonaEmpresaRol);        
        $idUltimoEstado=$ultimoEstado[0]['ultimo'];
		if ($idUltimoEstado){
			$entityUltimoEstado=$em->getRepository('schemaBundle:InfoPersonaEmpresaRolHisto')->find($idUltimoEstado);
			$estado=$entityUltimoEstado->getEstado();
		}
		return $datos= array('historial'=>$historial,'estado'=>$estado);
	} 

    /**
     * @Secure(roles="ROLE_311-3077")
     * Documentación para el método 'cambioRazonSocialAction'.
     *
     * Método que Renderiza la ventana para la reasignación de la razón social del cliente.
     *
     * @return Render Pantalla Cambio Razón Social.
     * 
     * @author Unknow
     * @version 1.0 Unknow
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 04-04-2016
     * Verificación del Cliente VIP para agregarlo a la sesión.
     * 
     * Se agregan campos Nuevos CarnetConadis, EsPrepago, PagaIva, ContribuyenteEspecial y Combo de Oficinas de Facturacion
     * para el caso de ser empresa Telconet se deben pedir dichos campos, en el caso de empresa Megadatos se deben setear los
     * valores por dafault
     * Se agrega seguridad para la opcion, misma de Cambio de Razon Social por Punto
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.2 06-06-2016.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.3 08-06-2016
     * Se inicializan los valores de sesión del cliente
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.3 08-06-2016
     * Se inicializa la variable $idperRef
     * 
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.4 28-08-2017 - Se agrega formularios para los datos del contrato, formas de pago y documentos digitales inicializados desde el 
     *                           controlador.
     * 
     * @author Gustavo Narea <gnarea@telconet.ec>
     * @version 1.5 18-09-2020 - Se parametriza limite  en la fecha de vencimiento de una tarjeta.
     * 
     * @author : Néstor Naula <nnaulal@telconet.ec>
     * @version 1.6 18-10-2020
     * Se agrega los parámetros obligatorios de imagenes a ingresar para MD
     *
     * @author : Alberto Arias <farias@telconet.ec>
     * @version 1.7 06-12-2021
     * Se agrega identificador para determinar si el cliente tiene el producto el canal del futbol
     *
     * @author Jefferson Alexy Carrillo <jacarrillo@telconet.ec>
     * @version 1.0 10-08-2022 - Se elimina consumo de representante legal por nueva implemnetacion con ms
     *
     */
    public function cambioRazonSocialAction($id, $idper)
    {
        $session  = $this->get('request')->getSession();
        $intIdPais = $session->get('intIdPais');
        $idperRef = null;
  
        // Se inicializan los valores de la sesión.
        $session->set('cliente', '');
        $session->set('ptoCliente','');
        $session->set('clienteContacto','');
        $session->set('puntoContactos','');
        $session->set('menu_modulo_activo', '');
        $session->set('nombre_menu_modulo_activo', '');
        $session->set('id_menu_modulo_activo', '');
        $session->set('imagen_menu_modulo_activo', '');
        $session->set('esVIP', '');
        
        $em_seguridad = $this->getDoctrine()->getManager("telconet_seguridad");
        $emGeneral    = $this->getDoctrine()->getManager('telconet_general');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("8", "1");
        $session->set('menu_modulo_activo', $entityItemMenu->getNombreItemMenu());
        $session->set('nombre_menu_modulo_activo', $entityItemMenu->getTitleHtml());
        $session->set('id_menu_modulo_activo', $entityItemMenu->getId());
        $session->set('imagen_menu_modulo_activo', $entityItemMenu->getUrlImagen());
        
        $em = $this->getDoctrine()->getManager();
        $emfn = $this->getDoctrine()->getManager('telconet_financiero');
        $request=$this->get('request');
        $codEmpresa=$request->getSession()->get('idEmpresa');
        
        $strPrefijoEmpresa = $request->getSession()->get('prefijoEmpresa');
        $boolExisteECDF = false;
        
        $entity = $em->getRepository('schemaBundle:InfoPersona')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoPersona entity.');
        }
        $entityContrato = $em->getRepository('schemaBundle:InfoContrato')->findContratosPorEmpresaPorEstadoPorPersona('Activo',$codEmpresa,$id);
        $deleteForm = $this->createDeleteForm($id);
        $entityPersonaRef = $em->getRepository('schemaBundle:InfoPersonaReferido')->findPorPersona($entity->getId());
        $referido = null;
        if ($entityPersonaRef) {
            $referido = $entityPersonaRef->getReferidoId();
            $idperRef = $entityPersonaRef->getRefPersonaEmpresaRolId();
        }
        $cliente = $em->getRepository('schemaBundle:InfoPersona')->getPersonaParaSession($codEmpresa, $id);
        
        $session->set('cliente', $cliente);
        
        $strEsVip = '';
            
        if($strPrefijoEmpresa == 'TN')
        {
            // Buscamos en InfoContratoDatoAdicional para verificar que sea cliente VIP
            $arrayParams        = array('ID_PER'  => $idper,
                                        'EMPRESA' =>  $codEmpresa,
                                        'ESTADO'  => 'Activo');
            $entityContratoDato = $em->getRepository('schemaBundle:InfoContratoDatoAdicional')->getResultadoDatoAdicionalContrato($arrayParams);
            $strEsVip           = $entityContratoDato && $entityContratoDato->getEsVip() ? 'Sí' : 'No';
        }
        
        $session->set('esVIP', $strEsVip);

        //Obtiene el ultimo estado del cliente o pre-cliente
		$datosHistorial=$this->obtieneUltimoEstadoCliente($id,'Cliente',$codEmpresa);
		$historial=$datosHistorial['historial'];
		$estado=$datosHistorial['estado'];
		
		//Obtiene formas de contacto
		$formasContacto=null;
		$arrformasContacto=$em->getRepository('schemaBundle:InfoPersonaFormaContacto')->findPorEstadoPorPersona($id,'Activo',9999999,1,0);
		if($arrformasContacto['registros'])
			$formasContacto=$arrformasContacto['registros'];
		
        //Recorre el historial y separa en arreglos cada estado
        $i=0;$creacion=null;$convertido=null;$eliminado=null;$ultMod=null;
			foreach($historial as $dato):
			//echo 'entro';
            if ($i==0){
                $creacion=array('estado'=>$dato->getEstado(),'usrCreacion'=>$dato->getUsrCreacion(),'feCreacion'=>$dato->getFeCreacion(),'ipCreacion'=>$dato->getIpCreacion());
            }
            if($i>0){
                if($dato->getEstado()=='Eliminado'){
                    $eliminado=array('estado'=>$dato->getEstado(),'usrCreacion'=>$dato->getUsrCreacion(),'feCreacion'=>$dato->getFeCreacion(),'ipCreacion'=>$dato->getIpCreacion());   
                }else{
                    $ultMod=array('estado'=>$dato->getEstado(),'usrCreacion'=>$dato->getUsrCreacion(),'feCreacion'=>$dato->getFeCreacion(),'ipCreacion'=>$dato->getIpCreacion());   
                }
            }
            $i++;
        endforeach;       
        
		$entityCambio = new InfoPersona();
        $form = $this->createForm(new ClienteType(array( 'empresaId'          => $codEmpresa,
                                                         'oficinaFacturacion' => null,
                                                         'tieneNumeroConadis' => 'N',
                                                         'esPrepago'          => 'S',
                                                         'pagaIva'            => 'S'
                                                       )
                                                 ), $entityCambio);		
		//VERIFICA DEUDA DEL CLIENTE
		$pagosController=new InfoPagoCabController();
		$pagosController->setContainer($this->container);
		$personaEmpresaRol=$em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($idper);
                $admiRol = $em->getRepository('schemaBundle:AdmiRol')->find($personaEmpresaRol->getEmpresaRolId()->getRolId()); 
                
		$puntosCliente=$em->getRepository('schemaBundle:InfoPunto')->findByPersonaEmpresaRolId($idper);
		$valor=0;
		foreach($puntosCliente as $pto)
    {	
			//$valor=$valor+$pagosController->obtieneSaldoPorPunto($pto->getId());
        $saldoarr=$emfn->getRepository('schemaBundle:InfoPagoCab')->obtieneSaldoPorPunto($pto->getId());
        $valor=$valor+$saldoarr[0]['saldo'];
      
        $arrayServiciosActivos = $em->getRepository('schemaBundle:InfoServicio')
        ->findServiciosPorEmpresaPorPuntoIdPorEstado($codEmpresa, $pto->getId(), "Activo");
        $arrayServicios = $arrayServiciosActivos['registros'];
        foreach($arrayServicios as $serv)
        {
            if(($strPrefijoEmpresa == 'MD' || $strPrefijoEmpresa == 'EN' )&& is_object($serv->getProductoId()) 
              && $serv->getProductoId()->getNombreTecnico() == "ECDF")
            {
                  $boolExisteECDF = true;
                  break;
            }
        }
    }
        
        //Informacion referente al contrato
        $arrayTipoDocumentos = array();
        $objTiposDocumentos  = $emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')->findByEstado("Activo");  
        foreach ( $objTiposDocumentos as $objTiposDocumentos )
        {   
           $arrayTipoDocumentos[$objTiposDocumentos->getId()] = $objTiposDocumentos->getDescripcionTipoDocumento();
        }

        $arrayListaDocumentoSubir = array();
        if($strPrefijoEmpresa == 'MD' || $strPrefijoEmpresa == 'EN')
        {
            $objTiposDocumentosSubir  = $emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
                                         ->findBy(array("estado"              => "Activo",
                                                        "codigoTipoDocumento" => array("CED","CEDR","FOT")));                   
            foreach ( $objTiposDocumentosSubir as $objTiposDocumentos )
            {   
                $arrayListaDocumentoSubir[$objTiposDocumentos->getId()] = $objTiposDocumentos->getDescripcionTipoDocumento();
            }
        }
        
        $objContratoCambioRazonSocial = new InfoContrato();
        $formInfoContrato             = $this->createForm(new InfoContratoType(array('validaFile'=>true)), $objContratoCambioRazonSocial);
        $formInfoContrato             = $formInfoContrato->createView();
        
        $objContratoFormaPago         = new InfoContratoFormaPago();
        
        $arrayAdmiParametroCabAnio  = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
        ->findOneBy(array("nombreParametro" => "ANIO_VIGENCIA_TARJETA",
                          "estado"          => "Activo"));
        if(is_object($arrayAdmiParametroCabAnio))
        {
            $arrayParamDetAnios = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->findBy(array("parametroId" => $arrayAdmiParametroCabAnio,
                                                        "estado"      => "Activo"));
            if ($arrayParamDetAnios)
            {
                $intAnioVencimiento = $arrayParamDetAnios[0]->getValor1();
            }
        }

        $objFormInfoFormaPago            = $this->createForm(new InfoContratoFormaPagoType(
                                                            array("intIdPais"=>$intIdPais,
                                                            "intAnioVencimiento"=>$intAnioVencimiento)), 
                                                            $objContratoFormaPago);
        $objFormInfoFormaPago            = $objFormInfoFormaPago->createView();
        
        $form_documentos              = $this->createForm(new InfoDocumentoType( array('validaFile'          =>true,
                                                                                       'arrayTipoDocumentos' =>$arrayTipoDocumentos)),
                                                          new InfoDocumento());
        $form_documentos              = $form_documentos->createView();

        $entityContratoDatoAdicional    = new InfoContratoDatoAdicional();
        $objFormDatoAdicionales         = $this->createForm(new InfoContratoDatoAdicionalType(), $entityContratoDatoAdicional);     
        
        // Generacion de la fecha
        $strFecha_act                 = date("Y-m-d");
        $strFecha                     = date('Y-m-d', strtotime("+12 months $strFecha_act"));

        /* @var $serviceInfoContrato \telconet\comercialBundle\Service\InfoContratoService */
        $serviceInfoContrato          = $this->get('comercial.InfoContrato');
        $objAdmiTipoContrato          = $serviceInfoContrato->obtenerTiposContrato($codEmpresa);

        $strEsCoordinadorMD = '1';

        return $this->render('comercialBundle:cliente:cambioRazonSocial.html.twig',
                             array('item'                   => $entityItemMenu,
                                   'entity'                 => $entity,
                                   'esCoordinadorMD'          => $strEsCoordinadorMD,
                                   'delete_form'            => $deleteForm->createView(),
                                   'form'                   => $form->createView(),
                                   'referido'               => $referido,
                                   'creacion'               => $creacion,
                                   'ultMod'                 => $ultMod,
                                   'eliminado'              => $eliminado,
                                   'contrato'               => $entityContrato,
                                   'estado'                 => $estado,
                                   'formasContacto'         => $formasContacto,
                                   'deuda'                  => round($valor, 2),
                                   'idper'                  => $idper,
                                   'prefijoEmpresa'         => $strPrefijoEmpresa,
                                   'rol'                    => $admiRol->getTipoRolId()->getDescripcionTipoRol(),
                                   'idperRef'               => $idperRef,
                                   'strFecha'               => $strFecha,
                                   'arrayTipoDocumentos'    => $arrayTipoDocumentos,
                                   'formInfoContrato'       => $formInfoContrato,
                                   'formInfoFormaPago'      => $objFormInfoFormaPago,
                                   'form_documentos'        => $form_documentos,
                                   'objAdmiTipoContrato'    => $objAdmiTipoContrato,
                                   'arrayListaDocumentoSubir'=> $arrayListaDocumentoSubir,
                                   'formDatoAdicionales'     => $objFormDatoAdicionales->createView(),
                                   'boolExisteECDF'          => $boolExisteECDF
                                ));
    }

    /**
     * Documentación para el método 'cambiarRazonSocialAction'.
     *
     * Método utilizado para realizar el cambio de Razón Social
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 2.0 14-05-2015
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 2.1 08-10-2015
     *
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 2.2 26-11-2015    Se agrega Validacion que no permita realizar Cambio de Razon Social hacia el mismo Numero de Identificacion.
     *
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 2.3 06-06-2016    Se agregan campos Nuevos CarnetConadis, EsPrepago, PagaIva, ContribuyenteEspecial y Combo de Oficinas de Facturacion
     *                            para el caso de ser empresa Telconet se deben pedir dichos campos, en el caso de empresa Megadatos se deben setear 
     *                            los valores por dafault
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 2.4 16-06-2015    Se agrega cancelación de LDAP de servicios antiguos y creación de LDAP nuevos servicios
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 2.5 27-06-2015    Se corrige cancelación de LDAP de servicios antiguos y creación de LDAP nuevos servicios
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 2.6 20-06-2016   
     * Se agrega informacion de Contactos a Clonarse en el Proceso de Cambio de Razon Social:
     * -Clono Contactos a nivel de Cliente hacia la nueva razon social
     * -Clono Contactos a nivel de Punto hacia los Logines de la nueva razon social           
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 2.7 11-08-2016   
     * Se incializa objeto clonado y se valida su instanciación.
     * Se elimina objeto oficina no utilizado.
     * Se establece un time-out más largo para clientes con gran cantidad de puntos
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 2.8 04-03-2017 - Al cliente nuevo '$entityPersonaEmpresaRol' se asocia en la columna personaEmpresaRolId la variable 
     *                           '$personaEmpresaRol' la cual corresponde al cliente antiguo de donde proviene el cambio de razon social que se está 
     *                           realizando. Adicional se agrega historial a nivel del servicio marcando como fecha de creación la fecha con la cual
     *                           se realiza el cálculo de los meses restantes para facturar el servicio.
     * 
     * @author Anabelle Penaherrera<apenaherrera@telconet.ec>
     * @version 2.9 02-05-2017 -Se guarda la Plantilla de Comisionistas a la nueva Razon social.
     * Se Cancela Plantilla de comisionistas en la antigua Razon Social
     * Se guarda Historial con fecha y observacion 'Se Confirmo el Servicio' en los servicios de la nueva Razon Social en base a la Fecha 
     * de Activacion o Confirmacion de Servicio de los servicios antiguos.
     * 
     * @author Anabelle Penaherrera<apenaherrera@telconet.ec>
     * @version 3.0 16-06-2017 - Se modifica el ingreso de Datos de Envio por Punto.
     * Se debe obtener la informacion de correos y telefonos del contacto de Facturacion del Punto o del cliente en ese orden. 
     * Funcion a llamar es la existente para la generacion del XML (DB_FINANCIERO.FNCK_COM_ELECTRONICO.GET_ADITIONAL_DATA_BYPUNTO)
     * Se debe considerar eliminar duplicidad de registros y solo se registrara un maximo de 2 correos y 2 telefonos separados por ;
     * La informacion del nombre_envio, direccion_envio sera tomados de la nueva Razon Social.
     * La informacion del Sector_id será tomado del Punto Clonado.  
     * 
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 3.1 23-09-2017 - Se agrega mejoras en el proceso de Cambio de Razón Social Tradicional:
     *                           1) Solicitar Formas de Pago desde la interfaaz.
     *                           2) Eliminar clonado de informacion de cliente origen (ya que esta informacion será solicitada por interfaz)
     *                           3) Subida de archivos digitales 
     *                           Unicamente para nuevos clientes.
     * 
     * @author Anabelle Peñaherrera<apenaherrera@telconet.ec>
     * @version 3.1 03-10-2017
     * Se agrega que cuando se realice CRS hacia cliente nuevo se asigne la caracteristrica CICLO_FACTURACION en el nuevo cliente o
     * cliente destino del cambio de razón social
     * Si se trata de un CRS hacia un Cliente ya existente, este deberá mantener su ciclo de facturación ya existente.
     * 
     * @author Jorge Guerrero<jguerrerop@telconet.ec>
     * @version 3.2 01-12-2017
     * Se agrega el parametro por empresa configurado en la admi_parametro
     *
     * @author Anabelle Peñaherrera<apenaherrera@telconet.ec>
     * @version 3.3 19-04-2018
     * Se modifica para que el CRS excluya los servicios en estado Cancel  if($serv->getEstado() != 'Cancel')
     * Ya que consideraba el estado 'Cancelado'
     * 
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 3.4
     * @since 15-05-2018
     * Cambios por ciclos de facturación:
     * Si el destino es un cliente nuevo, hereda el ciclo del cliente origen.
     * Si el destino es un cliente existente, se inserta en el historial de la InfoPersonaEmpresaRol para identificar cuando son de ciclos distintos.
     *
     * @author Anabelle Penaherrera<apenaherrera@telconet.ec>
     * @version 3.5 22-06-2018- Se agrega que se generen las caracteristicas de los servicios en estado activo, y se considera para el Producto
     *                          Fox_Primium que al clonar dichas caracteristicas se marque la caracteristica 'MIGRADO_FOX' en S.
     * 
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 3.6
     * @since 07-06-2018
     * Se hace el llamado a la función crearServicioCaracteristicaPorCRS para realizar la lógica que inserta la característica
     * en la tabla INFO_SERVICIO_CARACTERISTICA.
     *
     * @param Request $objRequest
     * 
     * @since 2.0
     * 
     * @author Hector Lozano<hlozano@telconet.ec>
     * @version 3.6
     * @since  13-07-2018
     * Se agrega validación para que no clone las características del servicio Netlifecloud cuando se realiza el Cambio de Razón Social.
     * En lugar de clonar se guardan nuevas caracteristicas del servicio Netlifecloud invocando al WebService de Intcomex. 
     * 
     * @author Hector Lozano<hlozano@telconet.ec>
     * @version 3.7
     * @since  25-07-2018
     * Se agrega validación para facturar a los servicios NetlifeCloud cancelados cuando se realiza cambio de Razón Social.
     * 
     * @author Hector Lozano<hlozano@telconet.ec>
     * @version 3.8
     * @since  31-08-2018
     * Se agrega validación para no clonar los descuentos al nuevo cliente cuando se realiza el Cambio de Razón Social.
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 3.9 26-12-2018 - Se realizan correcciones para Telconet Panama, no esta tomando la oficina en sesion cuando se trata de Panama.
     *
     *
     * @author Edgar Holguín<eholguin@telconet.ec>
     * @version 3.10
     * @since  26-12-2018
     * Se agrega validación para verificar si existen puntos con servicios en estados no permitidospara realizar Cambio de Razón Social. 
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 3.11
     * @since 21-01-2019
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 3.12 17-02-2019 Se agrega actualización de ldap para servicios Small Business y TelcoHome al aprobar el contrato 
     *                           por cambio de razón social
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 3.13 19-06-2019 Se agrega clonación de solicitudes de Agregar Equipo y Cambio de equipo por soporte en proceso de cambio
     *                          de razón social de puntos de un cliente
     * @since 3.12
     *
     * Se obtiene segun el tipo de caracteristica  ENLACE_DATOS o ES_BACKUP  los enlaces (Extremo-Concentrador) o (Backup- Principal)
     * existentes para un ID_SERVICIO Concentrador o Principal.       
     * Clona la informacion del enlace para asignar el ID_SERVICIO del nuevo CONCENTRADOR  o nuevo PRINCIPAL que fue generado por el cambio de 
     * Razón Social tradicional o por Login segun el caso.
     * Se genera Historial en el servicio Extremo o en servicio BACKUP indicando que se actualiza el enlace por el cambio de Razon Social    
     * Se cancela la caracteristica ENLACE_DATOS o ES_BACKUP que contiene la referencia al servicio CONCENTRADOR  o PRINCIPAL que fue Cancelado
     * por Cambio de Razon Social.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.5 20-06-2019  Se modifica operador en validación que compara números de identificación.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 3.15.5 17-01-2020 Se agrega registro de git shistorial con fecha de activación mínima del servicio origen.
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 3.16 12-10-2020 - Para productos Paramount y Noggin al momento de realizar un cambio se generan un nuevo usuario y password.
     * @since 3.15.5
     * 
     * @author Jonathan Mazon Sanchez <jmazon@telconet.ec>
     * @version 3.17 28-10-2020 - Se agrega bandera para realizar proceso a los productos Paramount y Noggin 
     *                            dentro de un plan o como producto adicional.
     * 
     * @author kevin ortiz <kcortiz@telconet.ec>
     * @version 3.18  22-10-2020  - Se agrega el proceso para solicitar nuevo suscriber id
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 3.19 06-11-2020 Se modifica el orden en que se están invocado los procesos que se ejecutan luego del commit del proceso principal
     *                          de cambio de razón social.
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 3.20 07-1-2021 - Se agrega codigo para acceder a la plantilla de envio de sms para productos Paramount y Noggin,
     *                            Se agrega validacion para que busque las caracteristicas del correo del producto, se envia el ID servicio.
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 3.21 21-04-2021 - Se adapta programación ya usada para W+AP para permitir clonación de solicitudes Extender Dual Band
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 3.22 09-08-2021 - Se parametriza validaciones para el Producto ECDF.
     * 
     * @author Jorge Luis Veliz <jlveliz@telconet.ec>
     * @version 3.22 17-08-2021 - Se agrega còdigo para replicar cortesias aceptadas en el Servicio
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 3.23 15-11-2021 - Se solicita regularizar la función precio para los productos de md.
     * 
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 3.24 10-01-2022 - Se agrega busqueda de codEmpresa al momento de validar la identificación
     * 
     * @author Katherine Solis <ksolis@telconet.ec>
     * @version 3.25 17-05-2022 - Se registra observación para identificar el usuario responsable de verificar ruc inválido
     * 
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 3.26 09-06-2022 - Se agrega opcion para crear logs indicando si servicio pierde o adquiere promociones.
     * 
     * @author Carlos Caguana <ccaguana@telconet.ec>
     * @version 3.27 05-07-2022 - Se agrega validación de la identificación del cliente
     * 
     * @author Alberto Arias <farias@telconet.ec>
     * @version 3.25 03-12-2021 - Se actualiza el proceso para agregar nuevo correo electronico en el producto ECDF
     * 
     * @author Alex Gomez <algomez@telconet.ec>
     * @version 3.29 10-08-2022 Se modifica estado de los puntos y servicios clonados por CRS tradicional y por punto 
     *                          cuando el contrato aun no ha sido autorizado. Aplica para MD y contrato digital.
     * 
     * @author Jessenia Piloso <jpiloso@telconet.ec>
     * @version 3.28 23-10-2022 - Se actualiza el proceso para agregar validaciones para el flujo de cambio de razon social 
     *                             de NetlifeCam Outdoor                                                   
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 3.30 19-01-2023 - Se envía por parámetro las variable strEsLogin el cúal se lo va a usar en la función de precio.
     * 
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 3.30 26-08-2022 - Se agrega consumo a MS por productos Konibit.
     *
     * @author Luis Farro <lfarro@telconet.ec>
     * @version 3.31 23-02-2023 - Se modifica el envio de productos. Se envia un solo array con todos los productos Konibit.
     *
     * 
     * @author Joel Broncano <jbroncano@telconet.ec>
     * @version 4.0 19-04-2023 - Soporte EN.
     * 
     * 
     */
    public function cambiarRazonSocialAction(Request $objRequest)
    {
        ini_set('max_execution_time', 9999999999);
        $serviceTokenCas       = $this->get('seguridad.TokenCas');
        /* @var $serviceInfoPunto \telconet\comercialBundle\Service\ConsumoKonibitService */
        $serviceKonibit        = $this->get('comercial.ConsumoKonibit');
        $datos_form            = $objRequest->request->get('clientetype');
        $array_formas_contacto = explode(",", $datos_form['formas_contacto']);
        $emComercial           = $this->getDoctrine()->getManager('telconet');
        $emGeneral             = $this->getDoctrine()->getManager("telconet_general");
        $emInfraestructura     = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emFinanciero          = $this->getDoctrine()->getManager('telconet_financiero');
        $emComuni              = $this->getDoctrine()->getManager('telconet_comunicacion');
        $intIdEmpresa          = $objRequest->getSession()->get('idEmpresa');
        $intIdOficina          = $objRequest->getSession()->get('idOficina');
        $strUsrCreacion        = $objRequest->getSession()->get('user');
        $strPrefijoEmpresa     = $objRequest->getSession()->get('prefijoEmpresa');
        $strClientIp           = $objRequest->getClientIp();
        $arrayServiciosLdap    = array();
        $strSituacion          = "Existente";
        $strEstadoActivo       = 'Activo';
        $serviceInfoServicio   = $this->get('comercial.InfoServicio');
        $arrayDatosFormFiles   = $objRequest->files->get('infodocumentotype');
        $arrayDatosFormTipos   = $objRequest->get('infodocumentotype');
        $arrayTipoDocumentos   = array();
        $serviceUtil           = $this->get('schema.Util');
        $intIdPuntoSession     = null;
        $strParaAsociadosServicios="PARAMETROS_ASOCIADOS_A_SERVICIOS_MD";

        

        $arrayParametros['strPrefijoEmpresa'] = $strPrefijoEmpresa;
        $arrayParametros['strEmpresaCod']     = $intIdEmpresa;
        $serviceComercial                     = $this->get('comercial.Comercial');
        $strAplicaCiclosFac                   = $serviceComercial->aplicaCicloFacturacion($arrayParametros);
        $strDatosRepresentanteLegal           = $objRequest->get('datosRepresentanteLegal');

        $serviceFoxPremium               = $this->get("tecnico.FoxPremium");
        $serviceCrypt                    = $this->get('seguridad.Crypt');
        $serviceTecnico                  = $this->get('tecnico.InfoServicioTecnico');
        $serviceServicioIPMP             = $this->get('tecnico.LicenciasKaspersky');
        $strStatusCliente                = 0;
        $arrayAdendumsExcluirRS          = array();
        $strFormaContrato                = $objRequest->get('formaContrato');
        $intContratoFisico               = $strFormaContrato == 'Contrato Fisico' ? 1 : 0;
        $intIdPersonaEmpresaRolDestino   = 0; 
        $servicePromociones              = $this->get('tecnico.Promociones');
        $arrayPuntosCRS                  =array();

        $arrayParamFuncionPrecio        = array('intIdCliente'          => $datos_form['antiguoIdCliente'],
                                                'intIdEmpresa'          => $intIdEmpresa,
                                                'strUsrCreacion'        => $strUsrCreacion,
                                                'strEsLogin'            =>  'N',
                                                'arrayLogin'            =>  null
                                            );


        $arrayDatosCliente      = $objRequest->request->get('clientetype');

        $strEstadoServicioPreactivo   = '';
        $boolAsignaEstadoPreactivo    = false;
        $strMensajeEstadoPreactivo    = '';

                              
        $arrayParamProducNetCam             = $serviceTecnico->paramProductosNetlifeCam();
                                 
        $intIdPersonEmpRolEmpl                    = $objRequest->getSession()->get('idPersonaEmpresaRol');
        $strMensajeCorreoECDF                     = "";
        $arrayParametrosECDF['boolCanalFutbol']   = false;
        $strTieneCorreoElectronico                = "NO";

        //ALMACENA PRODUCTOS KONIBIT
        $arrayListProdKon      = array(); 
        $arrayListProdKon      = [];

        //ALMACENA EL LOGIN ORIGEN
        $strLoginOrigenKon     = '';

        //ALMACENA EL COMPANY CODE ORIGEN
        $intCompCodeKon        = 0;

        //ALMACENA NOMBRE DEL PLAN DE INTERNET
        $strPlanInternet       = '';
        if(isset($datos_form["tieneCorreoElectronico"]) && !empty($datos_form["tieneCorreoElectronico"]))
        {
          $strTieneCorreoElectronico = $datos_form["tieneCorreoElectronico"];
        }
        if($strPrefijoEmpresa == 'EN')
        {
            $strParaAsociadosServicios="PARAMETROS_ASOCIADOS_A_SERVICIOS_EN";
        }
        
        if($strPrefijoEmpresa == 'MD' || $strPrefijoEmpresa == 'EN')
        {
            
            /* @var $serviceCliente ClienteService */
            $serviceCliente = $this->get('comercial.Cliente');
            $serviceCliente->funcionPrecioRegularizar($arrayParamFuncionPrecio);
        }

        if ($arrayDatosFormTipos != null )
        {
            foreach ($arrayDatosFormTipos as $arrayTipos)
            {                          
                foreach ( $arrayTipos as $intKeyTipo => $objValue )
                {                     
                    $arrayTipoDocumentos[$intKeyTipo] = $objValue;
                }
            }
        }
        
        $arrayServiciosNetlifeCloud = array();
        
        $arrayParametros = array_merge($objRequest->get('infocontratoformapagotype'),
                                        $objRequest->get('infocontratotype'),
                                        $objRequest->get('infocontratoextratype'),
                                        array('feFinContratoPost'        => $objRequest->get('feFinContratoPost')),
                                        array('arrayDatosFormFiles'      => $arrayDatosFormFiles),
                                        array('arrayTipoDocumentos'      => $arrayTipoDocumentos),
                                        array('strUsrCreacion'           => $strUsrCreacion),
                                        array('strClientIp'              => $strClientIp));
        
        $emComercial->getConnection()->beginTransaction();
        $emInfraestructura->getConnection()->beginTransaction();
        try
        {

            if(empty($arrayDatosCliente['identificacionCliente']))
            {
                throw new \Exception("No se encontró la identificación del cliente");
            }

            if(empty($arrayDatosCliente['tipoIdentificacion']))
            {
                throw new \Exception("No se encontró el tipo de identificación del cliente");
            }

            /* 
             * Verifica validez de la identificación ingresada
             * */
            $arrayParamValidaIdentifica = array('strTipoIdentificacion'     => $arrayDatosCliente['tipoIdentificacion'],
                                                'strIdentificacionCliente'  => $arrayDatosCliente['identificacionCliente'],
                                                'intIdPais'                 => "",
                                                'strCodEmpresa'             => $intIdEmpresa);
            $strValidacionRespuesta = $emComercial
                                           ->getRepository('schemaBundle:InfoPersona')
                                           ->validarIdentificacionTipo($arrayParamValidaIdentifica);
            
            if (!empty($strValidacionRespuesta))
            {
                $strValidacionRespuesta .= " Para el tipo identificación ".$arrayDatosCliente['tipoIdentificacion']." - "
                                            .$arrayDatosCliente['identificacionCliente'];
                throw new \Exception($strValidacionRespuesta,1);
            }

            //Consulta nuevo estado para servicios creados por Cambio de Razón Social por Login
            //previo a la autorizacion del contrato. Aplica MD y contrato digital
            if(($strPrefijoEmpresa === 'MD' || $strPrefijoEmpresa === 'EN'  )&& $intContratoFisico == 0)
            {
                $boolAsignaEstadoPreactivo = true;

                $arrayEstadosServicios = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getOne(
                                                        'ESTADOS_CAMBIO_RAZON_SOCIALXPUNTO',
                                                        'COMERCIAL',
                                                        'CAMBIO_RAZON_SOCIAL_POR_PUNTO',
                                                        '','','','','','',
                                                        $intIdEmpresa);
                
                if(isset($arrayEstadosServicios) && !empty($arrayEstadosServicios))
                {
                    $strEstadoServicioPreactivo = $arrayEstadosServicios["valor1"];
                }
                else
                {
                    $boolAsignaEstadoPreactivo = false;
                }

                $arrayParamObservacionHist = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getOne(
                                                        'OBSERVACION_CAMBIO_ESTADO_PREACTIVO',
                                                        'COMERCIAL',
                                                        'CAMBIO_RAZON_SOCIAL_POR_PUNTO',
                                                        'OBSERVACION_HIST_SERVICIO_PREACTIVO',
                                                        '','','','','',
                                                        $intIdEmpresa);
                
                if(isset($arrayParamObservacionHist) && !empty($arrayParamObservacionHist))
                {
                    $strMensajeEstadoPreactivo = $arrayParamObservacionHist["valor1"];
                }
                else
                {
                    throw new \Exception('No se encontro parametro por mensaje de confirmacion CRS');
                }
            }
          
            // Valido que no se pueda realizar cambio de razon social hacia el mismo cliente
            $objPersonaOrigen = $emComercial->getRepository('schemaBundle:InfoPersona')->find($datos_form['antiguoIdCliente']);
            
            if($objPersonaOrigen)
            {   
                if($objPersonaOrigen->getIdentificacionCliente() === $datos_form['identificacionCliente'])
                {
                    throw new \Exception('Error en el Numero de Identificacion, - '
                                         . 'No puede realizar Cambio de Razon Social hacia el mismo Cliente'
                                         . ' ['.$datos_form['identificacionCliente'].']');
                }
                else
                {
                    $arrayNombTecProdsSinActivarCRS     = array();
                    $arrayNombresTecnicosNotInCRSParams = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                    ->get($strParaAsociadosServicios,
                                                                          '',
                                                                          '',
                                                                          '',
                                                                          'CAMBIO_RAZON_SOCIAL',
                                                                          'NOMBRES_TECNICOS_PRODS_PERMITIDOS_SIN_ACTIVAR',
                                                                          '',
                                                                          '',
                                                                          '',
                                                                          $intIdEmpresa);
                    if(is_array($arrayNombresTecnicosNotInCRSParams) && count($arrayNombresTecnicosNotInCRSParams) > 0)
                    {
                        foreach($arrayNombresTecnicosNotInCRSParams as $arrayNombreTecnicoNotInCRSParam)
                        {   
                            $arrayNombTecProdsSinActivarCRS[] = $arrayNombreTecnicoNotInCRSParam['valor3'];
                        }
                    }
                    
                    $arrayParamServAsig                                     = array();
                    $arrayParamServAsig['intPersonaId']                     = $objPersonaOrigen->getId();  
                    $arrayParamServAsig['strEstadoCliente']                 = 'Activo';
                    $arrayParamServAsig['buscarPlanesYProductos']           = "SI";
                    $arrayParamServAsig['arrayNombresTecnicosProdNotIn']    = $arrayNombTecProdsSinActivarCRS;
                    $arrayAdmiParametroDet                                  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                                            ->get('ESTADOS_NO_PERMITIDOS_CRS',
                                                                                                  'FINANCIERO',
                                                                                                  'CAMBIO_RAZON_SOCIAL',
                                                                                                  "",
                                                                                                  "",
                                                                                                  "",
                                                                                                  "",
                                                                                                  "",
                                                                                                  "",
                                                                                                  $intIdEmpresa);
                    
                    $strMsj = 'Error. No se puede realizar Cambio de Razon Social, existen puntos con servicios en estados no permitidos ';

                    if($arrayAdmiParametroDet && count($arrayAdmiParametroDet) > 0)
                    {
                        $arrayEstados = array();
                        foreach($arrayAdmiParametroDet as $arrayParametro)
                        {
                            $strMsj        .= $arrayParametro['valor1'].' ';
                            $arrayEstados[] = $arrayParametro['valor1'];
                        }
                    }

                    $arrayParamServAsig['arrayEstadosServicios'] = $arrayEstados;
                    $arrayParamServAsig['strEmpresaCod']         = $intIdEmpresa;

                    $arrayResultado = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                  ->hasServiciosByParametros($arrayParamServAsig);         
             
                                 
                    if($arrayResultado['boolServiciosAT'])
                    {
                        $strMsj .= '. Logines: '; 
                        $arrayLoginesCR = $arrayResultado['arrayLogines'];
                        
                        foreach($arrayLoginesCR as $arrayLogines)
                        {
                            $strMsj .= $arrayLogines['login'].' ';
                        }                        
                        throw new \Exception($strMsj);      
                    }
                    
                }
            }
            else
            {
                throw new  \Exception("No existe la Persona a la que se intenta cambiar su razón social");
            }
            
            if($datos_form['yaexiste'] != 'S')
            {
                $entity = $emComercial->getRepository('schemaBundle:InfoPersona')
                                      ->findOneByIdentificacionCliente($datos_form['identificacionCliente']);
                if(!$entity)
                {
                    if(!empty($datos_form['tipoIdentificacion']))
                    {
                        $entity = new InfoPersona();
                        $entity->setTipoIdentificacion($datos_form['tipoIdentificacion']);
                        $entity->setIdentificacionCliente($datos_form['identificacionCliente']);
                        $entity->setFeCreacion(new \DateTime('now'));
                        $entity->setUsrCreacion($strUsrCreacion);
                        $entity->setIpCreacion($objRequest->getClientIp());
                    }
                    else
                    {
                        throw new  \Exception("No se encontró el tipo de identificación");
                    }
                }
                else
                {
                    //PONE ESTADO INACTIVO A TODOS LAS FORMAS DE CONTACTO DE LA PERSONA QUE tengan estado ACTIVO
                    /* @var $serviceInfoPersonaFormaContacto \telconet\comercialBundle\Service\InfoPersonaFormaContactoService */
                    $serviceInfoPersonaFormaContacto = $this->get('comercial.InfoPersonaFormaContacto');
                    $serviceInfoPersonaFormaContacto->inactivarPersonaFormaContactoActivasPorPersona($entity->getId(), $strUsrCreacion);
                }
            }
            $a = 0;
            $x = 0;
            for($i = 0; $i < count($array_formas_contacto); $i++)
            {
                if($a == 3)
                {
                    $a = 0;
                    $x++;
                }
                if($a == 1)
                {
                    $formas_contacto[$x]['formaContacto'] = $array_formas_contacto[$i];
                }
                if($a == 2)
                {
                    $formas_contacto[$x]['valor'] = $array_formas_contacto[$i];
                }
                $a++;
            }
            if($datos_form['yaexiste'] != 'S')
            {
                $entity->setTipoEmpresa($datos_form['tipoEmpresa']);
                $entity->setTipoTributario($datos_form['tipoTributario']);
                $entity->setRazonSocial($datos_form['razonSocial']);
                $entity->setRepresentanteLegal($datos_form['representanteLegal']);
                $entity->setNacionalidad($datos_form['nacionalidad']);
                $entity->setDireccionTributaria($datos_form['direccionTributaria']);
                //Guardo campos nuevos
                if($strPrefijoEmpresa == 'TN')
                {
                    $entity->setContribuyenteEspecial($datos_form ['contribuyenteEspecial']);
                    $entity->setPagaIva($datos_form ['pagaIva']);
                    $entity->setNumeroConadis($datos_form ['numeroConadis']);
                }
                else
                {
                    $entity->setPagaIva('S');
                }
                
                if(!$datos_form['tipoEmpresa'])
                {
                    $entity->setGenero($datos_form['genero']);
                    $entity->setEstadoCivil($datos_form['estadoCivil']);
                    $entity->setNombres($datos_form['nombres']);
                    $entity->setApellidos($datos_form['apellidos']);
                    $entity->setFechaNacimiento(date_create($datos_form['fechaNacimiento']['year'] . '-' . 
                                                            $datos_form['fechaNacimiento']['month'] . '-' . 
                                                            $datos_form['fechaNacimiento']['day']));
                    $entityAdmiTitulo = $emComercial->getRepository('schemaBundle:AdmiTitulo')->find($datos_form['tituloId']);
                    if($entityAdmiTitulo)
                    {
                        $entity->setTituloId($entityAdmiTitulo);
                    }
                }
                $entity->setOrigenProspecto('N');
                $entity->setEstado('Activo');
                $emComercial->persist($entity);
                $emComercial->flush();
                $strSituacion = "Nuevo";
            }

            $personaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                             ->getPersonaEmpresaRolPorPersonaPorTipoRolActivosPendientes($datos_form['antiguoIdCliente'], 
                                                                                                         'Cliente', 
                                                                                                         $intIdEmpresa);
            if($personaEmpresaRol === null)
            {
                throw new \Exception("No se ha podido encontrar al Cliente " . $objPersonaOrigen->getIdentificacionCliente());
            }
            
            if ('S' == $strAplicaCiclosFac)
            {
                //Obtengo Característica de CICLO_FACTURACION
                $objCaracteristicaCiclo = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                      ->findOneBy(array("descripcionCaracteristica" => "CICLO_FACTURACION",
                                                                        "estado"                    => "Activo"));
                if(!is_object($objCaracteristicaCiclo))
                {
                    throw new \Exception('No existe Caracteristica CICLO_FACTURACION - No se pudo generar el Cambio de Razón Social');
                }
                //SE OBTIENE EL CICLO DEL CLIENTE
                $objPerEmpRolCaracOrigen = $emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRolCarac")
                                                       ->findOneBy(array("personaEmpresaRolId" => $personaEmpresaRol->getId(),
                                                                         "estado"              => "Activo",
                                                                         "caracteristicaId"    => $objCaracteristicaCiclo->getId()));
                $objAdmiCicloOrigen = $emComercial->getRepository("schemaBundle:AdmiCiclo")->find($objPerEmpRolCaracOrigen->getValor());
            }

            $puntos_result = $emComercial->getRepository('schemaBundle:InfoPunto')
                                         ->findPtosPorEmpresaPorClientePorRol(   $intIdEmpresa, 
                                                                        $datos_form['antiguoIdCliente'], 
                                                                        "", 
                                                                        "Cliente", 
                                                                        99999999, 
                                                                        1, 
                                                                        0, 
                                                                        '');
            $puntos = $puntos_result['registros'];

            if($datos_form['yaexiste'] != 'S')
            {
                $entityEmpresaRol        = $emComercial->getRepository('schemaBundle:InfoEmpresaRol')
                                                       ->findPorNombreTipoRolPorEmpresa('Cliente', $intIdEmpresa);
                $entityPersonaEmpresaRol = new InfoPersonaEmpresaRol();
                $entityPersonaEmpresaRol->setEmpresaRolId($entityEmpresaRol);
                $entityPersonaEmpresaRol->setPersonaId($entity);
                
                //Guardo Oficina de Facturacion y marco cliente Prepago
                if($strPrefijoEmpresa== 'TN')
                {
                    $objInfoOficinaGrupoTN = $emComercial->getRepository('schemaBundle:InfoOficinaGrupo')
                                                         ->find((int) $datos_form['idOficinaFacturacion']);
                    if(!$objInfoOficinaGrupoTN)
                    {
                        throw new \Exception('No encontro Oficina [' . $datos_form['idOficinaFacturacion'] . '] para la creacion del Cliente - '
                        . 'No se pudo generar el Cambio de Razon Social');
                    }
                    $entityPersonaEmpresaRol->setOficinaId($objInfoOficinaGrupoTN);
                    $entityPersonaEmpresaRol->setEsPrepago($datos_form ['esPrepago']);
                }
                else
                {
                    if($strPrefijoEmpresa == 'TNP')
                    {
                        $objInfoOficinaGrupo = $emComercial->getRepository('schemaBundle:InfoOficinaGrupo')->find($intIdOficina);
                        if(!$objInfoOficinaGrupo)
                        {
                            throw new \Exception('No encontro Oficina [' . $intIdOficina . '] para la creacion del Cliente - '
                            . 'No se pudo generar el Cambio de Razon Social');                           
                        }
                        $entityPersonaEmpresaRol->setOficinaId($objInfoOficinaGrupo);
                        $entityPersonaEmpresaRol->setEsPrepago('S');
                    }
                }
                
                if ($strAplicaCiclosFac == 'S' )
                {
                    $objInfoOficinaGrupo = $emComercial->getRepository('schemaBundle:InfoOficinaGrupo')->find($intIdOficina);
                    if(!$objInfoOficinaGrupo)
                    {
                        throw new \Exception('No encontro Oficina [' . $intIdOficina . '] para la creacion del Cliente - '
                        . 'No se pudo generar el Cambio de Razon Social');
                    }
                    $entityPersonaEmpresaRol->setOficinaId($objInfoOficinaGrupo);
                    $entityPersonaEmpresaRol->setEsPrepago('S');

                    //Inserto Caracteristica de CICLO_FACTURACION en el nuevo cliente destino del CRS
                    $objPersEmpRolCaracCiclo = new InfoPersonaEmpresaRolCarac();
                    $objPersEmpRolCaracCiclo->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
                    $objPersEmpRolCaracCiclo->setCaracteristicaId($objCaracteristicaCiclo);
                    $objPersEmpRolCaracCiclo->setValor($objPerEmpRolCaracOrigen->getValor());
                    $objPersEmpRolCaracCiclo->setFeCreacion(new \DateTime('now'));
                    $objPersEmpRolCaracCiclo->setUsrCreacion($strUsrCreacion);
                    $objPersEmpRolCaracCiclo->setEstado('Activo');
                    $objPersEmpRolCaracCiclo->setIpCreacion($objRequest->getClientIp());
                    $emComercial->persist($objPersEmpRolCaracCiclo);

                    //Inserto Historial de creacion de caracteristica de CICLO_FACTURACION en el Cliente                
                    $objPersEmpRolCaracCicloHisto = new InfoPersonaEmpresaRolHisto();
                    $objPersEmpRolCaracCicloHisto->setUsrCreacion($strUsrCreacion);
                    $objPersEmpRolCaracCicloHisto->setFeCreacion(new \DateTime('now'));
                    $objPersEmpRolCaracCicloHisto->setIpCreacion($objRequest->getClientIp());
                    $objPersEmpRolCaracCicloHisto->setEstado('Activo');
                    $objPersEmpRolCaracCicloHisto->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
                    $objPersEmpRolCaracCicloHisto->setObservacion('Se generó cambio de Razón Social y se asignó Ciclo de Facturación: '
                        . $objAdmiCicloOrigen->getNombreCiclo());
                    $emComercial->persist($objPersEmpRolCaracCicloHisto);
                }
                                
                $entityPersonaEmpresaRol->setFeCreacion(new \DateTime('now'));
                $entityPersonaEmpresaRol->setUsrCreacion($strUsrCreacion);
                $entityPersonaEmpresaRol->setEstado('Activo');
                $emComercial->persist($entityPersonaEmpresaRol);
                $emComercial->flush();

                //REFERIDOS
                $referidos = $emComercial->getRepository('schemaBundle:InfoPersonaReferido')->findByPersonaEmpresaRolId($personaEmpresaRol->getId());
                foreach($referidos as $referido)
                {
                    $entityReferidoClonado = clone $referido;
                    $entityReferidoClonado->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
                    $entityReferidoClonado->setEstado('Activo');
                    $entityReferidoClonado->setFeCreacion(new \DateTime('now'));
                    $entityReferidoClonado->setIpCreacion($objRequest->getClientIp());
                    $emComercial->persist($entityReferidoClonado);
                    $emComercial->flush();

                    $referido->setEstado('Inactivo');
                    $emComercial->persist($referido);
                    $emComercial->flush();
                }
                //Clono Contactos Activos a nivel de Cliente hacia la nueva razon social
                $arrayContactos = $emComercial->getRepository('schemaBundle:InfoPersonaContacto')
                                     ->findByPersonaEmpresaRolIdYEstado($personaEmpresaRol->getId(),'Activo');                
                if( $arrayContactos )
                {
                    foreach( $arrayContactos as $contacto )
                    {  
                        $objContactoClonado = clone $contacto;
                        $objContactoClonado->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
                        $objContactoClonado->setFeCreacion(new \DateTime('now'));
                        $objContactoClonado->setIpCreacion($objRequest->getClientIp());   
                        $objContactoClonado->setUsrCreacion($strUsrCreacion);
                        $emComercial->persist($objContactoClonado);
                        $emComercial->flush();
                    }
                }

                //ReGISTRA LAS FORMAS DE CONTACTO DEL CLIENTE
                for($i = 0; $i < count($formas_contacto); $i++)
                {
                    $entityAdmiFormaContacto = $emComercial->getRepository('schemaBundle:AdmiFormaContacto')
                                                  ->findPorDescripcionFormaContacto($formas_contacto[$i]["formaContacto"]);
                    
                    $entity_persona_forma_contacto = new InfoPersonaFormaContacto();
                    $entity_persona_forma_contacto->setValor($formas_contacto[$i]["valor"]);
                    $entity_persona_forma_contacto->setEstado("Activo");
                    $entity_persona_forma_contacto->setFeCreacion(new \DateTime('now'));
                    $entity_persona_forma_contacto->setFormaContactoId($entityAdmiFormaContacto);
                    $entity_persona_forma_contacto->setIpCreacion($objRequest->getClientIp());
                    $entity_persona_forma_contacto->setPersonaId($entity);
                    $entity_persona_forma_contacto->setUsrCreacion($strUsrCreacion);
                    $emComercial->persist($entity_persona_forma_contacto);
                    $emComercial->flush();
                }
                
                if(($strPrefijoEmpresa != 'MD' &&  $strPrefijoEmpresa != 'EN') || $intContratoFisico == 1)
                {
                    // Ingreso Contrato para el nuevo Pre-cliente
                    $objInfoContrato = new InfoContrato();
                    $objInfoContrato->setValorAnticipo($arrayParametros['valorAnticipo']);
                    $objInfoContrato->setNumeroContratoEmpPub($arrayParametros['numeroContratoEmpPub']);

                    // Obtener la numeracion de la tabla admi_numeracion
                    $strSecuenciaAsig  = null;
                    $strNumeroContrato = null;
                    $objAdmiNumeracion    = $emComercial->getRepository('schemaBundle:AdmiNumeracion')
                                                        ->findByEmpresaYOficina($intIdEmpresa, $intIdOficina, "CON");

                    if( is_object($objAdmiNumeracion) )
                    {
                        $strSecuenciaAsig  = str_pad($objAdmiNumeracion->getSecuencia(), 7, "0", STR_PAD_LEFT);
                        $strNumeroContrato = $objAdmiNumeracion->getNumeracionUno() . "-" . $objAdmiNumeracion->getNumeracionDos() .
                                            "-" . $strSecuenciaAsig;
                        $objInfoContrato->setNumeroContrato($strNumeroContrato);
                    }

                    //Fecha Fin del contrato.
                    if($arrayParametros['feFinContratoPost'] instanceof \DateTime)
                    {
                        $objInfoContrato->setFeFinContrato($arrayParametros['feFinContratoPost']);
                    }
                    else
                    {
                        $arrayStartExp = explode("-", $arrayParametros['feFinContratoPost']);
                        $strFechaFin   = date("Y-m-d H:i:s", strtotime($arrayStartExp[0] . "-" . $arrayStartExp[1] . "-" . $arrayStartExp[2]));
                        $objInfoContrato->setFeFinContrato(date_create($strFechaFin));
                    }

                    $objAdmiFormaPago    = $emComercial->getRepository('schemaBundle:AdmiFormaPago')->find($arrayParametros['formaPagoId']);
                    $objAdmiTipoContrato = $emComercial->getRepository('schemaBundle:AdmiTipoContrato')->find($arrayParametros['tipoContratoId']);

                    $objInfoContrato->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
                    $objInfoContrato->setFormaPagoId($objAdmiFormaPago);
                    $objInfoContrato->setTipoContratoId($objAdmiTipoContrato);
                    $objInfoContrato->setFeCreacion(new \DateTime('now'));
                    $objInfoContrato->setUsrCreacion($strUsrCreacion);
                    $objInfoContrato->setEstado($strEstadoActivo);
                    $objInfoContrato->setIpCreacion($arrayParametros['strClientIp']);
                    $emComercial->persist($objInfoContrato);
                    
                    if( is_object($objAdmiNumeracion) )
                    {
                        // Actualizo la numeracion en la tabla
                        $intNumeroAct = ($objAdmiNumeracion->getSecuencia() + 1);
                        $objAdmiNumeracion->setSecuencia($intNumeroAct);
                        $emComercial->persist($objAdmiNumeracion);
                    }
                    // Llamo al service para validar el numero de tarjeta de la CTA y para guardar los docs. digitales
                    /* @var $serviceInfoContrato \telconet\comercialBundle\Service\InfoContratoService */
                    $serviceInfoContrato = $this->get('comercial.InfoContrato');
                        
                    // Informacion de las formas de pagos con datos adicionales
                    if( ($arrayParametros['tipoCuentaId'] != '')     && ($arrayParametros['bancoTipoCuentaId'] != '') &&
                        ($arrayParametros['numeroCtaTarjeta'] != '') && ($arrayParametros['titularCuenta'] != ''))
                    {
                        
                        //Llamo a funcion para validar numero de cuenta/tarjeta            
                        $arrayParametrosValidaCtaTarj                          = array();
                        $arrayParametrosValidaCtaTarj['intTipoCuentaId']       = $arrayParametros['tipoCuentaId'];
                        $arrayParametrosValidaCtaTarj['intBancoTipoCuentaId']  = $arrayParametros['bancoTipoCuentaId'];
                        $arrayParametrosValidaCtaTarj['strNumeroCtaTarjeta']   = $arrayParametros['numeroCtaTarjeta'];
                        $arrayParametrosValidaCtaTarj['strCodigoVerificacion'] = $arrayParametros['codigoVerificacion'];
                        $arrayParametrosValidaCtaTarj['intFormaPagoId']        = $arrayParametros['formaPagoId'];
                        $arrayParametrosValidaCtaTarj['strCodEmpresa']         = $intIdEmpresa;
                        
                        $arrayValidaciones = $serviceInfoContrato->validarNumeroTarjetaCta($arrayParametrosValidaCtaTarj);
                        if($arrayValidaciones)
                        {
                            foreach($arrayValidaciones as $key => $mensaje_validaciones)
                            {
                                foreach($mensaje_validaciones as $key_msj => $value)
                                {
                                    $strError = $strError . $value . ".\n";
                                }
                            }
                            throw new \Exception("No se pudo generar el Cambio de Razon Social - " . $strError);
                        }

                        $objAdmiBancoTipoCuenta = $emComercial->getRepository('schemaBundle:AdmiBancoTipoCuenta')
                                                            ->find($arrayParametros['bancoTipoCuentaId']);
                        
                        $objAdmiTipoCuenta      = $emComercial->getRepository('schemaBundle:AdmiTipoCuenta')
                                                            ->find($arrayParametros['tipoCuentaId']);
                        
                        if (is_object($objAdmiBancoTipoCuenta))
                        {
                            if($objAdmiBancoTipoCuenta->getEsTarjeta() == 'S')
                            {
                                if(!$arrayParametros['mesVencimiento'] || !$arrayParametros['anioVencimiento'])
                                {
                                    throw new \Exception('No se pudo generar el Cambio de Razon Social - '
                                                    . 'El Anio y mes de Vencimiento de la tarjeta son campos obligatorios');
                                }
                                if(!$arrayParametros['codigoVerificacion'])
                                {
                                    throw new \Exception('No se pudo generar el Cambio de Razon Social - '
                                                    . 'El codigo de verificacion de la tarjeta es un campo obligatorio');
                                }
                            }
                        }
                        else
                        {
                            throw new \Exception('No se pudo generar el Cambio de Razon Social - Banco invalido.');
                        }
                        
                        $objInfoContratoFormaPago = new InfoContratoFormaPago();
                        $objInfoContratoFormaPago->setContratoId($objInfoContrato);

                        if(!$arrayParametros['numeroCtaTarjeta'])
                        {
                            throw new \Exception('No se pudo generar el Cambio de Razon Social - '
                                            . 'El Numero de Cuenta / Tarjeta es un campo obligatorio ');
                        }
                        
                        // Llamo a funcion que realiza encriptado del numero de cuenta
                        /* @var $serviceCrypt \telconet\seguridadBundle\Service\CryptService */
                        $serviceCrypt        = $this->get('seguridad.Crypt');
                        $strNumeroCtaTarjeta = $serviceCrypt->encriptar($arrayParametros['numeroCtaTarjeta']);
                        
                        if($strNumeroCtaTarjeta)
                        {
                            $objInfoContratoFormaPago->setNumeroCtaTarjeta($strNumeroCtaTarjeta);
                        }
                        else
                        {
                            throw new \Exception('No se pudo generar el Cambio de Razon Social - '
                                            . 'No fue posible guardar el numero de cuenta/tarjeta ' . $arrayParametros['numeroCtaTarjeta']);
                        }
                        
                        if($objAdmiBancoTipoCuenta->getEsTarjeta() == 'S')
                        {
                            $objInfoContratoFormaPago->setAnioVencimiento($arrayParametros['anioVencimiento']);
                            $objInfoContratoFormaPago->setMesVencimiento($arrayParametros['mesVencimiento']);
                            $objInfoContratoFormaPago->setCodigoVerificacion($arrayParametros['codigoVerificacion']);
                        }
                        
                        
                        $objInfoContratoFormaPago->setTitularCuenta($arrayParametros['titularCuenta']);
                        $objInfoContratoFormaPago->setBancoTipoCuentaId($objAdmiBancoTipoCuenta);
                        $objInfoContratoFormaPago->setTipoCuentaId($objAdmiTipoCuenta);
                        $objInfoContratoFormaPago->setFeCreacion(new \DateTime('now'));
                        $objInfoContratoFormaPago->setUsrCreacion($arrayParametros['strUsrCreacion']);
                        $objInfoContratoFormaPago->setIpCreacion($arrayParametros['strClientIp']);
                        $objInfoContratoFormaPago->setEstado($strEstadoActivo);
                        $emComercial->persist($objInfoContratoFormaPago);

                        //Guardo files asociados al contrato                      
                        $arrayDocumentos = array_merge( array('datos_form_files'    => $arrayParametros['arrayDatosFormFiles']),
                        array('arrayTipoDocumentos' => $arrayParametros['arrayTipoDocumentos']));

                    }
                } 
                //Proceso para registrar el representante legal.
                if (($strPrefijoEmpresa === 'MD' ||$strPrefijoEmpresa === 'EN' ) && $entity->getTipoTributario() === 'JUR')
                {
                    $arrayDatosRL = json_decode($strDatosRepresentanteLegal,true);
                    if (empty($arrayDatosRL) || count($arrayDatosRL) < 1)
                    {
                        throw new \Exception("Debe registrar los datos del representante legal.");
                    }
                }
            }
            else
            {
                $entity = $emComercial->getRepository('schemaBundle:InfoPersona')
                             ->findOneByIdentificacionCliente($datos_form['identificacionCliente']);
                $entityPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                              ->getPersonaEmpresaRolPorPersonaPorTipoRolActivosPendientes($entity->getId(), 'Cliente', $intIdEmpresa);

                //Si ya existe el cliente, se verifica que tengan el mismo ciclo existente.
                if ('S' == $strAplicaCiclosFac)
                {
                    $objPerEmpRolCaracDestino = $emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRolCarac")
                                                            ->findOneBy(array("personaEmpresaRolId" => $entityPersonaEmpresaRol->getId(),
                                                                              "estado"              => "Activo",
                                                                              "caracteristicaId"    => $objCaracteristicaCiclo->getId()));

                    //Si el ciclo es diferente, se inserta un historial para identificar el caso.
                    if ($objPerEmpRolCaracOrigen->getValor() != $objPerEmpRolCaracDestino->getValor())
                    {
                        $objAdmiCicloDestino = $emComercial->getRepository("schemaBundle:AdmiCiclo")
                                                    ->find($objPerEmpRolCaracDestino->getValor());
                        $objInfoPersonaEmpresaRolHisto = new InfoPersonaEmpresaRolHisto();
                        $objInfoPersonaEmpresaRolHisto->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
                        $objInfoPersonaEmpresaRolHisto->setEstado($entityPersonaEmpresaRol->getEstado());
                        $objInfoPersonaEmpresaRolHisto->setFeCreacion(new \DateTime('now'));
                        $objInfoPersonaEmpresaRolHisto->setIpCreacion($arrayParametros['strClientIp']);
                        $objInfoPersonaEmpresaRolHisto->setUsrCreacion('cicloFactCRS');
                        $objInfoPersonaEmpresaRolHisto->setObservacion("Se realiza el CRS a un cliente que se encuentra en un ciclo "
                                . "distinto al origen:" . "Origen: "
                                . $objAdmiCicloOrigen->getNombreCiclo() . " |Destino: " . $objAdmiCicloDestino->getNombreCiclo());
                        $emComercial->persist($objInfoPersonaEmpresaRolHisto);
                    }
                }
            }
            
            if($entityPersonaEmpresaRol === null)
            {
                throw new \Exception("Entidad PersonaEmpresaRol nueva no encontrada o definida, verificar con el departamento de sistemas.");
            }

            //Consulto los puntos actuales del cliente nuevo
            $arrayPuntosClienteNuevo = $emComercial->getRepository('schemaBundle:InfoPunto')
                                                   ->findBy(array("personaEmpresaRolId" => $entityPersonaEmpresaRol->getId(),
                                                                  "estado"              => 'Activo'));
            foreach ($arrayPuntosClienteNuevo as $objPunto)
            {
                //Consulto si tiene adendums con estado Pendiente, arma un array de adendums que se deben excluir en el proceso de creacion
                //y autorizacion de RS
                $arrayAdendumPendiente = $emComercial->getRepository('schemaBundle:InfoAdendum')
                                                     ->findBy(array("puntoId" => $objPunto->getId(),
                                                                    "estado"  => array('Pendiente', 'Trasladado')));
                foreach ($arrayAdendumPendiente as $objAdendumPendiente)
                {
                    array_push($arrayAdendumsExcluirRS, $objAdendumPendiente->getId());
                }
            }
            
            /*
             * Al cliente nuevo '$entityPersonaEmpresaRol' se asocia en la columna personaEmpresaRolId la variable '$personaEmpresaRol' la cual
             * corresponde al cliente antiguo de donde proviene el cambio de razon social que se está realizando.
             */
            if( is_object($entityPersonaEmpresaRol) && is_object($personaEmpresaRol) )
            {
                $intIdClienteAntiguo = $personaEmpresaRol->getId() ? $personaEmpresaRol->getId() : 0;
                
                if( !empty($intIdClienteAntiguo) && $intIdClienteAntiguo > 0 )
                {
                    $entityPersonaEmpresaRol->setPersonaEmpresaRolId($intIdClienteAntiguo);
                    $emComercial->persist($entityPersonaEmpresaRol);
                    $emComercial->flush();
                }
                else
                {
                    throw new \Exception("El cliente del cual proviene el cambio de razón social no es válido");
                }//( !empty($intIdClienteAntiguo) && $intIdClienteAntiguo > 0 )
            }
            else
            {
                throw new \Exception("No se encontró la información correspondiente al cliente nuevo y/o al cliente del cual proviene el cambio de ".
                                     "razón social");
            }//( is_object($entityPersonaEmpresaRol) && is_object($personaEmpresaRol) )
            

            $puntosPadre = array();
            $indp        = 0;
            $arrayEnvKon = array();
            foreach($puntos as $pto)
            {
                $login = '';
                if($pto['estado'] != 'Cancelado')
                {
                    //CLONA EL LOGIN
                    $puntoObj           = $emComercial->getRepository('schemaBundle:InfoPunto')->find($pto['id']);
                    $entityPuntoClonado = clone $puntoObj;
                    $entityPuntoClonado->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
                    $entityPuntoClonado->setFeCreacion(new \DateTime('now'));
                    $entityPuntoClonado->setUsrCreacion($strUsrCreacion);
                    $entityPuntoClonado->setObservacion('');

                    $arr_puntos = $emComercial->getRepository('schemaBundle:InfoPunto')
                                     ->findPtosPorEmpresaPorCanton( $intIdEmpresa, 
                                                                    $entityPuntoClonado->getLogin(),
                                                                    $entityPuntoClonado->getSectorId()
                                                                                       ->getParroquiaId()
                                                                                       ->getCantonId()
                                                                                       ->getId(), 
                                                                    9999999, 
                                                                    1, 
                                                                    0);
     
                    $login = $entityPuntoClonado->getLogin() . ($arr_puntos['total'] + 1);
                    $entityPuntoClonado->setLogin($login);
                    
                    /*Asigna estado Pendiente antes de autorizacion de contrato digital*/
                    if($boolAsignaEstadoPreactivo && $entityPuntoClonado->getEstado() == "Activo")
                    {
                        $entityPuntoClonado->setEstado("Pendiente");
                    }

                    $emComercial->persist($entityPuntoClonado);
                    $emComercial->flush();

                    $intIdPuntoSession  = $entityPuntoClonado->getId();
                    array_push($arrayPuntosCRS,$intIdPuntoSession);

                    //Clono Contactos Activos a nivel de Punto hacia los Logines de la nueva razon social
                    $arrayPuntoContactos = $emComercial->getRepository('schemaBundle:InfoPuntoContacto')
                                              ->findByPuntoIdYEstado($pto['id'],'Activo'); 
                    if($arrayPuntoContactos)
                    {
                        foreach($arrayPuntoContactos as $contactoPto)
                        {
                            $objContactoPtoClonado = clone $contactoPto;
                            $objContactoPtoClonado->setPuntoId($entityPuntoClonado);
                            $objContactoPtoClonado->setFeCreacion(new \DateTime('now'));
                            $objContactoPtoClonado->setIpCreacion($objRequest->getClientIp());
                            $objContactoPtoClonado->setUsrCreacion($strUsrCreacion);
                            $emComercial->persist($objContactoPtoClonado);
                        }
                    }

                    $objInfoPuntoDatoAdicionalAntiguo = $emComercial->getRepository('schemaBundle:InfoPuntoDatoAdicional')
                                                                    ->findOneByPuntoId($pto['id']);

                    if(is_object($objInfoPuntoDatoAdicionalAntiguo))
                    {
                        $arrayParamDatoAdic                        = array();
                        $arrayParamDatoAdic['objInfoPuntoClonado'] = $entityPuntoClonado;
                        $arrayParamDatoAdic['objInfoPersona']      = $entity;
                        $arrayParamDatoAdic['strUsrCreacion']      = $strUsrCreacion;
                        $arrayParamDatoAdic['intIdPunto']          = $pto['id'];
                        $arrayParamDatoAdic['strTipoCrs']          = 'Cambio_Razon_Social_Tradicional';
                        $arrayParamDatoAdic['arrayFormasContacto'] = $formas_contacto;

                        $serviceInfoPunto   = $this->get('comercial.InfoPunto');
                        $objInfoPuntoDatoAdicionalClonado = $serviceInfoPunto->generarInfoPuntoDatoAdicional($arrayParamDatoAdic);
                       
                        if(is_object($objInfoPuntoDatoAdicionalClonado) && $objInfoPuntoDatoAdicionalClonado->getEsPadreFacturacion() == 'S')
                        {
                            $puntosPadre[$indp]['viejo'] = $objInfoPuntoDatoAdicionalAntiguo->getPuntoId();
                            $puntosPadre[$indp]['nuevo'] = $objInfoPuntoDatoAdicionalClonado->getPuntoId();
                            $indp++;
                        }
                    }

                    $arrayServiciosActivos = $emComercial->getRepository('schemaBundle:InfoServicio')
                        ->findServiciosPorEmpresaPorPunto($intIdEmpresa, $pto['id'], 99999999, 1, 0);
                    $arrayServicios   = $arrayServiciosActivos['registros'];
                    $strLoginOrigen   = $pto['login'];
                    $strLoginOrigenKon= $pto['login'];
                    $intIdPuntoOrigen = $pto['id'];
                    $intCompCodeKon   = $pto['id'];
                    $intContKonibit   = 0;
                    foreach($arrayServicios as $serv)
                    {
                        if( !is_object($serv) )
                        {
                            throw new \Exception("No se encontró la información del servicio para realizar el cambio de razon social.");
                        }
                        else
                        {
                            if($serv->getEstado() != 'Cancel')
                            {   
                                $strEjecutaCreacionSolWyAp  = "NO";
                                $strEjecutaCreacionSolEdb   = "NO";
                                $strEjecutaCreacionSolPlan  = "NO";
                                $strEjecutaFlujoNormal      = "SI";
                                $boolProductoNetlifeCam     = false;
                                if(($strPrefijoEmpresa == 'MD') && is_object($serv->getProductoId()))
                                {    
                                    if($serv->getProductoId()->getNombreTecnico() === "WDB_Y_EDB")
                                    {
                                        $arrayEstadoPermitidoCRSWdbyEdb = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                                    ->getOne(   'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD',
                                                                                                '',
                                                                                                '',
                                                                                                '',
                                                                                                'CAMBIO_RAZON_SOCIAL',
                                                                                                'ESTADOS_SERVICIOS_X_PROD_FLUJO_PERSONALIZADO',
                                                                                                'WDB_Y_EDB',
                                                                                                $serv->getEstado(),
                                                                                                '',
                                                                                                $intIdEmpresa);
                                        if(isset($arrayEstadoPermitidoCRSWdbyEdb) && !empty($arrayEstadoPermitidoCRSWdbyEdb))
                                        {
                                            $strEjecutaFlujoNormal      = "NO";
                                            $strEjecutaCreacionSolWyAp  = "SI";
                                            $strEstadoServicioPorCRS    = $arrayEstadoPermitidoCRSWdbyEdb['valor5'];

                                        }
                                        else
                                        {
                                            $strEjecutaFlujoNormal  = "SI";
                                            $strEstadoServicioPorCRS = $serv->getEstado();
                                        }
                                    }
                                    else if($serv->getProductoId()->getNombreTecnico() === "EXTENDER_DUAL_BAND")
                                    {
                                        $arrayEstadoPermitidoCRSEdb = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                                ->getOne(   'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD',
                                                                                            '',
                                                                                            '',
                                                                                            '',
                                                                                            'CAMBIO_RAZON_SOCIAL',
                                                                                            'ESTADOS_SERVICIOS_X_PROD_FLUJO_PERSONALIZADO',
                                                                                            'EXTENDER_DUAL_BAND',
                                                                                            $serv->getEstado(),
                                                                                            '',
                                                                                            $intIdEmpresa);
                                        if(isset($arrayEstadoPermitidoCRSEdb) && !empty($arrayEstadoPermitidoCRSEdb))
                                        {
                                            $strEjecutaFlujoNormal      = "NO";
                                            $strEjecutaCreacionSolEdb   = "SI";
                                            $strEstadoServicioPorCRS    = $arrayEstadoPermitidoCRSEdb['valor5'];
                                        }
                                        else
                                        {
                                            $strEjecutaFlujoNormal  = "SI";
                                            $strEstadoServicioPorCRS = $serv->getEstado();
                                        }
                                    }
                                    else if($serv->getProductoId()->getNombreTecnico() === "ECDF" && 
                                            $serv->getEstado() == 'Activo')
                                    {
                                        $strEjecutaFlujoNormal      = "SI";
                                        $strEstadoServicioPorCRS    = $serv->getEstado();
                                        if($strTieneCorreoElectronico === "NO")
                                        {
                                            $strEstadoServicioPorCRS  = "Pendiente";
                                        }
                                        else
                                        {
                                            $objServProdCaractCorreo = $serviceTecnico->getServicioProductoCaracteristica($serv,
                                                                                                'CORREO ELECTRONICO',
                                                                                                $serv->getProductoId());

                                            if(is_object($objServProdCaractCorreo))
                                            {
                                                $strCorreoAnterior = $objServProdCaractCorreo->getValor();
                                                if((!isset($strCorreoAnterior) || empty($strCorreoAnterior)))
                                                {
                                                   throw new \Exception("El cliente anterior no cuenta un correo eléctrónico de suscripción");
                                                }
                                                // Ejecutar WS del canal del futbol para actualizar el correo antiguo
                                                $objInfoPersonaAsigna = $emComercial->getRepository("schemaBundle:InfoPersona")
                                                ->findOneByLogin($strUsrCreacion);

                                                if(!is_object($objInfoPersonaAsigna) 
                                                || !in_array($objInfoPersonaAsigna->getEstado(), array('Activo','Pendiente','Modificado')))
                                                {
                                                    throw new \Exception('El usuario de creación no existe en telcos o no se encuentra Activo.');
                                                }
                                                $strUsuarioAsigna  = $objInfoPersonaAsigna->getNombres()." ".$objInfoPersonaAsigna->getApellidos();

                                                $arrayParametrosECDF["email_old"]              = $strCorreoAnterior;
                                                $arrayParametrosECDF["email_new"]              = $datos_form["correoElectronico"];
                                                $arrayParametrosECDF["usrCreacion"]            = $strUsrCreacion;
                                                $arrayParametrosECDF["ipCreacion"]             = $objRequest->getClientIp();
                                                $arrayParametrosECDF['strLoginOrigen']         = $pto['login'];
                                                $arrayParametrosECDF['strLoginDestino']        = $login;
                                                $arrayParametrosECDF['intIdEmpresa']           = $intIdEmpresa;
                                                $arrayParametrosECDF['strPrefijoEmpresa']      = $strPrefijoEmpresa;
                                                $arrayParametrosECDF['strUsuarioAsigna']       = $strUsuarioAsigna;
                                                $arrayParametrosECDF['intIdPersonaEmpresaRol'] = $intIdPersonEmpRolEmpl;
                                                $arrayParametrosECDF['intPuntoId']             = $entityPuntoClonado->getId();
                                                $arrayParametrosECDF['boolCrearTarea']         = true;
                                                $arrayParametrosECDF['objServicio']            = $serv;
                                                $arrayParametrosECDF['identificacionCliente']  = $datos_form['identificacionCliente'];

                                                $arrayResultado  = $serviceFoxPremium->actualizarCorreoECDF($arrayParametrosECDF);
                                                if($arrayResultado['mensaje'] != 'ok')
                                                {
                                                      $strMensajeCorreoECDF     = "<br />
                                                        <span>
                                                            <strong style='color: red;'>Error en la activación de correo ECDF:</strong> "
                                                            . $arrayResultado['mensaje'] .
                                                        "</span>";
                                                      $strEstadoServicioPorCRS  = "Pendiente";
                                                }
                                            }
                                            else 
                                            {
                                                throw new \Exception("El cliente anterior no cuenta un correo eléctrónico de suscripción");
                                            }
                                        }
                                    }
                                    else if($serv->getProductoId()->getNombreTecnico() === "NETLIFECAM OUTDOOR")
                                    {   
                                        $boolProductoNetlifeCam = true;
                                        $arrayEstadoPermitidoCRSCAM = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                                    ->getOne(   'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD',
                                                                                                '',
                                                                                                '',
                                                                                                '',
                                                                                                'CAMBIO_RAZON_SOCIAL',
                                                                                                'ESTADOS_SERVICIOS_X_PROD_FLUJO_PERSONALIZADO',
                                                                                                'NETLIFECAM OUTDOOR',
                                                                                                $serv->getEstado(),
                                                                                                '',
                                                                                                $intIdEmpresa);
                                        
                                        
                                        
                                        
                                        if(isset($arrayEstadoPermitidoCRSCAM) && !empty($arrayEstadoPermitidoCRSCAM)) 
                                        {
                                            $strEjecutaFlujoNormal      = "NO";
                                            $strEjecutaCreacionSolPlan  = "SI";
                                            $strEstadoServicioPorCRS    = $arrayEstadoPermitidoCRSCAM['valor5'];
                                        }
                                        else
                                        {
                                            $strEjecutaFlujoNormal  = "SI";
                                            $strEstadoServicioPorCRS = $serv->getEstado();
                                        }
                                    }
                                    else
                                    {
                                        $strEjecutaFlujoNormal = "SI";
                                        $strEstadoServicioPorCRS = $serv->getEstado();
                                    }
                                }
                                else
                                {
                                    $strEjecutaFlujoNormal = "SI";
                                    $strEstadoServicioPorCRS = $serv->getEstado();
                                }
                                
                                $entityServicio = clone $serv;
                                $entityServicio->setFeCreacion(new \DateTime('now'));
                                $entityServicio->setUsrCreacion($strUsrCreacion);
                                $entityServicio->setPuntoId($entityPuntoClonado);
                                $entityServicio->setObservacion($serv->getId());
                                if( $strPrefijoEmpresa != 'TN')
                                {
                                    $entityServicio->setPorcentajeDescuento(0);
                                    $entityServicio->setValorDescuento(null);
                                    $entityServicio->setDescuentoUnitario(null);
                                }
                                $entityServicio->setEstado($strEstadoServicioPorCRS);
                                if($datos_form['yaexiste'] == 'S')
                                {
                                    $entityServicio->setPuntoFacturacionId($entityPuntoClonado);
                                }
                                $emComercial->persist($entityServicio);
                                $emComercial->flush();

                                //Agrega Còdigo para replicar Cortesías aprobadas
                                if($strPrefijoEmpresa == 'TN')
                                {
                                    $intIdServ = $serv->getId();
                                    $arrayActualCortesias = $emComercial
                                                    ->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                    ->findCortesiasAprobadas($intIdServ);
                                
                                    if(count( $arrayActualCortesias) > 0) 
                                    {
                                        foreach ($arrayActualCortesias as $cortesia) 
                                        {
                                            $objNewCortesia = new InfoDetalleSolicitud();
                                            $objNewCortesia = clone $cortesia;
                                            $objNewCortesia->setServicioId($entityServicio);
                                            $objNewCortesia->setFeCreacion(new \DateTime('now'));
                                            $emComercial->persist($objNewCortesia);
                                            
                                            //Anula la cortesìa del anterior servicio
                                            $cortesia->setEstado('Anulado');
                                            $emComercial->persist($cortesia);
                                            
                                            
                                            //agregar historial a la solicitud nueva
                                            $strUsrLogin = $entityServicio->getPuntoId()->getLogin();
                                            $objHistorialNuevaCortesia = new InfoDetalleSolHist();
                                            $objHistorialNuevaCortesia->setDetalleSolicitudId($objNewCortesia);
                                            $objHistorialNuevaCortesia->setEstado($objNewCortesia->getEstado());
                                            $objHistorialNuevaCortesia->setFeCreacion(new \DateTime('now'));
                                            $objHistorialNuevaCortesia->setIpCreacion($strClientIp);
                                            $strObservacionViejaCortesia = "Generado por Cambio de Razón social a nombre de  $strUsrLogin";
                                            $objHistorialNuevaCortesia->setObservacion($strObservacionViejaCortesia);
                                            $objHistorialNuevaCortesia->setUsrCreacion($strUsrCreacion);
                                            $emComercial->persist($objHistorialNuevaCortesia);
                                            

                                            //Agrega al historial la anterior cortesia cancelada
                                            $objHistoriaViejaCortesia = new InfoDetalleSolHist();
                                            $objHistoriaViejaCortesia->setDetalleSolicitudId($cortesia);
                                            $objHistoriaViejaCortesia->setEstado($cortesia->getEstado());
                                            $objHistoriaViejaCortesia->setFeCreacion(new \DateTime('now'));
                                            $objHistoriaViejaCortesia->setIpCreacion($strClientIp);
                                            $strObservacionViejaCortesia = "Se genera por Cambio de Razón social a nombre de  $strUsrLogin";
                                            $objHistoriaViejaCortesia->setObservacion($strObservacionViejaCortesia);
                                            $objHistoriaViejaCortesia->setUsrCreacion($strUsrCreacion);
                                            $emComercial->persist($objHistoriaViejaCortesia);


                                            
                                            $emComercial->flush();
                                        }
                                    }
                                    
                                }

                                
                                if($strEjecutaFlujoNormal === "SI")
                                {
                                    $arrayParametrosServ = array("strAplicaCiclosFac" => $strAplicaCiclosFac,
                                                                 "objServicioOrigen"  => $serv,
                                                                 "objServicioDestino" => $entityServicio,
                                                                 "objAdmiCicloOrigen" => $objAdmiCicloOrigen,
                                                                 "strUsrCreacion"     => $strUsrCreacion,
                                                                 "strIpCreacion"      => $arrayParametros['strClientIp']);
                                    $arrayRespuesta = $serviceInfoServicio->crearServicioCaracteristicaPorCRS($arrayParametrosServ);
                                    if ($arrayRespuesta["strEstado"] != "OK")
                                    {
                                        throw new \Exception("Error al procesar el Cambio de Razón Social: " . $arrayRespuesta["strMensaje"]);
                                    }
                                }

                                /**
                                 * Bloque que genera un historial en el servicio con la fecha con la cual se realiza el cálculo de meses restantes
                                 */
                                $intFrecuenciaProducto = $serv->getFrecuenciaProducto() ? $serv->getFrecuenciaProducto() : 0;
                                
                                if( $strPrefijoEmpresa == 'TN' && $intFrecuenciaProducto > 1 && is_object($entityServicio) )
                                {
                                    $intIdServicioAntiguo = $serv->getId() ? $serv->getId() : 0;
                                    $intMesesRestantes    = $serv->getMesesRestantes() ? $serv->getMesesRestantes() : 0;
                                    
                                    $arrayParametrosGenerarHistorialReinicioConteo = array('intIdServicioAntiguo' => $intIdServicioAntiguo,
                                                                                           'objServicioNuevo'     => $entityServicio,
                                                                                           'strPrefijoEmpresa'    => $strPrefijoEmpresa,
                                                                                           'strUsrCreacion'       => $strUsrCreacion,
                                                                                           'intMesesRestantes'    => $intMesesRestantes);
                                    
                                    $serviceInfoServicio->generarHistorialReinicioConteo($arrayParametrosGenerarHistorialReinicioConteo);
                                }//( $strPrefijoEmpresa == 'TN' && $intFrecuenciaProducto > 1 && is_object($entityServicio) )

                                if($strEjecutaFlujoNormal === "SI")
                                {
                                    $arrayParametrosFechaAct = array('emFinanciero'  => $emFinanciero,
                                                                     'intIdServicio' => $serv->getId()
                                                                     );
                                
                                    if($strPrefijoEmpresa === 'MD' || $strPrefijoEmpresa === 'EN')
                                    {
                                        // Registro de historial con feActivacion de servicio origen
                                        $objAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                                         ->findOneBy(array('nombreParametro' => 'CAMBIO FORMA PAGO', 
                                                                                           'estado'          => 'Activo'));
                                        if(is_object($objAdmiParametroCab))
                                        {              
                                            $objAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                             ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                                                               'descripcion' => 'FECHA ACTIVACION ORIGEN',
                                                                                               'valor1'      => 'CAMBIO RAZON SOCIAL',
                                                                                               'empresaCod'  => $intIdEmpresa,
                                                                                               'estado'      => 'Activo'));
                                            $strFechaActivacionOrigen = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                                                    ->getFechaActivacionServicioOrigen($arrayParametrosFechaAct); 

                                            if(is_object($objAdmiParametroDet))
                                            {
                                                $strAccionHistOrigen = $objAdmiParametroDet->getValor2();

                                                if(isset($strAccionHistOrigen) && !empty($strFechaActivacionOrigen))
                                                {
                                                    $objServicioHistorial = new InfoServicioHistorial();
                                                    $objServicioHistorial->setServicioId($entityServicio);
                                                    $objServicioHistorial->setFeCreacion(new \DateTime($strFechaActivacionOrigen));
                                                    $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                                                    $objServicioHistorial->setEstado($entityServicio->getEstado());

                                                    if ($boolProductoNetlifeCam)
                                                    {   
                                                        $objAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                                        ->findOneBy(array('nombreParametro' => 'PROYECTO NETLIFECAM', 
                                                                                        'estado'          => 'Activo'));
                                                        if(is_object($objAdmiParametroCab))
                                                        {
                                                            $objAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                            ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                                                            'descripcion' => 'PARAMETROS NETLIFECAM OUTDOOR',
                                                                                            'valor1'      => 'CAMBIO RAZON SOCIAL',
                                                                                            'empresaCod'  => $intIdEmpresa,
                                                                                            'estado'      => 'Activo'));
                                                            $strAccionHistOrigen = $objAdmiParametroDet->getValor2();
                                                            $strObserHistOrigen = $objAdmiParametroDet->getValor3();
                                                            $objServicioHistorial->setAccion($strAccionHistOrigen);
                                                            $objServicioHistorial->setObservacion($strObserHistOrigen);
                                                    
                                                        }  
                                                    
                                                    }
                                                    else
                                                    {   
                                                        $strObserHistOrigen = 'Fecha inicial de servicio por Cambio de razón social.';
                                                        $objServicioHistorial->setAccion($strAccionHistOrigen);
                                                        $objServicioHistorial->setObservacion($strObserHistOrigen);
                                                    }
                                                    $emComercial->persist($objServicioHistorial);
                                                    
                                                }                            
                                            }
                                        }                                    
                                    }                                
                                    // Obtengo la fecha de confirmacion del servicio del cliente origen del cambio de razon social
                                    $strFechaActivacion = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                                      ->getFechaActivacionServicio($arrayParametrosFechaAct);

                                    if(isset($strFechaActivacion) && !empty($strFechaActivacion))
                                    {
                                        // Guardo Historial con fecha y observacion 'Se Confirmo el Servicio' en los servicios origenes del Cambio de 
                                        // Razon Social
                                        $objServicioHistorial = new InfoServicioHistorial();
                                        $objServicioHistorial->setServicioId($entityServicio);
                                        $objServicioHistorial->setFeCreacion(new \DateTime($strFechaActivacion));
                                        $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                                        $objServicioHistorial->setEstado($entityServicio->getEstado());
                                        $objServicioHistorial->setAccion('confirmarServicio');
                                        $objServicioHistorial->setObservacion('Se Confirmó el Servicio por Cambio de razón social');
                                        $emComercial->persist($objServicioHistorial);
                                    }
                                }
                                $entityServicioHistorial = new InfoServicioHistorial();
                                $entityServicioHistorial->setServicioId($entityServicio);
                                $entityServicioHistorial->setFeCreacion(new \DateTime('now'));
                                $entityServicioHistorial->setUsrCreacion($strUsrCreacion);
                                $entityServicioHistorial->setEstado($entityServicio->getEstado());
                                $entityServicioHistorial->setObservacion('Cambio de razon social');
                                $emComercial->persist($entityServicioHistorial);

                                 if ($boolProductoNetlifeCam && $strEjecutaFlujoNormal === "NO")
                                {   
                                    $objAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                    ->findOneBy(array('nombreParametro' => 'PROYECTO NETLIFECAM', 
                                                                    'estado'          => 'Activo'));
                                    if(is_object($objAdmiParametroCab))
                                    {
                                        $objAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                                        'descripcion' => 'PARAMETROS NETLIFECAM OUTDOOR',
                                                                        'valor1'      => 'CAMBIO RAZON SOCIAL',
                                                                        'empresaCod'  => $intIdEmpresa,
                                                                        'estado'      => 'Activo'));
                                        $strAccionHistOrigen = $objAdmiParametroDet->getValor2();
                                        $strObserHistOrigen = $objAdmiParametroDet->getValor3();  
                                    }
                                    
                                    $arrayParametrosFechaAct = array('emFinanciero'  => $emFinanciero,
                                                                     'intIdServicio' => $serv->getId()
                                                                     );
                                    $strFechaActivacionOrigen = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                                ->getFechaActivacionServicioOrigen($arrayParametrosFechaAct); 

                                           
                                    $entityServicioHistorial = new InfoServicioHistorial();
                                    $entityServicioHistorial->setServicioId($entityServicio);
                                    $entityServicioHistorial->setFeCreacion(new \DateTime($strFechaActivacionOrigen));
                                    $entityServicioHistorial->setUsrCreacion($strUsrCreacion);
                                    $entityServicioHistorial->setEstado($entityServicio->getEstado());
                                    $entityServicioHistorial->setAccion($strAccionHistOrigen);
                                    $entityServicioHistorial->setObservacion($strObserHistOrigen); 
                                    $emComercial->persist($entityServicioHistorial); 
                                }    

                                // Funcion que verifica si existen servicios extremos para un servicio concentrador, actualiza a todos los enlaces
                                // extremos existentes (servicios con caracteristica ENLACE_DATOS) el nuevo servicio Concentrador generado en el 
                                // cambio de razon Social
                                $arrayParametroEnlaceDatos = array ('strFechaCreacion'       => new \DateTime('now'),
                                                                    'strUsrCreacion'         => $strUsrCreacion,
                                                                    'strIpCreacion'          => $objRequest->getClientIp(),                                                                                                              
                                                                    'objInfoServicioOrigen'  => $serv,
                                                                    'objInfoServicioDestino' => $entityServicio,
                                                                    'strTipoCaracteristica'  => 'ENLACE_DATOS'); 
                                                   
                                /* @var $serviceInfoContrato \telconet\comercialBundle\Service\InfoContratoService */
                                $serviceInfoContrato = $this->get('comercial.InfoContrato');
                    
                                $strMsjActualizaConcentradorEnExtremos = $serviceInfoContrato->actualizaConcentradorEnExtremos(
                                                                                               $arrayParametroEnlaceDatos);
                                if($strMsjActualizaConcentradorEnExtremos)
                                {
                                    throw new \Exception($strMsjActualizaConcentradorEnExtremos);
                                }
                                
                                // Funcion que verifica si existen servicios BACKUP para un servicio PRINCIPAL, actualiza a todos los enlaces 
                                // BACKUPS existentes (servicios con caracteristica ES_BACKUP) el nuevo ID servicio PRINCIPAL generado en el cambio 
                                // de razon Social
                                $arrayParametroEnlacesBackup = array ('strFechaCreacion'       =>  new \DateTime('now'),
                                                                      'strUsrCreacion'         => $strUsrCreacion,
                                                                      'strIpCreacion'          => $objRequest->getClientIp(),                                                                                                              
                                                                      'objInfoServicioOrigen'  => $serv,
                                                                      'objInfoServicioDestino' => $entityServicio,
                                                                      'strTipoCaracteristica'  => 'ES_BACKUP'); 
                    
                                $strMsjActualizaPrincipalEnBackups = $serviceInfoContrato->actualizaConcentradorEnExtremos(
                                                                                           $arrayParametroEnlacesBackup);
                                if($strMsjActualizaPrincipalEnBackups)
                                {
                                    throw new \Exception($strMsjActualizaPrincipalEnBackups);
                                }
                    
                                //Seteamos la característica a buscar
                                $strCaractProducto='NETLIFECLOUD';
                                //Seteamos los parametros para enviar a la función getInfoCaractProducto
                                $arrayParamProdCaract = array(
                                                                'intServicioId'        => $serv->getId(),
                                                                'strCaracteristica'    => $strCaractProducto
                                                             );
                                // Se obtienen las características del servicio asociado
                                $objCaractProducto = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                                 ->getInfoCaractProducto($arrayParamProdCaract);

                                $strDescripcionProducto = "";
                                $objProductoServicio    = null;
                                $arrayProducto          = array();
                                //NOMBRE TECNICO DE PRODUCTOS DE TVS PERMITIDOS
                                $arrayNombreTecnicoPermitido = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->get('NOMBRE_TECNICO_PRODUCTOSTV_CRS',//nombre parametro cab
                                                                    'COMERCIAL', //modulo cab
                                                                    'OBTENER_NOMBRE_PRODUCTO',//proceso cab
                                                                    'FLUJO_CRS', //descripcion det
                                                                    '','','','','',
                                                                    $intIdEmpresa); //empresa
                                foreach($arrayNombreTecnicoPermitido as $arrayNombreTecnico)
                                {
                                    $arrayProdTvNombreTecnico[]   =   $arrayNombreTecnico['valor1'];
                                }
                                
                                if(is_object($entityServicio->getProductoId()))
                                {
                                    $strDescripcionProducto = $entityServicio->getProductoId()->getDescripcionProducto();
                                    $objProductoServicio    = $entityServicio->getProductoId();
                                    $strNombreTecnicoProdTv = $entityServicio->getProductoId()->getNombreTecnico();
                                    if(in_array($strNombreTecnicoProdTv,$arrayProdTvNombreTecnico ))
                                    {
                                        $arrayProducto  = $serviceFoxPremium->determinarProducto(
                                            array('strNombreTecnico'=>$strNombreTecnicoProdTv));
                                    }
                                }
                                else if(is_object($entityServicio->getPlanId()))
                                {
                                    $objPlanDet = $emComercial->getRepository('schemaBundle:InfoPlanDet')
                                                              ->findBy(array('planId' => $entityServicio->getPlanId(),
                                                                             'estado' => "Activo"));
                                    if(($objPlanDet))
                                    {
                                        foreach($objPlanDet as $idxPlanDet)
                                        {
                                            $objProducto = $emComercial->getRepository('schemaBundle:AdmiProducto')
                                                                       ->find($idxPlanDet->getProductoId());

                                            if(is_object($objProducto) && in_array($objProducto->getNombreTecnico(), $arrayProdTvNombreTecnico))
                                            {
                                                $objProductoServicio    = $objProducto;
                                                $strDescripcionProducto = $objProducto->getDescripcionProducto();
                                                $arrayProducto          = $serviceFoxPremium->determinarProducto(
                                                                            array('strNombreTecnico'=>$objProducto->getNombreTecnico()));
                                                break;
                                            }
                                        }
                                    }
                                }

                                $strBanderaCredenciales    = "N";
                                $strBanderaNotifica        = "N";
                                //Se obtiene el nombre de las caracteristicas: usuario y password para los productos configurados
                                if(isset($arrayProducto) && !empty($arrayProducto))
                                {
                                    //se agrega Parametro para validar si se generan o no credenciales de productos
                                    $arrayNombreTecnicoGeneraCredenciales = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                                        ->get('NO_GENERA_CREDENCIALES_CRS',//nombre parametro cab
                                                                                            'COMERCIAL', //modulo cab
                                                                                            'NO_GENERA_CREDENCIALES',//proceso cab
                                                                                            'PRODUCTO_TV', //descripcion det
                                                                                            '','','','','',
                                                                                            $intIdEmpresa); //empresa
                                    foreach($arrayNombreTecnicoGeneraCredenciales as $arrayNTGeneraCredenciales)
                                    {
                                        $arrayProdTvNombreTecGeneraCred[]   =   $arrayNTGeneraCredenciales['valor1'];
                                    }
                                    
                                    if(in_array($arrayProducto["strNombreTecnico"],$arrayProdTvNombreTecGeneraCred))
                                    {
                                        $strBanderaCredenciales    = "N";
                                        $strBanderaNotifica        = "S";
                                        $strNombreTecnico          = $arrayProducto["strNombreTecnico"];
                                        $strPlantillaCorreo        = $arrayProducto["strCodPlantNuevo"];
                                        $strAsuntoNuevoServicio    = $arrayProducto['strAsuntoNuevo'];
                                        $strPlantillaSms           = $arrayProducto['strSmsNuevo'];
                                        
                                        // EN CASO DE QUE EXISTA EL PRODUCTO Y SI TENGA CORREO
                                        if($arrayProducto["strNombreTecnico"] === "ECDF")
                                        {
                                            $strCaracteristicaUsuario  = $arrayProducto["strUser"];
                                            $strCaracteristicaPassword = $arrayProducto["strPass"];

                                            if($strTieneCorreoElectronico === "SI"
                                              && $entityServicio->getEstado() === "Activo")
                                            {
                                                $strBanderaCredenciales    = "S";
                                            }
                                            else
                                            {
                                                $strBanderaNotifica        = "N";
                                            }
                                        }
                                    }
                                    else
                                    {
                                        $strCaracteristicaUsuario  = $arrayProducto["strUser"];
                                        $strCaracteristicaPassword = $arrayProducto["strPass"];
                                        $strNombreTecnico          = $arrayProducto["strNombreTecnico"];
                                        $strPlantillaCorreo        = $arrayProducto["strCodPlantNuevo"];
                                        $strAsuntoNuevoServicio    = $arrayProducto['strAsuntoNuevo'];
                                        $strPlantillaSms           = $arrayProducto['strSmsNuevo'];
                                        $strBanderaCredenciales    = "S";
                                        $strBanderaNotifica        = "S";
                                    }
                                }

                                // Se valida que la característica del servicio sea diferente a NETLIFECLOUD
                                if($objCaractProducto['caracteristica']!='NETLIFECLOUD')
                                {
                                    //Obtengo Caracteristicas del servicio en estado Activo
                                    $objInfoServicioProdCaract = $emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                            ->findBy(array("servicioId" => $serv->getId()));

                                    foreach($objInfoServicioProdCaract as $servpc)
                                    {
                                        $strClonarCaracteristica   = "S";
                                        $strSuscriberIdStatus = '';
                                        $intProductoCaracteristica = $servpc->getProductoCaracterisiticaId();
                                        $arrayEstadosCaract = array('Activo','Pendiente');
                                        if(in_array($servpc->getEstado(),$arrayEstadosCaract))
                                        {
                                            $strEsCaractECDF = "NO";
                                            if(!empty($intProductoCaracteristica))
                                            {
                                                $objAdmiProductoCaracteristica = 
                                                $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                            ->find($intProductoCaracteristica);

                                                if(is_object($objAdmiProductoCaracteristica))
                                                {
                                                    $objAdmiCaracteristica = $objAdmiProductoCaracteristica->getCaracteristicaId();

                                                    if(is_object($objAdmiCaracteristica) &&
                                                        ($objAdmiCaracteristica->getDescripcionCaracteristica() == $strCaracteristicaUsuario ||
                                                            $objAdmiCaracteristica->getDescripcionCaracteristica() == $strCaracteristicaPassword))
                                                    {
                                                        $strClonarCaracteristica = "N";
                                                    }
                                                    else if($objAdmiCaracteristica->getDescripcionCaracteristica() === "SUSCRIBER_ID")
                                                    {
                                                        $objProductoIPMP = $emComercial->getRepository('schemaBundle:AdmiProducto')
                                                                                        ->findOneBy(array("descripcionProducto"
                                                                                        => 'I. PROTEGIDO MULTI PAID',
                                                                                            "estado" => "Activo"));
                                                        
                                                        if(is_object($entityServicio) && $entityServicio->getPlanId() !== null)
                                                        {
                                                            $strEsenario = "ACTIVACION_PROD_EN_PLAN";
                                                            
                                                        }
                                                        else
                                                        {
                                                            $strEsenario = "ACTIVACION_PROD_ADICIONAL";
                                                            $arrayParamsLicencias["intIdOficina"] = $intIdOficina;
                                                        }
                                                        
                                                        $arrayParamsLicencias = array("strProceso"              => "ACTIVACION_ANTIVIRUS",
                                                                                    "strEscenario"              => $strEsenario,
                                                                                    "objServicio"               => $entityServicio,
                                                                                    "objPunto"                  => $entityServicio->getPuntoId(),
                                                                                    "strCodEmpresa"             => $intIdEmpresa,
                                                                                    "objProductoIPMP"           => $objProductoIPMP,
                                                                                    "strUsrCreacion"            => $strUsrCreacion,
                                                                                    "strIpCreacion"             => $arrayParametros['strClientIp'],
                                                                                    "strEstadoServicioInicial"  => $entityServicio->getEstado(),
                                                                                    "intIdOficina"              => $intIdOficina);

                                                        $arrayRespuestaGestionLicencias = $serviceServicioIPMP
                                                                                        ->gestionarLicencias($arrayParamsLicencias);
                                                        $strStatusGestionLicencias      = $arrayRespuestaGestionLicencias["status"];
                                                        $strMensajeGestionLicencias     = $arrayRespuestaGestionLicencias["mensaje"];
                                                        $arrayRespuestaWs               = $arrayRespuestaGestionLicencias["arrayRespuestaWs"];
                                                        
                                                        if($strStatusGestionLicencias === "ERROR")
                                                        {
                                                            $strMostrarError = "SI";
                                                            throw new \Exception($strMensajeGestionLicencias);
                                                        }
                                                        else if(isset($arrayRespuestaWs) && !empty($arrayRespuestaWs)
                                                        && $arrayRespuestaWs["status"] === "OK" && $entityServicio->getPlanId() !== null)
                                                        {
                                                            $strClonarCaracteristica = "N";
                                                            $strSuscriberId = $arrayRespuestaWs["SuscriberId"];
                                                     
                                                            //Guardar informacion de la característica del producto
                                                            $objServicioProdCaract = new InfoServicioProdCaract();
                                                            $objServicioProdCaract->setServicioId($entityServicio->getId());
                                                            $objServicioProdCaract->setProductoCaracterisiticaId($intProductoCaracteristica);
                                                            $objServicioProdCaract->setValor($strSuscriberId);
                                                            $objServicioProdCaract->setEstado('Pendiente');
                                                            $objServicioProdCaract->setUsrCreacion($strUsrCreacion);
                                                            $objServicioProdCaract->setFeCreacion(new \DateTime('now'));
                                                            $emComercial->persist($objServicioProdCaract);
                                                            $emComercial->flush();
                                                        }
                                                        else
                                                        {
                                                            $strClonarCaracteristica = "N";
                                                        }
                                                    } 
                                                    else if($objAdmiCaracteristica->getDescripcionCaracteristica() == "CORREO ELECTRONICO")
                                                    {
                                                        //ReGISTRA LAS FORMAS DE CONTACTO DEL CLIENTE 
                                                        $strCorreoSuscripcion = $servpc->getValor();
                                                        if($arrayProducto["strNombreTecnico"] === "ECDF")
                                                        {
                                                            $strEsCaractECDF = "SI";
                                                            $objCorreoAntReverso = $servpc; 
                                                            if(!isset($strCorreoSuscripcion) || empty($strCorreoSuscripcion))
                                                            {
                                                                throw new \Exception("El cliente anterior no cuenta un correo electrónico de 
                                                                                      suscripción");
                                                            }
                                                            if($strTieneCorreoElectronico === "SI")
                                                            {
                                                                if(!isset($datos_form["correoElectronico"]) 
                                                                    || empty($datos_form["correoElectronico"]))
                                                                {
                                                                    throw new \Exception("Debes ingresar un correo válido");
                                                                }

                                                                $objServicioProdCaract = new InfoServicioProdCaract();
                                                                $objServicioProdCaract = clone $servpc;
                                                                $objServicioProdCaract->setServicioId($entityServicio->getId());
                                                                $objServicioProdCaract->setFeCreacion(new \DateTime('now'));
                                                                $objServicioProdCaract->setUsrCreacion($strUsrCreacion);
                                                                $objServicioProdCaract->setValor($datos_form["correoElectronico"]);
                                                                $emComercial->persist($objServicioProdCaract);
                                                                $emComercial->flush();
                                                            }
                                                        }
                                                        $strNuevoCorreoElectronico  = $strCorreoSuscripcion;

                                                        if(!empty($strNuevoCorreoElectronico))
                                                        {
                                                          $servpc->setValor($strNuevoCorreoElectronico);
                                                          $strClonarCaracteristica = "S";
                                                        }
                                                    }
                                                    else if($objAdmiCaracteristica->getDescripcionCaracteristica() == "CODIGO_PRODUCTO")
                                                    {
                                                        $strClonarCaracteristica = "N";
                                                    }
                                                }
                                            }
                                            
                                            if($strClonarCaracteristica == "S")
                                            {
                                                $objInfoServicioProdCaractClonado = new InfoServicioProdCaract();
                                                $objInfoServicioProdCaractClonado = clone $servpc;
                                                $objInfoServicioProdCaractClonado->setServicioId($entityServicio->getId());
                                                $objInfoServicioProdCaractClonado->setFeCreacion(new \DateTime('now'));
                                                $objInfoServicioProdCaractClonado->setUsrCreacion($strUsrCreacion);

                                                if($arrayProducto["strNombreTecnico"] === "ECDF"
                                                && $strEsCaractECDF == "SI"
                                                && $strEstadoServicioPorCRS == "Activo")
                                                {
                                                    $objInfoServicioProdCaractClonado->setEstado("Eliminado");
                                                }
                                                else if($arrayProducto["strNombreTecnico"] === "ECDF"
                                                && $strEsCaractECDF == "SI"
                                                && $strEstadoServicioPorCRS !== "Activo")
                                                {
                                                    $objInfoServicioProdCaractClonado->setEstado("Cancel");
                                                }

                                                $emComercial->persist($objInfoServicioProdCaractClonado);
                                                $emComercial->flush();
                                            }

                                            $strClonarCaracteristica = "S";
                                        } 
                                    }
                                   
                                }
                                else
                                {
                                    /* @var $licenciasOffice365Service \telconet\tecnicoBundle\Service\LicenciasOffice365Service */
                                    $licenciasOffice365Service = $this->get('tecnico.LicenciasOffice365');
                                    $strAccion                 = 'cambioRazonSocial';
                                    //Seteamos los parametros para enviar a la función renovarLicenciaOffice365
                                    $arrayParametrosWs = array(
                                                              'strPrefijoEmpresa'    => $strPrefijoEmpresa,
                                                              'strEmpresaCod'        => $intIdEmpresa,
                                                              'strUsuarioCreacion'   => $strUsrCreacion,
                                                              'strIp'                => $strClientIp, 
                                                              'intServicioId'        => $entityServicio->getId(),
                                                              'strAccion'            => $strAccion
                                                            );
                                    // Invocamos a la función renovarLicenciaOffice365 para la conexión con el WebServices de Intcomex                                   
                                    $arrayRespuestaLicencia=$licenciasOffice365Service->renovarLicenciaOffice365($arrayParametrosWs);
                                    
                                    if($arrayRespuestaLicencia["status"] == 'ERROR')
                                    {
                                        throw new \Exception($arrayRespuestaLicencia["mensaje"]);
                                    }
                                }

                                //Generacion de credenciales de productos de tv
                                if($strBanderaCredenciales == "S" && is_object($entity))
                                {
                                    //Para servicios Paramount y Noggin se generan nuevo usuario y contrasenia
                                    $arrayParametrosGenerarUsuario["intIdPersona"]     = $entity->getId();
                                    $arrayParametrosGenerarUsuario["strCaracUsuario"]  = $strCaracteristicaUsuario;
                                    $arrayParametrosGenerarUsuario["strNombreTecnico"] = $strNombreTecnico;

                                    $strUsuario  = $serviceFoxPremium->generaUsuarioFox($arrayParametrosGenerarUsuario);

                                    if(empty($strUsuario))
                                    {
                                        throw new \Exception("No se pudo obtener Usuario para el servicio ".$strDescripcionProducto);
                                    }

                                    $strPassword           = $serviceFoxPremium->generaContraseniaFox();
                                    $strPasswordEncriptado = $serviceCrypt->encriptar($strPassword);
                                    if(empty($strPassword))
                                    {
                                        throw new \Exception("No se pudo generar Password para el servicio ".$strDescripcionProducto);
                                    }

                                    //Insertar nuevas caracteristicas: usuario y password
                                    $serviceTecnico->ingresarServicioProductoCaracteristica($entityServicio,
                                                                                            $objProductoServicio,
                                                                                            $strCaracteristicaUsuario,
                                                                                            $strUsuario,
                                                                                            $strUsrCreacion);

                                    $serviceTecnico->ingresarServicioProductoCaracteristica($entityServicio,
                                                                                            $objProductoServicio,
                                                                                            $strCaracteristicaPassword,
                                                                                            $strPasswordEncriptado,
                                                                                            $strUsrCreacion);
                                    //Cambiar estado ELiminado de la caracteristica del correo del producto
                                    $arrayNombreTecnicoEliminaCaracCorreo = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                              ->get('NOMBRE_PRODUCTOSTV_ELIMINA_CARAC_CORREO',//nombre parametro cab
                                                                                    'COMERCIAL', //modulo cab
                                                                                    'ELIMINA_CARAC_CORREO',//proceso cab
                                                                                    'CRS_ELIMINA_CARAC_CORREO', //descripcion det
                                                                                    '','','','','',
                                                                                    $intIdEmpresa); //empresa
                                    foreach($arrayNombreTecnicoEliminaCaracCorreo as $arrayNombreTecnicoProd)
                                    {
                                        $arrayProdTvPermitido[]   =   $arrayNombreTecnicoProd['valor1'];
                                    }
                                    if(in_array($strNombreTecnico,$arrayProdTvPermitido))
                                    {
                                        $arrayParameter =   array(
                                                                    "strNombreTecnico"  =>  $strNombreTecnico,
                                                                    "strUsrCreacion"    =>  $strUsrCreacion,
                                                                    "intIdServicio"     =>  $entityServicio->getId()
                                                                 );
                                        $serviceFoxPremium->eliminarCaractCorreo($arrayParameter);
                                    }
                                }
                                //Se valida si se notifica por correo y sms productos de tv
                                if ($strBanderaNotifica === "S")
                                {
                                    //Coger las credenciales de la info_servicio_pro_caract clonadas
                                    if(empty($strPassword) && empty($strUsuario))
                                    {
                                        $arrayParamServProdCarac = array('intIdServicio' =>  $entityServicio->getId());
                                        $arrayCaracteristicasTv  =   $serviceFoxPremium->obtieneArrayCaracteristicas($arrayParamServProdCarac);

                                        if(is_array($arrayCaracteristicasTv) && !empty($arrayCaracteristicasTv))
                                        {
                                            $objServProdCaracContrasenia = $arrayCaracteristicasTv[$arrayProducto['strPass']];
                                            $objServProdCaracUsuario     = $arrayCaracteristicasTv[$arrayProducto['strUser']];
                                            $strUsuario                  = $objServProdCaracUsuario->getValor();
                                            $strPassword                 = $serviceCrypt->descencriptar($objServProdCaracContrasenia->getValor());
                                        }
                                        else
                                        {
                                            throw new \Exception('No se encontraron características del Servicio '. $arrayProducto['strMensaje']);
                                        }
                                    }
                                    //Guarda Historial de Notificacion de correo y sms
                                    $arrayParamHistorial        = array('strUsrCreacion'  => $strUsrCreacion, 
                                                                        'strClientIp'     => $strClientIp, 
                                                                        'objInfoServicio' => $entityServicio,
                                                                        'strTipoAccion'   => $arrayProducto['strAccionActivo'],
                                                                        'strMensaje'      => $arrayProducto['strMensaje']);
                                    //Notifico al cliente por Correo y SMS
                                    $serviceFoxPremium->notificaCorreoServicioFox(
                                                            array("strDescripcionAsunto"   => $strAsuntoNuevoServicio,
                                                                  "strCodigoPlantilla"     => $strPlantillaCorreo,
                                                                  "strEmpresaCod"          => $intIdEmpresa,
                                                                  "intPuntoId"             => $entityPuntoClonado->getId(),
                                                                  "intIdServicio"          => $entityServicio->getId(),
                                                                  "strNombreTecnico"       => $strNombreTecnico,
                                                                  "intPersonaEmpresaRolId" => $entityPuntoClonado->getPersonaEmpresaRolId()
                                                                                                                 ->getId(),
                                                                  "arrayParametros"        => array("contrasenia" => $strPassword,
                                                                                                    "usuario"     => $strUsuario),
                                                                  "arrayParamHistorial"    => $arrayParamHistorial
                                                                 ));

                                    //Se reemplaza la contraseña del mensaje del parámetro
                                    $strMensajeSMS = str_replace("{{USUARIO}}",
                                                                 $strUsuario,
                                                                 str_replace("{{CONTRASENIA}}",
                                                                             $strPassword,
                                                                             $strPlantillaSms));

                                    $serviceFoxPremium->notificaSMSServicioFox(
                                            array("strMensaje"             => $strMensajeSMS,
                                                  "strTipoEvento"          => "enviar_infobip",
                                                  "strEmpresaCod"          => $intIdEmpresa,
                                                  "intPuntoId"             => $entityPuntoClonado->getId(),
                                                  "intPersonaEmpresaRolId" => $entityPuntoClonado->getPersonaEmpresaRolId()->getId(),
                                                  "strNombreTecnico"       => $strNombreTecnico,
                                                  "arrayParamHistorial"    => $arrayParamHistorial
                                                 )
                                           );
                                }

                                $servicioTecnico = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')->findByServicioId($serv->getId());
                                foreach($servicioTecnico as $servT)
                                {
                                    $entityServicioTecnico = clone $servT;
                                    $entityServicioTecnico->setServicioId($entityServicio);
                                    $emComercial->persist($entityServicioTecnico);
                                }
                                $ips = $emInfraestructura->getRepository('schemaBundle:InfoIp')->findByServicioId($serv->getId());
                                foreach($ips as $ip)
                                {
                                    $entityIp = clone $ip;
                                    $entityIp->setServicioId($entityServicio->getId());
                                    $emInfraestructura->persist($entityIp);
                                }

                                if((($strPrefijoEmpresa == 'MD')
                                    || ($strPrefijoEmpresa === 'TN' && is_object($serv->getProductoId()) 
                                        && ($serv->getProductoId()->getNombreTecnico() === "INTERNET SMALL BUSINESS"
                                            || $serv->getProductoId()->getNombreTecnico() === "TELCOHOME")))
                                    && ($serv->getEstado() === "EnVerificacion" || $serv->getEstado() === "Activo"))
                                {
                                    $arrayServiciosLdap[] = array(
                                                                    'servicioAnterior' => $serv,
                                                                    'servicioNuevo'    => $entityServicio
                                                                 );
                                }
                                
                                // Se guarda la Plantilla de Comisionistas a la nueva Razon social
                                $arrayServicioComision = $emComercial->getRepository('schemaBundle:InfoServicioComision')
                                                                     ->findBy(array("servicioId" => $serv->getId(), "estado" => "Activo"));

                                foreach($arrayServicioComision as $objServicioComision)
                                {
                                    $objInfoServicioComision = clone $objServicioComision;
                                    $objInfoServicioComision->setServicioId($entityServicio);
                                    $objInfoServicioComision->setFeCreacion(new \DateTime('now'));
                                    $objInfoServicioComision->setIpCreacion($objRequest->getClientIp());
                                    $objInfoServicioComision->setUsrCreacion($strUsrCreacion);
                                    $emComercial->persist($objInfoServicioComision);
                                }

                                //se clonan las solicitudes de agregar equipo y cambio de equipo por soporte que se encuentren en estado permitidos
                                if(($strPrefijoEmpresa == 'MD' || $strPrefijoEmpresa == 'EN' )&& 
                                   is_object($entityServicio->getPlanId()) &&
                                   ($entityServicio->getEstado() == 'Activo' ||
                                    $entityServicio->getEstado() == 'In-Corte'
                                   ))
                                {
                                    $arrayParametrosClonarSolCrs = array(
                                                                         'objServicioOrigen'  => $serv,
                                                                         'objServicioDestino' => $entityServicio,
                                                                         'strUsrCreacion'     => $strUsrCreacion,
                                                                         'strIpCreacion'      => $objRequest->getClientIp(),
                                                                         'strEmpresaCod'      => $intIdEmpresa
                                                                        );
                                    $serviceInfoServicio->clonarSolicitudesPorCrs($arrayParametrosClonarSolCrs);
                                }
                                
                                if($strEjecutaCreacionSolWyAp === "SI")
                                {
                                    $arrayParamsWyApTrasladoyCRS    = array("objServicioOrigen"     => $serv,
                                                                            "objServicioDestino"    => $entityServicio,
                                                                            "strCodEmpresa"         => $intIdEmpresa,
                                                                            "strUsrCreacion"        => $strUsrCreacion,
                                                                            "strIpCreacion"         => $objRequest->getClientIp(),
                                                                            "strOpcion"             => "cambio de razón social");
                                    $arrayRespuestaWyApTrasladoyCrs = $serviceInfoServicio->creaSolicitudWyApTrasladoyCRS(
                                                                                                $arrayParamsWyApTrasladoyCRS);
                                    if($arrayRespuestaWyApTrasladoyCrs["status"] === "ERROR")
                                    {
                                        throw new \Exception($arrayRespuestaWyApTrasladoyCrs["mensaje"]);
                                    }
                                }
                                else if($strEjecutaCreacionSolEdb === "SI")
                                {
                                    $arrayParamsEdbTrasladoyCRS     = array("objServicioOrigen"     => $serv,
                                                                            "objServicioDestino"    => $entityServicio,
                                                                            "strCodEmpresa"         => $intIdEmpresa,
                                                                            "strUsrCreacion"        => $strUsrCreacion,
                                                                            "strIpCreacion"         => $objRequest->getClientIp(),
                                                                            "strOpcion"             => "cambio de razón social");
                                    $arrayRespuestaEdbTrasladoyCrs  = $serviceInfoServicio->creaSolicitudEdbTrasladoyCRS($arrayParamsEdbTrasladoyCRS);
                                    if($arrayRespuestaEdbTrasladoyCrs["status"] === "ERROR")
                                    {
                                        throw new \Exception($arrayRespuestaEdbTrasladoyCrs["mensaje"]);
                                    }
                                }
                                if ($strEjecutaCreacionSolPlan === "SI")
                                {   
                                    $arrayParamsCamCRS = array("objServicioOrigen"      => $serv,
                                                                "objServicioDestino"    => $entityServicio,
                                                                "strCodEmpresa"         => $intIdEmpresa,
                                                                "strUsrCreacion"        => $strUsrCreacion,
                                                                "strIpCreacion"         => $objRequest->getClientIp(),
                                                                "strOpcion"             => "cambio de razón social");
                                    $arrayRespuestaCamCrs  = $serviceInfoServicio->creaSolicitudNetLifeCAM($arrayParamsCamCRS);
                                    if($arrayRespuestaCamCrs["status"] === "ERROR")
                                    {
                                        throw new \Exception($arrayRespuestaCamCrs["mensaje"]);
                                    }
                                }

                                /*Asigna estado preactivo a servicio antes de autorizacion de contrato digital*/
                                if($boolAsignaEstadoPreactivo && $entityServicio->getEstado() == "Activo")
                                {
                                    $entityServicio->setEstado($strEstadoServicioPreactivo);
                                    $emComercial->persist($entityServicio);
                                    
                                    //registro de estado PreActivo en historial
                                    $entityServicioHistorial = new InfoServicioHistorial();
                                    $entityServicioHistorial->setServicioId($entityServicio);
                                    $entityServicioHistorial->setFeCreacion(new \DateTime('now'));
                                    $entityServicioHistorial->setUsrCreacion($strUsrCreacion);
                                    $entityServicioHistorial->setEstado($entityServicio->getEstado());
                                    $entityServicioHistorial->setObservacion($strMensajeEstadoPreactivo);
                                    $emComercial->persist($entityServicioHistorial);

                                    $emComercial->flush();
                                }

                                //INI VALIDACIONES KONIBIT
                                $strTelefono           = "";
                                $strCorreo             = "";
                                $arrayListadoServicios = array();
                                $arrayTokenCas         = array();
                                $arrayPdts             = array();
                                $arrayKonibit          = array();
                                $intIdProdKon          = 0;
                                if($strPrefijoEmpresa == 'MD')
                                {
                                    if($datos_form['yaexiste'] != 'S')
                                    {
                                        for($i = 0; $i < count($formas_contacto); $i++)
                                        {
                                            if ($formas_contacto[$i]["formaContacto"] == "Correo Electronico")
                                            {
                                                $strCorreo = $formas_contacto[$i]["valor"];
                                                break;
                                            }
                                        }
                                        for($i = 0; $i < count($formas_contacto); $i++)
                                        {
                                            if ($formas_contacto[$i]["formaContacto"] == "Telefono Movil")
                                            {
                                                $strTelefono = $formas_contacto[$i]["valor"];
                                                break;
                                            }
                                            if ($formas_contacto[$i]["formaContacto"] == "Telefono Movil Claro")
                                            {
                                                $strTelefono = $formas_contacto[$i]["valor"];
                                                break;
                                            }
                                            if ($formas_contacto[$i]["formaContacto"] == "Telefono Movil CNT")
                                            {
                                                $strTelefono = $formas_contacto[$i]["valor"];
                                                break;
                                            }
                                            if ($formas_contacto[$i]["formaContacto"] == "Telefono Movil Digicel")
                                            {
                                                $strTelefono = $formas_contacto[$i]["valor"];
                                                break;
                                            }
                                            if ($formas_contacto[$i]["formaContacto"] == "Telefono Movil Movistar")
                                            {
                                                $strTelefono = $formas_contacto[$i]["valor"];
                                                break;
                                            }
                                            if ($formas_contacto[$i]["formaContacto"] == "Telefono Movil Referencia IPCC")
                                            {
                                                $strTelefono = $formas_contacto[$i]["valor"];
                                                break;
                                            }
                                            if ($formas_contacto[$i]["formaContacto"] == "Telefono Movil Tuenti")
                                            {
                                                $strTelefono = $formas_contacto[$i]["valor"];
                                                break;
                                            }
                                        }
                                    }
                                    else
                                    {
                                        $arrayParamEmial                           = array();                 
                                        $arrayParamEmial['strEstado']              = "Activo";
                                        $arrayParamEmial['strDescFormaContacto']   = array("Correo Electronico");
                                        $arrayParamEmial['intIdPersonaEmpresaRol'] = $entityPersonaEmpresaRol->getId();
                                        $arrayCorreoCli                            = $emComercial
                                                                                     ->getRepository('schemaBundle:InfoPersonaContacto')
                                                                                     ->getEmailCliente($arrayParamEmial);

                                        foreach ($arrayCorreoCli as $arrayCorreo) 
                                        {
                                            $strCorreo = $arrayCorreo['strFormaContacto'];
                                            break;
                                        }
                                        $arrayParamTelf                           = array();                 
                                        $arrayParamTelf['strEstado']              = "Activo";
                                        $arrayParamTelf['strDescFormaContacto']   = array("Telefono Movil",
                                                                                          "Telefono Movil Claro",
                                                                                          "Telefono Movil CNT",
                                                                                          "Telefono Movil Digicel",
                                                                                          "Telefono Movil Movistar",
                                                                                          "Telefono Movil Referencia IPCC",
                                                                                          "Telefono Movil Tuenti");
                                        $arrayParamTelf['intIdPersonaEmpresaRol'] = $entityPersonaEmpresaRol->getId();
                                        $arrayContactosTelf                       = $emComercial
                                                                                    ->getRepository('schemaBundle:InfoPersonaContacto')
                                                                                    ->getEmailCliente($arrayParamTelf);
                                        foreach ($arrayContactosTelf as $arrayContactoT) 
                                        {
                                            $strTelefono = $arrayContactoT['strFormaContacto'];
                                            break;
                                        }
                                    }
                                    $arrayParametroKnb     = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                       ->getOne('INVOCACION_KONIBIT_ACTUALIZACION', 
                                                                                'TECNICO', 
                                                                                'DEBITOS',
                                                                                'WS_KONIBIT', 
                                                                                '', 
                                                                                '', 
                                                                                '', 
                                                                                '', 
                                                                                '', 
                                                                                $intIdEmpresa);
                                    $arrayListadoServicios = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                       ->get('PRODUCTOS ADICIONALES AUTOMATICOS','COMERCIAL','',
                                                                             'Lista de productos adicionales automaticos',
                                                                             '','','','','',$intIdEmpresa);
                                    
                                    if (is_object($serv->getProductoId()))
                                    {
                                        $intIdProdKon   = $serv->getProductoId()->getId();
                                        foreach($arrayListadoServicios as $objListado)
                                        {
                                            // Si encuentra un producto konibit procede pasar la caracteristica
                                            if ($intIdProdKon == $objListado['valor1'] && $objListado['valor3'] == "SI")
                                            {   

                                                //DATA
                                                $intContKonibit = $intContKonibit + 1;
                                                if ($intContKonibit > 1)
                                                {
                                                    $strLoginOrigen   = $entityPuntoClonado->getLogin();
                                                    $intIdPuntoOrigen = $entityPuntoClonado->getId();
                                                }
                                                // $arrayTokenCas  = $serviceTokenCas->generarTokenCas();
                                                //PRODUCTOS
                                                $objProductos   = array('orderID'      => $serv->getId(),
                                                                        'productSKU'   => $entityServicio->getProductoId()
                                                                                                         ->getCodigoProducto(),
                                                                        'productName'  => $entityServicio->getProductoId()
                                                                                                         ->getDescripcionProducto(),
                                                                        'quantity'     => '1',
                                                                        'included'     => false,
                                                                        'productoId'   => $intIdProdKon,
                                                                        'migrateTo'    => $entityServicio->getId(),
                                                                        'status'       => 'active'
                                                                       );

                                                $arrayPdts[]    = $objProductos;
                                                // AGREGO LOS PRODUCTOS A MI ARREGLO
                                                array_push( $arrayListProdKon,$objProductos );
                                            }
                                        }
                                    }
                                    else
                                    {
                                        $objPlanDet = $emComercial->getRepository('schemaBundle:InfoPlanDet')
                                                                  ->findBy(array('planId' => $serv->getPlanId(),
                                                                                 'estado' => "Activo"));
                                        
                                        $strPlanInternet = $serv->getPlanId()->getNombrePlan();
                                        if(($objPlanDet))
                                        {
                                            foreach($objPlanDet as $idxPlanDet)
                                            {
                                                $objProducto = $emComercial->getRepository('schemaBundle:AdmiProducto')
                                                                           ->find($idxPlanDet->getProductoId());

                                                if(is_object($objProducto))
                                                {
                                                    $intIdProdKon = $idxPlanDet->getProductoId();
                                                    foreach($arrayListadoServicios as $objListado)
                                                    {
                                                        // Si encuentra un producto konibit procede pasar la caracteristica
                                                        if ($intIdProdKon == $objListado['valor1'] && $objListado['valor3'] == "SI")
                                                        {   

                                                            //DATA
                                                            $intContKonibit = $intContKonibit + 1;
                                                            if ($intContKonibit > 1)
                                                            {
                                                                $strLoginOrigen   = $entityPuntoClonado->getLogin();
                                                                $intIdPuntoOrigen = $entityPuntoClonado->getId();
                                                            }
                                                            //PRODUCTOS
                                                            $objProductos   = array('orderID'      => $serv->getId(),
                                                                                    'productSKU'   => $objProducto->getCodigoProducto(),
                                                                                    'productName'  => $objProducto->getDescripcionProducto(),
                                                                                    'quantity'     => '1',
                                                                                    'included'     => true,
                                                                                    'productoId'   => $intIdProdKon,
                                                                                    'migrateTo'    => $entityServicio->getId(),
                                                                                    'status'       => 'active'
                                                                                   );

                                                            $arrayPdts[]    = $objProductos;
                                                            // AGREGO MIS PRODUCTOS A MI ARREGLO
                                                            array_push( $arrayListProdKon,$objProductos );
                                                        }
                                                    }
                                                }
                                            }
                                        }

                                    }
                                }
                            }
                        }
 
                        // Armo mi Array de envio a Konibit
                        if (!empty($arrayListProdKon))
                        {
                            $arrayTokenCas  = $serviceTokenCas->generarTokenCas();

                            $objDataProd    = array(
                                'companyName'   => $entity->getRazonSocial() ?
                                    $entity->getRazonSocial() :
                                    $entity->getNombres() . " " .
                                    $entity->getApellidos(),
                                'companyCode'   => $entityPuntoClonado->getId(),
                                'companyID'     => $datos_form['identificacionCliente'],
                                'contactName'   => $entity->getRazonSocial() ?
                                    $entity->getRazonSocial() :
                                    $entity->getNombres() . " " .
                                    $entity->getApellidos(),
                                'email'         => $strCorreo,
                                'phone'         => $strTelefono,
                                'login'         => $entityPuntoClonado->getLogin(),
                                'plan'          => $strPlanInternet,
                                'address'       => $entityPuntoClonado->getDireccion(),
                                'city'          => $entityPuntoClonado->getPuntoCoberturaId()
                                    ->getNombreJurisdiccion(),
                                'sector'        => $entityPuntoClonado->getSectorId()
                                    ->getNombreSector(),
                                'status'        => 'active',
                                'products'      => $arrayPdts
                            );
                            //DATA
                            $arrayData      = array(
                                'action'        => (isset($arrayParametroKnb["valor5"]) &&
                                    !empty($arrayParametroKnb["valor5"]))
                                    ? $arrayParametroKnb["valor5"] : "",
                                'partnerID'     => (isset($arrayParametroKnb["valor7"]) &&
                                    !empty($arrayParametroKnb["valor7"]))
                                    ? $arrayParametroKnb["valor7"] : "001",
                                'companyCode'   => $intCompCodeKon,
                                'companyID'     => $objPersonaOrigen->getIdentificacionCliente(),
                                'contactName'   => $objPersonaOrigen->getRazonSocial() ?
                                    $objPersonaOrigen->getRazonSocial() :
                                    $objPersonaOrigen->getNombres() . ' ' .
                                    $objPersonaOrigen->getApellidos(),
                                'login'         => $strLoginOrigenKon,
                                'data'          => $objDataProd,
                                'requestNumber' => '1',
                                'timestamp'     => ''
                            );

                            $arrayKonibit   = array(
                                'identifier'    => $entityServicio->getId(),
                                'type'          => (isset($arrayParametroKnb["valor4"]) &&
                                    !empty($arrayParametroKnb["valor4"]))
                                    ? $arrayParametroKnb["valor4"] : "",
                                'retryRequered' => true,
                                'process'       => (isset($arrayParametroKnb["valor6"]) &&
                                    !empty($arrayParametroKnb["valor6"]))
                                    ? $arrayParametroKnb["valor6"] : "",
                                'origin'        => (isset($arrayParametroKnb["valor3"]) &&
                                    !empty($arrayParametroKnb["valor3"]))
                                    ? $arrayParametroKnb["valor3"] : "",
                                'user'          => $strUsrCreacion,
                                'uri'           => (isset($arrayParametroKnb["valor1"]) &&
                                    !empty($arrayParametroKnb["valor1"]))
                                    ? $arrayParametroKnb["valor1"] : "",
                                'executionIp'   => $strClientIp,
                                'data'          => $arrayData
                            );


                            $arrayEnvKon[] = array(
                                'strToken'         => $arrayTokenCas['strToken'],
                                'strUser'          => $strUsrCreacion,
                                'strIp'            => $strClientIp,
                                'arrayPropiedades' => $arrayKonibit
                            );
                        }
                        
                        
                        $arrayCaractProdNetlifeCloud    = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                                      ->getInfoCaractProducto(array(
                                                                                                        'intServicioId'        => $serv->getId(),
                                                                                                        'strCaracteristica'    => "NETLIFECLOUD"));
                        if($arrayCaractProdNetlifeCloud['caracteristica'] === 'NETLIFECLOUD')
                        {
                            $arrayServiciosNetlifeCloud[]   = array('intIdServicio' => $serv->getId());
                        }
                    }//foreach($servicios as $serv)
                }//($pto['estado'] != 'Cancelado')
            }//foreach($puntos as $pto)
            if($datos_form['yaexiste'] != 'S')
            {
                $puntosNuevos = $emComercial->getRepository('schemaBundle:InfoPunto')->findByPersonaEmpresaRolId($entityPersonaEmpresaRol->getId());
                foreach($puntosNuevos as $punto)
                {
                    $objServiciosHijos = $emComercial->getRepository('schemaBundle:InfoServicio')->findByPuntoId($punto->getId());
                    foreach($objServiciosHijos as $servicio)
                    {
                        for($indp = 0; $indp < count($puntosPadre); $indp++)
                        {
                            if($puntosPadre[$indp]['viejo'] == $servicio->getPuntoFacturacionId())
                            {
                                $idPuntoPadre = $puntosPadre[$indp]['nuevo'];
                            }
                        }
                        if($idPuntoPadre != '')
                        {
                            $servicio->setPuntoFacturacionId($idPuntoPadre);
                        }
                        $emComercial->persist($servicio);
                    }
                }
            }

            $arrayParametros['ID_PERSONA_ROL']  = $personaEmpresaRol->getId();
            $arrayParametros['REQUEST']         = $objRequest;
            $arrayParametros['COMERCIAL']       = $emComercial;
            $arrayParametros['INFRAESTRUCTURA'] = $emInfraestructura;
            $arrayParametros['GENERAL']         = $emGeneral;
            $arrayParametros['COD_EMPRESA']     = $intIdEmpresa;
            $arrayParametros['PERSONA_ROL']     = $entityPersonaEmpresaRol;
            $arrayParametros['USR_CREACION']    = $strUsrCreacion;
            $arrayParametros["strCodEmpresa"]   = $intIdEmpresa;
            $arrayParametros["strIpCreacion"]   = $arrayParametros['strClientIp'];
                        
            //CANCELAR EL CLIENTE ANTERIOR	
             $this->cancelarClientePorRazonSocial($arrayParametros);
            
            // Obtener Identificación y Nombre del Cliente Origen
            $strClienteOrigen  = $entity->getIdentificacionCliente() . " - ";
            $strClienteOrigen .= $entity->getRazonSocial() ? $entity->getRazonSocial() :
                                                             $entity->getApellidos() . " " .$entity->getNombres();
            // Se registra el historial del Cliente Destino
            $entity_persona_historial = new InfoPersonaEmpresaRolHisto();
            $entity_persona_historial->setEstado($entity->getEstado());
            $entity_persona_historial->setFeCreacion(new \DateTime('now'));
            $entity_persona_historial->setIpCreacion($objRequest->getClientIp());
            $entity_persona_historial->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
            $entity_persona_historial->setUsrCreacion($strUsrCreacion);
            //validacion Ruc verificado en portal del SRI por parte del usuario
            if($datos_form["rucClienteInvalido"] == "true")
            {
                $strMensajeObservacion = '- Ruc verificado en portal del SRI por parte del usuario';
            }else
            {
                $strMensajeObservacion = '';
            }
            $entity_persona_historial->setObservacion("Cliente creado  por Cambio de Razón Social desde el Cliente: $strClienteOrigen"
            .$strMensajeObservacion);
            $emComercial->persist($entity_persona_historial);

            $emComercial->flush();
            
            $intIdPersonaEmpresaRolDestino = $entityPersonaEmpresaRol->getId();

            if ($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->commit();
            }

            if ($emInfraestructura->getConnection()->isTransactionActive())
            {
                $emInfraestructura->getConnection()->commit();
            }

            $strStatusFuncionPrincipal = "OK";
            
            if (!empty($arrayListProdKon))
            {
                $arrayEnvKon[0]['arrayPropiedades']['data']['data']['products'] = $arrayListProdKon;
                $arrayEnvKonibit = array();
                array_push( $arrayEnvKonibit,$arrayEnvKon[0] );
                
                if (!empty($arrayEnvKon)) 
                {
                    foreach($arrayEnvKonibit as $envkon)
                    {
                        $serviceKonibit->envioAKonibit($envkon);
                    }
                }
            }
        }
        catch(\Exception $e)
        {
            $strStatusFuncionPrincipal = "ERROR";
            $serviceUtil->insertError('Telcos+', 
                                      'ClienteController.cambiarRazonSocial',
                                      $e->getMessage(), 
                                      $strUsrCreacion, 
                                      $objRequest->getClientIp()
                                     );
            if ($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->rollback();
                $emComercial->getConnection()->close();
            }

            if ($emInfraestructura->getConnection()->isTransactionActive())
            {
                $emInfraestructura->getConnection()->rollback();
                $emInfraestructura->getConnection()->close();
            }

            $strUrlCliente = $this->generateUrl('cliente_cambio_razon_social', array('id'    => $datos_form['antiguoIdCliente'], 
                                                                                           'idper' => $datos_form['idper']));
            $objResponse = new Response(
                json_encode(
                            array('strMensaje'      => $e->getMessage(),
                                  'strStatus'       => 99,
                                  'strStatusCliente'=> $strStatusCliente,
                                  'strUrl'          => $strUrlCliente)
                           ));
            $this->get('session')->getFlashBag()->add('notice', $e->getMessage());
            return $objResponse;
            
        }
        if($strStatusFuncionPrincipal === "OK")
        {            
            $strMuestraErrorAdicionalCRS    = "NO";
            $strMsjErrorPorProceso          = "";
            $strMsjUsrErrorAdicionalCRS     = "";
            $strMsjBDErrorAdicionalCRS      = "";
            try
            {
                 //Si el cliente es nuevo, se agregan los archivos digitales.
                if($datos_form['yaexiste'] != 'S' && is_object($objInfoContrato))
                {
                    //Guardo files asociados al contrato                      
                    $arrayDocumentos = array_merge( array('datos_form_files'    => $arrayParametros['arrayDatosFormFiles']),
                    array('arrayTipoDocumentos' => $arrayParametros['arrayTipoDocumentos']));
                    
                    $arrayFileParametros                         =  array();
                    $arrayFileParametros['id']                   =  $objInfoContrato->getId();
                    $arrayFileParametros['datos_form']           =  $arrayDocumentos;
                    $arrayFileParametros['intClientIp']          =  (int) $strClientIp;
                    $arrayFileParametros['strUsrCreacion']       =  $strUsrCreacion;
                    $arrayFileParametros['strCodEmpresa']        =  $intIdEmpresa;
                    


                    $objContrato = $serviceInfoContrato->guardarArchivoDigitalNfs($arrayFileParametros);

                    if(!(is_object($objContrato)) )
                    {
                        $strMuestraErrorAdicionalCRS    = "SI";
                        $strMsjErrorPorProceso          = "Hubo un error al guardar documento asociado al contrato. ";
                        $strMsjUsrErrorAdicionalCRS     .= $strMsjErrorPorProceso;
                        $strMsjBDErrorAdicionalCRS      .= $strMsjErrorPorProceso;
                    }
                }
            }
            catch(\Exception $e)
            {
                $strMuestraErrorAdicionalCRS    = "SI";
                $strMsjErrorPorProceso          = "Ocurrió un error inesperado al guardar documento asociado al contrato. ";
                $strMsjUsrErrorAdicionalCRS     .= $strMsjErrorPorProceso;
                $strMsjBDErrorAdicionalCRS      .= $strMsjErrorPorProceso.$e->getMessage();
            }

            // Validamos si el nuevo servicio posee un plan con promocion activa lo registramos en su historial
            if(($strPrefijoEmpresa == 'MD' || $strPrefijoEmpresa == 'EN'    )&& is_object($entityServicio->getPlanId()))
            {
                //EJECUTAR PROMOCIONES DE SERVICIOS POR CAMBIO DE RAZON SOCIAL
                $arrayParametrosInfoBw = array();
                $arrayParametrosInfoBw['intIdServicio']     = $entityServicio->getId();
                $arrayParametrosInfoBw['intIdEmpresa']      = $intIdEmpresa;
                $arrayParametrosInfoBw['strTipoProceso']    = "CAMBIO_RAZON_SOCIAL";
                $arrayParametrosInfoBw['strValor']          = $serv->getId();
                $arrayParametrosInfoBw['strUsrCreacion']    = $strUsrCreacion;
                $arrayParametrosInfoBw['strIpCreacion']     = $objRequest->getClientIp();
                $arrayParametrosInfoBw['strPrefijoEmpresa'] = $strPrefijoEmpresa;
                $servicePromociones->configurarPromocionesBW($arrayParametrosInfoBw);
            }

            //Eliminación de Ldap de antiguo servicio y creación de Ldap de nuevo servicio para cambios de razón social de servicios de INTERNET MD
            if(isset($arrayServiciosLdap) && !empty($arrayServiciosLdap))
            {
                foreach($arrayServiciosLdap as $arrayServicioLdap)
                {
                    $arrayRespuestaLdap =   $serviceTecnico->configurarLdapCambioRazonSocial(
                                                                array(  "servicioAnterior"  => $arrayServicioLdap['servicioAnterior'],
                                                                        "servicioNuevo"     => $arrayServicioLdap['servicioNuevo'],
                                                                        "usrCreacion"       => $strUsrCreacion,
                                                                        "ipCreacion"        => $objRequest->getClientIp(),
                                                                        "prefijoEmpresa"    => $strPrefijoEmpresa ));
                    if($arrayRespuestaLdap["status"] === "ERROR" || !empty($arrayRespuestaLdap["mensaje"]))
                    {
                        $strMuestraErrorAdicionalCRS    = "SI";
                        $strMsjErrorPorProceso          = $arrayRespuestaLdap["mensaje"] . ". ";
                        $strMsjUsrErrorAdicionalCRS     .= $strMsjErrorPorProceso;
                        $strMsjBDErrorAdicionalCRS      .= $strMsjErrorPorProceso;
                    }
                }
            }
                
            // FACTURACIÓN DE LOS SERVICIOS CANCELADOS NETLIFECLOUD
            if(isset($arrayServiciosNetlifeCloud) && !empty($arrayServiciosNetlifeCloud))
            {
                foreach($arrayServiciosNetlifeCloud as $arrayServicioNetlifeCloud)
                {
                    //Se invoca a la función generarFacturaServicioCancelado para generar la factura a los servicios NetlifeCloud cancelados
                    $arrayRespuestaFacturaNetlifeCloud  = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                                      ->generarFacturaServicioCancelado(
                                                                          array(
                                                                                'strPrefijoEmpresa' => $strPrefijoEmpresa,
                                                                                'strEmpresaCod'     => $intIdEmpresa,
                                                                                'strIp'             => $objRequest->getClientIp(), 
                                                                                'intServicioId'     => $arrayServicioNetlifeCloud["intIdServicio"]
                                                                          ));
                    if($arrayRespuestaFacturaNetlifeCloud["status"] == 'ERROR')
                    {
                        $strMuestraErrorAdicionalCRS    = "SI";
                        $strMsjErrorPorProceso          = $arrayRespuestaFacturaNetlifeCloud["mensaje"] . ". ";
                        $strMsjUsrErrorAdicionalCRS     .= $strMsjErrorPorProceso;
                        $strMsjBDErrorAdicionalCRS      .= $strMsjErrorPorProceso;
                    }
                }
            }
            
            $strMensajeAdvertencia = "Proceso realizado".$strMensajeCorreoECDF;
            
            if($strMuestraErrorAdicionalCRS === "SI")
            {
                $strMensajeAdvertencia = 'Se ha realizado de manera correcta el proceso principal de cambio de '.
                                                                    'razón social. Sin embargo, se tuvieron los siguientes inconvenientes: '.
                                                                    $strMsjUsrErrorAdicionalCRS.
                                                                    'Por favor verificar con el departamento de Sistemas!';
                $this->get('session')->getFlashBag()->add('warning','Se ha realizado de manera correcta el proceso principal de cambio de '.
                                                                    'razón social. Sin embargo, se tuvieron los siguientes inconvenientes: '.
                                                                    $strMsjUsrErrorAdicionalCRS.
                                                                    'Por favor verificar con el departamento de Sistemas!');
                $serviceUtil->insertError(  'Telcos+', 
                                            'ClienteController.cambiarRazonSocial',
                                            $strMsjBDErrorAdicionalCRS, 
                                            $strUsrCreacion, 
                                            $objRequest->getClientIp()
                                           );
            }

            $strUrlCliente    = $this->generateUrl('cliente_show', array('id' => $entity->getId(), 'idper' => $entityPersonaEmpresaRol->getId()));
            $strStatusCliente = 1;
            
              //Proceso para registrar el representante legal.
              if (($strPrefijoEmpresa === 'MD'  || $strPrefijoEmpresa === 'EN'  )&& $entity->getTipoTributario() === 'JUR')
              {
                  $arrayDatosRL = json_decode($strDatosRepresentanteLegal,true);
                  if (count($arrayDatosRL) !=0)
                  {
                        
                        $strIdPais              = $objRequest->getSession()->get('intIdPais');                    

                        $strTipoIdentificacionCliente = $entity->getTipoIdentificacion();
                        $strIdentificacionCliente     = $entity->getIdentificacionCliente();

                        $serviceTokenCas = $this->get('seguridad.TokenCas');
                        $arrayTokenCas = $serviceTokenCas->generarTokenCas();   
            
            
                        $arrayParamsRepresent = array(
                        'token'                                => $arrayTokenCas['strToken'],
                        'esCambioRazonSocial'                  => true,
                        'codEmpresa'                           => $intIdEmpresa,
                        'prefijoEmpresa'                       => $strPrefijoEmpresa,        
                        'oficinaId'                            => $intIdOficina,
                        'origenWeb'                            => 'S',
                        'clientIp'                             => $strClientIp,        
                        'usrCreacion'                          => $strUsrCreacion, 
                        'idPais'                               => $strIdPais ,          
                        'tipoIdentificacion'                   => $strTipoIdentificacionCliente,
                        'identificacion'                       => $strIdentificacionCliente,
                        'representanteLegal'                   => $arrayDatosRL
                    );        
                                                            
                    $serviceRepresentanteLegalMs = $this->get('comercial.RepresentanteLegalMs');                         
                    $objResponseRepresent     =  $serviceRepresentanteLegalMs->wsActualizarRepresentanteLegal($arrayParamsRepresent);
                    if ($objResponseRepresent['strStatus']!='OK' ) 
                    {
                                                       
                        //Si se produce fallas en el proceso  se realiza un reveso de CRS
                        $strMotivoReverso = $objResponseRepresent['strMensaje']; 
                        $arrayParametros     = array(                                                                
                            'strCodEmpresa'                => $intIdEmpresa,
                            'strPrefijoEmpresa'            => $strPrefijoEmpresa,
                            'strUsrCreacion'               => $strUsrCreacion,
                            'strClientIp'                  => $strClientIp,
                            'strYaExiste'                  => $datos_form['yaexiste'],
                            'intIdPersonaEmpresaRolDestino'=> $intIdPersonaEmpresaRolDestino,
                            'strMotivoReverso'             => $strMotivoReverso
                            ); 
                        $serviceInfoContrato   = $this->get('comercial.InfoContrato');
                        $arrayReversoCRS       = $serviceInfoContrato->ejecutarReversoCambioRazonSocial($arrayParametros);
                        $strMensajeAdvertencia =  'Fallo en representante legal: '.$strMotivoReverso;               
                        $arrayParametro   = array('id' => $datos_form['antiguoIdCliente'],'idper' => $datos_form['idper']); 
                        $strUrlCliente    = $this->generateUrl('cliente_cambio_razon_social',  $arrayParametro);
                        $strStatusCliente = 0;
                        $arrayRespuesta =  array(   'strMensaje'       => $strMensajeAdvertencia ,
                                                    'strStatus'        => 99,
                                                    'strStatusCliente' => $strStatusCliente,
                                                    'strUrl'           => $strUrlCliente);                    
                                            
                        $objResponse = new Response( json_encode( $arrayRespuesta  ));
                        $this->get('session')->getFlashBag()->add('notice',   $arrayReversoCRS['strMensaje']);
                        return $objResponse;   

                    }  
                  }  
              }
            if(($strPrefijoEmpresa == 'MD'  || $strPrefijoEmpresa == 'EN' )&& $intContratoFisico != 1)
            {
                $objContrato          = $emComercial->getRepository('schemaBundle:InfoContrato')
                                                    ->findOneBy(array("personaEmpresaRolId" => $entityPersonaEmpresaRol->getId(),
                                                                      "estado" => array('PorAutorizar','Activo')));

                if(is_object($objContrato))
                {
                    $intFormaPagoId       = $objContrato->getFormaPagoId()->getId();
                    $objContratoFormaPago = $emComercial->getRepository('schemaBundle:InfoContratoFormaPago')
                                                        ->findPorContratoIdYEstado($objContrato->getId(), 'Activo');

                    if(is_object($objContratoFormaPago))
                    {
                        $intTipoCuentaId         = $objContratoFormaPago->getTipoCuentaId()->getId();
                        $intBancoTipoCuentaId    = $objContratoFormaPago->getBancoTipoCuentaId()->getId();
                    }
                }

                //Obtengo Característica de USUARIO
                $objCaracteristicaUsuario = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneBy(array("descripcionCaracteristica" => "USUARIO",
                                                                          "estado"                    => "Activo"));

                if(is_object($objCaracteristicaUsuario))
                {
                    //Inserto Caracteristica de USUARIO en el nuevo cliente
                    $objPersEmpRolCaracUsuario = new InfoPersonaEmpresaRolCarac();
                    $objPersEmpRolCaracUsuario->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
                    $objPersEmpRolCaracUsuario->setCaracteristicaId($objCaracteristicaUsuario);
                    $objPersEmpRolCaracUsuario->setValor($entityPersonaEmpresaRol->getPersonaId()->getIdentificacionCliente());
                    $objPersEmpRolCaracUsuario->setFeCreacion(new \DateTime('now'));
                    $objPersEmpRolCaracUsuario->setUsrCreacion($strUsrCreacion);
                    $objPersEmpRolCaracUsuario->setEstado('Activo');
                    $objPersEmpRolCaracUsuario->setIpCreacion($objRequest->getClientIp());
                    $emComercial->persist($objPersEmpRolCaracUsuario);
                    $emComercial->flush();
                }

                $strProcesoContrato      = $objRequest->get('procesoContrato');
                $strEnviarPin            = $objRequest->get('enviarPin');
                $arrayParametrosContrato = array(
                    'strClientIp'               => $strClientIp,
                    'strUsrCreacion'            => $strUsrCreacion,
                    'intCodEmpresa'             => $intIdEmpresa,
                    'strPrefijoEmpresa'         => $strPrefijoEmpresa,
                    'intIdOficina'              => $intIdOficina,                      
                    'arrayFormFiles'            => $arrayDatosFormFiles,               
                    'arrayFormTipos'            => $arrayDatosFormTipos,
                    'strNombrePantalla'         => $strProcesoContrato,
                    'intKey'                    => key($arrayDatosFormTipos),
                    'intIdPuntoSession'         => $intIdPuntoSession,
                    'strFeFinContratoPost'      => $objRequest->get('feFinContratoPost'),
                    'strConvenioPago'           => isset($objRequest->get('infocontratodatoadicionaltype')['convenioPago']) ? 'S':'N',
                    'strVip'                    => isset($objRequest->get('infocontratodatoadicionaltype')['esVip']) ? 'S':'N',
                    'strTramiteLegal'           => isset($objRequest->get('infocontratodatoadicionaltype')['esTramiteLegal']) ? 'S':'N',
                    'strPermiteCorteAutomatico' => isset($objRequest->get('infocontratodatoadicionaltype')['permiteCorteAutomatico']) ? 'S':'N',
                    'strFideicomiso'            => isset($objRequest->get('infocontratodatoadicionaltype')['fideicomiso']) ? 'S':'N',
                    'strTiempoEsperaMesesCorte' => $objRequest->get('infocontratodatoadicionaltype')['tiempoEsperaMesesCorte'],
                    'intFormaPago'              => isset($objRequest->get('infocontratotype')['formaPagoId']) ? 
                                                    $objRequest->get('infocontratotype')['formaPagoId'] :
                                                    $intFormaPagoId,
                    'strValorAnticipo'          => $objRequest->get('infocontratotype')['valorAnticipo'],
                    'strNumContratoEmpPub'      => $objRequest->get('infocontratotype')['numeroContratoEmpPub'],
                    'intTipoCuentaId'           => isset($objRequest->get('infocontratoformapagotype')['tipoCuentaId']) ? 
                                                    $objRequest->get('infocontratoformapagotype')['tipoCuentaId'] :
                                                    $intTipoCuentaId,
                    'intBancoTipoCuentaId'      => isset($objRequest->get('infocontratoformapagotype')['bancoTipoCuentaId']) ?
                                                    $objRequest->get('infocontratoformapagotype')['bancoTipoCuentaId'] :
                                                    $intBancoTipoCuentaId,
                    'strNumeroCtaTarjeta'       => $objRequest->get('infocontratoformapagotype')['numeroCtaTarjeta'],
                    'strTitularCuenta'          => $objRequest->get('infocontratoformapagotype')['titularCuenta'],
                    'strAnioVencimiento'        => $objRequest->get('infocontratoformapagotype')['anioVencimiento'] == 'Seleccione...' ? '' :
                                                $objRequest->get('infocontratoformapagotype')['anioVencimiento'],
                    'strMesVencimiento'         => $objRequest->get('infocontratoformapagotype')['mesVencimiento'],
                    'strCodigoVerificacion'     => $objRequest->get('infocontratoformapagotype')['codigoVerificacion'],
                    'intTipoContratoId'         => $objRequest->get('infocontratoextratype')['tipoContratoId'],
                    'intPersonaEmpresaRolId'    => $entityPersonaEmpresaRol->getId(),
                    'strCambioPago'             => $objRequest->get('CambioPago') != null ? 'S' : 'N',
                    'strTipoDocumento'          => $strProcesoContrato == 'Contrato' ? 'C' : 'AS',
                    'strTelefono'               => $objRequest->get('telefonoCliente'),
                    'strEnviarPin'              => isset($strEnviarPin) ? 'N' : 'S', 
                    'cambioRazonSocial'         => 'S',
                    'cambioRazonSocialPunto'    => 'N',
                    'arrayRSPuntos'             => array(),
                    'arrayAdendumsExcluirRS'    => $arrayAdendumsExcluirRS
                );
                //Se manda a generar contrato
                $arrayRespuesta  = $this->crearContratoDigital($arrayParametrosContrato);
                $strMensajeError =  '. Si desea cambiar el proceso a contrato Físico presione ok, '.
                                    'caso contrario cancelar para seguir el proceso Digital.';

                if($strMensajeAdvertencia == "Proceso realizado")
                {
                    $strMensajeAdvertencia = $arrayRespuesta['strMensaje'];
                }

                if($arrayRespuesta['strStatus'] !== 0)
                {
                    $strMensajeAdvertencia = $strMensajeAdvertencia.$strMensajeError;
                }

                //Si se produce fallas en el proceso  se realiza un reveso de CRS
                if($arrayRespuesta['strStatus'] !== 0 && ($strPrefijoEmpresa=='MD' || $strPrefijoEmpresa=='EN'  ))
                {    
 
                    $strMotivoReverso = $arrayRespuesta['strMensaje']; 
                    $arrayParametros     = array(                                                                
                        'strCodEmpresa'                => $intIdEmpresa,
                        'strPrefijoEmpresa'            => $strPrefijoEmpresa,
                        'strUsrCreacion'               => $strUsrCreacion,
                        'strClientIp'                  => $strClientIp,
                        'strYaExiste'                  => $datos_form['yaexiste'],
                        'intIdPersonaEmpresaRolDestino'=> $intIdPersonaEmpresaRolDestino,
                        'strMotivoReverso'             => $strMotivoReverso
                        );
                    /* @var $serviceInfoContrato \telconet\comercialBundle\Service\InfoContratoService */
                    $serviceInfoContrato   = $this->get('comercial.InfoContrato');
                    $arrayReversoCRS       = $serviceInfoContrato->ejecutarReversoCambioRazonSocial($arrayParametros);
                    $strMensajeAdvertencia = $arrayReversoCRS['strMensaje'];   
                    $strStatusCliente = 0;
                    
                    $arrayParametro   = array('id' => $datos_form['antiguoIdCliente'],
                                            'idper' => $datos_form['idper']); 

                    $strUrlCliente    = $this->generateUrl('cliente_cambio_razon_social',  $arrayParametro);

                    $arrayRespuesta =  array('strMensaje'      => $strMensajeAdvertencia ,
                                            'strStatus'        => 99,
                                            'strStatusCliente' => $strStatusCliente,
                                            'strUrl'           => $strUrlCliente);
                    // reverso correo ECDF anterior
                    if(is_object($objCorreoAntReverso))
                    {
                        $objInfoServProdCaract = $emComercial->getRepository("schemaBundle:InfoServicioProdCaract")
                        ->find($objCorreoAntReverso->getId());
                        if(is_object($objInfoServProdCaract))
                        {
                            $objServicioProdCaract = new InfoServicioProdCaract();
                            $objServicioProdCaract = clone $objInfoServProdCaract;
                            $objServicioProdCaract->setEstado("Activo");
                            $emComercial->persist($objServicioProdCaract);
                            $emComercial->flush();
                        }
                    }
                                        
                    $objResponse = new Response( json_encode( $arrayRespuesta  ));
                    $this->get('session')->getFlashBag()->add('notice',   $strMensajeAdvertencia );
                    return $objResponse;

                }

                $arrayRespuestaProceso =  array('strMensaje'        => $strMensajeAdvertencia,
                                                'strStatus'         => $arrayRespuesta['strStatus'],
                                                'strUrl'            => $strUrlCliente,
                                                'strStatusCliente'  => $strStatusCliente,
                                                'intCodEmpresa'     => $intIdEmpresa,
                                                'intPunto'          => $entityPuntoClonado->getId(),
                                                'intPersonaEmprRol' => $entityPersonaEmpresaRol->getId(),
                                                'arrayPuntosCRS'    =>  $arrayPuntosCRS,                                               
                                                'strContratoFisico' => $intContratoFisico
                ); 
            }
            else
            {
                $arrayRespuestaProceso = array('strMensaje'         => $strMensajeAdvertencia,
                                                'strStatus'         => 0,
                                                'strUrl'            => $strUrlCliente,
                                                'strStatusCliente'  => $strStatusCliente,
                                                'intCodEmpresa'     => $intIdEmpresa,
                                                'intPunto'          => $entityPuntoClonado->getId(),
                                                'intPersonaEmprRol' => $entityPersonaEmpresaRol->getId(),
                                                'arrayPuntosCRS'    =>  $arrayPuntosCRS,                                               
                                                'strContratoFisico' => $intContratoFisico
                                            );
            }


            $objResponse      = new Response(json_encode($arrayRespuestaProceso));
            return $objResponse;
        }
    }

    /**
    * 
    * Función que crea Contrato Digital para cambio de razon social
    *
    * @author Néstor Naula <nnaulal@telconet.ec>
    * @version 1.0 30-08-2020
    *
    * @author Alex Gomez <algomez@telconet.ec>
    * @version 1.1 10-08-2022 Se modifica estado de los puntos y servicios clonados por CRS tradicional y por punto 
    *                           cuando el contrato aun no ha sido autorizado. Aplica para MD y contrato digital.
    *
    * @return $strStatus
    */
    public function crearContratoDigital($arrayParametros)
    {
        $emGeneral                 = $this->getDoctrine()->getManager('telconet_general');
        $emComercial               = $this->getDoctrine()->getManager('telconet');
        $objServiceUtil            = $this->get('schema.Util');
        $strClientIp               = $arrayParametros['strClientIp'];
        $strUsrCreacion            = $arrayParametros['strUsrCreacion'];
        $intCodEmpresa             = $arrayParametros['intCodEmpresa'];
        $strPrefijoEmpresa         = $arrayParametros['strPrefijoEmpresa'];
        $intIdOficina              = $arrayParametros['intIdOficina'];                    
        $arrayFormFiles            = $arrayParametros['arrayFormFiles'];            
        $arrayFormTipos            = $arrayParametros['arrayFormTipos'];
        $intIdPuntoSession         = $arrayParametros['intIdPuntoSession'];
             
        $strConvenioPago           = $arrayParametros['strConvenioPago'];
        $strVip                    = $arrayParametros['strVip'];
        $strTramiteLegal           = $arrayParametros['strTramiteLegal'];
        $strPermiteCorteAutomatico = $arrayParametros['strPermiteCorteAutomatico'];
        $strFideicomiso            = $arrayParametros['strFideicomiso'];
        $strTiempoEsperaMesesCorte = !empty($arrayParametros['strTiempoEsperaMesesCorte'])?
                                        $arrayParametros['strTiempoEsperaMesesCorte'] : '1';

        $intFormaPago              = $arrayParametros['intFormaPago'];
        $strValorAnticipo          = $arrayParametros['strValorAnticipo'];
        $strNumContratoEmpPub      = $arrayParametros['strNumContratoEmpPub'];

        $intTipoCuentaId           = $arrayParametros['intTipoCuentaId'];
        $intBancoTipoCuentaId      = $arrayParametros['intBancoTipoCuentaId'];
        $strNumeroCtaTarjeta       = $arrayParametros['strNumeroCtaTarjeta'];
        $strTitularCuenta          = $arrayParametros['strTitularCuenta'];
        $strAnioVencimiento        = $arrayParametros['strAnioVencimiento'];
        $strMesVencimiento         = $arrayParametros['strMesVencimiento'];
        $strCodigoVerificacion     = $arrayParametros['strCodigoVerificacion'];

        $intTipoContratoId         = $arrayParametros['intTipoContratoId'];
        $intPersonaEmpresaRolId    = $arrayParametros['intPersonaEmpresaRolId'];

        $strCambioPago             = $arrayParametros['strCambioPago'];
        $strTipoDocumento          = $arrayParametros['strTipoDocumento'];
        $strFeFinContratoStr       = $arrayParametros['strFeFinContratoPost'];
        $strCambioRazonSocial      = $arrayParametros['cambioRazonSocial'];
        $strCambioRazonSocialPunto = $arrayParametros['cambioRazonSocialPunto'];
        $arrayRSPuntos             = $arrayParametros['arrayRSPuntos'];
        $arrayAdendumsExcluirRS    = $arrayParametros['arrayAdendumsExcluirRS'];
        $strEnviarPin              = $arrayParametros['strEnviarPin'];
        $arrayServiciosEnv         = array();
        $arrayParametrosContrato   = array();
        $arrayAdendumsRazonSocial  = array();
        $strCambioRazonSocial      = $strCambioRazonSocial != null ? $strCambioRazonSocial : 'N';

        $objFeFinContratoPost       = date_create($strFeFinContratoStr); 
        $strFeFinContratoPost       = date_format($objFeFinContratoPost,"Y-m-d h:i:s");

        $objFeInicioContrato        = (new \DateTime('now'))->format('Y-m-d H:i:s');
        $strTelefono                = $arrayParametros['strTelefono'];
        
        $arrayTipoDocumentos = array ();  

        $strEstadoServicioPreactivo   = '';
        $strEstadoPuntoPendiente      = '';
        
        try
        {
            foreach ($arrayFormTipos as $objTipos)
            {                           
                foreach ( $objTipos as $intKeyTipo => $strValueTipo)
                {                     
                    foreach ($arrayFormFiles as $objImagenes)                 
                    {  
                        foreach ( $objImagenes as $intKeyImagen => $strValueImagen) 
                        {        
                            if($intKeyTipo == $intKeyImagen)
                            {   
                                $strImagenBase64     = base64_encode(file_get_contents($strValueImagen)); 
                                $objTiposDocumentos  = $emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
                                                                ->findOneById($strValueTipo);

                                $arrayTipoDocumentos[$intKeyTipo] = array(
                                                                    "tipoDocumentoGeneralId"     => $strValueTipo,
                                                                    "codigoTipoDocumentoGeneral" => $objTiposDocumentos->getCodigoTipoDocumento(),
                                                                    "documento"                  => $strImagenBase64); 
                            }                   
                        }                          
                    
                    }             
                }
            }

            //Consulta nuevo estado para servicios creados por Cambio de Razón Social por Login
            //previo a la autorizacion del contrato. Solo aplica para MD y contrato Digital
            if($arrayParametros['strPrefijoEmpresa'] === 'MD' || $arrayParametros['strPrefijoEmpresa'] === 'EN')
            {
                $arrayEstadosServicios = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getOne(
                                                        'ESTADOS_CAMBIO_RAZON_SOCIALXPUNTO',
                                                        'COMERCIAL',
                                                        'CAMBIO_RAZON_SOCIAL_POR_PUNTO',
                                                        '','','','','','',
                                                        $arrayParametros['intCodEmpresa']);
                
                if(isset($arrayEstadosServicios) && !empty($arrayEstadosServicios))
                {
                    $strEstadoServicioPreactivo = $arrayEstadosServicios["valor1"];
                    $strEstadoPuntoPendiente = 'Pendiente';
                }
            }
            
            // Validar si existe un contrato activo del muevo personaEmpresaRolId
            $objContratoActivo = $emComercial->getRepository('schemaBundle:InfoContrato')
                                            ->findBy(array("personaEmpresaRolId" => $intPersonaEmpresaRolId,
                                                            "estado"              => array('PorAutorizar', 'Activo')));
            if(!empty($objContratoActivo) && count($objContratoActivo) > 0)
            {
                $objContrato                 = $objContratoActivo[0];
            }
            if($strCambioRazonSocial == "S")
            {
                if(!empty($intPersonaEmpresaRolId))
                {
                    if($strCambioRazonSocialPunto == "S")
                    {
                        ///Se agrega el estado Pendiente para mapeo de punto previa autorización del contrato
                        $arrayPuntosCliente = $emComercial->getRepository('schemaBundle:InfoPunto')
                                                        ->findBy(array("personaEmpresaRolId" => $intPersonaEmpresaRolId,
                                                                        "id"                  => $arrayRSPuntos,
                                                                        "estado"              => array("Activo",$strEstadoPuntoPendiente)));
                    } else
                    {
                        ///Se agrega el estado Pendiente para mapeo de punto previa autorización del contrato
                        $arrayPuntosCliente = $emComercial->getRepository('schemaBundle:InfoPunto')
                                                        ->findBy(array("personaEmpresaRolId" => $intPersonaEmpresaRolId,
                                                                        "estado"              => array("Activo",$strEstadoPuntoPendiente)));
                    }
                } else
                {
                    throw new \Exception("Parámetro personaEmpresaRolId no encontrado.");
                }

                $boolCrearNumeroAdendum = false;

                if(is_object($objContrato))
                {
                    $strTipoDocumento = "AP";
                    $boolCrearNumeroAdendum = true;
                }
                
                $strTipoAdendum   = $strTipoDocumento;
                $boolPasarAdendum = false;  
                foreach ($arrayPuntosCliente as $objPunto)
                {
                    ///Linea Modifico
                    $arrayServicios = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                ->findBy(array("puntoId" => $objPunto->getId(),
                                                                "estado"  => array("Activo",$strEstadoServicioPreactivo)));

                    //Obtener la numeracion de la tabla Admi_numeracion
                    $strCodigoNumeracion = $strTipoDocumento == 'AS' ? 'CONA' : 'CON';
                    $objDatosNumeracion  = $emComercial->getRepository('schemaBundle:AdmiNumeracion')
                                                    ->findByEmpresaYOficina($intCodEmpresa, $intIdOficina, $strCodigoNumeracion);
                    if($objDatosNumeracion)
                    {
                        $intSecuencia                   = str_pad($objDatosNumeracion->getSecuencia(), 7, "0", STR_PAD_LEFT);
                        $strNumeroAdendum               = $objDatosNumeracion->getNumeracionUno()
                                                        . "-"
                                                        . $objDatosNumeracion->getNumeracionDos()
                                                        . "-"
                                                        . $intSecuencia;
                        $boolActualizarSecuenciaAdendum = false;
                    } else
                    {
                        throw new \Exception("No se pudo obtener la numeración", 206);
                    }

                    foreach ($arrayServicios as $objServicio)
                    { 
                        $boolPasarAdendum = true;  
                        $objAdendumActivo = $emComercial->getRepository('schemaBundle:InfoAdendum')
                                                        ->findBy(array("puntoId"    => $objPunto->getId(),
                                                                    "servicioId" => $objServicio->getId(),
                                                                    "estado"     => array('Activo', 'Migrado')));

                        if(empty($objAdendumActivo) && count($objAdendumActivo) == 0)
                        {
                            $objAdendumRSPendiente = $emComercial->getRepository('schemaBundle:InfoAdendum')
                                                                ->findBy(array("puntoId"    => $objPunto->getId(),
                                                                                "servicioId" => $objServicio->getId(),
                                                                                "estado"     => 'Pendiente'));
                            if(!empty($objAdendumRSPendiente) && count($objAdendumRSPendiente) > 0)
                            {
                                $intAdendumPendiente = $objAdendumRSPendiente[0]->getId();
                                if(!in_array($intAdendumPendiente, $arrayAdendumsExcluirRS))
                                {
                                    array_push($arrayAdendumsRazonSocial, $objAdendumRSPendiente[0]->getId());
                                }
                            } 
                            else
                            {
                                //Si es cambio de razon social, se inserta los puntos y servicios en info adendum
                                $entityInfoAdendumNewServ = new InfoAdendum();
                                $entityInfoAdendumNewServ->setFeCreacion(new \DateTime('now'));
                                $entityInfoAdendumNewServ->setPuntoId($objPunto->getId());
                                $entityInfoAdendumNewServ->setServicioId($objServicio->getId());
                                $entityInfoAdendumNewServ->setIpCreacion($strClientIp);
                                $entityInfoAdendumNewServ->setUsrCreacion($strUsrCreacion);
                               
                                $entityInfoAdendumNewServ->setTipo($strTipoAdendum);
                                if($boolCrearNumeroAdendum)
                                {
                                    $entityInfoAdendumNewServ->setNumero($strNumeroAdendum);
                                    $boolActualizarSecuenciaAdendum = true;
                                }
                            
                                $entityInfoAdendumNewServ->setEstado("Pendiente");
                                $emComercial->persist($entityInfoAdendumNewServ);
                                $emComercial->flush();

                                array_push($arrayAdendumsRazonSocial, $entityInfoAdendumNewServ->getId());
                            }
                        }
                    }
                    if($boolPasarAdendum)
                    {
                        $strTipoAdendum = "AP";
                        $boolCrearNumeroAdendum = true;
                    }
                    $boolPuntoContratoRegistrado = true;

                    if($boolActualizarSecuenciaAdendum)
                    {
                        //Actualizo la numeracion en la tabla
                        $intSecuencia = ($objDatosNumeracion->getSecuencia() + 1);
                        $objDatosNumeracion->setSecuencia($intSecuencia);
                        $emComercial->persist($objDatosNumeracion);
                        $emComercial->flush();
                    }
                }
            }
            else
            {
                if(empty($intIdPuntoSession) && !empty($intPersonaEmpresaRolId))
                {
                    $objPunto = $emComercial->getRepository('schemaBundle:InfoPunto')
                                            ->findOneByPersonaEmpresaRolId($intPersonaEmpresaRolId);

                    $intIdPuntoSession = $objPunto->getId();
                }

                if(!empty($intIdPuntoSession))
                {
                    $arrayServicios = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                ->findByPuntoId($intIdPuntoSession);
                }

                foreach ($arrayServicios as $objServicio)
                {
                    array_push($arrayServiciosEnv, $objServicio->getId());
                }
            }

            $arrayDatosTarjeta = array('numeroCtaTarjeta'   => $strNumeroCtaTarjeta,
                                    'titularCuenta'      => $strTitularCuenta,
                                    'anioVencimiento'    => $strAnioVencimiento,
                                    'mesVencimiento'     => $strMesVencimiento,
                                    'codigoVerificacion' => $strCodigoVerificacion);
            if($strTipoDocumento == 'C')
            {
                $arrayDatosContrato = array_merge(
                        array('servicioId' => '1'),
                        array('servicios' => $arrayServiciosEnv),
                        array('puntoId' => $intIdPuntoSession),
                        array('valorAnticipo' => $strValorAnticipo),
                        array('numContratoEmpPub' => $strNumContratoEmpPub),
                        array('codNumeracionVE' => ""),
                        array('feInicioContrato' => $objFeInicioContrato),
                        array('feFinContratoPost' => $strFeFinContratoPost),
                        array('esConvenioPago' => $strConvenioPago),
                        array('esTramiteLegal' => $strTramiteLegal),
                        array('esVip' => $strVip),
                        array('permitirCorteAutomatico' => $strPermiteCorteAutomatico),
                        array('fideicomiso' => $strFideicomiso),
                        array('tiempoEsperaMesesCorte' => $strTiempoEsperaMesesCorte),
                        array('tipoContratoId' => $intTipoContratoId),
                        array('tipoCuentaId' => $intTipoCuentaId),
                        array('bancoTipoCuentaId' => $intBancoTipoCuentaId),
                        array('formaPagoId' => $intFormaPago),
                        array('adendumsRazonSocial' => $arrayAdendumsRazonSocial),
                        $arrayDatosTarjeta
                );
                $arrayParametrosContrato['contrato'] = $arrayDatosContrato;
            }
            else
            {
                if(!is_object($objContrato))
                {
                    throw new \Exception("No existe un contrato PorAutorizar o Activo");
                }
                $arrayDatosAdendum = array_merge(
                        array('contratoId' => $objContrato->getId()),
                        array('puntoId' => $intIdPuntoSession),
                        array('servicios' => $arrayServiciosEnv),
                        array('cambioNumeroTarjeta' => $strCambioPago),
                        array('tipoCuentaId' => $intTipoCuentaId),
                        array('bancoTipoCuentaId' => $intBancoTipoCuentaId),
                        array('formaPagoId' => $intFormaPago),
                        array('adendumsRazonSocial' => $arrayAdendumsRazonSocial),
                        $arrayDatosTarjeta
                );
                $arrayParametrosContrato['adendum'] = $arrayDatosAdendum;
            }
            
            /* @var $serviceTokenCas \telconet\seguridadBundle\Service\TokenCasService */
            $serviceTokenCas = $this->get('seguridad.TokenCas');
            $arrayTokenCas = $serviceTokenCas->generarTokenCas();

            if(empty($arrayTokenCas['strToken']))
            {
                throw new \Exception($arrayTokenCas['strMensaje']); 
            }

            $arrayParametrosContrato['ipCreacion']          = $strClientIp; 
            $arrayParametrosContrato['codEmpresa']          = $intCodEmpresa;
            $arrayParametrosContrato['prefijoEmpresa']      = $strPrefijoEmpresa; 
            $arrayParametrosContrato['usrCreacion']         = $strUsrCreacion; 
            $arrayParametrosContrato['origen']              = 'WEB';
            $arrayParametrosContrato['enviarPin']           = $strEnviarPin;
            $arrayParametrosContrato['oficinaId']           = $intIdOficina;
            $arrayParametrosContrato['tipo']                = $strTipoDocumento;
            $arrayParametrosContrato['numeroTelefonico']    = $strTelefono;
            $arrayParametrosContrato['personaEmpresaRolId'] = $intPersonaEmpresaRolId; 
            $arrayParametrosContrato['token']               = $arrayTokenCas['strToken'];
            $arrayParametrosContrato['cambioRazonSocial']   = $strCambioRazonSocial;
            $arrayParametrosContrato['documentosContrato']  = $arrayTipoDocumentos;

            /* @var $serviceInfoContrato \telconet\comercialBundle\Service\InfoContratoService */
            $serviceInfoContrato = $this->get('comercial.InfoContrato');

            $objInfoPersonaEmpresaRol        = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intPersonaEmpresaRolId);
            //Obtengo Característica de USUARIO
            $objCaracteristicaUsuario = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                    ->findOneBy(array("descripcionCaracteristica" => "USUARIO",
                                                                      "estado"                    => "Activo"));

            if(is_object($objCaracteristicaUsuario))
            {
                $arrayParameCaractPersona = array( 
                                                'estado'              => 'Activo',
                                                'personaEmpresaRolId' => $objInfoPersonaEmpresaRol,
                                                'caracteristicaId'    => $objCaracteristicaUsuario
                                            );

                $objPersonaEmpresaRolCarac = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                        ->findOneBy( $arrayParameCaractPersona );

                //Inserto Caracteristica de USUARIO si no existe
                if(!is_object($objPersonaEmpresaRolCarac))
                {
                    $objPersEmpRolCaracUsuario = new InfoPersonaEmpresaRolCarac();
                    $objPersEmpRolCaracUsuario->setPersonaEmpresaRolId($objInfoPersonaEmpresaRol);
                    $objPersEmpRolCaracUsuario->setCaracteristicaId($objCaracteristicaUsuario);
                    $objPersEmpRolCaracUsuario->setValor($objInfoPersonaEmpresaRol->getPersonaId()->getIdentificacionCliente());
                    $objPersEmpRolCaracUsuario->setFeCreacion(new \DateTime('now'));
                    $objPersEmpRolCaracUsuario->setUsrCreacion($strUsrCreacion);
                    $objPersEmpRolCaracUsuario->setEstado('Activo');
                    $objPersEmpRolCaracUsuario->setIpCreacion($strClientIp);
                    $emComercial->persist($objPersEmpRolCaracUsuario);
                    $emComercial->flush();
                }
            }


            $arrayRespuesta   = $serviceInfoContrato->crearContratoMS($arrayParametrosContrato);
            return $arrayRespuesta;
        }
        catch (\Exception $e)
        {   
            $objServiceUtil->insertError('Telcos+',
                                      'ClienteController.crearContratoDigital',
                                      $e->getMessage(),
                                      $strUsrCreacion,
                                      $strClientIp
                                     );

            $arrayRespuesta =  array('strMensaje' => 'Ocurrio un error al Crear Contrato, comuniquese con el Departamento de Sistemas',
                                    'strStatus'   => 99);
            return $arrayRespuesta;
        }
    }

    /**
     *
     * Documentación para el método 'cancelarClientePorRazonSocial'.
     *
     * Método que cancela al cliente, su contrato, forma de pago, puntos, servicios,  asociados junto 
     * 
     * @param $arrayParametros['ID_PERSONA_ROL']  Id de la info_persona_empresa_rol del cliente anterior.
     * @param $arrayParametros['REQUEST']         Objeto Request.
     * @param $arrayParametros['COMERCIAL']       Entity Manager Comercial.
     * @param $arrayParametros['INFRAESTRUCTURA'] Entity Manager Infraestructura.
     * @param $arrayParametros['PERSONA_ROL']     Entidad info_persona_empresa_rol del cliente nuevo.
     * @param $arrayParametros['USR_CREACION']    Login de usuario que procesa la operación.
     * @param $arrayParametros['GENERAL']         Entity Manager General.
     * @param $arrayParametros['COD_EMPRESA']     Id de la empresa.
     * 
     * @return Render Renderización de a ventana para la asignación del ejecutivo de Cobranza
     * 
     * @author Alejandro Domínguez<adominguez@telconet.ec>       
     * @version 1.1 30-08-2016
     * @since   1.0
     * Se organiza el código, se documenta y segmentan los procesos.
     * Se reúnen los parámetros en un arreglo.
     * Se registra el historial de cancelación por cambio de razón social con la debida obervación.
     * 
     * @author Anabelle Penaherrera<apenaherrera@telconet.ec>
     * @version 1.2 02-05-2017 -Se Cancelan las Plantillas de Comisionistas asociadas a los servicios del cliente origen del Cambio de Razon Social
     * Se genera Historial de Cancelacion de Plantilla. 
     * 
     * @author Anabelle Penaherrera<apenaherrera@telconet.ec>
     * @version 1.3 22-06-2018- Se agrega que se generen las caracteristicas de los servicios en estado activo, y se considera para el Producto
     *                          Fox_Primium que al clonar dichas caracteristicas se marque la caracteristica 'MIGRADO_FOX' en S.
     * 
     * @author kevin ortiz <kcortiz@telconet.ec>
     * @version 1.3  22-10-2020  - Se agrega el proceso para cancelacion de Productos IPMP
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.4 20-06-2018 Se agrega validación para que se coloque el estado Eliminado en lugar de Cancel para servicios W+Ap
     *
     * @author Néstor Naula<nnaulal@telconet.ec>
     * @version 1.5 08-11-2020- Se agrega validación para cambiar de estado la forma de pago si existe un contrato.
     * @since 1.4
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.5 21-04-2021 Se agrega validación para que se coloque el estado Eliminado en lugar de Cancel para servicios Extender Dual Band
     * 
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.6 14-07-2021 - Problemas al realizar cambio de razón social al momento de migrar el producto I. Protegido
     */
    public function cancelarClientePorRazonSocial($arrayParametros)
    {
        $idPersonaEmpresaRol     = $arrayParametros['ID_PERSONA_ROL'];
        $objRequest              = $arrayParametros['REQUEST'];
        $emComercial             = $arrayParametros['COMERCIAL'];
        $emInfraestructura       = $arrayParametros['INFRAESTRUCTURA'];
        $emGeneral               = $arrayParametros['GENERAL'];
        $strCodEmpresa           = $arrayParametros['COD_EMPRESA'];
        $entityPersonaRol        = $arrayParametros['PERSONA_ROL'];
        $usrCreacion             = $arrayParametros['USR_CREACION'];
        $intIdEmpresa            = $arrayParametros["strCodEmpresa"];
        $strIpCreacion           = $arrayParametros['strClientIp'];
        $strEstadoCancelacion    = 'Cancelado';
        $serviceServicioIPMP = $this->get('tecnico.LicenciasKaspersky');
        
        try
        {
            // 1.- CANCELACIÓN DEL CLIENTE
            $entityPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($idPersonaEmpresaRol);
            $entityPersonaEmpresaRol->setEstado($strEstadoCancelacion);
            $emComercial->persist($entityPersonaEmpresaRol);

            // Obtener Identificación y Nombre del Cliente Destino
            $objClienteDestino  = $entityPersonaRol->getPersonaId();
            $strClienteDestino  = $objClienteDestino->getIdentificacionCliente() .  " - ";
            $strClienteDestino .= $objClienteDestino->getRazonSocial() ? $objClienteDestino->getRazonSocial() :
                                                                        $objClienteDestino->getApellidos() . " " .$objClienteDestino->getNombres();
            // 2.- HISTORIAL DE CANCELACIÓN DEL CLIENTE
            $entity_persona_historial = new InfoPersonaEmpresaRolHisto();
            $entity_persona_historial->setEstado($strEstadoCancelacion);
            $entity_persona_historial->setFeCreacion(new \DateTime('now'));
            $entity_persona_historial->setIpCreacion($objRequest->getClientIp());
            $entity_persona_historial->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
            $entity_persona_historial->setUsrCreacion($usrCreacion);
            $entity_persona_historial->setObservacion("Se Cambió a la Razón Social a: $strClienteDestino");
            $emComercial->persist($entity_persona_historial);

            // 3.- CANCELACIÓN DEL CONTRATO DEL CLIENTE
            $entityInfoContrato = $emComercial->getRepository('schemaBundle:InfoContrato')
                                            ->findOneBy(array("personaEmpresaRolId" => $idPersonaEmpresaRol, "estado" => "Activo"));
            if(is_object($entityInfoContrato))
            {
                $entityInfoContrato->setEstado($strEstadoCancelacion);
                $emComercial->persist($entityInfoContrato);

                // 4.- CANCELACIÓN DE LAS FORMAS DE PAGO DEL CONTRATO DEL CLIENTE
                $objContratosFormaPago = $emComercial->getRepository('schemaBundle:InfoContratoFormaPago')
                                                  ->findByContratoId($entityInfoContrato->getId());
                foreach($objContratosFormaPago as $cfp)
                {
                    $cfp->setEstado($strEstadoCancelacion);
                    $emComercial->persist($cfp);
                }
            }
            // 5.- CANCELACIÓN DE LOS PUNTOS DEL CLIENTE
            $puntos = $emComercial->getRepository('schemaBundle:InfoPunto')->findByPersonaEmpresaRolId($idPersonaEmpresaRol);
            foreach($puntos as $pto)
            {
                $pto->setEstado($strEstadoCancelacion);
                $emComercial->persist($pto);

                // 6.- CANCELACIÓN DE LOS SERVICIOS DEL PUNTO
                $servicios = $emComercial->getRepository('schemaBundle:InfoServicio')->findByPuntoId($pto->getId());

                foreach($servicios as $serv)
                {
                    $strEstadoSpcAnterior           = "Cancelado";
                    $strEstadoServicioAnterior      = "Cancel";
                    $strObservacionServicioAnterior = "Cancelado por cambio de razón social";
                    if(is_object($serv->getProductoId()) 
                        && ($serv->getProductoId()->getNombreTecnico() === "WDB_Y_EDB"
                            || $serv->getProductoId()->getNombreTecnico() === "EXTENDER_DUAL_BAND"))
                    {
                        $arrayEstadoPermitidoCRSWdbyEdb = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                    ->getOne(   'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD',
                                                                                '',
                                                                                '',
                                                                                '',
                                                                                'CAMBIO_RAZON_SOCIAL',
                                                                                'ESTADOS_SERVICIOS_X_PROD_FLUJO_PERSONALIZADO',
                                                                                $serv->getProductoId()->getNombreTecnico(),
                                                                                $serv->getEstado(),
                                                                                '',
                                                                                $strCodEmpresa);
                        if(isset($arrayEstadoPermitidoCRSWdbyEdb) && !empty($arrayEstadoPermitidoCRSWdbyEdb))
                        {
                            $strEstadoServicioAnterior      = "Eliminado";
                            $strObservacionServicioAnterior = "Eliminado por cambio de razón social";
                            $strEstadoSpcAnterior           = "Eliminado";
                        }
                    }
                    $serv->setEstado($strEstadoServicioAnterior);
                    $emComercial->persist($serv);

                    // 7.- HISTORIAL DE CANCELACIÓN DEL SERVICIO NombreMotivo
                    $arrayParams             = array('nombreMotivo' => 'Cambio de Razon Social', 'estado' => 'Activo');
                    $entityAdmiMotivo        = $emComercial->getRepository('schemaBundle:AdmiMotivo')->findOneBy($arrayParams);
                    $entityServicioHistorial = new InfoServicioHistorial();
                    $entityServicioHistorial->setServicioId($serv);
                    $entityServicioHistorial->setFeCreacion(new \DateTime('now'));
                    $entityServicioHistorial->setIpCreacion($objRequest->getClientIp());
                    $entityServicioHistorial->setUsrCreacion($usrCreacion);
                    $entityServicioHistorial->setMotivoId($entityAdmiMotivo ? $entityAdmiMotivo->getId() : null);

                    $entityServicioHistorial->setObservacion($strObservacionServicioAnterior);
                    $entityServicioHistorial->setEstado($strEstadoServicioAnterior);
                    $emComercial->persist($entityServicioHistorial);

                    // 8.- CANCELACIÓN DE LAS CARACTERÍSTICAS DEL PRODUCTO-SERVICIO                 
                    //Obtengo Caracteristicas del servicio en estado Activo
                    $objInfoServicioProdCaract = $emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                            ->findBy(array("servicioId" => $serv->getId()));
                                                            
                    foreach($objInfoServicioProdCaract as $servpc)
                    {
                        
                        $intProductoCaracteristica = $servpc->getProductoCaracterisiticaId();
                        $arrayEstadosCaract = array('Activo','Pendiente');
                        if(in_array($servpc->getEstado(),$arrayEstadosCaract))
                        {
                            $objAdmiProductoCaracteristica = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                        ->find($intProductoCaracteristica);

                            $objAdmiCaracteristica = $objAdmiProductoCaracteristica->getCaracteristicaId();

                            if($objAdmiCaracteristica->getDescripcionCaracteristica() == "SUSCRIBER_ID")
                            {
                                
                                $strEstadoServicioInicial = $serv->getEstado();
                                $objProductoIPMP = $emComercial->getRepository('schemaBundle:AdmiProducto')
                                                                ->findOneBy(array("descripcionProducto" => 'I. PROTEGIDO MULTI PAID',
                                                                                    "estado" => "Activo"));

                                $arrayProCaractAntivirus = array( "objServicio"       => $serv,
                                                                    "objProducto"       => $objProductoIPMP,
                                                                    "strUsrCreacion"    => $usrCreacion);

                                $arrayProCaractAntivirus["strCaracteristica"] = "SUSCRIBER_ID";
                                $strRespuestaCaract = $serviceServicioIPMP
                                                            ->obtenerValorServicioProductoCaracteristica($arrayProCaractAntivirus);
                                if(is_object($strRespuestaCaract["objServicioProdCaract"]))
                                {
                                    $intSuscriberId = $strRespuestaCaract["objServicioProdCaract"]->getValor();
                                }

                                $arrayProCaractAntivirus["strCaracteristica"] = 'CORREO ELECTRONICO';
                                $arrayRespuestaGetSpc  = $serviceServicioIPMP
                                                                ->obtenerValorServicioProductoCaracteristica($arrayProCaractAntivirus);
                                if(is_object($arrayRespuestaGetSpc["objServicioProdCaract"]))
                                {
                                    $strCorreoSuscripcion = $arrayRespuestaGetSpc["objServicioProdCaract"]->getValor();
                                }

                                if(($intSuscriberId) && ($strCorreoSuscripcion))
                                {
                                    $strMsjErrorAdicHtml            = "No se pudo actualizar la suscripción al correo ".$strCorreoSuscripcion."<br>";

                                    $arrayParamsCancelarLicencias  = array("strProceso"               => "CANCELACION_ANTIVIRUS",
                                                                            "strEscenario"              => "CANCELACION_PROD_EN_PLAN",
                                                                            "objServicio"               => $serv,
                                                                            "objPunto"                  => $serv->getPuntoId(),
                                                                            "strCodEmpresa"             => $intIdEmpresa,
                                                                            "objProductoIPMP"           => $objProductoIPMP,
                                                                            "strUsrCreacion"            => $usrCreacion,
                                                                            "strIpCreacion"             => $strIpCreacion,
                                                                            "strEstadoServicioInicial"  => $strEstadoServicioInicial,
                                                                            "intSuscriberId"            => $intSuscriberId,
                                                                            "strCorreoSuscripcion"      => $strCorreoSuscripcion,
                                                                            "strMsjErrorAdicHtml"       => $strMsjErrorAdicHtml
                                                                            );

                                    $arrayRespuestaCancelarLicencias = $serviceServicioIPMP->gestionarLicencias($arrayParamsCancelarLicencias);
                                    $strStatusCancelarLicencias     = $arrayRespuestaCancelarLicencias["status"];
                                    $strMensajeCancelarLicencias    = $arrayRespuestaCancelarLicencias["mensaje"];
                                    $arrayRespuestaCancelarWs       = $arrayRespuestaCancelarLicencias["arrayRespuestaWs"];

                                    if($strStatusCancelarLicencias === "ERROR")
                                    {
                                        $strMostrarError = "SI";
                                        throw new \Exception($strMensajeCancelarLicencias);
                                    }
                                    else if(isset($arrayRespuestaCancelarWs) && !empty($arrayRespuestaCancelarWs) && 
                                            $arrayRespuestaCancelarWs["status"] !== "OK")
                                    {
                                        $strMostrarError = "SI";
                                        throw new \Exception($arrayRespuestaCancelarWs["mensaje"]);
                                    }
                                }
                            }
                            
                            // paso el valor de la caracteristica 'MIGRADO_FOX' a S, ya que el servicio fue clonado o migrado 
                            // por el cambio de razon social
                            $arrayParametrosFox = array();
                            $objRespuestaValidacion = null;
                            $arrayParametrosFox["strDescripcionCaracteristica"] = "MIGRADO_FOX";
                            $arrayParametrosFox["strNombreTecnico"]             = "FOXPREMIUM";
                            $arrayParametrosFox["intIdServicio"]                = $serv->getId();
                            $arrayParametrosFox["intIdServProdCaract"]          = $servpc->getId();
                            $arrayParametrosFox["strEstadoSpc"]                 = 'Activo';
                                        
                            $objRespuestaServProdCarac = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                                    ->getCaracteristicaServicio($arrayParametrosFox);
                            if (is_object($objRespuestaServProdCarac))
                            {
                                $servpc->setValor('S');
                            }
                            // Se procede a Cancelar las caracteristicas de los Servicios Origen del Cambio de Razon Social
                            $servpc->setEstado($strEstadoSpcAnterior);
                            $servpc->setFeUltMod(new \DateTime('now'));
                            $servpc->setUsrUltMod($usrCreacion);
                            $emComercial->persist($servpc); 
                        }
                    }               
                    
                    // 9.- CANCELACIÓN DE LA INFORMACIÓN IP DEL SERVICIO 
                    $ips = $emInfraestructura->getRepository('schemaBundle:InfoIp')->findByServicioId($serv->getId());
                    foreach($ips as $ip)
                    {
                        $ip->setEstado($strEstadoCancelacion);
                        $emInfraestructura->persist($ip);
                    }
                    
                    //Obtengo las plantillas de Comisionistas por servicios en estado Activo
                    $arrayServicioComision = $emComercial->getRepository('schemaBundle:InfoServicioComision')
                                                        ->findBy(array("servicioId" => $serv->getId(), "estado" => "Activo"));

                    foreach($arrayServicioComision as $objServicioComision)
                    {
                        //Cancelo estado de la plantilla del cliente origen del cambio de razon social, guardo usuario, ip y fecha.
                        $objServicioComision->setEstado($strEstadoCancelacion);
                        $objServicioComision->setFeUltMod(new \DateTime('now'));
                        $objServicioComision->setIpUltMod($objRequest->getClientIp());
                        $objServicioComision->setUsrUltMod($usrCreacion);
                        $emComercial->persist($objServicioComision);

                        /* Guardo un registro en el Historico en la plantilla del cliente origen del cambio de razon social 
                        que se Cancela */
                        $objInfoServicioComisionHisto = new InfoServicioComisionHisto();
                        $objInfoServicioComisionHisto->setServicioComisionId($objServicioComision);
                        $objInfoServicioComisionHisto->setServicioId($objServicioComision->getServicioId());
                        $objInfoServicioComisionHisto->setComisionDetId($objServicioComision->getComisionDetId());
                        $objInfoServicioComisionHisto->setPersonaEmpresaRolId($objServicioComision->getPersonaEmpresaRolId());
                        $objInfoServicioComisionHisto->setComisionVenta($objServicioComision->getComisionVenta());
                        $objInfoServicioComisionHisto->setComisionMantenimiento($objServicioComision->getComisionMantenimiento());
                        $objInfoServicioComisionHisto->setEstado($objServicioComision->getEstado());
                        $objInfoServicioComisionHisto->setObservacion('Plantilla de Comisionistas cancelada por cambio de razón social');
                        $objInfoServicioComisionHisto->setFeCreacion(new \DateTime('now'));
                        $objInfoServicioComisionHisto->setIpCreacion($objRequest->getClientIp());
                        $objInfoServicioComisionHisto->setUsrCreacion($usrCreacion);
                        $emComercial->persist($objInfoServicioComisionHisto);
                    }
                }            
            }

            $emComercial->flush();
            $emInfraestructura->flush();
        }
        catch (\Exception $e) 
        {
            error_log($e->getMessage());
        }
    }

    public function obtieneIdPersonaEmpresaRolPorIdCliente($idPersona,$idEmpresa,$em){
			$tieneCancelado=false;
			$tieneOtroEstado=false;
			$datosPersonaEmpresaRol=$em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
			->getPersonaEmpresaRolPorPersonaPorTipoRolTodos($idPersona,'Cliente',$idEmpresa);
			foreach($datosPersonaEmpresaRol as $per){
				if(($per->getEstado()=='Cancelado')||($per->getEstado()=='Cancel')){
					$tieneCancelado=true;
					$personaEmpresaRolCancelado=$per;
				}else
				{
					$tieneOtroEstado=true;
					$personaEmpresaRolOtroEstado=$per;
				}		
			}
			if($tieneCancelado && $tieneOtroEstado)
			{
				$personaEmpresaRol=$personaEmpresaRolOtroEstado;        				
			}elseif($tieneCancelado && !$tieneOtroEstado)
			{
				$personaEmpresaRol=$personaEmpresaRolCancelado;
			}else
			{
				$personaEmpresaRol=$personaEmpresaRolOtroEstado;
			}	
			return $personaEmpresaRol;
	}
	

    /**
     * @Secure(roles="ROLE_8-625")
     * 
     * Documentación para el método 'actualizaDireccionTributariaAction'.
     *
     * Método que actualiza la dirección tributaria de la persona.
     * 
     * @return Response resultado de la actualización.
     *
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>       
     * @version 1.1 22-08-2016
     * @since   1.0
     * Se normaliza el código, se corrige la programación.
     * Se actualiza también la dirección normal de la persona.
     * Se deja un registro historial del cambio.
     */
    public function actualizaDireccionTributariaAction()
    {
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
		
        $objPeticion = $this->get('request');
        $objSesion   = $objPeticion->getSession();
        $emComercial = $this->getDoctrine()->getManager('telconet');
        $emComercial->getConnection()->beginTransaction();
        
        try 
        {
            $intPersonaId  = $objPeticion->get('id_persona');
            $entityPersona = $emComercial->getRepository('schemaBundle:InfoPersona')->find($intPersonaId);
            
            if(!$entityPersona)
            {
                throw $this->createNotFoundException('Entity InfoPersona not found.');
            }
            
            $intPersonaEmpresaRolId  = $objPeticion->get('id_personaEmpresaRol');
            $entityPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intPersonaEmpresaRolId);
            
            if(!$entityPersonaEmpresaRol)
            {
                throw new \Exception("Entity InfoPersonaEmpresaRol not found.");
            }
            
            $entityAdmiMotivo = $emComercial->getRepository('schemaBundle:AdmiMotivo')
                                            ->findOneBy(array('nombreMotivo' => 'CAMBIO DATOS FACTURACION'));
            if(!$entityAdmiMotivo)
            {
                throw new \Exception("Entity AdmiMotivo not found.");
            }

            $strDirTributaria = trim($objPeticion->get('direccionTributaria'));

            if(!$strDirTributaria)
            {
                throw new \Exception("Debe ingresar una dirección válida.");
            }
            else if(strtolower($strDirTributaria) == strtolower($entityPersona->getDireccionTributaria()))
            {
                throw new \Exception("Debe ingresar una dirección diferente a la anterior.");
            }
            
            $strUsuarioCreacion = $objSesion->get('user');
            
            //REGISTRA EN LA TABLA DE PERSONA HISTORIAL           
            $objPersonaEmpresaRolHistorial = new InfoPersonaEmpresaRolHisto();
            $objPersonaEmpresaRolHistorial->setEstado($entityPersona->getEstado());
            $objPersonaEmpresaRolHistorial->setFeCreacion(new \DateTime('now'));
            $objPersonaEmpresaRolHistorial->setIpCreacion($objPeticion->getClientIp());
            $objPersonaEmpresaRolHistorial->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
            $objPersonaEmpresaRolHistorial->setUsrCreacion($strUsuarioCreacion);
            $objPersonaEmpresaRolHistorial->setObservacion("Dirección anterior: " . $entityPersona->getDireccionTributaria());
            $objPersonaEmpresaRolHistorial->setMotivoId($entityAdmiMotivo->getId());
            $emComercial->persist($objPersonaEmpresaRolHistorial);
            
            $entityPersona->setDireccionTributaria($strDirTributaria);
            $entityPersona->setDireccion($strDirTributaria);
            $emComercial->persist($entityPersona);
            $emComercial->flush();
            $emComercial->getConnection()->commit();
			
			$resultado = json_encode(array('success'=>true));
        } 
        catch (\Exception $e) 
        {
            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
            $resultado = json_encode(array('success' => false, 'mensaje' => $e->getMessage()));
        }

        $objRespuesta->setContent($resultado);
        return $objRespuesta;        
    }
    
    /** 
    * Modificado: Se agrega validacion que solo se procese la data si encuentra la persona empresa rol id con rol cliente
    * @author: Edgar Pin Villavicencio <epin@telconet.ec>
    * @version: 30-05-2023
    */
    
    public function excelReferidosAction() {
        $objPHPExcel = new PHPExcel();
        $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array( ' memoryCacheSize ' => '1024MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);		
        // Establecer propiedades
        $objPHPExcel->getProperties()
        ->setCreator("Telcos")
        ->setLastModifiedBy("Telcos")
        ->setTitle("Documento Excel de Referidos")
        ->setSubject("Documento Excel de Referidos")
        ->setDescription("")
        ->setKeywords("Excel Office 2007 openxml php")
        ->setCategory("Excel");
        
        $request = $this->getRequest();
        $estado = '';
        $fechaDesde = explode('T', $request->get("fechaDesde"));
        $fechaHasta = explode('T', $request->get("fechaHasta"));
        $estado = $request->get("estado");
        $nombre = $request->get("nombre");
        $apellido = $request->get("apellido");
        $razonSocial = $request->get("razonSocial");		
        $limit = $request->get("limit");
        $page = $request->get("page");
        $start = $request->get("start");
        $idEmpresa = $request->getSession()->get('idEmpresa');
        $em = $this->get('doctrine')->getManager('telconet');
        $emfn = $this->get('doctrine')->getManager('telconet_financiero');

        $resultado = $em->getRepository('schemaBundle:InfoPersona')
        ->findReferidosPorCriterios($estado, $idEmpresa, '', $fechaDesde[0], $fechaHasta[0], 
        $nombre, $apellido, $razonSocial, 'Cliente', $limit, $page, $start);

        $datos = $resultado['registros'];

        //print_r($datos);die;    
        $i = 2;

        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1', 'CLIENTE')
        ->setCellValue('B1', 'NUEVO_CLIENTE')
        ->setCellValue('C1', 'PAGOS')
        ->setCellValue('D1', 'DIRECCION')
        ->setCellValue('E1', 'FECHA_CREACION')
        ->setCellValue('F1', 'DESCUENTO')
        ->setCellValue('G1', 'FACTURA')
        ->setCellValue('H1', 'FECHA_EMISION')
        ->setCellValue('I1', 'ESTADO_FACTURA')                
        ->setCellValue('J1', 'ESTADO_CLIENTE')
        ;        
        
        foreach ($datos as $datos):
            $entityPersonaEmpresaRol=$this->obtieneIdPersonaEmpresaRolPorIdCliente($datos['idCliente'],$idEmpresa,$em);

            
            if ($entityPersonaEmpresaRol)
            {
                
                $entityPersonaEmpresaRolRef=$em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($datos['id']);

                $totalPagos=0;   
                            
                            $nombre='';
                $estado='';
                //Obtiene el ultimo estado de la persona
    //			echo 'per:'.$datos->getId();die;	
                $ultimoEstado = $em->getRepository('schemaBundle:InfoPersonaEmpresaRolHisto')->findUltimoEstadoPorPersonaEmpresaRol($entityPersonaEmpresaRol->getId());        

                //print_r($ultimoEstado);die;	
                $idUltimoEstado=$ultimoEstado[0]['ultimo'];
                if($idUltimoEstado)
                {
                    $entityUltimoEstado=$em->getRepository('schemaBundle:InfoPersonaEmpresaRolHisto')->find($idUltimoEstado);
                    $estado=$entityUltimoEstado->getEstado();
                }
                else
                {
                    $estado=$datos->getEstado();
                }			
                
                if($datos['razonSocial']){
                    $nombre=$datos['razonSocial'];
                }else
                {
                    $nombre=$datos['nombres'].' '.$datos['apellidos'];
                }    
                
                if($entityPersonaEmpresaRolRef->getPersonaId()->getRazonSocial()){
                    $nombreReferido=$entityPersonaEmpresaRolRef->getPersonaId()->getRazonSocial();
                }else
                {
                    $nombreReferido=$entityPersonaEmpresaRolRef->getPersonaId()->getNombres().' '.$entityPersonaEmpresaRolRef->getPersonaId()->getApellidos();
                }            
                
                if($datos['feCreacion'])
                        $fechaCreacion=strval(date_format($entityPersonaEmpresaRolRef->getFeCreacion(), "d/m/Y G:i"));
                else
                        $fechaCreacion='';
                
                    $estadoFacturaNuevoCliente="";
                    $numeroFacturaNuevoCliente="";
                    $valorFacturaNuevoCliente="";
                    $valorDescuento=0;
                    $fechaEmisionFact="";            
                $entityFacturaNuevoCliente=$emfn->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                    ->findPrimeraFacturaValidaPorPersonaEmpresaRol($entityPersonaEmpresaRolRef->getId());
                if(count($entityFacturaNuevoCliente)>0){
                    //print_r($entityFacturaNuevoCliente); 
                    $idDocumentoFacturaNuevoCliente=$entityFacturaNuevoCliente[0]['id'];
                    $estadoFacturaNuevoCliente=$entityFacturaNuevoCliente[0]['estadoImpresionFact'];
                    $numeroFacturaNuevoCliente=$entityFacturaNuevoCliente[0]['numeroFacturaSri'];
                    $valorFacturaNuevoCliente=$entityFacturaNuevoCliente[0]['valorTotal'];
                    $valorDescuento=$valorFacturaNuevoCliente/2; //50% del valor facturado al nuevo cliente
                    $fechaEmisionFact=strval(date_format($entityFacturaNuevoCliente[0]['feEmision'], "d/m/Y G:i"));
                    $pagos=$emfn->getRepository('schemaBundle:InfoPagoDet')->findByReferenciaId($idDocumentoFacturaNuevoCliente);
                    foreach($pagos as $pago){
                    $totalPagos=$totalPagos+$pago->getValorPago();                        
                    }
                    
                }
                
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$i, $nombre);            
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$i,$nombreReferido );
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$i,$totalPagos );
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$i,$datos['direccion'] );
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$i,$fechaCreacion );
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$i,round($valorDescuento,2) );
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$i,$numeroFacturaNuevoCliente );
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$i,$fechaEmisionFact );
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.$i,$estadoFacturaNuevoCliente );
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.$i,$estado );
            }

            $i++;
        endforeach;
        
        $objPHPExcel->getActiveSheet()->setTitle('Referidos'); 
        // Establecer la hoja activa, para que cuando se abra el documento se muestre primero.
        $objPHPExcel->setActiveSheetIndex(0);		
        // Se modifican los encabezados del HTTP para indicar que se envia un archivo de Excel.
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="reporte_referidos.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
		exit;
        
    }
     
	
    public function clientesFacturaAction() {
        $em = $this->getDoctrine()->getManager('telconet');
        $request = $this->get('request');

        return $this->render('comercialBundle:cliente:clientesFactura.html.twig', array());
    }
    
    public function excelClientesFacturaAction() {
        
        $objPHPExcel = new PHPExcel();
        $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array( ' memoryCacheSize ' => '1024MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);		
        // Establecer propiedades
        $objPHPExcel->getProperties()
        ->setCreator("Telcos")
        ->setLastModifiedBy("Telcos")
        ->setTitle("Documento Excel de Clientes")
        ->setSubject("Documento Excel de Clientes")
        ->setDescription("")
        ->setKeywords("Excel Office 2007 openxml php")
        ->setCategory("Excel");
$styleArray = array(
    'font' => array(
        'bold' => true
    )
);

        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1', 'CLIENTE')
        ->setCellValue('B1', 'DIRECCION')
        ->setCellValue('C1', 'FECHA_CREACION CLIENTE')
        ->setCellValue('D1', 'LOGIN')
        ->setCellValue('E1', 'VENDEDOR')
        ->setCellValue('F1', 'SERVICIO')
        ->setCellValue('G1', 'FECHA ACTIVACION SERVICIO')                
        ->setCellValue('H1', 'ESTADO_CLIENTE')                
        ->setCellValue('I1', 'FACTURA')
        ->setCellValue('J1', 'FECHA_EMISION')
        ->setCellValue('K1', 'ESTADO_FACTURA')                
        ->setCellValue('L1', 'PAGOS')                
        ;        
$objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('D1')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('E1')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('F1')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('G1')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('H1')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('I1')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('J1')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('K1')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('L1')->applyFromArray($styleArray);

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);

        $request = $this->getRequest();
        $fechaDesde = '';
        $fechaHasta = '';
        $nombre = $request->get("nombre");
        $apellido = $request->get("apellido");
        $razonSocial = $request->get("razonSocial");		
        $limit = $request->get("limit");
        $page = $request->get("page");
        $start = $request->get("start");
        $idEmpresa = $request->getSession()->get('idEmpresa');

        
        
        $mes=$request->get('mes');
	$anio=$request->get('anio');
        if($mes && $anio){
            $diasmes=date("d",mktime(0,0,0,$mes+1,0,$anio));
            $fechaDesde=$anio."-".$mes."-01";
            $fechaHasta=$anio."-".$mes."-".$diasmes;
        }
        //echo $fechaDesde."<br>";
        //echo $fechaHasta."<br>";
        //die;
        $em = $this->get('doctrine')->getManager('telconet');
        $emfn = $this->get('doctrine')->getManager('telconet_financiero');

        $resultado = $em->getRepository('schemaBundle:InfoServicio')
        ->findServiciosConEstadoClienteActivoPendientePorCriterios($idEmpresa, '', $fechaDesde, $fechaHasta, 
        $nombre, $apellido, $razonSocial, $limit, $page, $start);

        $datos = $resultado['registros'];

        $i = 2;

        foreach ($datos as $datos)
        {
            $totalPagos=0;                 
            $nombre='';
            $estado='';
            //Obtiene el ultimo estado de la persona	
            $ultimoEstado = $em->getRepository('schemaBundle:InfoPersonaEmpresaRolHisto')->findUltimoEstadoPorPersonaEmpresaRol($datos['id']);        	
            $idUltimoEstado=$ultimoEstado[0]['ultimo'];
            if($idUltimoEstado)
            {
                $entityUltimoEstado=$em->getRepository('schemaBundle:InfoPersonaEmpresaRolHisto')->find($idUltimoEstado);
                $estado=$entityUltimoEstado->getEstado();
            }
            else
                $estado=$datos->getEstado();			

            $entityServicio=$em->getRepository('schemaBundle:InfoServicio')->find($datos['servicioId']);
    
            //OBTIENE EL NOMBRE SERVICIO
            if($entityServicio->getProductoId()){
                $nombreServicio=$entityServicio->getProductoId()->getDescripcionProducto();
            }    
            elseif($entityServicio->getPlanId()){
                $nombreServicio=$entityServicio->getPlanId()->getNombrePlan();
            }            
            //OBTIENE FECHA ACTIVACION DEL SERVICIO
            $entityServicioHistorial=$em->getRepository('schemaBundle:InfoServicioHistorial')->findFechaActivacionPorServicioId($datos['servicioId']);
            if($entityServicioHistorial){
                $fechaActivacion=strval(date_format($entityServicioHistorial[0]->getFeCreacion(), "d/m/Y G:i"));
            }else{
                $fechaActivacion='';
            }            
            if($datos['razonSocial'])
                $nombre=$datos['razonSocial'];
            else
                $nombre=$datos['nombres'].' '.$datos['apellidos'];           
            
            if($datos['feCreacion'])
                    $fechaCreacion=strval(date_format($datos['feCreacion'], "d/m/Y G:i"));
            else
                    $fechaCreacion='';
            
                $estadoFacturaNuevoCliente="";
                $numeroFacturaNuevoCliente="";
                $valorFacturaNuevoCliente="";
                $fechaEmisionFact="";            
            $entityFacturaNuevoCliente=$emfn->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                 ->findPrimeraFacturaValidaPorPunto($datos['idPunto']);
            if(count($entityFacturaNuevoCliente)>0){ 
                $idDocumentoFacturaNuevoCliente=$entityFacturaNuevoCliente[0]['id'];
                $estadoFacturaNuevoCliente=$entityFacturaNuevoCliente[0]['estadoImpresionFact'];
                $numeroFacturaNuevoCliente=$entityFacturaNuevoCliente[0]['numeroFacturaSri'];
                $valorFacturaNuevoCliente=$entityFacturaNuevoCliente[0]['valorTotal'];
                $fechaEmisionFact=strval(date_format($entityFacturaNuevoCliente[0]['feEmision'], "d/m/Y G:i"));
                
                $pagos=$emfn->getRepository('schemaBundle:InfoPagoDet')->findByReferenciaId($idDocumentoFacturaNuevoCliente);
                foreach($pagos as $pago){
                    if(($pago->getEstado()=='Cerrado') || ($pago->getEstado()=='Activo')){
                        $totalPagos=$totalPagos+$pago->getValorPago();                       
                    }
                }
                
            }
             
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$i, $nombre);    
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$i,$datos['direccion'] );
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$i,$fechaCreacion );
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$i,$datos['login'] );
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$i,$datos['usrVendedor'] );
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$i,$nombreServicio );
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$i,$fechaActivacion );
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$i,$estado );            
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.$i,$numeroFacturaNuevoCliente );
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.$i,$fechaEmisionFact );
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'.$i,$estadoFacturaNuevoCliente );
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L'.$i,$totalPagos );
            
            $i++;
        }
       // die;
        $objPHPExcel->getActiveSheet()->setTitle('Clientes con Factura'); 
        // Establecer la hoja activa, para que cuando se abra el documento se muestre primero.
        $objPHPExcel->setActiveSheetIndex(0);
            //echo "HOLA";die;    
        // Se modifican los encabezados del HTTP para indicar que se envia un archivo de Excel.
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="reporte_clientes_factura.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
       exit;
        
    }    

    /**
    * Documentación para el método 'tiposNegocioActivosPorEmpresa_ajaxAction'.
    *
    * Obtiene los tipos de negocios mediante el service InfoPuntoService 
    *
    *
    * @return json_encode $response retorna los tipos de negocio
    *
    * @author Alexander Samaniego <awsamaniego@telconet.ec>
    * @version 1.0 05-06-2014
    */
    
    /**
    * @Secure(roles="ROLE_151-1477")
    */ 
    public function tiposNegocioActivosPorEmpresa_ajaxAction(){

        $request            = $this->get('request');
        $IdEmpresa          = $request->getSession()->get('idEmpresa');
        $InfoPuntoService   = $this->get('comercial.InfoPunto');
        $tipoNegocio        = $InfoPuntoService->obtenerTiposNegocio($IdEmpresa, 'idTipoNegocio', 'descripcion');

        $response           = new Response(json_encode(array('tiposNegocio' => $tipoNegocio)));
        $response->headers->set('Content-type', 'text/json');

        return $response;

    }
    
    /**
    * Documentación para el método 'cambiaTipoNegocio_ajaxAction'.
    *
    * Actualiza el tipo de negocio de un punto.
    *
    *
    * @return json_encode $response retorna (succes true|false), (msg error|correcto)
    *
    * @author Alexander Samaniego <awsamaniego@telconet.ec>
    * @version 1.0 05-06-2014
    */
    
    /**
    * @Secure(roles="ROLE_151-1477")
    */
    public function cambiaTipoNegocio_ajaxAction(){

        $peticion           = $this->get('request');
        $IdTipoNegocio      = $peticion->get('idTipoNegocio');
        $IdPunto            = $peticion->get('idPunto');
        
        $em          = $this->get('doctrine')->getManager('telconet');
        $InfoPunto   = $em->getRepository('schemaBundle:InfoPunto')->find($IdPunto);
        
        $InfoPuntoService   = $this->get('comercial.InfoPunto');

        $infoPunto          = array('idPunto' => $IdPunto, 'idTipoNegocio' => $IdTipoNegocio);
        
        /*Verifica si el punto es diferente de los estados (Cancelado | Cancel | Trasladado | Reubicado)
         * para poder realizar la actualizacion de cambio de tipo de negocio
         */
        if($InfoPunto->getEstado() != 'Cancelado' && $InfoPunto->getEstado() != 'Cancel' && $InfoPunto->getEstado() != 'Trasladado' && $InfoPunto->getEstado() != 'Reubicado'){
            $result             = $InfoPuntoService->actualizaPtoCliente($infoPunto);
        }else{
            $result['succes'] = false;
            $result['msg']    = 'No se puede actualizar el tipo de negocio.';
        }

        $response           = new Response();
        $response->headers->set('Content-type', 'text/json');
        $response->setContent(json_encode(array('succes' => $result['succes'], 'msg' => $result['msg'])));

        return $response;
    }

    /**
    * Documentación para el método 'ajaxListarPersonasPorRolesAction'.
    *
    * consulta las personas que tengan rol cliente y precliente
    * @param mixed $roles Roles de la persona separado por "|" ej: "Cliente|Pre-cliente"
    * @return json.
    *
    * @author Andres Montero <amontero@telconet.ec>
    * @version 1.0 04-06-2014
    * @author Edgar Holguin <eholguin@telconet.ec>
    * @version 1.1 02-12-2014    
    */
    public function ajaxListarPersonasPorRolesAction($roles){
        $request   = $this->getRequest();
        $session   = $request->getSession();
        $idEmpresa = $session->get('idEmpresa');
        $idpunto   = $request->get("idpunto");
        $em        = $this->get('doctrine')->getManager('telconet');
        $filter    = $request->get("query");
        
        if($roles)
            $arrayRolesPersona=  explode ("|", $roles);
        else    
            $arrayRolesPersona=array('Cliente');
            
        $arrayEstados=array('Eliminado','Inactivo','Pendiente','Anulado'); 

        if($idpunto!=null)
        {
            $datos = $em->getRepository('schemaBundle:InfoPersona')
                        ->findListadoClientesPorEmpresaPunto($arrayEstados,$idEmpresa,$filter,$arrayRolesPersona,$idpunto);        
        }
        else
        {
            $datos = $em->getRepository('schemaBundle:InfoPersona')
                        ->findListadoClientesPorEmpresaPorEstado($arrayEstados,$idEmpresa,$filter,$arrayRolesPersona);          
        }
        
        $i=1;
        foreach ($datos as $persona):            
            if($persona->getNombres()!="" && $persona->getApellidos()!="")
                $informacion_cliente=$persona->getNombres()." ".$persona->getApellidos();
            if($persona->getRazonSocial()!="")
                $informacion_cliente=$persona->getRazonSocial();		
            $arreglo[]= array(
	      'idcliente'=>$persona->getId(),
	      'descripcion'=>$informacion_cliente,
            );              
            $i++;     
        endforeach;
        
        if (!empty($arreglo))
            $response = new Response(json_encode(array('clientes' => $arreglo)));
        else
        {
            $arreglo[]= array(
	      'idcliente'=> "",
	      'descripcion'=> "",
            );
            
            $response = new Response(json_encode(array('clientes' => $arreglo)));
        }		
        
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }    
    /**
    * Documentación para el método 'tipoNegocioActual_ajaxAction'.
    *
    * Muestra el tipo de Negocio Actual
    *
    *
    * @return json_encode $response retorna tipoNegocioActual-> Nombre del Tipo de Negocio 
    *
    * @author Alexander Samaniego <awsamaniego@telconet.ec>
    * @version 1.0 19-06-2014
    */
    
    /**
    * @Secure(roles="ROLE_151-1477")
    */
    public function tipoNegocioActual_ajaxAction(){

        $peticion    = $this->get('request');
        $IdPunto     = $peticion->get('idPunto');
        $em          = $this->get('doctrine')->getManager('telconet');
        $InfoPunto   = $em->getRepository('schemaBundle:InfoPunto')->find($IdPunto);

        $response    = new Response();
        $response->headers->set('Content-type', 'text/json');
        $response->setContent(json_encode(array('tipoNegocioActual' => $InfoPunto->getTipoNegocioId()->getNombreTipoNegocio())));

        return $response;
    }
    
    /**
     * @Secure(roles="ROLE_311-3077")
     * Documentación para el método 'cambioRazonSocialPorPuntoAction'.
     *
     * Muestra informacion para realizar el cambio de razon social por Punto(Login) de un Cliente hacia otro Cliente Activo antiguo
     * o hacia un nuevo Cliente
     * 
     * Consideraciones: 
     * 1) No se permite realizar "Cambio de Razon Social por Punto" de Logines si el cliente posee deuda pendiente.
     * 2) Los Logines origen del "Cambio de Razon Social por Punto" quedaran en estado Cancelado asi como toda la data relacionada a este.
     * 3) No se permite realizar "Cambio de Razon Social por Punto" a todos los Logines de un Cliente esto debera realizarse desde la opcion de 
     *    "Cambio de Razon Social" tradicional    
     * 4) No se permite Cambio de Razon Social Por Login, si el Login es Punto Padre de Facturacion de otros Logines.
     * 5) No se permite Cambio de Razon Social Por Login si el Login no posee servicio en estado Activo
     * 6) Se Cargara el Grid solo con los Logines que cumplen las condiciones. 
     *     
     * @param integer $id    
     * @param integer $idper    
     *
     * @return Renders a view.
     *  
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 07-08-2015
     *
     * Se actualiza que no valide si cliente tiene deuda en caso de tratarse de  CLIENTE CANAL
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.1 13-01-2016
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.2 04-04-2016
     * Verificación del Cliente VIP para agregarlo a la sesión.
     * 
     * Se agregan campos Nuevos CarnetConadis, EsPrepago, PagaIva, ContribuyenteEspecial y Combo de Oficinas de Facturacion
     * para el caso de ser empresa Telconet se deben pedir dichos campos, en el caso de empresa Megadatos se deben setear los
     * valores por dafault
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.3 06-06-2016  
     * 
     * @author : Andrés Montero <amontero@telconet.ec>
     * @version 1.4 07-07-2017
     * Se envia id de pais de la empresa en sesión por parametros al crear formulario de formas de pago con InfoContratoFormaPagoType 
     * 
     * @author Gustavo Narea <gnarea@telconet.ec>
     * @version 1.5 18-09-2020
     * Se parametriza limite a la fecha de vencimiento de la tarjeta 
     * 
     * @author : Néstor Naula <nnaulal@telconet.ec>
     * @version 1.6 18-10-2020
     * Se agrega los parámetros obligatorios de imagenes a ingresar para MD
     * 
     * @author Alex Gomez <algomez@telconet.ec>
     * @version 1.7 10-08-2022 
     * Se agrega parametro para almacenar array de puntos por proceso de CRS 
     * @author Jefferson Alexy Carrillo <jacarrillo@telconet.ec>
     * @version 1.0 10-08-2022 - Se elimina consumo de representante legal por nueva implemnetacion con ms
     *
     */
    public function cambioRazonSocialPorPuntoAction($id, $idper)
    {
        $session        = $this->get('request')->getSession();
        $intIdPais      = $session->get('intIdPais');
        $emSeguridad    = $this->getDoctrine()->getManager("telconet_seguridad");
        $entityItemMenu = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("8", "1");
        $session->set('menu_modulo_activo'       , $entityItemMenu->getNombreItemMenu());
        $session->set('nombre_menu_modulo_activo', $entityItemMenu->getTitleHtml());
        $session->set('id_menu_modulo_activo'    , $entityItemMenu->getId());
        $session->set('imagen_menu_modulo_activo', $entityItemMenu->getUrlImagen());
        $boolExistePuntoActivo = 0;
        $boolExisteECDF = false;
  
        $em              = $this->getDoctrine()->getManager();
        $emfn            = $this->getDoctrine()->getManager('telconet_financiero');   
        $emGeneral       = $this->getDoctrine()->getManager('telconet_general');		
        $strCodEmpresa   = $session->get('idEmpresa');
        
        $strPrefijoEmpresa        = $session->get('prefijoEmpresa');
        $arrayListaDocumentoSubir =  array();
        $arrayPuntosCRS  = array();
        
        $objInfoPersona  = $em->getRepository('schemaBundle:InfoPersona')->find($id);
        $strEstadoActivo = 'Activo';
        if(!$objInfoPersona)
        {
            throw $this->createNotFoundException('Unable to find InfoPersona entity.');
        }
        $objInfoContrato        = null;
        $objInfoContrato        = $em->getRepository('schemaBundle:InfoContrato')->
                                  findContratosPorEmpresaPorEstadoPorPersona($strEstadoActivo, $strCodEmpresa, $id);
        $deleteForm             = $this->createDeleteForm($id);
        $objInfoPersonaReferido = $em->getRepository('schemaBundle:InfoPersonaReferido')->findPorPersona($objInfoPersona->getId());
        $objReferido = null;
        if($objInfoPersonaReferido)
        {
            $objReferido = $objInfoPersonaReferido->getReferidoId();
            $intIdPerRef = $objInfoPersonaReferido->getRefPersonaEmpresaRolId();
        }
        $objCliente = $em->getRepository('schemaBundle:InfoPersona')->getPersonaParaSession($strCodEmpresa, $id);        
        $session->set('cliente', $objCliente);
        $session->set('ptoCliente', '');
        $session->set('clienteContacto', '');
        $session->set('puntoContactos', '');
        
        $strEsVip = '';
            
        if($strPrefijoEmpresa == 'TN')
        {
            // Buscamos en InfoContratoDatoAdicional para verificar que sea cliente VIP
            $arrayParams        = array('ID_PER'  => $idper,
                                        'EMPRESA' => $strCodEmpresa,
                                        'ESTADO'  => 'Activo');
            $entityContratoDato = $em->getRepository('schemaBundle:InfoContratoDatoAdicional')->getResultadoDatoAdicionalContrato($arrayParams);
            $strEsVip           = $entityContratoDato && $entityContratoDato->getEsVip() ? 'Sí' : 'No';
        }
        
        $session->set('esVIP', $strEsVip);

        // Obtiene el ultimo estado del cliente o pre-cliente
        $arrayDatosHistorial = $this->obtieneUltimoEstadoCliente($id, 'Cliente', $strCodEmpresa);
        $objHistorial        = $arrayDatosHistorial['historial'];
        $strEstado           = $arrayDatosHistorial['estado'];

        // Obtiene formas de contacto
        $objFormasContacto   = null;
        $arrayFormasContacto = $em->getRepository('schemaBundle:InfoPersonaFormaContacto')
                               ->findPorEstadoPorPersona($id, $strEstadoActivo, 9999999, 1, 0);
        if($arrayFormasContacto['registros'])
        {
            $objFormasContacto = $arrayFormasContacto['registros'];
        }
       
        $objInfoPersonaCambioRazonSocial = new InfoPersona();
        $form                            = $this->createForm(new ClienteType(array( 'empresaId'          => $strCodEmpresa,
                                                                                    'oficinaFacturacion' => null,
                                                                                    'tieneNumeroConadis' => 'N',
                                                                                    'esPrepago'          => 'S',
                                                                                    'pagaIva'            => 'S')
                                                                             ), $objInfoPersonaCambioRazonSocial);        
        $objPersonaEmpresaRol            = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($idper);
        $objAdmiRol                      = $em->getRepository('schemaBundle:AdmiRol')->find($objPersonaEmpresaRol->getEmpresaRolId()->getRolId());        
        // Verifica deuda del cliente
        $objInfoPunto                    = $em->getRepository('schemaBundle:InfoPunto')->findByPersonaEmpresaRolId($idper);        
        $objInfoPuntoActivo              = $em->getRepository('schemaBundle:InfoPunto')->getPuntosCambioRazonSocialPorLogin($idper,'','','','','');
        if($objInfoPuntoActivo)
        {
            $boolExistePuntoActivo = 1;   
            $arrayPuntos = $objInfoPuntoActivo['registros'];
            foreach($arrayPuntos as $pto)
            {
                $arrayServiciosActivos = $em->getRepository('schemaBundle:InfoServicio')
                ->findServiciosPorEmpresaPorPunto($strCodEmpresa, $pto["id"], 99999999, 1, 0);
                $arrayServicios = $arrayServiciosActivos['registros'];
                foreach($arrayServicios as $serv)
                {
                    if($strPrefijoEmpresa == 'MD' && is_object($serv->getProductoId()) 
                      && $serv->getProductoId()->getNombreTecnico() === "ECDF")
                    {
                        // validación del producto ECDF
                          $boolExisteECDF = true;
                          break;
                    }
                }
            }
        }
        //obtengo deuda del cliente para validar
        $fltValor = 0;
        // Saco saldo deudor del Cliente , No se considera validacion si el CLIENTE es Canal
        if($objAdmiRol->getDescripcionRol() != 'Cliente Canal')
        {
            foreach($objInfoPunto as $pto)
            {
                $arraySaldoarr = $emfn->getRepository('schemaBundle:InfoPagoCab')->obtieneSaldoPorPunto($pto->getId());
                $fltValor      = $fltValor + $arraySaldoarr[0]['saldo'];
            }
        }
        //Informacion referente al contrato
        $arrayTipoDocumentos = array();
        $objTiposDocumentos  = $emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')->findByEstado("Activo");                   
        foreach ( $objTiposDocumentos as $objTiposDocumentos )
        {   
           $arrayTipoDocumentos[$objTiposDocumentos->getId()] = $objTiposDocumentos->getDescripcionTipoDocumento();
        }

        if($strPrefijoEmpresa == 'MD' || $strPrefijoEmpresa == 'EN')
        {
            $objTiposDocumentosSubir  = $emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
                                         ->findBy(array("estado"              => "Activo",
                                                        "codigoTipoDocumento" => array("CED","CEDR","FOT")));                   
            foreach ( $objTiposDocumentosSubir as $objTiposDocumentos )
            {   
                $arrayListaDocumentoSubir[$objTiposDocumentos->getId()] = $objTiposDocumentos->getDescripcionTipoDocumento();
            }
        }
        
        $objContratoCambioRazonSocial = new InfoContrato();
        $formInfoContrato             = $this->createForm(new InfoContratoType(array('validaFile'=>true)), $objContratoCambioRazonSocial);
        $formInfoContrato             = $formInfoContrato->createView();
        
        $objContratoFormaPago         = new InfoContratoFormaPago();
        
        $arrayAdmiParametroCabAnio  = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
        ->findOneBy(array("nombreParametro" => "ANIO_VIGENCIA_TARJETA",
                          "estado"          => "Activo"));
        if(is_object($arrayAdmiParametroCabAnio))
        {
            $arrayParamDetAnios = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->findBy(array("parametroId" => $arrayAdmiParametroCabAnio,
                                                        "estado"      => "Activo"));
            if ($arrayParamDetAnios)
            {
                $intAnioVencimiento = $arrayParamDetAnios[0]->getValor1();
            }
        }
        $objFormInfoFormaPago            = $this->createForm(new InfoContratoFormaPagoType(
                                                                array("intIdPais"=>$intIdPais,
                                                                "intAnioVencimiento"=>$intAnioVencimiento)), 
                                                                $objContratoFormaPago);
        $objFormInfoFormaPago            = $objFormInfoFormaPago->createView();
                
        $objFormDocumentos            = $this->createForm(new InfoDocumentoType(array('validaFile'=>true,
                                        'arrayTipoDocumentos'=>$arrayTipoDocumentos)), new InfoDocumento());
        $objFormDocumentos            = $objFormDocumentos->createView();
        
        $entityContratoDatoAdicional    = new InfoContratoDatoAdicional();
        $objFormDatoAdicionales         = $this->createForm(new InfoContratoDatoAdicionalType(), $entityContratoDatoAdicional);     

        /* @var $serviceInfoContrato \telconet\comercialBundle\Service\InfoContratoService */
        $serviceInfoContrato          = $this->get('comercial.InfoContrato');
        $entityAdmiTipoContrato       = $serviceInfoContrato->obtenerTiposContrato($strCodEmpresa);
        // Generacion de la fecha
        $strFecha_act                 = date("Y-m-d");
        $strFecha                     = date('Y-m-d', strtotime("+12 months $strFecha_act"));

        $strEsCoordinadorMD = '1';

        return $this->render('comercialBundle:cliente:cambioRazonSocialPorPunto.html.twig', array(
                'item'                    => $entityItemMenu,
                'entity'                  => $objInfoPersona,
                'esCoordinadorMD'          => $strEsCoordinadorMD,
                'delete_form'             => $deleteForm->createView(),
                'form'                    => $form->createView(),
                'referido'                => $objReferido,                
                'contrato'                => $objInfoContrato,
                'estado'                  => $strEstado,
                'formasContacto'          => $objFormasContacto,
                'deuda'                   => round($fltValor, 2),
                'idper'                   => $idper,
                'rol'                     => $objAdmiRol->getTipoRolId()->getDescripcionTipoRol(),
                'idperRef'                => $intIdPerRef,
                'boolExistePuntoActivo'   => $boolExistePuntoActivo,
                'form_documentos'         => $objFormDocumentos,
                'arrayTipoDocumentos'     => $arrayTipoDocumentos,
                'formInfoContrato'        => $formInfoContrato,
                'formInfoFormaPago'       => $objFormInfoFormaPago,
                'entityAdmiTipoContrato'  => $entityAdmiTipoContrato,
                'strFecha'                => $strFecha,
                'prefijoEmpresa'          => $strPrefijoEmpresa,
                'arrayListaDocumentoSubir'=> $arrayListaDocumentoSubir,
                'formDatoAdicionales'     => $objFormDatoAdicionales->createView(),
                'boolExisteECDF'          => $boolExisteECDF,
                'arrayPuntosCRS'          => $arrayPuntosCRS
        ));
    }
    
    /**
     * @Secure(roles="ROLE_151-3738")
     * 
     * Documentación para el método 'showAsignarIngenieroVIPAction'.
     *
     * Renderiza la pantalla para la asignación de ingenieros vip al cliente vip
     *
     * @param int $intIdPersona           IdPersona del Cliente
     * @param int $intIdPersonaEmpresaRol IdPersonaEmpresaRol del Cliente
     *
     * @return Render Pantalla Asignación Ingeniero VIP.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 04-04-2016
     */
    public function showAsignarIngenieroVIPAction($intIdPersona, $intIdPersonaEmpresaRol)
    {
        $objSesion         = $this->get('request')->getSession();
        $em                = $this->getDoctrine()->getManager();
        $strCodEmpresa     = $objSesion->get('idEmpresa');
        $entityInfoPersona = $em->getRepository('schemaBundle:InfoPersona')->find($intIdPersona);
        
        if(!$entityInfoPersona)
        {
            throw $this->createNotFoundException('Unable to find InfoPersona entity.');
        }
        
        $entityPersona = $em->getRepository('schemaBundle:InfoPersona')->getPersonaParaSession($strCodEmpresa, $intIdPersona);
        
        $objSesion->set('cliente', $entityPersona);
        $objSesion->set('esVIP', $strEsVip);

        $rolesPermitidos = array();
        
        if($this->get('security.context')->isGranted('ROLE_151-3697'))
        {
            $rolesPermitidos[] = 'ROLE_151-3697'; // ASIGNAR INGENIERO VIP AL CLIENTE
        }
        if($this->get('security.context')->isGranted('ROLE_151-3737'))
        {
            $rolesPermitidos[] = 'ROLE_151-3737'; // ELIMINAR INGENIERO VIP AL CLIENTE
        }
        if($this->get('security.context')->isGranted('ROLE_151-7197'))
        {
            $rolesPermitidos[] = 'ROLE_151-7197'; // EDITAR INGENIERO VIP AL CLIENTE
        }

        return $this->render('comercialBundle:cliente:asignarIngenieroVIP.html.twig', array('entity'          => $entityInfoPersona,
                                                                                            'idper'           => $intIdPersonaEmpresaRol,
                                                                                            'rolesPermitidos' => $rolesPermitidos));
    }

    /**
     * @Secure(roles="ROLE_151-3738")
     * 
     * Documentación para el método 'gridIngenierosVIPClienteAction'.
     *
     * Retorna listado de Todos ingenieros VIP asignados al cliente.
     *
     * @param int $idPer IdPersonaEmpresaRol del Cliente.
     *
     * @return Response Lista de Ingenieros VIP asociados al Cliente.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 04-04-2016
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.1 24-01-2020 - Se agrega en el envío del arreglo $arrayParametros los parámetros de los dos id
     *                           de la características de la ciudad y de la extensión de teléfono si su características existen
     */
    public function gridIngenierosVIPClienteAction($idPer)
    {
        $objRequest = $this->getRequest();
        $objSesion  = $objRequest->getSession();
        $em         = $this->getDoctrine()->getManager();
        
        $objAdmiCaractCiudad    = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                            ->findOneByDescripcionCaracteristica('ID_VIP_CIUDAD');
        if(is_object($objAdmiCaractCiudad))
        {
            $arrayParametros['strCaractCiudad'] = $objAdmiCaractCiudad->getID();
        }
        $objAdmiCaractExtension = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                            ->findOneByDescripcionCaracteristica('EXTENSION USUARIO');
        if(is_object($objAdmiCaractExtension))
        {
            $arrayParametros['strCaractExt']    = $objAdmiCaractExtension->getID();
        }

        $arrayParametros['ID_PER']         = $idPer;
        $arrayParametros['CARACTERISTICA'] = 'ID_VIP';
        $arrayParametros['ESTADO']         = 'Activo';
        $arrayParametros['EMPRESA']        = $objSesion->get('idEmpresa');
        $arrayParametros['LIMIT']          = $objRequest->get('limit');
        $arrayParametros['START']          = $objRequest->get('start');
        
        $strJsonIngenierosVIPCliente = $this->get('doctrine')->getManager('telconet')->getRepository('schemaBundle:InfoPersona')
                                                                                     ->getJsonIngenierosVIPCliente($arrayParametros);
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-type', 'text/json');
        $objRespuesta->setContent($strJsonIngenierosVIPCliente);
        
        return $objRespuesta;
    }
    
    /**
     * @Secure(roles="ROLE_151-3738")
     *  
     * Documentación para el método 'gridAjaxHistorialVIPAction'.
     *
     * Retorna listado del historial de asignación de los ingenieros VIP al cliente.
     *
     * @param int $idPer IdPersonaEmpresaRol del Cliente
     *
     * @return Response listado del historial de asignación.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 04-04-2016
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.1 24-01-2020 - Se agregan en la búsqueda del historial de los ingenieros VIP
     *                           las características de la ciudad y la extensión
     */
    public function gridAjaxHistorialVIPAction($idPer)
    {
        $arrayParametros['ID_PER']              = $idPer;
        $arrayParametros['CARACTERISTICA']      = 'ID_VIP';
        $arrayParametros['ESTADO']              = 'Eliminado';
        $arrayParametros['strCaractCiudad']     = 'ID_VIP_CIUDAD';
        $arrayParametros['strCaractExtension']  = 'EXTENSION USUARIO';
        $arrayParametros['EMPRESA']        = $this->getRequest()->getSession()->get('idEmpresa');
        
        $strJsonIngenierosVIPCliente = $this->get('doctrine')->getManager('telconet')->getRepository('schemaBundle:InfoPersona')
                                                                                     ->getJsonHistorialClienteVIP($arrayParametros);
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-type', 'text/json');
        $objRespuesta->setContent($strJsonIngenierosVIPCliente);
        
        return $objRespuesta;
    }
    
    /**
     * @Secure(roles="ROLE_151-3738")
     * 
     * Documentación para el método 'gridAjaxHistorialClienteAction'.
     *
     * Retorna listado del historial del cliente o precliente.
     *
     * @param int $idPer IdPersonaEmpresaRol del Cliente o Precliente
     *
     * @return Response listado del historial de asignación.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 04-04-2016
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 13-09-2016
     * Se quita el filtro de ESTADO y MOTIVO para la consulta del historial.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 16-09-2016 - Se modifica la función para que me retorne todo el historial del cliente o precliente. Adicional se quitan los roles
     *                           asignados puesto que el historial debe estar visible para todos los usuarios de TN y MD.
     */
    public function gridAjaxHistorialClienteAction($idPer)
    {
        $objRequest                 = $this->getRequest();
        $objSession                 = $objRequest->getSession();
        $intIdEmpresa               = $objSession->get('idEmpresa');
        $emComercial                = $this->get('doctrine')->getManager('telconet');
        $arrayParametros['ID_PER']  = $idPer;
        $arrayParametros['EMPRESA'] = $intIdEmpresa;
        
        $strJsonIngenierosCliente = $emComercial->getRepository('schemaBundle:InfoPersona')->getJsonHistorialCliente($arrayParametros);

        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-type', 'text/json');
        $objRespuesta->setContent($strJsonIngenierosCliente);
        
        return $objRespuesta;
    }
    
    /**
     * @Secure(roles="ROLE_151-3738")
     * 
     * Documentación para el método 'getAjaxComboIngenierosVIPAction'.
     *
     * Obtiene el listado de ingenieros vip asignables al cliente vip
     *
     * @param int $idPer IdPersonaEmpresaRol del Cliente
     *
     * @return Response Lista de ingenieros vip
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 04-04-2016
     */
    public function getAjaxComboIngenierosVIPAction($idPer)
    {
        $objRequest = $this->getRequest();
        $objSesion  = $objRequest->getSession();
        
        $arrayParametros['EMPRESA']         = $objSesion->get('idEmpresa');
        $arrayParametros['CARACTERISTICA']  = 'ING_VIP';
        $arrayParametros['ID_PER']          = intval($idPer);
        $arrayParametros['CARACTERISTICA2'] = 'ID_VIP';
        $arrayParametros['ES_ING_VIP']      = 'SI';
        $arrayParametros['ESTADO']          = 'Activo';
        $arrayParametros['INGENIERO']       = trim($objRequest->get('query'));
        
        $strJsonPuntosCliente = $this->get('doctrine')->getManager('telconet')->getRepository('schemaBundle:InfoPersona')
                                                                              ->getJsonIngenierosVIP($arrayParametros);
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-type', 'text/json');
        $objRespuesta->setContent($strJsonPuntosCliente);
        
        return $objRespuesta;
    }
    
    /**
     * @Secure(roles="ROLE_151-3717")
     * 
     * Documentación para el método 'getAjaxDefinirClienteVIPNormalAction'.
     *
     * Retorna listado de Todos ingenieros VIP asignados al cliente
     *
     * @return Response Lista de Puntos del cliente
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 04-04-2016
     */
    public function getAjaxDefinirClienteVIPNormalAction()
    {
        $objRequest    = $this->getRequest();
        $objSesion     = $objRequest->getSession();
        $intIdPer      = $objRequest->get('idPer');
        $usrCreacion   = $objSesion->get('user');
        $strCodEmpresa = $objSesion->get('idEmpresa');
        $em            = $this->getDoctrine()->getManager();
        try
        {
            $em->getConnection()->beginTransaction();
            // Buscamos en InfoContratoDatoAdicional para verificar que sea cliente VIP
            $arrayParametros    = array('ID_PER'  => $intIdPer,
                                        'EMPRESA' => $strCodEmpresa,
                                        'ESTADO'  => 'Activo');
            $entityContratoDato = $em->getRepository('schemaBundle:InfoContratoDatoAdicional')->getResultadoDatoAdicionalContrato($arrayParametros);

            if(!$entityContratoDato)
            {
                throw new \Exception("Entity InfoContratoDatoAdicional not found");
            }
            
            // Proceso de eliminación de ingenieros VIP asociados.
            if($entityContratoDato->getEsVip())
            {
                // Obtenemos la Característica
                $entityCaracteristica = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                           ->getCaracteristicaPorDescripcionPorEstado('ID_VIP', 'Activo');
                if(!$entityCaracteristica)
                {
                    throw new \Exception("Entity AdmiCaracteristica 'ID_VIP' not found");
                }

                $entityPersonaEmpRol = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intIdPer);

                if(!$entityPersonaEmpRol)
                {
                    throw new \Exception("Entity InfoPersonaEmpresaRol not found");
                }
                
                $arrayParametros    = array('personaEmpresaRolId' => $entityPersonaEmpRol,
                                            'caracteristicaId'    => $entityCaracteristica,
                                            'estado'              => 'Activo');

                $listaIngenierosVIP = $em->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')->findBy($arrayParametros);

                // Recorremos los ingenieros VIP asociados y se les da de baja
                foreach($listaIngenierosVIP as $entityIngenieroVIP)
                {
                    $entityIngenieroVIP->setEstado('Eliminado');
                    $entityIngenieroVIP->setFeUltMod(new \DateTime('now'));
                    $entityIngenieroVIP->setUsrUltMod($usrCreacion);

                    $em->persist($entityIngenieroVIP);
                }
                $entityContratoDato->setEsVip('N');
            }
            else
            {
                $entityContratoDato->setEsVip('S');
            }

            $em->persist($entityContratoDato);
            $em->flush();

            $em->getConnection()->commit();
            $strResponse = 'OK';
        }
        catch(\Exception $e)
        {
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            
            $strResponse = "No se concluyó la transacción con éxito. <br>Comuníquese con Sistemas para solucionar.<br>Error: " . $e->getMessage();
        }
        return new Response($strResponse);
    }

    /**
     * @Secure(roles="ROLE_151-3697")
     * 
     * Documentación para el método 'ajaxAsignarIngenieroVIPAction'.
     *
     * Asigna un nuevo Ingeniero VIP al cliente 
     *
     * @param int $idPer IdPersonaEmpresaRol del Cliente
     *
     * @return Response resultado de la operación.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 04-04-2016
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.1 24-01-2020 - Se agrega en la asignación del Ingeniero VIP la ciudad y la extensión de teléfono
     */
    public function ajaxAsignarIngenieroVIPAction($idPer)
    {
        $serviceUtil    = $this->get('schema.Util');
        $objRequest     = $this->getRequest();
        $objSesion      = $objRequest->getSession();
        $strIdPerIng    = $objRequest->get('ingeniero');
        $strCodEmpresa  = $objSesion->get('idEmpresa');
        $strExtPerIng   = $objRequest->get('extension');
        $strIdCiudadPer = $objRequest->get('ciudad');
        $strUsrCreacion = $objSesion->get('user');
        $emComercial    = $this->getDoctrine()->getManager();
        $emGeneral      = $this->getDoctrine()->getManager("telconet_general");
        
        try
        {
            $emComercial->getConnection()->beginTransaction();

            $arrayResponse = $this->procesarDatosIngenieroVIP($idPer, $strCodEmpresa, $strIdPerIng);
            $objInfoPersonaEmpRol   = $arrayResponse['PERSONA_EMP_ROL'];
            
            if(!isset($strIdCiudadPer) || empty($strIdCiudadPer))
            {
                throw new \Exception("No ha seleccionado la ciudad del Ingeniero VIP.");
            }
            //verifico si el id de la ciudad existe
            $objAdmiCanton          = $emGeneral->getRepository('schemaBundle:AdmiCanton')
                                                    ->findOneBy(array('id'      => $strIdCiudadPer,
                                                                      'estado'  => 'Activo'));
            if(!is_object($objAdmiCanton))
            {
                throw new \Exception("No se encontró la ciudad, por favor notificar a Sistemas.");
            }

            $objAdmiCaractCiudad    = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneByDescripcionCaracteristica('ID_VIP_CIUDAD');
            //verifico si la característica no existe retorno el error
            if( !is_object($objAdmiCaractCiudad) )
            {
                throw new \Exception("No se encontró la característica para asignar la ciudad del Ingeniero VIP, ".
                                     "por favor notificar a Sistemas.");
            }

            //obtengo todas las característica de este ingeniero vip
            $arrayPerEmpRolCarac    = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                 ->findBy(array('personaEmpresaRolId' => $objInfoPersonaEmpRol->getId(),
                                                                'caracteristicaId'    => $arrayResponse['CARACTERISTICA']->getId(),
                                                                'valor'               => $strIdPerIng,
                                                                'estado'              => 'Activo'));
            foreach( $arrayPerEmpRolCarac as $objInfoPerEmpRolCarac )
            {
                //busco si ya se encuentra asignada la ciudad para este ingeniero vip
                $objPerEmpRolCiudad = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                ->findOneBy(array('personaEmpresaRolCaracId' => $objInfoPerEmpRolCarac->getId(),
                                                                  'caracteristicaId'         => $objAdmiCaractCiudad->getId(),
                                                                  'valor'                    => $strIdCiudadPer,
                                                                  'estado'                   => 'Activo'));
                if( is_object($objPerEmpRolCiudad) )
                {
                    throw new \Exception("El Ingeniero VIP ya se encuentra asignado a la ciudad.");
                }
            }

            // Se crea la asignación del Ineniero VIP
            $entityPersonaEmpRolCarac = new InfoPersonaEmpresaRolCarac();
            $entityPersonaEmpRolCarac->setPersonaEmpresaRolId($arrayResponse['PERSONA_EMP_ROL']);
            $entityPersonaEmpRolCarac->setCaracteristicaId($arrayResponse['CARACTERISTICA']);
            $entityPersonaEmpRolCarac->setValor($strIdPerIng);
            $entityPersonaEmpRolCarac->setFeCreacion(new \DateTime('now'));
            $entityPersonaEmpRolCarac->setUsrCreacion($strUsrCreacion);
            $entityPersonaEmpRolCarac->setIpCreacion($objRequest->getClientIp());
            $entityPersonaEmpRolCarac->setEstado('Activo');
            $emComercial->persist($entityPersonaEmpRolCarac);
            $emComercial->flush();
            
             //Se asigna la ciudad de la característica del Ingeniero VIP
            $objPersonaEmpRolCaracCiudad = new InfoPersonaEmpresaRolCarac();
            $objPersonaEmpRolCaracCiudad->setPersonaEmpresaRolId($objInfoPersonaEmpRol);
            $objPersonaEmpRolCaracCiudad->setCaracteristicaId($objAdmiCaractCiudad);
            $objPersonaEmpRolCaracCiudad->setPersonaEmpresaRolCaracId($entityPersonaEmpRolCarac->getId());
            $objPersonaEmpRolCaracCiudad->setValor($strIdCiudadPer);
            $objPersonaEmpRolCaracCiudad->setFeCreacion(new \DateTime('now'));
            $objPersonaEmpRolCaracCiudad->setUsrCreacion($strUsrCreacion);
            $objPersonaEmpRolCaracCiudad->setIpCreacion($objRequest->getClientIp());
            $objPersonaEmpRolCaracCiudad->setEstado('Activo');
            $emComercial->persist($objPersonaEmpRolCaracCiudad);
            $emComercial->flush();

            if(isset($strExtPerIng) && !empty($strExtPerIng))
            {
                //verifico si la extension esta compuesta por números
                if( !preg_match('/^[0-9]+$/', $strExtPerIng) )
                {
                    throw new \Exception("La extensión de teléfono del Ingeniero VIP no es válida.");
                }

                //verifico si existe el parametro para validar la cantidad de números de la extensión
                $objAdmiParametroCab    = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                        ->findOneBy(array('nombreParametro' => 'CANTIDAD_NUMERO_EXTENSION',
                                                                          'estado'          => 'Activo'));
                if(!is_object($objAdmiParametroCab))
                {
                    throw new \Exception("No se encontró el parámetro para validar la cantidad de número de la extensión de teléfono, ".
                                         "por favor notificar a Sistemas.");
                }
                $objAdmiParametroDet    = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->findOneBy(array('parametroId' => $objAdmiParametroCab->getId(),
                                                                      'estado'      => 'Activo'));
                if(!is_object($objAdmiParametroDet))
                {
                    throw new \Exception("No se encontró el parámetro para validar la cantidad de número de la extensión de teléfono, ".
                                         "por favor notificar a Sistemas.");
                }
                $intCantidadNumeros     = $objAdmiParametroDet->getValor1();
                //verifico si la extension es igual a la cantidad de números validos
                if( strlen($strExtPerIng) != $intCantidadNumeros )
                {
                    throw new \Exception("La extensión debe tener $intCantidadNumeros números.");
                }

                $objAdmiCaractExtension     = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                    ->findOneByDescripcionCaracteristica('EXTENSION USUARIO');
                //verifico si la característica no existe retorno el error
                if( !is_object($objAdmiCaractExtension) )
                {
                    throw new \Exception("No se encontró la característica para la extensión del Ingeniero VIP, ".
                                         "por favor notificar a Sistemas.");
                }

                //Se asigna la extensión de teléfono del Ingeniero VIP
                $objPersonaEmpRolCaracExtension = new InfoPersonaEmpresaRolCarac();
                $objPersonaEmpRolCaracExtension->setPersonaEmpresaRolId($objInfoPersonaEmpRol);
                $objPersonaEmpRolCaracExtension->setCaracteristicaId($objAdmiCaractExtension);
                $objPersonaEmpRolCaracExtension->setPersonaEmpresaRolCaracId($entityPersonaEmpRolCarac->getId());
                $objPersonaEmpRolCaracExtension->setValor($strExtPerIng);
                $objPersonaEmpRolCaracExtension->setFeCreacion(new \DateTime('now'));
                $objPersonaEmpRolCaracExtension->setUsrCreacion($strUsrCreacion);
                $objPersonaEmpRolCaracExtension->setIpCreacion($objRequest->getClientIp());
                $objPersonaEmpRolCaracExtension->setEstado('Activo');
                $emComercial->persist($objPersonaEmpRolCaracExtension);
                $emComercial->flush();
            }

            $emComercial->getConnection()->commit();
            $strResponse = 'OK';
        }
        catch(\Exception $e)
        {
            if ($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->rollback();
                $emComercial->getConnection()->close();
            }

            $serviceUtil->insertError('Telcos+',
                                      'ClienteController.ajaxAsignarIngenieroVIPAction',
                                      $e->getMessage(),
                                      $strUsrCreacion,
                                      $objRequest->getClientIp()
                                     );

            $strResponse = $e->getMessage();
        }

        return new Response($strResponse);
    }

    /**
     * @Secure(roles="ROLE_151-3737")
     * 
     * Documentación para el método 'ajaxEliminarIngenieroVIPAction'.
     *
     * Elimina lógicamente el Ingeniero VIP asignado al cliente VIP
     *
     * @param int $idPer IdPersonaEmpresaRol del Cliente
     * 
     * @return Response resultado de la operación.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 04-04-2016
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.1 24-01-2020 - Se agrego la eliminación de las características de la ciudad
     *                           y la extensión de teléfono del Ingeniero VIP
     */
    public function ajaxEliminarIngenieroVIPAction($idPer)
    {
        $serviceUtil    = $this->get('schema.Util');
        $objRequest     = $this->getRequest();
        $objSesion      = $objRequest->getSession();
        $strIdIngCaract = $objRequest->get('ingeniero');
        $strUsrCreacion = $objSesion->get('user');
        $emComercial    = $this->getDoctrine()->getManager();

        try
        {
            $emComercial->getConnection()->beginTransaction();
            
            //busco si existe el ingeniero vip
            $objPersonaEmpRolCarac   = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')->find($strIdIngCaract);
            if( !is_object($objPersonaEmpRolCarac) )
            {
                throw new \Exception("No se encontró el Ingeniero VIP asignado, por favor notificar a Sistemas.");
            }
            
            // Se elimina Lógicamente la asignación del ingeniero VIP
            $objPersonaEmpRolCarac->setEstado('Eliminado');
            $objPersonaEmpRolCarac->setFeUltMod(new \DateTime('now'));
            $objPersonaEmpRolCarac->setUsrUltMod($strUsrCreacion);

            $emComercial->persist($objPersonaEmpRolCarac);
            $emComercial->flush();

            //obtengo las relaciones de características que contiene este Ingeniero VIP
            $arrayPerEmpRolCarac    = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                 ->findBy(array('personaEmpresaRolCaracId' => $objPersonaEmpRolCarac->getId(),
                                                                'estado'                   => 'Activo'));
            foreach( $arrayPerEmpRolCarac as $objInfoPerEmpRolCarac )
            {
                //se elimina las características del Ingeniero VIP
                $objInfoPerEmpRolCarac->setEstado('Eliminado');
                $objInfoPerEmpRolCarac->setFeUltMod(new \DateTime('now'));
                $objInfoPerEmpRolCarac->setUsrUltMod($strUsrCreacion);
                $emComercial->persist($objInfoPerEmpRolCarac);
                $emComercial->flush();
            }
            $emComercial->getConnection()->commit();
            $strResponse = 'OK';
        }
        catch(\Exception $e)
        {
            if ($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->rollback();
                $emComercial->getConnection()->close();
            }
            $serviceUtil->insertError('Telcos+',
                                      'ClienteController.ajaxEliminarIngenieroVIPAction',
                                      $e->getMessage(),
                                      $strUsrCreacion,
                                      $objRequest->getClientIp()
                                     );
            $strResponse = $e->getMessage();
        }

        return new Response($strResponse);
    }
    
    /**
     * Documentación para el método 'procesarDatosIngenieroVIP'.
     *
     * Valida que el cliente sea VIP
     * Retorna la característica ID_VIP, la PersonaEmpresaRol y su Caracteristica Empresa Rol
     *
     * @param Integer       $intIdPer      Id de la PersonaEmpresaRol
     * @param String        $strCodEmpresa Código de la empresa en sesión
     * @param String        $strLoginIng   Login del Ingeniero VIP
     *
     * @return Array ['PERSONA_EMP_ROL_CARAC'] InfoPersonaEmpresaRolCarac: Caracterista del Cliente VIP que relaciona al ingeniero VIP
     *               ['PERSONA_EMP_ROL']       InfoPersonaEmpresaRol:      PersonaEmpresaRol del Cliente VIP
     *               ['CARACTERISTICA']        AdmiCaracteristica:         Característica ID_VIP
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 04-04-2016
     */
    private function procesarDatosIngenieroVIP($intIdPer, $strCodEmpresa, $strLoginIng)
    {
        $em = $this->getDoctrine()->getManager();
        
        // Obtenemos la Característica
        $entityCaracteristica = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                   ->getCaracteristicaPorDescripcionPorEstado('ID_VIP', 'Activo');
        if(!$entityCaracteristica)
        {
           throw new \Exception("Entity AdmiCaracteristica 'ID_VIP' not found");
        }

        $entityPersonaEmpRol = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intIdPer);

        if(!$entityPersonaEmpRol)
        {
           throw new \Exception("Entity InfoPersonaEmpresaRol not found");
        }

        $arrayParams = array('personaEmpresaRolId' => $entityPersonaEmpRol, 
                             'caracteristicaId'    => $entityCaracteristica, 
                             'valor'               => $strLoginIng,
                             'estado'              => 'Activo');

        $arrayResponse['PERSONA_EMP_ROL_CARAC'] = $em->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')->findOneBy($arrayParams);
        $arrayResponse['PERSONA_EMP_ROL']       = $entityPersonaEmpRol;
        $arrayResponse['CARACTERISTICA']        = $entityCaracteristica;
        return $arrayResponse;
    }
    
    /**
     * @Secure(roles="ROLE_151-4138") 
     * 
     * Documentación para el método 'ajaxActualizarDatosFacturacionAction'.
     *
     * Actualiza los datos financieros "Paga IVA" y/o "Es Prepago", dejando un historial de cambios.
     *
     * @return Response resultado de la operación
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 03-06-2016
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 18-06-2016
     * Se corrige la forma de guardado del histórico de cambios
     * 
     * @author Anabelle Penaherrera<apenaherrera@telconet.ec>       
     * @version 1.2 26-07-2016
     * Se agrega parametro CONTRIBUCION_SOLIDARIA S/N  
     * 
     * @author Edgar Holguin<eholguin@telconet.ec>       
     * @version 1.3 11-07-2017
     * Se agrega en registro de historial el nuevo tipo de facturación (Postpago manual)
     *  
     * @author Adrian Limones <alimonesr@telconet.ec>
     * @version 1.4 05-08-2020    
     * Se agrega envio de notificacion por correo cuando se cambian datos de facturacion del cliente.    
     */    
     
    public function ajaxActualizarDatosFacturacionAction()
    {
        $objRequest                = $this->getRequest();
        $objSesion                 = $objRequest->getSession();
        $intIdPer                  = $objRequest->get('idPer');
        $strPagaIva                = $objRequest->get('pagaIva');
        $strEsPrepago              = $objRequest->get('esPrepago');
        $strContribucionSolidaria  = $objRequest->get('contribucionSolidaria');
        
        $strBoolPagaIva               = $objRequest->get('boolPagaIva');
        $strBoolEsPrepago             = $objRequest->get('boolEsPrepago');
        $strBoolContribucionSolidaria = $objRequest->get('boolContribucionSolidaria');
        $strEsPrepagoActual           = "";
        
        $usrCreacion   = $objSesion->get('user');
        $em            = $this->getDoctrine()->getManager();

        $strPagaIvaActual = "";
        $strContribucionSolidariaActual = "";
        $strPagaIvaModificado = '';
        $strContribucionSolidariaModificado = '';
        $strEsPrepagoVisual = '';

        try
        {
            $em->getConnection()->beginTransaction();
            
            $entityPersonaEmpRol = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intIdPer);
            $objMotivo           = $em->getRepository('schemaBundle:AdmiMotivo')->findOneBy(array('nombreMotivo' => 'CAMBIO DATOS FACTURACION'));
            $entityPersona = $em->getRepository('schemaBundle:InfoPersona')->find($entityPersonaEmpRol->getPersonaId());
            $strPagaIvaActual = $entityPersona->getPagaIva(); 
            $objContrSolidaria = $em->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                        ->getOneByCaracteristica($intIdPer,'CONTRIBUCION_SOLIDARIA');
                $objAdmiCaracteris = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                        ->findOneBy(array('descripcionCaracteristica' => 'CONTRIBUCION_SOLIDARIA'));
                if(!$objAdmiCaracteris)
                {
                    throw new \Exception("No existe Caracteristica CONTRIBUCION_SOLIDARIA");
                }
                
                if($objContrSolidaria)
                {

                    $strContribucionSolidariaActual = $objContrSolidaria->getValor();
                }  
            
              
            if(!$entityPersonaEmpRol)
            {
                throw new \Exception("Entity InfoPersonaEmpresaRol not found");
            }
            
            if(!$objMotivo)
            {
                throw new \Exception("Entity AdmiMotivo not found");
            }
            
            $strEsPrepagoActual = $entityPersonaEmpRol->getEsPrepago();
            
            if("S" === $strEsPrepagoActual)
            {
                $strEsPrepagoActual = 'Prepago';
            }
            else if("N" === $strEsPrepagoActual)
            {
                $strEsPrepagoActual = 'Postpago';
            }
            else
            {
                $strEsPrepagoActual = 'Postpago manual';
            }
            // Se evalúa la cadena recibida convertida en booleano
            if(filter_var($strBoolEsPrepago, FILTER_VALIDATE_BOOLEAN))
            {
                $entityPersonaEmpRol->setEsPrepago($strEsPrepago);
                
                $motivo = new \telconet\schemaBundle\Entity\AdmiMotivo();
                $motivo->getId();
                
                $entityInfoPersonaEmpresaRolHisto1 = new InfoPersonaEmpresaRolHisto();
                $entityInfoPersonaEmpresaRolHisto1->setEstado('Activo');
                $entityInfoPersonaEmpresaRolHisto1->setFeCreacion(new \DateTime('now'));
                $entityInfoPersonaEmpresaRolHisto1->setUsrCreacion($usrCreacion);
                $entityInfoPersonaEmpresaRolHisto1->setIpCreacion($objRequest->getClientIp());
                $entityInfoPersonaEmpresaRolHisto1->setPersonaEmpresaRolId($entityPersonaEmpRol);
                $entityInfoPersonaEmpresaRolHisto1->setMotivoId($objMotivo->getId());
                
                if($strEsPrepago == 'S')
                {
                    $entityInfoPersonaEmpresaRolHisto1->setObservacion("El cliente pasó de ser $strEsPrepagoActual a ser Prepago");
                }
                else if($strEsPrepago == 'N')
                {
                    $entityInfoPersonaEmpresaRolHisto1->setObservacion("El cliente pasó de ser $strEsPrepagoActual a ser Postpago");
                }
                else
                {
                    $entityInfoPersonaEmpresaRolHisto1->setObservacion("El cliente pasó de ser $strEsPrepagoActual a ser Postpago manual");
                }
                
                $em->persist($entityPersonaEmpRol);
                $em->persist($entityInfoPersonaEmpresaRolHisto1);
            }
            
            // Se evalúa la cadena recibida convertida en booleano
            if(filter_var($strBoolPagaIva, FILTER_VALIDATE_BOOLEAN))
            {
                $entityPersona = $em->getRepository('schemaBundle:InfoPersona')->find($entityPersonaEmpRol->getPersonaId());
                
                if(!$entityPersona)
                {
                    throw new \Exception("Entity InfoPersona not found");
                }
                
                $entityPersona->setPagaIva($strPagaIva);
                
                
                $entityInfoPersonaEmpresaRolHisto2 = new InfoPersonaEmpresaRolHisto();
                $entityInfoPersonaEmpresaRolHisto2->setEstado('Activo');
                $entityInfoPersonaEmpresaRolHisto2->setFeCreacion(new \DateTime('now'));
                $entityInfoPersonaEmpresaRolHisto2->setUsrCreacion($usrCreacion);
                $entityInfoPersonaEmpresaRolHisto2->setIpCreacion($objRequest->getClientIp());
                $entityInfoPersonaEmpresaRolHisto2->setPersonaEmpresaRolId($entityPersonaEmpRol);
                $entityInfoPersonaEmpresaRolHisto2->setMotivoId($objMotivo->getId());
                
                if($strPagaIva == 'S')
                {
                    $entityInfoPersonaEmpresaRolHisto2->setObservacion("El cliente empezó a pagar IVA");
                }
                else
                {
                    $entityInfoPersonaEmpresaRolHisto2->setObservacion("El cliente dejó de pagar IVA");
                }
                
                $em->persist($entityPersona);
                $em->persist($entityInfoPersonaEmpresaRolHisto2);
            }
            
            if(filter_var($strBoolContribucionSolidaria, FILTER_VALIDATE_BOOLEAN))
            {
                $objContrSolidaria = $em->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                        ->getOneByCaracteristica($intIdPer,'CONTRIBUCION_SOLIDARIA');
                $objAdmiCaracteris = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                        ->findOneBy(array('descripcionCaracteristica' => 'CONTRIBUCION_SOLIDARIA'));
                if(!$objAdmiCaracteris)
                {
                    throw new \Exception("No existe Caracteristica CONTRIBUCION_SOLIDARIA");
                }
                
                if($objContrSolidaria)
                {
                    $objContrSolidaria->setValor($strContribucionSolidaria);
                    $em->persist($objContrSolidaria);
                }
                else
                {
                    if($objAdmiCaracteris)
                    {
                        $objInfoPersonaEmpresaRolCarac = new InfoPersonaEmpresaRolCarac();
                        $objInfoPersonaEmpresaRolCarac->setPersonaEmpresaRolId($entityPersonaEmpRol);
                        $objInfoPersonaEmpresaRolCarac->setCaracteristicaId($objAdmiCaracteris);
                        $objInfoPersonaEmpresaRolCarac->setValor($strContribucionSolidaria);
                        $objInfoPersonaEmpresaRolCarac->setFeCreacion(new \DateTime('now'));
                        $objInfoPersonaEmpresaRolCarac->setUsrCreacion($usrCreacion);
                        $objInfoPersonaEmpresaRolCarac->setIpCreacion($objRequest->getClientIp());
                        $objInfoPersonaEmpresaRolCarac->setEstado('Activo');
                        $em->persist($objInfoPersonaEmpresaRolCarac);
                    }
                }
                $entityInfoPersonaEmpresaRolHisto3 = new InfoPersonaEmpresaRolHisto();
                $entityInfoPersonaEmpresaRolHisto3->setEstado('Activo');
                $entityInfoPersonaEmpresaRolHisto3->setFeCreacion(new \DateTime('now'));
                $entityInfoPersonaEmpresaRolHisto3->setUsrCreacion($usrCreacion);
                $entityInfoPersonaEmpresaRolHisto3->setIpCreacion($objRequest->getClientIp());
                $entityInfoPersonaEmpresaRolHisto3->setPersonaEmpresaRolId($entityPersonaEmpRol);
                $entityInfoPersonaEmpresaRolHisto3->setMotivoId($objMotivo->getId());
                
                if($strContribucionSolidaria == 'S')
                {
                    $entityInfoPersonaEmpresaRolHisto3->setObservacion("El cliente se marco como CONTRIBUCION_SOLIDARIA en Si");
                }
                else
                {
                    $entityInfoPersonaEmpresaRolHisto3->setObservacion("El cliente se marco como CONTRIBUCION_SOLIDARIA en No");
                }                                                
                $em->persist($entityInfoPersonaEmpresaRolHisto3);
            }
            
            $em->flush();
            $em->getConnection()->commit();
            
            $objResponse = new Response();
            $objResponse->headers->set('Content-Type', 'text/json');
            
            $arrayResultadoJson = array('pagaIva'               => $strPagaIva,
                                        'esPrepago'             => $strEsPrepago,
                                        'contribucionSolidaria' => $strContribucionSolidaria);
        }
        catch(\Exception $e)
        {
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            
            $arrayResultadoJson['error'] = "No se concluyó la transacción con éxito. <br>Comuníquese con Sistemas para solucionar.<br>Error: " . 
                                            $e->getMessage();
        }
        $objResponse->setContent(json_encode($arrayResultadoJson));
        
        if($objResponse)
        {    
        /*Si se modifican datos de facturacion, hacemos el envio de la notificacion*/
        $emComunicacion     = $this->getDoctrine()->getManager('telconet_comunicacion');
        $objPlantilla = $emComunicacion->getRepository('schemaBundle:AdmiPlantilla')
        ->findOneBy(array(
                        'estado'  => 'Modificado',
                        'codigo'  => 'CAMBIODATOSFACT')); 
                
                
                if($strPagaIva == 'S')  
                {
                    
                    $strPagaIvaModificado="SI";
                }
                else 
                {
                    
                    $strPagaIvaModificado="NO";
                } 
                
                
                if($strContribucionSolidaria == 'S')  
                {
                    
                    $strContribucionSolidariaModificado="SI";
                }
                else 
                {
                    
                    $strContribucionSolidariaModificado="NO"; 
                } 

                
                if($strEsPrepago == 'S')
                {
                    
                    $strEsPrepagoVisual = "Prepago";
                }
                else if($strEsPrepago == 'N')
                {
                    
                    $strEsPrepagoVisual = "Postpago";
                }
                else
                {
                    
                    $strEsPrepagoVisual = "Postpago manual";
                }

                if($strPagaIvaActual == 'S')  
                {
                    
                    $strPagaIvaActual="SI";
                }
                else 
                {
                    
                    $strPagaIvaActual="NO";
                }

                if($strContribucionSolidariaActual == 'S')  
                {
                    
                    $strContribucionSolidariaActual="SI";
                }
                else 
                {
                    
                    $strContribucionSolidariaActual="NO"; 
                }

    $strHoy  = date("Y-m-d h:i");
     if(is_object($objPlantilla)) 
      {
        $strPlantilla = $objPlantilla->getPlantilla();
        $strPlantilla   = str_replace('fechamodificacion',$strHoy,$strPlantilla);
        $strPlantilla   = str_replace('usuariocreacion',$usrCreacion,$strPlantilla);
        $strPlantilla   = str_replace('cliente',$objSesion->get("cliente")["razon_social"],$strPlantilla);
        $strPlantilla   = str_replace('name',$objSesion->get("cliente")["nombres"],$strPlantilla);
        $strPlantilla   = str_replace('apellido',$objSesion->get("cliente")["apellidos"],$strPlantilla);
        $strPlantilla   = str_replace('identificacion',$objSesion->get("cliente")["identificacion"],$strPlantilla);
        $strPlantilla   = str_replace('pagaivaactual',$strPagaIvaActual,$strPlantilla);
        $strPlantilla   = str_replace('contribucionsolidariaactual',$strContribucionSolidariaActual,$strPlantilla);
        $strPlantilla   = str_replace('tipofacturacionactual',$strEsPrepagoActual,$strPlantilla);
        $strPlantilla   = str_replace('pagaivamodificado',$strPagaIvaModificado,$strPlantilla);
        $strPlantilla   = str_replace('contribucionsolidariamodificado',$strContribucionSolidariaModificado,$strPlantilla);
        $strPlantilla   = str_replace('tipofacturacionmodificado',$strEsPrepagoVisual,$strPlantilla);
        $intIdPlantilla     = $objPlantilla->getId();
        
        $arrayParametrosMail = array(
        'strCodEmpresa'      => $objSesion->get('idEmpresa'),
        'strParametro'       => 'NOTIFICACION CAMBIO TIPO FACTURACION',
        'strModulo'          => 'FINANCIERO',
        'strCodigoPlantilla' => 'CAMBIODATOSFACT',
         'intIdPlantilla'    => $objPlantilla->getId(),
         'strPlantilla'      => $strPlantilla,
       );
      
          $serviceFinanciero = $this->get('financiero.InfoDocumentoFinancieroCab');

          $strRespuestaMail  = $serviceFinanciero->sendEmailNotificacionClienteByParametros($arrayParametrosMail);
       }
       
        }
        return $objResponse;
    }
    
    /**
     * gridPuntosCambioRazonSocialPorLoginAction
     *
     * Metodo para obtener los puntos clientes Login que pueden ser cambiados de razon social
     * Consideracion: 
     * 1)No se permite Cambio de Razon Social Por Login, si el Login es Punto Padre 
     * de Facturacion de otros Logines.
     * 2)No se permite Cambio de Razon Social Por Login si el Login no posee servicio en estado Activo
     * 3)Se Cargara el Grid solo con los Logines que cumplen las condiciones. 
     *
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 07-08-2015
     * 
     * @param integer $idper
     * @param string  $strLogin     
     * @param string  $strNombrePunto
     * @param string  $strDireccion
     * @param integer $start
     * @param integer $limit
     * @return JSON
     */
     public function gridPuntosCambioRazonSocialPorLoginAction($idper)
    {      
        
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion         = $this->get('request');                
        $strLogin         = $peticion->query->get('p_login');
        $strNombrePunto   = $peticion->query->get('p_nombrePunto');
        $strDireccion     = $peticion->query->get('p_direccion');
        $start            = $peticion->query->get('start');
        $limit            = $peticion->query->get('limit');
        
        if($strLogin!='' || $strNombrePunto!='' || $strDireccion!='')
        {
            $start = 0;  
        }
        $em      = $this->get('doctrine')->getManager('telconet');        
        $objJson = $em->getRepository('schemaBundle:InfoPunto')
                   ->getJsonCambioRazonSocialPorLogin($idper,$strLogin,$strNombrePunto,$strDireccion,$start,$limit);
        
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /**   
     * @Secure(roles="ROLE_311-3077")
     * Documentación para el método 'ejecutaCambioRazonSocialPorPuntoAction'.
     *
     * Metodo utilizado para ejecutar el cambio de razon social por Punto o Login
     * Consideraciones:
     * En caso de que el "Cambio de Razon Social por Punto" se realice sobre un cliente ya existente:  
     * 1) Se crearan los nuevos logines en el cliente ya existente, bajo el contrato ya existente, se guardara informacion de los
     *    Nuevos Puntos asi como toda la data relacionada a estos.
     * 2) Los Logines origen del "Cambio de Razon Social por Punto" quedaran en estado Cancelado asi como toda la data relacionada a estos.
     *     
     * En caso de que el "Cambio de Razon Social por Punto" se realice sobre un cliente Nuevo:
     * 1) Se procedera a crear el nuevo Cliente con ROL de "Pre-cliente".
     * 2) Se generaran las nuevas Formas de Contacto del nuevo PreCliente.
     * 3) Se generara un nuevo Contrato en estado "Pendiente" para el PreCliente.
     * 4) Se subiran los archivos digitales adjuntos al nuevo Contrato pendiente.
     * 5) Los Logines nuevos que se trasladan se crearan en el momento de la Aprobacion del Nuevo contrato
     * 6) Los Logines origen del "Cambio de Razon Social por Punto" pasaran a Cancelados asi como toda la data relacionada a estos en el momento
     *    de la Aprobacion del Nuevo Contrato.
     * 
     * Validaciones:
     * 1) No se permite realizar "Cambio de Razon Social por Punto" a todos los Logines de un Cliente esto debera realizarse desde la opcion de 
     *    "Cambio de Razon Social" tradicional    
     * 2) No se permite Cambio de Razon Social Por Login, si el Login es Punto Padre de Facturacion de otros Logines.
     * 3) No se permite Cambio de Razon Social Por Login si el Login no posee servicio Activo
     * 4) No se permite realizar "Cambio de Razon Social por Punto" si el cliente posee deuda pendiente.
     * @param Request $request
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 25-08-2015     
     *      
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.1 27-12-2018 - Se agrega strNombrePais y intIdPais en el array de Parametros para validaciones de Telconet Panama
     *      
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 1.1 30-12-2020 - Se parametriza la forma de crear un contrato MD de forma digital y fisica
     * 
     * @author Lizbeth Cruz <apenaherrera@telconet.ec>
     * @version 1.2 09-11-2020 - Se modifica función para obtener correctamente la respuesta de la función ejecutaCambioRazonSocialPorPunto
     *                           del service
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.3 15-11-2021 - Se solicita regularizar la caracteristica de la función precio y reverso de CRS.
     * 
     * @author Carlos Caguana <ccaguana@telconet.ec>
     * @version 1.4 05-07-2022 - Se agrega validación de la identificación del cliente
     * 
     * @author Alex Gomez <algomez@telconet.ec>
     * @version 1.5 10-08-2022 - Se agrega array de puntos a activar desde ms por proceso de CRS.
     * 
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.6 19-01-2023 - Se envía por parámetro las variable strEsLogin el cúal se lo va a usar en la función de precio.
     */
    public function ejecutaCambioRazonSocialPorPuntoAction(Request $objRequest)
    {
        $emComercial            = $this->get('doctrine')->getManager('telconet');            
        $strCodEmpresa          = $objRequest->getSession()->get('idEmpresa');
        $intIdOficina           = $objRequest->getSession()->get('idOficina');
        $strNombrePais          = $objRequest->getSession()->get('strNombrePais');
        $intIdPais              = $objRequest->getSession()->get('intIdPais');
        $strUsrCreacion         = $objRequest->getSession()->get('user');
        $strClientIp            = $objRequest->getClientIp();
        $strPrefijoEmpresa      = $objRequest->getSession()->get('prefijoEmpresa');
        $arrayDatosFormFiles    = $objRequest->files->get('infodocumentotype');               
        $arrayDatosFormTipos    = $objRequest->get('infodocumentotype');
        $key                    = key($arrayDatosFormTipos);  
        $arrayTipoDocumentos    = array();
        $strRetornaPuntoRS      = ($strPrefijoEmpresa == 'MD' ||$strPrefijoEmpresa == 'EN' ) ? 'S'  : 'N';
        $strFormaContrato       = $objRequest->get('formaContrato');
        $intContratoFisico      = $strFormaContrato == 'Contrato Fisico' ? 1 : 0;
        $arrayDatosCliente      = $objRequest->request->get('clientetype');
        $arrayPuntosCRSActivar  = array();

        $intIdPersonEmpRolEmpl  = $objRequest->getSession()->get('idPersonaEmpresaRol');

        $arrayPuntosSeleccionados = array();
        $objAsignaciones          = json_decode($objRequest->request->get('puntos_asignados'));
        $arrayAsignaciones        = $objAsignaciones->asignaciones;
        foreach($arrayAsignaciones as $objAsignacion)
        {
            $arrayPuntosSeleccionados[] = $objAsignacion->login;
        }

        if($strPrefijoEmpresa == 'MD' || $strPrefijoEmpresa == 'EN')
        {
            $arrayParamFuncionPrecio  = array('intIdCliente'   =>  $arrayDatosCliente['antiguoIdCliente'] ,
                                              'intIdEmpresa'   =>  $strCodEmpresa,
                                              'strUsrCreacion' =>  $strUsrCreacion,
                                              'strEsLogin'     =>  'S',
                                              'arrayLogin'     =>  $arrayPuntosSeleccionados );
            /* @var $serviceCliente ClienteService */
            $serviceCliente = $this->get('comercial.Cliente');
            $serviceCliente->funcionPrecioRegularizar($arrayParamFuncionPrecio);
        }

        foreach ($arrayDatosFormTipos as $key => $tipos)
        {                          
            foreach ( $tipos as $key_tipo => $value )
            {                     
                $arrayTipoDocumentos[$key_tipo] = $value;
            }
        }        
        
        //Agrego origen_web al arreglo que se envia al service y le envio con "S"                
        $arrayParams = array_merge($objRequest->request->get('clientetype'),               
                $objRequest->get('infocontratoformapagotype'),
                $objRequest->get('infocontratotype'),
                $objRequest->get('infocontratoextratype'),
                array('feFinContratoPost'        => $objRequest->get('feFinContratoPost')),  
                array('origen_web'               => 'S'),
                array('arrayDatosFormFiles'      => $arrayDatosFormFiles),
                array('arrayTipoDocumentos'      => $arrayTipoDocumentos),                
                array('strCodEmpresa'            => $strCodEmpresa),
                array('intIdOficina'             => $intIdOficina),
                array('strNombrePais'            => $strNombrePais),
                array('intIdPais'                => $intIdPais),
                array('strUsrCreacion'           => $strUsrCreacion),                
                array('strClientIp'              => $strClientIp),
                array('strPrefijoEmpresa'        => $strPrefijoEmpresa),
                array('strRetornarRSPuntos'      => $strRetornaPuntoRS),
                array('strDatosRepresentanteLegal' => $objRequest->get('datosRepresentanteLegal')),
                array('puntos_asignados'         => $objRequest->request->get('puntos_asignados')),
                array('intContratoFisico'        => $intContratoFisico),
                array('intIdPersonEmpRolEmpl'    => $intIdPersonEmpRolEmpl)
            );
        
        try
        {
            /* @var $serviceInfoContrato \telconet\comercialBundle\Service\InfoContratoService */
            $serviceInfoContrato    = $this->get('comercial.InfoContrato');


            if(empty($arrayDatosCliente['identificacionCliente']))
            {
                throw new \Exception("No se encontró la identificación del cliente");
            }

            if(empty($arrayDatosCliente['tipoIdentificacion']))
            {
                throw new \Exception("No se encontró el tipo de identificación del cliente");
            }

            /* 
             * Verifica validez de la identificación ingresada
             * */
            $arrayParamValidaIdentifica = array('strTipoIdentificacion'     => $arrayDatosCliente['tipoIdentificacion'],
                                                'strIdentificacionCliente'  => $arrayDatosCliente['identificacionCliente'],
                                                'intIdPais'                 => "",
                                                'strCodEmpresa'             => $strCodEmpresa);
            $strValidacionRespuesta = $emComercial
                                           ->getRepository('schemaBundle:InfoPersona')
                                           ->validarIdentificacionTipo($arrayParamValidaIdentifica);

            if (!empty($strValidacionRespuesta))
            {
                $strValidacionRespuesta .= " Para el tipo identificación ".$arrayDatosCliente['tipoIdentificacion']." - "
                                            .$arrayDatosCliente['identificacionCliente'];
                throw new \Exception($strValidacionRespuesta,1);
            }
            
            $strMensajeCorreoECDF   = "";
            $arrayRespuesta         = $serviceInfoContrato->ejecutaCambioRazonSocialPorPunto($arrayParams);
            $arrayPuntosCRSActivar  = $arrayRespuesta["arrayPuntosCRS"];

            if (isset($arrayRespuesta["strMensajeCorreoECDF"]) && !empty($arrayRespuesta["strMensajeCorreoECDF"])) 
            {
                $strMensajeCorreoECDF = $arrayRespuesta["strMensajeCorreoECDF"];
            }
            if($arrayRespuesta["status"] === "OK" && $arrayRespuesta["tipoMensaje"] === "warning")
            {
                $this->get('session')->getFlashBag()->add($arrayRespuesta["tipoMensaje"], $arrayRespuesta["mensaje"]);
            }
            else if($arrayRespuesta["status"] === "ERROR")
            {
                throw new \Exception($arrayRespuesta["mensaje"]);
            }
            $objInfoPersonaEmpresaRol   = $arrayRespuesta["objInfoPersonaEmpresaRol"];                      

            $intRolId    = $objInfoPersonaEmpresaRol->getEmpresaRolId()->getId();

            $objInfoEmpresaRol = $emComercial->getRepository('schemaBundle:InfoEmpresaRol')
                                        ->findOneById($intRolId);

            if($objInfoEmpresaRol->getRolId() == 1)
            {
                $strUrlCliente    = $this->generateUrl('cliente_show', 
                                                  array('id' => $objInfoPersonaEmpresaRol->getPersonaId()->getId(),
                                                        'idper' => $objInfoPersonaEmpresaRol->getId()));
            }
            else
            {
                $strUrlCliente    = $this->generateUrl('precliente_show', 
                                                  array('id' => $objInfoPersonaEmpresaRol->getPersonaId()->getId(),
                                                        'idper' => $objInfoPersonaEmpresaRol->getId()));
            }
 
            $strStatusCliente = 1;
            if(( $strPrefijoEmpresa == 'MD' ||  $strPrefijoEmpresa == 'EN')&& $intContratoFisico != 1)
            {
                if ($arrayRespuesta["status"] == 403) 
                {  
                    $arrayRespuesta['strStatus']  = $arrayRespuesta["status"]; 
                    $arrayRespuesta['strMensaje'] = $arrayRespuesta['mensaje']; 
                } 
                else 
                {
                    $arrayServMigrados        = $arrayRespuesta['arrayServMigrados'];
                    $arrayAdendumsExcluirRS   = $arrayRespuesta['arrayAdendumsExcluirRS'];

                    $arrayRSPuntos            = array();
                    if (is_array($arrayServMigrados))
                    {
                        foreach ($arrayServMigrados  as $servMigrado)
                        {
                            $objServClonado = $servMigrado['servicioNuevo'];
                            if(is_object($objServClonado))
                            {
                                array_push($arrayRSPuntos, $objServClonado->getPuntoId()->getId());
                            }
                            
                        }
                        $arrayRSPuntos = array_values(array_unique($arrayRSPuntos));
                    }

                    $arrayPuntos = $emComercial->getRepository('schemaBundle:InfoPunto')
                                            ->findBy(array('personaEmpresaRolId' => $objInfoPersonaEmpresaRol->getId(),
                                                            'estado'              => array("Activo","Pendiente"),

                                                            ),
                                                        array('id' => 'DESC')
                                                        );
        
                    if(!empty($arrayPuntos))
                    { 
                        foreach($arrayPuntos as $objPunto)
                        {
                            $intPunto = $objPunto->getId();
                        }
                    }

                    $objContrato          = $emComercial->getRepository('schemaBundle:InfoContrato')
                                                        ->findOneBy(array("personaEmpresaRolId" => $objInfoPersonaEmpresaRol->getId(),
                                                                        "estado" => array('PorAutorizar','Activo')));

                    if(is_object($objContrato))
                    {
                        $intFormaPagoId       = $objContrato->getFormaPagoId()->getId();
                        $objContratoFormaPago = $emComercial->getRepository('schemaBundle:InfoContratoFormaPago')
                                                            ->findPorContratoIdYEstado($objContrato->getId(), 'Activo');

                        if(is_object($objContratoFormaPago))
                        {
                            $intTipoCuentaId         = $objContratoFormaPago->getTipoCuentaId()->getId();
                            $intBancoTipoCuentaId    = $objContratoFormaPago->getBancoTipoCuentaId()->getId();
                        }
                    }

                    //Obtengo Característica de USUARIO
                    $objCaracteristicaUsuario = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                            ->findOneBy(array("descripcionCaracteristica" => "USUARIO",
                                                                            "estado"                    => "Activo"));

                    if(is_object($objCaracteristicaUsuario))
                    {
                        //Inserto Caracteristica de USUARIO en el nuevo cliente
                        $objPersEmpRolCaracUsuario = new InfoPersonaEmpresaRolCarac();
                        $objPersEmpRolCaracUsuario->setPersonaEmpresaRolId($objInfoPersonaEmpresaRol);
                        $objPersEmpRolCaracUsuario->setCaracteristicaId($objCaracteristicaUsuario);
                        $objPersEmpRolCaracUsuario->setValor($objInfoPersonaEmpresaRol->getPersonaId()->getIdentificacionCliente());
                        $objPersEmpRolCaracUsuario->setFeCreacion(new \DateTime('now'));
                        $objPersEmpRolCaracUsuario->setUsrCreacion($strUsrCreacion);
                        $objPersEmpRolCaracUsuario->setEstado('Activo');
                        $objPersEmpRolCaracUsuario->setIpCreacion($objRequest->getClientIp());
                        $emComercial->persist($objPersEmpRolCaracUsuario);
                        $emComercial->flush();
                    }

                    $strProcesoContrato      = $objRequest->get('procesoContrato');
                    $strEnviarPin            = $objRequest->get('enviarPin');
                    $arrayParametrosContrato = array(
                        'strClientIp'               => $strClientIp,
                        'strUsrCreacion'            => $strUsrCreacion,
                        'intCodEmpresa'             => $strCodEmpresa,
                        'strPrefijoEmpresa'         => $strPrefijoEmpresa,
                        'intIdOficina'              => $intIdOficina,                      
                        'arrayFormFiles'            => $arrayDatosFormFiles,               
                        'arrayFormTipos'            => $arrayDatosFormTipos,
                        'strNombrePantalla'         => $strProcesoContrato,
                        'intKey'                    => key($arrayDatosFormTipos),
                        'intIdPuntoSession'         => $intPunto,
                        'strFeFinContratoPost'      => $objRequest->get('feFinContratoPost'),
                        'strConvenioPago'           => isset($objRequest->get('infocontratodatoadicionaltype')['convenioPago']) ? 'S':'N',
                        'strVip'                    => isset($objRequest->get('infocontratodatoadicionaltype')['esVip']) ? 'S':'N',
                        'strTramiteLegal'           => isset($objRequest->get('infocontratodatoadicionaltype')['esTramiteLegal']) ? 'S':'N',
                        'strPermiteCorteAutomatico' => isset($objRequest->get('infocontratodatoadicionaltype')['permiteCorteAutomatico']) ? 'S':'N',
                        'strFideicomiso'            => isset($objRequest->get('infocontratodatoadicionaltype')['fideicomiso']) ? 'S':'N',
                        'strTiempoEsperaMesesCorte' => $objRequest->get('infocontratodatoadicionaltype')['tiempoEsperaMesesCorte'],
                        'intFormaPago'              => isset($objRequest->get('infocontratotype')['formaPagoId']) ? 
                                                        $objRequest->get('infocontratotype')['formaPagoId'] :
                                                        $intFormaPagoId,
                        'strValorAnticipo'          => $objRequest->get('infocontratotype')['valorAnticipo'],
                        'strNumContratoEmpPub'      => $objRequest->get('infocontratotype')['numeroContratoEmpPub'],
                        'intTipoCuentaId'           => isset($objRequest->get('infocontratoformapagotype')['tipoCuentaId']) ? 
                                                        $objRequest->get('infocontratoformapagotype')['tipoCuentaId'] :
                                                        $intTipoCuentaId,
                        'intBancoTipoCuentaId'      => isset($objRequest->get('infocontratoformapagotype')['bancoTipoCuentaId']) ?
                                                        $objRequest->get('infocontratoformapagotype')['bancoTipoCuentaId'] :
                                                        $intBancoTipoCuentaId,
                        'strNumeroCtaTarjeta'       => $objRequest->get('infocontratoformapagotype')['numeroCtaTarjeta'],
                        'strTitularCuenta'          => $objRequest->get('infocontratoformapagotype')['titularCuenta'],
                        'strAnioVencimiento'        => $objRequest->get('infocontratoformapagotype')['anioVencimiento'] == 'Seleccione...' ? '' :
                                                    $objRequest->get('infocontratoformapagotype')['anioVencimiento'],
                        'strMesVencimiento'         => $objRequest->get('infocontratoformapagotype')['mesVencimiento'],
                        'strCodigoVerificacion'     => $objRequest->get('infocontratoformapagotype')['codigoVerificacion'],
                        'intTipoContratoId'         => $objRequest->get('infocontratoextratype')['tipoContratoId'],
                        'intPersonaEmpresaRolId'    => $objInfoPersonaEmpresaRol->getId(),
                        'strCambioPago'             => $objRequest->get('CambioPago') != null ? 'S' : 'N',
                        'strTipoDocumento'          => $strProcesoContrato == 'Contrato' ? 'C' : 'AS',
                        'strTelefono'               => $objRequest->get('telefonoCliente'),
                        'strEnviarPin'              => isset($strEnviarPin) ? 'N' : 'S', 
                        'cambioRazonSocial'         => 'S',
                        'cambioRazonSocialPunto'    => 'S',
                        'arrayRSPuntos'             => $arrayRSPuntos,
                        'arrayAdendumsExcluirRS'    => $arrayAdendumsExcluirRS
                    );
                    //Se manda a generar contrato
                    $arrayRespuesta = $this->crearContratoDigital($arrayParametrosContrato);  
                }            
                                                           
                if($arrayRespuesta['strStatus'] !== 0 && $arrayParams['yaexiste'] != 'S')
                {
                    $arrayRespuesta['strMensaje'] = "Se realizó el reverso de cambio de razón social. (".
                                                    $arrayRespuesta['strMensaje'].
                                                    "). Por favor comunicarse con Sistemas Soporte";

                    $arrayPuntosCliente = $emComercial->getRepository('schemaBundle:InfoPunto')
                                                      ->findByPersonaEmpresaRolId($objInfoPersonaEmpresaRol->getId());
         
                    foreach($arrayPuntosCliente as $objPtos)
                    {	
                        $arrayInfoServicio = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                         ->findServiciosPorEmpresaPorPunto($strCodEmpresa, 
                                                                                           $objPtos->getId(), 
                                                                                           99999999, 1, 0);
                        
                        $objInfoServicio   = $arrayInfoServicio['registros'];

                        foreach($objInfoServicio as $objServ)
                        {                   
                            $objServ->setEstado("Eliminado");
                            $emComercial->persist($objServ);
                            $emComercial->flush();                 
                        }  

                        $arrayAdendum = $emComercial->getRepository('schemaBundle:InfoAdendum')
                                                    ->findBy(
                                                            array('puntoId' => $objPtos->getId())
                                                        );

                        foreach($arrayAdendum as $objAdendum)
                        {
                            $objAdendum->setEstado("Eliminado");
                            $objAdendum->setUsrModifica($strUsrCreacion);
                            $emComercial->persist($objAdendum);
                            $emComercial->flush();
                        }

                        $objPtos->setEstado("Eliminado"); 
                        $objPtos->setLogin(null);  
                        $emComercial->persist($objPtos);
                        $emComercial->flush();               
                    }

                    $objInfoPersonaRepresentante = $emComercial->getRepository('schemaBundle:InfoPersonaRepresentante')
                                                    ->findOneBy(
                                                        array('personaEmpresaRolId' => $objInfoPersonaEmpresaRol->getId(),
                                                              'estado'              => 'Activo'));

                    if(is_object($objInfoPersonaRepresentante))
                    {
                        $objInfoPersonaRepresentante->setEstado("Eliminado");   
                        $emComercial->persist($objInfoPersonaRepresentante);
                        $emComercial->flush();    
                    }

                    $objContrato = $emComercial->getRepository('schemaBundle:InfoContrato')
                                               ->findOneBy(
                                                            array('personaEmpresaRolId' => $objInfoPersonaEmpresaRol->getId()));

                    if(is_object($objContrato))
                    {
                        $objContrato->setEstado("Eliminado");
                        $emComercial->persist($objContrato);
                        $emComercial->flush();
                    }

                    $objInfoPersonaEmpresaRol->setEstado("Eliminado");
                    $emComercial->persist($objInfoPersonaEmpresaRol);
                    $emComercial->flush(); 

                    $strStatusCliente = 0;

                    throw new \Exception($arrayRespuesta['strMensaje']);
                } 
                
                if($arrayRespuesta['strStatus'] !== 0 && $arrayParams['yaexiste'] == 'S')
                {
                    $strMotivoReverso = $arrayRespuesta['strMensaje']; 
                    $arrayParametros     = array(                                                                
                        'strCodEmpresa'                => $strCodEmpresa,
                        'strPrefijoEmpresa'            => $strPrefijoEmpresa,
                        'strUsrCreacion'               => $strUsrCreacion,
                        'strClientIp'                  => $strClientIp,
                        'strYaExiste'                  => $arrayParams['yaexiste'],
                        'intIdPersonaEmpresaRolDestino'=> $objInfoPersonaEmpresaRol->getId(),
                        'strMotivoReverso'             => $strMotivoReverso
                        );
                    /* @var $serviceInfoContrato \telconet\comercialBundle\Service\InfoContratoService */
                    $serviceInfoContrato   = $this->get('comercial.InfoContrato');
                    $arrayReversoCRS       = $serviceInfoContrato->ejecutarReversoCambioRazonSocial($arrayParametros);
                    $strMensajeAdvertencia = $arrayReversoCRS['strMensaje'];   
                    $strStatusCliente = 0;
                    
                    throw new \Exception($strMensajeAdvertencia);
                }
            }
            else
            {
                $arrayRespuesta = array('strMensaje' => 'Proceso realizado',
                                        'strStatus'  => 0);
            }
            $arrayRespuesta['strMensaje'] .= $strMensajeCorreoECDF;
            $arrayRespuestaProceso =  array('strMensaje'        => $arrayRespuesta['strMensaje'],
                                            'strStatus'         => $arrayRespuesta['strStatus'],
                                            'strUrl'            => $strUrlCliente,
                                            'strStatusCliente'  => $strStatusCliente,
                                            'intCodEmpresa'     => $strCodEmpresa,
                                            'intPunto'          => $intPunto,
                                            'intPersonaEmprRol' => $objInfoPersonaEmpresaRol->getId(),
                                            'strContratoFisico' => $intContratoFisico,
                                            'arrayPuntosCRS'    => $arrayPuntosCRSActivar
                                            ); 

            $objResponse      = new Response(json_encode($arrayRespuestaProceso));

            return $objResponse;

        }
        catch (\Exception $e)
        {   //Direcciono a Pantalla de Cambio de Razon Social por Login muestro mensaje de error
            $this->get('session')->getFlashBag()->add('notice', $e->getMessage());
            $strUrlCliente = $this->generateUrl('cliente_cambioRazonSocialPorPunto',
                                    array('id' => $arrayParams['antiguoIdCliente'], 'idper'=>$arrayParams['personaEmpresaRolId']));
            $objResponse = new Response(
                json_encode(
                            array('strMensaje'      => $e->getMessage(),
                                  'strStatus'       => 99,
                                  'strStatusCliente'=> $strStatusCliente,
                                  'strUrl'          => $strUrlCliente)
                           ));
            return $objResponse;           
        }                
    }

   /**
    * Documentación para el método 'ajaxValidaContratoActivoAction'.
    *
    * Funcion que valida y verifica si existe la informacion de Contrato Activo para una persona_empresa_rol_id
    * Consideraciones: Llena informacion del contrato en arreglo que sera aprecargado en el twig en caso de no poseer contrato 
    * Activo devuelve bandera de que no encontro
    *     
    * @param integer $idper    
    *
    * @return Renders a view.
    *  
    * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
    * @version 1.0 11-09-2015
    *
    * @author Néstor Naula <nnaulal@telconet.ec>
    * @version 1.1 11-11-2020 - Se descrifra el número de tarjeta o cuenta
    * @since 1.0
    */
    public function ajaxValidaContratoActivoAction()
    {         
        $em                   = $this->get('doctrine')->getManager('telconet');
        $request              = $this->getRequest();
        $strCodEmpresa        = $request->getSession()->get('idEmpresa');
        $strPrefijoEmpresa    = $request->getSession()->get('prefijoEmpresa');
        $strIdentificacion    = trim($request->request->get("identificacion"));
        $intIdPersonaEmpRol   = $request->request->get("personaEmpresaRolId");
        $boolIsTarjeta        = false;
        $objPersonaEmpresaRol = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                ->findByIdentificacionTipoRolEmpresa($strIdentificacion, 'Cliente', $strCodEmpresa);
        if(!empty($intIdPersonaEmpRol))
        {
            $objPersonaEmpresaRol = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                     ->findOneById($intIdPersonaEmpRol);
        }
        $arrayDatos = array();
        if($objPersonaEmpresaRol)
        {  
            $objContrato = $em->getRepository('schemaBundle:InfoContrato')
                           ->findContratoActivoPorPersonaEmpresaRol($objPersonaEmpresaRol->getId());

            if(!empty($intIdPersonaEmpRol) && !is_object($objContrato))
            {
                $objContrato = $em->getRepository('schemaBundle:InfoContrato')
                                        ->findOneBy(array("personaEmpresaRolId" => $intIdPersonaEmpRol,
                                                          "estado" => array('PorAutorizar')));
            }
            if(!$objContrato)
            {
                return new Response("no");
            }
            else
            {
                $objContratoFormaPago = $em->getRepository('schemaBundle:InfoContratoFormaPago')
                                        ->findPorContratoIdYEstado($objContrato->getId(), 'Activo');

                if($objContratoFormaPago != null)
                {
                    $objTipoCuenta = $objContratoFormaPago->getTipoCuentaId();
                    if(is_object($objTipoCuenta))
                    {
                        $boolIsTarjeta = ($objTipoCuenta->getEsTarjeta() === 'S') ? true : false;
                    }
                    /* @var $serviceCrypt \telconet\seguridadBundle\Service\CryptService */
                    $serviceCrypt        = $this->get('seguridad.Crypt');
                    $strNumCtaTarjDesenc = $serviceCrypt->descencriptar($objContratoFormaPago->getNumeroCtaTarjeta());
                    $arrayDatos = array(
                        'idContrato'           => $objContrato->getId(),
                        'tipoContratoId'       => $objContrato->getTipoContratoId()->getId(),
                        'formaPagoId'          => $objContrato->getFormaPagoId()->getId(),
                        'numeroContratoEmpPub' => $objContrato->getNumeroContratoEmpPub(),
                        'tipoCuentaId'         => $objContratoFormaPago->getTipoCuentaId()->getId(),
                        'bancoTipoCuentaId'    => $objContratoFormaPago->getBancoTipoCuentaId()->getId(),
                        'numeroCtaTarjeta'     => $strNumCtaTarjDesenc,
                        'titularCuenta'        => $objContratoFormaPago->getTitularCuenta(),
                        'mesVencimiento'       => $objContratoFormaPago->getMesVencimiento(),
                        'anioVencimiento'      => $objContratoFormaPago->getAnioVencimiento(),
                        'codigoVerificacion'   => $objContratoFormaPago->getCodigoVerificacion(),
                        'boolIsTarjeta'        => $boolIsTarjeta);
                }
                else
                {
                    $arrayDatos = array(
                        'idContrato'           => $objContrato->getId(),
                        'tipoContratoId'       => $objContrato->getTipoContratoId()->getId(),
                        'formaPagoId'          => $objContrato->getFormaPagoId()->getId(),
                        'numeroContratoEmpPub' => $objContrato->getNumeroContratoEmpPub(),
                        'tipoCuentaId'         => '',
                        'bancoTipoCuentaId'    => '',
                        'numeroCtaTarjeta'     => '',
                        'titularCuenta'        => '',
                        'mesVencimiento'       => '',
                        'anioVencimiento'      => '',
                        'codigoVerificacion'   => '',
                        'boolIsTarjeta'        => $boolIsTarjeta);
                }
                return new Response(json_encode(array($arrayDatos)));
            }
        }
        return new Response("no");
    }

    /**
     * @Secure(roles="ROLE_151-4677") 
     * 
     * Documentación para el método 'ajaxActualizarFeFinContratoAction'.
     *
     * Actualiza Campo Fecha fin del contrato del cliente, deja Historial de la edicion.
     * Opcion solo aplicable para TN
     * @return Response resultado de la operación
     *      
     * @author Anabelle Penaherrera<apenaherrera@telconet.ec>       
     * @version 1.0 13-09-2016
     * 
     */
    public function ajaxActualizarFeFinContratoAction()
    {
        $objRequest               = $this->getRequest();
        $objSesion                = $objRequest->getSession();
        $intIdPer                 = $objRequest->get('idPer');        
        $strFechaFinContrato      = $objRequest->get('fechaFinContrato');         
        $arrayFeFinContrato       = explode("-",$strFechaFinContrato);        
        $strFechaFinContrato      = date("Y-m-d H:i:s", strtotime($arrayFeFinContrato[0]."-".$arrayFeFinContrato[1]."-".$arrayFeFinContrato[2]));
        $strFormatFeFinContrato   = date("Y-m-d", strtotime($arrayFeFinContrato[0]."-".$arrayFeFinContrato[1]."-".$arrayFeFinContrato[2]));
        $strUsrCreacion           = $objSesion->get('user');
        $strPrefijoEmpresa        = $objSesion->get('prefijoEmpresa');
        $em                       = $this->getDoctrine()->getManager();              
        $em->getConnection()->beginTransaction();
        try
        {                        
            $objPersonaEmpRol = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intIdPer);
            $objMotivo        = $em->getRepository('schemaBundle:AdmiMotivo')->findOneBy(array('nombreMotivo' => 'CAMBIO DATOS FACTURACION'));
            $objContrato      = $em->getRepository('schemaBundle:InfoContrato')
                                   ->findContratoActivoPorPersonaEmpresaRol($intIdPer);
           
            if($strPrefijoEmpresa!='TN')
            {                        
                throw new \Exception("Solo se permite editar Fecha Fin de Contrato para TN");              
            }    
           
            if(!$objPersonaEmpRol)
            { 
                throw new \Exception("No encontro Cliente sobre el cual se realiza la edicion");
            }
            
            if(!$objMotivo)
            { 
                throw new \Exception("No encontro motivo definido para la edicion");
            }
            
            if(!$objContrato)
            {                
                throw new \Exception("No encontro Contrato sobre el cual se realiza la edicion");
            }              
            
            if(!$strFechaFinContrato)
            {                        
                throw new \Exception("No se ha selecccionado Fecha Fin de Contrato a Actualizar");              
            }    
            $strFeFinContratoAnterior      = strval(date_format($objContrato->getFeFinContrato(), "d/m/Y"));
            $objInfoPersonaEmpresaRolHisto = new InfoPersonaEmpresaRolHisto();
            $objInfoPersonaEmpresaRolHisto->setEstado('Activo');
            $objInfoPersonaEmpresaRolHisto->setFeCreacion(new \DateTime('now'));
            $objInfoPersonaEmpresaRolHisto->setUsrCreacion($strUsrCreacion);
            $objInfoPersonaEmpresaRolHisto->setIpCreacion($objRequest->getClientIp());                 
            $objInfoPersonaEmpresaRolHisto->setPersonaEmpresaRolId($objPersonaEmpRol);                
            $objInfoPersonaEmpresaRolHisto->setMotivoId($objMotivo->getId());
             
            $objInfoPersonaEmpresaRolHisto->setObservacion("Fecha Fin Contrato anterior: " 
                                                              . $strFeFinContratoAnterior);
            $objContrato->setFeFinContrato(date_create($strFechaFinContrato));               
            $em->persist($objContrato);                    
            $em->persist($objInfoPersonaEmpresaRolHisto);                
            $em->flush();
            $em->getConnection()->commit();  
            $objResponse = new Response();
            $objResponse->headers->set('Content-Type', 'text/json');  
            $arrayResultadoJson = array('success'             => true,
                                        'strFechaFinContrato' => $strFormatFeFinContrato,
                                        'msg'                 => 'Se edito Fecha Fin del Contrato Correctamente');
        }
        catch(\Exception $e)
        {
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            
            $arrayResultadoJson = array('success'             => false,
                                        'strFechaFinContrato' =>  '',
                                        'msg'                 => 'Error, No se pudo editar Fecha Fin del Contrato: '.$e->getMessage());
        }                 
        $objResponse->setContent(json_encode($arrayResultadoJson));
        
        return $objResponse;
    }
    
    /**
     * @Secure(roles="ROLE_8-4937")
     * 
     * Documentación para el método 'actualizaTipoEmpresaTributarioAction'.
     * Método que actualiza los campos tipo de empresa  y tipo tributario de un cliente.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>       
     * @version 1.0 17-11-2016
     * 
     * @return $objJsonResponse resultado de la actualización.
     *
     */
    public function actualizaTipoEmpresaTributarioAction()
    {
        $objRequest             = $this->getRequest();
        $intIdPersona           = $objRequest->request->get('idPersona');
        $intIdPersonaRol        = $objRequest->request->get('idPersonaRol');
        $strTipoEmpresaNuevo    = $objRequest->request->get('tipoEmpresaNuevo');
        $strTipoTributarioNuevo = $objRequest->request->get('tipoTributarioNuevo');        
        $objSesion              = $objRequest->getSession();
        
        $arrayParametros        = array('intIdPersona'           => $intIdPersona,
                                        'intIdPersonaRol'        => $intIdPersonaRol,
                                        'strTipoEmpresaNuevo'    => $strTipoEmpresaNuevo,
                                        'strTipoTributarioNuevo' => $strTipoTributarioNuevo,
                                        'strUsrCreacion'         => $objSesion->get('user'),
                                        'strIpCreacion'          => $objRequest->getClientIp()
                                       );
        
        $servicePersona   = $this->get('comercial.InfoPersona');
        
        $objJsonResponse  = new JsonResponse();
        $objJsonResponse->setData($servicePersona->editaTipoEmpresaTributario($arrayParametros));  
        
        return $objJsonResponse;
    } 
    
   /**
    * @Secure(roles="ROLE_364-4997")
    * Documentación para el método 'gridReporteClientesFacturasAction'.
    *
    * Retorna resultado de consulta de clientes y facturas iniciales asociadas al mismo.
    *
    * @return JsonResponse
    *
    * @author Edgar Holguin <eholguin@telconet.ec>
    * @version 1.0 02-12-2016
    */
    public function gridReporteClientesFacturasAction()
    {
        ini_set('max_execution_time', 99999);
        
        $arrayParametros       = array();
        $objRequest            = $this->getRequest();
        $objSession            = $objRequest->getSession();  
        $serviceUtil           = $this->get('schema.Util');
        $strIpClient           = $objRequest->getClientIp();
        $strUsrSesion          = $objSession->get('user'); 
        $emComercial           = $this->getDoctrine()->getManager();  
        
        
        $strValorFormaContacto                         = "";
        $arrayParametros['strFechaDesde']              = "";
        $arrayParametros['strFechaHasta']              = ""; 
        $arrayParametros['emailUsrSesion']             = "";
        $arrayParametros['prefijoEmpresa']             = $objSession->get('prefijoEmpresa');
        $arrayParametros['strIdsPlan']                 = $objRequest->get("cbxIdPlan");
        $arrayParametros['strIdsOficinasVendedor']     = $objRequest->get("cbxIdOficina");
        $arrayParametros['strIdsOficinasPtoCobertura'] = $objRequest->get("cbxIdOficinaPtoCobertura");
        
        $strMes         = $objRequest->get('mes');
        $strAnio        = $objRequest->get('anio');
        
        if($strMes && $strAnio)
        {
            $strDiasMes = date("d",mktime(0,0,0,$strMes+1,0,$strAnio));
            $arrayParametros['strFechaDesde'] = "01/".$strMes."/".$strAnio;
            $arrayParametros['strFechaHasta'] = $strDiasMes."/".$strMes."/".$strAnio;
        }

        $arrayFechaDesde = explode('T', $objRequest->get("fechaActivacionDesde"));
        $arrayFechaHasta = explode('T', $objRequest->get("fechaActivacionHasta")); 
                 
        
        $strFechaActivacionDesde = (isset($arrayFechaDesde) ? $arrayFechaDesde[0] : 0);
        $strFechaActivacionHasta = (isset($arrayFechaHasta) ? $arrayFechaHasta[0] : 0);
                   
        if($strFechaActivacionDesde && $strFechaActivacionDesde != "0")
        {
            $arrayParametros['strFechaActivacionDesde'] = date_format(date_create($strFechaActivacionDesde), "d/m/Y");
        }
        
        if($strFechaActivacionHasta && $strFechaActivacionHasta != "0")
        {
            $arrayParametros['strFechaActivacionHasta'] = date_format(date_create($strFechaActivacionHasta." +1 day"), "d/m/Y");     
        }
        
        $arrayFechaPrePlanificacionDesde = explode('T', $objRequest->get("fechaPrePlanificacionDesde"));
        $arrayFechaPrePlanificacionHasta = explode('T', $objRequest->get("fechaPrePlanificacionHasta")); 
        
        $strFechaPrePlanificacionDesde = (isset($arrayFechaPrePlanificacionDesde) ? $arrayFechaPrePlanificacionDesde[0] : 0);
        $strFechaPrePlanificacionHasta = (isset($arrayFechaPrePlanificacionHasta) ? $arrayFechaPrePlanificacionHasta[0] : 0);
                   
        if($strFechaPrePlanificacionDesde && $strFechaPrePlanificacionDesde != "0")
        {
            $arrayParametros['strFechaPrePlanificacionDesde'] = date_format(date_create($strFechaPrePlanificacionDesde), "d/m/Y");
        }
        
        if($strFechaPrePlanificacionHasta && $strFechaPrePlanificacionHasta != "0")
        {
            $arrayParametros['strFechaPrePlanificacionHasta'] = date_format(date_create($strFechaPrePlanificacionHasta." +1 day"), "d/m/Y");     
        }        
        
        $objInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                ->find($objSession->get('idPersonaEmpresaRol'));

        if(is_object($objInfoPersonaEmpresaRol))
        {
            $strValorFormaContacto = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                 ->getValorFormaContactoPorCodigo($objInfoPersonaEmpresaRol->getPersonaId(),'MAIL');

            if(!is_null($strValorFormaContacto))
            {
                $arrayParametros['emailUsrSesion'] = strtolower($strValorFormaContacto);
            }                
        }        
        
        $objOciCon      = oci_connect(
                                        $this->container->getParameter('user_comercial'),
                                        $this->container->getParameter('passwd_comercial'), 
                                        $this->container->getParameter('database_dsn')
                                     ); 
        
        $objCursor       = oci_new_cursor($objOciCon); 
        
       
        $arrayParametros['intEmpresaId']      = $objSession->get('idEmpresa');
        $arrayParametros['strUsrSesion']      = trim($strUsrSesion);   
        $arrayParametros['strIdentificacion'] = trim($objRequest->get("identificacion"));
        $arrayParametros['strRazonSocial']    = trim($objRequest->get("razonSocial"));
        $arrayParametros['strNombres']        = trim($objRequest->get("nombre"));
        $arrayParametros['strApellidos']      = trim($objRequest->get("apellido"));
        $arrayParametros['intStart']          = $objRequest->get('start');
        $arrayParametros['intLimit']          = $objRequest->get('limit');       
        $arrayParametros['oci_con']           = $objOciCon;  
        $arrayParametros['cursor']            = $objCursor; 
        
        $arrayClientesFacturas = array();
        $objJsonResponse       = new JsonResponse($arrayClientesFacturas);
             
        try
        {        
            $arrayClientesFacturas  = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                  ->getJsonClientesFacturas($arrayParametros,$this->container->get('router'));
              
        }
        catch (\Exception $e) 
        {   
            error_log($e->getMessage());
            $serviceUtil->insertError('Telcos+', 
                                      'ClienteController.gridReporteClientesFacturas',
                                      $e->getMessage(), 
                                      $strUsrSesion, 
                                      $strIpClient
                                     );            
        }  
        
        
        $objJsonResponse->setData($arrayClientesFacturas);

        return $objJsonResponse;        
    }
    
    /**
     * getOficinasByEmpresaAction, obtiene la informacion de las ofinas por empresa
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.0 09-12-2016
     * 
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getOficinasByEmpresaAction()
    {
        $objRequest              = $this->getRequest();
        $objSession              = $objRequest->getSession();
        $objReturnResponse       = new ReturnResponse();
        $emComercial             = $this->getDoctrine()->getManager();


        $arrayParametros                      = array();
        $arrayParametros['strPrefijoEmpresa'] = $objSession->get('prefijoEmpresa'); 
        $arrayParametros['strEstadoEmpresa']  = 'Activo';
        $arrayParametros['strEstadoOficina']  = 'Activo';
      
        $strAppendOficina                     = $objRequest->get('strAppendDatos');
        
        $objOficinasByEmpresa = $emComercial->getRepository('schemaBundle:InfoOficinaGrupo')
                                            ->getOficinasByEmpresa($arrayParametros);

        $arrayOficinas = array();
        
        if(!empty($strAppendOficina))
        {
            $arrayOficinas[0] = array('intIdObj'          => 0,
                                      'strDescripcionObj' => $strAppendOficina);
        }
        $arrayOficinas = array_merge($arrayOficinas,$objOficinasByEmpresa->getRegistros());

        $objReturnResponse->setRegistros($arrayOficinas);
        $objReturnResponse->setTotal($objOficinasByEmpresa->getTotal());
        $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);
        
        $objJsonResponse = new JsonResponse((array) $objReturnResponse);

        return $objJsonResponse;
    }  
    
    /**
     * getPlanesByEmpresaAction, obtiene la informacion de los planes por empresa
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.0 14-12-2016
     * 
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getPlanesByEmpresaAction()
    {
        $objRequest              = $this->getRequest();
        $objSession              = $objRequest->getSession();
        $objReturnResponse       = new ReturnResponse();
        $emComercial             = $this->getDoctrine()->getManager();

        $arrayParametros                      = array();
        $arrayParametros['strPrefijoEmpresa'] = $objSession->get('prefijoEmpresa'); 
        $arrayParametros['strEstadoEmpresa']  = 'Activo';
        $arrayParametros['strEstadoPlan']     = 'Activo';
      
        $strAppendDatos                       = $objRequest->get('strAppendDatos');
        
        $objPlanesByEmpresa = $emComercial->getRepository('schemaBundle:InfoPlanCab')
                                          ->getPlanesByEmpresa($arrayParametros);

        $arrayPlanes = array();
        
        if(!empty($strAppendDatos))
        {
            $arrayPlanes[] = array('intIdObj'          => 0,
                                   'strDescripcionObj' => $strAppendDatos);
        }
        $arrayPlanes = array_merge($arrayPlanes,$objPlanesByEmpresa->getRegistros());
        

        $objReturnResponse->setRegistros($arrayPlanes);
        $objReturnResponse->setTotal($objPlanesByEmpresa->getTotal());
        $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);

        $objJsonResponse = new JsonResponse((array) $objReturnResponse);

        return $objJsonResponse;        
    }   
    
    
   /**
    * @Secure(roles="ROLE_364-4997")
    * generarReporteClientesFacturasAction
    * Metodo que permite enviar los parametros para la generación y envío del reporte via mail .
    *  
    * @return JsonResponse
    * 
    * @author Edgar Holguin <eholguin@telconet.ec>
    * @version 1.0 15-09-2016 
    */     
    public function generarReporteClientesFacturasAction()
    {
        ini_set('max_execution_time', 99999);
        
        $arrayParametros       = array();
        $objRequest            = $this->getRequest();
        $objSession            = $objRequest->getSession();  
        $serviceUtil           = $this->get('schema.Util');
        $strIpClient           = $objRequest->getClientIp();
        $strUsrSesion          = $objSession->get('user'); 
        $emComercial           = $this->getDoctrine()->getManager();
        $emFinanciero          = $this->getDoctrine()->getManager('telconet_financiero'); 
        
        
        $strValorFormaContacto                         = "";
        $arrayParametros['strFechaDesde']              = "";
        $arrayParametros['strFechaHasta']              = ""; 
        $arrayParametros['strEmailUsrSesion']          = "";
        $arrayParametros['strPrefijoEmpresa']          = $objSession->get('prefijoEmpresa');
        $arrayParametros['strIdsPlan']                 = $objRequest->get("cbxIdPlan");
        $arrayParametros['strIdsOficinasVendedor']     = $objRequest->get("cbxIdOficina");
        $arrayParametros['strIdsOficinasPtoCobertura'] = $objRequest->get("cbxIdOficinaPtoCobertura");
        
        $strMes         = $objRequest->get('mes');
        $strAnio        = $objRequest->get('anio');
        
        if($strMes && $strAnio)
        {
            $strDiasMes = date("d",mktime(0,0,0,$strMes+1,0,$strAnio));
            $arrayParametros['strFechaDesde'] = "01/".$strMes."/".$strAnio;
            $arrayParametros['strFechaHasta'] = $strDiasMes."/".$strMes."/".$strAnio;
        }

        $arrayFechaDesde = explode('T', $objRequest->get("fechaActivacionDesde"));
        $arrayFechaHasta = explode('T', $objRequest->get("fechaActivacionHasta")); 
                 
        
        $strFechaActivacionDesde = (isset($arrayFechaDesde) ? $arrayFechaDesde[0] : 0);
        $strFechaActivacionHasta = (isset($arrayFechaHasta) ? $arrayFechaHasta[0] : 0);
                   
        if($strFechaActivacionDesde && $strFechaActivacionDesde != "0")
        {
            $arrayParametros['strFechaActivacionDesde'] = date_format(date_create($strFechaActivacionDesde), "d/m/Y");
        }
        
        if($strFechaActivacionHasta && $strFechaActivacionHasta != "0")
        {
            $arrayParametros['strFechaActivacionHasta'] = date_format(date_create($strFechaActivacionHasta." +1 day"), "d/m/Y");     
        } 
        
 
        $arrayFechaPrePlanificacionDesde = explode('T', $objRequest->get("fechaPrePlanificacionDesde"));
        $arrayFechaPrePlanificacionHasta = explode('T', $objRequest->get("fechaPrePlanificacionHasta")); 
        
        $strFechaPrePlanificacionDesde = (isset($arrayFechaPrePlanificacionDesde) ? $arrayFechaPrePlanificacionDesde[0] : 0);
        $strFechaPrePlanificacionHasta = (isset($arrayFechaPrePlanificacionHasta) ? $arrayFechaPrePlanificacionHasta[0] : 0);
                   
        if($strFechaPrePlanificacionDesde && $strFechaPrePlanificacionDesde != "0")
        {
            $arrayParametros['strFechaPrePlanificacionDesde'] = date_format(date_create($strFechaPrePlanificacionDesde), "d/m/Y");
        }
        
        if($strFechaPrePlanificacionHasta && $strFechaPrePlanificacionHasta != "0")
        {
            $arrayParametros['strFechaPrePlanificacionHasta'] = date_format(date_create($strFechaPrePlanificacionHasta." +1 day"), "d/m/Y");     
        }
        
        $objInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                ->find($objSession->get('idPersonaEmpresaRol'));

        if(is_object($objInfoPersonaEmpresaRol))
        {
            $strValorFormaContacto = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                 ->getValorFormaContactoPorCodigo($objInfoPersonaEmpresaRol->getPersonaId(),'MAIL');

            if(!is_null($strValorFormaContacto))
            {
                $arrayParametros['strEmailUsrSesion'] = strtolower($strValorFormaContacto);
            }                
        }        

        $arrayParametros['intEmpresaId']      = $objSession->get('idEmpresa');
        $arrayParametros['strUsrSesion']      = trim($strUsrSesion);  
        $arrayParametros['strIdentificacion'] = trim($objRequest->get("identificacion"));
        $arrayParametros['strRazonSocial']    = trim($objRequest->get("razonSocial"));
        $arrayParametros['strNombres']        = trim($objRequest->get("nombre"));
        $arrayParametros['strApellidos']      = trim($objRequest->get("apellido"));

        $emFinanciero->getConnection()->beginTransaction(); 
        $strResultado = $emComercial->getRepository('schemaBundle:InfoPersona')->generarReporteClientesFacturas($arrayParametros);     

        try
        {
            
            // Registro de historial de generación de reporte
            $objInfoReporteHistorial = new InfoReporteHistorial();
            $objInfoReporteHistorial->setEmpresaCod($arrayParametros['strPrefijoEmpresa']);
            $objInfoReporteHistorial->setCodigoTipoReporte('RPCF');
            $objInfoReporteHistorial->setFeCreacion(new \DateTime('now'));
            $objInfoReporteHistorial->setUsrCreacion($arrayParametros['strUsrSesion']);
            $objInfoReporteHistorial->setEmailUsrCreacion($arrayParametros['strEmailUsrSesion']);
            $objInfoReporteHistorial->setEstado('Activo');
            $objInfoReporteHistorial->setAplicacion('Telcos'); 
            $emFinanciero->persist($objInfoReporteHistorial);
            $emFinanciero->flush();            
            $emFinanciero->getConnection()->commit();            
            
        }
        catch (\Exception $e) 
        {
            error_log($e->getMessage());
            if ($emFinanciero->getConnection()->isTransactionActive()) 
            {                        
                $emFinanciero->getConnection()->rollback();
            } 
            
            $emFinanciero->getConnection()->close();
            
            $serviceUtil->insertError('Telcos+', 
                                      'ClienteController.generarReporteClientesFacturasAction',
                                      $e->getMessage(), 
                                      $strUsrSesion, 
                                      $strIpClient
                                     );               
        }
        
        $objJsonResponse = new JsonResponse($strResultado);
        return $objJsonResponse;           
    }
    
    
    /**
     * Documentación para funcion 'getMaxLongitudIdentificacion'.
     * 
     * Funcion que retorna la longitud máxima de la identificación formato JSON
     * 
     * @return Response $objResponse
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.0
     * @since 06-02-2018
     */     
    public function getMaxLongitudIdentificacionAction() 
    {
        $objResponse = new Response();
        $objRequest  = $this->getRequest();
        $objSession  = $objRequest->getSession();
        $emComercial = $this->get('doctrine')->getManager('telconet');
        
        $arrayParametros["strTipoIdentificacion"]  = $objRequest->get('strTipoIdentificacion');
        $arrayParametros["strNombrePais"]          = $objSession->get('strNombrePais');
        
        
        $strJsonLongitudIdentificacion = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                  ->getJsonMaxLongitudIdentificacion($arrayParametros);
    
        $objResponse->setContent($strJsonLongitudIdentificacion);
        
        return $objResponse;
    }

    /**
     * @Secure(roles="ROLE_151-7177")
     *
     * Documentación para el método 'ajaxCambiarMarcaVipTecnicoAction'.
     *
     * Función para cambiar la marca del VIP Técnico y retorna el estado de la operación
     * con la marca y un mensaje de resultado o error.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 23-01-2020
     *
     * @param int $intIdPer IdPersonaEmpresaRol del Cliente.
     *
     * @return Response $objResponse - Estado de la operación, la marca del VIP Técnico
     *                                 y el mensaje de resultado o error
     */
    public function ajaxCambiarMarcaVipTecnicoAction($intIdPer)
    {
        $objRequest   = $this->getRequest();
        $objSesion    = $objRequest->getSession();
        $serviceUtil  = $this->get('schema.Util');
        $emComercial  = $this->getDoctrine()->getManager();
        $strIpClient  = $objRequest->getClientIp();
        $strUsrSesion = $objSesion->get('user');

        try
        {
            $emComercial->getConnection()->beginTransaction();

            if( !isset($intIdPer) || empty($intIdPer) )
            {
                throw new \Exception("No se esta recibiendo el ID del cliente, por favor notificar a Sistemas.");
            }

            $objAdmiCaracteristica   = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneByDescripcionCaracteristica('ID_VIP_TECNICO');
            //verifico si la característica no existe retorno el error
            if( !is_object($objAdmiCaracteristica) )
            {
                throw new \Exception("No se encontró la característica para el VIP Técnico, por favor notificar a Sistemas.");
            }

            $objInfoPersonaEmpRol    = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intIdPer);
            //verifico si el cliente no existe retorno el error
            if( !is_object($objInfoPersonaEmpRol) )
            {
                throw new \Exception("No se encontró el cliente, por favor notificar a Sistemas.");
            }

            //mensaje inicial de la respuesta por default
            $strMensaje              = 'Se activó VIP Técnico del cliente.';

            //seteo el valor inicial de la característica de VIP Técnico del cliente
            $strValorCaracteristica  = 'Sí';

            //busco si la característica del cliente ya se encuentra creada
            $objInfoPerEmpRolCarac   = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                ->findOneBy(array('personaEmpresaRolId' => $objInfoPersonaEmpRol->getId(),
                                                                  'caracteristicaId'    => $objAdmiCaracteristica->getId(),
                                                                  'estado'              => 'Activo'));
            if( is_object($objInfoPerEmpRolCarac) )
            {
                $strValorCaract      = $objInfoPerEmpRolCarac->getValor();
                if( $strValorCaract == 'Sí' )
                {
                    $strValorCaracteristica = 'No';
                    //cambio el mensaje de la respuesta
                    $strMensaje             = 'Se inactivó VIP Técnico del cliente.';
                }
                //procedo a guardar el historial de eliminación
                $objInfoPerEmpRolCarac->setFeUltMod(new \DateTime('now'));
                $objInfoPerEmpRolCarac->setUsrUltMod($strUsrSesion);
                $objInfoPerEmpRolCarac->setEstado('Eliminado');
                $emComercial->persist($objInfoPerEmpRolCarac);
            }

            //procedo a crear la nueva característica del cliente
            $objInfoPerEmpRolCaracNew = new InfoPersonaEmpresaRolCarac();
            $objInfoPerEmpRolCaracNew->setPersonaEmpresaRolId($objInfoPersonaEmpRol);
            $objInfoPerEmpRolCaracNew->setCaracteristicaId($objAdmiCaracteristica);
            $objInfoPerEmpRolCaracNew->setValor($strValorCaracteristica);
            $objInfoPerEmpRolCaracNew->setFeCreacion(new \DateTime('now'));
            $objInfoPerEmpRolCaracNew->setIpCreacion($strIpClient);
            $objInfoPerEmpRolCaracNew->setUsrCreacion($strUsrSesion);
            $objInfoPerEmpRolCaracNew->setEstado('Activo');
            $emComercial->persist($objInfoPerEmpRolCaracNew);

            $emComercial->flush();
            $emComercial->getConnection()->commit();

            $arrayResult = array(
                'status'   => 'OK',
                'result'   => $strValorCaracteristica,
                'mensaje'  => $strMensaje
            );
        }
        catch (\Exception $e)
        {
            $arrayResult = array(
                'status'   => 'ERROR',
                'result'   => null,
                'mensaje'  => $e->getMessage()
            );

            if ($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->rollback();
                $emComercial->getConnection()->close();
            }

            $serviceUtil->insertError('Telcos+',
                                      'ClienteController.ajaxCambiarMarcaVipTecnicoAction',
                                      $e->getMessage(),
                                      $strUsrSesion,
                                      $strIpClient
                                     );
        }

        $objResponse = new Response();
        $objResponse->headers->set('Content-type', 'text/json');
        $objResponse->setContent(json_encode($arrayResult));

        return $objResponse;
    }

    /**
     * @Secure(roles="ROLE_151-7197")
     *
     * Documentación para el método 'ajaxEditarIngenieroVipAction'.
     *
     * Función para editar la asignación del Ingeniero VIP del cliente
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 24-01-2020
     *
     * @param int $intIdPer IdPersonaEmpresaRol del Cliente.
     *
     * @return Response resultado de la operación.
     */
    public function ajaxEditarIngenieroVipAction($intIdPer)
    {
        $serviceUtil    = $this->get('schema.Util');
        $objRequest     = $this->getRequest();
        $objSesion      = $objRequest->getSession();
        $strIdIngCaract = $objRequest->get('ingeniero');
        $strExtPerIng   = $objRequest->get('extension');
        $strIdCiudadPer = $objRequest->get('ciudad');
        $strUsrCreacion = $objSesion->get('user');
        $emComercial    = $this->getDoctrine()->getManager();
        $emGeneral      = $this->getDoctrine()->getManager("telconet_general");

        try
        {
            $emComercial->getConnection()->beginTransaction();

            if(!isset($strIdCiudadPer) || empty($strIdCiudadPer))
            {
                throw new \Exception("No ha seleccionado la ciudad del Ingeniero VIP.");
            }

            //verifico si el id de la ciudad existe
            $objAdmiCanton          = $emGeneral->getRepository('schemaBundle:AdmiCanton')
                                                    ->findOneBy(array('id'      => $strIdCiudadPer,
                                                                      'estado'  => 'Activo'));
            if(!is_object($objAdmiCanton))
            {
                throw new \Exception("No se encontró la ciudad, por favor notificar a Sistemas.");
            }

            $objAdmiCaractCiudad    = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneByDescripcionCaracteristica('ID_VIP_CIUDAD');
            //verifico si la característica no existe retorno el error
            if( !is_object($objAdmiCaractCiudad) )
            {
                throw new \Exception("No se encontró la característica para actualizar la ciudad del Ingeniero VIP, ".
                                     "por favor notificar a Sistemas.");
            }
            
            //busco si existe el ingeniero vip
            $objInfoPerEmpRolCarac   = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')->find($strIdIngCaract);
            if( !is_object($objInfoPerEmpRolCarac) )
            {
                throw new \Exception("No se encontró el Ingeniero VIP asignado, por favor notificar a Sistemas.");
            }

            //busco si ya se encuentra asignada la ciudad para este ingeniero vip
            $objPerEmpRolCiudad     = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                ->findOneBy(array('personaEmpresaRolCaracId' => $objInfoPerEmpRolCarac->getId(),
                                                                  'caracteristicaId'         => $objAdmiCaractCiudad->getId(),
                                                                  'estado'                   => 'Activo'));
            $booleanCrearCiudad     = true;
            if( is_object($objPerEmpRolCiudad) )
            {
                $booleanCrearCiudad = false;
                $strCiudadAnterior  = $objPerEmpRolCiudad->getValor();

                //obtengo todas las característica de este ingeniero vip
                $arrayPerEmpRolCarac    = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                            ->findBy(array('personaEmpresaRolId' => $objInfoPerEmpRolCarac->getPersonaEmpresaRolId()->getId(),
                                                           'caracteristicaId'    => $objInfoPerEmpRolCarac->getCaracteristicaId()->getId(),
                                                           'valor'               => $objInfoPerEmpRolCarac->getValor(),
                                                           'estado'              => 'Activo'));
                foreach( $arrayPerEmpRolCarac as $objPerEmpRolCarac )
                {
                    //busco si ya se encuentra asignada la ciudad para este ingeniero vip
                    $objPersonaCiudad   = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                    ->findOneBy(array('personaEmpresaRolCaracId' => $objPerEmpRolCarac->getId(),
                                                                      'caracteristicaId'         => $objAdmiCaractCiudad->getId(),
                                                                      'valor'                    => $strIdCiudadPer,
                                                                      'estado'                   => 'Activo'));
                    if( is_object($objPersonaCiudad) && $strCiudadAnterior != $strIdCiudadPer )
                    {
                        throw new \Exception("El Ingeniero VIP ya se encuentra asignado a la ciudad.");
                    }
                }

                if( $strCiudadAnterior != $strIdCiudadPer )
                {
                    $objPerEmpRolCiudad->setFeUltMod(new \DateTime('now'));
                    $objPerEmpRolCiudad->setUsrUltMod($strUsrCreacion);
                    $objPerEmpRolCiudad->setEstado('Eliminado');
                    $emComercial->persist($objPerEmpRolCiudad);
                    $emComercial->flush();
                    $booleanCrearCiudad = true;
                }
            }
            if($booleanCrearCiudad)
            {
                //Se asigna la ciudad de la característica del Ingeniero VIP
                $objPersonaEmpRolCaracCiudad = new InfoPersonaEmpresaRolCarac();
                $objPersonaEmpRolCaracCiudad->setPersonaEmpresaRolId($objInfoPerEmpRolCarac->getPersonaEmpresaRolId());
                $objPersonaEmpRolCaracCiudad->setCaracteristicaId($objAdmiCaractCiudad);
                $objPersonaEmpRolCaracCiudad->setPersonaEmpresaRolCaracId($objInfoPerEmpRolCarac->getId());
                $objPersonaEmpRolCaracCiudad->setValor($strIdCiudadPer);
                $objPersonaEmpRolCaracCiudad->setFeCreacion(new \DateTime('now'));
                $objPersonaEmpRolCaracCiudad->setUsrCreacion($strUsrCreacion);
                $objPersonaEmpRolCaracCiudad->setIpCreacion($objRequest->getClientIp());
                $objPersonaEmpRolCaracCiudad->setEstado('Activo');
                $emComercial->persist($objPersonaEmpRolCaracCiudad);
                $emComercial->flush();
            }

            if(isset($strExtPerIng) && !empty($strExtPerIng))
            {
                //verifico si la extension esta compuesta por números
                if( !preg_match('/^[0-9]+$/', $strExtPerIng) )
                {
                    throw new \Exception("La extensión de teléfono del Ingeniero VIP no es válida.");
                }

                //verifico si existe el parametro para validar la cantidad de números de la extensión
                $objAdmiParametroCab    = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                        ->findOneBy(array('nombreParametro' => 'CANTIDAD_NUMERO_EXTENSION',
                                                                          'estado'          => 'Activo'));
                if(!is_object($objAdmiParametroCab))
                {
                    throw new \Exception("No se encontró el parámetro para validar la cantidad de número de la extensión de teléfono, ".
                                         "por favor notificar a Sistemas.");
                }
                $objAdmiParametroDet    = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->findOneBy(array('parametroId' => $objAdmiParametroCab->getId(),
                                                                      'estado'      => 'Activo'));
                if(!is_object($objAdmiParametroDet))
                {
                    throw new \Exception("No se encontró el parámetro para validar la cantidad de número de la extensión de teléfono, ".
                                         "por favor notificar a Sistemas.");
                }
                $intCantidadNumeros     = $objAdmiParametroDet->getValor1();
                //verifico si la extension es igual a la cantidad de números validos
                if( strlen($strExtPerIng) != $intCantidadNumeros )
                {
                    throw new \Exception("La extensión debe tener $intCantidadNumeros números.");
                }

                $objAdmiCaractExtension = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                    ->findOneByDescripcionCaracteristica('EXTENSION USUARIO');
                //verifico si la característica no existe retorno el error
                if( !is_object($objAdmiCaractExtension) )
                {
                    throw new \Exception("No se encontró la característica para la extensión del Ingeniero VIP, ".
                                         "por favor notificar a Sistemas.");
                }

                //busco si ya se encuentra asignada la extensión para este ingeniero vip
                $objPerEmpRolExtension  = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                    ->findOneBy(array('personaEmpresaRolCaracId' => $objInfoPerEmpRolCarac->getId(),
                                                                      'caracteristicaId'         => $objAdmiCaractExtension->getId(),
                                                                      'estado'                   => 'Activo'));
                $booleanCrearExtension  = true;
                if( is_object($objPerEmpRolExtension) )
                {
                    $booleanCrearExtension = false;
                    $strExtensionAnterior  = $objPerEmpRolExtension->getValor();
                    if( $strExtensionAnterior != $strExtPerIng )
                    {
                        $objPerEmpRolExtension->setFeUltMod(new \DateTime('now'));
                        $objPerEmpRolExtension->setUsrUltMod($strUsrCreacion);
                        $objPerEmpRolExtension->setEstado('Eliminado');
                        $emComercial->persist($objPerEmpRolExtension);
                        $emComercial->flush();
                        $booleanCrearExtension = true;
                    }
                }
                if($booleanCrearExtension)
                {
                    //Se asigna la extensión de teléfono del Ingeniero VIP
                    $objPersonaEmpRolCaracExtension = new InfoPersonaEmpresaRolCarac();
                    $objPersonaEmpRolCaracExtension->setPersonaEmpresaRolId($objInfoPerEmpRolCarac->getPersonaEmpresaRolId());
                    $objPersonaEmpRolCaracExtension->setCaracteristicaId($objAdmiCaractExtension);
                    $objPersonaEmpRolCaracExtension->setPersonaEmpresaRolCaracId($objInfoPerEmpRolCarac->getId());
                    $objPersonaEmpRolCaracExtension->setValor($strExtPerIng);
                    $objPersonaEmpRolCaracExtension->setFeCreacion(new \DateTime('now'));
                    $objPersonaEmpRolCaracExtension->setUsrCreacion($strUsrCreacion);
                    $objPersonaEmpRolCaracExtension->setIpCreacion($objRequest->getClientIp());
                    $objPersonaEmpRolCaracExtension->setEstado('Activo');
                    $emComercial->persist($objPersonaEmpRolCaracExtension);
                    $emComercial->flush(); 
                }
            }

            $emComercial->getConnection()->commit();
            $strResponse = 'OK';
        }
        catch(\Exception $e)
        {
            if ($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->rollback();
                $emComercial->getConnection()->close();
            }

            $serviceUtil->insertError('Telcos+',
                                      'ClienteController.ajaxEditarIngenieroVipAction',
                                      $e->getMessage(),
                                      $strUsrCreacion,
                                      $objRequest->getClientIp()
                                     );

            $strResponse = $e->getMessage();
        }

        return new Response($strResponse);
    }

    /**
     * @Secure(roles="ROLE_151-3697, ROLE_151-7197")
     *
     * Documentación para el método 'getAjaxComboCiudadesVipAction'.
     *
     * Obtiene el listado de las ciudades para la asignación del Ingeniero VIP
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 24-01-2020
     *
     * @return Response $objResponse - Lista de ciudades para la asignación del Ingeniero VIP
     */
    public function getAjaxComboCiudadesVipAction()
    {
        $objRequest   = $this->getRequest();
        $objSesion    = $objRequest->getSession();
        $strIpClient  = $objRequest->getClientIp();
        $strUsrSesion = $objSesion->get('user');
        $serviceUtil  = $this->get('schema.Util');
        $emGeneral    = $this->getDoctrine()->getManager("telconet_general");

        try
        {
            //obtengo el parámetro cabecera de las ciudades
            $objParametroCab    = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                ->findOneByNombreParametro('ID_CIUDADES_VIP_TECNICO');
            //verifico que exista el parámetro cabecera de las ciudades
            if( !is_object($objParametroCab) )
            {
                throw new \Exception("No se encontraron las ciudades para la asignación del Ingeniero VIP, por favor notificar a Sistemas.");
            }

            //obtengo los detalles de cabecera de las ciudades
            $arrayParametroDet  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->findBy(array('parametroId' => $objParametroCab->getId(),
                                                               'estado'      => 'Activo'));
            $arrayResult        = array();
            foreach($arrayParametroDet as $objParametroDet)
            {
                $objAdmiCanton  = $emGeneral->getRepository('schemaBundle:AdmiCanton')
                                                ->findOneBy(array('id'     => $objParametroDet->getValor1(),
                                                                  'estado' => 'Activo'));
                if( is_object($objAdmiCanton) )
                {
                    //agrego al arreglo el id y el nombre del cantón
                    $arrayResult[]  = array(
                            'id_ciu'   => $objAdmiCanton->getId(),
                            'ciudad'   => $objAdmiCanton->getNombreCanton(),
                    );
                }
            }
            //se formula el json de respuesta
            $strJsonResultado   = '{"total":"' . count($arrayResult) . '","registros":' . json_encode($arrayResult) . '}';
        }
        catch (\Exception $e)
        {
            $strJsonResultado   = '{"total":"0", "registros":[], "error":[' . $e->getMessage() . ']}';
            
            $serviceUtil->insertError('Telcos+',
                                      'ClienteController.getAjaxComboCiudadesVipAction',
                                      $e->getMessage(),
                                      $strUsrSesion,
                                      $strIpClient
                                     );
        }

        $objResponse = new Response();
        $objResponse->headers->set('Content-type', 'text/json');
        $objResponse->setContent($strJsonResultado);

        return $objResponse;
    }

    /**
     * @Secure(roles="ROLE_151-7337")
     *
     * Documentación para el método 'generarCorteMasivoTNAction'.
     *
     * Renderiza la pantalla para generar el corte masivo TN
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 09-04-2020
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.1 27-05-2020 - Se modifica el tipo proceso CortarCliente -> CortarClienteTN
     *
     * @return Render Pantalla Generar Corte Masivo TN.
     */
    public function generarCorteMasivoTNAction()
    {
        $objRequest   = $this->getRequest();
        $objSession   = $objRequest->getSession();
        $strPrefEmpre = $objSession->get('prefijoEmpresa');
        $arrayCliente = $objSession->get('cliente');
        $emComercial  = $this->getDoctrine()->getManager();
        $emGeneral    = $this->getDoctrine()->getManager("telconet_general");
        //seteo el tipo de proceso
        $strTipoProceso = 'CortarClienteTN';
        //verifico si la empresa es TN
        if( $strPrefEmpre != 'TN' )
        {
            throw $this->createNotFoundException('La empresa en sesión es la incorrecta, debe ser Telconet.');
        }
        //seteo las variables del cliente
        $booleanPerEmp  = false;
        $strRazonSocial = null;
        $intIdPerEmpRol = null;
        $strTipoIdentificacion  = '';
        $strIdentificacion      = '';
        $intMaxServiciosAgregar = 100;
        //verifico si existe un login en session
        if( !empty($arrayCliente) && isset($arrayCliente['id_persona_empresa_rol']) && isset($arrayCliente['id_persona']) )
        {
            $booleanPerEmp  = true;
            $intIdPerEmpRol = $arrayCliente['id_persona_empresa_rol'];
            $objInfoPersona = $emComercial->getRepository('schemaBundle:InfoPersona')->find($arrayCliente['id_persona']);
            $strRazonSocial = $objInfoPersona->getRazonSocial();
            $strTipoIdentificacion = $objInfoPersona->getTipoIdentificacion();
            $strIdentificacion     = $objInfoPersona->getIdentificacionCliente();
        }
        //obtengo el maximo de servicios agregados para el corte masivo
        $objParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')->findOneBy(
                                                array(  'nombreParametro'   => 'MAXIMO_SERVICIOS_AGREGADOS_CORTE_REACTIVAR_MASIVO',
                                                        'estado'            => 'Activo'));
        if( is_object($objParametroCab) )
        {
            $objParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findOneBy(
                                                    array(  "parametroId" => $objParametroCab->getId(),
                                                            "valor1"      => "CORTE_MASIVO",
                                                            "estado"      => "Activo"));
            if( is_object($objParametroDet) )
            {
                $intMaxServiciosAgregar = $objParametroDet->getValor2();
            }
        }

        return $this->render('comercialBundle:cliente:generarCorteReactivarMasivo.html.twig', array(
                'strTipoProceso'         => $strTipoProceso,
                'booleanPerEmp'          => $booleanPerEmp,
                'strRazonSocial'         => $strRazonSocial,
                'intIdper'               => $intIdPerEmpRol,
                'intMaxServiciosAgregar' => $intMaxServiciosAgregar,
                'strTipoIdentificacion'  => $strTipoIdentificacion,
                'strIdentificacion'      => $strIdentificacion
        ));
    }

    /**
     * @Secure(roles="ROLE_151-7337, ROLE_151-7357")
     *
     * Documentación para el método 'generarSolicitudCorteReactivarMasivoTNAction'.
     *
     * Método para generar la solicitud del corte masivo TN
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 13-04-2020
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.1 11-05-2020 - Se agregan las validaciones para el tipo de proceso reactivar cliente
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.2 27-05-2020 - Se modifica los tipos procesos: CortarCliente -> CortarClienteTN y ReactivarCliente -> ReactivarClienteTN
     *
     * @return Response $objResponse - Estado de la operación y el mensaje de resultado o error
     */
    public function generarSolicitudCorteReactivarMasivoTNAction()
    {
        ini_set('max_execution_time', 3000000);
        $objRequest         = $this->getRequest();
        $objSession         = $objRequest->getSession();
        $strIpClient        = $objRequest->getClientIp();
        $strUsrSesion       = $objSession->get('user');
        $serviceUtil        = $this->get('schema.Util');
        $arrayIdServicios   = json_decode($objRequest->get('arrayIdServicios'));
        $intIdEmpresa       = $objSession->get('idEmpresa');
        $emComercial        = $this->getDoctrine()->getManager();
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        $strTipoProceso     = $objRequest->get("tipoProceso");
        $intIdMotivo        = $objRequest->get("intIdMotivo");

        $emComercial->getConnection()->beginTransaction();
        $emInfraestructura->getConnection()->beginTransaction();
        try
        {
            if( empty($strTipoProceso) || ( $strTipoProceso != "CortarClienteTN" && $strTipoProceso != "ReactivarClienteTN" ) )
            {
                throw new \Exception("El tipo de proceso masivo es el incorrecto, por favor notificar a Sistemas.");
            }
            if( !empty($arrayIdServicios) && is_array($arrayIdServicios) && count($arrayIdServicios) > 0)
            {
                //verifico el tipo de proceso para setear las variables
                if($strTipoProceso == "CortarClienteTN")
                {
                    $strEstadoServicio = 'Activo';
                    $strTipoSolicitud  = 'SOLICITUD CORTE MASIVO';
                    $strObservacion    = 'Se creó la solicitud de cortar servicio (ProcesoMasivo).';
                }
                elseif($strTipoProceso == "ReactivarClienteTN")
                {
                    $strEstadoServicio = 'In-Corte';
                    $strTipoSolicitud  = 'SOLICITUD REACTIVAR MASIVO';
                    $strObservacion    = 'Se creó la solicitud de reactivar servicio (ProcesoMasivo).';
                }

                $objTipoSolicitud   = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                        ->findOneBy(array(  "descripcionSolicitud" => $strTipoSolicitud,
                                                            "estado"               => "Activo"));

                //registro de la cabecera
                $objInfoProcesoMasivoCab = new InfoProcesoMasivoCab();
                $objInfoProcesoMasivoCab->setTipoProceso($strTipoProceso);
                $objInfoProcesoMasivoCab->setEmpresaCod($intIdEmpresa);
                $objInfoProcesoMasivoCab->setEstado("Pendiente");
                $objInfoProcesoMasivoCab->setFeCreacion(new \DateTime('now'));
                $objInfoProcesoMasivoCab->setUsrCreacion($strUsrSesion);
                $objInfoProcesoMasivoCab->setIpCreacion($strIpClient);
                $objInfoProcesoMasivoCab->setCantidadServicios(count($arrayIdServicios));
                $emInfraestructura->persist($objInfoProcesoMasivoCab);
                $emInfraestructura->flush();

                //inserto los servicios en los detalles del masivo
                foreach($arrayIdServicios as $intIdServicio)
                {
                    $objInfoServicio = $emComercial->getRepository('schemaBundle:InfoServicio')->findOneBy(array(
                                                                                                'id'     => $intIdServicio,
                                                                                                'estado' => $strEstadoServicio
                                                                                            ));
                    if(is_object($objInfoServicio) )
                    {
                        //crear solicitud
                        $objDetalleSolicitud = new InfoDetalleSolicitud();
                        $objDetalleSolicitud->setServicioId($objInfoServicio);
                        $objDetalleSolicitud->setTipoSolicitudId($objTipoSolicitud);
                        $objDetalleSolicitud->setUsrCreacion($strUsrSesion);
                        $objDetalleSolicitud->setFeCreacion(new \DateTime('now'));
                        $objDetalleSolicitud->setObservacion($strObservacion);
                        if($strTipoProceso == "CortarClienteTN")
                        {
                            $objDetalleSolicitud->setMotivoId($intIdMotivo);
                        }
                        $objDetalleSolicitud->setEstado("Pendiente");
                        $emComercial->persist($objDetalleSolicitud);
                        $emComercial->flush();

                        //agregar historial a la solicitud
                        $objDetalleSolHistorial = new InfoDetalleSolHist();
                        $objDetalleSolHistorial->setDetalleSolicitudId($objDetalleSolicitud);
                        $objDetalleSolHistorial->setIpCreacion($strIpClient);
                        $objDetalleSolHistorial->setFeCreacion(new \DateTime('now'));
                        $objDetalleSolHistorial->setUsrCreacion($strUsrSesion);
                        $objDetalleSolHistorial->setEstado($objDetalleSolicitud->getEstado());
                        $objDetalleSolHistorial->setObservacion($strObservacion);
                        $emComercial->persist($objDetalleSolHistorial);
                        $emComercial->flush();

                        //registro de los detalles de la cabecera
                        $objInfoProcesoMasivoDet = new InfoProcesoMasivoDet();
                        $objInfoProcesoMasivoDet->setProcesoMasivoCabId($objInfoProcesoMasivoCab);
                        $objInfoProcesoMasivoDet->setPuntoId($objInfoServicio->getPuntoId()->getId());
                        $objInfoProcesoMasivoDet->setServicioId($objInfoServicio->getId());
                        $objInfoProcesoMasivoDet->setSolicitudId($objDetalleSolicitud->getId());
                        $objInfoProcesoMasivoDet->setFeCreacion(new \DateTime('now'));
                        $objInfoProcesoMasivoDet->setUsrCreacion($strUsrSesion);
                        $objInfoProcesoMasivoDet->setIpCreacion($strIpClient);
                        $objInfoProcesoMasivoDet->setEstado("Pendiente");
                        $emInfraestructura->persist($objInfoProcesoMasivoDet);
                        $emInfraestructura->flush();

                        //registro servicio historial
                        $objServicioHistorial = new InfoServicioHistorial();
                        $objServicioHistorial->setServicioId($objInfoServicio);
                        $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                        $objServicioHistorial->setUsrCreacion($strUsrSesion);
                        $objServicioHistorial->setIpCreacion($strIpClient);
                        $objServicioHistorial->setEstado($objInfoServicio->getEstado());
                        $objServicioHistorial->setObservacion($strObservacion);
                        $emComercial->persist($objServicioHistorial);
                        $emComercial->flush();
                    }
                    else
                    {
                        throw new \Exception("No se encontró el servicio con el id: $intIdServicio, por favor notificar a Sistemas.");
                    }
                }

                //actualizo el id de la solicitud con el ultimo registro
                $objInfoProcesoMasivoCab->setSolicitudId($objDetalleSolicitud->getId());
                $emInfraestructura->persist($objInfoProcesoMasivoCab);
                $emInfraestructura->flush();
            }
            else
            {
                throw new \Exception("No se está recibiendo los servicios, por favor notificar a Sistemas.");
            }

            //guardar todos los cambios
            if($emInfraestructura->getConnection()->isTransactionActive())
            {
                $emInfraestructura->flush();
                $emInfraestructura->getConnection()->commit();
                $emInfraestructura->getConnection()->close();
            }
            if($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->flush();
                $emComercial->getConnection()->commit();
                $emComercial->getConnection()->close();
            }

            if($strTipoProceso == "CortarClienteTN")
            {
                $strMensaje = 'Se generó correctamente la solicitud de Corte Masivo.';
            }
            elseif($strTipoProceso == "ReactivarClienteTN")
            {
                $strMensaje = 'Se generó correctamente la solicitud de Reactivación Masiva.';
            }
            //seteo el arreglo del resultado
            $arrayResultado = array(
                'status'   => 'OK',
                'mensaje'  => $strMensaje
            );
        }
        catch (\Exception $e)
        {
            //seteo el arreglo del resultado
            $arrayResultado = array(
                'status'   => 'ERROR',
                'mensaje'  => $e->getMessage()
            );
            if($emInfraestructura->getConnection()->isTransactionActive())
            {
                $emInfraestructura->getConnection()->rollback();
                $emInfraestructura->getConnection()->close();
            }
            if($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->rollback();
                $emComercial->getConnection()->close();
            }
            $serviceUtil->insertError('Telcos+',
                                      'ClienteController.generarSolicitudCorteReactivarMasivoTNAction',
                                      $e->getMessage(),
                                      $strUsrSesion,
                                      $strIpClient
                                    );
        }

        $objResponse = new JsonResponse();
        $objResponse->setContent(json_encode($arrayResultado));

        return $objResponse;
    }

    /**
     * Documentación para el método 'getAjaxComboRazonSocialAction'.
     *
     * Obtiene el listado de la razon social de los clientes
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 09-04-2020
     *
     * @return Response $objResponse - Lista de la razon social de los clientes
     */
    public function getAjaxComboRazonSocialAction()
    {
        $objRequest   = $this->getRequest();
        $objSession   = $objRequest->getSession();
        $strIpClient  = $objRequest->getClientIp();
        $strUsrSesion = $objSession->get('user');
        $serviceUtil  = $this->get('schema.Util');
        $emComercial  = $this->getDoctrine()->getManager();

        $strEstado       = 'Activo';
        $intIdEmpresa    = $objSession->get('idEmpresa');
        $strRazonSocial  = $objRequest->get("query");
        $strCliente      = $objRequest->get("query");
        $intLimit        = $objRequest->get("limit");
        $intStart        = $objRequest->get("start");
        $intPage         = $objRequest->get("page");
        $strModulo       = 'Cliente';
        $strPrefijoEmp   = $objSession->get('prefijoEmpresa');
        $strTipoPersonal = 'Otros';

        try
        {
            $arrayParametros = array();
            $arrayParametros['estado']         = $strEstado;
            $arrayParametros['idEmpresa']      = $intIdEmpresa;
            $arrayParametros['fechaDesde']     = null;
            $arrayParametros['fechaHasta']     = null;
            $arrayParametros['nombre']         = null;
            $arrayParametros['apellido']       = null;
            $arrayParametros['razon_social']   = $strRazonSocial;
            $arrayParametros['strCliente']     = $strCliente;
            $arrayParametros['limit']          = $intLimit;
            $arrayParametros['start']          = $intStart;
            $arrayParametros['page']           = $intPage;
            $arrayParametros['tipo_persona']   = 'Cliente';
            $arrayParametros['usuario']        = '';
            $arrayParametros['strModulo']             = $strModulo;
            $arrayParametros['strPrefijoEmpresa']     = $strPrefijoEmp;
            $arrayParametros['strTipoPersonal']       = $strTipoPersonal;
            $arrayParametros['intIdPersonEmpresaRol'] = null;
            $arrayResultado = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findPersonasPorCriterios($arrayParametros);

            $arrayResult    = array();
            $arrayRegistros = $arrayResultado['registros'];
            foreach($arrayRegistros as $arrayData)
            {
                $strNombre = $arrayData['razon_social'];
                if(empty($strNombre))
                {
                    $strNombre = $arrayData['nombres'].' '.$arrayData['apellidos'];
                }
                //agrego al arreglo el id y el nombre de la razon social
                $arrayResult[]  = array(
                    'id'                  => $arrayData['id'],
                    'nombre'              => $strNombre,
                    'tipo_identificacion' => $arrayData['tipo_identificacion'],
                    'identificacion'      => $arrayData['identificacion']
                );
            }
            //se formula el json de respuesta
            $strJsonResultado = '{"total":"' . count($arrayResult) . '","registros":' . json_encode($arrayResult) . '}';
        }
        catch (\Exception $e)
        {
            $strJsonResultado   = '{"total":0, "registros":[], "error":"' . $e->getMessage() . '"}';
            $serviceUtil->insertError('Telcos+',
                                      'ClienteController.getAjaxComboRazonSocialAction',
                                      $e->getMessage(),
                                      $strUsrSesion,
                                      $strIpClient
                                    );
        }

        $objResponse = new JsonResponse();
        $objResponse->setContent($strJsonResultado);

        return $objResponse;
    }

    /**
     * Documentación para el método 'getAjaxServiciosPorClienteTNAction'.
     *
     * Obtiene el listado de los servicios por cliente TN
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 09-04-2020
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.1 11-05-2020 - Se agregan las validaciones para el tipo de proceso reactivar cliente
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.2 27-05-2020 - Se modifica los tipos procesos: CortarCliente -> CortarClienteTN y ReactivarCliente -> ReactivarClienteTN
     *
     * @return Response $objResponse - Lista de los servicios por cliente TN
     */
    public function getAjaxServiciosPorClienteTNAction()
    {
        ini_set('max_execution_time', 3000000);
        $objRequest     = $this->getRequest();
        $objSession     = $objRequest->getSession();
        $strIpClient    = $objRequest->getClientIp();
        $strUsrSesion   = $objSession->get('user');
        $serviceUtil    = $this->get('schema.Util');
        $intIdPerEmpRol = $objRequest->get("intIdPerEmpRol");
        $intIdEmpresa   = $objSession->get('idEmpresa');
        $strTipoProceso   = $objRequest->get("tipoProceso");
        $intIdPunto       = $objRequest->get("intIdPunto");
        $arrayIdServicios = json_decode($objRequest->get('arrayIdServicios'));

        try
        {
            //verifico el tipo de proceso para setear la variable
            if($strTipoProceso == "CortarClienteTN")
            {
                $strEstadoServicio = 'Activo';
            }
            elseif($strTipoProceso == "ReactivarClienteTN")
            {
                $strEstadoServicio = 'In-Corte';
            }
            //seteo el arreglo de los resultados
            $arrayResult    = array();
            //verifico si existe el id del cliente
            if( !empty($intIdPerEmpRol) )
            {
                $arrayParametros   = array(
                        'intIdEmpresa'          => $intIdEmpresa,
                        'intIdPerEmpRol'        => $intIdPerEmpRol,
                        'intIdPunto'            => $intIdPunto,
                        'arrayIdServicios'      => $arrayIdServicios,
                        'strEstadoPunto'        => 'Activo',
                        'strEstadoServicio'     => $strEstadoServicio,
                        'strTipoProcesoCab'     => $strTipoProceso,
                        'strEstadoMasivoCab'    => 'Pendiente',
                        'strEstadoMasivoDet'    => 'Pendiente',
                        'strUsrSesion'          => $strUsrSesion,
                        'strIpClient'           => $strIpClient,
                );
                $arrayDatosServicios = $this->getDatosServiciosPorRazonSocial($arrayParametros);
                if( $arrayDatosServicios['status'] == 'OK' )
                {
                    //agrego los servicios al arreglo
                    $arrayResult = $arrayDatosServicios['result'];
                }
                else
                {
                    throw new \Exception($arrayDatosServicios['result']);
                }
            }
            //se formula el json de respuesta
            $strJsonResultado = '{"total":"' . count($arrayResult) . '","registros":' . json_encode($arrayResult) . '}';
        }
        catch (\Exception $e)
        {
            $strJsonResultado   = '{"total":0, "registros":[], "error":"' . $e->getMessage() . '"}';
            $serviceUtil->insertError('Telcos+',
                                      'ClienteController.getAjaxServiciosPorClienteTNAction',
                                      $e->getMessage(),
                                      $strUsrSesion,
                                      $strIpClient
                                    );
        }

        $objResponse = new JsonResponse();
        $objResponse->setContent($strJsonResultado);

        return $objResponse;
    }

    /**
     * Documentación para el método 'getAjaxLoginsPorRazonSocialAction'.
     *
     * Obtiene el listado de los logins por la razon social del cliente
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 29-04-2020
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.1 11-05-2020 - Se agregan las validaciones para el tipo de proceso reactivar cliente
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.2 27-05-2020 - Se modifica los tipos procesos: CortarCliente -> CortarClienteTN y ReactivarCliente -> ReactivarClienteTN
     *
     * @return Response $objResponse - Lista de los logins por la razon social del cliente
     */
    public function getAjaxLoginsPorRazonSocialAction()
    {
        $objRequest   = $this->getRequest();
        $objSession   = $objRequest->getSession();
        $strIpClient  = $objRequest->getClientIp();
        $strUsrSesion = $objSession->get('user');
        $serviceUtil  = $this->get('schema.Util');

        $intIdEmpresa = $objSession->get('idEmpresa');
        $intIdPerRol  = $objRequest->get("intIdPerEmpRol");
        $strLogin     = $objRequest->get("query");
        $strTipoProceso   = $objRequest->get("tipoProceso");
        $arrayIdServicios = json_decode($objRequest->get('arrayIdServicios'));

        try
        {
            //verifico el tipo de proceso para setear la variable
            if($strTipoProceso == "CortarClienteTN")
            {
                $strEstadoServicio = 'Activo';
            }
            elseif($strTipoProceso == "ReactivarClienteTN")
            {
                $strEstadoServicio = 'In-Corte';
            }
            $arrayParametros   = array(
                    'intIdEmpresa'          => $intIdEmpresa,
                    'intIdPerEmpRol'        => $intIdPerRol,
                    'strLogin'              => $strLogin,
                    'arrayIdServicios'      => $arrayIdServicios,
                    'strEstadoPunto'        => 'Activo',
                    'strEstadoServicio'     => $strEstadoServicio,
                    'strTipoProcesoCab'     => $strTipoProceso,
                    'strEstadoMasivoCab'    => 'Pendiente',
                    'strEstadoMasivoDet'    => 'Pendiente',
                    'strUsrSesion'          => $strUsrSesion,
                    'strIpClient'           => $strIpClient,
            );
            $arrayResultado = $this->getDatosServiciosPorRazonSocial($arrayParametros);

            $arrayResult    = array();
            //agrego al arreglo la selección para todos
            $arrayResult[]  = array(
                'id'     => null,
                'login'  => 'Todos',
            );
            if( $arrayResultado['status'] == 'OK' )
            {
                foreach($arrayResultado['result'] as $arrayData)
                {
                    //agrego al arreglo el id y el login
                    $arrayResult[]  = array(
                        'id'     => $arrayData['idLogin'],
                        'login'  => $arrayData['login'],
                    );
                }
            }

            //se formula el json de respuesta
            $strJsonResultado = '{"total":"' . count($arrayResult) . '","registros":' . json_encode($arrayResult) . '}';
        }
        catch (\Exception $e)
        {
            $strJsonResultado   = '{"total":0, "registros":[], "error":"' . $e->getMessage() . '"}';
            $serviceUtil->insertError('Telcos+',
                                      'ClienteController.getAjaxLoginsPorRazonSocialAction',
                                      $e->getMessage(),
                                      $strUsrSesion,
                                      $strIpClient
                                    );
        }

        $objResponse = new JsonResponse();
        $objResponse->setContent($strJsonResultado);

        return $objResponse;
    }

    /**
     * Documentación para el método 'getDatosServiciosPorRazonSocial'.
     *
     * Obtiene el listado de los servicios por cliente
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 08-05-2020
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.1 11-05-2020 - Se agregan las validaciones para el tipo de proceso reactivar cliente
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.2 27-05-2020 - Se modifica los tipos procesos: CortarCliente -> CortarClienteTN y ReactivarCliente -> ReactivarClienteTN
     *
     * @param Array $arrayParametros [
     *                                  intIdEmpresa          => id de la empresa
     *                                  intIdPerEmpRol        => id del cliente
     *                                  intIdPunto            => id del punto
     *                                  strLogin              => login del punto
     *                                  arrayIdServicios      => array de id de servicios
     *                                  strEstadoPunto        => estado del punto
     *                                  strEstadoServicio     => estado de los servicios
     *                                  strTipoProcesoCab     => tipo del proceso masivo cab
     *                                  strEstadoMasivoCab    => estado del proceso masivo cab
     *                                  strEstadoMasivoDet    => estado del proceso masivo det
     *                                  strUsrSesion          => nombre usuario en sesión
     *                                  strIpClient           => ip de sesión
     *                               ]
     *
     * @return Array $arrayResultado [
     *                                   'status'    => estado de respuesta de la operación 'OK' o 'ERROR',
     *                                   'result'    => arreglo con la información de los servicios o mensaje de error
     *                               ]
     */
    public function getDatosServiciosPorRazonSocial($arrayParametros)
    {
        $serviceUtil  = $this->get('schema.Util');
        $emComercial  = $this->getDoctrine()->getManager();
        $emGeneral    = $this->getDoctrine()->getManager("telconet_general");

        $strUsrSesion       = $arrayParametros['strUsrSesion'];
        $strIpClient        = $arrayParametros['strIpClient'];
        $intIdEmpresa       = $arrayParametros['intIdEmpresa'];
        $intIdPerEmpRol     = $arrayParametros['intIdPerEmpRol'];
        $intIdPunto         = $arrayParametros['intIdPunto'];
        $arrayIdServicios   = isset($arrayParametros['arrayIdServicios']) ? $arrayParametros['arrayIdServicios'] : null;
        $strLogin           = isset($arrayParametros['strLogin']) ? $arrayParametros['strLogin'] : null;
        $strEstadoPunto     = $arrayParametros['strEstadoPunto'];
        $strEstadoServicio  = $arrayParametros['strEstadoServicio'];
        $strTipoProcesoCab  = $arrayParametros['strTipoProcesoCab'];
        $strEstadoMasivoCab = $arrayParametros['strEstadoMasivoCab'];
        $strEstadoMasivoDet = $arrayParametros['strEstadoMasivoDet'];

        try
        {
            //seteo el arreglo de los resultados
            $arrayResult      = array();

            //verifico el tipo de proceso para setear la variable
            if($strTipoProcesoCab == "CortarClienteTN")
            {
                $strNombreProcesoDet = 'CORTE_MASIVO';
            }
            elseif($strTipoProcesoCab == "ReactivarClienteTN")
            {
                $strNombreProcesoDet = 'REACTIVAR_MASIVO';
            }

            $objInfoPerEmpRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intIdPerEmpRol);
            //verifico si existe el cliente
            if( !is_object($objInfoPerEmpRol) )
            {
                throw new \Exception("No se encuentra el cliente, por favor notificar a Sistemas.");
            }

            //seteo el arreglo de los nombres técnicos de los servicios de la condición uno y dos
            $arrayNombreTecCondUno  = array();
            $arrayNombreTecCondDos  = array();

            //obtengo la cabecera de los nombres técnicos de los servicios para el corte masivo
            $objAdmiParametroCab    = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')->findOneBy(
                                                    array(  'nombreParametro'   => 'NOMBRES_TECNICO_SERVICIOS_CORTE_REACTIVAR_MASIVO',
                                                            'estado'            => 'Activo'));
            if( !is_object($objAdmiParametroCab) )
            {
                throw new \Exception('No se encontraron los nombres técnicos de los servicios para el corte masivo, '.
                                     'por favor notificar a Sistemas.');
            }
            $arrayParametrosCondUno = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findBy(
                                                    array(  "parametroId" => $objAdmiParametroCab->getId(),
                                                            "valor1"      => $strNombreProcesoDet,
                                                            "valor2"      => "CONDICION_UNO",
                                                            "estado"      => "Activo"));
            foreach($arrayParametrosCondUno as $objParametro)
            {
                $arrayNombreTecCondUno[] = $objParametro->getValor3();
            }
            $arrayParametrosCondDos = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findBy(
                                                    array(  "parametroId" => $objAdmiParametroCab->getId(),
                                                            "valor1"      => $strNombreProcesoDet,
                                                            "valor2"      => "CONDICION_DOS",
                                                            "estado"      => "Activo"));
            foreach($arrayParametrosCondDos as $objParametro)
            {
                $arrayNombreTecCondDos[] = $objParametro->getValor3();
            }

            //verifico si los arreglos de los nombres técnicos están vacíos
            if(count($arrayNombreTecCondUno) == 0 && count($arrayNombreTecCondDos) == 0)
            {
                throw new \Exception("No se obtuvieron los nombres técnicos para la búsqueda de los servicios, ".
                                     "por favor notificar a Sistemas.");
            }

            //seteo el arreglo de los id de los productos no permitidos para el corte masivo
            $arrayIdProductosNoPer  = array();

            //obtengo la cabecera de los nombres técnicos de los servicios para el corte masivo
            $objAdmiParametroCabPro = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')->findOneBy(
                                                    array(  'nombreParametro'   => 'PRODUCTOS_NO_PERMITIDOS_CORTE_REACTIVAR_MASIVO',
                                                            'estado'            => 'Activo'));
            if( is_object($objAdmiParametroCabPro) )
            {
                $arrayParametrosDetPro = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findBy(
                                                        array(  "parametroId" => $objAdmiParametroCabPro->getId(),
                                                                "valor1"      => $strNombreProcesoDet,
                                                                "estado"      => "Activo"));
                foreach($arrayParametrosDetPro as $objParametro)
                {
                    $arrayIdProductosNoPer[] = $objParametro->getValor2();
                }
            }

            $arrayDatosParametros  = array(
                    'intIdEmpresa'          => $intIdEmpresa,
                    'intIdPerEmpRol'        => $objInfoPerEmpRol->getId(),
                    'intIdPunto'            => $intIdPunto,
                    'strLogin'              => $strLogin,
                    'arrayIdServicios'      => $arrayIdServicios,
                    'strTipoEnlace'         => 'PRINCIPAL',
                    'strGrupo'              => 'DATACENTER',
                    'arrayNombreTecCondUno' => $arrayNombreTecCondUno,
                    'arrayNombreTecCondDos' => $arrayNombreTecCondDos,
                    'arrayIdProductosNoPer' => $arrayIdProductosNoPer,
                    'strEstadoPunto'        => $strEstadoPunto,
                    'strEstadoServicio'     => $strEstadoServicio,
                    'strTipoProcesoCab'     => $strTipoProcesoCab,
                    'strEstadoMasivoCab'    => $strEstadoMasivoCab,
                    'strEstadoMasivoDet'    => $strEstadoMasivoDet
            );
            $arrayDatosServicios = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')->getServiciosPorClienteTN($arrayDatosParametros);
            if( $arrayDatosServicios['status'] == 'OK' )
            {
                foreach($arrayDatosServicios['result'] as $arrayDatos)
                {
                    if( !isset($arrayDatos['isMasivo']) || $arrayDatos['isMasivo'] == 'NO' )
                    {
                        //agrego los servicios al arreglo
                        $arrayResult[] = $arrayDatos;
                    }
                }
                $arrayResultado = array(
                    'status' => 'OK',
                    'result' => $arrayResult
                );
            }
            else
            {
                throw new \Exception($arrayDatosServicios['result']);
            }
        }
        catch (\Exception $e)
        {
            $arrayResultado = array(
                'status' => 'ERROR',
                'result' => $e->getMessage()
            );
            $serviceUtil->insertError('Telcos+',
                                      'ClienteController.getDatosServiciosPorRazonSocial',
                                      $e->getMessage(),
                                      $strUsrSesion,
                                      $strIpClient
                                    );
        }

        return $arrayResultado;
    }

    /**
     * @Secure(roles="ROLE_151-7357")
     *
     * Documentación para el método 'generarReactivarMasivoTNAction'.
     *
     * Renderiza la pantalla para generar la reactivación masiva TN
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 11-05-2020
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.1 27-05-2020 - Se modifica el tipo proceso ReactivarCliente -> ReactivarClienteTN
     *
     * @return Render Pantalla Reactivar Corte Masivo TN.
     */
    public function generarReactivarMasivoTNAction()
    {
        $objRequest   = $this->getRequest();
        $objSession   = $objRequest->getSession();
        $strPrefEmpre = $objSession->get('prefijoEmpresa');
        $arrayCliente = $objSession->get('cliente');
        $emComercial  = $this->getDoctrine()->getManager();
        $emGeneral    = $this->getDoctrine()->getManager("telconet_general");
        //seteo el tipo de proceso
        $strTipoProceso = 'ReactivarClienteTN';
        //verifico si la empresa es TN
        if( $strPrefEmpre != 'TN' )
        {
            throw $this->createNotFoundException('La empresa en sesión es la incorrecta, debe ser Telconet.');
        }
        //seteo las variables del cliente
        $booleanPerEmp  = false;
        $strRazonSocial = null;
        $intIdPerEmpRol = null;
        $strTipoIdentificacion  = '';
        $strIdentificacion      = '';
        $intMaxServiciosAgregar = 100;
        //verifico si existe un login en session
        if( !empty($arrayCliente) && isset($arrayCliente['id_persona_empresa_rol']) && isset($arrayCliente['id_persona']) )
        {
            $booleanPerEmp  = true;
            $intIdPerEmpRol = $arrayCliente['id_persona_empresa_rol'];
            $objInfoPersona = $emComercial->getRepository('schemaBundle:InfoPersona')->find($arrayCliente['id_persona']);
            $strRazonSocial = $objInfoPersona->getRazonSocial();
            $strTipoIdentificacion = $objInfoPersona->getTipoIdentificacion();
            $strIdentificacion     = $objInfoPersona->getIdentificacionCliente();
        }
        //obtengo el maximo de servicios agregados para el corte masivo
        $objParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')->findOneBy(
                                                array(  'nombreParametro'   => 'MAXIMO_SERVICIOS_AGREGADOS_CORTE_REACTIVAR_MASIVO',
                                                        'estado'            => 'Activo'));
        if( is_object($objParametroCab) )
        {
            $objParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findOneBy(
                                                    array(  "parametroId" => $objParametroCab->getId(),
                                                            "valor1"      => "REACTIVAR_MASIVO",
                                                            "estado"      => "Activo"));
            if( is_object($objParametroDet) )
            {
                $intMaxServiciosAgregar = $objParametroDet->getValor2();
            }
        }

        return $this->render('comercialBundle:cliente:generarCorteReactivarMasivo.html.twig', array(
                'strTipoProceso'         => $strTipoProceso,
                'booleanPerEmp'          => $booleanPerEmp,
                'strRazonSocial'         => $strRazonSocial,
                'intIdper'               => $intIdPerEmpRol,
                'intMaxServiciosAgregar' => $intMaxServiciosAgregar,
                'strTipoIdentificacion'  => $strTipoIdentificacion,
                'strIdentificacion'      => $strIdentificacion
        ));
    }

            

    /**
     * Documentación para el función 'reactivarClienteAction'.
     *
     * Función que crea tarea y solicitud.
     *
     * @return $objJsonResponse  - Respuesta de confirmación.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 11-11-2020
     *
     */
    public function reactivarClienteAction()
    {
        try
        {
            $objRequest             = $this->getRequest();
            $strNombreClt           = $objRequest->request->get('strNombreClt');
            $intIdPersona           = $objRequest->request->get('intIdPersona');
            $strLogin               = $objRequest->request->get('strLogin');
            $intIdPersonaRol        = $objRequest->request->get('intIdPersonaRol');
            $strSaldoPendiente      = $objRequest->request->get('strSaldoPendiente');
            $strPago                = $objRequest->request->get('strPago');
            $strAcuerdoPago         = $objRequest->request->get('strAcuerdoPago');
            $objSession             = $objRequest->getSession();
            $strUsrCreacion         = $objSession->get('user');
            $strIpCreacion          = $objRequest->getClientIp();
            $intIdEmpresa           = $objSession->get('idEmpresa')      ? $objSession->get('idEmpresa'):"";
            $strPrefijoEmpresa      = $objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa'):"";
            $intIdOficinaSesion     = $objSession->get('idOficina')      ? $objSession->get('idOficina') : 0;
            $objJsonResponse        = new JsonResponse();
            $emComercial            = $this->getDoctrine()->getManager();
            $serviceUtil            = $this->get('schema.Util');
            $emGeneral              = $this->get('doctrine')->getManager('telconet');
            $objSoporteService      = $this->get('soporte.SoporteService');
            $serviceCliente         = $this->get('comercial.Cliente');
            $servicePreCliente      = $this->get('comercial.PreCliente');
            $strMensaje             = "";
            $strLoginCobranza       = "";
            $boolSuccess            = true;
            $strMensajeSolicitud    = "Deuda total: $".$strSaldoPendiente.
                                      ", Va a pagar: ".$strPago.", Acuerdo de pago: ".$strAcuerdoPago;
            $boolMostrarMensajeUs   = false;
            if(empty($strSaldoPendiente))
            {
                throw new \Exception('El valor del saldo es un campo obligatorio.');
            }
            $objInfoPersonaClt = $emComercial->getRepository("schemaBundle:InfoPersona")
                                                ->find($intIdPersona);
            if(empty($objInfoPersonaClt) || !is_object($objInfoPersonaClt))
            {
                throw new \Exception('Los filtros para encontrar al cliente son incorrectos');
            }
            /**
             * Bloque que valida si la persona o empresa tiene un rol de pre-cliente activo o pendiente.
             */
            $strDescRoles    = array ('Pre-cliente');
            $strEstados      = array ('Activo','Pendiente'); 
            $arrayPreCliente = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                           ->buscaClientesPorIdentificacionTipoRolEmpresaEstados($objInfoPersonaClt->getIdentificacionCliente(),
                                                                                                 $strDescRoles,
                                                                                                 $intIdEmpresa,
                                                                                                 $strEstados);
            if(!empty($arrayPreCliente) && is_array($arrayPreCliente))
            {
                $boolMostrarMensajeUs = true;
                throw new \Exception('Persona o Empresa ya existe como Pre-cliente.');
            }
            /**
             * Bloque que valida el valor del saldo pendiente del cliente:
             * Si es menor a $100.
             *  -Crea tarea a cobranza.
             *  -Crea Pre-cliente.
             * Si es mayor a $100.
             *  -Crea tarea a cobranza.
             *  -Crea Solicitud.
             */
            if($strSaldoPendiente>0 && $strSaldoPendiente<100)
            {
                $strMensaje           = "Se creó el pre-cliente correctamente.";
                $arrayDatosPreCliente = $serviceCliente->obtenerDatosClientePorIdentificacion($intIdEmpresa,
                                                                                              $objInfoPersonaClt->getIdentificacionCliente(),
                                                                                              $strPrefijoEmpresa);
                if(empty($arrayDatosPreCliente) || !is_array($arrayDatosPreCliente))
                {
                    throw new \Exception('No existen datos del cliente.');
                }
                $arrayFormaContacto = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                  ->findBy(array("personaId"=>$objInfoPersonaClt->getId()));
                if(empty($arrayFormaContacto) || !is_array($arrayFormaContacto))
                {
                    throw new \Exception('No existen formas de contactos del cliente.');
                }
                foreach($arrayFormaContacto as $arrayItem)
                {
                    $arrayFormaContactoPreclt[] = array('formaContacto' => $arrayItem->getFormaContactoId()->getDescripcionFormaContacto(),
                                                        'valor'         => $arrayItem->getValor());
                }
                $arrayDatosPreCliente["origen_web"]           = "S";
                $arrayDatosPreCliente['strOpcionPermitida']   = 'NO';
                $arrayDatosPreCliente['strNombrePais']        = 'ECUADOR';
                $arrayDatosPreCliente['intIdPais']            =  1;
                $arrayDatosPreCliente['yaexiste']             =  'S';
                $arrayDatosPreCliente['fechaNacimiento']      = $objInfoPersonaClt->getFechaNacimiento();
                $arrayDatosPreCliente['origenIngresos']       = $objInfoPersonaClt->getOrigenIngresos();
                $arrayDatosPreCliente['idOficinaFacturacion'] = $intIdOficinaSesion;

                $arrayParametrosPreCliente = array('strCodEmpresa'        => $intIdEmpresa,
                                                   'strUsrCreacion'       => $strUsrCreacion,
                                                   'strClientIp'          => $strIpCreacion,
                                                   'arrayDatosForm'       => $arrayDatosPreCliente,
                                                   'strPrefijoEmpresa'    => $strPrefijoEmpresa,
                                                   'arrayFormasContacto'  => $arrayFormaContactoPreclt);
                $objPersonaEmpresaRol =  $servicePreCliente->crearPreCliente($objInfoPersonaClt,$arrayParametrosPreCliente);
                /**
                 * Bloque que crea la tarea al o los usuarios de cobranza.
                 */
                $strDepartamento    = "";
                $arrayDepartamentos = $emComercial->getRepository('schemaBundle:AdmiDepartamento')
                                                  ->getDepartamentosPorLogin(array("strLogin"              => $strLogin,
                                                                                   "intIdEmpresa"          => $intIdEmpresa,
                                                                                   "strEstadoDepartamento" => "Activo"));
                if(empty($arrayDepartamentos['registros']) || !is_array($arrayDepartamentos))
                {
                    throw new \Exception("No existe departamento en estado activo");
                }
                $arrayItemDepartamentos = $arrayDepartamentos['registros'];
                $arrayTarea             = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->get('PARAMETROS_SOLICITUD_REACTIVACION', 
                                                          'COMERCIAL',
                                                          '',
                                                          'TAREA_PROCESO_INFORMATIVO',
                                                          '',
                                                          '',
                                                          '',
                                                          '',
                                                          '',
                                                          $intIdEmpresa);
                if(empty($arrayTarea) || !is_array($arrayTarea))
                {
                    throw new \Exception("No existe tarea y proceso");
                }
                $objInfoPersona = $emComercial->getRepository("schemaBundle:InfoPersona")
                                                ->findOneByLogin($strUsrCreacion);

                if(!is_object($objInfoPersona) || !in_array($objInfoPersona->getEstado(), array('Activo','Pendiente','Modificado')))
                {
                    throw new \Exception('El usuario de creación no existe en telcos o no se encuentra Activo.');
                }
                $strUsuarioAsigna  = $objInfoPersona->getNombres()." ".$objInfoPersona->getApellidos();
                $arrayEtadoPersona = array('Activo','Pendiente','Modificado');
                $strDepartamento   = $arrayItemDepartamentos[0]["NOMBRE_DEPARTAMENTO"];
                $arrayDatosPersona = $emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                                 ->getInfoDatosPersona(array ('strRol'                     => 'Empleado',
                                                                              'strPrefijo'                 => $strPrefijoEmpresa,
                                                                              'strEstadoPersona'           => $arrayEtadoPersona,
                                                                              'strEstadoPersonaEmpresaRol' => 'Activo',
                                                                              'strDepartamento'            => $strDepartamento,
                                                                              'strLogin'                   => $strLogin));
                if(empty($arrayDatosPersona) || !is_array($arrayDatosPersona) ||
                (isset($arrayDatosPersona['status']) && $arrayDatosPersona['status'] === 'fail') ||
                ($arrayDatosPersona['status'] === 'ok' && empty($arrayDatosPersona['result'])))
                {
                    throw new \Exception('Error al obtener los datos del asignado, por favor comunicar a Sistemas.');
                }
                $strObservacionTarea = "Cliente: ".$strNombreClt.", Identificación: ".
                                       $objInfoPersonaClt->getIdentificacionCliente().
                                       ", solicita nuevos servicios con la siguiente información: ".$strMensajeSolicitud;
                $arrayParametros = array ('intIdPersonaEmpresaRol' => $arrayDatosPersona['result'][0]['idPersonaEmpresaRol'],
                                            'intIdEmpresa'           => $arrayDatosPersona['result'][0]['idEmpresa'],
                                            'strPrefijoEmpresa'      => $arrayDatosPersona['result'][0]['prefijoEmpresa'],
                                            'strNombreTarea'         => $arrayTarea[0]["valor2"],
                                            'strNombreProceso'       => $arrayTarea[0]["valor1"],
                                            'strObservacionTarea'    => $strObservacionTarea,
                                            'strMotivoTarea'         => $strObservacionTarea,
                                            'strTipoAsignacion'      => 'empleado',
                                            'strIniciarTarea'        => "S",
                                            'strTipoTarea'           => 'T',
                                            'strTareaRapida'         => 'N',
                                            'strFechaHoraSolicitada' => date("Y-m-d").' '.date("H:i:s"),
                                            'boolAsignarTarea'       => true,
                                            "strAplicacion"          => 'telcoSys',
                                            'strUsuarioAsigna'       => $strUsuarioAsigna,
                                            'strUserCreacion'        => $strUsrCreacion,
                                            'strIpCreacion'          => $strIpCreacion);
                $arrayRespuesta  = $objSoporteService->crearTareaCasoSoporte($arrayParametros);
                if(empty($arrayRespuesta) || !is_array($arrayRespuesta) ||
                (isset($arrayRespuesta['mensaje']) && $arrayRespuesta['mensaje'] === 'fail'))
                {
                    throw new \Exception('Error al crear la tarea, por favor comunicar a Sistemas.');
                }
            }
            else
            {
                $strMensaje = "Se generó la solicitud correctamente.";
                /**
                 * Bloque que valida si el cliente tiene una solicitud en proceso.
                 */
                $arrayParametrosSol                      = array();
                $arrayParametrosSol['intIdEmpresa']      = $intIdEmpresa;
                $arrayParametrosSol['strTipoSolicitud']  = "SOLICITUD DE REACTIVACION";
                $arrayParametrosSol['strCaracClt']       = "REFERENCIA_CLIENTE";
                $arrayParametrosSol['strCaracTarea']     = "REFERENCIA_TAREA";
                $arrayParametrosSol['strCaracUsuario']   = "REFERENCIA_USUARIO";
                $arrayParametrosSol['strCaracSaldoP']    = "REFERENCIA_SALDO_P";
                $arrayParametrosSol['strCaracSaldoR']    = "REFERENCIA_SALDO_R";
                $arrayParametrosSol['strEstadoNotIn']    = "Rechazada";
                $arrayParametrosSol['strPrefijoEmpresa'] = $strPrefijoEmpresa;
                $arrayParametrosSol['strIdentificacion'] = $objInfoPersonaClt->getIdentificacionCliente();
                $arrayResultado                          = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                       ->getSolicitudReactivacion($arrayParametrosSol);
                if(!empty($arrayResultado["error"]) && isset($arrayResultado["error"]))
                {
                    throw new \Exception($arrayResultado["error"]);
                }
                if(!empty($arrayResultado["total"]) && isset($arrayResultado["total"]) && $arrayResultado["total"] > 0)
                {
                    $boolMostrarMensajeUs = true;
                    throw new \Exception("Estimado Usuario ya existe una solicitud en proceso.");
                }
                /**
                 * Bloque que crea la tarea al o los usuarios de cobranza.
                 */
                $arrayListUsuarioCobranza = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                      ->get('PARAMETROS_SOLICITUD_REACTIVACION', 
                                                            'COMERCIAL', 
                                                            '', 
                                                            'LISTADO_USUARIOS', 
                                                            '', 
                                                            '', 
                                                            $strLogin, 
                                                            '', 
                                                            'no', 
                                                            $intIdEmpresa);
                if(empty($arrayListUsuarioCobranza) || !is_array($arrayListUsuarioCobranza))
                {
                    throw new \Exception("No existe listado de usuario");
                }
                foreach($arrayListUsuarioCobranza as $arrayItemUsCobranza)
                {
                    $strDepartamento    = "";
                    $strLoginCobranza   = $arrayItemUsCobranza['valor1'];
                    $arrayDepartamentos = $emComercial->getRepository('schemaBundle:AdmiDepartamento')
                                                      ->getDepartamentosPorLogin(array("strLogin"              => $arrayItemUsCobranza['valor1'],
                                                                                       "intIdEmpresa"          => $intIdEmpresa,
                                                                                       "strEstadoDepartamento" => "Activo"));
                    if(empty($arrayDepartamentos['registros']) || !is_array($arrayDepartamentos))
                    {
                        throw new \Exception("No existe departamento en estado activo");
                    }
                    $arrayItemDepartamentos = $arrayDepartamentos['registros'];
                    $arrayTarea             = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->get('PARAMETROS_SOLICITUD_REACTIVACION', 
                                                              'COMERCIAL',
                                                              '',
                                                              'TAREA_PROCESO',
                                                              '',
                                                              '',
                                                              '',
                                                              '',
                                                              '',
                                                              $intIdEmpresa);
                    if(empty($arrayTarea) || !is_array($arrayTarea))
                    {
                        throw new \Exception("No existe tarea y proceso");
                    }
                    $objInfoPersona = $emComercial->getRepository("schemaBundle:InfoPersona")
                                                  ->findOneByLogin($strUsrCreacion);

                    if(!is_object($objInfoPersona) || !in_array($objInfoPersona->getEstado(), array('Activo','Pendiente','Modificado')))
                    {
                        throw new \Exception('El usuario de creación no existe en telcos o no se encuentra Activo.');
                    }
                    $strUsuarioAsigna  = $objInfoPersona->getNombres()." ".$objInfoPersona->getApellidos();
                    $arrayEtadoPersona = array('Activo','Pendiente','Modificado');
                    $strDepartamento   = $arrayItemDepartamentos[0]["NOMBRE_DEPARTAMENTO"];
                    $arrayDatosPersona = $emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                                     ->getInfoDatosPersona(array ('strRol'                    => 'Empleado',
                                                                                  'strPrefijo'                 => $strPrefijoEmpresa,
                                                                                  'strEstadoPersona'           => $arrayEtadoPersona,
                                                                                  'strEstadoPersonaEmpresaRol' => 'Activo',
                                                                                  'strDepartamento'            => $strDepartamento,
                                                                                  'strLogin'                   => $arrayItemUsCobranza['valor1']));
                    if(empty($arrayDatosPersona) || !is_array($arrayDatosPersona) ||
                    (isset($arrayDatosPersona['status']) && $arrayDatosPersona['status'] === 'fail') ||
                    ($arrayDatosPersona['status'] === 'ok' && empty($arrayDatosPersona['result'])))
                    {
                        throw new \Exception('Error al obtener los datos del asignado, por favor comunicar a Sistemas.');
                    }
                    $strObservacionTarea = "Cliente: ".$strNombreClt.", Identificación: ".
                                            $objInfoPersonaClt->getIdentificacionCliente().
                                            ", solicita nuevos servicios con la siguiente información: ".$strMensajeSolicitud;
                    $arrayParametros = array ('intIdPersonaEmpresaRol' => $arrayDatosPersona['result'][0]['idPersonaEmpresaRol'],
                                              'intIdEmpresa'           => $arrayDatosPersona['result'][0]['idEmpresa'],
                                              'strPrefijoEmpresa'      => $arrayDatosPersona['result'][0]['prefijoEmpresa'],
                                              'strNombreTarea'         => $arrayTarea[0]["valor2"],
                                              'strNombreProceso'       => $arrayTarea[0]["valor1"],
                                              'strObservacionTarea'    => $strObservacionTarea,
                                              'strMotivoTarea'         => $strObservacionTarea,
                                              'strTipoAsignacion'      => 'empleado',
                                              'strIniciarTarea'        => "S",
                                              'strTipoTarea'           => 'T',
                                              'strTareaRapida'         => 'N',
                                              'strFechaHoraSolicitada' => date("Y-m-d").' '.date("H:i:s"),
                                              'boolAsignarTarea'       => true,
                                              "strAplicacion"          => 'telcoSys',
                                              'strUsuarioAsigna'       => $strUsuarioAsigna,
                                              'strUserCreacion'        => $strUsrCreacion,
                                              'strIpCreacion'          => $strIpCreacion);
                    $arrayRespuesta  = $objSoporteService->crearTareaCasoSoporte($arrayParametros);
                    if(empty($arrayRespuesta) || !is_array($arrayRespuesta) ||
                    (isset($arrayRespuesta['mensaje']) && $arrayRespuesta['mensaje'] === 'fail'))
                    {
                        throw new \Exception('Error al crear la tarea, por favor comunicar a Sistemas.');
                    }
                    /**
                     * Bloque que crea la solicitud al usuario asignado.
                     */
                    $objAdmiCaracClt = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                   ->findOneBy(array("descripcionCaracteristica" => "REFERENCIA_CLIENTE",
                                                                     "estado"                    => "Activo"));
                    if(!is_object($objAdmiCaracClt) && empty($objAdmiCaracClt))
                    {
                        throw new \Exception("No existe Objeto para la característica REFERENCIA_CLIENTE");
                    }
                    $objAdmiCaracUs = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                  ->findOneBy(array("descripcionCaracteristica" => "REFERENCIA_USUARIO",
                                                                    "estado"                    => "Activo"));
                    if(!is_object($objAdmiCaracUs) && empty($objAdmiCaracUs))
                    {
                        throw new \Exception("No existe Objeto para la característica REFERENCIA_USUARIO");
                    }
                    $objAdmiCaracUsC = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                   ->findOneBy(array("descripcionCaracteristica" => "REFERENCIA_USUARIO_COBRANZA",
                                                                     "estado"                    => "Activo"));
                    if(!is_object($objAdmiCaracUsC) && empty($objAdmiCaracUsC))
                    {
                        throw new \Exception("No existe Objeto para la característica REFERENCIA_USUARIO_COBRANZA");
                    }
                    $objAdmiCaracTarea = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                     ->findOneBy(array("descripcionCaracteristica" => "REFERENCIA_TAREA",
                                                                       "estado"                    => "Activo"));
                    if(!is_object($objAdmiCaracTarea) && empty($objAdmiCaracTarea))
                    {
                        throw new \Exception("No existe Objeto para la característica REFERENCIA_TAREA");
                    }
                    $objAdmiCaracSaldoPend = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                         ->findOneBy(array("descripcionCaracteristica" => "REFERENCIA_SALDO_P",
                                                                           "estado"                    => "Activo"));
                    if(!is_object($objAdmiCaracSaldoPend) && empty($objAdmiCaracSaldoPend))
                    {
                        throw new \Exception("No existe Objeto para la característica REFERENCIA_SALDO_P");
                    }
                    $objTipoSolicitudReactivacion = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                                ->findOneBy(array("descripcionSolicitud" => "SOLICITUD DE REACTIVACION",
                                                                                  "estado"               => "Activo"));
                    if(!is_object($objTipoSolicitudReactivacion) && empty($objTipoSolicitudReactivacion))
                    {
                        throw new \Exception("No existe Objeto para el tipo de Solicitud de Reactivación");
                    }
                    $objDetTipoSolReactivacion= new InfoDetalleSolicitud();
                    $objDetTipoSolReactivacion->setTipoSolicitudId($objTipoSolicitudReactivacion);
                    $objDetTipoSolReactivacion->setObservacion($strMensajeSolicitud);
                    $objDetTipoSolReactivacion->setFeCreacion(new \DateTime('now'));
                    $objDetTipoSolReactivacion->setUsrCreacion($strUsrCreacion);
                    $objDetTipoSolReactivacion->setEstado('Pendiente');
                    $emComercial->persist($objDetTipoSolReactivacion);
                    $emComercial->flush();
                    $objDetTipoSolReactivacionHist = new InfoDetalleSolHist();
                    $objDetTipoSolReactivacionHist->setDetalleSolicitudId($objDetTipoSolReactivacion);
                    $objDetTipoSolReactivacionHist->setEstado($objDetTipoSolReactivacion->getEstado());
                    $objDetTipoSolReactivacionHist->setFeCreacion(new \DateTime('now'));
                    $objDetTipoSolReactivacionHist->setUsrCreacion($strUsrCreacion);
                    $objDetTipoSolReactivacionHist->setObservacion($strMensajeSolicitud);
                    $objDetTipoSolReactivacionHist->setIpCreacion($strIpCreacion);
                    $emComercial->persist($objDetTipoSolReactivacionHist);
                    $emComercial->flush();
                    $objDetTipoSolReactivacionCarac = new InfoDetalleSolCaract();
                    $objDetTipoSolReactivacionCarac->setCaracteristicaId($objAdmiCaracClt);
                    $objDetTipoSolReactivacionCarac->setValor($intIdPersona);
                    $objDetTipoSolReactivacionCarac->setDetalleSolicitudId($objDetTipoSolReactivacion);
                    $objDetTipoSolReactivacionCarac->setEstado($objDetTipoSolReactivacion->getEstado());
                    $objDetTipoSolReactivacionCarac->setFeCreacion(new \DateTime('now'));
                    $objDetTipoSolReactivacionCarac->setUsrCreacion($strUsrCreacion);
                    $emComercial->persist($objDetTipoSolReactivacionCarac);
                    $emComercial->flush();
                    $objDetTipoSolReactivacionCarac = new InfoDetalleSolCaract();
                    $objDetTipoSolReactivacionCarac->setCaracteristicaId($objAdmiCaracUs);
                    $objDetTipoSolReactivacionCarac->setValor($strLogin);
                    $objDetTipoSolReactivacionCarac->setDetalleSolicitudId($objDetTipoSolReactivacion);
                    $objDetTipoSolReactivacionCarac->setEstado($objDetTipoSolReactivacion->getEstado());
                    $objDetTipoSolReactivacionCarac->setFeCreacion(new \DateTime('now'));
                    $objDetTipoSolReactivacionCarac->setUsrCreacion($strUsrCreacion);
                    $emComercial->persist($objDetTipoSolReactivacionCarac);
                    $emComercial->flush();
                    $objDetTipoSolReactivacionCarac = new InfoDetalleSolCaract();
                    $objDetTipoSolReactivacionCarac->setCaracteristicaId($objAdmiCaracUsC);
                    $objDetTipoSolReactivacionCarac->setValor($strLoginCobranza);
                    $objDetTipoSolReactivacionCarac->setDetalleSolicitudId($objDetTipoSolReactivacion);
                    $objDetTipoSolReactivacionCarac->setEstado($objDetTipoSolReactivacion->getEstado());
                    $objDetTipoSolReactivacionCarac->setFeCreacion(new \DateTime('now'));
                    $objDetTipoSolReactivacionCarac->setUsrCreacion($strUsrCreacion);
                    $emComercial->persist($objDetTipoSolReactivacionCarac);
                    $emComercial->flush();
                    $strObservacionSolTarea = "Se crea tarea: ".$arrayRespuesta['numeroTarea']." para la solicitud";
                    $objDetTipoSolReactivacionHist = new InfoDetalleSolHist();
                    $objDetTipoSolReactivacionHist->setDetalleSolicitudId($objDetTipoSolReactivacion);
                    $objDetTipoSolReactivacionHist->setEstado($objDetTipoSolReactivacion->getEstado());
                    $objDetTipoSolReactivacionHist->setFeCreacion(new \DateTime('now'));
                    $objDetTipoSolReactivacionHist->setUsrCreacion($strUsrCreacion);
                    $objDetTipoSolReactivacionHist->setObservacion($strObservacionSolTarea);
                    $objDetTipoSolReactivacionHist->setIpCreacion($strIpCreacion);
                    $emComercial->persist($objDetTipoSolReactivacionHist);
                    $emComercial->flush();
                    $objDetTipoSolReactivacionCarac = new InfoDetalleSolCaract();
                    $objDetTipoSolReactivacionCarac->setCaracteristicaId($objAdmiCaracTarea);
                    $objDetTipoSolReactivacionCarac->setValor($arrayRespuesta['numeroTarea']);
                    $objDetTipoSolReactivacionCarac->setDetalleSolicitudId($objDetTipoSolReactivacion);
                    $objDetTipoSolReactivacionCarac->setEstado($objDetTipoSolReactivacion->getEstado());
                    $objDetTipoSolReactivacionCarac->setFeCreacion(new \DateTime('now'));
                    $objDetTipoSolReactivacionCarac->setUsrCreacion($strUsrCreacion);
                    $emComercial->persist($objDetTipoSolReactivacionCarac);
                    $emComercial->flush();
                    $objDetTipoSolReactivacionCarac = new InfoDetalleSolCaract();
                    $objDetTipoSolReactivacionCarac->setCaracteristicaId($objAdmiCaracSaldoPend);
                    $objDetTipoSolReactivacionCarac->setValor($strSaldoPendiente);
                    $objDetTipoSolReactivacionCarac->setDetalleSolicitudId($objDetTipoSolReactivacion);
                    $objDetTipoSolReactivacionCarac->setEstado($objDetTipoSolReactivacion->getEstado());
                    $objDetTipoSolReactivacionCarac->setFeCreacion(new \DateTime('now'));
                    $objDetTipoSolReactivacionCarac->setUsrCreacion($strUsrCreacion);
                    $emComercial->persist($objDetTipoSolReactivacionCarac);
                    $emComercial->flush();
                }
                if($emComercial->getConnection()->isTransactionActive())
                {
                    $emComercial->getConnection()->commit();
                    $emComercial->getConnection()->close();
                }
            }
        }
        catch(\Exception $ex)
        {
            if($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->rollback();
                $emComercial->getConnection()->close();
            }
            $boolSuccess = false;
            $strMensaje  ="Error al generar la solicitud de reactivación, por favor comunicar a Sistemas.";
            if($boolMostrarMensajeUs)
            {
                $boolSuccess = true;
                $strMensaje  = $ex->getMessage();
            }
            $serviceUtil->insertError('TelcoS+',
                                      'ClienteController.reactivarClienteAction',
                                      $ex->getMessage(),
                                      $strUsrCreacion,
                                      $strIpCreacion);
        }
        $objJsonResponse->setData(array('success' => $boolSuccess, 'msg' => $strMensaje));
        return $objJsonResponse;
    }

    /**
     * Documentación para la función 'getUsuarioSolicitudAction'.
     *
     * Función que retorna el listado de usuarios para asignar la solicitud.
     *
     * @return $objResponse - Listado de Usuarios.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 11-11-2020
     *
     */
    public function getUsuarioSolicitudAction()
    {
        try
        {
            $objRequest             = $this->getRequest();
            $objSession             = $objRequest->getSession();
            $intIdEmpresa           = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa'):"";
            $strUsrCreacion         = $objSession->get('user')      ? $objSession->get('user'):"";
            $strIpCreacion          = $objRequest->getClientIp()    ? $objRequest->getClientIp():'127.0.0.1';
            $strSaldoPendiente      = $objRequest->query->get('strSaldoPendiente');
            $arrayUsuario           = array();
            $emGeneral              = $this->get('doctrine')->getManager('telconet');
            $serviceUtil            = $this->get('schema.Util');
            $strEsUsCobranza        = (!empty($strSaldoPendiente) && $strSaldoPendiente > 100) ? "si":"no";
            $arrayListUsuario       = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->get('PARAMETROS_SOLICITUD_REACTIVACION', 
                                                      'COMERCIAL', 
                                                      '', 
                                                      'LISTADO_USUARIOS', 
                                                      '', 
                                                      '', 
                                                      '', 
                                                      '', 
                                                      $strEsUsCobranza, 
                                                      $intIdEmpresa);
            if(empty($arrayListUsuario) || !is_array($arrayListUsuario))
            {
                throw new \Exception("No existe listado de usuario");
            }
            foreach($arrayListUsuario as $arrayItem)
            {
                $arrayUsuario[] = array('login'       => $arrayItem['valor1'], 
                                        'descripcion' => $arrayItem['valor2']);
            }
        }
        catch(\Exception $ex)
        {
            $serviceUtil->insertError('TelcoS+',
                            'ClienteController.getUsuarioSolicitudAction',
                            $ex->getMessage(),
                            $strUsrCreacion,
                            $strIpCreacion);
        }
        $objResponse = new Response(json_encode(array('usuario' => $arrayUsuario)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
     
    /**
     * Documentación para la función 'ajaxMsValidaIdentificacionAction'.
     *
     * Función que retorna el data persona de base o equifax consumo de micro servicio .
     *
     * @author Jefferson Alexy Carrillo A <jacarrillo@telconet.ec> 
     * @version 1.3 01-08-2021 
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec> 
     * @version 1.4 18-02-2022 - Se soliclta enviar los datos de la persona en el caso de que exista información del cliente
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec> 
     * @version 1.5 18-02-2022 - Se solventa problemas al crear prospecto para la empresa TN
     * 
     * @author Jefferson Alexy Carrillo <jacarrillo@telconet.ec>
     * @version 1.6 10-08-2022 - Se elimina consumo de representante legal por nueva implemnetacion con ms
     *
     * @author Eduardo Enrique Vargas<eevargas@telconet.ec>
     * @version 1.7 14-06-2023 - Se solventa problema al validar ciertos RUC por su digito verificador
     *
    */
   public function ajaxMsValidaIdentificacionAction()
   {
       $objRequest = $this->getRequest(); 
       $strCodEmpresa         = $objRequest->getSession()->get('idEmpresa');
       $strUsuario            = $objRequest->getSession()->get('user'); 
       $strIpClient           = $objRequest->getClientIp(); 
       $strPrefijoEmpresa     = $objRequest->getSession()->get('prefijoEmpresa'); 
       $strIdPais             = $objRequest->getSession()->get('intIdPais');
       $strIdentificacion     = trim($objRequest->request->get("identificacion"));
       $strTipoIdentificacion = trim($objRequest->request->get("tipoIdentificacion"));

       /* @var $serviceCliente ClienteService */
       $serviceCliente = $this->get('comercial.Cliente');
       $emComercial     = $this->getDoctrine()->getManager();
       $strDistribuidor = "";


       if(!empty($strPrefijoEmpresa) && $strPrefijoEmpresa == "TN")
       {
           $arrayDistribuidor = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                            ->getDistribuidor(array("strIdentificacion" => $strIdentificacion,
                                                                    "strPrefijoEmpresa" => $strPrefijoEmpresa));
           if(empty($arrayDistribuidor["error"]) && isset($arrayDistribuidor["resultado"]) && 
               !empty($arrayDistribuidor["resultado"]))
           {
               foreach($arrayDistribuidor["resultado"] as $arrayItem)
               {
                   if($arrayItem["intCantidadServ"] > 0 && $arrayItem["intCantidadSolAprobada"] == 0)
                   {
                       $strDistribuidor = (!empty($arrayItem["strDistribuidor"]) && isset($arrayItem["strDistribuidor"]))?
                                           $arrayItem["strDistribuidor"]:"";
                   }
               }
               if(!empty($strDistribuidor))
               {
                   $arrayArreglo["strDistribuidor"] = $strDistribuidor; 
                   $arrayResponse = array(
                       'strStatus' => 'OK',
                       'strMensaje' => 'Se encontro la siguiente información de esta identificación.',
                       'objData' => $arrayArreglo
                   );
                   $objResponse  = new Response(json_encode( $arrayResponse , true ));
                   $objResponse->headers->set('Content-type', 'text/json');
                   return $objResponse;
               }
           }
       }
        //VALIDA LA IDENTIFICACIÓN 
        $arrayParamValidaIdentifica = array(
            'strTipoIdentificacion'     =>$strTipoIdentificacion,
            'strIdentificacionCliente'  => $strIdentificacion,
            'intIdPais'                 => $strIdPais,
            'strCodEmpresa'             => $strCodEmpresa
        );
        $strMensaje = $emComercial->getRepository('schemaBundle:InfoPersona')->validarIdentificacionTipo($arrayParamValidaIdentifica);
        
        /*Condición para corregir bug de ciertos No. de RUC de Sociedades privadas y extranjeros
        no residentes con problemas en la validación del digito verificador.*/
        if($strMensaje != null && $strMensaje == 'El digito verificador no es valido.' &&
            $strTipoIdentificacion== 'RUC' && substr($strIdentificacion, 2, 1) == 9)
        {
            $strMensaje = '';
        }//Fin condición

        if($strMensaje != null && $strMensaje != '')
        {
        $arrayResponse = array(
            'strStatus' => 'NO',
            'strMensaje' => $strMensaje ,
            'objData' => null
        );
        $objResponse  = new Response(json_encode( $arrayResponse , true ));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
        }  

        if ($strPrefijoEmpresa == "MD" || $strPrefijoEmpresa == "EN") 
        { 

            $arrayParametrosPersona = array(
                "empresaCod"=>   $strCodEmpresa,
                "prefijoEmPresa"=>$strPrefijoEmpresa,
                "identificacion"=>$strIdentificacion ,
                "tipoIdentificacion"=> $strTipoIdentificacion,
                "user"=> $strUsuario ,
                "origen"=>"Web",
                "requireRecomendacion"=>true

            ); 

            $serviceTokenCas = $this->get('seguridad.TokenCas');
            $arrayTokenCas   = $serviceTokenCas->generarTokenCas();  
            $arrayParametrosPersona['token'] = $arrayTokenCas['strToken']; 
            
        /* @var $servicePreCliente \telconet\comercialBundle\Service\PreClienteMsService */ 
            $servicePreClienteMs = $this->get('comercial.PreClienteMs');
            $arrayResponse     =  $servicePreClienteMs->wsPersonaProspecto($arrayParametrosPersona);
            if ($arrayResponse['strStatus']!='OK' ) 
            {  
                $arrayResponse['strStatus']='NO'; //si falla el servicio envia status FAIL para poder continuar con el flujo
                $objResponse  = new Response(json_encode( $arrayResponse , true ));
                $objResponse->headers->set('Content-type', 'text/json');
                return $objResponse;
            } 
            $objData           = $arrayResponse['objData']; 
            $objPersona        = $objData['persona'];  
            $objPersonaReferido= $objData['personaReferido']; 
            $boolRecomendado   = $objData['isRecomendado'];
            
        
            $strRoles= ""; 
            $arrayAdmiRoles=  $objData['admiRoles']; 
            if (!is_null($arrayAdmiRoles)) 
            {
                for ($intCont=0; $intCont < count($arrayAdmiRoles) ; $intCont++) 
                { 
                    $objItem= $arrayAdmiRoles[$intCont];
                    $strRol=  $objItem["descripcionRol"];
                    $strRoles=  $strRoles.$strRol."|"; 
                }
            }
            
            $strOriginalDate = $objPersona["fechaNacimiento"];
            if (!is_null($strOriginalDate)) 
            {
                $objPersona["fechaNacimiento"] = date("d/m/Y", strtotime($strOriginalDate));
            } 

        
            $objPersona["referidoId"]=0; 
            $objPersona["referidoNombre"]= null; 
            if (!is_null( $objPersonaReferido)) 
            {
                $objPersona["referidoId"]=$objPersonaReferido['idPersona']; 
                $objPersona["referidoNombre"]= $objPersonaReferido['nombres'].' '.$objPersonaReferido['apellidos']; 
            } 
            
            $objPersonaFormaPago= $objData['formaPago']; 
            if (!is_null( $objPersonaFormaPago)) 
            {
                $objPersona["formaPagoId"] =   $objPersonaFormaPago['formaPagoId'] ;  
                $objPersona["tipoCuentaId"] =   $objPersonaFormaPago['tipoCuentaId'] ;    
                $objPersona["bancoTipoCuentaId"] =   $objPersonaFormaPago['bancoTipoCuentaId'] ;  
            } 
            


            $arrayDataPersona =     array() ; 
            $arrayDataPersona["isRecomendado"]=   $boolRecomendado ; 
            $arrayDataPersona["id"]= $objPersona["idPersona"];   
            $arrayDataPersona["strDistribuidor"]= null;  
            $arrayDataPersona["roles"]= $strRoles; 
            $arrayDataPersona["nombres"]= $objPersona["nombres"]; 
            $arrayDataPersona["apellidos"]= $objPersona["apellidos"]; 
            $arrayDataPersona["razonSocial"]= $objPersona["razonSocial"];    
            $arrayDataPersona["tituloId"]= $objPersona["tituloId"]; 
            $arrayDataPersona["tipoIdentificacion"]= $objPersona["tipoIdentificacion"]; 
            $arrayDataPersona["tipoEmpresa"]=   $objPersona["tipoEmpresa"];  
            $arrayDataPersona["representanteLegal"]= $objPersona["representanteLegal"];  
            $arrayDataPersona["nacionalidad"]= strtoupper($objPersona["nacionalidad"]); 
            $arrayDataPersona["genero"]= strtoupper($objPersona["genero"]); 
            $arrayDataPersona["direccionTributaria"]  =strtoupper($objPersona["direccionTributaria"]); 
            $arrayDataPersona["estadoCivil"] =strtoupper($objPersona["estadoCivil"]);  
            $arrayDataPersona["origenIngresos"]= $objPersona["origenIngresos"];   
            $arrayDataPersona["fechaNacimiento"]= $objPersona["fechaNacimiento"];  
            $arrayDataPersona["identificacionCliente"]  =   $objPersona["identificacionCliente"] ;       
            $arrayDataPersona["tipoTributario"]  =   strtoupper($objPersona["tipoTributario"] );    
            $arrayDataPersona["estado"]  =  strtoupper($objPersona["estado"]);    
            $arrayDataPersona["cargo"]  =  strtoupper($objPersona["cargo"]);  

    
            $arrayDataPersona["nombreComercial"]     =$objPersona["nombreComercial"];  
            $arrayDataPersona["fechaInicioCompania"] =$objPersona["fechaIniciCompania"];    
            $arrayDataPersona["estadoLegal"]         =$objPersona["estadoLegal"];
            $arrayDataPersona["tipoCompania"]        =$objPersona["tipoCompania"];
            $arrayDataPersona["edad"]                =$objPersona["edad"];
                                        
            $arrayDataPersona["formaPagoId"]        =  $objPersona["formaPagoId"]  ;  
            $arrayDataPersona["tipoCuentaId"]       =  $objPersona["tipoCuentaId"] ;   
            $arrayDataPersona["bancoTipoCuentaId"]  =  $objPersona["bancoTipoCuentaId"];


            $arrayDataPersona["floatSaldoPendiente"] = 0;
            $arrayDataPersona["datosPersonaEmpresaRol"]  = null;   

            

            $arrayDirecciones = array(); 
            for ($intCont=0; $intCont < count( $objPersona["direcciones"]); $intCont++) 
            { 
            $strDato=  $objPersona["direcciones"][ $intCont ]["direccion"]; 
            $strDato = substr($strDato, 2); 
            $strDato = trim( $strDato); 
            array_push($arrayDirecciones , array( $strDato));
            }


            $arrayTelefonos = array(); 
            for ($intCont=0; $intCont < count( $objPersona["telefonos"]); $intCont++) 
            { 
            $strDato=   $objPersona["telefonos"][ $intCont ]["telefono"];  

            $strDato =explode(":", $strDato);
            $strDato = $strDato[1]; 
            $strDato = trim( $strDato); 
            array_push($arrayTelefonos , array( $strDato));
            }


            $arrayDataPersona["dataRecomendacion"]="{}";
            
            $arrayDataPersona["arrayRecomendoDirecciones"]= $arrayDirecciones ; 
            $arrayDataPersona["arrayRecomendoTelefonoFijo"]=    $arrayTelefonos;  

            $arrayDataPersona["arrayRecomendacionesTarjetaCredito"]= array(); 
            $arrayDataPersona["arrayRecomendacionesIngresos"]= array(); 

            $boolRecomendado = true ; //buscar siempre las recomendaciones
            if ( $boolRecomendado ) 
            {
        
            $serviceTokenCas = $this->get('seguridad.TokenCas');
            $arrayTokenCas   = $serviceTokenCas->generarTokenCas();   

            $arrayParametrosRecomendacion = array(   
                "token"=> $arrayTokenCas['strToken'],
                "opcion"=>"CONSULTA_RECOMENDACIONES_PERSONA",
                "comandoConfiguracion"=> "NO",
                "ejecutaComando"=> "NO",
                "actualiza_datos"=> "NO",
                "empresa" =>$strPrefijoEmpresa,
                "usrCreacion"=> $strUsuario ,
                "ipCreacion"=> $strIpClient ,
                "datos"=>array(
                    "identificacion"=>$strIdentificacion ,
                    "tipoIdentificacion"=> $strTipoIdentificacion,
                    "tipoTributario"=>  $arrayDataPersona["tipoTributario"] 
                    )
            ); 
    
    
            $objDataRecomendacion = $this-> obtenerRecomendacionesEquifax( $arrayParametrosRecomendacion );
            $strDataRecomendacion = str_replace( '"', '\"' ,json_encode( $objDataRecomendacion["objDataRecomendacion"])); 
            $arrayDataPersona["dataRecomendacion"] = $strDataRecomendacion; 
            $arrayDataPersona["arrayRecomendacionesTarjetaCredito"] =  $objDataRecomendacion["arrayRecomendacionesTarjetaCredito"]; 
            $arrayDataPersona["arrayRecomendacionesIngresos"]       =  $objDataRecomendacion["arrayRecomendacionesIngresos"] ;

            }


            $arrayResponse = array(
                'strStatus' => 'OK',
                'strMensaje' => 'Se encontro la siguiente información de esta identificación.',
                'objData' => $arrayDataPersona
            );
            $objResponse  = new Response(json_encode( $arrayResponse , true ));
            $objResponse->headers->set('Content-type', 'text/json');
            return $objResponse;

        }
        $floatSaldoPendiente = 0;
        if(!empty($strPrefijoEmpresa) && $strPrefijoEmpresa == "TN")
        {
            $arrayDataPersona= $serviceCliente->obtenerDatosClientePorIdentificacion($strCodEmpresa, $strIdentificacion, $strPrefijoEmpresa);
            if(empty($arrayDataPersona) || !is_array($arrayDataPersona))
            {
                $arrayResponse = array(
                    'strStatus' => 'NO',
                    'strMensaje' => $strMensaje ,
                    'objData' => null
                );
                $objResponse  = new Response(json_encode( $arrayResponse , true ));
                $objResponse->headers->set('Content-type', 'text/json');
                return $objResponse;
            }
            $strDescRoles    = array ('Cliente');
            $strEstados      = array ('Cancelado');
            $arrayEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                            ->buscaClientesPorIdentificacionTipoRolEmpresaEstados($strIdentificacion,
                                                                                                $strDescRoles,
                                                                                                $strCodEmpresa,
                                                                                                $strEstados);
            if(!empty($arrayEmpresaRol) && is_array($arrayEmpresaRol))
            {
                foreach($arrayEmpresaRol as $arrayItem)
                {
                    $arraySaldoPendiente = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                        ->getSaldoPorCliente(array("intIdPersonEmpresaRol" => $arrayItem->getId(),
                                                                                    "strPrefijoEmpresa"     => $strPrefijoEmpresa));
                    if(empty($arraySaldoPendiente["error"]) && isset($arraySaldoPendiente["floatSaldoPendiente"]) && 
                        !empty($arraySaldoPendiente["floatSaldoPendiente"]))
                    {
                        $floatSaldoPendiente += $floatSaldoPendiente + $arraySaldoPendiente["floatSaldoPendiente"];
                    }
                }
            }
        }



        $arrayDataPersona["floatSaldoPendiente"] = $floatSaldoPendiente;

                
        $arrayResponse = array(
            'strStatus' => 'OK',
            'strMensaje' => 'Se encontro la siguiente información de esta identificación.',
            'objData' => $arrayDataPersona
        );
        $objResponse  = new Response(json_encode( $arrayResponse , true ));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    
   }


  /**
     * @author Jefferson Alexy Carrillo Anchundia <jacarrillo@telconet.ec> 
     * @version 1.0 07-07-2021
     * #Consumo de ms   data equifax de recomendaciones para el cliente
     * 
     * @return JsonResponse $objRespuesta
     */
    public function ajaxVerificarRecomendacionesAction()  
    {
 
        $objRequest            = $this->getRequest(); 
        $strCodEmpresa         = $objRequest->getSession()->get('idEmpresa');
        $strUsuario            = $objRequest->getSession()->get('user'); 
        $strIpClient           = $objRequest->getClientIp(); 
        $strPrefijoEmpresa     = $objRequest->getSession()->get('prefijoEmpresa'); 

        $strIdentificacion     = trim($objRequest->get("identificacion"));
        $strTipoIdentificacion = trim($objRequest->get("tipoIdentificacion"));
        $strTipoTributario     = trim($objRequest->get("tipoTributario"));

        $serviceTokenCas = $this->get('seguridad.TokenCas');
        $arrayTokenCas   = $serviceTokenCas->generarTokenCas();   

        $arrayParametrosRecomendacion = array(   
            "token"=> $arrayTokenCas['strToken'],
            "opcion"=>"CONSULTA_RECOMENDACIONES_PERSONA",
            "comandoConfiguracion"=> "NO",
            "ejecutaComando"=> "NO",
            "actualiza_datos"=> "NO",
            "empresa" =>$strPrefijoEmpresa,
            "usrCreacion"=> $strUsuario ,
            "ipCreacion"=> $strIpClient ,
            "datos"=>array(
                "identificacion"=>$strIdentificacion ,
                "tipoIdentificacion"=> $strTipoIdentificacion,
                "tipoTributario"=> $strTipoTributario
                )
        ); 


        $objDataRecomendacion = $this->obtenerRecomendacionesEquifax( $arrayParametrosRecomendacion );

        $arrayResponse = array( 
            "strStatus" =>    $objDataRecomendacion['strStatus'],
            "strMensaje" =>    $objDataRecomendacion['strMensaje'], 
            'objData' =>  $objDataRecomendacion
        );

        $arrayResponse = new Response(json_encode( $arrayResponse ));
        $arrayResponse->headers->set('Content-type', 'text/json');
        return $arrayResponse;
    }


    public function  obtenerRecomendacionesEquifax($arrayParametrosRecomendacion)  
    {

        $arrayRecomendacionesTarjetaCredito  = array();
        $arrayRecomendacionesIngresos        = array();

        $strTipoIdentificacion =  $arrayParametrosRecomendacion["datos"]["tipoIdentificacion"];
        $strTipoIdentificacion =  substr( $strTipoIdentificacion, 0,1); 
        $arrayParametrosRecomendacion["datos"]["tipoIdentificacion"] = $strTipoIdentificacion; 

        $strTipoTributario  =  $arrayParametrosRecomendacion["datos"]["tipoTributario"];

        /* @var $servicePreClienteMs \telconet\comercialBundle\Service\PreClienteMsService */ 
        $servicePreClienteMs = $this->get('comercial.PreClienteMs');
                     
        $arrayResponse      =  $servicePreClienteMs->wsVerificarRecomendaciones($arrayParametrosRecomendacion);
      
        $objData ="{}"; 
        if ($arrayResponse['strStatus']=='OK' ) 
        {  
            $objData  =  $arrayResponse['objData'];  
            $arrayRecomendaciones= $objData["recomendaciones"]; 
            
            for ($intCont=0; $intCont < count( $arrayRecomendaciones); $intCont++) 
            { 
                $objItem=  $arrayRecomendaciones[$intCont]; 
                $arrayListaDetalle = $objItem["detalle"]; 
                $strTipoRec=$objItem["tipo"];
                $strOrdenRecomendacion=$objItem["ordenRecomendacion"];  

                if ($strOrdenRecomendacion == 1)
                {
                    for ($intCont2=0; $intCont2 < count($arrayListaDetalle ); $intCont2++) 
                    { 
                        $objItem =   $arrayListaDetalle[$intCont2]; 
                        $strTituloRec =   $objItem["descripcion"];
                        $strDescripcionRec=  "0,00";
                        $objNewItem= array( "titulo"=> $strTituloRec,   "descripcion"=>   $strDescripcionRec); 
                        array_push(  $arrayRecomendacionesTarjetaCredito ,  $objNewItem);
            
                    }
                } 
                else
                {
                    for ($intCont4=0; $intCont4 < count($arrayListaDetalle ); $intCont4++) 
                    { 
                        $objItem=   $arrayListaDetalle[$intCont4];  
                        $strDescripcionRec=   $objItem["descripcion"];
                        $strTituloRec =  $strTipoRec; 
                        
                        $objNewItem= array( "titulo"=> $strTituloRec,   "descripcion"=>   $strDescripcionRec); 
                        array_push(  $arrayRecomendacionesIngresos ,  $objNewItem);
                

                    }
                }             
              
            }  
            $arrayResponse['strMensaje'] =  'Se encontró la siguiente información recomendada.';
       }  

        $arrayResponses = array(
            "strStatus" =>   $arrayResponse['strStatus'],
            "strMensaje" =>   $arrayResponse['strMensaje'],
            "objDataRecomendacion"=> $objData ,
            "arrayRecomendacionesTarjetaCredito" =>  $arrayRecomendacionesTarjetaCredito,
            "arrayRecomendacionesIngresos"     =>   $arrayRecomendacionesIngresos

        ); 
         
        return  $arrayResponses; 

    }


  /** 
     * Verificar si el representantes legal  esta disponible para ser vinculado
     *
     * @author Jefferson Alexy Carrillo <jacarrillo@telconet.ec>
     * @version 1.0 10-08-2022
     * 
     * @return Response Datos del representante legal disponible para ser vinculado
     */
    public function ajaxRepresentanteLegalVerificarAction()
    {
       
        $objRequest          = $this->getRequest();
        $strEmpresaCod       = $objRequest->getSession()->get('idEmpresa');
        $strPrefijoEmpresa   = $objRequest->getSession()->get('prefijoEmpresa');
        $strUsuario          = $objRequest->getSession()->get('user');
        $strIdPais           = $objRequest->getSession()->get('intIdPais'); 
        $intIdOficina           = $objRequest->getSession()->get('idOficina'); 
        $strClientIp            = $objRequest->getClientIp();

        
        $serviceUtil            = $this->get('schema.Util');
        
      
        $arrayResponse['status']   = "ERROR";
        $arrayResponse['message']  = ""; 
        

        $strTipoIdentificacionCliente = $objRequest->get('strTipoIdentificacionCliente');
        $strIdentificacionCliente     = $objRequest->get('strIdentificacionCliente'); 
        $strOrigen                    = $objRequest->get('strOrigen');

            
        $strTipoIdentificacion        = $objRequest->get('strTipoIdentificacion');
        $strIdentificacion            = $objRequest->get('strIdentificacion');
            
        $strTipoTributario            = $objRequest->get('strTipoTributario'); 
        
        try
        {

            $serviceTokenCas = $this->get('seguridad.TokenCas');
            $arrayTokenCas = $serviceTokenCas->generarTokenCas();   


            $arrayParams = array(
            'token'                                => $arrayTokenCas['strToken'],
            'codEmpresa'                           => $strEmpresaCod,
            'prefijoEmpresa'                       => $strPrefijoEmpresa,

            'oficinaId'                            => $intIdOficina,
            'origenWeb'                            => $strOrigen,
            'clientIp'                             => $strClientIp,

            'usrCreacion'                          => $strUsuario, 
            'idPais'                               => $strIdPais ,  

            'tipoIdentificacion'                   => $strTipoIdentificacionCliente,
            'identificacion'                       => $strIdentificacionCliente,
            
            'tipoIdentificacionRepresentante'      => $strTipoIdentificacion,
            'identificacionRepresentante'          => $strIdentificacion,
            'tipoTributarioRepresentante'          => $strTipoTributario 
           );

                                                   
            $serviceRepresentanteLegalMs = $this->get('comercial.RepresentanteLegalMs');
                 
            $objResponse     =  $serviceRepresentanteLegalMs->wsVerificarRepresentanteLegal( $arrayParams);
            if ($objResponse['strStatus']!='OK' ) 
            {
                throw new \Exception( $objResponse['strMensaje']);
            }         
          
            $arrayResponse['response'] = $objResponse ['objData'];
            $arrayResponse['status']   = $objResponse ['strStatus'];
            $arrayResponse['message']  = $objResponse ['strMensaje']; 
        }
        catch(\Exception $objException)
        {
            $arrayResponse['message'] = $objException->getMessage() ? $objException->getMessage()
              : 'Ha ocurrido un error inesperado al realizar la consulta';

            $arrayParametrosLog['enterpriseCode']   = $strEmpresaCod;
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = basename(__FILE__);
            $arrayParametrosLog['appClass']         = basename(__CLASS__);
            $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
            $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
            $arrayParametrosLog['messageUser']      = $arrayResponse['message'];
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['descriptionError'] = $objException->getMessage();
            $arrayParametrosLog['inParameters']     = json_encode($arrayParams, 128);
            $arrayParametrosLog['creationUser']     = $strUsuario;
            $serviceUtil->insertLog($arrayParametrosLog);
        }
        $objResponse = new Response(json_encode($arrayResponse));
        $objResponse->headers->set('Content-type', 'text/json');

        return $objResponse;
    }


  /** 
     * Consultar los representantes legal a vincular
     *
     * @author Jefferson Alexy Carrillo <jacarrillo@telconet.ec>
     * @version 1.0 10-08-2022
     * 
     * @return Response lista de representante legal vinculados al cliente
     */
    public function ajaxRepresentanteLegalConsultarAction()
    {
       
        $objRequest          = $this->getRequest();
        $strEmpresaCod       = $objRequest->getSession()->get('idEmpresa');
        $strPrefijoEmpresa   = $objRequest->getSession()->get('prefijoEmpresa');
        $strUsuario          = $objRequest->getSession()->get('user');
        $strIdPais           = $objRequest->getSession()->get('intIdPais'); 
        $intIdOficina           = $objRequest->getSession()->get('idOficina'); 
        $strClientIp            = $objRequest->getClientIp();

        
        $serviceUtil            = $this->get('schema.Util');
        
      
        $arrayResponse['status']   = "ERROR";
        $arrayResponse['message']  = ""; 
        
     
        $strTipoIdentificacionCliente = $objRequest->get('strTipoIdentificacionCliente');
        $strIdentificacionCliente     = $objRequest->get('strIdentificacionCliente'); 
        $strOrigen                    = $objRequest->get('strOrigen');
        
        try
        {

            $serviceTokenCas = $this->get('seguridad.TokenCas');
            $arrayTokenCas = $serviceTokenCas->generarTokenCas();   


            $arrayParams = array(
            'token'                                => $arrayTokenCas['strToken'],
            'codEmpresa'                           => $strEmpresaCod,
            'prefijoEmpresa'                       => $strPrefijoEmpresa,

            'oficinaId'                            => $intIdOficina,
            'origenWeb'                            => $strOrigen,
            'clientIp'                             => $strClientIp,

            'usrCreacion'                          => $strUsuario, 
            'idPais'                               => $strIdPais ,  

            'tipoIdentificacion'                   => $strTipoIdentificacionCliente,
            'identificacion'                       => $strIdentificacionCliente
           );

                                                   
            $serviceRepresentanteLegalMs = $this->get('comercial.RepresentanteLegalMs');
                 
            $objResponse     =  $serviceRepresentanteLegalMs->wsConsultarRepresentanteLegal($arrayParams);
            if ($objResponse['strStatus']!='OK' ) 
            {
                throw new \Exception( $objResponse['strMensaje']);
            }         
          
            $arrayResponse['response'] = $objResponse ['objData'];
            $arrayResponse['status']   = $objResponse ['strStatus'];
            $arrayResponse['message']  = $objResponse ['strMensaje']; 
        }
        catch(\Exception $objException)
        {
            $arrayResponse['message'] = $objException->getMessage() ? $objException->getMessage()
              : 'Ha ocurrido un error inesperado al realizar la consulta';

            $arrayParametrosLog['enterpriseCode']   = $strEmpresaCod;
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = basename(__FILE__);
            $arrayParametrosLog['appClass']         = basename(__CLASS__);
            $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
            $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
            $arrayParametrosLog['messageUser']      = $arrayResponse['message'];
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['descriptionError'] = $objException->getMessage();
            $arrayParametrosLog['inParameters']     = json_encode($arrayParams, 128);
            $arrayParametrosLog['creationUser']     = $strUsuario;
            $serviceUtil->insertLog($arrayParametrosLog);
        }
        $objResponse = new Response(json_encode($arrayResponse));
        $objResponse->headers->set('Content-type', 'text/json');

        return $objResponse;
    }


  /** 
     * Actualizar listado de representantes legal vinculados
     *
     * @author Jefferson Alexy Carrillo <jacarrillo@telconet.ec>
     * @version 1.0 10-08-2022
     * 
     * @return Response mensaje de confirmacion de ejecucion correcta de la transaccion
     */
    public function ajaxRepresentanteLegalActualizarAction()
    {
       
        $objRequest          = $this->getRequest();
        $strEmpresaCod       = $objRequest->getSession()->get('idEmpresa');
        $strPrefijoEmpresa   = $objRequest->getSession()->get('prefijoEmpresa');
        $strUsuario          = $objRequest->getSession()->get('user');
        $strIdPais           = $objRequest->getSession()->get('intIdPais'); 
        $intIdOficina           = $objRequest->getSession()->get('idOficina'); 
        $strClientIp            = $objRequest->getClientIp();

        
        $serviceUtil            = $this->get('schema.Util');
        
      
        $arrayResponse['status']   = "ERROR";
        $arrayResponse['message']  = ""; 
        
     
        $strTipoIdentificacionCliente = $objRequest->get('strTipoIdentificacionCliente');
        $strIdentificacionCliente     = $objRequest->get('strIdentificacionCliente'); 
        $strRepresentantes            = $objRequest->get('strRepresentantes');
        $strOrigen                    = $objRequest->get('strOrigen');
        
        try
        {

            $serviceTokenCas = $this->get('seguridad.TokenCas');
            $arrayTokenCas = $serviceTokenCas->generarTokenCas();   
 
            $objRepresentantes =    json_decode(  $strRepresentantes  , true);

            $arrayParams = array(
            'token'                                => $arrayTokenCas['strToken'],
            'codEmpresa'                           => $strEmpresaCod,
            'prefijoEmpresa'                       => $strPrefijoEmpresa,

            'oficinaId'                            => $intIdOficina,
            'origenWeb'                            => $strOrigen,
            'clientIp'                             => $strClientIp,

            'usrCreacion'                          => $strUsuario, 
            'idPais'                               => $strIdPais ,  

            'tipoIdentificacion'                   => $strTipoIdentificacionCliente,
            'identificacion'                       => $strIdentificacionCliente,
            'representanteLegal'                  => $objRepresentantes 
           );

                                                   
            $serviceRepresentanteLegalMs = $this->get('comercial.RepresentanteLegalMs');
                 
            $objResponse     =  $serviceRepresentanteLegalMs->wsActualizarRepresentanteLegal( $arrayParams);
            if ($objResponse['strStatus']!='OK' ) 
            {
                throw new \Exception( $objResponse['strMensaje']);
            }         
          
            $arrayResponse['response'] = $objResponse ['objData'];
            $arrayResponse['status']   = $objResponse ['strStatus'];
            $arrayResponse['message']  = $objResponse ['strMensaje']; 
        }
        catch(\Exception $objException)
        {
            $arrayResponse['message'] = $objException->getMessage() ? $objException->getMessage()
              : 'Ha ocurrido un error inesperado al realizar la consulta';

            $arrayParametrosLog['enterpriseCode']   = $strEmpresaCod;
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = basename(__FILE__);
            $arrayParametrosLog['appClass']         = basename(__CLASS__);
            $arrayParametrosLog['appMethod']        = basename(__FUNCTION__);
            $arrayParametrosLog['appAction']        = basename(__FUNCTION__);
            $arrayParametrosLog['messageUser']      = $arrayResponse['message'];
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['descriptionError'] = $objException->getMessage();
            $arrayParametrosLog['inParameters']     = json_encode($arrayParams, 128);
            $arrayParametrosLog['creationUser']     = $strUsuario;
            $serviceUtil->insertLog($arrayParametrosLog);
        }
        $objResponse = new Response(json_encode($arrayResponse));
        $objResponse->headers->set('Content-type', 'text/json');

        return $objResponse;
    }

   /**
     *
     * Documentación para la función 'aceptacionPoliticasAction'.
     *
     * Función que renderiza la página de Formulario de aceptación de política de mejora de la experiencia de clientes.
     *
     * @return render - Página de Formulario de clientes.
     *
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 13-12-2022
     *
     */
    public function aceptacionPoliticasAction()
    {
        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $strUsrCreacion         = $objSession->get('user')   ? $objSession->get('user'):"";
        $strIpCreacion          = $objRequest->getClientIp() ? $objRequest->getClientIp():'127.0.0.1';
        $serviceUtil            = $this->get('schema.Util');
        $emGeneral       = $this->getDoctrine()->getManager('telconet_general');
        try
        {
            if( $this->get('security.context')->isGranted('ROLE_6-1') )
            {
                $arrayRolesPermitidos[] = 'ROLE_6-1';
            }
            $arrayAdmiParametro = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->getOne("PARAMETROS_FORMULARIOS_COMERCIAL_CREDENCIAL", "", "", 
                                                     "ENVIO_WHATSAPP","", "", "", "",
                                                     "regularización de clientes",
                                                     $objRequest->getSession()->get("idEmpresa"));
            $strEnviaWhatsapp = 'NO';
            if($arrayAdmiParametro['valor1'])
            {
                $strEnviaWhatsapp = $arrayAdmiParametro['valor1'];
            }

            return $this->render('comercialBundle:cliente:indexAceptacionPoliticas.html.twig', array('rolesPermitidos'  => $arrayRolesPermitidos,
            'enviaWhatsapp' => $strEnviaWhatsapp));

            

        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError('TelcoS+', 
                                      'ClienteController.aceptacionPoliticasAction', 
                                      $e->getMessage(), $strUsrCreacion, $strIpCreacion);
        }
        return null;
    }
   /**
     *
     * Documentación para la función 'personaRegularizacionAction'.
     *
     * Función valida si la persona tiene rol y esta activo y retorna la data de la persona
     *
     * @return render - Página de Formulario de clientes.
     *
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 14-12-2022
     *
     */

    public function personaRegularizacionAjaxAction()
    {
        try 
        {
            $objRequest = $this->getRequest();
            $strEmpresaCod = $objRequest->getSession()->get('idEmpresa');
            $strPrefijoEmpresa = $objRequest->getSession()->get('prefijoEmpresa');
            $strUsuario = $objRequest->getSession()->get('user');
            $strTipoIdentificacion = $objRequest->get('tipoIdentificacion');
            $strIdentificacion = $objRequest->get('identificacion');
            $serviceCliente = $this->get('comercial.Cliente');

            $serviceTokenCas = $this->get('seguridad.TokenCas');
            $arrayTokenCas = $serviceTokenCas->generarTokenCas();
            if(empty($arrayTokenCas['strToken']))
            {
            throw new \Exception($arrayTokenCas['strMensaje']); 
            }
            $arrayParametros['token'] = $arrayTokenCas['strToken'];
            $arrayParametros['data']['empresaCod'] = $strEmpresaCod;
            $arrayParametros['data']['prefijoEmPresa'] = $strPrefijoEmpresa;
            $arrayParametros['data']['identificacion'] = $strIdentificacion;
            $arrayParametros['data']['tipoIdentificacion'] = substr($strTipoIdentificacion, 0, 3);
            $arrayParametros['data']['user'] = $strUsuario;
            $arrayParametros['data']['origen'] = "WEB";
            $arrayParametros['data']['requiereRecomendacion'] = false;

            $arrayPersona = $serviceCliente->personaRegularizacion($arrayParametros);
            $objResponse = new Response(json_encode($arrayPersona));
            $objResponse->headers->set('Content-type', 'text/json');
            return $objResponse;
        } 
        catch (\Exception $e)
        {
            error_log("error de consumo " . json_encode($e));
        }
    }
    /**
     * formasContactoProspectoAjaxAction, obtiene las formas de contacto que se utilizan para el prospecto
     * 
     * @author  Edgar Pin Villavicencio
     * @version 1.0  21-10-2022
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     */
    public function formasContactoProspectoAjaxAction() 
    {
        $objRequest = $this->getRequest();

        $serviceCliente      = $this->get('comercial.Cliente');
        
        $serviceTokenCas = $this->get('seguridad.TokenCas');
        $arrayTokenCas = $serviceTokenCas->generarTokenCas();
        $strUsuario     = $objRequest->getSession()->get('user');
        $strEmpresaCod  = $objRequest->getSession()->get("idEmpresa"); 

        if(empty($arrayTokenCas['strToken']))
        {
            throw new \Exception($arrayTokenCas['strMensaje']); 
        }

        $arrayParametros['token'] = $arrayTokenCas['strToken'];
        $arrayParametros['usrCreacion'] = $strUsuario;
        $arrayParametros['empresaCod']  = $strEmpresaCod;

        $arrayFormasCont = $serviceCliente->getFormasContactoMS($arrayParametros);

        foreach($arrayFormasCont as $item)
        {
            $arrayFormasContacto[] = array('id' => $item["idTipoFormaContacto"], 'descripcion' => $item["descripcion"], 'maximo' => $item["maximo"]);
        }

        $objResponse         = new Response(json_encode(array('formasContacto' => $arrayFormasContacto)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    
    /**
     * guardarFormasContactoProspectoAjaxAction, Permite guardar las formas de contacto consumiendo el microservicio de credenciales.
     * 
     * @author  Edgar Pin Villavicencio
     * @version 1.0 
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     */
    public function guardarFormasContactoProspectoAjaxAction() 
    {

        $servicePreCliente = $this->get('comercial.PreCliente');
        $serviceCliente      = $this->get('comercial.Cliente');
        
        $objRequest       = $this->getRequest();
        $strUsuario     = $objRequest->getSession()->get('user');
        $strEmpresaCod  = $objRequest->getSession()->get("idEmpresa"); 
        $objFormasContacto = json_decode($objRequest->get('data'));
        $arrayResult = array();
        $serviceTokenCas = $this->get('seguridad.TokenCas');
        $arrayTokenCas = $serviceTokenCas->generarTokenCas();

        if(empty($arrayTokenCas['strToken']))
        {
            throw new \Exception($arrayTokenCas['strMensaje']); 
        }

        $arrayParametros['token'] = $arrayTokenCas['strToken'];
        $arrayParametros['usrCreacion'] = $strUsuario;
        $arrayParametros['empresaCod']  = $strEmpresaCod;
        $arrayFormasCont = $serviceCliente->getFormasContactoMS($arrayParametros);

        foreach($arrayFormasCont as $item)
        {
            $arrayFormasContacto[] = array('id' => $item["idTipoFormaContacto"], 'descripcion' => $item["descripcion"], 'maximo' => $item["maximo"]);
        }
        

        foreach($objFormasContacto as $objFormaContacto)
        {
            $intIdFormaContacto = 0;
            foreach($arrayFormasContacto as $item)
            {
                if ($objFormaContacto->formaContacto == $item["descripcion"])
                {
                    $intIdFormaContacto = $item["id"];
                    break;
                }    
            }
            $arrayResult["formasContacto"][] = array("idFormaContacto" => $intIdFormaContacto, "valor" => $objFormaContacto->valor, 
                                                 "esWhatsapp" => $objFormaContacto->esWhatsapp);
        }
        $arrayResult["documentos"] = array("flujo de prospectos");
        $arrayResult["estado"] = "Activo";
        $arrayResult['empresaCod']  = $strEmpresaCod;
        $arrayResult["fechaAperturaLink"] = array();
        $arrayResult["referenciaData"] = array(array("data" => "", "nombreReferencia" => ""));
        $arrayResult["userCreacion"]  = $strUsuario;

        $serviceTokenCas = $this->get('seguridad.TokenCas');
        $arrayTokenCas = $serviceTokenCas->generarTokenCas();

        if(empty($arrayTokenCas['strToken']))
        {
            throw new \Exception($arrayTokenCas['strMensaje']); 
        }
        $arrayParametros['token'] = $arrayTokenCas['strToken'];
        $arrayParametros['data']  = $arrayResult;

        $arrayResponseMs = $serviceCliente->generarCredencial($arrayParametros);
        $arrayResponse = new Response(json_encode( $arrayResponseMs));
        $arrayResponse->headers->set('Content-type', 'text/json');

        return $arrayResponse;
    }

    /**
     * guardarFormasContactoProspectoAjaxAction, Permite guardar las formas de contacto consumiendo el microservicio de credenciales.
     * 
     * @author  Edgar Pin Villavicencio
     * @version 1.0 
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     */
    public function generarCredencialAjaxAction() 
    {
        $serviceCliente      = $this->get('comercial.Cliente');
        
        $objRequest       = $this->getRequest();
        $strUsuario = $objRequest->getSession()->get('user');
        $arrayCorreos = json_decode($objRequest->get('arrayCorreos'));
        $strEmpresaCod  = $objRequest->getSession()->get("idEmpresa"); 
        $arrayTelefonos = json_decode($objRequest->get('arrayTelefonos'));
        $arrayFormascontacto = array();
        foreach ($arrayCorreos as $arrayItem)
        {
            $arrayNew = array("idFormaContacto" => $arrayItem->formaContactoId,
                             "valor"            => $arrayItem->valor,
                             "esWhatsapp"       => $arrayItem->esWhatsapp
                            );
            if ($arrayItem->esWhatsapp)
            {
                $arrayFormascontacto[] = $arrayNew; 
            }                
        }
        foreach ($arrayTelefonos as $arrayItem)
        {
            $arrayNew = array("idFormaContacto" => $arrayItem->formaContactoId,
                             "valor"            => $arrayItem->valor,
                             "esWhatsapp"       => $arrayItem->esWhatsapp
                            );
            if ($arrayItem->esWhatsapp)
            {
                $arrayFormascontacto[] = $arrayNew; 
            }                
        }

        $strIdentificacion = $objRequest->get('identificacion');
        $arrayEnvio["documentos"] = array("regularización de clientes");
        $arrayEnvio["estado"] = "Activo";
        $arrayEnvio["fechaAperturaLink"] = array();
        $arrayEnvio['empresaCod']  = $strEmpresaCod;
        $arrayEnvio["formasContacto"] = $arrayFormascontacto;
        $arrayEnvio["referenciaData"] = array(array("data" => $strIdentificacion, "nombreReferencia" => "PERSONA"));
        $arrayEnvio["userCreacion"]  = $strUsuario;

        

        $serviceTokenCas = $this->get('seguridad.TokenCas');
        $arrayTokenCas = $serviceTokenCas->generarTokenCas();

        if(empty($arrayTokenCas['strToken']))
        {
            throw new \Exception($arrayTokenCas['strMensaje']); 
        }
        $arrayParametros['token'] = $arrayTokenCas['strToken'];
        $arrayParametros['data']  = $arrayEnvio;

        $arrayResponseMs = $serviceCliente->generarCredencial($arrayParametros);
        $arrayResponse = new Response(json_encode( $arrayResponseMs));
        $arrayResponse->headers->set('Content-type', 'text/json');

        return $arrayResponse;
    }
    


    /** 
    * 
    *  Documentación para el método 'verificarServiciosCrsPorPuntoAjaxAction'.
    * 
    *  @author Jefferson Alexy Carrillo <jacarrillo@telconet.ec>
    *  @version 1.0 24-01-2023 -  
    * 
    *  @since 23-07-2019
    * 
    */ 
    public function verificarServiciosCrsPorPuntoAjaxAction()
     {
        $objRequest         = $this->get('request');
        $objSession         = $objRequest->getSession();
        $objResponse           = new Response();
        $strUsuario         = $objSession->get('user');
        $objEm                 = $this->getDoctrine()->getManager('telconet'); 
      
        $intIdPersonaRol = $objRequest->get('intIdPersonaRol');
        $intIdPunto      = $objRequest->get('intIdPunto');
        $arrayResponse    = array(); 
        
        $arrayParametros  = array('intIdPersonaRol' =>  $intIdPersonaRol ,  'intIdPunto' =>   $intIdPunto  );   
        
        try
         { 
            $objEntity = $objEm->getRepository('schemaBundle:InfoPersonaEmpresaRol')->getCrsPorLoginValidateServices($arrayParametros);
            $intCount= count($objEntity); 
            if ($intCount==0)
            {
                $arrayResponse = array('status'=>'OK', 'message'=> 'No existe problemas' ); 
            }
            else
            {
                $arrayResponse = array('status'=>'ERROR', 'message'=> 'Los servicios de punto origen no han sido cancelados,
                 por lo consiguiente no puede usar esta opción.' ); 
            }  

        }
         catch (\Exception $e) 
        {           
            $arrayResponse = array('status'=>'ERROR', 'message' => $e->getMessage()); 
        }


        $objResponse = new Response(json_encode($arrayResponse));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;              
    }

        /**
    * @Secure(roles="ROLE_8-9077")
    * 
    * Documentacion de la funcion clienteEnvioLinkAction
    *
    * @author  Eduardo Montenegro <emontenegro@telconet.ec>
    * @version 1.0 
    * @since 1/02/2023
    * 
    */ 
    public function clienteEnvioLinkAction() 
    {
        $objDerechoTitular = $this->get('administracion.DerechoTitular');
        $arrayRespuesta    = $objDerechoTitular->getParametroWhatsapp();
        $arrayParametros   = json_decode($arrayRespuesta['result'],true)['data'];
        return $this->render('comercialBundle:cliente:envioLink.html.twig', [
                                                                banderaWhatsapp=>$arrayParametros['whatsapp']
                                                            ]);
    }

    public function ajaxEnviarLinkDescifrarAction()
    {
        $objResponse                                  = new JsonResponse();
        $objSession                                   = $this->get('session');
        $arrayParametros['identificacion']            = $this->getRequest()->get('identificacion');
        $arrayParametros['correo']                    = $this->getRequest()->get('correo');
        $arrayParametros['tipoIdentificacion']        = $this->getRequest()->get('tipoIdentificacion');
        if($this->getRequest()->get('celular')!=null)
        {
            $arrayParametros['celular'] = $this->getRequest()->get('celular');
        }
        $arrayParametros['infoLog']['identificacion'] = $objSession->get('id_empleado');
        $arrayParametros['infoLog']['login']          = $objSession->get('user');
        $arrayParametros['infoLog']['origen']         = 'TelcoS+';
        $objDerechoTitular                            = $this->get('administracion.DerechoTitular');
        $arrayRespuesta                               = $objDerechoTitular->enviarLink($arrayParametros);
        $objResponse->setData( $arrayRespuesta );
        return $objResponse;
    }

}
