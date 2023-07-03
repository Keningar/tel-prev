<?php

namespace telconet\tecnicoBundle\Service;

use Exception;
use telconet\schemaBundle\Entity\InfoInterfaceElemento;
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Entity\InfoServicio;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolHisto;
use telconet\schemaBundle\Entity\InfoDetalle;
use telconet\schemaBundle\Entity\InfoDetalleHistorial;
use telconet\schemaBundle\Entity\InfoDetalleAsignacion;
use telconet\schemaBundle\Entity\InfoCriterioAfectado;
use telconet\schemaBundle\Entity\InfoParteAfectada;
use telconet\schemaBundle\Entity\InfoDetalleSolCaract;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class InfoCancelarServicioService {
    private $emGeneral;
    private $emComercial;
    private $emInfraestructura;
    private $emSoporte;
    private $emComunicacion;
    private $emSeguridad;
    private $emFinanciero;    
    private $servicioGeneral;    
    private $activarService;
    private $licenciasMcAfee;
    private $licenciasOffice365;    
    private $container;
    private $host;
    private $pathTelcos;
    private $pathParameters;
    private $migracionService;    
    private $serviceUtil;
    private $networkingScripts;
    private $serviceSoporte;
    private $serviceCambiarPlanService;
    private $serviceCliente;
    private $rdaMiddleware;
    private $opcion                 = "CANCELAR";
    private $ejecutaComando;
    private $strConfirmacionTNMiddleware;
    private $serviceLicenciasKaspersky;
    private $servicePortalNetCam;
    private $serviceUtilidades;
    private $serviceCoordinarService;
    private $serviceCoordinar2;
    private $serviceInvestigacionDesarrollo;
    
    public function __construct(Container $container) {
        $this->container        = $container;
        $this->emSoporte        = $this->container->get('doctrine')->getManager('telconet_soporte');
        $this->emInfraestructura= $this->container->get('doctrine')->getManager('telconet_infraestructura');
        $this->emSeguridad      = $this->container->get('doctrine')->getManager('telconet_seguridad');
        $this->emComercial      = $this->container->get('doctrine')->getManager('telconet');
        $this->emComunicacion   = $this->container->get('doctrine')->getManager('telconet_comunicacion');
        $this->emNaf            = $this->container->get('doctrine')->getManager('telconet_naf');
        $this->emGeneral        = $this->container->get('doctrine')->getManager('telconet_general');
        $this->emFinanciero     = $this->container->get('doctrine')->getManager('telconet_financiero');        
        $this->host             = $this->container->getParameter('host');
        $this->pathTelcos       = $this->container->getParameter('path_telcos');
        $this->pathParameters   = $this->container->getParameter('path_parameters');
        $this->ejecutaComando   = $container->getParameter('ws_rda_ejecuta_scripts');
        $this->strConfirmacionTNMiddleware = $container->getParameter('ws_rda_opcion_confirmacion_middleware');
    }    
    
    public function setDependencies(InfoServicioTecnicoService $servicioGeneral, 
                                    InfoActivarPuertoService   $activarService,
                                    MigracionHuaweiService     $migracionService,
                                    LicenciasMcAfeeService     $licenciasMcAfeeServicio,
                                    NetworkingScriptsService   $networkingScript,
                                    Container                  $container,
                                    RedAccesoMiddlewareService $redAccesoMiddleware)
    {
        $this->servicioGeneral     = $servicioGeneral;
        $this->activarService      = $activarService;
        $this->migracionService    = $migracionService;
        $this->licenciasMcAfee     = $licenciasMcAfeeServicio;
        $this->licenciasOffice365  = $container->get('tecnico.LicenciasOffice365');
        $this->networkingScripts   = $networkingScript;
        $this->serviceUtil         = $container->get('schema.Util');
        $this->serviceInfoElemento = $container->get('tecnico.InfoElemento');
        $this->serviceSoporte      = $container->get('soporte.SoporteService');
        $this->serviceCliente      = $container->get('comercial.Cliente');
        $this->serviceUtilidades   = $container->get('administracion.Utilidades');
        $this->rdaMiddleware       = $redAccesoMiddleware;
        $this->serviceLicenciasKaspersky = $container->get('tecnico.LicenciasKaspersky');
	    $this->servicePortalNetCam       = $container->get('tecnico.PortalNetlifeCam3dEYEService');
        $this->serviceCambiarPlanService = $container->get('tecnico.InfoCambiarPlan');
        $this->servicePortalNetCam       = $container->get('tecnico.PortalNetlifeCam3dEYEService');
        $this->serviceCoordinarService   = $container->get('planificacion.Coordinar');
        $this->serviceCoordinar2         = $container->get('planificacion.Coordinar2');
        $this->serviceInvestigacionDesarrollo = $container->get('tecnico.InvestigacionDesarrolloWs');
    }
    
    /**
     * Funcion que sirve para la cancelacion de servicios
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 11-04-2016   Se corrige recuperacion de variable $producto
     * @since 1.0
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 01-09-2017   Se agrega el parametro del departamento de la session
     * 
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.2 10-12-2017   Se agrega llamda al nuevo flujo de Cancelacion para el producto "INTERNET SMALL BUSINESS".
     *
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.3 03-12-2018   Se agregan validaciones para gestionar los productos de la empresa TNP
     * @since 1.2
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.4 11-02-2019 Se agrega cancelación para servicios TelcoHome 
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.5  21-11-2019 - Se agrega el proceso para notificar la cancelación del servicio a konibit mediante GDA en caso de aplicar.
     * 
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.6 08-07-2020 - Actualización: Se agrega bloque de código para actualizar datos de tareas en nueva tabla DB_SOPORTE.INFO_TAREA
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.7 09-11-2020 - Se agregó parámetro strPrefijoEmpresa para utilizar en la función cancelarServicioMd
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.7 08-11-2021 Se agrega la invocación del web service para confirmación de opción de Tn a Middleware
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 2.8 01-08-2022 - Se agregan nuevos parámetros intIdOficina y strPrefijoEmpresaOrigen para la cancelación
     *                           de los servicios adicionales del servicio principal INTERNET VPNoGPON.
     *
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 2.9 01-03-2023 Se agrega validación para permitir flujo de cancelación Ecuanet.
     *       
     * @author Leonardo Mero <lemero@telconet.ec>
     * @version 3.0 06-12-2022 - Se agrega el parametro idAccion
     * 
     * @param Array $arrayPeticiones (idEmpresa, prefijoEmpresa, idServicio, idProducto, capacidad1, capacidad2, login, perfil, usrCreacion,
     *                                ipCreacion, motivo, idPersonaEmpresaRol, idAccion)
     */
    public function cancelarServicio($arrayPeticiones){
        //*OBTENCION DE PARAMETROS-----------------------------------------------*/
        $arrayDataConfirmacionTn= array();
        $idEmpresa              = $arrayPeticiones['idEmpresa'];
        $prefijoEmpresa         = $arrayPeticiones['prefijoEmpresa'];
        $idServicio             = $arrayPeticiones['idServicio'];
        $idProducto             = $arrayPeticiones['idProducto'];
        $capacidad1             = $arrayPeticiones['capacidad1'];
        $capacidad2             = $arrayPeticiones['capacidad2'];
        $login                  = $arrayPeticiones['login'];
        $perfil                 = $arrayPeticiones['perfil'];
        $usrCreacion            = $arrayPeticiones['usrCreacion'];
        $ipCreacion             = $arrayPeticiones['ipCreacion'];
        $motivo                 = $arrayPeticiones['motivo'];
        $idPersonaEmpresaRol    = $arrayPeticiones['idPersonaEmpresaRol'];
        $idAccion               = $arrayPeticiones['idAccion'];
        $intIdDepartamento      = $arrayPeticiones['intIdDepartamento'];
        $intIdOficina           = $arrayPeticiones['intIdOficina'];
        $intDetalleId           = null;

        $producto               = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($idProducto);
        $servicio               = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($idServicio);
        $servicioTecnico        = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                        ->findOneBy(array( "servicioId" => $servicio->getId()));
        $interfaceElementoId    = $servicioTecnico->getInterfaceElementoId();
        $interfaceElemento      = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                        ->find($interfaceElementoId);
        $elementoId             = $interfaceElemento->getElementoId();
        $elemento               = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($elementoId);
        $modeloElementoId       = $elemento->getModeloElementoId();
        $modeloElemento         = $this->emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')->find($modeloElementoId);
        $motivoObj              = $this->emGeneral->getRepository('schemaBundle:AdmiMotivo')->find($motivo);
        $boolEsInternetLite     = false;
        if($producto->getNombreTecnico() == 'INTERNET SMALL BUSINESS' || $producto->getNombreTecnico() == 'TELCOHOME')
        {
            $boolEsInternetLite = true;
        }
        $accionObj              = $this->emSeguridad->getRepository('schemaBundle:SistAccion')->find($idAccion);

        $estadoElemento          = $elemento->getEstado();
        $strPrefijoEmpresaOrigen = $arrayPeticiones['prefijoEmpresa'];
        //valido que el elemento no este eliminado
        if($estadoElemento)
        {
            if($estadoElemento != 'Activo')
            {
                $arrayFinal[] = array('status' => "ERROR", 'mensaje' => 'No se puede cancelar debido a que el elemento '.
                    $elemento->getNombreElemento().' esta en estado '.$estadoElemento);
                return $arrayFinal;
            }
        }
        //migracion_ttco_md
        $arrayEmpresaMigra = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
            ->getEmpresaEquivalente($idServicio, $prefijoEmpresa);

        if($arrayEmpresaMigra)
        { 
            if ($arrayEmpresaMigra['prefijo']=='TTCO')
            {
                 $idEmpresa= $arrayEmpresaMigra['id'];
                 $prefijoEmpresa = $arrayEmpresaMigra['prefijo'];
            }
        }
        
         //buscar ultima milla (tipo)
        $ultimaMillaId = $servicioTecnico->getUltimaMillaId();
        $ultimaMilla = $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')->find($ultimaMillaId);
        //*----------------------------------------------------------------------*/
         
        $arrayRespuestaProdsAdicionales = array();
        //*DECLARACION DE TRANSACCIONES------------------------------------------*/
        $this->emSoporte->getConnection()->beginTransaction();
        $this->emComercial->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();
        //*----------------------------------------------------------------------*/

        //Obtenemos los productos adicionales con la característica KONIBIT.
        $arrayServiciosProdKonibit = $this->emComercial->getRepository('schemaBundle:InfoPunto')->getServiciosProductoKonibit(
                    array ('arrayEstadosServicio' =>  array('Activo','In-Corte','In-Temp'),
                           'intIdPunto'           =>  $servicio->getPuntoId()->getId(),
                           'strEstadoProdCaract'  => 'Activo',
                           'strDescripcionCaract' => 'KONIBIT',
                           'strUsuario'           =>  $usrCreacion,
                           'strIp'                =>  $ipCreacion,
                           'objUtilService'       =>  $this->serviceUtil));

        //LOGICA DE NEGOCIO-----------------------------------------------------*/
        try{
            if($prefijoEmpresa=="TTCO"){
                $respuestaArray = $this->cancelarServicioTtco($servicio, $servicioTecnico, $modeloElemento, $producto, 
                                                              $elemento, $interfaceElemento, $login, $ultimaMilla, 
                                                              $usrCreacion, $ipCreacion, $motivoObj,$prefijoEmpresa,
                                                              $idPersonaEmpresaRol, $accionObj);
                $status = $respuestaArray[0]['status'];
                $mensaje = $respuestaArray[0]['mensaje'];
            }
            else if($prefijoEmpresa=="MD" || $strPrefijoEmpresaOrigen == "TNP" || $prefijoEmpresa=="EN")
            {
                $arrayParametros = array(   'intIdDepartamento'     => $intIdDepartamento,
                                            'servicio'              => $servicio,
                                            'servicioTecnico'       => $servicioTecnico,
                                            'modeloElemento'        => $modeloElemento,
                                            'producto'              => $producto,
                                            'elemento'              => $elemento,
                                            'interfaceElemento'     => $interfaceElemento,
                                            'login'                 => $login,
                                            'idEmpresa'             => $idEmpresa,
                                            'usrCreacion'           => $usrCreacion,
                                            'ipCreacion'            => $ipCreacion,
                                            'motivo'                => $motivoObj,
                                            'idPersonaEmpresaRol'   => $idPersonaEmpresaRol,
                                            'accion'                => $accionObj,
                                            'strPrefijoEmpresa'     => $prefijoEmpresa,
                                            'intIdOficina'          => $intIdOficina,
                                            'strPrefijoEmpresaOrigen' => $arrayPeticiones['strPrefijoEmpresaOrigen'],
                                            'idAccion'              => $arrayPeticiones['idAccion'],
                                        );
                
                if(!$boolEsInternetLite)
                {
                    if ($strPrefijoEmpresaOrigen == "TNP")
                    {
                        $respuestaArray = $this->cancelarServicioTnp($arrayParametros);
                    }
                    else
                    {
                        $arrayRespuestaProdsAdicionales = $this->verificaYObtieneInfoProductoAdicionalEnPlan(array( 
                                                                                                                "objServicio"       => $servicio,
                                                                                                                "strUsrCreacion"    => $usrCreacion,
                                                                                                                "strClientIp"       => $ipCreacion));
                        $respuestaArray = $this->cancelarServicioMd($arrayParametros);
                        $arrayDataConfirmacionTn    = $respuestaArray[0]['arrayDataConfirmacionTn'];
                        $strOcurrioException        = $respuestaArray[0]['strOcurrioException'];
                        if($strOcurrioException === "SI")
                        {
                            throw new \Exception($respuestaArray[0]['mensaje']);
                        }
                    }
                }
                else
                {
                    $respuestaArray = $this->cancelarServicioIsb($arrayParametros);
                }

                $status = $respuestaArray[0]['status'];
                $mensaje = $respuestaArray[0]['mensaje'];
                $intDetalleId = $respuestaArray[0]['intDetalleId'];
                $respuestaArray[0]['arrayRespuestaProdsAdicionales'] = $arrayRespuestaProdsAdicionales;
            }            
        }
        catch (\Exception $e) {
            if ($this->emInfraestructura->getConnection()->isTransactionActive()){
                $this->emInfraestructura->getConnection()->rollback();
            }
            
            if ($this->emComercial->getConnection()->isTransactionActive()){
                $this->emComercial->getConnection()->rollback();
            }
            
            if ($this->emSoporte->getConnection()->isTransactionActive()){
                $this->emSoporte->getConnection()->rollback();
            }
            $status="ERROR";
            $mensaje = "ERROR EN LA LOGICA DE NEGOCIO, ".$e->getMessage();
            $arrayFinal[] = array('status'=>"ERROR", 'mensaje'=>$mensaje);
            $this->rdaMiddleware->ejecutaWsSinEsperarRespuestaMiddleware($arrayDataConfirmacionTn);
            return $arrayFinal;
        }
        //*---------------------------------------------------------------------*/

        //*DECLARACION DE COMMITS*/
        if ($this->emInfraestructura->getConnection()->isTransactionActive()){
            $this->emInfraestructura->getConnection()->commit();
        }

        if ($this->emComercial->getConnection()->isTransactionActive()){
            $this->emComercial->getConnection()->commit();
        }
        
        if ($this->emSoporte->getConnection()->isTransactionActive()){
            $this->emSoporte->getConnection()->commit();
        }
        
        $this->emInfraestructura->getConnection()->close();
        $this->emComercial->getConnection()->close();
        $this->emSoporte->getConnection()->close();

        //Proceso que graba tarea en INFO_TAREA
        if (isset($intDetalleId) && $intDetalleId > 0)
        {
            $arrayParametrosInfoTarea['intDetalleId']   = $intDetalleId;
            $arrayParametrosInfoTarea['strUsrCreacion'] = $arrayPeticiones['usrCreacion'];
            $this->serviceSoporte->crearInfoTarea($arrayParametrosInfoTarea);
        }
        //*----------------------------------------------------------------------*/


        //Proceso para notificar la cancelación del servicio a konibit mediante GDA en caso de aplicar.
        try
        {
            if ($prefijoEmpresa === 'MD' && $status === 'OK' && is_object($servicio))
            {
                $this->emInfraestructura->getRepository('schemaBundle:InfoServicioTecnico')
                        ->notificarKonibit(array ('intIdServicio'  =>  $servicio->getId(),
                                                  'strTipoProceso' => 'CANCELAR',
                                                  'strTipoTrx'     => 'INDIVIDUAL',
                                                  'strUsuario'     =>  $usrCreacion,
                                                  'strIp'          =>  $ipCreacion,
                                                  'objUtilService' =>  $this->serviceUtil));

                //Se notifica la cancelación de los productos adicionales con la característica de KONIBIT.
                if (!empty($arrayServiciosProdKonibit['result']) && count($arrayServiciosProdKonibit['result']) > 0)
                {
                    foreach($arrayServiciosProdKonibit['result'] as $arrayValue)
                    {
                        $this->emInfraestructura->getRepository('schemaBundle:InfoServicioTecnico')
                                ->notificarKonibit(array ('intIdServicio'  =>  $arrayValue['idServicio'],
                                                          'strTipoProceso' => 'CANCELAR',
                                                          'strTipoTrx'     => 'INDIVIDUAL',
                                                          'strUsuario'     =>  $usrCreacion,
                                                          'strIp'          =>  $ipCreacion,
                                                          'objUtilService' =>  $this->serviceUtil));
                    }
                }
            }
        }
        catch (\Exception $objException)
        {
            $this->serviceUtil->insertError('Telcos+',
                                            'InfoCancelarServicioService->cancelarServicio->adicional',
                                            'IdServicio: '.$servicio->getId().' - Error: '.$objException->getMessage(),
                                             $usrCreacion,
                                             $ipCreacion);
        }

        $this->rdaMiddleware->ejecutaWsSinEsperarRespuestaMiddleware($arrayDataConfirmacionTn);

        return $respuestaArray;
    }
    
    /**
     * Función que sirve para verificar productos adicionales dentro del plan y obtener la información relacionada al producto
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 28-12-2018
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.1 28-03-2019 Se corrige filtro de detalles de plan del servicio
     * @since 1.0
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 07-08-2019 Se agrega validación de característica SUSCRIBER_ID para conocer si debe cancelar licencias Kaspersky.
     *                          En caso de no tener dicha característica, se seguirá el flujo de cancelación de licencias McAfee
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 28-07-2020 Se elimina validación de planes nuevos vigentes, ya que los detalles de los productos no son dependientes a ésta
     *
     * @author Karen Rodríguez V. <kyrodriguez@telconet.ec>
     * @version 1.4 10-03-2021 Se agrega parámetro strEstadoSpc en la consulta de característica de servicio.
     * 
     * @param array $arrayParametros [
     *                                  "intIdServicio"             => id del servicio,
     *                                  "strUsrCreacion"            => usuario de creación,
     *                                  "strClientIp"               => ip del cliente,
     *                                  "arrayInfoClienteMcAfee"    => información del cliente enviada al ws de McAfee
     *                               ]
     * @return array $arrayRespuestaServicio[
     *                                          "status"                    => OK o ERROR
     *                                          "arrayInfoClienteMcAfee"    => información del cliente relacionada al priducto McAfee
     *                                      ]
     */
    public function verificaYObtieneInfoProductoAdicionalEnPlan($arrayParametros)
    {
        $strTieneSuscriberId        = "NO";
        $boolFalse                  = false;
        $boolMacAfeeEnPlan          = false;
        $objProductoMcAfee          = null;
        $strUsrCreacion             = $arrayParametros["strUsrCreacion"];
        $strClientIp                = $arrayParametros["strClientIp"];
        $arrayInfoClienteMcAfee     = array();
        $intSuscriberId             = 0;
        $strCorreoSuscripcion       = "";
        try
        {
            $objServicio = $arrayParametros["objServicio"];
            
            if(!is_object($objServicio))
            {
                throw new \Exception("No se ha enviado el objeto servicio");
            }
            $objPlanServicio = $objServicio->getPlanId();
            if(is_object($objPlanServicio))
            {
                $intIdPlanServicio      = $objPlanServicio->getId();
                $arrayDetallesPlanServicio  = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                                                ->findBy(array("planId" => $intIdPlanServicio));
                                
                foreach($arrayDetallesPlanServicio as $objDetallePlanServicio)
                {
                    $objProductoDetallePlan = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                                ->find($objDetallePlanServicio->getProductoId());
                    if(is_object($objProductoDetallePlan))
                    {
                        $boolVerificaMacAfeeEnPlan  = strpos($objProductoDetallePlan->getDescripcionProducto(), 'I. PROTEGIDO MULTI PAID');

                        if($boolVerificaMacAfeeEnPlan !== $boolFalse)
                        {
                            $boolMacAfeeEnPlan  = $boolVerificaMacAfeeEnPlan;
                            $objProductoMcAfee  = $objProductoDetallePlan;
                        }
                    }
                }
                
                if($boolMacAfeeEnPlan !== $boolFalse && is_object($objProductoMcAfee))
                {
                    $arrayParametros["objProducto"] = $objProductoMcAfee;
                    $intIdServicio                  = $objServicio->getId();
                    $objSpcSuscriberId              = $this->servicioGeneral
                                                           ->getServicioProductoCaracteristica($objServicio, "SUSCRIBER_ID", $objProductoMcAfee);
                    if(!is_object($objSpcSuscriberId))
                    {
                        $arrayParametrosProdCaract['strEstadoSpc'] = 'Pendiente';
                        $objSpcSuscriberId              = $this->servicioGeneral
                                                           ->getServicioProductoCaracteristica($objServicio, "SUSCRIBER_ID", 
                                                           $objProductoMcAfee, $arrayParametrosProdCaract);
                    }
                    if(is_object($objSpcSuscriberId))
                    {
                        $strTieneSuscriberId    = "SI";
                        $intSuscriberId         = intval($objSpcSuscriberId->getValor());
                        
                        $objSpcCorreo           = $this->servicioGeneral
                                                       ->getServicioProductoCaracteristica($objServicio, "CORREO ELECTRONICO", $objProductoMcAfee);
                        if(is_object($objSpcCorreo))
                        {
                            $strCorreoSuscripcion = $objSpcCorreo->getValor();
                        }
                    }
                    else
                    {
                        $strTieneSuscriberId            = "NO";
                        $arrayDatosCliente              = $this->emComercial->getRepository("schemaBundle:InfoPersona")
                                                                        ->getDatosClientePorIdServicio($intIdServicio,false);
                        $arrayInfoClienteMcAfee         = $this->licenciasMcAfee
                                                               ->obtenerInformacionClienteMcAffe(array( 
                                                                                                "intIdPersona"      => 
                                                                                                $arrayDatosCliente['ID_PERSONA'],
                                                                                                "intIdServicio"     => $intIdServicio,
                                                                                                "strNombrePlan"     => "",
                                                                                                "strEsActivacion"   => "NO",
                                                                                                "objProductoMcAfee" => $objProductoMcAfee));
                    }
                    
                }
            }
            $strStatus = "OK";
        } 
        catch (\Exception $e)
        {
            $strStatus = "ERROR";
            $this->serviceUtil->insertError('Telcos+',
                                            'InfoCancelarServicioService->verificaYObtieneInfoProductoAdicionalEnPlan',
                                            $e->getMessage(),
                                            $strUsrCreacion,
                                            $strClientIp);
        }
        $arrayRespuestaServicio = array("status"                    => $strStatus,
                                        "strTieneSuscriberId"       => $strTieneSuscriberId,
                                        "intSuscriberId"            => $intSuscriberId,
                                        "strCorreoSuscripcion"      => $strCorreoSuscripcion,
                                        "arrayInfoClienteMcAfee"    => $arrayInfoClienteMcAfee);
        return $arrayRespuestaServicio;
    }
    
    /**
     * Función que sirve para cancelar productos adicionales que forman parte de un plan
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 16-12-2018
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.1 28-03-2019 Se corrige filtro de detalles de plan del servicio
     * @since 1.0
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 07-08-2019 Se obtiene parámetro strTieneSuscriberId que valida si el servicio tiene la característica SUSCRIBER_ID.
     *                          En caso de ser SI, se debe cancelar por el flujo de Kaspersky, caso contrario se cancelará por el flujo de McAfee
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 23-08-2019 Se elimina envío de variable strMsjErrorAdicHtml a la función gestionarLicencias ya que no está siendo usada
     *                          y se agrega el envío del parámetro strPermitePlanNoVigente para que deje pasar a las peticiones que vienen 
     *                          del web service de procesos masivos y el parámetro strPermiteEnvioCorreoError para no enviar correo individual de
     *                          error de cancelación con el proceso masivo
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.4 28-07-2020 Se elimina validación de planes nuevos vigentes, ya que los detalles de los productos no son dependientes a ésta
     * 
     * @author Karen Rodríguez V. <kyrodriguez@telconet.ec>
     * @version 1.5 11-03-2021 Se inserta historial de servicio  cuando no tiene SUSCRIBER_ID.
     * @param array $arrayParametros [
     *                                  "intIdServicio"             => id del servicio,
     *                                  "strUsrCreacion"            => usuario de creación,
     *                                  "strClientIp"               => ip del cliente,
     *                                  "arrayInfoClienteMcAfee"    => información del cliente enviada al ws de McAfee,
     *                                  "strTieneSuscriberId"       => SI o NO el servicio tiene asociada la caracterpistica SUSCRIBER ID
     *                               ]
     * 
     */
    public function cancelarProductosAdicionalesEnPlan($arrayParametros)
    {
        $boolFalse                  = false;
        $boolMacAfeeEnPlan          = false;
        $objProductoMcAfee          = null;
        $intIdServicio              = $arrayParametros["intIdServicio"];
        $strUsrCreacion             = $arrayParametros["strUsrCreacion"];
        $strClientIp                = $arrayParametros["strClientIp"];
        $arrayInfoClienteMcAfee     = $arrayParametros["arrayInfoClienteMcAfee"];
        $strTieneSuscriberId        = $arrayParametros["strTieneSuscriberId"];
        $intSuscriberId             = $arrayParametros["intSuscriberId"];
        $strCorreoSuscripcion       = $arrayParametros["strCorreoSuscripcion"];
        $strCodEmpresa              = $arrayParametros["strCodEmpresa"];
        $strPermiteEnvioCorreoError = $arrayParametros["strPermiteEnvioCorreoError"] ? $arrayParametros["strPermiteEnvioCorreoError"] : "SI";
        $strStatus                  = "ERROR";
        $strMensaje                 = "ERROR";
        try
        {
            $objServicio    = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                ->find($intIdServicio);
            if(!is_object($objServicio))
            {
                throw new \Exception("No se ha podido obtener el objeto servicio");
            }
            $arrayParametros["objServicio"] = $objServicio;
            $strEstadoServicioInicial       = $objServicio->getEstado();
            
            $objPlanServicio = $objServicio->getPlanId();
            if(is_object($objPlanServicio))
            {
                $intIdPlanServicio          = $objPlanServicio->getId();
                $arrayDetallesPlanServicio  = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                                                ->findBy(array("planId" => $intIdPlanServicio));
                foreach($arrayDetallesPlanServicio as $objDetallePlanServicio)
                {
                    $objProductoDetallePlan = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                                ->find($objDetallePlanServicio->getProductoId());
                    if(is_object($objProductoDetallePlan))
                    {
                        $boolVerificaMacAfeeEnPlan  = strpos($objProductoDetallePlan->getDescripcionProducto(), 'I. PROTEGIDO MULTI');

                        if($boolVerificaMacAfeeEnPlan !== $boolFalse)
                        {
                            $boolMacAfeeEnPlan  = $boolVerificaMacAfeeEnPlan;
                            $objProductoMcAfee  = $objProductoDetallePlan;
                        }
                    }
                }
                
                if($boolMacAfeeEnPlan !== $boolFalse && is_object($objProductoMcAfee))
                {
                    $arrayParametros["objProducto"] = $objProductoMcAfee;
                    if($strTieneSuscriberId === "SI")
                    {
                        $arrayParamsLicencias           = array("strProceso"                => "CANCELACION_ANTIVIRUS",
                                                                "strEscenario"              => "CANCELACION_PROD_EN_PLAN",
                                                                "intSuscriberId"            => $intSuscriberId,
                                                                "strCorreoSuscripcion"      => $strCorreoSuscripcion,
                                                                "objServicio"               => $objServicio,
                                                                "objPunto"                  => $objServicio->getPuntoId(),
                                                                "strCodEmpresa"             => $strCodEmpresa,
                                                                "objProductoIPMP"           => $objProductoMcAfee,
                                                                "strUsrCreacion"            => $strUsrCreacion,
                                                                "strIpCreacion"             => $strClientIp,
                                                                "strEstadoServicioInicial"  => $strEstadoServicioInicial,
                                                                "strPermiteEnvioCorreoError"=> $strPermiteEnvioCorreoError
                                                                );
                        $arrayRespuestaGestionLicencias = $this->serviceLicenciasKaspersky->gestionarLicencias($arrayParamsLicencias);
                        $strStatusGestionLicencias      = $arrayRespuestaGestionLicencias["status"];
                        $strMensajeGestionLicencias     = $arrayRespuestaGestionLicencias["mensaje"];
                        $arrayRespuestaWs               = $arrayRespuestaGestionLicencias["arrayRespuestaWs"];
                        
                        if($strStatusGestionLicencias === "ERROR")
                        {
                            throw new \Exception($strMensajeGestionLicencias);
                        }
                        else if(isset($arrayRespuestaWs) && !empty($arrayRespuestaWs) && $arrayRespuestaWs["status"] !== "OK")
                        {
                            throw new \Exception($arrayRespuestaWs["mensaje"]);
                        }
                        $strStatus  = $strStatusGestionLicencias;
                        $strMensaje = $strMensajeGestionLicencias;
                    }
                    else
                    {
                        if(empty($arrayInfoClienteMcAfee))
                        {
                            throw new \Exception("No se ha podido obtener la información de McAfee");
                        }
                        $this->emComercial->beginTransaction();
                        //historial del servicio
                        $objServicioHistorial = new InfoServicioHistorial();
                        $objServicioHistorial->setServicioId($objServicio);
                        $objServicioHistorial->setObservacion(
                                "No existe SUSCRIBER_ID dentro del cliente para ejecutar proceso");
                        $objServicioHistorial->setEstado($objServicio->getEstado());
                        $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                        $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                        $objServicioHistorial->setIpCreacion($strClientIp);
                        $objServicioHistorial->setAccion('cancelarCliente');
                        $this->emComercial->persist($objServicioHistorial);
                        $this->emComercial->flush();
                        $this->emComercial->commit();

                        $strStatus                  = 'OK';
                        $strMensaje                 = 'No existe SUSCRIBER_ID dentro del cliente para ejecutar proceso';
                    }
                }
            }
            else
            {
                $strStatus = "OK";
            }
        } 
        catch (\Exception $e)
        {
            $strStatus = "ERROR";
            $strMensaje = $e->getMessage();
            $this->serviceUtil->insertError('Telcos+',
                                            'InfoCancelarServicioService->cancelarProductosAdicionalesEnPlan',
                                            $e->getMessage(),
                                            $strUsrCreacion,
                                            $strClientIp);
        }
        //*DECLARACION DE COMMITS*/
        if ($this->emComercial->getConnection()->isTransactionActive())
        {
            $this->emComercial->getConnection()->commit();
        }
        
        $this->emComercial->getConnection()->close();
        $arrayRespuesta = array("status"    => $strStatus,
                                "mensaje"   => $strMensaje);
        return $arrayRespuesta;
    }
    
    /**
     * Función que realiza la cancelación de un servicio con el producto McAfee incluido en el plan 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 16-12-2018
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.1 02-04-2019  Se agrega log de errores en historial del servicio cuando se presentan problemas al cancelar la suscripción McAfee
     * @since 1.0
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.2 14-08-2019  Se quita close por generar problemas en transacciones
     * @since 1.1
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 04-09-2019 Se modifican los historiales de McAfee para especificar la tecnología
     * 
     * @param array $arrayParametros [
     *                                  "objServicio"               => objeto del servicio,
     *                                  "objProducto"               => objeto del producto McAfee dentro del plan
     *                                  "strUsrCreacion"            => usuario de creación,
     *                                  "strClientIp"               => ip del cliente,
     *                                  "arrayInfoClienteMcAfee"    => información del cliente enviada al ws de McAfee
     *                               ]
     * 
     * @return array $arrayRespuestaServicio[
     *                                          "status"    => OK o ERROR
     *                                          "mensaje"   => mensaje de error
     *                                      ]
     */
    public function cancelacionProductoMcAfeeEnPlan($arrayParametros)
    {
        $objServicio            = $arrayParametros["objServicio"];
        $objProductoMcAfee      = $arrayParametros["objProducto"];
        $strUsrCreacion         = $arrayParametros["strUsrCreacion"];
        $strClientIp            = $arrayParametros['strClientIp'];
        $arrayInfoClienteMcAfee = $arrayParametros["arrayInfoClienteMcAfee"];
        $strStatus              = "";
        $strMensaje             = "";
        $this->emComercial->beginTransaction();
        try
        {
            $arrayInfoClienteMcAfee["strTipoTransaccion"]   = 'Cancelacion';
            $arrayInfoClienteMcAfee["strNombre"]            = "";
            $arrayInfoClienteMcAfee["strApellido"]          = "";
            $arrayInfoClienteMcAfee["strIdentificacion"]    = "";
            $arrayInfoClienteMcAfee["strPassword"]          = "";
            $arrayInfoClienteMcAfee["strMetodo"]            = 'CancelarSuscripcion';
            $arrayInfoClienteMcAfee["intLIC_QTY"]           = $arrayInfoClienteMcAfee["strCantidadDispositivos"];
            $arrayInfoClienteMcAfee["intQTY"]               = 1;
            
            $arrayRespuestaSuscripcion = $this->licenciasMcAfee->operacionesSuscripcionCliente($arrayInfoClienteMcAfee);

            if($arrayRespuestaSuscripcion["procesoExitoso"] == "false")
            {
                if($this->emComercial->getConnection()->isTransactionActive())
                {
                    $this->emComercial->rollback();
                }
                $this->emComercial->getConnection()->beginTransaction();
                //historial del servicio
                $objServicioHistorial = new InfoServicioHistorial();
                $objServicioHistorial->setServicioId($objServicio);
                $objServicioHistorial->setObservacion("No se ha podido cancelar el producto ".$objProductoMcAfee->getDescripcionProducto()
                                                      ." con tecnología MCAFEE incluido en el plan<br>"
                                                      .$arrayRespuestaSuscripcion["mensajeRespuesta"]);
                $objServicioHistorial->setEstado($objServicio->getEstado());
                $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                $objServicioHistorial->setIpCreacion($strClientIp);
                $this->emComercial->persist($objServicioHistorial);
                $this->emComercial->flush();
                $this->emComercial->getConnection()->commit();
                $strStatus = "ERROR";
                throw new \Exception($arrayRespuestaSuscripcion["mensajeRespuesta"]);
            }
            
            //historial del servicio
            $objServicioHistorial = new InfoServicioHistorial();
            $objServicioHistorial->setServicioId($objServicio);
            $objServicioHistorial->setObservacion("Se canceló el producto ".$objProductoMcAfee->getDescripcionProducto()
                                                  ." con tecnología MCAFEE incluido en el plan");
            $objServicioHistorial->setEstado($objServicio->getEstado());
            $objServicioHistorial->setUsrCreacion($strUsrCreacion);
            $objServicioHistorial->setFeCreacion(new \DateTime('now'));
            $objServicioHistorial->setIpCreacion($strClientIp);
            $this->emComercial->persist($objServicioHistorial);
            $this->emComercial->flush();
            
            $strStatus = 'OK';
            $this->emComercial->commit();
        }
        catch (\Exception $e)
        {
            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->rollback();
            }
            
            if ($strStatus === 'ERROR')
            {
                $strMensaje = $e->getMessage();    
            }
            else
            {
                $strStatus  = "ERROR";
                $strMensaje = "";
            }
            $this->serviceUtil->insertError('Telcos+',
                                            'InfoCancelarServicioService->cancelacionProductoMcAfeeEnPlan',
                                            $e->getMessage(),
                                            $strUsrCreacion,
                                            $strClientIp);
            
        }
        $arrayRespuestaServicio = array("status"    => $strStatus,
                                        "mensaje"   => $strMensaje);
        return $arrayRespuestaServicio;
    }
    
    /**
     * Funcion que sirve para la ejecucion de cancelacion de servicios MD
     *  
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 13-04-2016    Se corrige cancelación de servicios de IPS adicionales
     * @since 1.0
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.2 05-05-2016    Se corrige creación de detalles de solicitudes de retiro de equipos
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.3 09-05-2016    Se agrega parametro empresa en metodo cancelarServicioMd por conflictos de producto INTERNET DEDICADO
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.4 10-05-2016    Se corrige creacion de tareas en generación de solicitudes de retiro de equipos
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.5 08-06-2016    Se corrige validación de servicios preferenciales y se setea correctamente el motivo en historial de servicio
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.6 14-06-2016    Se corrige validación de servicios 100/100 que no deben cancelar Ips
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.7 17-06-2016    Al momento de crear la tarea de retiro de equipos se ingresa el afectado en las tablas INFO_CRITERIO_AFECTADO
     *                            y INFO_PARTE_AFECTADA
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.8 06-07-2016    Se agregan estados de servicios para realizar cancelacion automatica luego de haber cancelado el servicio 
     *                            preferencial
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.9 18-07-2016    Se modifica eliminación de enlaces existentes que tengan como inicio
     *                            la interface del splitter del servicio a cancelar
     * 
     * @author Veronica Carrasco <vcarrasco@telconet.ec>
     * @version 1.10 08-07-2016    Se ha agredado el proceso de cancelación para el servicio NETLIFE ZONE
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.11 02-02-2017    Se agrega programación para recuperar un nivel mas de enlaces de equipos para generar el retiro de equipos 
     *                             correctamente para servicios que incluyen equipo Smart Wifi como Producto Adicional o que forme parte del 
     *                             plan de internet
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.12 04-07-2017    Se agrega programación para agregar retiro de equipos NETLIFECAM cuando existan servicios adicionales que 
     *    
     * 
     * @author Francisco Adum <fadum@netlife.net.ec>
     * @version 1.13 19-05-2017     Se actualiza metodo para que utilice el middleware de RDA.
     *                              Se corrige eliminacion de ips adicionales.
     *                              Se elimina validacion de CNR
     *                              Se corrige indentacion del codigo.
     * 
     * @since 1.11
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.14 09-08-2017 -  En la tabla INFO_DETALLE_HISTORIAL se registra el id_persona_empresa_rol del responsable de la tarea
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.15 14-09-2017 - Se realizan ajustes para definir que el estado inicial de una tarea sea 'Asignada'
     * 
     * @author Modificado: Jesús Bozada <jbozada@telconet.ec>
     * @version 1.16 24-10-2017 - Se agrega filtro de estado en busqueda de contrato asociado al punto a cancelar
     * @since 1.15
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.17 21-12-2017 - En la tabla INFO_DETALLE_ASIGNACION se registra el campo tipo asignado 'EMPLEADO'
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.18 05-04-2018 - Se corrige bug detectado, al momento de cancelar el ultimo servicio Activo deja en estado In-Corte al punto.
     *
     * @author Modificado: Jesús Bozada <jbozada@telconet.ec>
     * @version 1.19 09-07-2018 - Se agrega programación para cancelación de servicios con tecnología ZTE
     * @since 1.18
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.20 13-08-2019 Se agrega la cancelación de servicios adicionales I. PROTEGIDO MULTI PAID con tecnología Kaspersky
     *                           al cancelar el servicio de Internet
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.21 23-08-2019 Se elimina envío de variable strMsjErrorAdicHtml a la función gestionarLicencias ya que no está siendo usada
     * 
     * @author  Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 07-10-2019 Se agrega programación para realizar la cancelación de servicios NetlifeZone
     *
     * @author  Marlon Plúas <mpluas@telconet.ec>
     * @version 1.23 29-12-2019 - Se agrega el proceso para cancelar el servicio NetCam en la plataforma 3dEYE.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.24 11-05-2020 Se unifica las validaciones por marca y no por modelo de olt
     * 
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.25 08-07-2020 - Actualización: Se agrega bloque de código para actualizar datos de tareas en nueva tabla DB_SOPORTE.INFO_TAREA
     *
     * @author kevin ortiz <kcortiz@telconet.ec>
     * @version 1.26  22-10-2020  - Se agrega el proceso para eliminar caracteristicas en estado pendiente
     *
     *@author kevin ortiz <kcortiz@telconet.ec>
     * @version 1.26  22-10-2020  - Se agrega el proceso para eliminar caracteristicas en estado pendiente
     *
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.27 09-11-2020 - Se agrega envío de nuevos parámetros al middleware en caso de clientes PYME (ip_fija_wan, tipo_plan_actual)
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.28 16-11-2020 - Se agrega logica para buscar si existen servicios de Netlifecam asociados al punto y con esto agregar
     *                            la serie en la solicitud de retiro de equipo que se genera
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.29 28-01-2021 - Se agrega quita la validacion del estado por Inactivacion del producto Netlife Zone
     * 
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 1.30 16-06-2021 Se realiza invocacion del metodo para cancelar todos los servicios adicionales manuales
     *                          "cancelacionSimulServicios" cuando se cancele el servicio de internet
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.31 08-11-2021 Se construye el arreglo con la información que se enviará al web service para confirmación de opción 
     *                          de Tn a Middleware
     *
     * @param $arrayParametros  [servicio, servicioTecnico, modeloElemento, producto, elemento, interfaceElemento, login, idEmpresa,
     *                          usrCreacion, ipCreacion, motivo, idPersonaEmpresaRol, accion]
     * 
     * @author Liseth Candelario <lcandelario@telconet.ec>
     * @version 1.32 13-07-2022 Se llama a una función para actualizar el estado de UM en ARCGIS en MD
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.4 18-03-2023 - Se agrega Validación para habilitar flujo, envio de prefijo empresa para llamada al middleware 
     *                           y ldap al cancelar servicio en ecuanet.
     *
     * @author Emmanuel Martillo <emartillo@telconet.ec>
     * @version 1.5 20-06-2023 Se agrega bandera para validar el estado de la caracteristica suscriber_id y cancelar
     *                         de manera logica el servicio adicional I. PAID MULTIPAID.
     *
     */
    public function cancelarServicioMd( $arrayParametros )
    {
        $arrayDataConfirmacionTn= array();
        $strOcurrioException    = "NO";
        $servicio               = $arrayParametros['servicio'];
        $servicioTecnico        = $arrayParametros['servicioTecnico'];
        $modeloElemento         = $arrayParametros['modeloElemento'];
        $producto               = $arrayParametros['producto'];
        $elemento               = $arrayParametros['elemento'];
        $interfaceElemento      = $arrayParametros['interfaceElemento'];
        $login                  = $arrayParametros['login'];
        $idEmpresa              = $arrayParametros['idEmpresa'];
        $usrCreacion            = $arrayParametros['usrCreacion'];
        $ipCreacion             = $arrayParametros['ipCreacion'];
        $motivoObj              = $arrayParametros['motivo'];
        $idPersonaEmpresaRol    = $arrayParametros['idPersonaEmpresaRol'];
        $accionObj              = $arrayParametros['accion'];
        $intIdDepartamento      = $arrayParametros['intIdDepartamento'];
        $strPrefijoEmpresa      = $arrayParametros['strPrefijoEmpresa'];
        $strCapacidad1          = "";
        $strCapacidad2          = "";
        $arrayParametrosHist    = array();
        $intDetalleId           = null;

        try
        {
            $arrayParametrosHist["strCodEmpresa"]           = $idEmpresa;
            $arrayParametrosHist["strUsrCreacion"]          = $usrCreacion;
            $arrayParametrosHist["strOpcion"]               = "Historial";
            $arrayParametrosHist["strIpCreacion"]           = $ipCreacion;          
            $arrayParametrosHist["intIdDepartamentoOrigen"] = $intIdDepartamento;
        
            $objUltimaMilla         = $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')
                                                              ->find($servicioTecnico->getUltimaMillaId());
            $planCab                = $servicio->getPlanId();
            $planDet                = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')->findBy(array("planId"=>$planCab->getId()));
            $prodIp                 = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                           ->findBy(array("nombreTecnico"=>"IP","empresaCod"=>$idEmpresa, "estado"=>"Activo"));
            $prodInternet           = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                           ->findBy(array("nombreTecnico"=>"INTERNET","empresaCod"=>$idEmpresa, "estado"=>"Activo"));
            $objIpElemento          = $this->emComercial->getRepository('schemaBundle:InfoIp')
                                           ->findOneBy(array("elementoId"=>$elemento->getId(), "estado"=>"Activo"));
            $objDetalleElemento     = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                   ->findOneBy(array("elementoId"   => $servicioTecnico->getElementoId(),
                                                                     "detalleNombre"=> 'MIDDLEWARE',
                                                                     "estado"       => 'Activo'));

            $objProductoNz = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                             ->findOneBy(array("nombreTecnico" => "NETWIFI", 
                                                               "empresaCod"    => $idEmpresa));
            if (!is_object($objProductoNz) && $strPrefijoEmpresa != 'EN')
            {
                throw new \Exception("No se logró encontrar información del producto NETWIFI");
            }

            $ejecutaLdap            = "NO";
            $flagMiddleware         = false;

            if($objDetalleElemento)
            {
                if($objDetalleElemento->getDetalleValor() == 'SI')
                {
                    $flagMiddleware = true;
                }
            }

            $strMarcaOlt    = $modeloElemento->getMarcaElementoId()->getNombreMarcaElemento();

            $arrayEstadoNoValidados = array('Anulado',
                                            'Eliminado',
                                            'Cancel',
                                            'Trasladado',
                                            'Rechazada',
                                            'Inactivo',
                                            'AnuladoMigra',
                                            'Reubicado',
                                            'Eliminado-Migra',
                                            'AnuladoMigra',
                                            'migracion_ttco');

            if($flagMiddleware)
            {
                $ejecutaLdap            = "SI";
                $strLineProfile         = '';
                $strMacWifi             = '';
                $strVlan                = '';
                $strServiceProfile      = '';
                $strGemPort             = '';
                $strTrafficTable        = '';
                $strMacOnt              = '';
                $strIndiceCliente       = '';
                $scope                  = '';
                $strIpFija              = '';
                $strSpid                = '';
                $intIpFijasActivas      = 0;
                $arrayIpCancelar        = array();

                //OBTENER TIPO DE NEGOCIO
                $strTipoNegocio = $servicio->getPuntoId()->getTipoNegocioId()->getNombreTipoNegocio();

                //OBTENER DATOS DEL CLIENTE (NOMBRES E IDENTIFICACION)
                $objPersona         = $servicio->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId();
                $strIdentificacion  = $objPersona->getIdentificacionCliente();
                $strNombreCliente   = ($objPersona->getRazonSocial() != "") ? $objPersona->getRazonSocial() : 
                                                        $objPersona->getNombres()." ".$objPersona->getApellidos();

                //REVISAR SI EL SERVICIO TIENE IP [$flagProd1]
                $flagProd1 = 0;
                for($i=0;$i<count($planDet);$i++)
                {
                    foreach($prodIp as $productoIp)
                    {
                        if($planDet[$i]->getProductoId() == $productoIp->getId())
                        {
                            $prodIpPlan = $productoIp;
                            $flagProd1  = 1;
                            break;
                        }
                    }

                    for($j=0;$j<count($prodInternet);$j++)
                    {
                        if($planDet[$i]->getProductoId() == $prodInternet[$j]->getId())
                        {
                            $producto = $prodInternet[$j];
                            break;
                        }
                    }
                }

                //validar que no sea plan 100 100            
                $caractEdicionLimitada = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                           ->findOneBy(array("descripcionCaracteristica" => "EDICION LIMITADA",
                                                                             "estado"                    => "Activo"));

                $planCaractEdicionLimitada = $this->emComercial->getRepository('schemaBundle:InfoPlanCaracteristica')
                                                               ->findOneBy(array( "planId"            => $servicio->getPlanId()->getId(),
                                                                                  "caracteristicaId"  => $caractEdicionLimitada->getId(),
                                                                                  "estado"            => $servicio->getPlanId()->getEstado()));
                if($planCaractEdicionLimitada)
                {
                    //se valida caracteristicas edicion limitada y ejecución de ldap
                    if($planCaractEdicionLimitada->getValor() == "SI" && $ejecutaLdap == "SI")
                    {
                        $flagProd1=0;
                    }
                }

                //OBTENER SERIE ONT
                $idElementoCliente  = $servicioTecnico->getElementoClienteId();
                $objElementoCliente = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($idElementoCliente);
                $strSerieOnt        = $objElementoCliente->getSerieFisica();

                //OBTENER MAC ONT
                $spcMacOnt = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "MAC ONT", $producto);
                if($spcMacOnt)
                {
                    $strMacOnt = $spcMacOnt->getValor();
                }

                //OBTENER INDICE CLIENTE
                $spcIndiceCliente = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "INDICE CLIENTE", $producto);
                if($spcIndiceCliente)
                {
                    $strIndiceCliente = $spcIndiceCliente->getValor();
                }

                //OBTENER CARACTERISTICAS PARA TELLION
                if($strMarcaOlt == "TELLION")
                {
                    //OBTENER EL PERFIL
                    $spcLineProfile = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "PERFIL", $producto);
                    if($spcLineProfile)
                    {
                        $strLineProfile = $spcLineProfile->getValor();
                        $arrayPerfil= explode("_", $strLineProfile);
                        $strLineProfile  = $arrayPerfil[0]."_".$arrayPerfil[1];
                    }

                    //OBTENER MAC WIFI
                    $spcMacWifi = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "MAC WIFI", $producto);
                    if($spcMacWifi)
                    {
                        $strMacWifi = $spcMacWifi->getValor();
                    }
                }
                //OBTENER CARACTERISTICAS PARA HUAWEI
                else if($strMarcaOlt == "HUAWEI")
                {
                    //obtener line profile
                    $spcLineProfile = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "LINE-PROFILE-NAME", $producto);
                    if($spcLineProfile)
                    {
                        $strLineProfile = $spcLineProfile->getValor();
                    }

                    //obtener service profile
                    $srvProfileProdCaract = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "SERVICE-PROFILE", $producto);
                    if($srvProfileProdCaract)
                    {
                        $strServiceProfile = $srvProfileProdCaract->getValor();
                    }

                    //obtener vlan
                    $vlanProdCaract = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "VLAN", $producto);
                    if($vlanProdCaract)
                    {
                        $strVlan = $vlanProdCaract->getValor();                
                    }

                    //obtener gemport
                    $gemPortProdCaract = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "GEM-PORT", $producto);
                    if($gemPortProdCaract)
                    {
                        $strGemPort = $gemPortProdCaract->getValor();
                    }

                    //obtener traffic table
                    $trafficTableProdCaract = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "TRAFFIC-TABLE", $producto);
                    if($trafficTableProdCaract)
                    {
                        $strTrafficTable = $trafficTableProdCaract->getValor();
                    }

                    //obtener service-port
                    $spcServicePort = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "SPID", $producto);
                    if($spcServicePort)
                    {
                        $strSpid = $spcServicePort->getValor();
                    }
                }
                else if($strMarcaOlt == "ZTE")
                {
                    $objServiceProfile = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "SERVICE-PROFILE", $producto);
                    if($objServiceProfile)
                    {
                        $strServiceProfile = $objServiceProfile->getValor();
                    }

                    //OBTENER SERVICE-PORT
                    $objSpid = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "SPID", $producto);
                    if($objSpid)
                    {
                        $strSpid = $objSpid->getValor();
                    }

                    //OBTENER CAPACIDAD1
                    $objCapacidad1 = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "CAPACIDAD1", $producto);
                    if(is_object($objCapacidad1))
                    {
                        $strCapacidad1 = $objCapacidad1->getValor();
                    }

                    //OBTENER CAPACIDAD2
                    $objCapacidad2 = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "CAPACIDAD2", $producto);
                    if(is_object($objCapacidad2))
                    {
                        $strCapacidad2 = $objCapacidad2->getValor();
                    }
                }

                //OBTENER SERVICIOS DEL PUNTO
                $arrayServicios     = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                        ->findBy(array('puntoId' => $servicio->getPuntoId()));

                //OBTENER IPS ADICIONALES Y CUANTAS IPS
                $arrayDatosIps      = $this->servicioGeneral->getInfoIpsFijaPunto($arrayServicios, $prodIp, $servicio, $servicio->getEstado(), 'Activo',$producto);

                $intIpFijasActivas  = $arrayDatosIps['ip_fijas_activas'];

                if ($strTipoNegocio === 'PYME')
                {
                    //OBTENER IPS ADICIONALES
                    $arrayParametrosIpWan = array('objPunto'       => $servicio->getPuntoId(),
                                                  'strEmpresaCod'  => $idEmpresa,
                                                  'strUsrCreacion' => $usrCreacion,
                                                  'strIpCreacion'  => $ipCreacion);
                    $arrayDatosIpWan      = $this->servicioGeneral
                                                 ->getIpFijaWan($arrayParametrosIpWan);
                }
                if ($strTipoNegocio === 'PYME' && isset($arrayDatosIpWan['strStatus']) && !empty($arrayDatosIpWan['strStatus']) && 
                    $arrayDatosIpWan['strStatus'] === 'OK' && isset($arrayDatosIpWan['strExisteIpWan']) &&
                    !empty($arrayDatosIpWan['strExisteIpWan']) &&  $arrayDatosIpWan['strExisteIpWan'] === 'SI')
                {
                    $strIpFija = $arrayDatosIpWan['arrayInfoIp']['strIp'];
                    $scope     = $arrayDatosIpWan['arrayInfoIp']['strScope'];
                    $objIpWan  = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                      ->findOneBy(array("servicioId" => $arrayDatosIpWan['arrayInfoIp']['intIdServicioIp'],
                                                        "estado"     => "Activo"));
                }
                else if($flagProd1 == 1)
                {
                    //OBTENER IP DEL PLAN
                    $ipFija     = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                       ->findOneBy(array("servicioId"=>$servicio->getId(),"estado"=>"Activo"));

                    $strIpFija  = $ipFija->getIp();

                    //OBTENER SCOPE
                    $spcScope = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "SCOPE", $prodIpPlan);

                    if(!$spcScope)
                    {
                        //buscar scopes
                        $arrayScopeOlt = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')
                                                                 ->getScopePorIpFija($ipFija->getIp(), $servicioTecnico->getElementoId());

                        if (!$arrayScopeOlt)
                        {   
                            $arrayFinal[] = array('status'=>"ERROR", 'mensaje'=>"Ip Fija no pertenece a un Scope! <br>"
                                                                              . "Favor Comunicarse con el Dep. Gepon!");
                            return $arrayFinal;
                        }

                        $scope = $arrayScopeOlt['NOMBRE_SCOPE'];
                    }
                    else
                    {
                        $scope = $spcScope->getValor();
                    }
                }

                //SI PUNTO TIENE IPS ADICIONALES
                if($intIpFijasActivas > 0)
                {
                    //OBTENER IPS CANCELAR
                    $arrayIpCancelar    = $arrayDatosIps['valores'];                
                }

                $arrayDatos = array(
                                        'serial_ont'            => $strSerieOnt,
                                        'mac_ont'               => $strMacOnt,
                                        'nombre_olt'            => $elemento->getNombreElemento(),
                                        'ip_olt'                => $objIpElemento->getIp(),
                                        'puerto_olt'            => $interfaceElemento->getNombreInterfaceElemento(),
                                        'modelo_olt'            => $modeloElemento->getNombreModeloElemento(),
                                        'ont_id'                => $strIndiceCliente,
                                        'service_port'          => $strSpid,
                                        'gemport'               => $strGemPort,
                                        'service_profile'       => $strServiceProfile,
                                        'line_profile'          => $strLineProfile,
                                        'traffic_table'         => $strTrafficTable,
                                        'vlan'                  => $strVlan,
                                        'estado_servicio'       => $servicioTecnico->getServicioId()->getEstado(),
                                        'ip'                    => $strIpFija,
                                        'ip_fijas_activas'      => $intIpFijasActivas,
                                        'tipo_negocio_actual'   => $strTipoNegocio,
                                        'mac_wifi'              => $strMacWifi,
                                        'scope'                 => $scope,
                                        'ip_cancelar'           => $arrayIpCancelar,
                                        'capacidad_up'          => $strCapacidad1,
                                        'capacidad_down'        => $strCapacidad2
                                    );
                if ($strPrefijoEmpresa === 'MD')
                {
                    $arrayRespuestaSeteaInfo = $this->servicioGeneral
                                                    ->seteaInformacionPlanesPyme(array("intIdPlan"         => $servicio->getPlanId()->getId(),
                                                                                       "intIdPunto"        => $servicio->getPuntoId()->getId(),
                                                                                       "strConservarIp"    => "",
                                                                                       "strTipoNegocio"    => $strTipoNegocio,
                                                                                       "strPrefijoEmpresa" => $strPrefijoEmpresa,
                                                                                       "strUsrCreacion"    => $usrCreacion,
                                                                                       "strIpCreacion"     => $ipCreacion,
                                                                                       "strTipoProceso"    => "CANCELAR_PLAN",
                                                                                       "arrayInformacion"  => $arrayDatos));
                    if($arrayRespuestaSeteaInfo["strStatus"]  === "OK")
                    {
                        $arrayDatos = $arrayRespuestaSeteaInfo["arrayInformacion"];
                    }
                    else
                    {
                        $arrayFinal[] = array('status'  => $arrayRespuestaSeteaInfo["strStatus"],
                                              'mensaje' => "Existieron problemas al recuperar información necesaria ".
                                                           "para ejecutar proceso, favor notifique a Sistemas.");
                        return $arrayFinal;
                    }
                }
                $strPrefijoEmpresa = ($strPrefijoEmpresa == 'EN')? $strPrefijoEmpresa: null;
                $arrayDatosMiddleware = array(
                                                'nombre_cliente'        => $strNombreCliente,
                                                'login'                 => $login,
                                                'identificacion'        => $strIdentificacion,
                                                'datos'                 => $arrayDatos,
                                                'opcion'                => $this->opcion,
                                                'ejecutaComando'        => $this->ejecutaComando,
                                                'usrCreacion'           => $usrCreacion,
                                                'ipCreacion'            => $ipCreacion,
                                                'empresa'               => $strPrefijoEmpresa 
                                            );

                $arrayFinal         = $this->rdaMiddleware->middleware(json_encode($arrayDatosMiddleware));
                $status             = $arrayFinal['status'];
                $mensaje            = $arrayFinal['mensaje'];
                
                $arrayDataConfirmacionTn    = array('nombre_cliente'    => $strNombreCliente,
                                                    'login'             => $login,
                                                    'identificacion'    => $strIdentificacion,
                                                    'datos'             => array(   
                                                                                'serial_ont'                => $arrayDatos['serial_ont'],
                                                                                'nombre_olt'                => $arrayDatos['nombre_olt'],
                                                                                'ip_olt'                    => $arrayDatos['ip_olt'],
                                                                                'puerto_olt'                => $arrayDatos['puerto_olt'],
                                                                                'modelo_olt'                => $arrayDatos['modelo_olt'],
                                                                                'service_profile'           => $arrayDatos['service_profile'],
                                                                                'vlan'                      => $arrayDatos['vlan'],
                                                                                'service_port'              => $arrayDatos['service_port'],
                                                                                'estado_servicio'           => $arrayDatos['estado_servicio'],
                                                                                'tipo_negocio_actual'       => $arrayDatos['tipo_negocio_actual'],
                                                                                'opcion_confirmacion'       => $this->opcion,
                                                                                'respuesta_confirmacion'    => 'ERROR'
                                                                                ),
                                                    'opcion'            => $this->strConfirmacionTNMiddleware,
                                                    'ejecutaComando'    => $this->ejecutaComando,
                                                    'usrCreacion'       => $usrCreacion,
                                                    'ipCreacion'        => $ipCreacion,
                                                    'empresa'           => $strPrefijoEmpresa,
                                                    'statusMiddleware'  => $status);
                $flagAdicional      = 0;
                $mensajeAdicional   = "";

                //SI TIENE IPS ADICIONALES
                if($intIpFijasActivas > 0 && $arrayFinal['status'] == 'OK')
                {
                    $arrayRespuestaIp = $arrayFinal['ip_cancelar'];
                    foreach($arrayRespuestaIp as $ipCancelar)
                    {
                        if($ipCancelar['status'] == "ERROR")
                        {
                            $flagAdicional      = 1;
                            $mensajeAdicional   = $mensajeAdicional . $ipCancelar['mensaje'] . '<br>';
                        }
                        else
                        {
                            //ELIMINAR IP ADICIONAL
                            $objIpAdicional = $this->emInfraestructura
                                                   ->getRepository('schemaBundle:InfoIp')
                                                   ->findOneBy(array('servicioId' => $ipCancelar['id_servicio']),
                                                               array('id' => 'DESC'));
                            $objIpAdicional->setEstado('Eliminado');
                            $this->emInfraestructura->persist($objIpAdicional);
                            $this->emInfraestructura->flush();

                            //CANCELAR SERVICIO ADICIONAL
                            $objServicioAdicional = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($ipCancelar['id_servicio']);
                            $objServicioAdicional->setEstado("Cancel");
                            $this->emComercial->persist($objServicioAdicional);
                            $this->emComercial->flush();

                            //historial del servicio
                            $servicioHistorialIp = new InfoServicioHistorial();
                            $servicioHistorialIp->setServicioId($objServicioAdicional);
                            $servicioHistorialIp->setObservacion("Se cancelo el Servicio <br>".$ipCancelar['mensaje']);
                            $servicioHistorialIp->setMotivoId($motivoObj->getId());
                            $servicioHistorialIp->setEstado("Cancel");
                            $servicioHistorialIp->setUsrCreacion($usrCreacion);
                            $servicioHistorialIp->setFeCreacion(new \DateTime('now'));
                            $servicioHistorialIp->setIpCreacion($ipCreacion);
                            $servicioHistorialIp->setAccion($accionObj->getNombreAccion());
                            $this->emComercial->persist($servicioHistorialIp);
                            $this->emComercial->flush();

                            $mensaje = $mensaje . "<br>" . $ipCancelar['mensaje'];
                        }
                    }//foreach($arrayIpCancelar as $ipCancelar)

                    if($flagAdicional == 1)
                    {
                        throw new \Exception($mensaje . "<br>" . $mensajeAdicional);
                    }
                }//if($intIpFijasActivas > 0 && $arrayFinal['status'] == 'OK')
            }
            else
            {
                if($strMarcaOlt == "TELLION")
                {
                    //verifico si el olt esta aprovisionando el CNR
                    $objDetalleElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                  ->findOneBy(array('detalleNombre' => 'OLT MIGRADO CNR',
                                                                                    'elementoId'    => $interfaceElemento->getElementoId()->getId()));
                    if($objDetalleElemento)
                    {
                        $ejecutaLdap = "SI";
                    }
                }
                else 
                {
                    $ejecutaLdap = "SI";
                }
                $flagProd1 = 0;
                for($i=0;$i<count($planDet);$i++)
                {
                    foreach($prodIp as $productoIp)
                    {
                        if($planDet[$i]->getProductoId() == $productoIp->getId())
                        {
                            $prodIpPlan = $productoIp;
                            $flagProd1  = 1;
                            break;
                        }
                    }

                    for($j=0;$j<count($prodInternet);$j++)
                    {
                        if($planDet[$i]->getProductoId() == $prodInternet[$j]->getId())
                        {
                            $producto = $prodInternet[$j];
                            break;
                        }
                    }

                }

                //validar que no sea plan 100 100            
                $caractEdicionLimitada = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                           ->findOneBy(array("descripcionCaracteristica" => "EDICION LIMITADA",
                                                                             "estado"                    => "Activo"));

                $planCaractEdicionLimitada = $this->emComercial->getRepository('schemaBundle:InfoPlanCaracteristica')
                                                               ->findOneBy(array( "planId"            => $servicio->getPlanId()->getId(),
                                                                                  "caracteristicaId"  => $caractEdicionLimitada->getId(),
                                                                                  "estado"            => $servicio->getPlanId()->getEstado()));
                if($planCaractEdicionLimitada)
                {
                    //se valida caracteristicas edicion limitada y ejecución de ldap
                    if($planCaractEdicionLimitada->getValor() == "SI" && $ejecutaLdap == "SI")
                    {
                        $flagProd1=0;
                    }
                }        

                $servProdCaractIndiceCliente = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "INDICE CLIENTE", $producto);
                $spcSpid                     = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "SPID", $producto);
                $spcMacOnt                   = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "MAC ONT", $producto);

                if($flagProd1==0)
                {
                    $arrayParametros = array(
                                                'servicioTecnico'   => $servicioTecnico,
                                                'interfaceElemento' => $interfaceElemento,
                                                'modeloElemento'    => $modeloElemento,
                                                'spcIndiceCliente'  => $servProdCaractIndiceCliente,
                                                'login'             => $login,
                                                'spcSpid'           => $spcSpid,
                                                'spcMacOnt'         => $spcMacOnt,
                                                'idEmpresa'         => $idEmpresa
                                            );
                    //no tiene ips
                    $respuestaArray = $this->cancelarServicioMdSinIp($arrayParametros);
                    $status         = $respuestaArray[0]['status'];
                    $mensaje        = $respuestaArray[0]['mensaje'];
                }//if($flagProd1==0)
                else
                {
                    if($strMarcaOlt == "HUAWEI" || $objDetalleElemento)
                    {
                        //obtener caracteristica scope
                        $spcScope = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "SCOPE", $prodIpPlan);

                        if(!$spcScope)
                        {
                            //obtener ip fija
                            $ipFija = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                           ->findOneBy(array("servicioId"=>$servicio->getId(),"estado"=>"Activo"));

                            //buscar scopes
                            $arrayScopeOlt = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')
                                                                     ->getScopePorIpFija($ipFija->getIp(), $servicioTecnico->getElementoId());

                            if (!$arrayScopeOlt)
                            {   
                                $arrayFinal[] = array('status'=>"ERROR", 'mensaje'=>"Ip Fija no pertenece a un Scope! <br>"
                                                                                  . "Favor Comunicarse con el Dep. Gepon!");
                                return $arrayFinal;
                            }

                            $scope = $arrayScopeOlt['NOMBRE_SCOPE'];
                        }
                        else
                        {
                            $scope = $spcScope->getValor();
                        }
                    }

                    //tiene ips en el plan
                    $arrayParametros = array(
                                                'servicioTecnico'   => $servicioTecnico,
                                                'interfaceElemento' => $interfaceElemento,
                                                'modeloElemento'    => $modeloElemento,
                                                'producto'          => $producto,
                                                'login'             => $login,
                                                'spcIndiceCliente'  => $servProdCaractIndiceCliente,
                                                'spcSpid'           => $spcSpid,
                                                'spcMacOnt'         => $spcMacOnt,
                                                'scope'             => $scope,
                                                'idEmpresa'         => $idEmpresa,
                                                'ipCreacion'        => $ipCreacion,
                                                'usrCreacion'       => $usrCreacion
                                            );
                    $respuestaArray = $this->cancelarServicioMdConIp($arrayParametros);
                    $status         = $respuestaArray[0]['status'];
                    $mensaje        = $respuestaArray[0]['mensaje'];

                    //buscar ips adicionales en el punto
                    $punto          = $servicio->getPuntoId();
                    $serviciosPunto = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                        ->findBy(array("puntoId"=>$punto, "estado"=>"Activo"));
                    for($k=0;$k<count($serviciosPunto);$k++)
                    {
                        $servicioPunto = $serviciosPunto[$k];
                        if($servicioPunto->getId() != $servicio->getId())
                        {
                            $flagProdServicioPunto1=0;

                            if($servicioPunto->getProductoId())
                            {
                                foreach($prodIp as $productoIp)
                                {
                                    if($servicioPunto->getProductoId()->getId() == $productoIp->getId())
                                    {
                                        $flagProdServicioPunto1 = 1;
                                        break;
                                    }
                                }
                            }//if($servicioPunto->getProductoId())
                            else
                            {
                                $planCabServicioPunto = $servicioPunto->getPlanId();
                                if($planCabServicioPunto)
                                {
                                    $planDetServicioPunto = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                                                              ->findBy(array("planId"=>$planCabServicioPunto->getId()));
                                    for($i=0;$i<count($planDetServicioPunto);$i++)
                                    {
                                        foreach($prodIp as $productoIp)
                                        {
                                            if($planDetServicioPunto[$i]->getProductoId() == $productoIp->getId())
                                            {
                                                $flagProdServicioPunto1 = 1;
                                                break;
                                            }
                                        }
                                        for($j=0;$j<count($prodInternet);$j++)
                                        {
                                            if($planDetServicioPunto[$i]->getProductoId() == $prodInternet[$j]->getId())
                                            {
                                                $producto = $prodInternet[$j];
                                                break;
                                            }
                                        }
                                    }//for($i=0;$i<count($planDetServicioPunto);$i++)
                                }//if($planCabServicioPunto)
                            }//else

                            if($flagProdServicioPunto1!=0)
                            {
                                $spcMac = $this->servicioGeneral->getServicioProductoCaracteristica($servicioPunto,"MAC", $producto);
                                //se agrega validación para realizar la recuperacion solo en caso de ser un olt modelo HW ó aprovisionar Ips con CNR
                                if($modeloElemento->getNombreModeloElemento()=="MA5608T" || $objDetalleElemento)
                                {
                                    //obtener caracteristica scope
                                    $spcScopeAdi = $this->servicioGeneral->getServicioProductoCaracteristica($servicioPunto, "SCOPE", $producto);

                                    if(!$spcScopeAdi)
                                    {
                                        //obtener ip fija
                                        $ipFija = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                       ->findOneBy(array("servicioId"=>$servicioPunto->getId(),"estado"=>"Activo"));

                                        //buscar scopes
                                        $arrayScopeOlt = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')
                                                                                 ->getScopePorIpFija($ipFija->getIp(), 
                                                                                                     $servicioTecnico->getElementoId());

                                        if (!$arrayScopeOlt)
                                        {   
                                            $arrayFinal[] = array('status'=>"ERROR", 'mensaje'=>"Ip Fija Adicional no pertenece a un Scope! <br>"
                                                                                              . "Favor Comunicarse con el Dep. Gepon!");
                                            return $arrayFinal;
                                        }

                                        $scopeAdicional = $arrayScopeOlt['NOMBRE_SCOPE'];
                                    }
                                    else
                                    {
                                        $scopeAdicional = $spcScopeAdi->getValor();
                                    }
                                }
                                $arrParametrosCancel = array(
                                                                'servicioTecnico'   => $servicioTecnico,
                                                                'modeloElemento'    => $modeloElemento,
                                                                'interfaceElemento' => $interfaceElemento,
                                                                'producto'          => $producto,
                                                                'servicio'          => $servicioPunto,
                                                                'spcMac'            => $spcMac,
                                                                'scope'             => $scopeAdicional
                                                            );

                                //desconfigurar la ip adicional
                                $respuestaArrayAdicional = $this->cancelarServicioIp($arrParametrosCancel);
                                $statusAdicional         = $respuestaArrayAdicional[0]['status'];

                                if($statusAdicional=="ERROR")
                                {
                                    $arrayFinal[] = array('status'=>"ERROR", 'mensaje'=>$respuestaArrayAdicional[0]['mensaje']);
                                    return $arrayFinal;
                                }
                                //se agrega cancelación de servicios adicionales
                                $servicioPunto->setEstado("Cancel");
                                $this->emComercial->persist($servicioPunto);
                                $this->emComercial->flush();

                                //historial del servicio
                                $servicioHistorialIp = new InfoServicioHistorial();
                                $servicioHistorialIp->setServicioId($servicioPunto);
                                $servicioHistorialIp->setObservacion("Se cancelo el Servicio");
                                $servicioHistorialIp->setMotivoId($motivoObj->getId());
                                $servicioHistorialIp->setEstado("Cancel");
                                $servicioHistorialIp->setUsrCreacion($usrCreacion);
                                $servicioHistorialIp->setFeCreacion(new \DateTime('now'));
                                $servicioHistorialIp->setIpCreacion($ipCreacion);
                                $servicioHistorialIp->setAccion($accionObj->getNombreAccion());
                                $this->emComercial->persist($servicioHistorialIp);
                                $this->emComercial->flush();
                            }
                        }//if($servicioPunto->getId()!=$servicio->getId())
                    }//cierre for ips
                }//else
            }

            if($status=="OK")
            {
                //crear solicitud para retiro de equipo (ont y wifi)
                $tipoSolicitud = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                   ->findOneBy(array( "descripcionSolicitud" => "SOLICITUD RETIRO EQUIPO", "estado"=>"Activo"));
                $detalleSolicitud = new InfoDetalleSolicitud();
                $detalleSolicitud->setServicioId($servicio);
                $detalleSolicitud->setTipoSolicitudId($tipoSolicitud);
                $detalleSolicitud->setEstado("AsignadoTarea");
                $detalleSolicitud->setUsrCreacion($usrCreacion);
                $detalleSolicitud->setFeCreacion(new \DateTime('now'));
                $detalleSolicitud->setObservacion("SOLICITA RETIRO DE EQUIPO POR CANCELACION DEL SERVICIO");
                $this->emComercial->persist($detalleSolicitud);

                //crear las caract para la solicitud de retiro de equipo
                $entityAdmiCaracteristica = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')->find(360);

                $arrayParams['interfaceElementoConectorId']    = $servicioTecnico->getInterfaceElementoConectorId();
                $arrayParams['arrayData']                      = array();
                $arrayElementosServicio                        = $this->emInfraestructura
                                                                      ->getRepository('schemaBundle:InfoElemento')
                                                                      ->getElementosClienteByInterface($arrayParams);

                $arrayParametrosSmartWifi                                   = array();
                $arrayParametrosSmartWifi['intInterfaceElementoConectorId'] = $servicioTecnico->getInterfaceElementoClienteId();
                $arrayParametrosSmartWifi['strTipoSmartWifi']               = 'RentaSmartWifi';
                $arrayParametrosSmartWifi['arrayData']                      = array();
                $arrayElementosSmartWifi                                    = $this->emInfraestructura
                                                                                   ->getRepository('schemaBundle:InfoElemento')
                                                                                   ->getElementosSmartWifiByInterface($arrayParametrosSmartWifi);

                $arrayElementoRetiroEquipos                                 = array_merge($arrayElementosServicio,$arrayElementosSmartWifi);

                foreach($arrayElementoRetiroEquipos as $objElementoServicio)
                {
                    //valor del ont
                    $objEntityCaract= new InfoDetalleSolCaract();
                    $objEntityCaract->setCaracteristicaId($entityAdmiCaracteristica);
                    $objEntityCaract->setDetalleSolicitudId($detalleSolicitud);
                    $objEntityCaract->setValor($objElementoServicio->getId());
                    $objEntityCaract->setEstado("AsignadoTarea");
                    $objEntityCaract->setUsrCreacion($usrCreacion);
                    $objEntityCaract->setFeCreacion(new \DateTime('now'));
                    $this->emComercial->persist($objEntityCaract);
                    $this->emComercial->flush();
                }

                //******Se consultan los servicios del punto y se valida si existe un servicio de NetlifeCam Activo*****//
                if(is_object($servicio->getPuntoId()))
                {
                    $objInfoServicios = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                          ->findBy(array('puntoId' => $servicio->getPuntoId()->getId(),
                                                                         "estado" => "Activo"));

                    foreach($objInfoServicios as $idxInfoServicios)
                    {
                        $intIdProducto = "";
                        if(is_object($idxInfoServicios->getProductoId()))
                        {
                            $intIdProducto = $idxInfoServicios->getProductoId()->getId();

                            //Se consulta si el punto tiene servicios de NetlifeCam activos
                            $arrayParametrosProductoPermitido = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                                                  ->getOne('PROYECTO NETLIFECAM',
                                                                                           'INFRAESTRUCTURA',
                                                                                           'ACTIVACION PARA NETLIFECAM',
                                                                                           "PRODUCTO CONFIGURADO PARA REGISTRAR ELEMENTO",
                                                                                           $intIdProducto,
                                                                                           '',
                                                                                           '',
                                                                                           '',
                                                                                           '',
                                                                                           "18");

                            if(isset($arrayParametrosProductoPermitido["valor1"]) && !empty($arrayParametrosProductoPermitido["valor1"]))
                            {
                                //Se consulta si el servicio esta Activo y de ser asi se asocia la serie de la camara en la solicitud
                                $objServicioTecnico = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                           ->findOneBy(array("servicioId" => $idxInfoServicios->getId()));

                                $intElementoClienteId = "";

                                if (is_object($objServicioTecnico) && $objServicioTecnico->getElementoClienteId())
                                {
                                    $intElementoClienteId = $objServicioTecnico->getElementoClienteId();

                                    $objEntityCaract= new InfoDetalleSolCaract();
                                    $objEntityCaract->setCaracteristicaId($entityAdmiCaracteristica);
                                    $objEntityCaract->setDetalleSolicitudId($detalleSolicitud);
                                    $objEntityCaract->setValor($intElementoClienteId);
                                    $objEntityCaract->setEstado("AsignadoTarea");
                                    $objEntityCaract->setUsrCreacion($usrCreacion);
                                    $objEntityCaract->setFeCreacion(new \DateTime('now'));
                                    $this->emComercial->persist($objEntityCaract);
                                    $this->emComercial->flush();
                                }
                            }
                        }
                    }
                }

                //obtener tarea
                $entityProceso = $this->emSoporte->getRepository('schemaBundle:AdmiProceso')->findOneByNombreProceso("SOLICITAR RETIRO EQUIPO");
                $entityTareas  = $this->emSoporte->getRepository('schemaBundle:AdmiTarea')->findTareasActivasByProceso($entityProceso->getId());
                $entityTarea   = $entityTareas[0];

                //grabar nuevo info_detalle para la solicitud de retiro de equipo
                $entityDetalle = new InfoDetalle();
                $entityDetalle->setDetalleSolicitudId($detalleSolicitud->getId());
                $entityDetalle->setTareaId($entityTarea);
                $entityDetalle->setLongitud($servicio->getPuntoId()->getLongitud());
                $entityDetalle->setLatitud($servicio->getPuntoId()->getLatitud());
                $entityDetalle->setPesoPresupuestado(0);
                $entityDetalle->setValorPresupuestado(0);
                $entityDetalle->setIpCreacion($ipCreacion);
                $entityDetalle->setFeCreacion(new \DateTime('now'));
                $entityDetalle->setUsrCreacion($usrCreacion);
                $entityDetalle->setFeSolicitada(new \DateTime('now'));
                $this->emSoporte->persist($entityDetalle);
                $this->emSoporte->flush();

                //obtenemos el persona empresa rol del usuario
                $personaEmpresaRolUsr = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                             ->find($idPersonaEmpresaRol);

                //buscamos datos del dept, persona
                $departamento = $this->emGeneral->getRepository('schemaBundle:AdmiDepartamento')->find($personaEmpresaRolUsr->getDepartamentoId());
                $persona      = $personaEmpresaRolUsr->getPersonaId();

                //grabamos soporte.info_detalle_asignacion
                $detalleAsignacion = new InfoDetalleAsignacion();
                $detalleAsignacion->setDetalleId($entityDetalle);
                $detalleAsignacion->setAsignadoId($departamento->getId());
                $detalleAsignacion->setAsignadoNombre($departamento->getNombreDepartamento());
                $detalleAsignacion->setRefAsignadoId($persona->getId());
                if($persona->getRazonSocial()=="")
                {
                    $nombre = $persona->getNombres()." ".$persona->getApellidos();
                }
                else
                {
                    $nombre = $persona->getRazonSocial();
                }
                $detalleAsignacion->setRefAsignadoNombre($nombre);
                $detalleAsignacion->setPersonaEmpresaRolId($personaEmpresaRolUsr->getId());
                $detalleAsignacion->setTipoAsignado("EMPLEADO");
                $detalleAsignacion->setUsrCreacion($usrCreacion);
                $detalleAsignacion->setFeCreacion(new \DateTime('now'));
                $detalleAsignacion->setIpCreacion($ipCreacion);
                $this->emSoporte->persist($detalleAsignacion);
                $this->emSoporte->flush();

                //Se ingresa el historial de la tarea
                if(is_object($entityDetalle))
                {
                    $arrayParametrosHist["intDetalleId"] = $entityDetalle->getId();            
                    $intDetalleId                        = $arrayParametrosHist["intDetalleId"];
                }

                $arrayParametrosHist["strObservacion"]  = "Tarea Asignada";
                $arrayParametrosHist["strEstadoActual"] = "Asignada";     
                $arrayParametrosHist["strAccion"]       = "Asignada";

                $this->serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);              

                $afectadoNombre = "";
                $puntoId        = "";
                $puntoLogin     = "";
                if($servicio->getPuntoId()->getNombrePunto())
                {
                    $afectadoNombre = $servicio->getPuntoId()->getNombrePunto();
                }

                if($servicio->getPuntoId())
                {
                    $puntoId = $servicio->getPuntoId()->getId();
                    $puntoLogin = $servicio->getPuntoId()->getLogin();
                }

                // se graba en la DB_SOPORTE.INFO_CRITERIO_AFECTADO
                $criterio = new InfoCriterioAfectado();
                $criterio->setId(1);
                $criterio->setDetalleId($entityDetalle);
                $criterio->setCriterio("Clientes");
                $criterio->setOpcion("Cliente: " . $afectadoNombre . " | OPCION: Punto Cliente");
                $criterio->setFeCreacion(new \DateTime('now'));
                $criterio->setUsrCreacion($usrCreacion);
                $criterio->setIpCreacion($ipCreacion);
                $this->emSoporte->persist($criterio);
                $this->emSoporte->flush();

                // se graba en la DB_SOPORTE.INFO_PARTE_AFECTADA
                $afectado = new InfoParteAfectada();
                $afectado->setTipoAfectado("Cliente");
                $afectado->setDetalleId($entityDetalle->getId());
                $afectado->setCriterioAfectadoId($criterio->getId());
                $afectado->setAfectadoId($puntoId);
                $afectado->setFeIniIncidencia(new \DateTime('now'));
                $afectado->setAfectadoNombre($puntoLogin);
                $afectado->setAfectadoDescripcion($afectadoNombre);
                $afectado->setFeCreacion(new \DateTime('now'));
                $afectado->setUsrCreacion($usrCreacion);
                $afectado->setIpCreacion($ipCreacion);
                $this->emSoporte->persist($afectado);
                $this->emSoporte->flush();

                //crear historial para la solicitud
                $historialSolicitud = new InfoDetalleSolHist();
                $historialSolicitud->setDetalleSolicitudId($detalleSolicitud);
                $historialSolicitud->setEstado("AsignadoTarea");
                $historialSolicitud->setObservacion("GENERACION AUTOMATICA DE SOLICITUD RETIRO DE EQUIPO POR CANCELACION DEL SERIVICIO");
                $historialSolicitud->setUsrCreacion($usrCreacion);
                $historialSolicitud->setFeCreacion(new \DateTime('now'));
                $historialSolicitud->setIpCreacion($ipCreacion);
                $this->emComercial->persist($historialSolicitud);
                //------------------------------------------------------------------------------------------------

                //eliminar todas las ips que tenia ese servicio
                $infoIp = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')->findBy(array( "servicioId" => $servicio->getId()));
                for($i=0;$i<count($infoIp);$i++)
                {
                    $datoIp = $infoIp[$i];

                    $datoIp->setEstado("Eliminado");
                    $this->emInfraestructura->persist($datoIp);
                }
                $this->emInfraestructura->flush();
                //------------------------------------------------------------------------------------------------
                if (is_object($objIpWan))
                {
                    $objIpWan->setEstado("Eliminado");
                    $this->emInfraestructura->persist($objIpWan);
                }
                $flagProd     = 0 ;
                $contProdPref = 0;
                //verificar prod preferencial
                $planServicio = $servicio->getPlanId();
                if($planServicio!="" || $planServicio!=null)
                {
                    $arrayPlanDetServicio   = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                                                ->findBy(array( "planId" => $planServicio->getId()));
                    for($i=0;$i<count($arrayPlanDetServicio);$i++)
                    {
                        $intIdProdServicio1     = $arrayPlanDetServicio[$i]->getProductoId();
                        $objProductoServicio1   = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($intIdProdServicio1);

                        if($objProductoServicio1->getEsPreferencia()=="SI")
                        {
                            $flagProd  = 1;
                        }
                    }
                }
                else
                {
                    $intIdProdServicio1     = $servicio->getProductoId();
                    $objProductoServicio1   = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($intIdProdServicio1);

                    if($objProductoServicio1->getEsPreferencia()=="SI")
                    {
                        $flagProd=1;
                    }
                }

                //bandera para ver si plan tiene correo
                $flagCorreo            = 0;
                $servProdCaractUsuario = null;
                $caracteristicaUsuario = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                           ->findOneBy(array("descripcionCaracteristica" => "USUARIO", 
                                                                             "estado"                      => "Activo"));
                $productoCorreo        = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                            ->findOneBy(array("nombreTecnico" => "CORREO",
                                                                              "empresaCod"    => $idEmpresa, 
                                                                              "estado"        => "Activo"));
                if(is_object($productoCorreo))
                {
                    $prodCaracUsuario = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                ->findOneBy(array("productoId" => $productoCorreo->getId(), 
                                                                  "caracteristicaId" => $caracteristicaUsuario->getId(), 
                                                                  "estado"=>"Activo"));
                }

                //verificar si existe otro producto preferencial
                if($flagProd==1)
                {
                    $puntoPref = $servicio->getPuntoId();
                    $serviciosPunto1 = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                         ->findBy(array( "puntoId" => $puntoPref->getId()));
                    for($i=0;$i<count($serviciosPunto1);$i++)
                    {
                        $serv1 = $serviciosPunto1[$i];
                        if (!in_array($serv1->getEstado(),$arrayEstadoNoValidados))
                        {
                            $plan = $serv1->getPlanId();
                            if($plan!="" || $plan!=null)
                            {
                                $planDet = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                                             ->findBy(array( "planId" => $plan->getId()));
                                for($j=0;$j<count($planDet);$j++)
                                {
                                    $prodServicio = $planDet[$j]->getProductoId();

                                    $productoServicio = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($prodServicio);

                                    if($productoServicio->getEsPreferencia()=="SI")
                                    {
                                        $contProdPref++;
                                    }

                                    if($productoServicio->getNombreTecnico()=="CORREO")
                                    {
                                        $flagCorreo = 1;
                                    }
                                }
                            }
                            else
                            {
                                $prodServicio = $serv1->getProductoId();
                                $productoServicio = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($prodServicio);

                                if($productoServicio->getEsPreferencia()=="SI")
                                {
                                    $contProdPref++;
                                }
                            }
                        }
                    }
                }
                $boolFalse = false;
                $arrayServiciosNetlifezone = array();
                // Invocamos al metodo de detencion simultaneo de productos adicionales
                $strEstadoActual = $servicio->getEstado();
                $strPlanServicio = $servicio->getPlanId();
                if (!empty($strPlanServicio))
                {
                    $arrayDatosDetener = array(
                        "idPunto"      => $servicio->getPuntoId()->getId(),
                        "idServicio"   => $servicio->getId(),
                        "estadoActual" => $strEstadoActual,
                        "estado"       => "Anulado",
                        "observacion"  => "Se anula el producto en simultaneo con la cancelacion del servicio de internet",
                        "usuario"      => $usrCreacion,
                        "ipCreacion"   => $ipCreacion,
                        "idEmpresa"    => $idEmpresa,
                        "idPersonaRol"   => $idPersonaEmpresaRol,
                        "idDepartamento" => $intIdDepartamento
                    );
                    $this->serviceCoordinar2->cancelacionSimulServicios($arrayDatosDetener);
                }
                //se cancelan los servicios por producto preferencial
                if($flagProd==1 && $contProdPref<2)
                {
                    $punto          = $servicio->getPuntoId();
                    $serviciosPunto = $this->emComercial->getRepository('schemaBundle:InfoServicio')->findBy(array( "puntoId" => $punto->getId()));
                    for($i=0;$i<count($serviciosPunto);$i++)
                    {
                        $serv = $serviciosPunto[$i];
                        $strEstadoServicioInicial = $serv->getEstado();
                        if($serv->getEstado()=="Activo" || $serv->getEstado()=="In-Corte" || $serv->getEstado()=="In-Temp")
                        {
                            $strActualizaServicioNuevoAntivirus = "NO";
                            if($serv->getId() == $servicio->getId())
                            {
                                $servicio->setEstado("Cancel");
                                $this->emComercial->persist($servicio);
                                $this->emComercial->flush();

                                //historial del servicio
                                $servicioHistorial = new InfoServicioHistorial();
                                $servicioHistorial->setServicioId($servicio);
                                if($objUltimaMilla->getNombreTipoMedio()=="Radio")
                                {
                                    $servicioHistorial->setObservacion("Se cancelo el Servicio sin ejecucion de scripts");
                                }
                                else
                                {
                                    $servicioHistorial->setObservacion("Se cancelo el Servicio");
                                }
                                $servicioHistorial->setMotivoId($motivoObj->getId());
                                $servicioHistorial->setEstado("Cancel");
                                $servicioHistorial->setUsrCreacion($usrCreacion);
                                $servicioHistorial->setFeCreacion(new \DateTime('now'));
                                $servicioHistorial->setIpCreacion($ipCreacion);
                                $servicioHistorial->setAccion($accionObj->getNombreAccion());
                                $this->emComercial->persist($servicioHistorial);
                                $this->emComercial->flush();

                                //se eliminan correos del servidor pop
                                if($flagCorreo==1)
                                {
                                    $servProdCaractUsuario = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                  ->findBy(array("servicioId"                => $servicio->getId(), 
                                                                                 "productoCaracterisiticaId" => $prodCaracUsuario->getId(), 
                                                                                 "estado"                    => "Activo"));
                                    for($x=0;$x<count($servProdCaractUsuario);$x++)
                                    {
                                        $spcUsuario         = $servProdCaractUsuario[$x];
                                        $usuario            = $spcUsuario->getValor();
                                        $comando            = "java -jar -Djava.security.egd=file:/dev/./urandom ".
                                                              "/home/telcos/src/telconet/tecnicoBundle/batch/GestionCuentasCorreos.jar 'E' '".
                                                              $usuario."' ' ' ";
                                        $salidaCorreo       = shell_exec($comando);
                                        $posCorreo          = strpos($salidaCorreo, "{"); 
                                        $jsonObjCorreo      = substr($salidaCorreo, $posCorreo);
                                        $resultadJsonCorreo = json_decode($jsonObjCorreo);
                                        $statusCorreo       = $resultadJsonCorreo->status;
                                        if($statusCorreo=="error")
                                        {
                                            //historial del servicio
                                            $servicioHistorial = new InfoServicioHistorial();
                                            $servicioHistorial->setServicioId($servicio);
                                            $servicioHistorial->setObservacion("No se elimino el correo: ".$usuario.", del Servidor Pop");
                                            $servicioHistorial->setMotivoId($motivoObj->getId());
                                            $servicioHistorial->setEstado($servicio->getEstado());
                                            $servicioHistorial->setUsrCreacion($usrCreacion);
                                            $servicioHistorial->setFeCreacion(new \DateTime('now'));
                                            $servicioHistorial->setIpCreacion($ipCreacion);
                                            $this->emComercial->persist($servicioHistorial);
                                            $this->emComercial->flush();
                                        }
                                    }
                                }
                            }
                            else
                            {
                                $objProductoAdicional = $serv->getProductoId();
                                if(is_object($objProductoAdicional) 
                                    && strpos($objProductoAdicional->getDescripcionProducto(), 'I. PROTEGIDO MULTI PAID') !== $boolFalse)
                                {
                                    $objSpcSuscriberId  = $this->servicioGeneral
                                                               ->getServicioProductoCaracteristica($serv, "SUSCRIBER_ID", $objProductoAdicional);
                                    if(is_object($objSpcSuscriberId))
                                    {
                                        $strActualizaServicioNuevoAntivirus = "SI";
                                    }
                                    else
                                    {
                                        $arrayParametrosProdCaract['strEstadoSpc'] = 'Pendiente';
                                        $objSpcSuscriberIdPen  = $this->servicioGeneral
                                            ->getServicioProductoCaracteristica($serv, "SUSCRIBER_ID", $objProductoAdicional, $arrayParametrosProdCaract);
                                        if(is_object($objSpcSuscriberIdPen))
                                        {
                                            $strActualizaServicioNuevoAntivirus = "SI";
                                            $strLicenciaActiva = 'NO';
                                        }
                                        else
                                        {
                                            $strActualizaServicioNuevoAntivirus = "NO";
                                            $strLicenciaActiva = 'SI';
                                        }
                                    }
                                }
                                else
                                {
                                    $strActualizaServicioNuevoAntivirus = "NO";
                                }

                                if($strActualizaServicioNuevoAntivirus === "SI")
                                {
                                    $arrayParamsLicencias   = array("strProceso"                => "CANCELACION_ANTIVIRUS",
                                                                    "strEscenario"              => "CANCELACION_PROD_ADICIONAL_X_INTERNET",
                                                                    "objServicio"               => $serv,
                                                                    "objPunto"                  => $serv->getPuntoId(),
                                                                    "strCodEmpresa"             => $idEmpresa,
                                                                    "objProductoIPMP"           => null,
                                                                    "strUsrCreacion"            => $usrCreacion,
                                                                    "strIpCreacion"             => $ipCreacion,
                                                                    "objAccion"                 => $accionObj,
                                                                    "objMotivo"                 => $motivoObj,
                                                                    "strEstadoServicioInicial"  => $strEstadoServicioInicial,
                                                                    "strLicenciaActiva"         => $strLicenciaActiva
                                                                    );
                                    $this->serviceLicenciasKaspersky->gestionarLicencias($arrayParamsLicencias);
                                }
                                else
                                {
                                    // CANCELAR NETCAM
                                    $arrayParamsBusqCaracServCam = array("intIdServicio"                => $serv->getId(),
                                                                         "strDescripcionCaracteristica" => "CAMARA 3DEYE",
                                                                         "strEstadoSpc"                 => "Activo");

                                    $objServCaractCam = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                          ->getCaracteristicaServicio($arrayParamsBusqCaracServCam);

                                    $arrayParamsBusqCaracServRol = array("intIdServicio"                => $serv->getId(),
                                                                         "strDescripcionCaracteristica" => "ROL 3DEYE",
                                                                         "strEstadoSpc"                 => "Activo");

                                    $objServCaractRol = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                          ->getCaracteristicaServicio($arrayParamsBusqCaracServRol);

                                    if(is_object($objServCaractCam) && is_object($objServCaractRol))
                                    {
                                        $arrayRespCancelarServicio =
                                                $this->servicePortalNetCam->cancelarServicioNetCam($serv->getId(), $accionObj->getId(), "S");
                                    }

                                    if($arrayRespCancelarServicio["strStatus"] == "ERROR")
                                    {
                                        throw new \Exception('NO SE PUDO CANCELAR EL SERVICIO. <br>
                                                                        Error:'.$arrayRespCancelarServicio["strMessage"]);
                                    }

                                    $serv->setEstado("Cancel");
                                    $this->emComercial->persist($serv);
                                    $this->emComercial->flush();

                                    //historial del servicio
                                    $servicioHistorial = new InfoServicioHistorial();
                                    $servicioHistorial->setServicioId($serv);
                                    $servicioHistorial->setObservacion("Se canceló el servicio");
                                    $servicioHistorial->setMotivoId($motivoObj->getId());
                                    $servicioHistorial->setEstado("Cancel");
                                    $servicioHistorial->setUsrCreacion($usrCreacion);
                                    $servicioHistorial->setFeCreacion(new \DateTime('now'));
                                    $servicioHistorial->setIpCreacion($ipCreacion);
                                    $servicioHistorial->setAccion($accionObj->getNombreAccion());
                                    $this->emComercial->persist($servicioHistorial);
                                    $this->emComercial->flush();
                                }
                            }
                            if($strActualizaServicioNuevoAntivirus === "NO")
                            {
                                //eliminar las caracteristicas del servicio
                                $servProdCaract = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                    ->findBy(array("servicioId" => $serv->getId()));

                                $arrayEstados = array('Activo','Pendiente','Cancelado');

                                foreach($servProdCaract as $servpc)
                                {  
                                    if(in_array($servpc->getEstado(),$arrayEstados)) 
                                    {
                                        $servpc->setEstado("Eliminado");
                                        $servpc->setFeUltMod(new \DateTime('now'));
                                        $servpc->setUsrUltMod($usrCreacion);
                                        $this->emComercial->persist($servpc);
                                        $this->emComercial->flush();
                                    }    

                                }
                            }

                            if (is_object($serv->getProductoId()) && ($serv->getProductoId()->getId() == $objProductoNz->getId()))
                            {
                                $arrayServiciosNetlifezone[] = $serv;
                            }
                        }
                        //se agregan estados para cancelacion de servicios adicionales no preferenciales
                        else if( $serv->getEstado()=="Factible"  || 
                                 $serv->getEstado()=="PreAsignacionInfoTecnica" || 
                                 $serv->getEstado()=="Asignada"  || 
                                 $serv->getEstado()=="Pendiente" ||
                                 $serv->getEstado()=="EnPruebas"
                               )
                        {
                            $serv->setEstado("Eliminado");
                            $this->emComercial->persist($serv);
                            $this->emComercial->flush();

                            //historial del servicio
                            $servicioHistorial = new InfoServicioHistorial();
                            $servicioHistorial->setServicioId($serv);
                            $servicioHistorial->setObservacion("Se elimino el Servicio");
                            $servicioHistorial->setMotivoId($motivoObj->getId());
                            $servicioHistorial->setEstado("Eliminado");
                            $servicioHistorial->setUsrCreacion($usrCreacion);
                            $servicioHistorial->setFeCreacion(new \DateTime('now'));
                            $servicioHistorial->setIpCreacion($ipCreacion);
                            $servicioHistorial->setAccion($accionObj->getNombreAccion());
                            $this->emComercial->persist($servicioHistorial);
                            $this->emComercial->flush();

                            //obtener ips fijas q tiene el servicio
                            $objInfoIp = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                              ->findOneBy(array("servicioId"=>$serv->getId(),"tipoIp"=>"FIJA"));
                            if ($objInfoIp)
                            {
                                $objInfoIp->setEstado("Eliminado");
                                $this->emInfraestructura->persist($objInfoIp);
                                $this->emInfraestructura->flush();
                            }
                        }


                    }//end for
                }
                else
                {
                    $servicio->setEstado("Cancel");
                    $this->emComercial->persist($servicio);
                    $this->emComercial->flush();

                    //historial del servicio
                    $servicioHistorial = new InfoServicioHistorial();
                    $servicioHistorial->setServicioId($servicio);
                    if($objUltimaMilla->getNombreTipoMedio()=="Radio")
                    {
                        $servicioHistorial->setObservacion("Se cancelo el Servicio sin ejecucion de scripts");
                    }
                    else
                    {
                        $servicioHistorial->setObservacion("Se cancelo el Servicio");
                    }
                    $servicioHistorial->setMotivoId($motivoObj->getId());
                    $servicioHistorial->setEstado("Cancel");
                    $servicioHistorial->setUsrCreacion($usrCreacion);
                    $servicioHistorial->setFeCreacion(new \DateTime('now'));
                    $servicioHistorial->setIpCreacion($ipCreacion);
                    $servicioHistorial->setAccion($accionObj->getNombreAccion());
                    $this->emComercial->persist($servicioHistorial);
                    $this->emComercial->flush();
                }
                //------------------------------------------------------------------------------------------------

                //revisar si es el ultimo servicio en el punto
                $puntoObj     = $servicio->getPuntoId();
                $servicios    = $this->emComercial->getRepository('schemaBundle:InfoServicio')->findBy(array( "puntoId" => $puntoObj->getId()));
                $numServicios = count($servicios);
                $cont         = 0;
                for($i=0;$i<count($servicios);$i++)
                {
                    $servicioEstado = $servicios[$i]->getEstado();
                    if($servicioEstado=="Cancel"        || 
                       $servicioEstado=="Cancel-SinEje" || 
                       $servicioEstado=="Anulado"       || 
                       $servicioEstado=="Eliminado"     ||
                       $servicioEstado=="Rechazada")
                    {
                        $cont++;
                    }
                }
                if($cont == ($numServicios))
                {
                    $puntoObj->setEstado("Cancelado");
                    $this->emComercial->persist($puntoObj);
                    $this->emComercial->flush();
                }

                //revisar los puntos si estan todos Cancelados
                $personaEmpresaRol = $puntoObj->getPersonaEmpresaRolId();
                $puntos            = $this->emComercial->getRepository('schemaBundle:InfoPunto')
                                                       ->findBy(array( "personaEmpresaRolId" => $personaEmpresaRol->getId()));
                $numPuntos         = count($puntos);
                $contPuntos        = 0;
                for($i=0;$i<count($puntos);$i++)
                {
                    $punto1 = $puntos[$i];

                    if($punto1->getEstado()=="Cancelado")
                    {
                        $contPuntos++;
                    }
                }
                if(($numPuntos) == $contPuntos)
                {
                    //se cancela el contrato
                    $contrato = $this->emComercial
                                     ->getRepository('schemaBundle:InfoContrato')
                                     ->findOneBy(array( "personaEmpresaRolId" => $personaEmpresaRol->getId(),
                                                        "estado"              => "Activo"));
                    $contrato->setEstado("Cancelado");
                    $this->emComercial->persist($contrato);
                    $this->emComercial->flush();

                    //se cancela el personaEmpresaRol
                    $personaEmpresaRol->setEstado("Cancelado");
                    $this->emComercial->persist($personaEmpresaRol);
                    $this->emComercial->flush();

                    //se ingresa un registro en el historial empresa persona rol
                    $personaHistorial = new InfoPersonaEmpresaRolHisto();
                    $personaHistorial->setPersonaEmpresaRolId($personaEmpresaRol);
                    $personaHistorial->setEstado("Cancelado");
                    $personaHistorial->setUsrCreacion($usrCreacion);
                    $personaHistorial->setFeCreacion(new \DateTime('now'));
                    $personaHistorial->setIpCreacion($ipCreacion);
                    $this->emComercial->persist($personaHistorial);
                    $this->emComercial->flush();

                    //se cancela el cliente
                    $persona = $personaEmpresaRol->getPersonaId();
                    $persona->setEstado("Cancelado");
                    $this->emComercial->persist($persona);
                    $this->emComercial->flush();                
                }
                //------------------------------------------------------------------------------------------------

                //desconectar puerto del splitter
                $interfaceSplitter = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                             ->find($servicioTecnico->getInterfaceElementoConectorId());
                $interfaceSplitter->setEstado("not connect");
                $this->emInfraestructura->persist($interfaceSplitter);
                $this->emInfraestructura->flush();

                //eliminar enlace splitterN2-ont
                $arrayEnlacesAnteriores = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                                  ->findBy(array("interfaceElementoIniId" => $interfaceSplitter->getId(),
                                                                                 "estado"                 => "Activo"));
                foreach( $arrayEnlacesAnteriores as $objEnlace )
                {
                    $objEnlace->setEstado("Eliminado");
                    $this->emInfraestructura->persist($objEnlace);
                    $this->emInfraestructura->flush();
                }

                //se eliminan elementos del servicio
                foreach($arrayElementosServicio as $objElementoServicio)
                {
                    $objElementoServicio->setEstado("Eliminado");
                    $this->emInfraestructura->persist($objElementoServicio);
                    $this->emInfraestructura->flush();

                    //SE REGISTRA EL TRACKING DEL ELEMENTO
                    $strSerieElemento = $objElementoServicio->getSerieFisica();

                    $arrayParametrosAuditoria["strNumeroSerie"]  = $strSerieElemento;
                    $arrayParametrosAuditoria["strEstadoTelcos"] = 'Eliminado';
                    $arrayParametrosAuditoria["strEstadoNaf"]    = 'Instalado';
                    $arrayParametrosAuditoria["strEstadoActivo"] = 'Cancelado';
                    $arrayParametrosAuditoria["strUbicacion"]    = 'Cliente';
                    $arrayParametrosAuditoria["strCodEmpresa"]   = '18';
                    $arrayParametrosAuditoria["strTransaccion"]  = 'Cancelacion Servicio';
                    $arrayParametrosAuditoria["intOficinaId"]    = 0;

                    //Se consulta el login del cliente
                    if(is_object($servicio))
                    {
                        $strLoginTrazabilidad                   = $servicio->getPuntoId()->getLogin();
                        $arrayParametrosAuditoria["strLogin"]   = $strLoginTrazabilidad;
                    }

                    $arrayParametrosAuditoria["strUsrCreacion"]  = $usrCreacion;

                    $this->serviceInfoElemento->ingresaAuditoriaElementos($arrayParametrosAuditoria);
                    ////

                    //historial del elemento
                    $objHistorialElemento = new InfoHistorialElemento();
                    $objHistorialElemento->setElementoId($objElementoServicio);
                    $objHistorialElemento->setObservacion("Se elimino el elemento por cancelacion de Servicio");
                    $objHistorialElemento->setEstadoElemento("Eliminado");
                    $objHistorialElemento->setUsrCreacion($usrCreacion);
                    $objHistorialElemento->setFeCreacion(new \DateTime('now'));
                    $objHistorialElemento->setIpCreacion($ipCreacion);
                    $this->emInfraestructura->persist($objHistorialElemento);
                    $this->emInfraestructura->flush();

                    //eliminar puertos elemento
                    $arrayInterfacesElemento = $this->emInfraestructura
                                                    ->getRepository('schemaBundle:InfoInterfaceElemento')
                                                    ->findBy(array("elementoId" => $objElementoServicio->getId()));

                    foreach($arrayInterfacesElemento as $objInterfaceElemento)
                    {
                        $objInterfaceElemento->setEstado("Eliminado");
                        $this->emInfraestructura->persist($objInterfaceElemento);
                        $this->emInfraestructura->flush();
                    }

                }

                //actualizar ips
                if($flagProd1==1)
                {
                    //obtener ips fijas q tiene el servicio
                    $ipsFijas = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                     ->findBy(array("servicioId"=>$servicioTecnico->getServicioId()->getId(),"tipoIp"=>"FIJA", "estado"=>"Activo"));
                    for($i=0;$i<count($ipsFijas);$i++)
                    {
                        $ipFija = $ipsFijas[$i];
                        $ipFija->setEstado("Eliminado");
                        $this->emInfraestructura->persist($ipFija);
                        $this->emInfraestructura->flush();
                    }
                }

                if( $ejecutaLdap =="SI")
                {
                    if($strPrefijoEmpresa == 'EN')
                    {
                        //envio al ldap
                        $objResultadoJsonLdap = $this->servicioGeneral->ejecutarComandoLdap("E", $servicio->getId(), $strPrefijoEmpresa); 
                    }
                    else 
                    {
                        //envio al ldap
                        $objResultadoJsonLdap = $this->servicioGeneral->ejecutarComandoLdap("E", $servicio->getId());            
                    }
                    if($objResultadoJsonLdap->status!="OK")
                    {
                        $mensaje = $mensaje . "<br>" . $objResultadoJsonLdap->mensaje;
                    }
                }

                //Cancelacion de servicio NETLIFE WIFI
                //Obtenemos los usuarios no procesados
                $arrayParametrosNetlifeZone   = array();
                $arrayParametrosNetlifeZone['intIdEmpresa']   = $idEmpresa;
                $arrayParametrosNetlifeZone['strUsrCreacion'] = $usrCreacion;
                $arrayParametrosNetlifeZone['strIpCreacion']  = $ipCreacion;
                $arrayParametrosNetlifeZone['arrayServiciosNetlifezone'] = $arrayServiciosNetlifezone;
                $this->cancelarServiciosNetlifeWifi($arrayParametrosNetlifeZone);
            }//cierre if status ok
            else
            {
                throw new \Exception("NO SE PUDO CANCELAR EL SERVICIO. <br>Error:".$mensaje);
            }

            //enviar mail
            $asunto  ="Cancelacion de Servicio: Retiro de Equipos ";

            $prefijo = "";

            $empresaGrupo = $this->emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')->find($idEmpresa);

            if($empresaGrupo)
            {
                $prefijo = $empresaGrupo->getPrefijo();
            }

            $this->servicioGeneral->enviarMailCancelarServicio( $asunto,
                                                                $servicio,
                                                                $motivoObj->getId(),
                                                                $elemento,
                                                                $interfaceElemento->getNombreInterfaceElemento(),
                                                                $servicioHistorial,
                                                                $usrCreacion,
                                                                $ipCreacion,
                                                                $prefijo );
            $strStatusCancelarServicioMd                                = "OK";
            $strMsjCancelarServicioMd                                   = "Se Cancelo el Cliente";
            $arrayDataConfirmacionTn['datos']['respuesta_confirmacion'] = "OK";

            //------------------------------------------INICIO: ACTUALIZACIÓN DEL ESTADO EN ARCGIS - MD
            if($status=="OK")
            {
                $strUltimaMilla    = $objUltimaMilla->getNombreTipoMedio();
                if(($strUltimaMilla === 'Fibra Optica')||($strUltimaMilla === 'FTTx')||($strUltimaMilla === 'FO'))
                {
                    //solo para servicios que tienen fibra o fttx como última milla
                    $strUsrCancelacion = $usrCreacion;
                    $strLoginPunto     = $servicio->getPuntoId()->getLogin();
                    $strPrefijoEmpresa = $arrayParametros['strPrefijoEmpresa'];

                    // PuertoSwitch y  NombreSwitch
                    $objInterfaceElemento   = $arrayParametros['interfaceElemento'];
                    $strNombreSwitch        = $elemento->getNombreElemento();
                    $strPuertoSwitch        = $interfaceElemento->getNombreInterfaceElemento();

                    //Se llama al procedimiento en la base 
                    $arrayParamServicio   = array(
                                        "strUsrCancelacion" => $strUsrCancelacion,
                                        "strNombreSwitch"   => $strNombreSwitch,
                                        "strPuertoSwitch"   => $strPuertoSwitch,
                                        "strLoginPunto"     => $strLoginPunto,
                                        "strPrefijo"        => $strPrefijoEmpresa,
                                        "strIpCreacion"     => $ipCreacion,
                                        "objServicioPunto"  => $servicio
                                        );
                                        
                    $this->inactivarUmARCGIS($arrayParamServicio);
                }
            }
            //------------------------------------------FIN: ACTUALIZACIÓN DEL ESTADO EN ARCGIS - MD
        }
        catch(\Exception $e)
        {
            $strStatusCancelarServicioMd    = "ERROR";
            $strMsjCancelarServicioMd       = $e->getMessage();
            $intDetalleId                   = 0;
            $strOcurrioException            = "SI";
        }
        
        $arrayFinal[]   = array('status'                    => $strStatusCancelarServicioMd, 
                                'mensaje'                   => $strMsjCancelarServicioMd, 
                                'intDetalleId'              => $intDetalleId,
                                'arrayDataConfirmacionTn'   => $arrayDataConfirmacionTn,
                                'strOcurrioException'       => $strOcurrioException);
        return $arrayFinal;
    }            
    
    /**
     * Funcion que sirve para la ejecucion de scripts sobre los elementos correspondientes
     * para cancelar un servicio de internet con ip
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.1. 11-04-2015
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.2. 21-07-2015
     * @since 1.0
     * @param Array $arrayParametros (servicioTecnico, interfaceElemento, producto, login, modeloElemento, spcIndiceCliente, spcSpid, spcMacOnt)
     * 
     * @author Alberto Arias <farias@telconet.ec>
     * @version 1.3 25-03-2022 Se modifica el proceso de cancelar a WS
     */
    public function cancelarServicioMdConIp($arrayParametros)
    {
        $servicioTecnico    = $arrayParametros['servicioTecnico'];
        $interfaceElemento  = $arrayParametros['interfaceElemento'];
        $producto           = $arrayParametros['producto'];
        $modeloElemento     = $arrayParametros['modeloElemento'];
        $login              = $arrayParametros['login'];
        $spcIndiceCliente   = $arrayParametros['spcIndiceCliente'];
        $spcSpid            = $arrayParametros['spcSpid'];
        $spcMacOnt          = $arrayParametros['spcMacOnt'];
        $scope              = $arrayParametros['scope'];
        $mensaje            = "";
        
        //*OBTENER SCRIPT--------------------------------------------------------*/
        $scriptArray    = $this->servicioGeneral->obtenerArregloScript("cancelarCliente",$modeloElemento);
        $idDocumento    = $scriptArray[0]->idDocumento;
        $usuario        = $scriptArray[0]->usuario;
        $protocolo      = $scriptArray[0]->protocolo;
        //*----------------------------------------------------------------------*/
        $strUsrCreacion        = $arrayParametros['usrCreacion'];
        $strIpCreacion         = $arrayParametros['ipCreacion'];
        $strIdEmpresa          = $arrayParametros['idEmpresa'];
        if($idDocumento == 0)
        {
            $respuestaArray[] = array('status' => 'ERROR', 'mensaje' => 'NO EXISTE TAREA');
            return $respuestaArray;
        }
        
        try
        {
            //Se agrega validacion de Olt para no ejecutar fisicamente la cancelación en caso de que se encuentre NO Operativos
            $entitydetalleElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                             ->findOneBy(array("elementoId"     => $interfaceElemento->getElementoId()->getId(), 
                                                                                "detalleNombre" => "OLT OPERATIVO"));
            if ($entitydetalleElemento)
            {
                if ($entitydetalleElemento->getDetalleValor() == "NO")
                {
                    $status         = "OK";
                    $mensaje        = "OK";
                    $arrayFinal[]   = array('status' => $status, 'mensaje' => $mensaje);
                    return $arrayFinal;
                }
            }
            
            //obtener ips fijas q tiene el servicio
            $ipsFijas = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                ->findOneBy(array("servicioId"=>$servicioTecnico->getServicioId()->getId(),
                                                  "tipoIp"=>"FIJA", "estado"=>"Activo"));
            $strMarcaOlt    = $modeloElemento->getMarcaElementoId()->getNombreMarcaElemento();
            if($modeloElemento->getNombreModeloElemento()=="EP-3116")
            {
                //obtener olt
                $olt = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                   ->find($interfaceElemento->getElementoId()->getId());

                //obtener indice cliente
                $servProdCaracIndiceCliente = $this->servicioGeneral
                                                   ->getServicioProductoCaracteristica($servicioTecnico->getServicioId(), 
                                                                                       "INDICE CLIENTE", $producto);
                //consultar si el olt tiene aprovisionamiento de ips en el CNR
                $objDetalleElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                              ->findOneBy(array('detalleNombre' =>'OLT MIGRADO CNR',
                                                                                'elementoId' => $interfaceElemento->getElementoId()->getId()));

                if (!$objDetalleElemento)
                {    
                    //ejecutar script de Cancelacion del servicio
                    $resultadJson = $this->cancelarServicioOlt($interfaceElemento, $servProdCaracIndiceCliente, $servicioTecnico, $idDocumento,$login);
                    $status = $resultadJson->status;
                    if($status=="OK")
                    {
                        //obtener indice cliente
                        $servProdCaracPool = $this->servicioGeneral
                                                  ->getServicioProductoCaracteristica($servicioTecnico->getServicioId(), "POOL IP", $producto);

                        if($servProdCaracPool)
                        {
                            $pool = $servProdCaracPool->getValor();
                        }
                        else
                        {
                            //obtener perfil
                            $servProdCaracPerfil = $this->servicioGeneral
                                                        ->getServicioProductoCaracteristica($servicioTecnico->getServicioId(), "PERFIL", $producto);

                            //configurar Ip fija
                            //*OBTENER SCRIPT--------------------------------------------------------*/
                            $scriptArray    = $this->servicioGeneral->obtenerArregloScript("obtenerVlanParaIpFija",$modeloElemento);
                            $idDocumentoVlan= $scriptArray[0]->idDocumento;
                            $usuario        = $scriptArray[0]->usuario;
                            $protocolo      = $scriptArray[0]->protocolo;
                            //*----------------------------------------------------------------------*/
                            $resultadJsonVlan = $this->getVlanParaIpFija($servicioTecnico, $usuario, $interfaceElemento, 
                                                                         $servProdCaracPerfil->getValor(), $idDocumentoVlan);
                            $statusVlan = $resultadJsonVlan->status;
                            if($statusVlan=="OK")
                            {
                                $vlan = $resultadJsonVlan->mensaje;

                                //*OBTENER SCRIPT--------------------------------------------------------*/
                                $scriptArray    = $this->servicioGeneral->obtenerArregloScript("obtenerPoolParaIpFija",$modeloElemento);
                                $idDocumentoPool= $scriptArray[0]->idDocumento;
                                $usuario        = $scriptArray[0]->usuario;
                                $protocolo      = $scriptArray[0]->protocolo;
                                //*----------------------------------------------------------------------*/
                                $resultadJsonPool = $this->getPoolParaIpFija($servicioTecnico, $usuario, $interfaceElemento, $vlan, $idDocumentoPool);
                                $statusPool = $resultadJsonPool->status;
                                $pool = $resultadJsonPool->mensaje;
                                if($pool == "")
                                {
                                    $respuestaFinal[] = array('status' => 'ERROR',
                                                              'mensaje' => 'No se encuentra creado el Pool de Ips en el OLT:'
                                                                        . '<b>' . $olt->getNombreElemento() . '</b>,<br>'
                                                                        . 'Favor notificar al Dep. de Gepon');
                                    return $respuestaFinal;
                                }
                            }
                            else
                            {
                                $respuestaFinal[] = array('status'=>'ERROR', 
                                                          'mensaje'=>'No se encuentra creada la Vlan en el OLT:'
                                                                   . '<b>'.$olt->getNombreElemento().'</b>,<br>'
                                                                   . 'Favor notificar al Dep. de Gepon');
                                return $respuestaFinal;
                            }
                        }

                        //obtener mac wifi
                        $servProdCaracMacWifi = $this->servicioGeneral
                                                     ->getServicioProductoCaracteristica($servicioTecnico->getServicioId(), "MAC WIFI", $producto);

                        if($servProdCaracMacWifi)
                        {
                            //cambiar formato de la mac
                            $macWifiNueva = $this->cambiarMac($servProdCaracMacWifi->getValor());
                        }
                        else
                        {
                            //obtener mac wifi
                            $servProdCaracMacWifi = $this->servicioGeneral
                                                         ->getServicioProductoCaracteristica($servicioTecnico->getServicioId(), "MAC", $producto);

                            if($servProdCaracMacWifi)
                            {
                                //cambiar formato de la mac
                                $macWifiNueva = $this->cambiarMac($servProdCaracMacWifi->getValor());
                            }
                            else
                            {
                                $respuestaFinal[] = array('status'=>'ERROR', 'mensaje'=>'NO EXISTE MAC DEL WIFI');
                                return $respuestaFinal;
                            }
                        }

                        //desconfigurar Ip fija
                        //*OBTENER SCRIPT--------------------------------------------------------*/
                        $scriptArray        = $this->servicioGeneral->obtenerArregloScript("desconfigurarIpFija",$modeloElemento);
                        $idDocumentoDesconf = $scriptArray[0]->idDocumento;
                        $usuario            = $scriptArray[0]->usuario;
                        $protocolo          = $scriptArray[0]->protocolo;
                        //*----------------------------------------------------------------------*/

                        if($ipsFijas)
                        {
                            $resultadJsonDesconf = $this->desconfigurarIpFija($interfaceElemento, $usuario, $pool, $ipsFijas->getIp(), 
                                                                          $macWifiNueva, $idDocumentoDesconf);
                            $statusDesconf = $resultadJsonDesconf->status;
                            if($statusDesconf=="OK")
                            {
                                $respuestaFinal[] = array('status'=>'OK', 'mensaje'=>'OK');
                                return $respuestaFinal;
                            }
                            else
                            {
                                $respuestaFinal[] = array('status'=>'ERROR', 'mensaje'=>'NO SE DESCONFIGURO IP FIJA');
                                return $respuestaFinal;
                            }
                        }
                        else
                        {
                            $respuestaFinal[] = array('status'=>'OK', 'mensaje'=>'OK');
                            return $respuestaFinal;
                        }
                    }
                    else
                    {
                        $mensaje = $resultadJson->mensaje;
                    }       
                }
                else
                {
                    //$spcMacWifi = $this->servicioGeneral->getServicioProductoCaracteristica($servicioTecnico->getServicioId(), "MAC WIFI", $producto);
                    
                    $parametrosCancelaIp = array('interfaceElemento'=> $interfaceElemento,
                                                 'modeloElemento'   => $modeloElemento,
                                                 'spcIndiceCliente' => $spcIndiceCliente,
                                                 'login'            => $login,
                                                 'spcMac'           => $spcMacOnt,
                                                 'scope'            => $scope,
                                                 'servicioTecnico'  => $servicioTecnico,
                                                 'producto'         => $producto);

                    $cancelaIpArray = $this->cancelarServicioIpTellionCnr($parametrosCancelaIp);
                    
                    $status  = $cancelaIpArray[0]['status'];
                    $mensaje = $cancelaIpArray[0]['mensaje'];
                    
                    if ($status!= 'OK')
                    {
                        $respuestaFinal[] = array('status'=>$status, 'mensaje'=>$mensaje);
                        return $respuestaFinal;
                    }                    
                }     
            }//if($modeloElemento->getNombreModeloElemento()=="EP-3116")
            else if($strMarcaOlt == "HUAWEI")
            {
                list($tarjeta, $puertoPon) = split('/',$interfaceElemento->getNombreInterfaceElemento());
                //obtener elemento SM
                $modeloElementoSm = $this->emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                          ->findOneBy(array("nombreModeloElemento"  => "SM-SCE 8000",
                                                            "estado"                => "Activo"));
                
                //obtener elemento sm
                $elementoSm = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                          ->findOneBy(array("modeloElementoId"=>$modeloElementoSm->getId()));
                
                //obtener objeto modelo cnr
                $modeloElementoCnr = $this->emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                          ->findOneBy(array("nombreModeloElemento"  => "CNR UCS C220",
                                                            "estado"                => "Activo"));

                //obtener elemento cnr
                $elementoCnr = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                          ->findOneBy(array("modeloElementoId"  => $modeloElementoCnr->getId(),
                                                            "estado"            => "Activo"));
                
                //2. OBTENER SPID (BASE)
                $spid = $spcSpid->getValor();
                
                $objServicio           = $servicioTecnico->getServicioId();
                //OBTENER SERIE ONT
                $intIdElementoCliente  = $servicioTecnico->getElementoClienteId();
                $objElementoCliente    = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($intIdElementoCliente);
                $strSerieOnt           = ($objElementoCliente)?$objElementoCliente->getSerieFisica():"";
                // OBTENER MAC ONT
                $objMacOnt             = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "MAC ONT", $producto);
                $strMacOnt              = ($objMacOnt)?$objMacOnt->getValor():"";
                $strNombreElementoOlt   = "";
                $strIpElementoOlt       = "";
                $intIdElementoOlt       = $servicioTecnico->getElementoId();
                $objElementoOlt         = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($intIdElementoOlt);
                if(is_object($objElementoOlt))
                {
                    $strNombreElementoOlt   = $objElementoOlt->getNombreElemento();
                }
                $objIpElementoOlt       = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                ->findOneBy(array("elementoId" => $intIdElementoOlt));
                if(is_object($objIpElementoOlt))
                {
                    $strIpElementoOlt   = $objIpElementoOlt->getIp();
                }
                $objSpcGemPort          = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "GEM-PORT", $producto);
                $strGemPort             = ($objSpcGemPort) ? $objSpcGemPort->getValor() : "";
                $objIndiceCliente       = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "INDICE CLIENTE", $producto);
                $strOntId               = ($objIndiceCliente) ? $objIndiceCliente->getValor() : "";
                //OBTENER SERVICE-PORT
                $objSpcSpid             = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "SPID", $producto);
                $strServicePort         = ($objSpcSpid) ? $objSpcSpid->getValor() : "";
                //OBTENER SERVICE-PROFILE
                $objSpcServiceProfile   = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "SERVICE-PROFILE", $producto);
                $strServiceProfile      = ($objSpcServiceProfile) ? $objSpcServiceProfile->getValor() : "";
                //OBTENER LINE-PROFILE-NAME
                $objSpcLineProfileName  = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "LINE-PROFILE-NAME", $producto);
                $strOntLineProfile      = ($objSpcLineProfileName) ? $objSpcLineProfileName->getValor() : "";
                //OBTENER TRAFFIC-TABLE
                $objSpcTrafficTable     = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "TRAFFIC-TABLE", $producto);
                $strTrafficTable        = ($objSpcTrafficTable)  ? $objSpcTrafficTable->getValor() : "";
                //OBTENER VLAN
                $objSpcVlan             = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "VLAN", $producto);
                $strVlan                = ($objSpcVlan)  ? $objSpcVlan->getValor() : "";
                //OBTENER TIPO DE NEGOCIO
                $strTipoNegocio         = $servicioTecnico->getServicioId()->getPuntoId()->getTipoNegocioId()->getNombreTipoNegocio();
                //OBTENER MAC WIFI
                $objSpcMacWifi          = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "MAC WIFI", $producto);
                $strMacWifi             = ($objSpcMacWifi)  ? $objSpcMacWifi->getValor() : "";
                //OBTENER SCOPE
                $objSpcScope            = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "SCOPE", $producto);
                $strScope               = ($objSpcScope)  ? $objSpcScope->getValor() : "";
                $arrayIpCancelar        = array();
                //OBTENER CAPACIDAD1
                $objCapacidad1          = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "CAPACIDAD1", $producto);
                $strCapacidad1          = ($objCapacidad1)  ? $objCapacidad1->getValor() : "";
                //OBTENER CAPACIDAD2
                $objCapacidad2          = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "CAPACIDAD2", $producto);
                $strCapacidad2          = ($objCapacidad2)  ? $objCapacidad2->getValor() : "";
                //OBTENER NOMBRE CLIENTE
                $objPersona             = $servicioTecnico->getServicioId()->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId();
                $strNombreCliente       = $objPersona->__toString();
                //OBTENER IDENTIFICACION
                $strIdentificacion      = $objPersona->getIdentificacionCliente();
                //OBTENER IPS ADICIONALES 
                $arrayServicios         = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                        ->findBy(
                                          array("puntoId" => $servicioTecnico->getServicioId()->getPuntoId()->getId(), 
                                                "estado" => "Activo"));
                $arrayProdIp            = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->findBy(
                                          array( "nombreTecnico" => "IP",
                                                 "empresaCod" => $strIdEmpresa,
                                                 "estado"  => "Activo"));
                $arrayDatosIp           = $this->servicioGeneral->getInfoIpsFijaPunto( $arrayServicios, $arrayProdIp, 
                                        $servicioTecnico->getServicioId(), 'Activo', 'Activo', $producto);

                //OBTENER LA CANTIDAD DE IPS ADICIONALES ACTIVAS
                $intIpsFijasActivas     = $arrayDatosIp['ip_fijas_activas'];

                 //1. OBTENER IP CPE ARP (OLT)
                // cambiar obtenerIpArp a llamada WS CONSULTAR_ARP
                $arrayDatosONT          = array(
                                                'serial_ont'       => $strSerieOnt,
                                                'mac_ont'          => $strMacOnt,
                                                'nombre_olt'       => $strNombreElementoOlt,
                                                'ip_olt'           => $strIpElementoOlt,
                                                'puerto_olt'       => $interfaceElemento->getNombreInterfaceElemento(),
                                                'modelo_olt'       => $modeloElemento->getNombreModeloElemento(),
                                                'gemport'          => $strGemPort,
                                                'estado_servicio'  => $servicioTecnico->getServicioId()->getEstado(),
                                                'service_profile'  => $strServiceProfile,
                                                'line_profile'     => $strOntLineProfile,
                                                'traffic_table'    => $strTrafficTable,
                                                'ont_id'           => $strOntId,
                                               );
                $arrayDatosMiddleware   = array(
                                                'nombre_cliente'        => $strNombreCliente,
                                                'login'                 => $login,
                                                'identificacion'        => $strIdentificacion,
                                                'datos'                 => $arrayDatosONT,
                                                'opcion'                => "CONSULTAR_ARP_LC",
                                                'ejecutaComando'        => $this->ejecutaComando,
                                                'usrCreacion'           => $strUsrCreacion,
                                                'ipCreacion'            => $strIpCreacion,
                                              );
                $objJsonIpsArp          = $this->rdaMiddleware->middleware(json_encode($arrayDatosMiddleware));
                $objJsonIpsArp          = json_decode(json_encode($objJsonIpsArp));

                // cambiar cancelarServicioOltHuawei a llamada WS CANCELAR
                $arrayDatosONT          = array(
                                                'serial_ont'       => $strSerieOnt,
                                                'mac_ont'          => $strMacOnt,
                                                'nombre_olt'       => $strNombreElementoOlt,
                                                'ip_olt'           => $strIpElementoOlt,
                                                'puerto_olt'       => $interfaceElemento->getNombreInterfaceElemento(),
                                                'modelo_olt'       => $modeloElemento->getNombreModeloElemento(),
                                                'ont_id'           => $strOntId,
                                                'service_port'     => $strServicePort,
                                                'gemport'          => $strGemPort,
                                                'service_profile'  => $strServiceProfile,
                                                'line_profile'     => $strOntLineProfile,
                                                'traffic_table'    => $strTrafficTable,
                                                'vlan'             => $strVlan,
                                                'estado_servicio'  => $servicioTecnico->getServicioId()->getEstado(),
                                                'ip'               => '',
                                                'ip_fijas_activas' => $intIpsFijasActivas,
                                                'tipo_negocio_actual' => $strTipoNegocio,
                                                'mac_wifi'         => $strMacWifi,
                                                'scope'            => $strScope,
                                                'ip_cancelar'      => $arrayIpCancelar,
                                                'capacidad_up'     => $strCapacidad1,
                                                'capacidad_down'   => $strCapacidad2,
                                               );
                $arrayDatosMiddleware   = array(
                                                'nombre_cliente'        => $strNombreCliente,
                                                'login'                 => $login,
                                                'identificacion'        => $strIdentificacion,
                                                'datos'                 => $arrayDatosONT,
                                                'opcion'                => "CANCELAR",
                                                'ejecutaComando'        => $this->ejecutaComando,
                                                'usrCreacion'           => $strUsrCreacion,
                                                'ipCreacion'            => $strIpCreacion,
                                              );
                $arrayRespuestaMiddleware = $this->rdaMiddleware->middleware(json_encode($arrayDatosMiddleware));
                $objJsonCancelar          = json_decode(json_encode($arrayRespuestaMiddleware));
                $status                   = $objJsonCancelar->status;                                
                
                if($status=="OK")
                {
                    if($objJsonIpsArp->status=="OK")
                    {
                        $strListadoIpsArp  = $objJsonIpsArp->data;
                        
                        //4. DESCONFIGURAR IP ARP (OLT)
                        $scriptArray4 = $this->servicioGeneral->obtenerArregloScript("desconfigurarIpArp",$modeloElemento);
                        $this->servicioGeneral->ejecutarComandoPersonalizadoMdDatos($servicioTecnico->getElementoId(), $scriptArray4[0]->usuario,
                                                                     $strListadoIpsArp, $scriptArray4[0]->idDocumento, "desconfigurarIpArp");
                    }//if($jsonIpsArp->status=="OK")
                    else
                    {
                        $mensaje = $mensaje . "No existian Ips Arp";
                    }
                    
                    $macOnt = $this->cambiarMac($spcMacOnt->getValor());
                    //5. OBTENER IPS ASOCIADAS A UN SUSCRIBER (SM-SCE 8000)
                    $scriptArray5 = $this->servicioGeneral->obtenerArregloScript("obtenerIpsSuscriber",$modeloElementoSm);
                    $jsonIpsSuscriber = $this->servicioGeneral->ejecutarComandoPersonalizadoMdDatos($elementoSm->getId(), $scriptArray5[0]->usuario, 
                                                                               $macOnt, $scriptArray5[0]->idDocumento, "getIpsSuscriber");
                    
                    //6. ELIMINAR MAPEO DE SUSCRIBER (SM-SCE 8000)
                    $scriptArray6 = $this->servicioGeneral->obtenerArregloScript("eliminarSuscriber",$modeloElementoSm);
                    $this->servicioGeneral->ejecutarComandoMdEjecucion($elementoSm->getId(), $scriptArray6[0]->usuario, 
                                                                       $macOnt, $scriptArray6[0]->idDocumento);
                    
                    if($jsonIpsSuscriber->status=="OK")
                    {
                        $listadoIpsSuscriber  = $jsonIpsSuscriber->mensaje;
                        
                        //7. ELIMINAR IP DINAMICA (CNR)
                        $scriptArray7 = $this->servicioGeneral->obtenerArregloScript("desconfigurarIpDinamica",$modeloElementoCnr);
                        $this->servicioGeneral->ejecutarComandoPersonalizadoMdDatos($elementoCnr->getId(), $scriptArray7[0]->usuario, 
                                                                   $listadoIpsSuscriber, $scriptArray7[0]->idDocumento, "desconfigurarIpDinamica");
                    }
                    
                    $strDatos = $scope.",".$ipsFijas->getIp().",".$ipsFijas->getIp();
                    
                    //8. ELIMINAR IP FIJA (CNR)
                    $scriptArray8 = $this->servicioGeneral->obtenerArregloScript("desconfigurarIpFija",$modeloElementoCnr);
                    $jsonCnr = $this->servicioGeneral->ejecutarComandoMdEjecucion($elementoCnr->getId(), $scriptArray8[0]->usuario, 
                                                                                  $strDatos, $scriptArray8[0]->idDocumento);

                    if($jsonCnr->status!="OK")
                    {
                        $status  = "ERROR";
                        $mensaje = "No se elimino Ip Fija del CNR, <br>Error:".$objJsonCancelar->mensaje;
                        throw new \Exception($mensaje);
                    }
                }
                else
                {
                    $status  = "ERROR";
                    $mensaje = $objJsonCancelar->mensaje;
                    throw new \Exception($mensaje);
                }
            }//else if($modeloElemento->getNombreModeloElemento()=="MA5608T")            
        }
        catch (\Exception $e) 
        {
            $status="ERROR";
            $mensaje = "ERROR,".$e->getMessage();
            $arrayFinal[] = array('status'=>"ERROR", 'mensaje'=>$mensaje);
            return $arrayFinal;
        }
        
        $respuestaFinal[] = array('status'=>$status, 'mensaje'=>$mensaje);
        return $respuestaFinal;
    }
    
    
     /**
     * Funcion que sirve para cancelar la ip en el olt tellion y en el cnr
     * 
     * @author John Vera  <javera@telconet.ec>
     * @version 1.0 21-09-2015
     * @param $arrayParametros (servicioTecnico, modeloElemento, interfaceElemento, producto, servicio, spcIndiceCliente, spcMac, scope)
     */
    public function cancelarServicioIpTellionCnr($arrayParametros)
    {
        
        $interfaceElemento      = $arrayParametros['interfaceElemento'];
        $modeloElemento         = $arrayParametros['modeloElemento'];
        $spcIndiceCliente       = $arrayParametros['spcIndiceCliente'];
        $login                  = $arrayParametros['login'];
        $spcMac                 = $arrayParametros['spcMac'];
        $scope                  = $arrayParametros['scope'];
        $servicioTecnico        = $arrayParametros['servicioTecnico'];
        $producto               = $arrayParametros['producto'];
                    
        //*OBTENER SCRIPT--------------------------------------------------------*/
        $scriptArray    = $this->servicioGeneral->obtenerArregloScript("cancelarCliente",$modeloElemento);
        $idDocumento    = $scriptArray[0]->idDocumento;
        $usuario        = $scriptArray[0]->usuario;
        $protocolo      = $scriptArray[0]->protocolo;
        //*----------------------------------------------------------------------*/
        
        $servicio = $this->emInfraestructura->getRepository('schemaBundle:InfoServicio')
                                            ->findOneById($servicioTecnico->getServicioId());

        //OLT-CANCELACION DE SERVICIO
        $resultadJson = $this->cancelarServicioOlt($interfaceElemento, $spcIndiceCliente, 
                                                   $interfaceElemento, $idDocumento, $login);

        if($resultadJson->status == "OK")
        {

            $status         = "OK";
            $mensaje        .= "El servicio se canceló correctamente";
            
        }
        else
        {
            $status  = "ERROR";
            $mensaje = $resultadJson->mensaje;
            $arrayFinal[]   = array('status'=>$status, 'mensaje'=>'Error al cancelar el servicio'.$mensaje);
            return $arrayFinal;
        }

        $parametrosCancelaIp = array('servicio' => $servicio,
                                     'scope'    => $scope,
                                     'producto' => $producto,
                                     'macOnt'   => $spcMac->getValor());
        
        $resultCancelaIp = $this->cancelarIpTellionCnr($parametrosCancelaIp);
        
        $statusIpCancel = $resultCancelaIp[0]['status'];
        if($statusIpCancel!="OK")
        {
            $status         = "ERROR";
            $mensaje        .= "Error al cancelar Ip".$resultCancelaIp[0]['mensaje'];
            $arrayFinal[]   = array('status'=>$status, 'mensaje'=>$mensaje);
            return $arrayFinal;
        }
        else
        {
            $status         = "OK";
            $mensaje        .= " La Ip canceló correctamente";           
        }

        $arrayFinal[]   = array('status'=>$status, 'mensaje'=>$mensaje);
        return $arrayFinal;
    }
    
     /**
     * Funcion que sirve para desconfigurar la ip dinamica del SM y del CNR
     * 
     * @author John Vera  <javera@telconet.ec>
     * @version 1.0 21-09-2015
     * @param $macOnt
     */
    public function desconfigurarIpDinamicaCnr($macOnt)
    {
        
        $modeloElementoSm = $this->emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                                    ->findOneBy(array("nombreModeloElemento"  => "SM-SCE 8000",
                                                                      "estado"                => "Activo"));                                                        
        //obtener elemento sm
        $elementoSm = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                  ->findOneBy(array("modeloElementoId"=>$modeloElementoSm->getId()));

        //obtener objeto modelo cnr
        $modeloElementoCnr = $this->emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                  ->findOneBy(array("nombreModeloElemento"  => "CNR UCS C220",
                                                    "estado"                => "Activo"));
        
        //obtener elemento cnr
        $elementoCnr = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                  ->findOneBy(array("modeloElementoId"  => $modeloElementoCnr->getId(),
                                                    "estado"            => "Activo"));
              
        $macOntFormateada = $this->cambiarMac($macOnt);
        //5. OBTENER IPS ASOCIADAS A UN SUSCRIBER (SM-SCE 8000)
        $scriptArray5 = $this->servicioGeneral->obtenerArregloScript("obtenerIpsSuscriber",$modeloElementoSm);
        $jsonIpsSuscriber = $this->servicioGeneral->ejecutarComandoPersonalizadoMdDatos($elementoSm->getId(), $scriptArray5[0]->usuario, 
                                                                   $macOntFormateada, $scriptArray5[0]->idDocumento, "getIpsSuscriber");

        //ELIMINAR MAPEO DE SUSCRIBER (SM-SCE 8000)
        if($jsonIpsSuscriber->status=="OK")
        {
            $listadoIpsSuscriber  = $jsonIpsSuscriber->mensaje;

            //7. ELIMINAR IP DINAMICA (CNR)
            $scriptArray7 = $this->servicioGeneral->obtenerArregloScript("desconfigurarIpDinamica",$modeloElementoCnr);
            $ipDinamicaDesconfigurada = $this->servicioGeneral->ejecutarComandoPersonalizadoMdDatos($elementoCnr->getId(), $scriptArray7[0]->usuario, 
                                                  $listadoIpsSuscriber, $scriptArray7[0]->idDocumento, "desconfigurarIpDinamica");
            $this->desconfigurarMacOntSm($macOnt);  
        }else
        {
             $arrayFinal[]   = array('status'=>'ERROR', 'mensaje'=>'No se desconfiguro Ip dinámica.');
             return $arrayFinal;
        }
        
        $arrayFinal[]   = array('status'=>'OK', 'mensaje'=>'Se desconfiguró la Ip dinámica');
        return $arrayFinal;
        
    }
    
     /**
     * Funcion que sirve para cancelar la ip en el olt tellion y en el cnr
     * 
     * @author John Vera  <javera@telconet.ec>
     * @version 1.0 21-09-2015
     * 
     * @author Antonio Ayala  <afayala@telconet.ec>
     * @version 1.1 22-02-2021  Se consulta el tipo de Ip para la el servicio seleccionado.
     * 
     * @param $arrayParametros 
     * objServicio
     * scope
     * objProductoIp
     * macOnt en formato aaaa.bbbb.cccc
     */
    
    public function cancelarIpTellionCnr($arrayParametros)
    {
        $servicio               = $arrayParametros['servicio'];
        $scope                  = $arrayParametros['scope'];
        $producto               = $arrayParametros['producto'];     
        $macOnt                 = $arrayParametros['macOnt'];
        
        $servicioTecnico = $this->emInfraestructura->getRepository('schemaBundle:InfoServicioTecnico')
                                                   ->findOneByServicioId($servicio->getId());
        //obtener objeto modelo cnr
        $modeloElementoCnr = $this->emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                                     ->findOneBy(array("nombreModeloElemento"  => "CNR UCS C220",
                                                                       "estado"                => "Activo"));

        //obtener elemento cnr
        $elementoCnr = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                               ->findOneBy(array("modeloElementoId"  => $modeloElementoCnr->getId(),
                                                                 "estado"            => "Activo"));
        $strTipoIp = 'FIJA';
        
        //Obtiene tipo de ip por el servicio (PRIVADA)
        $objTipoIp = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                            ->findOneBy(array("servicioId"=>$servicio->getId(),
                                                              "tipoIp"=>"PRIVADA",
                                                              "estado"=>"Activo"));
        if (is_object($objTipoIp))
        {
            $strTipoIp = $objTipoIp->getTipoIp();
        }
        
        //obtener ips fijas q tiene el servicio
        $ipsFijas = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                            ->findOneBy(array("servicioId"=>$servicio->getId(),
                                                              "tipoIp"=>$strTipoIp,
                                                              "estado"=>"Activo"));

        //consulto scope del servicio
        if (!$scope)
        {
            //obtener caracteristica scope
            $spcScope = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "SCOPE", $producto);
            if(!$spcScope)
            {

                if ($servicioTecnico)
                {
                    //buscar scopes
                    $arrayScopeOlt = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')
                                                             ->getScopePorIpFija($ipsFijas->getIp(), $servicioTecnico->getElementoId());
                }
                else
                {
                    $arrayScopeOlt = '';
                }                
                
                if(!$arrayScopeOlt)
                {
                    $arrayFinal[] = array('status' => "ERROR",
                                          'mensaje' => "Ip Fija " . $ipsFijas->getIp() . " no pertenece a un Scope! <br>"
                                                       . "Favor Comunicarse con el Dep. Gepon!");
                    return $arrayFinal;
                }

                $scope = $arrayScopeOlt['NOMBRE_SCOPE'];
            }
            else
            {
                $scope = $spcScope->getValor();
            }
        }

        $datos = $scope.",".$ipsFijas->getIp().",".$ipsFijas->getIp(); 

        //8. ELIMINAR IP FIJA (CNR)
        $scriptArray8 = $this->servicioGeneral->obtenerArregloScript("desconfigurarIpFija",$modeloElementoCnr);
        $jsonCnr = $this->servicioGeneral->ejecutarComandoMdEjecucion($elementoCnr->getId(), $scriptArray8[0]->usuario, 
                                                                      $datos, $scriptArray8[0]->idDocumento);
        if ($macOnt)
        {
            //desconfiguro todas las ips del SM
            $this->desconfigurarMacOntSm($macOnt);
        }
        
        if($jsonCnr->status!="OK")
        {
            $status  = "ERROR";
            $mensaje = "No se elimino Ip Fija del CNR, <br>Error:".$jsonCnr->mensaje.$mensaje;
            throw new \Exception($mensaje);
        }
        else
        {
            $status  = "OK";
            $mensaje = "Ip se canceló correctamente."; 
        }
        
        $arrayFinal[]   = array('status'=>$status, 'mensaje'=>$mensaje);
        return $arrayFinal;
    }
    
     /**
     * Funcion para desconfigurar la mac del ont en el subcriber manager
     * 
     * @author John Vera  <javera@telconet.ec>
     * @version 1.0 21-09-2015
     * @param $macOnt en formato aaaa.bbbb.cccc
     */
    
    public function desconfigurarMacOntSm($macOnt)
    {
        
        $modeloElementoSm = $this->emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                                    ->findOneBy(array("nombreModeloElemento" => "SM-SCE 8000",
                                                    "estado" => "Activo"));
        //obtener elemento sm
        $elementoSm = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                              ->findOneBy(array("modeloElementoId" => $modeloElementoSm->getId()));
        
        $macOnt = $this->cambiarMac($macOnt);

        //6. ELIMINAR MAPEO DE SUSCRIBER (SM-SCE 8000)
        $scriptArray6 = $this->servicioGeneral->obtenerArregloScript("eliminarSuscriber", $modeloElementoSm);
        $result = $this->servicioGeneral->ejecutarComandoMdEjecucion($elementoSm->getId(), $scriptArray6[0]->usuario, $macOnt,
                                                                     $scriptArray6[0]->idDocumento);
        
        return $result;
    }

    /**
     * Funcion que sirve para cancelar el servicio de ip adicional
     *
     * @author Richard Cabrera  <rcabrera@telconet.ec>
     * @version 1.2 06-02-2018  No se debe considerar la respuesta del proceso de eliminacion del suscriber manager de un servicio
     *
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.1 14-04-2015
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.2 21-07-2015
     * @since 1.0
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 28-05-2018 Se agrega validación para servicios IP Small Business
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 05-05-2020 Se elimina código innecesario para servicios de Ips Small Busines, ya que dichos servicios no siguen este flujo
     * 
     * @param $arrayParametros (servicioTecnico, modeloElemento, interfaceElemento, producto, servicio, spcIndiceCliente, spcMac, scope)
     */
    public function cancelarServicioIp($arrayParametros)
    {
        $servicioTecnico    = $arrayParametros['servicioTecnico'];
        $interfaceElemento  = $arrayParametros['interfaceElemento'];     
        $modeloElemento     = $arrayParametros['modeloElemento'];
        $producto           = $arrayParametros['producto'];
        $servicio           = $arrayParametros['servicio'];
        $spcIndiceCliente   = $arrayParametros['spcIndiceCliente'];
        $spcMac             = $arrayParametros['spcMac'];
        $scope              = $arrayParametros['scope'];
        $strEsAdicional     = $arrayParametros['esAdicional'];
        $mensaje            = "";
        try
        {
            //Se agrega validacion de Olt para no ejecutar fisicamente la cancelación en caso de que se encuentre NO Operativos
            $entitydetalleElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                             ->findOneBy(array("elementoId"     => $interfaceElemento->getElementoId()->getId(), 
                                                                                "detalleNombre" => "OLT OPERATIVO"));
            if ($entitydetalleElemento)
            {
                if ($entitydetalleElemento->getDetalleValor() == "NO")
                {
                    $status         = "OK";
                    $mensaje        = "OK";
                    $arrayFinal[]   = array('status' => $status, 'mensaje' => $mensaje);
                    return $arrayFinal;
                }
            }
            
            //obtener ips fijas q tiene el servicio
            $ipsFijas = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                             ->findBy(array("servicioId"=>$servicio->getId(),"tipoIp"=>"FIJA", "estado"=>"Activo"));
            $arrayProductoIp    = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                    ->findBy(array("nombreTecnico"=>"IP", "estado"=>"Activo"));
            
            if($modeloElemento->getNombreModeloElemento()=="EP-3116")
            {
                
                //consultar si el olt tiene aprovisionamiento de ips en el CNR
                $objDetalleElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                              ->findOneBy(array('detalleNombre' =>'OLT MIGRADO CNR',
                                                                                'elementoId' => $interfaceElemento->getElementoId()->getId()));

                if (!$objDetalleElemento)
                {
                    //obtener indice cliente
                    $servProdCaracPool = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "POOL IP", $producto);

                    if($servProdCaracPool){
                        $pool = $servProdCaracPool->getValor();
                    }
                    else{
                        //obtener perfil
                        $servProdCaracPerfil = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "PERFIL", $producto);

                        if($servProdCaracPerfil){
                            $perfil = $servProdCaracPerfil->getValor();
                        }
                        else{
                            //buscar perfil
                            $perfil = $this->emComercial->getRepository("schemaBundle:InfoPlanCab")
                                                        ->getPerfilByPlanIdAndPuntoId("no", "", $servicio->getPuntoId()->getId());
                        }
                        if($perfil){
                            //configurar Ip fija
                            //*OBTENER SCRIPT--------------------------------------------------------*/
                            $scriptArray = $this->servicioGeneral->obtenerArregloScript("obtenerVlanParaIpFija",$modeloElemento);
                            $idDocumentoVlan= $scriptArray[0]->idDocumento;
                            $usuario= $scriptArray[0]->usuario;
                            $protocolo= $scriptArray[0]->protocolo;
                            //*----------------------------------------------------------------------*/
                            $resultadJsonVlan = $this->getVlanParaIpFija($servicioTecnico, $usuario, $interfaceElemento, $perfil, $idDocumentoVlan);
                            $statusVlan = $resultadJsonVlan->status;
                            if($statusVlan=="OK"){
                                $vlan = $resultadJsonVlan->mensaje;

                                //*OBTENER SCRIPT--------------------------------------------------------*/
                                $scriptArray = $this->servicioGeneral->obtenerArregloScript("obtenerPoolParaIpFija",$modeloElemento);
                                $idDocumentoPool= $scriptArray[0]->idDocumento;
                                $usuario= $scriptArray[0]->usuario;
                                $protocolo= $scriptArray[0]->protocolo;
                                //*----------------------------------------------------------------------*/
                                $resultadJsonPool = $this->getPoolParaIpFija($servicioTecnico, $usuario, $interfaceElemento, $vlan, $idDocumentoPool);
                                $statusPool = $resultadJsonPool->status;
                                $pool = $resultadJsonPool->mensaje;
                                if($pool==""){
                                    $respuestaFinal[] = array('status'=>'ERROR',
                                                              'mensaje'=>'No existe Pool en el Olt para la vlan:'.$vlan.',<br>'
                                                                       . 'Favor notificar al Dep. de Gepon');
                                    return $respuestaFinal;
                                }
                            }
                            else{
                                $respuestaFinal[] = array('status'=>'ERROR',
                                                          'mensaje'=>'No existe Vlan en el Olt para el perfil:'.$perfil.',<br>'
                                                                   . 'Favor notificar al Dep. de Gepon');
                                return $respuestaFinal;
                            }
                        }
                        else{
                            $respuestaFinal[] = array('status'=>'ERROR',
                                                      'mensaje'=>'No existe perfil para el servicio adicional,<br>'
                                                               . 'Favor notificar al Dep. de Sistemas');
                            return $respuestaFinal;
                        }
                    }

                    //obtener mac wifi
                    $servProdCaracMacWifi = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "MAC", $producto);
                    if($servProdCaracMacWifi){
                        //cambiar formato de la mac
                        $macWifiNueva = $this->cambiarMac($servProdCaracMacWifi->getValor());
                    }
                    else{
                        //obtener mac wifi
                        $servProdCaracMacWifi = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "MAC WIFI", $producto);

                        if($servProdCaracMacWifi){
                            //cambiar formato de la mac
                            $macWifiNueva = $this->cambiarMac($servProdCaracMacWifi->getValor());
                        }
                        else{
                            $respuestaFinal[] = array('status'=>'ERROR', 'mensaje'=>'NO EXISTE MAC DEL CLIENTE,'.$servicio->getId());
                            return $respuestaFinal;
                        }
                    }

                    //desconfigurar Ip fija
                    //*OBTENER SCRIPT--------------------------------------------------------*/
                    $scriptArray = $this->servicioGeneral->obtenerArregloScript("desconfigurarIpFija",$modeloElemento);
                    $idDocumentoDesconf = $scriptArray[0]->idDocumento;
                    $usuario= $scriptArray[0]->usuario;
                    $protocolo= $scriptArray[0]->protocolo;
                    //*----------------------------------------------------------------------*/

                    for($i=0;$i<count($ipsFijas);$i++){
                        $ipFija = $ipsFijas[$i];
                        $resultadJsonDesconf = $this->desconfigurarIpFija($interfaceElemento, $usuario, $pool, $ipFija->getIp(),
                                                                          $macWifiNueva, $idDocumentoDesconf);
                        $status = $resultadJsonDesconf->status;
                        if($status=="OK"){
                            $respuestaFinal[] = array('status'=>'OK', 'mensaje'=>'OK');

                            $ipFija->setEstado("Eliminado");
                            $this->emInfraestructura->persist($ipFija);
                            $this->emInfraestructura->flush();

                            $mensaje = $resultadJsonDesconf->mensaje;
                        }
                        else{
                            $respuestaFinal[] = array('status'=>'ERROR',
                                                      'mensaje'=>'No se pudo desconfigurar ip fija:'.$ipFija->getIp().
                                                                 'para un Servicio Adicional, <br> Favor Notificar a Sistemas!');
                            return $respuestaFinal;
                        }
                    }
                }
                else
                {

                    $parametrosCancelaIp = array('servicio' => $servicio,
                                                 'scope'    => $scope,
                                                 'producto' => $producto,
                                                 'macOnt'  =>  $macOnt);
        
                    $resultCancelaIp = $this->cancelarIpTellionCnr($parametrosCancelaIp);
                    $statusIpCancel = $resultCancelaIp[0]['status'];
                    $mensajeIpCancel = $resultCancelaIp[0]['mensaje'];
                    
                    if($statusIpCancel!="OK")
                    {
                        $respuestaFinal[] = array('status'=>"ERROR", 
                                                  'mensaje'=>"No se elimino Ip Fija del CNR, <br>Error:".$mensajeIpCancel.$mensaje);
                        return $respuestaFinal;
                    }
                }
            }
            else if($modeloElemento->getNombreModeloElemento()=="MA5608T")
            {
                //obtener elemento SM
                $modeloElementoSm = $this->emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                          ->findOneBy(array("nombreModeloElemento"  => "SM-SCE 8000",
                                                            "estado"                => "Activo"));
               
                //obtener elemento sm
                $elementoSm = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                          ->findOneBy(array("modeloElementoId"=>$modeloElementoSm->getId()));
               
                //obtener objeto modelo cnr
                $modeloElementoCnr = $this->emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                          ->findOneBy(array("nombreModeloElemento"  => "CNR UCS C220",
                                                            "estado"                => "Activo"));

                //obtener elemento cnr
                $elementoCnr = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                          ->findOneBy(array("modeloElementoId"  => $modeloElementoCnr->getId(),
                                                            "estado"            => "Activo"));
               
                //dividir interface para obtener tarjeta y puerto pon
                list($tarjeta, $puertoPon) = split('/',$interfaceElemento->getNombreInterfaceElemento());
                
                //se debe seleccionar el puerto ont mayor
                $interfaceCliente = $this->migracionService->getInterfaceClienteServicioIp($servicio->getPuntoId(), 
                                                                              $servicioTecnico, 
                                                                              $arrayProductoIp,
                                                                              "SI");
                if ('NO' == $strEsAdicional)
                {
                    if ($servicioTecnico->getInterfaceElementoClienteId())
                    {
                        $interfaceCliente = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                 ->find($servicioTecnico->getInterfaceElementoClienteId());
                    }
                    else
                    {
                        //se debe seleccionar el puerto ont mayor
                        $interfaceCliente = $this->migracionService->getInterfaceClienteServicioIp($servicio->getPuntoId(), 
                                                                              $servicioTecnico, 
                                                                              $arrayProductoIp,
                                                                              "SI");
                    }
                }

                if($interfaceCliente)
                {
                    if ('NO' != $strEsAdicional)
                    {
                    //1. DESCONFIGURAR IP FIJA (OLT)
                    $datosOlt = $tarjeta.",".$puertoPon.",".$spcIndiceCliente->getValor().",".$interfaceCliente->getNombreInterfaceElemento();
                    $scriptArray1 = $this->servicioGeneral->obtenerArregloScript("desconfigurarIpFija",$modeloElemento);
                    $jsonDesconfOlt = $this->servicioGeneral->ejecutarComandoMdEjecucion($servicioTecnico->getElementoId(),
                                                                       $scriptArray1[0]->usuario, $datosOlt, $scriptArray1[0]->idDocumento);
                    }

                    if($jsonDesconfOlt->status=="OK" || 'NO' == $strEsAdicional)
                    {
                        $mac = $this->cambiarMac($spcMac->getValor());
                        //2. ELIMINAR MAPEO DE SUSCRIBER (SM-SCE 8000)
                        $scriptArray2 = $this->servicioGeneral->obtenerArregloScript("eliminarSuscriber",$modeloElementoSm);
                        $jsonSm = $this->servicioGeneral->ejecutarComandoMdEjecucion($elementoSm->getId(), $scriptArray2[0]->usuario,
                                                                           $mac, $scriptArray2[0]->idDocumento);
                        if("ERROR"=="OK")
                        {
                            $mensaje = "Suscriber Manager: No se pudo Eliminar Suscriptor, Mensaje: ".$jsonSm->mensaje;
                            throw new \Exception($mensaje);
                        }

                        $datos = $scope.",".$ipsFijas[0]->getIp().",".$ipsFijas[0]->getIp();

                        //3. ELIMINAR IP FIJA (CNR)
                        $scriptArray3 = $this->servicioGeneral->obtenerArregloScript("desconfigurarIpFija",$modeloElementoCnr);
                        $jsonCnr = $this->servicioGeneral->ejecutarComandoMdEjecucion($elementoCnr->getId(), $scriptArray3[0]->usuario,
                                                                       $datos, $scriptArray3[0]->idDocumento);
                       
                        if($jsonCnr->status!="OK")
                        {
                            $mensaje = "CNR: No se pudo Desconfigurar Ip, Mensaje: ".$jsonCnr->mensaje;
                            $arrayFinal[] = array('status'=>"ERROR", 'mensaje'=>$mensaje);
                            return $arrayFinal;
                        }

                        $status = "OK";
                    }
                    else
                    {
                        $mensaje = "No se pudo eliminar la Ip Fija, <br> Error:".$jsonDesconfOlt->mensaje;
                        throw new \Exception($mensaje);
                    }
                }
                else
                {
                    $mensaje = "No se ha definido el Puerto ONT, Favor comunicarse con el Dep. Sistemas para su regularizacion! <br>"
                             . "Indicar en el ticket el numero del puerto ONT habilitado para dicha Ip Fija!";
                    throw new \Exception($mensaje);
                }
            }
        }
        catch (\Exception $e) {
            $status="ERROR";
            $mensaje = "ERROR,".$e->getMessage();
            $arrayFinal[] = array('status'=>"ERROR", 'mensaje'=>$mensaje);
            return $arrayFinal;
        }
       
        $respuestaFinal[] = array('status'=>$status, 'mensaje'=>$mensaje);
        return $respuestaFinal;
    }
    
    /**
     * Funcion que sirve para cancelar ips adicionales por medio del middleware
     * 
     * @author Francisco Adum <fadum@netlife.net.ec>
     * @version 1.0 27-06-2017
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 07-05-2018 Se agrega flujo para cancelación de IPs Adicionales Small Business
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 23-05-2018 Se agrega validaciones para cancelación de IPs Adicionales Small Business con OLTs Tellion
     * 
     * @author Modificado: Jesús Bozada <jbozada@telconet.ec>
     * @version 1.4 23-07-2018 Se agrega programación para ejecutar proceso para servicios con tecnología ZTE 
     * @since 1.3
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.5 04-05-2020 Se agrega invocación a la función obtenerParametrosProductosTnGpon por reestructuración de servicios Small Business
     *                          y así se obtiene la variable $strNombreTecnicoInternet y $arrayProdIp
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.6 11-05-2020 Se unifica las validaciones por marca y no por modelo de olt
     * 
     * @param $parametros [ servicio, servicioInternet, servicioTecnico, interfaceElemento, producto, ipFija, idEmpresa, usrCreacion, ipCreacion,
     *                      tieneIpFijaActiva, controlIpFija]
     */
    public function cancelarIpFijaAdicional($parametros)
    {
        $servicio           = $parametros['servicio'];
        $servicioInternet   = $parametros['servicioInternet'];
        $servicioTecnico    = $parametros['servicioTecnico'];
        $interfaceElemento  = $parametros['interfaceElemento'];
        $producto           = $parametros['producto'];
        $productoIp         = $parametros['productoIp'];
        $objIpFija          = $parametros['ipFija'];
        $macIpFija          = $parametros['macIpFija'];
        $idEmpresa          = $parametros['idEmpresa'];
        $usrCreacion        = $parametros['usrCreacion'];
        $ipCreacion         = $parametros['ipCreacion'];
        $controlIpFija      = $parametros['controlIpFija'];
        $strCapacidad1      = "";
        $strCapacidad2      = "";
        $strPrefijoEmpresa        = $parametros['strPrefijoEmpresa'];
        $strNombreTecnicoInternet = "";
        $strTipoProcesoMiddleware = "CANCELAR_IP_FIJA";
        $strGemPort         = "";
        $strServiceProfile  = "";
        $strLineProfile     = "";
        $strTraffic         = "";
        $strIndiceCliente   = "";
        $strSpid            = "";
        $strVlan            = "";
        $strMacWifi         = "";
        
        if($strPrefijoEmpresa === "TN" || $strPrefijoEmpresa === "TNP")
        {
            if(is_object($servicioInternet) && is_object($servicioInternet->getProductoId()))
            {
                $objProdInternet            = $servicioInternet->getProductoId();
                $strNombreTecnicoInternet   = $objProdInternet->getNombreTecnico();
                $arrayParamsInfoProds       = array("strValor1ParamsProdsTnGpon"    => "PRODUCTOS_RELACIONADOS_INTERNET_IP",
                                                    "strCodEmpresa"                 => $idEmpresa,
                                                    "intIdProductoInternet"         => $objProdInternet->getId());
                $arrayInfoMapeoProds        = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                ->obtenerParametrosProductosTnGpon($arrayParamsInfoProds);
                if(isset($arrayInfoMapeoProds) && !empty($arrayInfoMapeoProds))
                {
                    foreach($arrayInfoMapeoProds as $arrayInfoProd)
                    {
                        $intIdProductoIp    = $arrayInfoProd["intIdProdIp"];
                        $strCaractRelProdIp = $arrayInfoProd["strCaractRelProdIp"];
                        $objProdIPSB        = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($intIdProductoIp);
                        $arrayProdIp[]      = $objProdIPSB;
                    }
                }
            }
        }
        else
        {
            $arrayProdIp    = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->findBy(array( "nombreTecnico" => "IP", 
                                                                                                            "empresaCod"    => $idEmpresa, 
                                                                                                            "estado"        => "Activo"));
        }

        if(isset($strCaractRelProdIp) && !empty($strCaractRelProdIp) &&
           isset($intIdProductoIp) && !empty($intIdProductoIp) )
        {
            $arrayServiciosPunto = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                ->createQueryBuilder('s')
                ->innerJoin('schemaBundle:InfoServicioProdCaract', 'car', 'WITH', 'car.servicioId = s.id')
                ->innerJoin('schemaBundle:AdmiProductoCaracteristica', 'pc', 'WITH',
                        'pc.id = car.productoCaracterisiticaId')
                ->innerJoin('schemaBundle:AdmiCaracteristica', 'c', 'WITH', 'c.id = pc.caracteristicaId')
                ->where('s.puntoId = :puntoId')
                ->andWhere("s.productoId = :productoId")
                ->andWhere("car.valor = :idServioInt")
                ->andWhere("c.descripcionCaracteristica = :desCaracteristica")
                ->andWhere("car.estado = :estadoActivo")
                ->andWhere("c.estado = :estadoServicio")
                ->setParameter('puntoId', $servicioInternet->getPuntoId()->getId())
                ->setParameter('productoId', $intIdProductoIp)
                ->setParameter('idServioInt', $servicioInternet->getId())
                ->setParameter('desCaracteristica', $strCaractRelProdIp)
                ->setParameter('estadoActivo', 'Activo')
                ->setParameter('estadoServicio', 'Activo')
                ->getQuery()
                ->getResult();
            $arrayServiciosPunto[] = $servicioInternet;
        }
        else
        {
            $arrayServiciosPunto   = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                    ->findBy(array("puntoId" => $servicioInternet->getPuntoId()->getId(), "estado" => "Activo"));
        }

        $strTipoNegocio     = $servicio->getPuntoId()->getTipoNegocioId()->getNombreTipoNegocio();
        $elemento           = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($servicioTecnico->getElementoId());
        $objIpElemento      = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                    ->findOneBy(array('elementoId' => $elemento->getId(), 'estado' => 'Activo'));
        $modeloElemento     = $elemento->getModeloElementoId();
        $strMarcaOlt        = $modeloElemento->getMarcaElementoId()->getNombreMarcaElemento();

        //OBTENER NOMBRE CLIENTE
        $objPersona             = $servicio->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId();
        $strNombreCliente       = ($objPersona->getRazonSocial() != "") ? $objPersona->getRazonSocial() : 
                                                            $objPersona->getNombres()." ".$objPersona->getApellidos();

        //OBTENER IDENTIFICACION
        $strIdentificacion      = $objPersona->getIdentificacionCliente();

        //OBTENER LOGIN
        $strLogin               = $servicio->getPuntoId()->getLogin();

        //OBTENER INDICE CLIENTE
        $spcIndiceCliente       = $this->servicioGeneral->getServicioProductoCaracteristica($servicioInternet, "INDICE CLIENTE", $producto);
        if($spcIndiceCliente)
        {
            $strIndiceCliente   = $spcIndiceCliente->getValor();
        }

        //OBTENER IPS ADICIONALES 
        $arrayDatosIp       = $this->servicioGeneral
                                ->getInfoIpsFijaPunto(  $arrayServiciosPunto, $arrayProdIp, 
                                                        $servicioInternet, 'Activo', 'Activo', $producto);

        //OBTENER LA CANTIDAD DE IPS ADICIONALES ACTIVAS
        $intIpsFijasActivas = $arrayDatosIp['ip_fijas_activas'];

        //OBTENER SERIE ONT
        $elementoCliente    = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                    ->find($servicioTecnico->getElementoClienteId());
        $strSerieOnt        = $elementoCliente->getSerieFisica();

        //obtener scope
        $objCaractScope = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "SCOPE", $servicio->getProductoId());

        if($strMarcaOlt == "TELLION")
        {
            //OBTENER MAC ADICIONAL
            $strMacWifi = $macIpFija;

            $spcMacOnt = $this->servicioGeneral->getServicioProductoCaracteristica($servicioInternet, "MAC ONT", $producto);
            if($spcMacOnt)
            {
                $macIpFija = $spcMacOnt->getValor();
            }

            //OBTENER PERFIL
            $spcLineProfile = $this->servicioGeneral->getServicioProductoCaracteristica($servicioInternet, "PERFIL", $producto);
            if(is_object($spcLineProfile))
            {
                $strLineProfile = $spcLineProfile->getValor();
                $arrayPerfil    = explode("_", $strLineProfile);
                if($strNombreTecnicoInternet === "INTERNET SMALL BUSINESS")
                {
                    $strLineProfile  = $arrayPerfil[0]."_".$arrayPerfil[1]."_".$arrayPerfil[2];
                }
                else
                {
                    $strLineProfile  = $arrayPerfil[0]."_".$arrayPerfil[1];
                }
            }
        }
        else if($strMarcaOlt == "HUAWEI")
        {
            //OBTENER SERVICE-PORT
            $spcSpid        = $this->servicioGeneral->getServicioProductoCaracteristica($servicioInternet, "SPID", $producto);
            if($spcSpid)
            {
                $strSpid    = $spcSpid->getValor();
            }

            //OBTENER SERVICE PROFILE
            $spcServiceProfile = $this->servicioGeneral->getServicioProductoCaracteristica($servicioInternet, "SERVICE-PROFILE", $producto);
            if($spcServiceProfile)
            {
                $strServiceProfile = $spcServiceProfile->getValor();
            }

            //OBTENER LINE PROFILE NAME
            $spcLineProfile = $this->servicioGeneral->getServicioProductoCaracteristica($servicioInternet, "LINE-PROFILE-NAME", $producto);
            if($spcLineProfile)
            {
                $strLineProfile = $spcLineProfile->getValor();
            }

            //OBTENER VLAN
            $spcVlan        = $this->servicioGeneral->getServicioProductoCaracteristica($servicioInternet, "VLAN", $producto);
            if($spcVlan)
            {
                $strVlan    = $spcVlan->getValor();
            }

            //OBTENER GEM-PORT
            $spcGemPort     = $this->servicioGeneral->getServicioProductoCaracteristica($servicioInternet, "GEM-PORT", $producto);
            if($spcGemPort)
            {
                $strGemPort = $spcGemPort->getValor();
            }

            //OBTENER TRAFFIC-TABLE
            $spcTraffic     = $this->servicioGeneral->getServicioProductoCaracteristica($servicioInternet, "TRAFFIC-TABLE", $producto);
            if($spcTraffic)
            {
                $strTraffic = $spcTraffic->getValor();
            }
        }
        else if($strMarcaOlt == "ZTE")
        {
            $spcServiceProfile = $this->servicioGeneral->getServicioProductoCaracteristica($servicioInternet, "SERVICE-PROFILE", $producto);
            if($spcServiceProfile)
            {
                $strServiceProfile = $spcServiceProfile->getValor();
            }

            //OBTENER SERVICE-PORT
            $spcSpid        = $this->servicioGeneral->getServicioProductoCaracteristica($servicioInternet, "SPID", $producto);
            if($spcSpid)
            {
                $strSpid    = $spcSpid->getValor();
            }

            //OBTENER CAPACIDAD1
            $objCapacidad1 = $this->servicioGeneral->getServicioProductoCaracteristica($servicioInternet, "CAPACIDAD1", $producto);
            if(is_object($objCapacidad1))
            {
                $strCapacidad1 = $objCapacidad1->getValor();
            }

            //OBTENER CAPACIDAD2
            $objCapacidad2 = $this->servicioGeneral->getServicioProductoCaracteristica($servicioInternet, "CAPACIDAD2", $producto);
            if(is_object($objCapacidad2))
            {
                $strCapacidad2 = $objCapacidad2->getValor();
            }
        }
        else
        {
            $respuesta = array('status' => 'ERROR', 'mensaje' => 'Modelo de OLT no tiene aprovisionamiento!');
            return $respuesta;
        }
        
        if($strNombreTecnicoInternet === "INTERNET SMALL BUSINESS")
        {
            $strTipoNegocio = "PYME";
        }
        $arrayDatos = array(
                                'serial_ont'            => $strSerieOnt,
                                'mac_ont'               => $macIpFija,
                                'nombre_olt'            => $elemento->getNombreElemento(),
                                'ip_olt'                => $objIpElemento->getIp(),
                                'puerto_olt'            => $interfaceElemento->getNombreInterfaceElemento(),
                                'modelo_olt'            => $modeloElemento->getNombreModeloElemento(),
                                'gemport'               => $strGemPort,
                                'service_profile'       => $strServiceProfile,
                                'line_profile'          => $strLineProfile,
                                'traffic_table'         => $strTraffic,
                                'ont_id'                => $strIndiceCliente,
                                'service_port'          => $strSpid,
                                'vlan'                  => $strVlan,
                                'estado_servicio'       => $servicio->getEstado(),
                                'mac_wifi'              => $strMacWifi,
                                'tipo_negocio_actual'   => $strTipoNegocio,
                                'ip_fijas_activas'      => $intIpsFijasActivas,
                                'ip'                    => $objIpFija->getIp(),
                                'scope'                 => $objCaractScope->getValor(),
                                'capacidad_up'          => $strCapacidad1,
                                'capacidad_down'        => $strCapacidad2
                            );

        if($strTipoNegocio === "PYME" && is_object($servicio->getProductoId()))
        {
            $arrayParametrosCaracteristicas = array( 'intIdProducto'         => $servicio->getProductoId()->getId(),
                                                     'strDescCaracteristica' => 'IP WAN',
                                                     'strEstado'             => 'Activo' );
            $strExisteIpWan = $this->serviceUtilidades->validarCaracteristicaProducto($arrayParametrosCaracteristicas);
            if ($strExisteIpWan === 'S')
            {
                $strTipoProcesoMiddleware       = "CANCELAR_IP_FIJA_WAN";
                $arrayDatos['ip_fija_wan']      = '1';
                $arrayDatos['tipo_plan_actual'] = 'sin_ip';
            }
        }

        $arrayDatosMiddleware = array(
                                        'empresa'               => $strPrefijoEmpresa,
                                        'nombre_cliente'        => $strNombreCliente,
                                        'login'                 => $strLogin,
                                        'identificacion'        => $strIdentificacion,
                                        'datos'                 => $arrayDatos,
                                        'opcion'                => $strTipoProcesoMiddleware,
                                        'ejecutaComando'        => $this->ejecutaComando,
                                        'usrCreacion'           => $usrCreacion,
                                        'ipCreacion'            => $ipCreacion
                                    );

        $arrayFinal = $this->rdaMiddleware->middleware(json_encode($arrayDatosMiddleware));
            
        return $arrayFinal;
    }
     
    
    /**
     * Funcion que cancela la ip en el olt, solo ejecucion
     * de scripts y busca de parametros (vlan y pool)
     * 
     * @author Creado: Kenneth Jimenez <kjimenez@telconet.ec>
     * @author Modificado: Francisco Adum <fadum@telconet.ec>
     * @version 1.0 4-05-2014
     *     
     * @author Modificado: Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 23-12-2015 Se agrega programacion para cambio de perfiles de clientes Tellion Pro
     * 
     * @author Modificado: Jesus Bozada <jbozada@telconet.ec>
     * @version 1.2 09-05-2016 Se agrega parametro empresa en metodo cancelarIpsFijas por conflictos de 
     *                         producto INTERNET DEDICADO
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 28-05-2018 Se agrega validación para Ips Adicionales de Small Business
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.4 05-05-2019 Se elimina código innecesario para servicios de Ips Small Busines, ya que dichos servicios no siguen este flujo
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.5 22-02-2021 Se consulta si el tipo de Ip para ese servicio es PRIVADA
     * 
     * @param Array $parametros arreglo de parametros necesarios para cancelar ip fija adicional
     * 
     */
    public function cancelarIpsFijas($parametros)
    {
        $servicio          = $parametros['servicio'];
        $producto          = $parametros['producto']; 
        $servicioTecnico   = $parametros['servicioTecnico'];
        $interfaceElemento = $parametros['interfaceElemento'];
        $modeloElemento    = $parametros['modeloElemento'];
        $macWifi           = $parametros['macWifi'];
        $perfil            = $parametros['perfil'];
        $usrCreacion       = $parametros['usrCreacion'];
        $indiceCliente     = $parametros['indice'];
        $strTipoNegocio    = $parametros['tipoNegocio'];
        $strIdEmpresa      = $parametros['idEmpresa'];
		
        try
        {	
            $strTipoIp = 'FIJA';
        
            //Obtiene tipo de ip por el servicio (PRIVADA)
            $objTipoIp = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                ->findOneBy(array("servicioId" => $servicio->getId(),
                                                                  "tipoIp"     => "PRIVADA",
                                                                  "estado"     => "Activo"));
            if (is_object($objTipoIp))
            {
                $strTipoIp = $objTipoIp->getTipoIp();
            }

            //obtener ips fijas q tiene el servicio (en este caso siempre sera 1)
            $ipsFijas = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                             ->findBy(array("servicioId"=>$servicio->getId(),"tipoIp"=>$strTipoIp, "estado"=>"Activo"));
			
            //consultar si el olt tiene aprovisionamiento de ips en el CNR
            $objDetalleElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                          ->findOneBy(array('detalleNombre' => 'OLT MIGRADO CNR',
                                                                            'elementoId'    => $interfaceElemento->getElementoId()->getId()));
            $objProductoInt     = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                    ->findOneBy(array("descripcionProducto"   => "INTERNET DEDICADO",
                                                                       "estado"               => "Activo",
                                                                       "empresaCod"           => $strIdEmpresa));
            $servProdCaracMacOnt = $this->servicioGeneral 
                                        ->getServicioProductoCaracteristica($servicioTecnico->getServicioId(), "MAC ONT", $objProductoInt);
            if ($servProdCaracMacOnt)
            {
                $macOnt = $servProdCaracMacOnt->getValor() ;
            }

            if($objDetalleElemento)
            {
                
                //buscar scopes
                $arrayScopeOlt = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')
                                                         ->getScopePorIpFija($ipsFijas[0]->getIp(), $servicioTecnico->getElementoId());
                if(!$arrayScopeOlt)
                {
                    $arrayFinal[] = array('status'  => "ERROR",
                                          'mensaje' => "Ip Fija " . $ipsFijas[0]->getIp() . " no pertenece a un Scope! <br>"
                                                       . "Favor Comunicarse con el Dep. Gepon!");
                    return $arrayFinal;
                }

                $scope = $arrayScopeOlt['NOMBRE_SCOPE'];

                $parametrosCancelaIp = array(   'servicio' => $servicio,
                                                'scope'    => $scope,
                                                'producto' => $producto,
                                                'macOnt'   => $macOnt);

                $resultCancelaIp = $this->cancelarIpTellionCnr($parametrosCancelaIp);
                $statusIpCancel  = $resultCancelaIp[0]['status'];
                $mensajeIpCancel = $resultCancelaIp[0]['mensaje'];

                if($statusIpCancel!="OK")
                {
                    $respuestaFinal[] = array('status'=>"ERROR", 
                                              'mensaje'=>"No se elimino Ip Fija del CNR, <br>Error:".$mensajeIpCancel.$mensaje);
                    return $respuestaFinal;
                }else
                {
                    if ($strTipoNegocio == "PRO")
                    {
                        $lineaPon    = $interfaceElemento->getNombreInterfaceElemento();
                        //*OBTENER SCRIPT--------------------------------------------------------*/
                        $scriptArray = $this->servicioGeneral->obtenerArregloScript("cambioPlanCliente", $modeloElemento);
                        $idDocumento = $scriptArray[0]->idDocumento;
                        $usuario     = $scriptArray[0]->usuario;
                        $protocolo   = $scriptArray[0]->protocolo;
                        $datos       = $lineaPon . "," . $indiceCliente . "," . $indiceCliente . "," . $perfil;
                        //*----------------------------------------------------------------------*/
                        
                        $comando = "java -jar -Djava.security.egd=file:/dev/./urandom " .
                                $this->pathTelcos . "telcos/src/telconet/tecnicoBundle/batch/md_ejecucion.jar '" .
                                $this->host . "' '" . $idDocumento . "' '" . $usuario . "' '" . $protocolo . "' '" .
                                $servicioTecnico->getElementoId() . "' '" . $datos . "' '" . $this->pathParameters . "'";

                        $salida             = shell_exec($comando);
                        $pos                = strpos($salida, "{");
                        $jsonObj            = substr($salida, $pos);
                        $resultadJsonPerfil = json_decode($jsonObj);

                        $status  = $resultadJsonPerfil->status;
                        $mensaje = $resultadJsonPerfil->mensaje;
                        if ($status == "ERROR")
                        {
                            $respuestaFinal[] = array('status'  => 'ERROR',
                                                      'mensaje' => 'No se pudo configurar el nuevo perfil ' . $perfil . ' ' .
                                                                   'para la activacion de la Ip(s) Fija(s)');
                            return $respuestaFinal;
                        }

                        //obtener servicio referencial
                        $objServicioRef = $servicioTecnico->getServicioId();

                        //eliminar caracteristica perfil
                        $objServProdCaracPerfil = $this->servicioGeneral
                                                       ->getServicioProductoCaracteristica($objServicioRef, "PERFIL", $producto);
                        $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objServProdCaracPerfil, "Eliminado");

                        //ingresar caracteristica nueva perfil
                        $this->servicioGeneral
                             ->ingresarServicioProductoCaracteristica($objServicioRef, $producto, "PERFIL", $perfil, $usrCreacion);
                    }
                
                    $respuestaFinal[] = array('status'=>'OK', 'mensaje'=>'OK');
                    return $respuestaFinal;
                }
            }
            else
            {
                //obtener indice cliente
                $servProdCaracPool = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "POOL IP", $producto);

                if($servProdCaracPool)
                {
                    $pool = $servProdCaracPool->getValor();
                }
                else
                {
                    //configurar Ip fija
                    //*OBTENER SCRIPT--------------------------------------------------------*/
                    $scriptArray     = $this->servicioGeneral->obtenerArregloScript("obtenerVlanParaIpFija",$modeloElemento);
                    $idDocumentoVlan = $scriptArray[0]->idDocumento;
                    $usuario         = $scriptArray[0]->usuario;
                    $protocolo       = $scriptArray[0]->protocolo;
                    //*----------------------------------------------------------------------*/
                    $resultadJsonVlan = $this->getVlanParaIpFija($servicioTecnico, $usuario, $interfaceElemento, $perfil, $idDocumentoVlan);
                    $statusVlan       = $resultadJsonVlan->status;
                    if($statusVlan=="OK")
                    {
                        $vlan = $resultadJsonVlan->mensaje;

                        //*OBTENER SCRIPT--------------------------------------------------------*/
                        $scriptArray     = $this->servicioGeneral->obtenerArregloScript("obtenerPoolParaIpFija",$modeloElemento);
                        $idDocumentoPool = $scriptArray[0]->idDocumento;
                        $usuario         = $scriptArray[0]->usuario;
                        $protocolo       = $scriptArray[0]->protocolo;
                        //*----------------------------------------------------------------------*/
                        $resultadJsonPool = $this->getPoolParaIpFija($servicioTecnico, $usuario, $interfaceElemento, $vlan, $idDocumentoPool);
                        $statusPool       = $resultadJsonPool->status;
                        $pool             = $resultadJsonPool->mensaje;
                        if($pool=="")
                        {
                            $olt = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                        ->find($interfaceElemento->getElementoId()->getId());
                            $respuestaFinal[] = array('status'  => 'ERROR', 
                                                      'mensaje' => 'No Existe Pool para el Perfil <b>'.$perfil.'</b> '
                                                                   .'en <b>'.$olt->getNombreElemento().'</b>. '
                                                                   .'Favor Notificar a <b>GEPON</b>');
                            return $respuestaFinal;
                        }//if($pool=="")
                    }//if($statusVlan=="OK")
                    else
                    {
                        $olt = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                    ->find($interfaceElemento->getElementoId()->getId());
                        $respuestaFinal[] = array('status'  => 'ERROR',
                                                  'mensaje' => 'No existe Vlan para el Perfil <b>'.$perfil.'</b> '
                                                               .'en <b>'.$olt->getNombreElemento().'</b>. '
                                                               .'Favor Notificar a <b>GEPON</b>');
                        return $respuestaFinal;
                    }
                }//else - if($servProdCaracPool)

                //cambiar formato de la mac
                $macWifiNueva = $this->cambiarMac($macWifi);

                //desconfigurar Ip fija
                //*OBTENER SCRIPT--------------------------------------------------------*/
                $scriptArray        = $this->servicioGeneral->obtenerArregloScript("desconfigurarIpFija",$modeloElemento);
                $idDocumentoDesconf = $scriptArray[0]->idDocumento;
                $usuario            = $scriptArray[0]->usuario;
                $protocolo          = $scriptArray[0]->protocolo;
                //*----------------------------------------------------------------------*/

                if($ipsFijas)
                {
                    for($i=0;$i<count($ipsFijas);$i++)
                    {
                        $ipFija              = $ipsFijas[$i];
                        $resultadJsonDesconf = $this->desconfigurarIpFija($interfaceElemento, $usuario, $pool, 
                                                                          $ipFija->getIp(),$macWifiNueva, $idDocumentoDesconf);
                        $statusDesconf = $resultadJsonDesconf->status;

                        if($statusDesconf=="OK")
                        {
                            $respuestaFinal[] = array('status'=>'OK', 'mensaje'=>'OK');
                            return $respuestaFinal;
                        }
                        else
                        {
                            $respuestaFinal[] = array('status'=>'ERROR', 'mensaje'=>'NO SE DESCONFIGURO IP FIJA');
                            return $respuestaFinal;
                        }
                    }
                }//if($ipsFijas)
                else
                {
                    $respuestaFinal[] = array('status'=>'ERROR', 'mensaje'=>'NO EXISTEN IP(s) PARA CANCELAR');
                    return $respuestaFinal;
                }
            }		
        }//try
        catch (\Exception $e) {
            $status  = "ERROR";
            $mensaje = "ERROR,".$e->getMessage();
            $arrayFinal[] = array('status'=>"ERROR", 'mensaje'=>$mensaje);
            return $arrayFinal;
        }
    }
    
    /**
     * Funcion que transforma el formato de la mac
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @param String       $macWifi (mac en formato aaaa.bbbb.cccc)
     * @return mac en formato aa:aa:bb:bb:cc:cc
     */
    public function cambiarMac($macWifi){
		$macWifiNueva = "";
        $macWifi = trim($macWifi);
        $arr2 = explode(".",$macWifi);
            for($i=0;$i<count($arr2);$i++){
            $arr1 = str_split($arr2[$i]);
            for($j=0;$j<count($arr1);$j++){
                if($j==1 || $j==3 && ($i+1)!=count($arr1)-1){
                    $macWifiNueva = $macWifiNueva.$arr1[$j].":";
                }
                else{
                    $macWifiNueva = $macWifiNueva.$arr1[$j]."";
                }
            }
        }
        return $macWifiNueva;
    }
    
    /**
     * Funcion que ejecuta un script que obtiene el pool
     * por medio de la vlan
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 7-08-2014
     */
    public function getPoolParaIpFija($servicioTecnico, $usuario, $interfaceElemento, $vlan, $idDocumento){
        $datos = $vlan;
        $comando1 = "java -jar -Djava.security.egd=file:/dev/./urandom ".$this->pathTelcos."telcos/src/telconet/tecnicoBundle/batch/md_datos.jar '".
            $this->host."' 'obtenerPoolParaIpFija' '".$interfaceElemento->getElementoId()->getId()."' '".$usuario."' '".
            $interfaceElemento->getNombreInterfaceElemento()."' '".$idDocumento."' '".$datos."' '".$this->pathParameters."'";

        $salida1= shell_exec($comando1);
        $pos1 = strpos($salida1, "{"); 
        $jsonObj1= substr($salida1, $pos1);
        $resultadJson1 = json_decode($jsonObj1);
        
        return $resultadJson1;
    }
    
    /**
     * Funcion que ejecuta un script que obtiene la vlan
     * por medio del perfil
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 7-08-2014
     */
    public function getVlanParaIpFija($servicioTecnico, $usuario, $interfaceElemento, $perfil, $idDocumento){
        $datos = $perfil;
        $comando1 = "java -jar -Djava.security.egd=file:/dev/./urandom ".$this->pathTelcos."telcos/src/telconet/tecnicoBundle/batch/md_datos.jar '".
            $this->host."' 'obtenerVlanParaIpFija' '".$interfaceElemento->getElementoId()->getId()."' '".$usuario."' '".
            $interfaceElemento->getNombreInterfaceElemento()."' '".$idDocumento."' '".$datos."' '".$this->pathParameters."'";
        $salida1= shell_exec($comando1);
        $pos1 = strpos($salida1, "{"); 
        $jsonObj1= substr($salida1, $pos1);
        $resultadJson1 = json_decode($jsonObj1);
        
        return $resultadJson1;
    }
    
    /**
     * Funcion que sirve para la ejecucion de scripts sobre los elementos correspondientes
     * para cancelar un servicio de internet sin ip
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.1. 11-04-2015
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.2. 21-07-2015
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.3  09-05-2016   Se agrega parametro empresa en metodo cancelarServicioMdSinIp por conflictos de 
     *                            producto INTERNET DEDICADO
     * 
     * @since 1.0
     * @param Array $arrayParametros (servicioTecnico, interfaceElemento, login, modeloElemento, spcIndiceCliente, spcSpid, spcMacOnt)
     */
    public function cancelarServicioMdSinIp($arrayParametros){
        $servicioTecnico    = $arrayParametros['servicioTecnico'];
        $interfaceElemento  = $arrayParametros['interfaceElemento'];
        $login              = $arrayParametros['login'];
        $modeloElemento     = $arrayParametros['modeloElemento'];
        $spcIndiceCliente   = $arrayParametros['spcIndiceCliente'];
        $spcSpid            = $arrayParametros['spcSpid'];
        $spcMacOnt          = $arrayParametros['spcMacOnt'];
        $strIdEmpresa       = $arrayParametros['idEmpresa'];
        $mensaje            = "";
        
        try
        {
            //Se agrega validacion de Olt para no ejecutar fisicamente la cancelación en caso de que se encuentre NO Operativos
            $entitydetalleElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                             ->findOneBy(array("elementoId"     => $interfaceElemento->getElementoId()->getId(), 
                                                                                "detalleNombre" => "OLT OPERATIVO"));
            if ($entitydetalleElemento)
            {
                if ($entitydetalleElemento->getDetalleValor() == "NO")
                {
                    $status         = "OK";
                    $mensaje        = "OK";
                    $arrayFinal[]   = array('status' => $status, 'mensaje' => $mensaje);
                    return $arrayFinal;
                }
            }
            
            //*OBTENER SCRIPT--------------------------------------------------------*/
            $scriptArray    = $this->servicioGeneral->obtenerArregloScript("cancelarCliente",$modeloElemento);
            $idDocumento    = $scriptArray[0]->idDocumento;
            $usuario        = $scriptArray[0]->usuario;
            $protocolo      = $scriptArray[0]->protocolo;
            //*----------------------------------------------------------------------*/
            $strMarcaOlt    = $modeloElemento->getMarcaElementoId()->getNombreMarcaElemento();
            if($modeloElemento->getNombreModeloElemento()=="EP-3116")
            {
                //1. OLT-OBTENCION DE LA MAC DEL WIFI
                $scriptArray1   = $this->servicioGeneral->obtenerArregloScript("obtenerMacIpDinamica",$modeloElemento);
                $idDocumentoMac = $scriptArray1[0]->idDocumento;
                $usuarioMac     = $scriptArray1[0]->usuario;
                $macDinamica    = $this->getMacIpDinamica($interfaceElemento, $usuarioMac, $interfaceElemento, 
                                                          $spcIndiceCliente, $idDocumentoMac);

                //2. OLT-OBTENCION IP WIFI - ARP
                $scriptArray2   = $this->servicioGeneral->obtenerArregloScript("obtenerIpDinamicaArp",$modeloElemento);
                $idDocumento2   = $scriptArray2[0]->idDocumento;
                $usuarioIp      = $scriptArray2[0]->usuario;
                $ipDinamicaArp  = $this->getIpDinamica($interfaceElemento, $usuarioIp, $interfaceElemento, $macDinamica->mensaje, $idDocumento2);

                //2. OLT-OBTENCION IP WIFI - DHCP
                $scriptArray3   = $this->servicioGeneral->obtenerArregloScript("obtenerIpDinamicaDhcp",$modeloElemento);
                $idDocumento3   = $scriptArray3[0]->idDocumento;
                $ipDinamicaDhcp = $this->getIpDinamica($interfaceElemento, $usuarioIp, $interfaceElemento, $macDinamica->mensaje, $idDocumento3);

                //3. OLT-CANCELACION DE SERVICIO
                $resultadJson = $this->cancelarServicioOlt($interfaceElemento, $spcIndiceCliente, 
                                                           $interfaceElemento, $idDocumento, $login);

                if($resultadJson->status == "OK")
                {
                    if($ipDinamicaDhcp->mensaje!="")
                    {
                        //4.  OLT-ACTUALIZACION TABLA IP DHCP
                        $scriptArray5   = $this->servicioGeneral->obtenerArregloScript("clearTablaIpDhcp",$modeloElemento);
                        $idDocumento5   = $scriptArray5[0]->idDocumento;
                        $this->clearTablaIp($interfaceElemento, $idDocumento5,$ipDinamicaDhcp->mensaje);
                    }

                    if($ipDinamicaArp->mensaje!="")
                    {
                        //4.  OLT-ACTUALIZACION TABLA IP ARP
                        $scriptArray4 = $this->servicioGeneral->obtenerArregloScript("clearTablaIpArp",$modeloElemento);
                        $idDocumento4 = $scriptArray4[0]->idDocumento;
                        $this->clearTablaIp($interfaceElemento, $idDocumento4,$ipDinamicaArp->mensaje);
                    }
                    
                    //consultar si el olt tiene aprovisionamiento de ips en el CNR
                    $objDetalleElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                  ->findOneBy(array('detalleNombre' =>'OLT MIGRADO CNR',
                                                                                    'elementoId' => $interfaceElemento->getElementoId()->getId()));
                    if ($objDetalleElemento)
                    {
                        $producto = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                      ->findOneBy(array("descripcionProducto"   => "INTERNET DEDICADO",
                                                                        "estado"                => "Activo",
                                                                        "empresaCod"            => $strIdEmpresa));
                        //obtener mac ont
                        $servProdCaracMacOnt = $this->servicioGeneral 
                                                     ->getServicioProductoCaracteristica($servicioTecnico->getServicioId(), "MAC ONT", $producto);
                        if($servProdCaracMacOnt)
                        {
                            $this->desconfigurarIpDinamicaCnr($servProdCaracMacOnt->getValor());
                        }
                    }                    
                    
                    $status         = "OK";
                    $mensaje        = "OK";
                    $arrayFinal[]   = array('status'=>$status, 'mensaje'=>$mensaje);
                }
                else
                {
                    $status  = "ERROR";
                    $mensaje = $resultadJson->mensaje;
                    throw new \Exception($mensaje);
                }
            }
            //else if($modeloElemento->getNombreModeloElemento()=="MA5608T")
            else if($strMarcaOlt == "HUAWEI")
            {
                list($tarjeta, $puertoPon) = split('/',$interfaceElemento->getNombreInterfaceElemento());
                //obtener elemento SM
                $modeloElementoSm = $this->emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                          ->findOneBy(array("nombreModeloElemento"  => "SM-SCE 8000",
                                                            "estado"                => "Activo"));
                
                //obtener elemento sm
                $elementoSm = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                          ->findOneBy(array("modeloElementoId"=>$modeloElementoSm->getId()));
                
                //obtener objeto modelo cnr
                $modeloElementoCnr = $this->emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                          ->findOneBy(array("nombreModeloElemento"  => "CNR UCS C220",
                                                            "estado"                => "Activo"));

                //obtener elemento cnr
                $elementoCnr = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                          ->findOneBy(array("modeloElementoId"  => $modeloElementoCnr->getId(),
                                                            "estado"            => "Activo"));
                
                //1. OBTENER IP CPE ARP (OLT)
                $scriptArray1   = $this->servicioGeneral->obtenerArregloScript("obtenerIpCpeArp",$modeloElemento);
                $datos          = $tarjeta.",".$puertoPon.",".$spcIndiceCliente->getValor();
                $jsonIpsArp     = $this->activarService->obtenerDatosPorAccion($servicioTecnico, $scriptArray1[0]->usuario, 
                                                                               $datos, $scriptArray1[0]->idDocumento, "obtenerIpArp");
                
                //2. OBTENER SPID (BASE)
                $spid = $spcSpid->getValor();
                
                //3. CANCELAR SERVICIO Y ACTUALIZAR ARP (OLT)
                $parametrosCancelacion = array(
                                                'elementoId'    => $interfaceElemento->getElementoId()->getId(),
                                                'idDocumento'   => $idDocumento,
                                                'spid'          => $spid,
                                                'tarjeta'       => $tarjeta,
                                                'puertoPon'     => $puertoPon,
                                                'ontId'         => $spcIndiceCliente->getValor()
                                              );
                $jsonCancelar = $this->cancelarServicioOltHuawei($parametrosCancelacion);
                $status = $jsonCancelar->status;
                
                if($status=="OK")
                {
                    if($jsonIpsArp->status=="OK")
                    {
                        $listadoIpsArp  = $jsonIpsArp->mensaje;
                        
                        //4. DESCONFIGURAR IP ARP (OLT)
                        $scriptArray4 = $this->servicioGeneral->obtenerArregloScript("desconfigurarIpArp",$modeloElemento);
                        $this->servicioGeneral->ejecutarComandoPersonalizadoMdDatos($servicioTecnico->getElementoId(), $scriptArray4[0]->usuario,
                                                                     $listadoIpsArp, $scriptArray4[0]->idDocumento, "desconfigurarIpArp");
                    }//if($jsonIpsArp->status=="OK")
                    else
                    {
                        $mensaje = $mensaje . "No existian Ips Arp";
                    }
                    
                    $macOnt = $this->cambiarMac($spcMacOnt->getValor());
                    //5. OBTENER IPS ASOCIADAS A UN SUSCRIBER (SM-SCE 8000)
                    $scriptArray5 = $this->servicioGeneral->obtenerArregloScript("obtenerIpsSuscriber",$modeloElementoSm);
                    $jsonIpsSuscriber = $this->servicioGeneral->ejecutarComandoPersonalizadoMdDatos($elementoSm->getId(), $scriptArray5[0]->usuario, 
                                                                               $macOnt, $scriptArray5[0]->idDocumento, "getIpsSuscriber");
                    
                    //6. ELIMINAR MAPEO DE SUSCRIBER (SM-SCE 8000)
                    $scriptArray6 = $this->servicioGeneral->obtenerArregloScript("eliminarSuscriber",$modeloElementoSm);
                    $this->servicioGeneral->ejecutarComandoMdEjecucion($elementoSm->getId(), $scriptArray6[0]->usuario, 
                                                                       $macOnt, $scriptArray6[0]->idDocumento);
                    
                    if($jsonIpsSuscriber->status=="OK")
                    {
                        $listadoIpsSuscriber  = $jsonIpsSuscriber->mensaje;
                        
                        //7. ELIMINAR IP DINAMICA (CNR)
                        $scriptArray7 = $this->servicioGeneral->obtenerArregloScript("desconfigurarIpDinamica",$modeloElementoCnr);
                        $this->servicioGeneral->ejecutarComandoPersonalizadoMdDatos($elementoCnr->getId(), $scriptArray7[0]->usuario, 
                                                                   $listadoIpsSuscriber, $scriptArray7[0]->idDocumento, "desconfigurarIpDinamica");
                    }
                }
                else
                {
                    $status  = "ERROR";
                    $mensaje = $jsonCancelar->mensaje;
                    throw new \Exception($mensaje);
                }
            }
            else
            {
                throw new \Exception('Modelo de OLT no tiene aprovisionamiento');
            }
        }
        catch (\Exception $e) 
        {
            $status         = "ERROR";
            $mensaje        = "ERROR: ".$e->getMessage();
            $arrayFinal[]   = array('status'=>"ERROR", 'mensaje'=>$mensaje);
            return $arrayFinal;
        }
        
        $arrayFinal[] = array('status'=>$status, 'mensaje'=>$mensaje);
        return $arrayFinal;        
    }
    
    /**
     * Funcion que ejecuta un script que limpia la tabla arp
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 7-08-2014
     */
    public function clearTablaIp($servicioTecnico,$idDocumento,$ip){
        $datos = $ip;
        $comando = "nohup java -jar -Djava.security.egd=file:/dev/./urandom ".$this->pathTelcos."telcos/src/telconet/tecnicoBundle/batch/md_ejecucion.jar '".
            $this->host."' '".$idDocumento."' 'usuario' 'SSH' '".$servicioTecnico->getElementoId()->getId()."' '".
            $datos."' '".$this->pathParameters."'&";
        shell_exec($comando);
        
        return "OK";
    }
    
    /**
     * Funcion que sirve para Cancelar logicamente aquellos servicios que vienen de una orden de Reubicacion o Traslado y si son de diferentes
     * tecnologias, se crea la solicitud de retiro de equipos.
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 5-05-2015
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 13-06-2016   Se corrigen variables utilizadas en el ingreso de historiales de servicio originado por traslado
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.2 18-07-2016   Se modifica recuparación de servicio origen de trasaldo/reubicacion y se modifica eliminación
     *                           de enlace que tengan como inicio la interface del splitter del servicio origen.
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.3 01-03-2017   Se agrega validación para solo agregar al retiro de equipo elementos que no sean SmartWifi tipo venta
     * @since 1.2
     * 
     * @author Francisco Adum <fadum@netlife.net.ec>
     * @version 1.4 16-05-2017  Se corrige identacion del codigo.
     *                          Se corrige comentario de lo que hace la funcion.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.5 26-07-2018  Cuando sean mismos olts con mismas interfaces pero diferentes splitters
     *                          o cuando sean mismos olts con mismas interfaces y mismos splitters pero diferentes interfaces
     *                          se elimina el enlace entre la interface del splitter y la interface del ont del servicio de Internet del 
     *                          punto origen.
     *                          Además se eliminan características asociadas al servicio de internet e ips adicionales del punto origen y
     *                          la respectiva característica de traslado del servicio del punto destino.
     *                          así como también se elimina en caso de existir la ip del plan del servicio anterior y se modifica la validación 
     *      ,                    para permitir eliminar ldap del servicio anterior con factibilidad en OLTs TELLION con tipo de aprovisionamiento CNR.
     * 
     * @param array $arrayParametros (servicio, servicioTecnico, tipoOrden, usrCreacion, ipCreacion, producto, ultimaMilla, idPersonaEmpresaRol)
     */
    public function cancelarServicioPorTipoOrden($arrayParametros)
    {
        $objInfoServicio            = $arrayParametros['servicio'];
        $servicioTecnico            = $arrayParametros['servicioTecnico'];
        $tipoOrden                  = $arrayParametros['tipoOrden'];
        $usrCreacion                = $arrayParametros['usrCreacion'];
        $ipCreacion                 = $arrayParametros['ipCreacion'];
        $producto                   = $arrayParametros['producto'];
        $ultimaMilla                = $arrayParametros['ultimaMilla'];
        $idPersonaEmpresaRol        = $arrayParametros['idPersonaEmpresaRol'];
        $idEmpresa                  = $arrayParametros['idEmpresa'];
        $flagElemento               = 0;
        $flagInterface              = 0;
        $boolValidaCambioConector   = 0;
        
        //crear el historial del servicio -> EnPruebas--------------------------------------------------------------------------------
        $this->servicioGeneral->ingresarServicioHistorial($objInfoServicio, "EnPruebas", 
                                                          "Se actualiza estado del Servicio Por Tipo de Orden:".$tipoOrden, 
                                                          $usrCreacion, $ipCreacion);
       
        //si el tipo de orden es Traslado
        if($tipoOrden == "T")
        {
            //obtener el servicio anterior
            $spcTraslado        = $this->servicioGeneral->getServicioProductoCaracteristica($objInfoServicio, "TRASLADO", $producto);
            $servicioAnterior   = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($spcTraslado->getValor());
            
            if(is_object($servicioAnterior))
            {
                $arraySpcServicioAnterior   = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                ->findBy(array( "servicioId"    => $servicioAnterior->getId(),
                                                                                "estado"        => "Activo"));
                foreach($arraySpcServicioAnterior as $objSpcServicioAnterior)
                {
                    $objSpcServicioAnterior->setEstado('Eliminado');
                    $objSpcServicioAnterior->setUsrUltMod($usrCreacion);
                    $objSpcServicioAnterior->setFeUltMod(new \DateTime('now'));
                    $this->emComercial->persist($objSpcServicioAnterior);
                    $this->emComercial->flush();
                }
                
                $objInfoIpPlanServicioAnterior  = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                                          ->findOneBy(array("servicioId"    => $servicioAnterior->getId(),
                                                                                            "tipoIp"        => "FIJA", 
                                                                                            "estado"        => "Activo"));
                if(is_object($objInfoIpPlanServicioAnterior))
                {
                    $objInfoIpPlanServicioAnterior->setEstado("Eliminado");
                    $this->emInfraestructura->persist($objInfoIpPlanServicioAnterior);
                    $this->emInfraestructura->flush();
                }
            }
           
            //Se coloca data tecnica en los servicios de Ips adicionales
            $serviciosPunto     = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                   ->findBy(array("puntoId" => $objInfoServicio->getPuntoId()->getId()));
                        
            $prodIp             = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                        ->findBy(array("nombreTecnico"  => "IP",
                                                       "empresaCod"     => $idEmpresa,
                                                       "estado"         => "Activo"));
            
            //obtener servicios de ips adicionales
            $arrayIpsAdicionales =  Array();
            for ($i = 0; $i < count($serviciosPunto); $i++) 
            {
                if ($serviciosPunto[$i]->getPlanId()) 
                {
                    $planCab = $this->emComercial->getRepository('schemaBundle:InfoPlanCab')
                                    ->find($serviciosPunto[$i]->getPlanId()->getId());
                    $planDet = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                    ->findBy(array("planId" => $planCab->getId()));

                    for ($j = 0; $j < count($planDet); $j++) 
                    {
                        //contar las ip que estan en planes
                        foreach ($prodIp as $productoIp) {
                            if ($productoIp->getId() == $planDet[$j]->getProductoId()) 
                            {                                    
                                $arrayIpsAdicionales[] = $serviciosPunto[$i]->getId();                                                                        
                            }
                        }
                    }
                }
                else
                {
                    //contar las ip que estan como productos
                    $productoServicioPunto = $serviciosPunto[$i]->getProductoId();
                    foreach ($prodIp as $productoIp) {

                        if ($productoIp->getId() == $productoServicioPunto->getId()) 
                        {
                            $arrayIpsAdicionales[] = $serviciosPunto[$i]->getId();    
                        }
                    }
                }
            }
            
            //setear valores de servicio tecnico en servicios ips adicionales
            foreach($arrayIpsAdicionales as $serviciosIp)
            {
                $servicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($serviciosIp);
                
                $servicioTecnicoIps = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                        ->findOneByServicioId($servicio->getId());                                
                
                if(!$servicioTecnicoIps)
                {                    
                    $servicioTecnicoIps = new InfoServicioTecnico();
                    $servicioTecnicoIps->setServicioId($servicio);
                }                
                
                $servicioTecnicoIps->setElementoId($servicioTecnico->getElementoId());
                $servicioTecnicoIps->setInterfaceElementoId($servicioTecnico->getInterfaceElementoId());            
                $servicioTecnicoIps->setElementoContenedorId($servicioTecnico->getElementoContenedorId());
                $servicioTecnicoIps->setElementoConectorId($servicioTecnico->getElementoConectorId());
                $servicioTecnicoIps->setInterfaceElementoConectorId($servicioTecnico->getInterfaceElementoConectorId());
                if($servicio->getId() !== $objInfoServicio->getId())
                {
                    $servicioTecnicoIps->setElementoClienteId(null);
                    $servicioTecnicoIps->setInterfaceElementoClienteId(null);
                }
                $servicioTecnicoIps->setUltimaMillaId($servicioTecnico->getUltimaMillaId()); 
                
                $this->emComercial->persist($servicioTecnicoIps);
                $this->emComercial->flush();
                
                $objSpcTrasladoServicioIp = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "TRASLADO", $producto);
                if(is_object($objSpcTrasladoServicioIp))
                {
                    $objServicioIpAnterior  = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                ->find($objSpcTrasladoServicioIp->getValor());
                    if(is_object($objServicioIpAnterior))
                    {
                        $arraySpcServicioIpAnterior = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                        ->findBy(array( "servicioId"    => $objServicioIpAnterior->getId(),
                                                                                        "estado"        => "Activo"));
                        foreach($arraySpcServicioIpAnterior as $objSpcServicioIpAnterior)
                        {
                            $objSpcServicioIpAnterior->setEstado('Eliminado');
                            $objSpcServicioIpAnterior->setUsrUltMod($usrCreacion);
                            $objSpcServicioIpAnterior->setFeUltMod(new \DateTime('now'));
                            $this->emComercial->persist($objSpcServicioIpAnterior);
                            $this->emComercial->flush();
                        }
                    }
                    $objSpcTrasladoServicioIp->setEstado('Eliminado');
                    $objSpcTrasladoServicioIp->setUsrUltMod($usrCreacion);
                    $objSpcTrasladoServicioIp->setFeUltMod(new \DateTime('now'));
                    $this->emComercial->persist($objSpcTrasladoServicioIp);
                    $this->emComercial->flush();
                }
            }                        
            
            //obtener servicio tecnico anterior
            $servicioTecnicoAnterior  = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                             ->findOneBy(array("servicioId" => $servicioAnterior->getId()));
            
            //crear el historial del servicio -> Activo--------------------------------------------------------------------------------------
            $this->servicioGeneral->ingresarServicioHistorial($objInfoServicio, "Activo", 
                                                              "Se Activo el Servicio Traslado", 
                                                              $usrCreacion, $ipCreacion);
            
            //cambiar estado al servicio anterior-------------------------------------------------------------------------------------
            $servicioAnterior->setEstado("Trasladado");
            $this->emComercial->persist($servicioAnterior);
            $this->emComercial->flush();
            
            //cambiar estado al punto anterior-------------------------------------------------------------------------------------
            $puntoAnterior = $servicioAnterior->getPuntoId();
            $puntoAnterior->setEstado("Trasladado");
            $this->emComercial->persist($puntoAnterior);
            $this->emComercial->flush();
            
            //crear el historial para servicio anterior-------------------------------------------------------------------------------------
            $this->servicioGeneral->ingresarServicioHistorial($servicioAnterior, "Trasladado", 
                                                              "Se Traslado el Servicio", 
                                                              $usrCreacion, $ipCreacion);
            
            //liberacion de recursos------------------------------------------------------------------------------------------------------
            //VERIFICAR ELEMENTOS DIFERENTES
            if($servicioTecnico->getElementoId() != $servicioTecnicoAnterior->getElementoId())
            {
                $flagElemento = 1;
            }

            //VERIFICAR INTERFACES DIFERENTES
            if($servicioTecnico->getInterfaceElementoId() != $servicioTecnicoAnterior->getInterfaceElementoId() )
            {
                $flagInterface = 1;
            }
            
            /**
             * Validar mismos olt con misma interface y diferentes splitters o 
             * mismos olts con misma interface y mismos splitters pero diferentes interfaces
             */
            if( $servicioTecnico->getElementoId() === $servicioTecnicoAnterior->getElementoId()
                && $servicioTecnico->getInterfaceElementoId() === $servicioTecnicoAnterior->getInterfaceElementoId()
                && (($servicioTecnico->getElementoConectorId() !== $servicioTecnicoAnterior->getElementoConectorId())
                    || ($servicioTecnico->getElementoConectorId() === $servicioTecnicoAnterior->getElementoConectorId() 
                    && $servicioTecnico->getInterfaceElementoConectorId() !== $servicioTecnicoAnterior->getInterfaceElementoConectorId())))
            {
                $boolValidaCambioConector = 1;
            }
            
            if($flagElemento > 0 || $flagInterface > 0 || $boolValidaCambioConector === 1)
            {
                //puerto splitter anterior
                $interfaceElementoSplitterIdAnterior = $servicioTecnicoAnterior->getInterfaceElementoConectorId();

                //puerto ont anterior
                $interfaceElementoClienteIdAnterior = $servicioTecnicoAnterior->getInterfaceElementoClienteId();

                /* eliminar enlaces anteriores que tengan como inicio la interface del splitter 
                   del servicio a cancelar*/
                $arrayEnlacesAnteriores  = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                ->findBy(array("interfaceElementoIniId" => $interfaceElementoSplitterIdAnterior,
                                                               "estado"                 => "Activo"));

                foreach( $arrayEnlacesAnteriores as $objEnlace )
                {
                    $objEnlace->setEstado("Eliminado");
                    $this->emInfraestructura->persist($objEnlace);
                    $this->emInfraestructura->flush();
                }

                //desconectar puerto splitter anterior
                $interfaceElementoSplitterAnterior  = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                            ->find($interfaceElementoSplitterIdAnterior);
                $interfaceElementoSplitterAnterior->setEstado("not connect");
                $this->emInfraestructura->persist($interfaceElementoSplitterAnterior);
                $this->emInfraestructura->flush();
            }
            
            if($ultimaMilla == "Fibra Optica")
            {
                //verificar si usan diferentes tecnologias
                $oltAnterior = $this->emInfraestructura->find('schemaBundle:InfoElemento', $servicioTecnicoAnterior->getElementoId());
                $oltActual   = $this->emInfraestructura->find('schemaBundle:InfoElemento', $servicioTecnico->getElementoId());
                if($oltAnterior->getId() != $oltActual->getId())
                {
                    if($oltAnterior->getModeloElementoId()->getMarcaElementoId() != 
                       $oltActual->getModeloElementoId()->getMarcaElementoId())
                    {
                        $diferenteTecnologia = "SI";
                    }
                    else
                    {
                        $diferenteTecnologia = "NO";
                    }
                }

                if($diferenteTecnologia == "SI")
                {
                    //crear solicitudes de retiro de equipo
                    $arrayRetiroEquipo = array( 'servicio'              => $servicioAnterior,
                                                'servicioTecnico'       => $servicioTecnicoAnterior,
                                                'observacion'           => "TRASLADO DE SERVICIO POR CAMBIO DE TECNOLOGIA",
                                                'usrCreacion'           => $usrCreacion,
                                                'ipCreacion'            => $ipCreacion,
                                                'idPersonaEmpresaRol'   => $idPersonaEmpresaRol);
                    $this->servicioGeneral->crearSolicitudRetiroEquipo($arrayRetiroEquipo);

                    //eliminar ont anterior
                    $ontAnterior = $this->emInfraestructura->find('schemaBundle:InfoElemento', $servicioTecnicoAnterior->getElementoClienteId());
                    $ontAnterior->setEstado("Eliminado");
                    $this->emInfraestructura->persist($ontAnterior);
                    $this->emInfraestructura->flush();

                    //historial del elemento ont
                    $historialElemento = new InfoHistorialElemento();
                    $historialElemento->setElementoId($ontAnterior);
                    $historialElemento->setObservacion("Se elimino el ont por traslado de Servicio y cambio de tecnologia");
                    $historialElemento->setEstadoElemento("Eliminado");
                    $historialElemento->setUsrCreacion($usrCreacion);
                    $historialElemento->setFeCreacion(new \DateTime('now'));
                    $historialElemento->setIpCreacion($ipCreacion);
                    $this->emInfraestructura->persist($historialElemento);
                    $this->emInfraestructura->flush();

                    //eliminar wifi anterior
                    $interfaceElementoClienteIdAnterior = $servicioTecnicoAnterior->getInterfaceElementoClienteId();
                    $enlaceOntWifiAnterior  = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                 ->findOneBy(array("interfaceElementoIniId" => $interfaceElementoClienteIdAnterior,
                                                                   "estado"                 => "Activo"));
                    if($enlaceOntWifiAnterior)
                    {
                        if(strpos($enlaceOntWifiAnterior->getInterfaceElementoFinId()->getElementoId(), 'SmartWifi') == false)
                        {
                            //eliminar enlace anterior
                            $enlaceOntWifiAnterior->setEstado("Eliminado");
                            $this->emInfraestructura->persist($enlaceOntWifiAnterior);
                            $this->emInfraestructura->flush();

                            //eliminar elemento wifi
                            $wifiAnterior = $enlaceOntWifiAnterior->getInterfaceElementoFinId()->getElementoId();
                            $wifiAnterior->setEstado("Eliminado");
                            $this->emInfraestructura->persist($wifiAnterior);
                            $this->emInfraestructura->flush();

                            //historial del elemento wifi
                            $historialElementoWifi = new InfoHistorialElemento();
                            $historialElementoWifi->setElementoId($wifiAnterior);
                            $historialElementoWifi->setObservacion("Se elimino el wifi por traslado de Servicio y cambio de tecnologia");
                            $historialElementoWifi->setEstadoElemento("Eliminado");
                            $historialElementoWifi->setUsrCreacion($usrCreacion);
                            $historialElementoWifi->setFeCreacion(new \DateTime('now'));
                            $historialElementoWifi->setIpCreacion($ipCreacion);
                            $this->emInfraestructura->persist($historialElementoWifi);
                            $this->emInfraestructura->flush();
                        }
                    }
                }
                
                //Cancelacion en LDAP de servicio anterior                                
                $elemento           = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                              ->find($servicioTecnicoAnterior->getElementoId());
                $modeloElemento   = $elemento->getModeloElementoId();
                $strTipoAprovisionamientoOlt        = "";
                $objDetElementoAprovisionamiento    = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                              ->findOneBy(array("elementoId"    => 
                                                                                                $servicioTecnicoAnterior->getElementoId(), 
                                                                                                "detalleNombre" => "APROVISIONAMIENTO_IP"));
                if (is_object($objDetElementoAprovisionamiento))
                {
                    $strTipoAprovisionamientoOlt = $objDetElementoAprovisionamiento->getDetalleValor();
                    if($strTipoAprovisionamientoOlt === "CNR")
                    {
                        $resultadoJsonLdap = $this->servicioGeneral->ejecutarComandoLdap("E", $servicioAnterior->getId());
                        if($resultadoJsonLdap->status!="OK")
                        {
                            $mensaje = $mensaje . "<br>" . $resultadoJsonLdap->mensaje;
                        }
                    }
                }
            }//if($ultimaMilla == "Fibra Optica")           
        }
        //si el tipo de orden es Reubicacion
        else if($tipoOrden == "R")
        {
            //obtener el servicio anterior
            $spcReubicacion = $this->servicioGeneral->getServicioProductoCaracteristica($objInfoServicio, "REUBICACION", $producto);
            $servicioAnterior  = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($spcReubicacion->getValor());
            //crear el historial del servicio -> Activo--------------------------------------------------------------------------------------
            $this->servicioGeneral->ingresarServicioHistorial($objInfoServicio, "Activo", 
                                                              "Se Activo el Servicio Reubicado", 
                                                              $usrCreacion, $ipCreacion);
            
            //cambiar estado al servicio anterior-------------------------------------------------------------------------------------
            $servicioAnterior->setEstado("Reubicado");
            $this->emComercial->persist($servicioAnterior);
            $this->emComercial->flush();
            
            //crear el historial para servicio anterior-------------------------------------------------------------------------------------
            $this->servicioGeneral->ingresarServicioHistorial($servicioAnterior, "Reubicado", 
                                                              "Se Reubico el Servicio", 
                                                              $usrCreacion, $ipCreacion);
        }
    }
    
    /**
     * Funcion que ejecuta un script para cancelar el servicio de internet
     * en un olt huawei
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 8-04-2015
     */
    public function cancelarServicioOltHuawei($arrayParametros)
    {
        $elementoId     = $arrayParametros['elementoId'];
        $idDocumento    = $arrayParametros['idDocumento'];
        $spid           = $arrayParametros['spid'];
        $tarjeta        = $arrayParametros['tarjeta'];
        $puertoPon      = $arrayParametros['puertoPon'];
        $ontId          = $arrayParametros['ontId'];
        
        $datos = $spid.",".$tarjeta.",".$puertoPon.",".$ontId;
        $comando = "java -jar -Djava.security.egd=file:/dev/./urandom ".$this->pathTelcos."telcos/src/telconet/tecnicoBundle/batch/md_ejecucion.jar '".
                   $this->host."' '".$idDocumento."' 'usuario' 'SSH' '".$elementoId."' '".$datos."' '".$this->pathParameters."'";

        $salida= shell_exec($comando);
        $pos = strpos($salida, "{"); 
        $jsonObj= substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);                
        
        return $resultadJson;
    }
    
    /**
     * Funcion que ejecuta un script para desconfigurar el puerto
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 7-08-2014
     */
    public function cancelarServicioOlt($interfaceElemento,$servProdCaracIndiceCliente,$servicioTecnico,$idDocumento,$login){
        $loginTrunk = substr($login, 0, 17);
        $datos = $interfaceElemento->getNombreInterfaceElemento().",".$servProdCaracIndiceCliente->getValor().",".$servProdCaracIndiceCliente->getValor();
        $comando = "java -jar -Djava.security.egd=file:/dev/./urandom ".$this->pathTelcos."telcos/src/telconet/tecnicoBundle/batch/md_ejecucion.jar '".
            $this->host."' '".$idDocumento."' 'usuario' 'SSH' '".$interfaceElemento->getElementoId()->getId()."' '".
            $datos."' '".$this->pathParameters."'";
        $salida= shell_exec($comando);
        $pos = strpos($salida, "{"); 
            $jsonObj= substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);
        
        return $resultadJson;
    }
    
    /**
     * Funcion que ejecuta un script para desconfigurar la ip fija
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 7-08-2014
     */
    public function desconfigurarIpFija($interfaceElemento, $usuario, $pool, $ip, $mac, $idDocumento){
        $datos = $pool.",".$ip.",".$mac;
        $comando = "java -jar -Djava.security.egd=file:/dev/./urandom ".$this->pathTelcos."telcos/src/telconet/tecnicoBundle/batch/md_ejecucion.jar '".
            $this->host."' '".$idDocumento."' '".$usuario."' 'SSH' '".$interfaceElemento->getElementoId()->getId()."' '".
            $datos."' '".$this->pathParameters."'";

        $salida1= shell_exec($comando);
        $pos1 = strpos($salida1, "{"); 
        $jsonObj1= substr($salida1, $pos1);
        $resultadJson1 = json_decode($jsonObj1);
        
        return $resultadJson1;
    }
    
    /**
     * Funcion que ejecuta un script para obtener la mac dinamica
     * del cliente
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 7-08-2014
     */
    public function getMacIpDinamica($servicioTecnico, $usuario, $interfaceElemento, $servProdCaractIndice, $idDocumentoMac){
        $datos = $interfaceElemento->getNombreInterfaceElemento().",".$servProdCaractIndice->getValor();
        $comando1 = "java -jar -Djava.security.egd=file:/dev/./urandom ".$this->pathTelcos."telcos/src/telconet/tecnicoBundle/batch/md_datos.jar '".
            $this->host."' 'obtenerMacIpDinamica' '".$interfaceElemento->getElementoId()->getId()."' '".$usuario."' '".
            $interfaceElemento->getNombreInterfaceElemento()."' '".$idDocumentoMac."' '".$datos."' '".$this->pathParameters."'";

        $salida1= shell_exec($comando1);
        $pos1 = strpos($salida1, "{"); 
        $jsonObj1= substr($salida1, $pos1);
        $resultadJson1 = json_decode($jsonObj1);
        
        return $resultadJson1;
    }
    
    /**
     * Funcion que ejecuta un script para obtener la ip dinamica
     * del cliente
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 7-08-2014
     */
    public function getIpDinamica($servicioTecnico, $usuario, $interfaceElemento, $mac, $idDocumento){
        $datos = $mac;
        $comando1 = "java -jar -Djava.security.egd=file:/dev/./urandom ".$this->pathTelcos."telcos/src/telconet/tecnicoBundle/batch/md_datos.jar '".
            $this->host."' 'obtenerIpDinamica' '".$interfaceElemento->getElementoId()->getId()."' '".$usuario."' '".
            $interfaceElemento->getNombreInterfaceElemento()."' '".$idDocumento."' '".$datos."' '".$this->pathParameters."'";

        $salida1= shell_exec($comando1);
        $pos1 = strpos($salida1, "{"); 
        $jsonObj1= substr($salida1, $pos1);
        $resultadJson1 = json_decode($jsonObj1);
        
        return $resultadJson1;
    }
    
    /**
     * Funcion que sirve para cancelar clientes transtelco
     * para cancelar un servicio de internet sin ip
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 31-07-2015
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.2 09-05-2016  Se agrega parametro empresa en metodo cancelarServicioTtco por conflictos de producto INTERNET DEDICADO
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.3 08-06-2016  Se corrige validación de servicios preferenciales y se setea correctamente el motivo en historial de servicio
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.4 24-10-2017 - Se agrega filtro de estado en busqueda de contrato asociado al punto a cancelar
     * @since 1.3
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.5 21-12-2017 - En la tabla INFO_DETALLE_ASIGNACION se registra el campo tipo asignado 'EMPLEADO'
     *
     * @since 1.0
     * @param Array $arrayParametros ($servicio,servicioTecnico, interfaceElemento, modeloElemento, $producto, $elemento, $login, 
     *                                $ultimaMilla, $usrCreacion, $ipCreacion, $motivoObj, $prefijoEmpresa, $idPersonaEmpresaRol, $accionObj)
     */
    public function cancelarServicioTtco(   $servicio, 
                                            $servicioTecnico, 
                                            $modeloElemento, 
                                            $producto, 
                                            $elemento, 
                                            $interfaceElemento, 
                                            $login, 
                                            $ultimaMilla,
                                            $usrCreacion, 
                                            $ipCreacion, 
                                            $motivoObj,
                                            $prefijoEmpresa,
                                            $idPersonaEmpresaRol,
                                            $accionObj)
        {
        $nombreModeloElemento = $modeloElemento->getNombreModeloElemento();
        $reqAprovisionamiento = $modeloElemento->getReqAprovisionamiento();

        $caracteristicaVci = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                               ->findBy(array( "descripcionCaracteristica" => "VCI", "estado"=>"Activo"));
        $pcVci             = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                               ->findBy(array( "productoId" => $producto->getId(), 
                                                               "caracteristicaId"=>$caracteristicaVci[0]->getId()));
        $ispcVci           = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                               ->findBy(array( "servicioId" => $servicio->getId(), "productoCaracterisiticaId"=>$pcVci[0]->getId()));
        
        $arrayEstadoNoValidados = array('Anulado',
                                        'Eliminado',
                                        'Cancel',
                                        'Trasladado',
                                        'Rechazada',
                                        'Inactivo',
                                        'AnuladoMigra',
                                        'Reubicado',
                                        'Eliminado-Migra',
                                        'AnuladoMigra',
                                        'migracion_ttco');
        
        if(count($ispcVci)>0)
        {
            if($ispcVci[0]->getValor()>31 && $ispcVci[0]->getValor()<=100)
                $vciValor = "0/".$ispcVci[0]->getValor();
            else
                $vciValor = "0/35";
        }
        else{
            $vciValor = "0/35";
        }
        
        //Se agrega validacion de Olt para no ejecutar fisicamente la cancelación en caso de que se encuentre NO Operativos
        $entitydetalleElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                         ->findOneBy(array("elementoId"     => $interfaceElemento->getElementoId()->getId(), 
                                                                           "detalleNombre"  => "RADIO OPERATIVO"));

        if ($entitydetalleElemento)
        {
            if ($entitydetalleElemento->getDetalleValor() == "NO")
            {
                $reqAprovisionamiento = "NO";
            }
        }
         
        if($reqAprovisionamiento=="SI")
        {
            /*OBTENER SCRIPT--------------------------------------------------------*/
            $scriptArray = $this->servicioGeneral->obtenerArregloScript("cancelarCliente",$modeloElemento);
            $idDocumento = $scriptArray[0]->idDocumento;
            $usuario     = $scriptArray[0]->usuario;
            $protocolo   = $scriptArray[0]->protocolo;
            /*----------------------------------------------------------------------*/

            if($nombreModeloElemento == "A2024")
            {
                $datos        = $interfaceElemento->getNombreInterfaceElemento() . ",1";
                $resultadJson = $this->cancelarClienteA2024($idDocumento, $usuario, $protocolo, $elemento, $datos);
            }
            else if($nombreModeloElemento == "A2048")
            {
                $datos        = $interfaceElemento->getNombreInterfaceElemento() . ",1";
                $resultadJson = $this->cancelarClienteA2048($idDocumento, $usuario, $protocolo, $elemento, $datos);
            }
            else if($nombreModeloElemento == "R1AD24A")
            {
                $datos        = $interfaceElemento->getNombreInterfaceElemento() . "," . 
                                $interfaceElemento->getNombreInterfaceElemento() . "," . 
                                $interfaceElemento->getNombreInterfaceElemento() . ".1," . 
                                $interfaceElemento->getNombreInterfaceElemento() . ".1," . 
                                $interfaceElemento->getNombreInterfaceElemento() . ".1," . 
                                $interfaceElemento->getNombreInterfaceElemento();
                $resultadJson = $this->cancelarClienteR1AD24A($idDocumento, $usuario, $protocolo, $elemento, $datos);
            }
            else if($nombreModeloElemento == "R1AD48A")
            {
                $datos        = $interfaceElemento->getNombreInterfaceElemento() . "," . 
                                $interfaceElemento->getNombreInterfaceElemento() . "," . 
                                $interfaceElemento->getNombreInterfaceElemento() . ".1," . 
                                $interfaceElemento->getNombreInterfaceElemento() . ".1," . 
                                $interfaceElemento->getNombreInterfaceElemento() . ".1," . 
                                $interfaceElemento->getNombreInterfaceElemento();
                $resultadJson = $this->cancelarClienteR1AD48A($idDocumento, $usuario, $protocolo, $elemento, $datos);
            }
            else if($nombreModeloElemento == "6524")
            {
                $datos        = $interfaceElemento->getNombreInterfaceElemento() . "," . 
                                $interfaceElemento->getNombreInterfaceElemento() . "," . 
                                $interfaceElemento->getNombreInterfaceElemento();
                $resultadJson = $this->cancelarCliente6524($idDocumento, $usuario, $protocolo, $elemento, $datos);
            }
            else if($nombreModeloElemento == "7224")
            {
                $datos        = $interfaceElemento->getNombreInterfaceElemento() . "," . 
                                $interfaceElemento->getNombreInterfaceElemento() . "," . 
                                $interfaceElemento->getNombreInterfaceElemento() . "," . 
                                $interfaceElemento->getNombreInterfaceElemento() . "," . 
                                $interfaceElemento->getNombreInterfaceElemento();
                $resultadJson = $this->cancelarCliente7224($idDocumento, $usuario, $protocolo, $elemento, $datos);
            }
            else if($nombreModeloElemento == "MEA1")
            {
                $datos        = $interfaceElemento->getNombreInterfaceElemento() . "," . $vciValor . "," . $vciValor;
                $resultadJson = $this->cancelarClienteMea1($idDocumento, $usuario, $protocolo, $elemento, $datos);
            }
            else if($nombreModeloElemento == "MEA3")
            {
                $datos        = $interfaceElemento->getNombreInterfaceElemento() . "," . $vciValor . "," . $vciValor;
                $resultadJson = $this->cancelarClienteMea3($idDocumento, $usuario, $protocolo, $elemento, $datos);
            }
            else if($nombreModeloElemento == "IPTECOM" || $nombreModeloElemento == "411AH" || $nombreModeloElemento == "433AH")
            {
                $puntoId            = $servicio->getPuntoId();
                $punto              = $this->emComercial->getRepository('schemaBundle:InfoPunto')->find($puntoId->getId());
                $login              = $punto->getLogin();
                $producto           = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                        ->findBy(array("descripcionProducto" => "INTERNET DEDICADO", 
                                                                       "estado"              => "Activo",
                                                                       "empresaCod"          => '18'));
                $caracteristica     = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findBy(array("descripcionCaracteristica" => "MAC"));
                $prodCaract         = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                        ->findBy(array("productoId" => $producto[0]->getId(), 
                                                                       "caracteristicaId" => $caracteristica[0]->getId()));
                $servicioProdCaract = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                        ->findBy(array("servicioId" => $servicio->getId(), 
                                                                       "productoCaracterisiticaId" => $prodCaract[0]->getId()));
                $mac                = $servicioProdCaract[0]->getValor();

                /* OBTENER SCRIPT-------------------------------------------------------- */
                $scriptArray1 = $this->servicioGeneral->obtenerArregloScript("encontrarNumbersMac", $modeloElemento);
                $idDocumento1 = $scriptArray1[0]->idDocumento;
                $usuario1     = $scriptArray1[0]->usuario;
                $protocolo1   = $scriptArray1[0]->protocolo;
                /* ---------------------------------------------------------------------- */

                //numbers de la mac
                $datos2        = $mac;
                $resultadJson2 = $this->cortarClienteIPTECOM($idDocumento1, $usuario1, "radio", $elemento, $datos2);
                $resultado     = $resultadJson2->mensaje;

                if($resultado == "" || $resultado == " ")
                {
                    $status           = "ERROR";
                    $mensaje          = "ERROR CONEXION";
                    $arrayRespuesta[] = array('status' => $status, 'mensaje' => $mensaje);
                    return $arrayRespuesta;
                }

                $numbers = explode("\n", $resultado);

                $flag = 0;

                for($i = 0; $i < count($numbers); $i++)
                {
                    if(stristr($numbers[$i], $mac) === FALSE)
                    {
                        
                    }
                    else
                    {
                        $flag = 1;
                        break;
                    }
                }
                if($flag == 0)
                {
                    $status           = "ERROR";
                    $mensaje          = "ERROR ELEMENTO";
                    $arrayRespuesta[] = array('status' => $status, 'mensaje' => $mensaje);
                }
                else
                {
                    $status           = "OK";
                    $mensaje          = "OK";
                    $arrayRespuesta[] = array('status' => $status, 'mensaje' => $mensaje);
                }
            }

            if($nombreModeloElemento != "IPTECOM" && $nombreModeloElemento != "411AH" && $nombreModeloElemento != "433AH")
            {
                $status           = $resultadJson->status;
                $mensaje          = $resultadJson->mensaje;
                $arrayRespuesta[] = array('status' => $status, 'mensaje' => $mensaje);
            }
        }
        else
        {
            $status           = "OK";
            $mensaje          = "OK";
            $arrayRespuesta[] = array('status' => $status, 'mensaje' => $mensaje);
        }

        if($status == "OK" || $status == "ERROR")
        {
            //crear solicitud para retiro de equipo (ont y wifi)
            $tipoSolicitud = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                  ->findOneBy(array("descripcionSolicitud" => "SOLICITUD RETIRO EQUIPO", "estado" => "Activo"));
            $detalleSolicitud = new InfoDetalleSolicitud();
            $detalleSolicitud->setServicioId($servicio);
            $detalleSolicitud->setTipoSolicitudId($tipoSolicitud);
            $detalleSolicitud->setEstado("AsignadoTarea");
            $detalleSolicitud->setUsrCreacion($usrCreacion);
            $detalleSolicitud->setFeCreacion(new \DateTime('now'));
            $detalleSolicitud->setObservacion("SOLICITA RETIRO DE EQUIPO POR CANCELACION DEL SERVICIO");
            $this->emComercial->persist($detalleSolicitud);

            //crear las caract para la solicitud de retiro de equipo
            $entityAdmiCaracteristica = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')->find(360);

            //valor del ont
            $entityCaract = new InfoDetalleSolCaract();
            $entityCaract->setCaracteristicaId($entityAdmiCaracteristica);
            $entityCaract->setDetalleSolicitudId($detalleSolicitud);
            $entityCaract->setValor($servicioTecnico->getElementoClienteId());
            $entityCaract->setEstado("AsignadoTarea");
            $entityCaract->setUsrCreacion($usrCreacion);
            $entityCaract->setFeCreacion(new \DateTime('now'));
            $this->emComercial->persist($entityCaract);
            $this->emComercial->flush();

            $enlaceCliente = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                  ->findOneBy(array("interfaceElementoIniId" => $servicioTecnico->getInterfaceElementoClienteId(),
                                                    "estado" => "Activo"));
            if($enlaceCliente)
            {
                //obtener wifi
                $interfaceWifi = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                    ->find($enlaceCliente->getInterfaceElementoFinId());
                if($interfaceWifi)
                {
                    //valor del wifi
                    $entityCaract = new InfoDetalleSolCaract();
                    $entityCaract->setCaracteristicaId($entityAdmiCaracteristica);
                    $entityCaract->setDetalleSolicitudId($detalleSolicitud);
                    $entityCaract->setValor($interfaceWifi->getElementoId()->getId());
                    $entityCaract->setEstado("AsignadoTarea");
                    $entityCaract->setUsrCreacion($usrCreacion);
                    $entityCaract->setFeCreacion(new \DateTime('now'));
                    $this->emComercial->persist($entityCaract);
                    $this->emComercial->flush();
                }
            }

            //obtener tarea
            $entityProceso = $this->emSoporte->getRepository('schemaBundle:AdmiProceso')->findOneByNombreProceso("SOLICITAR RETIRO EQUIPO");
            $entityTareas = $this->emSoporte->getRepository('schemaBundle:AdmiTarea')->findTareasActivasByProceso($entityProceso->getId());
            $entityTarea = $entityTareas[0];

            //grabar nuevo info_detalle para la solicitud de retiro de equipo
            $entityDetalle = new InfoDetalle();
            $entityDetalle->setDetalleSolicitudId($detalleSolicitud->getId());
            $entityDetalle->setTareaId($entityTarea);
            $entityDetalle->setLongitud($servicio->getPuntoId()->getLongitud());
            $entityDetalle->setLatitud($servicio->getPuntoId()->getLatitud());
            $entityDetalle->setPesoPresupuestado(0);
            $entityDetalle->setValorPresupuestado(0);
            $entityDetalle->setIpCreacion($ipCreacion);
            $entityDetalle->setFeCreacion(new \DateTime('now'));
            $entityDetalle->setUsrCreacion($usrCreacion);
            $this->emSoporte->persist($entityDetalle);
            $this->emSoporte->flush();

            //obtenemos el persona empresa rol del usuario
            $personaEmpresaRolUsr = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                ->find($idPersonaEmpresaRol);

            //buscamos datos del dept, persona
            $departamento = $this->emGeneral->getRepository('schemaBundle:AdmiDepartamento')->find($personaEmpresaRolUsr->getDepartamentoId());
            $persona = $personaEmpresaRolUsr->getPersonaId();

            //grabamos soporte.info_detalle_asignacion
            $detalleAsignacion = new InfoDetalleAsignacion();
            $detalleAsignacion->setDetalleId($entityDetalle);
            $detalleAsignacion->setAsignadoId($departamento->getId());
            $detalleAsignacion->setAsignadoNombre($departamento->getNombreDepartamento());
            $detalleAsignacion->setRefAsignadoId($persona->getId());
            if($persona->getRazonSocial() == "")
            {
                $nombre = $persona->getNombres() . " " . $persona->getApellidos();
            }
            else
            {
                $nombre = $persona->getRazonSocial();
            }
            $detalleAsignacion->setRefAsignadoNombre($nombre);
            $detalleAsignacion->setPersonaEmpresaRolId($personaEmpresaRolUsr->getId());
            $detalleAsignacion->setTipoAsignado("EMPLEADO");
            $detalleAsignacion->setUsrCreacion($usrCreacion);
            $detalleAsignacion->setFeCreacion(new \DateTime('now'));
            $detalleAsignacion->setIpCreacion($ipCreacion);
            $this->emSoporte->persist($detalleAsignacion);
            $this->emSoporte->flush();

            //crear historial para la solicitud
            $historialSolicitud = new InfoDetalleSolHist();
            $historialSolicitud->setDetalleSolicitudId($detalleSolicitud);
            $historialSolicitud->setEstado("AsignadoTarea");
            $historialSolicitud->setObservacion("GENERACION AUTOMATICA DE SOLICITUD RETIRO DE EQUIPO POR CANCELACION DEL SERIVICIO");
            $historialSolicitud->setUsrCreacion($usrCreacion);
            $historialSolicitud->setFeCreacion(new \DateTime('now'));
            $historialSolicitud->setIpCreacion($ipCreacion);
            $this->emComercial->persist($historialSolicitud);
            //------------------------------------------------------------------------------------------------
            //eliminar todas las ips que tenia ese servicio
            $infoIp = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')->findBy(array("servicioId" => $servicio->getId()));
            for($i = 0; $i < count($infoIp); $i++)
            {
                $datoIp = $infoIp[$i];

                $datoIp->setEstado("Eliminado");
                $this->emInfraestructura->persist($datoIp);
            }
            $this->emInfraestructura->flush();

            //------------------------------------------------------------------------------------------------

            $flagProd = 0;
            $contProdPref = 0;
            //verificar prod preferencial
            $planServicio = $servicio->getPlanId();
            if($planServicio != "" || $planServicio != null)
            {
                $planDetServicio = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')->findBy(array("planId" => $planServicio->getId()));
                for($i = 0; $i < count($planDetServicio); $i++)
                {
                    $prodServicio1 = $planDetServicio[$i]->getProductoId();

                    $productoServicio1 = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($prodServicio1);

                    if($productoServicio1->getEsPreferencia() == "SI")
                    {
                        $flagProd = 1;
                    }
                }
            }
            else
            {
                $prodServicio1 = $servicio->getProductoId();
                $productoServicio1 = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($prodServicio1);

                if($productoServicio1->getEsPreferencia() == "SI")
                {
                    $flagProd = 1;
                }
            }

            //verificar si existe otro producto preferencial
            if($flagProd == 1)
            {
                $puntoPref = $servicio->getPuntoId();
                $serviciosPunto1 = $this->emComercial->getRepository('schemaBundle:InfoServicio')->findBy(array("puntoId" => $puntoPref->getId()));
                for($i = 0; $i < count($serviciosPunto1); $i++)
                {
                    $serv1 = $serviciosPunto1[$i];
                    if (!in_array($serv1->getEstado(),$arrayEstadoNoValidados))
                    {
                        $plan = $serv1->getPlanId();
                        if($plan != "" || $plan != null)
                        {
                            $planDet = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')->findBy(array("planId" => $plan->getId()));
                            for($j = 0; $j < count($planDet); $j++)
                            {
                                $prodServicio = $planDet[$j]->getProductoId();

                                $productoServicio = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($prodServicio);

                                if($productoServicio->getEsPreferencia() == "SI")
                                {
                                    $contProdPref++;
                                }
                            }
                        }
                        else
                        {
                            $prodServicio = $serv1->getProductoId();
                            $productoServicio = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($prodServicio);

                            if($productoServicio->getEsPreferencia() == "SI")
                            {
                                $contProdPref++;
                            }
                        }
                    }
                }
            }

            //se cancelan los servicios por producto preferencial
            if($flagProd == 1 && $contProdPref < 2)
            {
                $punto = $servicio->getPuntoId();
                $serviciosPunto = $this->emComercial->getRepository('schemaBundle:InfoServicio')->findBy(array("puntoId" => $punto->getId()));
                for($i = 0; $i < count($serviciosPunto); $i++)
                {
                    $serv = $serviciosPunto[$i];

                    if($serv->getEstado() == "Activo" || $serv->getEstado() == "In-Corte" || $serv->getEstado() == "In-Temp")
                    {
                        if($serv->getId() == $servicio->getId())
                        {
                            $servicio->setEstado("Cancel");
                            $this->emComercial->persist($servicio);
                            $this->emComercial->flush();

                            //historial del servicio
                            $servicioHistorial = new InfoServicioHistorial();
                            $servicioHistorial->setServicioId($servicio);
                            if($ultimaMilla->getNombreTipoMedio() == "Radio")
                            {
                                $servicioHistorial->setObservacion("Se cancelo el Servicio sin ejecucion de scripts");
                            }
                            else
                            {
                                $servicioHistorial->setObservacion("Se cancelo el Servicio");
                            }
                            $servicioHistorial->setMotivoId($motivoObj->getId());
                            $servicioHistorial->setEstado("Cancel");
                            $servicioHistorial->setUsrCreacion($usrCreacion);
                            $servicioHistorial->setFeCreacion(new \DateTime('now'));
                            $servicioHistorial->setIpCreacion($ipCreacion);
                            $servicioHistorial->setAccion($accionObj->getNombreAccion());
                            $this->emComercial->persist($servicioHistorial);
                            $this->emComercial->flush();
                        }
                        else
                        {
                            $serv->setEstado("Cancel");
                            $this->emComercial->persist($serv);
                            $this->emComercial->flush();

                            //historial del servicio
                            $servicioHistorial = new InfoServicioHistorial();
                            $servicioHistorial->setServicioId($serv);
                            $servicioHistorial->setObservacion("Se cancelo el Servicio");
                            $servicioHistorial->setMotivoId($motivoObj->getId());
                            $servicioHistorial->setEstado("Cancel");
                            $servicioHistorial->setUsrCreacion($usrCreacion);
                            $servicioHistorial->setFeCreacion(new \DateTime('now'));
                            $servicioHistorial->setIpCreacion($ipCreacion);
                            $servicioHistorial->setAccion($accionObj->getNombreAccion());
                            $this->emComercial->persist($servicioHistorial);
                            $this->emComercial->flush();
                        }
                    }
                    else if($servicio->getEstado() == "Factible")
                    {
                        $serv->setEstado("Eliminado");
                        $this->emComercial->persist($serv);
                        $this->emComercial->flush();

                        //historial del servicio
                        $servicioHistorial = new InfoServicioHistorial();
                        $servicioHistorial->setServicioId($serv);
                        $servicioHistorial->setObservacion("Se elimino el Servicio");
                        $servicioHistorial->setMotivoId($motivoObj->getId());
                        $servicioHistorial->setEstado("Eliminado");
                        $servicioHistorial->setUsrCreacion($usrCreacion);
                        $servicioHistorial->setFeCreacion(new \DateTime('now'));
                        $servicioHistorial->setIpCreacion($ipCreacion);
                        $servicioHistorial->setAccion($accionObj->getNombreAccion());
                        $this->emComercial->persist($servicioHistorial);
                        $this->emComercial->flush();
                    }
                }//end for
            }
            else
            {
                $servicio->setEstado("Cancel");
                $this->emComercial->persist($servicio);
                $this->emComercial->flush();

                //historial del servicio
                $servicioHistorial = new InfoServicioHistorial();
                $servicioHistorial->setServicioId($servicio);
                if($ultimaMilla->getNombreTipoMedio() == "Radio")
                {
                    $servicioHistorial->setObservacion("Se cancelo el Servicio sin ejecucion de scripts");
                }
                else
                {
                    $servicioHistorial->setObservacion("Se cancelo el Servicio");
                }
                $servicioHistorial->setMotivoId($motivoObj->getId());
                $servicioHistorial->setEstado("Cancel");
                $servicioHistorial->setUsrCreacion($usrCreacion);
                $servicioHistorial->setFeCreacion(new \DateTime('now'));
                $servicioHistorial->setIpCreacion($ipCreacion);
                $servicioHistorial->setAccion($accionObj->getNombreAccion());
                $this->emComercial->persist($servicioHistorial);
                $this->emComercial->flush();
            }
            //------------------------------------------------------------------------------------------------
            //revisar si es el ultimo servicio en el punto
            $puntoObj = $servicio->getPuntoId();
            $servicios = $this->emComercial->getRepository('schemaBundle:InfoServicio')->findBy(array("puntoId" => $puntoObj->getId()));
            $numServicios = count($servicios);
            $cont = 0;
            for($i = 0; $i < count($servicios); $i++)
            {
                $servicioEstado = $servicios[$i]->getEstado();
                if($servicioEstado == "Cancel" || $servicioEstado == "Cancel-SinEje")
                {
                    $cont++;
                }
            }
            if($cont == ($numServicios))
            {
                $puntoObj->setEstado("Cancelado");
                $this->emComercial->persist($puntoObj);
                $this->emComercial->flush();
            }

            //revisar los puntos si estan todos Cancelados
            $personaEmpresaRol = $puntoObj->getPersonaEmpresaRolId();
            $puntos = $this->emComercial->getRepository('schemaBundle:InfoPunto')
                           ->findBy(array("personaEmpresaRolId" => $personaEmpresaRol->getId()));
            $numPuntos = count($puntos);
            $contPuntos = 0;
            for($i = 0; $i < count($puntos); $i++)
            {
                $punto1 = $puntos[$i];

                if($punto1->getEstado() == "Cancelado")
                {
                    $contPuntos++;
                }
            }
            if(($numPuntos) == $contPuntos)
            {
                //se cancela el contrato
                $contrato = $this->emComercial
                                 ->getRepository('schemaBundle:InfoContrato')
                                 ->findOneBy(array( "personaEmpresaRolId" => $personaEmpresaRol->getId(),
                                                    "estado"              => "Activo"));
                
                $contrato->setEstado("Cancelado");
                $this->emComercial->persist($contrato);
                $this->emComercial->flush();

                //se cancela el personaEmpresaRol
                $personaEmpresaRol->setEstado("Cancelado");
                $this->emComercial->persist($personaEmpresaRol);
                $this->emComercial->flush();

                //se ingresa un registro en el historial empresa persona rol
                $personaHistorial = new InfoPersonaEmpresaRolHisto();
                $personaHistorial->setPersonaEmpresaRolId($personaEmpresaRol);
                $personaHistorial->setEstado("Cancelado");
                $personaHistorial->setUsrCreacion($usrCreacion);
                $personaHistorial->setFeCreacion(new \DateTime('now'));
                $personaHistorial->setIpCreacion($ipCreacion);
                $this->emComercial->persist($personaHistorial);
                $this->emComercial->flush();

                //se cancela el cliente
                $persona = $personaEmpresaRol->getPersonaId();
                $persona->setEstado("Cancelado");
                $this->emComercial->persist($persona);
                $this->emComercial->flush();
            }
            //------------------------------------------------------------------------------------------------

            if($mensaje != "")
            {
                //buscar enlace
                if($ultimaMilla->getNombreTipoMedio() == "Cobre")
                {
                    //cambiar de estado a la interface del elemento de backbone (dslam)
                    $interfaceElemento->setEstado("not connect");
                    $this->emInfraestructura->persist($interfaceElemento);
                    $this->emInfraestructura->flush();

                    $enlaces = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                        ->findBy(array("interfaceElementoIniId" => $interfaceElemento->getId(), "estado" => "Activo"));
                    for($i = 0; $i < count($enlaces); $i++)
                    {
                        //cambiar de estado al enlace
                        $enlaces[$i]->setEstado("Eliminado");
                        $enlace = $enlaces[$i];

                        //cambiar de estado al elemento CPE
                        $interfaceCpe1 = $enlaces[$i]->getInterfaceElementoFinId();
                        $elementoCpe = $interfaceCpe1->getElementoId();
                        $interfaceCpe1->setEstado("not connect");

                        $historial = $this->emInfraestructura->getRepository('schemaBundle:InfoHistorialElemento')
                            ->findBy(array("elementoId" => $elementoCpe->getId(), "estadoElemento" => "Eliminado"));

                        if(count($historial) == 0)
                        {
                            //historial del elemento
                            $historialElemento = new InfoHistorialElemento();
                            $historialElemento->setElementoId($elementoCpe);
                            $historialElemento->setObservacion("Se elimino el cpe por cancelacion de Servicio");
                            $historialElemento->setEstadoElemento("Eliminado");
                            $historialElemento->setUsrCreacion($usrCreacion);
                            $historialElemento->setFeCreacion(new \DateTime('now'));
                            $historialElemento->setIpCreacion($ipCreacion);
                            $this->emInfraestructura->persist($historialElemento);
                            $this->emInfraestructura->flush();
                        }

                        $elementoCpe->setEstado("Eliminado");
                        $this->emInfraestructura->persist($elementoCpe);
                        $this->emInfraestructura->flush();

                        $this->emInfraestructura->persist($enlace);
                        $this->emInfraestructura->flush();

                        $this->emInfraestructura->persist($interfaceCpe1);
                        $this->emInfraestructura->flush();
                    }
                }
                else if($ultimaMilla->getNombreTipoMedio() == "Radio")
                {
                    $interfaceCpeId = $servicioTecnico->getInterfaceElementoClienteId();
                    $interfaceCpe = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')->find($interfaceCpeId);

                    $enlaces = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                        ->findOneBy(array("interfaceElementoIniId" => $interfaceElemento->getId(), "interfaceElementoFinId" => $interfaceCpe->getId(),
                                          "estado" => "Activo"));

                    if($enlaces != "" || $enlaces != null)
                    {
                        $enlaces->setEstado("Eliminado");
                        $this->emInfraestructura->persist($enlaces);
                        $this->emInfraestructura->flush();
                    }

                    $elementoCpeId = $servicioTecnico->getElementoClienteId();
                    $elementoCpe = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($elementoCpeId);
                    $interfaceCpe->setEstado("not connect");

                    $historial = $this->emInfraestructura->getRepository('schemaBundle:InfoHistorialElemento')
                        ->findBy(array("elementoId" => $elementoCpe->getId(), "estadoElemento" => "Eliminado"));

                    if(count($historial) == 0)
                    {
                        //historial del elemento
                        $historialElemento = new InfoHistorialElemento();
                        $historialElemento->setElementoId($elementoCpe);
                        $historialElemento->setObservacion("Se elimino el cpe");
                        $historialElemento->setEstadoElemento("Eliminado");
                        $historialElemento->setUsrCreacion($usrCreacion);
                        $historialElemento->setFeCreacion(new \DateTime('now'));
                        $historialElemento->setIpCreacion($ipCreacion);
                        $this->emInfraestructura->persist($historialElemento);
                        $this->emInfraestructura->flush();
                    }

                    $this->emInfraestructura->persist($elementoCpe);
                    $this->emInfraestructura->flush();

                    $this->emInfraestructura->persist($interfaceCpe);
                    $this->emInfraestructura->flush();
                }
            }//cierra if mensaje vacio
            //enviar mail
            $asunto = "Cancelacion de Servicio: Retiro de Equipos ";
            $this->servicioGeneral->enviarMailCancelarServicio($asunto, $servicio, $motivoObj->getId(), $elemento, 
                                                               $interfaceElemento->getNombreInterfaceElemento(), $servicioHistorial, 
                                                               $usrCreacion, $ipCreacion, $prefijoEmpresa);
        }

        $respuestaFinal[] = array('status' => 'OK', 'mensaje' => "OK");
        return $respuestaFinal;
    }

    /**
     * Funcion que sirve para cancelar un cliente
     * que se encuentra configurado en un dslam de modelo A2024
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function cancelarClienteA2024($idDocumento, $usuario, $protocolo, $elementoId, $datos)
    {
        $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId->getId(), $datos);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve para cancelar un cliente
     * que se encuentra configurado en un dslam de modelo A2048
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function cancelarClienteA2048($idDocumento, $usuario, $protocolo, $elementoId, $datos)
    {
        $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId->getId(), $datos);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve para cancelar un cliente
     * que se encuentra configurado en un dslam de modelo R1AD24A
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function cancelarClienteR1AD24A($idDocumento, $usuario, $protocolo, $elementoId, $datos)
    {
        $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId->getId(), $datos);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve para cancelar un cliente
     * que se encuentra configurado en un dslam de modelo R1AD48A
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function cancelarClienteR1AD48A($idDocumento, $usuario, $protocolo, $elementoId, $datos)
    {
        $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId->getId(), $datos);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve para cancelar un cliente
     * que se encuentra configurado en un dslam de modelo 6524
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function cancelarCliente6524($idDocumento, $usuario, $protocolo, $elementoId, $datos)
    {
        $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId->getId(), $datos);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }
    
     /**
     * Funcion que sirve para ejecutar scripts de cancelacion para servicios
     * de cobre y radio
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 24-07-2015
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 18-05-2016    Se agregan validaciones de operatividad de elementos Radio y Dslam
     * 
     * @param array $arrayParametros (modeloElemento, interfaceElemento, servicio, producto)
     */
    public function cancelarServicioScriptTtco($arrayParametros)
    {
        $modeloElemento         = $arrayParametros['modeloElemento'];
        $interfaceElemento      = $arrayParametros['interfaceElemento'];
        $servicio               = $arrayParametros['servicio'];
        $producto               = $arrayParametros['producto'];
        $nombreModeloElemento   = $modeloElemento->getNombreModeloElemento();
        $elemento               = $interfaceElemento->getElementoId();
        $reqAprovisionamiento   = "SI";
        
        /*OBTENER SCRIPT--------------------------------------------------------*/
        $scriptArray    = $this->servicioGeneral->obtenerArregloScript("cancelarCliente",$modeloElemento);
        $idDocumento    = $scriptArray[0]->idDocumento;
        $usuario        = $scriptArray[0]->usuario;
        $protocolo      = $scriptArray[0]->protocolo;
        /*----------------------------------------------------------------------*/
        
        $spcVci = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "VCI", $producto);
        
        if($spcVci)
        {
            if($spcVci->getValor()>31 && $spcVci->getValor()<=100)
            {
                $vciValor = "0/".$spcVci->getValor();
            }
            else
            {
                $vciValor = "0/35";
            }
        }
        else
        {
            $vciValor = "0/35";
        }
        
        //Se agrega validacion de Olt para no ejecutar fisicamente la cancelación en caso de que se encuentre NO Operativos
        $entitydetalleElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                         ->findOneBy(array("elementoId"     => $interfaceElemento->getElementoId()->getId(), 
                                                                           "detalleNombre"  => "RADIO OPERATIVO"));

        if ($entitydetalleElemento)
        {
            if ($entitydetalleElemento->getDetalleValor() == "NO")
            {
                $reqAprovisionamiento = "NO";
            }
        }
         
        if($reqAprovisionamiento=="SI")
        {
            //ejecucion de scripts para dslams
            if($nombreModeloElemento=="A2024" || $nombreModeloElemento=="A2048")
            {
                $datos          = $interfaceElemento->getNombreInterfaceElemento().",1";
                $resultadJson   = $this->cancelarClienteA2024($idDocumento, $usuario, $protocolo, $elemento, $datos);
            }
            else if($nombreModeloElemento=="R1AD24A" || $nombreModeloElemento=="R1AD48A")
            {
                $datos          = $interfaceElemento->getNombreInterfaceElemento().",".$interfaceElemento->getNombreInterfaceElemento().",".
                                  $interfaceElemento->getNombreInterfaceElemento().".1,".$interfaceElemento->getNombreInterfaceElemento().".1,".
                                  $interfaceElemento->getNombreInterfaceElemento().".1,".$interfaceElemento->getNombreInterfaceElemento();
                $resultadJson   = $this->cancelarClienteR1AD24A($idDocumento, $usuario, $protocolo, $elemento, $datos);
            }
            else if($nombreModeloElemento=="6524")
            {
                $datos          = $interfaceElemento->getNombreInterfaceElemento().",".$interfaceElemento->getNombreInterfaceElemento().",".
                                  $interfaceElemento->getNombreInterfaceElemento();
                $resultadJson   = $this->cancelarCliente6524($idDocumento, $usuario, $protocolo, $elemento, $datos);
            }
            else if($nombreModeloElemento=="7224")
            {
                $datos          = $interfaceElemento->getNombreInterfaceElemento().",".$interfaceElemento->getNombreInterfaceElemento().",".
                                  $interfaceElemento->getNombreInterfaceElemento().",".$interfaceElemento->getNombreInterfaceElemento().",".
                                  $interfaceElemento->getNombreInterfaceElemento();
                $resultadJson   = $this->cancelarCliente7224($idDocumento, $usuario, $protocolo, $elemento, $datos);
            }
            else if($nombreModeloElemento=="MEA1" || $nombreModeloElemento=="MEA3")
            {
                $datos          = $interfaceElemento->getNombreInterfaceElemento().",".$vciValor.",".$vciValor;
                $resultadJson   = $this->cancelarClienteMea1($idDocumento, $usuario, $protocolo, $elemento, $datos);
            }

            $status             = $resultadJson->status; 
            $mensaje            = $resultadJson->mensaje;
            $arrayRespuesta[]   = array('status'=>$status, 'mensaje'=>$mensaje);

            //ejecucion de scripts para las Radios
            if($nombreModeloElemento=="IPTECOM" || $nombreModeloElemento=="411AH" || $nombreModeloElemento=="433AH")
            {            
                $spcMac = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "MAC", $producto);
                $mac = $spcMac->getValor();

                /*OBTENER SCRIPT--------------------------------------------------------*/
                $scriptArray1   = $this->servicioGeneral->obtenerArregloScript("encontrarNumbersMac",$modeloElemento);
                $idDocumento1   = $scriptArray1[0]->idDocumento;
                $usuario1       = $scriptArray1[0]->usuario;
                /*----------------------------------------------------------------------*/

                //numbers de la mac
                $datos2         = $mac;
                $resultadJson2  = $this->cortarClienteIPTECOM($idDocumento1, $usuario1, "radio", $elemento, $datos2);
                $resultado      = $resultadJson2->mensaje;

                if($resultado=="" || $resultado==" ")
                {
                    $status             = "ERROR";
                    $mensaje            = "ERROR CONEXION";
                    $arrayRespuesta[]   = array('status'=>$status, 'mensaje'=>$mensaje);
                    return $arrayRespuesta;
                }

                $numbers = explode("\n", $resultado);

                $flag=0;

                for($i = 0; $i < count($numbers); $i++)
                {
                    if(stristr($numbers[$i], $mac) === FALSE)
                    {

                    }
                    else
                    {
                        $flag = 1;
                        break;
                    }
                }
                if($flag == 0)
                {
                    $status  = "ERROR";
                    $mensaje = "ERROR ELEMENTO";
                    $arrayRespuesta[] = array('status' => $status, 'mensaje' => $mensaje);
                }
                else
                {
                    $status  = "OK";
                    $mensaje = "OK";
                    $arrayRespuesta[] = array('status' => $status, 'mensaje' => $mensaje);
                }
            }
        
        }
        else
        {
            $status           = "OK";
            $mensaje          = "OK";
            $arrayRespuesta[] = array('status' => $status, 'mensaje' => $mensaje);
        }
        
        return $arrayRespuesta;
    }

    /**
     * Funcion que sirve para cancelar un cliente
     * que se encuentra configurado en un dslam de modelo 7224
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function cancelarCliente7224($idDocumento, $usuario, $protocolo, $elementoId, $datos)
    {
        $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId->getId(), $datos);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve para cancelar un cliente
     * que se encuentra configurado en un dslam de modelo MEA1
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function cancelarClienteMea1($idDocumento, $usuario, $protocolo, $elementoId, $datos)
    {
        $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId->getId(), $datos);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve para cancelar un cliente
     * que se encuentra configurado en un dslam de modelo MEA3  
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function cancelarClienteMea3($idDocumento, $usuario, $protocolo, $elementoId, $datos)
    {
        $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId->getId(), $datos);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve para cortar un cliente
     * que se encuentra configurado en un radio IPTECOM
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function cortarClienteIPTECOM($idDocumento, $usuario, $tipo, $elementoId, $datos)
    {
        $salida = $this->servicioGeneral->ejecutarComandoRadio($idDocumento, $usuario, $tipo, $elementoId->getId(), $datos);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve para cancelar un cliente
     * que se encuentra configurado en un radio IPTECOM
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function cancelarClienteIPTECOM($idDocumento, $usuario, $tipo, $elementoId, $datos)
    {
        $salida = $this->servicioGeneral->ejecutarComandoRadio($idDocumento, $usuario, $tipo, $elementoId->getId(), $datos);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve para cortar un cliente
     * que se encuentra configurado en un servidor RADIUS
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function cancelarClienteRADIUS($idDocumento, $usuario, $tipo, $elementoId, $datos)
    {
        $salida = $this->servicioGeneral->ejecutarComandoRadio($idDocumento, $usuario, $tipo, $elementoId, $datos);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }
    
    /**
     * Funcion que genera realizar la cancelacion de servicios OTROS
     * 
     * @author  Creado: Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 22-10-2015
     * 
     * @author  Modificado: Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 06-07-2016   Se agrego el motivo para realizar registrar en la cancelacion del servicio
     * 
     * @author  Modificado: Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.2 06-07-2016   Se adiciona logica para cancelación de servicio Office 365
     * 
     * @author  Modificado: Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.3 01-12-2016 Se crea producto NetlifeCloud en reemplazo del Office 365, se procede a cambiar el producto.
     *
     * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
     * @version 1.4 21-06-2017 Se envia false como parametro a la función getDatosClientePorIdServicio.
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.5 11-10-2017 Se quita el error log del catch
     * 
     * @author Modificado: Jesús Bozada <jbozada@telconet.ec>
     * @version 1.6 07-02-2018 Se cambia estado a traslado en caso de Traslado TN
     *
     * @author Modificado: Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.7 04-07-2018  Si es Producto FOXPREMIUM se Cancelan las Caracteristicas Tecnicas del Servicio Fox  
     * 
     * @author Modificado: Edgar Holguin <eholguin@telconet.ec>
     * @version 1.8 25-07-2018  Se agrega generación de factura por cancelación de servicio con producto NetlifeCloud.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.9 16-12-2018 Se modifica el envío de parámetros a las funciones obtenerInformacionClienteMcAffe 
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.10 02-04-2019 Se agrega log de errores en historial del servicio cuando se presentan problemas al cancelar la suscripción McAfee
     * @since 1.9
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.11 13-06-2019 Se agrega programación para eliminar característica correo de productos mcafee a cancelar, se corrije historial 
     *                          de servicio cuando se presentan errores al activar licencia mcafee
     * @since 1.10
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.12 13-08-2019 Se agrega la cancelación de servicios adicionales I.PROTEGIDO MULTI PAID con tecnología Kaspersky
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.13 23-08-2019 Se elimina envío de variable strMsjErrorAdicHtml a la función gestionarLicencias ya que no está siendo usada
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 2.0 21-11-2019 - Se agrega el proceso para notificar la cancelación del servicio a konibit mediante GDA en caso de aplicar.
     * 
     * @author Jonathan Mazon Sanchez <jmazon@telconet.ec>
     * @version 2.1 28-09-2020 - Se agrega el proceso para la cancelación del servicio Paramount y Noggin.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.2 22-12-2020 Se agrega obtención de status y mensaje del procedimiento P_FACT_OFFICE365_CANCEL
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 2.3 21-01-2021 - Se genera la solicitud automática de verificación de licencia de equipos Fortigate para los servicios que requieran
     *
     * @author Karen Rodríguez V. <kyrodriguez@telconet.ec>
     * @version 2.4 11-03-2021 Se agrega parámetro strEstadoSpc en la consulta de característica de servicio.
     * 
     * @author Jonathan Mazon Sanchez <jmazon@telconet.ec>
     * @version 2.5 09-08-2021 - Se agrega parametriza el flujo de cancelación para los productos de tv.
     * 
     * @author Antonio Ayala Torres <afayala@telconet.ec>
     * @version 2.6 23-09-2021 - Se agrega validación para que no genere solicitud de retiro de equipo si es una 
     *                           migración de equipo secure NG Firewall.
     * @author Jonathan Mazon Sanchez <jmazon@telconet.ec>
     * @version 2.7 09-10-2021 - Se agrega la creacion de tarea rapida al cancelar el producto ECDF.
     * 
     * @author Jonathan Mazon Sanchez <jmazon@telconet.ec>
     * @version 2.8 30-11-2021 - Se modifica la invocación del paquete que genera la factura al cancelar un servicio El canal del fútbol
     *                           invocando a su vez un nuevo paquete que pueda ser parametrizado a futuro para nuevos servicios
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 2.9 01-08-2022 - Se agrega parámetro booleanEliminarEnlaces para los servicios relacionados con cámaras Safecity.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 3.0 14-10-2022 - Se agrega validación para la cancelación de los servicios del producto SEG VEHICULO.
     *
     * @author Leonardo Mero <lemero@telcoet.ec>
     * @version 3.1 06-12-2022 - Se agrega la validacion para el producto SAFE ENTRY
     * 
     * @author Axel Auza <aauza@telconet.ec>
     * @version 3.2 07-06-2023 - Se agrega validación para obtener los elementos por clientes en el producto SEG_VEHICULO
     * 
     * @param  $array $arrayParametrosActivacion
     * 
    */
    public function cancelarServiciosOtros($arrayParametrosActivacion)
    {
        $intIdServicio                          = $arrayParametrosActivacion['idServicio'];
        $intIdAccion                            = $arrayParametrosActivacion['idAccion'];
        $intIdMotivo                            = $arrayParametrosActivacion['idMotivo'];
        $strUsrCreacion                         = $arrayParametrosActivacion['usrCreacion'];
        $strClientIp                            = $arrayParametrosActivacion['clientIp'];
        $strOrigen                              = $arrayParametrosActivacion['strOrigen'];
        $strEmpresaCod                          = $arrayParametrosActivacion['idEmpresa'];
        $intIdPersonaEmpresaRol                 = $arrayParametrosActivacion['intIdPersonaEmpresaRol'];
        $strPrefijoEmpresa                      = $arrayParametrosActivacion['strPrefijoEmpresa'];
        $strMsjHistorial                        = $arrayParametrosActivacion['strMsjHistorial'] 
                                                  ? $arrayParametrosActivacion['strMsjHistorial'] : "Otros: Se cancelo el servicio";
        $strPermiteEnvioCorreoError             = $arrayParametrosActivacion['strPermiteEnvioCorreoError'] 
                                                  ? $arrayParametrosActivacion['strPermiteEnvioCorreoError'] : "SI";
        $booleanEliminarEnlaces                 = isset($arrayParametrosActivacion['booleanEliminarEnlaces']) 
                                                  ? $arrayParametrosActivacion['booleanEliminarEnlaces'] : false;
        $booleanBeginTransaction                = isset($arrayParametrosActivacion['booleanBeginTransaction']) 
                                                  ? $arrayParametrosActivacion['booleanBeginTransaction'] : true;
        $strNombreServicio                      = "";
        $strEstadoServicioAnt                   = "";
        $arrayParametros                        = array();
        $arrayRespuestaServicio                 = array();
        $entityAdmiProducto                     = null;
        $entityInfoPlanCab                      = null;
        $strPlan                                = "";
        $booleanValidaProducto                  = false;
        $booleanValidaProductoProteccionTotal   = false;
        $booleanValidaProductoOffice            = false;
        $booleanValidaProtegido                 = false;
        $booleanValidaOfficeMig                 = false;
        $boolEsProdIProtegMultiPaid             = false;
        $boolFalse                              = false;
        $strTieneSuscriberId                    = "NO";
        $strEstadoServicio                      = "";
        
        $em                                     = $this->emComercial;
        $emSeguridad                            = $this->emSeguridad;
        $emGeneral                              = $this->emGeneral;
        $emFinanciero                           = $this->emFinanciero;
        $intIdOficina                           = $arrayParametrosActivacion['idDepartamento'];
        
        $strEsMigracionNgFirewall   = $arrayParametrosActivacion['esMigracionNgFirewall']?$arrayParametrosActivacion['esMigracionNgFirewall']:"NO";
        
        if($booleanBeginTransaction)
        {
            $em->getConnection()->beginTransaction();
        }
        
        try
        {
            $servicio                       = $em->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
            $accion                         = $emSeguridad->getRepository('schemaBundle:SistAccion')->find($intIdAccion);
            $objMotivo                      = $emGeneral->getRepository('schemaBundle:AdmiMotivo')->find($intIdMotivo);
            
            if ($servicio->getProductoId())
            {
                $entityAdmiProducto = $em->getRepository('schemaBundle:AdmiProducto')->find($servicio->getProductoId());
                $strNombreServicio  = $entityAdmiProducto->getDescripcionProducto();
            }
            else
            {
                $entityInfoPlanCab = $em->getRepository('schemaBundle:InfoPlanCab')->find($servicio->getPlanId());
            }
            
            if (!empty($strOrigen) &&  $strOrigen == "T")
            {
                $strEstadoServicio = "Trasladado";
            }
            else
            {
                $strEstadoServicio = "Cancel";
            }
            $strEstadoServicioAnt = $servicio->getEstado();
            //servicio
            $servicio->setEstado($strEstadoServicio);
            $em->persist($servicio);
            $em->flush();

            //historial del servicio
            $servicioHistorial = new InfoServicioHistorial();
            $servicioHistorial->setServicioId($servicio);
            $servicioHistorial->setObservacion($strMsjHistorial);
            $servicioHistorial->setEstado($strEstadoServicio);
            $servicioHistorial->setMotivoId($objMotivo->getId());
            $servicioHistorial->setUsrCreacion($strUsrCreacion);
            $servicioHistorial->setFeCreacion(new \DateTime('now'));
            $servicioHistorial->setIpCreacion($strClientIp);
            $servicioHistorial->setAccion ($accion->getNombreAccion());
            $em->persist($servicioHistorial);
            $em->flush();
            
            //Cancelar servicios Nuevos McAfee, servicios McAfee antiguos MIGRADOS y servicios NetlifeCloud
            if($entityAdmiProducto || $entityInfoPlanCab)
            {
                //Se verifica si es un producto Nuevo McAfee o un Producto NetlifeCloud
                if ($entityAdmiProducto)
                {
                    $boolEsProdIProtegMultiPaid           = strpos($entityAdmiProducto->getDescripcionProducto(), 'I. PROTEGIDO MULTI PAID');
                    $booleanValidaProducto                = strpos($entityAdmiProducto->getDescripcionProducto(), 'I. PROTEGIDO');
                    $booleanValidaProductoProteccionTotal = strpos($entityAdmiProducto->getDescripcionProducto(), 'I. PROTECCION');
                    $booleanValidaProductoOffice          = strpos($entityAdmiProducto->getDescripcionProducto(), 'NetlifeCloud');
                    
                    if($boolEsProdIProtegMultiPaid !== $boolFalse)
                    {
                        $objSpcSuscriberId  = $this->servicioGeneral
                                                   ->getServicioProductoCaracteristica($servicio, "SUSCRIBER_ID", $entityAdmiProducto);
                        if(!is_object($objSpcSuscriberId))
                        {
                            $arrayParametrosProdCaract['strEstadoSpc'] = 'Pendiente';
                            $objSpcSuscriberId  = $this->servicioGeneral
                                                   ->getServicioProductoCaracteristica($servicio, "SUSCRIBER_ID", 
                                                   $entityAdmiProducto, $arrayParametrosProdCaract);
                        }
                        if(is_object($objSpcSuscriberId))
                        {
                            $strTieneSuscriberId = "SI";
                        }
                    }
                }
                //Se verifica si es un producto Antiguo McAfee u NetlifeCloud
                else
                {
                    $booleanValidaProtegido = strpos($entityInfoPlanCab->getCodigoPlan(), 'MCAFEE');
                    $booleanValidaOfficeMig = strpos($entityInfoPlanCab->getCodigoPlan(), 'NetlifeCloud');
                }
                $arrayProductoACancelar = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->get('NOMBRE_PRODUCTO_CANCELACION_INDIVIDUAL',//nombre parametro cab
                                                          'COMERCIAL', //modulo cab
                                                          'OBTENER_NOMBRE_PRODUCTO',//proceso cab
                                                          'PRODUCTOS_CANCELACION', //descripcion det
                                                          '','','','','',
                                                          $strEmpresaCod); //empresa
                foreach($arrayProductoACancelar as $arrayNombreProducto)
                {
                    $arrayProducto[] = $arrayNombreProducto['valor1'];
                }
                // Cancelación de infoServProdCarac de productos adicionales
                if(in_array($entityAdmiProducto->getNombreTecnico(),$arrayProducto))
                {
                    $arrayInfoServicioProdCaract = $em->getRepository('schemaBundle:InfoServicioProdCaract')
                                                      ->findBy(array("servicioId" => $servicio->getId(), 
                                                                     "estado"     => "Activo"));
                    
                    for($intX=0;$intX<count($arrayInfoServicioProdCaract);$intX++)
                    {
                        $objInfoServicioProdCaract = $arrayInfoServicioProdCaract[$intX];
                        $objInfoServicioProdCaract->setEstado("Eliminado");
                        $objInfoServicioProdCaract->setFeUltMod(new \DateTime('now'));
                        $objInfoServicioProdCaract->setUsrUltMod($strUsrCreacion);
                        $em->persist($objInfoServicioProdCaract);
                        $em->flush();
                    }
                }

                //------------ Creacion de tarea para productos adicionales parametrizados ----------------------------------------
                $arrayProductosACrearTarea = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getOne('PRODUCTO_CANCELACION_TAREA_RAPIDA',//nombre parametro cab
                                                          'TECNICO', //modulo cab
                                                          'CREAR_TAREA',//proceso cab
                                                          'CREAR_TAREA_FLUJO_CANCELAR_PROD_TV', //descripcion det
                                                          $entityAdmiProducto->getNombreTecnico(),//valor1
                                                          '','','','',
                                                          $strEmpresaCod); //empresa

                if(is_array($arrayProductosACrearTarea) && !empty($arrayProductosACrearTarea))
                {
                    $strNombreTarea         = $arrayProductosACrearTarea['valor2'];
                    $strObservacionTarea    = $arrayProductosACrearTarea['valor3'];
                    $strNombreProducto      = $arrayProductosACrearTarea['valor4'];
                    $strNombreClase         = $arrayProductosACrearTarea['valor5'];

                    if(empty($strNombreProducto) || empty($strNombreTarea) || empty($strObservacionTarea))
                    {
                        throw new \Exception("problema al obtener parámetros para la creación de tarea");
                    }
                    $objAdmiTarea =  $this->emSoporte->getRepository('schemaBundle:AdmiTarea')->findOneBy(array("nombreTarea" => $strNombreTarea,
                                                                                                                "estado"      => "Activo"));
                    if(is_object($objAdmiTarea))
                    {
                        $strTareaId = $objAdmiTarea->getId();
                    }
                    if(!empty($intIdServicio))
                    {
                        $objInfoServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
                        if(is_object($objInfoServicio))
                        {
                            $objInfoPunto = $objInfoServicio->getPuntoId();
                            if(is_object($objInfoPunto))
                            {
                                //verifico el sector
                                $objSector = $objInfoPunto->getSectorId();
                                if(is_object($objSector))
                                {
                                    //verifico la parroquia
                                    $objParroquia = $objSector->getParroquiaId();
                                    if(is_object($objParroquia))
                                    {
                                        //verifico el canton
                                        $objCanton = $objParroquia->getCantonId();
                                        if(is_object($objCanton))
                                        {
                                            $strCantonId       = $objCanton->getId();
                                            $strRegionServicio = $objCanton->getRegion();
                                        }
                                    }
                                }
                                $objInfoPersonaEmpresaRol = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                              ->find($intIdPersonaEmpresaRol);
                            }
                            if(is_object($objInfoPersonaEmpresaRol))
                            {
                                $objInfoPersona          = $objInfoPersonaEmpresaRol->getPersonaId();
                                $intIdDepartamentoOrigen = $objInfoPersonaEmpresaRol->getDepartamentoId();
                                if(!empty($intIdDepartamentoOrigen))
                                {
                                    $objAdmiDepartamento = $emGeneral->getRepository('schemaBundle:AdmiDepartamento')->find($intIdDepartamentoOrigen);
                                }

                                if(is_object($objInfoPersona))
                                {
                                    $intIdPersona             = $objInfoPersona->getId();
                                    $strNombrePersonaAsignada = $objInfoPersona->__toString();
                                }
                            }
                        }
                    }
                    
                    $arrayParametros["strObservacion"]          = str_replace("{{nombre_producto}}", $strNombreProducto, $strObservacionTarea);
                    $arrayParametros["intTarea"]                = $strTareaId;
                    $arrayParametros["strTipoAfectado"]         = "Cliente";
                    $arrayParametros["objPunto"]                = $objInfoPunto;
                    $arrayParametros["objDepartamento"]         = $objAdmiDepartamento;
                    $arrayParametros["strCantonId"]             = $strCantonId;
                    $arrayParametros["strEmpresaCod"]           = $strEmpresaCod;
                    $arrayParametros["strPrefijoEmpresa"]       = $strPrefijoEmpresa;
                    $arrayParametros["strUsrCreacion"]          = $strUsrCreacion;
                    $arrayParametros["strIpCreacion"]           = $strClientIp;
                    $arrayParametros["intDetalleSolId"]         = null;
                    $arrayParametros["intDepartamentoOrigen"]   = $intIdDepartamentoOrigen;
                    $arrayParametros["strIdPersonaAsig"]        = $intIdPersona;
                    $arrayParametros["strNombrePersonaAsig"]    = $strNombrePersonaAsignada;
                    $arrayParametros["strIdPerRolAsig"]         = $intIdPersonaEmpresaRol;
                    $arrayParametros["strBanderaTraslado"]      = "S";
                    $arrayParametros["strRegion"]               = $strRegionServicio;
                    $arrayParametros["nombreClaseDocumento"]    = $strNombreClase;
                    $arrayParametros["strEstadoActual"]         = "Finalizada";
                    $arrayParametros["strAccion"]               = "Finalizada";
                    $arrayParametros["asignadoEnDetSeguimiento"]= "Empleado";

                    $strNumeroTarea = $this->serviceCambiarPlanService->crearTareaRetiroEquipoPorDemo($arrayParametros);
                    //Consultar el idDetalle de la tarea
                    $objInfoComunicacion = $this->emComunicacion->getRepository('schemaBundle:InfoComunicacion')->find($strNumeroTarea);

                    if(is_object($objInfoComunicacion))
                    {
                        $intIdDetalle = $objInfoComunicacion->getDetalleId();
                    }

                    //Se cierra porque es una tarea rapida
                    $arrayParametrosHist["intDetalleId"]            = $intIdDetalle;
                    $arrayParametrosHist["strCodEmpresa"]           = $strEmpresaCod;
                    $arrayParametrosHist["strUsrCreacion"]          = $strUsrCreacion;
                    $arrayParametrosHist["strIpCreacion"]           = $strClientIp;
                    $arrayParametrosHist["intIdDepartamentoOrigen"] = $intIdDepartamentoOrigen;
                    $arrayParametrosHist["strEnviaDepartamento"]    = "";
                    $arrayParametrosHist["strOpcion"]               = "Seguimiento";
                    $arrayParametrosHist["strObservacion"]          = "Tarea fue Finalizada Obs: Tarea Rapida";
                    $arrayParametrosHist["strEstadoActual"]         = "Finalizada";
                    $arrayParametrosHist["strAccion"]               = "Finalizada";

                    $this->serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);

                    $arrayParametrosHist["strOpcion"] = "Historial";

                    $this->serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);

                    //CREACION DE FACTURA ECDF ADICIONAL
                    $arrayParametrosFacturacion = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getOne('FACTURACION_SERV_ADICIONAL',//nombre parametro cab
                                                          'FINANCIERO', //modulo cab
                                                          'FACTURACION',//proceso cab
                                                          'DESCRIPCION_FACTURA', //descripcion det
                                                          $entityAdmiProducto->getNombreTecnico(),//valor1
                                                          '','','','',
                                                          $strEmpresaCod); //empresa

                    if(!empty($arrayParametrosFacturacion['valor2']))
                    {
                        $arrayParamsFactCancelServicioAdicional                        = array();
                        $arrayParamsFactCancelServicioAdicional['intIdServicio']       = $intIdServicio;
                        $arrayParamsFactCancelServicioAdicional['strPrefijoEmpresa']   = $strPrefijoEmpresa;
                        $arrayParamsFactCancelServicioAdicional['strEmpresaCod']       = $strEmpresaCod;
                        $arrayParamsFactCancelServicioAdicional['strUsrCreacionFact']  = $arrayParametrosFacturacion['valor2'];
                    }
                    else
                    {
                        throw new \Exception("No se encontraron los parametros requeridos para la ejecución de la Factura del servicio: ".
                                                $entityAdmiProducto->getNombreTecnico());
                    }


                }
                //------------ Creacion de tarea para productos adicionales parametrizados ----------------------------------------

                if($strTieneSuscriberId === "SI")
                {
                    $arrayParamsLicencias           = array("strProceso"                => "CANCELACION_ANTIVIRUS",
                                                            "strEscenario"              => "CANCELACION_PROD_ADICIONAL",
                                                            "objServicio"               => $servicio,
                                                            "objPunto"                  => $servicio->getPuntoId(),
                                                            "strCodEmpresa"             => $strEmpresaCod,
                                                            "objProductoIPMP"           => null,
                                                            "strUsrCreacion"            => $strUsrCreacion,
                                                            "strIpCreacion"             => $strClientIp,
                                                            "strEstadoServicioInicial"  => $strEstadoServicioAnt,
                                                            "strPermiteEnvioCorreoError"=> $strPermiteEnvioCorreoError
                                                            );
                    $arrayRespuestaGestionLicencias = $this->serviceLicenciasKaspersky->gestionarLicencias($arrayParamsLicencias);
                    $strStatusGestionLicencias      = $arrayRespuestaGestionLicencias["status"];
                    $strMensajeGestionLicencias     = $arrayRespuestaGestionLicencias["mensaje"];
                    $arrayRespuestaWs               = $arrayRespuestaGestionLicencias["arrayRespuestaWs"];

                    if($strStatusGestionLicencias === "ERROR")
                    {
                        $arrayRespuestaServicio['status'] = "ERROR";
                        throw new \Exception($strMensajeGestionLicencias);
                    }
                    else if(isset($arrayRespuestaWs) && !empty($arrayRespuestaWs) && $arrayRespuestaWs["status"] !== "OK")
                    {
                        $arrayRespuestaServicio['status']  = 'ERROR';
                        $arrayRespuestaServicio['mensaje'] = $arrayRespuestaWs["mensaje"];
                        return $arrayRespuestaServicio;
                    }
                }
                //Se valida que sea un producto McAfee
                else if(($booleanValidaProducto !== false || $booleanValidaProductoProteccionTotal!== false ||  $booleanValidaProtegido !== false) 
                   && $strTieneSuscriberId === "NO")
                {
                    if($strTieneSuscriberId === "NO")
                    {
                        //historial del servicio
                        $objServicioHistorial = new InfoServicioHistorial();
                        $objServicioHistorial->setServicioId($servicio);
                        $objServicioHistorial->setObservacion(
                                "No existe SUSCRIBER_ID dentro del cliente para ejecutar proceso");
                        $objServicioHistorial->setEstado($servicio->getEstado());
                        $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                        $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                        $objServicioHistorial->setIpCreacion($strClientIp);
                        $objServicioHistorial->setAccion('cancelarCliente');
                        $em->persist($objServicioHistorial);
                        $em->flush();
                    }
                    if ($booleanValidaProtegido !== false && $entityInfoPlanCab)
                    {
                        $strPlan            = $entityInfoPlanCab->getNombrePlan();
                        $strNombreServicio  = $strPlan;
                    }
                    
                    $datosCliente = $em->getRepository("schemaBundle:InfoPersona")->getDatosClientePorIdServicio($servicio->getId(),"esProducto");
                    
                    if (!$datosCliente['ID_PERSONA'])
                    {
                        $datosCliente = $em->getRepository("schemaBundle:InfoPersona")->getDatosClientePorIdServicio($servicio->getId(),false);
                    }
                    
                    $arrayParametros = $this->licenciasMcAfee
                                            ->obtenerInformacionClienteMcAffe(array("intIdPersona"      => $datosCliente['ID_PERSONA'],
                                                                                    "intIdServicio"     => $servicio->getId(),
                                                                                    "strNombrePlan"     => $strPlan,
                                                                                    "strEsActivacion"   => "NO"));
                    
                    $arrayParametros["strTipoTransaccion"] = 'Cancelacion';
                    if($arrayParametros["strError"] == 'true')
                    {
                        $arrayRespuestaServicio['status'] = 'ERROR';
                        throw new \Exception("problemas al obtener informacion del cliente");
                    }

                    $arrayParametros["strNombre"]         = "";
                    $arrayParametros["strApellido"]       = "";
                    $arrayParametros["strIdentificacion"] = "";
                    $arrayParametros["strPassword"]       = "";
                    $arrayParametros["strMetodo"]         = 'CancelarSuscripcion';
                    if($booleanValidaProducto !== false)
                    {
                        $arrayParametros["intLIC_QTY"]        = $arrayParametros["strCantidadDispositivos"];
                        $arrayParametros["intQTY"]            = 1;
                    }
                    else if($booleanValidaProductoProteccionTotal !== false || $booleanValidaProtegido !== false)
                    {
                        $arrayParametros["intLIC_QTY"]        = 0;
                        $arrayParametros["intQTY"]            = 1;
                    }
                    
                    if (is_object($entityAdmiProducto))
                    {
                        $objSpcCorreoElectronico = $this->servicioGeneral->getServicioProductoCaracteristica($servicio,
                                                                                                             'CORREO ELECTRONICO',
                                                                                                             $entityAdmiProducto
                                                                                                            );
                        if (is_object($objSpcCorreoElectronico))
                        {
                            $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objSpcCorreoElectronico, "Eliminado");
                        }
                    }
                }//Se valida que sea un producto NetlifeCloud
                else if($booleanValidaProductoOffice !== false || $booleanValidaOfficeMig !== false)
                {
                    $datosCliente = $em->getRepository("schemaBundle:InfoPersona")->getDatosClientePorIdServicio($servicio->getId(),"esProducto");
                    
                    if (!$datosCliente['ID_PERSONA'])
                    {
                        $datosCliente = $em->getRepository("schemaBundle:InfoPersona")->getDatosClientePorIdServicio($servicio->getId(), false);
                    }
                    
                    $arrayObtenerInformacion                  = array();
                    $arrayObtenerInformacion["intIdPersona"]  = $datosCliente['ID_PERSONA'];
                    $arrayObtenerInformacion["intIdServicio"] = $servicio->getId();
                    $arrayObtenerInformacion["strUser"]       = $strUsrCreacion;
                    $arrayObtenerInformacion["strIpClient"]   = $strClientIp;
                    if ($booleanValidaOfficeMig !== false && $entityInfoPlanCab)
                    {
                        $strPlan = $entityInfoPlanCab->getNombrePlan();
                    }                 
                    // Generacinón de factura por cancelacinó de servicio
                    $strUsrCreacionFact = 'telcos_cancelacion';
                    
                    $arrayParametros = $this->licenciasOffice365->obtenerInformacionClienteOffice365($arrayObtenerInformacion);
                    
                    if($arrayParametros["strError"] == 'true')
                    {
                        $arrayRespuestaServicio['status'] = 'ERROR';
                        throw new \Exception("problemas al obtener informacion del cliente");
                    }

                    //Se envia vacio $arrayParametros["strMetodo"] debido a que no existen métodos para cancelar servicio por parte del proveedor.
                    $arrayParametros["strMetodo"]         = "";
                    $arrayParametros["strUser"]           = $strUsrCreacion;
                    $arrayParametros["strIpClient"]       = $strClientIp;
                    
                    if($booleanValidaProductoOffice !== false)
                    {
                        $arrayParametros["intLIC_QTY"]    = 0;
                        $arrayParametros["intQTY"]        = 1;
                    }

                    $arrayRespuestaServicio = $this->licenciasOffice365->operacionesSuscripcionCliente($arrayParametros);
                    
                    if($arrayRespuestaServicio["procesoExitoso"] == "false")
                    {
                        if($em->getConnection()->isTransactionActive())
                        {
                            $em->getConnection()->rollback();
                        }
                        $arrayRespuestaServicio['status']  = 'ERROR';
                        $arrayRespuestaServicio['mensaje'] = $arrayRespuestaServicio["mensajeRespuesta"];
                        return $arrayRespuestaServicio;
                    }
                }
            }//if($entityAdmiProducto || $entityInfoPlanCab)

            //seteo el flag del cpe si pertenece a un solo servicio
            $booleanFlagCpe = false;
            //seteo el flag de la propiedad del equipo
            $booleanFlagPropiedad = false;
            //obtengo el login auxiliar del servicio
            $strLoginAux    = $servicio->getLoginAux();
            if(!empty($strLoginAux))
            {
                //obtengo el elemento del cliente
                $objElementoCliente = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                            ->findOneBy(array("nombreElemento" => $strLoginAux,
                                                                              "estado"         => "Activo"));
                //validar si otro servicio usa el mismo cpe
                if(is_object($objElementoCliente))
                {
                    //verifico si el equipo pertenece a un solo servicio
                    $arrayParametrosCpe = array('objServicio'    => $servicio,
                                                'objElementoCpe' => $objElementoCliente);
                    $booleanFlagCpe     = $this->validarCpePorServicio($arrayParametrosCpe);
                    //verifico a que pertenece el equipo
                    $objPropiedadElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                ->findOneBy(array("elementoId"          => $objElementoCliente->getId(),
                                                                                  "detalleNombre"       => "PROPIEDAD",
                                                                                  "detalleDescripcion"  => "ELEMENTO PROPIEDAD DE",
                                                                                  "detalleValor"        => "TELCONET",
                                                                                  "estado"              => "Activo"));
                    if(is_object($objPropiedadElemento))
                    {
                        $booleanFlagPropiedad = true;
                    }
                }
            }

            //CREAR SOLICITUD RETIRO DE EQUIPO
            if(is_object($objElementoCliente) && $booleanFlagCpe && $booleanFlagPropiedad)
            {
                //obtener el servicio tecnico
                $objServicioTecnico = $em->getRepository('schemaBundle:InfoServicioTecnico')->findOneBy(array('servicioId' => $servicio->getId()));
                if(is_object($objServicioTecnico) && $strEsMigracionNgFirewall !== "SI")
                {
                    //seteo parametros para la solicitud de retiro de equipo
                    $arrayParametrosSolRet = array();
                    $arrayParametrosSolRet['objServicioTecnico'] = $objServicioTecnico;
                    $arrayParametrosSolRet['intIdElemento']      = $objElementoCliente->getId();
                    $arrayParametrosSolRet['objServicio']        = $servicio;
                    $arrayParametrosSolRet['ipCreacion']         = $strClientIp;
                    $arrayParametrosSolRet['usrCreacion']        = $strUsrCreacion;
                    $arrayParametrosSolRet['booleanEliminarEnlaces'] = $booleanEliminarEnlaces;
                    $this->generarSolicitudRetiroEquipo($arrayParametrosSolRet);
                    $this->eliminarElementoCliente($arrayParametrosSolRet);
                }
            }
            
            //retiro de equipos SEG VEHICULO
            if(is_object($entityAdmiProducto))
            {
                //verificar elementos
                $arrayElementos     = array();
                $arrayParElementos  = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->get('PARAMETROS_SEG_VEHICULOS',
                                                                  'TECNICO',
                                                                  '',
                                                                  'ELEMENTOS_PRODUCTO',
                                                                  $entityAdmiProducto->getId(),
                                                                  '',
                                                                  '',
                                                                  '',
                                                                  '',
                                                                  $strEmpresaCod,
                                                                  'valor5',
                                                                  '',
                                                                  '',
                                                                  '',
                                                                  'GENERAL');
                foreach($arrayParElementos as $arrayItemParEle)
                {
                    $strNombreElemento  = strtolower(str_replace(" ","-",$arrayItemParEle['valor3']))."-".$servicio->getLoginAux();
                    //obtengo el elemento del cliente
                    $objElementoCliente = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                ->findOneBy(array("nombreElemento" => $strNombreElemento,
                                                                                  "estado"         => "Activo"));
                    if(is_object($objElementoCliente))
                    {
                        $arrayElementos[] = $objElementoCliente;
                    }
                }
                //retiro de equipos
                if(!empty($arrayElementos))
                {
                    //seteo parametros para la solicitud de retiro de equipo
                    $arrayParametrosSolRet = array();
                    $arrayParametrosSolRet['arrayElementos'] = $arrayElementos;
                    $arrayParametrosSolRet['objServicio']    = $servicio;
                    $arrayParametrosSolRet['ipCreacion']     = $strClientIp;
                    $arrayParametrosSolRet['usrCreacion']    = $strUsrCreacion;
                    $this->generarSolicitudRetiroEquipo($arrayParametrosSolRet);
                }
                //eliminar los elementos
                foreach($arrayElementos as $objElementoCliente)
                {
                    //seteo parametros para la eliminar el elemento
                    $objServicioTecnico = $em->getRepository('schemaBundle:InfoServicioTecnico')
                                                                    ->findOneBy(array('servicioId' => $servicio->getId()));
                    $arrayParametrosEleEli = array();
                    $arrayParametrosEleEli['objServicioTecnico'] = $objServicioTecnico;
                    $arrayParametrosEleEli['intIdElementoCliente'] = $objElementoCliente->getId();
                    $arrayParametrosEleEli['objServicio']        = $servicio;
                    $arrayParametrosEleEli['ipCreacion']         = $strClientIp;
                    $arrayParametrosEleEli['usrCreacion']        = $strUsrCreacion;
                    $this->eliminarElementoCliente($arrayParametrosEleEli);
                }
            }

            if(is_object($entityAdmiProducto) && $entityAdmiProducto->getNombreTecnico() == 'SAFE ENTRY')
            {
                $boolCancelacionSafeEntry = isset($arrayParametrosActivacion['boolCancelacionSafeEntry'])
                                            ? $arrayParametrosActivacion['boolCancelacionSafeEntry'] : false;
                if(!$boolCancelacionSafeEntry)
                {
                    $arrayParametrosVerificar = array(
                        'objServicio'         => $servicio,
                        'objProducto'         => $entityAdmiProducto,
                        'intIdMotivo'         => $objMotivo->getId(),
                        'intIdAccion'         => $intIdAccion,
                        'idPersonaEmpresaRol' => $intIdPersonaEmpresaRol,
                        'departamentoId'      => $intIdOficina,
                        'strOrigen'           => $strOrigen,
                        'usrCreacion'         => $strUsrCreacion,
                        'ipCreacion'          => $strClientIp,
                        'strEmpresaCod'       => $strEmpresaCod,
                        'strPrefijoEmpresa'   => $strPrefijoEmpresa
                    );
                    $arrayRespuesta = $this->verificarServicioSafeEntryCancelar($arrayParametrosVerificar);
                    if($arrayRespuesta['status'] != 'OK')
                    {
                        throw new Exception ($arrayRespuesta['mensaje']);
                    }
                }
                //Se finaliza la solicitud de cancelacion
                $arrayParametrosCancelacion = array(
                    'objServicio' => $servicio,
                    'ipCreacion' => $strClientIp,
                    'usrCreacion' => $strUsrCreacion);
                $arrayRespuestaCancelacion = $this->finalizarSolicitudCancelacion($arrayParametrosCancelacion);

                if($arrayRespuestaCancelacion['status'] != 'OK')
                {
                    throw new Exception ($arrayRespuestaCancelacion['mensaje']);
                }  

                //Se obtienen los equipos del producto SAFE ENTRY
                $objCaracElementoId = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                            ->findOneBy(array("descripcionCaracteristica" => "ELEMENTO_CLIENTE_ID",
                                                                "estado"                    => "Activo"));
                $objProdCaracEleId  = $em->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                        ->findOneBy(array("productoId"       => $servicio->getProductoId()->getId(),
                                                          "caracteristicaId" => $objCaracElementoId->getId(),
                                                          "estado"           => "Activo"));
                if(is_object($objProdCaracEleId))
                {
                    $arrayServCaracElementos = $em->getRepository('schemaBundle:InfoServicioProdCaract')
                                                    ->findBy(array("servicioId"                => $servicio->getId(),
                                                                   "productoCaracterisiticaId" => $objProdCaracEleId->getId(),
                                                                   "estado"                    => "Activo"));
                    foreach($arrayServCaracElementos as $objItemSerCarEle)
                    {
                        $objElementoCliente = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                ->find($objItemSerCarEle->getValor());
                        if(is_object($objElementoCliente))
                        {
                            $arrayElementos[] = $objElementoCliente;
                        }
                    }
                }
                //retiro de equipos
                if(!empty($arrayElementos))
                {
                    //seteo parametros para la solicitud de retiro de equipo
                    $arrayParametrosSolRet = array(
                        'arrayElementos' => $arrayElementos,
                        'objServicio'    => $servicio,
                        'ipCreacion'     => $strClientIp,
                        'usrCreacion'    => $strUsrCreacion);
                        
                    $this->generarSolicitudRetiroEquipo($arrayParametrosSolRet);
                }
                //eliminar los elementos
                foreach($arrayElementos as $objElementoCliente)
                {
                    //seteo parametros para la eliminar el elemento
                    $objServicioTecnico = $em->getRepository('schemaBundle:InfoServicioTecnico')
                    ->findOneBy(array('servicioId' => $servicio->getId()));
                    
                    $arrayParametrosEleEli = array(
                        'objServicioTecnico'   => $objServicioTecnico,
                        'intIdElementoCliente' => $objElementoCliente->getId(),
                        'objServicio'          => $servicio,
                        'ipCreacion'           => $strClientIp,
                        'usrCreacion'          => $strUsrCreacion);
                        
                    $this->eliminarElementoCliente($arrayParametrosEleEli);
                }

                // Se cambia a estado Eliminado todas las caracteristicas del servicio
                $arraySerProdCaract = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                        ->findByServicioId($servicio->getId());
                foreach ($arraySerProdCaract as $objSerProdCaract) 
                {
                    $objSerProdCaract->setEstado('Eliminado');
                    $this->emComercial->persist($objSerProdCaract);
                    $this->emComercial->flush();                         
                }

                //Consumo del WS SE APP
                $arrayDatosConsumo = array(
                    'strProceso' => 'Cancelacion',
                    'objServicio' => $servicio,
                    'strUser' => $strUsrCreacion,
                    'strIpCreacion' => $strClientIp,
                    'intIdEmpresa'=> $strEmpresaCod);
                $arrayRespuestaWS = $this->serviceInvestigacionDesarrollo->consumoSafeEntryIDWs($arrayDatosConsumo);
                
                //Se ingresa la respuesta del WS
                $this->servicioGeneral->ingresarServicioHistorial( $servicio,$servicio->getEstado(),
                                    'Consumo WS Investigación Desarrollo: '.$arrayRespuestaWS['status'].': '.$arrayRespuestaWS['mensaje'],
                                    $strUsrCreacion,$strClientIp);
            }

            //CREAR SOLICITUD RPA CANCELACION LICENCIA
            $arrayParamDetMarcasRpa = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->get('RPA_MARCA_ELEMENTOS_CANCELACION_LICENCIA',
                                                    'TECNICO',
                                                    '',
                                                    '',
                                                    $servicio->getProductoId()->getId(),
                                                    '',
                                                    '',
                                                    '',
                                                    '',
                                                    $strEmpresaCod);
            //verificar si existe los parametros y el objeto del elemento
            if(is_array($arrayParamDetMarcasRpa) && !empty($arrayParamDetMarcasRpa) && is_object($objElementoCliente) 
                && $booleanFlagCpe && $booleanFlagPropiedad)
            {
                //obtener el id de la marca del elemento
                $intIdMarcaElemento     = $objElementoCliente->getModeloElementoId()->getMarcaElementoId()->getId();
                //seteo el arreglo de los id de las marcas
                $arrayIdMarcasLicencia  = array();
                foreach($arrayParamDetMarcasRpa as $arrayDetParametro)
                {
                    $arrayIdMarcasLicencia[] = $arrayDetParametro['valor2'];
                }
                //verifico si la marca requiere licenciamiento
                if(in_array($intIdMarcaElemento, $arrayIdMarcasLicencia))
                {
                    //obtengo el tipo de solicitud de rpa cancelación licencia
                    $objTipoSolicitudRpa    = $em->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                            ->findOneBy(array("descripcionSolicitud" => "SOLICITUD RPA CANCELACION LICENCIA",
                                                                              "estado"               => "Activo"));
                    if(is_object($objTipoSolicitudRpa))
                    {
                        //ingreso la solicitud
                        $objDetalleSolicitudRpa = new InfoDetalleSolicitud();
                        $objDetalleSolicitudRpa->setServicioId($servicio);
                        $objDetalleSolicitudRpa->setTipoSolicitudId($objTipoSolicitudRpa);
                        $objDetalleSolicitudRpa->setEstado("Pendiente");
                        $objDetalleSolicitudRpa->setObservacion("Se crea la solicitud de RPA Verificación Licencia.");
                        $objDetalleSolicitudRpa->setUsrCreacion($strUsrCreacion);
                        $objDetalleSolicitudRpa->setFeCreacion(new \DateTime('now'));
                        $em->persist($objDetalleSolicitudRpa);
                        $em->flush();
                        //crear historial para la solicitud
                        if(is_object($objDetalleSolicitudRpa))
                        {
                            $objHistorialSolicitudRpa = new InfoDetalleSolHist();
                            $objHistorialSolicitudRpa->setDetalleSolicitudId($objDetalleSolicitudRpa);
                            $objHistorialSolicitudRpa->setEstado("Pendiente");
                            $objHistorialSolicitudRpa->setObservacion("Se crea la solicitud de RPA Verificación Licencia.");
                            $objHistorialSolicitudRpa->setUsrCreacion($strUsrCreacion);
                            $objHistorialSolicitudRpa->setFeCreacion(new \DateTime('now'));
                            $objHistorialSolicitudRpa->setIpCreacion($strClientIp);
                            $em->persist($objHistorialSolicitudRpa);
                            $em->flush();
                        }
                    }
                }
            }

            $arrayRespuestaServicio['status']  = 'OK';
            $arrayRespuestaServicio['mensaje'] = '';
            $em->flush();
            if($booleanBeginTransaction)
            {
                $em->getConnection()->commit();
            }
            
            // Si es servicio cancelado con producto NetlifeCloud genera factura
            if($booleanValidaProductoOffice !== false ) 
            {
                $strStatusProductoOffice    = str_repeat(' ', 5);
                $strMsjErrorProductoOffice  = str_repeat(' ', 1000);
                $strSql    = "BEGIN DB_FINANCIERO.FNCK_FACTURACION.P_FACT_OFFICE365_CANCEL(:Pn_ServicioId, "
                                                                                        . ":Pv_PrefijoEmpresa, "
                                                                                        . ":Pv_EmpresaCod, "
                                                                                        . ":Pv_UsrCreacion, "
                                                                                        . ":Pv_Ip, "
                                                                                        . ":Pv_Status, "
                                                                                        . ":Pv_Mensaje); END;";
                $objStmt   = $emFinanciero->getConnection()->prepare($strSql);
                $objStmt->bindParam('Pn_ServicioId', $intIdServicio);
                $objStmt->bindParam('Pv_PrefijoEmpresa', $strPrefijoEmpresa);
                $objStmt->bindParam('Pv_EmpresaCod', $strEmpresaCod);
                $objStmt->bindParam('Pv_UsrCreacion', $strUsrCreacionFact);
                $objStmt->bindParam('Pv_Ip', $strClientIp);
                $objStmt->bindParam('Pv_Status', $strStatusProductoOffice);
                $objStmt->bindParam('Pv_Mensaje', $strMsjErrorProductoOffice);
                $objStmt->execute();  
            }
            
            if(isset($arrayParamsFactCancelServicioAdicional) && !empty($arrayParamsFactCancelServicioAdicional))
            {
                $this->emComercial->getRepository("schemaBundle:InfoServicio")
                                  ->generaFacturacionCancelServicioAdicional($arrayParamsFactCancelServicioAdicional);
            }
        }
        catch (\Exception $ex)
        {
            if($em->getConnection()->isTransactionActive() && $booleanBeginTransaction)
            {
                $em->getConnection()->rollback();
            }

            if ($arrayRespuestaServicio['status']  == 'ERROR')
            {
                $arrayRespuestaServicio['mensaje'] = $ex->getMessage();    
            }
            else
            {
                $arrayRespuestaServicio['status']  = 'ERROR';
                $arrayRespuestaServicio['mensaje'] = '';  
            }
            
        }


        //Proceso para notificar la cancelación del servicio a konibit mediante GDA en caso de aplicar.
        try
        {
            if ($strPrefijoEmpresa === 'MD' && $arrayRespuestaServicio['status'] === 'OK' && is_object($servicio))
            {
                $this->emInfraestructura->getRepository('schemaBundle:InfoServicioTecnico')
                        ->notificarKonibit(array ('intIdServicio'  => $servicio->getId(),
                                                  'strTipoProceso' => 'CANCELAR',
                                                  'strTipoTrx'     => 'INDIVIDUAL',
                                                  'strUsuario'     => $strUsrCreacion,
                                                  'strIp'          => $strClientIp,
                                                  'objUtilService' => $this->serviceUtil));
            }
        }
        catch (\Exception $objException)
        {
            $this->serviceUtil->insertError('Telcos+',
                                            'InfoCancelarServicioService->cancelarServiciosOtros->adicional',
                                            'IdServicio: '.$servicio->getId().' - Error: '.$objException->getMessage(),
                                             $strUsrCreacion,
                                             $strClientIp);
        }


        return $arrayRespuestaServicio;
    }

    /**
     * Funcion que sirve para realizar la cancelacion de servicios para la empresa TN
     * 
     * @since 1.0
     * 
     * @author Francisco Adum
     * @version 1.1 25-05-2016
     * Se agregan validaciones para cancelaciones
     * 
     * @author Allan Suarez
     * @version 1.2 15-06-2016 - Se devuelve statusCode necesario cuando haya invocacion a nivel de WS
     * 
     * @author Allan Suarez
     * @version 1.3 18-06-2016 - Se escribe historial de fallo para las solicitudes no exitosas
     * 
     * @author Francisco Adum
     * @version 1.4 27-06-2016
     * Se agregan validaciones para que se puedan cancelar servicios OTROS
     * 
     * @author Robinson Salgado
     * @version 1.5 11-07-2016
     * Se agrega el envio de correo de notificacion
     *    
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.6 05-07-2016 Se cambia envio de vlan->mac al WS y que sea solo del cliente a configurar
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.7 08-09-2016 Se agrega validaciones para evitar Fatal Errors  generados cuando datos importantes son faltantes
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.8 03-10-2016 Se agrega llamado a funcion que actualiza las capacidades del concentrador dado la cancelacion de su extremo
     *      
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.9 10-11-2016 Se agrega validación para que solo en servicios con enlaces PRINCIPAL realicen recalculo de BW en Concentrador
     *
     * @author Allan Suarez C. <arsuarez@telconet.ec>
     * @version 2.0 2016-11-14 Se incluye llamado a metodo de control de excepciones para manejo de mensajes de las mismas de manera correcta
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 2.1 18-11-2016 Se agrega validación para cuando se requiera cancelar servicio concentrador y tiene extremos activos no continue
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 2.2 24-11-2016 Se agrega funcion para cancelar equipo de cliente cuando se el ultimo servicio ligado a este
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 2.3 17-11-2016 Se cambia la validación para el retiro de equipo, solo es necesario que tenga elemento cliente
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 2.4 31-01-2017 Se agregan validación y programación para generación de tarea interna a dpto IPCCL2 para servicios que no son enlaces
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 2.5 19-05-2017 Se agregan validación para que no se pueda cancelar Servicios si existe al menos otro Rechazada dentro del punto
     * 
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 2.6 07-02-2017 Se agrega funcionalidad para poder ralizar la cancelación de servicios backups ligados a un servicio principal
     * 
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 2.7 05-06-2017 Se agrega validacion de atributo de objeto dentro de validacion de existencia del mismo ( $objProducto )
     * 
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 2.8 13-07-2017 Se coloca bloque donde se verifica enlace Principal dentro de bloque de flujos que son Enlace y poseen data tecnica
     * 
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 2.9 05-07-2017 Se agrega bloque de congifuracion de Bw para cuando exsten 2 servicios apuntando a un mismo cpe
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 3.0 19-07-2017 Se envian los parametros en un array a la función "getArrayInfoCambioPlanPorSolicitud"
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 3.1 03-08-2017 Se valida para que los retiros de equipo se hagan solo cuando hay un servicio
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 3.2 05-10-2017 Se agrega código para eliminar caracteristica SERVICIO_MISMA_ULTIMA_MILLA de todos los servicios que dependan
     *                         del servicio rechazado
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 3.3 21-01-2018 Se agrega lógica para cambio de tipo medio.
     * @since 3.1
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 3.3 22-01-2018  Se agrega programación para procesar cancelación de servicios por traslado
     * @since 3.2
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 3.4 03-04-2018 Se agrega la invocación a la función para eliminar rutas
     * @since 3.3
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @since 24-05-2018
     * @version 3.5 - Se envia descripcion de acuerdo a la Ultima milla del servicio para identificacion de NW
     *
     * @author Modificado: Jesús Bozada <jbozada@telconet.ec>
     * @version 3.6 19-04-2018 - Se agrega programación por integración de app Telcograph con procesos de Telcos
     * @since 3.5
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 3.6 24-01-2018 - Se aumenta validación para poder cancelar correctamente cuando se trate de un L3MPLS correspondiente a NETVOICE
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 3.7 14-05-2019 - Se actualiza el mensaje de error de MAC solo para cambio de tipo medio
     * @since 3.6
     *
     * @author Modificado: Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 3.8 21-11-2019 - Se identifica la region del login antes de crear la tarea automática de cancelación.
     * @since 3.7
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 3.9 01-06-2020 - Se agrega el id del servicio a la url 'configBW' del ws de networking para la validación del BW
     * @since 3.8
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 4.0 01-06-2020 - Se agrega el id del servicio a la url 'configSW' del ws de networking para la validación del BW
     * @since 3.9
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 4.1 14-07-2020 - Se agrega la acción de cancelar en la actualización de la capacidad del concentrador
     * @since 4.0
     *
     * @author Modificado: Néstor Naula López <nnaulal@telconet.ec>
     * @version 4.2 21-05-2020 - Se realiza el envío de información cliente cancelado al Zabbix.
     * @since 4.1
     *
     * @author Modificado: Néstor Naula López <nnaulal@telconet.ec>
     * @version 4.3 20-07-2020 - Se realiza cambio del services de soporte a inforServicio para el proceso del Zabbix.
     * @since 4.2
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 4.4 09-07-2020 - Se valida si el servicio a cancelar tiene servicios relacionados por instalación simultánea y si el servicio
     *                           requiere flujo.
     * @since 4.3
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 4.5 25-09-2020 - Se valida si el servicio a cancelar tiene servicios relacionados como FastCloud.
     *                           
     * @since 4.4
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 4.6 21-01-2021 - Se genera la solicitud automática de verificación de licencia de equipos Fortigate para los servicios que requieran
     * @since 4.5
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 4.7 22-03-2021 Se abre la programacion para servicios Internet SDWAN
     * @since 4.6
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 4.8 18-10-2021 Se realizan ajustes para obtener correctamente el valor de las VRF para servicios L3MPLS.
     * @since 4.7
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 4.9 23-02-2022 Se realizan ajustes para opcion configSW.
     * @since 4.8
     * 
     * @author Liseth Candelario <lcandelario@telconet.ec>
     * @version 4.10 13-07-2022 Se llama a una función para actualizar el estado de UM en ARCGIS - TN
     * @since 4.9
     */
    public function cancelarServicioTn($arrayPeticiones)
    { 
        //Se obtiene la informacion a enviar
        $flagCpe                = false;
        $flagUm                 = false;
        $flagEsEnlace           = false;
        $objDetalleSolicitud    = null;
        $statusCode             = "200";
        $strNombreCliente       = "";
        $strCiudadCliente       = "";
        $arrayParametrosCambP   = array();
        $strObservacionTraslado = "";
        $strSubRedIp            = "";
        $strMensajeError        = "";
        $objMotivo              = $this->emGeneral->getRepository('schemaBundle:AdmiMotivo')->find($arrayPeticiones['idMotivo']);
        $objAccion              = $this->emSeguridad->getRepository('schemaBundle:SistAccion')->find($arrayPeticiones['idAccion']);
        $objServicio            = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($arrayPeticiones['idServicio']);
        $objProducto            = $objServicio->getProductoId();
        $objServicioTecnico     = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                            ->findOneBy(array("servicioId" => $arrayPeticiones['idServicio']));

        $arrayParametros                = array();
        $arrayParametros['objServicio'] = $objServicio;
        $boolSeCancela                  = $this->servicioGeneral->validarCancelacionServicio($arrayParametros);
        $intIdElementoCPE               = 0;
        
        //Variables que se declaran para consultar si existen servicios relacionados con instalación simultánea y si el servicio encontrado
        //requiere flujo (OTROS)
        $intIdServicio                  = $arrayPeticiones['idServicio'];
        $intIdAccion                    = $arrayPeticiones['idAccion'];
        $intIdMotivo                    = $arrayPeticiones['idMotivo'];
        
        if(is_object($arrayPeticiones['objNuevoServicio']))
        {
            if($objServicioTecnico->getElementoClienteId() !== null)
            {
                //Verificar que el elemento cliente no sea directamente el CPE
                $objElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                       ->find($objServicioTecnico->getElementoClienteId());
                if($objElemento->getModeloElementoId()->getTipoElementoId()->getNombreTipoElemento() != 'CPE')
                {
                    $arrayDatosServicioTecnico['tipoElemento']                = "CPE";
                    $arrayDatosServicioTecnico['interfaceElementoConectorId'] = $objServicioTecnico->getInterfaceElementoClienteId();

                    $arrayDatoCpe = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                            ->getElementoClienteByTipoElemento($arrayDatosServicioTecnico);
                    if($arrayDatoCpe['msg'] == "FOUND")
                    {
                        $intIdElementoAntCPE = $arrayDatoCpe['idElemento'];
                    }
                }
                else
                {
                    $intIdElementoAntCPE = $objServicioTecnico->getElementoClienteId();
                }
            }
            
            if($arrayPeticiones['objNuevoServicio']->getElementoClienteId() !== null)
            {
                //Verificar que el elemento cliente no sea directamente el CPE
                $objElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                       ->find($arrayPeticiones['objNuevoServicio']->getElementoClienteId());
                if($objElemento->getModeloElementoId()->getTipoElementoId()->getNombreTipoElemento() != 'CPE')
                {
                    $arrayDatosServicioTecnico['tipoElemento']                = "CPE";
                    $arrayDatosServicioTecnico['interfaceElementoConectorId'] = $arrayPeticiones['objNuevoServicio']->getInterfaceElementoClienteId();

                    $arrayDatoCpe = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                      ->getElementoClienteByTipoElemento($arrayDatosServicioTecnico);
                    if($arrayDatoCpe['msg'] == "FOUND")
                    {
                        $intIdElementoNuevoCPE = $arrayDatoCpe['idElemento'];
                    }
                }
                else
                {
                    $intIdElementoNuevoCPE = $arrayPeticiones['objNuevoServicio']->getElementoClienteId();
                }
            }
            // Si el cpe del servicio a cancelarse y el nuevo son identicos no permito que se retire.
            $intIdElementoCPE = ($intIdElementoAntCPE == $intIdElementoNuevoCPE) ? $intIdElementoAntCPE : 0;
        }
        
        if(!$boolSeCancela)
        {
            $arrayFinal[] = array('status'     =>"ERROR", 
                                  'mensaje'    =>'El punto tiene un servicio <b>Rechazado</b>. Por favor gestionar la anulación del '
                                                 . 'mismo con el asesor comercial para continuar con la cancelación.',
                                  'statusCode' => 500);
            return $arrayFinal;
        }
        
        //Verificar si lo que se quiere cancelar es un Concentrador
        if(is_object($objProducto))
        {
            if($objProducto->getEsEnlace() == "SI")
            {
                $flagEsEnlace = true;
            }
            
            if($objProducto->getEsConcentrador() == 'SI')
            {
                //Los extremos con los siguientes estados no serán considerados dentro de la consulta
                $arrayParametrosConcen['estadosDiscriminados']      = array('Eliminado','Anulado','Rechazada','Cancel');
                $arrayParametrosConcen['intIdServicioConcentrador'] = $arrayPeticiones['idServicio'];
                
                $arrayInformacionExtremosConcentradores = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                                            ->getArrayInformacionConcentradorExtremo($arrayParametrosConcen);                                
                
                if(isset($arrayInformacionExtremosConcentradores))
                {
                    //Se obtiene todos los extremos ( se resta uno ya que es el correspondiente al concentrador en si mismo )
                    $intNumeroServiciosActivos  = count($arrayInformacionExtremosConcentradores)-1;                    
                    
                    //Si encuentra al menos un servicio activo no continua con la cancelacion del concentrador
                    if($intNumeroServiciosActivos > 0)
                    {
                        $arrayFinal[] = array('status'     =>"ERROR", 
                                              'mensaje'    =>'No puede ser Cancelado Servicio Concentrador ya que posee Extremos en estado Activo',
                                              'statusCode' => 500);
                        
                        $arrayPeticiones['objDetalleSolicitud'] = $objDetalleSolicitud;
                        $arrayPeticiones['mensajeError']        = $arrayFinal[0]['mensaje'];
                        $this->servicioGeneral->insertarHistorialSolicitudError($arrayPeticiones);
                    
                        return $arrayFinal;
                    }
                }   
            }
        }
                
        $arrayParametrosCambP["idServicio"]  = $arrayPeticiones['idServicio'];
        $arrayParametrosCambP["tipoProceso"] = "";        
                
        //Verificar si no existe solicitud de Cancelacion
        $arrayResultado = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                            ->getArrayInfoCambioPlanPorSolicitud($arrayParametrosCambP);
        
        if($arrayResultado && count($arrayResultado)>0)
        {
            $objDetalleSolicitud = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                    ->find($arrayResultado['idSolicitud']);
            
            $arrayPeticiones['idSolicitudPadre'] = $arrayResultado['idSolicitudPadre'];
        }
        
        //Parametros para el Correo de Notificacion
        $arrayParametrosCorreo = array();
        $arrayParametrosCorreo['idSolicitudPadre'] = isset($arrayPeticiones['idSolicitudPadre'])?$arrayPeticiones['idSolicitudPadre']:null;
        $arrayParametrosCorreo['idServicio']       = $arrayPeticiones['idServicio'];
        $arrayParametrosCorreo['usrCreacion']      = $arrayPeticiones['usrCreacion'];
                
        //*DECLARACION DE TRANSACCIONES------------------------------------------*/
        $this->emSoporte->getConnection()->beginTransaction();
        $this->emComercial->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();
        //*----------------------------------------------------------------------*/

        //LOGICA DE NEGOCIO-----------------------------------------------------*/
        try
        {
            if ($arrayPeticiones['strOrigen'] == "T")
            {
                $strObservacionTraslado      = "Este servicio cancelado registra la siguiente información técnica : <br>";
                $arrayServicioProductoCaract = $this->emComercial
                                                    ->getRepository('schemaBundle:InfoServicioProdCaract')
                                                    ->findBy(array("servicioId" => $objServicio->getId(), 
                                                                   "estado"     => "Activo"));
                if($arrayServicioProductoCaract)
                {
                    foreach($arrayServicioProductoCaract as $objServicioProductoCarac)
                    {
                        $objAdmiProdCaract = $this->emComercial
                                                  ->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                  ->find($objServicioProductoCarac->getProductoCaracterisiticaId());
                        if (is_object($objAdmiProdCaract))
                        {
                            $objCaracteristica = $objAdmiProdCaract->getCaracteristicaId();
                            if (is_object($objCaracteristica))
                            {
                                $strObservacionTraslado .= "<b>".$objCaracteristica->getDescripcionCaracteristica().":</b> ".
                                                           $objServicioProductoCarac->getValor() ."<br>";
                            }
                        }
                    }
                }
                $arrayIPs = $this->emInfraestructura
                                 ->getRepository('schemaBundle:InfoIp')
                                 ->findBy(array("servicioId" => $objServicio->getId(),
                                                "estado"     => "Activo"));
                if($arrayIPs)
                {
                    foreach($arrayIPs as $objIp)
                    {
                        $strSubRedIp = "";
                        $intSubRedId = $objIp->getSubredId();
                        if (!empty($intSubRedId))
                        {
                          $objSubRed = $this->emInfraestructura
                                            ->getRepository('schemaBundle:InfoSubred')
                                            ->find($objIp->getSubredId());  
                          if (is_object($objSubRed))
                          {
                              $strSubRedIp = $objSubRed->getSubred();
                          }
                        }
                        $strObservacionTraslado .= "<b>IP: </b> <br>"; 
                        $strObservacionTraslado .= "    <b>Ip:</b>      " . $objIp->getIp()     . "<br>";
                        $strObservacionTraslado .= "    <b>Mascara:</b> " . $objIp->getMascara(). "<br>";
                        $strObservacionTraslado .= "    <b>Subred:</b>  " . $strSubRedIp        . "<br>";
                        $strObservacionTraslado .= "    <b>Gateway:</b> " . $objIp->getGateway(). "<br>";
                        $strObservacionTraslado .= "    <b>Tipo:</b>    " . $objIp->getTipoIp()   . "<br>";
                        $strObservacionTraslado .= "    <b>Estado:</b>  " . $objIp->getEstado() . "<br>";
                    }
                }
                //obtener cpe del servicio 
                $objElementoCpe = $this->getElementoCpeServicioTn($objServicioTecnico);
                //validar si otro servicio usa el mismo cpe
                if(is_object($objElementoCpe))
                {
                    $strMacServicio = $this->servicioGeneral->getMacPorServicio($arrayPeticiones['idServicio']); 
                    
                    $strObservacionTraslado .= "<b>CPE: </b> <br>"; 
                    $strObservacionTraslado .= "    <b>Nombre:</b>   " . $objElementoCpe->getNombreElemento()     . "<br>";
                    $strObservacionTraslado .= "    <b>Modelo:</b>   " . $objElementoCpe->getModeloElementoId()
                                                                                        ->getNombreModeloElemento()."<br>";
                    $strObservacionTraslado .= "    <b>Marca:</b>    " . $objElementoCpe->getModeloElementoId()
                                                                                        ->getMarcaElementoId()
                                                                                        ->getNombreMarcaElemento(). "<br>";
                    $strObservacionTraslado .= "    <b>Serie:</b>    " . $objElementoCpe->getSerieFisica()     . "<br>";
                    $strObservacionTraslado .= "    <b>Mac:</b>      " . $strMacServicio     . "<br>";
                    $arrayDetalleElementoCpe = $this->emInfraestructura
                                                    ->getRepository('schemaBundle:InfoDetalleElemento')
                                                    ->findBy(array("elementoId"    => $objElementoCpe->getId(),
                                                                   "estado"        => "Activo"));
                    
                    foreach($arrayDetalleElementoCpe as $objDetalleElementoCpe)
                    {
                        $strObservacionTraslado .= "    <b>".$objDetalleElementoCpe->getDetalleNombre().":</b> " . 
                                                   $objDetalleElementoCpe->getDetalleValor(). "<br>";
                    }
                }
                
                $arrayRutas = $this->emInfraestructura
                                   ->getRepository("schemaBundle:InfoRutaElemento")
                                   ->findBy(array("servicioId"    => $objServicio->getId(),
                                                  "estado"        => "Activo"));
                if($arrayRutas && count($arrayRutas)>0)
                {
                    $strObservacionTraslado .= "<b>Rutas: </b> <br>"; 
                }
                foreach($arrayRutas as $objRuta)
                {
                    $strObservacionTraslado .= "    <b>Ruta </b> <br>";
                    $strObservacionTraslado .= "    <b>Nombre:</b>          " . $objRuta->getNombre(). "<br>";
                    $strObservacionTraslado .= "    <b>Red lan:</b>         " . $objRuta->getRedLan(). "<br>";
                    $strObservacionTraslado .= "    <b>Mascara red lan:</b> " . $objRuta->getMascaraRedLan(). "<br>";
                    $objRuta->setEstado("Eliminado");
                    $this->emInfraestructura->persist($objRuta);
                    $this->emInfraestructura->flush();
                }
                // Creacion del historial del servicio
                $objServicioHistorial = new InfoServicioHistorial();
                $objServicioHistorial->setServicioId($objServicio);
                $objServicioHistorial->setObservacion($strObservacionTraslado);
                $objServicioHistorial->setEstado($objServicio->getEstado());
                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                $objServicioHistorial->setIpCreacion($arrayPeticiones['ipCreacion']);
                $objServicioHistorial->setUsrCreacion($arrayPeticiones['usrCreacion']);
                $this->emComercial->persist($objServicioHistorial);
                $this->emComercial->flush();
                
            }
            if($flagEsEnlace)
            {
                if(!is_object($objServicioTecnico))               
                {
                    $this->serviceUtil->lanzarExcepcion('OBJETO','Data Técnica del servicio para realizar la Cancelación');
                }
                                                            
                $objInterfaceElemento   = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                ->find($objServicioTecnico->getInterfaceElementoId());                
                $objElemento            = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($objServicioTecnico->getElementoId());
                
                $objDetalleAnillo = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                            ->findOneBy(array(  "elementoId"    => $objElemento->getId(),
                                                                                "detalleNombre" => "ANILLO",
                                                                                "estado"        => "Activo"));
                
                if(!$objDetalleAnillo)
                {
                    $arrayFinal[] = array('status'     =>"ERROR", 
                                          'mensaje'    =>'Switch '.$objElemento->getNombreElemento().' no tiene informacion de ANILLO',
                                          'statusCode' => 500);
                    
                    $arrayPeticiones['objDetalleSolicitud'] = $objDetalleSolicitud;
                    $arrayPeticiones['mensajeError']        = $arrayFinal[0]['mensaje'];
                    $this->servicioGeneral->insertarHistorialSolicitudError($arrayPeticiones);
                    
                    return $arrayFinal;
                }
                                                                    
                //Capacidades totales de los servicios activos ligados a un puerto
                $arrayCapacidades = $this->emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")
                                                            ->getResultadoCapacidadesPorInterface($objInterfaceElemento->getId());                
                //mac
                
                if($arrayPeticiones['strTipoOrden'] == 'C')
                {
                    $arrayParametrosMac = array(
                                            'intIdServicio'     => $arrayPeticiones['idServicio'],
                                            'boolEsPseudoPe'    => false,
                                            'strTipoOrden'      => $arrayPeticiones['strTipoOrden'],
                                            'intIdTipoMedio'    => $objServicioTecnico->getUltimaMillaId()
                                            );
                    $macServicio = $this->servicioGeneral->getMacPorCambioTipoMedio($arrayParametrosMac); 
                }
                else
                {                    
                    $macServicio = $this->servicioGeneral->getMacPorServicio($arrayPeticiones['idServicio']); 
                }
                
                if(!$macServicio)
                {
                    $strMensajeError = "";
                    if($arrayPeticiones['strTipoOrden'] == 'C')
                    {
                        $strMensajeError = "No se pudo obtener la MAC por problemas en los enlaces del servicio origen, favor notificar a Sistemas";
                    }
                    else
                    {
                        $strMensajeError = "No se encuentra la MAC asociada al Servicio";
                    }

                    $arrayFinal[] = array('status'     =>"ERROR", 
                                          'mensaje'    => $strMensajeError,
                                          'statusCode' => 500);
                    
                    $arrayPeticiones['objDetalleSolicitud'] = $objDetalleSolicitud;
                    $arrayPeticiones['mensajeError']        = $arrayFinal[0]['mensaje'];
                    $this->servicioGeneral->insertarHistorialSolicitudError($arrayPeticiones);
                    
                    return $arrayFinal;
                }              
                
                //Vlan del servicio
                $strVlan = $this->servicioGeneral->obtenerVlanServicio($objServicio);
                
                if(!$strVlan)
                {
                    $arrayFinal[] = array('status'     =>"ERROR", 
                                          'mensaje'    =>'No se encontro caracteristica de Vlan asociada al Servicio',
                                          'statusCode' => 500);
                    
                    $arrayPeticiones['objDetalleSolicitud'] = $objDetalleSolicitud;
                    $arrayPeticiones['mensajeError']        = $arrayFinal[0]['mensaje'];
                    $this->servicioGeneral->insertarHistorialSolicitudError($arrayPeticiones);                                        
                    
                    return $arrayFinal;
                }
                
                $arrayMacServicio[] = $macServicio;
                $arrayMacVlan = array($strVlan=>$arrayMacServicio);
                
                //consultar el puerto del switch del servicio para saber si
                //tiene mas servicios asociados ese puerto
                $strCancel = 'NO';
                
                $arrayParamDetConfigSW = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->getOne('PARAM_CANCELACION_CONFIGSW',
                                                            'TECNICO',
                                                            '',
                                                            '',
                                                            '',
                                                            '',
                                                            '',
                                                            '',
                                                            '',
                                                            $arrayPeticiones['idEmpresa']);
                
                if(isset($arrayParamDetConfigSW['valor1']) && !empty($arrayParamDetConfigSW['valor1']))
                {
                    $strCancel = $arrayParamDetConfigSW['valor1'];
                }
                                
                if ($strCancel == 'SI')
                {
                    //llamamos a la funcion para validar el puerto del servicio
                    $arrayParametrosCancel = array('objServicio'    => $objServicio);
                    $boolFlagCancel = $this->validarPuertoPorServicio($arrayParametrosCancel);
                }
                
                $objAdmiTipoMedio = $this->emInfraestructura->getRepository("schemaBundle:AdmiTipoMedio")
                                                                ->find($objServicioTecnico->getUltimaMillaId());
                
                if(is_object($objAdmiTipoMedio))
                {
                    $strUltimaMilla = $objAdmiTipoMedio->getNombreTipoMedio();
                }

                $strDescripcion = '';

                if($strUltimaMilla == 'Fibra Optica')
                {
                    $strDescripcion = '_fib';
                }
                if($strUltimaMilla == 'Radio')
                {
                    $strDescripcion = '_rad';
                }
                if($strUltimaMilla == 'UTP')
                {
                    $strDescripcion = '_utp';
                }
                
                //se ejecuta la desconfiguracion del sw
                if($boolFlagCancel)
                {
                    //accion a ejecutar
                    $arrayPeticiones['url']          = 'configSW';
                    
                    if($objServicio->getdescripcionpresentafactura() == 'CANAL TELEFONIA')
                    {
                        $arrayPeticiones['servicio'] = 'NETVOICE-L3MPLS';      
                    }
                    else
                    {             
                        $arrayPeticiones['servicio'] = $objProducto->getNombreTecnico();
                    }
                    
                    $arrayPeticiones['accion']       = 'cancelar';  
                    $arrayPeticiones['id_servicio']  = $objServicio->getId();
                    $arrayPeticiones['nombreMetodo'] = 'InfoCancelarServicioService.cancelarServicioTn';
                    $arrayPeticiones['sw']           = $objElemento->getNombreElemento();
                    $arrayPeticiones['anillo']       = $objDetalleAnillo->getDetalleValor();
                    $arrayPeticiones['macVlan']      = $arrayMacVlan;
                    $arrayPeticiones['user_name']    = $arrayPeticiones['usrCreacion'];
                    $arrayPeticiones['user_ip']      = $arrayPeticiones['ipCreacion'];     
                    $arrayPeticiones['bw_up']        = intval($arrayCapacidades['totalCapacidad1']) - intval($arrayPeticiones['capacidadUno']);
                    $arrayPeticiones['bw_down']      = intval($arrayCapacidades['totalCapacidad2']) - intval($arrayPeticiones['capacidadDos']);
                    $arrayPeticiones['login_aux']    = $objServicio->getLoginAux();
                    $arrayPeticiones['descripcion']  = 'cce_'.$objServicio->getLoginAux().$strDescripcion;
                    $arrayPeticiones['pto']          = $objInterfaceElemento->getNombreInterfaceElemento();
                                    
                    //Ejecucion del metodo via WS para realizar la configuracion del SW
                    $arrayRespuesta = $this->networkingScripts->callNetworkingWebService($arrayPeticiones);

                    $status     = $arrayRespuesta['status'];
                    $mensaje    = $arrayRespuesta['mensaje'];
                    $statusCode = $arrayRespuesta['statusCode'];
                }
                                         
                //obtener cpe del servicio 
                $objElementoCpe = $this->getElementoCpeServicioTn($objServicioTecnico);
                
                //validar si otro servicio usa el mismo cpe
                if(is_object($objElementoCpe))
                {
                    $arrayParametrosCpe = array('objServicio'    => $objServicio,
                                                'objElementoCpe' => $objElementoCpe);
                    $flagCpe = $this->validarCpePorServicio($arrayParametrosCpe);
                }
                
                //se ejecuta la desconfiguracion del sw
                if($flagCpe && !$boolFlagCancel)
                {
                    //accion a ejecutar
                    $arrayPeticiones['url']          = 'configSW';
                    
                    if($objServicio->getdescripcionpresentafactura() == 'CANAL TELEFONIA')
                    {
                        $arrayPeticiones['servicio'] = 'NETVOICE-L3MPLS';      
                    }
                    else
                    {             
                        $arrayPeticiones['servicio'] = $objProducto->getNombreTecnico();
                    }
                    
                    $arrayPeticiones['accion']       = 'cancelar';                
                    $arrayPeticiones['id_servicio']  = $objServicio->getId();
                    $arrayPeticiones['nombreMetodo'] = 'InfoCancelarServicioService.cancelarServicioTn';
                    $arrayPeticiones['sw']           = $objElemento->getNombreElemento();
                    $arrayPeticiones['anillo']       = $objDetalleAnillo->getDetalleValor();
                    $arrayPeticiones['macVlan']      = $arrayMacVlan;
                    $arrayPeticiones['user_name']    = $arrayPeticiones['usrCreacion'];
                    $arrayPeticiones['user_ip']      = $arrayPeticiones['ipCreacion'];     
                    $arrayPeticiones['bw_up']        = intval($arrayCapacidades['totalCapacidad1']) - intval($arrayPeticiones['capacidadUno']);
                    $arrayPeticiones['bw_down']      = intval($arrayCapacidades['totalCapacidad2']) - intval($arrayPeticiones['capacidadDos']);
                    $arrayPeticiones['login_aux']    = $objServicio->getLoginAux();
                    $arrayPeticiones['descripcion']  = 'cce_'.$objServicio->getLoginAux().$strDescripcion;
                    $arrayPeticiones['pto']          = $objInterfaceElemento->getNombreInterfaceElemento();
                                        
                    //Ejecucion del metodo via WS para realizar la configuracion del SW
                    $arrayRespuesta = $this->networkingScripts->callNetworkingWebService($arrayPeticiones);

                    $status  = $arrayRespuesta['status'];
                    $mensaje = $arrayRespuesta['mensaje'];
                    $statusCode  = $arrayRespuesta['statusCode'];
                }
                else
                {
                    $arrayPeticionesBw              = array();
                    $arrayPeticionesBw['url']       = 'configBW';
                    $arrayPeticionesBw['accion']    = 'Activar';
                    $arrayPeticionesBw['nombreAccionBw'] = 'cancelar';
                    $arrayPeticionesBw['id_servicio'] = $objServicio->getId();
                    $arrayPeticionesBw['nombreMetodo'] = 'InfoCancelarServicioService.cancelarServicioTn';
                    $arrayPeticionesBw['sw']        = $objElemento->getNombreElemento();
                    $arrayPeticionesBw['pto']       = $objInterfaceElemento->getNombreInterfaceElemento();
                    $arrayPeticionesBw['anillo']    = $objDetalleAnillo->getDetalleValor();
                    $arrayPeticionesBw['bw_up']     = intval($arrayCapacidades['totalCapacidad1']) - intval($arrayPeticiones['capacidadUno']);
                    $arrayPeticionesBw['bw_down']   = intval($arrayCapacidades['totalCapacidad2']) - intval($arrayPeticiones['capacidadDos']);
                    $arrayPeticionesBw['servicio']  = $objServicio->getProductoId()->getNombreTecnico();
                    $arrayPeticionesBw['login_aux'] = $objServicio->getLoginAux();
                    $arrayPeticionesBw['user_name'] = $arrayPeticiones['usrCreacion'];
                    $arrayPeticionesBw['user_ip']   = $arrayPeticiones['ipCreacion'];

                    //Ejecucion del metodo via WS para realizar la configuracion del SW
                    $arrayRespuestaBw = $this->networkingScripts->callNetworkingWebService($arrayPeticionesBw);

                    if($arrayRespuestaBw['status'] != 'OK')
                    {
                        $this->serviceUtil->lanzarExcepcion('NETWORKING', $arrayRespuestaBw['mensaje']);
                    }
                    else
                    {
                        $status     = "OK";
                        $mensaje    = "OK";
                        $statusCode = 200;
                    }
                }
      
                if($status == "OK")
                {     
                    //obtener flag para ver si existen servicios con los mismos datos tecnicos
                    $arrayParametrosUm = array( 'objServicio'       => $objServicio,
                                                'objServicioTecnico'=> $objServicioTecnico);
                    $flagUm = $this->validarUmPorServicio($arrayParametrosUm);
                   
                    $arrayParametros = array();
                    
                    $arrayParametros['objServicio']                     = $objServicio;
                    $arrayParametros['idPersonaEmpresaRol']             = $arrayPeticiones['idPersonaEmpresaRol'];
                    $arrayParametros['usrCreacion']                     = $arrayPeticiones['usrCreacion'];
                    $arrayParametros['ipCreacion']                      = $arrayPeticiones['ipCreacion'];
                    $arrayParametros['objServicioTecnico']              = $objServicioTecnico;
                    $arrayParametros['interfaceEstado']                 = "not connected";
                    $arrayParametros['interfaceElementoConectorId']     = $objServicioTecnico->getInterfaceElementoConectorId();
                    $arrayParametros['objMotivo']                       = $objMotivo;
                    $arrayParametros['objAccion']                       = $objAccion;
                    $arrayParametros['flagCpe']                         = $flagCpe;
                    $arrayParametros['flagUm']                          = $flagUm;
                    $arrayParametros['intIdElemento']                   = $intIdElementoCPE;
                    $arrayParametros['strTipoOrden']                    = $arrayPeticiones['strTipoOrden'];

                    //Se realiza validacion para que solo ejecute recalculo de BW para Servicios con tipo de enlace PRINCIPAL
                    if($objServicioTecnico->getTipoEnlace() == 'PRINCIPAL' && $objServicio->getEstado() == 'Activo')
                    {
                        //bajar bw del concentrador
                        $arrayParametrosBw = array( 
                                                    "objServicio"       => $objServicio,
                                                    "nombreAccionBw"    => 'cancelar',
                                                    "usrCreacion"       => $arrayPeticiones['usrCreacion'],
                                                    "ipCreacion"        => $arrayPeticiones['ipCreacion'],
                                                    "capacidadUnoNueva" => intval($arrayPeticiones['capacidadUno']),
                                                    "capacidadDosNueva" => intval($arrayPeticiones['capacidadDos']),
                                                    "operacion"         => "-",
                                                    "accion"            => "Se actualiza Capacidades por Cancelación de "
                                                                           . "servicio : <b>".$objServicio->getLoginAux()."<b>"
                                                   );

                        //Se actualiza las capacidades del Concentrador
                        $this->servicioGeneral->actualizarCapacidadesEnConcentrador($arrayParametrosBw);        
                    }
                    
                    $objSolCaracVrf = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "VRF", $objServicio->getProductoId());
                    if(is_object($objSolCaracVrf))
                    {
                        $intIdServicioProdCaract = $objSolCaracVrf->getValor();

                        if(!empty($intIdServicioProdCaract))
                        {
                            $objServicioPordCaract = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                                       ->find($intIdServicioProdCaract);

                            if(is_object($objServicioPordCaract))
                            {
                               $strVrf =  $objServicioPordCaract->getValor();
                            }
                        }
                    }


                    
                    if($objProducto->getNombreTecnico()=="L3MPLS")
                    {
                        //desconfigurar del pe [objProducto, objServicio, objElemento]
                        $arrayParametrosPe = array  (
                                                        'objProducto'   => $objProducto,
                                                        'objServicio'   => $objServicio,
                                                        'objElemento'   => $objElemento,
                                                        'usrCreacion'   => $arrayPeticiones['usrCreacion'],
                                                        'ipCreacion'    => $arrayPeticiones['ipCreacion']
                                                    );
                        $arrayRespuestaPe = $this->desconfigurarServicioPe($arrayParametrosPe);
                        $statusCode       = $arrayRespuestaPe['statusCode'];

                        if($arrayRespuestaPe['status'] != "OK")
                        {
                            $this->serviceUtil->lanzarExcepcion('NETWORKING',$arrayRespuestaPe['mensaje']);
                        }
                        
                    }
                    else if($objProducto->getNombreTecnico() === "INTMPLS" || $objProducto->getNombreTecnico() === "INTERNET SDWAN")
                    {
                        $arrayParametrosResultado = $this->emGeneral->getRepository("schemaBundle:AdmiParametroDet")
                                                                    ->getOne('VRF-INTERNET',
                                                                             'TECNICO',
                                                                             '',
                                                                             'VRF-INTERNET',
                                                                             '','','','','',
                                                                             $arrayPeticiones["idEmpresa"],
                                                                             null
                                                                             );
                    
                        if(isset($arrayParametrosResultado['valor1']) && !empty($arrayParametrosResultado['valor1']))
                        {
                            $strVrf = $arrayParametrosResultado['valor1'];
                        }
                        else
                        {
                            $strVrf = 'telconet';
                        }
                    }

                    if($objServicioTecnico->getElementoClienteId() && ($flagCpe || $arrayPeticiones['strTipoOrden'] == 'C'))
                    {
                        $arrayParametros['strOrigen'] = $arrayPeticiones['strOrigen'];
                        //generar la solicitud de retiro de equipo
                        $this->generarSolicitudRetiroEquipo($arrayParametros);
                        $this->eliminarElementoCliente($arrayParametros);
                    }
                    //liberar datos de backbone
                    $this->generarCancelacionDatosBackboneConexion($arrayParametros);
                    $arrayParametros["objProducto"] = $objProducto;
                    $arrayParametros["strVrf"]      = $strVrf;
                    $arrayRespuestaEliminarRutas    = $this->eliminarRutasTn($arrayParametros);
                    if($arrayRespuestaEliminarRutas["status"] !== "OK")
                    {
                        throw new \Exception($arrayRespuestaEliminarRutas["mensaje"]);
                    }

                    //seteo el flag de la propiedad del equipo
                    $booleanFlagPropiedad = false;
                    if(is_object($objElementoCpe))
                    {
                        //verifico a que pertenece el equipo
                        $objPropiedadElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                    ->findOneBy(array("elementoId"          => $objElementoCpe->getId(),
                                                                                      "detalleNombre"       => "PROPIEDAD",
                                                                                      "detalleDescripcion"  => "ELEMENTO PROPIEDAD DE",
                                                                                      "detalleValor"        => "TELCONET",
                                                                                      "estado"              => "Activo"));
                        if(is_object($objPropiedadElemento))
                        {
                            $booleanFlagPropiedad = true;
                        }
                    }
                    //CREAR SOLICITUD RPA CANCELACION LICENCIA
                    $arrayParamDetMarcasRpa = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->get('RPA_MARCA_ELEMENTOS_CANCELACION_LICENCIA',
                                                            'TECNICO',
                                                            '',
                                                            '',
                                                            $objServicio->getProductoId()->getId(),
                                                            '',
                                                            '',
                                                            '',
                                                            '',
                                                            $arrayPeticiones['idEmpresa']);
                    if(is_object($objElementoCpe) && is_array($arrayParamDetMarcasRpa) && !empty($arrayParamDetMarcasRpa)
                        && $flagCpe && $booleanFlagPropiedad)
                    {
                        //obtener el id de la marca del elemento
                        $intIdMarcaElemento     = $objElementoCpe->getModeloElementoId()->getMarcaElementoId()->getId();
                        //seteo el arreglo de los id de las marcas
                        $arrayIdMarcasLicencia  = array();
                        foreach($arrayParamDetMarcasRpa as $arrayDetParametroIdRpa)
                        {
                            $arrayIdMarcasLicencia[] = $arrayDetParametroIdRpa['valor2'];
                        }
                        //verifico si la marca requiere licenciamiento
                        if(in_array($intIdMarcaElemento, $arrayIdMarcasLicencia))
                        {
                            //obtengo el tipo de solicitud de rpa cancelación licencia
                            $objTipoSolicitudRpa = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                            ->findOneBy(array("descripcionSolicitud" => "SOLICITUD RPA CANCELACION LICENCIA",
                                                                              "estado"               => "Activo"));
                            if(is_object($objTipoSolicitudRpa))
                            {
                                //ingreso la solicitud
                                $objDetalleSolicitudRpa = new InfoDetalleSolicitud();
                                $objDetalleSolicitudRpa->setServicioId($objServicio);
                                $objDetalleSolicitudRpa->setTipoSolicitudId($objTipoSolicitudRpa);
                                $objDetalleSolicitudRpa->setEstado("Pendiente");
                                $objDetalleSolicitudRpa->setObservacion("Se crea la solicitud de RPA Verificación Licencia.");
                                $objDetalleSolicitudRpa->setUsrCreacion($arrayPeticiones['usrCreacion']);
                                $objDetalleSolicitudRpa->setFeCreacion(new \DateTime('now'));
                                $this->emComercial->persist($objDetalleSolicitudRpa);
                                $this->emComercial->flush();
                                //crear historial para la solicitud
                                if(is_object($objDetalleSolicitudRpa))
                                {
                                    $objHistorialSolicitudRpa = new InfoDetalleSolHist();
                                    $objHistorialSolicitudRpa->setDetalleSolicitudId($objDetalleSolicitudRpa);
                                    $objHistorialSolicitudRpa->setEstado("Pendiente");
                                    $objHistorialSolicitudRpa->setObservacion("Se crea la solicitud de RPA Verificación Licencia.");
                                    $objHistorialSolicitudRpa->setUsrCreacion($arrayPeticiones['usrCreacion']);
                                    $objHistorialSolicitudRpa->setFeCreacion(new \DateTime('now'));
                                    $objHistorialSolicitudRpa->setIpCreacion($arrayPeticiones['ipCreacion']);
                                    $this->emComercial->persist($objHistorialSolicitudRpa);
                                    $this->emComercial->flush();
                                }
                            }
                        }
                    }
                }
                else
                {
                    $arrayFinal[] = array('status'     => $status, 
                                        'mensaje'    => "Error: ".$mensaje,
                                        'statusCode' => $statusCode    
                                        );
                }                  
                
                if($objServicioTecnico->getTipoEnlace() == 'PRINCIPAL')
                {
                    //Cancelar Servicio Backup enlazado al principal en caso de existir
                    $arrayParametrosBck                            = array();
                    $arrayParametrosBck['objServicio']             =   $objServicio;
                    $arrayParametrosBck['intCapacidadUno']         =   intval($arrayCapacidades['totalCapacidad1']) - 
                                                                       intval($arrayPeticiones['capacidadUno']);
                    $arrayParametrosBck['intCapacidadDos']         =   intval($arrayCapacidades['totalCapacidad2']) - 
                                                                       intval($arrayPeticiones['capacidadDos']);
                    $arrayParametrosBck['intIdPersonaEmpresaRol']  =   $arrayPeticiones['idPersonaEmpresaRol'];
                    $arrayParametrosBck['strUsrCreacion']          =   $arrayPeticiones['usrCreacion'];
                    $arrayParametrosBck['strIpCreacion']           =   $arrayPeticiones['ipCreacion'];
                    $arrayParametrosBck['strCodEmpresa']           =   $arrayPeticiones['idEmpresa'];
                    $arrayParametrosBck['objMotivo']               =   $objMotivo;
                    $arrayParametrosBck['objAccion']               =   $objAccion;
                    if($arrayPeticiones['strTipoOrden'] != 'C')
                    {
                        $arrayRespuestaBck   = $this->cancelarServicioBackup($arrayParametrosBck);

                        if($arrayRespuestaBck['strStatus'] != 'OK')
                        {
                            $arrayFinal[] = array('status'     => $arrayRespuestaBck['strStatus'],
                                                  'mensaje'    => $arrayRespuestaBck['strMensaje'],
                                                  'statusCode' => 500
                                                 );
                            return $arrayFinal;
                        }
                    }
                }
            }//if($flagEsEnlace)
            else
            {
                $status     = "OK";
                $mensaje    = "OK";
                $statusCode = 200;
                
                $arrayParametros = array();
                    
                $arrayParametros['objServicio']                     = $objServicio;
                $arrayParametros['usrCreacion']                     = $arrayPeticiones['usrCreacion'];
                $arrayParametros['ipCreacion']                      = $arrayPeticiones['ipCreacion'];
                $arrayParametros['objMotivo']                       = $objMotivo;
                $arrayParametros['objAccion']                       = $objAccion;
            }

            if($status == "OK")
            {
                //se procede a eliminar todas las caracteristicas SERVICIO_MISMA_ULTIMA_MILLA que dependan de este servicio
                $this->servicioGeneral->eliminarDependenciaMismaUM($objServicio, 
                                                                   $arrayPeticiones['usrCreacion'],
                                                                   $arrayPeticiones['ipCreacion']);
                
                //eliminar datos de servicio, punto, cliente
                $arrayParametros['strOrigen']      = $arrayPeticiones['strOrigen'];
                $arrayParametros['strObservacion'] = $arrayPeticiones['strObservacion'];
                $this->generarCancelacionDatosComerciales($arrayParametros);
                
                //Si el tipo de orden es C deberemos liberar el enlace del cpe.
                if($arrayPeticiones['strTipoOrden'] == 'C')
                {
                    if($arrayPeticiones['objNuevoServicio']->getTipoEnlace() == 'PRINCIPAL')
                    {
                        $entityCaracteristica = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                  ->findOneBy(array('descripcionCaracteristica' => 'ES_BACKUP', 
                                                                                    'tipo' => 'COMERCIAL'));
                        if(is_object($entityCaracteristica))
                        {
                            $objProdCaractBackup = $this->emComercial
                                                        ->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                        ->findOneBy(array(
                                                                          "productoId"       => $objServicio->getProductoId(),
                                                                          "caracteristicaId" => $entityCaracteristica->getId(),
                                                                          "estado"           => "Activo"
                                                                     ));
                            if(is_object($objProdCaractBackup))
                            {
                                $entityInfoServicioProdCaract = $this->emComercial
                                                                     ->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                     ->findOneBy(array(
                                                                                    'productoCaracterisiticaId' => $objProdCaractBackup->
                                                                                                                    getId(),
                                                                                    'valor'                     => $objServicio->getId(),
                                                                                    'estado'                    => "Activo"));
                                if(is_object($entityInfoServicioProdCaract))
                                {
                                    $entityInfoServicioProdCaract->setValor($arrayPeticiones['objNuevoServicio']->getServicioId()->getId());
                                    $this->emComercial->persist($entityInfoServicioProdCaract);
                                    $this->emComercial->flush();
                                }
                            }
                        }
                    }
                    $objDetalleInterfaz = $this->emInfraestructura
                                               ->getRepository('schemaBundle:InfoDetalleInterface')
                                               ->findOneBy(array("detalleValor" => $arrayPeticiones['idServicio']));
                    /* eliminar enlaces anteriores que tengan como inicio la interface del cpe 
                        del servicio a cancelar*/
                    if(is_object($objDetalleInterfaz))
                    {
                        $arrayEnlacesAnteriores  = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                        ->findBy(array(
                                                                        "interfaceElementoFinId" => $objDetalleInterfaz->getInterfaceElementoId(),
                                                                        "tipoMedioId"            => $objServicioTecnico->getUltimaMillaId(),
                                                                        "estado"                 => "Activo"));

                        foreach( $arrayEnlacesAnteriores as $objEnlace )
                        {
                            $objEnlace->setEstado("Eliminado");
                            $this->emInfraestructura->persist($objEnlace);
                            $this->emInfraestructura->flush();
                        }
                    }

                }

                $arrayFinal[] = array('status'  => $status, 'mensaje' => "OK" , 'statusCode' => $statusCode);
            }
            
            //Existe solicitud EnProceso de CambioPlan para el servicio ( Proceso Masivo )
            if($objDetalleSolicitud)
            {
                if($status == "OK")
                {
                    //Finalizar la solicitud de cambio de plan                        
                    $objDetalleSolicitud->setEstado("Finalizada");                                                                                                                   
                }
                else
                {
                    //Cuando existe un fallo de algun tipo se pone en estado Fallo a la solicitud                          
                    $objDetalleSolicitud->setEstado("Fallo");                        
                }

                $this->emComercial->persist($objDetalleSolicitud);
                $this->emComercial->flush();   

                $this->servicioGeneral->finalizarSolicitudPadrePorProcesoMasivo($arrayPeticiones);

                //Se crea Historial de Servicio
                $objDetalleSolsHist = new InfoDetalleSolHist();
                $objDetalleSolsHist->setDetalleSolicitudId($objDetalleSolicitud);
                $objDetalleSolsHist->setEstado($objDetalleSolicitud->getEstado());
                $objDetalleSolsHist->setFeCreacion(new \DateTime('now'));
                $objDetalleSolsHist->setUsrCreacion($arrayPeticiones['usrCreacion']);
                $objDetalleSolsHist->setIpCreacion($arrayPeticiones['ipCreacion']);
                $objDetalleSolsHist->setObservacion($status == "OK"?"Se Realizo Cancelacion exitosamente":$mensaje);
                $this->emComercial->persist($objDetalleSolsHist);
                $this->emComercial->flush();
                
                //Enviar Notificacion
                $this->servicioGeneral->enviarNotificacionFinalizadoSolicitudMasiva($arrayParametrosCorreo);
                
                //se agrega generación de tarea interna para servicios que no son enlaces
                if($status == "OK" && !$flagEsEnlace)
                {
                    $objUsuario = $this->emComercial
                                       ->getRepository('schemaBundle:InfoPersona')
                                       ->findOneBy(array('login'  => $arrayPeticiones['usrCreacion'],
                                                         'estado' => 'Activo'));

                    if(is_object($objUsuario))
                    {
                        $strNombreEmpleado = $objUsuario->getNombres()." ".$objUsuario->getApellidos();
                    }
               
                    $intIdPunto                = $objServicio->getPuntoId()->getId();
                    $objInfoPuntoDatoAdicional = $this->emComercial
                                                      ->getRepository('schemaBundle:InfoPuntoDatoAdicional')
                                                      ->findOneByPuntoId($intIdPunto);
                    if(is_object($objInfoPuntoDatoAdicional))
                    {
                        $objSector = $objInfoPuntoDatoAdicional->getSectorId();
                        if (is_object($objSector))
                        {
                            $objParroquia = $objSector->getParroquiaId();
                            if (is_object($objParroquia))
                            {
                                $objCanton = $objParroquia->getCantonId();
                                if(is_object($objCanton))
                                {
                                    $strCiudadCliente = $objCanton->getNombreCanton();
                                    $strRegion        = $objCanton->getRegion();
                                }
                            }
                        }
                    }
                    
                    $objPuntoCliente = $objServicio->getPuntoId();
                    if (is_object($objPuntoCliente))
                    {
                        $objPersonaEmpresaRolCliente = $objPuntoCliente->getPersonaEmpresaRolId();
                        if (is_object($objPersonaEmpresaRolCliente))
                        {
                            $objPersonaCliente = $objPersonaEmpresaRolCliente->getPersonaId();
                            if (is_object($objPersonaCliente))
                            {
                                $strNombreCliente = ($objPersonaCliente->getRazonSocial() ?
                                                     $objPersonaCliente->getRazonSocial() : 
                                                     (($objPersonaCliente->getNombres() || $objPersonaCliente->getApellidos()) ?
                                                     $objPersonaCliente->getNombres()." ".$objPersonaCliente->getApellidos():"") 
                                                    );
                            }
                        }
                    }
                    
                    $arrayParametros = array('strIdEmpresa'          => '10',
                                             'strPrefijoEmpresa'     => 'TN',
                                             'strNombreTarea'        => 'Cancelacion',
                                             'strObservacion'        => 'Se cancelo el servicio : '.
                                                                        $objServicio->getDescripcionPresentaFactura().'.',
                                             'strNombreDepartamento' => 'IPCCL2',
                                             'strCiudad'             => $strCiudadCliente,
                                             'strEmpleado'           => $strNombreEmpleado,
                                             'strUsrCreacion'        => $arrayPeticiones['usrCreacion'],
                                             'strIp'                 => $arrayPeticiones['ipCreacion'],
                                             'strOrigen'             => 'WEB-TN',
                                             'strLogin'              => $objServicio->getPuntoId()->getLogin(),
                                             'intPuntoId'            => $intIdPunto,
                                             'strNombreCliente'      => $strNombreCliente,
                                             'strRegion'             => $strRegion
                                            );

                   $this->serviceSoporte->ingresarTareaInterna($arrayParametros);
                }
            }
        }
        catch (\Exception $e) 
        {            
            if ($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->rollback();
            }
            
            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }
            
            if ($this->emSoporte->getConnection()->isTransactionActive())
            {
                $this->emSoporte->getConnection()->rollback();
            }    
            
            if($objDetalleSolicitud)
            {
                $objDetalleSolicitud->setEstado("Fallo");
                $this->emComercial->persist($objDetalleSolicitud);
                $this->emComercial->flush();   

                $this->servicioGeneral->finalizarSolicitudPadrePorProcesoMasivo($arrayPeticiones);

                //Se crea Historial de Servicio
                $objDetalleSolsHist = new InfoDetalleSolHist();
                $objDetalleSolsHist->setDetalleSolicitudId($objDetalleSolicitud);
                $objDetalleSolsHist->setEstado($objDetalleSolicitud->getEstado());
                $objDetalleSolsHist->setFeCreacion(new \DateTime('now'));
                $objDetalleSolsHist->setUsrCreacion($arrayPeticiones['usrCreacion']);
                $objDetalleSolsHist->setIpCreacion($arrayPeticiones['ipCreacion']);
                $objDetalleSolsHist->setObservacion($e->getMessage());
                $this->emComercial->persist($objDetalleSolsHist);
                $this->emComercial->flush();
            }
            
            $this->serviceUtil->insertError('Telcos+', 
                                            'InfoCancelarServicioService->cancelarServicioTn', 
                                            $e->getMessage(),
                                            $arrayPeticiones['usrCreacion'], 
                                            $arrayPeticiones['ipCreacion']
                                           );
            
            $mensaje = $this->serviceUtil->getMensajeException($e);
                    
            $arrayFinal[]   = array('status' => "ERROR", 'mensaje' => $mensaje, 'statusCode' => 500);
            return $arrayFinal;
        }
        //*---------------------------------------------------------------------*/
        
        //*DECLARACION DE COMMITS*/
        if ($this->emInfraestructura->getConnection()->isTransactionActive())
        {
            $this->emInfraestructura->getConnection()->commit();
        }

        if ($this->emComercial->getConnection()->isTransactionActive())
        {
            $this->emComercial->getConnection()->commit();
        }
        
        if ($this->emSoporte->getConnection()->isTransactionActive())
        {
            $this->emSoporte->getConnection()->commit();
        }
        
        $this->emInfraestructura->getConnection()->close();
        $this->emComercial->getConnection()->close();
        $this->emSoporte->getConnection()->close();
        //*----------------------------------------------------------------------*/
        
        $strStatus = $arrayFinal[0]['status'];
        if ($strStatus == "OK")
        {
            //Consultamos si el servicio tiene relacionado servicios como FastCloud
            $arrayServiciosRelacion = $this->getServiciosRelacion($intIdServicio);
            foreach ($arrayServiciosRelacion as $arrayServiciosRel)
            {
                $arrayPeticionesServiciosRel = array();
                $arrayPeticionesServiciosRel['idServicio']           = $arrayServiciosRel;
                $arrayPeticionesServiciosRel['idEmpresa']            = $arrayPeticiones['idEmpresa'];
                $arrayPeticionesServiciosRel['idAccion']             = $intIdAccion;
                $arrayPeticionesServiciosRel['idMotivo']             = $intIdMotivo;
                $arrayPeticionesServiciosRel['usrCreacion']          = $arrayPeticiones['usrCreacion'];
                $arrayPeticionesServiciosRel['clientIp']             = $arrayPeticiones['ipCreacion'];
                $arrayPeticionesServiciosRel['strPrefijoEmpresa']    = $arrayPeticiones['prefijoEmpresa'];
                
                $arrayRespuestaSer = $this->cancelarServiciosOtros($arrayPeticionesServiciosRel);
                $strStatus      = $arrayRespuestaSer['status'];
            }
            
            //Consultamos si el servicio tiene relacionado servicios de instalación simultánea
            $arraySimultanea = $this->getServiciosInstalacionSimultaneaRequiereFlujo($intIdServicio);
            foreach ($arraySimultanea as $item)
            {
                $arrayPeticionesSim = array();
                $arrayPeticionesSim['idServicio']           = $item;
                $arrayPeticionesSim['idEmpresa']            = $arrayPeticiones['idEmpresa'];
                $arrayPeticionesSim['idAccion']             = $intIdAccion;
                $arrayPeticionesSim['idMotivo']             = $intIdMotivo;
                $arrayPeticionesSim['usrCreacion']          = $arrayPeticiones['usrCreacion'];
                $arrayPeticionesSim['clientIp']             = $arrayPeticiones['ipCreacion'];
                $arrayPeticionesSim['strPrefijoEmpresa']    = $arrayPeticiones['prefijoEmpresa'];
                
                $arrayRespuestaSer = $this->cancelarServiciosOtros($arrayPeticionesSim);
                $strStatus      = $arrayRespuestaSer['status'];
            }
                                    
            $strPermiteProcesarMonitoreo = "NO";
            if(is_object($objProducto))
            {
                $strNombreTecnico              = $objProducto->getNombreTecnico();
                $arrayNombresTecnicoPermitidos = array("INTERNET", "L3MPLS", "INTMPLS", "INTERNET SDWAN");
                if (in_array($strNombreTecnico, $arrayNombresTecnicoPermitidos))
                {
                    $arrayParametrosCaractHostPortal = array('descripcionCaracteristica' => 'HOST_LOGIN_AUX',
                                                             'estado'                    => "Activo");

                    $objCaractHostPortal = $this->emComercial
                                                ->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneBy($arrayParametrosCaractHostPortal);
                    if(is_object($objCaractHostPortal))
                    {
                        $objPerCaracUrlHost = $this->emComercial
                                                   ->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                   ->findCaracteristicaPorCriterios(
                                                                                    array("caracteristicaId"    => $objCaractHostPortal->getId(),
                                                                                          "personaEmpresaRolId" => $objServicio
                                                                                                                   ->getPuntoId()
                                                                                                                   ->getPersonaEmpresaRolId()
                                                                                                                   ->getId(),
                                                                                          "empresaCod"          => '10',
                                                                                          "valor"               => strtoupper
                                                                                                                   ($objServicio->getLoginAux()),
                                                                                          "estado"              => "Activo")
                                                                                   );
                        if (is_object($objPerCaracUrlHost))
                        {
                            $strPermiteProcesarMonitoreo = "SI";
                        }
                    }
                }
            }
            if ($strPermiteProcesarMonitoreo == "SI")
            {
                //Generar creación de nuevo host para monitoreo de equipos en app TelcoGraph
                $arrayParametrosTelcoGraph                    = array();
                $arrayParametrosTelcoGraph['objInfoServicio'] = $objServicio;
                $arrayParametrosTelcoGraph['strUsrCreacion']  = $arrayParametros['usrCreacion'];
                $arrayParametrosTelcoGraph['strIpCreacion']   = $arrayParametros['ipCreacion'];
                $arrayParametrosTelcoGraph['strProceso']      = "cancelar";
                $this->servicioGeneral->procesaHostTelcoGraph($arrayParametrosTelcoGraph);
            }
            
            //Generar monitoreo del Zabbix
            if(is_object($objProducto))
            {
                $strNombreTecnico              = $objProducto->getNombreTecnico();
                $arrayNombresTecnicoPermitidos = array("INTERNET", "L3MPLS", "INTMPLS", "L3MPLS SDWAN", "INTERNET SDWAN");
                if (in_array($strNombreTecnico, $arrayNombresTecnicoPermitidos))
                {
                    $arrayParametrosZabbix                    = array();
                    $arrayParametrosZabbix['objInfoServicio'] = $objServicio;
                    $arrayParametrosZabbix['strUsrCreacion']  = $arrayParametros['usrCreacion'];
                    $arrayParametrosZabbix['strIpCreacion']   = $arrayParametros['ipCreacion'];
                    $arrayParametrosZabbix['strProceso']      = "cancelar";
                    $this->servicioGeneral->enviarInfoClienteZabbix($arrayParametrosZabbix);
                }
            }
        }
        
        //------------------------------------------INICIO: ACTUALIZACIÓN DEL ESTADO EN ARCGIS - TN
        if(is_object($objProducto))
        {
            $arrayParametrosConsulta                       = array();
            $arrayParametrosConsulta['strUsrCreacion']     = $arrayPeticiones['usrCreacion'];
            $arrayParametrosConsulta['strIpCreacion']      = $arrayPeticiones['ipCreacion'];
            $arrayParametrosConsulta['objServicioTecnico'] = $objServicioTecnico;
            $arrayParametrosConsulta['objProducto']        = $objProducto;
            // Pregunta si coincide con un producto parametrizado
            $arrayRespuestaProductoCancelacion              = $this->validarCondicionesProductos($arrayParametrosConsulta);
            if(($status == "OK") && ($arrayRespuestaProductoCancelacion['status']=='OK'))
            {
                // Solo si coincide ingresa a preguntar por los estados  de condiciòn
                $arrayRespuestaEstados                      = $this->validarCondicionesEstados($arrayParametrosConsulta);

                if( (($strUltimaMilla == 'Fibra Optica')||($strUltimaMilla == 'FTTx')||($strUltimaMilla == 'FO'))
                    && ($arrayRespuestaEstados['status']=='OK'))
                {
                    //solo para servicios que tienen fibra o fttx como última milla
                    $strUsrCancelacion = $arrayPeticiones['usrCreacion'];
                    $strLoginPunto     = $objServicio->getPuntoId()->getLogin();
                    $intIdEmpresa      = $arrayPeticiones['idEmpresa'];
                    $objEmpresaGrupo   = $this->emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')->find($intIdEmpresa);
                    $strPrefijoEmpresa = is_object($objEmpresaGrupo) ? $objEmpresaGrupo->getPrefijo() : "";
                    $strIpCreacion     = $arrayPeticiones['ipCreacion'];

                    // PuertoSwitch y  NombreSwitch
                    $strNombreSwitch  = $objElemento->getNombreElemento();
                    $strPuertoSwitch  = $objInterfaceElemento->getNombreInterfaceElemento();

                    $arrayParamServicio   = array(
                                        "strUsrCancelacion" => $strUsrCancelacion,
                                        "strNombreSwitch"   => $strNombreSwitch,
                                        "strPuertoSwitch"   => $strPuertoSwitch,
                                        "strLoginPunto"     => $strLoginPunto,
                                        "strPrefijo"        => $strPrefijoEmpresa,
                                        "strIpCreacion"     => $strIpCreacion,
                                        "objServicioPunto"  => $objServicio
                                        );

                    //Se llama al procedimiento en la base
                    $this->inactivarUmARCGIS($arrayParamServicio);
                }
            }
        }
        //------------------------------------------FIN: ACTUALIZACIÓN DEL ESTADO EN ARCGIS - TN
        return $arrayFinal;
    }        
    
    /**
     * 
     * Metodo que se encarga de cancelar servicios que se encuentre determinados como PseudoPe
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0
     * @since 22-11-2016
     * 
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 06-06-2017 Se envian los parametros en un array a la función "getArrayInfoCambioPlanPorSolicitud"
     *
     * @param Array $arrayPeticiones [
     *                                  idMotivo            Motivo por el cual se cancela el servicio
     *                                  idAccion            Que accion se realiza para inyectar en el historial
     *                                  idServicio          Servicio a ser cancelado
     *                                  idSolicitudPadre    Solicitud en caso de existir ( proceso masivo )
     *                                  idPersonaEmpresaRol Persona rol del servicio a ser cancelado
     *                                  capacidadUno        Capacidad de subida del servicio
     *                                  capacidadDos        Capacidad de bajada del servicio
     *                                  usrCreacion         Usuario de creacion del requerimiento
     *                                  ipCreacion          Ip de donde proviene el requerimiento
     *                               ]
     * @return Array $arrayFinal [ status , mensaje , statusCode ]
     * @throws \Exception
     */
    public function cancelarServicioPseudoPe($arrayPeticiones)
    {        
        $flagCpe                = false;
        $flagUm                 = false;
        $flagEsEnlace           = false;
        $objDetalleSolicitud    = null;
        $strVlan                = null;
        $statusCode             = "200";
        $arrayParametrosCambP   = array();        
        $objMotivo              = $this->emGeneral->getRepository('schemaBundle:AdmiMotivo')->find($arrayPeticiones['idMotivo']);
        $objAccion              = $this->emSeguridad->getRepository('schemaBundle:SistAccion')->find($arrayPeticiones['idAccion']);
        $objServicio            = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($arrayPeticiones['idServicio']);
        $objProducto            = $objServicio->getProductoId();
        $objServicioTecnico     = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                            ->findOneBy(array("servicioId" => $arrayPeticiones['idServicio']));
        
        $this->serviceUtil->validaObjeto($objServicio,"No existe Información del Servicio a cancelar");
        $this->serviceUtil->validaObjeto($objProducto,"No existe Información del Producto ligado al Servicio a cancelar");
        $this->serviceUtil->validaObjeto($objMotivo,"No existe Información del Motivo a cancelar el Servicio");
        $this->serviceUtil->validaObjeto($objAccion,"No existe Información del Accion del Servicio a cancelar");
        
        if($objProducto->getEsEnlace() == "SI")
        {
            $flagEsEnlace = true;
        }
        
        $arrayParametrosCambP["idServicio"]  = $arrayPeticiones['idServicio'];
        $arrayParametrosCambP["tipoProceso"] = "";         
        
        //Verificar si no existe solicitud de Cancelacion
        $arrayResultado = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                            ->getArrayInfoCambioPlanPorSolicitud($arrayParametrosCambP);
        
        if($arrayResultado && count($arrayResultado)>0)
        {
            $objDetalleSolicitud = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                     ->find($arrayResultado['idSolicitud']);
            
            $arrayPeticiones['idSolicitudPadre'] = $arrayResultado['idSolicitudPadre'];
        }
        
        //Parametros para el Correo de Notificacion
        $arrayParametrosCorreo = array();
        $arrayParametrosCorreo['idSolicitudPadre'] = isset($arrayPeticiones['idSolicitudPadre'])?$arrayPeticiones['idSolicitudPadre']:null;
        $arrayParametrosCorreo['idServicio']       = $arrayPeticiones['idServicio'];
        $arrayParametrosCorreo['usrCreacion']      = $arrayPeticiones['usrCreacion'];
                
        //*DECLARACION DE TRANSACCIONES------------------------------------------*/
        
        $this->emComercial->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();
        //*----------------------------------------------------------------------*/

        //LOGICA DE NEGOCIO-----------------------------------------------------*/
        try
        {
            if($flagEsEnlace)
            {
                $this->serviceUtil->validaObjeto($objServicioTecnico,"No existe Información tecnica del Servicio a cancelar");
                
                $arrayParametrosPseudoPe      = array ('idServicio' => $arrayPeticiones['idServicio']);
                $arrayDatosTecnicosPseudoPe   = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                                  ->getDatosFactibilidadPseudoPe($arrayParametrosPseudoPe);
                if(isset($arrayDatosTecnicosPseudoPe['data']))
                {
                    $idElementoPadre         = $arrayDatosTecnicosPseudoPe['data']['idElemento'];                                        
                    $strNombreElementoPadre  = $arrayDatosTecnicosPseudoPe['data']['nombrePe'];                    
                }
                
                //vlan
                $objServProdCaractVlanPseudoPe   = $this->servicioGeneral
                                                        ->getServicioProductoCaracteristica($objServicio,'VLAN_PROVEEDOR',$objProducto);
                    
                if(is_object($objServProdCaractVlanPseudoPe))
                {
                    $strVlan = $objServProdCaractVlanPseudoPe->getValor();
                }
                                
                if(!$strVlan)
                {                                   
                    $arrayFinal[] = array('status'     =>"ERROR", 
                                          'mensaje'    =>'No se encontro caracteristica de Vlan asociada al Servicio',
                                          'statusCode' => 500);
                    
                    $arrayPeticiones['objDetalleSolicitud'] = $objDetalleSolicitud;
                    $arrayPeticiones['mensajeError']        = $arrayFinal[0]['mensaje'];
                    $this->servicioGeneral->insertarHistorialSolicitudError($arrayPeticiones);                                        
                    
                    return $arrayFinal;
                }
                                
                //obtener cpe del servicio
                if($objServicioTecnico->getElementoClienteId())
                {
                    $objElementoCpe = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                              ->find($objServicioTecnico->getElementoClienteId());
                }
                
                $this->serviceUtil->validaObjeto($objElementoCpe,"No CPE relacionado al Servicio a cancelar");
                                
                //Obtener flag para confirmar si el cpe del servicio tiene otro servicio vinculado
                $arrayParametrosCpe = array('objServicio'    => $objServicio,
                                            'objElementoCpe' => $objElementoCpe
                                           );

                $flagCpe = $this->validarCpePorServicio($arrayParametrosCpe);
                                
                //obtener flag para ver si existen servicios con los mismos datos tecnicos
                $arrayParametrosUm = array( 'objServicio'       => $objServicio,                                            
                                            'intIdElementoPadre'=> $idElementoPadre,
                                            'boolEsPseudoPe'    => true
                                          );
                
                $flagUm = $this->validarUmPorServicio($arrayParametrosUm);

                $arrayParametros = array();

                $arrayParametros['objServicio']                     = $objServicio;
                $arrayParametros['idPersonaEmpresaRol']             = $arrayPeticiones['idPersonaEmpresaRol'];
                $arrayParametros['usrCreacion']                     = $arrayPeticiones['usrCreacion'];
                $arrayParametros['ipCreacion']                      = $arrayPeticiones['ipCreacion'];
                $arrayParametros['objServicioTecnico']              = $objServicioTecnico;
                $arrayParametros['interfaceEstado']                 = null;
                $arrayParametros['interfaceElementoConectorId']     = null;
                $arrayParametros['objMotivo']                       = $objMotivo;
                $arrayParametros['objAccion']                       = $objAccion;
                $arrayParametros['flagCpe']                         = $flagCpe;
                $arrayParametros['flagUm']                          = $flagUm;

                //eliminar rutas del servicio
                $arrayRutas = $this->emInfraestructura->getRepository("schemaBundle:InfoRutaElemento")
                                                      ->findBy(array( "servicioId"    => $objServicio->getId(),
                                                                      "estado"        => "Activo"));
                foreach($arrayRutas as $objRuta)
                {
                    $objRuta->setEstado("Eliminado");
                    $this->emInfraestructura->persist($objRuta);
                    $this->emInfraestructura->flush();
                }

                //Se realiza validacion para que solo ejecute recalculo de BW para Servicios con tipo de enlace PRINCIPAL
                if($objServicioTecnico->getTipoEnlace() == 'PRINCIPAL')
                {
                    //bajar bw del concentrador
                    $arrayParametrosBw = array( 
                                                "objServicio"       => $objServicio,
                                                "usrCreacion"       => $arrayPeticiones['usrCreacion'],
                                                "ipCreacion"        => $arrayPeticiones['ipCreacion'],
                                                "capacidadUnoNueva" => intval($arrayPeticiones['capacidadUno']),
                                                "capacidadDosNueva" => intval($arrayPeticiones['capacidadDos']),
                                                "operacion"         => "-",
                                                "accion"            => "Se actualiza Capacidades por Cancelación de "
                                                                       . "servicio : <b>".$objServicio->getLoginAux()."<b>"
                                               );

                    //Se actualiza las capacidades del Concentrador
                    $this->servicioGeneral->actualizarCapacidadesEnConcentrador($arrayParametrosBw);        
                }

                if($objProducto->getNombreTecnico()=="L3MPLS")
                {
                    //desconfigurar del pe [objProducto, objServicio, objElemento]
                    $arrayParametrosPe = array  (
                                                    'objProducto'            => $objProducto,
                                                    'objServicio'            => $objServicio,
                                                    'objElemento'            => null, //No existe Switch de Telconet ligado a un PE ( PseudoPe )
                                                    'usrCreacion'            => $arrayPeticiones['usrCreacion'],
                                                    'ipCreacion'             => $arrayPeticiones['ipCreacion'],
                                                    'esPseudoPe'             => true,
                                                    'strNombreElementoPadre' => $strNombreElementoPadre,
                                                    'strVlan'                => $strVlan
                                                );                    
                    $arrayRespuestaPe = $this->desconfigurarServicioPe($arrayParametrosPe);
                    $statusCode       = $arrayRespuestaPe['statusCode'];

                    if($arrayRespuestaPe['status'] != "OK")
                    {
                        throw new \Exception($arrayRespuestaPe['mensaje']);
                    }

                }

                //Si ya no existen servicios ligados a un cpe generar solicitid de equipo
                if($flagCpe)
                {
                    //generar la solicitud de retiro de equipo
                    $this->generarSolicitudRetiroEquipo($arrayParametros);
                    $this->eliminarElementoCliente($arrayParametros);                    
                }

                //liberar datos de backbone
                $this->generarCancelacionDatosBackboneConexion($arrayParametros);
                
                $status     = "OK";
                                     
            }//if($flagEsEnlace)
            else
            {
                $status     = "OK";
                $mensaje    = "OK";
                $statusCode = 200;
                
                $arrayParametros = array();
                    
                $arrayParametros['objServicio']                     = $objServicio;
                $arrayParametros['usrCreacion']                     = $arrayPeticiones['usrCreacion'];
                $arrayParametros['ipCreacion']                      = $arrayPeticiones['ipCreacion'];
                $arrayParametros['objMotivo']                       = $objMotivo;
                $arrayParametros['objAccion']                       = $objAccion;
            }
            
            if($status == "OK")
            {
                //eliminar datos de servicio, punto, cliente
                $this->generarCancelacionDatosComerciales($arrayParametros);

                $arrayFinal[] = array('status'  => $status, 'mensaje' => "OK" , 'statusCode' => $statusCode);
            }
            
            //Existe solicitud EnProceso de CambioPlan para el servicio ( Proceso Masivo )
            if(is_object($objDetalleSolicitud))
            {
                if($status == "OK")
                {
                    //Finalizar la solicitud de cambio de plan                        
                    $objDetalleSolicitud->setEstado("Finalizada");                                                                                                                   
                }
                else
                {
                    //Cuando existe un fallo de algun tipo se pone en estado Fallo a la solicitud                          
                    $objDetalleSolicitud->setEstado("Fallo");                        
                }

                $this->emComercial->persist($objDetalleSolicitud);
                $this->emComercial->flush();   

                $this->servicioGeneral->finalizarSolicitudPadrePorProcesoMasivo($arrayPeticiones);

                //Se crea Historial de Servicio
                $objDetalleSolsHist = new InfoDetalleSolHist();
                $objDetalleSolsHist->setDetalleSolicitudId($objDetalleSolicitud);
                $objDetalleSolsHist->setEstado($objDetalleSolicitud->getEstado());
                $objDetalleSolsHist->setFeCreacion(new \DateTime('now'));
                $objDetalleSolsHist->setUsrCreacion($arrayPeticiones['usrCreacion']);
                $objDetalleSolsHist->setIpCreacion($arrayPeticiones['ipCreacion']);
                $objDetalleSolsHist->setObservacion($status == "OK"?"Se Realizo Cancelacion exitosamente":$mensaje);
                $this->emComercial->persist($objDetalleSolsHist);
                $this->emComercial->flush();
                
                //Enviar Notificacion
                $this->servicioGeneral->enviarNotificacionFinalizadoSolicitudMasiva($arrayParametrosCorreo);                
            }                                               
        }
        catch (\Exception $e) 
        {            
            if ($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->rollback();
            }
            
            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }                          
            
            if(is_object($objDetalleSolicitud))
            {
                $objDetalleSolicitud->setEstado("Fallo");
                $this->emComercial->persist($objDetalleSolicitud);
                $this->emComercial->flush();   

                $this->servicioGeneral->finalizarSolicitudPadrePorProcesoMasivo($arrayPeticiones);

                //Se crea Historial de Servicio
                $objDetalleSolsHist = new InfoDetalleSolHist();
                $objDetalleSolsHist->setDetalleSolicitudId($objDetalleSolicitud);
                $objDetalleSolsHist->setEstado($objDetalleSolicitud->getEstado());
                $objDetalleSolsHist->setFeCreacion(new \DateTime('now'));
                $objDetalleSolsHist->setUsrCreacion($arrayPeticiones['usrCreacion']);
                $objDetalleSolsHist->setIpCreacion($arrayPeticiones['ipCreacion']);
                $objDetalleSolsHist->setObservacion($e->getMessage());
                $this->emComercial->persist($objDetalleSolsHist);
                $this->emComercial->flush();
            }
            
            $status         = "ERROR";
            $mensaje        = "Error al Cancelar el Servicio, notificar a Sistemas";
            $arrayFinal[]   = array('status' => "ERROR", 'mensaje' => $mensaje, 'statusCode' => 500);
            return $arrayFinal;
        }
        //*---------------------------------------------------------------------*/
        
        //*DECLARACION DE COMMITS*/
        if ($this->emInfraestructura->getConnection()->isTransactionActive())
        {
            $this->emInfraestructura->getConnection()->commit();
        }

        if ($this->emComercial->getConnection()->isTransactionActive())
        {
            $this->emComercial->getConnection()->commit();
        }                
        
        $this->emInfraestructura->getConnection()->close();
        $this->emComercial->getConnection()->close();
        
        return $arrayFinal;
    }
    
    
    // ====================================================================================================
    // ====================================================================================================
    // FUNCIONALIDAD GENERAL PARA LA CANCELACION DE SERVICIOS
    // ====================================================================================================
    // ====================================================================================================
    /**
     * Funcion que sirve para desconfigurar al cliente en el PE
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 25-05-2016
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 22-11-2016 - Se adapta función para que soporte escenario de servicios pseudope
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 11-10-2017 - Se realizan ajustes al obtener el rd_id, ahora se consulta por la caracteristica del persona empresa rol
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.3 17-07-2018 - Se enviá nuevo parámetro al método getPeBySwitch
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.4  20-03-2019 - Se envia la clase_servicio: INTERNET-HSRP y DATOS-HSRP para las ordenes de servicio que tienen definido
     *                            el esquema PE-HSRP
     * @since 1.3
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.5 19-06-2019 - Se agregan parámetros de razon_social y route_target export e import en el método configPE, con el objetivo de
     *                           enviar a configurar una lineas adicionales que permitan al cliente el monitoreo sus enlaces de datos
     * @since 1.4
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.6 04-03-2020 - Se aumenta validación para poder cancelar correctamente cuando se trate de un L3MPLS correspondiente a NETVOICE
     * 
     * @author $arrayParametros [objProducto, objServicio, objElemento]
     */
    public function desconfigurarServicioPe($arrayParametros)
    {
        $objServicio = $arrayParametros['objServicio'];
        $objElemento = $arrayParametros['objElemento'];
        $objProducto = $arrayParametros['objProducto'];        
        $status      = "ERROR";
        $mensaje     = "ERROR";
        $statusCode  = 500;        
        $strVlan     = null;
        $strAnillo   = null;
        $strBanderaLineasBravco   = "N";
        $strRouteTargetExport     = "";
        $strRouteTargetImport     = "";
        $strRazonSocial           = "";
        $arrayParametrosWs        = array();
        $boolEsPseudoPe           = isset($arrayParametros['esPseudoPe'])?$arrayParametros['esPseudoPe']:false;
        $strBanderaServProdCaract = "N";
        $objServicioTecnico = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                ->findOneBy(array("servicioId" => $objServicio->getId()));
        
        $objIp = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                         ->findOneBy(array("servicioId"    => $objServicio->getId(),
                                                           "estado"        => "Activo"));
            
        //se buscan cuantas ips tiene la subred
        $arrayIpsSubred = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                            ->findBy(array( 'subredId'  => $objIp->getSubredId(),
                                                            'estado'    => 'Activo'));
        $numIpsSubred = count($arrayIpsSubred);

        //ROUTER - cancelar en el pe
        if($numIpsSubred>0 && $numIpsSubred<2)
        {
            //obtener datos de la subred
            $objSubredAnterior = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')
                                        ->find($objIp->getSubredId());
            if(!$boolEsPseudoPe)
            {
                //obtener el anillo del elemento 
                $objDetalleElementoAnilloAnterior = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                    ->findOneBy(array(  "elementoId"    => $objElemento->getId(),
                                                                        "detalleNombre" => "ANILLO",
                                                                        "estado"        => "Activo"));
                if(is_object($objDetalleElementoAnilloAnterior))
                {
                    $strAnillo = $objDetalleElementoAnilloAnterior->getDetalleValor();
                }

                $arrayParametrosWs["intIdElemento"] = $objElemento->getId();
                $arrayParametrosWs["intIdServicio"] = $objServicio->getId();

                //obtener el elemento padre del elemento anterior
                $objElementoPadre = $this->servicioGeneral->getPeBySwitch($arrayParametrosWs);

                if(is_object($objElementoPadre))
                {
                    $strNombreElementoPadre = $objElementoPadre->getNombreElemento();
                }
                else
                {
                    throw new \Exception("Mensaje:".$objElementoPadre);
                }
                
                //obtener la vlan 
                $objSolCaracVlan = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "VLAN", $objServicio->getProductoId());

                if(is_object($objSolCaracVlan))
                {
                    $objPerEmpRolCarVlan = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                             ->find($objSolCaracVlan->getValor());
                    if(is_object($objPerEmpRolCarVlan))
                    {
                        $objDetalleElementoVlanAnterior = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                                  ->find($objPerEmpRolCarVlan->getValor());
                        if(is_object($objDetalleElementoVlanAnterior))
                        {
                            $strVlan = $objDetalleElementoVlanAnterior->getDetalleValor();
                        }
                    }
                }
            }
            else
            {
                $strNombreElementoPadre = isset($arrayParametros['strNombreElementoPadre'])?$arrayParametros['strNombreElementoPadre']:null;
                $strVlan                = isset($arrayParametros['strVlan'])?$arrayParametros['strVlan']:null;
            }                       
            //------------------------------------------------------------------

            //obtener la vrf 
            $objSolCaracVrf = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "VRF", $objServicio->getProductoId());

            $objPerEmpRolCarVrf = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                ->find($objSolCaracVrf->getValor());
            //------------------------------------------------------------------

            //obtener el protocolo
            $objSolCaracProtocolo = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "PROTOCOLO_ENRUTAMIENTO", 
                                                                $objServicio->getProductoId());
            //------------------------------------------------------------------

            //obtener el rd_id 
            $objCaractRdId = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneBy(array("descripcionCaracteristica" => "RD_ID", 
                                                                  "estado" => "Activo"));

            $objPerEmpRolCarRdId = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                ->findOneBy(array("personaEmpresaRolCaracId" => $objPerEmpRolCarVrf->getPersonaEmpresaRolCaracId(),
                                                                  "caracteristicaId"         => $objCaractRdId->getId(),
                                                                  "estado"                   => "Activo"));
            //------------------------------------------------------------------

            //obtener el default gateway 
            $objSolCaracDefaultGw = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "DEFAULT_GATEWAY", 
                                                                $objServicio->getProductoId());
            //------------------------------------------------------------------
            
            //obtener el as privado
            $objCaractAs = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneBy(array("descripcionCaracteristica" => "AS_PRIVADO", 
                                                                  "estado" => "Activo"));

            $objSolCaracAsPrivado = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                ->findOneBy(array("personaEmpresaRolId" => $objPerEmpRolCarVrf->getPersonaEmpresaRolId(),
                                                                  "caracteristicaId"    => $objCaractAs->getId(),
                                                                  "estado"              => "Activo"));
            
            //------------------------------------------------------------------

            //*************Validar si la orden de servicio tiene seteado el esquema de Pe-Hsrp*************//
            $arrayPeticiones                   = array();
            $arrayPeticiones['banderaBravco']  = 'NO';
            
            if($objServicio->getdescripcionpresentafactura() == 'CANAL TELEFONIA')
            {
                $arrayPeticiones['clase_servicio']    = 'NETVOICE-L3MPLS';      
            }
            else
            {             
                $arrayPeticiones['clase_servicio']    = $objProducto->getNombreTecnico();
            }       
            
            $strBanderaServProdCaract                       = "N";
            $arrayParametrosProdCaract["strCaracteristica"] = "PE-HSRP";
            $arrayParametrosProdCaract["objProducto"]       = $objProducto;
            $arrayParametrosProdCaract["objServicio"]       = $objServicio;

            $strBanderaServProdCaract = $this->serviceCliente->consultaServicioProdCaract($arrayParametrosProdCaract);

            if($strBanderaServProdCaract === "S")
            {
                $arrayPeticiones['banderaBravco']  = 'SI';
                $arrayPeticiones['clase_servicio'] = $objProducto->getClasificacion().'-HSRP';
            }
            //*************Validar si la orden de servicio tiene seteado el esquema de Pe-Hsrp*************//

            //Consultar Razon Social
            $objInfoPersona = $objServicio->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId();

            if(is_object($objInfoPersona))
            {
                $strRazonSocial = $objInfoPersona->getRazonSocial();
            }

            if(!empty($strRazonSocial))
            {
                $arrayRazonesSociales = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                          ->getOne('PROYECTO MONITOREO CLIENTES GRUPO BRAVCO',
                                                                   'INFRAESTRUCTURA',
                                                                   'ACTIVAR SERVICIO',
                                                                   'RAZON SOCIAL GRUPO BRAVCO',
                                                                   $strRazonSocial,
                                                                   '',
                                                                   '',
                                                                   '',
                                                                   '',
                                                                   '');
            }

            if(isset($arrayRazonesSociales["valor1"]) && !empty($arrayRazonesSociales["valor1"]))
            {
                $strBanderaLineasBravco = "S";
                $strRouteTargetExport   = $arrayRazonesSociales["valor2"];
                $strRouteTargetImport   = $arrayRazonesSociales["valor3"];
                $strRazonSocial         = $arrayRazonesSociales["valor4"];
            }

            //accion a ejecutar
            $arrayPeticiones['url']                   = 'configPE';
            $arrayPeticiones['accion']                = 'Cancelar';        
            $arrayPeticiones['sw']                    = is_object($objElemento)?$objElemento->getNombreElemento():null;
            $arrayPeticiones['vrf']                   = $objPerEmpRolCarVrf->getValor();
            $arrayPeticiones['pe']                    = $strNombreElementoPadre;
            $arrayPeticiones['anillo']                = $strAnillo;
            $arrayPeticiones['vlan']                  = $strVlan;
            $arrayPeticiones['subred']                = $objSubredAnterior->getSubred();
            $arrayPeticiones['mascara']               = $objSubredAnterior->getMascara();
            $arrayPeticiones['gateway']               = $objSubredAnterior->getGateway();
            $arrayPeticiones['rd_id']                 = $objPerEmpRolCarRdId->getValor();
            $arrayPeticiones['descripcion_interface'] = $objServicio->getLoginAux();
            $arrayPeticiones['ip_bgp']                = $objIp->getIp();
            $arrayPeticiones['asprivado']             = ($objSolCaracAsPrivado) ? $objSolCaracAsPrivado->getValor(): "";
            $arrayPeticiones['nombre_sesion_bgp']     = $objServicio->getLoginAux();
            $arrayPeticiones['default_gw']            = ($objSolCaracDefaultGw) ? $objSolCaracDefaultGw->getValor(): "NO";
            $arrayPeticiones['protocolo']             = $objSolCaracProtocolo->getValor();
            $arrayPeticiones['servicio']              = $objProducto->getNombreTecnico();
            $arrayPeticiones['login_aux']             = $objServicio->getLoginAux();
            $arrayPeticiones['tipo_enlace']           = $objServicioTecnico->getTipoEnlace();
            $arrayPeticiones['weight']                = null;
            $arrayPeticiones['user_name']             = $arrayParametros['usrCreacion'];
            $arrayPeticiones['user_ip']               = $arrayParametros['ipCreacion'];

            //Se envian a configurar lineas de monitoreo de enlaces de datos
            if($strBanderaLineasBravco === "S")
            {
                $arrayPeticiones['razon_social'] = $strRazonSocial;
                $arrayPeticiones['rt_export']    = $strRouteTargetExport;
                $arrayPeticiones['rt_import']    = $strRouteTargetImport;
            }

            //ROUTER - Ejecucion del metodo via WS para realizar la configuracion en el Pe
            $arrayRespuesta = $this->networkingScripts->callNetworkingWebService($arrayPeticiones);

            $status     = $arrayRespuesta['status'];
            $mensaje    = $arrayRespuesta['mensaje'];
            $statusCode = $arrayRespuesta['statusCode'];
        }
        else
        {
            $status     = "OK";
            $mensaje    = "OK";
            $statusCode = 200;
        }
        
        $respuestaArray = array('status' => $status, 'mensaje' => $mensaje , 'statusCode' => $statusCode);
        return $respuestaArray;
    }
    
    /**
     * Funcion que sirve para cambiar el bw del concentrador
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 25-05-2016
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.1 1-06-2016
     * Se agrega que se actualice las capacidades del concentrador
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.2 01-06-2020 - Se agrega el id del servicio a la url 'configBW' del ws de networking para la validación del BW
     *
     * @param $arrayParametros [objProducto, objServicio, usrCreacion, ipCreacion]
     */
    public function cambiarBwConcentrador($arrayParametros)
    {
        $objProducto    = $arrayParametros['objProducto'];
        $objServicio    = $arrayParametros['objServicio'];
        $strUsrCreacion = $arrayParametros['usrCreacion'];
        $strIpCreacion  = $arrayParametros['ipCreacion'];
        $flagExtremo    = false;
        $status         = "ERROR";
        $mensaje        = "ERROR";
        $statusCode     = 403;
        
        if($objProducto->getNombreTecnico()=="L3MPLS" && $objProducto->getEsConcentrador() == "NO")
        {
            //validacion de concentrador
            $objSpcEnlaceDatos = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "ENLACE_DATOS", $objProducto);
            if(is_object($objSpcEnlaceDatos))
            {
                $objServicioConcentrador    = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                   ->find(intval($objSpcEnlaceDatos->getValor()));
                
                if($objServicioConcentrador->getEstado() != "Activo")
                {
                    $status  = "ERROR";
                    $mensaje = "El Servicio del Concentrador no se encuentra en estado Activo, Favor Verificar!";

                    $respuestaArray[] = array('status'=>$status, 'mensaje'=>$mensaje);
                    return $respuestaArray;
                }
                else
                {
                    $flagExtremo = true;
                }
            }
            else
            {
                $status  = "ERROR";
                $mensaje = "El servicio no se encuentra enlazado, Favor Verificar!";

                $respuestaArray[] = array('status'=>$status, 'mensaje'=>$mensaje);
                return $respuestaArray;
            }
        }
        else
        {
            $status         = "OK";
            $mensaje        = "OK";
            $statusCode     = 404;
        }
        
        if($flagExtremo)
        {
            //datos del extremo
            $objSpcExtCapacidad1 = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, 
                                                                                             "CAPACIDAD1", 
                                                                                             $objServicio->getProductoId());
            $objSpcExtCapacidad2 = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, 
                                                                                             "CAPACIDAD2", 
                                                                                             $objServicio->getProductoId());
            
            //datos del concentrador
            $objServicioTecConcentrador = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                ->findOneBy(array("servicioId" => $objServicioConcentrador->getId()));
            $objElementoConcentrador = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                ->find($objServicioTecConcentrador->getElementoId());
            $objInterfaceElementoConcentrador = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                ->find($objServicioTecConcentrador->getInterfaceElementoId());
            $objSpcConCapacidad1 = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioConcentrador, 
                                                                                             "CAPACIDAD1", 
                                                                                             $objServicioConcentrador->getProductoId());
            $objSpcConCapacidad2 = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioConcentrador, 
                                                                                             "CAPACIDAD2", 
                                                                                             $objServicioConcentrador->getProductoId());
            $objDetalleAnillo = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                ->findOneBy(array(  "elementoId"    => $objElementoConcentrador->getId(),
                                                                    "detalleNombre" => "ANILLO",
                                                                    "estado"        => "Activo"));

            //ejecutar script para bw de concentrador
            $arrayPeticionesBw = array();
            $arrayPeticionesBw['url']       = 'configBW';
            $arrayPeticionesBw['accion']    = 'Activar';
            $arrayPeticionesBw['id_servicio'] = $objServicioConcentrador->getId();
            $arrayPeticionesBw['nombreMetodo'] = 'InfoCancelarServicioService.cambiarBwConcentrador';
            $arrayPeticionesBw['sw']        = $objElementoConcentrador->getNombreElemento();
            $arrayPeticionesBw['pto']       = $objInterfaceElementoConcentrador->getNombreInterfaceElemento();
            $arrayPeticionesBw['anillo']    = $objDetalleAnillo->getDetalleValor();
            $arrayPeticionesBw['bw_up']     = intval($objSpcConCapacidad1->getValor()) - intval($objSpcExtCapacidad1->getValor());
            $arrayPeticionesBw['bw_down']   = intval($objSpcConCapacidad2->getValor()) - intval($objSpcExtCapacidad2->getValor());
            $arrayPeticionesBw['servicio']  = $objServicioConcentrador->getProductoId()->getNombreTecnico();
            $arrayPeticionesBw['login_aux'] = $objServicioConcentrador->getLoginAux();
            $arrayPeticionesBw['user_name'] = $strUsrCreacion;
            $arrayPeticionesBw['user_ip']   = $strIpCreacion;

            //Ejecucion del metodo via WS para realizar la configuracion del SW
            $arrayRespuestaBw = $this->networkingScripts->callNetworkingWebService($arrayPeticionesBw);

            $status  = $arrayRespuestaBw['status'];
            $mensaje = $arrayRespuestaBw['mensaje'];
            $statusCode  = $arrayRespuestaBw['statusCode'];

            if($status != "OK")
            {
                $respuestaArray = array('status'     => $status, 
                                        'mensaje'    => "Error en el <b>CONCENTRADOR</b> ".$objServicioConcentrador->getLoginAux().": <br>".$mensaje,
                                        'statusCode' => $statusCode);
                return $respuestaArray;
            }
            
            $objSpcConCapacidad1->setValor(intval($objSpcConCapacidad1->getValor()) - intval($objSpcExtCapacidad1->getValor()));
            $this->emComercial->persist($objSpcConCapacidad1);
            $this->emComercial->flush();
            
            $objSpcConCapacidad2->setValor(intval($objSpcConCapacidad2->getValor()) - intval($objSpcExtCapacidad2->getValor()));
            $this->emComercial->persist($objSpcConCapacidad2);
            $this->emComercial->flush();
        }
        
        $respuestaArray = array('status'=>$status, 'mensaje'=>$mensaje, 'statusCode' => $statusCode);
        return $respuestaArray;
    }
    
    /**
     * Funcion que sirve para validar si otro servicio usa la misma ultima milla del servicio actual
     * true:  no usa los mismos datos tecnicos
     * false: usa los mismos datos tecnicos
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 25-05-2016
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 22-11-2016 - Se ajusta para que soporte escenario para servicios pseudope
     * 
     * @param $arrayParametros [objServicio, objServicioTecnico]
     */
    public function validarUmPorServicio($arrayParametros)
    {
        $objServicioAct     = $arrayParametros['objServicio'];
        $objServicioTecAct  = isset($arrayParametros['objServicioTecnico'])?$arrayParametros['objServicioTecnico']:null;
        $objPuntoAct        = $objServicioAct->getPuntoId();
        $flagUm             = true;
        
        $arrayServiciosActivos  = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                            ->findBy(array(  "puntoId"   => $objPuntoAct->getId(),
                                                             "estado"    => "Activo"));

        $arrayServiciosEnPruebas  = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                            ->findBy(array(  "puntoId"   => $objPuntoAct->getId(),
                                                             "estado"    => "EnPruebas"));

        $arrayServiciosInCorte  = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                            ->findBy(array(  "puntoId"   => $objPuntoAct->getId(),
                                                             "estado"    => "In-Corte"));
        
        $arrayServicios = array_merge($arrayServiciosActivos, $arrayServiciosInCorte, $arrayServiciosEnPruebas);

        foreach($arrayServicios as $objServicio)
        {
            if(is_object($objServicio) && $objServicio->getProductoId()->getEsEnlace()=="SI")
            {
                if(isset($arrayParametros['boolEsPseudoPe']) && $arrayParametros['boolEsPseudoPe'])
                {
                    $arrayParametrosPseudoPe      = array ('idServicio' => $objServicio->getId());
                    $arrayDatosTecnicosPseudoPe   = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                                      ->getDatosFactibilidadPseudoPe($arrayParametrosPseudoPe);

                    if(isset($arrayDatosTecnicosPseudoPe['data']))
                    {
                        $idElementoPadre  = $arrayDatosTecnicosPseudoPe['data']['idElemento'];
                        
                        if(isset($arrayParametros['intIdElementoPadre']))
                        {
                            if($idElementoPadre == $arrayParametros['intIdElementoPadre'])
                            {
                                return false;
                            }
                        }
                    }
                }
                else
                {
                    $objServicioTecnico  = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                ->findOneBy(array(  "servicioId"   => $objServicio->getId()));
                
                    if(is_object($objServicioTecnico))
                    {
                        //si sale del mismo puerto del sw y usa el mismo puerto del cassette -> usa los mismos datos tecnicos
                        if($objServicioTecAct->getInterfaceElementoId() == $objServicioTecnico->getInterfaceElementoId() &&
                        $objServicioTecAct->getInterfaceElementoConectorId() == $objServicioTecnico->getInterfaceElementoConectorId())
                        {
                            $flagUm = false;
                            break;
                        }
                    }                   
                }
            }
        }
        
        return $flagUm;
    }
    
    /**
     * Funcion que sirve para obtener el elemento cpe para servicios de TN
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 25-05-2016
     * @param $objServicioTecnico
     */
    public function getElementoCpeServicioTn($objServicioTecnico)
    {
        $objElementoCpe = null;
        
        //obtener el cpe
        if($objServicioTecnico->getElementoConectorId() == null)
        {
            //directo cpe
            $objElementoCpe = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                    ->find($objServicioTecnico->getElementoClienteId());
        }
        else
        {
            $strUMRadio = "NO";
            //se busca tipo medio radio para buscar CPE correctamente para este tipo Medio.
            $objAdmiTipoMedio = $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')
                                                        ->findOneByNombreTipoMedio("Radio");
            if(is_object($objAdmiTipoMedio))
            {
                if ($objServicioTecnico->getUltimaMillaId() == $objAdmiTipoMedio->getId())
                {
                    $strUMRadio = "SI";
                }
            }
            
            if ($strUMRadio == "SI")
            {
                //buscar cpe
                $arrayParametrosCpe = array('interfaceElementoConectorId'   => $objServicioTecnico->getInterfaceElementoClienteId(),
                                            'tipoElemento'                  => "CPE");
            }
            else
            {
                //buscar cpe
                $arrayParametrosCpe = array('interfaceElementoConectorId'   => $objServicioTecnico->getInterfaceElementoConectorId(),
                                            'tipoElemento'                  => "CPE");
            }
            
            
            $arrayRespuestaCpe = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                    ->getElementoClienteByTipoElemento($arrayParametrosCpe);

            if($arrayRespuestaCpe['msg'] == "FOUND")
            {
                $objElementoCpe  = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($arrayRespuestaCpe['idElemento']);
            }
        }//else
        
        return $objElementoCpe;
    }
    
    /**
     * Funcion que sirve para validar si otro servicio usa el mismo cpe
     * true:  no existe otro cpe 
     * false: existe otro cpe
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 25-05-2016          
     * 
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.1 24-11-2016 - Se agrega validacion cuando el elemento cliente sea tambien ROUTER
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.2 21-12-2016 - Se agrega validacion para servicios tecnicos que no posean informacion de elemento cliente al momento de
     *                           obtener informacion de CPE ( escenarios de servicios donde la data tecnica completa no es necesaria o sean
     *                           Servicios adicionales )
     * 
     * @author David León <mdleon@telconet.ec>
     * @version 1.3 23-01-2020 - Se agrega validacion para migraciones Sdwan con Misma ultima milla pero diferente CPE, se valida atraves del
     *                           id_servicio nuevo, de no llegar este dato no se toma en cuenta en la validación).
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.4 23-02-2022 - Se agrega parametro de cancelacion para hacer validaciones para opcion configSW.
     * 
     * @param $arrayParametros [objServicio, objElementoCpe]
     */
    public function validarCpePorServicio($arrayParametros)
    {
        $objServicioAct     = $arrayParametros['objServicio'];
        $objElementoCpeAct  = $arrayParametros['objElementoCpe'];
        $objPuntoAct        = $objServicioAct->getPuntoId();
        $objElementoCpe     = null;
        $flagCpe            = true;
        $objServicioSdwan   = $arrayParametros['objServicioSdwan'];
        $strTipoEnlace      = $arrayParametros['strTipoEnlace'];

        $arrayServiciosActivos  = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                            ->findBy(array(  "puntoId"   => $objPuntoAct->getId(),
                                                             "estado"    => "Activo"));

        $arrayServiciosEnPruebas  = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                            ->findBy(array(  "puntoId"   => $objPuntoAct->getId(),
                                                             "estado"    => "EnPruebas"));

        $arrayServiciosInCorte  = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                            ->findBy(array(  "puntoId"   => $objPuntoAct->getId(),
                                                             "estado"    => "In-Corte"));

        $arrayServicios = array_merge($arrayServiciosActivos, $arrayServiciosInCorte, $arrayServiciosEnPruebas);

        foreach($arrayServicios as $objServicio)
        {
            $servicioTecnico = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                    ->findOneBy(array("servicioId" => $objServicio->getId()));
            if(is_object($servicioTecnico))
            {
                if($objServicioAct->getId() != $objServicio->getId() &&
                    (empty($objServicioSdwan) || $objServicio->getId() == $objServicioSdwan) &&
                    (empty($strTipoEnlace) || $servicioTecnico->getTipoEnlace() != $strTipoEnlace))
                {
                    if($servicioTecnico->getElementoConectorId() == null)
                     {
                         if($servicioTecnico->getElementoClienteId() != null)
                         {
                             //directo cpe
                             $objElementoCpe = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                    ->find($servicioTecnico->getElementoClienteId());
                         }
                     }
                     else
                     {
                         $strUMRadio = "NO";
                         //se busca tipo medio radio para buscar CPE correctamente para este tipo Medio.
                         $objAdmiTipoMedio = $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')
                                                                     ->findOneByNombreTipoMedio("Radio");
                         if(is_object($objAdmiTipoMedio))
                         {
                             if ($servicioTecnico->getUltimaMillaId() == $objAdmiTipoMedio->getId())
                             {
                                 $strUMRadio = "SI";
                             }
                         }

                         if ($strUMRadio == "SI")
                         {
                             //buscar cpe
                             $arrayParametrosCpe = array('interfaceElementoConectorId'  => $servicioTecnico->getInterfaceElementoClienteId(),
                                                         'tipoElemento'                 => "CPE");
                         }
                         else
                         {
                             //buscar cpe
                             $arrayParametrosCpe = array('interfaceElementoConectorId'  => $servicioTecnico->getInterfaceElementoConectorId(),
                                                         'tipoElemento'                 => "CPE");
                         }

                         $arrayRespuestaCpe = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                 ->getElementoClienteByTipoElemento($arrayParametrosCpe);
                         if($arrayRespuestaCpe['msg'] == "FOUND")
                         {
                             $objElementoCpe  = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                            ->find($arrayRespuestaCpe['idElemento']);
                         }
                         else
                         {
                             $arrayParametrosCpe = array('interfaceElementoConectorId'  => $servicioTecnico->getInterfaceElementoConectorId(),
                                                         'tipoElemento'                 => "ROUTER");
                             $arrayRespuestaCpe = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                                          ->getElementoClienteByTipoElemento($arrayParametrosCpe);
                             if($arrayRespuestaCpe['msg'] == "FOUND")
                             {
                                 $objElementoCpe  = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                            ->find($arrayRespuestaCpe['idElemento']);
                             }
                         }
                     }//else 
                }
            
                if(is_object($objElementoCpe))
                {
                    if($objElementoCpeAct->getId() == $objElementoCpe->getId())
                    {
                        $flagCpe = false;
                        break;
                    }
                }
            }//if(is_object($servicioTecnico))
        }

        return $flagCpe;
    }
    
    /**
     * Funcion que permite generar una Solicitud de Retiro de Equipo
     *
     * @author Juan Carlos Lafuente <jlafuente@telconet.ec>
     * 
     * @version 1.0 10-04-2015 - Creacion del la funcionalidad
     * 
     * @param array $arrayParametros (objServicio, objServicioTecnico, idPersonaEmpresaRol
     *                                ipCreacion, usrCreacion)
     * 
     * @author John Vera R. <javera@telconet.ec>
     * @version 1.1 17-11-2016 Se agregó para que considere los elementos conectados al elemento cliente en el retiro de equipos.
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.2 11-05-2017 Se cambia el orden de instanciacion de variable que no permitia realizar el analisis en todas las interfaces
     *                         que poseen enlace para poder obtener los elementos que posee el cliente
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.3 21-12-2017 - En la tabla INFO_DETALLE_ASIGNACION se registra el campo tipo asignado 'EMPLEADO'
     *
     * @author Walther Joao Gaibor C<wgaibor@telconet.ec>
     * @version 1.4 01-02-2018 - Se implementa logica para cambio de tipo medio.
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.5 22-01-2018 Se agregan validaciones para no retirar equipos reutilizados en activaciones de traslados
     * @since 1.4
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.6 01-06-2021 - Se modifica el proceso añadiendo los dispositivos del cliente que se encuentran en el nodo.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.7 14-10-2022 - Se agrega validación para el retiro de equipos del producto SEG VEHICULO.
     *
     */
    public function generarSolicitudRetiroEquipo($arrayParametros)
    {
        $objServicio                = $arrayParametros['objServicio'];
        $strPermiteAgregarEquipoCtm = "SI";
        if(isset($arrayParametros['arrayElementos']))
        {
            $arrayElementos = $arrayParametros['arrayElementos'];
        }
        if(is_object($arrayParametros['objServicioTecnico']))
        {
            $objInfoElementoCliente = $this->emInfraestructura
                                           ->getRepository('schemaBundle:InfoElemento')
                                           ->find($arrayParametros['objServicioTecnico']->getElementoClienteId());

            if($arrayParametros['strTipoOrden'] == 'C')
            {
                if($arrayParametros['objServicioTecnico']->getElementoClienteId() == $arrayParametros['intIdElemento'])
                {
                    $strPermiteAgregarEquipoCtm = "NO";
                }
            }
        
            if ($strPermiteAgregarEquipoCtm == "SI")
            {
                //creo un array para llevar el control de los elementos que se ingresan
                $arrayElementos = array($objInfoElementoCliente);
            }

            $strPermiteAgregarEquipoCtm = "SI";
            
            //consultar todos los elementos que estén donde el cliente
            $arrayInterfacesElemento = $this->emInfraestructura
                                            ->getRepository('schemaBundle:InfoInterfaceElemento')
                                            ->findByElementoId($arrayParametros['objServicioTecnico']->getElementoClienteId()); 

            foreach($arrayInterfacesElemento as $objInterfaceElemento)
            {            
                $intIteraciones = 0 ;
                $boolEnlace     = true;
                while($boolEnlace == true)
                {
                    //si no entra en ningun if se sale del lazo
                    $boolEnlace= false;
                    $objEnlace = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                         ->findOneBy(array('interfaceElementoIniId' => $objInterfaceElemento->getId(), 
                                                                                           'estado' => 'Activo' ));
                    if(is_object($objEnlace))
                    {
                        $objInterfaceConectada = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                         ->find($objEnlace->getInterfaceElementoFinId());
                        if(is_object($objInterfaceConectada))
                        {
                            //compruebo si existe en el array
                            if (!in_array($objInterfaceConectada->getElementoId(), $arrayElementos))
                            {
                                $strPermiteAgregarEquipoCtm = "SI";
                                if($arrayParametros['strTipoOrden'] == 'C')
                                {
                                    if($objInterfaceConectada->getElementoId()->getId() == $arrayParametros['intIdElemento'])
                                    {
                                        $strPermiteAgregarEquipoCtm = "NO";
                                    }
                                }
                                if($strPermiteAgregarEquipoCtm == "SI")
                                {
                                    //ingreso el elemento al array
                                    array_push($arrayElementos, $objInterfaceConectada->getElementoId());
                                }

                            }
                            //si entra aqui es porque continua en el lazo
                            $boolEnlace           = true;
                            $objInterfaceElemento = $objInterfaceConectada;
                        }
                    }
                    //controlar para que no se cicle el while
                    $intIteraciones++;
                    if ($intIteraciones > 10)
                    {
                        break;
                    }                
                }
            } 
        }

        //Obtenemos los equipos del cliente que se encuentran en el nodo.
        if (is_object($objServicio))
        {
            $arrayElementosNodo = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                    ->obtenerDispositivosClienteNodo(array('intIdServicio' => $objServicio->getId()));

            if (!empty($arrayElementosNodo) && count($arrayElementosNodo) > 0)
            {
                $arrayElementos = array_merge($arrayElementos,$arrayElementosNodo);
            }
        }

        if ("T" == $arrayParametros['strOrigen'])
        {
            $objCaractTraslado = $this->emComercial
                                      ->getRepository('schemaBundle:AdmiCaracteristica')
                                      ->findOneBy(array( "descripcionCaracteristica" => "TRASLADO", 
                                                         "estado"                    => "Activo"));
            $objProdCaractTraslado = $this->emComercial
                                          ->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                          ->findOneBy(array( "productoId"       => $arrayParametros['objServicio']->getProductoId()
                                                                                                                  ->getId(), 
                                                             "caracteristicaId" => $objCaractTraslado->getId()));
            
            $objServProdCaractTraslado = $this->emComercial
                                              ->getRepository('schemaBundle:InfoServicioProdCaract')
                                              ->findOneBy(array( "estado" => "Activo", 
                                                                 "valor"  => $arrayParametros['objServicio']->getId(),
                                                                 "productoCaracterisiticaId" => $objProdCaractTraslado->getId()));
            
            if (is_object($objServProdCaractTraslado))
            {
                $intServicioNuevoId = $objServProdCaractTraslado->getServicioId();
                $objServicioNuevo   = $this->emComercial
                                           ->getRepository('schemaBundle:InfoServicio')
                                           ->find($intServicioNuevoId);
                
                $objServicioTecnico   = $this->emComercial
                                             ->getRepository('schemaBundle:InfoServicioTecnico')
                                             ->findOneByServicioId($objServicioNuevo->getId());
                
                if (is_object($objServicioTecnico))
                {
                    $objInfoElementoCliente = $this->emInfraestructura
                                                   ->getRepository('schemaBundle:InfoElemento')
                                                   ->find($objServicioTecnico->getElementoClienteId());
                    //creo un array para llevar el control de los elementos que se ingresan
                    $arrayElementosNuevos = array($objInfoElementoCliente);

                    //consultar todos los elementos que estén donde el cliente
                    $arrayInterfacesElemento = $this->emInfraestructura
                                                    ->getRepository('schemaBundle:InfoInterfaceElemento')
                                                    ->findByElementoId($objServicioTecnico->getElementoClienteId()); 

                    foreach($arrayInterfacesElemento as $objInterfaceElemento)
                    {            
                        $intIteraciones = 0 ;
                        $boolEnlace     = true;
                        while($boolEnlace == true)
                        {
                            //si no entra en ningun if se sale del lazo
                            $boolEnlace= false;
                            $objEnlace = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                                 ->findOneBy(array('interfaceElementoIniId' => $objInterfaceElemento->getId(), 
                                                                                                   'estado' => 'Activo' ));
                            if(is_object($objEnlace))
                            {
                                $objInterfaceConectada = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                                 ->find($objEnlace->getInterfaceElementoFinId());
                                if(is_object($objInterfaceConectada))
                                {
                                    //compruebo si existe en el array
                                    if (!in_array($objInterfaceConectada->getElementoId(), $arrayElementosNuevos))
                                    {
                                        //ingreso el elemento al array
                                        array_push($arrayElementosNuevos, $objInterfaceConectada->getElementoId());
                                    }
                                    //si entra aqui es porque continua en el lazo
                                    $boolEnlace           = true;
                                    $objInterfaceElemento = $objInterfaceConectada;
                                }
                            }
                            //controlar para que no se cicle el while
                            $intIteraciones++;
                            if ($intIteraciones > 10)
                            {
                                break;
                            }                
                        }
                    }
                    
                    $arrayElementosNoReutilizados = array();
                    $strElementoReutilizado = "NO";
                    foreach($arrayElementos as $objElementoCancelar)
                    {
                        $strElementoReutilizado = "NO";
                        foreach ($arrayElementosNuevos as $objElementoNuevo)
                        {
                            if (strtoupper($objElementoCancelar->getSerieFisica()) == strtoupper($objElementoNuevo->getSerieFisica()))
                            {
                                $strElementoReutilizado = "SI";
                            }
                        }
                        
                        if ($strElementoReutilizado == "NO")
                        {
                            array_push($arrayElementosNoReutilizados, $objElementoCancelar);
                        }
                    }
                    
                    $arrayElementos = $arrayElementosNoReutilizados;
                }
            }
        }
        
        if (count($arrayElementos) > 0)
        {
            $feCreacion = new \DateTime('now');
            // --------------------------------------------------------------------------------------------------------
            $objTipoSolicitud = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                     ->findOneBy(array( "descripcionSolicitud" => "SOLICITUD RETIRO EQUIPO", 
                                                        "estado"               => "Activo"));
            // --------------------------------------------------------------------------------------------------------
            $objCaracteristica= $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                  ->findOneBy(array( "descripcionCaracteristica" => "ELEMENTO CLIENTE", 
                                                                     "estado"                    => "Activo"));
            // --------------------------------------------------------------------------------------------------------
            $objProceso = $this->emSoporte->getRepository('schemaBundle:AdmiProceso')
                                          ->findOneByNombreProceso("SOLICITAR RETIRO EQUIPO");
            $objTareas  = $this->emSoporte->getRepository('schemaBundle:AdmiTarea')
                                          ->findTareasActivasByProceso($objProceso->getId());
            $objTarea = $objTareas[0];
            // --------------------------------------------------------------------------------------------------------        
            $strRegion           = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                     ->getRegionPorServicio($arrayParametros['objServicio']->getId());    
            $objAdmiParametroCab = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                   ->findOneByNombreParametro('RESPONSABLES_RETIRO_EQUIPO');
            $arrayAdmiParametroDet = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                     ->findBy(array('parametroId'=> $objAdmiParametroCab->getId(),
                                                                    'valor1'     => $strRegion?$strRegion:"R2"));        

            $objInfoPersona = $this->emComercial->getRepository('schemaBundle:InfoPersona')->findOneByLogin($arrayAdmiParametroDet[0]->getValor3()); 
            $objPersonaEmpresaRolUsr = $this->emComercial
                                              ->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                              ->findByIdentificacionTipoRolEmpresa($objInfoPersona->getIdentificacionCliente(),'Empleado','10');
            //...
            $departamento = $this->emGeneral->getRepository('schemaBundle:AdmiDepartamento')->find($arrayAdmiParametroDet[0]->getValor2());
            // --------------------------------------------------------------------------------------------------------
            // Creamos el Detalle Solicitud de Retito de Equipo
            $objDetalleSolicitud = new InfoDetalleSolicitud();
            $objDetalleSolicitud->setServicioId($arrayParametros['objServicio']);
            $objDetalleSolicitud->setTipoSolicitudId($objTipoSolicitud);
            $objDetalleSolicitud->setEstado("AsignadoTarea");
            $objDetalleSolicitud->setUsrCreacion($arrayParametros['usrCreacion']);
            $objDetalleSolicitud->setFeCreacion($feCreacion);
            $objDetalleSolicitud->setObservacion("SOLICITA RETIRO DE EQUIPO POR CANCELACION DEL SERVICIO");
            $this->emComercial->persist($objDetalleSolicitud);
            $this->emComercial->flush();

            foreach($arrayElementos as $objElemento)
            {
                // Detalle de la solicitud con el elemento del cliente
                $objDetalleSolicitudCarac = new InfoDetalleSolCaract();
                $objDetalleSolicitudCarac->setCaracteristicaId($objCaracteristica);
                $objDetalleSolicitudCarac->setDetalleSolicitudId($objDetalleSolicitud);
                $objDetalleSolicitudCarac->setValor($objElemento->getId());
                $objDetalleSolicitudCarac->setEstado("AsignadoTarea");
                $objDetalleSolicitudCarac->setFeCreacion($feCreacion);
                $objDetalleSolicitudCarac->setUsrCreacion($arrayParametros['usrCreacion']);
                $this->emComercial->persist($objDetalleSolicitudCarac);
                $this->emComercial->flush();
            }

            //grabar nuevo info_detalle para la solicitud de retiro de equipo
            $objDetalle = new InfoDetalle();
            $objDetalle->setDetalleSolicitudId($objDetalleSolicitud->getId());
            $objDetalle->setTareaId($objTarea);
            $objDetalle->setPesoPresupuestado(0);
            $objDetalle->setValorPresupuestado(0);
            $objDetalle->setFeCreacion($feCreacion);
            $objDetalle->setIpCreacion($arrayParametros['ipCreacion']);
            $objDetalle->setUsrCreacion($arrayParametros['usrCreacion']);
            $this->emSoporte->persist($objDetalle);
            $this->emSoporte->flush();

            // Asignacion de la Solicitud de Retiro de Equipo
            $objDetalleAsignacion = new InfoDetalleAsignacion();
            $objDetalleAsignacion->setDetalleId($objDetalle);
            $objDetalleAsignacion->setAsignadoId($departamento->getId());
            $objDetalleAsignacion->setAsignadoNombre($departamento->getNombreDepartamento());
            $objDetalleAsignacion->setRefAsignadoId($objPersonaEmpresaRolUsr->getPersonaId()->getId());
            if($objPersonaEmpresaRolUsr->getPersonaId()->getRazonSocial()=="")
            {
                $nombre = $objPersonaEmpresaRolUsr->getPersonaId()->getNombres()." ".$objPersonaEmpresaRolUsr->getPersonaId()->getApellidos();
            }
            else
            {
                $nombre = $objPersonaEmpresaRolUsr->getPersonaId()->getRazonSocial();
            }
            $objDetalleAsignacion->setRefAsignadoNombre($nombre);
            $objDetalleAsignacion->setPersonaEmpresaRolId($objPersonaEmpresaRolUsr->getId());
            $objDetalleAsignacion->setFeCreacion($feCreacion);
            $objDetalleAsignacion->setIpCreacion($arrayParametros['ipCreacion']);
            $objDetalleAsignacion->setUsrCreacion($arrayParametros['usrCreacion']);
            $this->emSoporte->persist($objDetalleAsignacion);
            $this->emSoporte->flush();

            //crear historial para la solicitud
            $objDetalleSolicitudHistorial = new InfoDetalleSolHist();
            $objDetalleSolicitudHistorial->setDetalleSolicitudId($objDetalleSolicitud);
            $objDetalleSolicitudHistorial->setEstado("AsignadoTarea");
            $objDetalleSolicitudHistorial->setObservacion("GENERACION AUTOMATICA DE SOLICITUD RETIRO DE EQUIPO POR CANCELACION DEL SERIVICIO");
            $objDetalleSolicitudHistorial->setFeCreacion($feCreacion);
            $objDetalleSolicitudHistorial->setIpCreacion($arrayParametros['ipCreacion']);
            $objDetalleSolicitudHistorial->setUsrCreacion($arrayParametros['usrCreacion']);
            $this->emComercial->persist($objDetalleSolicitudHistorial);
        }
    }
    
    /**
     * Funcion que permite generar la cancelacion de los datos del Backbone del clientes
     * desde la interface del elemento conector hasta el ultimo elemento de los enlaces
     *
     * @author Juan Carlos Lafuente <jlafuente@telconet.ec>
     * @version 1.0 10-04-2016 - Creacion del la funcionalidad
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.2 18-11-2016 - Validacion de objetos
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.1 10-04-2016
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.2 22-16-2016 - Se modifica y se quita bloque de código de cancelacion de equipo dentro de la eliminacion de datos de backbone
     *                           de cada servicio ya que genera inconsistencia cuando existe mas de un servicio ligado a un cpe
     * 
     * @param array $arrayParametros (objServicio, objServicioTecnico
     *                                ipCreacion, usrCreacion)
     */
    public function generarCancelacionDatosBackboneConexion($arrayParametros)
    {
        // ---------------------------------------------------------------------------
        // Eliminacion de IPs asociadas al servicio
        $arrayIPs = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                            ->findBy(array( "servicioId" => $arrayParametros['objServicio']->getId(),
                                                            "estado"     => "Activo"));
        if($arrayIPs)
        {
            foreach($arrayIPs as $objIp)
            {
                $objIp->setEstado("Eliminado");
                $this->emInfraestructura->persist($objIp);
                $this->emInfraestructura->flush();
            }
        }
        // ---------------------------------------------------------------------------
        // Eliminacion de las caracteristicas del servicio
        $arrayServicioProductoCaract = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                         ->findBy(array("servicioId" => $arrayParametros['objServicio']->getId(), 
                                                                        "estado"=>"Activo"));
        if($arrayServicioProductoCaract)
        {
            foreach($arrayServicioProductoCaract as $objServicioProductoCarac)
            {
                $objServicioProductoCarac->setEstado("Eliminado");
                $this->emComercial->persist($objServicioProductoCarac);
                $this->emComercial->flush();
            }
        }                
    }        
    
    /**
     * Funcion recursiva que permite eliminar todos los enlaces, elementos e interfaces
     * del backbone con el cliente desde la interface del elemento conector
     *
     * @author Juan Carlos Lafuente <jlafuente@telconet.ec>
     * 
     * @version 1.0 10-04-2015 - Creacion del la funcionalidad
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 02-06-2021 - Se registra tracking del elemento.
     *
     * @param array $arrayParametros (interfaceElementoConectorId, interfaceEstado
     *                                ipCreacion, usrCreacion)
     */
    public function cancelarElementosEnlazados($arrayParametros)
    {
        $interfaceElementoConectorId = $arrayParametros['interfaceElementoConectorId'];
        $interfaceEstado             = $arrayParametros['interfaceEstado'];
        $usrCreacion                 = $arrayParametros['usrCreacion'];
        $ipCreacion                  = $arrayParametros['ipCreacion'];
        $objServicio                 = $arrayParametros['objServicio'];

        // ...
        $feCreacion = new \DateTime('now');
        // --------------------------------------------------------------------------------------------------------
        $objInterfaceConector = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                        ->find($interfaceElementoConectorId);
        $objInterfaceConector->setEstado($interfaceEstado);
        $this->emInfraestructura->persist($objInterfaceConector);
        $this->emInfraestructura->flush();

        $arrayEnlaces = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                ->findBy(array("interfaceElementoIniId"=>$objInterfaceConector->getId()));

        if(count($arrayEnlaces)>0)
        {
            foreach($arrayEnlaces as $objEnlace)
            {
                // Se obtiene el elemento que se encuentra en el interfaceFin del enlace
                $objInterfaceFin = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                           ->find($objEnlace->getInterfaceElementoFinId());
                $objElementoFin = $objInterfaceFin->getElementoId();

                // Eliminar elemento del backbone de conexion
                $objElementoFin->setEstado("Eliminado");
                $this->emInfraestructura->persist($objElementoFin);
                $this->emInfraestructura->flush();

                // Historial del elemento eliminado
                $objHistorialElemento = new InfoHistorialElemento();
                $objHistorialElemento->setElementoId($objElementoFin);
                $objHistorialElemento->setObservacion("Se elimino el elemento por cancelacion de Servicio");
                $objHistorialElemento->setEstadoElemento("Eliminado");
                $objHistorialElemento->setUsrCreacion($usrCreacion);
                $objHistorialElemento->setFeCreacion($feCreacion);
                $objHistorialElemento->setIpCreacion($ipCreacion);
                $this->emInfraestructura->persist($objHistorialElemento);
                $this->emInfraestructura->flush();
                
                // Interfaces del elemento eliminado
                $arrayInterfaces = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                           ->findBy(array("elementoId"=>$objElementoFin->getId()));
                foreach($arrayInterfaces as $interface)
                {
                    $interface->setEstado("Eliminado");
                    $this->emInfraestructura->persist($interface);
                    $this->emInfraestructura->flush();
                }

                // Eliminar el enlace
                $objEnlace->setEstado("Eliminado");
                $this->emInfraestructura->persist($objEnlace);
                $this->emInfraestructura->flush();

                //PARAMETROS PARA REGISTRAR EL TRACKING DEL ELEMENTO.
                if ($objElementoFin->getSerieFisica() != null && $objElementoFin->getSerieFisica() != '00000')
                {
                    $arrayParametrosAuditoria = array();
                    $arrayParametrosAuditoria["strUsrCreacion"]  =  $usrCreacion;
                    $arrayParametrosAuditoria["strNumeroSerie"]  =  $objElementoFin->getSerieFisica();
                    $arrayParametrosAuditoria["strEstadoTelcos"] = 'Eliminado';
                    $arrayParametrosAuditoria["strEstadoNaf"]    = 'Instalado';
                    $arrayParametrosAuditoria["strEstadoActivo"] = 'Cancelado';
                    $arrayParametrosAuditoria["strUbicacion"]    = 'Cliente';
                    $arrayParametrosAuditoria["strCodEmpresa"]   = '10';
                    $arrayParametrosAuditoria["strTransaccion"]  = 'Cancelacion Servicio';
                    $arrayParametrosAuditoria["intOficinaId"]    =  0;

                    if (is_object($objServicio))
                    {
                        $objInfoPunto = $objServicio->getPuntoId();

                        if (is_object($objInfoPunto))
                        {
                            $objInfoPersonaEmpresaRol = $objInfoPunto->getPersonaEmpresaRolId();
                            $strCedulaCliente         = is_object($objInfoPersonaEmpresaRol) ?
                                                        is_object($objInfoPersonaEmpresaRol->getPersonaId()) ?
                                                        $objInfoPersonaEmpresaRol->getPersonaId()->getIdentificacionCliente() : "" : "";

                            $arrayParametrosAuditoria["strLogin"]         = $objInfoPunto->getLogin();
                            $arrayParametrosAuditoria["strCedulaCliente"] = $strCedulaCliente;
                        }
                    }

                    //LLAMADA AL MÉTODO QUE REGISTRA EL TRACKING DEL ELEMENTO.
                    $this->serviceInfoElemento->ingresaAuditoriaElementos($arrayParametrosAuditoria);
                }

                $arrayParams = array();
                $arrayParams['objServicio']                 =  $objServicio;
                $arrayParams['interfaceElementoConectorId'] =  $objInterfaceFin->getId();
                $arrayParams['interfaceEstado']             = "Eliminado";
                $arrayParams['usrCreacion']                 =  $usrCreacion;
                $arrayParams['ipCreacion']                  =  $ipCreacion;
                $this->cancelarElementosEnlazados($arrayParams);
            }
        }
    }

    /**
     * Funcion que permite generar la cancelacion de los datos Comerciales del cliente
     * verificando si se procede a cancelar el punto, contrato, personaEmpresaRol y persona
     *
     * @author Juan Carlos Lafuente <jlafuente@telconet.ec>
     * @version 1.0 10-04-2015 - Creacion del la funcionalidad
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 12-05-2017 - Se ajusta la cancelacion del Servicios verificando que cuando solo exista un ultimo Servicio
     *                           Activo a ser cancelado y tengo ademas servicios anulados o eliminados los tome en cuenta en 
     *                           la sumatoria para verificar que ya no existan mas servicios Activos o en proceso de Activacion
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.2 31-03-2017 - Observacion de Historial segun tipo de enlace a ser cancelado, si se cancela PRINCIPAL se escribe que se cancelo
     *                           por tal motivo en el historial del Backup cancelado
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.3 24-10-2017 - Se agrega filtro de estado en busqueda de contrato asociado al punto a cancelar
     * @since 1.2
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.4 22-01-2018 - Se agregan validaciones para cancelar datos comerciales de servicios de servicios trasladados
     * @since 1.3
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.5 31-01-2018 - Se agrega parametro en donde se puede colocar una observacion personalizada en el historial del servicio 
     *
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.6 22-05-2018 Se agregan los estados: "Anulada","Eliminada","Eliminado-Migra","Rechazada"
     * en las condiciones de los estados de los servicios para la correcta Cancelacion a nivel Comercial de estados de Clientes y Contratos, debido a que reportan
     * "ERROR EN ESTATUS - REPORTE DE CARTERA"
     * 
     * @param array $arrayParametros (objServicio, objServicioTecnico
     *                                ipCreacion, usrCreacion , strObservacion)
     */
    public function generarCancelacionDatosComerciales($arrayParametros)
    {
        $strEstadoServicio = 'Cancel';
        $strEstadoPunto    = 'Cancelado';
        $strObservacion    = "Se canceló el Servicio";
        if (!empty($arrayParametros['strOrigen']))
        {
            if ($arrayParametros['strOrigen'] == 'T')
            {
                $strEstadoServicio = 'Trasladado';
                $strEstadoPunto    = 'Trasladado';
                $strObservacion    = $arrayParametros['strObservacion'];
            }
        }
        $feCreacion = new \DateTime('now');
        // --------------------------------------------------------------------------------------------------------
        $objServicio = $arrayParametros['objServicio'];
        // --------------------------------------------------------------------------------------------------------
        $objPuntoServicio = $objServicio->getPuntoId();
        // --------------------------------------------------------------------------------------------------------
        $arrayServicios = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                            ->findBy(array( "puntoId" => $objPuntoServicio->getId()));
        // --------------------------------------------------------------------------------------------------------
        $objPersonaEmpresaRol = $objPuntoServicio->getPersonaEmpresaRolId();
        $arrayPuntos = $this->emComercial->getRepository('schemaBundle:InfoPunto')
                                         ->findBy(array( "personaEmpresaRolId" => $objPersonaEmpresaRol->getId()));
        // --------------------------------------------------------------------------------------------------------
        // Cancelacion del Servicio
        $objServicio->setEstado($strEstadoServicio);
        $this->emComercial->persist($objServicio);
        $this->emComercial->flush();
        
        
        //Si existe el key del array y ademas este es TRUE, se escribe historial de cancelacion para el Servicio BACKUP
        if(isset($arrayParametros['flagEsBackup']) && $arrayParametros['flagEsBackup'])
        {
            $strLoginAux = '';
            
            if(isset($arrayParametros['strLoginAuxPrincipal']) && !empty($arrayParametros['strLoginAuxPrincipal']))
            {
                $strLoginAux = ": ".$arrayParametros['strLoginAuxPrincipal'];
            }
            
            $strObservacion = "Se canceló el Enlace <b>BACKUP</b> por Cancelación de Enlace <b>PRINCIPAL</b> ".$strLoginAux;
        }
        
        if(isset($arrayParametros['strObservacion']) && !empty($arrayParametros['strObservacion']))
        {
            $strObservacion = $arrayParametros['strObservacion'];
        }

        // Creacion del historial del servicio
        $objServicioHistorial = new InfoServicioHistorial();
        $objServicioHistorial->setServicioId($objServicio);
        $objServicioHistorial->setObservacion($strObservacion);
        $objServicioHistorial->setEstado($strEstadoServicio);
        $objServicioHistorial->setFeCreacion($feCreacion);
        $objServicioHistorial->setIpCreacion($arrayParametros['ipCreacion']);
        $objServicioHistorial->setUsrCreacion($arrayParametros['usrCreacion']);
        $objServicioHistorial->setAccion($arrayParametros['objAccion']->getNombreAccion());
        $objServicioHistorial->setMotivoId($arrayParametros['objMotivo']->getId());
        $this->emComercial->persist($objServicioHistorial);
        $this->emComercial->flush();

        // Revisar si es el ultimo servicio para proceder a cancelar el Punto
        $numServicios = count($arrayServicios);
        $cont = 0;
        foreach($arrayServicios as $objServicioFromArray)
        {            
            if($objServicioFromArray->getEstado() == "Cancel"          || 
               $objServicioFromArray->getEstado() == "Cancel-SinEje"   ||
               $objServicioFromArray->getEstado() == "Anulado"         || 
               $objServicioFromArray->getEstado() == "Anulada"         || 
               $objServicioFromArray->getEstado() == "Eliminado"       ||
               $objServicioFromArray->getEstado() == "Eliminada"       ||
               $objServicioFromArray->getEstado() == "Eliminado-Migra" ||
               $objServicioFromArray->getEstado() == "Rechazada"       ||
               $objServicioFromArray->getEstado() == "Trasladado"
              )
            {
                $cont++;
            }
        }

        if($cont == $numServicios)
        {
            $objPuntoServicio->setEstado($strEstadoPunto);
            $this->emComercial->persist($objPuntoServicio);
            $this->emComercial->flush();
        }
        
        if($strEstadoServicio != 'Trasladado')
        {
            // Revisar si es el ultimo servicio para proceder a cancelar al Cliente y su Contrato
            $numPuntos = count($arrayPuntos);
            $contPuntos = 0;
            foreach($arrayPuntos as $objPuntoFromArray)
            {
                if($objPuntoFromArray->getEstado() == "Cancelado")
                {
                    $contPuntos++;
                }
            }
            if( $contPuntos == $numPuntos){
                // --------------------------------------------------------------------------------------------------------
                // Se cancela el contrato
                $objContrato = $this->emComercial
                                    ->getRepository('schemaBundle:InfoContrato')
                                    ->findOneBy(array( "personaEmpresaRolId" => $objPersonaEmpresaRol->getId(),
                                                       "estado"              => "Activo"));
                // --------------------------------------------------------------------------------------------------------
                $objContrato->setEstado("Cancelado");
                $this->emComercial->persist($objContrato);
                $this->emComercial->flush();

                //se elimina las caracteristicas de la persona empresa rol
                $arrayPerEmpRolCarac = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                 ->findBy(array( "personaEmpresaRolId"  => $objPersonaEmpresaRol->getId(),
                                                                 "estado"               => "Activo"));
                foreach($arrayPerEmpRolCarac as $objPerEmpRolCarac)
                {
                    $objPerEmpRolCarac->setEstado("Eliminado");
                    $this->emComercial->persist($objPerEmpRolCarac);
                    $this->emComercial->flush();
                }            

                // Se cancela el personaEmpresaRol
                $objPersonaEmpresaRol->setEstado("Cancelado");
                $this->emComercial->persist($objPersonaEmpresaRol);
                $this->emComercial->flush();

                //Se ingresa un registro en el historial empresa persona rol
                $objIPERHistorial = new InfoPersonaEmpresaRolHisto();
                $objIPERHistorial->setPersonaEmpresaRolId($objPersonaEmpresaRol);
                $objIPERHistorial->setEstado("Cancelado");
                $objIPERHistorial->setFeCreacion($feCreacion);
                $objIPERHistorial->setIpCreacion($arrayParametros['ipCreacion']);
                $objIPERHistorial->setUsrCreacion($arrayParametros['usrCreacion']);
                $this->emComercial->persist($objIPERHistorial);
                $this->emComercial->flush();

                // Se cancela el cliente
                $objPersona = $objPersonaEmpresaRol->getPersonaId();
                $objPersona->setEstado("Cancelado");
                $this->emComercial->persist($objPersona);
                $this->emComercial->flush();                
            }
        }
    }
    
    /**
     *
     * Metodo encargado de eliminar el cpe/router y sus interfaces una vez que se haya generado retiro de equipo
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0
     * @since 16-12-2016
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 07-02-2017 - Se renombra variables segun standard establecido
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 27-02-2018 Se registra tracking del elemento
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.3 13-05-2019 Para cambio de tipo medios no se realiza la eliminación del elemento.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.4 01-06-2021 - Se elimina los elementos del cliente que se encuentran en el nodo.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.5 01-08-2022 - Se valida el parámetro booleanEliminarEnlaces para eliminar los enlaces
     *                           de las interfaces del elemento.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.6 14-10-2022 - Se agrega validación para eliminar los elementos del producto SEG VEHICULO.
     *
     * @param type $arrayParametros 
     *                              [
     *                                 objServicioTecnico             Informacion tecnica del Servicio
     *                                 objServicio                    Servicio a ser cancelado
     *                                 usrCreacion                    Usuario de generacion del requerimiento
     *                                 ipCreacion                     Ip de donde se genera el requerimiento
     *                                 strTipoOrden                   Tipo de Orden
     *                              ]
     */
    public function eliminarElementoCliente($arrayParametros)
    {
        $objServicio = $arrayParametros['objServicio'];

        if($arrayParametros['objServicioTecnico']->getInterfaceElementoConectorId() == null)
        {
            //elimina directamente el elemento cliente
            $intElementoClienteId = isset($arrayParametros['intIdElementoCliente']) && !empty($arrayParametros['intIdElementoCliente'])
                                    ? $arrayParametros['intIdElementoCliente']
                                    : $arrayParametros['objServicioTecnico']->getElementoClienteId();
            $objElementoCliente   = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($intElementoClienteId);
            $strTipoOrden         = $arrayParametros["strTipoOrden"] ? $arrayParametros["strTipoOrden"] : "";

            if ($strTipoOrden !== 'C')
            {
                $objElementoCliente->setEstado("Eliminado");
                $this->emInfraestructura->persist($objElementoCliente);
                $this->emInfraestructura->flush();

                //SE REGISTRA EL TRACKING DEL ELEMENTO
                $arrayParametrosAuditoria = array();
                $arrayParametrosAuditoria["strUsrCreacion"]  =  $arrayParametros['usrCreacion'];
                $arrayParametrosAuditoria["strNumeroSerie"]  =  $objElementoCliente->getSerieFisica();
                $arrayParametrosAuditoria["strEstadoTelcos"] = 'Eliminado';
                $arrayParametrosAuditoria["strEstadoNaf"]    = 'Instalado';
                $arrayParametrosAuditoria["strEstadoActivo"] = 'Cancelado';
                $arrayParametrosAuditoria["strUbicacion"]    = 'Cliente';
                $arrayParametrosAuditoria["strCodEmpresa"]   = '10';
                $arrayParametrosAuditoria["strTransaccion"]  = 'Cancelacion Servicio';
                $arrayParametrosAuditoria["intOficinaId"]    =  0;

                //Se consulta el login del cliente
                if (is_object($arrayParametros['objServicioTecnico']->getServicioId()))
                {
                    $objInfoPunto = $this->emInfraestructura->getRepository('schemaBundle:InfoPunto')
                            ->find($arrayParametros['objServicioTecnico']->getServicioId()->getPuntoId()->getId());

                    if (is_object($objInfoPunto))
                    {
                        $objInfoPersonaEmpresaRol = $objInfoPunto->getPersonaEmpresaRolId();
                        $strCedulaCliente         = is_object($objInfoPersonaEmpresaRol) ?
                                                    is_object($objInfoPersonaEmpresaRol->getPersonaId()) ?
                                                    $objInfoPersonaEmpresaRol->getPersonaId()->getIdentificacionCliente() : "" : "";

                        $arrayParametrosAuditoria["strLogin"]         = $objInfoPunto->getLogin();
                        $arrayParametrosAuditoria["strCedulaCliente"] = $strCedulaCliente;
                    }
                }

                $this->serviceInfoElemento->ingresaAuditoriaElementos($arrayParametrosAuditoria);

                // Historial del elemento eliminado
                $objHistorialElemento = new InfoHistorialElemento();
                $objHistorialElemento->setElementoId($objElementoCliente);
                $objHistorialElemento->setObservacion("Se elimino el elemento por cancelacion de Servicio");
                $objHistorialElemento->setEstadoElemento("Eliminado");
                $objHistorialElemento->setUsrCreacion($arrayParametros['usrCreacion']);
                $objHistorialElemento->setFeCreacion(new \DateTime('now'));
                $objHistorialElemento->setIpCreacion($arrayParametros['ipCreacion']);
                $this->emInfraestructura->persist($objHistorialElemento);
                $this->emInfraestructura->flush();

                //elimina los puertos del elemento cliente
                $arrayInterfaceElementoCliente = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                      ->findBy(array("elementoId" => $objElementoCliente->getId()));

                foreach($arrayInterfaceElementoCliente as $objInterfaceElemento)
                {
                    $objInterfaceElemento->setEstado("Eliminado");
                    $this->emInfraestructura->persist($objInterfaceElemento);
                    $this->emInfraestructura->flush();
                    //verificar eliminar enlaces
                    if($arrayParametros['booleanEliminarEnlaces'])
                    {
                        $arrayEnlaces = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                ->findBy(array("interfaceElementoFinId"=>$objInterfaceElemento->getId()));
                        foreach($arrayEnlaces as $objEnlace)
                        {
                            $objEnlace->setEstado("Eliminado");
                            $this->emInfraestructura->persist($objEnlace);
                            $this->emInfraestructura->flush();
                        }
                    }
                }
            }
        }
        else
        {
            //elimina enlaces y elementos conectados desde el cassette
            $arrayParams = array();
            $arrayParams['objServicio']                 = $objServicio;
            $arrayParams['interfaceEstado']             = 'not connect';
            $arrayParams['ipCreacion']                  = $arrayParametros['ipCreacion'];
            $arrayParams['usrCreacion']                 = $arrayParametros['usrCreacion'];
            $arrayParams['interfaceElementoConectorId'] = $arrayParametros['objServicioTecnico']->getInterfaceElementoConectorId();

            $booleanCpe     = false;
            //obtener cpe del servicio
            $objElementoCpe = $this->getElementoCpeServicioTn($arrayParametros['objServicioTecnico']);
            
            if(is_object($objElementoCpe))
            {
                $arrayParametrosCpe = array('objServicio'    => $arrayParametros['objServicio'],
                                            'objElementoCpe' => $objElementoCpe);
                $booleanCpe         = $this->validarCpePorServicio($arrayParametrosCpe);
            }
            
            if($booleanCpe)
            {
                $this->cancelarElementosEnlazados($arrayParams);
            }
        }

        //Obtenemos los equipos del cliente que se encuentran en el nodo.
        if (is_object($objServicio))
        {
            $objInfoPunto       = $objServicio->getPuntoId();
            $strUsuario         = $arrayParametros['usrCreacion'];
            $strIpUsuario       = $arrayParametros['ipCreacion'];
            $arrayElementosNodo = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                    ->obtenerDispositivosClienteNodo(array('intIdServicio' => $objServicio->getId()));

            if (!empty($arrayElementosNodo) && count($arrayElementosNodo) > 0)
            {
                $arrayParametrosAuditoria = array();
                $arrayParametrosAuditoria["strUsrCreacion"]  =  $strUsuario;
                $arrayParametrosAuditoria["strEstadoTelcos"] = 'Eliminado';
                $arrayParametrosAuditoria["strEstadoNaf"]    = 'Instalado';
                $arrayParametrosAuditoria["strEstadoActivo"] = 'Cancelado';
                $arrayParametrosAuditoria["strUbicacion"]    = 'Nodo';
                $arrayParametrosAuditoria["strCodEmpresa"]   = '10';
                $arrayParametrosAuditoria["strTransaccion"]  = 'Cancelacion Servicio';
                $arrayParametrosAuditoria["intOficinaId"]    =  0;

                if (is_object($objInfoPunto))
                {
                    $objInfoPersonaEmpresaRol = $objInfoPunto->getPersonaEmpresaRolId();
                    $strCedulaCliente         = is_object($objInfoPersonaEmpresaRol) ?
                                                is_object($objInfoPersonaEmpresaRol->getPersonaId()) ?
                                                $objInfoPersonaEmpresaRol->getPersonaId()->getIdentificacionCliente() : "" : "";

                    $arrayParametrosAuditoria["strLogin"]         = $objInfoPunto->getLogin();
                    $arrayParametrosAuditoria["strCedulaCliente"] = $strCedulaCliente;
                }

                foreach ($arrayElementosNodo as $objInfoElementoNodo)
                {
                    $arrayParametrosAuditoria["strNumeroSerie"] = $objInfoElementoNodo->getSerieFisica();
                    $this->serviceInfoElemento->ingresaAuditoriaElementos($arrayParametrosAuditoria);

                    //Eliminación del elemento actual del cliente.
                    $arrayParametrosEliminar = array();
                    $arrayParametrosEliminar['intIdElemento']  =  $objInfoElementoNodo->getId();
                    $arrayParametrosEliminar['strObservacion'] = 'Eliminación del elemento por cancelación de servicio';
                    $arrayParametrosEliminar['strUsuario']     =  $strUsuario;
                    $arrayParametrosEliminar['strIpUsuario']   =  $strIpUsuario;
                    $arrayResEliminar = $this->serviceInfoElemento->eliminarElementoClienteNodo($arrayParametrosEliminar);
                    if (!$arrayResEliminar['status'])
                    {
                        throw new \Exception('Error : '.$arrayResEliminar['message']);
                    }
                }
            }
        }
    }
    
    /**
     * 
     * Proceso de cancelacion de productos Netlife Wifi para clientes cuyo
     * servicio de internet ha sido cancelado 
     * 
     * @param type $idEmpresa Id Empresa
     * @param type $servicio Objeto Servicio
     * @param type $accion Objeto Accion
     * @param type $usrCreacion Usuario Creacion
     * @param type $ipCreacion IP Creacion
     * 
     * @author  Veronica Carrasco Idrovo <vcarrasco@telconet.ec>
     * @version 1.0 30-06-2016
     * 
     * @author  Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 07-10-2019 Se modifica proceso para utilizar nuevo esquema Netlifezone
     * 
     */
    public function cancelarServiciosNetlifeWifi($arrayParametros)
    {
        $strEstado      = "Cancel";
        $strObservacion = "Netlife Zone: Se cancelo el servicio.";
        $strMetodoWs    = "delete_user";
        $intIdEmpresa   = $arrayParametros['intIdEmpresa'];
        $strUsrCreacion = $arrayParametros['strUsrCreacion'] ;
        $strIpCreacion  = $arrayParametros['strIpCreacion'];
        $arrayServiciosNetlifezone = $arrayParametros['arrayServiciosNetlifezone'];

        //Cancelamos los servicios NETWIFI
        foreach ($arrayServiciosNetlifezone as $objServicioNetwifi)
        {
            $arrayParametrosOperaciones = array();
            $arrayParametrosOperaciones['intIdEmpresa']   = $intIdEmpresa;
            $arrayParametrosOperaciones['intIdServicio']  = $objServicioNetwifi->getId();
            $arrayParametrosOperaciones['strUsuario']     = $strUsrCreacion;
            $arrayParametrosOperaciones['strIpCliente']   = $strIpCreacion;
            $arrayParametrosOperaciones['strEstado']      = $strEstado;
            $arrayParametrosOperaciones['strObservacion'] = $strObservacion;
            $arrayParametrosOperaciones['strMetodoWs']    = $strMetodoWs;
            $this->wifiNetlife->procesarOperacionesNetlifeWifi($arrayParametrosOperaciones);
        }
    }
    
    /**
     * Metodo encargado de realizar la cancelacion del Servicio Backup ligado a su enlace Principal
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0
     * @since 16-12-2016
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 - Se valida que si no tiene Backups se verifique que el Array efectivamente venga vacio con ( empty )
     * @since 05-06-2017
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @since 24-05-2018
     * @version 1.2 - Se envia descripcion de acuerdo a la Ultima milla del servicio para identificacion de NW
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.3 - Se agrega el id del servicio a la url 'configSW' del ws de networking para la validación del BW
     * @since 01-06-2020
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.4 - Se agrega validacion para envio de opcion configSW si existe un servicio que sale de otro equipo
     *                con otro puerto
     * @since 04-03-2022
     *
     * @param  Array $arrayParametros [
     *                                   objServicio              Servicio Principal
     *                                   intCapacidadUno          Capacidad que se resta al concentrador
     *                                   intCapacidadDos          Capacidad que se resta al concentrador
     *                                   intIdPersonaEmpresaRol   Persona empresa rol del cliente
     *                                   strUsrCreacion           Usuario de creacion de requerimiento
     *                                   strIpCreacion            Ip de creacion de requerimiento
     *                                   objMotivo                Motivo de cancelacion del servicio
     *                                   objAccion                Accion ligada a la cancelacion del servicio
     *                                   strCodEmpresa            Empresa a la que pertenece el punto
     *                                ]
     * @return Array $arrayRespuesta [ strStatus , strMensaje ]
     */
    private function cancelarServicioBackup($arrayParametros)
    {
        $arrayRespuesta    = array();
        
        $arrayServiciosBackup = $this->servicioGeneral->getServiciosBackupByServicioPrincipal($arrayParametros['objServicio']);
        
        if(isset($arrayServiciosBackup) && !empty($arrayServiciosBackup))
        {
            foreach($arrayServiciosBackup as $arrayServicios)
            {
                $objServicioBackup  = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($arrayServicios['id']);
                
                $this->serviceUtil->validarVariable($objServicioBackup,'Información del Servicio Backup');
                
                if($objServicioBackup->getEstado() == 'Activo' || $objServicioBackup->getEstado() == 'EnPruebas' || 
                   $objServicioBackup->getEstado() == 'In-Corte')
                {
                    $objServicioTecnico   = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                              ->findOneByServicioId($objServicioBackup->getId());

                    $this->serviceUtil->validarVariable($objServicioTecnico,'Información Técnica de Servicio Backup');
                    
                    $objProducto          = $objServicioBackup->getProductoId();

                    $objInterfaceElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                    ->find($objServicioTecnico->getInterfaceElementoId());
                    $objElemento          = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                    ->find($objServicioTecnico->getElementoId());

                    $objDetalleAnillo     = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                    ->findOneBy(array(  "elementoId"    => $objElemento->getId(),
                                                                                        "detalleNombre" => "ANILLO",
                                                                                        "estado"        => "Activo"));

                    $this->serviceUtil->validarVariable($objDetalleAnillo,'Información del Anillo del Servicio Backup');
                    $this->serviceUtil->validarVariable($objInterfaceElemento,'Información del puerto del Switch del Servicio Backup');
                    $this->serviceUtil->validarVariable($objElemento,'Información del Switch del Servicio Backup');

                    $strVlan          = $this->servicioGeneral->obtenerVlanServicio($objServicioBackup);

                    $strMacServicio   = $this->servicioGeneral->getMacPorServicio($objServicioBackup->getId());

                    $arrayMacServicio[] = $strMacServicio;
                    $arrayMacVlan       = array($strVlan => $arrayMacServicio);
                    
                    //consultar el puerto del switch del servicio para saber si
                    //tiene mas servicios asociados ese puerto
                    $strCancel = 'NO';

                    $arrayParamDetConfigSW = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->getOne('PARAM_CANCELACION_CONFIGSW',
                                                                'TECNICO',
                                                                '',
                                                                '',
                                                                '',
                                                                '',
                                                                '',
                                                                '',
                                                                '',
                                                                $arrayParametros['strCodEmpresa']);

                    if(isset($arrayParamDetConfigSW['valor1']) && !empty($arrayParamDetConfigSW['valor1']))
                    {
                        $strCancel = $arrayParamDetConfigSW['valor1'];
                    }

                    if ($strCancel == 'SI')
                    {
                        //llamamos a la funcion para validar el puerto del servicio
                        $arrayParametrosCancel = array('objServicio'    => $objServicioBackup);
                        $boolFlagCancel = $this->validarPuertoPorServicio($arrayParametrosCancel);
                    }
                    
                    $objAdmiTipoMedio = $this->emInfraestructura->getRepository("schemaBundle:AdmiTipoMedio")
                                                                    ->find($objServicioTecnico->getUltimaMillaId());

                    if(is_object($objAdmiTipoMedio))
                    {
                        $strUltimaMilla = $objAdmiTipoMedio->getNombreTipoMedio();
                    }

                    $strDescripcion = '';

                    if($strUltimaMilla == 'Fibra Optica')
                    {
                        $strDescripcion = '_fib';
                    }
                    if($strUltimaMilla == 'Radio')
                    {
                        $strDescripcion = '_rad';
                    }
                    if($strUltimaMilla == 'UTP')
                    {
                        $strDescripcion = '_utp';
                    }
                    
                    //se ejecuta la desconfiguracion del sw
                    if($boolFlagCancel)
                    {
                        //accion a ejecutar
                        $arrayPeticiones['url']          = 'configSW';

                        if($objServicioBackup->getdescripcionpresentafactura() == 'CANAL TELEFONIA')
                        {
                            $arrayPeticiones['servicio'] = 'NETVOICE-L3MPLS';      
                        }
                        else
                        {             
                            $arrayPeticiones['servicio'] = $objProducto->getNombreTecnico();
                        }

                        $arrayPeticiones['accion']       = 'cancelar';  
                        $arrayPeticiones['id_servicio']  = $objServicioBackup->getId();
                        $arrayPeticiones['nombreMetodo'] = 'InfoCancelarServicioService.cancelarServicioTn';
                        $arrayPeticiones['sw']           = $objElemento->getNombreElemento();
                        $arrayPeticiones['anillo']       = $objDetalleAnillo->getDetalleValor();
                        $arrayPeticiones['macVlan']      = $arrayMacVlan;
                        $arrayPeticiones['user_name']    = $arrayParametros['strUsrCreacion'];
                        $arrayPeticiones['user_ip']      = $arrayParametros['strIpCreacion'];   
                        $arrayPeticiones['bw_up']        = $arrayParametros['intCapacidadUno'];
                        $arrayPeticiones['bw_down']      = $arrayParametros['intCapacidadDos'];
                        $arrayPeticiones['login_aux']    = $objServicioBackup->getLoginAux();
                        $arrayPeticiones['descripcion']  = 'cce_'.$objServicioBackup->getLoginAux().$strDescripcion;
                        $arrayPeticiones['pto']          = $objInterfaceElemento->getNombreInterfaceElemento();
                                                
                        //Ejecucion del metodo via WS para realizar la configuracion del SW
                        $arrayRespuesta = $this->networkingScripts->callNetworkingWebService($arrayPeticiones);

                        $strSatus       = $arrayRespuesta['status'];
                        $strMensaje     = $arrayRespuesta['mensaje'];
                    }
                                        
                    //obtener cpe del servicio
                    $objElementoCpe     = $this->getElementoCpeServicioTn($objServicioTecnico);

                    //validar si otro servicio usa el mismo cpe
                    if(is_object($objElementoCpe))
                    {
                        $arrayParametrosCpe = array('objServicio'    => $objServicioBackup,
                                                    'objElementoCpe' => $objElementoCpe);
                        $booleanCpe         = $this->validarCpePorServicio($arrayParametrosCpe);
                    }

                    //se ejecuta la desconfiguracion del sw
                    if($booleanCpe && !$boolFlagCancel)
                    {
                        //accion a ejecutar
                        $arrayPeticiones['url']          = 'configSW';
                        $arrayPeticiones['accion']       = 'cancelar';                
                        $arrayPeticiones['id_servicio']  = $objServicioBackup->getId();
                        $arrayPeticiones['nombreMetodo'] = 'InfoCancelarServicioService.cancelarServicioBackup';
                        $arrayPeticiones['sw']           = $objElemento->getNombreElemento();
                        $arrayPeticiones['anillo']       = $objDetalleAnillo->getDetalleValor();
                        $arrayPeticiones['macVlan']      = $arrayMacVlan;
                        $arrayPeticiones['user_name']    = $arrayParametros['strUsrCreacion'];
                        $arrayPeticiones['user_ip']      = $arrayParametros['strIpCreacion'];     
                        $arrayPeticiones['bw_up']        = $arrayParametros['intCapacidadUno'];
                        $arrayPeticiones['bw_down']      = $arrayParametros['intCapacidadDos'];    
                        $arrayPeticiones['servicio']     = $objProducto->getNombreTecnico();
                        $arrayPeticiones['login_aux']    = $objServicioBackup->getLoginAux();
                        $arrayPeticiones['descripcion']  = 'cce_'.$objServicioBackup->getLoginAux().$strDescripcion;
                        $arrayPeticiones['pto']          = $objInterfaceElemento->getNombreInterfaceElemento();

                        //Ejecucion del metodo via WS para realizar la configuracion del SW
                        $arrayRespuesta = $this->networkingScripts->callNetworkingWebService($arrayPeticiones);

                        $strSatus      = $arrayRespuesta['status'];
                        $strMensaje    = $arrayRespuesta['mensaje'];
                    }
                    else
                    {
                        $strSatus     = "OK";
                        $strMensaje   = "OK";
                    }

                    if($strSatus == "OK")
                    {
                        //obtener flag para ver si existen servicios con los mismos datos tecnicos
                        $arrayParametrosUm = array( 'objServicio'       => $objServicioBackup,
                                                    'objServicioTecnico'=> $objServicioTecnico);

                        $booleanUm         = $this->validarUmPorServicio($arrayParametrosUm);

                        $arrayParametrosCancelacion = array();

                        $arrayParametrosCancelacion['objServicio']                     = $objServicioBackup;
                        $arrayParametrosCancelacion['idPersonaEmpresaRol']             = $arrayParametros['intIdPersonaEmpresaRol'];
                        $arrayParametrosCancelacion['usrCreacion']                     = $arrayParametros['strUsrCreacion'];
                        $arrayParametrosCancelacion['ipCreacion']                      = $arrayParametros['strIpCreacion'];
                        $arrayParametrosCancelacion['objServicioTecnico']              = $objServicioTecnico;
                        $arrayParametrosCancelacion['interfaceEstado']                 = "not connected";
                        $arrayParametrosCancelacion['interfaceElementoConectorId']     = $objServicioTecnico->getInterfaceElementoConectorId();
                        $arrayParametrosCancelacion['objMotivo']                       = $arrayParametros['objMotivo'];
                        $arrayParametrosCancelacion['objAccion']                       = $arrayParametros['objAccion'];
                        $arrayParametrosCancelacion['flagCpe']                         = $booleanCpe;
                        $arrayParametrosCancelacion['flagUm']                          = $booleanUm;
                        $arrayParametrosCancelacion['flagEsBackup']                    = true;
                        $arrayParametrosCancelacion['strLoginAuxPrincipal']            = $arrayParametros['objServicio']->getLoginAux();

                        //eliminar rutas del servicio
                        $arrayRutas = $this->emInfraestructura->getRepository("schemaBundle:InfoRutaElemento")
                                                              ->findBy(array( "servicioId"    => $objServicioBackup->getId(),
                                                                              "estado"        => "Activo"));
                        foreach($arrayRutas as $objRuta)
                        {
                            $objRuta->setEstado("Eliminado");
                            $this->emInfraestructura->persist($objRuta);
                            $this->emInfraestructura->flush();
                        }

                        if($objProducto->getNombreTecnico()=="L3MPLS")
                        {
                            //desconfigurar del pe [objProducto, objServicio, objElemento]
                            $arrayParametrosPe = array  (
                                                            'objProducto'   => $objProducto,
                                                            'objServicio'   => $objServicioBackup,
                                                            'objElemento'   => $objElemento,
                                                            'usrCreacion'   => $arrayParametros['strUsrCreacion'],
                                                            'ipCreacion'    => $arrayParametros['strIpCreacion']
                                                        );

                            $arrayRespuestaPe = $this->desconfigurarServicioPe($arrayParametrosPe);

                            if($arrayRespuestaPe['status'] != "OK")
                            {
                                $this->serviceUtil->lanzarExcepcion('NETWORKING',$arrayRespuestaPe['mensaje']);
                            }
                        }

                        if($booleanCpe)
                        {
                            //generar la solicitud de retiro de equipo
                            $this->generarSolicitudRetiroEquipo($arrayParametrosCancelacion);
                            $this->eliminarElementoCliente($arrayParametrosCancelacion);
                        }

                        //liberar datos de backbone
                        $this->generarCancelacionDatosBackboneConexion($arrayParametrosCancelacion);

                        //eliminar datos de servicio, punto, cliente
                        $this->generarCancelacionDatosComerciales($arrayParametrosCancelacion);

                        $arrayRespuesta = array('strStatus' => 'OK' , 'strMensaje' => 'OK');

                    }
                    else
                    {
                        $arrayRespuesta = array('strStatus' => 'ERROR' , 'strMensaje' => $strMensaje);
                    }       
                }
                else
                {
                    //Se Cancela logicamente los Servicios Backups dependientes del Principal de manera logica
                    $arrayParametrosCancelacion                                    = array();
                    $arrayParametrosCancelacion['objServicio']                     = $objServicioBackup;
                    $arrayParametrosCancelacion['idPersonaEmpresaRol']             = $arrayParametros['intIdPersonaEmpresaRol'];
                    $arrayParametrosCancelacion['usrCreacion']                     = $arrayParametros['strUsrCreacion'];
                    $arrayParametrosCancelacion['ipCreacion']                      = $arrayParametros['strIpCreacion'];
                    $arrayParametrosCancelacion['objMotivo']                       = $arrayParametros['objMotivo'];
                    $arrayParametrosCancelacion['objAccion']                       = $arrayParametros['objAccion'];
                    $arrayParametrosCancelacion['flagEsBackup']                    = true;
                    
                    //liberar datos de backbone
                    $this->generarCancelacionDatosBackboneConexion($arrayParametrosCancelacion);

                    //eliminar datos de servicio, punto, cliente
                    $this->generarCancelacionDatosComerciales($arrayParametrosCancelacion);

                    $arrayRespuesta = array('strStatus' => 'OK' , 'strMensaje' => 'OK');
                }
            }
        }
        else //Si no contiene servicio backup y continua el flujo normalmente
        {
            $arrayRespuesta = array('strStatus' => 'OK' , 'strMensaje' => 'OK');
        }
        
        return $arrayRespuesta;
    }
    
    /**
     * Funcion que sirve para la ejecucion de cancelacion de servicios de INTERNET SMALL BUSINESS para TN.
     *          - Se agrega validacion de MIDDLEWARE.
     *          - Se envia parametro prefijo empresa al ldap.
     *          - Se añade nuevo parametro de envio al servicio de RDA [empresa]. Si el producto es "INTERNET SMALL BUSINESS" 
     *            se enviará 'TN' caso contrario 'MD'.
     * 
     * 
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.0 10-12-2017
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 09-03-2018 Se agrega la cancelación por proceso masivo
     *      
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 15-05-2018 Se agrega flujo para cancelar servicios Small Business y sus Ips Adicionales
     *
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.3 22-05-2018 Se agregan los estados: "Anulada","Eliminado","Eliminada","Eliminado-Migra","Trasladado" 
     * en las condiciones de los estados de los servicios para la correcta Cancelacion a nivel Comercial de estados de Clientes y Contratos, debido a que reportan
     * "ERROR EN ESTATUS - REPORTE DE CARTERA"
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 23-05-2018 Se obtiene el line profile para servicios Small Business con OLTs TELLION 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.4 19-07-2018 Se realizan ajustes para reutilizar la función en caso de cancelación por traslado de un 
     *                         servicio Small Business. Además se modifican las observaciones de acuerdo al parámetro strOpcion que especifica el
     *                         proceso que se está realizando al invocar a ésta función.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.5 11-02-2019 Se agrega flujo para cancelación de servicios TelcoHome con sus ips adicionales
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.6 03-05-2020 Se elimina la función obtenerInfoMapeoProdPrefYProdsAsociados y en su lugar se usa obtenerParametrosProductosTnGpon,
     *                          debido a los cambios realizados por la reestructuración de servicios Small Businesss y TelcoHome
     * 
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.7 08-07-2020 - Actualización: Se agrega bloque de código para actualizar datos de tareas en nueva tabla DB_SOPORTE.INFO_TAREA
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.8 30-07-2020 - Se realiza la identificación por marca del equipo para obtener las características
     *                           para ejecutar la cancelación del servicio.
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.9 22-02-2021 - Se consulta el tipo de Ip del servicio.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 2.0 11-07-2022 Se agrega la validación de la caracteristica del servicio principal INTERNET VPNoGPON,
     *                         para obtener los servicios de las ip asociadas al servicio principal.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 2.1 01-08-2022 - Se agrega la validación para cancelar los servicios adicionales safecity del servicio principal INTERNET VPNoGPON.
     *
     * @author Liseth Candelario <lcandelario@telconet.ec>
     * @version 2.2 22-07-2022 Se llama a una función para actualizar el estado de UM en ARCGIS
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 2.3 07-10-2022 Se valida la variable $intIdPorducto para obtener el objeto del producto.
     * 
     * @author Leonardo Mero <lemero@telconet.ec>
     * @version 2.4 09-12-2023 Se agrega la validacion para realizar la cancelacion del producto SAFE ENTRY si el punto posee ese servicio
     *
     * @param $arrayParametros  [servicio, servicioTecnico, modeloElemento, producto, elemento, interfaceElemento, login, idEmpresa, 
     *                          usrCreacion, ipCreacion, motivo, idPersonaEmpresaRol, accion]
     * 
     */
    public function cancelarServicioIsb( $arrayParametros )
    {
        $objServicio                = $arrayParametros['servicio'];
        $strLogin                   = $arrayParametros['login'];
        $intIdEmpresa               = $arrayParametros['idEmpresa'];
        $strUsrCreacion             = $arrayParametros['usrCreacion'];
        $strIpCreacion              = $arrayParametros['ipCreacion'];
        $objMotivo                  = $arrayParametros['motivo'];
        $intIdPersonaEmpresaRol     = $arrayParametros['idPersonaEmpresaRol'];
        $intIdDepartamento          = $arrayParametros['intIdDepartamento'];
        $strOpcionProceso           = $arrayParametros["strOpcion"] ? $arrayParametros["strOpcion"] : "";
        $intDetalleId               = null;
        $arrayProdIPSB              = array();
        $arrayIdsProdsIps           = array();
        try
        {
            if(is_object($objServicio))
            {
                $intIdServicio = $objServicio->getId();
            }
            else
            {
                $intIdServicio  = $arrayParametros['idServicio'] > 0 ? $arrayParametros['idServicio'] : 0;
            }
            
            //Se verifica si existe alguna solicitud de cancelación
            $arrayResultadoSolicCancelacion = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                ->getArrayInfoCambioPlanPorSolicitud(array( "idServicio"        => $intIdServicio,
                                                                                                            "tipoProceso"       => "",
                                                                                                            "strTipoSolicitud"  => "CANCELACION"));
            if(isset($arrayResultadoSolicCancelacion) && !empty($arrayResultadoSolicCancelacion))
            {
                $intIdSolicitudCancelacion      = $arrayResultadoSolicCancelacion['idSolicitud'];
                $intIdSolicitudCancelacionPadre = $arrayResultadoSolicCancelacion['idSolicitudPadre'];

                $objDetalleSolicitudCancelacion = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                    ->find($intIdSolicitudCancelacion);
            }
            
            if(isset($strOpcionProceso) && !empty($strOpcionProceso) && $strOpcionProceso === "ProcesoMasivo")
            {
                $intIdProducto  = $arrayParametros['idProducto'] > 0 ? $arrayParametros['idProducto'] : 0;
                if($intIdProducto === 0)
                {
                    throw new \Exception("No se ha enviado el id del producto. <br>Por favor Revisar!");
                }
                $objProducto    = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($intIdProducto);

                $intIdAccion    = $arrayParametros['idAccion'] > 0 ? $arrayParametros['idAccion'] : 0;
                if($intIdAccion === 0)
                {
                    throw new \Exception("No se ha enviado el id de la acción. <br>Por favor Revisar!");
                }
                $objAccion  = $this->emSeguridad->getRepository('schemaBundle:SistAccion')->find($intIdAccion);

                if(!is_object($objProducto) || !is_object($objAccion))
                {
                    throw new \Exception("No se ha podido obtener el producto o la acción para cancelar. <br>Por favor Revisar!");
                }

                if($intIdServicio === 0)
                {
                    throw new \Exception("No se ha enviado el id del servicio. <br>Por favor Revisar!");
                }

                $objServicio        = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
                $objServicioTecnico = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                        ->findOneBy(array( "servicioId" => $intIdServicio));
                if(!is_object($objServicio) || !is_object($objServicioTecnico))
                {
                    throw new \Exception("No se ha podido obtener el servicio o el servicio técnico. <br>Por favor Revisar!");
                }

                $intIdInterfaceElementoOlt  = $objServicioTecnico->getInterfaceElementoId();
                if(empty($intIdInterfaceElementoOlt))
                {
                    throw new \Exception("No se ha podido obtener la interface del OLT. <br>Por favor Revisar!");
                }

                $objInterfaceElemento    = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                   ->find($intIdInterfaceElementoOlt);
                if(!is_object($objInterfaceElemento))
                {
                    throw new \Exception("No se ha podido obtener la interface del OLT. <br>Por favor Revisar!");
                }

                $objElemento = $objInterfaceElemento->getElementoId();
                if(!is_object($objElemento))
                {
                    throw new \Exception("No se ha podido obtener el OLT. <br>Por favor Revisar!");
                }

                $objModeloElemento = $objElemento->getModeloElementoId();
                if(!is_object($objModeloElemento))
                {
                    throw new \Exception("No se ha podido obtener el modelo del OLT. <br>Por favor Revisar!");
                }
            }
            else
            {
                $objServicioTecnico        = $arrayParametros['servicioTecnico'];
                $objModeloElemento         = $arrayParametros['modeloElemento'];
                $objProducto               = $arrayParametros['producto'];
                $objElemento               = $arrayParametros['elemento'];
                $objInterfaceElemento      = $arrayParametros['interfaceElemento'];
                $objAccion                 = $arrayParametros['accion'];

                if(!is_object($objServicio) || !is_object($objServicioTecnico) || !is_object($objModeloElemento) || !is_object($objProducto) 
                    || !is_object($objElemento) || !is_object($objInterfaceElemento) || !is_object($objAccion) )
                {
                    throw new \Exception("No se han enviado correctamente los parámetros. <br>Por favor Revisar!");
                }
            }
            
            $strNombreTecnicoProdPref   = $objProducto->getNombreTecnico();
            $objIpElemento              = $this->emComercial->getRepository('schemaBundle:InfoIp')
                                                            ->findOneBy(array(  "elementoId"    => $objElemento->getId(), 
                                                                                "estado"        => "Activo"));
            $objDetalleElemento         = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                  ->findOneBy(array("elementoId"    => $objServicioTecnico->getElementoId(),
                                                                                    "detalleNombre" => 'MIDDLEWARE',
                                                                                    "estado"        => 'Activo'));

            $strEjecutaLdap            = "NO";
            $boolMiddleware         = false;

            if(is_object($objDetalleElemento))
            {
                if($objDetalleElemento->getDetalleValor() == 'SI')
                {
                    $boolMiddleware = true;
                }
            }

            if(!$boolMiddleware)
            {
                throw new \Exception("No se pudo Cancelar el Servicio - OLT sin middleware");
            }             

            if($boolMiddleware)
            {
                $strEjecutaLdap         = "SI";
                $strLineProfile         = '';            
                $strVlan                = '';
                $strServiceProfile      = '';
                $strGemPort             = '';
                $strTrafficTable        = '';
                $strMacOnt              = '';
                $strIndiceCliente       = '';
                $strIpFija              = '';
                $strSpid                = '';
                $strMacWifi             = '';
                $strCapacidad1          = "";
                $strCapacidad2          = "";

                //OBTENER DATOS DEL CLIENTE (NOMBRES E IDENTIFICACION)
                $objPersona         = $objServicio->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId();
                $strIdentificacion  = $objPersona->getIdentificacionCliente();
                $strNombreCliente   = ($objPersona->getRazonSocial() != "") ? $objPersona->getRazonSocial() : 
                                                                              $objPersona->getNombres()." ".$objPersona->getApellidos();

                //OBTENER SERIE ONT
                $intIdElementoCliente  = $objServicioTecnico->getElementoClienteId();
                $objElementoCliente    = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($intIdElementoCliente);
                $strSerieOnt           = $objElementoCliente->getSerieFisica();
                
                $objServProdCaracTipoNegocio = $this->servicioGeneral->getServicioProductoCaracteristica(   $objServicio,
                                                                                                            "Grupo Negocio",
                                                                                                            $objProducto);
                if(is_object($objServProdCaracTipoNegocio))
                {
                    $strValorTipoNegocioProd = $objServProdCaracTipoNegocio->getValor();
                    list($strTipoNegocioProd) = explode("TN",$strValorTipoNegocioProd);
                }
                else
                {
                    throw new \Exception("No existe Caracteristica Grupo Negocio");
                }

                //OBTENER MAC ONT
                $spcMacOnt = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "MAC ONT", $objProducto);
                if(is_object($spcMacOnt))
                {
                    $strMacOnt = $spcMacOnt->getValor();
                }

                //OBTENER INDICE CLIENTE
                $objSpcIndiceCliente = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "INDICE CLIENTE", $objProducto);
                if(is_object($objSpcIndiceCliente))
                {
                    $strIndiceCliente = $objSpcIndiceCliente->getValor();
                }

                $strMarcaOlt = $objModeloElemento->getMarcaElementoId()->getNombreMarcaElemento();
                //OBTENER CARACTERISTICAS PARA HUAWEI, TELLION y ZTE
                if($strMarcaOlt == "HUAWEI")
                {
                    //obtener line profile
                    $objSpcLineProfile = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "LINE-PROFILE-NAME", $objProducto);
                    if(is_object($objSpcLineProfile))
                    {
                        $strLineProfile = $objSpcLineProfile->getValor();
                    }

                    //obtener service profile
                    $objSrvProfileProdCaract = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio,"SERVICE-PROFILE",$objProducto);
                    if(is_object($objSrvProfileProdCaract))
                    {
                        $strServiceProfile = $objSrvProfileProdCaract->getValor();
                    }

                    //obtener vlan
                    $objVlanProdCaract = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "VLAN", $objProducto);
                    if(is_object($objVlanProdCaract))
                    {
                        $strVlan = $objVlanProdCaract->getValor();                
                    }

                    //obtener gemport
                    $objGemPortProdCaract = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "GEM-PORT", $objProducto);
                    if(is_object($objGemPortProdCaract))
                    {
                        $strGemPort = $objGemPortProdCaract->getValor();
                    }

                    //obtener traffic table
                    $objTrafficTableProdCaract = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio,"TRAFFIC-TABLE",$objProducto);
                    if(is_object($objTrafficTableProdCaract))
                    {
                        $strTrafficTable = $objTrafficTableProdCaract->getValor();
                    }

                    //obtener service-port
                    $objSpcServicePort = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "SPID", $objProducto);
                    if(is_object($objSpcServicePort))
                    {
                        $strSpid = $objSpcServicePort->getValor();
                    }            
                }
                else if($strMarcaOlt == "TELLION")
                {
                    $objSpcLineProfile = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "PERFIL", $objProducto);
                    if(is_object($objSpcLineProfile))
                    {
                        $strLineProfile = $objSpcLineProfile->getValor();
                        $arrayPerfil    = explode("_", $strLineProfile);
                        $strLineProfile = $arrayPerfil[0]."_".$arrayPerfil[1]."_".$arrayPerfil[2];
                    }

                    //OBTENER MAC WIFI
                    $objSpcMacWifi = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "MAC WIFI", $objProducto);
                    if(is_object($objSpcMacWifi))
                    {
                        $strMacWifi = $objSpcMacWifi->getValor();
                    }
                }
                else if($strMarcaOlt == "ZTE")
                {
                    //obtener line profile
                    $objProfileProdCaract = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio,"SERVICE-PROFILE",$objProducto);
                    if( is_object($objProfileProdCaract) )
                    {
                        $strServiceProfile = $objProfileProdCaract->getValor();
                    }
                    //obtener service-port
                    $objSpcServicePort = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "SPID", $objProducto);
                    if( is_object($objSpcServicePort) )
                    {
                        $strSpid = $objSpcServicePort->getValor();
                    }
                    //obtener capacidad uno
                    $objCaractCapacidadUno = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "CAPACIDAD1", $objProducto);
                    if( is_object($objCaractCapacidadUno) )
                    {
                        $strCapacidad1 = $objCaractCapacidadUno->getValor();
                    }
                    //obtener capacidad dos
                    $objCaractCapacidadDos = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "CAPACIDAD2", $objProducto);
                    if( is_object($objCaractCapacidadDos) )
                    {
                        $strCapacidad2 = $objCaractCapacidadDos->getValor();
                    }
                }
                else
                {
                    throw new \Exception("No existe flujo para la marca ".$strMarcaOlt);
                }
                                
                if($strNombreTecnicoProdPref === "INTERNET SMALL BUSINESS")
                {
                    //Se verififca si el punto posee el servicio SAFE ENTRY para proceder con la cancelacion
                    $boolCancelacionSafeEntry = isset($arrayParametros['boolCancelacionSafeEntry'])
                                                ? $arrayParametros['boolCancelacionSafeEntry'] : false;
                    if(!$boolCancelacionSafeEntry)
                    {
                        $arrayParametrosVerificar = array(
                            'objServicio' => $objServicio,
                            'objProducto' => $objProducto,
                            'objMotivo'   => $objMotivo,
                            'strEmpresaCod'   => $intIdEmpresa,
                            'intIdAccion' => $arrayParametros['idAccion'],
                            'strPrefijoEmpresa' => $arrayParametros['strPrefijoEmpresaOrigen'],
                            'departamentoId' => $intIdDepartamento,
                            'idPersonaEmpresaRol' => $intIdPersonaEmpresaRol,
                            'ipCreacion' => $strIpCreacion,
                            'usrCreacion' => $strUsrCreacion
                        );
                        $arrayRespuesta = $this->verificarServicioSafeEntryCancelar($arrayParametrosVerificar);
                        if($arrayRespuesta['status'] != 'OK')
                        {
                            throw new Exception ($arrayRespuesta['mensaje']);
                        }
                    }
                    $arrayParamsInfoProds       = array("strValor1ParamsProdsTnGpon"    => "PRODUCTOS_RELACIONADOS_INTERNET_IP",
                                                        "strCodEmpresa"                 => $intIdEmpresa,
                                                        "intIdProductoInternet"         => $objProducto->getId());
                    $arrayInfoMapeoProds        = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                    ->obtenerParametrosProductosTnGpon($arrayParamsInfoProds);
                    if(isset($arrayInfoMapeoProds) && !empty($arrayInfoMapeoProds))
                    {
                        foreach($arrayInfoMapeoProds as $arrayInfoProd)
                        {
                            $intIdProdIp        = $arrayInfoProd["intIdProdIp"];
                            $strCaractRelProdIp = $arrayInfoProd["strCaractRelProdIp"];
                            $arrayProdIPSB[]    = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($intIdProdIp);
                            $arrayIdsProdsIps[] = $intIdProdIp;
                        }
                    }
                    else
                    {
                        throw new \Exception("No se ha podido obtener el correcto mapeo del servicio con la ip respectiva");
                    }
                    
                    if(empty($arrayProdIPSB))
                    {
                        throw new \Exception("No existe el producto IP. <br>Por favor Revisar!");
                    }
                    
                    //OBTENER IP DEL PLAN
                    $objIpFija  = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                          ->findOneBy(array("servicioId"    => $objServicio->getId(),
                                                                            "estado"        => "Activo"));
                    if(is_object($objIpFija))
                    {
                        $strIpFija  = $objIpFija->getIp();   
                    }

                    //OBTENER SCOPE
                    $objSpcScope = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "SCOPE", $objProducto);

                    if(!is_object($objSpcScope))
                    {
                        //buscar scopes
                        $arrayScopeOlt = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')
                                                                 ->getScopePorIpFija($strIpFija, $objServicioTecnico->getElementoId());

                        if (!$arrayScopeOlt)
                        {   
                            throw new \Exception("Ip Fija no pertenece a un Scope! <br> Favor Comunicarse con el Dep. Gepon!");
                        }

                        $strScope = $arrayScopeOlt['NOMBRE_SCOPE'];
                    }
                    else
                    {
                        $strScope = $objSpcScope->getValor();
                    }
                    
                    //OBTENER SERVICIOS DEL PUNTO
                    if(isset($strCaractRelProdIp) && !empty($strCaractRelProdIp) &&
                       isset($intIdProdIp) && !empty($intIdProdIp))
                    {
                        $arrayServicios     = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                            ->createQueryBuilder('s')
                            ->innerJoin('schemaBundle:InfoServicioProdCaract', 'car', 'WITH', 'car.servicioId = s.id')
                            ->innerJoin('schemaBundle:AdmiProductoCaracteristica', 'pc', 'WITH',
                                    'pc.id = car.productoCaracterisiticaId')
                            ->innerJoin('schemaBundle:AdmiCaracteristica', 'c', 'WITH', 'c.id = pc.caracteristicaId')
                            ->where('s.puntoId = :puntoId')
                            ->andWhere("s.productoId = :productoId")
                            ->andWhere("car.valor = :idServioInt")
                            ->andWhere("c.descripcionCaracteristica = :desCaracteristica")
                            ->andWhere("car.estado = :estadoActivo")
                            ->setParameter('puntoId', $objServicio->getPuntoId()->getId())
                            ->setParameter('productoId', $intIdProdIp)
                            ->setParameter('idServioInt', $objServicio->getId())
                            ->setParameter('desCaracteristica', $strCaractRelProdIp)
                            ->setParameter('estadoActivo', 'Activo')
                            ->getQuery()
                            ->getResult();
                        $arrayServicios[]   = $objServicio;
                    }
                    else
                    {
                        $arrayServicios     = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                ->findBy(array('puntoId' => $objServicio->getPuntoId()));
                    }

                    //OBTENER IPS ADICIONALES Y CUANTAS IPS
                    $arrayDatosIps      = $this->servicioGeneral->getInfoIpsFijaPunto($arrayServicios, $arrayProdIPSB, $objServicio, 
                                                                                      $objServicio->getEstado(), 'Activo', $objProducto);
                    $intIpFijasActivas  = $arrayDatosIps['ip_fijas_activas'];
                    //SI PUNTO TIENE IPS ADICIONALES
                    if($intIpFijasActivas > 0)
                    {
                        //OBTENER IPS CANCELAR
                        $arrayIpCancelar    = $arrayDatosIps['valores'];                
                    }
                    else
                    {
                        $arrayIpCancelar    = array();
                    }
                }
                else
                {
                    $strIpFija          = "";
                    $intIpFijasActivas  = 0;
                    $strScope           = "";
                    $arrayIpCancelar    = array();
                }
                
                $arrayDatos = array(
                                        'serial_ont'            => $strSerieOnt,
                                        'mac_ont'               => $strMacOnt,
                                        'nombre_olt'            => $objElemento->getNombreElemento(),
                                        'ip_olt'                => $objIpElemento->getIp(),
                                        'puerto_olt'            => $objInterfaceElemento->getNombreInterfaceElemento(),
                                        'modelo_olt'            => $objModeloElemento->getNombreModeloElemento(),
                                        'ont_id'                => $strIndiceCliente,
                                        'service_port'          => $strSpid,
                                        'gemport'               => $strGemPort,
                                        'service_profile'       => $strServiceProfile,
                                        'line_profile'          => $strLineProfile,
                                        'traffic_table'         => $strTrafficTable,
                                        'vlan'                  => $strVlan,
                                        'estado_servicio'       => $objServicioTecnico->getServicioId()->getEstado(),
                                        'ip'                    => $strIpFija,
                                        'ip_fijas_activas'      => $intIpFijasActivas,
                                        'tipo_negocio_actual'   => $strTipoNegocioProd,
                                        'mac_wifi'              => $strMacWifi,
                                        'scope'                 => $strScope,
                                        'ip_cancelar'           => $arrayIpCancelar,
                                        'capacidad_up'          => $strCapacidad1,
                                        'capacidad_down'        => $strCapacidad2
                                    );

                $arrayDatosMiddleware = array(
                                                'nombre_cliente'        => $strNombreCliente,
                                                'login'                 => $strLogin,
                                                'identificacion'        => $strIdentificacion,
                                                'datos'                 => $arrayDatos,
                                                'opcion'                => $this->opcion,
                                                'ejecutaComando'        => $this->ejecutaComando,
                                                'usrCreacion'           => $strUsrCreacion,
                                                'ipCreacion'            => $strIpCreacion,
                                                'empresa'               => 'TN'
                                            );
                $arrayFinal         = $this->rdaMiddleware->middleware(json_encode($arrayDatosMiddleware));
                $strStatus          = $arrayFinal['status'];
                $strMensaje         = $arrayFinal['mensaje'];
            }
            $boolErrorIpAdicional   = false;
            $strMensajeAdicional    = "";
            if($strOpcionProceso === "Traslado")
            {
                $strObservacionSolRetiroEquipo      = "SOLICITA RETIRO DE EQUIPO POR TRASLADO DE SERVICIO";
                $strObservacionSolHistRetiroEquipo  = "GENERACION AUTOMATICA DE SOLICITUD RETIRO DE EQUIPO POR TRASLADO DE SERVICIO";
                $strObservacionHistorialElemento    = "Se elimino el elemento por traslado de Servicio";
                $strEstadoServicioPorProceso        = "Trasladado";
                $strEstadoPuntoPorProceso           = "Trasladado";
                $strObservacionServicioPrincipal    = "Se Traslado el Servicio";
            }
            else
            {
                $strObservacionSolRetiroEquipo      = "SOLICITA RETIRO DE EQUIPO POR CANCELACION DEL SERVICIO";
                $strObservacionSolHistRetiroEquipo  = "GENERACION AUTOMATICA DE SOLICITUD RETIRO DE EQUIPO POR CANCELACION DEL SERVICIO";
                $strObservacionHistorialElemento    = "Se elimino el elemento por cancelacion de Servicio";
                $strEstadoServicioPorProceso        = "Cancel";
                $strEstadoPuntoPorProceso           = "Cancelado";
                $strObservacionServicioPrincipal    = "Se cancelo el Servicio";
            }
            if($intIpFijasActivas > 0 && $strStatus === 'OK')
            {
                $arrayRespuestaIp = $arrayFinal['ip_cancelar'];
                foreach($arrayRespuestaIp as $arrayIpCancelar)
                {
                    if($arrayIpCancelar['status'] === "ERROR")
                    {
                        $boolErrorIpAdicional   = true;
                        $strMensajeAdicional    = $strMensajeAdicional . $arrayIpCancelar['mensaje'] . '<br>';
                    }
                    else
                    {
                        //ELIMINAR IP ADICIONAL
                        $objIpAdicional = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                                  ->findOneBy(array('servicioId' => $arrayIpCancelar['id_servicio']));
                        $objIpAdicional->setEstado('Eliminado');
                        $this->emInfraestructura->persist($objIpAdicional);
                        $this->emInfraestructura->flush();

                        //CANCELAR SERVICIO ADICIONAL
                        $objServicioIpAdicional = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                    ->find($arrayIpCancelar['id_servicio']);
                        $objServicioIpAdicional->setEstado($strEstadoServicioPorProceso);
                        $this->emComercial->persist($objServicioIpAdicional);
                        $this->emComercial->flush();
                        
                        if($strOpcionProceso === "Traslado")
                        {
                            $strObservacionIp = 'Se Traslado el Servicio';
                        }
                        else
                        {
                            $strObservacionIp = "Se cancelo el Servicio <br>".$arrayIpCancelar['mensaje'];
                        }
                        
                        $objServicioHistorialIp = new InfoServicioHistorial();
                        $objServicioHistorialIp->setServicioId($objServicioIpAdicional);
                        $objServicioHistorialIp->setObservacion($strObservacionIp);
                        $objServicioHistorialIp->setMotivoId($objMotivo->getId());
                        $objServicioHistorialIp->setEstado($strEstadoServicioPorProceso);
                        $objServicioHistorialIp->setUsrCreacion($strUsrCreacion);
                        $objServicioHistorialIp->setFeCreacion(new \DateTime('now'));
                        $objServicioHistorialIp->setIpCreacion($strIpCreacion);
                        $objServicioHistorialIp->setAccion($objAccion->getNombreAccion());
                        $this->emComercial->persist($objServicioHistorialIp);
                        $this->emComercial->flush();
                        
                        $strMensaje = $strMensaje . "<br>" . $arrayIpCancelar['mensaje'];
                    }
                }
                
                if($boolErrorIpAdicional)
                {
                    throw new \Exception($strMensaje."<br>".$strMensajeAdicional);
                }
            }
            if($strStatus === "OK")
            {
                //crear solicitud para retiro de equipo (ont y wifi)
                $objTipoSolicitud = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                      ->findOneBy(array( "descripcionSolicitud" => "SOLICITUD RETIRO EQUIPO", "estado"=>"Activo"));

                $objDetalleSolicitud = new InfoDetalleSolicitud();
                $objDetalleSolicitud->setServicioId($objServicio);
                $objDetalleSolicitud->setTipoSolicitudId($objTipoSolicitud);
                $objDetalleSolicitud->setEstado("AsignadoTarea");
                $objDetalleSolicitud->setUsrCreacion($strUsrCreacion);
                $objDetalleSolicitud->setFeCreacion(new \DateTime('now'));
                $objDetalleSolicitud->setObservacion($strObservacionSolRetiroEquipo);
                $this->emComercial->persist($objDetalleSolicitud);

                //crear la caracteristica para la solicitud de retiro de equipo
                $entityAdmiCaracteristica = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')->find(360);

                $arrayParams['interfaceElementoConectorId']    = $objServicioTecnico->getInterfaceElementoConectorId();
                $arrayParams['arrayData']                      = array();
                $arrayElementoRetiroEquipos                    = $this->emInfraestructura
                                                                      ->getRepository('schemaBundle:InfoElemento')
                                                                      ->getElementosClienteByInterface($arrayParams);                        

                foreach($arrayElementoRetiroEquipos as $objElementoServicio)
                {
                    //valor del ont
                    $objEntityCaract= new InfoDetalleSolCaract();
                    $objEntityCaract->setCaracteristicaId($entityAdmiCaracteristica);
                    $objEntityCaract->setDetalleSolicitudId($objDetalleSolicitud);
                    $objEntityCaract->setValor($objElementoServicio->getId());
                    $objEntityCaract->setEstado("AsignadoTarea");
                    $objEntityCaract->setUsrCreacion($strUsrCreacion);
                    $objEntityCaract->setFeCreacion(new \DateTime('now'));
                    $this->emComercial->persist($objEntityCaract);
                    $this->emComercial->flush();
                }

                //obtener tarea
                $entityProceso = $this->emSoporte->getRepository('schemaBundle:AdmiProceso')->findOneByNombreProceso("SOLICITAR RETIRO EQUIPO");
                $entityTareas  = $this->emSoporte->getRepository('schemaBundle:AdmiTarea')->findTareasActivasByProceso($entityProceso->getId());
                $entityTarea   = $entityTareas[0];

                //grabar nuevo info_detalle para la solicitud de retiro de equipo
                $entityDetalle = new InfoDetalle();
                $entityDetalle->setDetalleSolicitudId($objDetalleSolicitud->getId());
                $entityDetalle->setTareaId($entityTarea);
                $entityDetalle->setLongitud($objServicio->getPuntoId()->getLongitud());
                $entityDetalle->setLatitud($objServicio->getPuntoId()->getLatitud());
                $entityDetalle->setPesoPresupuestado(0);
                $entityDetalle->setValorPresupuestado(0);
                $entityDetalle->setIpCreacion($strIpCreacion);
                $entityDetalle->setFeCreacion(new \DateTime('now'));
                $entityDetalle->setUsrCreacion($strUsrCreacion);
                $entityDetalle->setFeSolicitada(new \DateTime('now'));
                $this->emSoporte->persist($entityDetalle);
                $this->emSoporte->flush();

                //obtenemos el persona empresa rol del usuario
                $objPersonaEmpresaRolUsr    = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                ->find($intIdPersonaEmpresaRol);

                //buscamos datos del dept, persona
                $objDepartamento = $this->emGeneral->getRepository('schemaBundle:AdmiDepartamento')
                                                   ->find($objPersonaEmpresaRolUsr->getDepartamentoId());
                $objPersona      = $objPersonaEmpresaRolUsr->getPersonaId();

                //grabamos soporte.info_detalle_asignacion
                $objDetalleAsignacion = new InfoDetalleAsignacion();
                $objDetalleAsignacion->setDetalleId($entityDetalle);
                $objDetalleAsignacion->setAsignadoId($objDepartamento->getId());
                $objDetalleAsignacion->setAsignadoNombre($objDepartamento->getNombreDepartamento());
                $objDetalleAsignacion->setRefAsignadoId($objPersona->getId());

                if($objPersona->getRazonSocial()=="")
                {
                    $strNombre = $objPersona->getNombres()." ".$objPersona->getApellidos();
                }
                else
                {
                    $strNombre = $objPersona->getRazonSocial();
                }

                $objDetalleAsignacion->setRefAsignadoNombre($strNombre);
                $objDetalleAsignacion->setPersonaEmpresaRolId($objPersonaEmpresaRolUsr->getId());
                $objDetalleAsignacion->setUsrCreacion($strUsrCreacion);
                $objDetalleAsignacion->setFeCreacion(new \DateTime('now'));
                $objDetalleAsignacion->setIpCreacion($strIpCreacion);
                $this->emSoporte->persist($objDetalleAsignacion);
                $this->emSoporte->flush();
                
                $arrayParametrosHist                            = array();
                $arrayParametrosHist["strCodEmpresa"]           = $intIdEmpresa;
                $arrayParametrosHist["strUsrCreacion"]          = $strUsrCreacion;
                $arrayParametrosHist["strOpcion"]               = "Historial";
                $arrayParametrosHist["strIpCreacion"]           = $strIpCreacion;          
                $arrayParametrosHist["intIdDepartamentoOrigen"] = $intIdDepartamento;

                //Se ingresa el historial de la tarea
                if(is_object($entityDetalle))
                {
                    $arrayParametrosHist["intDetalleId"] = $entityDetalle->getId();            
                    $intDetalleId                        = $arrayParametrosHist["intDetalleId"];
                }

                $arrayParametrosHist["strObservacion"]  = "Tarea Asignada";
                $arrayParametrosHist["strEstadoActual"] = "Asignada";     
                $arrayParametrosHist["strAccion"]       = "Asignada";

                $this->serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);              

                $strAfectadoNombre = "";
                $intPuntoId        = "";
                $strPuntoLogin     = "";

                if($objServicio->getPuntoId()->getNombrePunto())
                {
                    $strAfectadoNombre = $objServicio->getPuntoId()->getNombrePunto();
                }

                if($objServicio->getPuntoId())
                {
                    $intPuntoId = $objServicio->getPuntoId()->getId();
                    $strPuntoLogin = $objServicio->getPuntoId()->getLogin();
                }

                // se graba en la DB_SOPORTE.INFO_CRITERIO_AFECTADO
                $objCriterio = new InfoCriterioAfectado();
                $objCriterio->setId(1);
                $objCriterio->setDetalleId($entityDetalle);
                $objCriterio->setCriterio("Clientes");
                $objCriterio->setOpcion("Cliente: " . $strAfectadoNombre . " | OPCION: Punto Cliente");
                $objCriterio->setFeCreacion(new \DateTime('now'));
                $objCriterio->setUsrCreacion($strUsrCreacion);
                $objCriterio->setIpCreacion($strIpCreacion);
                $this->emSoporte->persist($objCriterio);
                $this->emSoporte->flush();

                // se graba en la DB_SOPORTE.INFO_PARTE_AFECTADA
                $objAfectado = new InfoParteAfectada();
                $objAfectado->setTipoAfectado("Cliente");
                $objAfectado->setDetalleId($entityDetalle->getId());
                $objAfectado->setCriterioAfectadoId($objCriterio->getId());
                $objAfectado->setAfectadoId($intPuntoId);
                $objAfectado->setFeIniIncidencia(new \DateTime('now'));
                $objAfectado->setAfectadoNombre($strPuntoLogin);
                $objAfectado->setAfectadoDescripcion($strAfectadoNombre);
                $objAfectado->setFeCreacion(new \DateTime('now'));
                $objAfectado->setUsrCreacion($strUsrCreacion);
                $objAfectado->setIpCreacion($strIpCreacion);
                $this->emSoporte->persist($objAfectado);
                $this->emSoporte->flush();

                //crear historial para la solicitud
                $objHistorialSolicitud = new InfoDetalleSolHist();
                $objHistorialSolicitud->setDetalleSolicitudId($objDetalleSolicitud);
                $objHistorialSolicitud->setEstado("AsignadoTarea");
                $objHistorialSolicitud->setObservacion($strObservacionSolHistRetiroEquipo);
                $objHistorialSolicitud->setUsrCreacion($strUsrCreacion);
                $objHistorialSolicitud->setFeCreacion(new \DateTime('now'));
                $objHistorialSolicitud->setIpCreacion($strIpCreacion);
                $this->emComercial->persist($objHistorialSolicitud);
                //------------------------------------------------------------------------------------------------

                if($strNombreTecnicoProdPref === "INTERNET SMALL BUSINESS")
                {
                    //Cancelar Ip del servicio.
                    //eliminar todas las ips que tenia ese servicio
                    $arrayInfoIp = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                           ->findBy(array( "servicioId" => $objServicio->getId()));
                    for($intIndex=0;$intIndex<count($arrayInfoIp);$intIndex++)
                    {
                        $objDatoIp = $arrayInfoIp[$intIndex];

                        $objDatoIp->setEstado("Eliminado");
                        $this->emInfraestructura->persist($objDatoIp);
                    }
                    $this->emInfraestructura->flush();
                }
                //------------------------------------------------------------------------------------------------//
                //Cancelar Servicio.
                $objPunto               = $objServicio->getPuntoId();
                if(isset($strCaractRelProdIp) && !empty($strCaractRelProdIp) &&
                   isset($intIdProdIp) && !empty($intIdProdIp) )
                {
                    $arrayServiciosPunto = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                        ->createQueryBuilder('s')
                        ->innerJoin('schemaBundle:InfoServicioProdCaract', 'car', 'WITH', 'car.servicioId = s.id')
                        ->innerJoin('schemaBundle:AdmiProductoCaracteristica', 'pc', 'WITH',
                                'pc.id = car.productoCaracterisiticaId')
                        ->innerJoin('schemaBundle:AdmiCaracteristica', 'c', 'WITH', 'c.id = pc.caracteristicaId')
                        ->where('s.puntoId = :puntoId')
                        ->andWhere("s.productoId = :productoId")
                        ->andWhere("car.valor = :idServioInt")
                        ->andWhere("c.descripcionCaracteristica = :desCaracteristica")
                        ->andWhere("car.estado = :estadoActivo")
                        ->setParameter('puntoId', $objPunto->getId())
                        ->setParameter('productoId', $intIdProdIp)
                        ->setParameter('idServioInt', $objServicio->getId())
                        ->setParameter('desCaracteristica', $strCaractRelProdIp)
                        ->setParameter('estadoActivo', 'Activo')
                        ->getQuery()
                        ->getResult();
                    $arrayServiciosPunto[] = $objServicio;
                }
                else
                {
                    $arrayServiciosPunto = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                ->findBy(array( "puntoId" => $objPunto->getId()));
                }
                for($intIndiceServicio=0; $intIndiceServicio<count($arrayServiciosPunto); $intIndiceServicio++)
                {
                    $objServicioPunto       = $arrayServiciosPunto[$intIndiceServicio];
                    $strEstadoServicioPunto = $objServicioPunto->getEstado();
                    //Solo se cancelan los servicios Small Business y sus Ips Adicionales
                    if(is_object($objServicioPunto->getProductoId()) 
                        && ($objServicioPunto->getId() === $objServicio->getId()
                            || (isset($arrayIdsProdsIps) && !empty($arrayIdsProdsIps) 
                                && in_array($objServicioPunto->getProductoId()->getId(), $arrayIdsProdsIps))))
                    {
                        if($strEstadoServicioPunto === "Activo" || $strEstadoServicioPunto === "In-Corte" || $strEstadoServicioPunto === "In-Temp")
                        {
                            $objServicioPunto->setEstado($strEstadoServicioPorProceso);
                            $this->emComercial->persist($objServicioPunto);
                            $this->emComercial->flush();
                            
                            //historial del servicio
                            $objServicioPuntoHistorial = new InfoServicioHistorial();
                            $objServicioPuntoHistorial->setServicioId($objServicioPunto);
                            $objServicioPuntoHistorial->setObservacion($strObservacionServicioPrincipal);
                            $objServicioPuntoHistorial->setMotivoId($objMotivo->getId());
                            $objServicioPuntoHistorial->setEstado($strEstadoServicioPorProceso);
                            $objServicioPuntoHistorial->setUsrCreacion($strUsrCreacion);
                            $objServicioPuntoHistorial->setFeCreacion(new \DateTime('now'));
                            $objServicioPuntoHistorial->setIpCreacion($strIpCreacion);
                            $objServicioPuntoHistorial->setAccion($objAccion->getNombreAccion());
                            $this->emComercial->persist($objServicioPuntoHistorial);
                            $this->emComercial->flush();
                            if($objServicioPunto->getId() === $objServicio->getId())
                            {
                                $objServicioHistorialPrincipal = $objServicioPuntoHistorial;
                            }

                            //eliminar las caracteristicas del servicio
                            $arrayServProdCaractServicioPunto   = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                                    ->findBy(array("servicioId" => $objServicioPunto->getId(), 
                                                                                                   "estado"     => "Activo"));
                            for($intIndexServProdCaract=0;$intIndexServProdCaract<count($arrayServProdCaractServicioPunto);$intIndexServProdCaract++)
                            {
                                $objServProdCaractPunto = $arrayServProdCaractServicioPunto[$intIndexServProdCaract];
                                $objServProdCaractPunto->setEstado("Eliminado");
                                $this->emComercial->persist($objServProdCaractPunto);
                                $this->emComercial->flush();
                            }
                        }
                        //se agregan estados para cancelacion de servicios adicionales no preferenciales
                        else if( $strEstadoServicioPunto === "Factible"  || $strEstadoServicioPunto === "PreAsignacionInfoTecnica" 
                            || $strEstadoServicioPunto === "Asignada"  || $strEstadoServicioPunto === "Pendiente" 
                            || $strEstadoServicioPunto === "EnPruebas")
                        {
                            $objServicioPunto->setEstado("Eliminado");
                            $this->emComercial->persist($objServicioPunto);
                            $this->emComercial->flush();

                            //historial del servicio
                            $objServicioPuntoHistorial = new InfoServicioHistorial();
                            $objServicioPuntoHistorial->setServicioId($objServicioPunto);
                            $objServicioPuntoHistorial->setObservacion("Se elimino el Servicio");
                            $objServicioPuntoHistorial->setMotivoId($objMotivo->getId());
                            $objServicioPuntoHistorial->setEstado("Eliminado");
                            $objServicioPuntoHistorial->setUsrCreacion($strUsrCreacion);
                            $objServicioPuntoHistorial->setFeCreacion(new \DateTime('now'));
                            $objServicioPuntoHistorial->setIpCreacion($strIpCreacion);
                            $objServicioPuntoHistorial->setAccion($objAccion->getNombreAccion());
                            $this->emComercial->persist($objServicioPuntoHistorial);
                            $this->emComercial->flush();

                            $strTipoIps = 'FIJA';
        
                            //Obtiene tipo de ip por el servicio (PRIVADA)
                            $objTipoIp = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                            ->findOneBy(array("servicioId" => $objServicioPunto->getId(),
                                                                              "tipoIp"     => "PRIVADA",
                                                                              "estado"     => "Activo"));
                            if (is_object($objTipoIp))
                            {
                                $strTipoIps = $objTipoIp->getTipoIp();
                            }
                            
                            //obtener ips fijas q tiene el servicio
                            $objInfoIpServicioPunto = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                                              ->findOneBy(array("servicioId"    => $objServicioPunto->getId(),
                                                                                                "tipoIp"        => $strTipoIps));
                            if (is_object($objInfoIpServicioPunto))
                            {
                                $objInfoIpServicioPunto->setEstado("Eliminado");
                                $this->emInfraestructura->persist($objInfoIpServicioPunto);
                                $this->emInfraestructura->flush();
                            }
                        }
                    }
                }
                //cancelar sevicios adicionales gpon
                if(is_object($objServicio) && is_object($objServicio->getProductoId()))
                {
                    $arrayParaEliServAdd = array(
                        "objServicio"       => $objServicio,
                        "strObservacion"    => $strObservacionServicioPrincipal,
                        "objMotivo"         => $objMotivo,
                        "objAccion"         => $objAccion,
                        "intIdOficina"      => $arrayParametros['intIdOficina'],
                        "intIdPersonaEmpresaRol" => $intIdPersonaEmpresaRol,
                        "intIdEmpresa"      => $intIdEmpresa,
                        "strPrefijoEmpresa" => $arrayParametros['strPrefijoEmpresaOrigen'],
                        "strUsrCreacion"    => $strUsrCreacion,
                        "strIpCreacion"     => $strIpCreacion,
                    );
                    $this->cancelarServiciosSafecity($arrayParaEliServAdd);
                }
                //------------------------------------------------------------------------------------------------
                //Verificamos si es el ultimo servicio en el punto
                //Se cancela el punto.
                $objServicios    = $this->emComercial->getRepository('schemaBundle:InfoServicio')->findBy(array( "puntoId" => $objPunto->getId()));
                $intNumServicios = count($objServicios);
                $intCont         = 0;
                for($intIndex=0;$intIndex<count($objServicios);$intIndex++)
                {
                    $strServicioEstado = $objServicios[$intIndex]->getEstado();
                    
                    if($strServicioEstado=="Cancel"          || 
                       $strServicioEstado=="Cancel-SinEje"   || 
                       $strServicioEstado=="Anulado"         || 
                       $strServicioEstado=="Anulada"         || 
                       $strServicioEstado=="Eliminado"       || 
                       $strServicioEstado=="Eliminada"       || 
                       $strServicioEstado=="Eliminado-Migra" || 
                       $strServicioEstado=="Rechazada"       || 
                       $strServicioEstado=="Trasladado")
                    {
                        $intCont++;
                    }
                }
                if($intCont == ($intNumServicios))
                {
                    $objPunto->setEstado($strEstadoPuntoPorProceso);
                    $this->emComercial->persist($objPunto);
                    $this->emComercial->flush();
                }

                //Verificamos los puntos si estan todos Cancelados.
                $objPersonaEmpresaRol = $objPunto->getPersonaEmpresaRolId();
                $arrayPuntos               = $this->emComercial->getRepository('schemaBundle:InfoPunto')
                                                       ->findBy(array( "personaEmpresaRolId" => $objPersonaEmpresaRol->getId()));
                $intNumPuntos         = count($arrayPuntos);
                $intContPuntos        = 0;
                for($intIndex=0;$intIndex<count($arrayPuntos);$intIndex++)
                {
                    $objPuntoAux = $arrayPuntos[$intIndex];

                    if($objPuntoAux->getEstado()=="Cancelado")
                    {
                        $intContPuntos++;
                    }
                }

                if(($intNumPuntos == $intContPuntos) && $strOpcionProceso !== "Traslado")
                {
                    //Se cancela el contrato
                    $objContrato = $this->emComercial
                                     ->getRepository('schemaBundle:InfoContrato')
                                     ->findOneBy(array( "personaEmpresaRolId" => $objPersonaEmpresaRol->getId(),
                                                        "estado"              => "Activo"));
                    $objContrato->setEstado("Cancelado");
                    $this->emComercial->persist($objContrato);
                    $this->emComercial->flush();

                    //Se cancela el personaEmpresaRol
                    $objPersonaEmpresaRol->setEstado("Cancelado");
                    $this->emComercial->persist($objPersonaEmpresaRol);
                    $this->emComercial->flush();

                    //Se ingresa un registro en el historial de empresa persona rol
                    $objPersonaHistorial = new InfoPersonaEmpresaRolHisto();
                    $objPersonaHistorial->setPersonaEmpresaRolId($objPersonaEmpresaRol);
                    $objPersonaHistorial->setEstado("Cancelado");
                    $objPersonaHistorial->setUsrCreacion($strUsrCreacion);
                    $objPersonaHistorial->setFeCreacion(new \DateTime('now'));
                    $objPersonaHistorial->setIpCreacion($strIpCreacion);
                    $this->emComercial->persist($objPersonaHistorial);
                    $this->emComercial->flush();

                    //Se cancela el cliente
                    $objPersona = $objPersonaEmpresaRol->getPersonaId();
                    $objPersona->setEstado("Cancelado");
                    $this->emComercial->persist($objPersona);
                    $this->emComercial->flush();                
                }
                //----------------------------------------ENLACES-------------------------------------------------


                //Desconectar puerto del splitter
                $objInterfaceSplitter = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                ->find($objServicioTecnico->getInterfaceElementoConectorId());
                $objInterfaceSplitter->setEstado("not connect");
                $this->emInfraestructura->persist($objInterfaceSplitter);
                $this->emInfraestructura->flush();

                //eliminar enlace splitterN2-ont
                $arrayEnlacesAnteriores = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                                  ->findBy(array("interfaceElementoIniId" => $objInterfaceSplitter->getId(),
                                                                                 "estado"                 => "Activo"));
                foreach( $arrayEnlacesAnteriores as $objEnlace )
                {
                    $objEnlace->setEstado("Eliminado");
                    $this->emInfraestructura->persist($objEnlace);
                    $this->emInfraestructura->flush();
                }

                //se eliminan elementos del servicio
                foreach($arrayElementoRetiroEquipos as $objElementoServicio)
                {
                    $objElementoServicio->setEstado("Eliminado");
                    $this->emInfraestructura->persist($objElementoServicio);
                    $this->emInfraestructura->flush();

                    //historial del elemento
                    $objHistorialElemento = new InfoHistorialElemento();
                    $objHistorialElemento->setElementoId($objElementoServicio);
                    $objHistorialElemento->setObservacion($strObservacionHistorialElemento);
                    $objHistorialElemento->setEstadoElemento("Eliminado");
                    $objHistorialElemento->setUsrCreacion($strUsrCreacion);
                    $objHistorialElemento->setFeCreacion(new \DateTime('now'));
                    $objHistorialElemento->setIpCreacion($strIpCreacion);
                    $this->emInfraestructura->persist($objHistorialElemento);
                    $this->emInfraestructura->flush();

                    //eliminar puertos elemento
                    $arrayInterfacesElemento = $this->emInfraestructura
                                                    ->getRepository('schemaBundle:InfoInterfaceElemento')
                                                    ->findBy(array("elementoId" => $objElementoServicio->getId()));

                    foreach($arrayInterfacesElemento as $objInterfaceElemento)
                    {
                        $objInterfaceElemento->setEstado("Eliminado");
                        $this->emInfraestructura->persist($objInterfaceElemento);
                        $this->emInfraestructura->flush();
                    }

                }                        

                if( $strEjecutaLdap =="SI")
                {
                    //envio al ldap
                    $objResultadoJsonLdap = $this->servicioGeneral->ejecutarComandoLdap("E", $objServicio->getId(), 'TN');

                    if($objResultadoJsonLdap->status!="OK")
                    {
                        $strMensaje = $strMensaje . "<br>" . $objResultadoJsonLdap->mensaje;
                    }
                }

            }//cierre if status ok
            else
            {
                throw new \Exception("NO SE PUDO CANCELAR EL SERVICIO. <br>Error:".$strMensaje);
            }
            
            if($strOpcionProceso !== "Traslado")
            {
                //enviar mail
                $strAsunto  ="Cancelacion de Servicio: Retiro de Equipos ";

                $objEmpresaGrupo = $this->emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')->find($intIdEmpresa);
                $strPrefijo = is_object($objEmpresaGrupo) ? $objEmpresaGrupo->getPrefijo() : "";

                $this->servicioGeneral->enviarMailCancelarServicio( $strAsunto,
                                                                    $objServicio,
                                                                    $objMotivo->getId(),
                                                                    $objElemento,
                                                                    $objInterfaceElemento->getNombreInterfaceElemento(),
                                                                    $objServicioHistorialPrincipal,
                                                                    $strUsrCreacion,
                                                                    $strIpCreacion,
                                                                    $strPrefijo );
            }
            //------------------------------------------INICIO: ACTUALIZACIÓN DEL ESTADO EN ARCGIS - ISB
            $objServicioTecnico     = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                        ->findOneBy(array( "servicioId" => $intIdServicio));
            if(isset($intIdProducto) && !empty($intIdProducto))
            {
                $objProducto        = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                            ->find($intIdProducto);
            }
            if((is_object($objProducto)) && (is_object($objServicioTecnico)))
            {
                $arrayParametrosConsulta                       = array();
                $arrayParametrosConsulta['strUsrCreacion']     = $strUsrCreacion;
                $arrayParametrosConsulta['strIpCreacion']      = $strIpCreacion;
                $arrayParametrosConsulta['objServicioTecnico'] = $objServicioTecnico;
                $arrayParametrosConsulta['objProducto']        = $objProducto;
                // Pregunta si coincide con un producto parametrizado
                $arrayRespuestaProductoCancelacion = $this->validarCondicionesProductos($arrayParametrosConsulta);
                if(($strStatus == "OK") && ($arrayRespuestaProductoCancelacion['status']=='OK'))
                {
                    // Solo si coincide ingresa a preguntar por los estados  de condiciòn
                    $arrayRespuestaEstados         = $this->validarCondicionesEstados($arrayParametrosConsulta);

                    if($arrayRespuestaEstados['status']=='OK')
                    {
                        // PuertoSwitch y  NombreSwitch
                        $intIdInterfaceElemento  = $objServicioTecnico->getInterfaceElementoId();
                        $objInterfaceElemento    = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                           ->find($intIdInterfaceElemento);
                        $objElemento          = $objInterfaceElemento->getElementoId();
                        $strNombreSwitch      = $objElemento->getNombreElemento();
                        $strPuertoSwitch      = $objInterfaceElemento->getNombreInterfaceElemento();
                        $strLoginPunto        = $objServicio->getPuntoId()->getLogin();            
                        $objEmpresaGrupo      = $this->emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')->find($intIdEmpresa);
                        $strPrefijoEmpresa    = is_object($objEmpresaGrupo) ? $objEmpresaGrupo->getPrefijo() : "";
                        $arrayParamServicio   = array(
                                            "strUsrCancelacion" => $strUsrCreacion,
                                            "strNombreSwitch"   => $strNombreSwitch,
                                            "strPuertoSwitch"   => $strPuertoSwitch,
                                            "strLoginPunto"     => $strLoginPunto,
                                            "strPrefijo"        => $strPrefijoEmpresa,
                                            "strIpCreacion"     => $strIpCreacion,
                                            "objServicioPunto"  => $objServicio
                                            );
                        //Se llama al procedimiento en la base
                        $this->inactivarUmARCGIS($arrayParamServicio);
                    }
                }
            }
            //------------------------------------------FIN: ACTUALIZACIÓN DEL ESTADO EN ARCGIS   - ISB  
            $strStatus              = "OK";
            $strMensaje             = "OK";
            $strObservacionServicio = "Se Realizo la cancelacion exitosamente.";
            $strEstadoSolMasiva     = "Finalizada";
            $intStatusCode          = 200;
        }
        catch (\Exception $e) 
        {
            $strStatus              = "ERROR";
            $strMensaje             = $e->getMessage();
            $strObservacionServicio = $strMensaje;
            $strEstadoSolMasiva     = "Fallo";
            $intStatusCode          = 500;
        }
        
        
        if(is_object($objDetalleSolicitudCancelacion))
        {
            $objDetalleSolicitudCancelacion->setEstado($strEstadoSolMasiva);
            $this->emComercial->persist($objDetalleSolicitudCancelacion);
            $this->emComercial->flush();   

            $this->servicioGeneral->finalizarSolicitudPadrePorProcesoMasivo(array(  'idSolicitudPadre'     => $intIdSolicitudCancelacionPadre,
                                                                                    'usrCreacion'          => $strUsrCreacion,
                                                                                    'ipCreacion'           => $strIpCreacion ));

            //Se crea Historial de Servicio
            $objDetalleSolHist = new InfoDetalleSolHist();
            $objDetalleSolHist->setDetalleSolicitudId($objDetalleSolicitudCancelacion);
            $objDetalleSolHist->setEstado($objDetalleSolicitudCancelacion->getEstado());
            $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
            $objDetalleSolHist->setUsrCreacion($strUsrCreacion);
            $objDetalleSolHist->setIpCreacion($strIpCreacion);
            $objDetalleSolHist->setObservacion($strObservacionServicio);
            $this->emComercial->persist($objDetalleSolHist);
            $this->emComercial->flush();
            
            if($strStatus === "OK")
            {
                $this->servicioGeneral->enviarNotificacionFinalizadoSolicitudMasiva(array(  'idSolicitudPadre'     => $intIdSolicitudCancelacionPadre,
                                                                                            'idServicio'           => $objServicio->getId(),
                                                                                            'usrCreacion'          => $strUsrCreacion,
                                                                                            'ipCreacion'           => $strIpCreacion ));
            }
        }
        
        $arrayFinal[]      = array('status' => $strStatus, 'mensaje'=> $strMensaje, 'statusCode' => $intStatusCode);
        $arrayFinal[0]['intDetalleId'] = $intDetalleId;
        return $arrayFinal;
    }
    
    /**
     * cancelarServicioTnp
     * 
     * Funcion que sirve para la ejecucion de cancelacion de servicios de Internet Residencial para TNP.
     *          - Se agrega validacion de MIDDLEWARE.
     *          - Se envia parametro prefijo empresa al ldap.
     * 
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 14-11-2018
     * @since 1.0
     * 
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 08-07-2020 - Actualización: Se agrega bloque de código para actualizar datos de tareas en nueva tabla DB_SOPORTE.INFO_TAREA
     * 
     * @param $arrayParametros  [servicio, servicioTecnico, modeloElemento, producto, elemento, interfaceElemento, login, idEmpresa, 
     *                          usrCreacion, ipCreacion, motivo, idPersonaEmpresaRol, accion]
     */
    public function cancelarServicioTnp( $arrayParametros )
    {
        $objServicio               = $arrayParametros['servicio'];
        $strLogin                  = $arrayParametros['login'];
        $intIdEmpresa              = $arrayParametros['idEmpresa'];
        $strUsrCreacion            = $arrayParametros['usrCreacion'];
        $strIpCreacion             = $arrayParametros['ipCreacion'];
        $objMotivoObj              = $arrayParametros['motivo'];
        $intIdPersonaEmpresaRol    = $arrayParametros['idPersonaEmpresaRol'];
        $intIdDepartamento         = $arrayParametros['intIdDepartamento'];
        $intDetalleId              = null;
        try
        {
            $objServicioTecnico        = $arrayParametros['servicioTecnico'];
            $objModeloElemento         = $arrayParametros['modeloElemento'];
            $objProducto               = $arrayParametros['producto'];
            $objElemento               = $arrayParametros['elemento'];
            $objInterfaceElemento      = $arrayParametros['interfaceElemento'];
            $objAccion                 = $arrayParametros['accion'];

            if(!is_object($objServicio) || !is_object($objServicioTecnico) || !is_object($objModeloElemento) || !is_object($objProducto) 
                || !is_object($objElemento) || !is_object($objInterfaceElemento) || !is_object($objAccion) )
            {
                throw new \Exception("No se han enviado correctamente los parámetros. <br>Por favor Revisar!");
            }

            $arrayParametrosHist       = array();

            $arrayParametrosHist["strCodEmpresa"]           = $intIdEmpresa;
            $arrayParametrosHist["strUsrCreacion"]          = $strUsrCreacion;
            $arrayParametrosHist["strOpcion"]               = "Historial";
            $arrayParametrosHist["strIpCreacion"]           = $strIpCreacion;          
            $arrayParametrosHist["intIdDepartamentoOrigen"] = $intIdDepartamento;

            $objIpElemento          = $this->emComercial->getRepository('schemaBundle:InfoIp')
                                           ->findOneBy(array("elementoId"=>$objElemento->getId(), "estado"=>"Activo"));
            $objDetalleElemento     = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                   ->findOneBy(array("elementoId"   => $objServicioTecnico->getElementoId(),
                                                                     "detalleNombre"=> 'MIDDLEWARE',
                                                                     "estado"       => 'Activo'));

            $strEjecutaLdap            = "NO";
            $boolMiddleware         = false;

            if(is_object($objDetalleElemento) && 
               $objDetalleElemento->getDetalleValor() == 'SI')
            {
                $boolMiddleware = true;
            }

            if(!$boolMiddleware)
            {
                throw new \Exception("No se pudo Cancelar el Servicio - OLT sin middleware");
            }             

            if($boolMiddleware)
            {
                $strEjecutaLdap         = "SI";
                $strLineProfile         = '';            
                $strVlan                = '';
                $strServiceProfile      = '';
                $strGemPort             = '';
                $strTrafficTable        = '';
                $strMacOnt              = '';
                $strIndiceCliente       = '';
                $strIpFija              = '';
                $strSpid                = '';
                $strMacWifi             = '';

                //OBTENER DATOS DEL CLIENTE (NOMBRES E IDENTIFICACION)
                $objPersona         = $objServicio->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId();
                $strIdentificacion  = $objPersona->getIdentificacionCliente();
                $strNombreCliente   = ($objPersona->getRazonSocial() != "") ? $objPersona->getRazonSocial() : 
                                                        $objPersona->getNombres()." ".$objPersona->getApellidos();

                //OBTENER SERIE ONT
                $intIdElementoCliente  = $objServicioTecnico->getElementoClienteId();
                $objElementoCliente    = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($intIdElementoCliente);
                $strSerieOnt           = $objElementoCliente->getSerieFisica();

                //OBTENER MAC ONT
                $objSpcMacOnt = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "MAC ONT", $objProducto);
                if(is_object($objSpcMacOnt))
                {
                    $strMacOnt = $objSpcMacOnt->getValor();
                }

                //OBTENER INDICE CLIENTE
                $objSpcIndiceCliente = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "INDICE CLIENTE", $objProducto);
                if(is_object($objSpcIndiceCliente))
                {
                    $strIndiceCliente = $objSpcIndiceCliente->getValor();
                }
                $strNombreModeloOlt = $objModeloElemento->getNombreModeloElemento();
                
                if($strNombreModeloOlt === "MA5608T")
                {
                    //obtener line profile
                    $objSpcLineProfile = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "LINE-PROFILE-NAME", $objProducto);
                    if(is_object($objSpcLineProfile))
                    {
                        $strLineProfile = $objSpcLineProfile->getValor();
                    }

                    //obtener service profile
                    $objSrvProfileProdCaract = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio,"SERVICE-PROFILE",$objProducto);
                    if(is_object($objSrvProfileProdCaract))
                    {
                        $strServiceProfile = $objSrvProfileProdCaract->getValor();
                    }

                    //obtener vlan
                    $objVlanProdCaract = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "VLAN", $objProducto);
                    if(is_object($objVlanProdCaract))
                    {
                        $strVlan = $objVlanProdCaract->getValor();                
                    }

                    //obtener gemport
                    $objGemPortProdCaract = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "GEM-PORT", $objProducto);
                    if(is_object($objGemPortProdCaract))
                    {
                        $strGemPort = $objGemPortProdCaract->getValor();
                    }

                    //obtener traffic table
                    $objTrafficTableProdCaract = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio,"TRAFFIC-TABLE",$objProducto);
                    if(is_object($objTrafficTableProdCaract))
                    {
                        $strTrafficTable = $objTrafficTableProdCaract->getValor();
                    }

                    //obtener service-port
                    $objSpcServicePort = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "SPID", $objProducto);
                    if(is_object($objSpcServicePort))
                    {
                        $strSpid = $objSpcServicePort->getValor();
                    }            
                }
                else
                {
                    throw new \Exception("No existe flujo para el modelo ".$strNombreModeloOlt);
                }
                
                //OBTENER IP DEL PLAN
                $objIpFija     = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                      ->findOneBy(array("servicioId"=>$objServicio->getId(),
                                                                        "estado"=>"Activo"));
                if(is_object($objIpFija))
                {
                    $strIpFija  = $objIpFija->getIp();   
                }

                $arrayIpCancelar    = array();
                $arrayDatos = array(
                                        'serial_ont'            => $strSerieOnt,
                                        'mac_ont'               => $strMacOnt,
                                        'nombre_olt'            => $objElemento->getNombreElemento(),
                                        'ip_olt'                => $objIpElemento->getIp(),
                                        'puerto_olt'            => $objInterfaceElemento->getNombreInterfaceElemento(),
                                        'modelo_olt'            => $objModeloElemento->getNombreModeloElemento(),
                                        'ont_id'                => $strIndiceCliente,
                                        'service_port'          => $strSpid,
                                        'gemport'               => $strGemPort,
                                        'service_profile'       => $strServiceProfile,
                                        'line_profile'          => $strLineProfile,
                                        'traffic_table'         => $strTrafficTable,
                                        'vlan'                  => $strVlan,
                                        'estado_servicio'       => $objServicioTecnico->getServicioId()->getEstado(),
                                        'ip'                    => $strIpFija,
                                        'ip_fijas_activas'      => '',
                                        'tipo_negocio_actual'   => 'HOME',
                                        'mac_wifi'              => $strMacWifi,
                                        'scope'                 => $strScope,
                                        'ip_cancelar'           => $arrayIpCancelar
                                   );

                $arrayDatosMiddleware = array(
                                                'nombre_cliente'        => $strNombreCliente,
                                                'login'                 => $strLogin,
                                                'identificacion'        => $strIdentificacion,
                                                'datos'                 => $arrayDatos,
                                                'opcion'                => $this->opcion,
                                                'ejecutaComando'        => $this->ejecutaComando,
                                                'usrCreacion'           => $strUsrCreacion,
                                                'ipCreacion'            => $strIpCreacion,
                                                'empresa'               => 'TN'
                                            );
                $arrayFinal         = $this->rdaMiddleware->middleware(json_encode($arrayDatosMiddleware));
                $strStatus          = $arrayFinal['status'];
                $strMensaje         = $arrayFinal['mensaje'];
            }
            
            if($strStatus === "OK")
            {
                //crear solicitud para retiro de equipo (ont y wifi)
                $objTipoSolicitud = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                      ->findOneBy(array( "descripcionSolicitud" => "SOLICITUD RETIRO EQUIPO", "estado"=>"Activo"));

                $objDetalleSolicitud = new InfoDetalleSolicitud();
                $objDetalleSolicitud->setServicioId($objServicio);
                $objDetalleSolicitud->setTipoSolicitudId($objTipoSolicitud);
                $objDetalleSolicitud->setEstado("AsignadoTarea");
                $objDetalleSolicitud->setUsrCreacion($strUsrCreacion);
                $objDetalleSolicitud->setFeCreacion(new \DateTime('now'));
                $objDetalleSolicitud->setObservacion("SOLICITA RETIRO DE EQUIPO POR CANCELACION DEL SERVICIO");
                $this->emComercial->persist($objDetalleSolicitud);

                //crear la caracteristica para la solicitud de retiro de equipo
                $entityAdmiCaracteristica = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')->find(360);

                $arrayParams['interfaceElementoConectorId']    = $objServicioTecnico->getInterfaceElementoConectorId();
                $arrayParams['arrayData']                      = array();
                $arrayElementoRetiroEquipos                    = $this->emInfraestructura
                                                                      ->getRepository('schemaBundle:InfoElemento')
                                                                      ->getElementosClienteByInterface($arrayParams);                        

                foreach($arrayElementoRetiroEquipos as $objElementoServicio)
                {
                    //valor del ont
                    $objEntityCaract= new InfoDetalleSolCaract();
                    $objEntityCaract->setCaracteristicaId($entityAdmiCaracteristica);
                    $objEntityCaract->setDetalleSolicitudId($objDetalleSolicitud);
                    $objEntityCaract->setValor($objElementoServicio->getId());
                    $objEntityCaract->setEstado("AsignadoTarea");
                    $objEntityCaract->setUsrCreacion($strUsrCreacion);
                    $objEntityCaract->setFeCreacion(new \DateTime('now'));
                    $this->emComercial->persist($objEntityCaract);
                    $this->emComercial->flush();
                }

                //obtener tarea
                $entityProceso = $this->emSoporte->getRepository('schemaBundle:AdmiProceso')->findOneByNombreProceso("SOLICITAR RETIRO EQUIPO");
                $entityTareas  = $this->emSoporte->getRepository('schemaBundle:AdmiTarea')->findTareasActivasByProceso($entityProceso->getId());
                $entityTarea   = $entityTareas[0];

                //grabar nuevo info_detalle para la solicitud de retiro de equipo
                $entityDetalle = new InfoDetalle();
                $entityDetalle->setDetalleSolicitudId($objDetalleSolicitud->getId());
                $entityDetalle->setTareaId($entityTarea);
                $entityDetalle->setLongitud($objServicio->getPuntoId()->getLongitud());
                $entityDetalle->setLatitud($objServicio->getPuntoId()->getLatitud());
                $entityDetalle->setPesoPresupuestado(0);
                $entityDetalle->setValorPresupuestado(0);
                $entityDetalle->setIpCreacion($strIpCreacion);
                $entityDetalle->setFeCreacion(new \DateTime('now'));
                $entityDetalle->setUsrCreacion($strUsrCreacion);
                $entityDetalle->setFeSolicitada(new \DateTime('now'));
                $this->emSoporte->persist($entityDetalle);
                $this->emSoporte->flush();

                //obtenemos el persona empresa rol del usuario
                $objPersonaEmpresaRolUsr = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                ->find($intIdPersonaEmpresaRol);

                //buscamos datos del dept, persona
                $objDepartamento = $this->emGeneral->getRepository('schemaBundle:AdmiDepartamento')
                                                   ->find($objPersonaEmpresaRolUsr->getDepartamentoId());
                $objPersona      = $objPersonaEmpresaRolUsr->getPersonaId();

                //grabamos soporte.info_detalle_asignacion
                $objDetalleAsignacion = new InfoDetalleAsignacion();
                $objDetalleAsignacion->setDetalleId($entityDetalle);
                $objDetalleAsignacion->setAsignadoId($objDepartamento->getId());
                $objDetalleAsignacion->setAsignadoNombre($objDepartamento->getNombreDepartamento());
                $objDetalleAsignacion->setRefAsignadoId($objPersona->getId());

                if($objPersona->getRazonSocial()=="")
                {
                    $strNombre = $objPersona->getNombres()." ".$objPersona->getApellidos();
                }
                else
                {
                    $strNombre = $objPersona->getRazonSocial();
                }

                $objDetalleAsignacion->setRefAsignadoNombre($strNombre);
                $objDetalleAsignacion->setPersonaEmpresaRolId($objPersonaEmpresaRolUsr->getId());
                $objDetalleAsignacion->setUsrCreacion($strUsrCreacion);
                $objDetalleAsignacion->setFeCreacion(new \DateTime('now'));
                $objDetalleAsignacion->setIpCreacion($strIpCreacion);
                $this->emSoporte->persist($objDetalleAsignacion);
                $this->emSoporte->flush();

                //Se ingresa el historial de la tarea
                if(is_object($entityDetalle))
                {
                    $arrayParametrosHist["intDetalleId"] = $entityDetalle->getId();            
                    $intDetalleId                        = $arrayParametrosHist["intDetalleId"];
                }

                $arrayParametrosHist["strObservacion"]  = "Tarea Asignada";
                $arrayParametrosHist["strEstadoActual"] = "Asignada";     
                $arrayParametrosHist["strAccion"]       = "Asignada";

                $this->serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);              

                $strAfectadoNombre = "";
                $intPuntoId        = "";
                $strPuntoLogin     = "";

                if($objServicio->getPuntoId()->getNombrePunto())
                {
                    $strAfectadoNombre = $objServicio->getPuntoId()->getNombrePunto();
                }

                if($objServicio->getPuntoId())
                {
                    $intPuntoId = $objServicio->getPuntoId()->getId();
                    $strPuntoLogin = $objServicio->getPuntoId()->getLogin();
                }

                // se graba en la DB_SOPORTE.INFO_CRITERIO_AFECTADO
                $objCriterio = new InfoCriterioAfectado();
                $objCriterio->setId(1);
                $objCriterio->setDetalleId($entityDetalle);
                $objCriterio->setCriterio("Clientes");
                $objCriterio->setOpcion("Cliente: " . $strAfectadoNombre . " | OPCION: Punto Cliente");
                $objCriterio->setFeCreacion(new \DateTime('now'));
                $objCriterio->setUsrCreacion($strUsrCreacion);
                $objCriterio->setIpCreacion($strIpCreacion);
                $this->emSoporte->persist($objCriterio);
                $this->emSoporte->flush();

                // se graba en la DB_SOPORTE.INFO_PARTE_AFECTADA
                $objAfectado = new InfoParteAfectada();
                $objAfectado->setTipoAfectado("Cliente");
                $objAfectado->setDetalleId($entityDetalle->getId());
                $objAfectado->setCriterioAfectadoId($objCriterio->getId());
                $objAfectado->setAfectadoId($intPuntoId);
                $objAfectado->setFeIniIncidencia(new \DateTime('now'));
                $objAfectado->setAfectadoNombre($strPuntoLogin);
                $objAfectado->setAfectadoDescripcion($strAfectadoNombre);
                $objAfectado->setFeCreacion(new \DateTime('now'));
                $objAfectado->setUsrCreacion($strUsrCreacion);
                $objAfectado->setIpCreacion($strIpCreacion);
                $this->emSoporte->persist($objAfectado);
                $this->emSoporte->flush();

                //crear historial para la solicitud
                $objHistorialSolicitud = new InfoDetalleSolHist();
                $objHistorialSolicitud->setDetalleSolicitudId($objDetalleSolicitud);
                $objHistorialSolicitud->setEstado("AsignadoTarea");
                $objHistorialSolicitud->setObservacion("GENERACION AUTOMATICA DE SOLICITUD RETIRO DE EQUIPO POR CANCELACION DEL SERVICIO");
                $objHistorialSolicitud->setUsrCreacion($strUsrCreacion);
                $objHistorialSolicitud->setFeCreacion(new \DateTime('now'));
                $objHistorialSolicitud->setIpCreacion($strIpCreacion);
                $this->emComercial->persist($objHistorialSolicitud);
                
                //Cancelar Servicio.
                $objPunto               = $objServicio->getPuntoId();
                $objServicio->setEstado("Cancel");
                $this->emComercial->persist($objServicio);
                $this->emComercial->flush();

                //historial del servicio
                $objServicioPuntoHistorial = new InfoServicioHistorial();
                $objServicioPuntoHistorial->setServicioId($objServicio);
                $objServicioPuntoHistorial->setObservacion("Se cancelo el Servicio");
                $objServicioPuntoHistorial->setMotivoId($objMotivoObj->getId());
                $objServicioPuntoHistorial->setEstado("Cancel");
                $objServicioPuntoHistorial->setUsrCreacion($strUsrCreacion);
                $objServicioPuntoHistorial->setFeCreacion(new \DateTime('now'));
                $objServicioPuntoHistorial->setIpCreacion($strIpCreacion);
                $objServicioPuntoHistorial->setAccion($objAccion->getNombreAccion());
                $this->emComercial->persist($objServicioPuntoHistorial);
                $this->emComercial->flush();

                $objServicioHistorialPrincipal = $objServicioPuntoHistorial;
                
                //eliminar las caracteristicas del servicio
                $arrayServProdCaractServicioPunto   = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                        ->findBy(array("servicioId" => $objServicio->getId(), 
                                                                                       "estado"     => "Activo"));
                for($intIndexServProdCaract=0;$intIndexServProdCaract<count($arrayServProdCaractServicioPunto);$intIndexServProdCaract++)
                {
                    $objServProdCaractPunto = $arrayServProdCaractServicioPunto[$intIndexServProdCaract];
                    $objServProdCaractPunto->setEstado("Eliminado");
                    $this->emComercial->persist($objServProdCaractPunto);
                    $this->emComercial->flush();
                }
                
                //Verificamos si es el ultimo servicio en el punto
                //Se cancela el punto.
                $objServicios    = $this->emComercial->getRepository('schemaBundle:InfoServicio')->findBy(array( "puntoId" => $objPunto->getId()));
                $intNumServicios = count($objServicios);
                $intCont         = 0;
                for($intIndex=0;$intIndex<count($objServicios);$intIndex++)
                {
                    $strServicioEstado = $objServicios[$intIndex]->getEstado();
                    
                    if($strServicioEstado=="Cancel"          || 
                       $strServicioEstado=="Cancel-SinEje"   || 
                       $strServicioEstado=="Anulado"         || 
                       $strServicioEstado=="Anulada"         || 
                       $strServicioEstado=="Eliminado"       || 
                       $strServicioEstado=="Eliminada"       || 
                       $strServicioEstado=="Eliminado-Migra" || 
                       $strServicioEstado=="Rechazada"       || 
                       $strServicioEstado=="Trasladado")
                    {
                        $intCont++;
                    }
                }
                if($intCont == ($intNumServicios))
                {
                    $objPunto->setEstado("Cancelado");
                    $this->emComercial->persist($objPunto);
                    $this->emComercial->flush();
                }

                //Verificamos los puntos si estan todos Cancelados.
                $objPersonaEmpresaRol = $objPunto->getPersonaEmpresaRolId();
                $arrayPuntos          = $this->emComercial->getRepository('schemaBundle:InfoPunto')
                                             ->findBy(array( "personaEmpresaRolId" => $objPersonaEmpresaRol->getId()));
                $intNumPuntos         = count($arrayPuntos);
                $intContPuntos        = 0;
                for($intIndex=0;$intIndex<count($arrayPuntos);$intIndex++)
                {
                    $objPuntoAux = $arrayPuntos[$intIndex];

                    if($objPuntoAux->getEstado()=="Cancelado")
                    {
                        $intContPuntos++;
                    }
                }

                if(($intNumPuntos) == $intContPuntos)
                {
                    //Se cancela el contrato
                    $objContrato = $this->emComercial
                                     ->getRepository('schemaBundle:InfoContrato')
                                     ->findOneBy(array( "personaEmpresaRolId" => $objPersonaEmpresaRol->getId(),
                                                        "estado"              => "Activo"));
                    $objContrato->setEstado("Cancelado");
                    $this->emComercial->persist($objContrato);
                    $this->emComercial->flush();

                    //Se cancela el personaEmpresaRol
                    $objPersonaEmpresaRol->setEstado("Cancelado");
                    $this->emComercial->persist($objPersonaEmpresaRol);
                    $this->emComercial->flush();

                    //Se ingresa un registro en el historial de empresa persona rol
                    $objPersonaHistorial = new InfoPersonaEmpresaRolHisto();
                    $objPersonaHistorial->setPersonaEmpresaRolId($objPersonaEmpresaRol);
                    $objPersonaHistorial->setEstado("Cancelado");
                    $objPersonaHistorial->setUsrCreacion($strUsrCreacion);
                    $objPersonaHistorial->setFeCreacion(new \DateTime('now'));
                    $objPersonaHistorial->setIpCreacion($strIpCreacion);
                    $this->emComercial->persist($objPersonaHistorial);
                    $this->emComercial->flush();

                    //Se cancela el cliente
                    $objPersona = $objPersonaEmpresaRol->getPersonaId();
                    $objPersona->setEstado("Cancelado");
                    $this->emComercial->persist($objPersona);
                    $this->emComercial->flush();                
                }
                
                //Desconectar puerto del splitter
                $objInterfaceSplitter = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                ->find($objServicioTecnico->getInterfaceElementoConectorId());
                $objInterfaceSplitter->setEstado("not connect");
                $this->emInfraestructura->persist($objInterfaceSplitter);
                $this->emInfraestructura->flush();

                //eliminar enlace splitterN2-ont
                $arrayEnlacesAnteriores = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                                  ->findBy(array("interfaceElementoIniId" => $objInterfaceSplitter->getId(),
                                                                                 "estado"                 => "Activo"));
                foreach( $arrayEnlacesAnteriores as $objEnlace )
                {
                    $objEnlace->setEstado("Eliminado");
                    $this->emInfraestructura->persist($objEnlace);
                    $this->emInfraestructura->flush();
                }

                //se eliminan elementos del servicio
                foreach($arrayElementoRetiroEquipos as $objElementoServicio)
                {
                    $objElementoServicio->setEstado("Eliminado");
                    $this->emInfraestructura->persist($objElementoServicio);
                    $this->emInfraestructura->flush();

                    //historial del elemento
                    $objHistorialElemento = new InfoHistorialElemento();
                    $objHistorialElemento->setElementoId($objElementoServicio);
                    $objHistorialElemento->setObservacion("Se elimino el elemento por cancelacion de Servicio");
                    $objHistorialElemento->setEstadoElemento("Eliminado");
                    $objHistorialElemento->setUsrCreacion($strUsrCreacion);
                    $objHistorialElemento->setFeCreacion(new \DateTime('now'));
                    $objHistorialElemento->setIpCreacion($strIpCreacion);
                    $this->emInfraestructura->persist($objHistorialElemento);
                    $this->emInfraestructura->flush();

                    //eliminar puertos elemento
                    $arrayInterfacesElemento = $this->emInfraestructura
                                                    ->getRepository('schemaBundle:InfoInterfaceElemento')
                                                    ->findBy(array("elementoId" => $objElementoServicio->getId()));

                    foreach($arrayInterfacesElemento as $objInterfaceElemento)
                    {
                        $objInterfaceElemento->setEstado("Eliminado");
                        $this->emInfraestructura->persist($objInterfaceElemento);
                        $this->emInfraestructura->flush();
                    }

                }                        

                if( $strEjecutaLdap =="SI")
                {
                    //envio al ldap
                    $objResultadoJsonLdap = $this->servicioGeneral->ejecutarComandoLdap("E", $objServicio->getId(), 'TNP');

                    if($objResultadoJsonLdap->status!="OK")
                    {
                        $strMensaje = $strMensaje . "<br>" . $objResultadoJsonLdap->mensaje;
                    }
                }

            }//cierre if status ok
            else
            {
                throw new \Exception("NO SE PUDO CANCELAR EL SERVICIO. <br>Error:".$strMensaje);
            }

            //enviar mail
            $strAsunto  ="Cancelacion de Servicio: Retiro de Equipos ";

            $objEmpresaGrupo = $this->emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')->find($intIdEmpresa);
            $strPrefijo = is_object($objEmpresaGrupo) ? $objEmpresaGrupo->getPrefijo() : "";

            $this->servicioGeneral->enviarMailCancelarServicio( $strAsunto,
                                                                $objServicio,
                                                                $objMotivoObj->getId(),
                                                                $objElemento,
                                                                $objInterfaceElemento->getNombreInterfaceElemento(),
                                                                $objServicioHistorialPrincipal,
                                                                $strUsrCreacion,
                                                                $strIpCreacion,
                                                                $strPrefijo );
            
            $strStatus              = "OK";
            $strMensaje             = "OK";
            $strObservacionServicio = "Se Realizo la cancelacion exitosamente.";
            $strEstadoSolMasiva     = "Finalizada";
            $intStatusCode          = 200;
        }
        catch (\Exception $e) 
        {
            $strStatus              = "ERROR";
            $strMensaje             = $e->getMessage();
            $strObservacionServicio = $strMensaje;
            $strEstadoSolMasiva     = "Fallo";
            $intStatusCode          = 500;
        }
        
        
        if(is_object($objDetalleSolicitudCancelacion))
        {
            $objDetalleSolicitudCancelacion->setEstado($strEstadoSolMasiva);
            $this->emComercial->persist($objDetalleSolicitudCancelacion);
            $this->emComercial->flush();   

            $this->servicioGeneral->finalizarSolicitudPadrePorProcesoMasivo(array(  'idSolicitudPadre'     => $intIdSolicitudCancelacionPadre,
                                                                                    'usrCreacion'          => $strUsrCreacion,
                                                                                    'ipCreacion'           => $strIpCreacion ));

            //Se crea Historial de Servicio
            $objDetalleSolHist = new InfoDetalleSolHist();
            $objDetalleSolHist->setDetalleSolicitudId($objDetalleSolicitudCancelacion);
            $objDetalleSolHist->setEstado($objDetalleSolicitudCancelacion->getEstado());
            $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
            $objDetalleSolHist->setUsrCreacion($strUsrCreacion);
            $objDetalleSolHist->setIpCreacion($strIpCreacion);
            $objDetalleSolHist->setObservacion($strObservacionServicio);
            $this->emComercial->persist($objDetalleSolHist);
            $this->emComercial->flush();
            
            if($strStatus === "OK")
            {
                $this->servicioGeneral->enviarNotificacionFinalizadoSolicitudMasiva(array(  'idSolicitudPadre'     => $intIdSolicitudCancelacionPadre,
                                                                                            'idServicio'           => $objServicio->getId(),
                                                                                            'usrCreacion'          => $strUsrCreacion,
                                                                                            'ipCreacion'           => $strIpCreacion ));
            }
        }
        
        $arrayFinal[]      = array('status' => $strStatus, 'mensaje'=> $strMensaje, 'statusCode' => $intStatusCode);
        $arrayFinal[0]['intDetalleId'] = $intDetalleId;
        return $arrayFinal;
    }
    
    /**
     * Funcion que permite realizar la cancelacion logica del servicio para la empresa TNG
     * 
     * @author Jesus Banchen <jbanchen@telconet.ec>
     * @version 1.0
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 15-08-2019 Se modifica el mensaje de cancelación por cambios en js 
     * 
     * @param array $arrayPeticiones [id_servicio,usrcreacion,ipcreacion,motivo,idAccion]
     * @return array $arrayRespuesta [status,mensaje] 
     * 
    */
   public function cancelarServicioTng($arrayPeticiones)
    {
        $strServicioId  = $arrayPeticiones['idServicio'];
        $strUsrCreacion = $arrayPeticiones['usrCreacion'];
        $strIpCreacion  = $arrayPeticiones['ipCreacion'];
        $intIdMotivo    = $arrayPeticiones['motivo'];
        $intIdAccion    = $arrayPeticiones['idAccion'];

        try
        {
            $this->emComercial->getConnection()->beginTransaction();

            $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($strServicioId);
            $objMotivo = $this->emGeneral->getRepository('schemaBundle:AdmiMotivo')->find($intIdMotivo);
            $objAccion = $this->emSeguridad->getRepository('schemaBundle:SistAccion')->find($intIdAccion);

            $objServicio->setEstado("Cancel");
            $this->emComercial->persist($objServicio);
            $this->emComercial->flush();

            $objServicioPuntoHistorial = new InfoServicioHistorial();
            $objServicioPuntoHistorial->setServicioId($objServicio);
            $objServicioPuntoHistorial->setObservacion("Se canceló el servicio");
            $objServicioPuntoHistorial->setMotivoId($objMotivo->getId());
            $objServicioPuntoHistorial->setEstado("Cancel");
            $objServicioPuntoHistorial->setUsrCreacion($strUsrCreacion);
            $objServicioPuntoHistorial->setFeCreacion(new \DateTime('now'));
            $objServicioPuntoHistorial->setIpCreacion($strIpCreacion);
            $objServicioPuntoHistorial->setAccion($objAccion->getNombreAccion());
            $this->emComercial->persist($objServicioPuntoHistorial);
            $this->emComercial->flush();

            $arrayServProdCaractServicioPunto = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                  ->findBy(array("servicioId" => $objServicio->getId(),
                                                                                 "estado" => "Activo"));

            for ($intIndexServProdCaract = 0; $intIndexServProdCaract < count($arrayServProdCaractServicioPunto); $intIndexServProdCaract++)
            {
                $objServProdCaractPunto = $arrayServProdCaractServicioPunto[$intIndexServProdCaract];
                $objServProdCaractPunto->setEstado("Eliminado");
                $this->emComercial->persist($objServProdCaractPunto);
                $this->emComercial->flush();
            }

            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->commit();
            }

            $this->emComercial->getConnection()->close();
            
            $strStatus  = "OK";
            $strMensaje = "Se canceló el servicio";
        }
        catch (\Exception $ex)
        {
            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
                $this->emComercial->getConnection()->close();
            }
            
            $this->serviceUtil->insertError("Telcos+", "InfoCancelarServicioService->cancelarServicioTng",
                        $ex->getMessage(), $strUsrCreacion, $strIpCreacion
                );

            $strStatus = "ERROR";
            $strMensaje = "Error en el procesamiento de los datos..";
        }
        $arrayRespuesta[] = array('status' => $strStatus, 'mensaje' => $strMensaje);
        return $arrayRespuesta;
    }

    /**
     * Método utilizado para eliminar las rutas y las subredes, así como también el llamado al respectivo ws al cancelar un servicio
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 
     * 
     * @param array $arrayParametros [
     *                                  "objServicio"           => objeto servicio
     *                                  "objProducto"           => objeto producto
     *                                  "objServicioTecnico"    => objeto servicio técnico
     *                                  "usrCreacion"           => usuario creación
     *                                  "ipCreacion"            => ip de creación
     *                                  "strVrf"                => vrf
     *                                  ]
     * @return array $arrayRespuesta ["status", "mensaje"]
     */
    public function eliminarRutasTn($arrayParametros)
    {
        $objServicio        = $arrayParametros['objServicio'];
        $objProducto        = $arrayParametros['objProducto'];
        $objServicioTecnico = $arrayParametros['objServicioTecnico'];
        $strUsrCreacion     = $arrayParametros['usrCreacion'];
        $strIpCreacion      = $arrayParametros['ipCreacion'];
        $strVrf             = $arrayParametros['strVrf'];
        try
        {
            if(!is_object($objServicio) || !is_object($objProducto) || !is_object($objServicioTecnico))
            {
                throw new \Exception("No se ha enviado correctamente los parámetros");
            }
            
            $intIdServicio  = $objServicio->getId();
            $intIdProducto  = $objProducto->getId();
            $intElementoId  = $objServicioTecnico->getElementoId();
            $objElemento    = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")->find($intElementoId);
            
            if(!is_object($objElemento))
            {
                throw new \Exception("No se ha podido obtener el elemento");
            }
            
            $arrayRutas = $this->emInfraestructura->getRepository("schemaBundle:InfoRutaElemento")
                                                  ->findBy(array( "servicioId"    => $objServicio->getId(),
                                                                  "estado"        => "Activo"));
            foreach($arrayRutas as $objRutaElemento)
            {
                $objElementoPadre                       = $objRutaElemento->getElementoId();
                $arrayPeticionesWs                      = array();
                $arrayPeticionesWs['url']               = 'enrutamientoEstaticoPe';
                $arrayPeticionesWs['accion']            = 'eliminar';
                $arrayPeticionesWs['clase_servicio']    = $objProducto->getNombreTecnico();
                $arrayPeticionesWs['vrf']               = $strVrf;
                $arrayPeticionesWs['pe']                = $objElementoPadre->getNombreElemento();
                $arrayPeticionesWs['sw']                = $objElemento->getNombreElemento();
                $arrayPeticionesWs['name_route']        = $objRutaElemento->getNombre();
                if($objRutaElemento->getSubredId())
                {
                    $arrayPeticionesWs['net_lan']       = $objRutaElemento->getSubredId()->getSubred();
                    $arrayPeticionesWs['mask_lan']      = $objRutaElemento->getSubredId()->getMascara();
                }
                else
                {
                    $arrayPeticionesWs['net_lan']       = $objRutaElemento->getRedLan();
                    $arrayPeticionesWs['mask_lan']      = $objRutaElemento->getMascaraRedLan();
                }
                $arrayPeticionesWs['ip_destino']        = $objRutaElemento->getIpId()->getIp();
                $arrayPeticionesWs['distance_admin']    = $objRutaElemento->getDistanciaAdmin();
                $arrayPeticionesWs['option']            = 'E';
                $arrayPeticionesWs['servicio']          = $objProducto->getNombreTecnico();
                $arrayPeticionesWs['login_aux']         = $objServicio->getLoginAux();
                $arrayPeticionesWs['user_name']         = $strUsrCreacion;
                $arrayPeticionesWs['user_ip']           = $strIpCreacion;
                $arrayPeticionesWs['idServicio']        = $intIdServicio;
                $arrayPeticionesWs['idProducto']        = $intIdProducto;

                //Ejecución del método via WS para realizar la configuración del SW
                $arrayRespuestaWs   = $this->networkingScripts->callNetworkingWebService($arrayPeticionesWs);
                $strStatusWs        = $arrayRespuestaWs['status'];
                $strMensajeWs       = $arrayRespuestaWs['mensaje'];
                if($strStatusWs === 'OK')
                {
                    // Liberar Subred
                    if($objRutaElemento->getSubredId())
                    {
                        $arrayParametrosLiberarSubred               = array();
                        $arrayParametrosLiberarSubred['tipoAccion'] = 'liberar';
                        $arrayParametrosLiberarSubred['uso']        = $objRutaElemento->getSubredId()->getUso();
                        $arrayParametrosLiberarSubred['subredId']   = $objRutaElemento->getSubredId()->getId();
                        $arrayParametrosLiberarSubred['elementoId'] = $objRutaElemento->getElementoId()->getId();
                        $arrayParametrosLiberarSubred['mascara']    = $objRutaElemento->getSubredId()->getMascara();

                        $arrayResponseLiberar                       = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')
                                                                                              ->provisioningSubred($arrayParametrosLiberarSubred);

                        if($arrayResponseLiberar['msg'] !== 'OK')
                        {
                            throw new \Exception("Imposible eliminar ruta estatica. problemas con la subred asignada");
                        }
                    }
                    
                    $objRutaElemento->setEstado("Eliminado");
                    $this->emInfraestructura->persist($objRutaElemento);
                    $this->emInfraestructura->flush();

                    $objServicioHistorial = new InfoServicioHistorial();
                    $objServicioHistorial->setServicioId($objServicio);
                    $objServicioHistorial->setObservacion('Se Elimino la ruta <b>'.$objRutaElemento->getNombre().'</b>');
                    $objServicioHistorial->setIpCreacion($strIpCreacion);
                    $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                    $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                    $objServicioHistorial->setEstado($objServicio->getEstado());
                    $this->emComercial->persist($objServicioHistorial);
                    $this->emComercial->flush();
                }
                else
                {
                    throw new \Exception("Problemas con la ejecucion del ws ". $strMensajeWs);
                }
            }  
            $strStatus  = "OK";
            $strMensaje = "Rutas eliminadas satisfactoriamente";
        }
        catch (\Exception $e) 
        {
            $strStatus  = "ERROR";
            $strMensaje = "Error al eliminar rutas: ".$e->getMessage();
        }
        $arrayRespuesta["status"]   = $strStatus;
        $arrayRespuesta["mensaje"]  = $strMensaje;
        return $arrayRespuesta;
    }
    
    /**
     * Función que realiza la cancelación del servicio Internet Small Business con sus ips adicionales
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 17-07-2018
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 27-02-2019 Se modifica el mensaje en caso de error para identificar si es un servicio Small Business o TelcoHome                                                   
     * 
     * @param Array $arrayParametros [
     *                                  "arrayProdIpSb"                     => array de producto Ip
     *                                  "objProductoSB"                     => objeto del producto Small Business
     *                                  "objServicioSBAnterior"             => objeto del servicio Small Business del punto origen
     *                                  "objServicioTecnicoSBAnterior"      => objeto del servicio técnico Small Business del punto origen
     *                                  "objOltAnterior"            |       => objeto del olt del servicio Small Business del punto origen
     *                                  "objInterfaceElementoOltAnterior"   => objeto de la interface del olt del servicio Small Business 
     *                                                                         del punto origen
     *                                  "objModeloOltAnterior"              => marca del olt del Small Business del punto origen
     *                                  "strMarcaOltAnterior"               => marca del olt del Small Business del punto origen
     *                                  "strUsrCreacion"                    => usuario de creación
     *                                  "strIpCreacion"                     => ip de creación
     *                                  "strPrefijoEmpresa"                 => prefijo de la empresa
     *                                  "strCodEmpresa"                     => código de la empresa
     *                                  "strNombreTecnicoInternet"          => nombre técnico del servicio Small Business
     *                                  "strNombreTecnicoIP"                => nombre técnico de las ips adicionales Small Business
     *                                  "strLoginPuntoAnterior"             => login del punto origen
     *                                  "intIdDepartamento"                 => id del departamento en sesión
     *                                  "intIdPersonaEmpresaRol"            => id persona empresa rol en sesión
     *                                  "objAccionCancelar"                 => objeto de la accion cancelar
     *                                  "objMotivoCancelarTraslado"         => objeto del motivo del traslado
     *                               ]
     * @return Array $arrayRespuestaFinal [
     *                                      "strStatus"                 => estado del proceso ejecutado
     *                                      "strMensaje"                => mensaje del proceso ejecutado
     *                                      "intIpsServiciosAnterior"   => número de ips adicionales del punto origen
     *                                    ]
     * 
     */
    public function cancelarTrasladoSB($arrayParametros)
    {
        $strMensaje = "";
        $this->emComercial->beginTransaction();
        $this->emInfraestructura->beginTransaction();
        try
        {
            //Se cancela las ips Small Business del punto anterior
            $arrayRespuestaCancelarIpsAnterior  = $this->cancelarIpsTrasladoSB($arrayParametros);
            $strStatusCancelarIpsAnterior       = $arrayRespuestaCancelarIpsAnterior["strStatus"];
            $intIpsServiciosAnterior            = $arrayRespuestaCancelarIpsAnterior["intIpsServiciosAnterior"];
            if($strStatusCancelarIpsAnterior !== "OK")
            {
                $strMensaje = 'No se ha podido cancelar las ips que se desea trasladar ';
                throw new \Exception($strMensaje.$arrayRespuestaCancelarIpsAnterior["strMensaje"]);
            }
            
            $arrayParametrosCancelarSB          = array(   
                                                            'strOpcion'             => 'Traslado',
                                                            'intIdDepartamento'     => $arrayParametros["intIdDepartamento"],
                                                            'servicio'              => $arrayParametros["objServicioSBAnterior"],
                                                            'servicioTecnico'       => $arrayParametros["objServicioTecnicoSBAnterior"],
                                                            'modeloElemento'        => $arrayParametros["objModeloOltAnterior"],
                                                            'producto'              => $arrayParametros["objProductoSB"],
                                                            'elemento'              => $arrayParametros["objOltAnterior"],
                                                            'interfaceElemento'     => $arrayParametros["objInterfaceElementoOltAnterior"],
                                                            'login'                 => $arrayParametros["strLoginPuntoAnterior"],
                                                            'idEmpresa'             => $arrayParametros["strCodEmpresa"],
                                                            'usrCreacion'           => $arrayParametros["strUsrCreacion"],
                                                            'ipCreacion'            => $arrayParametros["strIpCreacion"],
                                                            'idPersonaEmpresaRol'   => $arrayParametros["intIdPersonaEmpresaRol"],
                                                            'accion'                => $arrayParametros["objAccionCancelar"],
                                                            'motivo'                => $arrayParametros["objMotivoCancelarTraslado"]
                                                        );
            $arrayRespuestaCancelarSBAnterior   = $this->cancelarServicioIsb($arrayParametrosCancelarSB);
            $strStatusCancelarSBAnterior        = $arrayRespuestaCancelarSBAnterior[0]['status'];
            if($strStatusCancelarSBAnterior !== "OK")
            {
                $strMensaje = 'No se ha podido cancelar el servicio '.$arrayParametros["strDescripcionProdPref"].' que se desea trasladar ';
                throw new \Exception($strMensaje.$arrayRespuestaCancelarSBAnterior[0]['mensaje']);
            }
            $strStatus = "OK";
            $this->emInfraestructura->commit();
            $this->emComercial->commit();
            
        }
        catch (\Exception $e)
        {
            $strStatus = "ERROR";
            if ($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->rollback();
            }

            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->rollback();
            }
            
            $this->emInfraestructura->close();
            $this->emComercial->close();
            
            $this->serviceUtil->insertError(    "Telcos+",
                                                "InfoActivarPuertoService->cancelarTrasladoSB",
                                                $e->getMessage(),
                                                $arrayParametros['strUsrCreacion'],
                                                $arrayParametros['strIpCreacion']
                                            );
        }
        
        try
        {
            /**
             * En caso de un error, se verifica si se cancelaron ips adicionales para volverlas a activar
             */
            $strMensajeMiddleware   = "";
            if($strStatus !== "OK" && isset($arrayRespuestaCancelarIpsAnterior["arrayIpsCanceladas"]) 
                && !empty($arrayRespuestaCancelarIpsAnterior["arrayIpsCanceladas"]))
            {
                $intIpsActivasInicial = $intIpsServiciosAnterior - count($arrayRespuestaCancelarIpsAnterior["arrayIpsCanceladas"]);
                foreach($arrayRespuestaCancelarIpsAnterior["arrayIpsCanceladas"] as $arrayIpCancelada)
                {
                    $arrayIpCancelada["tipoError"]          = "ERROR TRASLADO IPSB";
                    $arrayIpCancelada["ip_fijas_activas"]   = $intIpsActivasInicial;
                    $arrayRespuestaActivarIp                = $this->activarService->activarIpFijaAdicional($arrayIpCancelada);
                    if($arrayRespuestaActivarIp["status"] !== "OK")
                    {
                        $strMensajeMiddleware .= $arrayRespuestaActivarIp["mensaje"] . " ";
                    }
                }
            }
            if(!empty($strMensajeMiddleware))
            {
                $strMensajeMiddleware = '<br>Mensaje Middleware al activar Ips por error en cancelación de ip:'.$strMensajeMiddleware;
                throw new \Exception($strMensajeMiddleware);
            }
        } 
        catch (\Exception $ex) 
        {
            $this->serviceUtil->insertError(    "Telcos+",
                                                "InfoActivarPuertoService->cancelarTrasladoSB",
                                                $ex->getMessage(),
                                                $arrayParametros['strUsrCreacion'],
                                                $arrayParametros['strIpCreacion']
                                            );
        }
        $arrayRespuestaFinal = array('strStatus' => $strStatus, 'strMensaje' => $strMensaje, 'intIpsServiciosAnterior' => $intIpsServiciosAnterior);
        return $arrayRespuestaFinal;
    }
    
    /**
     * Función que realiza la cancelación de las ips Small Business al realizar un traslado
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 18-07-2018
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 04-05-2020 Se elimina la obtención de parámetros strNombreTecnicoInternet y strNombreTecnicoIP, ya que no son enviados a esta
     *                          función, debido a la reestructuración de servicios Small Business
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.2 24-03-2021 Se valida si el servicio Anterior tiene IP privada
     * 
     * @param Array $arrayParametros [
     *                                  "arrayProdIpSb"                     => array de producto Ip
     *                                  "objProductoSB"                     => objeto del producto Small Business
     *                                  "objServicioSBAnterior"             => objeto del servicio Small Business del punto origen
     *                                  "objServicioTecnicoSBAnterior"      => objeto del servicio técnico Small Business del punto origen
     *                                  "objOltAnterior"            |       => objeto del olt del servicio Small Business del punto origen
     *                                  "objInterfaceElementoOltAnterior"   => objeto de la interface del olt del servicio Small Business 
     *                                                                         del punto origen
     *                                  "objModeloOltAnterior"              => marca del olt del Small Business del punto origen
     *                                  "strMarcaOltAnterior"               => marca del olt del Small Business del punto origen
     *                                  "strUsrCreacion"                    => usuario de creación
     *                                  "strIpCreacion"                     => ip de creación
     *                                  "strPrefijoEmpresa"                 => prefijo de la empresa
     *                                  "strCodEmpresa"                     => código de la empresa
     *                                  "strLoginPuntoAnterior"             => login del punto origen
     *                                  "intIdDepartamento"                 => id del departamento en sesión
     *                                  "intIdPersonaEmpresaRol"            => id persona empresa rol en sesión
     *                                  "objAccionCancelar"                 => objeto de la accion cancelar
     *                                  "objMotivoCancelarTraslado"         => objeto del motivo del traslado
     *                               ]
     * @return Array $arrayRespuestaFinal [
     *                                      "strStatus"                 => estado del proceso ejecutado
     *                                      "strMensaje"                => mensaje del proceso ejecutado
     *                                      "intIpsServiciosAnterior"   => número de ips adicionales del punto origen
     *                                      "arrayIpsCanceladas"        => array con la información de las ips canceladas que se podrá usar 
     *                                                                     en caso de reverso de la cancelación
     *                                    ]
     * 
     */
    public function cancelarIpsTrasladoSB($arrayParametros)
    {
        $arrayProdIpSb                      = $arrayParametros["arrayProdIpSb"];
        $objProductoSB                      = $arrayParametros["objProductoSB"];
        $objServicioSBAnterior              = $arrayParametros["objServicioSBAnterior"];
        $objServicioTecnicoSBAnterior       = $arrayParametros["objServicioTecnicoSBAnterior"];
        $objInterfaceElementoOltAnterior    = $arrayParametros["objInterfaceElementoOltAnterior"];
        $strUsrCreacion                     = $arrayParametros["strUsrCreacion"];
        $strIpCreacion                      = $arrayParametros["strIpCreacion"];
        $strPrefijoEmpresa                  = $arrayParametros["strPrefijoEmpresa"];
        $strCodEmpresa                      = $arrayParametros["strCodEmpresa"];
        $strMarcaOltAnterior                = $arrayParametros["strMarcaOltAnterior"];
        $objAccionCancelar                  = $arrayParametros["objAccionCancelar"];
        $objMotivoCancelarTraslado          = $arrayParametros["objMotivoCancelarTraslado"];
        $intIpsActivasServicioAnterior      = 0;
        $strMensaje                         = "";
        $arrayIpsCanceladas                 = array();
        try
        {
            $objPuntoAnterior                   = $objServicioSBAnterior->getPuntoId();
            $arrayServiciosAnteriores           = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                    ->findBy(array( "puntoId"   => $objPuntoAnterior->getId(), 
                                                                                    "estado"    => "Activo"));
            $arrayDatosIpsServicioAnterior      = $this->servicioGeneral->getInfoIpsFijaPunto(  $arrayServiciosAnteriores, $arrayProdIpSb,
                                                                                                $objServicioSBAnterior, 'Activo', 'Activo',
                                                                                                $objProductoSB);
            $intIpsActivasServicioAnterior      = $arrayDatosIpsServicioAnterior['ip_fijas_activas'];
            $arrayIpsActivasServicioAnterior    = $arrayDatosIpsServicioAnterior['valores'];
            if($intIpsActivasServicioAnterior > 0)
            {
                foreach($arrayIpsActivasServicioAnterior as $arrayIpCancelar)
                {
                    $intIdServicioIpAnterior    = $arrayIpCancelar['id_servicio'];
                    $strMacServicioIpAnterior   = $arrayIpCancelar['mac'];
                    $intIdProducto              = $arrayIpCancelar['productoId'];
                    
                    //Consultar si el servicio anterior tiene IP Privada
                    $strTipoIp = 'FIJA';
                    $objCaracteristicaIpPrivada = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                          ->findOneBy(array( "descripcionCaracteristica" => "TIPO_ENRUTAMIENTO"));
                    if(is_object($objCaracteristicaIpPrivada))
                    {
                        $objProductoIp = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                     ->find($intIdProducto);

                        $objProdCaracteristicaIpPrivada = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                      ->findOneBy(array( "productoId"       => $objProductoIp->getId(), 
                                                                                         "caracteristicaId" => $objCaracteristicaIpPrivada->getId()
                                                                                       )
                                                                                 );
                        if(is_object($objProdCaracteristicaIpPrivada))
                        {
                            $objInfoServicioProdCaractIpPrivada = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                            ->findOneBy(array("servicioId"                 => $intIdServicioIpAnterior, 
                                                                              "productoCaracterisiticaId" => $objProdCaracteristicaIpPrivada->getId()
                                                                             )
                                                                       );
                            if (is_object($objInfoServicioProdCaractIpPrivada))
                            {
                                $strIpPrivada = ($objInfoServicioProdCaractIpPrivada)?$objInfoServicioProdCaractIpPrivada->getValor():"";
                                $strTipoIp = strtoupper($strIpPrivada);
                            }
                        }
                    }
                    
                    $objServicioIpAnterior      = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicioIpAnterior);
                    $objInfoIpServicioAnterior  = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                                          ->findOneBy(array("servicioId"   => $intIdServicioIpAnterior, 
                                                                                            "tipoIp"       => $strTipoIp, 
                                                                                            "estado"       => "Activo"));
                    if(!is_object($objServicioIpAnterior) || !is_object($objInfoIpServicioAnterior))
                    {
                        throw new \Exception('No existe ip para el servicio');
                    }
                    
                    $arrayIpCancelarMiddleware  = array('servicio'                  => $objServicioIpAnterior,
                                                        'servicioInternet'          => $objServicioSBAnterior,
                                                        'servicioTecnico'           => $objServicioTecnicoSBAnterior,
                                                        'interfaceElemento'         => $objInterfaceElementoOltAnterior,
                                                        'producto'                  => $objProductoSB,
                                                        'ipFija'                    => $objInfoIpServicioAnterior,
                                                        'macIpFija'                 => $strMacServicioIpAnterior,
                                                        'idEmpresa'                 => $strCodEmpresa,
                                                        'usrCreacion'               => $strUsrCreacion,
                                                        'ipCreacion'                => $strIpCreacion,
                                                        'strPrefijoEmpresa'         => $strPrefijoEmpresa);
                    $arrayRespuestaCancelarIp   = $this->cancelarIpFijaAdicional($arrayIpCancelarMiddleware);
                    if($arrayRespuestaCancelarIp["status"] !== "OK")
                    {
                        throw new \Exception($arrayRespuestaCancelarIp["mensaje"]);
                    }
                    $arrayIpsCanceladas[]   = array('servicio'                  => $objServicioIpAnterior,
                                                    'servicioInternet'          => $objServicioSBAnterior,
                                                    'servicioTecnico'           => $objServicioTecnicoSBAnterior,
                                                    'interfaceElemento'         => $objInterfaceElementoOltAnterior,
                                                    'producto'                  => $objProductoSB,
                                                    'ipFija'                    => $objInfoIpServicioAnterior,
                                                    'macIpFija'                 => $strMacServicioIpAnterior,
                                                    'idEmpresa'                 => $strCodEmpresa,
                                                    'tieneIpFijaActiva'         => "",
                                                    'controlIpFija'             => "",
                                                    'usrCreacion'               => $strUsrCreacion,
                                                    'ipCreacion'                => $strIpCreacion,
                                                    'strPrefijoEmpresa'         => $strPrefijoEmpresa);
                    $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicioIpAnterior, 
                                                                                    $objProductoSB, 
                                                                                    "MAC", 
                                                                                    $strMacServicioIpAnterior, 
                                                                                    $strUsrCreacion);
                    if($strMarcaOltAnterior !== "HUAWEI")
                    {
                        $objSpcPerfilServicioSBAnterior = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioSBAnterior, 
                                                                                                                    "PERFIL", 
                                                                                                                    $objProductoSB);
                        if(is_object($objSpcPerfilServicioSBAnterior) && count($arrayRespuestaCancelarIp) > 3 
                            && $objSpcPerfilServicioSBAnterior->getValor() != $arrayRespuestaCancelarIp['perfil'])
                        {
                            $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objSpcPerfilServicioSBAnterior, "Eliminado");
                            $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicioSBAnterior, 
                                                                                            $objProductoSB, 
                                                                                            "PERFIL", 
                                                                                            $arrayRespuestaCancelarIp['perfil'], 
                                                                                            $strUsrCreacion);
                        }
                    }
                    $objInfoIpServicioAnterior->setEstado("Eliminado");
                    $this->emInfraestructura->persist($objInfoIpServicioAnterior);
                    $this->emInfraestructura->flush();

                    $objServicioIpAnterior->setEstado("Trasladado");
                    $this->emComercial->persist($objServicioIpAnterior);
                    $this->emComercial->flush();
                    
                    $objServicioHistorial = new InfoServicioHistorial();
                    $objServicioHistorial->setServicioId($objServicioIpAnterior);
                    $objServicioHistorial->setObservacion('Se Traslado el Servicio');
                    $objServicioHistorial->setEstado('Trasladado');
                    $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                    $objServicioHistorial->setIpCreacion($strIpCreacion);
                    $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                    $objServicioHistorial->setAccion($objAccionCancelar->getNombreAccion());
                    $objServicioHistorial->setMotivoId($objMotivoCancelarTraslado->getId());
                    $this->emComercial->persist($objServicioHistorial);
                    $this->emComercial->flush();
                }
            }
            $strStatus = "OK";
        }
        catch (\Exception $e) 
        {
            $strStatus  = "ERROR";
            $strMensaje = $e->getMessage();
        }
        $arrayRespuestaFinal = array(   'strStatus'                 => $strStatus, 
                                        'strMensaje'                => $strMensaje, 
                                        'intIpsServiciosAnterior'   => $intIpsActivasServicioAnterior,
                                        'arrayIpsCanceladas'        => $arrayIpsCanceladas);
        return $arrayRespuestaFinal;
    }
    
    
    /**
     * Función que ejecuta facturación por cancelación de servicios.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 - 10-09-2018
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.1 - 20-08-2019 Se captura el valor del precio del equipo para realizar el cálculo en base al valor amortizado.
     * 
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 1.2 - 04-07-2020 Se agregan en la facturación las NDI por diferidos pendientes de generarse.
     * 
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.3 18-08-2020 - Se añade parámetro Tipo de Proceso, para identificar el proceso de PreCancelación de Deuda Diferida.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.4  16-03-2021 Se agrega lectura de parametros para facturación de cancelación de servicio netlifecam.
     * 
     * @author Jonathan Mazon Sanchez <jmazon@telconet.ec>
     * @version 1.5  28-09-2021 Se agrega lectura de parametros para facturación y creacion de tarea rapida al cancelar el servicio ECDF.
     * 
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.6  07-09-2022 Se modifica función para recorrer los arreglos de valores recibidos y crear las caracteristicas correspondientes
     *                          para generar el detalle de la factura.
     *
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.6  11-04-2022 Se agrega validación para seteo de usuario de creación de factura para procesos de traslados diferente tecnología
     * 
     * @author Hector Lozano <jbozada@telconet.ec>
     * @version 1.7  21-09-2022 Se modifica función para recorrer parametros recibidos como tipo Arreglo($arrayGeneralDescuentos, 
     *                          $arrayGeneralProdFacturar) y agregar la caracteristica correcpondiente.
     * 
     * @return $strRespuesta
     */
    public function ejecutarFacturacionCancelacion($arrayParametros)
    {
        
        
        $strIpCliente         = $arrayParametros['strIpCliente'];
        $strEmpresaCod        = $arrayParametros['strEmpresaCod']; 
        $strUsrCreacion       = $arrayParametros['strUsrCreacion'];        
        $strPrefijoEmpresa    = $arrayParametros['strPrefijoEmpresa'];
        $intIdServicio        = $arrayParametros['intIdServicio'];
        $floatSubtotal        = $arrayParametros['floatSubtotal'];
        $floatEquipos         = $arrayParametros['floatEquipos'];
        $floatInstalacion     = $arrayParametros['floatInstalacion'];
        $floatSubtotalnc      = $arrayParametros['floatSubtotalnc'];      
        $strCaracteristicas   = $arrayParametros['strCaracteristicas'];
        $objServiceUtil       = $arrayParametros['serviceUtil'];
        $intIdPersonaEmpresaRol   = $arrayParametros['intIdPersonaEmpresaRol'];
        $arrayEquiposFacturar = explode("-",$strCaracteristicas);
        
        $strParametroEquipos  = 'RETIRO_EQUIPOS_SOPORTE';
        $strProceso           = 'FACTURACION_RETIRO_EQUIPOS';
        $strModulo            = 'FINANCIERO';
        
        $arrayParametrosFact                           = array();
        $arrayParametrosFact['intMotivoId']            = null;
        $arrayParametrosFact['strMsnError']            = str_pad(' ', 30); 
        $arrayParametrosFact['strEmpresaCod']          = $strEmpresaCod;
        $arrayParametrosFact['strEstadoSolicitud']     = 'Pendiente';
        if ($strUsrCreacion === "telcosTraslada")
        {
            $arrayParametrosFact['strUsrCreacion'] = $strUsrCreacion;    
        }
        else
        {
            $arrayParametrosFact['strUsrCreacion'] = 'telcos_cancel_volun';
        }
        $arrayParametrosFact['strDescTipoSolicitud']   = 'SOLICITUD CANCELACION VOLUNTARIA';         
        $floatSubtotalNDI     = $arrayParametros['floatSubtotalNDI'];
        
        $arrayGeneralDescuentos   = json_decode($arrayParametros['arrayGeneralDescuentos'],true);
        $arrayGeneralProdFacturar = json_decode($arrayParametros['arrayGeneralProdFacturar'],true);
        $strCreaNC                = $arrayParametros['strCreaNC'];
        

        try
        {
            $this->emComercial->getConnection()->beginTransaction();
            
            $objMotivoCancelacion   =   $this->emComercial->getRepository('schemaBundle:AdmiMotivo')
                                                          ->findOneBy(array("nombreMotivo" => "Cancelacion Voluntaria",
                                                                            "estado"       => "Activo")); 
            
            if(is_object($objMotivoCancelacion))
            {
                $arrayParametrosFact['intMotivoId'] = $objMotivoCancelacion->getId();                    
            }            
                            
            $objInfoServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                 ->find($intIdServicio);

            if(is_object($objInfoServicio))
            {
                
                
                $objSolicitudCancelVoluntaria = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                                  ->findOneBy(array("descripcionSolicitud" => "SOLICITUD CANCELACION VOLUNTARIA",
                                                                                    "estado"               => "Activo"));            
                

                if(is_object($objSolicitudCancelVoluntaria))
                {
                    $objDetalleSolCancelVoluntaria = new InfoDetalleSolicitud();
                    $objDetalleSolCancelVoluntaria->setServicioId($objInfoServicio);
                    $objDetalleSolCancelVoluntaria->setTipoSolicitudId($objSolicitudCancelVoluntaria);
                    $objDetalleSolCancelVoluntaria->setObservacion("Se crea Solicitud de Cancelacion Voluntaria");
                    $objDetalleSolCancelVoluntaria->setPrecioDescuento($floatSubtotal);
                    if(is_object($objMotivoCancelacion))
                    {
                        $objDetalleSolCancelVoluntaria->setMotivoId($objMotivoCancelacion->getId());                    
                    }
                    $objDetalleSolCancelVoluntaria->setFeCreacion(new \DateTime('now'));
                    $objDetalleSolCancelVoluntaria->setUsrCreacion($strUsrCreacion);
                    $objDetalleSolCancelVoluntaria->setEstado('Pendiente');
                    $this->emComercial->persist($objDetalleSolCancelVoluntaria);
                    $this->emComercial->flush(); 
                    
                    
                   //Se inserta el historial de la solicitud por cancelación voluntaria.
                    $objInfoDetalleSolFactHistorial = new InfoDetalleSolHist();
                    $objInfoDetalleSolFactHistorial->setDetalleSolicitudId($objDetalleSolCancelVoluntaria);
                    $objInfoDetalleSolFactHistorial->setEstado($objDetalleSolCancelVoluntaria->getEstado());
                    $objInfoDetalleSolFactHistorial->setFeCreacion(new \DateTime('now'));
                    $objInfoDetalleSolFactHistorial->setUsrCreacion($strUsrCreacion);
                    $objInfoDetalleSolFactHistorial->setObservacion("Se crea Solicitud de Cancelacion Voluntaria");
                    $objInfoDetalleSolFactHistorial->setIpCreacion($strIpCliente);
                    $this->emComercial->persist($objInfoDetalleSolFactHistorial);
                    $this->emComercial->flush(); 
                    
                    //Se obtienen las características asociada a los equipos a facturar.
                  
                    if(count($arrayEquiposFacturar) > 0 && $floatEquipos > 0)
                    {
                        
                        $objAdmiCaractEquipo = $this->emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                                                 ->findOneBy(array("descripcionCaracteristica" => "EQUIPOS",
                                                                                   "estado"                    => "Activo"));
                        
                        //Se inserta la característica principal para equipos.
                        $objSolCaractEquipos = new InfoDetalleSolCaract();
                        $objSolCaractEquipos->setCaracteristicaId($objAdmiCaractEquipo);
                        $objSolCaractEquipos->setDetalleSolicitudId($objDetalleSolCancelVoluntaria);
                        $objSolCaractEquipos->setValor($floatEquipos);
                        $objSolCaractEquipos->setEstado("Activo");
                        $objSolCaractEquipos->setUsrCreacion($strUsrCreacion);
                        $objSolCaractEquipos->setFeCreacion(new \DateTime('now'));
                        $this->emComercial->persist($objSolCaractEquipos);
                        $this->emComercial->flush(); 
                        
                        foreach($arrayEquiposFacturar as $strCaractCantidad):
                            
                            $arrayPrecioCantidad         = explode("/",$strCaractCantidad);
                        
                            $intParamtEquipoFacturarId   = intval($arrayPrecioCantidad[0]);
                            
                            $intCantidad                 = intval($arrayPrecioCantidad[1]);
                            
                            $floatPrecio                 = floatval($arrayPrecioCantidad[2]);

                            $objAdmiCaracteristicaParamt = $this->emComercial->getRepository("schemaBundle:AdmiParametroDet")
                                                                             ->find($intParamtEquipoFacturarId);
                            
                            if(is_object($objAdmiCaracteristicaParamt))
                            {
                                $intCaracteristicaId = (int) ($objAdmiCaracteristicaParamt->getValor3());
                                
                                $strValor4 = $objAdmiCaracteristicaParamt->getValor4();

                                if(!isset($strValor4))
                                {
                                    $strValor4 = '';
                                }

                                $objAdmiCaracteristicaEquipo = $this->emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                                                                 ->find($intCaracteristicaId);
                                if(is_object($objAdmiCaracteristicaEquipo))
                                {
                                    $arrayParametrosResultado = $this->emComercial->getRepository("schemaBundle:AdmiParametroDet")
                                                                                  ->get($strParametroEquipos,
                                                                                        $strModulo,
                                                                                        $strProceso,
                                                                                        '',
                                                                                        '',
                                                                                        '',
                                                                                        $intCaracteristicaId,
                                                                                        $strValor4,
                                                                                        '',
                                                                                        $strEmpresaCod,
                                                                                        null
                                                                                        );

                                    if( $arrayParametrosResultado )
                                    {
                                        foreach( $arrayParametrosResultado as $arrayParametroDet )
                                        {
                                            if(isset($arrayParametroDet['valor2']) && !empty($arrayParametroDet['valor2']))
                                            {
                                                $floatPrecio = strval($floatPrecio * $intCantidad);
                                            }
                                        }
                                    }                            
                                    //Se inserta la característica por cada equipo
                                    $objSolCaractEquipo = new InfoDetalleSolCaract();
                                    $objSolCaractEquipo->setCaracteristicaId($objAdmiCaracteristicaEquipo);
                                    $objSolCaractEquipo->setDetalleSolicitudId($objDetalleSolCancelVoluntaria);
                                    $objSolCaractEquipo->setDetalleSolCaractId($objSolCaractEquipos->getId());
                                    $objSolCaractEquipo->setValor($floatPrecio);
                                    $objSolCaractEquipo->setEstado("Facturable");
                                    $objSolCaractEquipo->setUsrCreacion($strUsrCreacion);
                                    $objSolCaractEquipo->setFeCreacion(new \DateTime('now'));
                                    $this->emComercial->persist($objSolCaractEquipo);
                                    $this->emComercial->flush();                               
                                }
                                
                            }

                        endforeach;                        
                        
                    }
                    
                                       
                    if($floatInstalacion > 0)
                    {
                        $objAdmiCaractInstalacion = $this->emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                                                      ->findOneBy(array("descripcionCaracteristica" => "INSTALACION",
                                                                                        "estado"                    => "Activo"));
                        
                        //Se inserta la característica por instalacion
                        $objSolCaractInstalacion = new InfoDetalleSolCaract();
                        $objSolCaractInstalacion->setCaracteristicaId($objAdmiCaractInstalacion);
                        $objSolCaractInstalacion->setDetalleSolicitudId($objDetalleSolCancelVoluntaria);
                        $objSolCaractInstalacion->setValor($floatInstalacion);
                        $objSolCaractInstalacion->setEstado("Facturable");
                        $objSolCaractInstalacion->setUsrCreacion($strUsrCreacion);
                        $objSolCaractInstalacion->setFeCreacion(new \DateTime('now'));
                        $this->emComercial->persist($objSolCaractInstalacion);
                        $this->emComercial->flush();                       
                        
                    }
                    else
                    {
                        $floatInstalacion = 0;   
                    } 
                                       
                    // Ingreso de Solicitud de Nota de Crédito
               
                    if($strCreaNC=='S')
                    {                  

                        $objSolicitudNc = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                            ->findOneBy(array("descripcionSolicitud" => "SOLICITUD NOTA CREDITO",
                                                                              "estado"               => "Activo"));            

                        if(is_object($objSolicitudNc))
                        {
                            $objDetalleSolNc = new InfoDetalleSolicitud();
                            $objDetalleSolNc->setServicioId($objInfoServicio);
                            $objDetalleSolNc->setTipoSolicitudId($objSolicitudNc);
                            $objDetalleSolNc->setObservacion("Se crea Solicitud de Nota de Credito");
                            $objDetalleSolNc->setPrecioDescuento($floatSubtotalnc);
                            if(is_object($objMotivoCancelacion))
                            {
                                $objDetalleSolNc->setMotivoId($objMotivoCancelacion->getId());                    
                            }
                            $objDetalleSolNc->setFeCreacion(new \DateTime('now'));
                            $objDetalleSolNc->setUsrCreacion($strUsrCreacion);
                            $objDetalleSolNc->setEstado('Pendiente');
                            $this->emComercial->persist($objDetalleSolNc);
                            $this->emComercial->flush(); 


                           //Se inserta el historial de la solicitud de Nota de Credito.
                            $objInfoDetalleSolFactHistNc = new InfoDetalleSolHist();
                            $objInfoDetalleSolFactHistNc->setDetalleSolicitudId($objDetalleSolNc);
                            $objInfoDetalleSolFactHistNc->setEstado($objDetalleSolNc->getEstado());
                            $objInfoDetalleSolFactHistNc->setFeCreacion(new \DateTime('now'));
                            $objInfoDetalleSolFactHistNc->setUsrCreacion($strUsrCreacion);
                            $objInfoDetalleSolFactHistNc->setObservacion("Se crea Solicitud de Nota de Credito");
                            $objInfoDetalleSolFactHistNc->setIpCreacion($strIpCliente);
                            $this->emComercial->persist($objInfoDetalleSolFactHistNc);
                            $this->emComercial->flush(); 

                            // Se agrega caracteristica APLICA NC a la Solicitud de Cancelación Voluntaria

                            $objAdmiCaractFactNc  = $this->emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                                                      ->findOneBy(array("descripcionCaracteristica" => "SOLICITUD NOTA CREDITO",
                                                                                        "estado"                    => "Activo"));
                            if(is_object($objAdmiCaractFactNc))
                            {
                                $objSolCaractFactNc = new InfoDetalleSolCaract();
                                $objSolCaractFactNc->setCaracteristicaId($objAdmiCaractFactNc);
                                $objSolCaractFactNc->setDetalleSolicitudId($objDetalleSolCancelVoluntaria);
                                $objSolCaractFactNc->setValor($objDetalleSolNc->getId());
                                $objSolCaractFactNc->setEstado("Activo");
                                $objSolCaractFactNc->setUsrCreacion($strUsrCreacion);
                                $objSolCaractFactNc->setFeCreacion(new \DateTime('now'));
                                $this->emComercial->persist($objSolCaractFactNc);
                                $this->emComercial->flush(); 
                            }                        
                        }                    
                    }
                    
                    
                    if(count($arrayGeneralDescuentos) > 0)
                    {
                       foreach( $arrayGeneralDescuentos as $arrayDescuentos )
                       {
                           $strProductoDesc    = $arrayDescuentos['nombreProducto'];
                           $intIdProducto      = $arrayDescuentos['idProducto'];
                           $floatDescuento     = $arrayDescuentos['descPromo'];
                           $floatDescuentoAdic = $arrayDescuentos['descPromoAdicional'];
                           
                           if($strProductoDesc=='INTERNET')
                           {
                               if($floatDescuento > 0)
                               {
                                   $objAdmiCaractDescto = $this->emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                                                     ->findOneBy(array("descripcionCaracteristica" => "DESCUENTOS",
                                                                                       "estado"                    => "Activo"));

                                    //Se inserta la característica por instalacion
                                    $objSolCaractDescuento = new InfoDetalleSolCaract();
                                    $objSolCaractDescuento->setCaracteristicaId($objAdmiCaractDescto);
                                    $objSolCaractDescuento->setDetalleSolicitudId($objDetalleSolCancelVoluntaria);
                                    $objSolCaractDescuento->setValor($floatDescuento);
                                    $objSolCaractDescuento->setEstado("Facturable");
                                    $objSolCaractDescuento->setUsrCreacion($strUsrCreacion);
                                    $objSolCaractDescuento->setFeCreacion(new \DateTime('now'));
                                    $this->emComercial->persist($objSolCaractDescuento);
                                    $this->emComercial->flush();
                               }
                               else
                               {
                                   $floatDescuento = 0;   
                               }

                               
                               if($floatDescuentoAdic > 0)
                               {
                                    $objAdmiCaractDescto = $this->emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                                                             ->findOneBy(array("descripcionCaracteristica" => "DESCUENTO ADICIONAL",
                                                                                               "estado"                    => "Activo"));

                                    //Se inserta la característica por instalacion
                                    $objSolCaractDescuento = new InfoDetalleSolCaract();
                                    $objSolCaractDescuento->setCaracteristicaId($objAdmiCaractDescto);
                                    $objSolCaractDescuento->setDetalleSolicitudId($objDetalleSolCancelVoluntaria);
                                    $objSolCaractDescuento->setValor($floatDescuentoAdic);
                                    $objSolCaractDescuento->setEstado("Facturable");
                                    $objSolCaractDescuento->setUsrCreacion($strUsrCreacion);
                                    $objSolCaractDescuento->setFeCreacion(new \DateTime('now'));
                                    $this->emComercial->persist($objSolCaractDescuento);
                                    $this->emComercial->flush();                       

                                }
                                else
                                {   
                                    $floatDescuentoAdic = 0;   
                                }    
                           }
                           else
                           {
                                $floatSubtotalPromo = $floatDescuento + $floatDescuentoAdic;
                               
                                if($floatSubtotalPromo > 0)
                                {
                                    $arrayCaractProducto = $this->emComercial->getRepository("schemaBundle:InfoServicioTecnico")
                                                                       ->getCaractProducto(array("idProducto" => $intIdProducto));
                                
                                    $intIdcaracteristica = (int)$arrayCaractProducto['idCaractProducto'];
                                    
                                    if($intIdcaracteristica != null || $intIdcaracteristica != "")
                                    {
                                        $objAdmiCaractDcto =$this->emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                                                              ->find($intIdcaracteristica);   
                                    }
                                    else
                                    {
                                        $objAdmiCaractDcto = $this->emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                                                     ->findOneBy(array("descripcionCaracteristica" => "DESCUENTOS",
                                                                                       "estado"                    => "Activo"));
                                        
                                    }                                 
                                   
                                    //Se inserta la característica por instalacion
                                    $objSolCaractDescuento = new InfoDetalleSolCaract();
                                    $objSolCaractDescuento->setCaracteristicaId($objAdmiCaractDcto);
                                    $objSolCaractDescuento->setDetalleSolicitudId($objDetalleSolCancelVoluntaria);
                                    $objSolCaractDescuento->setValor($floatSubtotalPromo);
                                    $objSolCaractDescuento->setEstado("Facturable");
                                    $objSolCaractDescuento->setUsrCreacion($strUsrCreacion);
                                    $objSolCaractDescuento->setFeCreacion(new \DateTime('now'));
                                    $this->emComercial->persist($objSolCaractDescuento);
                                    $this->emComercial->flush();
                                }
         
                           }
                            
                            
                            $floatPorcInstalacion = $arrayDescuentos['porDescInstNC'];

                            if($floatInstalacion > 0 && $floatPorcInstalacion > 0)
                            {

                                $objAdmiCaractInst = $this->emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                                                       ->findOneBy(array("descripcionCaracteristica" => "INSTALACION",
                                                                                         "estado"                    => "Activo"));

                                $floatValorNcInst = round((($floatInstalacion*$floatPorcInstalacion)/100), 2);

                                //Se inserta la característica principal por el valor de instalacion.
                                if(is_object($objAdmiCaractInst))
                                {
                                    $objSolCaractInstNc = new InfoDetalleSolCaract();
                                    $objSolCaractInstNc->setCaracteristicaId($objAdmiCaractInst);
                                    $objSolCaractInstNc->setDetalleSolicitudId($objDetalleSolNc);
                                    $objSolCaractInstNc->setValor($floatValorNcInst);
                                    $objSolCaractInstNc->setEstado("Activo");
                                    $objSolCaractInstNc->setUsrCreacion($strUsrCreacion);
                                    $objSolCaractInstNc->setFeCreacion(new \DateTime('now'));
                                    $this->emComercial->persist($objSolCaractInstNc);
                                    $this->emComercial->flush();
                                }

                                $objAdmiCaractPorcInst = $this->emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                                              ->findOneBy(array("descripcionCaracteristica" => "PORCENTAJE INSTALACION NC",
                                                                                "estado"                    => "Activo")); 

                                //Se inserta la característica  por el valor del porcentaje aplicado a la instalación.
                                if(is_object($objAdmiCaractPorcInst))
                                {
                                    $objSolCaractPorInstNc = new InfoDetalleSolCaract();
                                    $objSolCaractPorInstNc->setCaracteristicaId($objAdmiCaractPorcInst);
                                    $objSolCaractPorInstNc->setDetalleSolicitudId($objDetalleSolNc);
                                    $objSolCaractPorInstNc->setDetalleSolCaractId($objSolCaractInstNc->getId());
                                    $objSolCaractPorInstNc->setValor($floatPorcInstalacion);
                                    $objSolCaractPorInstNc->setEstado("Activo");
                                    $objSolCaractPorInstNc->setUsrCreacion($strUsrCreacion);
                                    $objSolCaractPorInstNc->setFeCreacion(new \DateTime('now'));
                                    $this->emComercial->persist($objSolCaractPorInstNc);
                                    $this->emComercial->flush();
                                }

                            } 
                            
                            $floatPorcDescuento = $arrayDescuentos['porDescPromoNC'];
                            
                            if($floatDescuento > 0 && $floatPorcDescuento > 0)
                            {                              
                                
                                $floatValorNcDcto = round((($floatDescuento*$floatPorcDescuento)/100), 2);
                                
                                if($strProductoDesc=='INTERNET')
                                {
                                    $objAdmiCaractDcto = $this->emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                                                       ->findOneBy(array("descripcionCaracteristica" => "DESCUENTOS",
                                                                                         "estado"                    => "Activo"));
                                    
                                }
                                else
                                {
                                    $arrayCaractProducto = $this->emComercial->getRepository("schemaBundle:InfoServicioTecnico")
                                                                       ->getCaractProducto(array("idProducto" => $intIdProducto));
                                
                                    $intIdcaracteristica = (int)$arrayCaractProducto['idCaractProducto'];

                                    $objAdmiCaractDcto = $this->emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                                                           ->find($intIdcaracteristica); 
                                }
                                
                                //Se inserta la característica principal por valor de descuentos.
                                if(is_object($objAdmiCaractDcto))
                                {
                                    $objSolCaractDctoNc = new InfoDetalleSolCaract();
                                    $objSolCaractDctoNc->setCaracteristicaId($objAdmiCaractDcto);
                                    $objSolCaractDctoNc->setDetalleSolicitudId($objDetalleSolNc);
                                    $objSolCaractDctoNc->setValor($floatValorNcDcto);
                                    $objSolCaractDctoNc->setEstado("Activo");
                                    $objSolCaractDctoNc->setUsrCreacion($strUsrCreacion);
                                    $objSolCaractDctoNc->setFeCreacion(new \DateTime('now'));
                                    $this->emComercial->persist($objSolCaractDctoNc);
                                    $this->emComercial->flush(); 
                                }

                                $objAdmiCaractPorcDcto = $this->emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                                              ->findOneBy(array("descripcionCaracteristica" => "PORCENTAJE DESCUENTO NC",
                                                                                "estado"                    => "Activo"));

                                //Se inserta la característica  por el valor del porcentaje aplicado al descuento.
                                if(is_object($objAdmiCaractPorcDcto))
                                {
                                    $objSolCaractPorDctoNc = new InfoDetalleSolCaract();
                                    $objSolCaractPorDctoNc->setCaracteristicaId($objAdmiCaractPorcDcto);
                                    $objSolCaractPorDctoNc->setDetalleSolicitudId($objDetalleSolNc);
                                    $objSolCaractPorDctoNc->setDetalleSolCaractId($objSolCaractDctoNc->getId());
                                    $objSolCaractPorDctoNc->setValor($floatPorcDescuento);
                                    $objSolCaractPorDctoNc->setEstado("Activo");
                                    $objSolCaractPorDctoNc->setUsrCreacion($strUsrCreacion);
                                    $objSolCaractPorDctoNc->setFeCreacion(new \DateTime('now'));
                                    $this->emComercial->persist($objSolCaractPorDctoNc);
                                    $this->emComercial->flush();
                                }                               

                            }

                       }                    
                        
                    }
                    
         
                    if(count($arrayGeneralProdFacturar) > 0)
                    {
                       foreach( $arrayGeneralProdFacturar as $arrayProdFacturar )
                       {
                           $strProducto=$arrayProdFacturar['nombreProducto'];
                           
                           if($strProducto =='NETLIFECLOUD' && $arrayProdFacturar['valorFacturar']>0)
                           {
                                $objAdmiCaractNetlifeCloud = $this->emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                                                               ->findOneBy(array("descripcionCaracteristica" => "NETLIFECLOUD",
                                                                                                 "estado"                    => "Activo"));

                                //Se inserta la característica por instalacion
                                $objSolCaractNetlifeCloud = new InfoDetalleSolCaract();
                                $objSolCaractNetlifeCloud->setCaracteristicaId($objAdmiCaractNetlifeCloud);
                                $objSolCaractNetlifeCloud->setDetalleSolicitudId($objDetalleSolCancelVoluntaria);
                                $objSolCaractNetlifeCloud->setValor($arrayProdFacturar['valorFacturar']);
                                $objSolCaractNetlifeCloud->setEstado("Facturable");
                                $objSolCaractNetlifeCloud->setUsrCreacion($strUsrCreacion);
                                $objSolCaractNetlifeCloud->setFeCreacion(new \DateTime('now'));
                                $this->emComercial->persist($objSolCaractNetlifeCloud);
                                $this->emComercial->flush();                       

                            }        
                           
                            if($strProducto =='NETLIFEASSISTANCE' && $arrayProdFacturar['valorFacturar']>0)
                            {
                                $objAdmiCaractNetlifeAssistance = $this->emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                                                            ->findOneBy(array("descripcionCaracteristica" => "NETLIFEASSISTANCE",
                                                                                              "estado"                    => "Activo"));
                        
                                //Se inserta la característica por instalacion
                                $objSolCaractNetlfAssistance = new InfoDetalleSolCaract();
                                $objSolCaractNetlfAssistance->setCaracteristicaId($objAdmiCaractNetlifeAssistance);
                                $objSolCaractNetlfAssistance->setDetalleSolicitudId($objDetalleSolCancelVoluntaria);
                                $objSolCaractNetlfAssistance->setValor($arrayProdFacturar['valorFacturar']);
                                $objSolCaractNetlfAssistance->setEstado("Facturable");
                                $objSolCaractNetlfAssistance->setUsrCreacion($strUsrCreacion);
                                $objSolCaractNetlfAssistance->setFeCreacion(new \DateTime('now'));
                                $this->emComercial->persist($objSolCaractNetlfAssistance);
                                $this->emComercial->flush();                      

                            }
                            
                            if($strProducto =='ECDF' && $arrayProdFacturar['valorFacturar']>0)
                            {
                                $objAdmiCaractCanalDelFutbol = $this->emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                                                            ->findOneBy(array("descripcionCaracteristica" => "ELCANALDELFUTBOL",
                                                                                              "estado"                    => "Activo"));
                                if(is_object($objAdmiCaractCanalDelFutbol))
                                {
                                    //Se inserta la característica por instalacion
                                    $objSolCaractCanalDelFutbol = new InfoDetalleSolCaract();
                                    $objSolCaractCanalDelFutbol->setCaracteristicaId($objAdmiCaractCanalDelFutbol);
                                    $objSolCaractCanalDelFutbol->setDetalleSolicitudId($objDetalleSolCancelVoluntaria);
                                    $objSolCaractCanalDelFutbol->setValor($arrayProdFacturar['valorFacturar']);
                                    $objSolCaractCanalDelFutbol->setEstado("Facturable");
                                    $objSolCaractCanalDelFutbol->setUsrCreacion($strUsrCreacion);
                                    $objSolCaractCanalDelFutbol->setFeCreacion(new \DateTime('now'));
                                    $this->emComercial->persist($objSolCaractCanalDelFutbol);
                                    $this->emComercial->flush();                       

                                    //-------------------------CREACION DE TAREA RAPIDA-------------------------------------
                                    $arrayProductosACrearTarea = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                                            ->getOne('PRODUCTO_CANCELACION_TAREA_RAPIDA',//nombre parametro cab
                                                                                'TECNICO', //modulo cab
                                                                                'CREAR_TAREA',//proceso cab
                                                                                'CREAR_TAREA_FLUJO_CANCELAR_VOLUNTARIA_PROD_TV', //descripcion det
                                                                                'ECDF',//valor1
                                                                                '','','','',
                                                                                $strEmpresaCod); //empresa

                                    if(is_array($arrayProductosACrearTarea) && !empty($arrayProductosACrearTarea))
                                    {
                                        $strNombreTarea         = $arrayProductosACrearTarea['valor2'];
                                        $strObservacionTarea    = $arrayProductosACrearTarea['valor3'];
                                        $strNombreProducto      = $arrayProductosACrearTarea['valor4'];
                                        $strNombreClase         = $arrayProductosACrearTarea['valor5'];

                                        if(empty($strNombreProducto) || empty($strNombreTarea) || empty($strObservacionTarea))
                                        {
                                            throw new \Exception("problema al obtener parámetros para la creación de tarea");
                                        }
                                        $objAdmiTarea =  $this->emSoporte->getRepository('schemaBundle:AdmiTarea')
                                                                        ->findOneBy(array("nombreTarea" => $strNombreTarea,
                                                                                        "estado"      => "Activo"));
                                        if(is_object($objAdmiTarea))
                                        {
                                            $strTareaId = $objAdmiTarea->getId();
                                        }
                                        if(!empty($intIdServicio))
                                        {
                                            $objInfoServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
                                            if(is_object($objInfoServicio))
                                            {
                                                $objInfoPunto = $objInfoServicio->getPuntoId();
                                                if(is_object($objInfoPunto))
                                                {
                                                    //verifico el sector
                                                    $objSector = $objInfoPunto->getSectorId();
                                                    if(is_object($objSector))
                                                    {
                                                        //verifico la parroquia
                                                        $objParroquia = $objSector->getParroquiaId();
                                                        if(is_object($objParroquia))
                                                        {
                                                            //verifico el canton
                                                            $objCanton = $objParroquia->getCantonId();
                                                            if(is_object($objCanton))
                                                            {
                                                                $strCantonId       = $objCanton->getId();
                                                                $strRegionServicio = $objCanton->getRegion();
                                                            }
                                                        }
                                                    }
                                                    $objInfoPersonaEmpresaRol=$this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                                   ->find($intIdPersonaEmpresaRol);
                                                }
                                                if(is_object($objInfoPersonaEmpresaRol))
                                                {
                                                    $objInfoPersona          = $objInfoPersonaEmpresaRol->getPersonaId();
                                                    $intIdDepartamentoOrigen = $objInfoPersonaEmpresaRol->getDepartamentoId();
                                                    if(!empty($intIdDepartamentoOrigen))
                                                    {
                                                        $objAdmiDepartamento = $this->emComercial->getRepository('schemaBundle:AdmiDepartamento')
                                                                                        ->find($intIdDepartamentoOrigen);
                                                    }
                                                    if(is_object($objInfoPersona))
                                                    {
                                                        $intIdPersona             = $objInfoPersona->getId();
                                                        $strNombrePersonaAsignada = $objInfoPersona->__toString();
                                                    }
                                                }
                                            }
                                        }

                                        $arrayParametros["strObservacion"]          = str_replace("{{nombre_producto}}", 
                                                                                                    $strNombreProducto, $strObservacionTarea);
                                        $arrayParametros["intTarea"]                = $strTareaId;
                                        $arrayParametros["strTipoAfectado"]         = "Cliente";
                                        $arrayParametros["objPunto"]                = $objInfoPunto;
                                        $arrayParametros["objDepartamento"]         = $objAdmiDepartamento;
                                        $arrayParametros["strCantonId"]             = $strCantonId;
                                        $arrayParametros["strEmpresaCod"]           = $strEmpresaCod;
                                        $arrayParametros["strPrefijoEmpresa"]       = $strPrefijoEmpresa;
                                        $arrayParametros["strUsrCreacion"]          = $strUsrCreacion;
                                        $arrayParametros["strIpCreacion"]           = $strIpCliente;
                                        $arrayParametros["intDetalleSolId"]         = null;
                                        $arrayParametros["intDepartamentoOrigen"]   = $intIdDepartamentoOrigen;
                                        $arrayParametros["strIdPersonaAsig"]        = $intIdPersona;
                                        $arrayParametros["strNombrePersonaAsig"]    = $strNombrePersonaAsignada;
                                        $arrayParametros["strIdPerRolAsig"]         = $intIdPersonaEmpresaRol;
                                        $arrayParametros["strBanderaTraslado"]      = "S";
                                        $arrayParametros["strRegion"]               = $strRegionServicio;
                                        $arrayParametros["nombreClaseDocumento"]    = $strNombreClase;
                                        $arrayParametros["strEstadoActual"]         = "Finalizada";
                                        $arrayParametros["strAccion"]               = "Finalizada";
                                        $arrayParametros["asignadoEnDetSeguimiento"]= "Empleado";

                                        $strNumeroTarea = $this->serviceCambiarPlanService->crearTareaRetiroEquipoPorDemo($arrayParametros);
                                        //Consultar el idDetalle de la tarea
                                        $objInfoComunicacion = $this->emComunicacion->getRepository('schemaBundle:InfoComunicacion')
                                                                                    ->find($strNumeroTarea);

                                        if(is_object($objInfoComunicacion))
                                        {
                                            $intIdDetalle = $objInfoComunicacion->getDetalleId();
                                        }

                                        //Se cierra porque es una tarea rapida
                                        $arrayParametrosHist["intDetalleId"]            = $intIdDetalle;
                                        $arrayParametrosHist["strCodEmpresa"]           = $strEmpresaCod;
                                        $arrayParametrosHist["strUsrCreacion"]          = $strUsrCreacion;
                                        $arrayParametrosHist["strIpCreacion"]           = $strIpCliente;
                                        $arrayParametrosHist["intIdDepartamentoOrigen"] = $intIdDepartamentoOrigen;
                                        $arrayParametrosHist["strEnviaDepartamento"]    = "";
                                        $arrayParametrosHist["strOpcion"]               = "Seguimiento";
                                        $arrayParametrosHist["strObservacion"]          = "Tarea fue Finalizada Obs: Tarea Rapida";
                                        $arrayParametrosHist["strEstadoActual"]         = "Finalizada";
                                        $arrayParametrosHist["strAccion"]               = "Finalizada";

                                        $this->serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);

                                        $arrayParametrosHist["strOpcion"] = "Historial";

                                        $this->serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);
                                    }
                                }                     

                            }    
                                                        
                            if($strProducto =='PROMONETLIFECAM' && $arrayProdFacturar['valorFacturar']>0)
                            {
                                $objAdmiCaractDescuentoNetlifecam = $this->emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                                                        ->findOneBy(array("descripcionCaracteristica" => "DESCUENTO NETLIFECAM",
                                                                                          "estado"                    => "Activo"));

                                //Se inserta la característica por Descuento Netlifecam
                                $objSolCaractDescuentoNetlifecam = new InfoDetalleSolCaract();
                                $objSolCaractDescuentoNetlifecam->setCaracteristicaId($objAdmiCaractDescuentoNetlifecam);
                                $objSolCaractDescuentoNetlifecam->setDetalleSolicitudId($objDetalleSolCancelVoluntaria);
                                $objSolCaractDescuentoNetlifecam->setValor($arrayProdFacturar['valorFacturar']);
                                $objSolCaractDescuentoNetlifecam->setEstado("NoFacturable");
                                $objSolCaractDescuentoNetlifecam->setUsrCreacion($strUsrCreacion);
                                $objSolCaractDescuentoNetlifecam->setFeCreacion(new \DateTime('now'));
                                $this->emComercial->persist($objSolCaractDescuentoNetlifecam);
                                $this->emComercial->flush();                      

                            }

                       }                    
                        
                    }
                   

                    // Se agrega caracteristica FACTURACION DETALLADA
                    
                    $objAdmiCaractFactDet = $this->emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                                              ->findOneBy(array("descripcionCaracteristica" => "FACTURACION DETALLADA",
                                                                                "estado"                    => "Activo"));

                    $objSolCaractFactDet = new InfoDetalleSolCaract();
                    $objSolCaractFactDet->setCaracteristicaId($objAdmiCaractFactDet);
                    $objSolCaractFactDet->setDetalleSolicitudId($objDetalleSolCancelVoluntaria);
                    $objSolCaractFactDet->setValor('S');
                    $objSolCaractFactDet->setEstado("Activo");
                    $objSolCaractFactDet->setUsrCreacion($strUsrCreacion);
                    $objSolCaractFactDet->setFeCreacion(new \DateTime('now'));
                    $this->emComercial->persist($objSolCaractFactDet);
                    $this->emComercial->flush();                    
  
                }
                             
              
                
        
                $this->emComercial->getConnection()->commit();
            }
            
            $strRespuesta   = $this->emComercial->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                ->ejecutarFacturacionCancelacion($arrayParametrosFact);

            if ($floatSubtotalNDI > 0 )
            {
                $arrayParametrosNDI                   = array();
                $arrayParametrosNDI['strEmpresaCod']  = $strEmpresaCod;
                $arrayParametrosNDI['intIdServicio']  = $intIdServicio;
                $arrayParametrosNDI['strTipoProceso'] = "CancelacionVoluntaria";
                $arrayParametrosNDI['strMsnError']    = str_pad(' ', 30);
                
                $strRespuesta = $this->emComercial->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                  ->ejecutarNDICancelacion($arrayParametrosNDI);
            }
          
        }
        catch(\Exception $objEx)
        {
            $objServiceUtil->insertError('Telcos+',
                                      'InfoServicioController->ajaxEjecutarCancelacionVoluntariaAction',
                                      $objEx->getMessage(),
                                      $strUsrCreacion,
                                      $strIpCliente);
        }
        
        return $strRespuesta;
    }
    
    /**
     * Función que agrega el historial de facturación por cancelación de servicios.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 - 26-11-2018
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 15-08-2019 Se verifica el estado del servicio que debe ser Cancel
     * 
     */
    public function addHistorialFacturacion($arrayParametros)
    {            
        $intIdServicio   = $arrayParametros['intIdServicio'];
        $strFacturable   = $arrayParametros['strFacturable'];       
        $strUsrCreacion  = $arrayParametros['strUsrCreacion'];
        $objServiceUtil  = $arrayParametros['serviceUtil'];
        $strIpCliente    = $arrayParametros['strIpCliente'];
        $strObservacion  = $arrayParametros['strObservacion'];
        $intMotivoId     = $arrayParametros['intIdMotivo'];
        $strAccion       = 'Facturable';

        try
        {
            if($strFacturable === 'N')
            {
                $strAccion       = 'noFacturable';
                $strObservacion  = $arrayParametros['strObservacion'];                 
            }
            else
            {
                $strObservacion  = 'Se cancelo el Servicio';

                $objMotivoCancelacion   =   $this->emComercial->getRepository('schemaBundle:AdmiMotivo')
                                                              ->findOneBy(array("nombreMotivo" => "Cancelacion Voluntaria",
                                                                                "estado"       => "Activo")); 

                if(is_object($objMotivoCancelacion))
                {
                    $intMotivoId = $objMotivoCancelacion->getId();                    
                }                 
            }            
            
            $objInfoServicioInternet = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                         ->find($intIdServicio);
            
            
            if(is_object($objInfoServicioInternet))
            {            
                $intPuntoId = $objInfoServicioInternet->getPuntoId()->getId();
               
                $arrayServiciosPto = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                       ->findBy(array('puntoId' => $intPuntoId,
                                                                      'estado'  => 'Cancel'));
                
                foreach( $arrayServiciosPto as $objServicioPto )
                {
                    $objServicioHistorial = new InfoServicioHistorial();
                    $objServicioHistorial->setServicioId($objServicioPto);
                    $objServicioHistorial->setObservacion($strObservacion);
                    $objServicioHistorial->setMotivoId($intMotivoId);
                    $objServicioHistorial->setEstado("Cancel");
                    $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                    $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                    $objServicioHistorial->setIpCreacion($strIpCliente);
                    $objServicioHistorial->setAccion($strAccion);
                    $this->emComercial->persist($objServicioHistorial);
                    $this->emComercial->flush();
                }
                $this->emComercial->getConnection()->commit();
            }
        }
        catch(\Exception $objEx)
        {
            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }
            
            $objServiceUtil->insertError('Telcos+',
                                          'InfoServicioController->addHistorialFacturacion',
                                          $objEx->getMessage(),
                                          $strUsrCreacion,
                                          $strIpCliente);
        }
    } 
    
    /**
     * Función que permite obtener un arreglo con los servicios relacionados al servicio principal para instalación simultanea.
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.0 10-07-2020 - Version Inicial.
     *
     * @param $intIdServicio -> Contiene un int que representa el Id del servicio que tiene activación simultánea.
     * @return null|$intIdCouInstSim
     */

    public function getServiciosInstalacionSimultaneaRequiereFlujo($intIdServicio)
    {
        $arrayServiciosSimultaneos = array();

        /*Obtenemos el array del parámetro.*/
        $objParamsDet = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
            ->get('CARACTERISTICAS_SERVICIOS_SIMULTANEOS',
                'TECNICO',
                'INSTALACION_SIMULTANEA',
                '',
                '',
                '',
                '',
                '',
                '',
                10);

        /*Validamos que el arreglo no este vacío.*/
        if (is_array($objParamsDet) && !empty($objParamsDet))
        {
            $objCaracteristicasServiciosSimultaneos = json_decode($objParamsDet[0]['valor1'], true);
            $arrayProductosSimultaneos = $this->servicioGeneral->getArraybyKey('PRODUCTO_ID', $objCaracteristicasServiciosSimultaneos);
        }
        
        /* Obtengo un objeto con el servicio tradicional. */
        $objServicioTradicional = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                    ->find($intIdServicio);

        /* Se valida que no esten vacios. */
        if (is_object($objServicioTradicional) && isset($arrayProductosSimultaneos) && !is_null($arrayProductosSimultaneos))
        {
            /* Se trae un arreglo de servicios que cumplan con las condiciones de puntoId y productoId. */
            $arrayServiciosPunto = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                ->findBy(array(
                    'puntoId' => $objServicioTradicional->getPuntoId(),
                    'productoId' => $arrayProductosSimultaneos
                ));
            
            /* Se trae un objeto de la caracteristica. */
            $objAdmiCaract = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                ->findOneBy(array(
                    'descripcionCaracteristica' => 'INSTALACION_SIMULTANEA',
                    'estado' => 'Activo'
                ));
            
            /* Se valida que los servicios del punto sean 1 o mas. */
            if (count($arrayServiciosPunto)>=1)
            {
                /* Se define un arreglo para filtrar por estado los servicios simultaneos. */
                $arrayEstados = array('Rechazado', 'Rechazada', 'Anulado', 'Anulada');

                /* Se recorre el arreglo de servicios del punto */
                foreach ($arrayServiciosPunto as $key=>$objServicio)
                {
                    $objInfoServProdCaract = $this->emInfraestructura->getRepository('schemaBundle:InfoServicioProdCaract')
                        ->findOneBy(array(
                            'servicioId' => $objServicio->getId(),
                            'valor'      => $objServicioTradicional->getId()
                        ));

                    if(is_object($objInfoServProdCaract) && !empty($objInfoServProdCaract))
                    {
                        /* Se define variable para validar si el valor de la caracteristica es igual al del servicio tradicional. */
                        $boolIdInstSim = intval($objServicioTradicional->getId()) == intval($objInfoServProdCaract->getValor());
                        
                        /* Se valida el tema de la caracteristica, ademas de si el estado del servicio no esta incluido en el arreglo de estados. */
                        if ($boolIdInstSim && !in_array($objServicio->getEstado(), $arrayEstados))
                        {
                            /*Obtenemos el producto del servicio.*/
                            $objProducto = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                ->find($objServicio->getProductoId());
                                        
                            /*Validamos que el arreglo no este vacío.*/
                            if (is_object($objProducto))
                            {
                                $strDescripcionProducto = $objProducto->getDescripcionProducto();
                            }
                            
                            //Consultamos si el producto requiere flujo ya que antes no lo tenia
                            $arrayParametrosRequiereFlujo =   $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                              ->getOne("REQUIERE_FLUJO", 
                                                                                       "TECNICO", 
                                                                                       "", 
                                                                                       "", 
                                                                                       $strDescripcionProducto, 
                                                                                       "", 
                                                                                       "",
                                                                                       "",
                                                                                       "",
                                                                                        10
                                                                                     );
                            if(is_array($arrayParametrosRequiereFlujo) && !empty($arrayParametrosRequiereFlujo))
                            {
                                array_push($arrayServiciosSimultaneos, $objServicio->getId());
                            }
                        }
                    }
                }
            }
        }

        return $arrayServiciosSimultaneos;
    }

    
    /**
     * 
     * Función para cancelar servicio Wifi Dual Band por cancelación de último Extender conectado en el punto
     * 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec> 
     * @version 1.0 23-09-2020 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 25-05-2021 Se agregan parámetros strEliminaSolsGestionOnt y strEliminaSolsDualBand a la función 
     *                         cancelaOEliminaServiciosAdicionalesLogicamente para que se ejecute la eliminación de dichas solicitudes en esa función
     * 
     */
    public function cancelaServiciosWXCancelacionEdb($arrayParametros)
    {
        $intIdPunto             = $arrayParametros["intIdPunto"];
        $strCodEmpresa          = $arrayParametros["strCodEmpresa"];
        $strUsrCreacion         = $arrayParametros["strUsrCreacion"];
        $strIpCreacion          = $arrayParametros["strIpCreacion"];
        $intIdPersonaEmpresaRol = $arrayParametros["intIdPersonaEmpresaRol"];
        $strMensaje             = "";
        try
        {
            if(isset($intIdPunto) && !empty($intIdPunto))
            {
                $arrayRespuestaServInternetValido   = $this->servicioGeneral
                                                            ->obtieneServicioInternetValido(array(  "intIdPunto"    => $intIdPunto,
                                                                                                    "strCodEmpresa" => $strCodEmpresa));
                $strStatusServicioInternet  = $arrayRespuestaServInternetValido["status"];
                $objServicioInternet        = $arrayRespuestaServInternetValido["objServicioInternet"];
                if($strStatusServicioInternet === "OK" && is_object($objServicioInternet))
                {
                    $arrayRespuestaEdbEnlazado  = $this->servicioGeneral
                                                       ->verificaEquipoEnlazado(array(  "intIdServicioInternet" => $objServicioInternet->getId(),
                                                                                        "strTipoEquipoABuscar"  => "EXTENDER DUAL BAND"));
                    $strStatusEquipoEdbEnlazado = $arrayRespuestaEdbEnlazado["status"];
                    $strInfoEquipoEdbEnlazado   = $arrayRespuestaEdbEnlazado["infoEquipoEnlazado"];
                    if($strStatusEquipoEdbEnlazado === "OK" && (!isset($strInfoEquipoEdbEnlazado) || empty($strInfoEquipoEdbEnlazado))
                        && is_object($objServicioInternet->getPlanId()))
                    {
                        //El punto ya no tiene más extenders conectados
                        $objServicioTecnicoInternet     = $this->emComercial->getRepository("schemaBundle:InfoServicioTecnico")
                                                                            ->findOneByServicioId($objServicioInternet->getId());
                        $arrayRespuestaProdWdbEnPlan    = $this->servicioGeneral
                                                               ->obtieneProductoEnPlan(
                                                                                        array(  "intIdPlan"                 => 
                                                                                                $objServicioInternet->getPlanId()->getId(),
                                                                                                "strNombreTecnicoProducto"  => "WIFI_DUAL_BAND"));
                        $strProductoWdbEnPlan           = $arrayRespuestaProdWdbEnPlan["strProductoEnPlan"];
                        if($strProductoWdbEnPlan !== "SI")
                        {
                            /**
                             * El punto tampoco tiene asociado el producto Wifi dual band dentro del plan, por lo que se procederá a cancelar
                             * el servicio adicional Wifi Dual Band y se generará la solicitud de cambio de módem inmediato
                             */

                            $arrayRespuestaEliminaWdb   = $this->cancelaOEliminaServiciosAdicionalesLogicamente(
                                                                array(  "intIdPunto"                => $intIdPunto,
                                                                        "arrayNombresTecnicosProds" => array("WIFI_DUAL_BAND"),
                                                                        "strEliminaSolsGestionOnt"  => "SI",
                                                                        "strEliminaSolsDualBand"    => "SI",
                                                                        "strCodEmpresa"             => $strCodEmpresa,
                                                                        "strUsrCreacion"            => $strUsrCreacion,
                                                                        "strIpCreacion"             => $strIpCreacion));

                            if($arrayRespuestaEliminaWdb["status"] !== "OK")
                            {
                                throw new \Exception("No se ha podido cancelar lógicamente los servicios Wifi Dual Band");
                            }
                            if(is_object($objServicioTecnicoInternet))
                            {
                                $arrayRespuestaCreaSolCambioModemInmediato   = $this->servicioGeneral
                                    ->creaSolAutomaticaCambioModemInmediatoCpeOnt(
                                        array(  "objServicio"               => $objServicioInternet,
                                                "intIdPersonaEmpresaRol"    => $intIdPersonaEmpresaRol,
                                                "strIpCreacion"             => $strIpCreacion,
                                                "strUsrCreacion"            => $strUsrCreacion,
                                                "strObservacion"            => 
                                                "Solicitud creada automáticamente por cancelación de servicio Wifi Dual Band",
                                                "strNombreTecnicoProdMotivo"=> "WIFI_DUAL_BAND",
                                                "strProcesoEjecutante"      => "CANCELACION",
                                                "strCodEmpresa"             => $strCodEmpresa,
                                                "intPrecioDescuento"        => null,
                                                "intIdElementoCliente"      => $objServicioTecnicoInternet->getElementoClienteId(),
                                                "strTipoDocumento"          => "c"));
                                if($arrayRespuestaCreaSolCambioModemInmediato["status"] !== "OK")
                                {
                                    throw new \Exception("No se ha podido generar la solicitud de modem inmediato");
                                }
                                $strMensaje = "Se ha generado correctamente la solicitud de cambio de módem inmediato por cancelación automática "
                                              . "del servicio Wifi Dual band";
                            }
                        }
                    }
                }
            }
            $strStatus = "OK";
        }
        catch (\Exception $e) 
        {
            $strStatus = "ERROR";
            $strMensaje = $e->getMessage();
        }
        $arrayResultado = array("status"    => $strStatus,
                                "mensaje"   => $strMensaje);
        return $arrayResultado;
        
    }
    
    /**
     * 
     * Función para cancelar o eliminar servicios adicionales de manera lógica
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec> 
     * @version 1.0 23-09-2020
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 25-05-2021 Se agrega la eliminación de solicitudes que gestionan el ont o solicitudes dual band
     * 
     */
    public function cancelaOEliminaServiciosAdicionalesLogicamente($arrayParametros)
    {
        $intIdPunto                 = $arrayParametros["intIdPunto"];
        $strCodEmpresa              = $arrayParametros["strCodEmpresa"];
        $arrayNombresTecnicosProds  = $arrayParametros["arrayNombresTecnicosProds"];
        $strUsrCreacion             = $arrayParametros["strUsrCreacion"];
        $strIpCreacion              = $arrayParametros["strIpCreacion"];
        $strEliminaSolsGestionOnt   = $arrayParametros["strEliminaSolsGestionOnt"];
        $strEliminaSolsDualBand     = $arrayParametros["strEliminaSolsDualBand"];
        $strMensaje                 = "";
        try
        {
            $objAccion = $this->emSeguridad->getRepository('schemaBundle:SistAccion')->find(313);
            if (!is_object($objAccion))
            {
                throw new \Exception("No se encontró información de la acción para cancelar servicios");
            }
            $arrayRespuestaServiciosAdic    = $this->servicioGeneral
                                                   ->obtenerServiciosPorProducto(
                                                                                array("intIdPunto"                  => $intIdPunto,
                                                                                      "arrayNombresTecnicoProducto" => $arrayNombresTecnicosProds,
                                                                                      "strCodEmpresa"               => $strCodEmpresa));
            $arrayServiciosAdic             = $arrayRespuestaServiciosAdic["arrayServiciosPorProducto"];
            if(isset($arrayServiciosAdic) && !empty($arrayServiciosAdic))
            {
                foreach($arrayServiciosAdic as $objServicioAdic)
                {
                    if($objServicioAdic->getEstado() === 'Activo' || $objServicioAdic->getEstado() === 'In-Corte')
                    {
                        $strEstadoServicio      = "Cancel";
                        $strObservacionServicio = "Se cancelo el servicio";
                        $strNombreAccion        = $objAccion->getNombreAccion();
                    }
                    else
                    {
                        $strEstadoServicio      = "Eliminado";
                        $strObservacionServicio = "Se elimina el servicio";
                        $strNombreAccion        = "";
                    }
                                        
                    if($strEliminaSolsGestionOnt === "SI")
                    {
                        $this->servicioGeneral->eliminaSolicitudesGestionaOnt(array("intIdServicio" => $objServicioAdic->getId()));
                    }
                    
                    if($strEliminaSolsDualBand === "SI")
                    {
                        $this->servicioGeneral->eliminaSolicitudesDualBand(array("intIdServicio" => $objServicioAdic->getId()));
                    }
                              
                    $objServicioAdic->setEstado($strEstadoServicio);
                    $this->emComercial->persist($objServicioAdic);
                    $this->emComercial->flush();

                    //historial del servicio
                    $objServicioHistorial = new InfoServicioHistorial();
                    $objServicioHistorial->setServicioId($objServicioAdic);
                    $objServicioHistorial->setObservacion($objServicioAdic->getProductoId()->getDescripcionProducto()
                                                          .": ".$strObservacionServicio);
                    $objServicioHistorial->setEstado($strEstadoServicio);
                    $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                    $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                    $objServicioHistorial->setIpCreacion($strIpCreacion);
                    $objServicioHistorial->setAccion ($strNombreAccion);
                    $this->emComercial->persist($objServicioHistorial);
                    $this->emComercial->flush();
                }
            }
            $strStatus = "OK";
        }
        catch (\Exception $e)
        {
            $strStatus  = "ERROR";
            $strMensaje = $e->getMessage();
        }
        $arrayResultado = array("status"    => $strStatus,
                                "mensaje"   => $strMensaje);
        return $arrayResultado;
    }
    
    /**
     * Función que permite obtener un arreglo con los servicios relacionados al servicio principal como DIRECTLINK MPLS.
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.0 24-09-2020 - Version Inicial.
     *
     * @param $intIdServicio -> Contiene un int que representa el Id del servicio que tiene servicios relacionados.
     * @return null|$intIdCouInstSim
     */

    public function getServiciosRelacion($intIdServicio)
    {
        $arrayServiciosSimultaneos = array();

        /*Obtenemos el array del parámetro.*/
        $objParamsDet = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
            ->get('CARACTERISTICAS_SERVICIO_CONFIRMACION',
                'TECNICO',
                'SERVICIO_CONFIRMACION',
                '',
                '',
                '',
                '',
                '',
                '',
                10);

        /*Validamos que el arreglo no este vacío.*/
        if (is_array($objParamsDet) && !empty($objParamsDet))
        {
            $objCaracteristicasServiciosSimultaneos = json_decode($objParamsDet[0]['valor1'], true);
            $arrayProductosSimultaneos = $this->servicioGeneral->getArraybyKey('PRODUCTO_ID', $objCaracteristicasServiciosSimultaneos);
        }
        
        /* Obtengo un objeto con el servicio tradicional. */
        $objServicioTradicional = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                    ->find($intIdServicio);

        /* Se valida que no esten vacios. */
        if (is_object($objServicioTradicional) && isset($arrayProductosSimultaneos) && !is_null($arrayProductosSimultaneos))
        {
            /* Se trae un arreglo de servicios que cumplan con las condiciones de puntoId y productoId. */
            $arrayServiciosPunto = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                ->findBy(array(
                    'puntoId' => $objServicioTradicional->getPuntoId(),
                    'productoId' => $arrayProductosSimultaneos
                ));
            
            /* Se trae un objeto de la caracteristica. */
            $objAdmiCaract = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                ->findOneBy(array(
                    'descripcionCaracteristica' => 'RELACION_FAST_CLOUD',
                    'estado' => 'Activo'
                ));
            
            /* Se valida que los servicios del punto sean 1 o mas. */
            if (count($arrayServiciosPunto)>=1)
            {
                /* Se define un arreglo para filtrar por estado los servicios simultaneos. */
                $arrayEstados = array('Rechazado', 'Rechazada', 'Anulado', 'Anulada');

                /* Se recorre el arreglo de servicios del punto */
                foreach ($arrayServiciosPunto as $key=>$objServicio)
                {
                    $objProdCaract = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                          ->findOneBy(array(
                                                                            "productoId"       => $objServicioTradicional->getProductoId(),
                                                                            "caracteristicaId" => $objAdmiCaract->getId(),
                                                                            "estado"           => "Activo"
                                                                            ));
                    if(is_object($objProdCaract))
                    {
                        $objInfoServProdCaract = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                    ->findOneBy(array(
                                                                                    'productoCaracterisiticaId' => $objProdCaract->
                                                                                                                    getId(),
                                                                                    'servicioId' => $objServicioTradicional->getId(),
                                                                                    'valor'      => $objServicio->getId()
                                                                    ));
                    }
                    
                    if(is_object($objInfoServProdCaract) && !empty($objInfoServProdCaract))
                    {
                        /* Se define variable para validar si el valor de la caracteristica es igual al del servicio tradicional. */
                        $boolIdInstSim = intval($objServicio->getId()) == intval($objInfoServProdCaract->getValor());
                        
                        /* Se valida el tema de la caracteristica, ademas de si el estado del servicio no esta incluido en el arreglo de estados. */
                        if ($boolIdInstSim && !in_array($objServicio->getEstado(), $arrayEstados))
                        {
                            array_push($arrayServiciosSimultaneos, $objServicio->getId());
                        }
                    }
                }
            }
        }

        return $arrayServiciosSimultaneos;
    }
    
    /**
     * Funcion que sirve para validar si otro servicio usa el mismo puerto
     * true:  no existe otro puerto 
     * false: existe otro puerto
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.0 24-02-2022          
     * 
     * @param $arrayParametros [$arrayParametros]
     */
    public function validarPuertoPorServicio($arrayParametros)
    {
        $objServicioAct     = $arrayParametros['objServicio'];
        $objPuntoAct        = $objServicioAct->getPuntoId();
        $boolFlagCancel     = true;
                
        $objServicioTecnico = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                   ->findOneBy(array("servicioId" => $objServicioAct->getId()));

        $arrayServiciosActivos  = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                            ->findBy(array(  "puntoId"   => $objPuntoAct->getId(),
                                                             "estado"    => "Activo"));

        $arrayServiciosEnPruebas  = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                            ->findBy(array(  "puntoId"   => $objPuntoAct->getId(),
                                                             "estado"    => "EnPruebas"));

        $arrayServiciosInCorte  = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                            ->findBy(array(  "puntoId"   => $objPuntoAct->getId(),
                                                             "estado"    => "In-Corte"));

        $arrayServicios = array_merge($arrayServiciosActivos, $arrayServiciosInCorte, $arrayServiciosEnPruebas);

        foreach($arrayServicios as $objServicio)
        {
            $objServicioTecAct = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                    ->findOneBy(array("servicioId" => $objServicio->getId()));
            //si sale del mismo puerto del sw y usa el mismo puerto del cassette -> usa los mismos datos tecnicos
            if(is_object($objServicioTecAct) && ($objServicioAct->getId() != $objServicio->getId())
               && ($objServicioTecAct->getInterfaceElementoId() == $objServicioTecnico->getInterfaceElementoId() &&
                   $objServicioTecAct->getInterfaceElementoConectorId() == $objServicioTecnico->getInterfaceElementoConectorId()))
            {
                $boolFlagCancel = false;
                break;
            }
        }

        return $boolFlagCancel;
    }

    /**
     * Funcion que sirve para cancelar los servicios adicionales safecity del producto Internet VPNoGPON 
     * para la empresa TN bajo la red GPON
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 01-08-2022
     * 
     * @param Array $arrayParametros
     * 
     * @return Array $arrayRessultado [
     *                                  'status'    => estado de respuesta de la operación 'OK' o 'ERROR',
     *                                  'mensaje'   => mensaje de la operación o de error
     *                               ]
     */
    public function cancelarServiciosSafecity($arrayParametros)
    {
        $objServicio    = $arrayParametros['objServicio'];
        $strObservacion = $arrayParametros['strObservacion'];
        $objMotivo      = $arrayParametros['objMotivo'];
        $objAccion      = $arrayParametros['objAccion'];
        $intIdOficina   = $arrayParametros['intIdOficina'];
        $intIdPersonaEmpresaRol = $arrayParametros['intIdPersonaEmpresaRol'];
        $intIdEmpresa   = $arrayParametros['intIdEmpresa'];
        $strPrefijoEmpresa = $arrayParametros['strPrefijoEmpresa'];
        $strUsrCreacion = $arrayParametros['strUsrCreacion'];
        $strIpCreacion  = $arrayParametros['strIpCreacion'];
        try
        {
            $objServicioPrincipal = $objServicio;
            $arrayParServAdd = array(
                "intIdProducto"      => $objServicioPrincipal->getProductoId()->getId(),
                "intIdServicio"      => $objServicioPrincipal->getId(),
                "strNombreParametro" => 'CONFIG_PRODUCTO_DATOS_SAFE_CITY',
                "strUsoDetalles"     => 'AGREGAR_SERVICIO_ADICIONAL',
            );
            $arrayProdCaracConfProAdd  = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                    ->getServiciosPorProdAdicionalesSafeCity($arrayParServAdd);
            if($arrayProdCaracConfProAdd['status'] == 'OK' && count($arrayProdCaracConfProAdd['result']) > 0)
            {
                foreach($arrayProdCaracConfProAdd['result'] as $arrayServicioConfProAdd)
                {
                    $objServicioAdd = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                    ->find($arrayServicioConfProAdd['idServicio']);
                    if(is_object($objServicioAdd))
                    {
                        $strEstadoServAdd = $objServicioAdd->getEstado();
                        if($strEstadoServAdd == "Activo" || $strEstadoServAdd == "In-Corte")
                        {
                            $objServicioAdd->setEstado($objServicio->getEstado());
                            $this->emComercial->persist($objServicioAdd);
                            $this->emComercial->flush();
                            //historial del servicio
                            $objServicioPuntoHistorial = new InfoServicioHistorial();
                            $objServicioPuntoHistorial->setServicioId($objServicioAdd);
                            $objServicioPuntoHistorial->setObservacion($strObservacion);
                            $objServicioPuntoHistorial->setMotivoId($objMotivo->getId());
                            $objServicioPuntoHistorial->setEstado($objServicio->getEstado());
                            $objServicioPuntoHistorial->setUsrCreacion($strUsrCreacion);
                            $objServicioPuntoHistorial->setFeCreacion(new \DateTime('now'));
                            $objServicioPuntoHistorial->setIpCreacion($strIpCreacion);
                            $objServicioPuntoHistorial->setAccion($objAccion->getNombreAccion());
                            $this->emComercial->persist($objServicioPuntoHistorial);
                            $this->emComercial->flush();
                        }
                        else
                        {
                            $objServicioAdd->setEstado("Eliminado");
                            $this->emComercial->persist($objServicioAdd);
                            $this->emComercial->flush();
                            //historial del servicio
                            $objServicioPuntoHistorial = new InfoServicioHistorial();
                            $objServicioPuntoHistorial->setServicioId($objServicioAdd);
                            $objServicioPuntoHistorial->setObservacion("Se elimino el Servicio");
                            $objServicioPuntoHistorial->setMotivoId($objMotivo->getId());
                            $objServicioPuntoHistorial->setEstado("Eliminado");
                            $objServicioPuntoHistorial->setUsrCreacion($strUsrCreacion);
                            $objServicioPuntoHistorial->setFeCreacion(new \DateTime('now'));
                            $objServicioPuntoHistorial->setIpCreacion($strIpCreacion);
                            $objServicioPuntoHistorial->setAccion($objAccion->getNombreAccion());
                            $this->emComercial->persist($objServicioPuntoHistorial);
                            $this->emComercial->flush();
                        }
                        //eliminar ip del servicio
                        $objInfoIpServicio  = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                            ->findOneBy(array("servicioId" => $objServicioAdd->getId(), 
                                                                              "estado"     => array("Reservada","Activo")));
                        if(is_object($objInfoIpServicio))
                        {
                            $objInfoIpServicio->setEstado("Eliminado");
                            $this->emInfraestructura->persist($objInfoIpServicio);
                            $this->emInfraestructura->flush();
                        }
                        //eliminar las caracteristicas del servicio
                        $arrayServProdCaractServ = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                ->findBy(array("servicioId" => $objServicioAdd->getId(), 
                                                                               "estado"     => "Activo"));
                        foreach($arrayServProdCaractServ as $objServProdCaractServ)
                        {
                            $objServProdCaractServ->setEstado("Eliminado");
                            $this->emComercial->persist($objServProdCaractServ);
                            $this->emComercial->flush();
                        }
                        //seteo el flag del cpe si pertenece a un solo servicio
                        $booleanFlagCpe       = false;
                        //seteo el flag de la propiedad del equipo
                        $booleanFlagPropiedad = false;
                        //seteo el objeto del cliente
                        $objElementoCliente   = null;
                        //obtengo el login auxiliar del servicio
                        $strLoginAux          = $objServicioAdd->getLoginAux();
                        if(!empty($strLoginAux))
                        {
                            //obtengo el elemento del cliente
                            $objElementoCliente = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                        ->findOneBy(array("nombreElemento" => $strLoginAux,
                                                                                          "estado"         => "Activo"));
                            //validar si otro servicio usa el mismo cpe
                            if(is_object($objElementoCliente))
                            {
                                //verifico si el equipo pertenece a un solo servicio
                                $arrayParametrosCpe = array('objServicio'    => $objServicioAdd,
                                                            'objElementoCpe' => $objElementoCliente);
                                $booleanFlagCpe     = $this->validarCpePorServicio($arrayParametrosCpe);
                                //verifico a que pertenece el equipo
                                $objPropiedadElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                            ->findOneBy(array("elementoId"          => $objElementoCliente->getId(),
                                                                              "detalleNombre"       => "PROPIEDAD",
                                                                              "detalleDescripcion"  => "ELEMENTO PROPIEDAD DE",
                                                                              "detalleValor"        => "TELCONET",
                                                                              "estado"              => "Activo"));
                                if(is_object($objPropiedadElemento))
                                {
                                    $booleanFlagPropiedad = true;
                                }
                            }
                        }
                        //crear solicitud de retiro de equipo
                        if(is_object($objElementoCliente))
                        {
                            //obtener el servicio tecnico
                            $objServicioTecnicoAdd = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                            ->findOneBy(array('servicioId' => $objServicioAdd->getId()));
                            if(is_object($objServicioTecnicoAdd) && $booleanFlagCpe && $booleanFlagPropiedad)
                            {
                                //seteo parametros para la solicitud de retiro de equipo
                                $arrayParametrosSolRet = array();
                                $arrayParametrosSolRet['objServicioTecnico'] = $objServicioTecnicoAdd;
                                $arrayParametrosSolRet['intIdElemento']      = $objElementoCliente->getId();
                                $arrayParametrosSolRet['objServicio']        = $objServicioAdd;
                                $arrayParametrosSolRet['ipCreacion']         = $strIpCreacion;
                                $arrayParametrosSolRet['usrCreacion']        = $strUsrCreacion;
                                $arrayParametrosSolRet['booleanEliminarEnlaces'] = false;
                                $this->generarSolicitudRetiroEquipo($arrayParametrosSolRet);
                                $this->eliminarElementoCliente($arrayParametrosSolRet);
                            }
                            //eliminar enlaces
                            $arrayInterfaceEleCliente = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                            ->findBy(array("elementoId" => $objElementoCliente->getId()));

                            foreach($arrayInterfaceEleCliente as $objInterfaceElemento)
                            {
                                $arrayEnlaces = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                            ->findBy(array("interfaceElementoFinId"=>$objInterfaceElemento->getId()));
                                foreach($arrayEnlaces as $objEnlace)
                                {
                                    $objEnlace->setEstado("Eliminado");
                                    $this->emInfraestructura->persist($objEnlace);
                                    $this->emInfraestructura->flush();
                                }
                            }
                        }
                    }
                }
                //cancelar servicio security ng firewall
                $strParametroProdAdd = "PRODUCTO_SECURITY_NG_FIREWALL";
                if(is_object($objServicioAdd))
                {
                    $arrayParametrosDet  = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getOne('PARAMETROS PROYECTO GPON SAFECITY',
                                                        'INFRAESTRUCTURA',
                                                        'PARAMETROS',
                                                        'VALIDAR RELACION SERVICIO ADICIONAL CON DATOS SAFECITY',
                                                        $objServicioAdd->getProductoId()->getId(),
                                                        '',
                                                        '',
                                                        '',
                                                        '',
                                                        $intIdEmpresa);
                    if(!empty($arrayParametrosDet["valor7"]) && isset($arrayParametrosDet["valor7"]))
                    {
                        $strParametroProdAdd = $arrayParametrosDet["valor7"];
                    }
                }
                $arrayParObtenerServ = array(
                    "objPunto"       => $objServicio->getPuntoId(),
                    "strParametro"   => $strParametroProdAdd,
                    "strCodEmpresa"  => $intIdEmpresa,
                    "strUsrCreacion" => $strUsrCreacion,
                    "strIpCreacion"  => $strIpCreacion
                );
                $arrayResultSerNgFirewall = $this->servicioGeneral->getServicioGponPorProducto($arrayParObtenerServ);
                if($arrayResultSerNgFirewall["status"] == "OK" && is_object($arrayResultSerNgFirewall["objServicio"]))
                {
                    $objServicioAdd = $arrayResultSerNgFirewall["objServicio"];
                    if($objServicioAdd->getEstado() == "Activo" || $objServicioAdd->getEstado() == "In-Corte")
                    {
                        $arrayParCancel['idServicio']  = $objServicioAdd->getId();
                        $arrayParCancel['idEmpresa']   = $intIdEmpresa;
                        $arrayParCancel['idOficina']   = $intIdOficina;
                        $arrayParCancel['idAccion']    = $objAccion->getId();
                        $arrayParCancel['idMotivo']    = $objMotivo->getId();
                        $arrayParCancel['usrCreacion'] = $strUsrCreacion;
                        $arrayParCancel['clientIp']    = $strIpCreacion;
                        $arrayParCancel['strPrefijoEmpresa'] = $strPrefijoEmpresa;
                        $arrayParCancel['intIdPersonaEmpresaRol']  = $intIdPersonaEmpresaRol;
                        $arrayParCancel['booleanEliminarEnlaces']  = true;
                        $arrayParCancel['booleanBeginTransaction'] = false;
                        $this->cancelarServiciosOtros($arrayParCancel);
                    }
                    else
                    {
                        $objServicioAdd->setEstado("Eliminado");
                        $this->emComercial->persist($objServicioAdd);
                        $this->emComercial->flush();
                        //historial del servicio
                        $objServicioPuntoHistorial = new InfoServicioHistorial();
                        $objServicioPuntoHistorial->setServicioId($objServicioAdd);
                        $objServicioPuntoHistorial->setObservacion("Se elimino el Servicio");
                        $objServicioPuntoHistorial->setMotivoId($objMotivo->getId());
                        $objServicioPuntoHistorial->setEstado("Eliminado");
                        $objServicioPuntoHistorial->setUsrCreacion($strUsrCreacion);
                        $objServicioPuntoHistorial->setFeCreacion(new \DateTime('now'));
                        $objServicioPuntoHistorial->setIpCreacion($strIpCreacion);
                        $objServicioPuntoHistorial->setAccion($objAccion->getNombreAccion());
                        $this->emComercial->persist($objServicioPuntoHistorial);
                        $this->emComercial->flush();
                    }
                    //eliminar las caracteristicas del servicio
                    $arrayServProdCaractServ = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                            ->findBy(array("servicioId" => $objServicioAdd->getId(), 
                                                                           "estado"     => "Activo"));
                    foreach($arrayServProdCaractServ as $objServProdCaractServ)
                    {
                        $objServProdCaractServ->setEstado("Eliminado");
                        $this->emComercial->persist($objServProdCaractServ);
                        $this->emComercial->flush();
                    }
                }
            }
            //setear respuesta
            $arrayRespuesta = array(
                'strStatus'  => "OK",
                'strMensaje' => "OK"
            );
        }
        catch(\Exception $ex)
        {
            $arrayRespuesta = array(
                'strStatus'  => "ERROR",
                'strMensaje' => $ex->getMessage()
            );
            $this->serviceUtil->insertError("Telcos+",
                                            "InfoCancelarServicioService->cancelarServiciosSafecity",
                                            $ex->getMessage(),
                                            $strUsrCreacion,
                                            $strIpCreacion
                                           );
        }
        return $arrayRespuesta;
    }
    /*
     * Función que sirve para consultar estados que condicionan
     * la funciòn de inactivar UM de ARCGIS por cancelación de servicio
     * 
     * @author Liseth Candelario <lcandelario@telconet.ec>
     * @version 1.0 10-08-2022
     * 
     */
    public function validarCondicionesEstados($arrayParametros)
    {
        $strUsrCreacion             = $arrayParametros["strUsrCreacion"];
        $strIpCreacion              = $arrayParametros["strIpCreacion"];
        $objServicioTecnico         = $arrayParametros["objServicioTecnico"];
        $strStatus                  = 'ERROR';
        $strMensaje                 = '';
        $strEncontroServicio        = 'N';
        $intContadorResultados      = 0;
        $boolMasDeUnEstado          = false;

        try
        {
            if(is_array($arrayParametros) && is_object($objServicioTecnico))
            {
                // Servicio_id que se seleccionó
                $intServicioIdSeleccionado     = $objServicioTecnico->getServicioId()->getId();
                $strPuntoEstadoServicioTecnico = $objServicioTecnico->getServicioId()->getEstado();
                $intPuntoIdServicioTecnico     = $objServicioTecnico->getServicioId()->getPuntoId()->getId();
                // Consulta los valores de la UM en la InfoServicioTecnico
                $objInfoUmServicioTecnico      = $this->emInfraestructura->getRepository('schemaBundle:InfoServicioTecnico')
                                                ->findBy(array("interfaceElementoId" => $objServicioTecnico->getInterfaceElementoId(), 
                                                                "elementoId"         => $objServicioTecnico->getElementoId()) );

                //Consulta en la cabecera de paràmetros 
                $objParametroCabPermiteInactivar = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                    ->findOneBy(array("descripcion"=>'PARAMETROS QUE PERMITEN INACTIVAR LA UM EN ARCGIS', 
                                                                        "modulo"=>'TECNICO', "estado"=>'Activo'));

                if(($objInfoUmServicioTecnico))
                {
                    foreach($objInfoUmServicioTecnico as $arrayServios)
                    {
                        $intIdServicioTecnico      = $arrayServios->getServicioId()->getId();
                        $objInfoServCoincideUm     = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                        ->find($intIdServicioTecnico);
                        // Consulta los estados de los servicios que coinciden con la misma UM.
                        if(($objInfoServCoincideUm))
                        {
                            $intIdServicioCoincide     = $objInfoServCoincideUm->getId();
                            $intPuntoIdServicio        = $objInfoServCoincideUm->getPuntoId()->getId();

                            //INICIO - Para no considerar el servicio que estoy cancelando
                            if(($intServicioIdSeleccionado != $intIdServicioCoincide) && ($intPuntoIdServicio==$intPuntoIdServicioTecnico))
                            {
                                if(is_object($objParametroCabPermiteInactivar))
                                {
                                    //Detalle de parámetros -estados- para condicionar
                                    $arrayEstadosParaCondicionar = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->findBy(array("descripcion" => 'ESTADOS A CONSIDERAR PARA LA INACTIVACION',
                                                                    "parametroId" => $objParametroCabPermiteInactivar->getId(),
                                                                    "estado"      => 'Activo'));

                                    //Estados de cada servicio
                                    $strEstadoServicio      = $objInfoServCoincideUm->getEstado();

                                    if(isset($arrayEstadosParaCondicionar) && !empty($arrayEstadosParaCondicionar))
                                    {
                                        foreach($arrayEstadosParaCondicionar as $arrayValoresEstados)
                                        {
                                            $objValoresEstados[] = $arrayValoresEstados->getValor1();
                                        }

                                        if(!in_array($strEstadoServicio, $objValoresEstados))
                                        {
                                            //Cuando entre aquí significa que hay más servicios en la UM en estados no permitidos y por
                                            //ende no se debe de enviar actualizar a ARCGIS, es decir strStatus queda por default
                                            $intContadorResultados++;
                                            $strEncontroServicio = 'S';
                                            break;
                                        }
                                        unset($arrayEstadosParaCondicionar);
                                    }
                                }
                                else
                                {
                                    $strStatus  = 'ERROR';
                                    $strMensaje = 'No existen valores en los parámetros de cabecera';
                                }
                            }
                            //FIN - Para no considerar el servicio que estoy cancelando
                        }
                    }

                    if($intContadorResultados==0)
                    {
                        $boolMasDeUnEstado = true;
                        $strStatus  = 'OK';
                        $strMensaje = 'Los demás servicios del punto tienen estados permitidos';
                    }

                    //Cuando bandera $strEncontroServicio esté en S ingresa info error
                    if($strEncontroServicio == 'S')
                    {
                        $strStatus  = 'ERROR';
                        $strMensaje = ', no todos los servicios del punto están cancelados';
                        //historial del servicio
                        $objServicioHistorial = new InfoServicioHistorial();
                        $objServicioHistorial->setServicioId($objServicioTecnico->getServicioId());
                        $objServicioHistorial->setEstado($strPuntoEstadoServicioTecnico);
                        $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                        $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                        $objServicioHistorial->setIpCreacion($strIpCreacion);
                        $objServicioHistorial->setObservacion('No se inactiva la UM'.$strMensaje);
                        $this->emComercial->persist($objServicioHistorial);
                        $this->emComercial->flush();
                    }
                }
                else
                {
                    $strStatus = 'ERROR';
                    $strMensaje = 'No exisisten valores de la UM en la InfoServicioTecnico';
                }
            }
            if($strStatus=='ERROR')
            {
                $this->serviceUtil->insertError('Telcos+',
                                        'InfoCancelarServicioService->validarCondicionesEstados',
                                        $strStatus.', '.$strMensaje,
                                        $strUsrCreacion,
                                        $strIpCreacion);
            }
        }
        catch (\Exception $e)
        {
            $this->serviceUtil->insertError('Telcos+',
                                        'InfoCancelarServicioService->validarCondicionesEstados',
                                        $strStatus.' '.$e->getMessage(),
                                        $strUsrCreacion, $strIpCreacion);
        }

        $arrayRespuesta = array("status"    => $strStatus,
                    "boolCoincideProducto"  => $boolMasDeUnEstado,
                              "strMensaje"  => $strMensaje);
        return $arrayRespuesta;
    }

    /**
     * Función que sirve para consultar productos que condicionan la función
     * inactivar UM de ARCGIS por cancelación de servicio
     * 
     * @author Liseth Candelario <lcandelario@telconet.ec>
     * @version 1.0 10-08-2022
     * 
     */
    public function validarCondicionesProductos($arrayParametros)
    {
        $strUsrCreacion             = $arrayParametros["strUsrCreacion"];
        $strIpCreacion              = $arrayParametros["strIpCreacion"];
        $objProducto                = $arrayParametros["objProducto"];
        $strMensaje                 = '';
        $strStatus                  = 'ERROR';
        $boolCoincideProducto       = false;
        $intProducto                = $objProducto->getId();
        $intContadorResultados      = 0;

        try
        {
            if(is_array($arrayParametros))
            {
                /* Consulta en la cabecera de paràmetros*/
                $objParametroCabPermiteInactivar = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                    ->findOneBy(array("descripcion"=>'PARAMETROS QUE PERMITEN INACTIVAR LA UM EN ARCGIS', 
                                                                        "modulo"=>'TECNICO', "estado"=>'Activo'));

                if(is_object($objParametroCabPermiteInactivar))
                {
                    //Detalle de parámetros -productos- para condicionar
                    $objProductosParaCondicionar = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->findBy(array("descripcion" => 'PRODUCTOS A CONSIDERAR PARA LA INACTIVACION',
                                                                            "parametroId" => $objParametroCabPermiteInactivar->getId(),
                                                                            "estado"      => 'Activo'));

                    if(isset($objProductosParaCondicionar) && !empty($objProductosParaCondicionar))
                    {
                        foreach($objProductosParaCondicionar as $arrayValores)
                        {
                            $objValoresProductos = $arrayValores->getValor1();
                            if($objValoresProductos == $intProducto)
                            {
                                $intContadorResultados++;
                                break;
                            }
                        }
                        if($intContadorResultados>0)
                        {
                            $boolCoincideProducto = true;
                            $strStatus = 'OK';
                            $strMensaje = 'Si es un producto permitido';

                            $arrayRespuesta = array("status"    => $strStatus,
                                        "boolCoincideProducto"  => $boolCoincideProducto);
                        }
                    }        
                }
                else
                {
                    $strStatus = 'ERROR';
                    $strMensaje = 'No exisisten paràmetros de cabecera';
                }
            }

            if($strStatus=='ERROR')
            {
                $this->serviceUtil->insertError('Telcos+',
                                        'InfoCancelarServicioService->validarCondicionesEstados',
                                        $strStatus.', '.$strMensaje,
                                        $strUsrCreacion,
                                        $strIpCreacion);
            }
        }
        catch (\Exception $e)
        {
            $this->serviceUtil->insertError('Telcos+',
                                      'InfoCancelarServicioService->validarCondicionesProductos',
                                      $strStatus.' '.$e->getMessage(),
                                      $strUsrCreacion,
                                      $strIpCreacion);
        }

        $arrayRespuesta = array("status"    => $strStatus,
                    "boolCoincideProducto"  => $boolCoincideProducto);
        return $arrayRespuesta;
    }



    /**
     * Función que sirve para la inactivación en ARCGIS por cancelación de servicio
     * 
     * @author Liseth Candelario <lcandelario@telconet.ec>
     * @version 1.0 21-06-2022
     * 
     */
    public function inactivarUmARCGIS($arrayParametros)
    {
        $strDatabaseDsn             = $this->container->getParameter('database_dsn');
        $strUserInfraestructura     = $this->container->getParameter('user_infraestructura');
        $strPasswordInfraestructura = $this->container->getParameter('passwd_infraestructura');
        $strUsrCancelacion          = $arrayParametros["strUsrCancelacion"];
        $strNombreSwitch            = $arrayParametros["strNombreSwitch"];
        $strPuertoSwitch            = $arrayParametros["strPuertoSwitch"];
        $strLoginPunto              = $arrayParametros["strLoginPunto"];
        $strPrefijoEmpresa          = $arrayParametros["strPrefijo"];
        $strIpCreacion              = $arrayParametros["strIpCreacion"];
        $objServicioPunto           = $arrayParametros["objServicioPunto"];
        $strEstadoServicio          = $objServicioPunto->getEstado();
        $strMensaje                 = '';
        $strStatus                  = '';

        try
        {
            if(empty($strLoginPunto))
            {
                $strMensaje = 'No se ha podido obtener login';
            }
            
            if(!isset($strNombreSwitch)|| empty($strPuertoSwitch))
            {
                $strMensaje = 'No se ha podido obtener el Puerto o el Switch';
            }
                        
            $strSql         = " BEGIN DB_INFRAESTRUCTURA.INKG_SINC_ARCGIS.P_INACTIVAR_UM(
                                    :Pv_UsrCancelacion,
                                    :Pv_NombreSwitch,
                                    :Pv_PuertoSwitch,
                                    :Pv_LoginPunto,
                                    :Pv_Prefijo,
                                    :Pv_Status,
                                    :Pv_Mensaje); 
                                END;";
            $objConn = oci_connect($strUserInfraestructura, $strPasswordInfraestructura, $strDatabaseDsn);
            $objStmt = oci_parse($objConn, $strSql);
            
            oci_bind_by_name($objStmt, ':Pv_UsrCancelacion', $strUsrCancelacion);
            oci_bind_by_name($objStmt, ':Pv_NombreSwitch', $strNombreSwitch);
            oci_bind_by_name($objStmt, ':Pv_PuertoSwitch', $strPuertoSwitch);
            oci_bind_by_name($objStmt, ':Pv_LoginPunto', $strLoginPunto);
            oci_bind_by_name($objStmt, ':Pv_Prefijo', $strPrefijoEmpresa);
            oci_bind_by_name($objStmt, ':Pv_Status', $strStatus, 5);
            oci_bind_by_name($objStmt, ':Pv_Mensaje', $strMensaje, 2000);
            oci_execute($objStmt);
            if($strStatus === "OK")
            {
                //historial del servicio
                $objServicioHistorial = new InfoServicioHistorial();
                $objServicioHistorial->setServicioId($objServicioPunto);
                $objServicioHistorial->setEstado($strEstadoServicio);
                $objServicioHistorial->setUsrCreacion($strUsrCancelacion);
                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                $objServicioHistorial->setIpCreacion($strIpCreacion);
                $objServicioHistorial->setObservacion($strMensaje);
                $this->emComercial->persist($objServicioHistorial);
                $this->emComercial->flush();
            }
            else
            {
                //historial del servicio
                $objServicioHistorial = new InfoServicioHistorial();
                $objServicioHistorial->setServicioId($objServicioPunto);
                $objServicioHistorial->setEstado($strEstadoServicio);
                $objServicioHistorial->setUsrCreacion($strUsrCancelacion);
                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                $objServicioHistorial->setIpCreacion($strIpCreacion);
                $objServicioHistorial->setObservacion('No se inactiva UM'.$strMensaje);
                $this->emComercial->persist($objServicioHistorial);
                $this->emComercial->flush();

                $this->serviceUtil->insertError('Telcos+',
                                        'InfoCancelarServicioService->inactivarUmARCGIS',
                                        $strStatus.' '.$strMensaje.',
                                        Busqueda realizada: '.'NombreSwitch: '.$strNombreSwitch.'    
                                                                PuertoSwitch: '.$strPuertoSwitch.'
                                                                Login_aux:    '.$objServicioPunto->getLoginAux().'
                                                                Empresa:     '.$strPrefijoEmpresa,
                                        $strUsrCancelacion);
            }
        }
        catch (\Exception $e)
        {
            $this->serviceUtil->insertError('Telcos+',
                                      'InfoCancelarServicioService->inactivarUmARCGIS',
                                      $strStatus.' '.$e->getMessage(),
                                      $strUsrCancelacion);
        }

        $arrayRespuestaInactivarUm = array("status"    => $strStatus,
                                           "mensaje"   => $strMensaje);
        return $arrayRespuestaInactivarUm;
    }
    
    /**
     * Funcion  que permite finalizar la solicitud de cancelacion del servicio Safe Entry
     * 
     * @param array [objServicio
     *               ipCreacion 
     *               usrCreacion]
     * @return array [status => 'OK | ERROR'
     *                mensaje]
     * 
     * @author Leonardo Mero  <lemero@telconet.ec>
     * @version 1.0 09-12-2022 - Version inicial    
     */
    public function finalizarSolicitudCancelacion ($arrayParametros)
    {
        $objServicio    = $arrayParametros['objServicio'];
        $strIpCreacion  = $arrayParametros['ipCreacion'];
        $strUsrCreacion = $arrayParametros['usrCreacion'];

        try 
        {
            $objTipoSolicitud = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                  ->findOneBy(array('descripcionSolicitud' => 'CANCELACION',
                                                                    'estado'               => 'Activo'));
            
            $objDetalleSolicitud = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                   ->findOneBy(array('servicioId'     => $objServicio->getId(),
                                                     'tipoSolicitudId'=> $objTipoSolicitud->getId(),
                                                     'estado'         => 'Pendiente'));
            if(!isset($objDetalleSolicitud))
            {
                throw new \Exception('El servico no posee una solicitud de cancelacion en estado: Pendiente');
            }

            $objDetalleSolicitud->setEstado('Finalizado');
            $this->emComercial->persist($objDetalleSolicitud);
            
            $objDetalleSolHis = new InfoDetalleSolHist();
            $objDetalleSolHis->setDetalleSolicitudId($objDetalleSolicitud);
            $objDetalleSolHis->setObservacion('Se finaliza la tarea de cancelacion automaticamente');
            $objDetalleSolHis->setEstado("Finalizado");
            $objDetalleSolHis->setUsrCreacion($strUsrCreacion);
            $objDetalleSolHis->setFeCreacion(new \DateTime('now'));
            $this->emComercial->persist($objDetalleSolHis);
            $this->emComercial->flush();     

            $strStatus = 'OK';
            $strMensaje = 'Se finaliza la solicitud de cancelacion para el servicio ';
        } 
        catch (\Exception $e)
        {
            $strStatus  = "ERROR";
            $strMensaje = $e->getMessage();
            $this->serviceUtil->insertError("Telcos+",
                                            "InfoActivarPuertoService->finalizarSolicitudCancelacion",
                                            $strMensaje,
                                            $strUsrCreacion,
                                            $strIpCreacion);
        }
        return (array('status' => $strStatus ,'mensaje '=> $strMensaje));
    }

    /**
     * Funcion que permite verificar si se debe realizar las cancelaciones de los servicios 
     * relacionados al producto SAFE ENTRY
     * 
     * @param array [objServicio
     *               objProducto
     *               objMotivo
     *               strEmpresaCod
     *               intIdAccion
     *               intIdMotivo
     *               strOrigen
     *               strPrefijoEmpresa
     *               idPersonaEmpresRol
     *               departamentoId
     *               ipCreacion
     *               usrCreacion]
     * 
     * @return array [status => 'OK | ERROR'
     *                mensaje]
     * 
     * @author  Leonardo Mero <lemero.telconet.ec>
     * @version 1.0 09-12-2022 - Version inicial
     */
    public function verificarServicioSafeEntryCancelar($arrayParametros)
    {
        $objServicio             = $arrayParametros['objServicio'];
        $objPunto                = $objServicio->getPuntoId();
        $objCliente              = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                                        ->findOneBy(array('id'  => $objPunto->getPersonaEmpresaRolId()->getPersonaId()->getId(),
                                                          'estado' => 'Activo'));
        $objProducto             = $arrayParametros['objProducto'];
        $objMotivo               = $arrayParametros['objMotivo'];
        $strEmpresaCod           = $arrayParametros['strEmpresaCod'];
        $intIdAccion             = $arrayParametros['intIdAccion'];
        $intIdMotivo             = $arrayParametros['intIdMotivo'];
        $strOrigen               = $arrayParametros['strOrigen'];
        $strPrefijoEmpresa       = $arrayParametros['strPrefijoEmpresa'];
        $intIdPersonaEmpresaRol  = $arrayParametros['idPersonaEmpresaRol'];
        $intIdDepartamento       = $arrayParametros['departamentoId'];
        $strIpCreacion           = $arrayParametros['ipCreacion'];
        $strUsrCreacion          = $arrayParametros['usrCreacion'];


        try 
        {
            $objEmpresa = $this->emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneBy(array('prefijo'=>$strPrefijoEmpresa));

            $objPunto = $this->emComercial->getRepository('schemaBundle:InfoPunto')
                                          ->find($objServicio->getPuntoId());
            if(!isset($objPunto))
            {
                throw new \Exception("No se ha podido obtener el punto");      
            }

            //Se obtienen los productos requeridos para la cancelacion
            $arrayParametrosSafe = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getOne('CONFIG SAFE ENTRY',
                                                            'COMERCIAL',
                                                            '',
                                                            'SERVICIOS_REQUERIDOS',
                                                            '',
                                                            '',
                                                            '',
                                                            '',
                                                            '',
                                                            $strEmpresaCod);

            if(!is_array($arrayParametrosSafe))
            {
                throw new \Exception('No se ha podido obtener el parametro para realizar la validacion del servicio.');
            }

            //Servicios a cancelar
            $arrayServiciosCancelar = array_diff(json_decode($arrayParametrosSafe['valor1']), array($objProducto->getDescripcionProducto()));
            //Estados validos
            $arrayEstadosValidos = json_decode($arrayParametrosSafe['valor2']);

            //Verificamos si el punto posee el servicio SAFE ENTRY
            if($objProducto->getNombreTecnico() == 'INTERNET SMALL BUSINESS')
            {
                $objProSafeEntry = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                ->findOneBy(array('nombreTecnico' => 'SAFE ENTRY',
                                                                  'estado'        => 'Activo'));
                if(!is_object($objProSafeEntry))
                {
                    return (array('status' => 'OK', 'mensaje' => 'Se continua el flujo normal'));
                }
                $objSerSafeEntry = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                        ->findOneBy(array('productoId' => $objProSafeEntry->getId(),
                                                          'puntoId'    => $objServicio->getPuntoId(),
                                                          'estado'     => $arrayEstadosValidos));
                if(!is_object($objSerSafeEntry))
                {
                    return (array('status' => 'OK', 'mensaje' => 'El punto no posee el servicio SAFE ENTRY, se continua el flujo normal'));
                }
            }
            //
            foreach ($arrayServiciosCancelar as $strProducto)
            {
                $objProductoCancelar = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                       ->findOneBy(array('descripcionProducto' => $strProducto,
                                                         'estado'        => 'Activo'),
                                                         array('id'=> 'ASC' ));
                if(!isset($objProductoCancelar))
                {
                    throw new \Exception('No se ha podido obtener el producto');
                }

                $objServicioCancelar = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                       ->findOneBy(array('productoId' => $objProductoCancelar->getId(),
                                                         'puntoId'    => $objServicio->getPuntoId(),
                                                         'estado'     => $arrayEstadosValidos));
                
                if(!is_object($objServicioCancelar))
                {
                    continue;
                }
                
                $objAccion = $this->emSeguridad->getRepository('schemaBundle:SistAccion')->find($intIdAccion);
                                

                if($strProducto == 'Internet Small Business')
                {
                    //Cancelacion del servicio ISB
                    $objMotivo = $this->emGeneral->getRepository('schemaBundle:AdmiMotivo')->find($intIdMotivo); 

                    $objServicioTecnico = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                            ->findOneBy(array('servicioId' => $objServicioCancelar->getId()));
                        
                    if(!isset($objServicioTecnico))
                    {
                        throw new \Exception('No se ha podido encontrar el servicio tecnico.'.
                        ' No se puede generar la solicitud de cancelacion automatica');
                    }
                    $objElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($objServicioTecnico->getElementoId());

                    $objInterfaceElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                            ->findOneBy(array('elementoId' => $objServicioTecnico->getElementoId()));

                    $objModeloElemento = $this->emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                         ->find($objElemento->getModeloElementoId());
                    
                    $arrayParametrosCancelarServicio = array(
                        'intIdDepartamento'   => $intIdDepartamento,
                        'servicio'            => $objServicioCancelar,
                        'login'               => $objPunto->getLogin(),
                        'idEmpresa'           => $strEmpresaCod ? $strEmpresaCod : $objEmpresa->getId(),
                        'usrCreacion'         => $strUsrCreacion,
                        'ipCreacion'          => $strIpCreacion,
                        'motivo'              => $objMotivo,
                        'idPersonaEmpresaRol' => $intIdPersonaEmpresaRol,
                        'idAccion'            => $intIdAccion,
                        'producto'            => $objProductoCancelar,
                        'servicioTecnico'     => $objServicioTecnico,
                        'elemento'            => $objElemento,
                        'interfaceElemento'   => $objInterfaceElemento,
                        'modeloElemento'      => $objModeloElemento,
                        'accion'              => $objAccion,
                        'boolCancelacionSafeEntry' => true);

                    $arrayRespuesta = $this->cancelarServicioIsb($arrayParametrosCancelarServicio);      
                    if($arrayRespuesta['status'] != 'OK')
                    {
                        throw new \Exception($arrayRespuesta['mensaje']);              
                    }
                }

                if($strProducto == 'Cableado Estructurado' || $strProducto == 'SAFE ENTRY')
                {
                    //Cancelacion de los servicios

                    $arrayParametrosCancelarServicio = array(
                        'idServicio'            => $objServicioCancelar->getId(),
                        'idAccion'              => $intIdAccion,
                        'idMotivo'              => $objMotivo->getId() ,
                        'usrCreacion'           => $strUsrCreacion,
                        'clientIp'              => $strIpCreacion,
                        'strOrigen'             => $strOrigen,
                        'idEmpresa'             => $strEmpresaCod,
                        'intIdPersonaEmpresaRol'=> $intIdPersonaEmpresaRol,
                        'strPrefijoEmpresa'     => $strPrefijoEmpresa,
                        'boolCancelacionSafeEntry' => true);
                    $arrayRespuesta = $this->cancelarServiciosOtros($arrayParametrosCancelarServicio);
                    if($arrayRespuesta['status'] != 'OK')
                    {
                        throw new \Exception($arrayRespuesta['mensaje']);           
                    }
                }
                $arrayParametrosCancelacion = array(
                    'objServicio' => $objServicioCancelar,
                    'ipCreacion'  => $strIpCreacion,
                    'strUsrCreacion' => $strUsrCreacion);
                
                $this->finalizarSolicitudCancelacion($arrayParametrosCancelacion);

            }

            //Envio de tareas
            $arrayTareas = array('CONFIG_TAREA_OBRAS_CIVILES', 'CONFIG_TAREA_ELECTRICO');

            foreach ($arrayTareas as $strTarea)
            {
                $arrayParametrosSafe = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getOne('CONFIG TAREAS SAFE ENTRY',
                                                            'COMERCIAL',
                                                            '',
                                                            $strTarea,
                                                            '',
                                                            '',
                                                            '',
                                                            '',
                                                            '',
                                                            $strEmpresaCod);
                
                if(!is_array($arrayParametrosSafe))
                {
                    throw new \Exception('No se ha podido consultar los parametros para la creacion del tarea. Por favor notificar a Sistemas.');
                }

                $objTarea = $this->emSoporte->getRepository('schemaBundle:AdmiTarea')->findOneByNombreTarea($arrayParametrosSafe['valor1']); 

                if(!is_object($objTarea))
                {
                    throw new Exception('No se ha podido consultar la tarea');
                }

                $objDepartamento = $this->emSoporte->getRepository('schemaBundle:AdmiDepartamento')
                                                   ->findOneBy(array('nombreDepartamento' => $arrayParametrosSafe['valor3'],
                                                                     'empresaCod'         => $strEmpresaCod,
                                                                     'estado' => 'Activo'));
                if(!is_object($objDepartamento))
                {
                    throw new Exception('No se pudo encontar el departamento parametrizado');
                }

                $objInfoPersona =  $this->emComercial->getRepository('schemaBundle:InfoPersona')
                                                     ->findOneBy(array('login'  => $strUsrCreacion,
                                                                       'estado' => 'Activo'));

                //Se crea la tarea
                $arrayParamsTarea = array(
                    'strIdEmpresa'          => $strEmpresaCod,
                    'strPrefijoEmpresa'     => $strPrefijoEmpresa,
                    'strNombreTarea'        => $objTarea->getNombreTarea(),
                    'strObservacion'        => $arrayParametrosSafe['valor2'],
                    'strNombreDepartamento' => $objDepartamento->getNombreDepartamento(),
                    'strEmpleado'           => $objInfoPersona->getNombres().' '.$objInfoPersona->getApellidos() ,
                    'strNombreCliente'      => $objCliente->getNombres() ? $objCliente->getNombres().' '.$objCliente->getApellidos()
                                            : $objCliente->getRazonSocial(),
                    'strUsrCreacion'        => $strUsrCreacion,
                    'strIp'                 => $strIpCreacion,
                    'strOrigen'             => 'WEB-TN',
                    'strLogin'              => $objPunto->getLogin(),
                    'intPuntoId'            => $objPunto->getId(),
                    'strValidacionTags'     => 'NO',
                    'boolCrearInfoTarea'    => true);
                $arrayRes = $this->serviceSoporte->ingresarTareaInterna($arrayParamsTarea);

                if($arrayRes['status'] !== 'OK')
                {
                    throw new Exception($arrayRes['mensaje']);
                }

            }

            $strStatus = 'OK';
            $strMensaje = 'Se han cancelado los servicios: '.implode(',',$arrayServiciosCancelar).' automaticamente';

        }
        catch (Exception $e) 
        {
            $strStatus  = 'ERROR';
            $strMensaje = $e->getMessage();
            $this->serviceUtil->insertError('Telcos+',
                                            'InfoCancelarServicioService->verificarServicioSafeEntryCancelar',
                                            $strMensaje,
                                            $strUsrCreacion,
                                            $strIpCreacion);
        }

        return (array('status' => $strStatus ,'mensaje' => $strMensaje));
    }
    
}