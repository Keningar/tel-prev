<?php

namespace telconet\searchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class searchController extends Controller
{
    /**
     * Documentación para el método 'ajaxSearchAction'.
     *
     * Muestra los puntos asociados a un cliente  basada en los parámetros 
     * enviados por el usuario.
     *
     * @return Response 
     *
     * @author Christian Yunga <cyungat@telconet.ec>
     * @version 1.7 14-01-2023 Se agrega validación de perfiles solo los perfiles permitidos pueden ingresar a esta opcion.
     *                         Si pasa la validación entonces se registrara el inicio de sesion junto con el login del cliente al 
     *                         que se esta consultando solo para empresa MD.
     *  
     * @author Modificado: Edgar Pin Villavicenio <epin@telconet.ec>
     * @version 1.6 03-03-2021 - Se corrige en la obtención del contrato para que traiga el contrato asociado al punto
     *
     * @author Modificado: Gustavo Narea <gnarea@telconet.ec>
     * @version 1.5 17-02-2021 - Se añade verificacion por punto de cliente para poder extraer el contrato
     * 
     * @author Modificado: Angel Reina <areina@telconet.ec>
     * @version 1.4 27-09-2019 - Se agrega funcionalidad para visualización de botón de acceso directo a información del contrato.
     * 
     * @author Modificado: Kevin Baque <kbaque@telconet.ec>
     * @version 1.3 26-12-2018 Se realiza cambio para que la consulta se realice a través de la persona en sesion, solo para Telconet
     *                         en caso de ser asistente retorna información de los vendedores asignados al asistente
     *                         en caso de ser vendedor retorna su respectiva información 
     *                         en caso de ser subgerente retorna información  de los vendedores que reportan al subgerente
     *                         en caso de ser gerente y otro cargo no aplican los cambios
     * 
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.2 23-09-2015 - Se cambia la presentación del Nombre Completo del Cliente, primero el
     *                           el Apellido y luego el Nombre.
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.1 19-08-2015 - Se incluye el parámetro 'estado_grid_avanzada' para que se muestre 
     *                           en la tabla en la columna 'Estado Punto'. Además se incluye la opción 
     *                           de ver 'resumen'.
     * 
     * @version 1.0 Version Inicial
     */
    public function ajaxSearchAction()
    {
        $objRequest  = $this->getRequest();
        $objSession  = $objRequest->getSession();
        $objResponse = new Response();

        
        $objResponse->headers->set('Content-Type', 'text/json');
        
        $emComercial           = $this->getDoctrine()->getManager();
        $emGeneral             = $this->getDoctrine()->getManager('telconet_general');
        $emSeguridad           = $this->getDoctrine()->getManager('telconet_seguridad');
        $strUsrCreacion        = $objSession->get('user');
        $strCodEmpresa         = $objSession->get('idEmpresa');
        $strPrefijoEmpresa     = $objSession->get('prefijoEmpresa');
        $intIdPersonEmpresaRol = $objSession->get('idPersonaEmpresaRol');
        $intStart              = $objRequest->get("start");
        $intLimit              = $objRequest->get("limit");
        $objDatosJson          = $objRequest->get('datos');
        $objDatos              = json_decode($objDatosJson);
        $strTipoPersonal       = 'Otros';
        $intContadorClt        = 0;
        $arrayRoles            = array();
        
        $arrayCliente      = $objSession->get('cliente');
        $arrayPtoCliente   = $objSession->get('ptoCliente');
        $strIpCreacion     = $objRequest->getClientIp();
        $serviceTokenCas   = $this->get('seguridad.TokenCas');
        $serviceInfoLog    = $this->get('comercial.InfoLog');
        $arrayDatosCliente = array();
                
        if($strPrefijoEmpresa ===  'MD')
        {
            $arrayRoles['verDatosPunto']     = false;
            $arrayRoles['verDatosTecnicos']  = false;
            $arrayRoles['verEstadoCtaPto']   = false;
            $arrayRoles['verCasos']          = false;              
            $arrayRoles['verResumen']        = false;
            $arrayRoles['verDiagnosticoOss'] = false; 
            $boolUserTn                      = false;
            $boolUserMd                      = false;
           
            $objInfoPerEmpRolSession = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                   ->find($intIdPersonEmpresaRol);

            if(is_object($objInfoPerEmpRolSession))
            {
                
                $arrayPersEmpRolTn = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                 ->getPersonaEmpresaRolPorPersonaPorEmpresa($objInfoPerEmpRolSession->getPersonaId()->getId(), 
                                                                                          '10');
                if(count($arrayPersEmpRolTn)>0)
                {
                    $boolUserTn = true;
                }                 
                $arrayPersEmpRolMd = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                 ->getPersonaEmpresaRolPorPersonaPorEmpresa($objInfoPerEmpRolSession->getPersonaId()->getId(), 
                                                                                          '18');
                if(count($arrayPersEmpRolTn)>0)
                {
                    $boolUserMd = true;
                } 
                $objVerDatosPunto = $emSeguridad->getRepository('schemaBundle:SistPerfil')
                                                ->findOneBy(
                                                              array(
                                                                    'nombrePerfil'  => 'Md_Tn_DatosPunto',
                                                                    'estado'        => 'Activo'
                                                                   )
                                                            );            
                $objSegPerfPers1 = $emSeguridad->getRepository('schemaBundle:SeguPerfilPersona')
                                                ->findOneBy(
                                                              array(
                                                                      'perfilId'  => $objVerDatosPunto->getId(),
                                                                      'personaId' => $objInfoPerEmpRolSession->getPersonaId()->getId()
                                                                   )
                                                           );
               // Si es usuario TN-MD y tiene perfil o si es usuario MD y tiene perfil con acceso a modulo-accion
                if(is_object($objSegPerfPers1) || (!$boolUserTn && $this->get('security.context')->isGranted('ROLE_9-6'))
                    || ($boolUserTn && $boolUserMd && $this->get('security.context')->isGranted('ROLE_9-6')))
                {
                    $arrayRoles['verDatosPunto'] = true;
                }
         
                $arrayRoles['verDatosTecnicos']  = $this->get('security.context')->isGranted('ROLE_151-8917');
                $arrayRoles['verEstadoCtaPto']   = $this->get('security.context')->isGranted('ROLE_91-1');
                $arrayRoles['verCasos']          = $this->get('security.context')->isGranted('ROLE_78-1');                         
                $arrayRoles['verResumen']        = $this->get('security.context')->isGranted('ROLE_151-8897');
                $arrayRoles['verDiagnosticoOss'] = $this->get('security.context')->isGranted('ROLE_151-8898');
            } 
            
            if(!empty($arrayCliente))
            {
                 $objInfoPersona  = $emComercial->getRepository('schemaBundle:InfoPersona')->findOneById($arrayCliente['id']);

                 if(is_object($objInfoPersona))
                 {
                     $arrayDatosCliente['nombres']            = $objInfoPersona->getNombres();
                     $arrayDatosCliente['apellidos']          = $objInfoPersona->getApellidos();
                     $arrayDatosCliente['razon_social']       = $objInfoPersona->getRazonSocial();
                     $arrayDatosCliente['identificacion']     = $objInfoPersona->getIdentificacionCliente();
                     $arrayDatosCliente['tipoTributario']     = $objInfoPersona->getTipoTributario();
                     $arrayDatosCliente['tipoIdentificacion'] = $objInfoPersona->getTipoIdentificacion();
                     $arrayDatosCliente['login']              = $arrayPtoCliente['login'];
                 }                 
            } 
            $strOrigen        = '';
            $strMetodo        = '';
            $objAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                            ->findOneBy(array('nombreParametro' => 'VISUALIZACION LOGS', 
                                                              'estado'          => 'Activo'));
            if(is_object($objAdmiParametroCab))
            {              
                $objParamDetOrigen = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                 ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                                   'descripcion' => 'ORIGEN',
                                                                   'empresaCod'  => $strCodEmpresa,
                                                                   'estado'      => 'Activo'));

                $objParamDetMetodo = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                 ->findOneBy(array('parametroId'     => $objAdmiParametroCab,
                                                                   'observacion'     => 'BUSCAR LOGIN GENERAL',
                                                                   'empresaCod'      => $strCodEmpresa,
                                                                   'estado'          => 'Activo'));           
                if(is_object($objParamDetOrigen))
                {
                    $strOrigen  = $objParamDetOrigen->getValor1();
                }

                if(is_object($objParamDetMetodo))
                {
                    $strMetodo  = $objParamDetMetodo->getValor1();
                }             
            }
            $arrayParametrosLog                   = array();
            $arrayParametrosLog['strOrigen']      = $strOrigen;
            $arrayParametrosLog['strMetodo']      = $strMetodo;
            $arrayParametrosLog['strTipoEvento']  = 'INFO';
            $arrayParametrosLog['strIpUltMod']    = $strIpCreacion;
            $arrayParametrosLog['strUsrUltMod']   = $strUsrCreacion;
            $arrayParametrosLog['dateFechaEvento']= date("Y-m-d h:i:s");
            $arrayParametrosLog['strIdKafka']     = '';
         

            $arrayTokenCas               = $serviceTokenCas->generarTokenCas();
            $arrayParametrosLog['token'] = $arrayTokenCas['strToken'];
            $serviceInfoLog->registrarLogsMs($arrayParametrosLog);            
        }
        /**
         * BLOQUE QUE VALIDA LA CARACTERISTICA 'CARGO_GRUPO_ROLES_PERSONAL','ASISTENTE_POR_CARGO' ASOCIADA A EL USUARIO LOGONEADO 
         */
        $arrayResultadoCaracteristicas = $emComercial->getRepository('schemaBundle:InfoPersona')->getCargosPersonas($strUsrCreacion);
        if( !empty($arrayResultadoCaracteristicas) )
        {
            $arrayResultadoCaracteristicas = $arrayResultadoCaracteristicas[0];
            $strTipoPersonal               = $arrayResultadoCaracteristicas['STRCARGOPERSONAL'] ? $arrayResultadoCaracteristicas['STRCARGOPERSONAL'] : 'Otros';
        }

        $arrayResultado   = $emComercial->getRepository('schemaBundle:InfoPunto')->search($objDatos,$strCodEmpresa,$strPrefijoEmpresa,$intStart,$intLimit);
        $intTotalBusqueda = $arrayResultado['total'];
        if($arrayResultado) 
        {
            $resultadoArray = array();
                        
            foreach($arrayResultado["datos"] as $arrayItem)
            {
                $arrayResult       = array();
                $strVerResumen     = 'N';
                $strLinkVerResumen = '';
                $strPtoFacturacion = "";
                $objInfOficinaGrupo   = $emComercial->getRepository('schemaBundle:InfoOficinaGrupo')->find($arrayItem['id_oficina']);
    
                if($arrayItem['nombres_cliente'] && $arrayItem['apellidos_cliente'])
                {
                    $strNombreCompletoCliente = $arrayItem['apellidos_cliente']." ".$arrayItem['nombres_cliente'];
                }
                else
                {
                     $strNombreCompletoCliente = "";
                }
                
                //OBTENGO LOS DATOS DEL CONTRATO
                $arrayParametros ["id_cliente"] =  $arrayItem['id_cliente'];
                $arrayParametros ["id_punto"]   =  $arrayItem['id_punto'];

                $objPunto = $emComercial->getRepository('schemaBundle:InfoPunto')->find($arrayParametros ["id_punto"]);
                $intIdContrato = null;
                if ($objPunto)
                {

                    $arrayContrato = $emComercial->getRepository('schemaBundle:InfoContrato')
                                                 ->findBy(array("personaEmpresaRolId" => $objPunto->getPersonaEmpresaRolId()->getId()),
                                                          array("id" => "DESC")); 
                    $objContrato = ($arrayContrato) ? $arrayContrato[0] : null; 
                }
                if ($objContrato)
                {
                    $intIdContrato          = $objContrato->getId();
                }
                
                $intIdPunto   = $arrayItem['id_punto'];
                $arrayArreglo = array();

                $arrayResultado = array();
                $arrayResultado = $emComercial->getRepository('schemaBundle:InfoServicio')
                                              ->findServiciosPorEmpresaPorPunto($strCodEmpresa,$intIdPunto,$intLimit,0,$intStart);
                
                $objInfoPuntoDatoAdicional  = $emComercial->getRepository('schemaBundle:InfoPuntoDatoAdicional')
                                                          ->findOneBy(array('puntoId'=> $intIdPunto));
                
                if(is_object($objInfoPuntoDatoAdicional))
                {
                    if($objInfoPuntoDatoAdicional->getEsPadreFacturacion() === 'S')
                    {
                        $strPtoFacturacion = "Si";
                    }
                    else
                    {
                        $strPtoFacturacion = "No";
                    }
                    
                }
                
                $arrayDatos = array();
                $intTotal   = 0;

                $arrayDatos = $arrayResultado['registros'];
                $intTotal   = $arrayResultado['total'];

                foreach ($arrayDatos as $objDato)
                {
                    $strNombreTecnico = '';

                    if ($objDato->getProductoId())
                    {
                        $strNombreTecnico = $objDato->getProductoId()->getNombreTecnico();
                    }
                    elseif($objDato->getPlanId())
                    {
                        $entityDetallePlan = $emComercial->getRepository('schemaBundle:InfoPlanDet')->findByPlanId($objDato->getPlanId());

                        if( $entityDetallePlan )
                        {
                            foreach($entityDetallePlan as $objItemDetallePlan)
                            {
                                $entityProducto = $emComercial->getRepository('schemaBundle:AdmiProducto')
                                                              ->findOneById($objItemDetallePlan->getProductoId());

                                if( $entityProducto )
                                {
                                    if( strtolower($entityProducto->getNombreTecnico()) == 'internet')
                                    {
                                        $strNombreTecnico = $entityProducto->getNombreTecnico();
                                    }
                                }
                            }           
                        }    
                    }

                    $strEstadoServicio = $objDato->getEstado() ? $objDato->getEstado() : '';

                    if( ( $strEstadoServicio == 'Activo' || $strEstadoServicio == 'In-Corte' || $strEstadoServicio == 'Cancel') 
                          && $strNombreTecnico == 'INTERNET' )
                    {
                        $strVerResumen     = 'S';
                        $strLinkVerResumen = $this->generateUrl( 'dashboardInicio_resumen', array(
                                                                                                    'intServicio'  => $objDato->getId(),
                                                                                                    'intIdPersona' => $arrayItem['id_cliente'],
                                                                                                    'intPunto'     => $intIdPunto
                                                                                               )
                                                               );
                    }
                    
                    if( $strVerResumen == 'S' && $strLinkVerResumen )
                    {
                        break;
                    }
                }//foreach ($arrayDatos as $objDato)
                
                $strContinuar = "S";
                if( ($strPrefijoEmpresa == 'TN' && $strTipoPersonal !== 'Otros' ) && ( $strTipoPersonal !=='GERENTE_VENTAS' && !empty($arrayItem['usrVendedor'])) )
                {
                    $arrayParametrosVend                          = array();
                    $arrayParametrosVend['strPrefijoEmpresa']     = $strPrefijoEmpresa;
                    $arrayParametrosVend['strTipoPersonal']       = $strTipoPersonal;
                    $arrayParametrosVend['intIdPersonEmpresaRol'] = $intIdPersonEmpresaRol;
                    $arrayParametrosVend['strLoginVendedor']      = $arrayItem['usrVendedor'];
                    $arrayResultadoVendAsignado = $emComercial->getRepository('schemaBundle:InfoPersona')->getVendAsignado($arrayParametrosVend);

                    if( !empty($arrayResultadoVendAsignado['resultados']) )
                    {
                        $arrayResult['id_cliente_grid_avanzada']                = $arrayItem['id_cliente'];
                        $arrayResult['id_punto_grid_avanzada']                  = $arrayItem['id_punto'];
                        $arrayResult['razon_social_cliente_grid_avanzada']      = $arrayItem['razon_social_cliente'];
                        $arrayResult['nombre_completo_cliente_grid_avanzada']   = $strNombreCompletoCliente;
                        $arrayResult['login_grid_avanzada']                     = $arrayItem['login'];
                        $arrayResult['nombre_punto_grid_avanzada']              = $arrayItem['nombrePunto'];
                        $arrayResult['descripcion_punto_grid_avanzada']         = $arrayItem['descripcion_punto'];
                        $arrayResult['direccion_punto_grid_avanzada']           = $arrayItem['direccion_punto'];
                        $arrayResult['oficina_grid_avanzada']                   = sprintf("%s",$objInfOficinaGrupo);
                        $arrayResult['estado_grid_avanzada']                    = $arrayItem['estado_punto'];
                        $arrayResult['strPtoFacturacion']                       = $strPtoFacturacion;
                        $arrayResult['verResumen']                              = $strVerResumen;
                        $arrayResult['arrayRoles']                              = $arrayRoles;
                        $arrayResult['linkVerResumen']                          = $strLinkVerResumen;
                        
                        $arrayResult['uri_info_comercial']  = $this->get('router')->generate('infopunto_show', array(
                                                                                                                        'id'  => $arrayItem['id_punto'],
                                                                                                                        'rol' => 'Cliente'
                                                                                                                     )
                                                                                             );
        
                        $arrayResult['uri_info_tecnica']    = $this->get('router')->generate('servicio', array());
                        $arrayResult['uri_info_financiera'] = $this->get('router')->generate('reportes_estado_cuenta_pto_cliente', array());		  
                        $arrayResult['uri_info_soporte']    = $this->get('router')->generate('infocaso', array());
        
                        $arrayResult['strUrlAjaxSetPuntoSession'] = $this->get('router')->generate('search_ajaxSetPuntoSession', array());
                        $resultadoArray[] = $arrayResult;
                        
                    }
                    else
                    {
                        $intContadorClt ++;
                    }
                    $strContinuar = 'N';
                }
                if( $strContinuar == 'S' )
                {
                    $arrayResult['id_cliente_grid_avanzada']                = $arrayItem['id_cliente'];
                    $arrayResult['id_punto_grid_avanzada']                  = $arrayItem['id_punto'];
                    $arrayResult['razon_social_cliente_grid_avanzada']      = $arrayItem['razon_social_cliente'];
                    $arrayResult['nombre_completo_cliente_grid_avanzada']   = $strNombreCompletoCliente;
                    $arrayResult['login_grid_avanzada']                     = $arrayItem['login'];
                    $arrayResult['nombre_punto_grid_avanzada']              = $arrayItem['nombrePunto'];
                    $arrayResult['descripcion_punto_grid_avanzada']         = $arrayItem['descripcion_punto'];
                    $arrayResult['direccion_punto_grid_avanzada']           = $arrayItem['direccion_punto'];
                    $arrayResult['oficina_grid_avanzada']                   = sprintf("%s",$objInfOficinaGrupo);
                    $arrayResult['estado_grid_avanzada']                    = $arrayItem['estado_punto'];
                    $arrayResult['strPtoFacturacion']                       = $strPtoFacturacion;
                    $arrayResult['verResumen']                              = $strVerResumen;
                    $arrayResult['linkVerResumen']                          = $strLinkVerResumen;
                    $arrayResult['arrayRoles']                              = $arrayRoles;
                    
                    $arrayResult['uri_info_comercial']  = $this->get('router')->generate('infopunto_show', array(
                                                                                                                    'id'  => $arrayItem['id_punto'],
                                                                                                                    'rol' => 'Cliente'
                                                                                                                 )
                                                                                         );
                    
                    
                    $arrayResult['uri_info_tecnica']    = $this->get('router')->generate('servicio', array());
                    $arrayResult['uri_info_financiera'] = $this->get('router')->generate('reportes_estado_cuenta_pto_cliente', array());		  
                    $arrayResult['uri_info_soporte']    = $this->get('router')->generate('infocaso', array());
                    
                    //Validación si el usuario tiene contrato.
                    if(!empty($intIdContrato))
                    {
                       $arrayResult['uri_info_contrato']   = $this->get('router')->generate('infocontrato_show',array('id'  => $intIdContrato)); 
                    }
                    $arrayResult['strUrlAjaxSetPuntoSession'] = $this->get('router')->generate('search_ajaxSetPuntoSession', array());
                    $resultadoArray[] = $arrayResult;
                }
            }
            $strMensajeAux = "";
            if( $intContadorClt >0 )
            {
                $strMensajeAux= "Existe por lo menos 1 cliente que cumple con los parámetros ingresados pero no tiene permisos para poder visualizar su información. Por favor dirigirse al módulo Clientes";
            }
            $objData = '{"total":"'.$intTotalBusqueda.'",
                         "myMetaData":"'.$strMensajeAux.'",
                         "encontrados":'.json_encode($resultadoArray).'}';
        }
        else
        {
            $objData = '{"total":"0","encontrados":[]}';
        }
        $objResponse->setContent($objData);

        return $objResponse;
    }

    /**
     * Documentación para el método 'ajaxGetDatosGeneralesAction'.
     *
     * Método que obtiene información específica del Punto y del Cliente.
     *
     * @return Response Lista de datos del Punto y del Cliente.
     * 
     * @author Unknow
     * @version 1.0 Unknow
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>       
     * @version 1.1 29-06-2016
     * 
     * @author Edgar Holguín<eholguin@telconet.ec>       
     * @version 1.2 15-03-2018 Se agrega envío de logines que son padres de facturación de un punto.
     * 
     * Se obtiene el dato adicional el punto para saber si es padre de facturación o no.
     */
    public function ajaxGetDatosGeneralesAction()
    {
        $request  = $this->getRequest();
        $response = new Response();
        
        $response->headers->set('Content-Type', 'text/plain');
		$emComercial = $this->getDoctrine()->getManager();
		$idPersona = $request->get('idPersona');
        $idPunto = $request->get('idPunto');
        $datosGenerales = array();	
        
        $emComercial->getConnection()->beginTransaction();
		
        try{
            $infoPersona = $emComercial->getRepository('schemaBundle:InfoPersona')->find($idPersona);
            $infoPunto = $emComercial->getRepository('schemaBundle:InfoPunto')->find($idPunto);
            
            //datos cliente
            $datosGenerales["identificacionCliente"] = $infoPersona->getIdentificacionCliente();
            $datosGenerales["nombreCliente"] = sprintf("%s",$infoPersona);
            $datosGenerales["estadoCliente"] = $infoPunto->getPersonaEmpresaRolId()->getEstado();
            $datosGenerales["direccionCliente"] = $infoPersona->getDireccion();
            $datosGenerales["representanteLegalCliente"] = $infoPersona->getRepresentanteLegal();
            $datosGenerales["direccionTributariaCliente"] = $infoPersona->getDireccionTributaria();
            
            //datos punto
            $datosGenerales["loginPunto"] = sprintf("%s",$infoPunto->getLogin());
            $datosGenerales["descripcionPunto"] = sprintf("%s",$infoPunto->getDescripcionPunto());
            $datosGenerales["direccionPunto"] = sprintf("%s",$infoPunto->getDireccion());
            $datosGenerales["tipoNegocioPunto"] = sprintf("%s",$infoPunto->getTipoNegocioId());
            $datosGenerales["tipoUbicacionPunto"] = sprintf("%s",$infoPunto->getTipoUbicacionId());
            $datosGenerales["estadoPunto"] = $infoPunto->getEstado();
            $datosGenerales["strPuntosFacturacion"] = "";
            // InfoDatoAdicional para conocer si es padre de Facturación.
            $entityDatoAdicional = $emComercial->getRepository('schemaBundle:InfoPuntoDatoAdicional')->findOneBy(array('puntoId'=> $idPunto));
            if($entityDatoAdicional && $entityDatoAdicional->getEsPadreFacturacion() == 'S')
            {
                $datosGenerales["esPadreFacturacion"] = 'Sí';
            }
            else
            {
                $datosGenerales["esPadreFacturacion"] = 'No';
                $arrayPuntosFacturacion = $emComercial->getRepository('schemaBundle:InfoPunto')->getLoginesPuntosFacturacion($idPunto);
                foreach ($arrayPuntosFacturacion as $arrayLoginsPtoFacturacion):
                    $datosGenerales["strPuntosFacturacion"] .= $arrayLoginsPtoFacturacion['login']." ";
                endforeach;
            }

            $datosGeneralesHtml = $this->renderView('searchBundle:search:datosGenerales.html.twig', array('datosGenerales' => $datosGenerales));
            
            $response->setContent($datosGeneralesHtml);
            $emComercial->getConnection()->commit();
        }catch(\Exception $e)
        {
            $emComercial->getConnection()->rollback();

            $mensajeError = "Error: ".$e->getMessage();
            error_log($mensajeError);
            $response->setContent($mensajeError);
        }
        
        return $response;
    }
    
    public function ajaxGetServiciosPuntoAction() {
	$request = $this->getRequest();
        $id = $request->get("idPunto");
        $limit = $request->get("limit");
        $page = $request->get("page");
        $start = $request->get("start");
        
        $session  = $request->getSession(); 
        $idEmpresa = $session->get('idEmpresa');

        $em = $this->get('doctrine')->getManager('telconet');
        $em_infra = $this->get('doctrine')->getManager('telconet_infraestructura');
	
        $resultado = $em->getRepository('schemaBundle:InfoServicio')->findServiciosPorEmpresaPorPunto($idEmpresa,$id,$limit,$page,$start);
	
        $datos = $resultado['registros'];
        $total = $resultado['total'];

        $i = 1;
        foreach ($datos as $dato):
            if ($i % 2 == 0)
                $clase = 'k-alt';
            else
                $clase = '';

        
            $urlVer = "";
            $urlEditar = $this->generateUrl('cliente_edit', array('id' => $dato->getId()));
            $urlEliminar = $this->generateUrl('infopunto_delete_servicio_ajax', array('id' => $dato->getId()));
			
	        $em_comercial = $this->getDoctrine()->getManager();
	        
		
			$urlFactibilidad = false;			
            if (strtolower($dato->getEstado()) == trim(strtolower("Pre-servicio")) or strtolower($dato->getEstado()) == trim(strtolower("Rechazado")))
                $urlFactibilidad = true;
            else
                $urlFactibilidad = false;
            $linkFactibilidad = ($urlFactibilidad ? 'si' : 'no');
				
            $linkVer = $urlVer;
            if ($dato->getEstado() != "Convertido")
                $linkEditar = $urlEditar;
            else
                $linkEditar = "#";
            $linkEliminar = $urlEliminar;
            $tipoOrden='';
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
                $descripcionProducto=$dato->getPlanId()->getNombrePlan();                
            }
            $entityOT=null;	
            $numero_ot=null;
            if ($dato->getOrdenTrabajoId())
                    $entityOT = $em_comercial->getRepository('schemaBundle:InfoOrdenTrabajo')->findOneById($dato->getOrdenTrabajoId());
            if($entityOT){
                    $tipoOrden="";
                    $numero_ot=$entityOT->getNumeroOrdenTrabajo();
            }
            
             if($dato->getTipoOrden()=='N')
		$tipoOrden='Nueva';
	    else if($dato->getTipoOrden()=='R')
		$tipoOrden='Reubicacion';
	    else if($dato->getTipoOrden()=='T')
		$tipoOrden='Traslado';
	    else
		$tipoOrden='Nueva';
		
            $ultimaMilla='';
            $servicioTecnico=$em->getRepository('schemaBundle:InfoServicioTecnico')->findOneByServicioId($dato->getId());
            if($servicioTecnico){
                if($servicioTecnico->getUltimaMillaId()){
                    $entityUltimaMilla=$em_infra->getRepository('schemaBundle:AdmiTipoMedio')->find($servicioTecnico->getUltimaMillaId());
                    $ultimaMilla=$entityUltimaMilla->getNombreTipoMedio();
                }
            } 
            $descuento='';
            if ($dato->getValorDescuento()){
                $descuento="$".$dato->getValorDescuento();
            }
            elseif ($dato->getPorcentajeDescuento()){
                $descuento=$dato->getPorcentajeDescuento()."%";
            }            
            $arreglo[] = array(
                'idServicio' => $dato->getId(),
                'tipo'=>$tipo,
                'idPunto' => $dato->getPuntoId()->getId(),
                'descripcionPunto' => $dato->getPuntoId()->getDescripcionPunto(),
                'idProducto' => $idProducto,
                'descripcionProducto' => $descripcionProducto,
                'cantidad' => $dato->getCantidad(),
                'fechaCreacion' => strval(date_format($dato->getFeCreacion(), "d/m/Y G:i")),
                'precioVenta' => $dato->getPrecioVenta(),
				'valorDescuento' => $dato->getValorDescuento(),
				'porcentajeDescuento' => $dato->getPorcentajeDescuento(),
                'descuento'=>$descuento,                
                'estado' => $dato->getEstado(),
				'numeroOT' => $numero_ot,
                'linkVer' => $linkVer,
                'linkEditar' => $linkEditar,
                'linkEliminar' => $linkEliminar,
                'linkFactibilidad' => $linkFactibilidad,
                'clase' => $clase,
                'boton' => "",
                'tipoOrden'=>$tipoOrden,
                'ultimaMilla'=>$ultimaMilla
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
     
    public function ajaxSetPuntoSessionAction()
    {
        $request = $this->get('request');
        $session  = $request->getSession();
        $response = new Response();
        
        $response->headers->set('Content-Type', 'text/plain');
        $idPunto = $request->get('idPunto');
        
        $emComercial = $this->getDoctrine()->getManager(); 
        
        $emComercial->getConnection()->beginTransaction();
		
        try{
            
            $arrayParametros = array();
            $arrayParametros['serviceUtil']  = $this->get('schema.Util');
            $arrayParametros['strIpSession'] = $request->getClientIp();
            $arrayParametros['serviceRDA'] = $this->get('tecnico.RedAccesoMiddleware');

            $emComercial->getRepository('schemaBundle:InfoPunto')->setSessionByIdPunto($idPunto,$session, $arrayParametros);
            
            $response->setContent("OK");
            $emComercial->getConnection()->commit();
        }catch(\Exception $e){
            $emComercial->getConnection()->rollback();

            $mensajeError = "Error: ".$e->getMessage();
            error_log($mensajeError);
            $response->setContent($mensajeError);
        }
        
        return $response;
    }
     /**
     * ajaxGuardarLogAction()
     * Función que reliza el seteo de un log asociado a la opción de búsqueda avanzada enviada como parámetro.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 14-01-2023
     * @since 1.0
     *
     * @return $objResponse
     */    
    public function ajaxGuardarLogAction()
    {
        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'text/plain');        
               
        $objRequest           = $this->getRequest();
        $objSession           = $objRequest->getSession();
        $strUsrCreacion       = $objSession->get('user');
        $strCodEmpresa        = $objSession->get('idEmpresa');
        $strPrefijoEmpresa    = $objSession->get('prefijoEmpresa');
        $strIpCreacion        = $objRequest->getClientIp();
        $strObservacion       = $objRequest->get('strObservacion');
        $serviceInfoLog       = $this->get('comercial.InfoLog');
        $serviceTokenCas      = $this->get('seguridad.TokenCas');
        $emGeneral            = $this->getDoctrine()->getManager('telconet_general');
                
        try
        {
         
            if($strPrefijoEmpresa == 'MD' &&  (true === $this->get('security.context')->isGranted('ROLE_151-8898')))
            {           
                $strOrigen        = '';
                $strMetodo        = '';
                $objAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                ->findOneBy(array('nombreParametro' => 'VISUALIZACION LOGS', 
                                                                  'estado'          => 'Activo'));
                if(is_object($objAdmiParametroCab))
                {              
                    $objParamDetOrigen = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                     ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                                       'descripcion' => 'ORIGEN',
                                                                       'empresaCod'  => $strCodEmpresa,
                                                                       'estado'      => 'Activo'));

                    $objParamDetMetodo = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                     ->findOneBy(array('parametroId'     => $objAdmiParametroCab,
                                                                       'observacion'     => $strObservacion,
                                                                       'empresaCod'      => $strCodEmpresa,
                                                                       'estado'          => 'Activo'));           
                    if(is_object($objParamDetOrigen))
                    {
                        $strOrigen  = $objParamDetOrigen->getValor1();
                    }

                    if(is_object($objParamDetMetodo))
                    {
                        $strMetodo  = $objParamDetMetodo->getValor1();
                    }             
                }
                $arrayParametrosLog                   = array();
                $arrayParametrosLog['strOrigen']      = $strOrigen;
                $arrayParametrosLog['strMetodo']      = $strMetodo;
                $arrayParametrosLog['strTipoEvento']  = 'INFO';
                $arrayParametrosLog['strIpUltMod']    = $strIpCreacion;
                $arrayParametrosLog['strUsrUltMod']   = $strUsrCreacion;
                $arrayParametrosLog['dateFechaEvento']= date("Y-m-d h:i:s");
                $arrayParametrosLog['strIdKafka']     = '';


                $arrayTokenCas               = $serviceTokenCas->generarTokenCas();
                $arrayParametrosLog['token'] = $arrayTokenCas['strToken'];
                $serviceInfoLog->registrarLogsMs($arrayParametrosLog);
            }
            
            $objResponse->setContent("OK");   
            
            
        }catch(\Exception $e)
        {
            $strMsjError = "Error: ".$e->getMessage();
            error_log($strMsjError);
            $objResponse->setContent($strMsjError);
        }
    }    
}