<?php
namespace telconet\tecnicoBundle\Service;

use Doctrine\ORM\EntityManager;
use Exception;
use telconet\schemaBundle\Entity\InfoElemento;
use telconet\schemaBundle\Entity\InfoIpElemento;
use telconet\schemaBundle\Entity\InfoInterfaceElemento;
use telconet\schemaBundle\Entity\InfoServicioProdCaract;
use telconet\schemaBundle\Entity\InfoEnlace;
use telconet\schemaBundle\Entity\InfoEmpresaElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElementoUbica;
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Entity\InfoUbicacion; 
use telconet\schemaBundle\Entity\InfoDetalleInterface;
use telconet\schemaBundle\Entity\InfoServicio;
use telconet\schemaBundle\Entity\InfoIp;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoPuntoDatoAdicional;
use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\InfoDocumentoComunicacion;
use telconet\schemaBundle\Entity\InfoComunicacion;
use telconet\schemaBundle\Entity\InfoTareaSeguimiento;
use telconet\schemaBundle\Entity\AdmiMotivo;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolHisto;
use telconet\schemaBundle\Entity\InfoDetalle;
use telconet\schemaBundle\Entity\InfoDetalleHistorial;
use telconet\schemaBundle\Entity\InfoParteAfectada;
use telconet\schemaBundle\Entity\InfoCriterioAfectado;
use telconet\schemaBundle\Entity\InfoDetalleAsignacion;
use \telconet\schemaBundle\Entity\InfoServicioPlanCaract;
use telconet\schemaBundle\Entity\InfoOrdenTrabajo;
use telconet\schemaBundle\Entity\InfoDetalleSolCaract;
use telconet\planificacionBundle\Service\RecursosDeRedService;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class InfoCambiarPlanService{
    private $emComercial;
    private $emInfraestructura;
    private $emSoporte;
    private $emComunicacion;
    private $emSeguridad;
    private $emGeneral;
    private $servicioGeneral;
    private $activarService;
    private $serviceWifi;
    private $cancelarService;
    private $serviceSoporte;
    private $recursosRed;
    private $host;
    private $pathTelcos;
    private $pathParameters;
    private $rdaMiddleware;
    private $opcion                 = "CAMBIAR_PLAN";
    private $ejecutaComando;
    private $strConfirmacionTNMiddleware;
    private $utilService;
    private $serviceLicenciasKaspersky;
    private $serviceInternetProtegido;
    private $servicePromociones;
    private $serviceProceso;
    private $serviceFoxPremium;
    private $serviceTokenCas;
    private $serviceRestClient;
    private $strUrlValidacionCambioPlanUpMs;

    /**
     * @author Jose Cruz <jfcruzc@telconet.ec>
     * @version 2.2 15/12/2022 - llamado al servicio TOKENCAS
     */
    public function setDependencies(Container $objContainer)
    {
        $this->objTokenCasService    = $objContainer->get('seguridad.TokenCas');
        $this->servicioGeneral       = $objContainer->get('tecnico.InfoServicioTecnico');
        $this->activarService        = $objContainer->get('tecnico.InfoActivarPuerto');
        $this->cancelarService       = $objContainer->get('tecnico.InfoCancelarServicio');
        $this->reconectarService     = $objContainer->get('tecnico.InfoReconectarServicio');
        $this->networkingScripts     = $objContainer->get('tecnico.NetworkingScripts');
        $this->recursosRed           = $objContainer->get('planificacion.RecursosDeRed');
        $this->serviceWifi           = $objContainer->get('tecnico.InfoElementoWifi');
        $this->rdaMiddleware         = $objContainer->get('tecnico.RedAccesoMiddleware');
        $this->serviceEnvioPlantilla = $objContainer->get('soporte.EnvioPlantilla');
        $this->serviceSoporte        = $objContainer->get('soporte.SoporteService');
        $this->emSoporte             = $objContainer->get('doctrine')->getManager('telconet_soporte');
        $this->emInfraestructura     = $objContainer->get('doctrine')->getManager('telconet_infraestructura');
        $this->emSeguridad           = $objContainer->get('doctrine')->getManager('telconet_seguridad');
        $this->emComercial           = $objContainer->get('doctrine')->getManager('telconet');
        $this->emComunicacion        = $objContainer->get('doctrine')->getManager('telconet_comunicacion');
        $this->emNaf                 = $objContainer->get('doctrine')->getManager('telconet_naf');
        $this->emGeneral             = $objContainer->get('doctrine')->getManager('telconet_general');
        $this->host                  = $objContainer->getParameter('host');
        $this->pathTelcos            = $objContainer->getParameter('path_telcos');
        $this->pathParameters        = $objContainer->getParameter('path_parameters');              
        $this->ejecutaComando        = $objContainer->getParameter('ws_rda_ejecuta_scripts');
        $this->utilService           = $objContainer->get('schema.Util');
        $this->serviceLicenciasKaspersky = $objContainer->get('tecnico.LicenciasKaspersky');
        $this->serviceInternetProtegido  = $objContainer->get('tecnico.InternetProtegido');
        $this->servicePromociones        = $objContainer->get('tecnico.Promociones');
        $this->serviceProceso            = $objContainer->get('soporte.ProcesoService');
        $this->serviceFoxPremium         = $objContainer->get('tecnico.FoxPremium');
        $this->strConfirmacionTNMiddleware = $objContainer->getParameter('ws_rda_opcion_confirmacion_middleware');
        $this->serviceTokenCas              = $objContainer->get('seguridad.TokenCas');
        $this->serviceRestClient            = $objContainer->get('schema.RestClient');
        $this->strUrlValidacionCambioPlanUpMs = $objContainer->getParameter('ws_ms_validacionesCambioPlanUp_url');        
    }
        
    /**
     * Funcion que realiza el cambio de plan 
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 23-12-2015  Se agregan parametros utilizados para cambio de plan Tellion Cnr
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.2 23-02-2017  Se agregan parametros utilizados para la generación de solicitudes de 
     *                          agregar equipos en planes que incluyes productos SmartWifi
     * @since 1.1
     * 
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.3 01-09-2017  Se agrega el partametro de departamento en session
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.4 20-12-2017  Se agrega registro de log de respuesta de proceso de cambio de plan
     * @since 1.3
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.5 05-03-2018  Se agrega validación para flujo de servicios Internet Small Business
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.6 11-06-2018  Se agrega el envío del parámetro floatPrecioNuevoIp a la función cambioVelocidadIsb
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.7 03-08-2018  Se recuperan los valores de las capacidades del plan actual y se modifica la validación de actualización de
     *                          ldap por ZTE
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.8 28-11-2018  Se agregan validaciones para gestionar los productos de la empresa TNP
     * @since 1.7
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.9 29-11-2018 Se valida el cambio de planes de diciembre antes de ejecutar el proceso respectivo 
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.10 07-05-2019  Se modifica el orden de ejecución de procesos en cambio de planes MD
     * @since 1.9
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.11 05-06-2019 - Se agregan validaciones para restringir cambio de planes a clientes que tienen
     *                            contratado productos MCAFEE como servicios adicionales
     * @since 1.10
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.12 12-08-2019 - Se agregan validaciones para procesar la migración de licencias McAfee a Kaspersky
     * @since 1.11
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.13 07-09-2019 Se traspasa programación de migración de tecnología de licencias de Internet Protegido al respectivo service usado
     *                           desde procesos individuales y masivos
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.14 12-09-2019 - Se agrega proceso para validar promociones en servicios de clientes, en caso de 
     *                            que aplique a alguna promoción se le configurarán los anchos de bandas promocionales
     * @since 1.12
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 2.0 21-11-2019 - Se agrega el proceso para notificar el cambio de plan a konibit mediante GDA en caso de aplicar.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.1 06-05-2020  Se elimina el envío del parámetro floatPrecioNuevoIp a la función cambioVelocidadIsb, ya que no es necesaria
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.2 11-05-2020 Se unifica las validaciones por marca y no por modelo de olt
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.3 14-07-2020 Se modifica la función para realizar validación de solicitudes de agregar equipo en estado Asignada
     * 
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 2.4 16-07-2020 - Actualización: Se agrega bloque de código para actualizar datos de tareas en nueva tabla DB_SOPORTE.INFO_TAREA
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.4 15-07-2020 Se modifica la función para poder visualizar el mensaje de error en los procesos que se ejecutan posterior al cambio
     *                         de velocidad del servicio -> Gestión de licencias de Internet Protegido y equipos Dual Band
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.5 21-09-2020 Se agrega restricción para ejecutar proceso de cambio de plan que ejecuta la gestión de los dual band sólo cuando 
     *                         el punto no tenga servicios W+AP
     * 
     * @author kevin ortiz <kcortiz@telconet.ec>
     * @version 2.6  22-10-2020  - Se agrega el proceso para agregar el historial de suspencion de servicios y cancelacion
     * 
     * @author kevin ortiz <kcortiz@telconet.ec>
     * @version 2.7  22-10-2020  - Se agrega el proceso para la colonacion de caracteristicas 
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 2.8 26-10-2020 Se agrega nuevo parámetro utilizado para verificación de Ip Fija Wan en planes Pyme
     * 
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 2.9 03-03-2021 Se agrega nuevo parámetro para indicar si ippc solicita cableado ethernet en cambio de plan
     * 
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 3.0 22-03-2021 - Se agrega el proceso para ejecutar el recálculo en caso de poseer solicitud con  
     *                           Beneficio 3era Edad / Adulto Mayor ó Cliente con Discapacidad en el servicio cuando 
     *                           se realiza un cambio de plan para MD.
     *                         - Se agrega la lógica para guardar historial del cambio de plan MD, y adicional
     *                           se concatena el mensaje de salida ejecutado en el proceso de recálculo.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 3.1 12-11-2021 Se agrega la invocación del web service para confirmación de opción de Tn a Middleware
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 3.2 10-02-2022 - Se agrega la ejecución del mapeo de promoción después de actualizar el plan en el servicio.
     * 
     * @author Alberto Arias <farias@telconet.ec>
     * @version 3.3 09-06-2022 - Se modifica la llamada al metodo de notificarKonibit, para ejecutarse al final del metodo, 
     * debido a que debe guardarse el plan nuevo y luego realizar la llamada al metodo notificarKonibit.
     * 
     * @author Emmanuel Martillo <emartillo@telconet.ec>
     * @version 3.4 03-03-2023 - Se agrega validacion por Prefijo EN para que siga el flujo de Megadatos. 
     * 
     * @author Jonathan Burgos <jsburgos@telconet.ec>
     * @version 3.5 28-04-2023 - Se agrega un proceso para crear una tarea automatica cuando se cambia de plan, y tambien 
     * para generar y enviar el adendum cambio de plan. 
     */
    public function cambiarPlan($arrayPeticiones)
    {
        //*OBTENCION DE PARAMETROS-----------------------------------------------*/
        $idEmpresa            = $arrayPeticiones['idEmpresa'];
        $prefijoEmpresa       = $arrayPeticiones['prefijoEmpresa'];
        $idServicio           = $arrayPeticiones['idServicio'];
        $planId               = $arrayPeticiones['planId'];
        $precioViejo          = $arrayPeticiones['precioViejo'];
        $precioNuevo          = $arrayPeticiones['precioNuevo'];
        $capacidad1           = $arrayPeticiones['capacidad1'];
        $capacidad2           = $arrayPeticiones['capacidad2'];
        $strCapacidad1Actual  = $arrayPeticiones['capacidad1Actual'];
        $strCapacidad2Actual  = $arrayPeticiones['capacidad2Actual'];
        $usrCreacion          = $arrayPeticiones['usrCreacion'];
        $ipCreacion           = $arrayPeticiones['ipCreacion'];
        $intIdDepartamento    = $arrayPeticiones['intIdDepartamento'];
        $intIdPersonaEmpRol   = !empty($arrayPeticiones['intIdPersonaEmpRol'])?$arrayPeticiones['intIdPersonaEmpRol']:0;
        $intIdOficina         = !empty($arrayPeticiones['intIdOficina'])?$arrayPeticiones['intIdOficina']:0;
        $strConservarIp       = !empty($arrayPeticiones['strConservarIp'])?$arrayPeticiones['strConservarIp']:"";
        $strMsjErrorAdicional = "";
        $strIppcSolicita         = $arrayPeticiones['ippcSolicita'];
        $arrayDataConfirmacionTn = array();
        $strEjecucionWs       = !empty($arrayPeticiones['strEjecucionWs'])?$arrayPeticiones['strEjecucionWs']:"NO";

        //obtener parametros empresa para regularizacion cambio de plan.
        $boolRegCambioPlan = false;
        $objParametroCambioPlanCab   = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')->findOneBy(
            array('nombreParametro' => 'REGULARIZACION_CAMBIO_DE_PLAN',
                'estado'          => 'Activo'));
        if(is_object($objParametroCambioPlanCab))
        {
            $objEjecutaCambioDePlanDet     = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findOneBy(
                array("parametroId" => $objParametroCambioPlanCab->getId(),
                    "valor1"      => "EMPRESA_CAMBIO_PLAN",
                    "valor2"      => $prefijoEmpresa,
                    "estado"      => "Activo"));
            if(is_object($objEjecutaCambioDePlanDet) && $objEjecutaCambioDePlanDet->getValor3() == "SI" )
            {
                $boolRegCambioPlan = true;
            }
        }

        //Si es un cambio de plan permitido por empresa configurada y se consume desde el ws cambio de plan.
        if($boolRegCambioPlan && $strEjecucionWs == "SI")
        {
            //Buscar idPersonaEmpRol por medio del usuario de creacio enviado en el ws cambio de plan.
            $this->utilService->insertError('Telcos+', 
                                            'InfoCambiarPlanServices->cambiarPlan', 
                                            'Si es una regularizacion cambio de plan ejecutandose desde el ws cambio de plan.', 
                                            'telcos', 
                                            $ipCreacion
                                        );
            $arrayParametrosEmp['empresa']             = $idEmpresa;
            $arrayParametrosEmp['criterios']['login']  = $usrCreacion;
            $arrayPerEjecutaCP = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                  ->findPersonalByCriterios($arrayParametrosEmp);
            if(isset($arrayPerEjecutaCP['registros']) && !empty($arrayPerEjecutaCP['registros']))
            {
                $arrayInfoPerCP  = $arrayPerEjecutaCP['registros'][0];
                $intIdPersonaEmpRol   = $arrayInfoPerCP['idPersonaEmpresaRol'];
                $this->utilService->insertError('Telcos+', 
                                            'InfoCambiarPlanServices->cambiarPlan', 
                                            'PersonaEmpresRol encontrada '.$intIdPersonaEmpRol, 
                                            'telcos', 
                                            $ipCreacion
                                        );
            }
            else
            {
                $strMenesajeErr = "No se ha podido obtener información de usuario para crear tarea automatica de regularización cambio de plan.";
                $this->utilService->insertError('Telcos+', 
                                            'InfoCambiarPlanServices->cambiarPlan', 
                                            $strMenesajeErr, 
                                            'telcos', 
                                            $ipCreacion
                                        );
                throw new \Exception($strMenesajeErr);
            }
        }

        
        //migracion_ttco_md
        $arrayEmpresaMigra = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                  ->getEmpresaEquivalente($idServicio, $prefijoEmpresa);

        if($arrayEmpresaMigra)
        { 
            if ($arrayEmpresaMigra['prefijo']=='TTCO')
            {
                $idEmpresaFlujo      = $arrayEmpresaMigra['id'];
                $prefijoEmpresaFlujo = $arrayEmpresaMigra['prefijo'];
            }
            else
            {
                $idEmpresaFlujo      = $idEmpresa;
                $prefijoEmpresaFlujo = $prefijoEmpresa;
            }
        }

        
        if($precioNuevo>0)
        {
            if($precioNuevo > $precioViejo)
            {
                
            }
            else
            {
                $respuestaArray[] = array('status'  => 'PRECIO ANTERIOR DEBE SER MENOR AL NUEVO', 
                                          'mensaje' => 'PRECIO ANTERIOR DEBE SER MENOR AL NUEVO');
                return $respuestaArray;
            }
        }
        
        $servicio            = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($idServicio);
        $intIdPunto          = $servicio->getPuntoId()->getId();
        $servicioTecnico     = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                    ->findOneBy(array( "servicioId" => $servicio->getId()));
        $interfaceElementoId = $servicioTecnico->getInterfaceElementoId();
        $interfaceElemento   = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')->find($interfaceElementoId);
        $elemento            = $interfaceElemento->getElementoId();
        $modeloElemento      = $elemento->getModeloElementoId();
         //consultar si el olt tiene aprovisionamiento de ips en el CNR
        $objDetalleElemento  = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                       ->findOneBy(array('detalleNombre' => 'OLT MIGRADO CNR',
                                                                         'elementoId'    => $interfaceElemento->getElementoId()->getId()));
        $arrayRespuestaCambioPlan       = array();
        $arrayRespuestaVerifCambioPlan  = array();
        //*DECLARACION DE TRANSACCIONES------------------------------------------*/
        $this->emComercial->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();
        $this->emSoporte->getConnection()->beginTransaction();
        //*----------------------------------------------------------------------*/

        try{
            if($prefijoEmpresaFlujo=="TTCO")
            {
                $respuestaArray=$this->cambioPlanTtco($servicio, $planId, $interfaceElemento, $elemento, $modeloElemento, 
                                                      $capacidad1, $capacidad2, $idEmpresa, $usrCreacion, $ipCreacion, 
                                                      $precioViejo, $precioNuevo);
            }
            else if(($prefijoEmpresaFlujo=="MD" || $prefijoEmpresaFlujo=="EN" )|| 
                    ($prefijoEmpresaFlujo == 'TNP' && $arrayPeticiones["esIsb"] != "SI"))
            {
                $intIdPlanAnterior    = 0;
                $objPlanAnterior      = $servicio->getPlanId();
                $boolSuspendidoSinMacNuevoMac = false; 
                if(is_object($objPlanAnterior))
                {
                    $intIdPlanAnterior = $objPlanAnterior->getId();
                }
                $arrayRespuestaVerificaCambioPlan   = $this->verificaCambioPlan(array(  "intIdServicio"     => $idServicio,
                                                                                        "intIdPlanNuevo"    => $planId));
                if($arrayRespuestaVerificaCambioPlan['status'] === "OK")
                {
                    $arrayParametrosPlanesDiciembre     = array("codEmpresa"                => $idEmpresa,
                                                                "idServicio"                => $idServicio,
                                                                "idPlanNuevo"               => $planId
                                                                );
                    $arrayRespuestaVerifCambioPlan      = $this->verificarCambioPlanesDiciembre($arrayParametrosPlanesDiciembre);
                    if($arrayRespuestaVerifCambioPlan['status'] === "OK")
                    {
                        if($arrayRespuestaVerifCambioPlan["planActualTieneMcAfee"]  === "SI" && 
                        $arrayRespuestaVerifCambioPlan["planNuevoTieneMcAfee"]   === "SI" &&
                        $arrayRespuestaVerifCambioPlan["strTieneNuevoAntivirus"] === "NO")
                        {
                            $arrayRespProdsAdicionales = $this->cancelarService
                                                              ->verificaYObtieneInfoProductoAdicionalEnPlan(array( 
                                                                                                            "objServicio"       => $servicio,
                                                                                                            "strUsrCreacion"    => $usrCreacion,
                                                                                                            "strClientIp"       => $ipCreacion));
                        }

                        $arrayPLanSuspendido  = $this->emComercial->getRepository('schemaBundle:InfoPlanCab')
                                                             ->findBy(array("descripcionPlan"  => "Suspension Temporal del Servicio"));   
                              
                        $arrayPLanSuspendidoCodigo = array();

                        if($arrayPLanSuspendido != null)
                        {
                            foreach($arrayPLanSuspendido as $codigoPlan)
                            {
                                $arrayPLanSuspendidoCodigo[] = $codigoPlan->getCodigoPlan(); 
                            }
                        }
                        else 
                        {
                            $strMostrarError = "SI";
                            throw new \Exception('No se podido obtener los planes  en suspensión');
                        }
                        
                        if(is_object($arrayRespuestaVerifCambioPlan["objProdIntProtegidoAnt"]))
                        {
                            if($servicio->getPlanId() !== null)
                            {
                                $strProductoIPMP = $arrayRespuestaVerifCambioPlan["objProdIntProtegidoAnt"]->getId();

                                $objProductoIPMP = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($strProductoIPMP); 
                                
                                $objPlan  = $this->emComercial->getRepository('schemaBundle:InfoPlanCab')
                                                              ->findOneById($planId); 
                                    
                                $arrayProCaract  = array(   "objServicio"      => $servicio,
                                                            "objProducto"      => $objProductoIPMP,
                                                            "strUsrCreacion"   => $usrCreacion);
                                    
                                $arrayProCaract["strCaracteristica"] = "PLAN ANTERIOR";
                                $strRespuestaCaractPlan = $this->serviceLicenciasKaspersky
                                                               ->obtenerValorServicioProductoCaracteristica($arrayProCaract);
                                
                                //Si no existe, guardo
                                if(!is_object($strRespuestaCaractPlan["objServicioProdCaract"]))
                                {
                                    $arrayProCaract["strCaracteristica"] = "PLAN ANTERIOR";
                                    $arrayProCaract["strValor"]          = $objPlanAnterior->getNombreplan();
                                    $this->serviceLicenciasKaspersky->guardaServicioProductoCaracteristica($arrayProCaract);
                                }
                                else
                                {
                                    $arrayProCaract["strCaracteristica"] = "PLAN ANTERIOR";
                                    $arrayProCaract["strValorNuevo"]     = $objPlanAnterior->getNombreplan();                    
                                    $this->serviceLicenciasKaspersky->actualizarServicioProductoCaracteristica($arrayProCaract);
                                }
    
                                $arrayProCaractAntivirus  = array(  "objServicio"       => $servicio,
                                                                    "objProducto"       => $objProductoIPMP,
                                                                    "strUsrCreacion"    => $usrCreacion);
    
                                if(in_array($objPlan->getCodigoPlan(),$arrayPLanSuspendidoCodigo) &&
                                $arrayRespuestaVerifCambioPlan["planActualTieneMcAfee"]  === "SI" )
                                {
                                    $arrayParamsLicencias           = array("strProceso"                => "CORTE_ANTIVIRUS",
                                                                            "strEscenario"              => "SUSPENCION_PROD_EN_PLAN",
                                                                            "objServicio"               => $servicio,
                                                                            "objPunto"                  => $servicio->getPuntoId(),
                                                                            "objProductoIPMP"           => $objProductoIPMP,
                                                                            "strEstadoServicioInicial"  => $servicio->getEstado(),
                                                                            "strCodEmpresa"             => $idEmpresa,
                                                                            "strUsrCreacion"            => $usrCreacion,
                                                                            "strIpCreacion"             => $ipCreacion,
                                                                            );
    
                                    $arrayRespuestaGestionLicencias = $this->serviceLicenciasKaspersky->gestionarLicencias($arrayParamsLicencias);
                                    $strStatusGestionLicencias      = $arrayRespuestaGestionLicencias["status"];
                                    $strMensajeGestionLicencias     = $arrayRespuestaGestionLicencias["mensaje"];
            
                                    $arrayRespuestaWs               = $arrayRespuestaGestionLicencias["arrayRespuestaWs"];
                                        
                                    if($strStatusGestionLicencias === "ERROR")
                                    {
                                        $strMostrarError = "SI";
                                        throw new \Exception($strMensajeGestionLicencias);
                                    }
                                    else
                                    {
                                        $arrayProCaractAntivirus["strCaracteristica"] = "SUSCRIBER_ID";
                                        $arrayRespuestaCaract = $this->serviceLicenciasKaspersky
                                                            ->obtenerValorServicioProductoCaracteristica($arrayProCaractAntivirus);
                                        $strEstadoAnterior = $arrayRespuestaCaract["objServicioProdCaract"]->getEstado(); 
                                        
                                        $arrayProCaractAntivirus["strCaracteristica"] = "SUSCRIBER_ID ESTADO ANTERIOR";
                                        $strRespuestaCaractsuscriberid = $this->serviceLicenciasKaspersky
                                                            ->obtenerValorServicioProductoCaracteristica($arrayProCaractAntivirus);
                                        
                                        if(!is_object($strRespuestaCaractsuscriberid["objServicioProdCaract"]))
                                        {
                                            $arrayProCaractAntivirus["strCaracteristica"] = "SUSCRIBER_ID ESTADO ANTERIOR";
                                            $arrayProCaractAntivirus["strValor"]          = $strEstadoAnterior;
                                            $this->serviceLicenciasKaspersky->guardaServicioProductoCaracteristica($arrayProCaractAntivirus);
                                        }
                                        else//si existo
                                        {
    
                                            $arrayProCaractAntivirus["strCaracteristica"]  = "SUSCRIBER_ID ESTADO ANTERIOR";
                                            $arrayProCaractAntivirus["strValorNuevo" ]     = $strEstadoAnterior;                                    
                                            $this->serviceLicenciasKaspersky->actualizarServicioProductoCaracteristica($arrayProCaractAntivirus);
                                        } 
        
                                        $arrayProCaractAntivirusSuscriberID        = array( "objServicio"       => $servicio,
                                                                                            "objProducto"       => $objProductoIPMP,
                                                                                            "strUsrCreacion"    => $usrCreacion,
                                                                                            "strCaracteristica" => "SUSCRIBER_ID",
                                                                                            "strEstadoNuevo"    => "Suspendido"
                                                                                            );  
                                    
                                        $this->serviceLicenciasKaspersky
                                             ->actualizarServicioProductoCaracteristica($arrayProCaractAntivirusSuscriberID);
                                    }
                                }
                                // Reactivacion  Suspendidos SI
                                else if(in_array($objPlanAnterior->getCodigoPlan(),$arrayPLanSuspendidoCodigo) &&
                                    $arrayRespuestaVerifCambioPlan["planNuevoTieneMcAfee"]   === "SI")
                                {
    
                                    $arrayParamsLicencias = array("strProceso"                => "REACTIVACION_ANTIVIRUS",
                                                                  "strEscenario"              => "REACTIVACION_PROD_EN_PLAN",
                                                                  "objServicio"               => $servicio,
                                                                  "objPunto"                  => $servicio->getPuntoId(),
                                                                  "objProductoIPMP"           => $objProductoIPMP,
                                                                  "strEstadoServicioInicial"  => $servicio->getEstado(),
                                                                  "strCodEmpresa"             => $idEmpresa,
                                                                  "strUsrCreacion"            => $usrCreacion,
                                                                  "strIpCreacion"             => $ipCreacion,
                                                                  );
    
                                    $arrayRespuestaGestionLicencias = $this->serviceLicenciasKaspersky->gestionarLicencias($arrayParamsLicencias);
                                    $strStatusGestionLicencias      = $arrayRespuestaGestionLicencias["status"];
                                    $strMensajeGestionLicencias     = $arrayRespuestaGestionLicencias["mensaje"];
                                    $arrayRespuestaWs               = $arrayRespuestaGestionLicencias["arrayRespuestaWs"];
                                        
                                    if($strStatusGestionLicencias === "ERROR")
                                    {
                                        $strMostrarError = "SI";
                                        throw new \Exception('No se pudo realizar la reactivación del Producto Internet Protegido');
                                    }
                                    else
                                    {
                                        $arrayProCaract["strCaracteristica"] = "PLAN ANTERIOR";
                                        $strRespuestaCaractPlan = $this->serviceLicenciasKaspersky
                                                                    ->obtenerValorServicioProductoCaracteristica($arrayProCaract);
                                        $strNombrePlanAnterior = $strRespuestaCaractPlan["objServicioProdCaract"]->getValor();
    
                                        //ACTUALIZAR SUSCRIBER ID ESTADO ANTERIOR
                                        
                                        
                                        $arrayProCaractAntivirus["strCaracteristica"] = "SUSCRIBER_ID";
                                        $arrayRespuestaCaractsuscriberID = $this->serviceLicenciasKaspersky
                                                        ->obtenerValorServicioProductoCaracteristica($arrayProCaractAntivirus);

                                        if(is_object( $arrayRespuestaCaractsuscriberID["objServicioProdCaract"]))   
                                        {                                                     
                                            //Estado del suscriber (Suspendido)
                                            $strEstado = $arrayRespuestaCaractsuscriberID["objServicioProdCaract"]->getEstado(); 

                                            $arrayProCaractAntivirus["strCaracteristica"] = "SUSCRIBER_ID ESTADO ANTERIOR";
                                            $arrayRespuestaCaract = $this->serviceLicenciasKaspersky
                                                            ->obtenerValorServicioProductoCaracteristica($arrayProCaractAntivirus);
                                        
                                            if(!is_object($arrayRespuestaCaract["objServicioProdCaract"]))
                                            {
                                                $arrayProCaractAntivirus["strCaracteristica"] = "SUSCRIBER_ID ESTADO ANTERIOR";
                                                $arrayProCaractAntivirus["strValor"]          = $strEstado;
                                                $this->serviceLicenciasKaspersky
                                                     ->guardaServicioProductoCaracteristica($arrayProCaractAntivirus);
                                            }
                                            else
                                            {
                                                $strEstadoAnterior = $arrayRespuestaCaract["objServicioProdCaract"]->getValor();
                                                
                                                $arrayProCaractAntivirus["strCaracteristica"]  = "SUSCRIBER_ID ESTADO ANTERIOR";
                                                $arrayProCaractAntivirus["strValorNuevo" ]     = $strEstadoAnterior;
                                                $this->serviceLicenciasKaspersky
                                                     ->actualizarServicioProductoCaracteristica($arrayProCaractAntivirus);
                                                

                                            } 
        
                                            $arrayProCaractAntivirusSuscriberID    = array( "objServicio"       => $servicio,
                                                                                            "objProducto"       => $objProductoIPMP,
                                                                                            "strUsrCreacion"    => $usrCreacion,
                                                                                            "strCaracteristica" => "SUSCRIBER_ID",
                                                                                            "strEstadoNuevo"    => $strEstadoAnterior
                                                                                            );  
                                    
                                            $this->serviceLicenciasKaspersky
                                                 ->actualizarServicioProductoCaracteristica($arrayProCaractAntivirusSuscriberID);
                                        }                                                                                                         
                                    }
                                }
                                //cambio de plan con internet protegido a un plan sin internet protegido
                                 else if($arrayRespuestaVerifCambioPlan["planActualTieneMcAfee"]  === "SI" &&
                                    $arrayRespuestaVerifCambioPlan["planNuevoTieneMcAfee"]   === "NO")
                                {
                                    
                                    $arrayProCaractAntivirus["strCaracteristica"] = "SUSCRIBER_ID";
                                    $strRespuestaCaract = $this->serviceLicenciasKaspersky
                                                            ->obtenerValorServicioProductoCaracteristica($arrayProCaractAntivirus);

                                    if(is_object($strRespuestaCaract["objServicioProdCaract"]))    
                                    {                                                        
                                        $intSuscriberId = $strRespuestaCaract["objServicioProdCaract"]->getValor();
                                    
                                        $arrayProCaractAntivirus["strCaracteristica"] = 'CORREO ELECTRONICO';
                                        $arrayRespuestaGetSpc  = $this->serviceLicenciasKaspersky
                                                                      ->obtenerValorServicioProductoCaracteristica($arrayProCaractAntivirus);
                                        $strCorreoSuscripcion = $arrayRespuestaGetSpc["objServicioProdCaract"]->getValor();
        
                                        $strMsjErrorAdicHtml            = "No se pudo actualizar la suscripción al correo ".
                                                                          $strCorreoSuscripcion."<br>";
        
                                        $arrayParamsLicencias           = array("strProceso"                => "CANCELACION_ANTIVIRUS",
                                                                                "strEscenario"              => "CANCELACION_PROD_EN_PLAN",
                                                                                "objServicio"               => $servicio,
                                                                                "objPunto"                  => $servicio->getPuntoId(),
                                                                                "strCodEmpresa"             => $idEmpresa,
                                                                                "objProductoIPMP"           => $objProductoIPMP,
                                                                                "strUsrCreacion"            => $usrCreacion,
                                                                                "strIpCreacion"             => $ipCreacion,
                                                                                "strEstadoServicioInicial"  => $servicio->getEstado(),
                                                                                "intSuscriberId"            => $intSuscriberId,
                                                                                "strCorreoSuscripcion"      => $strCorreoSuscripcion,
                                                                                "strMsjErrorAdicHtml"       => $strMsjErrorAdicHtml
                                                                                );
        
                                        $arrayRespuestaGestionLicencias = $this->serviceLicenciasKaspersky
                                                                               ->gestionarLicencias($arrayParamsLicencias);

                                        $strStatusGestionLicencias      = $arrayRespuestaGestionLicencias["status"];
                                        $strMensajeGestionLicencias     = $arrayRespuestaGestionLicencias["mensaje"];
                                        $arrayRespuestaWs               = $arrayRespuestaGestionLicencias["arrayRespuestaWs"];
                                            
                                        if($strStatusGestionLicencias === "ERROR")
                                        {
                                            $strMostrarError = "SI";
                                            throw new \Exception($strMensajeGestionLicencias);
                                        }
                                        else
                                        {
        
                                            $objServHistServicio    = new InfoServicioHistorial();
                                            $objServHistServicio->setServicioId($servicio);
                                            $objServHistServicio->setObservacion("Se cancelo el servicio, nuevo plan
                                                                no tiene servicio de Internet Protegido");
                                            $objServHistServicio->setEstado("Activo");
                                            $objServHistServicio->setUsrCreacion($usrCreacion);
                                            $objServHistServicio->setFeCreacion(new \DateTime('now'));
                                            $objServHistServicio->setIpCreacion($ipCreacion);
                                            $this->emComercial->persist($objServHistServicio);
                                            $this->emComercial->flush();
                                        }
                                    }
    
                                }
                                else if ($arrayRespuestaVerifCambioPlan["planActualTieneMcAfee"]  === "SI" &&
                                        $arrayRespuestaVerifCambioPlan["planNuevoTieneMcAfee"]   === "SI")
                                {
                                    
                                    $arrayProCaractAntivirus["strCaracteristica"] = "SUSCRIBER_ID";
                                    $strRespuestaCaract = $this->serviceLicenciasKaspersky
                                                            ->obtenerValorServicioProductoCaracteristica($arrayProCaractAntivirus);

                                    if(is_object($strRespuestaCaract["objServicioProdCaract"]))
                                    {
                                        $strEstado = $strRespuestaCaract["objServicioProdCaract"]->getEstado();  
        
                                        $arrayProCaractAntivirus["strCaracteristica"] = "SUSCRIBER_ID ESTADO ANTERIOR";
                                        $arrayProCaractAntivirus["strValorNuevo"]     =  $strEstado;     
                                        $arrayProCaractAntivirus["strEstadoNuevo"]    =  "Activo";   
                                        $this->serviceLicenciasKaspersky->actualizarServicioProductoCaracteristica($arrayProCaractAntivirus);
                                    }
                                }
                            
                            }
                        }
                        else//reactivacion cuando plan anterior no tiene macafee
                        {
                            if(in_array($objPlanAnterior->getCodigoPlan(),$arrayPLanSuspendidoCodigo) &&
                                    $arrayRespuestaVerifCambioPlan["planNuevoTieneMcAfee"]   === "SI")
                            {          
                                $boolSuspendidoSinMacNuevoMac = true;                      
                                $objProductoIPMP = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->findOneBy
                                (array('descripcionProducto' => 'I. PROTEGIDO MULTI PAID', 'estado' => 'Activo')); 
                                
                                $objPlan  = $this->emComercial->getRepository('schemaBundle:InfoPlanCab')
                                                              ->findOneById($planId);                                                               
                                    
                                $arrayProCaractAntivirus  = array(   "objServicio"      => $servicio,
                                                            "objProducto"      => $objProductoIPMP,
                                                            "strUsrCreacion"   => $usrCreacion);

                                $arrayParamsLicencias = array("strProceso"                => "REACTIVACION_ANTIVIRUS",
                                                                "strEscenario"              => "REACTIVACION_PROD_EN_PLAN",
                                                                "objServicio"               => $servicio,
                                                                "objPunto"                  => $servicio->getPuntoId(),
                                                                "objProductoIPMP"           => $objProductoIPMP,
                                                                "strEstadoServicioInicial"  => $servicio->getEstado(),
                                                                "strCodEmpresa"             => $idEmpresa,
                                                                "strUsrCreacion"            => $usrCreacion,
                                                                "strIpCreacion"             => $ipCreacion,
                                                                );

                                $arrayRespuestaGestionLicencias = $this->serviceLicenciasKaspersky->gestionarLicencias($arrayParamsLicencias);
                                $strStatusGestionLicencias      = $arrayRespuestaGestionLicencias["status"];
                                $strMensajeGestionLicencias     = $arrayRespuestaGestionLicencias["mensaje"];
                                $arrayRespuestaWs               = $arrayRespuestaGestionLicencias["arrayRespuestaWs"];
                                    
                                if($strStatusGestionLicencias === "ERROR")
                                {
                                    $strMostrarError = "SI";
                                    throw new \Exception('No se pudo realizar la reactivación del Producto Internet Protegido');
                                }
                                else
                                {
                                    $arrayProCaractAntivirus["strCaracteristica"] = "PLAN ANTERIOR";
                                    $strRespuestaCaractPlan = $this->serviceLicenciasKaspersky
                                                                ->obtenerValorServicioProductoCaracteristica($arrayProCaractAntivirus);
                                    
                                    $arrayProCaractAntivirus["strCaracteristica"] = "SUSCRIBER_ID";
                                    $arrayRespuestaCaractsuscriberID = $this->serviceLicenciasKaspersky
                                                    ->obtenerValorServicioProductoCaracteristica($arrayProCaractAntivirus);
                                    //Estado del suscriber (Suspendido)

                                    if(is_object($arrayRespuestaCaractsuscriberID["objServicioProdCaract"]))
                                    {
                                        $strEstado = $arrayRespuestaCaractsuscriberID["objServicioProdCaract"]->getEstado(); 

                                        $arrayProCaractAntivirus["strCaracteristica"] = "SUSCRIBER_ID ESTADO ANTERIOR";
                                        $arrayRespuestaCaract = $this->serviceLicenciasKaspersky
                                                        ->obtenerValorServicioProductoCaracteristica($arrayProCaractAntivirus);
                                    
                                        if(!is_object($arrayRespuestaCaract["objServicioProdCaract"]))
                                        {
                                            $arrayProCaractAntivirus["strCaracteristica"] = "SUSCRIBER_ID ESTADO ANTERIOR";
                                            $arrayProCaractAntivirus["strValor"]          = $strEstado;
                                            $this->serviceLicenciasKaspersky->guardaServicioProductoCaracteristica($arrayProCaractAntivirus);
                                        }
                                        else
                                        {
                                            $strEstadoAnterior = $arrayRespuestaCaract["objServicioProdCaract"]->getValor(); 
                                            
                                            $arrayProCaractAntivirus["strCaracteristica"]  = "SUSCRIBER_ID ESTADO ANTERIOR";
                                            $arrayProCaractAntivirus["strValorNuevo" ]     = $strEstadoAnterior;                                    
                                            $this->serviceLicenciasKaspersky->actualizarServicioProductoCaracteristica($arrayProCaractAntivirus);
                                            

                                        } 

                                        $arrayProCaractAntivirusSuscriberID    = array( "objServicio"       => $servicio,
                                                                                        "objProducto"       => $objProductoIPMP,
                                                                                        "strUsrCreacion"    => $usrCreacion,
                                                                                        "strCaracteristica" => "SUSCRIBER_ID",
                                                                                        "strEstadoNuevo"    => $strEstadoAnterior
                                                                                        );  
                                
                                        $this->serviceLicenciasKaspersky
                                             ->actualizarServicioProductoCaracteristica($arrayProCaractAntivirusSuscriberID);
                                    }
                                }
                            }
                        }
                        $arrayParametros = array(
                                                'servicio'                => $servicio,
                                                'intIdDepartamento'       => $intIdDepartamento,
                                                'servicioTecnico'         => $servicioTecnico,
                                                'planId'                  => $planId,
                                                'interfaceElemento'       => $interfaceElemento,
                                                'elemento'                => $elemento,
                                                'modeloElemento'          => $modeloElemento,
                                                'capacidad1'              => $capacidad1,
                                                'capacidad2'              => $capacidad2,
                                                'capacidad1Actual'        => $strCapacidad1Actual,
                                                'capacidad2Actual'        => $strCapacidad2Actual,
                                                'idEmpresa'               => $idEmpresa,
                                                'usrCreacion'             => $usrCreacion,
                                                'ipCreacion'              => $ipCreacion,
                                                'precioViejo'             => $precioViejo,
                                                'precioNuevo'             => $precioNuevo,
                                                'intIdPersonaEmpRol'      => $intIdPersonaEmpRol,
                                                'intIdOficina'            => $intIdOficina,
                                                'strConservarIp'          => $strConservarIp,
                                                'strPrefijoEmpresaFlujo'  => $prefijoEmpresaFlujo,
                                                'ippcSolicita' => $strIppcSolicita,
                                                );

                        $respuestaArray = $this->cambioPlanMd($arrayParametros);
                        $arrayDataConfirmacionTn    = $respuestaArray[0]['arrayDataConfirmacionTn'];
                        $strOcurrioException        = $respuestaArray[0]['strOcurrioException'];
                        if($strOcurrioException === "SI")
                        {
                            throw new \Exception($respuestaArray[0]['mensaje']);
                        }
                        $this->utilService->insertError('Telcos+', 
                                                        'InfoCambiarPlanService.cambiarPlan', 
                                                        $respuestaArray[0]['mensaje'], 
                                                        $usrCreacion, 
                                                        $ipCreacion
                                                       );
                        $arrayRespuestaCambioPlan = $respuestaArray[0];
                    }
                    else
                    {
                        $respuestaArray[] = $arrayRespuestaVerifCambioPlan;
                    }
                }
                else
                {
                    $respuestaArray[] = $arrayRespuestaVerificaCambioPlan;
                }
            }
            else if(($prefijoEmpresaFlujo=="TN" && $arrayPeticiones["esIsb"] === "SI") ||
                    ($prefijoEmpresaFlujo=="TNP" && $arrayPeticiones["esIsb"] === "SI"))
            {
                $arrayParametros = array(
                                            'strOpcion'             => "GRID",
                                            'objElementoOlt'        => $elemento,
                                            'objModeloOlt'          => $modeloElemento,
                                            'objServicio'           => $servicio,
                                            'objServicioTecnico'    => $servicioTecnico,
                                            'objInterfaceOlt'       => $interfaceElemento,
                                            'strVelocidadNueva'     => $arrayPeticiones["velocidadNueva"],
                                            'strVelocidadAnterior'  => $arrayPeticiones["velocidadAnterior"],
                                            'strCodEmpresa'         => $idEmpresa,
                                            'strPrefijoEmpresa'     => $prefijoEmpresaFlujo,
                                            'strUsrCreacion'        => $usrCreacion,
                                            'strIpCreacion'         => $ipCreacion,
                                            'floatPrecioNuevo'      => $precioNuevo
                                        );
                $respuestaArray=$this->cambioVelocidadIsb($arrayParametros);
            }
            $this->emInfraestructura->getConnection()->commit();
            $this->emComercial->getConnection()->commit();
            $this->emSoporte->getConnection()->commit();

            //Proceso que graba tarea en INFO_TAREA
            if (isset($arrayRespuestaCambioPlan['intDetalleId']) && $arrayRespuestaCambioPlan['intDetalleId'] > 0)
            {
                $arrayParametrosInfoTarea['intDetalleId']   = $arrayRespuestaCambioPlan['intDetalleId'];
                $arrayParametrosInfoTarea['strUsrCreacion'] = $arrayPeticiones['usrCreacion'];
                $this->serviceSoporte->crearInfoTarea($arrayParametrosInfoTarea);
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
            
            $this->emInfraestructura->getConnection()->close();
            $this->emComercial->getConnection()->close();
            $this->emSoporte->getConnection()->close();
            
            $status           = "ERROR";
            $mensaje          = "ERROR EN LA LOGICA DE NEGOCIO, ".$e->getMessage();
            $respuestaArray[] = array('status'=>$status, 'mensaje'=>$mensaje);
        }
        
        //agregar validacion si es tellion cnr enviar actualizacion al ldap
        if(($modeloElemento->getMarcaElementoId()->getNombreMarcaElemento()=="HUAWEI" 
            || $modeloElemento->getMarcaElementoId()->getNombreMarcaElemento() === "ZTE" 
            ||($objDetalleElemento)) && $respuestaArray[0]["status"]=="OK")
        {
            //envio al ldap
            $mixResultadoJsonLdap = $this->servicioGeneral->ejecutarComandoLdap("A", $idServicio, $prefijoEmpresaFlujo);
            if($mixResultadoJsonLdap->status!="OK")
            {
                $strMsjErrorAdicional = $strMsjErrorAdicional . "<br>" . $mixResultadoJsonLdap->mensaje;
            }
        }
        try
        {
            //Se realizan las validaciones respectivas para los nuevos planes de MD
            if(($prefijoEmpresaFlujo=="MD" || $prefijoEmpresaFlujo=="EN" )
                && isset($arrayRespuestaCambioPlan) && !empty($arrayRespuestaCambioPlan) && $arrayRespuestaCambioPlan["status"] === "OK")
            {
                $arrayRespuestaServiciosWyAp    = $this->servicioGeneral
                                                       ->obtenerServiciosPorProducto(
                                                                                    array(  "intIdPunto"                    => $intIdPunto,
                                                                                            "arrayNombresTecnicoProducto"   => array("WDB_Y_EDB"),
                                                                                            "strCodEmpresa"                 => $idEmpresa));
                $intContadorServiciosWyAp       = $arrayRespuestaServiciosWyAp["intContadorServiciosPorProducto"];
                if(intval($intContadorServiciosWyAp) === 0)
                {
                    //Se ejecuta el procedimiento para ejecutar un cambio de plan con equipos Dual Band
                    $arrayRespuestaEjecutaProcesosCambioPlan   = $this->ejecutaProcesosCambioPlan(array("intIdServicio"     => $idServicio,
                                                                                                        "intIdPlanAnterior" => $intIdPlanAnterior,
                                                                                                        "intIdPlanNuevo"    => $planId,
                                                                                                        "strUsrCreacion"    => $usrCreacion,
                                                                                                        "strIpCreacion"     => $ipCreacion));
                    if($arrayRespuestaEjecutaProcesosCambioPlan['status'] !== "OK")
                    {
                        $strMsjErrorAdicional   = $strMsjErrorAdicional . "<br>" . $arrayRespuestaEjecutaProcesosCambioPlan["mensaje"];
                        $objServHistServicio    = new InfoServicioHistorial();
                        $objServHistServicio->setServicioId($servicio);
                        $objServHistServicio->setObservacion($arrayRespuestaEjecutaProcesosCambioPlan["mensaje"]);
                        $objServHistServicio->setEstado($servicio->getEstado());
                        $objServHistServicio->setUsrCreacion($usrCreacion);
                        $objServHistServicio->setFeCreacion(new \DateTime('now'));
                        $objServHistServicio->setIpCreacion($ipCreacion);
                        $this->emComercial->persist($objServHistServicio);
                        $this->emComercial->flush();
                    }
                }
                
                //verificar si el cliente tiene productos adicionales de Internet protegido en el punto
                $arrayParametrosIntProtegido = array();
                $arrayParametrosIntProtegido['strIpCreacion']       = $ipCreacion;
                $arrayParametrosIntProtegido['strUsrCreacion']      = $usrCreacion;
                $arrayParametrosIntProtegido['objServicioInternet'] = $servicio;
                $arrayProdAdiIntProtegido   = $this->obtenerServiciosInternetProtegido($arrayParametrosIntProtegido);
                $strStatusIntProtegido      = $arrayProdAdiIntProtegido['strStatus'];
                $strTieneAntivirusAdicional = $arrayProdAdiIntProtegido['strTieneAntivirusAdicional'];
                $arrayServiciosIntProtAnt   = $arrayProdAdiIntProtegido['arrayServiciosAdicionalesAntiguos'];
                $arrayRespuestaActAdiEnPlan = array();
                $arrayRespuestaActAdiEnPlan["status"]  = "ERROR";
                $arrayRespuestaActAdiEnPlan["mensaje"] = "ERROR";
                
                if($arrayRespuestaVerifCambioPlan["planActualTieneMcAfee"] === "NO" &&
                   $arrayRespuestaVerifCambioPlan["planNuevoTieneMcAfee"]  === "SI" && !$boolSuspendidoSinMacNuevoMac)
                {
                    $strActivaMcAfeeEnPlan      = "SI";
                    $arrayRespuestaActAdiEnPlan = $this->activarService
                                                       ->activarProductosAdicionalesEnPlan(array( "intIdServicio"     => $servicio->getId(),
                                                                                                  "strTipoProceso"    => "CAMBIAR_PLAN",
                                                                                                  "strOpcion"         => "ACTIVACION",
                                                                                                  "strCodEmpresa"     => $idEmpresa,
                                                                                                  "strUsrCreacion"    => $usrCreacion,
                                                                                                  "strClientIp"       => $ipCreacion));
                }
                
                //Se agrega la lógica para guardar historial del cambio de plan, luego de haber realizado el cambio de plan MD y adicional
                //se concatena el mensaje de salida ejecutado en el proceso de recálculo.
                $objPlanCabViejo = $arrayRespuestaCambioPlan['objPlanCabViejo'];
                $objPlanCabNuevo = $arrayRespuestaCambioPlan['objPlanCabNuevo']; 
                $intFrecuencia   = $arrayRespuestaCambioPlan['intFrecuencia']; 
                $objInfoServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($servicio->getId());
            
                if ( is_object($objInfoServicio) )
                {
                    $floatPrecioVentaActual   = $objInfoServicio->getPrecioVenta();
                    $floatValorDescuento      = $objInfoServicio->getValorDescuento();
                    $strObsDescuento          = '';
                }

                //servicio
                $servicio->setFrecuenciaProducto($intFrecuencia);
                $servicio->setPlanId($objPlanCabNuevo);
                if($precioNuevo > 0)
                {
                    if($precioNuevo > $precioViejo)
                    {
                        $servicio->setPrecioVenta($precioNuevo);
                        $floatPrecioNuevoPlan = $precioNuevo;
                    }
                    else
                    {
                        $servicio->setPrecioVenta($precioViejo);
                        $floatPrecioNuevoPlan = $precioViejo;
                    }
                }//end if precio nuevo > 0
                else
                {
                    $servicio->setPrecioVenta($precioViejo);
                    $floatPrecioNuevoPlan    = $precioViejo;
                }

                if($idEmpresa == '26')
                {
                    $servicio->setDescripcionPresentaFactura($objPlanCabNuevo->getNombrePlan());
                }

                //Se recalcula el valor del descuento del servicio
                $arrayRespuesta = $this->recalcularDescuentoServicio(array("objInfoServicio"      => $servicio,
                                                                           "strUsrCreacion"       => $usrCreacion,
                                                                           "strIpCreacion"        => $ipCreacion,
                                                                           "floatPrecionuevoPlan" => $floatPrecioNuevoPlan
                                                                           )
                                                                    );
                //Si no es null, obtengo respuesta exitosa
                $strObsDescuento          = "";
                if(!is_null($arrayRespuesta))
                {
                    if(is_object($arrayRespuesta["objInfoServicio"]))
                    {
                        $servicio->setValorDescuento($arrayRespuesta["objInfoServicio"]->getValorDescuento());
                        $servicio->setDescuentoUnitario($arrayRespuesta["objInfoServicio"]->getDescuentoUnitario());
                    }
                    if(!is_null($arrayRespuesta["strObsDescuento"]))
                    {
                        $strObsDescuento = $arrayRespuesta["strObsDescuento"];
                    }
                }

                $this->emComercial->persist($servicio);
                $this->emComercial->flush();

                //EJECUTAR PROMOCIONES DE SERVICIOS POR CAMBIO DE PLAN
                $objInfoServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($servicio->getId());
                $arrayParametrosInfoBw = array();
                $arrayParametrosInfoBw['intIdServicio']     = $objInfoServicio->getId();
                $arrayParametrosInfoBw['intIdEmpresa']      = $idEmpresa;
                $arrayParametrosInfoBw['strTipoProceso']    = "CAMBIO_PLAN";
                $arrayParametrosInfoBw['strValor']          = $objInfoServicio->getPlanId()->getId();
                $arrayParametrosInfoBw['strUsrCreacion']    = $usrCreacion;
                $arrayParametrosInfoBw['strIpCreacion']     = $ipCreacion;
                $arrayParametrosInfoBw['strPrefijoEmpresa'] = $prefijoEmpresaFlujo;
                $this->servicePromociones->configurarPromocionesBW($arrayParametrosInfoBw);

                //Proceso para ejecutar el recálculo en caso de poseer solicitud con Beneficio 3era Edad / Adulto Mayor ó 
                //Cliente con Discapacidad en el servicio enviado por parámetro.
                if (is_object($servicio))
                {
                    $arrayRespuestaRecalculo = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                ->ejecutaProcesoRecalculo(array('intIdEmpresa'        => $idEmpresa, 
                                                                                'strPrefijoEmpresa'   => $prefijoEmpresaFlujo,
                                                                                'strUsuario'          => 'telcos_recalculo', 
                                                                                'strEmailUsuario'     => null, 
                                                                                'intIdServicio'       => $servicio->getId(), 
                                                                                'strTipoProceso'      => 'INDIVIDUAL',
                                                                                'strIp'               => $ipCreacion,
                                                                                'objUtilService'      => $this->utilService));
                
                    $strObsDescuento = $strObsDescuento.$arrayRespuestaRecalculo['mensaje'];
                }

                //historial del servicio
                if($boolRegCambioPlan)
                {
                    error_log("ejecutando flujo de regularizacion cambio de plan");
                    $fltDiferenciaPrecio = $floatPrecioNuevoPlan - $floatPrecioVentaActual;
                    $objSoporteService  = $this->serviceSoporte;
                    $arrayTopDownFormulaA1 = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->getOne(
                                                                    "USR_CAMBIO_PLAN_TAREA_AUTO",
                                                                    "",
                                                                    '',
                                                                    'VALOR_TOP_DOWN_CAMBIO_PLAN_F_A1',
                                                                    '',
                                                                    '',
                                                                    '',
                                                                    ''
                                                                );
                    $fltTopFA1  = 4;
                    $fltDownFA1 = 0;
                    if (isset($arrayTopDownFormulaA1) && !empty($arrayTopDownFormulaA1))
                    {
                        $fltTopFA1  = $arrayTopDownFormulaA1['valor1'];
                        $fltDownFA1 = $arrayTopDownFormulaA1['valor2'];
                    }
                    $strObservacion = "Se cambio de plan, " . ($fltDiferenciaPrecio >= $fltTopFA1? 
                                                            "UPGRADE <br>" : ($fltDiferenciaPrecio >= $fltDownFA1? 
                                                            "ACTUALIZACION <br>" : "DOWNGRADE <br>"))
                                                            . "plan anterior:<b>" . $objPlanCabViejo->getNombrePlan() . "</b>,<br>"
                                                            . "plan nuevo:<b>" . $objPlanCabNuevo->getNombrePlan() . "</b>,<br>"
                                                            . "precio anterior:<b>" . $floatPrecioVentaActual . "</b>,<br>"
                                                            . "precio nuevo:<b>" . $floatPrecioNuevoPlan . "</b>" 
                                                            . ($strObsDescuento != "" ? ",<br>" : ",<br>")
                                                            . "motivo:<b>" . $arrayPeticiones['motivo'] . "</b>,<br>"
                                                            . "diferencia de precio:<b>" . $fltDiferenciaPrecio . "</b>,<br>"
                                                            . $strObsDescuento; 
                    $arrayParametrosTarea                              = array();
                    $arrayParametrosTarea['intIdEmpresa']              = $idEmpresa;
                    $arrayParametrosTarea['strPrefijoEmpresa']         = $prefijoEmpresaFlujo;
                    $arrayParametrosTarea['strNombreTarea']            = "CAMBIO DE PLAN";
                    $arrayParametrosTarea['strNombreProceso']          = "SISTEMAS: TELCOS -TECNICO";
                    $arrayParametrosTarea['strUserCreacion']           = $usrCreacion;
                    $arrayParametrosTarea['strIpCreacion']             = $ipCreacion;
                    $arrayParametrosTarea['intFormaContacto']          = 5;
                    $arrayParametrosTarea['strMotivoTarea']            = $strObservacion;
                    $arrayParametrosTarea['intPuntoId']                = $servicio->getPuntoId(); 
                    $arrayParametrosTarea['intIdPersonaEmpresaRol']    = $intIdPersonaEmpRol;
                    $arrayParametrosTarea['strObsSeguimiento']         = $strObservacion;
                    $arrayParametrosTarea['strObservacionTarea']       = $strObservacion;
                    $arrayParametrosTarea['boolAsignarTarea']          = true;
                    $arrayParametrosTarea['strTipoTarea']              = 'T';
                    $arrayParametrosTarea['strTareaRapida']            = 'S';
                    $arrayParametrosTarea['strUsuarioAsigna']          = $usrCreacion;
                    $arrayParametrosTarea['strTipoAsignacion']         = 'empleado';


                    $objServicioHist = new InfoServicioHistorial();
                    $objServicioHist->setServicioId($servicio);
                    $objServicioHist->setObservacion($strObservacion);
                    $objServicioHist->setEstado("Activo");
                    $objServicioHist->setUsrCreacion($usrCreacion);
                    $objServicioHist->setFeCreacion(new \DateTime('now'));
                    $objServicioHist->setIpCreacion($ipCreacion);
                    $this->emComercial->persist($objServicioHist);
                    $this->emComercial->flush();

                    if( floatval($floatPrecioNuevoPlan) > floatval($floatPrecioVentaActual) )
                    {
                        //historial del servicio
                        
                        $strObservacionHis = "Precio anterior: " . $floatPrecioVentaActual;
                        $objServicioHistorial = new InfoServicioHistorial();
                        $objServicioHistorial->setServicioId($servicio);
                        $objServicioHistorial->setObservacion($strObservacionHis);
                        $objServicioHistorial->setEstado("Activo");
                        $objServicioHistorial->setUsrCreacion($usrCreacion);
                        $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                        $objServicioHistorial->setIpCreacion($ipCreacion);
                        $objServicioHistorial->setAccion('confirmoCambioPrecio');
                        $this->emComercial->persist($objServicioHistorial);
                        $this->emComercial->flush();
                    }
                    
                    $respuestaArray[0]["mensaje"] = $strObservacion; 
                    
                    $this->utilService->insertError('Telcos+', 
                                                    'InfoCambiarPlanService.cambiarPlan', 
                                                    $respuestaArray[0]['mensaje'], 
                                                    $usrCreacion, 
                                                    $ipCreacion
                                                );
                    //Proceso para notificar el cambio de plan a konibit mediante GDA en caso de aplicar.
                    if (is_object($servicio))
                    {
                        $this->emInfraestructura->getRepository('schemaBundle:InfoServicioTecnico')
                                ->notificarKonibit(array ('intIdServicio'  => $servicio->getId(),
                                                        'strTipoProceso' => 'CAMBIOPLAN',
                                                        'strTipoTrx'     => 'INDIVIDUAL',
                                                        'strUsuario'     => $usrCreacion,
                                                        'strIp'          => $ipCreacion,
                                                        'objUtilService' => $this->utilService));
                    }
                    $arrayRespuestaTareaSoporte = $objSoporteService->crearTareaCasoSoporte($arrayParametrosTarea);
                    if($arrayRespuestaTareaSoporte["mensaje"] != "ok" )
                    {
                        $this->utilService->insertError('Telcos+', 
                                                    'InfoCambiarPlanServices->cambiarPlan', 
                                                    'No se ha creado la tarea de soporte en el proceso de cambio de plan', 
                                                    $usrCreacion, 
                                                    $ipCreacion
                                                );
                    }
                }
                else
                {
                    error_log("Ejecutando flujo normal de cambio de plan");
                    //historial del servicio
                    $observacion = "Se cambio de plan, "
                    . "plan anterior:<b>" . $objPlanCabViejo->getNombrePlan() . "</b>,<br>"
                    . "plan nuevo:<b>" . $objPlanCabNuevo->getNombrePlan() . "</b>,<br>"
                    . "precio anterior:<b>" . $floatPrecioVentaActual . "</b>,<br>"
                    . "precio nuevo:<b>" . $floatPrecioNuevoPlan . "</b>" . ($strObsDescuento != "" ? ",<br>" : "")
                    . $strObsDescuento;

                    $objServicioHist = new InfoServicioHistorial();
                    $objServicioHist->setServicioId($servicio);
                    $objServicioHist->setObservacion($observacion);
                    $objServicioHist->setEstado("Activo");
                    $objServicioHist->setUsrCreacion($usrCreacion);
                    $objServicioHist->setFeCreacion(new \DateTime('now'));
                    $objServicioHist->setIpCreacion($ipCreacion);
                    $this->emComercial->persist($objServicioHist);
                    $this->emComercial->flush();

                    if( floatval($floatPrecioNuevoPlan) > floatval($floatPrecioVentaActual) )
                    {
                        //historial del servicio
                        $strObservacion = "Precio anterior: " . $floatPrecioVentaActual;
                        $objServicioHistorial = new InfoServicioHistorial();
                        $objServicioHistorial->setServicioId($servicio);
                        $objServicioHistorial->setObservacion($strObservacion);
                        $objServicioHistorial->setEstado("Activo");
                        $objServicioHistorial->setUsrCreacion($usrCreacion);
                        $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                        $objServicioHistorial->setIpCreacion($ipCreacion);
                        $objServicioHistorial->setAccion('confirmoCambioPrecio');
                        $this->emComercial->persist($objServicioHistorial);
                        $this->emComercial->flush();
                    }
                    
                    $respuestaArray[0]["mensaje"] = $observacion; 
                    
                    $this->utilService->insertError('Telcos+', 
                                                    'InfoCambiarPlanService.cambiarPlan', 
                                                    $respuestaArray[0]['mensaje'], 
                                                    $usrCreacion, 
                                                    $ipCreacion
                                                    );
                    //Proceso para notificar el cambio de plan a konibit mediante GDA en caso de aplicar.
                    if (is_object($servicio))
                    {
                        $this->emInfraestructura->getRepository('schemaBundle:InfoServicioTecnico')
                                ->notificarKonibit(array ('intIdServicio'  => $servicio->getId(),
                                                            'strTipoProceso' => 'CAMBIOPLAN',
                                                            'strTipoTrx'     => 'INDIVIDUAL',
                                                            'strUsuario'     => $usrCreacion,
                                                            'strIp'          => $ipCreacion,
                                                            'objUtilService' => $this->utilService));
                    }
                }  
            }
        }
        catch (\Exception $e)
        {
            $this->utilService->insertError('Telcos+', 
                                            'InfoCambiarPlanService->cambiarPlanesNuevosMd', 
                                            $e->getMessage(), 
                                            $usrCreacion, 
                                            $ipCreacion
                                           );
        }

        if(isset($strMsjErrorAdicional) && !empty($strMsjErrorAdicional) 
            && isset($respuestaArray[0]["mensaje"]) && !empty($respuestaArray[0]["mensaje"]))
        {
            $respuestaArray[0]["mensaje"] = $respuestaArray[0]["mensaje"] . $strMsjErrorAdicional;
        }
        
        $this->rdaMiddleware->ejecutaWsSinEsperarRespuestaMiddleware($arrayDataConfirmacionTn);
        
        return $respuestaArray;
    }

    /**
     * @author Jose Cruz <jfcruzc@telconet.ec>
     * @version 1.0      Obtener el id del plan actual del servicio del cliente.
     * @since 02-02-2023
     */
    public function getPlanActualServicio($intIdServicio)
    {
        $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);

        if (is_null($objServicio)) 
        {
            return null;
        }

        return $objServicio->getPlanId();
    }

    /**
     * @author Jonathan Burgos <jsburgos@telconet.ec>
     * @version 1.0
     * @since 28-04-2023 Funcion que permite enviar notificacion y documento de adendum cambio de plan.
     */
    public function enviarNotificacionCambioPlan($arrayParametros)
    {

        $strCodigoMens      = $arrayParametros['strCodigoMens'];
        $strIdTipoPromoMens = $arrayParametros['strIdTipoPromoMens'];
        $intIdServicio      = $arrayParametros['intIdServicio'];
        $intIdEmpresa       = $arrayParametros['intIdEmpresa'];
        $strPrecioViejo     = $arrayParametros['precioViejo'];
        $intPlanId          = $arrayParametros['planId'];
        $intPlanIdViejo     = $arrayParametros['planIdViejo'];
        $strPrefijoEmpresa  = $arrayParametros['strPrefijoEmpresa'];
        $strMensaje         = $arrayParametros['strMensaje'];
        $strStatus          = "OK";
        
        $arrayMapeoPromo = array('strCodigoMens'      => $strCodigoMens,
                                 'strIdTipoPromoMens' => $strIdTipoPromoMens,
                                 'intIdServicio'      => $intIdServicio,
                                 'intIdEmpresa'       => $intIdEmpresa);

        $arrayRespuestaMapeoPromo = $this->mapeoPromoCambioPlan($arrayMapeoPromo);

        $strRetornaRespuesta    = $arrayRespuestaMapeoPromo['status'] ;
                
        if ($strRetornaRespuesta != "OK")
        {
            $strStatus  = "OK";
            $strMensaje = $arrayRespuestaMapeoPromo['mensaje'];
        }

        $objParametroCambioPlan   = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')->findOneBy(
            array('nombreParametro' => 'REGULARIZACION_CAMBIO_DE_PLAN',
                'estado'          => 'Activo'));

        if(is_object($objParametroCambioPlan))
        {
            $objEjecutaCambioDePlan     = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findOneBy(
                array("parametroId" => $objParametroCambioPlan->getId(),
                    "valor1"      => "EMPRESA_CAMBIO_PLAN",
                    "estado"      => "Activo"));
            if(is_object($objEjecutaCambioDePlan) && $objEjecutaCambioDePlan->getValor2() == $strPrefijoEmpresa)
            {
                try 
                {
                    $arrayParamCorreo = [
                        'idServicio'  => $intIdServicio,
                        'precioViejo' => $strPrecioViejo,
                        'planId'      => $intPlanId,
                        'planIdViejo' => $intPlanIdViejo
                    ];
                    $this->enviarCorreoCambioPlan($arrayParamCorreo);
                } catch (\Exception $e) 
                {
                    $strMensajeError = "Ha ocurrido un error con el microservicio resumen de compra. ".
                    "Error en enviarCorreoCambioPlan. ".$e->getMessage();
                    $this->utilService->insertError('Telcos+', 
                              'InfoCambiarPlanService.enviarNotificacionCambioPlan', 
                              $strMensajeError,
                              'telcos', 
                              '127.0.0.1'
                             );
                    error_log($strMensajeError);
                }
            }
        }
        $arrayRespuesta = array("status"    => $strStatus,
                                "mensaje"   => $strMensaje);
        return $arrayRespuesta;
    }

    /**
     * @author Jose Cruz <jfcruzc@telconet.ec>
     * @version 1.0
     * @since 02-02-2023 envio de correo con documento adendum por cambio de plan.
     */
    public function enviarCorreoCambioPlan($arrayParametros)
    {
        $intIdServicio          = $arrayParametros['idServicio'];
        $floatPrecioViejo         = $arrayParametros['precioViejo'];
        $intPlanId              = $arrayParametros['planId'];
        $intPlanIdViejo         = $arrayParametros['planIdViejo'];
        
        $objPlanCabViejo     = $this->emComercial->getRepository('schemaBundle:InfoPlanCab')->find($intPlanIdViejo);
        $objPlanCabNuevo     = $this->emComercial->getRepository('schemaBundle:InfoPlanCab')->find($intPlanId);

        $arrayTokenCas = $this->objTokenCasService->generarTokenCas();
                
                if(empty($arrayTokenCas['strToken']))
                {
                    throw new \Exception($arrayTokenCas['strMensaje']); 
                }
                
                $arrayParametrosCorreo   = array();
                $arrayParametrosCorreo['token'] = $arrayTokenCas['strToken'];
                $arrayParametrosCorreo['idServicio'] = $intIdServicio;
                
                $arrayParametrosCorreo['planViejoValor'] = $floatPrecioViejo;
                $arrayParametrosCorreo['planViejo'] = $objPlanCabViejo->getNombrePlan();
                $arrayParametrosCorreo['nombrePlan'] = $objPlanCabNuevo->getNombrePlan();
                $objSalida = $this->servicioGeneral
                            ->envioCorreoCambioVelocidad($arrayParametrosCorreo);
    }
    
    /**
     * 
     * cambioPlanTn
     * 
     * Metodo que sirve para realizar cambio de plan/velocidad para servicios de TN
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0
     * @since 04-05-2016
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 
     * @since 18-06-2016 - Se escribe historial de fallo para las solicitudes no exitosas
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.2
     * @since 01-07-2016 - Se cambio BW a concentrador cuando se realiza cambio de plan en el extremo en caso de tenerlo
     * 
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.3
     * @since 11-07-2016 - Notificacion por Correo Electronico
     * 
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.4
     * @since 14-08-2016 - Se finaliza la solicitud de descuento asociada a una solicitud de cambio de plan
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.5
     * @since 08-09-2016 - Se realiza validaciones para cuando exista data faltante requerida genere siempre registro de historial
     *                     con motivo de fallo          
     * 
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.6 23-12-2016 - Se crea un historial al servicio con accion 'confirmoCambioPrecio' cuando se realiza un cambio de plan y el nuevo
     * precio es mayor que el precio anterior. El historial creado será usado para generar la factura proporcional
     * correspondiente por aumento de ancho de banda.
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.7
     * @since 09-01-2017 Se incluye cambio de plan para producto wifi
     * 
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.8
     * @since 05-04-2017 Se agrega historial respectivo para poder facturar el proporcional del servicio por el cambio de plan.
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.9 
     * @since 10-04-2017 Se agrega para que se ejecute el cambio en los concentradores wifi
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 2.0
     * @since 26-04-2017 - Se modifica metodo para escribir historial referente a cambios de plan de BACKUPS referenciado su Servicio PRINCIPAL
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 2.1
     * @since 04-07-2017 - Se agrega sumatoria para configurar en el puerto del concentrador tantp extremos como servicios ligados a la misma UM
     * 
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.2 19-07-2017 - Se realizan ajustes para para incluir las solicitudes de Demo
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.3 03-08-2017 - Se realizan ajustes para activar la solicitud padre solo para solicitudes Demos
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.4 11-09-2017 - Se valida el ingreso de solicitudes de requerimiento de cliente para DEMOS, si el servicio ya tiene una en
     *                           estado Pendiente ya no permitirle ingresar mas
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.5 07-03-2018 - Se agrega flujo para servicios Internet Small Business
     * 
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.6 02-05-2018 - Se realiza validacion para considerar las solicitudes en Fallo
     *
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.7 27-06-2018 - Se agrega parametro al llamado de la funcion crearTareaRetiroEquipoPorDemo y se agrega el parametro $strNumeroTarea,
     *                           para que reciba la respuesta de la funcion
     *
     * @author Modificado: Jesús Bozada <jbozada@telconet.ec>
     * @version 2.8 19-04-2018 - Se agrega programación por integración de app Telcograph con procesos de Telcos
     * @since 2.6
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.9 21-06-2019 - Se valida que el mensaje: 'Cambio de Plan Realizado en Extremo' solo se de para servicios que tengan un concentrador
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 3.0 11-05-2020 - Se realizan ajustes para prevenir BandWidth en negativo.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 3.1 01-06-2020 - Se agrega el id del servicio a la url 'configBW' del ws de networking para la validación del BW
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 3.2 22-03-2021 Se abre la programacion para servicios Internet SDWAN
     *
     * @param Array $arrayParametros  []
     * @return Array $arrayRespuesta [ status , mensaje ]
     */
    public function cambioPlanTn($arrayParametros)
    {
        
        $strObservacionConcentrador = "";
        $strNuevaDescripcionNueva   = "";
        $strObservacionHistorial    = "";
        $arrayRespuesta             = array();
        $arrayParametrosCambP       = array();
        $arrayFechasDemo            = array();        
        $arrayParametrosTiemposDemo = array();        
        $strArrayDestinatarios      = array();   
        $arrayParametrosPlantilla   = array();
        $strEstadoActualSol         = "";
        $strCapacidad1Anterior      = "";
        $strCapacidad2Anterior      = "";        
        $strMensajeHistorialServ    = "";
        $strFacturableValor         = "";
        $intSolicitudActiva         = "";
        $strTieneSolicitudActiva    = "N";
        $strPrecioDemoValor         = "0";
        $strObservacionConcentrador = "";
        $strCodigoEmpresa           = "10";
        $strPrefijoEmpresa          = "TN";
        $strAsunto                  = "Notificación de Inicio del Demo";
        $strCodigoPLantilla         = "INICIO_DEMO";     
        $strVendedor                = "";
        $strAsesorComercial         = "";              
        $strFechaInicio             = "";
        $strFechaFin                = "";
        $strBanderaHistorial        = "N";
        $strInsertaHistorial        = "S";

        try
        {
            $this->emComercial->getConnection()->beginTransaction(); 
            
            $objProductoCp = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($arrayParametros['productoId']);
            $objServicioCp = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($arrayParametros['idServicio']);
         
            //Validar si el servicio tiene una solicitud de Demos en estado Activa                        
             $arrayParametrosSol["intServicio"]  = $arrayParametros['idServicio']; 
             $arrayParametrosSol["strSolicitud"] = "DEMOS";
             $arrayParametrosSol["strEstado"]    = "Activa";

             $objCaractReferencia = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                      ->findOneBy(array("descripcionCaracteristica" => 'REFERENCIA'));

             $intSolicitudActiva = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                     ->getSolicitudActivaPorServicio($arrayParametrosSol);

             if($intSolicitudActiva != "")
             {
                 $strTieneSolicitudActiva = "S";
                 
                 $arrayParametrosFinalizarSol["usrCreacion"] = $arrayParametros['usrCreacion'];
                 $arrayParametrosFinalizarSol["ipCreacion"]  = $arrayParametros['ipCreacion'];

                 $objInfoProcesoMasivoDet = $this->emInfraestructura->getRepository('schemaBundle:InfoProcesoMasivoDet')
                                                                    ->findOneBy(array("solicitudId" => $intSolicitudActiva));
                 
                 if(is_object($objInfoProcesoMasivoDet))
                 {
                     //Se obtiene la solicitud padre
                     $objInfoProcesoMasivoCab = $this->emInfraestructura->getRepository('schemaBundle:InfoProcesoMasivoCab')
                                                                        ->find($objInfoProcesoMasivoDet->getProcesoMasivoCabId()->getId());  
                     
                     if(is_object($objInfoProcesoMasivoCab))
                     {
                         $arrayParametrosFinalizarSol["idSolicitudPadre"] = $objInfoProcesoMasivoCab->getSolicitudId();
                     }                                          
                 }
                 
                 
                 
                 $objInfoDetalleSol = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->find($intSolicitudActiva);
                 
                      
                 if($arrayParametros['tipoProceso'] == "CambioPlan" && is_object($objInfoDetalleSol))  
                 {
                    //Finalizar la solicitud de cambio de plan                        
                    $objInfoDetalleSol->setEstado("Finalizada");
                    $this->emComercial->persist($objInfoDetalleSol);
                    $this->emComercial->flush();

                    //Se crea Historial de la finalizacion del detalle de la solicitud de descuento
                    $objDetalleSolsHist = new InfoDetalleSolHist();
                    $objDetalleSolsHist->setDetalleSolicitudId($objInfoDetalleSol);
                    $objDetalleSolsHist->setEstado($objInfoDetalleSol->getEstado());
                    $objDetalleSolsHist->setFeCreacion(new \DateTime('now'));
                    $objDetalleSolsHist->setUsrCreacion($arrayParametros['usrCreacion']);
                    $objDetalleSolsHist->setIpCreacion($arrayParametros['ipCreacion']);
                    $objDetalleSolsHist->setObservacion("Se finaliza la solicitud por ejecución de cambio de plan");
                    $this->emComercial->persist($objDetalleSolsHist);
                    $this->emComercial->flush();

                    //Finalizar Solicitudes Padres
                    $this->servicioGeneral->finalizarSolicitudPadrePorProcesoMasivo($arrayParametrosFinalizarSol);

                    $strMensajeHistorialServ = "Se finaliza el Demo por - ";
                 }
             }

            $arrayParametrosCambP["idServicio"]  = $arrayParametros['idServicio'];            
                        
             if($arrayParametros['tipoProceso'] == "Demos")
             {
                 $arrayParametrosCaract["descripcionSolicitud"]      = 'DEMOS';
                 $arrayParametrosCaract["descripcionCaracteristica"] = 'Cancelacion Demo';
                 $arrayParametrosCaract["estadoSolicitud"]           = array('Activa','Fallo');
                 $arrayParametrosCaract["idServicio"]                = $arrayParametros['idServicio'];
                 
                 //Se obtiene el valor del caracteristica de Cancelación Demo
                 $arrayCaracteristicaCancelacion = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                     ->getValorCaracteristicaXServicio($arrayParametrosCaract);

                 if($arrayCaracteristicaCancelacion["valor"] == "S")
                 {
                     $arrayParametros["tipoProceso"]      = "CancelDemos";
                     $arrayParametrosCambP["tipoProceso"] = "CancelDemos";
                 }
             }
             else
             {
                 $arrayParametrosCambP["tipoProceso"] = "";
             }
            
            if($arrayParametros["esISB"] === "SI")
            {
                $arrayParametrosCambP["strTipoSolicitud"] = "CAMBIO PLAN";
            }
            
            $arrayResultado = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                ->getArrayInfoCambioPlanPorSolicitud($arrayParametrosCambP);

            if($arrayResultado && count($arrayResultado)>0 && $arrayParametros["esISB"] === "SI")
            {
                $strStatus                              = "OK";
                $objDetalleSolicitud                    = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                            ->find($arrayResultado['idSolicitud']);
                if(isset($arrayResultado["velocidadNueva"]) && !empty($arrayResultado["velocidadNueva"])
                    && $arrayResultado['precioNuevo'] > 0)
                {
                    $arrayParametrosCambioVelocidad     = array(
                                                                    'intIdServicio'         => $arrayParametros["idServicio"],
                                                                    'strVelocidadNueva'     => $arrayResultado["velocidadNueva"],
                                                                    'strVelocidadAnterior'  => $arrayParametros["velocidadISB"],
                                                                    'strCodEmpresa'         => $arrayParametros["idEmpresa"],
                                                                    'strPrefijoEmpresa'     => $arrayParametros["prefijoEmpresa"],
                                                                    'strUsrCreacion'        => $arrayParametros['usrCreacion'],
                                                                    'strIpCreacion'         => $arrayParametros['ipCreacion'],
                                                                    'floatPrecioNuevo'      => $arrayResultado['precioNuevo']
                                                                );
                    $arrayRespuestaCambioVelocidad      = $this->cambioVelocidadIsb($arrayParametrosCambioVelocidad);
                    if($arrayRespuestaCambioVelocidad[0]['status'] === "OK")
                    {
                        $strMensajeLdap       = "";
                        $mixResultadoJsonLdap = $this->servicioGeneral->ejecutarComandoLdap("A", $arrayParametros["idServicio"], "TN");
                        if($mixResultadoJsonLdap->status!="OK")
                        {
                            $strMensajeLdap .= "<br>Error al ejecutar Ldap: " . $mixResultadoJsonLdap->mensaje;
                        }
                        
                        $statusCode = 200;
                        $objDetalleSolicitud->setEstado("Finalizada");
                        $this->emComercial->persist($objDetalleSolicitud);
                        $this->emComercial->flush();  
                        
                        $strMensaje = "Se realizo Cambio de Velocidad exitosamente".$strMensajeLdap;
                        
                        $objDetalleSolHist = new InfoDetalleSolHist();
                        $objDetalleSolHist->setDetalleSolicitudId($objDetalleSolicitud);
                        $objDetalleSolHist->setEstado($objDetalleSolicitud->getEstado());
                        $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
                        $objDetalleSolHist->setUsrCreacion($arrayParametros['usrCreacion']);
                        $objDetalleSolHist->setIpCreacion($arrayParametros['ipCreacion']);
                        $objDetalleSolHist->setObservacion($strMensaje);
                        $this->emComercial->persist($objDetalleSolHist);
                        $this->emComercial->flush();
                        
                        $arrayFinalizarSolMasiva = array(   "idSolicitudPadre"  => $arrayResultado['idSolicitudPadre'],
                                                            "usrCreacion"       => $arrayParametros['usrCreacion'],
                                                            "ipCreacion"        => $arrayParametros['ipCreacion'],
                                                            "idServicio"        => $arrayParametros['idServicio']);
                        $this->servicioGeneral->finalizarSolicitudPadrePorProcesoMasivo($arrayFinalizarSolMasiva);
                        $this->servicioGeneral->enviarNotificacionFinalizadoSolicitudMasiva($arrayFinalizarSolMasiva);
                    }
                    else
                    {
                        $strStatus  = "ERROR";
                        $strMensaje = $arrayRespuestaCambioVelocidad[0]['mensaje'];    
                        $statusCode = 500;

                        //Cuando existe un fallo de algun tipo se pone en estado Fallo a la solicitud                        
                        $objDetalleSolicitud->setEstado("Fallo");
                        $this->emComercial->persist($objDetalleSolicitud);
                        $this->emComercial->flush();
                        
                        //Se crea Historial de Servicio
                        $objDetalleSolHist = new InfoDetalleSolHist();
                        $objDetalleSolHist->setDetalleSolicitudId($objDetalleSolicitud);
                        $objDetalleSolHist->setEstado($objDetalleSolicitud->getEstado());
                        $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
                        $objDetalleSolHist->setUsrCreacion($arrayParametros['usrCreacion']);
                        $objDetalleSolHist->setIpCreacion($arrayParametros['ipCreacion']);
                        $objDetalleSolHist->setObservacion($strMensaje);
                        $this->emComercial->persist($objDetalleSolHist);
                        $this->emComercial->flush();
                    }
                }
                else
                {
                    $strStatus  = "ERROR";
                    $strMensaje = "No existe la velocidad o precio nuevo del cambio de plan que se desea realizar";    
                    $statusCode = 404;

                    //Cuando existe un fallo de algun tipo se pone en estado Fallo a la solicitud                        
                    $objDetalleSolicitud->setEstado("Fallo");
                    $this->emComercial->persist($objDetalleSolicitud);
                    $this->emComercial->flush();
                    
                    //Se crea Historial de Servicio
                    $objDetalleSolHist = new InfoDetalleSolHist();
                    $objDetalleSolHist->setDetalleSolicitudId($objDetalleSolicitud);
                    $objDetalleSolHist->setEstado($objDetalleSolicitud->getEstado());
                    $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
                    $objDetalleSolHist->setUsrCreacion($arrayParametros['usrCreacion']);
                    $objDetalleSolHist->setIpCreacion($arrayParametros['ipCreacion']);
                    $objDetalleSolHist->setObservacion($strMensaje);
                    $this->emComercial->persist($objDetalleSolHist);
                    $this->emComercial->flush();
                }
            }
            else if($arrayResultado && count($arrayResultado)>0)
            {     
                $objCaractCapac1Anterior = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                             ->findOneBy(array("descripcionCaracteristica" => 'CAPACIDAD1 ANTERIOR'));

                $objCaractCapac2Anterior = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                             ->findOneBy(array("descripcionCaracteristica" => 'CAPACIDAD2 ANTERIOR'));

                if($arrayParametros["tipoProceso"] == "CancelDemos")
                {
                    $strMensajeHistorialServ = "Cancelación de Demo - ";
                    //Se calcula los ancho de banda anteriores
                    if(is_object($objCaractCapac1Anterior))
                    {
                        $objInfoDetalleSolCaract = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                     ->findOneBy(array("detalleSolicitudId" => $arrayResultado['idSolicitud'],
                                                                                       "caracteristicaId"   => $objCaractCapac1Anterior->getId()));
                        if(is_object($objInfoDetalleSolCaract))
                        {
                            $strCapacidad1Anterior               = $objInfoDetalleSolCaract->getValor() ? $objInfoDetalleSolCaract->getValor() : "";
                            $arrayResultado['capacidadUnoNueva'] = $strCapacidad1Anterior;
                        }
                        
                    }
                    
                        
                    if(is_object($objCaractCapac2Anterior))
                    {                    
                        $objInfoDetalleSolCaract = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                     ->findOneBy(array("detalleSolicitudId" => $arrayResultado['idSolicitud'],
                                                                                       "caracteristicaId"   => $objCaractCapac2Anterior->getId()));
                        
                        if(is_object($objInfoDetalleSolCaract))
                        {
                            $strCapacidad2Anterior               = $objInfoDetalleSolCaract->getValor() ? $objInfoDetalleSolCaract->getValor() : "";
                            $arrayResultado['capacidadDosNueva'] = $strCapacidad2Anterior;                            
                            
                        }                        
                    }
                }
                elseif($arrayParametros["tipoProceso"] == "Demos")
                {
                    $strMensajeHistorialServ = "Demo - ";
                }

                
                                
                if($arrayParametros["tipoProceso"] == "Demos" || $arrayParametros["tipoProceso"] == "CancelDemos")
                {                          
                    $arrayParametrosTiemposDemo["intSolicitudId"] = $arrayResultado['idSolicitud'];
         
                    //Se obtienen las fechas del Demo
                    $arrayFechasDemo = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                         ->getFechasDemo($arrayParametrosTiemposDemo);         
                    
                    //Si el demo no es facturable, el precio nuevo es igual al actual
                    $objAdmiCaracteristica = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                               ->findOneBy(array("descripcionCaracteristica" => 'Facturable'));
                    
                    $objAdmiCaracteristicaP = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                ->findOneBy(array("descripcionCaracteristica" => 'Precio'));                    
                    
                    $objAdmiCaractCambioEquipo = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                   ->findOneBy(array("descripcionCaracteristica" => 'Cambio Equipo Demo'));
                    
                    $objAdmiCaractElementoCliente = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                      ->findOneBy(array("descripcionCaracteristica" => 'ELEMENTO CLIENTE'));
                    
                    $objTipoSolRetiroEquipo = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                                ->findOneBy(array("descripcionSolicitud" => "SOLICITUD RETIRO EQUIPO"));
                    
                    
                    

                    //Se obtiene el valor de la caracteristica Facturable
                    if(is_object($objAdmiCaracteristica))
                    {
                        $objInfoDetalleSolCaract = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                     ->findOneBy(array("detalleSolicitudId" => $arrayResultado['idSolicitud'],
                                                                                       "caracteristicaId"   => $objAdmiCaracteristica->getId()));

                        if(is_object($objInfoDetalleSolCaract))
                        {
                            $strFacturableValor = $objInfoDetalleSolCaract->getValor();
                        }
                    }   
                    
                    //Se obtiene el valor de la caracteristica Precio
                    if(is_object($objAdmiCaracteristicaP))
                    {
                        $objInfoDetalleSolCaract = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                     ->findOneBy(array("detalleSolicitudId" => $arrayResultado['idSolicitud'],
                                                                                       "caracteristicaId"   => $objAdmiCaracteristicaP->getId()));

                        if(is_object($objInfoDetalleSolCaract))
                        {
                            $strPrecioDemoValor = $objInfoDetalleSolCaract->getValor();
                        }
                    }  
                    
                    //Se obtiene el valor de la caracteristica Cambio de Equipo
                    if(is_object($objAdmiCaractCambioEquipo))
                    {
                        $objInfoDetalleSolCaract = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                     ->findOneBy(array("detalleSolicitudId" => $arrayResultado['idSolicitud'],
                                                                                       "caracteristicaId"   => $objAdmiCaractCambioEquipo->getId()));

                        if(is_object($objInfoDetalleSolCaract))
                        {
                            $strCambioEquipoDemo = $objInfoDetalleSolCaract->getValor();
                        }
                    }                        
                }

                $objProducto = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($arrayParametros['productoId']);
                $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($arrayParametros['idServicio']);
                
                //Capacidades totales de los servicios activos ligados a un puerto
                $arrayCapacidades = $this->emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")
                                                            ->getResultadoCapacidadesPorInterface($arrayParametros['interfaceElementoId']);
                
                //Se resta la capacidad actual del servicio/producto de la capacidad total del sw
                $bwUp   = intval($arrayCapacidades['totalCapacidad1']) - intval($arrayParametros['capacidadUno']);
                $bwDown = intval($arrayCapacidades['totalCapacidad2']) - intval($arrayParametros['capacidadDos']);                               
                
                //accion a ejecuta        
                $arrayPeticiones['url']          = 'configBW';
                $arrayPeticiones['accion']       = 'reconectar';
                $arrayPeticiones['id_servicio']  = $objServicio->getId();
                $arrayPeticiones['nombreAccionBw'] = 'cambio_plan';
                $arrayPeticiones['nombreMetodo'] = 'InfoCambiarPlanService.cambioPlanTn';
                $arrayPeticiones['sw']           = $arrayParametros['elementoNombre'];                
                $arrayPeticiones['user_name']    = $arrayParametros['usrCreacion'];
                $arrayPeticiones['user_ip']      = $arrayParametros['ipCreacion'];                
                $arrayPeticiones['bw_up']        = intval($bwUp)   + intval($arrayResultado['capacidadUnoNueva']);
                $arrayPeticiones['bw_down']      = intval($bwDown) + intval($arrayResultado['capacidadDosNueva']);
                $arrayPeticiones['servicio']     = $arrayParametros['descripcionProducto'];
                $arrayPeticiones['login_aux']    = $arrayParametros['loginAux'];                
                $arrayPeticiones['pto']          = $arrayParametros['interfaceElementoNombre'];  
                $arrayPeticiones['anillo']       = $arrayParametros['anillo']; 
                $intCapServTotalExtremo = $arrayPeticiones['bw_up'] >= $arrayPeticiones['bw_down'] ? 
                                          $arrayPeticiones['bw_up'] : $arrayPeticiones['bw_down'];
                
                if($objProducto->getDescripcionProducto() != 'INTERNET WIFI')
                {
                    //Ejecucion del metodo via WS para realizar la configuracion del SW
                    $arrayRespuestaService = $this->networkingScripts->callNetworkingWebService($arrayPeticiones);
                    
                    $strStatus  = $arrayRespuestaService['status'];
                    $strMensaje = $arrayRespuestaService['mensaje'];
                    $statusCode = $arrayRespuestaService['statusCode'];         
                }
                else
                {
                    $strStatus = "OK";
                }                
                
                $objDetalleSolicitud = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                         ->find($arrayResultado['idSolicitud']);

                $arrayParametros['idSolicitudPadre'] = $arrayResultado['idSolicitudPadre'];
                
                if($strStatus == "OK")
                {      
                    //Si configuro en el SWITCH  busca si tiene concentrador
                    //validacion de concentrador
                    $objSpcEnlaceDatos = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "ENLACE_DATOS", $objProducto);
                    
                    if(is_object($objSpcEnlaceDatos))
                    {                        
                        if($objProducto->getDescripcionProducto() == 'INTERNET WIFI')
                        {
                            
                            if(is_object($objProducto))
                            {
                                $objSpcConCapacidad1 = $this->servicioGeneral
                                                            ->getServicioProductoCaracteristica($objServicio, "CAPACIDAD1", $objProducto);
                            }

                            if(is_object($objSpcConCapacidad1))
                            {
                                $intBwUp = $objSpcConCapacidad1->getValor();
                            }
                            else
                            {
                                $result[] = array("status" => "ERROR",
                                                  "mensaje" => "No existen capacidades del concentrador. ");
                                return $result;
                            }
                        
                            //Capcidades mas nueva capacidad a configurar
                            $intCapacidadNueva =  (intval($arrayResultado['capacidadUnoNueva'])) - intval($intBwUp) ;
                            
                            if($intCapacidadNueva > 0)
                            {
                                $strOperacion = 'SUMA';
                            }
                            else
                            {
                                $strOperacion = 'RESTA';
                            }
                            
                            //cambio el anchos de banda de los concentradores
                            $arrayCambioBw = array();

                            $arrayCambioBw['objServicio']       = $objServicio;
                            $arrayCambioBw['intCapacidadNueva'] = abs($intCapacidadNueva);
                            $arrayCambioBw['strOperacion']      = $strOperacion;
                            $arrayCambioBw['usrCreacion']       = $arrayParametros['usrCreacion'];
                            $arrayCambioBw['ipCreacion']        = $arrayParametros['ipCreacion'];

                            $this->serviceWifi->cambioAnchoBanda($arrayCambioBw);
                        }
                        else
                        {                            
                            $objServicioConcentrador    = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                            ->find(intval($objSpcEnlaceDatos->getValor()));
                            if($objServicioConcentrador)
                            {                                          
                                $objServicioTecConcentrador = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                                                ->findOneBy(array("servicioId" => $objServicioConcentrador->getId()));
                                
                                if(is_object($objServicioTecConcentrador) &&
                                   $objServicioTecConcentrador->getElementoId() && 
                                   $objServicioTecConcentrador->getInterfaceElementoId() )
                                {
                                    $objElementoConcentrador = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                                       ->find($objServicioTecConcentrador->getElementoId());
                                    
                                    $objInterfaceElementoConcentrador = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                             ->find($objServicioTecConcentrador->getInterfaceElementoId());

                                    $objProductoConcentrador = $this->emInfraestructura->getRepository("schemaBundle:AdmiProducto")
                                                                                       ->find($objServicioConcentrador->getProductoId());

                                    //Debe existir tanto el elemento concentrador ( SW ) y el producto que hace referencia al concentrador para
                                    //poder configurar correctamente
                                    if($objElementoConcentrador && $objProductoConcentrador)
                                    {                                                   
                                        //Se valida si el servicio tiene enlazado un concentrador propiamente dicho para poder configurar
                                        if($objProductoConcentrador->getEsConcentrador()=='SI' || 
                                           $objProducto->getDescripcionProducto()== 'INTERNET WIFI')
                                        {                                          
                                            $objSpcConCapacidad1 = $this->servicioGeneral
                                                                        ->getServicioProductoCaracteristica($objServicioConcentrador, 
                                                                                                           "CAPACIDAD1", 
                                                                                                           $objProductoConcentrador);

                                            $objSpcConCapacidad2 = $this->servicioGeneral
                                                                        ->getServicioProductoCaracteristica($objServicioConcentrador, 
                                                                                                           "CAPACIDAD2", 
                                                                                                           $objProductoConcentrador);

                                            $objDetalleAnillo = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                     ->findOneBy(array(  "elementoId"    => $objElementoConcentrador->getId(),
                                                                                         "detalleNombre" => "ANILLO",
                                                                                         "estado"        => "Activo"));

                                            if(!$objDetalleAnillo)
                                            {
                                                $arrayFinal[] = array('status'     =>"ERROR", 
                                                                      'mensaje'    =>'Switch '.$objElementoConcentrador->getNombreElemento().' '
                                                                                     . 'no tiene informacion de ANILLO',
                                                                      'statusCode' => 500);

                                                $arrayParametros['objDetalleSolicitud'] = $objDetalleSolicitud;
                                                $arrayParametros['mensajeError']        = $arrayFinal[0]['mensaje'];
                                                $this->servicioGeneral->insertarHistorialSolicitudError($arrayParametros);

                                                return $arrayFinal;
                                            }

                                            //Se valida si las capacidades son correctas ( caracteristica ligada al servicio )
                                            if($objSpcConCapacidad1 && $objSpcConCapacidad2)
                                            {                                            
                                                //Cambiando las capacidades del concentrador                            
                                                $bwConcentradorAnteriorUp   = $objSpcConCapacidad1->getValor();
                                                $bwConcentradorAnteriorDown = $objSpcConCapacidad2->getValor();                               

                                                $arrayCapacidadesPorConcentrador = 
                                                         $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                              ->getArrayCapacidadesPorConcentrador($objServicioConcentrador->getId());

                                                $arrayCapacidadesConcentrador = $this->emInfraestructura
                                                                                     ->getRepository("schemaBundle:InfoInterfaceElemento")
                                                                                     ->getResultadoCapacidadesPorInterface(
                                                                                       $objInterfaceElementoConcentrador->getId());

                                                $intCapacidadUpExtremos       = intval($arrayCapacidadesPorConcentrador['totalCapacidadUno']) - 
                                                                                intval($arrayParametros['capacidadUno']);
                                                $intCapacidadDownExtremos     = intval($arrayCapacidadesPorConcentrador['totalCapacidadDos']) - 
                                                                                intval($arrayParametros['capacidadDos']);

                                                $intCapacidadUpExtremos       = intval($intCapacidadUpExtremos)   + 
                                                                                intval($arrayResultado['capacidadUnoNueva']);
                                                $intCapacidadDownExtremos     = intval($intCapacidadDownExtremos) + 
                                                                                intval($arrayResultado['capacidadDosNueva']);

                                                //Total de Servicios que usan la misma Ultima Milla
                                                /* Se realiza un ajuste para evitar que se registren valores negativos. */
                                                $intCapacidadUpExtremosTotal       = ($arrayCapacidadesConcentrador['totalCapacidad1'] -
                                                                                     intval($bwConcentradorAnteriorUp)) < 0 ? 0 :
                                                                                     $arrayCapacidadesConcentrador['totalCapacidad1'] -
                                                                                     intval($bwConcentradorAnteriorUp);

                                                /* Se realiza un ajuste para evitar que se registren valores negativos. */
                                                $intCapacidadDownExtremosTotal     = ($arrayCapacidadesConcentrador['totalCapacidad2'] -
                                                                                     intval($bwConcentradorAnteriorDown)) < 0 ? 0:
                                                                                     $arrayCapacidadesConcentrador['totalCapacidad2'] -
                                                                                     intval($bwConcentradorAnteriorDown);

                                                $intCapacidadUpExtremosTotal       = intval($intCapacidadUpExtremosTotal)   + 
                                                                                     intval($intCapacidadUpExtremos);
                                                $intCapacidadDownExtremosTotal     = intval($intCapacidadDownExtremosTotal) + 
                                                                                     intval($intCapacidadDownExtremos);

                                                $arrayPeticionesBw              = array();
                                                $arrayPeticionesBw['url']       = 'configBW';
                                                $arrayPeticionesBw['accion']    = 'Activar';
                                                $arrayPeticionesBw['id_servicio'] = $objServicioConcentrador->getId();
                                                $arrayPeticionesBw['nombreMetodo'] = 'InfoCambiarPlanService.cambioPlanTn';
                                                $arrayPeticionesBw['nombreAccionBw']  = 'cambio_plan';
                                                $arrayPeticionesBw['loginAuxExtremo'] = $objServicio->getLoginAux();
                                                $arrayPeticionesBw['bwAuxExtremo']    = $intCapServTotalExtremo;
                                                $arrayPeticionesBw['sw']        = $objElementoConcentrador->getNombreElemento();
                                                $arrayPeticionesBw['pto']       = $objInterfaceElementoConcentrador->getNombreInterfaceElemento();
                                                $arrayPeticionesBw['anillo']    = $objDetalleAnillo->getDetalleValor();
                                                $arrayPeticionesBw['bw_up']     = $intCapacidadUpExtremosTotal;
                                                $arrayPeticionesBw['bw_down']   = $intCapacidadDownExtremosTotal;
                                                $arrayPeticionesBw['servicio']  = $objServicioConcentrador->getProductoId()->getNombreTecnico();
                                                $arrayPeticionesBw['login_aux'] = $objServicioConcentrador->getLoginAux();
                                                $arrayPeticionesBw['user_name'] = $arrayParametros['usrCreacion'];
                                                $arrayPeticionesBw['user_ip']   = $arrayParametros['ipCreacion'];

                                                //Ejecucion del metodo via WS para realizar la configuracion del SW
                                                $arrayRespuestaBw = $this->networkingScripts->callNetworkingWebService($arrayPeticionesBw);

                                                $status  = $arrayRespuestaBw['status'];                            

                                                //Se actualiza las caracteristicas de capacidades del concentrador
                                                if($status == "OK")
                                                {
                                                    //Se actualiza las nuevas capacidades al servicio
                                                    $objSpcConCapacidad1->setEstado("Eliminado");
                                                    $objSpcConCapacidad1->setUsrUltMod($arrayParametros['usrCreacion']);
                                                    $objSpcConCapacidad1->setFeUltMod(new \DateTime('now'));
                                                    $this->emComercial->persist($objSpcConCapacidad1);
                                                    $this->emComercial->flush();

                                                    $objSpcConCapacidad2->setEstado("Eliminado");
                                                    $objSpcConCapacidad2->setUsrUltMod($arrayParametros['usrCreacion']);
                                                    $objSpcConCapacidad2->setFeUltMod(new \DateTime('now'));
                                                    $this->emComercial->persist($objSpcConCapacidad2);
                                                    $this->emComercial->flush();

                                                    //ingresar las nuevas caracteristicas del concentrador
                                                    $arrayParams['entityAdmiProducto'] = $objServicioConcentrador->getProductoId();
                                                    $arrayParams['entityInfoServicio'] = $objServicioConcentrador;
                                                    $arrayParams['strEstado']          = 'Activo';
                                                    $arrayParams['strUsrCreacion']     = $arrayParametros['usrCreacion'];

                                                    $arrayParams['strCaracteristica']  = 'CAPACIDAD1';                    
                                                    $arrayParams['strValor']           = $intCapacidadUpExtremos;
                                                    $objServProdCaractCap1 = $this->servicioGeneral->insertarInfoServicioProdCaract($arrayParams);
                                                    $this->emComercial->persist($objServProdCaractCap1);
                                                    $this->emComercial->flush();

                                                    $arrayParams['strCaracteristica']  = 'CAPACIDAD2';                    
                                                    $arrayParams['strValor']           = $intCapacidadDownExtremos;
                                                    $objServProdCaractCap2 = $this->servicioGeneral->insertarInfoServicioProdCaract($arrayParams);
                                                    $this->emComercial->persist($objServProdCaractCap2);
                                                    $this->emComercial->flush();

                                                    $strObservacionConcentrador =
                                                                    "<b>Cambio de Velocidad Realizado en Concentrador:</b>".
                                                                    "<br>Elemento: ".$objElementoConcentrador->getNombreElemento().
                                                                    "<br>Puerto  : ".$objInterfaceElementoConcentrador->getNombreInterfaceElemento().
                                                                    "<br> Velocidad Up anterior  :" . $bwConcentradorAnteriorUp.
                                                                    "<br> Velocidad Down anterior:" . $bwConcentradorAnteriorDown.
                                                                    "<br> Velocidad Up Nuevo  :" . $intCapacidadUpExtremos.
                                                                    "<br> Velocidad Down Nuevo:" . $intCapacidadDownExtremos;

                                                    //historial del servicio
                                                    $servicioHistorial = new InfoServicioHistorial();
                                                    $servicioHistorial->setServicioId($objServicioConcentrador);
                                                    $servicioHistorial->setObservacion(
                                                                       $strObservacionConcentrador.  
                                                                       "<br><b>".$strMensajeHistorialServ."Cambio de Plan Realizado en Extremo:</b>".
                                                                       "<br>Login:<b>".$arrayParametros['loginAux']."</b>".
                                                                       "<br>Elemento: ".$arrayParametros['elementoNombre'].
                                                                       "<br>Puerto  : ".$arrayParametros['interfaceElementoNombre']. 
                                                                       "<br> Velocidad Up anterior  :" . $arrayParametros['capacidadUno'].
                                                                       "<br> Velocidad Down anterior:" . $arrayParametros['capacidadDos'].
                                                                       "<br> Velocidad Up Nuevo  :" . $arrayResultado['capacidadUnoNueva'].
                                                                       "<br> Velocidad Down Nuevo:" . $arrayResultado['capacidadDosNueva']
                                                                       );
                                                    $servicioHistorial->setEstado($objServicioConcentrador->getEstado());
                                                    $servicioHistorial->setUsrCreacion($arrayParametros['usrCreacion']);
                                                    $servicioHistorial->setFeCreacion(new \DateTime('now'));
                                                    $servicioHistorial->setIpCreacion($arrayParametros['ipCreacion']);                    
                                                    $this->emComercial->persist($servicioHistorial);
                                                    $this->emComercial->flush();
                                                    
                                                    if(is_object($objCaractCapac1Anterior) && is_object($objCaractCapac2Anterior) && 
                                                       is_object($objDetalleSolicitud) && $arrayParametros["tipoProceso"] == "Demos")
                                                    {

                                                        $strEstadoActualSol = $objDetalleSolicitud->getEstado();

                                                        $objInfoDetalleSolicitud = $this->emComercial
                                                                                        ->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                                        ->findOneBy(array("detalleSolicitudId" 
                                                                                                            => $objDetalleSolicitud->getId(),
                                                                                                          "caracteristicaId"   
                                                                                                            => $objCaractCapac1Anterior->getId()));

                                                        if(!is_object($objInfoDetalleSolicitud))
                                                        {                                                    
                                                            //Capacidad 1
                                                            $objInfoDetalleSolCaract = new InfoDetalleSolCaract();
                                                            $objInfoDetalleSolCaract->setCaracteristicaId($objCaractCapac1Anterior);
                                                            $objInfoDetalleSolCaract->setValor($arrayParametros['capacidadUno']);
                                                            $objInfoDetalleSolCaract->setDetalleSolicitudId($objDetalleSolicitud);
                                                            $objInfoDetalleSolCaract->setEstado($strEstadoActualSol);
                                                            $objInfoDetalleSolCaract->setUsrCreacion($arrayParametros['usrCreacion']);
                                                            $objInfoDetalleSolCaract->setFeCreacion(new \DateTime('now'));                  
                                                            $this->emComercial->persist($objInfoDetalleSolCaract);
                                                            $this->emComercial->flush();

                                                            //Capacidad 2
                                                            $objInfoDetalleSolCaract = new InfoDetalleSolCaract();
                                                            $objInfoDetalleSolCaract->setCaracteristicaId($objCaractCapac2Anterior);
                                                            $objInfoDetalleSolCaract->setValor($arrayParametros['capacidadDos']);
                                                            $objInfoDetalleSolCaract->setDetalleSolicitudId($objDetalleSolicitud);
                                                            $objInfoDetalleSolCaract->setEstado($strEstadoActualSol);
                                                            $objInfoDetalleSolCaract->setUsrCreacion($arrayParametros['usrCreacion']);
                                                            $objInfoDetalleSolCaract->setFeCreacion(new \DateTime('now'));                  
                                                            $this->emComercial->persist($objInfoDetalleSolCaract);
                                                            $this->emComercial->flush();       
                                                        }
                                                    }                                                    
                                                }
                                                else
                                                {
                                                    //historial del servicio cuando ejecucion en concentrador falla
                                                    $servicioHistorial = new InfoServicioHistorial();
                                                    $servicioHistorial->setServicioId($objServicioConcentrador);
                                                    $servicioHistorial->setObservacion("Fallo en Ejecucion de WS para Concentrador en"
                                                                                     . " Cambio Plan de ".
                                                                                       "<b>".$arrayParametros['loginAux']."</b>"
                                                                                     . "<br><b>ERROR:</b><br>".$arrayRespuestaBw['mensaje']);
                                                    $servicioHistorial->setEstado($objServicioConcentrador->getEstado());
                                                    $servicioHistorial->setUsrCreacion($arrayParametros['usrCreacion']);
                                                    $servicioHistorial->setFeCreacion(new \DateTime('now'));
                                                    $servicioHistorial->setIpCreacion($arrayParametros['ipCreacion']);                    
                                                    $this->emComercial->persist($servicioHistorial);
                                                    $this->emComercial->flush(); 
                                                }
                                            }                                  
                                            else
                                            {
                                                $arrayFinal[] = array('status'     => "ERROR",
                                                                      'mensaje'    => 'Informacion de Capacidades de <b>Concentrador</b>'
                                                                                      . ' Incorrectas o Inexistentes',
                                                                      'statusCode' => 404);

                                                $arrayParametros['objDetalleSolicitud'] = $objDetalleSolicitud;
                                                $arrayParametros['mensajeError'] = $arrayFinal[0]['mensaje'];
                                                $this->servicioGeneral->insertarHistorialSolicitudError($arrayParametros);

                                                return $arrayFinal;
                                            }
                                        }
                                        else
                                        {
                                            $arrayFinal[] = array('status'     =>"ERROR", 
                                                                  'mensaje'    =>'Servicio no se encuentra enlazado a un '
                                                                               . '<b>Concentrador</b> correcto',
                                                                  'statusCode' => 500);

                                            $arrayParametros['objDetalleSolicitud'] = $objDetalleSolicitud;
                                            $arrayParametros['mensajeError']        = $arrayFinal[0]['mensaje'];
                                            $this->servicioGeneral->insertarHistorialSolicitudError($arrayParametros);

                                            return $arrayFinal;
                                        }
                                    }
                                }
                                else
                                {
                                    $arrayParametros['estado']              = "EnProceso";
                                    $arrayParametros['objDetalleSolicitud'] = $objDetalleSolicitud;
                                    $arrayParametros['mensajeError']        = "Concentrador sin data tecnica. "
                                                                            . "Imposible subir ancho de banda en concentrador.";
                                    $this->servicioGeneral->insertarHistorialSolicitudError($arrayParametros);
                                }
                            }     
                        }
                    }
                    else
                    {
                        if($objProducto->getDescripcionProducto() == 'INTERNET WIFI')
                        {
                            $arrayFinal[] = array('status'      => "ERROR",
                                                  'mensaje'     => 'El servicio wifi no tiene caracteristica ENLACE DATOS',
                                                  'statusCode'  => 500);

                            $arrayParametros['objDetalleSolicitud'] = $objDetalleSolicitud;
                            $arrayParametros['mensajeError']        = $arrayFinal[0]['mensaje'];
                            $this->servicioGeneral->insertarHistorialSolicitudError($arrayParametros);

                            return $arrayFinal;
                        }
                    }
                    
                    $objServProdCaractCap1 = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio,'CAPACIDAD1',$objProducto);
                    $objServProdCaractCap2 = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio,'CAPACIDAD2',$objProducto);  
                    
                    $strLoginPrincipal    = "";
                    $boolEsPrincipal      = true;
                    $strCabeceraHistorial = "<b>Cambio de Plan Realizado en Extremo:</b>";
                    
                    if(isset($arrayParametros['tipoEnlace']) && $arrayParametros['tipoEnlace'] == 'BACKUP')                 
                    {
                        //Si es Backup, buscar el Principal para referenciar que el cambio de Plan se realiza en el backup
                        $objServCaractPrincipal = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio,'ES_BACKUP',$objProducto);
                        
                        if(is_object($objServCaractPrincipal))
                        {
                            $objServicioPrincipal = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                      ->find($objServCaractPrincipal->getValor());
                            if(is_object($objServicioPrincipal))
                            {
                                $boolEsPrincipal      = false;
                                $strLoginPrincipal    = $objServicioPrincipal->getLoginAux();
                                $strCabeceraHistorial = "<b>Cambio de Plan Realizado por acción en Servicio Principal : "
                                                      . "<label style='color:green;'>".$strLoginPrincipal."</label></b>";
                            }
                        }
                    }

                    //Si no existen capacidades no se puede realizar los cambios de plan 
                    if($objServProdCaractCap1 && $objServProdCaractCap2)
                    {     
                        //historial del servicio
                        if(($arrayParametros["tipoProceso"] == "Demos" || $arrayParametros["tipoProceso"] == "CancelDemos"))
                        {
                            //Se valida que exista registrado el historial del servicio
                            if(is_object($objCaractReferencia))
                            {
                                $objInfoDetalleSolCaract = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                ->findOneBy(array("detalleSolicitudId" => $arrayResultado['idSolicitud'],
                                                                                  "valor"              => $strMensajeHistorialServ,
                                                                                  "caracteristicaId"   => $objCaractReferencia->getId()));

                                if(is_object($objInfoDetalleSolCaract))
                                {
                                    $strInsertaHistorial = "N";
                                }
                            }
                        }

                        if($strInsertaHistorial == "S")
                        {
                            //Se valida que el servicio posea el esquema Concentrador - Extremo
                            $objEsquemaConcentExtremo = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio,
                                                                                                                  "ENLACE_DATOS",
                                                                                                                  $objProducto);

                            if(is_object($objEsquemaConcentExtremo))
                            {
                                $strMsgHistorialEstremo = "Cambio de Plan Realizado en Extremo:</b>";
                            }
                            else
                            {
                                $strMsgHistorialEstremo = "Cambio de Plan Realizado:</b>";
                            }

                            $servicioHistorial = new InfoServicioHistorial();
                            $servicioHistorial->setServicioId($objServicio);

                            $strObservacionHistorial = "<b>".$strMensajeHistorialServ.$strMsgHistorialEstremo.
                                                       "<br>Elemento: ".$arrayParametros['elementoNombre'].
                                                       "<br>Puerto  : ".$arrayParametros['interfaceElementoNombre'].
                                                       "<br> Velocidad Up anterior  :" . $arrayParametros['capacidadUno'].
                                                       "<br> Velocidad Down anterior:" . $arrayParametros['capacidadDos'].
                                                       "<br> Velocidad Up Nuevo  :" . $arrayResultado['capacidadUnoNueva'].
                                                       "<br> Velocidad Down Nuevo:" . $arrayResultado['capacidadDosNueva'];

                            if($arrayParametros["tipoProceso"] != "Demos" && $arrayParametros["tipoProceso"] != "CancelDemos"
                                && $boolEsPrincipal)
                            {
                                $strObservacionHistorial .= "<br> Precio anterior: ".$arrayResultado['precio'].
                                                            "<br> Precio Nuevo   : ".$arrayResultado['precioNuevo'];
                            }

                            if($arrayParametros["tipoProceso"] == "Demos" || $arrayParametros["tipoProceso"] == "CancelDemos")
                            {
                                if($arrayParametros["tipoProceso"] == "Demos")
                                {
                                    $strFechaInicio = strval(date_format(new \DateTime('now'), "d-m-Y"));
                                    $strFechaFin    = date('d-m-Y', strtotime("$strFechaInicio + ".$arrayFechasDemo["intDiasMaximoSolDemo"]." day"));
                                }
                                else
                                {
                                    $strFechaInicio = $arrayFechasDemo["strFechaInicio"];
                                    $strFechaFin    = $arrayFechasDemo["strFechaFin"];
                                }

                                $strObservacionHistorial .= "<br> Inicio del Demo: ".$strFechaInicio;
                                $strObservacionHistorial .= "<br> Fin del Demo: ".$strFechaFin;
                                $strObservacionHistorial .= "<br> Numero de dias del Demo: ".$arrayFechasDemo["intDiasMaximoSolDemo"];

                                if($strFacturableValor == "SI")
                                {
                                    $strObservacionHistorial .= "<br> Precio del Demo: $".$strPrecioDemoValor;
                                }
                                else
                                {
                                    $strObservacionHistorial .= "<br> Precio del Demo: $0";
                                }
                            }

                            $strObservacionHistorial .= "<br>". $strObservacionConcentrador;

                            $servicioHistorial->setObservacion("".$strObservacionHistorial);
                            $servicioHistorial->setEstado($objServicio->getEstado());
                            $servicioHistorial->setUsrCreacion($arrayParametros['usrCreacion']);
                            $servicioHistorial->setFeCreacion(new \DateTime('now'));
                            $servicioHistorial->setIpCreacion($arrayParametros['ipCreacion']);
                            $this->emComercial->persist($servicioHistorial);
                            $this->emComercial->flush();

                            //Se registra la caracteristica de la referencia del historial
                            $objInfoDetalleSolCaract = new InfoDetalleSolCaract();
                            $objInfoDetalleSolCaract->setCaracteristicaId($objCaractReferencia);
                            $objInfoDetalleSolCaract->setValor($strMensajeHistorialServ);
                            $objInfoDetalleSolCaract->setDetalleSolicitudId($objDetalleSolicitud);
                            $objInfoDetalleSolCaract->setEstado($strEstadoActualSol);
                            $objInfoDetalleSolCaract->setUsrCreacion($arrayParametros['usrCreacion']);
                            $objInfoDetalleSolCaract->setFeCreacion(new \DateTime('now'));
                            $this->emComercial->persist($objInfoDetalleSolCaract);
                            $this->emComercial->flush();
                        }

                        if(is_object($objCaractCapac1Anterior) && is_object($objCaractCapac2Anterior) && 
                           is_object($objDetalleSolicitud) && $arrayParametros["tipoProceso"] == "Demos")
                        {

                            $strEstadoActualSol = $objDetalleSolicitud->getEstado();
                            
                            
                            $objInfoDetalleSolicitud = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                            ->findOneBy(array("detalleSolicitudId" 
                                                                                => $objDetalleSolicitud->getId(),
                                                                              "caracteristicaId"   
                                                                                => $objCaractCapac1Anterior->getId()));

                            if(!is_object($objInfoDetalleSolicitud))
                            {                              
                                //Capacidad 1
                                $objInfoDetalleSolCaract = new InfoDetalleSolCaract();
                                $objInfoDetalleSolCaract->setCaracteristicaId($objCaractCapac1Anterior);
                                $objInfoDetalleSolCaract->setValor($arrayParametros['capacidadUno']);
                                $objInfoDetalleSolCaract->setDetalleSolicitudId($objDetalleSolicitud);
                                $objInfoDetalleSolCaract->setEstado($strEstadoActualSol);
                                $objInfoDetalleSolCaract->setUsrCreacion($arrayParametros['usrCreacion']);
                                $objInfoDetalleSolCaract->setFeCreacion(new \DateTime('now'));                  
                                $this->emComercial->persist($objInfoDetalleSolCaract);
                                $this->emComercial->flush();

                                //Capacidad 2
                                $objInfoDetalleSolCaract = new InfoDetalleSolCaract();
                                $objInfoDetalleSolCaract->setCaracteristicaId($objCaractCapac2Anterior);
                                $objInfoDetalleSolCaract->setValor($arrayParametros['capacidadDos']);
                                $objInfoDetalleSolCaract->setDetalleSolicitudId($objDetalleSolicitud);
                                $objInfoDetalleSolCaract->setEstado($strEstadoActualSol);
                                $objInfoDetalleSolCaract->setUsrCreacion($arrayParametros['usrCreacion']);
                                $objInfoDetalleSolCaract->setFeCreacion(new \DateTime('now'));                  
                                $this->emComercial->persist($objInfoDetalleSolCaract);
                                $this->emComercial->flush();  
                            }
                        }                        

                        /**
                         * Se crea historial respectivo para poder facturar el proporcional del servicio por el cambio de plan
                         */
                        $floatAnteriorPrecio = $arrayResultado['precio'] ? $arrayResultado['precio'] : 0;
                        
                        if($arrayParametros["tipoProceso"] == "Demos" || $arrayParametros["tipoProceso"] == "CancelDemos")
                        {
                            $arrayResultado['precioNuevo'] = $arrayResultado['precio'];
                        }
                        
                        $floatNuevoPrecio    = $arrayResultado['precioNuevo'] ? $arrayResultado['precioNuevo'] : 0;
                        
                        if( floatval($floatNuevoPrecio) > floatval($floatAnteriorPrecio) )
                        {
                            $objServicioHistorial = new InfoServicioHistorial();
                            $objServicioHistorial->setServicioId($objServicio);
                            $objServicioHistorial->setObservacion("Precio anterior: ".$floatAnteriorPrecio);
                            $objServicioHistorial->setAccion("confirmoCambioPrecio");
                            $objServicioHistorial->setEstado($objServicio->getEstado());
                            $objServicioHistorial->setUsrCreacion($arrayParametros['usrCreacion']);
                            $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                            $objServicioHistorial->setIpCreacion($arrayParametros['ipCreacion']);
                            $this->emComercial->persist($objServicioHistorial);
                            $this->emComercial->flush();
                        }

                        //Solo se realiza cambio de Precio en enlace PRINCIPAL en Cambio de Plan
                        if($boolEsPrincipal)
                        {
                            //Se edita servicio colocando nuevo precio
                            $objServicio->setPrecioVenta($arrayResultado['precioNuevo']);
                            $strNuevaDescripcionAnterior = $objServicio->getDescripcionPresentaFactura();
                            if(!empty($strNuevaDescripcionAnterior))
                            {
                                $strNuevaDescripcionNueva = str_replace($arrayParametros['capacidadUno'], $arrayResultado['capacidadUnoNueva'], $strNuevaDescripcionAnterior);
                                $objServicio->setDescripcionPresentaFactura($strNuevaDescripcionNueva);
                            }
                            $this->emComercial->persist($objServicio);
                            $this->emComercial->flush();
                        }

                        //Se actualiza las nuevas capacidades al servicio                                                                                                       
                        $objServProdCaractCap1->setEstado("Eliminado");
                        $objServProdCaractCap1->setUsrUltMod($arrayParametros['usrCreacion']);
                        $objServProdCaractCap1->setFeUltMod(new \DateTime('now'));
                        $this->emComercial->persist($objServProdCaractCap1);
                        $this->emComercial->flush();

                        $objServProdCaractCap2->setEstado("Eliminado");
                        $objServProdCaractCap2->setUsrUltMod($arrayParametros['usrCreacion']);
                        $objServProdCaractCap2->setFeUltMod(new \DateTime('now'));
                        $this->emComercial->persist($objServProdCaractCap2);
                        $this->emComercial->flush();
                        
                         //ingresar las nuevas caracteristicas
                        $arrayParams['entityAdmiProducto'] = $objProducto;
                        $arrayParams['entityInfoServicio'] = $objServicio;
                        $arrayParams['strEstado']          = 'Activo';
                        $arrayParams['strUsrCreacion']     = $arrayParametros['usrCreacion'];

                        $arrayParams['strCaracteristica']  = 'CAPACIDAD1';                    
                        $arrayParams['strValor']           = $arrayResultado['capacidadUnoNueva'];                                                            
                        $objServProdCaractCap1 = $this->servicioGeneral->insertarInfoServicioProdCaract($arrayParams);
                        $this->emComercial->persist($objServProdCaractCap1);
                        $this->emComercial->flush();

                        $arrayParams['strCaracteristica']  = 'CAPACIDAD2';                    
                        $arrayParams['strValor']           = $arrayResultado['capacidadDosNueva'];                                                            
                        $objServProdCaractCap2 = $this->servicioGeneral->insertarInfoServicioProdCaract($arrayParams);
                        $this->emComercial->persist($objServProdCaractCap2);
                        $this->emComercial->flush();
                        
                        //Si la solicitud es de Demos el estado del detalle de la solicitud queda como Activa
                        if($arrayParametros["tipoProceso"] == "Demos")
                        {
                            //Se deja la solicitud de Demos como activa
                            $objDetalleSolicitud->setEstado("Activa");

                            //*************Se crea Solicitud para que se genere la PRE-FACTURA*************//
                            if($strFacturableValor == "SI")
                            {
                                $objAdmiTipoSolicitud = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                             ->findOneBy(array("descripcionSolicitud" => "SOLICITUD REQUERIMIENTOS DE CLIENTES"));

                                if(is_object($objAdmiTipoSolicitud) && is_object($objServicio))
                                {
                                    $arrayParametrosSol["strUsrCreacion"]  = "telcos_pma_demos";
                                    $arrayParametrosSol["strEstado"]       = "Pendiente";
                                    $arrayParametrosSol["intServicioId"]   = $objServicio->getId();

                                    $intSolicitudesReqCliente = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                                  ->getSolicitudesReqDeClientes($arrayParametrosSol);

                                    //Solo si el servicio no tiene solicitudes de requerimiento de clientes Pendiente se crea el registro
                                    if($intSolicitudesReqCliente == 0)
                                    {
                                        $objAdmiMotivo = $this->emGeneral->getRepository('schemaBundle:AdmiMotivo')
                                                                         ->findOneBy(array("nombreMotivo" => "DEMO"));

                                        //Se crea Historial en estado Pendiente
                                        $objDetalleSol = new InfoDetalleSolicitud();
                                        $objDetalleSol->setServicioId($objServicio);
                                        $objDetalleSol->setTipoSolicitudId($objAdmiTipoSolicitud);
                                        $objDetalleSol->setFeCreacion(new \DateTime('now'));
                                        $objDetalleSol->setUsrCreacion('telcos_pma_demos');
                                        $objDetalleSol->setPrecioDescuento($strPrecioDemoValor);

                                        if(is_object($objAdmiMotivo))
                                        {
                                            $objDetalleSol->setMotivoId($objAdmiMotivo->getId());
                                        }

                                        $objDetalleSol->setObservacion("Se crea solicitud por ejecucion de Demos");
                                        $objDetalleSol->setEstado("Pendiente");
                                        $this->emComercial->persist($objDetalleSol);
                                        $this->emComercial->flush();


                                        //Se crea Historial en estado Pendiente
                                        $objDetalleSolHist = new InfoDetalleSolHist();
                                        $objDetalleSolHist->setDetalleSolicitudId($objDetalleSol);
                                        $objDetalleSolHist->setEstado("Pendiente");
                                        $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
                                        $objDetalleSolHist->setUsrCreacion($arrayParametros['usrCreacion']);
                                        $objDetalleSolHist->setIpCreacion($arrayParametros['ipCreacion']);
                                        $objDetalleSolHist->setObservacion("Se crea solicitud por ejecucion de Demos");
                                        $this->emComercial->persist($objDetalleSolHist);
                                        $this->emComercial->flush();
                                    }
                                }  
                            }
                            //********Se crea Solicitud para que se genere la PRE-FACTURA********//
                            $objFormaContacto = $this->emComercial->getRepository("schemaBundle:AdmiFormaContacto")
                                                     ->findOneBy(array('descripcionFormaContacto' => 'Correo Electronico',
                                                                       'estado'                   => 'Activo')); 
                                                                                                                
                            //Se obtiene la notificacion del vendedor y del asesor comercial de la solicitud                            
                            if(is_object($objDetalleSolicitud))
                            {
                                //Correo del Asesor Comercial
                                $strAsesorComercial = $objDetalleSolicitud->getUsrCreacion();
                                                                                                
                                $objInfoPersona = $this->emComercial->getRepository("schemaBundle:InfoPersona")
                                                                    ->findOneBy(array('usrCreacion' => $strAsesorComercial));
                                
                                if(is_object($objInfoPersona) && is_object($objFormaContacto))
                                {
                                    //Se obtiene el correo de la persona asignada
                                    $objInfoPersonaFormaContacto = $this->emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                                        ->findOneBy(array('personaId'       => $objInfoPersona->getId(),
                                                                                          'formaContactoId' => $objFormaContacto->getId(),
                                                                                          'estado'          => "Activo"));   
                                    if(is_object($objInfoPersonaFormaContacto))
                                    {
                                        $strArrayDestinatarios[] = $objInfoPersonaFormaContacto->getValor();
                                    }   
                                }
                            }

                            if(is_object($objServicio))
                            {
                                $objInfoPunto = $this->emComercial->getRepository('schemaBundle:InfoPunto')
                                                                  ->find($objServicio->getPuntoId());
                                //Se obtiene el login
                                if(is_object($objInfoPunto))
                                {
                                    $strLogin = $objInfoPunto->getLogin();
                                    
                                    $objInfoPersonaEmpresaRol = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                                  ->find($objInfoPunto->getPersonaEmpresaRolId()->getId());
                                    
                                    if(is_object($objInfoPersonaEmpresaRol))
                                    {
                                        $objInfoPersona = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                                                                            ->find($objInfoPersonaEmpresaRol->getPersonaId());
                                        if(is_object($objInfoPersona))
                                        {
                                            $strCliente = $objInfoPersona->__toString();
                                        }
                                    } 
                                      
                                    //Se obtienen los contactos comerciales del punto        
                                    $arrayCorreosContactoComercialPunto = $this->emComercial->getRepository("schemaBundle:InfoPuntoContacto")
                                                                                            ->getArrayContactosPorPuntoYTipo($objInfoPunto->getId(),
                                                                                                                             "Contacto Comercial");
                        
                                    if($arrayCorreosContactoComercialPunto)
                                    {
                                        foreach($arrayCorreosContactoComercialPunto as $arrayCorreoContactoComercialPunto)
                                        {
                                            if($arrayCorreoContactoComercialPunto && !empty($arrayCorreoContactoComercialPunto['valor']))
                                            {
                                                $strArrayDestinatarios[] = $arrayCorreoContactoComercialPunto['valor'];
                                            }
                                        } 
                                    }   
                                }
                                
                                $objAdmiProducto = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                                     ->find($objServicio->getProductoId());                                
                                
                                //Se obtiene el servicio
                                if(is_object($objAdmiProducto))
                                {
                                    $strProducto = $objAdmiProducto->getDescripcionProducto();
                                }
                                
                                //Correo del Usuario Vendedor
                                $strVendedor = $objServicio->getUsrVendedor();                                
                                
                                $objInfoPersona = $this->emComercial->getRepository("schemaBundle:InfoPersona")
                                                                    ->findOneBy(array('usrCreacion' => $strVendedor));
                                
                                if(is_object($objInfoPersona) && is_object($objFormaContacto))
                                {
                                    //Se obtiene el correo de la persona asignada
                                    $objInfoPersonaFormaContacto = $this->emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                                        ->findOneBy(array('personaId'       => $objInfoPersona->getId(),
                                                                                          'formaContactoId' => $objFormaContacto->getId(),
                                                                                          'estado'          => "Activo"));   
                                    if(is_object($objInfoPersonaFormaContacto))
                                    {
                                        $strArrayDestinatarios[] = $objInfoPersonaFormaContacto->getValor();
                                    }   
                                }                                 
                            }      

                            
                            $arrayParametrosPlantilla = array('paramCliente'  => $strCliente,
                                                              'paramLogin'    => $strLogin,
                                                              'paramServicio' => $strProducto,
                                                              'paramFechaini' => $strFechaInicio,
                                                              'paramFechaFin' => $strFechaFin,
                                                              'paramDias'     => $arrayFechasDemo["intDiasMaximoSolDemo"]);

                            $this->serviceEnvioPlantilla->generarEnvioPlantilla($strAsunto,
                                                                                $strArrayDestinatarios,
                                                                                $strCodigoPLantilla,
                                                                                $arrayParametrosPlantilla,
                                                                                $strCodigoEmpresa,
                                                                                null,
                                                                                null);                            
                        }
                        else
                        {   
                            $intServicioId = "";
                            if($arrayParametros["tipoProceso"] == "CancelDemos" )
                            {
                                
                                //Se obtiene el punto
                                if(is_object($objServicio))
                                {
                                    $objInfoPunto  = $this->emComercial->getRepository('schemaBundle:InfoPunto')->find($objServicio->getPuntoId()); 
                                    $intServicioId = $objServicio->getPuntoId();
                                }

                                //Si existió cambio de equipo se crea la solicitud de Retiro de equipo
                                if($strCambioEquipoDemo == "S")
                                {
                                    //****************CREACION SOLICITUD DE RETIRO DE EQUIPO*******************//
                                    //Se ingresa el INFO_DETALLE_SOLICITUD
                                    $objSolRetiroEquipo  = new InfoDetalleSolicitud();
                                    if(is_object($objServicio))
                                    {
                                        $objSolRetiroEquipo->setServicioId($objServicio);
                                    }
                                    
                                    if(is_object($objTipoSolRetiroEquipo))
                                    {                                    
                                        $objSolRetiroEquipo->setTipoSolicitudId($objTipoSolRetiroEquipo);
                                    }
                                    $objSolRetiroEquipo->setEstado('AsignadoTarea');
                                    $objSolRetiroEquipo->setUsrCreacion($arrayParametros['usrCreacion']);
                                    $objSolRetiroEquipo->setFeCreacion(new \DateTime('now'));
                                    $this->emComercial->persist($objSolRetiroEquipo);
                                    $this->emComercial->flush();
                                    
                                    //Se ingresa el INFO_DETALLE_SOL_HIST
                                    $objSolRetiroEquipoHist = new InfoDetalleSolHist();
                                    $objSolRetiroEquipoHist->setDetalleSolicitudId($objSolRetiroEquipo);
                                    $objSolRetiroEquipoHist->setEstado($objSolRetiroEquipo->getEstado());
                                    $objSolRetiroEquipoHist->setFeCreacion(new \DateTime('now'));
                                    $objSolRetiroEquipoHist->setUsrCreacion($arrayParametros['usrCreacion']);
                                    $objSolRetiroEquipoHist->setIpCreacion($arrayParametros['ipCreacion']);
                                    $objSolRetiroEquipoHist->setObservacion("GENERACIÓN AUTOMÁTICA DE SOLICITUD RETIRO DE EQUIPO POR CANCELACIÓN "
                                                                            . "DEL DEMO");
                                    $this->emComercial->persist($objSolRetiroEquipoHist);
                                    $this->emComercial->flush();
                                    
                                    
                                    //Se obtiene la solicitud de cambio de moden inmediato
                                    if(is_object($objServicio))
                                    {
                                        $arrayParametrosCambModem["intServicioId"] = $objServicio->getId();    
                                    }

                                    $intSolCambModem = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                         ->getSolCambioModenInmediato($arrayParametrosCambModem);  
                                    
                                    
                                    //Se ingresa el INFO_DETALLE_SOL_CARACT
                                    $objSolRetiroEquipoCaract = new InfoDetalleSolCaract();
                                    
                                    if(is_object($objAdmiCaractElementoCliente))
                                    {                                        
                                        $objSolRetiroEquipoCaract->setCaracteristicaId($objAdmiCaractElementoCliente);
                                        
                                        $objInfoDetalleSolCaract = $this->emComercial
                                                                        ->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                        ->findOneBy(array("detalleSolicitudId" => $intSolCambModem,
                                                                                          "caracteristicaId"   
                                                                                           => $objAdmiCaractElementoCliente->getId()));

                                        if(is_object($objInfoDetalleSolCaract))
                                        {
                                            $intIdElementoCambioModem = $objInfoDetalleSolCaract->getValor();
                                        }                                        
                                    }
                                    $objSolRetiroEquipoCaract->setDetalleSolicitudId($objSolRetiroEquipo);
                                    $objSolRetiroEquipoCaract->setValor($intIdElementoCambioModem);
                                    $objSolRetiroEquipoCaract->setEstado("Pendiente");
                                    $objSolRetiroEquipoCaract->setUsrCreacion($arrayParametros['usrCreacion']);
                                    $objSolRetiroEquipoCaract->setFeCreacion(new \DateTime('now'));
                                    $this->emComercial->persist($objSolRetiroEquipoCaract);
                                    $this->emComercial->flush();
                                    //****************CREACION SOLICITUD DE RETIRO DE EQUIPO*******************//
                                    
                                    
                                    //**************CREACION DE TAREA DE RETIRO DE EQUIPO*****************//
                                    
                                    //Se obtiene departamento a asignar
                                    $arrayValoresParametros = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                              ->getOne('PARAMETROS PROYECTO DEMOS',
                                                                                       'COMERCIAL',
                                                                                       'DEMOS',
                                                                                       'DEPARTAMENTO_RESPONSABLE_DEMO',
                                                                                       '',
                                                                                       '',
                                                                                       '',
                                                                                       '',
                                                                                       '',
                                                                                       '');

                                    if(isset($arrayValoresParametros["valor1"]) && !empty($arrayValoresParametros["valor1"]))
                                    {
                                        $strDemoDepartamentoId = $arrayValoresParametros["valor1"];
                                    }                                     

                                    $objAdmiDepartamento = $this->emComercial->getRepository('schemaBundle:AdmiDepartamento')
                                                                             ->find($strDemoDepartamentoId);   
                                    
                                    
                                    //Se obtiene la tarea de retiro de equipo
                                    $arrayValoresParametros = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                              ->getOne('PARAMETROS PROYECTO DEMOS',
                                                                                       'COMERCIAL',
                                                                                       'DEMOS',
                                                                                       'TAREA DE RETIROS DE EQUIPO DEMO',
                                                                                       '',
                                                                                       '',
                                                                                       '',
                                                                                       '',
                                                                                       '',
                                                                                       '');

                                    if(isset($arrayValoresParametros["valor1"]) && !empty($arrayValoresParametros["valor1"]))
                                    {
                                        $strDemoTareaId = $arrayValoresParametros["valor1"];
                                    }  
                                    
                                    
                                    //Se obtiene la region a la que pertenece el servicio                                    
                                    $arrayParametrosRegion["intServicioId"] = $intServicioId;
                                    
                                    $strRegionServicio = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                           ->getRegionPorServicio($arrayParametrosRegion);
                                    
                                    if($strRegionServicio == "R1")
                                    {
                                        $strCanton = "CANTON_DEMO_GYE";
                                    }
                                    elseif($strRegionServicio == "R2")
                                    {
                                        $strCanton = "CANTON_DEMO_UIO";
                                    }
                                    
                                    
                                    //Se obtiene el canton a notificar
                                    $arrayValoresParametros = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                              ->getOne('PARAMETROS PROYECTO DEMOS',
                                                                                       'COMERCIAL',
                                                                                       'DEMOS',
                                                                                       $strCanton,
                                                                                       '',
                                                                                       '',
                                                                                       '',
                                                                                       '',
                                                                                       '',
                                                                                       '');

                                    if(isset($arrayValoresParametros["valor1"]) && !empty($arrayValoresParametros["valor1"]))
                                    {
                                        $strCanntonId = $arrayValoresParametros["valor1"];
                                    }                                      


                                    $arrayParametrosTarea["strObservacion"]         = "Tarea Automática de Retiro de Equipo por cancelacion de Demo";
                                    $arrayParametrosTarea["intTarea"]               = $strDemoTareaId;
                                    $arrayParametrosTarea["strTipoAfectado"]        = "Cliente";
                                    $arrayParametrosTarea["intSolicitudId"]         = $arrayResultado['idSolicitud'];
                                    $arrayParametrosTarea["objPunto"]               = $objInfoPunto;
                                    $arrayParametrosTarea["objDepartamento"]        = $objAdmiDepartamento;
                                    $arrayParametrosTarea["strCantonId"]            = $strCanntonId;
                                    $arrayParametrosTarea["strEmpresaCod"]          = $strCodigoEmpresa;
                                    $arrayParametrosTarea["strPrefijoEmpresa"]      = $strPrefijoEmpresa;
                                    $arrayParametrosTarea["strUsrCreacion"]         = $arrayParametros['usrCreacion'];
                                    $arrayParametrosTarea["strIpCreacion"]          = $arrayParametros['ipCreacion'];
                                    $arrayParametrosTarea["intDetalleSolId"]        = $objSolRetiroEquipo->getId();
                                    $arrayParametrosTarea["intDepartamentoOrigen"]  = $strDemoDepartamentoId;
                                    $arrayParametrosTarea["strBanderaTraslado"]     = "";

                                    $strNumeroTarea = $this->crearTareaRetiroEquipoPorDemo($arrayParametrosTarea);
                                    //**************CREACION DE TAREA DE RETIRO DE EQUIPO*****************//
                                }
                            }
                            
                            //Finalizar la solicitud de cambio de plan
                            $objDetalleSolicitud->setEstado("Finalizada");
                        }

                        $this->emComercial->persist($objDetalleSolicitud);
                        $this->emComercial->flush();  
                        
                        if($arrayParametros["tipoProceso"] == "Demos")
                        {
                            //Se activa la solicitud padre de demo, siempre y cuando todas sus hijas no esten pendientes
                            $arrayParametrosSolPendientes["intSolicitudId"] = $arrayParametros['idSolicitudPadre'];
                            $intSolicitudesPendientes = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                          ->getSolicitudesHijasDemoAbiertas($arrayParametrosSolPendientes);

                            if($intSolicitudesPendientes < 1)
                            {
                                //Se pone en estado Activa la solicitud cabecera
                                $objDetalleSolicitudPadre = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                              ->find($arrayParametros['idSolicitudPadre']);
                                if(is_object($objDetalleSolicitudPadre))
                                {
                                    $objDetalleSolicitudPadre->setEstado("Activa");
                                    $this->emComercial->persist($objDetalleSolicitudPadre);
                                    $this->emComercial->flush();

                                    //Se crea Historial
                                    $objDetalleSolsHist = new InfoDetalleSolHist();
                                    $objDetalleSolsHist->setDetalleSolicitudId($objDetalleSolicitudPadre);
                                    $objDetalleSolsHist->setEstado("Activa");
                                    $objDetalleSolsHist->setFeCreacion(new \DateTime('now'));
                                    $objDetalleSolsHist->setUsrCreacion($arrayParametros['usrCreacion']);
                                    $objDetalleSolsHist->setIpCreacion($arrayParametros['ipCreacion']);
                                    $objDetalleSolsHist->setObservacion("Se activa la solicitud padre");
                                    $this->emComercial->persist($objDetalleSolsHist);
                                    $this->emComercial->flush();
                                }
                            }
                            $intSolicitudesPendientes = 0;
                        }
                        if($arrayParametros["tipoProceso"] == "Demos")
                        {
                            $strMensaje = "Se ejecuto el Demo exitosamente";
                        }
                        elseif($arrayParametros["tipoProceso"] == "CancelDemos")
                        {
                            $strMensaje = "Se cancelo el Demo exitosamente";
                        }
                        else
                        {
                            $strMensaje = "Se realizo Cambio de Plan exitosamente";
                        }                        
                        
                        //Finalizar Solicitudes Padres                                                
                        $this->servicioGeneral->finalizarSolicitudPadrePorProcesoMasivo($arrayParametros);
                        
                        if($arrayParametros["tipoProceso"] != "Demos" && $arrayParametros["tipoProceso"] != "CancelDemos")
                        {
                            //Finalizar Solicitud de Descuento Detalle antiguas si las tiene
                            $intIdDetSolDesc = null;
                            $admiCaracteristicaEntity = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                             ->findOneBy(array("descripcionCaracteristica" => 'Referencia Solicitud Masiva'));

                            if($admiCaracteristicaEntity)
                            {
                                $objIdDetSolDesc = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                     ->findOneBy(array(
                                                                                      "caracteristicaId" => $admiCaracteristicaEntity,
                                                                                      "valor"            => $objDetalleSolicitud->getId()                                                                                  
                                                                                      ));
                                if($objIdDetSolDesc)
                                {
                                    $intIdDetSolDesc = $objIdDetSolDesc->getId();
                                }
                            }

                            // Finalizar todas las solicitudes de descuento asociadas al Servicio menos la ultima solicitud Aprobada
                            // la que esta asociada al cambio de plan ejecutado
                            $objTipoSolicitudDescuento = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                              ->findOneBy(array("descripcionSolicitud" => "SOLICITUD DESCUENTO MASIVA"));
                            if($objTipoSolicitudDescuento)
                            {
                                $arraySolicitudesDescuentoAntiguas = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                          ->findBy(array(
                                                                                        'tipoSolicitudId'   => $objTipoSolicitudDescuento,
                                                                                        'servicioId'        => $objServicio
                                                                                        ));
                                if($arraySolicitudesDescuentoAntiguas)
                                {
                                    foreach($arraySolicitudesDescuentoAntiguas as $solDescuentoDetalleAntigua)
                                    {
                                        if($solDescuentoDetalleAntigua->getId() != $intIdDetSolDesc)
                                        {
                                            //Finalizar la solicitud de cambio de plan                        
                                            $solDescuentoDetalleAntigua->setEstado("Finalizada");
                                            $this->emComercial->persist($solDescuentoDetalleAntigua);
                                            $this->emComercial->flush();

                                            //Se crea Historial de la finalizacion del detalle de la solicitud de descuento
                                            $objDetalleSolsHist = new InfoDetalleSolHist();
                                            $objDetalleSolsHist->setDetalleSolicitudId($solDescuentoDetalleAntigua);
                                            $objDetalleSolsHist->setEstado($solDescuentoDetalleAntigua->getEstado());
                                            $objDetalleSolsHist->setFeCreacion(new \DateTime('now'));
                                            $objDetalleSolsHist->setUsrCreacion($arrayParametros['usrCreacion']);
                                            $objDetalleSolsHist->setIpCreacion($arrayParametros['ipCreacion']);
                                            $objDetalleSolsHist->setObservacion($strMensaje);
                                            $this->emComercial->persist($objDetalleSolsHist);
                                            $this->emComercial->flush();

                                            //Obtener Solicitud Padre de un detalle
                                            $intIdSolDescPadre = $this->servicioGeneral->getValorCaracteristicaDetalleSolicitud(
                                                                                         $solDescuentoDetalleAntigua->getId(),'Referencia Solicitud');
                                            if($intIdSolDescPadre)
                                            {
                                                //Finalizar Solicitudes Padre de la solicitud de descuento de ser viable
                                                $arrayParametrosDescuento                     = array();
                                                $arrayParametrosDescuento['idSolicitudPadre'] = $intIdSolDescPadre;
                                                $arrayParametrosDescuento['usrCreacion']      = $arrayParametros['usrCreacion'];
                                                $arrayParametrosDescuento['ipCreacion']       = $arrayParametros['ipCreacion'];
                                                $this->servicioGeneral->finalizarSolicitudPadrePorProcesoMasivo($arrayParametrosDescuento);
                                            }
                                        }
                                    }
                                }
                            }
                        
                            // En caso de ser Servicios migrados se asigna a cero los valores de descuento dado que ya se asigno el nuevo precio
                            if($objServicio->getPorcentajeDescuento() > 0 || $objServicio->getValorDescuento() > 0)
                            {
                                $objServicio->setPorcentajeDescuento(0);
                                $objServicio->setValorDescuento(0);
                                $this->emComercial->persist($objServicio);
                                $this->emComercial->flush();

                                //historial del servicio
                                $servicioHistorial = new InfoServicioHistorial();
                                $servicioHistorial->setServicioId($objServicio);
                                $servicioHistorial->setObservacion("Se cambio a cero el descuento en el servicio por a la ejecución de cambio de plan");                        
                                $servicioHistorial->setEstado($objServicio->getEstado());
                                $servicioHistorial->setUsrCreacion($arrayParametros['usrCreacion']);
                                $servicioHistorial->setFeCreacion(new \DateTime('now'));
                                $servicioHistorial->setIpCreacion($arrayParametros['ipCreacion']);                    
                                $this->emComercial->persist($servicioHistorial);
                                $this->emComercial->flush();
                            }
                        }
                        
                        // Envio de Notificación
                        $this->servicioGeneral->enviarNotificacionFinalizadoSolicitudMasiva($arrayParametros);
                    }
                    else
                    {
                        $strStatus  = "ERROR";
                        $strMensaje = "No existen Capacidades a configurar";
                        $statusCode = 404;

                        //Cuando existe un fallo de algun tipo se pone en estado Fallo a la solicitud
                        $objDetalleSolicitud->setEstado("Fallo");
                        $this->emComercial->persist($objDetalleSolicitud);
                        $this->emComercial->flush();
                    }

                    if($arrayParametros["tipoProceso"] == "Demos" || $arrayParametros["tipoProceso"] == "CancelDemos")
                    {
                        //Se valida si el historial ya ha sido registrado
                        $objInfoDetalleSolHistorial = $this->emComercial->getRepository("schemaBundle:InfoDetalleSolHist")
                                                                        ->findOneBy(array('detalleSolicitudId' => $objDetalleSolicitud,
                                                                                          'observacion'        => $strMensaje));
                        if(is_object($objInfoDetalleSolHistorial))
                        {
                            $strBanderaHistorial = "S";
                        }
                    }

                    if($strBanderaHistorial == "N")
                    {
                        //Se crea Historial de la solicitud
                        $objDetalleSolsHist = new InfoDetalleSolHist();
                        $objDetalleSolsHist->setDetalleSolicitudId($objDetalleSolicitud);
                        $objDetalleSolsHist->setEstado($objDetalleSolicitud->getEstado());
                        $objDetalleSolsHist->setFeCreacion(new \DateTime('now'));
                        $objDetalleSolsHist->setUsrCreacion($arrayParametros['usrCreacion']);
                        $objDetalleSolsHist->setIpCreacion($arrayParametros['ipCreacion']);
                        $objDetalleSolsHist->setObservacion($strMensaje);
                        $this->emComercial->persist($objDetalleSolsHist);
                        $this->emComercial->flush();
                    }
                }
                else
                {
                    //Cuando existe un fallo de algun tipo se pone en estado Fallo a la solicitud                        
                    $objDetalleSolicitud->setEstado("Fallo");
                    $this->emComercial->persist($objDetalleSolicitud);
                    $this->emComercial->flush(); 
                    
                    //Se crea Historial de Servicio
                    $objDetalleSolsHist = new InfoDetalleSolHist();
                    $objDetalleSolsHist->setDetalleSolicitudId($objDetalleSolicitud);
                    $objDetalleSolsHist->setEstado($objDetalleSolicitud->getEstado());
                    $objDetalleSolsHist->setFeCreacion(new \DateTime('now'));
                    $objDetalleSolsHist->setUsrCreacion($arrayParametros['usrCreacion']);
                    $objDetalleSolsHist->setIpCreacion($arrayParametros['ipCreacion']);
                    $objDetalleSolsHist->setObservacion($strMensaje);
                    $this->emComercial->persist($objDetalleSolsHist);
                    $this->emComercial->flush();
                    
                    //Finalizar Solicitudes Padres                    
                    $this->servicioGeneral->finalizarSolicitudPadrePorProcesoMasivo($arrayParametros);
                }
            }
            else
            {
                $objDetalleSolicitud = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                         ->findOneBy(array('servicioId'=> $arrayParametros['idServicio']),
                                                                     array('id'        => 'DESC')
                                                                    );
                $strStatus  = "ERROR";
                $strMensaje = "Servicio no posee Solicitud Masiva creada";
                $statusCode = 404;                                
                
                if($objDetalleSolicitud)
                {
                    if($objDetalleSolicitud->getEstado() == 'Finalizada')
                    {
                        if($arrayParametros["tipoProceso"] == "CancelDemos")
                        {
                            $strStatus  = "OK";
                            $strMensaje = "Se cancelo el Demo exitosamente";
                            $statusCode = 200;                                
                        }
                        else
                        {
                            $strStatus  = "OK";
                            $strMensaje = "Se realizo Cambio de Plan exitosamente";
                            $statusCode = 200;                            
                        }
                    }
                    else if($arrayParametros["tipoProceso"] == "Demos")
                    {
                        $strStatus  = "OK";
                        $strMensaje = "Se ejecuto el Demo exitosamente";
                        $statusCode = 200;                        
                    }                    
                    else
                    {
                        //Se crea Historial de Servicio
                        $objDetalleSolsHist = new InfoDetalleSolHist();
                        $objDetalleSolsHist->setDetalleSolicitudId($objDetalleSolicitud);
                        $objDetalleSolsHist->setEstado($objDetalleSolicitud->getEstado());
                        $objDetalleSolsHist->setFeCreacion(new \DateTime('now'));
                        $objDetalleSolsHist->setUsrCreacion($arrayParametros['usrCreacion']);
                        $objDetalleSolsHist->setIpCreacion($arrayParametros['ipCreacion']);
                        $objDetalleSolsHist->setObservacion($strMensaje);
                        $this->emComercial->persist($objDetalleSolsHist);
                        $this->emComercial->flush();
                    }
                }
            }
        }
        catch (Exception $ex) 
        {
            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }
            
            $strStatus  = "ERROR";
            $strMensaje = "ERROR : ".$ex->getMessage();
            $statusCode = 500;
            
            error_log($ex->getMessage());
        }

        if ($this->emComercial->getConnection()->isTransactionActive())
        {
            $this->emComercial->getConnection()->commit();
        }        
        
        $this->emComercial->getConnection()->close();
        
        if ($strStatus == "OK")
        {
            $strPermiteProcesarMonitoreo = "NO";
            if(is_object($objProductoCp))
            {
                $strNombreTecnico              = $objProductoCp->getNombreTecnico();
                $arrayNombresTecnicoPermitidos = array("INTERNET", "L3MPLS", "INTMPLS", "INTERNET WIFI","INTERNET SDWAN");
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
                                                                                          "personaEmpresaRolId" => $objServicioCp
                                                                                                                   ->getPuntoId()
                                                                                                                   ->getPersonaEmpresaRolId()
                                                                                                                   ->getId(),
                                                                                          "empresaCod"          => '10',
                                                                                          "valor"               => strtoupper
                                                                                                                   ($objServicioCp->getLoginAux()),
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
                $arrayParametrosTelcoGraph['objInfoServicio'] = $objServicioCp;
                $arrayParametrosTelcoGraph['strUsrCreacion']  = $arrayParametros['usrCreacion'];
                $arrayParametrosTelcoGraph['strIpCreacion']   = $arrayParametros['ipCreacion'];
                $arrayParametrosTelcoGraph['strProceso']      = "actualizar";
                $this->servicioGeneral->procesaHostTelcoGraph($arrayParametrosTelcoGraph);
            }
        }
        
        $arrayRespuesta[] = array('status' => $strStatus, 'mensaje' => $strMensaje , 'statusCode' => $statusCode);
        
        return $arrayRespuesta;
    }
    
    
    /**
     * crearTareaRetiroEquipoPorDemo
     * 
     * Función que crea una tarea automática de retiro de equipo, esta dirigida a L2 segun corresponda
     * 
     * @param $arrayParametros [ intSolicitudId         => id de la solicitud a relacionar a la tarea
     *                           intTarea               => id de la tarea
     *                           strTipoAfectado        => tipo de afectado: 'Cliente', 'Elemento', 'Servicio'
     *                           objPunto               => objeto Punto del cliente 
     *                           objDepartamento        => objeto del departamento
     *                           strCantonId            => canton id
     *                           strEmpresaCod          => codigo de la empresa
     *                           strPrefijoEmpresa      => prefijo de la empresa 
     *                           strObservacion         => observación de la tarea
     *                           strUsrCreacion         => usuario de creacion
     *                           strIpCreacion          => ip de creacion 
     *                           intDetalleSolId        => id detalle solitud,
     *                           intDepartamentoOrigen  => id del departamento que crea la tarea,
     *                           strBanderaTraslado     => bandera que indica si se necesita buscar el jefe por departamento y 
     *                                                     region,
     *                           strRegion              => R1 o R2 ]
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 19-07-2017
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 09-08-2017 -  En la tabla INFO_TAREA_SEGUIMIENTO y INFO_DETALLE_HISTORIAL, se regulariza el departamento creador de la accion y
     *                            se adicionan los campos de persona empresa rol id para identificar el responsable actual
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 26-12-2017 - En el asunto y cuerpo del correo se agrega el nombre del proceso al que pertenece la tarea asignada
     *
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.3 09-01-2018 - Se adecua para hacer merge con destinatarios adicionales enviado como parametros y obtener el array
     *                           de destinos de acuerdo a necesidad de procesos
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.4 24-08-2018 - Se recibe nuevas variables para la generación de tareas automáticas para que se adapte a la información
     *                           enviada vía WS del aplicativo Sys Cloud-Center.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.5 29-03-2019 - Si ocurre un error, se almacena la Excepción.
     *
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 1.7 22-10-2019 - Se cambia el valor de strDescripcionRol de 'Jefe Departamental' a '%Jefe%' y validación de nulos.
     * @since 1.6
     * 
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.6 12-09-2019 - Reportan problemas de asignación a Jefe Nacional y Sub Jefe Nacional del departamento IPCCL2.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.7 18-12-2019 - Se agrega el parámetro 'strObtenerArray' para validar la respuesta del método y en caso que
     *                           que valor sea 'SI', el resultado a obtener será un array de valores.
     * 
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.8 10-06-2019 - Se agrega el llamado al proceso que crea la tarea en el Sistema de Sys Cloud-Center.
     * @since 1.7
     * 
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 1.9 08-06-2020 - Se agrega agrega las variables de fecha de apertura y asignación de la tarea para 
     *                           Syscloud
     * @since 1.8
     * 
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 2.0 22-06-2020 - Se valida que el correo sea enviado a la persona correspondiente.
     * @since 1.9
     * 
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 2.1 01-07-2020 - Se quita filtro para obtener el nombre del que asigna para el correo para traslado
     *                           y se lo deja de forma general.
     * @since 2.0
     * 
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 2.2 08-07-2020 - Se agrega la variable iniciarTarea el cual indica 
     *                           si la tarea pasa a estado Aceptada.
     * @since 2.1
     * 
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 2.3 08-07-2020 - Actualización: Se agrega bloque de código para actualizar datos de tareas en nueva tabla DB_SOPORTE.INFO_TAREA
     * 
     * @since 2.2
     *
     * @author kevin ortiz  <kcortiz@telconet.ec>
     * @version 2.4 08-07-2020 - Se corrigio el bug de jefe  departamento
     * 
     * @since 2.3
     * 
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 2.5 04-05-2021 - Actualización: Se permite ahora recibir por parámetro la clase de documento y la forma de contacto
     *                                          para crear la tarea.
     * 
     * @author Pedro Velez <psvelez@telconet.ec>
     * @version 2.5 13-08-2021 - Actualización: Se agrega parametro en llamado a funcion ingresaHistorialYSeguimientoPorTarea 
     *                                          para agregar departamento origen en la creacion de la tarea.
     * 
     * @author Jonathan Mazon Sanchez <jmazon@telconet.ec>
     * @version 2.6 28-09-2021 - Actualización: Se agrega Parametro $strEstadoActual y $strAccion, 
     *                                          la cual permite el ingreso del estado en que se va a crear la tarea, 
     *                                          si no se ingresa el estado, se crea como Asignada.
     * 
     * @since 2.4
     * 
     */
    public function crearTareaRetiroEquipoPorDemo($arrayParametros)
    {    
        $this->emSoporte->getConnection()->beginTransaction();    
        $this->emComunicacion->getConnection()->beginTransaction();    
        $strLogin               = "";
        $intPuntoId             = "";
        $strOpcion              = "";
        $strNombreDepartamento  = "";
        $intDepartamentoId      = "";
        $strNombreTarea         = "";
        $strUsuarioAsigna       = "Telcos+";
        $arrayTo                = array();
        $strCriterio            = "Clientes";        
        $arrayParametrosHist    = array();
        $strNombreProceso       = "";
        $boolEnviarCorreo       = true;
        $strOrigen              = 'web';
        $strUsrCreacionDetalle  = $arrayParametros["strUsrCreacion"];
        $strDescripcionRol      = 'Jefe';
        $strFechaSolicitada     = $arrayParametros["strFechaSolicitada"] ? $arrayParametros["strFechaSolicitada"] : new \DateTime('now');
        $strIdPersonaAsig       = $arrayParametros["strIdPersonaAsig"] ? $arrayParametros["strIdPersonaAsig"] : "";
        $strNombrePersonaAsig   = $arrayParametros["strNombrePersonaAsig"] ? $arrayParametros["strNombrePersonaAsig"] : "";
        $strIdPerRolAsig        = $arrayParametros["strIdPerRolAsig"] ? $arrayParametros["strIdPerRolAsig"] : "";
        $strIniciarTarea        = $arrayParametros["esAutomatico"] ? $arrayParametros["esAutomatico"] : "";
        $strNombreClaseDoc      = (isset($arrayParametros["nombreClaseDocumento"]) && !empty($arrayParametros["nombreClaseDocumento"]) )? 
                                  $arrayParametros["nombreClaseDocumento"] : 'REQUERIMIENTO INTERNO';
        $strNombreFormaContacto = (isset($arrayParametros["nombreFormaContacto"]) && !empty($arrayParametros["nombreFormaContacto"]) )? 
                                  $arrayParametros["nombreFormaContacto"] : 'Correo Electronico';
        $strAsigEnDetSeguimiento= (isset($arrayParametros["asignadoEnDetSeguimiento"]) && 
                                   !empty($arrayParametros["asignadoEnDetSeguimiento"]) )? 
                                  $arrayParametros["asignadoEnDetSeguimiento"] : 'Departamento';
        $strCreaTareaSys        = isset($arrayParametros["strCreaTareaSys"]) ? $arrayParametros["strCreaTareaSys"] : "N";
        $strAccion              = $arrayParametros['strAccion'] ? $arrayParametros['strAccion'] : '';
        $strEstadoActual        = $arrayParametros['strEstadoActual'] ? $arrayParametros['strEstadoActual'] : '';

        //Se define por default que siempre true para envío de correo
        if(isset($arrayParametros['boolEnviaCorreo']))
        {
            $boolEnviarCorreo = $arrayParametros['boolEnviaCorreo'];
        }

        if(isset($arrayParametros['origen']) && !empty($arrayParametros['origen']))
        {
            $strOrigen = $arrayParametros['origen'];
        }

        if(isset($arrayParametros['strAplicacion']) && !empty($arrayParametros['strAplicacion']))
        {
            $strUsrCreacionDetalle = $arrayParametros['strAplicacion'];
        }

        $arrayParametrosHist["strCodEmpresa"]           = $arrayParametros["strEmpresaCod"];
        $arrayParametrosHist["strUsrCreacion"]          = $arrayParametros["strUsrCreacion"];
        $arrayParametrosHist["strOpcion"]               = "Historial";
        $arrayParametrosHist["strIpCreacion"]           = $arrayParametros["strIpCreacion"];        
        $arrayParametrosHist["intIdDepartamentoOrigen"] = isset($arrayParametros["intDepartamentoOrigen"])?
                                                                $arrayParametros["intDepartamentoOrigen"]:0;
        
        try
        {               
            //Se obtiene la tarea
            $objAdmiTarea = $this->emSoporte->getRepository("schemaBundle:AdmiTarea")->find($arrayParametros["intTarea"]);
            
            //Se crea INFO_DETALLE
            $objInfoDetalle = new InfoDetalle();
            
            if(is_object($objAdmiTarea))
            {
                $strNombreTarea = $objAdmiTarea->getNombreTarea();
                $objInfoDetalle->setTareaId($objAdmiTarea);                
            }
            
            $objInfoDetalle->setObservacion($arrayParametros["strObservacion"]);
            $objInfoDetalle->setPesoPresupuestado(0);
            $objInfoDetalle->setValorPresupuestado(0);
            $objInfoDetalle->setFeSolicitada($strFechaSolicitada);
            $objInfoDetalle->setFeCreacion(new \DateTime('now'));
            $objInfoDetalle->setUsrCreacion($strUsrCreacionDetalle);
            $objInfoDetalle->setIpCreacion($arrayParametros["strIpCreacion"]);
            $objInfoDetalle->setDetalleSolicitudId($arrayParametros["intDetalleSolId"]);
            $this->emSoporte->persist($objInfoDetalle);
            $this->emSoporte->flush();      
            
            if($arrayParametros["strTipoAfectado"] == "Cliente")
            {
                //Se obtiene el login y el id punto
                if(is_object($arrayParametros["objPunto"]))
                {
                    $strLogin    = $arrayParametros["objPunto"]->getLogin();                
                    $intPuntoId  = $arrayParametros["objPunto"]->getId();
                }
                
                $strOpcion = "Cliente: " . $strLogin . " | OPCION: Punto Cliente";
            }
            
            if(isset($strLogin) && !empty($strLogin))
            {
                //Se crea el INFO_CRITERIO_AFECTADO            
                $objInfoCriterioAfectado = new InfoCriterioAfectado();
                $objInfoCriterioAfectado->setId("1");         
                $objInfoCriterioAfectado->setDetalleId($objInfoDetalle);
                $objInfoCriterioAfectado->setCriterio($strCriterio);
                $objInfoCriterioAfectado->setOpcion($strOpcion);
                $objInfoCriterioAfectado->setFeCreacion(new \DateTime('now'));
                $objInfoCriterioAfectado->setUsrCreacion($arrayParametros["strUsrCreacion"]);
                $objInfoCriterioAfectado->setIpCreacion($arrayParametros["strIpCreacion"]);
                $this->emSoporte->persist($objInfoCriterioAfectado);
                $this->emSoporte->flush();
                                
                //Se crea el INFO_PARTE_AFECTADA
                $objParteAfectada = new InfoParteAfectada();  
                $objParteAfectada->setTipoAfectado ("Cliente");
                $objParteAfectada->setDetalleId($objInfoDetalle->getId());
                $objParteAfectada->setCriterioAfectadoId($objInfoCriterioAfectado->getId());
                $objParteAfectada->setAfectadoId($intPuntoId);
                $objParteAfectada->setFeIniIncidencia(new \DateTime('now'));                        
                $objParteAfectada->setAfectadoNombre($strLogin);
                $objParteAfectada->setAfectadoDescripcion($strLogin);
                $objParteAfectada->setFeCreacion(new \DateTime('now'));
                $objParteAfectada->setUsrCreacion($arrayParametros["strUsrCreacion"]);
                $objParteAfectada->setIpCreacion($arrayParametros["strIpCreacion"]);
                $this->emSoporte->persist($objParteAfectada);
                $this->emSoporte->flush(); 
            }  
            
            if(is_object($arrayParametros["objDepartamento"]))
            {
                $strNombreDepartamento  = $arrayParametros["objDepartamento"]->getNombreDepartamento();
                $intDepartamentoId      = $arrayParametros["objDepartamento"]->getId();
            }

            //Se crea el INFO_DETALLE_ASIGNACION
            $objDetalleAsignacion = new InfoDetalleAsignacion();
            $objDetalleAsignacion->setDetalleId($objInfoDetalle);
            $objDetalleAsignacion->setMotivo($arrayParametros["strObservacion"]);
            $objDetalleAsignacion->setAsignadoNombre($strNombreDepartamento);
            $objDetalleAsignacion->setAsignadoId($intDepartamentoId);

            //Se crea bandera para validar si se necesita obtener el jefe por departamento y region
            if($arrayParametros["strBanderaTraslado"] == "S")
            {
                $arrayEmpleadoJefe = $this->emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                                       ->getResultadoJefeDepartamentoEmpresa($intDepartamentoId,
                                                                                             $arrayParametros["strEmpresaCod"],
                                                                                             $arrayParametros["strRegion"]);
            }
            else
            {
                $arrayParametrosResponsable["intCantonId"]      = $arrayParametros["strCantonId"];
                $arrayParametrosResponsable["strEstado"]        = "Activo";
                $arrayParametrosResponsable["intDepartamento"]  = $intDepartamentoId;
                $arrayParametrosResponsable["strRol"]           = $strDescripcionRol;
                $arrayParametrosResponsable["strTipoRol"]       = "Empleado";
                $arrayParametrosResponsable["strEmpresaCod"]    = $arrayParametros["strEmpresaCod"];

                $arrayEmpleadoJefe = $this->emComercial->getRepository("schemaBundle:InfoDetalleSolicitud")
                                                       ->getJefePorDepartamento($arrayParametrosResponsable);
               
                if (empty($arrayEmpleadoJefe)) 
                {
                    $arrayDepartametoRol  = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getOne(
                                                    'ROL_JEFE_DEPARTAMENTO',
                                                    '',
                                                    '',
                                                    '',
                                                    $strNombreDepartamento,
                                                    '',
                                                    $arrayParametros["strCantonId"],
                                                    '',
                                                    '',
                                                    $arrayParametros["strEmpresaCod"]
                                                );

                    if (!empty($arrayDepartametoRol)) 
                    {
                        $arrayParametrosResponsable["strRol"]  = $arrayDepartametoRol['valor2'];
                        $arrayEmpleadoJefe = $this->emComercial->getRepository("schemaBundle:InfoDetalleSolicitud")
                                                    ->getJefePorDepartamento($arrayParametrosResponsable);
                    }
                }
            }

            if (!empty($strIdPersonaAsig) && !empty($strNombrePersonaAsig) && !empty($strIdPerRolAsig))
            {
                $intIdPersonaAsignada = $strIdPersonaAsig;
                $strNombresAsignado   = $strNombrePersonaAsig;
                $objDetalleAsignacion->setRefAsignadoId($intIdPersonaAsignada);
                $objDetalleAsignacion->setRefAsignadoNombre($strNombresAsignado);
                $objDetalleAsignacion->setPersonaEmpresaRolId($strIdPerRolAsig);
            }
            else
            {
                $intIdPersonaAsignada = $arrayEmpleadoJefe["idPersona"];
                $strNombresAsignado   = $arrayEmpleadoJefe["nombreCompleto"];
                $objDetalleAsignacion->setRefAsignadoId($intIdPersonaAsignada);
                $objDetalleAsignacion->setRefAsignadoNombre($strNombresAsignado);
                $objDetalleAsignacion->setPersonaEmpresaRolId($arrayEmpleadoJefe["personaEmpresaRolId"]);
            }

            $objDetalleAsignacion->setTipoAsignado("EMPLEADO");
            $objDetalleAsignacion->setUsrCreacion($arrayParametros["strUsrCreacion"]);            
            $objDetalleAsignacion->setIpCreacion($arrayParametros["strIpCreacion"]);
            $objDetalleAsignacion->setFeCreacion(new \DateTime('now'));
            $this->emSoporte->persist($objDetalleAsignacion);
            $this->emSoporte->flush();            

            //Se ingresa el historial de la tarea
            if(is_object($objInfoDetalle))
            {
                $arrayParametrosHist["intDetalleId"] = $objInfoDetalle->getId();            
            }
                        
            $arrayParametrosHist["strObservacion"]  = ($strAsigEnDetSeguimiento == "Empleado")?
                                                      "Tarea fue Asignada a ".$strNombresAsignado:"Tarea fue Asignada a ".$strNombreDepartamento;
            $arrayParametrosHist["strEstadoActual"] = "Asignada";  
            $arrayParametrosHist["strAccion"]       = "Asignada";
            
            if(!empty($strEstadoActual))
            {
                $arrayParametrosHist["strEstadoActual"] = $strEstadoActual;
            }
            if(!empty($strAccion))
            {
                $arrayParametrosHist["strAccion"]       = $strAccion;
            }

            $arrayParametrosHist["strCreaTareaSys"] = $strCreaTareaSys;

            $this->serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);              
            
            
            //Se ingresa el seguimiento de la tarea          
            $arrayParametrosHist["strOpcion"] = "Seguimiento";                    

            $this->serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);

            if($strOrigen == 'ws')
            {
                $boolSeguimiento = false;

                //Verificar estado para iniciar la tarea automática
                if(isset($arrayParametros['esAutomatico']) && !empty($arrayParametros['esAutomatico']) && $arrayParametros['esAutomatico'] == 'S')
                {
                    $arrayParametrosHist["strEstadoActual"] = "Aceptada";
                    $arrayParametrosHist["strAccion"]       = "Aceptada";
                    $boolSeguimiento                        = true;
                }

                //Si se ingresa seguimiento adicional por parte del Ws
                if(isset($arrayParametros['seguimiento']) && !empty($arrayParametros['seguimiento']))
                {
                    $arrayParametrosHist["strObservacion"] = $arrayParametros['seguimiento'];
                    $boolSeguimiento                       = true;
                }

                if($boolSeguimiento)
                {
                    $this->serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);
                }

                //Se guarda en seguimiento el Id del requerimiento en el TelcoSys
                $arrayParametrosHist["strObservacion"] = 'El requerimiento generado desde Sys Cloud-Center es el <b>#'.
                                                         $arrayParametros['intIdTareaTelcoSys'].'</b>';
                $this->serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);
            }

            
            //**************************Se envia notificación**************************//
            $objAdmiClaseDocumento = $this->emComunicacion->getRepository("schemaBundle:AdmiClaseDocumento")
                                                          ->findOneBy(array('nombreClaseDocumento' => $strNombreClaseDoc));

            //Se crea el INFO_DOCUMENTO
            $objInfoDocumento = new InfoDocumento();
            $objInfoDocumento->setMensaje($arrayParametros["strObservacion"]);
            $objInfoDocumento->setNombreDocumento("Registro de tarea");

            if(is_object($objAdmiClaseDocumento))
            {
                $objInfoDocumento->setClaseDocumentoId($objAdmiClaseDocumento);
            }

            $objInfoDocumento->setFeCreacion(new \DateTime('now'));
            $objInfoDocumento->setEstado("Activo");
            $objInfoDocumento->setUsrCreacion($arrayParametros["strUsrCreacion"]);
            $objInfoDocumento->setIpCreacion($arrayParametros["strIpCreacion"]);
            $objInfoDocumento->setEmpresaCod($arrayParametros["strEmpresaCod"]);
            $this->emComunicacion->persist($objInfoDocumento);
            $this->emComunicacion->flush();


            $objFormaContacto = $this->emComercial->getRepository("schemaBundle:AdmiFormaContacto")
                                                  ->findOneBy(array('descripcionFormaContacto' => $strNombreFormaContacto,
                                                                    'estado'                   => 'Activo'));

            //Se crea el INFO_COMUNICACION
            $objInfoComunicacion = new InfoComunicacion();

            if(is_object($objFormaContacto))
            {
                $objInfoComunicacion->setFormaContactoId($objFormaContacto->getId());
            }

            $objInfoComunicacion->setClaseComunicacion("Recibido");
            $objInfoComunicacion->setDetalleId($objInfoDetalle->getId());
            $objInfoComunicacion->setFechaComunicacion(new \DateTime('now'));
            $objInfoComunicacion->setEstado("Activo");
            $objInfoComunicacion->setFeCreacion(new \DateTime('now'));
            $objInfoComunicacion->setUsrCreacion($arrayParametros["strUsrCreacion"]);
            $objInfoComunicacion->setIpCreacion($arrayParametros["strIpCreacion"]);
            $objInfoComunicacion->setEmpresaCod($arrayParametros["strEmpresaCod"]);
            $objInfoComunicacion->setRemitenteId($intPuntoId);
            $objInfoComunicacion->setRemitenteNombre($strLogin);
            $this->emComunicacion->persist($objInfoComunicacion);
            $this->emComunicacion->flush();


            //Se crea el INFO_DOCUMENTO_COMUNICACION
            $objInfoDocumentoComunicacion = new InfoDocumentoComunicacion();
            $objInfoDocumentoComunicacion->setComunicacionId($objInfoComunicacion);
            $objInfoDocumentoComunicacion->setDocumentoId($objInfoDocumento);
            $objInfoDocumentoComunicacion->setFeCreacion(new \DateTime('now'));
            $objInfoDocumentoComunicacion->setEstado('Activo');
            $objInfoDocumentoComunicacion->setUsrCreacion($arrayParametros["strUsrCreacion"]);
            $objInfoDocumentoComunicacion->setIpCreacion($arrayParametros["strIpCreacion"]);
            $this->emComunicacion->persist($objInfoDocumentoComunicacion);
            $this->emComunicacion->flush();            

            if($boolEnviarCorreo)
            {
                //Se obtiene el correo de la persona asignada
                $objInfoPersonaFormaContacto = $this->emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                                 ->findOneBy(array('personaId'       => $intIdPersonaAsignada,
                                                                                   'formaContactoId' => $objFormaContacto->getId(),
                                                                                   'estado'          => "Activo"));

                //OBTENGO EL CONTACTO DE LA PERSONA QUE ASIGNADA A LA TAREA
                if($objInfoPersonaFormaContacto)
                {
                    //Correo Persona Asignada
                    $strCorreoPersonaAsignada = $objInfoPersonaFormaContacto->getValor();
                    $arrayTo[]                = $strCorreoPersonaAsignada;
                }

                if(is_object($objAdmiTarea))
                {
                    $strNombreProceso = $objAdmiTarea->getProcesoId()->getNombreProceso();
                }

                $strAsunto = "Asignacion de Tarea | PROCESO: ".$strNombreProceso;

                //En el caso de existir correos externos adicionales
                if(isset($arrayParametros['arrayCorreos']) && !empty($arrayParametros['arrayCorreos']))
                {
                    $arrayTo = array_merge($arrayTo,$arrayParametros['arrayCorreos']);
                }

                //Se obtiene el id_persona del usuario
                $objInfoPersona = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                                                    ->findOneBy(array("login" => $arrayParametros["strUsrCreacion"]));

                if(is_object($objInfoPersona))
                {
                    $strUsuarioAsigna = $objInfoPersona->__toString();
                }
                else
                {
                    $strUsuarioAsigna = "Telcos+";
                }
                
                

                $arrayParametrosCorreo = array('detalle'            => $objInfoDetalle,
                                               'numeroTarea'        => $objInfoComunicacion->getId(),
                                               'nombreTarea'        => $strNombreTarea,
                                               'nombreProceso'      => $strNombreProceso,
                                               'nombreDepartamento' => $strNombreDepartamento." - ".$strNombresAsignado,
                                               'observacion'        => $arrayParametros["strObservacion"],
                                               'empleadoLogeado'    => $strUsuarioAsigna,
                                               'empresa'            => $arrayParametros["strPrefijoEmpresa"],
                                               'loginProcesado'     => $strLogin);

                $this->serviceEnvioPlantilla->generarEnvioPlantilla($strAsunto,
                                                                    $arrayTo,
                                                                    'TAREACERT',
                                                                    $arrayParametrosCorreo,
                                                                    $arrayParametros["strEmpresaCod"],
                                                                    $arrayParametros["strCantonId"],
                                                                    $intDepartamentoId);
            }

            //Se inicia la tarea de manera automática.
            if (strtoupper($strIniciarTarea) === 'S')
            {
                $arrayParametrosHist["strTipo"]        = 'iniciar';
                $arrayParametrosHist["strCodEmpresa"]  = $arrayParametros["strEmpresaCod"];
                $arrayParametrosHist["strUser"]        = $arrayParametros["strUsrCreacion"];
                $arrayParametrosHist["strIpUser"]      = $arrayParametros["strIpCreacion"];
                $arrayParametrosHist["objDetalle"]     = $objInfoDetalle;

                $arrayResultado = $this->serviceSoporte->administrarTarea($arrayParametrosHist);
            }

            //**************************Se envia notificación**************************//

            $this->emComunicacion->getConnection()->commit();
            $this->emSoporte->getConnection()->commit();

            //Proceso que graba tarea en INFO_TAREA
            $arrayParametrosInfoTarea['intDetalleId']   = is_object($objInfoDetalle)?$objInfoDetalle->getId():null;
            $arrayParametrosInfoTarea['strUsrCreacion'] = $strUsrCreacionDetalle;
            $this->serviceSoporte->crearInfoTarea($arrayParametrosInfoTarea);

            //Proceso para crear la tarea en Sys Cloud-Center
            if (is_object($objInfoComunicacion) && is_object($objAdmiTarea) && $strOrigen !== 'ws')
            {
                $strFechaSolicitada = date_format($objInfoDetalle->getFeSolicitada(), 'Y-m-d');
                $strHoraSolicitada  = date_format($objInfoDetalle->getFeSolicitada(), 'H:i:s');

                $arrayDatosPersonas = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                        ->getInfoDatosPersona(array ('strRol'                     => 'Empleado',
                                                     'strCodEmpresa'              => $arrayParametros['strEmpresaCod'],
                                                     'strLogin'                   => $arrayParametros['strUsrCreacion'],
                                                     'strEstadoPersona'           => 'Activo',
                                                     'strEstadoPersonaEmpresaRol' => 'Activo'));

                if (!empty($arrayDatosPersonas) && $arrayDatosPersonas['status'] === 'ok')
                {
                    $strUserAsigna  = $arrayDatosPersonas['result'][0]['nombres'].' '.
                                      $arrayDatosPersonas['result'][0]['apellidos'];
                    $strDeparAsigna = $arrayDatosPersonas['result'][0]['nombreDepartamento'];
                    $this->serviceProceso->putTareasSysCluod(array ('strNombreTarea'      => $strNombreTarea,
                                                                    'strNombreProceso'    => $objAdmiTarea
                                                                                                ->getProcesoId()
                                                                                                ->getNombreProceso(),
                                                                    'strObservacion'      => $arrayParametros["strObservacion"],
                                                                    'strFechaApertura'    => $strFechaSolicitada,
                                                                    'strHoraApertura'     => $strHoraSolicitada,
                                                                    'strUser'             => $arrayParametros["strUsrCreacion"],
                                                                    'strIpAsigna'         => $arrayParametros["strIpCreacion"],
                                                                    'strUserAsigna'       => $strUserAsigna,
                                                                    'strDeparAsigna'      => $strDeparAsigna,
                                                                    'strUserAsignado'     => $strNombresAsignado,
                                                                    'strDeparAsignado'    => $strNombreDepartamento,
                                                                    'objInfoComunicacion' => $objInfoComunicacion));
                }
            }

            if (isset($arrayParametros['strObtenerArray']) && !empty($arrayParametros['strObtenerArray'])
                    && $arrayParametros['strObtenerArray'] === 'SI')
            {
                return array('status'                   => true,
                             'intIdComunicacion'        => $objInfoComunicacion->getId(),
                             'strCorreoPersonaAsignada' => $strCorreoPersonaAsignada);
            }
            else
            {
                return $objInfoComunicacion->getId();
            }
        }
        catch(Exception $objException)
        {
            if ($this->emComunicacion->getConnection()->isTransactionActive())
            {
                $this->emComunicacion->getConnection()->rollback();
            }    
            
            $this->emComunicacion->getConnection()->close();
            
            if ($this->emSoporte->getConnection()->isTransactionActive())
            {
                $this->emSoporte->getConnection()->rollback();
            }

            $this->emSoporte->getConnection()->close();

            $this->utilService->insertError('Telcos+',
                                            'InfoCambiarPlanService->crearTareaRetiroEquipoPorDemo',
                                             $objException->getMessage(),
                                             $arrayParametros["strUsrCreacion"],
                                             $arrayParametros["strIpCreacion"]);

            return "";
        }
    }
    
    
    
    
    /**
     * Funcion que realiza el cambio de plan para
     * la empresa Megadatos
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 4-06-2014
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 26-10-2015 Se agrega validacion de detalle elemento para asignar nuevos perfiles a cliente en OLT ya migrados
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.2 23-11-2015 Se modifica la recuperación de perfil por Nuevos Planes Ultra Velocidad
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.3 16-01-2016 Se agrega validacion de modelo del elemento para bloqueo de cambios de planes Tellion CNR
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.4 13-04-2016 Se modifica funcion que recupera equivalencia de perfiles de planes para poder
     *                         aprovisionar a clientes con planes UAV
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.5 28-04-2016 Se agrega validación para actualizar el perfil del servicio de internet del cliente cuando 
     *                         son ips adicionales de clientes pyme tellion
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.6 17-05-2016 Se agrega validación para cambios de plan Pyme Tellion Pool
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.7 18-06-2016 Se agrega validación para cambios de plan de servicios que tienen traslados pendientes
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.8 24-02-2017 Se agregan validaciones para gestionar planes SmartWifi, los escenarios son los siguientes:
     *               - Plan Viejo sin SmartWifi -> Plan Nuevo con SmartWifi (genera solicitud agregar equipo para gestionar registro del nuevo equipo)
     *               - Plan Viejo con SmartWifi -> Plan Nuevo sin Smart Wifi (genera retiro de equipo SmartWifi que incluia el plan anterior)
     *               - Plan Viejo con SmartWifi -> Plan Nuevo con SmartWifi (no se realiza ninguna gestion) 
     *               - Plan Viejo sin SmartWifi -> Plan Nuevo sin SmartWifi (no se realiza ninguna gestion)
     * 
     * @since 1.7
     * 
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.9 09-08-2017 -  En la tabla INFO_DETALLE_HISTORIAL se registra el id_persona_empresa_rol del responsable de la tarea
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.10 14-09-2017 - Se realizan ajustes para definir que el estado inicial de una tarea sea 'Asignada'
     *
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.11 04-09-2017 Se agrega historial respectivo por cambio de plan unicamente cuando se cambia a un plan de mayor valor, 
     *                         (Se guarda cuando el precio actual es menor al precio del plan nuevo), para poder facturar el proporcional 
     *                          del servicio por el cambio de plan MD.
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.12 20-12-2017 Se agregan correcciones en la programación dentro del flujo middleware para solventar casos reportados por soporte
     * @since 1.11
     * 
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.12 21-12-2017 - En la tabla INFO_DETALLE_ASIGNACION se registra el campo tipo asignado 'EMPLEADO'
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.13 21-02-2018 - Se corrige un error al momento de realizar un cambio de plan, el usuario de creacion no puede ir null
     *                            en la tabla INFO_DETALLE_ASIGNACION
     *
     * Se agrega el recálculo del valor de descuento fijo.
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.14
     * @since 25-04-2018
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.0 03-08-2018 - Se obtienen las características adicionales necesarias para el envío al middleware para servicios con factibilidad
     *                           en olts de tecnología ZTE y se modifican validaciones que son únicamente para servicios en olts Tellion
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 2.1 28-02-2019 - Se agrega validación para no permitir realizar cambios de plan a servicios
     *                           que estan en proceso de traslado
     * @since 2.0
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 2.2 05-06-2019 - Se agregan validaciones para restringir cambio de planes a clientes que tienen
     *                           contratado productos MCAFEE como servicios adicionales
     * @since 2.1
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 2.3 12-08-2019 - Se agrega validación de flujo de antivirus para controlar cambio de planes de clientes
     * @since 2.2

     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.4 11-05-2020 Se unifica las validaciones por marca y no por modelo de olt
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.5 01-07-2020 Se elimina parámetros y validación para servicios de Internet Protegido ya que serán considerados en el procedimiento
     *                          de base implementado para verificación del cambio de plan
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.6 31-08-2020 Se agrega programación para eliminar todas las características anteriores en caso de que exista más de un registro
     *                         de una misma característica asociada al servicio
     * 
     * @author David Leon <mdleon@telconet.ec>
     * @version 2.7 21-09-2020 Verificamos si el producto adicional del plan se encuentra parametrizado para generar solicitud de planificación del
     *                          mismo.
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 2.8 26-10-2020 Se agrega nuevo parámetro utilizado para verificación de Ip Fija Wan en planes Pyme
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.9 29-12-2020 Se invoca a la función obtieneProductoEnPlan para verificar correctamente si un producto con nombre técnico IP 
     *                         está incluido en un plan que actualmente ya no se encuentra en estado Activo
     *
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 3.0 29-12-2020 Se agregan cambios para poder realizar cambio de planes de los siguientes escenarios:
     *                             - Home -> Pyme con Ips
     *                             - Pyme con Ips -> Pyme con Ips
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 3.1 07-1-2021 Se agrega flujo para productos Paramount o Noggin dentro de un plan o como producto adicional
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 3.2 08-01-2021 Se elimina la restricción para cambios de planes de PYME a HOME
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 3.3 17-02-2021 Se agrega la programación para cancelación de ips Wan y adicionales cuando se realiza un cambio de plan de PYME a HOME
     * 
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 3.4 03-03-2021 Se valida la generacion de la solicitud de cableado ethernet con la confirmacion del cliente.
     *                         De ser si, continua el flujo normal de ser no se crea la solicitud pero en estado Rechazado,
     *                         y deja la constancia de la eleccion en el historial del servicio.
     * 
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 3.5 16-04-2021 Se mueve parte de la lógica encargada de guardar historial del cambio de plan a la función 'cambiarPlan', 
     *                         para incluir el guardado de mensaje adicional del proceso de recálculo en la observación.
     * 
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 3.6 23-08-2021 Se agrega funcionalidad que valida el servicio cuando posea beneficio adulto mayor (3era Edad Resolución 07-2021) 
     *                         mediante la aplicación del parámetro APLICA_DESC_TIPO_PLAN_COMERCIAL al momento de realizar un cambio de plan.
     * 
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 3.7 03-01-2022 - Se corrige bug generado por validaciones de productos inactivos.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 3.8 12-11-2021 Se construye el arreglo con la información que se enviará al web service para confirmación de opción 
     *                          de Tn a Middleware
     * 
     * @author Emmanuel Martillo <emartillo@telconet.ec>
     * @version 3.9 03-03-2023 - Se agrega validacion por Prefijo Ecuanet EN para que siga el flujo de Megadatos,
     *                           adicional validacion para que el plan nuevo tenga internet dedicado y
     *                           Se agrega Validación y envio de prefijo empresa para llamada al middleware al cambiar plan en ecuanet.
     * 
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 3.9 12-12-2022 Se agrega lógica para consumir microservicio ms-comp-cliente para validaciones de saldo, documentos en el 
     *                         cliente al momento de realizar un cambio de plan MD.
     * 
     */
    public function cambioPlanMd($arrayParametros)
    {
        $servicio                = $arrayParametros['servicio'];
        $servicioTecnico         = $arrayParametros['servicioTecnico'];
        $planId                  = $arrayParametros['planId'];
        $interfaceElemento       = $arrayParametros['interfaceElemento'];
        $elemento                = $arrayParametros['elemento'];
        $modeloElemento          = $arrayParametros['modeloElemento'];
        $strCapacidad1Nueva      = $arrayParametros['capacidad1'] ? $arrayParametros['capacidad1'] : "";
        $strCapacidad2Nueva      = $arrayParametros['capacidad2'] ? $arrayParametros['capacidad2'] : "";
        $strCapacidad1Actual     = $arrayParametros['capacidad1Actual'] ? $arrayParametros['capacidad1Actual'] : "";
        $strCapacidad2Actual     = $arrayParametros['capacidad2Actual'] ? $arrayParametros['capacidad2Actual'] : "";
        $idEmpresa               = $arrayParametros['idEmpresa'];
        $usrCreacion             = $arrayParametros['usrCreacion'];
        $ipCreacion              = $arrayParametros['ipCreacion'];
        $precioViejo             = $arrayParametros['precioViejo'];
        $precioNuevo             = $arrayParametros['precioNuevo'];
        $intIdPersonaEmpRol      = $arrayParametros['intIdPersonaEmpRol'];
        $intIdOficina            = $arrayParametros['intIdOficina'];        
        $intIdDepartamento       = $arrayParametros['intIdDepartamento'];
        $strConservarIp          = $arrayParametros['strConservarIp'];
        $strPrefijoEmpresaFlujo  = $arrayParametros['strPrefijoEmpresaFlujo'];
        $strPrefijoEmpresa       = null;
        $arrayParametrosHist     = array();
        $floatPrecioNuevoPlan    = 0;
        $intDetalleId            = null;
        $strIppcSolicita         = $arrayParametros['ippcSolicita'];

        $arrayParametrosHist["strCodEmpresa"]           = $idEmpresa;
        $arrayParametrosHist["strUsrCreacion"]          = $usrCreacion;
        $arrayParametrosHist["strOpcion"]               = "Historial";
        $arrayParametrosHist["strIpCreacion"]           = $ipCreacion;          
        $arrayParametrosHist["intIdDepartamentoOrigen"] = $intIdDepartamento;
        $boolPlanificaAdicional  = false;
        $strAprovisionamiento       = "";
        $ejecutaScriptsOlt          = "SI";
        $strPlanViejoTieneSmartWifi = "NO";
        $strPlanNuevoTieneSmartWifi = "NO";
        $strPymesConIps             = "NO";
        $strProductoIpEnPlanViejo   = "NO";
        $strProductoIpEnPlanNuevo   = "NO";
        $strPlanViejoTieneParamount = "NO";
        $strPlanNuevoTieneParamount = "NO";
        $strPlanViejoTieneNoggin    = "NO";
        $strPlanNuevoTieneNoggin    = "NO";
        $strProdAdicTieneParamount  = "NO";
        $strProdAdicTieneNoggin     = "NO";
        $intIdCriterioAfectado      = 1;
        $strMigradoCnr              = "";
        $macWifi                    = '';
        $strVlanAntes               = '';
        $strGemPortAntes            = '';
        $strTrafficAntes            = '';
        $strServiceProfile          = '';
        $strMacOnt                  = '';
        $strIndiceCliente           = '';
        $strLineProfileAntes        = '';
        $strSpid                    = '';
        $strIp                      = '';
        $strScope                   = '';
        $strVlanNuevo               = '';
        $strGemPortNuevo            = '';
        $strTrafficNuevo            = '';
        $strLineProfileNuevo        = '';
        $intIpFijasActivas          = 0;
        $objServicioIpAdicional     = null;
        $intIpProPlan               = 0;
        $intFlagHomePyme            = 0;
        $arrayParamsSpc             = array("campoOrderByDescSpc" => "id", "orderBySpc" => "DESC");
        $strUsrRegulaSpc            = "regulaSpc";
        $arrayDataConfirmacionTn    = array();
        $strOcurrioException        = "NO";
        try
        {
            //valida envio de empresa al middleware
            if($idEmpresa == '33')
            {
                $strPrefijoEmpresa  = 'EN';
            }
            else if($idEmpresa == '26')
            {
                $strPrefijoEmpresa  = 'TNP';
            }

            $login                      = $servicio->getPuntoId()->getLogin();
            $punto                      = $servicio->getPuntoId();
            $arrayProdIp                = $this->emComercial
                                               ->getRepository('schemaBundle:AdmiProducto')
                                               ->findBy(array("nombreTecnico" => "IP", "empresaCod" => $idEmpresa, "estado" => "Activo"));
            $arrayProdInternet          = $this->emComercial
                                               ->getRepository('schemaBundle:AdmiProducto')
                                               ->findBy(array("nombreTecnico" => "INTERNET", "empresaCod" => $idEmpresa, "estado" => "Activo"));

            $objProdSmartWifiRenta      = $this->emComercial
                                                ->getRepository('schemaBundle:AdmiProducto')
                                                ->findOneBy(array("descripcionProducto" => "Renta SmartWiFi (Aironet 1602)",
                                                                  "empresaCod"          => $idEmpresa, 
                                                                  "estado"              => "Activo"));

            $objIpElemento              = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                ->findOneBy(array('elementoId' => $elemento->getId(), 'estado' => 'Activo'));

            $objDetalleElementoMid      = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                            ->findOneBy(array(  "elementoId"   => $servicioTecnico->getElementoId(),
                                                                "detalleNombre"=> 'MIDDLEWARE',
                                                                "estado"       => 'Activo'));
            $flagMiddleware             = false;

            $objParametroDet = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                           ->findOneBy(array('descripcion'=> 'NOMBRES_TECNICOS_PRODUCTOS_TV',
                                                             'estado'=> 'Activo'));
            $arrayProductosId = array();
            
            $objProdParamount           = $this->emComercial
                                                ->getRepository('schemaBundle:AdmiProducto')
                                                ->findOneBy(array("nombreTecnico"       => $objParametroDet->getValor2(),
                                                                  "estado"              => "Activo"));
            if (is_object($objProdParamount) && !empty($objProdParamount))
            {
                $arrayProductosId[] = $objProdParamount->getId();
            }
            $objProdNoggin              = $this->emComercial
                                                ->getRepository('schemaBundle:AdmiProducto')
                                                ->findOneBy(array("nombreTecnico"       => $objParametroDet->getValor1(),
                                                                  "estado"              => "Activo"));
            if (is_object($objProdNoggin) && !empty($objProdNoggin))
            {
                $arrayProductosId[] = $objProdNoggin->getId();
            }
            $arrayServiciosxPunto       = $this->emComercial
                                                ->getRepository('schemaBundle:InfoServicio')
                                                ->findBy(array("puntoId"    =>  $punto,
                                                               "productoId" =>  $arrayProductosId,
                                                               "estado"     =>  'Activo'));

            $arrayEstadosTraslado = array("Anulado", "Eliminado", "Cancel", "Rechazada");

            if($objDetalleElementoMid)
            {
                if($objDetalleElementoMid->getDetalleValor() == 'SI')
                {
                    $flagMiddleware = true;
                }
            }

            //verificar plan viejo
            $planCabViejo = $servicio->getPlanId();
            $planDetViejo = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                 ->findBy(array("planId" => $planCabViejo->getId()));

            //verificar plan nuevo
            $planCabNuevo = $this->emComercial->getRepository('schemaBundle:InfoPlanCab')->find($planId);
            $planDetNuevo = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                 ->findBy(array("planId" => $planCabNuevo->getId()));

            if ($planCabViejo->getTipo() == "PYME" && $planCabNuevo->getTipo() == "PRO")
            {
                $arrayFinal[] = array('status'  => "ERROR", 
                                      'mensaje' => "Cambio de Plan no permitido por definiciones Comerciales.");
                return $arrayFinal;            
            }

            //Se realiza validación para el servicio que posea solicitud de descuento de adulto mayor
            if($strPrefijoEmpresaFlujo == 'MD' || $strPrefijoEmpresaFlujo == 'EN') 
            {
                $arrayParametros[] = array('intIdServicio' => $servicio->getId(),
                                           'intIdEmpresa'  => $idEmpresa,
                                           'intIdPlan'     => $planCabNuevo->getId()
                                          );

                $arrayRespuesta = $this->validaSolicitudAdultoMayor($arrayParametros);
                $strStatus      = $arrayRespuesta[0]['strStatus'];

                if($strStatus != "OK" )
                {  
                    $arrayFinal[] = array('status'  => $arrayRespuesta[0]['strStatus'], 
                                          'mensaje' => $arrayRespuesta[0]['strMensaje']); 
                    return $arrayFinal; 
                }
            }
            
            //validación por el consumo del microservicio ms-comp-cliente
            if($strPrefijoEmpresaFlujo == 'MD' || $strPrefijoEmpresaFlujo == 'EN' ) 
            { 
                $arrayTokenCas = $this->serviceTokenCas->generarTokenCas();
                
                $intIdPersona    = $punto->getPersonaEmpresaRolId()->getPersonaId()->getId();

                $arrayParametros = array();
                $arrayParametros['strTokenCas']      = $arrayTokenCas['strToken'];
                $arrayParametros['usrCreacion']      = $usrCreacion;
                $arrayParametros['clienteIp']        = $ipCreacion;
                $arrayParametros['idEmpresa']        = $idEmpresa;
                $arrayParametros['idPersona']        = $intIdPersona;
                $arrayParametros['precioPlanActual'] = $servicio->getPrecioVenta();
                $arrayParametros['idPlanCabNuevo']   = $planCabNuevo->getId();

                $arrayResponse = $this->validacionPorCambioPlanUpMs($arrayParametros); 
                
                if($arrayResponse["strStatus"] != "OK")
                {
                    $arrayFinal[] = array('status'  => "ERROR", 
                                          'mensaje' => $arrayResponse["strMensaje"]);
                    return $arrayFinal; 
                } 
            } 

            $strCambioPlanPymeAHome = "NO";
            if($planCabViejo->getTipo() == "PYME" && $planCabNuevo->getTipo() == "HOME")
            {
                $strCambioPlanPymeAHome = "SI";
            }

            //se setean variales para identificar si los planes nuevos y viejos incluyen o no productos SmartWifi
            if(is_object($objProdSmartWifiRenta))
            {
                if(is_object($planCabViejo))
                {

                    $arrayPlanDet = $this->emComercial
                                         ->getRepository('schemaBundle:InfoPlanDet')
                                         ->findBy(array("planId" => $planCabViejo->getId()));

                    foreach($arrayPlanDet as $objPlanDet)
                    {
                        if($objPlanDet->getProductoId() == $objProdSmartWifiRenta->getId())
                        {
                            $strPlanViejoTieneSmartWifi = "SI";
                        }
                    }
                }
                if(is_object($planCabNuevo))
                {

                    $arrayPlanDet = $this->emComercial
                                         ->getRepository('schemaBundle:InfoPlanDet')
                                         ->findBy(array("planId" => $planCabNuevo->getId()));

                    foreach($arrayPlanDet as $objPlanDet)
                    {
                        if($objPlanDet->getProductoId() == $objProdSmartWifiRenta->getId())
                        {
                            $strPlanNuevoTieneSmartWifi = "SI";
                        }
                    }
                }
            }

            //PLan viejo y nuevo contiene paramount y Noggin
            $arrayPlanDetViejo          =    $planDetViejo;
            $arrayPlanDetNuevo          =    $planDetNuevo;
            $arrayPlanDetOrigen         =    array();
            $arrayPlanDetDestino        =    array();
            $objProdAdicionalParamount  = '';
            $objProdAdicionalNoggin     = '';

            if(is_array($arrayPlanDetViejo))
            {
                foreach($arrayPlanDetViejo as $objPlanDet)
                {
                    if(!empty($objProdParamount) && ($objPlanDet->getProductoId() == $objProdParamount->getId()))
                    {
                        $strPlanViejoTieneParamount = "SI";
                        $arrayPlanDetOrigen[] = $objPlanDet;
                    }
                    if(!empty($objProdNoggin) && ($objPlanDet->getProductoId() == $objProdNoggin->getId()))
                    {
                        $strPlanViejoTieneNoggin = "SI";
                        $arrayPlanDetOrigen[] = $objPlanDet;
                    }
                }
            }
            if(is_array($arrayPlanDetNuevo))
            {
                foreach($arrayPlanDetNuevo as $objPlanDet)
                {
                    if(!empty($objProdParamount) && ($objPlanDet->getProductoId() == $objProdParamount->getId()))
                    {
                        $strPlanNuevoTieneParamount = "SI";
                        $arrayPlanDetDestino[] = $objPlanDet;
                    }
                    if(!empty($objProdNoggin) && ($objPlanDet->getProductoId() == $objProdNoggin->getId()))
                    {
                        $strPlanNuevoTieneNoggin = "SI";
                        $arrayPlanDetDestino[] = $objPlanDet;
                    }
                }
            }
            //verifica si tiene productos adicionales Paramount y Noggin activos
            if(is_array($arrayServiciosxPunto))
            {
                foreach($arrayServiciosxPunto as $objServicioProdViejo)
                {
                    if(!empty($objProdParamount) && 
                      ($objServicioProdViejo->getProductoId()->getId() == $objProdParamount->getId()))
                    {
                        $strProdAdicTieneParamount = "SI";
                        $objProdAdicionalParamount = $objServicioProdViejo;
                    }
                    if(!empty($objProdNoggin) &&
                      ($objServicioProdViejo->getProductoId()->getId() == $objProdNoggin->getId()))
                    {
                        $strProdAdicTieneNoggin = "SI";
                        $objProdAdicionalNoggin = $objServicioProdViejo;
                    }
                }
            }


            $strAprovisionamiento   = $this->recursosRed->geTipoAprovisionamiento($servicioTecnico->getElementoId());

            //obtener producto internet de un plan det
            $indiceProductoInternet = $this->servicioGeneral->obtenerIndiceInternetEnPlanDet($planDetNuevo, $arrayProdInternet);
            $indiceProductoIp       = $this->servicioGeneral->obtenerIndiceInternetEnPlanDet($planDetNuevo, $arrayProdIp);
            $indiceProductoIpAnt    = $this->servicioGeneral->obtenerIndiceInternetEnPlanDet($planDetViejo, $arrayProdIp);
            
            if ($indiceProductoInternet!=-1)
            {
                $producto = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                             ->find($planDetNuevo[$indiceProductoInternet]->getProductoId());
            }
            else
            {
                throw  new Exception("El producto Internet Dedicado no se encuentra asociado al Plan Nuevo");
            }
            

            if ($indiceProductoIp!=-1)
            {
                $productoIp = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                   ->find($planDetNuevo[$indiceProductoIp]->getProductoId());
            }
            else
            {
                $productoIp = null;
            }
            if ($indiceProductoIpAnt!=-1)
            {
                $productoIpAnt = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                   ->find($planDetViejo[$indiceProductoIpAnt]->getProductoId());
            }
            else
            {
                $productoIpAnt = null;
            }

            //consultar si el olt tiene aprovisionamiento de ips en el CNR
            $objDetalleElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                          ->findOneBy(array('detalleNombre' => 'OLT MIGRADO CNR',
                                                                            'elementoId'    => $interfaceElemento->getElementoId()->getId()));

            if ($objDetalleElemento)
            {
                $strMigradoCnr = "SI";
            }
            else
            {
                $strMigradoCnr = "NO";
            }

            $strMarcaOlt    = $modeloElemento->getMarcaElementoId()->getNombreMarcaElemento();
            //se agrega validación de modelo de elemento para bloqueo de cambio de planes TELLION CNR
            if ($strAprovisionamiento == "CNR" && $strMigradoCnr == "NO" && $strMarcaOlt === "TELLION")
            {
                $arrayFinal[] = array('status'  => "ERROR", 
                                      'mensaje' => "Cambio de Plan no permitido por migracion de Olt.");
                return $arrayFinal;    
            }

            if (is_object($producto))
            {
                $arrayParametros["descripcionCaracteristica"] = "TRASLADO";
                $arrayParametros["descripcionProducto"]       = "INTERNET";
                $arrayParametros["nombreTecnico"]             = "INTERNET";
                $arrayParametros["valorSpc"]                  = $servicio->getId();
                $arrayParametros["estadosSpcNoConsiderados"]  = array("Eliminado");

                $objRespuestaValidacion = $this->emComercial
                                               ->getRepository('schemaBundle:InfoServicio')
                                               ->existeCaraceristicaServicio($arrayParametros);

                if (is_object($objRespuestaValidacion))
                {
                    $arrayFinal[] = array('status'  => "ERROR", 
                                          'mensaje' => "El cliente tiene una orden de Traslado de servicios en proceso, "
                                                       . "cambio de plan no permitido.");
                    return $arrayFinal;    
                }
            }

            //se agrega validación de modelo de elemento en cambios de planes TELLION CNR
            if ($strAprovisionamiento == "CNR" && $strMigradoCnr == "SI" && $strMarcaOlt === "TELLION")
            {
                if ($productoIpAnt)
                {
                    $objSpcScopeAntes = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "SCOPE", $productoIpAnt, 
                                                                                                  $arrayParamsSpc);
                    if(is_object($objSpcScopeAntes))
                    {
                        $this->servicioGeneral->actualizarServicioProdCaracts(array("objServicio"           => $servicio,
                                                                                    "objProducto"           => $productoIpAnt,
                                                                                    "strDescripcionCaract"  => "SCOPE",
                                                                                    "strEstadoNuevo"        => "Eliminado",
                                                                                    "strUsrUltMod"          => $strUsrRegulaSpc,
                                                                                    "intIdSpcNoActualizar"  => $objSpcScopeAntes->getId()));
                    }
                }
            }

            if (is_object($servicio) && !empty($servicio) && is_object($planCabNuevo) && !empty($planCabNuevo))
                {    
                    $arrayParametrosValor = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                        ->get('VALIDA_PROD_ADICIONAL', 
                                                                                 'COMERCIAL', 
                                                                                 '',
                                                                                 '',
                                                                                 'PROD_ADIC_PLANIFICA',
                                                                                 '',
                                                                                 '',
                                                                                 '',
                                                                                 '',
                                                                                 $idEmpresa);
                    if (is_array($arrayParametrosValor) && !empty($arrayParametrosValor))
                    {
                        foreach($arrayParametrosValor as $arrayParametro)
                        {
                            //buscamos en el plan instalado tenga el producto
                            $arrayParametros    =    array("Punto"      => $servicio->getPuntoId()->getId(),
                                                           "Producto"   => $arrayParametro['valor2'],
                                                           "Servicio"   => $servicio->getId(),
                                                           "Estado"     => 'Todos');
                            $arrayResultado     = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                    ->getProductoByPlanes($arrayParametros);

                            //buscamos si existe un producto en el nuevo plan
                            $arrayParametroNuevoPlan    =     array("Producto"   => $arrayParametro['valor2'],
                                                                    "Plan"       => $planCabNuevo->getId());
                            $arrayResultadoNuevo        = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                            ->getProductoByPlanes($arrayParametroNuevoPlan);

                            if(isset($arrayResultado['total']) && $arrayResultado['total'] <= 0 && isset($arrayResultadoNuevo['total']) 
                                && $arrayResultadoNuevo['total'] > 0)
                            {
                                $arrayDatosPuntoAdicional     =   array("Punto"          => $servicio->getPuntoId()->getId(),
                                                                        "Producto"       => $arrayParametro['valor2'],
                                                                        "Servicio"       => $servicio->getId(),//revisar esto
                                                                        "Observacion"    => $arrayParametro['valor3'],
                                                                        "Caracteristica" => $arrayParametro['valor4'],
                                                                        "Usuario"        => $usrCreacion,
                                                                        "Ip"             => $ipCreacion,
                                                                        "EmpresaId"      => $idEmpresa,
                                                                        "OficinaId"      => $intIdOficina);

                                $boolPlanificaAdicional = true;
                            }
                            elseif($arrayResultado['total'] > 0 && $arrayResultadoNuevo['total'] <= 0)
                            {
                                $strTipoSolicitud = "SOLICITUD PLANIFICACION";
                                $arrayParametroTipos = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->get('VALIDA_PROD_ADICIONAL', 
                                                                        'COMERCIAL', 
                                                                        '',
                                                                        'Solicitud cableado ethernet',
                                                                        '',
                                                                        '',
                                                                        '',
                                                                        '',
                                                                        '',
                                                                        '18');
                                if (is_array($arrayParametroTipos) && !empty($arrayParametroTipos))
                                {
                                    foreach($arrayParametroTipos as $parametroTipo)
                                    {
                                        if ($parametroTipo['valor1'] == $arrayParametro['valor2'])
                                        {
                                            $strTipoSolicitud = $parametroTipo['valor2'];
                                        }
                                    }
                                }
                                $objAdmiTipoSolicitudPla = $this->emComercial->getRepository("schemaBundle:AdmiTipoSolicitud")->findOneBy(
                                                                      array('descripcionSolicitud' => $strTipoSolicitud,
                                                                            'estado'               => 'Activo'));

                                if(is_object($objAdmiTipoSolicitudPla) && !empty($objAdmiTipoSolicitudPla))
                                {
                                    $arrayDatosPuntoAdicional     =   array("NuevoServicio"  => 'SI',
                                                    "EstadoServicio" => $servicio->getEstado(),
                                                    "Solicitud"      => '',
                                                    "Punto"          => $servicio->getPuntoId()->getId(),
                                                    "Producto"       => $arrayParametro['valor2'],
                                                    "Servicio"       => $servicio->getId(),//revisar esto
                                                    "Observacion"    => $arrayParametro['valor3'],
                                                    "Caracteristica" => $arrayParametro['valor4'],
                                                    "Usuario"        => $usrCreacion,
                                                    "Ip"             => $ipCreacion,
                                                    "EmpresaId"      => $idEmpresa,
                                                    "OficinaId"      => $intIdOficina,
                                                    "DowngradeCE"    => 'SI');
                                    $boolPlanificaAdicional = true;
                                }                        
                            }
                        }
                    }
                //
                }
            //

            //obtener datos necesarios para el cambio de plan
            if($producto)
            {
                //servicios adicionales
                $arrayServiciosPorPunto = $this->emComercial
                                               ->getRepository('schemaBundle:InfoServicio')
                                               ->findBy(array("puntoId" => $servicio->getPuntoId()->getId()));

                $arrayDatosIpCancelar   = $this->servicioGeneral->getInfoIpsFijaPunto(  $arrayServiciosPorPunto, $arrayProdIp, 
                                                                                        $servicio, 'Activo', 'Activo',$producto);
                $intIpFijasActivas = $arrayDatosIpCancelar['ip_fijas_activas'];

                if ($planCabViejo->getTipo() == "PRO")
                {
                    $intIpProPlan      = $this->servicioGeneral->verificarPlanTieneIp($planDetViejo, $arrayProdIp);
                    $intIpFijasActivas = $intIpFijasActivas + $intIpProPlan;
                }

                //verificar si servicio tiene ip adicional
                $flagProdAdicional = $this->servicioGeneral->verificarIpFijaEnPunto($arrayServiciosPorPunto, $arrayProdIp, $servicio);

                //antiguo
                $objSpcIndiceCliente    = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "INDICE CLIENTE", $producto, 
                                                                                                    $arrayParamsSpc);
                $objSpcSpid             = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "SPID", $producto, $arrayParamsSpc);
                $objSpcPerfil           = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "PERFIL", $producto, $arrayParamsSpc);
                $objSpcMacOnt           = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "MAC ONT", $producto, $arrayParamsSpc);

                if(is_object($objSpcIndiceCliente))
                {
                    $strIndiceCliente = $objSpcIndiceCliente->getValor();
                    $this->servicioGeneral->actualizarServicioProdCaracts(array("objServicio"           => $servicio,
                                                                                "objProducto"           => $producto,
                                                                                "strDescripcionCaract"  => "INDICE CLIENTE",
                                                                                "strEstadoNuevo"        => "Eliminado",
                                                                                "strUsrUltMod"          => $strUsrRegulaSpc,
                                                                                "intIdSpcNoActualizar"  => $objSpcIndiceCliente->getId()));
                }

                if(is_object($objSpcSpid))
                {
                    $this->servicioGeneral->actualizarServicioProdCaracts(array("objServicio"           => $servicio,
                                                                                "objProducto"           => $producto,
                                                                                "strDescripcionCaract"  => "SPID",
                                                                                "strEstadoNuevo"        => "Eliminado",
                                                                                "strUsrUltMod"          => $strUsrRegulaSpc,
                                                                                "intIdSpcNoActualizar"  => $objSpcSpid->getId()));
                }

                if(is_object($objSpcPerfil))
                {
                    $this->servicioGeneral->actualizarServicioProdCaracts(array("objServicio"           => $servicio,
                                                                                "objProducto"           => $producto,
                                                                                "strDescripcionCaract"  => "PERFIL",
                                                                                "strEstadoNuevo"        => "Eliminado",
                                                                                "strUsrUltMod"          => $strUsrRegulaSpc,
                                                                                "intIdSpcNoActualizar"  => $objSpcPerfil->getId()));
                }

                if(is_object($objSpcMacOnt))
                {
                    $this->servicioGeneral->actualizarServicioProdCaracts(array("objServicio"           => $servicio,
                                                                                "objProducto"           => $producto,
                                                                                "strDescripcionCaract"  => "MAC ONT",
                                                                                "strEstadoNuevo"        => "Eliminado",
                                                                                "strUsrUltMod"          => $strUsrRegulaSpc,
                                                                                "intIdSpcNoActualizar"  => $objSpcMacOnt->getId()));
                }

                //verificar perfil anterior
                if ($strMarcaOlt === "TELLION" && !is_object($objSpcPerfil))
                {
                    $respuestaFinal[] = array('status'  => 'ERROR',
                                              'mensaje' => 'No existe el perfil del servicio, Nofiticar a Sistemas!');
                    return $respuestaFinal;
                }

                //nuevo perfil
                $productoInternetPlanDet = $planDetNuevo[$indiceProductoInternet];
                $perfilNuevoObj          = $this->servicioGeneral->getPlanProductoCaracteristica("PERFIL", $productoInternetPlanDet, $producto);
                    //obtener perfil del nuevo plan
                if($perfilNuevoObj)
                {
                    $perfilNuevo = $perfilNuevoObj->getValor();
                    if ( $strMarcaOlt == "HUAWEI" || ($strAprovisionamiento == "CNR" && $strMigradoCnr == "SI"))
                    {
                        //Obtiene registro de parametro de perfil equivalente
                        $arrayParametrosFuncion                          = "";
                        $arrayParametrosFuncion['elementoOltId']         = $servicioTecnico->getElementoId();
                        $arrayParametrosFuncion['idPlan']                = $productoInternetPlanDet->getPlanId()->getId();
                        $arrayParametrosFuncion['valorPerfil']           = $perfilNuevo;
                        $arrayParametrosFuncion['tipoAprovisionamiento'] = $strAprovisionamiento;
                        $arrayParametrosFuncion['marca']                 = $modeloElemento->getMarcaElementoId()->getNombreMarcaElemento();
                        if ($planCabNuevo->getTipo() == "PRO")
                        {
                            if ($flagProdAdicional>0)
                            {
                                $arrayParametrosFuncion['tipoNegocio'] = 'PROIP';
                            }
                            else
                            {
                                $arrayParametrosFuncion['tipoNegocio'] = $planCabNuevo->getTipo();
                            }
                        }
                        else
                        {
                          $arrayParametrosFuncion['tipoNegocio'] = $planCabNuevo->getTipo();
                        }
                        $arrayParametrosFuncion['empresaCod']    = $idEmpresa;
                        $arrayParametrosFuncion['tipoEjecucion'] = 'FLUJO';
                        $perfilNuevo                          = $this->recursosRed->getPerfilPlanEquivalente($arrayParametrosFuncion);
                    }
                }
                else
                {
                        $respuestaFinal[] = array('status' => 'ERROR',
                                                 'mensaje' => 'No existe la relacion del perfil con el plan:' . $planCabNuevo->getNombrePlan());
                        return $respuestaFinal;
                }

                //obtener mac ont
                if(is_object($objSpcMacOnt))
                {
                    $strMacOnt  = $objSpcMacOnt->getValor();
                }
                else
                {
                    $respuestaFinal[] = array('status' => 'ERROR', 'mensaje' => 'No existe la mac ont del cliente, Favor regularizar');
                    return $respuestaFinal;
                }

                if($strMarcaOlt === "TELLION")
                {
                    //OBTENER LINE-PROFILE
                    if(is_object($objSpcPerfil))
                    {
                        $strLineProfileAntes  = $objSpcPerfil->getValor();
                    }

                    //obtener mac wifi
                    $objSpcMacWifi = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "MAC WIFI", $producto, $arrayParamsSpc);
                    if(is_object($objSpcMacWifi))
                    {
                        //cambiar formato de la mac
                        $macWifi = $objSpcMacWifi->getValor();
                        $this->servicioGeneral->actualizarServicioProdCaracts(array("objServicio"           => $servicio,
                                                                                    "objProducto"           => $producto,
                                                                                    "strDescripcionCaract"  => "MAC WIFI",
                                                                                    "strEstadoNuevo"        => "Eliminado",
                                                                                    "strUsrUltMod"          => $strUsrRegulaSpc,
                                                                                    "intIdSpcNoActualizar"  => $objSpcMacWifi->getId()));
                    }
                    else
                    {
                        //obtener mac wifi
                        $objSpcMacWifi = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "MAC", $producto, $arrayParamsSpc);

                        if($planCabNuevo->getTipo() != "HOME")
                        {
                            if(is_object($objSpcMacWifi))
                            {
                                //cambiar formato de la mac
                                $macWifi = $objSpcMacWifi->getValor();
                                $this->servicioGeneral->actualizarServicioProdCaracts(array("objServicio"           => $servicio,
                                                                                            "objProducto"           => $producto,
                                                                                            "strDescripcionCaract"  => "MAC",
                                                                                            "strEstadoNuevo"        => "Eliminado",
                                                                                            "strUsrUltMod"          => $strUsrRegulaSpc,
                                                                                            "intIdSpcNoActualizar"  => $objSpcMacWifi->getId()));
                            }
                            else
                            {
                                $respuestaFinal[] = array('status' => 'ERROR', 'mensaje' => 'NO EXISTE MAC WIFI DEL CLIENTE');
                                return $respuestaFinal;
                            }
                        }
                    }
                }
                //obtener caracteristica plan nuevo edicion limitada
                $caractEdicionLimitada     = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                  ->findOneBy(array("descripcionCaracteristica" => "EDICION LIMITADA", "estado" => "Activo"));
                $planCaractEdicionLimitada = $this->emComercial->getRepository('schemaBundle:InfoPlanCaracteristica')
                                                  ->findOneBy(array("planId"           => $planCabNuevo->getId(),
                                                                    "caracteristicaId" => $caractEdicionLimitada->getId(),
                                                                    "estado"           => "Activo"));

                //obtener caracteristica plan viejo edicion limitada
                $planViejoCaractEdicionLimitada = $this->emComercial->getRepository('schemaBundle:InfoPlanCaracteristica')
                                                       ->findOneBy(array("planId"           => $planCabViejo->getId(),
                                                                         "caracteristicaId" => $caractEdicionLimitada->getId(),
                                                                         "estado"           => "Activo"));
            }//if($producto)
            else
            {
                $arrayFinal[] = array('status' => "ERROR", 'mensaje' =>   "No existe producto INTERNET, "
                                                                        . "en el nuevo plan:" . $planCabNuevo->getNombrePlan() . " <br>"
                                                                        . "Favor Revisar!");
                return $arrayFinal;
            }

            if ($planCabViejo->getTipo() == "PYME")
            {
                $arrayRespuestaIpEnPlanViejo    = $this->servicioGeneral
                                                       ->obtieneProductoEnPlan( array(  "intIdPlan"                 => $planCabViejo->getId(),
                                                                                        "strNombreTecnicoProducto"  =>  "IP"));
                $strProductoIpEnPlanViejo       = $arrayRespuestaIpEnPlanViejo["strProductoEnPlan"];
            }
            if ($planCabNuevo->getTipo() == "PYME")
            {
                $arrayRespuestaIpEnPlanNuevo    = $this->servicioGeneral
                                                       ->obtieneProductoEnPlan( array(  "intIdPlan"                 => $planCabNuevo->getId(),
                                                                                        "strNombreTecnicoProducto"  =>  "IP"));
                $strProductoIpEnPlanNuevo       = $arrayRespuestaIpEnPlanNuevo["strProductoEnPlan"];
            }
            if ($planCabViejo->getTipo() == "PYME" && $planCabNuevo->getTipo() == "PYME")
            {
                if ($strProductoIpEnPlanViejo === "SI" && $strProductoIpEnPlanNuevo === "SI")
                {
                    $strPymesConIps = "SI";
                }
                if  ($strProductoIpEnPlanViejo === "SI" && $strProductoIpEnPlanNuevo === "NO" && empty($strConservarIp))
                {
                    $arrayFinal[] = array('status'  => "ERROR", 
                                          'mensaje' => "Plan Pyme actual incluye IP FIJA, debe seleccionar si acepta o ".
                                                       "no mantener la Ip Fija Wan, escenario soportado en Telcos+.");
                    return $arrayFinal;
                }
            }

            $objCaracteristicaTraslado = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                              ->findOneBy(array( "descripcionCaracteristica" => "TRASLADO", "estado"=>"Activo"));
            $objPcTraslado             = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                              ->findOneBy(array( "productoId"       => $producto->getId(),
                                                                 "caracteristicaId" => $objCaracteristicaTraslado->getId()));
            $objIspcTraslado           = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                              ->findOneBy(array("productoCaracterisiticaId" => $objPcTraslado->getId(), 
                                                                "valor"                     => $servicio->getId(),
                                                                "estado"                    => 'Activo'));
            if ($objIspcTraslado)
            {
                $objInfoServicioTraslado = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($objIspcTraslado->getValor());
                if($objInfoServicioTraslado)
                {
                    if(!in_array($objInfoServicioTraslado->getEstado(), $arrayEstadosTraslado))
                    {
                        $objInfoServicioNuevo = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                  ->find($objIspcTraslado->getServicioId());
                        $arrayFinal[] = array('status'  => "ERROR", 
                                      'mensaje' => "Cambio de Plan no permitido por que existe un traslado pendiente de activación. Punto Traslado: ".
                                                   $objInfoServicioNuevo->getPuntoId()->getLogin()." Estado servicio traslado: ".
                                                   $objInfoServicioNuevo->getEstado());
                        return $arrayFinal;  
                    }
                }
            }

            if($strMarcaOlt == "HUAWEI")
            {
                //obtener ont
                $elementoCliente             = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                    ->find($servicioTecnico->getElementoClienteId());
                $nombreModeloElementoCliente = $elementoCliente->getModeloElementoId()->getNombreModeloElemento();

                $arrayParametrosHuawei = $this->getParametrosElementoHuawei( $planCabNuevo, 
                                                                             $idEmpresa, 
                                                                             $elemento->getId(), 
                                                                             $idEmpresa == '26'?'HOME':$planCabNuevo->getTipo(), 
                                                                             $perfilNuevo);

                //var_dump($arrayParametrosHuawei);die();
                $strOntLineProfile = $arrayParametrosHuawei['line-profile']; //LINE-PROFILE
                $strVlan           = $arrayParametrosHuawei['vlan']; //VLAN
                $strGemPort        = $arrayParametrosHuawei['gem-port']; //GEM-PORT
                $strTrafficTable   = $arrayParametrosHuawei['traffic-table'];  //TRAFFIC-TABLE
                //
                //servicio prod caract service-profile
                $detalleElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                        ->findOneBy(array(  "detalleNombre" => "SERVICE-PROFILE-NAME",
                                                            "detalleValor"  => $nombreModeloElementoCliente, 
                                                            "elementoId"    => $elemento->getId()
                                                         )
                                                   );
                //var_dump($detalleElemento);die();
                if($detalleElemento)
                {
                    $serviceProfile    = $detalleElemento->getDetalleValor();
                    $objSpcServiceProfile   = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "SERVICE-PROFILE", $producto, 
                                                                                                        $arrayParamsSpc);
                    if (!is_object($objSpcServiceProfile))
                    {
                        //servicio prod caract service-profile
                        $this->servicioGeneral
                             ->ingresarServicioProductoCaracteristica($servicio, $producto, "SERVICE-PROFILE", $serviceProfile, $usrCreacion);
                        $objSpcServiceProfile = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "SERVICE-PROFILE", $producto);
                    }
                }
                else
                {
                    $respuestaFinal[] = array('status'  => 'ERROR', 
                                              'mensaje' => 'No existe Caracteristica SERVICE-PROFILE-NAME en el elemento, favor revisar!');
                    return $respuestaFinal;
                }

                //obtener caracteristicas antiguas
                $objSpcLineProfileNameAntes = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "LINE-PROFILE-NAME", $producto, 
                                                                                                        $arrayParamsSpc);
                $objSpcGemPortAntes         = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "GEM-PORT", $producto, 
                                                                                                        $arrayParamsSpc);
                $objSpcVlanAntes            = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "VLAN", $producto, 
                                                                                                        $arrayParamsSpc);
                $objSpcTrafficTableAntes    = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "TRAFFIC-TABLE", $producto, 
                                                                                                        $arrayParamsSpc);
                if ($productoIpAnt)
                {
                    $objSpcScopeAntes   = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "SCOPE", $productoIpAnt, 
                                                                                                    $arrayParamsSpc);
                    if(is_object($objSpcScopeAntes))
                    {
                        $this->servicioGeneral->actualizarServicioProdCaracts(array("objServicio"           => $servicio,
                                                                                    "objProducto"           => $productoIpAnt,
                                                                                    "strDescripcionCaract"  => "SCOPE",
                                                                                    "strEstadoNuevo"        => "Eliminado",
                                                                                    "strUsrUltMod"          => $strUsrRegulaSpc,
                                                                                    "intIdSpcNoActualizar"  => $objSpcScopeAntes->getId()));
                    }
                }

                if($flagMiddleware)
                {
                    //OBTENER VLAN DEL PLAN ANTERIOR
                    if(is_object($objSpcVlanAntes))
                    {
                        $strVlanAntes    = $objSpcVlanAntes->getValor();
                        $this->servicioGeneral->actualizarServicioProdCaracts(array("objServicio"           => $servicio,
                                                                                    "objProducto"           => $producto,
                                                                                    "strDescripcionCaract"  => "VLAN",
                                                                                    "strEstadoNuevo"        => "Eliminado",
                                                                                    "strUsrUltMod"          => $strUsrRegulaSpc,
                                                                                    "intIdSpcNoActualizar"  => $objSpcVlanAntes->getId()));
                    }

                    //OBTENER GEM-PORT DEL PLAN ANTERIOR
                    if(is_object($objSpcGemPortAntes))
                    {
                        $strGemPortAntes = $objSpcGemPortAntes->getValor();
                        $this->servicioGeneral->actualizarServicioProdCaracts(array("objServicio"           => $servicio,
                                                                                    "objProducto"           => $producto,
                                                                                    "strDescripcionCaract"  => "GEM-PORT",
                                                                                    "strEstadoNuevo"        => "Eliminado",
                                                                                    "strUsrUltMod"          => $strUsrRegulaSpc,
                                                                                    "intIdSpcNoActualizar"  => $objSpcGemPortAntes->getId()));
                    }

                    //OBTENER TRAFFIC-TABLE DEL PLAN ANTERIOR
                    if(is_object($objSpcTrafficTableAntes))
                    {
                        $strTrafficAntes = $objSpcTrafficTableAntes->getValor();
                        $this->servicioGeneral->actualizarServicioProdCaracts(array("objServicio"           => $servicio,
                                                                                    "objProducto"           => $producto,
                                                                                    "strDescripcionCaract"  => "TRAFFIC-TABLE",
                                                                                    "strEstadoNuevo"        => "Eliminado",
                                                                                    "strUsrUltMod"          => $strUsrRegulaSpc,
                                                                                    "intIdSpcNoActualizar"  => $objSpcTrafficTableAntes->getId()));
                    }

                    //OBTENER SERVICE PROFILE
                    if(is_object($objSpcServiceProfile))
                    {
                        $strServiceProfile = $objSpcServiceProfile->getValor();
                        $this->servicioGeneral->actualizarServicioProdCaracts(array("objServicio"           => $servicio,
                                                                                    "objProducto"           => $producto,
                                                                                    "strDescripcionCaract"  => "SERVICE-PROFILE",
                                                                                    "strEstadoNuevo"        => "Eliminado",
                                                                                    "strUsrUltMod"          => $strUsrRegulaSpc,
                                                                                    "intIdSpcNoActualizar"  => $objSpcServiceProfile->getId()));
                    }

                    //OBTENER MAC ONT
                    if(is_object($objSpcMacOnt))
                    {
                        $strMacOnt  = $objSpcMacOnt->getValor();
                    }

                    //OBTENER INDICE CLIENTE
                    if(is_object($objSpcIndiceCliente))
                    {
                        $strIndiceCliente  = $objSpcIndiceCliente->getValor();
                    }

                    //OBTENER SERVICE PORT
                    if(is_object($objSpcSpid))
                    {
                        $strSpid  = $objSpcSpid->getValor();
                    }

                    //OBTENER LINE-PROFILE DEL PLAN ANTERIOR
                    if(is_object($objSpcLineProfileNameAntes))
                    {
                        $strLineProfileAntes  = $objSpcLineProfileNameAntes->getValor();
                        $this->servicioGeneral->actualizarServicioProdCaracts(array("objServicio"           => $servicio,
                                                                                    "objProducto"           => $producto,
                                                                                    "strDescripcionCaract"  => "LINE-PROFILE-NAME",
                                                                                    "strEstadoNuevo"        => "Eliminado",
                                                                                    "strUsrUltMod"          => $strUsrRegulaSpc,
                                                                                    "intIdSpcNoActualizar"  => $objSpcLineProfileNameAntes->getId()));
                    }
                }

                //se eliminan caracteristicas viejas
                $this->servicioGeneral->actualizarServicioProdCaracts(array("objServicio"           => $servicio,
                                                                            "objProducto"           => $producto,
                                                                            "strDescripcionCaract"  => "LINE-PROFILE-NAME",
                                                                            "strEstadoNuevo"        => "Eliminado",
                                                                            "strUsrUltMod"          => $usrCreacion));
                $this->servicioGeneral->actualizarServicioProdCaracts(array("objServicio"           => $servicio,
                                                                            "objProducto"           => $producto,
                                                                            "strDescripcionCaract"  => "GEM-PORT",
                                                                            "strEstadoNuevo"        => "Eliminado",
                                                                            "strUsrUltMod"          => $usrCreacion));
                $this->servicioGeneral->actualizarServicioProdCaracts(array("objServicio"           => $servicio,
                                                                            "objProducto"           => $producto,
                                                                            "strDescripcionCaract"  => "VLAN",
                                                                            "strEstadoNuevo"        => "Eliminado",
                                                                            "strUsrUltMod"          => $usrCreacion));
                $this->servicioGeneral->actualizarServicioProdCaracts(array("objServicio"           => $servicio,
                                                                            "objProducto"           => $producto,
                                                                            "strDescripcionCaract"  => "TRAFFIC-TABLE",
                                                                            "strEstadoNuevo"        => "Eliminado",
                                                                            "strUsrUltMod"          => $usrCreacion));
                if (is_object($objSpcScopeAntes))
                {
                    if ($planCabViejo->getTipo() != "PYME" || $planCabNuevo->getTipo() != "PYME")
                    {
                        $this->servicioGeneral->actualizarServicioProdCaracts(array("objServicio"           => $servicio,
                                                                                    "objProducto"           => $productoIpAnt,
                                                                                    "strDescripcionCaract"  => "SCOPE",
                                                                                    "strEstadoNuevo"        => "Eliminado",
                                                                                    "strUsrUltMod"          => $usrCreacion));
                    }
                }

                //se agregan nuevas caracteristicas
                $this->servicioGeneral->ingresarServicioProductoCaracteristica($servicio, 
                                                                               $producto, 
                                                                               "LINE-PROFILE-NAME", 
                                                                               $strOntLineProfile, 
                                                                               $usrCreacion);

                $this->servicioGeneral->ingresarServicioProductoCaracteristica($servicio, 
                                                                               $producto, 
                                                                               "GEM-PORT", 
                                                                               $strGemPort, 
                                                                               $usrCreacion);

                $this->servicioGeneral->ingresarServicioProductoCaracteristica($servicio, 
                                                                               $producto, 
                                                                               "VLAN", 
                                                                               $strVlan, 
                                                                               $usrCreacion);

                $this->servicioGeneral->ingresarServicioProductoCaracteristica($servicio, 
                                                                               $producto, 
                                                                               "TRAFFIC-TABLE", 
                                                                               $strTrafficTable, 
                                                                               $usrCreacion);

                //indica si el plan nuevo tiene producto Ip
                $flagProdIpPlanNuevo = $this->servicioGeneral->verificarPlanTieneIp($planDetNuevo, $arrayProdIp);

                //obtener caracteristicas nuevas
                $objSpcLineProfileNameNuevo = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "LINE-PROFILE-NAME", $producto);
                $objSpcGemPortNuevo         = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "GEM-PORT", $producto);
                $objSpcVlanNuevo            = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "VLAN", $producto);
                $objSpcTrafficTableNuevo    = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "TRAFFIC-TABLE", $producto);

                if (!is_object($objSpcSpid) || !is_object($objSpcIndiceCliente))
                {
                    $respuestaFinal[] = array('status'  => 'ERROR', 
                                              'mensaje' => 'No existen las caracteristicas necesarias para realizar el cambio de plan,'.
                                                           ' Favor comunicarse con el Dpto de Sistemas.');
                    return $respuestaFinal;
                }

                if($flagMiddleware)
                {
                    //OBTENER VLAN DEL PLAN ANTERIOR
                    if(is_object($objSpcVlanNuevo))
                    {
                        $strVlanNuevo    = $objSpcVlanNuevo->getValor();
                    }

                    //OBTENER GEM-PORT DEL PLAN ANTERIOR
                    if(is_object($objSpcGemPortNuevo))
                    {
                        $strGemPortNuevo = $objSpcGemPortNuevo->getValor();
                    }

                    //OBTENER TRAFFIC-TABLE DEL PLAN ANTERIOR
                    if(is_object($objSpcTrafficTableNuevo))
                    {
                        $strTrafficNuevo = $objSpcTrafficTableNuevo->getValor();
                    }

                    //OBTENER LINE-PROFILE DEL PLAN ANTERIOR
                    if(is_object($objSpcLineProfileNameNuevo))
                    {
                        $strLineProfileNuevo  = $objSpcLineProfileNameNuevo->getValor();
                    }
                }
            }
            else if($strMarcaOlt === "ZTE")
            {
                if (empty($strCapacidad1Actual))
                {
                    $objSpcCapacidad1Actual = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "CAPACIDAD1", $producto, 
                                                                                                        $arrayParamsSpc);
                    if(is_object($objSpcCapacidad1Actual))
                    {
                        $strCapacidad1Actual = $objSpcCapacidad1Actual->getValor();
                        $this->servicioGeneral->actualizarServicioProdCaracts(array("objServicio"           => $servicio,
                                                                                    "objProducto"           => $producto,
                                                                                    "strDescripcionCaract"  => "CAPACIDAD1",
                                                                                    "strEstadoNuevo"        => "Eliminado",
                                                                                    "strUsrUltMod"          => $strUsrRegulaSpc,
                                                                                    "intIdSpcNoActualizar"  => $objSpcCapacidad1Actual->getId()));
                    }
                    else
                    {
                        $arrayRespuestaFinal[] = array('status'=>'ERROR', 'mensaje'=>'No existe característica CAPACIDAD1, favor revisar!');
                        return $arrayRespuestaFinal;
                    }
                }

                if (empty($strCapacidad2Actual))
                {
                    $objSpcCapacidad2Actual = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "CAPACIDAD2", $producto, 
                                                                                                        $arrayParamsSpc);
                    if(is_object($objSpcCapacidad2Actual))
                    {
                        $strCapacidad2Actual = $objSpcCapacidad2Actual->getValor();
                        $this->servicioGeneral->actualizarServicioProdCaracts(array("objServicio"           => $servicio,
                                                                                    "objProducto"           => $producto,
                                                                                    "strDescripcionCaract"  => "CAPACIDAD2",
                                                                                    "strEstadoNuevo"        => "Eliminado",
                                                                                    "strUsrUltMod"          => $strUsrRegulaSpc,
                                                                                    "intIdSpcNoActualizar"  => $objSpcCapacidad2Actual->getId()));
                    }
                    else
                    {
                        $arrayRespuestaFinal[] = array('status'=>'ERROR', 'mensaje'=>'No existe característica CAPACIDAD2, favor revisar!');
                        return $arrayRespuestaFinal;
                    }
                }

                if (empty($strCapacidad1Nueva) || empty($strCapacidad2Nueva))
                {
                    $arrayRespuestaFinal[] = array( 'status'    => 'ERROR', 
                                                    'mensaje'   => 'No se han podido obtener las capacidades del nuevo plan, favor revisar!');
                    return $arrayRespuestaFinal;
                }

                $objSpcServiceProfileActual = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "SERVICE-PROFILE", $producto,
                                                                                                        $arrayParamsSpc);
                if(is_object($objSpcServiceProfileActual))
                {
                    $strServiceProfile = $objSpcServiceProfileActual->getValor();
                    $this->servicioGeneral->actualizarServicioProdCaracts(array("objServicio"           => $servicio,
                                                                                "objProducto"           => $producto,
                                                                                "strDescripcionCaract"  => "SERVICE-PROFILE",
                                                                                "strEstadoNuevo"        => "Eliminado",
                                                                                "strUsrUltMod"          => $strUsrRegulaSpc,
                                                                                "intIdSpcNoActualizar"  => $objSpcServiceProfileActual->getId()));
                }
                else
                {
                    $objElementoOntCliente  = $this->emComercial->getRepository('schemaBundle:InfoElemento')
                                                                ->find($servicioTecnico->getElementoClienteId());
                    if(is_object($objElementoOntCliente))
                    {
                        $strServiceProfile = $objElementoOntCliente->getModeloElementoId()->getNombreModeloElemento();
                        $this->servicioGeneral->ingresarServicioProductoCaracteristica($servicio, $producto, "SERVICE-PROFILE", 
                                                                                       $strServiceProfile, $usrCreacion);
                    }
                    else
                    {
                        $arrayRespuestaFinal[] = array('status'=>'ERROR', 'mensaje'=>'No existe característica SERVICE-PROFILE, favor revisar!');
                        return $arrayRespuestaFinal;
                    }
                }

                $objSpcIndiceClienteActual = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "INDICE CLIENTE", $producto,
                                                                                                       $arrayParamsSpc);
                if(is_object($objSpcIndiceClienteActual))
                {
                    $strIndiceCliente = $objSpcIndiceClienteActual->getValor();
                }
                else
                {
                    $arrayRespuestaFinal[] = array('status'=>'ERROR', 'mensaje'=>'No existe característica INDICE CLIENTE, favor revisar!');
                    return $arrayRespuestaFinal;
                }

                $objSpcSpidActual = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "SPID", $producto, $arrayParamsSpc);
                if(is_object($objSpcSpidActual))
                {
                    $strSpid = $objSpcSpidActual->getValor();
                }
                else
                {
                    $arrayRespuestaFinal[] = array('status'=>'ERROR', 'mensaje'=>'No existe característica SPID, favor revisar!');
                    return $arrayRespuestaFinal;
                }

                if(is_object($productoIpAnt))
                {
                    $objSpcScopeActual  = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "SCOPE", $productoIpAnt, 
                                                                                                    $arrayParamsSpc);
                    if(is_object($objSpcScopeActual))
                    {
                        $this->servicioGeneral->actualizarServicioProdCaracts(array("objServicio"           => $servicio,
                                                                                    "objProducto"           => $productoIpAnt,
                                                                                    "strDescripcionCaract"  => "SCOPE",
                                                                                    "strEstadoNuevo"        => "Eliminado",
                                                                                    "strUsrUltMod"          => $strUsrRegulaSpc,
                                                                                    "intIdSpcNoActualizar"  => $objSpcScopeActual->getId()));
                    }
                }
            }

            if($flagMiddleware)
            {
                //OBTENER NOMBRE CLIENTE
                $objPersona         = $servicio->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId();
                $strNombreCliente   = ($objPersona->getRazonSocial() != "") ? $objPersona->getRazonSocial() : 
                                                        $objPersona->getNombres()." ".$objPersona->getApellidos();

                //OBTENER IDENTIFICACION
                $strIdentificacion  = $objPersona->getIdentificacionCliente();

                //OBTENER LOGIN
                $strLogin           = $servicio->getPuntoId()->getLogin();

                //OBTENER TIPO DE NEGOCIO
                $strTipoNegocio     = $servicio->getPuntoId()->getTipoNegocioId()->getNombreTipoNegocio();

                //OBTENER SERIE ONT
                $elementoCliente    = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                            ->find($servicioTecnico->getElementoClienteId());
                $strSerieOnt        = $elementoCliente->getSerieFisica();

                if ($idEmpresa == '26')
                {
                    $strTipoNegocio = 'HOME';
                }
                else
                {
                    //cambiar tipo de negocio, para poder hacer la solicitud de ip fija
                    $this->servicioGeneral->setTipoNegocioEnInfoPunto($punto, $planCabNuevo->getTipo(), $idEmpresa);
                }

                //CAMBIO PLAN HOME -> PYME (solicitar ip fija)
                if( ($planCabViejo->getTipo() == "HOME" && $planCabNuevo->getTipo() == "PYME" && $strProductoIpEnPlanNuevo === "SI") || 
                    ($planCabViejo->getTipo() == "PRO" && $planCabNuevo->getTipo() == "PYME" &&
                    $intIpFijasActivas == 0 && $strProductoIpEnPlanNuevo === "SI"))
                {
                    $intFlagHomePyme = 1;

                    //plan nuevo tiene ips
                    $arrayIpsObtenidas = $this->recursosRed
                                              ->getIpsDisponibleScopeOlt(1, 
                                                                         $servicioTecnico->getElementoId(), 
                                                                         $servicio->getId(), 
                                                                         $servicio->getPuntoId()->getId(), 
                                                                         "SI", 
                                                                         $planCabNuevo->getId());

                    if($arrayIpsObtenidas['error'])
                    {
                        $strStatus  = "ERROR";
                        $strMensaje = $arrayIpsObtenidas['error'];

                        //cambiar tipo de negocio, para poder hacer la solicitud de ip fija
                        $this->servicioGeneral->setTipoNegocioEnInfoPunto($punto, $planCabViejo->getTipo(), $idEmpresa);

                        $arrayFinal[]   = array('status' => $strStatus, 'mensaje' => $strMensaje);
                        return $arrayFinal;
                    }

                    //grabar ip
                    $arrayIpsRegistros = $arrayIpsObtenidas['ips'];

                    //grabar la ip en estado Reservada
                    $objIpFijaNueva  = $this->servicioGeneral
                                            ->reservarIpAdicional($arrayIpsRegistros[0]['ip'],
                                                                  $arrayIpsRegistros[0]['tipo'],
                                                                  $servicio,
                                                                  $usrCreacion,
                                                                  $ipCreacion);

                    $strIp      = $arrayIpsRegistros[0]['ip'];
                    $strScope   = $arrayIpsRegistros[0]['scope'];

                }

                //RDA debe cancelar la ip en el escenario PRO -> PYME tal cual como lo realiza para el escenario PRO -> HOME
                if($planCabViejo->getTipo() == "PRO" && 
                    ($planCabNuevo->getTipo() == "PYME" || $planCabNuevo->getTipo() == "HOME") &&
                    $strPymesConIps === "NO" &&
                    $intIpFijasActivas > 0)
                {
                    //se recupera ip ya existente
                    if ($intIpProPlan > 0)
                    {
                        $intIdServicioIpAdicional = $servicio->getId();
                        $objIpPlanPro = $this->emInfraestructura
                                                ->getRepository('schemaBundle:InfoIp')
                                                ->findOneBy(array("servicioId" => $servicio->getId(),
                                                                "estado"     => "Activo"));
                        if (is_object($objIpPlanPro))
                        {
                            $strIp = $objIpPlanPro->getIp();
                        }
                    }
                    else
                    {
                        $strIp                    = $arrayDatosIpCancelar['valores'][0]['ip'];
                        $intIdServicioIpAdicional = $arrayDatosIpCancelar['valores'][0]['id_servicio'];
                    }

                    $objServicioIpAdicional   = $this->emComercial
                                                        ->getRepository('schemaBundle:InfoServicio')
                                                        ->find($intIdServicioIpAdicional);
                    if(is_object($objServicioIpAdicional))
                    {
                        $objServicioTecnicoAdicional = $this->emInfraestructura
                                                            ->getRepository('schemaBundle:InfoServicioTecnico')
                                                            ->findOneByServicioId($objServicioIpAdicional->getId());
                        if(is_object($objServicioTecnicoAdicional))
                        {
                            $arrayScopeOlt = $this->emInfraestructura
                                                    ->getRepository('schemaBundle:InfoSubred')
                                                    ->getScopePorIpFija($strIp, $objServicioTecnicoAdicional->getElementoId());

                            if (!$arrayScopeOlt)
                            {   
                                $arrayFinal[] = array('status'  => "ERROR", 
                                                      'mensaje' => "Ip Fija del servicio no pertenece a un Scope! <br>".
                                                                   "Favor Comunicarse con el Dep. Gepon!");
                                return $arrayFinal;
                            }

                            $strScope = $arrayScopeOlt['NOMBRE_SCOPE'];
                        }
                    }
                }

                //QUITAR _1 o _5 DEL PERFIL PARA ENVIAR EL MIDDLEWARE
                if($elemento->getModeloElementoId()->getNombreModeloElemento() == 'EP-3116')
                {
                    $arrayPerfil         = explode("_", $strLineProfileAntes);
                    $strLineProfileAntes = $arrayPerfil[0]."_".$arrayPerfil[1];

                    $arrayPerfil         = explode("_", $perfilNuevo);
                    $strLineProfileNuevo = $arrayPerfil[0]."_".$arrayPerfil[1];
                }

                if($strCambioPlanPymeAHome === "SI")
                {
                    $arrayIpCancelarPymeAHome   = array();
                    $objIpWanEnServicioAdic     = null;
                    $objIpWanEnPlanPyme         = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                                          ->findOneBy(array("servicioId" => $servicio->getId(),
                                                                                            "estado"     => "Activo"));
                    if(is_object($objIpWanEnPlanPyme))
                    {
                        $strIp                      = $objIpWanEnPlanPyme->getIp();
                        $arrayScopeOltIpWanEnPlan   = $this->emInfraestructura
                                                           ->getRepository('schemaBundle:InfoSubred')
                                                           ->getScopePorIpFija($objIpWanEnPlanPyme->getIp(), $elemento->getId());
                        if(!$arrayScopeOltIpWanEnPlan)
                        {
                            $arrayFinal[] = array('status'  => "ERROR", 
                                                  'mensaje' => "Ip Fija del servicio no pertenece a un Scope! <br>".
                                                               "Favor Comunicarse con el Dep. Gepon!");
                            return $arrayFinal;
                        }
                        $strScope                   = $arrayScopeOltIpWanEnPlan['NOMBRE_SCOPE'];
                        $strTipoPlanActualPymeAHome = "con_ip";
                        $strIpFijaWanPymeAHome      = "";
                        $strTipoPlanNuevoPymeAHome  = "sin_ip";
                    }
                    else
                    {
                        $strTipoPlanActualPymeAHome         = "sin_ip";
                        $strIpFijaWanPymeAHome              = "0";
                        $strTipoPlanNuevoPymeAHome          = "sin_ip";
                        $arrayParamsIpWanServAdicional      = array('objPunto'       => $servicio->getPuntoId(),
                                                                    'strEmpresaCod'  => $idEmpresa,
                                                                    'strUsrCreacion' => $usrCreacion,
                                                                    'strIpCreacion'  => $ipCreacion);
                        $arrayRespuestaIpWanServicioAdic    = $this->servicioGeneral->getIpFijaWan($arrayParamsIpWanServAdicional);
                        if(isset($arrayRespuestaIpWanServicioAdic['strStatus']) && !empty($arrayRespuestaIpWanServicioAdic['strStatus'])
                            && $arrayRespuestaIpWanServicioAdic['strStatus'] === 'OK' && isset($arrayRespuestaIpWanServicioAdic['strExisteIpWan']) 
                            && !empty($arrayRespuestaIpWanServicioAdic['strExisteIpWan']) 
                            && $arrayRespuestaIpWanServicioAdic['strExisteIpWan'] === 'SI'
                          )
                        {
                            $strIp                  = $arrayRespuestaIpWanServicioAdic['arrayInfoIp']['strIp'];
                            $strScope               = $arrayRespuestaIpWanServicioAdic['arrayInfoIp']['strScope'];
                            $strIpFijaWanPymeAHome  = "1";
                            $objIpWanEnServicioAdic = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                           ->findOneBy(array("servicioId" => 
                                                                             $arrayRespuestaIpWanServicioAdic['arrayInfoIp']['intIdServicioIp'],
                                                                             "estado"     => "Activo"));
                        }
                    }
                    $intIpsCancelarPymeAHome    = $arrayDatosIpCancelar['ip_fijas_activas'];
                    if($intIpsCancelarPymeAHome > 0)
                    {
                        $arrayIpCancelarPymeAHome   = $arrayDatosIpCancelar['valores'];
                    }
                }
                /* si el plan pyme actual tiene ip y el usuario no desee conservar la ip incluída debo enviar la ip y el scope y RDA debe cancelar
                   esa ip, en telcos se elimina el registro de ip y la caracteristica scope */
                else if (!empty($strConservarIp))
                {
                    $objIpPlanPro = $this->emInfraestructura
                                         ->getRepository('schemaBundle:InfoIp')
                                         ->findOneBy(array("servicioId" => $servicio->getId(),
                                                           "estado"     => "Activo"));
                    if (is_object($objIpPlanPro) && $strConservarIp === "NO")
                    {
                        $strIp = $objIpPlanPro->getIp();
                    }
                    $objServicioTecnicoAdicional = $this->emInfraestructura
                                                        ->getRepository('schemaBundle:InfoServicioTecnico')
                                                        ->findOneByServicioId($servicio->getId());
                    if(is_object($objServicioTecnicoAdicional) && is_object($objIpPlanPro))
                    {
                        $arrayScopeOlt = $this->emInfraestructura
                                              ->getRepository('schemaBundle:InfoSubred')
                                              ->getScopePorIpFija($objIpPlanPro->getIp(), $objServicioTecnicoAdicional->getElementoId());

                        if (!$arrayScopeOlt)
                        {
                            $arrayFinal[] = array('status'  => "ERROR", 
                                                  'mensaje' => "Ip Fija del servicio no pertenece a un Scope! <br>".
                                                               "Favor Comunicarse con el Dep. Gepon!");
                            return $arrayFinal;
                        }
                        $strScopeConservaIp = $arrayScopeOlt['NOMBRE_SCOPE'];
                        if ($strConservarIp === "NO")
                        {
                            $strScope = $strScopeConservaIp;
                        }                    
                    }
                }

                $arrayDatos = array(
                                        'serial_ont'            => $strSerieOnt,
                                        'mac_ont'               => $strMacOnt,
                                        'nombre_olt'            => $elemento->getNombreElemento(),
                                        'ip_olt'                => $objIpElemento->getIp(),
                                        'puerto_olt'            => $interfaceElemento->getNombreInterfaceElemento(),
                                        'modelo_olt'            => $modeloElemento->getNombreModeloElemento(),
                                        'gemport'               => $strGemPortAntes,
                                        'service_profile'       => $strServiceProfile,
                                        'line_profile'          => $strLineProfileAntes,
                                        'traffic_table'         => $strTrafficAntes,
                                        'ont_id'                => $strIndiceCliente,
                                        'service_port'          => $strSpid,
                                        'vlan'                  => $strVlanAntes,
                                        'estado_servicio'       => $servicio->getEstado(),
                                        'mac_wifi'              => $macWifi,
                                        'tipo_negocio_actual'   => $strTipoNegocio,
                                        'line_profile_nuevo'    => $strLineProfileNuevo,
                                        'gemport_nuevo'         => $strGemPortNuevo,
                                        'traffic_table_nueva'   => $strTrafficNuevo,
                                        'tipo_negocio_nuevo'    => $idEmpresa == '26'?'HOME':$planCabNuevo->getTipo(),
                                        'vlan_nueva'            => $strVlanNuevo,
                                        'ip'                    => $strIp,
                                        'scope'                 => $strScope,
                                        'ip_fijas_activas'      => $intIpFijasActivas,
                                        'capacidad_up'          => $strCapacidad1Actual,
                                        'capacidad_down'        => $strCapacidad2Actual,
                                        'capacidad_up_nueva'    => $strCapacidad1Nueva,
                                        'capacidad_down_nueva'  => $strCapacidad2Nueva
                                    );
                if ($strPrefijoEmpresaFlujo === 'MD' || $strPrefijoEmpresaFlujo === 'EN')
                {
                    if($strCambioPlanPymeAHome === "SI")
                    {
                        $arrayDatos["tipo_plan_actual"] = $strTipoPlanActualPymeAHome;
                        $arrayDatos["ip_fija_wan"]      = $strIpFijaWanPymeAHome;
                        $arrayDatos["tipo_plan_nuevo"]  = $strTipoPlanNuevoPymeAHome;
                        $arrayDatos["ip_cancelar"]      = $arrayIpCancelarPymeAHome;
                    }
                    else
                    {
                        $arrayRespuestaSeteaInfo = $this->servicioGeneral
                                                        ->seteaInformacionPlanesPyme(array("intIdPlan"         => $servicio->getPlanId()->getId(),
                                                                                           "intIdPlanNuevo"    => $planCabNuevo->getId(),
                                                                                           "intIdPunto"        => $servicio->getPuntoId()->getId(),
                                                                                           "strConservarIp"    => $strConservarIp,
                                                                                           "strTipoNegocio"    => $planCabNuevo->getTipo(),
                                                                                           "strPrefijoEmpresa" => $strPrefijoEmpresaFlujo,
                                                                                           "strUsrCreacion"    => $usrCreacion,
                                                                                           "strIpCreacion"     => $ipCreacion,
                                                                                           "strTipoProceso"    => "CAMBIAR_PLAN",
                                                                                           "arrayInformacion"  => $arrayDatos,
                                                                                           "strTipoNegocioAnterior" => $planCabViejo->getTipo()));
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
                }

                $arrayDatosMiddleware = array(
                                                'nombre_cliente'        => $strNombreCliente,
                                                'login'                 => $strLogin,
                                                'identificacion'        => $strIdentificacion,
                                                'datos'                 => $arrayDatos,
                                                'opcion'                => $this->opcion,
                                                'ejecutaComando'        => $this->ejecutaComando,
                                                'usrCreacion'           => $usrCreacion,
                                                'ipCreacion'            => $ipCreacion,
                                                'empresa'               => $strPrefijoEmpresa,
                                            );

                $arrayFinal = $this->rdaMiddleware->middleware(json_encode($arrayDatosMiddleware));

                $statusFinal  = $arrayFinal['status'];
                $strMensaje   = $arrayFinal['mensaje'];
                $mensajeFinal = $strIndiceCliente;
                
                $this->utilService->insertError('Telcos+', 
                                                'InfoCambiarPlanService.cambiarPlan', 
                                                $strMensaje, 
                                                $usrCreacion, 
                                                $ipCreacion
                                               );
                $arrayDatosConfirmacionTn                           = $arrayDatos;
                $arrayDatosConfirmacionTn['opcion_confirmacion']    = $this->opcion;
                $arrayDatosConfirmacionTn['respuesta_confirmacion'] = 'ERROR'; 
                $arrayDataConfirmacionTn    = array('nombre_cliente'    => $strNombreCliente,
                                                    'login'             => $strLogin,
                                                    'identificacion'    => $strIdentificacion,
                                                    'datos'             => $arrayDatosConfirmacionTn,
                                                    'opcion'            => $this->strConfirmacionTNMiddleware,
                                                    'ejecutaComando'    => $this->ejecutaComando,
                                                    'usrCreacion'       => $usrCreacion,
                                                    'ipCreacion'        => $ipCreacion,
                                                    'empresa'           => $strPrefijoEmpresaFlujo,
                                                    'statusMiddleware'  => $statusFinal);
                
                if($intFlagHomePyme == 1)
                {
                    if($statusFinal == "OK")
                    {
                        //se activa ip en la base
                        $objIpFijaNueva->setEstado("Activo");
                        $this->emInfraestructura->persist($objIpFijaNueva);
                        $this->emInfraestructura->flush();
                        $this->servicioGeneral->ingresarServicioProductoCaracteristica($servicio, 
                                                                                       $productoIp, 
                                                                                       "SCOPE", 
                                                                                       $strScope, 
                                                                                       $usrCreacion);
                    }
                    else
                    {
                        //se elimina la ip en la base
                        $objIpFijaNueva->setEstado("Eliminado");
                        $this->emInfraestructura->persist($objIpFijaNueva);
                        $this->emInfraestructura->flush();
                    }
                }
            }
            else
            {
                if($strMarcaOlt === "ZTE")
                {
                    $arrayFinal[]   = array('status'    => "ERROR",
                                            'mensaje'   => "El OLT considerado no soporta el esquema del middleware. "
                                                           . "Favor Comunicarse con Sistemas!");
                    return $arrayFinal;
                }
                //crear arreglo de datos necesarios para invocar a los diferentes cambios de plan
                $arrayPeticiones[] = array(
                    'punto'                             => $punto,
                    'login'                             => $login,
                    'servicio'                          => $servicio,
                    'servicioTecnico'                   => $servicioTecnico,
                    'planCabViejo'                      => $planCabViejo,
                    'planDetViejo'                      => $planDetViejo,
                    'planCabNuevo'                      => $planCabNuevo,
                    'planDetNuevo'                      => $planDetNuevo,
                    'interfaceElemento'                 => $interfaceElemento,
                    'modeloElemento'                    => $modeloElemento,
                    'macOnt'                            => $strMacOnt,
                    'perfilNuevo'                       => $perfilNuevo,
                    'producto'                          => $producto,
                    'productoIp'                        => $productoIp,
                    'planCaractEdicionLimitada'         => $planCaractEdicionLimitada,
                    'planViejoCaractEdicionLimitada'    => $planViejoCaractEdicionLimitada,
                    'servProdCaractIndiceCliente'       => $objSpcIndiceCliente,
                    'servProdCaracMacOnt'               => $objSpcMacOnt,
                    'servProdCaracMacWifi'              => $objSpcMacWifi,
                    'servProdCaracPerfil'               => $objSpcPerfil,
                    'arrayProdIp'                       => $arrayProdIp,
                    'arrayProdInternet'                 => $arrayProdInternet,
                    'usrCreacion'                       => $usrCreacion,
                    'ipCreacion'                        => $ipCreacion,
                    'idEmpresa'                         => $idEmpresa,
                    'spid'                              => $objSpcSpid,
                    'serviceProfile'                    => $objSpcServiceProfile,
                    'lineProfile'                       => $objSpcLineProfileNameNuevo,
                    'vlan'                              => $objSpcVlanNuevo,
                    'gemPort'                           => $objSpcGemPortNuevo,
                    'trafficTable'                      => $objSpcTrafficTableNuevo,
                    'scopeAntes'                        => $objSpcScopeAntes,
                    'lineProfileAntes'                  => $objSpcLineProfileNameAntes,
                    'gemPortAntes'                      => $objSpcGemPortAntes,
                    'vlanAntes'                         => $objSpcVlanAntes,     
                    'trafficTableAntes'                 => $objSpcTrafficTableAntes
                );

                switch($planCabViejo->getTipo())
                {
                    case "HOME":
                        if ($strMarcaOlt == "TELLION")
                        {
                            if ($strMigradoCnr == "NO")
                            {
                                $arrayRespuesta = $this->cambioPlanHomeTellion($arrayPeticiones);
                            }
                            else
                            {
                                $arrayRespuesta = $this->cambioPlanHomeTellionCnr($arrayPeticiones);
                            }
                        }
                        else
                        {
                            $arrayRespuesta = $this->cambioPlanHomeHuawei($arrayPeticiones);                    
                        }

                        $statusFinal  = $arrayRespuesta[0]['status'];
                        $mensajeFinal = $arrayRespuesta[0]['mensaje'];
                        break;
                    case "PRO":
                        if ($strMarcaOlt == "TELLION")
                        {
                            if ($strMigradoCnr == "NO")
                            {
                                $arrayRespuesta = $this->cambioPlanProTellion($arrayPeticiones);
                            }
                            else
                            {
                                $arrayRespuesta = $this->cambioPlanProTellionCnr($arrayPeticiones);
                            }
                        }
                        else
                        {
                            $arrayRespuesta = $this->cambioPlanProHuawei($arrayPeticiones);
                        }

                        $statusFinal  = $arrayRespuesta[0]['status'];
                        $mensajeFinal = $arrayRespuesta[0]['mensaje'];
                        break;
                    case "PYME":
                        if ($strMarcaOlt == "TELLION")
                        {
                            if ($strMigradoCnr == "NO")
                            {
                                //validación de perfiles d eplanes previo cambio de plan pyme tellion pool
                                if (substr($arrayPeticiones[0]['perfilNuevo'],
                                           0,
                                           strlen($arrayPeticiones[0]['perfilNuevo'])-2) ==
                                    substr($arrayPeticiones[0]['servProdCaracPerfil']->getValor(),
                                           0,
                                           strlen($arrayPeticiones[0]['servProdCaracPerfil']->getValor())-2)
                                   )
                                {
                                    $ejecutaScriptsOlt = "NO";

                                }
                                //si son perfiles diferentes se ejecuta el cambio de plan en equipos y se actualizan caracteristicas de servicio
                                if ($ejecutaScriptsOlt == "SI")
                                {
                                    $arrayRespuesta = $this->cambioPlanPymeTellion($arrayPeticiones);
                                }
                                else
                                {
                                    $arrayRespuesta[0]['status']  = "OK";
                                    $arrayRespuesta[0]['mensaje'] = "";
                                }
                            }
                            else
                            {
                                $arrayRespuesta = $this->cambioPlanPymeTellionCnr($arrayPeticiones);
                            }
                        }
                        else
                        {
                            $arrayRespuesta = $this->cambioPlanPymeHuawei($arrayPeticiones);
                        }

                        $statusFinal  = $arrayRespuesta[0]['status'];
                        $mensajeFinal = $arrayRespuesta[0]['mensaje'];
                        break;
                }//switch($planCabViejo->getTipo())
            }

            if($statusFinal == "OK")
            {
                if($strMarcaOlt == "TELLION" && $ejecutaScriptsOlt == "SI")
                {    
                    $indiceNuevo = $mensajeFinal;

                    //caracteristicas viejas
                    $this->servicioGeneral->actualizarServicioProdCaracts(array("objServicio"           => $servicio,
                                                                                "objProducto"           => $producto,
                                                                                "strDescripcionCaract"  => "PERFIL",
                                                                                "strEstadoNuevo"        => "Eliminado",
                                                                                "strUsrUltMod"          => $usrCreacion));
                    if ($flagMiddleware)
                    {
                        $this->servicioGeneral->actualizarServicioProdCaracts(array("objServicio"           => $servicio,
                                                                                    "objProducto"           => $producto,
                                                                                    "strDescripcionCaract"  => "INDICE CLIENTE",
                                                                                    "strEstadoNuevo"        => "Eliminado",
                                                                                    "strUsrUltMod"          => $usrCreacion));
                    }

                    //caracteristicas nuevas
                    $this->servicioGeneral
                         ->ingresarServicioProductoCaracteristica($servicio, $producto, "INDICE CLIENTE", $indiceNuevo, $usrCreacion);

                    //servicios adicionales
                    $arrayServiciosPorPunto = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                   ->findBy(array("puntoId" => $servicio->getPuntoId()->getId()));
                    //verificar si servicio tiene ip adicional
                    $flagProdAdicional      = $this->servicioGeneral->verificarIpFijaEnPunto($arrayServiciosPorPunto, $arrayProdIp, $servicio);
                    if($flagProdAdicional>0 && $planCabNuevo->getTipo()=="PYME" && $planCabViejo->getTipo()=="PYME")
                    {
                        //si tiene ips adicionales el perfil debe ser _5
                        $perfilNuevo        = substr($perfilNuevo,0,strlen($perfilNuevo)-2)."_5";
                    }

                    $this->servicioGeneral
                         ->ingresarServicioProductoCaracteristica($servicio, $producto, "PERFIL", $perfilNuevo, $usrCreacion);
                }
                else if($strMarcaOlt === "ZTE")
                {
                    $this->servicioGeneral->actualizarServicioProdCaracts(array("objServicio"           => $servicio,
                                                                                "objProducto"           => $producto,
                                                                                "strDescripcionCaract"  => "CAPACIDAD1",
                                                                                "strEstadoNuevo"        => "Eliminado",
                                                                                "strUsrUltMod"          => $usrCreacion));
                    //GRABAMOS CAPACIDAD1 DEL NUEVO PLAN
                    $this->servicioGeneral->ingresarServicioProductoCaracteristica( $servicio, 
                                                                                   $producto, 
                                                                                   "CAPACIDAD1", 
                                                                                   $strCapacidad1Nueva, 
                                                                                   $usrCreacion );

                    $this->servicioGeneral->actualizarServicioProdCaracts(array("objServicio"           => $servicio,
                                                                                "objProducto"           => $producto,
                                                                                "strDescripcionCaract"  => "CAPACIDAD2",
                                                                                "strEstadoNuevo"        => "Eliminado",
                                                                                "strUsrUltMod"          => $usrCreacion));
                    //GRABAMOS CAPACIDAD2 DEL NUEVO PLAN
                    $this->servicioGeneral->ingresarServicioProductoCaracteristica( $servicio, 
                                                                                   $producto, 
                                                                                   "CAPACIDAD2", 
                                                                                   $strCapacidad2Nueva, 
                                                                                   $usrCreacion );

                    if (is_object($objSpcScopeActual) && ($planCabViejo->getTipo() !== "PYME" || $planCabNuevo->getTipo() !== "PYME"))
                    {
                        $this->servicioGeneral->actualizarServicioProdCaracts(array("objServicio"           => $servicio,
                                                                                    "objProducto"           => $productoIpAnt,
                                                                                    "strDescripcionCaract"  => "SCOPE",
                                                                                    "strEstadoNuevo"        => "Eliminado",
                                                                                    "strUsrUltMod"          => $usrCreacion));
                    }

                    $this->servicioGeneral->actualizarServicioProdCaracts(array("objServicio"           => $servicio,
                                                                                "objProducto"           => $producto,
                                                                                "strDescripcionCaract"  => "VLAN",
                                                                                "strEstadoNuevo"        => "Eliminado",
                                                                                "strUsrUltMod"          => $usrCreacion));
                    //GRABAMOS VLAN DEL NUEVO PLAN
                    $this->servicioGeneral->ingresarServicioProductoCaracteristica( $servicio, 
                                                                                   $producto, 
                                                                                   "VLAN", 
                                                                                   $arrayFinal['vlan'], 
                                                                                   $usrCreacion );

                    $this->servicioGeneral->actualizarServicioProdCaracts(array("objServicio"           => $servicio,
                                                                                "objProducto"           => $producto,
                                                                                "strDescripcionCaract"  => "CLIENT CLASS",
                                                                                "strEstadoNuevo"        => "Eliminado",
                                                                                "strUsrUltMod"          => $usrCreacion));
                    //GRABAMOS CLIENT CLASS DEL NUEVO PLAN
                    $this->servicioGeneral->ingresarServicioProductoCaracteristica( $servicio, 
                                                                                    $producto, 
                                                                                    "CLIENT CLASS", 
                                                                                    $arrayFinal['client_class'], 
                                                                                    $usrCreacion );

                    $this->servicioGeneral->actualizarServicioProdCaracts(array("objServicio"           => $servicio,
                                                                                "objProducto"           => $producto,
                                                                                "strDescripcionCaract"  => "PACKAGE ID",
                                                                                "strEstadoNuevo"        => "Eliminado",
                                                                                "strUsrUltMod"          => $usrCreacion));
                    //GRABAMOS PCKID DEL NUEVO PLAN
                    $this->servicioGeneral->ingresarServicioProductoCaracteristica( $servicio, 
                                                                                    $producto, 
                                                                                    "PACKAGE ID", 
                                                                                    $arrayFinal['pckid'], 
                                                                                    $usrCreacion );

                    $this->servicioGeneral->actualizarServicioProdCaracts(array("objServicio"           => $servicio,
                                                                                "objProducto"           => $producto,
                                                                                "strDescripcionCaract"  => "LINE-PROFILE-NAME",
                                                                                "strEstadoNuevo"        => "Eliminado",
                                                                                "strUsrUltMod"          => $usrCreacion));
                    //GRABAMOS LINE PROFILE NAME DEL NUEVO PLAN
                    $this->servicioGeneral->ingresarServicioProductoCaracteristica( $servicio, 
                                                                                    $producto, 
                                                                                    "LINE-PROFILE-NAME", 
                                                                                    $arrayFinal['line_profile'], 
                                                                                    $usrCreacion );
                }

                if( $flagMiddleware && $planCabViejo->getTipo() == "PRO" && 
                   ($planCabNuevo->getTipo() == "PYME" || $planCabNuevo->getTipo() == "HOME") && 
                   $intIpFijasActivas > 0)
                {
                    if ($intIpProPlan === 0)
                    {
                        $estadoServicioAdicional = "Cancel";
                        $observacionAdicional = "Se cancelo servicio adicional, por cambio de plan";
                        if($objServicioIpAdicional->getEstado()=="PreAsignacionInfoTecnica" || 
                            $objServicioIpAdicional->getEstado()=="Asignada")
                        {
                            $estadoServicioAdicional = "Anulado";
                            $observacionAdicional = "Se Anulo servicio adicional, por cambio de plan";

                        }                                    
                        //cambiar estado al servicio adicional
                        $objServicioIpAdicional->setEstado($estadoServicioAdicional);
                        $this->emComercial->persist($objServicioIpAdicional);
                        $this->emComercial->flush();

                        //historial del servicio adicional
                        $servicioHistorial = new InfoServicioHistorial();
                        $servicioHistorial->setServicioId($objServicioIpAdicional);
                        $servicioHistorial->setObservacion($observacionAdicional);
                        $servicioHistorial->setEstado($estadoServicioAdicional);
                        $servicioHistorial->setUsrCreacion($usrCreacion);
                        $servicioHistorial->setFeCreacion(new \DateTime('now'));
                        $servicioHistorial->setIpCreacion($ipCreacion);
                        $this->emComercial->persist($servicioHistorial);
                        $this->emComercial->flush();
                    }

                    $objIpAdicional = $this->emInfraestructura
                                           ->getRepository('schemaBundle:InfoIp')
                                           ->findOneBy(array("servicioId" => $objServicioIpAdicional->getId(),
                                                             "estado"     => "Activo"));
                    if(is_object($objIpAdicional))
                    {
                        $objIpAdicional->setEstado("Eliminado");
                        $this->emInfraestructura->persist($objIpAdicional);
                        $this->emInfraestructura->flush();
                    }
                }

                //frecuencia
                //obtener caracteristica de frecuencia
                $caractFrecuencia = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                         ->findOneBy(array("descripcionCaracteristica" => "FRECUENCIA", "estado" => "Activo"));
                $planCaractFrecuencia = $this->emComercial->getRepository('schemaBundle:InfoPlanCaracteristica')
                                             ->findOneBy(array("planId"           => $planCabNuevo->getId(),
                                                               "caracteristicaId" => $caractFrecuencia->getId(),
                                                               "estado"           => "Activo"));
                $frecuencia = 0;
                if($planCaractFrecuencia)
                {
                    $frecuencia = $planCaractFrecuencia->getValor();
                }

                //promociones
                //eliminar promociones
                $arrayPromocionesServicio = $this->emComercial->getRepository('schemaBundle:InfoServicioPlanCaract')
                                                 ->findBy(array("servicioId" => $servicio->getId(), "estado" => "Activo"));
                if(count($arrayPromocionesServicio)>0)
                {
                    for($i=0;$i<count($arrayPromocionesServicio);$i++)
                    {
                        $promocionesServicio = $arrayPromocionesServicio[$i];

                        //cancelar promociones
                        $promocionesServicio->setEstado("Cancel");
                        $this->emComercial->persist($promocionesServicio);
                        $this->emComercial->flush();
                    }

                    //historial del servicio
                    $servicioHistorial = new InfoServicioHistorial();
                    $servicioHistorial->setServicioId($servicio);
                    $servicioHistorial->setObservacion("Se cancelaron las promociones activas del plan anterior");
                    $servicioHistorial->setEstado("Activo");
                    $servicioHistorial->setUsrCreacion($usrCreacion);
                    $servicioHistorial->setFeCreacion(new \DateTime('now'));
                    $servicioHistorial->setIpCreacion($ipCreacion);
                    $this->emComercial->persist($servicioHistorial);
                    $this->emComercial->flush();
                }//end if(count($arrayPromocionesServicio)>0)

                //agregar promociones
                $arrayPlanCaract = $this->emComercial->getRepository('schemaBundle:InfoPlanCaracteristica')
                                        ->findBy(array("planId" => $planCabNuevo->getId(), "estado" => "Activo"));
                for($i=0;$i<count($arrayPlanCaract);$i++)
                {
                    $planCaract = $arrayPlanCaract[$i];

                    $servicioPlanCaract = new InfoServicioPlanCaract();
                    $servicioPlanCaract->setServicioId($servicio->getId());
                    $servicioPlanCaract->setPlanCaracteristicaId($planCaract->getId());
                    $servicioPlanCaract->setValor($planCaract->getValor());
                    $servicioPlanCaract->setEstado("Activo");
                    $servicioPlanCaract->setUsrCreacion($usrCreacion);
                    $servicioPlanCaract->setFeCreacion(new \DateTime('now'));
                    $this->emComercial->persist($servicioPlanCaract);
                    $this->emComercial->flush();
                }

                //genero solicitud de instalación de nuevo equipo SmartWifi
                if($strPlanViejoTieneSmartWifi == 'NO' && $strPlanNuevoTieneSmartWifi == 'SI')
                {
                    //generar solicitud con caracteristica que indique si el servicio que asigna responsable es un smartwifi
                    $objTipoSolicitudAgregarEquipo = $this->emComercial
                                                          ->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                          ->findOneByDescripcionSolicitud('SOLICITUD AGREGAR EQUIPO');

                    if (!is_object($objTipoSolicitudAgregarEquipo))
                    {
                        throw new \Exception("No se encontro información acerca del tipo de solicitud de planificacion");
                    }

                    $objAdmiCaracteristicaSmartWifi = $this->emComercial
                                                           ->getRepository("schemaBundle:AdmiCaracteristica")
                                                           ->findOneBy(array('descripcionCaracteristica' => 'SMART WIFI',
                                                                             'estado'                    => 'Activo'));
                    if (!is_object($objAdmiCaracteristicaSmartWifi))
                    {
                        throw new \Exception("No se encontro información acerca de caracteristica SMART WIFI");
                    }

                    $objDatosNumeracion    = $this->emComercial
                                                  ->getRepository('schemaBundle:AdmiNumeracion')
                                                  ->findByEmpresaYOficina($idEmpresa,$intIdOficina,'ORD');

                    if (!is_object($objDatosNumeracion))
                    {
                        throw new \Exception("No se generó la numeración correctamente");
                    }

                    $strSecuencia_asig     = str_pad($objDatosNumeracion->getSecuencia(),7, '0', STR_PAD_LEFT);
                    $strNumeroDeContrato   = $objDatosNumeracion->getNumeracionUno().'-'.
                                             $objDatosNumeracion->getNumeracionDos().'-'.
                                             $strSecuencia_asig;

                    $objOrdenTrabajo  = new InfoOrdenTrabajo();
                    $objOrdenTrabajo->setPuntoId($punto);
                    $objOrdenTrabajo->setTipoOrden('N');
                    $objOrdenTrabajo->setNumeroOrdenTrabajo($strNumeroDeContrato);
                    $objOrdenTrabajo->setFeCreacion(new \DateTime('now'));
                    $objOrdenTrabajo->setUsrCreacion($usrCreacion);
                    $objOrdenTrabajo->setIpCreacion($ipCreacion);
                    $objOrdenTrabajo->setOficinaId($intIdOficina);
                    $objOrdenTrabajo->setEstado('Pendiente');
                    $this->emComercial->persist($objOrdenTrabajo);
                    $this->emComercial->flush();

                    $intNumeroAct = ($objDatosNumeracion->getSecuencia()+1);
                    $objDatosNumeracion->setSecuencia($intNumeroAct);
                    $this->emComercial->persist($objDatosNumeracion);
                    $this->emComercial->flush();

                    $objDetalleSolicitud  = new InfoDetalleSolicitud();
                    $objDetalleSolicitud->setServicioId($servicio);
                    $objDetalleSolicitud->setTipoSolicitudId($objTipoSolicitudAgregarEquipo);
                    $objDetalleSolicitud->setEstado('PrePlanificada');
                    $objDetalleSolicitud->setUsrCreacion($usrCreacion);
                    $objDetalleSolicitud->setFeCreacion(new \DateTime('now'));
                    $this->emComercial->persist($objDetalleSolicitud);
                    $this->emComercial->flush();

                    $objDetalleSolHist = new InfoDetalleSolHist();
                    $objDetalleSolHist->setDetalleSolicitudId($objDetalleSolicitud);
                    $objDetalleSolHist->setIpCreacion($ipCreacion);
                    $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
                    $objDetalleSolHist->setUsrCreacion($usrCreacion);
                    $objDetalleSolHist->setEstado('PrePlanificada');
                    $this->emComercial->persist($objDetalleSolHist);
                    $this->emComercial->flush();

                    $objDetalleSolCaract= new InfoDetalleSolCaract();
                    $objDetalleSolCaract->setCaracteristicaId($objAdmiCaracteristicaSmartWifi);
                    $objDetalleSolCaract->setDetalleSolicitudId($objDetalleSolicitud);
                    $objDetalleSolCaract->setValor("SI");
                    $objDetalleSolCaract->setEstado("PrePlanificada");
                    $objDetalleSolCaract->setUsrCreacion($usrCreacion);
                    $objDetalleSolCaract->setFeCreacion(new \DateTime('now'));
                    $this->emComercial->persist($objDetalleSolCaract);
                    $this->emComercial->flush();
                }
                //genero retiro de equipo SmartWifi que incluia el plan anterior
                else if ($strPlanViejoTieneSmartWifi == 'SI' && $strPlanNuevoTieneSmartWifi == 'NO')
                {
                    //invocar metodo que recupera primer smart wifi (Equipo del plan)
                    $arrayParams                                   = array();
                    $arrayParams['intInterfaceElementoConectorId'] = $servicioTecnico->getInterfaceElementoClienteId();
                    $arrayParams['arrayData']                      = array();
                    $arrayParams['strBanderaReturn']               = 'INTERFACE';
                    $arrayParams['strTipoSmartWifi']               = 'SmartWifi';
                    $arrayParams['strRetornaPrimerWifi']           = 'SI';
                    $objInterfaceElementoAnteriorSmartWifi         = $this->emInfraestructura
                                                                          ->getRepository('schemaBundle:InfoElemento')
                                                                          ->getElementosSmartWifiByInterface($arrayParams);

                    if (!is_object($objInterfaceElementoAnteriorSmartWifi))
                    {
                        throw new \Exception("No se encontro información acerca de equipo SmartWifi del cliente");
                    }

                    $objEnlaceCliente = $this->emInfraestructura
                                             ->getRepository('schemaBundle:InfoEnlace')
                                             ->findOneBy(array("interfaceElementoFinId" => $objInterfaceElementoAnteriorSmartWifi->getId(),
                                                               "estado"                 => "Activo"));

                    //se valida que exista un elemento WIFI relacionado al ONT registrado dentro de los recursos tecnicos del servicio
                    if(is_object($objEnlaceCliente))
                    {
                        //elimino enlace
                        $objEnlaceCliente->setEstado("Eliminado");
                        $this->emInfraestructura->persist($objEnlaceCliente);
                        $this->emInfraestructura->flush(); 

                        //crear las caract para la solicitud de retiro de equipo
                        $objEnlaceClienteSiguiente = $this->emInfraestructura
                                                          ->getRepository('schemaBundle:InfoEnlace')
                                                          ->findOneBy(array("interfaceElementoIniId" => 
                                                                            $objInterfaceElementoAnteriorSmartWifi->getId(),
                                                                            "estado"                 => "Activo"));

                        //se valida que exista un elemento WIFI relacionado al ONT registrado dentro de los recursos tecnicos del servicio
                        if(is_object($objEnlaceClienteSiguiente))
                        {
                            //elimino enlace
                            $objEnlaceClienteSiguiente->setEstado("Eliminado");
                            $this->emInfraestructura->persist($objEnlaceClienteSiguiente);
                            $this->emInfraestructura->flush(); 

                            $objEnlaceNuevo = new InfoEnlace();
                            $objEnlaceNuevo->setInterfaceElementoIniId($objEnlaceCliente->getInterfaceElementoIniId());
                            $objEnlaceNuevo->setInterfaceElementoFinId($objEnlaceClienteSiguiente->getInterfaceElementoFinId());
                            $objEnlaceNuevo->setTipoMedioId($objEnlaceClienteSiguiente->getTipoMedioId());
                            $objEnlaceNuevo->setTipoEnlace("PRINCIPAL");
                            $objEnlaceNuevo->setEstado("Activo");
                            $objEnlaceNuevo->setUsrCreacion($usrCreacion);
                            $objEnlaceNuevo->setFeCreacion(new \DateTime('now'));
                            $objEnlaceNuevo->setIpCreacion($ipCreacion);
                            $this->emInfraestructura->persist($objEnlaceNuevo);
                            $this->emInfraestructura->flush(); 
                        }
                    }

                    $objElementoSmartWifi = $objInterfaceElementoAnteriorSmartWifi->getElementoId();

                    if (!is_object($objElementoSmartWifi))
                    {
                        throw new \Exception("No se encontro información acerca del elemento SmartWifi del cliente");
                    }

                    //crear solicitud para retiro de equipo (ont y wifi)
                    $objTipoSolicitud = $this->emComercial
                                             ->getRepository('schemaBundle:AdmiTipoSolicitud')
                                             ->findOneBy(array("descripcionSolicitud" => "SOLICITUD RETIRO EQUIPO", 
                                                               "estado"               => "Activo"));

                    if (!is_object($objTipoSolicitud))
                    {
                        throw new \Exception("No se encontro información acerca de la solicitud de retiro de equipo");
                    }

                    $objDetalleSolicitud = new InfoDetalleSolicitud();
                    $objDetalleSolicitud->setServicioId($servicio);
                    $objDetalleSolicitud->setTipoSolicitudId($objTipoSolicitud);
                    $objDetalleSolicitud->setEstado("AsignadoTarea");
                    $objDetalleSolicitud->setUsrCreacion($usrCreacion);
                    $objDetalleSolicitud->setFeCreacion(new \DateTime('now'));
                    $objDetalleSolicitud->setObservacion("SOLICITA RETIRO DE EQUIPO POR CANCELACION DEL SERVICIO");
                    $this->emComercial->persist($objDetalleSolicitud);
                    $this->emComercial->flush();
                    $objAdmiCaracteristica = $this->emComercial
                                                  ->getRepository("schemaBundle:AdmiCaracteristica")
                                                  ->findOneBy(array('descripcionCaracteristica' => 'ELEMENTO CLIENTE',
                                                                    'estado'                    => 'Activo'));
                    if (!is_object($objAdmiCaracteristica))
                    {
                        throw new \Exception("No se encontro información acerca de caracteristica ELEMENTO CLIENTE");
                    }

                    $objDetalleSolCaract= new InfoDetalleSolCaract();
                    $objDetalleSolCaract->setCaracteristicaId($objAdmiCaracteristica);
                    $objDetalleSolCaract->setDetalleSolicitudId($objDetalleSolicitud);
                    $objDetalleSolCaract->setValor($objElementoSmartWifi->getId());
                    $objDetalleSolCaract->setEstado("AsignadoTarea");
                    $objDetalleSolCaract->setUsrCreacion($usrCreacion);
                    $objDetalleSolCaract->setFeCreacion(new \DateTime('now'));
                    $this->emComercial->persist($objDetalleSolCaract);
                    $this->emComercial->flush();

                    $objProceso   = $this->emSoporte
                                         ->getRepository('schemaBundle:AdmiProceso')
                                         ->findOneByNombreProceso("SOLICITAR RETIRO EQUIPO");

                    if (!is_object($objProceso))
                    {
                        throw new \Exception("No se encontro información acerca del proceso solicitar retiro de equipo");
                    }

                    $arrayTareas  = $this->emSoporte
                                         ->getRepository('schemaBundle:AdmiTarea')
                                         ->findTareasActivasByProceso($objProceso->getId());
                    $objTarea     = $arrayTareas[0];

                    $objDetalle = new InfoDetalle();
                    $objDetalle->setDetalleSolicitudId($objDetalleSolicitud->getId());
                    $objDetalle->setTareaId($objTarea);
                    $objDetalle->setLongitud($servicio->getPuntoId()->getLongitud());
                    $objDetalle->setLatitud($servicio->getPuntoId()->getLatitud());
                    $objDetalle->setPesoPresupuestado(0);
                    $objDetalle->setValorPresupuestado(0);
                    $objDetalle->setIpCreacion($ipCreacion);
                    $objDetalle->setFeCreacion(new \DateTime('now'));
                    $objDetalle->setUsrCreacion($usrCreacion);
                    $objDetalle->setFeSolicitada(new \DateTime('now'));
                    $this->emSoporte->persist($objDetalle);
                    $this->emSoporte->flush();                                

                    $objPersonaEmpresaRolUsr = $this->emComercial
                                                    ->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                    ->find($intIdPersonaEmpRol);

                    if (!is_object($objPersonaEmpresaRolUsr))
                    {
                        throw new \Exception("No se encontro información acerca del rol del cliente en sesion");
                    }

                    $objDepartamento = $this->emGeneral
                                            ->getRepository('schemaBundle:AdmiDepartamento')
                                            ->find($objPersonaEmpresaRolUsr->getDepartamentoId());

                    if (!is_object($objDepartamento))
                    {
                        throw new \Exception("No se encontro información acerca del departamento del usuario en sesión");
                    }

                    $objPersona = $objPersonaEmpresaRolUsr->getPersonaId();

                    if (!is_object($objPersona))
                    {
                        throw new \Exception("No se encontro información acerca de la persona en sesión");
                    }

                    $objDetalleAsignacion = new InfoDetalleAsignacion();
                    $objDetalleAsignacion->setDetalleId($objDetalle);
                    $objDetalleAsignacion->setAsignadoId($objDepartamento->getId());
                    $objDetalleAsignacion->setAsignadoNombre($objDepartamento->getNombreDepartamento());
                    $objDetalleAsignacion->setRefAsignadoId($objPersona->getId());

                    if($objPersona->getRazonSocial()=="")
                    {
                        $strNombreAsignado = $objPersona->getNombres()." ".$objPersona->getApellidos();
                    }
                    else
                    {
                        $strNombreAsignado = $objPersona->getRazonSocial();
                    }
                    $objDetalleAsignacion->setRefAsignadoNombre($strNombreAsignado);
                    $objDetalleAsignacion->setPersonaEmpresaRolId($objPersonaEmpresaRolUsr->getId());
                    $objDetalleAsignacion->setUsrCreacion($usrCreacion);
                    $objDetalleAsignacion->setTipoAsignado("EMPLEADO");
                    $objDetalleAsignacion->setFeCreacion(new \DateTime('now'));
                    $objDetalleAsignacion->setIpCreacion($ipCreacion);
                    $this->emSoporte->persist($objDetalleAsignacion);
                    $this->emSoporte->flush();

                    //Se ingresa el historial de la tarea
                    if(is_object($objDetalle))
                    {
                        $arrayParametrosHist["intDetalleId"] = $objDetalle->getId();            
                        $intDetalleId                        = $arrayParametrosHist["intDetalleId"];
                    }

                    $arrayParametrosHist["strObservacion"]  = "Tarea Asignada";
                    $arrayParametrosHist["strEstadoActual"] = "Asignada";    
                    $arrayParametrosHist["strAccion"]       = "Asignada";

                    $this->serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);                 

                    $objPunto = $servicio->getPuntoId();
                    if (!is_object($objPunto))
                    {
                        throw new \Exception("No se encontro información acerca del punto del servicio");
                    }

                    $strAfectadoNombre = $objPunto->getNombrePunto();
                    $intPuntoId        = $objPunto->getId();
                    $strPuntoLogin     = $objPunto->getLogin();

                    $objCriterio = new InfoCriterioAfectado();
                    $objCriterio->setId($intIdCriterioAfectado);
                    $objCriterio->setDetalleId($objDetalle);
                    $objCriterio->setCriterio("Clientes");
                    $objCriterio->setOpcion("Cliente: " . $strAfectadoNombre . " | OPCION: Punto Cliente");
                    $objCriterio->setFeCreacion(new \DateTime('now'));
                    $objCriterio->setUsrCreacion($usrCreacion);
                    $objCriterio->setIpCreacion($ipCreacion);
                    $this->emSoporte->persist($objCriterio);
                    $this->emSoporte->flush();

                    $objAfectado = new InfoParteAfectada();
                    $objAfectado->setTipoAfectado("Cliente");
                    $objAfectado->setDetalleId($objDetalle->getId());
                    $objAfectado->setCriterioAfectadoId($objCriterio->getId());
                    $objAfectado->setAfectadoId($intPuntoId);
                    $objAfectado->setFeIniIncidencia(new \DateTime('now'));
                    $objAfectado->setAfectadoNombre($strPuntoLogin);
                    $objAfectado->setAfectadoDescripcion($strAfectadoNombre);
                    $objAfectado->setFeCreacion(new \DateTime('now'));
                    $objAfectado->setUsrCreacion($usrCreacion);
                    $objAfectado->setIpCreacion($ipCreacion);
                    $this->emSoporte->persist($objAfectado);
                    $this->emSoporte->flush();

                    $objHistorialSolicitud = new InfoDetalleSolHist();
                    $objHistorialSolicitud->setDetalleSolicitudId($objDetalleSolicitud);
                    $objHistorialSolicitud->setEstado("AsignadoTarea");
                    $objHistorialSolicitud->setObservacion("GENERACION AUTOMATICA DE SOLICITUD RETIRO DE EQUIPO POR CANCELACION DEL SERVICIO");
                    $objHistorialSolicitud->setUsrCreacion($usrCreacion);
                    $objHistorialSolicitud->setFeCreacion(new \DateTime('now'));
                    $objHistorialSolicitud->setIpCreacion($ipCreacion);
                    $this->emComercial->persist($objHistorialSolicitud);
                    $this->emComercial->flush();
                }
                // Valida si el cliente quiere el producto adicional de cableado ethernet
                if ($boolPlanificaAdicional && (is_array($arrayDatosPuntoAdicional) && !empty($arrayDatosPuntoAdicional)))
                {
                    $strEstadoCamPlan = "";
                    $arrayParametroTipos = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->get('VALIDA_PROD_ADICIONAL','COMERCIAL','',
                                            'Solicitud cableado ethernet','','','','','','18');
                    if (is_array($arrayParametroTipos) && !empty($arrayParametroTipos))
                    {
                        $objCableParametro = $arrayParametroTipos[0];
                    }
                    // Se manda a guardar la observacion
                    if ($arrayDatosPuntoAdicional['Producto'] == $objCableParametro['valor1']
                        && $strIppcSolicita=='NO')
                    {
                        $strObserSolicitud = 'Cliente no desea el servicio producto cableado ethernet';
                        $strEstadoCamPlan = "Rechazada";
                    }
                    else if ($arrayDatosPuntoAdicional['Producto'] == $objCableParametro['valor1']
                        && $strIppcSolicita=='SI')
                    {
                        $strObserSolicitud = 'Cliente desea el servicio producto cableado ethernet';
                        $strEstadoCamPlan = "Asignada";
                    }
                    $arrayDatosPuntoAdicional['EstadoCamPlan'] = $strEstadoCamPlan;
                    $this->servicioGeneral->generarOtServiciosAdicional($arrayDatosPuntoAdicional);
                    $servicioHistorial = new InfoServicioHistorial();
                    $servicioHistorial->setServicioId($servicio);
                    $servicioHistorial->setObservacion($strObserSolicitud);
                    $servicioHistorial->setEstado($strEstadoCamPlan);
                    $servicioHistorial->setUsrCreacion($usrCreacion);
                    $servicioHistorial->setFeCreacion(new \DateTime('now'));
                    $servicioHistorial->setIpCreacion($ipCreacion);
                    $this->emComercial->persist($servicioHistorial);
                    $this->emComercial->flush();
                }

                if($strCambioPlanPymeAHome === "SI")
                {
                    if(is_object($objIpWanEnPlanPyme))
                    {
                        $objIpWanEnPlanPyme->setEstado("Eliminado");
                        $this->emInfraestructura->persist($objIpWanEnPlanPyme);
                        $this->emInfraestructura->flush();

                        $this->servicioGeneral->actualizarServicioProdCaracts(array("objServicio"           => $servicio,
                                                                                    "objProducto"           => $productoIpAnt,
                                                                                    "strDescripcionCaract"  => "SCOPE",
                                                                                    "strEstadoNuevo"        => "Eliminado",
                                                                                    "strUsrUltMod"          => $usrCreacion));

                        $strObservacionIp = "Se eliminó la Ip Fija Wan incluida en el plan anterior.";
                        $objServicioHistoIpWanEnPlanPyme = new InfoServicioHistorial();
                        $objServicioHistoIpWanEnPlanPyme->setServicioId($servicio);
                        $objServicioHistoIpWanEnPlanPyme->setObservacion($strObservacionIp);
                        $objServicioHistoIpWanEnPlanPyme->setEstado("Activo");
                        $objServicioHistoIpWanEnPlanPyme->setUsrCreacion($usrCreacion);
                        $objServicioHistoIpWanEnPlanPyme->setFeCreacion(new \DateTime('now'));
                        $objServicioHistoIpWanEnPlanPyme->setIpCreacion($ipCreacion);
                        $this->emComercial->persist($objServicioHistoIpWanEnPlanPyme);
                        $this->emComercial->flush();
                    }
                    else if(is_object($objIpWanEnServicioAdic))
                    {
                        $objIpWanEnServicioAdic->setEstado("Eliminado");
                        $this->emInfraestructura->persist($objIpWanEnServicioAdic);
                        $this->emInfraestructura->flush();

                        $intIdServicioIpWanAdic = $objIpWanEnServicioAdic->getServicioId();

                        if(isset($intIdServicioIpWanAdic) && !empty($intIdServicioIpWanAdic))
                        {
                            $objServicioIpWanAdic   = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicioIpWanAdic);
                            if(is_object($objServicioIpWanAdic))
                            {
                                $objServicioIpWanAdic->setEstado("Cancel");
                                $this->emComercial->persist($objServicioIpWanAdic);
                                $this->emComercial->flush();

                                $objServicioHistoIpWanAdic = new InfoServicioHistorial();
                                $objServicioHistoIpWanAdic->setServicioId($objServicioIpWanAdic);
                                $objServicioHistoIpWanAdic->setObservacion("Se cancela servicio adicional por cambio de plan ".
                                                                           "y se elimina la Ip Fija Wan asociada");
                                $objServicioHistoIpWanAdic->setEstado("Cancel");
                                $objServicioHistoIpWanAdic->setUsrCreacion($usrCreacion);
                                $objServicioHistoIpWanAdic->setFeCreacion(new \DateTime('now'));
                                $objServicioHistoIpWanAdic->setIpCreacion($ipCreacion);
                                $this->emComercial->persist($objServicioHistoIpWanAdic);
                                $this->emComercial->flush();

                                if(is_object($objServicioIpWanAdic->getProductoId()))
                                {
                                    $this->servicioGeneral
                                         ->actualizarServicioProdCaracts(array( "objServicio"           => $objServicioIpWanAdic,
                                                                                "objProducto"           => $objServicioIpWanAdic->getProductoId(),
                                                                                "strDescripcionCaract"  => "SCOPE",
                                                                                "strEstadoNuevo"        => "Eliminado",
                                                                                "strUsrUltMod"          => $usrCreacion));
                                }

                            }
                        }
                    }

                    $arrayIpsPreAsigPymeAHome   = array();
                    $arrayDatosIpsPreAsig       = $this->servicioGeneral->getInfoIpsFijaPunto($arrayServiciosPorPunto, $arrayProdIp, $servicio,
                                                                                              'PreAsignacionInfoTecnica', 'Reservada', $producto);
                    $intNumIpsPreAsigPymeAHome  = $arrayDatosIpsPreAsig['ip_fijas_activas'];
                    if($intNumIpsPreAsigPymeAHome > 0)
                    {
                        $arrayIpsPreAsigPymeAHome    = $arrayDatosIpsPreAsig['valores'];
                    }

                    $arrayIpsAsignadasPymeAHome     = array();
                    $arrayDatosIpsAsignada          = $this->servicioGeneral->getInfoIpsFijaPunto($arrayServiciosPorPunto, $arrayProdIp, 
                                                                                                  $servicio, 'Asignada', 'Reservada', $producto);
                    $intNumIpsAsignadasPymeAHome    = $arrayDatosIpsAsignada['ip_fijas_activas'];
                    if($intNumIpsAsignadasPymeAHome > 0)
                    {
                        $arrayIpsAsignadasPymeAHome    = $arrayDatosIpsAsignada['valores'];
                    }

                    $arrayIpsCancelAnulaPymeAHome = array_merge($arrayIpCancelarPymeAHome, $arrayIpsPreAsigPymeAHome, $arrayIpsAsignadasPymeAHome);
                    if(isset($arrayIpsCancelAnulaPymeAHome) && !empty($arrayIpsCancelAnulaPymeAHome))
                    {
                        foreach($arrayIpsCancelAnulaPymeAHome as $arrayIpAdicCancelAnulaPymeAHome)
                        {
                            $intIdServicioIpAdicPymeAHome = $arrayIpAdicCancelAnulaPymeAHome['id_servicio'];
                            if(isset($intIdServicioIpAdicPymeAHome) && !empty($intIdServicioIpAdicPymeAHome))
                            {
                                $objServicioIpAdicPymeAHome = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                                ->find($intIdServicioIpAdicPymeAHome);
                                if(is_object($objServicioIpAdicPymeAHome))
                                {
                                    if($objServicioIpAdicPymeAHome->getEstado() === "Activo")
                                    {
                                        $strEstadoIpCancelAnula          = "Activo";
                                        $strEstadoServicioIpCancelAnula  = "Cancel";
                                        $strObsServicioIpCancelAnula     = "Se cancela servicio adicional por cambio de plan";
                                    }
                                    else
                                    {
                                        $strEstadoIpCancelAnula          = "Reservada";
                                        $strEstadoServicioIpCancelAnula  = "Anulado";
                                        $strObsServicioIpCancelAnula     = "Se anula servicio adicional por cambio de plan";
                                    }
                                    $objServicioIpAdicPymeAHome->setEstado($strEstadoServicioIpCancelAnula);
                                    $this->emComercial->persist($objServicioIpAdicPymeAHome);
                                    $this->emComercial->flush();

                                    $objServicioHistoIpAdicPymeAHome = new InfoServicioHistorial();
                                    $objServicioHistoIpAdicPymeAHome->setServicioId($objServicioIpAdicPymeAHome);
                                    $objServicioHistoIpAdicPymeAHome->setObservacion($strObsServicioIpCancelAnula);
                                    $objServicioHistoIpAdicPymeAHome->setEstado($strEstadoServicioIpCancelAnula);
                                    $objServicioHistoIpAdicPymeAHome->setUsrCreacion($usrCreacion);
                                    $objServicioHistoIpAdicPymeAHome->setFeCreacion(new \DateTime('now'));
                                    $objServicioHistoIpAdicPymeAHome->setIpCreacion($ipCreacion);
                                    $this->emComercial->persist($objServicioHistoIpAdicPymeAHome);
                                    $this->emComercial->flush();

                                    $objIpServicioIpAdicPymeAHome   = $this->emInfraestructura
                                                                           ->getRepository('schemaBundle:InfoIp')
                                                                           ->findOneBy(array("servicioId" => $objServicioIpAdicPymeAHome->getId(),
                                                                                             "estado"     => $strEstadoIpCancelAnula));
                                    if(is_object($objIpServicioIpAdicPymeAHome))
                                    {
                                        $objIpServicioIpAdicPymeAHome->setEstado("Eliminado");
                                        $this->emInfraestructura->persist($objIpServicioIpAdicPymeAHome);
                                        $this->emInfraestructura->flush();
                                    }

                                    if(is_object($objServicioIpAdicPymeAHome->getProductoId()))
                                    {
                                        $this->servicioGeneral
                                             ->actualizarServicioProdCaracts(array( "objServicio"           => $objServicioIpAdicPymeAHome,
                                                                                    "objProducto"           => 
                                                                                    $objServicioIpAdicPymeAHome->getProductoId(),
                                                                                    "strDescripcionCaract"  => "SCOPE",
                                                                                    "strEstadoNuevo"        => "Eliminado",
                                                                                    "strUsrUltMod"          => $usrCreacion));
                                    }

                                    $arraySolsServicioIpAdicPymeAHome   = $this->emComercial
                                                                               ->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                               ->findBy(array(  "servicioId" => $objServicioIpAdicPymeAHome,
                                                                                                "estado"     => 'PreAsignacionInfoTecnica'));
                                    if(isset($arraySolsServicioIpAdicPymeAHome) && !empty($arraySolsServicioIpAdicPymeAHome))
                                    {
                                        foreach($arraySolsServicioIpAdicPymeAHome as $objSolServicioIpAdicPymeAHome)
                                        {
                                            $objSolServicioIpAdicPymeAHome->setEstado("Eliminada");
                                            $this->emComercial->persist($objSolServicioIpAdicPymeAHome);
                                            $this->emComercial->flush();

                                            $objDetalleSolHistIpAdicPymeAHome = new InfoDetalleSolHist();
                                            $objDetalleSolHistIpAdicPymeAHome->setDetalleSolicitudId($objSolServicioIpAdicPymeAHome);
                                            $objDetalleSolHistIpAdicPymeAHome->setEstado($objSolServicioIpAdicPymeAHome->getEstado());
                                            $objDetalleSolHistIpAdicPymeAHome->setFeCreacion(new \DateTime('now'));
                                            $objDetalleSolHistIpAdicPymeAHome->setUsrCreacion($usrCreacion);
                                            $objDetalleSolHistIpAdicPymeAHome->setIpCreacion($ipCreacion);
                                            $objDetalleSolHistIpAdicPymeAHome->setObservacion("Se elimina solicitud por anulación de servicio "
                                                                                              ."al ejecutar un cambio de plan");
                                            $this->emComercial->persist($objDetalleSolHistIpAdicPymeAHome);
                                            $this->emComercial->flush();
                                        }
                                    }
                                }
                           }
                        }
                    }

                    if(isset($arrayProdIp) && !empty($arrayProdIp))
                    {
                        foreach($arrayProdIp as $objProdIp)
                        {
                            $this->servicioGeneral->actualizarServicioProdCaracts(array("objServicio"           => $servicio,
                                                                                        "objProducto"           => $objProdIp,
                                                                                        "strDescripcionCaract"  => "SCOPE",
                                                                                        "strEstadoNuevo"        => "Eliminado",
                                                                                        "strUsrUltMod"          => $strUsrRegulaSpc));
                        }
                    }
                }
                /* Se pueden presentar los siguientes 3 escenarios de cambios de plan entre planes PYME
                 * 1.- Cliente con plan anterior Pyme con Ip, plan nuevo Pyme sin Ip, strConservaIp SI, se generará
                 *     nuevo servicio con tipo de orden N y es venta S según las definiciones del usuarios
                 * 2.- Cliente con plan anterior Pyme con Ip, plan nuevo Pyme sin Ip, strConservaIp NO, se debe
                 *     ejecutar la cancelación de la Ip Wan que tenía el cliente y registrar historial indicando dicha cancelación
                 * 3.- Cliente con plan anterior Pyme sin Ip, plan nuevo Pyme sin Ip (se indicó por parte del usuario
                 *     para esta fase solo existirán planes nuevos sin IP) no se realiza ninguna acción adicional
                 */
                else if (!empty($strConservarIp))
                {
                    if ($strConservarIp === "SI")
                    {
                        if (is_object($objIpPlanPro))
                        {
                            $arrayRespuestaCreaProIp = $this->servicioGeneral
                                                            ->creaServicioIpFijaWan(array(  
                                                                                    "servicioInternet"          => 
                                                                                    $servicio,
                                                                                    "punto"                     => 
                                                                                    $servicio->getPuntoId(),
                                                                                    "servicioTecnicoInternet"   => 
                                                                                    $servicioTecnico,
                                                                                    "usrCreacion"               => 
                                                                                    $usrCreacion,
                                                                                    "ipCreacion"                => 
                                                                                    $ipCreacion,
                                                                                    "codEmpresa"                => 
                                                                                    $idEmpresa,
                                                                                    "nombreTecnico"             => 
                                                                                    "IP",
                                                                                    "nombreProducto"                => 
                                                                                    "IP FIJA"));
                            $objServHistServicio    = new InfoServicioHistorial();
                            $objServHistServicio->setServicioId($servicio);
                            $objServHistServicio->setObservacion($arrayRespuestaCreaProIp["strMensaje"]);
                            $objServHistServicio->setEstado($servicio->getEstado());
                            $objServHistServicio->setUsrCreacion($usrCreacion);
                            $objServHistServicio->setFeCreacion(new \DateTime('now'));
                            $objServHistServicio->setIpCreacion($ipCreacion);
                            $this->emComercial->persist($objServHistServicio);
                            $this->emComercial->flush();
                            if($arrayRespuestaCreaProIp["strStatus"] === "OK" && is_object($arrayRespuestaCreaProIp["objServicioProdIp"]))
                            {
                                $objIpPlanPro->setServicioId($arrayRespuestaCreaProIp["objServicioProdIp"]->getId());
                                $this->emInfraestructura->persist($objIpPlanPro);
                                $this->emInfraestructura->flush();

                                $this->servicioGeneral
                                     ->ingresarServicioProductoCaracteristica($arrayRespuestaCreaProIp["objServicioProdIp"],
                                                                              $arrayRespuestaCreaProIp["objServicioProdIp"]->getProductoId(), 
                                                                              "SCOPE", 
                                                                              $strScopeConservaIp, 
                                                                              $usrCreacion);
                                //ingresar mac ont del servicio de internet
                                $this->servicioGeneral->ingresarServicioProductoCaracteristica($arrayRespuestaCreaProIp["objServicioProdIp"],
                                                                                               $producto,
                                                                                               "MAC",
                                                                                               $strMacOnt,
                                                                                               $usrCreacion);

                                //historial del servicio
                                $objServHistServicioMac = new InfoServicioHistorial();
                                $objServHistServicioMac->setServicioId($arrayRespuestaCreaProIp["objServicioProdIp"]);
                                $objServHistServicioMac->setObservacion("Se configuró Ip Fija:" . $objIpPlanPro->getIp() . " con Mac:" . $strMacOnt);
                                $objServHistServicioMac->setEstado($arrayRespuestaCreaProIp["objServicioProdIp"]->getEstado());
                                $objServHistServicioMac->setUsrCreacion($usrCreacion);
                                $objServHistServicioMac->setFeCreacion(new \DateTime('now'));
                                $objServHistServicioMac->setIpCreacion($ipCreacion);
                                $this->emComercial->persist($objServHistServicioMac);
                                $this->emComercial->flush();

                                $this->servicioGeneral->actualizarServicioProdCaracts(array("objServicio"           => $servicio,
                                                                                            "objProducto"           => $productoIpAnt,
                                                                                            "strDescripcionCaract"  => "SCOPE",
                                                                                            "strEstadoNuevo"        => "Eliminado",
                                                                                            "strUsrUltMod"          => $usrCreacion));
                            }
                        }
                    }
                    else
                    {
                        $objIpPlanPro->setEstado("Eliminado");
                        $this->emInfraestructura->persist($objIpPlanPro);
                        $this->emInfraestructura->flush();

                        $this->servicioGeneral->actualizarServicioProdCaracts(array("objServicio"           => $servicio,
                                                                                    "objProducto"           => $productoIpAnt,
                                                                                    "strDescripcionCaract"  => "SCOPE",
                                                                                    "strEstadoNuevo"        => "Eliminado",
                                                                                    "strUsrUltMod"          => $usrCreacion));
                        //historial del servicio validar con RDA
                        $strObservacionIp = "Se eliminó la Ip Fija Wan incluída en el plan anterior.";
                        $objServicioHistIp = new InfoServicioHistorial();
                        $objServicioHistIp->setServicioId($servicio);
                        $objServicioHistIp->setObservacion($strObservacionIp);
                        $objServicioHistIp->setEstado("Activo");
                        $objServicioHistIp->setUsrCreacion($usrCreacion);
                        $objServicioHistIp->setFeCreacion(new \DateTime('now'));
                        $objServicioHistIp->setIpCreacion($ipCreacion);
                        $this->emComercial->persist($objServicioHistIp);
                        $this->emComercial->flush();
                    }
                }
                //Procesos para productos Paramount y Noggin
                //PARAMOUNT
                if ($strPlanViejoTieneParamount == "SI" && $strPlanViejoTieneNoggin =="NO") 
                {
                    //variables
                    $objProductoPlanDet    = $arrayPlanDetOrigen[0];

                    if($strPlanNuevoTieneParamount=="NO")
                    {
                        //Convertir Paramount a producto adicional 
                        $this->serviceFoxPremium->convertirAProductoAdicional(array(  'objServicio'        =>  $servicio,
                                                                                                        'objProductoPlanDet' =>  $objProductoPlanDet,
                                                                                                        'strUsrCreacion'     =>  $usrCreacion,
                                                                                                        'strIpCreacion'      =>  $ipCreacion));
                    }
                    if($strPlanNuevoTieneNoggin=="SI")
                    {
                        //Variables
                        $objProductoPlanDetNuevo    = $arrayPlanDetDestino[0];
                        if($strProdAdicTieneNoggin == "SI")
                        {
                            //convertir a plan y eliminar la infoservprodcarac del producto adicional y cancelar el servicio adicional
                            $this->serviceFoxPremium->convertirAPlan(array(   'objServicio'        =>  $servicio,
                                                                                                'strUsrCreacion'     =>  $usrCreacion,
                                                                                                'strIpCreacion'      =>  $ipCreacion,
                                                                                                'objProdAdicViejo'   =>  $objProdAdicionalNoggin));
                        }
                        else
                        {
                            //Activar Noggin dentro del plan y notificar
                            $this->serviceFoxPremium->activarProductoEnPlan(array('objServicio'        =>  $servicio,
                                                                                                    'objProductoPlanDet' =>  $objProductoPlanDetNuevo,
                                                                                                    'objProducto'        =>  $objProdNoggin,
                                                                                                    'strUsrCreacion'     =>  $usrCreacion,
                                                                                                    'strIpCreacion'      =>  $ipCreacion,
                                                                                                    'strCodEmpresa'      =>  $idEmpresa));
                        }
                    }
                }
                //NOGGIN
                else if ($strPlanViejoTieneNoggin =="SI" && $strPlanViejoTieneParamount == "NO")
                {
                    //variables
                    $objProductoPlanDet    = $arrayPlanDetOrigen[0];

                    if($strPlanNuevoTieneNoggin=="NO")
                    {
                        //Convertir Noggin a producto adicional
                        $this->serviceFoxPremium->convertirAProductoAdicional(array(  'objServicio'        =>  $servicio,
                                                                                                        'objProductoPlanDet' =>  $objProductoPlanDet,
                                                                                                        'strUsrCreacion'     =>  $usrCreacion,
                                                                                                        'strIpCreacion'      =>  $ipCreacion));
                    }
                    if($strPlanNuevoTieneParamount=="SI")
                    {
                        //Variables
                        $objProductoPlanDetNuevo    = $arrayPlanDetDestino[0];
                        if($strProdAdicTieneParamount == "SI")
                        {
                            //convertir a plan y eliminar la infoservprodcarac del producto adicional y cancelar el servicio adicional
                            $this->serviceFoxPremium->convertirAPlan(array(   'objServicio'        =>  $servicio,
                                                                                                'strUsrCreacion'     =>  $usrCreacion,
                                                                                                'strIpCreacion'      =>  $ipCreacion,
                                                                                                'objProdAdicViejo'   =>  $objProdAdicionalParamount));
                        }
                        else
                        {
                            //Activar Paramount dentro del plan y notificar
                            $this->serviceFoxPremium->activarProductoEnPlan(array('objServicio'        =>  $servicio,
                                                                                                    'objProductoPlanDet' =>  $objProductoPlanDetNuevo,
                                                                                                    'objProducto'        =>  $objProdParamount,
                                                                                                    'strUsrCreacion'     =>  $usrCreacion,
                                                                                                    'strIpCreacion'      =>  $ipCreacion,
                                                                                                    'strCodEmpresa'      =>  $idEmpresa));
                        }
                    }
                }
                else if(($strPlanNuevoTieneParamount=="SI" || $strPlanNuevoTieneNoggin=="SI") && 
                        ($strPlanViejoTieneNoggin =="NO" || $strPlanViejoTieneParamount == "NO"))
                {
                    if($strProdAdicTieneParamount != 'SI' && $strProdAdicTieneNoggin != 'SI')
                    {
                        //ACTIVAR LOS PRODUCTOS DENTRO DEL PLAN NUEVO
                        foreach($arrayPlanDetDestino as $objPlanDetDestino)
                        {
                            if($objPlanDetDestino->getProductoId() == $objProdParamount->getId())
                            {
                                $this->serviceFoxPremium->activarProductoEnPlan(array('objServicio'        =>  $servicio,
                                                                                                        'objProductoPlanDet' =>  $objPlanDetDestino,
                                                                                                        'objProducto'        =>  $objProdParamount,
                                                                                                        'strUsrCreacion'     =>  $usrCreacion,
                                                                                                        'strIpCreacion'      =>  $ipCreacion,
                                                                                                        'strCodEmpresa'      =>  $idEmpresa));
                            }
                            if($objPlanDetDestino->getProductoId() == $objProdNoggin->getId())
                            {
                                $this->serviceFoxPremium->activarProductoEnPlan(array('objServicio'        =>  $servicio,
                                                                                                        'objProductoPlanDet' =>  $objPlanDetDestino,
                                                                                                        'objProducto'        =>  $objProdNoggin,
                                                                                                        'strUsrCreacion'     =>  $usrCreacion,
                                                                                                        'strIpCreacion'      =>  $ipCreacion,
                                                                                                        'strCodEmpresa'      =>  $idEmpresa));
                            }
                        }
                    }
                    else
                    {
                        if($strProdAdicTieneParamount == "SI" && $strPlanNuevoTieneParamount=="SI")
                        {
                            //convertir a plan y eliminar la infoservprodcarac del producto adicional y cancelar el servicio adicional
                            $this->serviceFoxPremium->convertirAPlan(array(                     'objServicio'        =>  $servicio,
                                                                                                'strUsrCreacion'     =>  $usrCreacion,
                                                                                                'strIpCreacion'      =>  $ipCreacion,
                                                                                                'objProdAdicViejo'   =>  $objProdAdicionalParamount));
                        }
                        if($strProdAdicTieneNoggin == "SI" && $strPlanNuevoTieneNoggin=="SI")
                        {
                            //convertir a plan y eliminar la infoservprodcarac del producto adicional y cancelar el servicio adicional
                            $this->serviceFoxPremium->convertirAPlan(array(                     'objServicio'        =>  $servicio,
                                                                                                'strUsrCreacion'     =>  $usrCreacion,
                                                                                                'strIpCreacion'      =>  $ipCreacion,
                                                                                                'objProdAdicViejo'   =>  $objProdAdicionalNoggin));
                        }
                    }
                }
                else if($strPlanNuevoTieneParamount=="SI" && $strPlanNuevoTieneNoggin=="SI" && 
                        (($strPlanViejoTieneNoggin =="NO" && $strPlanViejoTieneParamount == "SI") ||
                         ($strPlanViejoTieneNoggin =="SI" && $strPlanViejoTieneParamount == "NO") ))
                {

                    //ACTIVAR PRODUCTO DENTRO DE PLAN QUE NO ESTE EN EL PLAN VIEJO
                    foreach($arrayPlanDetDestino as $objPlanDetDestino)
                    {
                    if(!empty($objProdParamount) && $strPlanViejoTieneParamount == "NO" &&
                       $objPlanDetDestino->getProductoId() == $objProdParamount->getId())
                        {
                            $this->serviceFoxPremium->activarProductoEnPlan(array('objServicio'        =>  $servicio,
                                                                                                    'objProductoPlanDet' =>  $objPlanDetDestino,
                                                                                                    'objProducto'        =>  $objProdParamount,
                                                                                                    'strUsrCreacion'     =>  $usrCreacion,
                                                                                                    'strIpCreacion'      =>  $ipCreacion,
                                                                                                    'strCodEmpresa'      =>  $idEmpresa));
                        }
                    if(!empty($objProdNoggin) && $strPlanViejoTieneNoggin == "NO" &&
                       $objPlanDetDestino->getProductoId() == $objProdNoggin->getId())
                        {
                            $this->serviceFoxPremium->activarProductoEnPlan(array('objServicio'        =>  $servicio,
                                                                                                    'objProductoPlanDet' =>  $objPlanDetDestino,
                                                                                                    'objProducto'        =>  $objProdNoggin,
                                                                                                    'strUsrCreacion'     =>  $usrCreacion,
                                                                                                    'strIpCreacion'      =>  $ipCreacion,
                                                                                                    'strCodEmpresa'      =>  $idEmpresa));
                        }
                    }
                }
                else if($strPlanNuevoTieneParamount=="NO" && $strPlanNuevoTieneNoggin=="NO" && 
                    ($strPlanViejoTieneNoggin =="SI" || $strPlanViejoTieneParamount == "SI"))
                {
                    foreach($arrayPlanDetOrigen as $objProductoPlanDet)
                    {

                    //Pasar como producto adicional
                    $this->serviceFoxPremium->convertirAProductoAdicional(array(   'objServicio'          =>  $servicio,
                                                                                  'objProductoPlanDet'   =>  $objProductoPlanDet,
                                                                                  'strUsrCreacion'       =>  $usrCreacion,
                                                                                  'strIpCreacion'        =>  $ipCreacion));
                    }
                }
                $arrayDataConfirmacionTn['datos']['respuesta_confirmacion'] = "OK";
                $strMensaje = "";
            }//end if($statusFinal == "OK")
            else
            {
                //cambiar tipo de negocio, para poder hacer la solicitud de ip fija
                $this->servicioGeneral->setTipoNegocioEnInfoPunto($punto, $planCabViejo->getTipo(), $idEmpresa);

                if($strMarcaOlt == "HUAWEI")
                {
                    //se eliminan caracteristicas nuevas
                    if (is_object($objSpcLineProfileNameNuevo))
                    {
                        $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objSpcLineProfileNameNuevo, "Eliminado");
                    }
                    if (is_object($objSpcGemPortNuevo))
                    { 
                        $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objSpcGemPortNuevo, "Eliminado");
                    }
                    if (is_object($objSpcVlanNuevo))
                    {
                        $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objSpcVlanNuevo, "Eliminado");
                    }
                    if (is_object($objSpcTrafficTableNuevo))
                    {
                        $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objSpcTrafficTableNuevo, "Eliminado");
                    }
                    if ($productoIp)
                    {
                        $objSpcScopeNuevo = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "SCOPE", $productoIp);
                        if(is_object($objSpcScopeNuevo))
                        {
                            $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objSpcScopeNuevo, "Eliminado");
                        }
                    }

                    //se activan caracteristicas viejas
                    $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objSpcLineProfileNameAntes, "Activo");
                    $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objSpcGemPortAntes, "Activo");
                    $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objSpcVlanAntes, "Activo");
                    $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objSpcTrafficTableAntes, "Activo");
                    if (is_object($objSpcScopeAntes))
                    {
                        $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objSpcScopeAntes, "Activo");
                    }
                }
            }
        }
        catch(\Exception $e)
        {
            $statusFinal            = "ERROR";
            $strMensaje             = $e->getMessage();
            $strOcurrioException    = "SI";
            
        }
        $arrayFinal[]   = array('status'                    => $statusFinal, 
                                'mensaje'                   => $strMensaje,
                                'intDetalleId'              => $intDetalleId,
                                'objPlanCabViejo'           => $planCabViejo,
                                'objPlanCabNuevo'           => $planCabNuevo,
                                'intFrecuencia'             => $frecuencia,
                                'arrayDataConfirmacionTn'   => $arrayDataConfirmacionTn,
                                'strOcurrioException'       => $strOcurrioException);
        return $arrayFinal;
    }

    /**
     * Realiza el recálculo de descuento del servicio.
     * Se da prioridad al porcentaje de descuento del servicio, caso contrario se verifica el porcentaje de descuento de una solicitud existente.
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.14
     * @since 27-04-2018
     */
    public function recalcularDescuentoServicio($arrayParametros)
    {
        $objInfoServicioTemp  = $arrayParametros["objInfoServicio"];
        $strUsrCreacion       = $arrayParametros["strUsrCreacion"];
        $strIpCreacion        = $arrayParametros["strIpCreacion"];
        $floatPrecioNuevoPlan = $arrayParametros["floatPrecionuevoPlan"];
        $strObsDescuento      = "";

        if(!is_object($objInfoServicioTemp))
        {
            $this->utilService->insertError('Telcos+',
                                            'InfoCambiarPlanService.recalcularDescuentoServicio',
                                            'No existe un servicio asignado',
                                            $strUsrCreacion,
                                            $strIpCreacion
                                            );
            return null;
        }
        try
        {
            $objInfoServicio = $this->emComercial->getRepository("schemaBundle:InfoServicio")->find($objInfoServicioTemp->getId());
            //Se recalcula el valor del valor del descuento.
            //Obtengo el tipo de solicitud 2 (SOLICITUD DE DESCUENTO)
            //Si el servicio tiene porcentaje de descuento es mandatorio.
            $floatValorDescuento       = $objInfoServicio->getValorDescuento();
            $floatPorcentajeDescuento  = ($objInfoServicio->getPorcentajeDescuento()) ? $objInfoServicio->getPorcentajeDescuento() : 0;
            $intCantidadServicio       = ($objInfoServicio->getCantidad() > 0) ? $objInfoServicio->getCantidad() : 1;
            //Si el servicio no tiene porcentaje de descuento.
            if(!($floatPorcentajeDescuento > 0))
            {
                $arrayListDetalleSolicitud = $this->emComercial->getRepository("schemaBundle:InfoDetalleSolicitud")
                                                  ->findByParameters(array("servicioId"      => $objInfoServicio->getId(),
                                                                           "estado"          => "Aprobado",
                                                                           "tipoSolicitudId" => 2,
                                                                           "orden"           => "ORDER BY 1 DESC"));
                $floatPorcentajeDescuento  = $arrayListDetalleSolicitud[0]["porcentajeDescuento"] ?
                                             floatval($arrayListDetalleSolicitud[0]["porcentajeDescuento"]) : 0;
            }
            //Si el nuevo valor del porcentaje obtenido en la solicitud es mayor a 0, actualizo los valores de descuento del servicio
            if($floatPorcentajeDescuento > 0)
            {
                $strObsDescuento = "descuento: <b> " . $floatPorcentajeDescuento . "%</b>,<br>";
                $floatValorDescuento    = round(($intCantidadServicio * $floatPrecioNuevoPlan * $floatPorcentajeDescuento / 100), 2);
                $objInfoServicio->setValorDescuento($floatValorDescuento);
                try
                {
                    $floatDescuentoUnitario = round(($floatValorDescuento / $intCantidadServicio), 2);
                }
                catch(\Exception $ex)
                {
                    $floatDescuentoUnitario = 0;
                    $this->utilService->insertError('Telcos+',
                                    'InfoCambiarPlanService.cambioPlanMd',
                                    $ex->getMessage(),
                                    $strUsrCreacion,
                                    $strIpCreacion
                                   );
                }
                $objInfoServicio->setDescuentoUnitario($floatDescuentoUnitario);
            }

            if ($floatValorDescuento > 0)
            {
                $strObsDescuento .= "valor descuento: <b> " . $floatValorDescuento . "</b><br>";
            }
        }catch(\Exception $ex)
        {
            $this->utilService->insertError('Telcos+',
                                    'InfoCambiarPlanService.cambioPlanMd',
                                    $ex->getMessage(),
                                    $strUsrCreacion,
                                    $strIpCreacion
                                   );
            return null;
        }
        return array("objInfoServicio"          => $objInfoServicio,
                     "strObsDescuento"          => $strObsDescuento);
    }

    /**
     * Funcion que realiza el cambio de plan de clientes en equipos TELLION de:
     * - Home -> Home
     * - Home -> Pro
     * - Home -> Pyme
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 7-08-2014
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 09-05-2016   Se agrega parametro empresa en metodo cambioPlanHomeTellion por conflictos de producto INTERNET DEDICADO
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.2 02-06-2016   Se corrige nombre de variable que sirve para retornar la respuesta del procesamiento del cambio de plan
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.3 02-06-2016   Se agrega linea para recuperar mensaje de respuesta de script
     * 
     * @param $arrayPeticiones  arreglo con las variables necesarias para realizar el cambio de plan
     */
    public function cambioPlanHomeTellion($arrayPeticiones)
    {
        $punto = $arrayPeticiones[0]['punto'];
        $login = $arrayPeticiones[0]['login'];

        $servicio           = $arrayPeticiones[0]['servicio'];
        $servicioTecnico    = $arrayPeticiones[0]['servicioTecnico'];
        $planCabViejo       = $arrayPeticiones[0]['planCabViejo'];
        $planDetViejo       = $arrayPeticiones[0]['planDetViejo'];

        $interfaceElemento  = $arrayPeticiones[0]['interfaceElemento'];
        $modeloElemento     = $arrayPeticiones[0]['modeloElemento'];

        $planCabNuevo       = $arrayPeticiones[0]['planCabNuevo'];
        $planDetNuevo       = $arrayPeticiones[0]['planDetNuevo'];
        $producto           = $arrayPeticiones[0]['producto'];            //producto internet
        $arrayProdIp        = $arrayPeticiones[0]['arrayProdIp'];
        $arrayProdInternet  = $arrayPeticiones[0]['arrayProdInternet'];

        $macOnt                         = $arrayPeticiones[0]['macOnt'];
        $perfilNuevo                    = $arrayPeticiones[0]['perfilNuevo'];
        $planCaractEdicionLimitada      = $arrayPeticiones[0]['planCaractEdicionLimitada'];
        $planViejoCaractEdicionLimitada      = $arrayPeticiones[0]['planViejoCaractEdicionLimitada'];
        $servProdCaractIndiceCliente    = $arrayPeticiones[0]['servProdCaractIndiceCliente'];
        $servProdCaractMacOnt           = $arrayPeticiones[0]['servProdCaracMacOnt'];
        $servProdCaracMacWifi           = $arrayPeticiones[0]['servProdCaracMacWifi'];
        $servProdCaracPerfil            = $arrayPeticiones[0]['servProdCaracPerfil'];

        $idEmpresa      = $arrayPeticiones[0]['idEmpresa'];
        $usrCreacion    = $arrayPeticiones[0]['usrCreacion'];
        $ipCreacion     = $arrayPeticiones[0]['ipCreacion'];

        $flagViejoEdLimitada = 0;
        $flagEdicionLimitada = 0;
        $flagProd            = 0;
        $status              = "ERROR";
        
        //validar si el plan viejo es un plan de edicion limitada
        if($planViejoCaractEdicionLimitada)
        {
            if($planViejoCaractEdicionLimitada->getValor() == "SI")
            {
                $flagViejoEdLimitada=1;
            }
        }

        //verificar si plan nuevo tiene ip en el plan
        $flagProd = $this->servicioGeneral->verificarPlanTieneIp($planDetNuevo, $arrayProdIp);
        
        //validar que el plan pyme tenga producto ip definido en el plan
        if($planCabNuevo->getTipo() == "PYME" && $flagProd<=0)
        {
            $respuestaFinal[] = array('status'  => 'ERROR', 
                                      'mensaje' => 'El Plan: <b>'.$planCabNuevo->getNombrePlan.'</b>, <br>'
                                                 . 'No tiene producto Ip definido, Favor Notificar al Dep. Marketing!');
            return $respuestaFinal;
        }//if($planCabNuevo->getTipo() == "PYME" && $flagProdIpPlanNuevo<=0)
        
        //validaciones para el plan de edicion limitada
        if($planCaractEdicionLimitada)
        {
            if($planCaractEdicionLimitada->getValor() == "SI")
            {
                $flagEdicionLimitada = 1;

                //verificar si existe la mac del wifi
                if($servProdCaracMacWifi)
                {
                    $macWifi = $servProdCaracMacWifi->getValor();
                }
                else
                {
                    $respuestaFinal[] = array('status' => 'ERROR',
                        'mensaje' => 'No existe registro de la Mac Wifi, <br>'
                        . 'Favor regularice la data!');
                    return $respuestaFinal;
                }
            }//if($planCaractEdicionLimitada->getValor() == "SI")
            else
            {
                $respuestaFinal[] = array('status' => 'ERROR',
                    'mensaje' => 'No se puede Realizar el cambio de plan, <br>'
                    . 'Caracteristica Edicion Limitada es: '.$planCaractEdicionLimitada->getValor().', <br>'
                    . 'Favor Notificar a Sistemas!');
                return $respuestaFinal;
            }
        }//if($planCaractEdicionLimitada)
        
        //validar que la mac que tiene el servicio este conectada en el olt
        //antes de ejecutar la cancelacion
        if($flagProd)
        {
            //*OBTENER SCRIPT MAC WIFI--------------------------------------------------------*/
            $scriptArrayMacWifi = $this->servicioGeneral->obtenerArregloScript("obtenerMacIpDinamica", $modeloElemento);
            $idDocumentoMacWifi = $scriptArrayMacWifi[0]->idDocumento;
            //*----------------------------------------------------------------------*/
            //verificar mac wifi en el olt
            $resultadoJsonMacWifi = $this->activarService
                ->verificarMacWifi($servicioTecnico, $interfaceElemento, $servProdCaracMacWifi->getValor(), 
                                   $servProdCaractIndiceCliente->getValor(), $idDocumentoMacWifi);
            $statusMacWifi = $resultadoJsonMacWifi->status;
            if($statusMacWifi == "ERROR")
            {
                $respuestaFinal[] = array('status' => $statusMacWifi, 'mensaje' => $resultadoJsonMacWifi->mensaje);
                return $respuestaFinal;
            }
        }//if($planCabNuevo->getTipo() == "PRO" || $planCabNuevo->getTipo() == "PYME")        

        //service que cancela el plan anterior
        if($flagViejoEdLimitada==1)
        {
            //eliminar suscriber del sce, para plan edicion limitada
            $resultadJsonSce = $this->ejecutarScriptEnSce($servicio, "", "eliminar");
            $statusSce = $resultadJsonSce->status;
            
            if($statusSce=="OK")
            {
                $arrayParametros = array(
                                        'servicioTecnico'   => $servicioTecnico,
                                        'interfaceElemento' => $interfaceElemento,
                                        'modeloElemento'    => $modeloElemento,
                                        'producto'          => $producto,
                                        'login'             => $login,
                                        'idEmpresa'         => $idEmpresa,
                                        'ipCreacion'        => $ipCreacion,
                                        'usrCreacion'       => $usrCreacion
                                    );
                $respuestaArray = $this->cancelarService->cancelarServicioMdConIp($arrayParametros);
                $status = $respuestaArray[0]['status'];
            }
            else
            {
                $arrayFinal[] = array('status' => "ERROR",
                    'mensaje' => "No se puede realizar el cambio de Plan, <br>"
                    . "Fallo la eliminacion del suscriptor en el SCE<br>"
                    . "Favor volver a intentar nuevamente, o comuniquese con el Dept. Sistemas");
                return $arrayFinal;
            }
        }
        else
        {
            $arrayParametros = array(
                                        'servicioTecnico'   => $servicioTecnico,
                                        'interfaceElemento' => $interfaceElemento,
                                        'modeloElemento'    => $modeloElemento,
                                        'spcIndiceCliente'  => $servProdCaractIndiceCliente,
                                        'login'             => $login,
                                        'spcSpid'           => "",
                                        'spcMacOnt'         => "",
                                        'idEmpresa'         => $idEmpresa
                                    );
            $respuestaArray = $this->cancelarService->cancelarServicioMdSinIp($arrayParametros);
            $status         = $respuestaArray[0]['status'];
            $mensaje        = $respuestaArray[0]['mensaje'];
        }        

        if($status == "OK")
        {
            //eliminamos el indice anterior
            $this->servicioGeneral->setEstadoServicioProductoCaracteristica($servProdCaractIndiceCliente, "Eliminado");

            //actualizamos el tipo de negocio
            $this->servicioGeneral->setTipoNegocioEnInfoPunto($punto, $planCabNuevo->getTipo(), $idEmpresa);
                        
            switch($planCabNuevo->getTipo())
            {
                //Home -> Home
                case "HOME":
                    //si el cambio es al plan de edicion limitada
                    if($flagEdicionLimitada == 1)
                    {
                        //solicitar 1 ip para el plan de edicion limitada
                        $arregloIps = $this->recursosRed
                            ->getIpsDisponiblePoolOlt(1, $servicioTecnico->getElementoId(), $servicio->getId(), 
                                                      $servicio->getPuntoId()->getId(), "SI", $planCabNuevo->getId());

                        if($arregloIps['error'])
                        {
                            $status = "ERROR";
                            $mensaje = $arregloIps['error'];
                            break;
                        }

                        $arrayIps = $arregloIps['ips'];

                        //verificar si la ip nueva se encuentra configurada
                        $resultadJsonIpConfig = $this->servicioGeneral
                            ->verificarIpConfigurada($modeloElemento, $servicioTecnico, $arrayIps[0]['ip']);
                        $status = $resultadJsonIpConfig->status;
                        $macIpConf = $resultadJsonIpConfig->mensaje;
                        if($status == "ERROR")
                        {
                            $olt = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                           ->find($servicioTecnico->getElementoId());
                            $mensaje = "Ip: <b>" . $arrayIps[0]['ip'] ."</b> ya se encuentra configurada en <b>" . 
                                        $olt->getNombreElemento() ."</b> con la mac <b>" . 
                                        $macIpConf ."</b>. Favor notificar a Sistemas.";
                            break;
                        }//if($status == "ERROR")
                        else if($status=="OK")
                        {
                            //graba la ip en estado reservada
                            $ipFija = $this->servicioGeneral
                                ->reservarIpAdicional($arrayIps[0]['ip'], $arrayIps[0]['tipo'], $servicio, $usrCreacion, $ipCreacion);
                            
                            $arrayParametros=array(
                                        'servicio'          => $servicio,
                                        'servicioTecnico'   => $servicioTecnico,
                                        'interfaceElemento' => $interfaceElemento,
                                        'modeloElemento'    => $modeloElemento,
                                        'producto'          => $producto,
                                        'macOnt'            => $macOnt,
                                        'macWifi'           => $macWifi,
                                        'perfil'            => $perfilNuevo,
                                        'login'             => $login,
                                        'usrCreacion'       => $usrCreacion
                                      );
                            
                            //ejecutar scripts de activar internet e ip fija
                            $respuestaFinal = $this->activarService
                                ->activarClienteMdConIp($arrayParametros);

                            $status = $respuestaFinal[0]['status'];
                            $mensaje = $respuestaFinal[0]['mensaje'];

                            //verificar respuesta de la ejecucion de scripts
                            if($status != "OK")
                            {
                                //cambia de estado a la ip fija
                                $ipFija->setEstado("Eliminado");
                                $this->emInfraestructura->persist($ipFija);
                                $this->emInfraestructura->flush();

                                //sale del switch para hacer rollback de scripts
                                break;
                            }//if($status != "OK")

                            //cambia de estado a la ip fija
                            $ipFija->setEstado("Activo");
                            $this->emInfraestructura->persist($ipFija);
                            $this->emInfraestructura->flush();

                            //ejecuta script para grabar la ip fija en el sce para control de bw
                            $resultadJsonSce = $this->ejecutarScriptEnSce($servicio, $ipFija->getIp(),"activar");
                            $statusSce = $resultadJsonSce->status;
                            if($statusSce != "OK")
                            {
                                //error de sce
                                $status = $statusSce;
                                $mensajeSce = "Activacion Sce:".$resultadJsonSce->status;

                                //grabamos el nuevo indice
                                $indiceNuevo = $mensaje;
                                $servProdCaractIndiceNuevo = $this->servicioGeneral
                                    ->ingresarServicioProductoCaracteristica($servicio, $producto, "INDICE CLIENTE", 
                                                                             $indiceNuevo, $usrCreacion);

                                //cancelar activacion nueva
                                $arrayParametros = array(
                                        'servicioTecnico'   => $servicioTecnico,
                                        'interfaceElemento' => $interfaceElemento,
                                        'modeloElemento'    => $modeloElemento,
                                        'producto'          => $producto,
                                        'login'             => $login,
                                        'idEmpresa'         => $idEmpresa,
                                        'ipCreacion'        => $ipCreacion,
                                        'usrCreacion'       => $usrCreacion
                                    );
                                $respuestaFinalCancelNuevo = $this->cancelarService->cancelarServicioMdConIp($arrayParametros);
                                $statusCancelNuevo = $respuestaFinalCancelNuevo[0]['status'];
                                if($statusCancelNuevo!="OK")
                                {
                                    $mensajeCancelNuevo = $respuestaFinalCancelNuevo[0]['mensaje'];
                                    $mensajeSce = $mensajeSce."<br>"
                                                . "Cancelacion Plan Nuevo:".$mensajeCancelNuevo;
                                }//if($statusCancelNuevo!="OK")

                                //eliminamos ip fija del servicio
                                $ipFija->setEstado("Eliminado");
                                $this->emInfraestructura->persist($ipFija);
                                $this->emInfraestructura->flush();

                                //eliminamos indice del plan nuevo
                                $this->servicioGeneral
                                     ->setEstadoServicioProductoCaracteristica($servProdCaractIndiceNuevo, "Eliminado");

                                $mensaje = 'No se configuro la ip:<b>' . $ipFija->getIp() . '</b> en el SCE, <br>'
                                         . 'Se reverso el cambio de plan, <br>'
                                         . 'Favor notificar a Sistemas, <br>'
                                         .  $mensajeSce;

                                break;
                            }//if($statusSce == "ERROR")
                            
                            return $respuestaFinal;
                        }
                        else
                        {
                            $status = "ERROR";
                            $mensaje = "Error Desconocido en script que revisa la ip configurada, Favor Notificar a Sistemas!";
                        }
                    }//if flag plan edicion limitada = 1
                    else
                    {
                        $arrayParametros=array(
                                        'servicioTecnico'   => $servicioTecnico,
                                        'interfaceElemento' => $interfaceElemento,
                                        'modeloElemento'    => $modeloElemento,
                                        'macOnt'            => $servProdCaractMacOnt->getValor(),
                                        'perfil'            => $perfilNuevo,
                                        'login'             => $servicio->getPuntoId()->getLogin(),
                                        'ontLineProfile'    => "",
                                        'serviceProfile'    => "",
                                        'serieOnt'          => "",
                                        'vlan'              => "",
                                        'gemPort'           => "",
                                        'trafficTable'      => ""
                                      );     
                        
                        //activar plan nuevo
                        $respuestaFinal = $this->activarService
                            ->activarClienteMdSinIp($arrayParametros);
                        $status = $respuestaFinal[0]['status'];

                        if($status == "OK")
                        {
                            return $respuestaFinal;
                        }
                        
                        $mensaje = $respuestaFinal[0]['mensaje'];
                    }
                    //plan nuevo: HOME
                    break;
                //Home -> Pro
                case "PRO":
                    //si el plan nuevo tiene producto ip
                    if($flagProd == 1)
                    {
                        //solicitar la ip para el nuevo plan
                        $arregloIps = $this->recursosRed
                            ->getIpsDisponiblePoolOlt(1, $servicioTecnico->getElementoId(), $servicio->getId(), 
                                                      $servicio->getPuntoId()->getId(), "SI", $planCabNuevo->getId());

                        if($arregloIps['error'])
                        {
                            $status = "ERROR";
                            $mensaje = $arregloIps['error'];
                            break;
                        }

                        //grabar ip
                        $arrayIps = $arregloIps['ips'];

                        //verifica si la ip se encuentra configurada
                        $resultadJsonIpConfig = $this->verificarIpFijaConfigurada($servicioTecnico, $modeloElemento, $arrayIps[0]['ip']);

                        $status = $resultadJsonIpConfig->status;
                        $macIpConf = $resultadJsonIpConfig->mensaje;
                        if($status == "ERROR")
                        {
                            $olt = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                           ->find($servicioTecnico->getElementoId());
                            $mensaje = "Ip: <b>" . $arrayIps[0]['ip'] ."</b> ya se encuentra configurada en <b>" . 
                                        $olt->getNombreElemento() ."</b> con la mac <b>" . 
                                        $macIpConf ."</b>. Favor notificar a Sistemas.";
                            break;
                        }//if($status == "ERROR")
                        else if($status=="OK")
                        {
                            //grabar ip en estado Reservada
                            $ipFija = $this->servicioGeneral
                                ->reservarIpAdicional($arrayIps[0]['ip'], $arrayIps[0]['tipo'], $servicio, $usrCreacion, $ipCreacion);
                            
                            $arrayParametros=array(
                                        'servicio'          => $servicio,
                                        'servicioTecnico'   => $servicioTecnico,
                                        'interfaceElemento' => $interfaceElemento,
                                        'modeloElemento'    => $modeloElemento,
                                        'producto'          => $producto,
                                        'macOnt'            => $macOnt,
                                        'macWifi'           => $macWifi,
                                        'perfil'            => $perfilNuevo,
                                        'login'             => $login,
                                        'usrCreacion'       => $usrCreacion
                                      );
                            
                            //activar nuevo plan con ip
                            $respuestaFinal = $this->activarService
                                ->activarClienteMdConIp($arrayParametros);
                            $status = $respuestaFinal[0]['status'];
                            if($status == "OK")
                            {
                                //se activa la ip
                                $ipFija->setEstado("Activo");
                                $this->emInfraestructura->persist($ipFija);
                                $this->emInfraestructura->flush();

                                return $respuestaFinal;
                            }//if($status == "OK")
                            
                            $mensaje = $respuestaFinal[0]['mensaje'];
                        }//else if($status=="OK")
                        else
                        {
                            $status = "ERROR";
                            $mensaje = "Error Desconocido en script que revisa la ip configurada, Favor Notificar a Sistemas!";
                        }
                        
                    }//if($flagProd == 1)
                    else
                    {
                        $arrayParametros=array(
                                        'servicioTecnico'   => $servicioTecnico,
                                        'interfaceElemento' => $interfaceElemento,
                                        'modeloElemento'    => $modeloElemento,
                                        'macOnt'            => $servProdCaractMacOnt->getValor(),
                                        'perfil'            => $perfilNuevo,
                                        'login'             => $servicio->getPuntoId()->getLogin(),
                                        'ontLineProfile'    => "",
                                        'serviceProfile'    => "",
                                        'serieOnt'          => "",
                                        'vlan'              => "",
                                        'gemPort'           => "",
                                        'trafficTable'      => ""
                                      );
                        
                        //activar nuevo plan sin ip
                        $arrayFinal = $this->activarService
                                           ->activarClienteMdSinIp($arrayParametros);

                        $status = $arrayFinal[0]['status'];
                        if($status == "OK")
                        {
                            return $arrayFinal;
                        }
                        
                        $mensaje = $arrayFinal[0]['mensaje'];
                    }
                    //plan nuevo: PRO
                    break;
                //Home -> Pyme
                case "PYME":
                    if($flagProd == 1)
                    {
                        //plan nuevo tiene ips
                        $arregloIps = $this->recursosRed
                            ->getIpsDisponiblePoolOlt(1, $servicioTecnico->getElementoId(), $servicio->getId(), 
                                                      $servicio->getPuntoId()->getId(), "SI", $planCabNuevo->getId());

                        if($arregloIps['error'])
                        {
                            $status = "ERROR";
                            $mensaje = $arregloIps['error'];
                            break;
                        }

                        //grabar ip
                        $arrayIps = $arregloIps['ips'];

                        //verifica si la ip se encuentra configurada
                        $resultadJsonIpConfig = $this->verificarIpFijaConfigurada($servicioTecnico, $modeloElemento, $arrayIps[0]['ip']);

                        $status = $resultadJsonIpConfig->status;
                        $macIpConf = $resultadJsonIpConfig->mensaje;
                        if($status == "ERROR")
                        {
                            $olt = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                           ->find($servicioTecnico->getElementoId());
                            $mensaje = "Ip: <b>" . $arrayIps[0]['ip'] ."</b> ya se encuentra configurada en <b>" . 
                                        $olt->getNombreElemento() ."</b> con la mac <b>" . 
                                        $macIpConf ."</b>. Favor notificar a Sistemas.";
                            break;
                        }//if($status == "ERROR")
                        else if($status == "OK")
                        {
                            //grabar la ip en estado Reservada
                            $ipFija = $this->servicioGeneral
                                ->reservarIpAdicional($arrayIps[0]['ip'], $arrayIps[0]['tipo'], $servicio, $usrCreacion, $ipCreacion);
                            
                            $arrayParametros=array(
                                        'servicio'          => $servicio,
                                        'servicioTecnico'   => $servicioTecnico,
                                        'interfaceElemento' => $interfaceElemento,
                                        'modeloElemento'    => $modeloElemento,
                                        'producto'          => $producto,
                                        'macOnt'            => $macOnt,
                                        'macWifi'           => $servProdCaracMacWifi->getValor(),
                                        'perfil'            => $perfilNuevo,
                                        'login'             => $login,
                                        'usrCreacion'       => $usrCreacion
                                      );
                            
                            //activar servicio con ip en el plan
                            $respuestaFinal = $this->activarService
                                ->activarClienteMdConIp($arrayParametros);

                            $status = $respuestaFinal[0]['status'];
                            
                            //si se activa bien
                            if($status == "OK")
                            {
                                //se activa ip en la base
                                $ipFija->setEstado("Activo");
                                $this->emInfraestructura->persist($ipFija);
                                $this->emInfraestructura->flush();

                                return $respuestaFinal;
                            }//if($status == "OK")
                            //si no se activa la ip
                            else
                            {
                                //se elimina la ip en la base
                                $ipFija->setEstado("Eliminado");
                                $this->emInfraestructura->persist($ipFija);
                                $this->emInfraestructura->flush();
                            }
                            
                            $mensaje = $respuestaFinal[0]['mensaje'];
                        }//else if($status == "OK")
                        else
                        {
                            $status = "ERROR";
                            $mensaje = "Error Desconocido en script que revisa la ip configurada, Favor Notificar a Sistemas!";
                        }                        
                    }//if($flagProd == 1)
                    else
                    {
                        $status="ERROR";
                        $mensaje = 'El nuevo plan: <b>' . $planCabNuevo->getNombrePlan() . '</b>, <br>'
                            . 'No tiene definido el producto IP FIJA, <br> '
                            . 'Favor Notificar al Dept. Marketing para que regularice el Plan!';
                    }
                    //plan nuevo: PYME
                    break;
            }//switch($planCabNuevo->getTipo())
        }//if($status == "OK")
        else
        {
            $arrayFinal[] = array('status' => "ERROR",
                'mensaje' => "No se puede realizar el cambio de Plan, <br>"
                . "Fallo la cancelacion del plan anterior:<b>" . $planCabViejo->getNombrePlan() . "</b><br>"
                . "Mensaje:" . $mensaje);
            return $arrayFinal;
        }
        
        //si fallo la activacion del plan nuevo
        if($status != "OK")
        {
            //regresar el tipo de negocio al original
            $this->servicioGeneral->setTipoNegocioEnInfoPunto($punto, $planCabViejo->getTipo(), $idEmpresa);
            
            $arrayParametros=array(
                                        'servicioTecnico'   => $servicioTecnico,
                                        'interfaceElemento' => $interfaceElemento,
                                        'modeloElemento'    => $modeloElemento,
                                        'macOnt'            => $macOnt,
                                        'perfil'            => $servProdCaracPerfil->getValor(),
                                        'login'             => $login,
                                        'ontLineProfile'    => "",
                                        'serviceProfile'    => "",
                                        'serieOnt'          => "",
                                        'vlan'              => "",
                                        'gemPort'           => "",
                                        'trafficTable'      => ""
                                      );
            
            //activar plan anterior
            $respuestaArray = $this->activarService
                                   ->activarClienteMdSinIp($arrayParametros);

            $mensajeActivarPlanAnterior = $respuestaArray[0]['mensaje'];
            $indiceNuevoAnterior = $mensajeActivarPlanAnterior;
            
            if($respuestaArray[0]['status']!="OK")
            {
                $mensaje = $mensaje. "<br>Activacion Plan Anterior:".$mensajeActivarPlanAnterior;
            }

            //grabamos el nuevo indice del plan anterior
            $this->servicioGeneral
                ->ingresarServicioProductoCaracteristica($servicio, $producto, "INDICE CLIENTE", $indiceNuevoAnterior, $usrCreacion);

            $arrayFinal[] = array('status' => "ERROR",
                'mensaje' => "No se puede realizar el cambio de Plan, <br>"
                . "Fallo activacion del plan nuevo:<b>" . $planCabNuevo->getNombrePlan() . "</b><br>"
                . $mensaje);
            return $arrayFinal;
        }//if($status != "OK")
    }
    
    /**
     * Funcion que realiza el cambio de plan de clientes en equipos TELLION que aprovisionan CNR de:
     * - Home -> Home
     * - Home -> Pro
     * - Home -> Pyme
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 07-12-2015
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 09-05-2016   Se agrega parametro empresa en metodo cambioPlanHomeTellionCnr por conflictos de producto INTERNET DEDICADO
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.2 02-06-2016   Se corrige nombre de variable que sirve para retornar la respuesta del procesamiento del cambio de plan
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.3 02-06-2016   Se agrega linea para recuperar mensaje de respuesta de script
     * 
     * @param $arrayPeticiones  arreglo con las variables necesarias para realizar el cambio de plan
     */
    public function cambioPlanHomeTellionCnr($arrayPeticiones)
    {
        $punto                          = $arrayPeticiones[0]['punto'];
        $login                          = $arrayPeticiones[0]['login'];
        $servicio                       = $arrayPeticiones[0]['servicio'];
        $servicioTecnico                = $arrayPeticiones[0]['servicioTecnico'];
        $planCabViejo                   = $arrayPeticiones[0]['planCabViejo'];
        $interfaceElemento              = $arrayPeticiones[0]['interfaceElemento'];
        $modeloElemento                 = $arrayPeticiones[0]['modeloElemento'];
        $planCabNuevo                   = $arrayPeticiones[0]['planCabNuevo'];
        $planDetNuevo                   = $arrayPeticiones[0]['planDetNuevo'];
        $producto                       = $arrayPeticiones[0]['producto'];         
        $arrayProdIp                    = $arrayPeticiones[0]['arrayProdIp'];
        $macOnt                         = $arrayPeticiones[0]['macOnt'];
        $perfilNuevo                    = $arrayPeticiones[0]['perfilNuevo'];
        $servProdCaractIndiceCliente    = $arrayPeticiones[0]['servProdCaractIndiceCliente'];
        $servProdCaractMacOnt           = $arrayPeticiones[0]['servProdCaracMacOnt'];
        $servProdCaracMacWifi           = $arrayPeticiones[0]['servProdCaracMacWifi'];
        $servProdCaracPerfil            = $arrayPeticiones[0]['servProdCaracPerfil'];
        $idEmpresa                      = $arrayPeticiones[0]['idEmpresa'];
        $usrCreacion                    = $arrayPeticiones[0]['usrCreacion'];
        $ipCreacion                     = $arrayPeticiones[0]['ipCreacion'];

        $flagViejoEdLimitada = 0;
        $flagProd            = 0;
        $status              = "ERROR";
        
        //verificar si plan nuevo tiene ip en el plan
        $flagProd = $this->servicioGeneral->verificarPlanTieneIp($planDetNuevo, $arrayProdIp);
        
        //validar que el plan pyme tenga producto ip definido en el plan
        if($planCabNuevo->getTipo() == "PYME" && $flagProd<=0)
        {
            $respuestaFinal[] = array('status'  => 'ERROR', 
                                      'mensaje' => 'El Plan: <b>'.$planCabNuevo->getNombrePlan.'</b>, <br>'
                                                 . 'No tiene producto Ip definido, Favor Notificar al Dep. Marketing!');
            return $respuestaFinal;
        }//if($planCabNuevo->getTipo() == "PYME" && $flagProdIpPlanNuevo<=0)
        
        //validar que la mac que tiene el servicio este conectada en el olt
        //antes de ejecutar la cancelacion
        if($flagProd)
        {
            //*OBTENER SCRIPT MAC WIFI--------------------------------------------------------*/
            $scriptArrayMacWifi = $this->servicioGeneral->obtenerArregloScript("obtenerMacIpDinamica", $modeloElemento);
            $idDocumentoMacWifi = $scriptArrayMacWifi[0]->idDocumento;
            //*----------------------------------------------------------------------*/
            //verificar mac wifi en el olt
            $resultadoJsonMacWifi = $this->activarService
                                         ->verificarMacWifi($servicioTecnico, $interfaceElemento, $servProdCaracMacWifi->getValor(), 
                                                            $servProdCaractIndiceCliente->getValor(), $idDocumentoMacWifi);
            $statusMacWifi = $resultadoJsonMacWifi->status;
            if($statusMacWifi == "ERROR")
            {
                $respuestaFinal[] = array('status' => $statusMacWifi, 'mensaje' => $resultadoJsonMacWifi->mensaje);
                return $respuestaFinal;
            }
        }//if($planCabNuevo->getTipo() == "PRO" || $planCabNuevo->getTipo() == "PYME")        
        
        $arrayParametros = array(
                                    'servicioTecnico'   => $servicioTecnico,
                                    'interfaceElemento' => $interfaceElemento,
                                    'modeloElemento'    => $modeloElemento,
                                    'spcIndiceCliente'  => $servProdCaractIndiceCliente,
                                    'login'             => $login,
                                    'spcSpid'           => "",
                                    'spcMacOnt'         => "",
                                    'idEmpresa'         => $idEmpresa
                                );
        $respuestaArray = $this->cancelarService->cancelarServicioMdSinIp($arrayParametros);
        $status         = $respuestaArray[0]['status'];
        $mensaje        = $respuestaArray[0]['mensaje'];
        if($status == "OK")
        {
            //eliminamos el indice anterior
            $this->servicioGeneral->setEstadoServicioProductoCaracteristica($servProdCaractIndiceCliente, "Eliminado");

            //actualizamos el tipo de negocio
            $this->servicioGeneral->setTipoNegocioEnInfoPunto($punto, $planCabNuevo->getTipo(), $idEmpresa);
                        
            switch($planCabNuevo->getTipo())
            {
                //Home -> Home
                case "HOME":
                   $arrayParametros=array(
                                    'servicioTecnico'   => $servicioTecnico,
                                    'interfaceElemento' => $interfaceElemento,
                                    'modeloElemento'    => $modeloElemento,
                                    'macOnt'            => $servProdCaractMacOnt->getValor(),
                                    'perfil'            => $perfilNuevo,
                                    'login'             => $servicio->getPuntoId()->getLogin(),
                                    'ontLineProfile'    => "",
                                    'serviceProfile'    => "",
                                    'serieOnt'          => "",
                                    'vlan'              => "",
                                    'gemPort'           => "",
                                    'trafficTable'      => ""
                                  );     

                    //activar plan nuevo
                    $respuestaFinal = $this->activarService
                                           ->activarClienteMdSinIp($arrayParametros);
                    $status = $respuestaFinal[0]['status'];

                    if($status == "OK")
                    {
                        return $respuestaFinal;
                    }

                    $mensaje = $respuestaFinal[0]['mensaje'];
                    //plan nuevo: HOME
                    break;
                //Home -> Pro
                case "PRO":
                     $arrayParametros=array(
                                            'servicioTecnico'   => $servicioTecnico,
                                            'interfaceElemento' => $interfaceElemento,
                                            'modeloElemento'    => $modeloElemento,
                                            'macOnt'            => $servProdCaractMacOnt->getValor(),
                                            'perfil'            => $perfilNuevo,
                                            'login'             => $servicio->getPuntoId()->getLogin(),
                                            'ontLineProfile'    => "",
                                            'serviceProfile'    => "",
                                            'serieOnt'          => "",
                                            'vlan'              => "",
                                            'gemPort'           => "",
                                            'trafficTable'      => ""
                                          );

                    //activar nuevo plan sin ip
                    $arrayFinal = $this->activarService
                                       ->activarClienteMdSinIp($arrayParametros);

                    $status = $arrayFinal[0]['status'];
                    if($status == "OK")
                    {
                        return $arrayFinal;
                    }

                    $mensaje = $arrayFinal[0]['mensaje'];

                    //plan nuevo: PRO
                    break;
                //Home -> Pyme
                case "PYME":
                    if($flagProd == 1)
                    {
                        //plan nuevo tiene ips
                        $arregloIps = $this->recursosRed
                                           ->getIpsDisponibleScopeOlt(1, $servicioTecnico->getElementoId(), $servicio->getId(), 
                                                                      $servicio->getPuntoId()->getId(), "SI", $planCabNuevo->getId());
                
                        if($arregloIps['error'])
                        {
                            $status  = "ERROR";
                            $mensaje = $arregloIps['error'];
                            break;
                        }

                        //grabar ip
                        $arrayIps = $arregloIps['ips'];

                        //grabar la ip en estado Reservada
                        $ipFija   = $this->servicioGeneral
                                         ->reservarIpAdicional($arrayIps[0]['ip'], $arrayIps[0]['tipo'], $servicio, $usrCreacion, $ipCreacion);

                        $arrayParametros=array(
                                    'servicio'          => $servicio,
                                    'servicioTecnico'   => $servicioTecnico,
                                    'interfaceElemento' => $interfaceElemento,
                                    'modeloElemento'    => $modeloElemento,
                                    'producto'          => $producto,
                                    'macOnt'            => $macOnt,
                                    'macWifi'           => $servProdCaracMacWifi->getValor(),
                                    'perfil'            => $perfilNuevo,
                                    'login'             => $login,
                                    'usrCreacion'       => $usrCreacion
                                  );

                        //activar servicio con ip en el plan
                        $respuestaFinal = $this->activarService
                                               ->activarClienteMdConIp($arrayParametros);

                        $status = $respuestaFinal[0]['status'];

                        //si se activa bien
                        if($status == "OK")
                        {
                            //se activa ip en la base
                            $ipFija->setEstado("Activo");
                            $this->emInfraestructura->persist($ipFija);
                            $this->emInfraestructura->flush();

                            return $respuestaFinal;
                        }//if($status == "OK")
                        //si no se activa la ip
                        else
                        {
                            //se elimina la ip en la base
                            $ipFija->setEstado("Eliminado");
                            $this->emInfraestructura->persist($ipFija);
                            $this->emInfraestructura->flush();
                        }

                        $mensaje = $respuestaFinal[0]['mensaje'];

                    }//if($flagProd == 1)
                    else
                    {
                        $status  =   "ERROR";
                        $mensaje =   'El nuevo plan: <b>' . $planCabNuevo->getNombrePlan() . '</b>, <br>'
                                   . 'No tiene definido el producto IP FIJA, <br> '
                                   . 'Favor Notificar al Dept. Marketing para que regularice el Plan!';
                    }
                    //plan nuevo: PYME
                    break;
            }//switch($planCabNuevo->getTipo())
        }//if($status == "OK")
        else
        {
            $arrayFinal[] = array(  'status'  => "ERROR",
                                    'mensaje' => "No se puede realizar el cambio de Plan, <br>"
                                                ."Fallo la cancelacion del plan anterior:<b>" . $planCabViejo->getNombrePlan() . "</b><br>"
                                                ."Mensaje:" . $mensaje);
            return $arrayFinal;
        }
        
        //si fallo la activacion del plan nuevo
        if($status != "OK")
        {
            //regresar el tipo de negocio al original
            $this->servicioGeneral->setTipoNegocioEnInfoPunto($punto, $planCabViejo->getTipo(), $idEmpresa);
            
            $arrayParametros=array(
                                        'servicioTecnico'   => $servicioTecnico,
                                        'interfaceElemento' => $interfaceElemento,
                                        'modeloElemento'    => $modeloElemento,
                                        'macOnt'            => $macOnt,
                                        'perfil'            => $servProdCaracPerfil->getValor(),
                                        'login'             => $login,
                                        'ontLineProfile'    => "",
                                        'serviceProfile'    => "",
                                        'serieOnt'          => "",
                                        'vlan'              => "",
                                        'gemPort'           => "",
                                        'trafficTable'      => ""
                                      );
            
            //activar plan anterior
            $respuestaArray = $this->activarService
                                   ->activarClienteMdSinIp($arrayParametros);

            $mensajeActivarPlanAnterior = $respuestaArray[0]['mensaje'];
            $indiceNuevoAnterior        = $mensajeActivarPlanAnterior;
            
            if($respuestaArray[0]['status']!="OK")
            {
                $mensaje = $mensaje. "<br>Activacion Plan Anterior:".$mensajeActivarPlanAnterior;
            }

            //grabamos el nuevo indice del plan anterior
            $this->servicioGeneral
                ->ingresarServicioProductoCaracteristica($servicio, $producto, "INDICE CLIENTE", $indiceNuevoAnterior, $usrCreacion);

            $arrayFinal[] = array('status'  => "ERROR",
                                  'mensaje' => "No se puede realizar el cambio de Plan, <br>"
                                              ."Fallo activacion del plan nuevo:<b>" . $planCabNuevo->getNombrePlan() . "</b><br>"
                                              .$mensaje);
            return $arrayFinal;
        }//if($status != "OK")
    }
    
    /**
     * Funcion que realiza el cambio de plan de clientes en equipos HUAWEI de:
     * - Home -> Home
     * - Home -> Pro
     * - Home -> Pyme
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 20-05-2015
     * @param $arrayPeticiones  arreglo con las variables necesarias para realizar el cambio de plan
     */
    public function cambioPlanHomeHuawei($arrayPeticiones)
    {
        $punto                          = $arrayPeticiones[0]['punto'];
        $servicio                       = $arrayPeticiones[0]['servicio'];
        $servicioTecnico                = $arrayPeticiones[0]['servicioTecnico'];
        $planCabViejo                   = $arrayPeticiones[0]['planCabViejo'];
        $interfaceElemento              = $arrayPeticiones[0]['interfaceElemento'];
        $planCabNuevo                   = $arrayPeticiones[0]['planCabNuevo'];
        $planDetNuevo                   = $arrayPeticiones[0]['planDetNuevo'];
        $arrayProdIp                    = $arrayPeticiones[0]['arrayProdIp'];
        $producto                       = $arrayPeticiones[0]['producto'];
        $productoIp                     = $arrayPeticiones[0]['productoIp'];
        $macOnt                         = $arrayPeticiones[0]['macOnt'];
        $servProdCaractIndiceCliente    = $arrayPeticiones[0]['servProdCaractIndiceCliente'];
        $idEmpresa                      = $arrayPeticiones[0]['idEmpresa'];
        $usrCreacion                    = $arrayPeticiones[0]['usrCreacion'];
        $ipCreacion                     = $arrayPeticiones[0]['ipCreacion'];
        $spcSpid                        = $arrayPeticiones[0]['spid'];
        $spcServiceProfile              = $arrayPeticiones[0]['serviceProfile'];
        $spcLineProfile                 = $arrayPeticiones[0]['lineProfile'];
        $spcVlan                        = $arrayPeticiones[0]['vlan'];
        $spcGemPort                     = $arrayPeticiones[0]['gemPort'];
        $spcTrafficTable                = $arrayPeticiones[0]['trafficTable'];
        $spcLineProfileAntes            = $arrayPeticiones[0]['lineProfileAntes'];
        $spcVlanAntes                   = $arrayPeticiones[0]['vlanAntes'];
        $spcGemPortAntes                = $arrayPeticiones[0]['gemPortAntes'];
        $spcTrafficTableAntes           = $arrayPeticiones[0]['trafficTableAntes'];
        $flagProd                       = 0;
        $status                         = "ERROR";
        
        $elemento = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($servicioTecnico->getElementoId());

        //obtener objeto modelo cnr
        $modeloElementoCnr = $this->emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                  ->findOneBy(array("nombreModeloElemento" => "CNR UCS C220",
                                                    "estado" => "Activo"));

        //obtener elemento cnr
        $elementoCnr = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                            ->findOneBy(array("modeloElementoId" => $modeloElementoCnr->getId()));
        $scriptArray = $this->servicioGeneral->obtenerArregloScript("configurarIpFija", $modeloElementoCnr);
        $idDocumentoConfig = $scriptArray[0]->idDocumento;
        $usuarioConfig     = $scriptArray[0]->usuario;

        //verificar si plan nuevo tiene ip
        $flagProdIpPlanNuevo = $this->servicioGeneral->verificarPlanTieneIp($planDetNuevo, $arrayProdIp);

        //verificar si plan nuevo tiene ip en el plan
        $flagProd            = $this->servicioGeneral->verificarPlanTieneIp($planDetNuevo, $arrayProdIp);

        //validar que el plan pyme tenga producto ip definido en el plan
        if($planCabNuevo->getTipo() == "PYME" && $flagProd <= 0)
        {
            $respuestaFinal[] = array('status'  => 'ERROR',
                                      'mensaje' => 'El Plan: <b>' . $planCabNuevo->getNombrePlan . '</b>, <br>'.
                                                   'No tiene producto Ip definido, Favor Notificar al Dep. Marketing!');
            return $respuestaFinal;
        }//if($planCabNuevo->getTipo() == "PYME" && $flagProdIpPlanNuevo<=0)
        //se debe eliminar las caracteristicas antiguas.        
        //actualizamos el tipo de negocio
        $this->servicioGeneral->setTipoNegocioEnInfoPunto($punto, $planCabNuevo->getTipo(), $idEmpresa);

        switch($planCabNuevo->getTipo())
        {
            //Home -> Home
            case "HOME":
                $arrParamReconectar = array(
                    'elemento'          => $elemento,
                    'interfaceElemento' => $interfaceElemento,
                    'spcIndiceCliente'  => $servProdCaractIndiceCliente,
                    'spcSpid'           => $spcSpid,
                    'servicioTecnico'   => $servicioTecnico,
                    'spcServiceProfile' => $spcServiceProfile->getValor(),
                    'spcLineProfile'    => $spcLineProfile,
                    'spcVlan'           => $spcVlan,
                    'spcGemPort'        => $spcGemPort,
                    'spcTrafficTable'   => $spcTrafficTable
                );

                $resultadJson = $this->reconectarService->reconectarServicioOltHuawei($arrParamReconectar);

                $respuestaFinal[0]['status']  = $resultadJson->status;
                $respuestaFinal[0]['mensaje'] = $resultadJson->mensaje;
                $status                       = $respuestaFinal[0]['status'];
                $mensaje                      = $respuestaFinal[0]['mensaje'];

                if($status == "OK")
                {
                    return $respuestaFinal;
                }
                //plan nuevo: HOME
                break;
            //Home -> Pro
            case "PRO":
                $arrParamReconectar = array(
                    'elemento'          => $elemento,
                    'interfaceElemento' => $interfaceElemento,
                    'spcIndiceCliente'  => $servProdCaractIndiceCliente,
                    'spcSpid'           => $spcSpid,
                    'servicioTecnico'   => $servicioTecnico,
                    'spcServiceProfile' => $spcServiceProfile->getValor(),
                    'spcLineProfile'    => $spcLineProfile,
                    'spcVlan'           => $spcVlan,
                    'spcGemPort'        => $spcGemPort,
                    'spcTrafficTable'   => $spcTrafficTable
                );

                $resultadJson = $this->reconectarService->reconectarServicioOltHuawei($arrParamReconectar);

                $respuestaFinal[0]['status']  = $resultadJson->status;
                $respuestaFinal[0]['mensaje'] = $resultadJson->mensaje;
                $status                       = $respuestaFinal[0]['status'];
                $mensaje                      = $respuestaFinal[0]['mensaje'];

                if($status == "OK")
                {
                    if($flagProdIpPlanNuevo > 0)
                    {
                        //plan nuevo tiene ips
                        $arregloIps = $this->recursosRed
                                           ->getIpsDisponibleScopeOlt(1, 
                                                                      $servicioTecnico->getElementoId(), 
                                                                      $servicio->getId(), 
                                                                      $servicio->getPuntoId()->getId(), 
                                                                      "SI", 
                                                                      $planCabNuevo->getId());

                        if($arregloIps['error'])
                        {
                            $status  = "ERROR";
                            $mensaje = $arregloIps['error'];
                            break;
                        }

                        //grabar ip
                        $arrayIps = $arregloIps['ips'];

                        //grabar la ip en estado Reservada
                        $ipFijaNueva = $this->servicioGeneral
                                            ->reservarIpAdicional($arrayIps[0]['ip'], 
                                                                  $arrayIps[0]['tipo'], 
                                                                  $servicio, 
                                                                  $usrCreacion, 
                                                                  $ipCreacion);

                        $strMacModificada = $this->activarService->cambiarMac($macOnt);
                        
                        //activar ip fija
                        $arrayParametrosIpFija = array(
                                                        'ipFija' => $arrayIps[0]['ip'],
                                                        'macOnt' => $strMacModificada,
                                                        'idDocumento' => $idDocumentoConfig,
                                                        'usuario' => $usuarioConfig,
                                                        'elementoCnr' => $elementoCnr
                                                      );
                        $resultadJsonIpFija = $this->activarService->configurarIpFijaHuawei($arrayParametrosIpFija);
                        $statusIpFija       = $resultadJsonIpFija->status;
                        $mensajeIpFija      = $resultadJsonIpFija->mensaje;
                        $respuestaFinal[0]['status']  = $statusIpFija;
                        $respuestaFinal[0]['mensaje'] = $mensajeIpFija;
                        if($statusIpFija != "OK")
                        {
                            $ipFijaNueva->setEstado("Eliminado");
                            $this->emInfraestructura->persist($ipFijaNueva);
                            $this->emInfraestructura->flush();
                            break;
                        }
                        //se activa ip en la base
                        $ipFijaNueva->setEstado("Activo");
                        $this->emInfraestructura->persist($ipFijaNueva);
                        $this->emInfraestructura->flush();
                        $this->servicioGeneral->ingresarServicioProductoCaracteristica($servicio, 
                                                                                       $productoIp, 
                                                                                       "SCOPE", 
                                                                                       $arrayIps[0]['scope'], 
                                                                                       $usrCreacion);
                    }
                    return $respuestaFinal;
                }
                //plan nuevo: HOME
                break;
            //Home -> Pyme
            case "PYME":
                if($flagProd == 1)
                {
                    $arrParamReconectar = array(
                        'elemento'          => $elemento,
                        'interfaceElemento' => $interfaceElemento,
                        'spcIndiceCliente'  => $servProdCaractIndiceCliente,
                        'spcSpid'           => $spcSpid,
                        'servicioTecnico'   => $servicioTecnico,
                        'spcServiceProfile' => $spcServiceProfile->getValor(),
                        'spcLineProfile'    => $spcLineProfile,
                        'spcVlan'           => $spcVlan,
                        'spcGemPort'        => $spcGemPort,
                        'spcTrafficTable'   => $spcTrafficTable
                    );

                    $resultadJson = $this->reconectarService->reconectarServicioOltHuawei($arrParamReconectar);

                    $respuestaFinal[0]['status']  = $resultadJson->status;
                    $respuestaFinal[0]['mensaje'] = $resultadJson->mensaje;
                    $status  = $respuestaFinal[0]['status'];
                    $mensaje = $respuestaFinal[0]['mensaje'];

                    if($status == "OK")
                    {
                        //plan nuevo tiene ips
                        $arregloIps = $this->recursosRed
                                           ->getIpsDisponibleScopeOlt(1, 
                                                                      $servicioTecnico->getElementoId(), 
                                                                      $servicio->getId(), 
                                                                      $servicio->getPuntoId()->getId(), 
                                                                      "SI", 
                                                                      $planCabNuevo->getId());

                        if($arregloIps['error'])
                        {
                            $status  = "ERROR";
                            $mensaje = $arregloIps['error'];
                            break;
                        }

                        //grabar ip
                        $arrayIps = $arregloIps['ips'];

                        //grabar la ip en estado Reservada
                        $ipFija = $this->servicioGeneral
                                       ->reservarIpAdicional($arrayIps[0]['ip'], $arrayIps[0]['tipo'], $servicio, $usrCreacion, $ipCreacion);

                        $strMacModificada = $this->activarService->cambiarMac($macOnt);
                        //activar ip fija
                        $arrayParametrosIpFija = array(
                            'ipFija'        => $arrayIps[0]['ip'],
                            'macOnt'        => $strMacModificada,
                            'idDocumento'   => $idDocumentoConfig,
                            'usuario'       => $usuarioConfig,
                            'elementoCnr'   => $elementoCnr
                        );
                        $resultadJsonIpFija = $this->activarService->configurarIpFijaHuawei($arrayParametrosIpFija);
                        $status             = $resultadJsonIpFija->status;
                        $mensaje            = $resultadJsonIpFija->mensaje;
                        $respuestaFinal[0]['status']  = $status;
                        $respuestaFinal[0]['mensaje'] = $mensaje;
                        //si se activa bien
                        if($status == "OK")
                        {
                            //se activa ip en la base
                            $ipFija->setEstado("Activo");
                            $this->emInfraestructura->persist($ipFija);
                            $this->emInfraestructura->flush();
                            $this->servicioGeneral->ingresarServicioProductoCaracteristica($servicio, 
                                                                                           $productoIp, 
                                                                                           "SCOPE", 
                                                                                           $arrayIps[0]['scope'], 
                                                                                           $usrCreacion);
                            return $respuestaFinal;
                        }//if($status == "OK")
                        else
                        {
                            //se elimina la ip en la base
                            $ipFija->setEstado("Eliminado");
                            $this->emInfraestructura->persist($ipFija);
                            $this->emInfraestructura->flush();
                        }
                    }
                }
                else
                {
                    $status = "ERROR";
                    $mensaje = 'El nuevo plan: <b>' . $planCabNuevo->getNombrePlan() . '</b>, <br>'.
                               'No tiene definido el producto IP FIJA, <br> '.
                               'Favor Notificar al Dept. Marketing para que regularice el Plan!';
                }
                //plan nuevo: PYME
                break;
        }//switch($planCabNuevo->getTipo())
        //si fallo la activacion del plan nuevo
        if($status != "OK")
        {
            //regresar el tipo de negocio al original
            $this->servicioGeneral->setTipoNegocioEnInfoPunto($punto, $planCabViejo->getTipo(), $idEmpresa);

            $arrParamReconectar = array(
                'elemento'          => $elemento,
                'interfaceElemento' => $interfaceElemento,
                'spcIndiceCliente'  => $servProdCaractIndiceCliente,
                'spcSpid'           => $spcSpid,
                'servicioTecnico'   => $servicioTecnico,
                'spcServiceProfile' => $spcServiceProfile->getValor(),
                'spcLineProfile'    => $spcLineProfileAntes,
                'spcVlan'           => $spcVlanAntes,
                'spcGemPort'        => $spcGemPortAntes,
                'spcTrafficTable'   => $spcTrafficTableAntes
            );

            $resultadJson = $this->reconectarService->reconectarServicioOltHuawei($arrParamReconectar);

            $respuestaFinal[0]['status'] = $resultadJson->status;
            $respuestaFinal[0]['mensaje'] = $resultadJson->mensaje;
            $status = $respuestaFinal[0]['status'];
            $mensajeActivarPlanAnterior = $respuestaFinal[0]['mensaje'];

            if($status != "OK")
            {
                $mensaje = $mensaje . "<br>Activacion Plan Anterior:" . $mensajeActivarPlanAnterior;
            }

            $arrayFinal[] = array('status' => "ERROR",
                                  'mensaje' => "No se puede realizar el cambio de Plan, <br>".
                                               "Fallo activacion del plan nuevo:<b>" . $planCabNuevo->getNombrePlan() . "</b><br>".
                                               $mensaje);
            return $arrayFinal;
        }//if($status != "OK")
    }

    /**
     * Funcion que realiza el cambio de plan de clientes en equipos TELLION de:
     * - Pro -> Home
     * - Pro -> Pro
     * - Pro -> Pyme
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 13-08-2014
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 09-05-2016   Se agrega parametro empresa en metodo cambioPlanProTellion por conflictos de producto INTERNET DEDICADO
     * 
     * @param $arrayPeticiones  arreglo con las variables necesarias para realizar el cambio de plan
     */
    public function cambioPlanProTellion($arrayPeticiones)
    {
        $punto = $arrayPeticiones[0]['punto'];
        $login = $arrayPeticiones[0]['login'];

        $servicio           = $arrayPeticiones[0]['servicio'];
        $servicioTecnico    = $arrayPeticiones[0]['servicioTecnico'];
        $planCabViejo       = $arrayPeticiones[0]['planCabViejo'];
        $planDetViejo       = $arrayPeticiones[0]['planDetViejo'];

        $interfaceElemento  = $arrayPeticiones[0]['interfaceElemento'];
        $modeloElemento     = $arrayPeticiones[0]['modeloElemento'];

        $planCabNuevo       = $arrayPeticiones[0]['planCabNuevo'];
        $planDetNuevo       = $arrayPeticiones[0]['planDetNuevo'];
        $producto           = $arrayPeticiones[0]['producto'];            //producto internet
        $arrayProdIp        = $arrayPeticiones[0]['arrayProdIp'];
        $arrayProdInternet  = $arrayPeticiones[0]['arrayProdInternet'];

        $macOnt                         = $arrayPeticiones[0]['macOnt'];
        $perfilNuevo                    = $arrayPeticiones[0]['perfilNuevo'];
        $planCaractEdicionLimitada      = $arrayPeticiones[0]['planCaractEdicionLimitada'];
        $servProdCaractIndiceCliente    = $arrayPeticiones[0]['servProdCaractIndiceCliente'];
        $servProdCaractMacOnt           = $arrayPeticiones[0]['servProdCaracMacOnt'];
        $servProdCaracMacWifi           = $arrayPeticiones[0]['servProdCaracMacWifi'];
        $servProdCaracPerfil            = $arrayPeticiones[0]['servProdCaracPerfil'];

        $idEmpresa      = $arrayPeticiones[0]['idEmpresa'];
        $usrCreacion    = $arrayPeticiones[0]['usrCreacion'];
        $ipCreacion     = $arrayPeticiones[0]['ipCreacion'];

        $flagProdIpPlanNuevo    = 0;
        $flagProdIpPlanViejo    = 0;
        $flagProdAdicional      = 0;
        $ipFija                 = "";
        $tipoIpFija             = "";
        $indiceIpFija           = -1;
        $ipPlan                 = null;

        //servicios adicionales
        $arrayServiciosPorPunto = $this->emComercial->getRepository('schemaBundle:InfoServicio')
            ->findBy(array("puntoId" => $servicio->getPuntoId()->getId()));

        //*OBTENER SCRIPT MAC WIFI--------------------------------------------------------*/
        $scriptArrayMacWifi = $this->servicioGeneral->obtenerArregloScript("obtenerMacIpDinamica", $modeloElemento);
        $idDocumentoMacWifi = $scriptArrayMacWifi[0]->idDocumento;
        //*----------------------------------------------------------------------*/
        //verificar que no se quiera cambiar al plan 100mb
        if($planCaractEdicionLimitada)
        {
            if($planCaractEdicionLimitada->getValor() == "SI" && $planCabNuevo->getTipo() == "HOME")
            {
                $arrayFinal[] = array('status' => "ERROR",
                    'mensaje' => "No se puede realizar el cambio de Plan, <br>"
                    . "<b>Exclusivo para planes Home</b>");
                return $arrayFinal;
            }//if($planCaractEdicionLimitada->getValor() == "SI" && $planCabNuevo->getTipo() == "HOME")
        }//if($planCaractEdicionLimitada)
        else
        {
            $planCaractEdicionLimitada=false;
        }

        //verificar si plan nuevo tiene ip
        $flagProdIpPlanNuevo = $this->servicioGeneral->verificarPlanTieneIp($planDetNuevo, $arrayProdIp);

        //verificar si plan viejo tiene ip
        $flagProdIpPlanViejo = $this->servicioGeneral->verificarPlanTieneIp($planDetViejo, $arrayProdIp);

        //verificar si servicio tiene ip adicional
        $flagProdAdicional = $this->servicioGeneral->verificarIpFijaEnPunto($arrayServiciosPorPunto, $arrayProdIp, $servicio);
        
        //si tiene producto ip en el plan y producto ip adicional, error
        if($flagProdIpPlanViejo>0 && $flagProdAdicional>0)
        {
            $respuestaFinal[] = array('status'  => 'ERROR', 
                                      'mensaje' => 'Tipo de Negocio PRO, no debe tener mas de una IP FIJA en estado Activo, <br>'
                                                 . 'Favor Regularizar!');
            return $respuestaFinal;
        }
        
        //si no es de edicion limitada, y tiene ip (adicional o en el plan nuevo) y se pasa a un home, error
        if(!$planCaractEdicionLimitada && ($flagProdIpPlanNuevo>0 || $flagProdAdicional>0) && $planCabNuevo->getTipo()=="HOME" )
        {
            $respuestaFinal[] = array('status'  => 'ERROR', 
                                      'mensaje' => 'El Plan: <b>'.$planCabNuevo->getNombrePlan.'</b>, <br>'
                                                 . 'Tiene producto Ip definido y es de tipo HOME, <br>'
                                                 . 'Por lo tanto el cambio de plan NO es permitido <br>'
                                                 . 'Favor Notificar al Dep. Marketing!');
            return $respuestaFinal;
        }
        
        //validar que el plan pyme tenga producto ip definido en el plan
        if($planCabNuevo->getTipo() == "PYME" && $flagProdIpPlanNuevo<=0)
        {
            $respuestaFinal[] = array('status'  => 'ERROR', 
                                      'mensaje' => 'El Plan: <b>'.$planCabNuevo->getNombrePlan.'</b>, <br>'
                                                 . 'No tiene producto Ip definido, Favor Notificar al Dep. Marketing!');
            return $respuestaFinal;
        }//if($planCabNuevo->getTipo() == "PYME" && $flagProdIpPlanNuevo<=0)
        
        /* si tiene ip adicional o dentro del plan pro, se debe validar que la mac wifi que tiene el servicio
         * este conectada en el olt antes de ejecutar la cancelacion del servicio */
        if(($flagProdAdicional > 0 || $flagProdIpPlanViejo > 0))
        {
            //obtener unicamente los servicios adicionales de ip fija
            $arrayServiciosIpAdicional = $this->servicioGeneral
                                              ->getServiciosIpAdicionalEnPunto($arrayServiciosPorPunto, $arrayProdIp, $servicio);
            
            if(count($arrayServiciosIpAdicional)>1)
            {
                $respuestaFinal[] = array('status' => 'ERROR', 
                                          'mensaje' => 'Existe mas de un Servicio de Ip en el Punto, <br>'
                                                     . 'con tipo de negocio:<b>PRO</b>,<br>'
                                                     . 'Favor Notificar a Sistemas!');
                return $respuestaFinal;
            }
            
            //verificar mac wifi en el olt
            $resultadoJsonMacWifi = $this->activarService
                ->verificarMacWifi($servicioTecnico, $interfaceElemento, $servProdCaracMacWifi->getValor(), 
                                   $servProdCaractIndiceCliente->getValor(), $idDocumentoMacWifi);
            $statusMacWifi = $resultadoJsonMacWifi->status;
            if($statusMacWifi == "ERROR")
            {
                $respuestaFinal[] = array('status' => $statusMacWifi, 'mensaje' => $resultadoJsonMacWifi->mensaje);
                return $respuestaFinal;
            }
            
            //verificar si la ip del plan esta configurada en el olt
            if($flagProdIpPlanViejo>0)
            {
                //obtener ip del plan
                $arrayIpPlan = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                            ->findBy(array("servicioId"=>$servicio->getId(),"estado"=>"Activo"));

                //validar si existe mas de una ip de plan dentro del servicio
                if(count($arrayIpPlan)>1)
                {
                    $respuestaFinal[] = array('status' => 'ERROR', 
                                              'mensaje' => 'Existe mas de una Ip de Plan en el Servicio,<br>'
                                                         . 'Favor Notificar a Sistemas!');
                    return $respuestaFinal;
                }

                //asignar a una variable
                $ipPlan = $arrayIpPlan[0];
            }//if($flagProdIpPlanViejo>0)
            //verificar si la ip del servicio adicional esta configurada en el olt
            else if ($flagProdAdicional>0)
            {
                //obtener ip del plan
                $arrayIpPlan = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                            ->findBy(array("servicioId"=>$arrayServiciosIpAdicional[0]->getId(),"estado"=>"Activo"));

                //validar si existe mas de una ip de plan dentro del servicio
                if(count($arrayIpPlan)>1)
                {
                    $respuestaFinal[] = array('status' => 'ERROR', 
                                              'mensaje' => 'Existe mas de una Ip de Plan en el Servicio,<br>'
                                                         . 'Favor Notificar a Sistemas!');
                    return $respuestaFinal;
                }

                //asignar a una variable
                $ipPlan = $arrayIpPlan[0];
            }//else if ($flagProdAdicional>0)
            
            //si la variable ip plan esta llena
            if($ipPlan!=null)
            {
                //cambiar mac
                $macWifi = strtoupper( $this->cancelarService->cambiarMac($servProdCaracMacWifi->getValor()) );
                
                //verificar si la ip del plan esta configurada con la mac que se encuentra en telcos
                $resultadoJsonIpConfigPlan = $this->servicioGeneral
                            ->verificarIpMacConfigurada($modeloElemento, $servicioTecnico, $ipPlan->getIp(), 
                                                          $macWifi);
                $statusIpConfigPlan = $resultadoJsonIpConfigPlan->status;

                if($statusIpConfigPlan == "ERROR")
                {
                    $respuestaFinal[] = array('status' => $statusIpConfigPlan, 
                                              'mensaje' => $resultadoJsonIpConfigPlan->mensaje.", <br> Favor Revisar!");
                    return $respuestaFinal;
                }    
            }//if($ipPlan!=null)
            else if($ipPlan==null && ($flagProdAdicional>0 || $flagProdIpPlanViejo>0))
            {
                $arrayFinal[] = array('status' => "ERROR", 
                                      'mensaje' => "Existen Servicios de Ips Adicionales Activos, "
                                                 . "pero <b>NO tienen ip aprovisionadas</b>,"
                                                 . "<br>Favor notificar al Dep. Sistemas, para su regularizacion!");
                return $arrayFinal;
            }//else if($ipPlan==null && ($flagProdAdicional>0 || $flagProdIpPlanViejo>0))
            
            //si tiene ip (adicional o dentro del plan) se debe solicitar la ip (para pro y pyme)
            if($flagProdIpPlanNuevo > 0 || $flagProdAdicional > 0)
            {
                //cambiar tipo de negocio, para poder hacer la solicitud de ip fija
                $this->servicioGeneral->setTipoNegocioEnInfoPunto($punto, $planCabNuevo->getTipo(), $idEmpresa);
                
                //revisar si existen ips pa el nuevo plan
                $arregloIps = $this->recursosRed
                                   ->getIpsDisponiblePoolOlt(1, $servicioTecnico->getElementoId(), 
                                                             $servicio->getId(), $punto->getId(), "SI", $planCabNuevo->getId());

                if($arregloIps['error'])
                {
                    //regresamos el tipo de negocio al original
                    $this->servicioGeneral->setTipoNegocioEnInfoPunto($punto, $planCabViejo->getTipo(), $idEmpresa);

                    $arrayFinal[] = array('status' => "ERROR", 'mensaje' => $arregloIps['error']);
                    return $arrayFinal;
                }

                //verificar si la ip esta configurada
                $arrayIps = $arregloIps['ips'];

                $resultadJsonPerfil = $this->servicioGeneral
                    ->verificarIpConfigurada($modeloElemento, $servicioTecnico, $arrayIps[0]['ip']);

                $status = $resultadJsonPerfil->status;
                $macIpConf = $resultadJsonPerfil->mensaje;
                if($status == "ERROR")
                {
                    //regresamos el tipo de negocio al original
                    $this->servicioGeneral->setTipoNegocioEnInfoPunto($punto, $planCabViejo->getTipo(), $idEmpresa);

                    $olt = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                        ->find($servicioTecnico->getElementoId());
                    $respuestaFinal[] = array('status' => 'ERROR', 'mensaje' => 'Ip: <b>' . $arrayIps[0]['ip'] .
                        '</b> ya se encuentra configurada en <b>' . $olt->getNombreElemento() .
                        '</b> con la mac <b>' . $macIpConf .
                        '</b>. Favor notificar a Sistemas.');
                    return $respuestaFinal;
                }

                //se asigna la ip
                $ipFija = $arrayIps[0]['ip'];
                $tipoIpFija = $arrayIps[0]['tipo'];
            }//if(($planCabNuevo->getTipo() == "PRO" || $planCabNuevo->getTipo() == "PYME") &&
             //($flagProdIpPlanNuevo > 0 || $flagProdAdicional > 0))
        }//if(($flagProdAdicional > 0 || $flagProdIpPlanViejo > 0) &&
         //($planCabNuevo->getTipo() == "HOME" || $planCabNuevo->getTipo() == "PRO" || $planCabNuevo->getTipo() == "PYME"))
        
        /* si no tiene ip adicionales o ip dentro del plan y el cambio es a un plan pyme que tiene ip fija se debe
          verificar que la mac wifi que esta en el servicio este conectada en el otl */
        else if(($flagProdAdicional == 0 || $flagProdIpPlanViejo == 0) && ($planCabNuevo->getTipo() == "PYME"))
        {
            //verificar mac wifi en el olt
            $resultadoJsonMacWifi = $this->activarService
                ->verificarMacWifi($servicioTecnico, $interfaceElemento, $servProdCaracMacWifi->getValor(), 
                                   $servProdCaractIndiceCliente->getValor(), $idDocumentoMacWifi);
            $statusMacWifi = $resultadoJsonMacWifi->status;
            if($statusMacWifi == "ERROR")
            {
                $respuestaFinal[] = array('status' => $statusMacWifi, 'mensaje' => $resultadoJsonMacWifi->mensaje);
                return $respuestaFinal;
            }//if($statusMacWifi == "ERROR")

            //cambiar tipo de negocio, para poder hacer la solicitud de ip fija
            $this->servicioGeneral->setTipoNegocioEnInfoPunto($punto, $planCabNuevo->getTipo(), $idEmpresa);

            //revisar si existen ips pa el nuevo plan
            $arregloIps = $this->recursosRed
                               ->getIpsDisponiblePoolOlt(1, $servicioTecnico->getElementoId(), $servicio->getId(), 
                                                         $punto->getId(), "SI", $planCabNuevo->getId());

            if($arregloIps['error'])
            {
                //regresamos el tipo de negocio al original
                $this->servicioGeneral->setTipoNegocioEnInfoPunto($punto, $planCabViejo->getTipo(), $idEmpresa);

                $arrayFinal[] = array('status' => "ERROR", 'mensaje' => $arregloIps['error']);
                return $arrayFinal;
            }//if($arregloIps['error'])

            //verificar si la ip esta configurada
            $arrayIps = $arregloIps['ips'];

            $resultadJsonPerfil = $this->servicioGeneral
                ->verificarIpConfigurada($modeloElemento, $servicioTecnico, $arrayIps[0]['ip']);

            $status = $resultadJsonPerfil->status;
            $macIpConf = $resultadJsonPerfil->mensaje;
            if($status == "ERROR")
            {
                //regresamos el tipo de negocio al original
                $this->servicioGeneral->setTipoNegocioEnInfoPunto($punto, $planCabViejo->getTipo(), $idEmpresa);

                $olt = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                    ->find($servicioTecnico->getElementoId());
                $respuestaFinal[] = array('status' => 'ERROR', 'mensaje' => 'Ip: <b>' . $arrayIps[0]['ip'] .
                    '</b> ya se encuentra configurada en <b>' . $olt->getNombreElemento() .
                    '</b> con la mac <b>' . $macIpConf .
                    '</b>. Favor notificar a Sistemas.');
                return $respuestaFinal;
            }//if($status == "ERROR")

            //se asigna la ip
            $ipFija     = $arrayIps[0]['ip'];
            $tipoIpFija = $arrayIps[0]['tipo'];
        }//else if(($flagProdAdicional == 0 || $flagProdIpPlanViejo == 0) &&
         //($planCabNuevo->getTipo() == "PYME" && $flagProdIpPlanNuevo > 0))

        //cancelar servicio de internet y/o ip fija
        //si tiene ip dentro del plan
        if($flagProdIpPlanViejo > 0)
        {
            $objIpViejaFijaAdicional = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                ->findOneBy(array("servicioId"=>$servicio->getId(),"tipoIp"=>"FIJA", "estado"=>"Activo"));
            $arrayParametros = array(
                                        'servicioTecnico'   => $servicioTecnico,
                                        'interfaceElemento' => $interfaceElemento,
                                        'modeloElemento'    => $modeloElemento,
                                        'producto'          => $producto,
                                        'login'             => $login,
                                        'idEmpresa'         => $idEmpresa,
                                        'ipCreacion'        => $ipCreacion,
                                        'usrCreacion'       => $usrCreacion
                                    );
            $respuestaArray = $this->cancelarService->cancelarServicioMdConIp($arrayParametros);
            $status = $respuestaArray[0]['status'];
            
            //cancelar ip fija de la tabla info_ip
            if($status=="OK")
            {
                //obtener ips fijas q tiene el servicio
                $objIpFija = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                    ->findOneBy(array("servicioId"=>$servicioTecnico->getServicioId()->getId(),
                                                      "tipoIp"=>"FIJA", "estado"=>"Activo"));
                $objIpFija->setEstado("Cancel");
                $this->emInfraestructura->persist($objIpFija);
                $this->emInfraestructura->flush();
            }
            
            $mensaje = $respuestaArray[0]['mensaje'];
        }//if($flagProdIpPlanViejo > 0)
        //si no tiene ip dentro del plan
        else
        {
            //cancelar servicio sin ip
            $arrayParametros = array(
                                        'servicioTecnico'   => $servicioTecnico,
                                        'interfaceElemento' => $interfaceElemento,
                                        'modeloElemento'    => $modeloElemento,
                                        'spcIndiceCliente'  => $servProdCaractIndiceCliente,
                                        'login'             => $login,
                                        'spcSpid'           => "",
                                        'spcMacOnt'         => "",
                                        'idEmpresa'         => $idEmpresa
                                    );
            $respuestaArray = $this->cancelarService
                                  ->cancelarServicioMdSinIp($arrayParametros);            
            $status = $respuestaArray[0]['status'];
            $mensaje = $respuestaArray[0]['mensaje'];
            
            //si tiene ip fija adicional, se debe cancelar la ip adicional.
            if($flagProdAdicional > 0 && $status == "OK")
            {
                //obtener indice para localizar el servicio adicional
                $indiceIpFija = $this->servicioGeneral
                    ->obtenerIndiceIpFijaEnArrayServicios($arrayServiciosPorPunto, $arrayProdIp, $servicio);
                
                if($indiceIpFija>=0)
                {
                    $servicioIpAdicional = $arrayServiciosPorPunto[$indiceIpFija];
                    
                    $objIpViejaFijaAdicional = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                ->findOneBy(array("servicioId"=>$servicioIpAdicional->getId(),"tipoIp"=>"FIJA", "estado"=>"Activo"));
                    
                    $arrParametrosCancel = array(
                                                    'servicioTecnico'   => $servicioTecnico,
                                                    'modeloElemento'    => $modeloElemento,
                                                    'interfaceElemento' => $interfaceElemento,
                                                    'producto'          => $producto,
                                                    'servicio'          => $servicioIpAdicional,
                                                    'spcMac'            => "",
                                                    'scope'             => ""
                                                );
                    
                    $respuestaIpAdicionalArray = $this->cancelarService->cancelarServicioIp($arrParametrosCancel);

                    $status  = $respuestaIpAdicionalArray[0]['status'];
                    $mensaje = $respuestaIpAdicionalArray[0]['mensaje'];

                    //si la cancelacion de la ip fija adicional, falla
                    if($status != "OK")
                    {
                        $arrayParametros=array(
                                                'servicioTecnico'   => $servicioTecnico,
                                                'interfaceElemento' => $interfaceElemento,
                                                'modeloElemento'    => $modeloElemento,
                                                'macOnt'            => $macOnt,
                                                'perfil'            => $servProdCaracPerfil->getValor(),
                                                'login'             => $login,
                                                'ontLineProfile'    => "",
                                                'serviceProfile'    => "",
                                                'serieOnt'          => "",
                                                'vlan'              => "",
                                                'gemPort'           => "",
                                                'trafficTable'      => ""
                                              );
                        
                        //activa el servicio anterior
                        $arrayRespuestaActivar = $this->activarService
                                               ->activarClienteMdSinIp($arrayParametros);
                        $statusActivarAnterior  = $arrayRespuestaActivar[0]['status'];
                        $mensajeActivarAnterior = $arrayRespuestaActivar[0]['mensaje'];
                        
                        //si no se activa el rollback
                        if($statusActivarAnterior!="OK")
                        {
                            $mensaje = $mensaje . "<br>Activacion Plan Anterior:".$mensajeActivarAnterior;
                        }//if($statusActivarAnterior!="OK")
                        else
                        {
                            $indiceNuevoAnterior = $mensajeActivarAnterior;

                            //grabamos el nuevo indice del plan anterior
                            $this->servicioGeneral
                                ->ingresarServicioProductoCaracteristica($servicio, $producto, "INDICE CLIENTE", 
                                                                         $indiceNuevoAnterior, $usrCreacion);
                        }
                    }//if($status != "OK")
                    
                    //obtener pool para eliminar la caracteristica
                    $servProdCaracPoolAdicional = $this->servicioGeneral
                                                    ->getServicioProductoCaracteristica($servicioIpAdicional, "POOL IP", $producto);

                    //eliminamos el pool anterior
                    if($servProdCaracPoolAdicional)
                    {
                        $this->servicioGeneral->setEstadoServicioProductoCaracteristica($servProdCaracPoolAdicional, "Eliminado");
                    }//if($servProdCaracPoolAdicional)

                    //obtener perfil para eliminar la caracteristica
                    $servProdCaractPerfilAdicional = $this->servicioGeneral
                                    ->getServicioProductoCaracteristica($servicioIpAdicional, "PERFIL", $producto);

                    //eliminamos el perfil anterior
                    if($servProdCaractPerfilAdicional)
                    {
                        $this->servicioGeneral->setEstadoServicioProductoCaracteristica($servProdCaractPerfilAdicional, "Eliminado");
                    }//if($servProdCaractPerfilAdicional)
                }//if($indiceIpFija>=0)
                else
                {
                    $arrayFinal[] = array('status'  => "ERROR", 
                                          'mensaje' => "Existen Servicios de Ips Adicionales Activos, "
                                                     . "pero <b>NO tienen producto Ip definido</b>,"
                                                     . "<br>Favor notificar al Dep. Sistemas, para su regularizacion!");
                    return $arrayFinal;
                }//else
            }//if($flagProdAdicional > 0 && $status == "OK")
        }//else

        if($status == "OK")
        {
            //eliminamos el indice anterior
            $this->servicioGeneral->setEstadoServicioProductoCaracteristica($servProdCaractIndiceCliente, "Eliminado");

            //actualizamos el tipo de negocio
            $this->servicioGeneral->setTipoNegocioEnInfoPunto($punto, $planCabNuevo->getTipo(), $idEmpresa);

            switch($planCabNuevo->getTipo())
            {
                //Pro -> Home
                case "HOME":
                    $arrayParametros=array(
                                        'servicioTecnico'   => $servicioTecnico,
                                        'interfaceElemento' => $interfaceElemento,
                                        'modeloElemento'    => $modeloElemento,
                                        'macOnt'            => $macOnt,
                                        'perfil'            => $perfilNuevo,
                                        'login'             => $login,
                                        'ontLineProfile'    => "",
                                        'serviceProfile'    => "",
                                        'serieOnt'          => "",
                                        'vlan'              => "",
                                        'gemPort'           => "",
                                        'trafficTable'      => ""
                                      );
                    
                    $respuestaArray = $this->activarService
                        ->activarClienteMdSinIp($arrayParametros);
                    $status = $respuestaArray[0]['status'];
                    $mensaje = $respuestaArray[0]['mensaje'];

                    if($status == "OK")
                    {
                        //si tiene ip adicional se debe cancelar el servicio adicional
                        if($flagProdAdicional > 0)
                        {
                            $servicioIpAdicional = $arrayServiciosPorPunto[$indiceIpFija];
                            if($servicioIpAdicional)
                            {
                                $strEstadoServicioAdicional = "Cancel";
                                $observacionAdicional = "Se cancelo servicio adicional, por cambio de plan";
                                if($servicioIpAdicional->getEstado()=="PreAsignacionInfoTecnica" || 
                                   $servicioIpAdicional->getEstado()=="Asignada")
                                {
                                    $strEstadoServicioAdicional = "Anulado";
                                    $observacionAdicional = "Se Anulo servicio adicional, por cambio de plan";

                                }                                    
                                //cambiar estado al servicio adicional
                                $servicioIpAdicional->setEstado($strEstadoServicioAdicional);
                                $this->emComercial->persist($servicioIpAdicional);
                                $this->emComercial->flush();

                                //historial del servicio adicional
                                $servicioHistorial = new InfoServicioHistorial();
                                $servicioHistorial->setServicioId($servicioIpAdicional);
                                $servicioHistorial->setObservacion($observacionAdicional);
                                $servicioHistorial->setEstado($strEstadoServicioAdicional);
                                $servicioHistorial->setUsrCreacion($usrCreacion);
                                $servicioHistorial->setFeCreacion(new \DateTime('now'));
                                $servicioHistorial->setIpCreacion($ipCreacion);
                                $this->emComercial->persist($servicioHistorial);
                                $this->emComercial->flush();
                            }//if($servicioIpAdicional)
                        }//if($flagProdAdicional > 0)

                        return $respuestaArray;
                    }//if($status == "OK")

                    break;
                //Pro -> Pro
                case "PRO":
                    //si el plan tiene ip, se activa el servicio con la ip fija
                    if($flagProdIpPlanNuevo > 0)
                    {
                        $arrayParametros=array(
                                        'servicio'          => $servicio,
                                        'servicioTecnico'   => $servicioTecnico,
                                        'interfaceElemento' => $interfaceElemento,
                                        'modeloElemento'    => $modeloElemento,
                                        'producto'          => $producto,
                                        'macOnt'            => $macOnt,
                                        'macWifi'           => $servProdCaracMacWifi->getValor(),
                                        'perfil'            => $perfilNuevo,
                                        'login'             => $login,
                                        'usrCreacion'       => $usrCreacion
                                      );
                        $respuestaArray = $this->activarService
                                               ->activarClienteMdConIp($arrayParametros);

                        $status = $respuestaArray[0]['status'];
                        $mensaje = $respuestaArray[0]['mensaje'];
                        
                        //si no se activa correctamente el plan nuevo
                        if($status!="OK")
                        {
                            //se realiza break para que vaya a hacer rollback de scripts
                            break;
                        }
                        
                        return $respuestaArray;
                    }//if($flagProdIpPlanNuevo > 0)
                    //si no tiene ip dentro del plan
                    else
                    {
                        $arrayParametros=array(
                                        'servicioTecnico'   => $servicioTecnico,
                                        'interfaceElemento' => $interfaceElemento,
                                        'modeloElemento'    => $modeloElemento,
                                        'macOnt'            => $macOnt,
                                        'perfil'            => $perfilNuevo,
                                        'login'             => $login,
                                        'ontLineProfile'    => "",
                                        'serviceProfile'    => "",
                                        'serieOnt'          => "",
                                        'vlan'              => "",
                                        'gemPort'           => "",
                                        'trafficTable'      => ""
                                      );
                        
                        //se activa solo el internet
                        $respuestaArray = $this->activarService
                                               ->activarClienteMdSinIp($arrayParametros);
                        $status = $respuestaArray[0]['status'];
                        $mensaje = $respuestaArray[0]['mensaje'];

                        if($status == "OK")
                        {
                            $indiceNuevo = $mensaje;
                            //si tiene ip adicionales
                            if($flagProdAdicional > 0)
                            {
                                //seleccionar el servicio con ip adicional
                                $servicioIpAdicional = $arrayServiciosPorPunto[$indiceIpFija];

                                //se reserva la ip
                                $objIpFijaNueva = $this->servicioGeneral
                                    ->reservarIpAdicional($ipFija, $tipoIpFija, $servicioIpAdicional, $usrCreacion, $ipCreacion);
                                
                                //ingresar perfil para el servicio adicional
                                $this->servicioGeneral
                                        ->ingresarServicioProductoCaracteristica($servicioIpAdicional, $producto, "PERFIL", 
                                                                                $perfilNuevo, $usrCreacion);

                                //se activa el servicio de ip adicional
                                $respuestaIpArray = $this->activarService
                                                         ->activarServicioIp($servicioIpAdicional, $servicioTecnico, 
                                                                             $producto, $interfaceElemento, $modeloElemento);

                                $statusIp = $respuestaIpArray[0]['status'];
                                $mensajeIp = $respuestaIpArray[0]['mensaje'];

                                //si no se activa la ip
                                if($statusIp != "OK")
                                {
                                    //mensaje de error en activar ip adicional
                                    $mensaje = $mensajeIp;
                                    
                                    //eliminar ip nueva
                                    $objIpFijaNueva->setEstado("Eliminado");
                                    $this->emInfraestructura->persist($objIpFija);
                                    $this->emInfraestructura->flush();
                                    
                                    //grabar indice para poder cancelar el plan nuevo
                                    $servProdCaractIndiceNuevo = $this->servicioGeneral
                                        ->ingresarServicioProductoCaracteristica($servicio, $producto, "INDICE CLIENTE", 
                                                                                 $indiceNuevo, $usrCreacion);

                                    //se cancela plan nuevo
                                    $arrayParametros = array(
                                        'servicioTecnico'   => $servicioTecnico,
                                        'interfaceElemento' => $interfaceElemento,
                                        'modeloElemento'    => $modeloElemento,
                                        'spcIndiceCliente'  => $servProdCaractIndiceCliente,
                                        'login'             => $login,
                                        'spcSpid'           => "",
                                        'spcMacOnt'         => "",
                                        'idEmpresa'         => $idEmpresa
                                    );
                                    $respuestaCancelarArray = $this->cancelarService->cancelarServicioMdSinIp($arrayParametros);
                                    
                                    $statusCancelar = $respuestaCancelarArray[0]['status'];
                                    $mensajeCancelar = $respuestaCancelarArray[0]['mensaje'];
                                    
                                    if($statusCancelar!="OK")
                                    {
                                        $mensaje = $mensaje . "<br>"
                                                 . "Cancelar Plan Nuevo:" . $mensajeCancelar;
                                    }

                                    //eliminar el indice nuevo grabado para la cancelacion del internet
                                    $this->servicioGeneral
                                        ->setEstadoServicioProductoCaracteristica($servProdCaractIndiceNuevo, "Eliminado");

                                    break;
                                }//if($status != "OK")
                                
                                //obtener pool del servicio internet
                                $poolServicioAdicional = $mensajeIp;
                                
                                //ingresar pool para el servicio adicional
                                $this->servicioGeneral
                                    ->ingresarServicioProductoCaracteristica($servicioIpAdicional, $producto, "POOL IP", 
                                                                             $poolServicioAdicional, $usrCreacion);
                            }//end if flag producto adicional > 0

                            return $respuestaArray;
                        }//end if status = ok
                    }//end else

                    break;
                //Pro -> Pyme
                case "PYME":
                    //reservar ip para el plan
                    $ipPyme = $this->servicioGeneral
                                   ->reservarIpAdicional($ipFija, $tipoIpFija, $servicio, $usrCreacion, $ipCreacion);
                    
                    $arrayParametros=array(
                                        'servicio'          => $servicio,
                                        'servicioTecnico'   => $servicioTecnico,
                                        'interfaceElemento' => $interfaceElemento,
                                        'modeloElemento'    => $modeloElemento,
                                        'producto'          => $producto,
                                        'macOnt'            => $macOnt,
                                        'macWifi'           => $servProdCaracMacWifi->getValor(),
                                        'perfil'            => $perfilNuevo,
                                        'login'             => $login,
                                        'usrCreacion'       => $usrCreacion
                                      );
                    
                    //activar el plan nuevo
                    $respuestaArray = $this->activarService
                                           ->activarClienteMdConIp($arrayParametros);
                    $status = $respuestaArray[0]['status'];
                    $mensaje = $respuestaArray[0]['mensaje'];

                    if($status == "OK")
                    {
                        //cambiar estado (activar) de la ip del plan
                        $ipPyme->setEstado("Activo");
                        $this->emInfraestructura->persist($ipPyme);
                        $this->emInfraestructura->flush();
                        
                        //si tiene ip adicional se debe cancelar el servicio adicional
                        if($flagProdAdicional > 0)
                        {
                            $servicioIpAdicional = $arrayServiciosPorPunto[$indiceIpFija];
                            if($servicioIpAdicional)
                            {
                                $estadoServicioAdicional = "Cancel";
                                $observacionAdicional = "Se cancelo servicio adicional, por cambio de plan";
                                if($servicioIpAdicional->getEstado()=="PreAsignacionInfoTecnica" || 
                                   $servicioIpAdicional->getEstado()=="Asignada")
                                {
                                    $estadoServicioAdicional = "Anulado";
                                    $observacionAdicional = "Se Anulo servicio adicional, por cambio de plan";

                                }                                    
                                //cambiar estado al servicio adicional
                                $servicioIpAdicional->setEstado($estadoServicioAdicional);
                                $this->emComercial->persist($servicioIpAdicional);
                                $this->emComercial->flush();

                                //historial del servicio adicional
                                $servicioHistorial = new InfoServicioHistorial();
                                $servicioHistorial->setServicioId($servicioIpAdicional);
                                $servicioHistorial->setObservacion($observacionAdicional);
                                $servicioHistorial->setEstado($estadoServicioAdicional);
                                $servicioHistorial->setUsrCreacion($usrCreacion);
                                $servicioHistorial->setFeCreacion(new \DateTime('now'));
                                $servicioHistorial->setIpCreacion($ipCreacion);
                                $this->emComercial->persist($servicioHistorial);
                                $this->emComercial->flush();
                            }//if($servicioIpAdicional)
                        }//if($flagProdAdicional > 0)

                        return $respuestaArray;
                    }//if($status == "OK")
                    else
                    {
                        //eliminar ip nueva
                        $ipPyme->setEstado("Eliminado");
                        $this->emInfraestructura->persist($ipPyme);
                        $this->emInfraestructura->flush();
                    }

                    break;
            }//end switch
        }//if($status == "OK")
        else
        {
            //reversar cambio de tipo de negocio
            $this->servicioGeneral->setTipoNegocioEnInfoPunto($punto, $planCabViejo->getTipo(), $idEmpresa);
            
            $arrayFinal[] = array('status' => "ERROR",
                'mensaje' => "No se puede realizar el cambio de Plan, <br>"
                . "Fallo la cancelacion del plan anterior:<b>" . $planCabViejo->getNombrePlan() . "</b><br>"
                . "Mensaje:" . $mensaje);
            return $arrayFinal;
        }//else

        //si falla la activacion del nuevo plan
        if($status != "OK")
        {
            //reversar cambio de tipo de negocio
            $this->servicioGeneral->setTipoNegocioEnInfoPunto($punto, $planCabViejo->getTipo(), $idEmpresa);
                    
            //rollback de scripts
            if($flagProdIpPlanViejo > 0)
            {
                //reservamos ip vieja del plan, para poderla activar
                $objIpViejaFijaAdicional->setEstado("Reservada");
                $this->emInfraestructura->persist($objIpViejaFijaAdicional);
                $this->emInfraestructura->flush();
                
                $arrayParametros=array(
                                        'servicio'          => $servicio,
                                        'servicioTecnico'   => $servicioTecnico,
                                        'interfaceElemento' => $interfaceElemento,
                                        'modeloElemento'    => $modeloElemento,
                                        'producto'          => $producto,
                                        'macOnt'            => $macOnt,
                                        'macWifi'           => $servProdCaracMacWifi->getValor(),
                                        'perfil'            => $servProdCaracPerfil->getValor(),
                                        'login'             => $login,
                                        'usrCreacion'       => $usrCreacion
                                      );
                
                $respuestaArray = $this->activarService
                                       ->activarClienteMdConIp($arrayParametros);

                $mensajeActivarPlanAnterior = $respuestaArray[0]['mensaje'];
                $indiceNuevoAnterior = $mensajeActivarPlanAnterior;
                
                if($respuestaArray[0]['status']!="OK")
                {
                    $mensaje = $mensaje . "<br>Activacion Plan Anterior:" . $mensajeActivarPlanAnterior;
                }//if($respuestaArray[0]['status']!="OK")

                //grabamos el nuevo indice del plan anterior
                $this->servicioGeneral
                    ->ingresarServicioProductoCaracteristica($servicio, $producto, "INDICE CLIENTE", $indiceNuevoAnterior, $usrCreacion);
            }//if($flagProdIpPlanViejo > 0)
            else
            {
                $arrayParametros=array(
                                        'servicioTecnico'   => $servicioTecnico,
                                        'interfaceElemento' => $interfaceElemento,
                                        'modeloElemento'    => $modeloElemento,
                                        'macOnt'            => $macOnt,
                                        'perfil'            => $servProdCaracPerfil->getValor(),
                                        'login'             => $login,
                                        'ontLineProfile'    => "",
                                        'serviceProfile'    => "",
                                        'serieOnt'          => "",
                                        'vlan'              => "",
                                        'gemPort'           => "",
                                        'trafficTable'      => ""
                                      );
                
                $respuestaArray = $this->activarService
                                     ->activarClienteMdSinIp($arrayParametros);

                $mensajeActivarPlanAnterior = $respuestaArray[0]['mensaje'];
                $indiceNuevoAnterior = $mensajeActivarPlanAnterior;

                //grabamos el nuevo indice del plan anterior
                $this->servicioGeneral
                    ->ingresarServicioProductoCaracteristica($servicio, $producto, "INDICE CLIENTE", $indiceNuevoAnterior, $usrCreacion);

                if($flagProdAdicional > 0)
                {
                    //activamos el pool anterior
                    if($servProdCaracPoolAdicional)
                    {
                        $this->servicioGeneral->setEstadoServicioProductoCaracteristica($servProdCaracPoolAdicional, "Activo");
                    }//if($servProdCaracPoolAdicional)

                    //activamos el perfil anterior
                    if($servProdCaractPerfilAdicional)
                    {
                        $this->servicioGeneral->setEstadoServicioProductoCaracteristica($servProdCaractPerfilAdicional, "Activo");
                    }//if($servProdCaractPerfilAdicional)
                    
                    //reservamos ip vieja adicional, para poderla activar
                    $objIpViejaFijaAdicional->setEstado("Reservada");
                    $this->emInfraestructura->persist($objIpViejaFijaAdicional);
                    $this->emInfraestructura->flush();
                    
                    $servicioIpAdicional = $arrayServiciosPorPunto[$indiceIpFija];
                    $arrayRespuestaIp = $this->activarService
                         ->activarServicioIp($servicioIpAdicional, $servicioTecnico, $producto, $interfaceElemento, $modeloElemento);
                    
                    if($arrayRespuestaIp[0]['status']!="OK")
                    {
                        $mensaje = $mensaje . "<br>Activacion Ip Anterior:" . $arrayRespuestaIp[0]['mensaje'];
                    }
                }//if($flagProdAdicional > 0)
                
                if($respuestaArray[0]['status']!="OK")
                {
                    $mensaje = $mensaje . "<br>Activacion Plan Anterior:" . $mensajeActivarPlanAnterior;
                }//if($respuestaArray[0]['status']!="OK")
            }//else

            $arrayFinal[] = array('status' => "ERROR",
                'mensaje' => "No se puede realizar el cambio de Plan, <br>"
                . "Fallo activacion del plan nuevo:<b>" . $planCabNuevo->getNombrePlan() . "</b><br>"
                . "Mensaje:" . $mensaje);
            return $arrayFinal;
        }//if($status != "OK")
    }
    
    /**
     * Funcion que realiza el cambio de plan de clientes en equipos TELLION que aprovisionan CNR de:
     * - Pro -> Home
     * - Pro -> Pro
     * - Pro -> Pyme
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 07-12-2015
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 09-05-2016   Se agrega parametro empresa en metodo cambioPlanProTellionCnr por conflictos de producto INTERNET DEDICADO
     * 
     * @param $arrayPeticiones  arreglo con las variables necesarias para realizar el cambio de plan
     */
    public function cambioPlanProTellionCnr($arrayPeticiones)
    {
        $punto                          = $arrayPeticiones[0]['punto'];
        $login                          = $arrayPeticiones[0]['login'];
        $servicio                       = $arrayPeticiones[0]['servicio'];
        $servicioTecnico                = $arrayPeticiones[0]['servicioTecnico'];
        $planCabViejo                   = $arrayPeticiones[0]['planCabViejo'];
        $planDetViejo                   = $arrayPeticiones[0]['planDetViejo'];
        $interfaceElemento              = $arrayPeticiones[0]['interfaceElemento'];
        $modeloElemento                 = $arrayPeticiones[0]['modeloElemento'];
        $planCabNuevo                   = $arrayPeticiones[0]['planCabNuevo'];
        $planDetNuevo                   = $arrayPeticiones[0]['planDetNuevo'];
        $producto                       = $arrayPeticiones[0]['producto'];       
        $arrayProdIp                    = $arrayPeticiones[0]['arrayProdIp'];
        $productoIp                     = $arrayPeticiones[0]['productoIp'];
        $macOnt                         = $arrayPeticiones[0]['macOnt'];
        $perfilNuevo                    = $arrayPeticiones[0]['perfilNuevo'];
        $planCaractEdicionLimitada      = $arrayPeticiones[0]['planCaractEdicionLimitada'];
        $servProdCaractIndiceCliente    = $arrayPeticiones[0]['servProdCaractIndiceCliente'];
        $servProdCaractMacOnt           = $arrayPeticiones[0]['servProdCaracMacOnt'];
        $servProdCaracMacWifi           = $arrayPeticiones[0]['servProdCaracMacWifi'];
        $servProdCaracPerfil            = $arrayPeticiones[0]['servProdCaracPerfil'];
        $spcScopeAntes                  = $arrayPeticiones[0]['scopeAntes'];
        $idEmpresa                      = $arrayPeticiones[0]['idEmpresa'];
        $usrCreacion                    = $arrayPeticiones[0]['usrCreacion'];
        $ipCreacion                     = $arrayPeticiones[0]['ipCreacion'];

        $flagProdIpPlanNuevo    = 0;
        $flagProdIpPlanViejo    = 0;
        $flagProdAdicional      = 0;
        $ipFija                 = "";
        $tipoIpFija             = "";
        $indiceIpFija           = -1;
        $ipPlan                 = null;

        //servicios adicionales
        $arrayServiciosPorPunto = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                       ->findBy(array("puntoId" => $servicio->getPuntoId()->getId()));

        //*OBTENER SCRIPT MAC WIFI--------------------------------------------------------*/
        $scriptArrayMacWifi = $this->servicioGeneral->obtenerArregloScript("obtenerMacIpDinamica", $modeloElemento);
        $idDocumentoMacWifi = $scriptArrayMacWifi[0]->idDocumento;
        //*----------------------------------------------------------------------*/
        //verificar que no se quiera cambiar al plan 100mb
        if($planCaractEdicionLimitada)
        {
            if($planCaractEdicionLimitada->getValor() == "SI" && $planCabNuevo->getTipo() == "HOME")
            {
                $arrayFinal[] = array('status'  => "ERROR",
                                      'mensaje' => "No se puede realizar el cambio de Plan, <br>"
                                                  ."<b>Exclusivo para planes Home</b>");
                return $arrayFinal;
            }//if($planCaractEdicionLimitada->getValor() == "SI" && $planCabNuevo->getTipo() == "HOME")
        }//if($planCaractEdicionLimitada)
        else
        {
            $planCaractEdicionLimitada=false;
        }
        
        //obtener indice para localizar el servicio adicional
        $indiceIpFija = $this->servicioGeneral
                             ->obtenerIndiceIpFijaEnArrayServicios($arrayServiciosPorPunto, $arrayProdIp, $servicio);

        if($indiceIpFija>=0)
        {
            $servicioIpAdicional = $arrayServiciosPorPunto[$indiceIpFija];
        }

        //verificar si plan nuevo tiene ip
        $flagProdIpPlanNuevo = $this->servicioGeneral->verificarPlanTieneIp($planDetNuevo, $arrayProdIp);

        //verificar si plan viejo tiene ip
        $flagProdIpPlanViejo = $this->servicioGeneral->verificarPlanTieneIp($planDetViejo, $arrayProdIp);

        //verificar si servicio tiene ip adicional
        $flagProdAdicional = $this->servicioGeneral->verificarIpFijaEnPunto($arrayServiciosPorPunto, $arrayProdIp, $servicio);
        
        //si tiene producto ip en el plan y producto ip adicional, error
        if($flagProdIpPlanViejo>0 && $flagProdAdicional>0)
        {
            $respuestaFinal[] = array('status'  => 'ERROR', 
                                      'mensaje' => 'Tipo de Negocio PRO, no debe tener mas de una IP FIJA en estado Activo, <br>'
                                                 . 'Favor Regularizar!');
            return $respuestaFinal;
        }
        
        //si no es de edicion limitada, y tiene ip (adicional o en el plan nuevo) y se pasa a un home, error
        if(!$planCaractEdicionLimitada && ($flagProdIpPlanNuevo>0 || $flagProdAdicional>0) && $planCabNuevo->getTipo()=="HOME" )
        {
            $respuestaFinal[] = array('status'  => 'ERROR', 
                                      'mensaje' => 'El Plan: <b>'.$planCabNuevo->getNombrePlan.'</b>, <br>'
                                                 . 'Tiene producto Ip definido y es de tipo HOME, <br>'
                                                 . 'Por lo tanto el cambio de plan NO es permitido <br>'
                                                 . 'Favor Notificar al Dep. Marketing!');
            return $respuestaFinal;
        }
        
        //validar que el plan pyme tenga producto ip definido en el plan
        if($planCabNuevo->getTipo() == "PYME" && $flagProdIpPlanNuevo<=0)
        {
            $respuestaFinal[] = array('status'  => 'ERROR', 
                                      'mensaje' => 'El Plan: <b>'.$planCabNuevo->getNombrePlan.'</b>, <br>'
                                                 . 'No tiene producto Ip definido, Favor Notificar al Dep. Marketing!');
            return $respuestaFinal;
        }//if($planCabNuevo->getTipo() == "PYME" && $flagProdIpPlanNuevo<=0)
        
        /* si tiene ip adicional o dentro del plan pro, se debe validar que la mac wifi que tiene el servicio
         * este conectada en el olt antes de ejecutar la cancelacion del servicio */
        if(($flagProdAdicional > 0 || $flagProdIpPlanViejo > 0))
        {
            //obtener unicamente los servicios adicionales de ip fija
            $arrayServiciosIpAdicional = $this->servicioGeneral
                                              ->getServiciosIpAdicionalEnPunto($arrayServiciosPorPunto, $arrayProdIp, $servicio);

            if(count($arrayServiciosIpAdicional) > 1)
            {
                $respuestaFinal[] = array('status'  => 'ERROR',
                                          'mensaje' => 'Existe mas de un Servicio de Ip en el Punto, <br>'.
                                                       'con tipo de negocio:<b>PRO</b>,<br>'.
                                                       'Favor Notificar a Sistemas!');
                return $respuestaFinal;
            }

            //verificar si la ip del plan esta configurada en el olt
            if($flagProdIpPlanViejo > 0)
            {
                //obtener ip del plan
                $arrayIpPlan = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                    ->findBy(array("servicioId" => $servicio->getId(), "estado" => "Activo"));

                //validar si existe mas de una ip de plan dentro del servicio
                if(count($arrayIpPlan) > 1)
                {
                    $respuestaFinal[] = array('status' => 'ERROR',
                                              'mensaje' => 'Existe mas de una Ip de Plan en el Servicio,<br>'.
                                                           'Favor Notificar a Sistemas!');
                    return $respuestaFinal;
                }

                //asignar a una variable
                $ipPlan = $arrayIpPlan[0];
            }//if($flagProdIpPlanViejo>0)
            //verificar si la ip del servicio adicional esta configurada en el olt
            else if($flagProdAdicional > 0)
            {
                //obtener ip del plan
                $arrayIpPlan = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                    ->findBy(array("servicioId" => $arrayServiciosIpAdicional[0]->getId(), "estado" => "Activo"));

                //validar si existe mas de una ip de plan dentro del servicio
                if(count($arrayIpPlan) > 1)
                {
                    $respuestaFinal[] = array('status' => 'ERROR',
                                              'mensaje' => 'Existe mas de una Ip de Plan en el Servicio,<br>'.
                                                           'Favor Notificar a Sistemas!');
                    return $respuestaFinal;
                }

                //asignar a una variable
                $ipPlan = $arrayIpPlan[0];
            }//else if ($flagProdAdicional>0)
        }//if(($flagProdAdicional > 0 || $flagProdIpPlanViejo > 0) &&
        
        
        //actualizamos el tipo de negocio
        $this->servicioGeneral->setTipoNegocioEnInfoPunto($punto, $planCabNuevo->getTipo(), $idEmpresa);
        if($flagProdIpPlanViejo > 0 || $flagProdAdicional > 0)
        {
            if($flagProdAdicional > 0)
            {
                $servicioPunto  = $arrayServiciosIpAdicional[0];
                $strEsAdicional = "SI";
            }
            else
            {
                $servicioPunto  = $servicio;
                $strEsAdicional = "NO";
            }
            $strEsAdicional = "NO";
            $objIpViejaFijaAdicional = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                            ->findOneBy(array("servicioId" => $servicioPunto->getId(), "tipoIp" => "FIJA", "estado" => "Activo"));
        }

        if(($flagProdIpPlanViejo > 0 && ($planCabNuevo->getTipo() == "PRO" || $planCabNuevo->getTipo() == "HOME")) ||
           ($flagProdAdicional > 0 && $planCabNuevo->getTipo() == "HOME"))
        {
            if ($flagProdAdicional)
            {
                $spcScopeAntes  = $this->servicioGeneral->getServicioProductoCaracteristica($arrayServiciosIpAdicional[0], 
                                                                                            "SCOPE", 
                                                                                            $arrayServiciosIpAdicional[0]->getProductoId());
            }
            $arrParametrosCancel = array(
                'servicioTecnico'   => $servicioTecnico,
                'modeloElemento'    => $modeloElemento,
                'interfaceElemento' => $interfaceElemento,
                'producto'          => $producto,
                'servicio'          => $servicioPunto,
                'spcMac'            => $servProdCaractMacOnt,
                'scope'             => $spcScopeAntes->getValor(),
                'spcIndiceCliente'  => $servProdCaractIndiceCliente,
                'esAdicional'       => $strEsAdicional
            );

            //desconfigurar la ip adicional
            $respuestaArrayAdicional = $this->cancelarService->cancelarServicioIp($arrParametrosCancel);
            $statusAdicional         = $respuestaArrayAdicional[0]['status'];

            if($statusAdicional == "ERROR")
            {
                $arrayFinal[] = array('status' => "ERROR", 'mensaje' => $respuestaArrayAdicional[0]['mensaje']);
                return $arrayFinal;
            }
            $objIpViejaFijaAdicional->setEstado("Cancel");
            $this->emInfraestructura->persist($objIpViejaFijaAdicional);
            $this->emInfraestructura->flush();
            $boolCanceloIp = true;
        }


        //cancelar servicio de internet y/o ip fija
        //si tiene ip dentro del plan
        if($flagProdIpPlanViejo > 0 && $planCabNuevo->getTipo() == "HOME")
        {
            $objIpViejaFijaAdicional = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                            ->findOneBy(array("servicioId"=>$servicio->getId(),"tipoIp"=>"FIJA", "estado"=>"Activo"));
            $arrayParametros = array(
                                        'servicioTecnico'   => $servicioTecnico,
                                        'interfaceElemento' => $interfaceElemento,
                                        'modeloElemento'    => $modeloElemento,
                                        'producto'          => $producto,
                                        'login'             => $login,
                                        'idEmpresa'         => $idEmpresa,
                                        'ipCreacion'        => $ipCreacion,
                                        'usrCreacion'       => $usrCreacion
                                    );
            $respuestaArray = $this->cancelarService->cancelarServicioMdConIp($arrayParametros);
            $status         = $respuestaArray[0]['status'];
            
            //cancelar ip fija de la tabla info_ip
            if($status=="OK")
            {
                //obtener ips fijas q tiene el servicio
                $objIpFija = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                  ->findOneBy(array("servicioId" => $servicioTecnico->getServicioId()->getId(),
                                                    "tipoIp"     => "FIJA",
                                                    "estado"     => "Activo"));
                $objIpFija->setEstado("Cancel");
                $this->emInfraestructura->persist($objIpFija);
                $this->emInfraestructura->flush();
            }
            
            $mensaje = $respuestaArray[0]['mensaje'];
        }//if($flagProdIpPlanViejo > 0)
        //si no tiene ip dentro del plan
        else
        {
            //cancelar servicio sin ip
            $arrayParametros = array(
                                        'servicioTecnico'   => $servicioTecnico,
                                        'interfaceElemento' => $interfaceElemento,
                                        'modeloElemento'    => $modeloElemento,
                                        'spcIndiceCliente'  => $servProdCaractIndiceCliente,
                                        'login'             => $login,
                                        'spcSpid'           => "",
                                        'spcMacOnt'         => "",
                                        'idEmpresa'         => $idEmpresa
                                    );
            $respuestaArray = $this->cancelarService
                                   ->cancelarServicioMdSinIp($arrayParametros);            
            $status  = $respuestaArray[0]['status'];
            $mensaje = $respuestaArray[0]['mensaje'];
            
        }//else

        if($status == "OK")
        {
            //eliminamos el indice anterior
            $this->servicioGeneral->setEstadoServicioProductoCaracteristica($servProdCaractIndiceCliente, "Eliminado");

            //actualizamos el tipo de negocio
            $this->servicioGeneral->setTipoNegocioEnInfoPunto($punto, $planCabNuevo->getTipo(), $idEmpresa);

            switch($planCabNuevo->getTipo())
            {
                //Pro -> Home
                case "HOME":
                    $arrayParametros=array(
                                        'servicioTecnico'   => $servicioTecnico,
                                        'interfaceElemento' => $interfaceElemento,
                                        'modeloElemento'    => $modeloElemento,
                                        'macOnt'            => $macOnt,
                                        'perfil'            => $perfilNuevo,
                                        'login'             => $login,
                                        'ontLineProfile'    => "",
                                        'serviceProfile'    => "",
                                        'serieOnt'          => "",
                                        'vlan'              => "",
                                        'gemPort'           => "",
                                        'trafficTable'      => ""
                                      );
                    
                    $respuestaArray = $this->activarService
                                           ->activarClienteMdSinIp($arrayParametros);
                    $status         = $respuestaArray[0]['status'];
                    $mensaje        = $respuestaArray[0]['mensaje'];

                    if($status == "OK")
                    {
                        //si tiene ip adicional se debe cancelar el servicio adicional
                        if($flagProdAdicional > 0 && $boolCanceloIp == true)
                        {
                            $servicioIpAdicional = $arrayServiciosPorPunto[$indiceIpFija];
                            if($servicioIpAdicional)
                            {
                                $estadoServicioAdicional = "Cancel";
                                $observacionAdicional = "Se cancelo servicio adicional, por cambio de plan";
                                if($servicioIpAdicional->getEstado()=="PreAsignacionInfoTecnica" || 
                                   $servicioIpAdicional->getEstado()=="Asignada")
                                {
                                    $estadoServicioAdicional = "Anulado";
                                    $observacionAdicional = "Se Anulo servicio adicional, por cambio de plan";

                                }                                    
                                //cambiar estado al servicio adicional
                                $servicioIpAdicional->setEstado($estadoServicioAdicional);
                                $this->emComercial->persist($servicioIpAdicional);
                                $this->emComercial->flush();

                                //historial del servicio adicional
                                $servicioHistorial = new InfoServicioHistorial();
                                $servicioHistorial->setServicioId($servicioIpAdicional);
                                $servicioHistorial->setObservacion($observacionAdicional);
                                $servicioHistorial->setEstado($estadoServicioAdicional);
                                $servicioHistorial->setUsrCreacion($usrCreacion);
                                $servicioHistorial->setFeCreacion(new \DateTime('now'));
                                $servicioHistorial->setIpCreacion($ipCreacion);
                                $this->emComercial->persist($servicioHistorial);
                                $this->emComercial->flush();
                            }//if($servicioIpAdicional)
                        }//if($flagProdAdicional > 0)

                        return $respuestaArray;
                    }//if($status == "OK")

                    break;
                //Pro -> Pro
                case "PRO":
                    
                    $arrayParametros=array(
                                    'servicioTecnico'   => $servicioTecnico,
                                    'interfaceElemento' => $interfaceElemento,
                                    'modeloElemento'    => $modeloElemento,
                                    'macOnt'            => $macOnt,
                                    'perfil'            => $perfilNuevo,
                                    'login'             => $login,
                                    'ontLineProfile'    => "",
                                    'serviceProfile'    => "",
                                    'serieOnt'          => "",
                                    'vlan'              => "",
                                    'gemPort'           => "",
                                    'trafficTable'      => ""
                                  );

                    //se activa solo el internet
                    $respuestaArray = $this->activarService
                                           ->activarClienteMdSinIp($arrayParametros);
                    $status         = $respuestaArray[0]['status'];
                    $mensaje        = $respuestaArray[0]['mensaje'];

                    if($status == "OK")
                    {
                        return $respuestaArray;
                    }//end if status = ok
                    
                    break;
                //Pro -> Pyme
                case "PYME":
                    
                    if ($flagProdAdicional == 0 && $flagProdIpPlanViejo == 0)
                    {
                        //solicitar la ip para el nuevo plan
                        $arregloIps = $this->recursosRed
                                           ->getIpsDisponibleScopeOlt(1, $servicioTecnico->getElementoId(), $servicio->getId(), 
                                                                      $servicio->getPuntoId()->getId(), "SI", $planCabNuevo->getId());
                        if($arregloIps['error'])
                        {
                            $status = "ERROR";
                            $mensaje = $arregloIps['error'];
                            break;
                        }

                        //grabar ip
                        $arrayIps = $arregloIps['ips'];
                        //grabar ip en estado Reservada
                        $ipPyme   = $this->servicioGeneral
                                         ->reservarIpAdicional($arrayIps[0]['ip'], $arrayIps[0]['tipo'], $servicio, $usrCreacion, $ipCreacion);
                    
                        $arrayParametros=array(
                                            'servicio'          => $servicio,
                                            'servicioTecnico'   => $servicioTecnico,
                                            'interfaceElemento' => $interfaceElemento,
                                            'modeloElemento'    => $modeloElemento,
                                            'producto'          => $producto,
                                            'macOnt'            => $macOnt,
                                            'macWifi'           => $servProdCaracMacWifi->getValor(),
                                            'perfil'            => $perfilNuevo,
                                            'login'             => $login,
                                            'usrCreacion'       => $usrCreacion
                                          );

                        //activar el plan nuevo
                        $respuestaArray = $this->activarService
                                               ->activarClienteMdConIp($arrayParametros);
                        $status         = $respuestaArray[0]['status'];
                        $mensaje        = $respuestaArray[0]['mensaje'];
                    }
                    else
                    {
                        
                        $arrayParametros=array(
                                        'servicioTecnico'   => $servicioTecnico,
                                        'interfaceElemento' => $interfaceElemento,
                                        'modeloElemento'    => $modeloElemento,
                                        'macOnt'            => $macOnt,
                                        'perfil'            => $perfilNuevo,
                                        'login'             => $login,
                                        'ontLineProfile'    => "",
                                        'serviceProfile'    => "",
                                        'serieOnt'          => "",
                                        'vlan'              => "",
                                        'gemPort'           => "",
                                        'trafficTable'      => ""
                                      );

                        //se activa solo el internet
                        $respuestaArray = $this->activarService
                                               ->activarClienteMdSinIp($arrayParametros);
                        $status         = $respuestaArray[0]['status'];
                        $mensaje        = $respuestaArray[0]['mensaje'];
                        if ($status == "OK")
                        {
                            if ($flagProdAdicional > 0)
                            {
                                //scope
                                $spcScopeIpAdicional  = $this->servicioGeneral
                                                             ->getServicioProductoCaracteristica($arrayServiciosIpAdicional[0], 
                                                                                                 "SCOPE", 
                                                                                                 $arrayServiciosIpAdicional[0]->getProductoId());

                                //se agregan nuevas caracteristicas
                                $this->servicioGeneral->ingresarServicioProductoCaracteristica($servicio, 
                                                                                               $productoIp, 
                                                                                               "SCOPE", 
                                                                                               $spcScopeIpAdicional->getValor(), 
                                                                                               $usrCreacion);

                            }
                            //modificar ip
                            $objIpViejaFijaAdicional->setServicioId($servicio->getId());
                            $this->emInfraestructura->persist($ipPlan);
                            $this->emInfraestructura->flush();
                        }
                        
                    }

                    if($status == "OK")
                    {
                        if ($ipPyme)
                        {
                            //cambiar estado (activar) de la ip del plan
                            $ipPyme->setEstado("Activo");
                            $this->emInfraestructura->persist($ipPyme);
                            $this->emInfraestructura->flush();
                        }
                        
                        //si tiene ip adicional se debe cancelar el servicio adicional
                        if($flagProdAdicional > 0)
                        {
                            $servicioIpAdicional = $arrayServiciosPorPunto[$indiceIpFija];
                            if($servicioIpAdicional)
                            {
                                $estadoServicioAdicional = "Cancel";
                                $observacionAdicional = "Se cancelo servicio adicional, por cambio de plan";
                                if($servicioIpAdicional->getEstado()=="PreAsignacionInfoTecnica" || 
                                   $servicioIpAdicional->getEstado()=="Asignada")
                                {
                                    $estadoServicioAdicional = "Anulado";
                                    $observacionAdicional = "Se Anulo servicio adicional, por cambio de plan";

                                }                                    
                                //cambiar estado al servicio adicional
                                $servicioIpAdicional->setEstado($estadoServicioAdicional);
                                $this->emComercial->persist($servicioIpAdicional);
                                $this->emComercial->flush();

                                //historial del servicio adicional
                                $servicioHistorial = new InfoServicioHistorial();
                                $servicioHistorial->setServicioId($servicioIpAdicional);
                                $servicioHistorial->setObservacion($observacionAdicional);
                                $servicioHistorial->setEstado($estadoServicioAdicional);
                                $servicioHistorial->setUsrCreacion($usrCreacion);
                                $servicioHistorial->setFeCreacion(new \DateTime('now'));
                                $servicioHistorial->setIpCreacion($ipCreacion);
                                $this->emComercial->persist($servicioHistorial);
                                $this->emComercial->flush();
                            }//if($servicioIpAdicional)
                        }//if($flagProdAdicional > 0)
                        
                        return $respuestaArray;
                    }//if($status == "OK")
                    else
                    {
                        if ($ipPyme)
                        {
                            //eliminar ip nueva
                            $ipPyme->setEstado("Eliminado");
                            $this->emInfraestructura->persist($ipPyme);
                            $this->emInfraestructura->flush();
                        }
                    }

                    break;
            }//end switch
        }//if($status == "OK")
        else
        {
            //reversar cambio de tipo de negocio
            $this->servicioGeneral->setTipoNegocioEnInfoPunto($punto, $planCabViejo->getTipo(), $idEmpresa);
            
            $arrayFinal[] = array('status' => "ERROR",
                                  'mensaje' => "No se puede realizar el cambio de Plan, <br>"
                                              ."Fallo la cancelacion del plan anterior:<b>" . $planCabViejo->getNombrePlan() . "</b><br>"
                                              ."Mensaje:" . $mensaje);
            return $arrayFinal;
        }//else

        //si falla la activacion del nuevo plan
        if($status != "OK")
        {
            //reversar cambio de tipo de negocio
            $this->servicioGeneral->setTipoNegocioEnInfoPunto($punto, $planCabViejo->getTipo(), $idEmpresa);
                    
            //rollback de scripts
            if($flagProdIpPlanViejo > 0)
            {
                //reservamos ip vieja del plan, para poderla activar
                $objIpViejaFijaAdicional->setEstado("Reservada");
                $this->emInfraestructura->persist($objIpViejaFijaAdicional);
                $this->emInfraestructura->flush();
                
                $arrayParametros=array(
                                        'servicio'          => $servicio,
                                        'servicioTecnico'   => $servicioTecnico,
                                        'interfaceElemento' => $interfaceElemento,
                                        'modeloElemento'    => $modeloElemento,
                                        'producto'          => $producto,
                                        'macOnt'            => $macOnt,
                                        'macWifi'           => $servProdCaracMacWifi->getValor(),
                                        'perfil'            => $servProdCaracPerfil->getValor(),
                                        'login'             => $login,
                                        'usrCreacion'       => $usrCreacion
                                      );
                
                $respuestaArray = $this->activarService
                                       ->activarClienteMdConIp($arrayParametros);

                $mensajeActivarPlanAnterior = $respuestaArray[0]['mensaje'];
                $indiceNuevoAnterior        = $mensajeActivarPlanAnterior;
                
                if($respuestaArray[0]['status']!="OK")
                {
                    $mensaje = $mensaje . "<br>Activacion Plan Anterior:" . $mensajeActivarPlanAnterior;
                }//if($respuestaArray[0]['status']!="OK")

                //grabamos el nuevo indice del plan anterior
                $this->servicioGeneral
                     ->ingresarServicioProductoCaracteristica($servicio, $producto, "INDICE CLIENTE", $indiceNuevoAnterior, $usrCreacion);
            }//if($flagProdIpPlanViejo > 0)
            else
            {
                $arrayParametros=array(
                                        'servicioTecnico'   => $servicioTecnico,
                                        'interfaceElemento' => $interfaceElemento,
                                        'modeloElemento'    => $modeloElemento,
                                        'macOnt'            => $macOnt,
                                        'perfil'            => $servProdCaracPerfil->getValor(),
                                        'login'             => $login,
                                        'ontLineProfile'    => "",
                                        'serviceProfile'    => "",
                                        'serieOnt'          => "",
                                        'vlan'              => "",
                                        'gemPort'           => "",
                                        'trafficTable'      => ""
                                      );
                
                $respuestaArray = $this->activarService
                                       ->activarClienteMdSinIp($arrayParametros);

                $mensajeActivarPlanAnterior = $respuestaArray[0]['mensaje'];
                $indiceNuevoAnterior        = $mensajeActivarPlanAnterior;

                //grabamos el nuevo indice del plan anterior
                $this->servicioGeneral
                     ->ingresarServicioProductoCaracteristica($servicio, $producto, "INDICE CLIENTE", $indiceNuevoAnterior, $usrCreacion);

                if($flagProdAdicional > 0)
                {
                    //activamos el pool anterior
                    if($servProdCaracPoolAdicional)
                    {
                        $this->servicioGeneral->setEstadoServicioProductoCaracteristica($servProdCaracPoolAdicional, "Activo");
                    }//if($servProdCaracPoolAdicional)

                    //activamos el perfil anterior
                    if($servProdCaractPerfilAdicional)
                    {
                        $this->servicioGeneral->setEstadoServicioProductoCaracteristica($servProdCaractPerfilAdicional, "Activo");
                    }//if($servProdCaractPerfilAdicional)
                    
                    //reservamos ip vieja adicional, para poderla activar
                    $objIpViejaFijaAdicional->setEstado("Reservada");
                    $this->emInfraestructura->persist($objIpViejaFijaAdicional);
                    $this->emInfraestructura->flush();
                    
                    $servicioIpAdicional = $arrayServiciosPorPunto[$indiceIpFija];
                    $arrayRespuestaIp    = $this->activarService
                                                ->activarServicioIp($servicioIpAdicional, 
                                                                    $servicioTecnico, 
                                                                    $producto, 
                                                                    $interfaceElemento, 
                                                                    $modeloElemento);
                    
                    if($arrayRespuestaIp[0]['status']!="OK")
                    {
                        $mensaje = $mensaje . "<br>Activacion Ip Anterior:" . $arrayRespuestaIp[0]['mensaje'];
                    }
                }//if($flagProdAdicional > 0)
                
                if($respuestaArray[0]['status']!="OK")
                {
                    $mensaje = $mensaje . "<br>Activacion Plan Anterior:" . $mensajeActivarPlanAnterior;
                }//if($respuestaArray[0]['status']!="OK")
            }//else

            $arrayFinal[] = array('status'  => "ERROR",
                                  'mensaje' => "No se puede realizar el cambio de Plan, <br>"
                                              ."Fallo activacion del plan nuevo:<b>" . $planCabNuevo->getNombrePlan() . "</b><br>"
                                              ."Mensaje:" . $mensaje);
            return $arrayFinal;
        }//if($status != "OK")
    }
    
    /**
     * Funcion que realiza el cambio de plan de clientes en equipos HUAWEI de:
     * - Pro -> Home
     * - Pro -> Pro
     * - Pro -> Pyme
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 20-05-2015
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 09-12-2015 Se agrega recuperacion de producto IP para servicios adicionales de Ip que pertenecen a un Plan
     * 
     * @param $arrayPeticiones  arreglo con las variables necesarias para realizar el cambio de plan
     */
    public function cambioPlanProHuawei($arrayPeticiones)
    {
        $punto                          = $arrayPeticiones[0]['punto'];
        $servicio                       = $arrayPeticiones[0]['servicio'];
        $servicioTecnico                = $arrayPeticiones[0]['servicioTecnico'];
        $planCabViejo                   = $arrayPeticiones[0]['planCabViejo'];
        $planDetViejo                   = $arrayPeticiones[0]['planDetViejo'];
        $interfaceElemento              = $arrayPeticiones[0]['interfaceElemento'];
        $modeloElemento                 = $arrayPeticiones[0]['modeloElemento'];
        $planCabNuevo                   = $arrayPeticiones[0]['planCabNuevo'];
        $planDetNuevo                   = $arrayPeticiones[0]['planDetNuevo'];
        $producto                       = $arrayPeticiones[0]['producto'];
        $productoIp                     = $arrayPeticiones[0]['productoIp'];
        $arrayProdIp                    = $arrayPeticiones[0]['arrayProdIp'];
        $macOnt                         = $arrayPeticiones[0]['macOnt'];
        $planCaractEdicionLimitada      = $arrayPeticiones[0]['planCaractEdicionLimitada'];
        $servProdCaractIndiceCliente    = $arrayPeticiones[0]['servProdCaractIndiceCliente'];
        $servProdCaractMacOnt           = $arrayPeticiones[0]['servProdCaracMacOnt'];
        $idEmpresa                      = $arrayPeticiones[0]['idEmpresa'];
        $usrCreacion                    = $arrayPeticiones[0]['usrCreacion'];
        $ipCreacion                     = $arrayPeticiones[0]['ipCreacion'];
        $spcSpid                        = $arrayPeticiones[0]['spid'];
        $spcServiceProfile              = $arrayPeticiones[0]['serviceProfile'];
        $spcLineProfile                 = $arrayPeticiones[0]['lineProfile'];
        $spcVlan                        = $arrayPeticiones[0]['vlan'];
        $spcGemPort                     = $arrayPeticiones[0]['gemPort'];
        $spcTrafficTable                = $arrayPeticiones[0]['trafficTable'];
        $spcLineProfileAntes            = $arrayPeticiones[0]['lineProfileAntes'];
        $spcVlanAntes                   = $arrayPeticiones[0]['vlanAntes'];
        $spcGemPortAntes                = $arrayPeticiones[0]['gemPortAntes'];
        $spcTrafficTableAntes           = $arrayPeticiones[0]['trafficTableAntes'];
        $spcScopeAntes                  = $arrayPeticiones[0]['scopeAntes'];
        $flagProdIpPlanNuevo            = 0;
        $flagProdIpPlanViejo            = 0;
        $flagProdAdicional              = 0;
        $ipFija                         = "";
        $ipPlan                         = null;
        $objIpViejaFijaAdicional        = null;
        $servicioPunto                  = null;
        $boolCanceloIp                  = false;
        $strEsAdicional                 = null;

        $elemento = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($servicioTecnico->getElementoId());

        //servicios adicionales
        $arrayServiciosPorPunto = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                       ->findBy(array("puntoId" => $servicio->getPuntoId()->getId()));

        //obtener objeto modelo cnr
        $modeloElementoCnr = $this->emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                  ->findOneBy(array("nombreModeloElemento" => "CNR UCS C220",
                                                    "estado" => "Activo"));

        //obtener elemento cnr
        $elementoCnr = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                            ->findOneBy(array("modeloElementoId" => $modeloElementoCnr->getId()));
        $scriptArray = $this->servicioGeneral->obtenerArregloScript("configurarIpFija", $modeloElementoCnr);
        $idDocumentoConfig = $scriptArray[0]->idDocumento;
        $usuarioConfig     = $scriptArray[0]->usuario;
        //*----------------------------------------------------------------------*/
        //verificar que no se quiera cambiar al plan 100mb
        if($planCaractEdicionLimitada)
        {
            if($planCaractEdicionLimitada->getValor() == "SI" && $planCabNuevo->getTipo() == "HOME")
            {
                $arrayFinal[] = array('status' => "ERROR",
                                      'mensaje' => "No se puede realizar el cambio de Plan, <br>".
                                                   "<b>Exclusivo para planes Home</b>");
                return $arrayFinal;
            }//if($planCaractEdicionLimitada->getValor() == "SI" && $planCabNuevo->getTipo() == "HOME")
        }//if($planCaractEdicionLimitada)
        else
        {
            $planCaractEdicionLimitada = false;
        }

        //verificar si plan nuevo tiene ip
        $flagProdIpPlanNuevo = $this->servicioGeneral->verificarPlanTieneIp($planDetNuevo, $arrayProdIp);

        //verificar si plan viejo tiene ip
        $flagProdIpPlanViejo = $this->servicioGeneral->verificarPlanTieneIp($planDetViejo, $arrayProdIp);

        //verificar si servicio tiene ip adicional
        $flagProdAdicional = $this->servicioGeneral->verificarIpFijaEnPunto($arrayServiciosPorPunto, $arrayProdIp, $servicio);

        //si tiene producto ip en el plan y producto ip adicional, error
        if($flagProdIpPlanViejo > 0 && $flagProdAdicional > 0)
        {
            $respuestaFinal[] = array('status' => 'ERROR',
                                      'mensaje' => 'Tipo de Negocio PRO, no debe tener mas de una IP FIJA en estado Activo, <br>'.
                                                   'Favor Regularizar!');
            return $respuestaFinal;
        }

        //si no es de edicion limitada, y tiene ip (en el plan nuevo) y se pasa a un home, error
        if(!$planCaractEdicionLimitada && ($flagProdIpPlanNuevo > 0 ) && $planCabNuevo->getTipo() == "HOME")
        {
            $respuestaFinal[] = array('status' => 'ERROR',
                                      'mensaje' => 'El Plan: <b>' . $planCabNuevo->getNombrePlan() . '</b>, <br>'.
                                                   'Tiene producto Ip definido y es de tipo HOME, <br>'.
                                                   'Por lo tanto el cambio de plan NO es permitido <br>'.
                                                   'Favor Notificar al Dep. Marketing!');
            return $respuestaFinal;
        }

        //validar que el plan pyme tenga producto ip definido en el plan
        if($planCabNuevo->getTipo() == "PYME" && $flagProdIpPlanNuevo <= 0)
        {
            $respuestaFinal[] = array('status' => 'ERROR',
                                      'mensaje' => 'El Plan: <b>' . $planCabNuevo->getNombrePlan() . '</b>, <br>'.
                                                   'No tiene producto Ip definido, Favor Notificar al Dep. Marketing!');
            return $respuestaFinal;
        }//if($planCabNuevo->getTipo() == "PYME" && $flagProdIpPlanNuevo<=0)

        /* si tiene ip adicional o dentro del plan pro, se debe validar que la mac wifi que tiene el servicio
         * este conectada en el olt antes de ejecutar la cancelacion del servicio */
        if(($flagProdAdicional > 0 || $flagProdIpPlanViejo > 0))
        {
            //obtener unicamente los servicios adicionales de ip fija
            $arrayServiciosIpAdicional = $this->servicioGeneral
                                              ->getServiciosIpAdicionalEnPunto($arrayServiciosPorPunto, $arrayProdIp, $servicio);

            if(count($arrayServiciosIpAdicional) > 1)
            {
                $respuestaFinal[] = array('status' => 'ERROR',
                                          'mensaje' => 'Existe mas de un Servicio de Ip en el Punto, <br>'.
                                                       'con tipo de negocio:<b>PRO</b>,<br>'.
                                                       'Favor Notificar a Sistemas!');
                return $respuestaFinal;
            }

            //verificar si la ip del plan esta configurada en el olt
            if($flagProdIpPlanViejo > 0)
            {
                //obtener ip del plan
                $arrayIpPlan = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                    ->findBy(array("servicioId" => $servicio->getId(), "estado" => "Activo"));

                //validar si existe mas de una ip de plan dentro del servicio
                if(count($arrayIpPlan) > 1)
                {
                    $respuestaFinal[] = array('status' => 'ERROR',
                                              'mensaje' => 'Existe mas de una Ip de Plan en el Servicio,<br>'.
                                                           'Favor Notificar a Sistemas!');
                    return $respuestaFinal;
                }

                //asignar a una variable
                $ipPlan = $arrayIpPlan[0];
            }//if($flagProdIpPlanViejo>0)
            //verificar si la ip del servicio adicional esta configurada en el olt
            else if($flagProdAdicional > 0)
            {
                //obtener ip del plan
                $arrayIpPlan = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                    ->findBy(array("servicioId" => $arrayServiciosIpAdicional[0]->getId(), "estado" => "Activo"));

                //validar si existe mas de una ip de plan dentro del servicio
                if(count($arrayIpPlan) > 1)
                {
                    $respuestaFinal[] = array('status' => 'ERROR',
                                              'mensaje' => 'Existe mas de una Ip de Plan en el Servicio,<br>'.
                                                           'Favor Notificar a Sistemas!');
                    return $respuestaFinal;
                }

                //asignar a una variable
                $ipPlan = $arrayIpPlan[0];
            }//else if ($flagProdAdicional>0)
        }//if(($flagProdAdicional > 0 || $flagProdIpPlanViejo > 0) &&
        //($planCabNuevo->getTipo() == "HOME" || $planCabNuevo->getTipo() == "PRO" || $planCabNuevo->getTipo() == "PYME"))
        //actualizamos el tipo de negocio
        $this->servicioGeneral->setTipoNegocioEnInfoPunto($punto, $planCabNuevo->getTipo(), $idEmpresa);
        if($flagProdIpPlanViejo > 0 || $flagProdAdicional > 0)
        {
            $entityProductoIpAdi = null;
            if($flagProdAdicional > 0)
            {
                $servicioPunto = $arrayServiciosIpAdicional[0];
                $strEsAdicional = "SI";
                
                //se valida si el servicio adicional de Ip es un plan
                if ($servicioPunto->getPlanId())
                {
                    // se recupera detalle del plan del servicio adicional de IP
                    $planDetIpAdi = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                         ->findBy(array("planId" => $servicioPunto->getPlanId()));

                    $indiceProductoIpAdi = $this->servicioGeneral->obtenerIndiceInternetEnPlanDet($planDetIpAdi, $arrayProdIp);


                    if ($indiceProductoIpAdi != -1)
                    {
                        // se recupera entidad Producto IP del servicio Adicional
                        $entityProductoIpAdi = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                    ->find($planDetIpAdi[$indiceProductoIpAdi]->getProductoId());
                    }
                    else
                    {
                        $entityProductoIpAdi = null;
                        $respuestaFinal[] = array('status'  => 'ERROR',
                                                  'mensaje' => 'Existe un servicio de Ip adicional Incorrecto,<br>'.
                                                               'Favor Notificar a Sistemas!');
                        return $respuestaFinal;
                    }
                }
                else
                //se recupera el producto de servicio adicional de Ip
                {
                    $entityProductoIpAdi = $servicioPunto->getProductoId();
                }
            }
            else
            {
                $servicioPunto = $servicio;
                $strEsAdicional = "NO";
            }
            $strEsAdicional = "NO";
            $objIpViejaFijaAdicional = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                            ->findOneBy(array("servicioId" => $servicioPunto->getId(), "tipoIp" => "FIJA", "estado" => "Activo"));
        }

        if(($flagProdIpPlanViejo > 0 || $flagProdAdicional > 0) &&
            !($flagProdIpPlanViejo > 0 && $flagProdIpPlanNuevo > 0) &&
            ($planCabViejo->getTipo() != "HOME") &&
           !($flagProdAdicional > 0 && $planCabNuevo->getTipo() != "HOME")
        )
        {
            if ($flagProdAdicional)
            {
                $spcScopeAntes  = $this->servicioGeneral->getServicioProductoCaracteristica($arrayServiciosIpAdicional[0], 
                                                                                            "SCOPE", 
                                                                                            $entityProductoIpAdi);
            }
            $arrParametrosCancel = array(
                'servicioTecnico'   => $servicioTecnico,
                'modeloElemento'    => $modeloElemento,
                'interfaceElemento' => $interfaceElemento,
                'producto'          => $producto,
                'servicio'          => $servicioPunto,
                'spcMac'            => $servProdCaractMacOnt,
                'scope'             => $spcScopeAntes->getValor(),
                'spcIndiceCliente'  => $servProdCaractIndiceCliente,
                'esAdicional'       => $strEsAdicional
            );

            //desconfigurar la ip adicional
            $respuestaArrayAdicional = $this->cancelarService->cancelarServicioIp($arrParametrosCancel);
            $statusAdicional         = $respuestaArrayAdicional[0]['status'];

            if($statusAdicional == "ERROR")
            {
                $arrayFinal[] = array('status' => "ERROR", 'mensaje' => $respuestaArrayAdicional[0]['mensaje']);
                return $arrayFinal;
            }
            $objIpViejaFijaAdicional->setEstado("Cancel");
            $this->emInfraestructura->persist($objIpViejaFijaAdicional);
            $this->emInfraestructura->flush();
            $boolCanceloIp = true;
        }

        switch($planCabNuevo->getTipo())
        {
            //Pro -> Home
            case "HOME":
                $arrParamReconectar = array(
                    'elemento'          => $elemento,
                    'interfaceElemento' => $interfaceElemento,
                    'spcIndiceCliente'  => $servProdCaractIndiceCliente,
                    'spcSpid'           => $spcSpid,
                    'servicioTecnico'   => $servicioTecnico,
                    'spcServiceProfile' => $spcServiceProfile->getValor(),
                    'spcLineProfile'    => $spcLineProfile,
                    'spcVlan'           => $spcVlan,
                    'spcGemPort'        => $spcGemPort,
                    'spcTrafficTable'   => $spcTrafficTable
                );

                $resultadJson = $this->reconectarService->reconectarServicioOltHuawei($arrParamReconectar);

                $respuestaFinal[0]['status']  = $resultadJson->status;
                $respuestaFinal[0]['mensaje'] = $resultadJson->mensaje;
                $status  = $respuestaFinal[0]['status'];
                $mensaje = $respuestaFinal[0]['mensaje'];

                if($status == "OK")
                {
                    //si tiene ip adicional se debe cancelar el servicio adicional
                    if($flagProdAdicional > 0)
                    {
                        $servicioIpAdicional = $servicioPunto;
                        if($servicioIpAdicional)
                        {
                            $estadoServicioAdicional = "Cancel";
                            $observacionAdicional    = "Se cancelo servicio adicional, por cambio de plan";
                            if($servicioIpAdicional->getEstado() == "PreAsignacionInfoTecnica" ||
                                $servicioIpAdicional->getEstado() == "Asignada")
                            {
                                $estadoServicioAdicional = "Anulado";
                                $observacionAdicional    = "Se Anulo servicio adicional, por cambio de plan";
                            }
                            //cambiar estado al servicio adicional
                            $servicioIpAdicional->setEstado($estadoServicioAdicional);
                            $this->emComercial->persist($servicioIpAdicional);
                            $this->emComercial->flush();

                            //historial del servicio adicional
                            $servicioHistorial = new InfoServicioHistorial();
                            $servicioHistorial->setServicioId($servicioIpAdicional);
                            $servicioHistorial->setObservacion($observacionAdicional);
                            $servicioHistorial->setEstado($estadoServicioAdicional);
                            $servicioHistorial->setUsrCreacion($usrCreacion);
                            $servicioHistorial->setFeCreacion(new \DateTime('now'));
                            $servicioHistorial->setIpCreacion($ipCreacion);
                            $this->emComercial->persist($servicioHistorial);
                            $this->emComercial->flush();
                        }//if($servicioIpAdicional)
                    }//if($flagProdAdicional > 0)

                    return $respuestaFinal;
                }//if($status == "OK")

                break;
            //Pro -> Pro
            case "PRO":

                $arrParamReconectar = array(
                    'elemento'          => $elemento,
                    'interfaceElemento' => $interfaceElemento,
                    'spcIndiceCliente'  => $servProdCaractIndiceCliente,
                    'spcSpid'           => $spcSpid,
                    'servicioTecnico'   => $servicioTecnico,
                    'spcServiceProfile' => $spcServiceProfile->getValor(),
                    'spcLineProfile'    => $spcLineProfile,
                    'spcVlan'           => $spcVlan,
                    'spcGemPort'        => $spcGemPort,
                    'spcTrafficTable'   => $spcTrafficTable
                );

                $resultadJson = $this->reconectarService->reconectarServicioOltHuawei($arrParamReconectar);

                $respuestaFinal[0]['status']  = $resultadJson->status;
                $respuestaFinal[0]['mensaje'] = $resultadJson->mensaje;
                $status  = $respuestaFinal[0]['status'];
                $mensaje = $respuestaFinal[0]['mensaje'];

                if($status == "OK")
                {
                    if($flagProdIpPlanNuevo > 0 && $flagProdIpPlanViejo == 0)
                    {
                        //plan nuevo tiene ips
                        $arregloIps = $this->recursosRed
                                           ->getIpsDisponibleScopeOlt(1, 
                                                                     $servicioTecnico->getElementoId(), 
                                                                     $servicio->getId(), 
                                                                     $servicio->getPuntoId()->getId(), 
                                                                     "SI", 
                                                                     $planCabNuevo->getId());

                        if($arregloIps['error'])
                        {
                            $status  = "ERROR";
                            $mensaje = $arregloIps['error'];
                            break;
                        }

                        //grabar ip
                        $arrayIps = $arregloIps['ips'];

                        //grabar la ip en estado Reservada
                        $ipFijaNueva = $this->servicioGeneral
                                            ->reservarIpAdicional($arrayIps[0]['ip'], $arrayIps[0]['tipo'], $servicio, $usrCreacion, $ipCreacion);

                        $strMacModificada = $this->activarService->cambiarMac($macOnt);
                        //activar ip fija
                        $arrayParametrosIpFija = array(
                            'ipFija'        => $arrayIps[0]['ip'],
                            'macOnt'        => $strMacModificada,
                            'idDocumento'   => $idDocumentoConfig,
                            'usuario'       => $usuarioConfig,
                            'elementoCnr'   => $elementoCnr
                        );
                        $resultadJsonIpFija = $this->activarService->configurarIpFijaHuawei($arrayParametrosIpFija);
                        $statusIpFija       = $resultadJsonIpFija->status;
                        $mensajeIpFija      = $resultadJsonIpFija->mensaje;
                        $respuestaFinal[0]['status']  = $statusIpFija;
                        $respuestaFinal[0]['mensaje'] = $mensajeIpFija;
                        if($statusIpFija != "OK")
                        {
                            $ipFijaNueva->setEstado("Eliminado");
                            $this->emInfraestructura->persist($ipFijaNueva);
                            $this->emInfraestructura->flush();
                            return $respuestaFinal;
                        }
                        //se activa ip en la base
                        $ipFijaNueva->setEstado("Activo");
                        $this->emInfraestructura->persist($ipFijaNueva);
                        $this->emInfraestructura->flush();
                        $this->servicioGeneral->ingresarServicioProductoCaracteristica($servicio, 
                                                                                       $productoIp, 
                                                                                       "SCOPE", 
                                                                                       $arrayIps[0]['scope'], 
                                                                                       $usrCreacion);
                    }
                    //si tiene ip adicional se debe cancelar el servicio adicional

                    return $respuestaFinal;
                }//if($status == "OK")
                break;
            //Pro -> Pyme
            case "PYME":
                if($flagProdIpPlanNuevo == 1)
                {
                    $arrParamReconectar = array(
                        'elemento'          => $elemento,
                        'interfaceElemento' => $interfaceElemento,
                        'spcIndiceCliente'  => $servProdCaractIndiceCliente,
                        'spcSpid'           => $spcSpid,
                        'servicioTecnico'   => $servicioTecnico,
                        'spcServiceProfile' => $spcServiceProfile->getValor(),
                        'spcLineProfile'    => $spcLineProfile,
                        'spcVlan'           => $spcVlan,
                        'spcGemPort'        => $spcGemPort,
                        'spcTrafficTable'   => $spcTrafficTable
                    );

                    $resultadJson = $this->reconectarService->reconectarServicioOltHuawei($arrParamReconectar);

                    $respuestaFinal[0]['status']  = $resultadJson->status;
                    $respuestaFinal[0]['mensaje'] = $resultadJson->mensaje;
                    $status  = $respuestaFinal[0]['status'];
                    $mensaje = $respuestaFinal[0]['mensaje'];

                    if($status == "OK")
                    {
                        if($flagProdIpPlanViejo == 0)
                        {
                            if ($flagProdAdicional == 0)
                            {
                                //plan nuevo tiene ips
                                $arregloIps = $this->recursosRed
                                                   ->getIpsDisponibleScopeOlt(1, 
                                                                              $servicioTecnico->getElementoId(), 
                                                                              $servicio->getId(), 
                                                                              $servicio->getPuntoId()->getId(), 
                                                                              "SI", 
                                                                              $planCabNuevo->getId());


                                if($arregloIps['error'])
                                {
                                    $status  = "ERROR";
                                    $mensaje = $arregloIps['error'];
                                    break;
                                }

                                //grabar ip
                                $arrayIps = $arregloIps['ips'];

                                //grabar la ip en estado Reservada
                                $ipFijaNueva = $this->servicioGeneral
                                                    ->reservarIpAdicional($arrayIps[0]['ip'], 
                                                                          $arrayIps[0]['tipo'], 
                                                                          $servicio, 
                                                                          $usrCreacion, 
                                                                          $ipCreacion);

                                $strMacModificada = $this->activarService->cambiarMac($macOnt);

                                //activar ip fija
                                $arrayParametrosIpFija = array(
                                    'ipFija'        => $arrayIps[0]['ip'],
                                    'macOnt'        => $strMacModificada,
                                    'idDocumento'   => $idDocumentoConfig,
                                    'usuario'       => $usuarioConfig,
                                    'elementoCnr'   => $elementoCnr
                                );
                                $resultadJsonIpFija = $this->activarService->configurarIpFijaHuawei($arrayParametrosIpFija);
                                $statusIpFija       = $resultadJsonIpFija->status;
                                $mensajeIpFija      = $resultadJsonIpFija->mensaje;
                                $respuestaFinal[0]['status']  = $statusIpFija;
                                $respuestaFinal[0]['mensaje'] = $mensajeIpFija;
                                if($statusIpFija != "OK")
                                {
                                    $ipFijaNueva->setEstado("Eliminado");
                                    $this->emInfraestructura->persist($ipFijaNueva);
                                    $this->emInfraestructura->flush();
                                    return $respuestaFinal;
                                }
                                //se activa ip en la base
                                $ipFijaNueva->setEstado("Activo");
                                $this->emInfraestructura->persist($ipFijaNueva);
                                $this->emInfraestructura->flush();
                                $this->servicioGeneral->ingresarServicioProductoCaracteristica($servicio, 
                                                                                               $productoIp, 
                                                                                               "SCOPE", 
                                                                                               $arrayIps[0]['scope'], 
                                                                                               $usrCreacion);
                            }
                            else
                            {
                                //scope
                                $spcScopeIpAdicional  = $this->servicioGeneral
                                                             ->getServicioProductoCaracteristica($arrayServiciosIpAdicional[0], 
                                                                                                 "SCOPE", 
                                                                                                 $entityProductoIpAdi);
                                //se valida que existe SCOPE de ip adicional
                                if(!$spcScopeIpAdicional)
                                {
                                    $status  = "ERROR";
                                    $mensaje = "No existe registro SCOPE de Ip adicional";
                                    break;
                                }
                                
                                //se agregan nuevas caracteristicas
                                $this->servicioGeneral->ingresarServicioProductoCaracteristica($servicio, 
                                                                                               $productoIp, 
                                                                                               "SCOPE", 
                                                                                               $spcScopeIpAdicional->getValor(), 
                                                                                               $usrCreacion);
                                
                                
                                //modificar ip
                                $ipPlan->setServicioId($servicio->getId());
                                $this->emInfraestructura->persist($ipPlan);
                                $this->emInfraestructura->flush();
                            }
                        }
                        //si tiene ip adicional se debe cancelar el servicio adicional
                        if($flagProdAdicional > 0)
                        {
                            $servicioIpAdicional = $servicioPunto;
                            if($servicioIpAdicional)
                            {
                                $estadoServicioAdicional = "Cancel";
                                $observacionAdicional    = "Se cancelo servicio adicional, por cambio de plan";
                                if($servicioIpAdicional->getEstado() == "PreAsignacionInfoTecnica" ||
                                    $servicioIpAdicional->getEstado() == "Asignada")
                                {
                                    $estadoServicioAdicional = "Anulado";
                                    $observacionAdicional    = "Se Anulo servicio adicional, por cambio de plan";
                                }
                                //cambiar estado al servicio adicional
                                $servicioIpAdicional->setEstado($estadoServicioAdicional);
                                $this->emComercial->persist($servicioIpAdicional);
                                $this->emComercial->flush();

                                //historial del servicio adicional
                                $servicioHistorial = new InfoServicioHistorial();
                                $servicioHistorial->setServicioId($servicioIpAdicional);
                                $servicioHistorial->setObservacion($observacionAdicional);
                                $servicioHistorial->setEstado($estadoServicioAdicional);
                                $servicioHistorial->setUsrCreacion($usrCreacion);
                                $servicioHistorial->setFeCreacion(new \DateTime('now'));
                                $servicioHistorial->setIpCreacion($ipCreacion);
                                $this->emComercial->persist($servicioHistorial);
                                $this->emComercial->flush();
                            }//if($servicioIpAdicional)
                        }//if($flagProdAdicional > 0)

                        return $respuestaFinal;
                    }//if($status == "OK")
                }
                else
                {
                    $status = "ERROR";
                    $mensaje = 'El nuevo plan: <b>' . $planCabNuevo->getNombrePlan() . '</b>, <br>'.
                               'No tiene definido el producto IP FIJA, <br> '.
                               'Favor Notificar al Dept. Marketing para que regularice el Plan!';
                }
                break;
        }//end switch
        //si falla la activacion del nuevo plan
        if($status != "OK")
        {
            //reversar cambio de tipo de negocio
            $this->servicioGeneral->setTipoNegocioEnInfoPunto($punto, $planCabViejo->getTipo(), $idEmpresa);

            $arrParamReconectar = array(
                'elemento'          => $elemento,
                'interfaceElemento' => $interfaceElemento,
                'spcIndiceCliente'  => $servProdCaractIndiceCliente,
                'spcSpid'           => $spcSpid,
                'servicioTecnico'   => $servicioTecnico,
                'spcServiceProfile' => $spcServiceProfile->getValor(),
                'spcLineProfile'    => $spcLineProfileAntes,
                'spcVlan'           => $spcVlanAntes,
                'spcGemPort'        => $spcGemPortAntes,
                'spcTrafficTable'   => $spcTrafficTableAntes
            );

            $resultadJson = $this->reconectarService->reconectarServicioOltHuawei($arrParamReconectar);

            $respuestaFinal[0]['status']  = $resultadJson->status;
            $respuestaFinal[0]['mensaje'] = $resultadJson->mensaje;
            $status                     = $respuestaFinal[0]['status'];
            $mensajeActivarPlanAnterior = $respuestaFinal[0]['mensaje'];

            if($status != "OK")
            {
                $mensaje = $mensaje . "<br>Activacion Plan Anterior:" . $mensajeActivarPlanAnterior;
            }
            //rollback de scripts
            if($boolCanceloIp == true)
            {
                //reservamos ip vieja del plan, para poderla activar
                $objIpViejaFijaAdicional->setEstado("Reservada");
                $this->emInfraestructura->persist($objIpViejaFijaAdicional);
                $this->emInfraestructura->flush();
                
                $strMacModificada = $this->activarService->cambiarMac($macOnt);
                
                //activar ip fija
                $arrayParametrosIpFija = array(
                    'ipFija'        => $objIpViejaFijaAdicional->getIp(),
                    'macOnt'        => $strMacModificada,
                    'idDocumento'   => $idDocumentoConfig,
                    'usuario'       => $usuarioConfig,
                    'elementoCnr'   => $elementoCnr
                );
                $resultadJsonIpFija = $this->activarService->configurarIpFijaHuawei($arrayParametrosIpFija);
                $statusIpFija       = $resultadJsonIpFija->status;
                $mensajeIpFija      = $resultadJsonIpFija->mensaje;
                $respuestaFinal[0]['status']  = $statusIpFija;
                $respuestaFinal[0]['mensaje'] = $mensajeIpFija;
                if($statusIpFija != "OK")
                {
                    $objIpViejaFijaAdicional->setEstado("Cancel");
                    $this->emInfraestructura->persist($objIpViejaFijaAdicional);
                    $this->emInfraestructura->flush();
                    $mensaje = $mensaje . "<br>Activacion Ip Anterior:" . $mensajeIpFija;
                }
                else
                {
                    //se activa ip en la base
                    $objIpViejaFijaAdicional->setEstado("Activo");
                    $this->emInfraestructura->persist($objIpViejaFijaAdicional);
                    $this->emInfraestructura->flush();
                }
            }
            $arrayFinal[] = array('status' => "ERROR",
                                  'mensaje' => "No se puede realizar el cambio de Plan, <br>".
                                               "Fallo activacion del plan nuevo:<b>" . $planCabNuevo->getNombrePlan() . "</b><br>".
                                               "Mensaje:" . $mensaje);
            return $arrayFinal;
        }//if($status != "OK")
    }

    /**
     * Funcion que realiza el cambio de plan de clientes en equipos TELLION de:
     * - Pyme -> Home
     * - Pyme -> Pro
     * - Pyme -> Pyme
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 13-08-2014
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 09-05-2016   Se agrega parametro empresa en metodo cambioPlanPymeTellion por conflictos de producto INTERNET DEDICADO
     * 
     * @param $arrayPeticiones  arreglo con las variables necesarias para realizar el cambio de plan
     */
    public function cambioPlanPymeTellion($arrayPeticiones)
    {
        $punto = $arrayPeticiones[0]['punto'];
        $login = $arrayPeticiones[0]['login'];

        $servicio           = $arrayPeticiones[0]['servicio'];
        $servicioTecnico    = $arrayPeticiones[0]['servicioTecnico'];
        $planCabViejo       = $arrayPeticiones[0]['planCabViejo'];
        $planDetViejo       = $arrayPeticiones[0]['planDetViejo'];

        $interfaceElemento  = $arrayPeticiones[0]['interfaceElemento'];
        $modeloElemento     = $arrayPeticiones[0]['modeloElemento'];

        $planCabNuevo       = $arrayPeticiones[0]['planCabNuevo'];
        $planDetNuevo       = $arrayPeticiones[0]['planDetNuevo'];
        $producto           = $arrayPeticiones[0]['producto'];            //producto internet
        $arrayProdIp        = $arrayPeticiones[0]['arrayProdIp'];
        $arrayProdInternet  = $arrayPeticiones[0]['arrayProdInternet'];

        $macOnt                         = $arrayPeticiones[0]['macOnt'];
        $perfilNuevo                    = $arrayPeticiones[0]['perfilNuevo'];
        $planCaractEdicionLimitada      = $arrayPeticiones[0]['planCaractEdicionLimitada'];
        $servProdCaractIndiceCliente    = $arrayPeticiones[0]['servProdCaractIndiceCliente'];
        $servProdCaractMacOnt           = $arrayPeticiones[0]['servProdCaracMacOnt'];
        $servProdCaracMacWifi           = $arrayPeticiones[0]['servProdCaracMacWifi'];
        $servProdCaracPerfil            = $arrayPeticiones[0]['servProdCaracPerfil'];

        $idEmpresa      = $arrayPeticiones[0]['idEmpresa'];
        $usrCreacion    = $arrayPeticiones[0]['usrCreacion'];
        $ipCreacion     = $arrayPeticiones[0]['ipCreacion'];

        $flagProdIpPlanNuevo    = 0;
        $flagProdIpPlanViejo    = 0;
        $flagProdAdicional      = 0;
        $tipoIpFija             = "";
        $indiceIpFija           = -1;
        $arrayIpNueva           = array();
        $arrayIpVieja           = array();
        $arrayIps               = null;
        $cantIpSolicito         = 0;
        $status                 = "";
        $servProdCaracPool      = null;
        
        $servProdCaracPool = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "POOL IP", $producto);
        
        //servicios adicionales
        $arrayServiciosPorPunto = $this->emComercial->getRepository('schemaBundle:InfoServicio')
            ->findBy(array("puntoId" => $servicio->getPuntoId()->getId()));

        //*OBTENER SCRIPT MAC WIFI--------------------------------------------------------*/
        $scriptArrayMacWifi = $this->servicioGeneral->obtenerArregloScript("obtenerMacIpDinamica", $modeloElemento);
        $idDocumentoMacWifi = $scriptArrayMacWifi[0]->idDocumento;
        //*----------------------------------------------------------------------*/
        //verificar que no se quiera cambiar al plan 100mb
        if($planCaractEdicionLimitada)
        {
            if($planCaractEdicionLimitada->getValor() == "SI" && $planCabNuevo->getTipo() == "HOME")
            {
                $arrayFinal[] = array('status' => "ERROR",
                    'mensaje' => "No se puede realizar el cambio de Plan, <br>"
                    . "<b>Exclusivo para planes Home</b>");
                return $arrayFinal;
            }//if($planCaractEdicionLimitada->getValor() == "SI" && $planCabNuevo->getTipo() == "HOME")
        }//if($planCaractEdicionLimitada)
        else
        {
            $planCaractEdicionLimitada = false;
        }

        //verificar si plan nuevo tiene ip
        $flagProdIpPlanNuevo = $this->servicioGeneral->verificarPlanTieneIp($planDetNuevo, $arrayProdIp);

        //verificar si plan viejo tiene ip
        $flagProdIpPlanViejo = $this->servicioGeneral->verificarPlanTieneIp($planDetViejo, $arrayProdIp);

        //verificar si servicio tiene ip adicional
        $flagProdAdicional = $this->servicioGeneral->verificarIpFijaEnPunto($arrayServiciosPorPunto, $arrayProdIp, $servicio);
        
        //validar que el plan pyme tenga producto ip definido en el plan
        if($planCabNuevo->getTipo() == "PYME" && $flagProdIpPlanNuevo<=0)
        {
            $respuestaFinal[] = array('status'  => 'ERROR', 
                                      'mensaje' => 'El Plan: <b>'.$planCabNuevo->getNombrePlan.'</b>, <br>'
                                                 . 'No tiene producto Ip definido, Favor Notificar al Dep. Marketing!');
            return $respuestaFinal;
        }//if($planCabNuevo->getTipo() == "PYME" && $flagProdIpPlanNuevo<=0)
        
        //si no es de edicion limitada, y tiene ip (adicional o en el plan nuevo) y se pasa a un home, error
        if(!$planCaractEdicionLimitada && $flagProdIpPlanNuevo>0 && $planCabNuevo->getTipo()=="HOME" )
        {
            $respuestaFinal[] = array('status'  => 'ERROR', 
                                      'mensaje' => 'El Plan: <b>'.$planCabNuevo->getNombrePlan.'</b>, <br>'
                                                 . 'Tiene producto Ip definido y es de tipo HOME, <br>'
                                                 . 'Por lo tanto el cambio de plan NO es permitido <br>'
                                                 . 'Favor Notificar al Dep. Marketing!');
            return $respuestaFinal;
        }
        
        //obtener unicamente los servicios adicionales de ip fija
        $arrayServiciosIpAdicional = $this->servicioGeneral
                                 ->getServiciosIpAdicionalEnPunto($arrayServiciosPorPunto, $arrayProdIp, $servicio);
        
        //obtener la cantidad de ips que se necesita
        if($flagProdIpPlanViejo>0 && ($planCabNuevo->getTipo()=="PRO" || $planCabNuevo->getTipo()=="PYME"))
        {
            $cantIpSolicito=1;
        }
        if($flagProdAdicional>0 && $planCabNuevo->getTipo()=="PYME")
        {
            $cantIpSolicito=$cantIpSolicito+count($arrayServiciosIpAdicional);
        }
        
        //solicitar ip
        if($cantIpSolicito>0)
        {
            //cambiar tipo de negocio, para poder hacer la solicitud de ip fija
            $this->servicioGeneral->setTipoNegocioEnInfoPunto($punto, $planCabNuevo->getTipo(), $idEmpresa);

            //revisar si existen ips pa el nuevo plan
            $arregloIps = $this->recursosRed
                ->getIpsDisponiblePoolOlt($cantIpSolicito, $servicioTecnico->getElementoId(), $servicio->getId(), 
                                          $punto->getId(), "SI", $planCabNuevo->getId());

            if($arregloIps['error'])
            {
                //regresamos el tipo de negocio al original
                $this->servicioGeneral->setTipoNegocioEnInfoPunto($punto, $planCabViejo->getTipo(), $idEmpresa);

                $arrayFinal[] = array('status' => "ERROR", 'mensaje' => $arregloIps['error']);
                return $arrayFinal;
            }//if($arregloIps['error'])

            //verificar si la ip esta configurada
            $arrayIps = $arregloIps['ips'];
        }//if($cantIpSolicito>0)
                
        //verificar si plan tiene ip y que esten en el olt correctamente 
        if($flagProdIpPlanViejo>0)
        {
            //verificar que la mac del wifi este en el olt
            $resultadoJsonMacWifi = $this->activarService
                ->verificarMacWifi($servicioTecnico, $interfaceElemento, $servProdCaracMacWifi->getValor(), 
                                   $servProdCaractIndiceCliente->getValor(), $idDocumentoMacWifi);
            $statusMacWifi = $resultadoJsonMacWifi->status;
            if($statusMacWifi == "ERROR")
            {
                //regresamos el tipo de negocio al original
                $this->servicioGeneral->setTipoNegocioEnInfoPunto($punto, $planCabViejo->getTipo(), $idEmpresa);
                
                $respuestaFinal[] = array('status' => $statusMacWifi, 'mensaje' => $resultadoJsonMacWifi->mensaje);
                return $respuestaFinal;
            }//if($statusMacWifi == "ERROR")
            
            //obtener ip del plan
            $arrayIpPlan = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                        ->findBy(array("servicioId"=>$servicio->getId(),"estado"=>"Activo"));
            
            //validar si existe mas de una ip de plan dentro del servicio
            if(count($arrayIpPlan)>1)
            {
                //regresamos el tipo de negocio al original
                $this->servicioGeneral->setTipoNegocioEnInfoPunto($punto, $planCabViejo->getTipo(), $idEmpresa);
                
                $respuestaFinal[] = array('status' => 'ERROR', 
                                          'mensaje' => 'Existe mas de una Ip de Plan en el Servicio,<br>'
                                                     . 'Favor Notificar a Sistemas!');
                return $respuestaFinal;
            }//if(count($arrayIpPlan)>1)
            
            //asignar a una variable
            $ipPlan = $arrayIpPlan[0];
            
            //agregar ip vieja a un array
//            array_push($arrayIpVieja, $ipPlan);
            
            //cambiar mac
            $macWifi = strtoupper( $this->cancelarService->cambiarMac($servProdCaracMacWifi->getValor()) );
            
            //verificar si la ip del plan esta configurada con la mac que se encuentra en telcos
            $resultadoJsonIpConfigPlan = $this->servicioGeneral
                        ->verificarIpMacConfigurada($modeloElemento, $servicioTecnico, $ipPlan->getIp(), 
                                                      $macWifi);
            $statusIpConfigPlan = $resultadoJsonIpConfigPlan->status;
            
            if($statusIpConfigPlan == "ERROR")
            {
                //regresamos el tipo de negocio al original
                $this->servicioGeneral->setTipoNegocioEnInfoPunto($punto, $planCabViejo->getTipo(), $idEmpresa);
                
                $respuestaFinal[] = array('status' => $statusIpConfigPlan, 
                                          'mensaje' => $resultadoJsonIpConfigPlan->mensaje.", <br> Favor Revisar!");
                return $respuestaFinal;
            }//if($statusIpConfigPlan == "ERROR")
            
            //verificar ips nuevas
            if($planCabViejo->getTipo()=="PRO" || $planCabViejo->getTipo()=="PYME")
            {
                //verifica si la ip nueva se encuentra configurada en el olt
                $resultadJsonPerfil = $this->servicioGeneral
                    ->verificarIpConfigurada($modeloElemento, $servicioTecnico, $arrayIps[0]['ip']);

                $status = $resultadJsonPerfil->status;
                $macIpConf = $resultadJsonPerfil->mensaje;
                if($status == "ERROR")
                {
                    //regresamos el tipo de negocio al original
                    $this->servicioGeneral->setTipoNegocioEnInfoPunto($punto, $planCabViejo->getTipo(), $idEmpresa);

                    $olt = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                        ->find($servicioTecnico->getElementoId());
                    $respuestaFinal[] = array('status' => 'ERROR', 'mensaje' => 'Ip: <b>' . $arrayIps[0]['ip'] .
                        '</b> ya se encuentra configurada en <b>' . $olt->getNombreElemento() .
                        '</b> con la mac <b>' . $macIpConf .
                        '</b>. Favor notificar a Sistemas.');
                    return $respuestaFinal;
                }//if($status == "ERROR")

                //se asigna la ip
                array_push($arrayIpNueva, $arrayIps[0]['ip']);
                $tipoIpFija = $arrayIps[0]['tipo'];
            }//if($planCabViejo->getTipo()=="PRO" || $planCabViejo->getTipo()=="PYME")
        }//if($flagProdIpPlanViejo>0)
        else
        {
            //regresamos el tipo de negocio al original
            $this->servicioGeneral->setTipoNegocioEnInfoPunto($punto, $planCabViejo->getTipo(), $idEmpresa);
            
            $respuestaFinal[] = array('status' => 'ERROR', 
                                      'mensaje' => 'Plan Pyme:<b>'.$planCabViejo->getNombrePlan().'</b>, '
                                                 . 'no puede estar sin ips dentro del plan,<br>Favor Revisar!');
            return $respuestaFinal;
        }//else
        
        //verificar si el punto tiene servicios ip adicional y que esten en el olt correctamente 
        if($flagProdAdicional>0)
        {
            //contador para el array de ips nuevas
            $c = 1;
            
            //se recorre el arreglo de servicios de ips adicionales
            for($i=0;$i<count($arrayServiciosIpAdicional);$i++)
            {
                $servicioIpAdicional = $arrayServiciosIpAdicional[$i];
                
                //obtener ip del servicio adicional
                $arrayIpAdicional = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                            ->findBy(array("servicioId"=>$servicioIpAdicional->getId(),"estado"=>"Activo"));

                //validar si existe mas de una ip adicional dentro del servicio
                if(count($arrayIpAdicional)>1)
                {
                    //regresamos el tipo de negocio al original
                    $this->servicioGeneral->setTipoNegocioEnInfoPunto($punto, $planCabViejo->getTipo(), $idEmpresa);
                    
                    $respuestaFinal[] = array('status' => 'ERROR', 
                                              'mensaje' => 'Existe mas de una Ip en algun Servicio Adicional,<br>'
                                                         . 'Favor Revisar!');
                    return $respuestaFinal;
                }//if(count($arrayIpAdicional)>1)
                
                //asignar a una variable
                $ipAdicional = $arrayIpAdicional[0];
                
                //obtener mac del cliente
                $servProdCaracMac = $this->servicioGeneral
                                             ->getServicioProductoCaracteristica($servicioIpAdicional, "MAC", $producto);
                
                if($servProdCaracMac)
                {
                    //agregar ip vieja a un array
                    array_push($arrayIpVieja, $ipAdicional);
                    
                    //cambiar formato de mac
                    $macCliente = strtoupper( $this->cancelarService->cambiarMac($servProdCaracMac->getValor()) );
                    
                    //verificar que exista mac con ip fija
                    $resultadoJsonIpConfigAdicional = $this->servicioGeneral
                                ->verificarIpMacConfigurada($modeloElemento, $servicioTecnico, $ipAdicional->getIp(), $macCliente);
                    $statusIpConfig = $resultadoJsonIpConfigAdicional->status;

                    if($statusIpConfig == "ERROR")
                    {
                        //regresamos el tipo de negocio al original
                        $this->servicioGeneral->setTipoNegocioEnInfoPunto($punto, $planCabViejo->getTipo(), $idEmpresa);
                        
                        $respuestaFinal[] = array('status' => $statusIpConfig, 
                                                  'mensaje' => $resultadoJsonIpConfigAdicional->mensaje.", <br> Favor Revisar!");
                        return $respuestaFinal;
                    }//if($statusIpConfig == "ERROR")
                }//if($servProdCaracMac)
                else
                {
                    //regresamos el tipo de negocio al original
                    $this->servicioGeneral->setTipoNegocioEnInfoPunto($punto, $planCabViejo->getTipo(), $idEmpresa);
                    
                    $respuestaFinal[] = array('status' => 'ERROR', 
                                              'mensaje' => 'No existe Mac registrada para el servicio de ip adicional,<br>'
                                                         . 'con ip:'.$ipAdicional->getIp());
                    return $respuestaFinal;
                }//else
                
                //verificar ips nuevas
                if($planCabNuevo->getTipo()=="PYME")
                {
                    //se verifica si las ips nuevas se encuentran configuradas en el olt
                    $resultadJsonPerfil = $this->servicioGeneral
                        ->verificarIpConfigurada($modeloElemento, $servicioTecnico, $arrayIps[$c]['ip']);

                    $status = $resultadJsonPerfil->status;
                    $macIpConf = $resultadJsonPerfil->mensaje;
                    if($status == "ERROR")
                    {
                        //regresamos el tipo de negocio al original
                        $this->servicioGeneral->setTipoNegocioEnInfoPunto($punto, $planCabViejo->getTipo(), $idEmpresa);

                        $olt = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                            ->find($servicioTecnico->getElementoId());
                        $respuestaFinal[] = array('status' => 'ERROR', 'mensaje' => 'Ip: <b>' . $arrayIps[$c]['ip'] .
                            '</b> ya se encuentra configurada en <b>' . $olt->getNombreElemento() .
                            '</b> con la mac <b>' . $macIpConf .
                            '</b>. Favor notificar a Sistemas.');
                        return $respuestaFinal;
                    }//if($status == "ERROR")

                    //se asigna la ip
                    array_push($arrayIpNueva, $arrayIps[$c]['ip']);
                    $c++;
                }//if($planCabNuevo->getTipo()=="PYME")
            }//for($i=0;$i<count($arrayServiciosIpAdicional);$i++)
        }//end if
        
        //cancelar servicio de internet con ip del plan
        if($flagProdIpPlanViejo>0)
        {
            $arrayParametros = array(
                                        'servicioTecnico'   => $servicioTecnico,
                                        'interfaceElemento' => $interfaceElemento,
                                        'modeloElemento'    => $modeloElemento,
                                        'producto'          => $producto,
                                        'login'             => $login,
                                        'idEmpresa'         => $idEmpresa,
                                        'ipCreacion'        => $ipCreacion,
                                        'usrCreacion'       => $usrCreacion
                                    );
            $respuestaArray = $this->cancelarService->cancelarServicioMdConIp($arrayParametros);
            $status = $respuestaArray[0]['status'];
            $mensaje = $respuestaArray[0]['mensaje'];
        }//if($flagProdIpPlanViejo>0)
        else
        {
            //regresamos el tipo de negocio al original
            $this->servicioGeneral->setTipoNegocioEnInfoPunto($punto, $planCabViejo->getTipo(), $idEmpresa);
            
            $respuestaFinal[] = array('status' => 'ERROR', 
                                      'mensaje' => 'Plan Pyme:<b>'.$planCabViejo->getNombrePlan().'</b>, '
                                                 . 'no puede estar sin ips dentro del plan,<br>Favor Revisar!');
            return $respuestaFinal;
        }//else
        
        //cancelar servicios de ips adicionales
        if($flagProdAdicional>0 && $status=="OK")
        {
            //recorremos servicios de ip adicionales
            for($i=0;$i<count($arrayServiciosIpAdicional);$i++)
            {
                $servicioIpAdicional = $arrayServiciosIpAdicional[$i];
                
                $arrParametrosCancel = array(
                                                'servicioTecnico'   => $servicioTecnico,
                                                'modeloElemento'    => $modeloElemento,
                                                'interfaceElemento' => $interfaceElemento,
                                                'producto'          => $producto,
                                                'servicio'          => $servicioIpAdicional,
                                                'spcMac'            => "",
                                                'scope'             => ""
                                            );
                
                //cancelar ip adicional (script y base)
                $respuestaArray = $this->cancelarService->cancelarServicioIp($arrParametrosCancel);
                $status = $respuestaArray[0]['status'];
                $mensaje = $respuestaArray[0]['mensaje'];
                
                //si falla algo en la cancelacion de ips adicionales
                if($status!="OK")
                {
                    //regresamos el tipo de negocio al original
                    $this->servicioGeneral->setTipoNegocioEnInfoPunto($punto, $planCabViejo->getTipo(), $idEmpresa);
                    
                    //reservar ip del plan para activar (rollback)
                    $ipPlan = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                ->findOneBy(array("servicioId" => $servicio->getId(), "estado"=>"Activo"));
                    $ipPlan->setEstado("Reservada");
                    $this->emInfraestructura->persist($ipPlan);
                    $this->emInfraestructura->flush();
                    
                    $arrayParametros=array(
                                        'servicio'          => $servicio,
                                        'servicioTecnico'   => $servicioTecnico,
                                        'interfaceElemento' => $interfaceElemento,
                                        'modeloElemento'    => $modeloElemento,
                                        'producto'          => $producto,
                                        'macOnt'            => $macOnt,
                                        'macWifi'           => $servProdCaracMacWifi->getValor(),
                                        'perfil'            => $servProdCaracPerfil->getValor(),
                                        'login'             => $login,
                                        'usrCreacion'       => $usrCreacion
                                      );
                    
                    //se activa el servicio de internet con la ip del plan
                    $respuestaActivarAnteriorArray = $this->activarService
                         ->activarClienteMdConIp($arrayParametros);
                    
                    $statusActivarAnterior  = $respuestaActivarAnteriorArray[0]['status'];
                    $mensajeActivarAnterior = $respuestaActivarAnteriorArray[0]['mensaje'];
                    $indiceNuevo = $mensajeActivarAnterior;
                    
                    if($statusActivarAnterior!="OK")
                    {
                        $mensaje = $mensaje . "<br>"
                                 . "Mensaje Activar Plan Anterior:".$mensajeActivarAnterior;
                    }
                    
                    //Activar ip del plan (rollback)
                    $ipPlan->setEstado("Activo");
                    $this->emInfraestructura->persist($ipPlan);
                    $this->emInfraestructura->flush();
            
                    //grabar indice para poder cancelar el plan nuevo
                    $servProdCaractIndiceNuevo = $this->servicioGeneral
                        ->ingresarServicioProductoCaracteristica($servicio, $producto, "INDICE CLIENTE", 
                                                                 $indiceNuevo, $usrCreacion);
                    
                    //activar ips adicionales anteriores
                    for($j=($i+1);$j>=1;$j--)
                    {
                        //obtener obj ip viejas
                        $objIpAdicionalAnterior = $arrayIpVieja[$j];
                        
                        //obtener obj servicio ip vieja
                        $servicioIpAdicional = $arrayServiciosIpAdicional[$i];
                        
                        //reservar ip anterior
                        $objIpAdicionalAnterior->setEstado("Reservada");
                        $this->emInfraestructura->persist($objIpAdicionalAnterior);
                        $this->emInfraestructura->flush();
                        
                        //activar ip adicional anterior (script y base)
                        $respuestaActivarIpAnteriorArray = $this->activarService
                                             ->activarServicioIp($servicioIpAdicional, $servicioTecnico, $producto, 
                                                                 $interfaceElemento, $modeloElemento);
                        
                        $statusActivarIpAnterior  = $respuestaActivarIpAnteriorArray[0]['status'];
                        $mensajeActivarIpAnterior = $respuestaActivarIpAnteriorArray[0]['mensaje'];
                        
                        if($statusActivarIpAnterior!="OK")
                        {
                            $mensaje = $mensaje . "<br>"
                                     . "Mensaje Activar Ip ".$objIpAdicionalAnterior->getIp()." Anterior:".$mensajeActivarIpAnterior;
                        }
                        
                        //retroceder contador i para obtener los servicios ip adicionales anteriores
                        $i--;
                    }
                    
                    $arrayFinal[] = array('status' => "ERROR",
                                          'mensaje' => "No se puede realizar el cambio de Plan, <br>"
                                                     . "Fallo la cancelacion de las ips adicionales<br>"
                                                     . "Mensaje:" . $mensaje);
                    return $arrayFinal;
                }//if($status!="OK")
                
                //obtener pool para eliminar la caracteristica
                $servProdCaracPoolAdicional = $this->servicioGeneral
                                                ->getServicioProductoCaracteristica($servicioIpAdicional, "POOL IP", $producto);
                
                //eliminamos el pool anterior
                if($servProdCaracPoolAdicional)
                {
                    $this->servicioGeneral->setEstadoServicioProductoCaracteristica($servProdCaracPoolAdicional, "Eliminado");
                }//if($servProdCaracPoolAdicional)
                
                //obtener perfil para eliminar la caracteristica
                $servProdCaractPerfilAdicional = $this->servicioGeneral
                                ->getServicioProductoCaracteristica($servicioIpAdicional, "PERFIL", $producto);
                
                //eliminamos el perfil anterior
                if($servProdCaractPerfilAdicional)
                {
                    $this->servicioGeneral->setEstadoServicioProductoCaracteristica($servProdCaractPerfilAdicional, "Eliminado");
                }//if($servProdCaractPerfilAdicional)
            }//for($i=0;$i<count($arrayServiciosIpAdicional);$i++)
        }//if($flagProdAdicional>0 && $status=="OK")
        
        //activar todo
        if($status=="OK")
        {
            //cancelar ip del plan
            $ipPlan = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                        ->findOneBy(array("servicioId" => $servicio->getId(), "estado"=>"Activo"));
            $ipPlan->setEstado("Cancel");
            $this->emInfraestructura->persist($ipPlan);
            $this->emInfraestructura->flush();
            
            //eliminamos el indice anterior
            $this->servicioGeneral->setEstadoServicioProductoCaracteristica($servProdCaractIndiceCliente, "Eliminado");
            
            //eliminamos el pool anterior
            if($servProdCaracPool)
            {
                $this->servicioGeneral->setEstadoServicioProductoCaracteristica($servProdCaracPool, "Eliminado");
            }//if($servProdCaracPool)

            //actualizamos el tipo de negocio
            $this->servicioGeneral->setTipoNegocioEnInfoPunto($punto, $planCabNuevo->getTipo(), $idEmpresa);
            
            switch($planCabNuevo->getTipo())
            {
                //pyme -> home
                case "HOME":
                    $arrayParametros=array(
                                        'servicioTecnico'   => $servicioTecnico,
                                        'interfaceElemento' => $interfaceElemento,
                                        'modeloElemento'    => $modeloElemento,
                                        'macOnt'            => $macOnt,
                                        'perfil'            => $perfilNuevo,
                                        'login'             => $login,
                                        'ontLineProfile'    => "",
                                        'serviceProfile'    => "",
                                        'serieOnt'          => "",
                                        'vlan'              => "",
                                        'gemPort'           => "",
                                        'trafficTable'      => ""
                                      );
                    
                    $respuestaArray = $this->activarService
                                           ->activarClienteMdSinIp($arrayParametros);
                    
                    $status = $respuestaArray[0]['status'];
                    $mensaje = $respuestaArray[0]['mensaje'];
                    
                    if($status=="OK")
                    {
                        //si tiene ip adicional se debe cancelar el servicio adicional
                        if($flagProdAdicional > 0)
                        {
                            for($i=0;$i<count($arrayServiciosIpAdicional);$i++)
                            {
                                $servicioIpAdicional = $arrayServiciosIpAdicional[$i];
                                                                
                                if($servicioIpAdicional)
                                {
                                    //caracteristicas adicionales
                                    $arrayCaracAdicional = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                        ->findBy(array("servicioId" => $servicioIpAdicional->getId(), "estado"=>"Activo"));
                                    
                                    for($j=0;$j<count($arrayCaracAdicional);$j++)
                                    {
                                        $caracAdicional = $arrayCaracAdicional[$j];
                                        
                                        //cambiar estado al servicio adicional
                                        $caracAdicional->setEstado("Cancel");
                                        $this->emComercial->persist($caracAdicional);
                                        $this->emComercial->flush();
                                    }//for($j=0;$j<count($arrayCaracAdicional);$j++)
                                    
                                    $estadoServicioAdicional = "Cancel";
                                    $observacionAdicional = "Se cancelo servicio adicional, por cambio de plan";
                                    if($servicioIpAdicional->getEstado()=="PreAsignacionInfoTecnica" || 
                                       $servicioIpAdicional->getEstado()=="Asignada")
                                    {
                                        $estadoServicioAdicional = "Anulado";
                                        $observacionAdicional = "Se Anulo servicio adicional, por cambio de plan";
                                        
                                    }                                    
                                    //cambiar estado al servicio adicional
                                    $servicioIpAdicional->setEstado($estadoServicioAdicional);
                                    $this->emComercial->persist($servicioIpAdicional);
                                    $this->emComercial->flush();

                                    //historial del servicio adicional
                                    $servicioHistorial = new InfoServicioHistorial();
                                    $servicioHistorial->setServicioId($servicioIpAdicional);
                                    $servicioHistorial->setObservacion($observacionAdicional);
                                    $servicioHistorial->setEstado($estadoServicioAdicional);
                                    $servicioHistorial->setUsrCreacion($usrCreacion);
                                    $servicioHistorial->setFeCreacion(new \DateTime('now'));
                                    $servicioHistorial->setIpCreacion($ipCreacion);
                                    $this->emComercial->persist($servicioHistorial);
                                    $this->emComercial->flush();
                                }//if($servicioIpAdicional)
                            }//for($i=0;$i<count($arrayServiciosIpAdicional);$i++)
                        }//if($flagProdAdicional > 0)

                        return $respuestaArray;
                    }//if($status=="OK")
                    
                    break;
                //pyme -> pro
                case "PRO":
                    $arrayParametros=array(
                                        'servicioTecnico'   => $servicioTecnico,
                                        'interfaceElemento' => $interfaceElemento,
                                        'modeloElemento'    => $modeloElemento,
                                        'macOnt'            => $macOnt,
                                        'perfil'            => $perfilNuevo,
                                        'login'             => $login,
                                        'ontLineProfile'    => "",
                                        'serviceProfile'    => "",
                                        'serieOnt'          => "",
                                        'vlan'              => "",
                                        'gemPort'           => "",
                                        'trafficTable'      => ""
                                      );
                    $respuestaArray = $this->activarService
                                           ->activarClienteMdSinIp($arrayParametros);
                    
                    $status = $respuestaArray[0]['status'];
                    $mensaje = $respuestaArray[0]['mensaje'];
                    
                    if($status=="OK")
                    {
                        //si tiene ip adicional se debe cancelar los servicios adicionales y activar el nuevo
                        if($flagProdAdicional > 0)
                        {
                            //cancelar servicios ip adicionales
                            for($i=0;$i<count($arrayServiciosIpAdicional);$i++)
                            {
                                $servicioIpAdicional = $arrayServiciosIpAdicional[$i];
                                                                
                                if($servicioIpAdicional)
                                {
                                    //caracteristicas adicionales
                                    $arrayCaracAdicional = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                        ->findBy(array("servicioId" => $servicioIpAdicional->getId(), "estado"=>"Activo"));
                                    
                                    for($j=0;$j<count($arrayCaracAdicional);$j++)
                                    {
                                        $caracAdicional = $arrayCaracAdicional[$j];
                                        
                                        //cambiar estado al servicio adicional
                                        $caracAdicional->setEstado("Cancel");
                                        $this->emComercial->persist($caracAdicional);
                                        $this->emComercial->flush();
                                    }//for($j=0;$j<count($arrayCaracAdicional);$j++)
                                    
                                    $estadoServicioAdicional = "Cancel";
                                    $observacionAdicional = "Se cancelo servicio adicional, por cambio de plan";
                                    if($servicioIpAdicional->getEstado()=="PreAsignacionInfoTecnica" || 
                                       $servicioIpAdicional->getEstado()=="Asignada")
                                    {
                                        $estadoServicioAdicional = "Anulado";
                                        $observacionAdicional = "Se Anulo servicio adicional, por cambio de plan";
                                        
                                    }                                    
                                    //cambiar estado al servicio adicional
                                    $servicioIpAdicional->setEstado($estadoServicioAdicional);
                                    $this->emComercial->persist($servicioIpAdicional);
                                    $this->emComercial->flush();

                                    //historial del servicio adicional
                                    $servicioHistorial = new InfoServicioHistorial();
                                    $servicioHistorial->setServicioId($servicioIpAdicional);
                                    $servicioHistorial->setObservacion($observacionAdicional);
                                    $servicioHistorial->setEstado($estadoServicioAdicional);
                                    $servicioHistorial->setUsrCreacion($usrCreacion);
                                    $servicioHistorial->setFeCreacion(new \DateTime('now'));
                                    $servicioHistorial->setIpCreacion($ipCreacion);
                                    $this->emComercial->persist($servicioHistorial);
                                    $this->emComercial->flush();
                                }//if($servicioIpAdicional)
                            }//for($i=0;$i<count($arrayServiciosIpAdicional);$i++)
                            
                            //crear y activar servicio ip adicional---------------------------------------------------------
                            $productoIp = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                        ->findOneBy(array("empresaCod" => $idEmpresa, "estado"=>"Activo",
                                                       "nombreTecnico"=>"IP", "descripcionProducto"=>"IP FIJA"));
                            
                            //asignar valor del prod ip
                            $posProd = strpos($productoIp->getFuncionPrecio(), "=");
                            $precioVentaProd = substr($productoIp->getFuncionPrecio(), $posProd+1);

                            //crear servicio ip adicional para plan pro
                            $servicioAdicionalIpPro = new InfoServicio();
                            $servicioAdicionalIpPro->setTipoOrden("N");
                            $servicioAdicionalIpPro->setProductoId($productoIp);
                            $servicioAdicionalIpPro->setCantidad(1);
                            $servicioAdicionalIpPro->setFrecuenciaProducto(1);
                            $servicioAdicionalIpPro->setPrecioVenta($precioVentaProd);
                            $servicioAdicionalIpPro->setPuntoFacturacionId($servicio->getPuntoFacturacionId());
                            $servicioAdicionalIpPro->setPuntoId($punto);
                            $servicioAdicionalIpPro->setEsVenta("S");
                            $servicioAdicionalIpPro->setEstado("Activo");
                            $servicioAdicionalIpPro->setUsrCreacion($usrCreacion);
                            $servicioAdicionalIpPro->setFeCreacion(new \DateTime('now'));
                            $servicioAdicionalIpPro->setIpCreacion($ipCreacion);
                            $this->emComercial->persist($servicioAdicionalIpPro);
                            $this->emComercial->flush();
                            
                            //historial del servicio para el servicio adicional
                            $servicioHistorial = new InfoServicioHistorial();
                            $servicioHistorial->setServicioId($servicioAdicionalIpPro);
                            $servicioHistorial->setObservacion("Se creo servicio adicional, por cambio de plan");
                            $servicioHistorial->setEstado("Activo");
                            $servicioHistorial->setUsrCreacion($usrCreacion);
                            $servicioHistorial->setFeCreacion(new \DateTime('now'));
                            $servicioHistorial->setIpCreacion($ipCreacion);
                            $this->emComercial->persist($servicioHistorial);
                            $this->emComercial->flush();
                            
                            //ingresar caracteristica perfil para el servicio adicional
                            $this->servicioGeneral->ingresarServicioProductoCaracteristica($servicioAdicionalIpPro, 
                                                        $producto, "PERFIL", $perfilNuevo, $usrCreacion);
                            
                            //grabar ip adicional como reservada
                            $this->servicioGeneral
                                ->reservarIpAdicional($arrayIpNueva[0], $tipoIpFija, $servicioAdicionalIpPro, 
                                                      $usrCreacion, $ipCreacion);

                            //grabar la caracteristica de la mac wifi en el servicio adicional
                            $this->servicioGeneral->ingresarServicioProductoCaracteristica($servicioAdicionalIpPro, $producto, 
                                                        "MAC WIFI", $servProdCaracMacWifi->getValor(), $usrCreacion);

                            //activar ip adicional
                            $respuestaArray = $this->activarService->activarServicioIp($servicioAdicionalIpPro, 
                                                    $servicioTecnico, $producto, $interfaceElemento, $modeloElemento);
                            $statusAdicional = $respuestaArray[0]['status'];
                            $mensajeAdicional = $respuestaArray[0]['mensaje'];
                            
                            //si no se activa correctamente la ip adicional
                            if($statusAdicional!="OK")
                            {
                                $indiceNuevo = $mensaje;
                                //grabar indice para poder cancelar el plan nuevo
                                $servProdCaractIndiceNuevo = $this->servicioGeneral
                                    ->ingresarServicioProductoCaracteristica($servicio, $producto, "INDICE CLIENTE", 
                                                                             $indiceNuevo, $usrCreacion);

                                //se cancela plan nuevo
                                $arrayParametros = array(
                                        'servicioTecnico'   => $servicioTecnico,
                                        'interfaceElemento' => $interfaceElemento,
                                        'modeloElemento'    => $modeloElemento,
                                        'spcIndiceCliente'  => $servProdCaractIndiceCliente,
                                        'login'             => $login,
                                        'spcSpid'           => "",
                                        'spcMacOnt'         => "",
                                        'idEmpresa'         => $idEmpresa
                                    );
                                $this->cancelarService->cancelarServicioMdSinIp($arrayParametros);

                                //eliminar el indice nuevo grabado para la cancelacion del internet
                                $this->servicioGeneral
                                    ->setEstadoServicioProductoCaracteristica($servProdCaractIndiceNuevo, "Eliminado");
                                
                                $mensaje = $mensajeAdicional;
                                $status = $statusAdicional;
                                
                                break;
                            }//if($statusAdicional!="OK")
                            
                            //ingresar caracteristica pool ip para el servicio adicional
                            $this->servicioGeneral->ingresarServicioProductoCaracteristica($servicioAdicionalIpPro, 
                                                        $producto, "POOL IP", $mensajeAdicional, $usrCreacion);
                        }//if($flagProdAdicional > 0)

                        return $respuestaArray;
                    }//if($status=="OK")
                    
                    break;
                //pyme -> pyme
                case "PYME":
                    if($flagProdAdicional>0)
                    {
                        //si tiene ips adicionales el perfil debe ser _5
                        $perfilNuevo = substr($perfilNuevo,0,strlen($perfilNuevo)-2)."_5";
                    }                    
                    
                    //flag para saber si no funciona configuracion de ip adicionales
                    $flagConfigIpAdicional = 0;
                    
                    //reservar ip para el plan
                    $ipPlanFija=$this->servicioGeneral
                                 ->reservarIpAdicional($arrayIpNueva[0], $tipoIpFija, $servicio, $usrCreacion, $ipCreacion);
                    
                    $arrayParametros=array(
                                        'servicio'          => $servicio,
                                        'servicioTecnico'   => $servicioTecnico,
                                        'interfaceElemento' => $interfaceElemento,
                                        'modeloElemento'    => $modeloElemento,
                                        'producto'          => $producto,
                                        'macOnt'            => $macOnt,
                                        'macWifi'           => $servProdCaracMacWifi->getValor(),
                                        'perfil'            => $perfilNuevo,
                                        'login'             => $login,
                                        'usrCreacion'       => $usrCreacion
                                      );
                    
                    //activar servicio de internet con ip fija en el plan (script)
                    $respuestaArray = $this->activarService
                                           ->activarClienteMdConIp($arrayParametros);
                    
                    $status  = $respuestaArray[0]['status'];
                    $mensaje = $respuestaArray[0]['mensaje'];
                    
                    //si se activa bien el servicio de internet
                    if($status=="OK")
                    {
                        //cambiar estado (activar) de la ip del plan
                        $ipPlanFija->setEstado("Activo");
                        $this->emInfraestructura->persist($ipPlanFija);
                        $this->emInfraestructura->flush();
                        //contador para array de ips nuevas
                        $c=1;
                        
                        if($flagProdAdicional > 0)
                        {
                            for($i=0;$i<count($arrayServiciosIpAdicional);$i++)
                            {
                                $servicioIpAdicional = $arrayServiciosIpAdicional[$i];
                                
                                //ingresar perfil para el servicio adicional
                                $this->servicioGeneral
                                        ->ingresarServicioProductoCaracteristica($servicioIpAdicional, $producto, "PERFIL", 
                                                                                $perfilNuevo, $usrCreacion);
                                
                                //reservar ip para los adicionales
                                $ipNuevaAdicional = $this->servicioGeneral
                                    ->reservarIpAdicional($arrayIpNueva[$c], $tipoIpFija, $servicioIpAdicional, 
                                                          $usrCreacion, $ipCreacion);
                                
                                //activa la ip (script y base)
                                $respuestaAdicionalArray = $this->activarService
                                                                ->activarServicioIp($servicioIpAdicional, $servicioTecnico, 
                                                                                    $producto, $interfaceElemento, 
                                                                                    $modeloElemento);
                                
                                $statusAdicional = $respuestaAdicionalArray[0]['status'];
                                $mensajeAdicional = $respuestaAdicionalArray[0]['mensaje'];
                                if($statusAdicional!="OK")
                                {
                                    $indiceNuevo = $mensaje;
                                    
                                    //grabar indice para poder cancelar el plan nuevo
                                    $servProdCaractIndiceNuevo = $this->servicioGeneral
                                        ->ingresarServicioProductoCaracteristica($servicio, $producto, "INDICE CLIENTE", 
                                                                                 $indiceNuevo, $usrCreacion);

                                    //se cancela plan nuevo
                                    $arrayParametros = array(
                                        'servicioTecnico'   => $servicioTecnico,
                                        'interfaceElemento' => $interfaceElemento,
                                        'modeloElemento'    => $modeloElemento,
                                        'producto'          => $producto,
                                        'login'             => $login,
                                        'idEmpresa'         => $idEmpresa,
                                        'ipCreacion'        => $ipCreacion,
                                        'usrCreacion'       => $usrCreacion
                                    );
                                    $this->cancelarService->cancelarServicioMdConIp($arrayParametros);

                                    //eliminar el indice nuevo grabado para la cancelacion del internet
                                    $this->servicioGeneral
                                        ->setEstadoServicioProductoCaracteristica($servProdCaractIndiceNuevo, "Eliminado");
                                    
                                    //cambiar estado (eliminar) de la ip del plan
                                    $ipPlanFija->setEstado("Eliminado");
                                    $this->emInfraestructura->persist($ipPlanFija);
                                    $this->emInfraestructura->flush();
                                    
                                    //eliminar caracteristica pool
                                    $servAnteriorProdCaracPool = $this->servicioGeneral
                                            ->getServicioProductoCaracteristica($servicio, "POOL IP", $producto);

                                    if($servAnteriorProdCaracPool)
                                    {
                                        $this->servicioGeneral
                                            ->setEstadoServicioProductoCaracteristica($servAnteriorProdCaracPool, "Eliminado");
                                    }
                                    
                                    $mensaje = $mensajeAdicional;
                                    $status = $statusAdicional;
                                    
                                    $flagConfigIpAdicional=1;
                                    
                                    //cancelar ips adicionales nuevas
                                    for($j=$i;$j>=0;$j--)
                                    {
                                        $servicioIpAdicional = $arrayServiciosIpAdicional[$j];
                
                                        $arrParametrosCancel = array(
                                                                        'servicioTecnico'   => $servicioTecnico,
                                                                        'modeloElemento'    => $modeloElemento,
                                                                        'interfaceElemento' => $interfaceElemento,
                                                                        'producto'          => $producto,
                                                                        'servicio'          => $servicioIpAdicional,
                                                                        'spcMac'            => "",
                                                                        'scope'             => ""
                                                                    );
                                        
                                        //cancelar ip nueva adicional (script y base)
                                        $respuestaCancelarIpNuevaAdicionalArray = $this->cancelarService->cancelarServicioIp($arrParametrosCancel);
                                        $statusCancelarIpNuevaAdicional = $respuestaCancelarIpNuevaAdicionalArray[0]['status'];
                                        $mensajeCancelarIpNuevaAdicional = $respuestaCancelarIpNuevaAdicionalArray[0]['mensaje'];
                                        if($statusCancelarIpNuevaAdicional!="OK")
                                        {
                                            $mensaje = $mensaje . "<br>"
                                                     . "Mensaje Cancelar Ip Nueva Adicional:" .$mensajeCancelarIpNuevaAdicional;
                                        }
                                        
                                        //eliminar caracteristica pool
                                        $servAdicionalProdCaracPool = $this->servicioGeneral
                                                ->getServicioProductoCaracteristica($servicioIpAdicional, "POOL IP", $producto);
                                        
                                        if($servAdicionalProdCaracPool)
                                        {
                                            $this->servicioGeneral
                                                ->setEstadoServicioProductoCaracteristica($servAdicionalProdCaracPool, "Eliminado");
                                        }
                                        
                                        //eliminar caracteristica perfil
                                        $servAdicionalProdCaracPerfil = $this->servicioGeneral
                                                ->getServicioProductoCaracteristica($servicioIpAdicional, "PERFIL", $producto);
                                        
                                        if($servAdicionalProdCaracPerfil)
                                        {
                                            $this->servicioGeneral
                                                ->setEstadoServicioProductoCaracteristica($servAdicionalProdCaracPerfil, "Eliminado");
                                        }
                                    }
                                }//end if $statusAdicional
                                
                                //obtener pool del servicio internet
                                $poolServicioAdicional = $mensajeAdicional;
                                $servAdicionalProdCaracPool = $this->servicioGeneral
                                                ->getServicioProductoCaracteristica($servicio, "POOL IP", $producto);
                                
                                if($servAdicionalProdCaracPool)
                                {
                                    //ingresar pool para el servicio adicional
                                    $this->servicioGeneral
                                        ->ingresarServicioProductoCaracteristica($servicioIpAdicional, $producto, "POOL IP", 
                                                                                 $poolServicioAdicional, $usrCreacion);
                                }//if($servProdCaracPool)
                                
                                //historial del servicio para el servicio adicional
                                $servicioHistorial = new InfoServicioHistorial();
                                $servicioHistorial->setServicioId($servicioIpAdicional);
                                $servicioHistorial->setObservacion("Se activo ip:<b>".$ipNuevaAdicional->getIp()."</b>, "
                                                                 . "por cambio de plan");
                                $servicioHistorial->setEstado($servicioIpAdicional->getEstado());
                                $servicioHistorial->setUsrCreacion($usrCreacion);
                                $servicioHistorial->setFeCreacion(new \DateTime('now'));
                                $servicioHistorial->setIpCreacion($ipCreacion);
                                $this->emComercial->persist($servicioHistorial);
                                $this->emComercial->flush();
                                
                                $c++;
                            }//end for array servicios ip adicional
                            if($flagConfigIpAdicional==1)
                            {
                                break;
                            }//if($flagConfigIpAdicional==1)
                        }//end if flag prod adicional > 0
                        return $respuestaArray;
                    }//end if status == OK
                    //si no se activa correctamente el servicio de internet
                    else
                    {
                        $indiceNuevo = $mensaje;
                        
                        //grabar indice para poder cancelar el plan nuevo
                        $servProdCaractIndiceNuevo = $this->servicioGeneral
                            ->ingresarServicioProductoCaracteristica($servicio, $producto, "INDICE CLIENTE", 
                                                                     $indiceNuevo, $usrCreacion);

                        //se cancela plan nuevo
                        $arrayParametros = array(
                                        'servicioTecnico'   => $servicioTecnico,
                                        'interfaceElemento' => $interfaceElemento,
                                        'modeloElemento'    => $modeloElemento,
                                        'producto'          => $producto,
                                        'login'             => $login,
                                        'idEmpresa'         => $idEmpresa,
                                        'ipCreacion'        => $ipCreacion,
                                        'usrCreacion'       => $usrCreacion
                                    );
                        $this->cancelarService->cancelarServicioMdConIp($arrayParametros);

                        //eliminar el indice nuevo grabado para la cancelacion del internet
                        $this->servicioGeneral
                            ->setEstadoServicioProductoCaracteristica($servProdCaractIndiceNuevo, "Eliminado");
                        
                        //cambiar estado (eliminar) de la ip del plan
                        $ipPlanFija->setEstado("Eliminado");
                        $this->emInfraestructura->persist($ipFija);
                        $this->emInfraestructura->flush();
                    }//else
                    
                    break;
            }//switch($planCabNuevo->getTipo())
        }//if($status=="OK")
        else
        {
            //regresamos el tipo de negocio al original
            $this->servicioGeneral->setTipoNegocioEnInfoPunto($punto, $planCabViejo->getTipo(), $idEmpresa);
            
            $arrayFinal[] = array('status' => "ERROR",
                'mensaje' => "No se puede realizar el cambio de Plan, <br>"
                . "Fallo la cancelacion del plan anterior:<b>" . $planCabViejo->getNombrePlan() . "</b><br>"
                . "Mensaje:" . $mensaje);
            return $arrayFinal;
        }//else
        
        if($status!="OK")
        {
            //reservar estado de ip del plan
            $ipPlan->setEstado("Reservada");
            $this->emInfraestructura->persist($ipPlan);
            $this->emInfraestructura->flush();
            
            $arrayParametros=array(
                                        'servicio'          => $servicio,
                                        'servicioTecnico'   => $servicioTecnico,
                                        'interfaceElemento' => $interfaceElemento,
                                        'modeloElemento'    => $modeloElemento,
                                        'producto'          => $producto,
                                        'macOnt'            => $macOnt,
                                        'macWifi'           => $servProdCaracMacWifi->getValor(),
                                        'perfil'            => $servProdCaracPerfil->getValor(),
                                        'login'             => $login,
                                        'usrCreacion'       => $usrCreacion
                                      );
            
            //activar servicio (internet e ip fija)
            $respuestaArray = $this->activarService
                                ->activarClienteMdConIp($arrayParametros);
            
            $mensajeActivarAnterior = $respuestaArray[0]['mensaje'];            
            $indiceNuevo = $mensajeActivarAnterior;
            
            //grabar indice para poder activar el plan anterior
            $servProdCaractIndiceNuevo = $this->servicioGeneral
                ->ingresarServicioProductoCaracteristica($servicio, $producto, "INDICE CLIENTE", 
                                                         $indiceNuevo, $usrCreacion);
            
            if($respuestaArray[0]['status']!="OK")
            {
                $mensaje = $mensaje . "<br>Activacion Plan Anterior" .$mensajeActivarAnterior;
            }//if($respuestaArray[0]['status']!="OK")
            
            //activar estado de ip del plan
            $ipPlan->setEstado("Activo");
            $this->emInfraestructura->persist($ipPlan);
            $this->emInfraestructura->flush();
            
            //activar ips adicionales anteriores
            for($j=0;$j<count($arrayIpVieja);$j++)
            {
                //obtener obj ip viejas
                $objIpAdicionalAnterior = $arrayIpVieja[$j];
                
                //obtener obj servicio ip vieja
                $servicioIpAdicional = $arrayServiciosIpAdicional[$j];
                
                //grabar pool anterior
                $this->servicioGeneral
                    ->ingresarServicioProductoCaracteristica($servicioIpAdicional, $producto, "POOL IP", 
                                                             $servProdCaracPool->getValor(), $usrCreacion);
                                
                //grabar perfil anterior
                $this->servicioGeneral
                    ->ingresarServicioProductoCaracteristica($servicioIpAdicional, $producto, "PERFIL", 
                                                             $servProdCaracPerfil->getValor(), $usrCreacion);
                
                //reservar ip anterior
                $objIpAdicionalAnterior->setEstado("Reservada");
                $this->emInfraestructura->persist($objIpAdicionalAnterior);
                $this->emInfraestructura->flush();

                //activar ip adicional anterior (script y base)
                $respuestaActivarIpAnteriorArray = $this->activarService
                                     ->activarServicioIp($servicioIpAdicional, $servicioTecnico, $producto, 
                                                         $interfaceElemento, $modeloElemento);

                $statusActivarIpAnterior  = $respuestaActivarIpAnteriorArray[0]['status'];
                $mensajeActivarIpAnterior = $respuestaActivarIpAnteriorArray[0]['mensaje'];

                if($statusActivarIpAnterior!="OK")
                {
                    $mensaje = $mensaje . "<br>"
                             . "Mensaje Activar Ip ".$objIpAdicionalAnterior->getIp()." Anterior:".$mensajeActivarIpAnterior;
                }
            }
            
            $arrayFinal[] = array('status' => "ERROR",
                'mensaje' => "No se puede realizar el cambio de Plan, <br>"
                . "Fallo activacion del plan nuevo:<b>" . $planCabNuevo->getNombrePlan() . "</b><br>"
                . "Mensaje:" . $mensaje);
            return $arrayFinal;
        }//if($status!="OK")
    }
    
    /**
     * Funcion que realiza el cambio de plan de clientes en equipos TELLION de:
     * - Pyme -> Home
     * - Pyme -> Pro
     * - Pyme -> Pyme
     * 
     * @author Jesus Bozada <fadum@telconet.ec>
     * @version 1.0 24-12-2015
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 09-05-2016   Se agrega parametro empresa en metodo cambioPlanPymeTellionCnr por conflictos de producto INTERNET DEDICADO
     * 
     * @param $arrayPeticiones  arreglo con las variables necesarias para realizar el cambio de plan
     */
    public function cambioPlanPymeTellionCnr($arrayPeticiones)
    {
        $punto                       = $arrayPeticiones[0]['punto'];
        $login                       = $arrayPeticiones[0]['login'];
        $servicio                    = $arrayPeticiones[0]['servicio'];
        $servicioTecnico             = $arrayPeticiones[0]['servicioTecnico'];
        $planCabViejo                = $arrayPeticiones[0]['planCabViejo'];
        $planDetViejo                = $arrayPeticiones[0]['planDetViejo'];
        $interfaceElemento           = $arrayPeticiones[0]['interfaceElemento'];
        $modeloElemento              = $arrayPeticiones[0]['modeloElemento'];
        $planCabNuevo                = $arrayPeticiones[0]['planCabNuevo'];
        $planDetNuevo                = $arrayPeticiones[0]['planDetNuevo'];
        $producto                    = $arrayPeticiones[0]['producto'];            //producto internet
        $arrayProdIp                 = $arrayPeticiones[0]['arrayProdIp'];
        $macOnt                      = $arrayPeticiones[0]['macOnt'];
        $perfilNuevo                 = $arrayPeticiones[0]['perfilNuevo'];
        $servProdCaractIndiceCliente = $arrayPeticiones[0]['servProdCaractIndiceCliente'];
        $idEmpresa                   = $arrayPeticiones[0]['idEmpresa'];
        $usrCreacion                 = $arrayPeticiones[0]['usrCreacion'];
        $flagProdIpPlanNuevo         = 0;
        $flagProdIpPlanViejo         = 0;
        $flagProdAdicional           = 0;
        $tipoIpFija                  = "";
        $indiceIpFija                = -1;
        $arrayIpNueva                = array();
        $arrayIpVieja                = array();
        $arrayIps                    = null;
        $cantIpSolicito              = 0;
        $status                      = "";
        
        
        //servicios adicionales
        $arrayServiciosPorPunto = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                       ->findBy(array("puntoId" => $servicio->getPuntoId()->getId()));

        //*OBTENER SCRIPT MAC WIFI--------------------------------------------------------*/
        $scriptArrayMacWifi = $this->servicioGeneral->obtenerArregloScript("obtenerMacIpDinamica", $modeloElemento);
        $idDocumentoMacWifi = $scriptArrayMacWifi[0]->idDocumento;
        //*----------------------------------------------------------------------*/
        
        //verificar si plan nuevo tiene ip
        $flagProdIpPlanNuevo = $this->servicioGeneral->verificarPlanTieneIp($planDetNuevo, $arrayProdIp);

        //verificar si plan viejo tiene ip
        $flagProdIpPlanViejo = $this->servicioGeneral->verificarPlanTieneIp($planDetViejo, $arrayProdIp);

        //verificar si servicio tiene ip adicional
        $flagProdAdicional   = $this->servicioGeneral->verificarIpFijaEnPunto($arrayServiciosPorPunto, $arrayProdIp, $servicio);
        
        //validar que el plan pyme tenga producto ip definido en el plan
        if($planCabNuevo->getTipo() == "PYME" && $flagProdIpPlanNuevo<=0)
        {
            $respuestaFinal[] = array('status'  => 'ERROR', 
                                      'mensaje' => 'El Plan: <b>'.$planCabNuevo->getNombrePlan.'</b>, <br>'
                                                 . 'No tiene producto Ip definido, Favor Notificar al Dep. Marketing!');
            return $respuestaFinal;
        }//if($planCabNuevo->getTipo() == "PYME" && $flagProdIpPlanNuevo<=0)
        
        //verificar si plan tiene ip y que esten en el olt correctamente 
        if($flagProdIpPlanViejo<=0)
        {
            //regresamos el tipo de negocio al original
            $this->servicioGeneral->setTipoNegocioEnInfoPunto($punto, $planCabViejo->getTipo(), $idEmpresa);
            
            $respuestaFinal[] = array('status'  => 'ERROR', 
                                      'mensaje' => 'Plan Pyme:<b>'.$planCabViejo->getNombrePlan().'</b>, '
                                                  .'no puede estar sin ips dentro del plan,<br>Favor Revisar!');
            return $respuestaFinal;
        }//else
        
        //cancelar servicio de internet con ip del plan
        if($flagProdIpPlanViejo>0)
        {
            //se cancela plan nuevo
            $arrayParametros = array(
                                        'servicioTecnico'   => $servicioTecnico,
                                        'interfaceElemento' => $interfaceElemento,
                                        'modeloElemento'    => $modeloElemento,
                                        'spcIndiceCliente'  => $servProdCaractIndiceCliente,
                                        'login'             => $login,
                                        'idEmpresa'         => $idEmpresa
                                    );
            $respuestaArray = $this->cancelarService->cancelarServicioMdSinIp($arrayParametros);
            
            
            $status  = $respuestaArray[0]['status'];
            $mensaje = $respuestaArray[0]['mensaje'];
        }//if($flagProdIpPlanViejo>0)
        
        //activar todo
        if($status=="OK")
        {
            //eliminamos el indice anterior
            $this->servicioGeneral->setEstadoServicioProductoCaracteristica($servProdCaractIndiceCliente, "Eliminado");
            
            //actualizamos el tipo de negocio
            $this->servicioGeneral->setTipoNegocioEnInfoPunto($punto, $planCabNuevo->getTipo(), $idEmpresa);
            
            switch($planCabNuevo->getTipo())
            {
                //pyme -> pyme
                case "PYME":
                    if($flagProdAdicional>0)
                    {
                        //si tiene ips adicionales el perfil debe ser _5
                        $perfilNuevo = substr($perfilNuevo,0,strlen($perfilNuevo)-2)."_5";
                    }                    
                    
                    $arrayParametros=array(
                                        'servicioTecnico'   => $servicioTecnico,
                                        'interfaceElemento' => $interfaceElemento,
                                        'modeloElemento'    => $modeloElemento,
                                        'macOnt'            => $macOnt,
                                        'perfil'            => $perfilNuevo,
                                        'login'             => $login,
                                        'ontLineProfile'    => "",
                                        'serviceProfile'    => "",
                                        'serieOnt'          => "",
                                        'vlan'              => "",
                                        'gemPort'           => "",
                                        'trafficTable'      => ""
                                      );
                    $respuestaArray = $this->activarService
                                           ->activarClienteMdSinIp($arrayParametros);
                    
                    $status         = $respuestaArray[0]['status'];
                    $mensaje        = $respuestaArray[0]['mensaje'];
                    
                    //si se activa bien el servicio de internet
                    if($status=="OK")
                    {
                        return $respuestaArray;
                    }//end if status == OK
                    //si no se activa correctamente el servicio de internet
                    else
                    {
                        $indiceNuevo = $mensaje;
                        
                        //grabar indice para poder cancelar el plan nuevo
                        $servProdCaractIndiceNuevo = $this->servicioGeneral
                                                          ->ingresarServicioProductoCaracteristica($servicio, 
                                                                                                   $producto, 
                                                                                                   "INDICE CLIENTE", 
                                                                                                   $indiceNuevo, 
                                                                                                   $usrCreacion);

                        //se cancela plan nuevo
                        $arrayParametros = array(
                                                    'servicioTecnico'   => $servicioTecnico,
                                                    'interfaceElemento' => $interfaceElemento,
                                                    'modeloElemento'    => $modeloElemento,
                                                    'spcIndiceCliente'  => $servProdCaractIndiceNuevo,
                                                    'producto'          => $producto,
                                                    'login'             => $login,
                                                    'idEmpresa'         => $idEmpresa
                                                );
                        $this->cancelarService->cancelarServicioMdSinIp($arrayParametros);

                        //eliminar el indice nuevo grabado para la cancelacion del internet
                        $this->servicioGeneral
                            ->setEstadoServicioProductoCaracteristica($servProdCaractIndiceNuevo, "Eliminado");
                    }//else
                    
                    break;
            }//switch($planCabNuevo->getTipo())
        }//if($status=="OK")
        else
        {
            //regresamos el tipo de negocio al original
            $this->servicioGeneral->setTipoNegocioEnInfoPunto($punto, $planCabViejo->getTipo(), $idEmpresa);
            
            $arrayFinal[] = array('status'  => "ERROR",
                                  'mensaje' => "No se puede realizar el cambio de Plan, <br>"
                                              ."Fallo la cancelacion del plan anterior:<b>" . $planCabViejo->getNombrePlan() . "</b><br>"
                                              ."Mensaje:" . $mensaje);
            return $arrayFinal;
        }//else
        
        if($status!="OK")
        {
            
            $arrayParametros=array(
                                        'servicioTecnico'   => $servicioTecnico,
                                        'interfaceElemento' => $interfaceElemento,
                                        'modeloElemento'    => $modeloElemento,
                                        'macOnt'            => $macOnt,
                                        'perfil'            => $perfilNuevo,
                                        'login'             => $login,
                                        'ontLineProfile'    => "",
                                        'serviceProfile'    => "",
                                        'serieOnt'          => "",
                                        'vlan'              => "",
                                        'gemPort'           => "",
                                        'trafficTable'      => ""
                                      );
            
            //activar servicio (internet e ip fija)
            $respuestaArray = $this->activarService
                                ->activarClienteMdSinIp($arrayParametros);
            
            $mensajeActivarAnterior = $respuestaArray[0]['mensaje'];            
            $indiceNuevo            = $mensajeActivarAnterior;
            
            //grabar indice para poder activar el plan anterior
            $servProdCaractIndiceNuevo = $this->servicioGeneral
                                              ->ingresarServicioProductoCaracteristica( $servicio, 
                                                                                        $producto, 
                                                                                        "INDICE CLIENTE", 
                                                                                        $indiceNuevo, 
                                                                                        $usrCreacion);
            
            if($respuestaArray[0]['status']!="OK")
            {
                $mensaje = $mensaje . "<br>Activacion Plan Anterior" .$mensajeActivarAnterior;
            }//if($respuestaArray[0]['status']!="OK")
            
            
            $arrayFinal[] = array('status'  => "ERROR",
                                  'mensaje' => "No se puede realizar el cambio de Plan, <br>"
                                              ."Fallo activacion del plan nuevo:<b>" . $planCabNuevo->getNombrePlan() . "</b><br>"
                                              ."Mensaje:" . $mensaje);
            return $arrayFinal;
        }//if($status!="OK")
    }
    
    /**
     * Funcion que realiza el cambio de plan  de clientes en equipos HUAWEI de:
     * - Pyme -> Home
     * - Pyme -> Pro
     * - Pyme -> Pyme
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 20-05-2015
     * @param $arrayPeticiones  arreglo con las variables necesarias para realizar el cambio de plan
     */
    public function cambioPlanPymeHuawei($arrayPeticiones)
    {
        $punto                          = $arrayPeticiones[0]['punto'];
        $servicio                       = $arrayPeticiones[0]['servicio'];
        $servicioTecnico                = $arrayPeticiones[0]['servicioTecnico'];
        $planCabViejo                   = $arrayPeticiones[0]['planCabViejo'];
        $planDetViejo                   = $arrayPeticiones[0]['planDetViejo'];
        $interfaceElemento              = $arrayPeticiones[0]['interfaceElemento'];
        $modeloElemento                 = $arrayPeticiones[0]['modeloElemento'];
        $planCabNuevo                   = $arrayPeticiones[0]['planCabNuevo'];
        $planDetNuevo                   = $arrayPeticiones[0]['planDetNuevo'];
        $producto                       = $arrayPeticiones[0]['producto'];
        $productoIp                     = $arrayPeticiones[0]['productoIp'];
        $arrayProdIp                    = $arrayPeticiones[0]['arrayProdIp'];
        $macOnt                         = $arrayPeticiones[0]['macOnt'];
        $planCaractEdicionLimitada      = $arrayPeticiones[0]['planCaractEdicionLimitada'];
        $servProdCaractIndiceCliente    = $arrayPeticiones[0]['servProdCaractIndiceCliente'];
        $servProdCaractMacOnt           = $arrayPeticiones[0]['servProdCaracMacOnt'];
        $idEmpresa                      = $arrayPeticiones[0]['idEmpresa'];
        $usrCreacion                    = $arrayPeticiones[0]['usrCreacion'];
        $ipCreacion                     = $arrayPeticiones[0]['ipCreacion'];
        $spcSpid                        = $arrayPeticiones[0]['spid'];
        $spcServiceProfile              = $arrayPeticiones[0]['serviceProfile'];
        $spcLineProfile                 = $arrayPeticiones[0]['lineProfile'];
        $spcVlan                        = $arrayPeticiones[0]['vlan'];
        $spcGemPort                     = $arrayPeticiones[0]['gemPort'];
        $spcTrafficTable                = $arrayPeticiones[0]['trafficTable'];
        $spcLineProfileAntes            = $arrayPeticiones[0]['lineProfileAntes'];
        $spcVlanAntes                   = $arrayPeticiones[0]['vlanAntes'];
        $spcGemPortAntes                = $arrayPeticiones[0]['gemPortAntes'];
        $spcTrafficTableAntes           = $arrayPeticiones[0]['trafficTableAntes'];
        $spcScopeAntes                  = $arrayPeticiones[0]['scopeAntes'];
        $flagProdIpPlanNuevo            = 0;
        $flagProdIpPlanViejo            = 0;
        $flagProdAdicional              = 0;
        $tipoIpFija                     = "";
        $arrayIpNueva                   = array();
        $arrayIpVieja                   = array();
        $status                         = "";
        $ipPlan                         = 0;

        $elemento = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($servicioTecnico->getElementoId());

        //servicios adicionales
        $arrayServiciosPorPunto = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                       ->findBy(array("puntoId" => $servicio->getPuntoId()->getId()));

        //obtener objeto modelo cnr
        $modeloElementoCnr = $this->emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                  ->findOneBy(array("nombreModeloElemento" => "CNR UCS C220",
                                                    "estado"               => "Activo"));

        //obtener elemento cnr
        $elementoCnr = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                            ->findOneBy(array("modeloElementoId" => $modeloElementoCnr->getId()));
        $scriptArray       = $this->servicioGeneral->obtenerArregloScript("configurarIpFija", $modeloElementoCnr);
        $idDocumentoConfig = $scriptArray[0]->idDocumento;
        $usuarioConfig     = $scriptArray[0]->usuario;

        //servicios adicionales
        $arrayServiciosPorPunto = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                       ->findBy(array("puntoId" => $servicio->getPuntoId()->getId()));

        //verificar que no se quiera cambiar al plan 100mb
        if($planCaractEdicionLimitada)
        {
            if($planCaractEdicionLimitada->getValor() == "SI" && $planCabNuevo->getTipo() == "HOME")
            {
                $arrayFinal[] = array('status' => "ERROR",
                                      'mensaje' => "No se puede realizar el cambio de Plan, <br>".
                                                   "<b>Exclusivo para planes Home</b>");
                return $arrayFinal;
            }//if($planCaractEdicionLimitada->getValor() == "SI" && $planCabNuevo->getTipo() == "HOME")
        }//if($planCaractEdicionLimitada)
        else
        {
            $planCaractEdicionLimitada = false;
        }

        //verificar si plan nuevo tiene ip
        $flagProdIpPlanNuevo = $this->servicioGeneral->verificarPlanTieneIp($planDetNuevo, $arrayProdIp);

        //verificar si plan viejo tiene ip
        $flagProdIpPlanViejo = $this->servicioGeneral->verificarPlanTieneIp($planDetViejo, $arrayProdIp);

        //verificar si servicio tiene ip adicional
        $flagProdAdicional = $this->servicioGeneral->verificarIpFijaEnPunto($arrayServiciosPorPunto, $arrayProdIp, $servicio);

        //validar que el plan pyme tenga producto ip definido en el plan
        if($planCabNuevo->getTipo() == "PYME" && $flagProdIpPlanNuevo <= 0)
        {
            $respuestaFinal[] = array('status' => 'ERROR',
                                      'mensaje' => 'El Plan: <b>' . $planCabNuevo->getNombrePlan . '</b>, <br>'.
                                                   'No tiene producto Ip definido, Favor Notificar al Dep. Marketing!');
            return $respuestaFinal;
        }//if($planCabNuevo->getTipo() == "PYME" && $flagProdIpPlanNuevo<=0)
        //si no es de edicion limitada, y tiene ip (adicional o en el plan nuevo) y se pasa a un home, error
        if(!$planCaractEdicionLimitada && $flagProdIpPlanNuevo > 0 && $planCabNuevo->getTipo() == "HOME")
        {
            $respuestaFinal[] = array('status' => 'ERROR',
                                      'mensaje' => 'El Plan: <b>' . $planCabNuevo->getNombrePlan . '</b>, <br>'.
                                                   'Tiene producto Ip definido y es de tipo HOME, <br>'.
                                                   'Por lo tanto el cambio de plan NO es permitido <br>'.
                                                   'Favor Notificar al Dep. Marketing!');
            return $respuestaFinal;
        }

        //obtener unicamente los servicios adicionales de ip fija
        $arrayServiciosIpAdicional = $this->servicioGeneral
                                          ->getServiciosIpAdicionalEnPunto($arrayServiciosPorPunto, $arrayProdIp, $servicio);


        //verificar si plan tiene ip
        if($flagProdIpPlanViejo > 0)
        {
            //obtener ip del plan
            $arrayIpPlan = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                ->findBy(array("servicioId" => $servicio->getId(), "estado" => "Activo"));

            //validar si existe mas de una ip de plan dentro del servicio
            if(count($arrayIpPlan) > 1)
            {
                //regresamos el tipo de negocio al original
                $this->servicioGeneral->setTipoNegocioEnInfoPunto($punto, $planCabViejo->getTipo(), $idEmpresa);

                $respuestaFinal[] = array('status' => 'ERROR',
                                          'mensaje' => 'Existe mas de una Ip de Plan en el Servicio,<br>'.
                                                       'Favor Notificar a Sistemas!');
                return $respuestaFinal;
            }//if(count($arrayIpPlan)>1)
            //asignar a una variable
            $ipPlan = $arrayIpPlan[0];
        }//if($flagProdIpPlanViejo>0)
        else
        {
            //regresamos el tipo de negocio al original
            $this->servicioGeneral->setTipoNegocioEnInfoPunto($punto, $planCabViejo->getTipo(), $idEmpresa);

            $respuestaFinal[] = array('status' => 'ERROR',
                                      'mensaje' => 'Plan Pyme:<b>' . $planCabViejo->getNombrePlan() . '</b>, '.
                                                   'no puede estar sin ips dentro del plan,<br>Favor Revisar!');
            return $respuestaFinal;
        }//else
        //verificar si el punto tiene servicios ip adicional
        if($flagProdAdicional > 0)
        {
            //se recorre el arreglo de servicios de ips adicionales
            for($i = 0; $i < count($arrayServiciosIpAdicional); $i++)
            {
                $servicioIpAdicional = $arrayServiciosIpAdicional[$i];

                //obtener ip del servicio adicional
                $arrayIpAdicional = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                         ->findBy(array("servicioId" => $servicioIpAdicional->getId(), "estado" => "Activo"));

                //validar si existe mas de una ip adicional dentro del servicio
                if(count($arrayIpAdicional) > 1)
                {
                    //regresamos el tipo de negocio al original
                    $this->servicioGeneral->setTipoNegocioEnInfoPunto($punto, $planCabViejo->getTipo(), $idEmpresa);

                    $respuestaFinal[] = array('status' => 'ERROR',
                                              'mensaje' => 'Existe mas de una Ip en algun Servicio Adicional,<br>'.
                                                           'Favor Revisar!');
                    return $respuestaFinal;
                }//if(count($arrayIpAdicional)>1)
                $ipAdicional = $arrayIpAdicional[0];
                array_push($arrayIpVieja, $ipAdicional);
            }//for($i=0;$i<count($arrayServiciosIpAdicional);$i++)
        }//end if
        //Cancelar Ip fija del plan
        if($planCabNuevo->getTipo() == "HOME" || ($flagProdIpPlanNuevo == 0 && $planCabNuevo->getTipo() == "PRO"))
        {
            $arrParametrosCancel = array(
                'servicioTecnico'   => $servicioTecnico,
                'modeloElemento'    => $modeloElemento,
                'interfaceElemento' => $interfaceElemento,
                'producto'          => $producto,
                'servicio'          => $servicio,
                'spcMac'            => $servProdCaractMacOnt,
                'spcIndiceCliente'  => $servProdCaractIndiceCliente,
                'scope'             => $spcScopeAntes->getValor(),
                'esAdicional'       => "NO"
            );

            //desconfigurar la ip adicional
            $respuestaArrayAdicional = $this->cancelarService->cancelarServicioIp($arrParametrosCancel);
            $statusAdicional         = $respuestaArrayAdicional[0]['status'];

            if($statusAdicional == "ERROR")
            {
                $arrayFinal[] = array('status' => "ERROR", 'mensaje' => $respuestaArrayAdicional[0]['mensaje']);
                return $arrayFinal;
            }
            //reservamos ip vieja del plan, para poderla activar
            $ipPlan->setEstado("Cancel");
            $this->emInfraestructura->persist($ipPlan);
            $this->emInfraestructura->flush();
        }

        //cancelar servicios de ips adicionales
        if($flagProdAdicional > 0 && $planCabNuevo->getTipo() != "PYME")
        {
            //recorremos servicios de ip adicionales
            for($i = 0; $i < count($arrayServiciosIpAdicional); $i++)
            {
                $servicioIpAdicional = $arrayServiciosIpAdicional[$i];

                $servProdCaractScopeAdi = $this->servicioGeneral
                                               ->getServicioProductoCaracteristica($servicioIpAdicional, "SCOPE", $productoIp);

                $servProdCaractMacAdi = $this->servicioGeneral
                                             ->getServicioProductoCaracteristica($servicioIpAdicional, "MAC", $producto);

                $servicioTecnicoAdicional = $this->emInfraestructura->getRepository('schemaBundle:InfoServicioTecnico')
                                                 ->findOneByServicioId($servicioIpAdicional->getId());

                $interfaceElementoAdicional = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                   ->find($servicioTecnicoAdicional->getInterfaceElementoClienteId());

                $arrParametrosCancel = array(
                    'servicioTecnico'       => $servicioTecnicoAdicional,
                    'modeloElemento'        => $modeloElemento,
                    'interfaceElemento'     => $interfaceElementoAdicional,
                    'producto'              => $producto,
                    'servicio'              => $servicioIpAdicional,
                    'spcMac'                => $servProdCaractMacAdi->getValor(),
                    'scope'                 => $servProdCaractScopeAdi->getValor(),
                    'esAdicional'           => "SI"
                );

                //cancelar ip adicional (script y base)
                $respuestaArray = $this->cancelarService->cancelarServicioIp($arrParametrosCancel);
                $status  = $respuestaArray[0]['status'];
                $mensaje = $respuestaArray[0]['mensaje'];

                //si falla algo en la cancelacion de ips adicionales
                if($status != "OK")
                {
                    //regresamos el tipo de negocio al original
                    $this->servicioGeneral->setTipoNegocioEnInfoPunto($punto, $planCabViejo->getTipo(), $idEmpresa);

                    $ipPlan->setEstado("Reservada");
                    $this->emInfraestructura->persist($ipPlan);
                    $this->emInfraestructura->flush();

                    $strMacModificada = $this->activarService->cambiarMac($macOnt);
                    
                    //activar ip fija
                    $arrayParametrosIpFija = array(
                        'ipFija'        => $ipPlan->getIp(),
                        'macOnt'        => $strMacModificada,
                        'idDocumento'   => $idDocumentoConfig,
                        'usuario'       => $usuarioConfig,
                        'elementoCnr'   => $elementoCnr
                    );
                    $resultadJsonIpFija = $this->activarService->configurarIpFijaHuawei($arrayParametrosIpFija);
                    $statusIpFija  = $resultadJsonIpFija->status;
                    $mensajeIpFija = $resultadJsonIpFija->mensaje;
                    $respuestaFinal[0]['status']  = $statusIpFija;
                    $respuestaFinal[0]['mensaje'] = $mensajeIpFija;
                    if($statusIpFija != "OK")
                    {
                        $ipPlan->setEstado("Eliminado");
                        $this->emInfraestructura->persist($ipPlan);
                        $this->emInfraestructura->flush();

                        $mensaje = $mensaje . "<br>"
                                            . "Mensaje Activar Ip Anterior:" . $mensajeIpFija;
                    }
                    else
                    {
                        //se activa ip en la base
                        $ipPlan->setEstado("Activo");
                        $this->emInfraestructura->persist($ipPlan);
                        $this->emInfraestructura->flush();
                    }
                    //activar ips adicionales anteriores
                    for($j = ($i + 1); $j >= 1; $j--)
                    {
                        //obtener obj ip viejas
                        $objIpAdicionalAnterior = $arrayIpVieja[$j];

                        //obtener obj servicio ip vieja
                        $servicioIpAdicional = $arrayServiciosIpAdicional[$i];

                        //reservar ip anterior
                        $objIpAdicionalAnterior->setEstado("Reservada");
                        $this->emInfraestructura->persist($objIpAdicionalAnterior);
                        $this->emInfraestructura->flush();

                        $servProdCaractMacAdi = $this->servicioGeneral
                            ->getServicioProductoCaracteristica($servicioIpAdicional, "MAC", $producto);

                        $strMacModificada = $this->activarService->cambiarMac($servProdCaractMacAdi->getValor());
                        
                        //activar ip fija
                        $arrayParametrosIpFija = array(
                            'ipFija'        => $objIpAdicionalAnterior->getIp(),
                            'macOnt'        => $strMacModificada,
                            'idDocumento'   => $idDocumentoConfig,
                            'usuario'       => $usuarioConfig,
                            'elementoCnr'   => $elementoCnr
                        );
                        $resultadJsonIpFija = $this->activarService->configurarIpFijaHuawei($arrayParametrosIpFija);
                        $statusIpFijaAdi = $resultadJsonIpFija->status;
                        $mensajeIpFija   = $resultadJsonIpFija->mensaje;
                        if($statusIpFijaAdi != "OK")
                        {
                            $objIpAdicionalAnterior->setEstado("Eliminado");
                            $this->emInfraestructura->persist($objIpAdicionalAnterior);
                            $this->emInfraestructura->flush();

                            $mensaje = $mensaje . "<br>"
                                . "Mensaje Activar Ip " . $objIpAdicionalAnterior->getIp() . " Anterior:" . $mensajeIpFija;
                        }
                        else
                        {
                            //se activa ip en la base
                            $objIpAdicionalAnterior->setEstado("Activo");
                            $this->emInfraestructura->persist($objIpAdicionalAnterior);
                            $this->emInfraestructura->flush();
                        }
                        //retroceder contador i para obtener los servicios ip adicionales anteriores
                        $i--;
                    }

                    $arrayFinal[] = array('status' => "ERROR",
                                          'mensaje' => "No se puede realizar el cambio de Plan, <br>".
                                                       "Fallo la cancelacion de las ips adicionales<br>".
                                                       "Mensaje:" . $mensaje);
                    return $arrayFinal;
                }//if($status!="OK")
                //se cancela ip en la base
                //obtener ips fijas q tiene el servicio
                $ipsFijasAdicionales = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                            ->findBy(array("servicioId" => $servicioIpAdicional->getId(), "tipoIp" => "FIJA", "estado" => "Activo"));
                $ipFijaAdicional = $ipsFijasAdicionales[0];
                $ipFijaAdicional->setEstado("Cancel");
                $this->emInfraestructura->persist($ipFijaAdicional);
                $this->emInfraestructura->flush();
            }//for($i=0;$i<count($arrayServiciosIpAdicional);$i++)
        }//if($flagProdAdicional>0 && $status=="OK")
        //actualizamos el tipo de negocio
        $this->servicioGeneral->setTipoNegocioEnInfoPunto($punto, $planCabNuevo->getTipo(), $idEmpresa);

        switch($planCabNuevo->getTipo())
        {
            //pyme -> home
            case "HOME":
                $arrParamReconectar = array(
                    'elemento'          => $elemento,
                    'interfaceElemento' => $interfaceElemento,
                    'spcIndiceCliente'  => $servProdCaractIndiceCliente,
                    'spcSpid'           => $spcSpid,
                    'servicioTecnico'   => $servicioTecnico,
                    'spcServiceProfile' => $spcServiceProfile->getValor(),
                    'spcLineProfile'    => $spcLineProfile,
                    'spcVlan'           => $spcVlan,
                    'spcGemPort'        => $spcGemPort,
                    'spcTrafficTable'   => $spcTrafficTable
                );

                $resultadJson = $this->reconectarService->reconectarServicioOltHuawei($arrParamReconectar);

                $respuestaFinal[0]['status']  = $resultadJson->status;
                $respuestaFinal[0]['mensaje'] = $resultadJson->mensaje;
                $status  = $respuestaFinal[0]['status'];
                $mensaje = $respuestaFinal[0]['mensaje'];

                if($status == "OK")
                {
                    //si tiene ip adicional se debe cancelar el servicio adicional
                    if($flagProdAdicional > 0)
                    {
                        for($i = 0; $i < count($arrayServiciosIpAdicional); $i++)
                        {
                            $servicioIpAdicional = $arrayServiciosIpAdicional[$i];
                            if($servicioIpAdicional)
                            {
                                //caracteristicas adicionales
                                $arrayCaracAdicional = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                            ->findBy(array("servicioId" => $servicioIpAdicional->getId(), "estado" => "Activo"));

                                for($j = 0; $j < count($arrayCaracAdicional); $j++)
                                {
                                    $caracAdicional = $arrayCaracAdicional[$j];

                                    //cambiar estado al servicio adicional
                                    $caracAdicional->setEstado("Cancel");
                                    $this->emComercial->persist($caracAdicional);
                                    $this->emComercial->flush();
                                }//for($j=0;$j<count($arrayCaracAdicional);$j++)

                                $strEstadoServicioAdicional = "Cancel";
                                $strObservacionAdicional = "Se cancelo servicio adicional, por cambio de plan";
                                if($servicioIpAdicional->getEstado() == "PreAsignacionInfoTecnica" ||
                                    $servicioIpAdicional->getEstado() == "Asignada")
                                {
                                    $strEstadoServicioAdicional = "Anulado";
                                    $strObservacionAdicional = "Se Anulo servicio adicional, por cambio de plan";
                                }
                                //cambiar estado al servicio adicional
                                $servicioIpAdicional->setEstado($strEstadoServicioAdicional);
                                $this->emComercial->persist($servicioIpAdicional);
                                $this->emComercial->flush();

                                //historial del servicio adicional
                                $servicioHistorial = new InfoServicioHistorial();
                                $servicioHistorial->setServicioId($servicioIpAdicional);
                                $servicioHistorial->setObservacion($strObservacionAdicional);
                                $servicioHistorial->setEstado($strEstadoServicioAdicional);
                                $servicioHistorial->setUsrCreacion($usrCreacion);
                                $servicioHistorial->setFeCreacion(new \DateTime('now'));
                                $servicioHistorial->setIpCreacion($ipCreacion);
                                $this->emComercial->persist($servicioHistorial);
                                $this->emComercial->flush();
                            }//if($servicioIpAdicional)
                        }//for($i=0;$i<count($arrayServiciosIpAdicional);$i++)
                    }//if($flagProdAdicional > 0)

                    return $respuestaFinal;
                }//if($status=="OK")

                break;
            //pyme -> pro
            case "PRO":
                $arrParamReconectar = array(
                    'elemento'          => $elemento,
                    'interfaceElemento' => $interfaceElemento,
                    'spcIndiceCliente'  => $servProdCaractIndiceCliente,
                    'spcSpid'           => $spcSpid,
                    'servicioTecnico'   => $servicioTecnico,
                    'spcServiceProfile' => $spcServiceProfile->getValor(),
                    'spcLineProfile'    => $spcLineProfile,
                    'spcVlan'           => $spcVlan,
                    'spcGemPort'        => $spcGemPort,
                    'spcTrafficTable'   => $spcTrafficTable
                );

                $resultadJson = $this->reconectarService->reconectarServicioOltHuawei($arrParamReconectar);

                $respuestaFinal[0]['status']  = $resultadJson->status;
                $respuestaFinal[0]['mensaje'] = $resultadJson->mensaje;
                $status  = $respuestaFinal[0]['status'];
                $mensaje = $respuestaFinal[0]['mensaje'];

                if($status == "OK")
                {
                    //si tiene ip adicional se debe cancelar el servicio adicional
                    if($flagProdAdicional > 0)
                    {
                        for($i = 0; $i < count($arrayServiciosIpAdicional); $i++)
                        {
                            $servicioIpAdicional = $arrayServiciosIpAdicional[$i];

                            if($servicioIpAdicional)
                            {
                                //caracteristicas adicionales
                                $arrayCaracAdicional = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                            ->findBy(array("servicioId" => $servicioIpAdicional->getId(), "estado" => "Activo"));

                                for($j = 0; $j < count($arrayCaracAdicional); $j++)
                                {
                                    $caracAdicional = $arrayCaracAdicional[$j];

                                    //cambiar estado al servicio adicional
                                    $caracAdicional->setEstado("Cancel");
                                    $this->emComercial->persist($caracAdicional);
                                    $this->emComercial->flush();
                                }//for($j=0;$j<count($arrayCaracAdicional);$j++)

                                $strEstadoServicioAdicional = "Cancel";
                                $strObservacionAdicional    = "Se cancelo servicio adicional, por cambio de plan";
                                if($servicioIpAdicional->getEstado() == "PreAsignacionInfoTecnica" ||
                                    $servicioIpAdicional->getEstado() == "Asignada")
                                {
                                    $strEstadoServicioAdicional = "Anulado";
                                    $strObservacionAdicional    = "Se Anulo servicio adicional, por cambio de plan";
                                }
                                //cambiar estado al servicio adicional
                                $servicioIpAdicional->setEstado($strEstadoServicioAdicional);
                                $this->emComercial->persist($servicioIpAdicional);
                                $this->emComercial->flush();

                                //historial del servicio adicional
                                $servicioHistorial = new InfoServicioHistorial();
                                $servicioHistorial->setServicioId($servicioIpAdicional);
                                $servicioHistorial->setObservacion($strObservacionAdicional);
                                $servicioHistorial->setEstado($strEstadoServicioAdicional);
                                $servicioHistorial->setUsrCreacion($usrCreacion);
                                $servicioHistorial->setFeCreacion(new \DateTime('now'));
                                $servicioHistorial->setIpCreacion($ipCreacion);
                                $this->emComercial->persist($servicioHistorial);
                                $this->emComercial->flush();
                            }//if($servicioIpAdicional)
                        }//for($i=0;$i<count($arrayServiciosIpAdicional);$i++)
                    }//if($flagProdAdicional > 0)

                    return $respuestaFinal;
                }//if($status=="OK")

                break;
            //pyme -> pyme
            case "PYME":
                if($flagProdIpPlanNuevo == 1)
                {
                    $arrParamReconectar = array(
                        'elemento'          => $elemento,
                        'interfaceElemento' => $interfaceElemento,
                        'spcIndiceCliente'  => $servProdCaractIndiceCliente,
                        'spcSpid'           => $spcSpid,
                        'servicioTecnico'   => $servicioTecnico,
                        'spcServiceProfile' => $spcServiceProfile->getValor(),
                        'spcLineProfile'    => $spcLineProfile,
                        'spcVlan'           => $spcVlan,
                        'spcGemPort'        => $spcGemPort,
                        'spcTrafficTable'   => $spcTrafficTable
                    );

                    $resultadJson = $this->reconectarService->reconectarServicioOltHuawei($arrParamReconectar);

                    $respuestaFinal[0]['status']  = $resultadJson->status;
                    $respuestaFinal[0]['mensaje'] = $resultadJson->mensaje;
                    $status  = $respuestaFinal[0]['status'];
                    $mensaje = $respuestaFinal[0]['mensaje'];

                    //si se activa bien el servicio de internet
                    if($status == "OK")
                    {
                        return $respuestaFinal;
                    }//end if status == OK
                }
                else
                {
                    $status = "ERROR";
                    $mensaje = 'El nuevo plan: <b>' . $planCabNuevo->getNombrePlan() . '</b>, <br>'.
                               'No tiene definido el producto IP FIJA, <br> '.
                               'Favor Notificar al Dept. Marketing para que regularice el Plan!';
                }
                break;
        }//switch($planCabNuevo->getTipo())

        if($status != "OK")
        {
            //regresamos el tipo de negocio al original
            $this->servicioGeneral->setTipoNegocioEnInfoPunto($punto, $planCabViejo->getTipo(), $idEmpresa);

            //activar servicio de internet antes
            $arrParamReconectar = array(
                'elemento'          => $elemento,
                'interfaceElemento' => $interfaceElemento,
                'spcIndiceCliente'  => $servProdCaractIndiceCliente,
                'spcSpid'           => $spcSpid,
                'servicioTecnico'   => $servicioTecnico,
                'spcServiceProfile' => $spcServiceProfile->getValor(),
                'spcLineProfile'    => $spcLineProfileAntes,
                'spcVlan'           => $spcVlanAntes,
                'spcGemPort'        => $spcGemPortAntes,
                'spcTrafficTable'   => $spcTrafficTableAntes
            );

            $resultadJson = $this->reconectarService->reconectarServicioOltHuawei($arrParamReconectar);

            $respuestaFinal[0]['status']  = $resultadJson->status;
            $respuestaFinal[0]['mensaje'] = $resultadJson->mensaje;
            $status                     = $respuestaFinal[0]['status'];
            $mensajeActivarPlanAnterior = $respuestaFinal[0]['mensaje'];

            if($status != "OK")
            {
                $mensaje = $mensaje . "<br>Activacion Plan Anterior:" . $mensajeActivarPlanAnterior;
            }

            //Activar Ip fija del plan
            if($planCabNuevo->getTipo() == "HOME" || ($flagProdIpPlanNuevo == 0 && $planCabNuevo->getTipo() == "PRO"))
            {
                //reservar estado de ip del plan
                $ipPlan->setEstado("Reservada");
                $this->emInfraestructura->persist($ipPlan);
                $this->emInfraestructura->flush();

                $strMacModificada = $this->activarService->cambiarMac($macOnt);
                
                //activar ip fija
                $arrayParametrosIpFija = array(
                    'ipFija'        => $ipPlan->getIp(),
                    'macOnt'        => $strMacModificada,
                    'idDocumento'   => $idDocumentoConfig,
                    'usuario'       => $usuarioConfig,
                    'elementoCnr'   => $elementoCnr
                );
                $resultadJsonIpFija         = $this->activarService->configurarIpFijaHuawei($arrayParametrosIpFija);
                $statusActivarIpAnterior    = $resultadJsonIpFija->status;
                $mensajeActivarIpAnterior   = $resultadJsonIpFija->mensaje;
                if($statusActivarIpAnterior != "OK")
                {
                    $ipPlan->setEstado("Cancel");
                    $this->emInfraestructura->persist($ipPlan);
                    $this->emInfraestructura->flush();
                    $mensaje = $mensaje . "<br>".
                                          "Mensaje Activar Ip " . $ipPlan->getIp() . " Anterior:" . $mensajeActivarIpAnterior;
                }
                else
                {
                    //se activa ip en la base
                    $ipPlan->setEstado("Activo");
                    $this->emInfraestructura->persist($ipPlan);
                    $this->emInfraestructura->flush();
                }
            }
            /*
              Aqui se debe configurar plan antiguo y activar ip del plan en caso de que haya sido cancelada
             */
            //activar servicios de ips adicionales antiguos
            if($flagProdAdicional > 0 && $planCabNuevo->getTipo() != "PYME")
            {
                //activar ips adicionales anteriores
                for($j = 0; $j < count($arrayIpVieja); $j++)
                {
                    //obtener obj ip viejas
                    $objIpAdicionalAnterior = $arrayIpVieja[$j];

                    //obtener obj servicio ip vieja
                    $servicioIpAdicional  = $arrayServiciosIpAdicional[$j];

                    $servProdCaractMacAdi = $this->servicioGeneral
                                                 ->getServicioProductoCaracteristica($servicioIpAdicional, "MAC", $producto);

                    //reservamos ip vieja del plan, para poderla activar
                    $objIpAdicionalAnterior->setEstado("Reservada");
                    $this->emInfraestructura->persist($objIpAdicionalAnterior);
                    $this->emInfraestructura->flush();

                    $strMacModificada = $this->activarService->cambiarMac($servProdCaractMacAdi->getValor());
                    
                    //activar ip fija
                    $arrayParametrosIpFija = array(
                        'ipFija'        => $objIpAdicionalAnterior->getIp(),
                        'macOnt'        => $strMacModificada,
                        'idDocumento'   => $idDocumentoConfig,
                        'usuario'       => $usuarioConfig,
                        'elementoCnr'   => $elementoCnr
                    );
                    $resultadJsonIpFija         = $this->activarService->configurarIpFijaHuawei($arrayParametrosIpFija);
                    $statusActivarIpAnterior    = $resultadJsonIpFija->status;
                    $mensajeActivarIpAnterior   = $resultadJsonIpFija->mensaje;
                    if($statusActivarIpAnterior != "OK")
                    {
                        $objIpAdicionalAnterior->setEstado("Cancel");
                        $this->emInfraestructura->persist($objIpAdicionalAnterior);
                        $this->emInfraestructura->flush();
                        $mensaje = $mensaje . "<br>".
                                   "Mensaje Activar Ip " . $objIpAdicionalAnterior->getIp() . " Anterior:" . $mensajeActivarIpAnterior;
                    }
                    else
                    {
                        //se activa ip en la base
                        $objIpAdicionalAnterior->setEstado("Activo");
                        $this->emInfraestructura->persist($objIpAdicionalAnterior);
                        $this->emInfraestructura->flush();
                    }
                }
            }
            $arrayFinal[] = array('status' => "ERROR",
                                  'mensaje' => "No se puede realizar el cambio de Plan, <br>".
                                               "Fallo activacion del plan nuevo:<b>" . $planCabNuevo->getNombrePlan() . "</b><br>".
                                               "Mensaje:" . $mensaje);
            return $arrayFinal;
        }//if($status!="OK")
    }

    /**
     * Funcion que ejecuta un script de verificacion de si
     * la ip ya se encuentra configurada en el olt
     * 
     * @author Creado: Francisco Adum <fadum@telconet.ec>
     * @version 1.0 12-08-2014
     * @param InfoServicioTecnico       $servicioTecnico
     * @param AdmiModeloElemento        $modeloElemento
     * @param String                    $ip
     */
    public function verificarIpFijaConfigurada($servicioTecnico, $modeloElemento, $ip)
    {
        //*OBTENER SCRIPT--------------------------------------------------------*/
        $scriptArray = $this->servicioGeneral->obtenerArregloScript("obtenerPoolParaIpFija",$modeloElemento);
        $idDocumentoPool= $scriptArray[0]->idDocumento;
        $usuario= $scriptArray[0]->usuario;
        $protocolo= $scriptArray[0]->protocolo;
        //*----------------------------------------------------------------------*/
        $comando = "java -jar -Djava.security.egd=file:/dev/./urandom ".$this->pathTelcos."telcos/src/telconet/tecnicoBundle/batch/md_datos.jar '".
            $this->host."' 'verificarIpConfigurada' '".$servicioTecnico->getElementoId()."' '".$usuario."' '".
            $protocolo."' '".$idDocumentoPool."' '".$ip."' '".$this->pathParameters."'";
        $salida= shell_exec($comando);
        $pos = strpos($salida, "{");
        $jsonObj= substr($salida, $pos);
        $resultadJson1 = json_decode($jsonObj);
        
        return $resultadJson1;
    }
    
    /**
     * Funcion que ejecuta un script de activar/eliminar la ip
     * en el sce
     * 
     * @author Creado: Francisco Adum <fadum@telconet.ec>
     * @version 1.1 2-02-2015
     * @param InfoServicio       $servicio
     * @param String             $ip
     * @param String             $accion (activar/eliminar)
     */
    public function ejecutarScriptEnSce($servicio, $ip, $accion)
    {
        //activa ip en el sce
        $comando = "java -jar -Djava.security.egd=file:/dev/./urandom ".$this->pathTelcos."telcos/src/telconet/tecnicoBundle/batch/md_sce.jar '".
            $this->host."' '".$servicio->getId()."' '".$accion."' '".$ip."' '".$this->pathParameters."'";
        $salida= shell_exec($comando);
        $pos = strpos($salida, "{"); 
        $jsonObj= substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);
        
        return $resultadJson;
    }
    
    public function cambioPlanTtco($servicio,$planId,$interfaceElemento,$elemento,$modeloElemento, $capacidad1, $capacidad2, 
                                   $idEmpresa,$usrCreacion, $ipCreacion, $precioViejo, $precioNuevo){
        $planObj = $this->emComercial->getRepository('schemaBundle:InfoPlanCab')->find($planId);
        $nombreModeloElemento = $modeloElemento->getNombreModeloElemento();
        $nombreInterfaceElemento = $interfaceElemento->getNombreInterfaceElemento();
        
//        if($nombreModeloElemento=="411AH" || $nombreModeloElemento=="433AH"){
//            $nombreModeloElemento = "RADIUS";
//        }
        
        if($modeloElemento->getReqAprovisionamiento()=="SI"){
            if($nombreModeloElemento!="411AH" && $nombreModeloElemento!="433AH"){
                //*OBTENER SCRIPT--------------------------------------------------------*/
                $scriptArray = $this->servicioGeneral->obtenerArregloScript("cambioVelocidad",$modeloElemento);
                $idDocumento= $scriptArray[0]->idDocumento;
                $usuario= $scriptArray[0]->usuario;
                $protocolo= $scriptArray[0]->protocolo;
                //*----------------------------------------------------------------------*/
                
                if($idDocumento==0){
                    $respuestaArray[] = array('status'=>'ERROR', 'mensaje'=>'NO EXISTE TAREA');
                    return $respuestaArray;
                }
            }
            
            $caracteristica1 = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                    ->findOneBy(array( "descripcionCaracteristica" => "CAPACIDAD1", "estado" => "Activo"));
            $caracteristica2 = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                    ->findOneBy(array( "descripcionCaracteristica" => "CAPACIDAD2", "estado" => "Activo"));

            $capProm1 = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                            ->findOneBy(array( "descripcionCaracteristica" => "CAPACIDAD-PROM1", "estado"=>"Activo"));
            $capProm2 = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                            ->findOneBy(array( "descripcionCaracteristica" => "CAPACIDAD-PROM2", "estado"=>"Activo"));
            $capInt1 = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                            ->findOneBy(array( "descripcionCaracteristica" => "CAPACIDAD-INT1", "estado"=>"Activo"));
            $capInt2 = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                            ->findOneBy(array( "descripcionCaracteristica" => "CAPACIDAD-INT2", "estado"=>"Activo"));

            $producto = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                            ->findOneBy(array( "nombreTecnico" => "INTERNET","empresaCod"=>$idEmpresa, "estado" => "Activo"));

            $prodCaracteristica1 = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                    ->findOneBy(array( "productoId" => $producto->getId(), "caracteristicaId" => $caracteristica1->getId()));
            $prodCaracteristica2 = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                    ->findOneBy(array( "productoId" => $producto->getId(), "caracteristicaId" => $caracteristica2->getId()));
            $pcProm1 = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')->findOneBy(array( "productoId" => $producto->getId(), "caracteristicaId"=>$capProm1, "estado"=>"Activo"));
            $pcProm2 = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')->findOneBy(array( "productoId" => $producto->getId(), "caracteristicaId"=>$capProm2, "estado"=>"Activo"));
            $pcInt1 = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')->findOneBy(array( "productoId" => $producto->getId(), "caracteristicaId"=>$capInt1, "estado"=>"Activo"));
            $pcInt2 = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')->findOneBy(array( "productoId" => $producto->getId(), "caracteristicaId"=>$capInt2, "estado"=>"Activo"));
            
            if($nombreModeloElemento=="6524"){
                $datos = $nombreInterfaceElemento.",".$capacidad2.",".$capacidad1;

                $resultadJson = $this->cambioVelocidad6524($idDocumento, $usuario, $protocolo, $elemento, $datos);
            }
            else if($nombreModeloElemento=="7224"){
                $datos = $nombreInterfaceElemento.",".$capacidad2.",".$capacidad1;
                $resultadJson = $this->cambioVelocidad7224($idDocumento, $usuario, $protocolo, $elemento, $datos);
            }
            else if($nombreModeloElemento=="R1AD24A"){
                $datos = $nombreInterfaceElemento.",".$capacidad2.",".$capacidad1;
                $resultadJson = $this->cambioVelocidadR1AD24A($idDocumento, $usuario, $protocolo, $elemento, $datos);

            }
            else if($nombreModeloElemento=="R1AD48A"){
                $datos = $nombreInterfaceElemento.",".$capacidad2.",".$capacidad1;
                $resultadJson = $this->cambioVelocidadR1AD48A($idDocumento, $usuario, $protocolo, $elemento, $datos);
            }
            else if($nombreModeloElemento=="A2024"){
                $flag=0;
                $detalleElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                        ->findBy(array( "elementoId" => $elemento, "detalleNombre"=>"PERFIL"));
                for($i=0;$i<count($detalleElemento);$i++){
                    $detalleCaracteristica1 = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleCaracteristica')
                                                   ->findOneBy(array("detalleElementoId" => $detalleElemento[$i], 
                                                                     "caracteristicaId"=>$caracteristica1->getId(),
                                                                     "descripcionCaracteristica" => $capacidad1));
                    $detalleCaracteristica2 = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleCaracteristica')
                                                   ->findOneBy(array("detalleElementoId" => $detalleElemento[$i], 
                                                                     "caracteristicaId"=>$caracteristica2->getId(), 
                                                                     "descripcionCaracteristica" => $capacidad2));

                    if($detalleCaracteristica1!=null && $detalleCaracteristica2!=null){
                        if($detalleCaracteristica1->getDetalleElementoId() == $detalleCaracteristica2->getDetalleElementoId()){
                            $valor = $detalleCaracteristica1->getValorCaracteristica();
                            $valor1 = explode("\r",$valor);
                            $flag=1;
                            break;
                        }
                    }
                }

                if($flag!=1){
                    $respuestaArray[] = array('status'=>'ERROR', 'mensaje'=>'NO EXISTE PERFIL');
                    return $respuestaArray;
                }

                $datos = $nombreInterfaceElemento.",".$valor1[0];
                $resultadJson = $this->cambioVelocidadA2024($idDocumento, $usuario, $protocolo, $elemento,$datos);
            }
            else if($nombreModeloElemento=="A2048"){
                $flag=0;
                $detalleElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                        ->findBy(array( "elementoId" => $elemento, "detalleNombre"=>"PERFIL"));
                for($i=0;$i<count($detalleElemento);$i++){
                    $detalleCaracteristica1 = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleCaracteristica')
                                                   ->findOneBy(array("detalleElementoId" => $detalleElemento[$i], 
                                                                     "caracteristicaId"=>$caracteristica1->getId(),
                                                                     "descripcionCaracteristica" => $capacidad1));
                    $detalleCaracteristica2 = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleCaracteristica')
                                                   ->findOneBy(array("detalleElementoId" => $detalleElemento[$i], 
                                                                     "caracteristicaId"=>$caracteristica2->getId(), 
                                                                     "descripcionCaracteristica" => $capacidad2));
                    if($detalleCaracteristica1!=null && $detalleCaracteristica2!=null){
                        if($detalleCaracteristica1->getDetalleElementoId() == $detalleCaracteristica2->getDetalleElementoId()){
                            $valor = $detalleCaracteristica1->getValorCaracteristica();
                            $valor1 = explode("\r",$valor);
                            $flag=1;
                            break;
                        }
                    }
                }

                if($flag!=1){
                    $respuestaArray[] = array('status'=>'ERROR', 'mensaje'=>'NO EXISTE PERFIL');
                    return $respuestaArray;
                }

                $datos = $nombreInterfaceElemento.",".$valor1[0];
                $resultadJson = $this->cambioVelocidadA2048($idDocumento, $usuario, $protocolo, $elemento, $datos);
            }
            else if($nombreModeloElemento=="MEA1"){
                $flag=0;
                $detalleElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                    ->findBy(array( "elementoId" => $elemento, "detalleNombre"=>"PERFIL"));
                for($i=0;$i<count($detalleElemento);$i++){
                    $detalleCaracteristica1 = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleCaracteristica')
                                                   ->findOneBy(array("detalleElementoId" => $detalleElemento[$i], 
                                                                     "caracteristicaId"=>$caracteristica1->getId(),
                                                                     "descripcionCaracteristica" => $capacidad1));
                    $detalleCaracteristica2 = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleCaracteristica')
                                                   ->findOneBy(array("detalleElementoId" => $detalleElemento[$i], 
                                                                     "caracteristicaId"=>$caracteristica2->getId(), 
                                                                     "descripcionCaracteristica" => $capacidad2));
                    if($detalleCaracteristica1!=null && $detalleCaracteristica2!=null){
                        if($detalleCaracteristica1->getDetalleElementoId() == $detalleCaracteristica2->getDetalleElementoId()){
                            $valor = $detalleCaracteristica1->getValorCaracteristica();
                            $valor1 = explode("\r",$valor);
                            $flag=1;
                            break;
                        }
                    }

                }

                if($flag!=1){
                    $respuestaArray[] = array('status'=>'ERROR', 'mensaje'=>'NO EXISTE PERFIL');
                    return $respuestaArray;
                }

                $datos = $nombreInterfaceElemento.",".$valor1[0];
                $resultadJson = $this->cambioVelocidadMea1($idDocumento, $usuario, $protocolo, $elemento, $datos);
            }
            else if($nombreModeloElemento=="MEA3"){
                $flag=0;
                $detalleElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                    ->findBy(array( "elementoId" => $elemento, "detalleNombre"=>"PERFIL"));
                for($i=0;$i<count($detalleElemento);$i++){
                    $detalleCaracteristica1 = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleCaracteristica')
                                                   ->findOneBy(array("detalleElementoId" => $detalleElemento[$i], 
                                                                     "caracteristicaId"=>$caracteristica1->getId(),
                                                                     "descripcionCaracteristica" => $capacidad1));
                    $detalleCaracteristica2 = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleCaracteristica')
                                                   ->findOneBy(array("detalleElementoId" => $detalleElemento[$i], 
                                                                     "caracteristicaId"=>$caracteristica2->getId(), 
                                                                     "descripcionCaracteristica" => $capacidad2));
                    if($detalleCaracteristica1!=null && $detalleCaracteristica2!=null){
                        if($detalleCaracteristica1->getDetalleElementoId() == $detalleCaracteristica2->getDetalleElementoId()){
                            $valor = $detalleCaracteristica1->getValorCaracteristica();
                            $valor1 = explode("\r",$valor);
                            $flag=1;
                            break;
                        }
                    }

                }

                if($flag!=1){
                    $respuestaArray[] = array('status'=>'ERROR', 'mensaje'=>'NO EXISTE PERFIL');
                    return $respuestaArray;
                }

                $datos = $nombreInterfaceElemento.",".$valor1[0];
                $resultadJson = $this->cambioVelocidadMea3($idDocumento, $usuario, $protocolo, $elemento, $datos);
            }
            else if($nombreModeloElemento=="IPTECOM" || $nombreModeloElemento=="411AH" || $nombreModeloElemento=="433AH" || $nombreModeloElemento=="RADIUS"){
                //base
                $puntoId = $servicio->getPuntoId();
                $punto = $this->emComercial->getRepository('schemaBundle:InfoPunto')->find($puntoId);
                $login = $punto->getLogin();

                $datos = "";
    //            $resultadJson1 = $this->cambioVelocidadIPTECOM($idDocumento, $usuario, "radio", $elementoId, $datos);

                //servidor
                $datos1 = $login.",".$capacidad1.",".$capacidad2;
                
                $elementoIdRadius = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->findOneBy(array( "nombreElemento" => "ttcoradius"));

                //*OBTENER SCRIPT--------------------------------------------------------*/
                $scriptArray = $this->servicioGeneral->obtenerArregloScriptGeneral("cambioVelocidadRADIUS",$elementoIdRadius->getModeloElementoId());
                $idDocumento= $scriptArray[0]->idDocumento;
                $usuario= $scriptArray[0]->usuario;
                $protocolo= $scriptArray[0]->protocolo;
                //*----------------------------------------------------------------------*/
                
                $resultadJson = $this->cambioVelocidadRADIUS($idDocumento, $usuario, "servidor", $elementoIdRadius, $datos1);
    //            print($resultadJson->status);
    //            die();
                $status=$resultadJson->status;
                
                if($status=="OK"){
                    $status="OK";
                }
                else{
                    $status="ERROR";
                }
            }
            if($nombreModeloElemento!="IPTECOM" && $nombreModeloElemento!="411AH" && $nombreModeloElemento!="433AH" && $nombreModeloElemento!="RADIUS"){
                $status=$resultadJson->status;
            }

            $flagSCE=0;
            if(stristr($planObj->getDescripcionPlan(), "VDSL") === FALSE) {
                $flagSCE=0;
            }
            else{
                $flagSCE=1;
            }

            if($status=="OK"){
                if($flagSCE==1){
                    $resultadJsonSce = $this->activarClienteSCE($servicio->getId(), "activar");
                    $statusSce = $resultadJsonSce->status;
                    if($statusSce=="OK"){
                        $status="OK";
                    }
                    else if($statusSce=="ERROR"){
                        $status="ERROR SCE";
                    }
                }
            }
        }
        
        if($status=="OK" || $modeloElemento->getReqAprovisionamiento()=="NO"){
            $planDetOjb = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                    ->findOneBy(array( "planId" => $planObj, "productoId"=>$producto->getId()));
            $planProdCaract = $this->emComercial->getRepository('schemaBundle:InfoPlanProductoCaract')
                                   ->findBy(array( "planDetId" => $planDetOjb->getId()));
            
            //eliminar viejas caracteristicas
            $servicioProdCaract = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                        ->findBy(array( "servicioId" => $servicio->getId(), "estado"=>"Activo"));
            for($j=0;$j<count($servicioProdCaract);$j++){
                $spc = $servicioProdCaract[$j];
                if($spc->getProductoCaracterisiticaId() == $prodCaracteristica1->getId() || $spc->getProductoCaracterisiticaId() == $prodCaracteristica2->getId() || 
                   $spc->getProductoCaracterisiticaId() == $pcProm1->getId() || $spc->getProductoCaracterisiticaId() == $pcProm2->getId() || 
                   $spc->getProductoCaracterisiticaId() == $pcInt1->getId() || $spc->getProductoCaracterisiticaId() == $pcInt2->getId()){
                    
                    $spc->setEstado("Eliminado");
                    $this->emComercial->persist($spc);
                    $this->emComercial->flush();
                }
            }
            
            //crear nuevas caracteristicas
            for($j=0;$j<count($planProdCaract);$j++){
                $ppc = $planProdCaract[$j];
                
                $spc = new InfoServicioProdCaract();
                $spc->setServicioId($servicio->getId());
                $spc->setProductoCaracterisiticaId($ppc->getProductoCaracterisiticaId());
                $spc->setValor($ppc->getValor());
                $spc->setEstado("Activo");
                $spc->setUsrCreacion($usrCreacion);
                $spc->setFeCreacion(new \DateTime('now'));
                $spc->setUsrUltMod($usrCreacion);
                $spc->setFeUltMod(new \DateTime('now'));
                $this->emComercial->persist($spc);
                $this->emComercial->flush();
                
            }
            
            //historial del servicio
            $servicioHistorial = new InfoServicioHistorial();
            $servicioHistorial->setServicioId($servicio);
            $servicioHistorial->setObservacion("Se cambio de plan, plan anterior:".$servicio->getPlanId());
            $servicioHistorial->setEstado("Activo");
            $servicioHistorial->setUsrCreacion($usrCreacion);
            $servicioHistorial->setFeCreacion(new \DateTime('now'));
            $servicioHistorial->setIpCreacion($ipCreacion);
            $this->emComercial->persist($servicioHistorial);
            $this->emComercial->flush();
            
            //servicio
            $servicio->setPlanId($planObj);
            if($precioNuevo>0){
                if($precioNuevo > $precioViejo){
                    $servicio->setPrecioVenta($precioNuevo);
                }
                else{
                    $servicio->setPrecioVenta($precioViejo);
                }
            }
            else{
                $servicio->setPrecioVenta($precioViejo);
            }
            $this->emComercial->persist($servicio);
            $this->emComercial->flush();
                        
            
            if($nombreModeloElemento=="A2024" || $nombreModeloElemento=="A2048" || $nombreModeloElemento=="MEA1" || $nombreModeloElemento=="MEA3"){
                $infoDetalleElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleInterface')
                                        ->findOneBy(array( "interfaceElementoId" => $interfaceElemento->getId(), "detalleNombre"=>"PERFIL"));
                
                if($infoDetalleElemento!=null){
                    $infoDetalleElemento->setDetalleValor($valor1[0]);
                    $this->emInfraestructura->persist($infoDetalleElemento);
                }
                else{
                    $infoDetalleElemento= new InfoDetalleInterface();
                    $infoDetalleElemento->setDetalleNombre("PERFIL");
                    $infoDetalleElemento->setInterfaceElementoId($interfaceElemento);
                    $infoDetalleElemento->setUsrCreacion($usrCreacion);
                    $infoDetalleElemento->setFeCreacion(new \DateTime('now'));
                    $infoDetalleElemento->setIpCreacion($ipCreacion);
                    $this->emInfraestructura->persist($infoDetalleElemento);
                }
                
                $this->emInfraestructura->flush();
            }
            
            
            $arrayFinal[] = array('status'=>"OK", 'mensaje'=>"OK");
            return $arrayFinal;
        }
        else if($status=="ERROR"){
            $arrayFinal[] = array('status'=>"ERROR", 'mensaje'=>"NO SE PUDO ACTIVAR EL PLAN NUEVO");
            return $arrayFinal;
        }
    }
    
    /**
     * Funcion que sirve para realizar el cambio de velocidad
     * a un cliente que se encuentra en un dslam
     * de modelo A2024
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function cambioVelocidadA2024($idDocumento, $usuario, $protocolo, $elementoId, $datos)
    {
        $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId->getId(), $datos);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve para realizar el cambio de velocidad
     * a un cliente que se encuentra en un dslam
     * de modelo A2048
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function cambioVelocidadA2048($idDocumento, $usuario, $protocolo, $elementoId, $datos)
    {
        $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId->getId(), $datos);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve para realizar el cambio de velocidad
     * a un cliente que se encuentra en un dslam
     * de modelo R1AD24A
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function cambioVelocidadR1AD24A($idDocumento, $usuario, $protocolo, $elementoId, $datos)
    {
        $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId->getId(), $datos);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve para realizar el cambio de velocidad
     * a un cliente que se encuentra en un dslam
     * de modelo R1AD48A
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function cambioVelocidadR1AD48A($idDocumento, $usuario, $protocolo, $elementoId, $datos)
    {
        $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId->getId(), $datos);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve para realizar el cambio de velocidad
     * a un cliente que se encuentra en un dslam
     * de modelo 6524
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function cambioVelocidad6524($idDocumento, $usuario, $protocolo, $elementoId, $datos)
    {
        $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId->getId(), $datos);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve para realizar el cambio de velocidad
     * a un cliente que se encuentra en un dslam
     * de modelo 7224
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function cambioVelocidad7224($idDocumento, $usuario, $protocolo, $elementoId, $datos)
    {
        $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId->getId(), $datos);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve para realizar el cambio de velocidad
     * a un cliente que se encuentra en un dslam
     * de modelo MEA1
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function cambioVelocidadMea1($idDocumento, $usuario, $protocolo, $elementoId, $datos)
    {
        $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId->getId(), $datos);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve para realizar el cambio de velocidad
     * a un cliente que se encuentra en un dslam
     * de modelo MEA3
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function cambioVelocidadMea3($idDocumento, $usuario, $protocolo, $elementoId, $datos)
    {
        $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId->getId(), $datos);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve para realizar el cambio de velocidad
     * a un cliente que se encuentra en un radio IPTECOM
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function cambioVelocidadIPTECOM($idDocumento, $usuario, $tipo, $elementoId, $datos)
    {
        $salida = $this->servicioGeneral->ejecutarComandoRadio($idDocumento, $usuario, $tipo, $elementoId->getId(), $datos);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve para realizar el cambio de velocidad
     * a un cliente que se encuentra en el servidor RADIUS
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function cambioVelocidadRADIUS($idDocumento, $usuario, $tipo, $elementoId, $datos)
    {
        $salida = $this->servicioGeneral->ejecutarComandoRadio($idDocumento, $usuario, $tipo, $elementoId->getId(), $datos);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }
    
    /**
     * Método que extrae las caracteristicas que son requeridas por las plantillas de RDA para cambio de plan.
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.0 08-07-2019
     *
     * @param type $arrayParametrosCaractHuawei[
     *                                           'objPlan'                       Object     Objeto que hace referencia al plan
     *                                           'objServicio'                   Object     Objeto Servicio.
                                                 'intIdElementoOlt'              Int        Id elemento a extraer caracteristicas.
                                                 'strCodEmpresa'                 String     Código de la empresa.
                                                 'strTipoNegocioGetProdCaract'   String     Tipo de negocio.
                                                 'strPerfilEquivalenteNuevo'     String     Perfil nuevo.
     * @return array $arrayRespuesta[ 'mensaje'     string      Mensaje de Consulta exitosa o error.
     *                                'status'      int         Codigo 200/500.
     *                                'data'        array       Contiene las caracteristicas del servicio.
     *                              ]
     */
    public function getParametrosElementoCaractHuawei($arrayParametrosCaractHuawei)
    {
        $arrayRespuesta = array();
        try
        {
            if(is_object($arrayParametrosCaractHuawei['objPlan']))
            {
                $strPlanCaracUltraV = $this->recursosRed->getCaracteristicaPorPlan($arrayParametrosCaractHuawei['objPlan']->getId());
            }
            else
            {
                $strPlanCaracUltraV = "NO";
            }
            $arrayProductoCaracteristicas = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                               ->getProductoCaracteristica($arrayParametrosCaractHuawei['strCodEmpresa'],
                                                                                           $arrayParametrosCaractHuawei['intIdElementoOlt'],
                                                                                           $arrayParametrosCaractHuawei['strPerfilEquivalenteNuevo'],
                                                                                           $arrayParametrosCaractHuawei['strTipoNegocioGetProdCaract'],
                                                                                           $strPlanCaracUltraV
                                                                                          );
            if(is_object($arrayParametrosCaractHuawei['objServicio']))
            {
                foreach($arrayProductoCaracteristicas as $arrayProdCaract)
                {
                    $objAdmiProductoCaracteristica = $this->emComercial
                                                          ->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                          ->findOneById($arrayProdCaract['ID_PRODUCTO_CARACTERISITICA']);
                    if(is_object($objAdmiProductoCaracteristica) &&
                                    $objAdmiProductoCaracteristica->getProductoId()->getDescripcionProducto() ===
                                    $arrayParametrosCaractHuawei['objServicio']->getProductoId()->getDescripcionProducto())
                    {
                        $arrayAdProCarac[] = array(
                                                    'ID_PRODUCTO_CARACTERISITICA' => $objAdmiProductoCaracteristica->getId(),
                                                    'DESCRIPCION_CARACTERISTICA'  => $arrayProdCaract
                                                                                     ['DESCRIPCION_CARACTERISTICA'],
                                                    'DETALLE_VALOR'               => $arrayProdCaract['DETALLE_VALOR']
                                                  );
                    }
                }
            }
            $arrayData      = array (
                                     'gem-port'      => $arrayAdProCarac[0]['DETALLE_VALOR'],
                                     'line-profile'  => $arrayAdProCarac[1]['DETALLE_VALOR'],
                                     'traffic-table' => $arrayAdProCarac[2]['DETALLE_VALOR'],
                                     'vlan'          => $arrayAdProCarac[3]['DETALLE_VALOR']
                                    );
            $arrayRespuesta = array(
                                    'mensaje' => 'OK',
                                    'status'  => 200,
                                    'data'    => $arrayData);

        }
        catch (\Exception $ex)
        {
            $arrayRespuesta = array('mensaje' => $ex->getMessage(),
                                    'status'  => 500);
        }
        return $arrayRespuesta;
    }

    /**
     * Funcion que sirve para obtener parametros necesarios en la Activacion/Cancelacion de servicios en equipos Huawei
     * 
     * @param Entity  $entityPlan entidad del plan del cual se obtendran los parametros
     * @param Integer $idEmpresa
     * @param Integer $idElemento  id de la entidad del elemento Olt del cliente
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 04-05-2015
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 23-11-2015 Se modifica forma de obtener parametros por nuevos planes Ultra Velocidad
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 06-03-2018 Se valida que exista un plan para obtener si es o no ultra velocidad
     */
    public function getParametrosElementoHuawei( $entityPlan, $idEmpresa, $idElemento, $tipoPlan, $perfilNuevo )
    {
        try
        {
            if(is_object($entityPlan))
            {
                $strPlanCaracUltraV = $this->recursosRed->getCaracteristicaPorPlan($entityPlan->getId());
            }
            else
            {
                $strPlanCaracUltraV = "NO";
            }
            $arrayProductoCaracteristicas = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                               ->getProductoCaracteristica($idEmpresa,
                                                                                           $idElemento,
                                                                                           $perfilNuevo,
                                                                                           $tipoPlan,
                                                                                           $strPlanCaracUltraV
                                                                                          );
            $arrayRespuesta = array (
                                    'gem-port'      => $arrayProductoCaracteristicas[0]['DETALLE_VALOR'],                                    
                                    'line-profile'  => $arrayProductoCaracteristicas[1]['DETALLE_VALOR'],
                                    'traffic-table' => $arrayProductoCaracteristicas[2]['DETALLE_VALOR'],
                                    'vlan'          => $arrayProductoCaracteristicas[3]['DETALLE_VALOR']
                                    );
            return $arrayRespuesta;
        }
        catch(\Exception $e)
        {
            $mensajeError = "Error: " . $e->getMessage();
            error_log($mensajeError);
            return null;
        }
        
    }

    /**
     * Función que realiza el cambio de velocidad para los servicios Internet Small Business
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 05-03-2018
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 05-06-2018 Se realizan las validaciones necesarias para contemplar cambios de velocidad de servicios Small Business
     *                         con o sin Ips con olt Huawei o Tellion 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 06-03-2019 Se agrega nombre técnico para obtener correctamente el valor para el parámetro MAPEO_VELOCIDAD_PERFIL
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 26-04-2019 Se corrige envío de parámetro tipo de negocio a la función getParametrosElementoHuawei, que 
     *                          para el caso de servicios Small Business, debe enviar la concatenación del tipo de negocio seguido de un '|'
     *                          y el nombre técnico del producto
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.4 09-07-2019 Se agrega nueva lógica para obtener las caracteristicas de un olt Huawei.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.5 05-05-2020 Se agrega la invocación de la función obtenerParametrosProductosTnGpon para la obtención
     *                          de productos ips Small Business
     * 
     * @author David León <mdleon@telconet.ec>
     * @version 1.6 03-07-2020 Se agrega Flujo para cambio de plan TelcoHome.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.7 25-08-2020 - Se agrega flujo para cambio de plan en la tecnología ZTE para productos Internet Small Business y TeloHome.
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.8 10-03-2022 - Se agrega flujo para cambio de plan Internet Safe.
     */
    public function cambioVelocidadIsb($arrayParametros)
    {
        $strOpcion              = $arrayParametros['strOpcion'] ? $arrayParametros['strOpcion'] : "";
        $strVelocidadNueva      = $arrayParametros["strVelocidadNueva"];
        $strVelocidadAnterior   = $arrayParametros["strVelocidadAnterior"];
        $strCodEmpresa          = $arrayParametros['strCodEmpresa'];
        $strPrefijoEmpresa      = $arrayParametros['strPrefijoEmpresa'];
        $strUsrCreacion         = $arrayParametros['strUsrCreacion'];
        $strIpCreacion          = $arrayParametros['strIpCreacion'];
        $floatPrecioNuevo       = $arrayParametros['floatPrecioNuevo'] > 0 ? $arrayParametros['floatPrecioNuevo'] : 0;
        $strEjecutaScriptsOlt   = "SI";
        $strMensaje             = "";
        $strCapacidad1Nueva     = "";
        $strCapacidad2Nueva     = "";
        $strCapacidad1Actual    = "";
        $strCapacidad2Actual    = "";
        
        try
        {
            if($strOpcion === "GRID")
            {
                $objElementoOlt         = $arrayParametros['objElementoOlt'];
                $objModeloOlt           = $arrayParametros['objModeloOlt'];
                $objServicio            = $arrayParametros['objServicio'];
                $objServicioTecnico     = $arrayParametros['objServicioTecnico'];
                $objInterfaceOlt        = $arrayParametros['objInterfaceOlt'];
            }
            else
            {
                $intIdServicio          = $arrayParametros['intIdServicio'] > 0 ? $arrayParametros['intIdServicio'] : 0;
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

                $objInterfaceOlt    = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')->find($intIdInterfaceElementoOlt);
                if(!is_object($objInterfaceOlt))
                {
                    throw new \Exception("No se ha podido obtener la interface del OLT. <br>Por favor Revisar!");
                }

                $objElementoOlt = $objInterfaceOlt->getElementoId();
                if(!is_object($objElementoOlt))
                {
                    throw new \Exception("No se ha podido obtener el OLT. <br>Por favor Revisar!");
                }

                $objModeloOlt = $objElementoOlt->getModeloElementoId();
                if(!is_object($objModeloOlt))
                {
                    throw new \Exception("No se ha podido obtener el modelo del OLT. <br>Por favor Revisar!");
                }
            }

            //Validar parámetros de la función
            if( !is_object($objElementoOlt) || !is_object($objModeloOlt) || !is_object($objServicio) || !is_object($objServicioTecnico) 
                || !is_object($objInterfaceOlt) || empty($strVelocidadNueva) 
                || empty($strCodEmpresa) || empty($strPrefijoEmpresa) || empty($strUsrCreacion) || empty($strIpCreacion) || $floatPrecioNuevo === 0)
            {
                throw new \Exception("No se han enviado los parámetros necesarios. <br>Por favor Revisar!");
            }
            $objProductoIsb = $objServicio->getProductoId();
            if(!is_object($objProductoIsb))
            {
                throw new \Exception("No se ha podido obtener el producto de Internet. <br>Por favor Revisar!");
            }
            $arrayProductoIpsb  = array();
            $intIdProductoSb    = $objProductoIsb->getId();
            $strNombreTecnicoSb = $objProductoIsb->getNombreTecnico();
            $strDescripcionSb   = $objProductoIsb->getDescripcionProducto();
            if($strNombreTecnicoSb === "INTERNET SMALL BUSINESS")
            {
                $arrayParamsInfoProds   = array("strValor1ParamsProdsTnGpon"    => "PRODUCTOS_RELACIONADOS_INTERNET_IP",
                                                "strCodEmpresa"                 => $strCodEmpresa,
                                                "intIdProductoInternet"         => $intIdProductoSb);
                $arrayInfoMapeoProds    = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                            ->obtenerParametrosProductosTnGpon($arrayParamsInfoProds);
                if(isset($arrayInfoMapeoProds) && !empty($arrayInfoMapeoProds))
                {
                    foreach($arrayInfoMapeoProds as $arrayInfoProd)
                    {
                        $intIdProductoIp        = $arrayInfoProd["intIdProdIp"];
                        $objProdIPSB            = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($intIdProductoIp);
                        $arrayProductoIpsb[]    = $objProdIPSB;
                    }
                }
                else
                {
                    throw new \Exception("No se ha podido obtener el producto Ip. <br>Por favor Revisar!");
                }
            }
            $intIdElementoOlt           = $objElementoOlt->getId();
            $objIpElementoOlt           = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                                  ->findOneBy(array('elementoId' => $intIdElementoOlt, 'estado' => 'Activo'));
            $objDetElementoMiddleware   = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                  ->findOneBy(array("elementoId"    => $intIdElementoOlt,
                                                                                    "detalleNombre" => 'MIDDLEWARE',
                                                                                    "estado"        => 'Activo'));
            $boolFlagMiddleware         = false;
            if(is_object($objDetElementoMiddleware) && $objDetElementoMiddleware->getDetalleValor() == 'SI')
            {
                $boolFlagMiddleware = true;
            }

            if(!$boolFlagMiddleware)
            {
                throw new \Exception("El olt del servicio que se desea cambiar no está permitido para el Middleware");
            }

            if (!is_object($objModeloOlt))
            {
                throw new \Exception("No se ha podido obtener el modelo del olt del servicio");
            }
            else
            {
                $objMarcaOlt                = $objModeloOlt->getMarcaElementoId();
                if(is_object($objMarcaOlt))
                {
                    $strNombreMarcaOlt = $objMarcaOlt->getNombreMarcaElemento();
                }
                else
                {
                    throw new \Exception("No se ha podido obtener la marca del olt");
                }
            }
            $strNombreModeloOlt     = $objModeloOlt->getNombreModeloElemento();
            $strAprovisionamiento   = $this->recursosRed->geTipoAprovisionamiento($intIdElementoOlt);
            //Consultar si el olt tiene aprovisionamiento de ips en el CNR
            $objDetOltMigradoCnr    = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                              ->findOneBy(array('detalleNombre' => 'OLT MIGRADO CNR',
                                                                                'elementoId'    => $intIdElementoOlt));
            if(is_object($objDetOltMigradoCnr))
            {
                $strOltMigradoCnr = "SI";
            }
            else
            {
                $strOltMigradoCnr = "NO";
            }
            //Se agrega validación de la marca del elemento para bloqueo de cambio de planes TELLION CNR
            if ($strAprovisionamiento === "CNR" && $strOltMigradoCnr === "NO" && $strNombreMarcaOlt !== "HUAWEI" && $strNombreMarcaOlt !== "ZTE")
            {
                throw new \Exception("Cambio de Plan no permitido por migracion de Olt");
            }
            $objSpcTipoNegocio  = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "Grupo Negocio", $objProductoIsb);
            if(is_object($objSpcTipoNegocio))
            {
                $strTipoNegocio = $objSpcTipoNegocio->getValor();
                if($strTipoNegocio !== "PYMETN")
                {
                    if(!empty($strNombreTecnicoSb) && $strNombreTecnicoSb==="TELCOHOME" && $strDescripcionSb==="TelcoHome" 
                        && $strTipoNegocio==="HOMETN")
                    {
                        $strTipoNegocioMiddleware = "HOME";
                        $intIpsFijasActivas = 0;
                        $strIpFija = 0;
                    }
                    else
                    {
                        throw new \Exception("No existe un flujo definido de ".$strDescripcionSb." con el grupo de negocio ".$strTipoNegocio);
                    }
                }
                else
                {
                    $strTipoNegocioMiddleware = "PYME";
                }
            }
            else
            {
                throw new \Exception("No existe grupo de negocio asociado a este servicio");
            }
            
            //Obtener el perfil equivalente de la nueva velocidad
            $strPerfilEquivalenteNuevo  = "";
            $arrayMapeoVelocidadPerfil  = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                          ->getOne('MAPEO_VELOCIDAD_PERFIL', 
                                                                   '', 
                                                                   '', 
                                                                   '', 
                                                                   $strVelocidadNueva, 
                                                                   '', 
                                                                   $strNombreTecnicoSb, 
                                                                   '', 
                                                                   '', 
                                                                   $strCodEmpresa);
            if( isset($arrayMapeoVelocidadPerfil['valor2'])  && !empty($arrayMapeoVelocidadPerfil['valor2']) )
            {
                $strPerfil  = $arrayMapeoVelocidadPerfil['valor2'];
                if ( $strNombreMarcaOlt === "HUAWEI" || ($strAprovisionamiento == "CNR" && $strOltMigradoCnr == "SI"))
                {
                    $arrayParamsPerfilEquiv                             = array();
                    $arrayParamsPerfilEquiv['elementoOltId']            = $intIdElementoOlt;
                    $arrayParamsPerfilEquiv['idPlan']                   = null;
                    $arrayParamsPerfilEquiv['valorPerfil']              = $strPerfil;
                    $arrayParamsPerfilEquiv['tipoAprovisionamiento']    = $strAprovisionamiento;
                    $arrayParamsPerfilEquiv['marca']                    = $strNombreMarcaOlt;
                    $arrayParamsPerfilEquiv['empresaCod']               = $strCodEmpresa;
                    $arrayParamsPerfilEquiv['tipoNegocio']              = $strTipoNegocio;
                    $arrayParamsPerfilEquiv['tipoEjecucion']            = 'FLUJO';
                    $strPerfilEquivalenteNuevo                          = $this->recursosRed->getPerfilPlanEquivalente($arrayParamsPerfilEquiv);
                    if(empty($strPerfilEquivalenteNuevo))
                    {
                        throw new \Exception("No existe un perfil configurado para la velocidad ".$strVelocidadNueva);
                    }
                }
            }
            else
            {
                throw new \Exception("No se ha mapeado un perfil para la velocidad del servicio");
            }

            if( isset($arrayMapeoVelocidadPerfil['valor4'])  && !empty($arrayMapeoVelocidadPerfil['valor4']) )
            {
                $strCapacidad1Nueva  = $arrayMapeoVelocidadPerfil['valor4'];
                $strCapacidad2Nueva  = $arrayMapeoVelocidadPerfil['valor4'];
            }
            else
            {
                throw new \Exception("No se han podido obtener las capacidades del nuevo plan, favor revisar!");
            }

            //Obtener la MAC ONT
            $objSpcMacOnt   = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "MAC ONT", $objProductoIsb);
            if(is_object($objSpcMacOnt))
            {
                $strMacOnt  = $objSpcMacOnt->getValor();
            }
            else
            {
                throw new \Exception("No existe la mac ont del cliente, Favor regularizar");
            }

            $intIdElementoCpeOnt            = $objServicioTecnico->getElementoClienteId();
            $objElementoCpeOnt              = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($intIdElementoCpeOnt);
            $strNombreModeloElementoCpeOnt  = $objElementoCpeOnt->getModeloElementoId()->getNombreModeloElemento();
            $strSerieCpeOnt                 = $objElementoCpeOnt->getSerieFisica();
            
            $objSpcIndiceCliente            = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, 
                                                                                                        "INDICE CLIENTE", 
                                                                                                        $objProductoIsb);
            $objSpcSpid                     = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "SPID", $objProductoIsb);
            $strLineProfileNameNueva        = "";
            $strGemPortNueva                = "";
            $strTrafficTableNueva           = "";
            $strVlanNueva                   = "";
            if($strNombreMarcaOlt === "HUAWEI")
            {
                $strTipoNegocioGetProdCaract    = $strTipoNegocio."|".$strNombreTecnicoSb;
                //Se obtienen las nuevas características del OLT HUAWEI para el nuevo perfil equivalente
                $arrayParametosCaracHuawei      = array('objServicio'                   => $objServicio,
                                                        'intIdElementoOlt'              => $intIdElementoOlt,
                                                        'strCodEmpresa'                 => $strCodEmpresa,
                                                        'strTipoNegocioGetProdCaract'   => $strTipoNegocioGetProdCaract,
                                                        'strPerfilEquivalenteNuevo'     => $strPerfilEquivalenteNuevo);
                $arrayParamsHuaweiPerfilNuevo   = $this->getParametrosElementoCaractHuawei($arrayParametosCaracHuawei);
                if($arrayParamsHuaweiPerfilNuevo['status'] === 200)
                {
                    $strLineProfileNameNueva        = $arrayParamsHuaweiPerfilNuevo['data']['line-profile'];
                    $strVlanNueva                   = $arrayParamsHuaweiPerfilNuevo['data']['vlan'];
                    $strGemPortNueva                = $arrayParamsHuaweiPerfilNuevo['data']['gem-port'];
                    $strTrafficTableNueva           = $arrayParamsHuaweiPerfilNuevo['data']['traffic-table'];
                }
                else
                {
                    throw new \Exception("No se pueden extraer las nuevas caracteristicas para el cambio de velocidad, favor revisar!");
                }

                //Se obtiene el SERVICE-PROFILE-NAME del OLT
                $objDetElementoServiceProfileName   = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                              ->findOneBy(array("detalleNombre" => "SERVICE-PROFILE-NAME",
                                                                                                "detalleValor"  => $strNombreModeloElementoCpeOnt, 
                                                                                                "elementoId"    => $intIdElementoOlt
                                                                                                )
                                                                                         );
                if(is_object($objDetElementoServiceProfileName))
                {
                    $strServiceProfile      = $objDetElementoServiceProfileName->getDetalleValor();
                    $objSpcServiceProfile   = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, 
                                                                                                        "SERVICE-PROFILE", 
                                                                                                        $objProductoIsb);
                    if (!is_object($objSpcServiceProfile))
                    {
                        $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicio, 
                                                                                        $objProductoIsb, 
                                                                                        "SERVICE-PROFILE", 
                                                                                        $strServiceProfile, 
                                                                                        $strUsrCreacion);
                        $objSpcServiceProfile  = $this->servicioGeneral->getServicioProductoCaracteristica( $objServicio, 
                                                                                                            "SERVICE-PROFILE", 
                                                                                                            $objProductoIsb);
                    }
                }
                else
                {
                    throw new \Exception("No existe Caracteristica SERVICE-PROFILE-NAME en el OLT, favor revisar!");
                }

                //Obtener SERVICE PROFILE
                if(is_object($objSpcServiceProfile))
                {
                    $strServiceProfile = $objSpcServiceProfile->getValor();
                }


                if (!is_object($objSpcIndiceCliente) || !is_object($objSpcSpid))
                {
                    throw new \Exception(   "No existen las caracteristicas necesarias para realizar el cambio de plan.<br>"
                                            . "Por favor comunicarse con el Dpto de Sistemas.");
                }
                else
                {
                    //Obtener INDICE CLIENTE
                    $strIndiceCliente   = $objSpcIndiceCliente->getValor();

                    //Obtener SERVICE PORT
                    $strSpid            = $objSpcSpid->getValor();
                }

                //Obtener características anteriores
                $objSpcLineProfileNameAnterior  = $this->servicioGeneral
                                                        ->getServicioProductoCaracteristica($objServicio, "LINE-PROFILE-NAME", $objProductoIsb);
                $objSpcGemPortAnterior          = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, 
                                                                                                            "GEM-PORT", 
                                                                                                            $objProductoIsb);
                $objSpcVlanAnterior             = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "VLAN", $objProductoIsb);
                $objSpcTrafficTableAnterior     = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, 
                                                                                                            "TRAFFIC-TABLE", 
                                                                                                            $objProductoIsb);

                //Obtener LINE-PROFILE con la velocidad anterior
                if(is_object($objSpcLineProfileNameAnterior))
                {
                    $strLineProfileNameAnterior = $objSpcLineProfileNameAnterior->getValor();
                }

                //Obtener GEM-PORT con la velocidad anterior
                if(is_object($objSpcGemPortAnterior))
                {
                    $strGemPortAnterior = $objSpcGemPortAnterior->getValor();
                }

                //Obtener VLAN con la velocidad anterior
                if(is_object($objSpcVlanAnterior))
                {
                    $strVlanAnterior    = $objSpcVlanAnterior->getValor();
                }

                //Obtener TRAFFIC-TABLE con la velocidad anterior
                if(is_object($objSpcTrafficTableAnterior))
                {
                    $strTrafficTableAnterior = $objSpcTrafficTableAnterior->getValor();
                }

                //Se eliminan características anteriores 
                $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objSpcLineProfileNameAnterior, "Eliminado");
                $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objSpcVlanAnterior, "Eliminado");
                $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objSpcGemPortAnterior, "Eliminado");
                $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objSpcTrafficTableAnterior, "Eliminado");

                //Se crean las nuevas características
                $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio, 
                                                                               $objProductoIsb, 
                                                                               "LINE-PROFILE-NAME", 
                                                                               $strLineProfileNameNueva, 
                                                                               $strUsrCreacion);

                $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio, 
                                                                               $objProductoIsb, 
                                                                               "GEM-PORT", 
                                                                               $strGemPortNueva, 
                                                                               $strUsrCreacion);

                $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio, 
                                                                               $objProductoIsb, 
                                                                               "VLAN", 
                                                                               $strVlanNueva, 
                                                                               $strUsrCreacion);

                $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio, 
                                                                               $objProductoIsb, 
                                                                               "TRAFFIC-TABLE", 
                                                                               $strTrafficTableNueva, 
                                                                               $strUsrCreacion);

                //obtener caracteristicas nuevas
                $objSpcLineProfileNameNuevo = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, 
                                                                                                        "LINE-PROFILE-NAME", 
                                                                                                        $objProductoIsb);
                $objSpcGemPortNuevo         = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "GEM-PORT", $objProductoIsb);
                $objSpcVlanNuevo            = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "VLAN", $objProductoIsb);
                $objSpcTrafficTableNuevo    = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio,
                                                                                                        "TRAFFIC-TABLE", 
                                                                                                        $objProductoIsb);
            }
            else if($strNombreMarcaOlt === "TELLION")
            {
                if(is_object($objSpcIndiceCliente))
                {
                    $strIndiceCliente   = $objSpcIndiceCliente->getValor();
                }
                $objSpcPerfil   = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "PERFIL", $objProductoIsb);

                //OBTENER LINE-PROFILE
                if(is_object($objSpcPerfil))
                {
                    $strLineProfileAnterior = $objSpcPerfil->getValor();
                }
                else
                {
                    throw new \Exception("No existe el perfil del servicio.<br>Por favor comunicarse con el Dpto de Sistemas.");
                }
                
                //obtener mac wifi
                $objSpcMacWifi = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "MAC WIFI", $objProductoIsb);
                if(is_object($objSpcMacWifi))
                {
                    //cambiar formato de la mac
                    $strMacWifi = $objSpcMacWifi->getValor();
                }
                else
                {
                    //obtener mac wifi
                    $objSpcMacWifi = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "MAC", $objProductoIsb);
                    if(is_object($objSpcMacWifi))
                    {
                        //cambiar formato de la mac
                        $strMacWifi = $objSpcMacWifi->getValor();
                    }
                    else
                    {
                        throw new \Exception("No existe mac wifi del cliente.<br>Por favor comunicarse con el Dpto de Sistemas.");
                    }
                }
                $arrayPerfilAnterior        = explode("_", $strLineProfileAnterior);
                $strLineProfileNameAnterior = $arrayPerfilAnterior[0] . "_" . $arrayPerfilAnterior[1] . "_" . $arrayPerfilAnterior[2];

                $arrayPerfilNuevo           = explode("_", $strPerfilEquivalenteNuevo);
                $strLineProfileNameNueva    = $arrayPerfilNuevo[0] . "_" . $arrayPerfilNuevo[1] . "_" . $arrayPerfilNuevo[2];
            }
            else if($strNombreMarcaOlt === "ZTE")
            {
                if (empty($strCapacidad1Actual))
                {
                    $objSpcCapacidad1Actual = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "CAPACIDAD1", $objProductoIsb);
                    if(is_object($objSpcCapacidad1Actual))
                    {
                        $strCapacidad1Actual = $objSpcCapacidad1Actual->getValor();
                    }
                    else
                    {
                        throw new \Exception("No existe característica CAPACIDAD1, favor revisar!");
                    }
                }

                if (empty($strCapacidad2Actual))
                {
                    $objSpcCapacidad2Actual = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "CAPACIDAD2", $objProductoIsb);
                    if(is_object($objSpcCapacidad2Actual))
                    {
                        $strCapacidad2Actual = $objSpcCapacidad2Actual->getValor();
                    }
                    else
                    {
                        throw new \Exception("No existe característica CAPACIDAD2, favor revisar!");
                    }
                }

                $objSpcServiceProfileActual = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio,
                                                                                                        "SERVICE-PROFILE",
                                                                                                        $objProductoIsb);
                if(is_object($objSpcServiceProfileActual))
                {
                    $strServiceProfile = $objSpcServiceProfileActual->getValor();
                }
                else
                {
                    $objElementoOntCliente  = $this->emComercial->getRepository('schemaBundle:InfoElemento')
                                                                ->find($objServicioTecnico->getElementoClienteId());
                    if(is_object($objElementoOntCliente))
                    {
                        $strServiceProfile = $objElementoOntCliente->getModeloElementoId()->getNombreModeloElemento();
                        $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio, $objProductoIsb, "SERVICE-PROFILE", 
                                                                                       $strServiceProfile, $strUsrCreacion);
                    }
                    else
                    {
                        throw new \Exception("No existe característica SERVICE-PROFILE-NAME en el OLT, favor revisar!");
                    }
                }

                $objSpcIndiceClienteActual = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio,
                                                                                                       "INDICE CLIENTE",
                                                                                                       $objProductoIsb);
                if(is_object($objSpcIndiceClienteActual))
                {
                    $strIndiceCliente = $objSpcIndiceClienteActual->getValor();
                }
                else
                {
                    throw new \Exception("No existe característica INDICE CLIENTE en el OLT, favor revisar!");
                }

                $objSpcSpidActual = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "SPID", $objProductoIsb);
                if(is_object($objSpcSpidActual))
                {
                    $strSpid = $objSpcSpidActual->getValor();
                }
                else
                {
                    throw new \Exception("No existe característica SPID en el OLT, favor revisar!");
                }
            }
            else
            {
                throw new \Exception("No existe flujo para la marca del olt ".$strNombreMarcaOlt
                                     ."<br>Por favor comunicarse con el Dpto de Sistemas.");
            }
            
            $objPersona         = $objServicio->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId();
            $strNombreCliente   = ($objPersona->getRazonSocial() != "") ? $objPersona->getRazonSocial() : 
                                                                          $objPersona->getNombres()." ".$objPersona->getApellidos();
            $strIdentificacion  = $objPersona->getIdentificacionCliente();
            $strLogin           = $objServicio->getPuntoId()->getLogin();

            $objServicioIpFija  = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                          ->findOneBy(array("servicioId"    => $objServicio->getId(),
                                                                            "estado"        => "Activo"));
            if($strNombreTecnicoSb!=="TELCOHOME" && $strDescripcionSb!=="TelcoHome" )
            {
                if(!is_object($objServicioIpFija))
                {
                    throw new \Exception("No se ha podido obtener la ip fija del servicio.<br>Por favor comunicarse con el Dpto de Sistemas.");
                }
                $strIpFija  = $objServicioIpFija->getIp();
            
                $objSpcScope = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "SCOPE", $objProductoIsb);

                if(!is_object($objSpcScope))
                {
                    //buscar scopes
                    $arrayScopeOlt  = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')
                                                              ->getScopePorIpFija($strIpFija, $intIdElementoOlt);
                    if (empty($arrayScopeOlt))
                    {
                        throw new \Exception("Ip Fija no pertenece a un Scope! <br>Favor Comunicarse con el Dep. Gepon!");
                    }
                    $strScope = $arrayScopeOlt['NOMBRE_SCOPE'];
                }
                else
                {
                    $strScope = $objSpcScope->getValor();
                }
                $intIpsFijasActivas = 0;
                if(isset($arrayProductoIpsb) && !empty($arrayProductoIpsb))
                {
                    $arrayServiciosPorPunto = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                ->findBy(array("puntoId" => $objServicio->getPuntoId()->getId()));
                    $arrayDatosIpActivas    = $this->servicioGeneral->getInfoIpsFijaPunto(  $arrayServiciosPorPunto, $arrayProductoIpsb, 
                                                                                            $objServicio, 'Activo', 'Activo', $objProductoIsb);
                    $intIpsFijasActivas     = $arrayDatosIpActivas['ip_fijas_activas'];
                }
            }
            $arrayDatos         = array(
                                        'serial_ont'            => $strSerieCpeOnt,
                                        'mac_ont'               => $strMacOnt,
                                        'nombre_olt'            => $objElementoOlt->getNombreElemento(),
                                        'ip_olt'                => $objIpElementoOlt->getIp(),
                                        'puerto_olt'            => $objInterfaceOlt->getNombreInterfaceElemento(),
                                        'modelo_olt'            => $strNombreModeloOlt,
                                        'gemport'               => $strGemPortAnterior,
                                        'service_profile'       => $strServiceProfile,
                                        'line_profile'          => $strLineProfileNameAnterior,
                                        'traffic_table'         => $strTrafficTableAnterior,
                                        'ont_id'                => $strIndiceCliente,
                                        'service_port'          => $strSpid,
                                        'vlan'                  => $strVlanAnterior,
                                        'estado_servicio'       => $objServicio->getEstado(),
                                        'mac_wifi'              => $strMacWifi,
                                        'tipo_negocio_actual'   => $strTipoNegocioMiddleware,
                                        'line_profile_nuevo'    => $strLineProfileNameNueva,
                                        'gemport_nuevo'         => $strGemPortNueva,
                                        'traffic_table_nueva'   => $strTrafficTableNueva,
                                        'tipo_negocio_nuevo'    => $strTipoNegocioMiddleware,
                                        'vlan_nueva'            => $strVlanNueva,
                                        'ip'                    => $strIpFija,
                                        'scope'                 => $strScope,
                                        'ip_fijas_activas'      => $intIpsFijasActivas,
                                        'capacidad_up'          => $strCapacidad1Actual,
                                        'capacidad_down'        => $strCapacidad2Actual,
                                        'capacidad_up_nueva'    => $strCapacidad1Nueva,
                                        'capacidad_down_nueva'  => $strCapacidad2Nueva
                                    );
            $arrayDatosMiddleware   = array(
                                            'empresa'               => $strPrefijoEmpresa,
                                            'nombre_cliente'        => $strNombreCliente,
                                            'login'                 => $strLogin,
                                            'identificacion'        => $strIdentificacion,
                                            'datos'                 => $arrayDatos,
                                            'opcion'                => $this->opcion,
                                            'ejecutaComando'        => $this->ejecutaComando,
                                            'usrCreacion'           => $strUsrCreacion,
                                            'ipCreacion'            => $strIpCreacion
                                        );
            $arrayFinalMiddleware   = $this->rdaMiddleware->middleware(json_encode($arrayDatosMiddleware));
            $strStatusMiddleware    = $arrayFinalMiddleware['status'];
            $strMensajeMiddleware   = $arrayFinalMiddleware['mensaje'];

            $this->utilService->insertError('Telcos+', 
                                            'InfoCambiarPlanService->cambiarPlanIsb', 
                                            $strMensajeMiddleware, 
                                            $strUsrCreacion, 
                                            $strIpCreacion
                                           );

            if($strStatusMiddleware === "OK")
            {
                if($strNombreMarcaOlt === "TELLION" && $strEjecutaScriptsOlt === "SI")
                {
                    $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objSpcPerfil, "Eliminado");
                    
                    $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objSpcIndiceCliente, "Eliminado");

                    $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicio, 
                                                                                    $objProductoIsb, 
                                                                                    "INDICE CLIENTE", 
                                                                                    $strIndiceCliente, 
                                                                                    $strUsrCreacion);
                    
                    
                    if(intval($intIpsFijasActivas) >0)
                    {
                        $strPerfilEquivalenteNuevo = substr($strPerfilEquivalenteNuevo,0,strlen($strPerfilEquivalenteNuevo)-2)."_5";
                    }
                    $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicio, 
                                                                                    $objProductoIsb, 
                                                                                    "PERFIL", 
                                                                                    $strPerfilEquivalenteNuevo, 
                                                                                    $strUsrCreacion);
                }
                else if($strNombreMarcaOlt === "ZTE")
                {
                    $objSpcCapacidad1Actual = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "CAPACIDAD1", $objProductoIsb);
                    if(is_object($objSpcCapacidad1Actual))
                    {
                        $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objSpcCapacidad1Actual, "Eliminado");
                    }
                    //GRABAMOS CAPACIDAD1 DEL NUEVO PLAN
                    $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio,
                                                                                   $objProductoIsb,
                                                                                   "CAPACIDAD1",
                                                                                   $strCapacidad1Nueva,
                                                                                   $strUsrCreacion);

                    $objSpcCapacidad2Actual = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "CAPACIDAD2", $objProductoIsb);
                    if(is_object($objSpcCapacidad2Actual))
                    {
                        $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objSpcCapacidad2Actual, "Eliminado");
                    }
                    //GRABAMOS CAPACIDAD2 DEL NUEVO PLAN
                    $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio,
                                                                                   $objProductoIsb,
                                                                                   "CAPACIDAD2",
                                                                                   $strCapacidad2Nueva,
                                                                                   $strUsrCreacion);

                    if(isset($arrayFinalMiddleware['vlan']) && !empty($arrayFinalMiddleware['vlan']))
                    {
                        $objSpcVlanActual = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "VLAN", $objProductoIsb);
                        if(is_object($objSpcVlanActual))
                        {
                            $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objSpcVlanActual, "Eliminado");
                        }
                        //GRABAMOS VLAN DEL NUEVO PLAN
                        $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio,
                                                                                       $objProductoIsb,
                                                                                       "VLAN",
                                                                                       $arrayFinalMiddleware['vlan'],
                                                                                       $strUsrCreacion);
                    }

                    if(isset($arrayFinalMiddleware['client_class']) && !empty($arrayFinalMiddleware['client_class']))
                    {
                        $objSpcClientClassActual = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio,
                                                                                                             "CLIENT CLASS",
                                                                                                             $objProductoIsb);
                        if(is_object($objSpcClientClassActual))
                        {
                            $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objSpcClientClassActual, "Eliminado");
                        }
                        //GRABAMOS CLIENT CLASS DEL NUEVO PLAN
                        $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio,
                                                                                       $objProductoIsb,
                                                                                       "CLIENT CLASS",
                                                                                       $arrayFinalMiddleware['client_class'],
                                                                                       $strUsrCreacion);
                    }

                    if(isset($arrayFinalMiddleware['pckid']) && !empty($arrayFinalMiddleware['pckid']))
                    {
                        $objSpcPackageIdActual = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio,
                                                                                                           "PACKAGE ID",
                                                                                                           $objProductoIsb);
                        if(is_object($objSpcPackageIdActual))
                        {
                            $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objSpcPackageIdActual, "Eliminado");
                        }
                        //GRABAMOS PCKID DEL NUEVO PLAN
                        $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio,
                                                                                       $objProductoIsb,
                                                                                       "PACKAGE ID",
                                                                                       $arrayFinalMiddleware['pckid'],
                                                                                       $strUsrCreacion);
                    }

                    if(isset($arrayFinalMiddleware['line_profile']) && !empty($arrayFinalMiddleware['line_profile']))
                    {
                        $objSpcLineProfileNameActual = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio,
                                                                                                                 "LINE-PROFILE-NAME",
                                                                                                                 $objProductoIsb);
                        if(is_object($objSpcLineProfileNameActual))
                        {
                            $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objSpcLineProfileNameActual, "Eliminado");
                        }
                        //GRABAMOS LINE PROFILE NAME DEL NUEVO PLAN
                        $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio,
                                                                                       $objProductoIsb,
                                                                                       "LINE-PROFILE-NAME",
                                                                                       $arrayFinalMiddleware['line_profile'],
                                                                                       $strUsrCreacion);
                    }
                }
                //Consulta velocidad por producto
                $strDescripcionVelocidad = 'VELOCIDAD';
                $strDescVelocidadIP      = '[VELOCIDAD]';
                
                $arrayAdmiParametroProducto = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                  ->getOne('PARAM_CARACT_VELOCIDAD_X_PRODUCTO',
                                                           '',
                                                           '',
                                                           '',
                                                           $objProductoIsb->getDescripcionProducto(),
                                                           '',
                                                           '',
                                                           '',
                                                           '',
                                                           $strCodEmpresa
                                                          );
                if (isset($arrayAdmiParametroProducto['valor2']) && !empty($arrayAdmiParametroProducto['valor2']))
                {
                    $strDescripcionVelocidad = $arrayAdmiParametroProducto['valor4'];
                    $strDescVelocidadIP      = $arrayAdmiParametroProducto['valor3'];
                }

                if($strNombreTecnicoSb!=="TELCOHOME" && $strDescripcionSb!=="TelcoHome" )
                {
                    $objSpcVelocidadAnterior = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, 
                                                                       $strDescripcionVelocidad, $objProductoIsb);
                    $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objSpcVelocidadAnterior, "Eliminado");

                    //Se crea la característica con el nuevo valor de la velocidad
                    $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio, 
                                                                                   $objProductoIsb, 
                                                                                   $strDescripcionVelocidad, 
                                                                                   $strVelocidadNueva, 
                                                                                   $strUsrCreacion);
                }
                else
                {
                    $objSpcVelocidadAnterior = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio,
                                                                                                         "VELOCIDAD_TELCOHOME",
                                                                                                         $objProductoIsb);
                    $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objSpcVelocidadAnterior, "Eliminado");

                    //Se crea la característica con el nuevo valor de la velocidad
                    $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio,
                                                                                   $objProductoIsb,
                                                                                   "VELOCIDAD_TELCOHOME",
                                                                                   $strVelocidadNueva,
                                                                                   $strUsrCreacion);
                }
                $floatPrecioVentaActual = $objServicio->getPrecioVenta();
                $objServicio->setPrecioVenta($floatPrecioNuevo);
                $strDescripcionFacturaAnterior = $objServicio->getDescripcionPresentaFactura();
                if(!empty($strDescripcionFacturaAnterior))
                {
                    $strDescripcionFacturaNueva = str_replace(  $strVelocidadAnterior, 
                                                                $strVelocidadNueva, 
                                                                $strDescripcionFacturaAnterior);
                    $objServicio->setDescripcionPresentaFactura($strDescripcionFacturaNueva);
                }
                //Se recalcula el valor del descuento del servicio
                $arrayRespuesta = $this->recalcularDescuentoServicio(array("objInfoServicio"      => $objServicio,
                                                                           "strUsrCreacion"       => $strUsrCreacion,
                                                                           "strIpCreacion"        => $strIpCreacion,
                                                                           "floatPrecionuevoPlan" => $floatPrecioNuevo
                                                                           )
                                                                    );
                //Si no es null, obtengo respuesta exitosa
                $strObsDescuento          = "";
                if(!is_null($arrayRespuesta))
                {
                    if(is_object($arrayRespuesta["objInfoServicio"]))
                    {
                        $objServicio->setValorDescuento($arrayRespuesta["objInfoServicio"]->getValorDescuento());
                        $objServicio->setDescuentoUnitario($arrayRespuesta["objInfoServicio"]->getDescuentoUnitario());
                    }
                    if(!is_null($arrayRespuesta["strObsDescuento"]))
                    {
                        $strObsDescuento = $arrayRespuesta["strObsDescuento"];
                    }
                }
                $this->emComercial->persist($objServicio);
                $this->emComercial->flush();

                //historial del servicio
                $strObservacionServicio = "Se cambio de velocidad,<br> "
                                        . "Velocidad anterior:<b>" . $strVelocidadAnterior . " MB</b>,<br>"
                                        . "Velocidad nueva:<b>" . $strVelocidadNueva . " MB</b>,<br>"
                                        . "Precio anterior:<b>" . $floatPrecioVentaActual . "</b>,<br>"
                                        . "Precio nuevo:<b>" . $floatPrecioNuevo . "</b>" . ($strObsDescuento != "" ? ",<br>" : "")
                                        . $strObsDescuento;

                $objServicioHistorial = new InfoServicioHistorial();
                $objServicioHistorial->setServicioId($objServicio);
                $objServicioHistorial->setObservacion($strObservacionServicio);
                $objServicioHistorial->setEstado($objServicio->getEstado());
                $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                $objServicioHistorial->setIpCreacion($strIpCreacion);
                $this->emComercial->persist($objServicioHistorial);
                $this->emComercial->flush();

                if( floatval($floatPrecioNuevo) > floatval($floatPrecioVentaActual) )
                {
                    /**
                     * Se crea historial respectivo para poder facturar el proporcional del servicio por el cambio de plan
                     */
                    $strObservacionCambioPrecio = "Precio anterior: " . $floatPrecioVentaActual;
                    $objServicioHistorialCambioPrecio   = new InfoServicioHistorial();
                    $objServicioHistorialCambioPrecio->setServicioId($objServicio);
                    $objServicioHistorialCambioPrecio->setObservacion($strObservacionCambioPrecio);
                    $objServicioHistorialCambioPrecio->setEstado($objServicio->getEstado());
                    $objServicioHistorialCambioPrecio->setUsrCreacion($strUsrCreacion);
                    $objServicioHistorialCambioPrecio->setFeCreacion(new \DateTime('now'));
                    $objServicioHistorialCambioPrecio->setIpCreacion($strIpCreacion);
                    $objServicioHistorialCambioPrecio->setAccion('confirmoCambioPrecio');
                    $this->emComercial->persist($objServicioHistorialCambioPrecio);
                    $this->emComercial->flush();
                }
                
                /**
                 * Se agrega cambio de precio para servicios IP Small Business por cambio de velocidad de servicio Small Business
                 */
                if($intIpsFijasActivas > 0)
                {
                    $arrayIpsActivas    = $arrayDatosIpActivas['valores'];
                    foreach($arrayIpsActivas as $arrayIpActiva)
                    {
                        $intIdServicioIpActiva  = $arrayIpActiva["id_servicio"];
                        $objServicioIpActiva    = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicioIpActiva);
                        if(is_object($objServicioIpActiva))
                        {
                            $floatPrecioNuevoIp = 0;
                            $objProductoIpSb    = $objServicioIpActiva->getProductoId();
                            if(!is_object($objProductoIpSb))
                            {
                                throw new \Exception("No se ha podido obtener el producto Ip.<br>"
                                                     . "Por favor comunicarse con el Dpto de Sistemas.");
                            }
                            $strFuncionPrecioIpSbProd   = $objProductoIpSb->getFuncionPrecio();
                            $arrayParamsReemplazar      = array('Math.ceil','Math.floor','Math.pow', $strDescVelocidadIP,'PRECIO');
                            $arrayValoresReemplazar     = array('ceil','floor','pow', $strVelocidadNueva, '$floatPrecioNuevoIp');
                            $strFuncionPrecioIpSb       = str_replace($arrayParamsReemplazar, $arrayValoresReemplazar, $strFuncionPrecioIpSbProd);
                            $strDigitoVerificacion      = substr($strFuncionPrecioIpSb, -1, 1);
                            if(is_numeric($strDigitoVerificacion))
                            {
                                $strFuncionPrecioIpSb = $strFuncionPrecioIpSb . ";";
                            }
                            eval($strFuncionPrecioIpSb);

                            if(empty($floatPrecioNuevoIp) || !is_numeric($floatPrecioNuevoIp))
                            {
                                throw new \Exception("No se ha podido obtener el precio nuevo para el producto "
                                                     .$objProductoIpSb->getDescripcionProducto().".<br>"
                                                     . "Por favor comunicarse con el Dpto de Sistemas.");
                            }
                            $floatPrecioVentaActualIp = $objServicioIpActiva->getPrecioVenta();
                            $strObservacionServicioIp = "Se cambio de velocidad,<br> "
                                                        . "Velocidad anterior:<b>" . $strVelocidadAnterior . " MB</b>,<br>"
                                                        . "Velocidad nueva:<b>" . $strVelocidadNueva . " MB</b>,<br>"
                                                        . "Precio anterior:<b>" . $floatPrecioVentaActualIp . "</b>,<br>"
                                                        . "Precio nuevo:<b>" . $floatPrecioNuevoIp . "</b>";
                            $objServicioIpActiva->setPrecioVenta($floatPrecioNuevoIp);
                            $strDescripcionFacturaAnteriorIp = $objServicioIpActiva->getDescripcionPresentaFactura();
                            if(!empty($strDescripcionFacturaAnteriorIp))
                            {
                                $strDescripcionFacturaNuevaIp = str_replace($strVelocidadAnterior, 
                                                                            $strVelocidadNueva, 
                                                                            $strDescripcionFacturaAnteriorIp);
                                $objServicioIpActiva->setDescripcionPresentaFactura($strDescripcionFacturaNuevaIp);
                            }
                            $this->emComercial->persist($objServicioIpActiva);
                            $this->emComercial->flush();
                            
                            $objServicioHistorialIp = new InfoServicioHistorial();
                            $objServicioHistorialIp->setServicioId($objServicioIpActiva);
                            $objServicioHistorialIp->setObservacion($strObservacionServicioIp);
                            $objServicioHistorialIp->setEstado($objServicioIpActiva->getEstado());
                            $objServicioHistorialIp->setUsrCreacion($strUsrCreacion);
                            $objServicioHistorialIp->setFeCreacion(new \DateTime('now'));
                            $objServicioHistorialIp->setIpCreacion($strIpCreacion);
                            $this->emComercial->persist($objServicioHistorialIp);
                            $this->emComercial->flush();
                            
                            if( floatval($floatPrecioNuevoIp) > floatval($floatPrecioVentaActualIp) )
                            {
                                /**
                                 * Se crea historial respectivo para poder facturar el proporcional del servicio por el cambio de plan
                                 */
                                $strObservacionCambioPrecio = "Precio anterior: " . $floatPrecioVentaActualIp;
                                $objServicioHistorialCambioPrecio   = new InfoServicioHistorial();
                                $objServicioHistorialCambioPrecio->setServicioId($objServicioIpActiva);
                                $objServicioHistorialCambioPrecio->setObservacion($strObservacionCambioPrecio);
                                $objServicioHistorialCambioPrecio->setEstado($objServicioIpActiva->getEstado());
                                $objServicioHistorialCambioPrecio->setUsrCreacion($strUsrCreacion);
                                $objServicioHistorialCambioPrecio->setFeCreacion(new \DateTime('now'));
                                $objServicioHistorialCambioPrecio->setIpCreacion($strIpCreacion);
                                $objServicioHistorialCambioPrecio->setAccion('confirmoCambioPrecio');
                                $this->emComercial->persist($objServicioHistorialCambioPrecio);
                                $this->emComercial->flush();
                            }
                            
                            if($strNombreMarcaOlt === "TELLION")
                            {
                                $objSpcPerfilIp = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioIpActiva, 
                                                                                                            "PERFIL", 
                                                                                                            $objProductoIsb);
                                if (is_object($objSpcPerfilIp))
                                {
                                    $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objSpcPerfilIp, "Eliminado");
                                }
                                $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicioIpActiva, 
                                                                                                $objProductoIsb, 
                                                                                                "PERFIL", 
                                                                                                $strPerfilEquivalenteNuevo, 
                                                                                                $strUsrCreacion);
                            }
                        }
                    }
                }
                $strMensaje = $strObservacionServicio;
            }
            else
            {
                if($strNombreMarcaOlt === "HUAWEI")
                {
                    //Se eliminan características nuevas
                    if (is_object($objSpcLineProfileNameNuevo))
                    {
                        $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objSpcLineProfileNameNuevo, "Eliminado");
                    }
                    if (is_object($objSpcGemPortNuevo))
                    { 
                        $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objSpcGemPortNuevo, "Eliminado");
                    }
                    if (is_object($objSpcVlanNuevo))
                    {
                        $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objSpcVlanNuevo, "Eliminado");
                    }
                    if (is_object($objSpcTrafficTableNuevo))
                    {
                        $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objSpcTrafficTableNuevo, "Eliminado");
                    }
                    
                    //Se activan caracteristicas anteriores
                    $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objSpcLineProfileNameAnterior, "Activo");
                    $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objSpcGemPortAnterior, "Activo");
                    $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objSpcVlanAnterior, "Activo");
                    $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objSpcTrafficTableAnterior, "Activo");
                }
                $strMensaje = $strMensajeMiddleware;
            }
            $strStatus  = $strStatusMiddleware;
        }
        catch(\Exception $e)
        {
            $strStatus  = "ERROR";
            $strMensaje = $e->getMessage();
            error_log($strMensaje);
        }
        
        $arrayRespuestaFinal[] = array('status' => $strStatus, 'mensaje' => $strMensaje);
        return $arrayRespuestaFinal;
        
    }
    
    
    /**
     * verificarCambioPlanesDiciembre
     * 
     * Función que verifica si se puede realizar o no el cambio de plan para los planes de diciembre
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 28-11-2018
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 15-01-2019 Se elimina restricción de cambio de plan hacia los planes vigentes de 6, 15 y 30MB considerados 
     *                         en el cambio de plan masivo. 
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.2 28-03-2019 Se agrega validación para generar las solicitudes de agregar equipos en clientes de manera correcta
     * @since 1.1
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 26-04-2019 Se corrige validación para permitir crear solicitud de agregar equipo con el extender
     *                          cuando se realiza un cambio de plan desde un plan con Wifi Dual Band a un pĺan con Wifi Dual Band y Extender.
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.4 05-06-2019 - Se agregan programación para retornar bandera que indica si el plan actual incluye el producto MCAFEE
     * @since 1.3
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.5 12-08-2019 - Se agregan programación para retornar información necesaría para realixar la
     *                           migración de tecnología de McAfee a Kaspersky
     * @since 1.4
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.6 14-07-2020 Se elimina código de validación de planes, ya que dicha programación ahora se encuentra en un 
     *                         procedimiento de base de datos
     */
    public function verificarCambioPlanesDiciembre($arrayParametros)
    {
        $strCodEmpresa                      = $arrayParametros['codEmpresa'];
        $intIdServicio                      = $arrayParametros['idServicio'];
        $intIdPlanNuevo                     = $arrayParametros['idPlanNuevo'];
        $strMensaje                         = "";
        $strPlanActualTieneMcAfee           = "NO";
        $strTieneNuevoAntivirus             = "NO";
        $strPlanNuevoTieneMcAfee            = "NO";
        $objProdIntProtegidoAnt             = null;
        $objProductoIntProtegido            = null;
        $strFlujoAntivirus                  = "";
        $strValorAntivirus                  = "";
        $intCantLicenciasPlanIntProt        = 0;
        try
        {
            $objServicio        = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
            $objPlanActual      = $objServicio->getPlanId();
            $intIdPlanActual    = $objPlanActual->getId();
            
            $boolFalse                          = false;
            
            $arrayDetallesPlanServicioAnterior  = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                                                    ->findBy(array("planId" => $intIdPlanActual));

            foreach($arrayDetallesPlanServicioAnterior as $objDetallePlanServicioAnterior)
            {
                $objProductoDetallePlanAnterior = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                                    ->find($objDetallePlanServicioAnterior->getProductoId());
                if(is_object($objProductoDetallePlanAnterior))
                {
                    $boolVerificaMacAfeeEnPlanAnterior = strpos($objProductoDetallePlanAnterior->getDescripcionProducto(), 'I. PROTEGIDO MULTI');

                    if($boolVerificaMacAfeeEnPlanAnterior !== $boolFalse)
                    {
                        $objProdIntProtegidoAnt   = $objProductoDetallePlanAnterior;
                        $strPlanActualTieneMcAfee = "SI";
                    }
                }
            }
            
            if ($strPlanActualTieneMcAfee == "SI" && is_object($objProdIntProtegidoAnt))
            {
                $objServProdCaracAntivirus = $this->servicioGeneral
                                                  ->getServicioProductoCaracteristica($objServicio,
                                                                                      "SUSCRIBER_ID",
                                                                                      $objProdIntProtegidoAnt);
                if (is_object($objServProdCaracAntivirus))
                {
                    $strTieneNuevoAntivirus = "SI";
                }
                else
                {
                    $arrayEstados = array();
                    $arrayEstados['strEstadoSpc'] = 'Suspendido';
                    $objServProdCaracAntivirus = $this->servicioGeneral
                                                ->getServicioProductoCaracteristica($objServicio,
                                                                                    "SUSCRIBER_ID",
                                                                                    $objProdIntProtegidoAnt,
                                                                                    $arrayEstados
                                                                                    );
                    if (is_object($objServProdCaracAntivirus))
                    {
                        $strTieneNuevoAntivirus = "SI";
                    }
                    else
                    {

                        $arrayEstados['strEstadoSpc'] = 'Pendiente';
                        $objServProdCaracAntivirus = $this->servicioGeneral
                                            ->getServicioProductoCaracteristica($objServicio,
                                                                                "SUSCRIBER_ID",
                                                                                $objProdIntProtegidoAnt,
                                                                                $arrayEstados
                                                                                );
                        if (is_object($objServProdCaracAntivirus))
                        {
                            $strTieneNuevoAntivirus = "SI";
                        }
                    }
                }
            }
            
            $arrayDetallesPlanServicioNuevo  = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                                                    ->findBy(array("planId" => $intIdPlanNuevo));

            foreach($arrayDetallesPlanServicioNuevo as $objDetallePlanServicioNuevo)
            {
                $objProductoDetallePlanNuevo = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                                    ->find($objDetallePlanServicioNuevo->getProductoId());
                if(is_object($objProductoDetallePlanNuevo))
                {
                    $boolVerificaMacAfeeEnPlanNuevo = strpos($objProductoDetallePlanNuevo->getDescripcionProducto(), 'I. PROTEGIDO MULTI');

                    if($boolVerificaMacAfeeEnPlanNuevo !== $boolFalse)
                    {
                        $strPlanNuevoTieneMcAfee      = "SI";
                        $objProductoIntProtegido      = $objProductoDetallePlanNuevo;
                        $arrayCaracteristicasPlanProd = $this->emComercial
                                                             ->getRepository('schemaBundle:InfoPlanProductoCaract')
                                                             ->getCaracteristicaByParametros($objDetallePlanServicioNuevo->getId(),
                                                                                             $objProductoDetallePlanNuevo->getId(),
                                                                                             'CANTIDAD DISPOSITIVOS');
                        if(is_array($arrayCaracteristicasPlanProd) && count($arrayCaracteristicasPlanProd)>0)
                        {
                            $intCantLicenciasPlanIntProt = (int) $arrayCaracteristicasPlanProd[0]["valor"];
                        }
                        else
                        {
                            $intCantLicenciasPlanIntProt = 0;
                        }
                    }
                }
            }
            
            $arrayValidaFlujoAntivirus  = $this->serviceLicenciasKaspersky->validaFlujoAntivirus(array( 
                                                                                                      "intIdPunto"        => 
                                                                                                      $objServicio->getPuntoId()->getId(),
                                                                                                      "strCodEmpresa"     => $strCodEmpresa
                                                                                                      ));
            $strFlujoAntivirus = $arrayValidaFlujoAntivirus["strFlujoAntivirus"];
            $strValorAntivirus = $arrayValidaFlujoAntivirus["strValorAntivirus"];
            $strStatus = "OK";
        }
        catch (\Exception $e) 
        {
            $strStatus  = "ERROR";
            $strMensaje = $e->getMessage();
        }
        $arrayRespuesta = array("status"                        => $strStatus,
                                "mensaje"                       => $strMensaje,
                                "planActualTieneMcAfee"         => $strPlanActualTieneMcAfee,
                                "planNuevoTieneMcAfee"          => $strPlanNuevoTieneMcAfee,
                                "strTieneNuevoAntivirus"        => $strTieneNuevoAntivirus,
                                "intCantLicenciasPlanIntProt"   => $intCantLicenciasPlanIntProt,
                                "objProdIntProtegidoAnt"        => $objProdIntProtegidoAnt,
                                "objProductoIntProtegido"       => $objProductoIntProtegido,
                                "strFlujoAntivirus"             => $strFlujoAntivirus,
                                "strValorAntivirus"             => $strValorAntivirus
                               );
        return $arrayRespuesta;
    }
    
    /**
     * obtenerServiciosInternetProtegido
     * 
     * Función que sirve para obtener servicios adicionales de internet protegido con estado Activo
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 06-08-2019
     *
     * @param $arrayParametros
     * 
     * @since 1.0
     */
    public function obtenerServiciosInternetProtegido($arrayParametros)
    {
        $objServicioInternet         = $arrayParametros['objServicioInternet'];
        $strUsrCreacion              = $arrayParametros['strUsrCreacion'];
        $strIpCreacion               = $arrayParametros['strIpCreacion'];
        $strTieneNuevoAntivirus      = "NO";
        $strTieneAntivirusAdicional  = "NO";
        $arrayRespuesta              = array();
        $intCantidadDispositivos     = 1;
        $intPrecioVenta              = 0;
        $arrayRespuesta['strStatus'] = "ERROR";
        $arrayRespuesta['strTieneAntivirusAdicional'] = "NO";
        $arrayRespuesta['arrayServiciosAdicionalesNuevos']   = array();
        $arrayRespuesta['arrayServiciosAdicionalesAntiguos'] = array();
        $arrayServiciosIntProtegidoNuevos             = array();
        $arrayServiciosIntProtegidoAntiguos           = array();
        try
        {
            if(!is_object($objServicioInternet))
            {
                throw new \Exception("No se ha podido obtener el Servicio de Internet del cliente ".
                                     "para recuperar servicios adicionales I. Protegido en el pumto");
            }
            $arrayServiciosAdicionales = $this->emComercial
                                              ->getRepository('schemaBundle:InfoServicio')
                                              ->findBy(array('puntoId' => $objServicioInternet->getPuntoId(),
                                                             'estado'  => array('Activo','Pendiente')));

            //se recorren servicios con estado Activo del cliente
            foreach($arrayServiciosAdicionales as $objServicioAdicional)
            {
                $strTieneNuevoAntivirus = "NO";
                $intPrecioVenta         = 0;
                $objAdmiProducto     = $objServicioAdicional->getProductoId();
                if (is_object($objAdmiProducto))
                {
                    $booleanValidaProducto = strpos($objAdmiProducto->getDescripcionProducto(), 'I. PROTEGIDO');
                    $booleanValidaProductoProteccionTotal = strpos($objAdmiProducto->getDescripcionProducto(), 'I. PROTECCION');
                    //se valida que sean productos McAfee
                    if ($booleanValidaProducto !== false || $booleanValidaProductoProteccionTotal !== false)
                    {
                        
                        $strTieneAntivirusAdicional = "SI";
                        //obtener caracteristica antivirus y marcar los servicios con tu marca de tecnología actual
                        $objServProdCaracAntivirus = $this->servicioGeneral
                                                          ->getServicioProductoCaracteristica($objServicioAdicional,
                                                                                              "SUSCRIBER_ID",
                                                                                              $objAdmiProducto);
                        if(is_object($objServProdCaracAntivirus))
                        {
                            $strTieneNuevoAntivirus = "SI";
                        }
                        else
                        {
                            $strTieneNuevoAntivirus = "NO";
                        }
                        if($booleanValidaProducto !== false)
                        {
                            $objServProdCaracCantDisp = $this->servicioGeneral
                                                             ->getServicioProductoCaracteristica($objServicioAdicional,
                                                                                                 "CANTIDAD DISPOSITIVOS",
                                                                                                 $objAdmiProducto);
                            if (is_object($objServProdCaracCantDisp))
                            {
                                $intCantidadDispositivos = (int) $objServProdCaracCantDisp->getValor();
                            }
                            else
                            {
                                $intCantidadDispositivos = 1;
                            }
                        }
                        else
                        {
                            $intCantidadDispositivos = 1;
                        }
                        $intPrecioVenta = $objServicioAdicional->getPrecioVenta();
                        
                        $arrayServicioIntProtegido = array();
                        $arrayServicioIntProtegido['intPrecioVenta']       = $intPrecioVenta;
                        $arrayServicioIntProtegido['objServicioAdicional'] = $objServicioAdicional;
                        $arrayServicioIntProtegido['strTieneNuevoAntivirus']  = $strTieneNuevoAntivirus;
                        $arrayServicioIntProtegido['intCantidadDispositivos'] = $intCantidadDispositivos;
                        if($strTieneNuevoAntivirus == "SI")
                        {
                            $arrayServiciosIntProtegidoNuevos[]   = $arrayServicioIntProtegido;
                        }
                        else
                        {
                            $arrayServiciosIntProtegidoAntiguos[] = $arrayServicioIntProtegido;
                        }
                    }
                }
            }
            $arrayRespuesta['strStatus'] = "OK";
        }   
        catch (\Exception $objEx) 
        {
            $strMensaje = $objEx->getMessage();
            $arrayRespuesta['strStatus'] = "ERROR";
            $this->utilService->insertError('Telcos+', 
                                            'InfoCambiarPlanService.obtenerServiciosInternetProtegido', 
                                            $strMensaje, 
                                            $strUsrCreacion, 
                                            $strIpCreacion
                                           );
        }
        $arrayRespuesta['strTieneAntivirusAdicional'] = $strTieneAntivirusAdicional;
        $arrayRespuesta['arrayServiciosAdicionalesNuevos']   = $arrayServiciosIntProtegidoNuevos;
        $arrayRespuesta['arrayServiciosAdicionalesAntiguos'] = $arrayServiciosIntProtegidoAntiguos;
        return $arrayRespuesta;
    }
    
    /**
     * Función que verifica el cambio de plan, restringiendo aquellas opciones que no son permitidas
     * 
     * @param $arrayParametros [
     *                              "intIdServicio"     => Id del servicio
     *                              "intIdPlanNuevo"    => Id del plan nuevo
     *                          ]
     * 
     * @return array $arrayRespuesta [ 
     *                                  "status"    => 'OK' o 'ERROR'
     *                                  "mensaje"   => Mensaje de error
     *                                ]
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 01-07-2020
     * 
     */
    public function verificaCambioPlan($arrayParametros)
    {
        $intIdServicio  = $arrayParametros["intIdServicio"];
        $intIdPlanNuevo = $arrayParametros["intIdPlanNuevo"];
        $strStatus      = str_repeat(' ', 5);
        $strMsjError    = str_repeat(' ', 1000);
        try
        {
            $strSql = " BEGIN 
                          DB_COMERCIAL.TECNK_SERVICIOS.P_VERIFICA_CAMBIO_PLAN(  :intIdServicio, 
                                                                                :intIdPlanNuevo, 
                                                                                :strStatus, 
                                                                                :strMsjError); 
                        END;";
            
            $objStmt = $this->emInfraestructura->getConnection()->prepare($strSql);
            $objStmt->bindParam('intIdServicio', $intIdServicio);
            $objStmt->bindParam('intIdPlanNuevo', $intIdPlanNuevo);
            $objStmt->bindParam('strStatus', $strStatus);
            $objStmt->bindParam('strMsjError', $strMsjError);
            $objStmt->execute();
            if(strlen(trim($strStatus)) > 0)
            {
                $strStatusRespuesta     = $strStatus;
                $strMensajeRespuesta    = $strMsjError;
            }
            else
            {
                $strStatusRespuesta     = "ERROR";
                $strMensajeRespuesta    = 'No se ha podido verificar el cambio de plan. Por favor comuníquese con Sistemas!';
            }
        }
        catch (\Exception $e)
        {
            $strStatusRespuesta     = "ERROR";
            $strMensajeRespuesta    = 'Ha ocurrido un error inesperado y no se ha podido verificar el cambio de plan. '
                                      .'Por favor comuníquese con Sistemas!';
        }
        $arrayRespuesta = array("status"    => $strStatusRespuesta,
                                "mensaje"   => $strMensajeRespuesta);
        return $arrayRespuesta;
    }
    
    /**
     * Función que realiza los procesos que deben ejecutarse luego de un cambio de plan exitoso
     * 
     * @param $arrayParametros [
     *                              "intIdServicio"     => Id del servicio
     *                              "intIdPlanAnterior" => Id del plan anterior
     *                              "intIdPlanNuevo"    => Id del plan nuevo
     *                              "strUsrCreacion"    => Usuario de creación
     *                              "strIpCreacion"     => Ip de creación
     *                          ]
     * 
     * @return array $arrayRespuesta [ 
     *                                  "status"    => 'OK' o 'ERROR'
     *                                  "mensaje"   => Mensaje de error
     *                                ]
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 01-07-2020
     * 
     */
    public function ejecutaProcesosCambioPlan($arrayParametros)
    {
        $intIdServicio      = $arrayParametros["intIdServicio"];
        $intIdPlanAnterior  = $arrayParametros["intIdPlanAnterior"];
        $intIdPlanNuevo     = $arrayParametros["intIdPlanNuevo"];
        $strUsrCreacion     = $arrayParametros["strUsrCreacion"];
        $strIpCreacion      = $arrayParametros["strIpCreacion"];
        $strStatus          = str_repeat(' ', 5);
        $strMsjError        = str_repeat(' ', 1000);
        try
        {
            $strSql = " BEGIN 
              DB_INFRAESTRUCTURA.INFRK_TRANSACCIONES.P_EJECUTA_PROCESOS_CAMBIOPLAN(  :intIdServicio,
                                                                                    :intIdPlanAnterior,
                                                                                    :intIdPlanNuevo,
                                                                                    :strUsrCreacion,
                                                                                    :strIpCreacion,
                                                                                    :strStatus, 
                                                                                    :strMsjError); 
                        END;";
            
            $objStmt = $this->emInfraestructura->getConnection()->prepare($strSql);
            $objStmt->bindParam('intIdServicio', $intIdServicio);
            $objStmt->bindParam('intIdPlanAnterior', $intIdPlanAnterior);
            $objStmt->bindParam('intIdPlanNuevo', $intIdPlanNuevo);
            $objStmt->bindParam('strUsrCreacion', $strUsrCreacion);
            $objStmt->bindParam('strIpCreacion', $strIpCreacion);
            $objStmt->bindParam('strStatus', $strStatus);
            $objStmt->bindParam('strMsjError', $strMsjError);
            $objStmt->execute();
            if(strlen(trim($strStatus)) > 0)
            {
                $strStatusRespuesta     = $strStatus;
                $strMensajeRespuesta    = $strMsjError;
            }
            else
            {
                $strStatusRespuesta     = "ERROR";
                $strMensajeRespuesta    = 'No se ha podido ejecutar el proceso de cambio de plan. Por favor comuníquese con Sistemas!';
            }
        }
        catch (\Exception $e)
        {
            $strStatusRespuesta     = "ERROR";
            $strMensajeRespuesta    = 'Ha ocurrido un error inesperado y no se ha podido realizar el cambio de plan. '
                                      .'Por favor comuníquese con Sistemas!';
        }
        $arrayRespuesta = array("status"    => $strStatusRespuesta,
                                "mensaje"   => $strMensajeRespuesta);
        return $arrayRespuesta;
    }


    /**
     * Función para validar los planes de suspension.
     * 
     * @param $arrayParametros [
     *                              "intIdPlanNuevo"    => Id del plan nuevo
     *                              "strPrefijoEmpresa" => Prefijo de la empresa
     *                              "intIdServicio"     => Id del servicio
     *                          ]
     * 
     * @return array $arrayRespuesta [ 
     *                                  "status"    => 'OK' o 'ERROR'
     *                                  "mensaje"   => Mensaje de error
     *                                ]
     * 
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 04-12-2020
     * 
     */
    public function validaPlanSuspension($arrayParametros)
    {
        $intIdPlanNuevo    = $arrayParametros['intIdPlanNuevo'];
        $strPrefijoEmpresa = $arrayParametros['strPrefijoEmpresa'];
        $intIdServicio     = $arrayParametros['intIdServicio'];

        try
        {
            //Cambio de velidad de suspención
            if ($strPrefijoEmpresa !== 'MD')
            {
                //Flujo normal.
                throw new \Exception('OK');                
            }

            if ($intIdPlanNuevo === '' || $intIdServicio === '' || $intIdPlanNuevo === null || $intIdServicio === null)
            {
                //Flujo normal.                
                throw new \Exception('OK');
            }

            $objInfoPlanCabNuevo    = $this->emComercial->getRepository('schemaBundle:InfoPlanCab')->find($intIdPlanNuevo);
            $objInfoServicio        = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
            $objInfoPlanCabAnterior = is_object($objInfoServicio) ? is_object($objInfoServicio->getPlanId()) ?
                                        $objInfoServicio->getPlanId() : null : null;

            if (!is_object($objInfoPlanCabNuevo) || !is_object($objInfoPlanCabAnterior))
            {
                throw new \Exception('OK');
            }

            //Otenemos el parámetro de suspension.
            $arrayAdmiParametroDet = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->getOne("TIPO_PLAN_POR_SUSPENSION", //Nombre parametro
                                                        "TECNICO",   //Modulo
                                                        "",          //Proceso
                                                        "",          //Descripcion
                                                        "",          //Valor1
                                                        "SUSPENSION",//Valor2
                                                        "","","","");

            if (empty($arrayAdmiParametroDet) || count($arrayAdmiParametroDet) < 1)
            {
                throw new \Exception('OK');
            }

            $strPlanSuspension = strtoupper($arrayAdmiParametroDet['valor1']);

            //Validamos que plan nuevo y anterior no sean de suspension.
            if ($strPlanSuspension !== strtoupper($objInfoPlanCabNuevo->getDescripcionPlan()) &&
                $strPlanSuspension !== strtoupper($objInfoPlanCabAnterior->getDescripcionPlan()))
            {
                throw new \Exception('OK');
            }

            //Validamos si el plan nuevo o anterior es de suspension.
            if ($strPlanSuspension === strtoupper($objInfoPlanCabNuevo->getDescripcionPlan()) &&
                $strPlanSuspension === strtoupper($objInfoPlanCabAnterior->getDescripcionPlan()))
            {
                throw new \Exception('No se puede hacer el cambio de velocidad por motivos que '.
                                     'el plan nuevo y anterior son de suspension.');                                                            
            }

            //Validacion de plan normal a suspencion.
            if (strtoupper($objInfoPlanCabNuevo->getDescripcionPlan()) === $strPlanSuspension)
            {
                $arrayAdmiParametroDet = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->getOne("TIPO_PLAN_POR_SUSPENSION", //nombre parametro
                                                        "TECNICO",   //modulo
                                                        "",          //proceso
                                                        "",          //descripcion
                                                        "PLAN",      //valor1
                                                        "","","PLAN_PRO","","");

                if (($objInfoPlanCabAnterior->getTipo() === 'PRO' ||
                    strpos($objInfoPlanCabAnterior->getCodigoPlan(),$arrayAdmiParametroDet['valor2']) !== false) &&
                    strpos($objInfoPlanCabNuevo->getCodigoPlan()   ,$arrayAdmiParametroDet['valor3']) !== false)
                {
                    throw new \Exception('OK');
                }

                if (strpos($objInfoPlanCabAnterior->getCodigoPlan(),$arrayAdmiParametroDet['valor2']) === false &&
                    strpos($objInfoPlanCabNuevo->getCodigoPlan()   ,$arrayAdmiParametroDet['valor3']) !== false)
                {
                    throw new \Exception('No se puede hacer cambio de velocidad por motivos que el plan nuevo '.
                                         'es de suspension PRO, y el plan anterior no es PRO.');  
                }

                if (strpos($objInfoPlanCabAnterior->getCodigoPlan(),$arrayAdmiParametroDet['valor2']) !== false &&
                    strpos($objInfoPlanCabNuevo->getCodigoPlan()   ,$arrayAdmiParametroDet['valor3']) === false)
                {
                    throw new \Exception('No se puede hacer cambio de velocidad por motivos que el plan anterior '.
                                         'es PRO, y el plan nuevo es de suspension '.$objInfoPlanCabNuevo->getTipo());
                }

                if ($objInfoPlanCabNuevo->getTipo() === 'HOME' && $objInfoPlanCabAnterior->getTipo() !== 'HOME')
                {                    
                    throw new \Exception('No se puede hacer cambio de velocidad por motivos que el plan nuevo '.
                                        'es de suspension '.
                                        $objInfoPlanCabNuevo->getTipo().' '.
                                        'y el plan anterior es '.
                                        $objInfoPlanCabAnterior->getTipo().'.');                                                                   
                }

                if ($objInfoPlanCabNuevo->getTipo() === 'HOME' && $objInfoPlanCabAnterior->getTipo() === 'HOME')
                {
                    throw new \Exception('OK');
                }

                if ($objInfoPlanCabNuevo->getTipo() === 'PYME' && $objInfoPlanCabAnterior->getTipo() !== 'PYME')
                {               
                    throw new \Exception('No se puede hacer cambio de velocidad por motivos que el plan nuevo '.
                                        'es de suspension '.
                                        $objInfoPlanCabNuevo->getTipo().' '.
                                        'y el plan anterior es '.
                                        $objInfoPlanCabAnterior->getTipo().'.');                                                                 
                }

                if ($objInfoPlanCabNuevo->getTipo() === 'PYME' && $objInfoPlanCabAnterior->getTipo() === 'PYME')
                {
                    throw new \Exception('OK');
                }
            }
            //Validacion de plan suspencion a normal.
            elseif (strtoupper($objInfoPlanCabAnterior->getDescripcionPlan()) === $strPlanSuspension)
            {
                $arrayAdmiParametroDet = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->getOne("TIPO_PLAN_POR_SUSPENSION", //Nombre parametro
                                                        "TECNICO",    //Modulo
                                                        "",           //Proceso
                                                        "",           //Descripcion
                                                        "SUSPENSION", //Valor1
                                                        "","","PLAN_PRO","","");

                if ( strpos($objInfoPlanCabAnterior->getCodigoPlan(),$arrayAdmiParametroDet['valor2']) !== false &&
                    (strpos($objInfoPlanCabNuevo->getCodigoPlan()   ,$arrayAdmiParametroDet['valor3']) !== false ||
                    $objInfoPlanCabNuevo->getTipo() === 'PRO'))
                {
                    throw new \Exception('OK');
                }

                if (strpos($objInfoPlanCabAnterior->getCodigoPlan(),$arrayAdmiParametroDet['valor2']) !== false &&
                    strpos($objInfoPlanCabNuevo->getCodigoPlan()   ,$arrayAdmiParametroDet['valor3']) === false)
                {                    
                    throw new \Exception('No se puede hacer cambio de velocidad por motivos que el plan anterior '.
                                         'es de suspension PRO, y el plan nuevo no es PRO.');                                            
                }

                if ($objInfoPlanCabAnterior->getTipo() === 'HOME' && $objInfoPlanCabNuevo->getTipo() !== 'HOME')
                {                    
                    throw new \Exception('No se puede hacer cambio de velocidad por motivos que el plan anterior '.
                                        'es de suspension '.
                                        $objInfoPlanCabAnterior->getTipo().' '.
                                        'y el plan nuevo es '.
                                        $objInfoPlanCabNuevo->getTipo().'.');                                                                
                }

                if ($objInfoPlanCabAnterior->getTipo() === 'HOME' && $objInfoPlanCabNuevo->getTipo() === 'HOME')
                {
                    throw new \Exception('OK');
                }

                if ($objInfoPlanCabAnterior->getTipo() === 'PYME' && $objInfoPlanCabNuevo->getTipo() !== 'PYME')
                {                    
                    throw new \Exception('No se puede hacer cambio de velocidad por motivos que el plan anterior '.
                                        'es de suspension '.
                                        $objInfoPlanCabAnterior->getTipo().' '.
                                        'y el plan nuevo es '.
                                        $objInfoPlanCabNuevo->getTipo().'.');                                                                
                }

                if ($objInfoPlanCabAnterior->getTipo() === 'PYME' && $objInfoPlanCabNuevo->getTipo() === 'PYME')
                {
                    throw new \Exception('OK');
                }
            }
            else
            {
                throw new \Exception('OK');
            }
        }
        catch (\Exception $objException)
        {
            $strMensaje = '';
            $strStatus  = 'OK';

            if($objException->getMessage() != 'OK')
            {
                $strStatus  = 'ERROR';
                $strMensaje = $objException->getMessage();
            }            

            return $arrayRespuesta = array('status'  => $strStatus,
                                           'mensaje' => $strMensaje);
        }
    }
    
     /**
     * Función que otorga las promociones por cambio de plan 
     * 
     * @param $arrayParametros [
     *                              "intIdPunto"     => Id del punto
     *                              "intIdEmpresa"    => Id la empresa
     *                          ]
     * 
     * @return array $arrayRespuesta [ 
     *                                  "strStatus"    => 'OK' o 'ERROR'
     *                                  "strMsjError"   => Mensaje de error
     *                                ]
     * 
     * @author Katherine Yager <kyager@telconet.ec>
     * @version 1.0 02-09-2020
     * 
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 1.1 09-11-2020 Se realizan cambios en el método para que se considere el código promocional en proceso 
     *                        que realiza el mapeo de cambio de plan individual.
     */
    public function mapeoPromoCambioPlan($arrayParametros)
    {
        $intIdServicio           = $arrayParametros["intIdServicio"];
        $strIdTipoPromocion      = $arrayParametros["strIdTipoPromoMens"];
        $strCodigoMens           = $arrayParametros["strCodigoMens"];
        $intIdPunto              = null;
        $strCodigoGrupoPromocion = 'PROM_MENS';
        $intIdEmpresa            = $arrayParametros["intIdEmpresa"];
        $strTipoProceso          = 'EXISTENTE';
        $strStatus               = str_repeat(' ', 5);
        $strMsjError             = str_repeat(' ', 1000);
        try
        {
            $strSql = " BEGIN 
                        DB_COMERCIAL.CMKG_PROMOCIONES.P_CAMBIO_PLAN_INDIVIDUAL (:intIdPunto,
                                                                                :intIdServicio, 
                                                                                :strCodigoGrupoPromocion,
                                                                                :intIdEmpresa, 
                                                                                :strTipoProceso,
                                                                                :strCodigoMens,
                                                                                :strIdTipoPromocion,
                                                                                :strStatus, 
                                                                                :strMsjError); 
                        END;";
            
            $objStmt = $this->emComercial->getConnection()->prepare($strSql);
            $objStmt->bindParam('intIdPunto', $intIdPunto);
            $objStmt->bindParam('intIdServicio', $intIdServicio);
            $objStmt->bindParam('strCodigoGrupoPromocion', $strCodigoGrupoPromocion);
            $objStmt->bindParam('intIdEmpresa', $intIdEmpresa);
            $objStmt->bindParam('strTipoProceso', $strTipoProceso);
            $objStmt->bindParam('strCodigoMens', $strCodigoMens);
            $objStmt->bindParam('strIdTipoPromocion', $strIdTipoPromocion);
            $objStmt->bindParam('strStatus', $strStatus);
            $objStmt->bindParam('strMsjError', $strMsjError);
            $objStmt->execute();
            if(strlen(trim($strStatus)) > 0)
            {
                $strStatusRespuesta     = $strStatus;
                $strMensajeRespuesta    = $strMsjError;
          
            }
            else
            {
                $strStatusRespuesta     = "ERROR";
                $strMensajeRespuesta    = 'No se ha podido otorgar el mapeo de promociones por cambio de plan. Por favor comuníquese con Sistemas!';
            }
        }
        catch (\Exception $e)
        {
            $strStatusRespuesta     = "ERROR";
            $strMensajeRespuesta    = 'Ha ocurrido un error inesperado y no se ha podido realizar el mapeo de promociones por cambio de plan. '
                                      .'Por favor comuniquese con Sistemas!';

        }

        $arrayRespuesta = array("status"    => $strStatusRespuesta,
                                "mensaje"   => $strMensajeRespuesta);
        return $arrayRespuesta;
    }
    
    /**
     * Función que valida el servicio cuando posea beneficio adulto mayor (3era Edad Resolución 07-2021) y retorne 
     * mensaje en el caso de no cumplir con la validación cuando el parámetro APLICA_DESC_TIPO_PLAN_COMERCIAL esté en N.
     * 
     * @param $arrayParametros [
     *                              "intIdServicio"  => Id del servicio
     *                              "intIdEmpresa"   => Id la empresa
     *                              "intIdPlan"      => Id del plan
     *                         ]
     * 
     * @return array $arrayRespuesta [ 
     *                                  "strStatus"  => 'OK' o 'ERROR'
     *                                  "strMensaje" => Mensaje de salida
     *                               ]
     * 
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 1.0 23-08-2021
     */
    public function validaSolicitudAdultoMayor($arrayParametros) 
    {   
        $intIdServicio  = $arrayParametros[0]['intIdServicio']; 
        $intIdEmpresa   = $arrayParametros[0]['intIdEmpresa']; 
        $intIdPlanNuevo = $arrayParametros[0]['intIdPlan']; 
        $strStatus     = "OK";
        $strMensaje    = "";

        $arraySolicitudBeneficio = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                        ->getSolicitudAdultoMayorPorServicio(array('intIdServicio' => $intIdServicio));
        
        $intCantidadSol = $arraySolicitudBeneficio['intCantidad'];

        //Se verifica que el servicio tenga solicitud con el beneficio
        if($intCantidadSol > 0)
        {
            //Se obtiene el mensaje de validación a mostrar
            $arrayMsjTipoCategPlan = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getOne('PARAM_FLUJO_ADULTO_MAYOR',
                                                         'COMERCIAL','','','MENSAJE_VALIDACION_TIPO_CATEGORIA_PLAN','',
                                                         '','','',$intIdEmpresa);

            //Obtengo parámetro aplica descuento tipo plan comercial
            $arrayAplicaDescAdultoMayor = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getOne('PARAM_FLUJO_ADULTO_MAYOR',
                                                        'COMERCIAL','','APLICA_DESC_TIPO_PLAN_COMERCIAL',
                                                        '', '', '', '', '', $intIdEmpresa);   

            $arrayParamCategPlanComerc = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getOne('PARAM_FLUJO_ADULTO_MAYOR',
                                                        'COMERCIAL','','CATEGORIA_PLAN_ADULTO_MAYOR',
                                                        '','','PLAN_COMERCIAL','','',$intIdEmpresa);    

            $strMsjTipoCategPlan = (isset($arrayMsjTipoCategPlan["valor2"])
                                        && !empty($arrayMsjTipoCategPlan["valor2"])) ? $arrayMsjTipoCategPlan["valor2"]
                                        : 'Beneficio Adulto Mayor No aplica a Planes Comerciales ';
            
            //Se obtiene el tipo de categoría del plan nuevo a cambiarse
            $arrayTipoCategoriaPlan  = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                              ->getTipoCategoriaPlan(array('strDescripcionCaract' => "TIPO_CATEGORIA_PLAN_ADULTO_MAYOR",
                                                                           'intIdPlan'            => $intIdPlanNuevo));

            if (!$arrayTipoCategoriaPlan && !isset($arrayTipoCategoriaPlan['strValor']))
            {
                $strMsjError = "No existe característica tipo categoría en el plan. No es posible realizar cambio de plan.";
                $arrayRespuesta[] = array('strStatus'  => "ERROR", 
                                          'strMensaje' => $strMsjError);
                return $arrayRespuesta; 
            }

            if ($arrayAplicaDescAdultoMayor["valor1"] == 'N' && $arrayTipoCategoriaPlan['strValor'] == $arrayParamCategPlanComerc['valor1'])
            {
                $arrayRespuesta[] = array('strStatus'  => "ERROR", 
                                          'strMensaje' => $strMsjTipoCategPlan);
                return $arrayRespuesta;   
            } 
        }
        
        $arrayRespuesta[] = array('strStatus'  => $strStatus,
                                  'strMensaje' => $strMensaje);
        return $arrayRespuesta;
    }

    /**
     * Función que sirve para realizar la sumatoria de las capacidades por el mismo service port id para los servicios GPON_MPLS.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 21-03-2022
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.1 30-06-2022 - Se actualiza las características del servicio como capacidad, velocidad y traffic table
     *                           con la capacidad total de la sumatoria de los servicios,
     *                           y se realiza control de la velocidad de los servicios en una característica del servicio.
     * 
     * @author Jenniffer Mujica <jmujica@telconet.ec>
     * @version 1.2 07-02-2023 - Se valida tipo de olt zte, por ingreso de nuevas capacidades de los servicios
     *
     * @param Array $arrayParametros [
     *                                  'objServicio'       => objeto del servicio
     *                                  'strCodEmpresa'     => código empresa
     *                                  'strPrefijoEmpresa' => prefijo empresa
     *                                  'strUsrCreacion'    => usuario creación
     *                                  'strIpCreacion'     => ip creación
     *                               ]
     *
     * @return Array $arrayResultado [
     *                                  'status'    => estado de respuesta de la operación 'OK' o 'ERROR',
     *                                  'mensaje'   => mensaje de la operación o de error
     *                               ]
     */
    public function actualizarCapacidadDatosTNGpon($arrayParametros)
    {
        $objServicio        = $arrayParametros['objServicio'];
        $intIdEmpresa       = $arrayParametros['strCodEmpresa'];
        $strPrefijoEmpresa  = $arrayParametros['strPrefijoEmpresa'];
        $strUsrCreacion     = $arrayParametros['strUsrCreacion'];
        $strIpCreacion      = $arrayParametros['strIpCreacion'];

        $this->emComercial->getConnection()->beginTransaction();
        try
        {
            //seteo la variable para la sumatoria de capacidades
            $intSumVelocidad = 0;
            //validar objeto
            if(!is_object($objServicio))
            {
                throw new \Exception("No es válido el objeto del servicio, por favor notificar a Sistemas.");
            }
            //obtengo el servicio tecnico
            $objServicioTecnico      = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                   ->findOneByServicioId($objServicio->getId());
            //obtengo la interface elemento olt
            $objInterfaceElementoOlt = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                   ->find($objServicioTecnico->getInterfaceElementoId());
            //obtengo el elemento olt
            $objElementoOlt          = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                    ->find($objServicioTecnico->getElementoId());
            //obtener ip del elemento
            $objIpOlt                = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                    ->findOneByElementoId($objElementoOlt->getId());
            if(!is_object($objIpOlt))
            {
                throw new \Exception("No se encontró la Ip del elemento ".$objElementoOlt->getNombreElemento()." para tipo de red GPON, ".
                                     "por favor notificar a Sistemas.");
            }
            //obtengo el servicio principal
            $objCaractSerPrincipal   = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio,
                                                              'RELACION_SERVICIOS_GPON_SAFECITY',$objServicio->getProductoId());
            if(!is_object($objCaractSerPrincipal))
            {
                throw new \Exception("No se ha podido obtener la característica del servicio Datos SafeCity, ".
                                     "por favor notificar a Sistemas.");
            }
            $objServicioOnt          = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($objCaractSerPrincipal->getValor());
            //obtengo el servicio tecnico
            $objServicioTecnicoOnt   = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                   ->findOneByServicioId($objServicioOnt->getId());
            //obtengo el elemento ont
            $objElementoOnt          = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                    ->find($objServicioTecnicoOnt->getElementoClienteId());
            //datos del cliente
            $strIdentificacion       = "";
            $strNombreCliente        = "";
            //obtengo el producto
            $objProducto             = $objServicio->getProductoId();
            //obtengo el punto
            $objPunto                = $objServicio->getPuntoId();
            //obtengo la persona empresa rol
            $objInfoPersonaEmpRol    = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                          ->find($objPunto->getPersonaEmpresaRolId()->getId());
            if(is_object($objInfoPersonaEmpRol))
            {
                $strIdentificacion = $objInfoPersonaEmpRol->getPersonaId()->getIdentificacionCliente();
                $strNombreCliente  = $objInfoPersonaEmpRol->getPersonaId()->__toString();
            }
            //obtengo indice del cliente
            $objCaractIndiceCliente = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio,
                                                                                                'INDICE CLIENTE',
                                                                                                $objProducto);
            if(!is_object($objCaractIndiceCliente))
            {
                throw new \Exception("No se encontró la característica INDICE CLIENTE del servicio, por favor notificar a Sistemas.");
            }
            //obtengo service port id
            $objCaractSpidCliente   = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio,
                                                                                                'SPID',
                                                                                                $objProducto);
            if(!is_object($objCaractSpidCliente))
            {
                throw new \Exception("No se encontró la característica SPID del servicio, por favor notificar a Sistemas.");
            }
            //obtengo service port id
            $objCaractMacOntCliente = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio,
                                                                                                'MAC ONT',
                                                                                                $objProducto);
            if(!is_object($objCaractMacOntCliente))
            {
                throw new \Exception("No se encontró la característica MAC ONT del servicio, por favor notificar a Sistemas.");
            }
            //obtengo line profile name
            $objCaractLineProfile   = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio,
                                                                                                'LINE-PROFILE-NAME',
                                                                                                $objProducto);
            if(!is_object($objCaractLineProfile))
            {
                throw new \Exception("No se encontró la característica LINE PROFILE del servicio, por favor notificar a Sistemas.");
            }
            //obtengo t cont
            $objCaractTContCliente  = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio,
                                                                                                'T-CONT',
                                                                                                $objProducto);
            if(!is_object($objCaractTContCliente))
            {
                throw new \Exception("No se encontró la característica T-CONT del servicio, por favor notificar a Sistemas.");
            }
            //obtengo velocidad gpon
            $objCarVelGponCliente   = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio,
                                                                                                'VELOCIDAD_GPON',
                                                                                                $objProducto);
            if(!is_object($objCarVelGponCliente))
            {
                throw new \Exception("No se encontró la característica VELOCIDAD GPON del servicio, por favor notificar a Sistemas.");
            }
            //obtener arreglo de los productos
            $arrayDatosWsDatos      = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                        ->getOne('NUEVA_RED_GPON_TN',
                                                                                'COMERCIAL',
                                                                                '',
                                                                                '',
                                                                                'WS_CAMBIO_PLAN_DATOS',
                                                                                '',
                                                                                '',
                                                                                '',
                                                                                '',
                                                                                $intIdEmpresa);
            if(empty($arrayDatosWsDatos))
            {
                throw new \Exception("No se pudo obtener los datos de cambio de plan para los productos de DATOS ".
                                     "para los servicios SafeCity, por favor notificar a Sistemas.");
            }
            //seteo opcion ws datos
            $strOpcionCambioPlan = $arrayDatosWsDatos['valor2'];
            //seteo estado del servicio para el ws datos
            $strEstadoServicio   = $arrayDatosWsDatos['valor5'];
            //obtener estados permitidos sumatoria
            $arrayEstadosPermitidos = array();
            $arrayParametrosEstados = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                        ->get('NUEVA_RED_GPON_TN',
                                                                            'COMERCIAL',
                                                                            '',
                                                                            '',
                                                                            'ESTADOS_SERVICIOS_PERMITIDOS_TOTAL_BW',
                                                                            '',
                                                                            '',
                                                                            '',
                                                                            '',
                                                                            $intIdEmpresa);
            if(empty($arrayParametrosEstados) || !is_array($arrayParametrosEstados))
            {
                throw new \Exception("No se pudo obtener los parámetros de estados para la sumatoria de capacidades ".
                                     "para los servicios SafeCity, por favor notificar a Sistemas.");
            }
            foreach($arrayParametrosEstados as $arrayDetalles)
            {
                $arrayEstadosPermitidos[] = $arrayDetalles['valor2'];
            }
            //obtener los servicios
            $arrayServiciosPunto = $this->emComercial->getRepository("schemaBundle:InfoServicio")
                                                ->createQueryBuilder('s')
                                                ->join("s.productoId", "p")
                                                ->join("s.puntoId", "pu")
                                                ->where("pu.id              = :puntoId")
                                                ->andWhere("p.nombreTecnico = :nombreTecnico")
                                                ->andWhere("s.estado       IN (:estados)")
                                                ->setParameter('puntoId',       $objPunto->getId())
                                                ->setParameter('nombreTecnico', $objProducto->getNombreTecnico())
                                                ->setParameter('estados',       array_values($arrayEstadosPermitidos))
                                                ->orderBy('s.id', 'ASC')
                                                ->getQuery()
                                                ->getResult();
            foreach($arrayServiciosPunto as $objServicioPunto)
            {
                //obtengo tipo red
                $objCaractTipoRed = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioPunto,
                                                                                              'TIPO_RED',
                                                                                              $objServicioPunto->getProductoId());
                //obtengo indice del cliente
                $objCarIndCliSer  = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioPunto,
                                                                                              'INDICE CLIENTE',
                                                                                              $objServicioPunto->getProductoId());
                //obtengo service port
                $objCaractSpidSer = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioPunto,
                                                                                              'SPID',
                                                                                              $objServicioPunto->getProductoId());
                //obtengo mac ont
                $objCarMacOntSer  = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioPunto,
                                                                                              'MAC ONT',
                                                                                              $objServicioPunto->getProductoId());
                //obtengo t cont
                $objCarTContSer = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioPunto,
                                                                                            'T-CONT',
                                                                                            $objServicioPunto->getProductoId());
                //obtengo velocidad gpon
                $objCarVelGponSer = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioPunto,
                                                                                              'VELOCIDAD_GPON',
                                                                                              $objServicioPunto->getProductoId());
                //obtengo velocidad por control de actualizar las características
                $objCarVelocidadSer = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioPunto,
                                                                                               'VELOCIDAD',
                                                                                               $objServicioPunto->getProductoId());
                //verificar si el servicio es tipo red GPON
                $booleanTipoRedGponPun = false;
                if(is_object($objCaractTipoRed))
                {
                    $arrayParVerTipoRedPun = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                                            ->getOne('NUEVA_RED_GPON_TN',
                                                                                                    'COMERCIAL',
                                                                                                    '',
                                                                                                    'VERIFICAR TIPO RED',
                                                                                                    'VERIFICAR_GPON',
                                                                                                    $objCaractTipoRed->getValor(),
                                                                                                    '',
                                                                                                    '',
                                                                                                    '');
                    if(isset($arrayParVerTipoRedPun) && !empty($arrayParVerTipoRedPun))
                    {
                        $booleanTipoRedGponPun = true;
                    }
                }
                //verificar servicio
                if($booleanTipoRedGponPun && is_object($objCarIndCliSer)
                   && is_object($objCaractSpidSer) && is_object($objCarMacOntSer)
                   && is_object($objCarVelGponSer) && is_object($objCarTContSer)
                   && $objCarIndCliSer->getValor()  == $objCaractIndiceCliente->getValor()
                   && $objCaractSpidSer->getValor() == $objCaractSpidCliente->getValor()
                   && $objCarMacOntSer->getValor()  == $objCaractMacOntCliente->getValor()
                   && $objCarTContSer->getValor()   == $objCaractTContCliente->getValor())
                {
                    $intSumVelocidad += is_object($objCarVelocidadSer) ? $objCarVelocidadSer->getValor()
                                        : $objCarVelGponSer->getValor();
                }
            }
            //verificar si se realiza el cambio de plan del total de las capacidades
            if( $intSumVelocidad != $objCarVelGponCliente->getValor() )
            {                
                //parametro para traffic-table anterior
                $intVelocidadAnterior    = $intSumVelocidad - $objCarVelGponCliente->getValor();
                $arrayParTrafficAnterior = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                                        ->getOne("MAPEO_VELOCIDAD_TRAFFIC_TABLE_GPON",
                                                                                                "TECNICO",
                                                                                                "",
                                                                                                "MAPEO_VELOCIDAD_TRAFFIC_TABLE_GPON",
                                                                                                $intVelocidadAnterior,
                                                                                                "",
                                                                                                "",
                                                                                                "",
                                                                                                "",
                                                                                                $intIdEmpresa);
                if(!isset($arrayParTrafficAnterior) || empty($arrayParTrafficAnterior['valor2'])
                    || empty($arrayParTrafficAnterior['valor3']))
                {
                    throw new \Exception("No se encontró el traffic-table($intVelocidadAnterior) del servicio con tipo de red GPON, ".
                                         "por favor notificar a Sistemas.");
                }
                
                //parametro para traffic-table nuevo
                $arrayParTrafficNuevo = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                                        ->getOne("MAPEO_VELOCIDAD_TRAFFIC_TABLE_GPON",
                                                                                                "TECNICO",
                                                                                                "",
                                                                                                "MAPEO_VELOCIDAD_TRAFFIC_TABLE_GPON",
                                                                                                $intSumVelocidad,
                                                                                                "",
                                                                                                "",
                                                                                                "",
                                                                                                "",
                                                                                                $intIdEmpresa);
                if(!isset($arrayParTrafficNuevo) || empty($arrayParTrafficNuevo['valor2'])
                    || empty($arrayParTrafficNuevo['valor3']))
                {
                    throw new \Exception("No se encontró el traffic-table($intSumVelocidad) del servicio con tipo de red GPON, ".
                                         "por favor notificar a Sistemas.");
                }


                //marca del olt
                $strMarcaElemento = $objElementoOlt->getModeloElementoId()->getMarcaElementoId()->getNombreMarcaElemento();
                
                $strTrafficTableAnterior = $arrayParTrafficAnterior['valor2'];
                $strTrafficTableNuevo    = $arrayParTrafficNuevo['valor2'];
                
                //se setea datos por marca de olt
                if($strMarcaElemento === 'ZTE')
                {
                    //seteo datos del plan
                    $strTrafficTableAnterior = $arrayParTrafficAnterior['valor3'];
                    $strTrafficTableNuevo    = $arrayParTrafficNuevo['valor3'];
                }

                //seteo datos del plan
                $arrayDatosPlanAnt[] = array(
                    "t_cont_datos"        => $objCaractTContCliente->getValor(),
                    "traffic_table_datos" => $strTrafficTableAnterior,
                );
                $arrayDatosPlanNue[] = array(
                    "t_cont_datos_nuevo"        => $objCaractTContCliente->getValor(),
                    "traffic_table_datos_nuevo" => $strTrafficTableNuevo,
                );

                //seteo datos ws
                $arrayDatos["datos_plan_anterior"] = $arrayDatosPlanAnt;
                $arrayDatos["datos_plan_nuevo"]    = $arrayDatosPlanNue;
                $arrayDatos["estado_servicio"] = $strEstadoServicio;
                $arrayDatos["ip_olt"]          = $objIpOlt->getIp();
                $arrayDatos["line_profile"]    = $objCaractLineProfile->getValor();
                $arrayDatos["login_aux"]       = $objServicio->getLoginAux();
                $arrayDatos["mac_ont"]         = $objCaractMacOntCliente->getValor();
                $arrayDatos["modelo_olt"]      = $objElementoOlt->getModeloElementoId()->getNombreModeloElemento();
                $arrayDatos["nombre_olt"]      = $objElementoOlt->getNombreElemento();
                $arrayDatos["ont_id"]          = $objCaractIndiceCliente->getValor();
                $arrayDatos["puerto_olt"]      = $objInterfaceElementoOlt->getNombreInterfaceElemento();
                $arrayDatos["serial_ont"]      = $objElementoOnt->getSerieFisica();
                $arrayDatos["service_port_id"] = $objCaractSpidCliente->getValor();
                $arrayDatos["tiene_datos"]     = "S";
                $arrayDatos["tiene_internet"]  = "N";
                //datos ws
                $arrayDatosMiddleware = array(
                    'nombre_cliente'       => $strNombreCliente,
                    'login'                => $objPunto->getLogin(),
                    'identificacion'       => $strIdentificacion,
                    'datos'                => $arrayDatos,
                    'opcion'               => $strOpcionCambioPlan,
                    'ejecutaComando'       => $this->ejecutaComando,
                    'usrCreacion'          => $strUsrCreacion,
                    'ipCreacion'           => $strIpCreacion,
                    'comandoConfiguracion' => $this->ejecutaComando,
                    'empresa'              => $strPrefijoEmpresa,
                );
                //se ejecuta ws de RDA
                $arrayResultado = $this->rdaMiddleware->middleware(json_encode($arrayDatosMiddleware));
                //actualizar la data tecnica de los servicios de cámaras
                if($arrayResultado['status'] == "OK")
                {
                    $strCapacidadGpon   = "";
                    $arrayCapacidadGpon = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                    ->getOne('PROD_VELOCIDAD_GPON',
                                                                            'COMERCIAL',
                                                                            '',
                                                                            '',
                                                                            $intSumVelocidad,
                                                                            'MB',
                                                                            '',
                                                                            '',
                                                                            '',
                                                                            $intIdEmpresa);
                    if(isset($arrayCapacidadGpon) && !empty($arrayCapacidadGpon))
                    {
                        $strCapacidadGpon = $arrayCapacidadGpon['valor3'];
                    }
                    //actualizar caracteristicas en los servicios
                    foreach($arrayServiciosPunto as $objServicioPunto)
                    {
                        //obtengo traffic table
                        $objCarTraTableSer = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioPunto,
                                                                                                'TRAFFIC-TABLE',
                                                                                                $objServicioPunto->getProductoId());
                        if(is_object($objCarTraTableSer))
                        {
                            $objCarTraTableSer->setEstado("Eliminado");
                            $objCarTraTableSer->setUsrUltMod($strUsrCreacion);
                            $objCarTraTableSer->setFeUltMod(new \DateTime('now'));
                            $this->emComercial->persist($objCarTraTableSer);
                            $this->emComercial->flush();
                        }
                        //se graba caracteristica traffic table
                        $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicioPunto,
                                                                                    $objServicioPunto->getProductoId(),
                                                                                    "TRAFFIC-TABLE",
                                                                                    $strTrafficTableNuevo,
                                                                                    $strUsrCreacion);
                        //obtengo velocidad gpon
                        $objCarVelGponSer = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioPunto,
                                                                                                      'VELOCIDAD_GPON',
                                                                                                      $objServicioPunto->getProductoId());
                        //obtengo velocidad por control de sumatoria
                        $objCarVelocidadSer = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioPunto,
                                                                                                        'VELOCIDAD',
                                                                                                        $objServicioPunto->getProductoId());
                        if(!is_object($objCarVelocidadSer))
                        {
                            //se graba caracteristica velocidad por control de sumatoria
                            //esta caracteristica se guarda la velocidad inicial del servicio
                            $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicioPunto,
                                                                                           $objServicioPunto->getProductoId(),
                                                                                           "VELOCIDAD",
                                                                                           $objCarVelGponSer->getValor(),
                                                                                           $strUsrCreacion);
                        }
                        //eliminar caracteristica velocidad gpon
                        if(is_object($objCarVelGponSer))
                        {
                            $objCarVelGponSer->setEstado("Eliminado");
                            $objCarVelGponSer->setUsrUltMod($strUsrCreacion);
                            $objCarVelGponSer->setFeUltMod(new \DateTime('now'));
                            $this->emComercial->persist($objCarVelGponSer);
                            $this->emComercial->flush();
                        }
                        //se graba caracteristica velocidad gpon
                        $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicioPunto,
                                                                                       $objServicioPunto->getProductoId(),
                                                                                       "VELOCIDAD_GPON",
                                                                                       $intSumVelocidad,
                                                                                       $strUsrCreacion);
                        //obtengo capacidad uno
                        $objCarCapUnoSer = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioPunto,
                                                                                                     'CAPACIDAD1',
                                                                                                     $objServicioPunto->getProductoId());
                        if(is_object($objCarCapUnoSer))
                        {
                            $objCarCapUnoSer->setEstado("Eliminado");
                            $objCarCapUnoSer->setUsrUltMod($strUsrCreacion);
                            $objCarCapUnoSer->setFeUltMod(new \DateTime('now'));
                            $this->emComercial->persist($objCarCapUnoSer);
                            $this->emComercial->flush();
                        }
                        //se graba caracteristica capacidad
                        $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicioPunto,
                                                                                       $objServicioPunto->getProductoId(),
                                                                                       "CAPACIDAD1",
                                                                                       $strCapacidadGpon,
                                                                                       $strUsrCreacion);
                        //obtengo capacidad dos
                        $objCarCapDosSer = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioPunto,
                                                                                                     'CAPACIDAD2',
                                                                                                     $objServicioPunto->getProductoId());
                        if(is_object($objCarCapDosSer))
                        {
                            $objCarCapDosSer->setEstado("Eliminado");
                            $objCarCapDosSer->setUsrUltMod($strUsrCreacion);
                            $objCarCapDosSer->setFeUltMod(new \DateTime('now'));
                            $this->emComercial->persist($objCarCapDosSer);
                            $this->emComercial->flush();
                        }
                        //se graba caracteristica capacidad
                        $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicioPunto,
                                                                                       $objServicioPunto->getProductoId(),
                                                                                       "CAPACIDAD2",
                                                                                       $strCapacidadGpon,
                                                                                       $strUsrCreacion);
                        //ingreso historial
                        $strObservacionServicio = "Se actualiza las características del servicio, ".
                                                  "por la sumatoria de las velocidades.";
                        $this->servicioGeneral->ingresarServicioHistorial($objServicioPunto, $objServicioPunto->getEstado(),
                                                                          $strObservacionServicio, $strUsrCreacion, $strIpCreacion);
                    }
                    //realizar commit
                    if ($this->emComercial->getConnection()->isTransactionActive())
                    {
                        $this->emComercial->getConnection()->commit();
                    }
                }
            }
            else
            {
                $arrayResultado = array(
                    "status"  => "OK",
                    "mensaje" => "No se realiza el cambio de plan del total de la capacidad porque solo existe un servicio asociado."
                );
            }
            //cerrar la conexion
            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->close();
            }
        }
        catch(\Exception $ex)
        {
            $arrayResultado = array(
                "status"  => "ERROR",
                "mensaje" => $ex->getMessage()
            );
            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
                $this->emComercial->getConnection()->close();
            }
            $this->utilService->insertError("Telcos+",
                                            "InfoCambiarPlanService.actualizarCapacidadDatosTNGpon",
                                            $ex->getMessage(),
                                            $strUsrCreacion,
                                            $strIpCreacion
                                           );
        }
        return $arrayResultado;
    }
    
    /**
    * Función que se encarga del consumo de microservicio ms-comp-cliente.
    * 
    * @author Alex Arreaga <atarreaga@telconet.ec> 
    * @version 1.0 12-12-2022
    * 
    * @param array $arrayParametros ["strToken"         :string:  Token cas,
    *                                "usrCreacion"      :string:  Usuario creación, 
    *                                "clienteIp"        :string:  Ip creación,
    *                                "idEmpresa"        :integer: Código empresa,
    *                                "idPersona"        :integer: Id persona,
    *                                "precioPlanActual" :float:   Precio plan actual,
    *                                "idPlanCabNuevo"   :integer: Id plan nuevo ]
    * @return $arrayResultado
    */
    public function validacionPorCambioPlanUpMs($arrayParametros)  
    {
        $arrayResultado = array();
        
        try 
        {
            $objOptions = array(CURLOPT_SSL_VERIFYPEER => false,
                                CURLOPT_HTTPHEADER     => array('Content-Type: application/json',
                                                                'tokencas: ' . $arrayParametros['strTokenCas']
                                                                )
                                );
            
            $strJsonData       = json_encode($arrayParametros);
            $strUrl            = $this->strUrlValidacionCambioPlanUpMs;
            $arrayResponseJson = $this->serviceRestClient->postJSON( $strUrl , $strJsonData, $objOptions);
            $strJsonRespuesta  = json_decode($arrayResponseJson['result'], true);
            
            if (isset($strJsonRespuesta['status']) && isset($strJsonRespuesta['message'])) 
            {
                $arrayResponse  = array('strStatus'  => $strJsonRespuesta['status'],
                                        'strMensaje' => $strJsonRespuesta['message'],
                                        'objData'    => $strJsonRespuesta['data'] );
                $arrayResultado = $arrayResponse;
            }
            else 
            {
                $arrayResultado['strStatus']  = "ERROR";
                $arrayResultado['strMensaje'] = empty($strJsonRespuesta['message']) ? 
                                                "No existe conectividad con el WS ms-comp-cliente." : $strJsonRespuesta['message'];
            }
        } 
        catch (\Exception $ex) 
        {
            $strRespuesta   = "Error al ejecutar las validaciones MS por cambio de plan. Favor Notificar a Sistemas. ".$ex->getMessage();
            $arrayResultado = array('strMensaje' => $strRespuesta);
            $this->utilService->insertError('Telcos+',
                                            'InfoCambiarPlanService.validacionPorCambioPlanMs',
                                            'Error InfoCambiarPlanService.validacionPorCambioPlanMs: '. $ex->getMessage(),
                                            $arrayParametros['usrCreacion'],
                                            $arrayParametros['clienteIp']);
        }
        
        return $arrayResultado;
    }

}
