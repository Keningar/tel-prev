<?php

namespace telconet\tecnicoBundle\Service;

use telconet\schemaBundle\Entity\InfoInterfaceElemento;
use telconet\schemaBundle\Entity\InfoServicioProdCaract;
use telconet\schemaBundle\Entity\InfoEnlace;
use telconet\schemaBundle\Entity\InfoServicio;
use telconet\schemaBundle\Entity\InfoIp;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolHisto;
use telconet\schemaBundle\Entity\InfoDetalle;
use telconet\schemaBundle\Entity\InfoDetalleElemento;
use telconet\schemaBundle\Entity\InfoDetalleAsignacion;
use telconet\schemaBundle\Entity\InfoDetalleSolCaract;
use telconet\planificacionBundle\Service\RecursosDeRedService;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

/**
 * Clase para la migracion de plataforma
 * 
 * @author Creado: Francisco Adum <fadum@telconet.ec>
 * @version 1.0 8-03-2015
 */
class MigracionHuaweiService {
    private $emComercial;
    private $emInfraestructura;
    private $emSoporte;
    private $emComunicacion;
    private $emSeguridad;
    private $emGeneral;
    private $emNaf;
    private $serviceGeneral;
    private $cancelarService;
    private $activarService;
    private $serviceUtil;
    private $cambioPlanService;
    private $recursoRedService;
    private $container;
    private $host;
    private $pathTelcos;
    private $pathParameters;
    private $serviceRdaMiddleware;
    private $serviceEnvioPlantilla;
    private $strEjecutaComando;
    private $strConfirmacionTNMiddleware;
    private $strOpcion = "MIGRAR";
    private $servicePromociones;
  
    public function setDependencies(Container $objContainer) 
    {
        $this->container            = $objContainer;
        $this->emSoporte            = $this->container->get('doctrine')->getManager('telconet_soporte');
        $this->emInfraestructura    = $this->container->get('doctrine')->getManager('telconet_infraestructura');
        $this->emSeguridad          = $this->container->get('doctrine')->getManager('telconet_seguridad');
        $this->emComercial          = $this->container->get('doctrine')->getManager('telconet');
        $this->emComunicacion       = $this->container->get('doctrine')->getManager('telconet_comunicacion');
        $this->emGeneral            = $this->container->get('doctrine')->getManager('telconet_general');
        $this->emNaf                = $this->container->get('doctrine')->getManager('telconet_naf');
        $this->host                 = $this->container->getParameter('host');
        $this->pathTelcos           = $this->container->getParameter('path_telcos');
        $this->pathParameters       = $this->container->getParameter('path_parameters');
        $this->strEjecutaComando    = $this->container->getParameter('ws_rda_ejecuta_scripts');
        $this->serviceGeneral       = $this->container->get('tecnico.InfoServicioTecnico');
        $this->cancelarService      = $this->container->get('tecnico.InfoCancelarServicio');
        $this->activarService       = $this->container->get('tecnico.InfoActivarPuerto');
        $this->cambioPlanService    = $this->container->get('tecnico.InfoCambiarPlan');
        $this->recursoRedService    = $this->container->get('planificacion.RecursosDeRed');  
        $this->serviceRdaMiddleware = $this->container->get('tecnico.RedAccesoMiddleware');    
        $this->serviceUtil          = $this->container->get('schema.Util');
        $this->servicePromociones   = $this->container->get('tecnico.Promociones');
        $this->serviceEnvioPlantilla= $this->container->get('soporte.EnvioPlantilla');
        $this->strConfirmacionTNMiddleware = $this->container->getParameter('ws_rda_opcion_confirmacion_middleware');
    }
    
    /**
     * Función que sirve para realizar el proceso de reserva de Ips por migración de tecnología de Tellion a Huawei/ZTE
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 29-10-2019
     * 
     * @param array $arrayParametros [
     *                                  "idElemento"                => id del olt,
     *                                  "idElementoIp"              => id del olt nuevo,
     *                                  "strMarcaElemento"          => marca del olt,
     *                                  "usrCreacion"               => usuario de creación,
     *                                  "ipCreacion"                => ip de creación,
     *                                  "strDatabaseDsn"            => Conexión a la Base de Datos
     *                                  "strUserComercial"          => Usuario del esquema Comercial
     *                                  "strPasswordComercial"      => Password del esquema Comercial
     *                                ]
     * @return array $arrayRespuesta [
     *                                  "status"                => OK o ERROR,
     *                                  "mensaje"               => Mensaje informativo
     *                                ]
     */
    public function reservarIpsMigracionOlt($arrayParametros)
    {
        $strDatabaseDsn             = $arrayParametros["strDatabaseDsn"];
        $strUserComercial           = $arrayParametros["strUserComercial"];
        $strPasswordComercial       = $arrayParametros["strPasswordComercial"];
        $intIdElemento              = $arrayParametros["idElemento"];
        $intIdElementoIp            = $arrayParametros["idElementoIp"];
        $strMarcaElemento           = $arrayParametros["strMarcaElemento"];
        $strUsrCreacion             = $arrayParametros["usrCreacion"];
        $strIpCreacion              = $arrayParametros["ipCreacion"];
        $intCantidadIps             = 0;
        $intCantidadIpsFaltantes    = 0;
        $intNumFilaIps              = 1;
        $strPermiteVisualizarError  = "NO";
        $strStatus                  = "";
        $strMensaje                 = "";
        $boolFalse                  = false;
        try
        {
            if(!isset($intIdElemento) || empty($intIdElemento) || !isset($intIdElementoIp) || empty($intIdElementoIp))
            {
                $strPermiteVisualizarError = "SI";
                throw new \Exception("No se han enviado correctamente los parámetros de los elementos");
            }
            
            $objElementoOlt         = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($intIdElemento);
            $objElementoOltNuevo    = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($intIdElementoIp);
            if(!is_object($objElementoOlt) || !is_object($objElementoOltNuevo))
            {
                throw new \Exception("No se han podido obtener los objetos de los olts con los ids enviados por parámetros");
            }
            
            $strMarcaElementoNuevo = $objElementoOltNuevo->getModeloElementoId()->getMarcaElementoId()->getNombreMarcaElemento();
            if(("TELLION" === $strMarcaElemento && "TELLION" === $strMarcaElementoNuevo) ||
               ("ZTE"     === $strMarcaElemento && "ZTE"     === $strMarcaElementoNuevo))
            {
                $strPermiteVisualizarError = "SI";
                throw new \Exception("No se puede realizar reserva de Ips entre OLTs de la misma tecnología");
            }
            
            $objCaractScope = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneBy(array(  'descripcionCaracteristica' => "SCOPE",
                                                                    'estado'                    => 'Activo'));
            if(!is_object($objCaractScope))
            {
                $strPermiteVisualizarError = "SI";
                throw new \Exception("No se ha podido obtener la característica SCOPE");
            }
            
            $arrayRespuestaScope        = $this->recursoRedService->getScopeDisponiblesOlt($intIdElementoIp);
            if ($arrayRespuestaScope['error'] != "")
            {
                $strPermiteVisualizarError = "SI";
                throw new \Exception($arrayRespuestaScope['error']);
            }
            $strContinuaProceso = "SI";
        }
        catch (\Exception $e)
        {
            $strContinuaProceso = "NO";
            if($strPermiteVisualizarError === "SI")
            {
                $strMensaje = $e->getMessage();
            }
            else
            {
                $strMensaje = "No se ha podido realizar el proceso de reserva debido a que existieron problemas al validar los parámetros enviados, "
                              ."favor notificar a Sistemas";
            }
            $this->serviceUtil->insertError('Telcos+', 
                                            'MigracionHuaweiService->reservarIpsMigracionOlt', 
                                            $e->getMessage(), 
                                            $strUsrCreacion, 
                                            $strIpCreacion);
        }
        
        if($strContinuaProceso === "SI")
        {
            $this->emInfraestructura->beginTransaction();
            $this->emComercial->beginTransaction();
            try
            {
                $arrayParametrosConteo      = array("strDatabaseDsn"            => $strDatabaseDsn,
                                                    "strUserComercial"          => $strUserComercial,
                                                    "strPasswordComercial"      => $strPasswordComercial,
                                                    "intIdElementoOlt"          => $intIdElemento,
                                                    "strRetornaDataServicios"   => "SI",
                                                    "strRetornaTotalServicios"  => "NO");
                $arrayRespuestaConteo       = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                                ->getRespuestaServiciosIpMigracion($arrayParametrosConteo);
                $strStatusRespuestaConteo   = $arrayRespuestaConteo["status"];
                if($strStatusRespuestaConteo === "OK")
                {
                    $objCursorServiciosIps  = $arrayRespuestaConteo["objCursorServiciosIps"];
                    if(!empty($objCursorServiciosIps))
                    {
                        while(($arrayRowCursor = oci_fetch_array($objCursorServiciosIps, OCI_ASSOC + OCI_RETURN_NULLS)) != $boolFalse)
                        {
                            $strLogin                   = $arrayRowCursor["LOGIN"];
                            $intIdServicio              = $arrayRowCursor["ID_SERVICIO"];
                            $strEstadoServicio          = $arrayRowCursor["ESTADO_SERVICIO"];
                            $strInformacionIpServicio   = $arrayRowCursor["INFORMACION_IP_SERVICIO"];
                            $intIdProductoIpServicio    = $arrayRowCursor["ID_PRODUCTO_IP_SERVICIO"];
                            $intCantidadIps             = $arrayRowCursor["CANTIDAD_IPS"];
                            $strDescripcionPlanProducto = "";
                            if($intCantidadIpsFaltantes > 0 )
                            {
                                $arrayRespuestaIpsDisponiblesXScope['faltantes'] = $intCantidadIps;
                            }
                            else
                            {
                                $arrayRespuestaIpsDisponiblesXScope = $this->recursoRedService->getIpsDisponiblesPorScopes( $intCantidadIps, 
                                                                                                                            $arrayRespuestaScope);
                            }

                            if($arrayRespuestaIpsDisponiblesXScope['faltantes'] > 0)
                            {
                                $intCantidadIpsFaltantes = $intCantidadIpsFaltantes + $arrayRespuestaIpsDisponiblesXScope['faltantes'];
                            }
                            else
                            {
                                if ($intCantidadIpsFaltantes == 0)
                                {
                                    $strDescripcionPlanProducto     = $arrayRowCursor["DESCRIPCION_PLAN_PRODUCTO"];
                                    $intCountIpsDisponiblesXScope   = count($arrayRespuestaIpsDisponiblesXScope['ips']);
                                    for($intIndxIpsDisponibles = 0; $intIndxIpsDisponibles < $intCountIpsDisponiblesXScope; $intIndxIpsDisponibles++)
                                    {
                                        $intIdIpAnteriorServicio    = 0;
                                        $strIpAnteriorServicio      = "";   
                                        if(!empty($strInformacionIpServicio))
                                        {
                                            list($intIdIpAnteriorServicio, $strIpAnteriorServicio) = explode("***",$strInformacionIpServicio);
                                        }
                                        $objInfoIp = new InfoIp();
                                        $objInfoIp->setIp($arrayRespuestaIpsDisponiblesXScope['ips'][$intIndxIpsDisponibles]['ip']);
                                        $objInfoIp->setTipoIp($arrayRespuestaIpsDisponiblesXScope['ips'][$intIndxIpsDisponibles]['tipo']);
                                        $objInfoIp->setVersionIp('IPV4');
                                        $objInfoIp->setServicioId($intIdServicio);
                                        $objInfoIp->setIpCreacion($strIpCreacion);
                                        $objInfoIp->setFeCreacion(new \DateTime('now'));
                                        $objInfoIp->setUsrCreacion($strUsrCreacion);
                                        $objInfoIp->setEstado('Reservada');
                                        $objInfoIp->setRefIpId($intIdIpAnteriorServicio);
                                        $this->emInfraestructura->persist($objInfoIp);
                                        $this->emInfraestructura->flush();

                                        $objProdCaractScope = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                                ->findOneBy(array(  'productoId'        => $intIdProductoIpServicio,
                                                                                                    'caracteristicaId'  => $objCaractScope->getId(),
                                                                                                    'estado'            => 'Activo'));
                                        if(!is_object($objProdCaractScope))
                                        {
                                            $strPermiteVisualizarError = "SI";
                                            throw new \Exception("No se ha podido obtener la relación del producto IP y la característica SCOPE");
                                        }

                                        $objSpcScope = new InfoServicioProdCaract();
                                        $objSpcScope->setServicioId($intIdServicio);
                                        $objSpcScope->setProductoCaracterisiticaId($objProdCaractScope->getId());
                                        $objSpcScope->setValor($arrayRespuestaIpsDisponiblesXScope['ips'][$intIndxIpsDisponibles]['scope']);
                                        $objSpcScope->setEstado("Reservada");
                                        $objSpcScope->setUsrCreacion($strUsrCreacion);
                                        $objSpcScope->setFeCreacion(new \DateTime('now'));
                                        $this->emComercial->persist($objSpcScope);
                                        $this->emComercial->flush();

                                        $strIpsReservadas = $strIpsReservadas . '<tr>'
                                                            . '<td>'. $intNumFilaIps . '</td>'
                                                            . '<td>'. $strLogin . '</td>'
                                                            . '<td>'. $strDescripcionPlanProducto . '</td>'
                                                            . '<td>'. $strEstadoServicio . '</td>' 
                                                            . '<td>'. $strIpAnteriorServicio . '</td>'
                                                            . '<td>'. $arrayRespuestaIpsDisponiblesXScope['ips'][$intIndxIpsDisponibles]['ip']
                                                            . '</td>'
                                                            . '</tr>';
                                        $intNumFilaIps   = $intNumFilaIps + 1;
                                    }
                                }

                            }
                        }
                    }

                    if ($intCantidadIpsFaltantes > 0)
                    {
                        $strPermiteVisualizarError = "SI";
                        throw new \Exception("Existen ".$intCantidadIpsFaltantes." Ips faltantes, No se pudieron reservar Ips. ".
                                             "Favor solicitar a GEPON ingresar un Nuevo Scope para el Olt");
                    }
                    else
                    {
                        if($strMarcaElementoNuevo === "HUAWEI")
                        {
                            $strMarcaElementoNuevo = "HW";
                        }
                        //Característica para saber si ya fue realizada una reserva de ips por migración
                        $objDetalleReservaIpMigracion = new InfoDetalleElemento();
                        $objDetalleReservaIpMigracion->setElementoId($intIdElemento);
                        $objDetalleReservaIpMigracion->setDetalleNombre("RESERVA IP MIGRACION ".$strMarcaElementoNuevo);
                        $objDetalleReservaIpMigracion->setDetalleValor($intIdElementoIp);
                        $objDetalleReservaIpMigracion->setDetalleDescripcion("Registro que indica si ya se realizo reserva de IPS para este OLT");
                        $objDetalleReservaIpMigracion->setFeCreacion(new \DateTime('now'));
                        $objDetalleReservaIpMigracion->setUsrCreacion($strUsrCreacion);
                        $objDetalleReservaIpMigracion->setIpCreacion($strIpCreacion);
                        $this->emInfraestructura->persist($objDetalleReservaIpMigracion);

                        //Característica para saber si ya fue realizada una reserva de ips por migración
                        $objDetalleReservaIpMigracionHw = new InfoDetalleElemento();
                        $objDetalleReservaIpMigracionHw->setElementoId($intIdElementoIp);
                        $objDetalleReservaIpMigracionHw->setDetalleNombre("RESERVA IP MIGRACION ".$strMarcaElementoNuevo);
                        $objDetalleReservaIpMigracionHw->setDetalleValor($intIdElemento);
                        $objDetalleReservaIpMigracionHw->setDetalleDescripcion("Registro que indica si ya se utilizo este OLT para reservar IPS");
                        $objDetalleReservaIpMigracionHw->setFeCreacion(new \DateTime('now'));
                        $objDetalleReservaIpMigracionHw->setUsrCreacion($strUsrCreacion);
                        $objDetalleReservaIpMigracionHw->setIpCreacion($strIpCreacion);
                        $this->emInfraestructura->persist($objDetalleReservaIpMigracionHw);

                        $this->emInfraestructura->flush();
                        
                        $this->emInfraestructura->commit();
                        $this->emComercial->commit();
                        
                        $arrayParametrosMail    = array('olt_anterior' => $objElementoOlt->getNombreElemento(),
                                                        'olt_nuevo'    => $objElementoOltNuevo->getNombreElemento(),
                                                        'registrosIps' => $strIpsReservadas
                                                       );
                        $this->serviceEnvioPlantilla->generarEnvioPlantilla('Migracion : Reservacion de Ips por OLT' , 
                                                                            '' , 
                                                                            'RES_IP', 
                                                                            $arrayParametrosMail , 
                                                                            '' ,
                                                                            '',
                                                                            '');
                        $strStatus  = "OK";
                        $strMensaje = "Se reservaron las Ips de manera correcta.";
                    }
                }
                else
                {
                    $strPermiteVisualizarError = "SI";
                    throw new \Exception("No se ha podido realizar la consulta de conteo de ips por migración");
                }
            }
            catch (\Exception $e)
            {
                $strStatus = "ERROR";
                if ($this->emInfraestructura->getConnection()->isTransactionActive())
                {
                    $this->emInfraestructura->getConnection()->rollback();
                }
                $this->emInfraestructura->getConnection()->close();
                
                if ($this->emComercial->getConnection()->isTransactionActive())
                {
                    $this->emComercial->getConnection()->rollback();
                }
                $this->emComercial->getConnection()->close();
                
                if($strPermiteVisualizarError === "SI")
                {
                    $strMensaje = $e->getMessage();
                }
                else
                {
                    $strMensaje = "No se ha podido realizar el proceso de reserva de ips, favor notificar a Sistemas";
                }
                $this->serviceUtil->insertError('Telcos+', 
                                                'MigracionHuaweiService->reservarIpsMigracionOlt', 
                                                $e->getMessage(), 
                                                $strUsrCreacion, 
                                                $strIpCreacion);
            }
        }
        else
        {
            $strStatus = "ERROR";
        }
        
        $arrayRespuesta = array("status"    => $strStatus,
                                "mensaje"   => $strMensaje);
        
        return $arrayRespuesta;
    }
    
    /**
     * Funcion que sirve para migrar el cliente de tellion a huawei
     * 
     * @author Creado: Francisco Adum <fadum@telconet.ec>
     * @version 1.0 8-03-2015
     * 
     * @author Creado: Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 8-03-2015 Se agregan validaciones de tipo de aprovisionamiento de Ips Tellion
     * 
     * @author Modificado: Jesus Bozada <jbozada@telconet.ec>
     * @version 1.2 23-02-2016 Se agrega finalización de tareas generadas en el solicitud
     * 
     * @author Modificado: John Vera <javera@telconet.ec>
     * @version 1.3 09-03-2016 Se agrega actualizacion del ldap por fallos en los reversos de los olts
     * 
     * @author Modificado: Jesus Bozada <jbozada@telconet.ec>
     * @version 1.4 25-04-2016 Se corrige forma de obtener el tipo de aprovisionamiento de ips del elemento tellion del cliente previo migración
     * 
     * @author Modificado: Jesus Bozada <jbozada@telconet.ec>
     * @version 1.5 28-04-2016 Se agregan validaciones dentro de proceso para soportar migraciones de clientes tellion CNR
     * 
     * @author Modificado: Jesus Bozada <jbozada@telconet.ec>
     * @version 1.6 09-05-2016 Se agrega parametro empresa en metodo migrarCliente por conflictos de producto INTERNET DEDICADO
     * 
     * @author Modificado: Jesus Bozada <jbozada@telconet.ec>
     * @version 1.7 16-06-2016 Se agrega registro de cancelación de servicio anterior para no presentar error en caso de reintento de migración
     * 
     * @author Modificado: Jesus Bozada <jbozada@telconet.ec>
     * @version 1.8 04-10-2016 Se agrega validación para no generar retiro de equipo wifi cuando el cliente desee conservar el equipo luego de la migración
     * 
     * @author Modificado: Jesus Bozada <jbozada@telconet.ec>
     * @version 1.9 31-10-2016 Se ajusta validación en proceso de mantener equipo wifi adicional para que funcione sin necesidad de aplicar los 
     *                         cambios en el aplicativo mobil y se procese todo de manera correcta
     * 
     * @author Modificado: Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.10 22-12-2016 Se ajusta la consulta al obtener el wifi
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.11 21-12-2017 - En la tabla INFO_DETALLE_ASIGNACION se registra el campo tipo asignado 'EMPLEADO'
     *
     * @author Modificado: Jesús Bozada <jbozada@telconet.ec>
     * @version 2.0 11-04-2019 - Se modifica toda la función para poder realizar migraciones de servicios a las tecnologías HUAWEI/ZTE
     * @since 1.11
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.1 28-06-2019 Se realizan modificaciones para que se pueda realziar la migración de servicios Small Business y TelcoHome
     * 
     * @author Angel Reina <areina@telconet.ec>
     * @version 1.12 17-06-2019 - Se agrega validación $objModeloElementoOlt->getNombreModeloElemento para identificar si la tecnología es HUAWEI modifique la Vlan.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.2 09-06-2019 Se realizan las validaciones necesarias para no finalizar la solicitud de migración en el caso de migraciones
     *                          de servicios con planes nuevos que requieran Extender Dual Band y se agregan validaciones adicionales para
     *                          restringir migraciones con servicios sin registro en la info_ip y cuando el número de ips adicionales a migrar
     *                          no es el mismo número de ips reservadas para la migración 
     * @since 2.1
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 2.3 16-09-2019 Se agrega proceso para validar promociones en servicios de clientes, en caso de 
     *                         que aplique a alguna promoción se le configurarán los anchos de bandas promocionales
     * @since 2.2
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.2 26-02-2020 Se modifica validación para adaptarse a la nueva forma de obtener Ips Small Business y evitar un error
     *                          hasta que se solucione completamente el problema de mapeo de Small Business con sus respectivas Ips
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.3 03-05-2020 Se elimina la función obtenerInfoMapeoProdPrefYProdsAsociados y en su lugar se usa obtenerParametrosProductosTnGpon,
     *                          debido a los cambios realizados por la reestructuración de servicios Small Business
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.3 11-05-2020 Se unifica las validaciones por marca y no por modelo de olt
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 2.4 16-06-2020 Se agrega parámetro equipoOntDualBand en $arrayDatosActivar para formar el JSON que se envía al middleware 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.5 22-09-2020 Se agregan validaciones con nueva función para modelos dual band y además se corrige el valor del parámetro 
     *                         equipoOntDualBand enviado a middleware tomando en cuenta si el plan tiene un Wifi Dual Band o como producto adicional
     *                         y no tomando en cuenta el extender dual band como lo hacía anteriormente
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.6 12-10-2020 Se inicializa la variable $strEquipoOntDualBand a NO para evitar validación de middleware que no permite un valor
     *                         vacío para la migración y se corrige invocación de nombre de service de servicioGeneral a serviceGeneral
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.7 01-12-2020 Se agrega verificación de equipo en NAF de acuerdo a validaciones existentes en AFK_PROCESOS.IN_P_PROCESA_INSTALACION,
     *                          antes de enviar el request a middleware, para evitar errores por NAF que obliguen la eliminación de línea pon
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.8 09-04-2021 Se modifica validación para servicios Wifi Dual Band para evitar usar la función verificaModelosDbEnServicioInternet
     *                         ya que con el cambio de Extenders para V5 queda obsoleta
     *
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 2.8 25-04-2021 Se agregan validaciones que permitirán realizar migraciones de clientes con planes PYME sin IP
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.9 10-11-2021 Se construye el arreglo con la información que se enviará al web service para confirmación de opción de Tn a Middleware
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.10 01-02-2022 Se agrega la validación si es que la tecnología permite validar equipos W y Extender dentro del plan
     * 
     */
    public function migrarCliente($arrayPeticiones)
    {
        $strLogin                   = $arrayPeticiones['login'];
        $strMacOnt                  = $arrayPeticiones['macOnt'];
        $strSerieOnt                = strtoupper($arrayPeticiones['serieOnt']);
        $strModeloOnt               = $arrayPeticiones['modeloOnt'];
        $intIdEmpresa               = $arrayPeticiones['idEmpresa'];
        $strIpCreacion              = $arrayPeticiones['ipCreacion'];
        $intIdServicio              = $arrayPeticiones['idServicio'];
        $intIdProducto              = $arrayPeticiones['idProducto'];
        $strUsrCreacion             = $arrayPeticiones['usrCreacion'];
        $strUltimaMilla             = $arrayPeticiones['ultimaMilla'];
        $strPrefijoEmpresa          = $arrayPeticiones['prefijoEmpresa'];
        $strMantieneEquipoWifi      = $arrayPeticiones['mantieneEquipoWifi'];
        $intInterfaceElementoId     = $arrayPeticiones['interfaceElementoId'];
        $intIdPersonaEmpresaRol     = $arrayPeticiones['idPersonaEmpresaRol'];
        $intSolicitudMigracionId    = $arrayPeticiones['solicitudMigracionId'];
        $strEsIsb                   = $arrayPeticiones['esIsb'] ? $arrayPeticiones['esIsb'] : "NO";
        $objEnlaceWifi              = null;
        $strStatusRespuestaCancelar  = "ERROR";
        $strStatusRespuestaActivar   = "ERROR";
        $strMensajeRespuestaCancelar = "ERROR";
        $strMensajeRespuestaActivar  = "ERROR";
        $strMensaje               = "";
        $strTipoArticulo          = "AF";
        $strIdentificacionCliente = "";
        $strIpServicio            = "";
        $strIpServicioTellion     = "";
        $strScopeServicio         = "";
        $strScopeServicioTellion  = "";
        $intIdSerProAdicional     = "";
        $intIpsFijasActivas       = 0;
        $intIpsFijasReservadas    = 0;
        $arrayIpsAdicionales = [];
        $arrayIpsAdiTellion  = [];
        $strCapacidad1       = "";
        $strCapacidad2       = "";
        $strGemPort          = "";
        $strServiceProfile   = "";
        $strLineProfile      = "";
        $strTrafficTable     = "";
        $strVlan             = "";
        $booleanCambioVlan   = false;
        $booleanIpMigracion  = false;
        $strEquipoOntDualBand= "NO";
        $strProductoEdbEnPlan= "";
        $strTieneIpWan       = "NO";
        $arrayDataConfirmacionTn = array();
        $this->emComercial->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();
        $this->emSoporte->getConnection()->beginTransaction();
        $this->emNaf->getConnection()->beginTransaction();
        
        try
        {
            
            $objServicio          = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
            $objProducto          = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($intIdProducto);
            $objPersona           = $objServicio->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId();
            $strNombreCliente     = ($objPersona->getRazonSocial() != "") ? $objPersona->getRazonSocial() : 
                                    $objPersona->getNombres()." ".$objPersona->getApellidos();
            $strIdentificacion    = $objPersona->getIdentificacionCliente();
            $objServicioTecnico   = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                      ->findOneBy(array( "servicioId" => $objServicio->getId()));
            $strTipoNegocio       = $objServicio->getPuntoId()->getTipoNegocioId()->getNombreTipoNegocio();
            $objInterfaceOlt      = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')->find($intInterfaceElementoId);
            $objElementoOlt       = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($objServicioTecnico->getElementoId());
            $objIpOlt             = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                            ->findOneBy(array("elementoId" => $objElementoOlt->getId(), "estado" => "Activo"));
            $objModeloElementoOlt = $objElementoOlt->getModeloElementoId();
            $strMarcaOlt          = $objElementoOlt->getModeloElementoId()->getMarcaElementoId()->getNombreMarcaElemento();
            if($strPrefijoEmpresa === "MD" && is_object($objServicio->getPlanId()))
            {
                $arrayVerifTecnologiaDualBand   = $this->serviceGeneral->verificaTecnologiaDualBand(array("intIdServicioInternet" => 
                                                                                                          $objServicio->getId()));
                $strStatusVerifTecnologiaDualBand   = $arrayVerifTecnologiaDualBand["status"];
                $strMensajeVerifTecnologiaDualBand  = $arrayVerifTecnologiaDualBand["mensaje"];
                $strModelosEquiposWdb               = $arrayVerifTecnologiaDualBand["modelosEquiposWdb"];
                if($strStatusVerifTecnologiaDualBand === "OK")
                {
                    $arrayInfoVerifVerifTecnologiaDualBand  = explode('|', $strMensajeVerifTecnologiaDualBand);
                    $strEsPermitidoWYExtenderEnPlanes       = $arrayInfoVerifVerifTecnologiaDualBand[2];
                    if(isset($strModelosEquiposWdb) && !empty($strModelosEquiposWdb))
                    {
                        $arrayVerificaWdbPorPunto       = $this->serviceGeneral
                                                               ->verificaProductoPorPunto(array(
                                                                            "intIdServicioInternet"         => $objServicio->getId(),
                                                                            "arrayNombresTecnicoProducto"   => array("WDB_Y_EDB", "WIFI_DUAL_BAND"),
                                                                            "strCodEmpresa"                 => $intIdEmpresa,
                                                                            "strVerificaEquipo"             => "NO",
                                                                            "strVerificaProdEnPlan"         => $strEsPermitidoWYExtenderEnPlanes));
                        $strWdbEncontrado       = $arrayVerificaWdbPorPunto["strProductoEncontrado"];
                        if($strWdbEncontrado === "SI")
                        {
                            $strServiciosAdicWdb    = $arrayVerificaWdbPorPunto["strServiciosAdicProducto"];
                            $arrayServiciosAdicWdb  = $arrayVerificaWdbPorPunto["arrayServiciosAdicProducto"];
                            if($strServiciosAdicWdb === "SI" && !empty($arrayServiciosAdicWdb))
                            {
                                $arrayVerificaModeloWdb = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                          ->getOne( 'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD', 
                                                                                    '',
                                                                                    '',
                                                                                    '',
                                                                                    'MODELOS_EQUIPOS',
                                                                                    '',
                                                                                    '',
                                                                                    'WIFI DUAL BAND',
                                                                                    $strModeloOnt,
                                                                                    $intIdEmpresa);

                                if(!isset($arrayVerificaModeloWdb) || empty($arrayVerificaModeloWdb))
                                {
                                    throw new \Exception("Servicio con ".$arrayServiciosAdicWdb[0]->getProductoId()->getDescripcionProducto()
                                                        .", se requiere la instalación con cualquiera de los siguientes "
                                                        ."modelos de CPE ONT: ".$strModelosEquiposWdb."<br>"
                                                        ."El modelo ".$strModeloOnt. " no es considerado como "
                                                        .$arrayServiciosAdicWdb[0]->getProductoId()->getDescripcionProducto());
                                }
                            }
                            $strEquipoOntDualBand = "SI";
                        }
                    }
                    if($strEsPermitidoWYExtenderEnPlanes === "SI")
                    {
                        $arrayRespuestaProdEdbEnPlan    = $this->serviceGeneral
                                                               ->obtieneProductoEnPlan(array(
                                                                            "intIdPlan"                 => $objServicio->getPlanId()->getId(),
                                                                            "strNombreTecnicoProducto"  => "EXTENDER_DUAL_BAND"));
                        $strProductoEdbEnPlan           = $arrayRespuestaProdEdbEnPlan["strProductoEnPlan"];
                    }
                }
            }
            $objEleCliTellion     = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                            ->find($objServicioTecnico->getElementoClienteId());
            $strSerieOntTellion   = $objEleCliTellion->getSerieFisica();
            $objSpcPerfil         = $this->serviceGeneral->getServicioProductoCaracteristica($objServicio, "PERFIL", $objProducto);
            if(!is_object($objSpcPerfil))
            {
                $strMensaje = 'No existe Caracteristica PERFIL en el servicio, favor revisar!';
                throw new \Exception($strMensaje);
            }
            
            $strPerfil = $objSpcPerfil->getValor();
            $arrayPerfil    = explode("_", $strPerfil);
            if($strEsIsb === "SI")
            {
                $objServProdCaracTipoNegocio = $this->serviceGeneral->getServicioProductoCaracteristica($objServicio,
                                                                                                        "Grupo Negocio",
                                                                                                        $objProducto);
                if(is_object($objServProdCaracTipoNegocio))
                {
                    $strValorTipoNegocioProd    = $objServProdCaracTipoNegocio->getValor();
                    list($strTipoNegocio)       = explode($strPrefijoEmpresa, $strValorTipoNegocioProd);
                }
                else
                {
                    throw new \Exception("No existe Caracteristica Grupo Negocio");
                }
                $strPerfil = $arrayPerfil[0]."_".$arrayPerfil[1]."_".$arrayPerfil[2];
            }
            else
            {
                $strPerfil = $arrayPerfil[0]."_".$arrayPerfil[1];
            }
            
            $objSpcIndice         = $this->serviceGeneral->getServicioProductoCaracteristica($objServicio, "INDICE CLIENTE", $objProducto);
            if(!is_object($objSpcIndice))
            {
                $strMensaje = 'No existe Caracteristica INDICE CLIENTE en el servicio, favor revisar!';
                throw new \Exception($strMensaje);
            }
            $objSpcMacOnt         = $this->serviceGeneral->getServicioProductoCaracteristica($objServicio, "MAC ONT", $objProducto);
            if(!is_object($objSpcMacOnt))
            {
                $strMensaje = 'No existe Caracteristica MAC ONT en el servicio, favor revisar!';
                throw new \Exception($strMensaje);
            }
            $objSpcMacWifi        = $this->serviceGeneral->getServicioProductoCaracteristica($objServicio, "MAC WIFI", $objProducto);
            if(!is_object($objSpcMacWifi))
            {
                $strMensaje = 'No existe Caracteristica MAC WIFI en el servicio, favor revisar!';
                throw new \Exception($strMensaje);
            }
            $objSpcInterfaceTellion = $this->serviceGeneral->getServicioProductoCaracteristica($objServicio, "INTERFACE ELEMENTO TELLION", $objProducto);
            if(!is_object($objSpcInterfaceTellion))
            {
                $strMensaje = 'No existe Caracteristica INTERFACE ELEMENTO TELLION en el servicio, favor revisar!';
                throw new \Exception($strMensaje);
            }
            $intIdIntefarceTellion  = $objSpcInterfaceTellion->getValor();
            if(empty($intIdIntefarceTellion))
            {
                $strMensaje = 'No existe valor en la Caracteristica INTERFACE ELEMENTO TELLION en el servicio, favor revisar!';
                throw new \Exception($strMensaje);
            }
            $objInterfaceOltTellion    = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')->find($intIdIntefarceTellion);
            if(!is_object($objInterfaceOltTellion))
            {
                $strMensaje = 'No existe INTERFACE ELEMENTO TELLION en el servicio a migrar, favor revisar!';
                throw new \Exception($strMensaje);
            }
            $objElementoOltTellion        = $objInterfaceOltTellion->getElementoId();
            $objModeloElementoOltTellion  = $objElementoOltTellion->getModeloElementoId();
            $objSolicitudMigracion        = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->find($intSolicitudMigracionId);
            if(!is_object($objSolicitudMigracion))
            {
                $strMensaje = 'No existe SOLICITUD DE MIGRACIÓN en el servicio a migrar, favor revisar!';
                throw new \Exception($strMensaje);
            }
            $objInterfaceElementoSplitter = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                    ->find($objServicioTecnico->getInterfaceElementoConectorId());
            $objIpOltTellion              = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                                    ->findOneBy(array("elementoId" => $objElementoOltTellion->getId(),
                                                                                      "estado"     => "Activo"));
            
            if ($strMarcaOlt == "HUAWEI")
            {
                $objSpcServiceProfile = $this->serviceGeneral->getServicioProductoCaracteristica($objServicio, "SERVICE-PROFILE", $objProducto);
                if(is_object($objSpcServiceProfile))
                {
                    $strServiceProfile = $objSpcServiceProfile->getValor();
                }
                else
                {
                    $objDetalleElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                               ->findOneBy(array("detalleNombre" => "SERVICE-PROFILE-NAME",
                                                                 "detalleValor"  => $strModeloOnt, 
                                                                 "elementoId"    => $objElementoOlt->getId()
                                                                )
                                                          );
                    if(is_object($objDetalleElemento))
                    {
                        $strServiceProfile = $objDetalleElemento->getDetalleValor();
                        $this->serviceGeneral->ingresarServicioProductoCaracteristica($objServicio, 
                                                                                      $objProducto, 
                                                                                      "SERVICE-PROFILE", 
                                                                                      $strServiceProfile, 
                                                                                      $strUsrCreacion);
                    }
                    else
                    {
                        $strMensaje = 'No existe Caracteristica SERVICE-PROFILE-ID en el elemento, favor revisar!';
                        throw new \Exception($strMensaje);
                    }
                }
            }
            else
            {
                $strServiceProfile = $strModeloOnt;
                $this->serviceGeneral->ingresarServicioProductoCaracteristica($objServicio, 
                                                                              $objProducto, 
                                                                              "SERVICE-PROFILE", 
                                                                              $strServiceProfile, 
                                                                              $strUsrCreacion);   
            }
            
            if ($strMarcaOlt == "HUAWEI")
            {
                $objSpcLineProfile = $this->serviceGeneral->getServicioProductoCaracteristica($objServicio, "LINE-PROFILE-NAME", $objProducto);
                if(!is_object($objSpcLineProfile))
                {
                    $strMensaje = 'No existe Caracteristica LINE-PROFILE-NAME, favor revisar!';
                    throw new \Exception($strMensaje);
                }
                $strLineProfile = $objSpcLineProfile->getValor();

                $objSpcVlan = $this->serviceGeneral->getServicioProductoCaracteristica($objServicio, "VLAN", $objProducto);
                
                if(!is_object($objSpcVlan))
                {
                    $strMensaje = 'No existe Caracteristica VLAN, favor revisar!';
                    throw new \Exception($strMensaje);
                }
                
                $strVlan = $objSpcVlan->getValor();
                

                $objSpcGemPort = $this->serviceGeneral->getServicioProductoCaracteristica($objServicio, "GEM-PORT", $objProducto);
                if(!is_object($objSpcGemPort))
                {
                    $strMensaje = 'No existe Caracteristica GEM-PORT, favor revisar!';
                    throw new \Exception($strMensaje);
                }
                $strGemPort = $objSpcGemPort->getValor();

                //obtener traffic table
                $objSpcTrafficTable = $this->serviceGeneral->getServicioProductoCaracteristica($objServicio, "TRAFFIC-TABLE", $objProducto);
                if(!is_object($objSpcTrafficTable))
                {
                    $strMensaje = 'No existe Caracteristica TRAFFIC-TABLE, favor revisar!';
                    throw new \Exception($strMensaje);
                }
                $strTrafficTable = $objSpcTrafficTable->getValor();
                
            }
            else if ($strMarcaOlt == "ZTE")
            {
                $objSpcCapacidad1 = $this->serviceGeneral->getServicioProductoCaracteristica($objServicio, "CAPACIDAD1", $objProducto);
                if(!is_object($objSpcCapacidad1))
                {
                    $strMensaje = 'No existe Caracteristica CAPACIDAD1, favor revisar!';
                    throw new \Exception($strMensaje);
                }
                $strCapacidad1 = $objSpcCapacidad1->getValor();

                $objSpcCapacidad2 = $this->serviceGeneral->getServicioProductoCaracteristica($objServicio, "CAPACIDAD2", $objProducto);
                if(!is_object($objSpcCapacidad2))
                {
                    $strMensaje = 'No existe Caracteristica CAPACIDAD2, favor revisar!';
                    throw new \Exception($strMensaje);
                }
                $strCapacidad2 = $objSpcCapacidad2->getValor();
            }
            else
            {
                $strMensaje = "Tecnología ".$strMarcaOlt." no permitida para realizar migración de servicio.";
                throw new \Exception($strMensaje);
            }
            
            $objEnlaceCliente = $this->emInfraestructura
                                     ->getRepository('schemaBundle:InfoEnlace')
                                     ->findOneBy(array("interfaceElementoIniId" => $objServicioTecnico->getInterfaceElementoClienteId(),
                                                       "estado"                 => "Activo"));
            if (!is_object($objEnlaceCliente))
            {
                $strMensaje = "Los enlaces de los equipos Tellion del cliente se encuentran incorrectos, favor revisar!";
                throw new \Exception($strMensaje);
            }
            $arrayProdIp = array();
            //SETEO DE IPS ANTIGUAS Y NUEVAS A UTILIZAR EN LA MIGRACIÓN DEL SERVICIO
            if ($strTipoNegocio == "PRO" || $strTipoNegocio == "PYME")
            {
                if($strEsIsb === "SI")
                {
                    $objProductoIp          = $objProducto;
                    $arrayPlanDet           = array();
                    $arrayParamsInfoProds   = array("strValor1ParamsProdsTnGpon"    => "PRODUCTOS_RELACIONADOS_INTERNET_IP",
                                                    "strCodEmpresa"                 => $intIdEmpresa,
                                                    "intIdProductoInternet"         => $objProducto->getId());
                    $arrayInfoMapeoProds    = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                ->obtenerParametrosProductosTnGpon($arrayParamsInfoProds);
                    if(isset($arrayInfoMapeoProds) && !empty($arrayInfoMapeoProds))
                    {
                        foreach($arrayInfoMapeoProds as $arrayInfoProd)
                        {
                            $intIdProdIp  = $arrayInfoProd["intIdProdIp"];
                            $arrayProdIp[]  = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($intIdProdIp);
                        }
                    }
                    else
                    {
                        $strMensaje = "No se ha podido obtener el correcto mapeo del servicio con la ip respectiva";
                        throw new \Exception($strMensaje);
                    }
                }
                else
                {
                    $objPlanCab         = $objServicio->getPlanId();
                    $arrayPlanDet       = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                                            ->findBy(array("planId" => $objPlanCab->getId()));
                    $arrayProdIp        = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                            ->findBy(array("nombreTecnico" => "IP", 
                                                                           "empresaCod"    => $intIdEmpresa, 
                                                                           "estado"        => "Activo"));
                }
                
                $arrayServiciosIps = $this->emComercial
                                          ->getRepository('schemaBundle:InfoServicio')
                                          ->findBy(array("puntoId" => $objServicio->getPuntoId()->getId(), "estado" => "Activo"));
                
                $arrayDatosIpTellion    = $this->serviceGeneral
                                               ->getInfoIpsFijaPunto($arrayServiciosIps,
                                                                     $arrayProdIp, 
                                                                     $objServicio,
                                                                     'Activo',
                                                                     'Activo',
                                                                     $objProducto);
                
                $arrayIpsAdiTellion     = $arrayDatosIpTellion['valores'];
                $intIpsFijasActivas     = $arrayDatosIpTellion['ip_fijas_activas'];
                
                $strValidaIpsTellion    = "";
                if(isset($arrayIpsAdiTellion) && !empty($arrayIpsAdiTellion))
                {
                    foreach($arrayIpsAdiTellion as $arrayIpTellion)
                    {
                        if(!empty($arrayIpTellion['id_servicio']) && empty($arrayIpTellion['ip']))
                        {
                            $strValidaIpsTellion .= 'El servicio IP con id '.$arrayIpTellion['id_servicio']
                                                    .' que se desea migrar no tiene asignada una ip '
                                                    .'en Telcos. Por favor notificar a Sistemas! <br>';
                            
                        }
                    }
                }
                
                if(!empty($strValidaIpsTellion))
                {
                    $strMensaje = $strValidaIpsTellion;
                    throw new \Exception($strMensaje);
                }
                
                $objIpFijaServicio = $this->emInfraestructura
                                          ->getRepository('schemaBundle:InfoIp')
                                          ->findOneBy(array("servicioId" => $objServicio->getId(),
                                                            "tipoIp"     => "FIJA",
                                                            "estado"     => "Activo"));
                
                if(isset($arrayPlanDet) && !empty($arrayPlanDet) && isset($arrayProdIp) && !empty($arrayProdIp))
                {
                    $intIndiceProductoIp = $this
                                            ->serviceGeneral
                                            ->obtenerIndiceInternetEnPlanDet($arrayPlanDet, $arrayProdIp);

                    if ($intIndiceProductoIp!=-1)
                    {
                        $objProductoIp = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                           ->find($arrayPlanDet[$intIndiceProductoIp]->getProductoId());
                    } 
                }
                
                $objCaracScope = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                   ->findOneBy(array("descripcionCaracteristica" => "SCOPE",
                                                                     "estado"                    => "Activo"));
                
                if(is_object($objCaracScope))
                {
                    if (is_object($objProductoIp))
                    {
                        $objProdCarac = $this->emComercial
                                             ->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                             ->findOneBy(array("productoId"       => $objProductoIp->getId(),
                                                               "caracteristicaId" => $objCaracScope->getId(),
                                                               "estado"           => "Activo"));
                        
                        if (!is_object($objProdCarac))
                        {
                            $strMensaje = 'No existe PRODUCTO CARACTERISTICA SCOPE, favor revisar!';
                            throw new \Exception($strMensaje);
                        }
                    }
                }
                else
                {
                    $strMensaje = 'No existe CARACTERISTICA SCOPE, favor revisar!';
                    throw new \Exception($strMensaje);
                }
                
                
                if (is_object($objIpFijaServicio))
                {
                    
                    //IP TELLION
                    if (!is_object($objProductoIp))
                    {
                        $strMensaje = 'No existe PRODUCTO IP dentro del plan, favor revisar!';
                        throw new \Exception($strMensaje);
                    }
                    $strIpServicioTellion   = $objIpFijaServicio->getIp();
                    
                    $objSpcScopeTellion = $this->serviceGeneral->getServicioProductoCaracteristica($objServicio, 
                                                                                                       "SCOPE", 
                                                                                                       $objProductoIp);
                    if(!is_object($objSpcScopeTellion))
                    {
                        $strMensaje = 'No existe SCOPE en el servicio a migrar, favor revisar!';
                        throw new \Exception($strMensaje);
                    }
                    $strScopeServicioTellion = $objSpcScopeTellion->getValor();
                    
                    
                    //IP A MIGRAR
                    
                    $objIpFijaReservada = $this->emInfraestructura
                                               ->getRepository('schemaBundle:InfoIp')
                                               ->findOneBy(array("servicioId" => $objServicio->getId(),
                                                                 "tipoIp"     => "FIJA",
                                                                 "estado"     => "Reservada"));
                    
                    if (is_object($objIpFijaReservada))
                    {
                        $strIpServicio = $objIpFijaReservada->getIp();
                        
                        $objSpcScope = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                         ->findOneBy(array("servicioId"                => $objServicio->getId(),
                                                                           "productoCaracterisiticaId" => $objProdCarac->getId(),
                                                                           "estado"                    => 'Reservada'));
                        if (!is_object($objSpcScope))
                        {
                            $strMensaje = 'No existe SCOPE RESERVADO en el servicio a migrar, favor revisar!';
                            throw new \Exception($strMensaje);
                        }
                        $strScopeServicio = $objSpcScope->getValor();
                    }
                    else
                    {
                        $strMensaje = 'No existe IP RESERVADA en el servicio a migrar, favor revisar!';
                        throw new \Exception($strMensaje);
                    }
                }
                else if ($strTipoNegocio == "PYME")
                {
                    $arrayParametrosIpWan = array('objPunto'       => $objServicio->getPuntoId(),
                                                  'strEmpresaCod'  => $intIdEmpresa,
                                                  'strUsrCreacion' => $strUsrCreacion,
                                                  'strIpCreacion'  => $strIpCreacion);
                    $arrayDatosIpWan      = $this->serviceGeneral
                                                 ->getIpFijaWan($arrayParametrosIpWan);
                    if (isset($arrayDatosIpWan['strStatus'])       && !empty($arrayDatosIpWan['strStatus'])     &&
                        $arrayDatosIpWan['strStatus'] === 'OK'     && isset($arrayDatosIpWan['strExisteIpWan']) &&
                        !empty($arrayDatosIpWan['strExisteIpWan']) &&  $arrayDatosIpWan['strExisteIpWan'] === 'SI')
                    {
                        $strTieneIpWan           = $arrayDatosIpWan['strExisteIpWan'];
                        $strIpServicioTellion    = $arrayDatosIpWan['arrayInfoIp']['strIp'];
                        $strScopeServicioTellion = $arrayDatosIpWan['arrayInfoIp']['strScope'];
                        $intIdServicioIpWan      = $arrayDatosIpWan['arrayInfoIp']['intIdServicioIp'];
                        $objIpFijaServicio       = $arrayDatosIpWan['arrayInfoIp']['objIp'];
                        $objSpcScopeTellion      = $arrayDatosIpWan['arrayInfoIp']['objScope'];
                        $objServicioIpWan        = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicioIpWan);
                        $objIpFijaReservada      = $this->emInfraestructura
                                                        ->getRepository('schemaBundle:InfoIp')
                                                        ->findOneBy(array("servicioId" => $intIdServicioIpWan,
                                                                          "estado"     => "Reservada"));
                        $objSpcMacIpWan = $this->serviceGeneral->getServicioProductoCaracteristica($objServicioIpWan, "MAC", $objProducto);
                        if(!is_object($objSpcMacIpWan))
                        {
                            $strMensaje = 'No existe Caracteristica MAC en la IP del servicio, favor revisar!';
                            throw new \Exception($strMensaje);
                        }
                        if (is_object($objIpFijaReservada))
                        {
                            $strIpServicio = $objIpFijaReservada->getIp();
                            $objSpcScope   = $this->emComercial
                                                  ->getRepository('schemaBundle:InfoServicioProdCaract')
                                                  ->findOneBy(
                                                    array("servicioId"                => $objServicioIpWan->getId(),
                                                          "productoCaracterisiticaId" => $objSpcScopeTellion->getProductoCaracterisiticaId(),
                                                          "estado"                    => 'Reservada'));
                            if (!is_object($objSpcScope))
                            {
                                $strMensaje = 'No existe SCOPE RESERVADO en el servicio a migrar, favor revisar!';
                                throw new \Exception($strMensaje);
                            }
                            $strScopeServicio = $objSpcScope->getValor();
                        }
                        else
                        {
                            $strMensaje = 'No existe IP RESERVADA en el servicio a migrar, favor revisar!';
                            throw new \Exception($strMensaje);
                        }
                    }
                }
                
                if ($intIpsFijasActivas > 0)
                {
                    $arrayDatosIpReservadas  = $this->serviceGeneral
                                                    ->getInfoIpsFijaPunto($arrayServiciosIps,
                                                                          $arrayProdIp, 
                                                                          $objServicio,
                                                                          'Activo',
                                                                          'Reservada',
                                                                          $objProducto);
                    $arrayIpsAdicionales    = $arrayDatosIpReservadas['valores'];
                    $intIpsFijasReservadas  = $arrayDatosIpReservadas['ip_fijas_activas'];
                    $strValidaIpsAdicionales= "";
                    if(isset($arrayIpsAdicionales) && !empty($arrayIpsAdicionales))
                    {
                        foreach($arrayIpsAdicionales as $arrayIpAdicional)
                        {
                            if(!empty($arrayIpAdicional['id_servicio']) && empty($arrayIpAdicional['ip']))
                            {
                                $strValidaIpsAdicionales    .= 'El servicio IP con id '.$arrayIpAdicional['id_servicio']
                                                               .' al que se va a migrar no tiene reservada una ip  '
                                                               .'en Telcos. Por favor notificar a Sistemas! <br>';
                            }
                        }
                    }
                    
                    if(!empty($strValidaIpsAdicionales))
                    {
                        $strMensaje = $strValidaIpsAdicionales;
                        throw new \Exception($strMensaje);
                    }
                    
                    if ($strTipoNegocio == "PRO" && !is_object($objIpFijaServicio))
                    {
                        $strIpServicioTellion    = $arrayIpsAdiTellion[0]['ip'];
                        $strScopeServicioTellion = $arrayIpsAdiTellion[0]['scope'];
                        $intIdSerProAdicional    = $arrayIpsAdiTellion[0]['id_servicio'];
                        $intIdScopeTellion       = $arrayIpsAdiTellion[0]['intIdSpcScope'];
                        $intIdMacTellion         = $arrayIpsAdiTellion[0]['intIdSpcMac'];
                        
                        $arrayIpsAdiTellion      = array();

                        $strIpServicio         = $arrayIpsAdicionales[0]['ip'];
                        $strScopeServicio      = $arrayIpsAdicionales[0]['scope'];
                        $intIdScope            = $arrayIpsAdicionales[0]['intIdSpcScope'];
                        $arrayIpsAdicionales   = array();
                        
                        if($intIpsFijasReservadas > 0 && $intIpsFijasActivas > 0)
                        {
                            $intIpsFijasReservadas = 0;
                            $intIpsFijasActivas    = 0;
                            $booleanIpMigracion    = true;
                            $strVlan = "302";
                            $booleanCambioVlan = true;                            
                        }

                        
                    }
                }
            }
            
            $arrayVerifOntNaf   = $this->serviceGeneral->buscarEquipoEnNafPorParametros(array( "serieEquipo"           => $strSerieOnt,
                                                                                                "estadoEquipo"          => "PI",
                                                                                                "tipoArticuloEquipo"    => "AF",
                                                                                                "modeloEquipo"          => $strModeloOnt));
            if($arrayVerifOntNaf["status"] === "ERROR")
            {
                $strMensaje = $arrayVerifOntNaf["mensaje"];
                throw new \Exception($strMensaje);
            }
            $arrayDatosCancelar = array (
                                        'serial_ont'            => $strSerieOntTellion,
                                        'mac_ont'               => $objSpcMacOnt->getValor(),
                                        'nombre_olt'            => $objElementoOltTellion->getNombreElemento(),
                                        'ip_olt'                => $objIpOltTellion->getIp(),
                                        'puerto_olt'            => $objInterfaceOltTellion->getNombreInterfaceElemento(),
                                        'modelo_olt'            => $objModeloElementoOltTellion->getNombreModeloElemento(),
                                        'ont_id'                => $objSpcIndice->getValor(),
                                        'service_port'          => '',
                                        'gemport'               => '',
                                        'service_profile'       => '',
                                        'line_profile'          => $strPerfil,
                                        'traffic_table'         => '',
                                        'vlan'                  => '',
                                        'estado_servicio'       => $objServicio->getEstado(),
                                        'ip'                    => $strIpServicioTellion,
                                        'ip_fijas_activas'      => $intIpsFijasActivas, //cantidad de ips fijas que tiene el cliente
                                        'tipo_negocio_actual'   => $strTipoNegocio,
                                        'mac_wifi'              => $objSpcMacWifi->getValor(),
                                        'scope'                 => $strScopeServicioTellion,
                                        'ip_cancelar'           => $arrayIpsAdiTellion,
                                        'capacidad_up'          => '',
                                        'capacidad_down'        => ''
                                        );

            $arrayDatosActivar  = array(
                                       'serial_ont'            => $strSerieOnt,
                                       'mac_ont'               => $strMacOnt,
                                       'nombre_olt'            => $objElementoOlt->getNombreElemento(),
                                       'ip_olt'                => $objIpOlt->getIp(),
                                       'puerto_olt'            => $objInterfaceOlt->getNombreInterfaceElemento(),
                                       'modelo_olt'            => $objModeloElementoOlt->getNombreModeloElemento(),
                                       'gemport'               => $strGemPort,
                                       'service_profile'       => $strServiceProfile,
                                       'line_profile'          => $strLineProfile,
                                       'traffic_table'         => $strTrafficTable,
                                       'vlan'                  => $strVlan,
                                       'estado_servicio'       => $objServicio->getEstado(),
                                       'ip'                    => $strIpServicio,
                                       'ip_fijas_activas'      => $intIpsFijasReservadas, //cantidad de ips fijas que tiene el cliente
                                       'tipo_negocio_actual'   => $strTipoNegocio,
                                       'mac_wifi'              => '',
                                       'scope'                 => $strScopeServicio,
                                       'ip_activar'            => $arrayIpsAdicionales,
                                       'capacidad_up'          => $strCapacidad1,
                                       'capacidad_down'        => $strCapacidad2,
                                       'equipoOntDualBand'     => $strEquipoOntDualBand
                                       );

            $arrayDatos = array("datos_cancelar" => $arrayDatosCancelar,
                                "datos_activar"  => $arrayDatosActivar);


            if ($strPrefijoEmpresa === 'MD')
            {
                $arrayRespuestaSeteaInfo = $this->serviceGeneral
                                                ->seteaInformacionPlanesPyme(array("intIdPlan"       => $objServicio->getPlanId()->getId(),
                                                                                   "intIdPunto"      => $objServicio->getPuntoId()->getId(),
                                                                                   "strConservarIp"  => "",
                                                                                   "strTipoNegocio"  => $strTipoNegocio,
                                                                                   "strPrefijoEmpresa" => $strPrefijoEmpresa,
                                                                                   "strUsrCreacion"    => $strUsrCreacion,
                                                                                   "strIpCreacion"     => $strIpCreacion,
                                                                                   "strTipoProceso"    => "MIGRAR_PLAN",
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
            
            $arrayDatosMiddleware = array(
                                          'empresa'               => $strPrefijoEmpresa,
                                          'nombre_cliente'        => $strNombreCliente,
                                          'login'                 => $strLogin,
                                          'identificacion'        => $strIdentificacion,
                                          'datos'                 => $arrayDatos,
                                          'opcion'                => $this->strOpcion,
                                          'ejecutaComando'        => $this->strEjecutaComando,
                                          'usrCreacion'           => $strUsrCreacion,
                                          'ipCreacion'            => $strIpCreacion
                                         );
            
            $arrayRespuesta = $this->serviceRdaMiddleware->middleware(json_encode($arrayDatosMiddleware));
            
            $strStatusRespuestaCancelar  = $arrayRespuesta['status_cancelar'];
            $strMensajeRespuestaCancelar = $arrayRespuesta['mensaje_cancelar'];
            $strStatusRespuestaActivar   = $arrayRespuesta['status_activar'];
            $strMensajeRespuestaActivar  = $arrayRespuesta['mensaje_activar'];
            
            if($strStatusRespuestaCancelar == "OK" && $strStatusRespuestaActivar == "OK")
            {
                $arrayDataConfirmacionTn    = array('nombre_cliente'    => $strNombreCliente,
                                                    'login'             => $strLogin,
                                                    'identificacion'    => $strIdentificacion,
                                                    'datos'             => array(   'datos_cancelar'        => $arrayDatosCancelar,
                                                                                    'datos_activar'         => $arrayDatosActivar,
                                                                                    'opcion_confirmacion'   => $this->strOpcion,
                                                                                    'respuesta_confirmacion'=> 'ERROR'),
                                                    'opcion'            => $this->strConfirmacionTNMiddleware,
                                                    'ejecutaComando'    => $this->strEjecutaComando,
                                                    'usrCreacion'       => $strUsrCreacion,
                                                    'ipCreacion'        => $strIpCreacion,
                                                    'empresa'           => $strPrefijoEmpresa,
                                                    'statusMiddleware'  => 'OK');
                
                //eliminar serv prod caract de indice cliente anterior
                $this->serviceGeneral->setEstadoServicioProductoCaracteristica($objSpcIndice,  "Eliminado");
                $this->serviceGeneral->setEstadoServicioProductoCaracteristica($objSpcMacOnt,  "Eliminado");
                $this->serviceGeneral->setEstadoServicioProductoCaracteristica($objSpcMacWifi, "Eliminado");
                $this->serviceGeneral->setEstadoServicioProductoCaracteristica($objSpcPerfil,  "Eliminado");
                
                
                //GRABAMOS INDICE_CLIENTE
                $this->serviceGeneral->ingresarServicioProductoCaracteristica($objServicio, 
                                                                              $objProducto, 
                                                                              "INDICE CLIENTE", 
                                                                              $arrayRespuesta['ont_id'], 
                                                                              $strUsrCreacion);

                
                //GRABAMOS SPID
                $this->serviceGeneral->ingresarServicioProductoCaracteristica($objServicio, 
                                                                              $objProducto, 
                                                                              "SPID", 
                                                                              $arrayRespuesta['spid'], 
                                                                              $strUsrCreacion);

                if ($strMarcaOlt == "ZTE")
                {
                    //GRABAMOS VLAN
                    $this->serviceGeneral
                         ->ingresarServicioProductoCaracteristica( $objServicio, 
                                                                   $objProducto, 
                                                                   "VLAN", 
                                                                   $arrayRespuesta['vlan'], 
                                                                   $strUsrCreacion );

                    //GRABAMOS CLIENT CLASS
                    $this->serviceGeneral
                         ->ingresarServicioProductoCaracteristica( $objServicio, 
                                                                   $objProducto, 
                                                                   "CLIENT CLASS", 
                                                                   $arrayRespuesta['client_class'], 
                                                                   $strUsrCreacion );

                    //GRABAMOS PCKID
                    $this->serviceGeneral
                         ->ingresarServicioProductoCaracteristica( $objServicio, 
                                                                   $objProducto, 
                                                                   "PACKAGE ID", 
                                                                   $arrayRespuesta['pckid'], 
                                                                   $strUsrCreacion );

                    //GRABAMOS LINE PROFILE NAME
                    $this->serviceGeneral
                         ->ingresarServicioProductoCaracteristica( $objServicio, 
                                                                   $objProducto, 
                                                                   "LINE-PROFILE-NAME", 
                                                                   $arrayRespuesta['line_profile'], 
                                                                   $strUsrCreacion );
                    
                }
                
                if ($strTipoNegocio == "PRO" || $strTipoNegocio == "PYME")
                {
                    
                    if ($booleanCambioVlan && $strMarcaOlt === "HUAWEI")
                    {
                        $objSpcVlan->setValor($strVlan);
                        $this->emComercial->persist($objSpcVlan);
                        $this->emComercial->flush();                                            
                    }
                    
                    if($booleanIpMigracion)
                    {
                        //IP CANCELAR
                        $objIpFijaCancelar = $this->emInfraestructura
                                            ->getRepository('schemaBundle:InfoIp')
                                            ->findOneBy(array("servicioId" => $intIdSerProAdicional,
                                                              "tipoIp"     => "FIJA",
                                                              "ip"         => $strIpServicioTellion,
                                                              "estado"     => "Activo"));

                        $objIpFijaCancelar->setEstado("Cancel");
                        $this->emInfraestructura->persist($objIpFijaCancelar);
                        $this->emInfraestructura->flush();
                        
                        //IP ACTIVAR
                        $objIpFijaActivar = $this->emInfraestructura
                                                   ->getRepository('schemaBundle:InfoIp')
                                                   ->findOneBy(array("servicioId" => $intIdSerProAdicional,
                                                                     "tipoIp"     => "FIJA",
                                                                     "ip"         => $strIpServicio,
                                                                     "estado"     => "Reservada"));
                        
                        $objIpFijaActivar->setEstado("Activo");
                        $this->emInfraestructura->persist($objIpFijaActivar);
                        $this->emInfraestructura->flush();

                        //Se elimina el scope anterior de la ip
                        $objSpcScopeTellion = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')->find($intIdScopeTellion);
                        $this->serviceGeneral->setEstadoServicioProductoCaracteristica($objSpcScopeTellion, "Eliminado");
                        //Se cambia a estado Activo el nuevo scope de la ip
                        $objSpcScopeNuevo = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')->find($intIdScope);
                        $this->serviceGeneral->setEstadoServicioProductoCaracteristica($objSpcScopeNuevo, "Activo");
                        //se elimina la MAC anterior de la ip
                        $objSpcMacTellion = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')->find($intIdMacTellion);
                        $this->serviceGeneral->setEstadoServicioProductoCaracteristica($objSpcMacTellion, "Eliminado");
                        //se crea la nueva MAC relaciona a la ip del servicio
                        $objServicioIpPro = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdSerProAdicional);
                        $this->serviceGeneral->ingresarServicioProductoCaracteristica($objServicioIpPro, 
                                                                                      $objProducto,
                                                                                      "MAC",
                                                                                      $strMacOnt,
                                                                                      $strUsrCreacion);
                        
                    }
                        
                        
                    if (is_object($objIpFijaServicio) && is_object($objIpFijaReservada))
                    {
                        $objIpFijaServicio->setEstado("Cancel");
                        $this->emInfraestructura->persist($objIpFijaServicio);
                        $this->emInfraestructura->flush();
                        
                        $objIpFijaReservada->setEstado("Activo");
                        $this->emInfraestructura->persist($objIpFijaReservada);
                        $this->emInfraestructura->flush();
                        
                        $this->serviceGeneral->setEstadoServicioProductoCaracteristica($objSpcScopeTellion, "Eliminado");
                        $this->serviceGeneral->setEstadoServicioProductoCaracteristica($objSpcScope, "Activo");
                    }
                    
                    if ($intIpsFijasActivas > 0)
                    {                    
                        if ($strTipoNegocio == "PRO" && !is_object($objIpFijaServicio))
                        {
                            //Se elimina el scope anterior de la ip
                            $objSpcScopeTellion = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')->find($intIdScopeTellion);
                            $this->serviceGeneral->setEstadoServicioProductoCaracteristica($objSpcScopeTellion, "Eliminado");
                            //Se cambia a estado Activo el nuevo scope de la ip
                            $objSpcScopeNuevo = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')->find($intIdScope);
                            $this->serviceGeneral->setEstadoServicioProductoCaracteristica($objSpcScopeNuevo, "Activo");
                            //se elimina la MAC anterior de la ip
                            $objSpcMacTellion = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')->find($intIdMacTellion);
                            $this->serviceGeneral->setEstadoServicioProductoCaracteristica($objSpcMacTellion, "Eliminado");
                            //se crea la nueva MAC relaciona a la ip del servicio
                            $objServicioIpPro = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdSerProAdicional);
                            $this->serviceGeneral->ingresarServicioProductoCaracteristica($objServicioIpPro, 
                                                                                          $objProducto,
                                                                                          "MAC",
                                                                                          $strMacOnt,
                                                                                          $strUsrCreacion);

                        }
                        else
                        {
                            foreach($arrayIpsAdiTellion as $arrayIpAdicTellion)
                            {
                                $intIdSerIpAdicional   = $arrayIpAdicTellion['id_servicio'];
                                
                                $objSpcScopeTellionAdi = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                              ->find($arrayIpAdicTellion['intIdSpcScope']);
                                $this->serviceGeneral->setEstadoServicioProductoCaracteristica($objSpcScopeTellionAdi, "Eliminado");
                                $objIpFijaServicio = $this->emInfraestructura
                                                          ->getRepository('schemaBundle:InfoIp')
                                                          ->findOneBy(array("servicioId" => $intIdSerIpAdicional,
                                                                            "tipoIp"     => "FIJA",
                                                                            "estado"     => "Activo"));
                                $objIpFijaServicio->setEstado("Cancel");
                                $this->emInfraestructura->persist($objIpFijaServicio);
                                $this->emInfraestructura->flush();
                                
                                //historial del servicio
                                $objServicioHistorial = new InfoServicioHistorial();
                                $objServicioHistorial->setServicioId($objServicio);
                                $objServicioHistorial->setObservacion("Se migro cancelacion Ip adicional Tellion");
                                $objServicioHistorial->setEstado("Cancel");
                                $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                                $objServicioHistorial->setIpCreacion($strIpCreacion);
                                $this->emComercial->persist($objServicioHistorial);
                                $this->emComercial->flush();
                            }
                            
                            foreach($arrayIpsAdicionales as $arrayIpAdicional)
                            {
                                $intIdSerIpAdicional   = $arrayIpAdicional['id_servicio'];
                                $objSpcScopeTellionAdi = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                              ->find($arrayIpAdicional['intIdSpcScope']);
                                
                                $this->serviceGeneral->setEstadoServicioProductoCaracteristica($objSpcScopeTellionAdi, "Activo");
                                $objIpFijaServicio = $this->emInfraestructura
                                                          ->getRepository('schemaBundle:InfoIp')
                                                          ->findOneBy(array("servicioId" => $intIdSerIpAdicional,
                                                                            "tipoIp"     => "FIJA",
                                                                            "estado"     => "Reservada"));
                                $objIpFijaServicio->setEstado("Activo");
                                $this->emInfraestructura->persist($objIpFijaServicio);
                                $this->emInfraestructura->flush();
                                
                                //historial del servicio
                                $objServicioHistorial = new InfoServicioHistorial();
                                $objServicioHistorial->setServicioId($objServicio);
                                $objServicioHistorial->setObservacion("Se migro Activacion Ip adicional ".$strMarcaOlt.".");
                                $objServicioHistorial->setEstado("Activo");
                                $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                                $objServicioHistorial->setIpCreacion($strIpCreacion);
                                $this->emComercial->persist($objServicioHistorial);
                                $this->emComercial->flush();                                
                            }
                        }
                    }
                    if ($strTieneIpWan === 'SI')
                    {
                        //se elimina la MAC anterior de la ip
                        $objSpcMacTellionIpWan = $this->emComercial
                                                      ->getRepository('schemaBundle:InfoServicioProdCaract')
                                                      ->find($objSpcMacIpWan->getId());
                        $this->serviceGeneral->setEstadoServicioProductoCaracteristica($objSpcMacTellionIpWan, "Eliminado");
                        //se crea la nueva MAC relaciona a la ip del servicio
                        $this->serviceGeneral->ingresarServicioProductoCaracteristica($objServicioIpWan,
                                                                                      $objProducto,
                                                                                      "MAC",
                                                                                      $strMacOnt,
                                                                                      $strUsrCreacion);
                    }
                }

                //crear solicitud para retiro de equipo (ont y wifi)
                $objTipoSolicitud = $this->emComercial
                                         ->getRepository('schemaBundle:AdmiTipoSolicitud')
                                         ->findOneBy(array( "descripcionSolicitud" => "SOLICITUD RETIRO EQUIPO", "estado"=>"Activo"));
                $objDetalleSolicitud = new InfoDetalleSolicitud();
                $objDetalleSolicitud->setServicioId($objServicio);
                $objDetalleSolicitud->setTipoSolicitudId($objTipoSolicitud);
                $objDetalleSolicitud->setEstado("AsignadoTarea");
                $objDetalleSolicitud->setUsrCreacion($strUsrCreacion);
                $objDetalleSolicitud->setFeCreacion(new \DateTime('now'));
                $objDetalleSolicitud->setObservacion("SOLICITA RETIRO DE EQUIPO POR CANCELACION DEL SERVICIO");
                $this->emComercial->persist($objDetalleSolicitud);

                //crear las caract para la solicitud de retiro de equipo
                $objCaracteristicaElemento = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                               ->findOneByDescripcionCaracteristica("ELEMENTO CLIENTE");
                
                //valor del ont
                $objDetalleSolCaract= new InfoDetalleSolCaract();
                $objDetalleSolCaract->setCaracteristicaId($objCaracteristicaElemento);
                $objDetalleSolCaract->setDetalleSolicitudId($objDetalleSolicitud);
                $objDetalleSolCaract->setValor($objServicioTecnico->getElementoClienteId());
                $objDetalleSolCaract->setEstado("AsignadoTarea");
                $objDetalleSolCaract->setUsrCreacion($strUsrCreacion);
                $objDetalleSolCaract->setFeCreacion(new \DateTime('now'));
                $this->emComercial->persist($objDetalleSolCaract);
                $this->emComercial->flush();

                $objElementoOnt = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                          ->find($objServicioTecnico->getElementoClienteId());
                $objElementoOnt->setEstado("Eliminado");
                $this->emInfraestructura->persist($objElementoOnt);

                //obtener wifi
                $objInterfaceWifi = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                         ->find($objEnlaceCliente->getInterfaceElementoFinId()->getId());
                /* se valida si el cliente desea usar adicionalmente el equipo wifi que tenia asignado previo a la migración
                   para mejorar el alcance de la señal wifi dentro del espacio fisico donde se encuentra realizada la instalación,
                   solamente cuando se envie la cadena "SI" se clonara el enlace y se mantendra el equipo wifi adicional*/
                if ($strMantieneEquipoWifi == "SI")
                {
                    /* se clona el enlace del elemento wifi existente para luego de realizar la creacion del nuevo elemento HW 
                       realizar el enlazamiento respectivo entre el elemento CPE ONT/WIFI HW y EQUIPO WIFI asignado previo a la migración */
                    $objEnlaceWifi = clone $objEnlaceCliente;

                }
                else
                {
                    //valor del wifi
                    $objDetalleSolCaract= new InfoDetalleSolCaract();
                    $objDetalleSolCaract->setCaracteristicaId($objCaracteristicaElemento);
                    $objDetalleSolCaract->setDetalleSolicitudId($objDetalleSolicitud);
                    $objDetalleSolCaract->setValor($objInterfaceWifi->getElementoId()->getId());
                    $objDetalleSolCaract->setEstado("AsignadoTarea");
                    $objDetalleSolCaract->setUsrCreacion($strUsrCreacion);
                    $objDetalleSolCaract->setFeCreacion(new \DateTime('now'));
                    $this->emComercial->persist($objDetalleSolCaract);
                    $this->emComercial->flush();

                    $objElementoWifi = $objInterfaceWifi->getElementoId();
                    $objElementoWifi->setEstado("Eliminado");
                    $this->emInfraestructura->persist($objElementoWifi);
                }
                
                //obtener tarea
                $objProcesoRetiroEquipo = $this->emSoporte->getRepository('schemaBundle:AdmiProceso')
                                                          ->findOneByNombreProceso("SOLICITAR RETIRO EQUIPO");
                $objTareasRetiro        = $this->emSoporte->getRepository('schemaBundle:AdmiTarea')
                                                          ->findTareasActivasByProceso($objProcesoRetiroEquipo->getId());
                $objTareaRetiro         = $objTareasRetiro[0];

                //grabar nuevo info_detalle para la solicitud de retiro de equipo
                $objDetalle = new InfoDetalle();
                $objDetalle->setDetalleSolicitudId($objDetalleSolicitud->getId());
                $objDetalle->setTareaId($objTareaRetiro);
                $objDetalle->setLongitud($objServicio->getPuntoId()->getLongitud());
                $objDetalle->setLatitud($objServicio->getPuntoId()->getLatitud());
                $objDetalle->setPesoPresupuestado(0);
                $objDetalle->setValorPresupuestado(0);
                $objDetalle->setIpCreacion($strIpCreacion);
                $objDetalle->setFeCreacion(new \DateTime('now'));
                $objDetalle->setUsrCreacion($strUsrCreacion);
                $this->emSoporte->persist($objDetalle);
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
                $objDetalleAsignacion->setDetalleId($objDetalle);
                $objDetalleAsignacion->setAsignadoId($objDepartamento->getId());
                $objDetalleAsignacion->setAsignadoNombre($objDepartamento->getNombreDepartamento());
                $objDetalleAsignacion->setRefAsignadoId($objPersona->getId());
                if($objPersona->getRazonSocial() == "")
                {
                    $strNombre = $objPersona->getNombres() . " " . $objPersona->getApellidos();
                }
                else
                {
                    $strNombre = $objPersona->getRazonSocial();
                }
                $objDetalleAsignacion->setRefAsignadoNombre($strNombre);
                $objDetalleAsignacion->setPersonaEmpresaRolId($objPersonaEmpresaRolUsr->getId());
                $objDetalleAsignacion->setTipoAsignado("EMPLEADO");
                $objDetalleAsignacion->setUsrCreacion($strUsrCreacion);
                $objDetalleAsignacion->setFeCreacion(new \DateTime('now'));
                $objDetalleAsignacion->setIpCreacion($strIpCreacion);
                $this->emSoporte->persist($objDetalleAsignacion);
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
                
                //DAR DE BAJA EN EL REPOSITORIO DEL NAF EL NUEVO ONT
                if($strPrefijoEmpresa=="MD")
                {
                    $strEmpresaCod = "10";
                }
                else
                {
                    $strEmpresaCod = $intIdEmpresa;
                }
                //ingresar elemento ont
                $arrayOntNaf           = $this->serviceGeneral->buscarElementoEnNaf($strSerieOnt, $strModeloOnt, "PI", "ActivarServicio");
                $strStatusOntNaf       = $arrayOntNaf[0]['status'];
                $strMensajeOntNaf      = $arrayOntNaf[0]['mensaje'];
                $strCodigoArticuloOnt = "";
                if($strStatusOntNaf == "OK")
                {
                    $objInterfaceOnt = $this->serviceGeneral
                                             ->ingresarElementoCliente( $strLogin,
                                                                        $strSerieOnt,
                                                                        $strModeloOnt,
                                                                        "-ont", 
                                                                        $objInterfaceElementoSplitter,
                                                                        $strUltimaMilla,
                                                                        $objServicio,
                                                                        $strUsrCreacion, 
                                                                        $strIpCreacion,
                                                                        $intIdEmpresa );

                    //actualizamos registro en el naf ont
                    $strPvMensajeerror = str_repeat(' ', 1000);                                                                    
                    $strSql = "BEGIN AFK_PROCESOS.IN_P_PROCESA_INSTALACION(:codigoEmpresaNaf, ".
                              ":codigoArticulo, :tipoArticulo, :identificacionCliente, :serieCpe, ".
                              ":cantidad, :pv_mensajeerror); END;";
                    $objStmt = $this->emNaf->getConnection()->prepare($strSql);
                    $objStmt->bindParam('codigoEmpresaNaf', $strEmpresaCod);
                    $objStmt->bindParam('codigoArticulo',   $strCodigoArticuloOnt);
                    $objStmt->bindParam('tipoArticulo',     $strTipoArticulo);
                    $objStmt->bindParam('identificacionCliente', $strIdentificacionCliente);
                    $objStmt->bindParam('serieCpe',              $strSerieOnt);
                    $objStmt->bindParam('cantidad',              intval(1));
                    $objStmt->bindParam('pv_mensajeerror',       $strPvMensajeerror);
                    $objStmt->execute();

                    if(strlen(trim($strPvMensajeerror))>0)
                    {
                        $strMensaje = "ERROR ONT NAF: ".$strPvMensajeerror;
                        throw new \Exception($strMensaje);
                    }

                    //guardar ont en servicio tecnico
                    $objServicioTecnico->setElementoClienteId($objInterfaceOnt->getElementoId()->getId());
                    $objServicioTecnico->setInterfaceElementoClienteId($objInterfaceOnt->getId());
                    $this->emComercial->persist($objServicioTecnico);
                    $this->emComercial->flush();

                    if(is_object($objEnlaceWifi))
                    {
                        $objEnlaceWifi->setInterfaceElementoIniId($objInterfaceOnt);
                        $objEnlaceWifi->setFeCreacion(new \DateTime('now'));
                        $objEnlaceWifi->setIpCreacion($strIpCreacion);
                        $this->emInfraestructura->persist($objEnlaceWifi);
                        $this->emInfraestructura->flush();
                    }
                }
                else
                {
                    $strMensaje = "ERROR NAF: ".$strMensajeOntNaf;
                    throw new \Exception($strMensaje);
                }

                //servicio prod caract mac ont
                $this->serviceGeneral->ingresarServicioProductoCaracteristica($objServicio, $objProducto, "MAC ONT", $strMacOnt, $strUsrCreacion);
                
                //ACTUALIZAR ESTADO DEL SPLITTER
                $objInterfaceElementoSplitter->setEstado("connected");
                $this->emInfraestructura->persist($objInterfaceElementoSplitter);
                $this->emInfraestructura->flush();
                
                if($strProductoEdbEnPlan === "SI")
                {
                    $strEstadoSolicitud         = "PendienteExtender";
                    $strObservacionSolicitud    = "PROCESO DE MIGRACIÓN EJECUTADO, PARA FINALIZARLO SE DEBE AGREGAR EL EXTENDER";
                }
                else
                {
                    $strEstadoSolicitud         = "Finalizada";
                    $strObservacionSolicitud    = "SE FINALIZA LA SOLICITUD DE MIGRACION";
                }
                $objSolicitudMigracion->setEstado($strEstadoSolicitud);
                $this->emComercial->persist($objSolicitudMigracion);

                //crear historial para la solicitud
                $objHistorialSolicitudMigra = new InfoDetalleSolHist();
                $objHistorialSolicitudMigra->setDetalleSolicitudId($objSolicitudMigracion);
                $objHistorialSolicitudMigra->setEstado($strEstadoSolicitud);
                $objHistorialSolicitudMigra->setObservacion($strObservacionSolicitud);
                $objHistorialSolicitudMigra->setUsrCreacion($strUsrCreacion);
                $objHistorialSolicitudMigra->setFeCreacion(new \DateTime('now'));
                $objHistorialSolicitudMigra->setIpCreacion($strIpCreacion);
                $this->emComercial->persist($objHistorialSolicitudMigra);
                $this->emComercial->flush();
            }//status activacion huawei
            else
            {
                $strMensaje    = 'Mensaje Cancelar: '.$strMensajeRespuestaCancelar . ' Mensaje Activar: ' . $strMensajeRespuestaActivar;
                throw new \Exception($strMensaje);
            }
            $arrayDataConfirmacionTn['datos']['respuesta_confirmacion'] = "OK";
        }
        catch (\Exception $objEx)
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
            
            if ($this->emNaf->getConnection()->isTransactionActive())
            {
                $this->emNaf->getConnection()->rollback();
            }
            
            $this->serviceUtil->insertError('Telcos+',
                                            'InfoActivarPuertoService->migrarCliente',
                                            $objEx->getMessage(),
                                            $strUsrCreacion,
                                            $strIpCreacion);
            
            if (empty($strMensaje))
            {
                $strMensaje    = "Se presentaron problemas al ejecutar el proceso, favor notificar a sistemas!";
            }
            $this->serviceRdaMiddleware->ejecutaWsSinEsperarRespuestaMiddleware($arrayDataConfirmacionTn);
            $arrayRespuestaFinal = array('status' => "ERROR", 'mensaje' => $strMensaje);
            return $arrayRespuestaFinal;
        }
        
        if ($this->emInfraestructura->getConnection()->isTransactionActive()){
            $this->emInfraestructura->getConnection()->commit();
        }

        if ($this->emComercial->getConnection()->isTransactionActive()){
            $this->emComercial->getConnection()->commit();
        }
        
        if ($this->emNaf->getConnection()->isTransactionActive()){
            $this->emNaf->getConnection()->commit();
        }
        
        if ($this->emSoporte->getConnection()->isTransactionActive()){
            $this->emSoporte->getConnection()->commit();
        }
        
        $strMsjMigracionOk    = "Migración realizada exitosamente";
        $objResultadoJsonLdap = $this->serviceGeneral->ejecutarComandoLdap("N", $intIdServicio, $strPrefijoEmpresa);
        if($objResultadoJsonLdap->status!="OK")
        {
            $objResultadoJsonLdap = $this->serviceGeneral->ejecutarComandoLdap("A", $intIdServicio, $strPrefijoEmpresa);
            if($objResultadoJsonLdap->status!="OK")
            {
                $strMsjMigracionOk .= "<br>" . $objResultadoJsonLdap->mensaje;
            }
        }        
        
        if($strProductoEdbEnPlan === "SI")
        {
            $strMsjMigracionOk .= "<br>Para culminar el proceso de migración debe agregar el Extender Dual Band ";
        }
        else
        {
            //finalizar tareas generadas en solicitudes
            $arrayParametros['intIdDetalleSolicitud'] = $objSolicitudMigracion->getId();
            $arrayParametros['strProceso']            = 'Migrar';
            $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleSolicitud')->cerrarTareasPorSolicitud($arrayParametros);
        }
        
        //EJECUTAR VALIDACIÓN DE PROMOCIONES BW
        $arrayParametrosInfoBw = array();
        $arrayParametrosInfoBw['intIdServicio']     = $objServicio->getId();
        $arrayParametrosInfoBw['intIdEmpresa']      = $intIdEmpresa;
        $arrayParametrosInfoBw['strTipoProceso']    = "MIGRACION_EQUIPO";
        $arrayParametrosInfoBw['strValor']          = $objServicio->getId();
        $arrayParametrosInfoBw['strUsrCreacion']    = $strUsrCreacion;
        $arrayParametrosInfoBw['strIpCreacion']     = $strIpCreacion;
        $arrayParametrosInfoBw['strPrefijoEmpresa'] = $strPrefijoEmpresa;
        $this->servicePromociones->configurarPromocionesBW($arrayParametrosInfoBw);
        
        $this->emInfraestructura->getConnection()->close();
        $this->emComercial->getConnection()->close();
        $this->emNaf->getConnection()->close();
        $this->emSoporte->getConnection()->close();
        $this->serviceRdaMiddleware->ejecutaWsSinEsperarRespuestaMiddleware($arrayDataConfirmacionTn);
        $arrayRespuestaFinal = array('status' => "OK", 'mensaje' => $strMsjMigracionOk);
        
        return $arrayRespuestaFinal;
    }   


    /**
     * Funcion que sirve para migrar la ip fija adicional de tellion a huawei
     * 
     * @author Creado: Francisco Adum <fadum@telconet.ec>
     * @author Creado: Jesus Bozada <jbozada@telconet.ec>
     * @author Creado: Jesus Bozada <jbozada@telconet.ec>
     * @author Creado: Jesus Bozada <jbozada@telconet.ec>
     * @author Creado: Jesus Bozada <jbozada@telconet.ec>
     * @author Modificado: Jesus Bozada <jbozada@telconet.ec>
     * @author Modificado: Jesus Bozada <jbozada@telconet.ec>
     * @author Modificado: Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 15-03-2015
     * @version 1.1 9-04-2015
     * @version 1.2 17-04-2015
     * @version 1.3 14-08-2015
     * @version 1.4 10-12-2015 Se agregan validaciones de tipo de aprovisionamiento de Ips Tellion
     * @version 1.5 10-12-2015 Se agregan validacion del tipo de aprovisionamiento del Olt tellion al cual pertenecia el cliente
     * @version 1.6 25-04-2016 Se corrige forma de obtener el tipo de aprovisionamiento de ips del elemento tellion del cliente previo migración
     * @version 1.7 28-04-2016 Se agregan validaciones dentro de proceso para soportar migraciones de ips de clientes tellion CNR
     * @version 1.8 09-05-2016 Se agrega parametro empresa en metodo migrarIpCliente por conflictos de producto INTERNET DEDICADO
     * 
     * @since   1.0 15-03-2015
     */
    public function migrarIpCliente($arrayPeticiones)
    {
        $ipFija         = $arrayPeticiones['ipFija'];
        $mac            = $arrayPeticiones['mac'];
        $idServicio     = $arrayPeticiones['idServicio'];
        $idEmpresa      = $arrayPeticiones['idEmpresa'];
        $usrCreacion    = $arrayPeticiones['usrCreacion'];
        $ipCreacion     = $arrayPeticiones['ipCreacion'];
        $strIpReservada = $arrayPeticiones['ipReservada'];
        
        $servicio         = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($idServicio);        
        $servicioTecnico  = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                              ->findOneBy(array( "servicioId" => $servicio->getId()));
        $plan             = $servicio->getPlanId();
        $arrayProductoIp  = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                 ->findBy(array("nombreTecnico"=>"IP", "estado"=>"Activo"));
        //obtener producto ip
        if($plan)
        {
            for($i=0;$i<count($arrayProductoIp);$i++)
            {
                $planDet = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                ->findOneBy(array("planId"     => $plan->getId(),
                                               "productoId" => $arrayProductoIp[$i]));
                if($planDet)
                {
                    $productoIpId = $planDet->getProductoId();
                    $productoIp = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($productoIpId);
                    break;
                }
            }            
        }
        else
        {
            $productoIp         = $servicio->getProductoId();
        }
        
        $productoInternet       = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                            ->findOneBy(array("esPreferencia"   =>"SI", 
                                                              "nombreTecnico"   => "INTERNET", 
                                                              "empresaCod"      =>$idEmpresa,
                                                              "estado"          =>"Activo"));       
        //obtener servicio internet
        $punto                  = $servicio->getPuntoId();
        $servicioInternet       = $this->getServicioInternetEnPunto($punto, $productoInternet);
        $spcInterfaceTellion    = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "INTERFACE ELEMENTO TELLION", $productoIp);
        $spcMigrado             = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "MIGRADO", $productoIp);
        $spcPerfil              = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "PERFIL", $productoInternet);
        $spcMacAnterior         = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "MAC", $productoInternet);
        $spcMacOntInt           = $this->servicioGeneral->getServicioProductoCaracteristica($servicioInternet, "MAC ONT", $productoInternet);
        if(!$spcPerfil)
        {
            $spcPerfil              = $this->servicioGeneral->getServicioProductoCaracteristica($servicioInternet, "PERFIL", $productoInternet);
            if(!$spcPerfil)
            {
                $respuestaFinal[] = array('status' =>'ERROR', 
                                          'mensaje'=>'No existe Caracteristica PERFIL para el servicio adicional, favor revisar!');
                return $respuestaFinal;
            }
        }
        $idIntefarceTellion     = $spcInterfaceTellion->getValor();
        $interfaceTellion       = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')->find($idIntefarceTellion);
        $modeloElementoTellion  = $interfaceTellion->getElementoId()->getModeloElementoId();
        
        //obtener objeto modelo cnr
        $modeloElementoCnr = $this->emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                  ->findOneBy(array("nombreModeloElemento"  => "CNR UCS C220",
                                                    "estado"                => "Activo"));

        //obtener elemento cnr
        $elementoCnr = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                  ->findOneBy(array("modeloElementoId"=>$modeloElementoCnr->getId()));
        
        //se agrega codigo de validacion de tipo de aprovisionamiento
        $servicioTecnicoInternet  = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                              ->findOneBy(array( "servicioId" => $servicioInternet->getId()));    
        
        $strTipoAprovisionamiento = $this->recursoRedService->geTipoAprovisionamiento($interfaceTellion->getElementoId()->getId());
        
        //*DECLARACION DE TRANSACCIONES------------------------------------------*/
        $this->emComercial->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();
        //*----------------------------------------------------------------------*/
        
        try
        {
            if ($strTipoAprovisionamiento == "POOL")
            {
                //cancelar ip adicional
                $arrayParametrosDesconfigIp = array (
                                                        'servicio'          => $servicio,
                                                        'producto'          => $productoInternet,
                                                        'servicioTecnico'   => $servicioTecnico,
                                                        'interfaceElemento' => $interfaceTellion,
                                                        'modeloElemento'    => $modeloElementoTellion,
                                                        'macWifi'           => $mac,
                                                        'perfil'            => $spcPerfil->getValor(),
                                                        'usrCreacion'       => $usrCreacion,
                                                        'idEmpresa'         => $idEmpresa
                                                    );
                $arrayResultadoDesconfigIp = $this->cancelarService->cancelarIpsFijas($arrayParametrosDesconfigIp);
                $status = $arrayResultadoDesconfigIp[0]['status'];
            }
            else
            {
                //se agrega cancelación de ip de un cliente tellion cnr pro 
                if ($punto->getTipoNegocioId()->getNombreTipoNegocio()=="PRO")
                {
                    $parametrosCancelaIp = array('servicio' => $servicio,
                                                 'producto' => $productoInternet,
                                                 'macOnt'   => $spcMacOntInt->getValor());

                    $resultCancelaIp = $this->cancelarService->cancelarIpTellionCnr($parametrosCancelaIp);

                    $statusIpCancel = $resultCancelaIp[0]['status'];
                    if($statusIpCancel!="OK")
                    {
                        $status     = "ERROR";
                        $mensajeIp  .= "Error al cancelar Ip".$resultCancelaIp[0]['mensaje'];
                        $mensaje    = "No se pudo Cancelar Ip en CNR <br>".$mensajeIp;
                        throw new \Exception($mensaje);
                    }
                    $status = "OK";
                }
                else
                {
                    $status = "OK";
                }
                
            }
            
            if($status=="OK")
            {
                if ($strTipoAprovisionamiento == "POOL")
                {
                    //obtener ips fijas q tiene el servicio (en este caso siempre sera 1)
                    $ipFijaAnterior = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                            ->findOneBy(array("servicioId"=>$servicio->getId(),"tipoIp"=>"FIJA", "estado"=>"Activo"));
                    $ipFijaAnterior->setEstado("Cancel");
                    $this->emInfraestructura->persist($ipFijaAnterior);
                }    
                //datos del servicio de internet
                $servicioTecnicoInternet    = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                          ->findOneBy(array( "servicioId" => $servicioInternet->getId()));
                $interfaceElemento          = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                        ->find($servicioTecnicoInternet->getInterfaceElementoId());
                $modeloElementoHuawei       = $interfaceElemento->getElementoId()->getModeloElementoId();
                
                if($punto->getTipoNegocioId()->getNombreTipoNegocio()=="PRO")
                {
                    $spcSpid                    = $this->servicioGeneral
                                                       ->getServicioProductoCaracteristica($servicioInternet, "SPID", $productoInternet);
                    $macOnt                     = $this->servicioGeneral
                                                       ->getServicioProductoCaracteristica($servicioInternet, "MAC ONT", $productoInternet);
                    $mac                        = $macOnt->getValor();
                    
                    //eliminar service port id
                    //*ELIMINAR SCRIPT SPID --------------------------------------------------------*/
                    $scriptArraySpid   = $this->servicioGeneral->obtenerArregloScript("eliminarSpid",$modeloElementoHuawei);
                    $idDocumentoSpid   = $scriptArraySpid[0]->idDocumento;
                    $usuario           = $scriptArraySpid[0]->usuario;
                    $protocolo         = $scriptArraySpid[0]->protocolo;
                    //*----------------------------------------------------------------------*/
                    
                    $arrayParametrosEliminarServicePort = array (
                                                                    'idDocumento'   => $idDocumentoSpid,
                                                                    'usuario'       => $usuario,
                                                                    'spid'          => $spcSpid->getValor(),
                                                                    'elementoId'    => $interfaceElemento->getElementoId()->getId(),
                                                                    'protocolo'     => $protocolo
                                                                );
                    $resultadoJsonEliminarSpid = $this->activarService->eliminarSpidHuawei($arrayParametrosEliminarServicePort);
                    $statusEliminarSpid = $resultadoJsonEliminarSpid->status;
                    
                    if($statusEliminarSpid=="OK")
                    {
                        $gemPort       = $this->servicioGeneral
                                              ->getServicioProductoCaracteristica($servicioInternet, "GEM-PORT", $productoInternet);
                        $ontId         = $this->servicioGeneral
                                              ->getServicioProductoCaracteristica($servicioInternet, "INDICE CLIENTE", $productoInternet);
                        $trafficTable  = $this->servicioGeneral
                                              ->getServicioProductoCaracteristica($servicioInternet, "TRAFFIC-TABLE", $productoInternet);
                        //dividir interface para obtener tarjeta y puerto pon
                        list($tarjeta, $puertoPon) = split('/',$interfaceElemento->getNombreInterfaceElemento());
                        
                        //*OBTENER SCRIPT--------------------------------------------------------*/
                        $scriptArray = $this->servicioGeneral->obtenerArregloScript("activarCliente",$modeloElementoHuawei);
                        $idDocumento= $scriptArray[0]->idDocumento;
                        $usuario= $scriptArray[0]->usuario;
                        $protocolo= $scriptArray[0]->protocolo;
                        //*----------------------------------------------------------------------*/
                        
                        //activar service port id con vlan=302, unicamente para ips adicionales
                        $arrayParametrosConfigSpid = array  (
                                                                'vlan'              => '302',
                                                                'gemPort'           => $gemPort->getValor(),
                                                                'trafficTable'      => $trafficTable->getValor(),
                                                                'idDocumento'       => $idDocumento,
                                                                'usuario'           => $usuario,
                                                                'servicioTecnico'   => $servicioTecnicoInternet,
                                                                'protocolo'         => $protocolo,
                                                                'tarjeta'           => $tarjeta,
                                                                'puertoPon'         => $puertoPon,
                                                                'ontId'             => $ontId->getValor()
                                                            );
                        $resultadoJsonActivarSpid = $this->activarService->activarClienteOltHuawei($arrayParametrosConfigSpid);
                        $statusActivarSpid = $resultadoJsonActivarSpid->status;
                        
                        if($statusActivarSpid!="OK")
                        {
                            throw new \Exception("No crear el Service-Port! <br>,".$resultadoJsonActivarSpid->mensaje);
                        }
                        
                        //eliminar serv prod caract de spid anterior
                        $this->servicioGeneral->setEstadoServicioProductoCaracteristica($spcSpid, "Eliminado");
                        
                        //*OBTENER SCRIPT SPID --------------------------------------------------------*/
                        $scriptArraySpid   = $this->servicioGeneral->obtenerArregloScript("obtenerSpid",$modeloElementoHuawei);
                        $idDocumentoSpid   = $scriptArraySpid[0]->idDocumento;
                        //*----------------------------------------------------------------------*/
                        
                        //variables datos
                        $datos = $tarjeta.",".$puertoPon.",".$ontId->getValor();

                        $resultadoJsonSpid = $this->activarService
                                                  ->obtenerDatosPorAccion($servicioTecnico, $usuario, $datos, $idDocumentoSpid, "obtenerSpid");
                        $statusSpid = $resultadoJsonSpid->status;
                        if($statusSpid!="OK")
                        {
                            throw new \Exception("No se pudo obtener el Service-Port! <br>,".$resultadoJsonSpid->mensaje);
                        }
                        $spid = $resultadoJsonSpid->mensaje;
                        
                        if($spid!="")
                        {
                            //servicio prod caract spid
                            $this->servicioGeneral
                                 ->ingresarServicioProductoCaracteristica($servicioInternet, $productoInternet, "SPID", $spid, $usrCreacion);
                        }
                        //elimino e ingreso la nueva vlan con 302
                        $spcVlan = $this->servicioGeneral->getServicioProductoCaracteristica($servicioInternet, "VLAN", $productoInternet);
                        $this->servicioGeneral->setEstadoServicioProductoCaracteristica($spcVlan, "Eliminado");
                        $this->servicioGeneral->ingresarServicioProductoCaracteristica( $servicioInternet, $productoInternet, "VLAN", '302', 
                                                                                        $usrCreacion);    
                    }
                    else
                    {
                        throw new \Exception("No se pudo eliminar el Service-Port! <br>,".$resultadoJsonEliminarSpid->mensaje);
                    }
                }
                else if($punto->getTipoNegocioId()->getNombreTipoNegocio()=="PYME")
                {
                    //se debe seleccionar el puerto ont (1-4)
                    $interfaceCliente = $this->getInterfaceClienteServicioIp($punto, $servicioTecnicoInternet, $arrayProductoIp);
                    
                    if(!$interfaceCliente)
                    {
                        $mensaje = "No Se puede migrar Ip, puesto que ya se encuentran ocupados los 4 Puertos del ONT";
                        throw new \Exception("No se pudo migrar la ip fija adicional! <br>".$mensaje);
                    }
                    
                    if($interfaceCliente)
                    {
                        //*CONFIGURAR IP FIJA --------------------------------------------------------*/
                        $scriptArrayIpFija = $this->servicioGeneral->obtenerArregloScript("configurarIpFija",$modeloElementoHuawei);
                        $idDocumentoIpFja  = $scriptArrayIpFija[0]->idDocumento;
                        $usuario           = $scriptArrayIpFija[0]->usuario;
                        $protocolo         = $scriptArrayIpFija[0]->protocolo;
                        //*----------------------------------------------------------------------*/

                        //dividir interface para obtener tarjeta y puerto pon
                        list($tarjeta, $puertoPon) = split('/',$interfaceElemento->getNombreInterfaceElemento());

                        //ont id
                        $spcIndice = $this->servicioGeneral
                                          ->getServicioProductoCaracteristica($servicioInternet, "INDICE CLIENTE", $productoInternet);

                        $arrayParametrosIpFija = array  (
                                                        'elementoId'    => $interfaceElemento->getElementoId()->getId(),
                                                        'idDocumento'   => $idDocumentoIpFja,
                                                        'usuario'       => $usuario,
                                                        'tarjeta'       => $tarjeta,
                                                        'puertoPon'     => $puertoPon,
                                                        'ontId'         => $spcIndice->getValor(),
                                                        'puertoOnt'     => $interfaceCliente->getNombreInterfaceElemento()
                                                    );
                        $resultadoJsonIpFija = $this->activarService->activarIpFijaHuawei($arrayParametrosIpFija);
                        
                        if($resultadoJsonIpFija->status!="OK")
                        {
                            $mensaje = "Activacion Puerto Ont: ".$resultadoJsonIpFija->mensaje;
                            throw new \Exception("No se pudo migrar la ip fija adicional! <br>".$mensaje);
                        }
                    }
                }
                if ($strTipoAprovisionamiento == "POOL" || 
                    ($punto->getTipoNegocioId()->getNombreTipoNegocio()=="PRO" && $strTipoAprovisionamiento == "CNR"))
                {
                    $arrayParametrosConfigIp = array (
                                                        'ipFija'            => $ipFija,
                                                        'modeloElementoCnr' => $modeloElementoCnr,
                                                        'elementoCnr'       => $elementoCnr,
                                                        'mac'               => $mac
                                                     );

                    //activar ip adicional            
                    $arrayResultadoConfigIp = $this->activarService->activarIpFijaCnr($arrayParametrosConfigIp);
                    $status                 = $arrayResultadoConfigIp[0]['status'];
                }
                else
                {
                   $status = "OK";
                }
                
                
                if($status=="OK")
                {
                    $this->servicioGeneral->setValorServicioProductoCaracteristica($spcMigrado, "SI");
                    if ($strTipoAprovisionamiento == "POOL")
                    {
                        if ($strIpReservada == null)
                        {
                        //grabar ip nueva
                            $ipFijaNueva = new InfoIp();
                            $ipFijaNueva->setServicioId($servicio->getId());
                            $ipFijaNueva->setIp($ipFija);
                            $ipFijaNueva->setEstado("Activo");
                            $ipFijaNueva->setUsrCreacion($usrCreacion);
                            $ipFijaNueva->setTipoIp("FIJA");
                            $ipFijaNueva->setVersionIp("IPV4");
                            $ipFijaNueva->setFeCreacion(new \DateTime('now'));
                            $ipFijaNueva->setIpCreacion($ipCreacion);
                            $this->emInfraestructura->persist($ipFijaNueva);
                            $this->emInfraestructura->flush();
                        }
                        else
                        {

                            $entityIpReservada = $this->emInfraestructura
                                                      ->getRepository('schemaBundle:InfoIp')
                                                      ->findOneBy(array( "ip"         => $ipFija, 
                                                                         "estado"     => 'Reservada',
                                                                         "servicioId" => $servicio->getId()
                                                                       )
                                                                 );
                            if ($entityIpReservada)
                            {
                                $entityIpReservada->setEstado("Activo");
                                $this->emInfraestructura->persist($entityIpReservada);
                                $this->emInfraestructura->flush();
                            }
                        }
                    }
                    //eliminar mac anterior
                    if($spcMacAnterior)
                    {
                        $this->servicioGeneral->setEstadoServicioProductoCaracteristica($spcMacAnterior, "Eliminado");
                    }                    
                    
                    //ingresar mac nueva
                    $this->servicioGeneral
                                 ->ingresarServicioProductoCaracteristica($servicio, $productoInternet, "MAC", $mac, $usrCreacion);
                    
                    //historial del servicio
                    $servicioHistorial = new InfoServicioHistorial();
                    $servicioHistorial->setServicioId($servicio);
                    if ($ipFijaAnterior)
                    {
                        $servicioHistorial->setObservacion("Se migro Ip Fija:".$ipFija." con Mac:".$mac."<br>"
                                                         . "Se elimino Ip Fija:".$ipFijaAnterior->getIp()."con Mac:".$mac);
                    }
                    else
                    {
                        $servicioHistorial->setObservacion("Se migro Ip Fija:".$ipFija." con Mac:".$mac);
                    }                    
                    $servicioHistorial->setEstado($servicio->getEstado());
                    $servicioHistorial->setUsrCreacion($usrCreacion);
                    $servicioHistorial->setFeCreacion(new \DateTime('now'));
                    $servicioHistorial->setIpCreacion($ipCreacion);
                    $this->emComercial->persist($servicioHistorial);
                    $this->emComercial->flush();
                    
                    $mensaje = "OK";
                }
                else
                {
                    $mensaje    = $arrayResultadoConfigIp[0]['mensaje'];
                    throw new \Exception("No se pudo activar la ip fija adicional! <br>".$mensaje);
                }                
            }
            else
            {
                throw new \Exception("No se pudo desconfigurar la ip fija adicional del Olt Tellion!");
            }
        }
        catch (\Exception $e) {
            if ($this->emInfraestructura->getConnection()->isTransactionActive()){
                $this->emInfraestructura->getConnection()->rollback();
            }
            
            if ($this->emComercial->getConnection()->isTransactionActive()){
                $this->emComercial->getConnection()->rollback();
            }
            
            $status="ERROR";
            $mensaje = "ERROR: <br> ".$e->getMessage();
            $respuestaFinal[] = array('status'=>$status, 'mensaje'=>$mensaje);
            return $respuestaFinal;
        }
        
        //*DECLARACION DE COMMITS*/
        if ($this->emInfraestructura->getConnection()->isTransactionActive()){
            $this->emInfraestructura->getConnection()->commit();
        }

        if ($this->emComercial->getConnection()->isTransactionActive()){
            $this->emComercial->getConnection()->commit();
        }
        
        $this->emInfraestructura->getConnection()->close();
        $this->emComercial->getConnection()->close();
        //*----------------------------------------------------------------------*/

        $respuestaFinal[] = array('status'=>$status, 'mensaje'=>$mensaje);
        return $respuestaFinal;
    }
    
    /**
     * Funcion que sirve para obtener la Interface del ONT para 
     * habilitacion del puerto desde el OLT huawei
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @author Jesus Bozada   <jbozada@telconet.ec>
     * @version 1.0  9-04-2015
     * @version 1.0 10-06-2015
     * @param InfoPunto           $punto
     * @param InfoServicioTecnico $servicioTecnicoInternet
     * @param Array               $arrayProductoIp
     * @param String              $esCancelacion
     */
    public function getInterfaceClienteServicioIp($punto, $servicioTecnicoInternet, $arrayProductoIp, $esCancelacion)

    {
        $serviciosPunto  = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                 ->findBy(array("puntoId"=>$punto->getId(), "estado"=>"Activo"));
        
        $cont=0;
        for($j=0;$j<count($serviciosPunto);$j++)
        {
            $servicio = $serviciosPunto[$j];
            $plan     = $servicio->getPlanId();
            if ($servicio->getId() != $servicioTecnicoInternet->getServicioId()->getId()) 
            {
                if($plan)
                {
                    for($i=0;$i<count($arrayProductoIp);$i++)
                    {
                        $planDet = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                        ->findOneBy(array(  "planId"     => $plan->getId(),
                                                            "productoId" => $arrayProductoIp[$i]));
                        if($planDet)
                        {
                            $spcMigrado = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "MIGRADO", $arrayProductoIp[$i]);

                            if($spcMigrado)
                            {
                                if($spcMigrado->getValor()=="SI")
                                {
                                    $cont++;
                                }
                            } 
                            else
                            {
                                $cont++;
                            }
                            //if($spcMigrado)  
                        }//if($planDet)
                    }//for($i=0;$i<count($arrayProductoIp);$i++)
                }//if($plan)
                else
                {
                    for($i=0;$i<count($arrayProductoIp);$i++)
                    {
                        $producto = $servicio->getProductoId();

                        if($arrayProductoIp[$i]->getId() == $producto->getId())
                        {
                            $spcMigrado = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "MIGRADO", $producto);

                            if($spcMigrado)
                            {
                                if($spcMigrado->getValor()=="SI")
                                {
                                    $cont++;
                                }
                            }
                            else
                            {
                                $cont++;
                            }//if($spcMigrado)  
                        }
                    }//for($i=0;$i<count($arrayProductoIp);$i++)
                }
            }
            
        }//for($j=0;$j<count($serviciosPunto);$j++)
        
        if ($esCancelacion == "SI")
        {
            $interfaceCliente = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                     ->findOneBy(array( "elementoId"                => $servicioTecnicoInternet->getElementoClienteId(),
                                                        "nombreInterfaceElemento"   => $cont));  
        }
        else
        {
            if($cont<4)
            {
                $interfaceCliente = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                         ->findOneBy(array( "elementoId"                => $servicioTecnicoInternet->getElementoClienteId(),
                                                            "nombreInterfaceElemento"   => $cont+1));
            }
            else if($cont>=4)
            {
                $interfaceCliente = null;
            }
        }
        
        return $interfaceCliente;
    }
    
    /**
     * Funcion que sirve para obtener el servicio internet
     * de un punto
     * 
     * @author Creado: Francisco Adum <fadum@telconet.ec>
     * @version 1.0 15-03-2015
     */
    public function getServicioInternetEnPunto($punto, $productoInternet)
    {
        $serviciosPunto  = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                 ->findBy(array("puntoId"=>$punto->getId(), "estado"=>"Activo"));
        $indice = -1;
        
        for($i=0;$i<count($serviciosPunto);$i++)
        {
            $servicio = $serviciosPunto[$i];
            $plan     = $servicio->getPlanId();
            
            if($plan)
            {
                $planDet = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                    ->findOneBy(array("planId"     => $plan->getId(),
                                                      "productoId" => $productoInternet->getId()));
                if($planDet)
                {
                    $indice = $i;
                    break;
                }
            }
        }
        
        if($indice!=-1)
        {
            return $serviciosPunto[$indice];
        }
        else
        {
            return null;
        }
    }

    /**
     * Permite crear las solicitudes de cambio de plan mediante un string en el cual se concatena
     * 
     * 
     * @author Creado: John Vera <fadum@telconet.ec>
     * @version 1.0 15-03-2015
     * 
     * @author Modificado: Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 11-03-2016   Se agrega validacion de tecnologia de olt, para poder realizar migracion logica TELLION y porder mantener la 
     *                           migracion HUAWEI definida desde un inicio
     */
    
    public function createSolicitudesCambioPlan($serviciosPlanes, $usuarioCrea, $tecnologia)
    {

        //Ejecuto procedimiento que crea las solicitudes
        $mensajeError = str_repeat(' ', 1000);
        if ($tecnologia == 'HUAWEI')
        {
            $sql = "BEGIN INFRK_TRANSACCIONES.INFRP_SOLICITUDES_MIGRACION_IP(:serviciosPlanes, :usuarioCrea, :mensajeError); END;";
        }
        else
        {
            $sql = "BEGIN INFRK_TRANSACCIONES.INFRP_MIGRACION_LOGICA_TELLION(:serviciosPlanes, :usuarioCrea, :mensajeError); END;";
        }
        $stmt = $this->emInfraestructura->getConnection()->prepare($sql);
        $stmt->bindParam('serviciosPlanes', $serviciosPlanes);
        $stmt->bindParam('usuarioCrea', $usuarioCrea);
        $stmt->bindParam('mensajeError', $mensajeError);
        $stmt->execute();

        return $mensajeError;
    }
    
}

