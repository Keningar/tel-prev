<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace telconet\planificacionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoDetalleSolCaract;
use telconet\schemaBundle\Entity\InfoDetalle;
use telconet\schemaBundle\Entity\InfoDetalleHistorial;
use telconet\schemaBundle\Entity\InfoIp;
use telconet\schemaBundle\Entity\InfoParteAfectada;
use telconet\schemaBundle\Entity\InfoCriterioAfectado;
use telconet\schemaBundle\Entity\InfoDetalleInterface;
use telconet\schemaBundle\Entity\InfoDetalleAsignacion;
use telconet\schemaBundle\Entity\InfoDetalleColaborador;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoElementoTrazabilidad;
use telconet\schemaBundle\Entity\InfoCuadrillaTarea;
use telconet\schemaBundle\Entity\InfoServicioProdCaract;
use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\InfoDocumentoComunicacion;
use telconet\schemaBundle\Entity\InfoComunicacion;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\JsonResponse;

class AsignarResponsableController extends Controller implements TokenAuthenticatedController
{
    
    /**
     * @Secure(roles="ROLE_139-1")
     */
    public function indexAction()
    {
        $rolesPermitidos = array();
        if(true === $this->get('security.context')->isGranted('ROLE_135-94'))
        {
            $rolesPermitidos[] = 'ROLE_135-94';
        }
        if(true === $this->get('security.context')->isGranted('ROLE_135-95'))
        {
            $rolesPermitidos[] = 'ROLE_135-95';
        }
        if(true === $this->get('security.context')->isGranted('ROLE_139-7'))
        {
            $rolesPermitidos[] = 'ROLE_139-7';
        }
        $em = $this->getDoctrine()->getManager('telconet_general');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("139", "1");

        return $this->render('planificacionBundle:AsignarResponsable:index.html.twig', array(
                'item' => $entityItemMenu,
                'rolesPermitidos' => $rolesPermitidos
        ));
    }

    /*
     * Llena el grid de consulta.
     */

    /**
     * @Secure(roles="ROLE_139-7")
     * 
     * @since 1.0
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 - 15-12-2017 Se parametriza variables que se envian a consultar datos para asignar responsable
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.2 - 17-04-2017 Arrays de productos especiales y de excepcion son enviados como array eliminando doble iteracion
     */
    public function gridAction()
    {
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');

        $objRequest            = $this->get('request');
        $objSession            = $objRequest->getSession();
        $strPrefijoEmpresa     = $objSession->get('prefijoEmpresa');
        $arrayFechaDesdePlanif = explode('T', $objRequest->query->get('fechaDesdePlanif'));
        $arrayFechaHastaPlanif = explode('T', $objRequest->query->get('fechaHastaPlanif'));
        $strLogin              = ($objRequest->query->get('login2') ? $objRequest->query->get('login2') : "");
        $strDescripcionPunto   = ($objRequest->query->get('descripcionPunto') ? $objRequest->query->get('descripcionPunto') : "");
        $strUsrVendedor        = ($objRequest->query->get('vendedor') ? $objRequest->query->get('vendedor') : "");
        $strCiudad             = ($objRequest->query->get('ciudad') ? $objRequest->query->get('ciudad') : "");
        $intIdTipoSolicitud    = $objRequest->query->get('tipoSolicitud');

        $emGeneral             = $this->getDoctrine()->getManager("telconet_general");
        $emComercial           = $this->getDoctrine()->getManager("telconet");
        $intIdDepartamento     = $objSession->get('idDepartamento');
        
        $objDepartamento       = $emGeneral->getRepository("schemaBundle:AdmiDepartamento")->find($intIdDepartamento);
        
        $arrayParametros                      = array();
        $arrayParametros['strCiudad']         = $strCiudad;
        $arrayParametros['emInfraestructura'] = $this->getDoctrine()->getManager("telconet_infraestructura");
        $arrayParametros['intStart']          = $objRequest->query->get('start');
        $arrayParametros['intLimit']          = $objRequest->query->get('limit');
        $arrayParametros['strFechaDesde']     = $arrayFechaDesdePlanif[0];
        $arrayParametros['strFechaHasta']     = $arrayFechaHastaPlanif[0];
        $arrayParametros['strLogin']          = $strLogin;
        $arrayParametros['intSectorId']       = '';
        $arrayParametros['strDescripcionPunto'] = $strDescripcionPunto;
        $arrayParametros['strUsrVendedor']      = $strUsrVendedor;
        $arrayParametros['intIdTipoSolicitud']  = $intIdTipoSolicitud;
        $arrayParametros['intCodEmpresa']       = $objSession->get('idEmpresa');
        
        $arrayProductos          = array();
        $arrayProductosExcepcion = array();
        $strRegion               = '';
        
        if(is_object($objDepartamento) && $strPrefijoEmpresa == 'TN')
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
                if(empty($arrayParametros['strCiudad']))
                {
                    $intIdOficina          = $objSession->get('idOficina');

                    $objOficina = $emComercial->getRepository("schemaBundle:InfoOficinaGrupo")->find($intIdOficina);

                    if(is_object($objOficina))
                    {
                        $objCanton = $emComercial->getRepository("schemaBundle:AdmiCanton")->find($objOficina->getCantonId());

                        if(is_object($objCanton))
                        {
                            $strRegion = $objCanton->getProvinciaId()->getRegionId()->getNombreRegion();
                        }
                    }
                }
        
                foreach($arrayInfoVisualizacion as $array)
                {
                    $arrayProductos[] = $array['valor2'];
                }
            }
            else//Filtra los productos que no seben ser mostrados en flujos normales con donde solo interviene PyL
            {
                $arrayInfoNoVisualizacion   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->get('EXCEPCION DE PRODUCTOS EN FLUJOS NORMALES', 
                                                              'COMERCIAL', 
                                                              '',
                                                              'FACTIBILIDAD',
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
                        $arrayProductosExcepcion[] = $array['valor1'];
                    }
                }
            }
        }
        
        $arrayParametros['arrayDescripcionProducto']          = $arrayProductos;
        $arrayParametros['arrayDescripcionProductoExcepcion'] = $arrayProductosExcepcion;
        $arrayParametros['strPrefijoEmpresa']                 = $strPrefijoEmpresa;
        $arrayParametros['strRegion']                         = $strRegion;

        $objJson = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->generarJsonAsignarResponsable($arrayParametros);
        
        $objRespuesta->setContent($objJson);

        return $objRespuesta;
    }

    /* combo EMPLEADOS llenado ajax */

    /**
     * Función usada para la asignación de una tarea a un empleado desde la pantalla de PYL
     * @return Response
     * 
     * se elimina configuración de rol debido que este metodo es reutilizado por otros modulos
     * @since 1.0
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 08-04-2022 Se modifica función para obtener personas por asignar cuando se valide por perfil en sesión
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.2 24-02-2023 Se agrega Bandera con el prefijo de la empresa ECUANET para que traiga los empleados de TN
     */
    public function getEmpleadosAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        $nombre = $peticion->query->get('query');
        $strAplicaFiltroEmpleadosXPerfil = $peticion->query->get('aplicaFiltroEmpleadosXPerfil');
        $strNombrePerfilEmpleadosXAsignar = $peticion->query->get('nombrePerfilEmpleadosXAsignar');

        $codEmpresa = ($peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa') : "");
        $strPrefijoEmpresa = ($peticion->getSession()->get('prefijoEmpresa') ? $peticion->getSession()->get('prefijoEmpresa') : "");
        
        if(isset($strAplicaFiltroEmpleadosXPerfil) && !empty($strAplicaFiltroEmpleadosXPerfil) && $strAplicaFiltroEmpleadosXPerfil === "SI")
        {
            $arrayRespuestaPersonasXPerfil  = $this->getDoctrine()->getManager("telconet")->getRepository('schemaBundle:InfoPersona')
                                                   ->getResultadoPersonasPorParametros( 
                                                        array(  "nombrePerfilAsignado"  => $strNombrePerfilEmpleadosXAsignar,
                                                                "descripcionTipoRol"    => "Empleado",
                                                                "codEmpresa"            => $codEmpresa,
                                                                "nombrePersona"         => $nombre));
            $objJson = json_encode($arrayRespuestaPersonasXPerfil);
        }
        else
        {
            if($codEmpresa == 18 || $strPrefijoEmpresa == 'EN')
            {
                $codEmpresa = 10;
            }
            $objData = $this->getDoctrine()
                ->getManager("telconet")
                ->getRepository('schemaBundle:InfoPersona')
                ->findPersonasXTipoRol("Empleado", $nombre, $codEmpresa, "Tecnico");

            $arreglo = array();
            $num = count($objData);
            if($objData && count($objData) > 0)
            {
                foreach($objData as $key => $entityPersona)
                {
                    $arreglo[] = array('id_empleado' => $entityPersona->getId(), 'nombre_empleado' => sprintf("%s", $entityPersona));
                }

                $dataF = json_encode($arreglo);
                $objJson = '{"total":"' . $num . '","encontrados":' . $dataF . '}';
            }
            else
            {
                $objJson = '{"total":"0","encontrados":[]}';
            }
        }
        
        $respuesta->setContent($objJson);
        return $respuesta;
    }

    /* combo EMPRESAS EXTERNAS llenado ajax */

    /**
     * @Secure(roles="ROLE_139-110")
     */
    public function getEmpresasExternasAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $peticion = $this->get('request');
        $session = $peticion->getSession();
        $idEmpresa = $session->get('idEmpresa');

        $nombre = $peticion->query->get('query');

        $objData = $this->getDoctrine()
            ->getManager("telconet")
            ->getRepository('schemaBundle:InfoPersona')
            ->findPersonasXTipoRol("Proveedor", $nombre, $idEmpresa, "");

        $arreglo = array();
        $num = count($objData);
        if($objData && count($objData) > 0)
        {
            foreach($objData as $key => $entityPersona)
            {
                $arreglo[] = array('id_empresa_externa' => $entityPersona->getId(), 'nombre_empresa_externa' => sprintf("%s", $entityPersona));
            }

            $dataF = json_encode($arreglo);
            $objJson = '{"total":"' . $num . '","encontrados":' . $dataF . '}';
        }
        else
        {
            $objJson = '{"total":"0","encontrados":[]}';
        }

        $respuesta->setContent($objJson);
        return $respuesta;
    }

    /**
     * getCuadrillasAction
     * Esta funcion retorna el listado de cuadrillas
     *
     * @version 1.0 Version Incial
     *
     * @author modificado Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 09-04-2018 Se agrega condicion para cuadrillas asignadas a Hal
     *
     * @author modificado Antonio Ayala <afayala@telconet.ec>
     * @version 1.2 29-06-2020 Se agrega condicion para cuadrillas por departamento
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.3 13-12-2021 | Se realizan ajustes para mejorar el mensaje de respuesta en formato JSON.
     *
     * @Secure(roles="ROLE_139-109")
     */
    public function getCuadrillasAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $peticion = $this->get('request');
        $nombre = $peticion->query->get('query');
        /*Se genera variable con el valor de strEsPuntoGpon.*/
        $strEsPuntoGpon = $peticion->query->get('strEsPuntoGpon');
        $arrayJsonResponse = array('total' => '', 'encontrados' => '', 'msg' => '');

        /*Se valida que el parametro no este vacio y contenga el valor S.*/
        if (!empty($strEsPuntoGpon) && $strEsPuntoGpon == 'S')
        {
            /*Se obtiene el servicio tecnico.*/
            $serviceGeneral = $this->get('tecnico.InfoServicioTecnico');
            /*Se obtiene el login desde la sesion.*/
            $arrayParams['login'] = $peticion->getSession()->get('ptoCliente')['login'];
            /*Se ejecuta servicio para obtener cuadrillas asociadas al login con tareas.*/
            $arrayCuadrillasAsignables = $serviceGeneral->getCuadrillasAsignables($arrayParams);
            /*Se define variable para los nombres de las cuadrillas.*/
            $arrayCuadrillasNombres = array();

            /*Se valida que el mensaje de respuesta del servicio sea OK.*/
            if ( $arrayCuadrillasAsignables['status'] == 'OK' && !empty($arrayCuadrillasAsignables['data'])
                 && is_array($arrayCuadrillasAsignables['data']) && count($arrayCuadrillasAsignables['data']) >=1 )
            {
                /*Recorremos el arreglo para generar la respuesta con los nombres de las cuadrillas.*/
                foreach ($arrayCuadrillasAsignables['data'] as $arrayCuadrillaAsignable)
                {
                    $arrayCuadrillasNombres[] = $arrayCuadrillaAsignable['nombreAsignado'];
                }
                /*Asignamos el valor final.*/
                $arrayParametros['arrayCuadrillasNombres'] = $arrayCuadrillasNombres;
            }
        }

        $intIdDepartamento = $peticion->query->get('idDepartamento');

        $arrayParametros["strNombreCuadrilla"]  = $nombre;
        $arrayParametros["strHal"]              = "S";
        $arrayParametros["intIdDepartamento"]   = $intIdDepartamento;

        $objData = $this->getDoctrine()
            ->getManager("telconet")
            ->getRepository('schemaBundle:AdmiCuadrilla')
            ->findCuadrillas($arrayParametros);

        $arreglo = array();
        $num = count($objData);
        if($objData && count($objData) > 0)
        {
            foreach($objData as $key => $entityPersona)
            {
                $arreglo[] = array('id_cuadrilla' => $entityPersona->getId(), 'nombre_cuadrilla' => $entityPersona->getNombreCuadrilla());
            }

            $arrayJsonResponse['total'] = $num;
            $arrayJsonResponse['encontrados'] = $arreglo;

            /*Validamos que se encuentre seteada la variable $arrayCuadrillasAsignables.*/
            if (isset($arrayCuadrillasAsignables) &&
                (count($arrayCuadrillasAsignables['data']) <= 0 || is_string($arrayCuadrillasAsignables['data'])))
            {
                /*Agregamos un mensaje en caso de que no exista la tarea asociada al cliente.*/
                $arrayJsonResponse['msg'] = "No existe tarea de soporte asociada al cliente";
            }

            $objJson = json_encode($arrayJsonResponse, true);

        }
        else
        {
            $objJson = '{"total":"0","encontrados":[]}';
        }

        $respuesta->setContent($objJson);
        return $respuesta;
    }

    public function ajaxGetJsonInterfacesByElementoAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $peticion = $this->get('request');
        $idElemento = $peticion->get('idElemento');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:InfoInterfaceElemento')
            ->generarJsonInterfaces($idElemento, "not connect", "", "");

        $respuesta->setContent($objJson);

        return $respuesta;
    }

    /**
     * @Secure(roles="ROLE_139-111")
     */
    public function asignarAjaxAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');

        $em = $this->getDoctrine()->getManager("telconet");
        $em_soporte = $this->getDoctrine()->getManager("telconet_soporte");

        $peticion = $this->get('request');
        $origen = $peticion->get('origen');
        $idFactibilidad = $peticion->get('id');
        $parametro = $peticion->get('param');
        $boolGuardoGlobal = false;

        if($origen == "local")
        {
            $boolGuardoGlobal = true;

            $array_valor = explode("|", $parametro);
            foreach($array_valor as $id):
                $retornaGuarda = $this->guardaUno($id);
                if($retornaGuarda != "OK")
                {
                    $boolGuardoGlobal = false;
                    $respuesta->setContent($retornaGuarda);
                }
            endforeach;
        }
        else if($origen == "otro" || $origen == "otro2")
        {
            $boolGuardoGlobal = true;

            $retornaGuarda = $this->guardaUno($idFactibilidad);
            if($retornaGuarda != "OK")
            {
                $boolGuardoGlobal = false;
                $respuesta->setContent($retornaGuarda);
            }
        }
        else
        {
            $boolGuardoGlobal = false;
            $respuesta->setContent("No existe esa opcion");
        }

        if($boolGuardoGlobal)
        {
            $respuesta->setContent("Se asigno la Tarea Correctamente.");
        }
        return $respuesta;
    }

    //************** FUNCTION QUE HACE EL PROCESO DE GUARDAR UN ID DETALLE SOLICITUD 
    function guardaUno($id)
    {
        $mensajeError = "";

        $peticion = $this->get('request');
        $session = $peticion->getSession();
        $idEmpresa = $session->get('idEmpresa');
        $prefijoEmpresa = $session->get('prefijoEmpresa');
        $em = $this->getDoctrine()->getManager("telconet");
        $em_soporte = $this->getDoctrine()->getManager("telconet_soporte");
        $em_comunicacion = $this->getDoctrine()->getManager("telconet_comunicacion");
        $em_infraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');

        $band = $peticion->get('banderaEscogido');
        $codigo = $peticion->get('codigoEscogido');
        $codigoAsignado = $this->retornaEscogidoResponsable($band, $codigo, "codigo");
        $nombreAsignado = $this->retornaEscogidoResponsable($band, $codigo, "nombre");
        $ref_codigoAsignado = $this->retornaEscogidoResponsable($band, $codigo, "ref_codigo");
        $ref_nombreAsignado = $this->retornaEscogidoResponsable($band, $codigo, "ref_nombre");

        $entity = $em->find('schemaBundle:InfoDetalleSolicitud', $id);
        $DataSolicit = $this->getDoctrine()
            ->getManager("telconet")
            ->getRepository('schemaBundle:InfoDetalleSolicitud')
            ->getRegistros(0, 1, "", "", "TODOS", "", "", $id);

        if($DataSolicit == null || $entity == null)
        {
            $mensajeError = "No existe la entidad";
        }
        else
        {
            $InfoServicio = $em->getRepository('schemaBundle:InfoServicio')->findOneById($entity->getServicioId());
            $nombrePlan = $InfoServicio->getPlanId()->getNombrePlan();
            $infoServicioTecnico = $em->getRepository('schemaBundle:InfoServicioTecnico')->findOneByServicioId($InfoServicio->getId());
            $TipoMedio = $em_infraestructura->getRepository('schemaBundle:AdmiTipoMedio')->find($infoServicioTecnico->getUltimaMillaId());

            if($TipoMedio->getNombreTipoMedio() == "Cobre")
            {
                if(strrpos($nombrePlan, "ADSL"))
                    $nombreProceso = "SOLICITAR NUEVO SERVICIO COBRE ADSL";
                else
                    $nombreProceso = "SOLICITAR NUEVO SERVICIO COBRE VDSL";
            }
            else
                $nombreProceso = "SOLICITAR NUEVO SERVICIO RADIO";

            $entityProceso = $em_soporte->getRepository('schemaBundle:AdmiProceso')->findOneByNombreProceso($nombreProceso);
            if($entityProceso != null)
            {
                $entityTareas = $em_soporte->getRepository('schemaBundle:AdmiTarea')->findTareasActivasByProceso($entityProceso->getId());
                if($entityTareas != null && count($entityTareas) > 0)
                {
                    $boolGuardo = true;

                    foreach($entityTareas as $key => $entityTarea)
                    {
                        $EDetalle = $em_soporte->getRepository('schemaBundle:InfoDetalle')->getOneDetalleByDetalleSolicitudTarea($id, $entityTarea->getId());
                        if($EDetalle != null)
                        {
                            $entityDetalle = $EDetalle;
                        }
                        else
                        {
                            //********* SAVE -- DETALLE (TAREA) **************
                            $entityDetalle = new InfoDetalle();
                            $entityDetalle->setDetalleSolicitudId($id);
                            $entityDetalle->setTareaId($entityTarea);
                            $entityDetalle->setLongitud($DataSolicit[0]["longitud"]);
                            $entityDetalle->setLatitud($DataSolicit[0]["latitud"]);
                            $entityDetalle->setPesoPresupuestado(0);
                            $entityDetalle->setValorPresupuestado(0);
                            $entityDetalle->setIpCreacion($peticion->getClientIp());
                            $entityDetalle->setFeCreacion(new \DateTime('now'));
                            $entityDetalle->setUsrCreacion($peticion->getSession()->get('user'));

                            $em_soporte->persist($entityDetalle);
                            $em_soporte->flush();
                        }

                        //********* SAVE -- DETALLE ASIGNACION **************
                        $entityDetalleAsignacion = new InfoDetalleAsignacion();
                        $entityDetalleAsignacion->setDetalleId($entityDetalle);
                        $entityDetalleAsignacion->setAsignadoId($codigoAsignado);
                        $entityDetalleAsignacion->setAsignadoNombre($nombreAsignado);
                        $entityDetalleAsignacion->setRefAsignadoId($ref_codigoAsignado);
                        $entityDetalleAsignacion->setRefAsignadoNombre($ref_nombreAsignado);
                        $entityDetalleAsignacion->setIpCreacion($peticion->getClientIp());
                        $entityDetalleAsignacion->setFeCreacion(new \DateTime('now'));
                        $entityDetalleAsignacion->setUsrCreacion($peticion->getSession()->get('user'));

                        $em_soporte->persist($entityDetalleAsignacion);
                        $em_soporte->flush();

                        if($entityDetalleAsignacion != null && $band == "cuadrilla")
                        {
                            $objData = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findByCuadrillaId($codigoAsignado);
                            if($objData && count($objData) > 0)
                            {
                                foreach($objData as $keyPersona => $entityPersona)
                                {
                                    $col_ref_codigoAsignado = $entityPersona->getPersonaId()->getId();
                                    $col_ref_nombreAsignado = $entityPersona->getPersonaId()->getNombres() . " " . $entityPersona->getPersonaId()->getApellidos();

                                    $col_codigoAsignado = $this->retornaEscogidoResponsable("empleado", $col_ref_codigoAsignado, "codigo");
                                    $col_nombreAsignado = $this->retornaEscogidoResponsable("empleado", $col_ref_codigoAsignado, "nombre");


                                    //********* SAVE -- DETALLE COLABORADOR**************
                                    $entityDetalleColaborador = new InfoDetalleColaborador();
                                    $entityDetalleColaborador->setDetalleAsignacionId($entityDetalleAsignacion);
                                    $entityDetalleColaborador->setAsignadoId($col_codigoAsignado);
                                    $entityDetalleColaborador->setAsignadoNombre($col_nombreAsignado);
                                    $entityDetalleColaborador->setRefAsignadoId($col_ref_codigoAsignado);
                                    $entityDetalleColaborador->setRefAsignadoNombre($col_ref_nombreAsignado);
                                    $entityDetalleColaborador->setIpCreacion($peticion->getClientIp());
                                    $entityDetalleColaborador->setFeCreacion(new \DateTime('now'));
                                    $entityDetalleColaborador->setUsrCreacion($peticion->getSession()->get('user'));

                                    $em_soporte->persist($entityDetalleColaborador);
                                    $em_soporte->flush();
                                }
                            }
                        }

                        if($entityDetalleAsignacion == null)
                            $boolGuardo = false;
                        else
                        {
                            //------- COMUNICACIONES --- NOTIFICACIONES                             
                            if($EDetalle != null)
                            {
                                $EDetalleAsignacion = $em_soporte->getRepository('schemaBundle:InfoDetalleAsignacion')->getUltimaAsignacion($EDetalle->getId());

                                $mensaje = $this->renderView('planificacionBundle:AsignarResponsable:mensajeReasignacion.html.twig', array('detalle' => $entityDetalle,
                                    'tarea' => $entityTarea,
                                    'detalleAsignacion' => $entityDetalleAsignacion,
                                    'detalleAsignacionAnterior' => $EDetalleAsignacion));

                                $asunto = "Reasignacion de Tarea " . $entityTarea->getNombreTarea();
                            }
                            else
                            {
                                $mensaje = $this->renderView('planificacionBundle:AsignarResponsable:mensajeAsignacion.html.twig', array('detalle' => $entityDetalle,
                                    'tarea' => $entityTarea,
                                    'detalleAsignacion' => $entityDetalleAsignacion));

                                $asunto = "Asignacion de Tarea " . $entityTarea->getNombreTarea();
                            }

                            //DESTINATARIOS.... 
                            $formasContacto = $em->getRepository('schemaBundle:InfoPersona')->getContactosByLoginPersonaAndFormaContacto($InfoServicio->getPuntoId()->getUsrVendedor(), 'Correo Electronico');

                            $to = array();
                            $to[] = 'notificaciones_telcos@telconet.ec';

                            if($formasContacto)
                            {
                                foreach($formasContacto as $formaContacto)
                                {
                                    $to[] = $formaContacto['valor'];
                                }
                            }

                            /* @var $envioPlantilla EnvioPlantilla */
                            $envioPlantilla = $this->get('soporte.EnvioPlantilla');
                            $envioPlantilla->enviarCorreo($asunto, $to, $mensaje);
                        }
                    }

                    if($boolGuardo)
                    {
                        $boolGuardoGlobal = true;

                        $entity->setEstado("Asignada");
                        $em->persist($entity);
                        $em->flush();

                        //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                        $lastDetalleSolhist = $em->getRepository('schemaBundle:InfoDetalleSolHist')->findOneDetalleSolicitudHistorial($id, 'Planificada');

                        $entityDetalleSolHist = new InfoDetalleSolHist();
                        $entityDetalleSolHist->setDetalleSolicitudId($entity);
                        if($lastDetalleSolhist)
                        {
                            $entityDetalleSolHist->setFeIniPlan($lastDetalleSolhist->getFeIniPlan());
                            $entityDetalleSolHist->setFeFinPlan($lastDetalleSolhist->getFeFinPlan());
                            $entityDetalleSolHist->setObservacion($lastDetalleSolhist->getObservacion());
                        }
                        $entityDetalleSolHist->setIpCreacion($peticion->getClientIp());
                        $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
                        $entityDetalleSolHist->setUsrCreacion($peticion->getSession()->get('user'));
                        $entityDetalleSolHist->setEstado('Asignada');

                        $em->persist($entityDetalleSolHist);
                        $em->flush();
                    }
                    else
                    {
                        $mensajeError = "No guardo";
                    }
                }
                else
                {
                    $mensajeError = "No existe tareas predefinidas";
                    // $respuesta->setContent("No existe tareas predefinidas");
                }
            }
            else
            {
                $mensajeError = "No existe el proceso predefinido";
                //$respuesta->setContent("No existe el proceso predefinido");
            }
        }

        if($boolGuardo)
        {
            return "OK";
        }
        else
        {
            return $mensajeError;
        }
    }
    
   /**
    * getEmpleadosPorEmpresaAction
    * Funcion que retorna los empleados por Empresa
    *
    * @return json $objResponse
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 21-05-2019
    */
    public function getEmpleadosPorEmpresaAction()
    {
        $objPeticion        = $this->get('request');
        $objSession         = $objPeticion->getSession();
        $strCodEmpresa      = $objSession->get('idEmpresa');
        $strNombres         = $objPeticion->query->get('query') ? $objPeticion->query->get('query') : "";
        $emComercial        = $this->getDoctrine()->getManager("telconet");
        $arrayEncontrados   = array();
        $arrayRespuesta     = array();
        $intNumeroEmpleados = 0;
        $objResponse        = new JsonResponse();

        //Se obtiene los tipos de elementos
        $arrayParametros["strEstado"]     = "Activo";
        $arrayParametros["strTipoRol"]    = "Empleado";
        $arrayParametros["strNombres"]    = $strNombres;
        $arrayParametros["strCodEmpresa"] = $strCodEmpresa;

        $arrayEmpleados = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->getEmpleadosPorEmpresa($arrayParametros);

        $intNumeroEmpleados = count($arrayEmpleados);

        if($intNumeroEmpleados > 0)
        {
            foreach($arrayEmpleados as $arrayIdxEmpleados)
            {
                $arrayEncontrados[] = array('intIdPersonEmpresaRol' => $arrayIdxEmpleados["idPersonaEmpresaRol"],
                                            'strNombresEmpleado'    => $arrayIdxEmpleados["nombresEmpleado"]);
            }
        }

        $arrayRespuesta["total"]       = $intNumeroEmpleados;
        $arrayRespuesta["encontrados"] = $arrayEncontrados;

        $objResponse->setData($arrayRespuesta);

        return $objResponse;
    }


     /**
     *
     * Documentación de la funcion 'getLiderCuadrilla'.
     * 
     * Método que retorna el lider de una cuadrilla   
     * 
     * @return Response retorna el resultado de la operación
     * 
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 23-10-2015
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 21-04-2017 Se realizan ajustes para determinar si una cuadrilla tiene asociada una tablet
     */
    public function getLiderCuadrillaAction()
    {
        $emComercial        = $this->getDoctrine()->getManager('telconet');
        $objPeticion        = $this->getRequest();
        $arrayParametros    = array();
        $objAdmiCuadrilla   = null;
        $strNombreCuadrilla = "";
        $strExisteTablet    = "";
        $objRespuesta       = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/plain');

        $intCuadrillaId = $objPeticion->get("cuadrillaId");
        
        //Se valida si la cuadrilla tiene asociada una tablet
        $arrayParametros["intCuadrillaId"]  = $intCuadrillaId;
        $arrayParametros["strTipoElemento"] = "TABLET";
        $arrayParametros["strEstado"]       = "Activo";

        $strExisteTablet = $emComercial->getRepository('schemaBundle:AdmiCuadrilla')->existeTabletPorCuadrilla($arrayParametros);

        $arrayDatos      = $emComercial->getRepository('schemaBundle:AdmiCuadrilla')->findJefeCuadrilla($intCuadrillaId); 

        //Se consulta el nombre de la cuadrilla
        $objAdmiCuadrilla = $emComercial->getRepository('schemaBundle:AdmiCuadrilla')->find($intCuadrillaId);

        if(is_object($objAdmiCuadrilla))
        {
            $strNombreCuadrilla = $objAdmiCuadrilla->getNombreCuadrilla();
        }
        return $objRespuesta->setContent(json_encode(array('nombreCuadrilla'     => $strNombreCuadrilla,
                                                           'existeTablet'        => $strExisteTablet,
                                                           'idPersona'           => $arrayDatos['idPersona'], 
                                                           'nombres'             => $arrayDatos['nombres'],
                                                           'idPersonaEmpresaRol' => $arrayDatos['idPersonaEmpresaRol'])));                                    
    }
    

    /**
     * asignarIndividualmenteAjaxAction
     * 
     * Esta funcion realiza la asignacion de un Empleado,Cuadrilla o Personal Externo. Desde la opcion de asignar
     * responsable en el modulo de Planificacion
     * 
     * @author modificado Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 23-10-2015 se realizan validaciones al proceso para incluir concepto de asignacion a cuadrillas y lider     
     * 
     * @author modificado Jesus Bozada <jbozada@telconet.ec>
     * @version 1.2 30-12-2015 Se agrega validacion de estado del servicio al momento de ejecutar la accion
     * 
     * @author modificado Jesus Bozada <jbozada@telconet.ec>
     * @version 1.3 19-01-2016 Se modifica Observacion de historial de servicios para Productos con nombreTecnico NETLIFECAM,
     *                         ahora se usara este nombre tecnico para productos Smart WiFi y NetlifeCam
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.4 17-05-2016 - Se guarda toda la información de la replanificación en la observación del historial del servicio
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.5 17-08-2016 - Se genera el numero de la tarea y solo para la empresa TN se copia la notificacion de instalacion a
     *                           todo el departamento del responsable asignado. Adicional este cambio va generar que la tarea automatica
     *                           se pueda visualizar en la consulta de actividades
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.6 05-09-2016 - Se coloca por defecto el proceso para fibra cuando el servicio no tenga última milla
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.7 17-10-2016 - Se registra el INFO_PERSONA_EMPRESA_ROL, de la persona a la cual se le asigna la tarea de instalacion,
     *                           dado que actualmente no se esta realizando y este dato es importante para calcular las tareas pendientes
     *                           del usuario en session.
     *
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.8 10-11-2016 - Se guarda como parte de la observación al técnico asignado
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.9 12-11-2016 - Se crea un seguimiento con la información del Ingeniero IPCCL2 asignado al asignar responsable cuando el prefijo
     *                           de la empresa se TN
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.0 15-11-2016 - Se valida que la información del Ingeniero IPCCL2 sea solo para las solicitudes de planificación 
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.1 22-11-2016 - Se agrega la persona_empresa_rol_id cuando la tarea se asigna a una cuadrilla que no tiene líder y que por ende se
     *                           se la asigna al primer integrante de la cuadrilla
     * 
     * @author Modificado: Jesus Bozada <jbozada@telconet.ec>
     * @version 2.2 06-12-2016 - Se agrega codigo para cambiar estado a caracteristica de solictiudes de retiro de equipo asignadas
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.3 09-02-2017 - Se valida que la solicitud quede en estado Asignada si el nombre técnico del producto es OTROS y si éste no requiere
     *                           información técnica o si el nombre técnico del producto es NETLIFECAM
     * 
     * @author Modificado: Jesus Bozada <jbozada@telconet.ec>
     * @version 2.4 14-02-2017 - Se valida que la solicitud quede en estado Asignada si el nombre técnico del producto es OTROS y si éste no requiere
     *                           información técnica o si la empresa es MD y no es enlace y no requiere info tecnica
     * 
     * @author Modificado: John Vera <javera@telconet.ec>
     * @version 2.5 17-02-2017 - Se valida cuando es un producto si es venta externa (NETVOICE) para que pase directamente a estado Asignada
     * 
     * @since 2.3
     * 
     * @author Modificado: Jesus Bozada <jbozada@telconet.ec>
     * @version 2.6 23-02-2017 - Se agrega nuevo tipo solicitud SOLICITUD AGREGAR EQUIPO para  gestionar solicitudes generadas
     *                           en el proceso de cambio de planes donde el nuevo plan incluya como detalle un producto SMART WIFI
     * @since 2.5
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.7 07-04-2017 - Se agrega la validación en caso de que el prefijo de la empresa sea 'MD' y que el servicio tenga asociado un
     *                           un plan y que dicho plan posea la característica FLUJO_PREPLANIFICACION_PLANIFICACION = 'SI' asociada al plan 
     *                           Netlifecam. De esta manera la solicitud de planificación pasa automáticamente de estado 'AsignadoTarea' a 
     *                           'Asignada' cuando el servicio está asociado a un plan.
     *                           $boolFlujoAsignadoAutomatico es seteada a true para indicar que se debe pasar la solicitud automáticamente 
     *                           al estado 'Asignada'
     * 
     * @author Modificado: Allan Suarez <arsuarez@telconet.ec>
     * @version 2.8 14-07-2017 - Validacion de estado de Servicio "Activo" no aplica para solicitudes de retiro de equipos existentes
     *  
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.9 14-07-2017 - Se agrega validación cuando el plan es netlifecam para que la tarea sea la especificada para dicha activación
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 3.0 09-08-2017 -  En la tabla INFO_TAREA_SEGUIMIENTO y INFO_DETALLE_HISTORIAL, se regulariza el departamento creador de la accion y 
     *                            se adicionan los campos de persona empresa rol id para identificar el responsable actual
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 3.1 14-09-2017 - Se realizan ajustes para definir que el estado inicial de una tarea sea 'Asignada'
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 3.2 23-11-2017 - Se agrega validaciones para la última milla FFTx
     *
     * @author Modificado: Jesús Bozada <jbozada@telconet.ec>
     * @version 3.3 22-01-2018 - Se agrega programación para asignación de responsable de solicitudes de reubicación TN
     * @since 3.2
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 3.4 16-03-2018 - Se registra trazabilidad del elemento para las solicitudes de retiro de equipo
     *
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 3.5 30-05-2018 - Se realiza ajustes para cambiar el orden en el nombre de los responsables de la trazabilidad, Apellidos - Nombres
     *
     * @author Modificado: Allan Suarez <arsuarez@telconet.ec>
     * @version 3.6 31-05-2018 - Se justa para que la solucion se muestre el detalle en forma de Multi Solucion ( NxN )
     * @since 3.5
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 3.7 12-11-2018 - Se realizan ajustes en el registro de la tarea con el fin de mostrar los números asignados por la empresa y no los
     *                           personales.
     * @since 3.6
     *
     * @author John Vera <javera@telconet.ec>
     * @version 3.8 19-12-2018 se llama a nueva función para el aprovisionamiento de las líneas de netvoice
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 3.9 05-05-2020 - Se agrega el tipo de solicitud: 'SOLICITUD AGREGAR EQUIPO MASIVO'
     *
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 4.0 17-07-2020 - Actualización: Se agrega bloque de código para actualizar datos de tareas en nueva tabla DB_SOPORTE.INFO_TAREA
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 4.1 13-11-2020 Se agregan las validaciones y el flujo necesario para asignar la solicitud de los servicios W+AP en caso que ocurriera
     *                         algún error en la asignación de tareas desde la planificación. Además se corrige la invocación de la función 
     *                         crearInfoTarea
     * 
     * @author Ronny Morán <rmoranc@telconet.ec>
     * @version 4.2 12-04-2021 - Actualización: Se establece valor "No tiene" en caso de no tener Ingeniero IPCCL2 Asignado.
     * 
     * @author Hector Sanchez <hsanchez@telconet.ec>
     * @version 4.3 27-04-2023 - se agrega Validacion por prefijo para que la empresa Ecuanet siga el flujo.
     * 
     * @version inicial 1.0
     *
     * @return string $respuesta  Mensaje de exito o error
     *     
     * @Secure(roles="ROLE_139-112")
     * 
     */
    public function asignarIndividualmenteAjaxAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');

        $emComercial         = $this->getDoctrine()->getManager("telconet");
        $em_soporte          = $this->getDoctrine()->getManager("telconet_soporte");
        $emComunicacion      = $this->getDoctrine()->getManager("telconet_comunicacion");
        $em_infraestructura  = $this->getDoctrine()->getManager("telconet_infraestructura");
        $em_general          = $this->getDoctrine()->getManager("telconet_general");
        $serviceSoporte      = $this->get('soporte.SoporteService');
        $serviceGeneral      = $this->get('tecnico.InfoServicioTecnico');
        $servicePlanificar   = $this->get('planificacion.Planificar');
        $peticion            = $this->get('request');
        $origen              = $peticion->get('origen');
        $idFactibilidad      = $peticion->get('id');
        $parametro           = $peticion->get('param');
        $paramResponsables   = $peticion->get('paramResponsables');
        $intIdPerTecnico     = $peticion->get('idPerTecnico');
        $idPersona           = "";
        $idPersonaEmpresaRol = "";
        $observacion         = "";
        $observacionServicio = "";
        $arrayParametros      = array();
        $arrayParametrosHist  = array();
        $intPersonaEmpresaRol = 0;
        $boolGuardoGlobal   = false;
        $session            = $peticion->getSession();
        $idEmpresa          = $session->get('idEmpresa');
        $prefijoEmpresa     = $session->get('prefijoEmpresa');
        $intIdDepartamento  = $session->get('idDepartamento');
        $codEmpresa         = $idEmpresa;
        $observacionData    = "";
        $strResponsableTrazabilidad = "";
        $strLoginTrazabilidad       = "";
        $arrayInfoDetalle           = array();
        $objSoporteService = $this->get('soporte.SoporteService');

        if($idEmpresa == 18 || $idEmpresa == 33)
        {
            $idEmpresa = 10;
        }
        $paramRespon        = explode("|", $paramResponsables);
        $solicitud          = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->find($idFactibilidad);
        $tipoSolicitud      = strtolower($solicitud->getTipoSolicitudId()->getDescripcionSolicitud());

        $emComercial->getConnection()->beginTransaction();
        $em_infraestructura->getConnection()->beginTransaction();
        $em_soporte->getConnection()->beginTransaction();  
        try
        {
            $arrayParametrosHist["strCodEmpresa"]           = $codEmpresa;
            $arrayParametrosHist["strUsrCreacion"]          = $session->get('user');
            $arrayParametrosHist["strEstadoActual"]         = "Asignada";
            $arrayParametrosHist["intIdDepartamentoOrigen"] = $intIdDepartamento;
            $arrayParametrosHist["strOpcion"]               = "Seguimiento";
            $arrayParametrosHist["strIpCreacion"]           = $peticion->getClientIp();

            // se agrega validacion del estado del servicio para bloquear operaciones incorrectas
            if( $solicitud->getServicioId()->getEstado() == "Activo" && 
                ($tipoSolicitud != 'solicitud migracion' &&
                 $tipoSolicitud != 'solicitud agregar equipo' &&
                 $tipoSolicitud != 'solicitud agregar equipo masivo' &&
                 $tipoSolicitud != 'solicitud retiro equipo' &&
                 $tipoSolicitud != 'solicitud reubicacion'))
            {
                $respuesta->setContent("El servicio Actualmente se encuentra con estado Activo, no es posible Asignar Responsable.");
                return $respuesta;
            }
            
            $boolSigueFlujoPlanificacion = false;
            if(is_object($solicitud->getServicioId()) && is_object($solicitud->getServicioId()->getProductoId())
                && (
                    $solicitud->getServicioId()->getProductoId()->getNombreTecnico() === "EXTENDER_DUAL_BAND" ||
                    $solicitud->getServicioId()->getProductoId()->getNombreTecnico() === "WIFI_DUAL_BAND" ||
                    ($solicitud->getServicioId()->getProductoId()->getNombreTecnico() === "WDB_Y_EDB" 
                        && $solicitud->getServicioId()->getEstado() != "Activo")
                   )
                && ($tipoSolicitud == "solicitud agregar equipo" || $tipoSolicitud == "solicitud agregar equipo masivo"))
            {
                $boolSigueFlujoPlanificacion = true;
            }
            
            if($origen == "local" || $origen == "otro" || $origen == "otro2")
            {         
                $boolGuardoGlobal = true;

                if(($origen == "otro" || $origen == "otro2") && !$parametro)
                {
                    $parametro = $idFactibilidad;
                }

                $array_valor = explode("|", $parametro);
                foreach($array_valor as $id):
                    $DataSolicit = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->find($id);
                    if($DataSolicit == null || $solicitud == null)
                    {
                        $mensajeError = "No existe la entidad";
                    }
                    else
                    {
                        $intIdPlan    = 0;
                        $InfoServicio = $solicitud->getServicioId();
                        //SE VALIDA QUE EXISTA ID_PLAN EN LA TABLA INFO_SERVICIO PARA CONTINUAR CON FLUJO NETLIFECAM
                        if($InfoServicio->getPlanId())
                        {
                            $intIdPlan = $InfoServicio->getPlanId()->getId();
                            $nombrePlan = $InfoServicio->getPlanId()->getNombrePlan();
                        }
                        else
                        {
                            $nombrePlan = '';
                        }
                        $infoServicioTecnico = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                           ->findOneByServicioId($InfoServicio->getId());
                        $nombreProceso = "";

                        if($tipoSolicitud == "solicitud cambio equipo")
                        {
                            $nombreProceso = "SOLICITAR CAMBIO EQUIPO";
                        }

                        if($tipoSolicitud == "solicitud retiro equipo")
                        {
                            $nombreProceso = "SOLICITAR RETIRO EQUIPO";
                        }
                        
                        if ($tipoSolicitud == "solicitud agregar equipo") 
                        {
                            $nombreProceso = "SOLICITUD AGREGAR EQUIPO";
                        }

                        if ($tipoSolicitud == "solicitud agregar equipo masivo")
                        {
                            $nombreProceso = "SOLICITUD AGREGAR EQUIPO";
                        }

                        $strSolucion = '';

                        if ($tipoSolicitud == "solicitud reubicacion") 
                        {
                            $nombreProceso = "SOLICITUD REUBICACION";
                        }

                        //Flujo para Data Center
                        if($prefijoEmpresa == 'TN' && strpos($InfoServicio->getProductoId()->getGrupo(),'DATACENTER')!==false)
                        {
                            $nombreProceso = "SOLICITAR NUEVO SERVICIO FIBRA";
                            
                            $arrayParametrosSolucion                  = array();
                            $arrayParametrosSolucion['objServicio']   = $InfoServicio;
                            $arrayParametrosSolucion['strCodEmpresa'] = $session->get('idEmpresa');
                            $strSolucion = $serviceGeneral->getNombreGrupoSolucionServicios($arrayParametrosSolucion);
                        }
                        
                        if(!$nombreProceso)
                        {
                            if($infoServicioTecnico)
                            {
                                if($infoServicioTecnico->getUltimaMillaId())
                                {
                                    $TipoMedio = $em_infraestructura->getRepository('schemaBundle:AdmiTipoMedio')
                                                                    ->find($infoServicioTecnico->getUltimaMillaId());
                                    if($TipoMedio->getNombreTipoMedio() == "Cobre")
                                    {
                                        if(strrpos($nombrePlan, "ADSL"))
                                        {
                                            $nombreProceso = "SOLICITAR NUEVO SERVICIO COBRE ADSL";
                                        }
                                        else
                                        {
                                            $nombreProceso = "SOLICITAR NUEVO SERVICIO COBRE VDSL";
                                        }
                                    }
                                    else if($TipoMedio->getNombreTipoMedio() == "Fibra Optica")
                                    {
                                        $nombreProceso = "SOLICITAR NUEVO SERVICIO FIBRA";
                                    }
                                    else if($TipoMedio->getNombreTipoMedio() == "FTTx")
                                    {
                                        $nombreProceso  = "SOLICITAR NUEVO SERVICIO FTTx";
                                    }
                                    else
                                    {
                                        $nombreProceso = "SOLICITAR NUEVO SERVICIO RADIO";
                                    }
                                }
                                else
                                {
                                    if($intIdPlan && $tipoSolicitud == "solicitud planificacion")
                                    {
                                        $arrayResultadoNetlifecam  = $emComercial->getRepository('schemaBundle:InfoPlanDet')
                                                                                 ->getProductosPlan(array("intIdPlan"        => $intIdPlan,
                                                                                                          "strNombreTecnico" => "CAMARA IP"));

                                        if(!empty($arrayResultadoNetlifecam))
                                        {
                                            $nombreProceso = "SOLICITAR NUEVO SERVICIO NETLIFECAM";
                                        }
                                        else
                                        {
                                            $nombreProceso = "SOLICITAR NUEVO SERVICIO FIBRA";
                                        }
                                    }
                                    else
                                    {
                                        $nombreProceso = "SOLICITAR NUEVO SERVICIO FIBRA";
                                    }
                                }
                            }
                        }

                        $entityProceso = $em_soporte->getRepository('schemaBundle:AdmiProceso')->findOneByNombreProceso($nombreProceso);
                        if($entityProceso != null)
                        {
                            $strObservacionTecnico  = "";
                            if($prefijoEmpresa=="TN" && $tipoSolicitud == "solicitud planificacion")
                            {
                                $strNombreCompletoTecnico   = "";
                                $strTelefonosMovil          = "";
                                $strObservacionTecnico      .= "<b>Ingeniero IPCCL2 Asignado:</b><br>";
                                if($intIdPerTecnico)
                                {
                                    $objPerTecnico  = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intIdPerTecnico);
                                    if(is_object($objPerTecnico))
                                    {
                                        $arrayParametros["intIdPersonaEmpresaRol"] = $intIdPerTecnico;
                                        $strTelefonosMovil =  $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                                          ->getNumerosAsignados($arrayParametros);

                                        $objPersonaTecnico  = $objPerTecnico->getPersonaId();
                                        if(is_object($objPersonaTecnico))
                                        {
                                            $strNombreCompletoTecnico   = sprintf("%s",$objPersonaTecnico);
                                        }
                                    }
                                }
                                $strObservacionTecnico.= !empty($strNombreCompletoTecnico) ? $strNombreCompletoTecnico : 'No Aplica';
                                if($strTelefonosMovil!="")
                                {
                                    $strObservacionTecnico.="<br/><b>Teléfonos:</b><br>".$strTelefonosMovil;
                                }
                            }
                            else if($prefijoEmpresa=="TN" && $tipoSolicitud == "solicitud reubicacion")
                            {
                                $strObservacionTecnico = $solicitud->getObservacion();
                            }
                            
                            $entityTareas = $em_soporte->getRepository('schemaBundle:AdmiTarea')->findTareasActivasByProceso($entityProceso->getId());
                            if($entityTareas != null && count($entityTareas) > 0)
                            {
                                $boolGuardo = true;

                                foreach($entityTareas as $key => $entityTarea)
                                {
                                    $band = "";
                                    $codigo = "";

                                    foreach($paramRespon as $array_responsables):
                                        $arrayVariablesR = explode("@@", $array_responsables);

                                        if($arrayVariablesR && count($arrayVariablesR) > 0)
                                        {
                                            if($entityTarea->getId() == $arrayVariablesR[0])
                                            {
                                                $band = $arrayVariablesR[1];
                                                $codigo = $arrayVariablesR[2];

                                                $codigoAsignado = $this->retornaEscogidoResponsable($band, $codigo, "codigo");
                                                $nombreAsignado = $this->retornaEscogidoResponsable($band, $codigo, "nombre");

                                                $ref_codigoAsignado = $this->retornaEscogidoResponsable($band, $codigo, "ref_codigo");
                                                $ref_nombreAsignado = $this->retornaEscogidoResponsable($band, $codigo, "ref_nombre");

                                                if($ref_codigoAsignado)
                                                {
                                                    $arrayParametros["strCodigoEmpresa"] = $idEmpresa;
                                                    $arrayParametros["intPersonaId"]     = $ref_codigoAsignado;

                                                    $intPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                                        ->getPersonaEmpresaRolPorIdPersonaYEmpresa($arrayParametros);
                                                }

                                                if($band == 'cuadrilla')
                                                {
                                                    $idPersona           = $arrayVariablesR[3];
                                                    $idPersonaEmpresaRol = $arrayVariablesR[4];
                                                }

                                            }
                                        }
                                    endforeach;
                                    
                                    $detalleSolHistId = $emComercial->getRepository('schemaBundle:InfoDetalleSolHist')
                                        ->findLastDetalleSolicitudHistorial($id);

                                    $detallSolHist = $emComercial->getRepository('schemaBundle:InfoDetalleSolHist')
                                        ->findAllSolicitudHistorial($detalleSolHistId[0]['id']);

                                    //Verificar formato
                                    $EDetalle = $em_soporte->getRepository('schemaBundle:InfoDetalle')
                                                           ->getOneDetalleByDetalleSolicitudTarea($id, $entityTarea->getId());
                                    
                                    if(!empty($strSolucion))
                                    {
                                        $strObservacionTecnico .= '<br>'.$strSolucion;
                                    }
                                    
                                    if($EDetalle != null)
                                    {                                      
                                        $entityDetalle = $EDetalle;
                                        $entityDetalle->setFeSolicitada($detallSolHist->getFeIniPlan());
                                        $entityDetalle->setObservacion($strObservacionTecnico);
                                        //$entityDetalle->setUsrCreacion($peticion->getSession()->get('user'));

                                        $em_soporte->persist($entityDetalle);
                                        $em_soporte->flush();
                                    }
                                    else
                                    {
                                        $entityDetalle = new InfoDetalle();
                                        $entityDetalle->setDetalleSolicitudId($id);
                                        $entityDetalle->setTareaId($entityTarea);
                                        $entityDetalle->setLongitud($InfoServicio->getPuntoId()->getLongitud());
                                        $entityDetalle->setLatitud($InfoServicio->getPuntoId()->getLatitud());
                                        $entityDetalle->setObservacion($strObservacionTecnico);
                                        $entityDetalle->setPesoPresupuestado(0);
                                        $entityDetalle->setValorPresupuestado(0);
                                        $entityDetalle->setIpCreacion($peticion->getClientIp());
                                        $entityDetalle->setFeCreacion(new \DateTime('now'));
                                        $entityDetalle->setFeSolicitada($detallSolHist->getFeIniPlan());
                                        $entityDetalle->setUsrCreacion($peticion->getSession()->get('user'));

                                        $em_soporte->persist($entityDetalle);
                                        $em_soporte->flush();
                                    }
                                                                        
                                    $arrayParametrosHist["intDetalleId"] = $entityDetalle->getId();

                                    //********* SAVE -- DETALLE ASIGNACION **************
                                    $entityDetalleAsignacion = new InfoDetalleAsignacion();
                                    $entityDetalleAsignacion->setDetalleId($entityDetalle);
                                    $entityDetalleAsignacion->setAsignadoId($codigoAsignado);
                                    if($nombreAsignado)
                                        $entityDetalleAsignacion->setAsignadoNombre($nombreAsignado);
                                                                        
                                    if($band == 'cuadrilla')
                                    {
                                        $integrantesCuadrilla = $emComercial->getRepository('schemaBundle:InfoCuadrillaTarea')
                                                                            ->getIntegrantesCuadrilla($codigoAsignado);
                                        
                                        if($idPersona != 0)
                                        {
                                            $objPersona = $emComercial->getRepository('schemaBundle:InfoPersona')->find($idPersona); 
                                            $entityDetalleAsignacion->setRefAsignadoId($idPersona);
                                            $entityDetalleAsignacion->setRefAsignadoNombre($objPersona->__toString());
                                        }
                                        else
                                        {
                                            $arrayDatos = $emComercial->getRepository('schemaBundle:AdmiCuadrilla')
                                                                      ->findJefeCuadrilla($codigoAsignado);                                             
   
                                            if($arrayDatos['idPersona'] != '')
                                            {
                                                $entityDetalleAsignacion->setRefAsignadoId($arrayDatos['idPersona']);
                                                $entityDetalleAsignacion->setRefAsignadoNombre($arrayDatos['nombres']);
                                                $entityDetalleAsignacion->setPersonaEmpresaRolId($arrayDatos['idPersonaEmpresaRol']);
                                                $strPersonaEmpresaRol = $arrayDatos['idPersonaEmpresaRol'];
                                                $objInfoPersonaEmpresaRol = $em_soporte->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                                       ->find($arrayDatos['idPersonaEmpresaRol']);
                                            }
                                            else
                                            {
                                                if(count($integrantesCuadrilla) > 0)
                                                { 
                                                    $entityDetalleAsignacion->setRefAsignadoId($integrantesCuadrilla[0]['idPersona']);
                                                    $entityDetalleAsignacion->setRefAsignadoNombre($integrantesCuadrilla[0]['nombres'].' '.
                                                                                                   $integrantesCuadrilla[0]['apellidos']);
                                                    $entityDetalleAsignacion->setPersonaEmpresaRolId($integrantesCuadrilla[0]['empresaRolId']);
                                                    $strPersonaEmpresaRol = $integrantesCuadrilla[0]['empresaRolId'];
                                                }
                                            }
                                        }
                                            
                                        if($idPersonaEmpresaRol != 0)
                                        {
                                            $entityDetalleAsignacion->setPersonaEmpresaRolId($idPersonaEmpresaRol);
                                            $strPersonaEmpresaRol = $idPersonaEmpresaRol;
                                            $objInfoPersonaEmpresaRol = $em_soporte->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                                   ->find($idPersonaEmpresaRol);
                                        }
                                    }
                                    else
                                    {
                                        $entityDetalleAsignacion->setRefAsignadoId($ref_codigoAsignado);
                                        $entityDetalleAsignacion->setRefAsignadoNombre($ref_nombreAsignado);
                                        $entityDetalleAsignacion->setPersonaEmpresaRolId($intPersonaEmpresaRol);
                                        $strPersonaEmpresaRol = $intPersonaEmpresaRol;
                                    }

                                    $entityDetalleAsignacion->setIpCreacion($peticion->getClientIp());
                                    $entityDetalleAsignacion->setFeCreacion(new \DateTime('now'));
                                    $entityDetalleAsignacion->setUsrCreacion($peticion->getSession()->get('user'));

                                    /* Se determina tipo de asignado CUADRILLA, EMPLEADO O CONTRATISTA */
                                    $tipoAsignado = strtoupper($band);
                                    if($tipoAsignado)
                                    {
                                        $entityDetalleAsignacion->setTipoAsignado($tipoAsignado);
                                        if($tipoAsignado=="CUADRILLA")
                                        {
                                            $observacionServicio.="<br>Asignada a: Cuadrilla";
                                            $cuadrilla=$emComercial->getRepository("schemaBundle:AdmiCuadrilla")
                                                                    ->find($entityDetalleAsignacion->getAsignadoId());
                                            $observacionServicio.="<br>Nombre: ".$cuadrilla->getNombreCuadrilla();
                                            $arrayDatos              = $emComercial->getRepository('schemaBundle:AdmiCuadrilla')
                                                                                    ->findJefeCuadrilla($entityDetalleAsignacion->getAsignadoId());
                                            $nombreLiderCuadrilla="N/A";
                                            if($arrayDatos)
                                            {
                                                $nombreLiderCuadrilla=$arrayDatos['nombres'];
                                            }
                                            $observacionServicio.="<br>L&iacute;der de Cuadrilla: ".$nombreLiderCuadrilla;
                                        }
                                        else if($tipoAsignado=="EMPLEADO")
                                        {
                                            $observacionServicio.="<br>Asignada a: Empleado";
                                            $observacionServicio.="<br>Nombre: ".$entityDetalleAsignacion->getRefAsignadoNombre();
                                            $observacionServicio.="<br>Departamento: ".$entityDetalleAsignacion->getAsignadoNombre();
                                            
                                        }
                                        else if($tipoAsignado=="EMPRESAEXTERNA")
                                        {
                                            $observacionServicio.="<br>Asignada a: Contratista";
                                            $observacionServicio.="<br>Nombre: ".$entityDetalleAsignacion->getAsignadoNombre();
                                        }
                                        $observacionServicio.="<br><br>";
                                    }

                                    $em_soporte->persist($entityDetalleAsignacion);
                                    $em_soporte->flush();

                                    //Se calcula el responsable de la trazabilidad de asignar responsable de retiro de equipo
                                    $objPersonaEmpresaRolUsr = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                           ->find($strPersonaEmpresaRol);
                                    if(is_object($objPersonaEmpresaRolUsr))
                                    {
                                        $intIdPersona = $objPersonaEmpresaRolUsr->getPersonaId()->getId();

                                        $objInfoPersona = $em_infraestructura->getRepository('schemaBundle:InfoPersona')->find($intIdPersona);

                                        if(is_object($objInfoPersona))
                                        {
                                            if($objInfoPersona->getRazonSocial() != "")
                                            {
                                                $strResponsableTrazabilidad = $objInfoPersona->getRazonSocial();
                                            }
                                            else if($objInfoPersona->getNombres() != "" && $objInfoPersona->getApellidos() != "")
                                            {
                                                $strResponsableTrazabilidad = $objInfoPersona->getApellidos() . " " . $objInfoPersona->getNombres();
                                            }
                                            else if($objInfoPersona->getRepresentanteLegal() != "")
                                            {
                                                $strResponsableTrazabilidad = $objInfoPersona->getRepresentanteLegal();
                                            }
                                            else
                                            {
                                                $strResponsableTrazabilidad = "";
                                            }
                                        }
                                    }

                                    //Se realiza el ingreso de la trazabilidad de los elementos a retirar
                                    if($tipoSolicitud == "solicitud retiro equipo")
                                    {
                                        $objInfoDetalleSolCaract = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                               ->findBy(array("detalleSolicitudId" => $idFactibilidad,
                                                                                              "caracteristicaId"   => 360));

                                        foreach($objInfoDetalleSolCaract as $objInfoDetalleSolCarac)
                                        {
                                            //Se obtiene el numero de la serie del elemento
                                            $objInfoElemento = $em_infraestructura->getRepository('schemaBundle:InfoElemento')
                                                                                  ->find($objInfoDetalleSolCarac->getValor());

                                            $objInfoElementoTrazabilidad = new InfoElementoTrazabilidad();

                                            if(is_object($objInfoElemento))
                                            {
                                                $objInfoElementoTrazabilidad->setNumeroSerie($objInfoElemento->getSerieFisica());
                                            }

                                            //Se obtiene el login asociado
                                            if(is_object($InfoServicio))
                                            {
                                                $strLoginTrazabilidad = $InfoServicio->getPuntoId()->getLogin();
                                            }

                                            $objInfoElementoTrazabilidad->setEstadoTelcos("Eliminado");
                                            $objInfoElementoTrazabilidad->setEstadoNaf("Instalado");
                                            $objInfoElementoTrazabilidad->setEstadoActivo("Eliminado");
                                            $objInfoElementoTrazabilidad->setUbicacion("Cliente");
                                            $objInfoElementoTrazabilidad->setLogin($strLoginTrazabilidad);
                                            $objInfoElementoTrazabilidad->setCodEmpresa($idEmpresa);
                                            $objInfoElementoTrazabilidad->setTransaccion("Asignacion Responsable Retiro Equipo");
                                            $objInfoElementoTrazabilidad->setObservacion("Asignacion Responsable Retiro Equipo");
                                            $objInfoElementoTrazabilidad->setOficinaId(0);
                                            $objInfoElementoTrazabilidad->setResponsable($strResponsableTrazabilidad);
                                            $objInfoElementoTrazabilidad->setUsrCreacion($peticion->getSession()->get('user'));
                                            $objInfoElementoTrazabilidad->setFeCreacion(new \DateTime('now'));
                                            $objInfoElementoTrazabilidad->setIpCreacion($peticion->getClientIp());
                                            $em_infraestructura->persist($objInfoElementoTrazabilidad);
                                            $em_infraestructura->flush();
                                        }
                                    }

                                    if($strObservacionTecnico!="")
                                    {
                                        //Se ingresa el historial de la info_detalle
                                        $arrayParametrosHist["strObservacion"] = $strObservacionTecnico;

                                        $serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);
                                    }

                                    //Se obtiene la clase del documento de una notificacion generada automaticamente por el sistema
                                    $objAdmiClaseDocumento = $emComunicacion->getRepository("schemaBundle:AdmiClaseDocumento")->find(24);

                                     //Se crea el numero de la tarea y se la asocia al id_detalle
                                    $objInfoDocumento = new InfoDocumento();
                                    $objInfoDocumento->setMensaje("Tarea generada automaticamente por el sistema Telcos");
                                    $objInfoDocumento->setNombreDocumento("Registro de llamada.");
                                    $objInfoDocumento->setClaseDocumentoId($objAdmiClaseDocumento);
                                    $objInfoDocumento->setFeCreacion(new \DateTime('now'));
                                    $objInfoDocumento->setEstado("Activo");
                                    $objInfoDocumento->setUsrCreacion($session->get('user'));
                                    $objInfoDocumento->setIpCreacion($peticion->getClientIp());
                                    $objInfoDocumento->setEmpresaCod($codEmpresa);
                                    $emComunicacion->persist($objInfoDocumento);
                                    $emComunicacion->flush();

                                    $objPunto = $InfoServicio->getPuntoId();
                                    if($objPunto)
                                    {
                                        //Se crea el numero de la tarea y se la asocia al id_detalle, se setea la
                                        //forma de contacto tipo correo electronico
                                        $objInfoComunicacion = new InfoComunicacion();
                                        $objInfoComunicacion->setFormaContactoId(5);
                                        $objInfoComunicacion->setRemitenteId($objPunto->getId());
                                        $objInfoComunicacion->setRemitenteNombre($objPunto->getLogin());
                                        $objInfoComunicacion->setClaseComunicacion("Recibido");
                                        $objInfoComunicacion->setDetalleId($entityDetalle->getId());
                                        $objInfoComunicacion->setFechaComunicacion(new \DateTime('now'));
                                        $objInfoComunicacion->setEstado("Activo");
                                        $objInfoComunicacion->setFeCreacion(new \DateTime('now'));
                                        $objInfoComunicacion->setUsrCreacion($session->get('user'));
                                        $objInfoComunicacion->setIpCreacion($peticion->getClientIp());
                                        $objInfoComunicacion->setEmpresaCod($codEmpresa);
                                        $emComunicacion->persist($objInfoComunicacion);
                                        $emComunicacion->flush();
                                    }

                                    $objInfoDocumentoComunicacion = new InfoDocumentoComunicacion();
                                    $objInfoDocumentoComunicacion->setComunicacionId($objInfoComunicacion);
                                    $objInfoDocumentoComunicacion->setDocumentoId($objInfoDocumento);
                                    $objInfoDocumentoComunicacion->setFeCreacion(new \DateTime('now'));
                                    $objInfoDocumentoComunicacion->setEstado('Activo');
                                    $objInfoDocumentoComunicacion->setUsrCreacion($session->get('user'));
                                    $objInfoDocumentoComunicacion->setIpCreacion($peticion->getClientIp());
                                    $emComunicacion->persist($objInfoDocumentoComunicacion);
                                    $emComunicacion->flush();

                                    if($band == 'cuadrilla')
                                    {    
                                        //*********************INGRESO DE INTEGRANTES DE CUADRILLA**********************
                                        $cuadrillaTarea = $emComercial->getRepository('schemaBundle:InfoCuadrillaTarea')
                                                                      ->getIntegrantesCuadrilla($codigoAsignado);                                                       

                                        foreach($cuadrillaTarea as $datoCuadrilla)
                                        {                         
                                            $infoCuadrillaTarea = new InfoCuadrillaTarea();
                                            $infoCuadrillaTarea->setDetalleId($entityDetalle);   
                                            $infoCuadrillaTarea->setCuadrillaId($codigoAsignado);  
                                            $infoCuadrillaTarea->setPersonaId($datoCuadrilla['idPersona']);                                   
                                            $infoCuadrillaTarea->setUsrCreacion($peticion->getSession()->get('user'));
                                            $infoCuadrillaTarea->setFeCreacion(new \DateTime('now'));     
                                            $infoCuadrillaTarea->setIpCreacion($peticion->getClientIp());             
                                            $em_soporte->persist($infoCuadrillaTarea); 
                                            $em_soporte->flush();  
                                        }
                                        //*********************INGRESO DE INTEGRANTES DE CUADRILLA**********************
                                    }
                                    
                                    //Se ingresa el historial de la info_detalle
                                    if(is_object($entityDetalle))
                                    {
                                        $arrayParametrosHist["intDetalleId"] = $entityDetalle->getId();
                                    }
                                    $arrayParametrosHist["strObservacion"] = "Tarea Asignada";
                                    $arrayParametrosHist["strOpcion"]      = "Historial";
                                    $arrayParametrosHist["strAccion"]      = "Asignada";

                                    $serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);

                                    $strAsignacion = $ref_nombreAsignado ? $ref_nombreAsignado : $nombreAsignado;

                                    //Se ingresa el historial de la info_detalle
                                    $arrayParametrosHist["strObservacion"] = "Tarea Asignada a " . $strAsignacion;
                                    $arrayParametrosHist["strOpcion"]      = "Seguimiento";
                                    
                                    $serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);

                                    if($entityDetalleAsignacion != null && $band == "cuadrilla")
                                    {
                                        $objData = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                               ->findByCuadrillaId($codigoAsignado);
                                        if($objData && count($objData) > 0)
                                        {
                                            foreach($objData as $keyPersona => $entityPersona)
                                            {
                                                $col_ref_codigoAsignado = $entityPersona->getPersonaId()->getId();
                                                $col_ref_nombreAsignado = $entityPersona->getPersonaId()->getNombres() . " " . 
                                                                          $entityPersona->getPersonaId()->getApellidos();

                                                $col_codigoAsignado = $this->retornaEscogidoResponsable("empleado", 
                                                                                                        $col_ref_codigoAsignado, 
                                                                                                        "codigo");
                                                $col_nombreAsignado = $this->retornaEscogidoResponsable("empleado", 
                                                                                                        $col_ref_codigoAsignado, 
                                                                                                        "nombre");
                                               
                                                
                                                if($col_nombreAsignado)
                                                {    
                                                    //********* SAVE -- DETALLE COLABORADOR**************
                                                    $entityDetalleColaborador = new InfoDetalleColaborador();
                                                    $entityDetalleColaborador->setDetalleAsignacionId($entityDetalleAsignacion);
                                                    $entityDetalleColaborador->setAsignadoId($col_codigoAsignado);
                                                    $entityDetalleColaborador->setAsignadoNombre($col_nombreAsignado);
                                                    $entityDetalleColaborador->setRefAsignadoId($col_ref_codigoAsignado);
                                                    $entityDetalleColaborador->setRefAsignadoNombre($col_ref_nombreAsignado);
                                                    $entityDetalleColaborador->setIpCreacion($peticion->getClientIp());
                                                    $entityDetalleColaborador->setFeCreacion(new \DateTime('now'));
                                                    $entityDetalleColaborador->setUsrCreacion($peticion->getSession()->get('user'));

                                                    $em_soporte->persist($entityDetalleColaborador);
                                                    $em_soporte->flush();
                                                }
                                            }
                                        }

                                        // falta coger de talba integrantes cuadrilla y grabar en INFODETALLECOLABORADORES
                                    }

                                    if($entityDetalleAsignacion == null)
                                        $boolGuardo = false;
                                    else
                                    {

                                        //------- COMUNICACIONES --- NOTIFICACIONES                             
                                        if($EDetalle != null)
                                        {
                                            $EDetalleAsignacion = $em_soporte->getRepository('schemaBundle:InfoDetalleAsignacion')
                                                                             ->getUltimaAsignacion($EDetalle->getId());

                                            $mensaje = $this->renderView('planificacionBundle:AsignarResponsable:notificacion.html.twig', 
                                                                         array('detalleSolicitud'          => $solicitud,
                                                                               'tarea'                     => $entityTarea,
                                                                               'detalleAsignacion'         => $entityDetalleAsignacion,
                                                                               'detalleAsignacionAnterior' => $EDetalleAsignacion,
                                                                               'numeroTarea'               => $objInfoComunicacion->getId()));

                                            $asunto = "Reasignacion de Tarea " . $entityTarea->getNombreTarea();
                                        }
                                        else
                                        {
                                            $mensaje = $this->renderView('planificacionBundle:AsignarResponsable:notificacion.html.twig', 
                                                                         array('detalleSolicitud'           => $solicitud,
                                                                               'tarea'                      => $entityTarea,
                                                                               'detalleAsignacion'          => $entityDetalleAsignacion,
                                                                               'detalleAsignacionAnterior'  => null,
                                                                               'numeroTarea'                => $objInfoComunicacion->getId())
                                                                        );

                                            $asunto = "Asignacion de Tarea " . $entityTarea->getNombreTarea();
                                        }

                                        //DESTINATARIOS.... 
                                        $formasContacto = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                                      ->getContactosByLoginPersonaAndFormaContacto($InfoServicio->getPuntoId()
                                                                                                                                ->getUsrVendedor(), 
                                                                                                                   'Correo Electronico');

                                        $to = array();
                                        $to[] = 'notificaciones_telcos@telconet.ec';

                                        if($formasContacto)
                                        {
                                            foreach($formasContacto as $formaContacto)
                                            {
                                                $to[] = $formaContacto['valor'];
                                            }
                                        }


                                        //Si es TN se verifica los alias del asignado y se copia la notificacion al departamento
                                        if($prefijoEmpresa == "TN")
                                        {
                                            if($band == 'cuadrilla' && $objInfoPersonaEmpresaRol)
                                            {
                                                $codigoAsignado = $objInfoPersonaEmpresaRol->getDepartamentoId();
                                            }
                                            $objAdmiAlias = $emComunicacion->getRepository('schemaBundle:AdmiAlias')
                                                                           ->findBy(array("departamentoId" => $codigoAsignado,
                                                                                          "estado"         => "Activo",
                                                                                          "empresaCod"     => $idEmpresa));
                                            if($objAdmiAlias)
                                            {
                                                foreach($objAdmiAlias as $alias)
                                                {
                                                    $to[] = $alias->getValor();
                                                }
                                            }
                                        }

                                        /* @var $envioPlantilla EnvioPlantilla */
                                        $envioPlantilla = $this->get('soporte.EnvioPlantilla');
                                        $envioPlantilla->enviarCorreo($asunto, $to, $mensaje);
                                    }
                                    $arrayInfoDetalle[] = $entityDetalle->getId();
                                }

                                if($origen == "local" && $boolGuardo)
                                {
                                    $boolGuardoGlobal = true;
                                    //SE ACTUALIZA EL ESTADO DE LA SOLICITUD	
                                    $solicitud->setEstado("AsignadoTarea");
                                    $emComercial->persist($solicitud);
                                    $emComercial->flush();

                                    //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                                    $lastDetalleSolhist = $emComercial->getRepository('schemaBundle:InfoDetalleSolHist')
                                                                      ->findOneDetalleSolicitudHistorial($id, 'Planificada');

                                    $entityDetalleSolHist = new InfoDetalleSolHist();
                                    $entityDetalleSolHist->setDetalleSolicitudId($solicitud);

                                    if($lastDetalleSolhist)
                                    {
                                        $entityDetalleSolHist->setFeIniPlan($lastDetalleSolhist->getFeIniPlan());
                                        $entityDetalleSolHist->setFeFinPlan($lastDetalleSolhist->getFeFinPlan());
                                        $entityDetalleSolHist->setObservacion($lastDetalleSolhist->getObservacion());
                                        $observacion         = $lastDetalleSolhist->getObservacion();
                                    }
                                    $entityDetalleSolHist->setIpCreacion($peticion->getClientIp());
                                    $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
                                    $entityDetalleSolHist->setUsrCreacion($peticion->getSession()->get('user'));
                                    $entityDetalleSolHist->setEstado('AsignadoTarea');
                                    // $entityDetalleSolHist->setEstado('Asignada');  

                                    $emComercial->persist($entityDetalleSolHist);
                                    $emComercial->flush();

                                    /*                                     * ***************************************************************** */

                                    $opcion = 'Cliente: ' . $InfoServicio->getPuntoId()->getNombrePunto() . ' | OPCION: Punto Cliente';

                                    $infoCriterioA = $em_soporte->getRepository('schemaBundle:InfoParteAfectada')
                                        ->getInfoParteAfectadaExistente($entityDetalle->getId());

                                    if($infoCriterioA)
                                    {
                                        $idCA = count($infoCriterioA) + 1;
                                    }
                                    else
                                        $idCA = 1;

                                    $infoCriterioAfectado = new InfoCriterioAfectado();

                                    $infoCriterioAfectado->setId($idCA);
                                    $infoCriterioAfectado->setDetalleId($entityDetalle);
                                    $infoCriterioAfectado->setCriterio("Clientes");
                                    $infoCriterioAfectado->setOpcion($opcion);
                                    $infoCriterioAfectado->setUsrCreacion($peticion->getSession()->get('user'));
                                    $infoCriterioAfectado->setFeCreacion(new \DateTime('now'));
                                    $infoCriterioAfectado->setIpCreacion($peticion->getClientIp());

                                    $em_soporte->persist($infoCriterioAfectado);
                                    $em_soporte->flush();

                                    $infoParteAfectada = new InfoParteAfectada();

                                    $infoParteAfectada->setCriterioAfectadoId($infoCriterioAfectado->getId());
                                    $infoParteAfectada->setDetalleId($entityDetalle->getId());
                                    $infoParteAfectada->setAfectadoId($InfoServicio->getPuntoId()->getId());
                                    $infoParteAfectada->setTipoAfectado("Cliente");
                                    $infoParteAfectada->setAfectadoNombre($InfoServicio->getPuntoId()->getLogin());
                                    $infoParteAfectada->setAfectadoDescripcion($InfoServicio->getPuntoId()->getNombrePunto());
                                    if($lastDetalleSolhist)
                                        $infoParteAfectada->setFeIniIncidencia($lastDetalleSolhist->getFeCreacion());
                                    else
                                        $infoParteAfectada->setFeIniIncidencia($solicitud->getFeCreacion());
                                    $infoParteAfectada->setUsrCreacion($peticion->getSession()->get('user'));
                                    $infoParteAfectada->setFeCreacion(new \DateTime('now'));
                                    $infoParteAfectada->setIpCreacion($peticion->getClientIp());

                                    $em_soporte->persist($infoParteAfectada);
                                    $em_soporte->flush();

                                    /*                                     * ***************************************************************** */

                                    if($tipoSolicitud == "solicitud planificacion" || $boolSigueFlujoPlanificacion)
                                    {
                                        //SE ACTUALIZA EL ESTADO DEL SERVICIO
                                        $entityServicio = $solicitud->getServicioId();
                                        $entityServicio->setEstado("AsignadoTarea");
                                        // $entityServicio->setEstado("Asignada");
                                        $emComercial->persist($entityServicio);
                                        $emComercial->flush();

                                        //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                                        $entityServicioHist = new InfoServicioHistorial();

                                        $entityServicioHist->setServicioId($entityServicio);

                                        $entityServicioHist->setIpCreacion($peticion->getClientIp());
                                        $entityServicioHist->setFeCreacion(new \DateTime('now'));
                                        $entityServicioHist->setUsrCreacion($peticion->getSession()->get('user'));
                                        $entityServicioHist->setEstado('AsignadoTarea');
                                        $entityServicioHist->setObservacion($observacion."<br>".$observacionServicio);
                                        // $entityServicioHist->setEstado('Asignada'); 
                                        
                                        $emComercial->persist($entityServicioHist);
                                        $emComercial->flush();
                                        
                                        $objInfoPuntoServicio=$InfoServicio->getPuntoId();
                                        $objInfoPersonaCliente=$objInfoPuntoServicio->getPersonaEmpresaRolId()->getPersonaId();
                                        $nombreCliente         = sprintf("%s",$objInfoPersonaCliente);
                                        $observacionData.="<b>Informaci&oacute;n del Cliente</b><br/>";
                                        $observacionData.="Nombre: ".$nombreCliente."<br/>";
                                        $observacionData.="Direcci&oacute;n: ".$objInfoPersonaCliente->getDireccionTributaria()."<br/>";


                                        $observacionData.="<br/><b>Informaci&oacute;n del Punto</b><br/>";
                                        $observacionData.="Nombre: ".$objInfoPuntoServicio->getNombrePunto()."<br/>";

                                        $observacionData.="Direcci&oacute;n: ".$objInfoPuntoServicio->getDireccion()."<br/>";
                                        $observacionData.="Referencia: ".$objInfoPuntoServicio->getDescripcionPunto()."<br/>";
                                        $observacionData.="Latitud: ".$objInfoPuntoServicio->getLatitud()."<br/>";
                                        $observacionData.="Longitud: ".$objInfoPuntoServicio->getLongitud()."<br/><br/>";
                                        $arrformasContactoPunto = $emComercial->getRepository('schemaBundle:InfoPuntoFormaContacto')
                                                                            ->findPorEstadoPorPunto($objInfoPuntoServicio->getId(), 'Activo', 6, 0);

                                        if($arrformasContactoPunto['registros'])
                                        {
                                            $formasContactoPunto = $arrformasContactoPunto['registros'];
                                            $observacionData.="Contactos<br/>";
                                            foreach($formasContactoPunto as $formaContactoPunto)
                                            {
                                                $descripcionFormaContactoPunto=$formaContactoPunto->getFormaContactoId()->getDescripcionFormaContacto();
                                                $observacionData.=$descripcionFormaContactoPunto.": ".$formaContactoPunto->getValor()."<br/>";

                                            }
                                        }

                                        $tipoSolFactible = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                                    ->findOneBy(array("descripcionSolicitud"=>"SOLICITUD FACTIBILIDAD"));

                                        $solFactibilidad = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                    ->findOneBy(array(
                                                                                        "servicioId"      => $solicitud->getServicioId(),
                                                                                        "tipoSolicitudId" => $tipoSolFactible
                                                                                    )
                                                                                );
                                        if($solFactibilidad)
                                        {
                                            $observacionData.="<br/><b>Datos de Factibilidad</b><br/>";
                                            $detalleSolLastFactibilidad = $emComercial->getRepository('schemaBundle:InfoDetalleSolHist')
                                                                            ->findLastDetalleSolHistByIdYEstado($solFactibilidad->getId(),
                                                                                                                "Factible");
                                            if($detalleSolLastFactibilidad)
                                            {
                                                $observacionFactibilidad=$detalleSolLastFactibilidad[0]['observacion'];
                                                $observacionData.=$observacionFactibilidad;
                                            }
                                        }

                                        
                                        $detalleSolLastPlanificacion = $emComercial->getRepository('schemaBundle:InfoDetalleSolHist')
                                                                                ->findLastDetalleSolHistByIdYEstado($id,"Planificada");
                                        if($detalleSolLastPlanificacion)
                                        {
                                            $estadoSolLastPlanificacion=$detalleSolLastPlanificacion[0]['estado'];
                                            
                                            if($estadoSolLastPlanificacion=="Planificada")
                                            {
                                                $observacionData.="<br/><b>Datos de Planificaci&oacute;n</b><br/>";
                                            }
                                            else if($estadoSolLastPlanificacion=="Replanificada")
                                            {
                                                $observacionData.="<br/><b>Datos de Replanificaci&oacute;n</b><br/>";
                                                $idMotivoLastPlanif=$detalleSolLastPlanificacion[0]['motivoId'];
                                                $motivoLastPlanificacion = $em_general->getRepository('schemaBundle:AdmiMotivo')->find($idMotivoLastPlanif);
                                                $observacionData.="Motivo: ".$motivoLastPlanificacion->getNombreMotivo()."<br/>";
                                            }
                                            $observacionPlanificacion="Observaci&oacute;n: ".$detalleSolLastPlanificacion[0]['observacion'];
                                            $feIniPlanificada=$detalleSolLastPlanificacion[0]['feIniPlan'];
                                            $feFinPlanificada=$detalleSolLastPlanificacion[0]['feFinPlan'];

                                            $fechaIniPlanificada=strval(date_format($feIniPlanificada, "d/m/Y"));
                                            $horaIniPlanificada=strval(date_format($feIniPlanificada, "H:i"));
                                            $horaFinPlanificada=strval(date_format($feFinPlanificada, "H:i"));
                                            $observacionPlanificacion.="<br>Fecha: ".$fechaIniPlanificada;
                                            $observacionPlanificacion.="<br>Hora Inicio: ".$horaIniPlanificada;
                                            $observacionPlanificacion.="<br>Hora Fin: ".$horaFinPlanificada;
                                            $observacionData.=$observacionPlanificacion;
                                        }
                                        
                                        //Se ingresa el historial de la info_detalle
                                        $arrayParametrosHist["strObservacion"] = $observacionData;

                                        $serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);
                                    }
                                    
                                    if($tipoSolicitud == "solicitud retiro equipo")
                                    {
                                        //actualizar las solicitudes caract
                                        $arrayDetalleSolicitudCarac = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                                  ->findBy(array("detalleSolicitudId"  => $solicitud->getId(), 
                                                                                                 "estado"              => "PrePlanificada"));
                                        foreach($arrayDetalleSolicitudCarac as $objDetalleSolCarac)
                                        {
                                            $objDetalleSolCarac->setEstado("AsignadoTarea");
                                            $emComercial->persist($objDetalleSolCarac);
                                            $emComercial->flush();
                                        }
                                    }
                                }
                                else
                                {
                                    $mensajeError = "No guardo";
                                }
                            }
                            else
                            {
                                $mensajeError = "No existe tareas predefinidas";
                                // $respuesta->setContent("No existe tareas predefinidas");
                            }
                        }
                        else
                        {
                            $mensajeError = "No existe el proceso predefinido";
                            //$respuesta->setContent("No existe el proceso predefinido");
                        }
                    }

                    if(!$boolGuardo)
                    {
                        $emComercial->getConnection()->rollback();
                        $em_infraestructura->getConnection()->rollback();
                        $em_soporte->getConnection()->rollback();
                        $boolGuardoGlobal = false;
                        $respuesta->setContent($mensajeError);
                    }

                endforeach;


                //validacion si es reubicacion copiar mismos recursos de red
                if($origen == "local" && $boolGuardoGlobal && ($tipoSolicitud == "solicitud planificacion" || $boolSigueFlujoPlanificacion))
                {
                    $objServicio        = $solicitud->getServicioId();
                    $tipoOrden          = "";
                    $admiProducto       = null;
                    $objPlanServicio    = null;
                    if(is_object($objServicio))
                    {
                        $tipoOrden          = $objServicio->getTipoOrden();
                        $admiProducto       = $objServicio->getProductoId();
                        $objPlanServicio    = $objServicio->getPlanId();
                    }
                    
                    if($tipoOrden == 'R')
                    {
                        $servicio = $solicitud->getServicioId();
                        $idServicio = $servicio->getId();

                        $productoInternetDedicado = $emComercial->getRepository('schemaBundle:AdmiProducto')
                                                               ->findOneBy(array("empresaCod"          => $codEmpresa, 
                                                                                 "descripcionProducto" => "INTERNET DEDICADO", 
                                                                                 "estado"              => "Activo")
                                                                          );
                        //obtengo el servicio anterior
                        $caracteristicaReubicacion = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                 ->findOneBy(array("descripcionCaracteristica" => "REUBICACION", 
                                                                                   "estado"                    => "Activo"));
                        $pcReubicacion = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                     ->findOneBy(array("productoId"       => $productoInternetDedicado->getId(), 
                                                                       "caracteristicaId" => $caracteristicaReubicacion->getId()));
                        $ispcReubicacion = $emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                       ->findOneBy(array("servicioId" => $idServicio, 
                                                                         "productoCaracterisiticaId" => $pcReubicacion->getId())
                                                                  );

                        $servicioAnterior = $emComercial->getRepository('schemaBundle:InfoServicio')->find($ispcReubicacion->getValor());
                        $idServicioAnterior = $servicioAnterior->getId();

                        //copiar elemento e interface
                        $infoServicioTecnico = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')->findOneByServicioId($idServicio);
                        $infoServicioTecnicoAnterior = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                                   ->findOneByServicioId($idServicioAnterior);

                        $infoServicioTecnico->setElementoId($infoServicioTecnicoAnterior->getElementoId());
                        $infoServicioTecnico->setInterfaceElementoId($infoServicioTecnicoAnterior->getInterfaceElementoId());
                        $infoServicioTecnico->setElementoContenedorId($infoServicioTecnicoAnterior->getElementoContenedorId());
                        $infoServicioTecnico->setElementoConectorId($infoServicioTecnicoAnterior->getElementoConectorId());
                        $infoServicioTecnico->setInterfaceElementoConectorId($infoServicioTecnicoAnterior->getInterfaceElementoConectorId());
                        $emComercial->persist($infoServicioTecnico);
                        $emComercial->flush();

                        //copiar todas las caracteristicas del servicio reubicado               
                        $servicioProdCaracts = $emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                           ->findBy(array("servicioId" => $idServicioAnterior, "estado" => 'Activo'));

                        foreach($servicioProdCaracts as $servicioProdCaract)
                        {
                            $servicioProdCaractCopy = new InfoServicioProdCaract();
                            $servicioProdCaractCopy = clone $servicioProdCaract;
                            $servicioProdCaractCopy->setServicioId($idServicio);
                            $servicioProdCaractCopy->setFeCreacion(new \DateTime('now'));
                            $servicioProdCaractCopy->setUsrCreacion($peticion->getSession()->get('user'));

                            $emComercial->persist($servicioProdCaractCopy);
                            $emComercial->flush();
                        }

                        //copiar todas las Ips
                        $infoIps = $em_infraestructura->getRepository('schemaBundle:InfoIp')
                                                      ->findBy(array("servicioId" => $idServicioAnterior, "estado" => 'Activo'));

                        foreach($infoIps as $infoIp)
                        {
                            $infoIpCopy = new InfoIp();
                            $infoIpCopy = clone $infoIp;
                            $infoIpCopy->setServicioId($idServicio);
                            $infoIpCopy->setFeCreacion(new \DateTime('now'));
                            $infoIpCopy->setUsrCreacion($peticion->getSession()->get('user'));
                            $infoIpCopy->setIpCreacion($peticion->getClientIp());

                            $em_infraestructura->persist($infoIpCopy);
                            $em_infraestructura->flush();
                        }

                        //actualizo estados
                        $solicitud->setEstado("Asignada");
                        $emComercial->persist($solicitud);
                        $emComercial->flush();

                        //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                        $lastDetalleSolhist = $emComercial->getRepository('schemaBundle:InfoDetalleSolHist')
                                                          ->findOneDetalleSolicitudHistorial($idFactibilidad, 'Planificada');

                        $entityDetalleSolHist = new InfoDetalleSolHist();
                        $entityDetalleSolHist->setDetalleSolicitudId($solicitud);
                        if($lastDetalleSolhist)
                        {
                            $entityDetalleSolHist->setFeIniPlan($lastDetalleSolhist->getFeIniPlan());
                            $entityDetalleSolHist->setFeFinPlan($lastDetalleSolhist->getFeFinPlan());
                            $entityDetalleSolHist->setObservacion($lastDetalleSolhist->getObservacion());
                        }
                        $entityDetalleSolHist->setIpCreacion($peticion->getClientIp());
                        $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
                        $entityDetalleSolHist->setUsrCreacion($peticion->getSession()->get('user'));
                        $entityDetalleSolHist->setEstado('Asignada');

                        $emComercial->persist($entityDetalleSolHist);
                        $emComercial->flush();

                        //SE ACTUALIZA EL ESTADO DEL SERVICIO
                        $servicio->setEstado("Asignada");
                        $emComercial->persist($servicio);
                        $emComercial->flush();

                        //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                        $entityServicioHist = new InfoServicioHistorial();
                        $entityServicioHist->setServicioId($servicio);
                        $entityServicioHist->setIpCreacion($peticion->getClientIp());
                        $entityServicioHist->setFeCreacion(new \DateTime('now'));
                        $entityServicioHist->setUsrCreacion($peticion->getSession()->get('user'));
                        $entityServicioHist->setEstado('Asignada');
                        $entityServicioHist->setObservacion('Por Reubicacion Pasa a Asignada');

                        $emComercial->persist($entityServicioHist);
                        $emComercial->flush();
                    }
                    $boolFlujoAsignadoAutomatico    = false;
                    $strDescripcionPlanProd         = "";
                    if(is_object($admiProducto))
                    {                        
                        $strEsVentaExterna = 'NO';
                        //verificar si tiene la caracteristica VENTA_EXTERNA
                        $objCaracteristicaExterna = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                ->findOneBy(array("descripcionCaracteristica" => "VENTA_EXTERNA", 
                                                                                  "estado"                    => "Activo"));
                        if(is_object($objCaracteristicaExterna))
                        {
                            $objProCaract = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                        ->findOneBy(array("productoId"       => $admiProducto->getId(), 
                                                                          "caracteristicaId" => $objCaracteristicaExterna->getId()));
                            if(is_object($objProCaract))
                            {
                                $strEsVentaExterna      = 'SI';
                                $strParametroDominio    = '';
                                $strTarea               = '';
                                $strParametroTareaDepar = '';
                         
                                    //obtengo los parametros
                                $objParametro = $emComercial->getRepository('schemaBundle:AdmiParametroCab')
                                                            ->findOneBy(array("nombreParametro" => 'PARAMETROS NETVOICE', 
                                                                              "estado"          =>  'Activo'));
                                if(is_object($objParametro))
                                {
                                    $objParametroTarea = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                                     ->findOneBy(array("descripcion" => 'TAREA', 
                                                                                       "parametroId" => $objParametro->getId(),
                                                                                       "estado"      => 'Activo'));  
                                    if(is_object($objParametroTarea))
                                    {
                                        $strTarea = $objParametroTarea->getValor1();
                                    }

                                    $objParametroTareaDepar = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                                          ->findOneBy(array("descripcion" => 'TAREA_DEPARTAMENTO', 
                                                                                            "parametroId" => $objParametro->getId(),
                                                                                            "estado"      => 'Activo'));
                                    if(is_object($objParametroTareaDepar))
                                    {
                                        $strParametroTareaDepar = $objParametroTareaDepar->getValor1();
                                    }
                                }
                                
                                /* @var $recursosDeRedService \telconet\tecnicoBundle\Service\InfoTelefoniaService */
                                $serviceActivar = $this->get('tecnico.InfoTelefonia');
                                $arrayParametrosLinea = array();
                                $arrayParametrosLinea['intIdServicio']      = $InfoServicio->getId();
                                $arrayParametrosLinea['strPrefijoEmpresa']  = $prefijoEmpresa;
                                $arrayParametrosLinea['strUser']            = $peticion->getSession()->get('user');
                                $arrayParametrosLinea['strIpClient']        = $peticion->getClientIp() ;
                                $arrayResultadoLinea = $serviceActivar->asignarLineaMD($arrayParametrosLinea);                                
                                
                                if($arrayResultadoLinea['status'] == 'OK')
                                {   

                                    //creo la tarea llamando al service de soporte
                                    $arrayParametros = array('strIdEmpresa'          => $codEmpresa,
                                                             'strPrefijoEmpresa'     => $prefijoEmpresa,
                                                             'strNombreTarea'        => $strTarea,
                                                             'strObservacion'        => 'Línea en proceso de activación: '
                                                                                        .' Número Telefónico: '.$arrayResultadoLinea['strTelefono']
                                                                                        .' Contraseña: '.$arrayResultadoLinea['strContrasena']
                                                                                        .' Dominio: '.$strParametroDominio,
                                                             'strNombreDepartamento' => $strParametroTareaDepar,
                                                             'strCiudad'             => '',
                                                             'strEmpleado'           => '',
                                                             'strUsrCreacion'        => $peticion->getSession()->get('user'),
                                                             'strIp'                 => $peticion->getClientIp(),
                                                             'strOrigen'             => 'WEB-TN',
                                                             'strLogin'              => $InfoServicio->getPuntoId()->getLogin(),
                                                             'intPuntoId'            => $InfoServicio->getPuntoId()->getId(),
                                                             'strNombreCliente'      => $nombreCliente
                                                            );
                                    /* @var $recursosDeRedService \telconet\soporteBundle\Service\SoporteService */
                                    $serviceSoporte = $this->get('soporte.soporteservice');

                                    $arrayTarea = $serviceSoporte->ingresarTareaInterna($arrayParametros);

                                    if($arrayTarea['status'] == "ERROR")
                                    {
                                        $respuesta->setContent('No se creó tarea Netvoice:  '.$arrayTarea['mensaje']);
                                        return $respuesta;
                                    }
                                }
                                else
                                {
                                    $respuesta->setContent(' No se asignó la línea '.$arrayResultadoLinea['strMensaje']);
                                    return $respuesta;
                                }                                   
                            }
                        }
                        /*
                         * Se modifica la validación para pasar el estado de la solicitud directamente a Asignada cuando el producto tenga de 
                         * nombre técnico OTROS y no requieran información técnica o cuando sea empresa MD y no sea enlace y no requiere info tecnica
                         */
                        if (
                             ($prefijoEmpresa == 'MD' && $admiProducto->getEsEnlace() == 'NO'  && $admiProducto->getRequiereInfoTecnica() == 'NO') ||
                             ($admiProducto->getNombreTecnico() == 'OTROS' && $admiProducto->getRequiereInfoTecnica() == 'NO' 
                             || $strEsVentaExterna == 'SI')
                           )
                        {
                            $boolFlujoAsignadoAutomatico    = true;
                            $strDescripcionPlanProd         = $admiProducto->getDescripcionProducto();
                            
                            
                        }
                    }
                    /**
                     * Se agrega validación cuando el servicio esté asociado a un plan y si es cliente MD, para validar si el plan tiene
                     * asociada la característica FLUJO_PREPLANIFICACION_PLANIFICACION='SI', que servirá como referencia para pasar el servicio 
                     * de estado 'AsignadoTarea' a 'Asignada' 
                     */
                    else if(is_object($objPlanServicio))
                    {
                        if($prefijoEmpresa == 'MD' || $prefijoEmpresa == 'EN')
                        {
                            $arrayParamSigueFlujoPP             = array(
                                                                        "intIdPlan"                         => $objPlanServicio->getId(),
                                                                        "strDescripcionCaracteristicaPlan"  => 'FLUJO_PREPLANIFICACION_PLANIFICACION',
                                                                        "strEstado"                         => "Activo"
                                                                  );
                            $arrayRespuestaSigueFlujoPP         = $emComercial->getRepository('schemaBundle:InfoPlanCaracteristica')
                                                                              ->getCaracteristicasPlanByCriterios($arrayParamSigueFlujoPP);
                            $intTotalCaractSigueFlujoPP         = $arrayRespuestaSigueFlujoPP['intTotal'];
                            $arrayResultadoCaractSigueFlujoPP   = $arrayRespuestaSigueFlujoPP['arrayResultado'];

                            if($intTotalCaractSigueFlujoPP > 0 && $arrayResultadoCaractSigueFlujoPP[0] 
                                && $arrayResultadoCaractSigueFlujoPP[0]['strValor'] == "SI")
                            {
                                $boolFlujoAsignadoAutomatico    = true;
                                $strDescripcionPlanProd         = $objPlanServicio->getNombrePlan();
                            }
                        }
                    }
                    else
                    {
                        throw new Exception("Ha ocurrido un error. No se ha encontrado ni el producto ni el plan del servicio");
                    }
                    if($boolFlujoAsignadoAutomatico && is_object($objServicio))
                    {
                        //actualizo estados
                        $solicitud->setEstado("Asignada");
                        $emComercial->persist($solicitud);
                        $emComercial->flush();

                        //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                        $lastDetalleSolhist = $emComercial->getRepository('schemaBundle:InfoDetalleSolHist')
                                                          ->findOneDetalleSolicitudHistorial($idFactibilidad, 'Planificada');

                        $entityDetalleSolHist = new InfoDetalleSolHist();
                        $entityDetalleSolHist->setDetalleSolicitudId($solicitud);
                        if($lastDetalleSolhist)
                        {
                            $entityDetalleSolHist->setFeIniPlan($lastDetalleSolhist->getFeIniPlan());
                            $entityDetalleSolHist->setFeFinPlan($lastDetalleSolhist->getFeFinPlan());
                            $entityDetalleSolHist->setObservacion($lastDetalleSolhist->getObservacion());
                        }
                        $entityDetalleSolHist->setIpCreacion($peticion->getClientIp());
                        $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
                        $entityDetalleSolHist->setUsrCreacion($peticion->getSession()->get('user'));
                        $entityDetalleSolHist->setEstado('Asignada');

                        $emComercial->persist($entityDetalleSolHist);
                        $emComercial->flush();
                        
                        if(is_object($objServicio->getProductoId()) && $objServicio->getProductoId()->getNombreTecnico() === "WDB_Y_EDB")
                        {
                            $arrayParamsServInternetValido  = array("intIdPunto"    => $objServicio->getPuntoId()->getId(),
                                                                    "strCodEmpresa" => $codEmpresa);
                            if($objServicio->getTipoOrden() === "T")
                            {
                                $arrayParamsServInternetValido["omiteEstadoPunto"] = "SI";
                            }
                            $arrayRespuestaServInternetValido   = $serviceGeneral->obtieneServicioInternetValido($arrayParamsServInternetValido);
                            $strStatusServInternetValido        = $arrayRespuestaServInternetValido["status"];
                            $objServicioInternet                = $arrayRespuestaServInternetValido["objServicioInternet"];
                            if($strStatusServInternetValido === "OK" && is_object($objServicioInternet))
                            {
                                if($objServicio->getTipoOrden() === "T")
                                {
                                    $arrayRespuestaPlanifWyApTraslado   = $servicePlanificar->gestionarPlanificacionWyApTraslado(array(
                                                                                    "objServicioInternet"   => $objServicioInternet,
                                                                                    "strCodEmpresa"         => $codEmpresa,
                                                                                    "objServicio"           => $objServicio,
                                                                                    "strUsrCreacion"        => $session->get('user')));
                                    if($arrayRespuestaPlanifWyApTraslado["status"] === "ERROR")
                                    {
                                        throw new \Exception($arrayRespuestaPlanifWyApTraslado["mensaje"]);
                                    }
                                    $strNuevoEstadoServicio = $arrayRespuestaPlanifWyApTraslado["nuevoEstadoServicio"];
                                }
                                else
                                {
                                    $arrayRespuestaWdbEnlazado  = $serviceGeneral->verificaEquipoEnlazado(
                                                                                       array("intIdServicioInternet" => $objServicioInternet->getId(),
                                                                                             "strTipoEquipoABuscar"  => "WIFI DUAL BAND"));
                                    $strInfoEquipoWdbEnlazado   = $arrayRespuestaWdbEnlazado["infoEquipoEnlazado"];
                                    if(isset($strInfoEquipoWdbEnlazado) && !empty($strInfoEquipoWdbEnlazado))
                                    {
                                        $strNuevoEstadoServicio = "PendienteAp";
                                    }
                                    else
                                    {
                                        $strNuevoEstadoServicio = "Asignada";
                                    }
                                }
                            }
                            else
                            {
                                throw new \Exception("No se ha podido obtener el servicio de Internet asociado al punto");
                            }
                        }
                        else
                        {
                            $strNuevoEstadoServicio = "Asignada";
                        }
                        //SE ACTUALIZA EL ESTADO DEL SERVICIO
                        $objServicio->setEstado($strNuevoEstadoServicio);
                        $emComercial->persist($objServicio);
                        $emComercial->flush();

                        //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                        $entityServicioHist = new InfoServicioHistorial();
                        $entityServicioHist->setServicioId($objServicio);
                        $entityServicioHist->setIpCreacion($peticion->getClientIp());
                        $entityServicioHist->setFeCreacion(new \DateTime('now'));
                        $entityServicioHist->setUsrCreacion($peticion->getSession()->get('user'));
                        $entityServicioHist->setEstado($strNuevoEstadoServicio);
                        $entityServicioHist->setObservacion('Por ser '.$strDescripcionPlanProd.' Pasa a: Asignada');

                        $emComercial->persist($entityServicioHist);
                        $emComercial->flush();
                    }
                }
                
                if($origen == "local" && 
                   $boolGuardoGlobal  && 
                   ($tipoSolicitud == "solicitud agregar equipo" || $tipoSolicitud == "solicitud agregar equipo masivo"
                    || $tipoSolicitud == "solicitud reubicacion"))
                {
                    //actualizar las solicitudes caract
                    $arrayDetalleSolicitudCarac = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                              ->findBy(array("detalleSolicitudId"  => $solicitud->getId(), 
                                                                             "estado"              => "PrePlanificada"));
                    foreach($arrayDetalleSolicitudCarac as $objDetalleSolCarac)
                    {
                        $objDetalleSolCarac->setEstado("Asignada");
                        $emComercial->persist($objDetalleSolCarac);
                        $emComercial->flush();
                    }
                    
                    //actualizo estados
                    $solicitud->setEstado("Asignada");
                    $emComercial->persist($solicitud);
                    $emComercial->flush();

                    $objDetalleSolhistAnt = $emComercial->getRepository('schemaBundle:InfoDetalleSolHist')
                                                        ->findOneDetalleSolicitudHistorial($idFactibilidad, 'Planificada');

                    $objDetalleSolHist = new InfoDetalleSolHist();
                    $objDetalleSolHist->setDetalleSolicitudId($solicitud);
                    if(is_object($objDetalleSolhistAnt))
                    {
                        $objDetalleSolHist->setFeIniPlan($objDetalleSolhistAnt->getFeIniPlan());
                        $objDetalleSolHist->setFeFinPlan($objDetalleSolhistAnt->getFeFinPlan());
                        $objDetalleSolHist->setObservacion($objDetalleSolhistAnt->getObservacion());
                    }
                    $objDetalleSolHist->setIpCreacion($peticion->getClientIp());
                    $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
                    $objDetalleSolHist->setUsrCreacion($peticion->getSession()->get('user'));
                    $objDetalleSolHist->setEstado('Asignada');
                    $emComercial->persist($objDetalleSolHist);
                    $emComercial->flush();
                }

                //validacion si es cambio de equipo y no tiene equipo asignado le crea
                if($prefijoEmpresa == "TTCO" && $origen == "local" && $boolGuardoGlobal && ($tipoSolicitud == "solicitud cambio equipo"))
                {
                    //GUARDAR INFO DETALLE SOLICICITUD SOL CARACT
                    $DetallesSolCaract = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                     ->findBy(array("detalleSolicitudId" => $solicitud->getId(), "estado" => "Activo"));

                    if(!$DetallesSolCaract)
                    {
                        $servicio            = $solicitud->getServicioId();
                        $idServicio          = $servicio->getId();
                        $infoServicioTecnico = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')->findOneByServicioId($idServicio);
                        $caracteristicaElementoCliente = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                     ->findOneBy(array("descripcionCaracteristica" => "ELEMENTO CLIENTE", 
                                                                                       "estado"                    => "Activo"));

                        $entityDetalleSolCaract = new InfoDetalleSolCaract();
                        $entityDetalleSolCaract->setCaracteristicaId($caracteristicaElementoCliente);
                        $entityDetalleSolCaract->setDetalleSolicitudId($solicitud);
                        $entityDetalleSolCaract->setValor($infoServicioTecnico->getElementoClienteId());
                        $entityDetalleSolCaract->setFeCreacion(new \DateTime('now'));
                        $entityDetalleSolCaract->setUsrCreacion($peticion->getSession()->get('user'));
                        $entityDetalleSolCaract->setEstado('Activo');

                        $emComercial->persist($entityDetalleSolCaract);
                        $emComercial->flush();

                        //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                        $entityServicioHist = new InfoServicioHistorial();
                        $entityServicioHist->setServicioId($servicio);
                        $entityServicioHist->setIpCreacion($peticion->getClientIp());
                        $entityServicioHist->setFeCreacion(new \DateTime('now'));
                        $entityServicioHist->setUsrCreacion($peticion->getSession()->get('user'));
                        $entityServicioHist->setEstado($solicitud->getEstado());
                        $entityServicioHist->setObservacion('Se agrego la caracteristica elemento id:' . $infoServicioTecnico->getElementoClienteId());

                        $emComercial->persist($entityServicioHist);
                        $emComercial->flush();
                    }
                }
            }
            else
            {
                $boolGuardoGlobal = false;
                $respuesta->setContent("No existe esa opcion");
            }

            
            //Realizar la asignación de Recursos de Red de manera automatica para flujos L2MPLS
            if(is_object($InfoServicio->getProductoId()) && $InfoServicio->getProductoId()->getNombreTecnico()=='L2MPLS')
            {                
                /* @var $serviceRecursoRed RecursosDeRedService */
                $serviceRecursoRed = $this->get('planificacion.RecursosDeRed');
                $arrayParametros                   = array();
                $arrayParametros['objServicio']    = $InfoServicio;
                $arrayParametros['strUsrCreacion'] = $peticion->getSession()->get('user');
                $arrayParametros['strIpCreacion']  = $peticion->getClientIp();
                $arrayParametros['intIdEmpresa']   = $peticion->getSession()->get('idEmpresa');
                $arrayParametros['strPrefijo']     = $peticion->getSession()->get('prefijoEmpresa');
                $arrayParametros['objSolicitud']   = $solicitud;    
                $arrayResultado                    = $serviceRecursoRed->asignarRecursosRedL2MPLS($arrayParametros);
                
                if($arrayResultado['status'] != 'OK')
                {
                    $boolGuardoGlobal = false;
                    $respuesta->setContent($arrayResultado['mensaje']);
                }                
            }
            
            if($boolGuardoGlobal)
            {                
                $emComercial->getConnection()->commit();
                $em_infraestructura->getConnection()->commit();
                $em_soporte->getConnection()->commit();
                $respuesta->setContent("Se asignaron la(s) Tarea(s) Correctamente.");
                
                //Proceso que graba tarea en INFO_TAREA
                for($intIinfoTarea=0;$intIinfoTarea <= count($arrayInfoDetalle);$intIinfoTarea++)
                {
                    $arrayParametrosInfoTarea['intDetalleId']   = $arrayInfoDetalle[$intIinfoTarea];
                    $arrayParametrosInfoTarea['strUsrCreacion'] = $peticion->getSession()->get('user');
                    $objSoporteService->crearInfoTarea($arrayParametrosInfoTarea);
                }
            }
        }
        catch(\Exception $e)
        {
            $emComercial->getConnection()->rollback();
            $em_infraestructura->getConnection()->rollback();
            $em_soporte->getConnection()->rollback();

            $mensajeError = "Error: " . $e->getMessage();
            error_log($mensajeError);
            $respuesta->setContent($mensajeError);
        }
        return $respuesta;
    }

    //************** FUNCTION QUE HACE EL PROCESO DE GUARDAR UN ID DETALLE SOLICITUD 
    function guardaUnoIndividual($id, $paramRespon)
    {
        $mensajeError = "";

        $peticion = $this->get('request');
        $session = $peticion->getSession();
        $idEmpresa = $session->get('idEmpresa');
        $prefijoEmpresa = $session->get('prefijoEmpresa');
        $em = $this->getDoctrine()->getManager("telconet");
        $em_soporte = $this->getDoctrine()->getManager("telconet_soporte");
        $em_infraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        $em_comunicacion = $this->getDoctrine()->getManager("telconet_comunicacion");
        $boolGuardo = false;
        $mensajeError = "Ocurrio un Error";

        $em->getConnection()->beginTransaction();
        $em_comunicacion->getConnection()->beginTransaction();

        try
        {

            $entity = $em->getRepository('schemaBundle:InfoDetalleSolicitud')->find($id);
            $DataSolicit = $this->getDoctrine()
                ->getManager("telconet")
                ->getRepository('schemaBundle:InfoDetalleSolicitud')
                ->getRegistros(0, 1, "", "", "TODOS", "", "", $id);

            if($DataSolicit == null || $entity == null)
            {
                $mensajeError = "No existe la entidad";
            }
            else
            {

                $InfoServicio = $entity->getServicioId();
                $nombrePlan = $InfoServicio->getPlanId()->getNombrePlan();
                $infoServicioTecnico = $em->getRepository('schemaBundle:InfoServicioTecnico')->findOneByServicioId($InfoServicio->getId());
                $TipoMedio = $em_infraestructura->getRepository('schemaBundle:AdmiTipoMedio')->find($infoServicioTecnico->getUltimaMillaId());

                if($TipoMedio->getNombreTipoMedio() == "Cobre")
                {
                    if(strrpos($nombrePlan, "ADSL"))
                        $nombreProceso = "SOLICITAR NUEVO SERVICIO COBRE ADSL";
                    else
                        $nombreProceso = "SOLICITAR NUEVO SERVICIO COBRE VDSL";
                }
                else
                    $nombreProceso = "SOLICITAR NUEVO SERVICIO RADIO";

                $entityProceso = $em_soporte->getRepository('schemaBundle:AdmiProceso')->findOneByNombreProceso($nombreProceso);
                if($entityProceso != null)
                {
                    $entityTareas = $em_soporte->getRepository('schemaBundle:AdmiTarea')->findTareasActivasByProceso($entityProceso->getId());
                    if($entityTareas != null && count($entityTareas) > 0)
                    {
                        $boolGuardo = true;

                        foreach($entityTareas as $key => $entityTarea)
                        {
                            $band = "";
                            $codigo = "";

                            foreach($paramRespon as $array_responsables):
                                $arrayVariablesR = explode("@@", $array_responsables);

                                if($arrayVariablesR && count($arrayVariablesR) > 0)
                                {
                                    if($entityTarea->getId() == $arrayVariablesR[0])
                                    {
                                        $band = $arrayVariablesR[1];
                                        $codigo = $arrayVariablesR[2];

                                        $codigoAsignado = $this->retornaEscogidoResponsable($band, $codigo, "codigo");
                                        $nombreAsignado = $this->retornaEscogidoResponsable($band, $codigo, "nombre");

                                        $ref_codigoAsignado = $this->retornaEscogidoResponsable($band, $codigo, "ref_codigo");
                                        $ref_nombreAsignado = $this->retornaEscogidoResponsable($band, $codigo, "ref_nombre");
                                    }
                                }
                            endforeach;

                            $EDetalle = $em_soporte->getRepository('schemaBundle:InfoDetalle')->getOneDetalleByDetalleSolicitudTarea($id, $entityTarea->getId());
                            if($EDetalle != null)
                            {
                                $entityDetalle = $EDetalle;
                            }
                            else
                            {
                                //********* SAVE -- DETALLE (TAREA) **************
                                $entityDetalle = new InfoDetalle();
                                $entityDetalle->setDetalleSolicitudId($id);
                                $entityDetalle->setTareaId($entityTarea);
                                $entityDetalle->setLongitud($DataSolicit[0]["longitud"]);
                                $entityDetalle->setLatitud($DataSolicit[0]["latitud"]);
                                $entityDetalle->setPesoPresupuestado(0);
                                $entityDetalle->setValorPresupuestado(0);
                                $entityDetalle->setIpCreacion($peticion->getClientIp());
                                $entityDetalle->setFeCreacion(new \DateTime('now'));
                                $entityDetalle->setUsrCreacion($peticion->getSession()->get('user'));

                                $em_soporte->persist($entityDetalle);
                                $em_soporte->flush();
                            }

                            //********* SAVE -- DETALLE ASIGNACION **************
                            $entityDetalleAsignacion = new InfoDetalleAsignacion();
                            $entityDetalleAsignacion->setDetalleId($entityDetalle);
                            $entityDetalleAsignacion->setAsignadoId($codigoAsignado);
                            $entityDetalleAsignacion->setAsignadoNombre($nombreAsignado);
                            $entityDetalleAsignacion->setRefAsignadoId($ref_codigoAsignado);
                            $entityDetalleAsignacion->setRefAsignadoNombre($ref_nombreAsignado);
                            $entityDetalleAsignacion->setIpCreacion($peticion->getClientIp());
                            $entityDetalleAsignacion->setFeCreacion(new \DateTime('now'));
                            $entityDetalleAsignacion->setUsrCreacion($peticion->getSession()->get('user'));

                            $em_soporte->persist($entityDetalleAsignacion);
                            $em_soporte->flush();

                            if($entityDetalleAsignacion != null && $band == "cuadrilla")
                            {
                                $objData = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findByCuadrillaId($codigoAsignado);
                                if($objData && count($objData) > 0)
                                {
                                    foreach($objData as $keyPersona => $entityPersona)
                                    {
                                        $col_ref_codigoAsignado = $entityPersona->getPersonaId()->getId();
                                        $col_ref_nombreAsignado = $entityPersona->getPersonaId()->getNombres() . " " . $entityPersona->getPersonaId()->getApellidos();

                                        $col_codigoAsignado = $this->retornaEscogidoResponsable("empleado", $col_ref_codigoAsignado, "codigo");
                                        $col_nombreAsignado = $this->retornaEscogidoResponsable("empleado", $col_ref_codigoAsignado, "nombre");


                                        //********* SAVE -- DETALLE COLABORADOR**************
                                        $entityDetalleColaborador = new InfoDetalleColaborador();
                                        $entityDetalleColaborador->setDetalleAsignacionId($entityDetalleAsignacion);
                                        $entityDetalleColaborador->setAsignadoId($col_codigoAsignado);
                                        $entityDetalleColaborador->setAsignadoNombre($col_nombreAsignado);
                                        $entityDetalleColaborador->setRefAsignadoId($col_ref_codigoAsignado);
                                        $entityDetalleColaborador->setRefAsignadoNombre($col_ref_nombreAsignado);
                                        $entityDetalleColaborador->setIpCreacion($peticion->getClientIp());
                                        $entityDetalleColaborador->setFeCreacion(new \DateTime('now'));
                                        $entityDetalleColaborador->setUsrCreacion($peticion->getSession()->get('user'));

                                        $em_soporte->persist($entityDetalleColaborador);
                                        $em_soporte->flush();
                                    }
                                }

                                // falta coger de talba integrantes cuadrilla y grabar en INFODETALLECOLABORADORES
                            }

                            if($entityDetalleAsignacion == null)
                                $boolGuardo = false;
                            else
                            {

                                //------- COMUNICACIONES --- NOTIFICACIONES                             
                                if($EDetalle != null)
                                {
                                    $EDetalleAsignacion = $em_soporte->getRepository('schemaBundle:InfoDetalleAsignacion')->getUltimaAsignacion($EDetalle->getId());

                                    $mensaje = $this->renderView('planificacionBundle:AsignarResponsable:notificacion.html.twig', array('detalleSolicitud' => $entity,
                                        'tarea' => $entityTarea,
                                        'detalleAsignacion' => $entityDetalleAsignacion,
                                        'detalleAsignacionAnterior' => $EDetalleAsignacion));

                                    $asunto = "Reasignacion de Tarea " . $entityTarea->getNombreTarea();
                                }
                                else
                                {
                                    $mensaje = $this->renderView('planificacionBundle:AsignarResponsable:notificacion.html.twig', array('detalleSolicitud' => $entity,
                                        'tarea' => $entityTarea,
                                        'detalleAsignacion' => $entityDetalleAsignacion,
                                        'detalleAsignacionAnterior' => null));

                                    $asunto = "Asignacion de Tarea " . $entityTarea->getNombreTarea();
                                }

                                //DESTINATARIOS.... 
                                $formasContacto = $em->getRepository('schemaBundle:InfoPersona')->getContactosByLoginPersonaAndFormaContacto($InfoServicio->getPuntoId()->getUsrVendedor(), 'Correo Electronico');

                                $to = array();
                                $to[] = 'notificaciones_telcos@telconet.ec';

                                if($formasContacto)
                                {
                                    foreach($formasContacto as $formaContacto)
                                    {
                                        $to[] = $formaContacto['valor'];
                                    }
                                }

                                /* @var $envioPlantilla EnvioPlantilla */
                                $envioPlantilla = $this->get('soporte.EnvioPlantilla');
                                $envioPlantilla->enviarCorreo($asunto, $to, $mensaje);
                            }
                        }

                        if($boolGuardo)
                        {
                            $boolGuardoGlobal = true;
                            //SE ACTUALIZA EL ESTADO DE LA SOLICITUD	
                            $entity->setEstado("AsignadoTarea");
                            // $entity->setEstado("Asignada");
                            $em->persist($entity);
                            $em->flush();

                            //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                            $lastDetalleSolhist = $em->getRepository('schemaBundle:InfoDetalleSolHist')->findOneDetalleSolicitudHistorial($id, 'Planificada');

                            $entityDetalleSolHist = new InfoDetalleSolHist();
                            $entityDetalleSolHist->setDetalleSolicitudId($entity);
                            if($lastDetalleSolhist)
                            {
                                $entityDetalleSolHist->setFeIniPlan($lastDetalleSolhist->getFeIniPlan());
                                $entityDetalleSolHist->setFeFinPlan($lastDetalleSolhist->getFeFinPlan());
                                $entityDetalleSolHist->setObservacion($lastDetalleSolhist->getObservacion());
                            }
                            $entityDetalleSolHist->setIpCreacion($peticion->getClientIp());
                            $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
                            $entityDetalleSolHist->setUsrCreacion($peticion->getSession()->get('user'));
                            $entityDetalleSolHist->setEstado('AsignadoTarea');
                            // $entityDetalleSolHist->setEstado('Asignada');  

                            $em->persist($entityDetalleSolHist);
                            $em->flush();

                            //SE ACTUALIZA EL ESTADO DEL SERVICIO
                            $entityServicio = $entity->getServicioId();
                            $entityServicio->setEstado("AsignadoTarea");
                            // $entityServicio->setEstado("Asignada");
                            $em->persist($entityServicio);
                            $em->flush();

                            //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                            $entityServicioHist = new InfoServicioHistorial();
                            $entityServicioHist->setServicioId($entityServicio);

                            $entityServicioHist->setIpCreacion($peticion->getClientIp());
                            $entityServicioHist->setFeCreacion(new \DateTime('now'));
                            $entityServicioHist->setUsrCreacion($peticion->getSession()->get('user'));
                            $entityServicioHist->setEstado('AsignadoTarea');
                            // $entityServicioHist->setEstado('Asignada');  

                            $em->persist($entityServicioHist);
                            $em->flush();
                        }
                        else
                        {
                            $mensajeError = "No guardo";
                        }
                    }
                    else
                    {
                        $mensajeError = "No existe tareas predefinidas";
                        // $respuesta->setContent("No existe tareas predefinidas");
                    }
                }
                else
                {
                    $mensajeError = "No existe el proceso predefinido";
                    //$respuesta->setContent("No existe el proceso predefinido");
                }
            }

            if($boolGuardo)
            {
                $em->getConnection()->commit();
                $em_comunicacion->getConnection()->commit();
                return "OK";
            }
            else
            {
                $em->getConnection()->rollback();
                $em_comunicacion->getConnection()->rollback();
                return $mensajeError;
            }

            $em->getConnection()->commit();
            $em_comunicacion->getConnection()->commit();
        }
        catch(\Exception $e)
        {
            $em->getConnection()->rollback();
            $em_comunicacion->getConnection()->rollback();

            $mensajeError = "Error: " . $e->getMessage();
            error_log($mensajeError);
        }

        return $mensajeError;
    }

    //************** FUNCTION QUE RETORNA EL CODIGO Y EL NOMBRE DEL ASIGNADO -- ESCOGIDO DEL COMBO
    function retornaEscogidoResponsable($band, $codigo, $retorna)
    {
        $em = $this->getDoctrine()->getManager("telconet");
        $em_soporte = $this->getDoctrine()->getManager("telconet_soporte");

        $peticion = $this->get('request');
        $codEmpresa = ($peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa') : "");
        if($codEmpresa == 18 || $codEmpresa == 33)
        {
            $codEmpresa = 10;
        }
        if($retorna == "codigo" || $retorna == "nombre")
        {
            $codigoAsignado = 0;
            $nombreAsignado = "";

            if($band == "empleado")
            {
                $arrayDepartamento = $em->getRepository('schemaBundle:InfoPersona')->getDepartamentoByEmpleado($codEmpresa, $codigo);
                $codigoAsignado = $arrayDepartamento["id_departamento"];
                $nombreAsignado = $arrayDepartamento["nombre_departamento"];
            }
            if($band == "empresaExterna")
            {
                if($retorna == "codigo")
                    return $codigo;

                $entityPersona = $em->getRepository('schemaBundle:InfoPersona')->findOneById($codigo);
                $codigoAsignado = $codigo;
                $nombreAsignado = sprintf("%s", $entityPersona);
                return $nombreAsignado;
            }
            else if($band == "cuadrilla")
            {
                if($retorna == "codigo")
                    return $codigo;

                $entityCuadrilla = $em->getRepository('schemaBundle:AdmiCuadrilla')->findOneById($codigo);
                $codigoAsignado = $codigo;
                $nombreAsignado = $entityCuadrilla->getNombreCuadrilla();
                return $nombreAsignado;
            }

            if($retorna == "codigo")
                return $codigoAsignado;
            else if($retorna == "nombre")
                return $nombreAsignado;
            else
                return false;
        }
        else if($retorna == "ref_codigo" || $retorna == "ref_nombre")
        {
            $ref_codigoAsignado = 0;
            $ref_nombreAsignado = "";

            if($band == "empleado")
            {
                if($retorna == "ref_codigo")
                    return $codigo;

                $entityPersona = $em->getRepository('schemaBundle:InfoPersona')->findOneById($codigo);
                $ref_nombreAsignado = sprintf("%s", $entityPersona);
                return $ref_nombreAsignado;
            }

            if($retorna == "ref_codigo")
                return $ref_codigoAsignado;
            else if($retorna == "ref_nombre")
                return $ref_nombreAsignado;
            else
                return false;
        }
    }

    /**
     *
     * Documentación de la funcion 'getTecnicos'.
     *
     * Método que retorna los empleados de cualquier departamento que aparecerán en el combo técnico al asignar un responsable
     *
     * @return JsonResponse retorna el resultado de la operación
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 10-11-2016
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 08-11-2018 Se realizan ajustes en el comboBox que muestra los ingenieros de IPCCL2 en la pantalla de asignar responsable,
     *                         el cambio consiste en mostrar solo el personal del departamento de IPCCL2 y el número telefónico asignado
     *                         por la empresa
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.2 09-04-2019 Se agregó funcionalidad para que si llega por $objRequest el tipo de esquema se valide
     *                         si el tipo de esquema es 1 para poder devolver técnicos de RADIO.
     */
    public function getTecnicosAction()
    {
        $emComercial           = $this->getDoctrine()->getManager('telconet');
        $objRequest            = $this->getRequest();
        $objSession            = $objRequest->getSession();
        $objResponse           = new JsonResponse();
        $intStart              = $objRequest->get("start") ? $objRequest->get("start") : 0;
        $intLimit              = $objRequest->get("limit") ? $objRequest->get("limit") : 0;
        $intIdEmpresa          = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $strOrigen             = $objRequest->get("origen") ? $objRequest->get("origen") : "";
        $strNombreDepartamento = $objRequest->get("departamento") ? $objRequest->get("departamento") : "";
        $intTipoEsquema        = $objRequest->get('tipo_esquema') ? $objRequest->get('tipo_esquema') : '';


        if($intIdEmpresa==18)
        {
            $intIdEmpresa = 10;
        }
        
        $strNombresApellidosTecnico = $objRequest->get("query") ? $objRequest->get("query") : '';

        $arrayParametros = array(
                                    'idEmpresa'                     => $intIdEmpresa,
                                    'nombreApellidoPer'             => $strNombresApellidosTecnico,
                                    'start'                         => $intStart,
                                    'limit'                         => $intLimit,
                                    'estado'                        => 'Activo',
                                    'strDescripcionTipoRol'         => 'Empleado',
                                    'strDescripcionFormaContacto'   => 'Telefono Movil',
                                    'origen'                        => $strOrigen,
                                    'departamento'                  => $strNombreDepartamento,

        );
        /* Se valida si el tipo de esquema es 1 (BACKBONE), para establecer el departamento como "RADIO" y obtener
           los técnicos de dicho departamento.*/
        if ($intTipoEsquema == 1)
        {
            $strNombreDepartamento = 'RADIO';
            $arrayParametros['strNombreDepartamento'] = $strNombreDepartamento;
        }

        $strJson    = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->getJSONPersonaEmpresaRolPorCriterios($arrayParametros);
        $objResponse->setContent($strJson);
        return $objResponse;
        
    }
    
}
