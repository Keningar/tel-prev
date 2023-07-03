<?php


namespace telconet\planificacionBundle\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoPersona;
use telconet\schemaBundle\Entity\AdmiProducto;
use telconet\schemaBundle\Entity\AdmiParametroDet;
use telconet\schemaBundle\Entity\AdmiDepartamento;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoServicio;
use telconet\schemaBundle\Entity\InfoCaso;
use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\InfoDocumentoRelacion;
use telconet\schemaBundle\Entity\InfoTareaCaracteristica;
use telconet\schemaBundle\Entity\InfoTareaSeguimiento;
use telconet\schemaBundle\Entity\InfoDetalleSolCaract;
use telconet\schemaBundle\Entity\AdmiCaracteristica;
use telconet\schemaBundle\Entity\AdmiProductoCaracteristica;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRol;
use telconet\schemaBundle\Entity\InfoServicioProdCaract;
use telconet\schemaBundle\Entity\AdmiCanton;
use telconet\schemaBundle\Form\InfoDetalleSolicitudType;
use Symfony\Component\HttpFoundation\Response;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;
use telconet\schemaBundle\Service\UtilService;

use Symfony\Component\HttpFoundation\JsonResponse;
use telconet\schemaBundle\Entity\ReturnResponse;
/**
 * Description of CoordinarProyectoController
 *
 * @author mdleon
 */
class CoordinarProyectoController extends Controller implements TokenAuthenticatedController
{

     /**
     * @Secure(roles="ROLE_456-1")
     *
     * Documentación para la función 'indexAction'.
     *
     * Función que carga la pantalla  de proyectos.
     * 
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.0 03-01-2022
     *
     * @author Manuel Carpio <mcarpio@telconet.ec>
     * @version 1.1 21-03-2023 Se agrega codigo de rol para generacion de reporte excel.
     * 
     * @return render Redirecciona al index de la opción.
     */
    public function indexAction()
    {
        $objRequest     = $this->getRequest();
        $strUsrCreacion = $objRequest->getSession()->get('user');
        $strIpCreacion  = $objRequest->getClientIp();
        $serviceUtil    = $this->get('schema.Util');
        $arrayRolesPermitidos = array();
        try
        {
            if($this->get('security.context')->isGranted('ROLE_456-1'))
            {
                $arrayRolesPermitidos[] = 'ROLE_456-1';
            }
            if(true === $this->get('security.context')->isGranted('ROLE_145-37'))
            {
                $arrayRolesPermitidos[] = 'ROLE_145-37';
            }
         
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError('Telcos', 'indexAction', $e->getMessage(), $strUsrCreacion, $strIpCreacion);
        }
        return $this->render('planificacionBundle:CoordinarProyecto:index.html.twig', array('rolesPermitidos' => $arrayRolesPermitidos));
    }
    
    /* 
     * Documentación para la función 'gridAction'.
     *
     * Función que muestra los estados de los servicios con sus respectivas acciones.
     *
     * @author mdleon <mdleon@telconet.ec>
     * @version 1.0 01-02-2023
     * 
     * @return Response se retorna los servicios con sus acciones.
     *
     */
    public function gridAction()
    {
        $serviceUtil = $this->get('schema.Util');
        $objRespuesta   = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $emGeneral   = $this->getDoctrine()->getManager("telconet_general");
        $objRequest       = $this->getRequest();

        $objPeticion         = $this->get('request');
        $objSession          = $this->get( 'session' );

        $strCodEmpresa       = $objSession->get('idEmpresa');
        $strPrefijoEmpresa   = $objSession->get('prefijoEmpresa');
        $strUsrCreacion      = $objSession->get('user');
        $strIpClient      = $objPeticion->getClientIp();
        $intIdDepartamento= $objSession->get('idDepartamento');
        
        $emComercial      = $this->get('doctrine')->getManager('telconet');
        $emInfraestructura= $this->get('doctrine')->getManager('telconet_infraestructura');
        $objEmSoporte     = $this->get('doctrine')->getManager('telconet_soporte');
        $emNaf            = $this->get('doctrine')->getManager("telconet_naf");

        $arrayDatosBusqueda    = array();
        $arrayDatosBusqueda['fechaDesdePlanif'] = "";
        $arrayDatosBusqueda['fechaHastaPlanif'] = "";
        $arrayDatosBusqueda['fechaDesdeIngOrd'] = "";
        $arrayDatosBusqueda['fechaHastaIngOrd'] = "";
        $arrayDatosBusqueda['emNaf']            = $emNaf;
        $arrayDatosBusqueda['tipoSolicitud']    = "";
        $arrayDatosBusqueda['estado']           = "";
        $arrayDatosBusqueda['ciudad']           = "";
        $arrayDatosBusqueda['idSector']         = "";
        $arrayDatosBusqueda['identificacion']   = "";
        $arrayDatosBusqueda['vendedor']         = "";
        $arrayDatosBusqueda['nombres']          = "";
        $arrayDatosBusqueda['apellidos']        = "";
        $arrayDatosBusqueda['login']            = "";
        $arrayDatosBusqueda['descripcionPunto'] = "";
        $arrayDatosBusqueda['estadoPunto']      = "";
        $arrayDatosBusqueda['idServicio']       = $objRequest->get('servicioId');
        $arrayDatosBusqueda['codEmpresa']       = $strCodEmpresa;
        $arrayDatosBusqueda['prefijoEmpresa']   = $strPrefijoEmpresa;
        $arrayDatosBusqueda['ultimaMilla']      = "";
        $arrayDatosBusqueda['usrCreacion']      = $strUsrCreacion;
        $arrayDatosBusqueda['start']            = "0";
        $arrayDatosBusqueda['limit']            = "14";
        $arrayDatosBusqueda['emComercial']      = $emComercial;
        $arrayDatosBusqueda['emInfraestructura']= $emInfraestructura;
        $arrayDatosBusqueda['tipoConsulta']     = "GRID";
        $arrayDatosBusqueda['grupo']            = '';
        $arrayDatosBusqueda['proyecto']         = 'SI';
        $arrayDatosBusqueda["ociCon"] = array('userComercial' => $this->container->getParameter('user_comercial'),
                                         'passComercial' => $this->container->getParameter('passwd_comercial'),
                                         'databaseDsn' => $this->container->getParameter('database_dsn'));
        try
        {
            $objDepartamento = $emGeneral->getRepository("schemaBundle:AdmiDepartamento")->find($intIdDepartamento);
            $arrayProductos          = array();
            $arrayProductosExcepcion = array();
            $strRegion               = '';

            if(is_object($objDepartamento))
            {
                //Si el usuario pertence a IPCCL2 se muestra la informacion ligada a DATACENTER
                $arrayInfoVisualizacion   =   $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->get('VISUALIZACION DATOS POR DEPARTAMENTO', 
                                                              'COMERCIAL', 
                                                              '',
                                                              'COORDINAR',
                                                              $objDepartamento->getNombreDepartamento(),
                                                              '',
                                                              '',
                                                              '', 
                                                              '', 
                                                              $objSession->get('idEmpresa'));
                if(!empty($arrayInfoVisualizacion))
                {
                    //Si no es enviado como parametro setea por default la oficina en sesion
                    if(empty($arrayDatosBusqueda['ciudad']) && $objSession->get('idEmpresa') != 18)
                    {
                        $intIdOficina          = $objSession->get('idOficina');

                        $objOficina = $emComercial->getRepository("schemaBundle:InfoOficinaGrupo")->find($intIdOficina);

                        if(is_object($objOficina))
                        {
                            $objCanton = $emComercial->getRepository("schemaBundle:AdmiCanton")->find($objOficina->getCantonId());

                            if(is_object($objCanton))
                            {
                                $strRegion              = $objCanton->getProvinciaId()->getRegionId()->getNombreRegion();
                                $arrayDatosBusqueda['grupo'] = 'DATACENTER';
                            }
                        }
                    }

                    foreach($arrayInfoVisualizacion as $array)
                    {
                        $arrayProductos[]     = array($array['valor2']);
                        $arrayTipoOrden[]     = array($array['valor2']);
                        $arrayTipoSolicitud[] = array($array['valor3']);
                    }

                }
                else//Filtra los productos que no seben ser mostrados en flujos normales con donde solo interviene PyL
                {
                    $arrayInfoNoVisualizacion   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->get('EXCEPCION DE PRODUCTOS EN FLUJOS NORMALES', 
                                                                  'COMERCIAL', 
                                                                  '',
                                                                  'COORDINAR',
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
                            $arrayProductosExcepcion[] = array($array['valor1']);
                        }
                    }
                }
            }

            $arrayDatosBusqueda['region']                            = $strRegion;
            $arrayDatosBusqueda['arrayDescripcionProducto']          = $arrayProductos;
            $arrayDatosBusqueda['arrayDescripcionProductoExcepcion'] = $arrayProductosExcepcion;
            $arrayDatosBusqueda['serviceTecnico']                    = $this->get('tecnico.InfoServicioTecnico');
            $arrayDatosBusqueda['planificarService']                 = $this->get('planificacion.planificar');
            $arrayDatosBusqueda['serviceInfoServicio']               = $this->get('comercial.infoservicio');
            $arrayDatosBusqueda['objEmSoporte']                      = $objEmSoporte;
            $arrayDatosBusqueda['tipoSolicitudPeticion']             = 'SOLICITUD PLANIFICACION';
            $arrayParamsVerifPerfilesCoordinar                  = array("strPrefijoSesion"      => $strPrefijoEmpresa,
                                                                        "strUserSesion"         => $strUsrCreacion,
                                                                        "strCodEmpresaSesion"   => $strCodEmpresa,
                                                                        "strProcesoEjecutante"  => 'GESTION_COORDINAR');
            $arrayDatosBusqueda['arrayParamsVerifPerfilesCoordinar'] = $arrayParamsVerifPerfilesCoordinar;

            $objJson = $this->getDoctrine()->getManager("telconet")
                                           ->getRepository('schemaBundle:InfoDetalleSolicitud')
                                           ->generarJsonCoordinar($arrayDatosBusqueda);
            $objJsonDatos = json_decode($objJson,true);
            $intTotal     = $objJsonDatos['total'];
            if($intTotal<=0)
            {
                $objJson = $this->detalleServicio($arrayDatosBusqueda);
            }
        }
        catch(\Exception $ex)
        {
            $serviceUtil->insertError('Telcos+', 'CoordinarProyectoController->gridAction', $ex->getMessage(), $strUsrCreacion, $strIpClient);
        }
        $objRespuesta->setContent($objJson);

        return $objRespuesta;
    }
    
    /**
     * getTareasClientesAction obtiene el listado de tareas por login.
     *
     * @author Manuel Adrian <mcarpio@telconet.ec>
     * @version 1.0 6-3-2023
     *
     * @return response
     */
    public function getTareasClientesAction()
    {
        ini_set('max_execution_time', 3000000);
        $objResponse        = new Response();
        $objResponse->headers->set('Content-Type', 'text/plain');
        $emComercial        = $this->getDoctrine()->getManager("telconet");       
        $objRequest         = $this->getRequest();
        $strLogin           = $objRequest->get('login');        
        
        $arrayRespuesta = $emComercial->getRepository("schemaBundle:InfoPunto")
                                      ->getTareasClientes(array('strLogin'    => $strLogin,
                                                                'strEstado'   => '',
                                                                'strFechaIni' => '',
                                                                'strFechaFin' => '',
                                                                'strMostrarAsignado'  => 'S',
                                                                'strVerTareasValidas' => 'S'));
        
        return new JsonResponse($arrayRespuesta);      
    }

    /**
     *
     * Método que ejecuta algoritmo de autorización automática o no en exceso de material.
     *
     * @author Manuel Adrian <mcarpio@telconet.ec>
     * @version 1.0 20-3-2023
     * 
     * @return response
     */
    public function getValidadorExcedenteMaterialAction()
    {
        ini_set('max_execution_time', 3000000);
        $objResponse              = new Response();
        $objResponse->headers->set('Content-Type', 'text/plain');
        $emComercial              = $this->getDoctrine()->getManager();
        $objRequest               = $this->getRequest();
        $objSession               = $objRequest->getSession();
        $intId                    = $objRequest->get('intIdServicio');
        $strClienteIp             = $objRequest->getClientIp();
        $strUsrCreacion           = $objSession->get('user');
        $strCodEmpresa            = $objSession->get('idEmpresa');
        $serviceUtil              = $this->get('schema.Util');
        $serviceEnvioPlantilla    = $this->get('soporte.EnvioPlantilla');
        $serviceSolicitud         = $this->get('comercial.Solicitudes');
        $serviceAutorizaciones    = $this->get('comercial.Autorizaciones');
        $emGeneral                = $this->getDoctrine()->getManager("telconet_general");
        $intIdDetalleSolicitud    = $objRequest->get('detalleSolId');
        $strEvidencia             = $objRequest->get('strEvidencia');
        $floatPrecioFibra         = $objRequest->get('precioFibra');
        $intMetrosFibra           = $objRequest->get('metrosFibra');
        $floatPrecioObraCivil     = $objRequest->get('precioObraCivil');
        $floatPrecioOtrosMate     = $objRequest->get('precioOtrosMate');
        
        // Total de Precio de obra civil y otros materiales
        $floatSubTotalOtrosClientes = $objRequest->get('subTotalOtrosClientes'); 
        // % que el cliente cancela
        $floatCanceladoPorCliente   = $objRequest->get('canceladoPorCliente');   
        // $ valor dependiente del % que el cliente asume
        $floatAsumeCliente          = $objRequest->get('asumeCliente');          
        $floatAsumeEmpresa          = $objRequest->get('asumeEmpresa');
        // Total a pagar, suma de los otros totales
        $floatTotalPagar            = $objRequest->get('totalPagar');            
        $strObservacion             = $objRequest->get('observacion');
        $strModulo                  = $objRequest->get('modulo');
        $strAccion                  = 'validaExcedenteMaterial';
        
        $floatSumaExcedente    = floatval($floatTotalPagar);
        $floatValidacionMRC    = 0; 
        $floatMCREnlaceActual  = 0;
        $boolAutorizado        = false;
        $boolExisteSolicitud   = false;
        $boolModuloQueValida   = false;
        $strRespuesta          = '';
        $boolAplicativoValida  = false;
        $boolPreplanificaAutomaticamente = false;

        // ----activa o no la validación de excedentes de materiales
        if($strModulo =='PLANIFICACION')
        {
            $boolModuloQueValida   = false;
        }
        else if(($strModulo =='COMERCIAL'))
        {
            $boolModuloQueValida   = true;
        }

        try
        {
        $emComercial->getConnection()->beginTransaction();
        $objServicio     = $emComercial->getRepository('schemaBundle:InfoServicio')->find($intId);
        if(is_object($objServicio))
        {
            $strEstadoServicio     = $objServicio->getEstado();
        }

        
        $objParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                               ->findOneBy(array("descripcion"=>'Validaciones para excedente de material', 
                                                 "modulo"=>'PLANIFICACIÓN',
                                                 "estado"=>'Activo'));
        if(is_object($objParametroCab))
        {
 
        $objParamValidacion1 = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                         ->findOneBy(array(
                                            "descripcion" => 'VALIDACION1',
                                            "parametroId" => $objParametroCab->getId(),
                                            "estado"      => 'Activo'));

        $objParamValidacion2 = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                         ->findOneBy(array(
                                            "descripcion" => 'VALIDACION2',
                                            "parametroId" => $objParametroCab->getId(),
                                            "estado"      => 'Activo' ));
        }
        else
        {
            throw new \Exception(': NO SE ENCONTRÓ  UN PARÁMETRO ; <br> <b>Validaciones para excedente de material</b>');
        }

        $objParametroCabCondicional = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                ->findOneBy(array("descripcion"=>'ESTADO PARA CONDICIONAR LA PREPLANIFICACION EN EXCEDENTES', 
                                                                    "modulo"=>'COMERCIAL',
                                                                    "estado"=>'Activo'));
        if(is_object($objParametroCabCondicional))
        {                                                        
            $objParamDetCondicional = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->findOneBy(array(
                                                                "descripcion" => 'CONDICIONAR PREPLANIFICACION EXCEDENTES',
                                                                "parametroId" => $objParametroCabCondicional->getId(),
                                                                "estado"      => 'Activo'));
            //Variable de decisión para preplanificación.
            if(!is_object($objParamDetCondicional))
            {
                throw new \Exception(': NO SE ENCONTRÓ  UN PARÁMETRO ; <br> <b>CONDICIONAR PREPLANIFICACION EXCEDENTES</b>');
            }
            else
            {
                $strEstadoCondicional  = $objParamDetCondicional->getValor1();
            }
        }
        else
        {
            throw new \Exception(': NO SE ENCONTRÓ  UN PARÁMETRO ; <br> <b>ESTADO PARA CONDICIONAR LA PREPLANIFICACION EN EXCEDENTES</b>');
        }


        $objParametroCabCodigo = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                ->findOneBy(array("descripcion"=>'INFORMACIÓN DEL MATERIAL PARA FACTURACIÓN', 
                                                                    "modulo"=>'COMERCIAL',
                                                                    "estado"=>'Activo'));
        if(is_object($objParametroCabCodigo))
        {
            $objParamDetCodigo = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->findOneBy(array("descripcion" => 'CODIGO DE MATERIAL DE FIBRA OPTICA',
                                                                  "parametroId" => $objParametroCabCodigo->getId(),
                                                                  "estado"      => 'Activo'));
            //Variable del código del material para insertar a la infodetalleSolMaterial.
            if(!is_object($objParamDetCodigo))
            {
                throw new \Exception(': NO SE ENCONTRÓ  UN PARÁMETRO ; <br> <b>CODIGO DE MATERIAL DE FIBRA OPTICA</b>');
            }
            else
            {    
                $strCodigoMaterial  = $objParamDetCodigo->getValor1();
            }
        }
        else
        {
            throw new \Exception(': NO SE ENCONTRÓ UN PARÁMETRO <br> <b>INFORMACIÓN DEL MATERIAL PARA FACTURACIÓN</b>');
        }

        $objCaracteristicaFibra  = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneByDescripcionCaracteristica('METRAJE FACTIBILIDAD PRECIO');

        $objCaracteristicaFibraMetros  = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneByDescripcionCaracteristica('METRAJE FACTIBILIDAD');

        $objCaracteristicaOCivil = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneByDescripcionCaracteristica('OBRA CIVIL PRECIO');
        if(!is_object($objCaracteristicaOCivil))
        {
            throw new \Exception(': NO SE ENCONTRÓ <br> <b>OBRA CIVIL PRECIO</b>');
        }

        $objCaracteristicaOtrosMat = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneByDescripcionCaracteristica('OTROS MATERIALES PRECIO');
        if(!is_object($objCaracteristicaOtrosMat))
        { 

            throw new \Exception(': NO SE ENCONTRÓ <br> <b>OTROS MATERIALES PRECIO</b>');
        }

        $objCaracteristicaCanceladoCliente = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                               ->findOneByDescripcionCaracteristica('COPAGOS CANCELADO POR EL CLIENTE PORCENTAJE');
        if(!is_object($objCaracteristicaCanceladoCliente))
        {
            throw new \Exception(': NO SE ENCONTRÓ <br> <b>COPAGOS CANCELADO POR EL CLIENTE PORCENTAJE</b>');
        }

        $objCaracteristicaAsumeCliente = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneByDescripcionCaracteristica('COPAGOS ASUME EL CLIENTE PRECIO');   
        if(!is_object($objCaracteristicaAsumeCliente))
        {
            throw new \Exception(': NO SE ENCONTRÓ <br> <b>COPAGOS ASUME EL CLIENTE PRECIO</b>');
        }

        $objCaracteristicaAsumeEmpresa = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneByDescripcionCaracteristica('COPAGOS ASUME LA EMPRESA PRECIO');
        if(!is_object($objCaracteristicaAsumeEmpresa))
        {
            throw new \Exception(': NO SE ENCONTRÓ <br> <b>COPAGOS ASUME LA EMPRESA PRECIO</b>');
        }

        // CARACTERÍSTICA DE TAREA EXCESO DE MATERIAL
        $objParametroTarea             = $emComercial->getRepository('schemaBundle:AdmiParametroCab')
                                                ->findOneBy(array('nombreParametro'=>'TAREA EXCESO DE MATERIAL'));
        
        //DATOS DE LA SOLICITUD MATERIALES EXCEDENTES
        $entityTipoSolicitud           = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                ->findOneByDescripcionSolicitud("SOLICITUD MATERIALES EXCEDENTES");

        //DATOS DE LA SOLICITUD PLANIFICACION                                        
        $entityTipoSolicitudPla        = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                ->findOneByDescripcionSolicitud("SOLICITUD PLANIFICACION");

         //DATOS DE LA SOLICITUD FACTIBILIDAD
        $entityTipoSolicitudFactibilidad = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                ->findOneByDescripcionSolicitud("SOLICITUD FACTIBILIDAD");
        
        // Obtenemos el Correo de GTN .
        $objParametroCargo      = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                    ->findOneBy(array("descripcion"=>'Cargo que autoriza excedente de material', 
                                                    "modulo"=>'PLANIFICACIÓN',  "estado"=>'Activo'));

        $objCargoAutoriza       = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                    ->findOneBy(array("descripcion"   => 'Cargo que recibirá solicitud de excedente de material', 
                                                    "parametroId" => $objParametroCargo->getId(), "estado"      => 'Activo'));

        $objDepartamento        = $emGeneral->getRepository('schemaBundle:AdmiDepartamento')
                                        ->findOneBy(array("nombreDepartamento" =>$objCargoAutoriza->getValor2(),
                                                        "estado"             =>'Activo'));

        $objRol                  = $emGeneral->getRepository('schemaBundle:AdmiRol')
                            ->findOneBy(array("descripcionRol" => $objCargoAutoriza->getValor1()));
        
        $objEmpresaRol           = $emGeneral->getRepository('schemaBundle:InfoEmpresaRol')
                                    ->findOneBy(array("rolId"      => $objRol->getId(),
                                                    "empresaCod" => $strCodEmpresa, "estado"     => 'Activo'));
        
        $objPersonaEmpresaRol    = $emGeneral->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                            ->findOneBy(array("empresaRolId"   => $objEmpresaRol->getId(),
                                                            "departamentoId" => $objDepartamento->getId(),
                                                            "estado"         => 'Activo'));

        $arrayFormasContactoAGtn = $emComercial->getRepository('schemaBundle:InfoPersona')
                                            ->getContactosByLoginPersonaAndFormaContacto($objPersonaEmpresaRol
                                            ->getPersonaId()->getLogin(),'Correo Electronico');


        //Obtenemos la forma de contacto de alias PYL y comercial - - CORREOS A ENVIAR LOS VALORES DE EXCEDENTES
        $objParametroCabCorreosExce = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                ->findOneBy(array("descripcion"=>'CORREOS A ENVIAR LOS VALORES DE EXCEDENTES', 
                                                                    "modulo"=>'COMERCIAL',
                                                                    "estado"=>'Activo'));

        if(is_object($objParametroCabCorreosExce))
        {
            $strDescParametroDetCorreoCom   = 'CORREOS QUE SE UTILIZARÁN PARA EL ENVÍO DE NOTIFICACIONES A COMERCIAL';
            $strDescParametroDetPyl         = 'CORREOS QUE SE UTILIZARÁN PARA EL ENVÍO DE NOTIFICACIONES AL ALIAS PYL';
            $arrayCorreosParaExcedente      = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->get('CORREO_EXCEDENTES', 
                                                        '',
                                                        '',
                                                        '',
                                                        '',
                                                        '',
                                                        '',
                                                        '',
                                                        '',
                                                        $objSession->get('idEmpresa'));

            if(isset($arrayCorreosParaExcedente) && !empty($arrayCorreosParaExcedente))
            {
                foreach($arrayCorreosParaExcedente as $arrayValores)
                {
                    if($arrayValores['descripcion'] == $strDescParametroDetCorreoCom)
                    {
                        // Formas de Contacto alias Comercial
                        $arrayDestinatario[] = $arrayValores["valor1"];
                    }

                    if($arrayValores['descripcion'] == $strDescParametroDetPyl)      
                    {
                        // Formas de Contacto alias Pyl
                        $arrayDestinatario[] = $arrayValores["valor1"];
                    }                    
                }
            }
            else
            {
                throw new \Exception(': NO SE ENCONTRÓ UN CORREO PARA ENVIAR LA NOTIFICACIÓN');
            }         
        }
        else
        {
            throw new \Exception(': NO SE ENCONTRÓ UN PARÁMETRO <br> <b> CORREOS A ENVIAR LOS VALORES DE EXCEDENTES </b>');
        }

        //Obtenemos la forma de contacto del creador del servicio
        $arrayFormasContactoAsistente = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                ->getContactosByLoginPersonaAndFormaContacto($objServicio->getPuntoId()
                                                ->getUsrCreacion(),'Correo Electronico');
        
        //Obtenemos la forma de contacto del asesor LOGIN_AUX
        $arrayFormasContactoAsesor = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                ->getContactosByLoginPersonaAndFormaContacto($objServicio->getPuntoId()
                                                ->getUsrVendedor(),'Correo Electronico');

        /* INI Validamos si existe solicitud de excedente de materiales en estado pendiente*/
        $objDetalleSolicitudExc     = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                   ->findOneBy(array( "servicioId"      => $objServicio->getId(),
                                                       "estado"         => 'Pendiente',
                                                      "tipoSolicitudId" => $entityTipoSolicitud->getId()));
        
        $objTipoSolExcMaterial = $emComercial->getRepository("schemaBundle:AdmiTipoSolicitud")
                                            ->findByDescripcionSolicitud('SOLICITUD MATERIALES EXCEDENTES');
        $objDetalleSolicitudExcTodas = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                ->findUlitmoDetalleSolicitudByIds( $objServicio->getId(),$objTipoSolExcMaterial[0]->getId());
        if(is_object($objDetalleSolicitudExcTodas))
        {
            $strEstadoSolicitudExcTodas  = $objDetalleSolicitudExcTodas->getEstado();
            $intIdSolicitudExcedenteTodas = $objDetalleSolicitudExcTodas->getId();

        }
        
        if(is_object($objServicio) && !empty($objServicio) &&
          is_object($objParametroCab) && !empty($objParametroCab) &&
          is_object($objParametroTarea) && !empty($objParametroTarea)&&
          is_object($entityTipoSolicitud) && !empty($entityTipoSolicitud))
        {
            //Obtenemos el detalle de solicitud de planificación
            $entityDetalleSolicitud = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                              ->findOneById($intIdDetalleSolicitud);

            // Solicitud de planificacion
            $objDetalleSolicitudPla = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                ->findOneBy(array( "servicioId"      => $objServicio->getId(),
                                                                   "tipoSolicitudId" => $entityTipoSolicitudPla->getId()));
            if(is_object($objDetalleSolicitudPla))
            {
                $strEstadoInfoDetalleSolicitud     = $objDetalleSolicitudPla->getEstado();
                $intIdInfoDetalleSolicitudPlan     = $objDetalleSolicitudPla->getId();

                // Consulta si existe información en la info_solicitud_material de ese servicio
                $entityDetalleSolMaterial = $emComercial->getRepository('schemaBundle:InfoDetalleSolMaterial')
                                ->findOneBy(array( "detalleSolicitudId" => $objDetalleSolicitudPla->getId()));
            }

            $objDetalleSolicitudFactibilidad      = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                            ->findOneBy(array( "servicioId" => $objServicio->getId()  
                                                                         ,"tipoSolicitudId" => $entityTipoSolicitudFactibilidad->getId()
                                                        ));

            if(is_object($objDetalleSolicitudFactibilidad))
            {
                $strEstadoSolicitudFactibilidad     = $objDetalleSolicitudFactibilidad->getEstado();
            }

            if(is_object($objDetalleSolicitudPla))
            {
            //Consulta si existe detalleSolCaract de fibra, obra civil, otros materiales, 
            //copagos(% que cancela el cliente, y precio que asume empresa o cliente)
            $objInfoDetalleSolCaractFibra = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                ->findOneBy(array('caracteristicaId'  =>$objCaracteristicaFibra,
                                                                  'detalleSolicitudId'=>$objDetalleSolicitudPla
                                                            ));
            $objInfoDetalleSolCaractFibraMetros = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                ->findOneBy(array('caracteristicaId'  =>$objCaracteristicaFibraMetros,
                                                                  'detalleSolicitudId'=>$objDetalleSolicitudPla
                                                            ));
            $objInfoDetalleSolCaractOCivil      = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                ->findOneBy(array('caracteristicaId'  =>$objCaracteristicaOCivil,
                                                                  'detalleSolicitudId'=>$objDetalleSolicitudPla
                                                            ));
            $objInfoDetalleSolCaractOtrosMat    = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                ->findOneBy(array('caracteristicaId'  =>$objCaracteristicaOtrosMat,
                                                                  'detalleSolicitudId'=>$objDetalleSolicitudPla
                                                            ));
            $objInfoDetalleSolCaractCanceladoCliente  = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                            ->findOneBy(array('caracteristicaId'  =>$objCaracteristicaCanceladoCliente,
                                                                             'detalleSolicitudId'=>$objDetalleSolicitudPla
                                                                ));
            $objInfoDetalleSolCaractAsumeCliente  = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                ->findOneBy(array('caracteristicaId'  =>$objCaracteristicaAsumeCliente,
                                                                    'detalleSolicitudId'=>$objDetalleSolicitudPla
                                                                ));
            $objInfoDetalleSolCaractAsumeEmpresa  = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                ->findOneBy(array('caracteristicaId'  =>$objCaracteristicaAsumeEmpresa,
                                                        'detalleSolicitudId'=>$objDetalleSolicitudPla
                                                                ));
                                                                
               if(!is_object($objInfoDetalleSolCaractFibra)  && empty($objInfoDetalleSolCaractFibra))
                    {
                        //Guardamos los valores de fibra en InfoDetalleSolCaract
                        $objInfoDetalleSolCaractFibra  = new InfoDetalleSolCaract();
                        $objInfoDetalleSolCaractFibra->setCaracteristicaId($objCaracteristicaFibra);
                        $objInfoDetalleSolCaractFibra->setDetalleSolicitudId($objDetalleSolicitudPla);
                        $objInfoDetalleSolCaractFibra->setEstado($objDetalleSolicitudPla->getEstado());
                        $objInfoDetalleSolCaractFibra->setFeCreacion(new \DateTime('now'));
                        $objInfoDetalleSolCaractFibra->setUsrCreacion($objSession->get('user'));

                        $objInfoDetalleSolCaractFibra->setValor($floatPrecioFibra);
                    
                        $emComercial->persist($objInfoDetalleSolCaractFibra);
                        $emComercial->flush();                
                    }
                    else
                    {
                        // Actualiza los valores de fibra en InfoDetalleSolCaract
                        $objInfoDetalleSolCaractFibra->setFeUltMod(new \DateTime('now'));
                        $objInfoDetalleSolCaractFibra->getUsrUltMod($objSession->get('user'));

                        $objInfoDetalleSolCaractFibra->setValor($floatPrecioFibra);
                    
                        $emComercial->persist($objInfoDetalleSolCaractFibra);
                        $emComercial->flush(); 
                    }

                    if(!is_object($objInfoDetalleSolCaractFibraMetros)   && empty($objInfoDetalleSolCaractFibraMetros))
                    {
                        //Guardamos los valores de obra civil en InfoDetalleSolCaract               
                        $objInfoDetalleSolCaractFibraMetros  = new InfoDetalleSolCaract();
                        $objInfoDetalleSolCaractFibraMetros->setCaracteristicaId($objCaracteristicaFibraMetros);
                        $objInfoDetalleSolCaractFibraMetros->setDetalleSolicitudId($objDetalleSolicitudPla);
                        $objInfoDetalleSolCaractFibraMetros->setEstado($objDetalleSolicitudPla->getEstado());
                        $objInfoDetalleSolCaractFibraMetros->setFeCreacion(new \DateTime('now'));
                        $objInfoDetalleSolCaractFibraMetros->setUsrCreacion($objSession->get('user'));
                    
                        $objInfoDetalleSolCaractFibraMetros->setValor($intMetrosFibra);
                        
                        $emComercial->persist($objInfoDetalleSolCaractFibraMetros);
                        $emComercial->flush();
                    }
                    else
                    {
                        //Actualiza los valores de obra civil en InfoDetalleSolCaract
                        $objInfoDetalleSolCaractFibraMetros->setFeUltMod(new \DateTime('now'));
                        $objInfoDetalleSolCaractFibraMetros->getUsrUltMod($objSession->get('user'));
                    
                        $objInfoDetalleSolCaractFibraMetros->setValor($intMetrosFibra);
                        
                        $emComercial->persist($objInfoDetalleSolCaractFibraMetros);
                        $emComercial->flush();
                    }
                    
                    if(!is_object($objInfoDetalleSolCaractOCivil)   && empty($objInfoDetalleSolCaractOCivil))
                    {
                        //Guardamos los valores de obra civil en InfoDetalleSolCaract               
                        $objInfoDetalleSolCaractOCivil = new InfoDetalleSolCaract();
                        $objInfoDetalleSolCaractOCivil->setCaracteristicaId($objCaracteristicaOCivil);
                        $objInfoDetalleSolCaractOCivil->setDetalleSolicitudId($objDetalleSolicitudPla);
                        $objInfoDetalleSolCaractOCivil->setEstado($objDetalleSolicitudPla->getEstado());
                        $objInfoDetalleSolCaractOCivil->setFeCreacion(new \DateTime('now'));
                        $objInfoDetalleSolCaractOCivil->setUsrCreacion($objSession->get('user'));
                    
                        $objInfoDetalleSolCaractOCivil->setValor($floatPrecioObraCivil);
                        
                        $emComercial->persist($objInfoDetalleSolCaractOCivil);
                        $emComercial->flush();
                    }
                    else
                    {
                        //Actualiza los valores de obra civil en InfoDetalleSolCaract
                        $objInfoDetalleSolCaractOCivil->setFeUltMod(new \DateTime('now'));
                        $objInfoDetalleSolCaractOCivil->getUsrUltMod($objSession->get('user'));
                    
                        $objInfoDetalleSolCaractOCivil->setValor($floatPrecioObraCivil);
                        
                        $emComercial->persist($objInfoDetalleSolCaractOCivil);
                        $emComercial->flush();
                    }


                    if(!is_object($objInfoDetalleSolCaractOtrosMat) && empty($objInfoDetalleSolCaractOtrosMat))
                    {
                        //Guardamos los valores de otros materiales en InfoDetalleSolCaract              
                        $objInfoDetalleSolCaractOtrosMat = new InfoDetalleSolCaract();
                        $objInfoDetalleSolCaractOtrosMat->setCaracteristicaId($objCaracteristicaOtrosMat);
                        $objInfoDetalleSolCaractOtrosMat->setDetalleSolicitudId($objDetalleSolicitudPla);
                        $objInfoDetalleSolCaractOtrosMat->setEstado($objDetalleSolicitudPla->getEstado());
                        $objInfoDetalleSolCaractOtrosMat->setFeCreacion(new \DateTime('now'));
                        $objInfoDetalleSolCaractOtrosMat->setUsrCreacion($objSession->get('user'));
                        
                        $objInfoDetalleSolCaractOtrosMat->setValor($floatPrecioOtrosMate);
                        
                        $emComercial->persist($objInfoDetalleSolCaractOtrosMat);
                        $emComercial->flush();
                        }
                        else
                        {
                            //Actualiza los valores de otros materiales en InfoDetalleSolCaract
                        $objInfoDetalleSolCaractOtrosMat->setFeUltMod(new \DateTime('now'));
                        $objInfoDetalleSolCaractOtrosMat->getUsrUltMod($objSession->get('user'));
                        
                        $objInfoDetalleSolCaractOtrosMat->setValor($floatPrecioOtrosMate);
                        
                        $emComercial->persist($objInfoDetalleSolCaractOtrosMat);
                        $emComercial->flush();

                    }

                        // ---- COPAGOS ----
                    if(!is_object($objInfoDetalleSolCaractCanceladoCliente) && empty($objInfoDetalleSolCaractCanceladoCliente))
                    {
                        //Guardamos los valores de copagos en InfoDetalleSolCaract
                        $objInfoDetalleSolCaractCanceladoCliente = new InfoDetalleSolCaract();
                        $objInfoDetalleSolCaractCanceladoCliente->setCaracteristicaId($objCaracteristicaCanceladoCliente);
                        $objInfoDetalleSolCaractCanceladoCliente->setDetalleSolicitudId($objDetalleSolicitudPla);
                        $objInfoDetalleSolCaractCanceladoCliente->setEstado($objDetalleSolicitudPla->getEstado());
                        $objInfoDetalleSolCaractCanceladoCliente->setFeCreacion(new \DateTime('now'));
                        $objInfoDetalleSolCaractCanceladoCliente->setUsrCreacion($objSession->get('user'));

                        $objInfoDetalleSolCaractCanceladoCliente->setValor($floatCanceladoPorCliente);
            
                        $emComercial->persist($objInfoDetalleSolCaractCanceladoCliente);
                        $emComercial->flush();
                    }
                    else
                    {
                        //Actualiza los valores de copagos en InfoDetalleSolCaract
                        $objInfoDetalleSolCaractCanceladoCliente->setFeUltMod(new \DateTime('now'));
                        $objInfoDetalleSolCaractCanceladoCliente->getUsrUltMod($objSession->get('user'));

                        $objInfoDetalleSolCaractCanceladoCliente->setValor($floatCanceladoPorCliente);
            
                        $emComercial->persist($objInfoDetalleSolCaractCanceladoCliente);
                        $emComercial->flush();
                    }

                    if(!is_object($objInfoDetalleSolCaractAsumeCliente)     && empty($objInfoDetalleSolCaractAsumeCliente))
                    {
                        //Guardamos los valores de copagos en InfoDetalleSolCaract                    
                        $objInfoDetalleSolCaractAsumeCliente = new InfoDetalleSolCaract();
                        $objInfoDetalleSolCaractAsumeCliente->setCaracteristicaId($objCaracteristicaAsumeCliente);
                        $objInfoDetalleSolCaractAsumeCliente->setDetalleSolicitudId($objDetalleSolicitudPla);
                        $objInfoDetalleSolCaractAsumeCliente->setEstado($objDetalleSolicitudPla->getEstado());
                        $objInfoDetalleSolCaractAsumeCliente->setFeCreacion(new \DateTime('now'));
                        $objInfoDetalleSolCaractAsumeCliente->setUsrCreacion($objSession->get('user'));             
            
                        $objInfoDetalleSolCaractAsumeCliente->setValor($floatAsumeCliente);
            
                        $emComercial->persist($objInfoDetalleSolCaractAsumeCliente);
                        $emComercial->flush();
                    }
                    else
                    {
                        //Actualiza los valores de copagos y en InfoDetalleSolCaract
                        $objInfoDetalleSolCaractAsumeCliente->setFeUltMod(new \DateTime('now'));
                        $objInfoDetalleSolCaractAsumeCliente->getUsrUltMod($objSession->get('user'));             
            
                        $objInfoDetalleSolCaractAsumeCliente->setValor($floatAsumeCliente);
            
                        $emComercial->persist($objInfoDetalleSolCaractAsumeCliente);
                        $emComercial->flush();
                    }

                    if(!is_object($objInfoDetalleSolCaractAsumeEmpresa)     && empty($objInfoDetalleSolCaractAsumeEmpresa))
                    {
                        //Guardamos los valores de copagos en InfoDetalleSolCaract                    
                        $objInfoDetalleSolCaractAsumeEmpresa = new InfoDetalleSolCaract();
                        $objInfoDetalleSolCaractAsumeEmpresa->setCaracteristicaId($objCaracteristicaAsumeEmpresa);
                        $objInfoDetalleSolCaractAsumeEmpresa->setDetalleSolicitudId($objDetalleSolicitudPla);
                        $objInfoDetalleSolCaractAsumeEmpresa->setEstado($objDetalleSolicitudPla->getEstado());
                        $objInfoDetalleSolCaractAsumeEmpresa->setFeCreacion(new \DateTime('now'));
                        $objInfoDetalleSolCaractAsumeEmpresa->setUsrCreacion($objSession->get('user'));
            
                        $objInfoDetalleSolCaractAsumeEmpresa->setValor($floatAsumeEmpresa);
            
                        $emComercial->persist($objInfoDetalleSolCaractAsumeEmpresa);
                        $emComercial->flush();
                    }
                    else
                    {
                        //Actualiza los valores de copagos en InfoDetalleSolCaract
                        $objInfoDetalleSolCaractAsumeEmpresa->setFeUltMod(new \DateTime('now'));
                        $objInfoDetalleSolCaractAsumeEmpresa->getUsrUltMod($objSession->get('user'));
            
                        $objInfoDetalleSolCaractAsumeEmpresa->setValor($floatAsumeEmpresa);
            
                        $emComercial->persist($objInfoDetalleSolCaractAsumeEmpresa);
                        $emComercial->flush();
                    }
            }
            else
            {
                if(is_object($objDetalleSolicitudFactibilidad))
                {
                    
                $objInfoDetalleSolCaractFibra = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(array('caracteristicaId'  =>$objCaracteristicaFibra,
                                                                    'detalleSolicitudId'=>$objDetalleSolicitudFactibilidad
                                                                ));
                $objInfoDetalleSolCaractFibraMetros = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(array('caracteristicaId'  =>$objCaracteristicaFibraMetros,
                                                                    'detalleSolicitudId'=>$objDetalleSolicitudFactibilidad
                                                                ));
                $objInfoDetalleSolCaractOCivil      = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(array('caracteristicaId'  =>$objCaracteristicaOCivil,
                                                                    'detalleSolicitudId'=>$objDetalleSolicitudFactibilidad
                                                                ));
                $objInfoDetalleSolCaractOtrosMat    = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(array('caracteristicaId'  =>$objCaracteristicaOtrosMat,
                                                                    'detalleSolicitudId'=>$objDetalleSolicitudFactibilidad
                                                                ));
                $objInfoDetalleSolCaractCanceladoCliente  = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                ->findOneBy(array('caracteristicaId'  =>$objCaracteristicaCanceladoCliente,
                                                                                'detalleSolicitudId'=>$objDetalleSolicitudFactibilidad
                                                                    ));
                $objInfoDetalleSolCaractAsumeCliente  = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(array('caracteristicaId'  =>$objCaracteristicaAsumeCliente,
                                                                        'detalleSolicitudId'=>$objDetalleSolicitudFactibilidad
                                                                    ));
                $objInfoDetalleSolCaractAsumeEmpresa  = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(array('caracteristicaId'  =>$objCaracteristicaAsumeEmpresa,
                                                            'detalleSolicitudId'=>$objDetalleSolicitudFactibilidad
                                                                    ));
            
                if(!is_object($objInfoDetalleSolCaractFibra)  && empty($objInfoDetalleSolCaractFibra))
                {
                    //Guardamos los valores de fibra en InfoDetalleSolCaract
                    $objInfoDetalleSolCaractFibra  = new InfoDetalleSolCaract();
                    $objInfoDetalleSolCaractFibra->setCaracteristicaId($objCaracteristicaFibra);
                    $objInfoDetalleSolCaractFibra->setDetalleSolicitudId($objDetalleSolicitudFactibilidad);
                    $objInfoDetalleSolCaractFibra->setEstado($objDetalleSolicitudFactibilidad->getEstado());
                    $objInfoDetalleSolCaractFibra->setFeCreacion(new \DateTime('now'));
                    $objInfoDetalleSolCaractFibra->setUsrCreacion($objSession->get('user'));

                    $objInfoDetalleSolCaractFibra->setValor($floatPrecioFibra);
                
                    $emComercial->persist($objInfoDetalleSolCaractFibra);
                    $emComercial->flush();                
                }
                else
                {
                    // Actualiza los valores de fibra en InfoDetalleSolCaract
                    $objInfoDetalleSolCaractFibra->setFeUltMod(new \DateTime('now'));
                    $objInfoDetalleSolCaractFibra->getUsrUltMod($objSession->get('user'));

                    $objInfoDetalleSolCaractFibra->setValor($floatPrecioFibra);
                
                    $emComercial->persist($objInfoDetalleSolCaractFibra);
                    $emComercial->flush(); 
                }
                    if(!is_object($objInfoDetalleSolCaractFibraMetros) && empty($objInfoDetalleSolCaractFibraMetros))
                    {
                        //Guardamos los valores de obra civil en InfoDetalleSolCaract               
                        $objInfoDetalleSolCaractFibraMetros  = new InfoDetalleSolCaract();
                        $objInfoDetalleSolCaractFibraMetros->setCaracteristicaId($objCaracteristicaFibraMetros);
                        $objInfoDetalleSolCaractFibraMetros->setDetalleSolicitudId($objDetalleSolicitudFactibilidad);
                        $objInfoDetalleSolCaractFibraMetros->setEstado($objDetalleSolicitudFactibilidad->getEstado());
                        $objInfoDetalleSolCaractFibraMetros->setFeCreacion(new \DateTime('now'));
                        $objInfoDetalleSolCaractFibraMetros->setUsrCreacion($objSession->get('user'));
                    
                        $objInfoDetalleSolCaractFibraMetros->setValor($intMetrosFibra);
                        
                        $emComercial->persist($objInfoDetalleSolCaractFibraMetros);
                        $emComercial->flush();
                    }
                    else
                    {
                        //Actualiza los valores de obra civil en InfoDetalleSolCaract
                        $objInfoDetalleSolCaractFibraMetros->setFeUltMod(new \DateTime('now'));
                        $objInfoDetalleSolCaractFibraMetros->getUsrUltMod($objSession->get('user'));
                    
                        $objInfoDetalleSolCaractFibraMetros->setValor($intMetrosFibra);
                        
                        $emComercial->persist($objInfoDetalleSolCaractFibraMetros);
                        $emComercial->flush();
                    } 
                    if(!is_object($objInfoDetalleSolCaractOCivil)   && empty($objInfoDetalleSolCaractOCivil))
                    {
                        //Guardamos los valores de obra civil en InfoDetalleSolCaract               
                        $objInfoDetalleSolCaractOCivil = new InfoDetalleSolCaract();
                        $objInfoDetalleSolCaractOCivil->setCaracteristicaId($objCaracteristicaOCivil);
                        $objInfoDetalleSolCaractOCivil->setDetalleSolicitudId($objDetalleSolicitudFactibilidad);
                        $objInfoDetalleSolCaractOCivil->setEstado($objDetalleSolicitudFactibilidad->getEstado());
                        $objInfoDetalleSolCaractOCivil->setFeCreacion(new \DateTime('now'));
                        $objInfoDetalleSolCaractOCivil->setUsrCreacion($objSession->get('user'));
                    
                        $objInfoDetalleSolCaractOCivil->setValor($floatPrecioObraCivil);
                        
                        $emComercial->persist($objInfoDetalleSolCaractOCivil);
                        $emComercial->flush();
                    }
                    else
                    {
                        //Actualiza los valores de obra civil en InfoDetalleSolCaract
                        $objInfoDetalleSolCaractOCivil->setFeUltMod(new \DateTime('now'));
                        $objInfoDetalleSolCaractOCivil->getUsrUltMod($objSession->get('user'));
                    
                        $objInfoDetalleSolCaractOCivil->setValor($floatPrecioObraCivil);
                        
                        $emComercial->persist($objInfoDetalleSolCaractOCivil);
                        $emComercial->flush();
                    }    
        
                    if(!is_object($objInfoDetalleSolCaractOtrosMat) && empty($objInfoDetalleSolCaractOtrosMat))
                    {
                        //Guardamos los valores de otros materiales en InfoDetalleSolCaract              
                        $objInfoDetalleSolCaractOtrosMat = new InfoDetalleSolCaract();
                        $objInfoDetalleSolCaractOtrosMat->setCaracteristicaId($objCaracteristicaOtrosMat);
                        $objInfoDetalleSolCaractOtrosMat->setDetalleSolicitudId($objDetalleSolicitudFactibilidad);
                        $objInfoDetalleSolCaractOtrosMat->setEstado($objDetalleSolicitudFactibilidad->getEstado());
                        $objInfoDetalleSolCaractOtrosMat->setFeCreacion(new \DateTime('now'));
                        $objInfoDetalleSolCaractOtrosMat->setUsrCreacion($objSession->get('user'));
                        
                        $objInfoDetalleSolCaractOtrosMat->setValor($floatPrecioOtrosMate);
                        
                        $emComercial->persist($objInfoDetalleSolCaractOtrosMat);
                        $emComercial->flush();
                        }
                        else
                        {
                            //Actualiza los valores de otros materiales en InfoDetalleSolCaract
                        $objInfoDetalleSolCaractOtrosMat->setFeUltMod(new \DateTime('now'));
                        $objInfoDetalleSolCaractOtrosMat->getUsrUltMod($objSession->get('user'));
                        
                        $objInfoDetalleSolCaractOtrosMat->setValor($floatPrecioOtrosMate);
                        
                        $emComercial->persist($objInfoDetalleSolCaractOtrosMat);
                        $emComercial->flush();
        
                    }
        
                        // ---- COPAGOS ----
                    if(!is_object($objInfoDetalleSolCaractCanceladoCliente) && empty($objInfoDetalleSolCaractCanceladoCliente))
                    {
                        //Guardamos los valores de copagos en InfoDetalleSolCaract
                        $objInfoDetalleSolCaractCanceladoCliente = new InfoDetalleSolCaract();
                        $objInfoDetalleSolCaractCanceladoCliente->setCaracteristicaId($objCaracteristicaCanceladoCliente);
                        $objInfoDetalleSolCaractCanceladoCliente->setDetalleSolicitudId($objDetalleSolicitudFactibilidad);
                        $objInfoDetalleSolCaractCanceladoCliente->setEstado($objDetalleSolicitudFactibilidad->getEstado());
                        $objInfoDetalleSolCaractCanceladoCliente->setFeCreacion(new \DateTime('now'));
                        $objInfoDetalleSolCaractCanceladoCliente->setUsrCreacion($objSession->get('user'));
        
                        $objInfoDetalleSolCaractCanceladoCliente->setValor($floatCanceladoPorCliente);
            
                        $emComercial->persist($objInfoDetalleSolCaractCanceladoCliente);
                        $emComercial->flush();
                    }
                    else
                    {
                        //Actualiza los valores de copagos en InfoDetalleSolCaract
                        $objInfoDetalleSolCaractCanceladoCliente->setFeUltMod(new \DateTime('now'));
                        $objInfoDetalleSolCaractCanceladoCliente->getUsrUltMod($objSession->get('user'));
        
                        $objInfoDetalleSolCaractCanceladoCliente->setValor($floatCanceladoPorCliente);
            
                        $emComercial->persist($objInfoDetalleSolCaractCanceladoCliente);
                        $emComercial->flush();
                    }
        
                    if(!is_object($objInfoDetalleSolCaractAsumeCliente)     && empty($objInfoDetalleSolCaractAsumeCliente))
                    {
                        //Guardamos los valores de copagos en InfoDetalleSolCaract                    
                        $objInfoDetalleSolCaractAsumeCliente = new InfoDetalleSolCaract();
                        $objInfoDetalleSolCaractAsumeCliente->setCaracteristicaId($objCaracteristicaAsumeCliente);
                        $objInfoDetalleSolCaractAsumeCliente->setDetalleSolicitudId($objDetalleSolicitudFactibilidad);
                        $objInfoDetalleSolCaractAsumeCliente->setEstado($objDetalleSolicitudFactibilidad->getEstado());
                        $objInfoDetalleSolCaractAsumeCliente->setFeCreacion(new \DateTime('now'));
                        $objInfoDetalleSolCaractAsumeCliente->setUsrCreacion($objSession->get('user'));             
            
                        $objInfoDetalleSolCaractAsumeCliente->setValor($floatAsumeCliente);
            
                        $emComercial->persist($objInfoDetalleSolCaractAsumeCliente);
                        $emComercial->flush();
                    }
                    else
                    {
                        //Actualiza los valores de copagos y en InfoDetalleSolCaract
                        $objInfoDetalleSolCaractAsumeCliente->setFeUltMod(new \DateTime('now'));
                        $objInfoDetalleSolCaractAsumeCliente->getUsrUltMod($objSession->get('user'));             
            
                        $objInfoDetalleSolCaractAsumeCliente->setValor($floatAsumeCliente);
            
                        $emComercial->persist($objInfoDetalleSolCaractAsumeCliente);
                        $emComercial->flush();
                    }
                    if(!is_object($objInfoDetalleSolCaractAsumeEmpresa)     && empty($objInfoDetalleSolCaractAsumeEmpresa))
                    {
                        //Guardamos los valores de copagos en InfoDetalleSolCaract                    
                        $objInfoDetalleSolCaractAsumeEmpresa = new InfoDetalleSolCaract();
                        $objInfoDetalleSolCaractAsumeEmpresa->setCaracteristicaId($objCaracteristicaAsumeEmpresa);
                        $objInfoDetalleSolCaractAsumeEmpresa->setDetalleSolicitudId($objDetalleSolicitudFactibilidad);
                        $objInfoDetalleSolCaractAsumeEmpresa->setEstado($objDetalleSolicitudFactibilidad->getEstado());
                        $objInfoDetalleSolCaractAsumeEmpresa->setFeCreacion(new \DateTime('now'));
                        $objInfoDetalleSolCaractAsumeEmpresa->setUsrCreacion($objSession->get('user'));
            
                        $objInfoDetalleSolCaractAsumeEmpresa->setValor($floatAsumeEmpresa);
            
                        $emComercial->persist($objInfoDetalleSolCaractAsumeEmpresa);
                        $emComercial->flush();
                    }
                    else
                    {
                        //Actualiza los valores de copagos en InfoDetalleSolCaract
                        $objInfoDetalleSolCaractAsumeEmpresa->setFeUltMod(new \DateTime('now'));
                        $objInfoDetalleSolCaractAsumeEmpresa->getUsrUltMod($objSession->get('user'));
            
                        $objInfoDetalleSolCaractAsumeEmpresa->setValor($floatAsumeEmpresa);
            
                        $emComercial->persist($objInfoDetalleSolCaractAsumeEmpresa);
                        $emComercial->flush();
                    }
                    
                }
                
            }
            if($floatTotalPagar    ==  0)
            {
                throw new \Exception(': NO SE ENCONTRÓ <b>NIGÚN VALOR PARA ENVIAR O VALIDAR</b>');
            }

            if($boolModuloQueValida)
            {
                if (($strEvidencia) && ($floatCanceladoPorCliente == 0))
                {
                    throw new \Exception(': EL CLIENTE TIENE EVIDENCIAS, <br> <b>POR FAVOR INGRESE VALORES EN COPAGOS</b>');
                }

                if ((!$strEvidencia) && ($floatCanceladoPorCliente != 0))
                {
                    throw new \Exception(': EL CLIENTE NO TIENE EVIDENCIAS, <br> 
                                                        <b>POR FAVOR CARGUE ARCHIVO(S) SI NECESITA VALORES EN COPAGOS</b>');
                }
            }
            
            if($floatCanceladoPorCliente != 0)
            {
                $floatPorcentajeEmpresa            = 100 - $floatCanceladoPorCliente;
            }
            else
            {
                $floatPorcentajeEmpresa            = 0;
            }
            $strTablaDeValores = '<br/><br/>
                                <table width="100%" cellspacing="4" cellpadding="4">
                                    <tr> <td colspan="4"><b>*Valores de excedente de materiales*</b></td></tr>
                                    <tr> <td colspan="4"><b>Valores de Otros Clientes:</b></td></tr>
                                    <tr><td>Precio de Fibra            </td><td>$'.+$floatPrecioFibra.            '</td> </tr>
                                    <tr><td>Precio de Obra Civil       </td> <td>$'.+$floatPrecioObraCivil.       '</td> </tr>
                                    <tr><td>Precio de Otros Materiales </td> <td>$'.+$floatPrecioOtrosMate .      '</td> </tr>
                                    <tr><td>Subtotal de Otros Clientes </td> <td><b>$'.+$floatSubTotalOtrosClientes.'<b></td> </tr> 
                                    <tr> <td colspan="3"><b>Valores de COPAGOS:</b></td></tr>
                                   <tr><td>% Cliente                     </td> <td>'.+$floatCanceladoPorCliente .'%</td> </tr>
                                   <tr><td>% Empresa                     </td> <td>'.+$floatPorcentajeEmpresa          .'% </td> </tr>
                                    <tr><td>Cliente cancela         </td> <td>$'.+$floatAsumeCliente       .'</td>  </tr>
                                    <tr><td>Empresa cancela         </td> <td>$'.+$floatAsumeEmpresa       .'</td>  </tr>
                                    <tr> <td colspan="1"><b>TOTAL:</b></td><td><b>$'.+$floatTotalPagar     .'<b></td></tr>
                               </table> <br/>';
 
            //se arma la tabla con los datos a enviar
            
            $strTablaDeValoresEnviar = '<br/><br/>
                                <table width="100%" cellspacing="4" cellpadding="4">
                                    <tr> <td colspan="4"><b>*Valores de excedente de materiales*</b></td></tr>
                                    <tr> <td colspan="4"><b>Valores de Otros Clientes:</b></td></tr>
                                    <tr><td>Precio de Fibra            </td><td>$'.+$floatPrecioFibra.            '</td> </tr>
                                    <tr><td>Precio de Obra Civil       </td> <td>$'.+$floatPrecioObraCivil.       '</td> </tr>
                                    <tr><td>Precio de Otros Materiales </td> <td>$'.+$floatPrecioOtrosMate .      '</td> </tr>
                                    <tr><td>Subtotal de Otros Clientes </td> <td><b>$'.+$floatSubTotalOtrosClientes.'<b></td> </tr> 
                                    <tr> <td colspan="3"><b>Valores de COPAGOS:</b></td></tr>
                                   <tr><td>% Cliente                     </td> <td>'.+$floatCanceladoPorCliente .'%</td> </tr>
                                   <tr><td>% Empresa                     </td> <td>'.+$floatPorcentajeEmpresa          .'% </td> </tr>
                                    <tr><td>Cliente cancela         </td> <td>$'.+$floatAsumeCliente       .'</td>  </tr>
                                    <tr><td>Empresa cancela         </td> <td>$'.+$floatAsumeEmpresa       .'</td>  </tr>
                                    <tr> <td colspan="1"><b>TOTAL:</b></td><td><b>$'.+$floatTotalPagar     .'<b></td></tr>
                               </table> <br/>';                                               
            
                // --------- Pregunta si está habilitada la variable de validación y continua al proceso de validar - MODULO COMERCIAL --
                if ($boolModuloQueValida)
                {
                  // 1.- COMERCIAL: Excedente de metraje la OT en estado FACTIBLE, Detenida, Detenido, PrePlanificada, Replanificada
                  if (($strEstadoServicio  == 'Factible')      || ($strEstadoServicio  == 'Detenida') || ($strEstadoServicio  == 'Detenido')
                  || ($strEstadoServicio  == 'PrePlanificada') || ($strEstadoServicio  == 'Replanificada') )
                  {
                        /* Es decir que tiene cargado respaldos o evidencias del cliente (Correo ok cliente, PDF - FORMATO Facturación) */
                    if ( ($strEvidencia) && ($floatCanceladoPorCliente != 0) )
                    {
                            //  COPAGOS.
                             $strAsunto         = "Notificación: se informa los valores del excedente de material | login: "
                                                .$objServicio->getPuntoId()->getLogin();
                       
                            $boolAplicativoValida = true;
                            $strObservacionEnviar = '<br/> <b>Servicio:</b>' . $objServicio->getProductoId()->getDescripcionProducto()
                                                    .'- ' . $objServicio->getDescripcionPresentaFactura()
                                                    .'<br/> <b>Observaci&oacute;n:</b>  << '. $strObservacion.'>>. <br/>'
                                                    .$strTablaDeValoresEnviar;
            
                            $strSeguimiento = '<br/> <b>Servicio:</b>
                                            '. $objServicio->getProductoId()->getDescripcionProducto()
                                            . '<br/> <b>Observación:</b>  << '. $strObservacion.'>>.<br/>'.
                                            $strTablaDeValores;
                    }
                    //   EL CLIENTE NO AUTORIZA EL COBRO, no hay evidencias
                    else
                    {
                            $strAsunto            = "Notificación: se informa los valores del excedente de material | login: "
                                                    .$objServicio->getPuntoId()->getLogin();

                            $strObservacionEnviar = '<br/> <b>Servicio:</b> ' . $objServicio->getProductoId()->getDescripcionProducto()
                                                    . ' - ' . $objServicio->getDescripcionPresentaFactura()
                                                    . ' <br/><b>Observaci&oacute;n:</b>  << '. $strObservacion.'>>.'
                                                    .$strTablaDeValoresEnviar;
                        
                            $strSeguimiento       = '<br/> <b>Servicio: </b>
                                                    ' . $objServicio->getProductoId()->getDescripcionProducto()
                                                    . ' <br/><b>Observación:</b>  << '. $strObservacion.'>>.'
                                                    .$strTablaDeValores;

                            $boolAplicativoValida = true;                        
                    }
                        if($boolAplicativoValida)
                        {
                            $floatMCREnlaceActual = $objServicio->getPrecioVenta();
                            if (is_object($objParamValidacion1) && !empty($objParamValidacion1) &&
                                is_object($objParamValidacion2) && !empty($objParamValidacion2) )
                            {
                                //Variables de decisión para autorización automática.
                                $floatValidacionMRC  = floatval($objParamValidacion1->getValor2());
                                $intMinMRCxRUC       = intval($objParamValidacion2->getValor2());
                                $intSupera           = intval($objParamValidacion2->getValor4());
                            
                                /*Cada vez que se requiera un excedente de fibra, como también materiales adicionales y trabajos de 
                                *obras civiles, el sistema debe unificar estos valores y comparar si sobrepasa su MRC (del enlace 
                                * a instalar) en 2,5 veces. */
                                if ($floatSumaExcedente < ($floatMCREnlaceActual * $floatValidacionMRC)) 
                                {
                                    $boolAutorizado =  true;
                                    $strSeguimiento = '<b>Se autoriza el valor de $'.$floatTotalPagar.' por Validación 1: </b><br/>'
                                                    . '"El sistema unifica los valores de fibra y obra civil<br/>'
                                                        . 'luego compara si sobrepasa su MRC (del <br/>'
                                                        . 'enlace a instalar) en ' . $floatValidacionMRC . ' veces".
                                                        <br/>'.$strSeguimiento;

                                    $strMail        = '<b>Se autoriza el valor de $'.$floatTotalPagar.' por Validación 1: </b>:<br/>'
                                                    . '"El sistema unifica los valores de fibra y obra civil '
                                                    . 'luego compara si sobrepasa su MRC (del '
                                                    . 'enlace a instalar) en ' . $floatValidacionMRC . ' veces". ' ;                              
                                }
                                else
                                {
                                    $arrayParametrosFacturacion = array();
                                    $arrayParametrosFacturacion['intIdPersonaRol'] = $objServicio->getPuntoId()->getPersonaEmpresaRolId();
                                    $arrayParametrosFacturacion['arrayEstados']    = ['Activo'];
                                    $arrayParametrosFacturacion['strEsVenta']      = $objServicio->getEsVenta();
                                    $floatTotalFacturacion  = $emComercial
                                                            ->getRepository('schemaBundle:InfoServicioTecnico')
                                                            ->getTotalFacturacionTelcograph($arrayParametrosFacturacion);
                                    /*Si al cliente como RUC le facturamos más de $5K en MRC y si supera las 5 veces (costo de 
                                        * excedentes) el MRC del enlace entonces se autoriza la instalación y debe seguir 
                                        * por flujo normal.
                                        */
                                    if (($floatTotalFacturacion > $intMinMRCxRUC)
                                        && $floatSumaExcedente < ($floatMCREnlaceActual * $intSupera))
                                    {
                                        $boolAutorizado =  true;
                                        $strSeguimiento = '<br/><b>Se autoriza el valor de $'.$floatTotalPagar.' por Validación 2: </b><br/>'
                                                        . '"Si al cliente como RUC le facturamos más de <br/>
                                                          $' . $intMinMRCxRUC . ' en MRC y  si supera <br/>
                                                          las ' . $intSupera . ' veces (costo de excedentes)<br/>
                                                           el MRC del enlace entonces se autoriza la instalación".'
                                                        .'<br/>'.$strSeguimiento;

                                        $strMail       = '<br/><b>Se autoriza el valor de $'.$floatTotalPagar.' por Validación 2: </b><br/>'
                                                        . '"Si al cliente como RUC le facturamos más de 
                                                        $' . $intMinMRCxRUC . ' en MRC y si supera 
                                                        las ' . $intSupera . ' veces (costo de excedentes) 
                                                        el MRC del enlace entonces se autoriza la instalación".<br/>';
                                    }
                                    else
                                    {
                                        $boolAutorizado =  false;
                                        $strSeguimiento = '<br/><b>NO se autoriza por Validación 2 </b><br/>'
                                                        . '"Si al cliente como RUC le facturamos más de<br/>
                                                         $' . $intMinMRCxRUC . ' en MRC y si supera 
                                                         <br/> las ' . $intSupera . ' veces (costo de excedentes) 
                                                         <br/>el MRC del enlace entonces se autoriza la instalación".'
                                                        .'<br/> <br/>'.$strSeguimiento;

                                        $strMail       = '<br/><b>NO se autoriza por Validación 2:</b> <br/>'
                                                        . '"Si al cliente como RUC le facturamos más 
                                                         de $' . $intMinMRCxRUC . ' en MRC y si supera 
                                                         las ' . $intSupera . ' veces (costo de excedentes)
                                                         el MRC del enlace entonces se autoriza la instalación".'
                                                         ;                                   
                                    }
                                }                          
                            }
                        
                            if( ($floatPrecioFibra==0) )
                            {
                                $floatTotalPagar     = $floatSubTotalOtrosClientes;
                            }

                            if ( ($strEvidencia) && ($floatCanceladoPorCliente != 0) )
                            {
                                $floatTotalPagar     = $floatAsumeCliente;
                            }
                           
                            //formatear a solo dos decimales
                            $floatTotalPagar = number_format($floatTotalPagar, 2,'.','');

                            $intCantidadEstimada    = 1;
                            $strCostoMaterial       = 0;
                            $intCantidadCliente     = 1;
                            $intCantidadUsada       = 0;
                            $intCantidadFacturada   = 1;
                            $strPrecioVentaMaterial = $floatTotalPagar;
                            $strValorCobrado        = $floatTotalPagar;
                            if ( ($strEvidencia) && ($floatCanceladoPorCliente != 0) )
                            {
                                $floatAsumeEmpresa;
                            }
                            else
                            {
                                $floatAsumeEmpresa = $floatTotalPagar;
                            }
                            //Consecuencias si se autoriza automáticamente
                            if($boolAutorizado)
                            {
                                // Inicio de cuando Si existe una solicitud de excedente pendiente
                                if($objDetalleSolicitudExc)
                                {
                                    $boolExisteSolicitud = true;

                                    $strObservacionEnviar .= '<b>Revalidaci&oacute;n de solcitud!<b> <br/>. 
                                                            <br/>Solicitud de excedente de material '.' #'.$objDetalleSolicitudExc->getId()
                                                            .'<br/>';

                                    $strSeguimiento .= '<b>Revalidación de solcitud!. <b> <br/>'.
                                                        'Solicitud de excedente de material </b> '.' #'.$objDetalleSolicitudExc->getId();
                                    $strSeguimientoSol = '<b>Revalidación de solcitud!. <b> <br/>'.
                                                    'Solicitud de excedente de material</b> <br/>'.' #'.$objDetalleSolicitudExc->getId();

                                    if ( ($strEvidencia) && ($floatCanceladoPorCliente != 0) )
                                    {
                                        $strSeguimiento     .= '. <br/> Con valores copagos:
                                                            La empresa asume $'.$floatAsumeEmpresa.'<br/>';
                                    }

                                    /* GUARDAR historial a INFO DETALLE_SOLICITUD_HISTORIAL*/
                                    $strEstadoEnviado = $objDetalleSolicitudExc->getEstado();
                                    $arrayParametrosTraSol = array(
                                                                "emComercial"                => $emComercial,
                                                                "strClienteIp"               => $strClienteIp,
                                                                "objDetalleSolicitudExc"     => $objDetalleSolicitudExc,
                                                                "strObservacion"             => $strSeguimientoSol,
                                                                "strUsrCreacion"             => $strUsrCreacion,
                                                                "strEstadoEnviado"           => $strEstadoEnviado );
                                    $arrayVerificar = $serviceAutorizaciones->registroTrazabilidadDeLaSolicitud($arrayParametrosTraSol);
                                    
                                    if($arrayVerificar['status'] == 'ERROR' )
                                    {
                                    throw new \Exception(': NO SE REALIZÓ EL PROCESO COMPLETO: registroTrazabilidadDeLaSolicitud
                                                            <br/> <b>'.$arrayVerificar['mensaje'].'</b>');
                                    }

                                }
                                /* FIN Validamos si existe solicitud de excedente de material pendiente*/
                                else
                                {
                                    // Solo si no tiene ninguna solicitud excedente, se crea una aprobada
                                    if(!is_object($objDetalleSolicitudExcTodas))
                                    {
                                        //CREO LA INFO DETALLE SOLICICITUD DE MATERIALES EXCEDENTES
                                        $strSeguimiento   .= 'Solicitud de excedente de material autorizada por aplicativo';
                                        $strSeguimientoSol = 'Solicitud de excedente de material autorizada por aplicativo';

                                        $strEstadoEnviado = "Aprobado";
                                        $strObservacionEnviar .= '<br/><b>Solicitud de excedente de material autorizada por aplicativo</b>,
                                                            en estado:'. $strEstadoEnviado.'<br/>';
                                        $arrayParametrosSolExc = array(
                                                                    "emComercial"                => $emComercial,
                                                                    "strClienteIp"               => $strClienteIp,
                                                                    "entityTipoSolicitud"        => $entityTipoSolicitud,
                                                                    "objServicio"                => $objServicio,
                                                                    "strSeguimiento"             => $strSeguimientoSol,
                                                                    "strUsrCreacion"             => $strUsrCreacion,
                                                                    "strEstadoEnviado"           => $strEstadoEnviado);
                                        $arrayVerificar    = $serviceSolicitud->registroSolicitudDeExcedenteMateriales($arrayParametrosSolExc);
                                        $intIdSolicitudGtn = $arrayVerificar['intIdSolicitud'];
                                        if($arrayVerificar['status'] == 'ERROR' )
                                        {
                                        throw new \Exception(': NO SE REALIZÓ EL PROCESO COMPLETO: registroSolicitudDeExcedenteMateriales
                                                                <br/> <b>'.$arrayVerificar['mensaje'].'</b><br/>');
                                        }
                                    }
                                    else
                                    {
                                        if($strEstadoSolicitudExcTodas =='Aprobado')
                                        {
                                            if($strValorCobrado==0)
                                            {
                                                throw new \Exception(': EL CLIENTE NO TIENE UN VALOR A FACTURAR, VERIFIQUE EL EXCEDENTE');
                                            }
                                            
                                            //ANULO LA SOLICITUD APROBADA DE MATERIALES EXCEDENTES
                                            $strEstadoEnviado          = "Anulada";
                                            $strSeguimiento            .= '<br/>Se anula solicitud #'
                                                                        .$intIdSolicitudExcedenteTodas.' porque se revalidan los datos';
                                            $strSeguimientoSol         = 'Se anula solicitud #'.$intIdSolicitudExcedenteTodas.' 
                                                                         porque se revalidan los datos';
                                            $arrayParametrosAnulSolExc = array(
                                                                        "emComercial"                => $emComercial,
                                                                        "strClienteIp"               => $strClienteIp,
                                                                        "intIdSolicitudExcedente"    => $intIdSolicitudExcedenteTodas,
                                                                        "strSeguimiento"             => $strSeguimientoSol,
                                                                        "strUsrCreacion"             => $strUsrCreacion,
                                                                        "strEstadoEnviado"           => $strEstadoEnviado);
                                            $arrayVerificar = $serviceAutorizaciones
                                                                ->anulacionSolicitudDeExcedenteMateriales($arrayParametrosAnulSolExc);
                                            if($arrayVerificar['status'] == 'ERROR' )
                                            {
                                            throw new \Exception(': NO SE REALIZÓ EL PROCESO COMPLETO: anulacionSolicitudDeExcedenteMateriales
                                                                    <br/> <b>'.$arrayVerificar['mensaje'].'</b><br/>');
                                            }
                                            $strObservacionEnviar    .= '<br/>Se anula solicitud #.'.$intIdSolicitudExcedenteTodas.'
                                                                         porque se revalidan los datos';

                                            //CREO LA INFO DETALLE SOLICICITUD DE MATERIALES EXCEDENTES Aprobado
                                            $strSeguimiento       .= '<br/>Solicitud nueva de excedente de material autorizada 
                                                                     por aplicativo';
                                            $strSeguimientoSol    = 'Solicitud nueva de excedente de material autorizada   por aplicativo';
                                            $strEstadoEnviado     = "Aprobado";
                                            $strObservacionEnviar .= '<br/><b>Solicitud de excedente de material autorizada por aplicativo</b>,
                                                                    en estado:'. $strEstadoEnviado.'<br/>';
                                            $arrayParametrosSolExc = array(
                                                                        "emComercial"                => $emComercial,
                                                                        "strClienteIp"               => $strClienteIp,
                                                                        "entityTipoSolicitud"        => $entityTipoSolicitud,
                                                                        "objServicio"                => $objServicio,
                                                                        "strSeguimiento"             => $strSeguimientoSol,
                                                                        "strUsrCreacion"             => $strUsrCreacion,
                                                                        "strEstadoEnviado"           => $strEstadoEnviado);
                                            $arrayVerificar    = $serviceSolicitud->registroSolicitudDeExcedenteMateriales($arrayParametrosSolExc);
                                            $intIdSolicitudGtn = $arrayVerificar['intIdSolicitud'];
                                            if($arrayVerificar['status'] == 'ERROR' )
                                            {
                                            throw new \Exception(': NO SE REALIZÓ EL PROCESO COMPLETO: registroSolicitudDeExcedenteMateriales
                                                                    <br/> <b>'.$arrayVerificar['mensaje'].'</b><br/>');
                                            }

                                            if(is_object($entityDetalleSolMaterial))
                                            {
                                                //ACTUALIZO LOS DATOS EN INFO DETALLE SOLICITUD MATERIAL
                                                $entityDetalleSolMaterial->setCostoMaterial($strCostoMaterial);
                                                $entityDetalleSolMaterial->setPrecioVentaMaterial($strPrecioVentaMaterial);
                                                $entityDetalleSolMaterial->setCantidadEstimada($intCantidadEstimada);
                                                $entityDetalleSolMaterial->setCantidadCliente($intCantidadCliente);
                                                $entityDetalleSolMaterial->setValorCobrado($strValorCobrado);
                                                $entityDetalleSolMaterial->setUsrCreacion($strUsrCreacion);
                                                $entityDetalleSolMaterial->setFeCreacion(new \DateTime('now'));
                                                $entityDetalleSolMaterial->setIpCreacion($strClienteIp);
                                                $entityDetalleSolMaterial->setCantidadUsada($intCantidadUsada);
                                                $entityDetalleSolMaterial->setCantidadFacturada($intCantidadFacturada);
                                                $emComercial->persist($entityDetalleSolMaterial);
                                                $emComercial->flush();
                                            }
                                        }
                                    }

                                    if((is_object($objDetalleSolicitudPla)) &&                        
                                    ($strEvidencia) && ($floatCanceladoPorCliente != 0) && ($objServicio->getEstado()!='Factible'))
                                    {
                                        if($strValorCobrado==0)
                                        {
                                            throw new \Exception(': EL CLIENTE NO TIENE UN VALOR A FACTURAR, VERIFIQUE EL EXCEDENTE');
                                        }

                                        if(is_object($entityDetalleSolMaterial))
                                        {
                                            //ACTUALIZO LOS DATOS EN INFO DETALLE SOLICITUD MATERIAL
                                            $entityDetalleSolMaterial->setCostoMaterial($strCostoMaterial);
                                            $entityDetalleSolMaterial->setPrecioVentaMaterial($strPrecioVentaMaterial);
                                            $entityDetalleSolMaterial->setCantidadEstimada($intCantidadEstimada);
                                            $entityDetalleSolMaterial->setCantidadCliente($intCantidadCliente);
                                            $entityDetalleSolMaterial->setValorCobrado($strValorCobrado);
                                            $entityDetalleSolMaterial->setUsrCreacion($strUsrCreacion);
                                            $entityDetalleSolMaterial->setFeCreacion(new \DateTime('now'));
                                            $entityDetalleSolMaterial->setIpCreacion($strClienteIp);
                                            $entityDetalleSolMaterial->setCantidadUsada($intCantidadUsada);
                                            $entityDetalleSolMaterial->setCantidadFacturada($intCantidadFacturada);
                                            $emComercial->persist($entityDetalleSolMaterial);
                                            $emComercial->flush();
                                        }
                                        else  
                                        {        
                                            //ENVÍO NUEVOS VALORES EN INFO DETALLE SOLICITUD MATERIAL                                   
                                            $arrayParametrosSolMat  = array(
                                                                    "emComercial"                => $emComercial,
                                                                    "strClienteIp"               => $strClienteIp,
                                                                    "intIdDetalleSolicitud"      => $intIdInfoDetalleSolicitudPlan,
                                                                    "strUsrCreacion"             => $strUsrCreacion,
                                                                    "strCodigoMaterial"          => $strCodigoMaterial,
                                                                    "strCostoMaterial"           => $strCostoMaterial,
                                                                    "strPrecioVentaMaterial"     => $strPrecioVentaMaterial,
                                                                    "intCantidadEstimada"        => $intCantidadEstimada,
                                                                    "intCantidadCliente"         => $intCantidadCliente,
                                                                    "intCantidadUsada"           => $intCantidadUsada,
                                                                    "intCantidadFacturada"       => $intCantidadFacturada,
                                                                    "strValorCobrado"            => $strValorCobrado);                    
                                            $arrayVerificar = $serviceAutorizaciones->registroSolicitudMaterial($arrayParametrosSolMat);
                                            if($arrayVerificar['status'] == 'ERROR' )
                                            {
                                                throw new \Exception(': NO SE REALIZÓ EL PROCESO: registroSolicitudMaterial  
                                                                        <br/> <b>'.$arrayVerificar['mensaje'].'</b>');
                                            }
                                        }
                                    }
                                }

                                if(($strEstadoServicio !== 'Anulado') )
                                {
                                    // Si el servico está en detenido lo preplanifica
                                    $boolPreplanificaAutomaticamente = true;
                                }
                                
                            }
                            /*  Consecuencias si NO se autoriza automáticamente */
                            else 
                            {
                                // Si existe solicitud en estado pendiente
                                if($objDetalleSolicitudExc)
                                {
                                    $boolExisteSolicitud = true;

                                    $strObservacionEnviar .= '<br/><b>Revalidaci&oacute;n de solcitud!<b> <br/>. 
                                                            <br/>Solicitud de excedente de material '.' #'.$objDetalleSolicitudExc->getId();

                                    $strSeguimiento  .= '<b>Revalidación de solcitud!. <b> <br/>'.
                                                        'Se validó la solicitud de excedente de material</b> <br/>'
                                                        .' #'.$objDetalleSolicitudExc->getId();

                                    if ( ($strEvidencia) && ($floatCanceladoPorCliente != 0) )
                                    {
                                        $strSeguimiento    .= '. <br/> Con valores copagos: 
                                                                La empresa asume $'.$floatAsumeEmpresa.'<br/>';
                                    }

                                    /* GUARDAR INFO DETALLE SOLICICITUD  HISTORIAL*/
                                    $strEstadoEnviado  = $objDetalleSolicitudExc->getEstado();
                                    $strSeguimientoSol = '<b>Revalidación de solcitud!. <b> <br/>'.
                                                    'Se validó la solicitud de excedente de material</b> <br/>'
                                                    .' #'.$objDetalleSolicitudExc->getId();
                                    $arrayParametrosTraSol = array(
                                                                "emComercial"                => $emComercial,
                                                                "strClienteIp"               => $strClienteIp,
                                                                "objDetalleSolicitudExc"     => $objDetalleSolicitudExc,
                                                                "strObservacion"             => $strSeguimientoSol,
                                                                "strUsrCreacion"             => $strUsrCreacion,
                                                                "strEstadoEnviado"           => $strEstadoEnviado );
                                    $arrayVerificar = $serviceAutorizaciones->registroTrazabilidadDeLaSolicitud($arrayParametrosTraSol);
                                    
                                    if($arrayVerificar['status'] == 'ERROR' )
                                    {
                                    throw new \Exception(': NO SE REALIZÓ EL PROCESO COMPLETO: registroTrazabilidadDeLaSolicitud
                                                            <br/> <b>'.$arrayVerificar['mensaje'].'</b>');
                                    }

                                    /* FIN Validamos si existe solicitud de excedente de material pendiente*/                                
                                }
                                else
                                {
                                    // Preguntamos que no tenga ninguna solicitud excedente para poder crearla pendiente
                                    if(!is_object($objDetalleSolicitudExcTodas))
                                    {
                                        $strSeguimiento       .= ' <br/><b>Se crea solicitud de excedente de material a GTN</b>';
                                        
                                        //CREO LA INFO DETALLE SOLICITUD Y EL HISTORIAL DE SOL. MATERIALES EXCEDENTES
                                        $strEstadoEnviado = "Pendiente";
                                        $strSeguimientoSol = 'Aplicativo no autoriza el valor de $'.$floatAsumeEmpresa.',
                                                             <br/><b>Se crea solicitud de excedente de material a GTN</b>';
                                        $arrayParametrosSolExc = array(
                                                                    "emComercial"                => $emComercial,
                                                                    "strClienteIp"               => $strClienteIp,
                                                                    "entityTipoSolicitud"        => $entityTipoSolicitud,
                                                                    "objServicio"                => $objServicio,
                                                                    "strSeguimiento"             => $strSeguimientoSol,
                                                                    "strUsrCreacion"             => $strUsrCreacion,
                                                                    "strEstadoEnviado"           => $strEstadoEnviado);
                                        $arrayVerificar    = $serviceSolicitud->registroSolicitudDeExcedenteMateriales($arrayParametrosSolExc);
                                        $intIdSolicitudGtn = $arrayVerificar['intIdSolicitud'];
                                        if($arrayVerificar['status'] == 'ERROR' )
                                        {
                                        throw new \Exception(': NO SE REALIZÓ EL PROCESO COMPLETO: registroSolicitudDeExcedenteMateriales
                                                                <br/> <b>'.$arrayVerificar['mensaje'].'</b><br/>');
                                        }
                                        
                                        $strObservacionEnviar    .= '<br/>Se crea solicitud de excedente de material a GTN'.''.
                                                                    ', con # Solicitud: '.$intIdSolicitudGtn;

                                        if ( ($strEvidencia) && ($floatCanceladoPorCliente != 0) )
                                        {
                                            $strObservacionEnviar .= '. <br> Con valores copagos: La empresa asume $'.$floatAsumeEmpresa;
                                        }
                                    }
                                    else
                                    {
                                        if($strEstadoSolicitudExcTodas =='Aprobado')
                                        {
                                            //ANULO LA SOLICITUD APROBADA DE MATERIALES EXCEDENTES
                                            $strEstadoEnviado        = "Anulada";
                                            $strSeguimiento         .= 'Se anula solicitud #
                                                                    '.$intIdSolicitudExcedenteTodas.' porque se revalidan los datos';
                                            $strSeguimientoSol         = 'Se anula solicitud #.'.$intIdSolicitudExcedenteTodas.'
                                                                     porque se revalidan los datos';
                                            $arrayParametrosAnulSolExc = array(
                                                                        "emComercial"                => $emComercial,
                                                                        "strClienteIp"               => $strClienteIp,
                                                                        "intIdSolicitudExcedente"    => $intIdSolicitudExcedenteTodas,
                                                                        "strSeguimiento"             => $strSeguimientoSol,
                                                                        "strUsrCreacion"             => $strUsrCreacion,
                                                                        "strEstadoEnviado"           => $strEstadoEnviado);
                                            $arrayVerificar = $serviceAutorizaciones
                                                                ->anulacionSolicitudDeExcedenteMateriales($arrayParametrosAnulSolExc);
                                            if($arrayVerificar['status'] == 'ERROR' )
                                            {
                                            throw new \Exception(': NO SE REALIZÓ EL PROCESO COMPLETO: anulacionSolicitudDeExcedenteMateriales
                                                                    <br/> <b>'.$arrayVerificar['mensaje'].'</b><br/>');
                                            }
                                            $strObservacionEnviar    .= '<br/>Se anula solicitud #.'.$intIdSolicitudExcedenteTodas.'
                                                                        porque se revalidan los datos';
                                            
                                            
                                            $strSeguimiento     .= ' <br/><b>Se crea nueva solicitud 
                                                                de excedente de material a GTN</b>';
                                            
                                            //CREO LA INFO DETALLE SOLICITUD Y EL HISTORIAL DE SOL. MATERIALES EXCEDENTES
                                            $strEstadoEnviado  = "Pendiente";
                                            $strSeguimientoSol = 'Aplicativo no autoriza el valor de $'.$floatAsumeEmpresa.',
                                                                 <br/><b>Se crea nueva solicitud de excedente de material a GTN</b>';
                                            $arrayParametrosSolExc = array(
                                                                        "emComercial"                => $emComercial,
                                                                        "strClienteIp"               => $strClienteIp,
                                                                        "entityTipoSolicitud"        => $entityTipoSolicitud,
                                                                        "objServicio"                => $objServicio,
                                                                        "strSeguimiento"             => $strSeguimientoSol,
                                                                        "strUsrCreacion"             => $strUsrCreacion,
                                                                        "strEstadoEnviado"           => $strEstadoEnviado);
                                            $arrayVerificar    = $serviceSolicitud
                                                                ->registroSolicitudDeExcedenteMateriales($arrayParametrosSolExc);
                                            $intIdSolicitudGtn = $arrayVerificar['intIdSolicitud'];
                                            if($arrayVerificar['status'] == 'ERROR' )
                                            {
                                            throw new \Exception(': NO SE REALIZÓ EL PROCESO COMPLETO: registroSolicitudDeExcedenteMateriales
                                                                    <br/> <b>'.$arrayVerificar['mensaje'].'</b><br/>');
                                            }
                                            
                                            $strObservacionEnviar    .= '<br/><br/>Se crea solicitud de excedente de material a GTN'.''.
                                                                        ', con # Solicitud: '.$intIdSolicitudGtn;

                                            if ( ($strEvidencia) && ($floatCanceladoPorCliente != 0) )
                                            {
                                                $strObservacionEnviar .= '.<br/> Con valores copagos: La empresa asume $'.$floatAsumeEmpresa;
                                            }
                                        }  
                                        else
                                        {
                                            $strSeguimiento       .= ' <br/><b>Solicitud en estado '.$strEstadoSolicitudExcTodas.'</b>';
                                        }
                                    } // fin del if y else a cualquier solicitud de materiales excedentes
                                } // fin de pregunta si hay una solicitud de materiales excedentes en pendiente
                                
                                    
                                    /* Inicio de envío de Correo a GTN para notificar de la solicitud de Excedende de Material. */
                                    $arrayParametrosMail = array(
                                                                "login"        => $objServicio->getPuntoId()->getLogin(),
                                                                "producto"     => $objServicio->getProductoId()->getDescripcionProducto(),
                                                                "mensaje"      => $strObservacionEnviar
                                                                );
                                    $strAsunto  = "Se creó una Solicitud de Materiales Excedentes "
                                                . "| login: ".$objServicio->getPuntoId()->getLogin() ;    
                                                
                                    $arrayParametrosNotif = array(
                                                                "strAsunto"                      => $strAsunto,
                                                                "arrayParametrosMail"            => $arrayParametrosMail,
                                                                "arrayDestinatario"              => 'GTN',
                                                                "strCodEmpresa"                  => $strCodEmpresa,
                                                                "serviceEnvioPlantilla"          => $serviceEnvioPlantilla,
                                                                "arrayFormasContactoAsistente"   => '' ,
                                                                "arrayFormasContactoAsesor"      => '',
                                                                "arrayFormasContactoAGtn"       => $arrayFormasContactoAGtn);
                                    $arrayVerificar = $serviceSolicitud->envioDeNotificaciones($arrayParametrosNotif);
                                    if($arrayVerificar['status'] == 'ERROR' )
                                    {
                                    throw new \Exception(': NO SE REALIZÓ EL PROCESO COMPLETO: envioDeNotificaciones
                                                            <br/> <b>'.$arrayVerificar['mensaje'].'</b>');
                                    }/* Fin de envío de Correo  */
                                    
                            } // fin si no autoriza
                        } //fin de si validar
                        
                        $strMail  .= $strObservacionEnviar;
                        $arrayParametrosMail = array(
                                                "login"      => $objServicio->getPuntoId()->getLogin(),
                                                "producto"   => $objServicio->getProductoId()->getDescripcionProducto(),
                                                "mensaje"    => $strMail );
                    
                        // GUARDAR INFO SERVICIO HISTORIAL - InfoServicioHistorial
                        $strEstadoEnviado = $objServicio->getEstado();
                        $arrayParametrosTraServ = array(
                                                    "emComercial"                => $emComercial,
                                                    "strClienteIp"               => $strClienteIp,
                                                    "objServicio"                => $objServicio,
                                                    "strSeguimiento"             => $strSeguimiento,
                                                    "strUsrCreacion"             => $strUsrCreacion,
                                                    "strAccion"                  => $strAccion,
                                                    "strEstadoEnviado"           => $strEstadoEnviado );
                        $arrayVerificar = $serviceAutorizaciones->registroTrazabilidadDelServicio($arrayParametrosTraServ);
                        if($arrayVerificar['status'] == 'ERROR' )
                        {
                        throw new \Exception(': NO SE REALIZÓ EL PROCESO COMPLETO: registroTrazabilidadDelServicio
                                                <br/> <b>'.$arrayVerificar['mensaje'].'</b>');
                        }

                        // Notificación por correo al área comercial respectiva.
                        $arrayParametrosNotif = array(
                                                    "strAsunto"                      => $strAsunto,
                                                    "arrayParametrosMail"            => $arrayParametrosMail,
                                                    "arrayDestinatario"              => 'Alias',
                                                    "strCodEmpresa"                  => $strCodEmpresa,
                                                    "serviceEnvioPlantilla"          => $serviceEnvioPlantilla,
                                                    "arrayFormasContactoAsistente"    => $arrayFormasContactoAsistente,
                                                    "arrayFormasContactoAsesor"         => $arrayFormasContactoAsesor,
                                                    "arrayFormasContactoAGtn"        => $arrayFormasContactoAGtn  );
                        $arrayVerificar = $serviceSolicitud->envioDeNotificaciones($arrayParametrosNotif);
                        if($arrayVerificar['status'] == 'ERROR' )
                        {
                        throw new \Exception(': NO SE REALIZÓ EL PROCESO COMPLETO: envioDeNotificaciones
                                                <br/> <b>'.$arrayVerificar['mensaje'].'</b>');
                        }

                        if($boolPreplanificaAutomaticamente)
                        {
                            $strEstadoEnviado = 'PrePlanificada';
                            $arrayParametrosPrePla = array(
                                                "emComercial"                => $emComercial,
                                                "strEstadoEnviado"           => $strEstadoEnviado,
                                                "objServicio"                => $objServicio,
                                                "strClienteIp"               => $strClienteIp,
                                                "strUsrCreacion"             => $strUsrCreacion);
                            $arrayVerificar = $serviceAutorizaciones->registroEstadoPrePlanificadaInfoDetalleSolicitud($arrayParametrosPrePla);
                            if($arrayVerificar['status'] == 'ERROR' )
                            {
                            throw new \Exception(': NO SE REALIZÓ EL PROCESO COMPLETO: registroEstadoPrePlanificadaInfoDetalleSolicitud
                                                    <br/> <b>'.$arrayVerificar['mensaje'].'</b>');
                            }
                        }
                        
                        if(is_object($objDetalleSolicitudPla))
                        {
                            // GUARDAR SOLICITUD HISTORIAL - InfoDetalleSolHist
                            $arrayParametrosTraSol = array(
                                                        "emComercial"                => $emComercial,
                                                        "strClienteIp"               => $strClienteIp,
                                                        "objDetalleSolicitudExc"     => $objDetalleSolicitudPla,
                                                        "strObservacion"             => $strSeguimientoSol,
                                                        "strUsrCreacion"             => $strUsrCreacion,
                                                        "strEstadoEnviado"           => $strEstadoEnviado );
                            $arrayVerificar = $serviceAutorizaciones->registroTrazabilidadDeLaSolicitud($arrayParametrosTraSol);
                            
                            if($arrayVerificar['status'] == 'ERROR' )
                            {
                            throw new \Exception(': NO SE REALIZÓ EL PROCESO COMPLETO: registroTrazabilidadDeLaSolicitud
                                                    <br/> <b>'.$arrayVerificar['mensaje'].'</b>');
                            }
                        }
                        $strRespuesta   = $strSeguimiento;
                       $emComercial->getConnection()->commit();
                  }
                  else
                  {
                    $strSeguimiento = '<b> Actualmente está en estado'. $strEstadoServicio.', </b> ,no se puede proceder';
                    $strRespuesta = $strSeguimiento; 
                  }
            }




            /* Entonces, es del módulo PYL y no valida, solo envia a comercial, envía notificaciones,registra historial, etc */
            else   
            {
               if( ($strEstadoInfoDetalleSolicitud == 'PrePlanificada') || ($strEstadoInfoDetalleSolicitud == 'Replanificada'))
               {
                //se valida que se requieren materiales adicionales u obras civiles
                if(($floatPrecioObraCivil!=0) || ($floatPrecioOtrosMate!=0)) 
                {
                    /*1.-	"Que se validen materiales adicionales u obras civiles de OTS y que el cliente AUTORICE EL COBRO */
                    if (($strEvidencia) &&  ($floatCanceladoPorCliente!=0))
                    {
                         $strSeguimiento = 'Envío a comercial los siguientes valores<br/> 
                                            que se requieren en materiales adicionales 
                                            <br/>u obras civiles. <br/>'.
                                            'El cliente autoriza el cobro<br/>'
                                            .'<br/> <b>Servicio: </b>' . $objServicio->getProductoId()->getDescripcionProducto()
                                            .'<br/><b>Observación:</b>  << '. $strObservacion.'>>. <br/>'
                                            .$strTablaDeValores
                                            .'<br/> <br/><b>Nota: DETENER LA OT: </b>';

                        $strObservacionEnviar = 'Env&iacute;o a comercial los siguientes valores que se requieren en materiales adicionales
                                                u obras civiles. <br/>'.
                                              'El cliente autoriza el cobro<br/>'
                                            . ' <br/> <b>Servicio: </b> ' . $objServicio->getProductoId()->getDescripcionProducto()
                                            . ' <br/>- ' . $objServicio->getDescripcionPresentaFactura().'<br/>'
                                            . '<br/><b>Observaci&oacute;n:</b>  << '. $strObservacion.'>>. '
                                            .$strTablaDeValoresEnviar;
                    }
                    else
                    {
                        $strSeguimiento = 'Envío a comercial los siguientes valores
                                            <br/> que se requieren en materiales adicionales u obras civiles<br/>'
                                            . ' <br/> <b>Servicio: </b>' . $objServicio->getProductoId()->getDescripcionProducto()
                                            . '<br/><b>Observación:</b>  << '. $strObservacion.'>>.'
                                            .$strTablaDeValores
                                            .' <br/> <b>Nota: DETENER LA OT: </b>';

                        $strObservacionEnviar = 'Env&iacute;o a comercial los siguientes valores que se requieren en materiales adicionales
                                                u obras civiles.'.
                                                '<br/> <b>Servicio: </b>' . $objServicio->getProductoId()->getDescripcionProducto()
                                                . ' - ' . $objServicio->getDescripcionPresentaFactura().'<br/>'
                                                . '<br/><b>Observaci&oacute;n:</b>  << '. $strObservacion.'>>. '
                                                .$strTablaDeValoresEnviar;
                    }
                 }
                 else
                 {
                   // No hay materiales adicionales u obras civiles de OTS y que el cliente AUTORICE EL COBRO"
                   if (($strEvidencia) &&  ($floatCanceladoPorCliente!=0))
                   {
                        $strSeguimiento     = 'Envío a comercial los siguientes valores 
                                             <br/> El cliente autoriza el cobro  
                                             <br/> <b>Servicio: </b>:' 
                                            . $objServicio->getProductoId()->getDescripcionProducto()
                                            .$strTablaDeValores
                                            .'<br/><b>Observación:</b>  << '. $strObservacion.'>>. <br/>'
                                            .'<br/><b>Nota: DETENER LA OT: </b>';

                        $strObservacionEnviar = 'Env&iacute;o a comercial los valores que el cliente autoriza'
                                                . ' <br/> <b>Servicio: </b> ' . $objServicio->getProductoId()->getDescripcionProducto()
                                                . ' - ' . $objServicio->getDescripcionPresentaFactura().'<br/>'
                                                .$strTablaDeValoresEnviar
                                                . '<br/><b>Observaci&oacute;n:</b>  << '. $strObservacion.'>>. ';
                   }
                   /* 2.-	Que se validen materiales adicionales u obras civiles de OTS y que el cliente 
                            NO AUTORICE EL COBRO, pero APLICATIVO APRUEBA */
                   else
                   {
                       $strSeguimiento      = 'Envío a comercial los siguientes valores 
                                            <br/> del servicio: ' . $objServicio->getProductoId()->getDescripcionProducto()
                                            .$strTablaDeValores
                                            .'<br/><b>Observación:</b>  << '. $strObservacion.'>>.'  
                                            .' <br/><b>Nota: DETENER LA OT: </b>';

                       $strObservacionEnviar = 'Env&iacute;o a comercial los siguientes valores 
                                                <br/> <b>Servicio: </b>' . $objServicio->getProductoId()->getDescripcionProducto()
                                                .' - ' . $objServicio->getDescripcionPresentaFactura().'<br/>'
                                                .'<br/><b>Observaci&oacute;n:</b>  << '. $strObservacion.'>>.' 
                                                .$strTablaDeValoresEnviar;
                   }
                 }
                    $strAsunto = "Se envió a comercial los valores de otros clientes | login: "
                                                    . $objServicio->getPuntoId()->getLogin();

                    /* INI enviar notificacion*/ 
                    $arrayParametrosMail = array(
                                    "login"                 => $objServicio->getPuntoId()->getLogin(),
                                    "producto"              => $objServicio->getProductoId()->getDescripcionProducto(),
                                    "mensaje"               => $strObservacionEnviar
                                    );
                        
                    // Notificación por correo al alias pyl y al área comercial respectiva.

                    $arrayParametrosNotif = array(
                                    "strAsunto"                         => $strAsunto,
                                    "arrayParametrosMail"               => $arrayParametrosMail,
                                    "arrayDestinatario"                 => 'Alias',
                                    "strCodEmpresa"                     => $strCodEmpresa,
                                    "serviceEnvioPlantilla"             => $serviceEnvioPlantilla,
                                    "arrayFormasContactoAsistente"       => $arrayFormasContactoAsistente,
                                    "arrayFormasContactoAsesor"         => $arrayFormasContactoAsesor,
                                    "arrayFormasContactoAGtn"           => $arrayFormasContactoAGtn
                                 );
                    $arrayVerificar = $serviceSolicitud->envioDeNotificaciones($arrayParametrosNotif);
                    if($arrayVerificar['status'] == 'ERROR' )
                    {
                        throw new \Exception(': NO SE REALIZÓ EL PROCESO COMPLETO: envioDeNotificaciones
                                            <br/> <b>'.$arrayVerificar['mensaje'].'</b>');
                    }
                        
                    //GUARDAR INFO SERVICIO HISTORIAL - InfoServicioHistorial,
                    $strEstadoEnviado = $objServicio->getEstado();
                    $arrayParametrosTraServ = array(
                                "emComercial"                => $emComercial,
                                "strClienteIp"               => $strClienteIp,
                                "objServicio"                => $objServicio,
                                "strSeguimiento"             => $strSeguimiento,
                                "strUsrCreacion"             => $strUsrCreacion,
                                "strAccion"                  => $strAccion,
                                "strEstadoEnviado"           => $strEstadoEnviado );
                    $arrayVerificar = $serviceAutorizaciones->registroTrazabilidadDelServicio($arrayParametrosTraServ);
                    if($arrayVerificar['status'] == 'ERROR' )
                    {
                        throw new \Exception(': NO SE REALIZÓ EL PROCESO COMPLETO:registroTrazabilidadDelServicio
                                            <br/> <b>'.$arrayVerificar['mensaje'].'</b>');
                    }

                else
                {       
                    $strRespuesta = $strSeguimiento;         
                    $emComercial->getConnection()->commit();
                }
                }
                else
                {
                    $strSeguimiento = '<b/> Actualmente está en estado'. $strEstadoServicio.', <br/> no se puede proceder';
                    $strRespuesta = $strSeguimiento; 
                }
            }
         }
        }
        catch (\Exception $e)
        {
            $emComercial->getConnection()->rollback();
            $serviceUtil->insertError('Telcos+', 
                                        'CoordinarController->validadorExcedenteMaterialAction', 
                                        $e->getMessage(), 
                                        $objSession->get('user'), 
                                        $strClienteIp
                                    );
       
            $arrayParametrosLog['enterpriseCode']   = "18";
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = "validadorExcedenteMaterialAction";
            $arrayParametrosLog['appClass']         = "validadorExcedenteMaterialAction";
            $arrayParametrosLog['appMethod']        = "validadorExcedenteMaterial";
            $arrayParametrosLog['appAction']        = "noDefinido";
            $arrayParametrosLog['messageUser']      = "Ocurrió un error en el validador de excedentes";
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['descriptionError'] = $e->getMessage();
            $arrayParametrosLog['inParameters']     = $strRespuesta;
            $arrayParametrosLog['creationUser']     = "TELCOS";
            $serviceUtil->insertLog($arrayParametrosLog);

            $objResponse = new Response(json_encode(array('mensaje' => 'ERROR'.$e->getMessage())));
            $objResponse->headers->set('Content-type', 'text/json');
            return $objResponse;

        }
       
        $arrayParametrosLog['enterpriseCode']   = "18";
        $arrayParametrosLog['logType']          = "1";
        $arrayParametrosLog['logOrigin']        = "TELCOS";
        $arrayParametrosLog['application']      = "validadorExcedenteMaterialAction";
        $arrayParametrosLog['appClass']         = "validadorExcedenteMaterialAction";
        $arrayParametrosLog['appMethod']        = "validadorExcedenteMaterial";
        $arrayParametrosLog['appAction']        = "noDefinido";
        $arrayParametrosLog['messageUser']      = "Proceso completo en el validador de excedentes";
        $arrayParametrosLog['status']           = "Ok";
        $arrayParametrosLog['descriptionError'] = 'validador de excedentes';
        $arrayParametrosLog['inParameters']     = $strRespuesta;
        $arrayParametrosLog['creationUser']     = "TELCOS";
        $serviceUtil->insertLog($arrayParametrosLog);

        $objResponse = new Response(json_encode(array('mensaje' => $strRespuesta)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;

    }


    /* 
     * Documentación para la función 'detalleServicio'.
     *
     * Función que retorna los servicios que no tienen solicitud de planificación.
     *
     * @author mdleon <mdleon@telconet.ec>
     * @version 1.0 01-02-2023
     * 
     * @return Response se retorna los servicios con sus acciones.
     *
     */
    public function detalleServicio($arrayDatos)
    {
       $serviceUtil = $this->get('schema.Util');
       $intStart       = $arrayDatos['start'];
       $intLimit       = $arrayDatos['limit'];
       try
       {
           $objJson = $this->getDoctrine()->getManager("telconet")
                                           ->getRepository('schemaBundle:InfoDetalleSolicitud')
                                           ->getRegistrosServiciosProyecto($intStart, $intLimit, $arrayDatos);
           if(empty($objJson))
           {
               throw new \Exception("No se encontro datos del servicio.");
           }
           if($objJson["ultimaMillaId"])
           {
                $objUltimaMilla =  $this->getDoctrine()->getManager("telconet")->getRepository('schemaBundle:AdmiTipoMedio')
                                        ->findOneById($objJson["ultimaMillaId"]);
           }
           $arrayRegistros = $objJson['registros'][0];
           $arrayEncontrados[]=array(

                    'tipoSolicitud'      => '',
                    'feSolPlanifica'     => '',
                    'fePlanificacion'   => '',
                    'id_factibilidad'   => $arrayRegistros["idDetalleSolicitud"],
                    'servicio'          => $arrayRegistros["idServicio"],
                    'id_punto'          => $arrayRegistros["idPunto"],
                    'estado_punto'      => $arrayRegistros["estadoPunto"],
                    'cliente'           => ucwords(strtolower(trim(($arrayRegistros["razonSocial"] ? $arrayRegistros["razonSocial"]
                                                    : $arrayRegistros["nombres"] . " " . $arrayRegistros["apellidos"])))),
                    'vendedor'          => trim((isset($arrayRegistros["nombreVendedor"]) ?  ($arrayRegistros["nombreVendedor"] ? 
                                                            ucwords(strtolower($arrayRegistros["nombreVendedor"])) : "") : "")),
                    'tipo_orden'        => trim($arrayRegistros["tipoOrden"]),
                    'direccion'         => ucwords(strtolower(trim($arrayRegistros["direccion"]))),
                    'observacion'       => ucwords(trim(strtolower($arrayRegistros["observacion"].'  '.$arrayRegistros["observacion_solicitud"]))),
                    'ciudad'            => ucwords(strtolower(trim(($arrayRegistros["nombreCanton"] ? $arrayRegistros["nombreCanton"]  : "")))),
                    'nombreSector'      => ucwords(strtolower(trim(($arrayRegistros["nombreSector"] ? $arrayRegistros["nombreSector"]  : "")))),
                    'jurisdiccion'      => ucwords(strtolower(trim(($arrayRegistros["nombreJurisdiccion"] ? $arrayRegistros["nombreJurisdiccion"]  
                                                                            : "Sin Jurisdiccion")))),
                    'latitud'           => trim(($arrayRegistros["latitud"] ? $arrayRegistros["latitud"]  : "")),
                    'longitud'          => trim(($arrayRegistros["longitud"] ? $arrayRegistros["longitud"]  : "")),
                    'ultimaMilla'       =>($objUltimaMilla)?$objUltimaMilla->getNombreTipoMedio():"",
                    'strPrefijoEmpresa' => $arrayDatos['prefijoEmpresa']?$arrayDatos['prefijoEmpresa']:"",
                    'estado'            => $arrayRegistros['estadoServicio'],
                    'acciones'          => ''
                        
                    );

            $arrayDatosServicio =json_encode($arrayEncontrados);
            $objResultado= '{"total": 1,"encontrados":'.$arrayDatosServicio.'}';
       }
        catch(\Exception $ex)
        {
            $serviceUtil->insertError('Telcos+', 'CoordinarProyectoController->detalleServicio', $ex->getMessage(), $arrayDatos['usrCreacion']);
            $objResultado= '{"total":"0","encontrados":[]}';
        }
            return $objResultado;
    }
    
    /* 
     * Documentación para la función 'listarProyectoAction'.
     *
     * Función que retorna los proyectos con sus puntos asociados.
     *
     * @author mdleon <mdleon@telconet.ec>
     * @version 1.0 01-02-2023
     * 
     * @return Response se retorna html.
     *
     */
    public function listarProyectoAction()
    {
        $objSession         = $this->get( 'session' );
        $objRequest         = $this->getRequest();
        $serviceUtil        = $this->get('schema.Util');
        $serviceCrm         = $this->get('comercial.ComercialCrm');
        $strUsrCreacion     = $objSession->get('user');
        $strHtml            = "";
        $strStatus          = "OK";
        $strTipoConsulta    = $objRequest->get('strTipo');
        $strRazonSocial     = $objRequest->get('strRazonS')? $objRequest->get('strRazonS'):"";
        $strLogin           = $objRequest->get('strLogin')? $objRequest->get('strLogin'):"";
        $strProyecto        = $objRequest->get('strProyecto')? $objRequest->get('strProyecto'):"";
        $strEstado          = $objRequest->get('strEstado');
        $strValidaProyecto  = "";
        $objResponse        = new JsonResponse();
        $strNombreDocumento = 'No Data';
        $strTipoDocumento   = '';
        $strFormaDocumento  = '';
        $strFechaDoc        = '';
        try
        {
            $arrayDatos = array("strTipoConsulta" => 'Proyecto',
                                 "strRazonS"      => $strRazonSocial,
                                 "strProyecto"    => $strProyecto,
                                 "strLogin"       => $strLogin,
                                 "strEstado"      => $strEstado);
            $arrayDatosProyectos = $this->getDoctrine()->getManager("telconet")
                                           ->getRepository('schemaBundle:InfoServicioProdCaract')
                                           ->getRegistrosProyecto($arrayDatos);
            if(empty($arrayDatosProyectos))
            {
                throw new \Exception("No se encontro datos de proyectos.");
            }
            $arrayProyectos = $arrayDatosProyectos['registros'];
            $strHtml .= '<div class="col-md">';
            $strHtml .= '<div class="body_content k-content">';
            $strHtml .= '<h4 class="panel-title">Listado de Proyectos</h4>';
            $strHtml .= '<div class="panel-group" id="accordion">';
            $strHtmlProyecto = "";
            foreach($arrayProyectos as $arrayProyecto)
            {
                $strIdProyecto     = $arrayProyecto['idProyecto'];
                $strNombreProyecto = $arrayProyecto['nombre'];
                $arrayDatos = array('strProyectoId'   => $strIdProyecto,
                                    'strTipoConsulta' => 'Puntos',
                                    'strLogin'        => $strLogin,
                                    'strEstado'       => $strEstado);
                
                $arrayDatosPuntos = $this->getDoctrine()->getManager("telconet")
                                           ->getRepository('schemaBundle:InfoServicioProdCaract')
                                           ->getRegistrosProyecto($arrayDatos);
                if(empty($arrayDatosPuntos))
                {
                    throw new \Exception("No se encontro datos de los puntos.");
                }
                $strIcon = "";
                $arrayPuntos = $arrayDatosPuntos['registros'];

                if(count($arrayPuntos) > 1)
                {
                    $strIcon = '&nbsp;&nbsp;<i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" '
                        . 'title="El proyecto puede contener mas de un login en otro tipo de estado"></i>';
                }
               
                $strHtmlProyecto .= '<div class="card border-success mb-0">';
                $strHtmlProyecto .= '<div class="card-header" style="height: 35px;>';
                $strHtmlProyecto .= '<h6 class="panel-title">';

                $strHtmlProyecto .= '<a class="accordion-toggle text-dark" data-toggle="collapse" data-parent="#accordion" '
                    . ' style="font-size: 16px;" href="#Pro'.$strIdProyecto.'">';
                $strHtmlProyecto .= '<strong>'.$strNombreProyecto.'</strong>'.$strIcon.'</a>';
                $strHtmlProyecto .= '</h6></div></div>';
                $strHtmlProyecto .= '<div id="Pro'.$strIdProyecto.'" class="panel-collapse collapse in" style="border-color: #28a745; '
                    . 'border-width: 1px; border-style: solid;">';
                $strHtmlProyecto .= '<div class="panel panel-default">';
                
                //Obtenemos los documentos
                $arrayDatos = array('strProyectoId' => $strIdProyecto,
                                    'strTipoConsulta' => 'Servicio');
                
                $arrayDatosServicios = $this->getDoctrine()->getManager("telconet")
                                           ->getRepository('schemaBundle:InfoServicioProdCaract')
                                           ->getRegistrosProyecto($arrayDatos);
                if(empty($arrayDatosServicios))
                {
                    throw new \Exception("No se encontro datos del servicio.");
                }
                $arrayServicioId = $arrayDatosServicios['registros'][0];
                $objServicio = $this->getDoctrine()->getManager("telconet")
                                                                        ->getRepository('schemaBundle:InfoServicio')
                                                                        ->findOneBy(array('id'=> $arrayServicioId['servicioId']));
                $objServicioTecnicoService  = $this->get('tecnico.InfoServicioTecnico');
                $objInfoServicioProdCaract = $objServicioTecnicoService->getServicioProductoCaracteristica($objServicio,
                                                                                                        'ID_PROPUESTA',
                                                                                                         $objServicio->getProductoId());
                

                $arrayParametros      = array("strIdPropuesta"           => $objInfoServicioProdCaract->getValor(),
                                              "strTipoDocumento"         => 'Cotizacion');
                $arrayParametrosWSCrm = array("arrayParametrosCRM"     => $arrayParametros,
                                              "strOp"                  => 'consultaDocumento',
                                              "strFuncion"             => 'procesar');
                $arrayRespuestaWSCrm  = $serviceCrm->getRequestCRM($arrayParametrosWSCrm);
                if(!empty($arrayRespuestaWSCrm["error"]) && isset($arrayRespuestaWSCrm["error"]))
                {
                    throw new \Exception('Error al Obtener el documento en TelcoCrm: '.$arrayRespuestaWSCrm["error"]);
                }

                $arrayResultados = json_decode(json_encode($arrayRespuestaWSCrm['resultado']), true);
                if ($arrayResultados && $arrayResultados != 'not_found')
                {
                    foreach($arrayResultados as $arrayResultado)
                    {
                        $strNombreDocumento = $arrayResultado['strNombreDocumento'];
                        $strFechaDoc      = $arrayResultado['strFechaCreacion'];
                        $strPath          = $arrayResultado['strPathFile'];
                        $strTipoDocumento = $arrayResultado['strTipoDocumento'];
                        if(!empty($strPath))
                        {
                            $arrayArchivo   = explode('/', $strPath);
                            $arrayCount     = count($arrayArchivo);
                            $strNuevoNombre = $arrayArchivo[$arrayCount - 1];
                            $arrayTipoDoc   = explode('.', $strNuevoNombre);
                            $arrayCountT    = count($arrayTipoDoc);
                            $strTipoDoc     = $arrayTipoDoc[$arrayCountT - 1];
                            $strFormaDocumento = $strTipoDoc;
                        }    
                    }
                }
                
                //Agregamos el cuadro para ver documentos
                $strHtmlProyecto .= '<div><nav class="navbar navbar-expand-lg navbar-light bg-light">';
                $strHtmlProyecto .= '<div class="collapse navbar-collapse" id="'.$strIdProyecto.'">';
                $strHtmlProyecto .= '<ul class="navbar-nav mr-auto" style="margin: 0 auto;">';
                $strHtmlProyecto .= '<li class="nav-item active">';
                $strHtmlProyecto .= '<button type="button" class="btn btn-outline-info btn-sm collapsed text-dark" 
                data-toggle="collapse" aria-expanded="false" data-target="#Contenedor'.$strIdProyecto.'">
                <strong>Documento por Proyecto</strong></button> ';
                $strHtmlProyecto .= '</li><li>&nbsp;&nbsp;</li></ul></div></nav></div>';
                $strHtmlProyecto .= '<div id="Contenedor'.$strIdProyecto.'" class="collapse">';
                $strHtmlProyecto .= '<table class="table table-striped table-bordered compact" id="tablaFac'.$strIdProyecto.'" class="display" '
                    . 'style="width:50%; margin: 0 auto;">';
                $strHtmlProyecto .= '<thead class="thead-light"> ';
                $strHtmlProyecto .= '<tr>';
                $strHtmlProyecto .= '<th><input name="select_all"  id="objListado-select-all"  type="checkbox" hidden="true"></th>';
                $strHtmlProyecto .= '<th>Nombre Documento</th>';
                $strHtmlProyecto .= '<th>Extension</th>';
                $strHtmlProyecto .= '<th>Archivo</th>';
                $strHtmlProyecto .= '<th>Fe. Creacion</th>';
                $strHtmlProyecto .= '<th>Acciones</th>';
                $strHtmlProyecto .= '</tr>';
                
                //recuperamos los archivos del proyecto
                $strHtmlProyecto .= '</tr>';
                $strHtmlProyecto .= '<td>1</td>';
                $strHtmlProyecto .= '<td>'.$strNombreDocumento.'</td>';
                $strHtmlProyecto .= '<td>'.$strFormaDocumento.'</td>';
                $strHtmlProyecto .= '<td>'.$strTipoDocumento.'</td>';
                $strHtmlProyecto .= '<td>'.$strFechaDoc.'</td>';
                $strHtmlProyecto .=  '<td><span class=`hint--bottom-left hint--default hint--medium hint--rounded btnVerArchivoDigital` 
                                aria-label="Ver Archivo Digital"><button type="button" class="btn btn-default btn-xs" 
                                onclick="window.open(`'.addslashes($strPath).'`, `_blank`);">
                                <i class="fa fa-search"></i></button></span></td>';

                $strHtmlProyecto .= '</tr>';
                //fin de archivo proyecto
                $strHtmlProyecto .= '</thead></table></div></div>';


                //fin documentos
                //Acordeon para Logines
                $strHtmlProyecto .= '<br><h4 class="panel-title" style="width:95%; margin: 0 auto;">Listado de Puntos</h4>';
                $strHtmlProyecto .= '<div class="panel-group" id="accordion'.$strIdProyecto.'" style="width:95%; margin: 0 auto;">';
                $strHtmlProyecto .= '<div class="panel panel-default template2">';
                
                //Fin logines
                $strHtmlPuntos = "";
                foreach($arrayPuntos as $arrayPunto)
                {
                    
                    $intPuntoId = $arrayPunto['puntoId'];
                    if(empty($intPuntoId))
                    {
                        throw new \Exception("No se encontro datos del Login.");
                    }
                    $objPunto = $this->getDoctrine()->getManager("telconet")
                                           ->getRepository('schemaBundle:InfoPunto')
                                           ->findOneBy(array('id'=> $intPuntoId));
                    //dibujamos el punto
                    $strHtmlPuntos .= '<div class="card border-success mb-0">';
                    $strHtmlPuntos .= '<div class="card-header" style="height: 35px;">';
                    $strHtmlPuntos .= '<h6 class="panel-title">';
                    $strHtmlPuntos .= '<a class="accordion-toggle2 text-dark" data-toggle="collapse" data-parent="#accordion" '

                        . ' style="font-size: 16px;" href="#Log'.$intPuntoId.'" onclick="diseñar('.$intPuntoId.')">';
                    $strHtmlPuntos .= '<strong>'.$objPunto->getLogin().'</strong></a>';
                    $strHtmlPuntos .= '</h6></div></div>';
                    //fin punto
                    $strHtmlPuntos .= '<div id="Log'.$intPuntoId.'" class="panel-collapse collapse in">';    
                    $strHtmlPuntos .= '<div class="panel panel-default">';
                    $strHtmlPuntos .= '<div class="panel" id="panel'.$intPuntoId.'" ></div>';
                    $strHtmlPuntos .= '</div></div>';
                    
                }
                $strHtmlProyecto .= $strHtmlPuntos;
                $strHtmlProyecto .= '<br></div></div></div>';
            }
            if(empty($arrayProyectos))
            {
                $strStatus = 'OK';
                $strHtml .= '<div class="info-success" style="padding:5px!important; margin: 15px 0px!important;">
                No se encontraron datos para los criterios de búsqueda seleccionados.</div>';
            }
            else
            {
                $strHtml .= $strHtmlProyecto;
            }
            $strHtml .= '</div></div>';            
        } catch (\Exception $ex) 
        {
            $strStatus = 'ERROR';
            $serviceUtil->insertError('Telcos+', 'CoordinarProyectoController->detalleServicio', $ex->getMessage(), $strUsrCreacion);
        }
        $objResponse->setData(array('strStatus' => $strStatus , 'strPlantillaProyecto' => $strHtml));
        
        return $objResponse;
    }
    
    /* 
     * Documentación para la función 'getListarServiciosAction'.
     *
     * Función que filtra solo los servicios permitidos a realizar seguimiento.
     *
     * @author mdleon <mdleon@telconet.ec>
     * @version 1.0 01-02-2023
     * 
     * @return Response se retorna los servicios que se mostraran en la ventana de seguimiento.
     *
     */
    public function getListarServiciosAction()
    {
        $emGeneral        = $this->getDoctrine()->getManager();
        $objRequest       = $this->getRequest();
		$objSession       = $objRequest->getSession();
		$intEmpresaId     = $objSession->get('idEmpresa');

        $arrayParametros  = array();
        $arrayParametros["ESTADOS"]        = 'Todos';
        $arrayParametros["strFechaDesde"]  = explode('T',$objRequest->get('fechaDesde'))[0];
        $arrayParametros["strFechaHasta"]  = explode('T',$objRequest->get('fechaHasta'))[0];
        $arrayParametros["strProducto"]    = $objRequest->get('producto');
        $arrayParametros["strEstado"]      = $objRequest->get('strEstado');
        $arrayParametros['PUNTO']          = $objRequest->get('strPunto');
        $arrayParametros['EMPRESA']        = $intEmpresaId;
        $arrayParametros['START']          = 0;
        $arrayParametros['LIMIT']          = 10;
        $intTotal                          = 0;
        
        
        $arrayListado = $emGeneral->getRepository('schemaBundle:InfoServicio')->getResultadoServiciosPorEmpresaPorPunto($arrayParametros);
        if(!empty($arrayListado))
        {
            foreach($arrayListado as $dato):
                $arrayParametrosDet =   $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->getOne("SEGUIMIENTO_PRODUCTOS", 
                                                                         "COMERCIAL", 
                                                                         "", 
                                                                         "", 
                                                                         $dato->getProductoId()->getDescripcionProducto(), 
                                                                         "", 
                                                                         "",
                                                                         "",
                                                                         "",
                                                                         $intEmpresaId
                                                                       );
                

                    if( $intTotal>=1)
                    {
                        $strDivPaneles .=',';
                    }else
                    {
                        $strDivPaneles ='{"data":[{"title":"default","hidden":true},';
                    }

                    $strTabStatus = '<table width=100%  cellpadding=1 cellspacing=0  border=0><tr>'
                        . '<td><div overflow=scroll, id=getPanelEstatus'.$dato->getId().'></div></td></tr></table>';
                    $strTitulo  ="";
                    $strDiv     ="";
                    $strTitulo  = '{"title"'.':'.'"'.$dato->getProductoId()->getDescripcionProducto().'","id":'.$dato->getId();
                    $strDiv     = '"html":"'.$strTabStatus.'<div  class=seguimiento_content id=seguimiento_content_'.$dato->getId().'></div>';
                    $strDiv    .= '<table width=100% cellpadding=1 cellspacing=0  border=0><tr>'
                        . '<td><div overflow=scroll, id=getPanelSeguimiento'.$dato->getId().'></div></td></tr></table>"';
                    $strExpand  = 'listeners: { expand: function() {grafica('.$dato->getId().'); '
                        . 'seguimiento_content_'.$dato->getId().'.Update();getPanelSeguimiento'.$dato->getId().'.Update();}}}';
                    $strDivPaneles .=$strTitulo.",".$strDiv.",".$strExpand;

                     $intTotal++;

            endforeach;
        if (!empty($strDivPaneles))
        {
            $strDivPaneles .= "]}";
        }
            $arrayArreglo[] = array(
                'datos'                        => $strDivPaneles
                );
        }
        if(!empty($arrayArreglo))
        {
            $objResponse = new Response(json_encode(array('total' => $intTotal, 'servicios' => $arrayArreglo)));
        }
        else
        {
            $arrayArreglo[] = array();
            $objResponse = new Response(json_encode(array('total' => $intTotal, 'servicios' => $arrayArreglo)));
        }
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
        
    }
        
    /* 
     * Documentación para la función 'getDetallesServiciosAction'.
     *
     * Función que devuelve los mensajes de los Tooltip a mostrarce en el grafico.
     *
     * @author mdleon <mdleon@telconet.ec>
     * @version 1.0 26-02-2023
     * 
     * @return Response se retorna los tooltip a mostrarce.
     *
     */
    public function getDetallesServiciosAction()
    {
        $objRequest        = $this->get('request');
		$objSession        = $objRequest->getSession();
		$intEmpresaId      = $objSession->get('idEmpresa');
        $objRequest        = $this->getRequest();
        $objSession        = $objRequest->getSession();
        $objReturnResponse = new ReturnResponse();

        $emComercial = $this->getDoctrine()->getManager("telconet");

        $arrayParametros['intIdSolicitud'] = $objRequest->get('intIdSolicitud');
        $arrayParametros['strCodigosEstaciones'] = $objRequest->get('strCodigosEstaciones');
        
        $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT);
        
        $arrayResultado = array();
        
        try
        {
            if(!empty($arrayParametros['intIdSolicitud']))
            {
                $objDetalleServicio    = $emComercial->getRepository('schemaBundle:InfoServicio')->find($arrayParametros['intIdSolicitud']);
                
                
                if(is_object($objDetalleServicio) && !empty($objDetalleServicio))
                {
                    $arrayParametrosDet =   $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->getOne("SEGUIMIENTO_PRODUCTOS", 
                                                                         "COMERCIAL", 
                                                                         "", 
                                                                         "", 
                                                                         $objDetalleServicio->getProductoId()->getDescripcionProducto(), 
                                                                         "", 
                                                                         "",
                                                                         "",
                                                                         "",
                                                                         $intEmpresaId
                                                                       );
                    if(!is_array($arrayParametrosDet) && empty($arrayParametrosDet))
                    {
                        $strFlujo = 'NO_TRADICIONAL';
                    }
                    else
                    {
                        $strFlujo   =   $arrayParametrosDet['valor2'];
                    }
                    $arrayParametrosEstServ =   $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->get("ESTADOS_SEGUIMIENTO_PRODUCTOS_PROYECTO", 
                                                                     "COMERCIAL", 
                                                                     "", 
                                                                     "", 
                                                                     "", 
                                                                     $strFlujo, 
                                                                     $objDetalleServicio->getEstado(),
                                                                     "",
                                                                     "",
                                                                     $intEmpresaId,
                                                                     "valor1"
                                                                   );

                    if(!is_array($arrayParametrosEstServ) && empty($arrayParametrosEstServ))
                    {
                        throw $this->createNotFoundException('No existe el Detalle de Estado de Seguimiento.');
                    }
                    $intValor1 = intval($arrayParametrosEstServ[0]['valor1']) ? intval($arrayParametrosEstServ[0]['valor1']): 0;
                    $arrayEstados        = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->get("ESTADOS_SEGUIMIENTO_PRODUCTOS_PROYECTO", 
                                                                     "COMERCIAL", 
                                                                     "", 
                                                                     "", 
                                                                     "", 
                                                                     $strFlujo, 
                                                                     "",
                                                                     "",
                                                                     "",
                                                                     $intEmpresaId,
                                                                     "valor1"
                                                                   );
                    foreach ($arrayEstados as $estacion) 
                    {
                        $strHtml ="";
                        $strEstado =  "inactivo";    
                        switch ($estacion['valor3'])
                        {
                            case "Pre-servicio":
                                $intEstadoIni = intval($estacion['valor1']);
                                $intValor1 = intval($arrayParametrosEstServ[0]['valor1']);
                                if($intEstadoIni <= $intValor1)
                                {
                                    $strHtml = "<p class='estacionItem ei_success'>La Solicitud fue creada exitosamente!</p>";
                                    $strEstado = "activo";
                                }
                                $arrayResultado[$estacion['valor3']] = array("contenido" => $strHtml, "estado" => $strEstado);
                                break;
                            case "Factible":
                                $arrayFactible = $emComercial->getRepository('schemaBundle:InfoServicioHistorial')->findBy(
                                                                                array('servicioId'=> $arrayParametros['intIdSolicitud'],
                                                                                      'estado'    => 'Factible'));
                                if($estacion['valor1'] <= $arrayParametrosEstServ[0]['valor1'])
                                {
                                    $objPreFactibi = $emComercial->getRepository('schemaBundle:InfoServicioHistorial')->findOneBy(
                                                                                array('servicioId'=> $arrayParametros['intIdSolicitud'],
                                                                                      'estado'    => 'PreFactibilidad'));
                                    
                                    $objFactibiProceso = $emComercial->getRepository('schemaBundle:InfoServicioHistorial')->findOneBy(
                                                                                array('servicioId'=> $arrayParametros['intIdSolicitud'],
                                                                                      'estado'    => 'FactibilidadEnProceso'));
                                    
                                    if(!is_object($objPreFactibi) && empty($objPreFactibi) && 
                                        !is_object($objFactibiProceso) && empty($objFactibiProceso))
                                    {
                                        $strHtml .= "<p class='estacionItem ei_success'>Factibilidad Automatica del Servicio!</p>";

                                    }
                                    else
                                    {
                                        $strHtml .= "<p class='estacionItem ei_success'>Prefactibilidad Atendida!</p>";
                                        $strHtml .= "<p class='estacionItem ei_success'>FactibilidadEnProceso Atendida!</p>";
                                    }
                                    
                                    $strEstado = "activo";
                                }else
                                {
                                    if($objDetalleServicio->getEstado()=='FactibilidadEnProceso')
                                    {
                                        $strHtml .= "<p class='estacionItem ei_success'>Prefactibilidad Atendida!</p>";
                                        $strHtml .= "<p class='estacionItem ei_info'>FactibilidadEnProceso Pendiente!</p>";
                                    }
                                    else
                                    {
                                        $strHtml .= "<p class='estacionItem ei_info'>Prefactibilidad Pendiente!</p>";
                                        $strHtml .= "<p class='estacionItem ei_info'>FactibilidadEnProceso Pendiente!</p>";
                                    }
                                }
                                $strHtml .= "<p class='estacionItem ei_info'>Total de veces Factible: ".count($arrayFactible)."</p>";
                                $arrayResultado[$estacion['valor3']] = array("contenido" => $strHtml, "estado" => $strEstado);
                                break;
                            case "Disponibilidad Recursos":
                                $objServicioTecnicoService  = $this->get('tecnico.InfoServicioTecnico');
                                $objInfoServicioProdCaract = $objServicioTecnicoService->getServicioProductoCaracteristica($objDetalleServicio,
                                                                                           'PEDIDO_ESTADO',$objDetalleServicio->getProductoId());
                                if(!is_object($objInfoServicioProdCaract) && empty($objInfoServicioProdCaract))
                                {
                                    $strHtml .= "<p class='estacionItem ei_info'>No se encuentra el Pedido!</p>";
                                }
                                else
                                {
                                    $strEstadoPedido = $objInfoServicioProdCaract->getValor();
                                    $strHtml .= "<p class='estacionItem ei_success'>Pedido Generado!</p>";
                                    if($estacion['valor1'] > $intValor1-2 && $strEstadoPedido != 'BODEGA')
                                    {
                                        if($strEstadoPedido == 'PEDIDO_GENERADO')
                                        {
                                            $strHtml .= "<p class='estacionItem ei_info'>Cotizado!</p>";
                                            $strHtml .= "<p class='estacionItem ei_info'>Orden de Compra!</p>";
                                            $strHtml .= "<p class='estacionItem ei_info'>Ingreso a Bodega!</p>";
                                        }
                                        else if ($strEstadoPedido == 'COTIZADO')
                                        {
                                            $strHtml .= "<p class='estacionItem ei_success'>Cotizado!</p>";
                                            $strHtml .= "<p class='estacionItem ei_info'>Orden de Compra!</p>";
                                            $strHtml .= "<p class='estacionItem ei_info'>Ingreso a Bodega!</p>";
                                        }
                                        else if ($strEstadoPedido == 'ORDEN_COMPRA')
                                        {
                                            $strHtml .= "<p class='estacionItem ei_success'>Cotizado!</p>";
                                            $strHtml .= "<p class='estacionItem ei_success'>Orden de Compra!</p>";
                                            $strHtml .= "<p class='estacionItem ei_info'>Ingreso a Bodega!</p>";
                                        }
                                        else
                                        {
                                            $strHtml .= "<p class='estacionItem ei_info'>Estado del Pedido no Encontrado!</p>";
                                        }
                                    }else
                                    {
                                        $strHtml .= "<p class='estacionItem ei_success'>Cotizado!</p>";
                                        $strHtml .= "<p class='estacionItem ei_success'>Orden de Compra!</p>";
                                        $strHtml .= "<p class='estacionItem ei_success'>Ingreso a Bodega!</p>";
                                        $strEstado = "activo";
                                    }
                                }
                                $arrayResultado[$estacion['valor3']] = array("contenido" => $strHtml, "estado" => $strEstado);
                                break;    
                            case "Planificada":
                                $arrayPrePlanificado = $emComercial->getRepository('schemaBundle:InfoServicioHistorial')->findBy(
                                                                                array('servicioId'=> $arrayParametros['intIdSolicitud'],
                                                                                      'estado'    => 'PrePlanificada'));
                                    
                                $arrayReplanificado = $emComercial->getRepository('schemaBundle:InfoServicioHistorial')->findBy(
                                                                                array('servicioId'=> $arrayParametros['intIdSolicitud'],
                                                                                      'estado'    => 'Replanificada'));
                                if($estacion['valor1'] < $arrayParametrosEstServ[0]['valor1'])
                                {
                                    if(empty($arrayReplanificado))
                                    {
                                        $strHtml .= "<p class='estacionItem ei_success'>Preplanificación realizada!</p>";
                                        $strHtml .= "<p class='estacionItem ei_info'>Total de veces Preplanificado: "
                                                                                                                .count($arrayPrePlanificado)."</p>";
                                    }
                                    else
                                    {
                                        $strHtml .= "<p class='estacionItem ei_success'>Preplanificación realizada!</p>";
                                        $strHtml .= "<p class='estacionItem ei_info'>Total de veces Preplanificado: "
                                                                                                                .count($arrayPrePlanificado)."</p>";
                                        $strHtml .= "<p class='estacionItem ei_success'>Replanificación Realizada!</p>";
                                        $strHtml .= "<p class='estacionItem ei_info'>Total de veces Replanificado: "
                                                                                                                  .count($arrayReplanificado)."</p>";
                                    }

                                    $strEstado = "activo";
                                }else
                                {
                                    if($objDetalleServicio->getEstado()=='Replanificada')
                                    {
                                        $strHtml .= "<p class='estacionItem ei_success'>Preplanificación Atendida!</p>";
                                        $strHtml .= "<p class='estacionItem ei_info'>Total de veces Preplanificado: "
                                                                                                                .count($arrayPrePlanificado)."</p>";
                                        $strHtml .= "<p class='estacionItem ei_info'>Replanificación Pendiente!</p>";
                                    }
                                    else
                                    {
                                        $strHtml .= "<p class='estacionItem ei_info'>Preplanificación Pendiente!</p>";
                                    }
                                }
                                $arrayResultado[$estacion['valor3']] = array("contenido" => $strHtml, "estado" => $strEstado);
                                break;
                            case "AsignadoTarea":
                                $arrayAsignadoTare = $emComercial->getRepository('schemaBundle:InfoServicioHistorial')->findBy(
                                                                                array('servicioId'=> $arrayParametros['intIdSolicitud'],
                                                                                      'estado'    => 'AsignadoTarea'));
                                if($estacion['valor1'] < $arrayParametrosEstServ[0]['valor1'])
                                {
                                    $strHtml .= "<p class='estacionItem ei_success'>Asignación de Recursos de Red Realizada</p>";
                                    $strEstado = "activo";
                                }else
                                {
                                    $strHtml .= "<p class='estacionItem ei_info'>Asignación de Recursos de Red Pendiente</p>";
                                    
                                }
                                $strHtml .= "<p class='estacionItem ei_info'>Total de veces AsignadoTarea: ".count($arrayAsignadoTare)."</p>";
                                $arrayResultado[$estacion['valor3']] = array("contenido" => $strHtml, "estado" => $strEstado);
                                break;
                            case "Asignada":
                                
                                if($estacion['valor1'] < $arrayParametrosEstServ[0]['valor1'])
                                {
                                    $strHtml .= "<p class='estacionItem ei_success'>Asignación de Equipo Realizada</p>";
                                    $strEstado = "activo";
                                }else
                                {
                                    $strHtml .= "<p class='estacionItem ei_info'>Asignación de Equipo Pendiente</p>";
                                    
                                }
                                
                                $arrayResultado[$estacion['valor3']] = array("contenido" => $strHtml, "estado" => $strEstado);
                                break;
                            case "Activo":
                                                            
                                if($estacion['valor1'] <= $arrayParametrosEstServ[0]['valor1'])
                                {
                                    $strHtml .= "<p class='estacionItem ei_success'>Activación Realizada</p>";
                                    $strEstado = "activo";
                                }else
                                {
                                    $strHtml .= "<p class='estacionItem ei_info'>Activación Pendiente</p>";
                                    
                                }
                                
                                $arrayResultado[$estacion['valor3']] = array("contenido" => $strHtml, "estado" => $strEstado);
                                break;
                            default:
                                $strHtml ="<p class='estacionItem ei_error'>Estación no Encontrada</p>";
                                $arrayResultado[$estacion] = array("contenido" => $strHtml, "estado" => $strEstado);
                                break;
                        }                        
                    }
                }
            }
            else
            {
                $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . ' No esta enviando el código de la solicitud.');
            }
            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
            $objReturnResponse->setStrMessageStatus(json_encode($arrayResultado));
        }
        catch(\Exception $ex)
        {
            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . " " . $ex->getMessage());
        }
        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');
        $objResponse->setContent(json_encode((array) $objReturnResponse));
        return $objResponse;
    }    
    
    /* 
     * Documentación para la función 'graficaServicioAction'.
     *
     * Función que devuelve los estado por los cuales pasa el servicio.
     *
     * @author mdleon <mdleon@telconet.ec>
     * @version 1.0 26-02-2023
     * 
     * @return Response se retorna los estado del servicio a consultar.
     *
     */
    public function graficaServicioAction ()
    {
        $emGeneral        = $this->getDoctrine()->getManager();
		$objRequest       = $this->get('request');
        $objRequest       = $this->getRequest();
		$objSession       = $objRequest->getSession();
		$intEmpresaId     = $objSession->get('idEmpresa');
        $intServicioId    = $objRequest->get('objServicio');
        $arrayEstado      = array();
        $intTotal         =   0;
        
        if (empty($intServicioId))
        {
            throw $this->createNotFoundException('No existe el Servicio.');
        }
        $objServicio    =   $emGeneral->getRepository('schemaBundle:InfoServicio')->find($intServicioId);
        if (!empty($objServicio) && $objServicio!='')
        {
            $strDescripcionProd =   $objServicio->getProductoId()->getDescripcionProducto();
                if($objServicio->getEstado()!="" )
                {
                    if(empty($strDescripcionProd) && $strDescripcionProd='')
                    {
                        throw new \Exception("No se encuentra la Descripción del Producto."); 
                    }
                    //CONSULTAMOS EL PRODUCTO Y SUS ESTADOS
                    $arrayParametrosDet =   $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->getOne("SEGUIMIENTO_PRODUCTOS", 
                                                                         "COMERCIAL", 
                                                                         "", 
                                                                         "", 
                                                                         $strDescripcionProd, 
                                                                         "", 
                                                                         "",
                                                                         "",
                                                                         "",
                                                                         $intEmpresaId
                                                                       );
                    if(!is_array($arrayParametrosDet) && empty($arrayParametrosDet))
                    {
                        $strFlujo = 'NO_TRADICIONAL';
                    }   
                    else
                    {
                        $strFlujo   =   $arrayParametrosDet['valor2'];
                    }
                    $arrayParametrosDetEst =   $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->get("ESTADOS_SEGUIMIENTO_PRODUCTOS_PROYECTO", 
                                                                     "COMERCIAL", 
                                                                     "", 
                                                                     "", 
                                                                     "", 
                                                                     $strFlujo, 
                                                                     "",
                                                                     "",
                                                                     "",
                                                                     $intEmpresaId,
                                                                     "valor1"
                                                                   );

                    if(!is_array($arrayParametrosDetEst) && empty($arrayParametrosDetEst))
                    {
                        throw $this->createNotFoundException('No existe el Detalle de Estado de Seguimiento.');
                    }
                    $arrayEstadoSegui   = array();
                    foreach($arrayParametrosDetEst as $arrayEstadoServicio)
                    {
                        if($arrayEstadoServicio['valor7']=='true')
                        {
                        $arrayEstado   =  array ($arrayEstadoServicio['valor3'],$arrayEstadoServicio['valor3'],$arrayEstadoServicio['valor4'],
                                                 $arrayEstadoServicio['valor5'],$arrayEstadoServicio['valor6']);
                        }
                        array_push($arrayEstadoSegui,$arrayEstado);
                        $intTotal++;
                    }    
                    if(!empty($arrayEstadoSegui))
                    {
                        $arrayEstado = $arrayEstadoSegui;
                    }
                    
                }
        }
        $objResponse = new Response(json_encode(array_values($arrayEstado)));
        
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    
    /* 
     * Documentación para la función 'getHistorialSeguimientoAction'.
     *
     * Función que devuelve el historial a mostrarce.
     *
     * @author mdleon <mdleon@telconet.ec>
     * @version 1.0 26-02-2023
     * 
     * @return Response se retorna el historial del servicio a consultar.
     *
     */
    public function getHistorialSeguimientoAction()
    {
        $objRequest          = $this->getRequest();
        $intContador         = 0;
        $emComercial         = $this->getDoctrine()->getManager("telconet");
        $objResponse         = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');
        $intServicioId = $objRequest->get('objServicio');

        $arrayResultado       = array();
        $arrayEncontrados     = array();
        if(!empty($intServicioId) && $intServicioId != 0)
        {
            $arrayParametros    =   array();
            $arrayParametros['intServicioId']    = intval($intServicioId);
            $arrayResultado     = $emComercial->getRepository('schemaBundle:InfoSeguimientoServicio')->historialSeguimiento($arrayParametros);
        }
        if(!empty($arrayResultado) && $arrayResultado['strStatus']=='OK')
        {
           $objSeguimientos = $arrayResultado['arrayData'];
           foreach($objSeguimientos as $objData)
           {
               $intMinutosTrans =0;
               $intDiasTrans    =0;
               if(is_null($objData->getTiempoTranscurrido()))
               {
                   $objActual = new \DateTime('now');
                   $intMinDiff = $objData->getFeCreacion()->diff($objActual);
                   $intMinutosTrans = ( ($intMinDiff->days * 24 ) * 60 ) +($intMinDiff->h  * 60 )+ ( $intMinDiff->i );
                   $intDiasTrans    = number_format($intMinutosTrans/24/60, 2, '.', '');
               }
               else
               {
                   $intMinutosTrans = $objData->getTiempoTranscurrido();
                   $intDiasTrans    = number_format($objData->getDiasTranscurrido(), 2, '.', '');
               }
               $intContador++;
               $arrayEncontrados[] = array(
                        'usrCreacion'          => $objData->getUsrCreacion(),
                        'servicioId'           => $objData->getServicioId()->getId(),
                        'observacion'          => $objData->getObservacion(),
                        'departamento'         => $objData->getDepartamento(),
                        'estado'               => $objData->getEstado(),
                        
                        'feCreacion'           => strval(date_format($objData->getFeCreacion(),"d/m/Y G:i")),
                        'feModificacion'       => strval(date_format($objData->getFeModificacion(),"d/m/Y G:i")),
                        'ipModificacion'       => $objData->getIpCreacion(),
                        'tiempoEstimado'       => $objData->getTiempoEstimado(),
                        'tiempoTranscurrido'   => $intMinutosTrans,
                        'diasTranscurrido'     => $intDiasTrans
                   );
        }
        $arrayDatos = json_encode($arrayEncontrados);
        $objJson = '{"total":"' . $intContador . '","encontrados":' . $arrayDatos . '}';
        $objResponse->setContent($objJson);
                   
        }
        return $objResponse;
    }
    
    /**
     * Documentación para la función 'getEstadosAction'.
     *
     * Función que obtiene los estados de las solicitudes de proyecto.
     *
     * @return Response - Lista de estados.
     *
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.0 20-02-2023
     *
     */
    public function getEstadosAction()
    {
        try
        {
            $objRequest             = $this->getRequest();
            $objSession             = $objRequest->getSession();
            $intIdEmpresa           = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa'):"";
            $strUsrCreacion         = $objSession->get('user')      ? $objSession->get('user'):"";
            $strIpCreacion          = $objRequest->getClientIp()    ? $objRequest->getClientIp():'127.0.0.1';
            $arrayEstados           = array();
            $emGeneral              = $this->get('doctrine')->getManager('telconet');
            $serviceUtil            = $this->get('schema.Util');
            $arrayListEstados       = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->get('ESTADO_PEDIDO_ARTICULO', 
                                                      'COMERCIAL', 
                                                      '', 
                                                      '', 
                                                      '', 
                                                      '', 
                                                      '', 
                                                      '', 
                                                      '', 
                                                      $intIdEmpresa);
            foreach($arrayListEstados as $arrayItem)
            {
                $arrayEstados[] = array('id'     => $arrayItem['valor1'], 
                                        'nombre' => $arrayItem['valor1']);
            }
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError('Telcos', 'getEstadosAction', $e->getMessage(), $strUsrCreacion, $strIpCreacion);
        }
        $objResponse = new Response(json_encode(array('estados' => $arrayEstados)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    
    /**
     * Documentación para la función 'programarProyectoAction'.
     *
     * Función para programar los diferentes trabajos en la ventana de proyecto.
     *
     * @return Responses.
     *
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.0 20-02-2022
     *
     */
    public function programarProyectoAction()
    {
        $objRespuesta   = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $emComercial    = $this->getDoctrine()->getManager('telconet');
        $emGeneral      = $this->getDoctrine()->getManager("telconet_general");        
        $emSoporte      = $this->getDoctrine()->getManager('telconet_soporte');
        $emComunicacion = $this->getDoctrine()->getManager("telconet_comunicacion");
        $objPeticion    = $this->get('request');
        $objSession     = $objPeticion->getSession();
        $serviceGeneral = $this->get('tecnico.InfoServicioTecnico');
        $serviceCambiarPlanService = $this->get('tecnico.InfoCambiarPlan');
        $serviceInfoServicio = $this->get('comercial.infoservicio');
        $serviceCrm         = $this->get('comercial.ComercialCrm');
        $emNaf            = $this->get('doctrine')->getManager("telconet_naf");
     
        $objDatos         = $objPeticion->get('listado');
        $arrayListadoP      = json_decode($objDatos, true);
        if(is_array($arrayListadoP) && count($arrayListadoP) > 0)
        {
            foreach($arrayListadoP as $arrayListadoPla)
            {
                $arrayParametros = array();
                if($arrayListadoPla['tarea_adicional'] === false)
                {
                    $objPersona = $emComercial->getRepository('schemaBundle:InfoPersona')->find($arrayListadoPla['empleado_id']);
                    if(is_object($objPersona))
                    {
                        $objPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                            ->findByIdentificacionTipoRolEmpresa($objPersona->getIdentificacionCliente(), 'Empleado', $objSession->get('idEmpresa'));
                        if(is_object($objPersonaEmpresaRol))
                        {
                            $arrayParametros['intIdPersonaEmpresaRol'] = $objPersonaEmpresaRol->getId();
                        }
                    }
                    
                    //PLANIFICACIÓN NORMAL                    
                    $arrayParametros['boolProyecto']           = true;
                    $arrayParametros['tarea_id']               = $arrayListadoPla['tarea_id'];
                    $arrayParametros['intIdPerEmpRolSesion']   = $objSession->get('idPersonaEmpresaRol');
                    $arrayParametros['strOrigen']              = $objPeticion->get('origen');
                    $arrayParametros['intIdFactibilidad']      = $objPeticion->get('id');
                    $arrayParametros['intIdCuadrilla']         = $arrayListadoPla['cuadrilla_id'];
                    $arrayParametros['strParametro']           = $objPeticion->get('param');
                    $arrayParametros['strParamResponsables']   = $arrayListadoPla['paramResponsable'];
                    $arrayParametros['intIdPersona']           = $arrayListadoPla['empleado_id'];
                    $arrayParametros['intIdPerTecnico']        = $arrayListadoPla['ingeniero_id'];
                    $arrayParametros['strCodEmpresa']          = $objSession->get('idEmpresa');
                    $arrayParametros['strPrefijoEmpresa']      = $objSession->get('prefijoEmpresa');
                    $arrayParametros['intIdDepartamento']      = $objSession->get('idDepartamento');
                    $arrayParametros['serviceInfoServicio']    = $serviceInfoServicio;
                    /*Array con los Id's de los servicios internet Wifi para instalacion simultanea*/
                    $arrayParametros['idIntWifiSim']           = $objPeticion->get('idIntWifiSim');
                    $arrayParametros['tipoEsquema']            = $objPeticion->get('tipoEsquema');
                    /*Array con los Id's de los servicios COU LINEAS TELEFONIA FIJA para instalacion simultanea*/
                    $arrayParametros['idIntCouSim']            = $objPeticion->get('idIntCouSim');
                    /* Array con los ID's de los servicios para instalación simultanea en estado pendiente.*/
                    $arrayParametros['arraySimultaneos']       = json_decode($objPeticion->get('arraySimultaneos'));
                    $arrayParametros['tienePersonalizacionOpcionesGridCoordinar'] = $objPeticion->get('tienePersonalizacionOpcionesGridCoordinar');
                    
                    $intOpcion                                 = $objPeticion->get('opcion');
                    $arrayParametrosSolucion = array();

                    $arrayFechaProgramacion = explode("T", $arrayListadoPla['fecha']);
                    $arrayFecha             = explode("T", $arrayListadoPla['hora_inicio']);
                    $arrayF                 = explode("-", $arrayFechaProgramacion[0]);
                    $arrayHoraF             = explode(":", $arrayFecha[1]);
                    $arrayFechaInicio       = date("Y/m/d H:i", strtotime($arrayF[2] . "-" . $arrayF[1] . "-" . $arrayF[0] . " " . $arrayFecha[1]));

                    $arrayFechaI = date_create(date('Y/m/d', strtotime($arrayFecha[0])));    //Fecha de Reprogramacion

                    $arrayFechaI = $arrayFechaI->format("Y-m-d");

                    $arrayFecha2            = explode("T", $arrayListadoPla['hora_fin']);
                    $arrayF2                = explode("-", $arrayFechaProgramacion[0]);
                    $arrayHoraF2            = explode(":", $arrayFecha2[1]);
                    $arrayFechaInicio2      = date("Y/m/d H:i", strtotime($arrayF2[2] . "-" .$arrayF2[1]. "-" . $arrayF2[0] . " " . $arrayFecha2[1]));
                    $strHoraInicioServicio  = $arrayHoraF;
                    $strHoraFinServicio     = $arrayHoraF2;
                    $strHoraInicio          = $arrayHoraF;
                    $strHoraFin             = $arrayHoraF2;

                    $arrayParametros['dateF']                   = $arrayF;
                    $arrayParametros['dateFecha']               = $arrayFecha;
                    $arrayParametros['strFechaInicio']          = $arrayFechaInicio;
                    $arrayParametros['strFechaFin']             = $arrayFechaInicio2;
                    $arrayParametros['strHoraInicioServicio']   = $strHoraInicioServicio;
                    $arrayParametros['strHoraFinServicio']      = $strHoraFinServicio;
                    $arrayParametros['dateFechaProgramacion']   = $arrayFechaI;
                    $arrayParametros['strHoraInicio']           = $strHoraInicio;
                    $arrayParametros['strHoraFin']              = $strHoraFin;
                    $arrayParametros['strObservacionServicio']  = $arrayListadoPla['observacion'];
                    $arrayParametros['strIpCreacion']           = $objPeticion->getClientIp();
                    $arrayParametros['strUsrCreacion']          = $objPeticion->getSession()->get('user');
                    $arrayParametros['strObservacionSolicitud'] = $arrayListadoPla['observacion'];
                    $arrayParametros['arrayEmpresas']           = $objPeticion->getSession()->get('arrayEmpresas');
                    $arrayParametros['strAtenderAntes']         = $objPeticion->get('atenderAntes');
                    $arrayParametros['strEsHal']                = $objPeticion->get('esHal');
                    $arrayParametros['intIdSugerenciaHal']      = $objPeticion->get('idSugerencia');
                    $boolControlaCupo = true;
                    $arrayParamProducNetCam   = $serviceGeneral->paramProductosNetlifeCam();
                    $strFechaReserva        = $objPeticion->get('fechaVigencia');
                    $objDateFechaReserva    = new \DateTime(date('Y-m-d H:i:s',strtotime($strFechaReserva)));
                    $objDateNow             = new \DateTime('now');
                    
                    $entityDetalleSolicitud = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                     ->find($arrayParametros['intIdFactibilidad']);
        
                    $objServicio = $emComercial->getRepository("schemaBundle:InfoServicio")->find($entityDetalleSolicitud->getServicioId()->getId());

                    $intJurisdicionId = $entityDetalleSolicitud->getServicioId()
                                    ->getPuntoId()
                                    ->getPuntoCoberturaId()->getId();

                    $intControlaCupo = $entityDetalleSolicitud->getServicioId()
                                    ->getPuntoId()
                                    ->getPuntoCoberturaId()->getCupo();       

                    if(isset($arrayParametros['tienePersonalizacionOpcionesGridCoordinar']) 
                        && !empty($arrayParametros['tienePersonalizacionOpcionesGridCoordinar'])
                        && $arrayParametros['tienePersonalizacionOpcionesGridCoordinar'] === "SI")
                    {
                        $boolControlaCupo = false;
                    }
                    if (is_object($objServicio) && is_object($objServicio->getProductoId()) && $boolControlaCupo)
                    {
                        $intProductoId = $objServicio->getProductoId()->getId();
                        $intCaracteristicaId = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                      ->findOneByDescripcionCaracteristica('PRODUCTO CONTROLA CUPO');

                        $entityControlaCupo =  $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                     ->findOneBy(array("productoId" => $intProductoId,
                                                                       "caracteristicaId" => $intCaracteristicaId));
                        if (is_object($entityControlaCupo))
                        {
                            $boolControlaCupo = false;
                        }           
                    }
                    //Planifico la solicitud
                    $objServicePlanificacion = $this->get('planificacion.Planificar');
                    $strNombreTecnico = is_object($objServicio->getProductoId()) ? $objServicio->getProductoId()->getNombreTecnico():'';
                    // En el caso de que sea hosting se envía correo 
                    if($strNombreTecnico == 'HOSTING')
                    {
                        $arrayParametrosSolucion['objServicio']   = $objServicio;
                        $arrayParametrosSolucion['strCodEmpresa'] = $objPeticion->getSession()->get('idEmpresa');
                        $strSolucion = $serviceGeneral->getNombreGrupoSolucionServicios($arrayParametrosSolucion);
                        $strNombreCanton = $serviceGeneral->getCiudadRelacionadaPorRegion($objServicio,$objSession->get('idEmpresa'));
                        $objPunto   =  $entityDetalleSolicitud->getServicioId()->getPuntoId();
                        if(!empty($strNombreCanton))
                        {
                            $objCanton = $emGeneral->getRepository("schemaBundle:AdmiCanton")->findOneByNombreCanton($strNombreCanton);

                            if(is_object($objCanton))
                            {
                                $intIdCanton     = $objCanton->getId();
                            }
                        }
                        $strNombreParametro = 'HOSTING TAREAS POR DEPARTAMENTO';
                        $strProceso         = 'INSTALACION MV';
                        $strObservacion     = 'Tarea automática: Creación de Máquinas Virtuales según recursos asignados en el '
                                            . 'Producto POOL DE RECURSOS ( HOSTING ) . '
                                            . '<br><b>Login : </b> '.$objPunto->getLogin()
                                            . '<br>'.$strSolucion;

                        $arrayParametrosEnvioPlantilla                      = array();
                        $arrayParametrosEnvioPlantilla['strUsrCreacion']    = $objPeticion->getSession()->get('user');
                        $arrayParametrosEnvioPlantilla['strIpCreacion']     = $objPeticion->getClientIp();
                        $arrayParametrosEnvioPlantilla['intDetalleSolId']   = $entityDetalleSolicitud->getId();
                        $arrayParametrosEnvioPlantilla['strTipoAfectado']   = 'Cliente';
                        $arrayParametrosEnvioPlantilla['objPunto']          = $objPunto;
                        $arrayParametrosEnvioPlantilla['strCantonId']       = $intIdCanton;
                        $arrayParametrosEnvioPlantilla['strEmpresaCod']     = $objSession->get('idEmpresa');
                        $arrayParametrosEnvioPlantilla['strPrefijoEmpresa'] = $objSession->get('prefijoEmpresa');
                        $arrayParametrosEnvioPlantilla['strObservacion']    = $strObservacion;
                        $arrayInfoEnvio   =   $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->get($strNombreParametro, 
                                                            'SOPORTE', 
                                                            '',
                                                            $strProceso,
                                                            $strNombreCanton,//GUAYAQUIL/UIO 
                                                            '',
                                                            '',
                                                            '', 
                                                            '', 
                                                            $objSession->get('idEmpresa'));
                        foreach($arrayInfoEnvio as $array)                    
                        {
                            $objTarea  = $emSoporte->getRepository("schemaBundle:AdmiTarea")->findOneByNombreTarea($array['valor3']);
                            $arrayParametrosEnvioPlantilla['intTarea']          = is_object($objTarea)?$objTarea->getId():'';

                            if(isset($array['valor2']) && !empty($array['valor2']))
                            {
                                $arrayParametrosEnvioPlantilla['arrayCorreos'] = array($array['valor2']);
                            }

                            $objDepartamento = $emSoporte->getRepository("schemaBundle:AdmiDepartamento")
                                                         ->findOneByNombreDepartamento($array['valor4']);
                            $arrayParametrosEnvioPlantilla['objDepartamento']    = $objDepartamento;
                            $arrayParametrosEnvioPlantilla["strBanderaTraslado"] = "";

                            $serviceCambiarPlanService->crearTareaRetiroEquipoPorDemo($arrayParametrosEnvioPlantilla);
                        }
                    }
                    if ($intOpcion == 0)
                    {
                        $arrayResultado         = $objServicePlanificacion->coordinarPlanificacion($arrayParametros);
                        if(isset($arrayResultado['codigoRespuesta']) && !empty($arrayResultado['codigoRespuesta']) && 
                            $arrayResultado['codigoRespuesta'] > 0)
                        {
                            $strStatusCoordinar = "OK";
                        }
                        else
                        {
                            $strStatusCoordinar = "ERROR";
                        }
                    }
                    else
                    {
                        $strStatusCoordinar = "OK";
                    }

                    $objSolicitud  = $emComercial->getRepository("schemaBundle:InfoDetalleSolicitud")->find($objPeticion->get('id'));

                    $boolEsHousing = false;

                    if(is_object($objSolicitud) && $objPeticion->getSession()->get('prefijoEmpresa') == 'TN')
                    {
                        $objServicio = $emComercial->getRepository("schemaBundle:InfoServicio")->find($objSolicitud->getServicioId()->getId());

                        if(is_object($objServicio) && $objServicio->getProductoId()->getNombreTecnico() == 'HOUSING')
                        {
                            $boolEsHousing = true;
                        }               

                    }
                    
                    $booleanStatusAsignar = false;
                    //Asigno responsable
                    if (
                       (($arrayParametros['strEsHal'] <> "S" && $arrayParametros['strParamResponsables'] <> "" 
                           && !$boolEsHousing && $strStatusCoordinar === "OK")
                        || ($arrayParametros['strEsHal'] === "S" && !$boolEsHousing && $strStatusCoordinar === "OK")) && !is_object($objDetalleSol)
                       )
                    {
                        $booleanStatusAsignar = true;
                        $arrayParametros['objDetalleSolHist']    = $arrayResultado['entityDetalleSolHist'];
                        $arrayParametros['serviceRecursosRed']   = $this->get('planificacion.RecursosDeRed');
                        $arrayParametros['objServicioHistorial'] = $arrayResultado['entityServicioHistorial'];
                        $arrayParametros['strTipoProceso'] = 'Programar';
                        $arrayResultado = $objServicePlanificacion->asignarPlanificacion($arrayParametros);
                        //Regularizacion de tareas NetlifeCam Outdoor
                        $arrayServAdiNet = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                       ->findBy(array('puntoId' => 
                                                       $entityDetalleSolicitud->getServicioId()->getPuntoId(),
                                                                         'estado'  => array('Asignada')));
                        if(!empty($arrayServAdiNet))
                        {
                            foreach($arrayServAdiNet as $arrayServiciosNetCam)
                            {
                                $objSolicitud  = $emComercial->getRepository("schemaBundle:InfoDetalleSolicitud")
                                                    ->findOneBy(array("servicioId" => $arrayServiciosNetCam->getId()));
                                $objInfoDetNet = $emSoporte->getRepository('schemaBundle:InfoDetalle')
                                                    ->findOneBy(array("detalleSolicitudId" => $objSolicitud->getId()));
                                $objInfoTarNet = $emSoporte->getRepository('schemaBundle:InfoTarea')
                                                    ->findOneBy(array("detalleId" => $objInfoDetNet->getId()));
                                $strNomTecNetlife    = is_object($arrayServiciosNetCam->getProductoId()) ? 
                                                        $arrayServiciosNetCam->getProductoId()->getNombreTecnico() : null;
                                if(in_array($strNomTecNetlife,$arrayParamProducNetCam) && !is_object($objInfoTarNet))
                                {
                                    $arrayParametrosInfoTarea['intDetalleId']   = $objInfoDetNet->getId();
                                    $arrayParametrosInfoTarea['strUsrCreacion'] = $objInfoDetNet->getUsrCreacion();                      
                                    $serviceSoporte->crearInfoTarea($arrayParametrosInfoTarea);
                                }
                            }
                        }
                    }
                    /***OBTENER LOS SERVICIOS ADICIONALES PARA PLANIFICACION***/
                    if( is_object($objServicio) && is_object($objServicio->getProductoId()) && $strStatusCoordinar === "OK" )
                    {
                        //seteo id de orden trabajo
                        $objOrdenTrabajoServ = $objServicio->getOrdenTrabajoId();
                        //seteo el status de asignar
                        $booleanStatusAsignar = false;
                        $objSolicitud  = $emComercial->getRepository("schemaBundle:InfoDetalleSolicitud")->find($objPeticion->get('id'));
                        if( is_object($objSolicitud) && $objSolicitud->getEstado() == "AsignadoTarea" )
                        {
                            $booleanStatusAsignar = true;
                        }
                        //seteo variable
                        $booleanServicioSwPoe = false;
                        $objServicioPrincipal = $objServicio;
                        $arrayParametrosDet   = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                          ->getOne('PARAMETROS PROYECTO GPON SAFECITY',
                                                                   'INFRAESTRUCTURA',
                                                                   'PARAMETROS',
                                                                   'VALIDAR RELACION SERVICIO ADICIONAL CON DATOS SAFECITY',
                                                                   $objServicio->getProductoId()->getId(),
                                                                   '',
                                                                   '',
                                                                   '',
                                                                   '',
                                                                   $objSession->get('idEmpresa'));
                        if(!empty($arrayParametrosDet) && isset($arrayParametrosDet["valor5"]) && $arrayParametrosDet["valor5"] == "SWITCHPOE")
                        {
                            $booleanServicioSwPoe       = true;
                            $objCaractServicioPrincipal = $serviceGeneral->getServicioProductoCaracteristica($objServicio,
                                                                    'RELACION_SERVICIOS_GPON_SAFECITY',$objServicio->getProductoId());
                            if(is_object($objCaractServicioPrincipal))
                            {
                                $objServicioPrincipal = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                                        ->find($objCaractServicioPrincipal->getValor());
                                if(!is_object($objServicioPrincipal))
                                {
                                    $objServicioPrincipal = $objServicio;
                                }
                            }
                        }
                        //obtener servicios adicionales
                        $arrayParServAdd = array(
                            "intIdProducto"      => $objServicioPrincipal->getProductoId()->getId(),
                            "intIdServicio"      => $objServicioPrincipal->getId(),
                            "strNombreParametro" => 'CONFIG_PRODUCTO_DATOS_SAFE_CITY',
                            "strUsoDetalles"     => 'AGREGAR_SERVICIO_ADICIONAL',
                        );
                        $arrayProdCaracConfProAdd  = $emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                ->getServiciosPorProdAdicionalesSafeCity($arrayParServAdd);
                        if($arrayProdCaracConfProAdd['status'] == 'OK' && count($arrayProdCaracConfProAdd['result']) > 0)
                        {
                            $arrayContadorAsigSerAdd = array();
                            foreach($arrayProdCaracConfProAdd['result'] as $arrayServicioConfProAdd)
                            {
                                $arrayEstadosSerAdd    = array("PrePlanificada","Planificada","Detenido");
                                $objServicioConfProAdd = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                            ->findOneBy(array("id"             => $arrayServicioConfProAdd['idServicio'],
                                                                              "ordenTrabajoId" => $objOrdenTrabajoServ,
                                                                              "estado"         => $arrayEstadosSerAdd));
                                if(is_object($objServicioConfProAdd) && $objServicioConfProAdd->getId() != $objServicio->getId())
                                {
                                    $objDetSolicitudProAdd = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                        ->findOneBy(array("servicioId"      => $objServicioConfProAdd->getId(),
                                                                          "tipoSolicitudId" => $entityDetalleSolicitud->getTipoSolicitudId()->getId(),
                                                                          "estado"          => $arrayEstadosSerAdd));
                                    if(is_object($objDetSolicitudProAdd))
                                    {
                                        //seteo el descripcion producto
                                        $strDescripcionProductoAdd = $objServicioConfProAdd->getProductoId()->getDescripcionProducto();
                                        //se obtiene los detalles del servicio adicional
                                        $arrayDetallesConfProAdd = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                        ->getOne('CONFIG_PRODUCTO_DATOS_SAFE_CITY',
                                                                                 'COMERCIAL',
                                                                                 '',
                                                                                 '',
                                                                                 $objServicioConfProAdd->getProductoId()->getId(),
                                                                                 'COORDINAR_OBSERVACION',
                                                                                 '',
                                                                                 '',
                                                                                 '',
                                                                                 $arrayParametros['strCodEmpresa']);
                                        if(isset($arrayDetallesConfProAdd) && !empty($arrayDetallesConfProAdd) &&
                                           !empty($arrayDetallesConfProAdd['valor3']) && !empty($arrayDetallesConfProAdd['valor4']))
                                        {
                                            $arrayParametros['strObservacionServicio']  = $arrayDetallesConfProAdd['valor3'];
                                            $arrayParametros['strObservacionSolicitud'] = $arrayDetallesConfProAdd['valor4'];
                                        }
                                        $arrayParametros['intIdFactibilidad'] = $objDetSolicitudProAdd->getId();
                                        $arrayParametros['strParametro']      = $objDetSolicitudProAdd->getId();
                                        //se coordina el servicio adicional
                                        $arrayResultadoProAdd = array();
                                        if($objDetSolicitudProAdd->getEstado() == "PrePlanificada" || 
                                                                                                    $objDetSolicitudProAdd->getEstado() == "Detenido")
                                        {
                                            $arrayResultadoProAdd = $objServicePlanificacion->coordinarPlanificacion($arrayParametros);
                                            if(isset($arrayResultadoProAdd['codigoRespuesta']) && !empty($arrayResultadoProAdd['codigoRespuesta'])
                                               && $arrayResultadoProAdd['codigoRespuesta'] > 0)
                                            {
                                                $strStatusCoordinarServAdd = "OK";
                                            }
                                            else
                                            {
                                                $strStatusCoordinarServAdd = "ERROR";
                                            }
                                        }
                                        else
                                        {
                                            $strStatusCoordinarServAdd = "OK";
                                        }
                                        //se realiza la asignacion del servicio adicional
                                        if( $strStatusCoordinarServAdd == "OK" && $booleanStatusAsignar)
                                        {
                                            //verificar si existe un id de tarea para validar
                                            if(!empty($arrayParametros['strParamResponsables']))
                                            {
                                                $arrayParamResponGpon   = explode("|", $arrayParametros['strParamResponsables']);
                                                $arrayParDetCamSafeCity = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                           ->getOne('NUEVA_RED_GPON_TN',
                                                                                    'COMERCIAL',
                                                                                    '',
                                                                                    'TAREA DE INSTALACION DEL SERVICIO',
                                                                                    $objServicioConfProAdd->getProductoId()->getId(),
                                                                                    '',
                                                                                    '',
                                                                                    '',
                                                                                    '',
                                                                                    $arrayParametros['strCodEmpresa']);
                                                if( isset($arrayParDetCamSafeCity) && !empty($arrayParDetCamSafeCity)
                                                    && isset($arrayParDetCamSafeCity['valor3'])&& !empty($arrayParDetCamSafeCity['valor3']) )
                                                {
                                                    //obtengo la tarea
                                                    $objAdmiTareaGpon = $emSoporte->getRepository('schemaBundle:AdmiTarea')
                                                                                ->findOneBy(array("nombreTarea" => $arrayParDetCamSafeCity['valor3'],
                                                                                                  "estado"      => "Activo"));
                                                    if(is_object($objAdmiTareaGpon))
                                                    {
                                                        $intIdTareaGpon = $objAdmiTareaGpon->getId();
                                                        //verifico los responsables
                                                        foreach($arrayParamResponGpon as $keyDet => $arrayDetResponGpon)
                                                        {
                                                            $arrayVariablesRespon = explode("@@", $arrayDetResponGpon);
                                                            if(is_array($arrayVariablesRespon) && count($arrayVariablesRespon) > 0
                                                               && $arrayVariablesRespon[0] != $intIdTareaGpon)
                                                            {
                                                                $arrayVariablesRespon[0] = $intIdTareaGpon;
                                                                $arrayDetResponGpon      = implode('@@',$arrayVariablesRespon);
                                                                $arrayParamResponGpon[$keyDet] = $arrayDetResponGpon;
                                                                $arrayParametros['strParamResponsables'] = implode("|", $arrayParamResponGpon);
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                            //se realiza la asignacion
                                            $arrayParametros['objDetalleSolHist']    = $arrayResultadoProAdd['entityDetalleSolHist'];
                                            $arrayParametros['serviceRecursosRed']   = $this->get('planificacion.RecursosDeRed');
                                            $arrayParametros['objServicioHistorial'] = $arrayResultadoProAdd['entityServicioHistorial'];
                                            $arrayResAsigServAdd = $objServicePlanificacion->asignarPlanificacion($arrayParametros);
                                            if(isset($arrayResAsigServAdd['idDetalle']) && !empty($arrayResAsigServAdd['idDetalle']))
                                            {
                                                //seteo contador
                                                if(!isset($arrayContadorAsigSerAdd[$strDescripcionProductoAdd]))
                                                {
                                                    $arrayContadorAsigSerAdd[$strDescripcionProductoAdd] = 1;
                                                }
                                                else
                                                {
                                                    $arrayContadorAsigSerAdd[$strDescripcionProductoAdd] += 1;
                                                }
                                                //obtengo el info detalle
                                                $objInfoDetalle = $emSoporte->getRepository('schemaBundle:InfoDetalle')
                                                                                                    ->find($arrayResAsigServAdd['idDetalle']);
                                                if(is_object($objInfoDetalle))
                                                {
                                                    //eliminar característica anterior
                                                    $objCaracSerDetalle = $serviceGeneral->getServicioProductoCaracteristica($objServicioConfProAdd,
                                                                                            'ID_DETALLE_TAREA_INSTALACION',
                                                                                            $objServicioConfProAdd->getProductoId());
                                                    if(is_object($objCaracSerDetalle))
                                                    {
                                                        $objCaracSerDetalle->setEstado("Eliminado");
                                                        $objCaracSerDetalle->setUsrUltMod($objPeticion->getSession()->get('user'));
                                                        $objCaracSerDetalle->setFeUltMod(new \DateTime('now'));
                                                        $emComercial->persist($objCaracSerDetalle);
                                                        $emComercial->flush();
                                                    }
                                                    //se guarda el id del detalle de la tarea
                                                    $objCaracDetalle = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                                 ->findOneByDescripcionCaracteristica('ID_DETALLE_TAREA_INSTALACION');
                                                    if(is_object($objCaracDetalle))
                                                    {
                                                        $objAdmiProDetalle = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                            ->findOneBy(array("productoId"       => $objServicioConfProAdd->getProductoId()->getId(),
                                                                              "caracteristicaId" => $objCaracDetalle->getId(),
                                                                              "estado"           => "Activo"));
                                                        if(is_object($objAdmiProDetalle))
                                                        {
                                                            $objCaracSerDetalle = new InfoServicioProdCaract();
                                                            $objCaracSerDetalle->setServicioId($objServicioConfProAdd->getId());
                                                            $objCaracSerDetalle->setProductoCaracterisiticaId($objAdmiProDetalle->getId());
                                                            $objCaracSerDetalle->setValor($objInfoDetalle->getId());
                                                            $objCaracSerDetalle->setEstado("Activo");
                                                            $objCaracSerDetalle->setUsrCreacion($arrayParametros['strUsrCreacion']);
                                                            $objCaracSerDetalle->setFeCreacion(new \DateTime('now'));
                                                            $emComercial->persist($objCaracSerDetalle);
                                                            $emComercial->flush();
                                                        }
                                                    }
                                                    $arrayParDetEstadoTareaGpon = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                               ->getOne('NUEVA_RED_GPON_TN',
                                                                                        'COMERCIAL',
                                                                                        '',
                                                                                        'CAMBIAR ESTADO TAREA SERVICIO',
                                                                                        $objServicioConfProAdd->getProductoId()->getId(),
                                                                                        '',
                                                                                        '',
                                                                                        '',
                                                                                        '',
                                                                                        $arrayParametros['strCodEmpresa']);
                                                    if( isset($arrayParDetEstadoTareaGpon) && !empty($arrayParDetEstadoTareaGpon)
                                                    && isset($arrayParDetEstadoTareaGpon['valor2'])&& !empty($arrayParDetEstadoTareaGpon['valor2'])
                                                    && isset($arrayParDetEstadoTareaGpon['valor3'])&& !empty($arrayParDetEstadoTareaGpon['valor3']) )
                                                    {
                                                        $arrayParametrosEstado                 = array();
                                                        $arrayParametrosEstado['cargarTiempo'] = "cliente";
                                                        $arrayParametrosEstado['esSolucion']   = "N";
                                                        $arrayParametrosEstado['estado']       = $arrayParDetEstadoTareaGpon['valor2'];
                                                        $arrayParametrosEstado['observacion']  = $arrayParDetEstadoTareaGpon['valor3'];

                                                        $serviceSoporte->cambiarEstadoTarea($objInfoDetalle, null, 
                                                                                            $objPeticion, $arrayParametrosEstado);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            //agregar historial tarea servicio principal
                            if(count($arrayContadorAsigSerAdd) > 0 && isset($arrayResultado['idDetalle']) && !empty($arrayResultado['idDetalle']))
                            {
                                //obtengo el info detalle
                                $objInfoDetalle = $emSoporte->getRepository('schemaBundle:InfoDetalle')->find($arrayResultado['idDetalle']);
                                if(is_object($objInfoDetalle))
                                {
                                    $strObservacionTarea = "";
                                    foreach($arrayContadorAsigSerAdd as $strDescripcionProductoAdd => $strContadorProductoAdd)
                                    {
                                        $strObservacionTarea .= "<br>".$strContadorProductoAdd." ".$strDescripcionProductoAdd;
                                    }
                                    $arrayParametrosHist                   = array();
                                    $arrayParametrosHist["intDetalleId"]   = $objInfoDetalle->getId();
                                    $arrayParametrosHist["strCodEmpresa"]  = $objSession->get('idEmpresa');
                                    $arrayParametrosHist["strUsrCreacion"] = $objSession->get('user');
                                    $arrayParametrosHist["strIpCreacion"]  = $objPeticion->getClientIp();
                                    $arrayParametrosHist["strOpcion"]      = "Seguimiento";
                                    $arrayParametrosHist["strEstadoActual"] = "Asignada";
                                    $arrayParametrosHist["strEnviaDepartamento"] = "N";
                                    $arrayParametrosHist["strObservacion"] = "Se generaron las siguientes tareas de instalación ".
                                                                             "para los servicios adicionales: ".$strObservacionTarea;

                                    $serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);
                                }
                            }
                        }
                    }
                    
                    if(is_object($objServicio) && is_object($objServicio->getProductoId()))
                    {
                        //Validar si el producto esta configurado para servicios adicionales DATOS GPON
                        $arrayParametrosDet = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                          ->getOne('PARAMETROS PROYECTO GPON SAFECITY',
                                                                   'INFRAESTRUCTURA',
                                                                   'PARAMETROS',
                                                                   'VALIDAR RELACION SERVICIO ADICIONAL CON DATOS SAFECITY',
                                                                   $objServicio->getProductoId()->getId(),
                                                                   '',
                                                                   '',
                                                                   '',
                                                                   '',
                                                                   $objSession->get('idEmpresa'));
                        if(!empty($arrayParametrosDet["valor1"]) && isset($arrayParametrosDet["valor1"]))
                        {
                            //eliminar característica anterior
                            $objCaracSerDetalle = $serviceGeneral->getServicioProductoCaracteristica($objServicio,
                                                                    'ID_DETALLE_TAREA_INSTALACION',$objServicio->getProductoId());
                            if(is_object($objCaracSerDetalle))
                            {
                                $objCaracSerDetalle->setEstado("Eliminado");
                                $objCaracSerDetalle->setUsrUltMod($objPeticion->getSession()->get('user'));
                                $objCaracSerDetalle->setFeUltMod(new \DateTime('now'));
                                $emComercial->persist($objCaracSerDetalle);
                                $emComercial->flush();
                            }
                            //se guarda el id del detalle de la tarea
                            $objCaracDetalle = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                            ->findOneByDescripcionCaracteristica('ID_DETALLE_TAREA_INSTALACION');
                            if(is_object($objCaracDetalle) && isset($arrayResultado['idDetalle']))
                            {
                                $objAdmiProDetalle = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                        ->findOneBy(array("productoId"       => $objServicio->getProductoId()->getId(),
                                                          "caracteristicaId" => $objCaracDetalle->getId(),
                                                          "estado"           => "Activo"));
                                if(is_object($objAdmiProDetalle))
                                {
                                    $objCaracSerDetalle = new InfoServicioProdCaract();
                                    $objCaracSerDetalle->setServicioId($objServicio->getId());
                                    $objCaracSerDetalle->setProductoCaracterisiticaId($objAdmiProDetalle->getId());
                                    $objCaracSerDetalle->setValor($arrayResultado['idDetalle']);
                                    $objCaracSerDetalle->setEstado("Activo");
                                    $objCaracSerDetalle->setUsrCreacion($objPeticion->getSession()->get('user'));
                                    $objCaracSerDetalle->setFeCreacion(new \DateTime('now'));
                                    $emComercial->persist($objCaracSerDetalle);
                                    $emComercial->flush();
                                }
                            }
                            //generar recursos red
                            $arrayParametros["objServicio"]                      = $objServicio;
                            $arrayParametros["strProtocoloEnrutamiento"]         = $arrayParametrosDet["valor4"];
                            $arrayParametros["strUsrCreacion"]                   = $objPeticion->getSession()->get('user');
                            $arrayParametros["strIpCreacion"]                    = $objPeticion->getClientIp();
                            $arrayParametros["strCodEmpresa"]                    = $objSession->get('idEmpresa');
                            $arrayParametros["strIdDepartamento"]                = $objSession->get('idDepartamento');
                            $arrayParametros["intIdDetalleSolicitudInstalacion"] = $arrayParametros['intIdFactibilidad'];
                            $arrayParametros["strTipoServicio"]                  = $arrayParametrosDet["valor5"];
                            $arrayResultadoCamara = $serviceInfoServicio->migrarServicioAEstadoAsignada($arrayParametros);
                            if($arrayResultadoCamara['status'] != "OK")
                            {
                                $objServicioHistorial = new InfoServicioHistorial();
                                $objServicioHistorial->setServicioId($objServicio);
                                $objServicioHistorial->setObservacion($arrayResultadoCamara['mensaje']);
                                $objServicioHistorial->setIpCreacion($objPeticion->getClientIp());
                                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                                $objServicioHistorial->setUsrCreacion($objPeticion->getSession()->get('user'));
                                $objServicioHistorial->setEstado($objServicio->getEstado());
                                $emComercial->persist($objServicioHistorial);
                                $emComercial->flush();
                            }
                        }
                    }
                    
                }
                else
                {
                    //CREAR TAREA
                    $objPersona = $emComercial->getRepository('schemaBundle:InfoPersona')->find($arrayListadoPla['empleado_id']);
                    if(!is_object($objPersona))
                    {
                        throw new \Exception("No se encuentra el empleado."); 
                    }
                    $intServicio            = $objPeticion->get('id_servicio');
                    $arrayFechaProgramacion = explode("T", $arrayListadoPla['fecha']);
                    $arrayFecha             = explode("T", $arrayListadoPla['hora_inicio']);
                    $arrayF                 = explode("-", $arrayFechaProgramacion[0]);
                    $arrayHoraF             = explode(":", $arrayFecha[1]);
                    $arrayFechaInicio       = date("Y/m/d H:i", strtotime($arrayF[2] . "-" . $arrayF[1] . "-" . $arrayF[0] . " " . $arrayFecha[1]));
        
                    $objServicio = $emComercial->getRepository("schemaBundle:InfoServicio")->find($intServicio);
                    if(!is_object($objServicio))
                    {
                        throw new \Exception("No se encuentra el Servicio."); 
                    }
                    $objPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                            ->findByIdentificacionTipoRolEmpresa($objPersona->getIdentificacionCliente(), 'Empleado', $objSession->get('idEmpresa'));
                    if(!is_object($objPersonaEmpresaRol))
                    {
                        throw new \Exception("No se encuentra el Rol del empleado."); 
                    }
                    $strPunto = $objServicio->getPuntoId()->getId();
                    $arrayParametrosTarea   = array('intIdPersonaEmpresaRol' => $objPersonaEmpresaRol->getId(),
                                            'intPuntoId'             => $objServicio->getPuntoId()->getId(),
                                            'intIdEmpresa'           => $objSession->get('codEmpresa'),
                                            'strPrefijoEmpresa'      => $objSession->get('prefijoEmpresa'),
                                            'strNombreTarea'         => $arrayListadoPla['nombreTarea'],
                                            'strNombreProceso'       => $arrayListadoPla['nombreProceso'],
                                            'strObservacionTarea'    => $arrayListadoPla['observacion'],
                                            'strMotivoTarea'         => $arrayListadoPla['observacion'],
                                            'strTipoAsignacion'      => 'empleado',
                                            'strIniciarTarea'        => 'S',
                                            'strTipoTarea'           => 'T',
                                            'strTareaRapida'         => 'N',
                                            'strFechaHoraSolicitada' => $arrayFechaInicio,
                                            'boolAsignarTarea'       => true,
                                            'strAplicacion'          => 'TelcoS+',
                                            'strUsuarioAsigna'       => $objPersona->getLogin(),
                                            'strUserCreacion'        => $objServicio->getUsrCreacion(),
                                            'strIpCreacion'          => $objServicio->getIpCreacion());
                    $serviceSoporte = $this->get('soporte.SoporteService');
                    $arrayResultado = $serviceSoporte->crearTareaCasoSoporte($arrayParametrosTarea);
                    if($arrayResultado['mensaje'] === 'fail')
                    {
                        throw new \Exception('Error al crear la tarea, por favor comuníquese con el departamento de Sistemas.');
                    }
                    else
                    {
                        try
                        {
                            $serviceUtil            = $this->get('schema.Util');
                            $emComunicacion->getConnection()->beginTransaction();
                            $emSoporte->getConnection()->beginTransaction();
                            $emNaf->getConnection()->beginTransaction();
                            $intNumeroDetalle = $arrayResultado['numeroDetalle'];
                            $intTareaId = $arrayResultado['numeroTarea'];
                            $objInfoServicioProdCaract = $serviceGeneral->getServicioProductoCaracteristica($objServicio,
                                                                                                            'ID_PROPUESTA',
                                                                                                            $objServicio->getProductoId());
                            $arrayParametrosTarea      = array("strIdPropuesta"           => $objInfoServicioProdCaract->getValor(),
                                                            );
                            $arrayParametrosWSCrm = array("arrayParametrosCRM"     => $arrayParametrosTarea,
                                                            "strOp"                  => 'consultaDocumento',
                                                            "strFuncion"             => 'procesar');
                            $arrayRespuestaWSCrm  = $serviceCrm->getRequestCRM($arrayParametrosWSCrm);
                            if(!empty($arrayRespuestaWSCrm["error"]) && isset($arrayRespuestaWSCrm["error"]))
                            {
                                throw new \Exception('Error al Obtener el documento en TelcoCrm: '.$arrayRespuestaWSCrm["error"]);
                            }
                            $arrayResultados = json_decode(json_encode($arrayRespuestaWSCrm['resultado']), true);
                            //sacamos datos de la tareas
                            $arrayParametrosTarea      = array("strIdPropuesta"           => $objInfoServicioProdCaract->getValor(),
                                                            );
                            $arrayParametrosWSCrm  = array("arrayParametrosCRM"     => $arrayParametrosTarea,
                                                            "strOp"                  => 'consultaTarea',
                                                            "strFuncion"             => 'procesar');
                            $arrayRespuestaWSCrmT  = $serviceCrm->getRequestCRM($arrayParametrosWSCrm);
                            if(!empty($arrayRespuestaWSCrmT["error"]) && isset($arrayRespuestaWSCrmT["error"]))
                            {
                                throw new \Exception('Error al Obtener el documento en TelcoCrm: '.$arrayRespuestaWSCrmT["error"]);
                            }
                            $arrayRegistrosTarea = json_decode(json_encode($arrayRespuestaWSCrmT['resultado']), true);   
                            if ($arrayRegistrosTarea && $arrayRegistrosTarea != 'not_found')
                            {
                                foreach ($arrayRegistrosTarea as $arrayData)
                                {
                                    $strTareaId                                 = $arrayData['idTarea'];
                                    if($strTareaId != null)
                                    {
                                        $objTareaDetalle = $emComunicacion->getRepository('schemaBundle:InfoComunicacion')->find($strTareaId);
                                    }
                                    if(is_object($objTareaDetalle))
                                    {
                                        $arrayParametrosDoc                         = array();
                                        $arrayParametrosDoc["intIdDetalle"]         = $objTareaDetalle->getDetalleId();
                                        $strPathTelcos                              = $this->container->getParameter('path_telcos');
                                        $arrayParametrosDoc["strPathTelcos"]        = $strPathTelcos."telcos/web";

                                        $objJson = $emComunicacion->getRepository('schemaBundle:InfoCaso')
                                                                                ->getJsonDocumentosCaso($arrayParametrosDoc,
                                                                                        $this->emInfraestructura,
                                                                                        $strUsrCreacion
                                                                                    );
                                        $arrayListadoD      = json_decode($objJson, true);
                                        foreach ($arrayListadoD['encontrados'] as $arrayListado)
                                        {
                                            $intCantidad = $intCantidad+1;
                                            $arrayDoc = array(
                                                                    'strNombreDocumento'       => $arrayListado["ubicacionLogica"],
                                                                    'strFechaCreacion'         => ($arrayListado["feCreacion"]),
                                                                    'strPathFile'              => $arrayListado["linkVerDocumento"]);

                                            array_push($arrayResultados,$arrayDoc);
                                        }
                                    }
                                }
                            }
                            foreach($arrayResultados as $arrayResultadoD)
                            {
                                $strNombreDocumento = $arrayResultadoD['strNombreDocumento'];
                                $strFechaDoc      = $arrayResultadoD['strFechaCreacion'];
                                $strPath          = $arrayResultadoD['strPathFile'];

                                if(!empty($strPath))
                                {
                                    $arrayArchivo   = explode('/', $strPath);
                                    $arrayCount     = count($arrayArchivo);
                                    $strNuevoNombre = $arrayArchivo[$arrayCount - 1];
                                    $arrayTipoDoc   = explode('.', $strNuevoNombre);
                                    $arrayCountT    = count($arrayTipoDoc);
                                    $strTipoDoc     = $arrayTipoDoc[$arrayCountT - 1];
                                }
                                $objAdmiTipoDocumento = $emComunicacion->getRepository('schemaBundle:AdmiTipoDocumento')
                                    ->findOneByExtensionTipoDocumento(strtoupper($strTipoDoc));
                                if(!is_object($objAdmiTipoDocumento))
                                {
                                    throw new \Exception("No se encuentra el tipo de documento, favor verificar.");
                                }

                                $objInfoDocumento = new InfoDocumento();
                                $objInfoDocumento->setMensaje("Tarea generada automáticamente por el sistema Telcos");
                                $objInfoDocumento->setNombreDocumento($strNombreDocumento);
                                $objInfoDocumento->setTipoDocumentoId($objAdmiTipoDocumento);
                                $objInfoDocumento->setUbicacionFisicaDocumento($strPath);//url
                                $objInfoDocumento->setUbicacionLogicaDocumento($strNombreDocumento);
                                $objInfoDocumento->setFeCreacion(new \DateTime('now'));
                                $objInfoDocumento->setEstado("Activo");
                                $objInfoDocumento->setUsrCreacion($objServicio->getUsrCreacion());
                                $objInfoDocumento->setIpCreacion($objServicio->getIpCreacion());
                                $objInfoDocumento->setEmpresaCod($objSession->get('codEmpresa'));
                                $emComunicacion->persist($objInfoDocumento);
                                $emComunicacion->flush();
                                
                                //Entidad de la tabla INFO_DOCUMENTO_RELACION donde se relaciona el documento cargado con el IdCaso
                                $objInfoDocumentoRelacion = new InfoDocumentoRelacion();
                                $objInfoDocumentoRelacion->setModulo('SOPORTE');
                                $objInfoDocumentoRelacion->setEstado('Activo');
                                $objInfoDocumentoRelacion->setFeCreacion(new \DateTime('now'));
                                $objInfoDocumentoRelacion->setUsrCreacion($objServicio->getUsrCreacion());
                                $objInfoDocumentoRelacion->setDetalleId($intNumeroDetalle);
                                $objInfoDocumentoRelacion->setDocumentoId($objInfoDocumento->getId());

                                $emComunicacion->persist($objInfoDocumentoRelacion);
                                $emComunicacion->flush();
                                
                                $objCaracteristicaServicio = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                ->findOneBy(array('descripcionCaracteristica' => 'PROYECTO_CRM', 
                                                                                'tipo'                      => 'COMERCIAL')
                                                                        );
                                $objInfoServicioProdCaractP = $serviceGeneral->getServicioProductoCaracteristica($objServicio,
                                                                                                'PROYECTO_CRM',
                                                                                                    $objServicio->getProductoId());

                                if(is_object($objCaracteristicaServicio) && !empty($intTareaId) 
                                    && is_object($objInfoServicioProdCaractP))
                                {
                                    //Agregamos la instancia
                                    $objInfoTareaCaracteristica = new InfoTareaCaracteristica();
                                    $objInfoTareaCaracteristica->setTareaId($intTareaId);
                                    $objInfoTareaCaracteristica->setDetalleId($intNumeroDetalle);
                                    $objInfoTareaCaracteristica->setCaracteristicaId($objCaracteristicaServicio->getId());
                                    $objInfoTareaCaracteristica->setValor($objInfoServicioProdCaractP->getValor());
                                    $objInfoTareaCaracteristica->setEstado('Activo');
                                    $objInfoTareaCaracteristica->setFeCreacion(new \DateTime('now'));
                                    $objInfoTareaCaracteristica->setUsrCreacion($objServicio->getUsrCreacion());
                                    $objInfoTareaCaracteristica->setIpCreacion($objServicio->getIpCreacion());
                                    $emSoporte->persist($objInfoTareaCaracteristica);
                                    $emSoporte->flush();
                                }
                            }
                            //Agregamos seguimiento con el pedido
                            $objInfoServicioProdCaractPedido = $serviceGeneral->getServicioProductoCaracteristica($objServicio,
                                                                                                'PEDIDO_ID',
                                                                                                    $objServicio->getProductoId());
                            $objInfoPersona = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                                                        ->find($objPersona->getId());
                            if(is_object($objInfoServicioProdCaractPedido) && is_object($objInfoPersona))
                            {
                                $intNumPedido      = $objInfoServicioProdCaractPedido->getValor();
                                $strResponsable = $objInfoPersona->getApellidos() .' '.$objInfoPersona->getNombres();

                                $objInfoTareaSeg = $emSoporte->getRepository('schemaBundle:InfoTareaSeguimiento')
                                            ->findOneBy(array("detalleId" => $intNumeroDetalle));
                                if(!is_object($objInfoTareaSeg))
                                {
                                    throw new \Exception("Tarea en Info Comunicación no encontrada, favor verificar.");
                                }
                                $objInfoTareaSeguimiento = new InfoTareaSeguimiento();
                                $objInfoTareaSeguimiento->setDetalleId($objInfoTareaSeg->getDetalleId());
                                $objInfoTareaSeguimiento->setObservacion('Número de pedido '.$intNumPedido.' asignado a .'.$strResponsable);
                                $objInfoTareaSeguimiento->setUsrCreacion('TELCOS+');
                                $objInfoTareaSeguimiento->setFeCreacion(new \DateTime('now'));
                                $objInfoTareaSeguimiento->setEmpresaCod($objSession->get('codEmpresa'));
                                $objInfoTareaSeguimiento->setEstadoTarea('Activo');
                                $objInfoTareaSeguimiento->setInterno($objInfoTareaSeg->getInterno());
                                $objInfoTareaSeguimiento->setDepartamentoId($objInfoTareaSeg->getDepartamentoId());
                                $objInfoTareaSeguimiento->setPersonaEmpresaRolId($objInfoTareaSeg->getPersonaEmpresaRolId());
                                $emSoporte->persist($objInfoTareaSeguimiento);
                                $emSoporte->flush();
                                }
                            //Actualizamos quien retira
                            $objInfoPersona = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                                                        ->find($arrayListadoPla['empleado_id']);
                            if(!is_object($objInfoPersona))
                            {
                                throw new \Exception("No se encuentra el id de la persona, favor verificar.");
                            }
                            $objInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                            ->findByIdentificacionTipoRolEmpresa($objInfoPersona->getIdentificacionCliente(), 'Empleado', 
                            $objSession->get('idEmpresa'));
                            if(!is_object($objInfoPersonaEmpresaRol))
                            {
                                throw new \Exception("No se encuentra el rol de la persona, favor verificar.");
                            }
                            $objDepartamento = $emComercial->getRepository('schemaBundle:AdmiDepartamento')
                                                    ->find($objInfoPersonaEmpresaRol->getDepartamentoId());
                            if(!is_object($objDepartamento))
                            {
                                throw new \Exception("No se encuentra el departamento, favor verificar.");
                            }
                            $arrayParametrosPro = array("intServicio"     => $objServicio->getId(),
                                                        "strDepartNombre" => $objDepartamento->getNombreDepartamento());
                            $arrayResultadoP   = $emNaf->getRepository('schemaBundle:admiProyectos')
                                                    ->getPedidoResponsable($arrayParametrosPro);  
                            if($arrayResultadoP['total'] >= 1)
                            {
                                $arrayRegistros = $arrayResultadoP['registros'];
                                $arrayUpdate = array();
                                foreach($arrayRegistros as $arrayRegistro)
                                {
                                    array_push($arrayUpdate,$arrayRegistro['detalleId']);
                                }
                                $arrayParametrosPro = array("strLogin"     => $objInfoPersona->getLogin());
                                $arrayResultadoPersonaNaf   = $emNaf->getRepository('schemaBundle:admiProyectos')
                                                        ->getPersonaByNaf($arrayParametrosPro);  
                                if($arrayResultadoPersonaNaf['total'] >= 1)
                                {
                                    $arrayRegistrosPersonaNaf = $arrayResultadoPersonaNaf['registros'][0];
                                    $intEmpleado = $arrayRegistrosPersonaNaf['empleadoid'];
                                    $arrayDatosActualizar = array('intUsuarioId'   => $intEmpleado,
                                                                    'strLoginUsu'    => $objInfoPersona->getLogin(),
                                                                    'arrayDetalleId' => $arrayUpdate);
                                    $arrayResultadoA   = $emNaf->getRepository('schemaBundle:admiProyectos')
                                                                    ->getActualizaResponsable($arrayDatosActualizar); 
                                }                                
                            }
                            if ($emNaf->getConnection()->isTransactionActive())
                            {
                                $emNaf->getConnection()->commit();
                            }

                            if ($emSoporte->getConnection()->isTransactionActive())
                            {
                                $emSoporte->getConnection()->commit();
                            }

                            if ($emComunicacion->getConnection()->isTransactionActive())
                            {
                                $emComunicacion->getConnection()->commit();
                            }
                        }catch(\Exception $e)
                        {
                            $serviceUtil->insertError('Telcos', 'programarProyectoAction', $e->getMessage(), $objPeticion->getSession()->get('user'), 
                                $objPeticion->getClientIp());
                            if ($emComunicacion->getConnection()->isTransactionActive())
                            {
                                $emComunicacion->getConnection()->rollback();
                            }
                            if ($emNaf->getConnection()->isTransactionActive())
                            {
                                $emNaf->getConnection()->rollback();
                            }

                            if ($emSoporte->getConnection()->isTransactionActive())
                            {
                                $emSoporte->getConnection()->rollback();
                            }
                        }   
                    }
                }
            }
        }
        
        $objRespuesta->setContent($arrayResultado['mensaje']);
        return $objRespuesta;
    }
    
    /**
     * Documentación para la función 'getTareasByProcesoAndTareaAction'.
     *
     * Función encargada de retornar los procesos con sus respectivos departamentos.
     *
     * @return Responses.
     *
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.0 20-02-2022
     *
     */
    public function getTareasByProcesoAndTareaAction() 
    {
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $serviceCrm         = $this->get('comercial.ComercialCrm');
        $objPeticion       = $this->get('request');
        $intServicioId     = $objPeticion->get('servicioId');
        $intIdSolicitud    = $objPeticion->get('id_solicitud');
        $strNombreTarea    = $objPeticion->get('nombreTarea');
        $strEstado         = $objPeticion->get('estado');
        $intStart          = $objPeticion->get('start');
        $intLimit          = $objPeticion->get('limit');
        $strAccion         = $objPeticion->get('accion');
        $strCodEmpresa     = ($objPeticion->getSession()->get('idEmpresa') ? $objPeticion->getSession()->get('idEmpresa') : "");
        $strPrefijoEmpresa = ($objPeticion->getSession()->get('prefijoEmpresa') ? $objPeticion->getSession()->get('prefijoEmpresa') : "");
        $arrayParametros    = array();     

        $arrayParametros['servicioId'] = $intServicioId;
        $arrayParametros['id_solicitud'] = $intIdSolicitud;
        $arrayParametros['nombreTarea'] = $strNombreTarea;
        $arrayParametros['estado'] = $strEstado;
        $arrayParametros['start'] = $intStart;
        $arrayParametros['limit'] = $intLimit;
        $arrayParametros['accion'] = $strAccion;
        $arrayParametros['idEmpresa'] = $strCodEmpresa;
        $arrayParametros['prefijoEmpresa'] = $strPrefijoEmpresa;

        $objServicePlanificar = $this->get('planificacion.planificar');
        $objJson = $objServicePlanificar->getTareasByProcesoAndTarea($arrayParametros);
        
        $arrayDatos = json_decode($objJson, true);
        $arrayResultados = $arrayDatos['encontrados'];
        
        
        $objServicio = $this->getDoctrine()->getManager("telconet")
                                                                ->getRepository('schemaBundle:InfoServicio')
                                                                ->findOneBy(array('id'=> $intServicioId));
        $objServicioTecnicoService  = $this->get('tecnico.InfoServicioTecnico');
        $objInfoServicioProdCaract = $objServicioTecnicoService->getServicioProductoCaracteristica($objServicio,
                                                                                                'COTIZACION_PRODUCTOS',
                                                                                                 $objServicio->getProductoId());

        $arrayParametros      = array("strCotizacion"           => $objInfoServicioProdCaract->getValor());
        $arrayParametrosWSCrm = array("arrayParametrosCRM"     => $arrayParametros,
                                      "strOp"                  => 'consultaDepartamento',
                                      "strFuncion"             => 'procesar');
        $arrayRespuestaWSCrm  = $serviceCrm->getRequestCRM($arrayParametrosWSCrm);
        if(!empty($arrayRespuestaWSCrm["error"]) && isset($arrayRespuestaWSCrm["error"]))
        {
            throw new \Exception('Error al Obtener el documento en TelcoCrm: '.$arrayRespuestaWSCrm["error"]);
        }
        $arrayResultadosCrm = json_decode(json_encode($arrayRespuestaWSCrm['resultado']), true);
        foreach($arrayResultadosCrm as $arrayResultado)
        {
            $strIdDepartamento    = $arrayResultado['strIdDepartamento'];
            $strDepartamento      = $arrayResultado['strDepartamento'];
            $boolExiste           = false;
            foreach($arrayResultados as $arrayTelcos)
            {
                $strDepartamentoTelcos = $arrayTelcos['nombreTarea'];
                $intPosicion = strpos($strDepartamentoTelcos,$strDepartamento);
                if(!empty($intPosicion))
                {
                    $boolExiste = true;
                }
            }
            if(!$boolExiste)
            {
                $arrayNewDepartamento= array('id_tarea' => 0 ,  'nombre_tarea_ant' => 'Ninguno', 
                                   'nombre_tarea_sig' => 'Ninguno', 'nombreTarea' => $strDepartamento, 
                                   'peso' => '0', 'costo' => '0', 'precio_promedio' => '0', 
                                   'tarea_id' => 0 , 'proceso_nombre' => 'Ninguno', 'estado' => 'Ninguno');
                array_push($arrayResultados,$arrayNewDepartamento);
            }
                
        }
        $arrayDepartamentos = array('total' => count($arrayResultados),
                                    'encontrados' => $arrayResultados);
        $objRespuesta->setContent(json_encode($arrayDepartamentos));
        return $objRespuesta;
    }
    
    /**
     * Documentación para la función 'getDocumentosAction'.
     *
     * Función encargada de retornar los documentos de crm y calculadora.
     *
     * @return Responses.
     *
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.0 20-02-2022
     *
     */
    public function getDocumentosAction()
    {
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $serviceCrm         = $this->get('comercial.ComercialCrm');
        $emComercial        = $this->getDoctrine()->getManager('telconet');
        $serviceUtil        = $this->get('schema.Util');
        $objPeticion        = $this->get('request');
        $intServicioId      = $objPeticion->get('idServicio');
        $arrayEncontrados   = array();
        $intCantidad        = 0;
        $objServicioTecnicoService  = $this->get('tecnico.InfoServicioTecnico');
        try
        {
            $objInfoServicio    = $emComercial->getRepository('schemaBundle:InfoServicio')->find($intServicioId);

            $objInfoServicioProdCaract = $objServicioTecnicoService->getServicioProductoCaracteristica($objInfoServicio,
                                                                                                            'ID_PROPUESTA',
                                                                                                             $objInfoServicio->getProductoId());
            $arrayParametros      = array("strIdPropuesta"           => $objInfoServicioProdCaract->getValor()
                                          );
            $arrayParametrosWSCrm = array("arrayParametrosCRM"     => $arrayParametros,
                                          "strOp"                  => 'consultaDocumento',
                                          "strFuncion"             => 'procesar');
            $arrayRespuestaWSCrm  = $serviceCrm->getRequestCRM($arrayParametrosWSCrm);
            if(!empty($arrayRespuestaWSCrm["error"]) && isset($arrayRespuestaWSCrm["error"]))
            {
                throw new \Exception('Error al Obtener el documento en TelcoCrm: '.$arrayRespuestaWSCrm["error"]);
            }
            $arrayRegistros = json_decode(json_encode($arrayRespuestaWSCrm['resultado']), true);        
            if ($arrayRegistros && $arrayRegistros != 'not_found')
            {
                foreach ($arrayRegistros as $arrayData)
                {
                    $intCantidad = $intCantidad+1;
                    $strNFS     = substr($arrayData["strPathFile"], 0, strrpos($arrayData["strPathFile"], '/'));
                    $strUrlNFS  = $arrayData["strPathFile"];
                    $boolNfs = (filter_var($strNFS, FILTER_VALIDATE_URL) !== false);
                    if($boolNfs)
                    {
                        $arrayData["strPathFile"]   = $strUrlNFS;
                    }
                    $arrayEncontrados[] = array(
                                                   'idDocumento'           => 1,
                                                   'ubicacionLogica'       => $arrayData["strNombreDocumento"],
                                                   'feCreacion'            => ($arrayData["strFechaCreacion"]),
                                                   'linkVerDocumento'      => $arrayData["strPathFile"],
                                                   'boolEliminarDocumento' => false);
                }
            }
            else
            {
                $objResultado = '{"total":"0","encontrados":[]}';
            }
            $arrayParametrosTarea  = array("strPropuesta"           => $objInfoServicioProdCaract->getValor()
                                          );
            $arrayParametrosWSCrm  = array("arrayParametrosCRM"     => $arrayParametrosTarea,
                                          "strOp"                  => 'consultaTarea',
                                          "strFuncion"             => 'procesar');
            $arrayRespuestaWSCrmT  = $serviceCrm->getRequestCRM($arrayParametrosWSCrm);
            if(!empty($arrayRespuestaWSCrmT["error"]) && isset($arrayRespuestaWSCrmT["error"]))
            {
                throw new \Exception('Error al Obtener el documento en TelcoCrm: '.$arrayRespuestaWSCrmT["error"]);
            }
            $arrayRegistrosTarea = json_decode(json_encode($arrayRespuestaWSCrmT['resultado']), true);   
            if ($arrayRegistrosTarea && $arrayRegistrosTarea != 'not_found')
            {
                $emInfraestructura  = $this->getDoctrine()->getManager("telconet_infraestructura");
                $emComunicacion     = $this->getDoctrine()->getManager("telconet_comunicacion");
                foreach ($arrayRegistrosTarea as $arrayData)
                {
                    $strTareaId                                 = $arrayData['idTarea'];
                    if($strTareaId != null)
                    {
                        $objTareaDetalle = $emComunicacion->getRepository('schemaBundle:InfoComunicacion')->find($strTareaId);
                    }
                    if(is_object($objTareaDetalle))
                    {
                        $arrayParametrosDoc                         = array();
                        $arrayParametrosDoc["intIdDetalle"]         = $objTareaDetalle->getDetalleId();
                        $strPathTelcos                              = $this->container->getParameter('path_telcos');
                        $arrayParametrosDoc["strPathTelcos"]        = $strPathTelcos."telcos/web";

                        $objJson = $emComunicacion->getRepository('schemaBundle:InfoCaso')->getJsonDocumentosCaso($arrayParametrosDoc,
                                                                                                            $emInfraestructura,
                                                                                                            $objPeticion->getSession()->get('user')
                                                                                                            );
                        $arrayListadoD      = json_decode($objJson, true);
                        foreach ($arrayListadoD['encontrados'] as $arrayListado)
                        {
                            $intCantidad = $intCantidad+1;
                            $arrayDoc = array(
                                                    'idDocumento'           => $arrayListado["idDocumento"],
                                                    'ubicacionLogica'       => $arrayListado["ubicacionLogica"],
                                                    'feCreacion'            => ($arrayListado["feCreacion"]),
                                                    'linkVerDocumento'      => $arrayListado["linkVerDocumento"],
                                                    'boolEliminarDocumento' => false);
                            
                            array_push($arrayEncontrados,$arrayDoc);
                        }
                    }
                }
            }
            $objData        = json_encode($arrayEncontrados);
            $objResultado   = '{"total":"'.$intCantidad.'","encontrados":'.$objData.'}';

        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError('Telcos', 'getJsonDocumentosAction', $e->getMessage(), $objPeticion->getSession()->get('user'), 
                $objPeticion->getClientIp());
        }
        $objRespuesta->setContent($objResultado);
        return $objRespuesta;
    }
    
    /**
     * Documentación para la función 'getValidaProyectoAction'.
     *
     * Función encargada de verificar que sea un proyecto.
     *
     * @return Responses.
     *
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.0 20-02-2023
     *
     */
    public function getValidaProyectoAction()
    {
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/plain');
        $emComercial        = $this->getDoctrine()->getManager('telconet');
        $serviceUtil        = $this->get('schema.Util');
        $objPeticion        = $this->get('request');
        $intServicioId      = $objPeticion->get('idServicio');
        $intTotal           = 1;
        $objServicioTecnicoService  = $this->get('tecnico.InfoServicioTecnico');
        try
        {
            $objInfoServicio    = $emComercial->getRepository('schemaBundle:InfoServicio')->find($intServicioId);

            $objInfoServicioProdCaract = $objServicioTecnicoService->getServicioProductoCaracteristica($objInfoServicio,
                                                                                                            'ID_PROPUESTA',
                                                                                                             $objInfoServicio->getProductoId());
            $objInfoServicioProdCaractPro = $objServicioTecnicoService->getServicioProductoCaracteristica($objInfoServicio,
                                                                                                            'PROYECTO_CRM',
                                                                                                             $objInfoServicio->getProductoId());
            if(is_object($objInfoServicioProdCaract) && is_object($objInfoServicioProdCaractPro))
            {
                $intTotal = 1;
            }
            $objRespuesta->setContent(json_encode(array('total' => $intTotal)));

        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError('Telcos', 'getValidaProyectoAction', $e->getMessage(), $objPeticion->getSession()->get('user'),
                $objPeticion->getClientIp());
        }
        return $objRespuesta;
    }
}
