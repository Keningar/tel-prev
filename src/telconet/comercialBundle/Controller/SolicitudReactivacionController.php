<?php

namespace telconet\comercialBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoDetalleSolCaract;

use telconet\schemaBundle\Entity\InfoPersona;
use telconet\schemaBundle\Entity\AdmiProducto;
use telconet\schemaBundle\Entity\AdmiParametroDet;
use telconet\schemaBundle\Entity\AdmiDepartamento;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoServicio;
use telconet\schemaBundle\Entity\AdmiCaracteristica;
use telconet\schemaBundle\Entity\AdmiProductoCaracteristica;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRol;
use telconet\schemaBundle\Entity\InfoServicioProdCaract;

use telconet\schemaBundle\Form\InfoDetalleSolicitudType;
use Symfony\Component\HttpFoundation\Response;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;
use telconet\schemaBundle\Service\UtilService;


class SolicitudReactivacionController extends Controller implements TokenAuthenticatedController
{
     private $strTipoSolicitud  = 'SOLICITUD DE REACTIVACION';
     private $strCaracClt       = 'REFERENCIA_CLIENTE';
     private $strCaracUsuario   = 'REFERENCIA_USUARIO';
     private $strCaracUsuarioC  = 'REFERENCIA_USUARIO_COBRANZA';
     private $strCaracTarea     = 'REFERENCIA_TAREA';
     private $strCaracSaldoP    = 'REFERENCIA_SALDO_P';
     private $strCaracSaldoR    = 'REFERENCIA_SALDO_R';

   /**
     * @Secure(roles="ROLE_453-1")
     *
     * Documentación para la función 'indexAction'.
     *
     * Función que carga la pantalla de solicitud de reactivación.
     *
     * @return render Redirecciona al index de la opción.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 11-11-2020
     *
     */
     public function indexAction()
     {
        try
        {
            $objRequest           = $this->getRequest();
            $strUsrCreacion       = $objRequest->getSession()->get('user');
            $strIpCreacion        = $objRequest->getClientIp();
            $serviceUtil          = $this->get('schema.Util');
            $arrayRolesPermitidos = array();

            if( $this->get('security.context')->isGranted('ROLE_453-1') )
            {
                $arrayRolesPermitidos[] = 'ROLE_453-1';
            }
            if( $this->get('security.context')->isGranted('ROLE_453-6') )
            {
                $arrayRolesPermitidos[] = 'ROLE_453-6';
            }
            if( $this->get('security.context')->isGranted('ROLE_453-7') )
            {
                $arrayRolesPermitidos[] = 'ROLE_453-7';
            }
            if( $this->get('security.context')->isGranted('ROLE_453-163') )
            {
                $arrayRolesPermitidos[] = 'ROLE_453-163';
            }
            if( $this->get('security.context')->isGranted('ROLE_453-94') )
            {
                $arrayRolesPermitidos[] = 'ROLE_453-94';
            }
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError('TelcoS+', 'SolicitudReactivacionController.indexAction', $e->getMessage(), $strUsrCreacion, $strIpCreacion);
        }
        return $this->render('comercialBundle:SolicitudReactivacion:index.html.twig', array('rolesPermitidos'  => $arrayRolesPermitidos));
     }

   /**
     * @Secure(roles="ROLE_453-7")
     *
     * Documentación para la función 'gridAction'.
     *
     * Función que retorna el listado de solicitudes.
     *
     * @return $objResponse - Listado de Solicitudes.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 11-11-2020
     *
     */
    public function gridAction()
    {
        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $strIdentificacion      = $objRequest->get("strIdentificacion")   ? $objRequest->get("strIdentificacion"):"";
        $strNombre              = $objRequest->get("strNombre")           ? $objRequest->get("strNombre"):"";
        $strApellido            = $objRequest->get("strApellido")         ? $objRequest->get("strApellido"):"";
        $strRazonSocial         = $objRequest->get("strRazonSocial")      ? $objRequest->get("strRazonSocial"):"";
        $strFechaInicio         = $objRequest->get("strFechaInicio")      ? $objRequest->get("strFechaInicio"):"";
        $strFechaFin            = $objRequest->get("strFechaFin")         ? $objRequest->get("strFechaFin"):"";
        $strEstado              = $objRequest->get("strEstado")           ? $objRequest->get("strEstado"):"";
        $intIdEmpresa           = $objSession->get('idEmpresa')           ? $objSession->get('idEmpresa'):"";
        $intIdPersonaEmpresaRol = $objSession->get('idPersonaEmpresaRol') ? $objSession->get('idPersonaEmpresaRol'):"";
        $strUsrCreacion         = $objSession->get('user')                ? $objSession->get('user'):"";
        $strIpCreacion          = $objRequest->getClientIp()              ? $objRequest->getClientIp():'127.0.0.1';
        $strPrefijoEmpresa      = $objSession->get('prefijoEmpresa')      ? $objSession->get('prefijoEmpresa'):"";
        $emComercial            = $this->get('doctrine')->getManager('telconet');
        $serviceUtil            = $this->get('schema.Util');
        $strTipoProyecto        = "";
        $intTotal               = 0;
        $arraySolicitud         = array();
        $strTipoPersonal        = 'Otros';
        try
        {
            $arrayListUsuarios = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                             ->get('PARAMETROS_SOLICITUD_REACTIVACION', 
                                                   'COMERCIAL', 
                                                   '', 
                                                   'LISTADO_USUARIOS', 
                                                   $strUsrCreacion, 
                                                   '', 
                                                   '', 
                                                   '', 
                                                   '', 
                                                   $intIdEmpresa);
            if(!empty($arrayListUsuarios) && is_array($arrayListUsuarios))
            {
                $arrayListUsuarios = $arrayListUsuarios[0];
                $strTipoPersonal   = $arrayListUsuarios['valor6'] ? $arrayListUsuarios['valor6']  : 'Otros';
                $strEstado         = $strEstado ? $strEstado : $arrayListUsuarios['valor4'];
            }
            else
            {
                $arrayResultadoCaracteristicas = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                             ->getCargosPersonas($strUsrCreacion);
                if(!empty($arrayResultadoCaracteristicas))
                {
                    $arrayResultadoCaracteristicas = $arrayResultadoCaracteristicas[0];
                    $strTipoPersonal               = $arrayResultadoCaracteristicas['STRCARGOPERSONAL'] ? 
                                                     $arrayResultadoCaracteristicas['STRCARGOPERSONAL'] : 'Otros';
                }
            }
            $arrayParametros                      = array();
            $arrayParametros['strIdentificacion'] = $strIdentificacion;
            $arrayParametros['strNombre']         = $strNombre;
            $arrayParametros['strApellido']       = $strApellido;
            $arrayParametros['strRazonSocial']    = $strRazonSocial;
            $arrayParametros['strFechaInicio']    = $strFechaInicio;
            $arrayParametros['strFechaFin']       = $strFechaFin;
            $arrayParametros['strTipoSolicitud']  = $this->strTipoSolicitud;
            $arrayParametros['strCaracClt']       = $this->strCaracClt;
            $arrayParametros['strCaracUsuario']   = $this->strCaracUsuario;
            $arrayParametros['strCaracUsuarioC']  = $this->strCaracUsuarioC;
            $arrayParametros['strCaracTarea']     = $this->strCaracTarea;
            $arrayParametros['strCaracSaldoP']    = $this->strCaracSaldoP;
            $arrayParametros['strCaracSaldoR']    = $this->strCaracSaldoR;
            $arrayParametros['strTipoPersonal']   = $strTipoPersonal;
            $arrayParametros['strUsrCreacion']    = $strUsrCreacion;
            $arrayParametros['strPrefijoEmpresa'] = $strPrefijoEmpresa;
            $arrayParametros['strEstado']         = $strEstado;

            $arrayResultado = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                          ->getSolicitudReactivacion($arrayParametros);

            if(isset($arrayResultado['error']) && !empty($arrayResultado['error']))
            {
                throw new \Exception($arrayResultado['error']);
            }

            if(!empty($arrayResultado["registros"]) && isset($arrayResultado["registros"]))
            {
                $arrayRegistros   = $arrayResultado['registros'];
                $intTotal         = $arrayResultado['total'];

                foreach($arrayRegistros as $arrayDatos)
                {
                    $arrayDataLink    = array('intIdSolicitud'  => $arrayDatos["ID_DETALLE_SOLICITUD"],
                                              'strEstado'       => $arrayDatos["ESTADO"]);
                    $strLinkVer       = array('linkVer'=> $this->generateUrl('show_solicitud_reactivacion',$arrayDataLink));
                    $arraySolicitud[] = array('intIdSolicitud'    => $arrayDatos["ID_DETALLE_SOLICITUD"],
                                              'intIdTarea'        => $arrayDatos["TAREA"],
                                              'strCliente'        => ucwords(strtolower($arrayDatos["NOMBRE_CLIENTE"])),
                                              'strIdentificacion' => $arrayDatos["IDENTIFICACION_CLIENTE"],
                                              'strEstado'         => $arrayDatos["ESTADO"],
                                              'strObservacion'    => $arrayDatos["OBSERVACION"],
                                              'strFeCreacion'     => $arrayDatos["FE_CREACION"],
                                              'strUsrCreacion'    => $arrayDatos["USR_CREACION"],
                                              'strAcciones'       => $strLinkVer);
                }
            }
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError('TelcoS+', 'SolicitudReactivacionController.gridAction', $e->getMessage(), $strUsrCreacion, $strIpCreacion);
        }
        $objResponse = new Response(json_encode(array('intTotal' => $intTotal, 'data' => $arraySolicitud)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

   /**
     * @Secure(roles="ROLE_453-6")
     *
     * Documentación para la función 'showAction'.
     *
     * Función que renderiza la página de Ver detalle de solicitudes.
     *
     * @param int $intIdSolicitud  => id de la solicitud.
     * @param string $strEstado    => estado de la solicitud.
     *
     * @return render - Página de Ver Solicitud.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 11-11-2020
     *
     */
    public function showAction($intIdSolicitud,$strEstado)
    {
        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $strUsrCreacion         = $objSession->get('user')           ? $objSession->get('user'):"";
        $strIpCreacion          = $objRequest->getClientIp()         ? $objRequest->getClientIp():'127.0.0.1';
        $strPrefijoEmpresa      = $objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa'):"";
        $serviceUtil            = $this->get('schema.Util');
        $emComercial            = $this->get('doctrine')->getManager('telconet');
        try
        {
            if(empty($intIdSolicitud))
            {
                throw new \Exception("El identificador es un parámetro obligatorio.");
            }
            $intTotal                             = 0;
            $arraySolicitud                       = array();
            $arraySolicitudDet                    = array();
            $arrayParametros                      = array();
            $arrayParametros['intIdSolicitud']    = $intIdSolicitud;
            $arrayParametros['strEstado']         = $strEstado;
            $arrayParametros['strTipoSolicitud']  = $this->strTipoSolicitud;
            $arrayParametros['strCaracClt']       = $this->strCaracClt;
            $arrayParametros['strCaracTarea']     = $this->strCaracTarea;
            $arrayParametros['strCaracUsuario']   = $this->strCaracUsuario;
            $arrayParametros['strCaracSaldoP']    = $this->strCaracSaldoP;
            $arrayParametros['strCaracSaldoR']    = $this->strCaracSaldoR;
            $arrayParametros['strPrefijoEmpresa'] = $strPrefijoEmpresa;
            $arrayResultado                       = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                ->getSolicitudReactivacion($arrayParametros);
            if(isset($arrayResultado['error']) && !empty($arrayResultado['error']))
            {
                throw new \Exception($arrayResultado['error']);
            }
            if(!empty($arrayResultado["registros"])&&isset($arrayResultado["registros"]))
            {
                $arrayRegistros = $arrayResultado['registros'];
                foreach($arrayRegistros as $arrayDatos)
                {
                    $strUsuario     = "";
                    $objInfoPersona = $emComercial->getRepository("schemaBundle:InfoPersona")
                                                  ->findOneByLogin($arrayDatos["US_ASIGNADO"]);
                    if(!empty($objInfoPersona) && is_object($objInfoPersona))
                    {
                        $strUsuario = ucwords(strtolower($objInfoPersona->getNombres()." ".$objInfoPersona->getApellidos()));
                    }
                    $strSaldoPendiente = "Por definir";
                    $strSaldoReal      = "Por definir";
                    if(!empty($arrayDatos["SALDO_P"]) && isset($arrayDatos["SALDO_P"]))
                    {
                        $strSaldoPendiente = "$".$arrayDatos["SALDO_P"];
                    }
                    if(!empty($arrayDatos["SALDO_R"]) && isset($arrayDatos["SALDO_R"]))
                    {
                        $strSaldoReal = "$".$arrayDatos["SALDO_R"];
                    }

                    $arraySolicitud = array('intIdSolicitud'      => $arrayDatos["ID_DETALLE_SOLICITUD"],
                                            'intIdTarea'          => $arrayDatos["TAREA"],
                                            'strCliente'          => ucwords(strtolower($arrayDatos["NOMBRE_CLIENTE"])),
                                            'strIdentificacion'   => $arrayDatos["IDENTIFICACION_CLIENTE"],
                                            'strEstado'           => $arrayDatos["ESTADO"],
                                            'strUsAsignado'       => $strUsuario,
                                            'strSaldoPendiente'   => $strSaldoPendiente,
                                            'strSaldoReal'        => $strSaldoReal,
                                            'strObservacion'      => $arrayDatos["OBSERVACION"],
                                            'strFeCreacion'       => $arrayDatos["FE_CREACION"],
                                            'strUsrCreacion'      => $arrayDatos["USR_CREACION"],
                                            'strDireccion'        => $arrayDatos["DIRECCION_TRIBUTARIA"]);
                }
            }
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError('TelcoS+', 'SolicitudReactivacionController.showAction', $e->getMessage(), $strUsrCreacion, $strIpCreacion);
        }
        return $this->render('comercialBundle:SolicitudReactivacion:show.html.twig',array('arraySolicitudDet' => $arraySolicitud));
    }

   /**
     * Documentación para la función 'getAprobarRechazarAction'.
     *
     * Función que aprueba o rechaza las solicitudes.
     *
     * @return $strResponse - Respuesta de confirmación.
     * 
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 11-11-2020
     */
    public function getAprobarRechazarAction()
    {
        $objRequest              = $this->getRequest();
        $objSession              = $objRequest->getSession();
        $strSaldoPendienteReal   = $objRequest->get("strSaldoPendienteReal")     ? $objRequest->get("strSaldoPendienteReal"):"";
        $intIdMotivoRechazar     = $objRequest->get("intIdMotivoRechazar")     ? $objRequest->get("intIdMotivoRechazar"):"";
        $strObservacionAprobar   = $objRequest->get("strObservacionAprobar")   ? $objRequest->get("strObservacionAprobar"):"";
        $strObservacionRechazar  = $objRequest->get("strObservacionRechazar")  ? $objRequest->get("strObservacionRechazar"):"";
        $strObservacionAsignar   = $objRequest->get("strObservacionAsignar")   ? $objRequest->get("strObservacionAsignar"):"";
        $strAccion               = $objRequest->get("strAccion")               ? $objRequest->get("strAccion"):"";
        $arraySolicitudes        = $objRequest->get("arraySolicitudes")        ? $objRequest->get("arraySolicitudes"):"";
        $strPrefijoEmpresa       = $objSession->get('prefijoEmpresa')          ? $objSession->get('prefijoEmpresa'):"";
        $intIdEmpresa            = $objSession->get('idEmpresa')               ? $objSession->get('idEmpresa'):"";
        $intIdPersonaEmpresaRol  = $objSession->get('idPersonaEmpresaRol')     ? $objSession->get('idPersonaEmpresaRol'):"";
        $strUsrCreacion          = $objSession->get('user')                    ? $objSession->get('user'):"";
        $intIdOficinaSesion      = $objSession->get('idOficina')               ? $objSession->get('idOficina') : 0;
        $strIpCreacion           = $objRequest->getClientIp()                  ? $objRequest->getClientIp():'127.0.0.1';
        $emComercial             = $this->get('doctrine')->getManager('telconet');
        $serviceEnvioPlantilla   = $this->get('soporte.EnvioPlantilla');
        $serviceUtil             = $this->get('schema.Util');
        $serviceComercial        = $this->get('comercial.Comercial');
        $serviceCliente          = $this->get('comercial.Cliente');
        $servicePreCliente       = $this->get('comercial.PreCliente');
        $strEstadoSol            = "";
        try
        {
            $arrayListUsuarios = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                             ->get('PARAMETROS_SOLICITUD_REACTIVACION', 
                                                   'COMERCIAL', 
                                                   '', 
                                                   'LISTADO_USUARIOS', 
                                                   $strUsrCreacion, 
                                                   '', 
                                                   '', 
                                                   '', 
                                                   '', 
                                                   $intIdEmpresa);
            if(!empty($arrayListUsuarios) && is_array($arrayListUsuarios))
            {
                $arrayListUsuarios = $arrayListUsuarios[0];
                $strTipoPersonal   = $arrayListUsuarios['valor1'] ? "USUARIO_GESTION_SOLICITUD"  : 'Otros';
                $strEstado         = $strEstado ? $strEstado : $arrayListUsuarios['valor4'];
            }
            if(empty($strTipoPersonal) || $strTipoPersonal != "USUARIO_GESTION_SOLICITUD")
            {
                throw new \Exception("Persona en sesión no tiene el perfil para poder ".$strAccion." la solicitud.");
            }
            if(empty($strEstado))
            {
                throw new \Exception("Ocurrió un error al cargar el estado.");
            }

            if( empty($arraySolicitudes) || !is_array($arraySolicitudes) )
            {
                throw new \Exception('El listado de solicitudes es un campo obligatorio.');
            }
            $arrayDestinatarios                   = array();
            $arrayParametros                      = array();
            $arrayParametros['intIdEmpresa']      = $intIdEmpresa;
            $arrayParametros['strTipoSolicitud']  = $this->strTipoSolicitud;
            $arrayParametros['strCaracClt']       = $this->strCaracClt;
            $arrayParametros['strCaracTarea']     = $this->strCaracTarea;
            $arrayParametros['strCaracUsuario']   = $this->strCaracUsuario;
            $arrayParametros['strCaracSaldoP']    = $this->strCaracSaldoP;
            $arrayParametros['strCaracSaldoR']    = $this->strCaracSaldoR;
            $arrayParametros['strEstado']         = $strEstado;
            $arrayParametros['strPrefijoEmpresa'] = $strPrefijoEmpresa;

            $emComercial->getConnection()->beginTransaction();

            $arrayParametros['intIdSolicitud']  = $arraySolicitudes;
            $arrayResultado                     = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                              ->getSolicitudReactivacion($arrayParametros);
            if(!empty($arrayResultado["error"]) && isset($arrayResultado["error"]))
            {
                throw new \Exception($arrayResultado["error"]);
            }
            if(empty($arrayResultado["registros"]) && isset($arrayResultado["registros"]))
            {
                throw new \Exception("No existen registros");
            }

            if($strAccion=="aprobar")
            {
                $strEstadoSol = "Aprobada";
                foreach($arrayResultado['registros'] as $arrayDatos)
                {
                    $objInfoPersona = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                  ->find($arrayDatos["ID_PERSONA"]);
                    if(empty($objInfoPersona) || !is_object($objInfoPersona))
                    {
                        throw new \Exception('No existen datos del cliente.');
                    }
                    $arrayDatosPreCliente = $serviceCliente->obtenerDatosClientePorIdentificacion($intIdEmpresa,
                                                                                                  $objInfoPersona->getIdentificacionCliente(),
                                                                                                  $strPrefijoEmpresa);
                    if(empty($arrayDatosPreCliente) || !is_array($arrayDatosPreCliente))
                    {
                        throw new \Exception('No existen datos del cliente.');
                    }
                    $arrayFormaContacto = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                      ->findBy(array("personaId"=>$objInfoPersona->getId()));
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
                    $arrayDatosPreCliente['fechaNacimiento']      = $objInfoPersona->getFechaNacimiento();
                    $arrayDatosPreCliente['origenIngresos']       = $objInfoPersona->getOrigenIngresos();
                    $arrayDatosPreCliente['idOficinaFacturacion'] = $intIdOficinaSesion;

                    $arrayParametrosPreCliente = array('strCodEmpresa'        => $intIdEmpresa,
                                                       'strUsrCreacion'       => $arrayDatos["USR_CREACION"],
                                                       'strClientIp'          => $strIpCreacion,
                                                       'arrayDatosForm'       => $arrayDatosPreCliente,
                                                       'strPrefijoEmpresa'    => $strPrefijoEmpresa,
                                                       'arrayFormasContacto'  => $arrayFormaContactoPreclt);
                    $objPersonaEmpresaRol =  $servicePreCliente->crearPreCliente($objPersona,$arrayParametrosPreCliente);

                    $arrayCorreos = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                ->getContactosByLoginPersonaAndFormaContacto($arrayDatos["USR_CREACION"],
                                                                                             'Correo Electronico');
                    if(!empty($arrayCorreos) && is_array($arrayCorreos))
                    {
                        foreach($arrayCorreos as $arrayItem)
                        {
                            if(!empty($arrayItem['valor']) && isset($arrayItem['valor']))
                            {
                                $arrayDestinatarios[] = $arrayItem['valor'];
                            }
                        }
                    }
                    $arrayParametrosMail  = array("strNombreCliente"         => $arrayDatos["NOMBRE_CLIENTE"],
                                                  "strIdentificacionCliente" => $arrayDatos["IDENTIFICACION_CLIENTE"],
                                                  "strObservacion"           => $strObservacionAprobar);
                    $serviceEnvioPlantilla->generarEnvioPlantilla("SOLICITUD DE REACTIVACIÓN APROBADA", 
                                                                  array_unique($arrayDestinatarios), 
                                                                  'APRUEBA_SOL_REA',
                                                                  $arrayParametrosMail,
                                                                  $strPrefijoEmpresa,
                                                                  '',
                                                                  '',
                                                                  null, 
                                                                  true,
                                                                  'notificaciones_telcos@telconet.ec');
                    $entityDetalleSolicitud         = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                  ->findOneById($arrayDatos["ID_DETALLE_SOLICITUD"]);
                    $arrayInfoDetSolCaracteristicas = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                    ->findBy(array('estado'             => $arrayDatos["ESTADO"],
                                                                                   'detalleSolicitudId' => $entityDetalleSolicitud));
                    if(!empty($arrayInfoDetSolCaracteristicas) && is_array($arrayInfoDetSolCaracteristicas))
                    {
                        foreach($arrayInfoDetSolCaracteristicas as $objInfoDetalleSolCaract)
                        {
                            if(is_object($objInfoDetalleSolCaract) && !empty($objInfoDetalleSolCaract))
                            {
                                $objInfoDetalleSolCaract->setEstado($strEstadoSol);
                                $objInfoDetalleSolCaract->setFeUltMod(new \DateTime('now'));
                                $objInfoDetalleSolCaract->setUsrUltMod($strUsrCreacion);
                                $emComercial->persist($objInfoDetalleSolCaract);
                                $emComercial->flush();
                            }
                        }
                    }
                    $entityDetalleSolicitud->setObservacion($strObservacionAprobar);
                    $entityDetalleSolicitud->setEstado($strEstadoSol);
                    $entityDetalleSolicitud->setUsrRechazo($strUsrCreacion);
                    $entityDetalleSolicitud->setFeRechazo(new \DateTime('now'));
                    $emComercial->persist($entityDetalleSolicitud);
                    $emComercial->flush();

                    $entityDetalleSolHist = new InfoDetalleSolHist();
                    $entityDetalleSolHist->setDetalleSolicitudId($entityDetalleSolicitud);
                    $entityDetalleSolHist->setObservacion($strObservacionAprobar);
                    $entityDetalleSolHist->setIpCreacion($strIpCreacion);
                    $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
                    $entityDetalleSolHist->setUsrCreacion($strUsrCreacion);
                    $entityDetalleSolHist->setEstado($strEstadoSol);
                    $emComercial->persist($entityDetalleSolHist);
                    $emComercial->flush();
                }
            }
            else if($strAccion=="asignar")
            {
                $strEstadoSol = "Asignada";
                $objAdmiCaracSaldoPendR = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                      ->findOneBy(array("descripcionCaracteristica" => $this->strCaracSaldoR,
                                                                        "estado"                    => "Activo"));
                if(!is_object($objAdmiCaracSaldoPendR) && empty($objAdmiCaracSaldoPendR))
                {
                    throw new \Exception("No existe Objeto para la característica REFERENCIA_SALDO_R");
                }
                foreach($arrayResultado['registros'] as $arrayDatos)
                {
                    $arrayCorreos = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                ->getContactosByLoginPersonaAndFormaContacto($arrayDatos["USR_CREACION"],
                                                                                             'Correo Electronico');
                    if(!empty($arrayCorreos) && is_array($arrayCorreos))
                    {
                        foreach($arrayCorreos as $arrayItem)
                        {
                            if(!empty($arrayItem['valor']) && isset($arrayItem['valor']))
                            {
                                $arrayDestinatarios[] = $arrayItem['valor'];
                            }
                        }
                    }
                    $arrayCorreos = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                ->getContactosByLoginPersonaAndFormaContacto($arrayDatos["US_ASIGNADO"],
                                                                                             'Correo Electronico');
                    if(!empty($arrayCorreos) && is_array($arrayCorreos))
                    {
                        foreach($arrayCorreos as $arrayItem)
                        {
                            if(!empty($arrayItem['valor']) && isset($arrayItem['valor']))
                            {
                                $arrayDestinatarios[] = $arrayItem['valor'];
                            }
                        }
                    }
                    $arrayParametrosMail  = array("strNombreCliente"         => $arrayDatos["NOMBRE_CLIENTE"],
                                                  "strIdentificacionCliente" => $arrayDatos["IDENTIFICACION_CLIENTE"],
                                                  "strObservacion"           => $strObservacionAsignar);
                    $serviceEnvioPlantilla->generarEnvioPlantilla("SOLICITUD DE REACTIVACIÓN ASIGNADA", 
                                                                  array_unique($arrayDestinatarios), 
                                                                  'ASIGNA_SOL_REA',
                                                                  $arrayParametrosMail,
                                                                  $strPrefijoEmpresa,
                                                                  '',
                                                                  '',
                                                                  null, 
                                                                  true,
                                                                  'notificaciones_telcos@telconet.ec');
                    $entityDetalleSolicitud         = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                    ->findOneById($arrayDatos["ID_DETALLE_SOLICITUD"]);
                    $strObservacion                 = $entityDetalleSolicitud->getObservacion().
                                                        ", Observación de cobranza: ".$strObservacionAsignar;
                    $arrayInfoDetSolCaracteristicas = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                  ->findBy(array('estado'             => $arrayDatos["ESTADO"],
                                                                                 'detalleSolicitudId' => $entityDetalleSolicitud));
                    if(!empty($arrayInfoDetSolCaracteristicas) && is_array($arrayInfoDetSolCaracteristicas))
                    {
                        foreach($arrayInfoDetSolCaracteristicas as $objInfoDetalleSolCaract)
                        {
                            if(is_object($objInfoDetalleSolCaract) && !empty($objInfoDetalleSolCaract))
                            {
                                $objInfoDetalleSolCaract->setEstado($strEstadoSol);
                                $objInfoDetalleSolCaract->setFeUltMod(new \DateTime('now'));
                                $objInfoDetalleSolCaract->setUsrUltMod($strUsrCreacion);
                                $emComercial->persist($objInfoDetalleSolCaract);
                                $emComercial->flush();
                            }
                        }
                    }
                    $entityDetalleSolicitud->setObservacion($strObservacion);
                    $entityDetalleSolicitud->setEstado($strEstadoSol);
                    $entityDetalleSolicitud->setUsrRechazo($strUsrCreacion);
                    $entityDetalleSolicitud->setFeRechazo(new \DateTime('now'));
                    $emComercial->persist($entityDetalleSolicitud);
                    $emComercial->flush();

                    $entityDetalleSolHist = new InfoDetalleSolHist();
                    $entityDetalleSolHist->setDetalleSolicitudId($entityDetalleSolicitud);
                    $entityDetalleSolHist->setObservacion($strObservacion);
                    $entityDetalleSolHist->setIpCreacion($strIpCreacion);
                    $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
                    $entityDetalleSolHist->setUsrCreacion($strUsrCreacion);
                    $entityDetalleSolHist->setEstado($strEstadoSol);
                    $emComercial->persist($entityDetalleSolHist);
                    $emComercial->flush();

                    $objDetTipoSolReactivacionCarac = new InfoDetalleSolCaract();
                    $objDetTipoSolReactivacionCarac->setCaracteristicaId($objAdmiCaracSaldoPendR);
                    $objDetTipoSolReactivacionCarac->setValor($strSaldoPendienteReal);
                    $objDetTipoSolReactivacionCarac->setDetalleSolicitudId($entityDetalleSolicitud);
                    $objDetTipoSolReactivacionCarac->setEstado($strEstadoSol);
                    $objDetTipoSolReactivacionCarac->setFeCreacion(new \DateTime('now'));
                    $objDetTipoSolReactivacionCarac->setUsrCreacion($strUsrCreacion);
                    $emComercial->persist($objDetTipoSolReactivacionCarac);
                    $emComercial->flush();
                }
            }
            else if($strAccion=="rechazar")
            {
                $strEstadoSol   = "Rechazada";
                foreach($arrayResultado['registros'] as $arrayDatos)
                {
                    $arrayCorreos = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                ->getContactosByLoginPersonaAndFormaContacto($arrayDatos["USR_CREACION"],
                                                                                            'Correo Electronico');
                    if(!empty($arrayCorreos) && is_array($arrayCorreos))
                    {
                        foreach($arrayCorreos as $arrayItem)
                        {
                            if(!empty($arrayItem['valor']) && isset($arrayItem['valor']))
                            {
                                $arrayDestinatarios[] = $arrayItem['valor'];
                            }
                        }
                    }
                    $arrayParametrosMail  = array("strNombreCliente"         => $arrayDatos["NOMBRE_CLIENTE"],
                                                  "strIdentificacionCliente" => $arrayDatos["IDENTIFICACION_CLIENTE"],
                                                  "strObservacion"           => $strObservacionRechazar);
                    $serviceEnvioPlantilla->generarEnvioPlantilla("SOLICITUD DE REACTIVACIÓN RECHAZADA", 
                                                                  array_unique($arrayDestinatarios), 
                                                                  'RECHAZA_SOL_REA', 
                                                                  $arrayParametrosMail,
                                                                  $strPrefijoEmpresa,
                                                                  '',
                                                                  '',
                                                                  null, 
                                                                  true,
                                                                  'notificaciones_telcos@telconet.ec');
                    $entityDetalleSolicitud         = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                  ->findOneById($arrayDatos["ID_DETALLE_SOLICITUD"]);
                    $arrayInfoDetSolCaracteristicas = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                  ->findBy(array('estado'             => $arrayDatos["ESTADO"],
                                                                                    'detalleSolicitudId' => $entityDetalleSolicitud));
                    if(!empty($arrayInfoDetSolCaracteristicas) && is_array($arrayInfoDetSolCaracteristicas))
                    {
                        foreach($arrayInfoDetSolCaracteristicas as $objInfoDetalleSolCaract)
                        {
                            if(is_object($objInfoDetalleSolCaract) && !empty($objInfoDetalleSolCaract))
                            {
                                $objInfoDetalleSolCaract->setEstado($strEstadoSol);
                                $objInfoDetalleSolCaract->setFeUltMod(new \DateTime('now'));
                                $objInfoDetalleSolCaract->setUsrUltMod($strUsrCreacion);
                                $emComercial->persist($objInfoDetalleSolCaract);
                                $emComercial->flush();
                            }
                        }
                    }
                    $entityDetalleSolicitud->setObservacion($strObservacionRechazar);
                    $entityDetalleSolicitud->setEstado($strEstadoSol);
                    $entityDetalleSolicitud->setUsrRechazo($strUsrCreacion);
                    $entityDetalleSolicitud->setFeRechazo(new \DateTime('now'));
                    if( !empty($intIdMotivoRechazar) )
                    {
                        $entityDetalleSolicitud->setMotivoId($intIdMotivoRechazar);
                    }
                    $emComercial->persist($entityDetalleSolicitud);
                    $emComercial->flush();

                    $entityDetalleSolHist = new InfoDetalleSolHist();
                    $entityDetalleSolHist->setDetalleSolicitudId($entityDetalleSolicitud);
                    $entityDetalleSolHist->setObservacion($strObservacionRechazar);
                    $entityDetalleSolHist->setIpCreacion($strIpCreacion);
                    $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
                    $entityDetalleSolHist->setUsrCreacion($strUsrCreacion);
                    $entityDetalleSolHist->setEstado($strEstadoSol);
                    if( !empty($intIdMotivoRechazar) )
                    {
                        $entityDetalleSolHist->setMotivoId($intIdMotivoRechazar);
                    }
                    $emComercial->persist($entityDetalleSolHist);
                    $emComercial->flush();
                }
            }
            if( $emComercial->getConnection()->isTransactionActive() )
            {
                $emComercial->getConnection()->commit();
                $emComercial->getConnection()->close();
            }
            $strResponse = "Acción ejecutada correctamente.";
        }
        catch(\Exception $e)
        {
            if( $emComercial->getConnection()->isTransactionActive() )
            {
                $emComercial->getConnection()->rollback();
                $emComercial->getConnection()->close();
            }
            $strResponse = "Ocurrió un error al ejecutar la acción, por favor comuniquese con Sistemas";
            $serviceUtil->insertError('TelcoS+', 
                                      'SolicitudReactivacionController.getAprobarRechazarSolicitudAction', 
                                      $strResponse . $e->getMessage(), 
                                      $strUsrCreacion, 
                                      $strIpCreacion);
        }
        return new Response($strResponse);
    }

   /**
     * Documentación para la función 'getMotivosAction'.
     *
     * Función que obtiene los motivos de las solicitudes de proyecto.
     *
     * @return Response - Lista de motivos.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 11-11-2020
     *
     */
    public function getMotivosAction()
    {
        try
        {
            $objRequest             = $this->getRequest();
            $objSession             = $objRequest->getSession();
            $strUsrCreacion         = $objSession->get('user')   ? $objSession->get('user'):"";
            $strIpCreacion          = $objRequest->getClientIp() ? $objRequest->getClientIp():'127.0.0.1';
            $arrayMotivos           = array();
            $emGeneral              = $this->get('doctrine')->getManager('telconet');
            $serviceUtil            = $this->get('schema.Util');

            $arrayListMotivos = $emGeneral->getRepository('schemaBundle:AdmiMotivo')
                                          ->findMotivosPorModuloPorItemMenuPorAccion('admiSolicitudReactivacion','Solicitud de Reactivación','index');
            if(!empty($arrayListMotivos) && is_array($arrayMotivos))
            {
                foreach($arrayListMotivos as $arrayItem)
                {
                    $arrayMotivos[] = array('id'     => $arrayItem->getId(),
                                            'nombre' => ucwords(strtolower($arrayItem->getNombreMotivo())));
                }
            }
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError('TelcoS+', 
                                      'SolicitudReactivacionController.getMotivosAction', 
                                      $e->getMessage(),
                                      $strUsrCreacion,
                                      $strIpCreacion);
        }
        $objResponse = new Response(json_encode(array('motivos' => $arrayMotivos)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
}