<?php

namespace telconet\comercialBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoDetalleSolCaract;
use telconet\schemaBundle\Entity\InfoProcesoMasivoCab;
use telconet\schemaBundle\Entity\InfoProcesoMasivoDet;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoServicioProdCaract;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\SecurityExtraBundle\Annotation\Secure;
use telconet\schemaBundle\Entity\ReturnResponse;
use telconet\soporteBundle\Service\EnvioPlantillaService;
use telconet\schemaBundle\Entity\AdmiCanton;
use telconet\schemaBundle\Entity\InfoPersona;
use \PHPExcel_IOFactory;
use \PHPExcel_Worksheet_PageSetup;
use \PHPExcel_CachedObjectStorageFactory;
use \PHPExcel_Settings;
use \PHPExcel_Style_Alignment;
use \PHPExcel;
use \PHPExcel_Style_Fill;
use \PHPExcel_Style_Border;
use \PHPExcel_Worksheet_MemoryDrawing;
use \PHPExcel_Shared_Date;
use \PHPExcel_Style_NumberFormat;
use \PHPExcel_Cell_DataType;


/**
 * Documentación para el controlador 'SolicitudesMasivasController'.
 * SolicitudesMasivasController, Contiene los metodos para el manejo de las solicitudes masivas de cambio de plan y cancelaciones
 * @author Robinson Salgado <rsalgado@telconet.ec>
 * @version 1.0 24-03-2016
 */
class SolicitudesMasivasController extends Controller
{
    
    //Porcentaje para calcular el Umbral del Nivel de Aprobacion para IPCCL2
    const PORC_UMBRAL = 0.8;
    const RANGO_APROBACION_SOLICITUDES      = 'RANGO_APROBACION_SOLICITUDES';
    const ADMINISTRACION_CARGOS_SOLICITUDES = 'ADMINISTRACION_CARGOS_SOLICITUDES';
    const COMERCIAL                         = 'COMERCIAL';
    const CARGO_GRUPO_ROLES_PERSONAL        = 'CARGO_GRUPO_ROLES_PERSONAL';
    const GRUPO_ROLES_PERSONAL              = 'GRUPO_ROLES_PERSONAL';
    const GERENTE_VENTAS                    = 'GERENTE_VENTAS';
    const ROLES_NO_PERMITIDOS               = 'ROLES_NO_PERMITIDOS'; 
    
    /**
     * indexAction, Redirecciona al index de la administración de solicitudes masivas.
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 24-03-2016
     * @return redireccion al index de la administración de SolicitudesMasivas
     *
     * @Secure(roles="ROLE_346-1")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $session = $request->getSession();        
        $puntoIdSesion = null;
        $ptoCliente_sesion = $session->get('ptoCliente');
        if($ptoCliente_sesion)
        {
            $puntoIdSesion = $ptoCliente_sesion['id'];
        }
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("346", "1");
        $session->set('id_menu_activo', $entityItemMenu->getId());
        $entityItemMenuPadre = $entityItemMenu->getItemMenuId();
        $session->set('menu_modulo_activo', $entityItemMenuPadre->getNombreItemMenu());
        $session->set('nombre_menu_modulo_activo', $entityItemMenuPadre->getTitleHtml());
        $session->set('id_menu_modulo_activo', $entityItemMenuPadre->getId());
        $session->set('imagen_menu_modulo_activo', $entityItemMenuPadre->getUrlImagen());

        //Modulo admiParametroCab : [346] Se debe cambiar el Id del modulo antes de pasar a producción
        if(true === $this->get('security.context')->isGranted('ROLE_346-1'))
        {
            $arrayRolesPermitidos[] = 'ROLE_346-1'; //Rol Index
        }
        if(true === $this->get('security.context')->isGranted('ROLE_346-2'))
        {
            $arrayRolesPermitidos[] = 'ROLE_346-2'; //Rol New
        }
        if(true === $this->get('security.context')->isGranted('ROLE_346-3'))
        {
            $arrayRolesPermitidos[] = 'ROLE_346-3'; //Rol Create
        }
        if(true === $this->get('security.context')->isGranted('ROLE_346-4'))
        {
            $arrayRolesPermitidos[] = 'ROLE_346-4'; //Rol Edit
        }
        if(true === $this->get('security.context')->isGranted('ROLE_346-5'))
        {
            $arrayRolesPermitidos[] = 'ROLE_346-5'; //Rol Update
        }
        if(true === $this->get('security.context')->isGranted('ROLE_346-6'))
        {
            $arrayRolesPermitidos[] = 'ROLE_346-6'; //Rol Show
        }
        if(true === $this->get('security.context')->isGranted('ROLE_346-8'))
        {
            $arrayRolesPermitidos[] = 'ROLE_346-8'; //Rol Delete
        }
        if(true === $this->get('security.context')->isGranted('ROLE_346-9'))
        {
            $arrayRolesPermitidos[] = 'ROLE_346-9'; //Rol Delete Ajax
        }
        return $this->render('comercialBundle:solicitudesmasivas:index.html.twig', array('item' => $entityItemMenu,
                'rolesPermitidos' => $arrayRolesPermitidos));
    }

    /**
     * newAction, Crea formulario para las creacion de solicitudes masivas
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 28-03-2016
     * @since 1.0
     * 
     * @Secure(roles="ROLE_346-2")
     */
    public function newAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        $cliente_sesion = $session->get('cliente');
        if($cliente_sesion)
        {
            $clienteIdSesion = $cliente_sesion['id'];
        }
        $arrayParametros = array('clienteId' => $clienteIdSesion);

        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $seguRelacionSistema = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema');

        $entitySeguRelacionSistema = $seguRelacionSistema->findBy(array('moduloId' => '346', 'accionId' => '2'));
        $session->set('id_relacion_sistema', $entitySeguRelacionSistema[0]->getId());

        return $this->render('comercialBundle:solicitudesmasivas:new.html.twig', $arrayParametros);
    }

    /**
     * showAction, Muestra los Datos de una solicitud masiva
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 28-03-2016
     * @since 1.0
     * 
     */
    public function showAction($id)
    {
        $peticion = $this->getRequest();
        $emComercial = $this->getDoctrine()->getManager("telconet");
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $seguRelacionSistema = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema');

        $entityItemMenu = $seguRelacionSistema->searchItemMenuByModulo("346", "6");
        $intIdEmpresa = ($peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa') : "");

        $infoDetalleSolicitudCabArray = $this->obtenerSolicitudAPresentar($emComercial, $intIdEmpresa, $id);

        return $this->render('comercialBundle:solicitudesmasivas:show.html.twig', array(
                'item' => $entityItemMenu,
                'infoDetalleSolicitudCab' => $infoDetalleSolicitudCabArray,
                'flag' => $peticion->get('flag')
        ));
    }

    /**
     * obtenerSolicitudAPresentar, Obtiene los valores a presentar de la cabecera en la vista
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 28-03-2016
     * @since 1.0
     * 
     * @author Modificado: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.1 05-08-2016 - Añadio la consulta para Producto Obsoleto
     * 
     */
    private function obtenerSolicitudAPresentar($em, $intIdEmpresa, $id)
    {
        $infoDetalleSolicitudCabArray = array();
        $arrayParametros              = array();

        $infoDetalleSolicitud = $em->getRepository('schemaBundle:InfoDetalleSolicitud');
        $infoDetalleSolCaract = $em->getRepository('schemaBundle:InfoDetalleSolCaract');
        $admiCaracteristica   = $em->getRepository('schemaBundle:AdmiCaracteristica');
        $admiProducto         = $em->getRepository('schemaBundle:AdmiProducto');
        $infoDetalleSolHist   = $em->getRepository('schemaBundle:InfoDetalleSolHist');

        $arrayParametros['intIdEmpresa']        = $intIdEmpresa;
        $arrayParametros['strCodigoSolicitud']  = $id;
        $arrayParametros['boolMasivas']         = 'true';
        $resultado = $infoDetalleSolicitud->findSolicitudes($arrayParametros);

        if(null == $resultado)
        {
            throw new NotFoundHttpException('No existe la Solicitud Masiva que se quiere mostrar');
        }

        $infoDetalleSolicitudCab          = $resultado['registros'][0];
        $infoDetalleSolicitudCabHistorial = $infoDetalleSolHist->findOneBy(array(
                                                                                "detalleSolicitudId" => $infoDetalleSolicitudCab['idDetalleSolicitud'],
                                                                                "estado"             => "Pendiente"
                                                                                ));

        $fechaPlanificacion = "";
        if($infoDetalleSolicitudCabHistorial->getFeFinPlan() != null)
        {
            $fechaPlanificacion = $infoDetalleSolicitudCabHistorial->getFeFinPlan()->format('Y-m-d H:i');
        }

        // Se buscan todas las caracteristicas de la solicitud masiva  
        $infoDetalleSolicitudCaractList = $infoDetalleSolCaract->findBy(array("detalleSolicitudId" => $infoDetalleSolicitudCab['idDetalleSolicitud']));
        $infoDetalleSolicitudCabArray['caracteristicas'] = array();
        for($intIndex = 0; $intIndex < count($infoDetalleSolicitudCaractList); $intIndex++)
        {
            $caracteristica = $admiCaracteristica->find($infoDetalleSolicitudCaractList[$intIndex]->getCaracteristicaId());
            if($caracteristica)
            {
                $descripcion    = $caracteristica->getDescripcionCaracteristica();
                $valor          = $infoDetalleSolicitudCaractList[$intIndex]->getValor();
                if(!empty($valor))
                {
                    if($descripcion == 'Producto Obsoleto')
                    {
                        $productoEntity = $admiProducto->find($valor);
                        if($productoEntity)
                        {
                            $infoDetalleSolicitudCabArray['caracteristicas']['Producto Cambio'] = $productoEntity->getDescripcionProducto();
                        }
                    }
                    else
                    {
                        $infoDetalleSolicitudCabArray['caracteristicas'][$descripcion] = $valor;
                    }
                }
            }
        }
        // Se busca un elemento de la solicitud masiva para obtener de el campos como Cliente y Producto
        $caracteristica                 = $admiCaracteristica->findOneBy(array("descripcionCaracteristica" => "Referencia Solicitud"));
        $infoDetalleSolicitudCaractList = $infoDetalleSolCaract->findOneBy(array(
                                                                                "valor"             => $infoDetalleSolicitudCab['idDetalleSolicitud'],
                                                                                "caracteristicaId"  => $caracteristica
                                                                                ));

        $intIdDetalle            = $infoDetalleSolicitudCaractList->getDetalleSolicitudId()->getId();
        $infoDetalleSolicitudDet = $infoDetalleSolicitud->find($intIdDetalle);

        $servicio = $infoDetalleSolicitudDet->getServicioId();
        $producto = $servicio->getProductoId();
        $punto    = $servicio->getPuntoId();
        $cliente  = $punto->getPersonaEmpresaRolId()->getPersonaId();

        $productoPlan = $producto->getDescripcionProducto();
        $strCliente   = $cliente->__tostring();

        $infoDetalleSolicitudCabArray['id']                 = $id;
        $infoDetalleSolicitudCabArray['cliente']            = $strCliente;
        $infoDetalleSolicitudCabArray['producto']           = $productoPlan;
        $infoDetalleSolicitudCabArray['tipoSolicitud']      = $infoDetalleSolicitudCab['descripcionSolicitud'];
        $infoDetalleSolicitudCabArray['descripcion']        = $infoDetalleSolicitudCab['observacion'];
        $infoDetalleSolicitudCabArray['fechaPlanificacion'] = $fechaPlanificacion;
        $infoDetalleSolicitudCabArray['motivo']             = $infoDetalleSolicitudCab['motivo'];
        $infoDetalleSolicitudCabArray['fechaCreacion']      = $infoDetalleSolicitudCab['feCreacion'];
        $infoDetalleSolicitudCabArray['usuarioCreacion']    = $infoDetalleSolicitudCab['usrCreacion'];
        $infoDetalleSolicitudCabArray['estado']             = $infoDetalleSolicitudCab['estado'];
        $infoDetalleSolicitudCabArray['colorEstado']        = $this->getColorEstado($infoDetalleSolicitudCab['estado']);
        $infoDetalleSolicitudCabArray['archivo']            = $infoDetalleSolicitudCab['archivo'];

        return $infoDetalleSolicitudCabArray;
    }

    /**
     * getColorEstado, Obtiene un color para los diferentes estados de la Solicitud Masiva
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 14-04-2016
     * @return string Retorna nombre de un color para aplicar un css
     */
    private function getColorEstado($estado)
    {
        $color = "blue";
        if($estado == 'Aprobada' || $estado == 'EnProceso')
        {
            $color = 'green';
        }
        else if($estado == 'Rechazada')
        {
            $color = 'red';
        }
        else if($estado == 'Eliminada')
        {
            $color = 'wine';
        }
        return $color;
    }

    /**
     * getSolicitudesMasivasAction, Obtiene listado las Solicitudes Masivas
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 26-03-2016
     * @return json Retorna un json del listado de las Solicitudes Masivas
     * 
     * @author Modificado: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.1 22-06-2016 - Se añadieron parametros especiales boolean y de estado para el filtrado de las solicitudes
     *                           para que una vez autorizadas por el area correspondiente no le vuelevan a salir.
     * 
     * @author Modificado: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.2 24-06-2016 - Se añadieron filtro de cliente por identificacion o razon social
     * 
     * @author Modificado: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.3 14-07-2016 - Se añadieron filtro de Oficina
     * 
     * @author Modificado: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.4 18-07-2016 - Se cambio la funcion de consulta para solicitudes masivas
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.5 24-05-2017 - Se agregan solicitudes de Demos
     * @author Modificado: Kevin Baque <kbaque@telconet.ec>
     * @version 1.6 22-11-2018 Se realiza cambio para quela consulta de Solicitudes Masivas se realice a través de la persona en sesion, solo para Telconet
     *                         en caso de ser asistente aparecerá las Solicitudes Masivas de los vendedores asignados al asistente
     *                         en caso de ser vendedor aparecerá sus Solicitudes Masivas
     *                         en caso de ser subgerente aparecerá las Solicitudes Masivas de los vendedores que reportan al subgerente
     *                         en caso de ser gerente aparecerá todos las Solicitudes Masivas
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.7 24-12-2021 - Se establece nueva lógica para visualizar las solicitudes de acuerdo al rango de aprobación y cargo,
     *                           los cambios solo aplican para Telconet.
     * 
     * @author David León <mdleon@telconet.ec>
     * @version 1.8 18-05-2022 - Se corrige código para poder mostrar las solicitudes en la ventana masiva de L2.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.9 19-09-2022 - Se establece nuevo filtro para retornar los clientes con el tipo de negocio ISP.
     *
     */
    public function getSolicitudesMasivasAction()
    {
        ini_set('max_execution_time', 60000);

        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');
        $arrayParametros = array();

        $objRequest   = $this->getRequest();
        $objSession    = $objRequest->getSession();

        $emComercial            = $this->getDoctrine()->getManager("telconet");
        $infoDetalleSolicitud   = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud');
        $strUsrCreacion         = $objSession->get('user');
        $strTipoPersonal        = 'Otros';
        $strPrefijoEmpresa      = $objSession->get('prefijoEmpresa');
        $strCodEmpresa          = $objSession->get('idEmpresa');
        $intIdPersonEmpresaRol  = $objSession->get('idPersonaEmpresaRol');
        $strIpCreacion          = $objRequest->getClientIp();
        $intIdCanton            = $objSession->get('intIdCanton') ? $objSession->get('intIdCanton') : "";
        $strIsp                 = $objRequest->query->get("cboIsp") ? $objRequest->query->get("cboIsp"):"No";
        $arraySolicitudes       = array();
        $arrayResultado         = array();
        $serviceUtilidades      = $this->get('administracion.Utilidades');
        $emComercial            = $this->getDoctrine()->getManager("telconet");
        $emGeneral              = $this->getDoctrine()->getManager('telconet_general');
        $strRegionSesion        = "";
        $floatDescPorAprobarIni = 0;
        $floatDescPorAprobarFin = 0;
        $arrayTPNoPermitido     = array("Otros","ASISTENTE","VENDEDOR");
        /**
         * BLOQUE QUE VALIDA LA CARACTERÍSTICA 'CARGO_GRUPO_ROLES_PERSONAL','ASISTENTE_POR_CARGO' ASOCIADA A EL USUARIO LOGONEADO 
         */
        if(!empty($strPrefijoEmpresa) && $strPrefijoEmpresa == "TN")
        {
            $arrayLoginVendedoresKam = array();
            $arrayRolesNoIncluidos   = array();
            $strCargosAdicionales    = ",'GERENTE_GENERAL_REGIONAL','GERENTE_GENERAL'";
            $objCargosCab            = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                 ->findOneBy(array('nombreParametro' => self::RANGO_APROBACION_SOLICITUDES,
                                                                   'modulo'          => self::COMERCIAL,
                                                                   'proceso'         => self::ADMINISTRACION_CARGOS_SOLICITUDES,
                                                                   'estado'          => 'Activo'));
            $arrayCargosDet    = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                           ->findBy(array('parametroId' => $objCargosCab->getId(),
                                                          'valor4'      => 'ES_JEFE',
                                                          'valor7'      => 'SI',
                                                          'estado'      => 'Activo'));
            $arrayCargoPersona = $emComercial->getRepository('schemaBundle:InfoPersona')
                                             ->getCargosPersonas($strUsrCreacion,$strCargosAdicionales);
            $strTipoPersonal   = (!empty($arrayCargoPersona) && is_array($arrayCargoPersona))
                                 ? $arrayCargoPersona[0]['STRCARGOPERSONAL']:'Otros';
            if((!empty($strTipoPersonal) && !in_array($strTipoPersonal,$arrayTPNoPermitido)) &&
               (is_object($objCargosCab) && !empty($objCargosCab)))
            {
                $objCargosDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->findOneBy(array('parametroId' => $objCargosCab->getId(),
                                                              'valor3'      => $strTipoPersonal,
                                                              'valor4'      => 'ES_JEFE',
                                                              'estado'      => 'Activo'));
                $floatDescPorAprobarIni  = (!empty($objCargosDet) && is_object($objCargosDet)) ? $objCargosDet->getValor1():'';
                $floatDescPorAprobarFin  = (!empty($objCargosDet) && is_object($objCargosDet)) ? $objCargosDet->getValor2():'';
            }
            /**
             * BLOQUE QUE OBTIENE EL LISTADO DE VENDEDORES KAMS
             */
            $arrayParametrosKam                          = array();
            $arrayResultadoVendedoresKam                 = array();
            $arrayParametrosKam['strPrefijoEmpresa']     = $strPrefijoEmpresa;
            $arrayParametrosKam['strCodEmpresa']         = $strCodEmpresa;
            $arrayParametrosKam['strEstadoActivo']       = 'Activo';
            $arrayParametrosKam['strDescCaracteristica'] = self::CARGO_GRUPO_ROLES_PERSONAL;
            $arrayParametrosKam['strNombreParametro']    = self::GRUPO_ROLES_PERSONAL;
            $arrayParametrosKam['strDescCargo']          = self::GERENTE_VENTAS;
            $arrayParametrosKam['strDescRolNoPermitido'] = self::ROLES_NO_PERMITIDOS;
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
            /**
             * BLOQUE QUE OBTIENE LA REGIÓN EN SESIÓN Y LOS PARÁMETROS NECESARIOS PARA FILTRAR POR REGIÓN
             */
            $objCanton             = $emComercial->getRepository("schemaBundle:AdmiCanton")->find($intIdCanton);
            $strRegionSesion       = $objCanton->getRegion();
            $arrayParametrosRoles  = array('strCodEmpresa'     => $strCodEmpresa,
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
        }
        $intItemMenuId = $objSession->get('id_menu_activo');
        if(!empty($intItemMenuId))
        {
            $arrayParametros['intIdItemMenu'] = $intItemMenuId;
        }
        $arrayParametros['intIdEmpresa']                = ($objRequest->getSession()->get('idEmpresa') ? $objRequest->getSession()->get('idEmpresa') : "");
        $arrayParametros['strFechaDesdePlanif']         = explode('T', $objRequest->query->get('fechaDesdePlanif'));
        $arrayParametros['strFechaHastaPlanif']         = explode('T', $objRequest->query->get('fechaHastaPlanif'));
        $arrayParametros['strFechaDesdeIngOrd']         = explode('T', $objRequest->query->get('fechaDesdeIngOrd'));
        $arrayParametros['strFechaHastaIngOrd']         = explode('T', $objRequest->query->get('fechaHastaIngOrd'));
        $arrayParametros['strLogin']                    = $objRequest->query->get('txtLogin');
        $arrayParametros['strDescripcionPunto']         = $objRequest->query->get('txtDescripcionPunto');
        $arrayParametros['strVendedor']                 = $objRequest->query->get('txtVendedor');
        $arrayParametros['strCiudad']                   = $objRequest->query->get('txtCiudad');
        $arrayParametros['txtClienteIdentificacion']    = $objRequest->query->get('txtClienteIdentificacion');
        $arrayParametros['intIdTipoSolicitud']          = $objRequest->query->get('cboTipoSolicitud');
        $arrayParametros['strTipoSolicitud']            = $objRequest->query->get('txtTipoSolicitud');        
        $arrayParametros['strEstado']                   = $objRequest->query->get('cboEstados');
        $arrayParametros['strCodigoSolicitud']          = $objRequest->query->get('txtCodigo');
        $arrayParametros['intIdProducto']               = $objRequest->query->get('cboProductos');
        $arrayParametros['boolMasivas']                 = $objRequest->query->get('boolMasivas');
        $arrayParametros['strTipoAprobacion']           = $objRequest->query->get('tipoAprobacion');        
        $arrayParametros['boolReqArchivo']              = $objRequest->query->get('boolReqArchivo');
        $arrayParametros['boolReqAprobPrecio']          = $objRequest->query->get('boolReqAprobPrecio');
        $arrayParametros['boolReqAprobRadio']           = $objRequest->query->get('boolReqAprobRadio');
        $arrayParametros['boolReqAprobIpccl2']          = $objRequest->query->get('boolReqAprobIpccl2');
        $arrayParametros['strEstadoDetalles']           = $objRequest->query->get('strEstadoDetalles');        
        $arrayParametros['strEstadoAprobPrecio']        = $objRequest->query->get('strEstadoAprobPrecio');
        $arrayParametros['strEstadoAprobRadio']         = $objRequest->query->get('strEstadoAprobRadio');
        $arrayParametros['strEstadoAprobIpccl2']        = $objRequest->query->get('strEstadoAprobIpccl2');
        $arrayParametros['boolVisualizar']              = $objRequest->query->get('boolVisualizar') ? $objRequest->query->get('boolVisualizar') : "";

        
        if($arrayParametros['strEstado'] != 'Pendiente')
        {
            $arrayParametros['strEstadoDetalles']       = 'Pendiente,Aprobada,EnProceso,Fallo,Finalizada';
            $arrayParametros['strEstadoAprobPrecio']    = 'Pendiente,Aprobada,Finalizada';
            $arrayParametros['strEstadoAprobRadio']     = 'Pendiente,Aprobada,Finalizada';
            $arrayParametros['strEstadoAprobIpccl2']    = 'Pendiente,Aprobada,Finalizada';
            
            if($arrayParametros['boolReqAprobRadio'] == 'true')
            {
                $arrayParametros['strEstadoAprobPrecio'] = null;
                $arrayParametros['strEstadoAprobIpccl2'] = null;
            }
            
            if($arrayParametros['boolReqAprobIpccl2'] == 'true')
            {
                $arrayParametros['strEstadoAprobPrecio'] = null;
                $arrayParametros['strEstadoAprobRadio'] = null;
            }
            
            $arrayParametros['strConector']             = 'OR';
        }
        
        $arrayParametros['intIdPuntoCobertura']   = $objRequest->query->get('cboOficinas');
        if($objRequest->query->get('boolVisualizar') == "Permitido")
        {
            $arrayParametros['intStart']              = $objRequest->query->get('start');
            $arrayParametros['intLimit']              = $objRequest->query->get('limit');
        }
        $arrayParametros['strPrefijoEmpresa']     = $strPrefijoEmpresa;
        $arrayParametros['strTipoPersonal']       = $strTipoPersonal;
        $arrayParametros['intIdPersonEmpresaRol'] = $intIdPersonEmpresaRol;
        $arrayParametros['strRegion']             = $strRegionSesion;
        $arrayParametros['strIsp']                = $strIsp;
        $arrayParametros['arrayRolNoPermitido']   = (!empty($arrayRolesNoIncluidos) && is_array($arrayRolesNoIncluidos))?$arrayRolesNoIncluidos:"";
        $arrayResultado                           = $infoDetalleSolicitud->findSolicitudesMasivas($arrayParametros);

        if(!empty($arrayResultado['registros']) && is_array($arrayResultado['registros']))
        {
            foreach($arrayResultado['registros'] as $arrayItemSolicitud)
            {
                $floatPorcentajeDescuento = 0;
                $strCargoAsignado         = "";
                if(!empty($strPrefijoEmpresa) && $strPrefijoEmpresa == "TN" && 
                  ($arrayItemSolicitud['descripcionSolicitud']=="CAMBIO PLAN"||$arrayItemSolicitud['descripcionSolicitud']=="CAMBIO PRECIO"))
                {
                    $arrayDetalleSolicitud = array();
                    $arrayParametrosSolDet = array();
                    $arrayParametrosSolDet['intIdEmpresa'] = $strCodEmpresa;
                    $arrayParametrosSolDet['intIdPadre']   = $arrayItemSolicitud['idDetalleSolicitud'];
                    $arrayParametrosSolDet['boolMasivas']  = 'false';
                    $arrayDetalleSolicitud = $infoDetalleSolicitud->findSolicitudes($arrayParametrosSolDet);
                    if(!empty($arrayDetalleSolicitud["registros"]) && is_array($arrayDetalleSolicitud["registros"])
                       &&($arrayItemSolicitud['descripcionSolicitud']=="CAMBIO PLAN"||$arrayItemSolicitud['descripcionSolicitud']=="CAMBIO PRECIO"))
                    {
                        foreach($arrayDetalleSolicitud["registros"] as $arrayItemDetSolicitud)
                        {
                            $strLoginVendedor            = "";
                            $intPrecioSolicitudMasiva    = 0;
                            $intPrecioMinimo             = 0;
                            $floatPorcentajeDescuento    = 0;
                            $floatPorcentajeDescuentoTmp = 0;
                            $strCargoAsignado            = "";
                            $intPrecioSolicitudMasiva = $this->getValorCaracteristicaDetalleSolicitud($arrayItemDetSolicitud['idDetalleSolicitud'],
                                                                                                      'Precio');
                            $intPrecioMinimo          = $this->getPrecioMinimoServicioSolicitud($emComercial,
                                                                                                $arrayItemDetSolicitud["idServicio"],
                                                                                                $arrayItemDetSolicitud["idDetalleSolicitud"],
                                                                                                $arrayItemDetSolicitud["productoId"]);
                            $objIdServicio     = $emComercial->getRepository("schemaBundle:InfoServicio")
                                                             ->find($arrayItemDetSolicitud['idServicio']);
                            $strLoginVendedor  = $objIdServicio->getUsrVendedor();
                            $strTipoNegocioPto = $objIdServicio->getPuntoId()->getTipoNegocioId()->getCodigoTipoNegocio();

                            if(!empty($intPrecioSolicitudMasiva) && !empty($intPrecioMinimo))
                            {
                                $floatPorcentajeDescuentoTmp = 100 - ((floatval($intPrecioSolicitudMasiva) * 100)/floatval($intPrecioMinimo));
                                $floatPorcentajeDescuento    = abs($floatPorcentajeDescuentoTmp);
                            }
                            if(is_array($arrayCargosDet))
                            {
                                //Se obtiene los datos del vendedor para saber si es de la región R1 o R2 y con ello se mostrará el cargo asignado
                                $arrayDatosVendedor = $emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                                                ->getInfoDatosPersona(array('strRol'                     => 'Empleado',
                                                                                            'strPrefijo'                 => $strPrefijoEmpresa,
                                                                                            'strEstadoPersona'           => array('Activo',
                                                                                                                                    'Pendiente',
                                                                                                                                    'Modificado'),
                                                                                            'strEstadoPersonaEmpresaRol' => 'Activo',
                                                                                            'strLogin'                   => $strLoginVendedor));
                                if(empty($arrayDatosVendedor) || !is_array($arrayDatosVendedor) ||
                                    (isset($arrayDatosVendedor['status']) && $arrayDatosVendedor['status'] === 'fail') ||
                                    ($arrayDatosVendedor['status'] === 'ok' && empty($arrayDatosVendedor['result'])))
                                {
                                    throw new \Exception('Error al obtener los datos del vendedor asignado, por favor comunicar a Sistemas.');
                                }
                                foreach($arrayCargosDet as $objCargosItem)
                                {
                                    if(floatval($floatPorcentajeDescuento) >= floatval($objCargosItem->getValor1()) && 
                                       floatval($floatPorcentajeDescuento) <= floatval($objCargosItem->getValor2()))
                                    {
                                        $strCargoAsignado = ucwords(strtolower(str_replace("_"," ",$objCargosItem->getValor3())));
                                        if((!empty($strCargoAsignado) && $strCargoAsignado == "Gerente Ventas") || 
                                           (!empty($strCargoAsignado) && $strCargoAsignado == "Subgerente" && 
                                            in_array($strLoginVendedor,$arrayLoginVendedoresKam)))
                                        {
                                            $strCargoAsignado = (!empty($arrayDatosVendedor['result'][0]['region'])) ? 
                                                                "Gerente Comercial ".$arrayDatosVendedor['result'][0]['region']:
                                                                "Gerente Comercial";
                                        }
                                        $strCargoAsignado = (!empty($strCargoAsignado) && $strCargoAsignado == "Subgerente" && 
                                                            $strTipoNegocioPto == "ISP") ? "Aprobador ISP" : $strCargoAsignado;
                                    }
                                }
                            }
                        }
                        if(!in_array($strTipoPersonal,$arrayTPNoPermitido)&& 
                            ($arrayItemSolicitud['descripcionSolicitud'] == "CAMBIO PLAN" || 
                            $arrayItemSolicitud['descripcionSolicitud'] == "CAMBIO PRECIO"))
                        {
                            if((floatval($floatPorcentajeDescuento) >= floatval($floatDescPorAprobarIni) && 
                                floatval($floatPorcentajeDescuento) <= floatval($floatDescPorAprobarFin)) ||
                                ($strTipoPersonal == "GERENTE_VENTAS" &&
                                (in_array($strLoginVendedor,$arrayLoginVendedoresKam) || $strUsrCreacion == $strLoginVendedor) &&
                                ($floatPorcentajeDescuento <=  floatval($floatDescPorAprobarFin))))
                            {
                                $arraySolicitudes[] = array('intIdSolicitud'             => $arrayItemSolicitud['idDetalleSolicitud'],
                                                            'strCliente'                 => $arrayItemSolicitud['cliente'],
                                                            'strTipoSolicitud'           => $arrayItemSolicitud['descripcionSolicitud'],
                                                            'strFeCreacion'              => $arrayItemSolicitud['feCreacion'],
                                                            'strFePlanificacion'         => $arrayItemSolicitud['fePlanificacion'],
                                                            'strFeEjecucion'             => $arrayItemSolicitud['feEjecucion'],
                                                            'strFeRechazo'               => $arrayItemSolicitud['feRechazo'],
                                                            'strProducto'                => $arrayItemSolicitud['producto'],
                                                            'strUsrCreacion'             => $arrayItemSolicitud['usrCreacion'],
                                                            'strEstado'                  => $arrayItemSolicitud['estado'],
                                                            'strArchivo'                 => $arrayItemSolicitud['archivo'],
                                                            'intTotalDetallesAprobados'  => $arrayItemSolicitud['totalAprobadas'],
                                                            'intTotalDetallesRechazadas' => $arrayItemSolicitud['totalRechazadas'],
                                                            'intTotalDetallesEnProceso'  => $arrayItemSolicitud['totalProceso'],
                                                            'intTotalDetallesFallo'      => $arrayItemSolicitud['totalFallo'],
                                                            'intTotalDetallesEliminada'  => $arrayItemSolicitud['totalEliminada'],
                                                            'intTotalDetallesFinalizada' => $arrayItemSolicitud['totalFinalizadas'],
                                                            'floatPorcentajeDescuento'   => round(floatval($floatPorcentajeDescuento),2).'%',
                                                            'strCargoAsignado'           => $strCargoAsignado);
                            }
                        }
                        else
                        {
                            $arraySolicitudes[] = array('intIdSolicitud'             => $arrayItemSolicitud['idDetalleSolicitud'],
                                                        'strCliente'                 => $arrayItemSolicitud['cliente'],
                                                        'strTipoSolicitud'           => $arrayItemSolicitud['descripcionSolicitud'],
                                                        'strFeCreacion'              => $arrayItemSolicitud['feCreacion'],
                                                        'strFePlanificacion'         => $arrayItemSolicitud['fePlanificacion'],
                                                        'strFeEjecucion'             => $arrayItemSolicitud['feEjecucion'],
                                                        'strFeRechazo'               => $arrayItemSolicitud['feRechazo'],
                                                        'strProducto'                => $arrayItemSolicitud['producto'],
                                                        'strUsrCreacion'             => $arrayItemSolicitud['usrCreacion'],
                                                        'strEstado'                  => $arrayItemSolicitud['estado'],
                                                        'strArchivo'                 => $arrayItemSolicitud['archivo'],
                                                        'intTotalDetallesAprobados'  => $arrayItemSolicitud['totalAprobadas'],
                                                        'intTotalDetallesRechazadas' => $arrayItemSolicitud['totalRechazadas'],
                                                        'intTotalDetallesEnProceso'  => $arrayItemSolicitud['totalProceso'],
                                                        'intTotalDetallesFallo'      => $arrayItemSolicitud['totalFallo'],
                                                        'intTotalDetallesEliminada'  => $arrayItemSolicitud['totalEliminada'],
                                                        'intTotalDetallesFinalizada' => $arrayItemSolicitud['totalFinalizadas'],
                                                        'floatPorcentajeDescuento'   => round(floatval($floatPorcentajeDescuento),2).'%',
                                                        'strCargoAsignado'           => $strCargoAsignado);
                        }
                    }
                }
                else
                {
                    $arraySolicitudes[] = array('intIdSolicitud'             => $arrayItemSolicitud['idDetalleSolicitud'],
                                                'strCliente'                 => $arrayItemSolicitud['cliente'],
                                                'strTipoSolicitud'           => $arrayItemSolicitud['descripcionSolicitud'],
                                                'strFeCreacion'              => $arrayItemSolicitud['feCreacion'],
                                                'strFePlanificacion'         => $arrayItemSolicitud['fePlanificacion'],
                                                'strFeEjecucion'             => $arrayItemSolicitud['feEjecucion'],
                                                'strFeRechazo'               => $arrayItemSolicitud['feRechazo'],
                                                'strProducto'                => $arrayItemSolicitud['producto'],
                                                'strUsrCreacion'             => $arrayItemSolicitud['usrCreacion'],
                                                'strEstado'                  => $arrayItemSolicitud['estado'],
                                                'strArchivo'                 => $arrayItemSolicitud['archivo'],
                                                'intTotalDetallesAprobados'  => $arrayItemSolicitud['totalAprobadas'],
                                                'intTotalDetallesRechazadas' => $arrayItemSolicitud['totalRechazadas'],
                                                'intTotalDetallesEnProceso'  => $arrayItemSolicitud['totalProceso'],
                                                'intTotalDetallesFallo'      => $arrayItemSolicitud['totalFallo'],
                                                'intTotalDetallesEliminada'  => $arrayItemSolicitud['totalEliminada'],
                                                'intTotalDetallesFinalizada' => $arrayItemSolicitud['totalFinalizadas'],
                                                'floatPorcentajeDescuento'   => round(floatval($floatPorcentajeDescuento),2).'%',
                                                'strCargoAsignado'           => $strCargoAsignado);
                }
            }
        }
        if(!empty($arraySolicitudes) && is_array($arraySolicitudes))
        {
            $objJson = '{"total":"' . $arrayResultado['total'] . '","jsonSolicitudesMasivas":' . json_encode($arraySolicitudes) . '}';
        }
        else
        {
            $objJson = '{"total":"0","jsonSolicitudesMasivas":[]}';
        }
        $objResponse->setContent($objJson);
        return $objResponse;
    }

    /**
     * getJsonHistorialSolicitudAction, Obtiene el Historial de las Solicitudes Masivas
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 26-03-2016
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 17-05-2016 - Se envía como parámetro el $emSoporte a la función generarJsonHistorialDetalleSolicitud
     * 
     * @return json Retorna un json de los historicos de las Solicitudes Masivas
     */
    public function getJsonHistorialSolicitudAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $emComercial = $this->get('doctrine')->getManager('telconet');
        $emGeneral = $this->get('doctrine')->getManager('telconet_general');
        $emSoporte = $this->get('doctrine')->getManager('telconet_soporte');
        $infoDetalleSolicitud = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud');
        $peticion = $this->getRequest();
        $idSolicitud = $peticion->query->get('idSolicitud');
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        $objJson = $infoDetalleSolicitud->generarJsonHistorialDetalleSolicitud($idSolicitud, $start, $limit, $emGeneral,$emSoporte);
        $respuesta->setContent($objJson);
        return $respuesta;
    }

    /**
     * getTipoSolicitudAction, Obtiene los tipos de solicitudes de las Solicitudes Masivas y segun itemMenuId en sesion
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 26-03-2016
     * @return json Retorna un json de los tipos de solicitudes de las Solicitudes Masivas
     */
    public function getTipoSolicitudAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/json');

        $session = $this->getRequest()->getSession();
        $itemMenuId = $session->get('id_menu_activo');

        $emComercial = $this->getDoctrine()->getManager();
        $admiTipoSolicitud = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud');

        $tiposSolicitud = $admiTipoSolicitud->findBy(array('itemMenuId' => $itemMenuId, 'estado' => 'Activo'));
        if($tiposSolicitud)
        {
            $arrayStoreTipoSolicitud = array();
            foreach($tiposSolicitud as $tipoSolicitud)
            {
                $arrayStoreTipoSolicitud[] = array('intIdTipoSolicitud' => $tipoSolicitud->getId(), 'strNombreTipoSolicitud' => $tipoSolicitud->getDescripcionSolicitud());
            }
            $data = '{"total":"' . count($arrayStoreTipoSolicitud) . '","jsonTipoSolicitud":' . json_encode($arrayStoreTipoSolicitud) . '}';
        }
        else
        {
            $data = '{"total":"0","resultado":[]}';
        }
        $response->setContent($data);
        return $response;
    }

    /**
     * getEstadosAction, Obtiene los estados de las Solicitudes Masivas
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 26-03-2016
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 10-07-2017 - Se agrega el estado 'Activa' para la consulta de las nuevas solicitudes
     *
     * @return json Retorna un json de los estados de las Solicitudes Masivas
     */
    public function getEstadosAction()
    {
        $arrayStoreEstados[] = array('strIdEstado' => 'Pendiente', 'strNombreEstado' => 'Pendiente');
        $arrayStoreEstados[] = array('strIdEstado' => 'Finalizada','strNombreEstado' => 'Finalizada');
        $arrayStoreEstados[] = array('strIdEstado' => 'Eliminada', 'strNombreEstado' => 'Eliminada');
        $arrayStoreEstados[] = array('strIdEstado' => 'Activa',    'strNombreEstado' => 'Activa');        
        $arrayStoreEstados[] = array('strIdEstado' => '', 'strNombreEstado' => 'Todos');
        $objResponse = new Response(json_encode(array('total' => "'" . count($arrayStoreEstados) . "'", 'jsonEstados' => $arrayStoreEstados)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
     * getPlanesAction, Obtiene los planes en estado Activo y segun la empresa en sesion
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 26-03-2016
     * @return json Retorna un json de los planes en estado Activo y segun la empresa
     */
    public function getPlanesAction()
    {
        $objRequest = $this->getRequest();
        $objSession = $objRequest->getSession();
        $intIdEmpresa = $objSession->get('idEmpresa');

        $emComerial = $this->getDoctrine()->getManager();

        $entityInfoPlan = $emComerial->getRepository('schemaBundle:InfoPlanCab')
            ->findBy(array('estado' => 'Activo', 'empresaCod' => $intIdEmpresa), array('nombrePlan' => 'ASC'));
        $arrayStorePlanes = array();
        foreach($entityInfoPlan as $objInfoPlan):
            $arrayStorePlanes[] = array('intIdPlan' => $objInfoPlan->getId(),
                'strNombrePlan' => $objInfoPlan->getNombrePlan());
        endforeach;
        $objResponse = new Response(json_encode(array('total' => "'" . count($arrayStorePlanes) . "'", 'jsonPlanes' => $arrayStorePlanes)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
     * getProductosAction, Obtiene los productos en estado Activo y segun la empresa en sesion
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 26-03-2016
     * @return json Retorna un json de los productos en estado Activo y segun la empresa
     * 
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.1 13-07-2016 - Se discrimina si EsConcentrador desde el requerimiento
     * 
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.2 21-07-2016 - Se obtiene el estado del producto de la peticion
     * 
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.3 20-07-2016 - Cambios en parametros de respuesta para vsat
     * 
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.4 03-08-2016 - Se aumento el filtro de nombre tecnico y el precio del producto en caso de los obsoletos
     * 
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.5 23-05-2017 - Se retornan productos tradicionales por concepto de solicitudes de Demo
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.6 09-08-2017 - Se realizan ajustes en la llamada a la función getProductosTradicionales, se envian parametros adicionales
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.6 07-03-2018 - Se agrega validación para permitir sólo solicitudes masivas de cambio de plan y cancelación para los
     *                           servicios Internet Small Business 
     * 
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.7 23-11-2018 - Se extraen las consultas de las características, de la iteración de lista de productos.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.8 04-02-2019 - Se obtiene la característica VELOCIDAD_TELCOHOME para servicios TelcoHome. 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.9 25-02-2019 - Se valida que para servicios TelcoHome sólo se permita las solicitudes de cancelación por solicitudes masivas. 
     * 
     * @author David Leon <mdleon@telconet.ec>
     * @version 2.0 03-07-2020 - Se Permite el producto TelcoHome para realizar cambio de plan.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.1 14-10-2020 Se modifican los parámetros enviados a la función getProductosTradicionales, ya que se ha cambiado dicha consulta
     *                         evitando comparar con el grupo de los productos. Se agrega el envío del parámetro strOmiteProductoRestringidoDemo para
     *                         restringir la obtención del producto INTERNET WIFI
     * 
     * @author Mario Ayerve <mayerve@telconet.ec>
     * @version 2.2 22-06-2021 Se validaron las solicitudes para que en el cambio de precio, aparezcan los productos de Small Business
     *                         estos deberan ser aplicados en las funcionalidad de cambio de precios y cambio de plan.
     */
    public function getProductosAction()
    {
        ini_set('max_execution_time', 90000);

        $objRequest               = $this->get('request');
        $objSession               = $objRequest->getSession();
        $intIdEmpresa             = $objSession->get('idEmpresa');
        $arrayParametrosProductos = array();

        $emComercial                = $this->getDoctrine()->getManager();
        $admiProducto               = $emComercial->getRepository('schemaBundle:AdmiProducto');
        $admiCaracteristica         = $emComercial->getRepository('schemaBundle:AdmiCaracteristica');
        $admiProductoCaracteristica = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica');
        $admiTipoSolicitud          = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud'); 

        $strIdCompuesto             = $objRequest->query->get('strIdCompuesto');
        $strTipoSolicitud           = $objRequest->query->get('strTipoSolicitud');
        $strEsConcentrador          = $objRequest->query->get('strEsConcentrador');
        $intIdProductoSeleccionado  = $objRequest->query->get('intIdProductoSeleccionado');
        
        $admiTipoSolicitudEntity    = null;
        
        //Características
        $objCaracteristicaVelocidad     = $admiCaracteristica->findOneBy(array("descripcionCaracteristica" => "VELOCIDAD"));
        $caracteristicaCapacidad1       = $admiCaracteristica->findOneBy(array("descripcionCaracteristica" => "CAPACIDAD1"));
        $caracteristicaCapacidad2       = $admiCaracteristica->findOneBy(array("descripcionCaracteristica" => "CAPACIDAD2"));
        $objCaractVelocidadTelcoHome    = $admiCaracteristica->findOneBy(array("descripcionCaracteristica" => "VELOCIDAD_TELCOHOME"));
        
        if(!empty($strTipoSolicitud)){
            $admiTipoSolicitudEntity = $admiTipoSolicitud->find($strTipoSolicitud);
        }
        
        $arrayParametrosProducto                        = array();
        $arrayParametrosProducto['intIdEmpresa']        = $intIdEmpresa;
        $arrayParametrosProducto['strEstado']           = $objRequest->query->get('strEstado');
        $arrayParametrosProducto['strSoporteMasivo']    = 'S';
        $arrayParametrosProducto['strEsConcentrador']   = $strEsConcentrador;
        $arrayParametrosProducto['strNombreTecnico']    = $objRequest->query->get('strNombreTecnico');
        
        if(empty($arrayParametrosProducto['strNombreTecnico']))
        {
            $arrayParametrosProducto['arrayComparador']                     = array();
            $arrayParametrosProducto['arrayComparador']['strOperador']      = 'OR';
            $arrayParametrosProducto['arrayComparador']['strNombreTecnico'] = 'INTMPLS';
        }
        
        if(is_object($admiTipoSolicitudEntity))
        {
            if($admiTipoSolicitudEntity->getDescripcionSolicitud() == 'DEMOS')
            {
                $arrayParametrosProductos["strEmpresaCod"]                      = $intIdEmpresa;
                $arrayParametrosProductos["strSoporteMasivo"]                   = "S";
                $arrayParametrosProductos["strEstadoServicio"]                  = "Activo";
                $arrayParametrosProductos["strNombreTecnico"]                   = "FINANCIERO";
                $arrayParametrosProductos["strEsProductoTradicional"]           = "SI";
                $arrayParametrosProductos["strOmiteProductoRestringidoDemo"]    = "SI";
                $arrayProductoresult = $admiProducto->getProductosTradicionales($arrayParametrosProductos);
            }
            else
            {
                $arrayProductoresult = $admiProducto->findProductos($arrayParametrosProducto);                
            }        
        }

        $entityAdmiProductoList = $arrayProductoresult['registros'];

        $arrayStoreProductos = array();
        foreach($entityAdmiProductoList as $objAdmiProducto): 
            /*
             * Validacion realizada para omitir aquellos productos en el combo de obsoletos que son inactivos y 
             * no tienen mas de un producto al cual cambiar es decir no son viables para el cambio de plan, tambien
             * dado que se quiere cambiar de un producto obsoleto a otro, en el combo no se presenta el producto origen
             */ 
            if($intIdProductoSeleccionado == $objAdmiProducto['idProducto'] || 
               ($arrayParametrosProducto['strEstado'] == 'Inactivo' && $objAdmiProducto['totalMismoNombreTecnico'] <= 1))
            {
                continue;
            }
            // Se obtiene el precio minimo de la funcion de precio del producto y se asigna el valor evaluando dicha funcion
            $intPrecio = 0;
            if($arrayParametrosProducto['strEstado'] == 'Inactivo' && !empty($objAdmiProducto['funcionPrecio']))
            {
                $funcionPrecio = str_replace('PRECIO', '$intPrecio', $objAdmiProducto['funcionPrecio']);
                $digitoVerificacion = substr($funcionPrecio, -1, 1);
                if(is_numeric($digitoVerificacion))
                {
                    $funcionPrecio = $funcionPrecio . ";";
                }
                eval($funcionPrecio);
            }
            
            if($objAdmiProducto['nombreTecnico'] === "INTERNET SMALL BUSINESS")
            {
                if($admiTipoSolicitudEntity != null && 
                    ($admiTipoSolicitudEntity->getDescripcionSolicitud() === 'DEMOS'))
                {
                    continue;
                }
                else
                {
                    //Se permite solicitudes para cambio de plan y cancelación
                    if(is_object($objCaracteristicaVelocidad))
                    {
                        $objProdCaractVelocidad = $admiProductoCaracteristica->findOneBy(array( "productoId"        => 
                                                                                                $objAdmiProducto['idProducto'],
                                                                                                "caracteristicaId"  => 
                                                                                                $objCaracteristicaVelocidad));
                    }
                    
                    if((!is_object($objProdCaractVelocidad) && $arrayParametrosProducto['strEstado'] != 'Inactivo') 
                        || $arrayParametrosProducto['strEstado'] == 'Inactivo' )
                    {
                        continue;
                    }
                }
            }
            else if($objAdmiProducto['nombreTecnico'] === "TELCOHOME")
            {
                if($admiTipoSolicitudEntity != null && 
                    ($admiTipoSolicitudEntity->getDescripcionSolicitud() === 'DEMOS'
                    ))
                {
                    continue;
                }
                else
                {
                    $objCaractVelocidadTelcoHome    = $admiCaracteristica->findOneBy(array("descripcionCaracteristica" => "VELOCIDAD_TELCOHOME"));

                    //Se permite solicitudes para cambio de plan y cancelación
                    if(is_object($objCaractVelocidadTelcoHome))
                    {
                        $objProdCaractVelocidadTelcoHome = $admiProductoCaracteristica->findOneBy(array("productoId"        => 
                                                                                                        $objAdmiProducto['idProducto'],
                                                                                                        "caracteristicaId"  => 
                                                                                                        $objCaractVelocidadTelcoHome));
                    }
                    
                    if((!is_object($objProdCaractVelocidadTelcoHome) && $arrayParametrosProducto['strEstado'] != 'Inactivo') 
                        || $arrayParametrosProducto['strEstado'] == 'Inactivo' )
                    {
                        continue;
                    }
                }
            }
            /* Si el tipo de solicitud es cambio de plan y no posee las caracteristicas de ancho de banda no son incluidas*/
            else if($admiTipoSolicitudEntity != null && $admiTipoSolicitudEntity->getDescripcionSolicitud() == 'CAMBIO PLAN')
            {
                                
                $prodCaractCapacidad1 = $admiProductoCaracteristica->findOneBy(array("productoId"=>$objAdmiProducto['idProducto'], "caracteristicaId"=>$caracteristicaCapacidad1));
                $prodCaractCapacidad2 = $admiProductoCaracteristica->findOneBy(array("productoId"=>$objAdmiProducto['idProducto'], "caracteristicaId"=>$caracteristicaCapacidad2));
                
                if(($prodCaractCapacidad1 == null &&  $prodCaractCapacidad2 == null  && $arrayParametrosProducto['strEstado'] != 'Inactivo') || 
                   ($arrayParametrosProducto['strEstado'] == 'Inactivo') && ($prodCaractCapacidad1 != null || $prodCaractCapacidad2 != null))
                {
                    continue;
                }
            }

            $strIdProducto = $objAdmiProducto['idProducto'];
            if($strIdCompuesto == 'S')
            {
                $strIdProducto .= '-' . $objAdmiProducto['esEnlace'];
            }
            
            $arrayStoreProductos[] = array('strIdProducto'        => $strIdProducto,
                                            'strNombreProducto'    => $objAdmiProducto['descripcionProducto'],
                                            'strEsEnlace'          => $objAdmiProducto['esEnlace'],
                                            'strSoporteMasivo'     => $objAdmiProducto['soporteMasivo'],
                                            'strEstado'            => $objAdmiProducto['estado'],
                                            'strNombreTecnico'     => $objAdmiProducto['nombreTecnico'],
                                            'intPrecio'            => $intPrecio
                                          );
        endforeach;
        $objResponse = new Response(json_encode(array('total' => "'" . count($arrayStoreProductos) . "'", 'jsonProductos' => $arrayStoreProductos)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    
    
    
     /**
     * getClienteMoraAction
     * 
     * Función que retorna si existen facturas abiertas al mes anterior
     *
     * @return integer facturasMora retorna las facturas abiertas del mes anterior
     * 
     * @return Response
     * 
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 17-07-2017
     */             
    public function getClienteMoraAction()
    {
        $emComercial         = $this->getDoctrine()->getManager();
        $objRequest          = $this->get('request');
        $objSession          = $objRequest->getSession();
        $arrayPuntoSession   = $objSession->get('ptoCliente');      
        $intFactAbiertas     = 0;
        $strFacturasAbiertas = "N";
        $arrayResultado      = array();
        $objRespuesta        = new JsonResponse();
        

        $objInfoDetalleSolicitudRepository = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud');        
                        
        //Se pregunta si el cliente tiene facturas abiertas al mes anterior
        $arrayParametrosFactAbiertas["intPersonaEmpresaRol"] = $arrayPuntoSession["id_persona_empresa_rol"];    

        $intFactAbiertas = $objInfoDetalleSolicitudRepository->getFacturasAbiertasMesAnterior($arrayParametrosFactAbiertas);  

        if($intFactAbiertas > 0)
        {
            $strFacturasAbiertas = "S";
        }        

        $objRespuesta->setData(array('strClienteMora' => $strFacturasAbiertas));

        return $objRespuesta;        
    }    

    
    /**
     * getMotivosAction, Obtiene los Motivos de cancelacion segun el id_relacion_sistema
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 26-03-2016
     * @return json Retorna un json de los motivos de cancelacion segun el id_relacion_sistema
     */
    public function getMotivosAction()
    {
        $objSession = $this->get('request')->getSession();
        $id_relacion_sistema = $objSession->get('id_relacion_sistema');
        $emGeneral = $this->get('doctrine')->getManager('telconet');
        $entityAdmiMotivos = $emGeneral->getRepository('schemaBundle:AdmiMotivo')
            ->loadMotivos($id_relacion_sistema);
        $arrayStoreMotivos = array();
        foreach($entityAdmiMotivos as $objAdmiMotivo):
            $arrayStoreMotivos[] = array('intIdMotivo' => $objAdmiMotivo->getId(),
                'strNombreMotivo' => $objAdmiMotivo->getNombreMotivo());
        endforeach;
        $objResponse = new Response(json_encode(array('total' => "'" . count($arrayStoreMotivos) . "'", 'jsonMotivos' => $arrayStoreMotivos)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
     * getPuntosServiciosAction, Obtiene listado de servicios por puntos
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 01-04-2016
     * @return json Retorna un json del listado de servicios por puntos
     * 
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.1 29-06-2016 - Se Recibe el Parametro strEstado y se añade en la descripcion el estado
     * 
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.2 20-07-2016 - Se agrego bandera en la respuesta para saber si el servicio tiene capacidades
     * 
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.3 28-07-2016 - Se obtiene si el CPE asociado al servicio tiene Capacidades Limites
     *
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.4 04-08-2016 - Se añadio el filtro por nombre tecnico para productos obsoletos, solo filtrar servicios con productos obsoletos que
     *                           coincidan con dicho nombre tecnico
     * 
     * @author  Edgar Holguin   <eholguin@telconet.ec>
     * @version 1.5 30-11-2016 - Se aumentó valor de tiempo establecido para timeout
     * 
     * @author  Allan Suarez   <arsuarez@telconet.ec>
     * @version 1.6 31-03-2017 - Se envia booleano indicando si es CAMBIO PLAN o no para segun eso filtrar servicios PRINCIPAL cuando se trate de 
     *                           este proceso
     *
     * @author  Richard Cabrera   <rcabrera@telconet.ec>
     * @version 1.7 19-07-2017 - Se realizan ajustes para validar si los servicios tienen solicitud de Demo activas
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.8 04-02-2019 - Se agrega la información de la velocidad para servicios TelcoHome
     * 
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.9 10-12-2019 - Se modifica logica para que los servicios Wifi Alquiler de Equipos puedan ser elegidos.
     * 
     */
    public function getPuntosServiciosAction()
    {
        ini_set('max_execution_time', 99999);
        
        $respuesta          = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $arrayParametros    = array();
        $arrayParametrosSol = array();
        $intSolicitudActiva = "";
        $strMensajeDemo     = "";
        $peticion           = $this->getRequest();

        $emComercial  = $this->getDoctrine()->getManager("telconet");
        $infoServicio = $emComercial->getRepository('schemaBundle:InfoServicio');

        $arrayParametros['intIdEmpresa'] = ($peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa') : "");
        $arrayParametros['intIdCliente'] = ($peticion->getSession()->get('cliente') ? $peticion->getSession()->get('cliente')['id'] : "");

        $arrayParametros['intIdTipoSolicitud']          = $peticion->query->get('intIdTipoSolicitud');
        $arrayParametros['strTipoEjecucion']            = $peticion->query->get('strTipoEjecucion');        
        $arrayParametros['strFechaPlanificada']         = $peticion->query->get('strFechaPlanificada');
        $arrayParametros['strHora']                     = $peticion->query->get('strHora');
        $arrayParametros['intIdMotivo']                 = $peticion->query->get('intIdMotivo');
        $arrayParametros['intIdProducto']               = $peticion->query->get('intIdProducto');
        $arrayParametros['intPrecio']                   = $peticion->query->get('intPrecio');
        $arrayParametros['intCapacidad2']               = $peticion->query->get('intCapacidad2');
        $arrayParametros['intCapacidad1']               = $peticion->query->get('intCapacidad1');
        $arrayParametros['strDescripcion']              = $peticion->query->get('strDescripcion');
        $arrayParametros['intIdUltimaMilla']            = $peticion->query->get('intIdUltimaMilla');
        $arrayParametros['strEstado']                   = $peticion->query->get('strEstado');
        $arrayParametros['strNombreTecnicoProducto']    = $peticion->query->get('strNombreTecnico');
        $arrayParametros['strEstadoProducto']           = $peticion->query->get('strEstadoProducto');
        $strNoIdServicios                               = $peticion->query->get('strNoIdServicios');
        $boolCambioPlan                                 = $peticion->query->get('boolCambioPlan');
        $arrayParametros['strNombreSolicitud']          = $peticion->query->get('strNombreSolicitud');

        if(!empty($strNoIdServicios))
        {
            $arrayParametros['strNoIdServicios'] = $strNoIdServicios;
        }
        // Se verifica que no haya idServicios en Solicitudes Masivas en estados distinto de Finalizada
        $idServiciosConSolMasivasAunFinalizadas = $this->getServiciosConSolMasivasAunFinalizadas($emComercial, $arrayParametros);
        if(!empty($idServiciosConSolMasivasAunFinalizadas))
        {        
            if(!empty($arrayParametros['strNoIdServicios']))
            {
                $arrayParametros['strNoIdServicios'] .= ','.$idServiciosConSolMasivasAunFinalizadas;
            }
            else
            {
                $arrayParametros['strNoIdServicios'] = $idServiciosConSolMasivasAunFinalizadas;
            }
        }

        $arrayParametros['intStart']         = $peticion->query->get('start');
        $arrayParametros['intLimit']         = $peticion->query->get('limit');
        $arrayParametros['boolEsCambioPlan'] = (!empty($boolCambioPlan) && $boolCambioPlan == 'true')?true:false;
        $arrayResultado                 = $infoServicio->findServicios($arrayParametros);
        $serviciosPuntos                = $arrayResultado['registros'];
        $total                          = $arrayResultado['total'];

        //Si existen Servicios
        if(count($serviciosPuntos) > 0)
        {
            foreach($serviciosPuntos as $servicio):
                //Se agrega Los Servicios por Puntos
                $boolTieneCPELimit      = false;
                $boolEsEnlace           = false;
                $boolSeleccionable      = false;
                $boolCapacidad1         = false;
                $boolCapacidad2         = false;
                $boolDemoActivo         = false;
                $datosActuales          = "<b>Precio:</b> $ " . $servicio['precioVenta'];
                
                $arrayParametros                    = array();
                $arrayParametros['intIdServicio']   = $servicio['idServicio'];
                $arrayResultadoCaract               = $infoServicio->findServiciosProductoCaracteristicas($arrayParametros);
                $caracteristicasServicio            = $arrayResultadoCaract['registros'];
                
                if(count($caracteristicasServicio) > 0)
                {
                    foreach($caracteristicasServicio as $caracteristica):
                        if($servicio['nombreTecnico'] === "INTERNET SMALL BUSINESS")
                        {
                            if($caracteristica['descripcionCaracteristica'] === "VELOCIDAD")
                            {
                                $datosActuales .= "<br><b>" . $caracteristica['descripcionCaracteristica'] . ": </b>" . $caracteristica['valor'];
                            }
                        }
                        else if($servicio['nombreTecnico'] === "TELCOHOME")
                        {
                            if($caracteristica['descripcionCaracteristica'] === "VELOCIDAD_TELCOHOME")
                            {
                                $datosActuales .= "<br><b>" . $caracteristica['descripcionCaracteristica'] . ": </b>" . $caracteristica['valor'];
                            }
                        }
                        else
                        {
                            $datosActuales .= "<br><b>" . $caracteristica['descripcionCaracteristica'] . ": </b>" . $caracteristica['valor'];
                        }
                        if($caracteristica['descripcionCaracteristica'] == 'CAPACIDAD1')
                        {
                            $boolCapacidad1 = true;
                        }                        
                        if($caracteristica['descripcionCaracteristica'] == 'CAPACIDAD2')
                        {
                            $boolCapacidad2 = true;
                        }
                        /* Se le definiran estas caracteristicas como true al servicio Wifi Alquiler Equipos debido
                        a que este no posee estas caracteristicas y las mismas son evaluadas en la validadción. */
                        if ($servicio['descripcionProducto'] == 'WIFI Alquiler Equipos')
                        {
                            $boolCapacidad1 = true;
                            $boolCapacidad2 = true;
                        }
                    endforeach;
                }
                $boolTieneCapacidades = $boolCapacidad1 && $boolCapacidad2;
                
                if(!empty($boolCambioPlan) && $boolCambioPlan == 'true')
                {                    
                    if(!empty($servicio['productoEsEnlace']) && $servicio['productoEsEnlace'] == 'SI')
                    {
                        $boolEsEnlace = true;
                    }                    
                    if(!empty($servicio['idServicioTecnico']))
                    {
                        $boolSeleccionable = true;
                    }
                    if(!empty($servicio['capacidad1ModeloElemento']) && !empty($servicio['capacidad2ModeloElemento']))
                    {
                        $boolTieneCPELimit = true;
                    }
                }

                 $arrayEstados = array("Pendiente","Aprobada","EnProceso");
                 $arrayParametrosSol["arrayEstados"]  = $arrayEstados;
                 $arrayParametrosSol["strSolicitud"]  = "DEMOS";
                 $arrayParametrosSol["intServicioId"] = $servicio['idServicio'];

                 $intSolicitudesDemo = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                   ->getSolicitudDemoAbiertas($arrayParametrosSol);

                 if($intSolicitudesDemo > 0)
                 {
                     //Se obtiene el estado de la Solicitud
                     $strEstadoSolDemo = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                      ->getEstadoSolDemo($arrayParametrosSol);
                 }

                 if($strEstadoSolDemo == "Pendiente" || $strEstadoSolDemo == "Aprobada" || $strEstadoSolDemo == "EnProceso")
                 {                       
                     $boolDemoActivo = true;
                     $strMensajeDemo = "<br><b>(***Solicitud Demo ".$strEstadoSolDemo."***)</b>";
                 }
                $strEstadoSolDemo       = "";
                $intSolicitudesDemo     = 0;
                $strDescripcionServicio = "<b>Descripción Servicio: </b>".$strMensajeDemo."<br>" . strip_tags ($servicio['descripcionServicio']);
                $strDescripcionServicio .=  "<br><b>Producto: </b>" . $servicio['descripcionProducto'];

                /* Se agrega una validacion para que muestre el texto "NO TIENE" en color rojo cuando no exista Login Aux. */
                if (empty($servicio['loginAux']))
                {
                    $strDescripcionServicio .=  "<br><b>Login Aux: </b><br>" . "<span class='red-text animated infinite bounce'>NO TIENE</span>";
                } else
                {
                    $strDescripcionServicio .=  "<br><b>Login Aux: </b><br>" . $servicio['loginAux'];
                }
                
                $strDescripcionServicio .=  "<br><b>Última Milla: </b><br>" . $servicio['nombreUltimaMilla'];

                if(!empty($servicio['nombreModeloElemento']))
                {
                    $strDescripcionServicio .=  "<br><b>Modelo CPE: </b><br>" . $servicio['nombreModeloElemento'];
                }

                $strDescripcionServicio .=  "<br><br><b>Estado: </b><br>" . $servicio['estado'];

                $arraySercicios[] = array(
                                            'intIdServicio'             => $servicio['idServicio'],
                                            'strDescripcionServicio'    =>  $strDescripcionServicio,
                                            'intIdPunto'                => $servicio['idPunto'],
                                            'strLogin'                  => $servicio['login'],
                                            'intIdProdructo'            => $servicio['idProducto'],
                                            'strdescripcionProducto'    => $servicio['descripcionProducto'],
                                            'strPrecioVenta'            => $servicio['precioVenta'],
                                            'strestado'                 => $servicio['estado'],
                                            'strDatosActuales'          => $datosActuales,
                                            'boolSeleccionable'         => $boolSeleccionable,
                                            'boolTieneCapacidades'      => $boolTieneCapacidades,
                                            'boolEsEnlace'              => $boolEsEnlace,
                                            'boolTieneCPELimit'         => $boolTieneCPELimit,
                                            'strLoginAux'                  => $servicio['loginAux'],
                                            'boolDemoActivo'            => $boolDemoActivo
                                        );
                
                $strMensajeDemo = "";
            endforeach;
        }

        if($total > 0)
        {
            $data       = json_encode($arraySercicios);
            $objJson    = '{"total":"' . $total . '","jsonPuntosServicios":' . $data . '}';
        }
        else
        {
            $objJson = '{"total":"0","jsonPuntosServicios":[]}';
        }
        $respuesta->setContent($objJson);
        return $respuesta;
    }

    /**
     * getServiciosSeleccionados, Obtiene listado de servicios por puntos seleccionados con su nivel de aprobacion
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 01-04-2016
     * @return json Retorna un json del listado de servicios por puntos
     * 
     * @author Modificado: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.1 22-06-2016 - Se presenta en la descripcion del servicio el nombre del switch, puerto y modelo cpe
     * 
     * @author Modificado: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.2 01-07-2016 - Se añadio el estado del servicio en la consulta y en el resultado
     * 
     * @author Modificado: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.3 21-07-2016 - Validaciones de Radio e IPCCL2 para productos EsEnlace Si
     * 
     * @author Modificado: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.4 01-08-2016 - Validaciones de IPCCL2 se optimizó la validacion de BW para el CPE
     * 
     * @author Modificado: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.5 05-08-2016 - Se añadio nuevo parametro intIdprodObsoleto
     * 
     * @author Modificado: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.6 10-08-2016 - Se cambio el esquema de almacenamiento de las caracteristicas nuevas por detalle
     * 
     * @author Modificado: Edgar Holguin <eholguin@telconet.ec>
     * @version 1.7 30-11-2016 - Se aumentó valor de tiempo establecido para timeout
     * 
     * @author Modificado: Allan Suarez <arsuarez@telconet.ec>
     * @version 1.8 03-04-2017 - Se realiza validacion en enlaces backups de las capacidades permitidas por los equipos del cliente
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.9 19-07-2017 - Se realizan ajustes para mostrar si la solicitud de demos es facturable.
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.0 08-03-2018 - Se realizan modificaciones para obtener correctamente la información para servicios Internet Small Business.
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.1 19-03-2019 - Se realizan modificaciones para obtener correctamente la información para servicios TelcoHome.
     * 
     */
    public function getServiciosSeleccionadosAction()
    {
        ini_set('max_execution_time', 99999);
        
        $respuesta       = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $arrayParametros = array();
        $peticion        = $this->getRequest();

        $emComercial                    = $this->getDoctrine()->getManager("telconet");
        $emInfraestructura              = $this->getDoctrine()->getManager("telconet_infraestructura");
        $emGeneral                      = $this->getDoctrine()->getManager("telconet_general");
        $infoDetalleSolRepository       = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud');
        $infoServicioRepository         = $emComercial->getRepository('schemaBundle:InfoServicio');
        $admiProductoRepository         = $emComercial->getRepository('schemaBundle:AdmiProducto');
        $infoProductoNivelRepository    = $emComercial->getRepository('schemaBundle:InfoProductoNivel');
        $infoEmpresaRolRepository       = $emComercial->getRepository('schemaBundle:InfoEmpresaRol');
        $admiRolRepository              = $emComercial->getRepository('schemaBundle:AdmiRol');
        $admiTipoSolicitud              = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud');
        $infoServicioTecnico            = $emComercial->getRepository('schemaBundle:InfoServicioTecnico');
        $admiMotivo                     = $emGeneral->getRepository('schemaBundle:AdmiMotivo');
        $infoElemento                   = $emInfraestructura->getRepository('schemaBundle:InfoElemento');
        $admiTipoMedio                  = $emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio');
        $infoInterfaceElemento          = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento');

        $arrayParametros['intIdEmpresa']        = ($peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa') : "");
        $arrayParametros['intIdCliente']        = ($peticion->getSession()->get('cliente') ? $peticion->getSession()->get('cliente')['id'] : "");
        $arrayParametros['intIdTipoSolicitud']  = $peticion->query->get('intIdTipoSolicitud');
        $objDetalleCaractacteristicasNuevas     = json_decode($peticion->query->get('strJsonDetalleCaract'));
        $arrayParametros['strIdServicios']      = $peticion->query->get('strIdServicios');
        $arrayParametros['strEstado']           = $peticion->query->get('strEstado');
        $arrayParametros['intStart']            = $peticion->query->get('start');
        $arrayParametros['intLimit']            = $peticion->query->get('limit');
        $serviceTecnico                         = $this->get('tecnico.InfoServicioTecnico');
        
        $tipoSolicitud = $admiTipoSolicitud->find($arrayParametros['intIdTipoSolicitud']);

        $arrayResultado     = $infoServicioRepository->findServicios($arrayParametros);
        $serviciosPuntos    = $arrayResultado['registros'];
        $total              = $arrayResultado['total'];

        //Si existen Servicios
        if(count($serviciosPuntos) > 0 && $objDetalleCaractacteristicasNuevas->arrayData)
        {
            foreach($serviciosPuntos as $servicio):
                
                $arrayParametros     = array();
                $capacidad1Original  = "";
                $capacidad2Original  = "";
                $strDatosNuevos      = "";
                
                $arrayParametros = $this->getCaracteristicasNuevasXServicio($objDetalleCaractacteristicasNuevas->arrayData, $servicio['idServicio']);
                if(count($arrayParametros) > 0)
                { 
                    if($arrayParametros['strTipoSolicitud'] == "DEMOS")
                    {                  
                        $strDatosNuevos .= "<b>Numero Dias: </b>".$arrayParametros['intDuracionDemo']."<br>";                            
                    }

                    if(!empty($arrayParametros['intPrecio']))
                    {
                        if($arrayParametros['strTipoSolicitud'] == "DEMOS")
                        {
                            $strDatosNuevos .= "<b>Precio del Demo: </b> $ " . $arrayParametros['intPrecio'];
                        }
                        else
                        {
                            $strDatosNuevos .= "<b>Precio: </b> $ " . $arrayParametros['intPrecio'];                            
                        }
                    }
                    else
                    {
                        if($arrayParametros['strEsFacturable'] == "NO")
                        {
                            $strDatosNuevos .= "<b>Precio del Demo: </b> $ 0";
                        }
                    }
                    if(!empty($arrayParametros['strTipoSolicitud']) && !empty($arrayParametros['strEsFacturable']) && 
                        $arrayParametros['strTipoSolicitud'] == "DEMOS")
                    {
                        $strDatosNuevos .= "<br><b>Facturable: </b> " . $arrayParametros['strEsFacturable'];
                    }                      
                    if(!empty($arrayParametros['intCapacidad1']))
                    {
                        $strDatosNuevos .= "<br><b>CAPACIDAD1: </b> " . $arrayParametros['intCapacidad1'];
                    }
                    if(!empty($arrayParametros['intCapacidad2']))
                    {
                        $strDatosNuevos .= "<br><b>CAPACIDAD2: </b> " . $arrayParametros['intCapacidad2'];
                    }
                    if(!empty($arrayParametros['intVelocidad']))
                    {
                        $strDatosNuevos .= "<br><b>VELOCIDAD: </b> " . $arrayParametros['intVelocidad'];
                    }

                    //Validar si el servicio tiene una solicitud de Demos en estado Activa                        
                    $arrayParametrosSol["intServicio"]  = $servicio['idServicio']; 
                    $arrayParametrosSol["strSolicitud"] = "DEMOS";
                    $arrayParametrosSol["strEstado"]    = "Activa";

                    $intSolicitudActiva = $infoDetalleSolRepository->getSolicitudActivaPorServicio($arrayParametrosSol);

                    if($intSolicitudActiva != "")
                    {
                        $strDatosActuales = "<b>(*****DEMO ACTIVO*****)</b><br>";
                    }

                    //Se agrega Los Servicios por Puntos
                    $strDatosActuales .= "<b>Precio:</b> $ " . $servicio['precioVenta'];

                    $arrayParametrosCaracteristica                  = array();
                    $arrayParametrosCaracteristica['intIdServicio'] = $servicio['idServicio'];

                    $arrayResultadoCaract       = $infoServicioRepository->findServiciosProductoCaracteristicas($arrayParametrosCaracteristica);
                    $caracteristicasServicio    = $arrayResultadoCaract['registros'];

                    $arrayProductoCaracteristicasValores = array();
                    if(count($caracteristicasServicio) > 0)
                    {
                        foreach($caracteristicasServicio as $caracteristica):
                            if($servicio['nombreTecnico'] === "INTERNET SMALL BUSINESS")
                            {
                                if($caracteristica['descripcionCaracteristica'] === "VELOCIDAD")
                                {
                                    $strDatosActuales .= "<br><b>" . $caracteristica['descripcionCaracteristica'] . ": </b>" 
                                                      . $caracteristica['valor'];
                                }
                            }
                            else if($servicio['nombreTecnico'] === "TELCOHOME")
                            {
                                if($caracteristica['descripcionCaracteristica'] === "VELOCIDAD_TELCOHOME")
                                {
                                    $strDatosActuales .= "<br><b>" . $caracteristica['descripcionCaracteristica'] . ": </b>" 
                                                      . $caracteristica['valor'];
                                }
                            }
                            else
                            {
                                $strDatosActuales .= "<br><b>" . $caracteristica['descripcionCaracteristica'] . ": </b>" . $caracteristica['valor'];
                            }
                            $arrayProductoCaracteristicasValores[$caracteristica['descripcionCaracteristica']] = $caracteristica['valor'];
                            if($caracteristica['descripcionCaracteristica'] == 'CAPACIDAD1')
                            {
                                $capacidad1Original = $caracteristica['valor'];
                                if(!empty($arrayParametros['intCapacidad1']))
                                {
                                    $arrayProductoCaracteristicasValores[$caracteristica['descripcionCaracteristica']] = $arrayParametros['intCapacidad1'];
                                }                            
                            }
                            else if($caracteristica['descripcionCaracteristica'] == 'CAPACIDAD2')
                            {
                                $capacidad2Original = $caracteristica['valor'];
                                if(!empty($arrayParametros['intCapacidad2']))
                                {
                                    $arrayProductoCaracteristicasValores[$caracteristica['descripcionCaracteristica']] = $arrayParametros['intCapacidad2'];
                                }
                            }
                            else if($caracteristica['descripcionCaracteristica'] == 'VELOCIDAD' && !empty($arrayParametros['intVelocidad']))
                            {
                                $arrayProductoCaracteristicasValores[$caracteristica['descripcionCaracteristica']] = $arrayParametros['intVelocidad'];
                            }
                        endforeach;
                    }
                    //Se obtiene el producto del servico para saber que nivel y porcentaje de Autorizacion posee, para productos Obsoletos se valida con
                    //el producto al cual se va a realizar el cambio.
                    $productoEntity = null;
                    if(empty($arrayParametros['intIdprodObsoleto']))
                    {
                        $productoEntity = $admiProductoRepository->find($servicio['idProducto']);
                    }
                    else
                    {
                        $productoEntity = $admiProductoRepository->find($arrayParametros['intIdprodObsoleto']);
                        $strDatosNuevos .= "<b>Producto: </b> " . $productoEntity->getDescripcionProducto() . "<br>" . $strDatosNuevos;
                    }

                    $strNivelAprobacion = "N/A";                
                    $precioMinimo       = "N/A";

                    $strEstadoPrecio    = "N/A";
                    $strEstadoRadio     = "N/A";
                    $strEstadoIpccl2    = "N/A";

                    $strMensajePrecio   = "N/A";
                    $strMensajeRadio    = "N/A";
                    $strMensajeIpccl2   = "N/A";

                    $strModeloSwitch    = "";
                    $strPuertoSwitch    = "";
                    $strModeloCPE       = "";

                    if($tipoSolicitud->getDescripcionSolicitud() != 'CANCELACION')
                    {
                        // Validacion del Nivel de Aprobacion del Precio
                        $funcionPrecio  = $productoEntity->getFuncionPrecio();
                        $precioMinimo   = $this->evaluarFuncionPrecio($funcionPrecio, $arrayProductoCaracteristicasValores);

                        // Se verifica si existe un Rol para un nivel de autorizacion y su respectivo porcentaje
                        $InfoProductoNivel  = $infoProductoNivelRepository->findOneBy(array("productoId" => $productoEntity));
                        $precioMinimoRol    = null;
                        if($InfoProductoNivel)
                        {
                            $empresaRolId           = $InfoProductoNivel->getEmpresaRolId();
                            $empresaRol             = $infoEmpresaRolRepository->find($empresaRolId);
                            $rol                    = $admiRolRepository->find($empresaRol->getRolId());
                            $porcentajeDescuento    = $InfoProductoNivel->getPorcentajeDescuento();
                            //El Precio minimo permitido cambia si exite alguien designado a aprobarlo dentro del rango
                            $precioMinimoRol        = $precioMinimo - ($precioMinimo * ($porcentajeDescuento / 100));
                        }

                        if($tipoSolicitud->getDescripcionSolicitud() != 'DEMOS')
                        {                        
                            if($precioMinimoRol != null && $arrayParametros['intPrecio'] > $precioMinimoRol && 
                               $arrayParametros['intPrecio'] < $precioMinimo)
                            {
                                $strNivelAprobacion = "<b class='text-warning'>" . $rol->getDescripcionRol() . "</b>";
                                $strMensajePrecio   = "El valor es menor al precio mínimo, pero dentro del <b>" . $porcentajeDescuento . 
                                                        "%</b> del rango de aprobación del <b>" . $rol->getDescripcionRol() . "</b>";
                                $strEstadoPrecio    = "Pendiente";
                            }
                            else if($arrayParametros['intPrecio'] < $precioMinimo)
                            {
                                $strNivelAprobacion = "<b class='text-warning'>Gerente General</b>";
                                $strMensajePrecio   = "Valor menor al precio mínimo, aprobación de <b>Gerente General</b>";
                                $strEstadoPrecio    = "Pendiente";
                            }
                        }
                                                                        
                        // Validacion del Nivel de Aprobacion del Ancho de Banda de ser RADIO
                        if($tipoSolicitud->getDescripcionSolicitud() == 'CAMBIO PLAN' || $tipoSolicitud->getDescripcionSolicitud() == 'DEMOS')
                        {
                            $infoServicioTecnicoEntity = $infoServicioTecnico->findOneBy(array("servicioId" => $servicio['idServicio']));
                            if($infoServicioTecnicoEntity && $productoEntity->getEsEnlace() == 'SI'){

                                // Busqueda del Switch y puerto
                                $intElementoIdSwitch = $infoServicioTecnicoEntity->getElementoId();
                                if($intElementoIdSwitch)
                                {
                                    $infoElementoSwitch = $infoElemento->find($intElementoIdSwitch);
                                    if($infoElementoSwitch)
                                    {
                                        $strModeloSwitch = $infoElementoSwitch->getNombreElemento();                                    
                                    }
                                }
                                $intInterfaceElementoIdSwitch = $infoServicioTecnicoEntity->getInterfaceElementoId();
                                if($intInterfaceElementoIdSwitch)
                                {
                                    $infoInterfaceElementoSwitch = $infoInterfaceElemento->find($intInterfaceElementoIdSwitch);
                                    if($infoInterfaceElementoSwitch)
                                    {
                                        $strPuertoSwitch = $infoInterfaceElementoSwitch->getNombreInterfaceElemento();
                                    }
                                }

                                $admiTipoMedioEntity = $admiTipoMedio->find($infoServicioTecnicoEntity->getUltimaMillaId());                    
                                if($admiTipoMedioEntity)
                                {
                                    if($admiTipoMedioEntity->getNombreTipoMedio() == 'Radio')
                                    {
                                         if((!empty($arrayParametros['intCapacidad1']) && !empty($capacidad1Original) && 
                                            $arrayParametros['intCapacidad1'] > $capacidad1Original) || (!empty($arrayParametros['intCapacidad2']) &&
                                            !empty($capacidad2Original) && $arrayParametros['intCapacidad2'] > $capacidad2Original))
                                        {
                                            $strMensajeRadio    = "El aumento de capacidad debe ser autorizado por <b>Radio</b>";
                                            $strEstadoRadio     = "Pendiente";
                                        }
                                    }
                                }
                                // Validacion del Nivel de Aprobacion del Ancho de Banda para IPCCL2
                                if(!empty($servicio['nombreModeloElemento']))
                                {
                                    $strModeloCPE = $servicio['nombreModeloElemento'];                                
                                    if(!empty($servicio['capacidad1ModeloElemento']) && !empty($servicio['capacidad2ModeloElemento']))
                                    {
                                        $intModeloCapacidad1KPBSUmbral = $servicio['capacidad1ModeloElemento'] * self::PORC_UMBRAL;
                                        $intModeloCapacidad2KPBSUmbral = $servicio['capacidad2ModeloElemento'] * self::PORC_UMBRAL;

                                        if($arrayParametros['intCapacidad1'] > $intModeloCapacidad1KPBSUmbral || 
                                           $arrayParametros['intCapacidad2'] > $intModeloCapacidad2KPBSUmbral)
                                        {
                                            $strMensajeIpccl2 = "El aumento de capacidad debe ser autorizado por <b>IPCCL2</b>, superó el umbral "
                                                                . "permitido.";
                                            $strEstadoIpccl2  = "Pendiente";
                                        }
                                    }
                                    else
                                    {
                                        $strMensajeIpccl2 = "La capacidad debe ser autorizado por <b>IPCCL2</b>, No se encontraron valores en los "
                                                            . "Límites de Capacidad para este CPE.";
                                        $strEstadoIpccl2  = "Pendiente";
                                    }
                                }
                                else
                                {
                                    $strMensajeIpccl2 = "El aumento de capacidad debe ser autorizado por <b>IPCCL2</b>, No se encontró CPE asociado.";
                                    $strEstadoIpccl2  = "Pendiente";
                                }
                                
                                //Se verifica factibilidad de cambio de plan en Servicios Backups
                                //Si la capacidad del PRINCIPAL es correcta respecto al UMBRAL verificar lo mismo a nivel de BACKUPS
                                $arrayParametrosValidarBackups                         = array();
                                $arrayParametrosValidarBackups['intIdServicio']        = $servicio['idServicio'];
                                $arrayParametrosValidarBackups['intCapacidadUnoNueva'] = $arrayParametros['intCapacidad1'];
                                $arrayParametrosValidarBackups['intCapacidadDosNueva'] = $arrayParametros['intCapacidad2'];
                                $arrayParametrosValidarBackups['floatUmbral']          = self::PORC_UMBRAL;
                                $arrayRespuesta = $serviceTecnico->getValidacionBackupPorCambioPlan($arrayParametrosValidarBackups);

                                if(!empty($arrayRespuesta['strMensajeIpccl2']))
                                {
                                    if($strMensajeIpccl2 == 'N/A')
                                    {
                                        $strMensajeIpccl2 = $arrayRespuesta['strMensajeIpccl2'];
                                    }
                                    else
                                    {
                                        $strMensajeIpccl2 .= "<br>".$arrayRespuesta['strMensajeIpccl2'];
                                    }
                                }
                                
                                if(!empty($arrayRespuesta['strMensajeRadio']))
                                {
                                    if($strMensajeRadio == 'N/A')
                                    {
                                        $strMensajeRadio = $arrayRespuesta['strMensajeRadio'];
                                    }
                                    else
                                    {
                                        $strMensajeRadio .= "<br>".$arrayRespuesta['strMensajeRadio'];
                                    }
                                }
                            }
                        }                    
                    }

                    $strNivelAprobacionTable = "<table class='sm_table_interior'>"
                                            . "<tr><th><div class='icon_precio'>Precio: </th><td class='color-warning-" 
                                            . ($strEstadoPrecio == 'N/A' ? 'ok' : 'precio') . "'>" . $strEstadoPrecio . "</div></td></tr>"
                                            . "<tr><th><div class='icon_radio'>Radio: </th><td class='color-warning-" 
                                            . ($strEstadoRadio == 'N/A' ? 'ok' : 'radio') . "'>" . $strEstadoRadio . "</div></td></tr>"
                                            . "<tr><th><div class='icon_ipccl2'>IPCCL2: </th><td class='color-warning-" 
                                            . ($strEstadoIpccl2 == 'N/A' ? 'ok' : 'ipccl2') . "'>" . $strEstadoIpccl2 . "</div></td></tr>"
                                            . "</table>";

                    $strMensajeTable = "<ul>"
                                        . "<li class='icon_precio color-warning-" . ($strEstadoPrecio == 'N/A' ? 'ok' : 'precio') . "'>" 
                                        . $strMensajePrecio . "</li>"
                                        . "<li class='icon_radio color-warning-" . ($strEstadoRadio == 'N/A' ? 'ok' : 'radio') . "'>" 
                                        . $strMensajeRadio . "</li>"
                                        . "<li class='icon_ipccl2 color-warning-" . ($strEstadoIpccl2 == 'N/A' ? 'ok' : 'ipccl2') . "'>" 
                                        . $strMensajeIpccl2 . "</li>"
                                        . "</ul>";

                    $strDescripcionServicio = "<b>Descripción Servicio: </b><br>" . strip_tags ($servicio['descripcionServicio']);
                    $strDescripcionServicio .=  "<br><b>Producto: </b>" . $servicio['descripcionProducto'];
                    $strDescripcionServicio .=  "<br><b>Login Aux: </b><br>" . $servicio['loginAux'];                
                    $strDescripcionServicio .=  "<br><b>Última Milla: </b><br>" . $servicio['nombreUltimaMilla'];
                    $strDescripcionServicio .=  "<br><b>Estado: </b><br>" . $servicio['estado'];
                    if($tipoSolicitud->getDescripcionSolicitud() == 'CAMBIO PLAN' || $tipoSolicitud->getDescripcionSolicitud() == 'DEMOS')
                    {
                        $strDescripcionServicio .=  "<br><b>Switch: </b><br>" . $strModeloSwitch;
                        $strDescripcionServicio .=  "<br><b>Puerto Switch: </b><br>" . $strPuertoSwitch;
                        $strDescripcionServicio .=  "<br><b>Modelo CPE: </b><br>" . $strModeloCPE;
                    }

                    $precioMinimo = ($precioMinimo != "N/A") ? "$ " . $precioMinimo : $precioMinimo;
                    
                    if(!empty($arrayParametros['intIdMotivo']))
                    {
                        $admiMotivoEntity = $admiMotivo->find($arrayParametros['intIdMotivo']);
                        if($admiMotivoEntity)
                        {
                            $strDatosNuevos .= "<br><b>Motivo: </b> " . $admiMotivoEntity->getNombreMotivo();
                        }
                    }
                    if(!empty($arrayParametros['strDescripcion']))
                    {
                        $strDatosNuevos .= "<br><b>Descripción: </b> " . $arrayParametros['strDescripcion'];
                    }
                    
                    if(!empty($arrayParametros['strFechaPlanificada']) && !empty($arrayParametros['strHora']))
                    {
                        $strDatosNuevos .= "<br><b>Ejecución Planificada: </b> " . $arrayParametros['strFechaPlanificada'] . " " . 
                                           $arrayParametros['strHora'];
                    }
                    

                    $arraySercicios[] = array(
                                                'intIdServicio'             => $servicio['idServicio'],
                                                'strDescripcionServicio'    => $strDescripcionServicio,
                                                'intIdPunto'                => $servicio['idPunto'],
                                                'strLogin'                  => $servicio['login'],
                                                'intIdProdructo'            => $servicio['idProducto'],
                                                'strdescripcionProducto'    => $servicio['descripcionProducto'],
                                                'strPrecioVenta'            => $servicio['precioVenta'],
                                                'strPrecioMinimo'           => $precioMinimo,
                                                'strestado'                 => $servicio['estado'],
                                                'strDatosActuales'          => $strDatosActuales,
                                                'strDatosNuevos'            => $strDatosNuevos,
                                                'strNivelAprobacion'        => $strNivelAprobacionTable,
                                                'strMensaje'                => $strMensajeTable
                                            );
                }
            endforeach;
        }

        if($total > 0)
        {
            $data = json_encode($arraySercicios);
            $objJson = '{"total":"' . $total . '","jsonServiciosSeleccionados":' . $data . '}';
        }
        else
        {
            $objJson = '{"total":"0","jsonPuntosServicios":[]}';
        }
        $respuesta->setContent($objJson);
        return $respuesta;
    }
    
    private function getKBPS($intCapacidad, $strUnidad)
    {
        $intCapacidadResultado = 0;
        switch($strUnidad)
        {
            case 'BPS':
                $intCapacidadResultado = $intCapacidad / 1024;
                break;
            case 'KBPS':
                $intCapacidadResultado = $intCapacidad;
                break;
            case 'MBPS':
                $intCapacidadResultado = $intCapacidad * 1024;
                break;
            case 'GBPS':
                $intCapacidadResultado = $intCapacidad * 1024 * 1024;
                break;
            default:
                 $intCapacidadResultado = $intCapacidad;
                break;
        }
        return  $intCapacidadResultado;
    }

    /**
     * evaluarFuncionPrecio, Evalua la funcion de precio en base a unos parametros dados y retorna el precio
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 01-04-2016
     * 
     * @param string $funcionPrecio Funcion de precio a evaluar
     * @param array $arrayProductoCaracteristicasValores Arreglo con los valores a ser reemplazados
     * @return int Retorna el precio obtenido de la evaluacion
     * 
     * @author Modificado: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.1 08-07-2016 - Se reemplazan las funciones JS usadas en la funcion de precio para que php pueda evaluarlas
     *                           correctamente
     * 
     * @author Modificado: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.2 05-08-2016 - Se verifica si el ultimo caracter de la funcion de precio es numerico para añadir ';' de ser necesario
     * 
     */
    private function evaluarFuncionPrecio($funcionPrecio, $arrayProductoCaracteristicasValores)
    {
        $precio             = 0;        
        $arrayFunctionJs    = array('Math.ceil','Math.floor','Math.pow');
        $arrayFunctionPhp   = array('ceil','floor','pow');
        $funcionPrecio      = str_replace($arrayFunctionJs, $arrayFunctionPhp, $funcionPrecio);
        
        foreach($arrayProductoCaracteristicasValores as $clave => $valor)
        {
            $funcionPrecio = str_replace("[" . $clave . "]", $valor, $funcionPrecio);
        }
        $funcionPrecio      = str_replace('PRECIO', '$precio', $funcionPrecio);
        $digitoVerificacion = substr($funcionPrecio, -1, 1);
        if(is_numeric($digitoVerificacion))
        {
            $funcionPrecio = $funcionPrecio . ";";
        }
        eval($funcionPrecio);
        return $precio;
    }

    /**
     * deleteAction, elimina logicamente a la solicitud masiva y a todos sus detalles
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 12-04-2016
     *
     */
    public function deleteAction()
    {
        $boolCambioPrecio = true;
        $objRequest = $this->getRequest();
        $objSession = $objRequest->getSession();
        $objReturnResponse = new ReturnResponse();

        $emComercial = $this->getDoctrine()->getManager("telconet");
        $InfoDetalleSolicitud = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud');
        $infoDetalleSolCaract = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract');

        $arrayParametros['intIdSolicitud'] = $objRequest->get('intIdSolicitud');
        $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT);
        $emComercial->getConnection()->beginTransaction();
        try
        {
            //Valida que el parámetro no se enviado vacío, caso contrario crea una excepción.
            if(!empty($arrayParametros['intIdSolicitud']))
            {
                //Busca el parámetro enviado en el request $arrayParametros['intIdParametroCab'] en la entidad AdmiParametroCab.
                $entityDetalleSolicitudCab = $InfoDetalleSolicitud->find($arrayParametros['intIdSolicitud']);

                //Valida que el objeto no sea nulo.
                if($entityDetalleSolicitudCab)
                {
                    $entityDetalleSolicitudCab->setEstado('Eliminada');
                    $emComercial->persist($entityDetalleSolicitudCab);
                    $emComercial->flush();

                    /* Historial de la Cabecera de la Solicitud */
                    $entityInfoDetalleSolHistCab = new InfoDetalleSolHist();
                    $entityInfoDetalleSolHistCab->setDetalleSolicitudId($entityDetalleSolicitudCab);
                    $entityInfoDetalleSolHistCab->setEstado($entityDetalleSolicitudCab->getEstado());
                    $entityInfoDetalleSolHistCab->setFeCreacion(new \DateTime('now'));
                    $entityInfoDetalleSolHistCab->setUsrCreacion($objSession->get('user'));
                    $emComercial->persist($entityInfoDetalleSolHistCab);
                    $emComercial->flush();

                    $arrayCaracteristicasSolicitud = $infoDetalleSolCaract->findBy(array("detalleSolicitudId" => $entityDetalleSolicitudCab));
                    if(count($arrayCaracteristicasSolicitud) > 0)
                    {
                        foreach($arrayCaracteristicasSolicitud as $caracteristicasSolicitud):
                            $caracteristicasSolicitud->setEstado('Eliminada');
                            $caracteristicasSolicitud->setFeUltMod(new \DateTime('now'));
                            $caracteristicasSolicitud->setUsrUltMod($objSession->get('user'));
                            $emComercial->persist($caracteristicasSolicitud);
                            $emComercial->flush();
                        endforeach;
                    }

                    $arrayParametros = array();
                    $arrayParametros['intIdEmpresa'] = ($objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : "");
                    $arrayParametros['intIdPadre'] = $entityDetalleSolicitudCab->getId();
                    $arrayParametros['boolMasivas'] = 'false';
                    $resultado = $InfoDetalleSolicitud->findSolicitudes($arrayParametros);
                    $solicitudes = $resultado['registros'];
                    $total = $resultado['total'];

                    //Si existen comprobantes
                    if($total > 0)
                    {
                        foreach($solicitudes as $solicitud):
                            $entityDetalleSolicitudDet = $InfoDetalleSolicitud->find($solicitud['idDetalleSolicitud']);
                            if($entityDetalleSolicitudDet)
                            {
                                $entityDetalleSolicitudDet->setEstado('Eliminada');
                                $emComercial->persist($entityDetalleSolicitudDet);
                                $emComercial->flush();

                                /* Historial de los Detalles de la Solicitud */
                                $entityInfoDetalleSolHistDet = new InfoDetalleSolHist();
                                $entityInfoDetalleSolHistDet->setDetalleSolicitudId($entityDetalleSolicitudDet);
                                $entityInfoDetalleSolHistDet->setEstado($entityDetalleSolicitudDet->getEstado());
                                $entityInfoDetalleSolHistDet->setFeCreacion(new \DateTime('now'));
                                $entityInfoDetalleSolHistDet->setUsrCreacion($objSession->get('user'));
                                $emComercial->persist($entityInfoDetalleSolHistDet);
                                $emComercial->flush();
                            }
                        endforeach;
                    }
                }
                else
                {
                    $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                    $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . ' No se encontró la Solicitud a ser Eliminada.');
                }
            }
            else
            {
                $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . ' No esta enviando el código de la solicitud.');
            }
            $emComercial->getConnection()->commit();
            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);
        }
        catch(\Exception $ex)
        {
            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . " " . $ex->getMessage());
            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
        }
        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');
        $objResponse->setContent(json_encode((array) $objReturnResponse));
        return $objResponse;
    }

    /**
     * crearSolicitudMasivaAction, Crea registros en la ADMI_DETALLE_SOLICITUD basado en la seleccion de servicios
     * a ser cambiados de un tipo de solicitud especificado.
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 06-04-2016
     * 
     * @author Modificado: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.1 28-06-2016 - Se modifico la funcion para guardar el archivo
     * 
     * @author Modificado: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.2 11-07-2016 - Notificacion via correo electronico
     * 
     * @author Modificado: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.3 21-07-2016 - Validaciones de Radio e IPCCL2 para productos EsEnlace Si
     * 
     * @author Modificado: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.4 01-08-2016 -Validaciones de IPCCL2 se optimizó la validacion de BW para el CPE
     * 
     * @author Modificado: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.5 11-08-2016 - Se cambio el esquema de almacenamiento de las caracteristicas nuevas por detalle
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.6 28-09-2016 - Se cambian los destinarios de la notificación al crear la solicitud masiva. 
     *                           En lugar de que sean los correos asociados directamente al punto, se modifica que los destinatarios
     *                           sean únicamente los correos asociados a los contactos comerciales del punto
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.7 31-10-2016 - Se agregan los correos del vendedor del servicio y del vendedor del punto como destinarios de las notificaciones al 
     *                           crear la solicitud masiva
     * 
     * @author Modificado: Allan Suarez <arsuarez@telconet.ec>
     * @version 1.8 22-05-2017 - Se valida que si el punto al cual pertenece el servicio a cancelar y este es el ultimo Activo y existe al menos
     *                           un Servicio Rechazado no permitir crear la Solicitud Masiva hasta gestionen dicho servicio
     * 
     * @author Modificado: Allan Suarez <arsuarez@telconet.ec>
     * @version 1.9 05-04-2017 - Se llama metodo para crear solicitudes de cambio de plan masivo para los servicios backups relacionados a un 
     *                           Principal en caso de existir
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.8 22-05-2017 - Se realizan ajustes para mostrar si la solicitud de demos es facturable y se pasa a estado aprobada directamente
     *                           porque no necesita aprobacion comercial.
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.9 08-03-2017 - Se obtiene el valor de la VELOCIDAD de los servicios Internet Small Business para agregarlo como característica
     *                           de la solicitud de cambio de plan
     * 
     * @author Modificado: David León <mdleon@telconet.ec>
     * @version 2.0 05-07-2022 - Eliminamos archivo del directorio luego de enviarlo.
     * 
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 2.1 17-08-2022 - Envío de notificación a la asistente, vendedor y subgerente. 
     *                           Los cambios solo aplican para las solicitudes de cambio de plan y precio.
     *
     * @Secure(roles="ROLE_346-3")
     */
    public function createAction()
    {
        $boolCambioPrecio   = true;
        $objRequest         = $this->getRequest();
        $objSession         = $objRequest->getSession();
        $objReturnResponse  = new ReturnResponse();
        
        $intIdEmpresa       = $objSession->get('idEmpresa');
        $strPrefijoEmpresa  = $objSession->get('prefijoEmpresa');

        $strIpClient            = $objRequest->getClientIp();
        $strUser                = $objSession->get("user");
        $serviceUtil            = $this->get('schema.Util');
        $serviceTecnico         = $this->get('tecnico.InfoServicioTecnico');
        $emComercial            = $this->getDoctrine()->getManager("telconet");
        $emGeneral              = $this->getDoctrine()->getManager('telconet_general');        
        $emInfraestructura      = $this->getDoctrine()->getManager("telconet_infraestructura");
        $admiCaracteristica     = $emComercial->getRepository('schemaBundle:AdmiCaracteristica');
        $infoServicio           = $emComercial->getRepository('schemaBundle:InfoServicio');
        $infoProductoNivel      = $emComercial->getRepository('schemaBundle:InfoProductoNivel');
        $admiTipoSolicitud      = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud');
        $infoServicioTecnico    = $emComercial->getRepository('schemaBundle:InfoServicioTecnico');
        $admiTipoMedio          = $emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio');
        $admiProducto           = $emComercial->getRepository('schemaBundle:AdmiProducto');
        $objServicioRepository  = $emComercial->getRepository('schemaBundle:InfoServicio');
        //Permiso de AutoAprobacion de solicitud masiva.
        $boolAutoAprobacion     = $this->get('security.context')->isGranted('ROLE_347-4038');
        $serviceEnvioPlantilla  = $this->get('soporte.EnvioPlantilla');
        $arrayParametros                        = array();
        $strEstadoDetalleSolicitud              = "Pendiente";
        $arrayParametros['intIdTipoSolicitud']  = $objRequest->get('intIdTipoSolicitud');
        $arrayParametros['strIdServicios']      = $objRequest->get('strIdServicios');
        $arrayParametros['strRutaArchivo']      = $objRequest->get('strRutaArchivo');
        $objDetalleCaractacteristicasNuevas     = json_decode($objRequest->get('strJsonDetalleCaract'));
        
        $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT);
        $emComercial->getConnection()->beginTransaction();
        try
        {
            //Valida que el nombre del parámetro no se enviado vacío, caso contrario crea una excepción.
            if(empty($arrayParametros['strIdServicios']))
            {
                throw new \Exception('No se ha seleccionado Servicios para la Soicitud Masiva.');
            }
            if(empty($arrayParametros['intIdTipoSolicitud']))
            {
                throw new \Exception('Debe Seleccionar un Tipo de Solicitud.');
            }
            if(!$objDetalleCaractacteristicasNuevas)
            {
                throw new \Exception('Los Servicios seleccionados no poseen caracteristicas nuevas.');
            }
            
            $tipoSolicitud = $admiTipoSolicitud->find($arrayParametros['intIdTipoSolicitud']);
            if($tipoSolicitud == null)
            {
                throw new \Exception('No se encontro el Tipo de Solicitud a Crear.');
            }

            if(strrpos($tipoSolicitud->getDescripcionSolicitud(), 'CAMBIO PLAN') === false 
                && strrpos($tipoSolicitud->getDescripcionSolicitud(), 'CAMBIO PRECIO') === false
                && strrpos($tipoSolicitud->getDescripcionSolicitud(), 'DEMOS') === false) {
                $boolCambioPrecio = false;
            }

            //Instacia un nuevo objeto de la entidad InfoDetalleSolicitud (Cabecera)
            $entityInfoDetalleSolicitudCab = new InfoDetalleSolicitud();
            $entityInfoDetalleSolicitudCab->setTipoSolicitudId($tipoSolicitud);
            $entityInfoDetalleSolicitudCab->setEstado('Pendiente');
            $entityInfoDetalleSolicitudCab->setFeCreacion(new \DateTime('now'));
            $entityInfoDetalleSolicitudCab->setUsrCreacion($objSession->get('user'));
            $emComercial->persist($entityInfoDetalleSolicitudCab);
            $emComercial->flush();

            /* Historial de la Cabecera de la Solicitud */
            $entityInfoDetalleSolHistCab = new InfoDetalleSolHist();
            $entityInfoDetalleSolHistCab->setDetalleSolicitudId($entityInfoDetalleSolicitudCab);
            $entityInfoDetalleSolHistCab->setEstado($entityInfoDetalleSolicitudCab->getEstado());
            $entityInfoDetalleSolHistCab->setObservacion($entityInfoDetalleSolicitudCab->getObservacion());
            $entityInfoDetalleSolHistCab->setMotivoId($entityInfoDetalleSolicitudCab->getMotivoId());
            $entityInfoDetalleSolHistCab->setFeCreacion(new \DateTime('now'));
            $entityInfoDetalleSolHistCab->setUsrCreacion($entityInfoDetalleSolicitudCab->getUsrCreacion());
            $emComercial->persist($entityInfoDetalleSolHistCab);
            $emComercial->flush();
            
            // Grabar el Atributo Archivo asociado a la cabecera de la SM
            if(!empty($arrayParametros['strRutaArchivo']))
            {
                $entityCaracteristica = $admiCaracteristica->findOneBy(array("descripcionCaracteristica" => 'Archivo'));
                $entityInfoDetalleSolCaract = new InfoDetalleSolCaract();
                $entityInfoDetalleSolCaract->setDetalleSolicitudId($entityInfoDetalleSolicitudCab);
                $entityInfoDetalleSolCaract->setCaracteristicaId($entityCaracteristica);
                $entityInfoDetalleSolCaract->setValor($arrayParametros['strRutaArchivo']);
                $entityInfoDetalleSolCaract->setEstado('Pendiente');
                $entityInfoDetalleSolCaract->setFeCreacion(new \DateTime('now'));
                $entityInfoDetalleSolCaract->setUsrCreacion($objSession->get('user'));
                $emComercial->persist($entityInfoDetalleSolCaract);
                $emComercial->flush();
                
                $arrayArchivo = explode('/', $arrayParametros['strRutaArchivo']);
                $countArray = count($arrayArchivo);
                $nuevoNombre = $arrayArchivo[$countArray - 1];
                
                $entityInfoDetalleSolHistCab = new InfoDetalleSolHist();
                $entityInfoDetalleSolHistCab->setDetalleSolicitudId($entityInfoDetalleSolicitudCab);
                $entityInfoDetalleSolHistCab->setEstado($entityInfoDetalleSolicitudCab->getEstado());
                $entityInfoDetalleSolHistCab->setObservacion("Se Adjunto un Documento : " . $nuevoNombre);
                $entityInfoDetalleSolHistCab->setFeCreacion(new \DateTime('now'));
                $entityInfoDetalleSolHistCab->setUsrCreacion($entityInfoDetalleSolicitudCab->getUsrCreacion());
                $emComercial->persist($entityInfoDetalleSolHistCab);
                $emComercial->flush();
            }

            /* Parametros para evaluar Descuentos en las Solicitudes */
            $arrayIdServicios   = explode(",", $arrayParametros['strIdServicios']);
            $servicioCliente    = null;
            $arrayTo            = array();
            $arraySinCancelar   = array();
            $intNumDetalles     = 0;
            foreach($arrayIdServicios as $idServicio):
                $arrayParametros = array();
                $arrayParametros = $this->getCaracteristicasNuevasXServicio($objDetalleCaractacteristicasNuevas->arrayData, $idServicio);
                if(count($arrayParametros) > 0)
                {
                    $servicio = $infoServicio->find($idServicio);
                    if($servicioCliente == null)
                    {
                        $servicioCliente = $servicio;
                    }
                    
                    if($tipoSolicitud->getDescripcionSolicitud() == 'CANCELACION')
                    {
                        $arrayParametrosSeCancela                = array();
                        $arrayParametrosSeCancela['objServicio'] = $servicio;
                        $boolSeCancela                           = $serviceTecnico->validarCancelacionServicio($arrayParametrosSeCancela);

                        if(!$boolSeCancela)
                        {
                            $strInfoServicio = '';

                            if($servicio->getLoginAux())
                            {
                                $strInfoServicio = $servicio->getLoginAux();
                            }
                            else
                            {
                                $strInfoServicio = 'Servicio con Producto : '.$servicio->getProductoId()->getDescripcionProducto();
                            }

                            $arraySinCancelar[] = $strInfoServicio;
                            continue;
                        }

                        $intNumDetalles++;
                    }
                    
                    //Instacia un nuevo objeto de la entidad InfoDetalleSolicitud para Detalle
                    $entityInfoDetalleSolicitudDet = new InfoDetalleSolicitud();
                    $entityInfoDetalleSolicitudDet->setServicioId($servicio);
                    $entityInfoDetalleSolicitudDet->setTipoSolicitudId($tipoSolicitud);
                    if(!empty($arrayParametros['intIdMotivo']))
                    {
                        $entityInfoDetalleSolicitudDet->setMotivoId($arrayParametros['intIdMotivo']);
                    }
                    $entityInfoDetalleSolicitudDet->setObservacion($arrayParametros['strDescripcion']);

                    //Se setea aprobada porque del Demo no necesita aprobacion comercial
                    if($tipoSolicitud->getDescripcionSolicitud() == 'DEMOS')
                    {
                        $strEstadoDetalleSolicitud = "Aprobada";
                    }
                    else
                    {
                        $strEstadoDetalleSolicitud = "Pendiente";
                    }
                    $entityInfoDetalleSolicitudDet->setEstado($strEstadoDetalleSolicitud);
                    $entityInfoDetalleSolicitudDet->setFeCreacion(new \DateTime('now'));
                    $entityInfoDetalleSolicitudDet->setUsrCreacion($objSession->get('user'));
                    $emComercial->persist($entityInfoDetalleSolicitudDet);
                    $emComercial->flush();
                    
                    // Se obtiene el conjunto de valores de los cambios a realizar para despues asignarlos 
                    // a la cabecera como caracteristicas de la misma 
                    $arrayDetalleSolicitudCaract = array();
                    if(!empty($arrayParametros['strEsFacturable']))
                    {
                        $arrayDetalleSolicitudCaract["Facturable"] = $arrayParametros['strEsFacturable'];
                    }
                    if(!empty($arrayParametros['intDuracionDemo']))
                    {
                        $arrayDetalleSolicitudCaract["Duracion Demo"]         = $arrayParametros['intDuracionDemo'];
                        $arrayDetalleSolicitudCaract["Cancelacion Demo"]      = "N";
                        $arrayDetalleSolicitudCaract["Envio de Notificacion"] = "N";
                        $arrayDetalleSolicitudCaract["Cambio Equipo Demo"]    = "N";
                    }
                    if(!empty($arrayParametros['intPrecio']) || $arrayParametros['intPrecio'] >= 0)
                    {
                        $arrayDetalleSolicitudCaract["Precio"] = $arrayParametros['intPrecio'];
                    }
                    if(!empty($arrayParametros['intCapacidad2']))
                    {
                        $arrayDetalleSolicitudCaract["CAPACIDAD2"] = $arrayParametros['intCapacidad2'];
                    }
                    if(!empty($arrayParametros['intCapacidad1']))
                    {
                        $arrayDetalleSolicitudCaract["CAPACIDAD1"] = $arrayParametros['intCapacidad1'];
                    }
                    if(!empty($arrayParametros['intVelocidad']))
                    {
                        $arrayDetalleSolicitudCaract["VELOCIDAD"] = $arrayParametros['intVelocidad'];
                    }
                    if(!empty($arrayParametros['intIdprodObsoleto']))
                    {
                        $arrayDetalleSolicitudCaract["Producto Obsoleto"] = $arrayParametros['intIdprodObsoleto'];
                    }

                    // Se guardan las caracteristicas del la solicitud cabecera
                    if($boolCambioPrecio)
                    {
                        foreach($arrayDetalleSolicitudCaract as $clave => $valor)
                        {                          
                            $caracteristica             = $admiCaracteristica->findOneBy(array("descripcionCaracteristica" => $clave));
                            $entityInfoDetalleSolCaract = new InfoDetalleSolCaract();
                            $entityInfoDetalleSolCaract->setDetalleSolicitudId($entityInfoDetalleSolicitudDet);
                            $entityInfoDetalleSolCaract->setCaracteristicaId($caracteristica);
                            $entityInfoDetalleSolCaract->setValor($valor);
                            $entityInfoDetalleSolCaract->setEstado('Pendiente');
                            $entityInfoDetalleSolCaract->setFeCreacion(new \DateTime('now'));
                            $entityInfoDetalleSolCaract->setUsrCreacion($objSession->get('user'));
                            $emComercial->persist($entityInfoDetalleSolCaract);
                            $emComercial->flush();
                        }
                    }
                    
                    //Se obtiene el producto del servico para saber que nivel y porcentaje de Autorizacion posee, para productos Obsoletos se valida 
                    //con el producto al cual se va a realizar el cambio.
                    $producto = null;                
                    if(empty($arrayParametros['intIdprodObsoleto']))
                    {
                        $producto = $servicio->getProductoId();
                    }
                    else
                    {
                        $producto = $admiProducto->find($arrayParametros['intIdprodObsoleto']);
                    }

                    $InfoProductoNivel   = $infoProductoNivel->findOneBy(array("productoId" => $producto));
                    $porcentajeDescuento = null;
                    if($InfoProductoNivel)
                    {
                        $empresaRolId           = $InfoProductoNivel->getEmpresaRolId();
                        $porcentajeDescuento    = $InfoProductoNivel->getPorcentajeDescuento();
                    }
                    
                    $feFinPlan = null;
                    if(!empty($arrayParametros['strFechaPlanificada']) && !empty($arrayParametros['strHora']))
                    {
                        $feFinPlan = new \DateTime($arrayParametros['strFechaPlanificada'] . ' ' . $arrayParametros['strHora']);
                    }

                    /* Historial de la Cabecera de la Solicitud */
                    $entityInfoDetalleSolHistDet = new InfoDetalleSolHist();
                    $entityInfoDetalleSolHistDet->setDetalleSolicitudId($entityInfoDetalleSolicitudDet);
                    $entityInfoDetalleSolHistDet->setEstado($entityInfoDetalleSolicitudDet->getEstado());
                    $entityInfoDetalleSolHistDet->setObservacion($entityInfoDetalleSolicitudDet->getObservacion());
                    $entityInfoDetalleSolHistDet->setMotivoId($entityInfoDetalleSolicitudDet->getMotivoId());
                    if($feFinPlan != null)
                    {
                        $entityInfoDetalleSolHistDet->setFeFinPlan($feFinPlan);
                    }
                    $entityInfoDetalleSolHistDet->setFeCreacion(new \DateTime('now'));
                    $entityInfoDetalleSolHistDet->setUsrCreacion($entityInfoDetalleSolicitudDet->getUsrCreacion());

                    $emComercial->persist($entityInfoDetalleSolHistDet);
                    $emComercial->flush();
                    // Se obtiene la caracteristica que servira para asociar cada detalle con la cabecera
                    $caracteristica = $admiCaracteristica->findOneBy(array("descripcionCaracteristica" => "Referencia Solicitud"));
                    // Se guarda la referencia de la cabecera como caracteristica de los detalles
                    $entityInfoDetalleSolCaract = new InfoDetalleSolCaract();
                    $entityInfoDetalleSolCaract->setDetalleSolicitudId($entityInfoDetalleSolicitudDet);
                    $entityInfoDetalleSolCaract->setEstado('Pendiente');
                    $entityInfoDetalleSolCaract->setCaracteristicaId($caracteristica);
                    $entityInfoDetalleSolCaract->setValor($entityInfoDetalleSolicitudCab->getId());
                    $entityInfoDetalleSolCaract->setFeCreacion(new \DateTime('now'));
                    $entityInfoDetalleSolCaract->setUsrCreacion($entityInfoDetalleSolicitudDet->getUsrCreacion());

                    $emComercial->persist($entityInfoDetalleSolCaract);
                    $emComercial->flush();

                    $strEstadoCambioPrecio  = 'N/A';
                    $strEstadoRadio         = 'N/A';
                    $strEstadoIpccl2        = 'N/A';
                    // Para Cambio de Precio se efectua la respectiva validacion de niveles de aprobacion para segun esto
                    // asignar los estados para que cada area sepa que debe aprobar, rechazar o si no aplica.
                    if($boolCambioPrecio)
                    {
                        $capacidad1Original = "";
                        $capacidad2Original = "";

                        $arrayParametrosCaracteristica                  = array();
                        $arrayParametrosCaracteristica['intIdServicio'] = $servicio->getId();

                        $arrayResultadoCaract       = $infoServicio->findServiciosProductoCaracteristicas($arrayParametrosCaracteristica);
                        $caracteristicasServicio    = $arrayResultadoCaract['registros'];

                        $arrayProductoCaracteristicasValores = array();
                        if(count($caracteristicasServicio) > 0)
                        {
                            foreach($caracteristicasServicio as $caracteristica):
                                $descripcionCaract                                       = $caracteristica['descripcionCaracteristica'];
                                $arrayProductoCaracteristicasValores[$descripcionCaract] = $caracteristica['valor'];

                                if($descripcionCaract == 'CAPACIDAD1')
                                {
                                    $capacidad1Original = $caracteristica['valor'];
                                } else if($descripcionCaract == 'CAPACIDAD2')
                                {
                                    $capacidad2Original = $caracteristica['valor'];
                                }                        

                                if(!empty($arrayDetalleSolicitudCaract[$descripcionCaract]))
                                {
                                    $arrayProductoCaracteristicasValores[$descripcionCaract] = $arrayDetalleSolicitudCaract[$descripcionCaract];
                                }
                            endforeach;
                        }
                        $funcionPrecio  = $producto->getFuncionPrecio();
                        $precioMinimo   = $this->evaluarFuncionPrecio($funcionPrecio, $arrayProductoCaracteristicasValores);
                        
                        if(($tipoSolicitud->getDescripcionSolicitud() != 'DEMOS') && ($arrayDetalleSolicitudCaract["Precio"] < $precioMinimo))
                        {
                            $strEstadoCambioPrecio = 'Pendiente';
                        }
                        
                        if(!empty($arrayDetalleSolicitudCaract['CAPACIDAD1']) && !empty($arrayDetalleSolicitudCaract['CAPACIDAD2']))
                        {
                            // Validacion del Nivel de Aprobacion del Ancho de Banda de ser RADIO                    
                            $infoServicioTecnicoEntity = $infoServicioTecnico->findOneBy(array("servicioId" => $servicio->getId()));
                            if($infoServicioTecnicoEntity && $producto->getEsEnlace() == 'SI')
                            {
                                $admiTipoMedioEntity = $admiTipoMedio->find($infoServicioTecnicoEntity->getUltimaMillaId());                    
                                if($admiTipoMedioEntity)
                                {
                                    if($admiTipoMedioEntity->getNombreTipoMedio() == 'Radio')
                                    {
                                         if((!empty($arrayDetalleSolicitudCaract['CAPACIDAD1']) && !empty($capacidad1Original) && 
                                             $arrayDetalleSolicitudCaract['CAPACIDAD1'] > $capacidad1Original) || 
                                             (!empty($arrayDetalleSolicitudCaract['CAPACIDAD2']) && !empty($capacidad2Original) && 
                                             $arrayDetalleSolicitudCaract['CAPACIDAD2'] > $capacidad2Original))
                                        {
                                            $strEstadoRadio = "Pendiente";
                                        }
                                    }
                                }

                                // Validacion del Nivel de Aprobacion del Ancho de Banda para IPCCL2
                                $arrayServicioResult = $infoServicio->findServicios(array("intIdServicio" => $idServicio));
                                $arrayServicio       = $arrayServicioResult['registros'];

                                if(!empty($arrayServicio) && count($arrayServicio) > 0 && !empty($arrayServicio[0]['nombreModeloElemento']))
                                {
                                    if(!empty($arrayServicio[0]['capacidad1ModeloElemento']) && 
                                       !empty($arrayServicio[0]['capacidad2ModeloElemento']))
                                    {
                                        $intModeloCapacidad1KPBSUmbral = $arrayServicio[0]['capacidad1ModeloElemento'] * self::PORC_UMBRAL;
                                        $intModeloCapacidad2KPBSUmbral = $arrayServicio[0]['capacidad2ModeloElemento'] * self::PORC_UMBRAL;

                                        if($arrayDetalleSolicitudCaract['CAPACIDAD1'] > $intModeloCapacidad1KPBSUmbral || 
                                           $arrayDetalleSolicitudCaract['CAPACIDAD2'] > $intModeloCapacidad2KPBSUmbral)
                                        {
                                            $strEstadoIpccl2  = "Pendiente";
                                        }
                                    }
                                    else
                                    {
                                        $strEstadoIpccl2  = "Pendiente";
                                    }
                                }
                                else
                                {
                                    $strEstadoIpccl2  = "Pendiente";
                                }
                            }
                        }
                    }                                        
                    // Se crea el Estado Cambio de Precio para la Solicitud Detalle
                    $caracteristica = $admiCaracteristica->findOneBy(array("descripcionCaracteristica" => "Estado Cambio Precio"));
                    if($caracteristica)
                    {
                        $entityInfoDetalleSolCaract = new InfoDetalleSolCaract();
                        $entityInfoDetalleSolCaract->setDetalleSolicitudId($entityInfoDetalleSolicitudDet);
                        $entityInfoDetalleSolCaract->setEstado('Pendiente');
                        $entityInfoDetalleSolCaract->setCaracteristicaId($caracteristica);
                        $entityInfoDetalleSolCaract->setValor($strEstadoCambioPrecio);
                        $entityInfoDetalleSolCaract->setFeCreacion(new \DateTime('now'));
                        $entityInfoDetalleSolCaract->setUsrCreacion($entityInfoDetalleSolicitudDet->getUsrCreacion());
                        $emComercial->persist($entityInfoDetalleSolCaract);
                        $emComercial->flush();
                    }

                    // Se crea el Estado Radio para la Solicitud Detalle
                    $caracteristica = $admiCaracteristica->findOneBy(array("descripcionCaracteristica" => "Estado Radio"));
                    if($caracteristica)
                    {
                        $entityInfoDetalleSolCaract = new InfoDetalleSolCaract();
                        $entityInfoDetalleSolCaract->setDetalleSolicitudId($entityInfoDetalleSolicitudDet);
                        $entityInfoDetalleSolCaract->setEstado('Pendiente');
                        $entityInfoDetalleSolCaract->setCaracteristicaId($caracteristica);
                        $entityInfoDetalleSolCaract->setValor($strEstadoRadio);
                        $entityInfoDetalleSolCaract->setFeCreacion(new \DateTime('now'));
                        $entityInfoDetalleSolCaract->setUsrCreacion($entityInfoDetalleSolicitudDet->getUsrCreacion());
                        $emComercial->persist($entityInfoDetalleSolCaract);
                        $emComercial->flush();
                    }

                    // Se crea el Estado IPCCL2 para la Solicitud Detalle
                    $caracteristica = $admiCaracteristica->findOneBy(array("descripcionCaracteristica" => "Estado IPCCL2"));
                    if($caracteristica)
                    {
                        $entityInfoDetalleSolCaract = new InfoDetalleSolCaract();
                        $entityInfoDetalleSolCaract->setDetalleSolicitudId($entityInfoDetalleSolicitudDet);
                        $entityInfoDetalleSolCaract->setEstado('Pendiente');
                        $entityInfoDetalleSolCaract->setCaracteristicaId($caracteristica);
                        $entityInfoDetalleSolCaract->setValor($strEstadoIpccl2);
                        $entityInfoDetalleSolCaract->setFeCreacion(new \DateTime('now'));
                        $entityInfoDetalleSolCaract->setUsrCreacion($entityInfoDetalleSolicitudDet->getUsrCreacion());
                        $emComercial->persist($entityInfoDetalleSolCaract);
                        $emComercial->flush();
                    }

                    if($strEstadoCambioPrecio == 'N/A' && $strEstadoRadio == 'N/A' && $strEstadoIpccl2 == 'N/A')
                    {
                        $entityInfoDetalleSolicitudDet->setEstado('Aprobada');
                        $emComercial->persist($entityInfoDetalleSolicitudDet);
                        $emComercial->flush();

                        $entityInfoDetalleSolHistDet = new InfoDetalleSolHist();
                        $entityInfoDetalleSolHistDet->setDetalleSolicitudId($entityInfoDetalleSolicitudDet);
                        $entityInfoDetalleSolHistDet->setEstado($entityInfoDetalleSolicitudDet->getEstado());
                        $entityInfoDetalleSolHistDet->setObservacion("Detalle aprobado Automaticamente al no tener nigun nivel de Aprobación.");
                        $entityInfoDetalleSolHistDet->setMotivoId($entityInfoDetalleSolicitudDet->getMotivoId());
                        $entityInfoDetalleSolHistDet->setFeCreacion(new \DateTime('now'));
                        $entityInfoDetalleSolHistDet->setUsrCreacion($entityInfoDetalleSolicitudDet->getUsrCreacion());

                        $emComercial->persist($entityInfoDetalleSolHistDet);
                        $emComercial->flush();
                    }       
                    
                    //Se busca servicios backups y generar solicitud de cambio plan masivo
                    if($tipoSolicitud->getDescripcionSolicitud() == 'CAMBIO PLAN')
                    {
                        $arrayParametrosServiciosBackups                          = array();
                        $arrayParametrosServiciosBackups['objSolicitudPadre']     = $entityInfoDetalleSolicitudCab;
                        $arrayParametrosServiciosBackups['objSolicitudPrincipal'] = $entityInfoDetalleSolicitudDet;
                        $arrayParametrosServiciosBackups['intCapacidadNueva1']    = $arrayParametros['intCapacidad1'];
                        $arrayParametrosServiciosBackups['intCapacidadNueva2']    = $arrayParametros['intCapacidad2'];
                        $arrayParametrosServiciosBackups['intPrecio']             = $arrayParametros['intPrecio'];
                        $arrayParametrosServiciosBackups['floatUmbral']           = self::PORC_UMBRAL;
                        $arrayParametrosServiciosBackups['intIdMotivo']           = $arrayParametros['intIdMotivo'];
                        $arrayParametrosServiciosBackups['objTipoSolicitud']      = $tipoSolicitud;
                        $arrayParametrosServiciosBackups['strDescripcion']        = $arrayParametros['strDescripcion'];
                        $arrayParametrosServiciosBackups['strUsrCreacion']        = $objSession->get('user');
                        $arrayParametrosServiciosBackups['objFeFinPlan']          = $feFinPlan;
                        $arrayRespuesta = $serviceTecnico->crearSolicitudCambioPlanBackups($arrayParametrosServiciosBackups);
                        
                        //Se crea historial de solicitud indicando que tiene solicitudes pendientes de backups en caso de existir
                        if(!empty($arrayRespuesta['strEstadoIpccl2']) || !empty($arrayRespuesta['strEstadoRadio']))
                        {
                            $strLoginesAux = $arrayRespuesta['strLoginesAux'];
                            
                            $entityInfoDetalleSolHistDet = new InfoDetalleSolHist();
                            $entityInfoDetalleSolHistDet->setDetalleSolicitudId($entityInfoDetalleSolicitudDet);
                            $entityInfoDetalleSolHistDet->setEstado($entityInfoDetalleSolicitudDet->getEstado());
                            $entityInfoDetalleSolHistDet->setObservacion("Detalle posee Solicitudes de Cambio Plan de Servicios <b>Backup</b> "
                                                                         . "en estado <b>Pendiente</b> que deben ser Aprobadas:".$strLoginesAux);
                            $entityInfoDetalleSolHistDet->setMotivoId($entityInfoDetalleSolicitudDet->getMotivoId());
                            $entityInfoDetalleSolHistDet->setFeCreacion(new \DateTime('now'));
                            $entityInfoDetalleSolHistDet->setUsrCreacion($entityInfoDetalleSolicitudDet->getUsrCreacion());

                            $emComercial->persist($entityInfoDetalleSolHistDet);
                            $emComercial->flush();
                        }
                    }
                    
                    $entityInfoPunto        = $servicio->getPuntoId();
                    $strUsrVendedorServicio = $servicio->getUsrVendedor();
                    if($strUsrVendedorServicio)
                    {
                        $arrayCorreosVendedorServicio  =  $emComercial->getRepository('schemaBundle:InfoPersona')
                                                                      ->getContactosByLoginPersonaAndFormaContacto($strUsrVendedorServicio,
                                                                                                                   'Correo Electronico');
                        if($arrayCorreosVendedorServicio)
                        {
                            foreach($arrayCorreosVendedorServicio as $arrayCorreoVendedorServicio)
                            {
                                if($arrayCorreoVendedorServicio && !empty($arrayCorreoVendedorServicio['valor']))
                                {
                                    $arrayTo[] = $arrayCorreoVendedorServicio['valor'];
                                }
                            }
                        }
                    }
                    
                    
                    if($entityInfoPunto)
                    {
                        $strUsrVendedorPunto            = $entityInfoPunto->getUsrVendedor();
                        if($strUsrVendedorPunto)
                        {
                            $arrayCorreosVendedorPunto  =  $emComercial->getRepository('schemaBundle:InfoPersona')
                                                                       ->getContactosByLoginPersonaAndFormaContacto($strUsrVendedorPunto,
                                                                                                                    'Correo Electronico');
                            if($arrayCorreosVendedorPunto)
                            {
                                foreach($arrayCorreosVendedorPunto as $arrayCorreoVendedorPunto)
                                {
                                    if($arrayCorreoVendedorPunto && !empty($arrayCorreoVendedorPunto['valor']))
                                    {
                                        $arrayTo[] = $arrayCorreoVendedorPunto['valor'];
                                    }
                                }
                            }
                        }
                        
                        
                        $arrayCorreosContactoComercialPunto = $emComercial->getRepository("schemaBundle:InfoPuntoContacto")
                                                                          ->getArrayContactosPorPuntoYTipo( $entityInfoPunto->getId(),
                                                                                                            "Contacto Comercial");
                        
                        if($arrayCorreosContactoComercialPunto)
                        {
                            foreach($arrayCorreosContactoComercialPunto as $arrayCorreoContactoComercialPunto)
                            {
                                if($arrayCorreoContactoComercialPunto && !empty($arrayCorreoContactoComercialPunto['valor']))
                                {
                                    $arrayTo[] = $arrayCorreoContactoComercialPunto['valor'];
                                }
                            } 
                        }  
                    }
                    if($strPrefijoEmpresa == 'TN' && ($tipoSolicitud->getDescripcionSolicitud() =="CAMBIO PLAN"
                       || $tipoSolicitud->getDescripcionSolicitud()=="CAMBIO PRECIO"))
                    {
                        $arrayDestinatarios       = array();
                        $strVendedor              = (is_object($entityInfoPunto)) ? $entityInfoPunto->getUsrVendedor():"";
                        $objPersona               = (is_object($entityInfoPunto)) ? $entityInfoPunto->getPersonaEmpresaRolId()->getPersonaId():"";
                        $strCliente               = "";
                        $strIdentificacion        = (is_object($objPersona)) ? $objPersona->getIdentificacionCliente():"";
                        $strCliente               = (is_object($objPersona) && $objPersona->getRazonSocial()) ?
                                                    $objPersona->getRazonSocial() : $objPersona->getNombres() . " " .$objPersona->getApellidos();
                        $intPrecioSolicitudMasiva = $this->getValorCaracteristicaDetalleSolicitud($entityInfoDetalleSolicitudDet->getId(),'Precio');

                        $arrayParametrosCaracteristica                  = array();
                        $arrayParametrosCaracteristica['intIdServicio'] = $servicio->getId();
                        $arrayResultadoCaract                           = $objServicioRepository
                                                                          ->findServiciosProductoCaracteristicas($arrayParametrosCaracteristica);
                        $arrayProductoCaracteristicasValores = array();
                        if(!empty($arrayResultadoCaract['registros']) && isset($arrayResultadoCaract['registros'])
                           && count($arrayResultadoCaract['registros']) > 0)
                        {
                            foreach($arrayResultadoCaract['registros'] as $objItemCaracteristica):
                                $strDescrCaract                                       = $objItemCaracteristica['descripcionCaracteristica'];
                                $arrayProductoCaracteristicasValores[$strDescrCaract] = $objItemCaracteristica['valor'];
                            endforeach;
                        }
                        $strFuncionPrecio            = $servicio->getProductoId()->getFuncionPrecio();
                        $strPrecioMinimo             = $this->evaluarFuncionPrecio($strFuncionPrecio, $arrayProductoCaracteristicasValores);
                        $floatPorcentajeDescuentoTmp = 100 - ((floatval($intPrecioSolicitudMasiva) * 100)/floatval($strPrecioMinimo));
                        $floatPorcentajeDescuento    = abs($floatPorcentajeDescuentoTmp);
                        $objCargosCab                = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                                 ->findOneBy(array('nombreParametro' => self::RANGO_APROBACION_SOLICITUDES,
                                                                                   'modulo'          => self::COMERCIAL,
                                                                                   'proceso'         => self::ADMINISTRACION_CARGOS_SOLICITUDES,
                                                                                   'estado'          => 'Activo'));
                        if(!is_object($objCargosCab) || empty($objCargosCab))
                        {
                            throw new \Exception('No se encontraron datos con los parámetros enviados.');
                        }
                        $arrayCargosDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->findBy(array('parametroId' => $objCargosCab->getId(),
                                                                   'valor4'      => 'ES_JEFE',
                                                                   'valor7'      => 'SI',
                                                                   'estado'      => 'Activo'));
                        $strCargoAsignado = "";
                        if(is_array($arrayCargosDet))
                        {
                            foreach($arrayCargosDet as $objCargosItem)
                            {
                                if(floatval($floatPorcentajeDescuento) >= floatval($objCargosItem->getValor1()) && 
                                   floatval($floatPorcentajeDescuento) <= floatval($objCargosItem->getValor2()))
                                {
                                    $strCargoAsignado = ucwords(strtolower(str_replace("_"," ",$objCargosItem->getValor3())));
                                }
                            }
                        }
                        //Correo del vendedor.
                        $arrayCorreos = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                    ->getContactosByLoginPersonaAndFormaContacto($strVendedor,
                                                                                                 "Correo Electronico");
                        if(!empty($arrayCorreos) && is_array($arrayCorreos))
                        {
                            foreach($arrayCorreos as $arrayItem)
                            {
                                if( !empty($arrayItem['valor']) && isset($arrayItem['valor']) )
                                {
                                    $arrayDestinatarios[] = $arrayItem['valor'];
                                }
                            }
                        }
                        //Correo del subgerente
                        $arrayResultadoCorreo    = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                               ->getSubgerentePorLoginVendedor(array("strLogin"=>$strVendedor));
                        if(!empty($arrayResultadoCorreo["registros"]) && isset($arrayResultadoCorreo["registros"]))
                        {
                            $arrayRegistrosCorreo = $arrayResultadoCorreo['registros'];
                            $arrayCorreos         = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                    ->getContactosByLoginPersonaAndFormaContacto($arrayRegistrosCorreo[0]["LOGIN_SUBGERENTE"],
                                                                                                 "Correo Electronico");
                            if(!empty($arrayCorreos) && is_array($arrayCorreos))
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
                        //Correo de la persona quien crea la solicitud.
                        $arrayCorreos = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                    ->getContactosByLoginPersonaAndFormaContacto($strUser,"Correo Electronico");
                        if(!empty($arrayCorreos) && is_array($arrayCorreos))
                        {
                            foreach($arrayCorreos as $arrayItem)
                            {
                                if( !empty($arrayItem['valor']) && isset($arrayItem['valor']) )
                                {
                                    $arrayDestinatarios[] = $arrayItem['valor'];
                                }
                            }
                        }
                        $strCuerpoCorreo      = "El presente correo es para indicarle que se creó una solicitud en TelcoS+ con los siguientes datos:";
                        $arrayParametrosMail  = array("strNombreCliente"         => $strCliente,
                                                      "strIdentificacionCliente" => $strIdentificacion,
                                                      "strObservacion"           => $arrayParametros['strDescripcion'],
                                                      "strCuerpoCorreo"          => $strCuerpoCorreo,
                                                      "strCargoAsignado"         => $strCargoAsignado);
                        $serviceEnvioPlantilla->generarEnvioPlantilla("CREACIÓN DE SOLICITUD MASIVA: ".$tipoSolicitud->getDescripcionSolicitud(),
                                                                      array_unique($arrayDestinatarios),
                                                                      "NOTIFICACION",
                                                                      $arrayParametrosMail,
                                                                      $strPrefijoEmpresa,
                                                                      "",
                                                                      "",
                                                                      null,
                                                                      true,
                                                                      "notificaciones_telcos@telconet.ec");
                    }
                }                
            endforeach;
            $emComercial->getConnection()->commit();
            
            try
            {
                //Si existe al menos un detalle se envia correo al cliente
                if($intNumDetalles > 0)
                {
                    // Creacion del EXCEL a adjuntar al correo de notificacion
                    $strRutaArchivoAdjunto = $this->generarExcelSolicitudMasiva($intIdEmpresa, $strPrefijoEmpresa, $entityInfoDetalleSolicitudCab->getId());
                    $strCliente            = "";

                    $arrayUsuarioCorreo = $this->getPersonaNombreCorreo($entityInfoDetalleSolicitudCab->getUsrCreacion());
                    if(!empty($arrayUsuarioCorreo['correo'])){
                        $arrayTo[] = $arrayUsuarioCorreo['correo'];
                    }

                    if($servicioCliente)
                    {
                        $puntoIdEntity = $servicioCliente->getPuntoId();
                        if($puntoIdEntity)
                        {
                            $personaIdEntity = $puntoIdEntity->getPersonaEmpresaRolId()->getPersonaId();
                            if($personaIdEntity)
                            {
                                $strCliente = '';
                                $arrayClienteCorreo = $this->getPersonaNombreCorreoByInfoPersona($personaIdEntity);
                                if(!empty($arrayClienteCorreo['nombre'])){
                                    $strCliente = $arrayClienteCorreo['nombre'];
                                }
                            }
                        }
                    }

                    $strTipoSolicitud   = $entityInfoDetalleSolicitudCab->getTipoSolicitudId()->getDescripcionSolicitud();
                    $fecha              = new \DateTime('now');

                    //Instancia del Objeto EnvioPlantilla para la notificacion
                    $envioPlantilla     = $this->get('soporte.EnvioPlantilla');
                    // Parametros a reemplazar en el cuerpo del correo
                    $arrayParametros    = array('cliente'          => $strCliente,
                                                'tipoSolicitud'    => $strTipoSolicitud,
                                                'fecha'            => $fecha);
                    //Llamada a la generacion del correo de notificacion y el envio del correo
                    $arrayTo = array_unique($arrayTo);
                    $envioPlantilla->generarEnvioPlantilla('Solicitud de ' . $strTipoSolicitud . ' #'.$entityInfoDetalleSolicitudCab->getId(),
                                                            $arrayTo,
                                                            'CREAR_SOL_CPM',
                                                            $arrayParametros,
                                                            $intIdEmpresa,
                                                            null,
                                                            null,
                                                            $strRutaArchivoAdjunto);
                    unlink($strRutaArchivoAdjunto);
                }
                
            }
            catch(\Exception $ex)
            {
                $serviceUtil->insertError('Telcos+', 'createAction', $ex->getMessage(), $strUser, $strIpClient);
            }
            
            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);
            $objReturnResponse->setRegistros($arraySinCancelar);
            $objReturnResponse->setTotal(count($arraySinCancelar));
        }
        catch(\Exception $ex)
        {
            $serviceUtil->insertError('Telcos+', 'createAction', $ex->getMessage(), $strUser, $strIpClient);
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . " " . $ex->getMessage());
            $objReturnResponse->setRegistros(array());
            $objReturnResponse->setTotal(0);
            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
        }
        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');
        $objResponse->setContent(json_encode((array) $objReturnResponse));
        return $objResponse;
    }

    /**
     * getSolicitudesMasivasHistorialAction, Obtiene listado del Historial de una Solicitud Masiva
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 11-04-2016
     * @return json Retorna un json del listado del Historial de una Solicitud Masiva
     */
    public function getSolicitudesMasivasHistorialAction()
    {
        ini_set('max_execution_time', 60000);
        
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $arrayParametros = array();
        $peticion = $this->getRequest();

        $emComercial = $this->getDoctrine()->getManager("telconet");
        $infoDetalleSolHist = $emComercial->getRepository('schemaBundle:InfoDetalleSolHist');
        $admiMotivo = $emComercial->getRepository('schemaBundle:AdmiMotivo');

        $arrayParametros['intIdDetalleSolicitud'] = $peticion->query->get('intIdSolicitudMasiva');
        $arrayParametros['intStart'] = $peticion->query->get('start');
        $arrayParametros['intLimit'] = $peticion->query->get('limit');

        $arrayResultado = $infoDetalleSolHist->getDetalleSolHist($arrayParametros);

        $solicitudes = $arrayResultado['registros'];
        $total = $arrayResultado['total'];

        //Si existen comprobantes
        if(count($solicitudes) > 0)
        {
            foreach($solicitudes as $solicitud):

                $motivo = ($solicitud->getMotivoId() != null ) ? $admiMotivo->find($solicitud->getMotivoId()) : null;
                $strNombreMotivo = ($motivo != null) ? $motivo->getNombreMotivo() : "";

                $strFeIniPlan = ($solicitud->getFeIniPlan() != null) ? $solicitud->getFeIniPlan()->format('Y-m-d H:i') : "";
                $strFeFinPlan = ($solicitud->getFeFinPlan() != null) ? $solicitud->getFeFinPlan()->format('Y-m-d H:i') : "";

                $arraySolicitudes[] = array(
                    'intIdSolicitudHistorial' => $solicitud->getId(),
                    'intIdDetalleSolicitud' => $solicitud->getDetalleSolicitudId()->getId(),
                    'strFeCreacion' => $solicitud->getFeCreacion()->format('Y-m-d H:i'),
                    'strUsrCreacion' => $solicitud->getUsrCreacion(),
                    'strEstado' => $solicitud->getEstado(),
                    'strFeIniPlan' => $strFeIniPlan,
                    'strFeFinPlan' => $strFeFinPlan,
                    'strObservacion' => $solicitud->getObservacion(),
                    'intIdMotivo' => $solicitud->getMotivoId(),
                    'strNombreMotivo' => $strNombreMotivo
                );
            endforeach;
        }

        if($total > 0)
        {
            $data = json_encode($arraySolicitudes);
            $objJson = '{"total":"' . $total . '","jsonSolicitudesMasivasHistorial":' . $data . '}';
        }
        else
        {
            $objJson = '{"total":"0","jsonSolicitudesMasivasHistorial":[]}';
        }
        $respuesta->setContent($objJson);
        return $respuesta;
    }

    /**
     * getDetalleSolicitudDetAction, Obtiene listado detalles de Solicitudes
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 26-03-2016
     * @return json Retorna un json del listado detalles de Solicitudes
     * 
     * @author Modificado: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.1 22-06-2016 - Se presenta en la descripcion del servicio el nombre del switch, puerto y modelo cpe
     * 
     * @author Modificado: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.2 01-08-2016 - Validaciones de IPCCL2 se optimizó la validacion de BW para el CPE
     * 
     * @author Modificado: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.3 05-08-2016 - Consulta de Producto para Cambio de Plan Obsoletos
     * 
     * @author Modificado: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.4 12-08-2016 - Consulta de caracteristicas por detalles
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.5 01-09-2016 - Se realizan validaciones para que se muestre el detalle aún si no existiera última milla
     * 
     * @author Modificado: Allan Suarez <arsuarez@telconet.ec>
     * @version 1.6 06-04-2017 - Se envia informacion de tipo de enlace y tipo de solicitud masiva para poder validar mostrar iconos de
     *                           ejecucion o rechazo de proceso masivo para servicios backups
     *                         - Se muestra nombre del Login Aux del Servicio principal referenciado en el backup
     * 
     * @author Modificado: Allan Suarez <arsuarez@telconet.ec>
     * @version 1.7 05-06-2017 - Se agrega obtencion de tipo enlace de la info tecnica dentro de la validacion de existencia de
     *                           el objeto
     * 
     * @author Modificado: Allan Suarez <arsuarez@telconet.ec>
     * @version 1.8 13-07-2017 - Se valida variables existentes para cuando no exista Tipo Enlace ( Data Tecnica ) para que pueda seguir flujo
     * 
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.9 19-07-2017 - Se realizan ajustes para mostrar si las solicitudes de Demos son facturables
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.0 08-03-2018 - Se agrega la información de la característica velocidad asociada a los productos Internet Small Business
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.1 04-02-2019 Se agregan validaciones para nuevo producto TelcoHome
     * 
     * @author Modificado: Edgar Holguín <eholguin@telconet.ec>
     * @version 2.2 30-05-2019 Se agrega validación para que se excluya solicitudes de cambio de plan (BACKUPS) en estado Rechazada al momento 
     *                         de consultar la existencia de al menos una caracteristica con valor Pendiente . 
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 2.3 17-09-2022 - Se agrega la información de precio por cada mbps, precio de lista y precio a descontar.
     *
     */
    public function getDetalleSolicitudDetAction()
    {
        ini_set('max_execution_time', 60000);
        
        $peticion  = $this->getRequest();
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $emComercial                    = $this->getDoctrine()->getManager("telconet");
        $emInfraestructura              = $this->getDoctrine()->getManager("telconet_infraestructura");
        $infoDetalleSolicitudRepository = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud');
        $infoServicioRepository         = $emComercial->getRepository('schemaBundle:InfoServicio');
        $infoDetalleSolCaractRepository = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract');
        $admiCaracteristicaRepository   = $emComercial->getRepository('schemaBundle:AdmiCaracteristica');
        $InfoDetalleSolHist             = $emComercial->getRepository('schemaBundle:InfoDetalleSolHist');
        $infoProductoNivel              = $emComercial->getRepository('schemaBundle:InfoProductoNivel');
        $infoEmpresaRol                 = $emComercial->getRepository('schemaBundle:InfoEmpresaRol');
        $admiRol                        = $emComercial->getRepository('schemaBundle:AdmiRol');
        $infoServicioTecnico            = $emComercial->getRepository('schemaBundle:InfoServicioTecnico');
        $admiProducto                   = $emComercial->getRepository('schemaBundle:AdmiProducto');
        $admiTipoMedio                  = $emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio');
        $infoElemento                   = $emInfraestructura->getRepository('schemaBundle:InfoElemento');
        $infoInterfaceElemento          = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento');
        
        $arrayParametros                    = array();
        $arrayParametros['intIdEmpresa']    = ($peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa') : "");
        $arrayParametros['intIdPadre']      = $peticion->query->get('intIdDetalleSolicitudCab');
        $arrayParametros['strEstado']       = $peticion->query->get('strEstado');
        $arrayParametros['boolMasivas']     = 'false';
        $arrayParametros['intStart']        = $peticion->query->get('start');
        $arrayParametros['intLimit']        = $peticion->query->get('limit');
        $strFechaInicioDemo                 = "";
        $strFechaFinDemo                    = "";
        $arrayParametrosDemo                = "";
        $arrayRespuestaFechasDemo           = "";
        
        $resultado      = $infoDetalleSolicitudRepository->findSolicitudes($arrayParametros);
        $solicitudes    = $resultado['registros'];
        $total          = $resultado['total'];
            
        //Si existen comprobantes
        if(count($solicitudes) > 0)
        {
            foreach($solicitudes as $solicitud):
                $capacidad1Original = "";
                $capacidad2Original = "";
                
                // Se buscan todas las caracteristicas de la solicitud detalle  
                $infoDetalleSolicitudCaract = $infoDetalleSolCaractRepository->findBy(array(
                                                                                           "detalleSolicitudId" => $solicitud['idDetalleSolicitud']
                                                                                           ));                                                
                $infoDetalleSolicitudDetArray = array();
                $strDatosNuevos  = "";
                $strDatoProducto = "";
                $productoEntity  = null;
                
                //Se muestran las fechas de inicio y fin de la solicitud de Demos
                if($solicitud['descripcionSolicitud'] == 'DEMOS')
                {
                    $arrayParametrosDemo["intSolicitudId"] = $solicitud['idDetalleSolicitud'];
                    $arrayRespuestaFechasDemo              = $infoDetalleSolicitudRepository->getFechasDemo($arrayParametrosDemo);

                    $strFechaInicioDemo = $arrayRespuestaFechasDemo["strFechaInicio"];
                    $strFechaFinDemo    = $arrayRespuestaFechasDemo["strFechaFin"];                    
                    
                    $strDatosNuevos = "<b>Fecha Inicio: </b>" . $strFechaInicioDemo. "<br>";
                    $strDatosNuevos .= "<b>Fecha Fin: </b>" . $strFechaFinDemo. "<br>";
                }
                $strCapacidad1 = "";
                $strPrecio     = "";
                for($intIndex = 0; $intIndex < count($infoDetalleSolicitudCaract); $intIndex++)
                {
                    $caracteristica = $admiCaracteristicaRepository->find($infoDetalleSolicitudCaract[$intIndex]->getCaracteristicaId());
                    $label = $caracteristica->getDescripcionCaracteristica();
                    $valor = $infoDetalleSolicitudCaract[$intIndex]->getValor();
                    
                    if(!empty($valor))
                    {                                               
                        if($label == 'CAPACIDAD1' || $label == 'CAPACIDAD2' || $label == 'Precio' || $label == 'Facturable' 
                            || $label == 'VELOCIDAD' || $label == 'VELOCIDAD_TELCOHOME')
                        {                            
                            if($solicitud['descripcionSolicitud'] == 'DEMOS' && $label == 'Precio')
                            {
                                $infoDetalleSolicitudDetArray[$label] = $valor;
                                $strDatosNuevos .= "<b>Precio del Demo</b>: " . '$ ' . $valor . "<br>";
                            }
                            else
                            {
                                $infoDetalleSolicitudDetArray[$label] = $valor;
                                $strDatosNuevos .= "<b>" . $label . "</b>: " . (($label == 'Precio') ? '$ ' . $valor : $valor) . "<br>";
                                $strCapacidad1   = ($label == "CAPACIDAD1" && $strCapacidad1 == "") ? $valor : $strCapacidad1;
                                $strPrecio       = ($label == "Precio" && $strPrecio == "") ? $valor : $strPrecio;
                            }
                        } 
                        else if($label == 'Producto Obsoleto')
                        {                
                            $productoEntity = $admiProducto->find($valor);
                            if($productoEntity)
                            {
                                $strDatoProducto = "<b>Producto</b>: " . $productoEntity->getDescripcionProducto() . "<br>";
                            }
                        }
                    }
                    else
                    {
                        if($label == 'Precio' && $solicitud['descripcionSolicitud'] == 'DEMOS')
                        {
                            $infoDetalleSolicitudDetArray[$label] = "0";
                            $strDatosNuevos .= "<b>Precio del Demo</b>: $ 0 <br>";
                        }
                    }
                }
                
                if(!empty($strDatoProducto))
                {
                    $strDatosNuevos = $strDatoProducto . $strDatosNuevos;
                }
                
                $strDatosNuevos .= "<b>Descripción</b>: " . $solicitud['observacion'] . "<br>";
                
                
                //Validar si el servicio tiene una solicitud de Demos en estado Activa                        
                $arrayParametrosSol["intServicio"]  = $solicitud['idServicio']; 
                $arrayParametrosSol["strSolicitud"] = "DEMOS";
                $arrayParametrosSol["strEstado"]    = "Activa";

                $intSolicitudActiva = $infoDetalleSolicitudRepository->getSolicitudActivaPorServicio($arrayParametrosSol);

                $datosActuales = "";
                if($intSolicitudActiva != "")
                {
                    $datosActuales = "<b>(*****DEMO ACTIVO*****)</b><br>";
                }                
                $intSolicitudActiva = "";                
                //Se agrega solicitudes al arreglo de solicitudes
                $servicio      = $infoServicioRepository->find($solicitud['idServicio']);
                $datosActuales .= "<b>Precio:</b> $ " . $servicio->getPrecioVenta();
                
                $arrayParametrosCaracteristica                  = array();
                $arrayParametrosCaracteristica['intIdServicio'] = $servicio->getId();
                
                $arrayResultadoCaract    = $infoServicioRepository->findServiciosProductoCaracteristicas($arrayParametrosCaracteristica);
                $caracteristicasServicio = $arrayResultadoCaract['registros'];

                $arrayProductoCaracteristicasValores = array();
                if(count($caracteristicasServicio) > 0)
                {
                    foreach($caracteristicasServicio as $caracteristica):
                        $descripcionCaract                                       = $caracteristica['descripcionCaracteristica'];
                        $strValor                                                = $caracteristica['valor'];
                        if($descripcionCaract == 'ES_BACKUP')
                        {
                            $objServicioPrincipal =  $infoServicioRepository->find($strValor);
                            
                            if(is_object($objServicioPrincipal))
                            {
                                $strValor = "<label style='color:green;'>".$objServicioPrincipal->getLoginAux()."</label>";
                            }
                        }
                        $objProdSol = $servicio->getProductoId();
                        if(is_object($objProdSol) 
                            && ($objProdSol->getNombreTecnico() === "INTERNET SMALL BUSINESS" 
                                || $objProdSol->getNombreTecnico() === "TELCOHOME"))
                        {
                            if($descripcionCaract === "VELOCIDAD" || $descripcionCaract === "VELOCIDAD_TELCOHOME")
                            {
                                $datosActuales  .= "<br><b>" . $descripcionCaract . ": </b>" . $strValor;
                            }
                        }
                        else
                        {
                            $datosActuales  .= "<br><b>" . $descripcionCaract . ": </b>" . $strValor;
                        }
                        $arrayProductoCaracteristicasValores[$descripcionCaract] = $caracteristica['valor'];
                        if(!empty($infoDetalleSolicitudDetArray[$descripcionCaract]))
                        {
                            if($descripcionCaract == 'CAPACIDAD1')
                            {
                                $capacidad1Original = $caracteristica['valor'];
                            } else if($descripcionCaract == 'CAPACIDAD2')
                            {
                                $capacidad2Original = $caracteristica['valor'];
                            }
                            $arrayProductoCaracteristicasValores[$descripcionCaract] = $infoDetalleSolicitudDetArray[$descripcionCaract];
                        }
                    endforeach;
                }
                                
                $strNivelAprobacion = "N/A";                
                $precioMinimo       = "N/A";
                
                $strEstadoPrecio    = "N/A";
                $strEstadoRadio     = "N/A";
                $strEstadoIpccl2    = "N/A";
                
                $strMensajePrecio   = "N/A";
                $strMensajeRadio    = "N/A";
                $strMensajeIpccl2   = "N/A";
                
                $strModeloSwitch    = "";
                $strPuertoSwitch    = "";
                $strModeloCPE       = "";
                
                if($productoEntity == null)
                {
                    $productoEntity     = $servicio->getProductoId();
                }
                // Consulta de los distintos niveles de aprobacion
                $strEstadoPrecio = $this->getValorCaracteristicaDetalleSolicitud($solicitud['idDetalleSolicitud'], "Estado Cambio Precio");
                $strEstadoRadio  = $this->getValorCaracteristicaDetalleSolicitud($solicitud['idDetalleSolicitud'], "Estado Radio");
                $strEstadoIpccl2 = $this->getValorCaracteristicaDetalleSolicitud($solicitud['idDetalleSolicitud'], "Estado IPCCL2");
                // Consulta de las leyendas que acompañan a los estados
                if($solicitud['descripcionSolicitud'] != 'CANCELACION')
                {
                    $funcionPrecio = $productoEntity->getFuncionPrecio();
                    $precioMinimo  = $this->evaluarFuncionPrecio($funcionPrecio, $arrayProductoCaracteristicasValores);
                    $strCapacidad1 = (empty($strCapacidad1) && $solicitud['descripcionSolicitud'] == "CAMBIO PRECIO") ? 
                                     $arrayProductoCaracteristicasValores["CAPACIDAD1"]:$strCapacidad1;
                    if(!empty($strCapacidad1) && !empty($strPrecio) && !empty($precioMinimo))
                    {
                        $floatCapacidadXMbps     = !empty($strCapacidad1) ? floatval($strCapacidad1)/1024 : "";
                        $strDatosNuevos         .= "<b>Capacidad en Mbps</b>: " . $floatCapacidadXMbps . "<br>";
                        $floatPrecioPorMbps      = floatval($strPrecio)/floatval($floatCapacidadXMbps);
                        $strDatosNuevos         .= "<b>Precio por Mbps</b>: $" . $floatPrecioPorMbps . "<br>";
                        $floatPrecioListaPorMbps = floatval($precioMinimo)/floatval($floatCapacidadXMbps);
                        $floatDescSolicitado     = abs((($floatPrecioListaPorMbps-$floatPrecioPorMbps)/$floatPrecioListaPorMbps)*100);
                        $strDatosNuevos         .= "<b>Descuento solicitado</b>: " . round($floatDescSolicitado,2) . "%<br>";
                    }
                    // Se verifica si existe un Rol para un nivel de autorizacion y su respectivo porcentaje
                    $InfoProductoNivelEntity = $infoProductoNivel->findOneBy(array("productoId" => $productoEntity));
                    $precioMinimoRol         = null;
                    if($InfoProductoNivelEntity)
                    {
                        $empresaRolId        = $InfoProductoNivelEntity->getEmpresaRolId();
                        $empresaRol          = $infoEmpresaRol->find($empresaRolId);
                        $rol                 = $admiRol->find($empresaRol->getRolId());
                        $porcentajeDescuento = $InfoProductoNivelEntity->getPorcentajeDescuento();
                        //El Precio minimo permitido cambia si exite alguien designado a aprobarlo dentro del rango
                        $precioMinimoRol     = $precioMinimo - ($precioMinimo * ($porcentajeDescuento / 100));
                    }

                    if($solicitud['descripcionSolicitud'] != 'DEMOS')
                    {
                        if((isset($infoDetalleSolicitudDetArray['Precio']) && !empty($infoDetalleSolicitudDetArray['Precio'])) &&
                           ($precioMinimoRol != null && $infoDetalleSolicitudDetArray['Precio'] > $precioMinimoRol && 
                           $infoDetalleSolicitudDetArray['Precio'] < $precioMinimo))
                        {
                            $strNivelAprobacion = "<b class='text-warning'>" . $rol->getDescripcionRol() . "</b>";
                            $strMensajePrecio = "El valor es menordi al precio mínimo, pero dentro del <b>" . $porcentajeDescuento 
                                                . "%</b> del rango de aprobación del <b>" . $rol->getDescripcionRol() . "</b>";

                        }
                        else if((isset($infoDetalleSolicitudDetArray['Precio']) && !empty($infoDetalleSolicitudDetArray['Precio'])) && 
                                ($infoDetalleSolicitudDetArray['Precio'] < $precioMinimo))
                        {
                            $strNivelAprobacion = "<b class='text-warning'>Gerente General</b>";
                            $strMensajePrecio = "Valor menor al precio mínimo, aprobación de <b>Gerente General</b>";
                        }
                    }
                    
                    if($solicitud['descripcionSolicitud'] == 'CAMBIO PLAN' || $solicitud['descripcionSolicitud'] == 'DEMOS')
                    {
                        // Validacion del Nivel de Aprobacion del Ancho de Banda de ser RADIO                    
                        $infoServicioTecnicoEntity = $infoServicioTecnico->findOneBy(array("servicioId" => $servicio->getId()));
                        if($infoServicioTecnicoEntity)
                        {
                            // Busqueda del Switch y puerto
                            $intElementoIdSwitch = $infoServicioTecnicoEntity->getElementoId();
                            if($intElementoIdSwitch)
                            {
                                $infoElementoSwitch = $infoElemento->find($intElementoIdSwitch);
                                if($infoElementoSwitch)
                                {
                                    $strModeloSwitch = $infoElementoSwitch->getNombreElemento();                                    
                                }
                            }
                            $intInterfaceElementoIdSwitch = $infoServicioTecnicoEntity->getInterfaceElementoId();
                            if($intInterfaceElementoIdSwitch)
                            {
                                $infoInterfaceElementoSwitch = $infoInterfaceElemento->find($intInterfaceElementoIdSwitch);
                                if($infoInterfaceElementoSwitch)
                                {
                                    $strPuertoSwitch = $infoInterfaceElementoSwitch->getNombreInterfaceElemento();
                                }
                            }                            
                            

                            if($infoServicioTecnicoEntity->getUltimaMillaId())
                            {
                                $admiTipoMedioEntity = $admiTipoMedio->find($infoServicioTecnicoEntity->getUltimaMillaId());                    
                                if($admiTipoMedioEntity)
                                {
                                    if($admiTipoMedioEntity->getNombreTipoMedio() == 'Radio')
                                    {
                                         if((!empty($infoDetalleSolicitudDetArray['CAPACIDAD1']) && !empty($capacidad1Original) && 
                                            $infoDetalleSolicitudDetArray['CAPACIDAD1'] > $capacidad1Original) || 
                                            (!empty($infoDetalleSolicitudDetArray['CAPACIDAD2']) && !empty($capacidad2Original) 
                                            && $infoDetalleSolicitudDetArray['CAPACIDAD2'] > $capacidad2Original))
                                        {
                                            $strMensajeRadio = "El aumento de capacidad debe ser autorizado por <b>Radio</b>";
                                        }
                                    }
                                }
                            }
                            
                            // Validacion del Nivel de Aprobacion del Ancho de Banda para IPCCL2
                            if(!empty($solicitud['nombreModeloElemento']))
                            {
                                $strModeloCPE = $solicitud['nombreModeloElemento'];                                
                                if(!empty($solicitud['capacidad1ModeloElemento']) && !empty($solicitud['capacidad2ModeloElemento']))
                                {
                                    $intModeloCapacidad1KPBSUmbral = $solicitud['capacidad1ModeloElemento'] * self::PORC_UMBRAL;
                                    $intModeloCapacidad2KPBSUmbral = $solicitud['capacidad2ModeloElemento'] * self::PORC_UMBRAL;

                                    if($infoDetalleSolicitudDetArray['CAPACIDAD1'] > $intModeloCapacidad1KPBSUmbral || 
                                       $infoDetalleSolicitudDetArray['CAPACIDAD2'] > $intModeloCapacidad2KPBSUmbral)
                                    {
                                        $strMensajeIpccl2 = "El aumento de capacidad debe ser autorizado por <b>IPCCL2</b>, superó el umbral "
                                                            . "permitido.";
                                    }
                                }
                                else
                                {
                                    $strMensajeIpccl2 = "La capacidad debe ser autorizado por <b>IPCCL2</b>, No se encontraron valores en los Límites"
                                                        . " de Capacidad para este CPE.";
                                }
                            }
                            else
                            {
                                $strMensajeIpccl2 = "El aumento de capacidad debe ser autorizado por <b>IPCCL2</b>, No se encontró CPE asociado.";
                            }
                        }
                    }
                }

                $strObservacion = "";
                if($strEstadoPrecio == 'Rechazada')
                {

                    $caracteristicaRefSolMasiva = $admiCaracteristicaRepository->findOneBy(array( "descripcionCaracteristica" => 
                                                                                                    "Referencia Solicitud Masiva"));
                    $infoDetalleSolCaractRefSolMasiva = $infoDetalleSolCaractRepository->findOneBy(array(   
                                                                                                            "valor"             => 
                                                                                                            $solicitud['idDetalleSolicitud'],
                                                                                                            "caracteristicaId"  => 
                                                                                                            $caracteristicaRefSolMasiva));
                    $SolicitudDescuentoDetalle = null;
                    if($infoDetalleSolCaractRefSolMasiva)
                    {
                        $SolicitudDescuentoDetalle = $infoDetalleSolCaractRefSolMasiva->getDetalleSolicitudId();
                    }
                    if($SolicitudDescuentoDetalle)
                    {
                        $infoDetalleSolicitudCabHistorial = $InfoDetalleSolHist->findOneBy(array(
                                                                                                "detalleSolicitudId" => $SolicitudDescuentoDetalle,
                                                                                                "estado"             => "Rechazada"
                                                                                                ));
                        if($infoDetalleSolicitudCabHistorial)
                        {
                            $strObservacion = $infoDetalleSolicitudCabHistorial->getObservacion();
                        }
                    }
                }
                else if($solicitud['estado'] == 'Rechazada')
                {
                    $infoDetalleSolicitudCabHistorial = $InfoDetalleSolHist->findOneBy(array(
                                                                                            "detalleSolicitudId" => $solicitud['idDetalleSolicitud'],
                                                                                            "estado" => "Rechazada"
                                                                                            ));
                    if($infoDetalleSolicitudCabHistorial)
                    {
                        $strObservacion = $infoDetalleSolicitudCabHistorial->getObservacion();
                    }
                    
                }
                else if(!empty($solicitud['observacion']))
                {
                    $strObservacion = $solicitud['observacion'];
                }
                
                $strDescripcionServicio  = "<b>Descripción Servicio: </b><br>" . $servicio->getDescripcionPresentaFactura();
                $strDescripcionServicio .=  "<br><b>Producto: </b>" . $productoEntity->getDescripcionProducto();
                $strDescripcionServicio .=  "<br><b>Login Aux: </b><br>" . $servicio->getLoginAux();
                
                $infoServicioTecnicoEntity  = $infoServicioTecnico->findOneBy(array("servicioId" => $solicitud['idServicio']));
                $strNombreTipoMedio         = "N/A";
                $strTipoEnlace              = "N/A";
                
                if($infoServicioTecnicoEntity)
                {
                    if($infoServicioTecnicoEntity->getUltimaMillaId())
                    {
                        $admiTipoMedioEntity = $admiTipoMedio->find($infoServicioTecnicoEntity->getUltimaMillaId());                    
                        if($admiTipoMedioEntity)
                        {
                            $strNombreTipoMedio = $admiTipoMedioEntity->getNombreTipoMedio();                        
                        }
                    }
                    
                    $strTipoEnlace = $infoServicioTecnicoEntity->getTipoEnlace();
                }
                $strDescripcionServicio .=  "<br><b>Última Milla: </b><br>" . $strNombreTipoMedio;
                $precioMinimo = ($precioMinimo != "N/A") ? "$ " . $precioMinimo : $precioMinimo;
                
                $strNivelAprobacionTable = "<table class='sm_table_interior'>"
                                        . "<tr><th><div class='icon_precio'>Precio: </th><td class='color-warning-" 
                                        . ($strEstadoPrecio == 'N/A' || $strEstadoPrecio == 'Aprobada' ? 'ok' : 'precio') . "'>" 
                                        . $strEstadoPrecio . "</div></td></tr>"
                                        . "<tr><th><div class='icon_radio'>Radio: </th><td class='color-warning-" 
                                        . ($strEstadoRadio == 'N/A'  || $strEstadoRadio == 'Aprobada' ? 'ok' : 'radio') . "'>" 
                                        . $strEstadoRadio . "</div></td></tr>"
                                        . "<tr><th><div class='icon_ipccl2'>IPCCL2: </th><td class='color-warning-" 
                                        . ($strEstadoIpccl2 == 'N/A'  || $strEstadoIpccl2 == 'Aprobada' ? 'ok' : 'ipccl2') . "'>" 
                                        . $strEstadoIpccl2 . "</div></td></tr>"
                                        . "</table>";
                
                $strMensajeTable = "<ul>"
                                    . "<li class='icon_precio color-warning-" 
                                    . ($strEstadoPrecio == 'N/A' || $strEstadoPrecio == 'Aprobada' ? 'ok' : 'precio') . "'>" 
                                    . $strMensajePrecio . "</li>"
                                    . "<li class='icon_radio color-warning-" 
                                    . ($strEstadoRadio == 'N/A'  || $strEstadoRadio == 'Aprobada' ? 'ok' : 'radio') . "'>" 
                                    . $strMensajeRadio . "</li>"
                                    . "<li class='icon_ipccl2 color-warning-" 
                                    . ($strEstadoIpccl2 == 'N/A'  || $strEstadoIpccl2 == 'Aprobada' ? 'ok' : 'ipccl2') . "'>" 
                                    . $strMensajeIpccl2 . "</li>"
                                    . "</ul>";
                

                $strBackupsPendientes = 'N';
                if($solicitud['descripcionSolicitud'] == 'CAMBIO PLAN' || $solicitud['descripcionSolicitud'] == 'DEMOS')
                {
                    $strDescripcionServicio .=  "<br><b>Switch: </b><br>" . $strModeloSwitch;
                    $strDescripcionServicio .=  "<br><b>Puerto Switch: </b><br>" . $strPuertoSwitch;
                    $strDescripcionServicio .=  "<br><b>Modelo CPE: </b><br>" . $strModeloCPE;
                    
                    if($strTipoEnlace == 'PRINCIPAL')
                    {
                        $objCaracteristica = $admiCaracteristicaRepository->findOneBy(array( "descripcionCaracteristica" => "ES_BACKUP"));

                        if(is_object($objCaracteristica))
                        {
                            //Se verifica que un servicio Principal con CAMBIO PLAN no posee solicitudes de servicios backups aun pendientes
                            $arraySolCaract = $infoDetalleSolCaractRepository->findBy(array(   
                                                                                        "valor"             => $solicitud['idDetalleSolicitud'],
                                                                                        "caracteristicaId"  => $objCaracteristica->getId(),
                                                                                        ));
                            foreach($arraySolCaract as $objSolCaract)
                            {
                                $objSolicitud = $objSolCaract->getDetalleSolicitudId();
                                 
                                if($objSolicitud->getEstado()!=='Rechazada')
                                {                                                             
                                    $arrayDetalleSolCaract = $infoDetalleSolCaractRepository->findByDetalleSolicitudId($objSolicitud->getId());

                                    foreach($arrayDetalleSolCaract as $objDetalles)
                                    {
                                        //Si existe al menos un detalle backup con valor Pendiente del tipo de proceso
                                        if($objDetalles->getValor() == 'Pendiente')
                                        {
                                            $strBackupsPendientes = 'S';
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                $arraySolicitudes[] = array(
                                            'intIdSolicitud'            => $solicitud['idDetalleSolicitud'],
                                            'intIdServicio'             => $solicitud['idServicio'],
                                            'strDescripcionServicio'    => $strDescripcionServicio,
                                            'intIdPunto'                => $solicitud['idPunto'],
                                            'strLogin'                  => $servicio->getPuntoId()->getLogin(),
                                            'intIdProdructo'            => $productoEntity->getId(),
                                            'strdescripcionProducto'    => $productoEntity->getDescripcionProducto(),
                                            'strPrecioVenta'            => $servicio->getPrecioVenta(),
                                            'strPrecioMinimo'           => $precioMinimo,
                                            'strEstado'                 => $solicitud['estado'],
                                            'strBackupsPendientes'      => $strBackupsPendientes,
                                            'strDatosActuales'          => $datosActuales,
                                            'strDatosNuevos'            => $strDatosNuevos,
                                            'strNivelAprobacion'        => $strNivelAprobacionTable,
                                            'strMensaje'                => $strMensajeTable,
                                            'strObservacion'            => $strObservacion,
                                            'strEstadoPrecio'           => $strEstadoPrecio,
                                            'strEstadoRadio'            => $strEstadoRadio,
                                            'strEstadoIpccl2'           => $strEstadoIpccl2,
                                            'strTipoEnlace'             => $strTipoEnlace,
                                            'strTipoSolicitud'          => $solicitud['descripcionSolicitud']
                                            );
            endforeach;
        }

        if($total > 0)
        {
            $data = json_encode($arraySolicitudes);
            $objJson = '{"total":"' . $total . '","jsonServiciosSeleccionados":' . $data . '}';
        }
        else
        {
            $objJson = '{"total":"0","jsonServiciosSeleccionados":[]}';
        }
        $respuesta->setContent($objJson);
        return $respuesta;
    }

    /**
     * aprobarIndexAction, Redirecciona a la pagina de autorizacion de solicitudes.
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 13-04-2016
     * @return redireccion al index de la autorizacion de SolicitudesMasivas
     *
     */
    public function aprobarIndexAction()
    {
        $arrayRolesPermitidos = array();
        
        $request = $this->getRequest();
        $session = $request->getSession();
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("346", "1");
        $session->set('id_menu_activo', $entityItemMenu->getId());
        
        //Modulo admiParametroCab : [347] Se debe cambiar el Id del modulo antes de pasar a producción
        if(true === $this->get('security.context')->isGranted('ROLE_347-1'))
        {
            $arrayRolesPermitidos[] = 'ROLE_347-1'; //Rol Index
        }
        if(true === $this->get('security.context')->isGranted('ROLE_347-6'))
        {
            $arrayRolesPermitidos[] = 'ROLE_347-6'; //Rol Show
        }
        if(true === $this->get('security.context')->isGranted('ROLE_347-163'))
        {
            $arrayRolesPermitidos[] = 'ROLE_347-163'; //Rol Aprobar
        }
        if(true === $this->get('security.context')->isGranted('ROLE_347-94'))
        {
            $arrayRolesPermitidos[] = 'ROLE_347-94'; //Rol Rechazar
        }
        if(true === $this->get('security.context')->isGranted('ROLE_443-8637'))
        {
            $arrayRolesPermitidos[] = 'ROLE_443-8637'; //Rol Ver solicitudes de clientes ISP
        }
        return $this->render('comercialBundle:solicitudesmasivas:aprobarIndex.html.twig', array('rolesPermitidos' => $arrayRolesPermitidos));
    }

    /**
     * aprobarRechazarAction, Muestra los Datos de una solicitud masiva para ser aprobada o rechazada
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 28-03-2016
     * @since 1.0
     * 
     */
    public function aprobarRechazarAction($id)
    {
        $peticion = $this->getRequest();
        $intIdEmpresa = ($peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa') : "");
        $emComercial = $this->getDoctrine()->getManager("telconet");

        $infoDetalleSolicitudCabArray = $this->obtenerSolicitudAPresentar($emComercial, $intIdEmpresa, $id);

        $arrayRolesPermitidos = array();
        //Modulo admiParametroCab : [347] Se debe cambiar el Id del modulo antes de pasar a producción        
        if(true === $this->get('security.context')->isGranted('ROLE_347-163'))
        {
            $arrayRolesPermitidos[] = 'ROLE_347-163'; //Rol Aprobar
        }
        if(true === $this->get('security.context')->isGranted('ROLE_347-94'))
        {
            $arrayRolesPermitidos[] = 'ROLE_347-94'; //Rol Rechazar
        }

        return $this->render('comercialBundle:solicitudesmasivas:aprobarRechazar.html.twig', array(
                'infoDetalleSolicitudCab' => $infoDetalleSolicitudCabArray,
                'flag' => $peticion->get('flag'),
                'rolesPermitidos' => $arrayRolesPermitidos
        ));
    }

    /**
     * aprobarSolicitudAction, aprueba una o varias solicitudes del detalle
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 25-04-2016
     * 
     * @author Modificado: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.1 11-07-2016 - Notificacion via correo electronico
     * 
     * @author Modificado: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.2 01-08-2016 - Aprobacion para descuento con cambio de plan de producto obsoleto
     * 
     * @author Modificado: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.3 13-08-2016 - Consulta de carateristicas por detalle
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.4 24-12-2021 - Se establece nueva lógica para aprobar las solicitudes de acuerdo al rango de aprobación y cargo,
     *                           los cambios solo aplican para Telconet.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.5 17-08-2022 - Envío de notificación a la asistente, vendedor y subgerente.
     *
     * @Secure(roles="ROLE_347-163")
     */
    public function aprobarSolicitudAction()
    {
        $objRequest         = $this->getRequest();
        $objSession         = $objRequest->getSession();
        $objReturnResponse  = new ReturnResponse();

        $strPrefijoEmpresa      = $objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa'):"";
        $strCodEmpresa          = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa'):"";
        $strUsrCreacion         = $objSession->get('user') ? $objSession->get('user') : '';
        $strTipoPersonal        = 'Otros';
        $boolAprobarPorRango    = false;
        $floatDescPorAprobarIni = 0;
        $floatDescPorAprobarFin = 0;
        $emGeneral              = $this->getDoctrine()->getManager('telconet_general');
        $emComercial            = $this->getDoctrine()->getManager("telconet");
        $serviceUtil            = $this->get('schema.Util');
        $infoDetalleSolicitud   = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud');
        $infoDetalleSolCaract   = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract');
        $infoPersonaEmpresaRol  = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol');
        $admiCaracteristica     = $emComercial->getRepository('schemaBundle:AdmiCaracteristica');
        $infoServicioRepository = $emComercial->getRepository('schemaBundle:InfoServicio');
        $infoProductoNivel      = $emComercial->getRepository('schemaBundle:InfoProductoNivel');
        $admiTipoSolicitud      = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud');
        $infoEmpresaRol         = $emComercial->getRepository('schemaBundle:InfoEmpresaRol');
        $admiRol                = $emComercial->getRepository('schemaBundle:AdmiRol');
        $admiProducto           = $emComercial->getRepository('schemaBundle:AdmiProducto');
        $serviceEnvioPlantilla  = $this->get('soporte.EnvioPlantilla');
        $idPersona              = $objSession->get('id_empleado');
        $codEmpresa             = $objSession->get('CodEmpresa');

        $arrayParametros                                    = array();
        $arrayParametros['intIdDetalleSolicitudCab']        = $objRequest->get('intIdDetalleSolicitudCab');
        $arrayParametros['strIdSolicitudesSeleccionadas']   = $objRequest->get('strIdSolicitudesSeleccionadas');
        
        $arraySucursales = array();
        $servicioCliente = null;
        $strCuerpoCorreo = "El presente correo es para indicarle que se aprobó una solicitud en TelcoS+ con los siguientes datos:";
        $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT);
        $emComercial->getConnection()->beginTransaction();
        try
        {
            //Valida que el parámetro no se enviado vacío, caso contrario crea una excepción.
            $arraySolicitudesDescuentoDetalle               = array();
            $arraySolicitudesDescuentoDetalleAutomaticas    = array();
            $arrayIdSolicitudesAprobar                      = array();
            if(!empty($arrayParametros['strIdSolicitudesSeleccionadas']))
            {
                $arrayIdSolicitudes     = explode(",", $arrayParametros['strIdSolicitudesSeleccionadas']);
                $totalDetallesAprobadas = 0;
                /**
                 * BLOQUE QUE VALIDA LA CARACTERÍSTICA 'CARGO_GRUPO_ROLES_PERSONAL','ASISTENTE_POR_CARGO' ASOCIADA A EL USUARIO LOGONEADO
                 */
                if(!empty($strPrefijoEmpresa) && $strPrefijoEmpresa == "TN")
                {
                    $arrayLoginVendedoresKam = array();
                    $strCargosAdicionales    = ",'GERENTE_GENERAL_REGIONAL','GERENTE_GENERAL'";
                    $objCargosCab            = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                         ->findOneBy(array('nombreParametro' => self::RANGO_APROBACION_SOLICITUDES,
                                                                           'modulo'          => self::COMERCIAL,
                                                                           'proceso'         => self::ADMINISTRACION_CARGOS_SOLICITUDES,
                                                                           'estado'          => 'Activo'));
                    if(!is_object($objCargosCab) || empty($objCargosCab))
                    {
                        throw new \Exception('No se encontraron datos con los parámetros enviados.');
                    }
                    $arrayCargoPersona = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                     ->getCargosPersonas($strUsrCreacion,$strCargosAdicionales);
                    $strTipoPersonal   = (!empty($arrayCargoPersona) && is_array($arrayCargoPersona))
                                         ? $arrayCargoPersona[0]['STRCARGOPERSONAL']:'Otros';

                    if(!empty($strTipoPersonal) && $strTipoPersonal!="Otros" && $strTipoPersonal!="ASISTENTE")
                    {
                        $objCargosDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                  ->findOneBy(array('parametroId' => $objCargosCab->getId(),
                                                                    'valor3'      => $strTipoPersonal,
                                                                    'valor4'      => 'ES_JEFE',
                                                                    'estado'      => 'Activo'));
                        $floatDescPorAprobarIni  = (!empty($objCargosDet) && is_object($objCargosDet)) ? $objCargosDet->getValor1():'';
                        $floatDescPorAprobarFin  = (!empty($objCargosDet) && is_object($objCargosDet)) ? $objCargosDet->getValor2():'';
                    }
                    /**
                     * BLOQUE QUE OBTIENE EL LISTADO DE VENDEDORES KAMS
                     */
                    $arrayParametrosKam                          = array();
                    $arrayResultadoVendedoresKam                 = array();
                    $arrayParametrosKam['strPrefijoEmpresa']     = $strPrefijoEmpresa;
                    $arrayParametrosKam['strCodEmpresa']         = $strCodEmpresa;
                    $arrayParametrosKam['strEstadoActivo']       = 'Activo';
                    $arrayParametrosKam['strDescCaracteristica'] = self::CARGO_GRUPO_ROLES_PERSONAL;
                    $arrayParametrosKam['strNombreParametro']    = self::GRUPO_ROLES_PERSONAL;
                    $arrayParametrosKam['strDescCargo']          = self::GERENTE_VENTAS;
                    $arrayParametrosKam['strDescRolNoPermitido'] = self::ROLES_NO_PERMITIDOS;
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

                }
                foreach($arrayIdSolicitudes as $idSolicitud)
                {
                    //Busca la solicitud detalle por id
                    $entityDetalleSolicitudDet = $infoDetalleSolicitud->find($idSolicitud);

                    //Valida que el objeto no sea nulo.
                    if($entityDetalleSolicitudDet)
                    {
                        $intPrecioSolicitudMasiva = $this->getValorCaracteristicaDetalleSolicitud($idSolicitud, 'Precio');
                        $intProductoObsoletoId    = $this->getValorCaracteristicaDetalleSolicitud($idSolicitud, 'Producto Obsoleto');
                        
                        $infoDetalleSolicitudCaract = $infoDetalleSolCaract->findBy(array("detalleSolicitudId" => $idSolicitud));
                        $infoDetalleSolicitudDetArray = array();
                        for($intIndex = 0; $intIndex < count($infoDetalleSolicitudCaract); $intIndex++)
                        {
                            $caracteristica = $admiCaracteristica->find($infoDetalleSolicitudCaract[$intIndex]->getCaracteristicaId());
                            $label          = $caracteristica->getDescripcionCaracteristica();
                            $valor          = $infoDetalleSolicitudCaract[$intIndex]->getValor();
                            if($label != 'Archivo')
                            {
                                $infoDetalleSolicitudDetArray[$label] = $valor;
                            }
                        }
        
                        if($entityDetalleSolicitudDet->getEstado() == 'Aprobada')
                        {
                            continue;
                        }
                        
                        if($servicioCliente == null)
                        {
                            $servicioCliente = $entityDetalleSolicitudDet->getServicioId();
                        }
                        
                        $strSucursal         = $entityDetalleSolicitudDet->getServicioId()->getPuntoId()->getLogin();
                        $strSucursal        .= " (" . $entityDetalleSolicitudDet->getServicioId()->getDescripcionPresentaFactura() . ")";
                        $arraySucursales[]   = $strSucursal;
                        
                        $entityDetalleSolicitudDet->setEstado('Aprobada');
                        $emComercial->persist($entityDetalleSolicitudDet);
                        $emComercial->flush();

                        /* Historial de la Solicitud Detalle */
                        $entityInfoDetalleSolHistDet = new InfoDetalleSolHist();
                        $entityInfoDetalleSolHistDet->setDetalleSolicitudId($entityDetalleSolicitudDet);
                        $entityInfoDetalleSolHistDet->setEstado($entityDetalleSolicitudDet->getEstado());
                        $entityInfoDetalleSolHistDet->setFeCreacion(new \DateTime('now'));
                        $entityInfoDetalleSolHistDet->setUsrCreacion($objSession->get('user'));
                        $emComercial->persist($entityInfoDetalleSolHistDet);
                        $emComercial->flush();

                        $arrayCaracteristicasSolicitud = $infoDetalleSolCaract->findBy(array("detalleSolicitudId" => $entityDetalleSolicitudDet));
                        if(count($arrayCaracteristicasSolicitud) > 0)
                        {
                            foreach($arrayCaracteristicasSolicitud as $caracteristicasSolicitud):
                                $caracteristicasSolicitud->setEstado('Aprobada');
                                $caracteristicasSolicitud->setFeUltMod(new \DateTime('now'));
                                $caracteristicasSolicitud->setUsrUltMod($objSession->get('user'));
                                $emComercial->persist($caracteristicasSolicitud);
                                $emComercial->flush();
                            endforeach;
                        }

                        if($intPrecioSolicitudMasiva != null)
                        {
                            // Se verifica si la solicitud Detalle necesita solicitud de Descuento
                            $servicio = $entityDetalleSolicitudDet->getServicioId();
                            $arrayParametrosCaracteristica                  = array();
                            $arrayParametrosCaracteristica['intIdServicio'] = $servicio->getId();
                            $arrayResultadoCaract                           = $infoServicioRepository
                                ->findServiciosProductoCaracteristicas($arrayParametrosCaracteristica);
                            $caracteristicasServicio                        = $arrayResultadoCaract['registros'];

                            $arrayProductoCaracteristicasValores = array();
                            if(count($caracteristicasServicio) > 0)
                            {
                                foreach($caracteristicasServicio as $caracteristica):
                                    $strDescrCaract                                       = $caracteristica['descripcionCaracteristica'];
                                    $arrayProductoCaracteristicasValores[$strDescrCaract] = $caracteristica['valor'];
                                    if(!empty($infoDetalleSolicitudDetArray[$strDescrCaract]))
                                    {
                                        $strValorInfoDetalleSolicitudCab                      = $infoDetalleSolicitudDetArray[$strDescrCaract];
                                        $arrayProductoCaracteristicasValores[$strDescrCaract] = $strValorInfoDetalleSolicitudCab;
                                    }
                                endforeach;
                            }

                            //Se obtiene el producto del servico para saber que nivel y porcentaje de Autorizacion posee, para productos Obsoletos se 
                            //valida con el producto al cual se va a realizar el cambio.
                            $productoEntity = null;
                            if($intProductoObsoletoId == null)
                            {
                                $productoEntity = $entityDetalleSolicitudDet->getServicioId()->getProductoId();
                            }
                            else
                            {
                                $productoEntity = $admiProducto->find($intProductoObsoletoId);
                            }
                            
                            if($productoEntity)
                            {
                                $funcionPrecio = $productoEntity->getFuncionPrecio();
                                $precioMinimo  = $this->evaluarFuncionPrecio($funcionPrecio, $arrayProductoCaracteristicasValores);

                                // Se verifica si existe un Rol para un nivel de autorizacion y su respectivo porcentaje
                                $InfoProductoNivel = $infoProductoNivel->findOneBy(array("productoId" => $productoEntity));
                                $precioMinimoRol   = null;
                                $boolAutomatica    = false;
                                if($InfoProductoNivel)
                                {
                                    $empresaRolId        = $InfoProductoNivel->getEmpresaRolId();
                                    $empresaRol          = $infoEmpresaRol->find($empresaRolId);
                                    $rol                 = $admiRol->find($empresaRol->getRolId());
                                    $porcentajeDescuento = $InfoProductoNivel->getPorcentajeDescuento();
                                    //El Precio minimo permitido cambia si exite alguien designado a aprobarlo dentro del rango
                                    $precioMinimoRol     = $precioMinimo - ($precioMinimo * ($porcentajeDescuento / 100));

                                    //PARA QUE SE AÑADA AL ARRAY DE APROBACION AUTOMATICA EL QUE APRUEBA DEBE TENER EL ROL DEL $InfoProductoNivel
                                    $infoPersonaEmpresaRolSession = $infoPersonaEmpresaRol->getPersonaEmpresaRolPorPersonaPorTipoRol($idPersona, 
                                                                                                $rol->getDescripcionRol(), $codEmpresa);
                                    if($infoPersonaEmpresaRolSession)
                                    {
                                        $rolUserSession = $infoPersonaEmpresaRolSession->getEmpresaRolId()->getRolId();
                                        $boolAutomatica = ($rol == $rolUserSession);
                                    }
                                }
                                $strTipoSolicitud = $entityDetalleSolicitudDet->getTipoSolicitudId()->getDescripcionSolicitud();
                                if((!empty($strPrefijoEmpresa) && $strPrefijoEmpresa == "TN") && 
                                   (!empty($strTipoPersonal)   && $strTipoPersonal != "Otros" && $strTipoPersonal != "ASISTENTE") && 
                                   (!empty($strTipoSolicitud)  && ($strTipoSolicitud == "CAMBIO PLAN" || $strTipoSolicitud == "CAMBIO PRECIO")))
                                {
                                    $objIdServicio    = $entityDetalleSolicitudDet->getServicioId();
                                    $strLoginVendedor = $objIdServicio->getUsrVendedor() ? $objIdServicio->getUsrVendedor():"";
                                    $floatPorcentajeDescuentoTmp = 100 - ((floatval($intPrecioSolicitudMasiva) * 100)/floatval($precioMinimo));
                                    $floatPorcentajeDescuento    = abs($floatPorcentajeDescuentoTmp);
                                    $boolAprobarPorRango      = false;
                                    
                                    $boolAprobarPorRango      = ((floatval($floatPorcentajeDescuento) >= floatval($floatDescPorAprobarIni) && 
                                                                  floatval($floatPorcentajeDescuento) <=  floatval($floatDescPorAprobarFin)) ||
                                                                  ($strTipoPersonal == "GERENTE_VENTAS" &&
                                                                  (in_array($strLoginVendedor,$arrayLoginVendedoresKam) ||
                                                                  $strUsrCreacion == $strLoginVendedor) &&
                                                                  ($floatPorcentajeDescuento <=  floatval($floatDescPorAprobarFin)))) ? true : false;
                                    if($boolAprobarPorRango)
                                    {
                                        $arrayDestinatarios       = array();
                                        $objPunto                 = $objIdServicio->getPuntoId();
                                        $objPersona               = (is_object($objPunto)) ? $objPunto->getPersonaEmpresaRolId()->getPersonaId():"";
                                        $strCliente               = "";
                                        $strIdentificacion        = (is_object($objPersona)) ? $objPersona->getIdentificacionCliente():"";
                                        $strCliente               = (is_object($objPersona) && $objPersona->getRazonSocial())?
                                                                     $objPersona->getRazonSocial() : 
                                                                     $objPersona->getNombres() . " " .$objPersona->getApellidos();
                                        $strCargoAsignado         = ucwords(strtolower(str_replace("_"," ",$strTipoPersonal)));
                                        //Correo del vendedor.
                                        $arrayCorreos = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                                    ->getContactosByLoginPersonaAndFormaContacto($strLoginVendedor,
                                                                                                                 "Correo Electronico");
                                        if(!empty($arrayCorreos) && is_array($arrayCorreos))
                                        {
                                            foreach($arrayCorreos as $arrayItem)
                                            {
                                                if( !empty($arrayItem['valor']) && isset($arrayItem['valor']) )
                                                {
                                                    $arrayDestinatarios[] = $arrayItem['valor'];
                                                }
                                            }
                                        }
                                        //Correo del subgerente
                                        $arrayResultadoCorreo    = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                               ->getSubgerentePorLoginVendedor(array("strLogin"=>$strLoginVendedor));
                                        if(!empty($arrayResultadoCorreo["registros"]) && isset($arrayResultadoCorreo["registros"]))
                                        {
                                            $arrayRegistrosCorreo = $arrayResultadoCorreo['registros'];
                                            $arrayCorreos = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                           ->getContactosByLoginPersonaAndFormaContacto($arrayRegistrosCorreo[0]["LOGIN_SUBGERENTE"],
                                                                                                        "Correo Electronico");
                                            if(!empty($arrayCorreos) && is_array($arrayCorreos))
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
                                        //Correo de la persona quien crea la solicitud.
                                        $arrayCorreos = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                        ->getContactosByLoginPersonaAndFormaContacto($entityDetalleSolicitudDet->getUsrCreacion(),
                                                                                                     "Correo Electronico");
                                        if(!empty($arrayCorreos) && is_array($arrayCorreos))
                                        {
                                            foreach($arrayCorreos as $arrayItem)
                                            {
                                                if( !empty($arrayItem['valor']) && isset($arrayItem['valor']) )
                                                {
                                                    $arrayDestinatarios[] = $arrayItem['valor'];
                                                }
                                            }
                                        }
                                        $arrayParametrosMail  = array("strNombreCliente"         => $strCliente,
                                                                      "strIdentificacionCliente" => $strIdentificacion,
                                                                      "strObservacion"           => $entityDetalleSolicitudDet->getObservacion(),
                                                                      "strCuerpoCorreo"          => $strCuerpoCorreo,
                                                                      "strCargoAsignado"         => $strCargoAsignado);
                                        $serviceEnvioPlantilla->generarEnvioPlantilla("APROBACIÓN DE SOLICITUD MASIVA: ".$strTipoSolicitud,
                                                                                      array_unique($arrayDestinatarios),
                                                                                      "NOTIFICACION",
                                                                                      $arrayParametrosMail,
                                                                                      $strPrefijoEmpresa,
                                                                                      "",
                                                                                      "",
                                                                                      null,
                                                                                      true,
                                                                                      "notificaciones_telcos@telconet.ec");
                                    }
                                }
                                if($precioMinimoRol != null && $intPrecioSolicitudMasiva > $precioMinimoRol && 
                                   $intPrecioSolicitudMasiva < $precioMinimo && $boolAutomatica)
                                {
                                    $arraySolicitudesDescuentoDetalleAutomaticas[] = array(
                                                                                          "DetalleSolicitudDet" =>$entityDetalleSolicitudDet, 
                                                                                          "precioMinimo"        => $precioMinimoRol,
                                                                                          "precioSolicitud"     => $intPrecioSolicitudMasiva,
                                                                                          "boolAprobarPorRango" => $boolAprobarPorRango);
                                }
                                else if($intPrecioSolicitudMasiva < $precioMinimo)
                                {
                                    $arraySolicitudesDescuentoDetalle[] = array(
                                                                               "DetalleSolicitudDet" =>$entityDetalleSolicitudDet, 
                                                                               "precioMinimo"        => $precioMinimo,
                                                                               "precioSolicitud"     => $intPrecioSolicitudMasiva,
                                                                               "boolAprobarPorRango" => $boolAprobarPorRango);
                                }
                            }
                        }
                    }
                    $totalDetallesAprobadas++;
                }
            }
            else
            {
                $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . ' No esta enviando el/los código(s) de la(s) solicitud(es) a aprobar.');
            }

            //Busca la solicitud Cabecera por id
            $entityDetalleSolicitudCab = $infoDetalleSolicitud->find($arrayParametros['intIdDetalleSolicitudCab']);
            if($entityDetalleSolicitudCab)
            {
                /* Historial de la Solicitud Detalle */
                $entityInfoDetalleSolHistCab = new InfoDetalleSolHist();
                $entityInfoDetalleSolHistCab->setDetalleSolicitudId($entityDetalleSolicitudCab);
                $entityInfoDetalleSolHistCab->setObservacion("Se aprobaron ".$totalDetallesAprobadas." Detalles de esta Solicitud Masiva.");
                $entityInfoDetalleSolHistCab->setEstado($entityDetalleSolicitudCab->getEstado());
                $entityInfoDetalleSolHistCab->setFeCreacion(new \DateTime('now'));
                $entityInfoDetalleSolHistCab->setUsrCreacion($objSession->get('user'));
                $emComercial->persist($entityInfoDetalleSolHistCab);
                $emComercial->flush();
            }

            // Se Crea la solicitud de Descuento masiva de no existir, y de existir se crea solo el detalle adjunto a su solicitud padre.
            if(count($arraySolicitudesDescuentoDetalleAutomaticas) > 0 || count($arraySolicitudesDescuentoDetalle) > 0)
            {

                // Se busca el tipo 'SOLICITUD DESCUENTO MASIVA'
                $tipoSolicitud = $admiTipoSolicitud->findOneBy(array("descripcionSolicitud" => "SOLICITUD DESCUENTO MASIVA"));

                $caractRefSolMasiva = $admiCaracteristica->findOneBy(array("descripcionCaracteristica" => 'Referencia Solicitud Masiva'));
                $infoDetalleSolCaractRefSolMasiva = $infoDetalleSolCaract->findOneBy(array(
                                                                                        "valor" => $arrayParametros['intIdDetalleSolicitudCab'], 
                                                                                        "caracteristicaId" => $caractRefSolMasiva
                                                                                     ));
                if($infoDetalleSolCaractRefSolMasiva)
                {
                    $solicitudDescuentoCab = $infoDetalleSolCaractRefSolMasiva->getDetalleSolicitudId();
                }
                else
                {
                    //Instancia un nuevo objeto de la entidad InfoDetalleSolicitud (Cabecera) para Solicitudes de Descuento
                    $solicitudDescuentoCab = new InfoDetalleSolicitud();
                    $solicitudDescuentoCab->setTipoSolicitudId($tipoSolicitud);
                    $solicitudDescuentoCab->setObservacion("Solicitud de Descuento Masiva Automática, Código Referencia: " . $arrayParametros['intIdDetalleSolicitudCab']);
                    $solicitudDescuentoCab->setEstado('Pendiente');
                    $solicitudDescuentoCab->setFeCreacion(new \DateTime('now'));
                    $solicitudDescuentoCab->setUsrCreacion($objSession->get('user'));
                    $emComercial->persist($solicitudDescuentoCab);
                    $emComercial->flush();

                    /* Historial de la Cabecera de la Solicitud */
                    $solicitudDescuentoHistCab = new InfoDetalleSolHist();
                    $solicitudDescuentoHistCab->setDetalleSolicitudId($solicitudDescuentoCab);
                    $solicitudDescuentoHistCab->setEstado($solicitudDescuentoCab->getEstado());
                    $solicitudDescuentoHistCab->setObservacion($solicitudDescuentoCab->getObservacion());
                    $solicitudDescuentoHistCab->setFeCreacion(new \DateTime('now'));
                    $solicitudDescuentoHistCab->setUsrCreacion($solicitudDescuentoCab->getUsrCreacion());
                    $emComercial->persist($solicitudDescuentoHistCab);
                    $emComercial->flush();
                    
                    $entityInfoDetalleSolCaract = new InfoDetalleSolCaract();
                    $entityInfoDetalleSolCaract->setDetalleSolicitudId($solicitudDescuentoCab);
                    $entityInfoDetalleSolCaract->setEstado('Pendiente');
                    $entityInfoDetalleSolCaract->setCaracteristicaId($caractRefSolMasiva);
                    $entityInfoDetalleSolCaract->setValor($arrayParametros['intIdDetalleSolicitudCab']);
                    $entityInfoDetalleSolCaract->setFeCreacion(new \DateTime('now'));
                    $entityInfoDetalleSolCaract->setUsrCreacion($solicitudDescuentoCab->getUsrCreacion());
                    $emComercial->persist($entityInfoDetalleSolCaract);
                    $emComercial->flush();
                    
                    // Se crea en el historial de la solicitud Masiva un registro para q sepan que hay una solicitud de descuento masiva asociada
                    if($entityDetalleSolicitudCab)
                    {
                        /* Historial de la Solicitud Detalle */
                        $entityInfoDetalleSolHistCab = new InfoDetalleSolHist();
                        $entityInfoDetalleSolHistCab->setDetalleSolicitudId($entityDetalleSolicitudCab);
                        $entityInfoDetalleSolHistCab->setObservacion("Se creó una Solicitud Descuento Masiva con código: ".$solicitudDescuentoCab->getId().", asociada a esta Solicitud.");
                        $entityInfoDetalleSolHistCab->setEstado($entityDetalleSolicitudCab->getEstado());
                        $entityInfoDetalleSolHistCab->setFeCreacion(new \DateTime('now'));
                        $entityInfoDetalleSolHistCab->setUsrCreacion($objSession->get('user'));
                        $emComercial->persist($entityInfoDetalleSolHistCab);
                        $emComercial->flush();
                    }
                }

                if(count($arraySolicitudesDescuentoDetalle) > 0)
                {
                    foreach($arraySolicitudesDescuentoDetalle as $solicitudDetalleArray)
                    {
                        $solicitudDetalle         = $solicitudDetalleArray["DetalleSolicitudDet"];
                        $precioMinimoServicio     = $solicitudDetalleArray["precioMinimo"];
                        $intPrecioSolicitudMasiva = $solicitudDetalleArray["precioSolicitud"];
                        $boolAprobarPorRango      = $solicitudDetalleArray["boolAprobarPorRango"];

                        $servicio                 = $solicitudDetalle->getServicioId();
                        $precioDescuento          = $precioMinimoServicio - $intPrecioSolicitudMasiva;

                        //Instacia un nuevo objeto de la entidad InfoDetalleSolicitud para Detalle
                        $entityInfoDetalleSolicitudDet = new InfoDetalleSolicitud();
                        $entityInfoDetalleSolicitudDet->setServicioId($servicio);
                        $entityInfoDetalleSolicitudDet->setTipoSolicitudId($tipoSolicitud);
                        $entityInfoDetalleSolicitudDet->setPrecioDescuento($precioDescuento);
                        $entityInfoDetalleSolicitudDet->setEstado('Pendiente');
                        $entityInfoDetalleSolicitudDet->setFeCreacion(new \DateTime('now'));
                        $entityInfoDetalleSolicitudDet->setUsrCreacion($objSession->get('user'));
                        $emComercial->persist($entityInfoDetalleSolicitudDet);
                        $emComercial->flush();
                        if($boolAprobarPorRango)
                        {
                            $arrayIdSolicitudesAprobar[] = $entityInfoDetalleSolicitudDet->getId();
                        }

                        /* Historial de la Cabecera de la Solicitud */
                        $entityInfoDetalleSolHistDet = new InfoDetalleSolHist();
                        $entityInfoDetalleSolHistDet->setDetalleSolicitudId($entityInfoDetalleSolicitudDet);
                        $entityInfoDetalleSolHistDet->setEstado($entityInfoDetalleSolicitudDet->getEstado());
                        $entityInfoDetalleSolHistDet->setObservacion($entityInfoDetalleSolicitudDet->getObservacion());
                        $entityInfoDetalleSolHistDet->setFeCreacion(new \DateTime('now'));
                        $entityInfoDetalleSolHistDet->setUsrCreacion($entityInfoDetalleSolicitudDet->getUsrCreacion());
                        $emComercial->persist($entityInfoDetalleSolHistDet);
                        $emComercial->flush();

                        $caracteristica = $admiCaracteristica->findOneBy(array("descripcionCaracteristica" => "Referencia Solicitud"));

                        $entityInfoDetalleSolCaract = new InfoDetalleSolCaract();
                        $entityInfoDetalleSolCaract->setDetalleSolicitudId($entityInfoDetalleSolicitudDet);
                        $entityInfoDetalleSolCaract->setEstado('Pendiente');
                        $entityInfoDetalleSolCaract->setCaracteristicaId($caracteristica);
                        $entityInfoDetalleSolCaract->setValor($solicitudDescuentoCab->getId());
                        $entityInfoDetalleSolCaract->setFeCreacion(new \DateTime('now'));
                        $entityInfoDetalleSolCaract->setUsrCreacion($entityInfoDetalleSolicitudDet->getUsrCreacion());
                        $emComercial->persist($entityInfoDetalleSolCaract);
                        $emComercial->flush();

                        $entityInfoDetalleSolCaract = new InfoDetalleSolCaract();
                        $entityInfoDetalleSolCaract->setDetalleSolicitudId($entityInfoDetalleSolicitudDet);
                        $entityInfoDetalleSolCaract->setEstado('Pendiente');
                        $entityInfoDetalleSolCaract->setCaracteristicaId($caractRefSolMasiva);
                        $entityInfoDetalleSolCaract->setValor($solicitudDetalle->getId());
                        $entityInfoDetalleSolCaract->setFeCreacion(new \DateTime('now'));
                        $entityInfoDetalleSolCaract->setUsrCreacion($entityInfoDetalleSolicitudDet->getUsrCreacion());
                        $emComercial->persist($entityInfoDetalleSolCaract);
                        $emComercial->flush();
                        
                        // Se crea en el historial de la solicitud Detalle un registro para q sepan que hay una solicitud de descuento detalle asociada
                        $entityInfoDetalleSolHistDet = new InfoDetalleSolHist();
                        $entityInfoDetalleSolHistDet->setDetalleSolicitudId($solicitudDetalle);
                        $entityInfoDetalleSolHistDet->setObservacion("Se creó una Solicitud Descuento Detalle con código: ".$entityInfoDetalleSolicitudDet->getId()." en estado 'Pendiente', asociada a esta Solicitud.");
                        $entityInfoDetalleSolHistDet->setEstado($solicitudDetalle->getEstado());
                        $entityInfoDetalleSolHistDet->setFeCreacion(new \DateTime('now'));
                        $entityInfoDetalleSolHistDet->setUsrCreacion($objSession->get('user'));
                        $emComercial->persist($entityInfoDetalleSolHistDet);
                        $emComercial->flush();
                    }
                }
                else if(count($arraySolicitudesDescuentoDetalleAutomaticas) > 0)
                {
                    foreach($arraySolicitudesDescuentoDetalleAutomaticas as $solicitudDetalleArray)
                    {
                        $solicitudDetalle         = $solicitudDetalleArray["DetalleSolicitudDet"];
                        $precioMinimoServicio     = $solicitudDetalleArray["precioMinimo"];
                        $intPrecioSolicitudMasiva = $solicitudDetalleArray["precioSolicitud"];
                        $boolAprobarPorRango      = $solicitudDetalleArray["boolAprobarPorRango"];
                        $servicio                 = $solicitudDetalle->getServicioId();
                        $precioDescuento          = $precioMinimoServicio - $intPrecioSolicitudMasiva;

                        //Instacia un nuevo objeto de la entidad InfoDetalleSolicitud para Detalle
                        $entityInfoDetalleSolicitudDet = new InfoDetalleSolicitud();
                        $entityInfoDetalleSolicitudDet->setServicioId($servicio);
                        $entityInfoDetalleSolicitudDet->setTipoSolicitudId($tipoSolicitud);
                        $entityInfoDetalleSolicitudDet->setPrecioDescuento($precioDescuento);
                        $entityInfoDetalleSolicitudDet->setEstado('Aprobada');
                        $entityInfoDetalleSolicitudDet->setFeCreacion(new \DateTime('now'));
                        $entityInfoDetalleSolicitudDet->setUsrCreacion($objSession->get('user'));
                        $emComercial->persist($entityInfoDetalleSolicitudDet);
                        $emComercial->flush();
                        if($boolAprobarPorRango)
                        {
                            $arrayIdSolicitudesAprobar[] = $entityInfoDetalleSolicitudDet->getId();
                        }
                        /* Historial de la Cabecera de la Solicitud */
                        $entityInfoDetalleSolHistDet = new InfoDetalleSolHist();
                        $entityInfoDetalleSolHistDet->setDetalleSolicitudId($entityInfoDetalleSolicitudDet);
                        $entityInfoDetalleSolHistDet->setEstado($entityInfoDetalleSolicitudDet->getEstado());
                        $entityInfoDetalleSolHistDet->setObservacion($entityInfoDetalleSolicitudDet->getObservacion());
                        $entityInfoDetalleSolHistDet->setFeCreacion(new \DateTime('now'));
                        $entityInfoDetalleSolHistDet->setUsrCreacion($entityInfoDetalleSolicitudDet->getUsrCreacion());
                        $emComercial->persist($entityInfoDetalleSolHistDet);
                        $emComercial->flush();

                        $caracteristica = $admiCaracteristica->findOneBy(array("descripcionCaracteristica" => "Referencia Solicitud"));

                        $entityInfoDetalleSolCaract = new InfoDetalleSolCaract();
                        $entityInfoDetalleSolCaract->setDetalleSolicitudId($entityInfoDetalleSolicitudDet);
                        $entityInfoDetalleSolCaract->setEstado('Aprobada');
                        $entityInfoDetalleSolCaract->setCaracteristicaId($caracteristica);
                        $entityInfoDetalleSolCaract->setValor($solicitudDescuentoCab->getId());
                        $entityInfoDetalleSolCaract->setFeCreacion(new \DateTime('now'));
                        $entityInfoDetalleSolCaract->setUsrCreacion($entityInfoDetalleSolicitudDet->getUsrCreacion());
                        $emComercial->persist($entityInfoDetalleSolCaract);
                        $emComercial->flush();

                        $entityInfoDetalleSolCaract = new InfoDetalleSolCaract();
                        $entityInfoDetalleSolCaract->setDetalleSolicitudId($entityInfoDetalleSolicitudDet);
                        $entityInfoDetalleSolCaract->setEstado('Aprobada');
                        $entityInfoDetalleSolCaract->setCaracteristicaId($caractRefSolMasiva);
                        $entityInfoDetalleSolCaract->setValor($solicitudDetalle->getId());
                        $entityInfoDetalleSolCaract->setFeCreacion(new \DateTime('now'));
                        $entityInfoDetalleSolCaract->setUsrCreacion($entityInfoDetalleSolicitudDet->getUsrCreacion());
                        $emComercial->persist($entityInfoDetalleSolCaract);
                        $emComercial->flush();
                        
                        // Se crea en el historial de la solicitud Detalle un registro para q sepan que hay una solicitud de descuento detalle asociada
                        $entityInfoDetalleSolHistDet = new InfoDetalleSolHist();
                        $entityInfoDetalleSolHistDet->setDetalleSolicitudId($solicitudDetalle);
                        $entityInfoDetalleSolHistDet->setObservacion("Se creó una Solicitud Descuento Detalle con código: ".$entityInfoDetalleSolicitudDet->getId()." en estado 'Aprobada', asociada a esta Solicitud.");
                        $entityInfoDetalleSolHistDet->setEstado($solicitudDetalle->getEstado());
                        $entityInfoDetalleSolHistDet->setFeCreacion(new \DateTime('now'));
                        $entityInfoDetalleSolHistDet->setUsrCreacion($objSession->get('user'));
                        $emComercial->persist($entityInfoDetalleSolHistDet);
                        $emComercial->flush();
                    }
                }
            }
            $emComercial->getConnection()->commit();
            
            try
            {
                $strCliente     = "";
                $arrayTo        = array();
                $strVendedor    = "";
                
                $arrayUsuarioCorreo = $this->getPersonaNombreCorreo($entityDetalleSolicitudCab->getUsrCreacion());
                if(!empty($arrayUsuarioCorreo['correo'])){
                    $arrayTo[] = $arrayUsuarioCorreo['correo'];
                }
                
                if($servicioCliente)
                {
                    $strUsrVendedor         = $servicioCliente->getPuntoId()->getUsrVendedor();                    
                    $arrayVendedorCorreo    = $this->getPersonaNombreCorreo($strUsrVendedor);
                    if(!empty($arrayVendedorCorreo['correo'])){
                        $arrayTo[] = $arrayVendedorCorreo['correo'];
                    }
                    
                    if(!empty($arrayVendedorCorreo['nombre'])){
                        $strVendedor = $arrayVendedorCorreo['nombre'];
                    }
                    
                    $puntoIdEntity = $servicioCliente->getPuntoId();
                    if($puntoIdEntity)
                    {
                        $personaIdEntity = $puntoIdEntity->getPersonaEmpresaRolId()->getPersonaId();
                        if($personaIdEntity)                            
                        {
                            $strCliente = $personaIdEntity->__toString();
                        }
                    }
                }
                
                $strUsuarioSolicitante  = '';
                $arraySolicitanteCorreo = $this->getPersonaNombreCorreo($entityDetalleSolicitudCab->getUsrCreacion());
                if(!empty($arraySolicitanteCorreo['correo'])){
                    $arrayTo[] = $arraySolicitanteCorreo['correo'];
                }
                if(!empty($arraySolicitanteCorreo['nombre'])){
                    $strUsuarioSolicitante = $arraySolicitanteCorreo['nombre'];
                }
                
                $arrayAprobadorCorreo = $this->getPersonaNombreCorreo($objSession->get('user'));
                if(!empty($arrayAprobadorCorreo['correo'])){
                    $arrayTo[] = $arrayAprobadorCorreo['correo'];
                }
                
                $strTipoSolicitud   = $entityDetalleSolicitudCab->getTipoSolicitudId()->getDescripcionSolicitud();
                $fecha              = new \DateTime('now');
                $strUrlShow         = $this->container->getParameter('host_solicitudes') . $this->generateUrl('solicitudesmasivas_show', 
                                                                                                array('id' => $entityDetalleSolicitudCab->getId()));
                
                //Instancia del Objeto EnvioPlantilla para la notificacion
                $envioPlantilla = $this->get('soporte.EnvioPlantilla');
                // Parametros a reemplazar en el cuerpo del correo
                $arrayParametros = array('tipoSolicitud'        => $strTipoSolicitud,
                                   'codigoSolicitud'            => $entityDetalleSolicitudCab->getId(),
                                   'fecha'                      => $fecha,
                                   'usuarioSolicitante'         => $strUsuarioSolicitante,
                                   'claseSolicitud'             => 'Normal',
                                   'cliente'                    => $strCliente,
                                   'estadoSolicitud'            => $entityDetalleSolicitudCab->getEstado(),
                                   'estadoAprobacion'           => 'Aprobada',
                                   'fecha'                      => $fecha,
                                   'sucursales'                 => $arraySucursales,
                                   'vendedor'                   => $strVendedor,
                                   'url'                        => $strUrlShow
                                 );
                //Llamada a la generacion del correo de notificacion y el envio del correo
                $arrayTo = array_unique($arrayTo);
                $envioPlantilla->generarEnvioPlantilla('Autorización Comercial de '.$strTipoSolicitud.' con código '.$entityDetalleSolicitudCab->getId(),
                                   $arrayTo,
                                   'AUT_COMER_CPM',
                                   $arrayParametros,
                                   $codEmpresa,
                                   null,
                                   null);
            }
            catch(\Exception $ex)
            {
                error_log('Envio de Correo SMC-' . $entityDetalleSolicitudCab->getId() . ':' . $ex->getMessage());
            }
            
            
            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);
        }
        catch(\Exception $ex)
        {
            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . " " . $ex->getMessage());
            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
        }
        //Bloque que aprueba las solicitudes automáticamente, si se encuentra en el rango de aprobación.
        if((!empty($arrayIdSolicitudesAprobar) && is_array($arrayIdSolicitudesAprobar)) &&
           (!empty($solicitudDescuentoCab) && is_object($solicitudDescuentoCab)))
        {
            $arrayParametrosAprobSol                             = array();
            $arrayParametrosAprobSol["strUsrCreacion"]           = $objSession->get('user');
            $arrayParametrosAprobSol["strIpCreacion"]            = $objRequest->getClientIp();
            $arrayParametrosAprobSol["strCodEmpresa"]            = $objSession->get('idEmpresa');
            $arrayParametrosAprobSol["intIdDetalleSolicitudCab"] = $solicitudDescuentoCab->getId();
            $arrayParametrosAprobSol["arrayIdSolicitudes"]       = array_unique($arrayIdSolicitudesAprobar);
            $arrayResultadoAprobacion                            = $this->aprobarSolicitudDescuento($arrayParametrosAprobSol);
            if(!empty($arrayResultadoAprobacion["status"]) && isset($arrayResultadoAprobacion["status"]) &&
                $arrayResultadoAprobacion["status"] == "ERROR")
            {
                $serviceUtil->insertError('TELCOS+',
                                          'SolicitudesMasivasController.aprobarSolicitudDescuento',
                                          'Envio de Correo SMC: '. $arrayResultadoAprobacion["message"],
                                          $objSession->get('user'),
                                          $objRequest->getClientIp());
            }
        }
        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');
        $objResponse->setContent(json_encode((array) $objReturnResponse));
        return $objResponse;
    }

    /**
     * rechazarSolicitudAction, rechaza una o varias solicitudes del detalle
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 12-04-2016
     * 
     * se añadio el repositorio AdmiCaracteristica
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.1 29-07-2016
     * 
     * Se agrega rechazo de solicitudes de servicios backups
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.2 06-04-2017
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.3 17-08-2022 - Envío de notificación a la asistente, vendedor y subgerente.
     *
     * @Secure(roles="ROLE_347-94")
     */
    public function rechazarSolicitudAction()
    {        
        $objRequest = $this->getRequest();
        $objSession = $objRequest->getSession();
        $objReturnResponse = new ReturnResponse();
        $strPrefijoEmpresa = $objSession->get('prefijoEmpresa');
        $emComercial = $this->getDoctrine()->getManager("telconet");
        $infoDetalleSolicitud = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud');
        $infoDetalleSolCaract = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract');
        $serviceTecnico       = $this->get('tecnico.InfoServicioTecnico');
        $serviceEnvioPlantilla  = $this->get('soporte.EnvioPlantilla');
        $arrayParametros['intIdDetalleSolicitudCab'] = $objRequest->get('intIdDetalleSolicitudCab');
        $arrayParametros['strIdSolicitudesSeleccionadas'] = $objRequest->get('strIdSolicitudesSeleccionadas');
        $arrayParametros['strMotivo'] = $objRequest->get('strMotivo');
        $strCuerpoCorreo = "El presente correo es para indicarle que se rechazó una solicitud en TelcoS+ con los siguientes datos:";
        $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT);
        $emComercial->getConnection()->beginTransaction();
        try
        {
            //Valida que el parámetro no se enviado vacío, caso contrario crea una excepción.
            if(!empty($arrayParametros['strIdSolicitudesSeleccionadas']))
            {
                if(!empty($arrayParametros['strMotivo']))
                {

                    $arrayIdSolicitudes = explode(",", $arrayParametros['strIdSolicitudesSeleccionadas']);
                    $totalDetallesRechazadas = 0;
                    foreach($arrayIdSolicitudes as $idSolicitud)
                    {
                        //Busca la solicitud detalle por id
                        $entityDetalleSolicitudDet = $infoDetalleSolicitud->find($idSolicitud);

                        //Valida que el objeto no sea nulo.
                        if($entityDetalleSolicitudDet)
                        {
                            if($entityDetalleSolicitudDet->getEstado() == 'Rechazada')
                            {
                                continue;
                            }
                            $entityDetalleSolicitudDet->setEstado('Rechazada');
                            $emComercial->persist($entityDetalleSolicitudDet);
                            $emComercial->flush();

                            /* Historial de la Solicitud Detalle */
                            $entityInfoDetalleSolHistDet = new InfoDetalleSolHist();
                            $entityInfoDetalleSolHistDet->setDetalleSolicitudId($entityDetalleSolicitudDet);
                            $entityInfoDetalleSolHistDet->setObservacion($arrayParametros['strMotivo']);
                            $entityInfoDetalleSolHistDet->setEstado($entityDetalleSolicitudDet->getEstado());
                            $entityInfoDetalleSolHistDet->setFeCreacion(new \DateTime('now'));
                            $entityInfoDetalleSolHistDet->setUsrCreacion($objSession->get('user'));
                            $emComercial->persist($entityInfoDetalleSolHistDet);
                            $emComercial->flush();

                            $arrayCaracteristicasSolicitud = $infoDetalleSolCaract->findBy(array("detalleSolicitudId" => $entityDetalleSolicitudDet));
                            if(count($arrayCaracteristicasSolicitud) > 0)
                            {
                                foreach($arrayCaracteristicasSolicitud as $caracteristicasSolicitud):
                                    if($caracteristicasSolicitud->getCaracteristicaId()->getDescripcionCaracteristica() == 'Estado Cambio Precio')
                                    {
                                        $caracteristicasSolicitud->setValor('N/A');
                                    }
                                    $caracteristicasSolicitud->setEstado('Rechazada');
                                    $caracteristicasSolicitud->setFeUltMod(new \DateTime('now'));
                                    $caracteristicasSolicitud->setUsrUltMod($objSession->get('user'));
                                    $emComercial->persist($caracteristicasSolicitud);
                                    $emComercial->flush();
                                endforeach;
                            }
                            $totalDetallesRechazadas++;
                            
                            $objTipoSolicitud = $entityDetalleSolicitudDet->getTipoSolicitudId();
                            
                            //Variable que guarda el total de solicitudes backups rechazadas
                            $intRechazadasBackups = 0;
                            
                            if($objTipoSolicitud->getDescripcionSolicitud() == 'CAMBIO PLAN')
                            {
                                //Rechazar solicitudes de servicios backups en caso de existir
                                $arrayParametrosRechazoBackups                          = array();
                                $arrayParametrosRechazoBackups['objSolicitudPrincipal'] = $entityDetalleSolicitudDet;
                                $arrayParametrosRechazoBackups['strMotivo']             = $arrayParametros['strMotivo'];
                                $arrayParametrosRechazoBackups['strUsrCreacion']        = $objSession->get('user');
                                $intRechazadasBackups = $serviceTecnico->rechazarSolicitudCambioPlanBackups($arrayParametrosRechazoBackups);
                            }
                            
                            if($strPrefijoEmpresa == 'TN' && ($objTipoSolicitud->getDescripcionSolicitud() =="CAMBIO PLAN"
                               || $objTipoSolicitud->getDescripcionSolicitud()=="CAMBIO PRECIO"))
                            {
                                $arrayDestinatarios       = array();
                                $objServicio              = $entityDetalleSolicitudDet->getServicioId();
                                $objPunto                 = $objServicio->getPuntoId();
                                $strVendedor              = (is_object($objPunto)) ? $objPunto->getUsrVendedor():"";
                                $objPersona               = (is_object($objPunto)) ? $objPunto->getPersonaEmpresaRolId()->getPersonaId():"";
                                $strCliente               = "";
                                $strIdentificacion        = (is_object($objPersona)) ? $objPersona->getIdentificacionCliente():"";
                                $strCliente               = (is_object($objPersona) && $objPersona->getRazonSocial()) ?
                                                            $objPersona->getRazonSocial() : 
                                                            $objPersona->getNombres() . " " .$objPersona->getApellidos();
                                $strCargosAdicionales     = ",'GERENTE_GENERAL_REGIONAL','GERENTE_GENERAL'";
                                $arrayCargoPersona        = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                            ->getCargosPersonas($objSession->get('user'),$strCargosAdicionales);
                                $strTipoPersonal          = (!empty($arrayCargoPersona) && is_array($arrayCargoPersona))
                                                             ? $arrayCargoPersona[0]['STRCARGOPERSONAL']:'Otros';
                                $strCargoAsignado         = ucwords(strtolower(str_replace("_"," ",$strTipoPersonal)));
                                //Correo del vendedor.
                                $arrayCorreos = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                            ->getContactosByLoginPersonaAndFormaContacto($strVendedor,
                                                                                                         "Correo Electronico");
                                if(!empty($arrayCorreos) && is_array($arrayCorreos))
                                {
                                    foreach($arrayCorreos as $arrayItem)
                                    {
                                        if( !empty($arrayItem['valor']) && isset($arrayItem['valor']) )
                                        {
                                            $arrayDestinatarios[] = $arrayItem['valor'];
                                        }
                                    }
                                }
                                //Correo del subgerente
                                $arrayResultadoCorreo    = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                       ->getSubgerentePorLoginVendedor(array("strLogin"=>$strVendedor));
                                if(!empty($arrayResultadoCorreo["registros"]) && isset($arrayResultadoCorreo["registros"]))
                                {
                                    $arrayRegistrosCorreo = $arrayResultadoCorreo['registros'];
                                    $arrayCorreos         = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                            ->getContactosByLoginPersonaAndFormaContacto($arrayRegistrosCorreo[0]["LOGIN_SUBGERENTE"],
                                                                                                         "Correo Electronico");
                                    if(!empty($arrayCorreos) && is_array($arrayCorreos))
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
                                //Correo de la persona quien crea la solicitud.
                                $arrayCorreos = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                ->getContactosByLoginPersonaAndFormaContacto($entityDetalleSolicitudDet->getUsrCreacion(),
                                                                                             "Correo Electronico");
                                if(!empty($arrayCorreos) && is_array($arrayCorreos))
                                {
                                    foreach($arrayCorreos as $arrayItem)
                                    {
                                        if( !empty($arrayItem['valor']) && isset($arrayItem['valor']) )
                                        {
                                            $arrayDestinatarios[] = $arrayItem['valor'];
                                        }
                                    }
                                }
                                $strTipoSolicitudMv   = $objTipoSolicitud->getDescripcionSolicitud();
                                $arrayParametrosMail  = array("strNombreCliente"         => $strCliente,
                                                              "strIdentificacionCliente" => $strIdentificacion,
                                                              "strObservacion"           => $entityDetalleSolicitudDet->getObservacion(),
                                                              "strCuerpoCorreo"          => $strCuerpoCorreo,
                                                              "strCargoAsignado"         => $strCargoAsignado);
                                $serviceEnvioPlantilla->generarEnvioPlantilla("RECHAZO DE SOLICITUD MASIVA: ".$strTipoSolicitudMv,
                                                                              array_unique($arrayDestinatarios),
                                                                              "NOTIFICACION",
                                                                              $arrayParametrosMail,
                                                                              $strPrefijoEmpresa,
                                                                              "",
                                                                              "",
                                                                              null,
                                                                              true,
                                                                              "notificaciones_telcos@telconet.ec");
                            }
                            $totalDetallesRechazadas = $totalDetallesRechazadas + $intRechazadasBackups;
                        }
                    }

                    $intIdDetalleSolicitudCab = $arrayParametros['intIdDetalleSolicitudCab'];

                    $arrayParametros = array();
                    $arrayParametros['intIdEmpresa'] = ($objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : "");
                    $arrayParametros['intIdPadre'] = $intIdDetalleSolicitudCab;
                    $arrayParametros['boolMasivas'] = 'false';
                    $resultado = $infoDetalleSolicitud->findSolicitudes($arrayParametros);
                    $solicitudes = $resultado['registros'];
                    $total = $resultado['total'];

                    $totalRechazadasEliminadas = 0;
                    foreach($solicitudes as $solicitud):
                        if($solicitud['estado'] == 'Rechazada' || $solicitud['estado'] == 'Eliminada')
                        {
                            $totalRechazadasEliminadas++;
                        }
                    endforeach;
                    
                    //Busca la solicitud Cabecera por id
                    $entityDetalleSolicitudCab = $infoDetalleSolicitud->find($intIdDetalleSolicitudCab);
                    if($entityDetalleSolicitudCab)
                    {
                        /* Historial de la Solicitud Detalle */
                        $entityInfoDetalleSolHistCab = new InfoDetalleSolHist();
                        $entityInfoDetalleSolHistCab->setDetalleSolicitudId($entityDetalleSolicitudCab);
                        $entityInfoDetalleSolHistCab->setObservacion("Se rechazaron ".$totalDetallesRechazadas." Detalles de esta Solicitud Masiva.");
                        $entityInfoDetalleSolHistCab->setEstado($entityDetalleSolicitudCab->getEstado());
                        $entityInfoDetalleSolHistCab->setFeCreacion(new \DateTime('now'));
                        $entityInfoDetalleSolHistCab->setUsrCreacion($objSession->get('user'));
                        $emComercial->persist($entityInfoDetalleSolHistCab);
                        $emComercial->flush();
                    }
                        
                    // si los totales son iguales quiere decir que no queda ningun detalle pendiente o valido y la solicitud masiva se rechaza tambien                    
                    if($total == $totalRechazadasEliminadas)
                    {
                        
                        //Valida que el objeto no sea nulo.
                        if($entityDetalleSolicitudCab)
                        {
                            if($entityDetalleSolicitudCab->getEstado() != 'Rechazada')
                            {
                                $entityDetalleSolicitudCab->setEstado('Rechazada');
                                $emComercial->persist($entityDetalleSolicitudCab);
                                $emComercial->flush();

                                /* Historial de la Solicitud Detalle */
                                $entityInfoDetalleSolHistCab = new InfoDetalleSolHist();
                                $entityInfoDetalleSolHistCab->setDetalleSolicitudId($entityDetalleSolicitudCab);
                                $entityInfoDetalleSolHistCab->setObservacion($arrayParametros['strMotivo']);
                                $entityInfoDetalleSolHistCab->setEstado($entityDetalleSolicitudCab->getEstado());
                                $entityInfoDetalleSolHistCab->setFeCreacion(new \DateTime('now'));
                                $entityInfoDetalleSolHistCab->setUsrCreacion($objSession->get('user'));
                                $emComercial->persist($entityInfoDetalleSolHistCab);
                                $emComercial->flush();

                                $arrayCaracteristicasSolicitud = $infoDetalleSolCaract->findBy(array("detalleSolicitudId" => $entityDetalleSolicitudCab));
                                if(count($arrayCaracteristicasSolicitud) > 0)
                                {
                                    foreach($arrayCaracteristicasSolicitud as $caracteristicasSolicitud):
                                        $caracteristicasSolicitud->setEstado('Rechazada');
                                        $caracteristicasSolicitud->setFeUltMod(new \DateTime('now'));
                                        $caracteristicasSolicitud->setUsrUltMod($objSession->get('user'));
                                        $emComercial->persist($caracteristicasSolicitud);
                                        $emComercial->flush();
                                    endforeach;
                                }
                            }
                        }
                    }
                } else
                {
                    $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                    $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . ' Debe ingresa un Motivo de Rechazo.');
                }
            }
            else
            {
                $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . ' No esta enviando el/los código(s) de la(s) solicitud(es) a rechazar.');
            }
            $emComercial->getConnection()->commit();
            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);
        }
        catch(\Exception $ex)
        {
            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . " " . $ex->getMessage());
            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
        }
        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');
        $objResponse->setContent(json_encode((array) $objReturnResponse));
        return $objResponse;
    }

    /**
     * getDetalleSolicitudEstadoAction, obtiene el estado de la solicitud dada
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 12-04-2016
     *
     */
    public function getDetalleSolicitudEstadoAction()
    {
        $objRequest = $this->getRequest();
        $objReturnResponse = new ReturnResponse();

        $emComercial = $this->getDoctrine()->getManager("telconet");
        $infoDetalleSolicitud = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud');

        $arrayParametros['intIdDetalleSolicitud'] = $objRequest->get('intIdDetalleSolicitud');
        $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT);
        $entityDetalleSolicitudDet = $infoDetalleSolicitud->find($arrayParametros['intIdDetalleSolicitud']);
        if($entityDetalleSolicitudDet)
        {
            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
            $objReturnResponse->setStrMessageStatus($entityDetalleSolicitudDet->getEstado());
        }
        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');
        $objResponse->setContent(json_encode((array) $objReturnResponse));
        return $objResponse;
    }

    /**
     * ejecutarIndexAction, Redirecciona a la pagina de ejecucion de solicitudes.
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 16-04-2016
     * @return redireccion al index de la ejecucion de Solicitudes Masivas
     *
     * @Secure(roles="ROLE_349-1")
     */
    public function ejecutarIndexAction()
    {
        $arrayRolesPermitidos = array();
        
        $request = $this->getRequest();
        $session = $request->getSession();
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("346", "1");
        $session->set('id_menu_activo', $entityItemMenu->getId());
        
        //Modulo admiParametroCab : [349] Se debe cambiar el Id del modulo antes de pasar a producción
        if(true === $this->get('security.context')->isGranted('ROLE_349-1'))
        {
            $arrayRolesPermitidos[] = 'ROLE_349-1'; //Rol Index
        }
        if(true === $this->get('security.context')->isGranted('ROLE_349-6'))
        {
            $arrayRolesPermitidos[] = 'ROLE_349-6'; //Rol Show
        }
        if(true === $this->get('security.context')->isGranted('ROLE_349-4037'))
        {
            $arrayRolesPermitidos[] = 'ROLE_349-4037'; //Rol Ejecutar
        }

        return $this->render('comercialBundle:solicitudesmasivas:ejecutarIndex.html.twig', array('rolesPermitidos' => $arrayRolesPermitidos));
    }
    
    /**
     * ejecutarAction, Muestra los Datos de una solicitud de masiva para ser ejecutada
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 01-05-2016
     * @since 1.0
     * 
     */
    public function ejecutarAction($id)
    {
        $peticion = $this->getRequest();
        $intIdEmpresa = ($peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa') : "");
        $emComercial = $this->getDoctrine()->getManager("telconet");

        $infoDetalleSolicitudCabArray = $this->obtenerSolicitudAPresentar($emComercial, $intIdEmpresa, $id);

        $arrayRolesPermitidos = array();
        //Modulo admiParametroCab : [349] Se debe cambiar el Id del modulo antes de pasar a producción        
        if(true === $this->get('security.context')->isGranted('ROLE_349-4037'))
        {
            $arrayRolesPermitidos[] = 'ROLE_349-4037'; //Rol Ejecutar
        }
        
        return $this->render('comercialBundle:solicitudesmasivas:ejecutar.html.twig', array(
                'infoDetalleSolicitudCab' => $infoDetalleSolicitudCabArray,
                'flag' => $peticion->get('flag'),
                'rolesPermitidos' => $arrayRolesPermitidos
        ));
    }

    /**
     * uploadArchivoAction, sube un  archivo asociado a una solicitud Masiva.
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 21-04-2016
     * 
     * @author Modificado: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.1 24-06-2016 - Se añadio historial a la cabecera de la Solicitud Masiva
     * 
     * @author Modificado: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.2 28-06-2016 - Se modifico la funcion para guardar el archivo sin necesidad de la SM y
     *                           $boolUploadOk se lo puso en true en caso de error para que el json sea bien generado
     * 
     * 
     * @author Modificado: Jorge Veliz <jlveliz@telconet.ec>
     * @version 1.3 18-06-2021 - Se modifico la subida mediante el ms
     * 

     */
    public function uploadArchivoAction()
    {
        $objRequest = $this->getRequest();
        $objSession = $objRequest->getSession();

        $empresaPrefijo = $objSession->get('prefijoEmpresa');
        $intIdDetalleSolicitud = $objRequest->get('intIdDetalleSolicitud');

        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');
        $boolUploadOk = false;
        $objReturnResponse = new ReturnResponse();
        $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT);

        $emComercial = $this->getDoctrine()->getManager('telconet');
        $infoDetalleSolicitud = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud');
        $admiCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica');
        $infoDetalleSolCaract = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract');
        
        $strArchivoNuevo = '';
        $boolGuardarCaracteristica = ($intIdDetalleSolicitud != null) ? true : false;
        $objSolicitud = null;
        $caracteristica = null;
        $objSolCaract = null;
        $strUbicacionFisica = '';
        $strLogin           = $objSession->get('user');
        $serviceUtil        = $this->get('schema.Util');

        try
        {
            if($boolGuardarCaracteristica)
            {
                $objSolicitud = $infoDetalleSolicitud->find($intIdDetalleSolicitud);
                $caracteristica = $admiCaracteristica->findOneBy(array("descripcionCaracteristica" => 'Archivo'));
                $objSolCaract = $infoDetalleSolCaract->findOneBy(array("detalleSolicitudId" => $objSolicitud, "caracteristicaId" => $caracteristica));
                $emComercial->getConnection()->beginTransaction();
            }

            $fileRoot = $this->container->getParameter('ruta_upload');
            $path = $this->container->getParameter('path_telcos');
            $file = $objRequest->files;
            $objArchivo = $file->get('archivo');
            if($file && count($file) > 0)
            {
                if(isset($objArchivo))
                {
                    if($objArchivo && count($objArchivo) > 0)
                    {
                        $strAnioActual = date("Y");
                        $strMesActual = date("m");
                        $strDiaActual = date("d");
                        $archivo = $objArchivo->getClientOriginalName();
                        $arrayArchivo = explode('.', $archivo);
                        $countArray = count($arrayArchivo);
                        $midleNameFile = ($intIdDetalleSolicitud != null) ? $intIdDetalleSolicitud : $strAnioActual.$strMesActual.$strDiaActual;
                        $nombreArchivo = "SCM_" . $midleNameFile;
                        $extArchivo = $arrayArchivo[$countArray - 1];
                        $prefijo = substr(md5(uniqid(rand())), 0, 6);

                        if($archivo != "")
                        {
                            $strNuevoNombre = $nombreArchivo . "_" . $prefijo . "." . $extArchivo;
                            $strNuevoNombre = str_replace(" ", "_", $strNuevoNombre);                          
                            $strNombreApp       = 'TelcosWeb';
                            $arrayPathAdicional = [];
                            $strSubModulo = "SolicitudesMasivas";
                       
                            $arrayParamNfs          = array(
                                'prefijoEmpresa'       => $empresaPrefijo,
                                'strApp'               => $strNombreApp,
                                'strSubModulo'         => $strSubModulo,
                                'arrayPathAdicional'   => $arrayPathAdicional,
                                'strBase64'            => base64_encode(file_get_contents($objArchivo)),
                                'strNombreArchivo'     => $strNuevoNombre,
                                'strUsrCreacion'       => $strLogin);
                            $arrayRespNfs = $serviceUtil->guardarArchivosNfs($arrayParamNfs);

                            if(isset($arrayRespNfs))
                            {
                                if($arrayRespNfs['intStatus'] == 200)
                                {
                                    $strArchivoNuevo = $arrayRespNfs['strUrlArchivo'];
                                    $boolUploadOk = true;
                                }
                                else
                                {
                                    throw new \Exception('No se pudo crear el punto, error al cargar el archivo digital');
                                }

                            } 
                            else
                            {
                                throw new \Exception('Error, no hay respuesta del WS para almacenar el documento');
                            }
                        }
                    }
                }
            }
            
            if($boolGuardarCaracteristica && $boolUploadOk)
            {                
                $archivoAnterior = "";

                if($objSolCaract)
                {
                    $archivoAnterior = $objSolCaract->getValor();
                    // Se Actualiza la ruta del Archivo si ya exite el registro                                        
                    $objSolCaract->setValor($strArchivoNuevo);
                    $objSolCaract->setFeUltMod(new \DateTime('now'));
                    $objSolCaract->setUsrUltMod($objSession->get('user'));
                    $emComercial->persist($objSolCaract);
                }
                else
                {
                    //Guardar la ruta del archivo
                    $InfoDetalleSolCaract = new InfoDetalleSolCaract();
                    $InfoDetalleSolCaract->setDetalleSolicitudId($objSolicitud);
                    $InfoDetalleSolCaract->setCaracteristicaId($caracteristica);
                    $InfoDetalleSolCaract->setValor($strArchivoNuevo);
                    $InfoDetalleSolCaract->setEstado('Pendiente');
                    $InfoDetalleSolCaract->setFeCreacion(new \DateTime('now'));
                    $InfoDetalleSolCaract->setUsrCreacion($objSession->get('user'));
                    $emComercial->persist($InfoDetalleSolCaract);
                }
                $emComercial->flush();
                // Historial en la Cabecera de la Solicitud Masiva
                $mensajeAdicional = !empty($archivoAnterior) ? "Se reemplazó el archivo anterior, " : "" ;
                $entityInfoDetalleSolHistCab = new InfoDetalleSolHist();
                $entityInfoDetalleSolHistCab->setDetalleSolicitudId($objSolicitud);
                $entityInfoDetalleSolHistCab->setEstado($objSolicitud->getEstado());
                $entityInfoDetalleSolHistCab->setObservacion($mensajeAdicional . "Se Adjunto un Documento : " . $strNuevoNombre);
                $entityInfoDetalleSolHistCab->setFeCreacion(new \DateTime('now'));
                $entityInfoDetalleSolHistCab->setUsrCreacion($objSolicitud->getUsrCreacion());
                $emComercial->persist($entityInfoDetalleSolHistCab);
                $emComercial->flush();

                $emComercial->getConnection()->commit();
            }

            if($boolUploadOk)
            {
                $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
                $objReturnResponse->setStrMessageStatus("Archivo Subido Correctamente");
                $objReturnResponse->setRegistros($strArchivoNuevo);
            }
            else
            {
                // Se setea el booleano en truen para la correcta formacion del json, Adicional en el js se valida el strStatus para el procesamiento.
                $boolUploadOk = true;
                $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . " Ha ocurrido un error, por favor reporte a Sistemas");
            }
        }
        catch(\Exception $e)
        {
            if($boolGuardarCaracteristica)
            {
                if($emComercial->getConnection()->isTransactionActive())
                {
                    $emComercial->getConnection()->rollback();
                }
            }
            
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . $e->getMessage());
        }

        $resultado = '{"success":' . $boolUploadOk . ',"respuesta":' . json_encode((array) $objReturnResponse) . '}';
        $objResponse->setContent($resultado);
        return $objResponse;
    }

    /**
     * Documentación para el método 'downloadArchivoAction'.
     * Este metodo obtiene los un documento a partir de la url
     *
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 22-04-2016
     * 
     * @author Jorge Veliz <jlveliz@telconet.ec>
     * @version 1.2 22-04-2021 descarga por url del nfs

     */
    public function downloadArchivoAction()
    {
        $objRequest = $this->getRequest();
        $strUrl = $objRequest->get('strUrl');

        $arrayUrl = explode("/", $strUrl);
        
        if(isset($strUrl))
        {
            $strFile = file_get_contents($strUrl);
        }
        else
        {
            $strPath = $this->container->getParameter('path_telcos');
            $objFile = fopen($strPath . $strUrl, "r");
            $strFile = fread($objFile, filesize($strPath . $strUrl));
            fclose($strFile);
        }


        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'mime/type');
        $objResponse->headers->set('Content-Disposition', 'attachment;filename="' . $arrayUrl[sizeof($arrayUrl) - 1]);
        $objResponse->setContent($strFile);
        return $objResponse;
    }

    /**
     * descuentoIndexAction, Redirecciona a la pagina de aprobacion de solicitudes de descuento masivas.
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 28-04-2016
     * @return redireccion al index de la aprobacion de Solicitudes Masivas de descuento
     *
     * @Secure(roles="ROLE_348-1")
     */
    public function descuentoIndexAction()
    {
        $arrayRolesPermitidos = array();
        //Modulo admiParametroCab : [348] Se debe cambiar el Id del modulo antes de pasar a producción
        if(true === $this->get('security.context')->isGranted('ROLE_348-1'))
        {
            $arrayRolesPermitidos[] = 'ROLE_348-1'; //Rol Index
        }
        if(true === $this->get('security.context')->isGranted('ROLE_348-6'))
        {
            $arrayRolesPermitidos[] = 'ROLE_348-6'; //Rol Show
        }
        if(true === $this->get('security.context')->isGranted('ROLE_348-163'))
        {
            $arrayRolesPermitidos[] = 'ROLE_348-163'; //Rol Aprobar
        }        
        if(true === $this->get('security.context')->isGranted('ROLE_348-94'))
        {
            $arrayRolesPermitidos[] = 'ROLE_348-94'; //Rol Rechazar
        }

        return $this->render('comercialBundle:solicitudesmasivas:descuentoIndex.html.twig', array('rolesPermitidos' => $arrayRolesPermitidos));
    }
    
    /**
     * getEstadosDescuentoAction, Obtiene los estados de las Solicitudes Masivas
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 26-03-2016
     * @return json Retorna un json de los estados de las Solicitudes Masivas
     */
    public function getEstadosDescuentoAction()
    {
        $arrayStoreEstados[] = array('strIdEstado' => 'Pendiente', 'strNombreEstado' => 'Pendiente');
        $arrayStoreEstados[] = array('strIdEstado' => 'Finalizada', 'strNombreEstado' => 'Finalizada');
        $objResponse = new Response(json_encode(array('total' => "'" . count($arrayStoreEstados) . "'", 'jsonEstados' => $arrayStoreEstados)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    
    /**
     * getSolicitudesDescuentoMasivasAction, Obtiene listado las Solicitudes de Descuento Masivas
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 28-04-2016
     * @return json Retorna un json del listado de las Solicitudes de Descuento Masivas
     * 
     * @author Modificado: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.1 24-06-2016 - Se añadieron filtro de cliente por identificacion o razon social y se codigo referencia SM
     * 
     * @author Modificado: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.2 14-07-2016 - Se añadieron filtro de Oficina
     * 
     * @author Modificado: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.3 18-07-2016 - Se cambio la funcion de consulta para solicitudes masivas
     * 
     * 
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.4 16-12-2021 - Se realiza cambio para que la consulta de Solicitudes Masivas se realice a través de la persona en sesión
     *                           en caso de ser asistente aparecerá las Solicitudes Masivas de los vendedores asignados al asistente
     *                           en caso de ser vendedor aparecerá sus Solicitudes Masivas
     *                           en caso de ser subgerente aparecerá las Solicitudes Masivas de los vendedores que reportan al subgerente
     *                           en caso de ser gerente aparecerá todos las Solicitudes Masivas
     *                           los cambios solo aplican para Telconet.
     *
     */
    public function getSolicitudesDescuentoMasivasAction()
    {
        ini_set('max_execution_time', 60000);
        
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $arrayParametros = array();

        $peticion = $this->getRequest();

        $emComercial = $this->getDoctrine()->getManager("telconet");
        $objSession             = $peticion->getSession();
        $strUsrCreacion         = $objSession->get('user');
        $strTipoPersonal        = 'Otros';
        $strPrefijoEmpresa      = $objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa') : "";
        $strCodEmpresa          = $peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa'):"";
        $intIdPersonEmpresaRol  = $objSession->get('idPersonaEmpresaRol') ? $objSession->get('idPersonaEmpresaRol'):"";
        $strIpCreacion          = $peticion->getClientIp() ? $peticion->getClientIp():"";
        $intIdCanton            = $objSession->get('intIdCanton') ? $objSession->get('intIdCanton') : "";
        $arrayRolesNoIncluidos  = array();
        $serviceUtilidades      = $this->get('administracion.Utilidades');
        /**
         * BLOQUE QUE VALIDA LA CARACTERÍSTICA 'CARGO_GRUPO_ROLES_PERSONAL','ASISTENTE_POR_CARGO' ASOCIADA A EL USUARIO LOGONEADO 
         */
        if(!empty($strPrefijoEmpresa) && $strPrefijoEmpresa == "TN")
        {
            $strCargosAdicionales = ",'GERENTE_GENERAL_REGIONAL','GERENTE_GENERAL'";
            $arrayCargoPersona    = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                ->getCargosPersonas($strUsrCreacion,$strCargosAdicionales);
            $strTipoPersonal   = (!empty($arrayCargoPersona) && is_array($arrayCargoPersona))
                                    ? $arrayCargoPersona[0]['STRCARGOPERSONAL']:'Otros';
            /**
             * BLOQUE QUE OBTIENE LA REGIÓN EN SESIÓN Y LOS PARÁMETROS NECESARIOS PARA FILTRAR POR REGIÓN
             */
            $objCanton             = $emComercial->getRepository("schemaBundle:AdmiCanton")
                                                 ->find($intIdCanton);
            $strRegionSesion       = $objCanton->getRegion();
            $arrayParametrosRoles  = array( 'strCodEmpresa'     => $strCodEmpresa,
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
        }
        $infoDetalleSolicitud = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud');
        $infoDetalleSolCaract = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract');
        $admiCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica');
        $admiTipoSolicitud = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud');
        
        $tipoSolicitud = $admiTipoSolicitud->findOneBy(array('descripcionSolicitud' => 'SOLICITUD DESCUENTO MASIVA'));
        
        $arrayParametros['intIdEmpresa'] = ($peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa') : "");
        $arrayParametros['strFechaDesdeIngOrd'] = explode('T', $peticion->query->get('fechaDesdeIngOrd'));
        $arrayParametros['strFechaHastaIngOrd'] = explode('T', $peticion->query->get('fechaHastaIngOrd'));
        $arrayParametros['strLogin'] = $peticion->query->get('txtLogin');
        $arrayParametros['strCiudad'] = $peticion->query->get('txtCiudad');
        $arrayParametros['strEstado'] = $peticion->query->get('cboEstados');
        $arrayParametros['strCodigoSolicitud'] = $peticion->query->get('txtCodigo');
         $arrayParametros['txtCodigoSolicitudMasiva'] = $peticion->query->get('txtCodigoSolicitudMasiva');
        $arrayParametros['intIdProducto'] = $peticion->query->get('cboProductos');
        $arrayParametros['intIdTipoSolicitud'] = $tipoSolicitud->getId();
        $arrayParametros['boolMasivas'] = $peticion->query->get('boolMasivas');
        $arrayParametros['txtClienteIdentificacion'] = $peticion->query->get('txtClienteIdentificacion');
        $arrayParametros['intStart'] = $peticion->query->get('start');
        $arrayParametros['intLimit'] = $peticion->query->get('limit');
        
        $arrayParametros['intIdPuntoCobertura'] = $peticion->query->get('cboOficinas');
        $arrayParametros['strPrefijoEmpresa']   = $strPrefijoEmpresa;
        $arrayParametros['strTipoPersonal']     = $strTipoPersonal;
        $arrayParametros['strRegion']           = $strRegionSesion;
        $arrayParametros['arrayRolNoPermitido'] = (!empty($arrayRolesNoIncluidos) && is_array($arrayRolesNoIncluidos))?$arrayRolesNoIncluidos:"";
        $arrayParametros['intIdPersonEmpresaRol'] = $intIdPersonEmpresaRol;
        $arrayResultado = $infoDetalleSolicitud->findSolicitudesMasivas($arrayParametros);
        $solicitudes = $arrayResultado['registros'];
        $total = $arrayResultado['total'];

        //Si existen comprobantes
        if(count($solicitudes) > 0)
        {
            foreach($solicitudes as $solicitud):
                $arraySolicitudes[] = array(
                    'intIdSolicitud' => $solicitud['idDetalleSolicitud'],
                    'intIdSolicitudReferencia' => $solicitud['idDetalleSolicitudReferencia'],
                    'strCliente' => $solicitud['cliente'],
                    'strLogin' => $solicitud['login'],
                    'strTipoSolicitud' => $solicitud['descripcionSolicitud'],
                    'strFeCreacion' => $solicitud['feCreacion'],
                    'strObservacion' => $solicitud['observacion'],
                    'strUsrVendedor' => $solicitud['usrVendedor'],
                    'strVendedor' => $solicitud['nombreVendedor'],
                    'strProducto' => $solicitud['producto'],
                    'strUsrCreacion' => $solicitud['usrCreacion'],
                    'strEstado' => $solicitud['estado']
                );
            endforeach;
        }

        if($total > 0)
        {
            $data = json_encode($arraySolicitudes);
            $objJson = '{"total":"' . $total . '","jsonSolicitudesMasivas":' . $data . '}';
        }
        else
        {
            $objJson = '{"total":"0","jsonSolicitudesMasivas":[]}';
        }
        $respuesta->setContent($objJson);
        return $respuesta;
    }
    
    /**
     * aprobarRechazarDescuentoAction, Muestra los Datos de una solicitud de descuento masiva para ser aprobada o rechazada
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 29-04-2016
     * @since 1.0
     * 
     */
    public function aprobarRechazarDescuentoAction($id)
    {
        $peticion = $this->getRequest();
        $intIdEmpresa = ($peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa') : "");
        $emComercial = $this->getDoctrine()->getManager("telconet");

        $infoDetalleSolicitudCabArray = $this->obtenerSolicitudAPresentar($emComercial, $intIdEmpresa, $id);

        $arrayRolesPermitidos = array();
        //Modulo admiParametroCab : [348] Se debe cambiar el Id del modulo antes de pasar a producción        
        if(true === $this->get('security.context')->isGranted('ROLE_348-163'))
        {
            $arrayRolesPermitidos[] = 'ROLE_348-163'; //Rol Aprobar
        }
        if(true === $this->get('security.context')->isGranted('ROLE_348-94'))
        {
            $arrayRolesPermitidos[] = 'ROLE_348-94'; //Rol Rechazar
        }

        return $this->render('comercialBundle:solicitudesmasivas:aprobarRechazarDescuento.html.twig', array(
                'infoDetalleSolicitudCab' => $infoDetalleSolicitudCabArray,
                'flag' => $peticion->get('flag'),
                'rolesPermitidos' => $arrayRolesPermitidos
        ));
    }
    
    /**
     * getDetalleSolicitudDetDescuentoAction, Obtiene listado detalles de Solicitudes
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 30-03-2016
     * @return json Retorna un json del listado detalles de Solicitudes de Descuento masivas
     * 
     * @author Modificado: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.1 05-08-2016 - Consulta Para mostrar en la solicitud de descuesto el producto al cual se cambia en el caso de cambio de plan de
     *                           Producto Obsoletos.
     * 
     * @author Modificado: Edgar Holguín <eholguin@telconet.ec>
     * @version 1.2 15-10-2018 - Se agrega validación para poder visualizar y aprobar solicitudes de descuento a servicios que no poseen UM. 
     */
    public function getDetalleSolicitudDetDescuentoAction()
    {
        ini_set('max_execution_time', 60000);
        
        $peticion  = $this->getRequest();
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');        

        $emComercial                    = $this->getDoctrine()->getManager("telconet");
        $emInfraestructura              = $this->getDoctrine()->getManager("telconet_infraestructura");
        $infoDetalleSolicitudRepository = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud');
        $infoServicioRepository         = $emComercial->getRepository('schemaBundle:InfoServicio');
        $infoServicioTecnico            = $emComercial->getRepository('schemaBundle:InfoServicioTecnico');
        $admiProducto                   = $emComercial->getRepository('schemaBundle:AdmiProducto');
        $admiTipoMedio                  = $emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio');

        $arrayParametros                    = array();
        $arrayParametros['intIdEmpresa']    = ($peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa') : "");
        $arrayParametros['intIdPadre']      = $peticion->query->get('intIdDetalleSolicitudCab');        
        $arrayParametros['strEstado']       = $peticion->query->get('strEstado');
        $arrayParametros['boolMasivas']     = 'false';
        $arrayParametros['intStart']        = $peticion->query->get('start');
        $arrayParametros['intLimit']        = $peticion->query->get('limit');
        
        $resultado      = $infoDetalleSolicitudRepository->findSolicitudes($arrayParametros);
        $solicitudes    = $resultado['registros'];
        $total          = $resultado['total'];
        
        //Si existen comprobantes
        if(count($solicitudes) > 0)
        {
            foreach($solicitudes as $solicitud):
                $intPrecioNuevo                 = 0;
                $strDescripcionProductoNuevo    = "";
                $intPrecioMinimo                = 0;
                $intProductoObsoletoId          = null;
                //Se agrega solicitudes al arreglo de solicitudes
                $servicio       = $infoServicioRepository->find($solicitud['idServicio']);
                $productoEntity = $servicio->getProductoId();
                
                $idSolMasivaRef = $this->getValorCaracteristicaDetalleSolicitud($solicitud['idDetalleSolicitud'], 'Referencia Solicitud Masiva');
                if($idSolMasivaRef)
                {
                    $intPrecioNuevo         = $this->getValorCaracteristicaDetalleSolicitud($idSolMasivaRef, 'Precio');
                    $intProductoObsoletoId  = $this->getValorCaracteristicaDetalleSolicitud($idSolMasivaRef, 'Producto Obsoleto');
                    if($intProductoObsoletoId)
                    {
                        $admiProductoEntity = $admiProducto->find($intProductoObsoletoId);
                        if($admiProductoEntity)
                        {
                            $strDescripcionProductoNuevo = $admiProductoEntity->getDescripcionProducto();
                        }
                    }
                }
                
                $strDescripcionServicio  = "<b>Descripción Servicio: </b><br>" . $servicio->getDescripcionPresentaFactura();
                $strDescripcionServicio .=  "<br><b>Producto: </b>" . $productoEntity->getDescripcionProducto();
                if(!empty($strDescripcionProductoNuevo))
                {
                    $strDescripcionServicio .=  "<br><b>Producto Cambio: </b>" . $strDescripcionProductoNuevo;
                }
                $strDescripcionServicio .=  "<br><b>Login Aux: </b><br>" . $servicio->getLoginAux();
                
                $infoServicioTecnicoEntity  = $infoServicioTecnico->findOneBy(array("servicioId" => $solicitud['idServicio']));
                $strNombreTipoMedio         = "";
                if($infoServicioTecnicoEntity && $infoServicioTecnicoEntity->getUltimaMillaId())
                {
                    $admiTipoMedioEntity = $admiTipoMedio->find($infoServicioTecnicoEntity->getUltimaMillaId());                
                    if($admiTipoMedioEntity){
                        $strNombreTipoMedio = $admiTipoMedioEntity->getNombreTipoMedio();
                    }
                }
                $strDescripcionServicio .=  "<br><b>Última Milla: </b><br>" . $strNombreTipoMedio;
                                
                $intPrecioMinimo = $this->getPrecioMinimoServicioSolicitud($emComercial, $solicitud['idServicio'], 
                                                                           $idSolMasivaRef,$intProductoObsoletoId);

                $arraySolicitudes[] = array(
                                            'intIdSolicitud'            => $solicitud['idDetalleSolicitud'],
                                            'intIdServicio'             => $solicitud['idServicio'],
                                            'strDescripcionServicio'    => $strDescripcionServicio,
                                            'intIdPunto'                => $solicitud['idPunto'],
                                            'strLogin'                  => $servicio->getPuntoId()->getLogin(),
                                            'intIdProdructo'            => $productoEntity->getId(),
                                            'strdescripcionProducto'    => $productoEntity->getDescripcionProducto(),
                                            'strPrecioVenta'            => $servicio->getPrecioVenta(),
                                            'strEstado'                 => $solicitud['estado'],
                                            'strObservacion'            => $solicitud['observacion'],
                                            'intPrecioDescuento'        => $solicitud['precioDescuento'],
                                            'intPrecioActual'           => $servicio->getPrecioVenta(),
                                            'intPrecioNuevo'            => $intPrecioNuevo,
                                            'intPrecioMinimo'           => $intPrecioMinimo,
                                            'intPorcentajeDescuento'    => $solicitud['porcentajeDescuento']
                                            );
            endforeach;
        }

        if($total > 0)
        {
            $data = json_encode($arraySolicitudes);
            $objJson = '{"total":"' . $total . '","jsonServiciosSeleccionados":' . $data . '}';
        }
        else
        {
            $objJson = '{"total":"0","jsonServiciosSeleccionados":[]}';
        }
        $respuesta->setContent($objJson);
        return $respuesta;
    }
    
    /**
     * getPrecioMinimoServicioSolicitud, rechaza una o varias solicitudes de descuento del detalle
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 21-06-2016
     *
     * @param Object emComercial Entity manager para acceder a la DB
     * @param int intIdServicio Identificador del servicio
     * @param int idSolMasivaRef Identificador de la solicitud masiva referencial
     * @param int intIdProducto Identificador del Producto Obsoleto a realizar el cambio
     * @return int Retorna un precio del producto segun las caracteristicas del servicio
     * 
     * @author Modificado: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.1 05-08-2016 - Se aumento del parametro intIdProducto para consultar el precio minimo de otro producto en caso de Producto Obsoleto
     * 
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.2 12-01-2022 - Se agrega validación para el cálculo del precio mínimo.
     */
    private function getPrecioMinimoServicioSolicitud($emComercial, $intIdServicio, $idSolMasivaRef, $intIdProducto = null)
    {
        $intPrecioMinimo = 0;
        $intCapacidad1   = "";
        $intCapacidad2   = "";
        $boolCapacidad   = false;
        $strTipoSolicitud= "";
        try
        {
            $infoServicioRepository         = $emComercial->getRepository('schemaBundle:InfoServicio');
            $infoDetalleSolCaractRepository = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract');
            $admiCaracteristicaRepository   = $emComercial->getRepository('schemaBundle:AdmiCaracteristica');
            $admiProductoRepository         = $emComercial->getRepository('schemaBundle:AdmiProducto');
            $objSolicitud                   = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud');
            $ServicioEntity                 = $infoServicioRepository ->find($intIdServicio);
            if(!empty($idSolMasivaRef))
            {
                $objInfoSolicitud = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->find($idSolMasivaRef);
                $strTipoSolicitud = is_object($objInfoSolicitud) ? $objInfoSolicitud->getTipoSolicitudId()->getDescripcionSolicitud():"";
            }
            if($ServicioEntity)
            {    

                $caractCapacidad1 = $admiCaracteristicaRepository->findOneBy(array("descripcionCaracteristica" => 'CAPACIDAD1'));
                if($caractCapacidad1)
                {
                    $infoDetalleSolCaractRefSolMasiva1 = $infoDetalleSolCaractRepository->findOneBy(array("detalleSolicitudId" => $idSolMasivaRef, "caracteristicaId" => $caractCapacidad1));
                    if($infoDetalleSolCaractRefSolMasiva1)
                    {
                        $intCapacidad1 = $infoDetalleSolCaractRefSolMasiva1->getValor();
                    }                
                }

                $caractCapacidad2 = $admiCaracteristicaRepository->findOneBy(array("descripcionCaracteristica" => 'CAPACIDAD2'));
                if($caractCapacidad2)
                {
                    $infoDetalleSolCaractRefSolMasiva2 = $infoDetalleSolCaractRepository->findOneBy(array("detalleSolicitudId" => $idSolMasivaRef, "caracteristicaId" => $caractCapacidad2));
                    if($infoDetalleSolCaractRefSolMasiva2)
                    {
                        $intCapacidad2 = $infoDetalleSolCaractRefSolMasiva2->getValor();
                    }                
                }

                $arrayParametrosCaracteristica                  = array();
                $arrayParametrosCaracteristica['intIdServicio'] = $ServicioEntity->getId();
                
                $arrayResultadoCaract       = $infoServicioRepository->findServiciosProductoCaracteristicas($arrayParametrosCaracteristica);                
                $caracteristicasServicio    = $arrayResultadoCaract['registros'];
                
                $arrayProductoCaracteristicasValores = array();
                if(count($caracteristicasServicio) > 0)
                {
                    foreach($caracteristicasServicio as $caracteristica):

                        $arrayProductoCaracteristicasValores[$caracteristica['descripcionCaracteristica']] = $caracteristica['valor'];
                        if($caracteristica['descripcionCaracteristica'] == 'CAPACIDAD1')
                        {
                            if(!empty($intCapacidad1))
                            {
                                $boolCapacidad = true;
                                $arrayProductoCaracteristicasValores[$caracteristica['descripcionCaracteristica']] = $intCapacidad1;
                            }                            
                        } else if($caracteristica['descripcionCaracteristica'] == 'CAPACIDAD2')
                        {
                            if(!empty($intCapacidad2))
                            {
                                $boolCapacidad = true;
                                $arrayProductoCaracteristicasValores[$caracteristica['descripcionCaracteristica']] = $intCapacidad2;
                            }
                        }
                    endforeach;
                }

                $productoEntity = null;
                if($intIdProducto == null)
                {
                    $productoEntity = $ServicioEntity->getProductoId();
                }
                else
                {
                    $productoEntity = $admiProductoRepository->find($intIdProducto);
                }
                
                if(is_object($productoEntity) && !empty($arrayProductoCaracteristicasValores) && $boolCapacidad)
                {
                    // Validacion del Nivel de Aprobacion del Precio
                    $funcionPrecio      = $productoEntity->getFuncionPrecio();
                    $intPrecioMinimo    = $this->evaluarFuncionPrecio($funcionPrecio, $arrayProductoCaracteristicasValores);
                }
                elseif(is_object($productoEntity) && !empty($arrayProductoCaracteristicasValores) && $strTipoSolicitud == "CAMBIO PRECIO")
                {
                    $intPrecioMinimo    = $this->evaluarFuncionPrecio($productoEntity->getFuncionPrecio(), $arrayProductoCaracteristicasValores);
                }
            }        
        }
        catch(\Exception $ex)
        {
            error_log("Calculo funcionPrecio: " . $ex->getMessage());
        }
        return $intPrecioMinimo;
    }
    
    /**
     * rechazarSolicitudDescuentoAction, rechaza una o varias solicitudes de descuento del detalle
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 30-04-2016
     *
     * @Secure(roles="ROLE_348-94")
     */
    public function rechazarSolicitudDescuentoAction()
    {
        $objRequest = $this->getRequest();
        $objSession = $objRequest->getSession();
        $objReturnResponse = new ReturnResponse();

        $emComercial = $this->getDoctrine()->getManager("telconet");
        $infoDetalleSolicitud = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud');
        $infoDetalleSolCaract = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract');
        $admiCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica');

        $arrayParametros['intIdDetalleSolicitudCab'] = $objRequest->get('intIdDetalleSolicitudCab');
        $arrayParametros['strIdSolicitudesSeleccionadas'] = $objRequest->get('strIdSolicitudesSeleccionadas');
        $arrayParametros['strMotivo'] = $objRequest->get('strMotivo');

        $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT);
        $emComercial->getConnection()->beginTransaction();
        try
        
        {
            //Valida que el parámetro no se enviado vacío, caso contrario crea una excepción.
            if(!empty($arrayParametros['strIdSolicitudesSeleccionadas']))
            {
                if(!empty($arrayParametros['strMotivo']))
                {

                    $arrayIdSolicitudes = explode(",", $arrayParametros['strIdSolicitudesSeleccionadas']);
                    $totalDetallesRechzadas = 0;
                    foreach($arrayIdSolicitudes as $idSolicitud)
                    {
                        //Busca la solicitud detalle por id
                        $entityDetalleSolicitudDet = $infoDetalleSolicitud->find($idSolicitud);

                        //Valida que el objeto no sea nulo.
                        if($entityDetalleSolicitudDet)
                        {
                            if($entityDetalleSolicitudDet->getEstado() == 'Rechazada')
                            {
                                continue;
                            }
                            $entityDetalleSolicitudDet->setEstado('Rechazada');
                            $emComercial->persist($entityDetalleSolicitudDet);
                            $emComercial->flush();

                            /* Historial de la Solicitud Detalle */
                            $entityInfoDetalleSolHistDet = new InfoDetalleSolHist();
                            $entityInfoDetalleSolHistDet->setDetalleSolicitudId($entityDetalleSolicitudDet);
                            $entityInfoDetalleSolHistDet->setObservacion($arrayParametros['strMotivo']);
                            $entityInfoDetalleSolHistDet->setEstado($entityDetalleSolicitudDet->getEstado());
                            $entityInfoDetalleSolHistDet->setFeCreacion(new \DateTime('now'));
                            $entityInfoDetalleSolHistDet->setUsrCreacion($objSession->get('user'));
                            $emComercial->persist($entityInfoDetalleSolHistDet);
                            $emComercial->flush();

                            $arrayCaracteristicasSolicitud = $infoDetalleSolCaract->findBy(array("detalleSolicitudId" => $entityDetalleSolicitudDet));
                            if(count($arrayCaracteristicasSolicitud) > 0)
                            {
                                foreach($arrayCaracteristicasSolicitud as $caracteristicasSolicitud):
                                    $caracteristicasSolicitud->setEstado('Rechazada');
                                    $caracteristicasSolicitud->setFeUltMod(new \DateTime('now'));
                                    $caracteristicasSolicitud->setUsrUltMod($objSession->get('user'));
                                    $emComercial->persist($caracteristicasSolicitud);
                                    $emComercial->flush();
                                endforeach;
                            }
                            
                            // Se busca la referencia de la solicitud Masiva detalle para cambiar el 'Estado Cambio Precio'
                            $caracteristicaRefSolMasiva = $admiCaracteristica->findOneBy(array("descripcionCaracteristica" => "Referencia Solicitud Masiva"));
                            $infoDetalleSolCaractRefSolMasiva = $infoDetalleSolCaract->findOneBy(array("detalleSolicitudId" => $entityDetalleSolicitudDet, "caracteristicaId" => $caracteristicaRefSolMasiva));
                            $intIdReferenciaSolMasivaDetalle = $infoDetalleSolCaractRefSolMasiva->getValor();
                            $caracteristicaEstadoCambioPrecio = $admiCaracteristica->findOneBy(array("descripcionCaracteristica" => "Estado Cambio Precio"));                            
                            $infoDetalleSolCaractEstadoCambioPrecio = $infoDetalleSolCaract->findOneBy(array("detalleSolicitudId" => $intIdReferenciaSolMasivaDetalle, "caracteristicaId" => $caracteristicaEstadoCambioPrecio));
                            $infoDetalleSolCaractEstadoCambioPrecio->setValor('Rechazada');
                            $infoDetalleSolCaractEstadoCambioPrecio->setFeUltMod(new \DateTime('now'));
                            $infoDetalleSolCaractEstadoCambioPrecio->setUsrUltMod($objSession->get('user'));
                            $emComercial->persist($infoDetalleSolCaractEstadoCambioPrecio);
                            $emComercial->flush();
                            
                            // Se crea en el historial de la solicitud Detalle un registro para q sepan que la solicitud de descuento detalle asociada cambio de estado
                            $solicitudDetalle = $infoDetalleSolicitud->find($intIdReferenciaSolMasivaDetalle);
                            if($solicitudDetalle)
                            {
                                $entityInfoDetalleSolHistDet = new InfoDetalleSolHist();
                                $entityInfoDetalleSolHistDet->setDetalleSolicitudId($solicitudDetalle);
                                $entityInfoDetalleSolHistDet->setObservacion("Se rechazó la Solicitud Descuento Detalle con código: ".$entityDetalleSolicitudDet->getId().", asociada a esta Solicitud.");
                                $entityInfoDetalleSolHistDet->setEstado($solicitudDetalle->getEstado());
                                $entityInfoDetalleSolHistDet->setFeCreacion(new \DateTime('now'));
                                $entityInfoDetalleSolHistDet->setUsrCreacion($objSession->get('user'));
                                $emComercial->persist($entityInfoDetalleSolHistDet);
                                $emComercial->flush();
                            }
                            $totalDetallesRechzadas++;
                        }
                    }

                    $detalleSolicitudCab = $infoDetalleSolicitud->find($arrayParametros['intIdDetalleSolicitudCab']);
                    if($detalleSolicitudCab)
                    {
                        /* Historial de la Solicitud Detalle */
                        $entityInfoDetalleSolHistCab = new InfoDetalleSolHist();
                        $entityInfoDetalleSolHistCab->setDetalleSolicitudId($detalleSolicitudCab);
                        $entityInfoDetalleSolHistCab->setObservacion("Se rechazaron ".$totalDetallesRechzadas." Detalles de esta Solicitud Descuento Masiva.");
                        $entityInfoDetalleSolHistCab->setEstado($detalleSolicitudCab->getEstado());
                        $entityInfoDetalleSolHistCab->setFeCreacion(new \DateTime('now'));
                        $entityInfoDetalleSolHistCab->setUsrCreacion($objSession->get('user'));
                        $emComercial->persist($entityInfoDetalleSolHistCab);
                        $emComercial->flush();

                        // Se cuenta el total de detalles en estado pendientes y si es cero se Finaliza la Cabecera
                        $arrayParametrosDetalle = array();
                        $arrayParametrosDetalle['intIdEmpresa'] = ($objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : "");
                        $arrayParametrosDetalle['intIdPadre'] = $detalleSolicitudCab->getId();
                        $arrayParametrosDetalle['strEstado'] = 'Pendiente';
                        $arrayParametrosDetalle['boolMasivas'] = 'false';
                        $resultado = $infoDetalleSolicitud->findSolicitudes($arrayParametrosDetalle);
                        $totalDetallesPendientes = $resultado['total'];

                        if($totalDetallesPendientes == 0)
                        {
                            $detalleSolicitudCab->setEstado('Finalizada');
                            $emComercial->persist($detalleSolicitudCab);
                            $emComercial->flush();

                            /* Historial de la Solicitud Detalle */
                            $entityInfoDetalleSolHistCab = new InfoDetalleSolHist();
                            $entityInfoDetalleSolHistCab->setDetalleSolicitudId($detalleSolicitudCab);
                            $entityInfoDetalleSolHistCab->setEstado('Finalizada');
                            $entityInfoDetalleSolHistCab->setFeCreacion(new \DateTime('now'));
                            $entityInfoDetalleSolHistCab->setUsrCreacion($objSession->get('user'));
                            $emComercial->persist($entityInfoDetalleSolHistCab);
                            $emComercial->flush();
                        }
                    }     
                } else
                {
                    $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                    $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . ' Debe ingresa un Motivo de Rechazo.');
                }
            }
            else
            {
                $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . ' No esta enviando el/los código(s) de la(s) solicitud(es) a rechazar.');
            }
            $emComercial->getConnection()->commit();
            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);
        }
        catch(\Exception $ex)
        {
            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . " " . $ex->getMessage());
            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
        }
        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');
        $objResponse->setContent(json_encode((array) $objReturnResponse));
        return $objResponse;
    }
    
    /**
     * aprobarSolicitudDescuentoAction, aprueba una o varias solicitudes del detalle
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 01-05-2016
     * 
     * @author Modificado: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.1 11-07-2016 - Notificacion via correo electronico
     * 
     * @author Modificado: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.2 13-08-2016 - Consulta de caracteristicas por detalle
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.3 30-12-2021 - Se reestructura la función de aprobación.
     *
     * @Secure(roles="ROLE_348-163")
     */
    public function aprobarSolicitudDescuentoAction()
    {
        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $objReturnResponse      = new ReturnResponse();
        $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT);
        try
        {
            $arrayParametros = array();
            $arrayParametros["strUsrCreacion"]                = $objSession->get('user');
            $arrayParametros["strIpCreacion"]                 = $objRequest->getClientIp();
            $arrayParametros["strCodEmpresa"]                 = $objSession->get('idEmpresa');
            $arrayParametros["intIdDetalleSolicitudCab"]      = $objRequest->get('intIdDetalleSolicitudCab');
            $arrayParametros["arrayIdSolicitudes"]            = explode(",", $objRequest->get('strIdSolicitudesSeleccionadas'));
            $arrayResultadoAprobacion                         = $this->aprobarSolicitudDescuento($arrayParametros);
            if(!empty($arrayResultadoAprobacion["status"]) && isset($arrayResultadoAprobacion["status"]) &&
                    $arrayResultadoAprobacion["status"] == "ERROR")
            {
                throw new \Exception($arrayResultadoAprobacion["message"]);
            }
            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);
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

    /**
     * Documentación para la función 'aprobarSolicitudDescuento'.
     *
     * Función que aprueba solicitudes masivas de descuento.
     *
     * @param array $arrayParametros [
     *                                  "strUsrCreacion"                => Login del usuario en sesión.
     *                                  "strIpCreacion"                 => IP del usuario en sesión.
     *                                  "strCodEmpresa"                 => Código de la empresa.
     *                                  "intIdDetalleSolicitudCab"      => Cabecera de la solicitud.
     *                                  "arrayIdSolicitudes"            => Solicitudes de detalle.
     *                               ]
     *
     * @return array $arrayResultado [
     *                                 "message"        =>  mensaje de confirmación o de error.
     *                                 "status"         =>  estado de la petición.
     *                               ]
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 30-12-2021
     *
     */
    public function aprobarSolicitudDescuento($arrayParametros)
    {
        $strUsrCreacion               = (!empty($arrayParametros["strUsrCreacion"])) ? $arrayParametros["strUsrCreacion"]: "";
        $strIpCreacion                = (!empty($arrayParametros["strIpCreacion"]))  ? $arrayParametros["strIpCreacion"]: '127.0.0.1';
        $strCodEmpresa                = (!empty($arrayParametros["strCodEmpresa"]))  ? $arrayParametros["strCodEmpresa"]: "";
        $emComercial                  = $this->getDoctrine()->getManager("telconet");
        $serviceUtil                  = $this->get('schema.Util');
        $arrayRespuesta               = array();
        $arraySucursales              = array();
        $strStatus                    = "EXITO";
        $intTotalDetallesPendientes   = 0;
        $intTotalDetallesCPPendientes = 0;
        try
        {
            $emComercial->getConnection()->beginTransaction();
            if(empty($arrayParametros["intIdDetalleSolicitudCab"]) || empty($arrayParametros["arrayIdSolicitudes"]))
            {
                throw new \Exception('No está enviando el/los código(s) de la(s) solicitud(es) a aprobar.');
            }
            $objCaracRefSolMasiva       = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                      ->findOneBy(array("descripcionCaracteristica" => "Referencia Solicitud Masiva"));
            if(empty($objCaracRefSolMasiva) || !is_object($objCaracRefSolMasiva))
            {
                throw new \Exception('No existe característica: Referencia Solicitud Masiva');
            }
            $objCaracEstadoCambioPrecio = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                      ->findOneBy(array("descripcionCaracteristica" => "Estado Cambio Precio"));
            if(empty($objCaracRefSolMasiva) || !is_object($objCaracRefSolMasiva))
            {
                throw new \Exception('No existe característica: Estado Cambio Precio');
            }
            $intTotalDetSolAprobadas = 0;
            foreach($arrayParametros["arrayIdSolicitudes"] as $arrayItemSolicitud)
            {
                $intIdSolicitudReferencia = $this->getValorCaracteristicaDetalleSolicitud($arrayItemSolicitud, "Referencia Solicitud Masiva");
                $intPrecioNuevo           = (!empty($intIdSolicitudReferencia)) ? 
                                            $this->getValorCaracteristicaDetalleSolicitud($intIdSolicitudReferencia, "Precio") : 0;
                $objDetalleSolicitudDet   = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                        ->find($arrayItemSolicitud);
                if(!empty($objDetalleSolicitudDet) && is_object($objDetalleSolicitudDet))
                {
                    if($objDetalleSolicitudDet->getEstado() == 'Aprobada')
                    {
                        continue;
                    }
                    $objServicio = $objDetalleSolicitudDet->getServicioId();
                    if(!empty($objServicio) && is_object($objServicio))
                    {
                        $arraySucursales[] = array( 'punto'            => $objServicio->getPuntoId()->getLogin(),
                                                    'servicioAfectado' => $objServicio->getDescripcionPresentaFactura(),
                                                    'precioActual'     => $objServicio->getPrecioVenta(),
                                                    'precioNuevo'      => $intPrecioNuevo);
                    }
                    $objDetalleSolicitudDet->setEstado('Aprobada');
                    $emComercial->persist($objDetalleSolicitudDet);
                    $emComercial->flush();
                    //Ingresamos historial de la Solicitud Detalle.
                    $objInfoDetalleSolHistDet = new InfoDetalleSolHist();
                    $objInfoDetalleSolHistDet->setDetalleSolicitudId($objDetalleSolicitudDet);
                    $objInfoDetalleSolHistDet->setEstado($objDetalleSolicitudDet->getEstado());
                    $objInfoDetalleSolHistDet->setFeCreacion(new \DateTime('now'));
                    $objInfoDetalleSolHistDet->setUsrCreacion($strUsrCreacion);
                    $emComercial->persist($objInfoDetalleSolHistDet);
                    $emComercial->flush();
                    //Recorremos todas las característica de la Solicitud Detalle para aprobarlas.
                    $arrayCaracteristicasSolicitud = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                 ->findBy(array("detalleSolicitudId" => $objDetalleSolicitudDet));
                    if(!empty($arrayCaracteristicasSolicitud) && is_array($arrayCaracteristicasSolicitud) && count($arrayCaracteristicasSolicitud)>0)
                    {
                        foreach($arrayCaracteristicasSolicitud as $arrayItemCaracSolicitud)
                        {
                            $arrayItemCaracSolicitud->setEstado('Aprobada');
                            $arrayItemCaracSolicitud->setFeUltMod(new \DateTime('now'));
                            $arrayItemCaracSolicitud->setUsrUltMod($strUsrCreacion);
                            $emComercial->persist($arrayItemCaracSolicitud);
                            $emComercial->flush();
                        }
                    }
                    //Se Busca el la referencia de la solicitud masiva detalle para cambiarle el estado a su característica "Estado cambio Precio".
                    $objDetalleSolCaractRefSolMasiva       = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                          ->findOneBy(array("detalleSolicitudId" => $objDetalleSolicitudDet,
                                                                                            "caracteristicaId"   => $objCaracRefSolMasiva));
                    if(!empty($objDetalleSolCaractRefSolMasiva) && is_object($objDetalleSolCaractRefSolMasiva))
                    {
                        $arraySolCaractEstadoCambioPrecio                       = array();
                        $arraySolCaractEstadoCambioPrecio["detalleSolicitudId"] = $objDetalleSolCaractRefSolMasiva->getValor();
                        $arraySolCaractEstadoCambioPrecio["caracteristicaId"]   = $objCaracEstadoCambioPrecio;
                        $objDetalleSolCaractEstadoCambioPrecio = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                             ->findOneBy($arraySolCaractEstadoCambioPrecio);
                        if(!empty($objDetalleSolCaractEstadoCambioPrecio) && is_object($objDetalleSolCaractEstadoCambioPrecio))
                        {
                            $objDetalleSolCaractEstadoCambioPrecio->setValor('Aprobada');
                            $objDetalleSolCaractEstadoCambioPrecio->setFeUltMod(new \DateTime('now'));
                            $objDetalleSolCaractEstadoCambioPrecio->setUsrUltMod($strUsrCreacion);
                            $emComercial->persist($objDetalleSolCaractEstadoCambioPrecio);
                            $emComercial->flush();
                        }
                        //Ingresamos historial de la Solicitud Detalle.para tener conocimiento que cambió de estado.
                        $objSolicitudDetalle = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                           ->find($objDetalleSolCaractRefSolMasiva->getValor());
                        if(!empty($objSolicitudDetalle) && is_object($objSolicitudDetalle))
                        {
                            $objInfoDetalleSolHistDet = new InfoDetalleSolHist();
                            $objInfoDetalleSolHistDet->setDetalleSolicitudId($objSolicitudDetalle);
                            $objInfoDetalleSolHistDet->setObservacion("Se aprobó la Solicitud Descuento Detalle con código: ".
                                                                      $objDetalleSolicitudDet->getId().", asociada a esta Solicitud.");
                            $objInfoDetalleSolHistDet->setEstado($objSolicitudDetalle->getEstado());
                            $objInfoDetalleSolHistDet->setFeCreacion(new \DateTime('now'));
                            $objInfoDetalleSolHistDet->setUsrCreacion($strUsrCreacion);
                            $emComercial->persist($objInfoDetalleSolHistDet);
                            $emComercial->flush();
                        }
                    }
                    $intTotalDetSolAprobadas++;
                }
            }

            $objDetalleSolicitudCab = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                  ->find($arrayParametros["intIdDetalleSolicitudCab"]);
            if(!empty($objDetalleSolicitudCab) && is_object($objDetalleSolicitudCab))
            {
                //Ingresamos historial de la Solicitud Detalle.
                $objInfoDetalleSolHistCab = new InfoDetalleSolHist();
                $objInfoDetalleSolHistCab->setDetalleSolicitudId($objDetalleSolicitudCab);
                $objInfoDetalleSolHistCab->setObservacion("Se aprobaron ".$intTotalDetSolAprobadas.
                                                          " Detalles de esta Solicitud Descuento Masiva.");
                $objInfoDetalleSolHistCab->setEstado($objDetalleSolicitudCab->getEstado());
                $objInfoDetalleSolHistCab->setFeCreacion(new \DateTime('now'));
                $objInfoDetalleSolHistCab->setUsrCreacion($strUsrCreacion);
                $emComercial->persist($objInfoDetalleSolHistCab);
                $emComercial->flush();
                //Se cuenta el total de detalles en estado pendientes y si es cero se Finaliza la Cabecera
                $arrayParametrosDetalle                 = array();
                $arrayParametrosDetalle['intIdEmpresa'] = $strCodEmpresa;
                $arrayParametrosDetalle['intIdPadre']   = $objDetalleSolicitudCab->getId();
                $arrayParametrosDetalle['strEstado']    = 'Pendiente';
                $arrayParametrosDetalle['boolMasivas']  = 'false';
                $arrayResultado                         = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                      ->findSolicitudes($arrayParametrosDetalle);
                $intTotalDetallesPendientes             = $arrayResultado['total'];

                $objDetalleSolCaractRefSolMasiva = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                               ->findOneBy(array("detalleSolicitudId" => $objDetalleSolicitudCab,
                                                                                 "caracteristicaId"   => $objCaracRefSolMasiva));
                if(!empty($objDetalleSolCaractRefSolMasiva) && is_object($objDetalleSolCaractRefSolMasiva))
                {
                    $arrayParametrosEstados                          = array();
                    $arrayParametrosEstados['strEstadoCambioPrecio'] = 'Pendiente';
                    $arrayParametrosEstados['idDetalleSolicitud']    = $objDetalleSolCaractRefSolMasiva->getValor();
                    $arrayResultadoEstados                           = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                                   ->findTotalSolicitudesPorEstado($arrayParametrosEstados);
                    $intTotalDetallesCPPendientes                    = $arrayResultadoEstados['total'];
                }
                if($intTotalDetallesPendientes == 0 && $intTotalDetallesCPPendientes == 0)
                {
                    $objDetalleSolicitudCab->setEstado('Finalizada');
                    $emComercial->persist($objDetalleSolicitudCab);
                    $emComercial->flush();
                    $objInfoDetalleSolHistCab = new InfoDetalleSolHist();
                    $objInfoDetalleSolHistCab->setDetalleSolicitudId($objDetalleSolicitudCab);
                    $objInfoDetalleSolHistCab->setEstado('Finalizada');
                    $objInfoDetalleSolHistCab->setFeCreacion(new \DateTime('now'));
                    $objInfoDetalleSolHistCab->setUsrCreacion($strUsrCreacion);
                    $emComercial->persist($objInfoDetalleSolHistCab);
                    $emComercial->flush();
                }
            }
            $emComercial->getConnection()->commit();
            try
            {
                $strCliente                       = "";
                $arrayTo                          = array();
                $objDetalleSolicitudCabReferencia = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->find($intIdSolicitudReferencia);
                if(!empty($objDetalleSolicitudCabReferencia) && is_object($objDetalleSolicitudCabReferencia))
                {
                   $arrayUsuarioCorreoRef = $this->getPersonaNombreCorreo($objDetalleSolicitudCabReferencia->getUsrCreacion());
                    if(!empty($arrayUsuarioCorreoRef['correo']))
                    {
                        $arrayTo[] = $arrayUsuarioCorreoRef['correo'];
                    }
                }
                $arrayUsuarioCorreo = $this->getPersonaNombreCorreo($objDetalleSolicitudCab->getUsrCreacion());
                if(!empty($arrayUsuarioCorreo['correo']))
                {
                    $arrayTo[] = $arrayUsuarioCorreo['correo'];
                }
                $objPunto = $objServicio->getPuntoId();
                if(!empty($objPunto) && is_object($objPunto))
                {
                    $objPersona = $objPunto->getPersonaEmpresaRolId()->getPersonaId();
                    if(!empty($objPersona) && is_object($objPersona))
                    {
                        $strCliente         = '';
                        $arrayClienteCorreo = $this->getPersonaNombreCorreoByInfoPersona($objPersona);
                        
                        if(!empty($arrayClienteCorreo['nombre']))
                        {
                            $strCliente = $arrayClienteCorreo['nombre'];
                        }
                    }
                }
                $strUsuarioSolicitante     = '';
                $objDetalleSolicitudCab    = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                         ->find($arrayParametros["intIdDetalleSolicitudCab"]);
                $arraySolicitanteCorreo    = $this->getPersonaNombreCorreo($objDetalleSolicitudCab->getUsrCreacion());
                if(!empty($arraySolicitanteCorreo['correo']))
                {
                    $arrayTo[] = $arraySolicitanteCorreo['correo'];
                }
                if(!empty($arraySolicitanteCorreo['nombre']))
                {
                    $strUsuarioSolicitante = $arraySolicitanteCorreo['nombre'];
                }
                $strTipoSolicitud      = $objDetalleSolicitudCab->getTipoSolicitudId()->getDescripcionSolicitud();
                $strUrlShow            = $this->container->getParameter('host_solicitudes') . 
                                         $this->generateUrl('aprobacionsolicitudesdescuentomasivas_aprobarRechazar', 
                                                            array('id' => $objDetalleSolicitudCab->getId()));
                $serviceEnvioPlantilla = $this->get('soporte.EnvioPlantilla');
                $arrayParametrosCorreo = array('tipoSolicitud'         => $strTipoSolicitud,
                                               'codigoSolicitud'       => $objDetalleSolicitudCab->getId(),
                                               'fecha'                 => new \DateTime('now'),
                                               'usuarioSolicitante'    => $strUsuarioSolicitante,
                                               'cliente'               => $strCliente,
                                               'sucursales'            => $arraySucursales,
                                               'url'                   => $strUrlShow);
                $arrayTo = array_unique($arrayTo);
                $serviceEnvioPlantilla->generarEnvioPlantilla('Solicitud de ' . $strTipoSolicitud . ' #'. $objDetalleSolicitudCab->getId(),
                                                              $arrayTo,
                                                              'AUT_GEREN_CPM',
                                                              $arrayParametrosCorreo,
                                                              $strCodEmpresa,
                                                              null,
                                                              null);
            }
            catch(\Exception $ex)
            {
                $serviceUtil->insertError('TELCOS+',
                                          'SolicitudesMasivasController.aprobarSolicitudDescuento',
                                          'Envio de Correo SMC: '. $ex->getMessage(),
                                          $strUsrCreacion,
                                          $strIpCreacion);
            }
            $arrayRespuesta['status']  = $strStatus;
            $arrayRespuesta['message'] = "Se aprobaron las solicitudes correctamente.";
        }
        catch(\Exception $ex)
        {
            $arrayRespuesta['status']  = "ERROR";
            $arrayRespuesta['message'] = $ex->getMessage();
            if($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->rollback();
                $emComercial->getConnection()->close();
            }
            $serviceUtil->insertError('TELCOS+',
                                      'SolicitudesMasivasController.aprobarSolicitudDescuento',
                                      $ex->getMessage(),
                                      $strUsrCreacion,
                                      $strIpCreacion);
        }
        return $arrayRespuesta;
    }

    /**
     * getSolicitudesMasivasEjecucionAction, Obtiene listado las Solicitudes Masivas para ejecucion
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 01-04-2016
     * @return json Retorna un json del listado de las Solicitudes Masivas para ejecucion
     * 
     * @author Modificado: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.1 24-06-2016 - Se añadieron filtro de cliente por identificacion o razon social
     * 
     * @author Modificado: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.2 14-07-2016 - Se añadieron filtro de Oficina
     * 
     * @author Modificado: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.3 19-07-2016 - Se cambio la funcion de consulta de solicitudes masivas
     * 
     */
    public function getSolicitudesMasivasEjecucionAction()
    {
        ini_set('max_execution_time', 60000);
        
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $arrayParametros = array();

        $peticion = $this->getRequest();
        $session = $peticion->getSession();

        $emComercial = $this->getDoctrine()->getManager("telconet");
        $infoDetalleSolicitud = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud');

        $itemMenuId = $session->get('id_menu_activo');
        if(!empty($itemMenuId))
        {
            $arrayParametros['intIdItemMenu'] = $itemMenuId;
        }
        $arrayParametros['intIdEmpresa'] = ($peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa') : "");
        $arrayParametros['strFechaDesdePlanif'] = explode('T', $peticion->query->get('fechaDesdePlanif'));
        $arrayParametros['strFechaHastaPlanif'] = explode('T', $peticion->query->get('fechaHastaPlanif'));
        $arrayParametros['strFechaDesdeIngOrd'] = explode('T', $peticion->query->get('fechaDesdeIngOrd'));
        $arrayParametros['strFechaHastaIngOrd'] = explode('T', $peticion->query->get('fechaHastaIngOrd'));
        $arrayParametros['strLogin'] = $peticion->query->get('txtLogin');
        $arrayParametros['strDescripcionPunto'] = $peticion->query->get('txtDescripcionPunto');
        $arrayParametros['strVendedor'] = $peticion->query->get('txtVendedor');
        $arrayParametros['strCiudad'] = $peticion->query->get('txtCiudad');
        $arrayParametros['intIdTipoSolicitud'] = $peticion->query->get('cboTipoSolicitud');
        $arrayParametros['strEstado'] = $peticion->query->get('cboEstados');
        $arrayParametros['strCodigoSolicitud'] = $peticion->query->get('txtCodigo');
        $arrayParametros['intIdProducto'] = $peticion->query->get('cboProductos');
        $arrayParametros['boolMasivas'] = $peticion->query->get('boolMasivas');
        $arrayParametros['txtClienteIdentificacion'] = $peticion->query->get('txtClienteIdentificacion');
        
        $arrayParametros['boolReqArchivo'] = $peticion->query->get('boolReqArchivo');
        $arrayParametros['boolReqAprobPrecio'] = $peticion->query->get('boolReqAprobPrecio');
        $arrayParametros['boolReqAprobRadio'] = $peticion->query->get('boolReqAprobRadio');
        $arrayParametros['boolReqAprobIpccl2'] = $peticion->query->get('boolReqAprobIpccl2');
        $arrayParametros['strEstadoDetalles'] = $peticion->query->get('strEstadoDetalles');
        
        $arrayParametros['strEstadoAprobPrecio'] = $peticion->query->get('strEstadoAprobPrecio');
        $arrayParametros['strEstadoAprobRadio'] = $peticion->query->get('strEstadoAprobRadio');
        $arrayParametros['strEstadoAprobIpccl2'] = $peticion->query->get('strEstadoAprobIpccl2');
        
        if($arrayParametros['strEstado'] != 'Pendiente')
        {
            $arrayParametros['strEstadoDetalles'] = null;
        }
        
        $arrayParametros['intStart'] = $peticion->query->get('start');
        $arrayParametros['intLimit'] = $peticion->query->get('limit');
        
        $arrayParametros['intIdPuntoCobertura'] = $peticion->query->get('cboOficinas');
        $arrayParametros['totalesExtra'] = true;

        $arrayResultado = $infoDetalleSolicitud->findSolicitudesMasivas($arrayParametros);
        $solicitudes = $arrayResultado['registros'];
        $total = $arrayResultado['total'];

        //Si existen comprobantes
        if(count($solicitudes) > 0)
        {
            foreach($solicitudes as $solicitud):
                $arraySolicitudes[] = array(
                    'intIdSolicitud'                    => $solicitud['idDetalleSolicitud'],            
                    'strCliente'                        => $solicitud['cliente'],
                    'strTipoSolicitud'                  => $solicitud['descripcionSolicitud'],                    
                    'strFeCreacion'                     => $solicitud['feCreacion'],
                    'strProducto'                       => $solicitud['producto'],
                    'strUsrCreacion'                    => $solicitud['usrCreacion'],
                    'strEstado'                         => $solicitud['estado'],
                    'strArchivo'                        => $solicitud['archivo'],
                    'intTotalDetallesAprobados'         => $solicitud['totalAprobadas'],
                    'intTotalDetallesRechazadas'        => $solicitud['totalRechazadas'],
                    'intTotalDetallesEnProceso'         => $solicitud['totalProceso'],
                    'intTotalDetallesFallos'            => $solicitud['totalFallo'],
                    'intTotalDetallesEliminada'         => $solicitud['totalEliminada'],
                    'intTotalDetallesFinalizada'        => $solicitud['totalFinalizadas'],
                    'intTotalDetalles'                  => $solicitud['totalDetalles'],
                    'intTotalDetallesCPAprobados'       => $solicitud['totalDetallesCPAprobados'],
                    'intTotalDetallesCPNA'              => $solicitud['totalDetallesCPNA'],
                    'intTotalDetallesRadioAprobados'    => $solicitud['totalDetallesRadioAprobados'],
                    'intTotalDetallesRadioNA'           => $solicitud['totalDetallesRadioNA'],
                    'intTotalDetallesIpccl2Aprobados'   => $solicitud['totalDetallesIpccl2Aprobados'],
                    'intTotalDetallesIpccl2NA'          => $solicitud['totalDetallesIpccl2NA']
                );
            endforeach;
        }

        if($total > 0)
        {
            $data = json_encode($arraySolicitudes);
            $objJson = '{"total":"' . $total . '","jsonSolicitudesMasivas":' . $data . '}';
        }
        else
        {
            $objJson = '{"total":"0","jsonSolicitudesMasivas":[]}';
        }
        $respuesta->setContent($objJson);
        return $respuesta;
    }
    
    
    /**
     * ejecutarSolicitudAction, aprueba una o varias solicitudes del detalle
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 01-05-2016
     * 
     * @author Modificado: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.1 11-07-2016 - Notificacion via correo electronico
     * 
     * @author Modificado: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.2 26-07-2016 - Finalizacion logica del cambio de plan si no es enlace el producto
     * 
     * @author Modificado: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.3 05-08-2016 - Se añadio la ejecucion para cambio de plan de productos obsoletos
     * 
     * @author Modificado: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.4 13-08-2016 - Consulta de caracteristicas por detalle y finalizacion de solicitudes de descuento
     *
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.5 01-09-2016 - Se realizan ajustes para que las solicitudes de cancelaciones pasen a estado finalizada automáticamente
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.6 29-09-2016 - Se realizan ajustes para que las notificaciones sean enviadas únicamanente a los correos de los contactos
     *                           comerciales del punto, además del usuario de creación
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.7 23-12-2016 - Se crea un historial al servicio con accion 'confirmoCambioPrecio' cuando se realiza un cambio de plan y el nuevo 
     *                           precio es mayor que el precio anterior. El historial creado será usado para generar la factura proporcional 
     *                           correspondiente por aumento de ancho de banda.
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.8 11-01-2017 - Se crea un historial al servicio con accion 'confirmoCambioPrecio' cuando se realiza un cambio de precio al
     *                           servicios, y el nuevo precio es mayor que el precio anterior. El historial creado será usado para generar la factura
     *                           proporcional correspondiente por cambio de precio.
     * 
     * @author Modificado: Allan Suarez <arsuarez@telconet.ec>
     * @version 1.9 26-04-2017 - Se agrega codigo para crear procesos masivos de servicios backups solo de tipo CAMBIO PLAN
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.0 20-07-2017 - Se realizan ajustes para que el proceso tome en cuenta las nuevas solicitudes de Demo
     * 
     * @author Modificado: Edgar Holguin <eholguin@telconet.ec>
     * @version 2.1 06-09-2017 - Se agrega seteo de campo descuento unitario en aprobación de solicitud de descuento masiva.
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.2 08-03-2018 -  Se obtiene valores de velocidad actual y nueva de los servicios Internet Small Business 
     *                            para el envío de notificaciones  
     *
     * @author Modificado: Jesús Bozada <jbozada@telconet.ec>
     * @version 2.3 23-03-2018 - Se agrega cambio de estado ERROR a procesosmasivosdet de servicios que se van a reintentar el procesamiento
     * @since 2.2
     *
     * Se fijan los valores de descuentos en 0 cuando es cambio de plan y cambio de precio.
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 2.4
     * @since 30-05-2018
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.5 04-02-2019 Se obtiene valores de velocidad actual y nueva de los servicios TelcoHome para el envío de notificaciones 
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 2.6 14-01-2020 Se corrige al momento de obtener los valores de velocidad anterior para Internet Small Business y Telco Home.
     *
     * @Secure(roles="ROLE_349-4037")
     */
    public function ejecutarSolicitudAction()
    {        
        $objRequest         = $this->getRequest();
        $objSession         = $objRequest->getSession();
        $objReturnResponse  = new ReturnResponse();

        $emComercial        = $this->getDoctrine()->getManager("telconet");     
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');        
        
        $infoDetalleSolicitud           = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud');
        $infoDetalleSolCaract           = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract');
        $admiProducto                   = $emComercial->getRepository('schemaBundle:AdmiProducto');
        $admiProductoCaracteristica     = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica');
        $infoServicioProdCaract         = $emComercial->getRepository('schemaBundle:InfoServicioProdCaract');
        $admiTipoSolicitud              = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud');
        $admiCaracteristica             = $emComercial->getRepository('schemaBundle:AdmiCaracteristica');
        $infoProcesoMasivoCab           = $emInfraestructura->getRepository('schemaBundle:InfoProcesoMasivoCab');
        $codEmpresa                     = $objSession->get('idEmpresa');

        $arrayParametros['intIdDetalleSolicitudCab']        = $objRequest->get('intIdDetalleSolicitudCab');
        $arrayParametros['strIdSolicitudesSeleccionadas']   = $objRequest->get('strIdSolicitudesSeleccionadas');
        $serviceTecnico                                     = $this->get('tecnico.InfoServicioTecnico');
        
        $entityDetalleSolicitudCab  = $infoDetalleSolicitud->find($arrayParametros['intIdDetalleSolicitudCab']);
        $entityTipoSolicitud        = $entityDetalleSolicitudCab->getTipoSolicitudId();
        
        $servicioCliente        = null;
        $arraySucursales        = array();        
        $tipoProceso            = "";
        $boolCambioPrecioMasivo = false;
        $boolCambioPlanMasivo   = false;
        $boolEnceraDescuentos   = false;
        
        if(strpos($entityTipoSolicitud->getDescripcionSolicitud(),"CANCELACION") !== false)
        {
            $tipoProceso            = "CancelarCliente";
        }
        else if(strpos($entityTipoSolicitud->getDescripcionSolicitud(),"CAMBIO PLAN") !== false)
        {
            $tipoProceso            = "CambioPlanMasivo";
            $boolCambioPlanMasivo   = true;
            $boolEnceraDescuentos   = true;
        }
        else if(strpos($entityTipoSolicitud->getDescripcionSolicitud(),"CAMBIO PRECIO") !== false)
        {
            $tipoProceso            = "CambioPrecioMasivo";
            $boolCambioPrecioMasivo = true;
            $boolEnceraDescuentos   = true;
        }
        else if(strpos($entityTipoSolicitud->getDescripcionSolicitud(),"DEMOS") !== false)
        {
            $tipoProceso          = "Demos";
            $boolCambioPlanMasivo = true;
        }        
        
        $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT);
        $emComercial->getConnection()->beginTransaction();
        $emInfraestructura->getConnection()->beginTransaction();
        try
        {            
            $arraySolicitudesDetalle = array();
            if(!empty($arrayParametros['strIdSolicitudesSeleccionadas']))
            {
                $arrayIdSolicitudes = explode(",", $arrayParametros['strIdSolicitudesSeleccionadas']);
                foreach($arrayIdSolicitudes as $idSolicitud)
                {
                    $boolesEnlace         = false;
                    $anteriorCapacidad1   = null;
                    $anteriorCapacidad2   = null;
                    $strVelocidadAnterior = null;
                    
                    //Busca la solicitud detalle por id
                    $entityDetalleSolicitudDet = $infoDetalleSolicitud->find($idSolicitud);
                    
                    //Valida que el objeto no sea nulo.
                    if($entityDetalleSolicitudDet)
                    {
                        // Consulta de caracteristicas por detalle
                        $nuevoPrecioMasivo = $this->getValorCaracteristicaDetalleSolicitud($idSolicitud, 'Precio');
                        $nuevaCapacidad1   = $this->getValorCaracteristicaDetalleSolicitud($idSolicitud, 'CAPACIDAD1');
                        $nuevaCapacidad2   = $this->getValorCaracteristicaDetalleSolicitud($idSolicitud, 'CAPACIDAD2');
                        $strVelocidadNueva = $this->getValorCaracteristicaDetalleSolicitud($idSolicitud, 'VELOCIDAD');
                        if(isset($strVelocidadNueva) && !empty($strVelocidadNueva))
                        {
                            $strBwNuevo = $strVelocidadNueva;
                        }
                        else
                        {
                            $strVelocidadNueva = $this->getValorCaracteristicaDetalleSolicitud($idSolicitud, 'VELOCIDAD_TELCOHOME');
                            if(isset($strVelocidadNueva) && !empty($strVelocidadNueva))
                            {
                                $strBwNuevo = $strVelocidadNueva;
                            }
                            else
                            {
                                $strBwNuevo = $nuevaCapacidad1 . '/' . $nuevaCapacidad2;
                            }
                        }
                        
                        $nuevoProducto     = $this->getValorCaracteristicaDetalleSolicitud($idSolicitud, 'Producto Obsoleto');
                    
                        if($entityDetalleSolicitudDet->getEstado() == 'EnProceso' || $entityDetalleSolicitudDet->getEstado() == 'Finalizada')
                        {
                            continue;
                        }
                            
                        $entityServicio = $entityDetalleSolicitudDet->getServicioId();                        
                        if($entityServicio)
                        {
                            if($servicioCliente == null)
                            {
                                $servicioCliente = $entityServicio;
                            }
                            $entityPunto = $entityServicio->getPuntoId();
                            $punto = $entityPunto->getLogin();
                            
                            $servicioAfectado = $entityServicio->getDescripcionPresentaFactura();
                            $precioActual = $entityServicio->getPrecioVenta();
                            
                            $anteriorCapacidad1 = $this->getValorCaracteristicaServicio($entityServicio, 'CAPACIDAD1');
                            $anteriorCapacidad2 = $this->getValorCaracteristicaServicio($entityServicio, 'CAPACIDAD2');
                            
                            $entityProducto    = $entityServicio->getProductoId();
                            if($entityProducto)
                            {
                                if($entityProducto->getNombreTecnico() == 'INTERNET SMALL BUSINESS')
                                {
                                    $strVelocidadAnterior = $this->getValorCaracteristicaServicio($entityServicio, 'VELOCIDAD');
                                }
                                else if($entityProducto->getNombreTecnico() == 'TELCOHOME')
                                {
                                    $strVelocidadAnterior = $this->getValorCaracteristicaServicio($entityServicio, 'VELOCIDAD_TELCOHOME');
                                }
                                
                                if($entityProducto->getEsEnlace() == 'SI')
                                {
                                    $boolesEnlace = true;
                                }
                            }
                            if(isset($strVelocidadAnterior) && !empty($strVelocidadAnterior))
                            {
                                $strBwActual = $strVelocidadAnterior;
                            }
                            else
                            {
                                $strBwActual = $anteriorCapacidad1 . '/' . $anteriorCapacidad2;
                            }
                            
                            $arraySucursales[] = array(
                                                      'punto'           => $punto,
                                                      'servicio'        => $servicioAfectado,
                                                      'precioActual'    => $precioActual,
                                                      'bwActual'        => $strBwActual,
                                                      'precioNuevo'     => $nuevoPrecioMasivo,
                                                      'bwNuevo'         => $strBwNuevo
                                                      );
                            if ($boolEnceraDescuentos)
                            {
                                // En caso de ser Servicios migrados se asigna a cero los valores de descuento dado que ya se asigno el nuevo precio
                                if($entityServicio->getPorcentajeDescuento() > 0 ||
                                   $entityServicio->getValorDescuento()      > 0 ||
                                   $entityServicio->getDescuentoUnitario()   > 0)
                                {
                                    $entityServicio->setDescuentoUnitario(0);
                                    $entityServicio->setPorcentajeDescuento(0);
                                    $entityServicio->setValorDescuento(0);
                                    $emComercial->persist($entityServicio);
                                    $emComercial->flush();

                                    //historial del servicio
                                    $entityServicioHistorial = new InfoServicioHistorial();
                                    $entityServicioHistorial->setServicioId($entityServicio);
                                    $entityServicioHistorial->setObservacion("Se cambió a cero el descuento en el servicio por " .
                                            "la ejecución de " . $tipoProceso);
                                    $entityServicioHistorial->setEstado($entityServicio->getEstado());
                                    $entityServicioHistorial->setUsrCreacion($objSession->get('user'));
                                    $entityServicioHistorial->setFeCreacion(new \DateTime('now'));
                                    $entityServicioHistorial->setIpCreacion($objRequest->getClientIp());
                                    $emComercial->persist($entityServicioHistorial);
                                    $emComercial->flush();
                                }
                            }
                        }
                        $estadoDetalle = 'EnProceso';
                        if($boolCambioPrecioMasivo || ($boolCambioPlanMasivo && !$boolesEnlace) || $nuevoProducto != null)
                        {
                            $productoNuevoEntity = null;
                            // Se debe cambiar el estado de los servicios si es cambio de precio y crear el historial
                            $servicioEntity = $entityServicio ? $entityServicio : $entityDetalleSolicitudDet->getServicioId();
                            if($servicioEntity)
                            {
                                $servicioPrecioAnterior = $servicioEntity->getPrecioVenta();
                                $servicioEntity->setPrecioVenta($nuevoPrecioMasivo);
                                $productoAnteriorEntity = $servicioEntity->getProductoId();
                                if($nuevoProducto)
                                {
                                    $productoNuevoEntity = $admiProducto->find($nuevoProducto);
                                    if($productoNuevoEntity)
                                    {
                                        $servicioEntity->setProductoId($productoNuevoEntity);
                                        $servicioEntity->setDescripcionPresentaFactura(ucfirst(
                                                                                       strtolower($productoNuevoEntity->getDescripcionProducto())));
                                    }
                                }
                                
                                
                                $emComercial->persist($servicioEntity);
                                $emComercial->flush();
                                
                                if($boolCambioPlanMasivo && $productoNuevoEntity == null)
                                {
                                    $entityInfoServicioProdCaractCapacidad1 = $this->getCaracteristicaServicio($servicioEntity, 'CAPACIDAD1');
                                        $entityInfoServicioProdCaractCapacidad1->setValor($nuevaCapacidad1);
                                        $emComercial->persist($entityInfoServicioProdCaractCapacidad1);
                                        $emComercial->flush();
                                    
                                    $entityInfoServicioProdCaractCapacidad2 = $this->getCaracteristicaServicio($servicioEntity, 'CAPACIDAD2');
                                        $entityInfoServicioProdCaractCapacidad2->setValor($nuevaCapacidad2);
                                        $emComercial->persist($entityInfoServicioProdCaractCapacidad2);
                                        $emComercial->flush();
                                    }
                                else if($productoNuevoEntity)
                                {
                                    // Se obtiene el listado de Productos Caracteristicas asociados al servicio y se las pone en estado Eliminado,
                                    // adicional se busca si existen los mismos productos caracteristicas para el nuevo producto y asocian al servicio
                                    $infoServicioProdCaractList = $infoServicioProdCaract->findBy(array(
                                                                                                        "servicioId" => $servicioEntity,
                                                                                                        "estado"     => "Activo"
                                                                                                        ));
                                    if($infoServicioProdCaractList)
                                    {
                                        foreach($infoServicioProdCaractList as $infoServicioProdCaractEntity)
                                        {
                                            $infoServicioProdCaractEntity->setEstado("Eliminado");
                                            $emComercial->persist($infoServicioProdCaractEntity);
                                            $emComercial->flush();
                                            
                                            $intProductoCaracteristicaId = $infoServicioProdCaractEntity->getProductoCaracterisiticaId();
                                            if($intProductoCaracteristicaId)
                                            {
                                                $admiProductoCaracteristicaEntity = $admiProductoCaracteristica->find($intProductoCaracteristicaId);
                                                if($admiProductoCaracteristicaEntity)
                                                {
                                                    $admiCaracteristicaEntity = $admiProductoCaracteristicaEntity->getCaracteristicaId();
                                                    if($admiCaracteristicaEntity)
                                                    {
                                                        $admiProductoCaracteristicaNueva = $admiProductoCaracteristica->findOneBy(array(
                                                                                                "productoId"        => $productoNuevoEntity,
                                                                                                "caracteristicaId"  => $admiCaracteristicaEntity,
                                                                                                "estado"            => "Activo"
                                                                                            ));
                                                        if($admiProductoCaracteristicaNueva)
                                                        {
                                                            $infoServicioProdCaractNueva = new InfoServicioProdCaract();
                                                            $infoServicioProdCaractNueva->setServicioId($servicioEntity);
                                                            $infoServicioProdCaractNueva->setProductoCaracterisiticaId($admiProductoCaracteristicaNueva);
                                                            $infoServicioProdCaractNueva->setValor($infoServicioProdCaractEntity->getValor());
                                                            $infoServicioProdCaractNueva->setEstado("Activo");
                                                            $infoServicioProdCaractNueva->setUsrCreacion($objSession->get('user'));
                                                            $infoServicioProdCaractNueva->setFeCreacion(new \DateTime('now'));
                                                            $emComercial->persist($infoServicioProdCaractNueva);
                                                            $emComercial->flush();
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }

                                //Historial del servicio
                                $strObservacion = '';
                                if($boolCambioPrecioMasivo)
                                {
                                    $strObservacion .= 'Se hizo Cambio de Precio:';
                                }
                                else if($boolCambioPlanMasivo)
                                {
                                    $strObservacion .= 'Se hizo Cambio de Plan:';
                                }
                                
                                if($boolCambioPlanMasivo && $productoNuevoEntity == null)
                                {
                                    $strObservacion .= "<br> Velocidad Up anterior  :" . $anteriorCapacidad1;
                                    $strObservacion .= "<br> Velocidad Down anterior:" . $anteriorCapacidad2;
                                    $strObservacion .= "<br> Velocidad Up Nuevo  :" . $nuevaCapacidad1;
                                    $strObservacion .= "<br> Velocidad Down Nuevo:" . $nuevaCapacidad2;
                                } 
                                else if($productoNuevoEntity && $productoAnteriorEntity)
                                {  
                                    $strObservacion .= "<br> Producto anterior  :" . $productoAnteriorEntity->getDescripcionProducto();
                                    $strObservacion .= "<br> Producto Nuevo:" . $productoNuevoEntity->getDescripcionProducto();
                                }
                                
                                $strObservacion .= "<br> Precio anterior: " . $servicioPrecioAnterior;
                                $strObservacion .= "<br> Precio Nuevo   : " . $nuevoPrecioMasivo;
                                
                                $servicioHistorial = new InfoServicioHistorial();
                                $servicioHistorial->setServicioId($servicioEntity);
                                $servicioHistorial->setObservacion($strObservacion);
                                $servicioHistorial->setEstado("Activo");
                                $servicioHistorial->setUsrCreacion($objSession->get('user'));
                                $servicioHistorial->setFeCreacion(new \DateTime('now'));
                                $servicioHistorial->setIpCreacion($objRequest->getClientIp());
                                $emComercial->persist($servicioHistorial);
                                $emComercial->flush();
                                
                                
                                /**
                                 * Se crea historial respectivo para poder facturar el proporcional por el cambio de plan, o el cambio de precio del
                                 * servicio
                                 */
                                if( (floatval($nuevoPrecioMasivo) > floatval($servicioPrecioAnterior)) )
                                {
                                    $objServicioHistorial = new InfoServicioHistorial();
                                    $objServicioHistorial->setServicioId($servicioEntity);
                                    $objServicioHistorial->setObservacion("Precio anterior: ".$servicioPrecioAnterior);
                                    $objServicioHistorial->setAccion("confirmoCambioPrecio");
                                    $objServicioHistorial->setEstado("Activo");
                                    $objServicioHistorial->setUsrCreacion($objSession->get('user'));
                                    $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                                    $objServicioHistorial->setIpCreacion($objRequest->getClientIp());
                                    $emComercial->persist($objServicioHistorial);
                                }

                                $estadoDetalle = 'Finalizada';
                            }                 
                        }
                        
                        $entityDetalleSolicitudDet->setEstado($estadoDetalle);
                        $emComercial->persist($entityDetalleSolicitudDet);
                        $emComercial->flush();

                        /* Historial de la Solicitud Detalle */
                        $entityInfoDetalleSolHistDet = new InfoDetalleSolHist();
                        $entityInfoDetalleSolHistDet->setDetalleSolicitudId($entityDetalleSolicitudDet);
                        $entityInfoDetalleSolHistDet->setEstado($entityDetalleSolicitudDet->getEstado());
                        $entityInfoDetalleSolHistDet->setFeCreacion(new \DateTime('now'));
                        $entityInfoDetalleSolHistDet->setUsrCreacion($objSession->get('user'));
                        $emComercial->persist($entityInfoDetalleSolHistDet);
                        $emComercial->flush();

                        $arrayCaracteristicasSolicitud = $infoDetalleSolCaract->findBy(array("detalleSolicitudId" => $entityDetalleSolicitudDet));
                        if(count($arrayCaracteristicasSolicitud) > 0)
                        {
                            foreach($arrayCaracteristicasSolicitud as $caracteristicasSolicitud):
                                $caracteristicasSolicitud->setEstado($estadoDetalle);
                                $caracteristicasSolicitud->setFeUltMod(new \DateTime('now'));
                                $caracteristicasSolicitud->setUsrUltMod($objSession->get('user'));
                                $emComercial->persist($caracteristicasSolicitud);
                                $emComercial->flush();
                            endforeach;
                        }
                        
                        $objTipoSolicitud = $entityDetalleSolicitudDet->getTipoSolicitudId();
                        
                        $arrayRespuesta = array();
                            
                        if($objTipoSolicitud->getDescripcionSolicitud() == 'CAMBIO PLAN')
                        {
                            //Genera proceso masivo para Servicios Backup
                            $arrayParametrosProcesoMasivoBackup                          = array();
                            $arrayParametrosProcesoMasivoBackup['objSolicitudPrincipal'] = $entityDetalleSolicitudDet;
                            $arrayParametrosProcesoMasivoBackup['strUsrCreacion']        = $objSession->get('user');
                            $arrayRespuesta = $serviceTecnico->crearProcesoMasivoCambioPlanBackups($arrayParametrosProcesoMasivoBackup);
                        }
                        
                        //Adjuntar el Listado para Procesos Masivos
                        $arraySolicitudesDetalle[] = $entityDetalleSolicitudDet;
                        
                        if(!empty($arrayRespuesta))
                        {
                            foreach($arrayRespuesta as $objSolicitud)
                            {
                                $arraySolicitudesDetalle[] = $objSolicitud;
                            }
                        }
                        
                        if($boolCambioPrecioMasivo || ($boolCambioPlanMasivo && !$boolesEnlace) || $nuevoProducto != null)
                        {
                            $intIdDetSolDesc = null;
                            $admiCaracteristicaEntity = $admiCaracteristica
                                                        ->findOneBy(array("descripcionCaracteristica" => 'Referencia Solicitud Masiva'));
                            //Finalizar Solicitud de Descuento Detalle si la tiene
                            if($admiCaracteristicaEntity)
                            {
                                $objIdDetSolDesc = $infoDetalleSolCaract->findOneBy(array(
                                                                                      "caracteristicaId"    => $admiCaracteristicaEntity,
                                                                                      "valor"               => $entityDetalleSolicitudDet                                                                                  
                                                                                      ));
                                if($objIdDetSolDesc)
                                {
                                    $intIdDetSolDesc = $objIdDetSolDesc->getId();
                                }
                            }

                            // Finalizar todas las solicitudes de descuento asociadas al Servicio menos la ultima solicitud Aprobada
                            // la que esta asociada al cambio de plan ejecutado
                            $objTipoSolicitudDescuento = $admiTipoSolicitud->findOneBy(array("descripcionSolicitud" => "SOLICITUD DESCUENTO MASIVA"));
                            if($objTipoSolicitudDescuento)
                            {
                                $arraySolicitudesDescuentoAntiguas = $infoDetalleSolicitud->findBy(array(
                                                                                                   'tipoSolicitudId' => $objTipoSolicitudDescuento,
                                                                                                   'servicioId'      => $entityDetalleSolicitudDet
                                                                                                                        ->getServicioId()
                                                                                                   ));
                                if($arraySolicitudesDescuentoAntiguas)
                                {
                                    foreach($arraySolicitudesDescuentoAntiguas as $solDescuentoDetalleAntigua)
                                    {
                                        if($solDescuentoDetalleAntigua->getId() != $intIdDetSolDesc)
                                        {
                                            //Finalizar la solicitud de cambio de plan                        
                                            $solDescuentoDetalleAntigua->setEstado("Finalizada");
                                            $emComercial->persist($solDescuentoDetalleAntigua);
                                            $emComercial->flush();

                                            //Se crea Historial de la finalizacion del detalle de la solicitud de descuento
                                            $objDetalleSolsHist = new InfoDetalleSolHist();
                                            $objDetalleSolsHist->setDetalleSolicitudId($solDescuentoDetalleAntigua);
                                            $objDetalleSolsHist->setEstado($solDescuentoDetalleAntigua->getEstado());
                                            $objDetalleSolsHist->setFeCreacion(new \DateTime('now'));
                                            $objDetalleSolsHist->setUsrCreacion($objSession->get('user'));
                                            $objDetalleSolsHist->setIpCreacion($objRequest->getClientIp());
                                            $objDetalleSolsHist->setObservacion("Se realizo Cambio de Precio exitosamente");
                                            $emComercial->persist($objDetalleSolsHist);
                                            $emComercial->flush();

                                            //Obtener Solicitud Padre de un detalle
                                            $intIdSolDescPadre = $this->getValorCaracteristicaDetalleSolicitud(
                                                                                         $solDescuentoDetalleAntigua->getId(),'Referencia Solicitud');
                                            if($intIdSolDescPadre)
                                            {
                                                $objDetalleSolicitudDescuentoCab = $infoDetalleSolicitud->find($intIdSolDescPadre);
                                                if($objDetalleSolicitudDescuentoCab)
                                                {
                                                    if($objDetalleSolicitudDescuentoCab->getEstado() != 'Finalizada')
                                                    {
                                                        $arrayParametrosEstados                         = array();
                                                        $arrayParametrosEstados['strEstado']            = 'Pendiente';
                                                        $arrayParametrosEstados['idDetalleSolicitud']   = $intIdSolDescPadre;
                                                        $arrayResultadoEstados                          = $infoDetalleSolicitud
                                                            ->findTotalSolicitudesPorEstado($arrayParametrosEstados);
                                                        $intTotalDetallesPendientesDec                  = $arrayResultadoEstados['total'];

                                                        $arrayParametrosEstados                         = array();
                                                        $arrayParametrosEstados['strEstado']            = 'Aprobada';
                                                        $arrayParametrosEstados['idDetalleSolicitud']   = $intIdSolDescPadre;
                                                        $arrayResultadoEstados                          = $infoDetalleSolicitud
                                                            ->findTotalSolicitudesPorEstado($arrayParametrosEstados);
                                                        $intTotalDetallesAprobadosDesc                  = $arrayResultadoEstados['total'];

                                                        $arrayParametrosEstados = array();
                                                        $arrayParametrosEstados['strEstado']            = 'Finalizada';
                                                        $arrayParametrosEstados['idDetalleSolicitud']   = $entityDetalleSolicitudCab->getId();
                                                        $arrayResultadoEstados                          = $infoDetalleSolicitud
                                                            ->findTotalSolicitudesPorEstado($arrayParametrosEstados);
                                                        $intTotalDetallesFinalizadoDesc                 = $arrayResultadoEstados['total'];

                                                        if($intTotalDetallesFinalizadoDesc > 0 && $intTotalDetallesPendientesDec == 0 && 
                                                           $intTotalDetallesAprobadosDesc == 0)
                                                        {
                                                            $objDetalleSolicitudDescuentoCab->setEstado('Finalizada');
                                                            $emComercial->persist($objDetalleSolicitudDescuentoCab);
                                                            $emComercial->flush();

                                                            // Se crea en el Historia de la Solicitud Masiva Cabecera un Registro                        
                                                            $entityInfoDetalleSolHistCab = new InfoDetalleSolHist();
                                                            $entityInfoDetalleSolHistCab->setDetalleSolicitudId($objDetalleSolicitudDescuentoCab);
                                                            $entityInfoDetalleSolHistCab->setObservacion("Se Finalizó la Cabecera de la Solicitud "
                                                                                                         . "Masiva, todos los detalles han sido "
                                                                                                         . "procesados.");
                                                            $entityInfoDetalleSolHistCab->setEstado('Finalizada');
                                                            $entityInfoDetalleSolHistCab->setFeCreacion(new \DateTime('now'));
                                                            $entityInfoDetalleSolHistCab->setUsrCreacion($objSession->get('user'));
                                                            $emComercial->persist($entityInfoDetalleSolHistCab);
                                                            $emComercial->flush();
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                
                $cantidadServicios = count($arraySolicitudesDetalle);
                
                if($cantidadServicios > 0)
                {
                     $entityDetalleSolicitudCab = $infoDetalleSolicitud->find($arrayParametros['intIdDetalleSolicitudCab']);
                     if($entityDetalleSolicitudCab)
                     {
                        // Se crea en el Historia de la Solicitud Masiva Cabecera un Registro                        
                        $entityInfoDetalleSolHistCab = new InfoDetalleSolHist();
                        $entityInfoDetalleSolHistCab->setDetalleSolicitudId($entityDetalleSolicitudCab);
                        $entityInfoDetalleSolHistCab->setObservacion("Se ejecutaron ".$cantidadServicios." detalles, asociada a esta Solicitud Masiva.");
                        $entityInfoDetalleSolHistCab->setEstado($entityDetalleSolicitudCab->getEstado());
                        $entityInfoDetalleSolHistCab->setFeCreacion(new \DateTime('now'));
                        $entityInfoDetalleSolHistCab->setUsrCreacion($objSession->get('user'));
                        $emComercial->persist($entityInfoDetalleSolHistCab);
                        $emComercial->flush();
                        
                        // Si es Cambio de precio o plan(esEnlace = NO) se Verifica que ya no existan Detalles En estado: 
                        // Pendiente, Aprobados o en Procesos y se Finaliza la Cabecera
                        if($boolCambioPrecioMasivo || ($boolCambioPlanMasivo && !$boolesEnlace) || $nuevoProducto != null)
                        {
                            $arrayParametrosEstados = array();
                            $arrayParametrosEstados['strEstado'] = 'Pendiente';
                            $arrayParametrosEstados['idDetalleSolicitud'] = $entityDetalleSolicitudCab->getId();
                            $arrayResultadoEstados = $infoDetalleSolicitud->findTotalSolicitudesPorEstado($arrayParametrosEstados);
                            $intTotalDetallesPendientes = $arrayResultadoEstados['total'];

                            $arrayParametrosEstados = array();
                            $arrayParametrosEstados['strEstado'] = 'Aprobada';
                            $arrayParametrosEstados['idDetalleSolicitud'] = $entityDetalleSolicitudCab->getId();
                            $arrayResultadoEstados = $infoDetalleSolicitud->findTotalSolicitudesPorEstado($arrayParametrosEstados);
                            $intTotalDetallesAprobados = $arrayResultadoEstados['total'];


                            $arrayParametrosEstados = array();
                            $arrayParametrosEstados['strEstado'] = 'EnProceso';
                            $arrayParametrosEstados['idDetalleSolicitud'] = $entityDetalleSolicitudCab->getId();
                            $arrayResultadoEstados = $infoDetalleSolicitud->findTotalSolicitudesPorEstado($arrayParametrosEstados);
                            $intTotalDetallesEnProceso = $arrayResultadoEstados['total'];

                            $arrayParametrosEstados = array();
                            $arrayParametrosEstados['strEstado'] = 'Finalizada';
                            $arrayParametrosEstados['idDetalleSolicitud'] = $entityDetalleSolicitudCab->getId();
                            $arrayResultadoEstados = $infoDetalleSolicitud->findTotalSolicitudesPorEstado($arrayParametrosEstados);
                            $intTotalDetallesFinalizado = $arrayResultadoEstados['total'];
                            
                            if($intTotalDetallesFinalizado > 0 && $intTotalDetallesEnProceso == 0 && $intTotalDetallesAprobados == 0 && $intTotalDetallesPendientes == 0)
                            {
                                $entityDetalleSolicitudCab->setEstado('Finalizada');
                                $emComercial->persist($entityInfoDetalleSolHistCab);
                                $emComercial->flush();

                                // Se crea en el Historia de la Solicitud Masiva Cabecera un Registro                        
                                $entityInfoDetalleSolHistCab = new InfoDetalleSolHist();
                                $entityInfoDetalleSolHistCab->setDetalleSolicitudId($entityDetalleSolicitudCab);
                                $entityInfoDetalleSolHistCab->setObservacion("Se Finalizó la Cabecera de la Solicitud Masiva, todos los detalles han sido procesados.");
                                $entityInfoDetalleSolHistCab->setEstado('Finalizada');
                                $entityInfoDetalleSolHistCab->setFeCreacion(new \DateTime('now'));
                                $entityInfoDetalleSolHistCab->setUsrCreacion($objSession->get('user'));
                                $emComercial->persist($entityInfoDetalleSolHistCab);
                                $emComercial->flush();
                            }
                        }
                     }
                    
                    $entityInfoProcesoMasivoCab = $infoProcesoMasivoCab->findOneBy(array("solicitudId"=>$arrayParametros['intIdDetalleSolicitudCab'],"estado"=>"Pendiente"));

                    $estadoProcesoDetalle = 'Pendiente';
                    if($boolCambioPrecioMasivo || ($boolCambioPlanMasivo && !$boolesEnlace) || $nuevoProducto != null)
                    {
                        $estadoProcesoDetalle = 'Activo';
                    }
                    
                    if($entityInfoProcesoMasivoCab)
                    {
                        $cantidadServicios = $entityInfoProcesoMasivoCab->getCantidadServicios() + $cantidadServicios;
                        $entityInfoProcesoMasivoCab->setCantidadServicios($cantidadServicios);
                        $entityInfoProcesoMasivoCab->setFeUltMod(new \DateTime('now'));
                        $entityInfoProcesoMasivoCab->setUsrUltMod($objSession->get('user'));
                    }
                    else
                    {
                        //Crear Cabecera Procesos Masivos
                        $entityInfoProcesoMasivoCab = new InfoProcesoMasivoCab();
                        $entityInfoProcesoMasivoCab->setTipoProceso($tipoProceso);
                        $entityInfoProcesoMasivoCab->setEmpresaCod($codEmpresa);
                        $entityInfoProcesoMasivoCab->setCantidadServicios($cantidadServicios);
                        $entityInfoProcesoMasivoCab->setEstado($estadoProcesoDetalle);
                        $entityInfoProcesoMasivoCab->setFeCreacion(new \DateTime('now'));
                        $entityInfoProcesoMasivoCab->setUsrCreacion($objSession->get('user'));
                        $entityInfoProcesoMasivoCab->setIpCreacion($objRequest->getClientIp());
                        $entityInfoProcesoMasivoCab->setSolicitudId($arrayParametros['intIdDetalleSolicitudCab']);
                    }
                    $emInfraestructura->persist($entityInfoProcesoMasivoCab);
                    $emInfraestructura->flush();

                    foreach($arraySolicitudesDetalle as $solicitudDetalle):
                        //Cambiar estado a ERROR de todos los procesosmasivosdet del servicio asociados a la misma cabecera
                        $arrayProcesoDetPorCab = $emInfraestructura->getRepository('schemaBundle:InfoProcesoMasivoDet')
                                                                   ->findBy(array('procesoMasivoCabId' => $entityInfoProcesoMasivoCab->getId(),
                                                                                  'servicioId'         => $solicitudDetalle->getServicioId()
                                                                                                                           ->getId()));
                        foreach($arrayProcesoDetPorCab as $objProcesoMasivoDet)
                        {
                            $objProcesoMasivoDet->setEstado("ERROR");
                            $objProcesoMasivoDet->setFeUltMod(new \DateTime('now'));
                            $objProcesoMasivoDet->setUsrUltMod($objSession->get('user'));
                            $emInfraestructura->persist($objProcesoMasivoDet);
                            $emInfraestructura->flush();
                        }
                        
                        //Crear Detalles Procesos Masivos
                        $entityInfoProcesoMasivoDet = new InfoProcesoMasivoDet();
                        $entityInfoProcesoMasivoDet->setProcesoMasivoCabId($entityInfoProcesoMasivoCab);
                        $entityInfoProcesoMasivoDet->setServicioId($solicitudDetalle->getServicioId()->getId());
                        $entityInfoProcesoMasivoDet->setPuntoId($solicitudDetalle->getServicioId()->getPuntoId()->getId());
                        $entityInfoProcesoMasivoDet->setObservacion($solicitudDetalle->getObservacion());
                        $entityInfoProcesoMasivoDet->setEstado($estadoProcesoDetalle);
                        $entityInfoProcesoMasivoDet->setFeCreacion(new \DateTime('now'));
                        $entityInfoProcesoMasivoDet->setUsrCreacion($objSession->get('user'));
                        $entityInfoProcesoMasivoDet->setIpCreacion($objRequest->getClientIp());
                        $entityInfoProcesoMasivoDet->setSolicitudId($solicitudDetalle->getId());
                        $emInfraestructura->persist($entityInfoProcesoMasivoDet);
                        $emInfraestructura->flush();
                        
                        // Se crea en el historial de la solicitud Detalle un registro para q sepan que la solicitud de descuento detalle asociada cambio de estado
                        $entityInfoDetalleSolHistDet = new InfoDetalleSolHist();
                        $entityInfoDetalleSolHistDet->setDetalleSolicitudId($solicitudDetalle);
                        $entityInfoDetalleSolHistDet->setObservacion("Se creó un Proceso Detalle con código: ".$entityInfoProcesoMasivoDet->getId().", asociada a esta Solicitud.");
                        $entityInfoDetalleSolHistDet->setEstado($solicitudDetalle->getEstado());
                        $entityInfoDetalleSolHistDet->setFeCreacion(new \DateTime('now'));
                        $entityInfoDetalleSolHistDet->setUsrCreacion($objSession->get('user'));
                        $emComercial->persist($entityInfoDetalleSolHistDet);
                        $emComercial->flush();
                        
                        // Se crea en el historial adicional de la solicitud Detalle un registro para q sepan ha finalizado
                        if($solicitudDetalle->getEstado() == 'Finalizado')
                        {
                            $entityInfoDetalleSolHistDet = new InfoDetalleSolHist();
                            $entityInfoDetalleSolHistDet->setDetalleSolicitudId($solicitudDetalle);
                            $entityInfoDetalleSolHistDet->setObservacion("Se Finalizó este Solicitud Detalle.");
                            $entityInfoDetalleSolHistDet->setEstado($solicitudDetalle->getEstado());
                            $entityInfoDetalleSolHistDet->setFeCreacion(new \DateTime('now'));
                            $entityInfoDetalleSolHistDet->setUsrCreacion($objSession->get('user'));
                            $emComercial->persist($entityInfoDetalleSolHistDet);
                            $emComercial->flush();
                        }
                        
                    endforeach;
                }
            }
            else
            {
                $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . ' No esta enviando el/los código(s) de la(s) solicitud(es) a ejecutar.');
            }

            $emComercial->getConnection()->commit();
            $emInfraestructura->getConnection()->commit();
            
            try
            {
                $strCliente         = "";
                $strCorreoCliente   = "";
                $arrayTo            = array();
                
                $arrayUsuarioCorreo = $this->getPersonaNombreCorreo($entityDetalleSolicitudCab->getUsrCreacion());
                if(!empty($arrayUsuarioCorreo['correo']))
                {
                    $arrayTo[] = $arrayUsuarioCorreo['correo'];
                }
                
                if($servicioCliente)
                {
                    $puntoIdEntity = $servicioCliente->getPuntoId();
                    if($puntoIdEntity)
                    {
                        $arrayCorreosContactoComercialPunto = $emComercial->getRepository("schemaBundle:InfoPuntoContacto")
                                                                          ->getArrayContactosPorPuntoYTipo( $puntoIdEntity->getId(),
                                                                                                            "Contacto Comercial");
                        
                        if($arrayCorreosContactoComercialPunto)
                        {
                            foreach($arrayCorreosContactoComercialPunto as $arrayCorreoContactoComercialPunto)
                            {
                                if($arrayCorreoContactoComercialPunto && !empty($arrayCorreoContactoComercialPunto['valor']))
                                {
                                    $arrayTo[] = $arrayCorreoContactoComercialPunto['valor'];
                                }
                            } 
                        } 
                        
                        $personaIdEntity = $puntoIdEntity->getPersonaEmpresaRolId()->getPersonaId();
                        if($personaIdEntity)                            
                        {
                            $strCliente         = '';
                            $arrayClienteCorreo = $this->getPersonaNombreCorreoByInfoPersona($personaIdEntity);
                            
                            if(!empty($arrayClienteCorreo['correo']))
                            {
                                $strCorreoCliente = $arrayClienteCorreo['correo'];
                            }
                            
                            if(!empty($arrayClienteCorreo['nombre']))
                            {
                                $strCliente = $arrayClienteCorreo['nombre'];
                            }
                        }
                    }
                }
                
                $strUsuarioSolicitante  = '';
                $arraySolicitanteCorreo = $this->getPersonaNombreCorreo($entityDetalleSolicitudCab->getUsrCreacion());
                if(!empty($arraySolicitanteCorreo['correo']))
                {
                    $arrayTo[] = $arraySolicitanteCorreo['correo'];
                }
                if(!empty($arraySolicitanteCorreo['nombre']))
                {
                    $strUsuarioSolicitante = $arraySolicitanteCorreo['nombre'];
                }
                
                $strTipoSolicitud = $entityDetalleSolicitudCab->getTipoSolicitudId()->getDescripcionSolicitud();
                
                //Instancia del Objeto EnvioPlantilla para la notificacion
                $envioPlantilla  = $this->get('soporte.EnvioPlantilla');
                
                $arrayParametros = array(
                                        'tipoSolicitud'      => $strTipoSolicitud,
                                        'codigoSolicitud'    => $entityDetalleSolicitudCab->getId(),
                                        'sucursales'         => $arraySucursales
                                        );
                
                $strCodigoPlantilla = "AUT_COBRA_CPM";
                $strSubject = 'Ejecución Cobranzas Solicitud de ' . $strTipoSolicitud . ' #'. $entityDetalleSolicitudCab->getId();
                if($boolCambioPrecioMasivo || ($boolCambioPlanMasivo && !$boolesEnlace) || $nuevoProducto != null)
                {
                    $strCodigoPlantilla         = "FIN_SOL_CPM";
                    $strSubject                 = 'Solicitud de ' . $strTipoSolicitud . ' #'.$entityDetalleSolicitudCab->getId();
                    $arrayParametros['cliente'] = $strCliente;
                }
                
                $arrayTo = array_unique($arrayTo);
                $envioPlantilla->generarEnvioPlantilla(
                                                      $strSubject,
                                                      $arrayTo,
                                                      $strCodigoPlantilla,
                                                      $arrayParametros,
                                                      $codEmpresa,
                                                      null,
                                                      null);
            }
            catch(\Exception $ex)
            {
                error_log('Envio de Correo SMC-' . $entityInfoDetalleSolicitudCab->getId() . ':' . $ex->getMessage());
            }
            
            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);
        }
        catch(\Exception $ex)
        {
            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . " " . $ex->getMessage());
            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
        }
        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');
        $objResponse->setContent(json_encode((array) $objReturnResponse));
        return $objResponse;
    }
    
    /**
     * getServiciosConSolMasivasAunFinalizadas, retorna los idServicios en solicitudes masivas que no han finalizado
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 11-05-2016
     * 
     * @author Modificado: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.1 06-08-2016 -  Se añadio un CAMBIO PRECIO a la consulta
     * 
     */
    private function getServiciosConSolMasivasAunFinalizadas($emComercial, $arrayParametros)
    {
        $idServicios                            = "";
        $infoDetalleSolicitud                   = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud');        
        $arrayParametros['strTiposSolicitudes'] = "CAMBIO PLAN,CAMBIO PRECIO,CANCELACION";
        $arrayResultado                         = $infoDetalleSolicitud->findServiciosEnSolicitudesNoFinalizadas($arrayParametros);
        $servicios                              = $arrayResultado['registros'];
        $total                                  = $arrayResultado['total'];

        if($total > 0)
        {
            for($i = 0; $i < $total; $i++){
                $intServicioId = $servicios[$i]['servicioId'];
                $idServicios  .= ($i < $total-1) ? $intServicioId.",": $intServicioId;
            }
        }
        
        return $idServicios;
    }
    
    /**
     * getDetalleEstacionesAction, Obtine el Detalle en cada una de las Estaciones de una Solicitud Masiva
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 19-05-2016
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 01-09-2016 - Se realizan ajustes para el mensaje en el tooltip de la ejecución
     *
     */
    public function getDetalleEstacionesAction()
    {        
        $objRequest = $this->getRequest();
        $objSession = $objRequest->getSession();
        $objReturnResponse = new ReturnResponse();

        $emComercial = $this->getDoctrine()->getManager("telconet");
        $InfoDetalleSolicitud = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud');
        $infoDetalleSolCaract = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract');
        $admiCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica');

        $arrayParametros['intIdSolicitud'] = $objRequest->get('intIdSolicitud');
        $arrayParametros['strCodigosEstaciones'] = $objRequest->get('strCodigosEstaciones');
        
        $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT);
        
        $arrayResultado = array();
        $arrayEstaciones = explode(",", $arrayParametros['strCodigosEstaciones']);
            
        try
        {
            //Valida que el parámetro no se enviado vacío, caso contrario crea una excepción.
            if(!empty($arrayParametros['intIdSolicitud']))
            {
                $objDetalleSolicitud= $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->find($arrayParametros['intIdSolicitud']);
                $strTipoSolicitud   = "";
                if($objDetalleSolicitud)
                {
                    if($objDetalleSolicitud->getTipoSolicitudId())
                    {
                        $strTipoSolicitud   = $objDetalleSolicitud->getTipoSolicitudId()->getDescripcionSolicitud();
                    }
                }
                
                //Busca el parámetro enviado en el request $arrayParametros['intIdParametroCab'] en la entidad AdmiParametroCab.
                $entityDetalleSolicitudCab = $InfoDetalleSolicitud->find($arrayParametros['intIdSolicitud']);
                
                $estadoSolicitudCab = $entityDetalleSolicitudCab->getEstado();

                //Valida que el objeto no sea nulo.
                if($entityDetalleSolicitudCab)
                {
                    
                    $arrayParametrosEstados = array();
                    $arrayParametrosEstados['idDetalleSolicitud'] = $arrayParametros['intIdSolicitud'];
                    $arrayResultadoEstados = $InfoDetalleSolicitud->findTotalSolicitudesPorEstado($arrayParametrosEstados);
                    $intTotalDetalles = $arrayResultadoEstados['total'];
                    
                    $arrayParametrosEstados = array();
                    $arrayParametrosEstados['strEstado'] = 'Pendiente';
                    $arrayParametrosEstados['idDetalleSolicitud'] = $arrayParametros['intIdSolicitud'];
                    $arrayResultadoEstados = $InfoDetalleSolicitud->findTotalSolicitudesPorEstado($arrayParametrosEstados);
                    $intTotalDetallesPendientes = $arrayResultadoEstados['total'];

                    $arrayParametrosEstados = array();
                    $arrayParametrosEstados['strEstado'] = 'Aprobada';
                    $arrayParametrosEstados['idDetalleSolicitud'] = $arrayParametros['intIdSolicitud'];
                    $arrayResultadoEstados = $InfoDetalleSolicitud->findTotalSolicitudesPorEstado($arrayParametrosEstados);
                    $intTotalDetallesAprobados = $arrayResultadoEstados['total'];
                    
                    $arrayParametrosEstados = array();
                    $arrayParametrosEstados['strEstado'] = 'Rechazada';
                    $arrayParametrosEstados['idDetalleSolicitud'] = $arrayParametros['intIdSolicitud'];
                    $arrayResultadoEstados = $InfoDetalleSolicitud->findTotalSolicitudesPorEstado($arrayParametrosEstados);
                    $intTotalDetallesRechazada = $arrayResultadoEstados['total'];

                    $arrayParametrosEstados = array();
                    $arrayParametrosEstados['strEstado'] = 'EnProceso';
                    $arrayParametrosEstados['idDetalleSolicitud'] = $arrayParametros['intIdSolicitud'];
                    $arrayResultadoEstados = $InfoDetalleSolicitud->findTotalSolicitudesPorEstado($arrayParametrosEstados);
                    $intTotalDetallesEnProceso = $arrayResultadoEstados['total'];

                    $arrayParametrosEstados = array();
                    $arrayParametrosEstados['strEstado'] = 'Finalizada';
                    $arrayParametrosEstados['idDetalleSolicitud'] = $arrayParametros['intIdSolicitud'];
                    $arrayResultadoEstados = $InfoDetalleSolicitud->findTotalSolicitudesPorEstado($arrayParametrosEstados);
                    $intTotalDetallesFinalizado = $arrayResultadoEstados['total'];
                    
                    /* Cambio de Precio */
                    
                    $arrayParametrosEstados = array();
                    $arrayParametrosEstados['strEstadoCambioPrecio'] = 'Pendiente';
                    $arrayParametrosEstados['idDetalleSolicitud'] = $arrayParametros['intIdSolicitud'];
                    $arrayResultadoEstados = $InfoDetalleSolicitud->findTotalSolicitudesPorEstado($arrayParametrosEstados);
                    $intTotalDetallesCPPendientes = $arrayResultadoEstados['total'];

                    $arrayParametrosEstados = array();
                    $arrayParametrosEstados['strEstadoCambioPrecio'] = 'Aprobada';
                    $arrayParametrosEstados['idDetalleSolicitud'] = $arrayParametros['intIdSolicitud'];
                    $arrayResultadoEstados = $InfoDetalleSolicitud->findTotalSolicitudesPorEstado($arrayParametrosEstados);
                    $intTotalDetallesCPAprobados = $arrayResultadoEstados['total'];
                    
                    $arrayParametrosEstados = array();
                    $arrayParametrosEstados['strEstadoCambioPrecio'] = 'Rechazada';
                    $arrayParametrosEstados['idDetalleSolicitud'] = $arrayParametros['intIdSolicitud'];
                    $arrayResultadoEstados = $InfoDetalleSolicitud->findTotalSolicitudesPorEstado($arrayParametrosEstados);
                    $intTotalDetallesCPRechazadas = $arrayResultadoEstados['total'];

                    $arrayParametrosEstados = array();
                    $arrayParametrosEstados['strEstadoCambioPrecio'] = 'N/A';
                    $arrayParametrosEstados['idDetalleSolicitud'] = $arrayParametros['intIdSolicitud'];
                    $arrayResultadoEstados = $InfoDetalleSolicitud->findTotalSolicitudesPorEstado($arrayParametrosEstados);
                    $intTotalDetallesCPNA = $arrayResultadoEstados['total'];
                    
                    /* Estado Radio */
                    
                    $arrayParametrosEstados = array();
                    $arrayParametrosEstados['strEstadoRadio'] = 'Pendiente';
                    $arrayParametrosEstados['idDetalleSolicitud'] = $arrayParametros['intIdSolicitud'];
                    $arrayResultadoEstados = $InfoDetalleSolicitud->findTotalSolicitudesPorEstado($arrayParametrosEstados);
                    $intTotalDetallesRadioPendientes = $arrayResultadoEstados['total'];

                    $arrayParametrosEstados = array();
                    $arrayParametrosEstados['strEstadoRadio'] = 'Aprobada';
                    $arrayParametrosEstados['idDetalleSolicitud'] = $arrayParametros['intIdSolicitud'];
                    $arrayResultadoEstados = $InfoDetalleSolicitud->findTotalSolicitudesPorEstado($arrayParametrosEstados);
                    $intTotalDetallesRadioAprobados = $arrayResultadoEstados['total'];
                    
                    $arrayParametrosEstados = array();
                    $arrayParametrosEstados['strEstadoRadio'] = 'Rechazada';
                    $arrayParametrosEstados['idDetalleSolicitud'] = $arrayParametros['intIdSolicitud'];
                    $arrayResultadoEstados = $InfoDetalleSolicitud->findTotalSolicitudesPorEstado($arrayParametrosEstados);
                    $intTotalDetallesRadioRechazadas = $arrayResultadoEstados['total'];

                    $arrayParametrosEstados = array();
                    $arrayParametrosEstados['strEstadoRadio'] = 'N/A';
                    $arrayParametrosEstados['idDetalleSolicitud'] = $arrayParametros['intIdSolicitud'];
                    $arrayResultadoEstados = $InfoDetalleSolicitud->findTotalSolicitudesPorEstado($arrayParametrosEstados);
                    $intTotalDetallesRadioNA = $arrayResultadoEstados['total'];
                    
                    /* Estado Ipccl2 */
                    
                    $arrayParametrosEstados = array();
                    $arrayParametrosEstados['strEstadoIpccl2'] = 'Pendiente';
                    $arrayParametrosEstados['idDetalleSolicitud'] = $arrayParametros['intIdSolicitud'];
                    $arrayResultadoEstados = $InfoDetalleSolicitud->findTotalSolicitudesPorEstado($arrayParametrosEstados);
                    $intTotalDetallesIpccl2Pendientes = $arrayResultadoEstados['total'];

                    $arrayParametrosEstados = array();
                    $arrayParametrosEstados['strEstadoIpccl2'] = 'Aprobada';
                    $arrayParametrosEstados['idDetalleSolicitud'] = $arrayParametros['intIdSolicitud'];
                    $arrayResultadoEstados = $InfoDetalleSolicitud->findTotalSolicitudesPorEstado($arrayParametrosEstados);
                    $intTotalDetallesIpccl2Aprobados = $arrayResultadoEstados['total'];
                    
                    $arrayParametrosEstados = array();
                    $arrayParametrosEstados['strEstadoIpccl2'] = 'Rechazada';
                    $arrayParametrosEstados['idDetalleSolicitud'] = $arrayParametros['intIdSolicitud'];
                    $arrayResultadoEstados = $InfoDetalleSolicitud->findTotalSolicitudesPorEstado($arrayParametrosEstados);
                    $intTotalDetallesIpccl2Rechazadas = $arrayResultadoEstados['total'];

                    $arrayParametrosEstados = array();
                    $arrayParametrosEstados['strEstadoIpccl2'] = 'N/A';
                    $arrayParametrosEstados['idDetalleSolicitud'] = $arrayParametros['intIdSolicitud'];
                    $arrayResultadoEstados = $InfoDetalleSolicitud->findTotalSolicitudesPorEstado($arrayParametrosEstados);
                    $intTotalDetallesIpccl2NA = $arrayResultadoEstados['total'];
                    
                    
                    foreach ($arrayEstaciones as $estacion) {
                        $html ="";
                        $estado = ($estadoSolicitudCab == "Finalizada") ? "activo" : "inactivo";
                            
                        switch ($estacion) {
                            case "solicitudMasiva":
                                if($estadoSolicitudCab != 'Eliminada'){
                                    $html = "<p class='estacionItem ei_success'>La Solicitud fue creada exitosamente!</p>";
                                    $estado = "activo";
                                }else{
                                    $html = "<p class='estacionItem ei_error'>La Solicitud fue Eliminada.</p>";
                                }                                
                                $arrayResultado[$estacion] = array("contenido" => $html, "estado" => $estado);
                                break;
                            case "archivo":
                                
                                $caracteristica = $admiCaracteristica->findOneBy(array("descripcionCaracteristica" => 'Archivo'));
                                $objSolCaract = $infoDetalleSolCaract->findOneBy(array("detalleSolicitudId" => $entityDetalleSolicitudCab, "caracteristicaId" => $caracteristica));
                                if($objSolCaract != null && $objSolCaract->getValor() != null)
                                {
                                    $html .= "<p class='estacionItem ei_success'>El Archivo fue subido exitosamente!</p>";
                                    $estado = "activo";
                                }
                                else
                                {
                                    $html .= "<p class='estacionItem ei_error'>No se ha adjuntado ningún archivo.</p>";
                                }
                                
                                $arrayResultado[$estacion] = array("contenido" => $html, "estado" => $estado);
                                break;
                            case "autorizacion":
                                if($intTotalDetalles == $intTotalDetallesPendientes)
                                {
                                    $html .= "<p class='estacionItem ei_warning'>La Solicitud Masiva esta pendiente de Aprobación/Rechazo</p>";
                                    $html .= "<p class='estacionItem ei_info'>".$intTotalDetallesPendientes." detalles Pendientes</p>";
                                }else{
                                    $html .= "<p class='estacionItem ei_info'>".$intTotalDetallesCPPendientes." detalles Pendientes";                                    
                                    if($intTotalDetallesFinalizado > 0 && $intTotalDetallesEnProceso == 0 && $intTotalDetallesAprobados == 0){
                                        $html .= "<p class='estacionItem ei_success'>".$intTotalDetallesFinalizado." fueron detalles Aprobadas";
                                    }else if( $intTotalDetallesEnProceso > 0 && $intTotalDetallesFinalizado == 0 && $intTotalDetallesAprobados == 0){
                                        $html .= "<p class='estacionItem ei_success'>".$intTotalDetallesEnProceso." fueron detalles Aprobadas";                                        
                                    }else if( $intTotalDetallesEnProceso > 0 && $intTotalDetallesFinalizado > 0 && $intTotalDetallesAprobados == 0){
                                        $html .= "<p class='estacionItem ei_success'>".($intTotalDetallesEnProceso + $intTotalDetallesFinalizado)." fueron detalles Aprobadas";                                        
                                    } else if( $intTotalDetallesEnProceso > 0 && $intTotalDetallesAprobados > 0 &&  $intTotalDetallesFinalizado== 0){
                                        $html .= "<p class='estacionItem ei_success'>".($intTotalDetallesEnProceso + $intTotalDetallesAprobados)." fueron detalles Aprobadas";                                        
                                    } else if( $intTotalDetallesFinalizado > 0 && $intTotalDetallesAprobados > 0 && $intTotalDetallesEnProceso == 0){
                                        $html .= "<p class='estacionItem ei_success'>".($intTotalDetallesFinalizado + $intTotalDetallesAprobados)." fueron detalles Aprobadas";                                        
                                    } else if( $intTotalDetallesAprobados > 0 && $intTotalDetallesFinalizado == 0 &&  $intTotalDetallesEnProceso == 0){
                                        $html .= "<p class='estacionItem ei_success'>".$intTotalDetallesAprobados." fueron detalles Aprobadas";
                                    } else {
                                        $class  = ($intTotalDetallesAprobados > 0) ? "ei_success" : "ei_info" ;
                                        $html .= "<p class='estacionItem ".$class."'>".$intTotalDetallesAprobados." fueron detalles Aprobadas";
                                    }
                                    $class  = ($intTotalDetallesRechazada > 0) ? "ei_rechazo" : "ei_info" ;
                                    $html .= "<p class='estacionItem ".$class."'>".$intTotalDetallesRechazada." detalles Rechazadas";
                                    $html .= "<hr><p><b>TOTAL: ".$intTotalDetalles." detalles";
                                    $estado = "activo";
                                }
                                
                                $arrayResultado[$estacion] = array("contenido" => $html, "estado" => $estado);
                                break;
                            case "autorizacionPrecio":
                                
                                if($intTotalDetallesCPPendientes > 0 && $intTotalDetallesCPAprobados == 0){
                                    $html .= "<p class='estacionItem ei_warning'>La Solicitud Masiva esta pendiente de Aprobación/Rechazo para que se genere la Solicitud de descuento</p><br>";                                    
                                    $html .= "<p class='estacionItem ei_info'>".$intTotalDetallesCPPendientes." detalles Pendientes";
                                    $class  = ($intTotalDetallesCPAprobados > 0) ? "ei_success" : "ei_info" ;
                                    $html .= "<p class='estacionItem ".$class."'>".$intTotalDetallesCPAprobados." detalles Aprobadas";
                                    $class  = ($intTotalDetallesCPRechazadas > 0) ? "ei_rechazo" : "ei_info" ;
                                    $html .= "<p class='estacionItem ".$class."'>".$intTotalDetallesCPRechazadas." detalles Rechazadas";
                                    $html .= "<br><p class='estacionItem ei_info'>".$intTotalDetallesCPNA." detalles No Aplican</p>";
                                } else if($intTotalDetalles == $intTotalDetallesCPNA)
                                {
                                    $html .= "<p class='estacionItem ei_info'>No necesita solicitudes de descuento</p>";
                                    $html .= "<p class='estacionItem ei_info'>".$intTotalDetallesCPNA." detalles No Aplican / ".$intTotalDetalles." detalles</p>";
                                    $estado = "activo";
                                }else{
                                    $html .= "<p class='estacionItem ei_info'>".$intTotalDetallesCPPendientes." detalles Pendientes";
                                    $class  = ($intTotalDetallesCPAprobados > 0) ? "ei_success" : "ei_info" ;
                                    $html .= "<p class='estacionItem ".$class."'>".$intTotalDetallesCPAprobados." detalles Aprobadas";
                                    $class  = ($intTotalDetallesCPRechazadas > 0) ? "ei_rechazo" : "ei_info" ;
                                    $html .= "<p class='estacionItem ".$class."'>".$intTotalDetallesCPRechazadas." detalles Rechazadas";
                                    $estado = "activo";
                                }
                                
                                $arrayResultado[$estacion] = array("contenido" => $html, "estado" => $estado);
                                break;
                            case "autorizacionRadio":
                                
                                if($intTotalDetallesRadioPendientes > 0 && $intTotalDetallesRadioAprobados == 0){
                                    $html .= "<p class='estacionItem ei_warning'>La Solicitud Masiva esta pendiente de Aprobación/Rechazo para que pueda ser Aprobada por Radio</p><br>";                                    
                                    $html .= "<p class='estacionItem ei_info'>".$intTotalDetallesRadioPendientes." detalles Pendientes";
                                    $class  = ($intTotalDetallesRadioAprobados > 0) ? "ei_success" : "ei_info" ;
                                    $html .= "<p class='estacionItem ".$class."'>".$intTotalDetallesRadioAprobados." detalles Aprobadas";
                                    $class  = ($intTotalDetallesRadioRechazadas > 0) ? "ei_rechazo" : "ei_info" ;
                                    $html .= "<p class='estacionItem ".$class."'>".$intTotalDetallesRadioRechazadas." detalles Rechazadas";
                                    $html .= "<br><p class='estacionItem ei_info'>".$intTotalDetallesRadioNA." detalles No Aplican</p>";
                                } else if($intTotalDetalles == $intTotalDetallesRadioNA)
                                {
                                    $html .= "<p class='estacionItem ei_info'>No necesita aprobación de Radio</p>";
                                    $html .= "<p class='estacionItem ei_info'>".$intTotalDetallesRadioNA." detalles No Aplican / ".$intTotalDetalles." detalles</p>";
                                    $estado = "activo";
                                }else{
                                    $html .= "<p class='estacionItem ei_info'>".$intTotalDetallesRadioPendientes." detalles Pendientes";
                                    $class  = ($intTotalDetallesRadioAprobados > 0) ? "ei_success" : "ei_info" ;
                                    $html .= "<p class='estacionItem ".$class."'>".$intTotalDetallesRadioAprobados." detalles Aprobadas";
                                    $class  = ($intTotalDetallesRadioRechazadas > 0) ? "ei_rechazo" : "ei_info" ;
                                    $html .= "<p class='estacionItem ".$class."'>".$intTotalDetallesRadioRechazadas." detalles Rechazadas";
                                    $estado = "activo";
                                }
                                
                                $arrayResultado[$estacion] = array("contenido" => $html, "estado" => $estado);
                                break;
                            case "autorizacionIpccl2":
                                
                                if($intTotalDetallesIpccl2Pendientes > 0 && $intTotalDetallesIpccl2Aprobados == 0){
                                    $html .= "<p class='estacionItem ei_warning'>La Solicitud Masiva esta pendiente de Aprobación/Rechazo para que pueda ser aprobada por IPCCL2</p><br>";                                    
                                    $html .= "<p class='estacionItem ei_info'>".$intTotalDetallesIpccl2Pendientes." detalles Pendientes";
                                    $class  = ($intTotalDetallesIpccl2Aprobados > 0) ? "ei_success" : "ei_info" ;
                                    $html .= "<p class='estacionItem ".$class."'>".$intTotalDetallesIpccl2Aprobados." detalles Aprobadas";
                                    $class  = ($intTotalDetallesIpccl2Rechazadas > 0) ? "ei_rechazo" : "ei_info" ;
                                    $html .= "<p class='estacionItem ".$class."'>".$intTotalDetallesIpccl2Rechazadas." detalles Rechazadas";
                                    $html .= "<br><p class='estacionItem ei_info'>".$intTotalDetallesIpccl2NA." detalles No Aplican</p>";
                                } else if($intTotalDetalles == $intTotalDetallesIpccl2NA)
                                {
                                    $html .= "<p class='estacionItem ei_info'>No necesita solicitudes de descuento</p>";
                                    $html .= "<p class='estacionItem ei_info'>".$intTotalDetallesIpccl2NA." detalles No Aplican / ".$intTotalDetalles." detalles</p>";
                                    $estado = "activo";
                                }else{
                                    $html .= "<p class='estacionItem ei_info'>".$intTotalDetallesIpccl2Pendientes." detalles Pendientes";
                                    $class  = ($intTotalDetallesIpccl2Aprobados > 0) ? "ei_success" : "ei_info" ;
                                    $html .= "<p class='estacionItem ".$class."'>".$intTotalDetallesIpccl2Aprobados." detalles Aprobadas";
                                    $class  = ($intTotalDetallesIpccl2Rechazadas > 0) ? "ei_rechazo" : "ei_info" ;
                                    $html .= "<p class='estacionItem ".$class."'>".$intTotalDetallesIpccl2Rechazadas." detalles Rechazadas";
                                    $estado = "activo";
                                }
                                
                                $arrayResultado[$estacion] = array("contenido" => $html, "estado" => $estado);
                                break;
                            case "ejecucion":
                                                            
                                $boolEnProcesoOFinalizadas      = $intTotalDetallesFinalizado > 0 || $intTotalDetallesEnProceso > 0;
                                $boolAprobadasYCambioPrecioNA   = false;
                                if($strTipoSolicitud=="CANCELACION")
                                {
                                    $boolAprobadasYCambioPrecioNA = $intTotalDetallesAprobados > 0;
                                }
                                else
                                {
                                    $boolAprobadasYCambioPrecioNA = $intTotalDetallesAprobados > 0 &&  $intTotalDetallesCPAprobados > 0;
                                }
                                
                                $boolWarning = ($intTotalDetallesAprobados == 0 || $intTotalDetallesAprobados > 0) 
                                                &&  ($intTotalDetallesCPAprobados == 0 || 
                                                    ($intTotalDetallesCPNA == $intTotalDetallesAprobados && $intTotalDetallesAprobados>0))
                                                && $intTotalDetallesFinalizado == 0;
                                if($boolWarning)
                                {
                                    $html .= "<p class='estacionItem ei_warning'>La solicitud Masiva debe tener al menos un detalle Aprobado</p>";
                                }
                                
                                if($boolAprobadasYCambioPrecioNA)
                                {
                                    $html .= "<p class='estacionItem ei_warning'>La solicitud Masiva tiene solicitudes listas para Ejecutar</p>";
                                }
                                if($boolEnProcesoOFinalizadas){
                                    $html .= "<p class='estacionItem ei_success'>".$intTotalDetallesEnProceso." detalles EnProceso / ".$intTotalDetalles." detalles</p>";
                                    $estado = "activo";
                                }                        
                                
                                $arrayResultado[$estacion] = array("contenido" => $html, "estado" => $estado);
                                break;
                            case "finalizada":
                                $class  = ($estadoSolicitudCab == "Finalizada") ? "ei_success" : "ei_info" ;
                                $html .= "<p class='estacionItem ".$class."'>".$intTotalDetallesFinalizado." detalles Finalizadas / ".$intTotalDetalles." detalles</p>";
                                if($estadoSolicitudCab == "Finalizada"){                            
                                    $estado = "activo";
                                }                                
                                $arrayResultado[$estacion] = array("contenido" => $html, "estado" => $estado);
                                break;
                            default:
                                $html ="<p class='estacionItem ei_error'>Estación no Encontrada</p>";
                                $arrayResultado[$estacion] = array("contenido" => $html, "estado" => $estado);
                                break;
                        }                        
                    }
                }
                else
                {
                    $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                    $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . ' No se encontró la Solicitud a ser Eliminada.');
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
    
    /**
     * radioIndexAction, Redirecciona a la pagina de aprobacion de solicitudes masivas por parte de Radio.
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 03-06-2016
     * @return redireccion al index de la aprobacion de Solicitudes Masivas de radio
     *
     */
    public function radioIndexAction()
    {
        $arrayRolesPermitidos = array();
        
        $em_seguridad = $this->getDoctrine()->getManager("telconet_seguridad");       
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("351", "1");
        
        if(true === $this->get('security.context')->isGranted('ROLE_351-1'))
        {
            $arrayRolesPermitidos[] = 'ROLE_351-1'; //Rol Index
        }
        if(true === $this->get('security.context')->isGranted('ROLE_351-6'))
        {
            $arrayRolesPermitidos[] = 'ROLE_351-6'; //Rol Show
        }
        if(true === $this->get('security.context')->isGranted('ROLE_351-163'))
        {
            $arrayRolesPermitidos[] = 'ROLE_351-163'; //Rol Aprobar
        }        
        if(true === $this->get('security.context')->isGranted('ROLE_351-94'))
        {
            $arrayRolesPermitidos[] = 'ROLE_351-94'; //Rol Rechazar
        }
        return $this->render('comercialBundle:solicitudesmasivas:tecnicoIndex.html.twig', array('item' => $entityItemMenu, 'rolesPermitidos' => $arrayRolesPermitidos, 'tipoAprobacion' => 'radio'));
    }    
    
    /**
     * ipccl2IndexAction, Redirecciona a la pagina de aprobacion de solicitudes masivas por parte de IPCCL2.
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 03-06-2016
     * @return redireccion al index de la aprobacion de Solicitudes Masivas de IPCCL2
     *
     */
    public function ipccl2IndexAction()
    {
        $arrayRolesPermitidos = array();
        
        $em_seguridad = $this->getDoctrine()->getManager("telconet_seguridad");       
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("352", "1");
        
        if(true === $this->get('security.context')->isGranted('ROLE_352-1'))
        {
            $arrayRolesPermitidos[] = 'ROLE_352-1'; //Rol Index
        }
        if(true === $this->get('security.context')->isGranted('ROLE_352-6'))
        {
            $arrayRolesPermitidos[] = 'ROLE_352-6'; //Rol Show
        }
        if(true === $this->get('security.context')->isGranted('ROLE_352-163'))
        {
            $arrayRolesPermitidos[] = 'ROLE_352-163'; //Rol Aprobar
        }        
        if(true === $this->get('security.context')->isGranted('ROLE_352-94'))
        {
            $arrayRolesPermitidos[] = 'ROLE_352-94'; //Rol Rechazar
        }
        return $this->render('comercialBundle:solicitudesmasivas:tecnicoIndex.html.twig', array('item' => $entityItemMenu, 'rolesPermitidos' => $arrayRolesPermitidos, 'tipoAprobacion' => 'ipccl2'));
    }
        
    /**
     * aprobarRechazarRadioAction, Muestra los Datos de una solicitud masiva para ser aprobada o rechazada por Radio
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 15-06-2016
     * @since 1.0
     * 
     */
    public function aprobarRechazarRadioAction($id)
    {
        $peticion = $this->getRequest();
        $intIdEmpresa = ($peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa') : "");
        $emComercial = $this->getDoctrine()->getManager("telconet");

        $infoDetalleSolicitudCabArray = $this->obtenerSolicitudAPresentar($emComercial, $intIdEmpresa, $id);
        
        $em_seguridad = $this->getDoctrine()->getManager("telconet_seguridad");       
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("351", "1");

        $arrayRolesPermitidos = array();
        //Modulo admiParametroCab : [351] Se debe cambiar el Id del modulo antes de pasar a producción        
        if(true === $this->get('security.context')->isGranted('ROLE_351-163'))
        {
            $arrayRolesPermitidos[] = 'ROLE_351-163'; //Rol Aprobar
        }
        if(true === $this->get('security.context')->isGranted('ROLE_351-94'))
        {
            $arrayRolesPermitidos[] = 'ROLE_351-94'; //Rol Rechazar
        }

        return $this->render('comercialBundle:solicitudesmasivas:tecnicoAprobarRechazar.html.twig', array(
                'item' => $entityItemMenu,
                'infoDetalleSolicitudCab' => $infoDetalleSolicitudCabArray,
                'flag' => $peticion->get('flag'),
                'rolesPermitidos' => $arrayRolesPermitidos,
                'tipoAprobacion' => 'radio'
        ));
    }
    
    /**
     * aprobarRechazarAction, Muestra los Datos de una solicitud masiva para ser aprobada o rechazada por IPCCL2
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 15-06-2016
     * @since 1.0
     * 
     */
    public function aprobarRechazarIpccl2Action($id)
    {
        $peticion = $this->getRequest();
        $intIdEmpresa = ($peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa') : "");
        $emComercial = $this->getDoctrine()->getManager("telconet");

        $infoDetalleSolicitudCabArray = $this->obtenerSolicitudAPresentar($emComercial, $intIdEmpresa, $id);
        
        $em_seguridad = $this->getDoctrine()->getManager("telconet_seguridad");       
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("352", "1");

        $arrayRolesPermitidos = array();
        //Modulo admiParametroCab : [352] Se debe cambiar el Id del modulo antes de pasar a producción        
        if(true === $this->get('security.context')->isGranted('ROLE_352-163'))
        {
            $arrayRolesPermitidos[] = 'ROLE_352-163'; //Rol Aprobar
        }
        if(true === $this->get('security.context')->isGranted('ROLE_352-94'))
        {
            $arrayRolesPermitidos[] = 'ROLE_352-94'; //Rol Rechazar
        }

        return $this->render('comercialBundle:solicitudesmasivas:tecnicoAprobarRechazar.html.twig', array(
                'item' => $entityItemMenu,
                'infoDetalleSolicitudCab' => $infoDetalleSolicitudCabArray,
                'flag' => $peticion->get('flag'),
                'rolesPermitidos' => $arrayRolesPermitidos, 
                'tipoAprobacion' => 'ipccl2'
        ));
    }
    
    /**
     * aprobarSolicitudRadioAction, aprueba una o varias solicitudes del detalle para el estado de RADIO
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 15-06-2016
     * 
     * @author Modificado: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.1 11-07-2016 - Notificacion via correo electronico
     * 
     * @author Modificado: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.2 11-08-2016 - Consulta de caracteristicas por detalle
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.3 01-08-2017 - Se quita variable $fecha que no es usada
     *
     * @Secure(roles="ROLE_351-163")
     */
    public function aprobarSolicitudRadioAction()
    {
        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $objReturnResponse      = new ReturnResponse();
        
        $intIdEmpresa           = ($objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : "");

        $emComercial            = $this->getDoctrine()->getManager("telconet");
        $infoDetalleSolicitud   = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud');
        $infoDetalleSolCaract   = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract');
        $admiCaracteristica     = $emComercial->getRepository('schemaBundle:AdmiCaracteristica');

        $arrayParametros['intIdDetalleSolicitudCab']        = $objRequest->get('intIdDetalleSolicitudCab');
        $arrayParametros['strIdSolicitudesSeleccionadas']   = $objRequest->get('strIdSolicitudesSeleccionadas');
        
        $strAreaTecnica  = 'RADIO';
        $strEstado       = 'Estado Radio';
        
        $servicioCliente = null;
        $arraySucursales = array();
        
        $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT);
        $emComercial->getConnection()->beginTransaction();
        try
        {
            //Valida que el parámetro no se enviado vacío, caso contrario crea una excepción.
            if(!empty($arrayParametros['strIdSolicitudesSeleccionadas']))
            {
                $arrayIdSolicitudes     = explode(",", $arrayParametros['strIdSolicitudesSeleccionadas']);
                $totalDetallesAprobadas = 0;
                foreach($arrayIdSolicitudes as $idSolicitud)
                {
                    //Busca la solicitud detalle por id
                    $entityDetalleSolicitudDet = $infoDetalleSolicitud->find($idSolicitud);
                    // Se Consultan las nuevas caracteristicas por detalle
                    $precioNuevo = $this->getValorCaracteristicaDetalleSolicitud($idSolicitud, 'Precio');
                    $bwNuevo     = $this->getValorCaracteristicaDetalleSolicitud($idSolicitud, 'CAPACIDAD1');
                    $bwNuevo    .= '/' . $this->getValorCaracteristicaDetalleSolicitud($idSolicitud, 'CAPACIDAD2');

                    //Valida que el objeto no sea nulo.
                    if($entityDetalleSolicitudDet)
                    {
                        $entityServicio = $entityDetalleSolicitudDet->getServicioId();                        
                        if($entityServicio)
                        {
                            if($servicioCliente == null)
                            {
                                $servicioCliente = $entityServicio;
                            }
                            $entityPunto = $entityServicio->getPuntoId();
                            $punto       = $entityPunto->getLogin();
                            
                            $strUsrVendedor = $entityPunto->getUsrVendedor();
                            $arrayVendedor  = $this->getPersonaNombreCorreo($strUsrVendedor);
                            $vendedor       = $arrayVendedor['nombre'];
                            
                            $servicioAfectado = $entityServicio->getDescripcionPresentaFactura();
                            $precioActual     = $entityServicio->getPrecioVenta();
                            
                            $bwActual  = $this->getValorCaracteristicaServicio($entityServicio, 'CAPACIDAD1');
                            $bwActual .= '/' . $this->getValorCaracteristicaServicio($entityServicio, 'CAPACIDAD2');
                            
                            $arraySucursales[] = array( 
                                                      'punto'           => $punto,
                                                      'servicio'        => $servicioAfectado,
                                                      'precioActual'    => $precioActual,
                                                      'precioNuevo'     => $precioNuevo,
                                                      'bwActual'        => $bwActual,
                                                      'bwNuevo'         => $bwNuevo,
                                                      'vendedor'        => $vendedor
                                                      );
                        }
                        
                        $caracteristicaEstadoTecnico = $admiCaracteristica->findOneBy(array("descripcionCaracteristica" => $strEstado));
                        $infoDetalleSolCaractEstadoTecnico = $infoDetalleSolCaract
                                                                ->findOneBy(array(
                                                                                 "detalleSolicitudId" => $entityDetalleSolicitudDet->getId(), 
                                                                                 "caracteristicaId"   => $caracteristicaEstadoTecnico->getId()
                                                                                 ));

                        if($infoDetalleSolCaractEstadoTecnico)
                        {
                            $infoDetalleSolCaractEstadoValor = $infoDetalleSolCaractEstadoTecnico->getValor();
                            
                            if($infoDetalleSolCaractEstadoValor == 'Pendiente')
                            {
                                $infoDetalleSolCaractEstadoTecnico->setValor('Aprobada');
                                $emComercial->persist($infoDetalleSolCaractEstadoTecnico);
                                $emComercial->flush();
                                
                                /* Historial de la Solicitud Detalle */
                                $entityInfoDetalleSolHistDet = new InfoDetalleSolHist();
                                $entityInfoDetalleSolHistDet->setDetalleSolicitudId($entityDetalleSolicitudDet);
                                $entityInfoDetalleSolHistDet->setEstado($entityDetalleSolicitudDet->getEstado());
                                $entityInfoDetalleSolHistDet->setObservacion("Detalle aprobado por " . $strAreaTecnica);
                                $entityInfoDetalleSolHistDet->setFeCreacion(new \DateTime('now'));
                                $entityInfoDetalleSolHistDet->setUsrCreacion($objSession->get('user'));
                                $emComercial->persist($entityInfoDetalleSolHistDet);
                                $emComercial->flush();
                                
                                $totalDetallesAprobadas++;
                            }
                        }
                    }
                }
            }
            else
            {
                $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . ' No esta enviando el/los código(s) de la(s) solicitud(es) a aprobar.');
            }
            
            //Busca la solicitud Cabecera por id
            $entityDetalleSolicitudCab = $infoDetalleSolicitud->find($arrayParametros['intIdDetalleSolicitudCab']);
            if($entityDetalleSolicitudCab)
            {
                /* Historial de la Solicitud Detalle */
                $entityInfoDetalleSolHistCab = new InfoDetalleSolHist();
                $entityInfoDetalleSolHistCab->setDetalleSolicitudId($entityDetalleSolicitudCab);
                $entityInfoDetalleSolHistCab->setObservacion($strAreaTecnica . " : Se aprobaron ".$totalDetallesAprobadas." Detalles de esta Solicitud Masiva.");
                $entityInfoDetalleSolHistCab->setEstado($entityDetalleSolicitudCab->getEstado());
                $entityInfoDetalleSolHistCab->setFeCreacion(new \DateTime('now'));
                $entityInfoDetalleSolHistCab->setUsrCreacion($objSession->get('user'));
                $emComercial->persist($entityInfoDetalleSolHistCab);
                $emComercial->flush();
            }
            
            $emComercial->getConnection()->commit();
            
            try
            {
                $strCliente = "";
                $arrayTo    = array();
                
                $arrayUsuarioCorreo = $this->getPersonaNombreCorreo($entityDetalleSolicitudCab->getUsrCreacion());
                if(!empty($arrayUsuarioCorreo['correo'])){
                    $arrayTo[] = $arrayUsuarioCorreo['correo'];
                }
                
                if($servicioCliente)
                {
                    $puntoIdEntity = $servicioCliente->getPuntoId();
                    if($puntoIdEntity)
                    {
                        $personaIdEntity = $puntoIdEntity->getPersonaEmpresaRolId()->getPersonaId();
                        if($personaIdEntity)                            
                        {
                            $strCliente         = '';
                            $arrayClienteCorreo = $this->getPersonaNombreCorreoByInfoPersona($personaIdEntity);
                            
                            if(!empty($arrayClienteCorreo['nombre'])){
                                $strCliente = $arrayClienteCorreo['nombre'];
                            }
                        }
                    }
                }
                
                $strUsuarioSolicitante  = '';
                $arraySolicitanteCorreo = $this->getPersonaNombreCorreo($entityDetalleSolicitudCab->getUsrCreacion());
                if(!empty($arraySolicitanteCorreo['correo'])){
                    $arrayTo[] = $arraySolicitanteCorreo['correo'];
                }
                if(!empty($arraySolicitanteCorreo['nombre'])){
                    $strUsuarioSolicitante = $arraySolicitanteCorreo['nombre'];
                }
                
                $strUsuarioAprobacion   = '';
                $arrayAprobacionCorreo  = $this->getPersonaNombreCorreo($objSession->get('user'));
                if(!empty($arrayAprobacionCorreo['correo'])){
                    $arrayTo[] = $arrayAprobacionCorreo['correo'];
                }
                if(!empty($arrayAprobacionCorreo['nombre'])){
                    $strUsuarioAprobacion = $arrayAprobacionCorreo['nombre'];
                }
                
                $strTipoSolicitud = $entityDetalleSolicitudCab->getTipoSolicitudId()->getDescripcionSolicitud();
                $strUrlShow       = $this->container->getParameter('host_solicitudes') . 
                                    $this->generateUrl('solicitudesmasivas_show', array('id' => $entityDetalleSolicitudCab->getId()));
                
                //Instancia del Objeto EnvioPlantilla para la notificacion
                $envioPlantilla   = $this->get('soporte.EnvioPlantilla');
                
                $arrayParametros  = array(
                                         'tipoSolicitud'        => $strTipoSolicitud,
                                         'codigoSolicitud'      => $entityDetalleSolicitudCab->getId(),
                                         'sucursales'           => $arraySucursales,
                                         'url'                  => $strUrlShow,
                                         'usuarioAprobacion'    => $strUsuarioAprobacion,
                                         'estadoSolicitud'      => 'Aprobada'
                                         );

                $arrayTo = array_unique($arrayTo);
                $envioPlantilla->generarEnvioPlantilla(
                                                      'Autorización ' . $strAreaTecnica . ' Solicitud de ' . $strTipoSolicitud . ' #'. 
                                                      $entityDetalleSolicitudCab->getId(),
                                                      $arrayTo,
                                                      'AUT_TEC_CPM',
                                                      $arrayParametros,
                                                      $intIdEmpresa,
                                                      null,
                                                      null);
                
            }
            catch(\Exception $ex)
            {
                error_log('Envio de Correo SMC-' . $entityDetalleSolicitudCab->getId() . ':' . $ex->getMessage());
            }
            
            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);
        }
        catch(\Exception $ex)
        {
            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . " " . $ex->getMessage());
            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
        }
        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');
        $objResponse->setContent(json_encode((array) $objReturnResponse));
        return $objResponse;
    }
    
    /**
     * rechazarSolicitudRadioAction, rechaza una o varias solicitudes del detalle por RADIO
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 15-06-2016
     * 
     * Se agrega rechazo de solicitudes de servicios backups
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 06-04-2017
     *
     * @Secure(roles="ROLE_351-94")
     */
    public function rechazarSolicitudRadioAction()
    {        
        $objRequest = $this->getRequest();
        $objSession = $objRequest->getSession();
        $objReturnResponse = new ReturnResponse();

        $emComercial          = $this->getDoctrine()->getManager("telconet");
        $serviceTecnico       = $this->get('tecnico.InfoServicioTecnico');
        $infoDetalleSolicitud = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud');
        $infoDetalleSolCaract = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract');
        $admiCaracteristica   = $emComercial->getRepository('schemaBundle:AdmiCaracteristica');

        $arrayParametros['intIdDetalleSolicitudCab'] = $objRequest->get('intIdDetalleSolicitudCab');
        $arrayParametros['strIdSolicitudesSeleccionadas'] = $objRequest->get('strIdSolicitudesSeleccionadas');
        $arrayParametros['strMotivo'] = $objRequest->get('strMotivo');
        
        $strAreaTecnica = 'RADIO';
        $strEstado = 'Estado Radio';

        $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT);
        $emComercial->getConnection()->beginTransaction();
        try
        {
            //Valida que el parámetro no se enviado vacío, caso contrario crea una excepción.
            if(!empty($arrayParametros['strIdSolicitudesSeleccionadas']))
            {
                if(!empty($arrayParametros['strMotivo']))
                {

                    $arrayIdSolicitudes = explode(",", $arrayParametros['strIdSolicitudesSeleccionadas']);
                    $totalDetallesRechazadas = 0;
                    foreach($arrayIdSolicitudes as $idSolicitud)
                    {
                        //Busca la solicitud detalle por id
                        $entityDetalleSolicitudDet = $infoDetalleSolicitud->find($idSolicitud);

                        //Valida que el objeto no sea nulo.
                        if($entityDetalleSolicitudDet)
                        {
                            $caracteristicaEstadoTecnico = $admiCaracteristica->findOneBy(array("descripcionCaracteristica" => $strEstado));
                            $infoDetalleSolCaractEstadoTecnico = $infoDetalleSolCaract->findOneBy(array("detalleSolicitudId" => $entityDetalleSolicitudDet->getId(), "caracteristicaId" => $caracteristicaEstadoTecnico->getId()));

                            if($infoDetalleSolCaractEstadoTecnico)
                            {
                                $infoDetalleSolCaractEstadoValor = $infoDetalleSolCaractEstadoTecnico->getValor();

                                if($infoDetalleSolCaractEstadoValor == 'Pendiente')
                                {
                                    $infoDetalleSolCaractEstadoTecnico->setValor('Rechazada');
                                    $emComercial->persist($infoDetalleSolCaractEstadoTecnico);
                                    $emComercial->flush();

                                    /* Historial de la Solicitud Detalle */
                                    $entityInfoDetalleSolHistDet = new InfoDetalleSolHist();
                                    $entityInfoDetalleSolHistDet->setDetalleSolicitudId($entityDetalleSolicitudDet);
                                    $entityInfoDetalleSolHistDet->setEstado($entityDetalleSolicitudDet->getEstado());
                                    $entityInfoDetalleSolHistDet->setObservacion("Detalle rechazado por " . $strAreaTecnica . ". Motivo: ". $arrayParametros['strMotivo']);
                                    $entityInfoDetalleSolHistDet->setFeCreacion(new \DateTime('now'));
                                    $entityInfoDetalleSolHistDet->setUsrCreacion($objSession->get('user'));
                                    $emComercial->persist($entityInfoDetalleSolHistDet);
                                    $emComercial->flush();
                                    
                                    $entityDetalleSolicitudDet->setEstado('Rechazada');
                                    $emComercial->persist($entityDetalleSolicitudDet);
                                    $emComercial->flush();

                                    $totalDetallesRechazadas++;
                                }
                            
                                $objTipoSolicitud = $entityDetalleSolicitudDet->getTipoSolicitudId();

                                //Variable que guarda el total de solicitudes backups rechazadas
                                $intRechazadasBackups = 0;

                                if($objTipoSolicitud->getDescripcionSolicitud() == 'CAMBIO PLAN')
                                {
                                    //Rechazar solicitudes de servicios backups en caso de existir
                                    $arrayParametrosRechazoBackups                          = array();
                                    $arrayParametrosRechazoBackups['objSolicitudPrincipal'] = $entityDetalleSolicitudDet;
                                    $arrayParametrosRechazoBackups['strMotivo']             = $arrayParametros['strMotivo'];
                                    $arrayParametrosRechazoBackups['strUsrCreacion']        = $objSession->get('user');
                                    $intRechazadasBackups = $serviceTecnico->rechazarSolicitudCambioPlanBackups($arrayParametrosRechazoBackups);
                                }

                                $totalDetallesRechazadas = $totalDetallesRechazadas + $intRechazadasBackups;
                            }
                        }
                    }

                    $intIdDetalleSolicitudCab = $arrayParametros['intIdDetalleSolicitudCab'];
                    //Busca la solicitud Cabecera por id
                    $entityDetalleSolicitudCab = $infoDetalleSolicitud->find($intIdDetalleSolicitudCab);
                    if($entityDetalleSolicitudCab)
                    {
                        /* Historial de la Solicitud Detalle */
                        $entityInfoDetalleSolHistCab = new InfoDetalleSolHist();
                        $entityInfoDetalleSolHistCab->setDetalleSolicitudId($entityDetalleSolicitudCab);
                        $entityInfoDetalleSolHistCab->setObservacion("Se rechazaron ".$totalDetallesRechazadas." Detalles de esta Solicitud Masiva.");
                        $entityInfoDetalleSolHistCab->setEstado($entityDetalleSolicitudCab->getEstado());
                        $entityInfoDetalleSolHistCab->setFeCreacion(new \DateTime('now'));
                        $entityInfoDetalleSolHistCab->setUsrCreacion($objSession->get('user'));
                        $emComercial->persist($entityInfoDetalleSolHistCab);
                        $emComercial->flush();
                        
                        // Se Verifica si Ya no queda ningun detalle en estado Pendiente
                        $arrayParametros = array();
                        $arrayParametros['intIdEmpresa'] = ($objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : "");
                        $arrayParametros['intIdPadre'] = $intIdDetalleSolicitudCab;
                        $arrayParametros['boolMasivas'] = 'false';
                        $resultado = $infoDetalleSolicitud->findSolicitudes($arrayParametros);
                        $solicitudes = $resultado['registros'];
                        $total = $resultado['total'];

                        $totalRechazadasEliminadasFinalizadas = 0;
                        foreach($solicitudes as $solicitud):
                            if($solicitud['estado'] == 'Rechazada' || $solicitud['estado'] == 'Eliminada' || $solicitud['estado'] == 'Finalizada')
                            {
                                $totalRechazadasEliminadasFinalizadas++;
                            }
                        endforeach;
                        
                        // si los totales son iguales quiere decir que no queda ningun detalle pendiente o valido y la solicitud masiva se rechaza tambien                    
                        if($total == $totalRechazadasEliminadasFinalizadas)
                        {
                            if($entityDetalleSolicitudCab->getEstado() == 'Pendiente')
                            {
                                $entityDetalleSolicitudCab->setEstado('Finalizada');
                                $emComercial->persist($entityDetalleSolicitudCab);
                                $emComercial->flush();

                                /* Historial de la Solicitud Detalle */
                                $entityInfoDetalleSolHistCab = new InfoDetalleSolHist();
                                $entityInfoDetalleSolHistCab->setDetalleSolicitudId($entityDetalleSolicitudCab);
                                $entityInfoDetalleSolHistCab->setObservacion("Se Finalizó la Solicitud Masiva, ya no tiene ningún detalle pendiente");
                                $entityInfoDetalleSolHistCab->setEstado($entityDetalleSolicitudCab->getEstado());
                                $entityInfoDetalleSolHistCab->setFeCreacion(new \DateTime('now'));
                                $entityInfoDetalleSolHistCab->setUsrCreacion($objSession->get('user'));
                                $emComercial->persist($entityInfoDetalleSolHistCab);
                                $emComercial->flush();
                            }
                        }
                    }
                } else
                {
                    $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                    $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . ' Debe ingresa un Motivo de Rechazo.');
                }
            }
            else
            {
                $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . ' No esta enviando el/los código(s) de la(s) solicitud(es) a rechazar.');
            }
            $emComercial->getConnection()->commit();
            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);
        }
        catch(\Exception $ex)
        {
            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . " " . $ex->getMessage());
            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
        }
        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');
        $objResponse->setContent(json_encode((array) $objReturnResponse));
        return $objResponse;
    }
    
    
    /**
     * aprobarSolicitudIpccl2Action, aprueba una o varias solicitudes del detalle para el estado de IPCCL2
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 15-06-2016
     * 
     * @author Modificado: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.1 11-07-2016 - Notificacion via correo electronico
     * 
     * @author Modificado: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.2 11-08-2016 - Consulta de caracteristicas por detalle
     *
     * @Secure(roles="ROLE_352-163")
     */
    public function aprobarSolicitudIpccl2Action()
    {
        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $objReturnResponse      = new ReturnResponse();
        
        $intIdEmpresa           = ($objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : "");

        $emComercial            = $this->getDoctrine()->getManager("telconet");
        $infoDetalleSolicitud   = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud');
        $infoDetalleSolCaract   = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract');
        $admiCaracteristica     = $emComercial->getRepository('schemaBundle:AdmiCaracteristica');

        $arrayParametros['intIdDetalleSolicitudCab']        = $objRequest->get('intIdDetalleSolicitudCab');
        $arrayParametros['strIdSolicitudesSeleccionadas']   = $objRequest->get('strIdSolicitudesSeleccionadas');
        
        $strAreaTecnica = 'IPCCL2';
        $strEstado      = 'Estado IPCCL2';
        
        $arraySucursales = array();
        $servicioCliente = null;
        
        $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT);
        $emComercial->getConnection()->beginTransaction();
        try
        {
            //Valida que el parámetro no se enviado vacío, caso contrario crea una excepción.
            if(!empty($arrayParametros['strIdSolicitudesSeleccionadas']))
            {
                $arrayIdSolicitudes = explode(",", $arrayParametros['strIdSolicitudesSeleccionadas']);
                $totalDetallesAprobadas = 0;
                foreach($arrayIdSolicitudes as $idSolicitud)
                {
                    //Busca la solicitud detalle por id
                    $entityDetalleSolicitudDet  = $infoDetalleSolicitud->find($idSolicitud);
                    // Consulta de caracteristicas por detalle
                    $precioNuevo                = $this->getValorCaracteristicaDetalleSolicitud($idSolicitud, 'Precio');
                    $bwNuevo                    = $this->getValorCaracteristicaDetalleSolicitud($idSolicitud, 'CAPACIDAD1');
                    $bwNuevo                   .= '/' . $this->getValorCaracteristicaDetalleSolicitud($idSolicitud, 'CAPACIDAD2');

                    //Valida que el objeto no sea nulo.
                    if($entityDetalleSolicitudDet)
                    {
                        $entityServicio = $entityDetalleSolicitudDet->getServicioId();                        
                        if($entityServicio)
                        {
                            if($servicioCliente == null)
                            {
                                $servicioCliente = $entityServicio;
                            }
                            $entityPunto = $entityServicio->getPuntoId();
                            $punto       = $entityPunto->getLogin();
                            
                            $strUsrVendedor = $entityPunto->getUsrVendedor();
                            $arrayVendedor  = $this->getPersonaNombreCorreo($strUsrVendedor);
                            $vendedor       = $arrayVendedor['nombre'];
                            
                            $servicioAfectado   = $entityServicio->getDescripcionPresentaFactura();
                            $precioActual       = $entityServicio->getPrecioVenta();
                            
                            $bwActual  = $this->getValorCaracteristicaServicio($entityServicio, 'CAPACIDAD1');
                            $bwActual .= '/' . $this->getValorCaracteristicaServicio($entityServicio, 'CAPACIDAD2');
                            
                            $arraySucursales[] = array( 
                                                      'punto'           => $punto,
                                                      'servicio'        => $servicioAfectado,
                                                      'precioActual'    => $precioActual,
                                                      'precioNuevo'     => $precioNuevo,
                                                      'bwActual'        => $bwActual,
                                                      'bwNuevo'         => $bwNuevo,
                                                      'vendedor'        => $vendedor
                                                      );
                        }
                        
                        $caracteristicaEstadoTecnico = $admiCaracteristica->findOneBy(array("descripcionCaracteristica" => $strEstado));
                        $objInfoDetalleSolCaractEstadoTecnico = $infoDetalleSolCaract->findOneBy(array("detalleSolicitudId" => $entityDetalleSolicitudDet->getId(), "caracteristicaId" => $caracteristicaEstadoTecnico->getId()));

                        if($objInfoDetalleSolCaractEstadoTecnico)
                        {
                            $infoDetalleSolCaractEstadoValor = $objInfoDetalleSolCaractEstadoTecnico->getValor();
                            
                            if($infoDetalleSolCaractEstadoValor == 'Pendiente')
                            {
                                $objInfoDetalleSolCaractEstadoTecnico->setValor('Aprobada');
                                $emComercial->persist($objInfoDetalleSolCaractEstadoTecnico);
                                $emComercial->flush();
                                
                                /* Historial de la Solicitud Detalle */
                                $entityInfoDetalleSolHistDet = new InfoDetalleSolHist();
                                $entityInfoDetalleSolHistDet->setDetalleSolicitudId($entityDetalleSolicitudDet);
                                $entityInfoDetalleSolHistDet->setEstado($entityDetalleSolicitudDet->getEstado());
                                $entityInfoDetalleSolHistDet->setObservacion("Detalle aprobado por " . $strAreaTecnica);
                                $entityInfoDetalleSolHistDet->setFeCreacion(new \DateTime('now'));
                                $entityInfoDetalleSolHistDet->setUsrCreacion($objSession->get('user'));
                                $emComercial->persist($entityInfoDetalleSolHistDet);
                                $emComercial->flush();
                                
                                $totalDetallesAprobadas++;
                            }
                        }
                    }
                }
            }
            else
            {
                $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . ' No esta enviando el/los código(s) de la(s) solicitud(es) a aprobar.');
            }
            
            //Busca la solicitud Cabecera por id
            $entityDetalleSolicitudCab = $infoDetalleSolicitud->find($arrayParametros['intIdDetalleSolicitudCab']);
            if($entityDetalleSolicitudCab)
            {
                /* Historial de la Solicitud Detalle */
                $entityInfoDetalleSolHistCab = new InfoDetalleSolHist();
                $entityInfoDetalleSolHistCab->setDetalleSolicitudId($entityDetalleSolicitudCab);
                $entityInfoDetalleSolHistCab->setObservacion($strAreaTecnica . " : Se aprobaron ".$totalDetallesAprobadas." Detalles de esta Solicitud Masiva.");
                $entityInfoDetalleSolHistCab->setEstado($entityDetalleSolicitudCab->getEstado());
                $entityInfoDetalleSolHistCab->setFeCreacion(new \DateTime('now'));
                $entityInfoDetalleSolHistCab->setUsrCreacion($objSession->get('user'));
                $emComercial->persist($entityInfoDetalleSolHistCab);
                $emComercial->flush();
            }
            
            $emComercial->getConnection()->commit();
            
            try
            {
                $strCliente = "";
                $arrayTo = array();
                
                $arrayUsuarioCorreo = $this->getPersonaNombreCorreo($entityDetalleSolicitudCab->getUsrCreacion());
                if(!empty($arrayUsuarioCorreo['correo'])){
                    $arrayTo[] = $arrayUsuarioCorreo['correo'];
                }
                
                if($servicioCliente)
                {
                    $puntoIdEntity = $servicioCliente->getPuntoId();
                    if($puntoIdEntity)
                    {
                        $personaIdEntity = $puntoIdEntity->getPersonaEmpresaRolId()->getPersonaId();
                        if($personaIdEntity)                            
                        {
                            $strCliente         = '';
                            $arrayClienteCorreo = $this->getPersonaNombreCorreoByInfoPersona($personaIdEntity);
                            
                            if(!empty($arrayClienteCorreo['nombre'])){
                                $strCliente = $arrayClienteCorreo['nombre'];
                            }
                        }
                    }
                }
                
                $strUsuarioSolicitante  = '';
                $arraySolicitanteCorreo = $this->getPersonaNombreCorreo($entityDetalleSolicitudCab->getUsrCreacion());
                if(!empty($arraySolicitanteCorreo['correo'])){
                    $arrayTo[] = $arraySolicitanteCorreo['correo'];
                }
                if(!empty($arraySolicitanteCorreo['nombre'])){
                    $strUsuarioSolicitante = $arraySolicitanteCorreo['nombre'];
                }
                
                $strUsuarioAprobacion  = '';
                $arrayAprobacionCorreo = $this->getPersonaNombreCorreo($objSession->get('user'));
                if(!empty($arrayAprobacionCorreo['correo'])){
                    $arrayTo[] = $arrayAprobacionCorreo['correo'];
                }
                if(!empty($arrayAprobacionCorreo['nombre'])){
                    $strUsuarioAprobacion = $arrayAprobacionCorreo['nombre'];
                }
                
                $strTipoSolicitud = $entityDetalleSolicitudCab->getTipoSolicitudId()->getDescripcionSolicitud();
                $strUrlShow       = $this->container->getParameter('host_solicitudes') . 
                                    $this->generateUrl('solicitudesmasivas_show', array('id' => $entityDetalleSolicitudCab->getId()));
                
                //Instancia del Objeto EnvioPlantilla para la notificacion
                $envioPlantilla  = $this->get('soporte.EnvioPlantilla');
                
                $arrayParametros = array(
                                        'tipoSolicitud'     => $strTipoSolicitud,
                                        'codigoSolicitud'   => $entityDetalleSolicitudCab->getId(),
                                        'sucursales'        => $arraySucursales,
                                        'url'               => $strUrlShow,
                                        'usuarioAprobacion' => $strUsuarioAprobacion,
                                        'estadoSolicitud'   => 'Aprobada'
                                        );

                $arrayTo = array_unique($arrayTo);
                $envioPlantilla->generarEnvioPlantilla('Autorización ' . $strAreaTecnica . ' Solicitud de ' . $strTipoSolicitud . ' #'. $entityDetalleSolicitudCab->getId(),
                                                   $arrayTo,
                                                   'AUT_TEC_CPM',
                                                   $arrayParametros,
                                                   $intIdEmpresa,
                                                   null,
                                                   null);
                
            }
            catch(\Exception $ex)
            {
                error_log('Envio de Correo SMC-' . $entityDetalleSolicitudCab->getId() . ':' . $ex->getMessage());
            }
            
            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);
        }
        catch(\Exception $ex)
        {
            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . " " . $ex->getMessage());
            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
        }
        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');
        $objResponse->setContent(json_encode((array) $objReturnResponse));
        return $objResponse;
    }
    
    /**
     * rechazarSolicitudIpccl2Action, rechaza una o varias solicitudes del detalle por IPCCL2
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 15-06-2016
     * 
     * se añadio el repositorio AdmiCaracteristica
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.1 29-07-2016
     * 
     * Se agrega rechazo de solicitudes de servicios backups
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.2 06-04-2017
     *
     * @Secure(roles="ROLE_352-94")
     */
    public function rechazarSolicitudIpccl2Action()
    {        
        $objRequest = $this->getRequest();
        $objSession = $objRequest->getSession();
        $objReturnResponse = new ReturnResponse();

        $emComercial          = $this->getDoctrine()->getManager("telconet");
        $serviceTecnico       = $this->get('tecnico.InfoServicioTecnico');
        $infoDetalleSolicitud = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud');
        $infoDetalleSolCaract = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract');
        $admiCaracteristica   = $emComercial->getRepository('schemaBundle:AdmiCaracteristica');

        $arrayParametros['intIdDetalleSolicitudCab'] = $objRequest->get('intIdDetalleSolicitudCab');
        $arrayParametros['strIdSolicitudesSeleccionadas'] = $objRequest->get('strIdSolicitudesSeleccionadas');
        $arrayParametros['strMotivo'] = $objRequest->get('strMotivo');
        
        $strAreaTecnica = 'IPCCL2';
        $strEstado = 'Estado IPCCL2';

        $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT);
        $emComercial->getConnection()->beginTransaction();
        try
        {
            //Valida que el parámetro no se enviado vacío, caso contrario crea una excepción.
            if(!empty($arrayParametros['strIdSolicitudesSeleccionadas']))
            {
                if(!empty($arrayParametros['strMotivo']))
                {

                    $arrayIdSolicitudes = explode(",", $arrayParametros['strIdSolicitudesSeleccionadas']);
                    $totalDetallesRechazadas = 0;
                    foreach($arrayIdSolicitudes as $idSolicitud)
                    {
                        //Busca la solicitud detalle por id
                        $entityDetalleSolicitudDet = $infoDetalleSolicitud->find($idSolicitud);

                        //Valida que el objeto no sea nulo.
                        if($entityDetalleSolicitudDet)
                        {
                            $caracteristicaEstadoTecnico = $admiCaracteristica->findOneBy(array("descripcionCaracteristica" => $strEstado));
                            $objInfoDetalleSolCaractEstadoTecnico = $infoDetalleSolCaract->findOneBy(array("detalleSolicitudId" => $entityDetalleSolicitudDet->getId(), "caracteristicaId" => $caracteristicaEstadoTecnico->getId()));

                            if($objInfoDetalleSolCaractEstadoTecnico)
                            {
                                $infoDetalleSolCaractEstadoValor = $objInfoDetalleSolCaractEstadoTecnico->getValor();

                                if($infoDetalleSolCaractEstadoValor == 'Pendiente')
                                {
                                    $objInfoDetalleSolCaractEstadoTecnico->setValor('Rechazada');
                                    $emComercial->persist($objInfoDetalleSolCaractEstadoTecnico);
                                    $emComercial->flush();

                                    /* Historial de la Solicitud Detalle */
                                    $entityInfoDetalleSolHistDet = new InfoDetalleSolHist();
                                    $entityInfoDetalleSolHistDet->setDetalleSolicitudId($entityDetalleSolicitudDet);
                                    $entityInfoDetalleSolHistDet->setEstado($entityDetalleSolicitudDet->getEstado());
                                    $entityInfoDetalleSolHistDet->setObservacion("Detalle rechazado por " . $strAreaTecnica . ". Motivo: ". $arrayParametros['strMotivo']);
                                    $entityInfoDetalleSolHistDet->setFeCreacion(new \DateTime('now'));
                                    $entityInfoDetalleSolHistDet->setUsrCreacion($objSession->get('user'));
                                    $emComercial->persist($entityInfoDetalleSolHistDet);
                                    $emComercial->flush();
                                    
                                    $entityDetalleSolicitudDet->setEstado('Rechazada');
                                    $emComercial->persist($entityDetalleSolicitudDet);
                                    $emComercial->flush();

                                    $totalDetallesRechazadas++;
                                }
                                
                                $objTipoSolicitud = $entityDetalleSolicitudDet->getTipoSolicitudId();

                                //Variable que guarda el total de solicitudes backups rechazadas
                                $intRechazadasBackups = 0;

                                if($objTipoSolicitud->getDescripcionSolicitud() == 'CAMBIO PLAN')
                                {
                                    //Rechazar solicitudes de servicios backups en caso de existir
                                    $arrayParametrosRechazoBackups                          = array();
                                    $arrayParametrosRechazoBackups['objSolicitudPrincipal'] = $entityDetalleSolicitudDet;
                                    $arrayParametrosRechazoBackups['strMotivo']             = $arrayParametros['strMotivo'];
                                    $arrayParametrosRechazoBackups['strUsrCreacion']        = $objSession->get('user');
                                    $intRechazadasBackups = $serviceTecnico->rechazarSolicitudCambioPlanBackups($arrayParametrosRechazoBackups);
                                }

                                $totalDetallesRechazadas = $totalDetallesRechazadas + $intRechazadasBackups;
                            }
                        }
                    }

                    $intIdDetalleSolicitudCab = $arrayParametros['intIdDetalleSolicitudCab'];
                    //Busca la solicitud Cabecera por id
                    $entityDetalleSolicitudCab = $infoDetalleSolicitud->find($intIdDetalleSolicitudCab);
                    if($entityDetalleSolicitudCab)
                    {
                        /* Historial de la Solicitud Detalle */
                        $entityInfoDetalleSolHistCab = new InfoDetalleSolHist();
                        $entityInfoDetalleSolHistCab->setDetalleSolicitudId($entityDetalleSolicitudCab);
                        $entityInfoDetalleSolHistCab->setObservacion("Se rechazaron ".$totalDetallesRechazadas." Detalles de esta Solicitud Masiva.");
                        $entityInfoDetalleSolHistCab->setEstado($entityDetalleSolicitudCab->getEstado());
                        $entityInfoDetalleSolHistCab->setFeCreacion(new \DateTime('now'));
                        $entityInfoDetalleSolHistCab->setUsrCreacion($objSession->get('user'));
                        $emComercial->persist($entityInfoDetalleSolHistCab);
                        $emComercial->flush();
                              
                        // Se Verifica si Ya no queda ningun detalle en estado Pendiente
                        $arrayParametros = array();
                        $arrayParametros['intIdEmpresa'] = ($objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : "");
                        $arrayParametros['intIdPadre'] = $intIdDetalleSolicitudCab;
                        $arrayParametros['boolMasivas'] = 'false';
                        $resultado = $infoDetalleSolicitud->findSolicitudes($arrayParametros);
                        $solicitudes = $resultado['registros'];
                        $total = $resultado['total'];

                        $totalRechazadasEliminadasFinalizadas = 0;
                        foreach($solicitudes as $solicitud):
                            if($solicitud['estado'] == 'Rechazada' || $solicitud['estado'] == 'Eliminada' || $solicitud['estado'] == 'Finalizada')
                            {
                                $totalRechazadasEliminadasFinalizadas++;
                            }
                        endforeach;
                        
                        // si los totales son iguales quiere decir que no queda ningun detalle pendiente o valido
                        // y la solicitud masiva se rechaza tambien
                        if($total == $totalRechazadasEliminadasFinalizadas)
                        {
                            if($entityDetalleSolicitudCab->getEstado() == 'Pendiente')
                            {
                                $entityDetalleSolicitudCab->setEstado('Finalizada');
                                $emComercial->persist($entityDetalleSolicitudCab);
                                $emComercial->flush();

                                /* Historial de la Solicitud Detalle */
                                $entityInfoDetalleSolHistCab = new InfoDetalleSolHist();
                                $entityInfoDetalleSolHistCab->setDetalleSolicitudId($entityDetalleSolicitudCab);
                                $entityInfoDetalleSolHistCab->setObservacion("Se Finalizó la Solicitud Masiva, ya no tiene ningún detalle pendiente");
                                $entityInfoDetalleSolHistCab->setEstado($entityDetalleSolicitudCab->getEstado());
                                $entityInfoDetalleSolHistCab->setFeCreacion(new \DateTime('now'));
                                $entityInfoDetalleSolHistCab->setUsrCreacion($objSession->get('user'));
                                $emComercial->persist($entityInfoDetalleSolHistCab);
                                $emComercial->flush();
                            }
                        }
                    }
                } else
                {
                    $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                    $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . ' Debe ingresa un Motivo de Rechazo.');
                }
            }
            else
            {
                $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . ' No esta enviando el/los código(s) de la(s) solicitud(es) a rechazar.');
            }
            $emComercial->getConnection()->commit();
            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);
        }
        catch(\Exception $ex)
        {
            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . " " . $ex->getMessage());
            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
        }
        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');
        $objResponse->setContent(json_encode((array) $objReturnResponse));
        return $objResponse;
    }
    
    /**
     * rechazarEjecucionSolicitudAction, rechaza una o varias solicitudes del detalle por Cobranzas
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 24-06-2016
     * 
     * Se agrega rechazo de solicitudes de servicios backups
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 06-04-2017
     *
     * @Secure(roles="ROLE_349-4037")
     */
    public function rechazarEjecucionSolicitudAction()
    {        
        $objRequest = $this->getRequest();
        $objSession = $objRequest->getSession();
        $objReturnResponse = new ReturnResponse();

        $emComercial          = $this->getDoctrine()->getManager("telconet");
        $serviceTecnico       = $this->get('tecnico.InfoServicioTecnico');
        $infoDetalleSolicitud = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud');
        $infoDetalleSolCaract = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract');

        $arrayParametros['intIdDetalleSolicitudCab'] = $objRequest->get('intIdDetalleSolicitudCab');
        $arrayParametros['strIdSolicitudesSeleccionadas'] = $objRequest->get('strIdSolicitudesSeleccionadas');
        $arrayParametros['strMotivo'] = $objRequest->get('strMotivo');

        $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT);
        $emComercial->getConnection()->beginTransaction();
        try
        {
            //Valida que el parámetro no se enviado vacío, caso contrario crea una excepción.
            if(!empty($arrayParametros['strIdSolicitudesSeleccionadas']))
            {
                if(!empty($arrayParametros['strMotivo']))
                {

                    $arrayIdSolicitudes = explode(",", $arrayParametros['strIdSolicitudesSeleccionadas']);
                    $totalDetallesRechazadas = 0;
                    foreach($arrayIdSolicitudes as $idSolicitud)
                    {
                        //Busca la solicitud detalle por id
                        $entityDetalleSolicitudDet = $infoDetalleSolicitud->find($idSolicitud);

                        //Valida que el objeto no sea nulo.
                        if($entityDetalleSolicitudDet)
                        {
                            if($entityDetalleSolicitudDet->getEstado() == 'Rechazada')
                            {
                                continue;
                            }
                            $entityDetalleSolicitudDet->setEstado('Rechazada');
                            $emComercial->persist($entityDetalleSolicitudDet);
                            $emComercial->flush();

                            /* Historial de la Solicitud Detalle */
                            $entityInfoDetalleSolHistDet = new InfoDetalleSolHist();
                            $entityInfoDetalleSolHistDet->setDetalleSolicitudId($entityDetalleSolicitudDet);
                            $entityInfoDetalleSolHistDet->setObservacion("Ejecutar Solicitud Masiva: " . $arrayParametros['strMotivo']);
                            $entityInfoDetalleSolHistDet->setEstado($entityDetalleSolicitudDet->getEstado());
                            $entityInfoDetalleSolHistDet->setFeCreacion(new \DateTime('now'));
                            $entityInfoDetalleSolHistDet->setUsrCreacion($objSession->get('user'));
                            $emComercial->persist($entityInfoDetalleSolHistDet);
                            $emComercial->flush();

                            $arrayCaracteristicasSolicitud = $infoDetalleSolCaract->findBy(array("detalleSolicitudId" => $entityDetalleSolicitudDet));
                            if(count($arrayCaracteristicasSolicitud) > 0)
                            {
                                foreach($arrayCaracteristicasSolicitud as $caracteristicasSolicitud):
                                    $caracteristicasSolicitud->setEstado('Rechazada');
                                    $caracteristicasSolicitud->setFeUltMod(new \DateTime('now'));
                                    $caracteristicasSolicitud->setUsrUltMod($objSession->get('user'));
                                    $emComercial->persist($caracteristicasSolicitud);
                                    $emComercial->flush();
                                endforeach;
                            }
                            $totalDetallesRechazadas++;
                            
                            $objTipoSolicitud = $entityDetalleSolicitudDet->getTipoSolicitudId();
                            
                            //Variable que guarda el total de solicitudes backups rechazadas
                            $intRechazadasBackups = 0;
                            
                            if($objTipoSolicitud->getDescripcionSolicitud() == 'CAMBIO PLAN')
                            {
                                //Rechazar solicitudes de servicios backups en caso de existir
                                $arrayParametrosRechazoBackups                          = array();
                                $arrayParametrosRechazoBackups['objSolicitudPrincipal'] = $entityDetalleSolicitudDet;
                                $arrayParametrosRechazoBackups['strMotivo']             = $arrayParametros['strMotivo'];
                                $arrayParametrosRechazoBackups['strUsrCreacion']        = $objSession->get('user');
                                $intRechazadasBackups = $serviceTecnico->rechazarSolicitudCambioPlanBackups($arrayParametrosRechazoBackups);
                            }
                            
                            $totalDetallesRechazadas = $totalDetallesRechazadas + $intRechazadasBackups;
                        }
                    }

                    $intIdDetalleSolicitudCab = $arrayParametros['intIdDetalleSolicitudCab'];

                    $arrayParametros = array();
                    $arrayParametros['intIdEmpresa'] = ($objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : "");
                    $arrayParametros['intIdPadre'] = $intIdDetalleSolicitudCab;
                    $arrayParametros['boolMasivas'] = 'false';
                    $resultado = $infoDetalleSolicitud->findSolicitudes($arrayParametros);
                    $solicitudes = $resultado['registros'];
                    $total = $resultado['total'];

                    $totalRechazadasEliminadas = 0;
                    foreach($solicitudes as $solicitud):
                        if($solicitud['estado'] == 'Rechazada' || $solicitud['estado'] == 'Eliminada')
                        {
                            $totalRechazadasEliminadas++;
                        }
                    endforeach;
                    
                    //Busca la solicitud Cabecera por id
                    $entityDetalleSolicitudCab = $infoDetalleSolicitud->find($intIdDetalleSolicitudCab);
                    if($entityDetalleSolicitudCab)
                    {
                        /* Historial de la Solicitud Detalle */
                        $entityInfoDetalleSolHistCab = new InfoDetalleSolHist();
                        $entityInfoDetalleSolHistCab->setDetalleSolicitudId($entityDetalleSolicitudCab);
                        $entityInfoDetalleSolHistCab->setObservacion("Ejecutar Solicitud Masiva: Se rechazaron ".$totalDetallesRechazadas." Detalles de esta Solicitud Masiva. ");
                        $entityInfoDetalleSolHistCab->setEstado($entityDetalleSolicitudCab->getEstado());
                        $entityInfoDetalleSolHistCab->setFeCreacion(new \DateTime('now'));
                        $entityInfoDetalleSolHistCab->setUsrCreacion($objSession->get('user'));
                        $emComercial->persist($entityInfoDetalleSolHistCab);
                        $emComercial->flush();
                    }
                        
                    // si los totales son iguales quiere decir que no queda ningun detalle pendiente o valido y la solicitud masiva se rechaza tambien                    
                    if($total == $totalRechazadasEliminadas)
                    {                        
                        //Valida que el objeto no sea nulo.
                        if($entityDetalleSolicitudCab)
                        {
                            if($entityDetalleSolicitudCab->getEstado() != 'Rechazada')
                            {
                                $entityDetalleSolicitudCab->setEstado('Rechazada');
                                $emComercial->persist($entityDetalleSolicitudCab);
                                $emComercial->flush();

                                /* Historial de la Solicitud Detalle */
                                $entityInfoDetalleSolHistCab = new InfoDetalleSolHist();
                                $entityInfoDetalleSolHistCab->setDetalleSolicitudId($entityDetalleSolicitudCab);
                                $entityInfoDetalleSolHistCab->setObservacion($arrayParametros['strMotivo']);
                                $entityInfoDetalleSolHistCab->setEstado($entityDetalleSolicitudCab->getEstado());
                                $entityInfoDetalleSolHistCab->setFeCreacion(new \DateTime('now'));
                                $entityInfoDetalleSolHistCab->setUsrCreacion($objSession->get('user'));
                                $emComercial->persist($entityInfoDetalleSolHistCab);
                                $emComercial->flush();

                                $arrayCaracteristicasSolicitud = $infoDetalleSolCaract->findBy(array("detalleSolicitudId" => $entityDetalleSolicitudCab));
                                if(count($arrayCaracteristicasSolicitud) > 0)
                                {
                                    foreach($arrayCaracteristicasSolicitud as $caracteristicasSolicitud):
                                        $caracteristicasSolicitud->setEstado('Rechazada');
                                        $caracteristicasSolicitud->setFeUltMod(new \DateTime('now'));
                                        $caracteristicasSolicitud->setUsrUltMod($objSession->get('user'));
                                        $emComercial->persist($caracteristicasSolicitud);
                                        $emComercial->flush();
                                    endforeach;
                                }
                            }
                        }
                    }
                } else
                {
                    $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                    $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . ' Debe ingresa un Motivo de Rechazo.');
                }
            }
            else
            {
                $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . ' No esta enviando el/los código(s) de la(s) solicitud(es) a rechazar.');
            }
            $emComercial->getConnection()->commit();
            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);
        }
        catch(\Exception $ex)
        {
            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . " " . $ex->getMessage());
            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
        }
        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');
        $objResponse->setContent(json_encode((array) $objReturnResponse));
        return $objResponse;
    }
    
    /**
     * generarExcelSolicitudMasiva, crea un archivo Excel en base a la información de la Solicitud Masiva
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 27-06-2016
     *
     * @param int $intIdEmpresa Identificador de la empresa
     * @param string $strPrefijoEmpresa Prefijo de la empresa
     * @param int $intIdDetalleSolicitudCab Identificador de la cabecera de la solicitud masiva
     * 
     * @return string Ruta del archivo excel generado
     * 
     * @author Modificado: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.1 11-08-2016 - Se cambio la consulta de caracteristicas de la cabecera a los detalles
     * 
     * @author Modificado: David León <mdleon@telconet.ec>
     * @version 2.0 05-07-2022 - Modificamos la ruta del directorio.
     * 
     */
    public function generarExcelSolicitudMasiva($intIdEmpresa, $strPrefijoEmpresa, $intIdDetalleSolicitudCab)
    {
        $strUbicacionFisica = '';
        try
        {
            $emComercial                = $this->getDoctrine()->getManager("telconet");
            $emInfraestructura          = $this->getDoctrine()->getManager("telconet_infraestructura");
            $infoDetalleSolicitud       = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud');
            $infoPersona                = $emComercial->getRepository('schemaBundle:InfoPersona');
            $infoPersonaFormaContacto   = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto');
            $admiFormaContacto          = $emComercial->getRepository('schemaBundle:AdmiFormaContacto');
            $infoPuntoContacto          = $emComercial->getRepository('schemaBundle:InfoPuntoContacto');
            $infoServicioTecnico        = $emComercial->getRepository('schemaBundle:InfoServicioTecnico');
            $admiImpuesto               = $emComercial->getRepository('schemaBundle:AdmiImpuesto');
            $admiTipoMedio              = $emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio');

            $infoDetalleSolicitudCabecera = $infoDetalleSolicitud->find($intIdDetalleSolicitudCab);

            if($infoDetalleSolicitudCabecera)
            {
                $strTipoSolicitud = $infoDetalleSolicitudCabecera->getTipoSolicitudId()->getDescripcionSolicitud();                
                $strTitulo        = "ORDENES DE SERVICIOS";
                $strSubTitulo     = "FOR GCO UIO 01 ORDEN DE SERVICIO ver 11 01 08";
                $strTituloTabla   = "DESCRIPCIÓN DE LOS ENLACES CONTRATADOS";
                $boolTotales      = true;
                
                if($strTipoSolicitud == "CANCELACION")
                {
                    $strTitulo      = "ORDENES DE CANCELACIÓN";
                    $strSubTitulo   = "";
                    $strTituloTabla = "DESCRIPCIÓN DE CANCELACIONES";
                    $boolTotales    = false;
                }
                    
                $strTipoSolicitud                = ucfirst(strtolower($strTipoSolicitud));
                
                $arrayParametros                 = array();
                $arrayParametros['intIdEmpresa'] = $intIdEmpresa;
                $arrayParametros['intIdPadre']   = $infoDetalleSolicitudCabecera->getId();
                $arrayParametros['boolMasivas']  = 'false';
                $resultado                       = $infoDetalleSolicitud->findSolicitudes($arrayParametros);
                $solicitudes                     = $resultado['registros'];
                $total                           = $resultado['total'];
                
                $strNombreUsrCreacion            = '';
                $infoPersonaComercial            = $infoPersona->findOneBy(array("login" => $infoDetalleSolicitudCabecera->getUsrCreacion()));
                if($infoPersonaComercial)
                {
                    $strNombreUsrCreacion = $infoPersonaComercial->__tostring();
                }

                //Si existen comprobantes
                if($total > 0)
                {
                    
                    $infoPersonaCliente = $infoPersona->find($solicitudes[0]['idCliente']);
                    $strNombreArchivo   = 'Solicitud_Masiva_'.$intIdDetalleSolicitudCab.'_'.date('d_M_Y').'.xls';
                    $fileRoot           = $this->container->getParameter('ruta_upload');
                    $path               = $this->container->getParameter('path_telcos');
                    $strNombreArchivo   = str_replace(" ", "_", $strNombreArchivo);

                    $strSubDir      = $strPrefijoEmpresa . "/comercial/solicitudesmasivasTemp/" ;
                    $destino        = $fileRoot . $strSubDir;

                    //Se Genera el Directorio si no existe
                    $comercialService  = $this->get('comercial.Comercial');
                    $objReturnResponse = $comercialService->validateDirExistCreate(["strPathTelcos" => $path, "strPath" => $destino]);
                    if("100" === $objReturnResponse->getStrStatus())
                    {
                        $strUbicacionFisica = $path . $destino . $strNombreArchivo;

                        $objPHPExcel    = new PHPExcel();
                        $cacheMethod    = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
                        $cacheSettings  = array(' memoryCacheSize ' => '1024MB');
                        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

                        $objPHPExcel->getProperties()->setCreator("TELCOS++");
                        $objPHPExcel->getProperties()->setLastModifiedBy($infoDetalleSolicitudCabecera->getUsrCreacion());
                        $objPHPExcel->getProperties()->setTitle("Solicitud Masiva de " . $strTipoSolicitud);
                        $objPHPExcel->getProperties()->setSubject("Solicitud Masiva " . $strTipoSolicitud . ": " . $intIdDetalleSolicitudCab);
                        $objPHPExcel->getProperties()->setDescription("Muestra el resumen detallado de la Solicitud Masiva con todos sus detalles");
                        $objPHPExcel->getProperties()->setKeywords("Solicitud Masiva, " . $strTipoSolicitud);
                        $objPHPExcel->getProperties()->setCategory("Reporte");

                        //Crea estilo para el titulo del reporte
                        $arrayStyleBold = array(
                                                'font'      => array(
                                                                    'bold'  => true,
                                                                    'color' => array('rgb' => '000000'),
                                                                    'size'  => 10,
                                                                    'name'  => 'LKLUG'
                                                                    ),
                                                'alignment' => array(
                                                                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                                                    'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER
                                                                    )
                                                );
                        
                        //Crea estilo para el titulo del reporte
                        $arrayStyleTitulo   = array(
                                                    'font'      => array(
                                                                        'bold'  => true,
                                                                        'color' => array('rgb' => '006699'),
                                                                        'size'  => 12,
                                                                        'name'  => 'LKLUG'
                                                                        ),
                                                    'alignment' => array(
                                                                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                                                        'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER
                                                                        ),
                                                    'fill'      => array(
                                                                        'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                                                                        'color' => array('rgb' => 'FFFFFF')
                                                                        )
                                                    );
                        
                        //Crea estilo para el subtitulo del reporte
                        $arrayStyleSubTitulo = array(
                                                    'font'          => array(
                                                                            'bold'  => true,
                                                                            'color' => array('rgb' => '006699'),
                                                                            'size'  => 8,
                                                                            'name'  => 'LKLUG'
                                                                            ),
                                                    'alignment'     => array(
                                                                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                                                            'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER
                                                                            ),
                                                    'fill'          => array(
                                                                            'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                                                                            'color' => array('rgb' => 'FFFFFF')
                                                                            )
                                                    );

                        //Crea estilo para la cabecera del reporte
                        $arrayStyleCabecera = array(
                                                    'font'          => array(
                                                                            'bold'  => false,
                                                                            'color' => array('rgb' => 'FFFFFF'),
                                                                            'size'  => 10,
                                                                            'name'  => 'LKLUG'
                                                                            ),
                                                    'alignment'     => array(
                                                                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                                                            'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER
                                                                            ),
                                                    'fill'          => array(
                                                                            'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                                                                            'color' => array('rgb' => '888888')
                                                                            )
                                                    );

                        //Crea estilo para el cuerpo del reporte
                        $arrayStyleBodyTable = array(
                                                    'font'          => array(
                                                                            'bold'  => false,
                                                                            'color' => array('rgb' => '000000'),
                                                                            'size'  => 8,
                                                                            'name'  => 'LKLUG'
                                                                            ),
                                                    'alignment'     => array(
                                                                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                                                            'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER
                                                                            ),
                                                    'fill'          => array(
                                                                            'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                                                                            'color' => array('rgb' => 'FFFFFF')
                                                                            )
                                                    );

                        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(70);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
                        $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(60);
                        $objPHPExcel->getActiveSheet()->mergeCells('A1:I1');
                        $objPHPExcel->getActiveSheet()->setCellValue('A1', $strTitulo);
                        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($arrayStyleTitulo);
                        $objPHPExcel->getActiveSheet()->mergeCells('A2:I2');
                        $objPHPExcel->getActiveSheet()->setCellValue('A2', $strSubTitulo);
                        $objPHPExcel->getActiveSheet()->getStyle('A2')->applyFromArray($arrayStyleSubTitulo);

                        //Obtiene la ruta de la imagen
                        $strPath  = $this->get('kernel')->getRootDir() . '/../web/public/images/telconet.jpg';
                        $objImage = imagecreatefromjpeg($strPath);
                        //Si obtiene la imagen la crea en la celda A1
                        if($objImage){
                            $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
                            $objDrawing->setName('TELCOS++');
                            $objDrawing->setDescription('TELCOS++');
                            $objDrawing->setImageResource($objImage);
                            $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
                            $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
                            $objDrawing->setHeight(100);
                            $objDrawing->setWidth(138);
                            $objDrawing->setCoordinates('A1');
                            $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                        }
                        
                        // Datos de la Cabecera
                        
                        $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValue('A4', 'Fecha')
                                    ->setCellValue('A6', 'Ejecutivo Comercial');
                        $objPHPExcel->getActiveSheet()->getStyle('A4:A6')->applyFromArray($arrayStyleBold);
                        
                        $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValue('B4', date_format($infoDetalleSolicitudCabecera->getFeCreacion(), 'Y-m-d'))
                                    ->setCellValue('B6', $strNombreUsrCreacion);
                        
                        $objPHPExcel->getActiveSheet()->mergeCells('A8:I8');
                        $objPHPExcel->getActiveSheet()->setCellValue('A8', 'INFORMACIÓN ADMINISTRATIVA DEL CLIENTE');
                        $objPHPExcel->getActiveSheet()->getStyle('A8')->applyFromArray($arrayStyleTitulo);
                        
                        $objPHPExcel->getActiveSheet()->mergeCells('A10:B10');
                        $objPHPExcel->getActiveSheet()->setCellValue('A10', 'DATOS DEL CLIENTE');
                        $objPHPExcel->getActiveSheet()->getStyle('A10')->applyFromArray($arrayStyleTitulo);
                        
                        $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValue('A11', 'Razón Social')
                                    ->setCellValue('A12', 'Tipo de Negocio')
                                    ->setCellValue('A13', 'Identificación del Cliente')
                                    ->setCellValue('A14', 'Representante Legal del Cliente')
                                    ->setCellValue('A15', 'Dirección')
                                    ->setCellValue('A16', 'País')
                                    ->setCellValue('A17', 'Teléfono')
                                    ->setCellValue('A18', 'Fax')
                                    ->setCellValue('A19', 'Email');
                        $objPHPExcel->getActiveSheet()->getStyle('A11:A19')->applyFromArray($arrayStyleBold);                        
                                                                        
                        if($infoPersonaCliente)
                        {
                            $strNombrePais   = '';
                            $infoPaisCliente = $infoPersonaCliente->getPaisId();
                            if($infoPaisCliente)
                            {
                                $strNombrePais = $infoPaisCliente->getNombrePais();
                            }
                            
                            $strTelefono              = "";
                            $admiFormaContactoCliente = $admiFormaContacto->findOneBy(array("descripcionFormaContacto"=>"Telefono Fijo"));
                            if($admiFormaContactoCliente)
                            {
                              $infoPersonaFormaContactoCliente = $infoPersonaFormaContacto->findOneBy(array("formaContactoId" => $admiFormaContactoCliente, "personaId" => $infoPersonaCliente));                            
                                if($infoPersonaFormaContactoCliente)
                                {
                                    $strTelefono = $infoPersonaFormaContactoCliente->getValor();
                                }
                            }
                            
                            $strFax                   = "";
                            $admiFormaContactoCliente = $admiFormaContacto->findOneBy(array("descripcionFormaContacto"=>"Fax"));
                            if($admiFormaContactoCliente)
                            {
                              $infoPersonaFormaContactoCliente = $infoPersonaFormaContacto->findOneBy(array("formaContactoId" => $admiFormaContactoCliente, "personaId" => $infoPersonaCliente));                            
                                if($infoPersonaFormaContactoCliente)
                                {
                                    $strFax = $infoPersonaFormaContactoCliente->getValor();
                                }
                            }
                            
                            $strEmail                 = "";
                            $admiFormaContactoCliente = $admiFormaContacto->findOneBy(array("descripcionFormaContacto"=>"Correo Electronico"));
                            if($admiFormaContactoCliente)
                            {
                              $infoPersonaFormaContactoCliente = $infoPersonaFormaContacto->findOneBy(array("formaContactoId" => $admiFormaContactoCliente, "personaId" => $infoPersonaCliente));                            
                                if($infoPersonaFormaContactoCliente)
                                {
                                    $strEmail = $infoPersonaFormaContactoCliente->getValor();
                                }
                            }
                            
                            $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('B11', $infoPersonaCliente->__toString())
                            ->setCellValue('B12', $infoPersonaCliente->getTipoEmpresa())
                            ->setCellValueExplicit('B13', $infoPersonaCliente->getIdentificacionCliente(), PHPExcel_Cell_DataType::TYPE_STRING)
                            ->setCellValue('B14', $infoPersonaCliente->getRepresentanteLegal())
                            ->setCellValue('B15', $infoPersonaCliente->getDireccion())
                            ->setCellValue('B16', $strNombrePais)
                            ->setCellValue('B17', $strTelefono)
                            ->setCellValue('B18', $strFax)
                            ->setCellValue('B19', $strEmail);
                        }
                        
                        $objPHPExcel->getActiveSheet()->mergeCells('A21:I21');
                        $objPHPExcel->getActiveSheet()->setCellValue('A21', $strTituloTabla);
                        $objPHPExcel->getActiveSheet()->getStyle('A21')->applyFromArray($arrayStyleTitulo);
                                                
                        $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValue('A23', 'Sitio Origen (Direccion)')
                                    ->setCellValue('B23', 'Sitio Destino (Direccion)')
                                    ->setCellValue('C23', 'Contacto Destino')
                                    ->setCellValue('D23', 'Telefono Destino')
                                    ->setCellValue('E23', 'Medio de Trasmision (Ultima Milla)')
                                    ->setCellValue('F23', 'BW S/B (Kbps)')
                                    ->setCellValue('G23', 'Servicios Contratados (Datos o Internet)')
                                    ->setCellValue('H23', 'Precio Mensual USD')
                                    ->setCellValue('I23', 'User Sistema');
                        $objPHPExcel->getActiveSheet()->getStyle('A23:I23')->applyFromArray($arrayStyleCabecera);
                        $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(20);
                        $objPHPExcel->getActiveSheet()->getStyle('A23:I23')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

                        $intCounterRows = 24;
                        $intSubtotal = 0;
                        foreach($solicitudes as $solicitudDetalle):
                            
                            $infoDetalleSolicitudDetalle = $infoDetalleSolicitud->find($solicitudDetalle['idDetalleSolicitud']);                        
                            $strDireccionPunto           = '';
                            $strNombreConctactoPunto     = '';
                            $strUltimaMilla              = '';
                            // Cambiar el ID del $intIdDetalleSolicitudCab a $solicitudDetalle['idDetalleSolicitud'] cuando cambie el guardado
                            $strCapacidad1 = $this->getValorCaracteristicaDetalleSolicitud($solicitudDetalle['idDetalleSolicitud'], "CAPACIDAD1");
                            $strCapacidad2 = $this->getValorCaracteristicaDetalleSolicitud($solicitudDetalle['idDetalleSolicitud'], "CAPACIDAD2");
                            $strPrecio     = $this->getValorCaracteristicaDetalleSolicitud($solicitudDetalle['idDetalleSolicitud'], "Precio");
                            
                            $strCapacidad1Anterior = 0;
                            $strCapacidad2Anterior = 0;
                            $strPrecioAnterior     = 0;
                            
                            $strDescipcionServicioNuevo = '';
                            $strLoginPunto              = '';
                            $strTelefonoDestino         = '';
                            
                            if($infoDetalleSolicitudDetalle)
                            {
                                $infoServicioEntity = $infoDetalleSolicitudDetalle->getServicioId();
                                if($infoServicioEntity)
                                {
                                    $strDescipcionServicioNuevo = $infoServicioEntity->getDescripcionPresentaFactura();                                    
                                    $strCapacidad1Anterior      = $this->getValorCaracteristicaServicio($infoServicioEntity, "CAPACIDAD1");
                                    $strCapacidad2Anterior      = $this->getValorCaracteristicaServicio($infoServicioEntity, "CAPACIDAD2");
                                    $strPrecioAnterior          = $infoServicioEntity->getPrecioVenta();
                                    
                                    $infoPuntoEntity            = $infoServicioEntity->getPuntoId();
                                    if($infoPuntoEntity)
                                    {
                                        $strDireccionPunto       = $infoPuntoEntity->getDireccion();
                                        $strLoginPunto           = $infoPuntoEntity->getLogin();
                                        $infoPuntoContactoEntity = $infoPuntoContacto->findOneBy(array("puntoId" => $infoPuntoEntity));
                                        if($infoPuntoContactoEntity)
                                        {
                                            $infoPersonaContacto = $infoPuntoContactoEntity->getContactoId();
                                            if($infoPersonaContacto)
                                            {
                                                $strNombreConctactoPunto = $infoPersonaContacto->__toString();
                                                
                                                $admiFormaContactoCliente = $admiFormaContacto->findOneBy(array(
                                                    "descripcionFormaContacto"=>"Telefono Fijo"));
                                                if($admiFormaContactoCliente)
                                                {
                                                  $infoPersonaFormaContactoCliente = $infoPersonaFormaContacto->findOneBy(array(
                                                      "formaContactoId" => $admiFormaContactoCliente, "personaId" => $infoPersonaContacto));                            
                                                    if($infoPersonaFormaContactoCliente)
                                                    {
                                                        $strTelefonoDestino = $infoPersonaFormaContactoCliente->getValor();
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    
                                    $infoServicioTecnicoEntity = $infoServicioTecnico->findOneBy(array("servicioId" => $infoServicioEntity));
                                    if($infoServicioTecnicoEntity)
                                    {
                                        $intUltimaMillaId    = $infoServicioTecnicoEntity->getUltimaMillaId();
                                        $admiTipoMedioEntity = $admiTipoMedio->find($intUltimaMillaId);
                                        if($admiTipoMedioEntity)
                                        {
                                            $strUltimaMilla = $admiTipoMedioEntity->getNombreTipoMedio();
                                        }
                                    }
                                }                                
                                
                                $strCapacidad1Final = !empty($strCapacidad1) ? $strCapacidad1 : $strCapacidad1Anterior ;
                                $strCapacidad2Final = !empty($strCapacidad2) ? $strCapacidad2 : $strCapacidad2Anterior ;
                                $strPrecioFinal     = !empty($strPrecio) ? $strPrecio : $strPrecioAnterior ;
                                
                                $strCapacidades     = !empty($strCapacidad1Final) ? $strCapacidad1Final : "N" ;
                                $strCapacidades    .= "/" ;
                                $strCapacidades    .= !empty($strCapacidad2Final) ? $strCapacidad2Final : "A" ;
                                
                                $strPrecioFinal     = !empty($strPrecioFinal) ? $strPrecioFinal : "N/A" ;
                                
                                $objPHPExcel->getActiveSheet()->getStyle('A'. $intCounterRows .':I'. $intCounterRows)
                                            ->applyFromArray($arrayStyleBodyTable);
                                $objPHPExcel->getActiveSheet()->getStyle('A'. $intCounterRows .':I'. $intCounterRows)
                                            ->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                                $objPHPExcel->getActiveSheet()->getRowDimension($intCounterRows)->setRowHeight(20);

                                $objPHPExcel->getActiveSheet()->setCellValue('A'. $intCounterRows, $strDireccionPunto);
                                $objPHPExcel->getActiveSheet()->setCellValue('B'. $intCounterRows, $strDireccionPunto);
                                $objPHPExcel->getActiveSheet()->setCellValue('C'. $intCounterRows, $strNombreConctactoPunto);
                                $objPHPExcel->getActiveSheet()->setCellValue('D'. $intCounterRows, $strTelefonoDestino);
                                $objPHPExcel->getActiveSheet()->setCellValue('E'. $intCounterRows, $strUltimaMilla);
                                $objPHPExcel->getActiveSheet()->setCellValue('F'. $intCounterRows, $strCapacidades);
                                $objPHPExcel->getActiveSheet()->setCellValue('G'. $intCounterRows, $strDescipcionServicioNuevo);
                                
                                $objPHPExcel->getActiveSheet()->setCellValue('H'. $intCounterRows, $strPrecioFinal);
                                $objPHPExcel->getActiveSheet()->getStyle('H'. $intCounterRows)->getAlignment()
                                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                                
                                $objPHPExcel->getActiveSheet()->setCellValue('I'. $intCounterRows, $strLoginPunto);

                                $intCounterRows = $intCounterRows + 1;
                                $intSubtotal    = $intSubtotal + $strPrecio;
                            }
                        endforeach;                        
                        
                        if($boolTotales)
                        {
                            $intPorcentajeIva       = "";
                            $strDescripcionImpuesto = "";
                            $admiImpuestoEntity     = $admiImpuesto->findOneBy(array("tipoImpuesto" => "IVA","estado" => "Activo"));
                            if($admiImpuestoEntity)
                            {
                                $intPorcentajeIva       = $admiImpuestoEntity->getPorcentajeImpuesto();
                                $strDescripcionImpuesto = $admiImpuestoEntity->getDescripcionImpuesto();
                            }

                            $objPHPExcel->setActiveSheetIndex(0)
                                        ->setCellValue('G'.($intCounterRows+1), 'Subtotal')
                                        ->setCellValue('G'.($intCounterRows + 2), $strDescripcionImpuesto)
                                        ->setCellValue('G'.($intCounterRows + 3), 'Total');
                            $objPHPExcel->getActiveSheet()->getStyle('G'.($intCounterRows+1).':G'.($intCounterRows+3))
                                        ->applyFromArray($arrayStyleCabecera);
                            $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(20);
                            $objPHPExcel->getActiveSheet()->getStyle('G'.($intCounterRows+1).':G'.($intCounterRows+3))->getBorders()->getAllBorders()
                                        ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

                            $intIva   = ($intSubtotal * $intPorcentajeIva) / 100;
                            $intTotal = $intSubtotal + $intIva;

                            $objPHPExcel->setActiveSheetIndex(0)
                                        ->setCellValue('H'.($intCounterRows+1), $intSubtotal)
                                        ->setCellValue('H'.($intCounterRows + 2), $intIva)
                                        ->setCellValue('H'.($intCounterRows + 3), $intTotal );
                        }
                        
                        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
                        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

                        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                        $objWriter->save($strUbicacionFisica);
                    }
                }
            }
        } catch (\Exception $ex) {
            error_log($ex->getMessage());
        }
        return $strUbicacionFisica;
    }
    
    /**
     * Documentación para el método 'downloadExcelAction'.
     * Este metodo obtiene los un documento Excel a partir del identificador de la SM
     *
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 27-06-2016
     * 
     * @author Modificado: David León <mdleon@telconet.ec>
     * @version 2.0 05-07-2022 - Eliminamos archivo del directorio luego de enviarlo.
     *
     * 
     */
    public function downloadExcelAction()
    {
        error_reporting(E_ALL);
        ini_set('max_execution_time', 3000000);
        $objRequest = $this->getRequest();
        $objSession = $objRequest->getSession();        
        $intIdEmpresa = $objSession->get('idEmpresa');
        $strPrefijoEmpresa = $objSession->get('prefijoEmpresa');
        $intIdDetalleSolicitudCab = $objRequest->get('id');
        
        $strUrl = $this->generarExcelSolicitudMasiva($intIdEmpresa, $strPrefijoEmpresa, $intIdDetalleSolicitudCab);

        $arrayUrl = explode("/", $strUrl);
        //$path = $this->container->getParameter('path_telcos');
        $file = fopen($strUrl, "r") or die("Unable to open file!");
        $strFile = fread($file, filesize($strUrl));
        fclose($file);
        unlink($strUrl);

        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'application/vnd.ms-excel');
        $objResponse->headers->set('Content-Disposition', 'attachment;filename="' . $arrayUrl[sizeof($arrayUrl) - 1]);
        $objResponse->headers->set('Cache-Control', 'max-age=0');
        $objResponse->setContent($strFile);
        return $objResponse;
    }
    
    /**
     * Documentación para el método 'getValorCaracteristicaDetalleSolicitud'.
     * Este metodo obtiene la caracteristica asociada a un DetalleSolicitud por su nombre de Caracteristica
     *
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 27-06-2016
     * 
     * @param int $intIdDetalleSolicitud Identificador del Detalle Solicitud
     * @param string $strNombreCaracteristica Nombre de la caracteristica
     * 
     */
    public function getValorCaracteristicaDetalleSolicitud($intIdDetalleSolicitud, $strNombreCaracteristica)
    {
        $strValor = '';
        
        $emComercial = $this->getDoctrine()->getManager("telconet");        
        $admiCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica');
        $infoDetalleSolCaract = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract');
        
        $admiCaracteristicaEntity = $admiCaracteristica->findOneBy(array("descripcionCaracteristica" => $strNombreCaracteristica));
        if($admiCaracteristicaEntity)
        {
            $infoDetalleSolCaractEntity = $infoDetalleSolCaract->findOneBy(array("detalleSolicitudId" => $intIdDetalleSolicitud, "caracteristicaId" => $admiCaracteristicaEntity));
            if($infoDetalleSolCaractEntity)
            {
                $strValor = $infoDetalleSolCaractEntity->getValor();
            }
        }
        
        return $strValor;
    }
    
    /**
     * Documentación para el método 'getValorCaracteristicaDetalleSolicitud'.
     * Este metodo obtiene la caracteristica asociada a un Servicio por su nombre de Caracteristica
     *
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 27-06-2016
     * 
     * @param object $infoServicioEntity Entidad InfoServicio
     * @param string $strNombreCaracteristica Nombre de la caracteristica
     * 
     */
    public function getValorCaracteristicaServicio($infoServicioEntity, $strNombreCaracteristica)
    {
        $strValor = '';
        $emComercial = $this->getDoctrine()->getManager("telconet");        
        $admiCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica');
        $admiProductoCaracteristica = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica');
        $infoServicioProdCaract = $emComercial->getRepository('schemaBundle:InfoServicioProdCaract');
        
        $admiCaracteristicaEntity = $admiCaracteristica->findOneBy(array("descripcionCaracteristica" => $strNombreCaracteristica));
        if($admiCaracteristicaEntity && $infoServicioEntity)
        {
            $admiProductoCaracteristicaEntity = $admiProductoCaracteristica->findOneBy(array("productoId" => $infoServicioEntity->getProductoId(), "caracteristicaId" => $admiCaracteristicaEntity));
            if($admiProductoCaracteristicaEntity)
            {
                $infoServicioProdCaractEntity = $infoServicioProdCaract->findOneBy(array("servicioId" => $infoServicioEntity, "productoCaracterisiticaId" => $admiProductoCaracteristicaEntity));
                if($infoServicioProdCaractEntity)
                {
                    $strValor = $infoServicioProdCaractEntity->getValor();
                }
            }            
        }
        return $strValor;
    }
    
    /**
     * Documentación para el método 'getPersonaNombreCorreo'.
     * Este metodo obtiene un array con el nombre de la persona y su correo de tenerlo por medio de su login
     *
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 06-07-2016
     * 
     * @param string $strUsrLogin Login del usuario a ser consultar
     * @return array con los datos de la persona
     */
    public function getPersonaNombreCorreo($strUsrLogin)
    {
        $arrayResultado = array();
        $emComercial = $this->getDoctrine()->getManager("telconet"); 
        $infoPersona = $emComercial->getRepository('schemaBundle:InfoPersona');
        $admiFormaContacto = $emComercial->getRepository('schemaBundle:AdmiFormaContacto');
        $infoPersonaFormaContacto = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto');
        
        $entityInfoPersona = $infoPersona->findOneBy(array("login" => $strUsrLogin));
        if($entityInfoPersona)
        {
            $arrayResultado['nombre'] = $entityInfoPersona->__toString();

            $entityAdmiFormaContacto = $admiFormaContacto->findOneBy(array("descripcionFormaContacto"=>"Correo Electronico"));
            if($entityAdmiFormaContacto)
            {
              $entityInfoPersonaFormaContacto = $infoPersonaFormaContacto->findOneBy(array("formaContactoId" => $entityAdmiFormaContacto, "personaId" => $entityInfoPersona));                            
                if($entityInfoPersonaFormaContacto)
                {
                    $arrayResultado['correo'] = $entityInfoPersonaFormaContacto->getValor();
                }
            }
        }                    
        return $arrayResultado;
    }
    
    /**
     * Documentación para el método 'getPersonaNombreCorreoByInfoPersona'.
     * Este metodo obtiene un array con el nombre de la persona y su correo de tenerlo por medio la persona
     *
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 11-07-2016
     * 
     * @param Object $entityInfoPersona persona a ser consultar
     * @return array con los datos de la persona
     */
    public function getPersonaNombreCorreoByInfoPersona($entityInfoPersona)
    {
        $arrayResultado = array();
        $emComercial = $this->getDoctrine()->getManager("telconet"); 
        $infoPersona = $emComercial->getRepository('schemaBundle:InfoPersona');
        $admiFormaContacto = $emComercial->getRepository('schemaBundle:AdmiFormaContacto');
        $infoPersonaFormaContacto = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto');
        
        if($entityInfoPersona)
        {
            $arrayResultado['nombre'] = $entityInfoPersona->__toString();

            $entityAdmiFormaContacto = $admiFormaContacto->findOneBy(array("descripcionFormaContacto"=>"Correo Electronico"));
            if($entityAdmiFormaContacto)
            {
              $entityInfoPersonaFormaContacto = $infoPersonaFormaContacto->findOneBy(array("formaContactoId" => $entityAdmiFormaContacto, "personaId" => $entityInfoPersona));                            
                if($entityInfoPersonaFormaContacto)
                {
                    $arrayResultado['correo'] = $entityInfoPersonaFormaContacto->getValor();
                }
            }
        }                    
        return $arrayResultado;
    }
    
    /**
     * getOficinasAction, Obtiene las oficinas en estado Activo y segun la empresa en sesion
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 14-07-2016
     * @return json Retorna un json de las oficinas en estado Activo y segun la empresa
     * 
     */
    public function getOficinasAction()
    {
        $objRequest = $this->get('request');
        $objSession = $objRequest->getSession();
        $intIdEmpresa = $objSession->get('idEmpresa');
        
        $emInfraestructura  = $this->getDoctrine()->getManager("telconet_infraestructura"); 
        $admiJurisdiccion   =  $emInfraestructura->getRepository('schemaBundle:AdmiJurisdiccion');
        
        $objResponse = new Response($admiJurisdiccion->generarJsonJurisdiccionesPorEmpresa($intIdEmpresa));
        $objResponse->headers->set('Content-type', 'text/json');
        
        return $objResponse;
    }
    
    /**
     * Documentación para el método 'getCaracteristicaServicio'.
     * Este metodo obtiene la caracteristica asociada a un Servicio por su nombre de Caracteristica
     *
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 26-07-2016
     * 
     * @param object $infoServicioEntity Entidad InfoServicio
     * @param string $strNombreCaracteristica Nombre de la caracteristica
     * @return object InfoServicioProdCaract
     */
    public function getCaracteristicaServicio($infoServicioEntity, $strNombreCaracteristica)
    {
        $infoServicioProdCaractEntity   = null;
        
        $emComercial                    = $this->getDoctrine()->getManager("telconet");        
        $admiCaracteristica             = $emComercial->getRepository('schemaBundle:AdmiCaracteristica');
        $admiProductoCaracteristica     = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica');
        $infoServicioProdCaract         = $emComercial->getRepository('schemaBundle:InfoServicioProdCaract');        
        $admiCaracteristicaEntity       = $admiCaracteristica->findOneBy(array("descripcionCaracteristica" => $strNombreCaracteristica));
        
        if($admiCaracteristicaEntity && $infoServicioEntity)
        {
            $admiProductoCaracteristicaEntity = $admiProductoCaracteristica->findOneBy(array("productoId" => $infoServicioEntity->getProductoId(), "caracteristicaId" => $admiCaracteristicaEntity));
            if($admiProductoCaracteristicaEntity)
            {
                $infoServicioProdCaractEntity = $infoServicioProdCaract->findOneBy(array("servicioId" => $infoServicioEntity, "productoCaracterisiticaId" => $admiProductoCaracteristicaEntity));
            }            
        }
        return $infoServicioProdCaractEntity;
    }

    /**
     * getModeloCpeByServicioTipo, el modelo del Elemento CPE asociado a un servicio
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 28-07-2016
     * 
     * @param int intServicioId Identificador del Servicio
     * @return Object Retorna un objecto del tipo InfoModeloElemento
     * 
     */
    private function getModeloCpeByServicioTipo($intServicioId)
    {
        $infoModeloElemento     = null;        
        $emComercial            = $this->getDoctrine()->getManager("telconet");
        $emInfraestructura      = $this->getDoctrine()->getManager("telconet_infraestructura");
        $infoServicioTecnico    = $emComercial->getRepository('schemaBundle:InfoServicioTecnico');
        $infoElemento           = $emInfraestructura->getRepository('schemaBundle:InfoElemento');
        
        $infoServicioTecnicoEntity = $infoServicioTecnico->findOneBy(array("servicioId" => $intServicioId));
        if($infoServicioTecnicoEntity)
        {
            // Validacion del Nivel de Aprobacion del Ancho de Banda para IPCCL2
            $intInterfaceElementoConectorId = $infoServicioTecnicoEntity->getInterfaceElementoConectorId();
            // Si es diferente de NULL entonces debo obtener el CPE con un metodo en caso contrario ya es un CPE
            $intElementoClienteId = null;
            if($intInterfaceElementoConectorId)
            {
                $arrayParametrosElemento                                = array();
                $arrayParametrosElemento['interfaceElementoConectorId'] = $intInterfaceElementoConectorId;
                $arrayParametrosElemento['tipoElemento']                = 'CPE';
                
                $elementoClienteIdResult = $infoElemento->getElementoClienteByTipoElemento($arrayParametrosElemento);

                if($elementoClienteIdResult['msg'] == 'FOUND')
                {
                    $intElementoClienteId = $elementoClienteIdResult['idElemento'];
                }
            }
            else
            {
                $intElementoClienteId = $infoServicioTecnicoEntity->getElementoClienteId();
            }

            if($intElementoClienteId)
            {
                $infoElementoEntity = $infoElemento->find($intElementoClienteId);
                if($infoElementoEntity)
                {
                    $infoModeloElemento = $infoElementoEntity->getModeloElementoId();

                    if($infoModeloElemento)
                    {
                        $admiTipoElementoEntity = $infoModeloElemento->getTipoElementoId();
                        if($admiTipoElementoEntity)
                        {
                            // Se verifica que el nombre el Tipo Elemento tenga 'CPE'
                            $strNombreTipoElemento = $admiTipoElementoEntity->getNombreTipoElemento();
                            if(strpos($strNombreTipoElemento, 'CPE') === false)
                            {
                                $infoModeloElemento = null;
                            }
                        }
                    }
                }
            }
        }
        return $infoModeloElemento;
    }
    
    /**
     * getCaracteristicasNuevasXServicio, obtiene un array con los distintas caracteristicas de un servicio especificado
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 10-08-2016
     * 
     * @param Object objArrayData Objeto json que contiene caracteristicas por servicio
     * @param int intIdServicio Identificador del Servicio
     * @return array Retorna un array con las caracteristicas nuevas asignadas a un servicio
     * 
     */
    private function getCaracteristicasNuevasXServicio($objArrayData, $intIdServicio)
    {
        $arrayCaracteristicas = array();
        if($objArrayData && $intIdServicio)
        {
            foreach($objArrayData as $objCaractServ):
                if($objCaractServ->intIdDetalle == $intIdServicio && $objCaractServ->data)
                {
                    foreach ($objCaractServ->data as $clave => $valor)
                    {
                        $arrayCaracteristicas[$clave] = $valor;
                    }
                    break;
                }
            endforeach;
        }
        return $arrayCaracteristicas;
    }
}
