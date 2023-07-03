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

class GestionPendientesController extends Controller implements TokenAuthenticatedController
{
    /**
     * Muestra la pantalla index del modulo de asignaciones
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 10-02-2020
     * @since 1.0
     * @return render a index.html.twig
     */
    /**
    * @Secure(roles="ROLE_455-1")
    */
    public function indexAction()
    {
        $objRequest    = $this->get('request');
        $objSession    = $objRequest->getSession();
        $strUsuario    = $objSession->get('user');
        $strCodEmpresa = $objSession->get('idEmpresa');
        $objEmComercial= $this->getDoctrine()->getManager('telconet');
        $arrayEmpleado = $objEmComercial->getRepository("schemaBundle:InfoPersona")->getPersonaDepartamentoPorUserEmpresa($strUsuario,$strCodEmpresa);
        if (!empty($arrayEmpleado))
        {
            $strNombreDepartamento = $arrayEmpleado['NOMBRE_DEPARTAMENTO'];
            $intIdDepartamento     = $arrayEmpleado['ID_DEPARTAMENTO'];
        }
        $arrayParametros['strNombreParametro'] = "TRAMOS_PARA_MODULO_GESTION_PENDIENTES";
        $arrayParametros['strModulo']          = "SOPORTE";
        $arrayParametros['strEstado']          = "Activo";
        $arrayAdmiParametroCab                 = $objEmComercial->getRepository('schemaBundle:AdmiParametroCab')->findParametrosCab($arrayParametros);
        $intIdParametroTipoProblema            = 0;
        foreach($arrayAdmiParametroCab['arrayResultado'] as $arrayAdmiParamCab)
        {
            $intIdParametroTipoProblema = $arrayAdmiParamCab['intIdParametro'];
        }
        $arrayParametros['strNombreParametro'] = "CIRCUITOS_PARA_MODULO_GESTION_PENDIENTES";
        $arrayAdmiParametroCab                 = $objEmComercial->getRepository('schemaBundle:AdmiParametroCab')->findParametrosCab($arrayParametros);
        $intIdParametroCircuito                = 0;
        foreach($arrayAdmiParametroCab['arrayResultado'] as $arrayAdmiParamCab)
        {
            $intIdParametroCircuito = $arrayAdmiParamCab['intIdParametro'];
        }
        return $this->render('soporteBundle:GestionPendientes:index.html.twig', array(
                                                                           'nombreDepartamento'         => $strNombreDepartamento,
                                                                           'idDepartamento'             => $intIdDepartamento,
                                                                           'codEmpresa'                 => $strCodEmpresa,
                                                                           'idParametroCabTipoProblema' => $intIdParametroTipoProblema,
                                                                           'idParametroCabCircuito'     => $intIdParametroCircuito,
                                                                          ));
    }

   /**
     * 
     * Actualización: Se crea función en service con la consulta a base de datos de los pendientes.
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.2 13-08-2021
     * 
     * Actualización: Se agrega que retorne el tramo, hilo_telefonica, tarea_informe_id, fecha_ini_tarea, 
     *                fecha_fin_tarea, asig_tarea, telf_asig_tarea y estado_tarea_inf de los pendientes consultados.
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 04-05-2021
     * 
     * Construye la informacion para mostrar el listado de pendientes del departamento en sesión
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 14-01-2021
     * @since 1.0
     * @return JsonResponse
     */
    public function gridAction()
    {
        $objJsonRespuesta     = new JsonResponse();
        $objServiceUtil       = $this->get('schema.Util');
        $objSoporteService    = $this->get('soporte.SoporteService');
        $objSession           = $this->get('request')->getSession();
        $strIpCreacion        = $this->get('request')->getClientIp();
        $strCodEmpresa        = $objSession->get('idEmpresa');
        $intIdOficina         = $objSession->get('idOficina');
        $strUsr               = $objSession->get('user');
        $objEmSoporte         = $this->getDoctrine()->getManager("telconet_soporte");
        $objEmComercial       = $this->getDoctrine()->getManager("telconet");
        $strUserDbSoporte     = $this->container->getParameter('user_soporte');
        $strPasswordDbSoporte = $this->container->getParameter('passwd_soporte');
        $strDatabaseDsn       = $this->container->getParameter('database_dsn');
        $objDateFechaHoy      = date("Y/m/d");
        $objRequest           = $this->get('request');
        $strEstado            = $objRequest->get('estado');
        $strTipo              = $objRequest->get('tipoPendiente');
        $strTabVisible        = $objRequest->get('tabVisible');
        $strFechaIni          = $objRequest->get('fechaIni');
        $strFechaFin          = $objRequest->get('fechaFin');
        try
        {
            $arrayEmp = $objEmComercial->getRepository("schemaBundle:InfoPersona")->getPersonaDepartamentoPorUserEmpresa($strUsr,$strCodEmpresa);
            if (!empty($arrayEmp))
            {
                $intIdDepartamento = $arrayEmp['ID_DEPARTAMENTO'];
            }
            if ($strTabVisible == 'GestionPendientesRecorridos' || $strTabVisible == 'GestionPendientesMunicipio')
            {
                $intIdDepartamento = "";
            }
            $arrayParametros                         = array();
            $arrayParametros['strUserDbSoporte']     = $strUserDbSoporte;
            $arrayParametros['strPasswordDbSoporte'] = $strPasswordDbSoporte;
            $arrayParametros['strDatabaseDsn']       = $strDatabaseDsn;
            $arrayParametros['strCodEmpresa']        = $strCodEmpresa;
            $arrayParametros['strIdDepartamento']    = $intIdDepartamento;
            $arrayParametros['intIdCanton']          = "";
            $arrayParametros['strEstado']            = ($strEstado == 'TODOS') ? null : $strEstado;
            $arrayParametros['strTipo']              = $strTipo;
            $arrayParametros['strTabVisible']        = $strTabVisible;
            $arrayParametros['strFechaIni']          = $strFechaIni;
            $arrayParametros['strFechaFin']          = $strFechaFin;
            $arrayParametros['strUsrSesion']         = $strUsrEmpleado;
            $arrayResultado = $objSoporteService->getListadoGestionPendientes($arrayParametros);
            $objJsonRespuesta->setData(['data'=>$arrayResultado]);

        }
        catch(\Exception $objE)
        {
            $objServiceUtil->insertError($strUsr,'SoporteBundle.GestionPendientesController.gridAction',
                                        'Error al consultar los pendientes. '.$objE->getMessage(),$strUsr,$strIpCreacion);
        }
        return $objJsonRespuesta;
    }

   /**
     * Construye la informacion para mostrar el listado de pendientes del departamento en sesión
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 14-01-2021
     * @since 1.0
     * @return JsonResponse
     */
    public function gridSeguimientosAction()
    {
        $objJsonResponse      = new JsonResponse();
        $objServiceUtil       = $this->get('schema.Util');
        $objSession           = $this->get('request')->getSession();
        $strIpCreacion        = $this->get('request')->getClientIp();
        $strCodEmpresa        = $objSession->get('idEmpresa');
        $intIdOficina         = $objSession->get('idOficina');
        $strUsrEmpleado       = $objSession->get('user');
        $objEmSoporte         = $this->getDoctrine()->getManager("telconet_soporte");
        $objEmComercial       = $this->getDoctrine()->getManager("telconet");
        $strUserDbSoporte     = $this->container->getParameter('user_soporte');
        $strPasswordDbSoporte = $this->container->getParameter('passwd_soporte');
        $strDatabaseDsn       = $this->container->getParameter('database_dsn');
        $objRequest           = $this->get('request');
        $intIdTarea           = $objRequest->get('idTarea');
        $intReferenciaId      = $objRequest->get('referenciaId');
        $strProcedencia       = $objRequest->get('procedencia');
        try
        {
            $arrayEmpleado  = $objEmComercial->getRepository("schemaBundle:InfoPersona")
                                         ->getPersonaDepartamentoPorUserEmpresa($strUsrEmpleado,$strCodEmpresa);
            if (!empty($arrayEmpleado))
            {
                $intIdDepartamento = $arrayEmpleado['ID_DEPARTAMENTO'];
            }
            $arrayParametros                         = array();
            $arrayParametros['strUserDbSoporte']     = $strUserDbSoporte;
            $arrayParametros['strPasswordDbSoporte'] = $strPasswordDbSoporte;
            $arrayParametros['strDatabaseDsn']       = $strDatabaseDsn;
            $arrayParametros['strCodEmpresa']        = $strCodEmpresa;
            $arrayParametros['strIdDepartamento']    = $intIdDepartamento;
            $arrayParametros['intIdTarea']           = $intIdTarea;
            $arrayParametros['intReferenciaId']      = $intReferenciaId;
            $arrayParametros['strProcedencia']       = $strProcedencia;
            $objCursor = $objEmSoporte->getRepository('schemaBundle:InfoAsignacionSolicitud')->getSeguimientosGestPendiente($arrayParametros);
            if( !empty($objCursor) )
            {
                $arrayResultado   = array();
                $i                = 0;
                while( ($arrayResultCursor = oci_fetch_array($objCursor, OCI_ASSOC + OCI_RETURN_NULLS)) )
                {
                    $arrayResultado[$i]['numero']         = $i+1;
                    $arrayResultado[$i]['fecha']       = (isset($arrayResultCursor['FE_CREACION']) && !empty($arrayResultCursor['FE_CREACION']))
                                                            ? $arrayResultCursor['FE_CREACION'] : '';
                    $arrayResultado[$i]['empleado']    = (isset($arrayResultCursor['USR_CREACION']) && !empty($arrayResultCursor['USR_CREACION']))
                                                            ? strtolower($arrayResultCursor['USR_CREACION']) : '';
                    $arrayResultado[$i]['observacion'] = (isset($arrayResultCursor['OBSERVACION']) && !empty($arrayResultCursor['OBSERVACION']))
                                                            ? $arrayResultCursor['OBSERVACION'] : '';
                    $arrayResultado[$i]['departamento']= (isset($arrayResultCursor['DEPARTAMENTO']) && !empty($arrayResultCursor['DEPARTAMENTO']))
                                                            ? $arrayResultCursor['DEPARTAMENTO'] : '';
                    $arrayResultado[$i]['tipo']        = ( isset($arrayResultCursor['TIPO']) && !empty($arrayResultCursor['TIPO']) )
                                                            ? $arrayResultCursor['TIPO'] : '';
                    $i++;
                }
            }
        }
        catch(\Exception $e)
        {
            $objServiceUtil->insertError( 'Telcos+','SoporteBundle.GestionPendientesController.gridSeguimientosAction',
                                       'Error al consultar los pendientes. '.$e->getMessage(),$strUsrEmpleado,$strIpCreacion );
        }
        $objJsonResponse->setData(['data'=>$arrayResultado]);
        return $objJsonResponse;
    }

    /**
     * Guarda seguimiento de un pendiente
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 10-02-2021
     * @since 1.0
     * @return JsonResponse
     */
    public function ajaxCreaSeguimientoAction()
    {
        $objRequest         = $this->getRequest();
        $objSesion          = $objRequest->getSession();
        $strDetalle         = $objRequest->get('strDetalle');
        $intAsignacionSolId = $objRequest->get('intId');
        $strProcedencia     = $objRequest->get('procedencia');
        $intComunicacionId  = $objRequest->get('comunicacionId');
        $strUsrCreacion     = $objSesion->get('user');
        $codEmpresa         = $objSesion->get('idEmpresa');
        $strPrefijo         = $objSesion->get('prefijoEmpresa');
        $intIdDepartamento  = $objSesion->get('idDepartamento');
        $strIpCreacion      = $objRequest->getClientIp();
        $objEmSoporte       = $this->getDoctrine()->getManager("telconet_soporte");
        $objServiceUtil     = $this->get('schema.Util');
        try
        {
            $objEmSoporte->getConnection()->beginTransaction();
            $objInfoAsignacionSolicitud = $objEmSoporte->getRepository("schemaBundle:InfoAsignacionSolicitud")->findOneById($intAsignacionSolId);
            if (is_object($objInfoAsignacionSolicitud))
            {
                $arrayParametrosSeguimiento['intIdAsignacion']            = $intAsignacionSolId;
                $arrayParametrosSeguimiento['strDetalle']                 = $strDetalle;
                $arrayParametrosSeguimiento['strUsuarioCreacion']         = $strUsrCreacion;
                $arrayParametrosSeguimiento['strUsuarioGestion']          = "";
                $arrayParametrosSeguimiento['strIpCreacion']              = $objRequest->getClientIp();
                $arrayParametrosSeguimiento['strGestionado']              = "S";
                $arrayParametrosSeguimiento['strProcedencia']             = $strProcedencia;
                $arrayParametrosSeguimiento['intSeguimientoAsignacionId'] = null;
                $arrayParametrosSeguimiento['intComunicacionId']          = $intComunicacionId;
                $objSoporteService                                        = $this->get('soporte.SoporteService');
                $strRespIngresoSeguimiento = $objSoporteService->crearSeguimientoAsignacionSolicitud($arrayParametrosSeguimiento);
                $objEmSoporte->getConnection()->commit();
                $strResponse = 'OK';
            }
            else
            {
                throw new \Exception('No se encontro id de asignación para crear seguimiento');
            }
        }
        catch(\Exception $e)
        {
            $objEmSoporte->getConnection()->rollback();
            $objEmSoporte->getConnection()->close();
            $strResponse = "Ocurrio un error al ingresar el seguimiento, por favor consulte con el Administrador";
            $objServiceUtil->insertError( 'Telcos+','SoporteBundle.GestionPendientesController.ajaxCreaSeguimientoAction',
                                          '[Error al ingresar seguimiento del pendiente]. '.$e->getMessage(),$strUsrCreacion,$strIpCreacion );
        }
        return new Response($strResponse);
    }

   /**
     * Actualización: Se agrega que retorne la estado, fecha y detalle_id de la tarea consultada 
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 04-05-2021
     * 
     * Función que obtiene la información de observación y nombre de tarea de una tarea
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 23-03-2021
     * @since 1.0
     * @return JsonResponse
     */
    public function ajaxObtenerDatosTareaAction()
    {
        $objJsonResponse      = new JsonResponse();
        $objServiceUtil       = $this->get('schema.Util');
        $objSession           = $this->get('request')->getSession();
        $strIpCreacion        = $this->get('request')->getClientIp();
        $strUsrEmpleado       = $objSession->get('user');
        $objEmSoporte         = $this->getDoctrine()->getManager("telconet_soporte");
        $objEmComercial       = $this->getDoctrine()->getManager("telconet");
        $strUserDbSoporte     = $this->container->getParameter('user_soporte');
        $strPasswordDbSoporte = $this->container->getParameter('passwd_soporte');
        $strDatabaseDsn       = $this->container->getParameter('database_dsn');
        $objRequest           = $this->get('request');
        $intIdTarea           = $objRequest->get('idTarea');
        try
        {
            $arrayParametros                         = array();
            $arrayParametros['strUserDbSoporte']     = $strUserDbSoporte;
            $arrayParametros['strPasswordDbSoporte'] = $strPasswordDbSoporte;
            $arrayParametros['strDatabaseDsn']       = $strDatabaseDsn;
            $arrayParametros['intIdComunicacion']    = $intIdTarea;
            $objCursor = $objEmSoporte->getRepository('schemaBundle:InfoAsignacionSolicitud')->getDatosTarea($arrayParametros);
            if( !empty($objCursor) )
            {
                $arrayResultado   = array();
                $i                = 0;
                while( ($arrayResultadoCursor = oci_fetch_array($objCursor, OCI_ASSOC + OCI_RETURN_NULLS)) != false )
                {
                    $arrayResultado[$i]['numero']      = $i+1;
                    $arrayResultado[$i]['observacion'] = (isset($arrayResultadoCursor['OBSERVACION']) && !empty($arrayResultadoCursor['OBSERVACION']))
                                                            ? $arrayResultadoCursor['OBSERVACION'] : '';
                    $arrayResultado[$i]['tarea']        = ( isset($arrayResultadoCursor['TAREA']) && !empty($arrayResultadoCursor['TAREA']) )
                                                            ? $arrayResultadoCursor['TAREA'] : '';
                    $arrayResultado[$i]['estado']       = ( isset($arrayResultadoCursor['ESTADO']) && !empty($arrayResultadoCursor['ESTADO']) )
                                                            ? $arrayResultadoCursor['ESTADO'] : '';
                    $arrayResultado[$i]['fecha']        = ( isset($arrayResultadoCursor['FECHA']) && !empty($arrayResultadoCursor['FECHA']) )
                                                            ? $arrayResultadoCursor['FECHA'] : '';
                    $arrayResultado[$i]['detalle_id']   = (isset($arrayResultadoCursor['DETALLE_ID']) && !empty($arrayResultadoCursor['DETALLE_ID']))
                                                            ? $arrayResultadoCursor['DETALLE_ID'] : '';
                    $i++;
                }
            }
        }
        catch(\Exception $e)
        {
            $objServiceUtil->insertError( 'Telcos+','SoporteBundle.GestionPendientesController.getDatosTareaAction',
                                       'Error al consultar los pendientes. '.$e->getMessage(),$strUsrEmpleado,$strIpCreacion );
        }
        $objJsonResponse->setData(['data'=>$arrayResultado]);
        return $objJsonResponse;
    }

    /**
     * Función que realiza el proceso de buscar los parámetros segun la descripción recibida por parámetro
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 04-05-2021
     * @since 1.0
     * @return JsonResponse
     */
    public function ajaxObtenerParametrosAction()
    {
        $objJsonRespuesta     = new JsonResponse();
        $objEmSoporte         = $this->getDoctrine()->getManager('telconet_soporte');
        $objEmComercial       = $this->getDoctrine()->getManager('telconet');
        $objServiceUtil       = $this->get('schema.Util');
        $objRequest           = $this->get('request');
        $objSession           = $objRequest->getSession();
        $strCodEmpresa        = $objSession->get('idEmpresa');
        $strIpCreacion        = $objRequest->getClientIp();
        $strUsuario           = $objSession->get('user');
        $strDescParametro     = $objRequest->get('descParametro');
        $objJsonRespuesta->setData(['data'=>array()]);
        try
        {
            $arrayEmpleado = $objEmComercial->getRepository("schemaBundle:InfoPersona")
                                            ->getPersonaDepartamentoPorUserEmpresa($strUsuario, $strCodEmpresa);
            if (!empty($arrayEmpleado))
            {
                $intIdDepartamento = $arrayEmpleado['ID_DEPARTAMENTO'];
            }
            $arrayItems = $objEmSoporte->getRepository("schemaBundle:AdmiParametroDet")->get($strDescParametro,
                                                                                             "SOPORTE","","","","","","","",$strCodEmpresa);
            $objJsonRespuesta->setData(['data'=>$arrayItems]);
        }
        catch(\Exception $e)
        {
            $objServiceUtil->insertError( 'Telcos+','SoporteBundle.AgenteController.ajaxObtenerParametrosAction',
                                          'Error al consultar los parámetros. '.$e->getMessage(),$strUsuario,$strIpCreacion );
        }
        return $objJsonRespuesta;
    }

    /**
     * Actualización: Se agrega ingreso de circuito, ciudad Notificación y departamento Notificación
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 30-06-2021
     * 
     * Función que realiza el proceso de agregar un tramo al pendiente
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 04-05-2021
     * @since 1.0
     * @return JsonResponse
     */
    public function ajaxAgregarTramoAction()
    {
        $objRequest         = $this->getRequest();
        $objSesion          = $objRequest->getSession();
        $intAsignacionSolId = $objRequest->get('intId');
        $strTramo           = $objRequest->get('strTramo');
        $strHiloTelefonica  = $objRequest->get('strHiloTelef');
        $strCircuito        = $objRequest->get('strCircuito');
        $strCiudadNotif     = $objRequest->get('strCiudadNotif');
        $strDepNotif        = $objRequest->get('strDepNotif');
        $strUsrUltMod       = $objSesion->get('user');
        $strIpCreacion      = $objRequest->getClientIp();
        $strCodEmpresa      = $objSesion->get('idEmpresa');
        $objEmSoporte       = $this->getDoctrine()->getManager("telconet_soporte");
        $strResponse        = "Ocurrio un error al agregar tramo, por favor consulte con el Administrador";
        try
        {
            $objEmSoporte->getConnection()->beginTransaction();
            $entityInfoAsignacionSolicitud = $objEmSoporte->getRepository("schemaBundle:InfoAsignacionSolicitud")->findOneById($intAsignacionSolId);
            if (is_object($entityInfoAsignacionSolicitud))
            {
                if (isset($strCiudadNotif) && isset($strDepNotif) && $entityInfoAsignacionSolicitud->getTipoAtencion() == "CASO" )
                {
                    //consultar en los parámetros si la ciudad y el departamento tienen alias configurados para notificación
                    $booleanEncontroAlias = false;
                    $arrayAliases = $objEmSoporte->getRepository("schemaBundle:AdmiParametroDet")->get(
                                    'VALORES_ASIGNACION_TAREA_INFORME_MODULO_GESTION_PENDIENTES',
                                                    "SOPORTE","","","","",$strDepNotif,"","",$strCodEmpresa);
                    for($intIndice = 0; $intIndice < count($arrayAliases); $intIndice++ )
                    {
                        $arrayAliasesObtenidos = explode(",",$arrayAliases[$intIndice]['valor1']);
                        for($intInd = 0; $intInd < count($arrayAliasesObtenidos); $intInd++)
                        {
                            $arrayCantonesAliasesObtenidos = explode("|",$arrayAliasesObtenidos[$intInd]);
                            if ($arrayCantonesAliasesObtenidos[0] == $strCiudadNotif)
                            {
                                $booleanEncontroAlias = true;
                                break;
                            }
                        }
                    }
                    if (!$booleanEncontroAlias)
                    {
                        $strResponse = 'No se encontro Alias para la ciudad y el departamento seleccionado';
                        throw new \Exception($strResponse);
                    }
                }
                if ($entityInfoAsignacionSolicitud->getNotificacion() == "INICIAL")
                {
                    $arrayParametros['strNotificacion']    = "";    
                }
                $strDatoAdicional = $entityInfoAsignacionSolicitud->getDatoAdicional();
                if (isset($strDatoAdicional))
                {
                    $objDatoAdicional = json_decode($strDatoAdicional);
                    $objNuevoDatoAdicional = (object) [
                        'ciudadNotif'   => $strCiudadNotif,
                        'depNotif'      => $strDepNotif,
                        'fechaFinNotif' => $objDatoAdicional->fechaFinNotif,
                        'horaFinNotif'  => $objDatoAdicional->horaFinNotif,
                        'detalleNotif'  => $objDatoAdicional->detalleNotif
                    ];
                }
                $arrayParametros['intIdAsignacion']    = $intAsignacionSolId;
                $arrayParametros['strTramo']           = $strTramo;
                $arrayParametros['strHiloTelefonica']  = (isset($strHiloTelefonica))?$strHiloTelefonica:"";
                $arrayParametros['strCircuito']        = (isset($strCircuito))?$strCircuito:"";
                $arrayParametros['strDatoAdicional']   = ($entityInfoAsignacionSolicitud->getTipoAtencion() == "CASO")?
                                                            (
                                                            (is_object($objNuevoDatoAdicional))?
                                                             json_encode($objNuevoDatoAdicional):
                                                             '{"ciudadNotif":"'.$strCiudadNotif.
                                                             '","depNotif":"'.$strDepNotif.'","fechaFinNotif":"","horaFinNotif":"","detalleNotif":""}'
                                                            ):"";
                $arrayParametros['strUsrUltMod']       = $strUsrUltMod;
                $arrayParametros['strUsrAsignado']     = "";
                $arrayParametros['dateFeUltMod']       = new \DateTime('now');
                $arrayParametros['strIpUltMod']        = $strIpCreacion;
                $objSoporteService = $this->get('soporte.SoporteService');
                $objSoporteService->modificarAsignacionSolicitud($arrayParametros);
                $objEmSoporte->getConnection()->commit();
                $objEmSoporte->getConnection()->close();
                $strResponse = 'OK';
            }
            else
            {
                $strResponse = 'No se encontro id del pendiente para modificar';
                throw new \Exception($strResponse);   
            }
        }
        catch(\Exception $e)
        {
            $objEmSoporte->getConnection()->rollback();
            $objEmSoporte->getConnection()->close();
            error_log("ERROR AL AGREGAR TRAMO EN PENDIENTE DE RECORRIDO");
            error_log($e->getMessage());
        }
        return new Response($strResponse);
    }

    /**
     * Actualización: Se realiza validación para evitar doble registro de tarea de informe de recorrido
     * @author Fernando López <filopez@telconet.ec>
     * @version 1.1 25-02-2022
     * 
     * Función que realiza el proceso de crear tarea de Informe Ejecutivo
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 04-05-2021
     * @since 1.0
     * @return JsonResponse
     */
    public function ajaxCrearTareaInformeAction()
    {
        $objRequest           = $this->getRequest();
        $objJsonRespuesta     = new JsonResponse();
        $objSesion            = $objRequest->getSession();
        $intIdAsignacionSol   = $objRequest->get('idAsignacion');
        $intIdFormaContacto   = $objRequest->get('idFormaContacto');
        $intIdClaseDocumento  = $objRequest->get('idClaseDocumento');
        $intIdProceso         = $objRequest->get('idProceso');
        $intIdTarea           = $objRequest->get('idTarea');
        $intIdComunicacion    = $objRequest->get('idComunicacion');
        $strObservacion       = $objRequest->get('observacion');
        $strEncargadoTarea    = $objRequest->get('encargadoTarea');
        $strUsuario           = $objSesion->get('user');
        $strCodEmpresa        = $objSesion->get('idEmpresa');
        $strPrefijoEmpresa    = $objSesion->get('prefijoEmpresa');
        $strIpCreacion        = $objRequest->getClientIp();
        $objEmSoporte         = $this->getDoctrine()->getManager("telconet_soporte");
        $objEmComunicacion    = $this->getDoctrine()->getManager('telconet_comunicacion');
        $objEmComercial       = $this->getDoctrine()->getManager('telconet');
        $objServiceUtil       = $this->get('schema.Util');
        $strUserDbSoporte     = $this->container->getParameter('user_soporte');
        $strPasswordDbSoporte = $this->container->getParameter('passwd_soporte');
        $strDatabaseDsn       = $this->container->getParameter('database_dsn');
        $intIdPersona           = "";
        $strFechaInicio         = "";
        $strFechaFin            = "";
        $strNombreTecnico       = "";
        $strCelularTecnico      = "";
        $strTramo               = "";
        $strHiloTelefonica      = "";
        $arrayCorreos           = array();
        try
        {
            $arrayParametrosTareaInf['intIdFormaContacto'] = $intIdFormaContacto;
            $arrayParametrosTareaInf['intIdClaseDocumento'] = $intIdClaseDocumento;
            $arrayParametrosTareaInf['intIdTarea'] = $intIdTarea;
            $arrayParametrosTareaInf['intIdComunicacion'] = $intIdComunicacion;
            $arrayParametrosTareaInf['strObservacion'] = $strObservacion;
            $arrayParametrosTareaInf['strUsuario'] = $strUsuario;
            $arrayParametrosTareaInf['strCodEmpresa'] = $strCodEmpresa;
            $arrayParametrosTareaInf['strPrefijoEmpresa'] = $strPrefijoEmpresa;
            $arrayParametrosTareaInf['strIpCreacion'] = $strIpCreacion;
            $arrayParametrosTareaInf['objEmSoporte'] = $objEmSoporte;
            $arrayParametrosTareaInf['objEmComunicacion'] = $objEmComunicacion;
            $arrayParametrosTareaInf['objEmComercial'] = $objEmComercial;
            $arrayParametrosTareaInf['strEncargadoTarea'] = $strEncargadoTarea;

            $objInfoAsignacionSolicitud = $objEmSoporte->getRepository("schemaBundle:InfoAsignacionSolicitud")->findOneById($intIdAsignacionSol);
            if (is_object($objInfoAsignacionSolicitud))
            {
                $strHiloTelefonica = $objInfoAsignacionSolicitud->getHiloTelefonica();
                $strTramo          = $objInfoAsignacionSolicitud->getTramo();

                $strDatoAdicional = $objInfoAsignacionSolicitud->getDatoAdicional();
                if (isset($strDatoAdicional) && is_numeric($strDatoAdicional)) 
                {
                    throw new \Exception('Estimado usuario ya existe una tarea de informe registrada para la tarea '.$intIdComunicacion.
                    ', por favor actualice la vista');
                }

            }

            $objSoporteService = $this->get('soporte.SoporteService');
            $arrayRespuestaTareaInforme = $objSoporteService->getDatosTareaInformeRecorrido($arrayParametrosTareaInf);

            $intNumeroTarea = null;

            if ($arrayRespuestaTareaInforme["status"] == 200)
            {
                $intNumeroTarea              = $arrayRespuestaTareaInforme["data"]["intNumeroTarea"];
                $arrayCorreos                = $arrayRespuestaTareaInforme["data"]["arrayCorreos"];
                $intIdPersona                = $arrayRespuestaTareaInforme["data"]["intIdPersona"];
                $objAdmiDepartamento         = $arrayRespuestaTareaInforme["data"]["objAdmiDepartamento"];
                $arrayParametrosTareaInforme = $arrayRespuestaTareaInforme["data"]["arrayDatosTarea"];

                $objServiceInfoCambiarPlan = $this->get('tecnico.InfoCambiarPlan');
                $intNumeroTarea = $objServiceInfoCambiarPlan->crearTareaRetiroEquipoPorDemo($arrayParametrosTareaInforme);
                if (isset($intNumeroTarea) && $intNumeroTarea!="")
                {
                    $objJsonRespuesta->setData(['mensaje'=> 'La tarea fue creada con exito!',
                                                'status' => 200,'data'=>array("idTarea"=>$intNumeroTarea)]);
                }
            }
            
            if($intNumeroTarea == "" || $intNumeroTarea == null)
            {
                throw new \Exception('Ocurrio un error, no se pudo crear la tarea de informe de recorrido');
            }

            //Asigna la tarea de informe al pendiente
            $arrayParametrosAsig['intIdAsignacion']    = $intIdAsignacionSol;
            $arrayParametrosAsig['strDatoAdicional']   = $intNumeroTarea;
            $arrayParametrosAsig['strUsrUltMod']       = $strUsuario;
            $arrayParametrosAsig['dateFeUltMod']       = new \DateTime('now');
            $arrayParametrosAsig['strIpUltMod']        = $strIpCreacion;
            $objSoporteService                         = $this->get('soporte.SoporteService');
            $objSoporteService->modificarAsignacionSolicitud($arrayParametrosAsig);
            
            //Obtiene los datos de la tarea
            $arrayParametrosDatosTarea                         = array();
            $arrayParametrosDatosTarea['strUserDbSoporte']     = $strUserDbSoporte;
            $arrayParametrosDatosTarea['strPasswordDbSoporte'] = $strPasswordDbSoporte;
            $arrayParametrosDatosTarea['strDatabaseDsn']       = $strDatabaseDsn;
            $arrayParametrosDatosTarea['intIdComunicacion']    = $intIdComunicacion;
            $objCursor = $objEmSoporte->getRepository('schemaBundle:InfoAsignacionSolicitud')->getDatosTarea($arrayParametrosDatosTarea);

            if( !empty($objCursor) )
            {
                $arrayResultado   = array();
                $booleanResult    = false;
                while( ($arrayResultadoCursor = oci_fetch_array($objCursor, OCI_ASSOC + OCI_RETURN_NULLS)) != $booleanResult )
                {
                    $strObservacion    = ( isset($arrayResultadoCursor['OBSERVACION']) && !empty($arrayResultadoCursor['OBSERVACION']) )
                                         ? $arrayResultadoCursor['OBSERVACION'] : '';
                    $strFechaInicio    = ( isset($arrayResultadoCursor['FECHA_INI_TAREA']) && !empty($arrayResultadoCursor['FECHA_INI_TAREA']) )
                                         ? $arrayResultadoCursor['FECHA_INI_TAREA'] : '';
                    $strFechaFin       = ( isset($arrayResultadoCursor['FECHA_FIN_TAREA']) && !empty($arrayResultadoCursor['FECHA_FIN_TAREA']) )
                                         ? $arrayResultadoCursor['FECHA_FIN_TAREA'] : '';
                    $strCelularTecnico = ( isset($arrayResultadoCursor['TELEFONO_ASIGNADO']) && !empty($arrayResultadoCursor['TELEFONO_ASIGNADO']) )
                                         ? $arrayResultadoCursor['TELEFONO_ASIGNADO'] : '';
                    $strNombreTecnico  = ( isset($arrayResultadoCursor['NOMBRE_ASIGNADO']) && !empty($arrayResultadoCursor['NOMBRE_ASIGNADO']) )
                                         ? $arrayResultadoCursor['NOMBRE_ASIGNADO'] : '';
                }
            }
            //Enviar Notificacion
            $arrayContactosCoordinadores = $objEmSoporte->getRepository("schemaBundle:AdmiParametroDet")->get('CORREOS_REMITENTES_TAREA_INFORME_NOC',
                                                                        "SOPORTE","","","","","","","",$strCodEmpresa);
            //Obtiene correos de coordinadores
            for($intIndice = 0; $intIndice < count($arrayContactosCoordinadores); $intIndice++ )
            {
                if ($arrayContactosCoordinadores[$intIndice]['valor2'] == 'ALIAS_DEPARTAMENTO')
                {
                    $arrayCorreos[] = $arrayContactosCoordinadores[$intIndice]['valor1'];
                }
                else
                {
                    $arrayParametrosCoord = array();
                    $arrayParametrosCoord['arrayEstadoATR']          = ['arrayEstado' => ['Activo']];
                    $arrayParametrosCoord['arrayEstadoIPER']         = ['arrayEstado' => ['Activo'], 'strComparador' => 'IN'];
                    $arrayParametrosCoord['arrayEstadoIPR']          = ['arrayEstado' => ['Activo'], 'strComparador' => 'IN'];
                    $arrayParametrosCoord['arrayDescripcionTipoRol'] = ['arrayDescripcionTipoRol' => ['Empleado'], 'strComparador' => 'IN'];
                    $arrayParametrosCoord['arrayEmpresaCod']         = ['arrayEmpresaCod' => [$strCodEmpresa]];
                    $arrayParametrosCoord['arrayPersona']            = ['arrayPersona'    => [$arrayContactosCoordinadores[$intIndice]['valor1']]];
                    $objDataCoordinador                              = $objEmComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                                                                      ->getResultadoPersonaEmpresaRol($arrayParametrosCoord);
                    if (!is_object($objDataCoordinador) )
                    {
                        break;
                    }
                    if (count($objDataCoordinador->registros) <= 0)
                    {
                        break;
                    }
                    if ($objDataCoordinador->registros[0]['strDescripcionRol'] == $arrayContactosCoordinadores[$intIndice]['valor2'])
                    {
                        $strFormaContactoCoordinadores = $objEmComercial->getRepository("schemaBundle:InfoPersonaFormaContacto")
                                                                        ->getStringFormasContactoParaSession(
                                                                                                $arrayContactosCoordinadores[$intIndice]['valor1'],
                                                                                                'Correo Electronico');
                        $arrayFormaContactoCoordinadores = explode(",",$strFormaContactoCoordinadores);
                        $arrayFormaContactoAsignado      = explode(",",$strFormaContactoAsignado);
                        for($intInd = 0; $intInd < count($arrayFormaContactoCoordinadores); $intInd++ )
                        {
                            $arrayCorreos[] = $arrayFormaContactoCoordinadores[$intInd];
                        }
                    }
                }
            }
            //Obtiene correo de persona asignada
            $strFormaContactoAsignado   = $objEmComercial->getRepository("schemaBundle:InfoPersonaFormaContacto")
                                                         ->getStringFormasContactoParaSession($intIdPersona,'Correo Electronico');
            $arrayFormaContactoAsignado = explode(",",$strFormaContactoAsignado);
            for($intInd = 0; $intInd < count($arrayFormaContactoAsignado); $intInd++ )
            {
                $arrayCorreos[] = $arrayFormaContactoAsignado[$intInd];
            }

            $arrayParametrosCorreo = array(
              'numeroTarea'             => $intIdComunicacion,
              'telefonica'              => $strHiloTelefonica,
              'nombreTecnico'           => $strNombreTecnico,
              'celularTecnico'          => $strCelularTecnico,
              'observacion'             => $strObservacion,
              'numeroTareaInforme'      => $intNumeroTarea,
              'departamento'            => $objAdmiDepartamento->getNombreDepartamento(),
              'fechaInicio'             => $strFechaInicio,
              'fechaFin'                => $strFechaFin,
              'tramo'                   => $strTramo);

            $objServiceEnvioPlantilla = $this->get('soporte.EnvioPlantilla');
            $objServiceEnvioPlantilla->generarEnvioPlantilla("Notificación de tarea de Informe de recorrido",$arrayCorreos,
                                                             "MAIL_TINF_NOC",$arrayParametrosCorreo,$strCodEmpresa,'','');
        }
        catch(\Exception $e)
        {
            if(strpos($e->getMessage(), 'Estimado usuario ya existe una tarea de informe registrada para la tarea') === false)
            {
                $strDataError = ['mensaje'=>'Ocurrio un error, no se pudo crear la tarea!','status' => 500,'data'=>array()];
            }
            else
            {
                $strDataError = ['mensaje'=> $e->getMessage(),'status' => 500,'data'=>array()];
            }
            $objJsonRespuesta->setData($strDataError);
            $objServiceUtil->insertError($strUsuario,'SoporteBundle.GestionPendientesController.ajaxCrearTareaInformeAction',
                                        'Error al crear tarea de informe ==>> '.$e->getMessage(),$strUsuario,$strIpCreacion);
        }
        return $objJsonRespuesta;
    }

    /**
     * 
     * Actualización: Se agrega bloque de código para proceso de envio de correo
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 13-08-2021
     * 
     * Función que realiza el proceso de crear tarea de Informe Ejecutivo
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 04-05-2021
     * @since 1.0
     * @return JsonResponse
     */
    public function ajaxCrearEnvioNotificacionAction()
    {
        $objRequest             = $this->getRequest();
        $objJsonRespuesta       = new JsonResponse();
        $objSesion              = $objRequest->getSession();
        $intIdAsignacionSol     = $objRequest->get('idAsignacion');
        $strTipoNotificacion    = $objRequest->get('tipoNotificacion');
        $strFechaNotifFinal     = $objRequest->get('fechaNotifFinal');
        $strDetalleNotifFinal   = $objRequest->get('detalleNotifFinal');
        $strUsuario             = $objSesion->get('user');
        $strCodEmpresa          = $objSesion->get('idEmpresa');
        $strPrefijoEmpresa      = $objSesion->get('prefijoEmpresa');
        $strIpCreacion          = $objRequest->getClientIp();
        $objEmSoporte           = $this->getDoctrine()->getManager("telconet_soporte");
        $objEmComunicacion      = $this->getDoctrine()->getManager('telconet_comunicacion');
        $objEmGeneral           = $this->getDoctrine()->getManager('telconet_general');
        $objEmInfraestructura   = $this->getDoctrine()->getManager('telconet_infraestructura');
        $objEmComercial         = $this->getDoctrine()->getManager('telconet');
        $objServiceUtil         = $this->get('schema.Util');
        $strUserDbSoporte       = $this->container->getParameter('user_soporte');
        $strPasswordDbSoporte   = $this->container->getParameter('passwd_soporte');
        $strDatabaseDsn         = $this->container->getParameter('database_dsn');
        $strRespuesta           = "";
        $intIdDepartamento      = "";
        $intIdPersona           = "";
        $intIdPersonaEmpresaRol = "";
        $strNombrePerAsigna     = "";
        $intIdCanton            = "";
        $intIdCantonJefe        = "";
        $strLoginAfectado       = "";
        $strFechaInicio         = "";
        $strFechaFin            = "";
        $strNombreTecnico       = "";
        $strCelularTecnico      = "";
        $strTramo               = "";
        $strCircuito            = "";
        $strHiloTelefonica      = "";
        $arrayEmpleado          = array();
        $arrayCorreos           = array();
        try
        {
            $objInfoAsignacionSolicitud = $objEmSoporte->getRepository("schemaBundle:InfoAsignacionSolicitud")->findOneById($intIdAsignacionSol);
            if (is_object($objInfoAsignacionSolicitud))
            {
                $strTramo          = $objInfoAsignacionSolicitud->getTramo();
                $strCircuito       = $objInfoAsignacionSolicitud->getCircuito();
            }
            $objInfoCaso                               = $objEmSoporte->getRepository('schemaBundle:InfoCaso')
                                                                      ->find($objInfoAsignacionSolicitud->getReferenciaId());
            $arrayParametrosCaso['numero']             = $objInfoCaso->getNumeroCaso();
            $arrayParametrosCaso['idEmpresaSeleccion'] = $strCodEmpresa;
            $arrayDatosCaso                            = $objEmSoporte->getRepository('schemaBundle:InfoCaso')
                                                                      ->generarJsonCasos($arrayParametrosCaso, 0, 1000, $objSesion, $objEmComercial, 
                                                                        null,$objEmInfraestructura, $objEmGeneral, $objEmComunicacion);
            $arrayRespuestaCaso                        = (array) json_decode($arrayDatosCaso);

            $arrayParametrosAfect['intIdCaso'] = $arrayRespuestaCaso['encontrados'][0]->id_caso;
            $objJsonAfectados = $objEmSoporte->getRepository('schemaBundle:InfoCaso')->getAfectadosCaso($arrayParametrosAfect);
            $objArrayData = json_decode($objJsonAfectados)->encontrados[0];
            $intIdPunto = "";
            if($objArrayData->tipo_afectado=='Cliente')
            {
                $intIdPunto = $objArrayData->id_afectado;
            }
            if (isset($intIdPunto))
            {
                $objInfoPunto     = $objEmComercial->getRepository('schemaBundle:InfoPunto')->find($intIdPunto);
                $strLoginAfectado = $objInfoPunto->getLogin();
                $objInfoPersona   = $objInfoPunto->getPersonaEmpresaRolId()->getPersonaId();
                $strRazonSocial   = $objInfoPersona->getRazonSocial();
                $strCliente       = isset($strRazonSocial)?$strRazonSocial:$objInfoPersona->getNombres()." ".$objInfoPersona->getApellidos();
            }
            $objDatoAdicional   = json_decode($objInfoAsignacionSolicitud->getDatoAdicional());
            $strFechaFinalNotif = "";
            $strHoraFinalNotif  = "";
            if (isset($strFechaNotifFinal) && isset($strDetalleNotifFinal))
            {
                $arrayFechaNotif       = explode("  ",$strFechaNotifFinal);
                $strDetalleNotifFinal  = preg_replace('([^À-ÿA-Za-z0-9\s])', '', $strDetalleNotifFinal);
                $strFechaFinalNotif    = str_replace("/","-",$arrayFechaNotif[0]);
                $strHoraFinalNotif     = $arrayFechaNotif[1];
                $objNuevoDatoAdicional = (object) [
                    'ciudadNotif'   => $objDatoAdicional->ciudadNotif,
                    'depNotif'      => $objDatoAdicional->depNotif,
                    'fechaFinNotif' => $strFechaFinalNotif,
                    'horaFinNotif'  => $strHoraFinalNotif,
                    'detalleNotif'  => $strDetalleNotifFinal
                ];
            }
            $strCiudad       = "";
            $strDepartamento = "";
            if (is_object($objDatoAdicional))
            {
                $objCiudad       = $objEmGeneral->getRepository("schemaBundle:AdmiCanton")->find($objDatoAdicional->ciudadNotif);
                $strCiudad       = $objCiudad->getNombreCanton();
                $objDepartamento = $objEmGeneral->getRepository("schemaBundle:AdmiDepartamento")->find($objDatoAdicional->depNotif);
                $strDepartamento = $objDepartamento->getNombreDepartamento();
            }

            //Obtiene el correo destino del departamento encargado
            $arrayAliases = $objEmSoporte->getRepository("schemaBundle:AdmiParametroDet")->get(
                                                        'VALORES_ASIGNACION_TAREA_INFORME_MODULO_GESTION_PENDIENTES',
                                                        "SOPORTE","","","","",$objDatoAdicional->depNotif,"","",$strCodEmpresa);
            for($intIndice = 0; $intIndice < count($arrayAliases); $intIndice++ )
            {
                $arrayAliasesObtenidos = explode(",",$arrayAliases[$intIndice]['valor1']);
                for($intInd = 0; $intInd < count($arrayAliasesObtenidos); $intInd++)
                {
                    $arrayCantonesAliasesObtenidos = explode("|",$arrayAliasesObtenidos[$intInd]);
                    if ($arrayCantonesAliasesObtenidos[0] == $objDatoAdicional->ciudadNotif)
                    {
                        $arrayCorreos[] = $arrayCantonesAliasesObtenidos[1];
                    }
                }
            }

            //Obtiene los correos destino para la notificación inicial o final
            $strReportadoPor   = "";
            $strNumeroContacto = "";
            $arrayAliasesDestino = $objEmSoporte->getRepository("schemaBundle:AdmiParametroDet")
                                                ->get('CORREOS_DESTINO_NOTIFICACION_TELEFONICA',"SOPORTE","","","","","","","",$strCodEmpresa);
            for($intIndice = 0; $intIndice < count($arrayAliasesDestino); $intIndice++ )
            {
                $arrayCorreos[] = $arrayAliasesDestino[$intIndice]['valor1'];
            }

            //Obtiene los datos de reportado por y numero contacto de notificación inicial o final
            $arrayDatosReportadoPor = $objEmSoporte->getRepository("schemaBundle:AdmiParametroDet")
                                                   ->get('DATOS_REPORTADO_POR_NOTIFICACION_TELEFONICA',"SOPORTE","","","","","","","",$strCodEmpresa);
            for($intIndice = 0; $intIndice < count($arrayDatosReportadoPor); $intIndice++ )
            {
                $strReportadoPor   = $arrayDatosReportadoPor[$intIndice]['valor1'];
                $strNumeroContacto = $arrayDatosReportadoPor[$intIndice]['valor2'];
            }

            //Calcular tiempos entre fecha inicio y fecha fin
            $strMinutos = "";
            if (isset($arrayRespuestaCaso['encontrados'][0]->fecha_apertura) && $arrayRespuestaCaso['encontrados'][0]->fecha_apertura != "" && 
                isset($strFechaFinalNotif) && $strFechaFinalNotif != "")
            {
                $objFechaInicio      = new \DateTime($arrayRespuestaCaso['encontrados'][0]->fecha_apertura." ".
                                                     $arrayRespuestaCaso['encontrados'][0]->hora_apertura);
                $objFechaFin         = new \DateTime($strFechaFinalNotif." ".$strHoraFinalNotif);
                $objDiferenciaFechas = $objFechaFin->diff($objFechaInicio);
                $intMinutos         += $objDiferenciaFechas->days * 24 * 60;
                $intMinutos         += $objDiferenciaFechas->h * 60;
                $intMinutos         += $objDiferenciaFechas->i;
                $strMinutos         = $intMinutos." min";
            }

            $strAsunto = ($strTipoNotificacion == 'INICIAL')?
            "Notificación de Incidencia: Trouble Ticket Abierto #".$objInfoCaso->getNumeroCaso():
            "Notificación de Incidencia: Trouble Ticket Cerrado #".$objInfoCaso->getNumeroCaso();
  
            $arrayParametrosCorreo = array(
                'numeroTicket'            => $objInfoCaso->getNumeroCaso(),
                'cliente'                 => $strCliente,
                'reportadoPor'            => $strReportadoPor,
                'login'                   => $strLoginAfectado,
                'numeroContacto'          => $strNumeroContacto,
                'fechaInicio'             => $arrayRespuestaCaso['encontrados'][0]->fecha_apertura,
                'horaInicio'              => $arrayRespuestaCaso['encontrados'][0]->hora_apertura,
                'fechaFin'                => $strFechaFinalNotif,
                'horaFin'                 => $strHoraFinalNotif,
                'tiempoTotalAfectacion'   => $strMinutos,
                'circuito'                => $strCircuito,
                'tramo'                   => $strTramo,
                'eventoReportado'         => $arrayRespuestaCaso['encontrados'][0]->tipo_afectacion,
                'accionesRealizadas'      => ($strTipoNotificacion=='INICIAL')?
                                             $arrayRespuestaCaso['encontrados'][0]->version_ini:$strDetalleNotifFinal,
                'departamentoAsignado'    => ucwords(strtolower($strDepartamento))." - ".ucwords(strtolower($strCiudad)),
                'responsableAsignado'     => $arrayRespuestaCaso['encontrados'][0]->departamento_asignado
            );

            $objServiceEnvioPlantilla = $this->get('soporte.EnvioPlantilla');
            $objServiceEnvioPlantilla->generarEnvioPlantilla($strAsunto,$arrayCorreos,"NOTIF_TELFNC",$arrayParametrosCorreo,$strCodEmpresa,'','');

            $objJsonRespuesta->setData(['mensaje'=> 'La notificación fue enviada con exito!','status' => 200]);

            //modifica la notificación y dato adicional
            $arrayParametrosAsig['intIdAsignacion']    = $intIdAsignacionSol;
            $arrayParametrosAsig['strNotificacion']    = $strTipoNotificacion;
            $arrayParametrosAsig['strDatoAdicional']   = (is_object($objNuevoDatoAdicional))?
                                                            json_encode($objNuevoDatoAdicional, JSON_UNESCAPED_UNICODE):
                                                            json_encode($objDatoAdicional, JSON_UNESCAPED_UNICODE);
            $arrayParametrosAsig['strUsrUltMod']       = $strUsuario;
            $arrayParametrosAsig['dateFeUltMod']       = new \DateTime('now');
            $arrayParametrosAsig['strIpUltMod']        = $strIpCreacion;
            $objSoporteService                         = $this->get('soporte.SoporteService');
            $objSoporteService->modificarAsignacionSolicitud($arrayParametrosAsig);
        }
        catch(\Exception $e)
        {
            $objJsonRespuesta->setData(['mensaje'=>'Ocurrio un error, no se pudo enviar la notificación!','status' => 500,'data'=>array()]);
            $objServiceUtil->insertError($strUsuario,'SoporteBundle.GestionPendientesController.ajaxCrearEnvioNotificacionAction',
                                        'Error al crear notificación ==>> '.$e->getMessage(),$strUsuario,$strIpCreacion);
        }
        return $objJsonRespuesta;
    }

    /**
     * Función que realiza el proceso de envío de listado de pendientes
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 13-08-2021
     * @since 1.0
     * @return JsonResponse
     */
    public function ajaxEnviarListadoPendientesAction()
    {
        $objJsonRespuesta     = new JsonResponse();
        $objServiceUtil       = $this->get('schema.Util');
        $objSoporteService    = $this->get('soporte.SoporteService');
        $objSession           = $this->get('request')->getSession();
        $strIpCreacion        = $this->get('request')->getClientIp();
        $strCodEmpresa        = $objSession->get('idEmpresa');
        $strUsr               = $objSession->get('user');
        $objEmSoporte         = $this->getDoctrine()->getManager("telconet_soporte");
        $objEmComercial       = $this->getDoctrine()->getManager("telconet");
        $strUserDbSoporte     = $this->container->getParameter('user_soporte');
        $strPasswordDbSoporte = $this->container->getParameter('passwd_soporte');
        $strDatabaseDsn       = $this->container->getParameter('database_dsn');
        $objRequest           = $this->get('request');
        $strEstado            = $objRequest->get('estado');
        $strTabVisible        = $objRequest->get('tabVisible');
        try
        {
            $arrayEmp = $objEmComercial->getRepository("schemaBundle:InfoPersona")->getPersonaDepartamentoPorUserEmpresa($strUsr,$strCodEmpresa);
            if (!empty($arrayEmp))
            {
                $intIdDepartamento = $arrayEmp['ID_DEPARTAMENTO'];
            }
            $arrayParametros                         = array();
            $arrayParametros['strUserDbSoporte']     = $strUserDbSoporte;
            $arrayParametros['strPasswordDbSoporte'] = $strPasswordDbSoporte;
            $arrayParametros['strDatabaseDsn']       = $strDatabaseDsn;
            $arrayParametros['strCodEmpresa']        = $strCodEmpresa;
            $arrayParametros['strIdDepartamento']    = $intIdDepartamento;
            $arrayParametros['strEstado']            = ($strEstado == 'TODOS') ? null : $strEstado;
            $arrayParametros['strTabVisible']        = $strTabVisible;
            $arrayParametros['strUsrSesion']         = $strUsr;

            $strResultado = $objSoporteService->enviarListadoDePendientes($arrayParametros);
            if ($strResultado == 'Ok')
            {
                $objJsonRespuesta->setData(['mensaje'=> 'El correo fue enviado con exito!','status' => 200]);
            }
            else
            {
                throw new \Exception($strResultado);
            }
        }
        catch(\Exception $objE)
        {
            $strMensajeError= 'Ocurrio un error, no se pudo enviar el correo de listado de pendientes';
            if ($objE->getMessage() == 'No se encontró correos destinatarios')
            {
                $strMensajeError= 'Ocurrio un error, '.$objE->getMessage();
            }
            elseif($objE->getMessage() == 'módulo no configurado')
            {
                $strMensajeError= 'Ocurrio un error, '.$objE->getMessage().' para el uso de esta opción';
            }
            $objJsonRespuesta->setData(['mensaje'=> $strMensajeError,'status' => 500,'data'=>array()]);
            $objServiceUtil->insertError($strUsr,'SoporteBundle.GestionPendientesController.ajaxEnviarListadoPendientesAction',
                                        'Error al consultar los pendientes. '.$objE->getMessage(),$strUsr,$strIpCreacion);
        }
        return $objJsonRespuesta;
    }

    /**
     * Función que realiza el proceso de envío de reporte de labores
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 13-08-2021
     * @since 1.0
     * @return JsonResponse
     */
    public function ajaxEnviarReporteLaboresAction()
    {
        $objJsonRespuesta     = new JsonResponse();
        $objServiceUtil       = $this->get('schema.Util');
        $objSoporteService    = $this->get('soporte.SoporteService');
        $objSession           = $this->get('request')->getSession();
        $strIpCreacion        = $this->get('request')->getClientIp();
        $strCodEmpresa        = $objSession->get('idEmpresa');
        $strUsr               = $objSession->get('user');
        $objRequest           = $this->get('request');
        $strTurno             = $objRequest->get('turno');
        try
        {
            $arrayParametros             = array();
            $arrayParametros['strTurno'] = $strTurno;
            $arrayParametros['strUser']  = $strUsr;

            $arrayResultado = $objSoporteService->enviarReporteLabores($arrayParametros);

            if ($arrayResultado['mensaje'] == 'reporte enviado')
            {
                $objJsonRespuesta->setData(['mensaje'=> $arrayResultado['mensaje'],'status' => 200]);
            }
            else
            {
                throw new \Exception('No se pudo enviar el reporte');
            }
        }
        catch(\Exception $objE)
        {
            $strMensajeError= 'Ocurrio un error, No se pudo enviar reporte de labores diarias';
            $objJsonRespuesta->setData(['mensaje'=> $strMensajeError,'status' => 500,'data'=>array()]);
            $objServiceUtil->insertError($strUsr,'SoporteBundle.GestionPendientesController.ajaxEnviarReporteLaboresAction',
                                        'Error al enviar reporte de labores: '.$objE->getMessage(),$strUsr,$strIpCreacion);
        }
        return $objJsonRespuesta;
    }
}
