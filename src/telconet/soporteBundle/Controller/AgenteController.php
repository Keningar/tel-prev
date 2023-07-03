<?php

namespace telconet\soporteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\schemaBundle\Form\CallActivityType;
use telconet\schemaBundle\Entity\InfoAsignacionSolicitud;
use telconet\schemaBundle\Entity\InfoAsignacionSolicitudReg;
use telconet\schemaBundle\Entity\InfoSeguimientoAsignacion;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;

use telconet\soporteBundle\Service\SoporteService;
use telconet\tecnicoBundle\Service\DataTecnicaService;
use telconet\comercialBundle\Service\InfoPersonaEmpresaRolService;

class AgenteController extends Controller implements TokenAuthenticatedController
{
    /**
     * Actualización: 
     *  - Se añade información necesaria para gestionar la tarea en agente
     * @author Fernando López <filopez@telconet.ec>
     * @version 1.4 02-12-2021
     * 
     * Actualización: 
     *  - Se envia por defecto la vista: Administración.
     *  - Se envian variables necesarias para la usar notificaciones webpush con FCM.
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.4 13-04-2020
     * 
     * Actualización: Se agrega que retorne el id del departamento del usuario en sesión
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.3 23-03-2020
     * 
     * Actualización: Se  elimina el calculo de 6 meses hacia atras
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.2 05-11-2018
     *
     * Actualización: Se envia por parametros al twig un arreglo con
     * los últimos 6 meses
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 12-10-2018
     *
     * Muestra la pantalla index del modulo de asignaciones
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 19-07-2018
     * @since 1.0
     * @return render a index.html.twig
     */
    /**
    * @Secure(roles="ROLE_416-1")
    */
    public function indexAction()
    {

        $objRequest     = $this->get('request');
        $objSession     = $objRequest->getSession();
        $strUsuario     = $objSession->get('user');
        $strCodEmpresa  = $objSession->get('idEmpresa');
        $objEmComercial = $this->getDoctrine()->getManager('telconet');
        $strVista       = "Administrador";
        $strPrefijoEmpresaSession = $objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa') : "";
        $intIdCantonUsrSession  = 0;
        $intIdOficinaSesion             = $objSession->get('idOficina') ? $objSession->get('idOficina') : 0;
        $intIdDepartamentoUsrSession     = $objSession->get('idDepartamento') ? $objSession->get('idDepartamento') : 0;
        if($intIdOficinaSesion)
        {
            $objOficinaSesion           = $objEmComercial->getRepository("schemaBundle:InfoOficinaGrupo")->find($intIdOficinaSesion);
            if(is_object($objOficinaSesion))
            {
                $intIdCantonUsrSession   = $objOficinaSesion->getCantonId();
            }
        }

        $arrayEmpleado  = $objEmComercial->getRepository("schemaBundle:InfoPersona")
                                         ->getPersonaDepartamentoPorUserEmpresa($strUsuario, $strCodEmpresa);
        if (!empty($arrayEmpleado))
        {
            $strNombreDepartamento = $arrayEmpleado['NOMBRE_DEPARTAMENTO'];
            $intIdDepartamento     = $arrayEmpleado['ID_DEPARTAMENTO'];
        }
        $arrayParametros['strNombreParametro'] = "TIPOS DE PROBLEMA PARA MODULO ASIGNACIONES TN";
        $arrayParametros['strModulo']          = "SOPORTE";
        $arrayParametros['strEstado']          = "Activo";
        $arrayAdmiParametroCab                 = $objEmComercial->getRepository('schemaBundle:AdmiParametroCab')
                                                                ->findParametrosCab($arrayParametros);
        $intIdParametroTipoProblema            = 0;
        foreach($arrayAdmiParametroCab['arrayResultado'] as $arrayAdmiParamCab)
        {
            $intIdParametroTipoProblema = $arrayAdmiParamCab['intIdParametro'];
        }
        $strPublicVapidKey    = $this->container->getParameter('public_vapid_key');
        $strFcmApiKey         = $this->container->getParameter('webpush_agente_fcm_api_key');
        $strAuthDomain        = $this->container->getParameter('webpush_agente_fcm_auth_domain');
        $strDatabaseUrl       = $this->container->getParameter('webpush_agente_database_url');
        $strProjectId         = $this->container->getParameter('webpush_agente_fcm_project_id');
        $strStorageBucket     = $this->container->getParameter('webpush_agente_fcm_storage_bucket');
        $strMessagingSenderId = $this->container->getParameter('webpush_agente_fcm_messaging_sender_id');
        $strAppId             = $this->container->getParameter('webpush_agente_fcm_app_id');
        $strMeasurementId     = $this->container->getParameter('webpush_agente_fcm_measurement_id');

        return $this->render('soporteBundle:Agente:index.html.twig', array(
                                                                           'vistaSoporte'               => $strVista,
                                                                           'nombreDepartamento'         => $strNombreDepartamento,
                                                                           'idDepartamento'             => $intIdDepartamento,
                                                                           'idParametroCabTipoProblema' => $intIdParametroTipoProblema,
                                                                           'publicVapidKey'             => $strPublicVapidKey,
                                                                           'fcmApiKey'                  => $strFcmApiKey,
                                                                           'fcmAuthDomain'              => $strAuthDomain,
                                                                           'fcmDatabaseUrl'             => $strDatabaseUrl,
                                                                           'fcmProyectId'               => $strProjectId,
                                                                           'fcmStorageBucket'           => $strStorageBucket,
                                                                           'fcmMessagingSenderId'       => $strMessagingSenderId,
                                                                           'fcmAppId'                   => $strAppId,
                                                                           'fcmMeasurementId'           => $strMeasurementId,
                                                                           'strPrefijoEmpresaSession'      => $strPrefijoEmpresaSession,
                                                                           'intIdCantonUsrSession'         => $intIdCantonUsrSession,
                                                                           'intIdDepartamentoUsrSession'   => $intIdDepartamentoUsrSession
                                                                          ));
    }

    /**
     * Muestra la pantalla para seleccionar el tipo de vista: Agente, Administrador, Jefe o Coordinador
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 19-07-2018
     * @since 1.0
     * @return render a agente.html.twig
     */
    public function seleccionarVistaAction($vista)
    {
        $session        = $this->get('request')->getSession();

        $em_seguridad   = $this->getDoctrine()->getManager("telconet_seguridad");
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("416", "1");
        //$vista          = "";
        $session->set('vistaSoporte', $vista);

        return $this->render('soporteBundle:Agente:seleccionarVista.html.twig', array(
                                                                            'item'         => $entityItemMenu,
                                                                            'vistaSoporte' =>  $session->get('vistaSoporte')
                                                                           ));
    }


    /**
     * Actualización: Se añade parámetros permiteVerNuevosCamposTareas, para validar que se consulte lo nuevo de tarea solo para este rol.
     * @author Fernando López <filopez@telconet.ec>
     * @version 1.9 3-01-2022
     * 
     * Actualización: Se añade funcionalidad para finalizar tarea
     * @author Fernando López <filopez@telconet.ec>
     * @version 1.8 19-11-2021
     * 
     * Actualización: Se añade funcionalidad acciones de gestión de tareas
     * @author Francisco Cueva <facueva@telconet.ec>
     * @version 1.7 14-09-2021
     * 
     * Actualización: Se agrega programación para validar que si esta la asignación en Standby se cambie a EnGestion
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.5 28-05-2020
     * 
     * Actualización: Se recepta parámetro idCanton y si tiene el perfil de ver 
     *                todos los empleados del departamento a nivel nacional
     *                se lo envia a la función que obtiene los datos del grid. 
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.5 13-01-2020
     * 
     * Actualización: Se agrega en el parámetro "$strAsignacionProactiva" para activar bandera y realizar
     *                la consulta de las asignaciones que sean de origen Proactivo.
     * @author Miguel Angulo Sánchez <jmangulos@telconet.ec>
     * @version 1.4 14-06-2019
     * 
     * Actualización: Se agrega en el parámetro "$strFechaFin" para también 
     *                considerar una fecha fin en el rango de búsqueda de 
     *                las asignaciones.
     * @author Miguel Angulo Sánchez <jmangulos@telconet.ec>
     * @version 1.3 20-05-2019
     * 
     * 
     * Actualización: Se agrega en la programación que si tiene el perfil de ver 
     *                todos los empleados del departamento a nivel nacional 
     *                entonces envia el parámetro $intIdcanton => null
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.2 04-02-2019
     * 
     * Actualización: Se consulta las asignaciones por canton
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 15-01-2019
     *
     * El grid para mostrar la información del detalle de asignaciones
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 19-07-2018
     * @since 1.0
     * @return JsonResponse
     */
    public function gridAction()
    {
        $objJsonRespuesta        = new JsonResponse();
        $em                      = $this->getDoctrine()->getManager('telconet_soporte');
        $em_comercial            = $this->getDoctrine()->getManager('telconet');
        $objEmGeneral                = $this->getDoctrine()->getManager('telconet_general');
        $serviceUtil             = $this->get('schema.Util');
        $objRequest              = $this->get('request');
        $objSession              = $objRequest->getSession();
        $strIpCreacion           = $objRequest->getClientIp();
        $strFecha                = $objRequest->get('fecha');
        $strFechaFin             = $objRequest->get('fechaFin');
        $intIdCantonConsulta     = $objRequest->get('idCanton');
        $asignacionConsultaHijas = $objRequest->get('asignacionConsultaHijas');
        $intPadreId              = $objRequest->get('intPadreId');
        $strAsignacionProactiva  = $objRequest->get('asigProactivas');
        $strTabVisible           = $objRequest->get('tabVisible');
        $strEstado               = $objRequest->get('estado');
        $strUsuario              = $objSession->get('user');
        $codEmpresa              = $objSession->get('idEmpresa');
        $intPersonaEmpresaRol    = $objSession->get('idPersonaEmpresaRol');
        $intLimite               = 999999999;
        $intIdDepartamento       = null;
        $intIdCanton             = null;
        $strNombreDepartamento   = "";
        $strUserDbSoporte        = $this->container->getParameter('user_soporte');
        $strPasswordDbSoporte    = $this->container->getParameter('passwd_soporte');
        $strDatabaseDsn          = $this->container->getParameter('database_dsn');
        $objJsonRespuesta->setData(['data'=>array()]);
        $booleanRegistroActivos  = $this->get('security.context')->isGranted('ROLE_197-6779');

        //Rol que permite ver as nuevas acciones en agente para gestionar la tarea
        $booleanPermiteVerNuevosCamposTareas  = $this->get('security.context')->isGranted('ROLE_416-8217');

        $objEmComunicacion       = $this->getDoctrine()->getManager("telconet_comunicacion");
        try
        {
            $arrEmpleado        = $em_comercial->getRepository("schemaBundle:InfoPersona")
                                               ->getPersonaDepartamentoPorUserEmpresa($strUsuario, $codEmpresa);
            if (!empty($arrEmpleado))
            {
                $intIdDepartamento     = $arrEmpleado['ID_DEPARTAMENTO'];
                $strNombreDepartamento = $arrEmpleado['NOMBRE_DEPARTAMENTO'];
                $intIdCanton           = $arrEmpleado['ID_CANTON'];
            }
            //Se consulta si el tiene perfil asignado para ver todos los empleados de todos los cantones del departamento
            if (true === $this->get('security.context')->isGranted('ROLE_416-6077'))
            {
                $intIdCanton = $intIdCantonConsulta;
            }
                        
            $i=0;
            if (!empty($strFecha))
            {
                $arrayParametros["consultaPorDefecto"]      = "N";
            }
            else
            {
                $arrayParametros["consultaPorDefecto"]      = "S";
            }
            
            if (!empty($strAsignacionProactiva))
            {
                $arrayParametros["asignacionProactiva"]      = "S";
            }
            else
            {
                $arrayParametros["asignacionProactiva"]      = "N";
            }
            
            $arrayParametros["start"]                   = 0;
            $arrayParametros["limit"]                   = $intLimite;
            $arrayParametros["codEmpresa"]              = $codEmpresa;
            $arrayParametros["usrAsignado"]             = "";
            $arrayParametros["feCreacion"]              = $strFecha;
            $arrayParametros["feCreacionFin"]           = $strFechaFin;
            $arrayParametros["strCambioTurno"]          = "";
            $arrayParametros["intIdDepartamento"]       = $intIdDepartamento;
            $arrayParametros["intIdCanton"]             = $intIdCanton;
            $arrayParametros["esGroupBy"]               = "N";
            $arrayParametros["esGroupByUsrAsignacion"]  = "N";
            $arrayParametros["esGroupByEstado"]         = "N";
            $arrayParametros["esGroupByTipoAtencion"]   = "N";
            $arrayParametros["esGroupByOrigen"]         = "N";
            $arrayParametros["esOrderByUsrAsignacion"]  = "N";
            $arrayParametros["esOrderByEstado"]         = "S";
            $arrayParametros["esOrderByFeCreacionDesc"] = "S";
            $arrayParametros["esOrderByTipoAtencion"]   = "N";
            $arrayParametros["buscaPendientes"]         = "N";
            $arrayParametros["strUserDbSoporte"]        = $strUserDbSoporte;
            $arrayParametros["strPasswordDbSoporte"]    = $strPasswordDbSoporte;
            $arrayParametros["strDatabaseDsn"]          = $strDatabaseDsn;
            $arrayParametros["container"]               = $this->container;
            $arrayParametros["strDepartamento"]         = $strNombreDepartamento;
            $arrayParametros["strUsrCambioTurno"]       = $strUsuario;
            $arrayParametros["strUsrSession"]       = $strUsuario;
            $arrayParametros['asignacionConsultaHijas'] = $asignacionConsultaHijas;
            $arrayParametros['intPadreId']              = $intPadreId;
            $arrayParametros['strTabVisible']           = $strTabVisible;
            $arrayParametros['strEstado']               = $strEstado;

            $arrayParametros['objEmSoporte']             = $em;
            $arrayParametros['objEmComunicacion']        = $objEmComunicacion;
            $arrayParametros['emComercial']              = $em_comercial;
            $arrayParametros['permiteRegistroActivos']   = $booleanRegistroActivos;
            $arrayParametros['objEmGeneral']             = $objEmGeneral;
            $arrayParametros['idPersonaEmpresaRol']      = $intPersonaEmpresaRol;

            $arrayParametros['permiteVerNuevosCamposTareas'] = $booleanPermiteVerNuevosCamposTareas;

            $objJsonRespuesta = $em->getRepository("schemaBundle:InfoAsignacionSolicitud")->generarJsonDetalleAsignaciones($arrayParametros);
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError( 'Telcos+',
                                       'SoporteBundle.AgenteController.gridAction',
                                       'Error al consultar las asignaciones. '.$e->getMessage(),
                                       $strUsuario,
                                       $strIpCreacion );
        }
        return $objJsonRespuesta;
    }


    /*
     * Actualización: Se realiza el llamado del seguimiento de Asignación.
     * @author Miguel Angulo Sánchez <jmangulos@telconet.ec>
     * @version 1.4 14-06-2019
     */
    public function ajaxGetSeguimientoAction()
    {
       
        $emSoporte          = $this->getDoctrine()->getManager("telconet_soporte");
        $objRequest         = $this->get('request');
        $idAsignacion      =  $objRequest->get('data');
        $objJsonRespuesta   = new JsonResponse();
      
        $objres = $emSoporte->getRepository("schemaBundle:InfoAsignacionSolicitud")->getSeguimientoAsignacion($idAsignacion);
        
        $objJsonRespuesta->setData(['seguimiento'=>$objres]);
        
        return $objJsonRespuesta;
    }
    

    /**
     * Actualización: Se agrega enviar el id del canton para la consulta de agentes por ciudad
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.6 23-03-2020
     * 
     * Actualización: Se agrega que retorne la última fecha de estado de conexión de los agentes
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.5 22-01-2019
     *
     * Actualización: Se agrega el parametro orden para ordenar los agentes
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.4 10-01-2019
     *
     * Actualización: Se consulta en query principal el afectado y el asignado
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.3 04-12-2018
     *
     * Actualización: Se agrega el estado de conexión, extensión de teléfono, nombres y secuencial
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.2 24-10-2018
     *
     * Actualización: Se consulta si tiene perfiles asignados que permitan
     * ver todos los empleados de todos los cantones del departamento
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 03-10-2018
     *
     * Construye la informacion de la cabecera del cuadro de asignaciones
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 19-07-2018
     * @since 1.0
     * @return JsonResponse
     */
    public function cabeceraGraficoAsignacionesAction()
    {
        $objJsonResponse      = new JsonResponse();
        $serviceUtil          = $this->get('schema.Util');
        $objSession           = $this->get('request')->getSession();
        $strIpCreacion        = $this->get('request')->getClientIp();
        $codEmpresa           = $objSession->get('idEmpresa');
        $usrEmpleado          = $objSession->get('user');
        $em_soporte           = $this->getDoctrine()->getManager("telconet_soporte");
        $em_comercial         = $this->getDoctrine()->getManager("telconet");
        $strUserDbSoporte     = $this->container->getParameter('user_soporte');
        $strPasswordDbSoporte = $this->container->getParameter('passwd_soporte');
        $strDatabaseDsn       = $this->container->getParameter('database_dsn');
        $dateFechaHoy         = date("Y/m/d");
        $objRequest           = $this->get('request');
        $strOrden             = $objRequest->get('orden');
        $intIdCantonConsulta  = $objRequest->get('idCanton');
        $verEmpleadosDepNac   = false;
        //Se consulta si el tiene perfil asignado para ver todos los empleados de todos los cantones del departamento
        if (true === $this->get('security.context')->isGranted('ROLE_416-6077'))
        {
            $verEmpleadosDepNac = true;
        }
        try
        {
            $arrEmpleado  = $em_comercial->getRepository("schemaBundle:InfoPersona")->getPersonaDepartamentoPorUserEmpresa($usrEmpleado,$codEmpresa);
            if (!empty($arrEmpleado))
            {
                $intIdDepartamento = $arrEmpleado['ID_DEPARTAMENTO'];
                $intIdCanton       = $arrEmpleado['ID_CANTON'];
            }
            $arrayParametrosEmpleados                         = array();
            $arrayParametrosEmpleados['strUserDbSoporte']     = $strUserDbSoporte;
            $arrayParametrosEmpleados['strPasswordDbSoporte'] = $strPasswordDbSoporte;
            $arrayParametrosEmpleados['strDatabaseDsn']       = $strDatabaseDsn;
            $arrayParametrosEmpleados['strCodEmpresa']        = $codEmpresa;
            $arrayParametrosEmpleados['strIdDepartamento']    = $intIdDepartamento;
            $arrayParametrosEmpleados['strOrden']             = ($strOrden !== null)?$strOrden:"ESTADO_CONEXION";
            if($verEmpleadosDepNac)
            {
                $arrayParametrosEmpleados['strIdCanton'] = $intIdCantonConsulta;
            }
            else
            {
                $arrayParametrosEmpleados['strIdCanton'] = $intIdCanton;
            }
            $arrayParametrosEmpleados['strFeCreacion']        = $dateFechaHoy;
            $cursorEmpleados                                  = $em_soporte->getRepository('schemaBundle:InfoAsignacionSolicitud')
                                                                           ->getEmpleadosConAsignacionesPorDep($arrayParametrosEmpleados);
            if( !empty($cursorEmpleados) )
            {
                $arrayEmpleados   = array();
                $i                = 0;
                while( ($arrayResultadoCursor = oci_fetch_array($cursorEmpleados, OCI_ASSOC + OCI_RETURN_NULLS)) != false )
                {
                    $arrayEmpleados[$i]['numero']         = $i+1;

                    $arrayEmpleados[$i]['cantidad']       = ( isset($arrayResultadoCursor['CANTIDAD']) && !empty($arrayResultadoCursor['CANTIDAD']) )
                                                            ? $arrayResultadoCursor['CANTIDAD'] : '0';
                    $arrayEmpleados[$i]['usrAsignado']    = ( isset($arrayResultadoCursor['LOGIN']) && !empty($arrayResultadoCursor['LOGIN']) )
                                                          ? strtolower($arrayResultadoCursor['LOGIN']) : '';
                    $arrayEmpleados[$i]['nombres']        = ( isset($arrayResultadoCursor['NOMBRES']) && !empty($arrayResultadoCursor['NOMBRES']) )
                                                            ? $arrayResultadoCursor['NOMBRES'] : '';
                    $arrayEmpleados[$i]['apellidos']      = (isset($arrayResultadoCursor['APELLIDOS']) && !empty($arrayResultadoCursor['APELLIDOS']))
                                                            ? $arrayResultadoCursor['APELLIDOS'] : '';

                    $arrayEmpleados[$i]['idPersonaRol']   = '';
                    $arrayEmpleados[$i]['estadoConexion'] = '';
                    $arrayEmpleados[$i]['extension']      = '';
                    $arrayEmpleados[$i]['acciones']       = '';

                    //Si tiene idPersonaRol entonces se puede proceder a consultar la característica
                    if( isset($arrayResultadoCursor['ID_PERSONA_ROL']) && !empty($arrayResultadoCursor['ID_PERSONA_ROL']) )
                    {
                        $strFechaConexionFormat = "d-m-a h:m";
                        if(isset($arrayResultadoCursor['FE_ESTADO_CONEXION']) && !empty($arrayResultadoCursor['FE_ESTADO_CONEXION']))
                        {
                            $strFechaHoy            = date("d-m");
                            $objFechaConexion       = date_create(str_replace(".000000000", "", $arrayResultadoCursor['FE_ESTADO_CONEXION']));
                            $strFechaConexionFormat = date_format($objFechaConexion, 'd-m-y H:i');
                            $arrayFechaConexion     = explode(" ",$strFechaConexionFormat);
                            $strFechaConexionFormat = ($strFechaHoy === $arrayFechaConexion[0])?
                                                      "hoy ".$arrayFechaConexion[1]:$arrayFechaConexion[0]." ".$arrayFechaConexion[1];
                        }
                        $arrayEmpleados[$i]['idPersonaRol']     = $arrayResultadoCursor['ID_PERSONA_ROL'];
                        $arrayEmpleados[$i]['estadoConexion']   = $arrayResultadoCursor['ESTADO_CONEXION'];
                        $arrayEmpleados[$i]['extension']        = $arrayResultadoCursor['EXTENSION'];
                        $arrayEmpleados[$i]['feEstadoConexion'] = $strFechaConexionFormat;
                    }
                    $i++;
                }
            }
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError( 'Telcos+',
                                       'SoporteBundle.AgenteController.gridAction',
                                       'Error al consultar las asignaciones. '.$e->getMessage(),
                                       $usrEmpleado,
                                       $strIpCreacion );
        }
        $objJsonResponse->setData(['data'=>$arrayEmpleados]);
        return $objJsonResponse;
    }

    /**
     * Actualización: Se agrega programación para obtener el campo infoTareas el cual contiene las tareas de un caso.
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.2 21-02-2019
     * 
     * Actualización: Se agrega campo dato adicional
     * Tambien se quita el strlower de nombre reporta, nombre sitio y detalle
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 28-11-2018
     *
     * Obtiene información de una asignación por id
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 19-07-2018
     * @since 1.0
     * @return JsonResponse
     */
    public function obtieneInformacionAsignacionAction()
    {
        $objJsonResponse      = new JsonResponse();
        $serviceUtil          = $this->get('schema.Util');
        $objSession           = $this->get('request')->getSession();
        $strIpCreacion        = $this->get('request')->getClientIp();
        $objRequest           = $this->getRequest();
        $intId                = $objRequest->get('intId');
        $usrEmpleado          = $objSession->get('user');
        $intIdDepartamento    = $objSession->get('idDepartamento');
        $em_soporte           = $this->getDoctrine()->getManager("telconet_soporte");
        $em_comercial         = $this->getDoctrine()->getManager("telconet");
        $strUserDbSoporte     = $this->container->getParameter('user_soporte');
        $strPasswordDbSoporte = $this->container->getParameter('passwd_soporte');
        $strDatabaseDsn       = $this->container->getParameter('database_dsn');
        try
        {
            //consulta nombre de departamento en sesion
            $objDepartamento = $em_comercial->getRepository("schemaBundle:AdmiDepartamento")->findOneById($intIdDepartamento);

            $arrayParametros                         = array();
            $arrayParametros['strUserDbSoporte']     = $strUserDbSoporte;
            $arrayParametros['strPasswordDbSoporte'] = $strPasswordDbSoporte;
            $arrayParametros['strDatabaseDsn']       = $strDatabaseDsn;
            $arrayParametros['intIdAsignacion']      = $intId;
            $cursorAsignacion                        = $em_soporte->getRepository('schemaBundle:InfoAsignacionSolicitud')
                                                                  ->getAsignacionPorId($arrayParametros);

            if( !empty($cursorAsignacion) )
            {
                $arrayResultado   = array();
                $i                = 0;
                while( ($arrayResultadoCursor = oci_fetch_array($cursorAsignacion, OCI_ASSOC + OCI_RETURN_NULLS)) != false )
                {
                    $arrayResultado[$i]['idAsignacion']         = ( isset($arrayResultadoCursor['ID_ASIGNACION_SOLICITUD'])
                                                                  && !empty($arrayResultadoCursor['ID_ASIGNACION_SOLICITUD']) )
                                                                  ? $arrayResultadoCursor['ID_ASIGNACION_SOLICITUD'] : 0;
                    $arrayResultado[$i]['usrAsignado']          = ( isset($arrayResultadoCursor['USR_ASIGNADO'])
                                                                  && !empty($arrayResultadoCursor['USR_ASIGNADO']) )
                                                                  ? strtolower($arrayResultadoCursor['USR_ASIGNADO']) : '';
                    $arrayResultado[$i]['tipoAtencion']         = ( isset($arrayResultadoCursor['TIPO_ATENCION'])
                                                                  && !empty($arrayResultadoCursor['TIPO_ATENCION']) )
                                                                  ? $arrayResultadoCursor['TIPO_ATENCION'] : '';
                    $arrayResultado[$i]['tipoProblema']         = ( isset($arrayResultadoCursor['TIPO_PROBLEMA'])
                                                                  && !empty($arrayResultadoCursor['TIPO_PROBLEMA']) )
                                                                  ? strtolower($arrayResultadoCursor['TIPO_PROBLEMA']) : '';
                    $arrayResultado[$i]['referenciaCliente']    = ( isset($arrayResultadoCursor['REFERENCIA_CLIENTE'])
                                                                  && !empty($arrayResultadoCursor['REFERENCIA_CLIENTE']) )
                                                                  ? strtolower($arrayResultadoCursor['REFERENCIA_CLIENTE']) : '';
                    $arrayResultado[$i]['feAsignacion']         = ( isset($arrayResultadoCursor['FE_CREACION'])
                                                                  && !empty($arrayResultadoCursor['FE_CREACION']) )
                                                                  ? $arrayResultadoCursor['FE_CREACION'] : '';
                    $arrayResultado[$i]['criticidad']           = ( isset($arrayResultadoCursor['CRITICIDAD'])
                                                                  && !empty($arrayResultadoCursor['CRITICIDAD']) )
                                                                  ? $arrayResultadoCursor['CRITICIDAD'] : '';
                    $arrayResultado[$i]['nombreReporta']        = ( isset($arrayResultadoCursor['NOMBRE_REPORTA'])
                                                                  && !empty($arrayResultadoCursor['NOMBRE_REPORTA']) )
                                                                  ? $arrayResultadoCursor['NOMBRE_REPORTA'] : '';
                    $arrayResultado[$i]['nombreSitio']          = ( isset($arrayResultadoCursor['NOMBRE_SITIO'])
                                                                  && !empty($arrayResultadoCursor['NOMBRE_SITIO']) )
                                                                  ? $arrayResultadoCursor['NOMBRE_SITIO'] : '';
                    $arrayResultado[$i]['origen']               = ( isset($arrayResultadoCursor['ORIGEN'])
                                                                  && !empty($arrayResultadoCursor['ORIGEN']) )
                                                                  ? strtolower($arrayResultadoCursor['ORIGEN']) : '';
                    $arrayResultado[$i]['detalle']              = ( isset($arrayResultadoCursor['DETALLE'])
                                                                  && !empty($arrayResultadoCursor['DETALLE']) )
                                                                  ? $arrayResultadoCursor['DETALLE'] : '';
                    $arrayResultado[$i]['estado']               = ( isset($arrayResultadoCursor['ESTADO'])
                                                                  && !empty($arrayResultadoCursor['ESTADO']) )
                                                                  ? $arrayResultadoCursor['ESTADO'] : '';
                    $arrayResultado[$i]['numero']               = ( isset($arrayResultadoCursor['NUMERO'])
                                                                  && !empty($arrayResultadoCursor['NUMERO']) )
                                                                  ? strtolower($arrayResultadoCursor['NUMERO']) : '';
                    $arrayResultado[$i]['estadoTarea']          = ( isset($arrayResultadoCursor['ESTADO_TAREA'])
                                                                  && !empty($arrayResultadoCursor['ESTADO_TAREA']) )
                                                                  ? $arrayResultadoCursor['ESTADO_TAREA'] : '';
                    $arrayResultado[$i]['infoTareas']           = ( isset($arrayResultadoCursor['INFO_TAREAS'])
                                                                  && !empty($arrayResultadoCursor['INFO_TAREAS']) )
                                                                  ? $arrayResultadoCursor['INFO_TAREAS'] : '';
                    $arrayResultado[$i]['estadoCaso']           = ( isset($arrayResultadoCursor['ESTADO_CASO'])
                                                                  && !empty($arrayResultadoCursor['ESTADO_CASO']) )
                                                                  ? $arrayResultadoCursor['ESTADO_CASO'] : '';
                    $arrayResultado[$i]['referenciaId']         = ( isset($arrayResultadoCursor['REFERENCIA_ID'])
                                                                  && !empty($arrayResultadoCursor['REFERENCIA_ID']) )
                                                                  ? strtolower($arrayResultadoCursor['REFERENCIA_ID']) : 0;
                    $arrayResultado[$i]['numeroTarea']          = ( isset($arrayResultadoCursor['NUMERO_TAREA'])
                                                                  && !empty($arrayResultadoCursor['NUMERO_TAREA']) )
                                                                  ? $arrayResultadoCursor['NUMERO_TAREA'] : '';
                    $arrayResultado[$i]['datoAdicional']        = ( isset($arrayResultadoCursor['DATO_ADICIONAL'])
                                                                  && !empty($arrayResultadoCursor['DATO_ADICIONAL']) )
                                                                  ? $arrayResultadoCursor['DATO_ADICIONAL'] : '';

                    $arrayResultado[$i]['departamentoAsignado'] = "";
                    $arrayResultado[$i]['feApertura']           = "";
                    $arrayResultado[$i]['feCierre']             = "";
                    $arrayResultado[$i]['minutos']              = "0";
                    $arrayResultado[$i]['minutosCierre']        = "0";
                    //Verificamos si el caso cerrado y esta en el departamento en sesion, de ser asi se lo pone como Abierto
                    //Ultimo asignado de la primera tarea del caso
                    if ( isset($arrayResultado[$i]['numeroTarea']) && $arrayResultado[$i]['numeroTarea']!="")
                    {
                        $parametrosTarea["actividad"]         = $arrayResultado[$i]['numeroTarea'];
                        $parametrosTarea["strOpcionBusqueda"] = "N";

                        $rrDataTareaResultado = $em_soporte->getRepository('schemaBundle:InfoDetalle')
                                                          ->getRegistrosMisTareas($parametrosTarea, 0, 99999 , "");
                        $arrDataTarea         =  $rrDataTareaResultado['resultados'];

                        if (!empty($arrDataTarea) && count($arrDataTarea)>0)
                        {
                            $departamentoAsignado = (isset($arrDataTarea[0]['asignadoNombre']) ? $arrDataTarea[0]['asignadoNombre']  : "");

                        }
                    }

                    if ($arrayResultado[$i]['estadoCaso']!== 'Cerrado' && $arrayResultado[$i]['estadoCaso']!== '')
                    {
                            $arrayResultado[$i]['estadoCaso'] = strtoupper($departamentoAsignado) ===
                                                                       (!empty($objDepartamento) ?
                                                                        strtoupper($objDepartamento->getNombreDepartamento()) : "")
                                                                ? "Abierto" : "Escalado";
                    }

                    $i++;
                }
            }
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError( 'Telcos+',
                                       'SoporteBundle.AgenteController.gridAction',
                                       'Error al consultar las asignaciones. '.$e->getMessage(),
                                       $usrEmpleado,
                                       $strIpCreacion );
            error_log($e->getMessage());
        }
        $objJsonResponse->setData(['data'=>$arrayResultado]);
        return $objJsonResponse;
    }

    /**
     *
     * Actualización: Se usa nueva función obtenerDataTecnicaServicio para obtener data técnica
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.4 10-01-2019
     *
     * Actualización: Se agrega vrf, vlan, capacidad 1 y capacidad 2
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.3 29-11-2018
     *
     * Actualización: Para los minutos de tiempo de cierre si no tiene fecha finalización lo calcula con la fecha actual
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.2 05-11-2018
     *
     * Actualización: Calcula tiempo en minutos si la fecha inicio es menor a la fecha fin
     * ver todos los empleados de todos los cantones del departamento
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 03-10-2018
     *
     * Obtiene información tecnica deun caso de una asignación por id_caso
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 28-08-2018
     * @since 1.0
     * @return JsonResponse
     */
    public function obtieneInfoTecnicaCasoAction()
    {
        $objJsonResponse      = new JsonResponse();
        $serviceUtil          = $this->get('schema.Util');
        $objSession           = $this->get('request')->getSession();
        $strIpCreacion        = $this->get('request')->getClientIp();
        $objRequest           = $this->getRequest();
        $intIdCaso            = $objRequest->get('intId');
        $strNumeroTarea       = $objRequest->get('strNumeroTarea');
        $strFeAsignacion      = $objRequest->get('feAsignacion');
        $usrEmpleado          = $objSession->get('user');
        $strEmpresaCod        = $objSession->get('idEmpresa');
        $strPrefijo           = $objSession->get('prefijoEmpresa');
        $em_soporte           = $this->getDoctrine()->getManager("telconet_soporte");
        $soporteService       = $this->get('soporte.SoporteService');
        try
        {
            $arrayResultadoTec   = array();
            $arrayResultado      = array();

            if(!empty($intIdCaso))
            {
                //consulta nombre de departamento en sesion
                $objJsonAfectadosCaso = $em_soporte->getRepository('schemaBundle:InfoCaso')->generarJsonAfectadosTotalXCaso($intIdCaso,0,999999);
                $objRespAfectadosCaso = json_decode($objJsonAfectadosCaso);
                $arrAfectadosCaso     = $objRespAfectadosCaso->encontrados;

                $arrayResultado[0]['departamentoAsignado'] = "N/A";
                $arrayResultado[0]['feApertura']           = "N/A";
                $arrayResultado[0]['feCierre']             = "N/A";
                $arrayResultado[0]['minutos']              = "0";
                $arrayResultado[0]['minutosCierre']        = "0";


                if (!empty($strNumeroTarea))
                {
                    $parametrosTarea["actividad"]         = $strNumeroTarea;
                    $parametrosTarea["strOpcionBusqueda"] = "N";

                    $rrDataTareaResultado = $em_soporte->getRepository('schemaBundle:InfoDetalle')
                                                      ->getRegistrosMisTareas($parametrosTarea, 0, 99999 , "");
                    $arrDataTarea         =  $rrDataTareaResultado['resultados'];

                    if (!empty($arrDataTarea) && count($arrDataTarea)>0)
                    {
                        $departamentoAsignado = (isset($arrDataTarea[0]['asignadoNombre']) ? $arrDataTarea[0]['asignadoNombre']  : "");
                        $arrayResultado[0]['departamentoAsignado'] = $departamentoAsignado;
                    }
                }


                //Obtenemos fecha de apertura del caso
                $entityInfoCaso                   = $em_soporte->getRepository("schemaBundle:InfoCaso")->findOneById($intIdCaso);
                $arrayResultado[0]['feApertura']  = strval(date_format($entityInfoCaso->getFeApertura(), "Y/m/d G:i"));
                $arrayResultado[0]['feCierre']    = strval(date_format($entityInfoCaso->getFeCierre(), "Y/m/d G:i"));
                //obtiene la diferencia de fechas
                $objFechaAsignacion = new \DateTime($strFeAsignacion);
                $objFechaApertura   = new \DateTime($arrayResultado[0]['feApertura']);
                $arrayResultado[0]['minutos']  = $soporteService->obtenerDiferenciaFechas($objFechaApertura, $objFechaAsignacion);
                //Calcula tiempo en minutos si la fecha inicio es menor a la fecha fin
                $fechaInicioTiempoRespuesta    = strtotime(date_format($objFechaAsignacion, 'Y-m-d'));
                $fechaFinTiempoRespuesta       = strtotime(date_format($objFechaApertura, 'Y-m-d'));
                $difSegundosTiempoRespuesta    = $fechaFinTiempoRespuesta - $fechaInicioTiempoRespuesta;
                $difDiasTiempoRespuesta        = (($fechaFinTiempoRespuesta - $fechaInicioTiempoRespuesta) /60 /60 /24);
                if (($difDiasTiempoRespuesta < 0) && ($difSegundosTiempoRespuesta < 0))
                {
                    $arrayResultado[0]['minutos'] = (($difSegundosTiempoRespuesta / 60) + $arrayResultado[0]['minutos']);
                }
                if(!empty($arrayResultado[0]['feCierre']))
                {
                    $obj_FechaCierre    = new \DateTime($arrayResultado[0]['feCierre']);
                    $arrayResultado[0]['minutosCierre'] = $soporteService->obtenerDiferenciaFechas($obj_FechaCierre, $objFechaApertura);
                }
                else
                {
                    $obj_FechaCierre                     = new \DateTime('NOW');
                    $arrayResultado[0]['minutosCierre'] = $soporteService->obtenerDiferenciaFechas($obj_FechaCierre, $objFechaApertura);
                    $arrayResultado[0]['feCierre']       = "N/A";
                }


                //Verifica información de afectados del caso
                $iRes = 0;
                for ($i = 0; $i<count($arrAfectadosCaso);$i++)
                {
                    $objAfectado = $arrAfectadosCaso[$i];
                    if ($objAfectado->tipo_afectado === "Servicio")
                    {
                        $arrayParametros['strEmpresaCod']   = $strEmpresaCod;
                        $arrayParametros['strPrefijo']      = $strPrefijo;
                        $arrayParametros['intIdAfectado']   = $objAfectado->id_afectado;
                        $arrayResultadoTec[$iRes]  = $this->obtenerDataTecnicaServicio($arrayParametros);
                        $iRes++;
                    }
                }
            }
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError( 'Telcos+',
                                       'SoporteBundle.AgenteController.gridAction',
                                       'Error al consultar las asignaciones. '.$e->getMessage(),
                                       $usrEmpleado,
                                       $strIpCreacion );
            error_log($e->getMessage());
        }
        $objJsonResponse->setData(['dataTecnica'=>$arrayResultadoTec,'data'=>$arrayResultado]);
        return $objJsonResponse;
    }
    /**
     * Obtiene información técnica de la tarea
     *
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 10-01-2019
     * @since 1.0
     * @return JsonResponse
     */
    public function obtieneInfoTecnicaTareaAction()
    {
        $objJsonResponse = new JsonResponse();
        $objServiceUtil  = $this->get('schema.Util');
        $objSession      = $this->get('request')->getSession();
        $strIpCreacion   = $this->get('request')->getClientIp();
        $objRequest      = $this->getRequest();
        $strNumeroTarea  = $objRequest->get('strNumeroTarea');
        $strUsrEmpleado  = $objSession->get('user');
        $strEmpresaCod   = $objSession->get('idEmpresa');
        $strPrefijo      = $objSession->get('prefijoEmpresa');
        $objEmSoporte    = $this->getDoctrine()->getManager("telconet_soporte");
        $objEmComercial  = $this->getDoctrine()->getManager("telconet");
        try
        {
            $arrayResultadoTec   = array();

            if(!empty($strNumeroTarea))
            {
                $objInfoComunicacion = $objEmSoporte->getRepository('schemaBundle:InfoComunicacion')->findOneById($strNumeroTarea);
                //consulta nombre de departamento en sesion
                $arrayAfectadosTarea = $objEmSoporte->getRepository('schemaBundle:InfoParteAfectada')
                                                   ->findBy(
                                                               array(
                                                                     'detalleId'=>$objInfoComunicacion->getDetalleId()
                                                                    )
                                                              );

                $intIRes = 0;
                for ($intI = 0; $intI<count($arrayAfectadosTarea);$intI++)
                {

                    $objAfectado    = $arrayAfectadosTarea[$intI];
                    $arrayServicios = $objEmComercial->getRepository('schemaBundle:InfoServicio')
                                                     ->findBy(
                                                              array(
                                                                    'puntoId' => $objAfectado->getAfectadoId(),
                                                                    'estado'  => 'Activo'
                                                                   )
                                                             );

                    foreach ($arrayServicios as $objServicio)
                    {
                        if($intIRes > 5)
                        {
                            break;
                        }
                        if ($strPrefijo ==='TN' && is_object($objServicio->getProductoId())
                                && $objServicio->getProductoId()->getRequiereInfoTecnica() === 'SI')
                        {
                            $arrayParametros['strEmpresaCod']   = $strEmpresaCod;
                            $arrayParametros['strPrefijo']      = $strPrefijo;
                            $arrayParametros['intIdAfectado']   = $objServicio->getId();
                            $arrayResultadoTec[$intIRes]        = $this->obtenerDataTecnicaServicio($arrayParametros);
                            $intIRes++;
                        }
                    }
                }
            }
        }
        catch(\Exception $objE)
        {
            $objServiceUtil->insertError( 'Telcos+',
                                          'SoporteBundle.AgenteController.gridAction',
                                          'Error al consultar las asignaciones. '.$objE->getMessage(),
                                          $strUsrEmpleado,
                                          $strIpCreacion
                                        );
            error_log($objE->getMessage());
        }
        $objJsonResponse->setData(['dataTecnica'=>$arrayResultadoTec]);
        return $objJsonResponse;
    }

    /**
     * Obtiene información técnica según el id del servicio recibido por parametro
     * @param array $arrayParametros
     * [
     *      intIdAfectado => Id del servicio
     *      strEmpresaCod => Código de la empresa
     *      strPrefijo    => Prefijo de la empresa
     * ]
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 10-01-2019
     * @since 1.0
     * @return JsonResponse
     */
    public function obtenerDataTecnicaServicio($arrayParametros)
    {
        $intIdAfectado     = $arrayParametros['intIdAfectado'];
        $strEmpresaCod     = $arrayParametros['strEmpresaCod'];
        $strPrefijo        = $arrayParametros['strPrefijo'];
        $arrayResultadoTec = array();
        //Verifica información de afectados del caso
        $tecnicoService = $this->get('tecnico.DataTecnica');
        $arrayResultadoTec['idServicio']        = $intIdAfectado;
        $arrayResultadoTec['servicio']          = "N/A";
        $arrayResultadoTec['producto']          = "N/A";
        $arrayResultadoTec['switch']            = "N/A";
        $arrayResultadoTec['login']             = "N/A";
        $arrayResultadoTec['interfaceElemento'] = "N/A";
        $arrayResultadoTec['cpeCliente']        = "N/A";
        $arrayResultadoTec['macCpe']            = "N/A";
        $arrayResultadoTec['ipCpe']             = "N/A";
        $arrayResultadoTec['pe']                = "N/A";
        $arrayResultadoTec['vrf']               = "N/A";
        $arrayResultadoTec['capacidad1']        = "N/A";
        $arrayResultadoTec['capacidad2']        = "N/A";
        $arrayResultadoTec['vlan']              = "N/A";
        //
        $arrayPeticiones['idEmpresa']      = $strEmpresaCod;
        $arrayPeticiones['prefijoEmpresa'] = $strPrefijo;
        $arrayPeticiones['idServicio']     = $intIdAfectado;
        $respuesta                         = $tecnicoService->getDataTecnica($arrayPeticiones);

        $elemento          = $respuesta['elemento'];
        $interfaceElemento = $respuesta['interfaceElemento'];
        $servicio          = $respuesta['servicio'];
        $macCpe            = $respuesta['macCpe'];
        $ipCpe             = $respuesta['IpCpe'];
        $elementoRouter    = $respuesta['elementoRouter'];
        $strVrf            = $respuesta['vrf'];
        $strCapacidad1     = $respuesta['capacidad1'];
        $strCapacidad2     = $respuesta['capacidad2'];
        $strVlan           = $respuesta['vlan'];
        $objProducto       = $respuesta['producto'];
        is_object($elemento) ? $arrayResultadoTec['switch'] = $elemento->getNombreElemento() : "N/A";

        is_object($servicio) ? $arrayResultadoTec['login'] = $servicio->getPuntoId()->getLogin() : "N/A";

        is_object($interfaceElemento)
        ? $arrayResultadoTec['interfaceElemento'] = $interfaceElemento->getNombreInterfaceElemento() : "N/A";

        !empty($elementoRouter) ? $arrayResultadoTec['pe'] = $elementoRouter : "N/A";

        $arrayResultadoTec['macCpe'] = $macCpe;

        is_object($ipCpe) && !empty($ipCpe) ? $arrayResultadoTec['ipCpe'] = $ipCpe->getIp() : "N/A";

        !empty($strVrf) ? $arrayResultadoTec['vrf'] = $strVrf : "N/A";

        !empty($strCapacidad1) ? $arrayResultadoTec['capacidad1'] = $strCapacidad1 : "N/A";

        !empty($strCapacidad2) ? $arrayResultadoTec['capacidad2'] = $strCapacidad2 : "N/A";

        !empty($strVlan) ? $arrayResultadoTec['vlan'] = $strVlan : "N/A";

        is_object($objProducto) ? $arrayResultadoTec['producto'] = $objProducto->getDescripcionProducto() : "N/A";

        return $arrayResultadoTec;
    }


    /**
     * Actualización: Para los minutos de tiempo de cierre si no tiene fecha finalización lo calcula con la fecha actual
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.2 05-11-2018
     *
     * Actualización: Calcula tiempo en minutos si la fecha inicio es menor a la fecha fin
     * ver todos los empleados de todos los cantones del departamento
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 03-10-2018
     *
     * Obtiene información de una tarea de una asignación por id_tarea
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 28-08-2018
     * @since 1.0
     * @return JsonResponse
     */
    public function obtieneInfoAdicionalTareaAction()
    {
        $objJsonResponse      = new JsonResponse();
        $serviceUtil          = $this->get('schema.Util');
        $objSession           = $this->get('request')->getSession();
        $strIpCreacion        = $this->get('request')->getClientIp();
        $objRequest           = $this->getRequest();
        $intIdTarea           = $objRequest->get('intId');
        $strFeAsignacion      = $objRequest->get('feAsignacion');
        $usrEmpleado          = $objSession->get('user');
        $em_soporte           = $this->getDoctrine()->getManager("telconet_soporte");
        $soporteService       = $this->get('soporte.SoporteService');
        try
        {
            if(!empty($intIdTarea))
            {
                $arrayResultado[$i]['minutos']              = '0';
                $arrayResultado[$i]['minutosCierre']        = '0';
                $arrayResultado[$i]['fechaUltMod']          = 'N/A';
                $arrayResultado[$i]['fechaFinalizada']      = 'N/A';
                $arrayResultado[$i]['departamentoAsignado'] = 'N/A';
                $arrayResultado[$i]['empleadoAsignado']     = 'N/A';
                $arrayResultado[$i]['fechaSolicitada']      = 'N/A';
                $arrayResultado[$i]['fechaCreada']          = 'N/A';

                $parametros["tarea"]             = "";
                $parametros["estado"]            = "Todos";
                $parametros["tipo"]              = "";
                $parametros["cliente"]           = "";
                $parametros["actividad"]         = $intIdTarea;
                $parametros["caso"]              = "";
                $parametros["strOrigen"]         = "";
                $parametros["asignado"]          = "";
                $parametros['intProceso']        = "";
                $parametros["strOpcionBusqueda"] = "N";

                $rrDataTareaResultado = $em_soporte->getRepository('schemaBundle:InfoDetalle')->getRegistrosMisTareas($parametros, 0, 99999 , "");
                $arrDataTarea         =  $rrDataTareaResultado['resultados'];
                //obtiene la diferencia de fechas
                $objFechaAsignacion            = new \DateTime($strFeAsignacion);
                $objFechaApertura              = $arrDataTarea[0]['feTareaCreada'];
                $arrayResultado[$i]['minutos'] = $soporteService->obtenerDiferenciaFechas($objFechaApertura, $objFechaAsignacion);
                //Calcula tiempo en minutos si la fecha inicio es menor a la fecha fin
                $fechaInicioTiempoRespuesta    = strtotime(date_format($objFechaAsignacion, 'Y-m-d'));
                $fechaFinTiempoRespuesta       = strtotime(date_format($objFechaApertura, 'Y-m-d'));
                $difSegundosTiempoRespuesta    = $fechaFinTiempoRespuesta - $fechaInicioTiempoRespuesta;
                $difDiasTiempoRespuesta        = (($fechaFinTiempoRespuesta - $fechaInicioTiempoRespuesta) /60 /60 /24);
                if (($difDiasTiempoRespuesta < 0) && ($difSegundosTiempoRespuesta < 0))
                {
                    $arrayResultado[$i]['minutos'] = (($difSegundosTiempoRespuesta / 60) + $arrayResultado[$i]['minutos']);
                }

                if(!empty($arrDataTarea[0]['feTareaHistorial']) && $arrDataTarea[0]['estado']==='Finalizada')
                {
                    $obj_FechaCierre                        = $arrDataTarea[0]['feTareaHistorial'];
                    $arrayResultado[$i]['minutosCierre']    = $soporteService->obtenerDiferenciaFechas($obj_FechaCierre, $objFechaApertura);
                    $arrayResultado[$i]['fechaFinalizada']  = strval(date_format($arrDataTarea[0]['feTareaHistorial'] , "Y-m-d H:i"));
                }
                else
                {
                    $obj_FechaCierre                        = new \DateTime('NOW');
                    $arrayResultado[$i]['minutosCierre']    = $soporteService->obtenerDiferenciaFechas($obj_FechaCierre, $objFechaApertura);
                    $arrayResultado[$i]['fechaUltMod']      = strval(date_format($arrDataTarea[0]['feTareaHistorial'] , "Y-m-d H:i"));
                }

                $arrayResultado[$i]['departamentoAsignado'] = $arrDataTarea[0]['asignadoNombre'];
                $arrayResultado[$i]['empleadoAsignado']     = $arrDataTarea[0]['refAsignadoNombre'];
                $arrayResultado[$i]['fechaSolicitada']      = strval(date_format($arrDataTarea[0]['feSolicitada'] , "Y-m-d H:i"));
                $arrayResultado[$i]['fechaCreada']          = strval(date_format($arrDataTarea[0]['feTareaCreada'] , "Y-m-d H:i"));

            }
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError( 'Telcos+',
                                       'SoporteBundle.AgenteController.obtieneInfoAdicionalTareaAction',
                                       'Error al consultar info adicional tarea. '.$e->getMessage(),
                                       $usrEmpleado,
                                       $strIpCreacion );
        }
        $objJsonResponse->setData(['data'=>$arrayResultado]);
        return $objJsonResponse;
    }


    /**
     *
     * Actualización: Se cambia los nombres de los campos que retorna el string de las asignaciones por cada usuario
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.3 01-01-2019
     *
     * Actualización: Se obtiene las asignaciones directamente desde la función getEmpleadosConAsignacionesPorDep para optimizar tiempo de respuesta
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.2 04-12-2018
     *
     * Actualización: Se busca el login afectado para presentarlo en la inforación del tooltip
     * Se verifica si tiene perfil para poder mostrar los empleados del departamento sin excluir canton
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 03-10-2018
     *
     * Construye el grid con la informacion de los detalles del cuadro de asignaciones
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 19-07-2018
     * @since 1.0
     * @return JsonResponse
     */
    public function gridGraficoAsignacionesAction()
    {
        $objJsonResponse        = new JsonResponse();
        $em_soporte             = $this->getDoctrine()->getManager('telconet_soporte');
        $em_comercial           = $this->getDoctrine()->getManager('telconet');
        $serviceUtil            = $this->get('schema.Util');
        $objRequest             = $this->get('request');
        $objSession             = $objRequest->getSession();
        $strIpCreacion          = $objRequest->getClientIp();
        $strUsuario             = $objSession->get('user');
        $codEmpresa             = $objSession->get('idEmpresa');
        $dateFechaHoy           = date("Y/m/d");

        $strUserDbSoporte       = $this->container->getParameter('user_soporte');
        $strPasswordDbSoporte   = $this->container->getParameter('passwd_soporte');
        $strDatabaseDsn         = $this->container->getParameter('database_dsn');

        $arrAsignacionesGrafico = array();
        $intLimite              = 15;
        $verEmpleadosDepNac   = false;
        //Se consulta si el tiene perfil asignado para ver todos los empleados de todos los cantones del departamento
        if (true === $this->get('security.context')->isGranted('ROLE_416-6077'))
        {
            $verEmpleadosDepNac = true;
        }

        try
        {
            $arrEmpleado        = $em_comercial->getRepository("schemaBundle:InfoPersona")
                                               ->getPersonaDepartamentoPorUserEmpresa($strUsuario, $codEmpresa);
            if (!empty($arrEmpleado))
            {
                $intIdDepartamento = $arrEmpleado['ID_DEPARTAMENTO'];
                $intIdCanton       = $arrEmpleado['ID_CANTON'];
            }
            $arrayParametrosAsignaciones                         = array();
            $arrayParametrosAsignaciones['strUserDbSoporte']     = $strUserDbSoporte;
            $arrayParametrosAsignaciones['strPasswordDbSoporte'] = $strPasswordDbSoporte;
            $arrayParametrosAsignaciones['strDatabaseDsn']       = $strDatabaseDsn;
            $arrayParametrosAsignaciones['strCodEmpresa']        = $codEmpresa;
            $arrayParametrosAsignaciones['strIdDepartamento']    = $intIdDepartamento;
            if($verEmpleadosDepNac)
            {
                $arrayParametrosAsignaciones['strIdCanton'] = "";
            }
            else
            {
                $arrayParametrosAsignaciones['strIdCanton'] = $intIdCanton;
            }
            $arrayParametrosAsignaciones['strFeCreacion']        = $dateFechaHoy;
            $cursorEmpleados                                     = $em_soporte->getRepository('schemaBundle:InfoAsignacionSolicitud')
                                                                              ->getEmpleadosConAsignacionesPorDep($arrayParametrosAsignaciones);
            if( !empty($cursorEmpleados) )
            {
                $arrayParametros["start"]                  = 0;
                $arrayParametros["limit"]                  = $intLimite;
                $arrayParametros["codEmpresa"]             = $codEmpresa;
                $arrayParametros["feCreacion"]             = $dateFechaHoy;
                $arrayParametros["esGroupBy"]              = "N";
                $arrayParametros["esGroupByUsrAsignacion"] = "N";
                $arrayParametros["esGroupByEstado"]        = "N";
                $arrayParametros["esGroupByTipoAtencion"]  = "N";
                $arrayParametros["esGroupByOrigen"]        = "N";
                $arrayParametros["esOrderByUsrAsignacion"] = "N";
                $arrayParametros["esOrderByEstado"]        = "N";
                $arrayParametros["esOrderByTipoAtencion"]  = "N";
                $arrayParametros["esOrderByTodosEstados"]  = "S";
                $i=0;
                while( ($arrayResultadoCursor = oci_fetch_array($cursorEmpleados, OCI_ASSOC + OCI_RETURN_NULLS)) != false )
                {
                    $strLoginAgente                 = ( isset($arrayResultadoCursor['LOGIN']) && !empty($arrayResultadoCursor['LOGIN']) )
                                                      ? strtolower($arrayResultadoCursor['LOGIN']) : '';
                    $arrayParametros["usrAsignado"] = $strLoginAgente;
                    $i                              = 0;
                    for($intIndice=0;$intIndice<$intLimite;$intIndice++)
                    {
                        $strAsignaciones = (isset($arrayResultadoCursor['ASIGNACIONES']) && !empty($arrayResultadoCursor['ASIGNACIONES']))
                                           ? $arrayResultadoCursor['ASIGNACIONES'] : '';
                        $arrayAsignaciones = json_decode($strAsignaciones);
                        $arrAsignacionesGrafico[$intIndice][$strLoginAgente] =  array(
                                                                                      "tipoAtencion"      => "",
                                                                                      "estado"            => "",
                                                                                      "estadoTarea"       => "",
                                                                                      "estadoCaso"        => "",
                                                                                      "referenciaCliente" => ""
                                                                                     );
                    }

                    for($intIasig=0;$intIasig < count($arrayAsignaciones);$intIasig++)
                    {
                        $arrAsignacionesGrafico[$i][$arrayAsignaciones[$intIasig]->USR]= array(
                                                                                              "tipo"              =>
                                                                                                  $arrayAsignaciones[$intIasig]->TIPO,
                                                                                              "tipoAtencion"      =>
                                                                                                  $arrayAsignaciones[$intIasig]->TATENC,
                                                                                              "estado"            =>
                                                                                                  $arrayAsignaciones[$intIasig]->ESTADO,
                                                                                              "estadoTarea"       =>
                                                                                                  $arrayAsignaciones[$intIasig]->ETAREA,
                                                                                              "estadoCaso"        =>
                                                                                                  $arrayAsignaciones[$intIasig]->ECASO,
                                                                                              "referenciaCliente" =>
                                                                                                  $arrayAsignaciones[$intIasig]->REFCLI,
                                                                                              "numero"            =>
                                                                                                  $arrayAsignaciones[$intIasig]->NUM,
                                                                                              "feCreacion"        =>
                                                                                                  $arrayAsignaciones[$intIasig]->FECHA,
                                                                                              "pin"               =>
                                                                                                  $arrayAsignaciones[$intIasig]->PIN,
                                                                                              "loginAfectado"     =>
                                                                                                  $arrayAsignaciones[$intIasig]->LOGINA
                                                                                        );
                        $i++;
                    }
                }
            }
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError( 'Telcos+',
                                       'SoporteBundle.AgenteController.gridAction',
                                       'Error al consultar las asignaciones. '.$e->getMessage(),
                                       $strUsuario,
                                       $strIpCreacion );
        }
        $objJsonResponse->setData(['data'=>$arrAsignacionesGrafico]);
        return $objJsonResponse;
    }

    /**
     * Actualización: Se añade validación para que lo nuevo de agente solo afecte al nuevo perfil verNuevosCamposTareas 
     * @author Fernando López <filopez@telconet.ec>
     * @version 1.9 31-01-2022
     * 
     * Actualización: Se añade funcionalidad para actualizar asignación cuando se realice una asignación individual
     * desde agente=>asignaciones y actualizar la asiganción cuando se realiza una reasignación de tarea desde 
     * agente=>asignaciones
     * @author Fernando López <filopez@telconet.ec>
     * @version 1.8 02-12-2021
     * 
     * Actualización: Se añade funcionalidad para filtrar por fechas y obtener información adicional
     * @author Francisco Cueva <facueva@telconet.ec>
     * @version 1.7 13-09-2021
     * 
     * Actualización: Se permite asignar por lotes de tareas
     * @author Francisco Cueva <facueva@telconet.ec>
     * @version 1.6 08-07-2021
     * 
     * Actualización: Se agrega que al registrar asignación se registre la oficina del usuario asignado
     *                en lugar del usuario que se encuentra en sesión.
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.5 24-01-2020
     * 
     * Actualización: Se agrega array de asignaciones Proactivas
     * @author Miguel Angulo Sánchez <jmangulos@telconet.ec>
     * @version 1.4 19-06-2019
     * 
     * Actualización: Se ingresa el id de la oficina en la asignacion
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.3 15-01-2019
     *
     * Actualización: Se valida que el agente exista antes de grabar la asignación
     * de crear una asignación
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.2 10-01-2019
     *
     * Actualización: Se permite agregar el numero de tarea o caso directamente al momento
     * de crear una asignación
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 03-10-2018
     *
     * Guarda la asignacion de una solicitud de soporte a un empleado
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 19-07-2018
     * @since 1.0
     *
     * @return JsonResponse
     */
    public function ajaxAsignarSolicitudAction()
    {
        $objRequest        = $this->getRequest();
        $objSesion         = $objRequest->getSession();
        $em_comercial      = $this->getDoctrine()->getManager('telconet');
        $strUsuario        = $objSesion->get('user');
        $codEmpresa        = $objSesion->get('idEmpresa');
        $intIdDepartamento = 0;

        //perfil para nuevas funcionalidades de agente para administrar las tareas
        $booleanPermiteVerNuevosCamposTareas  = $this->get('security.context')->isGranted('ROLE_416-8217');
        
        $arrEmpleado       = $em_comercial->getRepository("schemaBundle:InfoPersona")
                                          ->getPersonaDepartamentoPorUserEmpresa($strUsuario, $codEmpresa);
        
        $strAgente = $objRequest->get('strAgente');
        $emSoporte         = $this->getDoctrine()->getManager("telconet_soporte");
        
        /*Bandera para realizar una actualización de una asiganción, solo aplica para asigancion individual del modulo 
        Agente=>Asignaciones y que tengan el rol de permiteVerNuevosCamposTareas */
        $strUpdateAsignacion = ($objRequest->get('strUpdateAsignacion'))?$objRequest->get('strUpdateAsignacion'):'N';

        /*Bandera para actualizar el agente de una asiganción cuando se ejecuta una tarea de otro usuario */
        $strUpdateNewAgente = ($objRequest->get('strUpdateNewAgente'))?$objRequest->get('strUpdateNewAgente'):'';
        $intIdDetalleHist = ""; // se retorna el nuevo idDetalle historial para iniciarl la tarea
        //Nuevo agente a actualizar cuando se realiza una reasignación desde agente=>asignaciones (formato 1826387@@2833591)
        $strAgenteReasigna = ($objRequest->get('strAgenteReasigna'))?$objRequest->get('strAgenteReasigna'):'';
        $strLoginReasigna = '';

        if($booleanPermiteVerNuevosCamposTareas)
        {                 
            if($strAgenteReasigna != '' && strpos($strAgenteReasigna,'@@')!== false && $strUpdateAsignacion == 'S')
            {
                $arrayPersonaAgente = explode("@@",$strAgenteReasigna);
                $intIdPersona = $arrayPersonaAgente[0];
                $objInfoPersonaAgente = $em_comercial->getRepository("schemaBundle:InfoPersona")->findOneById($intIdPersona);
                if(is_object($objInfoPersonaAgente))
                {
                    $strAgente = $objInfoPersonaAgente->getLogin();
                    $strLoginReasigna = 'S';
                }                      
            }
            if($strUpdateNewAgente == 'S')
            {
                $strLoginReasigna = 'S';
            }

            $objInfoPersonaAsig = $em_comercial->getRepository("schemaBundle:InfoPersona")->findOneByLogin($strAgente);
        }

        if (!empty($arrEmpleado))
        {
            $intIdDepartamento = $arrEmpleado['ID_DEPARTAMENTO'];
        }

        //Valida que el agente exista antes de grabar la asignación
        $arrayAgente = $em_comercial->getRepository("schemaBundle:InfoPersona")
                                    ->getPersonaDepartamentoPorUserEmpresa($strAgente, $codEmpresa);
        if (empty($arrayAgente))
        {
            $strResponse = 'Agente asignado no existe';
            return new Response($strResponse);
        }
        elseif ($intIdDepartamento !== $arrayAgente['ID_DEPARTAMENTO'])
        {
            $strResponse = 'Agente asignado no pertenece a su departamento';
            return new Response($strResponse);
        }
        
        $arrayTareas    = $objRequest->get('strNumero');
        if(is_array($arrayTareas) == 1)
        {
            $arrayIdAsignacion = [];
            foreach ($arrayTareas as $key => $value)
            {
                $arrayResponse = [];
                $intIdAsignacionSolicitud = 0;

                $arrParametros['intDepartamentoId'] = $intIdDepartamento;
                $arrParametros['strOrigen']         = $objRequest->get('strOrigen');
                $arrParametros['strTipoAtencion']   = $objRequest->get('strTipoAtencion');
                $arrParametros['strTipoProblema']   = $objRequest->get('strTipoProblema');
                $arrParametros['strNombreReporta']  = $objRequest->get('strNombreReporta');
                $arrParametros['strNombreSitio']    = $objRequest->get('strNombreSitio');
                $arrParametros['strCriticidad']     = $objRequest->get('strCriticidad');
                $arrParametros['strAgente']         = $objRequest->get('strAgente');
                $arrParametros['strDetalle']        = $objRequest->get('strDetalle');
                $arrParametros['strNumero']         = $value;
                $arrParametros['idEmpresa']         = $objSesion->get('idEmpresa');
                $arrParametros['strUsrCreacion']    = $objSesion->get('user');
                $arrParametros['intOficinaId']      = $arrayAgente['ID_OFICINA'];
                $arrParametros['strIpCreacion']     = $objRequest->getClientIp();
                $arrParametros['arrayAsigProact']   = $objRequest->get('arrayAsigProact');
                $arrParametros['boolFlagRespuesta'] = true;
                $soporteService = $this->get('soporte.SoporteService');
                $strResponse    = $soporteService->crearAsignacionSolicitud($arrParametros);

                $arrayResponse         = explode("|",$strResponse);
                $intIdAsignacionSolicitud = (int)$arrayResponse[1];

                if($booleanPermiteVerNuevosCamposTareas)
                { 
                    $entityInfoAsignacionSolicitud = $emSoporte->getRepository("schemaBundle:InfoAsignacionSolicitud")
                                                                ->findOneById($intIdAsignacionSolicitud);
                    $intIdDetalle = null;
                    $intIdCaso    = null;
                    $strEmpleado  = null;
                    if ( $entityInfoAsignacionSolicitud->getTipoAtencion() === "TAREA" )
                    {
                        $strNumeroTarea = $entityInfoAsignacionSolicitud->getReferenciaId();
                    }
                    elseif ( $entityInfoAsignacionSolicitud->getTipoAtencion() === "CASO" )
                    {
                        $intIdCaso      = $entityInfoAsignacionSolicitud->getReferenciaId();
                        $strNumeroTarea = $intIdTarea;
                    }
                    if(!empty($strNumeroTarea))
                    {
                        $entityInfoComunicacion = $emSoporte->getRepository("schemaBundle:InfoComunicacion")
                                                            ->findOneById($strNumeroTarea);
                        if (is_object($entityInfoComunicacion))
                        {
                            $intIdDetalle = $entityInfoComunicacion->getDetalleId();
                        }
                    }
                    $entityInfoPersona = $em_comercial->getRepository("schemaBundle:InfoPersona")->findOneByLogin($objSesion->get('user'));
                    if (is_object($entityInfoPersona))
                    {
                        $strEmpleado = $entityInfoPersona->getNombres()." ".$entityInfoPersona->getApellidos();
                    }
                    $arrayParametrosSegTarea['idEmpresa']            = $codEmpresa;
                    $arrayParametrosSegTarea['prefijoEmpresa']       = $objSesion->get('prefijoEmpresa');
                    $arrayParametrosSegTarea['idCaso']               = $intIdCaso;
                    $arrayParametrosSegTarea['idDetalle']            = $intIdDetalle;
                    $arrayParametrosSegTarea['seguimiento']          = $objRequest->get('strDetalle');
                    $arrayParametrosSegTarea['departamento']         = $objSesion->get('idDepartamento');
                    $arrayParametrosSegTarea['regInterno']           = "N";
                    $arrayParametrosSegTarea['empleado']             = $strEmpleado;
                    $arrayParametrosSegTarea['usrCreacion']          = $objSesion->get('user');
                    $arrayParametrosSegTarea['ipCreacion']           = $objRequest->getClientIp();
                    $arrayParametrosSegTarea['strEjecucionTarea']    = "N";
                    $arrayParametrosSegTarea['strEnviaDepartamento'] = "S";

                    $strRespuestaReplica = $soporteService->ingresarSeguimientoTarea($arrayParametrosSegTarea);


                    $arrayParametrosAsigTarea['idEmpresa']             = $codEmpresa;
                    $arrayParametrosAsigTarea['strOrigenHal']          = "NO";
                    $arrayParametrosAsigTarea['strClienteReprograma']  = "";
                    $arrayParametrosAsigTarea['prefijoEmpresa']        = $objSesion->get('prefijoEmpresa');
                    $arrayParametrosAsigTarea['id_detalle']            = $intIdDetalle;
                    $arrayParametrosAsigTarea['motivo']                = "";
                    $arrayParametrosAsigTarea['departamento_asignado'] = $objSesion->get('idDepartamento');
                    $arrayParametrosAsigTarea['empleado_asignado']     = $objInfoPersonaAsig->getId();
                    $arrayParametrosAsigTarea['cuadrilla_asignada']    = null;
                    $arrayParametrosAsigTarea['contratista_asignada']  = null;
                    $arrayParametrosAsigTarea['tipo_asignado']         = "empleado";
                    $arrayParametrosAsigTarea['fecha_ejecucion']       = new \DateTime('now');
                    $arrayParametrosAsigTarea['id_departamento']       = $objSesion->get('idDepartamento');
                    $arrayParametrosAsigTarea['clientIp']              = $objRequest->getClientIp();
                    $arrayParametrosAsigTarea['user']                  = $objSesion->get('user');

                    $strRespuestaAsigTarea = $soporteService->reasignarTarea($arrayParametrosAsigTarea);
                }
            }
        }else
        {
            $arrParametros['intDepartamentoId'] = $intIdDepartamento;
            $arrParametros['strOrigen']         = $objRequest->get('strOrigen');
            $arrParametros['strTipoAtencion']   = $objRequest->get('strTipoAtencion');
            $arrParametros['strLogin']          = $objRequest->get('strLogin');
            $arrParametros['strTipoProblema']   = $objRequest->get('strTipoProblema');
            $arrParametros['strNombreReporta']  = $objRequest->get('strNombreReporta');
            $arrParametros['strNombreSitio']    = $objRequest->get('strNombreSitio');
            $arrParametros['strCriticidad']     = $objRequest->get('strCriticidad');
            $arrParametros['strAgente']         = $strAgente;
            $arrParametros['strDetalle']        = $objRequest->get('strDetalle');
            $arrParametros['strNumero']         = $objRequest->get('strNumero');
            $arrParametros['idEmpresa']         = $objSesion->get('idEmpresa');
            $arrParametros['strUsrCreacion']    = $objSesion->get('user');
            $arrParametros['intOficinaId']      = $arrayAgente['ID_OFICINA'];
            $arrParametros['strIpCreacion']     = $objRequest->getClientIp();
            $arrParametros['arrayAsigProact']   = $objRequest->get('arrayAsigProact');
            $arrParametros['boolFlagRespuesta'] = true;
            $arrParametros['strUpdateAsignacion'] = $strUpdateAsignacion;
            $arrParametros['strLoginReasigna'] = $strLoginReasigna;
                    
            $soporteService = $this->get('soporte.SoporteService');
            $strResponse    = $soporteService->crearAsignacionSolicitud($arrParametros);

            $arrayResponse = explode("|",$strResponse);
            $intIdAsignacionSolicitud = (int)$arrayResponse[1];

            if($booleanPermiteVerNuevosCamposTareas)
            {
                $entityInfoAsignacionSolicitud = $emSoporte->getRepository("schemaBundle:InfoAsignacionSolicitud")
                                                    ->findOneById($intIdAsignacionSolicitud);
                $intIdDetalle = null;
                $intIdCaso    = null;
                $strEmpleado  = null;
                if ( $entityInfoAsignacionSolicitud->getTipoAtencion() === "TAREA" )
                {
                    $strNumeroTarea = $entityInfoAsignacionSolicitud->getReferenciaId();
                }
                elseif ( $entityInfoAsignacionSolicitud->getTipoAtencion() === "CASO" )
                {
                    $intIdCaso      = $entityInfoAsignacionSolicitud->getReferenciaId();
                    $strNumeroTarea = $intIdTarea;
                }
                if(!empty($strNumeroTarea))
                {
                    $entityInfoComunicacion = $emSoporte->getRepository("schemaBundle:InfoComunicacion")
                                                        ->findOneById($strNumeroTarea);
                    if (is_object($entityInfoComunicacion))
                    {
                        $intIdDetalle = $entityInfoComunicacion->getDetalleId();
                    }
                }
                $entityInfoPersona = $em_comercial->getRepository("schemaBundle:InfoPersona")->findOneByLogin($objSesion->get('user'));
                if (is_object($entityInfoPersona))
                {
                    $strEmpleado = $entityInfoPersona->getNombres()." ".$entityInfoPersona->getApellidos();
                }
                $arrayParametrosSegTarea['idEmpresa']            = $codEmpresa;
                $arrayParametrosSegTarea['prefijoEmpresa']       = $objSesion->get('prefijoEmpresa');
                $arrayParametrosSegTarea['idCaso']               = $intIdCaso;
                $arrayParametrosSegTarea['idDetalle']            = $intIdDetalle;
                $arrayParametrosSegTarea['seguimiento']          = $objRequest->get('strDetalle');
                $arrayParametrosSegTarea['departamento']         = $objSesion->get('idDepartamento');
                $arrayParametrosSegTarea['regInterno']           = "N";
                $arrayParametrosSegTarea['empleado']             = $strEmpleado;
                $arrayParametrosSegTarea['usrCreacion']          = $objSesion->get('user');
                $arrayParametrosSegTarea['ipCreacion']           = $objRequest->getClientIp();
                $arrayParametrosSegTarea['strEjecucionTarea']    = "N";
                $arrayParametrosSegTarea['strEnviaDepartamento'] = "S";

                $strRespuestaReplica = $soporteService->ingresarSeguimientoTarea($arrayParametrosSegTarea);

                $arrayParametrosAsigTarea['idEmpresa']              = $codEmpresa;
                $arrayParametrosAsigTarea['strOrigenHal']           = "NO";
                $arrayParametrosAsigTarea['strClienteReprograma']   = "";
                $arrayParametrosAsigTarea['prefijoEmpresa']         = $objSesion->get('prefijoEmpresa');
                $arrayParametrosAsigTarea['id_detalle']             = $intIdDetalle;
                $arrayParametrosAsigTarea['motivo']                 = "";
                $arrayParametrosAsigTarea['departamento_asignado']  = $objSesion->get('idDepartamento');
                $arrayParametrosAsigTarea['empleado_asignado']      = $objInfoPersonaAsig->getId();
                $arrayParametrosAsigTarea['cuadrilla_asignada']     = null;
                $arrayParametrosAsigTarea['contratista_asignada']   = null;
                $arrayParametrosAsigTarea['tipo_asignado']          = "empleado";
                $arrayParametrosAsigTarea['fecha_ejecucion']        = new \DateTime('now');
                $arrayParametrosAsigTarea['id_departamento']        = $objSesion->get('idDepartamento');
                $arrayParametrosAsigTarea['clientIp']           = $objRequest->getClientIp();
                $arrayParametrosAsigTarea['user']           = $objSesion->get('user');

                $strRespuestaAsigTarea = $soporteService->reasignarTarea($arrayParametrosAsigTarea);
                
                if(!empty($strNumeroTarea) && !empty($intIdDetalle) && $strUpdateNewAgente == 'S' 
                        && $strUpdateAsignacion == 'S')
                {
                        $objInfoProgresoTarea = $emSoporte->getRepository('schemaBundle:InfoDetalleHistorial')
                                                    ->findOneBy(array('detalleId' => $intIdDetalle),
                                                        array('id'        => 'DESC'));
                        //Nuevo id historial, para poder iniciar la tarea de otro usuario desde asignaciones 
                        $intIdDetalleHist =  $objInfoProgresoTarea->getId();
                        $intIdDetalleHist =  !empty($intIdDetalleHist)?$intIdDetalleHist:"";
                }
            }
        }
        $strNewResponse = ($intIdDetalleHist !=="" )?$arrayResponse[0].'&'.$intIdDetalleHist:$arrayResponse[0];
        return new Response($strNewResponse);
    }
    
    
    /**
     * Función para relacionar Asignación padre e hija
     * @author Miguel Angulo Sánchez <jmangulos@telconet.ec>
     * @version 1.0 24-06-2019
     * @since 1.0
     * @return JsonResponse
     */
    
    public function relacionaAsignacionHijaPadreAction()
    {
        $objRequest        = $this->getRequest();

        $intIdAsigPadre  = $objRequest->get('intIdAsigPadre');
        $arrayIdAsigHija = $objRequest->get('arrayIdAsigHija');
        
        $arreglo = array(
                        "inIdAsigPadre"   => $intIdAsigPadre,
                        "arrayIdAsigHija" => json_decode($arrayIdAsigHija)
                        );
        
        $serviceInfoServicio = $this->get('soporte.SoporteService');
        $strResponse = $serviceInfoServicio->agregaAsignacionesHija($arreglo);
        
        return new Response($strResponse);
    }   

    /**
     * Actualización: Se agrega programación para consultar seguimientos según el id de la tarea enviado por parámetro
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 21-02-2019
     * 
     * El grid para mostrar la información de los seguimientos de una asignacion
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 19-07-2018
     * @since 1.0
     * @return JsonResponse
     */
    public function gridSeguimientosAction()
    {
        $objJsonRespuesta     = new JsonResponse();
        $em                   = $this->getDoctrine()->getManager('telconet_soporte');
        $serviceUtil          = $this->get('schema.Util');
        $objRequest           = $this->get('request');
        $intId                = $objRequest->get('intId');
        $intIdTarea           = $objRequest->get('intIdTarea');
        $objSession           = $objRequest->getSession();
        $strIpCreacion        = $objRequest->getClientIp();
        $strUsuario           = $objSession->get('user');
        $intLimite            = 999999999;
        $strUserDbSoporte     = $this->container->getParameter('user_soporte');
        $strPasswordDbSoporte = $this->container->getParameter('passwd_soporte');
        $strDatabaseDsn       = $this->container->getParameter('database_dsn');

        $objJsonRespuesta->setData(['data'=>array()]);
        try
        {
            $arrayParametrosSeguimientos                          = array();
            $arrayParametrosSeguimientos['intLimit']              = $intLimite;
            $arrayParametrosSeguimientos['intStart']              = 0;
            $arrayParametrosSeguimientos['strUserDbSoporte']      = $strUserDbSoporte;
            $arrayParametrosSeguimientos['strPasswordDbSoporte']  = $strPasswordDbSoporte;
            $arrayParametrosSeguimientos['strDatabaseDsn']        = $strDatabaseDsn;
            $arrayParametrosSeguimientos['intIdAsignacion']       = $intId;
            $arrayParametrosSeguimientos['intIdTarea']            = $intIdTarea;
            $arrayParametrosSeguimientos['strUsrGestion']         = "";
            $arrayParametrosSeguimientos['strTipo']               = "POR_ASIGNACION";
            $objJsonRespuesta = $em->getRepository("schemaBundle:InfoAsignacionSolicitud")
                                   ->generarJsonDetalleSeguimientos($arrayParametrosSeguimientos);
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError( 'Telcos+',
                                       'SoporteBundle.AgenteController.gridAction',
                                       'Error al consultar las asignaciones. '.$e->getMessage(),
                                       $strUsuario,
                                       $strIpCreacion );
        }
        return $objJsonRespuesta;
    }

    /**
     * El grid para mostrar la información de los seguimientos de una asignacion
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 19-07-2018
     * @since 1.0
     * @return JsonResponse
     */
    public function ajaxSeguimientosPendientesAction()
    {
        $objJsonRespuesta     = new JsonResponse();
        $em                   = $this->getDoctrine()->getManager('telconet_soporte');
        $serviceUtil          = $this->get('schema.Util');
        $objRequest           = $this->get('request');
        $objSession           = $objRequest->getSession();
        $strIpCreacion        = $objRequest->getClientIp();
        $strUsuario           = $objSession->get('user');
        $intLimite            = 999999999;
        $strUserDbSoporte     = $this->container->getParameter('user_soporte');
        $strPasswordDbSoporte = $this->container->getParameter('passwd_soporte');
        $strDatabaseDsn       = $this->container->getParameter('database_dsn');

        $objJsonRespuesta->setData(['data'=>array()]);
        try
        {
            $arrayParametrosSeguimientos                          = array();
            $arrayParametrosSeguimientos['strUserDbSoporte']      = $strUserDbSoporte;
            $arrayParametrosSeguimientos['strPasswordDbSoporte']  = $strPasswordDbSoporte;
            $arrayParametrosSeguimientos['strDatabaseDsn']        = $strDatabaseDsn;
            $arrayParametrosSeguimientos['strUsrGestion']         = $strUsuario;
            $objJsonRespuesta = $em->getRepository("schemaBundle:InfoAsignacionSolicitud")
                                   ->generarJsonDetalleSeguimientosPendUsr($arrayParametrosSeguimientos);
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError( 'Telcos+',
                                       'SoporteBundle.AgenteController.ajaxSeguimientosPendientesAction',
                                       'Error al consultar las asignaciones pendientes por usuario. '.$e->getMessage(),
                                       $strUsuario,
                                       $strIpCreacion );
        }
        return $objJsonRespuesta;
    }


    /**
     * Actualización: Se agrega programación para consultar id detalle de la tarea 
     *                según el id de la tarea enviado por parámetro cuando es un Caso
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 21-02-2019
     * 
     * Guarda seguimiento de una asignación
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 20-07-2018
     * @since 1.0
     * @return JsonResponse
     */
    public function ajaxCreaSeguimientoAction()
    {
        $objRequest         = $this->getRequest();
        $objSesion          = $objRequest->getSession();
        $strDetalle         = $objRequest->get('strDetalle');
        $intAsignacionSolId = $objRequest->get('intId');
        $intIdTarea         = $objRequest->get('intIdTarea');
        $strSincronizar     = $objRequest->get('sync');
        $strUsrCreacion     = $objSesion->get('user');
        $codEmpresa         = $objSesion->get('idEmpresa');
        $strPrefijo         = $objSesion->get('prefijoEmpresa');
        $intIdDepartamento  = $objSesion->get('idDepartamento');
        $strIpCreacion      = $objRequest->getClientIp();
        $emSoporte          = $this->getDoctrine()->getManager("telconet_soporte");
        $emComercial        = $this->getDoctrine()->getManager("telconet");

        try
        {
            $emSoporte->getConnection()->beginTransaction();

            $entityInfoAsignacionSolicitud = $emSoporte->getRepository("schemaBundle:InfoAsignacionSolicitud")->findOneById($intAsignacionSolId);

            if (is_object($entityInfoAsignacionSolicitud))
            {
                $arrParametrosSeguimiento['intIdAsignacion']            = $intAsignacionSolId;
                $arrParametrosSeguimiento['strDetalle']                 = $strDetalle;
                $arrParametrosSeguimiento['strUsuarioCreacion']         = $strUsrCreacion;
                $arrParametrosSeguimiento['strUsuarioGestion']          = "";
                $arrParametrosSeguimiento['strIpCreacion']              = $objRequest->getClientIp();
                $arrParametrosSeguimiento['strGestionado']              = "S";
                $arrParametrosSeguimiento['strProcedencia']             = ($strSincronizar==="S"?"Sincronizado":"Interno");
                $arrParametrosSeguimiento['intSeguimientoAsignacionId'] = null;
                $soporteService            = $this->get('soporte.SoporteService');
                $strRespIngresoSeguimiento = $soporteService->crearSeguimientoAsignacionSolicitud($arrParametrosSeguimiento);
                $emSoporte->getConnection()->commit();
                if ($strRespIngresoSeguimiento === "OK" && $strSincronizar === "S")
                {
                    $intIdDetalle = null;
                    $intIdCaso    = null;
                    $strEmpleado  = null;
                    if ( $entityInfoAsignacionSolicitud->getTipoAtencion() === "TAREA" )
                    {
                        $strNumeroTarea = $entityInfoAsignacionSolicitud->getReferenciaId();
                    }
                    elseif ( $entityInfoAsignacionSolicitud->getTipoAtencion() === "CASO" )
                    {
                        $intIdCaso      = $entityInfoAsignacionSolicitud->getReferenciaId();
                        $strNumeroTarea = $intIdTarea;
                    }

                    //Obtenemos el idDetalle con el numero de la tarea id InfoComunicacion
                    if(!empty($strNumeroTarea))
                    {
                        $entityInfoComunicacion = $emSoporte->getRepository("schemaBundle:InfoComunicacion")
                                                            ->findOneById($strNumeroTarea);
                        if (is_object($entityInfoComunicacion))
                        {
                            $intIdDetalle = $entityInfoComunicacion->getDetalleId();
                        }
                    }
                    //Buscamos nombre de empleado
                    $entityInfoPersona = $emComercial->getRepository("schemaBundle:InfoPersona")->findOneByLogin($strUsrCreacion);
                    if (is_object($entityInfoPersona))
                    {
                        $strEmpleado = $entityInfoPersona->getNombres()." ".$entityInfoPersona->getApellidos();
                    }
                    //Copiar el seguimiento a la tarea ligada a la asignación
                    $arrayParametrosSegTarea['idEmpresa']            = $codEmpresa; //codigo de la empresa en sesion
                    $arrayParametrosSegTarea['prefijoEmpresa']       = $strPrefijo; //prefijo de la empresa en sesion
                    $arrayParametrosSegTarea['idCaso']               = $intIdCaso; //id del caso si lo tuviera
                    $arrayParametrosSegTarea['idDetalle']            = $intIdDetalle; //id detalle del id de tarea
                    $arrayParametrosSegTarea['seguimiento']          = $strDetalle; //detalle del seguimiento
                    $arrayParametrosSegTarea['departamento']         = $intIdDepartamento; //id del departamento origen
                    $arrayParametrosSegTarea['regInterno']           = "N"; //si es seguimiento interno de la tarea
                    $arrayParametrosSegTarea['empleado']             = $strEmpleado; //Nombre completo del empleado
                    $arrayParametrosSegTarea['usrCreacion']          = $strUsrCreacion; //usuario que crea el seguimiento
                    $arrayParametrosSegTarea['ipCreacion']           = $strIpCreacion; //ip de creacion del seguimiento
                    $arrayParametrosSegTarea['strEjecucionTarea']    = "N"; //se marca N para el envio de mail y sms al cliente
                    $arrayParametrosSegTarea['strEnviaDepartamento'] = "S"; //Indica si se envia o no el departamento
                    //
                    $respuestaReplica = $soporteService->ingresarSeguimientoTarea($arrayParametrosSegTarea);
                }

                $strResponse = 'OK';
            }
            else
            {
                throw new \Exception('No se encontro id de asignación para crear seguimiento');
            }

        }
        catch(\Exception $e)
        {
            $emSoporte->getConnection()->rollback();
            $emSoporte->getConnection()->close();
            $strResponse = "Ocurrio un error al ingresar el seguimiento, por favor consulte con el Administrador";
            error_log("ERROR AL INGRESAR SEGUIMIENTO DE ASIGNACION DE SOLICITUD");
            error_log($e->getMessage());
        }

        return new Response($strResponse);
    }


    /**
     *
     * Actualización: Se usa service para agregar número de tarea o caso
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 05-11-2018
     *
     * Guarda el numero de tarea en una asignación
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 20-07-2018
     * @since 1.0
     * @return JsonResponse
     */
    public function ajaxAgregarNumeroTareaAction()
    {
        $objRequest         = $this->getRequest();
        $objSesion          = $objRequest->getSession();
        $intAsignacionSolId = $objRequest->get('intId');
        $strNumero          = $objRequest->get('strNumeroTarea');
        $strTipoAtencion    = $objRequest->get('strTipoAtencion');
        $strTipoProblema    = $objRequest->get('strTipoProblema');
        $usrCreacion        = $objSesion->get('user');
        $emSoporte          = $this->getDoctrine()->getManager("telconet_soporte");

        try
        {
                $soporteService = $this->get('soporte.SoporteService');

                $arrayParametros['intIdAsignacion'] = $intAsignacionSolId;
                $arrayParametros['strNumeroTarea']  = $strNumero;
                $arrayParametros['strTipoAtencion'] = $strTipoAtencion;
                $arrayParametros['strTipoProblema'] = $strTipoProblema;
                $arrayParametros['strUsuario']      = $usrCreacion;
                $arrayParametros['strIpCreacion']   = $objRequest->getClientIp();

                $strResponse = $soporteService->agregarNumeroEnAsignacionSolicitud($arrayParametros);
        }
        catch(\Exception $e)
        {
            $emSoporte->getConnection()->rollback();
            $emSoporte->getConnection()->close();
            $strResponse = "Ocurrio un error al modificar asignación, por favor consulte con el Administrador";
            error_log("ERROR AL INGRESAR SEGUIMIENTO DE ASIGNACION DE SOLICITUD");
            error_log($e->getMessage());
        }

        return new Response($strResponse);
    }

    /**
     * Actualización: Se agrega programación para grabar un seguimiento cuando se marque como cambio
     *                de turno a la asignación.
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 27-02-2019
     * 
     * Marcar como cambio de turno a la asignacion
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 24-07-2018
     * @since 1.0
     * @return JsonResponse
     */
    public function ajaxMarcarCambioTurnoAction()
    {
        $objRequest         = $this->getRequest();
        $objSesion          = $objRequest->getSession();
        $intAsignacionSolId = $objRequest->get('intId');
        $strCambioTurno     = $objRequest->get('strCambioTurno');
        $strDetalleSeg      = $objRequest->get('strDetalle');
        $strUsrCreacion     = $objSesion->get('user');
        $emSoporte          = $this->getDoctrine()->getManager("telconet_soporte");
        try
        {
            $emSoporte->getConnection()->beginTransaction();

            $entityInfoAsignacionSolicitud = $emSoporte->getRepository("schemaBundle:InfoAsignacionSolicitud")->findOneById($intAsignacionSolId);
            if (is_object($entityInfoAsignacionSolicitud))
            {
                $entityInfoAsignacionSolicitud->setCambioTurno($strCambioTurno);

                $emSoporte->persist($entityInfoAsignacionSolicitud);
                $emSoporte->flush();

                $arrayParametrosSeg['intIdAsignacion']            = $intAsignacionSolId;
                $arrayParametrosSeg['strDetalle']                 = $strDetalleSeg;
                $arrayParametrosSeg['strUsuarioCreacion']         = $strUsrCreacion;
                $arrayParametrosSeg['strUsuarioGestion']          = "";
                $arrayParametrosSeg['strIpCreacion']              = $objRequest->getClientIp();
                $arrayParametrosSeg['strGestionado']              = "S";
                $arrayParametrosSeg['strProcedencia']             = "Interno";
                $arrayParametrosSeg['intSeguimientoAsignacionId'] = null;
                $objSoporteService                                = $this->get('soporte.SoporteService');
                $objSoporteService->crearSeguimientoAsignacionSolicitud($arrayParametrosSeg);
                $emSoporte->getConnection()->commit();
                $strResponse = 'OK';
            }
            else
            {
                throw new \Exception('No se encontro id de asignación para crear seguimiento');
            }

        }
        catch(\Exception $e)
        {
            $emSoporte->getConnection()->rollback();
            $emSoporte->getConnection()->close();
            $strResponse = "Ocurrio un error al ingresar el seguimiento, por favor consulte con el Administrador";
            error_log("ERROR AL INGRESAR SEGUIMIENTO DE ASIGNACION DE SOLICITUD");
            error_log($e->getMessage());
        }

        return new Response($strResponse);
    }


    /**
     * Actualización: Se agrega programación para validar que si esta la asignación en Standby se cambie a EnGestion
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.5 28-05-2020
     * 
     * Actualización: Se agrega validación de cambio de turno para agentes que esten en estado de conexión almuerzo.
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.3 23-03-2020
     * 
     * Actualización: Se agrega que al realizar cambio de turno se registre la oficina del usuario asignado
     *                en lugar del usuario que se encuentra en sesión.
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.2 24-01-2020
     *
     * Actualización: Se corrige ortografía en mensaje que retorna como respuesta.
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 06-02-2019
     * 
     * Se corrige variable de codEmpresa por strCodEmpresa
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 10-01-2019
     *
     * Graba las asignaciones como cambio de turno y los reasigna a nuevos agentes
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 01-08-2018
     * @since 1.0
     * @return JsonResponse
     */
    public function ajaxGrabaCambioTurnoAction()
    {
        $objRequest         = $this->getRequest();
        $objSesion          = $objRequest->getSession();
        $strAsignaciones    = $objRequest->get('strAsignaciones');
        $strUsrCreacion     = $objSesion->get('user');
        $strCodEmpresa         = $objSesion->get('idEmpresa');
        $emSoporte          = $this->getDoctrine()->getManager("telconet_soporte");
        $objEmComercial     = $this->getDoctrine()->getManager("telconet");
        $emSoporte->getConnection()->beginTransaction();
        $arrayResponse     = array(
                                   'respuesta'       => "ERROR",
                                   'procesoCompleto' => "ERROR",
                                   'detalle'         => ""
                                  );
        $intIdDepartamento       = 0;
        $booleanValidacionAgente = false;
        $strAsignacionesNoExiste = "";
        $strAsignacionesNoPermit = "";
        try
        {
            if (strpos($strAsignaciones, ',')){
                $arrAsignaciones = explode(",",$strAsignaciones);
            }
            else
            {
                $arrAsignaciones[]=$strAsignaciones;
            }
            //Consulto el departamento del empleado en sesion
            $arrayEmpleado       = $objEmComercial->getRepository("schemaBundle:InfoPersona")
                                                  ->getPersonaDepartamentoPorUserEmpresa($strUsrCreacion, $strCodEmpresa);
            if (!empty($arrayEmpleado))
            {
                $intIdDepartamento = $arrayEmpleado['ID_DEPARTAMENTO'];
            }

            for($i=0;$i<count($arrAsignaciones);$i++)
            {

                $arrAsignacion = explode("|",$arrAsignaciones[$i]);

                $entityInfoAsignacionSolicitud = $emSoporte->getRepository("schemaBundle:InfoAsignacionSolicitud")->findOneById($arrAsignacion[0]);
                if (is_object($entityInfoAsignacionSolicitud))
                {
                    //Valida que el agente exista antes de grabar la asignación
                    $arrayAgente = $objEmComercial->getRepository("schemaBundle:InfoPersona")
                                                ->getPersonaDepartamentoPorUserEmpresa($arrAsignacion[1], $strCodEmpresa);
                    if (empty($arrayAgente) || ($intIdDepartamento !== $arrayAgente['ID_DEPARTAMENTO']))
                    {
                        $booleanValidacionAgente = true;
                        $strAsignacionesNoExiste .= $entityInfoAsignacionSolicitud->getReferenciaId()." ";
                    }
                    else
                    {
                        $arrayParametrosEstadoCon["intPersonaRolId"] = $arrayAgente['ID_PERSONA_EMPRESA_ROL'];
                        $arrayResultadoEstadoCon = $emSoporte->getRepository('schemaBundle:InfoAsignacionSolicitud')
                                                    ->getEstadoConexionyExtension($arrayParametrosEstadoCon);

                        if (count($arrayResultadoEstadoCon)>0 && 
                           ($arrayResultadoEstadoCon[0]['estadoConexion'] === "Almuerzo" || 
                           $arrayResultadoEstadoCon[0]['estadoConexion'] === "Ocupado") )
                        {
                            $strAsignacionesNoPermit .= $entityInfoAsignacionSolicitud->getReferenciaId()." ";
                            continue;
                        }

                        $entityInfoAsignacionSolicitud->setCambioTurno("N");
                        if ($entityInfoAsignacionSolicitud->getEstado() === "Standby")
                        {
                            $entityInfoAsignacionSolicitud->setEstado("EnGestion");
                        }
                        $entityInfoAsignacionSolicitud->setUsrAsignado($arrAsignacion[1]);
                        $entityInfoAsignacionSolicitud->setOficinaId($arrayAgente['ID_OFICINA']);


                        $emSoporte->persist($entityInfoAsignacionSolicitud);
                        $emSoporte->flush();

                        $arrParametrosSeguimiento['intIdAsignacion']            = $entityInfoAsignacionSolicitud->getId();
                        $arrParametrosSeguimiento['strDetalle']                 = "Se reasigno asignación a ".
                                                                                  $arrAsignacion[1]." por cambio de turno";
                        $arrParametrosSeguimiento['strUsuarioCreacion']         = $strUsrCreacion;
                        $arrParametrosSeguimiento['strUsuarioGestion']          = "";
                        $arrParametrosSeguimiento['strIpCreacion']              = $objRequest->getClientIp();
                        $arrParametrosSeguimiento['strGestionado']              = "S";
                        $arrParametrosSeguimiento['strProcedencia']             = "Interno";
                        $arrParametrosSeguimiento['intSeguimientoAsignacionId'] = null;
                        $soporteService = $this->get('soporte.SoporteService');
                        $soporteService->crearSeguimientoAsignacionSolicitud($arrParametrosSeguimiento);

                        //Crea un Historial por crear nueva asignación
                        $arrParametrosHist['intIdAsignacion'] = $entityInfoAsignacionSolicitud->getId();
                        $arrParametrosHist['strTipo']         = 'REASIGNACION';
                        $arrParametrosHist['strUsrAsignado']  = $arrAsignacion[1];
                        $arrParametrosHist['strUsrCreacion']  = $strUsrCreacion;
                        $arrParametrosHist['strIpCreacion']   = $objRequest->getClientIp();
                        $soporteService->crearHistorialAsignacionSolicitud($arrParametrosHist);
                    }
                }
                else
                {
                    throw new \Exception('No se encontro id de asignación ('+$arrAsignacion[0]+') para crear seguimiento');
                }
            }
            $arrayResponse ['respuesta']       = "OK";
            $arrayResponse ['procesoCompleto'] = "OK";
            $arrayResponse ['detalle']         = "Proceso finalizó con éxito!";
            $arrayResponse ['detalleNoExiste'] = "";
            $arrayResponse ['detalleNoPermit'] = "";
            if($booleanValidacionAgente === true)
            {
                $arrayResponse ['procesoCompleto'] = "PARCIAL";
                $arrayResponse ['detalleNoExiste'] = "Las siguientes asignaciones:[".$strAsignacionesNoExiste.
                                                     "] no pueden ser procesadas porque los agentes asignados no pertenecen al departamento";
            }
            if ($strAsignacionesNoPermit !== "")
            {
                $arrayResponse ['procesoCompleto'] = "PARCIAL";
                $arrayResponse ['detalleNoPermit'] = "Las siguientes asignaciones: [".$strAsignacionesNoPermit.
                                                     "] no pueden ser procesadas porque los agentes asignados ".
                                                     "se encuentran en estado 'Almuerzo' o 'Ocupado'";
            }

            $emSoporte->getConnection()->commit();
        }
        catch(\Exception $e)
        {
            $emSoporte->getConnection()->rollback();
            $emSoporte->getConnection()->close();
            $arrayResponse = array(
                                   'respuesta' => "ERROR",
                                   'respuesta' => "ERROR",
                                   'detalle'   => "Ocurrio un error al ingresar reasignación por cambio de turno,".
                                                  " por favor consulte con el Administrador"
                                  );
            error_log("ERROR AL REASIGNAR ASIGNACIONES POR CAMBIO DE TURNO");
            error_log($e->getMessage());
        }

        return new JsonResponse($arrayResponse);
    }

    /**
     * Actualización: Se añade parámetros permiteVerNuevosCamposTareas, para validar que se consulte lo nuevo de tarea solo para este rol.
     * @author Fernando López <filopez@telconet.ec>
     * @version 1.5 3-01-2022
     * 
     * Actualización: Se añade parámetros necesarios para la función que genera las asignaciones pendientes. 
     * @author Fernando López <filopez@telconet.ec>
     * @version 1.4 29-12-2021
     * 
     * Actualización: Se recepta parámetro idCanton y si tiene el perfil de ver 
     *                todos los empleados del departamento a nivel nacional
     *                se lo envia a la función que obtiene los datos del grid. 
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.3 13-01-2020
     * 
     * Actualización: Se agrega validación que si tiene perfil de verEmpleadosAsignacionDepNacional no envia el id del cantón
     *                para consultar las asignaciones por cambio de turno
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.2 14-02-2019
     *
     * Actualización: Se consulta las asignaciones por canton
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 15-01-2019
     *
     * El grid para mostrar la información del detalle de asignaciones
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 19-07-2018
     * @since 1.0
     * @return JsonResponse
     */
    public function gridCambioTurnoAction()
    {
        $objJsonRespuesta     = new JsonResponse();
        $em                   = $this->getDoctrine()->getManager('telconet_soporte');
        $em_comercial         = $this->getDoctrine()->getManager('telconet');
        $serviceUtil          = $this->get('schema.Util');
        $objRequest           = $this->get('request');
        $objSession           = $objRequest->getSession();
        $strIpCreacion        = $objRequest->getClientIp();
        $strUsuario           = $objSession->get('user');
        $codEmpresa           = $objSession->get('idEmpresa');
        $intLimite            = 999999999;
        $strUserDbSoporte     = $this->container->getParameter('user_soporte');
        $strPasswordDbSoporte = $this->container->getParameter('passwd_soporte');
        $strDatabaseDsn       = $this->container->getParameter('database_dsn');
        $objJsonRespuesta->setData(['data'=>array()]);
        $intIdDepartamento    = 0;
        $intIdCantonConsulta  = $objRequest->get('idCanton');
        $intIdCanton          = null;

        $intPersonaEmpresaRol    = $objSession->get('idPersonaEmpresaRol');
        $booleanRegistroActivos  = $this->get('security.context')->isGranted('ROLE_197-6779');
        $objEmComunicacion       = $this->getDoctrine()->getManager("telconet_comunicacion");
        $objEmGeneral                = $this->getDoctrine()->getManager('telconet_general');
        
        //Rol que permite ver as nuevas acciones en agente para gestionar la tarea
        $booleanPermiteVerNuevosCamposTareas  = $this->get('security.context')->isGranted('ROLE_416-8217');

        try
        {
            $arrEmpleado        = $em_comercial->getRepository("schemaBundle:InfoPersona")
                                               ->getPersonaDepartamentoPorUserEmpresa($strUsuario, $codEmpresa);
            if (!empty($arrEmpleado))
            {
                $intIdDepartamento     = $arrEmpleado['ID_DEPARTAMENTO'];
                $strNombreDepartamento = $arrEmpleado['NOMBRE_DEPARTAMENTO'];
                $intIdCanton           = $arrEmpleado['ID_CANTON'];
            }
            //Se consulta si el tiene perfil asignado para ver todos los empleados de todos los cantones del departamento
            if (true === $this->get('security.context')->isGranted('ROLE_416-6077'))
            {
                $intIdCanton = $intIdCantonConsulta;
            }
            $i=0;
            $arrayParametros["consultaPorDefecto"]      = "N";
            $arrayParametros["start"]                   = 0;
            $arrayParametros["limit"]                   = $intLimite;
            $arrayParametros["codEmpresa"]              = $codEmpresa;
            $arrayParametros["usrAsignado"]             = "";
            $arrayParametros["feCreacion"]              = "";
            $arrayParametros["strCambioTurno"]          = "S";
            $arrayParametros["intIdDepartamento"]       = $intIdDepartamento;
            $arrayParametros["intIdCanton"]             = $intIdCanton;
            $arrayParametros["esGroupBy"]               = "N";
            $arrayParametros["esGroupByUsrAsignacion"]  = "N";
            $arrayParametros["esGroupByEstado"]         = "N";
            $arrayParametros["esGroupByTipoAtencion"]   = "N";
            $arrayParametros["esGroupByOrigen"]         = "N";
            $arrayParametros["esOrderByUsrAsignacion"]  = "N";
            $arrayParametros["esOrderByEstado"]         = "N";
            $arrayParametros["esOrderByFeCreacionDesc"] = "S";
            $arrayParametros["esOrderByTipoAtencion"]   = "N";
            $arrayParametros["buscaPendientes"]         = "S";
            $arrayParametros["strUserDbSoporte"]        = $strUserDbSoporte;
            $arrayParametros["strPasswordDbSoporte"]    = $strPasswordDbSoporte;
            $arrayParametros["strDatabaseDsn"]          = $strDatabaseDsn;
            $arrayParametros["container"]               = $this->container;
            $arrayParametros["strDepartamento"]         = $strNombreDepartamento;

            $arrayParametros['objEmSoporte']            = $em;
            $arrayParametros['objEmComunicacion']       = $objEmComunicacion;
            $arrayParametros['emComercial']             = $em_comercial;
            $arrayParametros['permiteRegistroActivos']  = $booleanRegistroActivos;
            $arrayParametros['objEmGeneral']            = $objEmGeneral;
            $arrayParametros['idPersonaEmpresaRol']     = $intPersonaEmpresaRol ;
            $arrayParametros["strUsrSession"]           = $strUsuario;

            $arrayParametros['permiteVerNuevosCamposTareas'] = $booleanPermiteVerNuevosCamposTareas;

            $objJsonRespuesta = $em->getRepository("schemaBundle:InfoAsignacionSolicitud")->generarJsonDetalleAsignaciones($arrayParametros);

        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError( 'Telcos+',
                                       'SoporteBundle.AgenteController.gridCambioTurnoAction',
                                       'Error al consultar las asignaciones por cambio de turno. '.$e->getMessage(),
                                       $strUsuario,
                                       $strIpCreacion );
        }
        return $objJsonRespuesta;
    }

    /**
     *
     * Se valida que agente exista antes de grabar la asignación de seguimiento
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 11-01-2019
     *
     * Asigna seguimiento para continuar la gestion de una asignación por otro agente
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 19-07-2018
     * @since 1.0
     * @return JsonResponse
     */
    public function ajaxAsignarSeguimientoAction()
    {        
        $objRequest                = $this->getRequest();
        $objSesion                 = $objRequest->getSession();
        $strUsuario                = $objSesion->get('user');
        $intAsignacionSolId        = $objRequest->get('intId');
        $strDetalle                = $objRequest->get('strDetalle');
        $strUsrAsignadoSeguimiento = $objRequest->get('strUsrGestion');
        $strCodEmpresa             = $objSesion->get('idEmpresa');
        $objEmComercial            = $this->getDoctrine()->getManager("telconet");
        //parametros seguimiento
        $arrParametros['intIdAsignacion']            = $intAsignacionSolId;
        $arrParametros['strDetalle']                 = $strDetalle;
        $arrParametros['strUsuarioCreacion']         = $strUsuario;
        $arrParametros['strUsuarioGestion']          = $strUsrAsignadoSeguimiento;
        $arrParametros['strProcedencia']             = 'Interno';
        $arrParametros['strIpCreacion']              = $objRequest->getClientIp();
        $arrParametros['intSeguimientoAsignacionId'] = null;
        //Valida que el agente exista antes de grabar la asignación
        $arrayAgente = $objEmComercial->getRepository("schemaBundle:InfoPersona")
                                      ->getPersonaDepartamentoPorUserEmpresa($strUsrAsignadoSeguimiento, $strCodEmpresa);
        if (empty($arrayAgente))
        {
            $arrParametros['strGestionado']  = "S";
            $strResponse                     = 'Agente asignado no existe';
            return new Response($strResponse);
        }
        else
        {
            $arrParametros['strGestionado']  = "N";
            $soporteService                  = $this->get('soporte.SoporteService');
            $strResponse                     = $soporteService->crearSeguimientoAsignacionSolicitud($arrParametros);
            return new Response($strResponse);
        }
    }


    /**
     * Responder Asignación de seguimiento
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 08-08-2018
     * @since 1.0
     * @return JsonResponse
     */
    public function ajaxResponderAsignarSeguimientoAction()
    {
        $objRequest                                  = $this->getRequest();
        $strIpCreacion                               = $objRequest->getClientIp();
        $objSesion                                   = $objRequest->getSession();
        $strUsuario                                  = $objSesion->get('user');
        $intAsignacionSolId                          = $objRequest->get('intId');
        $intSeguimientoId                            = $objRequest->get('intIdSeg');
        $strDetalle                                  = $objRequest->get('strDetalle');
        $serviceUtil                                 = $this->get('schema.Util');
        //parametros
        $arrParametros['intIdAsignacion']            = $intAsignacionSolId;
        $arrParametros['strDetalle']                 = $strDetalle;
        $arrParametros['strUsuarioCreacion']         = $strUsuario;
        $arrParametros['strUsuarioGestion']          = "";
        $arrParametros['strProcedencia']             = 'Interno';
        $arrParametros['strIpCreacion']              = $objRequest->getClientIp();
        $arrParametros['strGestionado']              = "S";
        $arrParametros['intSeguimientoAsignacionId'] = $intSeguimientoId;
        $emSoporte                                   = $this->getDoctrine()->getManager("telconet_soporte");
        try
        {
            $emSoporte->getConnection()->beginTransaction();
            //cambia a procesado S el seguimiento asignado
            $entityInfoSeguimiento = $emSoporte->getRepository("schemaBundle:InfoSeguimientoAsignacion")->findOneById($intSeguimientoId);
            if (is_object($entityInfoSeguimiento))
            {
                $entityInfoSeguimiento->setGestionado('S');
                $entityInfoSeguimiento->setFeGestion(new \DateTime('now'));
                $emSoporte->persist($entityInfoSeguimiento);
                $emSoporte->flush();
            }
            $soporteService = $this->get('soporte.SoporteService');
            $strResponse    = $soporteService->crearSeguimientoAsignacionSolicitud($arrParametros);
            $emSoporte->getConnection()->commit();
        }
        catch(\Exception $e)
        {
            $emSoporte->getConnection()->rollback();
            $emSoporte->getConnection()->close();
            error_log($e->getMessage());
            $strResponse = "Ocurrio un error al responder asignación de seguimiento, por favor consulte con el Administrador";
            $serviceUtil->insertError( 'Telcos+',
                                       'SoporteBundle.AgenteController.ajaxResponderAsignarSeguimientoAction',
                                       'Error al responder asignación de seguimiento. '.$e->getMessage(),
                                       $strUsuario,
                                       $strIpCreacion );

        }
        return new Response($strResponse);
    }

    /**
     * Actualización: Se añade parámetros permiteVerNuevosCamposTareas, para validar que se consulte lo nuevo de tarea solo para este rol.
     * @author Fernando López <filopez@telconet.ec>
     * @version 1.3 3-01-2022
     * 
     * Actualización: Se añade parámetros necesarios para la función que genera las asignaciones pendientes. 
     * @author Fernando López <filopez@telconet.ec>
     * @version 1.2 29-12-2021
     * 
     * Actualización: Se agrega programación para validar si se requiere consultar
     *                todas las asignaciones pendientes o no.
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 27-02-2019
     * 
     * El grid para mostrar la información del detalle de asignaciones
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 19-07-2018
     * @since 1.0
     * @return JsonResponse
     */
    public function ajaxAsignacionesPendientesAction()
    {
        $objJsonRespuesta     = new JsonResponse();
        $em                   = $this->getDoctrine()->getManager('telconet_soporte');
        $serviceUtil          = $this->get('schema.Util');
        $objRequest           = $this->get('request');
        $objSession           = $objRequest->getSession();
        $strIpCreacion        = $objRequest->getClientIp();
        $strUsuario           = $objSession->get('user');
        $strCodEmpresa        = $objSession->get('idEmpresa');
        $strBuscarTodos       = $objRequest->get('todos');
        $intIdDepartamento    = $objSession->get('idDepartamento');
        $intIdCanton          = $objSession->get('intIdCanton');
        $intLimite            = 999999999;
        $objJsonRespuesta->setData(['data'=>array()]);

        $intPersonaEmpresaRol    = $objSession->get('idPersonaEmpresaRol');
        $booleanRegistroActivos  = $this->get('security.context')->isGranted('ROLE_197-6779');
        $objEmComunicacion       = $this->getDoctrine()->getManager("telconet_comunicacion");
        $objEmComercial            = $this->getDoctrine()->getManager('telconet');
        $objEmGeneral            = $this->getDoctrine()->getManager('telconet_general');

        //Rol que permite ver as nuevas acciones en agente para gestionar la tarea
        $booleanPermiteVerNuevosCamposTareas  = $this->get('security.context')->isGranted('ROLE_416-8217');
        
        try
        {
            //Se consulta si el tiene perfil asignado para ver todos los empleados de todos los cantones del departamento
            if (true === $this->get('security.context')->isGranted('ROLE_416-6077'))
            {
                $intIdCanton = null;
            }
            $arrayParametros["consultaPorDefecto"]      = "N";
            $arrayParametros["start"]                   = 0;
            $arrayParametros["limit"]                   = $intLimite;
            $arrayParametros["codEmpresa"]              = $strCodEmpresa;
            $arrayParametros["usrAsignado"]             = ($strBuscarTodos === 'S') ? "" : $strUsuario;
            $arrayParametros["feCreacion"]              = "";
            $arrayParametros["strCambioTurno"]          = "";
            $arrayParametros["intIdDepartamento"]       = $intIdDepartamento;
            $arrayParametros["intIdCanton"]             = $intIdCanton;
            $arrayParametros["esGroupBy"]               = "N";
            $arrayParametros["esGroupByUsrAsignacion"]  = "N";
            $arrayParametros["esGroupByEstado"]         = "N";
            $arrayParametros["esGroupByTipoAtencion"]   = "N";
            $arrayParametros["esGroupByOrigen"]         = "N";
            $arrayParametros["esOrderByUsrAsignacion"]  = "N";
            $arrayParametros["esOrderByEstado"]         = "N";
            $arrayParametros["esOrderByFeCreacionDesc"] = "S";
            $arrayParametros["esOrderByTipoAtencion"]   = "N";
            $arrayParametros["buscaPendientes"]         = "S";
            $arrayParametros["strUserDbSoporte"]        = "";
            $arrayParametros["strPasswordDbSoporte"]    = "";
            $arrayParametros["strDatabaseDsn"]          = "";
            $arrayParametros["container"]               = $this->container;

            $arrayParametros['objEmSoporte']            = $em;
            $arrayParametros['objEmComunicacion']       = $objEmComunicacion;
            $arrayParametros['emComercial']             = $objEmComercial;
            $arrayParametros['permiteRegistroActivos']  = $booleanRegistroActivos;
            $arrayParametros['objEmGeneral']            = $objEmGeneral;
            $arrayParametros['idPersonaEmpresaRol']     = $intPersonaEmpresaRol;
            $arrayParametros["strUsrSession"]           = $strUsuario;

            $arrayParametros['permiteVerNuevosCamposTareas'] = $booleanPermiteVerNuevosCamposTareas;

            $objJsonRespuesta = $em->getRepository("schemaBundle:InfoAsignacionSolicitud")->generarJsonDetalleAsignaciones($arrayParametros);
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError( 'Telcos+',
                                       'SoporteBundle.AgenteController.gridAction',
                                       'Error al consultar las asignaciones. '.$e->getMessage(),
                                       $strUsuario,
                                       $strIpCreacion );
        }
        return $objJsonRespuesta;
    }


    /*
     * Proceso para realizar el cambio de turno automáticamente
     * @author Miguel Angulo Sanchez <jmagulos@telconet.ec>
     * @version 1.0 30-05-2019
    */
    public function cambioTurnoAutomaticoAction() 
    {     
        $emSoporte            = $this->getDoctrine()->getManager("telconet_soporte");
        $serviceUtil          = $this->get('schema.Util');
        $objRequest           = $this->get('request');
        $objSession           = $objRequest->getSession();
        $strIpCreacion        = $objRequest->getClientIp();
        $strUsuario           = $objSession->get('user');
        $strCodEmpresa        = $objSession->get('idEmpresa'); 
        $strBuscarTodos       = $objRequest->get('todos');
        $intIdDepartamento    = $objSession->get('idDepartamento');
        $intIdCanton          = $objSession->get('intIdCanton');
        $strDetalleSeg        = "Cambio de turno por método automático.";
        $strMensaje           = 'No se encontraron asignaciones de '.$strUsuario.' para realizar cambio de turno';
        $arrayParametros      = array();
        $arrayRespuesta       = array();
        $arrayParametrosSeg   = array();
        $i                    = 0;
        $arrayParametros["consultaPorDefecto"]      = "N";
        $arrayParametros["codEmpresa"]              = $strCodEmpresa;
        $arrayParametros["usrAsignado"]             = ($strBuscarTodos === 'S') ? "" : $strUsuario;
        $arrayParametros["feCreacion"]              = "";
        $arrayParametros["strCambioTurno"]          = "";
        $arrayParametros["strCambioTurnoAuto"]      = 'S';
        $arrayParametros["intIdDepartamento"]       = $intIdDepartamento;
        $arrayParametros["intIdCanton"]             = $intIdCanton;
        $arrayParametros["esGroupBy"]               = "N";
        $arrayParametros["esGroupByUsrAsignacion"]  = "N";
        $arrayParametros["esGroupByEstado"]         = "N";
        $arrayParametros["esGroupByTipoAtencion"]   = "N";
        $arrayParametros["esGroupByOrigen"]         = "N";
        $arrayParametros["esOrderByUsrAsignacion"]  = "N";
        $arrayParametros["esOrderByEstado"]         = "N";
        $arrayParametros["esOrderByFeCreacionDesc"] = "S";
        $arrayParametros["esOrderByTipoAtencion"]   = "N";
        $arrayParametros["buscaPendientes"]         = "S";
        $arrayParametros["strUserDbSoporte"]        = "";
        $arrayParametros["strPasswordDbSoporte"]    = "";
        $arrayParametros["strDatabaseDsn"]          = "";
        $strCambioTurno                             = 'S';
           
        try
        {
            $arrayRespuesta = $emSoporte->getRepository("schemaBundle:InfoAsignacionSolicitud")->getDetalleAsignaciones($arrayParametros);
            
            if(!empty($arrayRespuesta))
            {
                foreach($arrayRespuesta as $objAsignaciones)
                {
                    
                    $entityInfoAsignacionSolicitud = $emSoporte->getRepository("schemaBundle:InfoAsignacionSolicitud")
                                                               ->findOneById($objAsignaciones['id']);
                    
                    $entityInfoAsignacionSolicitud->setCambioTurno($strCambioTurno);

                    $emSoporte->persist($entityInfoAsignacionSolicitud);
                    $emSoporte->flush();
                    
                    $arrayParametrosSeg['intIdAsignacion']            = $entityInfoAsignacionSolicitud->getID();
                    $arrayParametrosSeg['strDetalle']                 = $strDetalleSeg;
                    $arrayParametrosSeg['strUsuarioCreacion']         = $strUsuario;
                    $arrayParametrosSeg['strUsuarioGestion']          = "";
                    $arrayParametrosSeg['strIpCreacion']              = $strIpCreacion;
                    $arrayParametrosSeg['strGestionado']              = "S";
                    $arrayParametrosSeg['strProcedencia']             = "Interno";
                    $arrayParametrosSeg['intSeguimientoAsignacionId'] = null;
                    $objSoporteService                                = $this->get('soporte.SoporteService');
                    
                    $objSoporteService->crearSeguimientoAsignacionSolicitud($arrayParametrosSeg);
                    
                    $i++;
                }
                    if( $i > 0)
                    {
                        $strMensaje = 'Se realizó el cambio de turno de '.$i.' Asignacion(es) de '.$strUsuario;
                    }
            }
            else
            {
                $strMensaje = 'No se encontraron asignaciones para realizar cambio de turno.';
            }
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError( 'Telcos+',
                                       'SoporteBundle.AgenteController.cambioTurnoAutomaticoAction',
                                       'Error al realizar cambio de turno automático. '.$e->getMessage(),
                                       $strUsuario,
                                       $strIpCreacion );
            
            $strMensaje = 'Ocurrio un error, su solicitud no fue procesada.';
        }
        
        return new Response($strMensaje);
    }
    
    /**
     *
     * Actualización: Se inicializa con null parametros: strNombreReporta, strNombreSitio y strDatoAdicional
     *                para el service modificarAsignacionSolicitud
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 11-12-2018
     *
     * eliminar una asignación asignación
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 16-08-2018
     * @since 1.0
     * @return JsonResponse
     */
    public function ajaxEliminarAsignacionAction()
    {
        $objRequest         = $this->getRequest();
        $objSesion          = $objRequest->getSession();
        $intAsignacionSolId = $objRequest->get('intId');
        $strUsrUltMod       = $objSesion->get('user');
        $strIpCreacion        = $objRequest->getClientIp();
        $emSoporte          = $this->getDoctrine()->getManager("telconet_soporte");

        try
        {
            $emSoporte->getConnection()->beginTransaction();

            $entityInfoAsignacionSolicitud = $emSoporte->getRepository("schemaBundle:InfoAsignacionSolicitud")->findOneById($intAsignacionSolId);
            if (is_object($entityInfoAsignacionSolicitud))
            {
                $arrayParametros['strNombreReporta'] = null;
                $arrayParametros['strNombreSitio']   = null;
                $arrayParametros['strDatoAdicional'] = null;
                $arrParametros['intIdAsignacion']    = $intAsignacionSolId;
                $arrParametros['strEstado']          = "Eliminado";
                $arrParametros['strUsrUltMod']       = $strUsrUltMod;
                $arrParametros['strUsrAsignado']     = "";
                $arrParametros['dateFeUltMod']       = new \DateTime('now');
                $arrParametros['strIpUltMod']        = $strIpCreacion;
                $soporteService = $this->get('soporte.SoporteService');
                $soporteService->modificarAsignacionSolicitud($arrParametros);

                $emSoporte->getConnection()->commit();
                $emSoporte->getConnection()->close();
                $strResponse = 'OK';
            }
            else
            {
                throw new \Exception('No se encontro id de asignación para eliminar');
            }

        }
        catch(\Exception $e)
        {
            $emSoporte->getConnection()->rollback();
            $emSoporte->getConnection()->close();
            $strResponse = "Ocurrio un error al eliminar una asignación, por favor consulte con el Administrador";
            error_log("ERROR AL ELIMINAR DE ASIGNACION DE SOLICITUD");
            error_log($e->getMessage());
        }

        return new Response($strResponse);
    }


    /**
     * eliminar un seguimiento
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 16-08-2018
     * @since 1.0
     * @return JsonResponse
     */
    public function ajaxEliminarSeguimientoAction()
    {
        $objRequest         = $this->getRequest();
        $objSesion          = $objRequest->getSession();
        $intIdSeguimiento   = $objRequest->get('intId');
        $strUsrUltMod       = $objSesion->get('user');
        $strIpCreacion      = $objRequest->getClientIp();
        $emSoporte          = $this->getDoctrine()->getManager("telconet_soporte");
        try
        {
            $emSoporte->getConnection()->beginTransaction();

            $entityInfoSeguimientoAsignacion = $emSoporte->getRepository("schemaBundle:InfoSeguimientoAsignacion")->findOneById($intIdSeguimiento);
            if (is_object($entityInfoSeguimientoAsignacion))
            {
                $arrParametros['intIdSeguimiento'] = $intIdSeguimiento;
                $arrParametros['strEstado']       = "Eliminado";
                $arrParametros['strUsrUltMod']    = $strUsrUltMod;
                $arrParametros['dateFeUltMod']    = new \DateTime('now');
                $arrParametros['strIpUltMod']     = $strIpCreacion;
                $soporteService = $this->get('soporte.SoporteService');
                $soporteService->modificarSeguimientoAsignacion($arrParametros);

                $emSoporte->getConnection()->commit();
                $strResponse = 'OK';
            }
            else
            {
                throw new \Exception('No se encontro id de asignación para crear seguimiento');
            }

        }
        catch(\Exception $e)
        {
            $emSoporte->getConnection()->rollback();
            $emSoporte->getConnection()->close();
            $strResponse = "Ocurrio un error al eliminar una asignación, por favor consulte con el Administrador";
            error_log("ERROR AL ELIMINAR DE ASIGNACION DE SOLICITUD");
            error_log($e->getMessage());
        }
        return new Response($strResponse);
    }

    /**
     * Construye el grid con la informacion de los detalles del cuadro de asignaciones por usuario en sesión
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 20-08-2018
     * @since 1.0
     * @return JsonResponse
     */
    public function gridGraficoAsignacionesUsrAction()
    {
        $objJsonResponse        = new JsonResponse();
        $em_soporte             = $this->getDoctrine()->getManager('telconet_soporte');
        $serviceUtil            = $this->get('schema.Util');
        $objRequest             = $this->get('request');
        $objSession             = $objRequest->getSession();
        $strIpCreacion          = $objRequest->getClientIp();
        $strUsuario             = $objSession->get('user');
        $codEmpresa             = $objSession->get('idEmpresa');
        $strUserDbSoporte       = $this->container->getParameter('user_soporte');
        $strPasswordDbSoporte   = $this->container->getParameter('passwd_soporte');
        $strDatabaseDsn         = $this->container->getParameter('database_dsn');
        $dateFechaHoy           = date("Y/m/d");
        $intLimite              = 25;
        $arrAsignacionesGrafico = array();
        try
        {
            $arrayParametros["start"]                   = 0;
            $arrayParametros["limit"]                   = 0;
            $arrayParametros["codEmpresa"]              = $codEmpresa;
            $arrayParametros["usrAsignado"]             = $strUsuario;
            $arrayParametros["feCreacion"]              = $dateFechaHoy;
            $arrayParametros["esGroupBy"]               = "N";
            $arrayParametros["esGroupByUsrAsignacion"]  = "N";
            $arrayParametros["esGroupByEstado"]         = "N";
            $arrayParametros["esGroupByTipoAtencion"]   = "N";
            $arrayParametros["esGroupByOrigen"]         = "N";
            $arrayParametros["esOrderByUsrAsignacion"]  = "S";
            $arrayParametros["esOrderByEstado"]         = "N";
            $arrayParametros["esOrderByTipoAtencion"]   = "N";
            $arrayParametros["esOrderByFeCreacionDesc"] = "S";
            $arrayParametros["esOrderByTodosEstados"]   = "N";

            for($intIndice=0;$intIndice<$intLimite;$intIndice++)
            {
                for($intIndiceFila=0;$intIndiceFila<8;$intIndiceFila++)
                {
                    $strTitulo = "";

                    $arrAsignacionesGrafico[$intIndiceFila][$intIndice] =  array(
                                                                                "titulo"            => $strTitulo,
                                                                                "tipoAtencion"      => "",
                                                                                "estado"            => "",
                                                                                "estadoTarea"       => "",
                                                                                "estadoCaso"        => "",
                                                                                "referenciaCliente" => ""
                                                                               );
                }
            }
            $intItarea=0;
            $intIcaso=0;
            $intIpendiente=0;
            $arrayAsignaciones = $em_soporte->getRepository("schemaBundle:InfoAsignacionSolicitud")->getRegistrosAsignaciones( $arrayParametros );
            foreach($arrayAsignaciones as $arrAsignacion)
            {
                if ($arrAsignacion['tipoAtencion']==='TAREA' && $arrAsignacion["estado"]!=='Pendiente')
                {
                    $arrAsignacionesGrafico[1][$intItarea] =  array(
                                                                        "titulo"            => "",
                                                                        "tipoAtencion"      => $arrAsignacion['tipoAtencion'],
                                                                        "estado"            => $arrAsignacion['estado'],
                                                                        "estadoTarea"       => $arrAsignacion['estadoTarea'],
                                                                        "estadoCaso"        => $arrAsignacion['estadoCaso'],
                                                                        "referenciaCliente" => $arrAsignacion['referenciaCliente'],
                                                                        "numero"            => $arrAsignacion['numero'],
                                                                        "feCreacion"        => $arrAsignacion['feCreacion']
                                                                   );
                    $intItarea++;
                }
                elseif ($arrAsignacion['tipoAtencion']==='CASO' && $arrAsignacion["estado"]!=='Pendiente')
                {
                    $arrAsignacionesGrafico[2][$intIcaso] =  array(
                                                                        "titulo"            => "",
                                                                        "tipoAtencion"      => $arrAsignacion['tipoAtencion'],
                                                                        "estado"            => $arrAsignacion['estado'],
                                                                        "estadoTarea"       => $arrAsignacion['estadoTarea'],
                                                                        "estadoCaso"        => $arrAsignacion['estadoCaso'],
                                                                        "referenciaCliente" => $arrAsignacion['referenciaCliente'],
                                                                        "numero"            => $arrAsignacion['numero'],
                                                                        "feCreacion"        => $arrAsignacion['feCreacion']
                                                                   );
                    $intIcaso++;
                }
                else
                {
                    $arrAsignacionesGrafico[3][$intIpendiente] =  array(
                                                                        "titulo"            => "",
                                                                        "tipoAtencion"      => $arrAsignacion['tipoAtencion'],
                                                                        "estado"            => $arrAsignacion['estado'],
                                                                        "estadoTarea"       => $arrAsignacion['estadoTarea'],
                                                                        "estadoCaso"        => $arrAsignacion['estadoCaso'],
                                                                        "referenciaCliente" => $arrAsignacion['referenciaCliente'],
                                                                        "numero"            => $arrAsignacion['numero'],
                                                                        "feCreacion"        => $arrAsignacion['feCreacion']
                                                                   );
                    $intIpendiente++;
                }
            }
            //Consultamos los seguimientos por usuario
            $arrayParametrosSeguimientos                          = array();
            $arrayParametrosSeguimientos['intLimit']              = $intLimite;
            $arrayParametrosSeguimientos['intStart']              = 0;
            $arrayParametrosSeguimientos['strUserDbSoporte']      = $strUserDbSoporte;
            $arrayParametrosSeguimientos['strPasswordDbSoporte']  = $strPasswordDbSoporte;
            $arrayParametrosSeguimientos['strDatabaseDsn']        = $strDatabaseDsn;
            $arrayParametrosSeguimientos['intIdAsignacion']       = null;
            $arrayParametrosSeguimientos['strUsrCreacion']        = $strUsuario;
            $arrayParametrosSeguimientos['strFeCreacion']         = $dateFechaHoy;
            $arrayParametrosSeguimientos['strTipo']               = "POR_USUARIO";
            $cursorSeguimientos                                   = $em_soporte->getRepository("schemaBundle:InfoAsignacionSolicitud")
                                                                               ->getSeguimientos($arrayParametrosSeguimientos);
            if( !empty($cursorSeguimientos) )
            {
                $intIseguimientos = 0;
                while( ($arrayResultadoCursor = oci_fetch_array($cursorSeguimientos, OCI_ASSOC + OCI_RETURN_NULLS)) != false )
                {
                    $strFeCreacionSeg                         = ( isset($arrayResultadoCursor['FE_CREACION'])
                                                                  && !empty($arrayResultadoCursor['FE_CREACION']) )
                                                                  ? $arrayResultadoCursor['FE_CREACION'] : '';
                    $strDetalleSeg                            = ( isset($arrayResultadoCursor['DETALLE'])
                                                                  && !empty($arrayResultadoCursor['DETALLE']) )
                                                                  ? strtolower($arrayResultadoCursor['DETALLE']) : '';
                    $strTipoSeg                               = ( isset($arrayResultadoCursor['TIPO'])
                                                                  && !empty($arrayResultadoCursor['TIPO']) )
                                                                  ? strtolower($arrayResultadoCursor['TIPO']) : '';
                    $strNumeroSeg                             = ( isset($arrayResultadoCursor['NUMERO'])
                                                                  && !empty($arrayResultadoCursor['NUMERO']) )
                                                                  ? strtolower($arrayResultadoCursor['NUMERO']) : '';
                    $strTipoAtencionSeg                       = ( isset($arrayResultadoCursor['TIPO_ATENCION'])
                                                                  && !empty($arrayResultadoCursor['TIPO_ATENCION']) )
                                                                  ? strtoupper($arrayResultadoCursor['TIPO_ATENCION']) : '';
                    $strReferenciaClienteSeg                  = ( isset($arrayResultadoCursor['REFERENCIA_CLIENTE'])
                                                                  && !empty($arrayResultadoCursor['REFERENCIA_CLIENTE']) )
                                                                  ? strtolower($arrayResultadoCursor['REFERENCIA_CLIENTE']) : '';

                    $arrAsignacionesGrafico[4][$intIseguimientos] =  array(
                                                                           "titulo"            => "",
                                                                           "tipoAtencion"      => $strTipoAtencionSeg,
                                                                           "estado"            => "",
                                                                           "estadoTarea"       => "",
                                                                           "estadoCaso"        => "",
                                                                           "referenciaCliente" => $strReferenciaClienteSeg,
                                                                           "numero"            => $strNumeroSeg,
                                                                           "feCreacion"        => $strFeCreacionSeg
                                                                   );
                    $intIseguimientos++;
                }
            }
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError( 'Telcos+',
                                       'SoporteBundle.AgenteController.gridAction',
                                       'Error al consultar las asignaciones. '.$e->getMessage(),
                                       $strUsuario,
                                       $strIpCreacion );
        }
        $objJsonResponse->setData(['data'=>$arrAsignacionesGrafico]);
        return $objJsonResponse;
    }


    /**
     * Actualización: Se agrega consulta de tipos problemas que sea ahora por idDepartamento
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 26-09-2018
     *
     * Metodo que retorna los tipos de problema según tipo de atención enviado por parametro
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 22-08-2018
     * @since 1.0
     * @return JsonResponse
     */
    public function ajaxObtenerTiposProblemaAction()
    {
        $objJsonRespuesta     = new JsonResponse();
        $emSoporte            = $this->getDoctrine()->getManager('telconet_soporte');
        $emComercial          = $this->getDoctrine()->getManager('telconet');
        $serviceUtil          = $this->get('schema.Util');
        $objRequest           = $this->get('request');
        $objSession           = $objRequest->getSession();
        $codEmpresa           = $objSession->get('idEmpresa');
        $strIpCreacion        = $objRequest->getClientIp();
        $strUsuario           = $objSession->get('user');

        $objJsonRespuesta->setData(['data'=>array()]);
        try
        {
            $arrEmpleado        = $emComercial->getRepository("schemaBundle:InfoPersona")
                                               ->getPersonaDepartamentoPorUserEmpresa($strUsuario, $codEmpresa);
            if (!empty($arrEmpleado))
            {
                $intIdDepartamento = $arrEmpleado['ID_DEPARTAMENTO'];
            }

            $arrTiposProblema = $emSoporte->getRepository("schemaBundle:AdmiParametroDet")->get(
                                                                                                "TIPOS DE PROBLEMA PARA MODULO ASIGNACIONES TN",
                                                                                                "SOPORTE",
                                                                                                "",
                                                                                                "",
                                                                                                //$tipoAtencion,
                                                                                                "",
                                                                                                "",
                                                                                                $intIdDepartamento,
                                                                                                "",
                                                                                                "",
                                                                                                $codEmpresa
                                                                                               );
            $objJsonRespuesta->setData(['data'=>$arrTiposProblema]);
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError( 'Telcos+',
                                       'SoporteBundle.AgenteController.ajaxObtenerTiposProblemaAction',
                                       'Error al consultar los tipos de problema. '.$e->getMessage(),
                                       $strUsuario,
                                       $strIpCreacion );
        }
        return $objJsonRespuesta;
    }

    /**
     * El grid para mostrar la información del historial de la asignación
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 31-08-2018
     * @since 1.0
     * @return JsonResponse
     */
    public function gridHistorialAsignacionAction()
    {
        $objJsonRespuesta     = new JsonResponse();
        $em                   = $this->getDoctrine()->getManager('telconet_soporte');
        $serviceUtil          = $this->get('schema.Util');
        $objRequest           = $this->get('request');
        $intId                = $objRequest->get('intId');
        $objSession           = $objRequest->getSession();
        $strIpCreacion        = $objRequest->getClientIp();
        $strUsuario           = $objSession->get('user');
        $intLimite            = 999999999;

        $objJsonRespuesta->setData(['data'=>array()]);
        try
        {
            $arrayParametrosHistorial                          = array();
            $arrayParametrosHistorial['intLimit']              = $intLimite;
            $arrayParametrosHistorial['intStart']              = 0;
            $arrayParametrosHistorial['intIdAsignacion']       = $intId;
            $arrayParametrosHistorial['strTipo']               = "TODOS";

            $objJsonRespuesta = $em->getRepository("schemaBundle:InfoAsignacionSolicitud")
                                   ->generarJsonDetalleHistorialAsignacion($arrayParametrosHistorial);
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError( 'Telcos+',
                                       'SoporteBundle.AgenteController.gridHistorialAsignacionAction',
                                       'Error al consultar historial de asignaciones. '.$e->getMessage(),
                                       $strUsuario,
                                       $strIpCreacion );
        }
        return $objJsonRespuesta;
    }

    /**
     * Se agrega programación para consultar asignaciones totalizadas por estado
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.5 28-05-2020
     * 
     * Actualización: Se recibe parametro canton para consultar por ciudad si es requerido
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.4 20-04-2020
     * 
     * Actualización: Se agrega parametro para consultar las asignaciones por canton
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.3 15-01-2019
     *
     * Actualización: Se optimiza función, ahora se usa procedimiento de oracle para obtener información
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.2 05-12-2018
     *
     * Actualización: Se busca las asignaciones ahora por rango de fechas
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 26-09-2018
     *
     * Obtiene asignaciones totalizado por usuario de creación
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 19-07-2018
     * @since 1.0
     * @return JsonResponse
     */
    public function obtieneAsignacionesTotalizadoAction()
    {
        $objJsonResponse        = new JsonResponse();
        $em_soporte             = $this->getDoctrine()->getManager('telconet_soporte');
        $em_comercial           = $this->getDoctrine()->getManager('telconet');
        $serviceUtil            = $this->get('schema.Util');
        $objRequest             = $this->get('request');
        $objSession             = $objRequest->getSession();
        $strIpCreacion          = $objRequest->getClientIp();
        $strUsuario             = $objSession->get('user');
        $strCodEmpresa          = $objSession->get('idEmpresa');
        $strTotalizadoPor       = $objRequest->get('totalizadoPor');
        $strFechaIni            = $objRequest->get('fechaIni');
        $strFechaFin            = $objRequest->get('fechaFin');
        $intIdCantonConsulta    = $objRequest->get('idCanton');
        $strEstado              = $objRequest->get('estado');
        //
        $strUserDbSoporte       = $this->container->getParameter('user_soporte');
        $strPasswordDbSoporte   = $this->container->getParameter('passwd_soporte');
        $strDatabaseDsn         = $this->container->getParameter('database_dsn');

        $arrAsignacionesGrafico = array();
        try
        {
            $arrEmpleado        = $em_comercial->getRepository("schemaBundle:InfoPersona")
                                               ->getPersonaDepartamentoPorUserEmpresa($strUsuario, $strCodEmpresa);
            if (!empty($arrEmpleado))
            {
                $intIdDepartamento     = $arrEmpleado['ID_DEPARTAMENTO'];
                $intIdCanton           = $arrEmpleado['ID_CANTON'];
            }
            //Se consulta si el tiene perfil asignado para ver todos los empleados de todos los cantones del departamento
            if (true === $this->get('security.context')->isGranted('ROLE_416-6077'))
            {
                $intIdCanton = $intIdCantonConsulta;
            }

            //Consultamos los seguimientos por usuario
            $arrayParametrosTotalizado                          = array();
            $arrayParametrosTotalizado['strUserDbSoporte']      = $strUserDbSoporte;
            $arrayParametrosTotalizado['strPasswordDbSoporte']  = $strPasswordDbSoporte;
            $arrayParametrosTotalizado['strDatabaseDsn']        = $strDatabaseDsn;
            $arrayParametrosTotalizado['strCodEmpresa']         = $strCodEmpresa;
            $arrayParametrosTotalizado['intIdDepartamento']     = $intIdDepartamento;
            $arrayParametrosTotalizado['intIdCanton']           = $intIdCanton;
            $arrayParametrosTotalizado['strEstado']             = $strEstado;
            $arrayParametrosTotalizado['strFeCreacionIni']      = $strFechaIni;
            $arrayParametrosTotalizado['strFeCreacionFin']      = $strFechaFin;
            $arrayParametrosTotalizado['strTotalizadoPor']      = $strTotalizadoPor;
            $objCursorTotalizado                                   = $em_soporte->getRepository("schemaBundle:InfoAsignacionSolicitud")
                                                                             ->getAsignacionesTotalizado($arrayParametrosTotalizado);
            if( !empty($objCursorTotalizado) )
            {
                $intI=0;
                while( $arrayResultadoCursor = oci_fetch_array($objCursorTotalizado, OCI_ASSOC + OCI_RETURN_NULLS) )
                {
                    $strItem                                = ( isset($arrayResultadoCursor['ITEM'])
                                                                  && !empty($arrayResultadoCursor['ITEM']) )
                                                                  ? $arrayResultadoCursor['ITEM'] : '';
                    $strCantidad                            = ( isset($arrayResultadoCursor['CANTIDAD'])
                                                                  && !empty($arrayResultadoCursor['CANTIDAD']) )
                                                                  ? $arrayResultadoCursor['CANTIDAD'] : '';
                    $strSubItems                            = ( isset($arrayResultadoCursor['SUBCONSULTA'])
                                                                  && !empty($arrayResultadoCursor['SUBCONSULTA']) )
                                                                  ? $arrayResultadoCursor['SUBCONSULTA'] : '';
                    $color = "";
                    $arrAsignacionesGrafico[$intI] =  array(
                                                            "name"      => $strItem,
                                                            "y"         => intval($strCantidad),
                                                            "color"     => $color,
                                                            "drilldown" => $strItem
                                                        );
                    $arraySubItems    = json_decode($strSubItems);

                    $arrTiposProblema = array();
                    for($intIitems=0;$intIitems < count($arraySubItems);$intIitems++)
                    {
                        $arrTiposProblema []= array(
                                                        $arraySubItems[$intIitems]->ITEM,
                                                        intval($arraySubItems[$intIitems]->CANTIDAD)
                                                   );
                    }
                    $arrAsignacionesGraficoUsuario[] = array(
                                                                "id"   => $strItem,
                                                                "data" => $arrTiposProblema
                                                            );
                    $intI++;
                }
            }

        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError( 'Telcos+',
                                       'SoporteBundle.AgenteController.gridAction',
                                       'Error al consultar las asignaciones. '.$e->getMessage(),
                                       $strUsuario,
                                       $strIpCreacion );
        }
        $objJsonResponse->setData(['data'=>$arrAsignacionesGrafico,'dataUser'=>$arrAsignacionesGraficoUsuario]);
        return $objJsonResponse;
    }

    /**
     * Obtiene asignaciones totalizado por estado
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 07-02-2019
     * @since 1.0
     * @return JsonResponse
     */
    public function obtieneAsignacionesTotalizadoPorEstadoAction()
    {
        $objJsonResponse          = new JsonResponse();
        $objEmSoporte             = $this->getDoctrine()->getManager('telconet_soporte');
        $objEmComercial           = $this->getDoctrine()->getManager('telconet');
        $objServiceUtil           = $this->get('schema.Util');
        $objRequest               = $this->get('request');
        $objSession               = $objRequest->getSession();
        $strIpCreacion            = $objRequest->getClientIp();
        $strUsuario               = $objSession->get('user');
        $strCodEmpresa            = $objSession->get('idEmpresa');        
        $intIdCanton              = null;
        $strUserDbSoporte         = $this->container->getParameter('user_soporte');
        $strPasswordDbSoporte     = $this->container->getParameter('passwd_soporte');
        $strDatabaseDsn           = $this->container->getParameter('database_dsn');
        $arrayAsignacionesGrafico = array();

        try
        {
            $arrayEmpleado        = $objEmComercial->getRepository("schemaBundle:InfoPersona")
                                                   ->getPersonaDepartamentoPorUserEmpresa($strUsuario, $strCodEmpresa);
            if (!empty($arrayEmpleado))
            {
                $intIdDepartamento     = $arrayEmpleado['ID_DEPARTAMENTO'];
                $intIdCanton           = $arrayEmpleado['ID_CANTON'];
            }
            //Se consulta si el tiene perfil asignado para ver todos los empleados de todos los cantones del departamento
            if (true === $this->get('security.context')->isGranted('ROLE_416-6077'))
            {
                $intIdCanton = null;
            }

            //Consultamos los seguimientos por usuario
            $arrayParametrosTotalizado                          = array();
            $arrayParametrosTotalizado['strUserDbSoporte']      = $strUserDbSoporte;
            $arrayParametrosTotalizado['strPasswordDbSoporte']  = $strPasswordDbSoporte;
            $arrayParametrosTotalizado['strDatabaseDsn']        = $strDatabaseDsn;
            $arrayParametrosTotalizado['strCodEmpresa']         = $strCodEmpresa;
            $arrayParametrosTotalizado['intIdDepartamento']     = $intIdDepartamento;
            $arrayParametrosTotalizado['intIdCanton']           = $intIdCanton;
            $objCursorTotalizado                                = $objEmSoporte->getRepository("schemaBundle:InfoAsignacionSolicitud")
                                                                               ->getAsignacionesTotalizadoPorEstado($arrayParametrosTotalizado);
            if( !empty($objCursorTotalizado) )
            {
                $intI=0;
                while( $arrayResultadoCursor = oci_fetch_array($objCursorTotalizado, OCI_ASSOC + OCI_RETURN_NULLS) )
                {
                    $strItem                                = ( isset($arrayResultadoCursor['ITEM'])
                                                                  && !empty($arrayResultadoCursor['ITEM']) )
                                                                  ? $arrayResultadoCursor['ITEM'] : '';
                    $strCantidad                            = ( isset($arrayResultadoCursor['CANTIDAD'])
                                                                  && !empty($arrayResultadoCursor['CANTIDAD']) )
                                                                  ? $arrayResultadoCursor['CANTIDAD'] : '';
                    $arrayAsignacionesGrafico[$intI] =  array(
                                                              "name"      => $strItem,
                                                              "y"         => intval($strCantidad),
                                                              "color"     => "",
                                                              "drilldown" => $strItem
                                                        );
                    $arraySubItems    = json_decode($strSubItems);

                    $arrayTiposProblema = array();
                    for($intIitems=0;$intIitems < count($arraySubItems);$intIitems++)
                    {
                        $arrayTiposProblema []= array(
                                                          $arraySubItems[$intIitems]->ITEM,
                                                          intval($arraySubItems[$intIitems]->CANTIDAD)
                                                     );
                    }
                    $arrayAsignacionesGraficoUsuario[] = array(
                                                                  "id"   => $strItem,
                                                                  "data" => $arrTiposProblema
                                                              );
                    $intI++;
                }
            }
        }
        catch(\Exception $e)
        {
            $objServiceUtil->insertError( 'Telcos+',
                                          'SoporteBundle.AgenteController.gridAction',
                                          'Error al consultar las asignaciones. '.$e->getMessage(),
                                          $strUsuario,
                                          $strIpCreacion );
        }
        $objJsonResponse->setData(['data'=>$arrayAsignacionesGrafico,'dataUser'=>$arrayAsignacionesGraficoUsuario]);
        return $objJsonResponse;
    }

    /**
     * Se agrega programación para consultar seguimientos totalizados por estado
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.5 28-05-2020
     * 
     * Actualización: Se recibe parametro canton para consultar por ciudad si es requerido
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.4 20-04-2020
     *
     * Actualización: Se agrega parametro para consultar los seguimientos por canton
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.2 15-01-2019
     *
     * Actualización: Se busca los seguimientos ahora por rango de fechas
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 26-09-2018
     *
     * Obtiene seguimientos totalizado por usuario de creación
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 23-09-2018
     * @since 1.0
     * @return JsonResponse
     */
    public function obtieneSeguimientosTotalizadoPorUsrAction()
    {
        $objJsonResponse        = new JsonResponse();
        $em_soporte             = $this->getDoctrine()->getManager('telconet_soporte');
        $em_comercial           = $this->getDoctrine()->getManager('telconet');
        $serviceUtil            = $this->get('schema.Util');
        $objRequest             = $this->get('request');
        $objSession             = $objRequest->getSession();
        $strIpCreacion          = $objRequest->getClientIp();
        $strUsuario             = $objSession->get('user');
        $codEmpresa             = $objSession->get('idEmpresa');
        $strFechaIni            = $objRequest->get('fechaIni');
        $strFechaFin            = $objRequest->get('fechaFin');
        $intIdCantonConsulta    = $objRequest->get('idCanton');
        $strEstado              = $objRequest->get('estado');
        $dateFechaHoy           = date("Y/m/d");
        $intIdCanton            = null;

        $arrSeguimientosGrafico = array();
        try
        {
            $arrEmpleado        = $em_comercial->getRepository("schemaBundle:InfoPersona")
                                               ->getPersonaDepartamentoPorUserEmpresa($strUsuario, $codEmpresa);
            if (!empty($arrEmpleado))
            {
                $intIdDepartamento     = $arrEmpleado['ID_DEPARTAMENTO'];
                $intIdCanton           = $arrEmpleado['ID_CANTON'];
            }
            //Se consulta si se tiene perfil asignado para ver todos los empleados de todos los cantones
            if (true === $this->get('security.context')->isGranted('ROLE_416-6077'))
            {
                $intIdCanton = $intIdCantonConsulta;
            }
            $arrParametros["strCodEmpresa"]           = $codEmpresa;
            $arrParametros["intIdDepartamento"]       = $intIdDepartamento;
            $arrParametros['intIdCanton']             = $intIdCanton;
            $arrParametros['strEstado']               = $strEstado;
            $arrParametros["objFeCreacion"]           = $dateFechaHoy;
            $arrParametros["strFeCreacionIni"]        = $strFechaIni;
            $arrParametros["strFeCreacionFin"]        = $strFechaFin;

            $arrParametros["strTotalizadoPor"]     = 'USUARIO';
            $i=0;
            $arraySeguimientos = $em_soporte->getRepository("schemaBundle:InfoAsignacionSolicitud")->getSeguimientosTotalizado($arrParametros);

            foreach($arraySeguimientos as $arrSeguimiento)
            {


                $arrSeguimientosGrafico[$i] =  array(
                                                        "name"      => $arrSeguimiento['usrCreacion'],
                                                        "y"         => intval($arrSeguimiento['cantidad']),
                                                        "drilldown" => $arrSeguimiento['usrAsignado']
                                                    );
                $i++;
            }
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError( 'Telcos+',
                                       'SoporteBundle.AgenteController.gridAction',
                                       'Error al consultar las asignaciones. '.$e->getMessage(),
                                       $strUsuario,
                                       $strIpCreacion );
        }
        $objJsonResponse->setData(['data'=>$arrSeguimientosGrafico]);
        return $objJsonResponse;
    }

    /**
     * 
     * Actualización: Se agrega registrar el cambio de estado de conexión en nueva tabla InfoAsignacionSolicitudReg
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 18-03-2020
     * 
     * Actualiza el estado de la conexión del usuario
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 23-10-2018
     * @since 1.0
     * @return JsonResponse
     */
    public function ajaxActualizaEstadoConexionAction()
    {
        $objRequest             = $this->getRequest();
        $objSesion              = $objRequest->getSession();
        $strEstado              = $objRequest->get('strEstadoConexion');
        $strExtension           = $objRequest->get('strExtension');
        $strUsrCreacion         = $objSesion->get('user');
        $intIdPersonaEmpresaRol = $objSesion->get('idPersonaEmpresaRol');
        $strIpCreacion          = $objRequest->getClientIp();
        $emComercial            = $this->getDoctrine()->getManager("telconet");
        $emSoporte              = $this->getDoctrine()->getManager("telconet_soporte");
        $emSoporte->beginTransaction();
        try
        {
            $serviceSoporteService = $this->get('soporte.SoporteService');
            $arrayParametros = array();
            $arrayParametros['intIdPersonaEmpresaRol'] = $intIdPersonaEmpresaRol;
            $arrayParametros['strValor']               = $strEstado;
            $arrayParametros['strUsrUltMod']           = $strUsrCreacion;
            $arrayParametros['dateFeUltMod']           = new \DateTime('now');
            $arrayParametros['strIpUltMod']            = $strIpCreacion;
            $arrayParametros['strCaracteristica']      = "ESTADO CONEXION MODULO ASIGNACIONES";
            $strResponseCaractConexion = $serviceSoporteService->modificarCaracteristicaConexionyExtension($arrayParametros);
            if ($strResponseCaractConexion === 'OK')
            {
                $objInfoAsignacionSolicitudReg = new InfoAsignacionSolicitudReg();
                $objInfoAsignacionSolicitudReg->setPersonaEmpresaRolId($intIdPersonaEmpresaRol);
                $objInfoAsignacionSolicitudReg->setEstadoConexion($strEstado);
                $objInfoAsignacionSolicitudReg->setExtension($strExtension);
                $objInfoAsignacionSolicitudReg->setFeConexion(new \DateTime('now'));
                $objInfoAsignacionSolicitudReg->setFeCreacion(new \DateTime('now'));
                $objInfoAsignacionSolicitudReg->setUsrCreacion($strUsrCreacion);
                $objInfoAsignacionSolicitudReg->setIpCreacion($strIpCreacion);
                $objInfoAsignacionSolicitudReg->setEstado('Activo');
                $emSoporte->persist($objInfoAsignacionSolicitudReg);
                $emSoporte->flush();
                $emSoporte->getConnection()->commit();
                $strResponse = "OK";
            }
            else
            {
                throw new \Exception($strResponseCaractConexion);
            }
        }
        catch(\Exception $e)
        {
            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
            $strResponse = "Ocurrio un error al cambiar estado de conexión, por favor consulte con el Administrador";
            error_log($strResponse);
            error_log($e->getMessage());
        }
        return new Response($strResponse);
    }

    /**
     * Actualiza la extensión telefónica del usuario
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 23-10-2018
     * @since 1.0
     * @return JsonResponse
     */
    public function ajaxActualizaExtensionAction()
    {
        $objRequest             = $this->getRequest();
        $objSesion              = $objRequest->getSession();
        $strExtension           = $objRequest->get('strExtension');
        $intIdPersonaEmpresaRol = $objRequest->get('idPersonaEmpresaRol');
        $strUsrCreacion         = $objSesion->get('user');
        $strIpCreacion          = $objRequest->getClientIp();
        $emComercial            = $this->getDoctrine()->getManager("telconet");

        try
        {
            $serviceSoporteService = $this->get('soporte.SoporteService');
            $arrayParametros = array();
            $arrayParametros['intIdPersonaEmpresaRol'] = $intIdPersonaEmpresaRol;
            $arrayParametros['strValor']               = $strExtension;
            $arrayParametros['strUsrUltMod']           = $strUsrCreacion;
            $arrayParametros['dateFeUltMod']           = new \DateTime('now');
            $arrayParametros['strIpUltMod']            = $strIpCreacion;
            $arrayParametros['strCaracteristica']      = "EXTENSION USUARIO";
            $strResponse = $serviceSoporteService->modificarCaracteristicaConexionyExtension($arrayParametros);
        }
        catch(\Exception $e)
        {
            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
            $strResponse = "Ocurrio un error al cambiar número de extensión, por favor consulte con el Administrador";
            error_log($strResponse);
            error_log($e->getMessage());
        }
        return new Response($strResponse);
    }

    /**
     * 
     * Actualización: Se agrega validación de que exista el agente antes de obtener estado de conexión
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.2 18-03-2020
     * 
     * Actualización: Se usa nueva función getEstadoConexionyExtension
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 04-12-2018
     *
     * Obtiene el estado de conexión del usuario
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 23-10-2018
     * @since 1.0
     * @return JsonResponse
     */
    public function ajaxObtenerEstadoConexionAction()
    {
        $objJsonResponse        = new JsonResponse();
        $objRequest             = $this->getRequest();
        $strUsrAgente           = $objRequest->get('strAgente');
        $objSesion              = $objRequest->getSession();
        $intIdPersonaEmpresaRol = $objSesion->get('idPersonaEmpresaRol');
        $strCodEmpresa          = $objSesion->get('idEmpresa');
        $emSoporte              = $this->getDoctrine()->getManager("telconet_soporte");
        $emComercial            = $this->getDoctrine()->getManager("telconet");
        try
        {
            $emSoporte->getConnection()->beginTransaction();

            //Valida que el agente exista antes de obtener estado de conexión
            if (isset($strUsrAgente) && $strUsrAgente != "")
            {
                $arrayAgente = $emComercial->getRepository("schemaBundle:InfoPersona")
                                           ->getPersonaDepartamentoPorUserEmpresa($strUsrAgente, $strCodEmpresa);
                if (empty($arrayAgente))
                {
                    throw new \Exception('No se encontró el agente');
                }
                else
                {
                    $intIdPersonaEmpresaRol = $arrayAgente['ID_PERSONA_EMPRESA_ROL'];
                }
            }
            $arrayRespuesta    = array();
            $arrayRespuesta['idPersonaEmpresaRol'] = $intIdPersonaEmpresaRol;
            $arrayRespuesta['estadoConexion']      = "";
            $arrayRespuesta['extensionUsuario']    = "";
            $arrayRespuesta['mensajeError']        = "";

            $arrayParametros["intPersonaRolId"] = $intIdPersonaEmpresaRol;

            $arrayResultado = $emSoporte->getRepository('schemaBundle:InfoAsignacionSolicitud')->getEstadoConexionyExtension($arrayParametros);

            if (count($arrayResultado)>0)
            {
                $arrayRespuesta['estadoConexion']      = $arrayResultado[0]['estadoConexion'];
                $arrayRespuesta['extensionUsuario']    = $arrayResultado[0]['extensionUsuario'];
            }
        }
        catch(\Exception $e)
        {
            $emSoporte->getConnection()->rollback();
            $emSoporte->getConnection()->close();
            $arrayRespuesta['mensajeError'] = "Ocurrio un error al leer estado de conexión o extensión, por favor consulte con el Administrador";
            error_log("ERROR AL LEER CARACTERISTICA DE CONEXION O EXTENSION EN MODULO AGENTE");
            error_log($e->getMessage());
        }
        $objJsonResponse->setData(['data'=>$arrayRespuesta]);
        return $objJsonResponse;
    }

    /**
     * Se agrega programación para modificar el campo tabVisible de la asignación
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.3 28-05-2020
     * 
     * Actualización: Se agrega programación para permitir modificar el tipo de problema
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.2 20-02-2019
     * 
     * Actualización: Se permite modificar campo Contacto Reporta, Contacto en Sitio y Datos adicionales
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 05-12-2018
     *
     * modifica una asignación
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 23-11-2018
     * @since 1.0
     * @return JsonResponse
     */
    public function ajaxModificarAsignacionAction()
    {
        $objRequest         = $this->getRequest();
        $objSesion          = $objRequest->getSession();
        $intAsignacionSolId = $objRequest->get('intId');
        $strValor           = $objRequest->get('strValor');
        $strTipo            = $objRequest->get('strTipo');
        $strEstado          = $objRequest->get('strEstado');
        $strUsrUltMod       = $objSesion->get('user');
        $strIpCreacion      = $objRequest->getClientIp();
        $emSoporte          = $this->getDoctrine()->getManager("telconet_soporte");

        try
        {
            $emSoporte->getConnection()->beginTransaction();

            $entityInfoAsignacionSolicitud = $emSoporte->getRepository("schemaBundle:InfoAsignacionSolicitud")->findOneById($intAsignacionSolId);
            if (is_object($entityInfoAsignacionSolicitud))
            {
                $arrayParametros['strNombreReporta'] = null;
                $arrayParametros['strNombreSitio']   = null;
                $arrayParametros['strDatoAdicional'] = null;
                $arrayParametros['strTabVisible']    = null;
                $arrayParametros['strEstado']        = null;
                $arrayParametros['intIdAsignacion']  = $intAsignacionSolId;
                $arrayParametros['strUsrUltMod']     = $strUsrUltMod;
                $arrayParametros['dateFeUltMod']     = new \DateTime('now');
                $arrayParametros['strIpUltMod']      = $strIpCreacion;
                if ($strTipo === 'nombreReporta')
                {
                    $arrayParametros['strNombreReporta'] = $strValor;
                }
                if ($strTipo === 'nombreSitio')
                {
                    $arrayParametros['strNombreSitio'] = $strValor;
                }
                if ($strTipo === 'datoAdicional')
                {
                    $arrayParametros['strDatoAdicional'] = $strValor;
                }
                if ($strTipo === 'estado')
                {
                    $arrayParametros['strEstado'] = $strEstado;
                }
                if ($strTipo === 'tipoProblema')
                {
                    $arrayParametros['strTipoProblema'] = $strValor;
                }
                if ($strTipo === 'tabVisible')
                {
                    $arrayParametros['strTabVisible'] = $strValor;
                }
                $objSoporteService = $this->get('soporte.SoporteService');
                $objSoporteService->modificarAsignacionSolicitud($arrayParametros);
                $emSoporte->getConnection()->commit();
                $emSoporte->getConnection()->close();
                $strResponse = 'OK';
            }
            else
            {
                throw new \Exception('No se encontro id de asignación para modificar');
            }

        }
        catch(\Exception $e)
        {
            $emSoporte->getConnection()->rollback();
            $emSoporte->getConnection()->close();
            $strResponse = "Ocurrio un error al modificar una asignación, por favor consulte con el Administrador";
            error_log("ERROR AL MODIFICAR LA ASIGNACION DE SOLICITUD");
            error_log($e->getMessage());
        }

        return new Response($strResponse);
    }

    /**
     * Actualización: Se permite modificar campo Contacto Reporta, Contacto en Sitio y Datos adicionales
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 05-12-2018
     *
     * modifica una asignación
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 23-11-2018
     * @since 1.0
     * @return JsonResponse
     */
    public function ajaxEnviaReporteAsignacionesPendAction()
    {
        $objRequest         = $this->getRequest();
        $objSesion          = $objRequest->getSession();
        $strUsuario         = $objSesion->get('user');
        $strCodEmpresa      = $objSesion->get('idEmpresa');
        $emSoporte          = $this->getDoctrine()->getManager("telconet_soporte");
        $emComercial        = $this->getDoctrine()->getManager("telconet");
        $intIdDepartamento  = "";
        $intIdCanton        = "";
        try
        {
            $strUserDbSoporte       = $this->container->getParameter('user_soporte');
            $strPasswordDbSoporte   = $this->container->getParameter('passwd_soporte');
            $strDatabaseDsn         = $this->container->getParameter('database_dsn');
            
            $arrayEmpleado          = $emComercial->getRepository("schemaBundle:InfoPersona")
                                                  ->getPersonaDepartamentoPorUserEmpresa($strUsuario, $strCodEmpresa);
            if (!empty($arrayEmpleado))
            {
                $intIdDepartamento     = $arrayEmpleado['ID_DEPARTAMENTO'];
                $intIdCanton           = $arrayEmpleado['ID_CANTON'];
            }

            //Se consulta si el tiene perfil asignado para ver todos los empleados de todos los cantones del departamento
            if (true === $this->get('security.context')->isGranted('ROLE_416-6077'))
            {
                $intIdCanton = null;
            }
            $arrayParametros = array();
            $arrayParametros['strCodEmpresa']        = $strCodEmpresa;
            $arrayParametros['intDepartamentoId']    = $intIdDepartamento;
            $arrayParametros['intCantonId']          = $intIdCanton;
            $arrayParametros['strUserDbSoporte']     = $strUserDbSoporte;
            $arrayParametros['strPasswordDbSoporte'] = $strPasswordDbSoporte;
            $arrayParametros['strDatabaseDsn']       = $strDatabaseDsn;

           $arrayRespuesta = $emSoporte->getRepository("schemaBundle:InfoAsignacionSolicitud")
                                        ->getReporteAsignacionesPendientes($arrayParametros);
            if (!empty($arrayRespuesta))
            {
                $strResponse = $arrayRespuesta['strRespuesta'];
            }
            else
            {
                $strResponse = "Ocurrio un error al enviar correo";
            }

        }
        catch(\Exception $e)
        {
            $emSoporte->getConnection()->rollback();
            $emSoporte->getConnection()->close();
            $strResponse = "Ocurrio un error al enviar correo";
            error_log($e->getMessage());
        }

        return new Response($strResponse);
    }

    /**
     * Permite obtener el último agente al que se le creo una asignación
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 21-02-2019
     * @since 1.0
     * @return JsonResponse
     */
    public function ajaxObtenerUltimoAgenteAsignadoAction()
    {
        $objRequest         = $this->getRequest();
        $objSesion          = $objRequest->getSession();
        $strUsuario         = $objSesion->get('user');
        $strCodEmpresa      = $objSesion->get('idEmpresa');
        $serviceUtil        = $this->get('schema.Util');
        $strIpCreacion      = $objRequest->getClientIp();
        $emSoporte          = $this->getDoctrine()->getManager("telconet_soporte");
        $emComercial        = $this->getDoctrine()->getManager("telconet");
        $intIdDepartamento  = "";
        $intIdCanton        = "";
        try
        {
            $strUserDbSoporte       = $this->container->getParameter('user_soporte');
            $strPasswordDbSoporte   = $this->container->getParameter('passwd_soporte');
            $strDatabaseDsn         = $this->container->getParameter('database_dsn');
            $arrayEmpleado          = $emComercial->getRepository("schemaBundle:InfoPersona")
                                                  ->getPersonaDepartamentoPorUserEmpresa($strUsuario, $strCodEmpresa);
            if (!empty($arrayEmpleado))
            {
                $intIdDepartamento     = $arrayEmpleado['ID_DEPARTAMENTO'];
                $intIdCanton           = $arrayEmpleado['ID_CANTON'];
            }
            //Se consulta si el tiene perfil asignado para ver todos los empleados de todos los cantones del departamento
            if (true === $this->get('security.context')->isGranted('ROLE_416-6077'))
            {
                $intIdCanton = null;
            }
            $arrayParametros = array();
            $arrayParametros['strCodEmpresa']        = $strCodEmpresa;
            $arrayParametros['intIdDepartamento']    = $intIdDepartamento;
            $arrayParametros['intIdCanton']          = $intIdCanton;
            $arrayParametros['strUserDbSoporte']     = $strUserDbSoporte;
            $arrayParametros['strPasswordDbSoporte'] = $strPasswordDbSoporte;
            $arrayParametros['strDatabaseDsn']       = $strDatabaseDsn;

           $arrayRespuesta = $emSoporte->getRepository("schemaBundle:InfoAsignacionSolicitud")
                                        ->getUltimoAgenteAsignado($arrayParametros);
            if (!empty($arrayRespuesta))
            {
                $strResponse = $arrayRespuesta['strRespuesta'];
            }
            else
            {
                $strResponse = "Ocurrio un error al buscar último agente asignado.";
            }
        }
        catch(\Exception $e)
        {
            $strResponse = "Ocurrio un error al buscar último agente asignado.";
            error_log($e->getMessage());
            $serviceUtil->insertError( 'Telcos+',
                                       'SoporteBundle.AgenteController.ajaxObtenerUltimoAgenteAsignadoAction',
                                       'Error al consultar último asignado => '.$e->getMessage(),
                                       $strUsuario,
                                       $strIpCreacion );
        }
        return new Response($strResponse);
    }

    /**
     * Actualización: Se realiza validaciones para consultar información adicional de las tareas solo para usuarios que tengan el
     *                perfil verNuevosCamposTareas
     * @author Fernando López <filopez@telconet.ec>
     * @version 1.2 24-01-2022
     * 
     * Actualización: Obtener información adicional de tarea para gestionarla desde agente
     * @author Fernando López <filopez@telconet.ec>
     * @version 1.1 16-11-2021
     * 
     * Obtiene asignaciones totalizado por estado
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 22-02-2019
     * @since 1.0
     *
     * Actualización: Se añaden campos adicionales
     * @author Francisco Cueva <facueva@telconet.ec>
     * @version 1.0 08-07-2021
     *
     * @return JsonResponse
     */
    public function gridTareasPendientesDepartamentoAction()
    {
        $objJsonResponse          = new JsonResponse();
        $objEmSoporte             = $this->getDoctrine()->getManager('telconet_soporte');
        $objServiceUtil           = $this->get('schema.Util');
        $objEmGeneral                = $this->getDoctrine()->getManager('telconet_general');
        $objRequest               = $this->get('request');
        $objSession               = $objRequest->getSession();
        $strIpCreacion            = $objRequest->getClientIp();
        $strUsuario               = $objSession->get('user');
        $strCodEmpresa            = $objSession->get('idEmpresa');
        $intIdOfina               = $objSession->get('idOficina');
        $intIdDepartamento        = $objSession->get('idDepartamento');
        $intPersonaEmpresaRol     = $objSession->get('idPersonaEmpresaRol');
        $strUserDbSoporte         = $this->container->getParameter('user_soporte');
        $strPasswordDbSoporte     = $this->container->getParameter('passwd_soporte');
        $strDatabaseDsn           = $this->container->getParameter('database_dsn');
        $arrayTareas              = array();

        $strFechaInicio          = $objRequest->get('fecha');
        $strFechaFin             = $objRequest->get('fechaFin');
        $booleanRegistroActivos  = $this->get('security.context')->isGranted('ROLE_197-6779');

        //Rol que permite ver as nuevas acciones en agente para gestionar la tarea
        $booleanPermiteVerNuevosCamposTareas  = $this->get('security.context')->isGranted('ROLE_416-8217');

        try
        {
            //Consultamos los seguimientos por usuario
            $arrayParametros                          = array();
            $arrayParametros['strUserDbSoporte']      = $strUserDbSoporte;
            $arrayParametros['strPasswordDbSoporte']  = $strPasswordDbSoporte;
            $arrayParametros['strDatabaseDsn']        = $strDatabaseDsn;
            $arrayParametros['strCodEmpresa']         = $strCodEmpresa;
            $arrayParametros['intIdDepartamento']     = $intIdDepartamento;
            $arrayParametros['intIdOficina']          = $intIdOfina;
            $arrayParametros['permiteVerNuevosCamposTareas']          = $booleanPermiteVerNuevosCamposTareas;
            $arrayParametros['fechaInicio']          = !is_null($strFechaInicio)?date('Y-m-d', strtotime($strFechaInicio)):'';
            $arrayParametros['fechaFin']             = !is_null($strFechaFin)?date('Y-m-d', strtotime($strFechaFin)):'';
            $objCursor                                = $objEmSoporte->getRepository("schemaBundle:InfoAsignacionSolicitud")
                                                                     ->getTareasPendientesDepartamento($arrayParametros);
            
            $strIdsTareasNoReqActivos = "";

            $arrayIdTareasNoReqActivo 	= $objEmSoporte->getRepository('schemaBundle:AdmiParametroDet') 
                                                    ->getOne('IDS_TAREAS_NO_REG_ACTIVOS','','','','','','','');

            if (is_array($arrayIdTareasNoReqActivo))
            {
                $strIdsTareasNoReqActivos = !empty($arrayIdTareasNoReqActivo['valor1']) ? $arrayIdTareasNoReqActivo['valor1'] : "";
            }

            if( !empty($objCursor) )
            {
                $intI=0;
                while( $arrayResultadoCursor = oci_fetch_array($objCursor, OCI_ASSOC + OCI_RETURN_NULLS) )
                {
                    $strFecha            = ( isset($arrayResultadoCursor['FE_TAREA_CREADA'])
                                        && !empty($arrayResultadoCursor['FE_TAREA_CREADA']) )
                                        ? $arrayResultadoCursor['FE_TAREA_CREADA'] : '';
                    $intNumeroTarea      = ( isset($arrayResultadoCursor['ID_COMUNICACION'])
                                        && !empty($arrayResultadoCursor['ID_COMUNICACION']) )
                                        ? $arrayResultadoCursor['ID_COMUNICACION'] : '';
                    $strDepAsignado      = ( isset($arrayResultadoCursor['ASIGNADO_NOMBRE'])
                                        && !empty($arrayResultadoCursor['ASIGNADO_NOMBRE']) )
                                        ? $arrayResultadoCursor['ASIGNADO_NOMBRE'] : '';
                    $strEstado           = ( isset($arrayResultadoCursor['ESTADO'])
                                        && !empty($arrayResultadoCursor['ESTADO']) )
                                        ? $arrayResultadoCursor['ESTADO'] : '';
                    $strNombreTarea      = ( isset($arrayResultadoCursor['NOMBRE_TAREA'])
                                        && !empty($arrayResultadoCursor['NOMBRE_TAREA']) )
                                        ? $arrayResultadoCursor['NOMBRE_TAREA'] : '';
                    $strLogin            = ( isset($arrayResultadoCursor['AFECTADOS'])
                                        && !empty($arrayResultadoCursor['AFECTADOS']) )
                                        ? $arrayResultadoCursor['AFECTADOS'] : '';
                    $strUsrAsignado      = ( isset($arrayResultadoCursor['USR_ASIGNADO'])
                                        && !empty($arrayResultadoCursor['USR_ASIGNADO']) )
                                        ? $arrayResultadoCursor['USR_ASIGNADO'] : '';
                    $strObservacionTarea = ( isset($arrayResultadoCursor['OBSERVACION'])
                                        && !empty($arrayResultadoCursor['OBSERVACION']) )
                                        ? $arrayResultadoCursor['OBSERVACION'] : '';
                    $strUsrCreacion      = ( isset($arrayResultadoCursor['USRCREACIONDETALLE'])
                                        && !empty($arrayResultadoCursor['USRCREACIONDETALLE']) )
                                        ? $arrayResultadoCursor['USRCREACIONDETALLE'] : '';
                    $strNombreDepto      = ( isset($arrayResultadoCursor['NOMBRE_DEPARTAMENTO'])
                                        && !empty($arrayResultadoCursor['NOMBRE_DEPARTAMENTO']) )
                                        ? $arrayResultadoCursor['NOMBRE_DEPARTAMENTO'] : '';  
                    $strNombreProceso    = ( isset($arrayResultadoCursor['NOMBRE_PROCESO'])
                                        && !empty($arrayResultadoCursor['NOMBRE_PROCESO']) )
                                        ? $arrayResultadoCursor['NOMBRE_PROCESO'] : '';  
                    $strNombreEmpresa    = ( isset($arrayResultadoCursor['NOMBRE_EMPRESA'])
                                        && !empty($arrayResultadoCursor['NOMBRE_EMPRESA']) )
                                        ? $arrayResultadoCursor['NOMBRE_EMPRESA'] : '';                                             
                    $strUltimoDeptoAsig  = ( isset($arrayResultadoCursor['ULTIMO_DTO_ASIG'])
                                        && !empty($arrayResultadoCursor['ULTIMO_DTO_ASIG']) )
                                        ? $arrayResultadoCursor['ULTIMO_DTO_ASIG'] : ''; 
                    $strSisUltimoUsrAsig = ( isset($arrayResultadoCursor['ULTIMO_USR_ASIG'])
                                        && !empty($arrayResultadoCursor['ULTIMO_USR_ASIG']) )
                                        ? $arrayResultadoCursor['ULTIMO_USR_ASIG'] : ''; 
                    $strTrazabilidad     = ( isset($arrayResultadoCursor['TRAZABILIDAD'])
                                        && !empty($arrayResultadoCursor['TRAZABILIDAD']) )
                                        ? $arrayResultadoCursor['TRAZABILIDAD'] : '';

                    $strFechaCreacionTarea  = "";
                    $strMinutos= "";
                    $intCasoId = 0; 
                    $strIniciadaDesdeMobil = "S";
                    $strPerteneceACaso = false;
                    $strCasoPerteneceTn = false;
                    $objFechaEjecucion = "";
                    $strHoraEjecucion  = "";
                    $intIdDetalle = "";
                    $strNombreUsrAsignado = "";
                    $intMinutos = "";
                    $intIdDetalleHist = "";
                    $strCerrarTarea = 'S';
                    $strEstadoHist = "";
                    $intDepartamentoId ="";
                    $intAsignadoId  ="";
                    $strTipoAsignado ="";
                    $strNombreTareaAnterior ="";
                    $intIdTareaAnterior ="";
                    $intIdTarea ="";
                    $strBanderaFinalizarInformeEjecutivo = 'S';
                    $booleanMostrarInfoAdicional= false;
                    $booleanEsInterdep          = true;    
                    
                    //Validación para extraer información de tarae solo para usuarios que tengan el rol de ver nuevos campos
                    if(isset($booleanPermiteVerNuevosCamposTareas) && $booleanPermiteVerNuevosCamposTareas)
                    {
                        $intIdTarea     = ( isset($arrayResultadoCursor['IDTAREA'])
                                        && !empty($arrayResultadoCursor['IDTAREA']) )
                                        ? $arrayResultadoCursor['IDTAREA'] : '';
                        $intAsignadoId     = ( isset($arrayResultadoCursor['ASIGNADO_ID'])
                                            && !empty($arrayResultadoCursor['ASIGNADO_ID']) )
                                            ? $arrayResultadoCursor['ASIGNADO_ID'] : '';
                        $strTipoAsignado     = ( isset($arrayResultadoCursor['TIPO_ASIGNADO'])
                                                    && !empty($arrayResultadoCursor['TIPO_ASIGNADO']) )
                                                    ? $arrayResultadoCursor['TIPO_ASIGNADO'] : '';                                              
                        $intIdDetalle      = ( isset($arrayResultadoCursor['IDDETALLE'])
                                                    && !empty($arrayResultadoCursor['IDDETALLE']) )
                                                    ? (int)$arrayResultadoCursor['IDDETALLE'] : '';
                        $intIdDetalleHist      = ( isset($arrayResultadoCursor['IDDETALLEHIST'])
                                                    && !empty($arrayResultadoCursor['IDDETALLEHIST']) )
                                                    ? (int)$arrayResultadoCursor['IDDETALLEHIST'] : '';
                        $strEstadoHist      = ( isset($arrayResultadoCursor['ESTADOHIST'])
                                                    && !empty($arrayResultadoCursor['ESTADOHIST']) )
                                                    ? $arrayResultadoCursor['ESTADOHIST'] : '';
                        $strFechaCreaHist      = ( isset($arrayResultadoCursor['FECHACREAHIST'])
                                                    && !empty($arrayResultadoCursor['FECHACREAHIST']) )
                                                    ? $arrayResultadoCursor['FECHACREAHIST'] : '';
                        $strNombreUsrAsignado      = ( isset($arrayResultadoCursor['REF_ASIGNADO_NOMBRE'])
                                                    && !empty($arrayResultadoCursor['REF_ASIGNADO_NOMBRE']) )
                                                    ? $arrayResultadoCursor['REF_ASIGNADO_NOMBRE'] : '';
                        $intNumeroTareaPadre      = ( isset($arrayResultadoCursor['NUMERO_TAREA_PADRE'])
                                                    && !empty($arrayResultadoCursor['NUMERO_TAREA_PADRE']) )
                                                    ? $arrayResultadoCursor['NUMERO_TAREA_PADRE'] : '';
                        $strPermiteFinalizar     = ( isset($arrayResultadoCursor['PERMITE_FINALIZAR_INFORME'])
                                                    && !empty($arrayResultadoCursor['PERMITE_FINALIZAR_INFORME']) )
                                                    ? $arrayResultadoCursor['PERMITE_FINALIZAR_INFORME'] : '';
                        $objFechaEjecucion     = ( isset($arrayResultadoCursor['FECHA_EJECUCION'])
                                                    && !empty($arrayResultadoCursor['FECHA_EJECUCION']) )
                                                    ? $arrayResultadoCursor['FECHA_EJECUCION'] : '';
                        $intIdTareaAnterior    = ( isset($arrayResultadoCursor['ID_TAREA_ANTERIOR'])
                                                    && !empty($arrayResultadoCursor['ID_TAREA_ANTERIOR']) )
                                                    ? $arrayResultadoCursor['ID_TAREA_ANTERIOR'] : "";
                        $strNombreTareaAnterior    = ( isset($arrayResultadoCursor['NOMBRE_TAREA_ANTERIOR'])
                                                    && !empty($arrayResultadoCursor['NOMBRE_TAREA_ANTERIOR']) )
                                                    ? $arrayResultadoCursor['NOMBRE_TAREA_ANTERIOR'] : "";
                        $intCasoId     = ( isset($arrayResultadoCursor['IDCASO'])
                                                    && !empty($arrayResultadoCursor['IDCASO']) )
                                                    ? $arrayResultadoCursor['IDCASO'] : 0;
                        $intCasoEmpresaCod     = ( isset($arrayResultadoCursor['CASO_EMPRESA_COD'])
                                                    && !empty($arrayResultadoCursor['CASO_EMPRESA_COD']) )
                                                    ? $arrayResultadoCursor['CASO_EMPRESA_COD'] : "";
                        $intDepartamentoId     = ( isset($arrayResultadoCursor['DEPARTAMENTO_ID'])
                                                    && !empty($arrayResultadoCursor['DEPARTAMENTO_ID']) )
                                                    ? (int)$arrayResultadoCursor['DEPARTAMENTO_ID'] : "";
                        $strTareaInfoAdicional     = ( isset($arrayResultadoCursor['TAREA_INFO_ADICIONAL'])
                                                    && !empty($arrayResultadoCursor['TAREA_INFO_ADICIONAL']) )
                                                    ? $arrayResultadoCursor['TAREA_INFO_ADICIONAL'] : "";
                        $strFeCreacionTareaAceptada     = ( isset($arrayResultadoCursor['FECHA_TAREA_CREACION'])
                                                    && !empty($arrayResultadoCursor['FECHA_TAREA_CREACION']) )
                                                    ? $arrayResultadoCursor['FECHA_TAREA_CREACION'] : "";
                        $intValorTiempoPausa     = ( isset($arrayResultadoCursor['VALOR_TIEMPO_PAUSA'])
                                                        && !empty($arrayResultadoCursor['VALOR_TIEMPO_PAUSA']) )
                                                        ? $arrayResultadoCursor['VALOR_TIEMPO_PAUSA'] : "";
                        $strFeCreacionReanuda     = ( isset($arrayResultadoCursor['FECHA_CREACION_REANUDA'])
                                                        && !empty($arrayResultadoCursor['FECHA_CREACION_REANUDA']) )
                                                        ? $arrayResultadoCursor['FECHA_CREACION_REANUDA'] : "";

                        

                        // Validar si la persona en session puede finalizar la tarea de generacion de informe ejecutivo
                        $strCerrarTarea  = $intNumeroTareaPadre==='N'?'N':'S';
                        $strBanderaFinalizarInformeEjecutivo = ($strPermiteFinalizar === 'N')?'N':'S';

                        // obtener valor para la consulta de archivo
                        $booleanMostrarInfoAdicional = ($strTareaInfoAdicional !== '')?true:false; 

                        $strPerteneceACaso = ($intCasoId !== 0)?true:false;

                        $strCasoPerteneceTn = ($intCasoEmpresaCod == "10")?true:false;

                        if($objFechaEjecucion != "")
                        {
                            $arrayFecha        = explode(" ", $objFechaEjecucion);
                            $arrayFech         = explode("-", $arrayFecha[0]);
                            $arrayHora         = explode(":", $arrayFecha[1]);
                            $objFechaEjecucion = $arrayFech[2] . "-" . $arrayFech[1] . "-" . $arrayFech[0];
                            $strHoraEjecucion  = $arrayHora[0] . ":" . $arrayHora[1];
                        }

                        if($strEstado == 'Asignada')
                        {
                            $strFechaCreacionTarea = new \DateTime($strFecha);
                        }
                        else
                        {
                            if($strFeCreacionTareaAceptada != "")
                            {
                                $strFechaCreacionTarea      = new \DateTime($strFeCreacionTareaAceptada);
                            }
                        }

                        if( ($strEstado == 'Cancelada' || $strEstado == 'Finalizada' || $strEstado == 'Rechazada' || $strEstado == 'Anulada') 
                                && isset($strFechaCreaHist) && $strFechaCreaHist !=='')
                        {
                            $objDatetimeFinal = new \DateTime($strFechaCreaHist);
                        }
                        else
                        {
                            $objDatetimeFinal = new \DateTime();
                        }

                        if(is_object($strFechaCreacionTarea))
                        {
                            $objDatetimeDiferenciaFechas = $objDatetimeFinal->diff($strFechaCreacionTarea);
                            $intMinutos  = $objDatetimeDiferenciaFechas->days * 24 * 60;
                            $intMinutos += $objDatetimeDiferenciaFechas->h * 60;
                            $intMinutos += $objDatetimeDiferenciaFechas->i;
                        }
                        $strMinutos  = $intMinutos.' minutos';

                        if($strEstado == "Pausada")
                        {
                            if(isset($intValorTiempoPausa) && $intValorTiempoPausa !== '')
                            {
                                $strMinutos = $intValorTiempoPausa . ' minutos';
                            }
                        }
                        else if($strEstado <> 'Cancelada' && $strEstado <> 'Finalizada' && $strEstado <> 'Rechazada' 
                        && isset($strFeCreacionReanuda) && $strFeCreacionReanuda !== '')
                        {
                            $objDateFechaReanudada       = new \DateTime($strFeCreacionReanuda);
                            $objDateFechaActual          = new \DateTime();
                            $objDatetimeDiferenciaFechas = $objDateFechaActual->diff($objDateFechaReanudada);

                            $intMinutos = $objDatetimeDiferenciaFechas->days * 24 * 60;
                            $intMinutos += $objDatetimeDiferenciaFechas->h * 60;
                            $intMinutos += $objDatetimeDiferenciaFechas->i;

                            if(isset($intValorTiempoPausa) && $intValorTiempoPausa !== '')
                            {
                                $intTiempoTareaPausada = $intValorTiempoPausa;
                            }

                            $strMinutos = $intMinutos + $intTiempoTareaPausada;
                            $strMinutos = $strMinutos.' minutos';
                           
                        }

                        $intMinutos = substr($strMinutos,0,-8);

                        $arrayIdsTareasNoReqActivo = explode (",", $strIdsTareasNoReqActivos);  

                        if(in_array($intIdTarea,$arrayIdsTareasNoReqActivo) || $intCasoId != 0)
                        {
                            $booleanEsInterdep = false;
                        }

                    }
                    
                    $arrayTareas[]       = array(
                        "numero"           => ($intI+1),
                        "feCreacion"       => $strFecha,
                        "numeroTarea"      => $intNumeroTarea,
                        "loginAfectado"    => $strLogin,
                        "usrAsignado"      => $strUsrAsignado,
                        "asignado"         => $strDepAsignado,
                        "nombreTarea"      => $strNombreTarea,
                        "observacionTarea" => str_replace('*fff','"',$strObservacionTarea),
                        "estado"           => $strEstado,
                        "usrCreacion"      => $strUsrCreacion,
                        "departamentoCrea" => $strNombreDepto,
                        "nombreProceso"    => $strNombreProceso,
                        "trazabilidadTareaCrea" => $strTrazabilidad,
                        "empresaTarea"     => $strNombreEmpresa,
                        "ultimoDeptoAsig"  => $strUltimoDeptoAsig,
                        "sisUltimoUsrAsig" => $strSisUltimoUsrAsig,
                        "acciones"         => "",
                        "minutosTranscurridos" => $strMinutos,
                        "idDetalle" => $intIdDetalle,
                        "idPersonaEmpresaRol" => $intPersonaEmpresaRol,
                        "idDetalleHist" => $intIdDetalleHist,
                        "nombreUsrAsignado" => $strNombreUsrAsignado,
                        "EstadoHist" => $strEstadoHist,
                        "strBanderaFinalizarInformeEjecutivo" => $strBanderaFinalizarInformeEjecutivo,
                        "fechaEjecucion"         => $objFechaEjecucion,
                        "horaEjecucion"          => $strHoraEjecucion,
                        "duracionMinutos"        => $intMinutos,
                        "permiteRegistroActivos" => $booleanRegistroActivos,
                        "id_caso"                => $intCasoId,
                        "esInterdepartamental"   => $booleanEsInterdep,
                        "departamentoId"         => $intDepartamentoId,
                        "asignado_id"            => $intAsignadoId,
                        "tipoAsignado"           => $strTipoAsignado,
                        "cerrarTarea"            => $strCerrarTarea,
                        "iniciadaDesdeMobil"     => $strIniciadaDesdeMobil,
                        "perteneceCaso"          => $strPerteneceACaso,
                        "casoPerteneceTN"        => $strCasoPerteneceTn,
                        "nombreTareaAnterior"   =>  ($strNombreTareaAnterior != '')?$strNombreTareaAnterior: 
                                                    ($strNombreTarea? $strNombreTarea:"N/A"),
                        "idTareaAnterior"       =>  ($intIdTareaAnterior != '')?$intIdTareaAnterior:$intIdTarea,
                        "asignado_nombre"        => $strDepAsignado,
                        "ref_asignado_nombre"    => $strNombreUsrAsignado,
                        "nombre_proceso"         => $strNombreProceso,
                        "clientes"               => $strLogin,
                        "duracionTarea"          => $strMinutos,
                        "id_tarea"               => $intIdTarea,
                        "intIdDetalleHist"       => $intIdDetalleHist,
                        "strTareaIncAudMant"     => $booleanMostrarInfoAdicional ? 'S' : 'N',
                        'strUsuarioSession'      => $strUsuario                            
                        );
                    
                    $intI++;
                }
            }
        }
        catch(\Exception $e)
        {
            $objServiceUtil->insertError( 'Telcos+',
                                          'SoporteBundle.AgenteController.obtieneTareasPendientesAction',
                                          'Error al consultar las tareas del departamento. '.$e->getMessage(),
                                          $strUsuario,
                                          $strIpCreacion );
        }
        $objJsonResponse->setData(['data'=>$arrayTareas]);
        return $objJsonResponse;
    }

    /**
     * 
     * Se agrega programación para consultar top de logins por estado de asignación.
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.2 28-05-2020
     * 
     * Actualización: Se recibe parametro canton para consultar por ciudad si es requerido
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 20-04-2020
     * 
     * Obtiene asignaciones totalizado por estado
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 22-02-2019
     * @since 1.0
     * @return JsonResponse
     */
    public function obtieneTopLoginsAction()
    {
        $objJsonResponse          = new JsonResponse();
        $objEmSoporte             = $this->getDoctrine()->getManager('telconet_soporte');
        $objServiceUtil           = $this->get('schema.Util');
        $objRequest               = $this->get('request');
        $objSession               = $objRequest->getSession();
        $strIpCreacion            = $objRequest->getClientIp();
        $strFechaIni              = $objRequest->get('fechaIni');
        $strFechaFin              = $objRequest->get('fechaFin');
        $intIdCantonConsulta      = $objRequest->get('idCanton');
        $strEstado                = $objRequest->get('estado');
        $strUsuario               = $objSession->get('user');
        $strCodEmpresa            = $objSession->get('idEmpresa');
        $intIdCanton              = $objSession->get('intIdCanton');
        $intIdDepartamento        = $objSession->get('idDepartamento');
        $strUserDbSoporte         = $this->container->getParameter('user_soporte');
        $strPasswordDbSoporte     = $this->container->getParameter('passwd_soporte');
        $strDatabaseDsn           = $this->container->getParameter('database_dsn');
        $arrayResultado           = array();

        try
        {
            //Se consulta si se tiene perfil asignado para ver todos los empleados de todos los cantones
            if (true === $this->get('security.context')->isGranted('ROLE_416-6077'))
            {
                $intIdCanton = $intIdCantonConsulta;
            }

            //Consultamos el top de logins
            $arrayParametros                          = array();
            $arrayParametros['strUserDbSoporte']      = $strUserDbSoporte;
            $arrayParametros['strPasswordDbSoporte']  = $strPasswordDbSoporte;
            $arrayParametros['strDatabaseDsn']        = $strDatabaseDsn;
            $arrayParametros['strCodEmpresa']         = $strCodEmpresa;
            $arrayParametros['intIdDepartamento']     = $intIdDepartamento;
            $arrayParametros['intIdCanton']           = $intIdCanton;
            $arrayParametros['strEstado']             = $strEstado;
            $arrayParametros['strFechaIni']           = $strFechaIni;
            $arrayParametros['strFechaFin']           = $strFechaFin;
            $objCursor                                = $objEmSoporte->getRepository("schemaBundle:InfoAsignacionSolicitud")
                                                                     ->getTopLogins($arrayParametros);
            if( !empty($objCursor) )
            {
                $arrayTareas = array();
                $arrayCasos  = array();
                while( $arrayResultadoCursor = oci_fetch_array($objCursor, OCI_ASSOC + OCI_RETURN_NULLS) )
                {
                    $strLogin            = ( isset($arrayResultadoCursor['AFECTADO'])
                                           && !empty($arrayResultadoCursor['AFECTADO']) )
                                           ? $arrayResultadoCursor['AFECTADO'] : '';
                    $strTipoAtencion     = ( isset($arrayResultadoCursor['TIPO_ATENCION'])
                                           && !empty($arrayResultadoCursor['TIPO_ATENCION']) )
                                           ? $arrayResultadoCursor['TIPO_ATENCION'] : '';
                    $intCantidad         = ( isset($arrayResultadoCursor['CANTIDAD'])
                                           && !empty($arrayResultadoCursor['CANTIDAD']) )
                                           ? $arrayResultadoCursor['CANTIDAD'] : '';
                    if ($strTipoAtencion === "TAREA")
                    {
                        $arrayTareas[] = array(
                                               "name" => $strLogin,
                                               "y"    => intval($intCantidad)
                                              );
                    }
                    elseif ($strTipoAtencion === "CASO")
                    {
                        $arrayCasos[] = array(
                                              "name" => $strLogin,
                                              "y"    => intval($intCantidad)
                                             );
                    }
                }
                $arrayResultado[] = array("name" => "TAREAS", "color" => "#6B9B37", "data" => $arrayTareas);
                $arrayResultado[] = array("name" => "CASOS",  "color" => "#C88719", "data" => $arrayCasos );
            }
        }
        catch(\Exception $e)
        {
            $objServiceUtil->insertError( 'Telcos+',
                                          'SoporteBundle.AgenteController.obtieneTareasPendientesAction',
                                          'Error al consultar las tareas del departamento. '.$e->getMessage(),
                                          $strUsuario,
                                          $strIpCreacion );
        }
        $objJsonResponse->setData($arrayResultado);
        return $objJsonResponse;
    }

    /**
     * 
     * Se agrega opción para consultar parametros de estado y hora.
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 28-05-2020
     *
     * Metodo que retorna los cantones configurados en AdmiParametroDet para el módulo Agente
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 09-01-2020
     * @since 1.0
     * @return JsonResponse
     */
    public function ajaxObtenerParametrosAction()
    {
        $objJsonRespuesta     = new JsonResponse();
        $emSoporte            = $this->getDoctrine()->getManager('telconet_soporte');
        $serviceUtil          = $this->get('schema.Util');
        $objRequest           = $this->get('request');
        $objSession           = $objRequest->getSession();
        $strCodEmpresa        = $objSession->get('idEmpresa');
        $intIdDepartamento    = $objSession->get('idDepartamento');
        $strIpCreacion        = $objRequest->getClientIp();
        $strUsuario           = $objSession->get('user');
        $strTipoParametro     = $objRequest->get('tipo');
        $strParamValor1       = "";
        $strDescripcion       = "";
        $strOrdenamiento      = "valor3";

        if ($strTipoParametro === 'cantones')
        {
            $strDescripcion  = "CANTONES PARA MODULO ASIGNACIONES TN";
            $strOrdenamiento = "valor3";
        }
        elseif ($strTipoParametro === 'estados')
        {
            $strDescripcion = "ESTADOS PARA MODULO ASIGNACIONES TN";
            $strOrdenamiento = "valor3";
        }
        elseif ($strTipoParametro === 'horas')
        {
            $strParamValor1 = $intIdDepartamento;
            $strDescripcion = "HORAS CAMBIO DE TURNO PARA MODULO ASIGNACIONES TN";
            $strOrdenamiento = "descripcion";
        }
        $objJsonRespuesta->setData(['data'=>array()]);
        try
        {
            $arrayItems = $emSoporte->getRepository("schemaBundle:AdmiParametroDet")->get(
                                                                                             $strDescripcion,
                                                                                             "SOPORTE",
                                                                                             "",
                                                                                             "",
                                                                                             $strParamValor1,
                                                                                             "",
                                                                                             "",
                                                                                             "",
                                                                                             "",
                                                                                             $strCodEmpresa,
                                                                                             $strOrdenamiento
                                                                                         );
            $objJsonRespuesta->setData(['data'=>$arrayItems]);
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError( 'Telcos+',
                                       'SoporteBundle.AgenteController.ajaxObtenerCantonesAction',
                                       'Error al consultar los cantones. '.$e->getMessage(),
                                       $strUsuario,
                                       $strIpCreacion );
        }
        return $objJsonRespuesta;
    }

    /**
     * Actualiza datos del tipo de problema
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 11-03-2020
     * @since 1.0
     * @return JsonResponse
     */
    public function ajaxActualizaTipoProblemaParametrizadoAction()
    {
        $objRequest             = $this->getRequest();
        $objSesion              = $objRequest->getSession();
        $strEstado              = $objRequest->get('strEstadoConexion');
        $strUsrCreacion         = $objSesion->get('user');
        $intIdPersonaEmpresaRol = $objSesion->get('idPersonaEmpresaRol');
        $strIpCreacion          = $objRequest->getClientIp();
        $emComercial            = $this->getDoctrine()->getManager("telconet");

        try
        {
            $serviceSoporteService = $this->get('soporte.SoporteService');
            $arrayParametros = array();
            $arrayParametros['intIdPersonaEmpresaRol'] = $intIdPersonaEmpresaRol;
            $arrayParametros['strValor']               = $strEstado;
            $arrayParametros['strUsrUltMod']           = $strUsrCreacion;
            $arrayParametros['dateFeUltMod']           = new \DateTime('now');
            $arrayParametros['strIpUltMod']            = $strIpCreacion;
            $arrayParametros['strCaracteristica']      = "ESTADO CONEXION MODULO ASIGNACIONES";
            $strResponse = $serviceSoporteService->modificarCaracteristicaConexionyExtension($arrayParametros);
        }
        catch(\Exception $e)
        {
            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
            $strResponse = "Ocurrio un error al cambiar estado de conexión, por favor consulte con el Administrador";
            error_log($strResponse);
            error_log($e->getMessage());
        }
        return new Response($strResponse);
    }

   /**
     * Obtiene total de asignaciones sin numero de tarea o caso asociado
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 13-03-2020
     * @since 1.0
     * @return JsonResponse
     */
    public function ajaxObtenerTotalAsignacionesSinNumeroAction()
    {
        $objJsonResponse          = new JsonResponse();
        $objEmSoporte             = $this->getDoctrine()->getManager('telconet_soporte');
        $objServiceUtil           = $this->get('schema.Util');
        $objRequest               = $this->get('request');
        $objSession               = $objRequest->getSession();
        $strUsuario               = $objSession->get('user');
        $strCodEmpresa            = $objSession->get('idEmpresa');
        $intIdCanton               = $objSession->get('intIdCanton');
        $intIdDepartamento        = $objSession->get('idDepartamento');
        $strUserDbSoporte         = $this->container->getParameter('user_soporte');
        $strPasswordDbSoporte     = $this->container->getParameter('passwd_soporte');
        $strDatabaseDsn           = $this->container->getParameter('database_dsn');
        $arrayResultado           = array();

        try
        {
            //Consultamos el top de logins
            $arrayParametros                          = array();
            $arrayParametros['strUserDbSoporte']      = $strUserDbSoporte;
            $arrayParametros['strPasswordDbSoporte']  = $strPasswordDbSoporte;
            $arrayParametros['strDatabaseDsn']        = $strDatabaseDsn;
            $arrayParametros['strCodEmpresa']         = $strCodEmpresa;
            $arrayParametros['intIdDepartamento']     = $intIdDepartamento;
            $arrayParametros['intIdCanton']           = $intIdCanton;
            $arrayParametros['strUsrAsignado']        = $strUsuario;
            $intTotal                                 = $objEmSoporte->getRepository("schemaBundle:InfoAsignacionSolicitud")
                                                                     ->getTotalAsignacionesSinNumero($arrayParametros);

            $intTotal = isset($intTotal)?$intTotal:0;
            $arrayResultado[] = array("total" => $intTotal);
        }
        catch(\Exception $e)
        {
            $objServiceUtil->insertError( 'Telcos+',
                                          'SoporteBundle.AgenteController.obtieneTotalAsignacionesSinNumeroAction',
                                          'Error al consultar total asignaciones sin número asociado: '.$e->getMessage(),
                                          $strUsuario,
                                          $strIpCreacion );
        }
        $objJsonResponse->setData($arrayResultado);
        return $objJsonResponse;
    }

    /**
     * Obtiene el historial de conexión al módulo agente de un usuario
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 18-03-2020
     * @since 1.0
     * @return JsonResponse
     */
    public function ajaxObtenerRegistrosConexionUsrAction()
    {
        $objJsonResponse          = new JsonResponse();
        $objEmSoporte             = $this->getDoctrine()->getManager('telconet_soporte');
        $objServiceUtil           = $this->get('schema.Util');
        $objRequest               = $this->get('request');
        $strIpCreacion            = $objRequest->getClientIp();
        $intIdPersonaEmpresaRol   = $objRequest->get('idPer');
        $intMes                   = $objRequest->get('mes');
        $intAnio                  = $objRequest->get('anio');
        $strUserDbSoporte         = $this->container->getParameter('user_soporte');
        $strPasswordDbSoporte     = $this->container->getParameter('passwd_soporte');
        $strDatabaseDsn           = $this->container->getParameter('database_dsn');
        $arrayHistorial           = array();
        try
        {
            //Consultamos el top de logins
            $arrayParametros                           = array();
            $arrayParametros['strUserDbSoporte']       = $strUserDbSoporte;
            $arrayParametros['strPasswordDbSoporte']   = $strPasswordDbSoporte;
            $arrayParametros['strDatabaseDsn']         = $strDatabaseDsn;
            $arrayParametros['intIdPersonaEmpresaRol'] = $intIdPersonaEmpresaRol;
            $arrayParametros['intMes']                 = $intMes;
            $arrayParametros['intAnio']                = $intAnio;

            $objCursor                                 = $objEmSoporte->getRepository("schemaBundle:InfoAsignacionSolicitud")
                                                                      ->getRegistrosConexionUsr($arrayParametros);
            if( !empty($objCursor) )
            {
                while( $arrayResultadoCursor = oci_fetch_array($objCursor, OCI_ASSOC + OCI_RETURN_NULLS) )
                {
                    $strFecha            = ( isset($arrayResultadoCursor['FE_CONEXION'])
                                           && !empty($arrayResultadoCursor['FE_CONEXION']) )
                                           ? $arrayResultadoCursor['FE_CONEXION'] : '';
                    $strExtension        = ( isset($arrayResultadoCursor['EXTENSION'])
                                           && !empty($arrayResultadoCursor['EXTENSION']) )
                                           ? $arrayResultadoCursor['EXTENSION'] : '';
                    $strEstado           = ( isset($arrayResultadoCursor['ESTADO_CONEXION'])
                                           && !empty($arrayResultadoCursor['ESTADO_CONEXION']) )
                                           ? $arrayResultadoCursor['ESTADO_CONEXION'] : '';

                    $objFechaConexion       = date_create(str_replace(".000000", "", $strFecha));
                    $strFechaConexionFormat = date_format($objFechaConexion, 'd-m-y H:i');

                    $arrayHistorial[]    = array(
                                               "fecha"     => $strFechaConexionFormat,
                                               "estado"    => $strEstado,
                                               "extension" => $strExtension
                                              );
                }
            }
        }
        catch(\Exception $e)
        {
            $objServiceUtil->insertError( 'Telcos+',
                                          'SoporteBundle.AgenteController.ajaxObtenerHistorialConexionAction',
                                          'Error al consultar el historial de conexión. '.$e->getMessage(),
                                          $strUsuario,
                                          $strIpCreacion );
        }
        $objJsonResponse->setData(['data'=>$arrayHistorial]);
        return $objJsonResponse;
    }

    /**
     * Actualiza caracteristica de agente que este en sesión
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 19-04-2020
     * @since 1.0
     * @return JsonResponse
     */
    public function ajaxActualizaCaracteristicaAgenteAction()
    {
        $objRequest             = $this->getRequest();
        $objSesion              = $objRequest->getSession();
        $intIdPersonaEmpresaRol = $objSesion->get('idPersonaEmpresaRol');
        $strUsrCreacion         = $objSesion->get('user');
        $strValor               = $objRequest->get('strValor');
        $strCaracteristica      = $objRequest->get('strCarac');
        $strIpCreacion          = $objRequest->getClientIp();
        $emComercial            = $this->getDoctrine()->getManager("telconet");

        try
        {
            $servicePersonaEmpresaRolService = $this->get('comercial.InfoPersonaEmpresaRol');
            $arrayParametros = array();
            $arrayParametros['intIdPersonaEmpresaRol'] = $intIdPersonaEmpresaRol;
            $arrayParametros['strValor']               = $strValor;
            $arrayParametros['strUsrUltMod']           = $strUsrCreacion;
            $arrayParametros['dateFeUltMod']           = new \DateTime('now');
            $arrayParametros['strIpUltMod']            = $strIpCreacion;
            $arrayParametros['strCaracteristica']      = $strCaracteristica;
            $strResponse = $servicePersonaEmpresaRolService->crearPersonaEmpresaRolCarac($arrayParametros);
        }
        catch(\Exception $e)
        {
            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
            $strResponse = "Ocurrio un error al crear característica, por favor consulte con el Administrador";
            error_log($strResponse);
            error_log($e->getMessage());
        }
        return new Response($strResponse);
    }

    /**
    *
    * Cambia una a asignación a estado Standby
    * @author Andrés Montero <amontero@telconet.ec>
    * @version 1.0 12-05-2020
    * @since 1.0
    * @return JsonResponse
    */
   public function ajaxPonerAsignacionStandbyAction()
   {
       $objRequest        = $this->getRequest();
       $objSesion         = $objRequest->getSession();
       $strUsuario        = $objSesion->get('user');
       $strIpCreacion     = $objRequest->getClientIp();
       $objServiceUtil    = $this->get('schema.Util');
       try
       {
            $arrayParametros['intIdAsignacion']     = $objRequest->get('intId');
            $arrayParametros['strTipoHistorial']    = $objRequest->get('strTipoHist');
            if ($arrayParametros['strTipoHistorial'] == 'STANDBY')
            {
                $arrayParametros['strObservacion']      = 'Se cambio asignación a Standby, se configuro para reasignarla en el cambio de turno del '.
                                                          $objRequest->get('strFeCambT').'.   Observaciones: '.$objRequest->get('strObs');
            }
            else
            {
                $arrayParametros['strObservacion']      = 'Se finaliza Standby de asignación y se reasigna a '.$objRequest->get('strAgente').
                                                          '.   Observaciones: '.$objRequest->get('strObs');
            }
            $arrayParametros['strAgente']           = $objRequest->get('strAgente')?$objRequest->get('strAgente'):"";
            $arrayParametros['objFechaHoraCambioT'] = $objRequest->get('strFeCambT')?
                                                        date_create_from_format('d/m/Y H:i:s', $objRequest->get('strFeCambT').":00"):"";
            $arrayParametros['strUsrCreacion']      = $strUsuario;
            $arrayParametros['strIpCreacion']       = $strIpCreacion;

            $objSoporteService = $this->get('soporte.SoporteService');
            $strResponse    = $objSoporteService->ponerQuitarAsignacionStandby($arrayParametros);
       }
       catch(\Exception $e)
       {
           $objServiceUtil->insertError( 'Telcos+',
                                         'SoporteBundle.AgenteController.ajaxPonerAsignacionStandbyAction',
                                         'Error al procesar asignación a estado standby. '.$e->getMessage(),
                                         $strUsuario,
                                         $strIpCreacion );
            $strResponse = "Error";
       }
       return new Response($strResponse);
   }

}
