<?php

namespace telconet\comercialBundle\Controller;

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
use telconet\schemaBundle\Entity\AdmiCaracteristica;
use telconet\schemaBundle\Entity\AdmiProductoCaracteristica;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRol;
use telconet\schemaBundle\Entity\InfoServicioProdCaract;

use telconet\schemaBundle\Form\InfoDetalleSolicitudType;
use Symfony\Component\HttpFoundation\Response;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;
use telconet\schemaBundle\Service\UtilService;


class SolicitudProyectoController extends Controller implements TokenAuthenticatedController
{
     private $strTipoSolicitud  = 'SOLICITUD DE PROYECTO';
     private $strCaracteristica = 'COTIZACION_PRODUCTOS';
     private $strCaracProyecto  = 'TIPO_PROYECTO';

   /**
     * @Secure(roles="ROLE_448-1")
     *
     * Documentación para la función 'indexAction'.
     *
     * Función que carga la pantalla de solicitud de proyecto.
     * 
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.0 03-07-2020
     *
     * @return render Redirecciona al index de la opción.
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

            if( $this->get('security.context')->isGranted('ROLE_448-1') )
            {
                $arrayRolesPermitidos[] = 'ROLE_448-1';
            }
            if( $this->get('security.context')->isGranted('ROLE_448-6') )
            {
                $arrayRolesPermitidos[] = 'ROLE_448-6';
            }
            if( $this->get('security.context')->isGranted('ROLE_448-7') )
            {
                $arrayRolesPermitidos[] = 'ROLE_448-7';
            }
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError('Telcos', 'indexAction', $e->getMessage(), $strUsrCreacion, $strIpCreacion);
        }
        return $this->render('comercialBundle:SolicitudProyecto:index.html.twig', array('rolesPermitidos' => $arrayRolesPermitidos));
     }

   /**
     * @Secure(roles="ROLE_448-7")
     *
     * Documentación para la función 'gridAction'.
     *
     * Función que retorna el listado de solicitudes de proyecto.
     *
     * @return $objResponse - Listado de Solicitudes.
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.0 03-07-2020
     *
     */
    public function gridAction()
    {
        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $strNombre              = $objRequest->get("strNombre")           ? $objRequest->get("strNombre"):"";
        $strApellido            = $objRequest->get("strApellido")         ? $objRequest->get("strApellido"):"";
        $strRazonSocial         = $objRequest->get("strRazonSocial")      ? $objRequest->get("strRazonSocial"):"";
        $strLogin               = $objRequest->get("strLogin")            ? $objRequest->get("strLogin"):"";
        $strFechaInicio         = $objRequest->get("strFechaInicio")      ? $objRequest->get("strFechaInicio"):"";
        $strFechaFin            = $objRequest->get("strFechaFin")         ? $objRequest->get("strFechaFin"):"";
        $strEstado              = $objRequest->get("strEstado")           ? $objRequest->get("strEstado"):"Pendiente";
        $intIdEmpresa           = $objSession->get('idEmpresa')           ? $objSession->get('idEmpresa'):"";
        $intIdPersonaEmpresaRol = $objSession->get('idPersonaEmpresaRol') ? $objSession->get('idPersonaEmpresaRol'):"";
        $strUsrCreacion         = $objSession->get('user')                ? $objSession->get('user'):"";
        $strIpCreacion          = $objRequest->getClientIp()              ? $objRequest->getClientIp():'127.0.0.1';
        $emComercial            = $this->get('doctrine')->getManager('telconet');
        $serviceUtil            = $this->get('schema.Util');
        $serviceComercial       = $this->get('comercial.Comercial');
        $strTipoProyecto        = "";
        $intTotal               = 0;
        $arraySolicitud         = array();
        try
        {
            /**
             * BLOQUE QUE VALIDA LA CARACTERISTICA 'CARGO_GRUPO_ROLES_PERSONAL' ASOCIADA A EL USUARIO LOGONEADO.
             */
            $arrayPersonalEmpresaRol       = array('intIdPersonEmpresaRol' => $intIdPersonaEmpresaRol,
                                                   'strUsrCreacion'        => $strUsrCreacion,
                                                   'strIpCreacion'         => $strIpCreacion);
            $arrayResultadoCaracteristicas = $serviceComercial->getCaracteristicasPersonalEmpresaRol($arrayPersonalEmpresaRol);

            if( ( !empty($arrayResultadoCaracteristicas) && is_array($arrayResultadoCaracteristicas) ) &&
                ( isset($arrayResultadoCaracteristicas['strCargoPersonal']) && !empty($arrayResultadoCaracteristicas['strCargoPersonal']) ) )
            {
                $strTipoPersonal = $arrayResultadoCaracteristicas['strCargoPersonal'];
                if( !empty($strTipoPersonal) && $strTipoPersonal == "JEFE_DEPARTAMENTAL_PMO" )
                {
                    $strTipoProyecto = "PMO";
                }
                elseif( !empty($strTipoPersonal) && $strTipoPersonal == "JEFE_DEPARTAMENTAL_PYL" )
                {
                    $strTipoProyecto = "PYL";
                }
            }
            else
            {
                throw new \Exception('Persona en sesión no tiene el perfil para poder visualizar las solicitudes.');
            }
            $arrayParametros                      = array();
            $arrayParametros['strNombre']         = $strNombre;
            $arrayParametros['strApellido']       = $strApellido;
            $arrayParametros['strRazonSocial']    = $strRazonSocial;
            $arrayParametros['strLogin']          = $strLogin;
            $arrayParametros['strFechaInicio']    = $strFechaInicio;
            $arrayParametros['strFechaFin']       = $strFechaFin;
            $arrayParametros['intIdEmpresa']      = $intIdEmpresa;
            $arrayParametros['strTipoSolicitud']  = $this->strTipoSolicitud;
            $arrayParametros['strCaracteristica'] = $this->strCaracteristica;
            $arrayParametros['strEstado']         = $strEstado;
            $arrayParametros['strTipoProyecto']   = $strTipoProyecto;
            $arrayParametros['strCaracProyecto']  = $this->strCaracProyecto;
            $arrayParametros['strBandera']        = "TOTALIZADO";

            $arrayResultado = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                          ->getSolicitudProyecto($arrayParametros);

            if( isset($arrayResultado['error']) && !empty($arrayResultado['error']) )
            {
                throw new \Exception($arrayResultado['error']);
            }

            if( !empty($arrayResultado["registros"]) && isset($arrayResultado["registros"]) )
            {
                $arrayRegistros   = $arrayResultado['registros'];
                $intTotal         = $arrayResultado['total'];

                foreach($arrayRegistros as $arrayDatos)
                {
                    $strAsesor           = $arrayDatos["USR_VENDEDOR"];
                    $objPersonaVendedor  = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                       ->findOneBy(array('login'=>$arrayDatos["USR_VENDEDOR"]));
                    if( !empty($objPersonaVendedor) && is_object($objPersonaVendedor))
                    {
                        $strAsesor = $objPersonaVendedor->getNombres().' '.$objPersonaVendedor->getApellidos();
                    }
                    $arrayDataLink    = array('intIdCotizacion' => $arrayDatos["IDCOTIZACION"],
                                              'strEstado'       => $arrayDatos["ESTADO"]);
                    $strLinkVer       = array('linkVer'=> $this->generateUrl('show_solicitud_proyecto',$arrayDataLink));
                    $arraySolicitud[] = array('intIdCotizacion' => $arrayDatos["IDCOTIZACION"],
                                              'strCliente'      => ucwords(strtolower($arrayDatos["NOMBRE_CLIENTE"])),
                                              'strAsesor'       => ucwords(strtolower($strAsesor)),
                                              'strEstado'       => $arrayDatos["ESTADO"],
                                              'strObservacion'  => $arrayDatos["OBSERVACION"],
                                              'strFeCreacion'   => $arrayDatos["FE_CREACION"],
                                              'strAcciones'     => $strLinkVer);
                }
            }
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError('Telcos', 'gridAction', $e->getMessage(), $strUsrCreacion, $strIpCreacion);
        }
        $objResponse = new Response(json_encode(array('intTotal' => $intTotal, 'data' => $arraySolicitud)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

   /**
     * @Secure(roles="ROLE_448-6")
     *
     * Documentación para la función 'showAction'.
     *
     * Función que renderiza la página de Ver detalle de solicitudes de proyecto.
     *
     * @param int $intIdCotizacion => id de la cotización.
     * @param string $strEstado    => estado de la cotización.
     *
     * @return render - Página de Ver Solicitud.
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.0 03-07-2020
     *
     */
    public function showAction($intIdCotizacion,$strEstado)
    {
        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $intIdEmpresa           = $objSession->get('idEmpresa')           ? $objSession->get('idEmpresa'):"";
        $intIdPersonaEmpresaRol = $objSession->get('idPersonaEmpresaRol') ? $objSession->get('idPersonaEmpresaRol'):"";
        $strUsrCreacion         = $objSession->get('user')                ? $objSession->get('user'):"";
        $strIpCreacion          = $objRequest->getClientIp()              ? $objRequest->getClientIp():'127.0.0.1';
        $serviceUtil            = $this->get('schema.Util');
        $emComercial            = $this->get('doctrine')->getManager('telconet');
        try
        {
            if( empty($intIdCotizacion) )
            {
                throw new \Exception("El identificador es un parámetro obligatorio.");
            }
            $intTotal                             = 0;
            $arraySolicitud                       = array();
            $arraySolicitudDet                    = array();
            $arrayParametros                      = array();
            $arrayParametros['intIdCotizacion']   = $intIdCotizacion;
            $arrayParametros['intIdEmpresa']      = $intIdEmpresa;
            $arrayParametros['strEstado']         = $strEstado;
            $arrayParametros['strTipoSolicitud']  = $this->strTipoSolicitud;
            $arrayParametros['strCaracteristica'] = $this->strCaracteristica;

            $arrayResultado = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                          ->getSolicitudProyecto($arrayParametros);
            if( isset($arrayResultado['error']) && !empty($arrayResultado['error']) )
            {
                throw new \Exception($arrayResultado['error']);
            }
            if( !empty($arrayResultado["registros"])&&isset($arrayResultado["registros"]) )
            {
                $arrayRegistros = $arrayResultado['registros'];
                $intTotal       = $arrayResultado['total'];
                foreach($arrayRegistros as $arrayDatos)
                {
                    $strDctoUnitario     = "$  " . number_format(0, 2);
                    $strValorDescuento   = "$  " . number_format(0, 2);
                    $floatPrecioTotal    = "$  " . number_format(0, 2);
                    $floatValorDescuento = 0;

                    if( !empty($arrayDatos["VALOR_DESCUENTO"]) && isset($arrayDatos["VALOR_DESCUENTO"]) )
                    {
                        $strValorDescuento   = "$  " . number_format($arrayDatos["VALOR_DESCUENTO"], 2);
                        $floatValorDescuento = $arrayDatos["VALOR_DESCUENTO"];
                    }
                    elseif( (!empty($arrayDatos["PORCENTAJE_DESCUENTO"]) && isset($arrayDatos["PORCENTAJE_DESCUENTO"]) )
                            &&(!empty($arrayDatos["PRECIO_VENTA"])&&isset($arrayDatos["PRECIO_VENTA"])))
                    {
                        $floatValorDescuento = ($arrayDatos["PORCENTAJE_DESCUENTO"] / 100) * $arrayDatos["PRECIO_VENTA"];
                    }

                    if( !empty($arrayDatos["DESCUENTO_UNITARIO"]) && isset($arrayDatos["DESCUENTO_UNITARIO"]) )
                    {
                        $strDctoUnitario = "$  " . number_format($arrayDatos["DESCUENTO_UNITARIO"], 2);
                    }

                    if( ( !empty($arrayDatos["CANTIDAD"]) && isset($arrayDatos["CANTIDAD"]) ) &&
                        ( !empty($arrayDatos["PRECIO_VENTA"]) && isset($arrayDatos["PRECIO_VENTA"]) ) )
                    {
                        $floatPrecioTotal = ($arrayDatos["CANTIDAD"] * $arrayDatos["PRECIO_VENTA"]) - $floatValorDescuento;
                    }

                    $floatPrecioTotal = $floatPrecioTotal < 0 ? 0 : $floatPrecioTotal;

                    $strCliente      = (!empty($arrayDatos["NOMBRE_CLIENTE"])&&isset($arrayDatos["NOMBRE_CLIENTE"]))
                                        ?$arrayDatos["NOMBRE_CLIENTE"]:"";
                    $strLogin        = (!empty($arrayDatos["LOGIN"])&&isset($arrayDatos["LOGIN"]))?$arrayDatos["LOGIN"]:"";
                    $intIdCotizacion = (!empty($arrayDatos["IDCOTIZACION"])&&isset($arrayDatos["IDCOTIZACION"]))?$arrayDatos["IDCOTIZACION"]:"";
                    $strEstado       = (!empty($arrayDatos["ESTADO"])&&isset($arrayDatos["ESTADO"]))
                                        ?$arrayDatos["ESTADO"]:"";
                    $strEstadoSer    = (!empty($arrayDatos["ESTADO_SERVICIO"])&&isset($arrayDatos["ESTADO_SERVICIO"]))
                                        ?$arrayDatos["ESTADO_SERVICIO"]:"";
                    $strPrecioVenta  = (!empty($arrayDatos["PRECIO_VENTA"])&&isset($arrayDatos["PRECIO_VENTA"]))?$arrayDatos["PRECIO_VENTA"]:"0";
                    $strPrecioInst   = (!empty($arrayDatos["PRECIO_INSTALACION"])&&isset($arrayDatos["PRECIO_INSTALACION"]))
                                        ?$arrayDatos["PRECIO_INSTALACION"]:"0";
                    $strDescProd     = (!empty($arrayDatos["DESCRIPCION_PRODUCTO"])&&isset($arrayDatos["DESCRIPCION_PRODUCTO"]))
                                        ?$arrayDatos["DESCRIPCION_PRODUCTO"]:"";
                    $strCantidad     = (!empty($arrayDatos["CANTIDAD"])&&isset($arrayDatos["CANTIDAD"]))
                                        ?$arrayDatos["CANTIDAD"]:"";

                    $strAsesor           = $arrayDatos["USR_VENDEDOR"];
                    $objPersonaVendedor  = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                        ->findOneBy(array('login'=>$arrayDatos["USR_VENDEDOR"]));
                    if( !empty($objPersonaVendedor) && is_object($objPersonaVendedor))
                    {
                        $strAsesor = $objPersonaVendedor->getNombres().' '.$objPersonaVendedor->getApellidos();
                    }


                    $arrayServicio[] = array('strProducto'     => $strDescProd,
                                             'strLogin'        => $strLogin,
                                             'strCantidad'     => $strCantidad,
                                             'strDescuentoUni' => $strDctoUnitario,
                                             'strDescuento'    => $strValorDescuento,
                                             'strEstadoSer'    => $strEstadoSer,
                                             'strPrecioVenta'  => "$  " . number_format($strPrecioVenta, 2),
                                             'strPrecioTotal'  => "$  " . number_format($floatPrecioTotal, 2),
                                             'strPrecioInst'   => "$  " . number_format($strPrecioInst, 2));
                }
                $arraySolicitudDet = array('intIdCotizacion' => $intIdCotizacion,
                                           'strEstado'       => $strEstado,
                                           'arrayServicio'   => $arrayServicio,
                                           'strCliente'      => ucwords(strtolower($strCliente)),
                                           'strAsesor'       => ucwords(strtolower($strAsesor)));
            }
            if( empty($arraySolicitudDet) || !is_array($arraySolicitudDet) )
            {
                $arrayServicio    = array();
                $arrayServicio [] = array("strProducto"     => "",
                                          "strLogin"        => "",
                                          "strCantidad"     => "",
                                          "strDescuentoUni" => "",
                                          "strDescuento"    => "",
                                          "strEstadoSer"    => "",
                                          "strPrecioVenta"  => "",
                                          "strPrecioTotal"  => "",
                                          "strPrecioInst"   => "");
                $arraySolicitudDet = array('intIdCotizacion' => "",
                                           'arrayServicio'   => $arrayServicio,
                                           'strCliente'      => "",
                                           'strAsesor'       => "");
            }
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError('Telcos', 'showAction', $e->getMessage(), $strUsrCreacion, $strIpCreacion);
        }
        return $this->render('comercialBundle:SolicitudProyecto:show.html.twig',array('arraySolicitudDet' => $arraySolicitudDet));
    }

   /**
     * Documentación para la función 'crearTarea'.
     *
     * Función que invoca al web services de crear taréa.
     *
     * @param array $arrayParametros [
     *                                  "strUsrCreacion"       => Usuario quien crea la tarea,
     *                                  "strIpCreacion"        => Ip del usuario en sesión,
     *                                  "nombreProceso"        => Nombre del proceso,
     *                                  "nombreTarea"          => Nombre de la tarea,
     *                                  "observacion"          => Observacion de la tarea a crear,
     *                                  "fechaSolicitada"      => Hora de la tarea,
     *                                  "horaSolicitada"       => Fecha solicitada para la ejecucion de la tarea,
     *                                  "nombreDepartamento"   => Departamento de la persona a quién se le va a crear la taréa,
     *                                  "loginAsignado"        => Nombre de la persona a quién se le va a crear la taréa,
     *                                  "strPrefijoEmpresa"    => Prefijo de la empresa,
     *                                  "esAutomatico"         => Valor boleano para iniciar la taréa
     *                               ]
     *
     * @return Array $arrayRespuesta.
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.0 03-07-2020
     *
     */
    public function crearTarea($arrayParametros)
    {
        try
        {
            $emSoporte         = $this->getDoctrine()->getManager("telconet_soporte");
            $emComercial       = $this->get('doctrine')->getManager('telconet');
            $objSoporteService = $this->get('soporte.SoporteService');
            $serviceUtil       = $this->get('schema.Util');

            if( !isset($arrayParametros['strPrefijoEmpresa']) || empty($arrayParametros['strPrefijoEmpresa']) )
            {
                throw new \Exception("La variable strPrefijoEmpresa es un campo obligatorio.");
            }
            if( !isset($arrayParametros['nombreDepartamento']) || empty($arrayParametros['nombreDepartamento']) )
            {
                throw new \Exception("La variable nombreDepartamento es un campo obligatorio.");
            }
            if( !isset($arrayParametros['loginAsignado']) || empty($arrayParametros['loginAsignado']) )
            {
                throw new \Exception("La variable loginAsignado es un campo obligatorio.");
            }
            if( !isset($arrayParametros['strUsrCreacion']) || empty($arrayParametros['strUsrCreacion']) )
            {
                throw new \Exception("La variable UsrCreacion es un campo obligatorio.");
            }
            $strUsrCreacion = $arrayParametros["strUsrCreacion"];
            if( !isset($arrayParametros['strIpCreacion']) || empty($arrayParametros['strIpCreacion']) )
            {
                throw new \Exception("La variable strIpCreacion es un campo obligatorio.");
            }
            $strIpCreacion = $arrayParametros["strIpCreacion"];
            if( !isset($arrayParametros['nombreProceso']) || empty($arrayParametros['nombreProceso']) )
            {
                throw new \Exception("El nombre del proceso es un campo obligatorio.");
            }
            if( !isset($arrayParametros['nombreTarea']) || empty($arrayParametros['nombreTarea']) )
            {
                throw new \Exception("El nombre de la tarea es un campo obligatorio.");
            }
            if( !isset($arrayParametros['horaSolicitada'])  || empty($arrayParametros['horaSolicitada']) ||
                !isset($arrayParametros['fechaSolicitada']) || empty($arrayParametros['fechaSolicitada']) )
            {
                throw new \Exception('La horaSolicitada y(o) fechaSolicitada es un campo obligatorio');
            }
            $arrayFecha = explode('-', $arrayParametros['fechaSolicitada']);
            if( count($arrayFecha) !== 3 || !checkdate($arrayFecha[1], $arrayFecha[2], $arrayFecha[0]) )
            {
                throw new \Exception('El Formato de fecha Inválido');
            }
            if( strtotime($arrayParametros['horaSolicitada']) === false )
            {
                throw new \Exception('El Formato de hora Inválido');
            }
            $objInfoPersona = $emComercial->getRepository("schemaBundle:InfoPersona")
                                          ->findOneByLogin($strUsrCreacion);

            if( !is_object($objInfoPersona) || !in_array($objInfoPersona->getEstado(), array('Activo','Pendiente','Modificado')) )
            {
                throw new \Exception('El usuario de creación no existe en telcos o no se encuentra Activo.');
            }

            $strUsuarioAsigna  = $objInfoPersona->getNombres()." ".$objInfoPersona->getApellidos();
            $arrayDatosPersona = $emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                             ->getInfoDatosPersona(array ('strRol'                     => 'Empleado',
                                                                          'strPrefijo'                 => $arrayParametros['strPrefijoEmpresa'],
                                                                          'strEstadoPersona'           => array('Activo','Pendiente','Modificado'),
                                                                          'strEstadoPersonaEmpresaRol' => 'Activo',
                                                                          'strDepartamento'            => $arrayParametros['nombreDepartamento'],
                                                                          'strLogin'                   => $arrayParametros['loginAsignado']));

            if( $arrayDatosPersona['status'] === 'fail' )
            {
                throw new \Exception('Error al obtener los datos del asignado, por favor comunicar a Sistemas.');
            }

            if( $arrayDatosPersona['status'] === 'ok' && empty($arrayDatosPersona['result']) )
            {
                throw new \Exception('Los filtros para encontrar al empleado asignado son incorrectos '.
                            'o el empleado no existe en telcos');
            }

            $arrayParametros = array ('intIdPersonaEmpresaRol' => $arrayDatosPersona['result'][0]['idPersonaEmpresaRol'],
                                      'intIdEmpresa'           => $arrayDatosPersona['result'][0]['idEmpresa'],
                                      'strPrefijoEmpresa'      => $arrayDatosPersona['result'][0]['prefijoEmpresa'],
                                      'strNombreTarea'         => $arrayParametros['nombreTarea'],
                                      'strNombreProceso'       => $arrayParametros['nombreProceso'],
                                      'strObservacionTarea'    => $arrayParametros['observacion'],
                                      'strMotivoTarea'         => $arrayParametros['observacion'],
                                      'strTipoAsignacion'      => 'empleado',
                                      'strIniciarTarea'        => $arrayParametros['esAutomatico'],
                                      'strTipoTarea'           => 'T',
                                      'strTareaRapida'         => 'N',
                                      'strFechaHoraSolicitada' => $arrayParametros['fechaSolicitada'].' '.$arrayParametros['horaSolicitada'],
                                      'boolAsignarTarea'       => true,
                                      "strAplicacion"          => 'telcoSys',
                                      'strUsuarioAsigna'       => $strUsuarioAsigna,
                                      'strUserCreacion'        => $strUsrCreacion,
                                      'strIpCreacion'          => $strIpCreacion);
            $arrayRespuesta  = $objSoporteService->crearTareaCasoSoporte($arrayParametros);

            if( $arrayRespuesta['mensaje'] === 'fail' )
            {
                throw new \Exception('Error al crear la tarea, por favor comunicar a Sistemas.');
            }
            $arrayRespuesta['status']              = $arrayRespuesta['mensaje'];
            $arrayRespuesta['numeroTarea']         = $arrayRespuesta['numeroTarea'];
            $arrayRespuesta['idDetalleTarea']      = $arrayRespuesta['numeroDetalle'];
            $arrayRespuesta['infomacionAdicional'] = $arrayRespuesta['infomacionAdicional'];
        }
        catch(\Exception $e)
        {
            $strMensaje               = "Ocurrió un error al crear la tarea, por favor comuníquese con el departamento de Sistemas.";
            $arrayRespuesta['status'] = "fail";
            $arrayRespuesta['error']  = $e->getMessage();
            $serviceUtil->insertError('Telcos', 'crearTarea', $strMensaje . $e->getMessage(), $strUsrCreacion, $strIpCreacion);
        }
        return $arrayRespuesta;
    }

   /**
     * Documentación para la función 'getAprobarRechazarSolicitudAction'.
     *
     * Función que aprueba o rechaza las solicitudes de proyecto.
     * 
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.0 03-07-2020
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.1 03-09-2020 - Se agrega lógica para eliminar característica cuando se rechaza el proyecto
     *                           y dejar disponible la propuesta-cotización para futuros ingresos.
     *
     * @return $strResponse - Respuesta de confirmación.
     */
    public function getAprobarRechazarSolicitudAction()
    {
        $objRequest              = $this->getRequest();
        $objSession              = $objRequest->getSession();
        $strCodigo               = $objRequest->get("strCodigo")               ? $objRequest->get("strCodigo"):"";
        $intIdMotivoReasignar    = $objRequest->get("intIdMotivoReasignar")    ? $objRequest->get("intIdMotivoReasignar"):"";
        $intIdMotivoRechazar     = $objRequest->get("intIdMotivoRechazar")     ? $objRequest->get("intIdMotivoRechazar"):"";
        $strObservacionAprobar   = $objRequest->get("strObservacionAprobar")   ? $objRequest->get("strObservacionAprobar"):"";
        $strObservacionReasignar = $objRequest->get("strObservacionReasignar") ? $objRequest->get("strObservacionReasignar"):"";
        $strObservacionRechazar  = $objRequest->get("strObservacionRechazar")  ? $objRequest->get("strObservacionRechazar"):"";
        $strAccion               = $objRequest->get("strAccion")               ? $objRequest->get("strAccion"):"";
        $arraySolicitudes        = $objRequest->get("arraySolicitudes")        ? $objRequest->get("arraySolicitudes"):"";
        $strUsuario              = $objRequest->get("strUsuario")              ? $objRequest->get("strUsuario"):"";
        $strPrefijoEmpresa       = $objSession->get('prefijoEmpresa')          ? $objSession->get('prefijoEmpresa'):"";
        $intIdEmpresa            = $objSession->get('idEmpresa')               ? $objSession->get('idEmpresa'):"";
        $intIdPersonaEmpresaRol  = $objSession->get('idPersonaEmpresaRol')     ? $objSession->get('idPersonaEmpresaRol'):"";
        $strUsrCreacion          = $objSession->get('user')                    ? $objSession->get('user'):"";
        $strIpCreacion           = $objRequest->getClientIp()                  ? $objRequest->getClientIp():'127.0.0.1';
        $emComercial             = $this->get('doctrine')->getManager('telconet');
        $serviceEnvioPlantilla   = $this->get('soporte.EnvioPlantilla');
        $serviceUtil             = $this->get('schema.Util');
        $serviceTelcoCrm         = $this->get('comercial.ComercialCrm');
        $emGeneral               = $this->get('doctrine')->getManager('telconet');
        $strEstadoSol            = "";
        $serviceComercial        = $this->get('comercial.Comercial');
        try
        {
            /**
             * BLOQUE QUE VALIDA LA CARACTERISTICA 'CARGO_GRUPO_ROLES_PERSONAL' ASOCIADA A EL USUARIO LOGONEADO 
             */
            $arrayPersonalEmpresaRol       = array('intIdPersonEmpresaRol' => $intIdPersonaEmpresaRol,
                                                   'strUsrCreacion'        => $strUsrCreacion,
                                                   'strIpCreacion'         => $strIpCreacion);

            $arrayResultadoCaracteristicas = $serviceComercial->getCaracteristicasPersonalEmpresaRol($arrayPersonalEmpresaRol);

            if( ( !empty($arrayResultadoCaracteristicas) && is_array($arrayResultadoCaracteristicas) ) &&
                ( isset($arrayResultadoCaracteristicas['strCargoPersonal']) && !empty($arrayResultadoCaracteristicas['strCargoPersonal']) ) )
            {
                $strTipoPersonal = $arrayResultadoCaracteristicas['strCargoPersonal'];
                if( !empty($strTipoPersonal) && $strTipoPersonal == "JEFE_DEPARTAMENTAL_PMO" )
                {
                    $strTipoProyecto     = "PMO";
                }
                elseif( !empty($strTipoPersonal) && $strTipoPersonal == "JEFE_DEPARTAMENTAL_PYL" )
                {
                    $strTipoProyecto     = "PYL";
                }
            }
            else
            {
                throw new \Exception('Persona en sesión no tiene el perfil para poder aprobar,reasignar,rechazar la solicitud.');
            }

            $arrayDestinatarios                   = array();
            $arrayParametros                      = array();
            $arrayParametros['intIdEmpresa']      = $intIdEmpresa;
            $arrayParametros['strTipoSolicitud']  = $this->strTipoSolicitud;
            $arrayParametros['strCaracteristica'] = $this->strCaracteristica;
            $arrayParametros['strEstado']         = "Pendiente";
            $arrayParametros['strTipoProyecto']   = $strTipoProyecto;
            $arrayParametros['strCaracProyecto']  = $this->strCaracProyecto;

            if( empty($arraySolicitudes) || !is_array($arraySolicitudes) )
            {
                throw new \Exception('El listado de solicitudes es un campo obligatorio.');
            }
            $emComercial->getConnection()->beginTransaction();
            if( $strAccion=="aprobar" )
            {
                if( empty($strUsuario) )
                {
                    throw new \Exception('Se necesita un usuario para asignar el proyecto.');
                }
                //Bloque que obtiene el listado de usuarios para notificar la creación del proyecto y la creación de tarea.
                $strEstadoSol   = "Aprobado";
                $strObservacion = "Se aprueba la solicitud de proyecto con la sgte. observación: ".$strObservacionAprobar;
                for($i=0;$i<count($arraySolicitudes);$i++)
                {
                    $arrayDestinatarios = array();
                    $arrayListUsuarios  = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                      ->get('PARAMETROS_TELCOCRM',
                                                            'COMERCIAL', 
                                                            '', 
                                                            'LISTADO_USUARIOS_NOTIFICACION',
                                                            $strTipoProyecto,
                                                            '', 
                                                            '', 
                                                            '', 
                                                            '',
                                                            $intIdEmpresa);
                    if( !empty($arrayListUsuarios) && is_array($arrayListUsuarios) )
                    {
                        foreach($arrayListUsuarios as $arrayItem)
                        {
                            $arrayCorreos = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                        ->getContactosByLoginPersonaAndFormaContacto($arrayItem["valor2"],
                                                                                                    'Correo Electronico');
                            if( !empty($arrayCorreos) && is_array($arrayCorreos) )
                            {
                                foreach($arrayCorreos as $arrayItem)
                                {
                                    if( !empty($arrayItem['valor']) && isset($arrayItem['valor']) )
                                    {
                                        $arrayDestinatarios[] = $arrayItem['valor'];
                                    }
                                }
                            }
                        }
                    }
                    $arrayCorreos = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                ->getContactosByLoginPersonaAndFormaContacto($strUsuario,
                                                                                            'Correo Electronico');
                    if( !empty($arrayCorreos) && is_array($arrayCorreos) )
                    {
                        foreach($arrayCorreos as $arrayItem)
                        {
                            if( !empty($arrayItem['valor']) && isset($arrayItem['valor']) )
                            {
                                $arrayDestinatarios[] = $arrayItem['valor'];
                            }
                        }
                    }

                    //Bloque que notifica la creación del proyecto al usuario asignado.
                    $arrayParametros['intIdCotizacion'] = trim($arraySolicitudes[$i]);
                    $arrayParametros['strBandera']      = "TOTALIZADO";
                    $arrayResultado                     = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                      ->getSolicitudProyecto($arrayParametros);
                    if( !empty($arrayResultado["registros"]) && isset($arrayResultado["registros"]) )
                    {
                        $arrayRegistros          = $arrayResultado['registros'];
                        $arrayParametrosVendedor = array("strLogin"=>$arrayRegistros[0]["USR_VENDEDOR"]);
                        $arrayResultadoCorreo    = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                               ->getSubgerentePorLoginVendedor($arrayParametrosVendedor);

                        if( !empty($arrayResultadoCorreo["registros"]) && isset($arrayResultadoCorreo["registros"]) )
                        {
                            $arrayRegistrosCorreo = $arrayResultadoCorreo['registros'];

                            $arrayCorreos = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                        ->getContactosByLoginPersonaAndFormaContacto($arrayRegistrosCorreo[0]["LOGIN_SUBGERENTE"],
                                                                                                    'Correo Electronico');
                            if( !empty($arrayCorreos) && is_array($arrayCorreos) )
                            {
                                foreach($arrayCorreos as $arrayItem)
                                {
                                    if( !empty($arrayItem['valor']) && isset($arrayItem['valor']) )
                                    {
                                        $arrayDestinatarios[] = $arrayItem['valor'];
                                    }
                                }
                            }

                            $arrayCorreos = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                        ->getContactosByLoginPersonaAndFormaContacto($arrayRegistrosCorreo[0]["LOGIN_VENDEDOR"],
                                                                                                    'Correo Electronico');
                            if( !empty($arrayCorreos) && is_array($arrayCorreos) )
                            {
                                foreach($arrayCorreos as $arrayItem)
                                {
                                    if( !empty($arrayItem['valor']) && isset($arrayItem['valor']) )
                                    {
                                        $arrayDestinatarios[] = $arrayItem['valor'];
                                    }
                                }
                            }
                        }
                        $arrayParametrosMail  = array("strNombreCliente"         => $arrayRegistros[0]["NOMBRE_CLIENTE"],
                                                      "strIdentificacionCliente" => $arrayRegistros[0]["IDENTIFICACION_CLIENTE"],
                                                      "strObservacion"           => $strObservacion);
                        $serviceEnvioPlantilla->generarEnvioPlantilla("NUEVO PROYECTO TELCOCRM", 
                                                                      array_unique($arrayDestinatarios), 
                                                                     'NUEVO_PROYECTO', 
                                                                      $arrayParametrosMail,
                                                                      $strPrefijoEmpresa,
                                                                      '',
                                                                      '',
                                                                      null, 
                                                                      true,
                                                                      'notificaciones_telcos@telconet.ec');
                    }
                    //Bloque que actualiza el estado de la cotización y realiza los n pedidos en TelcoCRM.
                    $arrayParametrosTelcoCRM = array("intIdCotizacion"      => $arraySolicitudes[$i],
                                                     "strPrefijoEmpresa"    => $strPrefijoEmpresa,
                                                     "strCodEmpresa"        => $intIdEmpresa);
                    $arrayParametrosWSCrm    = array("arrayParametrosCRM"   => $arrayParametrosTelcoCRM,
                                                     "strOp"                => 'editCotizacion',
                                                     "strFuncion"           => 'procesar');
                    $arrayRespuestaWSCrm     = $serviceTelcoCrm->getRequestCRM($arrayParametrosWSCrm);
                    if( !empty($arrayRespuestaWSCrm["error"]) && $arrayRespuestaWSCrm["error"]!="" )
                    {
                        throw new \Exception('Error al Actualizar Estado de la cotización en TelcoCrm.');
                    }

                    //Bloque que crea el proyecto en TelcoCRM.
                    $arrayParametrosTelcoCRM = array("intIdCotizacion"      => $arraySolicitudes[$i],
                                                     "strCodigo"            => $strCodigo,
                                                     "strLogin"             => $strUsuario);
                    $arrayParametrosWSCrm    = array("arrayParametrosCRM"   => $arrayParametrosTelcoCRM,
                                                     "strOp"                => 'createProyecto',
                                                     "strFuncion"           => 'procesar');
                    $arrayRespuestaWSCrm     = $serviceTelcoCrm->getRequestCRM($arrayParametrosWSCrm);
                    if( !empty($arrayRespuestaWSCrm["error"]) && $arrayRespuestaWSCrm["error"]!="" )
                    {
                        throw new \Exception('Error al crear proyecto en TelcoCrm.');
                    }

                    //Bloque que recorre el listado de usuarios para la creación de tarea.
                    if( !empty($arrayListUsuarios) && is_array($arrayListUsuarios) )
                    {
                        foreach($arrayListUsuarios as $arrayItem)
                        {
                            if( !empty($arrayItem['valor4']) && isset($arrayItem['valor4']) && $arrayItem['valor4'] == "S" )
                            {
                                $arrayDepartamentos = $emComercial->getRepository('schemaBundle:AdmiDepartamento')
                                                                  ->getDepartamentosPorLogin(array("strLogin"              => $arrayItem['valor2'],
                                                                                                   "intIdEmpresa"          => $intIdEmpresa,
                                                                                                   "strEstadoDepartamento" => "Activo"));
                                if( !empty( $arrayDepartamentos['registros']) && is_array($arrayDepartamentos) )
                                {
                                    $arrayitemDepartamentos = $arrayDepartamentos['registros'];
                                    $arrayTareaCont         = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                        ->get('PARAMETROS_TELCOCRM', 
                                                                              'COMERCIAL',
                                                                              '',
                                                                              'TAREA_PROYECTO_CONTABILIDAD',
                                                                              $strTipoProyecto,
                                                                              '',
                                                                              '',
                                                                              '',
                                                                              '',
                                                                              $intIdEmpresa);
                                    if( !empty($arrayTareaCont) && is_array($arrayTareaCont) )
                                    {
                                        $strObsTarea = "El proyecto está asignado a la persona: ".$strUsuario." con la siguiente observación: ";
                                        $arrayData   = array('nombreProceso'      => $arrayTareaCont[0]["valor2"],
                                                             'nombreTarea'        => $arrayTareaCont[0]["valor3"],
                                                             'loginAsignado'      => $arrayItem['valor2'],
                                                             'strUsrCreacion'     => $strUsrCreacion,
                                                             'strIpCreacion'      => $strIpCreacion,
                                                             'nombreDepartamento' => $arrayitemDepartamentos[0]["NOMBRE_DEPARTAMENTO"],
                                                             'fechaSolicitada'    => date("Y-m-d"),
                                                             'horaSolicitada'     => date("H:i:s"),
                                                             'esAutomatico'       => "S",
                                                             'strPrefijoEmpresa'  => $strPrefijoEmpresa,
                                                             'observacion'        => $strObsTarea.$strObservacionAprobar);
                                        $arrayTarea  = $this->crearTarea($arrayData);
                                        if( !empty($arrayTarea["error"]) && isset($arrayTarea["error"]) )
                                        {
                                            throw new \Exception('Error al crear tarea en TelcoS+ para la creación de centro de costos: '.$arrayTarea["error"]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    //Bloque que crea la tarea del proyecto al usuario asignado.
                    $arrayDepartamentos = $emComercial->getRepository('schemaBundle:AdmiDepartamento')
                                                      ->getDepartamentosPorLogin(array("strLogin"              => $strUsuario,
                                                                                       "intIdEmpresa"          => $intIdEmpresa,
                                                                                       "strEstadoDepartamento" => "Activo"));
                    if( !empty( $arrayDepartamentos['registros']) && is_array($arrayDepartamentos) )
                    {
                        $arrayitemDepartamentos = $arrayDepartamentos['registros'];
                        $arrayTareaProyecto     = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->get('PARAMETROS_TELCOCRM', 
                                                                  'COMERCIAL',
                                                                  '',
                                                                  'TAREA_PROYECTO',
                                                                  $strTipoProyecto,
                                                                  '',
                                                                  '',
                                                                  '',
                                                                  '',
                                                                  $intIdEmpresa);
                        if( !empty($arrayTareaProyecto) && is_array($arrayTareaProyecto) )
                        {
                            $arrayData   = array('nombreProceso'      => $arrayTareaProyecto[0]["valor2"],
                                                 'nombreTarea'        => $arrayTareaProyecto[0]["valor3"],
                                                 'loginAsignado'      => $strUsuario,
                                                 'strUsrCreacion'     => $strUsrCreacion,
                                                 'strIpCreacion'      => $strIpCreacion,
                                                 'nombreDepartamento' => $arrayitemDepartamentos[0]["NOMBRE_DEPARTAMENTO"],
                                                 'fechaSolicitada'    => date("Y-m-d"),
                                                 'horaSolicitada'     => date("H:i:s"),
                                                 'esAutomatico'       => "S",
                                                 'strPrefijoEmpresa'  => $strPrefijoEmpresa,
                                                 'observacion'        => $strObservacionAprobar);
                            $arrayTarea  = $this->crearTarea($arrayData);
                            if( !empty($arrayTarea["error"]) && isset($arrayTarea["error"]) )
                            {
                                throw new \Exception('Error al crear tarea en TelcoS+ para la creación de centro de costos: '.$arrayTarea["error"]);
                            }
                        }
                    }
                    //Bloque que guarda el historial del servicio y de la solicitud.
                    $arrayParametros['intIdCotizacion'] = $arraySolicitudes[$i];
                    $arrayParametros['strBandera']      = "";
                    $arrayResultado                     = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                      ->getSolicitudProyecto($arrayParametros);
                    if( !empty($arrayResultado["registros"]) && isset($arrayResultado["registros"]) )
                    {
                        foreach($arrayResultado['registros'] as $arrayDatos)
                        {
                            $entityDetalleSolicitud = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                  ->findOneById($arrayDatos["ID_DETALLE_SOLICITUD"]);
                            $entityDetalleSolicitud->setObservacion($strObservacion);
                            $entityDetalleSolicitud->setEstado($strEstadoSol);
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
                            $objServicio = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                       ->findOneBy(array('id'=>$arrayDatos["ID_SERVICIO"]));
                            if( (!empty($objServicio) && is_object($objServicio) )
                                && ( isset($arrayRespuestaWSCrm["resultado"]) && !empty($arrayRespuestaWSCrm["resultado"]) ))
                            {
                                $objResultadoWSCRM = $arrayRespuestaWSCrm["resultado"];
                                if( ( !empty($objResultadoWSCRM) && is_object($objResultadoWSCRM) )
                                    &&( !empty($objResultadoWSCRM->intIdProyecto) && !empty($objResultadoWSCRM->strNombreProyecto) ) )
                                {
                                    $intIdProyecto     = $objResultadoWSCRM->intIdProyecto;
                                    $strNombreProyecto = $objResultadoWSCRM->strNombreProyecto;
                                    $strObservacionIsh = "Se crea proyecto ".$strNombreProyecto.
                                                         " en TelcoCRM con la sgte. observación: ".$strObservacionAprobar;
                                    $objServicioHist = new InfoServicioHistorial();
                                    $objServicioHist->setServicioId($objServicio);
                                    $objServicioHist->setObservacion($strObservacionIsh);
                                    $objServicioHist->setIpCreacion($strIpCreacion);
                                    $objServicioHist->setUsrCreacion($strUsrCreacion);
                                    $objServicioHist->setFeCreacion(new \DateTime('now'));
                                    $objServicioHist->setEstado($objServicio->getEstado());
                                    $emComercial->persist($objServicioHist);
                                    $emComercial->flush();
                                    $objAdmiCaracProyecto = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                        ->findOneBy(array("descripcionCaracteristica" => 'ID_PROYECTO',
                                                                                          "estado"                    => "Activo"));
                                    if( is_object($objAdmiCaracProyecto) && !empty($objAdmiCaracProyecto) )
                                    {
                                        $arrayProdCaractProyecto   = array("productoId"       => $objServicio->getProductoId()->getId(),
                                                                           "caracteristicaId" => $objAdmiCaracProyecto,
                                                                           "estado"           => "Activo");
                                        $objAdmiProdCaractProyecto = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                                 ->findOneBy($arrayProdCaractProyecto);
                                        if( is_object($objAdmiProdCaractProyecto)&&!empty($objAdmiProdCaractProyecto) )
                                        {
                                            $objServicioProdCaract  = new InfoServicioProdCaract();
                                            $objServicioProdCaract->setServicioId($objServicio->getId());
                                            $objServicioProdCaract->setProductoCaracterisiticaId($objAdmiProdCaractProyecto->getId());
                                            $objServicioProdCaract->setValor($intIdProyecto);
                                            $objServicioProdCaract->setEstado('Activo');
                                            $objServicioProdCaract->setUsrCreacion($objServicio->getUsrCreacion());
                                            $objServicioProdCaract->setFeCreacion(new \DateTime('now'));
                                            $emComercial->persist($objServicioProdCaract);
                                            $emComercial->flush();
                                        }
                                    }
                                    $objAdmiCaracProyecto = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                        ->findOneBy(array("descripcionCaracteristica" => 'NOMBRE_PROYECTO',
                                                                                          "estado"                    => "Activo"));
                                    if( is_object($objAdmiCaracProyecto)&&!empty($objAdmiCaracProyecto) )
                                    {
                                        $arrayProdCaractProyecto   = array("productoId"       => $objServicio->getProductoId()->getId(),
                                                                           "caracteristicaId" => $objAdmiCaracProyecto,
                                                                           "estado"           => "Activo");
                                        $objAdmiProdCaractProyecto = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                                 ->findOneBy($arrayProdCaractProyecto);
                                        if( is_object($objAdmiProdCaractProyecto)&&!empty($objAdmiProdCaractProyecto) )
                                        {
                                            $objServicioProdCaract  = new InfoServicioProdCaract();
                                            $objServicioProdCaract->setServicioId($objServicio->getId());
                                            $objServicioProdCaract->setProductoCaracterisiticaId($objAdmiProdCaractProyecto->getId());
                                            $objServicioProdCaract->setValor($strNombreProyecto);
                                            $objServicioProdCaract->setEstado('Activo');
                                            $objServicioProdCaract->setUsrCreacion($objServicio->getUsrCreacion());
                                            $objServicioProdCaract->setFeCreacion(new \DateTime('now'));
                                            $emComercial->persist($objServicioProdCaract);
                                            $emComercial->flush();
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            else if( $strAccion=="reasignar" )
            {
                $strEstadoSol       = "Pendiente";
                //Bloque que notifica la reasignación de la solicitud
                for($i=0;$i<count($arraySolicitudes);$i++)
                {
                    $arrayDestinatarios = array();
                    $arrayListUsuarios  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->get('PARAMETROS_TELCOCRM', 
                                                        'COMERCIAL',
                                                        '',
                                                        'LISTADO_USUARIOS_NOTIFICACION_REASIGNACION',
                                                        $strTipoProyecto,
                                                        '',
                                                        '',
                                                        '',
                                                        '',
                                                        $intIdEmpresa);
                    if( !empty($arrayListUsuarios) && is_array($arrayListUsuarios) )
                    {
                        foreach($arrayListUsuarios as $arrayItem)
                        {
                            $strTipoProyectoTemp = $arrayItem["valor4"];
                            $arrayCorreos        = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                               ->getContactosByLoginPersonaAndFormaContacto($arrayItem["valor2"],
                                                                                                            'Correo Electronico');
                            if( !empty($arrayCorreos) && is_array($arrayCorreos) )
                            {
                                foreach($arrayCorreos as $arrayItem)
                                {
                                    if( !empty($arrayItem['valor']) && isset($arrayItem['valor']) )
                                    {
                                        $arrayDestinatarios[] = $arrayItem['valor'];
                                    }
                                }
                            }
                        }
                    }
                    $strObservacion                     = "Se reasigna la solicitud de proyecto al departamento de: ".$strTipoProyectoTemp.
                                                          " con la sgte. observación: ".$strObservacionReasignar;
                    $arrayParametros['intIdCotizacion'] = trim($arraySolicitudes[$i]);
                    $arrayParametros['strBandera']      = "TOTALIZADO";
                    $arrayResultado                     = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                      ->getSolicitudProyecto($arrayParametros);
                    if( !empty($arrayResultado["registros"]) && isset($arrayResultado["registros"]) )
                    {
                        $arrayRegistros          = $arrayResultado['registros'];
                        $arrayParametrosVendedor = array("strLogin"=>$arrayRegistros[0]["USR_VENDEDOR"]);
                        $arrayResultadoCorreo    = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                               ->getSubgerentePorLoginVendedor($arrayParametrosVendedor);

                        if( !empty($arrayResultadoCorreo["registros"]) && isset($arrayResultadoCorreo["registros"]) )
                        {
                            $arrayRegistrosCorreo = $arrayResultadoCorreo['registros'];

                            $arrayCorreos = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                        ->getContactosByLoginPersonaAndFormaContacto($arrayRegistrosCorreo[0]["LOGIN_SUBGERENTE"],
                                                                                                     'Correo Electronico');
                            if( !empty($arrayCorreos) && is_array($arrayCorreos) )
                            {
                                foreach($arrayCorreos as $arrayItem)
                                {
                                    if( !empty($arrayItem['valor']) && isset($arrayItem['valor']) )
                                    {
                                        $arrayDestinatarios[] = $arrayItem['valor'];
                                    }
                                }
                            }

                            $arrayCorreos = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                        ->getContactosByLoginPersonaAndFormaContacto($arrayRegistrosCorreo[0]["LOGIN_VENDEDOR"],
                                                                                                     'Correo Electronico');
                            if( !empty($arrayCorreos) && is_array($arrayCorreos) )
                            {
                                foreach($arrayCorreos as $arrayItem)
                                {
                                    if( !empty($arrayItem['valor']) && isset($arrayItem['valor']) )
                                    {
                                        $arrayDestinatarios[] = $arrayItem['valor'];
                                    }
                                }
                            }
                        }

                        $arrayParametrosMail  = array("strNombreCliente"         => $arrayRegistros[0]["NOMBRE_CLIENTE"],
                                                      "strIdentificacionCliente" => $arrayRegistros[0]["IDENTIFICACION_CLIENTE"],
                                                      "strObservacion"           => $strObservacion);
                        $serviceEnvioPlantilla->generarEnvioPlantilla("REASIGNACION DE SOLICITUD DE PROYECTO", 
                                                                      array_unique($arrayDestinatarios), 
                                                                      'REASIGNA_SOL', 
                                                                      $arrayParametrosMail,
                                                                      $strPrefijoEmpresa,
                                                                      '',
                                                                      '',
                                                                      null, 
                                                                      true,
                                                                      'notificaciones_telcos@telconet.ec');
                    }
                }

                $objAdmiCaractProyecto = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                     ->findOneBy(array("descripcionCaracteristica" => 'TIPO_PROYECTO',
                                                                       "estado"                    => "Activo"));
                if( !empty($objAdmiCaractProyecto) && is_object($objAdmiCaractProyecto) )
                {
                    $arrayParametros['intIdCotizacion'] = $arraySolicitudes;
                    $arrayParametros['strBandera']      = "";
                    $arrayResultado                     = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                      ->getSolicitudProyecto($arrayParametros);
                    if( !empty($arrayResultado["registros"]) && isset($arrayResultado["registros"]) )
                    {
                        foreach($arrayResultado['registros'] as $arrayDatos)
                        {
                            $entityDetalleSolicitud = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                  ->findOneById($arrayDatos["ID_DETALLE_SOLICITUD"]);
                            $entityDetalleSolicitud->setObservacion($strObservacion);
                            $entityDetalleSolicitud->setEstado($strEstadoSol);
                            if( !empty($intIdMotivoReasignar) )
                            {
                                $entityDetalleSolicitud->setMotivoId($intIdMotivoReasignar);
                            }
                            $emComercial->persist($entityDetalleSolicitud);
                            $emComercial->flush();

                            $entityDetalleSolHist = new InfoDetalleSolHist();
                            $entityDetalleSolHist->setDetalleSolicitudId($entityDetalleSolicitud);
                            $entityDetalleSolHist->setObservacion($strObservacion);
                            $entityDetalleSolHist->setIpCreacion($strIpCreacion);
                            $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
                            $entityDetalleSolHist->setUsrCreacion($strUsrCreacion);
                            $entityDetalleSolHist->setEstado($strEstadoSol);
                            if( !empty($intIdMotivoReasignar) )
                            {
                                $entityDetalleSolHist->setMotivoId($intIdMotivoReasignar);
                            }
                            $emComercial->persist($entityDetalleSolHist);
                            $emComercial->flush();
                            $objServicio = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                       ->findOneBy(array('id'=>$arrayDatos["ID_SERVICIO"]));
                            if( !empty($objServicio) && is_object($objServicio) )
                            {
                                $objProdCaract = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                             ->findOneBy(array("productoId" => $objServicio->getProductoId(),
                                                                               "caracteristicaId"=>$objAdmiCaractProyecto->getId()));
                                if( !empty($objProdCaract) && is_object($objProdCaract) )
                                {
                                    $objInfoServicioProdCaract = $emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                             ->findOneBy(array("servicioId"                => $objServicio->getId(),
                                                                                               "productoCaracterisiticaId" => $objProdCaract->getId()));
                                }
                                if( !empty($objInfoServicioProdCaract) && is_object($objInfoServicioProdCaract) )
                                {
                                    $objInfoServicioProdCaract->setValor($strTipoProyectoTemp);
                                    $objInfoServicioProdCaract->setFeUltMod(new \DateTime('now'));
                                    $objInfoServicioProdCaract->setUsrUltMod($strUsrCreacion);
                                    $emComercial->persist($objInfoServicioProdCaract);
                                    $emComercial->flush();
                                    $objServicioHist = new InfoServicioHistorial();
                                    $objServicioHist->setServicioId($objServicio);
                                    if( !empty($intIdMotivoReasignar) )
                                    {
                                        $objServicioHist->setMotivoId($intIdMotivoReasignar);
                                    }
                                    $objServicioHist->setObservacion($strObservacion);
                                    $objServicioHist->setIpCreacion($strIpCreacion);
                                    $objServicioHist->setUsrCreacion($strUsrCreacion);
                                    $objServicioHist->setFeCreacion(new \DateTime('now'));
                                    $objServicioHist->setEstado($objServicio->getEstado());
                                    $emComercial->persist($objServicioHist);
                                    $emComercial->flush();
                                }
                            }
                        }
                    }
                }
            }
            else if( $strAccion=="rechazar" )
            {
                $strEstadoSol   = "Rechazado";
                $strObservacion = "Se rechaza la solicitud de proyecto con la sgte. observación: ".$strObservacionRechazar;
                for($i=0;$i<count($arraySolicitudes);$i++)
                {
                    //Bloque que notifica al subgerente el estado rechazado del proyecto.
                    $arrayParametros['intIdCotizacion'] = trim($arraySolicitudes[$i]);
                    $arrayParametros['strBandera']      = "TOTALIZADO";
                    $arrayResultado                     = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                      ->getSolicitudProyecto($arrayParametros);
                    if( !empty($arrayResultado["registros"]) && isset($arrayResultado["registros"]) )
                    {
                        $arrayRegistros          = $arrayResultado['registros'];
                        $arrayParametrosVendedor = array("strLogin"=>$arrayRegistros[0]["USR_VENDEDOR"]);
                        $arrayResultadoCorreo    = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                               ->getSubgerentePorLoginVendedor($arrayParametrosVendedor);

                        if( !empty($arrayResultadoCorreo["registros"]) && isset($arrayResultadoCorreo["registros"]) )
                        {
                            $arrayRegistrosCorreo = $arrayResultadoCorreo['registros'];

                            $arrayCorreos = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                        ->getContactosByLoginPersonaAndFormaContacto($arrayRegistrosCorreo[0]["LOGIN_SUBGERENTE"],
                                                                                                    'Correo Electronico');
                            if( !empty($arrayCorreos) && is_array($arrayCorreos) )
                            {
                                foreach($arrayCorreos as $arrayItem)
                                {
                                    if( !empty($arrayItem['valor']) && isset($arrayItem['valor']) )
                                    {
                                        $arrayDestinatarios[] = $arrayItem['valor'];
                                    }
                                }
                            }

                            $arrayCorreos = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                        ->getContactosByLoginPersonaAndFormaContacto($arrayRegistrosCorreo[0]["LOGIN_VENDEDOR"],
                                                                                                    'Correo Electronico');
                            if( !empty($arrayCorreos) && is_array($arrayCorreos) )
                            {
                                foreach($arrayCorreos as $arrayItem)
                                {
                                    if( !empty($arrayItem['valor']) && isset($arrayItem['valor']) )
                                    {
                                        $arrayDestinatarios[] = $arrayItem['valor'];
                                    }
                                }
                            }
                            $arrayParametrosMail  = array("strNombreCliente"         => $arrayRegistros[0]["NOMBRE_CLIENTE"],
                                                          "strIdentificacionCliente" => $arrayRegistros[0]["IDENTIFICACION_CLIENTE"],
                                                          "strObservacion"           => $strObservacion);
                            $serviceEnvioPlantilla->generarEnvioPlantilla("RECHAZO DE SOLICITUD DE PROYECTO", 
                                                                          array_unique($arrayDestinatarios), 
                                                                          'RECHAZA_SOL', 
                                                                          $arrayParametrosMail,
                                                                          $strPrefijoEmpresa,
                                                                          '',
                                                                          '',
                                                                          null, 
                                                                          true,
                                                                          'notificaciones_telcos@telconet.ec');
                        }
                    }
                }
                //Bloque que obtiene las caracteristicas del servicio para rechazarlas.
                $objAdmiCaractProyectoTipo   = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                            ->findOneBy(array("descripcionCaracteristica" => 'TIPO_PROYECTO',
                                                                              "estado"                    => "Activo"));
                $objAdmiCaractProyectoNombre = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                            ->findOneBy(array("descripcionCaracteristica" => 'COTIZACION_NOMBRE',
                                                                              "estado"                    => "Activo"));
                $objAdmiCaractProyectoId     = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                            ->findOneBy(array("descripcionCaracteristica" => 'COTIZACION_PRODUCTOS',
                                                                              "estado"                    => "Activo"));
                $objAdmiCaractPropuestaId     = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                            ->findOneBy(array("descripcionCaracteristica" => 'ID_PROPUESTA',
                                                                              "estado"                    => "Activo"));
                $objAdmiCaractPropuestaNombre = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                            ->findOneBy(array("descripcionCaracteristica" => 'NOMBRE_PROPUESTA',
                                                                              "estado"                    => "Activo"));

                if( (!empty($objAdmiCaractProyectoTipo) && is_object($objAdmiCaractProyectoTipo))
                    && (!empty($objAdmiCaractProyectoNombre) && is_object($objAdmiCaractProyectoNombre))
                    && (!empty($objAdmiCaractProyectoId) && is_object($objAdmiCaractProyectoId))
                    && (!empty($objAdmiCaractPropuestaId) && is_object($objAdmiCaractPropuestaId))
                    && (!empty($objAdmiCaractPropuestaNombre) && is_object($objAdmiCaractPropuestaNombre)) )
                {
                    $arrayCaracteristicas[]             = $objAdmiCaractProyectoTipo->getId();
                    $arrayCaracteristicas[]             = $objAdmiCaractProyectoNombre->getId();
                    $arrayCaracteristicas[]             = $objAdmiCaractProyectoId->getId();
                    $arrayCaracteristicas[]             = $objAdmiCaractPropuestaId->getId();
                    $arrayCaracteristicas[]             = $objAdmiCaractPropuestaNombre->getId();
                    $arrayParametros['intIdCotizacion'] = $arraySolicitudes;
                    $arrayParametros['strBandera']      = "";
                    $arrayResultado                     = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                      ->getSolicitudProyecto($arrayParametros);
                    if( !empty($arrayResultado["registros"]) && isset($arrayResultado["registros"]) )
                    {
                        foreach($arrayResultado['registros'] as $arrayDatos)
                        {
                            $objServicio = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                       ->findOneBy(array('id'=>$arrayDatos["ID_SERVICIO"]));
                            if( !empty($objServicio) && is_object($objServicio) )
                            {
                                if( !empty($arrayCaracteristicas) && is_array($arrayCaracteristicas) )
                                {
                                    for($i=0;$i<count($arrayCaracteristicas);$i++)
                                    {
                                        $objProdCaract = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                     ->findOneBy(array("productoId" => $objServicio->getProductoId(),
                                                                                       "caracteristicaId"=>$arrayCaracteristicas[$i]));
                                        if( !empty($objProdCaract) && is_object($objProdCaract) )
                                        {
                                            $objInfoServicioProdCaract = $emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                                     ->findOneBy(array("servicioId"                => $objServicio->getId(),
                                                                                                       "productoCaracterisiticaId" => $objProdCaract->getId()));
                                        }
                                        if( !empty($objInfoServicioProdCaract) && is_object($objInfoServicioProdCaract) )
                                        {
                                            if((!empty($objAdmiCaractPropuestaId) && is_object($objAdmiCaractPropuestaId)) 
                                                && ($arrayCaracteristicas[$i] == $objAdmiCaractPropuestaId->getId()))
                                            {
                                                $arrayPropuesta[] = $objInfoServicioProdCaract->getValor();
                                            }
                                            $objInfoServicioProdCaract->setEstado($strEstadoSol);
                                            $objInfoServicioProdCaract->setFeUltMod(new \DateTime('now'));
                                            $objInfoServicioProdCaract->setUsrUltMod($strUsrCreacion);
                                            $emComercial->persist($objInfoServicioProdCaract);
                                            $emComercial->flush();
                                        }
                                    }
                                }
                                $entityDetalleSolicitud = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                      ->findOneById($arrayDatos["ID_DETALLE_SOLICITUD"]);
                                $entityDetalleSolicitud->setObservacion($strObservacion);
                                $entityDetalleSolicitud->setEstado($strEstadoSol);
                                if( !empty($intIdMotivoRechazar) )
                                {
                                    $entityDetalleSolicitud->setMotivoId($intIdMotivoRechazar);
                                }
                                $emComercial->persist($entityDetalleSolicitud);
                                $emComercial->flush();

                                $entityDetalleSolHist = new InfoDetalleSolHist();
                                $entityDetalleSolHist->setDetalleSolicitudId($entityDetalleSolicitud);
                                $entityDetalleSolHist->setObservacion($strObservacion);
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

                                $objServicioHist = new InfoServicioHistorial();
                                $objServicioHist->setServicioId($objServicio);
                                if( !empty($intIdMotivoRechazar) )
                                {
                                    $objServicioHist->setMotivoId($intIdMotivoRechazar);
                                }
                                $objServicioHist->setObservacion($strObservacion);
                                $objServicioHist->setIpCreacion($strIpCreacion);
                                $objServicioHist->setUsrCreacion($strUsrCreacion);
                                $objServicioHist->setFeCreacion(new \DateTime('now'));
                                $objServicioHist->setEstado($objServicio->getEstado());
                                $emComercial->persist($objServicioHist);
                                $emComercial->flush();
                            }
                        }
                        if(is_array($arrayPropuesta) && !empty($arrayPropuesta))
                        {
                            $arrayPropuesta = array_unique($arrayPropuesta);
                            for($intContador=0;$intContador<count($arrayPropuesta);$intContador++)
                            {
                                $arrayParametros      = array("intIdPropuesta"         => $arrayPropuesta[$intContador],
                                                              "strBandera"             => "DETALLE",
                                                              "strOpcion"              => "ELIMINAR",
                                                              "strPrefijoEmpresa"      => $strPrefijoEmpresa, 
                                                              "strCodEmpresa"          => $intIdEmpresa);
                                $arrayParametrosWSCrm = array("arrayParametrosCRM"     => $arrayParametros,
                                                              "strOp"                  => 'editPropuesta',
                                                              "strFuncion"             => 'procesar');
                                $arrayRespuestaWSCrm  = $serviceTelcoCrm->getRequestCRM($arrayParametrosWSCrm);
                                if(!empty($arrayRespuestaWSCrm["error"]) && isset($arrayRespuestaWSCrm["error"]))
                                {
                                    throw new \Exception('Error al Actualizar Estado de la propuesta en TelcoCrm: '.$arrayRespuestaWSCrm["error"]);
                                }
                            }
                        }
                    }
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
            $serviceUtil->insertError('Telcos', 'getAprobarRechazarSolicitudAction', $strResponse . $e->getMessage(), $strUsrCreacion, $strIpCreacion);
        }
        return new Response($strResponse);
    }

   /**
     * Documentación para la función 'getEstadosAction'.
     *
     * Función que obtiene los estados de las solicitudes de proyecto.
     *
     * @return Response - Lista de estados.
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.0 03-07-2020
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
                                                ->get('PARAMETROS_TELCOCRM', 
                                                      'COMERCIAL', 
                                                      '', 
                                                      'ESTADOS_PROYECTOS', 
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
     * Documentación para la función 'getMotivosAction'.
     *
     * Función que obtiene los motivos de las solicitudes de proyecto.
     *
     * @return Response - Lista de motivos.
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.0 03-07-2020
     *
     */
    public function getMotivosAction()
    {
        try
        {
            $objRequest             = $this->getRequest();
            $strAccion              = $objRequest->get('strAccion') ? $objRequest->get('strAccion'):"";
            $objSession             = $objRequest->getSession();
            $intIdEmpresa           = $objSession->get('idEmpresa')           ? $objSession->get('idEmpresa'):"";
            $intIdPersonaEmpresaRol = $objSession->get('idPersonaEmpresaRol') ? $objSession->get('idPersonaEmpresaRol'):"";
            $strUsrCreacion         = $objSession->get('user')                ? $objSession->get('user'):"";
            $strIpCreacion          = $objRequest->getClientIp()              ? $objRequest->getClientIp():'127.0.0.1';
            $arrayMotivos           = array();
            $emGeneral              = $this->get('doctrine')->getManager('telconet');
            $serviceUtil            = $this->get('schema.Util');
            $serviceComercial       = $this->get('comercial.Comercial');

            if( !empty($strAccion) && $strAccion == "reasignar" )
            {
                $strDescripcion = "MOTIVO_REASIGNACION_PROYECTO";
            }
            elseif( !empty($strAccion) && $strAccion == "rechazar" )
            {
                $strDescripcion = "MOTIVO_RECHAZO_PROYECTO";
            }
            else
            {
                throw new \Exception("La acción a ejecutar es un campo obligatorio.");
            }

            /**
             * BLOQUE QUE VALIDA LA CARACTERISTICA 'CARGO_GRUPO_ROLES_PERSONAL' ASOCIADA A EL USUARIO LOGONEADO 
             */
            $arrayPersonalEmpresaRol       = array('intIdPersonEmpresaRol' => $intIdPersonaEmpresaRol,
                                                   'strUsrCreacion'        => $strUsrCreacion,
                                                   'strIpCreacion'         => $strIpCreacion);
            $arrayResultadoCaracteristicas = $serviceComercial->getCaracteristicasPersonalEmpresaRol($arrayPersonalEmpresaRol);

            if( ( !empty($arrayResultadoCaracteristicas) && is_array($arrayResultadoCaracteristicas) ) &&
                ( isset($arrayResultadoCaracteristicas['strCargoPersonal']) && !empty($arrayResultadoCaracteristicas['strCargoPersonal']) ) )
            {
                $strTipoPersonal = $arrayResultadoCaracteristicas['strCargoPersonal'];
                if( !empty($strTipoPersonal) && $strTipoPersonal == "JEFE_DEPARTAMENTAL_PMO" )
                {
                    $strTipoProyecto = "PMO";
                }
                elseif( !empty($strTipoPersonal) && $strTipoPersonal == "JEFE_DEPARTAMENTAL_PYL" )
                {
                    $strTipoProyecto = "PYL";
                }
            }
            else
            {
                throw new \Exception('Persona en sesión no tiene el perfil para poder visualizar los motivos.');
            }
            $arrayLisMotivo = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                        ->get('PARAMETROS_TELCOCRM', 
                                              'COMERCIAL', 
                                              '', 
                                              $strDescripcion,
                                              $strTipoProyecto,
                                              '',
                                              '',
                                              '',
                                              '',
                                              $intIdEmpresa);
            if( empty($arrayLisMotivo) || !is_array($arrayLisMotivo) )
            {
                throw new \Exception("Lista de motivo se encuentra vacía.");
            }
            $objListMotivos = $emGeneral->getRepository('schemaBundle:AdmiMotivo')
                                        ->findMotivosPorModuloPorItemMenuPorAccion('admiSolicitudProyecto', 'Solicitud de Proyecto', 'index');

            foreach($objListMotivos as $objItem)
            {
                foreach($arrayLisMotivo as $arrayItem)
                {
                    if( !empty($arrayItem['valor2'])&&isset($arrayItem['valor2']) )
                    {
                        if( $arrayItem['valor2'] == $objItem->getNombreMotivo() )
                        {
                            $arrayMotivos[] = array('id'     => $objItem->getId(), 
                                                    'nombre' => $objItem->getNombreMotivo());
                        }
                    }
                }
            }
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError('Telcos', 'getMotivosAction', $e->getMessage(), $strUsrCreacion, $strIpCreacion);
        }
        $objResponse = new Response(json_encode(array('motivos' => $arrayMotivos)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

   /**
     * Documentación para la función 'getUsuariosAction'.
     *
     * Función que obtiene el listado de usuarios para la asignación de proyectos.
     *
     * @return Response - Lista de usuarios.
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.0 03-07-2020
     *
     */
    public function getUsuariosAction()
    {
        try
        {
            $objRequest             = $this->getRequest();
            $objSession             = $objRequest->getSession();
            $intIdEmpresa           = $objSession->get('idEmpresa')           ? $objSession->get('idEmpresa'):"";
            $intIdPersonaEmpresaRol = $objSession->get('idPersonaEmpresaRol') ? $objSession->get('idPersonaEmpresaRol'):"";
            $strUsrCreacion         = $objSession->get('user')                ? $objSession->get('user'):"";
            $strIpCreacion          = $objRequest->getClientIp()              ? $objRequest->getClientIp():'127.0.0.1';
            $arrayUsuarios          = array();
            $emGeneral              = $this->get('doctrine')->getManager('telconet');
            $serviceUtil            = $this->get('schema.Util');
            $serviceComercial       = $this->get('comercial.Comercial');

            /**
             * BLOQUE QUE VALIDA LA CARACTERISTICA 'CARGO_GRUPO_ROLES_PERSONAL' ASOCIADA A EL USUARIO LOGONEADO 
             */
            $arrayPersonalEmpresaRol       = array('intIdPersonEmpresaRol' => $intIdPersonaEmpresaRol,
                                                   'strUsrCreacion'        => $strUsrCreacion,
                                                   'strIpCreacion'         => $strIpCreacion);
            $arrayResultadoCaracteristicas = $serviceComercial->getCaracteristicasPersonalEmpresaRol($arrayPersonalEmpresaRol);

            if( ( !empty($arrayResultadoCaracteristicas) && is_array($arrayResultadoCaracteristicas) ) &&
                ( isset($arrayResultadoCaracteristicas['strCargoPersonal']) && !empty($arrayResultadoCaracteristicas['strCargoPersonal']) ) )
            {
                $strTipoPersonal = $arrayResultadoCaracteristicas['strCargoPersonal'];
                if( !empty($strTipoPersonal) && $strTipoPersonal == "JEFE_DEPARTAMENTAL_PMO" )
                {
                    $strTipoProyecto = "PMO";
                }
                elseif( !empty($strTipoPersonal) && $strTipoPersonal == "JEFE_DEPARTAMENTAL_PYL" )
                {
                    $strTipoProyecto = "PYL";
                }
            }
            else
            {
                throw new \Exception('Persona en sesión no tiene el perfil para poder visualizar los usuarios.');
            }
            $arrayListUsuarios = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                           ->get('PARAMETROS_TELCOCRM', 
                                                 'COMERCIAL', 
                                                 '', 
                                                 'LISTADO_USUARIOS_PROYECTO', 
                                                 $strTipoProyecto, 
                                                 '', 
                                                 '', 
                                                 '', 
                                                 '', 
                                                 $intIdEmpresa);
            foreach($arrayListUsuarios as $arrayItem)
            {
                $arrayUsuarios[] = array('id'     => $arrayItem['valor2'],
                                         'nombre' => $arrayItem['valor3']);
            }
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError('Telcos', 'getUsuariosAction', $e->getMessage(), $strUsrCreacion, $strIpCreacion);
        }
        $objResponse = new Response(json_encode(array('usuarios' => $arrayUsuarios)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
}