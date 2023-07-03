<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\planificacionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoEnlace;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

class FactibilidadCambioUltimaMillaController extends Controller implements TokenAuthenticatedController
{ 
    /**
     * @Secure(roles="ROLE_353-1")
     * 
     * Documentación para el método 'indexAction'.
     *
     * Metodo de direccionamiento principal de pantalla 
     * @return render direccinamiento a la pantalla solicitada
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 17-06-2016
     *   
     */
    public function indexAction()
    {
        $rolesPermitidos = array();
        
        if(true === $this->get('security.context')->isGranted('ROLE_353-2717'))
        {
            $rolesPermitidos[] = 'ROLE_353-2717'; // EDITAR FACTIBILIDAD
        }
        
        $em_seguridad   = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("353", "1");        
        
        return $this->render('planificacionBundle:Factibilidad:indexFactibilidadUM.html.twig', array(
                            'item'            => $entityItemMenu,
                            'rolesPermitidos' => $rolesPermitidos
        ));                
    }   

    /**
     * @Secure(roles="ROLE_353-7")
     * 
     * Metodo que devuelve las solicitudes de cambio de UM para dar Factibilidad
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0
     * @since 24/06/2016
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxGridAction()
    {
        ini_set('max_execution_time', 3000000);
        $respuesta            = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion             = $this->get('request');
        $codEmpresa           = ($peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa') : "");
		
        $fechaDesdePlanif     = explode('T',$peticion->get('fechaDesdePlanif'));
        $fechaHastaPlanif     = explode('T',$peticion->get('fechaHastaPlanif'));                
        $login                = $peticion->get('login');       
        $ciudad               = $peticion->get('ciudad');              
        $ultimaMilla          = $peticion->get('ultimaMilla');   
        $start                = $peticion->get('start'); 
        $limit                = $peticion->get('limit'); 

        $strIdJurisdiccion = $peticion->get('id_jurisdiccion'); 

        $strLimite = $peticion->get('limite'); 

        $emComercial                   = $this->getDoctrine()->getManager("telconet");
        $arrayJurisdiccionesId = array();
        $arrayJurisdiccionesEmpresaId = array();
        $strEmpresaCod = $peticion->getSession()->get('idEmpresa');

        $strCaracteristica    = 'CONFIGURACION_PERFILES_FACTIBILIDAD';
        $strEstado            = 'Activo';
        $entityCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                               ->getCaracteristicaPorDescripcionPorEstado($strCaracteristica, $strEstado);
        $intIdEmpleado = $peticion->getSession()->get('idPersonaEmpresaRol'); 
        $intIdCaracteristica = $entityCaracteristica->getId(); 
        $strIdEmpleado = explode("@@", $intIdEmpleado)[0];
        $arrayInfoEmpresaRolCarac = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                        ->findInfoPersonaEmpresaRolCaracByPersonaDescripcion($strIdEmpleado, $intIdCaracteristica);
        $arrayJurisdiccionesEmpresa = $emComercial->getRepository('schemaBundle:AdmiJurisdiccion')
        ->getResultadoJurisdiccionesPorEmpresaSinEstado($strEmpresaCod);
        foreach ($arrayJurisdiccionesEmpresa as $empresa ) 
        {
            $arrayJurisdiccionesEmpresaId[] = $empresa->getId();
        }
        foreach ($arrayInfoEmpresaRolCarac as $registro) 
        {
            if (in_array((int) $registro->getValor(), $arrayJurisdiccionesEmpresaId)) 
            {
                $arrayJurisdiccionesId[] = $registro->getValor();
            }
        }
        
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");
        
        //se agrage modificacion de parametros para realizar consulta de registros de prefactibilidad
        $arrayParametros                             = array();
        $arrayParametros["em"]                       = $em;
        $arrayParametros["start"]                    = $start;
        $arrayParametros["limit"]                    = $limit;
        $arrayParametros["search_fechaDesdePlanif"]  = $fechaDesdePlanif[0];
        $arrayParametros["search_fechaHastaPlanif"]  = $fechaHastaPlanif[0];
        $arrayParametros["search_login2"]            = $login;       
        $arrayParametros["search_ciudad"]            = $ciudad;      
        $arrayParametros["codEmpresa"]               = $codEmpresa;
        $arrayParametros["ultimaMilla"]              = $ultimaMilla;
        $arrayParametros["validaRechazado"]          = "NO";
        $arrayParametros["tipoSolicitud"]            = "SOLICITUD CAMBIO ULTIMA MILLA";
        $arrayParametros["arrayJurisdiccionesId"]      = $arrayJurisdiccionesId;
        $arrayParametros["search_jurisdiccion"]        = $strIdJurisdiccion;
        $arrayParametros["limite"] = $strLimite;
        /* @var $soporteService SoporteService */        
        $arrayParametros["serviceTecnico"]           = $this->get('tecnico.InfoServicioTecnico');                
        
        //migracion clientes transtelco - se agrega parametro ultima milla
        $objJson = $this->getDoctrine()
                        ->getManager("telconet")
                        ->getRepository('schemaBundle:InfoDetalleSolicitud')
                        ->generarJsonPreFactibilidad($arrayParametros);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    public function ajaxComboJurisdiccionesAction()
    {
        ini_set('max_execution_time', 3000000);
        $objRespuesta            = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        
        $objPeticion             = $this->get('request');
        $strCodEmpresa           = ($objPeticion->getSession()->get('idEmpresa') ? $objPeticion->getSession()->get('idEmpresa') : "");
		
        $arrayFechaDesdePlanif     = explode('T',$objPeticion->get('fechaDesdePlanif'));
        $arrayFechaHastaPlanif     = explode('T',$objPeticion->get('fechaHastaPlanif'));                
        $objLogin                = $objPeticion->get('login');       
        $strCiudad               = $objPeticion->get('ciudad');              
        $strUltimaMilla          = $objPeticion->get('ultimaMilla');   
        $intStart                = $objPeticion->get('start'); 
        $intLimit                = $objPeticion->get('limit'); 

        $strIdJurisdiccion = $objPeticion->get('id_jurisdiccion'); 
        $strJurisdiccion = $objPeticion->get('query');

        $strLimite = $objPeticion->get('limite'); 

        $emComercial                   = $this->getDoctrine()->getManager("telconet");
        $arrayJurisdiccionesId = array();
        $arrayJurisdiccionesEmpresaId = array();
        $strEmpresaCod = $objPeticion->getSession()->get('idEmpresa');

        $strCaracteristica    = 'CONFIGURACION_PERFILES_FACTIBILIDAD';
        $strEstado            = 'Activo';
        $entityCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                               ->getCaracteristicaPorDescripcionPorEstado($strCaracteristica, $strEstado);
        $intIdEmpleado = $objPeticion->getSession()->get('idPersonaEmpresaRol'); 
        $intIdCaracteristica = $entityCaracteristica->getId(); 
        $strIdEmpleado = explode("@@", $intIdEmpleado)[0];
        $arrayInfoEmpresaRolCarac = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')                 
        ->findInfoPersonaEmpresaRolCaracByPersonaDescripcion($strIdEmpleado, $intIdCaracteristica);
        $arrayJurisdiccionesEmpresa = $emComercial->getRepository('schemaBundle:AdmiJurisdiccion')
        ->getResultadoJurisdiccionesPorEmpresaSinEstado($strEmpresaCod, $strJurisdiccion);
        foreach ($arrayJurisdiccionesEmpresa as $empresa ) 
        {
            $arrayJurisdiccionesEmpresaId[] = $empresa->getId();
        }
        foreach ($arrayInfoEmpresaRolCarac as $registro) 
        {
            if (in_array((int) $registro->getValor(), $arrayJurisdiccionesEmpresaId)) 
            {
                $arrayJurisdiccionesId[] = $registro->getValor();
            }
        }

        if (is_array($arrayJurisdiccionesId) && count($arrayJurisdiccionesId) > 0)
        {
            $arrayResponse[] = array("id_jurisdiccion"     => -1, 
                                    "jurisdiccion" => "Todas");
            foreach ($arrayJurisdiccionesEmpresa as $empresa ) 
            {
                if (in_array((string) $empresa->getId(), $arrayJurisdiccionesId)) 
                {
                    $objJurisdiccion = array("id_jurisdiccion"=>$empresa->getId(), "jurisdiccion"=>$empresa->getNombreJurisdiccion());
                    $arrayResponse[] = $objJurisdiccion;
                }
            }
            usort($arrayResponse, "cmp");
            $objData = '{"total":"' . count($arrayResponse) . '","encontrados":' . json_encode($arrayResponse) . '}';

            $objRespuesta->setContent($objData);
        
            return $objRespuesta;
        }
        
        $objEm = $this->getDoctrine()->getManager("telconet_infraestructura");
        
        //se agrage modificacion de parametros para realizar consulta de registros de prefactibilidad
        $arrayParametros                             = array();
        $arrayParametros["em"]                       = $objEm;
        $arrayParametros["start"]                    = $intStart;
        $arrayParametros["limit"]                    = $intLimit;
        $arrayParametros["search_fechaDesdePlanif"]  = $arrayFechaDesdePlanif[0];
        $arrayParametros["search_fechaHastaPlanif"]  = $arrayFechaHastaPlanif[0];
        $arrayParametros["search_login2"]            = $objLogin;       
        $arrayParametros["search_ciudad"]            = $strCiudad;      
        $arrayParametros["codEmpresa"]               = $strCodEmpresa;
        $arrayParametros["ultimaMilla"]              = $strUltimaMilla;
        $arrayParametros["validaRechazado"]          = "NO";
        $arrayParametros["tipoSolicitud"]            = "SOLICITUD CAMBIO ULTIMA MILLA";
        $arrayParametros["arrayJurisdiccionesId"]      = $arrayJurisdiccionesId;
        $arrayParametros["search_jurisdiccion"]        = $strIdJurisdiccion;
        $arrayParametros["jurisdiccion"] = $strJurisdiccion;
        $arrayParametros["limite"] = $strLimite;
        /* @var $soporteService SoporteService */        
        $arrayParametros["serviceTecnico"]           = $this->get('tecnico.InfoServicioTecnico');                
        
        //migracion clientes transtelco - se agrega parametro ultima milla
        $objJson = $this->getDoctrine()
                        ->getManager("telconet")
                        ->getRepository('schemaBundle:InfoDetalleSolicitud')
                        ->generarJsonPreFactibilidad($arrayParametros);
        $objRespuesta->setContent($objJson);
        
        return $objRespuesta;
    }

    /**
     * 
     * Metoto que obtiene la informacion de Backbone nueva a partir de la interface conector del nuevo puerto
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0
     * @since 27-06-2016
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 - Se modifica que obtener la informacion nueva de acuerdo al tipo de ultima milla y tipo de backbone del servicio
     * @since 27-07-2016
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 17-07-2018 - Se enviá nuevo parámetro al método getPeBySwitch
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxGetInfoBackboneNuevoUMAction()
    {
        $respuesta = new Response();
        $arrayParametrosWs = array();
        $respuesta->headers->set('Content-Type', 'text/json');
        $emInfraestructura              = $this->getDoctrine()->getManager("telconet_infraestructura");
        $peticion                       = $this->get('request');        
        
        $strUltimaMilla                 =   $peticion->get('ultimaMilla');
        $strTipoBackbone                =   $peticion->get('tipoBackbone');
        $intIdServicio                  =   $peticion->get('idServicio');

        //IN CASSETE / IN ROSETA / IN CPE
        $intInterfaceElementoConector   = $peticion->get('idInterfaceElementoConector');

        try
        {
            if($strUltimaMilla == "UTP" || ( $strUltimaMilla == "Fibra Optica" && $strTipoBackbone == "DIRECTO") )
            {
                $intIdInterfaceElemento = $intInterfaceElementoConector;
                $intIdElemento          = $peticion->get('elemento');
            }            
            else //Si es Fibra Optica - RUTA
            {
                $arrResultado = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                  ->getElementoPadre($intInterfaceElementoConector, 'INTERFACE', 'SWITCH');
                
                if($arrResultado)
                {
                    $intIdInterfaceElemento = $arrResultado[0]['IDINTERFACEELEMENTO'];
                    $intIdElemento          = $arrResultado[0]['IDELEMENTO'];
                }
                else
                {
                    $arrayRespuesta = array(
                                    'status' => 'ERROR' ,
                                    'msg'    => "Error de enlace entre los elementos. Notificar a GIS"
                                    );
                    $respuesta->setContent(json_encode($arrayRespuesta));
                    return $respuesta;
                }  
                
            }
            
            $objInterfaceElemento = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')->find($intIdInterfaceElemento);
                
            $objElemento          = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($intIdElemento);

            $objDetalleElemento   = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                      ->findOneBy(array('elementoId'    => $intIdElemento,
                                                                        'detalleNombre' => 'ANILLO',
                                                                        'estado'        => 'Activo')
                                                                );
            /* @var $soporteService SoporteService */
            $servicioTecnico = $this->get('tecnico.InfoServicioTecnico');

            $arrayParametrosWs["intIdElemento"] = $intIdElemento;
            $arrayParametrosWs["intIdServicio"] = $intIdServicio;

            $objElementoPadre = $servicioTecnico->getPeBySwitch($arrayParametrosWs);
            //Crea Array de resultado
            $arrayResultado = array('idInterfaceElemento'       => $objInterfaceElemento?$objInterfaceElemento->getId():"NA", 
                                    'nombreInterfaceElemento'   => $objInterfaceElemento?$objInterfaceElemento->getNombreInterfaceElemento():"NA",
                                    'idElemento'                => $objElemento?$objElemento->getId():"NA",
                                    'nombreElemento'            => $objElemento?$objElemento->getNombreElemento():"NA",
                                    'idElementoPadre'           => $objElementoPadre?$objElementoPadre->getId():"NA",
                                    'nombreElementoPadre'       => $objElementoPadre?$objElementoPadre->getNombreElemento():"NA",
                                    'anillo'                    => $objDetalleElemento?$objDetalleElemento->getDetalleValor():"NA"
                                    );

            $arrayRespuesta = array(
                            'status' => 'OK' ,
                            'result' => $arrayResultado
                            );                                                              
        } 
        catch (\Exception $ex) 
        {            
            $arrayRespuesta = array(
                                'status' => 'ERROR' ,
                                'msg'    => "ERROR : ".$ex->getMessage()
                                );
        }
                
        $respuesta->setContent(json_encode($arrayRespuesta));
        return $respuesta;
    }          
    
    /**
     * @Secure(roles="ROLE_353-2717")
     * 
     * Metodo que Genera la factibilidad para todos los servicios asociados a una solicitud de cambio de UM
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 25-10-2016
     * @since 1.0
     * 
     * @return Response  $objRespuesta  Objeto que contiene mensaje de respuesta a mostrar al usuario
     */
    public function ajaxGenerarFactibilidadAction()
    {
        $objRespuesta    = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/plain');
        $objPeticion  = $this->get('request');
        
        $objSession                  = $objPeticion->getSession();
        $strUser                     = $objSession->get("user");
        $strIp                       = $objPeticion->getClientIp();
        $strEmpresCod                = $objPeticion->getSession()->get('idEmpresa');
        $emComercial                 = $this->getDoctrine()->getManager("telconet");
        $emInfraestructura           = $this->getDoctrine()->getManager("telconet_infraestructura");
        $serviceUtil                 = $this->get('schema.Util');
        $serviceTecnico              = $this->get('tecnico.InfoServicioTecnico');
        $serviceFactibilidadCambioUm = $this->get('planificacion.FactibilidadCambioUltimaMilla');
        $strMensajeRespuesta         = "";
        $strLoginAux                 = "";
        $strTipoFactibilidad         = "";
        $strUltimaMilla              = "";
        $arrayParametrosRequest      = "";
        $booleanProcesoExitoso       = true;
        $intIdSolicitud              = $objPeticion->get('idSolicitud');
        
        $emComercial->getConnection()->beginTransaction();            
        $emInfraestructura->getConnection()->beginTransaction();
        
        try
        {
            $objSolicitud = $emComercial->getRepository("schemaBundle:InfoDetalleSolicitud")->find($intIdSolicitud);
            if(is_object($objSolicitud))
            {
                $objCaracteristicaUm  = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                    ->findOneBy(array("descripcionCaracteristica" => "SERVICIO_MISMA_ULTIMA_MILLA",
                                                                      "estado"                    => "Activo"));
                if (!is_object($objCaracteristicaUm))
                {
                    throw new \Exception("No existe caracteristica Servicio Misma Ultima Milla.");
                }
                $objTipoSolicitud     = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                    ->findOneBy(array("descripcionSolicitud" => "SOLICITUD CAMBIO ULTIMA MILLA",
                                                                      "estado"               => "Activo"));
                
                $arrayDetalleSolicitudCarac = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                          ->findBy(array("detalleSolicitudId" => $objSolicitud->getId(), 
                                                                         "caracteristicaId"   => $objCaracteristicaUm->getId(),
                                                                         "estado"             => "FactibilidadEnProceso"));
                /* se recorren servicios asociados a la solicitud gestionada para poder generar factibilidad 
                   a todos los servicios que utilizan la misma um */
                foreach($arrayDetalleSolicitudCarac as $objDetalleSolCarac)
                {
                    $objServicio = $objSolicitud->getServicioId();
                    
                    
                    $objDetalleSolicitud = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                       ->findOneBy(array('servicioId'      => $objDetalleSolCarac->getValor(),
                                                                         'tipoSolicitudId' => $objTipoSolicitud->getId(),
                                                                         'estado'          => "FactibilidadEnProceso"));
                    if (is_object($objDetalleSolicitud))
                    {
                        $strLoginAux               = "";
                        $strTipoFactibilidad       = "";
                        $strUltimaMilla            = "";
                        $objServicio               = $objDetalleSolicitud->getServicioId();
                        $strLoginAux               = $objServicio->getLoginAux();
                        $objServicioTecnico        = $emComercial->getRepository("schemaBundle:InfoServicioTecnico")
                                                                 ->findOneByServicioId($objServicio->getId());
                        if (!is_object($objServicioTecnico))
                        {
                            throw new \Exception("No existe información técnica de servicios.");
                        }
                        $objTipoMedio              = $emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')
                                                                       ->find($objServicioTecnico->getUltimaMillaId());
                        if (!is_object($objTipoMedio))
                        {
                            throw new \Exception("No existe ultima milla en la información técnica de un servicio procesado.");
                        }
                        $strUltimaMilla     = $objTipoMedio->getNombreTipoMedio();  
                        //si la ultima milla es Fibra Optica por default es RUTA
                        if ($strUltimaMilla == "Fibra Optica")
                        {
                           $strTipoFactibilidad = "RUTA"; 
                        }
                        
                        $objServProdCaractTipoFact = $serviceTecnico->getServicioProductoCaracteristica($objServicio,
                                                                                                        'TIPO_FACTIBILIDAD',
                                                                                                        $objServicio->getProductoId());
                        //Si no existe la caracteristica mencionada se setea por default a Fibra Ruta
                        if (is_object($objServProdCaractTipoFact))
                        {
                            $strTipoFactibilidad = $objServProdCaractTipoFact->getValor();
                        }
                        
                        if ($strUltimaMilla == "Radio")
                        {
                            $arrayParametrosRequest = array(
                                                            'intIdSolicitud'       => $objDetalleSolicitud->getId(),
                                                            'intIdSwitchNew'       => $objPeticion->get('idSwitch'),
                                                            'intIdInterfaceNew'    => $objPeticion->get('idInterface'),
                                                            'intIdRadioBbNew'      => $objPeticion->get('idRadioBb'),
                                                            'strTipoCambio'        => $objPeticion->get('tipoCambio'),
                                                            'strEsTercerizada'     => $objPeticion->get('esTercerizada'),
                                                            'intIdTercerizadora'   => $objPeticion->get('tercerizadora'),
                                                            'strUsrCreacion'       => $strUser,
                                                            'strIpCreacion'        => $strIp
                                                           );
                            
                            $objRespuestaProceso = $serviceFactibilidadCambioUm->generarFactibilidadUMRadio($arrayParametrosRequest);
                        }
                        else if ($strUltimaMilla == "Fibra Optica" && $strTipoFactibilidad == "RUTA")
                        {
                            $arrayParametrosRequest = array(
                                                            'intIdSolicitud'       => $objDetalleSolicitud->getId(),
                                                            'intIdSwitchNew'       => $objPeticion->get('idSwitch'),
                                                            'intIdInterfaceNew'    => $objPeticion->get('idInterface'),
                                                            'intIdCajaNew'         => $objPeticion->get('idCaja'),
                                                            'intIdCassetteNew'     => $objPeticion->get('idCassette'),
                                                            'intIdInterfaceOutNew' => $objPeticion->get('idInterfaceElementoConector'),
                                                            'strTipoCambio'        => $objPeticion->get('tipoCambio'),
                                                            'strEmpresaCod'        => $strEmpresCod,
                                                            'strUsrCreacion'       => $strUser,
                                                            'strIpCreacion'        => $strIp
                                                           );
                            
                            $objRespuestaProceso = $serviceFactibilidadCambioUm->generarFactibilidadUM($arrayParametrosRequest);
                        }
                        else //Para UTP y FIBRA DIRECTA
                        {
                            $arrayParametrosRequest = array(
                                                            'intIdSolicitud'       => $objDetalleSolicitud->getId(),
                                                            'intIdSwitchNew'       => $objPeticion->get('idSwitch'),
                                                            'intIdInterfaceNew'    => $objPeticion->get('idInterface'),
                                                            'strTipoCambio'        => $objPeticion->get('tipoCambio'),
                                                            'strUsrCreacion'       => $strUser,
                                                            'strIpCreacion'        => $strIp
                                                           );
                            
                            $objRespuestaProceso = $serviceFactibilidadCambioUm->generarFactibilidadUtpFODirecto($arrayParametrosRequest);
                        }
                        if ($objRespuestaProceso->getContent() == "Factibilidad de Cambio de ultima milla generada Correctamente.")
                        {
                            $strMensajeRespuesta = $strMensajeRespuesta.
                                                   "Factibilidad de Cambio de ultima milla generada ".
                                                   "Correctamente para el servicio con login auxiliar: ".$strLoginAux."<br>";
                        }
                        else
                        {
                            $booleanProcesoExitoso = false;
                            $strMensajeRespuesta = $strMensajeRespuesta.
                                                   "Existieron errores al procesar la factibilidad ".
                                                   "para el servicio con login auxiliar: ".$strLoginAux." - ".
                                                   $objRespuestaProceso->getContent()."<br>";
                        }
                    }
                }
                if (!$booleanProcesoExitoso)
                {
                    throw new \Exception($strMensajeRespuesta);
                }
                
                $emComercial->getConnection()->commit();
                $emInfraestructura->getConnection()->commit();
            }
            else
            {
                $strMensajeRespuesta = "No existe solicitud a procesar, favor notificar a sistemas";
            }
        } 
        catch (\Exception $ex) 
        {
            $serviceUtil->insertError('Telcos+', 
                                      'FactibilidadCambioUltimaMillaController.ajaxGenerarFactibilidadAction', 
                                      $ex->getMessage(),
                                      $strUser,
                                      $strIp);
            $strMensajeRespuesta = "Se presentaron errores al procesar la factibilidad, favor notificar a sistemas.";
            
            if ($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->rollback();
            }
            
            if ($emInfraestructura->getConnection()->isTransactionActive())
            {
                $emInfraestructura->getConnection()->rollback();
            }
            
            $emComercial->getConnection()->close();
            $emInfraestructura->getConnection()->close();
        }
        
        $objRespuesta->setContent($strMensajeRespuesta);
        return $objRespuesta;
    }
}
