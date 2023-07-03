<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\planificacionBundle\Controller;

use Doctrine\ORM\UnexpectedResultException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\JsonResponse;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoElemento;
use telconet\schemaBundle\Entity\InfoEnlace;
use telconet\schemaBundle\Entity\InfoUbicacion;
use telconet\schemaBundle\Entity\InfoEmpresaElementoUbica;
use telconet\schemaBundle\Entity\InfoEmpresaElemento;
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Entity\InfoRelacionElemento;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoServicioTecnico;
use telconet\planificacionBundle\Service\PlanificarService;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

class NodoWifiController extends Controller implements TokenAuthenticatedController
{

        /**
     *
     * Retorna el twig y carga los permisos de la pantalla
     * @return retorna el twig
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 28-12-2015
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.1 07-07-2016 se agrega el permiso Editar Factibilidad Wifi
     */
    public function indexAction()
    {
        $rolesPermitidos = array();
        if(true === $this->get('security.context')->isGranted('ROLE_341-3897'))
        {
            $rolesPermitidos[] = 'ROLE_341-3897'; // NewNodoWifiFactibilidad
        }
        if(true === $this->get('security.context')->isGranted('ROLE_341-3898'))
        {
            $rolesPermitidos[] = 'ROLE_341-3898'; // RelacionElementoNodoWifi
        }
        if(true === $this->get('security.context')->isGranted('ROLE_341-3917'))
        {
            $rolesPermitidos[] = 'ROLE_341-3917'; // AsignaFactibilidadNodoWifi
        }
        if(true === $this->get('security.context')->isGranted('ROLE_341-3918'))
        {
            $rolesPermitidos[] = 'ROLE_341-3918'; // RechazarFactibilidadNodoWifi
        }        
        if(true === $this->get('security.context')->isGranted('ROLE_341-4417'))
        {
            $rolesPermitidos[] = 'ROLE_341-4417'; // Editar Factibilidad Wifi
        }        
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("320", "1");
        return $this->render('planificacionBundle:Factibilidad:indexNodoWifi.html.twig', array(
                            'item'              => $entityItemMenu,
                            'rolesPermitidos'   => $rolesPermitidos
        ));
    }

    /**
     *
     * ajaxConsultaPlanificacionNodoAction
     * Consulta las solicitudes de nodo wifi
     *
     * @return json con las solucitudes de nodo wifi
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 28-12-2015
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 10-10-2018 - Se agrega en el arrayParametros el idCanton y la fecha de inicio por defecto.
     *                           Adicional se aplica los estandares de calidad.
     * 
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.2 16-12-2019 - Se agrega el parametro $arrayParametros['serviceInfoServicio'] para poder hacer
     *                           un filtro en el grid de Factibilidad Nodo Wifi.
     * 
     */
    public function ajaxConsultaPlanificacionNodoAction()
    {
        ini_set('max_execution_time', 3000000);
        $objRespuesta     = new Response();
        $objPeticion      = $this->get('request');
        $strCodEmpresa    = ($objPeticion->getSession()->get('idEmpresa') ? $objPeticion->getSession()->get('idEmpresa') : "");
        $strFechaDesde    = explode('T', $objPeticion->get('fechaDesdePlanif'));
        $strFechaHasta    = explode('T', $objPeticion->get('fechaHastaPlanif'));
        $strIdCanton      = $objPeticion->get('idCanton');
        $strEstado        = $objPeticion->get('txtEstado');
        $strNombreNodo    = $objPeticion->get('txtNodo');
        $strLogin         = $objPeticion->get('txtLogin');
        $intStart         = $objPeticion->get('start');
        $intLimit         = $objPeticion->get('limit');
        $arrayParametros  = array();
        $strFechaIni      = '';
        $emComercial      = $this->getDoctrine()->getManager('telconet');
        $emGeneral        = $this->getDoctrine()->getManager('telconet_general');
        $objRespuesta->headers->set('Content-Type', 'text/json');

        $objSolicitud = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
            ->findOneBy(array('descripcionSolicitud' => 'SOLICITUD NODO WIFI',
                              'estado'               => 'Activo'));

        $arrayAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
            ->getOne('FECHA_INICIO_DEFECTO','','','','PLANIFICACION','','','','','');

        if (!empty($arrayAdmiParametroDet) && count($arrayAdmiParametroDet) > 0 && !empty($arrayAdmiParametroDet['valor2']))
        {
            $intCantidadDias = intval($arrayAdmiParametroDet['valor2']);

            if ($intCantidadDias > 0)
            {
                $objDateNow = new \DateTime('now');
                $objDateNow->modify("-$intCantidadDias day");
                $strFechaIni = date_format($objDateNow, 'Y-m-d');
            }
        }

        $arrayParametros["idSolicitud"]         = $objSolicitud->getId();
        $arrayParametros["codEmpresa"]          = $strCodEmpresa;
        $arrayParametros["estado"]              = $strEstado;
        $arrayParametros["nombreNodo"]          = $strNombreNodo;
        $arrayParametros["login"]               = $strLogin;
        $arrayParametros["start"]               = $intStart;
        $arrayParametros["limit"]               = $intLimit;
        $arrayParametros["search_fechaDesde"]   = $strFechaDesde[0];
        $arrayParametros["search_fechaHasta"]   = $strFechaHasta[0];
        $arrayParametros["intIdCanton"]         = $strIdCanton;
        $arrayParametros["strFechaIni"]         = $strFechaIni;
        $arrayParametros['serviceInfoServicio'] = $this->get('tecnico.infoserviciotecnico');

            $arraySolicitudes = $emComercial->getRepository('schemaBundle:InfoElemento')->getJsonFactibilidNodoWifi($arrayParametros);

        $objRespuesta->setContent($arraySolicitudes);

        return $objRespuesta;
    }

    /**
     * @Secure(roles="ROLE_341-3918")
     *
     * rechazarSolicitudNodoWifiAction
     * rechaza la solicitud de nodo wifi
     *
     * @return response con OK o con el ERROR
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 28-12-2015
     */
    public function rechazarSolicitudNodoWifiAction()
    {
        $respuesta      = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $peticion       = $this->get('request');
        $idSolicitud    = $peticion->get('idSolicitud');
        $idElemento     = $peticion->get('idElemento');
        $id_motivo      = $peticion->get('id_motivo');
        $observacion    = $peticion->get('observacion');
        $session        = $peticion->getSession();
        
        $emComercial = $this->getDoctrine()->getManager('telconet');
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');

        $objDetalleSolicitud = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->findOneById($idSolicitud);

        $emInfraestructura->getConnection()->beginTransaction();
        $emComercial->getConnection()->beginTransaction();

        try
        {
            if($objDetalleSolicitud)
            {
                $objServicio = $objDetalleSolicitud->getServicioId();
                
                if($objServicio)
                {
                    //cambio de estado a la info servicio 
                    $objServicio->setEstado('Rechazada');
                    $emComercial->persist($objServicio);
                    $emComercial->flush();

                    $objServicioHistorial = new InfoServicioHistorial();
                    $objServicioHistorial->setServicioId($objServicio);
                    $objServicioHistorial->setObservacion($observacion);
                    $objServicioHistorial->setUsrCreacion($session->get('user'));
                    $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                    $objServicioHistorial->setIpCreacion($peticion->getClientIp());
                    $objServicioHistorial->setEstado("Rechazada");
                    $emComercial->persist($objServicioHistorial);
                    $emComercial->flush();
                }

                //actualizo la solicitud
                $objDetalleSolicitud->setMotivoId($id_motivo);
                $objDetalleSolicitud->setObservacion($observacion);
                $objDetalleSolicitud->setUsrRechazo($session->get('user'));
                $objDetalleSolicitud->setFeRechazo(new \DateTime('now'));
                $objDetalleSolicitud->setEstado("Rechazada");
                $emComercial->persist($objDetalleSolicitud);
                $emComercial->flush();

                //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                $objDetalleSolHist = new InfoDetalleSolHist();
                $objDetalleSolHist->setDetalleSolicitudId($objDetalleSolicitud);
                $objDetalleSolHist->setIpCreacion($peticion->getClientIp());
                $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
                $objDetalleSolHist->setUsrCreacion($peticion->getSession()->get('user'));
                $objDetalleSolHist->setEstado('FactibilidadEnProceso');
                $objDetalleSolHist->setObservacion($observacion);
                $emComercial->persist($objDetalleSolHist);
                $emComercial->flush();

                if($idElemento)
                {                
                    //actualizo el elemento
                    $objElemento = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->findOneById($idElemento);
                    $objElemento->setObservacion('Nodo Wifi rechazada en la factibilidad. ' . $observacion);
                    $objElemento->setEstado("Rechazada");
                    $emInfraestructura->persist($objElemento);
                    $emInfraestructura->flush();

                    //Historial elemento
                    $objInfoHistorialElemento = new InfoHistorialElemento();
                    $objInfoHistorialElemento->setElementoId($objElemento);
                    $objInfoHistorialElemento->setObservacion('Nodo Wifi rechazada en la factibilidad');
                    $objInfoHistorialElemento->setFeCreacion(new \DateTime('now'));
                    $objInfoHistorialElemento->setUsrCreacion($session->get('user'));
                    $objInfoHistorialElemento->setIpCreacion($peticion->getClientIp());
                    $objInfoHistorialElemento->setEstadoElemento('Rechazada');
                    $emInfraestructura->persist($objInfoHistorialElemento);
                    $emInfraestructura->flush();
                }

                $respuesta->setContent("OK");
            }
            else
            {
                $respuesta->setContent("No existe la solicitud");
            }
            $emInfraestructura->getConnection()->commit();
            $emComercial->getConnection()->commit();
        }
        catch(Exception $e)
        {

            $emInfraestructura->getConnection()->rollback();
            $emComercial->getConnection()->rollback();
            $mensajeError = "Error: " . $e->getMessage();
            $respuesta->setContent($mensajeError);
        }

        return $respuesta;
    }

    /**
     * @Secure(roles="ROLE_341-3898")
     *
     * factibilidadPuntoClienteAction
     * Aprueba la solicitud de nodo wifi
     *
     * @return response con OK o con el ERROR
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 28-12-2015
     */
    public function relacionElementoAction()
    {
        $respuesta      = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $peticion       = $this->get('request');
        $session        = $peticion->getSession();
        $idSolicitud    = $peticion->get('idSolicitud');
        $idElementoB    = $peticion->get('idElemento');

        try
        {
            $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
            $objDetalleSolicitud = $emInfraestructura->getRepository('schemaBundle:InfoDetalleSolicitud')->find($idSolicitud);
            
            //verificar que el elemento tenga enlazado un elemento padre
            $objEnlace = $emInfraestructura->getRepository('schemaBundle:InfoEnlace')->getEnlacePadreElemento($idElementoB);
            
            if(!$objEnlace)
            {
                $respuesta->setContent('Debe crear el enlace padre a este elemento. ');
                return $respuesta;
            }
            else
            {
                $capacidadOutput = $objEnlace[0]['capacidadOutput'];
                if (!$capacidadOutput)
                {
                    $respuesta->setContent('El enlace padre no tiene capacidad output asignada. ');
                    return $respuesta;
                }
            }

            $emInfraestructura->getConnection()->beginTransaction();

            if($objDetalleSolicitud)
            {
                $idElementoA = $objDetalleSolicitud->getElementoId();
                $idServicio = $objDetalleSolicitud->getServicioId();
                if($idServicio)
                {
                    $estadoSolicitud = 'RelacionElemento';
                }
                else
                {
                    $estadoSolicitud = 'Finalizada';
                }

                //actualizo la solicitud
                $objDetalleSolicitud->setEstado($estadoSolicitud);
                $emInfraestructura->persist($objDetalleSolicitud);
                $emInfraestructura->flush();

                //GUARDAR INFO DETALLE SOLICITUD HISTORIAL
                $objDetalleSolHist = new InfoDetalleSolHist();
                $objDetalleSolHist->setDetalleSolicitudId($objDetalleSolicitud);
                $objDetalleSolHist->setIpCreacion($peticion->getClientIp());
                $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
                $objDetalleSolHist->setUsrCreacion($peticion->getSession()->get('user'));
                $objDetalleSolHist->setEstado($estadoSolicitud);
                $emInfraestructura->persist($objDetalleSolHist);
                $emInfraestructura->flush();

                //actualizo el elemento
                $objElemento = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->findOneById($idElementoA);
                $objElemento->setObservacion('Nodo wifi aprobada en la factibilidad por ' . $session->get('user'));
                $objElemento->setEstado("Activo");
                $emInfraestructura->persist($objDetalleSolicitud);
                $emInfraestructura->flush();

                //Historial elemento
                $objInfoHistorialElemento = new InfoHistorialElemento();
                $objInfoHistorialElemento->setElementoId($objElemento);
                $objInfoHistorialElemento->setObservacion('Nodo wifi aprobada en la factibilidad');
                $objInfoHistorialElemento->setFeCreacion(new \DateTime('now'));
                $objInfoHistorialElemento->setUsrCreacion($session->get('user'));
                $objInfoHistorialElemento->setIpCreacion($peticion->getClientIp());
                $objInfoHistorialElemento->setEstadoElemento('Activo');
                $emInfraestructura->persist($objInfoHistorialElemento);
                $emInfraestructura->flush();
                
                //relacion elemento
                $objRelacionElemento = new InfoRelacionElemento();
                $objRelacionElemento->setElementoIdA($idElementoA);
                $objRelacionElemento->setElementoIdB($idElementoB);
                $objRelacionElemento->setTipoRelacion("CONTIENE");
                $objRelacionElemento->setObservacion("Nodo Wifi contiene Router");
                $objRelacionElemento->setEstado("Activo");
                $objRelacionElemento->setUsrCreacion($session->get('user'));
                $objRelacionElemento->setFeCreacion(new \DateTime('now'));
                $objRelacionElemento->setIpCreacion($peticion->getClientIp());
                $emInfraestructura->persist($objRelacionElemento);
                $emInfraestructura->flush();

                $respuesta->setContent("OK");
            }
            else
            {
                $respuesta->setContent("No existe solicitud");
            }

            $emInfraestructura->getConnection()->commit();
        }
        catch(Exception $e)
        {
            $emInfraestructura->getConnection()->rollback();
            $mensajeError = "Error: " . $e->getMessage();
            $respuesta->setContent($mensajeError);
        }

        return $respuesta;
    }

    /**
     * @Secure(roles="ROLE_341-3897")
     *
     * fechaFactibilidadAction
     * Actualiza la fecha cuando se realizará la instalación del nodo wifi y crea el elemento
     *
     * @return response con OK o con el ERROR
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 28-12-2015
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 17-09-2018 - Se agrega la validación de las coordenadas de latitud y longitud de acuerdo al país en sesión
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.2 13-03-2019 - Se agrega característica para asignar tarea a IPCCL2 cuando se
     * ingrese un nuevo nodo WIFI y además se guarde una referencia a la tarea en el historial del servicio.
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.3 14-04-2019 - Se agrega funcionalidad para que dependiendo del tipo de esquema se generé la tarea
     * al departamento de RADIO para esquema 1 y al departamento de IPCCL2 para esquema 2.
     *
     * @throws Exception
     *
     */
    public function fechaFactibilidadNuevoElementoAction()
    {
        $respuesta              = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $session                = $this->get('request')->getSession();
        $peticion               = $this->get('request');
        $idSolicitud            = $peticion->get('idSolicitud');
        $intIdPunto             = $peticion->get('idPunto');
        $strNombreProducto      = $peticion->get('producto');
        $strNombreDepartamento  = 'IPCCL2';
        $strNombreProceso       = 'TAREAS DE IPCCL2 - NODO WIFI';
        $observacion            = $peticion->get('observacion');
        $fechaProgramacion      = explode('T', $peticion->get('fechaProgramacion'));
        $dateF                  = explode("-", $fechaProgramacion[0]);
        $fechaCreacionTramo     = new \DateTime(date("Y/m/d G:i:s", strtotime($dateF[2] . "-" . $dateF[1] . "-" . $dateF[0])));
        $nombreElemento         = strtoupper($peticion->get('nombreElemento'));
        $parroquiaId            = $peticion->get('idParroquia');
        $alturaSnm              = 0;
        $longitudUbicacion      = $peticion->get('longitud');
        $latitudUbicacion       = $peticion->get('latitud');
        $direccionUbicacion     = $peticion->get('direccion');
        $descripcionElemento    = $peticion->get('descripcion');
        $idModeloElemento       = $peticion->get('tipoElemento');
        $intTipoEsquema         = $peticion->get('tipoEsquema');
        $objGenerarTarea        = $this->get('soporte.soporteservice');
        $strObservacion         = "<b>Tarea Automática:</b><br/>Activación de nuevo nodo Wifi<br/>";
        $emInfraestructura      = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emComercial            = $this->getDoctrine()->getManager();
        $emGeneral              = $this->getDoctrine()->getManager();
        $emInfraestructura->beginTransaction();
        $emComercial->beginTransaction();


        // Si existe la variable de tipo esquema, se agrega el tag para la observación de la tarea.
        if ($intTipoEsquema)
        {
            $strObservacion .= "<b>Tipo Esquema: </b>$intTipoEsquema<br/>";
            $strNombreDepartamento  = $intTipoEsquema == 1 ? 'RADIO' : 'IPCCL2';
            $strNombreProceso = $intTipoEsquema == 1 ? 'TAREAS DE RADIOENLACE WIFI - NODO WIFI': 'TAREAS DE IPCCL2 - NODO WIFI';
        }

        try
        {
            $objDetalleSolicitud = $emInfraestructura->getRepository('schemaBundle:InfoDetalleSolicitud')->find($idSolicitud);
            if($objDetalleSolicitud)
            {

                $modeloElemento = $emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')->find($idModeloElemento);

                //verificar que el nombre del elemento no se repita
                $elementoRepetido = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                       ->findOneBy(array("nombreElemento"   => $nombreElemento,
                                                         "estado"           => array("Activo", "Pendiente", "Factible", "PreFactibilidad"),
                                                         "modeloElementoId" => $modeloElemento->getId()));

                if(!$elementoRepetido)
                {
                    //actualizo el estado del servicio y creo el historial
                    //cambio de estado a la info servicio Factible e historial
                    $objServicio =  $emComercial->getRepository('schemaBundle:InfoServicio')->find($objDetalleSolicitud->getServicioId()->getId());

                    $objServicio->setEstado('FactibilidadEnProceso');
                    $emComercial->persist($objServicio);
                    $emComercial->flush();

                    $objElemento = new InfoElemento();
                    $objElemento->setNombreElemento($nombreElemento);
                    $objElemento->setDescripcionElemento($descripcionElemento);
                    $objElemento->setModeloElementoId($modeloElemento);
                    $objElemento->setUsrResponsable($session->get('user'));
                    $objElemento->setUsrCreacion($session->get('user'));
                    $objElemento->setFeCreacion(new \DateTime('now'));
                    $objElemento->setIpCreacion($peticion->getClientIp());
                    $objElemento->setEstado("Pendiente");

                    $emInfraestructura->persist($objElemento);
                    $emInfraestructura->flush();

                    //historial elemento
                    $objHistorialElemento = new InfoHistorialElemento();
                    $objHistorialElemento->setElementoId($objElemento);
                    $objHistorialElemento->setEstadoElemento("Pendiente");
                    $objHistorialElemento->setObservacion("Se ingreso un Nodo Wifi");
                    $objHistorialElemento->setUsrCreacion($session->get('user'));
                    $objHistorialElemento->setFeCreacion(new \DateTime('now'));
                    $objHistorialElemento->setIpCreacion($peticion->getClientIp());
                    $emInfraestructura->persist($objHistorialElemento);
                    $emInfraestructura->flush();

                    $serviceInfoElemento        = $this->get("tecnico.InfoElemento");
                    $arrayRespuestaCoordenadas  = $serviceInfoElemento->validarLimitesCoordenadasElemento(array("latitudElemento"   =>
                                                                                                                $latitudUbicacion,
                                                                                                                "longitudElemento"  =>
                                                                                                                $longitudUbicacion,
                                                                                                                "msjTipoElemento"   =>
                                                                                                                "del nodo wifi "
                                                                                                         ));
                    if($arrayRespuestaCoordenadas["status"] === "ERROR")
                    {
                        throw new Exception($arrayRespuestaCoordenadas['mensaje']);
                    }

                    //info ubicacion
                    $parroquia = $emInfraestructura->find('schemaBundle:AdmiParroquia', $parroquiaId);
                    $objUbicacionElemento = new InfoUbicacion();
                    $objUbicacionElemento->setLatitudUbicacion($latitudUbicacion);
                    $objUbicacionElemento->setLongitudUbicacion($longitudUbicacion);
                    $objUbicacionElemento->setDireccionUbicacion($direccionUbicacion);
                    $objUbicacionElemento->setAlturaSnm($alturaSnm);
                    $objUbicacionElemento->setParroquiaId($parroquia);
                    $objUbicacionElemento->setUsrCreacion($session->get('user'));
                    $objUbicacionElemento->setFeCreacion(new \DateTime('now'));
                    $objUbicacionElemento->setIpCreacion($peticion->getClientIp());
                    $emInfraestructura->persist($objUbicacionElemento);
                    $emInfraestructura->flush();

                    //empresa elemento ubicacion
                    $objEmpresaElementoUbica = new InfoEmpresaElementoUbica();
                    $objEmpresaElementoUbica->setEmpresaCod($session->get('idEmpresa'));
                    $objEmpresaElementoUbica->setElementoId($objElemento);
                    $objEmpresaElementoUbica->setUbicacionId($objUbicacionElemento);
                    $objEmpresaElementoUbica->setUsrCreacion($session->get('user'));
                    $objEmpresaElementoUbica->setFeCreacion(new \DateTime('now'));
                    $objEmpresaElementoUbica->setIpCreacion($peticion->getClientIp());
                    $emInfraestructura->persist($objEmpresaElementoUbica);
                    $emInfraestructura->flush();

                    //empresa elemento
                    $objEmpresaElemento = new InfoEmpresaElemento();
                    $objEmpresaElemento->setElementoId($objElemento);
                    $objEmpresaElemento->setEmpresaCod($session->get('idEmpresa'));
                    $objEmpresaElemento->setEstado("Activo");
                    $objEmpresaElemento->setUsrCreacion($session->get('user'));
                    $objEmpresaElemento->setIpCreacion($peticion->getClientIp());
                    $objEmpresaElemento->setFeCreacion(new \DateTime('now'));
                    $emInfraestructura->persist($objEmpresaElemento);
                    $emInfraestructura->flush();

                    //GUARDA INFO DETALLE SOLICITUD
                    $objDetalleSolicitud->setObservacion($observacion);
                    $objDetalleSolicitud->setFeEjecucion($fechaCreacionTramo);
                    $objDetalleSolicitud->setEstado("FactibilidadEnProceso");
                    $objDetalleSolicitud->setElementoId($objElemento->getId());
                    $emInfraestructura->persist($objDetalleSolicitud);
                    $emInfraestructura->flush();


                    //GUARDAR INFO DETALLE SOLICITUD HISTORIAL
                    $objDetalleSolHist = new InfoDetalleSolHist();
                    $objDetalleSolHist->setDetalleSolicitudId($objDetalleSolicitud);
                    $objDetalleSolHist->setIpCreacion($peticion->getClientIp());
                    $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
                    $objDetalleSolHist->setUsrCreacion($session->get('user'));
                    $objDetalleSolHist->setEstado('FactibilidadEnProceso');
                    $objDetalleSolHist->setObservacion($observacion);
                    $emInfraestructura->persist($objDetalleSolHist);
                    $emInfraestructura->flush();

                    // Obtengo información importante para poder pasarla al servicio de creación de tarea.
                    $entityPunto            = $emComercial->getRepository('schemaBundle:InfoPunto')
                                                        ->findOneBy(array('id'=>$intIdPunto));
                    $entitySector           = $emComercial->getRepository('schemaBundle:AdmiSector')
                                                        ->findOneBy(array('id'=> $entityPunto->getSectorId()));
                    $entityParroquia        = $emComercial->getRepository('schemaBundle:AdmiParroquia')
                                                        ->findOneBy(array('id'=>$entitySector->getParroquiaId()));
                    $entityCanton           = $emComercial->getRepository('schemaBundle:AdmiCanton')
                                                        ->findOneBy(array('id'=>$entityParroquia->getCantonId()));
                    $objProceso             = $emGeneral->getRepository('schemaBundle:AdmiProceso')
                                                        ->findOneBy(array('nombreProceso'=>$strNombreProceso));
                    $objTarea               = $emGeneral->getRepository('schemaBundle:AdmiTarea')
                                                        ->findOneBy(array(
                                                            'nombreTarea'   =>  'ACTIVACION NODO WIFI',
                                                            'procesoId'     =>  $objProceso->getId()
                                                        ));
                    $objCreationUser        = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                            ->findOneBy(array('login'=>$session->get('user')));
                    $entityInfoPerEmpRol    = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                        ->findOneBy(array(  'id'              =>  $entityPunto->getPersonaEmpresaRolId(),
                                                                            'empresaRolId'    =>  1,
                                                                            'estado'          =>  'Activo'));
                    $entityCliente          = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                       ->findOneBy(array('id'=>$entityInfoPerEmpRol->getPersonaId()));
                    $objInfoDetSol          = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                        ->findOneBy(array('servicioId'=>$objServicio->getId()));


                    $arrayParams = array(
                        'strIdEmpresa'          => $session->get('idEmpresa'),
                        'strPrefijoEmpresa'     => $session->get('prefijoEmpresa'),
                        'strNombreTarea'        => $objTarea->getNombreTarea(),
                        'strObservacion'        => $strObservacion,
                        'strNombreDepartamento' => $strNombreDepartamento,
                        'strCiudad'             => $entityCanton->getNombreCanton(),
                        'strEmpleado'           => $objCreationUser->getNombres().' '.$objCreationUser->getApellidos() ,
                        'strUsrCreacion'        => $objCreationUser->getLogin(),
                        'strIp'                 => $this->get('request')->getClientIp(),
                        'strOrigen'             => 'WEB-TN',
                        'strLogin'              => $entityPunto->getLogin(),
                        'intPuntoId'            => $entityPunto->getId(),
                        'strNombreCliente'      => $entityCliente->getNombres() ? $entityCliente->getNombres().' '.$entityCliente->getApellidos() :
                                                    $entityCliente->getRazonSocial(),
                        'objDetalleSolicitud'   => $objInfoDetSol,
                        'strRegion'             => $entityCanton->getRegion(),
                        'strValidacionTags'     => 'NO'
                    );

                    $strNumeroTarea = $objGenerarTarea->ingresarTareaInterna($arrayParams);

                    if ($strNumeroTarea['status'] == 'OK')
                    {
                        // Se agrega numero de tarea en el historial del servicio.
                        $objServicioHistorial = new InfoServicioHistorial();
                        $objServicioHistorial->setServicioId($objServicio);
                        $objServicioHistorial->setObservacion("<b>Se generó la tarea automática: </b> #". $strNumeroTarea['id'] ." para la creación de un Nodo Wifi<br/>");
                        $objServicioHistorial->setUsrCreacion($session->get('user'));
                        $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                        $objServicioHistorial->setIpCreacion($peticion->getClientIp());
                        $objServicioHistorial->setEstado("FactibilidadEnProceso");
                        $emComercial->persist($objServicioHistorial);
                        $emComercial->flush();
                        $emComercial->commit();
                    }else
                    {
                        throw new Exception($strNumeroTarea['mensaje']);
                    }

                    $emInfraestructura->commit();
                    $respuesta->setContent("OK");

                    /*Si el producto es Internet Wifi, se reemplaza el contenido de la respuesta, para poder retornar
                    el id de la tarea generada.*/
                    if($strNombreProducto == 'INTERNET WIFI')
                    {
                        $respuesta->setContent(json_encode($strNumeroTarea));
                    }
                }
                else
                {
                    $respuesta->setContent(" El nombre ya existe en otro Elemento con estado ".$elementoRepetido->getestado());
                }

            }
            else
                $respuesta->setContent("No existe Solicitud");

        }
        catch(Exception $e)
        {
            if ($emInfraestructura->getConnection()->isTransactionActive())
            {
                $emInfraestructura->rollback();
            }
            $emInfraestructura->close();
            
            if ($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->rollback();
            }
            $emComercial->close();
            $respuesta->setContent("Error: " . $e->getMessage());
        }

        return $respuesta;
    }

    
     /**
     *
     * 
     * fechaFactibilidadAction
     * Actualiza la fecha cuando se realizará la instalación del nodo wifi
     *
     * @return response con OK o con el ERROR
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 28-12-2015
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.1 27-06-2016  validacion cuando un nodo wifi es BAKCBONE o CLIENTE
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.2 29-06-2016  La interface del router  wifi se actualiza a estado ocupada
     *                      Ingreso de buffer e hilos cuando se realiza el enlace entre el odf y el router wifi
     *                      El enlace ahora se lo realiza entre el odf y el router wifi
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.3 27-06-2016  nueva funcion para que se pueda editar un nodo wifi
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.4 12-07-2016  se incluye estado 'finalizada' para la edicion
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.5 31-08-2016  activacion de servicio wifi con web service de networking
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.6 15-09-2016 se utiliza el producto del servicio
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.7 21-09-2016 se consulta la interface del sw antes de hacer actualizaciones
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.8 04-10-2016 Se agregó que considere el servicio de navegación en estado En Pruebas, y si no se puede enlazar enviará una alerta
      * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.9 18-10-2016 Se agregó notificación cuando se da factibilidad correctamente
     * 
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 2.0 01-04-2019 Se agregó funcionalidad para cerrar tarea automática de "Activación nuevo nodo Wifi".
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 2.1 23-05-2019 - Se agrega funcionalidad para que cuando un servicio sea INTERNET WIFI con ESQUEMA 2 se llame
     *                          a la funcion generarInstalacionWifiL2, y asi se genere la instalacion evitando que el proceso
     *                          retorne a GIS.
     * 
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 2.2 07-09-2020 - Se modifica funcionalidad para que cuando se edite la factibilidad se guarde en el historial con el 
     *                          estado del servicio, no con el estado de la solicitud.                       
     * 
     */
    public function asignarFactibilidadAction()
    { 
        $respuesta              = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $session                = $this->get('request')->getSession();
        $peticion               = $this->get('request');
        $objBufferHilo          = '';
        $modeloNodoWifi         = $peticion->get('modeloNodoWifi');
        $idSolicitud            = $peticion->get('idSolicitud');
        $floatMetraje           = $peticion->get('floatMetraje');
        $strObservacion         = $peticion->get('strObservacion');
        $intEmpresa             = $session->get('idEmpresa');
        $boolFactibilidadL2     = $peticion->get('factibilidadIPCCL2');
        $strEnvioCorreo         = "SI";
        $strUsuario             = $session->get('user');
        $objSoporteService      = $this->get('soporte.SoporteService');
        //verifico permisos


        if(true === $this->get('security.context')->isGranted('ROLE_341-4417') ||
            $boolFactibilidadL2)
        {
            if($modeloNodoWifi == 'BACKBONE')
            {
                $idElementoConector         = $peticion->get('idCasette');
                $idInterfaceElementoConector= $peticion->get('idInterfaceCasette');
                $idElementoContenedor       = $peticion->get('intIdElementoCaja');
                $idElemento                 = $peticion->get('idElementoWifi');
                $idInterfaceElemento        = $peticion->get('idInterfaceElementoWifi');
                $idInterfaceOdf             = $peticion->get('intInterfaceOdf');
            }
            else
            {
                $idElementoConector             = $peticion->get('idElementoWifi');
                $idInterfaceElementoConector    = $peticion->get('idInterfaceElementoWifi');
                $idElementoContenedor           = $peticion->get('idNodoWifi');
            }
            $observacion            = 'Flujo de factibilidad de nodo wifi';

            $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
            $emComercial = $this->getDoctrine()->getManager('telconet');
            $serviceServicioGeneral = $this->get('tecnico.InfoServicioTecnico');
            $emComunicacion = $this->getDoctrine()->getManager('telconet_comunicacion');
            $emInfraestructura->getConnection()->beginTransaction();
            $emComercial->getConnection()->beginTransaction();
            $objDetalleSolicitud = $emInfraestructura->getRepository('schemaBundle:InfoDetalleSolicitud')->find($idSolicitud);

            try
            {
                if($objDetalleSolicitud)
                {
                    $objInterfaceElemento = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                              ->findOneById($idInterfaceElementoConector);
                    $idServicio = $objDetalleSolicitud->getServicioId()->getId();
                    $objServicio = $emComercial->getRepository('schemaBundle:InfoServicio')->find($idServicio);
                    $objProducto = $objServicio->getProductoId();
                    $objServicioTecnico = $emInfraestructura->getRepository('schemaBundle:InfoServicioTecnico')
                                                            ->findOneByServicioId($objServicio->getId());
                    if($idElemento)
                    {
                        //obtengo el sw de backbone y lo relaciono al odf
                        $idInterfaceElementoSw = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                   ->getInterfaceElementoPadre($idElemento, 'ELEMENTO', 'SWITCH');
                    }
                    $observacionEdicion = '';
                    //si la solicitud es factible s xq se va a editar, se debe reversar
                    if($objDetalleSolicitud->getEstado() == 'Factible' || $objDetalleSolicitud->getEstado() == 'Finalizada')
                    {
                        if($objServicio->getEstado() == 'Factible' || $objServicio->getEstado() == 'AsignadoTarea')
                        {
                            $strEnvioCorreo = "NO";
                            $observacionEdicion = 'Edición de ';
                            $objSpc = $serviceServicioGeneral->getServicioProductoCaracteristica($objServicio, "INTERFACE_ELEMENTO_ID", $objProducto);

                            $objSpc->setEstado("Eliminado");
                            $emComercial->persist($objSpc);
                            $emComercial->flush();

                            if($objSpc)
                            {
                                //eliminamos el enlace
                                $objEnlaceEdit = $emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                                   ->findOneBy(array('interfaceElementoIniId' => $objSpc->getValor(),
                                                                                      'estado'                 => 'Activo'));

                                if($objEnlaceEdit)
                                {
                                    $objInterfaceIni = $objEnlaceEdit->getInterfaceElementoIniId();
                                    $objInterfaceFin = $objEnlaceEdit->getInterfaceElementoFinId();
                                    $objInterfaceConectorEdit = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                                  ->find($objServicioTecnico->getInterfaceElementoConectorId());

                                    $objEnlaceEdit->setEstado("Eliminado");
                                    $emInfraestructura->persist($objEnlaceEdit);
                                    $emInfraestructura->flush();

                                    $objInterfaceIni->setEstado("not connect");
                                    $emInfraestructura->persist($objInterfaceIni);
                                    $emInfraestructura->flush();                                

                                    $objInterfaceFin->setEstado("not connect");
                                    $emInfraestructura->persist($objInterfaceFin);
                                    $emInfraestructura->flush();

                                    //se debe liberar la interface del elemento conector
                                    $objInterfaceConectorEdit->setEstado("not connect");
                                    $emInfraestructura->persist($objInterfaceConectorEdit);
                                    $emInfraestructura->flush();

                                }
                                else
                                {
                                    //no tiene enlace pero si debo reversar el puerto del router wifi
                                    $objInterfaceElementoEdit = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                                  ->find($objSpc->getValor());

                                    $objInterfaceElementoEdit->setEstado('not connect');
                                    $emInfraestructura->persist($objInterfaceElementoEdit);
                                    $emInfraestructura->flush();                            
                                }
                            }
                        }
                        else
                        {
                            $respuesta->setContent('No se puede editar factibilidad porque el servicio está en estado '.$objServicio->getEstado());
                            return $respuesta;
                        }
                    }                
                    else
                    {
                        //cambio de estado a la info servicio Factible e historial
                        $objServicio->setEstado('Factible');
                        $emComercial->persist($objServicio);
                        $emComercial->flush();

                        //actualizo la solicitud de factiblidad a finalizada e historial
                        //GUARDA INFO DETALLE SOLICITUD
                        $objDetalleSolicitud->setObservacion($observacion);
                        $objDetalleSolicitud->setFeEjecucion(new \DateTime('now'));
                        $objDetalleSolicitud->setEstado("Factible");
                        $emInfraestructura->persist($objDetalleSolicitud);
                        $emInfraestructura->flush();

                        //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                        $objDetalleSolHist = new InfoDetalleSolHist();
                        $objDetalleSolHist->setDetalleSolicitudId($objDetalleSolicitud);
                        $objDetalleSolHist->setIpCreacion($peticion->getClientIp());
                        $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
                        $objDetalleSolHist->setUsrCreacion($session->get('user'));
                        $objDetalleSolHist->setEstado('Factible');
                        $objDetalleSolHist->setObservacion($observacion);
                        $emInfraestructura->persist($objDetalleSolHist);
                        $emInfraestructura->flush();
                    }
                    //actualizo el puerto de la interface a connected

                    $objInterfaceElemento->setEstado('connected');
                    $emInfraestructura->persist($objInterfaceElemento);
                    $emInfraestructura->flush();

                    //creo el enlace de datos
                    $objdetalleElemento = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                            ->findOneBy(array('detalleNombre'=> "ID_PUNTO",
                                                                              "elementoId"   => $peticion->get('idNodoWifi'),
                                                                              'estado'       => 'Activo'));
                    if($objdetalleElemento)
                    {
                        $objServicioNavega = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                         ->findOneBy(array('puntoId'                    => $objdetalleElemento->getDetalleValor(),
                                                                           'descripcionPresentaFactura' => 'Concentrador L3MPLS Navegacion',
                                                                           'estado'                     => array('Activo','EnPruebas')));
                        if($objServicioNavega)
                        {
                            $serviceServicioGeneral->ingresarServicioProductoCaracteristica($objServicio,
                                                                                            $objProducto,
                                                                                            "ENLACE_DATOS",
                                                                                            $objServicioNavega->getId(),
                                                                                            $session->get('user'));
                        }
                        else
                        {
                            $respuesta->setContent("No se encuentra el servicio Concentrador L3MPLS Navegacion del nodo Wifi.");
                            return $respuesta;
                        }
                    }
                    
                    if($modeloNodoWifi == 'BACKBONE')
                    {
                        //obtengo el IN del odf
                        $objEnlaceOdfIn =  $emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                             ->findOneBy(array('interfaceElementoFinId'=> $idInterfaceOdf,
                                                                               'estado'                => 'Activo'));
                        if($objEnlaceOdfIn)
                        {
                            $objInterfaceOdf = $objEnlaceOdfIn->getinterfaceElementoIniId();
                        }

                        //actualizo a ocupado la interface del elemento wifi
                        $objInterfaceElementoWifi = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                      ->findOneById($idInterfaceElemento);
                        $objInterfaceElementoWifi->setEstado('connected');
                        $emInfraestructura->persist($objInterfaceElementoWifi);
                        $emInfraestructura->flush();

                        //guardo el elemento wifi como servicio prod caract                    
                        $serviceServicioGeneral->ingresarServicioProductoCaracteristica($objServicio, $objProducto, "INTERFACE_ELEMENTO_ID", 
                                                                                        $idInterfaceElemento, $session->get('user'));

                        if($idInterfaceElementoSw)
                        {
                            $idInterfaceElemento = $idInterfaceElementoSw;
                            $objInterfaceElemento = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                        ->find($idInterfaceElemento);
                            $idElemento = $objInterfaceElemento->getElementoId()->getId();
                            $nombreElemento = $objInterfaceElemento->getElementoId()->getNombreElemento();
                            $nombreInterfaceElemento = $objInterfaceElemento->getNombreInterfaceElemento();
                        }
                        else
                        {
                            $respuesta->setContent("El elemento no está enlazado a un Switch de Backbone, favor crear la relación. ");
                            return $respuesta;
                        }

                        if($objInterfaceElemento)
                        {
                            $objAdmiTipoMedio   = $emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')
                                                                    ->findOneByNombreTipoMedio('Fibra Optica');

                            $arrayBufferHilo= $emInfraestructura->getRepository('schemaBundle:InfoBufferHilo')
                                                                ->getBufferHiloBy('ROJO', 'TRANSPARENTE', 'FIBRA DE 1 HILO', 10);
                            $idHiloBuffer = $arrayBufferHilo['registros'][0]['idBufferHilo'];

                            if($idHiloBuffer)
                            {
                                $objBufferHilo= $emInfraestructura->getRepository('schemaBundle:InfoBufferHilo')->find($idHiloBuffer);
                            }
                            //creo el enlace del router wifi con el odf
                            $enlace  = new InfoEnlace();
                            $enlace->setInterfaceElementoIniId($objInterfaceElementoWifi);
                            $enlace->setInterfaceElementoFinId($objInterfaceOdf);
                            $enlace->setTipoMedioId($objAdmiTipoMedio);
                            $enlace->setTipoEnlace("PRINCIPAL");
                            $enlace->setEstado("Activo");
                            $enlace->setBufferId($objBufferHilo);
                            $enlace->setUsrCreacion($session->get('user'));
                            $enlace->setFeCreacion(new \DateTime('now'));
                            $enlace->setIpCreacion($peticion->getClientIp());
                            $emInfraestructura->persist($enlace);
                            $emInfraestructura->flush();

                            $objInterfaceOdf->setEstado('connected');
                            $emInfraestructura->persist($objInterfaceOdf);
                            $emInfraestructura->flush();

                            $objInterfaceElemento->setEstado('connected');
                            $emInfraestructura->persist($objInterfaceElemento);
                            $emInfraestructura->flush();                        
                        }

                    }
                    else
                    {
                        //obtenego el id elemento del element padre
                        $idInterfaceElemento = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                 ->getInterfaceElementoPadre($idElementoConector, 'ELEMENTO', 'SWITCH');
                        //consulto del servicio navegacion del nodo wifi el elemento y le seteo
                        if(!$idInterfaceElemento)
                        {
                            if($objServicioNavega)
                            {
                                  $objServicioTecnicoNavega = $emInfraestructura->getRepository('schemaBundle:InfoServicioTecnico')
                                                                                ->findOneByServicioId($objServicioNavega->getId());

                                  if($objServicioTecnicoNavega)
                                  {
                                      $idInterfaceElemento = $objServicioTecnicoNavega->getInterfaceElementoId();
                                  }
                            }
                        }
                        
                        if($idInterfaceElemento)
                        {
                            $objInterfaceElemento = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                      ->findOneById($idInterfaceElemento);
                            if($objInterfaceElemento)
                            {
                                $idElemento = $objInterfaceElemento->getElementoId()->getId();
                                $nombreElemento = $objInterfaceElemento->getElementoId()->getNombreElemento();
                                $nombreInterfaceElemento = $objInterfaceElemento->getNombreInterfaceElemento();
                            }
                        }
                        else
                        {
                            $respuesta->setContent("No se encuentra la relación con el Switch de Backbone, favor crear la relación. ");
                            return $respuesta;
                        }
                        //guardo la interface del elemetno wifi
                        $serviceServicioGeneral->ingresarServicioProductoCaracteristica($objServicio, $objProducto, "INTERFACE_ELEMENTO_ID", 
                                                                                        $idInterfaceElementoConector, $session->get('user'));  
                    }
                    $objElementoInterfaceConector = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                      ->find($idInterfaceElementoConector);
                    if($objElementoInterfaceConector)
                    {
                        $nombreElementoConector = $objElementoInterfaceConector->getElementoId()->getNombreElemento();
                        $nombreInterfaceElementoConector = $objElementoInterfaceConector->getNombreInterfaceElemento();
                    }


                    $objServicioTecnico->setElementoId($idElemento);
                    $objServicioTecnico->setInterfaceElementoId($idInterfaceElemento);
                    $objServicioTecnico->setElementoConectorId($idElementoConector);
                    $objServicioTecnico->setInterfaceElementoConectorId($idInterfaceElementoConector);
                    $objServicioTecnico->setElementoContenedorId($idElementoContenedor);
                    $emInfraestructura->persist($objServicioTecnico);
                    $emInfraestructura->flush();

                    $objServicioHistorial = new InfoServicioHistorial();
                    $objServicioHistorial->setServicioId($objServicio);
                    $objServicioHistorial->setObservacion($observacionEdicion.'Factibilidad:<br> Elemento: '.$nombreElemento
                                                          .' <br> Puerto: '.$nombreInterfaceElemento.''
                                                          . ' <br> Elemento Conector: '.$nombreElementoConector
                                                          .'<br> Puerto: '.$nombreInterfaceElementoConector
                                                          .' <br> Observación: '.$strObservacion);
                    $objServicioHistorial->setUsrCreacion($session->get('user'));
                    $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                    $objServicioHistorial->setIpCreacion($peticion->getClientIp());
                    $objServicioHistorial->setEstado($objServicio->getEstado());
                    $emComercial->persist($objServicioHistorial);
                    $emComercial->flush();

                    $serviceServicioGeneral->ingresarServicioProductoCaracteristica($objServicio, $objProducto, "METRAJE FACTIBILIDAD", $floatMetraje,
                                                                                    $session->get('user'));

                    if($strEnvioCorreo == "SI")
                    {
                        /* Generación del envío de correo al aprobar la solicitud de Factibilidad
                         * Se obtiene el vendedor del servicio para agregarlo como destinatario del correo que se enviará al aprobar
                         * la factibilidad
                         */
                        $strAsunto ="Aprobación de Solicitud de Factibilidad de Instalación #".$objDetalleSolicitud->getId();
                        $arrayTo     = array();

                        if($objServicio->getUsrVendedor())
                        {
                            // DESTINATARIOS....
                            $arrayFormasContacto = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                               ->getContactosByLoginPersonaAndFormaContacto($objServicio->getUsrVendedor(),
                                                                                                            'Correo Electronico');
                            if($arrayFormasContacto)
                            {
                                foreach($arrayFormasContacto as $arrayFormaContacto)
                                {
                                    $arrayTo[] = $arrayFormaContacto['valor'];
                                }
                            }
                        }

                        /* Envío de correo por medio de plantillas
                         * Se obtiene la plantilla y se invoca a la función generarEnvioPlantilla del service que internamente obtiene
                         * los alias asociados a la plantilla 'APROBAR_FACTIB' y envía el respectivo correo
                         */
                        /* @var $envioPlantilla EnvioPlantilla */
                        $arrayParametros        = array('detalleSolicitud' => $objDetalleSolicitud,'usrAprueba'=>$session->get('user'));
                        $envioPlantillaService  = $this->get('soporte.EnvioPlantilla');
                        $envioPlantillaService->generarEnvioPlantilla( $strAsunto,
                                                                       $arrayTo,
                                                                       'APROBAR_FACTIB',
                                                                       $arrayParametros,
                                                                       $intEmpresa,
                                                                       '',
                                                                       '',
                                                                       null,
                                                                       true,
                                                                       'notificaciones_telcos@telconet.ec');
                    }

                    $emComercial->getConnection()->commit();
                    $emInfraestructura->getConnection()->commit();

                    /*
                     * Obtengo todos los parámetros necesarios para poder llamar el servicio de cierre de tarea automática
                    */
                    $objUsuario             = $emComercial->getRepository('schemaBundle:InfoPersona')
                                            ->getPersonaDepartamentoPorUserEmpresa($strUsuario, 10);

                    $objInfoDetalleSol      = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                        ->findOneBy(array(  'servicioId'        =>  $objServicio->getId(),
                                            'tipoSolicitudId'   =>  128));

                    // Validacion de que exista el objeto y contenga el método.
                    if (is_object($objInfoDetalleSol) && method_exists($objInfoDetalleSol, 'getId'))
                    {
                        $objInfoDetalle     = $emComercial->getRepository('schemaBundle:InfoDetalle')
                                            ->findOneBy(array('detalleSolicitudId'=>$objInfoDetalleSol->getId()));

                        // Validacion de que exista el objeto y contenga el método.
                        if (is_object($objInfoDetalle) && method_exists($objInfoDetalle, 'getId'))
                        {
                            $objInfoComunicacion       = $emComunicacion->getRepository('schemaBundle:InfoComunicacion')
                                                        ->findOneBy(array('detalleId'=>$objInfoDetalle->getId()));

                            $arrayParametrosFecha   = array(
                                'fechaInicio'   => $objInfoDetalle->getFeCreacion()->format('d-m-Y'),
                                'horaInicio'    => $objInfoDetalle->getFeCreacion()->format('H:i')
                            );

                            $arrayTiempoServer      = $objSoporteService->obtenerHoraTiempoTranscurrido($arrayParametrosFecha);

                            // Validacion de que exista el objeto y contenga el método.
                            if (is_object($objInfoComunicacion) && method_exists($objInfoComunicacion, 'getId'))
                            {
                                // Se establecen todos los parametros necesarios.
                                $arrayParametros            = array(
                                    'idEmpresa'             => 10,
                                    'prefijoEmpresa'        => "TN",
                                    'idDetalle'             => $objInfoComunicacion->getDetalleId(),
                                    'tarea'                 => $objInfoDetalle->getTareaId()->getId(),
                                    'tiempoTotal'           => $arrayTiempoServer['tiempoTotal'],
                                    'fechaCierre'           => $arrayTiempoServer['fechaFin'],
                                    'horaCierre'            => $arrayTiempoServer['horaFin'],
                                    'fechaEjecucion'        => $arrayTiempoServer['fechaInicio'],
                                    'horaEjecucion'         => $arrayTiempoServer['horaInicio'],
                                    'esSolucion'            => true,
                                    'fechaApertura'         => "",
                                    'horaApertura'          => "",
                                    'jsonMateriales'        => "",
                                    'idAsignado'            => $objUsuario['ID_PERSONA'],
                                    'observacion'           => "Se finaliza automáticamente la tarea de Activación nuevo nodo Wifi",
                                    'empleado'              => $objUsuario['NOMBRES']." ".$objUsuario['APELLIDOS'],
                                    'usrCreacion'           => $strUsuario,
                                    'ipCreacion'            => $this->get('request')->getClientIp(),
                                    'strEnviaDepartamento'  => "N"
                                );
                                // Se llama al servicio que cierra la tarea.
                                $arrayRespuestaTarea = $objSoporteService->finalizarTarea($arrayParametros);
                            }
                        }
                    }

                    $strMensajeTarea = (isset($arrayRespuestaTarea) && $arrayRespuestaTarea['status'] == 'OK') ? ', '.$arrayRespuestaTarea['mensaje']: '';

                    $respuesta->setContent(json_encode(array(
                        'status'=>'OK',
                        'mensaje'=>$strMensajeTarea
                    )));

                    /*Valido si el servicio INTERNET WIFI es de tipo ESQUEMA 2.*/
                    $intTipoEsquema = $this->get('planificacion.planificar')->getTipoEsquema($objServicio);

                    if ($intTipoEsquema == 2 && $boolFactibilidadL2)
                    {
                        $strInstalacionWifiL2 = $this->generarInstalacionWifiL2($idSolicitud, $peticion, $idServicio);
                        $respuesta->setContent(json_encode(array(
                            'status'=>'OK',
                            'mensaje'=>$strMensajeTarea,
                            'status_InstalacionWifiL2'=>$strInstalacionWifiL2
                        )));
                    }
                    
                }
                else
                    $respuesta->setContent("No existe Solicitud");
            }
            catch(Exception $e)
            {
                $emInfraestructura->getConnection()->rollback();
                $emComercial->getConnection()->rollback();

                $mensajeError = "Error: " . $e->getMessage();
                error_log($mensajeError);
                $respuesta->setContent($mensajeError);
            }

            return $respuesta;
        }
        else
        {
            $respuesta->setContent("No tiene permisos para realizar esta opción.");
            return $respuesta;
        }
    }

    /**
     * Función que se encargar de cambiar de estado el servicio Wifi para instalación simultánea simulando el botón de GIS.
     *
     * @param
     *         • $intIdSolicitud  -> Contiene un int con el id de la solicitud.
     *         • $objPeticion     -> Contiene un objeto de petición PHP.
     *         • $intIdServicio   -> Contiene un int con el id del servicio.
     *
     * @return $respuesta
     * @throws Exception
     */

    public function generarInstalacionWifiL2($intIdSolicitud, $objPeticion, $intIdServicio)
    {
        $entityEm                    = $this->getDoctrine()->getManager();
        $entityEm->getConnection()->beginTransaction();

        $entityTipoSolicitud   = $entityEm->getRepository('schemaBundle:AdmiTipoSolicitud')
            ->findOneByDescripcionSolicitud("SOLICITUD PLANIFICACION");
        $entityServicio        = $entityEm->getRepository('schemaBundle:InfoServicio')
            ->find($intIdServicio);
        $objSolicitudFactibilidad = $entityEm->getRepository('schemaBundle:InfoDetalleSolicitud')
            ->findOneBy(array("servicioId" => $entityServicio->getId(), "estado" => "Factible"));

        $strUsrCreacion = $objPeticion->getSession()->get('user');
        $strClientIp    = $objPeticion->getClientIp();

        try
        {
            $entitySolicitud = new InfoDetalleSolicitud();
            $entitySolicitud->setServicioId($entityServicio);
            $entitySolicitud->setTipoSolicitudId($entityTipoSolicitud);
            $entitySolicitud->setEstado('AsignadoTarea');
            $entitySolicitud->setUsrCreacion($objPeticion->getSession()->get('user'));
            $entitySolicitud->setFeCreacion(new \DateTime('now'));
            $entityEm->persist($entitySolicitud);
            $entityEm->flush();

            $entityServicio->setEstado('AsignadoTarea');
            $entityEm->persist($entityServicio);
            $entityEm->flush();

            //se finaliza la solicitud cuando es un wifi
            if ($entityServicio->getProductoId() && $entityServicio->getProductoId()->getNombreTecnico() == 'INTERNET WIFI')
            {
                //Si el Producto es INTERNET WIFI finaliza la solicitud de PLANIFICACION
                    $objSolicitudFactibilidad->setEstado('Finalizada');
                    $entityEm->persist($objSolicitudFactibilidad);
                    $entityEm->flush();

            }

            //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
            $entityDetalleSolHist = new InfoDetalleSolHist();
            $entityDetalleSolHist->setDetalleSolicitudId($entitySolicitud);
            $entityDetalleSolHist->setIpCreacion($objPeticion->getClientIp());
            $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
            $entityDetalleSolHist->setUsrCreacion($objPeticion->getSession()->get('user'));
            $entityDetalleSolHist->setEstado('AsignadoTarea');
            $entityDetalleSolHist->setObservacion('Se cambia de estado para instalación simultánea.');
            $entityEm->persist($entityDetalleSolHist);
            $entityEm->flush();

            $entityEm->getConnection()->commit();

            $strRespuesta = 'OK';
        }
        catch (Exception $e)
        {
            if ($entityEm->getConnection()->isTransactionActive())
            {
                $strMensajeError = "Error: Ha ocurrido un error, por favor notificar a Sistemas.";
                $entityEm->getConnection()->rollback();
                $entityEm->close();
            }
            $this->utilServicio->insertError('Telcos+',
                "NodoWifiController.generarInstalacionWifiL2",
                "Error: <br>" . $e->getMessage(),
                $strUsrCreacion,
                $strClientIp);

            $strRespuesta = $strMensajeError;

        }

        return $strRespuesta;

    }

    /**
     *
     * ajaxComboCajasNodoAction
     * consulta las cajas que se enlazarán al nodo wifi
     *
     * @return json con las cajas y los id
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 28-12-2015
     */
    public function getElementosSinRelacionAction()
    {
        $respuesta          = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $peticion           = $this->get('request');
        $strNombreElemento  = $peticion->get('query');
        $strTipoElemento    = $peticion->get('tipoElemento');
        $idCanton           = $peticion->get('idCanton');
        $idEmpresa          = ($peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa') : "");

        $objJson = $this->getDoctrine()->getManager("telconet_infraestructura")->getRepository('schemaBundle:InfoElemento')
            ->getJsonElementosSinRelacion($strNombreElemento, $strTipoElemento, $idEmpresa, $idCanton);

        $respuesta->setContent($objJson);

        return $respuesta;
    }
    
    
        /**
     *
     * getElementosAction
     * consulta los elementos que se enlazarán al nodo wifi
     *
     * @return json con las cajas y los id
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 28-12-2015
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.1 08-09-2016  si el canton viene vacio se lo obtiene por servicio
     */
    public function getElementosAction()
    {
        $respuesta          = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $peticion           =  $this->get('request');
        $strNombreElemento  = $peticion->get('query');
        $strTipoElemento    = $peticion->get('tipoElemento');
        $idCanton           = $peticion->get('idCanton');
        $idServicio         = $peticion->get('idServicio');
        $idEmpresa          = ($peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa') : "");
        
        if (!$idCanton)
        {
            if($idServicio)
            {
                //obtengo los datos del canton mediante el servicio
                $idCanton = $this->getDoctrine()->getManager("telconet")->getRepository('schemaBundle:InfoServicio')
                                                ->getCantonPorServicio($idServicio);
            }
        }

        $objJson = $this->getDoctrine()->getManager("telconet_infraestructura")->getRepository('schemaBundle:InfoElemento')
            ->getJsonElementosXtipoEmpresaCanton($strNombreElemento, $strTipoElemento, $idEmpresa, $idCanton);

        $respuesta->setContent($objJson);

        return $respuesta;
    }
    
        
        /**
     *
     * getElementosAction
     * consulta las cajas que se enlazarán al nodo wifi
     *
     * @return json con las cajas y los id
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 28-12-2015
     */
    public function getElementosFactiblesAction()
    {
        $respuesta  = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $peticion   = $this->get('request');
        $idElemento = $peticion->get('idElemento');
        $idServicio = $peticion->get('idServicio');

        $objJson = $this->getDoctrine()->getManager("telconet_infraestructura")->getRepository('schemaBundle:InfoElemento')
            ->getJsonFactibilidadPorNodoWifi($idElemento, $idServicio);       
        
        $respuesta->setContent($objJson);

        return $respuesta;
    }
    

}
