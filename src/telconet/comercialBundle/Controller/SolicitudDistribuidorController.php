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


class SolicitudDistribuidorController extends Controller implements TokenAuthenticatedController
{
    private $strTipoSolicitud       = "SOLICITUD DE DISTRIBUIDOR";
    private $strCaracIdentificacion = "IDENTIFICACION_CLT_DISTRIBUIDOR";
    private $strCaracRazonSocial    = "RAZON_SOCIAL_CLT_DISTRIBUIDOR";
    private $strCaracVendedor       = "VENDEDOR_CLT_DISTRIBUIDOR";
    private $strCaracProducto       = "PRODUCTOS_DISTRIBUIDOR";

   /**
     * @Secure(roles="ROLE_460-1")
     *
     * Documentación para la función 'indexAction'.
     *
     * Función que carga la pantalla de solicitud de reactivación.
     *
     * @return render Redirecciona al index de la opción.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 11-05-2021
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

            if( $this->get('security.context')->isGranted('ROLE_460-1') )
            {
                $arrayRolesPermitidos[] = 'ROLE_460-1';
            }
            if( $this->get('security.context')->isGranted('ROLE_460-6') )
            {
                $arrayRolesPermitidos[] = 'ROLE_460-6';
            }
            if( $this->get('security.context')->isGranted('ROLE_460-7') )
            {
                $arrayRolesPermitidos[] = 'ROLE_460-7';
            }
            if( $this->get('security.context')->isGranted('ROLE_460-163') )
            {
                $arrayRolesPermitidos[] = 'ROLE_460-163';
            }
            if( $this->get('security.context')->isGranted('ROLE_460-94') )
            {
                $arrayRolesPermitidos[] = 'ROLE_460-94';
            }
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError('TelcoS+', 'SolicitudDistribuidorController.indexAction', $e->getMessage(), $strUsrCreacion, $strIpCreacion);
        }
        return $this->render('comercialBundle:SolicitudDistribuidor:index.html.twig', array('rolesPermitidos'  => $arrayRolesPermitidos));
     }

    /**
     * Documentación para la función 'newAction'.
     *
     * Función que crea las solicitudes.
     *
     * @return Response - Mensaje de exito.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 11-05-2021
     *
     */
    public function newAction()
    {
        try
        {
            $objRequest             = $this->getRequest();
            $objSession             = $objRequest->getSession();
            $strUsrCreacion         = $objSession->get('user')              ? $objSession->get('user'):"";
            $strIpCreacion          = $objRequest->getClientIp()            ? $objRequest->getClientIp():'127.0.0.1';
            $strVendedor            = $objRequest->get("strVendedor")       ? $objRequest->get("strVendedor"):"";
            $strIdentificacion      = $objRequest->get("strIdentificacion") ? $objRequest->get("strIdentificacion"):"";
            $strRazonSocial         = $objRequest->get("strRazonSocial")    ? $objRequest->get("strRazonSocial"):"";
            $strObservacion         = $objRequest->get("strObservacion")    ? $objRequest->get("strObservacion"):"";
            $strPrefijoEmpresa      = $objSession->get('prefijoEmpresa')    ? $objSession->get('prefijoEmpresa'):"";
            $arrayLineaNegocio      = $objRequest->get("arrayLineaNegocio") ? $objRequest->get("arrayLineaNegocio"):"";
            $arrayProductos         = $objRequest->get("arrayProductos")    ? $objRequest->get("arrayProductos"):"";
            $emComercial            = $this->get('doctrine')->getManager('telconet');
            $strProductos           = implode("|",$arrayProductos);
            $serviceUtil            = $this->get('schema.Util');
            $strCodigoError         = "";
            $serviceEnvioPlantilla  = $this->get('soporte.EnvioPlantilla');
            $strEstado              = 'Pendiente';
            if(empty($strVendedor) || empty($strIdentificacion) || empty($strRazonSocial) || empty($strObservacion) || empty($strProductos))
            {
                $strCodigoError = "204";
                throw new \Exception("Estimado usuario, debe llenar todos los campos para crear la solicitud.");
            }
            $arrayParametros                           = array();
            $arrayParametros['strIdentificacion']      = $strIdentificacion;
            $arrayParametros['strTipoSolicitud']       = "SOLICITUD DE DISTRIBUIDOR";
            $arrayParametros['strCaracIdentificacion'] = "IDENTIFICACION_CLT_DISTRIBUIDOR";
            $arrayParametros['strCaracRazonSocial']    = "RAZON_SOCIAL_CLT_DISTRIBUIDOR";
            $arrayParametros['strCaracVendedor']       = "VENDEDOR_CLT_DISTRIBUIDOR";
            $arrayParametros['strCaracProducto']       = "PRODUCTOS_DISTRIBUIDOR";
            $arrayParametros['strEstadoNotIn']         = "Rechazado";
            $arrayParametros['strPrefijoEmpresa']      = $strPrefijoEmpresa;
            $arrayResultado                            = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                     ->getSolicitudDistribuidor($arrayParametros);
            if(!empty($arrayResultado["error"]) && isset($arrayResultado["error"]))
            {
                throw new \Exception($arrayResultado["error"]);
            }
            if(!empty($arrayResultado["total"]) && isset($arrayResultado["total"]) && $arrayResultado["total"] > 0)
            {
                $strCodigoError = "204";
                throw new \Exception("Estimado Usuario ya existe una solicitud en proceso.");
            }
            $arrayResultadoCaracteristicas = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                         ->getCargosPersonas($strUsrCreacion);
            if(!empty($arrayResultadoCaracteristicas))
            {
                $arrayResultadoCaracteristicas = $arrayResultadoCaracteristicas[0];
                $strTipoPersonal               = $arrayResultadoCaracteristicas['STRCARGOPERSONAL'] ? 
                                                 $arrayResultadoCaracteristicas['STRCARGOPERSONAL'] : 'Otros';
            }
            $strEstado = (!empty($strTipoPersonal) && ($strTipoPersonal == 'SUBGERENTE' || $strTipoPersonal == 'GERENTE_VENTAS'))
                         ? 'Pendiente Gerente' : 'Pendiente';

            $objTipoSolicitud = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                            ->findOneBy(array("descripcionSolicitud" => $this->strTipoSolicitud,
                                                              "estado"               => "Activo"));
            if(!is_object($objTipoSolicitud) && empty($objTipoSolicitud))
            {
                throw new \Exception("No existe Objeto para el tipo de Solicitud ".$this->strTipoSolicitud.".");
            }
            $objAdmiCaract = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                         ->findOneBy(array("descripcionCaracteristica" => $this->strCaracIdentificacion,
                                                           "estado"                    => "Activo"));
            if(!is_object($objAdmiCaract) && empty($objAdmiCaract))
            {
                throw new \Exception("No existe Objeto para la característica ".$this->strCaracIdentificacion.".");
            }
            $objAdmiCaractVend = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                             ->findOneBy(array("descripcionCaracteristica" => $this->strCaracVendedor,
                                                               "estado"                    => "Activo"));
            if(!is_object($objAdmiCaractVend) && empty($objAdmiCaractVend))
            {
                throw new \Exception("No existe Objeto para la característica ".$this->strCaracVendedor.".");
            }
            $objAdmiCaractRazonSocial = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                    ->findOneBy(array("descripcionCaracteristica" => $this->strCaracRazonSocial,
                                                                      "estado"                    => "Activo"));
            if(!is_object($objAdmiCaractRazonSocial) && empty($objAdmiCaractRazonSocial))
            {
                throw new \Exception("No existe Objeto para la característica ".$this->strCaracRazonSocial.".");
            }
            $objAdmiCaractProducto = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                 ->findOneBy(array("descripcionCaracteristica" => $this->strCaracProducto,
                                                                   "estado"                    => "Activo"));
            if(!is_object($objAdmiCaractProducto) && empty($objAdmiCaractProducto))
            {
                throw new \Exception("No existe Objeto para la característica ".$this->strCaracProducto.".");
            }
            $emComercial->getConnection()->beginTransaction();
            $objDetInfoSol= new InfoDetalleSolicitud();
            $objDetInfoSol->setTipoSolicitudId($objTipoSolicitud);
            $objDetInfoSol->setObservacion($strObservacion);
            $objDetInfoSol->setFeCreacion(new \DateTime('now'));
            $objDetInfoSol->setUsrCreacion($strUsrCreacion);
            $objDetInfoSol->setEstado($strEstado);
            $emComercial->persist($objDetInfoSol);
            $emComercial->flush();
            $objDetInfoSolHist = new InfoDetalleSolHist();
            $objDetInfoSolHist->setDetalleSolicitudId($objDetInfoSol);
            $objDetInfoSolHist->setEstado($objDetInfoSol->getEstado());
            $objDetInfoSolHist->setFeCreacion(new \DateTime('now'));
            $objDetInfoSolHist->setUsrCreacion($strUsrCreacion);
            $objDetInfoSolHist->setObservacion($strObservacion);
            $objDetInfoSolHist->setIpCreacion($strIpCreacion);
            $emComercial->persist($objDetInfoSolHist);
            $emComercial->flush();
            $objDetInfoSolCarac = new InfoDetalleSolCaract();
            $objDetInfoSolCarac->setCaracteristicaId($objAdmiCaract);
            $objDetInfoSolCarac->setValor($strIdentificacion);
            $objDetInfoSolCarac->setDetalleSolicitudId($objDetInfoSol);
            $objDetInfoSolCarac->setEstado($objDetInfoSol->getEstado());
            $objDetInfoSolCarac->setFeCreacion(new \DateTime('now'));
            $objDetInfoSolCarac->setUsrCreacion($strUsrCreacion);
            $emComercial->persist($objDetInfoSolCarac);
            $emComercial->flush();
            $objDetInfoSolCarac = new InfoDetalleSolCaract();
            $objDetInfoSolCarac->setCaracteristicaId($objAdmiCaractVend);
            $objDetInfoSolCarac->setValor($strVendedor);
            $objDetInfoSolCarac->setDetalleSolicitudId($objDetInfoSol);
            $objDetInfoSolCarac->setEstado($objDetInfoSol->getEstado());
            $objDetInfoSolCarac->setFeCreacion(new \DateTime('now'));
            $objDetInfoSolCarac->setUsrCreacion($strUsrCreacion);
            $emComercial->persist($objDetInfoSolCarac);
            $emComercial->flush();
            $objDetInfoSolCarac = new InfoDetalleSolCaract();
            $objDetInfoSolCarac->setCaracteristicaId($objAdmiCaractRazonSocial);
            $objDetInfoSolCarac->setValor($strRazonSocial);
            $objDetInfoSolCarac->setDetalleSolicitudId($objDetInfoSol);
            $objDetInfoSolCarac->setEstado($objDetInfoSol->getEstado());
            $objDetInfoSolCarac->setFeCreacion(new \DateTime('now'));
            $objDetInfoSolCarac->setUsrCreacion($strUsrCreacion);
            $emComercial->persist($objDetInfoSolCarac);
            $emComercial->flush();
            $objDetInfoSolCarac = new InfoDetalleSolCaract();
            $objDetInfoSolCarac->setCaracteristicaId($objAdmiCaractProducto);
            $objDetInfoSolCarac->setValor($strProductos);
            $objDetInfoSolCarac->setDetalleSolicitudId($objDetInfoSol);
            $objDetInfoSolCarac->setEstado($objDetInfoSol->getEstado());
            $objDetInfoSolCarac->setFeCreacion(new \DateTime('now'));
            $objDetInfoSolCarac->setUsrCreacion($strUsrCreacion);
            $emComercial->persist($objDetInfoSolCarac);
            $emComercial->flush();
            if($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->commit();
                $emComercial->getConnection()->close();
            }
            $strMensajeCorreo = "creó una solicitud";
            $arrayParametrosVendedor = array("strLogin"=>$strVendedor);
            $arrayResultadoCorreo    = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                   ->getSubgerentePorLoginVendedor($arrayParametrosVendedor);

            if(!empty($arrayResultadoCorreo["registros"]) && isset($arrayResultadoCorreo["registros"]))
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
            }
            $arrayCorreos = $emComercial->getRepository('schemaBundle:InfoPersona')
                                        ->getContactosByLoginPersonaAndFormaContacto($strUsrCreacion,
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
                                        ->getContactosByLoginPersonaAndFormaContacto($strVendedor,
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
            $arrayParametrosMail  = array("strMensajeCorreo"         => $strMensajeCorreo,
                                          "strNombreCliente"         => $strRazonSocial,
                                          "strIdentificacionCliente" => $strIdentificacion,
                                          "strObservacion"           => $strObservacion);
            $serviceEnvioPlantilla->generarEnvioPlantilla("SOLICITUD DE DISTRIBUIDOR", 
                                                          array_unique($arrayDestinatarios), 
                                                          'DISTRIBUIDOR',
                                                          $arrayParametrosMail,
                                                          $strPrefijoEmpresa,
                                                          '',
                                                          '',
                                                          null, 
                                                          true,
                                                          'notificaciones_telcos@telconet.ec');
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
            if($strCodigoError == "204")
            {
                $strResponse = $e->getMessage();
            }
            $serviceUtil->insertError('TelcoS+', 
                                      'SolicitudDistribuidorController.newAction', 
                                      $e->getMessage(),
                                      $strUsrCreacion,
                                      $strIpCreacion);
        }
        return new Response($strResponse);
    }

   /**
     * @Secure(roles="ROLE_460-7")
     *
     * Documentación para la función 'gridAction'.
     *
     * Función que retorna el listado de solicitudes.
     *
     * @return $objResponse - Listado de Solicitudes.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 11-05-2021
     *
     */
    public function gridAction()
    {
        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $strIdentificacion      = $objRequest->get("strIdentificacion")   ? $objRequest->get("strIdentificacion"):"";
        $strRazonSocial         = $objRequest->get("strRazonSocial")      ? $objRequest->get("strRazonSocial"):"";
        $strFechaInicio         = $objRequest->get("strFechaInicio")      ? $objRequest->get("strFechaInicio"):"";
        $strFechaFin            = $objRequest->get("strFechaFin")         ? $objRequest->get("strFechaFin"):"";
        $strEstadoFiltro        = $objRequest->get("strEstado")           ? $objRequest->get("strEstado"):"";
        $intIdEmpresa           = $objSession->get('idEmpresa')           ? $objSession->get('idEmpresa'):"";
        $intIdPersonaEmpresaRol = $objSession->get('idPersonaEmpresaRol') ? $objSession->get('idPersonaEmpresaRol'):"";
        $intIdCanton            = $objSession->get('intIdCanton')         ? $objSession->get('intIdCanton') : "";
        $strUsrCreacion         = $objSession->get('user')                ? $objSession->get('user'):"";
        $strIpCreacion          = $objRequest->getClientIp()              ? $objRequest->getClientIp():'127.0.0.1';
        $strPrefijoEmpresa      = $objSession->get('prefijoEmpresa')      ? $objSession->get('prefijoEmpresa'):"";
        $emComercial            = $this->get('doctrine')->getManager('telconet');
        $serviceUtil            = $this->get('schema.Util');
        $serviceUtilidades      = $this->get('administracion.Utilidades');
        $intTotal               = 0;
        $arraySolicitud         = array();
        $strTipoPersonal        = 'Otros';
        $boolIgnorarCargo       = false;
        try
        {
            if(empty($intIdCanton))
            {
                throw new \Exception('Error al obtener el cantón del usuario en sesión.');
            }
            $objCanton = $emComercial->getRepository("schemaBundle:AdmiCanton")->find($intIdCanton);
            if(empty($objCanton) || !is_object($objCanton))
            {
                throw new \Exception('Error al obtener el cantón del usuario en sesión.');
            }
            $strRegionSesion   = $objCanton->getRegion();
            /**
             * Bloque que válida si el usuario en sesión pertenece a la lista 
             * de personas que puede aprobar solicitudes, sin importar el cargo.
             */
            $arrayListaUsuarios = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                              ->get('PARAMETROS_SOLICITUD_DISTRIBUIDOR', 
                                                    'COMERCIAL', 
                                                    '', 
                                                    'LISTA_USUARIO_APROBACION', 
                                                    $strUsrCreacion,
                                                    $strRegionSesion,
                                                    '',
                                                    '',
                                                    '',
                                                    $intIdEmpresa);
            if(!empty($arrayListaUsuarios) && is_array($arrayListaUsuarios))
            {
                $boolIgnorarCargo = true;
                $strEstado        = (!empty($strEstadoFiltro) && $strEstadoFiltro != "") 
                                     ? $strEstadoFiltro: $arrayListaUsuarios[0]["valor3"];
                $strTipoPersonal  = $arrayListaUsuarios[0]["valor4"];
            }
            $arrayResultadoCaracteristicas = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                         ->getCargosPersonas($strUsrCreacion);
            if(!empty($arrayResultadoCaracteristicas) && !$boolIgnorarCargo)
            {
                $arrayResultadoCaracteristicas = $arrayResultadoCaracteristicas[0];
                $strTipoPersonal               = $arrayResultadoCaracteristicas['STRCARGOPERSONAL'] ? 
                                                 $arrayResultadoCaracteristicas['STRCARGOPERSONAL'] : 'Otros';
            }
            if(empty($strEstadoFiltro))
            {
                if(!empty($strTipoPersonal) && ($strTipoPersonal == "GERENTE_VENTAS"))
                {
                    $strEstado = "Pendiente Gerente";
                }
                else
                {
                    $strEstado = "Pendiente";
                }
            }
            else
            {
                $strEstado = $strEstadoFiltro;
            }
            $arrayRolesNoIncluidos = array();
            $arrayParametrosRoles  = array( 'strCodEmpresa'     => $intIdEmpresa,
                                            'strValorRetornar'  => 'descripcion',
                                            'strNombreProceso'  => 'JEFES',
                                            'strNombreModulo'   => 'COMERCIAL',
                                            'strNombreCabecera' => 'ROLES_NO_PERMITIDOS',
                                            'strUsrCreacion'    => $strUsrCreacion,
                                            'strIpCreacion'     => $strIpCreacion );
            $arrayResultadosRolesNoIncluidos = $serviceUtilidades->getDetallesParametrizables($arrayParametrosRoles);

            if( isset($arrayResultadosRolesNoIncluidos['resultado']) && !empty($arrayResultadosRolesNoIncluidos['resultado']) )
            {
                foreach( $arrayResultadosRolesNoIncluidos['resultado'] as $strRolNoIncluido )
                {
                    $arrayRolesNoIncluidos[] = $strRolNoIncluido;
                }
            }

            /**
             * BLOQUE QUE OBTIENE EL LISTADO DE VENDEDORES KAMS
             */
            $arrayParametrosKam                          = array();
            $arrayResultadoVendedoresKam                 = array();
            $arrayParametrosKam['strPrefijoEmpresa']     = $strPrefijoEmpresa;
            $arrayParametrosKam['strCodEmpresa']         = $intIdEmpresa;
            $arrayParametrosKam['strEstadoActivo']       = 'Activo';
            $arrayParametrosKam['strDescCaracteristica'] = 'CARGO_GRUPO_ROLES_PERSONAL';
            $arrayParametrosKam['strNombreParametro']    = 'GRUPO_ROLES_PERSONAL';
            $arrayParametrosKam['strDescCargo']          = 'GERENTE_VENTAS';
            $arrayParametrosKam['strDescRolNoPermitido'] = 'ROLES_NO_PERMITIDOS';
            $arrayResultadoVendedoresKam                 = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                                        ->getVendedoresKams($arrayParametrosKam);
            if(isset($arrayResultadoVendedoresKam['error']) && !empty($arrayResultadoVendedoresKam['error']))
            {
                throw new \Exception($arrayResultadoVendedoresKam['error']);
            }
            if(!empty($arrayResultadoVendedoresKam['vendedoresKam']) && is_array($arrayResultadoVendedoresKam['vendedoresKam']))
            {
                foreach($arrayResultadoVendedoresKam['vendedoresKam'] as $arrayItem)
                {
                    $arrayLoginVendedoresKam[] = $arrayItem['LOGIN'];
                }
            }

            $arrayParametros                           = array();
            $arrayParametros['strIdentificacion']      = $strIdentificacion;
            $arrayParametros['strRazonSocial']         = $strRazonSocial;
            $arrayParametros['strFechaInicio']         = $strFechaInicio;
            $arrayParametros['strFechaFin']            = $strFechaFin;
            $arrayParametros['strTipoSolicitud']       = $this->strTipoSolicitud;
            $arrayParametros['strCaracIdentificacion'] = $this->strCaracIdentificacion;
            $arrayParametros['strCaracRazonSocial']    = $this->strCaracRazonSocial;
            $arrayParametros['strCaracVendedor']       = $this->strCaracVendedor;
            $arrayParametros['strCaracProducto']       = $this->strCaracProducto;
            $arrayParametros['strTipoPersonal']        = $strTipoPersonal;
            $arrayParametros['strUsrCreacion']         = $strUsrCreacion;
            $arrayParametros['strPrefijoEmpresa']      = $strPrefijoEmpresa;
            $arrayParametros['strEstado']              = $strEstado;
            $arrayParametros['intIdPersonaEmpresaRol'] = $intIdPersonaEmpresaRol;
            $arrayParametros['boolIgnorarCargo']       = $boolIgnorarCargo;
            $arrayParametros['strRegion']              = $strRegionSesion;
            $arrayParametros['intIdEmpresa']           = $intIdEmpresa;
            $arrayParametros['arrayVendedoresKam']     = $arrayLoginVendedoresKam;
            $arrayParametros['arrayRolNoPermitido']    = (!empty($arrayRolesNoIncluidos)&&is_array($arrayRolesNoIncluidos))
                                                          ? $arrayRolesNoIncluidos:"";

            $arrayResultado = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                          ->getSolicitudDistribuidor($arrayParametros);

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
                    $strUsuarioVend   = "";
                    $objInfoPersona   = $emComercial->getRepository("schemaBundle:InfoPersona")
                                                    ->findOneByLogin($arrayDatos["VENDEDOR"]);
                    if(!empty($objInfoPersona) && is_object($objInfoPersona))
                    {
                        $strUsuarioVend = ucwords(strtolower($objInfoPersona->getNombres()." ".$objInfoPersona->getApellidos()));
                    }
                    $strUsuarioCreador = "";
                    $objInfoPersona = $emComercial->getRepository("schemaBundle:InfoPersona")
                                                  ->findOneByLogin($arrayDatos["USR_CREACION"]);
                    if(!empty($objInfoPersona) && is_object($objInfoPersona))
                    {
                        $strUsuarioCreador = ucwords(strtolower($objInfoPersona->getNombres()." ".$objInfoPersona->getApellidos()));
                    }
                    $arrayDataLink    = array('intIdSolicitud'  => $arrayDatos["ID_DETALLE_SOLICITUD"],
                                              'strEstado'       => $arrayDatos["ESTADO"]);
                    $strLinkVer       = array('linkVer'=> $this->generateUrl('show',$arrayDataLink));
                    $arraySolicitud[] = array('intIdSolicitud'    => $arrayDatos["ID_DETALLE_SOLICITUD"],
                                              'strCliente'        => ucwords(strtolower($arrayDatos["RAZON_SOCIAL"])),
                                              'strVendedor'       => $strUsuarioVend,
                                              'strIdentificacion' => $arrayDatos["IDENTIFICACION"],
                                              'strEstado'         => $arrayDatos["ESTADO"],
                                              'strObservacion'    => $arrayDatos["OBSERVACION"],
                                              'strFeCreacion'     => $arrayDatos["FE_CREACION"],
                                              'strUsrCreacion'    => $strUsuarioCreador,
                                              'strAcciones'       => $strLinkVer);
                }
            }
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError('TelcoS+', 'SolicitudDistribuidorController.gridAction', $e->getMessage(), $strUsrCreacion, $strIpCreacion);
        }
        $objResponse = new Response(json_encode(array('intTotal' => $intTotal, 'data' => $arraySolicitud)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

   /**
     * @Secure(roles="ROLE_460-6")
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
     * @version 1.0 11-05-2021
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
            $arraySolicitud                            = array();
            $arrayParametros                           = array();
            $arrayParametros['intIdSolicitud']         = $intIdSolicitud;
            $arrayParametros['strEstado']              = $strEstado;
            $arrayParametros['strTipoSolicitud']       = $this->strTipoSolicitud;
            $arrayParametros['strCaracIdentificacion'] = $this->strCaracIdentificacion;
            $arrayParametros['strCaracRazonSocial']    = $this->strCaracRazonSocial;
            $arrayParametros['strCaracVendedor']       = $this->strCaracVendedor;
            $arrayParametros['strCaracProducto']       = $this->strCaracProducto;
            $arrayParametros['strPrefijoEmpresa']      = $strPrefijoEmpresa;
            $arrayResultado                            = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                     ->getSolicitudDistribuidor($arrayParametros);
            if(isset($arrayResultado['error']) && !empty($arrayResultado['error']))
            {
                throw new \Exception($arrayResultado['error']);
            }
            if(!empty($arrayResultado["registros"])&&isset($arrayResultado["registros"]))
            {
                $arrayRegistros = $arrayResultado['registros'];
                foreach($arrayRegistros as $arrayDatos)
                {
                    if(!empty($arrayDatos["PRODUCTOS"]) && isset($arrayDatos["PRODUCTOS"]))
                    {
                        $arrayProductos = explode("|",$arrayDatos["PRODUCTOS"]);
                        if(!empty($arrayProductos) && is_array($arrayProductos))
                        {
                            foreach($arrayProductos as $arrayItem)
                            {
                                $objProducto         = $emComercial->getRepository('schemaBundle:AdmiProducto')->find($arrayItem);
                                $arrayDetProductos[] = array('strIdProducto'     => $objProducto->getId(),
                                                             'strDescProducto'   => $objProducto->getDescripcionProducto(),
                                                             'strGrupo'          => $objProducto->getGrupo(),
                                                             'strSubGrupo'       => $objProducto->getSubgrupo(),
                                                             'strLineaNegocio'   => $objProducto->getLineaNegocio());
                            }
                        }
                    }
                    $strUsuarioVend = "";
                    $objInfoPersona = $emComercial->getRepository("schemaBundle:InfoPersona")
                                                  ->findOneByLogin($arrayDatos["VENDEDOR"]);
                    if(!empty($objInfoPersona) && is_object($objInfoPersona))
                    {
                        $strUsuarioVend = ucwords(strtolower($objInfoPersona->getNombres()." ".$objInfoPersona->getApellidos()));
                    }
                    $strUsuarioCreador = "";
                    $objInfoPersona = $emComercial->getRepository("schemaBundle:InfoPersona")
                                                  ->findOneByLogin($arrayDatos["USR_CREACION"]);
                    if(!empty($objInfoPersona) && is_object($objInfoPersona))
                    {
                        $strUsuarioCreador = ucwords(strtolower($objInfoPersona->getNombres()." ".$objInfoPersona->getApellidos()));
                    }
                    $arraySolicitud = array('intIdSolicitud'    => $arrayDatos["ID_DETALLE_SOLICITUD"],
                                            'strCliente'        => ucwords(strtolower($arrayDatos["RAZON_SOCIAL"])),
                                            'strVendedor'       => $strUsuarioVend,
                                            'strIdentificacion' => $arrayDatos["IDENTIFICACION"],
                                            'strEstado'         => $arrayDatos["ESTADO"],
                                            'strObservacion'    => $arrayDatos["OBSERVACION"],
                                            'strFeCreacion'     => $arrayDatos["FE_CREACION"],
                                            'strUsrCreacion'    => $strUsuarioCreador,
                                            'arrayDetProductos' => $arrayDetProductos);
                }
            }
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError('TelcoS+', 'SolicitudDistribuidorController.showAction', $e->getMessage(), $strUsrCreacion, $strIpCreacion);
        }
        return $this->render('comercialBundle:SolicitudDistribuidor:show.html.twig',array('arraySolicitudDet' => $arraySolicitud));
    }

   /**
     *
     * @Secure(roles="ROLE_460-163")
     *
     * Documentación para la función 'getAprobarRechazarAction'.
     *
     * Función que aprueba o rechaza las solicitudes.
     *
     * @return $strResponse - Respuesta de confirmación.
     * 
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 11-05-2021
     */
    public function getAprobarRechazarAction()
    {
        $objRequest              = $this->getRequest();
        $objSession              = $objRequest->getSession();
        $strObservacionAprobar   = $objRequest->get("strObservacionAprobar")   ? $objRequest->get("strObservacionAprobar"):"";
        $strObservacionRechazar  = $objRequest->get("strObservacionRechazar")  ? $objRequest->get("strObservacionRechazar"):"";
        $strAccion               = $objRequest->get("strAccion")               ? $objRequest->get("strAccion"):"";
        $arraySolicitudes        = $objRequest->get("arraySolicitudes")        ? $objRequest->get("arraySolicitudes"):"";
        $strPrefijoEmpresa       = $objSession->get('prefijoEmpresa')          ? $objSession->get('prefijoEmpresa'):"";
        $intIdEmpresa            = $objSession->get('idEmpresa')               ? $objSession->get('idEmpresa'):"";
        $strUsrCreacion          = $objSession->get('user')                    ? $objSession->get('user'):"";
        $strIpCreacion           = $objRequest->getClientIp()                  ? $objRequest->getClientIp():'127.0.0.1';
        $emComercial             = $this->get('doctrine')->getManager('telconet');
        $serviceEnvioPlantilla   = $this->get('soporte.EnvioPlantilla');
        $serviceUtil             = $this->get('schema.Util');
        $strEstadoSol            = "";
        try
        {
            $arrayListaUsuarios = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                              ->get('PARAMETROS_SOLICITUD_DISTRIBUIDOR', 
                                                    'COMERCIAL', 
                                                    '', 
                                                    'LISTA_USUARIO_APROBACION', 
                                                    $strUsrCreacion,
                                                    $strRegionSesion,
                                                    '',
                                                    '',
                                                    '',
                                                    $intIdEmpresa);
            if(!empty($arrayListaUsuarios) && is_array($arrayListaUsuarios))
            {
                $boolIgnorarCargo = true;
                $strEstado        = (!empty($strEstadoFiltro) && $strEstadoFiltro != "") 
                                     ? $strEstadoFiltro: $arrayListaUsuarios[0]["valor3"];
                $strTipoPersonal  = $arrayListaUsuarios[0]["valor4"];
            }
            $arrayResultadoCaracteristicas = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                         ->getCargosPersonas($strUsrCreacion);
            if(!empty($arrayResultadoCaracteristicas) && !$boolIgnorarCargo)
            {
                $arrayResultadoCaracteristicas = $arrayResultadoCaracteristicas[0];
                $strTipoPersonal               = $arrayResultadoCaracteristicas['STRCARGOPERSONAL'] ? 
                                                 $arrayResultadoCaracteristicas['STRCARGOPERSONAL'] : 'Otros';
            }
            if(!empty($strTipoPersonal) && $strTipoPersonal == 'SUBGERENTE')
            {
                $strEstadoSol = "Pendiente Gerente";
            }
            else if(!empty($strTipoPersonal) && $strTipoPersonal == 'GERENTE_VENTAS')
            {
                $strEstadoSol = "Aprobada";
            }
            else
            {
                throw new \Exception("Persona en sesión no tiene el cargo para poder ".$strAccion." la solicitud.");
            }
            if(empty($strEstadoSol))
            {
                throw new \Exception("No se encontró estado para la solicitud.");
            }
            if( empty($arraySolicitudes) || !is_array($arraySolicitudes) )
            {
                throw new \Exception('El listado de solicitudes es un campo obligatorio.');
            }
            $arrayDestinatarios                        = array();
            $arrayParametros                           = array();
            $arrayParametros['strTipoSolicitud']       = $this->strTipoSolicitud;
            $arrayParametros['strCaracIdentificacion'] = $this->strCaracIdentificacion;
            $arrayParametros['strCaracRazonSocial']    = $this->strCaracRazonSocial;
            $arrayParametros['strCaracVendedor']       = $this->strCaracVendedor;
            $arrayParametros['strCaracProducto']       = $this->strCaracProducto;
            $arrayParametros['strPrefijoEmpresa']      = $strPrefijoEmpresa;
            $emComercial->getConnection()->beginTransaction();
            $arrayParametros['intIdSolicitud']  = $arraySolicitudes;
            $arrayResultado                     = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                              ->getSolicitudDistribuidor($arrayParametros);
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
                if($strTipoPersonal == 'SUBGERENTE')
                {
                    $strMensajeCorreo = "aprobó la solicitud por parte del subgerente";
                }
                if($strTipoPersonal == 'GERENTE_VENTAS')
                {
                    $strMensajeCorreo = "aprobó la solicitud por parte del gerente";
                }
                foreach($arrayResultado['registros'] as $arrayDatos)
                {
                    $arrayParametrosVendedor = array("strLogin"=>$arrayDatos["VENDEDOR"]);
                    $arrayResultadoCorreo    = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                           ->getSubgerentePorLoginVendedor($arrayParametrosVendedor);

                    if(!empty($arrayResultadoCorreo["registros"]) && isset($arrayResultadoCorreo["registros"]))
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
                    }
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
                                                ->getContactosByLoginPersonaAndFormaContacto($arrayDatos["VENDEDOR"],
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
                    $arrayParametrosMail  = array("strMensajeCorreo"         => $strMensajeCorreo,
                                                  "strNombreCliente"         => $arrayDatos["RAZON_SOCIAL"],
                                                  "strIdentificacionCliente" => $arrayDatos["IDENTIFICACION"],
                                                  "strObservacion"           => $strObservacionAprobar);
                    $serviceEnvioPlantilla->generarEnvioPlantilla("SOLICITUD DE DISTRIBUIDOR", 
                                                                  array_unique($arrayDestinatarios), 
                                                                  'DISTRIBUIDOR',
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
                    $entityDetalleSolHist->setObservacion($strObservacion);
                    $entityDetalleSolHist->setIpCreacion($strIpCreacion);
                    $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
                    $entityDetalleSolHist->setUsrCreacion($strUsrCreacion);
                    $entityDetalleSolHist->setEstado($strEstadoSol);
                    $emComercial->persist($entityDetalleSolHist);
                    $emComercial->flush();
                }
            }
            else if($strAccion=="rechazar")
            {
                if($strTipoPersonal == 'SUBGERENTE')
                {
                    $strMensajeCorreo = "rechazó la solicitud por parte del subgerente";
                }
                if($strTipoPersonal == 'GERENTE_VENTAS')
                {
                    $strMensajeCorreo = "rechazó la solicitud por parte del gerente";
                }
                $strEstadoSol = "Rechazado";
                foreach($arrayResultado['registros'] as $arrayDatos)
                {
                    $arrayParametrosVendedor = array("strLogin"=>$arrayDatos["VENDEDOR"]);
                    $arrayResultadoCorreo    = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                           ->getSubgerentePorLoginVendedor($arrayParametrosVendedor);

                    if(!empty($arrayResultadoCorreo["registros"]) && isset($arrayResultadoCorreo["registros"]))
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
                    }
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
                                                ->getContactosByLoginPersonaAndFormaContacto($arrayDatos["VENDEDOR"],
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
                    $arrayParametrosMail  = array("strMensajeCorreo"         => $strMensajeCorreo,
                                                  "strNombreCliente"         => $arrayDatos["RAZON_SOCIAL"],
                                                  "strIdentificacionCliente" => $arrayDatos["IDENTIFICACION"],
                                                  "strObservacion"           => $strObservacionRechazar);
                    $serviceEnvioPlantilla->generarEnvioPlantilla("SOLICITUD DE DISTRIBUIDOR", 
                                                                  array_unique($arrayDestinatarios), 
                                                                  'DISTRIBUIDOR',
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
                    $emComercial->persist($entityDetalleSolicitud);
                    $emComercial->flush();

                    $entityDetalleSolHist = new InfoDetalleSolHist();
                    $entityDetalleSolHist->setDetalleSolicitudId($entityDetalleSolicitud);
                    $entityDetalleSolHist->setObservacion($strObservacionRechazar);
                    $entityDetalleSolHist->setIpCreacion($strIpCreacion);
                    $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
                    $entityDetalleSolHist->setUsrCreacion($strUsrCreacion);
                    $entityDetalleSolHist->setEstado($strEstadoSol);
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
                                      'SolicitudDistribuidorController.getAprobarRechazarAction', 
                                      $strResponse . $e->getMessage(), 
                                      $strUsrCreacion, 
                                      $strIpCreacion);
        }
        return new Response($strResponse);
    }

   /**
     * Documentación para la función 'getVendedorAction'.
     *
     * Función que obtiene los vendedores de acuerdo a la persona en sesión.
     *
     * @return Response - Lista de vendedor.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 11-05-2021
     *
     */
    public function getVendedorAction()
    {
        try
        {
            $objRequest             = $this->getRequest();
            $objSession             = $objRequest->getSession();
            $strUsrCreacion         = $objSession->get('user')   ? $objSession->get('user'):"";
            $strIpCreacion          = $objRequest->getClientIp() ? $objRequest->getClientIp():'127.0.0.1';
            $emComercial            = $this->get('doctrine')->getManager('telconet');
            $serviceUtil            = $this->get('schema.Util');
            $serviceUtilidades      = $this->get('administracion.Utilidades');
            $strPrefijoEmpresa      = $objSession->get('prefijoEmpresa');
            $strCodEmpresa          = $objSession->get('idEmpresa');
            $intIdPersonaEmpresaRol = $objSession->get('idPersonaEmpresaRol');
            /**
             * BLOQUE QUE VALIDA LA CARACTERISTICA 'CARGO_GRUPO_ROLES_PERSONAL','ASISTENTE_POR_CARGO' ASOCIADA A EL USUARIO LOGONEADO 
             */
            $arrayResultadoCaracteristicas = $emComercial->getRepository('schemaBundle:InfoPersona')->getCargosPersonas($strUsrCreacion);
            if(!empty($arrayResultadoCaracteristicas))
            {
                $arrayResultadoCaracteristicas = $arrayResultadoCaracteristicas[0];
                $strTipoPersonal               = $arrayResultadoCaracteristicas['STRCARGOPERSONAL'] 
                                                 ? $arrayResultadoCaracteristicas['STRCARGOPERSONAL'] : 'Otros';
            }
            $arrayParametros                        = array();
            $arrayParametros['usuario']             = $intIdPersonaEmpresaRol;
            $arrayParametros['empresa']             = $strCodEmpresa;
            $arrayParametros['estadoActivo']        = 'Activo';
            $arrayParametros['caracteristicaCargo'] = 'CARGO_GRUPO_ROLES_PERSONAL';
            $arrayParametros['nombreArea']          = 'Comercial';
            $arrayParametros['strTipoRol']          = array('Empleado', 'Personal Externo');

            /**
             * BLOQUE QUE BUSCA LOS ROLES NO PERMITIDOS PARA LA BUSQUEDA DEL PERSONAL
             */
            $arrayRolesNoIncluidos = array();
            $arrayParametrosRoles  = array( 'strCodEmpresa'     => $strCodEmpresa,
                                            'strValorRetornar'  => 'descripcion',
                                            'strNombreProceso'  => 'JEFES',
                                            'strNombreModulo'   => 'COMERCIAL',
                                            'strNombreCabecera' => 'ROLES_NO_PERMITIDOS',
                                            'strUsrCreacion'    => $strUsrCreacion,
                                            'strIpCreacion'     => $strIpCreacion );

            $arrayResultadosRolesNoIncluidos = $serviceUtilidades->getDetallesParametrizables($arrayParametrosRoles);

            if(isset($arrayResultadosRolesNoIncluidos['resultado']) && !empty($arrayResultadosRolesNoIncluidos['resultado']))
            {
                foreach($arrayResultadosRolesNoIncluidos['resultado'] as $strRolNoIncluido)
                {
                    $arrayRolesNoIncluidos[] = $strRolNoIncluido;
                }
                $arrayParametros['rolesNoIncluidos'] = $arrayRolesNoIncluidos;
            }

            /**
             * BLOQUE QUE BUSCA LOS ROLES PERMITIDOS PARA LA BUSQUEDA DEL PERSONAL
             */
            $arrayRolesIncluidos                       = array();
            $arrayParametrosRoles['strNombreCabecera'] = 'ROLES_PERMITIDOS';

            $arrayResultadosRolesIncluidos = $serviceUtilidades->getDetallesParametrizables($arrayParametrosRoles);

            if(isset($arrayResultadosRolesIncluidos['resultado']) && !empty($arrayResultadosRolesIncluidos['resultado']))
            {
                foreach($arrayResultadosRolesIncluidos['resultado'] as $strRolIncluido)
                {
                    $arrayRolesIncluidos[] = $strRolIncluido;
                }
                $arrayParametros['strTipoRol'] = $arrayRolesIncluidos;
            }

            /**
             * SE VALIDA QUE SE CONSIDEREN LOS DEPARTAMENTOS COMERCIALES AGRUPADOS EN EL PARAMETRO 'GRUPO_DEPARTAMENTOS'
             */
            $arrayParametrosDepartamentos = array('strCodEmpresa'     => $strCodEmpresa,
                                                  'strValorRetornar'  => 'valor1',
                                                  'strNombreProceso'  => 'ADMINISTRACION_JEFES',
                                                  'strNombreModulo'   => 'COMERCIAL',
                                                  'strNombreCabecera' => 'GRUPO_DEPARTAMENTOS',
                                                  'strValor2Detalle'  => 'COMERCIAL',
                                                  'strUsrCreacion'    => $strUsrCreacion,
                                                  'strIpCreacion'     => $strIpCreacion);

            $arrayResultadosDepartamentos = $serviceUtilidades->getDetallesParametrizables($arrayParametrosDepartamentos);

            if(isset($arrayResultadosDepartamentos['resultado']) && !empty($arrayResultadosDepartamentos['resultado']))
            {
                $arrayParametros['departamento'] = $arrayResultadosDepartamentos['resultado'];
            }

            /**
             * SE OBTIENE EL CARGO DE VENDEDOR DEL PARAMETRO 'GRUPO_ROLES_PERSONAL'
             */
            $arrayParametrosCargoVendedor = array('strCodEmpresa'     => $strCodEmpresa,
                                                  'strValorRetornar'  => 'id',
                                                  'strNombreProceso'  => 'ADMINISTRACION_JEFES',
                                                  'strNombreModulo'   => 'COMERCIAL',
                                                  'strNombreCabecera' => 'GRUPO_ROLES_PERSONAL',
                                                  'strValor3Detalle'  => 'VENDEDOR',
                                                  'strUsrCreacion'    => $strUsrCreacion,
                                                  'strIpCreacion'     => $strIpCreacion);

            $arrayResultadosCargoVendedor = $serviceUtilidades->getDetallesParametrizables($arrayParametrosCargoVendedor);

            if(isset($arrayResultadosCargoVendedor['resultado']) && !empty($arrayResultadosCargoVendedor['resultado']))
            {
                foreach($arrayResultadosCargoVendedor['resultado'] as $intIdCargoVendedor)
                {
                    $arrayParametros['criterios']['cargo'] = $intIdCargoVendedor;
                }
            }
            $arrayParametros['strPrefijoEmpresa']       = $strPrefijoEmpresa;
            $arrayParametros['strTipoPersonal']         = $strTipoPersonal;
            $arrayParametros['intIdPersonEmpresaRol']   = $intIdPersonaEmpresaRol;
            $arrayParametros['strFiltrarTodosEstados']  = $strFiltrarTodosEstados;

            $arrayPersonalVendedor = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                 ->findPersonalByCriterios($arrayParametros);

            if(isset($arrayPersonalVendedor['registros']) && !empty($arrayPersonalVendedor['registros'])
                && isset($arrayPersonalVendedor['total']) && $arrayPersonalVendedor['total'] > 0)
            {
                foreach($arrayPersonalVendedor['registros'] as $arrayVendedor)
                {
                    $strNombreVendedor      = ( isset($arrayVendedor['nombres']) && !empty($arrayVendedor['nombres']) )
                        ? ucwords(strtolower($arrayVendedor['nombres'])).' ' : '';
                    $strNombreVendedor      .= ( isset($arrayVendedor['apellidos']) && !empty($arrayVendedor['apellidos']) )
                        ? ucwords(strtolower($arrayVendedor['apellidos'])) : '';
                    $strLoginVendedor       = ( isset($arrayVendedor['login']) && !empty($arrayVendedor['login']) )
                        ? $arrayVendedor['login'] : '';
                    $intIdPersona           = ( isset($arrayVendedor['id']) && !empty($arrayVendedor['id']) )
                        ? $arrayVendedor['id'] : 0;
                    $intIdPersonaEmpresaRol = ( isset($arrayVendedor['idPersonaEmpresaRol']) && !empty($arrayVendedor['idPersonaEmpresaRol']) )
                        ? $arrayVendedor['idPersonaEmpresaRol'] : 0;

                    $arrayItemVendedor                           = array();
                    $arrayItemVendedor['nombre']                 = $strNombreVendedor;
                    $arrayItemVendedor['login']                  = $strLoginVendedor;
                    $arrayItemVendedor['intIdPersona']           = $intIdPersona;
                    $arrayItemVendedor['intIdPersonaEmpresaRol'] = $intIdPersonaEmpresaRol;
                    $arrayVendedores[]                           = $arrayItemVendedor;
                }
            }
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError('TelcoS+', 
                                      'SolicitudDistribuidorController.getVendedorAction', 
                                      $e->getMessage(),
                                      $strUsrCreacion,
                                      $strIpCreacion);
        }
        $objResponse = new Response(json_encode(array('arrayVendedores' => $arrayVendedores)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

   /**
     *
     * Documentación para la función 'getProductosAction'.
     *
     * Función que retorna el listado de productos.
     *
     * @return $objResponse - Listado de productos.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 27-05-2021
     * 
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 06-09-2021 - Se actualiza el llamado de la función para listar los productos.
     *
     */
    public function getProductosAction()
    {
        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $arrayLineaNegocio      = $objRequest->get("arrayLineaNegocio")   ? $objRequest->get("arrayLineaNegocio"):"";
        $intIdEmpresa           = $objSession->get('idEmpresa')           ? $objSession->get('idEmpresa'):"";
        $strUsrCreacion         = $objSession->get('user')                ? $objSession->get('user'):"";
        $strIpCreacion          = $objRequest->getClientIp()              ? $objRequest->getClientIp():'127.0.0.1';
        $serviceUtil            = $this->get('schema.Util');
        $serviceInfoServicio    = $this->get('comercial.InfoServicio');
        $strModulo              = "Comercial";
        $arrayProductos         = array();
        try
        {
            if(!empty($arrayLineaNegocio) && is_array($arrayLineaNegocio))
            {
                $arrayListadoProductos = $serviceInfoServicio->obtenerProductos(array("strCodEmpresa" => $intIdEmpresa,
                                                                                      "strModulo"     => $strModulo));
                if(!empty($arrayListadoProductos) && is_array($arrayListadoProductos))
                {
                    foreach($arrayLineaNegocio as $arrayItemLineaNegocio)
                    {
                        foreach($arrayListadoProductos as $arrayItem)
                        {
                            if(strtoupper($arrayItem->getLineaNegocio()) == strtoupper($arrayItemLineaNegocio))
                            {
                                $arrayItemProducto                         = array();
                                $arrayItemProducto['id']                   = $arrayItem->getId();
                                $arrayItemProducto['descripcionProducto']  = $arrayItem->getDescripcionProducto();
                                $arrayProductos[]                          = $arrayItemProducto;
                            }
                        }
                    }
                }
            }
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError('TelcoS+',
                                      'SolicitudDistribuidorController.gridAction',
                                      $e->getMessage(),
                                      $strUsrCreacion,
                                      $strIpCreacion);
        }
        $objResponse = new Response(json_encode(array('arrayProductos' => $arrayProductos)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

   /**
     *
     * Documentación para la función 'getLineaNegocioAction'.
     *
     * Función que retorna el listado de las líneas de negocio.
     *
     * @return $objResponse - Listado de líneas de negocio.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 27-05-2021
     *
     */
    public function getLineaNegocioAction()
    {
        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $intIdEmpresa           = $objSession->get('idEmpresa')           ? $objSession->get('idEmpresa'):"";
        $strUsrCreacion         = $objSession->get('user')                ? $objSession->get('user'):"";
        $strIpCreacion          = $objRequest->getClientIp()              ? $objRequest->getClientIp():'127.0.0.1';
        $emGeneral              = $this->get('doctrine')->getManager('telconet');
        $serviceUtil            = $this->get('schema.Util');
        $arrayLineaNegocio      = array();
        try
        {
            $arrayParamLineaNegocio = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->get('LINEA_NEGOCIO', 
                                                      'COMERCIAL', 
                                                      '', 
                                                      '', 
                                                      '', 
                                                      '', 
                                                      '', 
                                                      '', 
                                                      '', 
                                                      $intIdEmpresa);
            foreach($arrayParamLineaNegocio as $arrayItem)
            {
                $arrayLineaNegocio[] = array('id'           => $arrayItem['valor1'],
                                             'lineaNegocio' => $arrayItem['valor1']);
            }
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError('TelcoS+',
                                      'SolicitudDistribuidorController.getLineaNegocioAction',
                                      $e->getMessage(),
                                      $strUsrCreacion,
                                      $strIpCreacion);
        }
        $objResponse = new Response(json_encode(array('arrayLineaNegocio' => $arrayLineaNegocio)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
}
