<?php

namespace telconet\tecnicoBundle\Service;

use Exception;
use telconet\schemaBundle\Entity\InfoServicioProdCaract;
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Entity\InfoIp;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoTareaCaracteristica;
use telconet\schemaBundle\Entity\InfoDetalleElemento;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoDetalle;
use telconet\schemaBundle\Entity\InfoDetalleAsignacion;
use telconet\schemaBundle\Entity\InfoDetalleSolCaract;
use telconet\schemaBundle\Entity\InfoDetalleInterface;
use telconet\schemaBundle\Entity\InfoUbicacion;
use telconet\schemaBundle\Entity\InfoEmpresaElementoUbica;
use telconet\schemaBundle\Entity\InfoEmpresaElemento;
use telconet\schemaBundle\Entity\InfoEnlace;
use telconet\schemaBundle\Entity\InfoElemento;
use telconet\schemaBundle\Entity\InfoInterfaceElemento;
use telconet\tecnicoBundle\Service\InfoServicioTecnicoService;
use telconet\tecnicoBundle\Service\InfoCancelarServicioService;
use telconet\tecnicoBundle\Service\InfoActivarPuertoService;
use telconet\tecnicoBundle\Service\NetworkingScriptsService;
use telconet\schemaBundle\Service\UtilService;
use telconet\schemaBundle\Entity\InfoRelacionElemento;
use telconet\comercialBundle\Service\InfoServicioService;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

/**
 * Clase que sirve para el cambio de Equipos
 * 
 * @author Versión Inicial
 * @version 1.0
 * 
 * @author Modificado: Duval Medina C. <dmedina@telconet.ec>
 * @version 1.1 2016-09-06 - Eliminación de 'use' no utilizadas
 *                           Inclusión de variable $serviceUtil de tipo 'schema.Util'
 */

class InfoCambioElementoService {
    private $emComercial;
    private $emInfraestructura;
    private $emSoporte;
    private $emComunicacion;
    private $emSeguridad;
    private $emGeneral;
    private $emNaf;
    private $servicioGeneral;
    private $activarService;
    private $cancelarService;
    private $container;
    private $cambiarPuertoService;
    private $networkingScripts;
    private $serviceUtil;
    private $opcion                 = "CAMBIAR_EQUIPO";
    private $ejecutaComando;
    private $strConfirmacionTNMiddleware;
    private $serviceSolicitudes;
    private $serviceCliente;
    private $serviceConfirmar;
    private $servicePromociones;
    private $serviceSoporte;
    private $serviceInfoServicio;

    public function setDependencies(Container $objContainer) 
    {
        $this->container            = $objContainer;
        $this->emNaf                = $this->container->get('doctrine')->getManager('telconet_naf');
        $this->emGeneral            = $this->container->get('doctrine')->getManager('telconet_general');
        $this->emSoporte            = $this->container->get('doctrine')->getManager('telconet_soporte');
        $this->emSeguridad          = $this->container->get('doctrine')->getManager('telconet_seguridad');
        $this->emComercial          = $this->container->get('doctrine')->getManager('telconet');
        $this->emComunicacion       = $this->container->get('doctrine')->getManager('telconet_comunicacion');
        $this->emInfraestructura    = $this->container->get('doctrine')->getManager('telconet_infraestructura');
        $this->serviceSolicitudes   = $this->container->get('comercial.Solicitudes');
        $this->ejecutaComando       = $this->container->getParameter('ws_rda_ejecuta_scripts');
        $this->serviceInfoElemento  = $this->container->get('tecnico.InfoElemento');
        $this->serviceCliente       = $this->container->get('comercial.Cliente');
        $this->servicioGeneral      = $this->container->get('tecnico.InfoServicioTecnico');
        $this->activarService       = $this->container->get('tecnico.InfoActivarPuerto');
        $this->cancelarService      = $this->container->get('tecnico.InfoCancelarServicio');
        $this->cambiarPuertoService = $this->container->get('tecnico.InfoCambiarPuerto');
        $this->networkingScripts    = $this->container->get('tecnico.NetworkingScripts');
        $this->serviceUtil          = $this->container->get('schema.Util');
        $this->rdaMiddleware        = $this->container->get('tecnico.RedAccesoMiddleware');
        $this->serviceConfirmar     = $this->container->get('tecnico.InfoConfirmarServicio');
        $this->servicePromociones   = $this->container->get('tecnico.Promociones');
        $this->serviceInfoServicio  = $this->container->get('comercial.InfoServicio');
        $this->serviceSoporte       = $this->container->get('soporte.SoporteService');
        $this->strConfirmacionTNMiddleware = $this->container->getParameter('ws_rda_opcion_confirmacion_middleware');
    }

    /**
     * Función para realizar el cambio de dispositivo del cliente que se
     * encuentra en el nodo o un elemento que pertenece al nodo.
     * 
     * @author Fernando López C. <filopez@telconet.ec>
     * @version 1.1 13-07-2022 Se realiza ajuste en la creación de la solicitud de retiro de equipo por cambio de equipo en nodo
     *                           para que registre el id_elemento del equipo y no el id_elemento del nodo.
     *                           Adicional se cambia posisión de código por conficto de foreing key en la tabla info_elemento,
     *                           ya que el commit se realiza al final del proceso.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 28-05-2021
     *
     * @param Array $arrayParametros [
     *                                 boolPerteneceElementoNodo : Valor booleano que indica si el elemento pertenece al nodo.
     *                                 intIdSolicitud            : Id de la solicitud.
     *                                 intNumeroTarea            : Número de tarea.
     *                                 intIdDetalle              : Id de la tabla info_detalle.
     *                                 intIdEmpresa              : Id de la empresa.
     *                                 strTipoResponsable        : Tipo de responsable. C = Cuadrilla, E = Empleado
     *                                 intIdResponsable          : Id del responsable. (Id de la cuadrilla o id de la persona empresa rol).
     *                                 intIdServicio             : Id del servicio del cliente.
     *                                 intIdElementoNodo         : Id del nodo.
     *                                 intIdDispositivoActual    : Id del elemento actual del cliente.
     *                                 strSerieDispositivoNuevo  : Serie del elemento nuevo del cliente.
     *                                 strModeloDispositivoNuevo : Modelo del elemento nuevo del cliente.
     *                                 strMacDispositivoNuevo    : Mac del elemento nuevo del cliente.
     *                                 strTipoDispositivoNuevo   : Tipo del elemento nuevo del cliente.
     *                                 strUsuario                : Login del usuario quien realiza la transacción.
     *                                 strIpUsuario              : Ip del usuario quien realiza la transacción.
     *                               ]
     * @return Array $arrayRespuesta
     */
    public function cambioDispositivoNodo($arrayParametros)
    {
        $boolPerteneceElementoNodo = $arrayParametros['boolPerteneceElementoNodo'];
        $intIdSolicitud            = $arrayParametros['intIdSolicitud'];
        $intNumeroTarea            = $arrayParametros['intNumeroTarea'];
        $intIdDetalle              = $arrayParametros['intIdDetalle'];
        $intIdEmpresa              = $arrayParametros['intIdEmpresa'];
        $strTipoResponsable        = $arrayParametros['strTipoResponsable'];
        $intIdResponsable          = $arrayParametros['intIdResponsable'];
        $intIdServicio             = $arrayParametros['intIdServicio'];
        $intIdElementoNodo         = $arrayParametros['intIdElementoNodo'];
        $intIdDispositivoActual    = $arrayParametros['intIdDispositivoActual'];
        $strNombreElemento         = $arrayParametros['strNombreNuevoElemento'];
        $strSerieDispositivoNuevo  = $arrayParametros['strSerieDispositivoNuevo'];
        $strModeloDispositivoNuevo = $arrayParametros['strModeloDispositivoNuevo'];
        $strMacDispositivoNuevo    = $arrayParametros['strMacDispositivoNuevo'];
        $strTipoDispositivoNuevo   = $arrayParametros['strTipoDispositivoNuevo'];
        $strUsuario                = $arrayParametros['strUsuario'];
        $strIpUsuario              = $arrayParametros['strIpUsuario'];
        $objInfoServicio           = null;
        $objInfoPunto              = null;
        $strLogin                  = null;
        $intIdSolicitudRetiro      = null;

        $this->emComercial->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();
        $this->emNaf->getConnection()->beginTransaction();
        $this->emSoporte->getConnection()->beginTransaction();

        try
        {
            //Obtenemos la información del elemento actual.
            $objInfoElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                    ->find($intIdDispositivoActual);

            if (!is_object($objInfoElemento))
            {
                throw new \Exception('Error : No se logro obtener los datos del dispositivo actual del cliente.');
            }

            //Verificamos si el cambio es un elemento del cliente o del Nodo.
            if ($boolPerteneceElementoNodo)
            {
                if (empty($intIdElementoNodo))
                {
                    throw new \Exception('Error : El id del elemento nodo se encuentra vacio.');
                }

                //Obtenemos los datos del elemento nodo.
                $objInfoElementoNodo = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                        ->find($intIdElementoNodo);

                if (!is_object($objInfoElementoNodo))
                {
                    throw new \Exception('Error : No existe el nodo con id de elemento '.$intIdElementoNodo);
                }

                $strLogin = $objInfoElementoNodo->getNombreElemento();
            }
            else
            {
                if (empty($intIdServicio))
                {
                    throw new \Exception('Error : El id del servicio se encuentra vacio.');
                }

                //Obtenemos los datos del servicio.
                $objInfoServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                        ->find($intIdServicio);

                if (!is_object($objInfoServicio))
                {
                    throw new \Exception('Error : No existe el servicio del cliente con id '.$intIdServicio);
                }

                //Obtenemos los datos del punto
                $objInfoPunto = $objInfoServicio->getPuntoId();

                if (is_object($objInfoPunto))
                {
                    $strLogin   = $objInfoPunto->getLogin();
                    $intIdPunto = $objInfoPunto->getId();
                }
            }

            //Parámetros para realizar la carga y descarga de los activos.
            $arrayParametrosCargaDescarga = array();
            $arrayParametrosCargaDescarga['boolPerteneceElementoNodo'] = $boolPerteneceElementoNodo;
            $arrayParametrosCargaDescarga['intNumeroTarea']            = $intNumeroTarea;
            $arrayParametrosCargaDescarga['intIdElementoNodo']         = $intIdElementoNodo;
            $arrayParametrosCargaDescarga['objServicio']               = $objInfoServicio;
            $arrayParametrosCargaDescarga['idElementoActual']          = $objInfoElemento->getId();
            $arrayParametrosCargaDescarga['serieElementoNuevo']        = $strSerieDispositivoNuevo;
            $arrayParametrosCargaDescarga['tipoResponsable']           = $strTipoResponsable;
            $arrayParametrosCargaDescarga['idResponsable']             = $intIdResponsable;
            $arrayParametrosCargaDescarga['idEmpresa']                 = $intIdEmpresa;
            $arrayParametrosCargaDescarga['usrCreacion']               = $strUsuario;
            $arrayParametrosCargaDescarga['ipCreacion']                = $strIpUsuario;
            $arrayParametrosCargaDescarga['strTipoParametro']             = 'CambioElemento';
            $arrayResCarDes = $this->serviceInfoElemento->cargaDescargaActivosCambioEquipo($arrayParametrosCargaDescarga);
            if (!$arrayResCarDes['status'])
            {
                throw new \Exception('Error : '.$arrayResCarDes['message']);
            }

            //Parámetros para la creación del nuevo elemento.
            $strNombreNuevoElemento = !empty($strNombreElemento) ? $strNombreElemento : $objInfoElemento->getNombreElemento();
            $arrayElementoNodo = array();
            $arrayElementoNodo['boolEsUbicacionNodo']         = true;
            $arrayElementoNodo['boolPerteneceElementoNodo']   = $boolPerteneceElementoNodo;
            $arrayElementoNodo['intIdElementoNodo']           = $intIdElementoNodo;
            $arrayElementoNodo['objServicio']                 = $objInfoServicio;
            $arrayElementoNodo['intIdElementoActual']         = $objInfoElemento->getId();
            $arrayElementoNodo['nombreElementoCliente']       = $strNombreNuevoElemento;
            $arrayElementoNodo['serieElementoCliente']        = $strSerieDispositivoNuevo;
            $arrayElementoNodo['nombreModeloElementoCliente'] = $strModeloDispositivoNuevo;
            $arrayElementoNodo['strMacDispositivo']           = $strMacDispositivoNuevo;
            $arrayElementoNodo['intIdEmpresa']                = $intIdEmpresa;
            $arrayElementoNodo['usrCreacion']                 = $strUsuario;
            $arrayElementoNodo['ipCreacion']                  = $strIpUsuario;
            $strRespuesta = $this->servicioGeneral->ingresarElementoClienteTN($arrayElementoNodo,$strTipoDispositivoNuevo);
            if ($strRespuesta !== "" && is_string($strRespuesta))
            {
                throw new \Exception("Error : $strRespuesta");
            }

            //Se actualiza el nuevo elemento en el naf.
            $arrayParametrosNaf = array();
            $arrayParametrosNaf['empresaCod']            = $intIdEmpresa;
            $arrayParametrosNaf['modeloCpe']             = '';
            $arrayParametrosNaf['tipoArticulo']          = 'AF';
            $arrayParametrosNaf['identificacionCliente'] = '';
            $arrayParametrosNaf['serieCpe']              = $strSerieDispositivoNuevo;
            $arrayParametrosNaf['cantidad']              = 1;
            $strMensajeError = $this->procesaInstalacionElemento($arrayParametrosNaf);
            if (strlen(trim($strMensajeError)) > 0)
            {
                throw new \Exception("Error : $strMensajeError");
            }

            //Se cambia posición de código para crear solicitud de retiro elemento nodo por temas de referencias foreing key
            //con la tabla de db_infraestructura.info_elemento.
            if($boolPerteneceElementoNodo)
            {
                $objAdmiTipoSolicitud     = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                        ->findOneBy(array("descripcionSolicitud" => "SOLICITUD RETIRO EQUIPO",
                                          "estado"               => "Activo"));

                //Característica que indica que el elemento pertenece a un nodo.
                $objAdmiCaracteristicaSol = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                        ->findOneBy(array("descripcionCaracteristica" => "ELEMENTO NODO",
                                          "estado"                    => "Activo"));

                //Característica que indica que la solicitud se crea por un cambio de elemento.
                $objAdmiCaracteristicaCe  = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                        ->findOneBy(array("descripcionCaracteristica" => "CAMBIO ELEMENTO",
                                          "estado"                    => "Activo"));

                //Característica que sirve para enlazar la solicitud con la tarea.
                $objAdmiCaracteristicaTar = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                        ->findOneBy(array("descripcionCaracteristica" => "SOLICITUD NODO",
                                          "estado"                    => "Activo"));

                $objInfoDetalleSolicitud = new InfoDetalleSolicitud();
                $objInfoDetalleSolicitud->setTipoSolicitudId($objAdmiTipoSolicitud);
                $objInfoDetalleSolicitud->setEstado("AsignadoTarea");
                $objInfoDetalleSolicitud->setObservacion("SOLICITA RETIRO DE EQUIPO POR CAMBIO DE EQUIPO");
                $objInfoDetalleSolicitud->setElementoId($objInfoElemento->getId());
                $objInfoDetalleSolicitud->setUsrCreacion($strUsuario);
                $objInfoDetalleSolicitud->setFeCreacion(new \DateTime('now'));
                $this->emComercial->persist($objInfoDetalleSolicitud);
                $this->emComercial->flush();

                $objInfoDetalleSolHist = new InfoDetalleSolHist();
                $objInfoDetalleSolHist->setDetalleSolicitudId($objInfoDetalleSolicitud);
                $objInfoDetalleSolHist->setEstado("AsignadoTarea");
                $objInfoDetalleSolHist->setObservacion("GENERACION AUTOMATICA DE SOLICITUD RETIRO DE EQUIPO POR CAMBIO DE EQUIPO");
                $objInfoDetalleSolHist->setUsrCreacion($strUsuario);
                $objInfoDetalleSolHist->setIpCreacion($strIpUsuario);
                $objInfoDetalleSolHist->setFeCreacion(new \DateTime('now'));
                $this->emComercial->persist($objInfoDetalleSolHist);
                $this->emComercial->flush();

                //Creación del la característica de la solicitud que indica que el elemento pertenece a un nodo.
                $objInfoDetalleSolCaract = new InfoDetalleSolCaract();
                $objInfoDetalleSolCaract->setCaracteristicaId($objAdmiCaracteristicaSol);
                $objInfoDetalleSolCaract->setDetalleSolicitudId($objInfoDetalleSolicitud);
                $objInfoDetalleSolCaract->setValor($objInfoElemento->getId());
                $objInfoDetalleSolCaract->setEstado("AsignadoTarea");
                $objInfoDetalleSolCaract->setUsrCreacion($strUsuario);
                $objInfoDetalleSolCaract->setFeCreacion(new \DateTime('now'));
                $this->emComercial->persist($objInfoDetalleSolCaract);
                $this->emComercial->flush();

                //Creación del la característica de la solicitud que indica que la solicitud es creada por un cambio de equipo.
                $objInfoDetalleSolCaract = new InfoDetalleSolCaract();
                $objInfoDetalleSolCaract->setCaracteristicaId($objAdmiCaracteristicaCe);
                $objInfoDetalleSolCaract->setDetalleSolicitudId($objInfoDetalleSolicitud);
                $objInfoDetalleSolCaract->setValor($objInfoElemento->getId());
                $objInfoDetalleSolCaract->setEstado("AsignadoTarea");
                $objInfoDetalleSolCaract->setUsrCreacion($strUsuario);
                $objInfoDetalleSolCaract->setFeCreacion(new \DateTime('now'));
                $this->emComercial->persist($objInfoDetalleSolCaract);
                $this->emComercial->flush();

                //Creación de la característica de la tarea para enlazar la solicitud con la tarea.
                $objInfoTareaCaracteristica = new InfoTareaCaracteristica();
                $objInfoTareaCaracteristica->setTareaId($intNumeroTarea);
                $objInfoTareaCaracteristica->setDetalleId($intIdDetalle);
                $objInfoTareaCaracteristica->setCaracteristicaId($objAdmiCaracteristicaTar->getId());
                $objInfoTareaCaracteristica->setValor($objInfoDetalleSolicitud->getId());
                $objInfoTareaCaracteristica->setEstado('Activo');
                $objInfoTareaCaracteristica->setUsrCreacion($strUsuario);
                $objInfoTareaCaracteristica->setIpCreacion($strIpUsuario);
                $objInfoTareaCaracteristica->setFeCreacion(new \DateTime('now'));
                $this->emSoporte->persist($objInfoTareaCaracteristica);
                $this->emSoporte->flush();

                $intIdSolicitudRetiro = $objInfoDetalleSolicitud->getId();
            }

            //Eliminación del elemento actual.
            $arrayParametrosEliminar = array();
            $arrayParametrosEliminar['boolPerteneceElementoNodo'] =  $boolPerteneceElementoNodo;
            $arrayParametrosEliminar['intIdElemento']             =  $objInfoElemento->getId();
            $arrayParametrosEliminar['strObservacion']            = 'Eliminación del elemento por cambio de equipo';
            $arrayParametrosEliminar['strUsuario']                =  $strUsuario;
            $arrayParametrosEliminar['strIpUsuario']              =  $strIpUsuario;
            $arrayResEliminar = $this->serviceInfoElemento->eliminarElementoClienteNodo($arrayParametrosEliminar);
            if (!$arrayResEliminar['status'])
            {
                throw new \Exception('Error : '.$arrayResEliminar['message']);
            }

            //Creación del historial.
            if ($boolPerteneceElementoNodo)
            {
                $strObservacionHistEle = "Se realizo un cambio de elemento: "
                                        ." **Elemento Anterior**:"
                                        ." - Nombre: ".$objInfoElemento->getNombreElemento()
                                        ." - Serie:  ".$objInfoElemento->getSerieFisica()
                                        ." - Modelo: ".$objInfoElemento->getModeloElementoId()->getNombreModeloElemento()
                                       . " **Elemento Actual**:"
                                       . " - Nombre: ".$strNombreNuevoElemento
                                       . " - Serie:  ".$strSerieDispositivoNuevo
                                       . " - Modelo: ".$strModeloDispositivoNuevo;

                $objHistorialElemento = new InfoHistorialElemento();
                $objHistorialElemento->setElementoId($objInfoElementoNodo);
                $objHistorialElemento->setObservacion($strObservacionHistEle);
                $objHistorialElemento->setEstadoElemento($objInfoElementoNodo->getEstado());
                $objHistorialElemento->setUsrCreacion($strUsuario);
                $objHistorialElemento->setIpCreacion($strIpUsuario);
                $objHistorialElemento->setFeCreacion(new \DateTime('now'));
                $this->emInfraestructura->persist($objHistorialElemento);
                $this->emInfraestructura->flush();
            }
            else
            {
                $strObservacionHistSer = "<b>Se realizo un cambio de dispositivo Cliente en Nodo:</b><br>"
                                       . "<b style='color:blue'>Dispositivo Anterior:</b><br>"
                                       . "<b>Nombre:</b> ".$objInfoElemento->getNombreElemento().      "<br>"
                                       . "<b>Serie:</b>  ".$objInfoElemento->getSerieFisica().         "<br>"
                                       . "<b>Modelo:</b> ".$objInfoElemento->getModeloElementoId()
                                                                           ->getNombreModeloElemento()."<br>"
                                       . "<b style='color:blue'>Dispositivo Actual:</b><br>"
                                       . "<b>Nombre:</b> ".$strNombreNuevoElemento    ."<br>"
                                       . "<b>Serie:</b>  ".$strSerieDispositivoNuevo. "<br>"
                                       . "<b>Modelo:</b> ".$strModeloDispositivoNuevo."<br>";

                $objServicioHistorial = new InfoServicioHistorial();
                $objServicioHistorial->setServicioId($objInfoServicio);
                $objServicioHistorial->setObservacion($strObservacionHistSer);
                $objServicioHistorial->setEstado($objInfoServicio->getEstado());
                $objServicioHistorial->setUsrCreacion($strUsuario);
                $objServicioHistorial->setIpCreacion($strIpUsuario);
                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                $this->emComercial->persist($objServicioHistorial);
                $this->emComercial->flush();
            }

            //Finalización de la solicitud de cambio de equipo.
            if ($boolPerteneceElementoNodo)
            {
                $objInfoDetalle          = $this->emComercial->getRepository('schemaBundle:InfoDetalle')
                        ->find($intIdDetalle);
                $objInfoDetalleSolicitud = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                        ->find($intIdSolicitud);

                if (is_object($objInfoDetalleSolicitud))
                {
                    $objInfoDetalleSolicitud->setEstado("Finalizada");
                    $this->emComercial->persist($objInfoDetalleSolicitud);
                    $this->emComercial->flush();

                    $objInfoDetalleSolHist = new InfoDetalleSolHist();
                    $objInfoDetalleSolHist->setDetalleSolicitudId($objInfoDetalleSolicitud);
                    $objInfoDetalleSolHist->setEstado("Finalizada");
                    $objInfoDetalleSolHist->setObservacion("Se finalizo la solicitud");
                    $objInfoDetalleSolHist->setUsrCreacion($strUsuario);
                    $objInfoDetalleSolHist->setIpCreacion($strIpUsuario);
                    $objInfoDetalleSolHist->setFeCreacion(new \DateTime('now'));
                    $this->emComercial->persist($objInfoDetalleSolHist);
                    $this->emComercial->flush();

                    $arrayInfoDetalleSolCaract = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                            ->findBy(array("detalleSolicitudId" => $objInfoDetalleSolicitud->getId()));

                    foreach ($arrayInfoDetalleSolCaract as $objInfoDetalleSolCaract)
                    {
                        $objInfoDetalleSolCaract->setEstado("Finalizada");
                        $this->emComercial->persist($objInfoDetalleSolCaract);
                        $this->emComercial->flush();
                    }
                }

                $objAdmiCaracteristica = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                        ->findOneBy(array("descripcionCaracteristica" => "SOLICITUD NODO",
                                          "estado"                    => "Activo"));

                if (is_object($objAdmiCaracteristica))
                {
                    //Obtenemos la característica de la solicitud y tarea.
                    $objInfoTareaCaracteristica = $this->emSoporte->getRepository('schemaBundle:InfoTareaCaracteristica')
                            ->findOneBy(array('caracteristicaId' => $objAdmiCaracteristica->getId(),
                                              'detalleId'        => $intIdDetalle,
                                              'valor'            => $intIdSolicitud));

                    if (is_object($objInfoTareaCaracteristica))
                    {
                        $objInfoTareaCaracteristica->setEstado("Finalizada");
                        $objInfoTareaCaracteristica->setFeModificacion(new \DateTime('now'));
                        $objInfoTareaCaracteristica->setUsrModificacion($strUsuario);
                        $objInfoTareaCaracteristica->setIpModificacion($strIpUsuario);
                        $this->emSoporte->persist($objInfoTareaCaracteristica);
                        $this->emSoporte->flush();
                    }
                }
            }
            else
            {
                $arrayParametrosFinalizarSol = array();
                $arrayParametrosFinalizarSol['objServicio']         =  $objInfoServicio;
                $arrayParametrosFinalizarSol['strTipoSolicitud']    = 'SOLICITUD CAMBIO DE MODEM INMEDIATO';
                $arrayParametrosFinalizarSol['strEstadoSolicitud']  = 'AsignadoTarea';
                $arrayParametrosFinalizarSol['strUsrCreacion']      =  $strUsuario;
                $arrayParametrosFinalizarSol['strIpCreacion']       =  $strIpUsuario;
                $arrayResponse = $this->servicioGeneral->finalizarDetalleSolicitud($arrayParametrosFinalizarSol);

                if ($arrayResponse['status'] != "OK")
                {
                    throw new \Exception('Error : No se logro finalizar la solicitud de cambio de equipo: '.$arrayResponse['mensaje']);
                }

                $objInfoDetalleSolicitud = $arrayResponse['objDetalleSolicitud'];
                $objInfoDetalle          = $this->emComercial->getRepository('schemaBundle:InfoDetalle')
                        ->findOneBy(array("detalleSolicitudId" => $objInfoDetalleSolicitud->getId()));
            }

            //Obtenemos el detalle de asignación.
            $objInfoDetalleAsignacion = $this->emComercial->getRepository('schemaBundle:InfoDetalleAsignacion')
                    ->findOneBy(array("detalleId" => $objInfoDetalle->getId()));

            //Parámetros para registrar el tracking del elemento
            $arrayParametrosAuditoria = array();
            $arrayParametrosAuditoria['boolPerteneceElementoNodo'] =  $boolPerteneceElementoNodo;
            $arrayParametrosAuditoria["strLogin"]                  =  $strLogin;
            $arrayParametrosAuditoria["strUsrCreacion"]            =  $strUsuario;
            $arrayParametrosAuditoria["strNumeroSerie"]            =  $objInfoElemento->getSerieFisica();
            $arrayParametrosAuditoria["strEstadoTelcos"]           = 'Eliminado';
            $arrayParametrosAuditoria["strEstadoNaf"]              = 'Instalado';
            $arrayParametrosAuditoria["strEstadoActivo"]           = 'CambioEquipo';
            $arrayParametrosAuditoria["strUbicacion"]              = 'EnTransito';
            $arrayParametrosAuditoria["strCodEmpresa"]             =  $intIdEmpresa;
            $arrayParametrosAuditoria["strTransaccion"]            = 'Cambio de Elemento';
            $arrayParametrosAuditoria["intOficinaId"]              =  0;

            //Parámetros para crear la solicitud de retiro de equipo de un cliente.
            $arrayParametrosCrearSol = array();
            $arrayParametrosCrearSol['strMotivoSolicitud']             = "CAMBIO DE EQUIPO";
            $arrayParametrosCrearSol["strBandResponsableCambioEquipo"] = "S";
            $arrayParametrosCrearSol['objServicio']                    =  $objInfoServicio;
            $arrayParametrosCrearSol['objElementoCliente']             =  $objInfoElemento;
            $arrayParametrosCrearSol['idPersonaEmpresaRol']            =  $objInfoDetalleAsignacion->getPersonaEmpresaRolId();
            $arrayParametrosCrearSol['usrCreacion']                    =  $strUsuario;
            $arrayParametrosCrearSol['ipCreacion']                     =  $strIpUsuario;
            $arrayParametrosCrearSol["intIdPunto"]                     =  $intIdPunto;
            $arrayParametrosCrearSol["strLogin"]                       =  $strLogin;

            $intMotivoId = $objInfoDetalleSolicitud->getMotivoId();
            if (!empty($intMotivoId))
            {
                 $objAdmiMotivo = $this->emComercial->getRepository('schemaBundle:AdmiMotivo')->find($intMotivoId);
                 if(is_object($objAdmiMotivo))
                 {
                     $arrayParametrosAuditoria["strObservacion"] = $objAdmiMotivo->getNombreMotivo();
                 }
            }

            if ($strTipoResponsable == "C" )
            {
                $arrayDatos = $this->emComercial->getRepository('schemaBundle:AdmiCuadrilla')->findJefeCuadrilla($intIdResponsable);
                if (empty($arrayDatos))
                {
                    throw new \Exception('Error : No se logro obtener el lider de la cuadrilla.');
                }

                $arrayParametrosAuditoria["intIdPersona"]          =  $arrayDatos['idPersona'];
                $arrayParametrosCrearSol["strPersonaEmpresaRolId"] =  $arrayDatos['idPersonaEmpresaRol'];
                $arrayParametrosCrearSol["intIdCuadrilla"]         =  $intIdResponsable;
                $arrayParametrosCrearSol["strTipoAsignado"]        = "CUADRILLA";
            }
            else
            {
                $objInfoPersonaEmpresaRol = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intIdResponsable);
                if (!is_object($objInfoPersonaEmpresaRol))
                {
                    throw new \Exception('Error : No se logro obtener los datos del empleado.');
                }

                $arrayParametrosAuditoria["intIdPersona"]          =  $objInfoPersonaEmpresaRol->getPersonaId()->getId();
                $arrayParametrosCrearSol["strPersonaEmpresaRolId"] =  $objInfoPersonaEmpresaRol->getId();
                $arrayParametrosCrearSol["strTipoAsignado"]        = "EMPLEADO";
            }

            //Ingreso de la auditoria del elemento actual.
            $this->serviceInfoElemento->ingresaAuditoriaElementos($arrayParametrosAuditoria);

            //Generación de la solicitud de retiro de equipo.
            if (!$boolPerteneceElementoNodo)
            {
                $this->servicioGeneral->generarSolicitudRetiroEquipo($arrayParametrosCrearSol);
            }
            
            if ($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->commit();
            }

            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->commit();
            }

            if ($this->emSoporte->getConnection()->isTransactionActive())
            {
                $this->emSoporte->getConnection()->commit();
            }

            if ($this->emNaf->getConnection()->isTransactionActive())
            {
                $this->emNaf->getConnection()->commit();
            }

            $arrayRespuesta = array ('status'  =>  true,
                                     'message' => 'Proceso Ejecutado' ,
                                     'data'    =>  array('idSolicitudRetiro' => $intIdSolicitudRetiro));
        }
        catch (\Exception $objException)
        {
            if ($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->rollback();
                $this->emInfraestructura->getConnection()->close();
            }

            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
                $this->emComercial->getConnection()->close();
            }

            if ($this->emNaf->getConnection()->isTransactionActive())
            {
                $this->emNaf->getConnection()->rollback();
                $this->emNaf->getConnection()->close();
            }

            if ($this->emSoporte->getConnection()->isTransactionActive())
            {
                $this->emSoporte->getConnection()->rollback();
                $this->emSoporte->getConnection()->close();
            }

            $strMessage = "Error al realizar el cambio de equipo";
            $strCodigo  = (new \DateTime('now'))->format('YmdHis').substr(md5(uniqid(rand())),0,6);

            if (strpos($objException->getMessage(),'Error : ') !== false)
            {
                $strMessage = explode('Error : ', $objException->getMessage())[1];
            }

            $this->serviceUtil->insertError('InfoCambioElementoService',
                                            'cambioDispositivoNodo',
                                             $strCodigo.'- 1 -'.$objException->getMessage(),
                                             $strUsuario,
                                             $strIpUsuario);

            $this->serviceUtil->insertError('InfoCambioElementoService',
                                            'cambioDispositivoNodo',
                                             $strCodigo.'- 2 -'.json_encode($arrayParametros),
                                             $strUsuario,
                                             $strIpUsuario);

            $arrayRespuesta = array ('status' => false,'message' => $strMessage);
        }
        return $arrayRespuesta;
    }

    /**
     * Service que realiza el cambio del elemento del cliente,
     *
     * @author Versión original
     * @version 1.0 
     *
     * @author Modificado: Duval Medina C. <dmedina@telconet.ec>
     * @version 1.1 2016-08-30 Bloque de validación throw se incluyo para los 3 if, cuando los métodos devuelvan ERROR
     *                         Agregar else final para contemplar la no recepción del Prefijo Empresa
     *                         Guardar en base los errorres no controlados
     *                         Eliminar las referencias no usadas
     * 
     * @author Modificado: Allan Suárez C. <arsuarez@telconet.ec>
     * @version 1.2 2016-09-14 Se realiza validaciones para que soporte servicios migrados sin data GIS, se ajusta para que en esos
     *                         casos se crea la información de enlaces del cliente de manera correcta
     * 
     * @author Modificado: Jesus Bozada <jbozada@telconet.ec>
     * @version 1.3 10-10-2016 Se agregan parametros usados para cambio de cpe hw de clientes con solicitud de migracion finalizada
     * 
     * @author Modificado: Allan Suárez C. <arsuarez@telconet.ec>
     * @version 1.4 2016-10-21 Se realiza adecuación para poder realizar cambios de CPE que tengan dependiente varios servicios en sus puertos wan
     *                         Se valida objeto que contiene información tecnica del servicio
     * 
     * @author Modificado: Allan Suárez C. <arsuarez@telconet.ec>
     * @version 1.5 2016-11-23 Se realiza adecuación para poder ejecutar flujo para servicios pseudope
     * 
     * @author Modificado: Allan Suárez C. <arsuarez@telconet.ec>
     * @version 1.6 2017-02-07 Se utiliza string para validar si un servicio es pseudope segun variable con S o N
     *    
     * @author Modificado: Jesus Bozada <jbozada@telconet.ec>
     * @version 1.7 2017-03-01 Se agregan parametros para procesar cambio de elementos SmartWifi
     * @since 1.6
     * 
     * @author Modificado: John Vera <javera@telconet.ec>
     * @version 1.8 27-04-2017 Se valida para que el usuario ingrese el TIPO DE FACTIBILIDAD en caso de que no exista
     *   
     * @author Modificado: John Vera <javera@telconet.ec>
     * @version 1.9 03-05-2017 Cuando es la ultima milla Radio se consulta en el parametro TIPO_FACTIBILIDAD_UM que tipo de factibilidad es.
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.0 26-02-2018 Se agregan validaciones para servicios Internet Small Business y se agrega el prefijo empresa para LDAP
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.1 25-09-2018 - Se realizan ajustes en la pantalla de cambio de cpe, se agrega la opción que registra la cuadrilla responsable
     *                           del retiro del equipo
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.2 03-10-2018 - Se realizan ajustes para agregar el concepto que el responsable del retiro de equipo puede ser una
     *                           cuadrilla o un empleado
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 2.3
     * @since 11-09-2018
     * Se agrega la creación de la solicitud de facturación por retiro de equipo.
     *
     * @author Modificado: Jesús Bozada <jbozada@telconet.ec>
     * @version 2.4 12-09-2018 Se agregan validaciones para gestionar nuevo producto AP WIFI
     * @since 2.2
     *
     * @author Modificado: Jesús Bozada <jbozada@telconet.ec>
     * @version 2.5 28-11-2018 - Se agregan validaciones para gestionar los productos de la empresa TNP
     * @since 2.2
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.4 03-12-2018 - Se obtiene parámetro que indica si el elemento es un Extender Dual Band
     * 
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 2.5
     * @since 11-01-2019
     * Se modifica el orden de ejecución de la facturación de equipos.
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 2.6 28-02-2019 - Se agrega validación para que no se envien correos de errores al momento de realizar cambios de elementos
     *                           que son gestionados por una solicitud de agregar equipo
     * @since 2.5
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.7 27-02-2019 Se verifica si es un servicio TelcoHome para que siga el flujo de servicios Small Business
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 2.8 20-06-2019 Se agrega programación para procesar cambios de equipos Dual Band del cliente
     * @since 2.7
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 2.9 16-09-2019 Se agrega proceso para validar promociones en servicios de clientes, en caso de 
     *                         que aplique a alguna promoción se le configurarán los anchos de bandas promocionales
     * @since 2.8
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.10 11-05-2020 Se unifica las validaciones por marca y no por modelo de olt
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 3.0 13-05-2020 - Se agrega el tipo de solicitud: 'SOLICITUD CAMBIO EQUIPO POR SOPORTE MASIVO' y 'SOLICITUD AGREGAR EQUIPO MASIVO'
     * @since 2.9
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 3.1 23-09-2020 Se modifica el orden de la validación para la facturación de visitas técnica, considerando que el producto W + AP 
     *                         no debe crear facturas de este tipo
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 3.2 27-05-2021 Se agrega eliminación de solicitudes que gestionan el ont y las solicitudes dual band asociadas a servicios 
     *                         Wifi Dual Band, W+AP y Extender Dual Band debido a que se está planificando solicitudes de servicios en estado 
     *                         Eliminado, provocando una actualización de estado del servicio ya Eliminado 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 3.3 15-04-2021 Se agrega nuevo parámetro para distinguir los servicios que requieren cambio de ont por medio de una solicitud
     *                         agregar equipo
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 3.3 24-03-2021 Se agrega lógica para que no se eliminen los extender dual band, en el escenario cuando se cambia de un Dual Band
     *                         a un V5
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 3.3 13-05-2021 Se agrega parámetro strOrigen
     *
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 3.2 11-05-2021 Se agrega lógica para controlar que el servicio tenga una solicitud
     *                          de agregar o cambiar equipo
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 3.3 28-05-2021 - Se modifica el método agregando el proceso para cambiar el dipositivo del
     *                           cliente que se encuentran en el nodo.
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 3.4 24-08-2021 Se agrega lógica para controlar si se necesita validar una solicitud
     *                          de agregar o cambiar equipo
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 3.5 10-11-2021 Se agrega la invocación del web service para confirmación de opción de Tn a Middleware
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 3.6 25-01-2022 - Se agrega lógica de cambia equipo para los servicios SafeCity bajo la red GPON_MPLS
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 3.7 04-02-2022 - Se valida la sincronización de extender para cambio de modem inmediato en equipos ZTE.
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 3.8 07-11-2022 - Se agrega lógica de cambio equipo para los servicios Seg Vehiculo.
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 3.9 22-03-2023 - Se agrega Bandera prefijoEmpresa para permitir ingresar al flujo de cambio equipo para ECUANET.
     * 
     * @author Alberto Arias <farias@telconet.ec>
     * @version 3.10 22-05-2023 - Se agrega insertLog para verificar si el proceso de invocación a WS CONFIRMACION_TN, 
     * se realizó de forma correcta.
     * 
     * @author Leonardo Mero <lemero@telconet.ec>
     * @version 4.0 09-12-2022 - Se agregan las validaciones para el producto SAFE ENTRY
     * 
     * @author Alberto Arias <farias@telconet.ec>
     * @version 4.1 07-06-2023 - Se agrega error_log para verificar si se ejecutan las peticiones a MIDDLEWARE
     * 
     * @param type $arrayPeticiones
     * @return array
     */
    public function cambioElemento($arrayPeticiones)
    {
        //*OBTENCION DE PARAMETROS-----------------------------------------------*/
        $idEmpresa              = $arrayPeticiones[0]['idEmpresa'];
        $prefijoEmpresa         = $arrayPeticiones[0]['prefijoEmpresa'];
        $idServicio             = $arrayPeticiones[0]['idServicio'];
        $intIdServicioInternet  = $arrayPeticiones[0]['intIdServicioInternet'];
        $idElemento             = $arrayPeticiones[0]['idElemento'];
        $modeloCpe              = $arrayPeticiones[0]['modeloCpe'];
        $ipCpe                  = $arrayPeticiones[0]['ipCpe'];
        $intIdResponsable       = $arrayPeticiones[0]['idResponsable'];
        $strTipoResponsable     = $arrayPeticiones[0]['tipoResponsable'];
        $nombreCpe              = $arrayPeticiones[0]['nombreCpe'];
        $macCpe                 = $arrayPeticiones[0]['macCpe'];
        $nombreInterface        = $arrayPeticiones[0]['nombreInterface'];
        $macCpeBck              = $arrayPeticiones[0]['macCpeBck'];
        $nombreInterfaceBck     = $arrayPeticiones[0]['nombreInterfaceBck'];
        $serieCpe               = trim(strtoupper($arrayPeticiones[0]['serieCpe']));
        $descripcionCpe         = $arrayPeticiones[0]['descripcionCpe'];
        $tipoElementoCpe        = $arrayPeticiones[0]['tipoElementoCpe'];
        $usrCreacion            = $arrayPeticiones[0]['usrCreacion'];
        $ipCreacion             = $arrayPeticiones[0]['ipCreacion'];
        $intIdElementoWifi      = $arrayPeticiones[0]['intIdElementoWifi'];
        $strModeloWifi          = $arrayPeticiones[0]['strModeloWifi'];
        $strMacWifi             = $arrayPeticiones[0]['strMacWifi'];
        $strRegistroEquipos     = $arrayPeticiones[0]['strRegistraEquipo']?$arrayPeticiones[0]['strRegistraEquipo']:"N";
        $strSerieWifi           = $arrayPeticiones[0]['strSerieWifi'];
        $strDescripcionWifi     = $arrayPeticiones[0]['strDescripcionWifi'];
        $strNombreWifi          = $arrayPeticiones[0]['strNombreWifi'];
        $strTieneMigracionHw    = $arrayPeticiones[0]['strTieneMigracionHw'];
        $strEquipoCpeHw         = $arrayPeticiones[0]['strEquipoCpeHw'];
        $strEquipoWifiAdicional = $arrayPeticiones[0]['strEquipoWifiAdicional'];
        $strAgregarWifi         = $arrayPeticiones[0]['strAgregarWifi'];
        $interfacesConectadas   = $arrayPeticiones[0]['interfacesConectadas'];
        $strEsPseudoPe          = $arrayPeticiones[0]['esPseudoPe'];
        $strEsSmartWifi         = $arrayPeticiones[0]['strEsSmartWifi'];
        $strEsWifiDualBand      = $arrayPeticiones[0]['strEsWifiDualBand'];
        $objEmpleadoSesion      = $arrayPeticiones[0]['objEmpleadoSesion'];
        $strEsApWifi            = $arrayPeticiones[0]['strEsApWifi'];
        $strEsExtenderDualBand  = $arrayPeticiones[0]['strEsExtenderDualBand'];
        $strOrigen              = $arrayPeticiones[0]['strOrigen'] ? $arrayPeticiones[0]['strOrigen'] : "TELCOS";
        $strCambioEquiposDualBand     = $arrayPeticiones[0]['cambioEquiposDualBand'] ? $arrayPeticiones[0]['cambioEquiposDualBand'] : "NO";
        $strEsCambioPorSoporte        = $arrayPeticiones[0]['strEsCambioPorSoporte'] ? $arrayPeticiones[0]['strEsCambioPorSoporte'] : "NO";
        $strEsAgregarEquipoMasivo     = $arrayPeticiones[0]['strEsAgregarEquipoMasivo'] ? $arrayPeticiones[0]['strEsAgregarEquipoMasivo'] : "NO";
        $strEsCambioEquiSoporteMasivo = $arrayPeticiones[0]['strEsCambioEquiSoporteMasivo']?$arrayPeticiones[0]['strEsCambioEquiSoporteMasivo']:"NO";
        $arrayDataConfirmacionTn      = array();

        //Llamada al proceso que realiza el cambio del dispositivo del cliente que se encuentra en el nodo.
        $strUbicacionDispositivo = $arrayPeticiones[0]['strUbicacionDispositivo'];
        if ($strUbicacionDispositivo == "Nodo" && $prefijoEmpresa == "TN")
        {
            $arrayParametrosNodo = array();
            $arrayParametrosNodo['intIdServicio']             = $idServicio;
            $arrayParametrosNodo['intIdDispositivoActual']    = $idElemento;
            $arrayParametrosNodo['strSerieDispositivoNuevo']  = $serieCpe;
            $arrayParametrosNodo['strModeloDispositivoNuevo'] = $modeloCpe;
            $arrayParametrosNodo['strTipoDispositivoNuevo']   = $tipoElementoCpe;
            $arrayParametrosNodo['strMacDispositivoNuevo']    = $macCpe;
            $arrayParametrosNodo['intIdEmpresa']              = $idEmpresa;
            $arrayParametrosNodo['strUsuario']                = $usrCreacion;
            $arrayParametrosNodo['strIpUsuario']              = $ipCreacion;
            $arrayParametrosNodo['intIdResponsable']          = $intIdResponsable;
            $arrayParametrosNodo['strTipoResponsable']        = $strTipoResponsable;
            $arrayResCambioDispo = $this->cambioDispositivoNodo($arrayParametrosNodo);
            if ($arrayResCambioDispo['status'])
            {
                $arrayRespuesta[] = array("status" => "OK"   , "mensaje" => $arrayResCambioDispo['message']);
            }
            else
            {
                $arrayRespuesta[] = array("status" => "ERROR", "mensaje" => $arrayResCambioDispo['message']);
            }
            return $arrayRespuesta;
        }

        $strEsMigracionNgFirewall     = $arrayPeticiones[0]['esMigracionNgFirewall']?$arrayPeticiones[0]['esMigracionNgFirewall']:"NO";
        $strIdPersonaEmpresaRol       = $arrayPeticiones[0]['idPersonaEmpresaRol'];
        
        $strEsCambioOntPorSolAgregarEquipo  = $arrayPeticiones[0]['strEsCambioOntPorSolAgregarEquipo'] ?
                                              $arrayPeticiones[0]['strEsCambioOntPorSolAgregarEquipo'] : "NO";
        
        if($strEsCambioOntPorSolAgregarEquipo === "SI")
        {
            $strTipoSolicitud   = "SOLICITUD AGREGAR EQUIPO";
            $strEstadoSolicitud = "Asignada";
        }
        else if($strCambioEquiposDualBand === "SI")
        {
            if($strEsAgregarEquipoMasivo === "NO")
            {
                $strTipoSolicitud   = "SOLICITUD AGREGAR EQUIPO";
            }
            else
            {
                $strTipoSolicitud   = "SOLICITUD AGREGAR EQUIPO MASIVO";
            }
            
            $strEstadoSolicitud = "Asignada";
            if ($strEsWifiDualBand == "SI")
            {
                //recuperar información de internet y del producto adicional para posteriores seteos de variables y busqueda de información
                $intIdServicioProdWifiDB = $idServicio;
                $idServicio              = $intIdServicioInternet;
            }
        }
        elseif($strEsCambioPorSoporte === "SI")
        {
            if($strEsCambioEquiSoporteMasivo === "NO")
            {
                $strTipoSolicitud   = "SOLICITUD CAMBIO EQUIPO POR SOPORTE";
            }
            else
            {
                $strTipoSolicitud   = "SOLICITUD CAMBIO EQUIPO POR SOPORTE MASIVO";
            }              

            $strEstadoSolicitud = "Asignada";
        }
        else
        {
            $strTipoSolicitud   = "SOLICITUD CAMBIO DE MODEM INMEDIATO";
            $strEstadoSolicitud = "AsignadoTarea";
        }
        $arrayParametros        = array();
        $mensaje                = "";
        $elemento               = null;
        $modeloElemento         = "";
        $strMarcaOlt            = "";
        $interfaceElemento      = null;
        $strEsPlan              = "NO";
        $strEsIsb               = "NO";
        $strEquipoActualEsDB    = "NO";
        $strEquipoNuevoEsDB     = "NO";
        $strEquipoActualEstaParametrizado = "NO";
        $strEquipoNuevoEstaParametrizado = "NO";
        $this->host = $arrayPeticiones[0]['host'];

        //migracion_ttco_md
        $arrayEmpresaMigra = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                               ->getEmpresaEquivalente($idServicio, $prefijoEmpresa);

        if($arrayEmpresaMigra)
        {
            if ($arrayEmpresaMigra['prefijo']=='TTCO')
            {
                 $prefijoEmpresa= $arrayEmpresaMigra['prefijo'];
            }
        }
        
        $servicio            = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                 ->find($idServicio);
        $servicioTecnico     = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                 ->findOneBy(array( "servicioId" => $servicio->getId()));
        $producto            = $servicio->getProductoId();
                        
        if($idEmpresa==10 || ($idEmpresa==26 && is_object($producto) && $producto->getNombreTecnico() === "INTERNET SMALL BUSINESS"))
        {
            
            if(is_object($producto) 
                && ($producto->getNombreTecnico() === "INTERNET SMALL BUSINESS" || $producto->getNombreTecnico() === "TELCOHOME"))
            {
                $strEsIsb = "SI";
            }
        }
        else
        {
            $producto            = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                 ->findOneBy(array('empresaCod'     => $idEmpresa, 
                                                                   'nombreTecnico'  => array('INTERNET'),
                                                                   'estado'         => 'Activo'));
        }
        
        $objPlanServicio = $servicio->getPlanId();
        if(is_object($objPlanServicio))
        {
            $strEsPlan = "SI";
        }
        
        // jlafuente: Se obtienen el Interface y Elemento de Backbone
        //            * MD -> Se obtienen del OLT
        //            * TN -> Se obtienen del SWITCH
        //Se valida existencia de informacion ya que para escenarios pseudope no hay datos tecnicos completos
        if($servicioTecnico->getInterfaceElementoId())
        {
            $interfaceElementoId = $servicioTecnico->getInterfaceElementoId();
            $interfaceElemento   = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                           ->find($interfaceElementoId);
            $elemento            = $interfaceElemento->getElementoId();
            $modeloElemento      = $elemento->getModeloElementoId();
            $strMarcaOlt         = $modeloElemento->getMarcaElementoId()->getNombreMarcaElemento();
        }                
    
        if($strMarcaOlt == "TELLION")
        {
            //verifico si el olt esta aprovisionando el CNR
            $objDetalleElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                          ->findOneBy(array('detalleNombre' => 'OLT MIGRADO CNR',
                                                                            'elementoId'    => $interfaceElemento->getElementoId()
                                                                                                                 ->getId()));
            if($objDetalleElemento)
            {
                $ejecutaLdap = "SI";
            }
        }
        else
        {
            $ejecutaLdap = "SI";
        }

        //*DECLARACION DE TRANSACCIONES------------------------------------------*/
        $this->emComercial->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();
        $this->emNaf->getConnection()->beginTransaction();
        //*----------------------------------------------------------------------*/
        
        try{
            $objSolicitudCMI          = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                          ->findOneBy(array("descripcionSolicitud" => $strTipoSolicitud,
                                                                            "estado"               => "Activo"));
            $intIdServicioConsultaSol = $idServicio;
            if ($strEsWifiDualBand == "SI")
            {
                //recuperar información de internet y del producto adicional para posteriores seteos de variables y busqueda de información
                $intIdServicioConsultaSol = $intIdServicioProdWifiDB;
            }
            
            //Consultar se es cambio de equipo por servicio Security Ng Firewall
            if ($strEsMigracionNgFirewall == "NO")
            {
                $objSolicitudCambioModemTemp = $this->emComercial->getRepository("schemaBundle:InfoDetalleSolicitud")
                                                             ->findOneBy(array("estado"          => $strEstadoSolicitud,
                                                                               "servicioId"      => $intIdServicioConsultaSol,
                                                                               "tipoSolicitudId" => $objSolicitudCMI));
            
                if (!is_object($objSolicitudCambioModemTemp))
                {
                    $mensaje = 'No se encontró ninguna solicitud para agregar o cambiar equipo';
                    throw new \Exception($mensaje);
                }

                $intSolicitudCambioModemId   = $objSolicitudCambioModemTemp->getId();
            }
            
            if($prefijoEmpresa=="TTCO"){
                $respuestaArray = $this->cambioElementoTtco($servicio, 
                                                            $servicioTecnico, 
                                                            $producto, 
                                                            $serieCpe, 
                                                            $modeloCpe, 
                                                            $nombreCpe, 
                                                            $descripcionCpe, 
                                                            $ipCpe, 
                                                            $macCpe, 
                                                            $idEmpresa, 
                                                            $usrCreacion, 
                                                            $ipCreacion, 
                                                            $tipoElementoCpe);
            }
            else if($prefijoEmpresa=="MD" || $strEsIsb === "SI" || $prefijoEmpresa == "TNP" || $prefijoEmpresa == "EN")
            {
                $arrayParametros = array( 'servicio'               => $servicio,
                                          'servicioTecnico'        => $servicioTecnico,
                                          'modeloElemento'         => $modeloElemento,
                                          'strMarcaOlt'            => $strMarcaOlt,
                                          'interfaceElemento'      => $interfaceElemento,
                                          'producto'               => $producto,
                                          'serieCpe'               => $serieCpe,
                                          'codigoArticulo'         => $modeloCpe,
                                          'nombreCpe'              => $nombreCpe,
                                          'descripcionCpe'         => $descripcionCpe,
                                          'macCpe'                 => $macCpe,
                                          'strEsAgregarEquipoMasivo'     => $strEsAgregarEquipoMasivo,
                                          'strEsCambioEquiSoporteMasivo' => $strEsCambioEquiSoporteMasivo,
                                          'tipoElementoCpe'        => $tipoElementoCpe,
                                          'idEmpresa'              => $idEmpresa,
                                          'idElementoCliente'      => $idElemento,
                                          'intIdElementoWifi'      => $intIdElementoWifi,
                                          'strModeloWifi'          => $strModeloWifi,
                                          'strMacWifi'             => $strMacWifi,
                                          'strSerieWifi'           => $strSerieWifi,
                                          'strDescripcionWifi'     => $strDescripcionWifi,
                                          'strNombreWifi'          => $strNombreWifi,
                                          'strTieneMigracionHw'    => $strTieneMigracionHw,
                                          'strEquipoCpeHw'         => $strEquipoCpeHw,
                                          'strEquipoWifiAdicional' => $strEquipoWifiAdicional,
                                          'strAgregarWifi'         => $strAgregarWifi,
                                          'strEsPlan'              => $strEsPlan,
                                          'strEsSmartWifi'         => $strEsSmartWifi,
                                          'strEsApWifi'            => $strEsApWifi,
                                          'usrCreacion'            => $usrCreacion,
                                          'ipCreacion'             => $ipCreacion,
                                          'prefijoEmpresa'         => $prefijoEmpresa,
                                          'strEsIsb'               => $strEsIsb,
                                          'strEsExtenderDualBand'  => $strEsExtenderDualBand,
                                          'strEsWifiDualBand'      => $strEsWifiDualBand,
                                          'objEmpleadoSesion'      => $objEmpleadoSesion,
                                          'intIdServicioProdWifiDB'  => $intIdServicioProdWifiDB,
                                          'strCambioEquiposDualBand' => $strCambioEquiposDualBand,
                                          'strOrigen'                => $strOrigen,
                                          'strEsCambioPorSoporte'    => $strEsCambioPorSoporte,
                                          'strEsCambioOntPorSolAgregarEquipo' => $strEsCambioOntPorSolAgregarEquipo
                                        );
                
                $respuestaArray = $this->cambioElementoMd($arrayParametros);
            }
            else if($prefijoEmpresa=="TN"){
                $booleanSegVehiculo = false;
                if(is_object($servicio->getProductoId()) && $servicio->getProductoId()->getNombreTecnico() == "SEG_VEHICULO")
                {
                    $booleanSegVehiculo = true;
                }
                $boolSafeEntry = $servicio->getProductoId()->getNombreTecnico() == 'SAFE ENTRY';
                if(!is_object($servicioTecnico))
                {
                    $respuestaArray[] = array('status'  => "ERROR",
                                              'mensaje' => "ERROR EN LOGICA DE NEGOCIO, Servicio no posee información Técnica");
                    return $respuestaArray;
                }

                if($servicioTecnico->getUltimaMillaId())
                {
                    $objUltimaMilla = $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')
                                                              ->find($servicioTecnico->getUltimaMillaId());
                }

                if(!is_object($objUltimaMilla) && $strRegistroEquipos != "S" && !$booleanSegVehiculo && !$boolSafeEntry)
                {
                    $respuestaArray[] = array('status'  => "ERROR",
                                              'mensaje' => "ERROR EN LOGICA DE NEGOCIO, Servicio No Posee información de Ultima Milla");
                    return $respuestaArray;
                }

                $arrayParams = array();
                $arrayParams['objServicio']          = $servicio;
                $arrayParams['strRegistroEquipos']   = $strRegistroEquipos;
                $arrayParams['objServicioTecnico']   = $servicioTecnico;
                // ...
                $arrayParams['objProducto']          = $producto;
                // ...
                $arrayParams['objElemento']          = $elemento;
                $arrayParams['objModeloElemento']    = $modeloElemento;
                $arrayParams['objInterfaceElemento'] = $interfaceElemento;
                //...
                $arrayParams['idElementoActual']     = $idElemento;
                //...
                $arrayParams['serieElementoNuevo']   = $serieCpe;
                $arrayParams['macElementoNuevo']     = $macCpe;
                $arrayParams['modeloElementoNuevo']  = $modeloCpe;
                $arrayParams['nombreElementoNuevo']  = $nombreCpe;
                $arrayParams['descripcionNuevo']     = $descripcionCpe;
                $arrayParams['tipoElementoNuevo']    = $tipoElementoCpe;
                //...
                $arrayParams['idEmpresa']            = $idEmpresa;
                $arrayParams['prefijoEmpresa']       = $prefijoEmpresa;
                //...
                $arrayParams['usrCreacion']          = $usrCreacion;
                $arrayParams['ipCreacion']           = $ipCreacion;
                // ...
                $arrayParams['objUltimaMilla']       = $objUltimaMilla;
                $arrayParams['strEsPseudoPe']        = $strEsPseudoPe;

                if($servicioTecnico->getUltimaMillaId())
                {
                    $objTipoMedio   = $this->emInfraestructura->getRepository("schemaBundle:AdmiTipoMedio")
                                                              ->find($servicioTecnico->getUltimaMillaId());
                }

                if(is_object($objTipoMedio))
                {
                    $strUltimaMilla     = $objTipoMedio->getNombreTipoMedio();
                    $strCodigoTipoMedio = $objTipoMedio->getCodigoTipoMedio();
                }
                
                $boolEsFibraRuta = false;
                //Obtener la caracteristica TIPO_FACTIBILIDAD para discriminar que sea FIBRA DIRECTA o RUTA
                
                if($strRegistroEquipos != "S" && $strEsMigracionNgFirewall == "NO")
                {
                    $objServProdCaractTipoFact = $this->servicioGeneral
                                                      ->getServicioProductoCaracteristica($servicio,'TIPO_FACTIBILIDAD',$servicio->getProductoId());

                    if($objServProdCaractTipoFact)
                    {
                        if($objServProdCaractTipoFact->getValor() == "RUTA")
                        {
                            $boolEsFibraRuta = true;
                        }
                    }
                    else
                    {
                        //consulto en los parametros a ver si la um tiene relacion con algun tipo de factibilidad
                        $arrayParametro   = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')->getOne('TIPO_FACTIBILIDAD_UM',
                                                                                                                       '',
                                                                                                                       '',
                                                                                                                       $strCodigoTipoMedio,
                                                                                                                       '',
                                                                                                                       '',
                                                                                                                       '',
                                                                                                                       '',
                                                                                                                       '',
                                                                                                                       '',
                                                                                                                       '');

                        if(is_array($arrayParametro))
                        {
                            if($arrayParametro['valor1'] == 'RUTA')
                            {
                                $boolEsFibraRuta = true;
                            }
                        }
                        else
                        {
                            //se cambio validacion para que el usuario especifique si el tipo de factibilidad es RUTA o DIRECTO
                            $respuestaArray[] = array('status' => "ERROR",
                                                      'mensaje'=> "No se puede realizar cambio de equipo, favor corregir la información de
                                                                   Factibilidad del servicio!.");
                            return $respuestaArray;
                        }

                    }
                }

                $arrayParams['boolEsFibraRuta']        = $boolEsFibraRuta;
                $arrayParams['nombreInterface']        = $nombreInterface;
                $arrayParams['idResponsable']          = $intIdResponsable;
                $arrayParams['tipoResponsable']        = $strTipoResponsable;

                //Cuando se requiera cambios de varios servicios ligados a un CPE en el/los puertos wan
                $arrayParams['macCpeBck']              = $macCpeBck;
                $arrayParams['nombreInterfaceBck']     = $nombreInterfaceBck;
                $arrayParams['interfacesConectadas']   = $interfacesConectadas;
                $arrayParams['esMigracionNgFirewall']  = $strEsMigracionNgFirewall;
                $arrayParams['idPersonaEmpresaRol']    = $strIdPersonaEmpresaRol;
                
                //se valida si el tipo de red es GPON
                $booleanTipoRedGpon = false;
                if(isset($arrayPeticiones[0]['strTipoRed']) && !empty($arrayPeticiones[0]['strTipoRed']))
                {
                    $arrayParVerTipoRed = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getOne('NUEVA_RED_GPON_TN',
                                                            'COMERCIAL',
                                                            '',
                                                            'VERIFICAR TIPO RED',
                                                            'VERIFICAR_GPON',
                                                            $arrayPeticiones[0]['strTipoRed'],
                                                            '',
                                                            '',
                                                            '');
                    if(isset($arrayParVerTipoRed) && !empty($arrayParVerTipoRed))
                    {
                        $booleanTipoRedGpon = true;
                    }
                }
                //se verifica el tipo de red
               if($booleanTipoRedGpon)
                {
                    $respuestaArray = $this->cambioElementoTnGpon($arrayParams);
                }
                elseif($booleanSegVehiculo)
                {
                    $respuestaArray = $this->cambioElementoTnSegVehiculo($arrayParams);
                }
                elseif($boolSafeEntry)
                {
                    $respuestaArray = $this->cambioElementoTnSafeEntry($arrayParams);
                }
                else
                {
                    $respuestaArray = $this->cambioElementoTn($arrayParams);
                }
            }
            else
            {
                $respuestaArray[] = array('status'  => "ERROR",
                                          'mensaje' => "ERROR EN LOGICA DE NEGOCIO, no está definida la Empresa");
            }
            $status                       = $respuestaArray[0]?$respuestaArray[0]['status']:'ERROR';
            $arrayRespuestaCambioElemento = $respuestaArray[0];
            if (is_array($arrayRespuestaCambioElemento))
            {
                $strTipoCambioElemento              = isset($arrayRespuestaCambioElemento["strTipoCambioElemento"])?
                                                        $arrayRespuestaCambioElemento["strTipoCambioElemento"]:"";
                $strEquipoActualEsDB                = isset($arrayRespuestaCambioElemento["strEquipoActualEsDB"])?
                                                        $arrayRespuestaCambioElemento["strEquipoActualEsDB"]:"NO";
                $strEquipoNuevoEsDB                 = isset($arrayRespuestaCambioElemento["strEquipoNuevoEsDB"])?
                                                        $arrayRespuestaCambioElemento["strEquipoNuevoEsDB"]:"NO";
                $strEquipoActualEstaParametrizado   = isset($arrayRespuestaCambioElemento["strEquipoActualEstaParametrizado"])?
                                                        $arrayRespuestaCambioElemento["strEquipoActualEstaParametrizado"]:"NO";
                $strEquipoNuevoEstaParametrizado    = isset($arrayRespuestaCambioElemento["strEquipoNuevoEstaParametrizado"])?
                                                        $arrayRespuestaCambioElemento["strEquipoNuevoEstaParametrizado"]:"NO";
                $arrayDataConfirmacionTn            = $arrayRespuestaCambioElemento["arrayDataConfirmacionTn"];
            }

            if($status != 'OK')
            {
                $mensaje = $respuestaArray[0]?$respuestaArray[0]['mensaje']:'ERROR EN LOGICA DE NEGOCIO, sin respuesta conocida!';
                throw new \Exception($mensaje);
            }

            //*DECLARACION DE COMMITS*/
            if ($this->emInfraestructura->getConnection()->isTransactionActive()){
                $this->emInfraestructura->getConnection()->commit();
            }

            if ($this->emComercial->getConnection()->isTransactionActive()){
                $this->emComercial->getConnection()->commit();
            }

            if ($this->emNaf->getConnection()->isTransactionActive()){
                $this->emNaf->getConnection()->commit();
            }

            //---------------------------------------------------------------------------
            //Se crea la solicitud de FACTURACION RETIRO DE EQUIPO en caso de ser necesario.
            if ($arrayParams['esMigracionNgFirewall']=="NO")
            {
                $objSolicitudCambioModemIn = $this->emComercial->getRepository("schemaBundle:InfoDetalleSolicitud")
                                                               ->findOneBy(array("id"     => $intSolicitudCambioModemId,
                                                                                 "estado" => "Finalizada"));
                if($strCambioEquiposDualBand === "NO" && $strEsCambioOntPorSolAgregarEquipo === "NO")
                {
                    $this->serviceSolicitudes
                         ->creaSolicitudFacturacionEquipo(array("intElementoClienteId"    => $idElemento,
                                                                "objInfoDetalleSolicitud" => $objSolicitudCambioModemIn,
                                                                "objInfoServicio"         => $servicio,
                                                                "strEmpresaCod"           => strval($idEmpresa),
                                                                "strUsrCreacion"          => $usrCreacion,
                                                                "strIpCreacion"           => $ipCreacion));
                }
            }
                        
            if($strCambioEquiposDualBand === "SI" && $strEsWifiDualBand == "SI"
                && isset($intIdServicioProdWifiDB) && !empty($intIdServicioProdWifiDB))
            {
                $objServicioWifiDB              = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                    ->find($intIdServicioProdWifiDB);
                if(is_object($objServicioWifiDB))
                {
                    /**
                     * Consultar en los parametros si la bandera de facturar las visitas técnicas para activación
                     * de wifi dual band está encendida.
                     */
                    $arrayAdmiParametroDebeFacurar  = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                      ->getOne('FACTURAR_VISITA_POR_INSTALACION',
                                                                               'TECNICO',
                                                                               '',
                                                                               $objServicioWifiDB->getProductoId()->getNombreTecnico(),
                                                                               '',
                                                                               '',
                                                                               '',
                                                                               '',
                                                                               '',
                                                                               $idEmpresa);
                    if (isset($arrayAdmiParametroDebeFacurar['valor1']) &&
                        !empty($arrayAdmiParametroDebeFacurar['valor1']) &&
                        $arrayAdmiParametroDebeFacurar['valor1'] === 'SI')
                    {
                        $arrayParametrosSolicitudVisitaDB = array();
                        $arrayParametrosSolicitudVisitaDB['strUser']       = $usrCreacion;
                        $arrayParametrosSolicitudVisitaDB['strIpServicio'] = $ipCreacion;
                        $arrayParametrosSolicitudVisitaDB['objServicio']   = $objServicioWifiDB;
                        $arrayParametrosSolicitudVisitaDB['strEmpresaCod'] = $idEmpresa;
                        $arrayParametrosSolicitudVisitaDB['floatValor']    = $arrayAdmiParametroDebeFacurar['valor2'];
                        $this->servicioGeneral->generarSolicitudVisitaTecnicaPorInstalacion($arrayParametrosSolicitudVisitaDB);
                    }
                }
            }
            
            /* Si se realiza el cambio del elemento WIFI de un cliente MD se valida si tiene equipos extenders a reconfigurar y 
               adicional se valida si tiene un producto WIFI_DUAL_BAND como adicional para actualizar la información técnica de ese servicio.
               Validar si el cambio de modem inmediato se realiza de un WIFI DUAL BAND hacia un equipo que no es DUAL BAND, de ser así
               se verifica si existe un producto dual band como adicional en estado Activo ó In-Corte y se procede a la cancelación
               del producto.
             */
            if ($strCambioEquiposDualBand == "NO" && 
                $strTipoSolicitud         ==  "SOLICITUD CAMBIO DE MODEM INMEDIATO" &&
                ($strTipoCambioElemento == "HUAWEI" || $strTipoCambioElemento == "ZTE") )
            {
                if( ($strEquipoActualEsDB == "SI" && $strEquipoNuevoEsDB == "SI" && $strTipoCambioElemento == "HUAWEI")
                    || ($strEquipoActualEstaParametrizado == "SI" && $strEquipoNuevoEstaParametrizado == "SI"
                        && $strTipoCambioElemento == "ZTE") )
                {
                    //generá sincronización de extender's en caso de existir
                    $arrayParametrosSincronizarWDB = array();
                    $arrayParametrosSincronizarWDB['strProceso']     = 'CAMBIO_ELEMENTO';
                    $arrayParametrosSincronizarWDB['objServicio']    = $servicio;
                    $arrayParametrosSincronizarWDB['strCodEmpresa']  = $idEmpresa;
                    $arrayParametrosSincronizarWDB['strUsrCreacion'] = $usrCreacion;
                    $arrayParametrosSincronizarWDB['strIpCreacion']  = $ipCreacion;
                    $arrayParametrosSincronizarWDB['objProductoInternet'] = $producto;
                    $this->servicioGeneral->generarSincronizacionExtenderDualBand($arrayParametrosSincronizarWDB);
                }
                
                /* validación para cancelación de producto WIFI_DUAL_BAND como adicional y cancelación de extenders dual band 
                   que se encuentren como producto adicional */
                if ($strEquipoActualEsDB == "SI" && $strEquipoNuevoEsDB == "NO" && $strTipoCambioElemento == "HUAWEI")
                {
                    try
                    {
                        /* se procede a buscar si existe algún producto WIFI_DUAL_BAND como servicio adicional Activo ó In-Corte 
                         * y se realiza la cancelación del servico en caso de existir
                         */
                        $objPuntoServicio   = $servicio->getPuntoId();
                        $objAccion = $this->emSeguridad->getRepository('schemaBundle:SistAccion')->find(313);
                        if (!is_object($objAccion))
                        {
                            throw new \Exception("No se encontró información de la acción para cancelar servicios");
                        }
                        
                        $objServicioTecnicoAct      = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                                        ->findOneBy(array( "servicioId" => $servicio->getId()));
                        $arrayRespuestaServiciosWdb = $this->servicioGeneral
                                                           ->obtenerServiciosPorProducto(
                                                                                           array("intIdPunto"                    => 
                                                                                                 $objPuntoServicio->getId(),
                                                                                                 "arrayNombresTecnicoProducto"   => 
                                                                                                 array("WIFI_DUAL_BAND", "WDB_Y_EDB"),
                                                                                                 "strCodEmpresa"             => $idEmpresa));
                        $arrayServiciosWdb          = $arrayRespuestaServiciosWdb["arrayServiciosPorProducto"];
                        $arrayServiciosWyApCancel   = array();
                        if(isset($arrayServiciosWdb) && !empty($arrayServiciosWdb))
                        {
                            foreach($arrayServiciosWdb as $objServicioProdWdb)
                            {
                                if($objServicioProdWdb->getEstado() === 'Activo' || $objServicioProdWdb->getEstado() === 'In-Corte')
                                {
                                    $strEstadoServicio      = "Cancel";
                                    $strObservacionServicio = "Se cancelo el servicio";
                                    $strNombreAccion        = $objAccion->getNombreAccion();
                                    if(is_object($objServicioProdWdb->getProductoId()) 
                                        && $objServicioProdWdb->getProductoId()->getNombreTecnico() === "WDB_Y_EDB")
                                    {
                                        $arrayServiciosWyApCancel[]  = $objServicioProdWdb;
                                    }
                                }
                                else
                                {
                                    $strEstadoServicio      = "Eliminado";
                                    $strObservacionServicio = "Se elimina el servicio";
                                    $strNombreAccion        = "";
                                }
                                
                                $this->servicioGeneral->eliminaSolicitudesGestionaOnt(array("intIdServicio" => $objServicioProdWdb->getId()));
                                $this->servicioGeneral->eliminaSolicitudesDualBand(array("intIdServicio" => $objServicioProdWdb->getId()));
                                
                                $objServicioProdWdb->setEstado($strEstadoServicio);
                                $this->emComercial->persist($objServicioProdWdb);
                                $this->emComercial->flush();

                                //historial del servicio
                                $objServicioHistorial = new InfoServicioHistorial();
                                $objServicioHistorial->setServicioId($objServicioProdWdb);
                                $objServicioHistorial->setObservacion($objServicioProdWdb->getProductoId()->getDescripcionProducto()
                                                                      .": ".$strObservacionServicio);
                                $objServicioHistorial->setEstado($strEstadoServicio);
                                $objServicioHistorial->setUsrCreacion($usrCreacion);
                                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                                $objServicioHistorial->setIpCreacion($ipCreacion);
                                $objServicioHistorial->setAccion ($strNombreAccion);
                                $this->emComercial->persist($objServicioHistorial);
                                $this->emComercial->flush();
                            }
                        }
                        
                        if(isset($arrayServiciosWyApCancel) && !empty($arrayServiciosWyApCancel))
                        {
                            $boolFalse              = false;
                            $objServicioWyApCancel  = $arrayServiciosWyApCancel[0];
                            if(is_object($objServicioTecnicoAct))
                            {
                                $intIdInterfaceOntAct = $objServicioTecnicoAct->getElementoClienteId();
                                if(isset($intIdInterfaceOntAct) && !empty($intIdInterfaceOntAct))
                                {
                                    $objInterfaceOntAct = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                                  ->find($intIdInterfaceOntAct);
                                }
                            }
                            $objTipoSolicitudRetiro = $this->emComercial
                                                           ->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                           ->findOneBy(array("descripcionSolicitud" => "SOLICITUD RETIRO EQUIPO",
                                                                             "estado"               => "Activo"));
                            if(is_object($objTipoSolicitudRetiro))
                            {
                                $objSolicitudRetiro = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                           ->findOneBy(array("servicioId"      => $servicio->getId(), 
                                                                             "tipoSolicitudId" => $objTipoSolicitudRetiro, 
                                                                             "estado"          => "AsignadoTarea"),
                                                                       array('feCreacion' => 'DESC'));
                                //caracteristica ELEMENTO CLIENTE
                                $objCaracteristicaElementoCliente = $this->emComercial
                                                                         ->getRepository('schemaBundle:AdmiCaracteristica')
                                                                         ->find(360);
                                if (is_object($objSolicitudRetiro) && is_object($objCaracteristicaElementoCliente) 
                                    && is_object($objServicioWyApCancel))
                                {
                                    //obtener el primer extender
                                    $objServicioTecnicoWyApCancel   = $this->emComercial
                                                                           ->getRepository('schemaBundle:InfoServicioTecnico')
                                                                           ->findOneBy(array( "servicioId" => $objServicioWyApCancel->getId()));
                                    if(is_object($objServicioTecnicoWyApCancel))
                                    {
                                        $intIdExtenderWyApCancel            = $objServicioTecnicoWyApCancel->getElementoClienteId();
                                        $intIdInterfaceExtenderWyApCancel   = $objServicioTecnicoWyApCancel->getInterfaceElementoClienteId();
                                        if(isset($intIdExtenderWyApCancel) && !empty($intIdExtenderWyApCancel)
                                            && isset($intIdInterfaceExtenderWyApCancel) && !empty($intIdInterfaceExtenderWyApCancel))
                                        {
                                            $objExtenderWyApCancel  = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                                              ->find($intIdExtenderWyApCancel);
                                            $objInterfaceExtenderWyApCancel = $this->emInfraestructura
                                                                                   ->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                                   ->find($intIdInterfaceExtenderWyApCancel);
                                            if (is_object($objExtenderWyApCancel) && is_object($objInterfaceExtenderWyApCancel)
                                                && is_object($objInterfaceExtenderWyApCancel)
                                                && strpos($objExtenderWyApCancel->getNombreElemento(), 'ExtenderDualBand') !== $boolFalse)
                                            {    
                                                $objEntityCaract = new InfoDetalleSolCaract();
                                                $objEntityCaract->setCaracteristicaId($objCaracteristicaElementoCliente);
                                                $objEntityCaract->setDetalleSolicitudId($objSolicitudRetiro);
                                                $objEntityCaract->setValor($objExtenderWyApCancel->getId());
                                                $objEntityCaract->setEstado("AsignadoTarea");
                                                $objEntityCaract->setUsrCreacion($usrCreacion);
                                                $objEntityCaract->setFeCreacion(new \DateTime('now'));
                                                $this->emComercial->persist($objEntityCaract);
                                                $this->emComercial->flush();
                                                
                                                $objEnlaceWyApCancel    = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                                               ->findOneBy( array(  "interfaceElementoFinId" => 
                                                                                                    $objInterfaceExtenderWyApCancel,
                                                                                                    "estado"                 => 'Activo' ));
                                                if(is_object($objEnlaceWyApCancel))
                                                {
                                                    $objEnlaceWyApCancel->setEstado('Eliminado');
                                                    $this->emInfraestructura->persist($objEnlaceWyApCancel);
                                                    $this->emInfraestructura->flush();

                                                    //se eliminan elementos del servicio
                                                    $objExtenderWyApCancel->setEstado("Eliminado");
                                                    $this->emInfraestructura->persist($objExtenderWyApCancel);
                                                    $this->emInfraestructura->flush();

                                                    //SE REGISTRA EL TRACKING DEL ELEMENTO
                                                    $arrayParametrosAuditoria                       = array();
                                                    $arrayParametrosAuditoria["strNumeroSerie"]     = $objExtenderWyApCancel->getSerieFisica();
                                                    $arrayParametrosAuditoria["strEstadoTelcos"]    = 'Eliminado';
                                                    $arrayParametrosAuditoria["strEstadoNaf"]       = 'Instalado';
                                                    $arrayParametrosAuditoria["strEstadoActivo"]    = 'Cancelado';
                                                    $arrayParametrosAuditoria["strUbicacion"]       = 'Cliente';
                                                    $arrayParametrosAuditoria["strCodEmpresa"]      = $idEmpresa;
                                                    $arrayParametrosAuditoria["strTransaccion"]     = 'Cancelacion Servicio';
                                                    $arrayParametrosAuditoria["intOficinaId"]       = 0;
                                                    $arrayParametrosAuditoria["strLogin"]           = $objPuntoServicio->getLogin();
                                                    $arrayParametrosAuditoria["strUsrCreacion"]     = $usrCreacion;

                                                    $this->serviceInfoElemento->ingresaAuditoriaElementos($arrayParametrosAuditoria);

                                                    //historial del elemento
                                                    $objHistorialElemento = new InfoHistorialElemento();
                                                    $objHistorialElemento->setElementoId($objExtenderWyApCancel);
                                                    $objHistorialElemento->setObservacion("Se elimino el elemento por cancelacion de Servicio");
                                                    $objHistorialElemento->setEstadoElemento("Eliminado");
                                                    $objHistorialElemento->setUsrCreacion($usrCreacion);
                                                    $objHistorialElemento->setFeCreacion(new \DateTime('now'));
                                                    $objHistorialElemento->setIpCreacion($ipCreacion);
                                                    $this->emInfraestructura->persist($objHistorialElemento);
                                                    $this->emInfraestructura->flush();

                                                    $objInterfaceExtenderWyApCancel->setEstado("Eliminado");
                                                    $this->emInfraestructura->persist($objInterfaceExtenderWyApCancel);
                                                    $this->emInfraestructura->flush();
                                                }
                                                
                                                $objEnlaceExtenderWyApYOtro = $this->emInfraestructura
                                                                                   ->getRepository('schemaBundle:InfoEnlace')
                                                                                   ->findOneBy( array(  "interfaceElementoIniId" => 
                                                                                                        $objInterfaceExtenderWyApCancel,
                                                                                                        "estado"                 => 'Activo' ));
                                                if(is_object($objInterfaceOntAct) && is_object($objEnlaceExtenderWyApYOtro))
                                                {
                                                    $objEnlaceExtenderWyApYOtro->setEstado('Eliminado');
                                                    $this->emInfraestructura->persist($objEnlaceExtenderWyApYOtro);
                                                    $this->emInfraestructura->flush();

                                                    $objInfoEnlaceOntYOtro = new InfoEnlace();
                                                    $objInfoEnlaceOntYOtro->setInterfaceElementoIniId($objInterfaceOntAct);
                                                    $objInfoEnlaceOntYOtro->setInterfaceElementoFinId(
                                                        $objEnlaceExtenderWyApYOtro->getInterfaceElementoFinId());
                                                    $objInfoEnlaceOntYOtro->setTipoMedioId($objEnlaceExtenderWyApYOtro->getTipoMedioId());
                                                    $objInfoEnlaceOntYOtro->setTipoEnlace($objEnlaceExtenderWyApYOtro->getTipoEnlace());
                                                    $objInfoEnlaceOntYOtro->setEstado("Activo");
                                                    $objInfoEnlaceOntYOtro->setUsrCreacion($usrCreacion);
                                                    $objInfoEnlaceOntYOtro->setFeCreacion(new \DateTime('now'));
                                                    $objInfoEnlaceOntYOtro->setIpCreacion($ipCreacion);
                                                    $this->emInfraestructura->persist($objInfoEnlaceOntYOtro);
                                                    $this->emInfraestructura->flush();
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        
                        /**
                         * Se procede a buscar si existe algún producto EXTENDER_DUAL_BAND como servicio adicional Activo ó In-Corte 
                         * y se realiza la cancelación del servico en caso de existir
                         */
                        $arrayServiciosEdbConDataTecnica    = array();
                        $arrayRespuestaServiciosEdb         = $this->servicioGeneral
                                                                   ->obtenerServiciosPorProducto(
                                                                                                array("intIdPunto"                    => 
                                                                                                      $objPuntoServicio->getId(),
                                                                                                      "arrayNombresTecnicoProducto"   => 
                                                                                                      array("EXTENDER_DUAL_BAND"),
                                                                                                      "strCodEmpresa"             => $idEmpresa));
                        $arrayServiciosEdb                  = $arrayRespuestaServiciosEdb["arrayServiciosPorProducto"];
                        if(isset($arrayServiciosEdb) && !empty($arrayServiciosEdb) && $strEquipoNuevoEstaParametrizado == "NO")
                        {
                            foreach($arrayServiciosEdb as $objServicioProdEdb)
                            {
                                if($objServicioProdEdb->getEstado() === 'Activo' || $objServicioProdEdb->getEstado() === 'In-Corte')
                                {
                                    $strEstadoServicio                  = "Cancel";
                                    $strObservacionServicio             = "Se cancelo el servicio";
                                    $strNombreAccion                    = $objAccion->getNombreAccion();
                                    $arrayServiciosEdbConDataTecnica[]  = $objServicioProdEdb;
                                }
                                else
                                {
                                    $strEstadoServicio      = "Eliminado";
                                    $strObservacionServicio = "Se elimina el servicio";
                                    $strNombreAccion        = "";
                                }

                                $this->servicioGeneral->eliminaSolicitudesDualBand(array("intIdServicio" => $objServicioProdEdb->getId()));

                                $objServicioProdEdb->setEstado($strEstadoServicio);
                                $this->emComercial->persist($objServicioProdEdb);
                                $this->emComercial->flush();

                                //historial del servicio
                                $objServicioHistorial = new InfoServicioHistorial();
                                $objServicioHistorial->setServicioId($objServicioProdEdb);
                                $objServicioHistorial->setObservacion($objServicioProdEdb->getProductoId()->getDescripcionProducto()
                                                                      .": ".$strObservacionServicio);
                                $objServicioHistorial->setEstado($strEstadoServicio);
                                $objServicioHistorial->setUsrCreacion($usrCreacion);
                                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                                $objServicioHistorial->setIpCreacion($ipCreacion);
                                $objServicioHistorial->setAccion ($strNombreAccion);
                                $this->emComercial->persist($objServicioHistorial);
                                $this->emComercial->flush();
                            }
                            
                            if(isset($arrayServiciosEdbConDataTecnica) && !empty($arrayServiciosEdbConDataTecnica))
                            {
                                $objTipoSolicitudRetiro = $this->emComercial
                                                               ->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                               ->findOneBy(array("descripcionSolicitud" => "SOLICITUD RETIRO EQUIPO",
                                                                                 "estado"               => "Activo"));
                                if(is_object($objTipoSolicitudRetiro))
                                {
                                    $objSolicitudRetiro = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                               ->findOneBy(array("servicioId"      => $servicio->getId(), 
                                                                                 "tipoSolicitudId" => $objTipoSolicitudRetiro, 
                                                                                 "estado"          => "AsignadoTarea"),
                                                                           array('feCreacion' => 'DESC'));
                                    //caracteristica ELEMENTO CLIENTE
                                    $objCaracteristicaElementoCliente = $this->emComercial
                                                                             ->getRepository('schemaBundle:AdmiCaracteristica')
                                                                             ->find(360);
                                    if (is_object($objSolicitudRetiro) &&
                                        is_object($servicioTecnico) &&
                                        is_object($objCaracteristicaElementoCliente))
                                    {
                                        
                                        $arrayParams['intInterfaceElementoConectorId'] = $servicioTecnico->getInterfaceElementoClienteId();
                                        $arrayParams['strTipoSmartWifi']               = 'ExtenderDualBand';
                                        $arrayParams['arrayData']                      = array();
                                        $arrayElementosExtenderDualBand                = $this->emInfraestructura
                                                                                              ->getRepository('schemaBundle:InfoElemento')
                                                                                              ->getElementosSmartWifiByInterface($arrayParams);
                                        foreach($arrayElementosExtenderDualBand as $objElementoExtenderDualBand)
                                        {
                                            $objEntityCaract = new InfoDetalleSolCaract();
                                            $objEntityCaract->setCaracteristicaId($objCaracteristicaElementoCliente);
                                            $objEntityCaract->setDetalleSolicitudId($objSolicitudRetiro);
                                            $objEntityCaract->setValor($objElementoExtenderDualBand->getId());
                                            $objEntityCaract->setEstado("AsignadoTarea");
                                            $objEntityCaract->setUsrCreacion($usrCreacion);
                                            $objEntityCaract->setFeCreacion(new \DateTime('now'));
                                            $this->emComercial->persist($objEntityCaract);
                                            $this->emComercial->flush();
                                        }
                                    }
                                }
                            }
                            
                            /*
                             * Se deben eliminar los elemento Extender Dual Band de los enlaces
                             * existentes en el servicio de internet y al cancelar un extender dual band
                             * como prod adicional tb hay que eliminarlo de los enlaces
                             */
                            if(is_object($objServicioTecnicoAct))
                            {
                                $objInterfaceCli = $this->emInfraestructura
                                                        ->getRepository('schemaBundle:InfoInterfaceElemento')
                                                        ->find($objServicioTecnicoAct->getInterfaceElementoClienteId());
                                if (is_object($objInterfaceCli))
                                {
                                    $arrayParametrosEnlaceElementos = array();
                                    $arrayParametrosEnlaceElementos['objInterfaceEquipoNoExtender']   = $objInterfaceCli;
                                    $arrayParametrosEnlaceElementos['intInterfaceElementoConectorId'] = 
                                        $objServicioTecnicoAct->getInterfaceElementoClienteId();
                                    $arrayParametrosEnlaceElementos['strTipoElemento']                = 'ExtenderDualBand';
                                    $arrayParametrosEnlaceElementos['strUsrCreacion']                 = $usrCreacion;
                                    $arrayParametrosEnlaceElementos['strIpCreacion']                  = $ipCreacion;
                                    $arrayParametrosEnlaceElementos['strCodEmpresa']                  = $idEmpresa;
                                    $arrayParametrosEnlaceElementos['objPunto']                       = $objPuntoServicio;
                                    $this->eliminarEnlacesElementosExtenderDualBand($arrayParametrosEnlaceElementos);
                                }
                            }
                        }
                    }
                    catch (\Exception $objEx)
                    {
                        $strMensaje = $objEx->getMessage();
                        $this->serviceUtil->insertError('Telcos+', 
                                                        'InfoCambioElementoService->cambioElemento', 
                                                        $strMensaje, 
                                                        $usrCreacion, 
                                                        $ipCreacion);
                    }
                }
            }
            
            //Actualizo el ldap en el caso de los equipos huawei
            if( $ejecutaLdap =="SI")
            {   //reconfiguro en el ldap
                $mixResultadoJsonLdap = $this->servicioGeneral->ejecutarComandoLdap("A", $idServicio, $prefijoEmpresa);
                if($mixResultadoJsonLdap->status != "OK")
                {   
                    $strMsjErrorLdap = "Se ejecuto el cambio de elemento, pero no se ejecuto en el ldap ".$mixResultadoJsonLdap->mensaje;
                    // LOG 'CONFIRMACION_TN' ENVIO
                    if ($arrayDataConfirmacionTn["statusMiddleware"] === "OK") 
                    {
                        $this->serviceUtil->insertLog(array(
                        'enterpriseCode'      => 18,
                        'logType'             => 1,
                        'logOrigin'           => 'TELCOS',
                        'application'         => 'TELCOS',
                        'appClass'            => basename(__CLASS__),
                        'appMethod'           => basename(__FUNCTION__),
                        'descriptionError'    => 'PROCESO ANTES DE LA PETICION <br> '.$strMsjErrorLdap,
                        'status'              => 'EnvioErrorLdap',
                        'messageUser'         => $this->opcion,
                        'appAction'           => $this->strConfirmacionTNMiddleware."_".$servicio->getPuntoId()->getLogin(),
                        'inParameters'        => json_encode($arrayDataConfirmacionTn),
                        'creationUser'        => $usrCreacion));
                        $strErrorLog = "Proceso: CambioEquipoMD, Opción: ".$this->strConfirmacionTNMiddleware
                        .", Login: ".$servicio->getPuntoId()->getLogin()
                        .", appClass: ".basename(__CLASS__).", appMethod: ".basename(__FUNCTION__). ", creationUser: ".$usrCreacion
                        .", Status : EnvioErrorLdap, Mensaje: ".$strMsjErrorLdap. ", Descripcion: PROCESO ANTES DE LA PETICION";
                        error_log($strErrorLog);
                    }
                    $this->rdaMiddleware->ejecutaWsSinEsperarRespuestaMiddleware($arrayDataConfirmacionTn);
                    // LOG 'CONFIRMACION_TN' RESPUESTA
                    if ($arrayDataConfirmacionTn["statusMiddleware"] === "OK") 
                    {
                        $this->serviceUtil->insertLog(array(
                        'enterpriseCode'      => 18,
                        'logType'             => 1,
                        'logOrigin'           => 'TELCOS',
                        'application'         => 'TELCOS',
                        'appClass'            => basename(__CLASS__),
                        'appMethod'           => basename(__FUNCTION__),
                        'descriptionError'    => 'PROCESO DESPUES DE LA PETICION <br> '.$strMsjErrorLdap,
                        'status'              => 'RespuestaErrorLdap',
                        'messageUser'         => $this->opcion,
                        'appAction'           => $this->strConfirmacionTNMiddleware."_".$servicio->getPuntoId()->getLogin(),
                        'inParameters'        => json_encode($arrayDataConfirmacionTn),
                        'creationUser'        => $usrCreacion));
                        $strErrorLog = "Proceso: CambioEquipoMD, Opción: ".$this->strConfirmacionTNMiddleware
                        .", Login: ".$servicio->getPuntoId()->getLogin()
                        .", appClass: ".basename(__CLASS__).", appMethod: ".basename(__FUNCTION__). ", creationUser: ".$usrCreacion
                        .", Status : RespuestaErrorLdap, Mensaje: ".$strMsjErrorLdap. ", Descripcion: PROCESO DESPUES DE LA PETICION";
                        error_log($strErrorLog);
                    }
                    $respuestaArray[0] = array('status' => "OK",
                                               'mensaje' => $strMsjErrorLdap);
                    return $respuestaArray;
                }
            }
            
            //VALIDAR QUE SEA MD Y LANZAR EL PROCESO DE VALIDACIÓN DE PROMOCIONES BW
            if($prefijoEmpresa=="MD" || $prefijoEmpresa=="EN")
            {
                $arrayParametrosInfoBw = array();
                $arrayParametrosInfoBw['intIdServicio']     = $servicio->getId();
                $arrayParametrosInfoBw['intIdEmpresa']      = $idEmpresa;
                $arrayParametrosInfoBw['strTipoProceso']    = "CAMBIO_EQUIPO";
                $arrayParametrosInfoBw['strValor']          = $servicio->getId();
                $arrayParametrosInfoBw['strUsrCreacion']    = $usrCreacion;
                $arrayParametrosInfoBw['strIpCreacion']     = $ipCreacion;
                $arrayParametrosInfoBw['strPrefijoEmpresa'] = $prefijoEmpresa;
                $this->servicePromociones->configurarPromocionesBW($arrayParametrosInfoBw);
            }
        }
        catch (\Exception $e) {
            if ($this->emInfraestructura->getConnection()->isTransactionActive()){
                $this->emInfraestructura->getConnection()->rollback();
            }
            
            if ($this->emComercial->getConnection()->isTransactionActive()){
                $this->emComercial->getConnection()->rollback();
            }
            
            if ($this->emNaf->getConnection()->isTransactionActive()){
                $this->emNaf->getConnection()->rollback();
            }
            
            if($mensaje == "")
            {
                $mensaje = "Ocurrio un ERROR. Por favor Notifiquelo a Sistemas.";
                $this->serviceUtil->insertError('Telcos+', 'CambioElemento', $e->getMessage(),$usrCreacion,$ipCreacion);
            }
            $arrayDataConfirmacionTn['datos']['respuesta_confirmacion'] = "ERROR";
            $respuestaArray[0] = array('status'=>"ERROR", 'mensaje'=>$mensaje);
        }

        $this->emInfraestructura->getConnection()->close();
        $this->emComercial->getConnection()->close();
        //*----------------------------------------------------------------------*/
        // LOG 'CONFIRMACION_TN' ENVIO
        if ($arrayDataConfirmacionTn["statusMiddleware"] === "OK") 
        {
              $this->serviceUtil->insertLog(array(
                        'enterpriseCode'      => 18,
                        'logType'             => 1,
                        'logOrigin'           => 'TELCOS',
                        'application'         => 'TELCOS',
                        'appClass'            => basename(__CLASS__),
                        'appMethod'           => basename(__FUNCTION__),
                        'descriptionError'    => 'PROCESO ANTES DE LA PETICION',
                        'status'              => 'EnvioOK',
                        'messageUser'         => $this->opcion,
                        'appAction'           => $this->strConfirmacionTNMiddleware."_".$servicio->getPuntoId()->getLogin(),
                        'inParameters'        => json_encode($arrayDataConfirmacionTn),
                        'creationUser'        => $usrCreacion));
              $strErrorLog = "Proceso: CambioEquipoMD, Opción: ".$this->strConfirmacionTNMiddleware
                .", Login: ".$servicio->getPuntoId()->getLogin()
                .", appClass: ".basename(__CLASS__).", appMethod: ".basename(__FUNCTION__). ", creationUser: ".$usrCreacion
                .", Status : ERROR, Mensaje: ".$mensaje. ", Descripcion: PROCESO ANTES DE LA PETICION";
              error_log($strErrorLog);
        }
        $this->rdaMiddleware->ejecutaWsSinEsperarRespuestaMiddleware($arrayDataConfirmacionTn);
        // LOG 'CONFIRMACION_TN' RESPUESTA
        if ($arrayDataConfirmacionTn["statusMiddleware"] === "OK") 
        {
              $this->serviceUtil->insertLog(array(
                        'enterpriseCode'      => 18,
                        'logType'             => 1,
                        'logOrigin'           => 'TELCOS',
                        'application'         => 'TELCOS',
                        'appClass'            => basename(__CLASS__),
                        'appMethod'           => basename(__FUNCTION__),
                        'descriptionError'    => 'PROCESO DESPUES DE LA PETICION',
                        'status'              => 'RespuestaOK',
                        'messageUser'         => $this->opcion,
                        'appAction'           => $this->strConfirmacionTNMiddleware."_".$servicio->getPuntoId()->getLogin(),
                        'inParameters'        => json_encode($arrayDataConfirmacionTn),
                        'creationUser'        => $usrCreacion));
              $strErrorLog = "Proceso: CambioEquipoMD, Opción: ".$this->strConfirmacionTNMiddleware
                .", Login: ".$servicio->getPuntoId()->getLogin()
                .", appClass: ".basename(__CLASS__).", appMethod: ".basename(__FUNCTION__). ", creationUser: ".$usrCreacion
                .", Status : RespuestaOK, Mensaje: ".$mensaje. ", Descripcion: PROCESO DESPUES DE LA PETICION";
              error_log($strErrorLog);
        }
        return $respuestaArray;
    }
    
    /**
     * eliminarEnlacesElementosExtenderDualBand
     * 
     * Método que elimina los elementos Extender Dual Band de los enlaces del servicio Internet del cliente
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0
     * @since 1.0
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 Se agrega eliminación de equipos a nivel de la INFO_ELEMENTO e INFO_HISTORIAL_ELEMENTO de los equipos Extender
     * 
     * @param Array $arrayParametros [
     *                                objInterfaceEquipoNoExtender     Objeto de interface de un equipo que no es extender
     *                                intInterfaceElementoConectorId   Objeto de interface inicial usado para el proceso de recuperación de elementos
     *                                strTipoElemento                  String que indica el tipo de elemento "ExtenderDualBand"
     *                                strUsrCreacion                   Usuario que ejecuta la operación
     *                                strIpCreacion                    Ip del usuario que ejecuta la oeracinó
     *                               ]
     * @return $arrayData ó $objInterfaceElementoFinPlan ó $objInterfaceFin según los parametros enviados en el método
     */
    public function eliminarEnlacesElementosExtenderDualBand($arrayParametros)
    {
        $objInterfaceEquipoNoExtender   = isset($arrayParametros['objInterfaceEquipoNoExtender'])?$arrayParametros['objInterfaceEquipoNoExtender']:null;
        $intInterfaceElementoConectorId = isset($arrayParametros['intInterfaceElementoConectorId'])?
                                          $arrayParametros['intInterfaceElementoConectorId']:null;
        $strTipoElemento                = isset($arrayParametros['strTipoElemento'])?$arrayParametros['strTipoElemento']:null;
        $strUsrCreacion                 = isset($arrayParametros['strUsrCreacion'])?$arrayParametros['strUsrCreacion']:null;
        $strIpCreacion                  = isset($arrayParametros['strIpCreacion'])?$arrayParametros['strIpCreacion']:null;
        $strCodEmpresa                  = isset($arrayParametros['strCodEmpresa'])?$arrayParametros['strCodEmpresa']:null;
        $objPunto                       = $arrayParametros['objPunto'];
        $arrayParametrosAuditoria       = array();
        try
        {
            $objEnlace = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                              ->findOneBy( array("interfaceElementoIniId" => $intInterfaceElementoConectorId,
                                                 "estado"                 => 'Activo' ));
            // se verifica si existe enlace conectado al interface 
            if(is_object($objEnlace))
            {
                // Se obtiene el interfaceElementoFinal
                $objInterfaceFin = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                             ->find($objEnlace->getInterfaceElementoFinId());
                // Se obtiene el elementoFinal
                $objElementoFin = $objInterfaceFin->getElementoId();
                if (is_object($objElementoFin))
                {
                    if (strpos($objElementoFin->getNombreElemento(), $strTipoElemento) !== false)
                    {
                        $objEnlace->setEstado('Eliminado');
                        $this->emInfraestructura->persist($objEnlace);
                        $this->emInfraestructura->flush();

                        //se eliminan elementos del servicio
                        $objElementoFin->setEstado("Eliminado");
                        $this->emInfraestructura->persist($objElementoFin);
                        $this->emInfraestructura->flush();

                        //SE REGISTRA EL TRACKING DEL ELEMENTO
                        $arrayParametrosAuditoria["strNumeroSerie"]  = $objElementoFin->getSerieFisica();
                        $arrayParametrosAuditoria["strEstadoTelcos"] = 'Eliminado';
                        $arrayParametrosAuditoria["strEstadoNaf"]    = 'Instalado';
                        $arrayParametrosAuditoria["strEstadoActivo"] = 'Cancelado';
                        $arrayParametrosAuditoria["strUbicacion"]    = 'Cliente';
                        $arrayParametrosAuditoria["strCodEmpresa"]   = $strCodEmpresa;
                        $arrayParametrosAuditoria["strTransaccion"]  = 'Cancelacion Servicio';
                        $arrayParametrosAuditoria["intOficinaId"]    = 0;
                        if(is_object($objPunto))
                        {
                            $arrayParametrosAuditoria["strLogin"] = $objPunto->getLogin();
                        }

                        $arrayParametrosAuditoria["strUsrCreacion"] = $strUsrCreacion;

                        $this->serviceInfoElemento->ingresaAuditoriaElementos($arrayParametrosAuditoria);

                        //historial del elemento
                        $objHistorialElemento = new InfoHistorialElemento();
                        $objHistorialElemento->setElementoId($objElementoFin);
                        $objHistorialElemento->setObservacion("Se elimino el elemento por cancelacion de Servicio");
                        $objHistorialElemento->setEstadoElemento("Eliminado");
                        $objHistorialElemento->setUsrCreacion($strUsrCreacion);
                        $objHistorialElemento->setFeCreacion(new \DateTime('now'));
                        $objHistorialElemento->setIpCreacion($strIpCreacion);
                        $this->emInfraestructura->persist($objHistorialElemento);
                        $this->emInfraestructura->flush();

                        //eliminar puertos elemento
                        $arrayInterfacesElemento    = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                              ->findBy(array("elementoId" => $objElementoFin->getId()));

                        foreach($arrayInterfacesElemento as $objInterfaceElemento)
                        {
                            $objInterfaceElemento->setEstado("Eliminado");
                            $this->emInfraestructura->persist($objInterfaceElemento);
                            $this->emInfraestructura->flush();
                        }
                    }
                    else
                    {
                        //validar que sean diferentes interface
                        $objEnlaceExiste = $this->emInfraestructura
                                          ->getRepository('schemaBundle:InfoEnlace')
                                          ->findOneBy( array("interfaceElementoIniId" => $objInterfaceEquipoNoExtender->getId(),
                                                             "interfaceElementoFinId" => $objInterfaceFin->getId(),
                                                             "estado"                 => 'Activo' ));
                        if(!is_object($objEnlaceExiste))
                        {
                            //Se crea el nuevo enlace
                            $objInfoEnlace = new InfoEnlace();
                            $objInfoEnlace->setInterfaceElementoIniId($objInterfaceEquipoNoExtender);
                            $objInfoEnlace->setInterfaceElementoFinId($objInterfaceFin);
                            $objInfoEnlace->setTipoMedioId($objEnlace->getTipoMedioId());
                            $objInfoEnlace->setTipoEnlace($objEnlace->getTipoEnlace());
                            $objInfoEnlace->setEstado("Activo");
                            $objInfoEnlace->setUsrCreacion($strUsrCreacion);
                            $objInfoEnlace->setFeCreacion(new \DateTime('now'));
                            $objInfoEnlace->setIpCreacion($strIpCreacion);
                            $this->emInfraestructura->persist($objInfoEnlace);
                        }
                        $objInterfaceEquipoNoExtender = $objInterfaceFin;
                    }
                    //se invoca recursivamente el metodo
                    $arrayParams = array();
                    $arrayParams['intInterfaceElementoConectorId'] = $objInterfaceFin->getId();
                    $arrayParams['strTipoElemento']                = $strTipoElemento;
                    $arrayParams['objInterfaceEquipoNoExtender']   = $objInterfaceEquipoNoExtender;
                    $arrayParams['strUsrCreacion']                 = $strUsrCreacion;
                    $arrayParams['strIpCreacion']                  = $strIpCreacion;
                    return $this->eliminarEnlacesElementosExtenderDualBand($arrayParams);
                }
            }
            return "OK";
        } 
        catch (\Exception $objEx)
        {
            $this->serviceUtil->insertError('Telcos+', 
                                            'InfoCambioElementoService.eliminarEnlacesElementosExtenderDualBand', 
                                            $objEx->getMessage(),
                                            $strUsrCreacion, 
                                            $strIpCreacion
                                           );
            return "ERROR";
        }
    }
    
    /**
     * Service que realiza el cambio del elemento del cliente, con ejecucion de scripts
     * 
     * @author Modificado: John Vera <javera@telconet.ec>
     * @version 1.1 29-05-2015
     * 
     * @author Modificado: Jesus Bozada <jbozada@telconet.ec>
     * @version 1.2 10-10-2016
     * 
     * @author Modificado: Jesus Bozada <jbozada@telconet.ec>
     * @version 1.3 01-03-2017     Se agreg programación para procesar cambios de elementos SmartWifi
     * @since 1.2
     * 
     * @author Francisco Adum <fadum@netlife.net.ec>
     * @version 1.4 14-05-2017  Se agrega bandera para ejecutar scripts por middleware
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.5 07-08-2018 Se agrega verificación del modelo de olt C320 para que se invoque a la función cambioElementoZte 
     *                         y se siga la programación para servicios con factibilidad en olts de tecnología ZTE
     * 
     * @author Modificado: Jesus Bozada <jbozada@telconet.ec>
     * @version 1.6 20-10-2018     Se modifica condición IF para procesar correctamente el cambio de elemento
     * @since 1.5
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.7 03-12-2018 Se agrega flujo para cambio de elementos Extender Dual Band
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.8 03-12-2018 Se agrega programación para poder retornar el tipo de cambio de equipo realizado
     *                         mediante el parámetro "strTipoCambioElemento" 
     * @since 1.7

     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.9 11-05-2020 Se unifica las validaciones por marca y no por modelo de olt
     * 
     * @param Array $arrayParametros [ 
     *                                 servicio               => Objeto de la tabla InfoServicio
     *                                 servicioTecnico        => Objeto de la tabla InfoServicioTecnico
     *                                 modeloElemento         => Objeto de la tabla AdmiModeloElemento
     *                                 interfaceElemento      => Objeto de la tabla InfoInterfaceElemento
     *                                 producto               => Objeto de la tabla AdmiProducto
     *                                 serieCpe               => Cadena de caracteres con serie de cpe 
     *                                 codigoArticulo         => Cadena de caracteres con modelo de cpe
     *                                 nombreCpe              => Cadena de caracteres con nombre de cpe
     *                                 descripcionCpe         => Cadena de caracteres con descripcion de cpe
     *                                 macCpe                 => Cadena de caracteres con mac de cpe
     *                                 tipoElementoCpe        => Cadena de caracteres con tipo de elemento de cpe 
     *                                 idEmpresa              => Cadena de caracteres con identificador de empresa
     *                                 idElementoCliente      => Numero entero identificador de elemento del cliente
     *                                 intIdElementoWifi      => Numero entero identificador del elemento wifi adicional a cambiar
     *                                 strModeloWifi          => Cadena de caracteres con modelo de wifi
     *                                 strMacWifi             => Cadena de caracteres con mac de wifi,
     *                                 strSerieWifi           => Cadena de caracteres con serie de wifi
     *                                 strDescripcionWifi     => Cadena de caracteres con descripcion de wifi
     *                                 strNombreWifi          => Cadena de caracteres con nombre de wifi
     *                                 strTieneMigracionHw    => Cadena de caracteres que indica si el cliente tiene una solicitud 
     *                                                           de migracion finalizada
     *                                 strEquipoCpeHw         => Cadena de caracteres que indica el tipo de operacion a realizar con 
     *                                                           el equipo CPE HW, ('MANTENER EQUIPO','CAMBIAR EQUIPO')
     *                                 strEquipoWifiAdicional => Cadena de caracteres que indica el tipo de operacion a realizar con 
     *                                                           el equipo WIFI ADICIONAL, ('MANTENER EQUIPO','CAMBIAR EQUIPO')
     *                                 strAgregarWifi         => Cadena de caracteres que indica si se desea agregar un Wifi adicional 
     *                                                           para clientes hw
     *                                 strEsSmartWifi         => Cadena de caracteres que indica si se esta realizando el cambio de equipo SmartWifi
     *                                 strEsPlan              => Cadena de caracteres que indica si un servicio es plan o producto
     *                                 usrCreacion            => Cadena de caracteres con usuario de creacion a utilizar
     *                                 ipCreacion             => Cadena de caracteres con ip de creacion a utilizar
     *                                 prefijoEmpresa         => Cadena de caracteres con prefijo de empresa a utilizar,
     *                                 strEsExtenderDualBand  => Cadena de caracteres que indica si se está realizando el cambio de equipo 
     *                                                           Extender Dual Band
     *                                 strEsCambioPorSoporte  => Cadena de caracteres que indica si se está realizando el cambio de equipo mediante solicitud de soporte
     *                               ]
     */    
    public function cambioElementoMd($arrayParametros)
    {
        $strTipoCambioElemento = "";
        if($arrayParametros['strEsSmartWifi'] == "SI")
        {
            $strTipoCambioElemento = "SmartWifi";
            $arrayFinal = $this->cambioElementoSmartWifi($arrayParametros);
        }
        else if($arrayParametros['strEsApWifi'] == "SI")
        {
            $strTipoCambioElemento = "ApWifi";
            $arrayFinal = $this->cambioElementoApWifi($arrayParametros);
        }
        else if($arrayParametros['strEsExtenderDualBand'] == "SI")
        {
            $strTipoCambioElemento = "ExtenderDualBand";
            $arrayFinal = $this->cambioElementoExtenderDualBand($arrayParametros);
        }
        else if($arrayParametros['strMarcaOlt'] == "HUAWEI")
        {
            $strTipoCambioElemento = "HUAWEI";
            $arrayFinal = $this->cambioElementoHuawei($arrayParametros);
        }
        else if($arrayParametros['strMarcaOlt'] == "TELLION")
        {
            $strTipoCambioElemento = "TELLION";
            $arrayFinal = $this->cambioElementoTellion($arrayParametros);
        }
        else if($arrayParametros['strMarcaOlt'] == "ZTE")
        {
            $strTipoCambioElemento = "ZTE";
            $arrayFinal = $this->cambioElementoZte($arrayParametros);
        }
        else
        {
            $arrayFinal[] = array('status'  => "ERROR", 
                                  'mensaje' => "El modelo " . $arrayParametros['modeloElemento'] . " no esta soportado.");
        }
        $arrayFinal[0]["strTipoCambioElemento"] = $strTipoCambioElemento;
        return $arrayFinal;
    }

    /**
     * Service que realiza el cambio del elemento del cliente en equipos tellion, con ejecucion de scripts
     *
     * @author Modificado: John Vera <javera@telconet.ec>
     * @version 1.0 modificado:29-05-2015
     * 
     * @author Modificado: Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 modificado:14-12-2016    Se agrega seteo de variable que almacena Objeto de Producto Ip a utilizar en el proceso
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 06-09-2017 -  En la tabla INFO_DETALLE_ASIGNACION se guarda el PersonaEmpresaRolId del responsable de la tarea de retiro de
     *                            equipo
     * 
     * @author Modificado: Allan Suarez <arsuarez@telconet.ec>
     * @version 1.3 modificado:15-11-2017    Se corrige el indice en la busqueda de Productos de Ips Adicionales para compararlas 
     *                                       con las obtenidas en el Plan Det del Servicios, el proceso se lo realiza en un bucle 
     *                                       anidado en el cual se estaba enviando el indice mas externo a buscar en el arreglo 
     *                                       siendo corregido por el indice del bucle mas interno ( Regularización cambios realizados en caliente )
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.4 21-12-2017 - En la tabla INFO_DETALLE_ASIGNACION se registra el campo tipo asignado 'EMPLEADO'
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.5 08-02-2018 - Se realizan ajustes para que al momento de ejecutar el cambio de elemento, la nueva serie se asocie a un nuevo
     *                           elemento.
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.6 01-03-2018 Se registra tracking del elemento
     *
     * @author Modificado: Jesus Bozada <jbozada@telconet.ec>
     * @version 1.7 06-03-2018 - Se agregan correcciones en creacion de elementos y en generación de solicitudes de retiro de equipos
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.8 21-02-2018 - Se realizan ajustes para corregir bug al momento de realizar un cambio de ont, no se estan actualizando la
     *                           nueva serie ni la mac
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.9 17-04-2018 - Se agrega validación para actualizar la MAC en los servicios con productos IP adicionales cuando el tipo de negocio
     *                           es PRO y el OLT tiene middleware
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.0 29-05-2018 - Se agrega el parametro del id_persona en el registro de la trazabilidad
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.1 30-05-2018 - Se considera realizar el cambio de elemento para servicios Small Business
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.2 03-07-2018 - Se agregan validaciones necesarias para cambios de equipos CPE en servicios Small Business
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.3 29-10-2018 - Se obtiene el valor correcto para la variable ip_fijas_activas enviada al middleware cuando se realiza 
     *                           un cambio de modem inmediato de tipo CPE ONT
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.4 27-02-2019 - Se agregan validaciones para realizar cambios de equipos Tellion de servicios TelcoHome
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 2.5 08-11-2019 Se agrega descripción de producto en la búsqueda de la función findOneBy, ya que actualmente
     *                          para el producto INTERNET SMALL BUSINESS existen n registros con el mismo nombre tecnico.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 2.6 12-11-2019 Se agrega lógica para que la búsqueda del producto sea dinámico tanto para MD como para TN.
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.7 04-05-2020 - Se elimina la función obtenerInfoMapeoProdPrefYProdsAsociados y en su lugar se usa obtenerParametrosProductosTnGpon,
     *                            debido a los cambios realizados por la reestructuración de servicios Small Business
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 2.8 07-08-2020 - Se valida el response del middleware para cambio de equipo en los servicios TN.
     *                           (Internet Small Business y TelcoHome)
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.9 01-12-2020 Se agrega verificación de equipo en NAF de acuerdo a validaciones existentes en AFK_PROCESOS.IN_P_PROCESA_INSTALACION,
     *                          antes de enviar el request a middleware, para evitar errores por NAF que obliguen la eliminación de línea pon
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 3.0 16-11-2020 Se agregan validación para ejecutar cambio de planes PYME con nuevos parámetros enviados al middleware
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 3.1 10-11-2021 Se construye el arreglo con la información que se enviará al web service para confirmación de opción de Tn a Middleware
     */
    public function cambioElementoTellion($arrayParametros)
    {
        $servicio           = $arrayParametros['servicio'];
        $servicioTecnico    = $arrayParametros['servicioTecnico'];
        $modeloElemento     = $arrayParametros['modeloElemento'];
        $interfaceElemento  = $arrayParametros['interfaceElemento'];
        $producto           = $arrayParametros['producto'];
        $serieCpe           = $arrayParametros['serieCpe'];
        $codigoArticulo     = $arrayParametros['codigoArticulo'];
        $nombreCpe          = $arrayParametros['nombreCpe'];
        $descripcionCpe     = $arrayParametros['descripcionCpe'];
        $macCpe             = $arrayParametros['macCpe'];
        $tipoElementoCpe    = $arrayParametros['tipoElementoCpe'];
        $idEmpresa          = $arrayParametros['idEmpresa'];
        $idElementoCliente  = $arrayParametros['idElementoCliente'];
        $usrCreacion        = $arrayParametros['usrCreacion'];
        $ipCreacion         = $arrayParametros['ipCreacion'];
        $prefijoEmpresa     = $arrayParametros['prefijoEmpresa'];
        $strEsIsb           = $arrayParametros['strEsIsb'] ? $arrayParametros['strEsIsb'] : "NO";
        $flagMiddleware     = false;
        $arrayParametrosAuditoria = array();
        $boolValidaModeloCpeWifiIsb = false;
        $arrayDatosConfirmacionTn   = array();
        
        try
        {
            //verifico si el olt esta aprovisionando el CNR
            $objDetalleElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                          ->findOneBy(array('detalleNombre' => 'OLT MIGRADO CNR',
                                                                            'elementoId' => $interfaceElemento->getElementoId()->getId()));   

            //buscar de mac ont
            $servProdCaractMacOnt = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "MAC ONT", $producto);

            //REVISAR SI EL ELEMENTO ES PARTE DEL PILOTO DEL MIDDLEWARE
            $objDetalleElementoMid  = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                            ->findOneBy(array(  "elementoId"   => $servicioTecnico->getElementoId(),
                                                                "detalleNombre"=> 'MIDDLEWARE',
                                                                "estado"       => 'Activo'));

            //OBTENER TIPO DE NEGOCIO
            $strTipoNegocio     = $servicio->getPuntoId()->getTipoNegocioId()->getNombreTipoNegocio();

            if($objDetalleElementoMid)
            {
                if($objDetalleElementoMid->getDetalleValor() == 'SI')
                {
                    $flagMiddleware = true;
                }
            }

            //OBTENER DATOS DEL CLIENTE (NOMBRES E IDENTIFICACION)
            $objPersona         = $servicio->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId();
            $strIdentificacion  = $objPersona->getIdentificacionCliente();
            $strNombreCliente   = $objPersona->__toString();

            $login                  = $servicio->getPuntoId()->getLogin();
            $tipoArticulo           = "AF";
            $identificacionCliente  = "";
            $strEstadoDetalleSol    = "";
            if($strEsIsb === "SI")
            {
                if(!is_object($producto))
                {
                    throw new \Exception('No existe producto asociado al servicio');
                }
                $intIdProdPref              = $producto->getId();
                $strDescripcionProdPref     = $producto->getDescripcionProducto();
                $strNombreTecnicoProdPref   = $producto->getNombreTecnico();
                $arrayParamsInfoProds       = array("strValor1ParamsProdsTnGpon"    => "PRODUCTOS_RELACIONADOS_INTERNET_IP",
                                                    "strCodEmpresa"                 => $idEmpresa,
                                                    "intIdProductoInternet"         => $intIdProdPref);
                $arrayInfoMapeoProds        = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                ->obtenerParametrosProductosTnGpon($arrayParamsInfoProds);
                if($tipoElementoCpe === "CPE WIFI" || ($tipoElementoCpe === "CPE" && strrpos($nombreCpe, "wifi")))
                {
                    if($strNombreTecnicoProdPref === "INTERNET SMALL BUSINESS")
                    {
                        $boolSbConIp    = false;
                        if(isset($arrayInfoMapeoProds) && !empty($arrayInfoMapeoProds))
                        {
                            foreach($arrayInfoMapeoProds as $arrayInfoProd)
                            {
                                $intIdProductoIp            = $arrayInfoProd["intIdProdIp"];
                                $objProdIPSB                = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                                                ->find($intIdProductoIp);
                                $arrayServiciosPuntoIPSB    = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                                ->findBy(array( "puntoId"       => $servicio->getPuntoId(),
                                                                                                "productoId"    => $objProdIPSB,
                                                                                                "estado"        => 
                                                                                                array(  "PreAsignacionInfoTecnica",
                                                                                                        "Asignada",
                                                                                                        "Activo")
                                                                                       ));
                                if(isset($arrayServiciosPuntoIPSB) && !empty($arrayServiciosPuntoIPSB))
                                {
                                    $boolSbConIp = true;
                                }
                            }
                        }
                        else
                        {
                            throw new \Exception('No se ha podido obtener el correcto mapeo del servicio con la ip respectiva');
                        }

                        if($boolSbConIp)
                        {
                            $strTipoIp = "CON_IP";
                        }
                        else
                        {
                            $strTipoIp = "SIN_IP";
                        }
                    }
                    else
                    {
                        $strTipoIp = "SIN_IP";
                    }
                    $arrayParamsValidarCpeWifi  = array("strModeloCpeWifi"          => $codigoArticulo,
                                                        "strMacCpeWifi"             => $macCpe,
                                                        "strCodEmpresa"             => $idEmpresa,
                                                        "strTipoBusqueda"           => $strTipoIp,
                                                        "strProdBusqueda"           => $strNombreTecnicoProdPref,
                                                        "strAccion"                 => "ACTIVAR",
                                                        "strDescripcionProdPref"    => $strDescripcionProdPref);
                    $arrayValidacionCpeWifiIsb  = $this->activarService->validarCpeWifiTellionSB($arrayParamsValidarCpeWifi);
                    if($arrayValidacionCpeWifiIsb["strStatus"] !== "OK")
                    {
                        $arrayRespuestaValidacionIsb[] = array( 'status'    => $arrayValidacionCpeWifiIsb["strStatus"], 
                                                                'mensaje'   => $arrayValidacionCpeWifiIsb["strMensaje"]);
                        return $arrayRespuestaValidacionIsb;
                    }
                    $tipoElementoCpe            = "CPE WIFI";
                    $boolValidaModeloCpeWifiIsb = true;
                }
                if($strNombreTecnicoProdPref === "INTERNET SMALL BUSINESS")
                {
                    $strTipoNegocio = "PYME";
                }
                else
                {
                    $strTipoNegocio = "HOME";
                }
            }
        
            $arrayVerifCpeNaf   = $this->servicioGeneral->buscarEquipoEnNafPorParametros(array( "serieEquipo"           => $serieCpe,
                                                                                                "estadoEquipo"          => "PI",
                                                                                                "tipoArticuloEquipo"    => "AF",
                                                                                                "modeloEquipo"          => $codigoArticulo));
            if($arrayVerifCpeNaf["status"] === "ERROR")
            {
                $arrayRespuesta[]   = array("status"  => $arrayVerifCpeNaf["status"],
                                            "mensaje" => $arrayVerifCpeNaf["mensaje"]);
                return $arrayRespuesta;
            }
            
            if($tipoElementoCpe=="CPE ONT")
            {
                $caracteristicaMac = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                       ->findOneBy(array( "descripcionCaracteristica" => "MAC ONT", "estado"=>"Activo"));
                $productoCaracteristicaMac = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                ->findOneBy(array( "productoId" => $producto->getId(), 
                                                                   "caracteristicaId"=>$caracteristicaMac->getId()));

                if($flagMiddleware)
                {
                    $intNumIpEnPlan = 0;
                    if($strEsIsb === "SI")
                    {
                        $arrayProdIp        = array();
                        $objProdInternet    = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($intIdProdPref);
                        if($strNombreTecnicoProdPref === "INTERNET SMALL BUSINESS")
                        {
                            if(isset($arrayInfoMapeoProds) && !empty($arrayInfoMapeoProds))
                            {
                                foreach($arrayInfoMapeoProds as $arrayInfoProd)
                                {
                                    $intIdProductoIp    = $arrayInfoProd["intIdProdIp"];
                                    $objProdIPSB        = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                                            ->find($intIdProductoIp);
                                    $arrayProdIp[]      = $objProdIPSB;
                                }
                            }
                            else
                            {
                                $strMensaje = "No se ha podido obtener el correcto mapeo del servicio con la ip respectiva";
                                throw new \Exception($strMensaje);
                            }
                            $intNumIpEnPlan = 1;
                        }
                        else
                        {
                            $intNumIpEnPlan = 0;
                        }
                    }
                    else
                    {
                        $objProdInternet    = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                                ->findOneBy(array(  "nombreTecnico" => "INTERNET",
                                                                                    "empresaCod"    => $idEmpresa, 
                                                                                    "estado"        => "Activo"));

                        $arrayProdIp        = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                                ->findBy(array( "nombreTecnico" => "IP",
                                                                                "empresaCod"    => $idEmpresa, 
                                                                                "estado"        => "Activo"));
                    }
                    $objPlanCab = $servicio->getPlanId();
                    if(is_object($objPlanCab))
                    {
                        $arrayPlanDet = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')->findBy(array("planId"=>$objPlanCab->getId()));
                        //verifica si plan del servicio tiene ip 
                        for($intI = 0; $intI < count($arrayPlanDet); $intI++)
                        {
                            for($intJ = 0; $intJ < count($arrayProdIp); $intJ++)
                            {
                                if($arrayPlanDet[$intI]->getProductoId() == $arrayProdIp[$intJ]->getId())
                                {
                                    $intNumIpEnPlan = 1;
                                    break;
                                }
                            }
                            if($intNumIpEnPlan === 1)
                            {
                                break;
                            }
                        }
                    }
                    $arrayServiciosPunto    = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                ->findBy(array("puntoId" => $servicio->getPuntoId()->getId()));
                    $intContadorIpsFijas    = 0;
                    $arrayServicioIpAdicionales = array();
                    for($intIndex = 0; $intIndex < count($arrayServiciosPunto); $intIndex++)
                    {
                        $objServicioPunto = $arrayServiciosPunto[$intIndex];
                        if( is_object($objServicioPunto) 
                            && ($objServicioPunto->getEstado() == "Activo" || $objServicioPunto->getEstado() == "In-Corte" ) 
                            && $objServicioPunto->getId() != $servicio->getId())
                        {
                            if(is_object($objServicioPunto->getPlanId()))
                            {
                                $objPlanCabServPunto    = $this->emComercial->getRepository('schemaBundle:InfoPlanCab')
                                                                            ->find($objServicioPunto->getPlanId()->getId());
                                $objPlanDetServPunto    = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                                                            ->findBy(array("planId" => $objPlanCabServPunto->getId()));

                                for($intIndexJ = 0; $intIndexJ < count($objPlanDetServPunto); $intIndexJ++)
                                {
                                    foreach($arrayProdIp as $objProductoIp)
                                    {
                                        if($objProductoIp->getId() == $objPlanDetServPunto[$intIndexJ]->getProductoId())
                                        {
                                            $arrayServicioIpAdicionales[] = array("idServicio" => $objServicioPunto->getId());
                                            $intContadorIpsFijas++;
                                        }
                                    }
                                }
                            }
                            else
                            {
                                $objProductoServicioPunto = $objServicioPunto->getProductoId();
                                foreach($arrayProdIp as $objProductoIp)
                                {
                                    if($objProductoIp->getId() === $objProductoServicioPunto->getId())
                                    {
                                        $arrayServicioIpAdicionales[]   = array("idServicio" => $objServicioPunto->getId());
                                        $intContadorIpsFijas++;
                                    }
                                }
                            }
                        }
                    }
                    $intIpsFijasActivas = 0;
                    $intNumTotalIpsPto  = $intNumIpEnPlan + $intContadorIpsFijas;
                    if($intNumTotalIpsPto > 0)
                    {
                        $arrayServiciosPuntoActivo  = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                        ->findBy(array("puntoId" => $servicio->getPuntoId(), "estado" => "Activo"));
                        $arrayDatosIps              = $this->servicioGeneral->getInfoIpsFijaPunto($arrayServiciosPuntoActivo, $arrayProdIp, $servicio,
                                                                                                  'Activo', 'Activo', $objProdInternet);
                        $intIpsFijasActivas         = $arrayDatosIps['ip_fijas_activas'];
                    }
                    //buscar indice del servicio
                    $servProdCaractIndiceCliente = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "INDICE CLIENTE", $producto);

                    //buscar mac wifi
                    $servProdCaractMacWifi  = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "MAC WIFI", $producto);
                    if($servProdCaractMacWifi)
                    {
                        $strMacWifi = $servProdCaractMacWifi->getValor();
                    }
                    else
                    {
                        $strMacWifi = "";
                    }

                    //servicio prod caract - mac (ont o wifi)
                    $servicioProdCaractMac = $servProdCaractMacOnt;

                    //buscar perfil del servicio
                    $servProdCaractPerfil = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "PERFIL", $producto);
                    if($servProdCaractPerfil)
                    {
                        $strPerfil  = $servProdCaractPerfil->getValor();
                        $arrayPerfil= explode("_", $strPerfil);
                        if($strEsIsb === "SI")
                        {
                            $strPerfil  = $arrayPerfil[0]."_".$arrayPerfil[1]."_".$arrayPerfil[2];
                        }
                        else
                        {
                            $strPerfil  = $arrayPerfil[0]."_".$arrayPerfil[1];
                        }
                    }

                    //OBTENER IP DEL ELEMENTO
                    $objIpElemento      = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                ->findOneBy(array("elementoId" => $interfaceElemento->getElementoId()));

                    //DATOS PARA EL MIDDLEWARE
                    $arrayDatos = array(
                                            'mac_ont'               => $servProdCaractMacOnt->getValor(),
                                            'nombre_olt'            => $interfaceElemento->getElementoId()->getNombreElemento(),
                                            'ip_olt'                => $objIpElemento->getIp(),
                                            'puerto_olt'            => $interfaceElemento->getNombreInterfaceElemento(),
                                            'modelo_olt'            => 
                                            $interfaceElemento->getElementoId()->getModeloElementoId()->getNombreModeloElemento(),
                                            'line_profile'          => $strPerfil,
                                            'ont_id'                => $servProdCaractIndiceCliente->getValor(),
                                            'estado_servicio'       => $servicio->getEstado(),
                                            'ip'                    => '',     
                                            'scope'                 => '', 
                                            'ip_fijas_activas'      => $intIpsFijasActivas,
                                            'tipo_negocio_actual'   => $strTipoNegocio,
                                            'serial_ont_nuevo'      => $serieCpe,
                                            'mac_ont_nueva'         => $macCpe,
                                            'mac_wifi'              => $strMacWifi
                                        );
                    if ($prefijoEmpresa === 'MD')
                    {
                        $arrayRespuestaSeteaInfo = $this->servicioGeneral
                                                        ->seteaInformacionPlanesPyme(array("intIdPlan"         => $servicio->getPlanId()->getId(),
                                                                                           "intIdPunto"        => $servicio->getPuntoId()->getId(),
                                                                                           "strConservarIp"    => "",
                                                                                           "strTipoNegocio"    => $strTipoNegocio,
                                                                                           "strPrefijoEmpresa" => $prefijoEmpresa,
                                                                                           "strUsrCreacion"    => $usrCreacion,
                                                                                           "strIpCreacion"     => $ipCreacion,
                                                                                           "strTipoProceso"    => 'CAMBIAR_ELEMENTO',
                                                                                           "arrayInformacion"  => $arrayDatos));
                        if($arrayRespuestaSeteaInfo["strStatus"]  === "OK")
                        {
                            $arrayDatos = $arrayRespuestaSeteaInfo["arrayInformacion"];
                        }
                        else
                        {
                            throw new \Exception("Existieron problemas al recuperar información necesaria ".
                                                 "para ejecutar proceso, favor notifique a Sistemas.");
                        }
                    }
                    $arrayDatosMiddleware = array(
                                                    'empresa'               => $prefijoEmpresa,
                                                    'nombre_cliente'        => $strNombreCliente,
                                                    'login'                 => $servicio->getPuntoId()->getLogin(),
                                                    'identificacion'        => $strIdentificacion,
                                                    'datos'                 => $arrayDatos,
                                                    'opcion'                => $this->opcion,
                                                    'ejecutaComando'        => $this->ejecutaComando,
                                                    'usrCreacion'           => $usrCreacion,
                                                    'ipCreacion'            => $ipCreacion
                                                );

                    $arrayRespuesta = $this->rdaMiddleware->middleware(json_encode($arrayDatosMiddleware));

                    $strStatusCancelar = isset($arrayRespuesta['status_cancelar']) ? $arrayRespuesta['status_cancelar'] : $arrayRespuesta['status'];
                    $strStatusActivar  = isset($arrayRespuesta['status_activar']) ? $arrayRespuesta['status_activar'] : $arrayRespuesta['status'];

                    if($strStatusCancelar != 'OK' || $strStatusActivar != 'OK')
                    {
                        $strMensaje         = isset($arrayRespuesta['mensaje']) ? "Error: ".$arrayRespuesta['mensaje'] :
                                              "Cancelar: ".$arrayRespuesta['mensaje_cancelar']."\n Activar: ".$arrayRespuesta['mensaje_activar'];
                        throw new \Exception($strMensaje);
                    }

                    $strSerieElementoClienteOnt = "";
                    $intIdElementoCliente       = $servicioTecnico->getElementoClienteId();
                    if(isset($intIdElementoCliente) && !empty($intIdElementoCliente))
                    {
                        $objElementoClienteOnt  = $this->emComercial->getRepository('schemaBundle:InfoElemento')
                                                                    ->find($intIdElementoCliente);
                        if(is_object($objElementoClienteOnt))
                        {
                            $strSerieElementoClienteOnt = $objElementoClienteOnt->getSerieFisica();
                        }
                    }
                    $arrayDatosConfirmacionTn                           = $arrayDatos;
                    $arrayDatosConfirmacionTn['serial_ont']             = $strSerieElementoClienteOnt;
                    $arrayDatosConfirmacionTn['opcion_confirmacion']    = $this->opcion;
                    $arrayDatosConfirmacionTn['respuesta_confirmacion'] = 'ERROR'; 
                    $arrayDataConfirmacionTn    = array('nombre_cliente'    => $strNombreCliente,
                                                        'login'             => $servicio->getPuntoId()->getLogin(),
                                                        'identificacion'    => $strIdentificacion,
                                                        'datos'             => $arrayDatosConfirmacionTn,
                                                        'opcion'            => $this->strConfirmacionTNMiddleware,
                                                        'ejecutaComando'    => $this->ejecutaComando,
                                                        'usrCreacion'       => $usrCreacion,
                                                        'ipCreacion'        => $ipCreacion,
                                                        'empresa'           => $prefijoEmpresa,
                                                        'statusMiddleware'  => 'OK');
                }
                else
                {
                    //*OBTENER SCRIPT--------------------------------------------------------*/
                    $scriptArray = $this->servicioGeneral->obtenerArregloScript("cancelarCliente",$modeloElemento);
                    $idDocumento= $scriptArray[0]->idDocumento;
                    $usuario= $scriptArray[0]->usuario;
                    $protocolo= $scriptArray[0]->protocolo;
                    //*----------------------------------------------------------------------*/

                    //buscar indice del servicio
                    $servProdCaractIndiceCliente = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "INDICE CLIENTE", $producto);

                    //servicio prod caract - mac (ont o wifi)
                    $servicioProdCaractMac = $servProdCaractMacOnt;

                    $caracteristicaMac = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                       ->findOneBy(array( "descripcionCaracteristica" => "MAC ONT", "estado"=>"Activo"));
                    $productoCaracteristicaMac = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                    ->findOneBy(array( "productoId" => $producto->getId(), 
                                                                       "caracteristicaId"=>$caracteristicaMac->getId()));

                    $objResultadJson = $this->cancelarService->cancelarServicioOlt($interfaceElemento, $servProdCaractIndiceCliente, 
                                                                                   $servicioTecnico, $idDocumento, $login);
                    $strStatusCancel = $objResultadJson->status;
                    if($strStatusCancel=="OK")
                    {
                        if($objDetalleElemento)
                        {
                            $this->cancelarService->desconfigurarMacOntSm($servProdCaractMacOnt->getValor());
                        }                
                        //buscar perfil del servicio
                        $servProdCaractPerfil = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "PERFIL", $producto);
                        //*OBTENER SCRIPT--------------------------------------------------------*/
                        $scriptArray = $this->servicioGeneral->obtenerArregloScript("activarCliente",$modeloElemento);
                        $idDocumento= $scriptArray[0]->idDocumento;
                        $usuario= $scriptArray[0]->usuario;
                        $protocolo= $scriptArray[0]->protocolo;
                        //*----------------------------------------------------------------------*/
                        $objResultadJson = $this->activarService
                                                ->activarClienteOlt($interfaceElemento, $servProdCaractIndiceCliente->getValor(), $macCpe,
                                                                    $servProdCaractPerfil->getValor(), $login, $idDocumento, 
                                                                    $usuario, $protocolo, $servicioTecnico);
                        $strStatusActiv = $objResultadJson->status;
                        if($strStatusActiv=="ERROR")
                        {
                            $strMensaje = "NO SE PUEDE ACTIVAR LA MAC NUEVA, CAMBIO ONT";
                            $arrayRespFinal[] = array('status'=>"ERROR", 'mensaje'=>$strMensaje);
                            return $arrayRespFinal;
                        }
                    }
                    else{
                        $strMensaje = "NO SE PUEDE CANCELAR LA MAC ANTERIOR, CAMBIO ONT";
                        $arrayRespFinal[] = array('status'=>"ERROR", 'mensaje'=>$strMensaje);
                        return $arrayRespFinal;
                    }
                }
            }
            else if($tipoElementoCpe=="CPE WIFI")
            {
                $caracteristicaMac = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneBy(array( "descripcionCaracteristica" => "MAC WIFI", "estado"=>"Activo"));
                $productoCaracteristicaMac = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                             ->findOneBy(array( "productoId" => $producto->getId(), "caracteristicaId"=>$caracteristicaMac->getId()));

                if($flagMiddleware)
                {
                    $cantIpCliente = 0;

                    //OBTENER IP DEL ELEMENTO
                    $objIpElemento      = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                ->findOneBy(array("elementoId" => $interfaceElemento->getElementoId()));

                    //buscar indice del servicio
                    $servProdCaractIndiceCliente = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "INDICE CLIENTE", $producto);

                    //buscar perfil del servicio
                    $servProdCaractPerfil = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "PERFIL", $producto);
                    if($servProdCaractPerfil)
                    {
                        $strPerfil  = $servProdCaractPerfil->getValor();
                        $arrayPerfil= explode("_", $strPerfil);
                        if($strEsIsb === "SI")
                        {
                            $strPerfil  = $arrayPerfil[0]."_".$arrayPerfil[1]."_".$arrayPerfil[2];
                        }
                        else
                        {
                            $strPerfil  = $arrayPerfil[0]."_".$arrayPerfil[1];
                        }
                    }
                    $flagProd1=0;
                    if($strEsIsb === "SI")
                    {
                        $arrayProdIp        = array();
                        $objProdInternet    = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($intIdProdPref);
                        if($strNombreTecnicoProdPref === "INTERNET SMALL BUSINESS")
                        {
                            if(isset($arrayInfoMapeoProds) && !empty($arrayInfoMapeoProds))
                            {
                                foreach($arrayInfoMapeoProds as $arrayInfoProd)
                                {
                                    $intIdProductoIp    = $arrayInfoProd["intIdProdIp"];
                                    $objProdIPSB        = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                                            ->find($intIdProductoIp);
                                    $arrayProdIp[]      = $objProdIPSB;
                                }
                            }
                            else
                            {
                                $strMensaje = "No se ha podido obtener el correcto mapeo del servicio con la ip respectiva";
                                throw new \Exception($strMensaje);
                            }
                            $flagProd1  = 1;
                        }
                        else
                        {
                            $flagProd1  = 0;
                        }
                    }
                    else
                    {
                        $objProdInternet= $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                            ->findBy(array("nombreTecnico"  => "INTERNET",
                                                                            "empresaCod"    => $idEmpresa, 
                                                                            "estado"        => "Activo"));
                        $arrayProdIp    = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                            ->findBy(array( "nombreTecnico" => "IP",
                                                                            "empresaCod"    => $idEmpresa, 
                                                                            "estado"        => "Activo"));
                    }
                    $planCab = $servicio->getPlanId();
                    if(is_object($planCab))
                    {
                        $planDet = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')->findBy(array("planId"=>$planCab->getId()));
                        //verifica si plan del servicio tiene ip 
                        for($i = 0; $i < count($planDet); $i++)
                        {
                            for($intIndiceProdIp = 0; $intIndiceProdIp < count($arrayProdIp); $intIndiceProdIp++)
                            {
                                if($planDet[$i]->getProductoId() == $arrayProdIp[$intIndiceProdIp]->getId())
                                {
                                    $flagProd1 = 1;
                                    break;
                                }
                            }

                            for($intIndiceProdInternet = 0; $intIndiceProdInternet < count($objProdInternet); $intIndiceProdInternet++)
                            {
                                if($planDet[$i]->getProductoId() == $objProdInternet[$intIndiceProdInternet]->getId())
                                {
                                    $producto = $objProdInternet[$intIndiceProdInternet];
                                    break;
                                }
                            }

                            if($flagProd1 == 1)
                            {
                                break;
                            }
                        }//for($i = 0; $i < count($planDet); $i++)
                    }
                    else if(is_object($servicio->getProductoId()) 
                            && ($servicio->getProductoId()->getNombreTecnico() === "INTERNET SMALL BUSINESS"
                                || $servicio->getProductoId()->getNombreTecnico() === "TELCOHOME"))
                    {
                        $producto = $servicio->getProductoId();
                    }

                    if($flagProd1==0)
                    {
                        //no tiene ip en el plan
                        $serviciosPorPunto = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                               ->findBy(array("puntoId"=>$servicio->getPuntoId()->getId()));

                        //OBTENER IPS ADICIONALES
                        $arrayDatosIp   = $this->servicioGeneral
                                               ->getInfoIpsFijaPunto($serviciosPorPunto, $arrayProdIp, $servicio, 'Activo', 'Activo', $producto);

                        $cantIpCliente  = $arrayDatosIp['ip_fijas_activas'];

                    }
                    else
                    {
                        $cantIpCliente = 1;

                        //buscar la mac del wifi
                        $servProdCaractMacWifi  = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "MAC WIFI", $producto);
                        $strMac                 = $servProdCaractMacWifi->getValor();
                        $servicioTmp            = $servicio;
                        $servicioTecnicoTmp     = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                                ->findOneBy(array( "servicioId" => $servicioTmp->getId()));
                    }

                    if($cantIpCliente == 1)
                    {
                        if($flagProd1 == 1)
                        {
                            //OBTENER IP DEL PLAN
                            $ipFija     = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                               ->findOneBy(array("servicioId"=>$servicio->getId(),"estado"=>"Activo"));

                            $strIpFija  = $ipFija->getIp();

                            //OBTENER SCOPE
                            $objSpcScope    = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "SCOPE", $arrayProdIp);

                            if(!is_object($objSpcScope))
                            {
                                //buscar scopes
                                $arrayScopeOlt = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')
                                                                         ->getScopePorIpFija($ipFija->getIp(), $servicioTecnico->getElementoId());

                                if (!$arrayScopeOlt)
                                {   
                                    throw new \Exception("Ip Fija no pertenece a un Scope! <br>Favor Comunicarse con el Dep. Gepon!");
                                }

                                $strScope = $arrayScopeOlt['NOMBRE_SCOPE'];
                            }
                            else
                            {
                                $strScope = $objSpcScope->getValor();
                            }
                        }//if($flagProd1 == 1)
                        else
                        {
                            $arrayValores   = $arrayDatosIp['valores'];
                            $strIpFija      = $arrayValores[0]['ip'];
                            $strMac         = $arrayValores[0]['mac'];
                            $idServicioIp   = $arrayValores[0]['id_servicio'];

                            $objServicioIp  = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($idServicioIp);

                            //OBTENER SCOPE
                            $objSpcScope    = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioIp, 
                                                                                                        "SCOPE", 
                                                                                                        $objServicioIp->getProductoId()
                                                                                                       );
                            $strScope       = $objSpcScope->getValor();
                        }

                        if($servProdCaractMacWifi)
                        {
                            $servicioProdCaractMac = $servProdCaractMacWifi;
                        }

                        //DATOS PARA EL MIDDLEWARE
                        $arrayDatos = array(
                                                'mac_ont'               => $servProdCaractMacOnt->getValor(),
                                                'mac_wifi'              => $strMac,
                                                'nombre_olt'            => $interfaceElemento->getElementoId()->getNombreElemento(),
                                                'ip_olt'                => $objIpElemento->getIp(),
                                                'puerto_olt'            => $interfaceElemento->getNombreInterfaceElemento(),
                                                'modelo_olt'            => 
                                                $interfaceElemento->getElementoId()->getModeloElementoId()->getNombreModeloElemento(),
                                                'line_profile'          => $strPerfil,
                                                'ont_id'                => $servProdCaractIndiceCliente->getValor(),
                                                'estado_servicio'       => $servicio->getEstado(),
                                                'ip'                    => $strIpFija,     
                                                'scope'                 => $strScope, 
                                                'ip_fijas_activas'      => $cantIpCliente,
                                                'tipo_negocio_actual'   => $strTipoNegocio,
                                                'mac_wifi_nueva'        => $macCpe
                                            );

                        $arrayDatosMiddleware = array(
                                                        'empresa'               => $prefijoEmpresa,
                                                        'nombre_cliente'        => $strNombreCliente,
                                                        'login'                 => $servicio->getPuntoId()->getLogin(),
                                                        'identificacion'        => $strIdentificacion,
                                                        'datos'                 => $arrayDatos,
                                                        'opcion'                => 'CAMBIAR_WIFI',
                                                        'ejecutaComando'        => $this->ejecutaComando,
                                                        'usrCreacion'           => $usrCreacion,
                                                        'ipCreacion'            => $ipCreacion
                                                    );

                        $arrayRespuesta = $this->rdaMiddleware->middleware(json_encode($arrayDatosMiddleware));

                        $strStatus = $arrayRespuesta['status'];

                        if($strStatus != 'OK')
                        {
                            $strMensaje            = "Error: ".$arrayRespuesta['mensaje'];
                            throw new \Exception($strMensaje);
                        }

                        $arrayDatosConfirmacionTn                           = $arrayDatos;
                        $arrayDatosConfirmacionTn['opcion_confirmacion']    = 'CAMBIAR_WIFI';
                        $arrayDatosConfirmacionTn['respuesta_confirmacion'] = 'ERROR'; 
                        $arrayDataConfirmacionTn    = array('nombre_cliente'    => $strNombreCliente,
                                                            'login'             => $servicio->getPuntoId()->getLogin(),
                                                            'identificacion'    => $strIdentificacion,
                                                            'datos'             => $arrayDatosConfirmacionTn,
                                                            'opcion'            => $this->strConfirmacionTNMiddleware,
                                                            'ejecutaComando'    => $this->ejecutaComando,
                                                            'usrCreacion'       => $usrCreacion,
                                                            'ipCreacion'        => $ipCreacion,
                                                            'empresa'           => $prefijoEmpresa,
                                                            'statusMiddleware'  => 'OK');
                    }//if($cantIpCliente = 1)
                }
                else
                {
                    $planCab = $servicio->getPlanId();
                    $planDet = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')->findBy(array("planId"=>$planCab->getId()));
                    $prodIp = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                 ->findBy(array("nombreTecnico"=>"IP","empresaCod"=>$idEmpresa, "estado"=>"Activo"));
                    $prodInternet = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                     ->findBy(array("nombreTecnico"=>"INTERNET","empresaCod"=>$idEmpresa, "estado"=>"Activo"));
                    $flagProd1=0;
                    //verifica si plan del servicio tiene ip 
                    for($i = 0; $i < count($planDet); $i++)
                    {
                        for($j = 0; $j < count($prodIp); $j++)
                        {
                            if($planDet[$i]->getProductoId() == $prodIp[$j]->getId())
                            {
                                $prodIpCliente = $prodIp[$j] ;
                                $flagProd1 = 1;
                                break;
                            }
                        }

                        for($j = 0; $j < count($prodInternet); $j++)
                        {
                            if($planDet[$i]->getProductoId() == $prodInternet[$j]->getId())
                            {
                                $producto = $prodInternet[$j];
                                break;
                            }
                        }

                        if($flagProd1 == 1)
                        {
                            break;
                        }
                    }

                    if($flagProd1==0){
                        //no tiene ip
                        $serviciosPorPunto = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                               ->findBy(array("puntoId"=>$servicio->getPuntoId()->getId()));

                        //verificar si los servicios del punto, tienen ip adicional
                        for($z=0;$z<count($serviciosPorPunto);$z++)
                        {
                           //solo de servicios Activos o In-corte
                           if($serviciosPorPunto[$z]->getEstado()=="Activo" || $serviciosPorPunto[$z]->getEstado()=="In-Corte")
                           {
                               $planCab = $serviciosPorPunto[$z]->getPlanId();
                               $productoServicio = $serviciosPorPunto[$z]->getProductoId();

                               //verificar si tiene ip dentro del plan
                               if($planCab)
                               {
                                   $planDet = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                                   ->findBy(array("planId"=>$planCab->getId()));

                                   $flagProd2=0;
                                   for($i=0;$i<count($planDet);$i++)
                                   {
                                       for($j = 0; $j < count($prodIp); $j++)
                                       {
                                           if($planDet[$i]->getProductoId() == $prodIp[$j]->getId())
                                           {
                                               $prodIpCliente = $prodIp[$j];
                                               $flagProd2     = 1;
                                               break;
                                           }
                                       }

                                       for($j=0;$j<count($prodInternet);$j++)
                                       {
                                           if($planDet[$i]->getProductoId() == $prodInternet[$j]->getId())
                                           {
                                               $producto = $prodInternet[$j];
                                               break;
                                           }
                                       }

                                       if($flagProd2==1)
                                       {
                                           break;
                                       }
                                   }    
                               }//if($planCab)
                               //verificar si tiene ip en el producto
                               else
                               {
                                   if($productoServicio)
                                   {
                                       foreach($arrayProdIp as $objProdIp)
                                       {
                                           if($productoServicio->getId() == $objProdIp->getId())
                                           {
                                               $prodIpCliente = $objProdIp;
                                               $flagProd2     = 1;
                                               break;
                                           }
                                       }
                                   }
                               }

                               if($flagProd2==1)
                               {
                                   //buscar la mac del wifi
                                   $servProdCaractMacWifi = $this->servicioGeneral
                                                                 ->getServicioProductoCaracteristica($servicio, "MAC WIFI", $producto);
                                   $servicioTmp = $serviciosPorPunto[$z];
                                   $servicioTecnicoTmp = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                                           ->findOneBy(array( "servicioId" => $servicioTmp->getId()));
                                   break;
                               }
                           }//if($serviciosPorPunto[$z]->getEstado()=="Activo" || $serviciosPorPunto[$z]->getEstado()=="In-Corte")
                        }//for($z=0;$z<count($serviciosPorPunto);$z++)
                    }
                    else
                    {
                        //buscar la mac del wifi
                        $servProdCaractMacWifi = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "MAC WIFI", $producto);
                        $servicioTmp = $servicio;
                        $servicioTecnicoTmp = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                                ->findOneBy(array( "servicioId" => $servicioTmp->getId()));
                    }


                    //validar que no sea plan 100 100

                    $caractEdicionLimitada = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                        ->findOneBy(array("descripcionCaracteristica" => "EDICION LIMITADA",
                                                                             "estado" => "Activo"));

                    $planCaractEdicionLimitada = $this->emComercial->getRepository('schemaBundle:InfoPlanCaracteristica')
                                                                ->findOneBy(array(
                                                                 "planId"            => $servicio->getPlanId()->getId(),
                                                                 "caracteristicaId"  => $caractEdicionLimitada->getId(),
                                                                 "estado"            => $servicio->getPlanId()->getEstado()));
                    if($planCaractEdicionLimitada)
                    {
                        if($planCaractEdicionLimitada->getValor() == "SI" && $objDetalleElemento)
                        {
                            $flagProd1=0;
                            $flagProd2=0;
                        }
                    }            

                    if($flagProd1==1 || $flagProd2==1){
                        //obtener ips fijas q tiene el servicio
                        $ipsFijas = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                            ->findOneBy(array("servicioId"=>$servicioTecnicoTmp->getServicioId()->getId(),
                                                                              "tipoIp"=>"FIJA", "estado"=>"Activo"));//$prodIpCliente
                        //tiene ip el servicio o el punto (servicio adicional)
                        if($servProdCaractMacWifi){
                            $servicioProdCaractMac = $servProdCaractMacWifi;
                        }
                        else{
                            $arrayRespuestaFinal[] = array('status'=>'ERROR', 'mensaje'=>'NO EXISTE MAC ANTERIOR');
                            return $arrayRespuestaFinal;
                        }


                        if (!$objDetalleElemento)
                        {
                            //obtener indice cliente
                            $servProdCaracPool = $this->servicioGeneral->getServicioProductoCaracteristica($servicioTecnicoTmp->getServicioId(), 
                                                                                                           "POOL IP",
                                                                                                           $producto);

                            if($servProdCaracPool){
                                $pool = $servProdCaracPool->getValor();
                            }
                            else{
                                //obtener perfil
                                $servProdCaracPerfil = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "PERFIL", $producto);

                                //configurar Ip fija
                                //*OBTENER SCRIPT--------------------------------------------------------*/
                                $scriptArray = $this->servicioGeneral->obtenerArregloScript("obtenerVlanParaIpFija",$modeloElemento);
                                $idDocumentoVlan= $scriptArray[0]->idDocumento;
                                $usuario= $scriptArray[0]->usuario;
                                $protocolo= $scriptArray[0]->protocolo;
                                //*----------------------------------------------------------------------*/
                                $objResultadJsonVlan = $this->cancelarService->getVlanParaIpFija($servicioTecnicoTmp, $usuario, $interfaceElemento, 
                                                                                                 $servProdCaracPerfil->getValor(), $idDocumentoVlan);
                                $strStatusVlan = $objResultadJsonVlan->status;
                                if($strStatusVlan=="OK")
                                {
                                    $strValorVlan = $objResultadJsonVlan->mensaje;

                                    //*OBTENER SCRIPT--------------------------------------------------------*/
                                    $scriptArray = $this->servicioGeneral->obtenerArregloScript("obtenerPoolParaIpFija",$modeloElemento);
                                    $idDocumentoPool= $scriptArray[0]->idDocumento;
                                    $usuario= $scriptArray[0]->usuario;
                                    $protocolo= $scriptArray[0]->protocolo;
                                    //*----------------------------------------------------------------------*/
                                    $objResultadJsonPool= $this->cancelarService->getPoolParaIpFija($servicioTecnicoTmp, $usuario, $interfaceElemento,
                                                                                                     $strValorVlan, $idDocumentoPool);
                                    $strStatusPool = $objResultadJsonPool->status;
                                    if($strStatusPool=="OK")
                                    {
                                        $strMensajePool = $objResultadJsonPool->mensaje;
                                    }
                                    else{
                                        $arrayRespuestaFinal[] = array('status'=>'ERROR', 'mensaje'=>'NO EXISTE POOL');
                                        return $arrayRespuestaFinal;
                                    }
                                }
                                else{
                                    $arrayRespuestaFinal[] = array('status'=>'ERROR', 'mensaje'=>'NO EXISTE VLAN');
                                    return $arrayRespuestaFinal;
                                }                    
                            }

                            //cambiar formato de la mac
                            $macWifiNueva = $this->cancelarService->cambiarMac($servProdCaractMacWifi->getValor());

                            //desconfigurar Ip fija con mac anterior
                            //*OBTENER SCRIPT--------------------------------------------------------*/
                            $scriptArray = $this->servicioGeneral->obtenerArregloScript("desconfigurarIpFija",$modeloElemento);
                            $idDocumentoDesconf = $scriptArray[0]->idDocumento;
                            $usuario= $scriptArray[0]->usuario;
                            $protocolo= $scriptArray[0]->protocolo;
                            //*----------------------------------------------------------------------*/

                            if($ipsFijas){
                                $interfaceElementoTmp = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                    ->find($servicioTecnicoTmp->getInterfaceElementoId());
                                $objResultadJsonDesconf = $this->cancelarService->desconfigurarIpFija($interfaceElementoTmp, $usuario, 
                                                                                                      $strMensajePool, $ipsFijas->getIp(), 
                                                                                                      $macWifiNueva, $idDocumentoDesconf);
                                $strStatusDesconf = $objResultadJsonDesconf->status;
                                if($strStatusDesconf=="ERROR")
                                {
                                    $arrayRespuestaFinal[] = array('status'=>'ERROR', 'mensaje'=>'NO SE DESCONFIGURO IP FIJA');
                                    return $arrayRespuestaFinal;
                                }
                            }

                            //configurar la ip con la nueva mac--------------------------------------------------------------------------------------
                            //cambiar formato de mac
                            $macWifiCpeNueva = $this->activarService->cambiarMac($macCpe);

                            //*OBTENER SCRIPT--------------------------------------------------------*/
                            $scriptArray1 = $this->servicioGeneral->obtenerArregloScript("configurarIpFija",$modeloElemento);
                            $idDocumentoConfig= $scriptArray1[0]->idDocumento;
                            $usuarioConfig= $scriptArray1[0]->usuario;
                            //*----------------------------------------------------------------------*/

                            $objResultadJsonIp = $this->activarService->configurarIpFija($servicioTecnicoTmp, $usuarioConfig, $strMensajePool, 
                                                                                         $ipsFijas->getIp(), $macWifiCpeNueva, $idDocumentoConfig);
                            $strStatus = $objResultadJsonIp->status;
                            if($strStatus=="ERROR")
                            {
                                $arrayRespuestaFinal[] = array('status'=>'ERROR', 'mensaje'=>'NO SE CONFIGURO IP FIJA');
                                return $arrayRespuestaFinal;
                            }                
                        }
                        else
                        {     
                            $objScopeInterno    = $this->servicioGeneral->getServicioProductoCaracteristica($servicioTmp, "SCOPE", $prodIpCliente);
                            //cancelamos la ip                    
                            $parametrosCancelaIp = array('servicio' => $servicioTmp,
                                                         'scope'    => $objScopeInterno->getValor(),
                                                         'producto' => $prodIpCliente,
                                                         'macOnt'   => $servProdCaractMacOnt->getValor());

                            $resultCancelaIp = $this->cancelarService->cancelarIpTellionCnr($parametrosCancelaIp);

                            $statusIpCancel = $resultCancelaIp[0]['status'];
                            if($statusIpCancel!="OK")
                            {
                                $strMensaje     .= "Error al cancelar Ip".$resultCancelaIp[0]['mensaje'];
                                $arrayFinal[]   = array('status'=>"ERROR", 'mensaje'=>$strMensaje);
                                return $arrayFinal;
                            }
                            //configuramos la nueva ip
                            //obtener objeto modelo cnr
                            $modeloElementoCnr = $this->emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                                      ->findOneBy(array("nombreModeloElemento"  => "CNR UCS C220",
                                                                        "estado"                => "Activo"));
                            //obtener elemento cnr
                            $elementoCnr = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                      ->findOneBy(array("modeloElementoId"=>$modeloElementoCnr->getId()));      

                            $parametrosActivaIp = array('ipFija'              => $ipsFijas->getIp(),
                                                        'modeloElementoCnr'   => $modeloElementoCnr,
                                                        'elementoCnr'         => $elementoCnr,
                                                        'mac'                 => $macCpe);


                            $resultadJsonIpFija = $this->activarService->activarIpFijaCnr($parametrosActivaIp);

                            if($resultadJsonIpFija[0]['status']!="OK")
                            {
                                $arrayRespuestaFinal[] = array('status'=>'ERROR', 'mensaje'=>$resultadJsonIpFija[0]['mensaje']);
                                return $arrayRespuestaFinal;
                            }                    

                        }
                    }            
                    if($flagProd1==0 && $flagProd2==0)
                    {
                        //no tiene ip
                        $servProdCaractMacWifi = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "MAC WIFI", $producto);
                        if($servProdCaractMacWifi){
                            $servicioProdCaractMac = $servProdCaractMacWifi;
                            //se libera la ip dianmica
                            if ($objDetalleElemento)
                            {
                                $resultCancelaIp = $this->cancelarService->desconfigurarIpDinamicaCnr($servProdCaractMacOnt->getValor());
                            }
                        }
                        else{
                            $arrayRespuestaFinal[] = array('status'=>'ERROR', 'mensaje'=>'NO EXISTE MAC ANTERIOR');
                            return $arrayRespuestaFinal;
                        }
                    } 
                }
            }//else if($tipoElementoCpe=="CPE WIFI")
            else
            {
                throw new \Exception("Tipo de ONT no existe para realizar cambio de elemento!");
            }
            
            if($prefijoEmpresa=="MD"){
                $empresaCod= "10";
            }
            else{
                $empresaCod= $idEmpresa;
            }

            //buscar elemento cpe
            $cpeNafArray = $this->servicioGeneral->buscarElementoEnNaf($serieCpe, $codigoArticulo, "PI", "ActivarServicio");
            $cpeNaf = $cpeNafArray[0]['status'];
            $codigoArticuloCpe = $cpeNafArray[0]['mensaje'];
            if($cpeNaf=="OK"){
                //actualizamos registro en el naf del cpe
                $pv_mensajeerror = str_repeat(' ', 1000);                                                                    
                $sql = "BEGIN AFK_PROCESOS.IN_P_PROCESA_INSTALACION(:codigoEmpresaNaf, "
                . ":codigoArticulo, :tipoArticulo, :identificacionCliente, :serieCpe, :cantidad, :pv_mensajeerror); END;";
                $stmt = $this->emNaf->getConnection()->prepare($sql);
                $stmt->bindParam('codigoEmpresaNaf', $empresaCod);
                $stmt->bindParam('codigoArticulo', $codigoArticulo);
                $stmt->bindParam('tipoArticulo',$tipoArticulo);
                $stmt->bindParam('identificacionCliente', $identificacionCliente);
                $stmt->bindParam('serieCpe', $serieCpe);
                $stmt->bindParam('cantidad', intval(1));
                $stmt->bindParam('pv_mensajeerror', $pv_mensajeerror);
                $stmt->execute();

                if(strlen(trim($pv_mensajeerror))>0)
                {
                    $arrayRespuestaFinal[] = array("status"=>"NAF", "mensaje"=>"ERROR WIFI NAF: ".$pv_mensajeerror, 
                                                   "arrayDataConfirmacionTn" => $arrayDataConfirmacionTn);
                    return $arrayRespuestaFinal;
                }
            }
            else{
                $arrayRespuestaFinal[] = array('status'=>'NAF', 'mensaje'=>$codigoArticuloCpe, "arrayDataConfirmacionTn" => $arrayDataConfirmacionTn);
                return $arrayRespuestaFinal;
            }

            //finalizar la solicitud de cambio de Cpe
            $tipoSolicitudCambio = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                        ->findOneBy(array( "descripcionSolicitud" => "SOLICITUD CAMBIO EQUIPO", "estado"=>"Activo"));
            $solicitudCambioCpe = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                    ->findOneBy(array( "servicioId" => $servicio->getId() , "tipoSolicitudId"=>$tipoSolicitudCambio, "estado"=>"AsignadoTarea"));
            if($solicitudCambioCpe){
                $strEstadoDetalleSol = "AsignadoTarea";
                $solicitudCambioCpe->SetEstado("Finalizada");
                $this->emComercial->persist($solicitudCambioCpe);
                $this->emComercial->flush();
            }
            else{
                $strEstadoDetalleSol = "Finalizada";
                $tipoSolicitudCambio = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                    ->findOneBy(array( "descripcionSolicitud" => "SOLICITUD CAMBIO DE MODEM INMEDIATO", "estado"=>"Activo"));
                $solicitudCambioCpe = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                            ->findOneBy(array( "servicioId" => $servicio->getId() , "tipoSolicitudId"=>$tipoSolicitudCambio, 
                                                               "estado"=>"AsignadoTarea"));

                //eliminar las caracteristicas de la solicitud (elementos escogidos)
                $caracteristicaSolicitud = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                     ->findOneBy(array( "detalleSolicitudId" => $solicitudCambioCpe->getId(), 
                                                                        "valor"              => $idElementoCliente , 
                                                                        "estado"             => "AsignadoTarea"));
                if($caracteristicaSolicitud){
                    $caracteristicaSolicitud->setEstado("Finalizada");
                    $caracteristicaSolicitud->setUsrCreacion($usrCreacion);
                    $caracteristicaSolicitud->setFeCreacion(new \DateTime('now'));
                    $this->emComercial->persist($caracteristicaSolicitud);
                    $this->emComercial->flush();
                }	

                $caractSolicitud = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                     ->findBy(array( "detalleSolicitudId" => $solicitudCambioCpe->getId(), "estado"=>"Activo"));
                if(count($caractSolicitud)>0){
                    //no se le hace nada a la solicitud, solo cuando termine de hacer los cambios se finaliza
                }
                else{
                    $solicitudCambioCpe->SetEstado("Finalizada");
                    $this->emComercial->persist($solicitudCambioCpe);
                    $this->emComercial->flush();
                }
            }

            if($solicitudCambioCpe->getEstado()=="Finalizada"){
                //crear solicitud para retiro de equipo (cpe)
                $tipoSolicitud = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                            ->findOneBy(array( "descripcionSolicitud" => "SOLICITUD RETIRO EQUIPO", "estado"=>"Activo"));

                $detalleSolicitud = new InfoDetalleSolicitud();
                $detalleSolicitud->setServicioId($servicio);
                $detalleSolicitud->setTipoSolicitudId($tipoSolicitud);
                $detalleSolicitud->setEstado("AsignadoTarea");
                $detalleSolicitud->setUsrCreacion($usrCreacion);
                $detalleSolicitud->setFeCreacion(new \DateTime('now'));
                $detalleSolicitud->setObservacion("SOLICITA RETIRO DE EQUIPO POR CAMBIO DE MODEM");
                $this->emComercial->persist($detalleSolicitud);
                $this->emComercial->flush();

                //crear las caract para la solicitud de retiro de equipo
                $entityAdmiCaracteristica= $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')->find(360);
                $caractSolicitudCambioElemento = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                     ->findBy(array( "detalleSolicitudId" => $solicitudCambioCpe->getId(), 
                                                                     "estado"             => $strEstadoDetalleSol));

                for($i=0;$i<count($caractSolicitudCambioElemento);$i++){
                    $entityCaract= new InfoDetalleSolCaract();
                    $entityCaract->setCaracteristicaId($entityAdmiCaracteristica);
                    $entityCaract->setDetalleSolicitudId($detalleSolicitud);
                    $entityCaract->setValor($caractSolicitudCambioElemento[$i]->getValor());
                    $entityCaract->setEstado("AsignadoTarea");
                    $entityCaract->setUsrCreacion($usrCreacion);
                    $entityCaract->setFeCreacion(new \DateTime('now'));
                    $this->emComercial->persist($entityCaract);
                    $this->emComercial->flush();
                }

                //buscar el info_detalle de la solicitud
                $detalleCambioCpe = $this->emComercial->getRepository('schemaBundle:InfoDetalle')
                                                    ->findOneBy(array( "detalleSolicitudId" => $solicitudCambioCpe->getId()));

                //obtener tarea
                $entityProceso = $this->emSoporte->getRepository('schemaBundle:AdmiProceso')->findOneByNombreProceso("SOLICITAR RETIRO EQUIPO");
                $entityTareas = $this->emSoporte->getRepository('schemaBundle:AdmiTarea')->findTareasActivasByProceso($entityProceso->getId());
                $entityTarea = $entityTareas[0];

                //grabar nuevo info_detalle para la solicitud de retiro de equipo
                $entityDetalle = new InfoDetalle();
                $entityDetalle->setDetalleSolicitudId($detalleSolicitud->getId());
                $entityDetalle->setTareaId($entityTarea);
                $entityDetalle->setLongitud($servicio->getPuntoId()->getLongitud());
                $entityDetalle->setLatitud($servicio->getPuntoId()->getLatitud());
                $entityDetalle->setPesoPresupuestado(0);
                $entityDetalle->setValorPresupuestado(0);
                $entityDetalle->setIpCreacion($ipCreacion);
                $entityDetalle->setFeCreacion(new \DateTime('now'));
                $entityDetalle->setUsrCreacion($usrCreacion);
                $this->emSoporte->persist($entityDetalle);
                $this->emSoporte->flush();

                //buscar la info_detalle_asignacion de la solicitud
                if($detalleCambioCpe){
                    $detalleAsignacionCambioCpe = $this->emComercial->getRepository('schemaBundle:InfoDetalleAsignacion')
                                                                    ->findOneBy(array( "detalleId" => $detalleCambioCpe->getId()));

                    //asignar los mismos responsables a la solicitud de retiro de equipo
                    $entityDetalleAsignacion = new InfoDetalleAsignacion();
                    $entityDetalleAsignacion->setDetalleId($entityDetalle);
                    $entityDetalleAsignacion->setAsignadoId($detalleAsignacionCambioCpe->getAsignadoId());
                    $entityDetalleAsignacion->setAsignadoNombre($detalleAsignacionCambioCpe->getAsignadoNombre());
                    $entityDetalleAsignacion->setRefAsignadoId($detalleAsignacionCambioCpe->getRefAsignadoId());
                    $entityDetalleAsignacion->setRefAsignadoNombre($detalleAsignacionCambioCpe->getRefAsignadoNombre());
                    $entityDetalleAsignacion->setPersonaEmpresaRolId($detalleAsignacionCambioCpe->getPersonaEmpresaRolId());
                    $entityDetalleAsignacion->setTipoAsignado("EMPLEADO");
                    $entityDetalleAsignacion->setIpCreacion($ipCreacion);
                    $entityDetalleAsignacion->setFeCreacion(new \DateTime('now'));
                    $entityDetalleAsignacion->setUsrCreacion($usrCreacion);
                    $this->emSoporte->persist($entityDetalleAsignacion);
                    $this->emSoporte->flush();

                    $arrayParametrosAuditoria["intIdPersona"] = $detalleAsignacionCambioCpe->getRefAsignadoId();
                }

                //crear historial para la solicitud
                $historialSolicitud = new InfoDetalleSolHist();
                $historialSolicitud->setDetalleSolicitudId($detalleSolicitud);
                $historialSolicitud->setEstado("AsignadoTarea");
                $historialSolicitud->setObservacion("GENERACION AUTOMATICA DE SOLICITUD RETIRO DE EQUIPO POR CAMBIO DE MODEM");
                $historialSolicitud->setUsrCreacion($usrCreacion);
                $historialSolicitud->setFeCreacion(new \DateTime('now'));
                $historialSolicitud->setIpCreacion($ipCreacion);
                $this->emComercial->persist($historialSolicitud);
                $this->emComercial->flush();
            }
            $strNombreInterfaceElementoWanIsb = "";
            if($boolValidaModeloCpeWifiIsb)
            {
                $arrayModelosCpeWifiIsb             = $this->emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                                                              ->getModelosElementosCpe($codigoArticulo,"","CPE WIFI", "Activo",'','');
                if(empty($arrayModelosCpeWifiIsb))
                {
                    $arrayModelosCpeWifiIsb         = $this->emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                                                              ->getModelosElementosCpe($codigoArticulo,"","CPE", "Activo",'','');
                }
                if(isset($arrayModelosCpeWifiIsb) && !empty($arrayModelosCpeWifiIsb) && is_object($arrayModelosCpeWifiIsb[0]))
                {
                    $modeloElementoCpe = $arrayModelosCpeWifiIsb[0];
                }
                else
                {
                    throw new \Exception("El modelo elemento ".$codigoArticulo." no pertenece a uno"
                                        ." de los modelos permitidos para realizar el cambio de equipo: CPE WIFI o CPE");
                }

                $objAdmiTipoInterfaceWan    = $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoInterface')
                                                                      ->findOneBy(array("nombreTipoInterface"   => "Wan",
                                                                                        "estado"                => "Activo"));
                if(is_object($objAdmiTipoInterfaceWan))
                {
                    $objAdmiInterfaceElementoWan    = $this->emInfraestructura->getRepository('schemaBundle:AdmiInterfaceModelo')
                                                                              ->findOneBy(array("modeloElementoId"  => $modeloElementoCpe,
                                                                                                "tipoInterfaceId"   => $objAdmiTipoInterfaceWan,
                                                                                                "estado"            => "Activo"));
                    if(is_object($objAdmiInterfaceElementoWan))
                    {
                        $arrayFormatAdmiInterfaceElementoWan    = explode("?", $objAdmiInterfaceElementoWan->getFormatoInterface());
                        $strNombreInterfaceElementoWanIsb       = $arrayFormatAdmiInterfaceElementoWan[0] . "1";
                    }
                }
            }
            else
            {
                //buscamos modelo
                $modeloElementoCpe = $this->emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                                        ->findOneBy(array( "nombreModeloElemento" => $codigoArticulo, "estado"=>"Activo"));
            }
            if($tipoElementoCpe=="CPE ONT"){
                $cpeId = $servicioTecnico->getElementoClienteId();
                $objCpeACambiar = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($cpeId);
            }
            else if($tipoElementoCpe=="CPE WIFI"){
                $cpeInterfaceId = $servicioTecnico->getInterfaceElementoClienteId();
                $objEnlaceCpeWifi   = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                          ->findOneBy(array("interfaceElementoIniId"=>$cpeInterfaceId));
                $objInterfaceCpeWifi= $objEnlaceCpeWifi->getInterfaceElementoFinId();
                $objCpeACambiar     = $objInterfaceCpeWifi->getElementoId();
            }
            if($strEsIsb === "SI")
            {
                $idEmpresa = 18;
            }   
            /////////////////////////////////////////////// SE RECREA EL NUEVO ELEMENTO CPE WIFI ///////////////////////////////////////////
            //SE OBTIENE EL ELEMENTO ANTERIOR
            if($tipoElementoCpe=="CPE WIFI")
            {
                $objServicioProdCaractMac = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "MAC WIFI", $producto);

                if(is_object($objInterfaceCpeWifi))
                {
                    $intIdElementoAnterior = $objInterfaceCpeWifi->getElementoId();
                }

                //SE CREA EL NUEVO ELEMENTO
                $objInfoElementoCpeWifi = new InfoElemento();
                $objInfoElementoCpeWifi->setNombreElemento($nombreCpe);
                $objInfoElementoCpeWifi->setDescripcionElemento($descripcionCpe);
                $objInfoElementoCpeWifi->setModeloElementoId($modeloElementoCpe);
                $objInfoElementoCpeWifi->setSerieFisica($serieCpe);
                $objInfoElementoCpeWifi->setEstado("Activo");
                $objInfoElementoCpeWifi->setUsrResponsable($usrCreacion);
                $objInfoElementoCpeWifi->setUsrCreacion($usrCreacion);
                $objInfoElementoCpeWifi->setFeCreacion(new \DateTime('now'));
                $objInfoElementoCpeWifi->setIpCreacion($ipCreacion);
                $this->emInfraestructura->persist($objInfoElementoCpeWifi);
                $this->emInfraestructura->flush();

                //SE REGISTRA EL TRACKING DEL ELEMENTO - NUEVO
                $arrayParametrosAuditoria["strNumeroSerie"]  = $serieCpe;
                $arrayParametrosAuditoria["strEstadoTelcos"] = 'Activo';
                $arrayParametrosAuditoria["strEstadoNaf"]    = 'Instalado';
                $arrayParametrosAuditoria["strEstadoActivo"] = 'Activo';
                $arrayParametrosAuditoria["strUbicacion"]    = 'Cliente';
                $arrayParametrosAuditoria["strCodEmpresa"]   = "18";
                $arrayParametrosAuditoria["strTransaccion"]  = 'Activacion Cliente';
                $arrayParametrosAuditoria["intOficinaId"]    = 0;

                //Se consulta el login del cliente
                if(is_object($servicioTecnico->getServicioId()))
                {
                    $objInfoPunto = $this->emInfraestructura->getRepository('schemaBundle:InfoPunto')
                                                            ->find($servicioTecnico->getServicioId()->getPuntoId()->getId());
                    if(is_object($objInfoPunto))
                    {
                        $arrayParametrosAuditoria["strLogin"] = $objInfoPunto->getLogin();
                    }
                }

                $arrayParametrosAuditoria["strUsrCreacion"] = $usrCreacion;

                $this->serviceInfoElemento->ingresaAuditoriaElementos($arrayParametrosAuditoria);

                //SE ASOCIA EL NUEVO ELEMENTO A LA INFO_EMPRESA_ELEMENTO_UBICA
                $objInfoEmpresaElementoUbic = $this->emInfraestructura->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                                                                      ->findOneByElementoId($intIdElementoAnterior);

                if(is_object($objInfoEmpresaElementoUbic))
                {
                    $objInfoEmpresaElementoUbica = new InfoEmpresaElementoUbica();
                    $objInfoEmpresaElementoUbica->setEmpresaCod($objInfoEmpresaElementoUbic->getEmpresaCod());
                    $objInfoEmpresaElementoUbica->setElementoId($objInfoElementoCpeWifi);
                    $objInfoEmpresaElementoUbica->setUbicacionId($objInfoEmpresaElementoUbic->getUbicacionId());
                    $objInfoEmpresaElementoUbica->setUsrCreacion($usrCreacion);
                    $objInfoEmpresaElementoUbica->setFeCreacion(new \DateTime('now'));
                    $objInfoEmpresaElementoUbica->setIpCreacion($ipCreacion);
                    $this->emInfraestructura->persist($objInfoEmpresaElementoUbica);
                    $this->emInfraestructura->flush();
                }

                //SE ASOCIA EL NUEVO ELEMENTO A LA INFO_EMPRESA_ELEMENTO
                $objInfoEmpreElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoEmpresaElemento')
                                                                ->findOneByElementoId($intIdElementoAnterior);

                if(is_object($objInfoEmpreElemento))
                {
                    //SE ELIMINA EL REGISTRO ACTUAL
                    $objInfoEmpreElemento->setEstado("Eliminado");
                    $this->emInfraestructura->persist($objInfoEmpreElemento);
                    $this->emInfraestructura->flush();

                    $objInfoEmpresaElemento = new InfoEmpresaElemento();
                    $objInfoEmpresaElemento->setEmpresaCod($objInfoEmpreElemento->getEmpresaCod());
                    $objInfoEmpresaElemento->setElementoId($objInfoElementoCpeWifi);
                    $objInfoEmpresaElemento->setEstado("Activo");
                    $objInfoEmpresaElemento->setUsrCreacion($usrCreacion);
                    $objInfoEmpresaElemento->setFeCreacion(new \DateTime('now'));
                    $objInfoEmpresaElemento->setIpCreacion($ipCreacion);
                    $this->emInfraestructura->persist($objInfoEmpresaElemento);
                    $this->emInfraestructura->flush();
                }

                //SE GENERAN LAS INTERFACES DEL NUEVO ELEMENTO
                $arrayInterfaceModelo = $this->emInfraestructura->getRepository('schemaBundle:AdmiInterfaceModelo')
                                                                ->findBy(array("modeloElementoId" => $modeloElementoCpe->getId()));

                foreach($arrayInterfaceModelo as $objArrayInterfaceModelo)
                {
                    $intCantidadInterfaces = $objArrayInterfaceModelo->getCantidadInterface();
                    $strFormato            = $objArrayInterfaceModelo->getFormatoInterface();

                    for($i = 1; $i <= $intCantidadInterfaces; $i++)
                    {
                        $objInterfacesElementoWifi = new InfoInterfaceElemento();
                        $arrayFormat = explode("?", $strFormato);

                        $strNombreInterfaceElemento = $arrayFormat[0] . $i;

                        $objInterfacesElementoWifi->setNombreInterfaceElemento($strNombreInterfaceElemento);
                        $objInterfacesElementoWifi->setElementoId($objInfoElementoCpeWifi);
                        $objInterfacesElementoWifi->setEstado("not connect");
                        $objInterfacesElementoWifi->setUsrCreacion($usrCreacion);
                        $objInterfacesElementoWifi->setFeCreacion(new \DateTime('now'));
                        $objInterfacesElementoWifi->setIpCreacion($ipCreacion);
                        $this->emInfraestructura->persist($objInterfacesElementoWifi);
                        $this->emInfraestructura->flush();
                    }
                }

                //MAC VIEJA
                if($objServicioProdCaractMac)
                {
                    $objServicioProdCaractMac->setEstado("Eliminado");
                    $this->emComercial->persist($objServicioProdCaractMac);
                    $this->emComercial->flush();
                }

                //MAC NUEVA
                $objServicioProdCaracteMac = new InfoServicioProdCaract();
                $objServicioProdCaracteMac->setServicioId($servicio->getId());
                $objServicioProdCaracteMac->setProductoCaracterisiticaId($productoCaracteristicaMac->getId());
                $objServicioProdCaracteMac->setValor($macCpe);
                $objServicioProdCaracteMac->setEstado("Activo");
                $objServicioProdCaracteMac->setUsrCreacion($usrCreacion);
                $objServicioProdCaracteMac->setFeCreacion(new \DateTime('now'));
                $this->emComercial->persist($objServicioProdCaracteMac);
                $this->emComercial->flush();

                if($flagMiddleware)
                {
                    $this->actualizarMacCambioElementoPro(array("strIpCreacion"         => $ipCreacion,
                                                                "strUsrCreacion"        => $usrCreacion,
                                                                "strTipoElementoCpe"    => $tipoElementoCpe,
                                                                "strCodEmpresa"         => $idEmpresa,
                                                                "objProducto"           => $producto,
                                                                "objServicio"           => $servicio,
                                                                "strMacCpe"             => $macCpe,
                                                                "strTipoNegocio"        => $strTipoNegocio
                                                                ));
                }
                //SE CAMBIA A ESTADO ELIMINADO EL ELEMENTO ANTERIOR
                $objInfoElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                        ->find($intIdElementoAnterior);

                if(is_object($objInfoElemento))
                {
                    $objInfoElemento->setEstado("Eliminado");
                    $this->emInfraestructura->persist($objInfoElemento);
                    $this->emInfraestructura->flush();
                }

                //SE REGISTRA EL TRACKING DEL ELEMENTO - ANTERIOR
                $arrayParametrosAuditoria["strNumeroSerie"]  = $objInfoElemento->getSerieFisica();
                $arrayParametrosAuditoria["strEstadoTelcos"] = 'Eliminado';
                $arrayParametrosAuditoria["strEstadoNaf"]    = 'Instalado';
                $arrayParametrosAuditoria["strEstadoActivo"] = 'CambioEquipo';
                $arrayParametrosAuditoria["strUbicacion"]    = 'EnTransito';
                $arrayParametrosAuditoria["strCodEmpresa"]   = $idEmpresa;
                $arrayParametrosAuditoria["strTransaccion"]  = 'Cambio de Elemento';
                $arrayParametrosAuditoria["intOficinaId"]    = 0;
                $arrayParametrosAuditoria["strUsrCreacion"] = $usrCreacion;

                //Se consulta el login del cliente
                if(is_object($servicioTecnico->getServicioId()))
                {
                    $objInfoPunto = $this->emInfraestructura->getRepository('schemaBundle:InfoPunto')
                                                            ->find($servicioTecnico->getServicioId()->getPuntoId()->getId());
                    if(is_object($objInfoPunto))
                    {
                        $arrayParametrosAuditoria["strLogin"] = $objInfoPunto->getLogin();
                    }
                }

                $arrayParametrosAuditoria["strUsrCreacion"] = $usrCreacion;

                $this->serviceInfoElemento->ingresaAuditoriaElementos($arrayParametrosAuditoria);
                ////

                //historial elemento
                $objHistorialElemento = new InfoHistorialElemento();
                $objHistorialElemento->setElementoId($objCpeACambiar);
                $objHistorialElemento->setEstadoElemento($objCpeACambiar->getEstado());
                $objHistorialElemento->setObservacion("Se Cambio de Modelo, modelo anterior:"
                                                      .$objCpeACambiar->getModeloElementoId()->getNombreModeloElemento().
                                                      ", serie anterior:".$objCpeACambiar->getSerieFisica());
                $objHistorialElemento->setUsrCreacion($usrCreacion);
                $objHistorialElemento->setFeCreacion(new \DateTime('now'));
                $objHistorialElemento->setIpCreacion($ipCreacion);
                $this->emInfraestructura->persist($objHistorialElemento);
                $this->emInfraestructura->flush();


                //SE DESCONECTAN LAS INTERFACES DEL ELEMENTO WIFI Y SE ELIMINAN LOS ENLACES ASOCIADOS
                $arrayInterfaceElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                  ->findBy(array("elementoId" => $intIdElementoAnterior));

                foreach($arrayInterfaceElemento as $objInterfaceElemento)
                {
                    $objInterfaceElemento->setEstado("not connect");
                    $objInterfaceElemento->setUsrUltMod($usrCreacion);
                    $objInterfaceElemento->setFeUltMod(new \DateTime('now'));
                    $this->emInfraestructura->persist($objInterfaceElemento);

                    //Se obtiene la interface, del elemento recien generado, para los enlaces ONT - CPE  - SMARTWIFI
                    $arrayParametrosInterface["strNombreInterfaceElemento"] = $strNombreInterfaceElementoWanIsb;
                    $arrayParametrosInterface["intElementoId"]              = $objInfoElementoCpeWifi->getId();
                    $intInterfaceElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                    ->getPrimeraInterface($arrayParametrosInterface);

                    $objInfoInterfaceElementoFin = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                           ->find($intInterfaceElemento);

                    //SE ELIMINAN LOS ENLACES INI - SMART WIFI
                    $arrayInfoEnlacesIni = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                                   ->findBy(array("interfaceElementoIniId" => $objInterfaceElemento->getId(),
                                                                                  "estado"                 => "Activo"));

                    foreach($arrayInfoEnlacesIni as $objInfoEnlaceIni)
                    {
                        //Se obtiene la interface elemento fin del enlace, cuando tenga un servicio smart wifi
                        $intInterfaceElementoSmart = $objInfoEnlaceIni->getInterfaceElementoFinId();

                        $objInfoEnlaceIni->setEstado("Eliminado");
                        $this->emInfraestructura->persist($objInfoEnlaceIni);

                        //SE RECREA EL ENLACE ENTRE EL CPE WIFI Y EL SMART WIFI

                        //Se obtiene el tipo de interface
                        $objAdmiTipoMedio = $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')
                                                                    ->find($objInfoEnlaceIni->getTipoMedioId()->getId());

                        if(is_object($objInfoInterfaceElementoFin) && is_object($intInterfaceElementoSmart)
                           && is_object($objAdmiTipoMedio))
                        {

                            //Se pone en estado connect la interface
                            $objInfoInterfaceElementoFin->setEstado("connected");
                            $this->emInfraestructura->persist($objInfoInterfaceElementoFin);

                            //Se crea el nuevo enlace
                            $objInfoEnlace = new InfoEnlace();
                            $objInfoEnlace->setInterfaceElementoIniId($objInfoInterfaceElementoFin);
                            $objInfoEnlace->setInterfaceElementoFinId($intInterfaceElementoSmart);
                            $objInfoEnlace->setTipoMedioId($objAdmiTipoMedio);
                            $objInfoEnlace->setTipoEnlace($objInfoEnlaceIni->getTipoEnlace());
                            $objInfoEnlace->setEstado("Activo");
                            $objInfoEnlace->setUsrCreacion($usrCreacion);
                            $objInfoEnlace->setFeCreacion(new \DateTime('now'));
                            $objInfoEnlace->setIpCreacion($ipCreacion);
                            $this->emInfraestructura->persist($objInfoEnlace);
                        }
                    }

                    //SE ELIMINAN LOS ENLACES FIN
                    $arrayInfoEnlacesFin = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                                   ->findBy(array("interfaceElementoFinId" => $objInterfaceElemento->getId(),
                                                                                  "estado"                 => "Activo"));

                    foreach($arrayInfoEnlacesFin as $objInfoEnlaceFin)
                    {
                        $objInfoEnlaceFin->setEstado("Eliminado");
                        $this->emInfraestructura->persist($objInfoEnlaceFin);

                        //Se recrea el enlace Eliminado, asociando a la nueva interfaz generada

                        //Se obtiene la interface Ini
                        $objInfoInterfaceElementoIni = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                               ->find($objInfoEnlaceFin->getInterfaceElementoIniId()->getId());

                        //Se obtiene el tipo de interface
                        $objAdmiTipoMedio = $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')
                                                                    ->find($objInfoEnlaceFin->getTipoMedioId()->getId());

                        if(is_object($objInfoInterfaceElementoIni) && is_object($objInfoInterfaceElementoFin)
                           && is_object($objAdmiTipoMedio))
                        {

                            //Se pone en estado connect la interface
                            $objInfoInterfaceElementoFin->setEstado("connected");
                            $this->emInfraestructura->persist($objInfoInterfaceElementoFin);

                            //Se crea el nuevo enlace del ONT al CPE WIFI
                            $objInfoEnlace = new InfoEnlace();
                            $objInfoEnlace->setInterfaceElementoIniId($objInfoInterfaceElementoIni);
                            $objInfoEnlace->setInterfaceElementoFinId($objInfoInterfaceElementoFin);
                            $objInfoEnlace->setTipoMedioId($objAdmiTipoMedio);
                            $objInfoEnlace->setTipoEnlace($objInfoEnlaceFin->getTipoEnlace());
                            $objInfoEnlace->setEstado("Activo");
                            $objInfoEnlace->setUsrCreacion($usrCreacion);
                            $objInfoEnlace->setFeCreacion(new \DateTime('now'));
                            $objInfoEnlace->setIpCreacion($ipCreacion);
                            $this->emInfraestructura->persist($objInfoEnlace);
                        }
                    }
                }

                $this->emInfraestructura->flush();
            }
            /////////////////////////////////////////////// SE RECREA EL NUEVO ELEMENTO CPE WIFI //////////////////////////////////////////
            else
            {
                $intIdInterfaceElementoSplitter     = $servicioTecnico->getInterfaceElementoConectorId();
                $intIdInterfaceElementoCpeOnt       = $servicioTecnico->getInterfaceElementoClienteId();
                $intIdElementoCpeOnt                = $servicioTecnico->getElementoClienteId();

                $arrayParametrosCambioCpeOnt    = array(
                                                        "objPunto"                          => $servicio->getPuntoId(),
                                                        "intIdServicio"                     => $servicio->getId(),
                                                        "intIdPersona"                      => $arrayParametrosAuditoria["intIdPersona"],
                                                        "objServicioProdCaractMacAnterior"  => $servicioProdCaractMac,
                                                        "intIdProductoCaracteristicaMac"    => $productoCaracteristicaMac->getId(),
                                                        "intIdInterfaceElementoIzq"         => $intIdInterfaceElementoSplitter,
                                                        "intIdInterfaceElementoAnterior"    => $intIdInterfaceElementoCpeOnt,
                                                        "intIdElementoAnterior"             => $intIdElementoCpeOnt,//$intIdElementoCpeOnt,
                                                        "strSerieElementoNuevo"             => $serieCpe,
                                                        "strMacElementoNuevo"               => $macCpe,
                                                        "strModeloElementoNuevo"            => $modeloElementoCpe->getNombreModeloElemento(),
                                                        "strTipoElementoNuevo"              => "-ont",
                                                        "strCodEmpresa"                     => $idEmpresa,
                                                        "strUsrCreacion"                    => $usrCreacion,
                                                        "strIpCreacion"                     => $ipCreacion
                                                      );

                $arrayCambioCpeOnt       = $this->cambioElementoCpeOntWifiEnlacesInterfacesMd($arrayParametrosCambioCpeOnt);
                $strStatusCambioCpeOnt   = $arrayCambioCpeOnt['strStatus'];
                $strMensajeCambioCpeOnt  = $arrayCambioCpeOnt['strMensaje'];

                if($strStatusCambioCpeOnt === "OK")
                {
                    $objElementoNuevoCpeOnt                     = $arrayCambioCpeOnt["objElementoNuevo"];
                    $objInterfaceElementoNuevoCpeOntConnected   = $arrayCambioCpeOnt["objInterfaceElementoNuevoConnected"];

                    if(is_object($objElementoNuevoCpeOnt) && is_object($objInterfaceElementoNuevoCpeOntConnected))
                    {
                        $servicioTecnico->setInterfaceElementoClienteId($objInterfaceElementoNuevoCpeOntConnected->getId());
                        $servicioTecnico->setElementoClienteId($objElementoNuevoCpeOnt->getId());
                        $this->emComercial->persist($servicioTecnico);
                        $this->emComercial->flush();
                    }
                    else
                    {
                        throw new \Exception("No se ha podido obtener correctamente el elemento y la interface cliente nueva");
                    }
                }
                else
                {
                    throw new \Exception($strMensajeCambioCpeOnt);
                }
            }   
            
            //historial del servicio
            $servicioHistorial = new InfoServicioHistorial();
            $servicioHistorial->setServicioId($servicio);
            $servicioHistorial->setObservacion("Se Realizo un Cambio de Elemento Cliente ".$tipoElementoCpe);
            $servicioHistorial->setEstado($servicio->getEstado());
            $servicioHistorial->setUsrCreacion($usrCreacion);
            $servicioHistorial->setFeCreacion(new \DateTime('now'));
            $servicioHistorial->setIpCreacion($ipCreacion);
            $this->emComercial->persist($servicioHistorial);
            $this->emComercial->flush();
            
            $arrayDataConfirmacionTn['datos']['respuesta_confirmacion'] = "OK";
            $strStatusCambioTellion     = "OK";
            $strMensajeCambioTellion    = 'SE REALIZO EL CAMBIO DE ELEMENTO';
        }
        catch (\Exception $e) 
        {
            $strStatusCambioTellion  = "ERROR";
            $strMensajeCambioTellion = $e->getMessage();
            $this->serviceUtil->insertError("Telcos+",
                                            "InfoCambioElementoService->cambioElementoTellion",
                                            $e->getMessage(),
                                            $usrCreacion,
                                            $ipCreacion
                                           );
        }
        $arrayRespuestaFinal[]  = array('status'                    => $strStatusCambioTellion, 
                                        'mensaje'                   => $strMensajeCambioTellion, 
                                        'arrayDataConfirmacionTn'   => $arrayDataConfirmacionTn);
        return $arrayRespuestaFinal;
    }
    
    /**
     * Service que realiza el cambio del elemento del cliente en equipos huawei, con ejecucion de scripts
     *
     * @author Creado: John Vera <javera@telconet.ec>
     * @version 1.0 29-05-2015
     * 
     * @author Modificado: John Vera <javera@telconet.ec>
     * @version 1.1 07-09-2015 modificado John Vera
     * 
     * @author Modificado: Jesus Bozada <jbozada@telconet.ec>
     * @version 1.2 28-09-2015 modificado Jesus Bozada - Se elimina recuperación de caracteristica perfil, 
     *                                                   clientes HW no necesitan este valor
     * 
     * @author Modificado: Jesus Bozada <jbozada@telconet.ec>
     * @version 1.3 09-05-2016 modificado Jesus Bozada - Se agrega parametro empresa en metodo cambioElementoHuawei  
     *                                                   por conflictos de producto INTERNET DEDICADO
     * 
     * @author Modificado: Jesus Bozada <jbozada@telconet.ec>
     * @version 1.4 25-09-2016 modificado Jesus Bozada - Se agregan validaciones y se elimina partes de codigo con 
     *                                                   el objetivo de soportar el nuevo modelo de CPE HW
     * 
     * @author Modificado: Jesus Bozada <jbozada@telconet.ec>
     * @version 1.5 10-10-2016 modificado Jesus Bozada - Se agregan parametros y validaciones para gestionar cambio
     *                                                   de equipo HW de servicios que tienen solicitudes de migracion
     *                                                   finalizadas
     * 
     * @author Francisco Adum <fadum@netlife.net.ec>
     * @version 1.6 17-07-2017  Se agrega funcionalidad para que se ejecute con el middleware
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.7 06-09-2017 -  En la tabla INFO_DETALLE_ASIGNACION se guarda el PersonaEmpresaRolId del responsable de la tarea de
     *                            retiro de equipo
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.8 21-12-2017 - En la tabla INFO_DETALLE_ASIGNACION se registra el campo tipo asignado 'EMPLEADO'
     * 
     * @author Modificado: Jesus Bozada <jbozada@telconet.ec>
     * @version 1.9 06-03-2018 - Se agregan correcciones en creacion de elementos y en generación de solicitudes de retiro de equipos
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.0 26-02-2018 - Se agrega el flujo para servicios Internet Small Business
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.1 14-04-2018 - Se agrega validación para actualizar la MAC en los servicios con productos IP adicionales cuando el tipo de negocio
     *                           es PRO y el OLT tiene middleware
     * 
     * @param Array $arrayParametros [ 
     *                                 servicio               => Objeto de la tabla InfoServicio
     *                                 servicioTecnico        => Objeto de la tabla InfoServicioTecnico
     *                                 modeloElemento         => Objeto de la tabla AdmiModeloElemento
     *                                 interfaceElemento      => Objeto de la tabla InfoInterfaceElemento
     *                                 producto               => Objeto de la tabla AdmiProducto
     *                                 serieCpe               => Cadena de caracteres con serie de cpe 
     *                                 codigoArticulo         => Cadena de caracteres con modelo de cpe
     *                                 nombreCpe              => Cadena de caracteres con nombre de cpe
     *                                 descripcionCpe         => Cadena de caracteres con descripcion de cpe
     *                                 macCpe                 => Cadena de caracteres con mac de cpe
     *                                 tipoElementoCpe        => Cadena de caracteres con tipo de elemento de cpe 
     *                                 idEmpresa              => Cadena de caracteres con identificador de empresa
     *                                 idElementoCliente      => Numero entero identificador de elemento del cliente
     *                                 intIdElementoWifi      => Numero entero identificador del elemento wifi adicional a cambiar
     *                                 strModeloWifi          => Cadena de caracteres con modelo de wifi
     *                                 strMacWifi             => Cadena de caracteres con mac de wifi,
     *                                 strSerieWifi           => Cadena de caracteres con serie de wifi
     *                                 strDescripcionWifi     => Cadena de caracteres con descripcion de wifi
     *                                 strNombreWifi          => Cadena de caracteres con nombre de wifi
     *                                 strTieneMigracionHw    => Cadena de caracteres que indica si el cliente tiene una solicitud 
     *                                                           de migracion finalizada
     *                                 strEquipoCpeHw         => Cadena de caracteres que indica el tipo de operacion a realizar con 
     *                                                           el equipo CPE HW, ('MANTENER EQUIPO','CAMBIAR EQUIPO')
     *                                 strEquipoWifiAdicional => Cadena de caracteres que indica el tipo de operacion a realizar con 
     *                                                           el equipo WIFI ADICIONAL, ('MANTENER EQUIPO','CAMBIAR EQUIPO')
     *                                 strAgregarWifi         => Cadena de caracteres que indica si se desea agregar un Wifi adicional 
     *                                                           para clientes hw
     *                                 usrCreacion            => Cadena de caracteres con usuario de creacion a utilizar
     *                                 ipCreacion             => Cadena de caracteres con ip de creacion a utilizar
     *                                 prefijoEmpresa         => Cadena de caracteres con prefigo de empresa a utilizar,
     *                                 strEsIsb               => 'SI' o 'NO' es un servicio Internet Small Business 
     *                               ]
     * @author Modificado: Fabricio Bermeo <fbermeo@telconet.ec>
     * @version 2.2 22-05-2018 - Se cambia la validación luego de la llamada al middleware para verificar los flag de cancelación y activación 
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.3 24-05-2018 - Se realiza regularización por cambios en caliente en servicios PRO con IP adicional y sin IP en el plan
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.4 29-05-2018 - Se agrega el parametro del id_persona en el registro de la trazabilidad
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.5 30-05-2018 - Se agregan validaciones para las IPs adicionales Small Business
     * 
     * @author Modificado: Jesús Bozada <jbozada@telconet.ec>
     * @version 2.6 28-11-2018 - Se agregan validaciones para gestionar los productos de la empresa TNP
     * @since 2.5
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.7 27-02-2019 - Se agregan validaciones para realizar cambios de equipos Huawei de servicios TelcoHome
     * 
     * @author Modificado: Jesús Bozada <jbozada@telconet.ec>
     * @version 2.8 20-06-2019 - Se agregan validaciones y modificaciones para poder realizar cambios de equipos y activaciones de equipos WIFI DUAL BAND
     * @since 2.7
     * 
     * @author Modificado: Jesús Bozada <jbozada@telconet.ec>
     * @version 2.9 11-07-2019 - Se corrije método setEstado en la linea 4305 y se modifica validación de tipo
     *                           de proceso ejecutado para finalizar solicitudes
     * @since 2.8
     * 
     * @author Modificado: Antonio Ayala <afayala@telconet.ec>
     * @version 2.10 29-01-2020 - Se corrije condición en la linea 3227 agregando si el tipo de negocio es diferente de HOME
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.11 13-05-2020 - Se agrega el tipo de solicitud: 'SOLICITUD CAMBIO EQUIPO POR SOPORTE MASIVO' y 'SOLICITUD AGREGAR EQUIPO MASIVO'
     * @since 2.10
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.11 04-05-2020 - Se elimina la función obtenerInfoMapeoProdPrefYProdsAsociados y en su lugar se usa 
     *                             obtenerParametrosProductosTnGpon, debido a los cambios realizados por la reestructuración de servicios
     *                             Small Business
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 3.0 07-08-2020 - Se valida el response del middleware para cambio de equipo en los servicios TN.
     *                           (Internet Small Business y TelcoHome)
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 3.1 23-09-2020 Se agregan las validaciones respectivas para el flujo del W + AP cuando el servicio de Internet ya se encuentre Activo
     *                         de tal manera que se genere el cambio de equipo del CPE ONT y se cree el detalle a la solicitud de AGREGAR EQUIPO 
     *                         para agregar el Extender Dual Band. El servicio W + ap pasa a estado PendienteAp
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 3.2 01-12-2020 Se agrega verificación de equipo en NAF de acuerdo a validaciones existentes en AFK_PROCESOS.IN_P_PROCESA_INSTALACION,
     *                         antes de enviar el request a middleware, para evitar errores por NAF que obliguen la eliminación de línea pon
     *
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 3.3 16-11-2020 Se agregan validación para ejecutar cambio de planes PYME con nuevos parámetros enviados al middleware
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 3.4 15-04-2021 Se agrega nuevo parámetro para distinguir los servicios que requieren cambio de ont por medio de una solicitud
     *                         agregar equipo
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 3.4 24-03-2021 Se agrega logica que permita cambiar de un Dual Band a un V5
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 3.5 13-05-2021 Se agrega lógica para evitar finalizar las tareas asociadas a una solicitud cuando el origen es MOVIL
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 3.6 10-11-2021 Se construye el arreglo con la información que se enviará al web service para confirmación de opción de Tn a Middleware
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 3.7 21-01-2022 Se elimina la restricción para realizar el cambio de un equipo normal a un W. De esta manera para agregar un W, el 
     *                         ont anterior puede ser cualquiera. 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 3.8 18-03-2022 Se obtiene la información necesaria para escenario de cliente con plan PYME que no incluye IP, pero que tiene la
     *                         IP FIJA como un servicio adicional y se la envía a función cambioElementoCpeOntWifiEnlacesInterfacesMd que gestiona
     *                         la información de la MAC
     * 
     * @author Alberto Arias <farias@telconet.ec>
     * @version 3.9 22-05-2023 - Se agrega insertLog para verificar si el proceso de invocación a WS CONFIRMACION_TN, 
     * se realizó de forma correcta.
     * 
     * @author Alberto Arias <farias@telconet.ec>
     * @version 4.0 07-06-2023 - Se agrega error_log para verificar si se ejecutan las peticiones a MIDDLEWARE
     */
    public function cambioElementoHuawei($arrayParametros)
    {
        $servicio                   = $arrayParametros['servicio'];
        $servicioTecnico            = $arrayParametros['servicioTecnico'];
        $modeloElemento             = $arrayParametros['modeloElemento'];
        $interfaceElemento          = $arrayParametros['interfaceElemento'];
        $producto                   = $arrayParametros['producto'];
        $serieCpe                   = $arrayParametros['serieCpe'];
        $modeloCpe                  = $arrayParametros['codigoArticulo'];
        $nombreCpe                  = $arrayParametros['nombreCpe'];
        $descripcionCpe             = $arrayParametros['descripcionCpe'];
        $macCpe                     = $arrayParametros['macCpe'];
        $tipoElementoCpe            = $arrayParametros['tipoElementoCpe'];
        $empresa                    = $arrayParametros['idEmpresa'];
        $idElementoCliente          = $arrayParametros['idElementoCliente'];
        $usrCreacion                = $arrayParametros['usrCreacion'];
        $ipCreacion                 = $arrayParametros['ipCreacion'];
        $prefijoEmpresa             = $arrayParametros['prefijoEmpresa'];
        $intIdElementoWifi          = $arrayParametros['intIdElementoWifi'];
        $strModeloWifi              = $arrayParametros['strModeloWifi'];
        $strMacWifi                 = $arrayParametros['strMacWifi'];
        $strSerieWifi               = $arrayParametros['strSerieWifi']; 
        $strDescripcionWifi         = $arrayParametros['strDescripcionWifi'];
        $strNombreWifi              = $arrayParametros['strNombreWifi']; 
        $strTieneMigracionHw        = $arrayParametros['strTieneMigracionHw'];
        $strEquipoCpeHw             = $arrayParametros['strEquipoCpeHw'];    
        $strEsAgregarEquipoMasivo     = $arrayParametros['strEsAgregarEquipoMasivo']?$arrayParametros['strEsAgregarEquipoMasivo']:"NO";
        $strEsCambioEquiSoporteMasivo = $arrayParametros['strEsCambioEquiSoporteMasivo']?$arrayParametros['strEsCambioEquiSoporteMasivo']:"NO";
        $strEquipoWifiAdicional     = $arrayParametros['strEquipoWifiAdicional'];
        $strAgregarWifi             = $arrayParametros['strAgregarWifi'];
        $strEsIsb                   = $arrayParametros['strEsIsb'];
        $strCambioEquiposDualBand   = $arrayParametros['strCambioEquiposDualBand'];
        $strEsCambioPorSoporte      = $arrayParametros['strEsCambioPorSoporte'];
        $strEsWifiDualBand          = $arrayParametros['strEsWifiDualBand'];
        $objEmpleadoSesion          = $arrayParametros['objEmpleadoSesion'];
        $intIdServicioProdWifiDB    = $arrayParametros['intIdServicioProdWifiDB'];
        $strEsCambioOntPorSolAgregarEquipo = $arrayParametros['strEsCambioOntPorSolAgregarEquipo'];
        $valorTraffic               = '';
        $valorGemport               = '';
        $valorVlan                  = '';
        $valorLineProfile           = '';
        $spcSpid                    = '';
        $spcMacOnt                  = '';
        $servProdCaractIndiceCliente = '';
        $intIdPersona                = "";
        $servicioId                  = $servicio->getId();
        $strEstadoDetalleSol         = "";
        $perfil                      = '';
        $flagMiddleware              = false;
        $flagProdViejo               = 0;
        $strEquipoActualEsDB         = "NO";
        $strEquipoNuevoEsDB          = "NO";
        $strWifiDualBandEnPlan       = "NO";
        $objServicioProdWyAp         = null;
        $strServicioProdWyAp         = "NO";
        $strOrigen                   = $arrayParametros['strOrigen'];
        $strParametroOrigenTelcos    = "TELCOS"; 
        $arrayDataConfirmacionTn     = array();
        $arrayProdIp                 = array();
        $arrayDatosIpWan             = array();
        $strExisteIpWan              = "";
        $intIdServicioAdicIpWan      = 0;
        $objSpcMacIpWan              = null;
        
        $strOntActualEstaParametrizado = "NO";
        $strOntNuevoEstaParametrizado  = "NO";
        
        if($strEsCambioOntPorSolAgregarEquipo === "SI")
        {
            $strTipoSolicitud   = "SOLICITUD AGREGAR EQUIPO";
            $strEstadoSolicitud = "Asignada";
        }
        else if($strCambioEquiposDualBand === "SI")
        {
           if($strEsAgregarEquipoMasivo === "NO")
            {
                $strTipoSolicitud   = "SOLICITUD AGREGAR EQUIPO";
            }
            else
            {
                $strTipoSolicitud   = "SOLICITUD AGREGAR EQUIPO MASIVO";
            }            
            
            $strEstadoSolicitud = "Asignada";
        }
        else if($strEsCambioPorSoporte === "SI")
        {
            if($strEsCambioEquiSoporteMasivo === "NO")
            {
                $strTipoSolicitud   = "SOLICITUD CAMBIO EQUIPO POR SOPORTE";
            }
            else
            {
                $strTipoSolicitud   = "SOLICITUD CAMBIO EQUIPO POR SOPORTE MASIVO";
            }                

            $strEstadoSolicitud = "Asignada";
        }
        else
        {
            $strTipoSolicitud   = "SOLICITUD CAMBIO DE MODEM INMEDIATO";
            $strEstadoSolicitud = "AsignadoTarea";
        }
        if(isset($strEsIsb) && !empty($strEsIsb) && $strEsIsb === "SI")
        {
            if(!is_object($producto))
            {
                $arrayRespuestaFinal[] = array('status' => 'ERROR', 'mensaje' => 'No existe producto asociado al servicio');
                return $arrayRespuestaFinal;
            }
            $intIdProdPref              = $producto->getId();
            $strNombreTecnicoProdPref   = $producto->getNombreTecnico();
            if($strNombreTecnicoProdPref === "TELCOHOME")
            {
                $flagProdViejo          = 0;
                $strTipoNegocio         = "HOME";
            }
            else
            {
                $flagProdViejo          = 1;
                $strTipoNegocio         = "PYME";
                $arrayParamsInfoProds   = array("strValor1ParamsProdsTnGpon"    => "PRODUCTOS_RELACIONADOS_INTERNET_IP",
                                                "strCodEmpresa"                 => $empresa,
                                                "intIdProductoInternet"         => $intIdProdPref);
                $arrayInfoMapeoProds    = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                            ->obtenerParametrosProductosTnGpon($arrayParamsInfoProds);
                
                if(isset($arrayInfoMapeoProds) && !empty($arrayInfoMapeoProds))
                {
                    foreach($arrayInfoMapeoProds as $arrayInfoProd)
                    {
                        $intIdProductoIp    = $arrayInfoProd["intIdProdIp"];
                        $objProdIPSB        = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($intIdProductoIp);
                        $arrayProdIp[]      = $objProdIPSB;
                    }
                }
                else
                {
                    $arrayRespuestaFinal[]  = array('status'    => 'ERROR', 
                                                    'mensaje'   => 'No se ha podido obtener el correcto mapeo del servicio con la ip respectiva');
                    return $arrayRespuestaFinal;
                }
            }
        }
        else if($prefijoEmpresa === "TNP")
        {
            $flagProdViejo          = 0;
            $strTipoNegocio         = "HOME";
            $arrayProdIp            = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                        ->findBy(array( "nombreTecnico" => "IP",
                                                                        "empresaCod"    => $empresa,
                                                                        "estado"        => "Activo"));
        }
        else
        {
            //OBTENER TIPO DE NEGOCIO
            $strTipoNegocio         = $servicio->getPuntoId()->getTipoNegocioId()->getNombreTipoNegocio();
            $arrayProdIp            = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                        ->findBy(array( "nombreTecnico" => "IP",
                                                                        "empresaCod"    => $empresa,
                                                                        "estado"        => "Activo"));
        }
        
        $caracteristicaMac = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                               ->findOneBy(array( "descripcionCaracteristica" => "MAC ONT", "estado"=>"Activo"));
        $productoCaracteristicaMac = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                       ->findOneBy(array("productoId" => $producto->getId(), 
                                                                         "caracteristicaId" => $caracteristicaMac->getId()));
        $objCaracteristicaMacWifi = $this->emComercial
                                         ->getRepository('schemaBundle:AdmiCaracteristica')
                                         ->findOneBy(array( "descripcionCaracteristica" => "MAC WIFI", "estado"=>"Activo"));
        if (is_object($objCaracteristicaMacWifi))
        {
            $objProductoCaracteristicaWifi = $this->emComercial
                                                  ->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                  ->findOneBy(array("productoId"       => $producto->getId(), 
                                                                    "caracteristicaId" => $objCaracteristicaMacWifi->getId()));
        }
        $elemento = $interfaceElemento->getElementoId();

        $servicioProdCaractMac     = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "MAC ONT", $producto);
        $objServicioProdCaractWifi = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "MAC WIFI", $producto);

        //buscar caracteristicas para olt huawei
        //obtener ont
        $elementoCliente = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                   ->findOneById($servicioTecnico->getElementoClienteId());
        if($elementoCliente)
        {
            $modeloOnt = $elementoCliente->getModeloElementoId()->getNombreModeloElemento();
            $serieOnt = $elementoCliente->getSerieFisica();
        }
        else
        {
            $respuestaFinal[] = array('status' => 'ERROR', 'mensaje' => 'No existe el ont del cliente, favor revisar!');
            return $respuestaFinal;
        }
        
        // se valida que en caso de ser un cambio de modem inmediato HW no se permita cambiar de un EQUIPO ESTANDAR a un EQUIPO DUAL BAND
        if ($strTipoSolicitud == "SOLICITUD CAMBIO DE MODEM INMEDIATO")
        {
            //Se consulta los modelos DUAL BAND permitidos
            $strNombreParametro      = 'EQUIPOS_PERMITIDOS_CAMBIO_EQUIPO_POR_SOPORTE';
            $strModuloParametro      = 'TECNICO';
            $strProcesoParametro     = 'VALIDACION_DE_EQUIPOS';
            $strTipoEquiposParametro = 'DUAL BAND';
            $arrayModeloDualBand     = array();
            $arrayAdmiParametroDB    = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                       ->get($strNombreParametro, 
                                                             $strModuloParametro, 
                                                             $strProcesoParametro, 
                                                             '',
                                                             '',
                                                             $strTipoEquiposParametro,
                                                             '',
                                                             '',
                                                             '',
                                                             $empresa);
            if(is_array($arrayAdmiParametroDB) && count($arrayAdmiParametroDB) > 0)
            {
                foreach($arrayAdmiParametroDB as $arrayParametroDB)
                {   
                    $arrayModeloDualBand[] = $arrayParametroDB['valor1'];
                }
            }

            if(in_array($modeloOnt, $arrayModeloDualBand))
            {
                $strEquipoActualEsDB = "SI";
            }
            
            if(in_array($modeloCpe, $arrayModeloDualBand))
            {
                $strEquipoNuevoEsDB = "SI";
            }

            //Se consultan los modelos de CPE ONT parametrizados, por ejemplo: ONT V5
            $strNombreParametro         = 'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD';
            $strCampoValor1             = 'MODELOS_EQUIPOS';
            $strCampoValor6             = 'ONT';
            $arrayModelosParametrizados = array(); //por ejemplo modelo: ONT V5 => EG8M8145V5G06
            $arrayAdmiParametroOnt      = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                          ->get($strNombreParametro,
                                                                '',
                                                                '',
                                                                '',
                                                                $strCampoValor1,
                                                                '',
                                                                '',
                                                                '',
                                                                '',
                                                                $empresa,
                                                                '',
                                                                $strCampoValor6);

            if(is_array($arrayAdmiParametroOnt) && count($arrayAdmiParametroOnt) > 0)
            {
                foreach($arrayAdmiParametroOnt as $arrayParametroOnt)
                {
                    $arrayModelosParametrizados[] = $arrayParametroOnt['valor5'];
                }
            }

            if(in_array($modeloCpe, $arrayModelosParametrizados))
            {
                $strOntNuevoEstaParametrizado = "SI";
            }

            if(in_array($modeloOnt, $arrayModelosParametrizados))
            {
                $strOntActualEstaParametrizado = "SI";
            }
            
            $objPlanServicio                = $servicio->getPlanId();
            if(is_object($objPlanServicio))
            {
                $arrayRespuestaVerifProdEnPlan  = $this->servicioGeneral
                                                       ->obtieneProductoEnPlan(array(   "intIdPlan"                 => $objPlanServicio->getId(),
                                                                                        "strNombreTecnicoProducto"  => "WIFI_DUAL_BAND",
                                                                                     ));
                if($arrayRespuestaVerifProdEnPlan["strProductoEnPlan"] === "SI")
                {
                    $strWifiDualBandEnPlan = "SI";
                }

                if ($strWifiDualBandEnPlan == "SI" && $strEquipoActualEsDB == "SI" && $strEquipoNuevoEsDB == "NO" && 
                    $strOntNuevoEstaParametrizado == "NO")
                {
                    $arrayRespuestaFinal[]  = array('status'  => 'ERROR', 
                                                    'mensaje' => 'No se puede realizar este cambio de equipo, '.
                                                                 'su plan actual incluye el equipo WIFI DUAL BAND, '.
                                                                 'no puede utilizar un nuevo equipo que no sea DUAL BAND u '."ONT V5");
                    return $arrayRespuestaFinal;
                }

                if ($strWifiDualBandEnPlan == "SI" && $strOntActualEstaParametrizado == "SI" && $strOntNuevoEstaParametrizado == "NO")
                {
                    $arrayRespuestaFinal[]  = array('status'  => 'ERROR',
                                                    'mensaje' => 'No se puede realizar este cambio de equipo, '.
                                                                 'su plan actual incluye el equipo CPE ONT V5, '.
                                                                 'no puede utilizar un nuevo equipo que no sea CPE ONT V5');
                    return $arrayRespuestaFinal;
                }
            }
        }

        $spcLineProfile = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "LINE-PROFILE-NAME", $producto);
        if($spcLineProfile)
        {
            $valorLineProfile = $spcLineProfile->getValor();
        }
        else
        {
            $respuestaFinal[] = array('status' => 'ERROR', 'mensaje' => 'No existe Caracteristica LINE-PROFILE-NAME, favor revisar!');
            return $respuestaFinal;
        }          
        $spcVlan = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "VLAN", $producto);
        if($spcVlan)
        {
            $valorVlan = $spcVlan->getValor();
        }
        else
        {
            $respuestaFinal[] = array('status' => 'ERROR', 'mensaje' => 'No existe Caracteristica VLAN, favor revisar!');
            return $respuestaFinal;
        }
        $spcGemPort = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "GEM-PORT", $producto);
        if($spcGemPort)
        {
            $valorGemport = $spcGemPort->getValor();
        }
        else
        {
            $respuestaFinal[] = array('status' => 'ERROR', 'mensaje' => 'No existe Caracteristica GEM-PORT, favor revisar!');
            return $respuestaFinal;
        }
        $spcTraffic = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "TRAFFIC-TABLE", $producto);
        if($spcTraffic)
        {
            $valorTraffic = $spcTraffic->getValor();
        }
        else
        {
            $respuestaFinal[] = array('status' => 'ERROR', 'mensaje' => 'No existe Caracteristica TRAFFIC-TABLE, favor revisar!');
            return $respuestaFinal;
        }
        $spcSpid = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "SPID", $producto);
        if(!$spcSpid)
        {
            $respuestaFinal[] = array('status' => 'ERROR', 'mensaje' => 'No existe Caracteristica Spid, favor revisar!');
            return $respuestaFinal;
        }
        else
        {
            $strSpid = $spcSpid->getValor();
        }
        
        $spcMacOnt = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "MAC ONT", $producto);
        if(!$spcMacOnt)
        {
            $respuestaFinal[] = array('status' => 'ERROR', 'mensaje' => 'No existe Mac del Ont , favor revisar!');
            return $respuestaFinal;
        }
        
        $servProdCaractIndiceCliente = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "INDICE CLIENTE", $producto);
        if(!$servProdCaractIndiceCliente)
        {
            $respuestaFinal[] = array('status' => 'ERROR', 'mensaje' => 'No existe Caracteristica Indice, favor revisar!');
            return $respuestaFinal;
        }
        else
        {
            $strOntId = $servProdCaractIndiceCliente->getValor();
        }

        //obtengo el indice viejo del cliente
        $indiceClienteViejoSpc = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "INDICE CLIENTE", $producto);

        //obtener mac ont
        $servProdCaractMacOnt = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "MAC ONT", $producto);
        if($servProdCaractMacOnt)
        {
            $macOnt = $servProdCaractMacOnt->getValor();
        }
        else
        {
            $respuestaFinal[] = array('status' => 'ERROR', 'mensaje' => 'NO EXISTE MAC ONT DEL CLIENTE,' . $servicio->getId());
            return $respuestaFinal;
        }
        
        $objDetalleElementoMid  = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                        ->findOneBy(array(  "elementoId"   => $servicioTecnico->getElementoId(),
                                                            "detalleNombre"=> 'MIDDLEWARE',
                                                            "estado"       => 'Activo'));
        
        if($objDetalleElementoMid)
        {
            if($objDetalleElementoMid->getDetalleValor() == 'SI')
            {
                $flagMiddleware = true;
            }
        }

        try
        {
            if($flagMiddleware)
            {
                $strIpFija          = '';
                $scope              = '';
                $strServiceProfile  = '';
                
                //OBTENER DATOS DEL CLIENTE (NOMBRES E IDENTIFICACION)
                $objPersona         = $servicio->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId();
                $strIdentificacion  = $objPersona->getIdentificacionCliente();
                $strNombreCliente   = $objPersona->__toString();
                
                //verificar ip en el plan----------------------------------------------------------
                $planCabViejo = $servicio->getPlanId();
                if(is_object($planCabViejo))
                {
                    $planDetViejo = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                                  ->findBy(array("planId" => $planCabViejo->getId()));
                    
                    
                    for($i = 0; $i < count($planDetViejo); $i++)
                    {
                        for($intIndiceProdIp = 0; $intIndiceProdIp < count($arrayProdIp); $intIndiceProdIp++)
                        {
                            if($planDetViejo[$i]->getProductoId() == $arrayProdIp[$intIndiceProdIp]->getId())
                            {
                                $flagProdViejo = 1;
                                break;
                            }
                        }
                    }//for($i=0;$i<count($planDetViejo);$i++) 
                }
                //----------------------------------------------------------------------------------
                
                //OBTENER SERVICE-PROFILE
                $spcServiceProf = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "SERVICE-PROFILE", $producto);
                if(!$spcServiceProf)
                {
                    $elementoClienteAnterior = $this->emComercial->getRepository('schemaBundle:InfoElemento')
                                                    ->find($servicioTecnico->getElementoClienteId());
                    $this->servicioGeneral
                         ->ingresarServicioProductoCaracteristica($servicio, $producto, "SERVICE-PROFILE",
                                                                  $elementoClienteAnterior->getModeloElementoId()->getNombreModeloElemento(),
                                                                  $usrCreacion);

                    $strServiceProfile = $elementoClienteAnterior->getModeloElementoId()->getNombreModeloElemento();
                }
                else
                {
                    $strServiceProfile = $spcServiceProf->getValor();
                }
                
                $objCaractServiceProfile    = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                ->findOneBy(array(  "descripcionCaracteristica" => "SERVICE-PROFILE", 
                                                                                    "estado"                    => "Activo"));
                if(is_object($objCaractServiceProfile))
                {
                    $objProdCaractServiceProfile    = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                        ->findOneBy(array("productoId"          => $producto->getId(),
                                                                                          "caracteristicaId"    => $objCaractServiceProfile->getId())
                                                                                   );
                    if(is_object($objProdCaractServiceProfile))
                    {
                        $arrayServProdCaractsServiceProfiles    = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                                    ->findBy(array( "servicioId"                => $servicio->getId(),
                                                                                                    "productoCaracterisiticaId" => 
                                                                                                    $objProdCaractServiceProfile->getId(),
                                                                                                    "estado"                    => 'Activo'));
                        //Se eliminan todas las características de SERVICE-PROFILE asociadas al servicio 
                        foreach($arrayServProdCaractsServiceProfiles as $objServProdCaractServiceProfile)
                        {
                            $objServProdCaractServiceProfile->setEstado('Eliminado');
                            $objServProdCaractServiceProfile->setUsrUltMod($usrCreacion);
                            $objServProdCaractServiceProfile->setFeUltMod(new \DateTime('now'));
                            $this->emComercial->persist($objServProdCaractServiceProfile);
                            $this->emComercial->flush();
                        }
                    }
                }
                
                //Se ingresa nueva característica SERVICE-PROFILE con el modelo nuevo ingresado
                $this->servicioGeneral->ingresarServicioProductoCaracteristica( $servicio, $producto, "SERVICE-PROFILE",
                                                                                $modeloCpe, $usrCreacion); 
                
                //OBTENER IP DEL ELEMENTO
                $objIpElemento      = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                            ->findOneBy(array("elementoId" => $elemento->getId()));
                
                //OBTENER SERVICIOS DEL PUNTO
                $arrServiciosPunto  = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                    ->findBy(array("puntoId" => $servicio->getPuntoId(), "estado" => "Activo"));
                        
                //OBTENER IPS ADICIONALES
                $arrayDatosIp       = $this->servicioGeneral
                                           ->getInfoIpsFijaPunto($arrServiciosPunto, $arrayProdIp, $servicio, 'Activo', 'Activo',$producto);
                
                $cantIpCliente      = $arrayDatosIp['ip_fijas_activas'];

                if ($strTipoNegocio === 'PYME')
                {
                    //OBTENER IPS ADICIONALES
                    $arrayParametrosIpWan = array('objPunto'       => $servicio->getPuntoId(),
                                                  'strEmpresaCod'  => $empresa,
                                                  'strUsrCreacion' => $usrCreacion,
                                                  'strIpCreacion'  => $ipCreacion);
                    $arrayDatosIpWan      = $this->servicioGeneral
                                                 ->getIpFijaWan($arrayParametrosIpWan);
                }

                //SI EL SERVICIO TIENE IP EN EL PLAN
                if ($strTipoNegocio === 'PYME' && isset($arrayDatosIpWan['strStatus']) && !empty($arrayDatosIpWan['strStatus']) && 
                    $arrayDatosIpWan['strStatus'] === 'OK' && isset($arrayDatosIpWan['strExisteIpWan']) &&
                    !empty($arrayDatosIpWan['strExisteIpWan']) &&  $arrayDatosIpWan['strExisteIpWan'] === 'SI')
                {
                    $strIpFija = $arrayDatosIpWan['arrayInfoIp']['strIp'];
                    $scope     = $arrayDatosIpWan['arrayInfoIp']['strScope'];
                    $strExisteIpWan         = $arrayDatosIpWan['strExisteIpWan'];
                    $intIdServicioAdicIpWan = $arrayDatosIpWan['arrayInfoIp']['intIdServicioIp'];
                    $objSpcMacIpWan         = $arrayDatosIpWan['arrayInfoIp']['objSpcMac'];
                }
                else if($flagProdViejo == 1 && $strTipoNegocio <> 'HOME')
                {
                    $arrayServicioIp[] = array("idServicio" => $servicio->getId());

                    //OBTENER IP DEL PLAN
                    $ipFija     = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                       ->findOneBy(array("servicioId"=>$servicio->getId(),"estado"=>"Activo"));

                    $strIpFija  = $ipFija->getIp();
                    
                    if(isset($strEsIsb) && !empty($strEsIsb) && $strEsIsb === "SI")
                    {
                        $prodIpPlan = $producto;
                    }

                    //OBTENER SCOPE
                    $spcScope = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "SCOPE", $prodIpPlan);

                    if(!$spcScope)
                    {
                        //buscar scopes
                        $arrayScopeOlt = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')
                                                                 ->getScopePorIpFija($ipFija->getIp(), $servicioTecnico->getElementoId());

                        if (!$arrayScopeOlt)
                        {   
                            $arrayFinal[] = array('status'=>"ERROR", 'mensaje'=>"Ip Fija no pertenece a un Scope! <br>"
                                                                              . "Favor Comunicarse con el Dep. Gepon!");
                            return $arrayFinal;
                        }

                        $scope = $arrayScopeOlt['NOMBRE_SCOPE'];
                    }
                    else
                    {
                        $scope = $spcScope->getValor();
                    }
                }//if($flagProdViejo == 1)
                else if($cantIpCliente == 1)
                {
                    $arrayValores       = $arrayDatosIp['valores'];
                    $strIpFija          = $arrayValores[0]['ip'];
                    $intIdServicioIp    = $arrayValores[0]['id_servicio'];
                    $objServicioIp      = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicioIp);
                    //OBTENER SCOPE
                    $objSpcScope        = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioIp, 
                                                                                                    "SCOPE", 
                                                                                                    $objServicioIp->getProductoId()
                                                                                                   );
                    if(is_object($objSpcScope))
                    {
                        $scope          = $objSpcScope->getValor();  
                    }
                }
                
                if ($strTieneMigracionHw != 'SI' ||
                    ($strTieneMigracionHw == 'SI' && $strEquipoCpeHw == 'CAMBIAR EQUIPO') ||
                    $strCambioEquiposDualBand == 'SI' || $strEsCambioOntPorSolAgregarEquipo === "SI"
                   )
                {
                    $arrayVerifOntNaf   = $this->servicioGeneral->buscarEquipoEnNafPorParametros(array( "serieEquipo"           => $serieCpe,
                                                                                                        "estadoEquipo"          => "PI",
                                                                                                        "tipoArticuloEquipo"    => "AF",
                                                                                                        "modeloEquipo"          => $modeloCpe));
                    if($arrayVerifOntNaf["status"] === "ERROR")
                    {
                        throw new \Exception($arrayVerifOntNaf["mensaje"]);
                    }
                }
                
                $this->actualizarMacCambioElementoPro(array("strIpCreacion"         => $ipCreacion,
                                                            "strUsrCreacion"        => $usrCreacion,
                                                            "strTipoElementoCpe"    => $tipoElementoCpe,
                                                            "strCodEmpresa"         => $empresa,
                                                            "objProducto"           => $producto,
                                                            "objServicio"           => $servicio,
                                                            "strMacCpe"             => $macCpe,
                                                            "strTipoNegocio"        => $strTipoNegocio
                                                            ));
                //DATOS PARA EL MIDDLEWARE
                $arrayDatos = array(
                                        'serial_ont'            => $serieOnt,
                                        'mac_ont'               => $macOnt,
                                        'nombre_olt'            => $elemento->getNombreElemento(),
                                        'ip_olt'                => $objIpElemento->getIp(),
                                        'puerto_olt'            => $interfaceElemento->getNombreInterfaceElemento(),
                                        'modelo_olt'            => $elemento->getModeloElementoId()->getNombreModeloElemento(),
                                        'gemport'               => $valorGemport,
                                        'service_profile'       => $strServiceProfile,
                                        'line_profile'          => $valorLineProfile,
                                        'traffic_table'         => $valorTraffic,
                                        'ont_id'                => $strOntId,
                                        'service_port'          => $strSpid,
                                        'vlan'                  => $valorVlan,
                                        'estado_servicio'       => $servicio->getEstado(),
                                        'ip'                    => $strIpFija,     //ip plan actual
                                        'scope'                 => $scope,         //scope actual
                                        'ip_fijas_activas'      => $cantIpCliente,
                                        'tipo_negocio_actual'   => $strTipoNegocio,
                                        'serial_ont_nueva'      => $serieCpe,
                                        'mac_ont_nueva'         => $macCpe,
                                        'service_profile_nuevo' => $modeloCpe
                                    );
                if ($prefijoEmpresa === 'MD')
                {
                    $arrayRespuestaSeteaInfo = $this->servicioGeneral
                                                    ->seteaInformacionPlanesPyme(array("intIdPlan"         => $servicio->getPlanId()->getId(),
                                                                                       "intIdPunto"        => $servicio->getPuntoId()->getId(),
                                                                                       "strConservarIp"    => "",
                                                                                       "strTipoNegocio"    => $strTipoNegocio,
                                                                                       "strPrefijoEmpresa" => $prefijoEmpresa,
                                                                                       "strUsrCreacion"    => $usrCreacion,
                                                                                       "strIpCreacion"     => $ipCreacion,
                                                                                       "strTipoProceso"    => 'CAMBIAR_ELEMENTO',
                                                                                       "arrayInformacion"  => $arrayDatos));
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
                $arrayDatosMiddleware = array(
                                                'empresa'               => $prefijoEmpresa,
                                                'nombre_cliente'        => $strNombreCliente,
                                                'login'                 => $servicio->getPuntoId()->getLogin(),
                                                'identificacion'        => $strIdentificacion,
                                                'datos'                 => $arrayDatos,
                                                'opcion'                => $this->opcion,
                                                'ejecutaComando'        => $this->ejecutaComando,
                                                'usrCreacion'           => $usrCreacion,
                                                'ipCreacion'            => $ipCreacion
                                            );
                if ($strTieneMigracionHw != 'SI' ||
                    ($strTieneMigracionHw == 'SI' && $strEquipoCpeHw == 'CAMBIAR EQUIPO') ||
                    $strCambioEquiposDualBand == 'SI' || $strEsCambioOntPorSolAgregarEquipo === "SI"
                   )
                {
                    // LOG 'CAMBIAR EQUIPO' ENVIO
                    $this->serviceUtil->insertLog(array(
                        'enterpriseCode'      => 18,
                        'logType'             => 1,
                        'logOrigin'           => 'TELCOS',
                        'application'         => 'TELCOS',
                        'appClass'            => basename(__CLASS__),
                        'appMethod'           => basename(__FUNCTION__),
                        'descriptionError'    => 'PROCESO ANTES DE LA PETICION',
                        'status'              => 'Envio',
                        'messageUser'         =>  $this->opcion,
                        'appAction'           => 'CAMBIAR_EQUIPO_'.$servicio->getPuntoId()->getLogin(),
                        'inParameters'        => json_encode($arrayDatosMiddleware),
                        'creationUser'        => $usrCreacion));
                    $strErrorLog = "Proceso: CambioEquipoMD, Opción: CAMBIAR_EQUIPO"
                    .", Login: ".$servicio->getPuntoId()->getLogin()
                    .", appClass: ".basename(__CLASS__).", appMethod: ".basename(__FUNCTION__). ", creationUser: ".$usrCreacion
                    .", Status : Envio, Descripcion: PROCESO ANTES DE LA PETICION";
                    error_log($strErrorLog);
                    $arrayRespuesta = $this->rdaMiddleware->middleware(json_encode($arrayDatosMiddleware));
                    // LOG 'TRASLADAR' RESPUESTA
                    $this->serviceUtil->insertLog(array(
                        'enterpriseCode'      => 18,
                        'logType'             => 1,
                        'logOrigin'           => 'TELCOS',
                        'application'         => 'TELCOS',
                        'appClass'            => basename(__CLASS__),
                        'appMethod'           => basename(__FUNCTION__),
                        'descriptionError'    => 'PROCESO DESPUES DE LA PETICION',
                        'status'              => 'Respuesta',
                        'messageUser'         =>  $this->opcion,
                        'appAction'           => 'CAMBIAR_EQUIPO_'.$servicio->getPuntoId()->getLogin(),
                        'inParameters'        => json_encode($arrayRespuesta),
                        'creationUser'        => $usrCreacion));
                    $strErrorLog = "Proceso: CambioEquipoMD, Opción: CAMBIAR_EQUIPO"
                    .", Login: ".$servicio->getPuntoId()->getLogin()
                    .", appClass: ".basename(__CLASS__).", appMethod: ".basename(__FUNCTION__). ", creationUser: ".$usrCreacion
                    .", Status : Respuesta,  Descripcion: PROCESO DESPUES DE LA PETICION";
                    error_log($strErrorLog);
                    $strStatusCancelar = isset($arrayRespuesta['status_cancelar']) ? $arrayRespuesta['status_cancelar'] : $arrayRespuesta['status'];
                    $strStatusActivar  = isset($arrayRespuesta['status_activar']) ? $arrayRespuesta['status_activar'] : $arrayRespuesta['status'];
                    
                    if($strStatusActivar == 'OK' && $strStatusCancelar == 'OK')
                    {
                        $arrayDatosConfirmacionTn                           = $arrayDatos;
                        $arrayDatosConfirmacionTn['opcion_confirmacion']    = $this->opcion;
                        $arrayDatosConfirmacionTn['respuesta_confirmacion'] = 'ERROR';
                        $arrayDataConfirmacionTn    = array('nombre_cliente'    => $strNombreCliente,
                                                            'login'             => $servicio->getPuntoId()->getLogin(),
                                                            'identificacion'    => $strIdentificacion,
                                                            'datos'             => $arrayDatosConfirmacionTn,
                                                            'opcion'            => $this->strConfirmacionTNMiddleware,
                                                            'ejecutaComando'    => $this->ejecutaComando,
                                                            'usrCreacion'       => $usrCreacion,
                                                            'ipCreacion'        => $ipCreacion,
                                                            'empresa'           => $prefijoEmpresa,
                                                            'statusMiddleware'  => 'OK');
                        //buscar elemento cpe
                        $cpeNafArray        = $this->servicioGeneral->buscarElementoEnNaf($serieCpe, $modeloCpe , "PI", "ActivarServicio");
                        $cpeNaf             = $cpeNafArray[0]['status'];
                        $codigoArticuloCpe  = $cpeNafArray[0]['mensaje'];
                        if($cpeNaf == "OK")
                        {
                            //actualizamos registro en el naf del cpe
                            $arrayParametrosNaf = array(   'tipoArticulo'          => 'AF',
                                                           'identificacionCliente' => '',
                                                           'empresaCod'            => '',
                                                           'modeloCpe'             => $modeloCpe,
                                                           'serieCpe'              => $serieCpe,
                                                           'cantidad'              => '1');

                            $mensajeError = $this->procesaInstalacionElemento($arrayParametrosNaf);

                            if(strlen(trim($mensajeError)) > 0)
                            {
                                $respuestaFinal[] = array(  "status"                    => "NAF", 
                                                            "mensaje"                   => "ERROR WIFI NAF: " . $mensajeError, 
                                                            "arrayDataConfirmacionTn"   => $arrayDataConfirmacionTn);
                                return $respuestaFinal;
                            }
                        }
                        else
                        {
                            $respuestaFinal[] = array(  'status'                    => 'NAF', 
                                                        'mensaje'                   => $codigoArticuloCpe,
                                                        "arrayDataConfirmacionTn"   => $arrayDataConfirmacionTn);
                            return $respuestaFinal;
                        }
                    }
                    else
                    {
                        if(isset($arrayRespuesta['mensaje']))
                        {
                            throw new \Exception($arrayRespuesta['mensaje']);
                        }
                        else
                        {
                            throw new \Exception("Cancelar: ".$arrayRespuesta['mensaje_cancelar']." Activar: ".$arrayRespuesta['mensaje_activar']);
                        }
                    }
                }
            }
            else
            {
                if(isset($strEsIsb) && !empty($strEsIsb) && $strEsIsb === "SI")
                {
                    $arrayFinal[]   = array('status'    => "ERROR",
                                            'mensaje'   => "El OLT considerado no soporta el esquema del middleware"
                                                            . "Favor Comunicarse con Sistemas!");
                    return $arrayFinal;
                }
                $planEdicionLimitada = 'NO';
                //obtener caracteristica plan edicion limitada
                $caractEdicionLimitada      = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                ->findOneBy(array(  "descripcionCaracteristica"=>"EDICION LIMITADA",
                                                                                    "estado"                   =>"Activo"));
                $planCab                    = $this->emComercial->getRepository('schemaBundle:InfoPlanCab')
                                                                ->find($servicio->getPlanId()->getId());
                $planCaractEdicionLimitada  = $this->emComercial->getRepository('schemaBundle:InfoPlanCaracteristica')
                                                                ->findOneBy(array("planId"            =>$planCab->getId(),
                                                                                  "caracteristicaId"  =>$caractEdicionLimitada->getId(),
                                                                                  "estado"            =>$planCab->getEstado()));
                
                if($planCaractEdicionLimitada)
                {
                    $planEdicionLimitada = $planCaractEdicionLimitada->getValor();
                }
                
                //verificar ip en el plan----------------------------------------------------------
                $planCabViejo = $servicio->getPlanId();
                $planDetViejo = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                                  ->findBy(array("planId" => $planCabViejo->getId()));

                $flagProdViejo = 0;
                for($i = 0; $i < count($planDetViejo); $i++)
                {
                    for($intIndiceProdIp = 0; $intIndiceProdIp < count($arrayProdIp); $intIndiceProdIp++)
                    {
                        if($planDetViejo[$i]->getProductoId() == $arrayProdIp[$intIndiceProdIp]->getId())
                        {
                            $flagProdViejo = 1;
                            break;
                        }
                    }
                }//for($i=0;$i<count($planDetViejo);$i++)
                //----------------------------------------------------------------------------------
                //verificar si punto tiene ip adicional---------------------------------------------
                $serviciosPunto = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                    ->findBy(array("puntoId" => $servicio->getPuntoId()->getId()));
                $x = 1;
                $contIpsFijas = 0;
                $arrayServicioIp[] = array("idServicio" => "");
                for($i = 0; $i < count($serviciosPunto); $i++)
                {
                    $servicioPunto = $serviciosPunto[$i];
                    if(($servicioPunto->getEstado() == "Activo" || $servicioPunto->getEstado() == "In-Corte" ) &&
                        $servicioPunto->getId() != $servicio->getId())
                    {
                        if($servicioPunto->getPlanId())
                        {
                            $planCab = $this->emComercial->getRepository('schemaBundle:InfoPlanCab')->find($servicioPunto->getPlanId()->getId());
                            $planDet = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')->findBy(array("planId" => $planCab->getId()));

                            for($intIndicePlanDet = 0; $intIndicePlanDet < count($planDet); $intIndicePlanDet++)
                            {
                            //contar las ip que estan en planes
                                foreach($arrayProdIp as $productoIp)
                                {
                                    if($productoIp->getId() == $planDet[$intIndicePlanDet]->getProductoId())
                                    {
                                        $arrayServicioIp[] = array("idServicio" => $servicioPunto->getId());
                                        $contIpsFijas++;
                                    }
                                }
                            }
                        }//if($servicioPunto->getPlanId())
                        else
                        {
                            //contar las ip que estan como productos
                            $productoServicioPunto = $servicioPunto->getProductoId();
                            foreach($arrayProdIp as $productoIp)
                            {

                                if($productoIp->getId() == $productoServicioPunto->getId())
                                {
                                    $arrayServicioIp[] = array("idServicio" => $servicioPunto->getId());
                                    $arrayServicioIpProducto[] = array("idServicio" => $servicioPunto->getId());
                                    $contIpsFijas++;
                                }
                            }
                        }//else
                    }
                }//for($i=0; $i<count($serviciosPunto); $i++)
                //----------------------------------------------------------------------------------
                $totalIpPto = $contIpsFijas + $flagProdViejo;
                if ($strTieneMigracionHw != 'SI' || ($strTieneMigracionHw == 'SI' && $strEquipoCpeHw == 'CAMBIAR EQUIPO'))
                {
                    if($totalIpPto > 0 && $planEdicionLimitada == 'NO')
                    {
                            //ingreso la ip del servicio al arreglo
                            $ipServicio = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                                  ->findOneBy(array("servicioId"=> $servicioId,
                                                                                    "estado"    => "Activo"));
                            if($ipServicio)
                            {
                                $arrayIps[] = array("ip" => $ipServicio->getIp(), "tipo" => $ipServicio->getTipoIp());
                            }
                            //obtengo las ip de los servicios adicionales
                            for($i = 0; $i < count($arrayServicioIp); $i++)
                            {

                                if($arrayServicioIp[$i]['idServicio'])
                                {
                                    $ipViejas = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                                        ->findOneBy(array(  "servicioId" => $arrayServicioIp[$i]['idServicio'],
                                                                                            "estado" => "Activo"));
                                    if($ipViejas)
                                    {
                                        $arrayIps[] = array("ip" => $ipViejas->getIp(), "tipo" => $ipViejas->getTipoIp());
                                    }
                                }
                            }

                         //cancelar servicio e ips adicionales

                        for($i = 0; $i < $totalIpPto; $i++)
                        {
                            $tmp = $i;
                            //si la ip esta dentro del plan de internet
                            if($flagProdViejo == 1 && $planConIp == 0)
                            {
                                $servicioIpPlan = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                                          ->findOneBy(array("servicioId" => $servicio->getId(), "estado" => "Activo"));


                                //obtener caracteristica scope
                                $spcScope = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "SCOPE", $producto);
                                if(!$spcScope)
                                {
                                    //obtener ip fija
                                    $ipFija = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                                      ->findOneBy(array("servicioId" => $servicio->getId(), "estado" => "Activo"));

                                    //buscar scopes
                                    $arrayScopeOlt = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')
                                                                             ->getScopePorIpFija($ipFija->getIp(), $servicioTecnico->getElementoId());

                                    if(!$arrayScopeOlt)
                                    {
                                        $arrayFinal[] = array(  'status' => "ERROR",
                                                                'mensaje' => "Ip Fija " . $ipFija->getIp() . " no pertenece a un Scope! <br>"
                                                                            . "Favor Comunicarse con el Dep. Gepon!");
                                        return $arrayFinal;
                                    }

                                    $scope = $arrayScopeOlt['NOMBRE_SCOPE'];
                                }
                                else
                                {
                                    $scope = $spcScope->getValor();
                                }

                                //cancelamos (script) servicio con ip
                                $arrayParametros = array(
                                    'servicioTecnico'   => $servicioTecnico,
                                    'interfaceElemento' => $interfaceElemento,
                                    'modeloElemento'    => $elemento->getModeloElementoId(),
                                    'producto'          => $producto,
                                    'login'             => $servicio->getPuntoId()->getLogin(),
                                    'spcIndiceCliente'  => $servProdCaractIndiceCliente,
                                    'spcSpid'           => $spcSpid,
                                    'spcMacOnt'         => $spcMacOnt,
                                    'scope'             => $scope);

                                $respuestaArrayCancel = $this->cancelarService->cancelarServicioMdConIp($arrayParametros);
                                $statusCancel         = $respuestaArrayCancel[0]['status'];

                                if($statusCancel == "OK")
                                {
                                //eliminamos ip vieja
                                    if($servicioIpPlan)
                                    {
                                        $servicioIpPlan->setEstado("Eliminado");
                                        $this->emInfraestructura->persist($servicioIpPlan);
                                        $this->emInfraestructura->flush();
                                    }

                                    $planConIp = 1;
                                }//if($statusCancel=="OK")
                                else
                                {
                                    $arrayFinal[] = array('status' => "ERROR", 'mensaje' => 'No se pudo Cancelar el servicio con IP, <br>'
                                        . 'Favor verificar todos los datos!</br>' . $respuestaArrayCancel[0]['mensaje']);
                                    return $arrayFinal;
                                }
                            }//if ($flagProdViejo==1)
                            else if($flagProdViejo == 0 && $planConIp == 0)
                            {

                                $arrayParametros = array(
                                    'servicioTecnico'   => $servicioTecnico,
                                    'interfaceElemento' => $interfaceElemento,
                                    'modeloElemento'    => $elemento->getModeloElementoId(),
                                    'spcIndiceCliente'  => $servProdCaractIndiceCliente,
                                    'login'             => $servicio->getPuntoId()->getLogin(),
                                    'spcSpid'           => $spcSpid,
                                    'spcMacOnt'         => $spcMacOnt,
                                    'idEmpresa'         => $empresa
                                );

                                $respuestaArrayCancel = $this->cancelarService->cancelarServicioMdSinIp($arrayParametros);

                                $statusCancel = $respuestaArrayCancel[0]['status'];

                                if($statusCancel == "ERROR")
                                {
                                    $arrayFinal[] = array('status' => "ERROR", 'mensaje' => 'No se pudo Cancelar el servicio sin Ip, <br>'
                                        . 'Favor verificar todos los datos!</br>' . $respuestaArrayCancel[0]['mensaje']);
                                    return $arrayFinal;
                                }
                                //si entra por aqui significa es un plan sin ip pero con una ip adicional o fija.. aumento el  $totalIpPto
                                $proConIpAdi = "OK";
                                $totalIpPto++;
                                $planConIp = 1;
                            }
                            else
                            {
                                //servicio adicional de ip
                                $servicioIpAdicional = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                         ->find($arrayServicioIp[$i]['idServicio']);
                                $spcMac = '';
                                $scopeAdicional = '';

                                $spcMac = $this->servicioGeneral->getServicioProductoCaracteristica($servicioIpAdicional, "MAC", $producto);

                                //obtener caracteristica scope
                                $spcScopeAdi = $this->servicioGeneral->getServicioProductoCaracteristica($servicioIpAdicional, "SCOPE", $producto);

                                if(!$spcScopeAdi)
                                {
                                    //obtener ip fija
                                    $ipFija = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                                      ->findOneBy(array("servicioId" => $servicioIpAdicional->getId(), "estado" => "Activo"));

                                    //buscar scopes
                                    $arrayScopeOlt = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')
                                                                             ->getScopePorIpFija($ipFija->getIp(), $servicioTecnico->getElementoId());

                                    if(!$arrayScopeOlt)
                                    {
                                        $arrayFinal[] = array('status' => "ERROR", 'mensaje' => "Ip Fija Adicional no pertenece a un Scope! <br>"
                                            . "Favor Comunicarse con el Dep. Gepon!");
                                        return $arrayFinal;
                                    }

                                    $scopeAdicional = $arrayScopeOlt['NOMBRE_SCOPE'];
                                }
                                else
                                {
                                    $scopeAdicional = $spcScopeAdi->getValor();
                                }

                                $arrParametrosCancel = array(
                                    'servicioTecnico'   => $servicioTecnico,
                                    'modeloElemento'    => $elemento->getModeloElementoId(),
                                    'interfaceElemento' => $interfaceElemento,
                                    'producto'          => $producto,
                                    'servicio'          => $servicioIpAdicional,
                                    'spcIndiceCliente'  => $indiceClienteViejoSpc,
                                    'spcMac'            => $spcMac,
                                    'scope'             => $scopeAdicional
                                );

                                //cancelar (script) ip adicional                        
                                $this->cancelarService->cancelarServicioIp($arrParametrosCancel);

                                //eliminar (base) ip adicional
                                $ipAdicional = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                                       ->findOneBy(array("servicioId"   => $servicioIpAdicional->getId(),
                                                                                         "estado"       => "Activo"));
                                if($ipAdicional)
                                {
                                    $ipAdicional->setEstado("Eliminado");
                                    $this->emInfraestructura->persist($ipAdicional);
                                    $this->emInfraestructura->flush();
                                }
                            }
                        }//for ($i=0;$i<$totalIpPto;$i++)
                        //Estado eliminado a los indices de la tabla producto servicio caracteristica
                        $this->servicioGeneral->setEstadoServicioProductoCaracteristica($indiceClienteViejoSpc, "Eliminado");
                        //activar servicio e ips adicional
                        $planConIp = 0;
                        for($i = 0; $i < $totalIpPto; $i++)
                        {
                            $tmp = $i;
                            //si la ip esta dentro del plan de internet

                            if($flagProdViejo == 1 && $planConIp == 0)
                            {
                                //reservamos la ip nueva
                                $ipFija = new InfoIp();
                                $ipFija->setIp($arrayIps[$i]['ip']);
                                $ipFija->setEstado("Reservada");
                                $ipFija->setTipoIp($arrayIps[$i]['tipo']);
                                $ipFija->setServicioId($servicio->getId());
                                $ipFija->setUsrCreacion($usrCreacion);
                                $ipFija->setFeCreacion(new \DateTime('now'));
                                $ipFija->setIpCreacion($ipCreacion);
                                $this->emInfraestructura->persist($ipFija);
                                $this->emInfraestructura->flush();

                                //obtener service profile, buscar el service profile en el elemento
                                $elemento        = $interfaceElemento->getElementoId();
                                $detalleElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                           ->findOneBy(array(   "detalleNombre" => "SERVICE-PROFILE-NAME",
                                                                                                "detalleValor"  => $modeloOnt,
                                                                                                "elementoId"    => $elemento->getId()));
                                if($detalleElemento)
                                {
                                    $serviceProfile = $detalleElemento->getDetalleValor();

                                    //se registra caracteristica service-profile
                                    $this->servicioGeneral->ingresarServicioProductoCaracteristica( $servicio, 
                                                                                                    $producto, 
                                                                                                    "SERVICE-PROFILE", 
                                                                                                    $serviceProfile, 
                                                                                                    $usrCreacion );
                                }
                                else
                                {
                                    $respuestaFinal[] = array('status'  => 'ERROR',
                                                              'mensaje' => 'No existe Caracteristica SERVICE-PROFILE-NAME '.
                                                                           'en el elemento, favor revisar!');
                                    return $respuestaFinal;
                                }

                                $arrayParametros = array(
                                    'servicio'          => $servicio,
                                    'servicioTecnico'   => $servicioTecnico,
                                    'interfaceElemento' => $interfaceElemento,
                                    'modeloElemento'    => $elemento->getModeloElementoId(),
                                    'producto'          => $producto,
                                    'macOnt'            => $macCpe,
                                    'macWifi'           => $macWifi,
                                    'perfil'            => $perfil,
                                    'login'             => $servicio->getPuntoId()->getLogin(),
                                    'ontLineProfile'    => $valorLineProfile,
                                    'serviceProfile'    => $serviceProfile,
                                    'serieOnt'          => $serieCpe,
                                    'vlan'              => $valorVlan,
                                    'gemPort'           => $valorGemport,
                                    'trafficTable'      => $valorTraffic,
                                    'usrCreacion'       => $usrCreacion
                                );
                                //activamos servicio con ip

                                $respuestaArrayActivar = $this->activarService->activarClienteMdConIp($arrayParametros);
                                $strStatusActivar      = $respuestaArrayActivar[0]['status'];

                                if($strStatusActivar == "OK")
                                {

                                    $ipFija->setEstado("Activo");
                                    $this->emInfraestructura->persist($ipFija);
                                    $this->emInfraestructura->flush();

                                    //guardamos el indice
                                    $indiceCliente = $respuestaArrayActivar[0]['mensaje'];

                                    $this->servicioGeneral->ingresarServicioProductoCaracteristica($servicio, $producto, "INDICE CLIENTE", $indiceCliente,
                                                                                                   $usrCreacion);

                                    $arraySpid = array( 'modeloElemento'    => $elemento->getModeloElementoId(),
                                                        'interfaceElemento' => $interfaceElemento,
                                                        'ontId'             => $indiceCliente,
                                                        'servicioTecnico'   => $servicioTecnico);

                                    $resultArraySpid = $this->cambiarPuertoService->getSpidHuawei($arraySpid);

                                    $spidStatus = $resultArraySpid[0]['status'];

                                    if($spidStatus == 'ERROR')
                                    {
                                        $arrayFinal[] = array('status' => "ERROR",
                                                              'mensaje' => 'No se pudo consultar el spid, <br>' . $resultArraySpid['mensaje']);
                                        return $arrayFinal;
                                    }
                                    else
                                    {
                                        $spidViejoSpc = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "SPID", $producto);
                                        $this->servicioGeneral->setEstadoServicioProductoCaracteristica($spidViejoSpc, "Eliminado");

                                        $this->servicioGeneral->ingresarServicioProductoCaracteristica($servicio, $producto, "SPID", 
                                                                                                       $resultArraySpid[0]['mensaje'], $usrCreacion);
                                        /* se elimina service profile anterior y se crea nuevo service profile con el modelo de elemento ingresado
                                           para el cambio de equipo */
                                        $srvProfileProdCaract = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "SERVICE-PROFILE", $producto);
                                        if($srvProfileProdCaract)
                                        {
                                            $this->servicioGeneral->setEstadoServicioProductoCaracteristica($srvProfileProdCaract, "Eliminado");
                                        }
                                        $this->servicioGeneral->ingresarServicioProductoCaracteristica( $servicio, 
                                                                                                        $producto, 
                                                                                                        "SERVICE-PROFILE", 
                                                                                                        $serviceProfile, 
                                                                                                        $usrCreacion );
                                    }

                                    $planConIp = 1;
                                }//if($statusActivar=="OK")
                                else
                                {
                                    $ipFija->setEstado("Eliminado");
                                    $this->emInfraestructura->persist($ipFija);
                                    $this->emInfraestructura->flush();

                                    $arrayParametros = array(
                                        'servicio'          => $servicio,
                                        'servicioTecnico'   => $servicioTecnico,
                                        'interfaceElemento' => $interfaceElemento,
                                        'modeloElemento'    => $elemento->getModeloElementoId(),
                                        'producto'          => $producto,
                                        'macOnt'            => $macOnt,
                                        'macWifi'           => $macWifi,
                                        'perfil'            => $perfil,
                                        'login'             => $servicio->getPuntoId()->getLogin(),
                                        'ontLineProfile'    => $valorLineProfile,
                                        'serviceProfile'    => $serviceProfile,
                                        'serieOnt'          => $serieOnt,
                                        'vlan'              => $valorVlan,
                                        'gemPort'           => $valorGemport,
                                        'trafficTable'      => $valorTraffic,
                                        'usrCreacion'       => $usrCreacion
                                    );

                                    //activamos servicio con ip del puerto anterior
                                    $respuestaArrayActivarClie = $this->activarService->activarClienteMdConIp($arrayParametros);
                                    //activo el indice viejo del cliente
                                    $this->servicioGeneral->setEstadoServicioProductoCaracteristica($indiceClienteViejoSpc, "Activo");
                                    $arrayFinal[] = array('status' => "ERROR", 
                                                          'mensaje' => 'No se pudo Activar cliente con Ip, <br>'
                                                                      . 'Favor verificar todos los datos!<br>' . $respuestaArrayActivar[0]['mensaje']);
                                    return $arrayFinal;
                                }
                            }//if ($flagProdViejo==1)
                            else if($flagProdViejo == 0 && $planConIp == 0)
                            {
                                $arrayParametros = array(
                                    'servicioTecnico'   => $servicioTecnico,
                                    'interfaceElemento' => $interfaceElemento,
                                    'modeloElemento'    => $elemento->getModeloElementoId(),
                                    'macOnt'            => $macCpe,
                                    'perfil'            => $perfil,
                                    'login'             => $servicio->getPuntoId()->getLogin(),
                                    'ontLineProfile'    => $valorLineProfile,
                                    'serviceProfile'    => $modeloCpe,
                                    'serieOnt'          => $serieCpe,
                                    'vlan'              => $valorVlan, //VLAN
                                    'gemPort'           => $valorGemport, //GEM-PORT
                                    'trafficTable'      => $valorTraffic //TRAFFIC-TABLE
                                );
                                //activamos servicio sin ip
                                $respuestaArrayActivar = $this->activarService->activarClienteMdSinIp($arrayParametros);
                                $strStatusActivar      = $respuestaArrayActivar[0]['status'];
                                if($strStatusActivar == "ERROR")
                                {
                                    $arrayParametros = array(
                                        'servicioTecnico'   => $servicioTecnico,
                                        'interfaceElemento' => $interfaceElemento,
                                        'modeloElemento'    => $elemento->getModeloElementoId(),
                                        'macOnt'            => $macOnt,
                                        'perfil'            => $perfil,
                                        'login'             => $servicio->getPuntoId()->getLogin(),
                                        'ontLineProfile'    => $valorLineProfile,
                                        'serviceProfile'    => $modeloOnt,
                                        'serieOnt'          => $serieOnt,
                                        'vlan'              => $valorVlan, //VLAN
                                        'gemPort'           => $valorGemport, //GEM-PORT
                                        'trafficTable'      => $valorTraffic //TRAFFIC-TABLE
                                    );

                                    //activamos servicio sin ip del puerto anterior
                                    $respuestaArrayActivarClie = $this->activarService->activarClienteMdSinIp($arrayParametros);
                                    //activamos el indice anterior del cliente
                                    $this->servicioGeneral->setEstadoServicioProductoCaracteristica($indiceClienteViejoSpc, "Activo");
                                    $arrayFinal[] = array('status' => "ERROR", 
                                                          'mensaje' => 'No se pudo Activar el Puerto Nuevo, <br>'
                                                                       . 'Favor verificar todos los datos !<br>' . $respuestaArrayActivar[0]['mensaje']);
                                    return $arrayFinal;
                                }
                                $indiceCliente = $respuestaArrayActivar[0]['mensaje'];
                                //creacion de indice en la tabla servicio producto caracteristica
                                $this->servicioGeneral->ingresarServicioProductoCaracteristica($servicio, $producto, "INDICE CLIENTE", $indiceCliente,
                                    $usrCreacion);

                                $arraySpid = array( 'modeloElemento'    => $modeloElemento,
                                                    'interfaceElemento' => $interfaceElemento,
                                                    'ontId'             => $indiceCliente,
                                                    'servicioTecnico'   => $servicioTecnico);

                                $resultArraySpid = $this->cambiarPuertoService->getSpidHuawei($arraySpid);

                                $spidStatus = $resultArraySpid[0]['status'];

                                if($spidStatus == 'ERROR')
                                {
                                    $arrayFinal[] = array('status' => "ERROR",
                                        'mensaje' => 'No se pudo consultar el spid, <br>'
                                        . $resultArraySpid['mensaje']);
                                    return $arrayFinal;
                                }
                                else
                                {
                                    $spidViejoSpc = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "SPID", $producto);
                                    $this->servicioGeneral->setEstadoServicioProductoCaracteristica($spidViejoSpc, "Eliminado");

                                    $this->servicioGeneral->ingresarServicioProductoCaracteristica( $servicio, $producto, "SPID", 
                                                                                                    $resultArraySpid[0]['mensaje'], $usrCreacion );
                                    /* se elimina service profile anterior y se crea nuevo service profile con el modelo de elemento ingresado
                                       para el cambio de equipo */
                                    $srvProfileProdCaract = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "SERVICE-PROFILE", $producto);
                                    if($srvProfileProdCaract)
                                    {
                                        $this->servicioGeneral->setEstadoServicioProductoCaracteristica($srvProfileProdCaract, "Eliminado");
                                    }
                                    $this->servicioGeneral->ingresarServicioProductoCaracteristica($servicio, 
                                                                                                   $producto, 
                                                                                                   "SERVICE-PROFILE", 
                                                                                                   $modeloCpe,
                                                                                                   $usrCreacion);
                                }


                                $planConIp = 1;
                            }//else if($flagProdViejo==0)
                            else
                            {
                                //servicio adicional de ip
                                $servicioIpAdicional = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                    ->findOneById($arrayServicioIp[$i]['idServicio']); 

                                $spcMacIp = $this->servicioGeneral->getServicioProductoCaracteristica($servicioIpAdicional, "MAC", $producto);

                                if($spcMacIp)
                                {
                                    $macIp = $spcMacIp->getValor();
                                }


                                if($proConIpAdi == "OK")
                                {
                                    $ipCliente      = $arrayIps[$i-1]['ip'];
                                    $tipoIpCliente  = $arrayIps[$i-1]['tipo'];
                                    $macIp          = $macCpe;
                                }
                                else
                                {
                                    $ipCliente      = $arrayIps[$i]['ip'];
                                    $tipoIpCliente  = $arrayIps[$i]['tipo'];
                                }
                                //reservamos la ip nueva
                                $ipFija = new InfoIp();
                                $ipFija->setIp($ipCliente);
                                $ipFija->setEstado("Reservada");
                                $ipFija->setTipoIp($tipoIpCliente);
                                $ipFija->setServicioId($servicioIpAdicional->getId());
                                $ipFija->setUsrCreacion($usrCreacion);
                                $ipFija->setFeCreacion(new \DateTime('now'));
                                $ipFija->setIpCreacion($ipCreacion);
                                $this->emInfraestructura->persist($ipFija);
                                $this->emInfraestructura->flush();

                                $arrayPeticiones['ipFija']      = $ipCliente;
                                $arrayPeticiones['mac']         = $macIp;
                                $arrayPeticiones['idServicio']  = $servicioIpAdicional->getId();
                                $arrayPeticiones['idEmpresa']   = $empresa;
                                $arrayPeticiones['usrCreacion'] = $usrCreacion;
                                $arrayPeticiones['ipCreacion']  = $ipCreacion;

                                $activarServicioIp = $this->cambiarPuertoService->activarIpAdicionalHuawei($arrayPeticiones);

                                //activar (script y base) ip adicional
                                $activarServicioIpStatus = $activarServicioIp['status'];
                                if($activarServicioIpStatus == 'ERROR')
                                {
                                    $arrayFinal[] = array('status' => "ERROR",
                                                          'mensaje' => 'No se pudo Activar la ip adicional, <br>' . $activarServicioIp['mensaje']);
                                    return $arrayFinal;
                                }
                            }
                        }

                    }//if($totalIpPto>0)//planes sin ip
                    else
                    {
                        $spcSpid = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "SPID", $producto);
                        $spcMacOnt = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "MAC ONT", $producto);
                        //cancelar servicio
                        $servProdCaractIndiceCliente = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "INDICE CLIENTE", $producto);
                        $arrayParametrosCancel = array(
                            'servicioTecnico'   => $servicioTecnico,
                            'interfaceElemento' => $interfaceElemento,
                            'modeloElemento'    => $elemento->getModeloElementoId(),
                            'spcIndiceCliente'  => $servProdCaractIndiceCliente,
                            'login'             => $servicio->getPuntoId()->getLogin(),
                            'spcSpid'           => $spcSpid,
                            'spcMacOnt'         => $spcMacOnt,
                            'idEmpresa'         => $empresa
                        );
                        $respuestaArrayCancel = $this->cancelarService->cancelarServicioMdSinIp($arrayParametrosCancel);
                        $statusCancel         = $respuestaArrayCancel[0]['status'];

                        if($statusCancel == "ERROR")
                        {
                            $arrayFinal[] = array('status' => "ERROR", 
                                                  'mensaje' => 'No se pudo Cancelar el servicio sin IPs, <br>'
                                                               . 'Favor verificar todos los datos!<br>' . $respuestaArrayCancel[0]['mensaje']);
                            return $arrayFinal;
                        }

                        //poner estado eliminado a los indices de la tabla producto servicio caracteristica
                        $this->servicioGeneral->setEstadoServicioProductoCaracteristica($indiceClienteViejoSpc, "Eliminado");

                        $arrayParametros = array(
                            'servicioTecnico'   => $servicioTecnico,
                            'interfaceElemento' => $interfaceElemento,
                            'modeloElemento'    => $elemento->getModeloElementoId(),
                            'macOnt'            => $macCpe,
                            'perfil'            => $perfil,
                            'login'             => $servicio->getPuntoId()->getLogin(),
                            'ontLineProfile'    => $valorLineProfile,
                            'serviceProfile'    => $modeloCpe,
                            'serieOnt'          => $serieCpe,
                            'vlan'              => $valorVlan, //VLAN
                            'gemPort'           => $valorGemport, //GEM-PORT
                            'trafficTable'      => $valorTraffic //TRAFFIC-TABLE
                        );

                        //activamos servicio sin ip
                        $respuestaArrayActivar = $this->activarService->activarClienteMdSinIp($arrayParametros);
                        $strStatusActivar      = $respuestaArrayActivar[0]['status'];

                        if($strStatusActivar == "ERROR")
                        {
                            $arrayParametros = array(
                                'servicioTecnico'   => $servicioTecnico,
                                'interfaceElemento' => $interfaceElemento,
                                'modeloElemento'    => $elemento->getModeloElementoId(),
                                'macOnt'            => $macOnt,
                                'perfil'            => $perfil,
                                'login'             => $servicio->getPuntoId()->getLogin(),
                                'ontLineProfile'    => $valorLineProfile,
                                'serviceProfile'    => $modeloOnt,
                                'serieOnt'          => $serieOnt,
                                'vlan'              => $valorVlan, //VLAN
                                'gemPort'           => $valorGemport, //GEM-PORT
                                'trafficTable'      => $valorTraffic //TRAFFIC-TABLE
                            );

                            //activamos servicio sin ip del puerto anterior
                            $respuestaArrayActivarClie = $this->activarService->activarClienteMdSinIp($arrayParametros);

                            //activamos el indice anterior del cliente
                            $this->servicioGeneral->setEstadoServicioProductoCaracteristica($indiceClienteViejoSpc, "Activo");
                            $arrayFinal[] = array('status' => "ERROR", 
                                                  'mensaje' => 'No se pudo Activar el Puerto Nuevo, <br>'
                                                               . 'Favor verificar todos los datos!<br>' . $respuestaArrayActivar[0]['mensaje']);
                            return $arrayFinal;
                        }

                        $indiceCliente = $respuestaArrayActivar[0]['mensaje'];

                        $this->servicioGeneral->ingresarServicioProductoCaracteristica($servicio, $producto, "INDICE CLIENTE", $indiceCliente, $usrCreacion);

                        $arraySpid = array( 'modeloElemento'    => $modeloElemento,
                                            'interfaceElemento' => $interfaceElemento,
                                            'ontId'             => $indiceCliente,
                                            'servicioTecnico'   => $servicioTecnico);

                        $resultArraySpid = $this->cambiarPuertoService->getSpidHuawei($arraySpid);
                        $spidStatus      = $resultArraySpid[0]['status'];

                        if($spidStatus != 'OK')
                        {
                            $arrayFinal[] = array(  'status' => "ERROR",
                                                    'mensaje' => 'No se pudo consultar el spid, <br>'
                                                                 . $resultArraySpid['mensaje']);
                            return $arrayFinal;
                        }
                        else
                        {
                            $spidViejoSpc = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "SPID", $producto);
                            $this->servicioGeneral->setEstadoServicioProductoCaracteristica($spidViejoSpc, "Eliminado");

                            $this->servicioGeneral->ingresarServicioProductoCaracteristica($servicio, $producto, "SPID", $resultArraySpid[0]['mensaje'],
                                $usrCreacion);
                            /* se elimina service profile anterior y se crea nuevo service profile con el modelo de elemento ingresado
                               para el cambio de equipo */
                            $srvProfileProdCaract = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "SERVICE-PROFILE", $producto);
                            if($srvProfileProdCaract)
                            {
                                $this->servicioGeneral->setEstadoServicioProductoCaracteristica($srvProfileProdCaract, "Eliminado");
                            }
                            $this->servicioGeneral->ingresarServicioProductoCaracteristica( $servicio, 
                                                                                            $producto, 
                                                                                            "SERVICE-PROFILE", 
                                                                                            $modeloCpe, 
                                                                                            $usrCreacion );

                        }
                    }

                    //buscar elemento cpe
                    $cpeNafArray = $this->servicioGeneral->buscarElementoEnNaf($serieCpe, $modeloCpe , "PI", "ActivarServicio");
                    $cpeNaf = $cpeNafArray[0]['status'];
                    $codigoArticuloCpe = $cpeNafArray[0]['mensaje'];
                    if($cpeNaf == "OK")
                    {
                        //actualizamos registro en el naf del cpe
                        $arrayParametrosNaf = array(   'tipoArticulo'          => 'AF',
                                                       'identificacionCliente' => '',
                                                       'empresaCod'            => '',
                                                       'modeloCpe'             => $modeloCpe,
                                                       'serieCpe'              => $serieCpe,
                                                       'cantidad'              => '1');

                        $mensajeError = $this->procesaInstalacionElemento($arrayParametrosNaf);

                        if(strlen(trim($mensajeError)) > 0)
                        {
                            $respuestaFinal[] = array("status" => "NAF", "mensaje" => "ERROR WIFI NAF: " . $mensajeError);
                            return $respuestaFinal;
                        }
                    }
                    else
                    {
                        $respuestaFinal[] = array('status' => 'NAF', 'mensaje' => $codigoArticuloCpe);
                        return $respuestaFinal;
                    }
                }
            }
            
            $intIdInterfaceElementoSplitter     = $servicioTecnico->getInterfaceElementoConectorId();
            $intIdInterfaceElementoCpeOnt       = $servicioTecnico->getInterfaceElementoClienteId();
            $intIdElementoCpeOnt                = $servicioTecnico->getElementoClienteId();
            
            //validaciones para agregar o cambiar el wifi adicional entregado a clientes con solicitudes de migracion finalizadas
            if ($strTieneMigracionHw == 'SI')
            {
                if ($strEquipoWifiAdicional == 'CAMBIAR EQUIPO' || $strAgregarWifi == 'SI')
                {
                    //buscar elemento cpe
                    $arrayWifiNaf      = $this->servicioGeneral
                                              ->buscarElementoEnNaf($strSerieWifi, 
                                                                    $strModeloWifi , 
                                                                    "PI", 
                                                                    "ActivarServicio");
                    $strWifiNaf            = $arrayWifiNaf[0]['status'];
                    $strCodigoArticuloWifi = $arrayWifiNaf[0]['mensaje'];
                    if($strWifiNaf == "OK")
                    {
                        if ($strEquipoWifiAdicional == 'CAMBIAR EQUIPO')
                        {
                            $objServicioProdCaractMacAnterior   = $objServicioProdCaractWifi;
                            $intIdInterfaceElementoAnterior     = 0;
                            $intIdElementoAnterior              = $intIdElementoWifi;
                            
                            $objInterfaceElementoAnterior       = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                                          ->findOneBy(array("elementoId"    => $intIdElementoWifi,
                                                                                                            "estado"        => "connected"));
                            if(is_object($objInterfaceElementoAnterior))
                            {
                                $intIdInterfaceElementoAnterior = $objInterfaceElementoAnterior->getId();
                            }
                        }
                        else
                        {
                            $objServicioProdCaractMacAnterior   = null;
                            $intIdInterfaceElementoAnterior     = 0;
                            $intIdElementoAnterior              = 0;
                        }
                        
                        $arrayParametrosWifiAdicional   = array(
                                                                "objPunto"                          => $servicio->getPuntoId(),
                                                                "intIdServicio"                     => $servicio->getId(),
                                                                "intIdPersona"                      => $intIdPersona,
                                                                "objServicioProdCaractMacAnterior"  => $objServicioProdCaractMacAnterior,
                                                                "intIdProductoCaracteristicaMac"    => $objProductoCaracteristicaWifi->getId(),
                                                                "intIdInterfaceElementoIzq"         => $intIdInterfaceElementoCpeOnt,
                                                                "intIdInterfaceElementoAnterior"    => $intIdInterfaceElementoAnterior,
                                                                "intIdElementoAnterior"             => $intIdElementoAnterior,
                                                                "strSerieElementoNuevo"             => $strSerieWifi,
                                                                "strMacElementoNuevo"               => $strMacWifi,
                                                                "strModeloElementoNuevo"            => $strModeloWifi,
                                                                "strTipoElementoNuevo"              => "-wifi-adicional",
                                                                "strCodEmpresa"                     => $empresa,
                                                                "strUsrCreacion"                    => $usrCreacion,
                                                                "strIpCreacion"                     => $ipCreacion,
                                                                "strAgregarWifi"                    => $strAgregarWifi,
                                                                "strTipoMedioEnlaceNuevo"           => "Fibra Optica"
                                                               );
                        
                        
                        $arrayCambioWifiAdicional       = $this->cambioElementoCpeOntWifiEnlacesInterfacesMd($arrayParametrosWifiAdicional);
                        $strStatusCambioWifiAdicional   = $arrayCambioWifiAdicional['strStatus'];
                        $strMensajeCambioWifiAdicional  = $arrayCambioWifiAdicional['strMensaje'];

                        if($strStatusCambioWifiAdicional === "ERROR")
                        {
                            throw new \Exception($strMensajeCambioWifiAdicional);
                        }
                        //actualizamos registro en el naf del cpe
                        $arrayParametrosNaf = array(   'tipoArticulo'          => 'AF',
                                                       'identificacionCliente' => '',
                                                       'empresaCod'            => '',
                                                       'modeloCpe'             => $strModeloWifi,
                                                       'serieCpe'              => $strSerieWifi,
                                                       'cantidad'              => '1');

                        $mensajeError = $this->procesaInstalacionElemento($arrayParametrosNaf);

                        if(strlen(trim($mensajeError)) > 0)
                        {
                            $respuestaFinal[] = array(  "status"                    => "NAF", 
                                                        "mensaje"                   => "ERROR WIFI NAF: " . $mensajeError,
                                                        "arrayDataConfirmacionTn"   => $arrayDataConfirmacionTn);
                            return $respuestaFinal;
                        }
                    }
                    else
                    {
                        $respuestaFinal[] = array(  'status' => 'NAF', 
                                                    'mensaje' => $strCodigoArticuloWifi,
                                                    "arrayDataConfirmacionTn"   => $arrayDataConfirmacionTn);
                        return $respuestaFinal;
                    }
                }
            }
            
            //buscar solicitud en servicio de wifi dual band
            
            $intIdServicioSol = $servicio->getId();
            
            if ($strEsWifiDualBand == "SI")
            {
                $intIdServicioSol = $intIdServicioProdWifiDB;
            }
            
            //finalizar la solicitud de cambio de Cpe
            $tipoSolicitudCambio = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                     ->findOneBy(array("descripcionSolicitud" => "SOLICITUD CAMBIO EQUIPO", "estado" => "Activo"));
            $solicitudCambioCpe = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                    ->findOneBy(array("servicioId" => $intIdServicioSol, 
                                                                      "tipoSolicitudId" => $tipoSolicitudCambio, 
                                                                      "estado" => "AsignadoTarea"));
            if($solicitudCambioCpe)
            {
                $strEstadoDetalleSol    = "AsignadoTarea";
                $solicitudCambioCpe->SetEstado("Finalizada");
                $this->emComercial->persist($solicitudCambioCpe);
                $this->emComercial->flush();
            }
            else
            {
                $strEstadoDetalleSol    = "Finalizada";
                $tipoSolicitudCambio = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                         ->findOneBy(array("descripcionSolicitud" => $strTipoSolicitud, 
                                                                           "estado" => "Activo"));
                $solicitudCambioCpe = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                        ->findOneBy(array("servicioId" => $intIdServicioSol, 
                                                                          "tipoSolicitudId" => $tipoSolicitudCambio, 
                                                                          "estado" => $strEstadoSolicitud));
                if($strEsCambioOntPorSolAgregarEquipo === "SI")
                {
                    $arrayDetsSolCaracts    = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                ->findBy(array("detalleSolicitudId" => $solicitudCambioCpe->getId(), 
                                                                               "estado"             => $strEstadoSolicitud));
                    if(isset($arrayDetsSolCaracts) && !empty($arrayDetsSolCaracts))
                    {
                        foreach($arrayDetsSolCaracts as $objDetSolCaract)
                        {
                            $objDetSolCaract->setEstado("Finalizada");
                            $objDetSolCaract->setUsrUltMod($usrCreacion);
                            $objDetSolCaract->setFeUltMod(new \DateTime('now'));
                            $this->emComercial->persist($objDetSolCaract);
                            $this->emComercial->flush();
                        }
                    }
                    $solicitudCambioCpe->setEstado("Finalizada");
                    $this->emComercial->persist($solicitudCambioCpe);
                    $this->emComercial->flush();
                }
                else if($strCambioEquiposDualBand === "SI")
                {
                    $objAdmiCaracteristicaWifiDualBand  = $this->emComercial
                                                               ->getRepository("schemaBundle:AdmiCaracteristica")
                                                               ->findOneBy(array(   'descripcionCaracteristica' => 'WIFI DUAL BAND',
                                                                                    'estado'                    => 'Activo'));
                    if (is_object($objAdmiCaracteristicaWifiDualBand))
                    {
                        $objDetalleSolCaractWifiDualBand = $this->emComercial
                                                                ->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                ->findOneBy(
                                                                            array(
                                                                                  "detalleSolicitudId"=> $solicitudCambioCpe,
                                                                                  "caracteristicaId"  => $objAdmiCaracteristicaWifiDualBand,
                                                                                  "estado"            => $strEstadoSolicitud
                                                                                 )
                                                                           );
                        if(is_object($objDetalleSolCaractWifiDualBand))
                        {
                            $objDetalleSolCaractWifiDualBand->setEstado("Finalizada");
                            $objDetalleSolCaractWifiDualBand->setUsrUltMod($usrCreacion);
                            $objDetalleSolCaractWifiDualBand->setFeUltMod(new \DateTime('now'));
                            $this->emComercial->persist($objDetalleSolCaractWifiDualBand);
                            $this->emComercial->flush();
                        }
                    }
                    
                    $objAdmiCaracteristicaElementoCliente   = $this->emComercial
                                                                   ->getRepository("schemaBundle:AdmiCaracteristica")
                                                                   ->findOneBy(array(   'descripcionCaracteristica' => 'ELEMENTO CLIENTE',
                                                                                        'estado'                    => 'Activo'));
                    if (is_object($objAdmiCaracteristicaElementoCliente))
                    {
                        $objDetalleSolCaractElementoCliente = $this->emComercial
                                                                   ->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                   ->findOneBy(
                                                                                array(
                                                                                      "detalleSolicitudId"=> $solicitudCambioCpe,
                                                                                      "caracteristicaId"  => $objAdmiCaracteristicaElementoCliente,
                                                                                      "estado"            => $strEstadoSolicitud
                                                                                     )
                                                                               );
                        if(is_object($objDetalleSolCaractElementoCliente))
                        {
                            $objDetalleSolCaractElementoCliente->setEstado("Finalizada");
                            $objDetalleSolCaractElementoCliente->setUsrUltMod($usrCreacion);
                            $objDetalleSolCaractElementoCliente->setFeUltMod(new \DateTime('now'));
                            $this->emComercial->persist($objDetalleSolCaractElementoCliente);
                            $this->emComercial->flush();
                        }
                    }
                    $arrayCaracteristicasSolicitud = $this->emComercial
                                                          ->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                          ->findBy(array("detalleSolicitudId" => $solicitudCambioCpe->getId(), 
                                                                         "estado"             => $strEstadoSolicitud));
                    if(empty($arrayCaracteristicasSolicitud))
                    {
                        if(isset($intIdServicioProdWifiDB) && !empty($intIdServicioProdWifiDB))
                        {
                            $objServicioProdEquipoWdb   = $this->emComercial->getRepository("schemaBundle:InfoServicio")
                                                                            ->find($intIdServicioProdWifiDB);
                            if(is_object($objServicioProdEquipoWdb) && is_object($objServicioProdEquipoWdb->getProductoId())
                                && $objServicioProdEquipoWdb->getProductoId()->getNombreTecnico() === "WDB_Y_EDB")
                            {
                                $strServicioProdWyAp    = "SI";
                                $objServicioProdWyAp    = $objServicioProdEquipoWdb;
                            }
                        }
                        
                        if($strServicioProdWyAp === "NO")
                        {
                            $solicitudCambioCpe->setEstado("Finalizada");
                            $this->emComercial->persist($solicitudCambioCpe);
                            $this->emComercial->flush();
                        }
                    }
                }
                else if($strEsCambioPorSoporte === "SI")
                {
                    //eliminar las caracteristicas de la solicitud en estado Asignada
                    $arrayCaracteristicasSolicitud = $this->emComercial
                                                          ->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                          ->findBy(array("detalleSolicitudId" => $solicitudCambioCpe->getId(), 
                                                                         "estado"             => $strEstadoSolicitud));
                    foreach($arrayCaracteristicasSolicitud as $objCaracteristicaSolicitud)
                    {
                        $objCaracteristicaSolicitud->setEstado("Finalizada");
                        $objCaracteristicaSolicitud->setUsrUltMod($usrCreacion);
                        $objCaracteristicaSolicitud->setFeUltMod(new \DateTime('now'));
                        $this->emComercial->persist($objCaracteristicaSolicitud);
                        $this->emComercial->flush();
                    }
                    
                    $solicitudCambioCpe->setEstado("Finalizada");
                    $this->emComercial->persist($solicitudCambioCpe);
                    $this->emComercial->flush();
                }
                else
                {
                    //eliminar las caracteristicas de la solicitud en estado AsignadoTarea
                    $arrayCaracteristicasSolicitud = $this->emComercial
                                                          ->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                          ->findBy(array("detalleSolicitudId" => $solicitudCambioCpe->getId(), 
                                                                         "estado"             => "AsignadoTarea"));
                    foreach($arrayCaracteristicasSolicitud as $objCaracteristicaSolicitud)
                    {
                        $objCaracteristicaSolicitud->setEstado("Finalizada");
                        $objCaracteristicaSolicitud->setUsrCreacion($usrCreacion);
                        $objCaracteristicaSolicitud->setFeCreacion(new \DateTime('now'));
                        $this->emComercial->persist($objCaracteristicaSolicitud);
                        $this->emComercial->flush();
                    }

                    $caractSolicitud = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                        ->findBy(array("detalleSolicitudId" => $solicitudCambioCpe->getId(), "estado" => "Activo"));
                    if(count($caractSolicitud) > 0)
                    {
                        //no se le hace nada a la solicitud, solo cuando termine de hacer los cambios se finaliza
                    }
                    else
                    {
                        $solicitudCambioCpe->setEstado("Finalizada");
                        $this->emComercial->persist($solicitudCambioCpe);
                        $this->emComercial->flush();
                    }
                }
            }
            /* en caso de no tener solicitud de migración ó tener solicitud de migracion finalizada y solicitar cambio de CPE hw
               ó cambio de equipo wifi adicional se procede a generar solicitud de retiro de equipo*/
            if( ($solicitudCambioCpe->getEstado() == "Finalizada" || 
                 $strEsCambioOntPorSolAgregarEquipo === "SI" ||
                 $strCambioEquiposDualBand === "SI"               ||
                 $strEsCambioPorSoporte    === "SI") && 
                (  $strTieneMigracionHw != 'SI' ||
                   ($strTieneMigracionHw == 'SI' && $strEquipoCpeHw == 'CAMBIAR EQUIPO') ||
                   ($strTieneMigracionHw == 'SI' && $strEquipoWifiAdicional == 'CAMBIAR EQUIPO') ||
                   $strCambioEquiposDualBand === "SI"
                )
              )
            {
                //crear solicitud para retiro de equipo (cpe)
                $tipoSolicitud = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                   ->findOneBy(array("descripcionSolicitud" => "SOLICITUD RETIRO EQUIPO", 
                                                                     "estado" => "Activo"));

                $detalleSolicitud = new InfoDetalleSolicitud();
                $detalleSolicitud->setServicioId($servicio);
                $detalleSolicitud->setTipoSolicitudId($tipoSolicitud);
                $detalleSolicitud->setEstado("AsignadoTarea");
                $detalleSolicitud->setUsrCreacion($usrCreacion);
                $detalleSolicitud->setFeCreacion(new \DateTime('now'));
                $detalleSolicitud->setObservacion("SOLICITA RETIRO DE EQUIPO POR CAMBIO DE MODEM");
                $this->emComercial->persist($detalleSolicitud);
                $this->emComercial->flush();

                //crear las caract para la solicitud de retiro de equipo
                $objCaracteristica = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                       ->findOneBy(array('descripcionCaracteristica'  => 'ELEMENTO CLIENTE',
                                                                         'estado'                     => 'Activo'));
                //se recuperan caracteristicas de equipos cpe de la solicitud y se agregan al retiro de equipo
                if ($strTieneMigracionHw != 'SI' || 
                    ($strTieneMigracionHw == 'SI' && $strEquipoCpeHw == 'CAMBIAR EQUIPO') ||
                    $strCambioEquiposDualBand === "SI" ||
                    $strEsCambioOntPorSolAgregarEquipo === "SI"
                   )
                {
                    if ($strCambioEquiposDualBand === "SI" && $strEsWifiDualBand === "SI")
                    {
                        $entityCaract = new InfoDetalleSolCaract();
                        $entityCaract->setCaracteristicaId($objCaracteristica);
                        $entityCaract->setDetalleSolicitudId($detalleSolicitud);
                        $entityCaract->setValor($servicioTecnico->getElementoClienteId());
                        $entityCaract->setEstado("AsignadoTarea");
                        $entityCaract->setUsrCreacion($usrCreacion);
                        $entityCaract->setFeCreacion(new \DateTime('now'));
                        $this->emComercial->persist($entityCaract);
                        $this->emComercial->flush();
                    }
                    else
                    {
                        $caractSolicitudCambioElemento = $this->emComercial
                                                              ->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                              ->findBy(array("detalleSolicitudId" => $solicitudCambioCpe->getId(),
                                                                             "caracteristicaId"   => $objCaracteristica,
                                                                             "estado"             => $strEstadoDetalleSol));

                        for($i = 0; $i < count($caractSolicitudCambioElemento); $i++)
                        {
                            $entityCaract = new InfoDetalleSolCaract();
                            $entityCaract->setCaracteristicaId($objCaracteristica);
                            $entityCaract->setDetalleSolicitudId($detalleSolicitud);
                            $entityCaract->setValor($caractSolicitudCambioElemento[$i]->getValor());
                            $entityCaract->setEstado("AsignadoTarea");
                            $entityCaract->setUsrCreacion($usrCreacion);
                            $entityCaract->setFeCreacion(new \DateTime('now'));
                            $this->emComercial->persist($entityCaract);
                            $this->emComercial->flush();
                        }
                    }
                }
                //se recuperan caracteristicas de equipos wifi adicional de la solicitud y se agregan al retiro de equipo
                if ($strTieneMigracionHw == 'SI' && $strEquipoWifiAdicional == 'CAMBIAR EQUIPO')
                {
                    //crear las caract para la solicitud de retiro de equipo
                    $objCaracteristicaElementoWifi = $this->emComercial
                                                          ->getRepository('schemaBundle:AdmiCaracteristica')
                                                          ->findOneBy(array('descripcionCaracteristica'  =>'ELEMENTO WIFI',
                                                                            'estado'                     => 'Activo'));
                    if (is_object($objCaracteristicaElementoWifi))
                    {
                        $objCaracteristicasWifi = $this->emComercial
                                                       ->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                       ->findBy(array("detalleSolicitudId" => $solicitudCambioCpe->getId(),
                                                                      "caracteristicaId"   => $objCaracteristicaElementoWifi,
                                                                      "estado"             => $strEstadoDetalleSol));
                    }
                    foreach($objCaracteristicasWifi as $objCaracteristicaWifi)
                    {
                        $entityCaract = new InfoDetalleSolCaract();
                        $entityCaract->setCaracteristicaId($objCaracteristica);
                        $entityCaract->setDetalleSolicitudId($detalleSolicitud);
                        $entityCaract->setValor($objCaracteristicaWifi->getValor());
                        $entityCaract->setEstado("AsignadoTarea");
                        $entityCaract->setUsrCreacion($usrCreacion);
                        $entityCaract->setFeCreacion(new \DateTime('now'));
                        $this->emComercial->persist($entityCaract);
                        $this->emComercial->flush();
                    }
                }
                //buscar el info_detalle de la solicitud
                $detalleCambioCpe = $this->emComercial->getRepository('schemaBundle:InfoDetalle')
                                                      ->findOneBy(array("detalleSolicitudId" => $solicitudCambioCpe->getId()));

                //obtener tarea
                $entityProceso = $this->emSoporte->getRepository('schemaBundle:AdmiProceso')->findOneByNombreProceso("SOLICITAR RETIRO EQUIPO");
                $entityTareas = $this->emSoporte->getRepository('schemaBundle:AdmiTarea')->findTareasActivasByProceso($entityProceso->getId());
                $entityTarea = $entityTareas[0];

                //grabar nuevo info_detalle para la solicitud de retiro de equipo
                $entityDetalle = new InfoDetalle();
                $entityDetalle->setDetalleSolicitudId($detalleSolicitud->getId());
                $entityDetalle->setTareaId($entityTarea);
                $entityDetalle->setLongitud($servicio->getPuntoId()->getLongitud());
                $entityDetalle->setLatitud($servicio->getPuntoId()->getLatitud());
                $entityDetalle->setPesoPresupuestado(0);
                $entityDetalle->setValorPresupuestado(0);
                $entityDetalle->setIpCreacion($ipCreacion);
                $entityDetalle->setFeCreacion(new \DateTime('now'));
                $entityDetalle->setUsrCreacion($usrCreacion);
                $this->emSoporte->persist($entityDetalle);
                $this->emSoporte->flush();

                //buscar la info_detalle_asignacion de la solicitud
                if($detalleCambioCpe)
                {
                    $detalleAsignacionCambioCpe = $this->emComercial->getRepository('schemaBundle:InfoDetalleAsignacion')
                                                                    ->findOneBy(array("detalleId" => $detalleCambioCpe->getId()));

                    //asignar los mismos responsables a la solicitud de retiro de equipo
                    $entityDetalleAsignacion = new InfoDetalleAsignacion();
                    $entityDetalleAsignacion->setDetalleId($entityDetalle);
                    $entityDetalleAsignacion->setAsignadoId($detalleAsignacionCambioCpe->getAsignadoId());
                    $entityDetalleAsignacion->setAsignadoNombre($detalleAsignacionCambioCpe->getAsignadoNombre());
                    $entityDetalleAsignacion->setRefAsignadoId($detalleAsignacionCambioCpe->getRefAsignadoId());
                    $entityDetalleAsignacion->setRefAsignadoNombre($detalleAsignacionCambioCpe->getRefAsignadoNombre());
                    $entityDetalleAsignacion->setPersonaEmpresaRolId($detalleAsignacionCambioCpe->getPersonaEmpresaRolId());
                    $entityDetalleAsignacion->setTipoAsignado("EMPLEADO");
                    $entityDetalleAsignacion->setIpCreacion($ipCreacion);
                    $entityDetalleAsignacion->setFeCreacion(new \DateTime('now'));
                    $entityDetalleAsignacion->setUsrCreacion($usrCreacion);
                    $this->emSoporte->persist($entityDetalleAsignacion);
                    $this->emSoporte->flush();

                    $objPersonaEmpresaRolUsr = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                 ->find($detalleAsignacionCambioCpe->getPersonaEmpresaRolId());
                    if(is_object($objPersonaEmpresaRolUsr))
                    {
                        $intIdPersona = $objPersonaEmpresaRolUsr->getPersonaId()->getId();
                    }
                }

                //crear historial para la solicitud de retiro de equipo
                $historialSolicitud = new InfoDetalleSolHist();
                $historialSolicitud->setDetalleSolicitudId($detalleSolicitud);
                $historialSolicitud->setEstado("AsignadoTarea");
                $historialSolicitud->setObservacion("GENERACION AUTOMATICA DE SOLICITUD RETIRO DE EQUIPO POR CAMBIO DE MODEM");
                $historialSolicitud->setUsrCreacion($usrCreacion);
                $historialSolicitud->setFeCreacion(new \DateTime('now'));
                $historialSolicitud->setIpCreacion($ipCreacion);
                $this->emComercial->persist($historialSolicitud);
                $this->emComercial->flush();
            }
            
            if($solicitudCambioCpe->getEstado() == "Finalizada")
            {
                //crear historial para la solicitud de cambio de equipo
                $objHistorialSolicitudCpe = new InfoDetalleSolHist();
                $objHistorialSolicitudCpe->setDetalleSolicitudId($solicitudCambioCpe);
                $objHistorialSolicitudCpe->setEstado($solicitudCambioCpe->getEstado());
                $objHistorialSolicitudCpe->setObservacion("Finalizacion de cambio de CPE");
                $objHistorialSolicitudCpe->setUsrCreacion($usrCreacion);
                $objHistorialSolicitudCpe->setFeCreacion(new \DateTime('now'));
                $objHistorialSolicitudCpe->setIpCreacion($ipCreacion);
                $this->emComercial->persist($objHistorialSolicitudCpe);
                $this->emComercial->flush();
            }
            /* en caso de no tener solicitud de migración ó tener solicitud de migracion finalizada y solicitar cambio de CPE hw
               se procede a actualizar la información del equipo */
            if ($strTieneMigracionHw != 'SI' || 
                ($strTieneMigracionHw == 'SI' && $strEquipoCpeHw == 'CAMBIAR EQUIPO') ||
                $strCambioEquiposDualBand == "SI" || $strEsCambioOntPorSolAgregarEquipo === "SI")
            {
                if(isset($strEsIsb) && !empty($strEsIsb) && $strEsIsb === "SI")
                {
                    $empresa = 18;
                }
                $arrayParametrosCambioCpeOnt    = array(
                                                        "objPunto"                          => $servicio->getPuntoId(),
                                                        "intIdServicio"                     => $servicio->getId(),
                                                        "intIdPersona"                      => $intIdPersona,
                                                        "objServicioProdCaractMacAnterior"  => $servicioProdCaractMac,
                                                        "intIdProductoCaracteristicaMac"    => $productoCaracteristicaMac->getId(),
                                                        "intIdInterfaceElementoIzq"         => $intIdInterfaceElementoSplitter,
                                                        "intIdInterfaceElementoAnterior"    => $intIdInterfaceElementoCpeOnt,
                                                        "intIdElementoAnterior"             => $intIdElementoCpeOnt,
                                                        "strSerieElementoNuevo"             => $serieCpe,
                                                        "strMacElementoNuevo"               => $macCpe,
                                                        "strModeloElementoNuevo"            => $modeloCpe,
                                                        "strTipoElementoNuevo"              => "-ont",
                                                        "strCodEmpresa"                     => $empresa,
                                                        "strUsrCreacion"                    => $usrCreacion,
                                                        "strIpCreacion"                     => $ipCreacion,
                                                        "strExisteIpWan"                    => $strExisteIpWan,
                                                        "intIdServicioAdicIpWan"            => $intIdServicioAdicIpWan,
                                                        "objSpcMacIpWan"                    => $objSpcMacIpWan,
                                                        "objProductoInternet"               => $producto
                                                  );
                $arrayCambioCpeOnt              = $this->cambioElementoCpeOntWifiEnlacesInterfacesMd($arrayParametrosCambioCpeOnt);
                $strStatusCambioCpeOnt          = $arrayCambioCpeOnt['strStatus'];
                $strMensajeCambioCpeOnt         = $arrayCambioCpeOnt['strMensaje'];

                if($strStatusCambioCpeOnt === "OK")
                {
                    $objElementoNuevoCpeOnt                     = $arrayCambioCpeOnt["objElementoNuevo"];
                    $objInterfaceElementoNuevoCpeOntConnected   = $arrayCambioCpeOnt["objInterfaceElementoNuevoConnected"];
                    
                    if(!is_object($objElementoNuevoCpeOnt) || !is_object($objInterfaceElementoNuevoCpeOntConnected))
                    {
                        throw new \Exception("No se ha podido obtener correctamente el elemento y la interface cliente nueva");
                    }

                    $servicioTecnico->setInterfaceElementoClienteId($objInterfaceElementoNuevoCpeOntConnected->getId());
                    $servicioTecnico->setElementoClienteId($objElementoNuevoCpeOnt->getId());
                    $this->emComercial->persist($servicioTecnico);
                    $this->emComercial->flush();
                    if($strCambioEquiposDualBand == "SI" && $strServicioProdWyAp === "SI" && is_object($objServicioProdWyAp))
                    {
                        $objServicioProdWyAp->setEstado("PendienteAp");
                        $this->emComercial->persist($objServicioProdWyAp);
                        $this->emComercial->flush();

                        $objCaractExtenderDualBand  = $this->emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                                                        ->findOneBy(array('descripcionCaracteristica' => 'EXTENDER DUAL BAND',
                                                                                          'estado'                    => 'Activo'));
                        if (!is_object($objCaractExtenderDualBand))
                        {
                            throw new \Exception("No se encontró información acerca de característica EXTENDER DUAL BAND");
                        }

                        $objDetalleSolCaractEquipoDualBand = new InfoDetalleSolCaract();
                        $objDetalleSolCaractEquipoDualBand->setCaracteristicaId($objCaractExtenderDualBand);
                        $objDetalleSolCaractEquipoDualBand->setDetalleSolicitudId($solicitudCambioCpe);
                        $objDetalleSolCaractEquipoDualBand->setValor("SI");
                        $objDetalleSolCaractEquipoDualBand->setEstado($strEstadoSolicitud);
                        $objDetalleSolCaractEquipoDualBand->setUsrCreacion($usrCreacion);
                        $objDetalleSolCaractEquipoDualBand->setFeCreacion(new \DateTime('now'));
                        $this->emComercial->persist($objDetalleSolCaractEquipoDualBand);
                        $this->emComercial->flush();
                    }
                    if($strCambioEquiposDualBand == "SI" && $strEsWifiDualBand == "SI" && $strServicioProdWyAp === "NO")
                    {
                        $objServicioProdWifiDB = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicioProdWifiDB);
                        $objAccion             = $this->emSeguridad->getRepository('schemaBundle:SistAccion')->find(847);

                        if (is_object($objServicioProdWifiDB) && is_object($objAccion))
                        {
                            $objServicioTecProdWifiDB = $this->emComercial
                                                             ->getRepository('schemaBundle:InfoServicioTecnico')
                                                             ->findOneBy(array( "servicioId" => $intIdServicioProdWifiDB));
                            if (is_object($objServicioTecProdWifiDB))
                            {
                                $objServicioTecProdWifiDB->setInterfaceElementoClienteId($objInterfaceElementoNuevoCpeOntConnected->getId());
                                $objServicioTecProdWifiDB->setElementoClienteId($objElementoNuevoCpeOnt->getId());
                                $this->emComercial->persist($objServicioTecProdWifiDB);
                                $this->emComercial->flush();
                            }

                            $strNombreAccion            = $objAccion->getNombreAccion();
                            $strObservacionHistorial    = "Se confirmo el servicio";
                            $objServicioProdWifiDB->setEstado("Activo");
                            $this->emComercial->persist($objServicioProdWifiDB);
                            $this->emComercial->flush();

                            //historial del servicio
                            $objServicioHistorial = new InfoServicioHistorial();
                            $objServicioHistorial->setServicioId($objServicioProdWifiDB);
                            $objServicioHistorial->setObservacion($strObservacionHistorial);
                            $objServicioHistorial->setEstado($objServicioProdWifiDB->getEstado());
                            $objServicioHistorial->setUsrCreacion($usrCreacion);
                            $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                            $objServicioHistorial->setIpCreacion($ipCreacion);
                            $objServicioHistorial->setAccion($strNombreAccion);
                            $this->emComercial->persist($objServicioHistorial);
                            $this->emComercial->flush();

                            $arrayParametros = array();
                            $arrayParametros['intIdDetalleSolicitud'] = $solicitudCambioCpe->getId();
                            $arrayParametros['strProceso']            = 'Activar';

                            $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleSolicitud')->cerrarTareasPorSolicitud($arrayParametros);    

                            $arrayParametrosMail =   array(
                                           "servicio"                      => $objServicioProdWifiDB,
                                           "prefijoEmpresa"                => $prefijoEmpresa,
                                           "empleadoSesion"                => $objEmpleadoSesion,
                                           "observacionActivarServicio"    => $strObservacionHistorial,
                                           "idEmpresa"                     => $empresa,
                                           "user"                          => $usrCreacion,
                                           "ipClient"                      => $ipCreacion
                                          );

                            $this->serviceConfirmar->envioMailConfirmarServicio($arrayParametrosMail);
                        }
                    }

                    $arrayParametroOrigenTelcos = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                  ->getOne( 'PARAMETROS_GENERALES_MOVIL', 
                                                                            '', 
                                                                            '', 
                                                                            '', 
                                                                            'ORIGEN_TELCOS', 
                                                                            '', 
                                                                            '', 
                                                                            ''
                                                                           );
                    if(is_array($arrayParametroOrigenTelcos))
                    {
                        $strParametroOrigenTelcos = $arrayParametroOrigenTelcos['valor2'] ? $arrayParametroOrigenTelcos['valor2'] : "TELCOS";
                    }

                    if($strEsCambioOntPorSolAgregarEquipo === "SI" && $strOrigen == $strParametroOrigenTelcos)
                    {
                        $arrayParamsSolCambioOntPorSolAgregarEquipo = array(   
                                                                            "intIdDetalleSolicitud"   => $solicitudCambioCpe->getId(),
                                                                            "strProceso"              => 
                                                                            "SEGUIMIENTO_GENERAL:Solicitud asociada a esta tarea fue finalizada "
                                                                            ."por cambio de ont");
                        $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                ->cerrarTareasPorSolicitud($arrayParamsSolCambioOntPorSolAgregarEquipo);
                    }
                }
                else
                {
                    throw new \Exception($strMensajeCambioCpeOnt);
                }
                
            }
            
            //historial del servicio
            $servicioHistorial = new InfoServicioHistorial();
            $servicioHistorial->setServicioId($servicio);
            $servicioHistorial->setObservacion("Se Realizo un Cambio de Elemento Cliente " . $tipoElementoCpe);
            $servicioHistorial->setEstado($servicio->getEstado());
            $servicioHistorial->setUsrCreacion($usrCreacion);
            $servicioHistorial->setFeCreacion(new \DateTime('now'));
            $servicioHistorial->setIpCreacion($ipCreacion);
            $this->emComercial->persist($servicioHistorial);
            $this->emComercial->flush();
            
            if ($strEsCambioPorSoporte === "SI")
            {
                $strTieneEquipoDualBand  = "NO";
                $arrayParamModelosNuevos = $this->emGeneral
                                                ->getRepository('schemaBundle:AdmiParametroDet')
                                                ->get('EQUIPOS_PERMITIDOS_CAMBIO_EQUIPO_POR_SOPORTE',
                                                      'TECNICO',
                                                      'VALIDACION_DE_EQUIPOS',
                                                      '',
                                                      '',
                                                      'DUAL BAND',
                                                      '',
                                                      '',
                                                      '',
                                                      '18');
                foreach($arrayParamModelosNuevos as $arrayParamModeloNuevo)
                {
                    if ($modeloCpe ==  $arrayParamModeloNuevo["valor1"])
                    {
                        $strTieneEquipoDualBand = "SI";
                    }
                }
                
                if ($strTieneEquipoDualBand == "SI")
                {
                    $arrayRespuestaCreaProdWifiDualBand = $this->servicioGeneral
                                                               ->creaServicioEquipoDualBand(array(  
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
                                                                                                    $empresa,
                                                                                                    "nombreTecnico"             => 
                                                                                                    "WIFI_DUAL_BAND",
                                                                                                    "strProceso"                => 
                                                                                                    "CambioPorSoporte"));
                    $objServHistServicio    = new InfoServicioHistorial();
                    $objServHistServicio->setServicioId($servicio);
                    $objServHistServicio->setObservacion($arrayRespuestaCreaProdWifiDualBand["mensaje"]);
                    $objServHistServicio->setEstado($servicio->getEstado());
                    $objServHistServicio->setUsrCreacion($usrCreacion);
                    $objServHistServicio->setFeCreacion(new \DateTime('now'));
                    $objServHistServicio->setIpCreacion($ipCreacion);
                    $this->emComercial->persist($objServHistServicio);
                    $this->emComercial->flush();
                }
            }
            $strStatusCambioElementoHw  = "OK";
            $strMsjCambioElementoHw     = "OK";
            $arrayDataConfirmacionTn['datos']['respuesta_confirmacion'] = "OK";
        }
        catch(\Exception $e)
        {
            if($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->rollback();
            }

            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }
            $strStatusCambioElementoHw  = "ERROR";
            $strMsjCambioElementoHw     = "ERROR, " . $e->getMessage();
            $this->serviceUtil->insertLog(array(
                        'enterpriseCode'      => 18,
                        'logType'             => 1,
                        'logOrigin'           => 'TELCOS',
                        'application'         => 'TELCOS',
                        'appClass'            => basename(__CLASS__),
                        'appMethod'           => basename(__FUNCTION__),
                        'descriptionError'    => $strMsjCambioElementoHw,
                        'status'              => 'ErrorCambioEquipo',
                        'messageUser'         =>  $this->opcion,
                        'appAction'           => 'CAMBIAR_EQUIPO_'.$servicio->getPuntoId()->getLogin(),
                        'inParameters'        => json_encode($arrayDataConfirmacionTn),
                        'creationUser'        => $usrCreacion));
        }
        
        $arrayFinal[] = array('status'                          => $strStatusCambioElementoHw,
                              'mensaje'                         => $strMsjCambioElementoHw,
                              'strEquipoActualEsDB'             => $strEquipoActualEsDB,
                              'strEquipoNuevoEsDB'              => $strEquipoNuevoEsDB,
                              'strEquipoNuevoEstaParametrizado' => $strOntNuevoEstaParametrizado, // Ejemplo: ONT V5
                              'arrayDataConfirmacionTn'         => $arrayDataConfirmacionTn
                             );
        return $arrayFinal;

    }
    
    /**
     * cambioElementoSmartWifi
     * 
     * Service que realiza el cambio del elemento del cliente en equipos huawei, con ejecucion de scripts
     *
     * @author Creado: Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 01-03-2017
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 06-09-2017 -  En la tabla INFO_DETALLE_ASIGNACION se guarda el PersonaEmpresaRolId del responsable de la tarea de retiro de
     *                            equipo
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 21-12-2017 - En la tabla INFO_DETALLE_ASIGNACION se registra el campo tipo asignado 'EMPLEADO'
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.3 27-02-2018 Se registra tracking del elemento
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.4 29-05-2018 - Se agrega el parametro del id_persona en el registro de la trazabilidad
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.5 03-12-2018 - Se coloca en estado connected a la interface del Smart Wifi
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.6 21-06-2019   Se modifica service utilizado para registrar el log de errores de la operación
     * @since 1.5
     *
     * @param Array $arrayParametros [ 
     *                                 servicio               => Objeto de la tabla InfoServicio
     *                                 servicioTecnico        => Objeto de la tabla InfoServicioTecnico
     *                                 serieCpe               => Cadena de caracteres con serie de cpe 
     *                                 codigoArticulo         => Cadena de caracteres con modelo de cpe
     *                                 nombreCpe              => Cadena de caracteres con nombre de cpe
     *                                 macCpe                 => Cadena de caracteres con mac de cpe
     *                                 tipoElementoCpe        => Cadena de caracteres con tipo de elemento de cpe 
     *                                 idEmpresa              => Cadena de caracteres con identificador de empresa
     *                                 idElementoCliente      => Numero entero identificador de elemento del cliente
     *                                 usrCreacion            => Cadena de caracteres con usuario de creacion a utilizar
     *                                 ipCreacion             => Cadena de caracteres con ip de creacion a utilizar
     *                                 prefijoEmpresa         => Cadena de caracteres con prefigo de empresa a utilizar
     *                               ]
     * @return Array $arrayRespuestaFinal [
     *                                      status  =>  Cadena de caracteres que indica el estado de la transacción
     *                                      mensaje =>  Cadena de caracteres que indica el mensaje de respuesta del proceso
     *                                    ]
     */
    public function cambioElementoSmartWifi($arrayParametros) 
    {
        $objServicio                = !empty($arrayParametros['servicio'])?$arrayParametros['servicio']:null;
        $objServicioTecnico         = !empty($arrayParametros['servicioTecnico'])?$arrayParametros['servicioTecnico']:null;
        $strSerieSmartWifi          = !empty($arrayParametros['serieCpe'])?$arrayParametros['serieCpe']:"";
        $strModeloSmartWifi         = !empty($arrayParametros['codigoArticulo'])?$arrayParametros['codigoArticulo']:"";
        $strTipoElementoSmartWifi   = !empty($arrayParametros['tipoElementoCpe'])?$arrayParametros['tipoElementoCpe']:"";
        $strMacSmartWifi            = !empty($arrayParametros['macCpe'])?$arrayParametros['macCpe']:"";
        $intIdElementoCliente       = !empty($arrayParametros['idElementoCliente'])?$arrayParametros['idElementoCliente']:0;
        $strEmpresaCod              = !empty($arrayParametros['idEmpresa'])?$arrayParametros['idEmpresa']:"";
        $strUsrCreacion             = !empty($arrayParametros['usrCreacion'])?$arrayParametros['usrCreacion']:"";
        $strIpCreacion              = !empty($arrayParametros['ipCreacion'])?$arrayParametros['ipCreacion']:"";
        $strEsPlan                  = !empty($arrayParametros['strEsPlan'])?$arrayParametros['strEsPlan']:""; 
        $strUltimaMilla             = '';
        $strTipoArticulo            = "AF";
        $strIdentificacionCliente   = "";
        $arrayRespuestaFinal        = array();
        $arrayParametrosAuditoria   = array();

        try
        {
           if(!is_object($objServicio))
            {
                $arrayRespuestaFinal[] = array('status' => 'ERROR', 'mensaje' => 'No existe información del servicio, favor revisar!');
                return $arrayRespuestaFinal;
            }

            if(!is_object($objServicioTecnico))
            {
                $arrayRespuestaFinal[] = array('status' => 'ERROR', 'mensaje' => 'No existe información técnica del servicio, favor revisar!');
                return $arrayRespuestaFinal;
            }
            //obtener elemento SmartWifi
            $objElementoSmartWifi = $this->emInfraestructura
                                         ->getRepository('schemaBundle:InfoElemento')
                                         ->find($intIdElementoCliente);
            if(!is_object($objElementoSmartWifi))
            {
                $arrayRespuestaFinal[] = array('status' => 'ERROR', 'mensaje' => 'No existe el elemento SmartWifi del cliente, favor revisar!');
                return $arrayRespuestaFinal;
            }

            $objTipoMedio = $this->emInfraestructura
                                 ->getRepository('schemaBundle:AdmiTipoMedio')
                                 ->find($objServicioTecnico->getUltimaMillaId());
            if (is_object($objTipoMedio))
            {
                $strUltimaMilla = $objTipoMedio->getNombreTipoMedio(); 
            }
            
            //se eliminan elementos del servicio*******
            $objElementoSmartWifi->setEstado("Eliminado");
            $this->emInfraestructura->persist($objElementoSmartWifi);
            $this->emInfraestructura->flush();

            //SE REGISTRA EL TRACKING DEL ELEMENTO - ANTERIOR
            $arrayParametrosAuditoria["strNumeroSerie"]  = $objElementoSmartWifi->getSerieFisica();
            $arrayParametrosAuditoria["strEstadoTelcos"] = 'Eliminado';
            $arrayParametrosAuditoria["strEstadoNaf"]    = 'Instalado';
            $arrayParametrosAuditoria["strEstadoActivo"] = 'CambioEquipo';
            $arrayParametrosAuditoria["strUbicacion"]    = 'EnTransito';
            $arrayParametrosAuditoria["strCodEmpresa"]   = $strEmpresaCod;
            $arrayParametrosAuditoria["strTransaccion"]  = 'Cambio de Elemento';
            $arrayParametrosAuditoria["intOficinaId"]    = 0;

            //Se consulta el login del cliente
            if(is_object($objServicioTecnico->getServicioId()))
            {
                $objInfoPunto = $this->emInfraestructura->getRepository('schemaBundle:InfoPunto')
                                                        ->find($objServicioTecnico->getServicioId()->getPuntoId()->getId());
                if(is_object($objInfoPunto))
                {
                    $arrayParametrosAuditoria["strLogin"] = $objInfoPunto->getLogin();
                }
            }

            $arrayParametrosAuditoria["strUsrCreacion"] = $strUsrCreacion;
            ////

            //historial del elemento
            $objHistorialElemento = new InfoHistorialElemento();
            $objHistorialElemento->setElementoId($objElementoSmartWifi);
            $objHistorialElemento->setObservacion("Se elimino el elemento por Cambio de equipo");
            $objHistorialElemento->setEstadoElemento("Eliminado");
            $objHistorialElemento->setUsrCreacion($strUsrCreacion);
            $objHistorialElemento->setFeCreacion(new \DateTime('now'));
            $objHistorialElemento->setIpCreacion($strIpCreacion);
            $this->emInfraestructura->persist($objHistorialElemento);
            $this->emInfraestructura->flush();

            //eliminar puertos del elemento
            $arrayInterfacesElemento = $this->emInfraestructura
                                            ->getRepository('schemaBundle:InfoInterfaceElemento')
                                            ->findBy(array("elementoId" => $objElementoSmartWifi->getId()));

            foreach($arrayInterfacesElemento as $objInterfaceElemento)
            {
                $objInterfaceElemento->setEstado("Eliminado");
                $this->emInfraestructura->persist($objInterfaceElemento);
                $this->emInfraestructura->flush();
            }
            
            //se procede a realizar el ingreso del elemento Smart Wifi y despacharlo en el NAF
            $arrayWifiNaf = $this->servicioGeneral->buscarElementoEnNaf($strSerieSmartWifi, 
                                                                        $strModeloSmartWifi, 
                                                                        "PI", 
                                                                        "ActivarServicio");
            $strWifiNaf            = $arrayWifiNaf[0]['status'];
            $strCodigoArticuloWifi = "";
            if($strWifiNaf == "OK")
            {
                $objInterfaceElementoSmartWifi = $this->servicioGeneral
                                                      ->ingresarElementoCliente( $objServicio->getPuntoId()->getLogin(), 
                                                                                 $strSerieSmartWifi, 
                                                                                 $strModeloSmartWifi,
                                                                                 '-'.$objServicio->getId().'-RentaSmartWifi', 
                                                                                 null, 
                                                                                 $strUltimaMilla,
                                                                                 $objServicio, 
                                                                                 $strUsrCreacion, 
                                                                                 $strIpCreacion, 
                                                                                 $strEmpresaCod );
                if(is_object($objInterfaceElementoSmartWifi))
                {
                    $objInterfaceElementoSmartWifi->setEstado("connected");
                    $this->emInfraestructura->persist($objInterfaceElementoSmartWifi);
                    $this->emInfraestructura->flush();

                    $objElementoNuevoSmartWifi = $objInterfaceElementoSmartWifi->getElementoId();
                    
                    if(!is_object($objElementoNuevoSmartWifi))
                    {
                        $arrayRespuestaFinal[] = array('status'  => 'ERROR', 
                                                       'mensaje' => 'No se recuperó correctamente información del elemento SmartWifi');
                        return $arrayRespuestaFinal;
                    }
                    //historial del servicio
                    $objServicioHistorial = new InfoServicioHistorial();
                    $objServicioHistorial->setServicioId($objServicio);
                    $objServicioHistorial->setObservacion("Se registro el elemento con nombre: ".
                                                          $objElementoNuevoSmartWifi->getNombreElemento().
                                                          ", Serie: ".
                                                          $strSerieSmartWifi.
                                                          ", Modelo: ".
                                                          $strModeloSmartWifi.
                                                          ", Mac: ".
                                                          $strMacSmartWifi
                                                         );
                    $objServicioHistorial->setEstado($objServicio->getEstado());
                    $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                    $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                    $objServicioHistorial->setIpCreacion($strIpCreacion);
                    $this->emComercial->persist($objServicioHistorial);
                    $this->emComercial->flush();
                    
                    //actualizamos registro en el naf wifi
                    $strMensajeError = str_repeat(' ', 1000);                                                                  
                    $strSql          = "BEGIN AFK_PROCESOS.IN_P_PROCESA_INSTALACION(:codigoEmpresaNaf, ".
                                       ":codigoArticulo, :tipoArticulo, :identificacionCliente, :serieCpe, ".
                                       ":cantidad, :pv_mensajeerror); END;";
                    $objStmt = $this->emNaf->getConnection()->prepare($strSql);
                    $objStmt->bindParam('codigoEmpresaNaf',      $strEmpresaCod);
                    $objStmt->bindParam('codigoArticulo',        $strCodigoArticuloWifi);
                    $objStmt->bindParam('tipoArticulo',          $strTipoArticulo);
                    $objStmt->bindParam('identificacionCliente', $strIdentificacionCliente);
                    $objStmt->bindParam('serieCpe',              $strSerieSmartWifi);
                    $objStmt->bindParam('cantidad',              intval(1));
                    $objStmt->bindParam('pv_mensajeerror',       $strMensajeError);
                    $objStmt->execute();

                    if(strlen(trim($strMensajeError))<=0)
                    {
                        if($strEsPlan == "NO")
                        {
                            $objMacSmartWifi = $this->servicioGeneral
                                                    ->getServicioProductoCaracteristica($objServicio, "MAC WIFI", $objServicio->getProductoId());

                            if(is_object($objMacSmartWifi))
                            {
                                $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objMacSmartWifi, "Eliminado");
                            }
                            //servicio prod caract mac wifi
                            $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio, 
                                                                                           $objServicio->getProductoId(), 
                                                                                           "MAC WIFI", 
                                                                                           $strMacSmartWifi, 
                                                                                           $strUsrCreacion);
                        }
                        
                        //info_detalle_elemento gestion remota
                        $this->servicioGeneral->ingresarDetalleElemento($objElementoNuevoSmartWifi,
                                                                        "MAC WIFI",
                                                                        "MAC WIFI",
                                                                        $strMacSmartWifi,
                                                                        $strUsrCreacion,
                                                                        $strIpCreacion); 
                        if($strEsPlan == "SI")
                        {
                            $arrayParams['intInterfaceElementoConectorId'] = $objServicioTecnico->getInterfaceElementoClienteId();
                            $arrayParams['arrayData']                      = array();
                            $arrayParams['strBanderaReturn']               = 'INTERFACE';
                            $arrayParams['strTipoSmartWifi']               = 'SmartWifi';
                            $arrayParams['strRetornaPrimerWifi']           = 'SI';
                            $objInterfaceElementoAnteriorSmartWifi         = $this->emInfraestructura
                                                                                  ->getRepository('schemaBundle:InfoElemento')
                                                                                  ->getElementosSmartWifiByInterface($arrayParams);
                        }
                        else
                        {
                            $objInterfaceElementoAnteriorSmartWifi = $this->emInfraestructura
                                                                          ->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                          ->find($objServicioTecnico->getInterfaceElementoClienteId());
                        }
                        
                        if($strEsPlan == "NO")
                        {
                            //guardar ont en servicio tecnico
                            $objServicioTecnico->setElementoClienteId($objElementoNuevoSmartWifi->getId());
                            $objServicioTecnico->setInterfaceElementoClienteId($objInterfaceElementoSmartWifi->getId());
                            $this->emComercial->persist($objServicioTecnico);
                            $this->emComercial->flush();
                        }

                        if(is_object($objInterfaceElementoAnteriorSmartWifi))
                        {
                            //elimino enlace
                            $objEnlaceCliente = $this
                                               ->emInfraestructura
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

                                //crear nuevo enlace
                                $objEnlaceNuevo = new InfoEnlace();
                                $objEnlaceNuevo->setInterfaceElementoIniId($objEnlaceCliente->getInterfaceElementoIniId());
                                $objEnlaceNuevo->setInterfaceElementoFinId($objInterfaceElementoSmartWifi);
                                $objEnlaceNuevo->setTipoMedioId($objEnlaceCliente->getTipoMedioId());
                                $objEnlaceNuevo->setTipoEnlace("PRINCIPAL");
                                $objEnlaceNuevo->setEstado("Activo");
                                $objEnlaceNuevo->setUsrCreacion($strUsrCreacion);
                                $objEnlaceNuevo->setFeCreacion(new \DateTime('now'));
                                $objEnlaceNuevo->setIpCreacion($strIpCreacion);
                                $this->emInfraestructura->persist($objEnlaceNuevo);
                                $this->emInfraestructura->flush(); 
                                
                                $objEnlaceClienteSiguiente = $this
                                                             ->emInfraestructura
                                                             ->getRepository('schemaBundle:InfoEnlace')
                                                             ->findOneBy(array("interfaceElementoIniId" => $objInterfaceElementoAnteriorSmartWifi
                                                                                                           ->getId(),
                                                                               "estado"                 => "Activo"));

                                //se valida que exista un elemento WIFI relacionado al ONT registrado dentro de los recursos tecnicos del servicio
                                if(is_object($objEnlaceClienteSiguiente))
                                {
                                    //elimino enlace
                                    $objEnlaceClienteSiguiente->setEstado("Eliminado");
                                    $this->emInfraestructura->persist($objEnlaceClienteSiguiente);
                                    $this->emInfraestructura->flush(); 
                                    
                                    //crear nuevo enlace
                                    $objEnlaceNuevoSiguiente = new InfoEnlace();
                                    $objEnlaceNuevoSiguiente->setInterfaceElementoIniId($objInterfaceElementoSmartWifi);
                                    $objEnlaceNuevoSiguiente->setInterfaceElementoFinId($objEnlaceClienteSiguiente->getInterfaceElementoFinId());
                                    $objEnlaceNuevoSiguiente->setTipoMedioId($objEnlaceClienteSiguiente->getTipoMedioId());
                                    $objEnlaceNuevoSiguiente->setTipoEnlace("PRINCIPAL");
                                    $objEnlaceNuevoSiguiente->setEstado("Activo");
                                    $objEnlaceNuevoSiguiente->setUsrCreacion($strUsrCreacion);
                                    $objEnlaceNuevoSiguiente->setFeCreacion(new \DateTime('now'));
                                    $objEnlaceNuevoSiguiente->setIpCreacion($strIpCreacion);
                                    $this->emInfraestructura->persist($objEnlaceNuevoSiguiente);
                                    $this->emInfraestructura->flush(); 
                                }
                                
                                //finalizar solicitud de cambio de modem inmediato y crear solicitud de retiro de equipo
                                $objTipoSolicitudCambio = $this->emComercial
                                                               ->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                               ->findOneBy(array("descripcionSolicitud" => "SOLICITUD CAMBIO EQUIPO", 
                                                                                 "estado"               => "Activo"));
                                
                                if(!is_object($objTipoSolicitudCambio))
                                {
                                    $arrayRespuestaFinal[] = array('status'  => 'ERROR', 
                                                                   'mensaje' => 'No se recuperó correctamente información '.
                                                                                'de la solicitud de cambio de equipo');
                                    return $arrayRespuestaFinal;
                                }
                    
                                $objSolicitudCambioSmartWifi = $this->emComercial
                                                                    ->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                    ->findOneBy(array("servicioId"      => $objServicio->getId(), 
                                                                                      "tipoSolicitudId" => $objTipoSolicitudCambio, 
                                                                                      "estado"          => "AsignadoTarea"));
                                if(is_object($objSolicitudCambioSmartWifi))
                                {
                                    $strEstadoDetalleSol    = "AsignadoTarea";
                                    $objSolicitudCambioSmartWifi->setEstado("Finalizada");
                                    $this->emComercial->persist($objSolicitudCambioSmartWifi);
                                    $this->emComercial->flush();
                                }
                                else
                                {
                                    $strEstadoDetalleSol    = "Finalizada";
                                    $objTipoSolicitudCambio = $this->emComercial
                                                                   ->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                                   ->findOneBy(array("descripcionSolicitud" => "SOLICITUD CAMBIO DE MODEM INMEDIATO", 
                                                                                     "estado"               => "Activo"));
                                    $objSolicitudCambioSmartWifi = $this->emComercial
                                                                        ->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                        ->findOneBy(array("servicioId"      => $objServicio->getId(), 
                                                                                          "tipoSolicitudId" => $objTipoSolicitudCambio, 
                                                                                          "estado"          => "AsignadoTarea"));
                                    if (is_object($objSolicitudCambioSmartWifi))
                                    {
                                        //eliminar las caracteristicas de la solicitud en estado AsignadoTarea
                                        $arrayCaracteristicasSol = $this->emComercial
                                                                        ->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                        ->findBy(array("detalleSolicitudId" => $objSolicitudCambioSmartWifi->getId(), 
                                                                                       "estado"             => "AsignadoTarea"));
                                        foreach($arrayCaracteristicasSol as $objCaracteristicaSolicitud)
                                        {
                                            $objCaracteristicaSolicitud->setEstado($strEstadoDetalleSol);
                                            $objCaracteristicaSolicitud->setUsrCreacion($strUsrCreacion);
                                            $objCaracteristicaSolicitud->setFeCreacion(new \DateTime('now'));
                                            $this->emComercial->persist($objCaracteristicaSolicitud);
                                            $this->emComercial->flush();
                                        }

                                        $arrayCaractSolicitud = $this->emComercial
                                                                ->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                ->findBy(array("detalleSolicitudId" => $objSolicitudCambioSmartWifi->getId(), 
                                                                               "estado"             => "Activo"));
                                        if(count($arrayCaractSolicitud) == 0)
                                        {
                                            $objSolicitudCambioSmartWifi->setEstado($strEstadoDetalleSol);
                                            $this->emComercial->persist($objSolicitudCambioSmartWifi);
                                            $this->emComercial->flush();
                                        }
                                    }
                                }
                                
                                if (is_object($objSolicitudCambioSmartWifi))
                                {
                                    if( $objSolicitudCambioSmartWifi->getEstado() == "Finalizada" )
                                    {
                                        //crear solicitud para retiro de equipo (cpe)
                                        $objTipoSolicitud = $this->emComercial
                                                                 ->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                                 ->findOneBy(array("descripcionSolicitud" => "SOLICITUD RETIRO EQUIPO", 
                                                                                   "estado"               => "Activo"));

                                        $objDetalleSolicitud = new InfoDetalleSolicitud();
                                        $objDetalleSolicitud->setServicioId($objServicio);
                                        $objDetalleSolicitud->setTipoSolicitudId($objTipoSolicitud);
                                        $objDetalleSolicitud->setEstado("AsignadoTarea");
                                        $objDetalleSolicitud->setUsrCreacion($strUsrCreacion);
                                        $objDetalleSolicitud->setFeCreacion(new \DateTime('now'));
                                        $objDetalleSolicitud->setObservacion("SOLICITA RETIRO DE EQUIPO POR CAMBIO DE MODEM");
                                        $this->emComercial->persist($objDetalleSolicitud);
                                        $this->emComercial->flush();

                                        //crear las caract para la solicitud de retiro de equipo
                                        $objCaracteristica = $this->emComercial
                                                                  ->getRepository('schemaBundle:AdmiCaracteristica')
                                                                  ->findOneBy(array('descripcionCaracteristica'  => 'ELEMENTO CLIENTE',
                                                                                    'estado'                     => 'Activo'));


                                        $arrayCaractSolCambioElemento = $this
                                                                        ->emComercial
                                                                        ->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                        ->findBy(array("detalleSolicitudId" => $objSolicitudCambioSmartWifi->getId(),
                                                                                       "caracteristicaId"   => $objCaracteristica,
                                                                                       "estado"             => $strEstadoDetalleSol));

                                        foreach($arrayCaractSolCambioElemento as $objCaracteristicaSolicitud)
                                        {
                                            $objDetSolCaract = new InfoDetalleSolCaract();
                                            $objDetSolCaract->setCaracteristicaId($objCaracteristica);
                                            $objDetSolCaract->setDetalleSolicitudId($objDetalleSolicitud);
                                            $objDetSolCaract->setValor($objCaracteristicaSolicitud->getValor());
                                            $objDetSolCaract->setEstado("AsignadoTarea");
                                            $objDetSolCaract->setUsrCreacion($strUsrCreacion);
                                            $objDetSolCaract->setFeCreacion(new \DateTime('now'));
                                            $this->emComercial->persist($objDetSolCaract);
                                            $this->emComercial->flush();
                                        }

                                        //buscar el info_detalle de la solicitud
                                        $objDetalleCambioSmartWifi = $this->emComercial
                                                                          ->getRepository('schemaBundle:InfoDetalle')
                                                                          ->findOneBy(
                                                                                      array("detalleSolicitudId" => $objSolicitudCambioSmartWifi
                                                                                                                    ->getId()
                                                                                           )
                                                                                     );

                                        //obtener tarea
                                        $objProceso  = $this->emSoporte
                                                            ->getRepository('schemaBundle:AdmiProceso')
                                                            ->findOneByNombreProceso("SOLICITAR RETIRO EQUIPO");
                                        
                                        if(!is_object($objProceso))
                                        {
                                            $arrayRespuestaFinal[] = array('status'  => 'ERROR', 
                                                                           'mensaje' => 'No se recuperó correctamente información '.
                                                                                        'de la solicitud de retiro de equipo');
                                            return $arrayRespuestaFinal;
                                        }
                                
                                        $arrayTareas = $this->emSoporte
                                                            ->getRepository('schemaBundle:AdmiTarea')
                                                            ->findTareasActivasByProceso($objProceso->getId());
                                        $objTarea    = $arrayTareas[0];

                                        //grabar nuevo info_detalle para la solicitud de retiro de equipo
                                        $objDetalle = new InfoDetalle();
                                        $objDetalle->setDetalleSolicitudId($objDetalleSolicitud->getId());
                                        $objDetalle->setTareaId($objTarea);
                                        $objDetalle->setLongitud($objServicio->getPuntoId()->getLongitud());
                                        $objDetalle->setLatitud($objServicio->getPuntoId()->getLatitud());
                                        $objDetalle->setPesoPresupuestado(0);
                                        $objDetalle->setValorPresupuestado(0);
                                        $objDetalle->setIpCreacion($strIpCreacion);
                                        $objDetalle->setFeCreacion(new \DateTime('now'));
                                        $objDetalle->setUsrCreacion($strUsrCreacion);
                                        $this->emSoporte->persist($objDetalle);
                                        $this->emSoporte->flush();

                                        //buscar la info_detalle_asignacion de la solicitud
                                        if(is_object($objDetalleCambioSmartWifi))
                                        {
                                            $objDetAsigCambioSmartWifi = $this->emComercial
                                                                              ->getRepository('schemaBundle:InfoDetalleAsignacion')
                                                                              ->findOneBy(array("detalleId" => $objDetalleCambioSmartWifi->getId()));
                                            
                                            if (is_object($objDetAsigCambioSmartWifi))
                                            {
                                                //asignar los mismos responsables a la solicitud de retiro de equipo
                                                $objDetalleAsignacion = new InfoDetalleAsignacion();
                                                $objDetalleAsignacion->setDetalleId($objDetalle);
                                                $objDetalleAsignacion->setAsignadoId($objDetAsigCambioSmartWifi->getAsignadoId());
                                                $objDetalleAsignacion->setAsignadoNombre($objDetAsigCambioSmartWifi->getAsignadoNombre());
                                                $objDetalleAsignacion->setRefAsignadoId($objDetAsigCambioSmartWifi->getRefAsignadoId());
                                                $objDetalleAsignacion->setRefAsignadoNombre($objDetAsigCambioSmartWifi->getRefAsignadoNombre());
                                                $objDetalleAsignacion->setPersonaEmpresaRolId($objDetAsigCambioSmartWifi->getPersonaEmpresaRolId());
                                                $objDetalleAsignacion->setTipoAsignado("EMPLEADO");
                                                $objDetalleAsignacion->setIpCreacion($strIpCreacion);
                                                $objDetalleAsignacion->setFeCreacion(new \DateTime('now'));
                                                $objDetalleAsignacion->setUsrCreacion($strUsrCreacion);
                                                $this->emSoporte->persist($objDetalleAsignacion);
                                                $this->emSoporte->flush();

                                                $arrayParametrosAuditoria["intIdPersona"] = $objDetAsigCambioSmartWifi->getRefAsignadoId();
                                            }
                                        }

                                        $this->serviceInfoElemento->ingresaAuditoriaElementos($arrayParametrosAuditoria);

                                        //crear historial para la solicitud de retiro de equipo
                                        $objHistorialSolicitud = new InfoDetalleSolHist();
                                        $objHistorialSolicitud->setDetalleSolicitudId($objDetalleSolicitud);
                                        $objHistorialSolicitud->setEstado("AsignadoTarea");
                                        $objHistorialSolicitud->setObservacion("GENERACION AUTOMATICA DE SOLICITUD RETIRO DE ".
                                                                               "EQUIPO POR CAMBIO DE MODEM");
                                        $objHistorialSolicitud->setUsrCreacion($strUsrCreacion);
                                        $objHistorialSolicitud->setFeCreacion(new \DateTime('now'));
                                        $objHistorialSolicitud->setIpCreacion($strIpCreacion);
                                        $this->emComercial->persist($objHistorialSolicitud);
                                        $this->emComercial->flush();
                                    }
                                }
                                
                                //historial del servicio
                                $objServicioHistorial = new InfoServicioHistorial();
                                $objServicioHistorial->setServicioId($objServicio);
                                $objServicioHistorial->setObservacion("Se Realizo un Cambio de Elemento Cliente " . $strTipoElementoSmartWifi);
                                $objServicioHistorial->setEstado($objServicio->getEstado());
                                $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                                $objServicioHistorial->setIpCreacion($strIpCreacion);
                                $this->emComercial->persist($objServicioHistorial);
                                $this->emComercial->flush();
                                
                                $strStatus  = "OK";
                                $strMensaje = "OK";
                            }
                        }
                    }
                    else
                    {
                        $strMensaje = "ERROR WIFI NAF: ".$strMensajeError; 
                        $strStatus  = 'ERROR';
                    }
                }
                else
                {
                    $strMensaje = 'Se presentaron errores al ingresar el elemento Smart Wifi.'; 
                    $strStatus  = 'ERROR';
                }
            }
            else
            {
                $strMensaje = "ERROR WIFI NAF: ".$arrayWifiNaf[0]['mensaje']; 
                $strStatus  = 'ERROR';
            } 
        }
        catch(\Exception $ex)
        {
            if($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->rollback();
            }

            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }
            
            $this->serviceUtil->insertError('Telcos+', 
                                            'InfoCambioElementoService.cambioElementoSmartWifi', 
                                            $ex->getMessage(),
                                            $strUsrCreacion, 
                                            $strIpCreacion
                                           );
            
            $arrayRespuestaFinal[] = array('status'  => "ERROR", 
                                           'mensaje' => "Se presentaron errores al procesar el cambio de elemento, favor notificar a sistemas!");
            return $arrayRespuestaFinal;
        }

        $arrayRespuestaFinal[] = array('status' => $strStatus, 'mensaje' => $strMensaje);
        return $arrayRespuestaFinal;

    }
    
    /**
     * cambioElementoApWifi
     * 
     * Service que realiza el cambio del elemento del cliente ApWifi
     *
     * @author Creado: Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 12-09-2018
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.1 21-06-2019   Se modifica service utilizado para registrar el log de errores de la operación
     * @since 1.0
     *
     * @param Array $arrayParametros [ 
     *                                 servicio               => Objeto de la tabla InfoServicio
     *                                 servicioTecnico        => Objeto de la tabla InfoServicioTecnico
     *                                 serieCpe               => Cadena de caracteres con serie de cpe 
     *                                 codigoArticulo         => Cadena de caracteres con modelo de cpe
     *                                 nombreCpe              => Cadena de caracteres con nombre de cpe
     *                                 macCpe                 => Cadena de caracteres con mac de cpe
     *                                 tipoElementoCpe        => Cadena de caracteres con tipo de elemento de cpe 
     *                                 idEmpresa              => Cadena de caracteres con identificador de empresa
     *                                 idElementoCliente      => Numero entero identificador de elemento del cliente
     *                                 usrCreacion            => Cadena de caracteres con usuario de creacion a utilizar
     *                                 ipCreacion             => Cadena de caracteres con ip de creacion a utilizar
     *                                 prefijoEmpresa         => Cadena de caracteres con prefigo de empresa a utilizar
     *                               ]
     * @return Array $arrayRespuestaFinal
     */
    public function cambioElementoApWifi($arrayParametros) 
    {
        $objServicio              = !empty($arrayParametros['servicio'])?$arrayParametros['servicio']:null;
        $objServicioTecnico       = !empty($arrayParametros['servicioTecnico'])?$arrayParametros['servicioTecnico']:null;
        $strSerieApWifi           = !empty($arrayParametros['serieCpe'])?$arrayParametros['serieCpe']:"";
        $strModeloApWifi          = !empty($arrayParametros['codigoArticulo'])?$arrayParametros['codigoArticulo']:"";
        $strTipoElementoApWifi    = !empty($arrayParametros['tipoElementoCpe'])?$arrayParametros['tipoElementoCpe']:"";
        $strMacApWifi             = !empty($arrayParametros['macCpe'])?$arrayParametros['macCpe']:"";
        $intIdElementoCliente     = !empty($arrayParametros['idElementoCliente'])?$arrayParametros['idElementoCliente']:0;
        $strEmpresaCod            = !empty($arrayParametros['idEmpresa'])?$arrayParametros['idEmpresa']:"";
        $strUsrCreacion           = !empty($arrayParametros['usrCreacion'])?$arrayParametros['usrCreacion']:"";
        $strIpCreacion            = !empty($arrayParametros['ipCreacion'])?$arrayParametros['ipCreacion']:"";
        $strEsPlan                = !empty($arrayParametros['strEsPlan'])?$arrayParametros['strEsPlan']:""; 
        $strUltimaMilla           = '';
        $strTipoArticulo          = "AF";
        $strIdentificacionCliente = "";
        $arrayRespuestaFinal      = array();
        $arrayParametrosAuditoria = array();

        try
        {
            if(!is_object($objServicio))
            {
                $arrayRespuestaFinal[] = array('status' => 'ERROR', 'mensaje' => 'No existe información del servicio, favor revisar!');
                return $arrayRespuestaFinal;
            }

            if(!is_object($objServicioTecnico))
            {
                $arrayRespuestaFinal[] = array('status' => 'ERROR', 'mensaje' => 'No existe información técnica del servicio, favor revisar!');
                return $arrayRespuestaFinal;
            }
            //obtener elemento ApWifi
            $objElementoApWifi = $this->emInfraestructura
                                         ->getRepository('schemaBundle:InfoElemento')
                                         ->find($intIdElementoCliente);
            if(!is_object($objElementoApWifi))
            {
                $arrayRespuestaFinal[] = array('status' => 'ERROR', 'mensaje' => 'No existe el elemento ApWifi del cliente, favor revisar!');
                return $arrayRespuestaFinal;
            }

            $objTipoMedio = $this->emInfraestructura
                                 ->getRepository('schemaBundle:AdmiTipoMedio')
                                 ->find($objServicioTecnico->getUltimaMillaId());
            if (is_object($objTipoMedio))
            {
                $strUltimaMilla = $objTipoMedio->getNombreTipoMedio(); 
            }
            
            //se eliminan elementos del servicio*******
            $objElementoApWifi->setEstado("Eliminado");
            $this->emInfraestructura->persist($objElementoApWifi);
            $this->emInfraestructura->flush();

            //SE REGISTRA EL TRACKING DEL ELEMENTO - ANTERIOR
            $arrayParametrosAuditoria["strNumeroSerie"]  = $objElementoApWifi->getSerieFisica();
            $arrayParametrosAuditoria["strEstadoTelcos"] = 'Eliminado';
            $arrayParametrosAuditoria["strEstadoNaf"]    = 'Instalado';
            $arrayParametrosAuditoria["strEstadoActivo"] = 'CambioEquipo';
            $arrayParametrosAuditoria["strUbicacion"]    = 'EnTransito';
            $arrayParametrosAuditoria["strCodEmpresa"]   = $strEmpresaCod;
            $arrayParametrosAuditoria["strTransaccion"]  = 'Cambio de Elemento';
            $arrayParametrosAuditoria["intOficinaId"]    = 0;

            //Se consulta el login del cliente
            if(is_object($objServicioTecnico->getServicioId()))
            {
                $objInfoPunto = $this->emInfraestructura->getRepository('schemaBundle:InfoPunto')
                                                        ->find($objServicioTecnico->getServicioId()->getPuntoId()->getId());
                if(is_object($objInfoPunto))
                {
                    $arrayParametrosAuditoria["strLogin"] = $objInfoPunto->getLogin();
                }
            }

            $arrayParametrosAuditoria["strUsrCreacion"] = $strUsrCreacion;
            ////

            //historial del elemento
            $objHistorialElemento = new InfoHistorialElemento();
            $objHistorialElemento->setElementoId($objElementoApWifi);
            $objHistorialElemento->setObservacion("Se elimino el elemento por Cambio de equipo");
            $objHistorialElemento->setEstadoElemento("Eliminado");
            $objHistorialElemento->setUsrCreacion($strUsrCreacion);
            $objHistorialElemento->setFeCreacion(new \DateTime('now'));
            $objHistorialElemento->setIpCreacion($strIpCreacion);
            $this->emInfraestructura->persist($objHistorialElemento);
            $this->emInfraestructura->flush();

            //eliminar puertos del elemento
            $arrayInterfacesElemento = $this->emInfraestructura
                                            ->getRepository('schemaBundle:InfoInterfaceElemento')
                                            ->findBy(array("elementoId" => $objElementoApWifi->getId()));

            foreach($arrayInterfacesElemento as $objInterfaceElemento)
            {
                $objInterfaceElemento->setEstado("Eliminado");
                $this->emInfraestructura->persist($objInterfaceElemento);
                $this->emInfraestructura->flush();
            }
            
            //se procede a realizar el ingreso del elemento Ap Wifi y despacharlo en el NAF
            $arrayWifiNaf = $this->servicioGeneral->buscarElementoEnNaf($strSerieApWifi, 
                                                                        $strModeloApWifi, 
                                                                        "PI", 
                                                                        "ActivarServicio");
            $strWifiNaf            = $arrayWifiNaf[0]['status'];
            $strCodigoArticuloWifi = "";
            if($strWifiNaf == "OK")
            {
                $objInterfaceElementoApWifi = $this->servicioGeneral
                                                      ->ingresarElementoCliente( $objServicio->getPuntoId()->getLogin(), 
                                                                                 $strSerieApWifi, 
                                                                                 $strModeloApWifi,
                                                                                 '-'.$objServicio->getId().'-RentaApWifi', 
                                                                                 null, 
                                                                                 $strUltimaMilla,
                                                                                 $objServicio, 
                                                                                 $strUsrCreacion, 
                                                                                 $strIpCreacion, 
                                                                                 $strEmpresaCod );
                if(is_object($objInterfaceElementoApWifi))
                {
                    $objElementoNuevoApWifi = $objInterfaceElementoApWifi->getElementoId();
                    
                    if(!is_object($objElementoNuevoApWifi))
                    {
                        $arrayRespuestaFinal[] = array('status'  => 'ERROR', 
                                                       'mensaje' => 'No se recuperó correctamente información del elemento ApWifi');
                        return $arrayRespuestaFinal;
                    }
                    //historial del servicio
                    $objServicioHistorial = new InfoServicioHistorial();
                    $objServicioHistorial->setServicioId($objServicio);
                    $objServicioHistorial->setObservacion("Se registro el elemento con nombre: ".
                                                          $objElementoNuevoApWifi->getNombreElemento().
                                                          ", Serie: ".
                                                          $strSerieApWifi.
                                                          ", Modelo: ".
                                                          $strModeloApWifi.
                                                          ", Mac: ".
                                                          $strMacApWifi
                                                         );
                    $objServicioHistorial->setEstado($objServicio->getEstado());
                    $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                    $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                    $objServicioHistorial->setIpCreacion($strIpCreacion);
                    $this->emComercial->persist($objServicioHistorial);
                    $this->emComercial->flush();
                    
                    //actualizamos registro en el naf wifi
                    $strMensajeError = str_repeat(' ', 1000);                                                                  
                    $strSql          = "BEGIN AFK_PROCESOS.IN_P_PROCESA_INSTALACION(:codigoEmpresaNaf, ".
                                       ":codigoArticulo, :tipoArticulo, :identificacionCliente, :serieCpe, ".
                                       ":cantidad, :pv_mensajeerror); END;";
                    $objStmt = $this->emNaf->getConnection()->prepare($strSql);
                    $objStmt->bindParam('codigoEmpresaNaf',      $strEmpresaCod);
                    $objStmt->bindParam('codigoArticulo',        $strCodigoArticuloWifi);
                    $objStmt->bindParam('tipoArticulo',          $strTipoArticulo);
                    $objStmt->bindParam('identificacionCliente', $strIdentificacionCliente);
                    $objStmt->bindParam('serieCpe',              $strSerieApWifi);
                    $objStmt->bindParam('cantidad',              intval(1));
                    $objStmt->bindParam('pv_mensajeerror',       $strMensajeError);
                    $objStmt->execute();

                    if(strlen(trim($strMensajeError))<=0)
                    {
                        if($strEsPlan == "NO")
                        {
                            $objMacApWifi = $this->servicioGeneral
                                                    ->getServicioProductoCaracteristica($objServicio, "MAC WIFI", $objServicio->getProductoId());

                            if(is_object($objMacApWifi))
                            {
                                $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objMacApWifi, "Eliminado");
                            }
                            //servicio prod caract mac wifi
                            $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio, 
                                                                                           $objServicio->getProductoId(), 
                                                                                           "MAC WIFI", 
                                                                                           $strMacApWifi, 
                                                                                           $strUsrCreacion);
                        }
                        
                        //info_detalle_elemento gestion remota
                        $this->servicioGeneral->ingresarDetalleElemento($objElementoNuevoApWifi,
                                                                        "MAC WIFI",
                                                                        "MAC WIFI",
                                                                        $strMacApWifi,
                                                                        $strUsrCreacion,
                                                                        $strIpCreacion); 
                        if($strEsPlan == "SI")
                        {
                            $arrayParams['intInterfaceElementoConectorId'] = $objServicioTecnico->getInterfaceElementoClienteId();
                            $arrayParams['arrayData']                      = array();
                            $arrayParams['strBanderaReturn']               = 'INTERFACE';
                            $arrayParams['strTipoApWifi']               = 'ApWifi';
                            $arrayParams['strRetornaPrimerWifi']           = 'SI';
                            $objInterfaceElementoAnteriorApWifi            = $this->emInfraestructura
                                                                                  ->getRepository('schemaBundle:InfoElemento')
                                                                                  ->getElementosApWifiByInterface($arrayParams);
                        }
                        else
                        {
                            $objInterfaceElementoAnteriorApWifi = $this->emInfraestructura
                                                                       ->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                       ->find($objServicioTecnico->getInterfaceElementoClienteId());
                        }
                        
                        if($strEsPlan == "NO")
                        {
                            //guardar ont en servicio tecnico
                            $objServicioTecnico->setElementoClienteId($objElementoNuevoApWifi->getId());
                            $objServicioTecnico->setInterfaceElementoClienteId($objInterfaceElementoApWifi->getId());
                            $this->emComercial->persist($objServicioTecnico);
                            $this->emComercial->flush();
                        }

                        if(is_object($objInterfaceElementoAnteriorApWifi))
                        {
                            //elimino enlace
                            $objEnlaceCliente = $this
                                               ->emInfraestructura
                                                ->getRepository('schemaBundle:InfoEnlace')
                                                ->findOneBy(array("interfaceElementoFinId" => $objInterfaceElementoAnteriorApWifi->getId(),
                                                                  "estado"                 => "Activo"));
                            
                            //se valida que exista un elemento WIFI relacionado al ONT registrado dentro de los recursos tecnicos del servicio
                            if(is_object($objEnlaceCliente))
                            {
                                //elimino enlace
                                $objEnlaceCliente->setEstado("Eliminado");
                                $this->emInfraestructura->persist($objEnlaceCliente);
                                $this->emInfraestructura->flush(); 

                                //crear nuevo enlace
                                $objEnlaceNuevo = new InfoEnlace();
                                $objEnlaceNuevo->setInterfaceElementoIniId($objEnlaceCliente->getInterfaceElementoIniId());
                                $objEnlaceNuevo->setInterfaceElementoFinId($objInterfaceElementoApWifi);
                                $objEnlaceNuevo->setTipoMedioId($objEnlaceCliente->getTipoMedioId());
                                $objEnlaceNuevo->setTipoEnlace("PRINCIPAL");
                                $objEnlaceNuevo->setEstado("Activo");
                                $objEnlaceNuevo->setUsrCreacion($strUsrCreacion);
                                $objEnlaceNuevo->setFeCreacion(new \DateTime('now'));
                                $objEnlaceNuevo->setIpCreacion($strIpCreacion);
                                $this->emInfraestructura->persist($objEnlaceNuevo);
                                $this->emInfraestructura->flush(); 
                                
                                $objEnlaceClienteSiguiente = $this
                                                             ->emInfraestructura
                                                             ->getRepository('schemaBundle:InfoEnlace')
                                                             ->findOneBy(array("interfaceElementoIniId" => $objInterfaceElementoAnteriorApWifi
                                                                                                           ->getId(),
                                                                               "estado"                 => "Activo"));

                                //se valida que exista un elemento WIFI relacionado al ONT registrado dentro de los recursos tecnicos del servicio
                                if(is_object($objEnlaceClienteSiguiente))
                                {
                                    //elimino enlace
                                    $objEnlaceClienteSiguiente->setEstado("Eliminado");
                                    $this->emInfraestructura->persist($objEnlaceClienteSiguiente);
                                    $this->emInfraestructura->flush(); 
                                    
                                    //crear nuevo enlace
                                    $objEnlaceNuevoSiguiente = new InfoEnlace();
                                    $objEnlaceNuevoSiguiente->setInterfaceElementoIniId($objInterfaceElementoApWifi);
                                    $objEnlaceNuevoSiguiente->setInterfaceElementoFinId($objEnlaceClienteSiguiente->getInterfaceElementoFinId());
                                    $objEnlaceNuevoSiguiente->setTipoMedioId($objEnlaceClienteSiguiente->getTipoMedioId());
                                    $objEnlaceNuevoSiguiente->setTipoEnlace("PRINCIPAL");
                                    $objEnlaceNuevoSiguiente->setEstado("Activo");
                                    $objEnlaceNuevoSiguiente->setUsrCreacion($strUsrCreacion);
                                    $objEnlaceNuevoSiguiente->setFeCreacion(new \DateTime('now'));
                                    $objEnlaceNuevoSiguiente->setIpCreacion($strIpCreacion);
                                    $this->emInfraestructura->persist($objEnlaceNuevoSiguiente);
                                    $this->emInfraestructura->flush(); 
                                }
                                
                                //finalizar solicitud de cambio de modem inmediato y crear solicitud de retiro de equipo
                                $objTipoSolicitudCambio = $this->emComercial
                                                               ->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                               ->findOneBy(array("descripcionSolicitud" => "SOLICITUD CAMBIO EQUIPO", 
                                                                                 "estado"               => "Activo"));
                                
                                if(!is_object($objTipoSolicitudCambio))
                                {
                                    $arrayRespuestaFinal[] = array('status'  => 'ERROR', 
                                                                   'mensaje' => 'No se recuperó correctamente información '.
                                                                                'de la solicitud de cambio de equipo');
                                    return $arrayRespuestaFinal;
                                }
                    
                                $objSolicitudCambioApWifi = $this->emComercial
                                                                    ->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                    ->findOneBy(array("servicioId"      => $objServicio->getId(), 
                                                                                      "tipoSolicitudId" => $objTipoSolicitudCambio, 
                                                                                      "estado"          => "AsignadoTarea"));
                                if(is_object($objSolicitudCambioApWifi))
                                {
                                    $strEstadoDetalleSol    = "AsignadoTarea";
                                    $objSolicitudCambioApWifi->setEstado("Finalizada");
                                    $this->emComercial->persist($objSolicitudCambioApWifi);
                                    $this->emComercial->flush();
                                }
                                else
                                {
                                    $strEstadoDetalleSol    = "Finalizada";
                                    $objTipoSolicitudCambio = $this->emComercial
                                                                   ->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                                   ->findOneBy(array("descripcionSolicitud" => "SOLICITUD CAMBIO DE MODEM INMEDIATO", 
                                                                                     "estado"               => "Activo"));
                                    $objSolicitudCambioApWifi = $this->emComercial
                                                                        ->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                        ->findOneBy(array("servicioId"      => $objServicio->getId(), 
                                                                                          "tipoSolicitudId" => $objTipoSolicitudCambio, 
                                                                                          "estado"          => "AsignadoTarea"));
                                    if (is_object($objSolicitudCambioApWifi))
                                    {
                                        //eliminar las caracteristicas de la solicitud en estado AsignadoTarea
                                        $arrayCaracteristicasSol = $this->emComercial
                                                                        ->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                        ->findBy(array("detalleSolicitudId" => $objSolicitudCambioApWifi->getId(), 
                                                                                       "estado"             => "AsignadoTarea"));
                                        foreach($arrayCaracteristicasSol as $objCaracteristicaSolicitud)
                                        {
                                            $objCaracteristicaSolicitud->setEstado($strEstadoDetalleSol);
                                            $objCaracteristicaSolicitud->setUsrCreacion($strUsrCreacion);
                                            $objCaracteristicaSolicitud->setFeCreacion(new \DateTime('now'));
                                            $this->emComercial->persist($objCaracteristicaSolicitud);
                                            $this->emComercial->flush();
                                        }

                                        $arrayCaractSolicitud = $this->emComercial
                                                                ->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                ->findBy(array("detalleSolicitudId" => $objSolicitudCambioApWifi->getId(), 
                                                                               "estado"             => "Activo"));
                                        if(count($arrayCaractSolicitud) == 0)
                                        {
                                            $objSolicitudCambioApWifi->setEstado($strEstadoDetalleSol);
                                            $this->emComercial->persist($objSolicitudCambioApWifi);
                                            $this->emComercial->flush();
                                        }
                                    }
                                }
                                
                                if (is_object($objSolicitudCambioApWifi) &&  $objSolicitudCambioApWifi->getEstado() == "Finalizada" )
                                {
                                    //crear solicitud para retiro de equipo (cpe)
                                    $objTipoSolicitud = $this->emComercial
                                                             ->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                             ->findOneBy(array("descripcionSolicitud" => "SOLICITUD RETIRO EQUIPO", 
                                                                               "estado"               => "Activo"));

                                    $objDetalleSolicitud = new InfoDetalleSolicitud();
                                    $objDetalleSolicitud->setServicioId($objServicio);
                                    $objDetalleSolicitud->setTipoSolicitudId($objTipoSolicitud);
                                    $objDetalleSolicitud->setEstado("AsignadoTarea");
                                    $objDetalleSolicitud->setUsrCreacion($strUsrCreacion);
                                    $objDetalleSolicitud->setFeCreacion(new \DateTime('now'));
                                    $objDetalleSolicitud->setObservacion("SOLICITA RETIRO DE EQUIPO POR CAMBIO DE MODEM");
                                    $this->emComercial->persist($objDetalleSolicitud);
                                    $this->emComercial->flush();

                                    //crear las caract para la solicitud de retiro de equipo
                                    $objCaracteristica = $this->emComercial
                                                              ->getRepository('schemaBundle:AdmiCaracteristica')
                                                              ->findOneBy(array('descripcionCaracteristica'  => 'ELEMENTO CLIENTE',
                                                                                'estado'                     => 'Activo'));


                                    $arrayCaractSolCambioElemento = $this
                                                                    ->emComercial
                                                                    ->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                    ->findBy(array("detalleSolicitudId" => $objSolicitudCambioApWifi->getId(),
                                                                                   "caracteristicaId"   => $objCaracteristica,
                                                                                   "estado"             => $strEstadoDetalleSol));

                                    foreach($arrayCaractSolCambioElemento as $objCaracteristicaSolicitud)
                                    {
                                        $objDetSolCaract = new InfoDetalleSolCaract();
                                        $objDetSolCaract->setCaracteristicaId($objCaracteristica);
                                        $objDetSolCaract->setDetalleSolicitudId($objDetalleSolicitud);
                                        $objDetSolCaract->setValor($objCaracteristicaSolicitud->getValor());
                                        $objDetSolCaract->setEstado("AsignadoTarea");
                                        $objDetSolCaract->setUsrCreacion($strUsrCreacion);
                                        $objDetSolCaract->setFeCreacion(new \DateTime('now'));
                                        $this->emComercial->persist($objDetSolCaract);
                                        $this->emComercial->flush();
                                    }

                                    //buscar el info_detalle de la solicitud
                                    $objDetalleCambioApWifi = $this->emComercial
                                                                   ->getRepository('schemaBundle:InfoDetalle')
                                                                   ->findOneBy(
                                                                                array("detalleSolicitudId" => $objSolicitudCambioApWifi
                                                                                                              ->getId()
                                                                                      )
                                                                              );

                                    //obtener tarea
                                    $objProceso  = $this->emSoporte
                                                        ->getRepository('schemaBundle:AdmiProceso')
                                                        ->findOneByNombreProceso("SOLICITAR RETIRO EQUIPO");

                                    if(!is_object($objProceso))
                                    {
                                        $arrayRespuestaFinal[] = array('status'  => 'ERROR', 
                                                                       'mensaje' => 'No se recuperó correctamente información '.
                                                                                    'de la solicitud de retiro de equipo');
                                        return $arrayRespuestaFinal;
                                    }

                                    $arrayTareas = $this->emSoporte
                                                        ->getRepository('schemaBundle:AdmiTarea')
                                                        ->findTareasActivasByProceso($objProceso->getId());
                                    $objTarea    = $arrayTareas[0];

                                    //grabar nuevo info_detalle para la solicitud de retiro de equipo
                                    $objDetalle = new InfoDetalle();
                                    $objDetalle->setDetalleSolicitudId($objDetalleSolicitud->getId());
                                    $objDetalle->setTareaId($objTarea);
                                    $objDetalle->setLongitud($objServicio->getPuntoId()->getLongitud());
                                    $objDetalle->setLatitud($objServicio->getPuntoId()->getLatitud());
                                    $objDetalle->setPesoPresupuestado(0);
                                    $objDetalle->setValorPresupuestado(0);
                                    $objDetalle->setIpCreacion($strIpCreacion);
                                    $objDetalle->setFeCreacion(new \DateTime('now'));
                                    $objDetalle->setUsrCreacion($strUsrCreacion);
                                    $this->emSoporte->persist($objDetalle);
                                    $this->emSoporte->flush();

                                    //buscar la info_detalle_asignacion de la solicitud
                                    if(is_object($objDetalleCambioApWifi))
                                    {
                                        $objDetAsigCambioApWifi = $this->emComercial
                                                                       ->getRepository('schemaBundle:InfoDetalleAsignacion')
                                                                       ->findOneBy(array("detalleId" => $objDetalleCambioApWifi->getId()));

                                        if (is_object($objDetAsigCambioApWifi))
                                        {
                                            //asignar los mismos responsables a la solicitud de retiro de equipo
                                            $objDetalleAsignacion = new InfoDetalleAsignacion();
                                            $objDetalleAsignacion->setDetalleId($objDetalle);
                                            $objDetalleAsignacion->setAsignadoId($objDetAsigCambioApWifi->getAsignadoId());
                                            $objDetalleAsignacion->setAsignadoNombre($objDetAsigCambioApWifi->getAsignadoNombre());
                                            $objDetalleAsignacion->setRefAsignadoId($objDetAsigCambioApWifi->getRefAsignadoId());
                                            $objDetalleAsignacion->setRefAsignadoNombre($objDetAsigCambioApWifi->getRefAsignadoNombre());
                                            $objDetalleAsignacion->setPersonaEmpresaRolId($objDetAsigCambioApWifi->getPersonaEmpresaRolId());
                                            $objDetalleAsignacion->setTipoAsignado("EMPLEADO");
                                            $objDetalleAsignacion->setIpCreacion($strIpCreacion);
                                            $objDetalleAsignacion->setFeCreacion(new \DateTime('now'));
                                            $objDetalleAsignacion->setUsrCreacion($strUsrCreacion);
                                            $this->emSoporte->persist($objDetalleAsignacion);
                                            $this->emSoporte->flush();

                                            $arrayParametrosAuditoria["intIdPersona"] = $objDetAsigCambioApWifi->getRefAsignadoId();
                                        }
                                    }

                                    $this->serviceInfoElemento->ingresaAuditoriaElementos($arrayParametrosAuditoria);

                                    //crear historial para la solicitud de retiro de equipo
                                    $objHistorialSolicitud = new InfoDetalleSolHist();
                                    $objHistorialSolicitud->setDetalleSolicitudId($objDetalleSolicitud);
                                    $objHistorialSolicitud->setEstado("AsignadoTarea");
                                    $objHistorialSolicitud->setObservacion("GENERACION AUTOMATICA DE SOLICITUD RETIRO DE ".
                                                                           "EQUIPO POR CAMBIO DE MODEM");
                                    $objHistorialSolicitud->setUsrCreacion($strUsrCreacion);
                                    $objHistorialSolicitud->setFeCreacion(new \DateTime('now'));
                                    $objHistorialSolicitud->setIpCreacion($strIpCreacion);
                                    $this->emComercial->persist($objHistorialSolicitud);
                                    $this->emComercial->flush();
                                }
                                
                                //historial del servicio
                                $objServicioHistorial = new InfoServicioHistorial();
                                $objServicioHistorial->setServicioId($objServicio);
                                $objServicioHistorial->setObservacion("Se Realizo un Cambio de Elemento Cliente " . $strTipoElementoApWifi);
                                $objServicioHistorial->setEstado($objServicio->getEstado());
                                $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                                $objServicioHistorial->setIpCreacion($strIpCreacion);
                                $this->emComercial->persist($objServicioHistorial);
                                $this->emComercial->flush();
                                
                                $strStatus  = "OK";
                                $strMensaje = "OK";
                            }
                        }
                    }
                    else
                    {
                        $strMensaje = "ERROR WIFI NAF: ".$strMensajeError; 
                        $strStatus  = 'ERROR';
                    }
                }
                else
                {
                    $strMensaje = 'Se presentaron errores al ingresar el elemento Ap Wifi.'; 
                    $strStatus  = 'ERROR';
                }
            }
            else
            {
                $strMensaje = "ERROR WIFI NAF: ".$arrayWifiNaf[0]['mensaje']; 
                $strStatus  = 'ERROR';
            } 
        }
        catch(\Exception $ex)
        {
            if($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->rollback();
            }

            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }
            
            $this->serviceUtil->insertError('Telcos+', 
                                            'InfoCambioElementoService.cambioElementoApWifi', 
                                            $ex->getMessage(),
                                            $strUsrCreacion, 
                                            $strIpCreacion
                                           );
            
            $arrayRespuestaFinal[] = array('status'  => "ERROR", 
                                           'mensaje' => "Se presentaron errores al procesar el cambio de elemento, favor notificar a sistemas!");
            return $arrayRespuestaFinal;
        }

        $arrayRespuestaFinal[] = array('status' => $strStatus, 'mensaje' => $strMensaje);
        return $arrayRespuestaFinal;

    }

   /**procesaInstalacionElemento
    * Service que actualiza el equipo del cliente en el NAF
    *
    * @author Creado: John Vera <javera@telconet.ec>
    * @version 1.0 15-06-2015
    */  
    public function procesaInstalacionElemento($arrayParametros)
    {
        $tipoArticulo           = $arrayParametros['tipoArticulo'];
        $identificacionCliente  = $arrayParametros['identificacionCliente'];
        $empresaCod             = $arrayParametros['empresaCod'];
        $modeloCpe              = $arrayParametros['modeloCpe'];
        $serieCpe               = $arrayParametros['serieCpe'];
        $cantidad               = $arrayParametros['cantidad'];

        //actualizamos registro en el naf del cpe
        $pv_mensajeerror = str_repeat(' ', 1000);
        $sql = "BEGIN AFK_PROCESOS.IN_P_PROCESA_INSTALACION(:codigoEmpresaNaf, "
            . ":codigoArticulo, :tipoArticulo, :identificacionCliente, :serieCpe, "
            . ":cantidad, :pv_mensajeerror); END;";
        $stmt = $this->emNaf->getConnection()->prepare($sql);
        $stmt->bindParam('codigoEmpresaNaf', $empresaCod);
        $stmt->bindParam('codigoArticulo', $modeloCpe);
        $stmt->bindParam('tipoArticulo', $tipoArticulo);
        $stmt->bindParam('identificacionCliente', $identificacionCliente);
        $stmt->bindParam('serieCpe', $serieCpe);
        $stmt->bindParam('cantidad', intval($cantidad));
        $stmt->bindParam('pv_mensajeerror', $pv_mensajeerror);
        $stmt->execute();

        return $pv_mensajeerror;
    }

    public function cambioElementoTtco($servicio, $servicioTecnico, $producto, $serieCpe, $codigoArticulo, $nombreCpe, $descripcionCpe, 
                                       $ipCpe, $macCpe, $idEmpresa, $usrCreacion, $ipCreacion, $tipoElementoCpe){
        if($ipCpe=="" || $ipCpe=="NA"){
            $status="ERROR";
            $mensaje = "IP DEL EQUIPO INCORRECTA";
            $respuestaFinal[] = array('status'=>$status, 'mensaje'=>$mensaje);
            return $respuestaFinal;
        }
        
        //finalizar la solicitud de cambio de Cpe
        $tipoSolicitudCambio = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                    ->findOneBy(array( "descripcionSolicitud" => "SOLICITUD CAMBIO EQUIPO", "estado"=>"Activo"));
        $solicitudCambioCpe = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                ->findOneBy(array( "servicioId" => $servicio->getId() , "tipoSolicitudId"=>$tipoSolicitudCambio, "estado"=>"AsignadoTarea"));
        if($solicitudCambioCpe){
            $solicitudCambioCpe->SetEstado("Finalizada");
            $this->emComercial->persist($solicitudCambioCpe);
            $this->emComercial->flush();
        }
        else{
            $tipoSolicitudCambio = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                ->findOneBy(array( "descripcionSolicitud" => "SOLICITUD CAMBIO DE MODEM INMEDIATO", "estado"=>"Activo"));
            $solicitudCambioCpe = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                ->findOneBy(array( "servicioId" => $servicio->getId() , "tipoSolicitudId"=>$tipoSolicitudCambio, "estado"=>"AsignadoTarea"));
            $solicitudCambioCpe->SetEstado("Finalizada");
            $this->emComercial->persist($solicitudCambioCpe);
            $this->emComercial->flush();
            
            //eliminar las caracteristicas de la solicitud (elementos escogidos)
            $caractSolicitud = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                 ->findBy(array( "detalleSolicitudId" => $solicitudCambioCpe->getId() , "estado"=>"AsignadoTarea"));
            for($i=0;$i<count($caractSolicitud);$i++){
                $caracteristicaSolicitud = $caractSolicitud[$i];
                $caracteristicaSolicitud->setEstado("Finalizada");
                $caracteristicaSolicitud->setUsrCreacion($usrCreacion);
                $caracteristicaSolicitud->setFeCreacion(new \DateTime('now'));
                $this->emComercial->persist($caracteristicaSolicitud);
                $this->emComercial->flush();
            }
        }
        
        if($solicitudCambioCpe->getEstado()=="Finalizada"){
            //crear solicitud para retiro de equipo (cpe)
            $tipoSolicitud = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                        ->findOneBy(array( "descripcionSolicitud" => "SOLICITUD RETIRO EQUIPO", "estado"=>"Activo"));

            $detalleSolicitud = new InfoDetalleSolicitud();
            $detalleSolicitud->setServicioId($servicio);
            $detalleSolicitud->setTipoSolicitudId($tipoSolicitud);
            $detalleSolicitud->setEstado("AsignadoTarea");
            $detalleSolicitud->setUsrCreacion($usrCreacion);
            $detalleSolicitud->setFeCreacion(new \DateTime('now'));
            $detalleSolicitud->setObservacion("SOLICITA RETIRO DE EQUIPO POR CAMBIO DE MODEM");
            $this->emComercial->persist($detalleSolicitud);
            $this->emComercial->flush();             

            //crear las caract para la solicitud de retiro de equipo
            $entityAdmiCaracteristica= $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')->find(360);
            $caractSolicitudCambioElemento = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                 ->findBy(array( "detalleSolicitudId" => $solicitudCambioCpe->getId(), "estado"=>"Finalizada"));
            
            for($i=0;$i<count($caractSolicitudCambioElemento);$i++){
                $entityCaract= new InfoDetalleSolCaract();
                $entityCaract->setCaracteristicaId($entityAdmiCaracteristica);
                $entityCaract->setDetalleSolicitudId($detalleSolicitud);
                $entityCaract->setValor($caractSolicitudCambioElemento[$i]->getValor());
                $entityCaract->setEstado("AsignadoTarea");
                $entityCaract->setUsrCreacion($usrCreacion);
                $entityCaract->setFeCreacion(new \DateTime('now'));
                $this->emComercial->persist($entityCaract);
                $this->emComercial->flush();
            }
            
            //buscar el info_detalle de la solicitud
            $detalleCambioCpe = $this->emComercial->getRepository('schemaBundle:InfoDetalle')
                                                ->findOneBy(array( "detalleSolicitudId" => $solicitudCambioCpe->getId()));

            //obtener tarea
            $entityProceso = $this->emSoporte->getRepository('schemaBundle:AdmiProceso')->findOneByNombreProceso("SOLICITAR RETIRO EQUIPO");
            $entityTareas = $this->emSoporte->getRepository('schemaBundle:AdmiTarea')->findTareasActivasByProceso($entityProceso->getId());
            $entityTarea = $entityTareas[0];

            //grabar nuevo info_detalle para la solicitud de retiro de equipo
            $entityDetalle = new InfoDetalle();
            $entityDetalle->setDetalleSolicitudId($detalleSolicitud->getId());
            $entityDetalle->setTareaId($entityTarea);
            $entityDetalle->setLongitud($servicio->getPuntoId()->getLongitud());
            $entityDetalle->setLatitud($servicio->getPuntoId()->getLatitud());
            $entityDetalle->setPesoPresupuestado(0);
            $entityDetalle->setValorPresupuestado(0);
            $entityDetalle->setIpCreacion($ipCreacion);
            $entityDetalle->setFeCreacion(new \DateTime('now'));
            $entityDetalle->setUsrCreacion($usrCreacion);
            $this->emSoporte->persist($entityDetalle);
            $this->emSoporte->flush();

            //buscar la info_detalle_asignacion de la solicitud
            if($detalleCambioCpe){
                $detalleAsignacionCambioCpe = $this->emComercial->getRepository('schemaBundle:InfoDetalleAsignacion')
                                                                ->findOneBy(array( "detalleId" => $detalleCambioCpe->getId()));

                //asignar los mismos responsables a la solicitud de retiro de equipo
                $entityDetalleAsignacion = new InfoDetalleAsignacion();
                $entityDetalleAsignacion->setDetalleId($entityDetalle);
                $entityDetalleAsignacion->setAsignadoId($detalleAsignacionCambioCpe->getAsignadoId());
                $entityDetalleAsignacion->setAsignadoNombre($detalleAsignacionCambioCpe->getAsignadoNombre());
                $entityDetalleAsignacion->setRefAsignadoId($detalleAsignacionCambioCpe->getRefAsignadoId());
                $entityDetalleAsignacion->setRefAsignadoNombre($detalleAsignacionCambioCpe->getRefAsignadoNombre());
                $entityDetalleAsignacion->setIpCreacion($ipCreacion);
                $entityDetalleAsignacion->setFeCreacion(new \DateTime('now'));
                $entityDetalleAsignacion->setUsrCreacion($usrCreacion);
                $this->emSoporte->persist($entityDetalleAsignacion);
                $this->emSoporte->flush();
            }

            //crear historial para la solicitud
            $historialSolicitud = new InfoDetalleSolHist();
            $historialSolicitud->setDetalleSolicitudId($detalleSolicitud);
            $historialSolicitud->setEstado("AsignadoTarea");
            $historialSolicitud->setObservacion("GENERACION AUTOMATICA DE SOLICITUD RETIRO DE EQUIPO POR CAMBIO DE MODEM");
            $historialSolicitud->setUsrCreacion($usrCreacion);
            $historialSolicitud->setFeCreacion(new \DateTime('now'));
            $historialSolicitud->setIpCreacion($ipCreacion);
            $this->emComercial->persist($historialSolicitud);
            $this->emComercial->flush();
        }
        
        //buscamos modelo
        $modeloElementoCpe = $this->emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                                ->findOneBy(array( "nombreModeloElemento" => $codigoArticulo, "estado"=>"Activo"));
        
        if($tipoElementoCpe=="CPE WIFI"){
            $cpeInterfaceId = $servicioTecnico->getInterfaceElementoClienteId();
            $enlaceCpe = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')->findOneBy(array("interfaceElementoIniId"=>$cpeInterfaceId));
            $interfaceCpeWifi = $enlaceCpe->getInterfaceElementoFinId();
            $cpe = $interfaceCpeWifi->getElementoId();
        }
        else{
            $cpeId = $servicioTecnico->getElementoClienteId();
            $cpe = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($cpeId);
        }

        //historial elemento
        $historialElemento = new InfoHistorialElemento();
        $historialElemento->setElementoId($cpe);
        $historialElemento->setEstadoElemento($cpe->getEstado());
        $historialElemento->setObservacion("Se Cambio de Modelo, modelo anterior:".$cpe->getModeloElementoId()->getNombreModeloElemento().", serie anterior:".$cpe->getSerieFisica());
        $historialElemento->setUsrCreacion($usrCreacion);
        $historialElemento->setFeCreacion(new \DateTime('now'));
        $historialElemento->setIpCreacion($ipCreacion);
        $this->emInfraestructura->persist($historialElemento);
        $this->emInfraestructura->flush();

        //seteamos los nuevos parametros al cpe
        $cpe->setModeloElementoId($modeloElementoCpe);
        $cpe->setSerieFisica($serieCpe);
        $cpe->setDescripcionElemento($descripcionCpe);
        $cpe->setNombreElemento($nombreCpe);
        $this->emInfraestructura->persist($cpe);
        $this->emInfraestructura->flush();

        //eliminamos la anterior ip
        $ipAnterior = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                              ->findOneBy(array( "elementoId" => $cpeId, "estado"=>"Activo"));
        if($ipAnterior){
            $ipAnterior->setEstado("Eliminado");
            $this->emInfraestructura->persist($ipAnterior);
            $this->emInfraestructura->flush();
        }
        
        if($tipoElementoCpe!="CPE WIFI"){
            //ingresamos la nueva ip
            $ipElemento = new InfoIp();
            $ipElemento->setElementoId($cpeId);
            $ipElemento->setIp($ipCpe);
            $ipElemento->setVersionIp("IPV4");
            $ipElemento->setUsrCreacion($usrCreacion);
            $ipElemento->setFeCreacion(new \DateTime('now'));
            $ipElemento->setIpCreacion($ipCreacion);
            $ipElemento->setEstado("Activo");
            $this->emInfraestructura->persist($ipElemento);
            $this->emInfraestructura->flush();
        }

        //MAC
        $caracteristicaMac = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                               ->findOneBy(array( "descripcionCaracteristica" => "MAC", "estado"=>"Activo"));
        $productoCaracteristicaMac = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                        ->findOneBy(array( "productoId" => $producto->getId(), "caracteristicaId"=>$caracteristicaMac->getId()));
        $servicioProductoCaracteristicaMac = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                            ->findOneBy(array( "servicioId" => $servicio->getId(), "productoCaracterisiticaId"=>$productoCaracteristicaMac->getId()));

        if($servicioProductoCaracteristicaMac!=null){
            $macValor = $servicioProductoCaracteristicaMac->getValor();
            if($macValor!=$macCpe){
                $servicioProductoCaracteristicaMac->setValor($macCpe);
                $this->emComercial->persist($servicioProductoCaracteristicaMac);
                $this->emComercial->flush();
            }
        }
        else{
            $servicioProductoCaracteristicaMac = new InfoServicioProdCaract();
            $servicioProductoCaracteristicaMac->setServicioId($servicio->getId());
            $servicioProductoCaracteristicaMac->setProductoCaracterisiticaId($productoCaracteristicaMac->getId());
            $servicioProductoCaracteristicaMac->setValor($macCpe);
            $servicioProductoCaracteristicaMac->setEstado("Activo");
            $servicioProductoCaracteristicaMac->setUsrCreacion($usrCreacion);
            $servicioProductoCaracteristicaMac->setFeCreacion(new \DateTime('now'));
            $this->emComercial->persist($servicioProductoCaracteristicaMac);
            $this->emComercial->flush();
        }
        
        //buscar elemento cpe
        $cpeNafArray = $this->servicioGeneral->buscarElementoEnNaf($serieCpe, $codigoArticulo, "PI", "ActivarServicio");
        $cpeNaf = $cpeNafArray[0]['status'];
        $codigoArticuloCpe = $cpeNafArray[0]['mensaje'];
        if($cpeNaf=="OK"){
            $tipoArticulo="AF";
            $identificacionCliente="";
            //actualizamos registro en el naf del cpe
            $pv_mensajeerror = str_repeat(' ', 1000);                                                                    
            $sql = "BEGIN AFK_PROCESOS.IN_P_PROCESA_INSTALACION(:codigoEmpresaNaf, "
            . ":codigoArticulo, :tipoArticulo, :identificacionCliente, :serieCpe, "
            . ":cantidad, :pv_mensajeerror); END;";
            $stmt = $this->emNaf->getConnection()->prepare($sql);
            $stmt->bindParam('codigoEmpresaNaf', $idEmpresa);
            $stmt->bindParam('codigoArticulo', $codigoArticulo);
            $stmt->bindParam('tipoArticulo',$tipoArticulo);
            $stmt->bindParam('identificacionCliente', $identificacionCliente);
            $stmt->bindParam('serieCpe', $serieCpe);
            $stmt->bindParam('cantidad', intval(1));
            $stmt->bindParam('pv_mensajeerror', $pv_mensajeerror);
            $stmt->execute();

            if(strlen(trim($pv_mensajeerror))>0)
            {
                $respuestaFinal[] = array("status"=>"NAF", "mensaje"=>"ERROR NAF: ".$pv_mensajeerror);
                return $respuestaFinal;
            }
        }
        else{
            $respuestaFinal[] = array('status'=>'NAF', 'mensaje'=>$codigoArticuloCpe);
            return $respuestaFinal;
        }

        //historial del servicio
        $servicioHistorial = new InfoServicioHistorial();
        $servicioHistorial->setServicioId($servicio);
        $servicioHistorial->setObservacion("Se Realizo un Cambio de Modem");
        $servicioHistorial->setEstado($servicio->getEstado());
        $servicioHistorial->setUsrCreacion($usrCreacion);
        $servicioHistorial->setFeCreacion(new \DateTime('now'));
        $servicioHistorial->setIpCreacion($ipCreacion);
        $this->emComercial->persist($servicioHistorial);
        $this->emComercial->flush();
        
        $status="OK";
        $mensaje = "OK";
        $respuestaFinal[] = array('status'=>$status, 'mensaje'=>$mensaje);
        return $respuestaFinal;
    }

    
    /**
     * Service que realiza el cambio del elemento del cliente, 
     * con ejecucion de scripts para la empresa Telconet
     *
     * @author Modificado: Juan Carlos Lafuente <jlafuente@telconet.ec>
     * @version 1.0 05-05-2016
     *
     * @author Modificado: Duval Medina C. <dmedina@telconet.ec>
     * @version 1.1 2016-06-28 Presentar el tipo de elemento que no esta soportado
     *
     * @author Modificado: Duval Medina C. <dmedina@telconet.ec>
     * @version 1.2 2016-07-04 Incluir Try catch para manejar Exception no contempladas
     * 
     * @author Modificado: Duval Medina C. <dmedina@telconet.ec>
     * @version 1.3 2016-09-08 Incluir elemento 'ROUTER' como permitido, siempre que este pertenezca a un Nodo Wifi
     *                         Guardar en base los errores no controlados
     * 
     * @author Duval Medina C. <dmedina@telconet.ec>
     * @version 1.4 2016-10-07 Incluir elemento 'CPE WIFI' como permitido, manejado como CPE
     * 
     * @author Modificado: John Vera <javera@telconet.ec> 
     * @version 1.5 07-11-2016 Se incluye el cambio de elemento para tipos RADIO
     * 
     * @author Allan Suárez C. <arsuarez@telconet.ec>
     * @version 1.6 2016-11-22 Se utiliza llamado a método encargado de cambio de elemento ROUTER cuando el tipo de equipo nuevo corresponda
     * 
     * @author Allan Suárez C. <arsuarez@telconet.ec>
     * @version 1.7 2016-11-23 Se modifica flujo para que soporte servicios de pseudope
     * 
     * @author Allan Suárez C. <arsuarez@telconet.ec>
     * @version 1.8 2017-02-07 Se modifica validaciones de pseudope para que pregunte si es pseudope S o N y no como booleano
     * 
     * @author John Vera R. <javera@telconet.ec>
     * @version 1.9 2017-04-13 Se modifico para que soporte el cambio de elemento wifi esquema 2 (nodo wifi cliente)
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.0 15-11-2017 Se modifica la validación que verifica si se tiene el mismo elemento cliente lo que significa que el cambio debe 
     *                         hacerse también al nodo
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 2.1 27-05-2021 - Se agrega el proceso para realizar la carga y descarga de los activos en el naf.
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 2.2 15-09-2021 - Se agrega validación para que no entre a descargar activos si es migracion NG Firewall.
     */
    public function cambioElementoTn($arrayParametros)
    {
        try
        {
            $strEsMigracion = $arrayParametros['esMigracionNgFirewall'];
            if ($strEsMigracion == "NO")
            {
                //Se realiza la carga y descarga de los Activos.
                $arrayResCarDes = $this->serviceInfoElemento->cargaDescargaActivosCambioEquipo($arrayParametros);
                if (!$arrayResCarDes['status'])
                {
                    $arrayResponseTn[] = array("status"  => "ERROR", "mensaje" => $arrayResCarDes['message']);
                    return $arrayResponseTn;
                }
                
                //verifico si el punto que se va a cambiar tiene un nodo wifi
                $objDetalleElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                           ->findOneBy(array('detalleNombre' => "ID_PUNTO",
                                                             'detalleValor'  => $arrayParametros['objServicio']->getPuntoId()->getId(),
                                                             'estado'        => 'Activo'));            
                if(is_object($objDetalleElemento))
                {
                    //verificar si el servicio que se va a cambiar tiene relacion con el servicio del nodo wifi 
                    //obtengo servicio wifi
                    $objServicioNav = $this->emInfraestructura->getRepository('schemaBundle:InfoServicio')
                                                              ->findOneBy(array('puntoId' => $arrayParametros['objServicio']->getPuntoId()->getId(),
                                                                                'descripcionPresentaFactura' => 'Concentrador L3MPLS Navegacion'));
                    if(is_object($objServicioNav))
                    {
                        $objServicioTecNav = $this->emInfraestructura->getRepository('schemaBundle:InfoServicioTecnico')
                                                                     ->findOneByServicioId($objServicioNav->getId());

                        $objServicioTecActual = $this->emInfraestructura->getRepository('schemaBundle:InfoServicioTecnico')
                                                                       ->findOneByServicioId($arrayParametros['objServicio']->getId());
                        //si tiene el mismo elemento cliente significa que el cambio debe hacerse tambien al nodo
                        if(is_object($objServicioTecActual) && is_object($objServicioTecNav)
                            && ($objServicioTecActual->getElementoClienteId() === $objServicioTecNav->getElementoClienteId() 
                                && $arrayParametros['tipoElementoNuevo'] == 'CPE'))
                        {
                            $arrayParametros['tipoElementoNuevo'] = "ROUTER";
                        }
                    }                
                }
            }
            
            if(isset($arrayParametros['strEsPseudoPe']) && $arrayParametros['strEsPseudoPe'] == 'S')
            {
                $arrayResponseTn = $this->cambioElementoPseudoPe($arrayParametros);
            }
            else if($arrayParametros['tipoElementoNuevo'] == "CPE" || $arrayParametros['tipoElementoNuevo'] == "CPE WIFI")
            {
                $arrayResponseTn = $this->cambioElementoCPE($arrayParametros);
            }
            else if($arrayParametros['tipoElementoNuevo'] == "TRANSCEIVER")
            {
                $arrayResponseTn = $this->cambioElementoTransceiver($arrayParametros);
            }
            else if($arrayParametros['tipoElementoNuevo'] == "RADIO")
            {
                $arrayResponseTn = $this->cambioElementoRadio($arrayParametros);
            }
            else if($arrayParametros['tipoElementoNuevo'] == "ROUTER")
            {

                if(is_object($objDetalleElemento))
                {
                    $arrayParametros['objDetalleElemento'] = $objDetalleElemento;
                    $arrayResponseTn = $this->cambioElementoRouter($arrayParametros);
                }
                else
                {
                    $arrayResponseTn[] = array('status'  => "ERROR",
                                               'mensaje' => "El Cambio de ROUTER solo es soportado si está en Nodo Wifi.");
                }
            }
            else
            {
                $arrayResponseTn[] = array('status'  => "ERROR",
                                           'mensaje' => "El tipo elemento '" . $arrayParametros['tipoElementoNuevo'] . "' no está soportado.");
            }
        }
        catch(\Exception $e)
        {
            $this->serviceUtil->insertError('Telcos+', 'cambioElementoTn', $e->getMessage(),
                                            $arrayParametros['usrCreacion'], $arrayParametros['ipCreacion']);            
            $arrayResponseTn[] = array('status'  => 'ERROR', 
                                       'mensaje' => 'Ocurrio un error. Por favor notificar a Sistemas.');
        }
        return $arrayResponseTn;
    }

    /**
     * Service que realiza el cambio del elemento del cliente, 
     * con ejecucion de scripts para la empresa Telconet
     *
     * @author Modificado: Juan Carlos Lafuente <jlafuente@telconet.ec>
     * @version 1.0 05-05-2016
     *
     * @author Modificado: Duval Medina C. <dmedina@telconet.ec>
     * @version 1.1 2016-06-28 Incluir Validaciones para lso casos en que los enlaces no existan
     *                         Consolidar formatos de return en los WS de Networking
     *                         Agregar información para rastreo de errores
     *                         Se cambia parámetro descripción para WS con formato adecuado
     *
     * @author Modificado: Duval Medina C. <dmedina@telconet.ec>
     * @version 1.2 2016-07-01 Incluir comprobación de la existencia de MAC para el elemento actual
     *                         Se valida la existencia de interface para el nuevo elemento
     *                         Se retorna los errores en la posicion 0 del arreglo para conservar formato
     * 
     * @author Modificado: Allan Suarez  <arsuarez@telconet.ec>
     * @version 1.3 29-07-2016 - Se ajusta para discriminar que puerto tomar para realizar el nuevo enlace segun el tipo de milla del servicio
     * 
     * @author Modificado: Allan Suarez  <arsuarez@telconet.ec>
     * @version 1.4 15-08-2016 - Se modifica para que solo obtenga la vlan relacionada al servicio a realizar el cambio de cpe mas no todas las vlans
     *                           del puerto del switch
     * 
     * @author Modificado: Duval Medina C.  <dmedina@telconet.ec>
     * @version 1.5 2016-08-17 - Se valida la existencia de Enlaces Internos para su  eliminación
     *                           y que exista InfoInterfaceElemento para UTP
     * 
     * @author Modificado: Allan Suarez  <arsuarez@telconet.ec>
     * @version 1.6 2016-09-13 - Se valida escenarios de servicios con Fibra migrados con informacion de CPE directo conectado al SW
     *                         - Se completa informacion de Roseta y Tx a servicios migrados Directo
     * 
     * @author Modificado: Jesus Bozada  <jbozada@telconet.ec>
     * @version 1.7 15-09-2016 - Se agregan parametros a metodo que registra finalización de solicitud de cambio de elemento
     * 
     * @author Modificado: Duval Medina C.  <dmedina@telconet.ec>
     * @version 1.8 2016-09-17 - Se obtiene el nombre de la Interfaz cuando el medio es UTP
     *                           Se modifica mensaje para cuando el Servicio no posee MAC
     *                           Cuando sea de tipo ROUTER, Ingresar estos Detalles: 'TIPO ELEMENTO RED' y CAPACIDAD
     *                           Agregar relación de contenencia entre el Nodo y el nueVo equipo ROUTER
     *                           Se duplian los enlaces internos para el nuevo equipo en caso sea Router
     * 
     * @author Duval Medina C.  <dmedina@telconet.ec>
     * @version 1.9 2016-10-04 - Se incluye la descripción de la Interfaz en la búsqueda, ya que en ella se está guardando el tipo de Interfaz
     *                              Y así distiguir entre las interfaces con el mismo nombre.
     *
     * @author Allan Suarez C.  <arsuarez@telconet.ec>
     * @version 2.0 2016-10-25 - Se modulariza función y se realiza alcance para que soporte el cambio de CPE cuando este tiene los dos puertos
     *                           wan ocupados y contiene mas de un servicio ligado
     * 
     * @author Allan Suarez C.  <arsuarez@telconet.ec>
     * @version 2.1 2017-07-26 - Se valida existencia de informacion de Servicio ligado a una interface WAN de un puerto para poder obtener
     *                           la data tecnica, tomando en cuanta que para servicios viejos esta informacion puede que no haya sido migrada
     *                           correctamente
     *
     * @author Richard Cabrera  <rcabrera@telconet.ec>
     * @version 2.2 2017-09-21 - Se realizan ajustes en la forma como se obtiene un servicio ligado a la interface wan del cpe, se obtienen solo los
     *                           servicios en estado: Activo,enPruebas e In-Corte
     *
     * @author Richard Cabrera  <rcabrera@telconet.ec>
     * @version 2.3 2018-03-16 - Se genera tracking del elemento
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.4 29-05-2018 - Se agrega el parametro del id_persona en el registro de la trazabilidad
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.5 28-08-2018 - Se realizan ajustes para validar que en el cambio de equipo de un servicio Port Channel o tenGiga,
     *                           se envié la interface correcta al WS de Networking al momento de activar la mac
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.6 25-09-2018 - Se realizan ajustes en la pantalla de cambio de cpe, se agrega la opción que registra la cuadrilla responsable
     *                           del retiro del equipo
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.7 03-10-2018 - Se realizan ajustes para agregar el concepto que el responsable del retiro de equipo puede ser una
     *                           cuadrilla o un empleado
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.8 05-04-2019 - Se realizan ajustes para agregar el concepto de reutilizar un equipo que ya esta instalado en otro servicio
     *                           (escenario SDWAN)
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.9 22-05-2019 - Se realizan ajustes para crear una tarea de retiro de equipo que sea asignada al responsable del retiro de equipo
     *
     * @author Modificado: Felix Caicedo <facaicedo@telconet.ec>
     * @version 3.0 29-11-2019 - Se realizan ajustes en el llamado de la configuración de la mac en los ws de networking
     * 
     * @author Modificado: Antonio Ayala <afayala@telconet.ec>
     * @version 3.1 25-08-2021 - Se realiza validación si el cambio de equipo es por Security Ng Firewall
     */
    public function cambioElementoCPE($arrayParametros)
    {
        $arrayServicios                 = array();
        $arraySwPuertoLogico            = array();
        $strPuertoSw                    = "";
        $intMotivoId                    = "";
        $strBandServCaract              = "N";
        $objElementoEquipoClienteNuevo  = null;
        $intIdPunto = "";
        $strlogin   = "";

        $objElementoClienteActual = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                            ->findOneById($arrayParametros['idElementoActual']);

        $this->serviceUtil->validaObjeto($objElementoClienteActual,'No se encuentra Elemento CPE/ROUTER a ser cambiado, por favor verificar');

        if(is_object($arrayParametros["objProducto"]) && is_object($arrayParametros["objServicio"]))
        {
            //*************Buscar si el servicio ya cuenta con la característica enviado*************//
            $arrayParametrosProdCaract["strCaracteristica"] = "SDWAN-CAMBIO_EQUIPO";
            $arrayParametrosProdCaract["objProducto"]       = $arrayParametros["objProducto"];
            $arrayParametrosProdCaract["objServicio"]       = $arrayParametros["objServicio"];

            $strBandServCaract = $this->serviceCliente->consultaServicioProdCaract($arrayParametrosProdCaract);
            //*************Buscar si el servicio ya cuenta con la característica enviado*************//
        }

        if($strBandServCaract === "N" && $arrayParametros['esMigracionNgFirewall'] === "NO")
        {
            // ===================================================================================================
            // REGISTRO DEL NUEVO ELEMENTO EN NAF Y TELCOS
            // ===================================================================================================

            $arrayParametros["strOrigen"] = "cambioEquipo";
            $arrayRequestElemento = $this->registrarElementoNuevoNAF($arrayParametros);

            if(!isset($arrayRequestElemento) || $arrayRequestElemento['status'] != 'OK')
            {
                $arrayResponseCPE[] = array('status'  => 'ERROR',
                                            'mensaje' => $arrayRequestElemento['mensaje']);
                return $arrayResponseCPE;
            }
            else
            {
                //Se obtiene el objeto que referencia al nuevo elemento creado
                $objElementoCpeNuevo = $arrayRequestElemento['objElementoCpe'];
            }
        }
        else if($strBandServCaract === "S" || $arrayParametros['esMigracionNgFirewall'] === "SI")
        {
            //Se consulta el elemento por la serie ya existente
            $objElementoEquipoClienteNuevo = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                     ->findOneBy(array('serieFisica' => $arrayParametros["serieElementoNuevo"],
                                                                                        'estado'     => 'Activo'));

            if(!is_object($objElementoEquipoClienteNuevo))
            {
                $strBusquedaSerieNueva         = strtoupper($arrayParametros["serieElementoNuevo"]);
                $objElementoEquipoClienteNuevo = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                         ->findOneBy(array('serieFisica' => $strBusquedaSerieNueva,
                                                                                           'estado'      => 'Activo'));
            }

            //Se obtiene el objeto que referencia al nuevo elemento creado
            $objElementoCpeNuevo = $objElementoEquipoClienteNuevo;
        }

        $this->serviceUtil->validaObjeto($objElementoCpeNuevo,'No pudo ser creado nuevo CPE, por favor revisar');

        // ===================================================================================================
        //  SE OBTIENE Y RECORRE LAS INTERFACES ES CONECTADAS DEL CPE ANTERIOR
        // ===================================================================================================
        
        $arrayParametrosInterfacesOcupadasCpe                  = array();
        $arrayParametrosInterfacesOcupadasCpe['intIdElemento'] = $arrayParametros['idElementoActual'];
               
        //Se obtiene las interfaces del cpe anterior que se encuentren conectadas wan1 , wan2 o ambas
        $arrayInterfacesOcupadasCpe = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                           ->getResultadoInterfacesElementoPorNombreInterface($arrayParametrosInterfacesOcupadasCpe);
        
        $arrayResultadoInterfaces   = $arrayInterfacesOcupadasCpe['resultado'];
        if( !isset($arrayResultadoInterfaces) || empty($arrayResultadoInterfaces) )
        {
            $arrayResponseCPE[] = array('status'  => 'ERROR', 
                                        'mensaje' => 'No se encontró la MAC del servicio, notificar a Sistemas.');
            return $arrayResponseCPE;
        }
                                  
        foreach($arrayResultadoInterfaces as $interfacesCpe)
        {
            $strMacPorInterface = $interfacesCpe['mac'];  //mac del puerto
            $strNombreInterface = $interfacesCpe['nombreInterfaceElemento'];
            $intIdInterface     = $interfacesCpe['idInterfaceElemento'];

            if ($arrayParametros['esMigracionNgFirewall'] === "SI")
            {
                $arrayEstados = array('Activo');
            }
            else
            {
                //Se obtiene un servicio ligado a la interface wan del cpe para obtener la informacion tecnica relacionada con cada puerto conectado
                $arrayEstados = array('EnPruebas','Activo','In-Corte');
            }
            
            $arrayParametrosInterface["arrayEstados"]           = $arrayEstados;
            $arrayParametrosInterface["strDetalleNombre"]       = 'servicio';
            $arrayParametrosInterface["intInterfaceElementoId"] = $intIdInterface;

            $arrayServicios = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleInterface')
                                                      ->getServiciosPorInterfaceElemento($arrayParametrosInterface);

            $intIdServicio = null;
            
            if(!empty($arrayServicios[0]["idServicio"]))
            {
                $intIdServicio = $arrayServicios[0]["idServicio"];
            }

            if($intIdServicio)
            {
                $objServicio        = $this->emInfraestructura->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);

                $this->serviceUtil->validaObjeto($objServicio,'No se encuentra información del Servicio, por favor revisar');

                //Informacion tecnica ligada a cada interface wan
                $objServicioTecnico = $this->emInfraestructura->getRepository('schemaBundle:InfoServicioTecnico')
                                                              ->findOneByServicioId($intIdServicio);

                $this->serviceUtil->validaObjeto($objElementoClienteActual,'No se encuentra Información Técnica de Servicio, por favor revisar');

                if($arrayParametros['strRegistroEquipos'] != "S")
                {
                    $objUltimaMilla     = $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')
                                                                  ->find($objServicioTecnico->getUltimaMillaId());

                    $this->serviceUtil->validaObjeto($objUltimaMilla,'No se encuentra información Ultima Milla, por favor revisar');

                    $strUltimaMilla     = $objUltimaMilla->getNombreTipoMedio();

                    $boolEsFibraRuta = false;

                    //Obtener la caracteristica TIPO_FACTIBILIDAD para discriminar que sea FIBRA DIRECTA o RUTA
                    $objServProdCaractTipoFact = $this->servicioGeneral
                                                      ->getServicioProductoCaracteristica($objServicio,'TIPO_FACTIBILIDAD',$objServicio->getProductoId());

                    if(is_object($objServProdCaractTipoFact))
                    {
                        if($objServProdCaractTipoFact->getValor() == "RUTA")
                        {
                            $boolEsFibraRuta = true;
                        }
                    }
                    else
                    {
                        if($strUltimaMilla == "Fibra Optica")
                        {
                            //Si contiene informacion de GIS y no tiene caracteristica
                            if($objServicioTecnico->getInterfaceElementoConectorId())
                            {
                                $boolEsFibraRuta = true;
                            }
                        }
                    }

                    //Se obtiene la información del objeto interface mas próximo enlazado con el cpe a ser cambiado
                    $arrayParametrosPuertoOutVecino['objServicioTecnico'] = $objServicioTecnico;
                    $arrayParametrosPuertoOutVecino['objUltimaMilla']     = $objUltimaMilla;
                    $arrayParametrosPuertoOutVecino['boolEsFibraRuta']    = $boolEsFibraRuta;
                    $arrayInformacionPuertoOutVecino                      = $this->getArrayInformacionPuertoOutVecinoCpe($arrayParametrosPuertoOutVecino);

                    if($arrayInformacionPuertoOutVecino['status'] == 'ERROR')
                    {
                        $arrayResponseCPE[] = array('status'  => 'ERROR',
                                                    'mensaje' => $arrayInformacionPuertoOutVecino['mensaje']);
                        return $arrayResponseCPE;
                    }

                    $objInterfaceTransciverOUT = $arrayInformacionPuertoOutVecino['objInterfaceOutVecinoCpe'];
                    $boolEsMigrado             = $arrayInformacionPuertoOutVecino['boolEsMigrado'];
                
                    // ===================================================================================================
                    //                      ENLAZAR CPE CON LA INTERFACE MAS PROXIMA ( TX, SW, RADIO )
                    // ===================================================================================================

                    $boolEsRegulado       = false;
                    $objInterfaceOut      = $objInterfaceTransciverOUT;

                    //Crear informacion de Tx y Roseta para servicio Fibra que no tengan informacion de Backbone completa
                    if($strUltimaMilla == 'Fibra Optica')
                    {
                        if($objServicioTecnico->getElementoClienteId())
                        {
                            $objElementoCliente = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                          ->find($objServicioTecnico->getElementoClienteId());

                            if($arrayParametros['tipoElementoNuevo'] == 'CPE')
                            {
                                //Sólo si el elemento cliente para FO no es ROSETA crear correctamente la referencia con la Ros y el Tx
                                if(is_object($objElementoCliente) &&
                                   $objElementoCliente->getModeloElementoId()->getTipoElementoId()->getNombreTipoElemento() != 'ROSETA')
                                {
                                    $arrayParametrosCrearElementosFaltantes['objInterfaceOUT']   =  $objInterfaceTransciverOUT;
                                    $arrayParametrosCrearElementosFaltantes['objServicio']       =  $objServicio;
                                    $arrayParametrosCrearElementosFaltantes['objUltimaMilla']    =  $objUltimaMilla;
                                    $arrayParametrosCrearElementosFaltantes['boolEsMigrado']     =  $boolEsMigrado;
                                    $arrayParametrosCrearElementosFaltantes['tipoElementoNuevo'] =  $arrayParametros['tipoElementoNuevo'];
                                    $arrayParametrosCrearElementosFaltantes['usrCreacion']       =  $arrayParametros['usrCreacion'];
                                    $arrayParametrosCrearElementosFaltantes['ipCreacion']        =  $arrayParametros['ipCreacion'];
                                    $arrayParametrosCrearElementosFaltantes['idEmpresa']         =  $arrayParametros['idEmpresa'];

                                    $arrayRespuestaCrearElementos = $this->crearElementoRosetaTxFaltantes($arrayParametrosCrearElementosFaltantes);

                                    $objInterfaceConector = $arrayRespuestaCrearElementos['objInterfaceElementoClienteConector'];//Roseta
                                    $objInterfaceOut      = $arrayRespuestaCrearElementos['objInterfaceElementoOut'];            //Tx
                                    $boolEsRegulado       = $arrayRespuestaCrearElementos['boolEsRegulado'];
                                }
                            }
                            else if($arrayParametros['tipoElementoNuevo'] == 'CPE WIFI')
                            {
                                $boolEsRegulado = true;
                            }
                        }
                    }
                }

                //Crear el enlace entre CPE Nuevo e informacion de bb existente
                //Interface del CPE
                $objInterfaceElementoNuevo = $this->emInfraestructura
                                                  ->getRepository('schemaBundle:InfoInterfaceElemento')
                                                  ->findOneBy(array("elementoId"                   => $objElementoCpeNuevo->getId(),
                                                                    "nombreInterfaceElemento"      => $arrayParametros['interfacesConectadas'] == 1 ?
                                                                                                      $arrayParametros['nombreInterface']:
                                                                                                      $strNombreInterface,
                                                                    "descripcionInterfaceElemento" => "Wan"));

                $this->serviceUtil->validaObjeto($objInterfaceElementoNuevo,'No se encuentra Información de la Interface Wan');

                if($arrayParametros['strRegistroEquipos'] != "S")
                {
                    $objEnlaceCpeNuevoVecinoOut = new InfoEnlace();
                    $objEnlaceCpeNuevoVecinoOut->setInterfaceElementoIniId($objInterfaceOut);
                    $objEnlaceCpeNuevoVecinoOut->setInterfaceElementoFinId($objInterfaceElementoNuevo);
                    $objEnlaceCpeNuevoVecinoOut->setTipoMedioId($objUltimaMilla);
                    $objEnlaceCpeNuevoVecinoOut->setTipoEnlace($objServicioTecnico->getTipoEnlace());
                    $objEnlaceCpeNuevoVecinoOut->setEstado("Activo");
                    $objEnlaceCpeNuevoVecinoOut->setUsrCreacion($arrayParametros['usrCreacion']);
                    $objEnlaceCpeNuevoVecinoOut->setFeCreacion(new \DateTime('now'));
                    $objEnlaceCpeNuevoVecinoOut->setIpCreacion($arrayParametros['ipCreacion']);
                    $this->emInfraestructura->persist($objEnlaceCpeNuevoVecinoOut);

                    $objInterfaceElementoNuevo->setEstado("connected");
                    $this->emInfraestructura->persist($objInterfaceElementoNuevo);

                }

                $arrayVlanMacActual   = array();
                $arrayVlanMacAnterior = array();

                //Recorremos todos los servicios ligados a cada interface wan
                //Se realizará por cada servicio:
                // - Obtención de información de vlan-mac por cada servicio para generar el arreglo a ser enviado al WebService de NW
                // - Actualizar información tecnica por cada servicio
                // - Actualizar tipo de factibilidad
                // - Ingresar informacion de detalle de interface para cada servicio                    
                foreach($arrayServicios as $arrayIdxServicios)
                {
                    $intIdServicio = $arrayIdxServicios["idServicio"];

                    if($intIdServicio)
                    {
                        $objServicioPorPuerto = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);

                        $this->serviceUtil->validaObjeto($objServicioPorPuerto,'No se encuentra información del Servicio');

                        $objServicioTecnicoServicioPuerto = $this->emInfraestructura->getRepository('schemaBundle:InfoServicioTecnico')
                                                                                    ->findOneByServicioId($objServicioPorPuerto->getId());

                        $this->serviceUtil->validaObjeto($objServicioTecnicoServicioPuerto,'No se encuentra Información Técnica de Servicio');

                        $objProductoServicioPuerto = $objServicioPorPuerto->getProductoId();

                        $objSolCaracVlan  = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioPorPuerto,
                                                                                                      "VLAN", 
                                                                                                      $objProductoServicioPuerto);    
                        
                        //Si es fibra optica  y es regulado ( creacion Roseta y Tx ) se actualiza informacion tecnica
                        if($strUltimaMilla == 'Fibra Optica' && $boolEsRegulado)
                        {
                            $objServicioTecnicoServicioPuerto->setElementoClienteId($objInterfaceConector->getElementoId()->getId()); //Roseta
                            $objServicioTecnicoServicioPuerto->setInterfaceElementoClienteId($objInterfaceConector->getId()); //Roseta OUT
                            $this->emInfraestructura->persist($objServicioTecnicoServicioPuerto);
                            $this->emInfraestructura->flush();
                        }
                        else if($strUltimaMilla == 'UTP' || $arrayParametros['strRegistroEquipos'] === "S" )
                        {
                            $objServicioTecnicoServicioPuerto->setElementoClienteId($objElementoCpeNuevo->getId());
                            $objServicioTecnicoServicioPuerto->setInterfaceElementoClienteId($objInterfaceElementoNuevo->getId());
                            $this->emInfraestructura->persist($objServicioTecnicoServicioPuerto);
                            $this->emInfraestructura->flush();
                        }

                        // ===================================================================================================
                        //                                 SE LOCALIZA LA VLAN DEL CLIENTE 
                        // ===================================================================================================

                        $strVlan          = null;

                        if(is_object($objSolCaracVlan) && is_object($objProductoServicioPuerto))
                        {        
                            if($objProductoServicioPuerto->getNombreTecnico()=="L3MPLS" || 
                                $objProductoServicioPuerto->getNombreTecnico()=="L3MPLS SDWAN")
                            {  
                                $objPerEmpRolCarVlan = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                                         ->find($objSolCaracVlan->getValor());

                                if(is_object($objPerEmpRolCarVlan))
                                {
                                    $objDetalleElementoVlan = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                                      ->find($objPerEmpRolCarVlan->getValor());
                                }
                            }
                            else
                            {
                                $objDetalleElementoVlan = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                                  ->find($objSolCaracVlan->getValor());
                            }

                            if(is_object($objDetalleElementoVlan))
                            {
                                $strVlan = $objDetalleElementoVlan->getDetalleValor();
                            }
                            else
                            {
                                $strVlan = 1;
                            }
                        }

                        if(!$strVlan && $arrayParametros['strRegistroEquipos'] != "S")
                        {
                            $arrayRespuestaError[] = array('status' => "ERROR",
                                                           'mensaje' => 'No se encontró la Vlan del Servicio '.
                                                                        $objServicioPorPuerto->getLoginAux());
                            return $arrayRespuestaError;
                        }   

                        $strMacActual = null;

                        //Se obtiene la nueva MAC que se requiere para configurar el SW para el cambio de equipo
                        if($strNombreInterface == 'wan1')
                        {                            
                            if($arrayParametros['nombreInterface'] == 'wan1')
                            {
                                $strMacActual = $arrayParametros['macElementoNuevo'];
                            }
                            else
                            {
                                $strMacActual = $arrayParametros['macCpeBck'];
                            }                            
                        }
                        else
                        {
                            if(isset($arrayParametros['macCpeBck']) && $arrayParametros['macCpeBck'])
                            {                               
                                if($arrayParametros['nombreInterface'] == 'wan2')
                                {
                                    $strMacActual = $arrayParametros['macElementoNuevo'];
                                }
                                else
                                {
                                    $strMacActual = $arrayParametros['macCpeBck'];
                                }                                
                            }
                            else
                            {
                                $strMacActual = $arrayParametros['macElementoNuevo'];
                            }                                
                        }
                        
                        //Si solo existe un puerto conectado wan1 o wan2 tomara el valor que viene en el campo definido como principal
                        if($arrayParametros['interfacesConectadas'] == 1)
                        {
                            $strMacActual = $arrayParametros['macElementoNuevo'];
                        }
                                        
                        //Arreglo de la MAC Anterior con la VLAN de cada Servicio
                        if(array_key_exists($strVlan, $arrayVlanMacAnterior)) 
                        {
                            $arrayMAcs                      = $arrayVlanMacAnterior[$strVlan];
                            $arrayMAcs[]                    = $strMacPorInterface;
                            $arrayVlanMacAnterior[$strVlan] = $arrayMAcs;
                        }
                        else
                        {
                            $arrayVlanMacAnterior[$strVlan] = array($strMacPorInterface);
                        }
                        
                        //Arreglo de la MAC Anterior con la VLAN de cada Servicio
                        if(array_key_exists($strVlan, $arrayVlanMacActual)) 
                        {
                            $arrayMAcs                      = $arrayVlanMacActual[$strVlan];
                            $arrayMAcs[]                    = $strMacActual;
                            $arrayVlanMacActual[$strVlan]   = $arrayMAcs;
                        }
                        else
                        {
                            $arrayVlanMacActual[$strVlan] = array($strMacActual);
                        }
                        
                        //Se crea la carateristica DIRECTO para regularizar en caso de no existir ( Fibra Optica regularizado )
                        $objServProdCaractTipoFact = $this->servicioGeneral
                                                          ->getServicioProductoCaracteristica($objServicioPorPuerto,
                                                                                             'TIPO_FACTIBILIDAD',
                                                                                             $objServicioPorPuerto->getProductoId());
                        if(!is_object($objServProdCaractTipoFact))
                        {
                            $objCaracteristica = $this->emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                                                   ->findOneByDescripcionCaracteristica("TIPO_FACTIBILIDAD");

                            if(is_object($objCaracteristica))
                            {
                                $objProdCaract  = $this->emComercial->getRepository("schemaBundle:AdmiProductoCaracteristica")
                                                       ->findOneBy(array('caracteristicaId'  => $objCaracteristica->getId(),
                                                                         'productoId'        => $objServicioPorPuerto->getProductoId()->getId()));

                                if(is_object($objProdCaract))
                                {                        
                                    $objInfoServicioProdCaract = new InfoServicioProdCaract();
                                    $objInfoServicioProdCaract->setServicioId($objServicioPorPuerto->getId()); 
                                    $objInfoServicioProdCaract->setProductoCaracterisiticaId($objProdCaract->getId());
                                    $objInfoServicioProdCaract->setValor("DIRECTO");
                                    $objInfoServicioProdCaract->setEstado("Activo");
                                    $objInfoServicioProdCaract->setFeCreacion(new \DateTime('now'));
                                    $objInfoServicioProdCaract->setUsrCreacion($arrayParametros['usrCreacion']);
                                    $this->emComercial->persist($objInfoServicioProdCaract);
                                    $this->emComercial->flush();
                                }
                            }
                        }   
                        
                        //Guardar la MAC del CPE atada a la interface del equipo                           
                        $objInterfaceElementoNuevo->setMacInterfaceElemento($strMacActual);
                        $objInterfaceElementoNuevo->setEstado('connected');
                        $this->emInfraestructura->persist($objInterfaceElementoNuevo);
                        $this->emInfraestructura->flush();                                                

                        //Se guarda referencia en la detalle interface del servicio relacionado a este puerto y mac
                        $objInfoDetalleInterface = new InfoDetalleInterface();
                        $objInfoDetalleInterface->setInterfaceElementoId($objInterfaceElementoNuevo);
                        $objInfoDetalleInterface->setDetalleNombre("servicio");
                        $objInfoDetalleInterface->setDetalleValor($objServicioPorPuerto->getId());
                        $objInfoDetalleInterface->setUsrCreacion($arrayParametros['usrCreacion']);
                        $objInfoDetalleInterface->setFeCreacion(new \DateTime('now'));
                        $objInfoDetalleInterface->setIpCreacion($arrayParametros['ipCreacion']);
                        $this->emInfraestructura->persist($objInfoDetalleInterface);
                        $this->emInfraestructura->flush();
                        
                        //Historial en cada servicio perteneciente al CPE a ser cambiado
                        $strHistorialPorServicio = "<b>Se realizo un Cambio de Elemento Cliente:</b><br>"
                                                 . "<b style='color:red'>CPE Anterior : </b><br>"
                                                 . "<b>Nombre CPE : </b> ".$objElementoClienteActual->getNombreElemento()."<br>"
                                                 . "<b>Serie CPE  : </b> ".$objElementoClienteActual->getSerieFisica()."<br>"
                                                 . "<b>Modelo CPE : </b> ".$objElementoClienteActual->getModeloElementoId()
                                                                                                    ->getNombreModeloElemento()."<br>"
                                                 . "<b style='color:red'>CPE Actual : </b><br>"
                                                 . "<b>Nombre CPE : </b> ".$objElementoCpeNuevo->getNombreElemento()."<br>"
                                                 . "<b>Serie  CPE : </b> ".$objElementoCpeNuevo->getSerieFisica()."<br>"
                                                 . "<b>Modelo CPE : </b> ".$objElementoCpeNuevo->getModeloElementoId()
                                                                                               ->getNombreModeloElemento()."<br>";
                        
                        $objServicioHistorial = new InfoServicioHistorial();
                        $objServicioHistorial->setServicioId($objServicioPorPuerto);
                        $objServicioHistorial->setObservacion($strHistorialPorServicio);
                        $objServicioHistorial->setEstado($arrayParametros['objServicio']->getEstado());
                        $objServicioHistorial->setUsrCreacion($arrayParametros['usrCreacion']);
                        $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                        $objServicioHistorial->setIpCreacion($arrayParametros['ipCreacion']);
                        $this->emComercial->persist($objServicioHistorial);
                        $this->emComercial->flush();

                        //Se elimina la caracteristica SDWAN-CAMBIO_EQUIPO
                        if(is_object($objServicioPorPuerto->getProductoId()) && is_object($objServicioPorPuerto))
                        {
                            //****Buscar si el servicio cuenta con la característica SDWAN-CAMBIO_EQUIPO, de ser asi se procede a eliminarla*****//
                            $objAdmiCaracteristica = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                       ->findOneBy(array("descripcionCaracteristica" => 'SDWAN-CAMBIO_EQUIPO',
                                                                                         "estado"                    => "Activo"));

                            if(is_object($objAdmiCaracteristica))
                            {
                                $objAdmiProductoCaract = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                              ->findOneBy(array("caracteristicaId" => $objAdmiCaracteristica->getId(),
                                                                                "productoId"       => $objServicioPorPuerto->getProductoId()
                                                                                                                           ->getId()));

                                if(is_object($objAdmiProductoCaract))
                                {
                                    $objServProdCaractSdwan = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                   ->findOneBy(array("servicioId"                => $objServicioPorPuerto->getId(),
                                                                                     "productoCaracterisiticaId" => $objAdmiProductoCaract->getId(),
                                                                                     "estado"                    => "Activo"));

                                    if(is_object($objServProdCaractSdwan))
                                    {
                                        $objServProdCaractSdwan->setEstado("Eliminado");
                                        $this->emComercial->persist($objServProdCaractSdwan);
                                        $this->emComercial->flush();

                                        //Se registra historial del servicio
                                        $objServicioHistorial = new InfoServicioHistorial();
                                        $objServicioHistorial->setServicioId($objServicioPorPuerto);
                                        $objServicioHistorial->setObservacion("Luego de ejecutar el cambio de CPE se elimina automáticamente la"
                                                                            . "característica <br> que permite reutilizar un equipo ya instalado");
                                        $objServicioHistorial->setEstado($objServicioPorPuerto->getEstado());
                                        $objServicioHistorial->setUsrCreacion($arrayParametros['usrCreacion']);
                                        $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                                        $objServicioHistorial->setIpCreacion($arrayParametros['ipCreacion']);
                                        $this->emComercial->persist($objServicioHistorial);
                                        $this->emComercial->flush();
                                    }
                                }
                            }
                            //*******Buscar si el servicio cuenta con la característica SDWAN-CAMBIO_EQUIPO, de ser asi se procede a eliminarla***//
                        }
                    }
                }

                if($arrayParametros['strRegistroEquipos'] === "S")
                {
                    //Se asocia la mac actual al nuevo equipo
                    $objDetalleElementoMac = new InfoDetalleElemento();
                    $objDetalleElementoMac->setElementoId($objElementoCpeNuevo->getId());
                    $objDetalleElementoMac->setDetalleNombre("MAC");
                    $objDetalleElementoMac->setDetalleValor($arrayParametros["macElementoNuevo"]);
                    $objDetalleElementoMac->setDetalleDescripcion("Mac del equipo del cliente");
                    $objDetalleElementoMac->setFeCreacion(new \DateTime('now'));
                    $objDetalleElementoMac->setUsrCreacion($arrayParametros['usrCreacion']);
                    $objDetalleElementoMac->setIpCreacion($arrayParametros['ipCreacion']);
                    $objDetalleElementoMac->setEstado('Activo');
                    $this->emInfraestructura->persist($objDetalleElementoMac);
                    $this->emInfraestructura->flush();
                }

                if($arrayParametros['strRegistroEquipos'] != "S")
                {
                    //Se realiza configuración en WebService ( cambio_cpe )

                    $objProducto          = $objServicio->getProductoId();
                    $objElemento          = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                    ->find($objServicioTecnico->getElementoId());
                    $objInterfaceElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                    ->find($objServicioTecnico->getInterfaceElementoId());
                    // ....
                    $objDetEleAnillo      = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                    ->findOneBy(array("elementoId"       => $objElemento->getId(),
                                                                                      "detalleNombre"    => "ANILLO",
                                                                                      "estado"           => "Activo"));

                    $this->serviceUtil->validaObjeto($objDetEleAnillo,'No se encuentra Información de Anillo del Servicio');
                    $this->serviceUtil->validaObjeto($objElemento,'No se encuentra Información de Switch');
                    $this->serviceUtil->validaObjeto($objInterfaceElemento,'No se encuentra Información Puerto de Switch');
                    $this->serviceUtil->validaObjeto($objProducto,'No se encuentra Información de Producto ligado al Servicio');

                    $loginAux             = $objServicio->getLoginAux();
                    $strDescripcionUm     = $objUltimaMilla->getNombreTipoMedio()== "Fibra Optica"?'_fib':'_rad';

                    // ===================================================================================================
                    // CALL WS "NetworkingScriptsService" >>> SE CANCELA LA MAC DEL ELEMENTO ACTUAL
                    // ===================================================================================================
                    //*******Se valida si el servicio selecionado esta en un Port Channel o tenGiga******//
                    $arrayParametrosLogico["intServicio"] = $arrayParametros['objServicio']->getId();

                    $arraySwPuertoLogico = $this->emInfraestructura->getRepository('schemaBundle:InfoServicio')
                                                ->getPuertoLogicoSwPorServicio($arrayParametrosLogico);

                    $strPuertoSw = $objInterfaceElemento->getNombreInterfaceElemento();
                    if(count($arraySwPuertoLogico) > 0)
                    {
                        $strPuertoSw = $arraySwPuertoLogico["strNombreInterfaceElemento"];
                    }
                    else
                    {
                        $strPuertoSw = $objInterfaceElemento->getNombreInterfaceElemento();
                    }
                    
                    // ===================================================================================================
                    // CALL WS "NetworkingScriptsService" >>> CAMBIO DE MAC DEL ELEMENTO
                    // ===================================================================================================
                    
                    $arrayRequestWS                 = array();
                    $arrayRequestWS['url']          = 'cambio_cpe';
                    $arrayRequestWS['accion']       = 'cambio_cpe';
                    $arrayRequestWS['sw']           = $objElemento->getNombreElemento();
                    $arrayRequestWS['pto']          = $strPuertoSw;
                    $arrayRequestWS['anillo']       = $objDetEleAnillo->getDetalleValor();
                    $arrayRequestWS['descripcion']  = 'cce_'.$loginAux.$strDescripcionUm;
                    $arrayRequestWS['servicio']     = $objProducto->getNombreTecnico();
                    $arrayRequestWS['login_aux']    = $loginAux;
                    $arrayRequestWS['user_name']    = $arrayParametros['usrCreacion'];
                    $arrayRequestWS['user_ip']      = $arrayParametros['ipCreacion'];
                    
                    $arrayRequestWS['cpe_anterior'] = array();
                    foreach( $arrayVlanMacAnterior as $strValueVlanCPE => $arrayValueMacCPE )
                    {
                        foreach( $arrayValueMacCPE as $strValueMacCPE )
                        {
                            $arrayRequestWS['cpe_anterior'][] = array(
                                        'vlan' => $strValueVlanCPE,
                                        'mac'  => $strValueMacCPE,
                                    );
                        }
                    }
                    
                    $arrayRequestWS['cpe_nuevo']    = array();
                    foreach( $arrayVlanMacActual as $strValueVlanCPE => $arrayValueMacCPE )
                    {
                        foreach( $arrayValueMacCPE as $strValueMacCPE )
                        {
                            $arrayRequestWS['cpe_nuevo'][] = array(
                                        'vlan' => $strValueVlanCPE,
                                        'mac'  => $strValueMacCPE,
                                    );
                        }
                    }
                    
                    //ejecución del método vía WS para realizar la configuración del SW
                    $arrayRespuesta = $this->networkingScripts->callNetworkingWebService($arrayRequestWS);
                    if($arrayRespuesta['status'] != 'OK')
                    {
                        $arrayResponseCPE[]        = $arrayRespuesta;
                        return $arrayResponseCPE;
                    }

                    // ===================================================================================================
                    // ELIMINAMOS LOS ENLACES CON EL ELEMENTO ACTUAL
                    // ===================================================================================================

                    if($boolEsRegulado)//Si se creo roseta nueva, se elimina enlace entre SW y puerto del CPE
                    {
                        $objEnlace    = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                                ->findOneBy(array('interfaceElementoIniId' => $objInterfaceTransciverOUT->getId(),//SW
                                                                               'interfaceElementoFinId'    => $intIdInterface,//CPE
                                                                               'estado'                    => 'Activo'));
                        if(is_object($objEnlace))
                        {
                            $objEnlace->setEstado('Eliminado');
                            $this->emInfraestructura->persist($objEnlace);
                            $this->emInfraestructura->flush();
                        }
                    }
                    else //Flujos normales
                    {
                        $arrayEnlaces = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                                ->findBy(array('interfaceElementoIniId' => $objInterfaceTransciverOUT->getId(),
                                                                               'estado'                 => 'Activo'));
                        foreach ($arrayEnlaces as $objEnlace)
                        {
                            //Se elimina el enlace anterior
                            //Si el la interface nuevo es igual al del nuevo cpe no elimina el enlace
                            if($objEnlace->getInterfaceElementoFinId()->getElementoId()->getId() != $objElementoCpeNuevo->getId())
                            {
                                $objEnlace->setEstado('Eliminado');
                                $this->emInfraestructura->persist($objEnlace);
                                $this->emInfraestructura->flush();
                            }
                        }
                    }
                }
            }        
        }

        if($arrayParametros['strRegistroEquipos'] != "S")
        {
            // ===================================================================================================
            // SE TRANSFIEREN LOS DETALLES ELEMENTO ACTUAL AL NUEVO Y SE ACTUALIZA LA MAC
            // ===================================================================================================
            $arrayParametros['objElementoClienteActual'] = $objElementoClienteActual;
            $arrayParametros['objElementoCpeNuevo']      = $objElementoCpeNuevo;

            $this->traspasarInformacionTecnicaElemento($arrayParametros);
        }
        // ===================================================================================================
        // ELIMINAMOS LOS PUERTOS DEL ELEMENTO ACTUAL
        // ===================================================================================================

        $interfacesElementoActual = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                            ->getInterfacesByIdElemento($objElementoClienteActual->getId());
        foreach ($interfacesElementoActual as $objInterface) 
        {
            if($arrayParametros['strRegistroEquipos'] != "S")
            {
                $objEnlaceInterno = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                            ->findOneBy(array('interfaceElementoIniId' =>$objInterface->getId(),
                                                                              'estado'                 =>'Activo'));
                if(is_object($objEnlaceInterno))
                {
                    if(strpos($objInterface->getNombreInterfaceElemento(), "IN") !== FALSE )
                    {
                        // ELIMINAMOS ENLACES INTERNOS
                        $objEnlaceInterno->setEstado('Eliminado');
                        $this->emInfraestructura->persist($objEnlaceInterno);
                        $this->emInfraestructura->flush();
                    }
                }
            }

            $objInterface->setEstado('Eliminado');
            $this->emInfraestructura->persist($objInterface);
            $this->emInfraestructura->flush();  
        }
        // ===================================================================================================
        // ELIMINAMOS EL ELEMENTO ACTUAL
        // ===================================================================================================
        $objElementoClienteActual->setEstado('Eliminado');
        $this->emInfraestructura->persist($objElementoClienteActual);
        $this->emInfraestructura->flush();

        //SE REGISTRA EL TRACKING DEL ELEMENTO
        $arrayParametrosAuditoria["strNumeroSerie"]  = $objElementoClienteActual->getSerieFisica();
        $arrayParametrosAuditoria["strEstadoTelcos"] = 'Eliminado';
        $arrayParametrosAuditoria["strEstadoNaf"]    = 'Instalado';
        $arrayParametrosAuditoria["strEstadoActivo"] = 'CambioEquipo';
        $arrayParametrosAuditoria["strUbicacion"]    = 'EnTransito';
        $arrayParametrosAuditoria["strCodEmpresa"]   = "10";
        $arrayParametrosAuditoria["strTransaccion"]  = 'Cambio de Elemento';
        $arrayParametrosAuditoria["intOficinaId"]    = 0;

        //Se consulta el login del cliente
        if(is_object($arrayParametros['objServicio']))
        {
            $objInfoPunto = $this->emInfraestructura->getRepository('schemaBundle:InfoPunto')
                                                    ->find($arrayParametros['objServicio']->getPuntoId()->getId());
            if(is_object($objInfoPunto))
            {
                $arrayParametrosAuditoria["strLogin"] = $objInfoPunto->getLogin();

                $intIdPunto = $objInfoPunto->getId();
                $strlogin   = $arrayParametrosAuditoria["strLogin"];
            }
        }

        $arrayParametrosAuditoria["strUsrCreacion"] = $arrayParametros['usrCreacion'];

        // ===================================================================================================
        // CANCELAR SOLICITUD DE CAMBIO DE EQUIPO
        // ===================================================================================================
        $arrayParams = array();
        $arrayParams['objServicio']         = $arrayParametros['objServicio'];
        $arrayParams['strTipoSolicitud']    = "SOLICITUD CAMBIO DE MODEM INMEDIATO";
        $arrayParams['strEstadoSolicitud']  = "AsignadoTarea";
        $arrayParams['strUsrCreacion']      = $arrayParametros['usrCreacion'];
        $arrayParams['strIpCreacion']       = $arrayParametros['ipCreacion'];
        // ...
        if ($arrayParametros['esMigracionNgFirewall'] === "NO")
        {
            $arrayResponse = $this->servicioGeneral->finalizarDetalleSolicitud($arrayParams);
        }
        else
        {
            $arrayResponse['status'] = 'OK';
        }
        
        
        if($arrayResponse['status'] == 'OK')
        {
            $arrayParams = array();
            
            if ($arrayParametros['esMigracionNgFirewall'] === "NO")
            {
                // SE LOCALIZA LA ASIGACION DE LA SOLICITUD DE CAMBIO DE EQUIPO PARA REASIGNAR A LA MISMA PERSONA EL RETIRO DE EQUIPO
                $objDetalle = $this->emComercial->getRepository('schemaBundle:InfoDetalle')
                                                ->findOneBy(array("detalleSolicitudId" => $arrayResponse['objDetalleSolicitud']->getId()));
                $objDetalleAsignacion = $this->emComercial->getRepository('schemaBundle:InfoDetalleAsignacion')
                                                          ->findOneBy(array("detalleId" => $objDetalle->getId()));
                $arrayParams['idPersonaEmpresaRol'] = $objDetalleAsignacion->getPersonaEmpresaRolId();
            }
            else
            {
                $arrayParams['idPersonaEmpresaRol'] = $arrayParametros['idPersonaEmpresaRol'];
            }
                        
            // ...
            // ===================================================================================================
            // CREAR SOLICITUD DE RETIRO DE EQUIPO POR CAMBIO DE EQUIPO
            // ===================================================================================================
            
            $arrayParams['objServicio']         = $arrayParametros['objServicio'];
            $arrayParams['objElementoCliente']  = $objElementoClienteActual;

            if ($arrayParametros['esMigracionNgFirewall'] === "NO")
            {
                $arrayParams['strMotivoSolicitud']  ='CAMBIO DE EQUIPO';
            }
            else
            {
                $arrayParams['strMotivoSolicitud']  ='MIGRACIÓN EQUIPO NG FIREWALL';
            }
            
            $arrayParams['usrCreacion']         = $arrayParametros['usrCreacion'];
            $arrayParams['ipCreacion']          = $arrayParametros['ipCreacion'];
            // ...

            $objPersonaEmpresaRolUsr = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                         ->find($arrayParams['idPersonaEmpresaRol']);
   
            if(!empty($arrayParametros["idResponsable"]) && isset($arrayParametros["idResponsable"]) && !empty($arrayParametros["tipoResponsable"]))
            {
                if(is_object($arrayResponse['objDetalleSolicitud']))
                {
                     $intMotivoId = $arrayResponse['objDetalleSolicitud']->getMotivoId();

                     if(!empty($intMotivoId))
                     {
                          $objAdmiMotivo = $this->emComercial->getRepository('schemaBundle:AdmiMotivo')->find($intMotivoId);

                          if(is_object($objAdmiMotivo))
                          {
                              $arrayParametrosAuditoria["strObservacion"] = $objAdmiMotivo->getNombreMotivo();
                          }
                     }
                }

                if($arrayParametros["tipoResponsable"] == "C" )
                {
                    $arrayDatos = $this->emComercial->getRepository('schemaBundle:AdmiCuadrilla')
                                                    ->findJefeCuadrilla($arrayParametros["idResponsable"]);

                    if(!empty($arrayDatos) && isset($arrayDatos))
                    {
                        $arrayParametrosAuditoria["intIdPersona"] = $arrayDatos['idPersona'];
                        $arrayParams["strPersonaEmpresaRolId"]    = $arrayDatos['idPersonaEmpresaRol'];
                    }
                    else
                    {
                        if(is_object($objPersonaEmpresaRolUsr))
                        {
                            $arrayParametrosAuditoria["intIdPersona"] = $objPersonaEmpresaRolUsr->getPersonaId()->getId();
                        }
                    }

                    $arrayParams["intIdCuadrilla"]  = $arrayParametros["idResponsable"];
                    $arrayParams["strTipoAsignado"] = "CUADRILLA";
                }
                else if($arrayParametros["tipoResponsable"] == "E" )
                {
                    $objInfoPersonaEmpresaRol = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                  ->find($arrayParametros["idResponsable"]);

                    if(is_object($objInfoPersonaEmpresaRol))
                    {
                        $arrayParametrosAuditoria["intIdPersona"] = $objInfoPersonaEmpresaRol->getPersonaId();
                    }

                    if ($arrayParametros['esMigracionNgFirewall'] === "NO")
                    {
                        $arrayParams["strPersonaEmpresaRolId"] = $arrayParametros["idResponsable"];
                    }
                    else
                    {
                        $arrayParams["strPersonaEmpresaRolId"] = $arrayParametros["idPersonaEmpresaRol"];
                    }
                    $arrayParams["strTipoAsignado"]        = "EMPLEADO";
                }
            }
            else
            {
                if(is_object($objPersonaEmpresaRolUsr))
                {
                    $arrayParametrosAuditoria["intIdPersona"] = $objPersonaEmpresaRolUsr->getPersonaId()->getId();
                }
            }

            $arrayParams["intIdPunto"]                     = $intIdPunto;
            $arrayParams["strLogin"]                       = $strlogin;
            $arrayParams["strBandResponsableCambioEquipo"] = "S";

            $this->serviceInfoElemento->ingresaAuditoriaElementos($arrayParametrosAuditoria);
            $arrayParams['esMigracionNgFirewall'] = $arrayParametros['esMigracionNgFirewall'];

            if($arrayParametros['strRegistroEquipos'] != "S")
            {
                $this->servicioGeneral->generarSolicitudRetiroEquipo($arrayParams);
            }

            $arrayResponseCPE[] = array('status' => "OK", 'mensaje' => "OK");
            return $arrayResponseCPE;
        }
        else
        {
            $arrayResponseCPE[] = array('status'  => 'ERROR', 
                                        'mensaje' => 'En finalizarDetalleSolicitud: '.$arrayResponse['mensaje']);
            return $arrayResponseCPE;
        }       
    }        
    
    /**
     * 
     * Método encargado de realizar el cambio de equipo para elementos de tipo ROUTER
     * 
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0
     * @since 25-10-2016
     * 
     @param Array $arrayParametros      [
     *                                      boolEsFibraRuta                      Booleano que determina el tipo de factibilidad ( RUTA/DIRECTO )
     *                                      objServicioTecnico                   Objeto referente a la informacion tecnica del servicio
     *                                      objServicio                          Objeto referente a la informacion del servicio
     *                                      objProducto                          Objeto referente a la información del producto ligado al servicio
     *                                      objElemento                          Objeto referente al Switch del cual sale el enlace del servicio
     *                                      objInterfaceElemento                 Objeto referente a la interface del cual sale el servicio
     *                                      objInterfaceOUT                      Interface más próxima enlazada al puerto del cpe a ser cambiado
     *                                      esMigrado                            Indicador de servicios migrados sin data GIS
     *                                      objUltimaMilla                       Objeto referente a la ultima milla del servicios
     *                                      tipoElementoNuevo                    Valor del tipo del modelo del equipo a cambiar
     *                                      idEmpresa                            Empresa de donde se genera la acción
     *                                      usrCreacion                          Usuario de quien genera la acción
     *                                      ipCreacion                           Ip desde donde se genera la acción
     *                                    ]
     * @return Array $arrayResponseCPE [ status   OK/ERROR
     *                                   mensaje  Mensaje de acuerdo al evento generado
     *                                 ]
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.1
     * @since 20-01-2017 Se cambio la validacion de las asignacion de puertos del nuevo elemento, porque los modelos y sus interfaces no son los 
     *                   mismos
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.2
     * @since 13-04-2017 Se cambio la validacion para que soporte el cambio de elemento nodo wifi esquema 2
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.3
     * @since 27-04-2017 Se valido que se actualice la info servicio tecnico cuando el tipo de factibilidad es DIRECTO
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.4 15-11-2017 Se agrega actualización de la información tanto en la info_servicio_tecnico y la info_servicio_historial
     *                         cuando los servicios tienen el mismo elementoClienteId asociado con el servicio actual
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.5 01-03-2018 Se registra tracking del elemento
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.6 29-05-2018 - Se agrega el parametro del id_persona en el registro de la trazabilidad
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.7 25-09-2018 - Se realizan ajustes en la pantalla de cambio de cpe, se agrega la opción que registra la cuadrilla responsable
     *                           del retiro del equipo
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.8 03-10-2018 - Se realizan ajustes para agregar el concepto que el responsable del retiro de equipo puede ser una
     *                           cuadrilla o un empleado
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.9 22-05-2019 - Se realizan ajustes para crear una tarea de retiro de equipo que sea asignada al responsable del retiro de equipo
     *
     * @author Modificado: Felix Caicedo <facaicedo@telconet.ec>
     * @version 2.0 29-11-2019 - Se realizan ajustes en el llamado de la configuración de la mac en los ws de networking
     */
    private function cambioElementoRouter($arrayParametros)
    {                       
        $boolEsFibraRuta          = $arrayParametros['boolEsFibraRuta'];
        $objServicioTecnico       = $arrayParametros['objServicioTecnico'];
        $objUltimaMilla           = $arrayParametros['objUltimaMilla'];
        $intMotivoId              = "";
        $intIdPunto               = "";
        $strlogin                 = "";
 
        $objElementoClienteActual = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                            ->findOneById($arrayParametros['idElementoActual']);              
                                          
        $arrayInformacionPuertoOutVecino       = $this->getArrayInformacionPuertoOutVecinoCpe($arrayParametros);

        if($arrayInformacionPuertoOutVecino['status'] == 'ERROR')
        {
            $arrayResponseCPE[] = array('status'  => 'ERROR',
                                        'mensaje' => $arrayInformacionPuertoOutVecino['mensaje']);
            return $arrayResponseCPE;
        }

        $objInterfaceTransciverOUT = $arrayInformacionPuertoOutVecino['objInterfaceOutVecinoCpe'];    
        
        $this->serviceUtil->validaObjeto($objInterfaceTransciverOUT,'No se pudo obtener interface más próxima conectado al Cpe anterior');
        
        // ===================================================================================================
        // SE LOCALIZA LA VLAN DEL CLIENTE 
        // ===================================================================================================
        $objServicio          = $arrayParametros['objServicio'];
        $objProducto          = $arrayParametros['objProducto'];
        $objElemento          = $arrayParametros['objElemento'];
        $objInterfaceElemento = $arrayParametros['objInterfaceElemento'];
               
        $this->serviceUtil->validaObjeto($objElemento,'No se encuentra Información de Switch');
        $this->serviceUtil->validaObjeto($objInterfaceElemento,'No se encuentra Información Puerto de Switch');
        $this->serviceUtil->validaObjeto($objProducto,'No se encuentra Información de Producto ligado al Servicio');
        
        // ....
        $objDetEleAnillo      = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                        ->findOneBy(array("elementoId"       => $objElemento->getId(),
                                                                          "detalleNombre"    => "ANILLO",
                                                                          "estado"           => "Activo"));
        
        $this->serviceUtil->validaObjeto($objDetEleAnillo,'No se pudo obtener información del anillo ligado al Switch');

        $macServicio          = $this->servicioGeneral->getMacPorServicio($objServicio->getId()); 
                
        if(!$macServicio)
        {
            $arrayFinal[] = array('status'=>"ERROR", 'mensaje'=>'No se encuentra la MAC asociada al Servicio, por favor verificar');
            return $arrayFinal;
        }                   
        
        $objSolCaracVlan  = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "VLAN", $objServicio->getProductoId());
        
        $strVlan          = null;
        
        if(is_object($objSolCaracVlan))
        {        
            if($objProducto->getNombreTecnico()=="L3MPLS")
            {  
                $objPerEmpRolCarVlan = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                         ->find($objSolCaracVlan->getValor());

                if(is_object($objPerEmpRolCarVlan))
                {
                    $objDetalleElementoVlan = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                      ->find($objPerEmpRolCarVlan->getValor());
                }
            }
            else
            {
                $objDetalleElementoVlan = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                  ->find($objSolCaracVlan->getValor());
            }

            if(is_object($objDetalleElementoVlan))
            {
                $strVlan = $objDetalleElementoVlan->getDetalleValor();
            }                        
        }

        if(!$strVlan)
        {
            $arrayRespuestaError[] = array('status'=>"ERROR", 'mensaje'=>'No se encontró la Vlan del Servicio');
            return $arrayRespuestaError;
        }              
 
        $arrayVlanMacActual   = array($strVlan => array($macServicio));
                
        $arrayVlanMacNuevo    = array($strVlan => array($arrayParametros['macElementoNuevo']));

        $loginAux             = $objServicio->getLoginAux();
        $strDescripcionUm     = $objUltimaMilla->getNombreTipoMedio()== "Fibra Optica"?'_fib':'_rad';

        // ===================================================================================================
        // REGISTRO DEL NUEVO ELEMENTO EN NAF Y TELCOS
        // ===================================================================================================
        $arrayRequestElemento = $this->registrarElementoNuevoNAF($arrayParametros);
        
        if(!isset($arrayRequestElemento) || $arrayRequestElemento['status'] != 'OK')
        {
            $arrayResponseCPE[] = array('status'  => 'ERROR',
                                        'mensaje' => $arrayRequestElemento['mensaje']);
            return $arrayResponseCPE;
        }
        else
        {
            //Se obtiene el objeto que referencia al nuevo elemento creado
            $objElementoCpeNuevo = $arrayRequestElemento['objElementoCpe'];
        }
        
        $this->serviceUtil->validaObjeto($objElementoCpeNuevo,'No se encuentra Información de Elemento Router nuevo creado');
       
        //Crear el enlace entre CPE Nuevo e informacion de bb existente
        //Interface del CPE
        $objInterfaceElementoNuevo = $this->emInfraestructura
                                          ->getRepository('schemaBundle:InfoInterfaceElemento')
                                          ->findOneBy(array("elementoId"                   => $objElementoCpeNuevo->getId(),
                                                            "nombreInterfaceElemento"      => $arrayParametros['nombreInterface']));

        $this->serviceUtil->validaObjeto($objInterfaceElementoNuevo,'No se encuentra Información de la Interface Wan');

        $objEnlaceCpeNuevoVecinoOut = new InfoEnlace();
        $objEnlaceCpeNuevoVecinoOut->setInterfaceElementoIniId($objInterfaceTransciverOUT);
        $objEnlaceCpeNuevoVecinoOut->setInterfaceElementoFinId($objInterfaceElementoNuevo);
        $objEnlaceCpeNuevoVecinoOut->setTipoMedioId($objUltimaMilla);
        $objEnlaceCpeNuevoVecinoOut->setTipoEnlace($objServicioTecnico->getTipoEnlace());
        $objEnlaceCpeNuevoVecinoOut->setEstado("Activo");
        $objEnlaceCpeNuevoVecinoOut->setUsrCreacion($arrayParametros['usrCreacion']);
        $objEnlaceCpeNuevoVecinoOut->setFeCreacion(new \DateTime('now'));
        $objEnlaceCpeNuevoVecinoOut->setIpCreacion($arrayParametros['ipCreacion']);
        $this->emInfraestructura->persist($objEnlaceCpeNuevoVecinoOut);

        $objInterfaceElementoNuevo->setEstado("connected");
        $this->emInfraestructura->persist($objInterfaceElementoNuevo);
                       
        // ===================================================================================================
        // SE TRANSFIEREN LOS DETALLES ELEMENTO ACTUAL AL NUEVO Y SE ACTUALIZA LA MAC
        // ===================================================================================================
        $arrayParametros['objElementoClienteActual'] = $objElementoClienteActual;
        $arrayParametros['objElementoCpeNuevo']      = $objElementoCpeNuevo;
        
        $this->traspasarInformacionTecnicaElemento($arrayParametros);
        
        // ---------------------------------------------------------------------------------------------------

        $objElementoCpeNuevo->setNombreElemento($objElementoClienteActual->getNombreElemento());
        $this->emInfraestructura->persist($objElementoCpeNuevo);
        $this->emInfraestructura->flush();

        $objDetEleTipoEleRed = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                       ->findOneBy(array("elementoId"       => $objElementoClienteActual->getId(),
                                                                         "detalleNombre"    => "TIPO ELEMENTO RED",
                                                                         "estado"           => "Activo"));
        if(is_object($objDetEleTipoEleRed))
        {
            $objDetEleTipoEleRed->setEstado('Eliminado');
            $this->emInfraestructura->persist($objDetEleTipoEleRed);
            $this->emInfraestructura->flush();
            $this->servicioGeneral->ingresarDetalleElemento($objElementoCpeNuevo,
                                                            "TIPO ELEMENTO RED",
                                                            "Indicar que es un router de uso Wifi", 
                                                            $objDetEleTipoEleRed->getDetalleValor(), 
                                                            $arrayParametros['usrCreacion'],
                                                            $arrayParametros['ipCreacion']);
        }

        $objDetEleCapacidad = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                       ->findOneBy(array("elementoId"       => $objElementoClienteActual->getId(),
                                                                         "detalleNombre"    => "CAPACIDAD",
                                                                         "estado"           => "Activo"));
        if(is_object($objDetEleCapacidad))
        {
            $objDetEleCapacidad->setEstado('Eliminado');
            $this->emInfraestructura->persist($objDetEleCapacidad);
            $this->emInfraestructura->flush();
            $this->servicioGeneral->ingresarDetalleElemento($objElementoCpeNuevo,
                                                            "CAPACIDAD",
                                                            "Capacidad del elemento en Kb", 
                                                            $objDetEleCapacidad->getDetalleValor(), 
                                                            $arrayParametros['usrCreacion'],
                                                            $arrayParametros['ipCreacion']);
        }

        //relacion elemento
        $objDetEleIdPunto       = $arrayParametros['objDetalleElemento']; 
        if(is_object($objDetEleIdPunto))
        {
            $objRelacionElementoActual = $this->emInfraestructura->getRepository('schemaBundle:InfoRelacionElemento')
                                                               ->findOneBy(array("elementoIdA" => $objDetEleIdPunto->getElementoId(),
                                                                                 "elementoIdB" => $objElementoClienteActual->getId(),
                                                                                 "estado"      => 'Activo'
                                                                                 ));
            if(is_object($objRelacionElementoActual))
            {
                $objRelacionElementoActual->setEstado('Eliminado');
                $this->emInfraestructura->persist($objRelacionElementoActual);
            }

            $objRelacionElemento = new InfoRelacionElemento();
            $objRelacionElemento->setElementoIdA($objDetEleIdPunto->getElementoId());//Nodo Wifi
            $objRelacionElemento->setElementoIdB($objElementoCpeNuevo->getId());
            $objRelacionElemento->setTipoRelacion("CONTIENE");
            $objRelacionElemento->setObservacion("Nodo Wifi contiene Router");
            $objRelacionElemento->setEstado("Activo");
            $objRelacionElemento->setUsrCreacion($arrayParametros['usrCreacion']);
            $objRelacionElemento->setFeCreacion(new \DateTime('now'));
            $objRelacionElemento->setIpCreacion($arrayParametros['ipCreacion']);
            $this->emInfraestructura->persist($objRelacionElemento);
            $this->emInfraestructura->flush();
        }

        //Guardar la MAC del CPE atada a la interface del equipo                           
        $objInterfaceElementoNuevo->setMacInterfaceElemento($arrayParametros['macElementoNuevo']);
        $objInterfaceElementoNuevo->setEstado('connected');
        $this->emInfraestructura->persist($objInterfaceElementoNuevo);
        $this->emInfraestructura->flush();                                                

       
        //agrego los detalles interfaces a los servicios
        $arrayServicios = $this->emInfraestructura->getRepository('schemaBundle:InfoServicio')
                                                  ->findBy(array('puntoId' => $objServicio->getPuntoId()->getId()));
        
        
        $arrayIdsServiciosHistoriales = array();
        foreach($arrayServicios as $objServicioDetalle)
        {
            if(is_object($objServicioDetalle))
            {
                $intIdServicioPunto = $objServicioDetalle->getId();
                $objServicioTec     = $this->emInfraestructura->getRepository('schemaBundle:InfoServicioTecnico')
                                                              ->findOneByServicioId($intIdServicioPunto);
                //si tiene el mismo elemento cliente significa debe agregarse el detalle al servicio
                if(is_object($objServicioTecnico) && is_object($objServicioTec) 
                    && ($objServicioTecnico->getElementoClienteId() == $objServicioTec->getElementoClienteId()))
                {
                    $arrayIdsServiciosHistoriales[] = $intIdServicioPunto;

                    //Se guarda referencia en la detalle interface del servicio relacionado a este puerto y mac
                    $objInfoDetalleInterface = new InfoDetalleInterface();
                    $objInfoDetalleInterface->setInterfaceElementoId($objInterfaceElementoNuevo);
                    $objInfoDetalleInterface->setDetalleNombre("servicio");
                    $objInfoDetalleInterface->setDetalleValor($objServicioDetalle->getId());
                    $objInfoDetalleInterface->setUsrCreacion($arrayParametros['usrCreacion']);
                    $objInfoDetalleInterface->setFeCreacion(new \DateTime('now'));
                    $objInfoDetalleInterface->setIpCreacion($arrayParametros['ipCreacion']);
                    $this->emInfraestructura->persist($objInfoDetalleInterface);
                    $this->emInfraestructura->flush();

                    if(!$boolEsFibraRuta)
                    {
                        $objServicioTec->setElementoClienteId($objElementoCpeNuevo->getId());
                        $objServicioTec->setInterfaceElementoClienteId($objInterfaceElementoNuevo->getId());
                        $this->emInfraestructura->persist($objServicioTec);
                        $this->emInfraestructura->flush();
                    }
                }
            } 
        }

        // ===================================================================================================
        // ELIMINAMOS LOS ENLACES CON EL ELEMENTO ACTUAL
        // ===================================================================================================

        if(
            ($boolEsFibraRuta && $arrayParametros['objServicioTecnico']->getInterfaceElementoConectorId() != null) || 
            (
                $arrayParametros['objServicioTecnico']->getInterfaceElementoClienteId()!=null && !$boolEsFibraRuta
            )            
          )
        {

            $arrayEnlaces = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                    ->findBy(array('interfaceElementoIniId' => $objInterfaceTransciverOUT->getId(),
                                                                   'estado'                 => 'Activo'));
            foreach ($arrayEnlaces as $objEnlace) 
            {                        
                //Se elimina el enlace anterior
                //Si el la interface nuevo es igual al del nuevo cpe no elimina el enlace
                if($objEnlace->getInterfaceElementoFinId()->getElementoId()->getId() != $objElementoCpeNuevo->getId())
                {
                    $objEnlace->setEstado('Eliminado');
                    $this->emInfraestructura->persist($objEnlace);
                    $this->emInfraestructura->flush();  
                }
            }
        }
        // ===================================================================================================
        // ELIMINAMOS LOS PUERTOS DEL ELEMENTO ACTUAL
        // ===================================================================================================
        
        $objProdCarac = $this->emInfraestructura
                             ->getRepository('schemaBundle:AdmiProductoCaracteristica')
                             ->findByDescripcionProductoAndCaracteristica(array(
                                                                            'strDescProducto'       => 'INTERNET WIFI',
                                                                            'strDescCaracteristica' => 'INTERFACE_ELEMENTO_ID',
                                                                            'strEstado'             => 'Activo'));
        
        $interfacesElementoActual = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                            ->getInterfacesByIdElemento($objElementoClienteActual->getId());
        foreach ($interfacesElementoActual as $objInterface) 
        {
            $objEnlaceInterno = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                        ->findOneBy(array('interfaceElementoIniId' =>$objInterface->getId(),
                                                                          'estado'                 =>'Activo'));
            if(is_object($objEnlaceInterno))
            {                    
                //valido que si es el mismo modelo busque el mismo puerto, pero si son modelos diferentes se asigna a cualquiera
                if($objElementoCpeNuevo->getModeloElementoId() == $objElementoClienteActual->getModeloElementoId())
                {
                    $interfaceElementoNuevo = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                   ->findOneBy(array('nombreInterfaceElemento'     =>$objInterface->getNombreInterfaceElemento(),
                                                                     'descripcionInterfaceElemento'=>$objInterface->getDescripcionInterfaceElemento(),
                                                                     'elementoId'                  =>$objElementoCpeNuevo->getId(),
                                                                     'estado'                      =>"not connect"
                                                                    )
                                                                );
                }
                else
                {
                    $interfaceElementoNuevo = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                   ->findOneBy(array('elementoId'                   => $objElementoCpeNuevo->getId(),
                                                                     'estado'                       => "not connect"));
                    if(!is_object($interfaceElementoNuevo))
                    {
                        $arrayResponseCPE[] = array('status'  => 'ERROR', 
                                                    'mensaje' => "El número de puertos conectados de equipo origen es superior al número de puertos 
                                                                  disponibles del nuevo equipo. Favor verificar el nuevo modelo");
                        return $arrayResponseCPE;
                    }
                }
                
                if(is_object($interfaceElementoNuevo))
                {
                    $objEnlaceNuevo = $this->duplicarEnlaceNuevoInicio($objEnlaceInterno,
                                                                       $interfaceElementoNuevo,
                                                                       $arrayParametros['usrCreacion'],
                                                                       $arrayParametros['ipCreacion']);
                    $interfaceElementoNuevo->setEstado($objInterface->getEstado());
                    $this->emInfraestructura->persist($interfaceElementoNuevo);
                    $this->emInfraestructura->persist($objEnlaceNuevo);
                    $this->emInfraestructura->flush();

                    if(is_object($objProdCarac))
                    {
                        $servProdCarac = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                              ->findOneBy(array( "productoCaracterisiticaId" => $objProdCarac->getId(),
                                                                 "valor"                     => $objInterface->getId(),
                                                                 "estado"                    => "Activo"));
                        if(is_object($servProdCarac))
                        {
                            $servProdCarac->setEstado('Eliminado');
                            $servProdCarac->setUsrUltMod($arrayParametros['usrCreacion']);
                            $servProdCarac->setFeUltMod(new \DateTime('now'));
                            $this->emComercial->persist($servProdCarac);

                            $objInfoServicioProdCaract = new InfoServicioProdCaract();
                            $objInfoServicioProdCaract->setServicioId($servProdCarac->getServicioId()); 
                            $objInfoServicioProdCaract->setProductoCaracterisiticaId($objProdCarac->getId());
                            $objInfoServicioProdCaract->setValor($interfaceElementoNuevo->getId());
                            $objInfoServicioProdCaract->setEstado("Activo");
                            $objInfoServicioProdCaract->setFeCreacion(new \DateTime('now'));
                            $objInfoServicioProdCaract->setUsrCreacion($arrayParametros['usrCreacion']);
                            $this->emComercial->persist($objInfoServicioProdCaract);
                            $this->emComercial->flush();
                        }
                    }
                }
                else
                {
                    $arrayResponseCPE[] = array('status'  => 'ERROR', 
                                                'mensaje' => "La interface '".$objInterface->getNombreInterfaceElemento().
                                                             "' no existe para el nuevo elemento. Y tiene un enlace existente!");
                    return $arrayResponseCPE;
                }

                if(strpos($objInterface->getNombreInterfaceElemento(), "IN") !== FALSE )
                {
                    // ELIMINAMOS ENLACES INTERNOS
                    $objEnlaceInterno->setEstado('Eliminado');
                    $this->emInfraestructura->persist($objEnlaceInterno);
                    $this->emInfraestructura->flush();
                }
            }
            $objInterface->setEstado('Eliminado');
            $this->emInfraestructura->persist($objInterface);
            $this->emInfraestructura->flush();  
        }
        // ===================================================================================================
        // ELIMINAMOS EL ELEMENTO ACTUAL
        // ===================================================================================================
        $objElementoClienteActual->setEstado('Eliminado');
        $this->emInfraestructura->persist($objElementoClienteActual);
        $this->emInfraestructura->flush();
        
        // ===================================================================================================
        // CALL WS "NetworkingScriptsService" >>> CAMBIO DE MAC DEL ELEMENTO
        // ===================================================================================================
        
        $arrayRequestWS                 = array();
        $arrayRequestWS['url']          = 'cambio_cpe';
        $arrayRequestWS['accion']       = 'cambio_cpe';
        $arrayRequestWS['sw']           = $objElemento->getNombreElemento();
        $arrayRequestWS['pto']          = $objInterfaceElemento->getNombreInterfaceElemento();
        $arrayRequestWS['anillo']       = $objDetEleAnillo->getDetalleValor();
        $arrayRequestWS['descripcion']  = 'cce_'.$loginAux.$strDescripcionUm;
        $arrayRequestWS['servicio']     = $objProducto->getNombreTecnico();
        $arrayRequestWS['login_aux']    = $loginAux;
        $arrayRequestWS['user_name']    = $arrayParametros['usrCreacion'];
        $arrayRequestWS['user_ip']      = $arrayParametros['ipCreacion'];
        
        $arrayRequestWS['cpe_anterior'] = array();
        foreach( $arrayVlanMacActual as $strValueVlanCPE => $arrayValueMacCPE )
        {
            foreach( $arrayValueMacCPE as $strValueMacCPE )
            {
                $arrayRequestWS['cpe_anterior'][] = array(
                            'vlan' => $strValueVlanCPE,
                            'mac'  => $strValueMacCPE,
                        );
            }
        }

        $arrayRequestWS['cpe_nuevo']    = array();
        foreach( $arrayVlanMacNuevo as $strValueVlanCPE => $arrayValueMacCPE )
        {
            foreach( $arrayValueMacCPE as $strValueMacCPE )
            {
                $arrayRequestWS['cpe_nuevo'][] = array(
                            'vlan' => $strValueVlanCPE,
                            'mac'  => $strValueMacCPE,
                        );
            }
        }

        //ejecución del método vía WS para realizar la configuración del SW
        $arrayRespuesta = $this->networkingScripts->callNetworkingWebService($arrayRequestWS);
        if($arrayRespuesta['status'] != 'OK')
        {
            $arrayResponseCPE[]        = $arrayRespuesta;
            return $arrayResponseCPE;
        }

        //SE REGISTRA EL TRACKING DEL ELEMENTO
        $arrayParametrosAuditoria["strNumeroSerie"]  = $objElementoClienteActual->getSerieFisica();
        $arrayParametrosAuditoria["strEstadoTelcos"] = 'Eliminado';
        $arrayParametrosAuditoria["strEstadoNaf"]    = 'Instalado';
        $arrayParametrosAuditoria["strEstadoActivo"] = 'CambioEquipo';
        $arrayParametrosAuditoria["strUbicacion"]    = 'EnTransito';
        $arrayParametrosAuditoria["strCodEmpresa"]   = "10";
        $arrayParametrosAuditoria["strTransaccion"]  = 'Cambio de Elemento';
        $arrayParametrosAuditoria["intOficinaId"]    = 0;

        //Se consulta el login del cliente
        if(is_object($arrayParametros['objServicio']))
        {
            $objInfoPunto = $this->emInfraestructura->getRepository('schemaBundle:InfoPunto')
                                                    ->find($arrayParametros['objServicio']->getPuntoId()->getId());
            if(is_object($objInfoPunto))
            {
                $arrayParametrosAuditoria["strLogin"] = $objInfoPunto->getLogin();

                $intIdPunto = $objInfoPunto->getId();
                $strlogin   = $arrayParametrosAuditoria["strLogin"];
            }
        }

        $arrayParametrosAuditoria["strUsrCreacion"] = $arrayParametros['usrCreacion'];

        // ===================================================================================================
        // CANCELAR SOLICITUD DE CAMBIO DE EQUIPO
        // ===================================================================================================
        $arrayParams = array();
        $arrayParams['objServicio']         = $arrayParametros['objServicio'];
        $arrayParams['strTipoSolicitud']    = "SOLICITUD CAMBIO DE MODEM INMEDIATO";
        $arrayParams['strEstadoSolicitud']  = "AsignadoTarea";
        $arrayParams['strUsrCreacion']      = $arrayParametros['usrCreacion'];
        $arrayParams['strIpCreacion']       = $arrayParametros['ipCreacion'];
        // ...
        $arrayResponse = $this->servicioGeneral->finalizarDetalleSolicitud($arrayParams);
        if($arrayResponse['status'] == 'OK')
        {
            // SE LOCALIZA LA ASIGACION DE LA SOLICITUD DE CAMBIO DE EQUIPO PARA REASIGNAR A LA MISMA PERSONA EL RETIRO DE EQUIPO
            $objDetalle = $this->emComercial->getRepository('schemaBundle:InfoDetalle')
                                            ->findOneBy(array("detalleSolicitudId" => $arrayResponse['objDetalleSolicitud']->getId()));
            $objDetalleAsignacion = $this->emComercial->getRepository('schemaBundle:InfoDetalleAsignacion')
                                                      ->findOneBy(array("detalleId" => $objDetalle->getId()));
            // ...
            // ===================================================================================================
            // CREAR SOLICITUD DE RETIRO DE EQUIPO POR CAMBIO DE EQUIPO
            // ===================================================================================================
            $arrayParams = array();
            $arrayParams['objServicio']         = $arrayParametros['objServicio'];
            $arrayParams['objElementoCliente']  = $objElementoClienteActual;
            $arrayParams['idPersonaEmpresaRol'] = $objDetalleAsignacion->getPersonaEmpresaRolId();
            $arrayParams['strMotivoSolicitud']  ='CAMBIO DE EQUIPO';
            $arrayParams['usrCreacion']         = $arrayParametros['usrCreacion'];
            $arrayParams['ipCreacion']          = $arrayParametros['ipCreacion'];
            // ...

            $objPersonaEmpresaRolUsr = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                         ->find($objDetalleAsignacion->getPersonaEmpresaRolId());

            if(!empty($arrayParametros["idResponsable"]) && isset($arrayParametros["idResponsable"]) && !empty($arrayParametros["tipoResponsable"]))
            {
                if(is_object($arrayResponse['objDetalleSolicitud']))
                {
                     $intMotivoId = $arrayResponse['objDetalleSolicitud']->getMotivoId();

                     if(!empty($intMotivoId))
                     {
                          $objAdmiMotivo = $this->emComercial->getRepository('schemaBundle:AdmiMotivo')->find($intMotivoId);

                          if(is_object($objAdmiMotivo))
                          {
                              $arrayParametrosAuditoria["strObservacion"] = $objAdmiMotivo->getNombreMotivo();
                          }
                     }
                }

                if($arrayParametros["tipoResponsable"] == "C" )
                {
                    $arrayDatos = $this->emComercial->getRepository('schemaBundle:AdmiCuadrilla')
                                                    ->findJefeCuadrilla($arrayParametros["idResponsable"]);

                    if(!empty($arrayDatos) && isset($arrayDatos))
                    {
                        $arrayParametrosAuditoria["intIdPersona"] = $arrayDatos['idPersona'];
                        $arrayParams["strPersonaEmpresaRolId"]    = $arrayDatos['idPersonaEmpresaRol'];
                    }
                    else
                    {
                        if(is_object($objPersonaEmpresaRolUsr))
                        {
                            $arrayParametrosAuditoria["intIdPersona"] = $objPersonaEmpresaRolUsr->getPersonaId()->getId();
                        }
                    }

                    $arrayParams["intIdCuadrilla"]  = $arrayParametros["idResponsable"];
                    $arrayParams["strTipoAsignado"] = "CUADRILLA";
                }
                else if($arrayParametros["tipoResponsable"] == "E" )
                {
                    $objInfoPersonaEmpresaRol = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                  ->find($arrayParametros["idResponsable"]);

                    if(is_object($objInfoPersonaEmpresaRol))
                    {
                        $arrayParametrosAuditoria["intIdPersona"] = $objInfoPersonaEmpresaRol->getPersonaId();
                    }

                    $arrayParams["strPersonaEmpresaRolId"] = $arrayParametros["idResponsable"];
                    $arrayParams["strTipoAsignado"]        = "EMPLEADO";
                }
            }
            else
            {
                if(is_object($objPersonaEmpresaRolUsr))
                {
                    $arrayParametrosAuditoria["intIdPersona"] = $objPersonaEmpresaRolUsr->getPersonaId()->getId();
                }
            }

            $arrayParams["intIdPunto"]                     = $intIdPunto;
            $arrayParams["strLogin"]                       = $strlogin;
            $arrayParams["strBandResponsableCambioEquipo"] = "S";

            $this->serviceInfoElemento->ingresaAuditoriaElementos($arrayParametrosAuditoria);

            $this->servicioGeneral->generarSolicitudRetiroEquipo($arrayParams);
            // ...
            // Se crean los historiales para todos los servicios asociados
            $tipoElementoCambioEquipo = $objElementoClienteActual->getModeloElementoId()->getTipoElementoId()->getNombreTipoElemento();
            foreach($arrayIdsServiciosHistoriales as $intIdServicioCrearHistorial)
            {
                $objServicioCrearHistorial  = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicioCrearHistorial);
                if(is_object($objServicioCrearHistorial))
                {
                    $objServicioHistorial = new InfoServicioHistorial();
                    $objServicioHistorial->setServicioId($objServicioCrearHistorial);
                    $objServicioHistorial->setObservacion("Se Realizo un Cambio de Elemento Cliente " . $tipoElementoCambioEquipo);
                    $objServicioHistorial->setEstado($arrayParametros['objServicio']->getEstado());
                    $objServicioHistorial->setUsrCreacion($arrayParams['usrCreacion']);
                    $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                    $objServicioHistorial->setIpCreacion($arrayParametros['ipCreacion']);
                    $this->emComercial->persist($objServicioHistorial);
                    $this->emComercial->flush();
                    
                }
            }
            $arrayResponseCPE[] = array('status' => "OK", 'mensaje' => "OK");
            return $arrayResponseCPE;
        }
        else
        {
            $arrayResponseCPE[] = array('status'  => 'ERROR', 
                                        'mensaje' => 'En finalizarDetalleSolicitud: '.$arrayResponse['mensaje']);
            return $arrayResponseCPE;
        }
        
    }    
    
    
    /**
     * 
     * Método encargado de realizar el cambio de equipo para servicios pseudope
     * 
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0
     * @since 23-11-2016
     *
     * @author Richard Cabrera  <rcabrera@telconet.ec>
     * @version 1.1 2017-09-21 - Se realizan ajustes en la forma como se obtiene un servicio ligado a la interface wan del cpe, se obtienen solo los
     *                           servicios en estado: Activo,enPruebas e In-Corte
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 01-03-2018 Se registra tracking del elemento
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.3 29-05-2018 - Se agrega el parametro del id_persona en el registro de la trazabilidad
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.4 26-09-2018 - Se realizan ajustes en la pantalla de cambio de cpe, se agrega la opción que registra la cuadrilla responsable
     *                           del retiro del equipo
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.5 03-10-2018 - Se realizan ajustes para agregar el concepto que el responsable del retiro de equipo puede ser una
     *                           cuadrilla o un empleado
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.6 22-05-2019 - Se realizan ajustes para crear una tarea de retiro de equipo que sea asignada al responsable del retiro de equipo
     *
     @param Array $arrayParametros      [
     *                                      objServicioTecnico                   Objeto referente a la informacion tecnica del servicio
     *                                      objServicio                          Objeto referente a la informacion del servicio
     *                                      objProducto                          Objeto referente a la información del producto ligado al servicio
     *                                      objElemento                          Objeto referente al Switch del cual sale el enlace del servicio     
     *                                      objUltimaMilla                       Objeto referente a la ultima milla del servicios
     *                                      tipoElementoNuevo                    Valor del tipo del modelo del equipo a cambiar
     *                                      idEmpresa                            Empresa de donde se genera la acción
     *                                      usrCreacion                          Usuario de quien genera la acción
     *                                      ipCreacion                           Ip desde donde se genera la acción
     *                                    ]
     * @return Array $arrayResponseCPE [ status   OK/ERROR
     *                                   mensaje  Mensaje de acuerdo al evento generado
     *                                 ]
     */
    private function cambioElementoPseudoPe($arrayParametros)
    {
        $arrayServicios           = array();
        $intMotivoId              = "";
        $intIdPunto               = "";
        $strlogin                 = "";
        $objElementoClienteActual = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                            ->findOneById($arrayParametros['idElementoActual']);
        
        $this->serviceUtil->validaObjeto($objElementoClienteActual,'No se encuentra Elemento CPE/ROUTER a ser cambiado, por favor verificar');
        
        // ===================================================================================================
        // REGISTRO DEL NUEVO ELEMENTO EN NAF Y TELCOS
        // ===================================================================================================
                
        $arrayRequestElemento = $this->registrarElementoNuevoNAF($arrayParametros);
        
        if(!isset($arrayRequestElemento) || $arrayRequestElemento['status'] != 'OK')
        {
            $arrayResponseCPE[] = array('status'  => 'ERROR',
                                        'mensaje' => $arrayRequestElemento['mensaje']);
            return $arrayResponseCPE;
        }
        else
        {
            //Se obtiene el objeto que referencia al nuevo elemento creado
            $objElementoCpeNuevo = $arrayRequestElemento['objElementoCpe'];
        }
        
        $this->serviceUtil->validaObjeto($objElementoCpeNuevo,'No pudo ser creado nuevo CPE, por favor revisar');
          
        // ===================================================================================================
        //  SE OBTIENE Y RECORRE LAS INTERFACES ES CONECTADAS DEL CPE ANTERIOR
        // ===================================================================================================
        
        $arrayParametrosInterfacesOcupadasCpe                  = array();
        $arrayParametrosInterfacesOcupadasCpe['intIdElemento'] = $arrayParametros['idElementoActual'];
               
        //Se obtiene las interfaces del cpe anterior que se encuentren conectadas wan1 , wan2 o ambas
        $arrayInterfacesOcupadasCpe = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                           ->getResultadoInterfacesElementoPorNombreInterface($arrayParametrosInterfacesOcupadasCpe);
        
        $arrayResultadoInterfaces   = $arrayInterfacesOcupadasCpe['resultado'];                
                                  
        foreach($arrayResultadoInterfaces as $interfacesCpe)
        {
            $strNombreInterface = $interfacesCpe['nombreInterfaceElemento'];
            $intIdInterface     = $interfacesCpe['idInterfaceElemento'];

            $arrayEstados = array('EnPruebas','Activo','In-Corte');

            $arrayParametrosInterface["arrayEstados"]           = $arrayEstados;
            $arrayParametrosInterface["strDetalleNombre"]       = 'servicio';
            $arrayParametrosInterface["intInterfaceElementoId"] = $intIdInterface;

            $arrayServicios = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleInterface')
                                                      ->getServiciosPorInterfaceElemento($arrayParametrosInterface);

            //Se obtiene un servicio ligado a la interface wan del cpe para obtener la informacion tecnica relacionada con cada puerto conectado
            if(!empty($arrayServicios[0]["idServicio"]))
            {
                $intIdServicio = $arrayServicios[0]["idServicio"];
            }

            if($intIdServicio)
            {
                $objServicio        = $this->emInfraestructura->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);

                $this->serviceUtil->validaObjeto($objServicio,'No se encuentra información del Servicio, por favor revisar');

                //Informacion tecnica ligada a cada interface wan
                $objServicioTecnico = $this->emInfraestructura->getRepository('schemaBundle:InfoServicioTecnico')
                                                              ->findOneByServicioId($intIdServicio);

                $this->serviceUtil->validaObjeto($objElementoClienteActual,'No se encuentra Información Técnica de Servicio, por favor revisar');
                
                //Crear el enlace entre CPE Nuevo e informacion de bb existente
                //Interface del CPE
                $objInterfaceElementoNuevo = $this->emInfraestructura
                                                  ->getRepository('schemaBundle:InfoInterfaceElemento')
                                                  ->findOneBy(array("elementoId"                   => $objElementoCpeNuevo->getId(),
                                                                    "nombreInterfaceElemento"      => $arrayParametros['interfacesConectadas'] == 1 ?
                                                                                                      $arrayParametros['nombreInterface']:
                                                                                                      $strNombreInterface,
                                                                    "descripcionInterfaceElemento" => "Wan"));
                
                $this->serviceUtil->validaObjeto($objInterfaceElementoNuevo,'No se encuentra Información de la Interface Wan');
                
                $objInterfaceElementoNuevo->setEstado("connected");
                $this->emInfraestructura->persist($objInterfaceElementoNuevo);                                                

                //Recorremos todos los servicios ligados a cada interface wan
                //Se realizará por cada servicio:               
                // - Actualizar información tecnica por cada servicio             
                // - Ingresar informacion de detalle de interface para cada servicio                    
                foreach($arrayServicios as $arrayIdxServicios)
                {
                    $intIdServicio = $arrayIdxServicios["idServicio"];

                    if($intIdServicio)
                    {
                        $objServicioPorPuerto = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);

                        $this->serviceUtil->validaObjeto($objServicioPorPuerto,'No se encuentra información del Servicio');

                        $objServicioTecnicoServicioPuerto = $this->emInfraestructura->getRepository('schemaBundle:InfoServicioTecnico')
                                                                                    ->findOneByServicioId($objServicioPorPuerto->getId());

                        $this->serviceUtil->validaObjeto($objServicioTecnicoServicioPuerto,'No se encuentra Información Técnica de Servicio');
                        
                        $objServicioTecnicoServicioPuerto->setElementoClienteId($objElementoCpeNuevo->getId());
                        $objServicioTecnicoServicioPuerto->setInterfaceElementoClienteId($objInterfaceElementoNuevo->getId());
                        $this->emInfraestructura->persist($objServicioTecnicoServicioPuerto);
                        $this->emInfraestructura->flush();
                                               
                        $strMacActual = null;

                        //Se obtiene la nueva MAC que se requiere para configurar el SW para el cambio de equipo
                        if($strNombreInterface == 'wan1')
                        {                            
                            if($arrayParametros['nombreInterface'] == 'wan1')
                            {
                                $strMacActual = $arrayParametros['macElementoNuevo'];
                            }
                            else
                            {
                                $strMacActual = $arrayParametros['macCpeBck'];
                            }                            
                        }
                        else
                        {
                            if(isset($arrayParametros['macCpeBck']) && $arrayParametros['macCpeBck'])
                            {                               
                                if($arrayParametros['nombreInterface'] == 'wan2')
                                {
                                    $strMacActual = $arrayParametros['macElementoNuevo'];
                                }
                                else
                                {
                                    $strMacActual = $arrayParametros['macCpeBck'];
                                }                                
                            }
                            else
                            {
                                $strMacActual = $arrayParametros['macElementoNuevo'];
                            }                                
                        }
                        
                        //Si solo existe un puerto conectado wan1 o wan2 tomara el valor que viene en el campo definido como principal
                        if($arrayParametros['interfacesConectadas'] == 1)
                        {
                            $strMacActual = $arrayParametros['macElementoNuevo'];
                        }                                                
                        
                        //Guardar la MAC del CPE atada a la interface del equipo                           
                        $objInterfaceElementoNuevo->setMacInterfaceElemento($strMacActual);
                        $objInterfaceElementoNuevo->setEstado('connected');
                        $this->emInfraestructura->persist($objInterfaceElementoNuevo);
                        $this->emInfraestructura->flush();                                                

                        //Se guarda referencia en la detalle interface del servicio relacionado a este puerto y mac
                        $objInfoDetalleInterface = new InfoDetalleInterface();
                        $objInfoDetalleInterface->setInterfaceElementoId($objInterfaceElementoNuevo);
                        $objInfoDetalleInterface->setDetalleNombre("servicio");
                        $objInfoDetalleInterface->setDetalleValor($objServicioPorPuerto->getId());
                        $objInfoDetalleInterface->setUsrCreacion($arrayParametros['usrCreacion']);
                        $objInfoDetalleInterface->setFeCreacion(new \DateTime('now'));
                        $objInfoDetalleInterface->setIpCreacion($arrayParametros['ipCreacion']);
                        $this->emInfraestructura->persist($objInfoDetalleInterface);
                        $this->emInfraestructura->flush();
                        
                        //Historial en cada servicio perteneciente al CPE a ser cambiado
                        $strHistorialPorServicio = "<b>Se realizo un Cambio de Elemento Cliente:</b><br>"
                                                 . "<b style='color:red'>CPE Anterior : </b><br>"
                                                 . "<b>Nombre CPE : </b> ".$objElementoClienteActual->getNombreElemento()."<br>"
                                                 . "<b>Serie CPE  : </b> ".$objElementoClienteActual->getSerieFisica()."<br>"
                                                 . "<b>Modelo CPE : </b> ".$objElementoClienteActual->getModeloElementoId()
                                                                                                    ->getNombreModeloElemento()."<br>"
                                                 . "<b style='color:red'>CPE Actual : </b><br>"
                                                 . "<b>Nombre CPE : </b> ".$objElementoCpeNuevo->getNombreElemento()."<br>"
                                                 . "<b>Serie  CPE : </b> ".$objElementoCpeNuevo->getSerieFisica()."<br>"
                                                 . "<b>Modelo CPE : </b> ".$objElementoCpeNuevo->getModeloElementoId()
                                                                                               ->getNombreModeloElemento()."<br>";
                        
                        $objServicioHistorial = new InfoServicioHistorial();
                        $objServicioHistorial->setServicioId($objServicioPorPuerto);
                        $objServicioHistorial->setObservacion($strHistorialPorServicio);
                        $objServicioHistorial->setEstado($arrayParametros['objServicio']->getEstado());
                        $objServicioHistorial->setUsrCreacion($arrayParametros['usrCreacion']);
                        $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                        $objServicioHistorial->setIpCreacion($arrayParametros['ipCreacion']);
                        $this->emComercial->persist($objServicioHistorial);
                        $this->emComercial->flush();
                    }             
                }                               
            }        
        }                            
           
        // ===================================================================================================
        // SE TRANSFIEREN LOS DETALLES ELEMENTO ACTUAL AL NUEVO Y SE ACTUALIZA LA MAC
        // ===================================================================================================
        $arrayParametros['objElementoClienteActual'] = $objElementoClienteActual;
        $arrayParametros['objElementoCpeNuevo']      = $objElementoCpeNuevo;
        
        $this->traspasarInformacionTecnicaElemento($arrayParametros);
        // ===================================================================================================
        // ELIMINAMOS LOS PUERTOS DEL ELEMENTO ACTUAL
        // ===================================================================================================

        $interfacesElementoActual = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                            ->getInterfacesByIdElemento($objElementoClienteActual->getId());
        foreach ($interfacesElementoActual as $objInterface) 
        {
            $objEnlaceInterno = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                        ->findOneBy(array('interfaceElementoIniId' =>$objInterface->getId(),
                                                                          'estado'                 =>'Activo'));
            if(is_object($objEnlaceInterno))
            {                    
                if(strpos($objInterface->getNombreInterfaceElemento(), "IN") !== FALSE )
                {
                    // ELIMINAMOS ENLACES INTERNOS
                    $objEnlaceInterno->setEstado('Eliminado');
                    $this->emInfraestructura->persist($objEnlaceInterno);
                    $this->emInfraestructura->flush();
                }
            }
            $objInterface->setEstado('Eliminado');
            $this->emInfraestructura->persist($objInterface);
            $this->emInfraestructura->flush();  
        }
        // ===================================================================================================
        // ELIMINAMOS EL ELEMENTO ACTUAL
        // ===================================================================================================
        $objElementoClienteActual->setEstado('Eliminado');
        $this->emInfraestructura->persist($objElementoClienteActual);
        $this->emInfraestructura->flush();

        //SE REGISTRA EL TRACKING DEL ELEMENTO
        $arrayParametrosAuditoria["strNumeroSerie"]  = $objElementoClienteActual->getSerieFisica();
        $arrayParametrosAuditoria["strEstadoTelcos"] = 'Eliminado';
        $arrayParametrosAuditoria["strEstadoNaf"]    = 'Instalado';
        $arrayParametrosAuditoria["strEstadoActivo"] = 'CambioEquipo';
        $arrayParametrosAuditoria["strUbicacion"]    = 'EnTransito';
        $arrayParametrosAuditoria["strCodEmpresa"]   = "10";
        $arrayParametrosAuditoria["strTransaccion"]  = 'Cambio de Elemento';
        $arrayParametrosAuditoria["intOficinaId"]    = 0;

        //Se consulta el login del cliente
        if(is_object($arrayParametros['objServicio']))
        {
            $objInfoPunto = $this->emInfraestructura->getRepository('schemaBundle:InfoPunto')
                                                    ->find($arrayParametros['objServicio']->getPuntoId()->getId());
            if(is_object($objInfoPunto))
            {
                $arrayParametrosAuditoria["strLogin"] = $objInfoPunto->getLogin();

                $intIdPunto = $objInfoPunto->getId();
                $strlogin   = $arrayParametrosAuditoria["strLogin"];
            }
        }

        $arrayParametrosAuditoria["strUsrCreacion"] = $arrayParametros['usrCreacion'];

        // ===================================================================================================
        // CANCELAR SOLICITUD DE CAMBIO DE EQUIPO
        // ===================================================================================================
        $arrayParams = array();
        $arrayParams['objServicio']         = $arrayParametros['objServicio'];
        $arrayParams['strTipoSolicitud']    = "SOLICITUD CAMBIO DE MODEM INMEDIATO";
        $arrayParams['strEstadoSolicitud']  = "AsignadoTarea";
        $arrayParams['strUsrCreacion']      = $arrayParametros['usrCreacion'];
        $arrayParams['strIpCreacion']       = $arrayParametros['ipCreacion'];
        // ...
        $arrayResponse = $this->servicioGeneral->finalizarDetalleSolicitud($arrayParams);
        if($arrayResponse['status'] == 'OK')
        {
            // SE LOCALIZA LA ASIGACION DE LA SOLICITUD DE CAMBIO DE EQUIPO PARA REASIGNAR A LA MISMA PERSONA EL RETIRO DE EQUIPO
            $objDetalle = $this->emComercial->getRepository('schemaBundle:InfoDetalle')
                                            ->findOneBy(array("detalleSolicitudId" => $arrayResponse['objDetalleSolicitud']->getId()));
            $objDetalleAsignacion = $this->emComercial->getRepository('schemaBundle:InfoDetalleAsignacion')
                                                      ->findOneBy(array("detalleId" => $objDetalle->getId()));
            // ...
            // ===================================================================================================
            // CREAR SOLICITUD DE RETIRO DE EQUIPO POR CAMBIO DE EQUIPO
            // ===================================================================================================
            $arrayParams = array();
            $arrayParams['objServicio']         = $arrayParametros['objServicio'];
            $arrayParams['objElementoCliente']  = $objElementoClienteActual;
            $arrayParams['idPersonaEmpresaRol'] = $objDetalleAsignacion->getPersonaEmpresaRolId();
            $arrayParams['strMotivoSolicitud']  ='CAMBIO DE EQUIPO';
            $arrayParams['usrCreacion']         = $arrayParametros['usrCreacion'];
            $arrayParams['ipCreacion']          = $arrayParametros['ipCreacion'];
            // ...

            $objPersonaEmpresaRolUsr = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                         ->find($objDetalleAsignacion->getPersonaEmpresaRolId());

            if(!empty($arrayParametros["idResponsable"]) && isset($arrayParametros["idResponsable"]) && !empty($arrayParametros["tipoResponsable"]))
            {
                if(is_object($arrayResponse['objDetalleSolicitud']))
                {
                     $intMotivoId = $arrayResponse['objDetalleSolicitud']->getMotivoId();

                     if(!empty($intMotivoId))
                     {
                          $objAdmiMotivo = $this->emComercial->getRepository('schemaBundle:AdmiMotivo')->find($intMotivoId);

                          if(is_object($objAdmiMotivo))
                          {
                              $arrayParametrosAuditoria["strObservacion"] = $objAdmiMotivo->getNombreMotivo();
                          }
                     }
                }

                if($arrayParametros["tipoResponsable"] == "C" )
                {
                    $arrayDatos = $this->emComercial->getRepository('schemaBundle:AdmiCuadrilla')
                                                    ->findJefeCuadrilla($arrayParametros["idResponsable"]);

                    if(!empty($arrayDatos) && isset($arrayDatos))
                    {
                        $arrayParametrosAuditoria["intIdPersona"] = $arrayDatos['idPersona'];
                        $arrayParams["strPersonaEmpresaRolId"]    = $arrayDatos['idPersonaEmpresaRol'];
                    }
                    else
                    {
                        if(is_object($objPersonaEmpresaRolUsr))
                        {
                            $arrayParametrosAuditoria["intIdPersona"] = $objPersonaEmpresaRolUsr->getPersonaId()->getId();
                        }
                    }

                    $arrayParams["intIdCuadrilla"]  = $arrayParametros["idResponsable"];
                    $arrayParams["strTipoAsignado"] = "CUADRILLA";
                }
                else if($arrayParametros["tipoResponsable"] == "E" )
                {
                    $objInfoPersonaEmpresaRol = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                  ->find($arrayParametros["idResponsable"]);

                    if(is_object($objInfoPersonaEmpresaRol))
                    {
                        $arrayParametrosAuditoria["intIdPersona"] = $objInfoPersonaEmpresaRol->getPersonaId();
                    }

                    $arrayParams["strPersonaEmpresaRolId"] = $arrayParametros["idResponsable"];
                    $arrayParams["strTipoAsignado"]        = "EMPLEADO";
                }
            }
            else
            {
                if(is_object($objPersonaEmpresaRolUsr))
                {
                    $arrayParametrosAuditoria["intIdPersona"] = $objPersonaEmpresaRolUsr->getPersonaId()->getId();
                }
            }

            $arrayParams["intIdPunto"]                     = $intIdPunto;
            $arrayParams["strLogin"]                       = $strlogin;
            $arrayParams["strBandResponsableCambioEquipo"] = "S";

            $this->serviceInfoElemento->ingresaAuditoriaElementos($arrayParametrosAuditoria);

            $this->servicioGeneral->generarSolicitudRetiroEquipo($arrayParams);

            $arrayResponseCPE[] = array('status' => "OK", 'mensaje' => "OK");
            return $arrayResponseCPE;
        }
        else
        {
            $arrayResponseCPE[] = array('status'  => 'ERROR', 
                                        'mensaje' => 'En finalizarDetalleSolicitud: '.$arrayResponse['mensaje']);
            return $arrayResponseCPE;
        }
    }
    /**
     * 
     * Función que ayuda a obtener la información del objeto ( interface ) más próximo enlazado al CPE a ser cambiado
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0
     * @since 24-10-2016
     * 
     * @param Array $arrayParametros                  [
     *                                                   objServicioTecnico        Objeto referente a la información del servicio
     *                                                   objUltimaMilla            Objeto referente a la ultima milla del servicio
     *                                                   boolEsFibraRuta           booleano que indica que el enlace es RUTA o DIRECTO
     *                                                ]
     * @return Array $arrayInformacionPuertoOutVecino [
     *                                                   status                    OK/ERROR deacuerdo al evento que se produzca
     *                                                   mensaje                   Mensaje de Error en caso de que se genere
     *                                                   objInterfaceOutVecinoCpe  Objeto de la interface proxima al CPE anterior para ser
     *                                                                             enlazado al nuevo equipo
     *                                                   boolEsMigrado             Booleano que indica que servicio ha sido migrado sin data GIS
     *                                                 ]
     */
    private function getArrayInformacionPuertoOutVecinoCpe($arrayParametros)
    {
        $arrayInformacionPuertoOutVecino                              = array();                        
        $arrayInformacionPuertoOutVecino['status']                    = "OK";
        $arrayInformacionPuertoOutVecino['mensaje']                   = "OK"; 
        $arrayInformacionPuertoOutVecino['objInterfaceOutVecinoCpe']  = "OK";
        $arrayInformacionPuertoOutVecino['boolEsMigrado']             = "OK";
        
        $objInterfaceOutVecinoCpe   = null ;
        
        if(isset($arrayParametros['objServicioTecnico']) && is_object($arrayParametros['objServicioTecnico']))
        {
            $objUltimaMilla             = $arrayParametros['objUltimaMilla'];
            $boolEsFibraRuta            = $arrayParametros['boolEsFibraRuta'];
            $objServicioTecnico         = $arrayParametros['objServicioTecnico'];
        }
        else
        {
            $arrayInformacionPuertoOutVecino['status']  = "ERROR";
            $arrayInformacionPuertoOutVecino['mensaje'] = "Servicio no posee información Técnica, por favor revisar";
            return $arrayInformacionPuertoOutVecino;
        }
                        
        $boolEsMigrado         = false;
        
        //Si es Fibra Ruta
        if($objUltimaMilla->getNombreTipoMedio() == "Fibra Optica")
        {
            $intIdInterfaceInicial = null;
                        
            //Si Es Fibra RUTA
            if($boolEsFibraRuta)
            {
                //CASSETTE OUT - BUSCAMOS EL TX - OUT
                if($objServicioTecnico->getInterfaceElementoConectorId()!=null)
                {
                    $intIdInterfaceInicial = $objServicioTecnico->getInterfaceElementoConectorId();//CASSETTE
                    
                }
                else //Para servicios migrados que no tienen data GIS
                {
                    $interfacesElementoSw      = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                         ->find($objServicioTecnico->getInterfaceElementoId());
                    $objInterfaceOutVecinoCpe = $interfacesElementoSw;//Se obtiene la interface OUT del SW para enganchar nuevo elemento cliente
                    
                    $boolEsMigrado = true;
                }
            } 
            else //Si es Fibra DIRECTO
            {
                $objElementoConectado  = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                              ->find($objServicioTecnico->getElementoClienteId());
                
                if($objElementoConectado->getModeloElementoId()->getTipoElementoId()->getNombreTipoElemento()=='ROSETA')
                {
                    $intIdInterfaceInicial = $objServicioTecnico->getInterfaceElementoClienteId();//ROSETA
                }
                else
                {
                    $interfacesElementoSw      = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                         ->find($objServicioTecnico->getInterfaceElementoId());
                    $objInterfaceOutVecinoCpe = $interfacesElementoSw;//SWITCH
                    
                    $boolEsMigrado = true;
                }
                
            }
            
            //------------------------------------------------------------------------
            
            if(!$boolEsMigrado)//Si no es migrado es obtiene el OUT a partir de la Roseta o del cassette segun tipo de factibilidad RUTA o DIRECTO
            {
                // Request para obtener el transiver en el servicio para poder ubicar el enlace correcto con el Elemeneto nuevo
                $arrayParamRequest = array('interfaceElementoConectorId'=> $intIdInterfaceInicial,
                                           'tipoElemento'               => 'TRANSCEIVER');

                // Se obtiene la informacion del Transceiver desde el OUT de la ROSETA para obtener el OUT del Transceiver con el CPE
                $arrayResponse = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                         ->getElementoClienteByTipoElemento($arrayParamRequest);

                if($arrayResponse['msg'] == 'FOUND')
                {
                    $interfacesElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                  ->getInterfacesByIdElemento($arrayResponse['idElemento']);
                    foreach ($interfacesElemento as $interfaceIt) 
                    {
                        if(strtolower($interfaceIt->getNombreInterfaceElemento()) === 'eth1')
                        {
                            $objInterfaceOutVecinoCpe = $interfaceIt;
                            break;
                        }
                    }
                }
                if($objInterfaceOutVecinoCpe != null)
                {
                    $objInterfaceOutVecinoCpe->setEstado('connected');
                    $this->emInfraestructura->persist($objInterfaceOutVecinoCpe);
                    $this->emInfraestructura->flush();
                }
                else
                {
                    $arrayInformacionPuertoOutVecino['status']  = "ERROR";
                    $arrayInformacionPuertoOutVecino['mensaje'] = "Servicio sin enlaces correctos. Favor notificar";
                    return $arrayInformacionPuertoOutVecino;
                }
            }                                                  
        }//Fibra Optica DIRECTO - UTP - Radio
        else
        {            
            //Si es UTP la INterface OUT es la del SW
            if($objUltimaMilla->getNombreTipoMedio()=="UTP")
            {
                $interfacesElementoSw      = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                     ->find($objServicioTecnico->getInterfaceElementoId());
                $objInterfaceOutVecinoCpe = $interfacesElementoSw; //Se obtiene la interface OUT del SW para enganchar nuevo elemento cliente
            }
            else //Si es Radio 
            {
                $interfacesElementoRadioRoseta = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                      ->find($objServicioTecnico->getInterfaceElementoClienteId());
                $objInterfaceOutVecinoCpe = $interfacesElementoRadioRoseta; //Si es Radio el OUT es la interface del Radio a enganchar 
            }                        
        }
        
        $arrayInformacionPuertoOutVecino['objInterfaceOutVecinoCpe']  = $objInterfaceOutVecinoCpe;
        $arrayInformacionPuertoOutVecino['boolEsMigrado']             = $boolEsMigrado;
        
        return $arrayInformacionPuertoOutVecino;
    }

    /**
     * Función que duplica un Enlace cambiando la interface inicial
     * 
     * @author Duval Medina C. <dmedina@telconet.ec>
     * @version 1.0 2016-09-20
     * 
     * @param type $objEnlaceAct
     * @param type $objInterfaceNueva
     * @param type $usrCreacion
     * @param type $clientIp
     * @return \telconet\tecnicoBundle\Service\InfoEnlace
     */
    private function duplicarEnlaceNuevoInicio($objEnlaceAct,$objInterfaceNueva,$usrCreacion,$clientIp)
    {
        $objEnlace = new InfoEnlace();
        $objEnlace->setInterfaceElementoIniId($objInterfaceNueva);
        $objEnlace->setInterfaceElementoFinId($objEnlaceAct->getInterfaceElementoFinId());
        $objEnlace->setTipoMedioId($objEnlaceAct->getTipoMedioId());
        $objEnlace->setTipoEnlace($objEnlaceAct->getTipoEnlace());

        $objEnlace->setCapacidadInput($objEnlaceAct->getCapacidadInput());
        $objEnlace->setCapacidadOutput($objEnlaceAct->getCapacidadOutput());
        $objEnlace->setUnidadMedidaInput($objEnlaceAct->getUnidadMedidaInput());
        $objEnlace->setUnidadMedidaOutput($objEnlaceAct->getUnidadMedidaOutput());

        $objEnlace->setCapacidadIniFin($objEnlaceAct->getCapacidadIniFin());
        $objEnlace->setCapacidadFinIni($objEnlaceAct->getCapacidadFinIni());
        $objEnlace->setUnidadMedidaUp($objEnlaceAct->getUnidadMedidaUp());
        $objEnlace->setUnidadMedidaDown($objEnlaceAct->getUnidadMedidaDown());
        $objEnlace->setEstado($objEnlaceAct->getEstado());
        $objEnlace->setUsrCreacion($usrCreacion);
        $objEnlace->setFeCreacion(new \DateTime('now'));
        $objEnlace->setIpCreacion($clientIp);
        
        return $objEnlace;
    }
    /**
     * Service que realiza el cambio del elemento del cliente, 
     * con ejecucion de scripts para la empresa Telconet
     *
     * @author Modificado: Juan Carlos Lafuente <jlafuente@telconet.ec>
     * @version 1.0 05-05-2016
     * 
     * @author Modificado: Allan Suarez  <arsuarez@telconet.ec>
     * @version 1.1 29-07-2016 - Se ajusta para discriminar que puerto tomar para realizar el nuevo enlace segun el tipo de milla del servicio
     * 
     * @author Modificado: Jesus Bozada  <jbozada@telconet.ec>
     * @version 1.2 15-09-2016 - Se agregan parametros a metodo que registra finalización de solicitud de cambio de elemento
     * 
     * @author Modificado: Allan Suarez  <arsuarez@telconet.ec>
     * @version 1.3 25-10-2016 - Se modifique para que cambio de TX se adapte a modificacion de métodos para crear enlaces y registros en NAF de
     *                           nuevos elementos ( cpe/router/tx )
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.4 01-03-2018 Se registra tracking del elemento
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.5 29-05-2018 - Se agrega el parametro del id_persona en el registro de la trazabilidad
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.6 25-09-2018 - Se realizan ajustes en la pantalla de cambio de cpe, se agrega la opción que registra la cuadrilla responsable
     *                           del retiro del equipo
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.7 03-10-2018 - Se realizan ajustes para agregar el concepto que el responsable del retiro de equipo puede ser una
     *                           cuadrilla o un empleado
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.8 22-05-2019 - Se realizan ajustes para crear una tarea de retiro de equipo que sea asignada al responsable del retiro de equipo
     */
    public function cambioElementoTransceiver($arrayParametros)
    {
        $intIdPunto      = "";
        $strlogin        = "";
        $boolEsFibraRuta = $arrayParametros['boolEsFibraRuta'];
        $intMotivoId     = "";

        $objElementoClienteActual = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                            ->findOneById($arrayParametros['idElementoActual']);
        
        $objInterfaceRosetaOUT = null ;
        
        //Si es Fibra Ruta obtengo el tx buscandolo a partir del puerto del cassette
        if($boolEsFibraRuta)
        {
            // Request para obtener el transiver en el servicio para poder ubicar el enlace correcto con el Elemeneto nuevo
            $arrayParamRequest = array('interfaceElementoConectorId'=> $arrayParametros['objServicioTecnico']->getInterfaceElementoConectorId(),
                                       'tipoElemento'               => 'ROSETA');
            
            // Se obtiene la informacion del OUT de la Roseta para conectar el elemento
            $arrayResponse = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                     ->getElementoClienteByTipoElemento($arrayParamRequest);
            
            if($arrayResponse['msg'] == 'FOUND')
            {
                $interfacesElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                              ->getInterfacesByIdElemento($arrayResponse['idElemento']);
                foreach ($interfacesElemento as $interfaceIt) 
                {
                    if($interfaceIt->getNombreInterfaceElemento() === 'OUT 1')
                    {
                        $objInterfaceRosetaOUT = $interfaceIt;
                        break;
                    }
                }
            }
        }
        else //Fibra DIRECTO el tx esta conectado a la roseta establecido como elemento_cliente_id
        {
            $objInterfaceRosetaOUT = $this->emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")
                                                             ->find($arrayParametros['objServicioTecnico']->getInterfaceElementoClienteId());
        }
               
        $objInterfaceRosetaOUT->setEstado('connected');
        $this->emInfraestructura->persist($objInterfaceRosetaOUT);
        $this->emInfraestructura->flush();
        // ===================================================================================================
        // REGISTRO DEL NUEVO ELEMENTO EN NAF Y TELCOS
        // ===================================================================================================
        $arrayParametros['nombreElementoNuevo'] = $objElementoClienteActual->getNombreElemento();               
        $arrayParametros['boolEsFibraRuta']     = $boolEsFibraRuta;       
        
        $arrayRequestElemento = $this->registrarElementoNuevoNAF($arrayParametros);
        
        if(!isset($arrayRequestElemento) || $arrayRequestElemento['status'] != 'OK')
        {
            $arrayResponseCPE[] = array('status'  => 'ERROR',
                                        'mensaje' => $arrayRequestElemento['mensaje']);
            return $arrayResponseCPE;
        }
        else
        {
            //Se obtiene el objeto que referencia al nuevo elemento creado
            $objElementoTxNuevo = $arrayRequestElemento['objElementoCpe'];
        }
        
        $this->serviceUtil->validaObjeto($objElementoTxNuevo,'No se encuentra Información de Elemento Tx nuevo a ser creado');
        
        $objInterfaceElementoClienteIn = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                  ->findOneBy(array( "elementoId"               => $objElementoTxNuevo->getId(),
                                                                     "nombreInterfaceElemento"  => "IN 1"));
            
        $objInterfaceElementoClienteOut = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                              ->findOneBy(array( "elementoId"               => $objElementoTxNuevo->getId(),
                                                                 "nombreInterfaceElemento"  => "eth1"));

        //enlace con el elemento vecino
        $objEnlace = new InfoEnlace();
        $objEnlace->setInterfaceElementoIniId($objInterfaceRosetaOUT);
        $objEnlace->setInterfaceElementoFinId($objInterfaceElementoClienteIn);
        $objEnlace->setTipoMedioId($arrayParametros['objUltimaMilla']);
        $objEnlace->setTipoEnlace("PRINCIPAL");
        $objEnlace->setEstado("Activo");
        $objEnlace->setUsrCreacion($arrayParametros['usrCreacion']);
        $objEnlace->setFeCreacion(new \DateTime('now'));
        $objEnlace->setIpCreacion($arrayParametros['ipCreacion']);
        $this->emInfraestructura->persist($objEnlace);

        //conectar interface in
        $objInterfaceElementoClienteIn->setEstado("connected");
        $this->emInfraestructura->persist($objInterfaceElementoClienteIn);

        //enlace interno
        $objEnlaceInterno = new InfoEnlace();
        $objEnlaceInterno->setInterfaceElementoIniId($objInterfaceElementoClienteIn);
        $objEnlaceInterno->setInterfaceElementoFinId($objInterfaceElementoClienteOut);
        $objEnlaceInterno->setTipoMedioId($arrayParametros['objUltimaMilla']);
        $objEnlaceInterno->setTipoEnlace("PRINCIPAL");
        $objEnlaceInterno->setEstado("Activo");
        $objEnlaceInterno->setUsrCreacion($arrayParametros['usrCreacion']);
        $objEnlaceInterno->setFeCreacion(new \DateTime('now'));
        $objEnlaceInterno->setIpCreacion($arrayParametros['ipCreacion']);
        $this->emInfraestructura->persist($objEnlaceInterno);
        $this->emInfraestructura->flush(); 
                  
        // ===================================================================================================
        // ELIMINAMOS LOS ENLACES CON EL ELEMENTO ACTUAL
        // ===================================================================================================
        $arrayEnlaces = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                ->findBy(array('interfaceElementoIniId' =>$objInterfaceRosetaOUT->getId(),
                                                               'estado'                 =>'Activo'));
        foreach ($arrayEnlaces as $objEnlaceIt) 
        {
            if($objEnlaceIt->getInterfaceElementoFinId()->getElementoId()->getId() != $objElementoTxNuevo->getId())
            {
                $objEnlaceIt->setEstado('Eliminado');
                $this->emInfraestructura->persist($objEnlaceIt);
                $this->emInfraestructura->flush();  
            }
        }
        // ===================================================================================================
        // ELIMINAMOS LOS PUERTOS DEL ELEMENTO ACTUAL
        // ===================================================================================================
        $interfacesElementoActual = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                            ->getInterfacesByIdElemento($objElementoClienteActual->getId());
        foreach ($interfacesElementoActual as $objInterfaceIt) 
        {
            if(strpos($objInterfaceIt->getNombreInterfaceElemento(), "IN") !== FALSE )
            {
                // ELIMINAMOS ENLACES INTERNOS
                $objEnlaceInterno = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                            ->findOneBy(array('interfaceElementoIniId' =>$objInterfaceIt->getId(),
                                                                              'estado'                 =>'Activo'));
                $objEnlaceInterno->setEstado('Eliminado');
                $this->emInfraestructura->persist($objEnlaceInterno);
                $this->emInfraestructura->flush();  
            }
            $objInterfaceIt->setEstado('Eliminado');
            $this->emInfraestructura->persist($objInterfaceIt);
            $this->emInfraestructura->flush();  
        }
        // ===================================================================================================
        // ELIMINAMOS EL ELEMENTO ACTUAL
        // ===================================================================================================
        $objElementoClienteActual->setEstado('Eliminado');
        $this->emInfraestructura->persist($objElementoClienteActual);
        $this->emInfraestructura->flush();

        //SE REGISTRA EL TRACKING DEL ELEMENTO
        $arrayParametrosAuditoria["strNumeroSerie"]  = $objElementoClienteActual->getSerieFisica();
        $arrayParametrosAuditoria["strEstadoTelcos"] = 'Eliminado';
        $arrayParametrosAuditoria["strEstadoNaf"]    = 'Instalado';
        $arrayParametrosAuditoria["strEstadoActivo"] = 'CambioEquipo';
        $arrayParametrosAuditoria["strUbicacion"]    = 'EnTransito';
        $arrayParametrosAuditoria["strCodEmpresa"]   = "10";
        $arrayParametrosAuditoria["strTransaccion"]  = 'Cambio de Elemento';
        $arrayParametrosAuditoria["intOficinaId"]    = 0;

        //Se consulta el login del cliente
        if(is_object($arrayParametros['objServicio']))
        {
            $objInfoPunto = $this->emInfraestructura->getRepository('schemaBundle:InfoPunto')
                                                    ->find($arrayParametros['objServicio']->getPuntoId()->getId());
            if(is_object($objInfoPunto))
            {
                $arrayParametrosAuditoria["strLogin"] = $objInfoPunto->getLogin();

                $intIdPunto = $objInfoPunto->getId();
                $strlogin   = $arrayParametrosAuditoria["strLogin"];
            }
        }

        $arrayParametrosAuditoria["strUsrCreacion"] = $arrayParametros['usrCreacion'];

        // ===================================================================================================
        // CREAMOS EL ENLACE CON EL ELEMENTO CPE 
        // ===================================================================================================
        $objInterfaceOUTTransceiver = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                              ->findOneBy(array("elementoId"             =>$objElementoClienteActual,
                                                                             "nombreInterfaceElemento"=>"eth1"));
        $objInterfaceElementoClienteOut->setEstado('connected');
        $this->emInfraestructura->persist($objInterfaceOUTTransceiver);
        $this->emInfraestructura->flush();
        // ...
        $objEnlaceTransceiveirCPE = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                            ->findOneBy(array('interfaceElementoIniId' =>$objInterfaceOUTTransceiver->getId(),
                                                                              'estado'                 =>'Activo'));
        $objEnlaceTransceiveirCPE->setInterfaceElementoIniId($objInterfaceElementoClienteOut);
        $this->emInfraestructura->persist($objEnlaceTransceiveirCPE);
        $this->emInfraestructura->flush();  
        // ===================================================================================================
        // CANCELAR SOLICITUD DE CAMBIO DE EQUIPO
        // ===================================================================================================
        $arrayParams = array();
        $arrayParams['objServicio']         = $arrayParametros['objServicio'];
        $arrayParams['strTipoSolicitud']    = "SOLICITUD CAMBIO DE MODEM INMEDIATO";
        $arrayParams['strEstadoSolicitud']  = "AsignadoTarea";
        $arrayParams['strUsrCreacion']      = $arrayParametros['usrCreacion'];
        $arrayParams['strIpCreacion']       = $arrayParametros['ipCreacion'];
        // ...
        $arrayResponse = $this->servicioGeneral->finalizarDetalleSolicitud($arrayParams);
        if($arrayResponse['status'] == 'OK')
        {
            // SE LOCALIZA LA ASIGACION DE LA SOLICITUD DE CAMBIO DE EQUIPO PARA REASIGNAR A LA MISMA PERSONA EL RETIRO DE EQUIPO
            $objDetalle = $this->emComercial->getRepository('schemaBundle:InfoDetalle')
                                            ->findOneBy(array("detalleSolicitudId" => $arrayResponse['objDetalleSolicitud']->getId()));
            $objDetalleAsignacion = $this->emComercial->getRepository('schemaBundle:InfoDetalleAsignacion')
                                                      ->findOneBy(array("detalleId" => $objDetalle->getId()));
            // ...
            // ===================================================================================================
            // CREAR SOLICITUD DE RETIRO DE EQUIPO POR CAMBIO DE EQUIPO
            // ===================================================================================================
            $arrayParams = array();
            $arrayParams['objServicio']         = $arrayParametros['objServicio'];
            $arrayParams['objElementoCliente']  = $objElementoClienteActual;
            $arrayParams['idPersonaEmpresaRol'] = $objDetalleAsignacion->getPersonaEmpresaRolId();
            $arrayParams['strMotivoSolicitud']  ='CAMBIO DE EQUIPO';
            $arrayParams['usrCreacion']         = $arrayParametros['usrCreacion'];
            $arrayParams['ipCreacion']          = $arrayParametros['ipCreacion'];
            // ...

            $objPersonaEmpresaRolUsr = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                         ->find($objDetalleAsignacion->getPersonaEmpresaRolId());

            if(!empty($arrayParametros["idResponsable"]) && isset($arrayParametros["idResponsable"]) && !empty($arrayParametros["tipoResponsable"]))
            {
                if(is_object($arrayResponse['objDetalleSolicitud']))
                {
                     $intMotivoId = $arrayResponse['objDetalleSolicitud']->getMotivoId();

                     if(!empty($intMotivoId))
                     {
                          $objAdmiMotivo = $this->emComercial->getRepository('schemaBundle:AdmiMotivo')->find($intMotivoId);

                          if(is_object($objAdmiMotivo))
                          {
                              $arrayParametrosAuditoria["strObservacion"] = $objAdmiMotivo->getNombreMotivo();
                          }
                     }
                }

                if($arrayParametros["tipoResponsable"] == "C" )
                {
                    $arrayDatos = $this->emComercial->getRepository('schemaBundle:AdmiCuadrilla')
                                                    ->findJefeCuadrilla($arrayParametros["idResponsable"]);

                    if(!empty($arrayDatos) && isset($arrayDatos))
                    {
                        $arrayParametrosAuditoria["intIdPersona"] = $arrayDatos['idPersona'];
                        $arrayParams["strPersonaEmpresaRolId"]    = $arrayDatos['idPersonaEmpresaRol'];
                    }
                    else
                    {
                        if(is_object($objPersonaEmpresaRolUsr))
                        {
                            $arrayParametrosAuditoria["intIdPersona"] = $objPersonaEmpresaRolUsr->getPersonaId()->getId();
                        }
                    }

                    $arrayParams["intIdCuadrilla"]  = $arrayParametros["idResponsable"];
                    $arrayParams["strTipoAsignado"] = "CUADRILLA";
                }
                else if($arrayParametros["tipoResponsable"] == "E" )
                {
                    $objInfoPersonaEmpresaRol = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                  ->find($arrayParametros["idResponsable"]);

                    if(is_object($objInfoPersonaEmpresaRol))
                    {
                        $arrayParametrosAuditoria["intIdPersona"] = $objInfoPersonaEmpresaRol->getPersonaId();
                    }

                    $arrayParams["strPersonaEmpresaRolId"] = $arrayParametros["idResponsable"];
                    $arrayParams["strTipoAsignado"]        = "EMPLEADO";
                }
            }
            else
            {
                if(is_object($objPersonaEmpresaRolUsr))
                {
                    $arrayParametrosAuditoria["intIdPersona"] = $objPersonaEmpresaRolUsr->getPersonaId()->getId();
                }
            }

            $arrayParams["intIdPunto"]                     = $intIdPunto;
            $arrayParams["strLogin"]                       = $strlogin;
            $arrayParams["strBandResponsableCambioEquipo"] = "S";

            $this->serviceInfoElemento->ingresaAuditoriaElementos($arrayParametrosAuditoria);

            $this->servicioGeneral->generarSolicitudRetiroEquipo($arrayParams);
            // ...

            // Historial del servicio
            $tipoElementoCambioEquipo = $objElementoClienteActual->getModeloElementoId()->getTipoElementoId()->getNombreTipoElemento();
            $servicioHistorial = new InfoServicioHistorial();
            $servicioHistorial->setServicioId($arrayParametros['objServicio']);
            $servicioHistorial->setObservacion("Se Realizo un Cambio de Elemento Cliente " . $tipoElementoCambioEquipo);
            $servicioHistorial->setEstado($arrayParametros['objServicio']->getEstado());
            $servicioHistorial->setUsrCreacion($arrayParams['usrCreacion']);
            $servicioHistorial->setFeCreacion(new \DateTime('now'));
            $servicioHistorial->setIpCreacion($arrayParametros['ipCreacion']);
            $this->emComercial->persist($servicioHistorial);
            $this->emComercial->flush();
            // ....
            $arrayResponseCPE[] = array('status' => "OK", 'mensaje' => "OK");
            return $arrayResponseCPE;
        }
        else
        {
            $arrayResponseCPE[] = array('status'  => 'ERROR', 
                                        'mensaje' => $arrayResponse['mensaje']);
            return $arrayResponseCPE;
        }        
    }
    
    
    /**
     * Service que realiza el cambio del elemento de elemento tipo radio del cliente
     *
     * @params $arrayParametros
     * 
     *  $arrayParametros['objServicio']
     *  $arrayParametros['objServicioTecnico']
     *  $arrayParametros['objProducto']
     *  $arrayParametros['objElemento']
     *  $arrayParametros['objModeloElemento']
     *  $arrayParametros['objInterfaceElemento']
     *  $arrayParametros['idElementoActual']
     *  $arrayParametros['serieElementoNuevo']
     *  $arrayParametros['macElementoNuevo']
     *  $arrayParametros['modeloElementoNuevo']
     *  $arrayParametros['nombreElementoNuevo']
     *  $arrayParametros['descripcionNuevo']
     *  $arrayParametros['tipoElementoNuevo']
     *  $arrayParametros['idEmpresa']
     *  $arrayParametros['prefijoEmpresa']
     *  $arrayParametros['usrCreacion']
     *  $arrayParametros['ipCreacion']
     * 
     * @return $arrayResultadoCambio
     * 
     *  $arrayResultadoCambio['status']
     *  $arrayResultadoCambio['mensaje']
     * 
     * @author John Alberto Vera R. <javera@telconet.ec>
     * @version 1.0 05-11-2016
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 27-02-2018 Se registra tracking del elemento
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 29-05-2018 - Se agrega el parametro del id_persona en el registro de la trazabilidad
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.3 25-09-2018 - Se realizan ajustes en la pantalla de cambio de cpe, se agrega la opción que registra la cuadrilla responsable
     *                           del retiro del equipo
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.4 03-10-2018 - Se realizan ajustes para agregar el concepto que el responsable del retiro de equipo puede ser una
     *                           cuadrilla o un empleado
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.5 22-05-2019 - Se realizan ajustes para crear una tarea de retiro de equipo que sea asignada al responsable del retiro de equipo
     */
    public function cambioElementoRadio($arrayParametros)
    {
        $arrayParametrosAuditoria = array();
        $intMotivoId     = "";
        $intIdPunto      = "";
        $strlogin        = "";

        try
        {
            if(!is_object($arrayParametros['objServicioTecnico']))
            {
                throw new \Exception("No existe información técnica. ");
            }
        
            $objElementoClienteActual = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                ->findOneById($arrayParametros['idElementoActual']);
            if(!is_object($objElementoClienteActual))
            {
                throw new \Exception("No existe el elemento nuevo ".$arrayParametros['idElementoActual']);
            }            
            
            $objUltimaMilla           = $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')
                                                                ->find($arrayParametros['objServicioTecnico']->getUltimaMillaId());
            
            if(!is_object($objUltimaMilla))
            {
                throw new \Exception("No existe la última milla. ");
            }            
            //obtengo el enlace de entrada
            $arrayInterfacesElementoActual = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                     ->findBy(array('elementoId'=>$arrayParametros['idElementoActual']));
            $objInterfaceElementoConector = null;
            foreach($arrayInterfacesElementoActual as $objInterfaceElementoActual)
            {
                if($objInterfaceElementoActual->getEstado() == 'connected')
                {
                    $objInfoEnlace = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                             ->findOneBy(array(  'interfaceElementoFinId'=> $objInterfaceElementoActual->getId(),
                                                                                 'estado'                => 'Activo'));
                    if(is_object($objInfoEnlace))
                    {
                        $objInterfaceElementoConector = $objInfoEnlace->getInterfaceElementoIniId();
                        //elimino el enlace de entrada
                        $objInfoEnlace->setEstado('Eliminado');
                        $this->emInfraestructura->persist($objInfoEnlace);
                        $this->emInfraestructura->flush();
                    }
                }
            }
            
            if(!is_object($objInterfaceElementoConector))
            {
                throw new \Exception("No se encontró la interface del elemento conector. ");
            }

            $objInfoEnlaceCpe = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                ->findOneBy(array('interfaceElementoIniId'=> $arrayParametros['objServicioTecnico']->getInterfaceElementoClienteId(),
                                                  'estado'                => 'Activo'));

            if(is_object($objInfoEnlaceCpe))
            {
                //elimino el enlace de salida
                $objInfoEnlaceCpe->setEstado('Eliminado');
                $this->emInfraestructura->persist($objInfoEnlaceCpe);
                $this->emInfraestructura->flush();
            }
            
            // ===================================================================================================
            // PROCESO PARA REGISTRAR EL CPE 
            // ===================================================================================================
            $arrayParametrosCpe = array('nombreElementoCliente'         => $objElementoClienteActual->getNombreElemento(),
                                        'nombreModeloElementoCliente'   => $arrayParametros['modeloElementoNuevo'],
                                        'serieElementoCliente'          => $arrayParametros['serieElementoNuevo'],
                                        'macElementoCliente'            => $arrayParametros['macElementoNuevo'],
                                        'objInterfaceElementoVecinoOut' => $objInterfaceElementoConector,
                                        'objUltimaMilla'                => $objUltimaMilla,
                                        'objServicio'                   => $arrayParametros['objServicio'],
                                        'intIdEmpresa'                  => $arrayParametros['idEmpresa'],
                                        'usrCreacion'                   => $arrayParametros['usrCreacion'],
                                        'ipCreacion'                    => $arrayParametros['ipCreacion'],
                                        'esServicioNuevo'               => "NO",
                                        'interface'                     => $arrayParametros['nombreInterface']);
            
            $objInterfaceElementoOut = $this->servicioGeneral->ingresarElementoClienteTN($arrayParametrosCpe,$arrayParametros['tipoElementoNuevo']);
            
            if(is_object($objInterfaceElementoOut) && is_object($objInfoEnlaceCpe))
            {
                //enlace con el elemento vecino
                $objEnlace = new InfoEnlace();
                $objEnlace->setInterfaceElementoIniId($objInterfaceElementoOut);
                $objEnlace->setInterfaceElementoFinId($objInfoEnlaceCpe->getInterfaceElementoFinId());
                $objEnlace->setTipoMedioId($objUltimaMilla);
                $objEnlace->setTipoEnlace("PRINCIPAL");
                $objEnlace->setEstado("Activo");
                $objEnlace->setUsrCreacion($arrayParametros['usrCreacion']);
                $objEnlace->setFeCreacion(new \DateTime('now'));
                $objEnlace->setIpCreacion($arrayParametros['ipCreacion']);
                $this->emInfraestructura->persist($objEnlace);
            }
            else
            {
                throw new \Exception('Ocurrio un error al ingresar el elemento.');
            }

            //elimino el elemento        
            $objElementoClienteActual->setEstado('Eliminado');
            $this->emInfraestructura->persist($objElementoClienteActual);
            $this->emInfraestructura->flush();

            //SE REGISTRA EL TRACKING DEL ELEMENTO
            $arrayParametrosAuditoria["strNumeroSerie"]  = $objElementoClienteActual->getSerieFisica();
            $arrayParametrosAuditoria["strEstadoTelcos"] = 'Eliminado';
            $arrayParametrosAuditoria["strEstadoNaf"]    = 'Instalado';
            $arrayParametrosAuditoria["strEstadoActivo"] = 'CambioEquipo';
            $arrayParametrosAuditoria["strUbicacion"]    = 'EnTransito';
            $arrayParametrosAuditoria["strCodEmpresa"]   = "10";
            $arrayParametrosAuditoria["strTransaccion"]  = 'Cambio de Elemento';
            $arrayParametrosAuditoria["intOficinaId"]    = 0;

            //Se consulta el login del cliente
            if(is_object($arrayParametros['objServicioTecnico']->getServicioId()))
            {
                $objInfoPunto = $this->emInfraestructura->getRepository('schemaBundle:InfoPunto')
                                                        ->find($arrayParametros['objServicioTecnico']->getServicioId()->getPuntoId()->getId());
                if(is_object($objInfoPunto))
                {
                    $arrayParametrosAuditoria["strLogin"] = $objInfoPunto->getLogin();

                    $intIdPunto = $objInfoPunto->getId();
                    $strlogin   = $arrayParametrosAuditoria["strLogin"];
                }
            }

            $arrayParametrosAuditoria["strUsrCreacion"] = $arrayParametros['usrCreacion'];

            ////

            //se elimina las interfaces
            foreach($arrayInterfacesElementoActual as $objInterfaceElementoActual)
            {
                $objInterfaceElementoActual->setEstado('Eliminado');
                $this->emInfraestructura->persist($objInterfaceElementoActual);
                $this->emInfraestructura->flush();
            }

            $arrayDetallesElementoActual = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                   ->findBy(array('elementoId'=>$arrayParametros['idElementoActual']));

            foreach ($arrayDetallesElementoActual as $objDetalleElementoActual)
            {

                if($objDetalleElementoActual->getEstado() == 'Activo')
                {
                    if($objDetalleElementoActual->getDetalleNombre() == 'MAC')
                    {
                        //creo con el nuevo elemento
                        $objDetalleElemento = new InfoDetalleElemento();
                        $objDetalleElemento->setElementoId($objInterfaceElementoOut->getElementoId()->getId());
                        $objDetalleElemento->setDetalleNombre($objDetalleElementoActual->getDetalleNombre());
                        $objDetalleElemento->setDetalleValor($arrayParametros['macElementoNuevo']);
                        $objDetalleElemento->setDetalleDescripcion($objDetalleElementoActual->getDetalleDescripcion());
                        $objDetalleElemento->setUsrCreacion($arrayParametros['usrCreacion']);
                        $objDetalleElemento->setFeCreacion(new \DateTime('now'));
                        $objDetalleElemento->setIpCreacion($arrayParametros['ipCreacion']);
                        $objDetalleElemento->setEstado('Activo');
                        $this->emInfraestructura->persist($objDetalleElemento);
                    }
                    else
                    {
                        //creo con el nuevo elemento
                        $objDetalleElemento = new InfoDetalleElemento();
                        $objDetalleElemento->setElementoId($objInterfaceElementoOut->getElementoId()->getId());
                        $objDetalleElemento->setDetalleNombre($objDetalleElementoActual->getDetalleNombre());
                        $objDetalleElemento->setDetalleValor($objDetalleElementoActual->getDetalleValor());
                        $objDetalleElemento->setDetalleDescripcion($objDetalleElementoActual->getDetalleDescripcion());
                        $objDetalleElemento->setUsrCreacion($arrayParametros['usrCreacion']);
                        $objDetalleElemento->setFeCreacion(new \DateTime('now'));
                        $objDetalleElemento->setIpCreacion($arrayParametros['ipCreacion']);
                        $objDetalleElemento->setEstado('Activo');
                        $this->emInfraestructura->persist($objDetalleElemento);
                    }
                }

                //Elimino los antiguos
                $objDetalleElementoActual->setEstado('Eliminado');
                $this->emInfraestructura->persist($objDetalleElementoActual);
                $this->emInfraestructura->flush();

            }

            //actualizo la info tecnica
            $arrayParametros['objServicioTecnico']->setElementoClienteId($objInterfaceElementoOut->getElementoId()->getId());
            $arrayParametros['objServicioTecnico']->setInterfaceElementoClienteId($objInterfaceElementoOut->getId());
            $this->emComercial->persist($arrayParametros['objServicioTecnico']);
            $this->emComercial->flush();

            // ===================================================================================================
            // CANCELAR SOLICITUD DE CAMBIO DE EQUIPO
            // ===================================================================================================
            $arrayParams = array();
            $arrayParams['objServicio']         = $arrayParametros['objServicio'];
            $arrayParams['strTipoSolicitud']    = "SOLICITUD CAMBIO DE MODEM INMEDIATO";
            $arrayParams['strEstadoSolicitud']  = "AsignadoTarea";
            $arrayParams['strUsrCreacion']      = $arrayParametros['usrCreacion'];
            $arrayParams['strIpCreacion']       = $arrayParametros['ipCreacion'];
            
            $arrayResponse = $this->servicioGeneral->finalizarDetalleSolicitud($arrayParams);
            if($arrayResponse['status'] == 'OK')
            {
                // SE LOCALIZA LA ASIGACION DE LA SOLICITUD DE CAMBIO DE EQUIPO PARA REASIGNAR A LA MISMA PERSONA EL RETIRO DE EQUIPO
                $objDetalle = $this->emComercial->getRepository('schemaBundle:InfoDetalle')
                                                ->findOneBy(array("detalleSolicitudId" => $arrayResponse['objDetalleSolicitud']->getId()));
                $objDetalleAsignacion = $this->emComercial->getRepository('schemaBundle:InfoDetalleAsignacion')
                                                          ->findOneBy(array("detalleId" => $objDetalle->getId()));
                // ...
                // ===================================================================================================
                // CREAR SOLICITUD DE RETIRO DE EQUIPO POR CAMBIO DE EQUIPO
                // ===================================================================================================
                $arrayParams = array();
                $arrayParams['objServicio']         = $arrayParametros['objServicio'];
                $arrayParams['objElementoCliente']  = $objElementoClienteActual;
                $arrayParams['idPersonaEmpresaRol'] = $objDetalleAsignacion->getPersonaEmpresaRolId();
                $arrayParams['strMotivoSolicitud']  ='CAMBIO DE EQUIPO';
                $arrayParams['usrCreacion']         = $arrayParametros['usrCreacion'];
                $arrayParams['ipCreacion']          = $arrayParametros['ipCreacion'];
                // ...

                $objPersonaEmpresaRolUsr = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                             ->find($objDetalleAsignacion->getPersonaEmpresaRolId());

                if(!empty($arrayParametros["idResponsable"]) && isset($arrayParametros["idResponsable"]) 
                    && !empty($arrayParametros["tipoResponsable"]))
                {
                    if(is_object($arrayResponse['objDetalleSolicitud']))
                    {
                         $intMotivoId = $arrayResponse['objDetalleSolicitud']->getMotivoId();

                         if(!empty($intMotivoId))
                         {
                              $objAdmiMotivo = $this->emComercial->getRepository('schemaBundle:AdmiMotivo')->find($intMotivoId);

                              if(is_object($objAdmiMotivo))
                              {
                                  $arrayParametrosAuditoria["strObservacion"] = $objAdmiMotivo->getNombreMotivo();
                              }
                         }
                    }

                    if($arrayParametros["tipoResponsable"] == "C" )
                    {
                        $arrayDatos = $this->emComercial->getRepository('schemaBundle:AdmiCuadrilla')
                                                        ->findJefeCuadrilla($arrayParametros["idResponsable"]);

                        if(!empty($arrayDatos) && isset($arrayDatos))
                        {
                            $arrayParametrosAuditoria["intIdPersona"] = $arrayDatos['idPersona'];
                            $arrayParams["strPersonaEmpresaRolId"]    = $arrayDatos['idPersonaEmpresaRol'];
                        }
                        else
                        {
                            if(is_object($objPersonaEmpresaRolUsr))
                            {
                                $arrayParametrosAuditoria["intIdPersona"] = $objPersonaEmpresaRolUsr->getPersonaId()->getId();
                            }
                        }

                        $arrayParams["intIdCuadrilla"]  = $arrayParametros["idResponsable"];
                        $arrayParams["strTipoAsignado"] = "CUADRILLA";
                    }
                    else if($arrayParametros["tipoResponsable"] == "E" )
                    {
                        $objInfoPersonaEmpresaRol = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                      ->find($arrayParametros["idResponsable"]);

                        if(is_object($objInfoPersonaEmpresaRol))
                        {
                            $arrayParametrosAuditoria["intIdPersona"] = $objInfoPersonaEmpresaRol->getPersonaId();
                        }

                        $arrayParams["strPersonaEmpresaRolId"] = $arrayParametros["idResponsable"];
                        $arrayParams["strTipoAsignado"]        = "EMPLEADO";
                    }
                }
                else
                {
                    if(is_object($objPersonaEmpresaRolUsr))
                    {
                        $arrayParametrosAuditoria["intIdPersona"] = $objPersonaEmpresaRolUsr->getPersonaId()->getId();
                    }
                }

                $arrayParams["intIdPunto"]                     = $intIdPunto;
                $arrayParams["strLogin"]                       = $strlogin;
                $arrayParams["strBandResponsableCambioEquipo"] = "S";

                $this->serviceInfoElemento->ingresaAuditoriaElementos($arrayParametrosAuditoria);

                $this->servicioGeneral->generarSolicitudRetiroEquipo($arrayParams);
                // ...

                // Historial del servicio
                $strTipoElementoCambioEquipo = $objElementoClienteActual->getModeloElementoId()->getTipoElementoId()->getNombreTipoElemento();
                $objServicioHistorial = new InfoServicioHistorial();
                $objServicioHistorial->setServicioId($arrayParametros['objServicio']);
                $objServicioHistorial->setObservacion("Se Realizo un Cambio de Elemento Cliente " . $strTipoElementoCambioEquipo);
                $objServicioHistorial->setEstado($arrayParametros['objServicio']->getEstado());
                $objServicioHistorial->setUsrCreacion($arrayParametros['usrCreacion']);
                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                $objServicioHistorial->setIpCreacion($arrayParametros['ipCreacion']);
                $this->emComercial->persist($objServicioHistorial);
                $this->emComercial->flush();
                
                //actualizamos registro en el naf del cpe
                $arrayParametrosNaf = array( 'tipoArticulo'          => 'AF',
                                             'identificacionCliente' => '',
                                             'empresaCod'            => '',
                                             'modeloCpe'             => $arrayParametros['modeloElementoNuevo'],
                                             'serieCpe'              => $arrayParametros['serieElementoNuevo'],
                                             'cantidad'              => '1');
                //...
                $strMensaje = $this->procesaInstalacionElemento($arrayParametrosNaf);
                //...
                if(strlen(trim($strMensaje)) > 0)
                {
                    throw new \Exception("ERROR ELEMENTO EN NAF: " . $strMensaje);
                }
                
                $arrayResultadoCambio[] = array('status' => "OK", 'mensaje' => "OK");
                return $arrayResultadoCambio;

            }
            else
            {
                throw new \Exception($arrayResponse['mensaje']);
            }
        }
        catch (\Exception $e)
        {
            $this->serviceUtil->insertError('Telcos+',
                                            'InfoCambioElementoService.cambioElementoRadio',
                                            $e->getMessage(),
                                            $arrayParametros['usrCreacion'],
                                            $arrayParametros['ipCreacion']);

            $arrayResultadoCambio[] = array('status' => "ERROR", 'mensaje' => "Ocurrió un error, favor notificar a Sistemas. ");
            return $arrayResultadoCambio;          
        }

    }
    
    /**
     * Service que realiza el cambio del elemento del cliente, 
     * con ejecucion de scripts para la empresa Telconet
     *
     * $arrayParametros [idElementoCliente, serieElementoCliente, modeloElementoCliente ]
     *
     * @author Modificado: Juan Carlos Lafuente <jlafuente@telconet.ec>
     * @version 1.0 05-05-2016
     *
     * @author Modificado: Duval Medina C. <dmedina@telconet.ec>
     * @version 1.1 2016-06-28 Se regulariza valiación por falta de información Técnica
     * 
     * @author Modificado: Allan Suarez C. <arsuarez@telconet.ec>
     * @version 1.2 2016-09-13 Se crea roseta y tx para regularizar clientes que fueron migrados son fibra optica y constan como enlaces directos
     *
     * @author Modificado: Duval Medina C. <dmedina@telconet.ec>
     * @version 1.3 2016-09-15 Se incluyó 'mensaje' en respuesta de ERROR para seguimiento
     *                         se envia 'nombreInterface' en el arreglo para creación de elemento en ingresarElementoClienteTN
     *
     * @author Duval Medina C. <dmedina@telconet.ec>
     * @version 1.4 2016-10-11 Marcar con Regulado cuando sea un equipo tipo 'CPE WIFI'
     * 
     * @author Allan Suarez C. <arsuarez@telconet.ec>
     * @version 1.5 2016-10-25 Se segmenta codigo de registro de elemento en telcos y NAF del bloque de generacion de TX y Roseta faltante
     */
    public function registrarElementoNuevoNAF($arrayParametros)
    { 
        // ===================================================================================================
        // PROCESO EN EL NAF
        // ===================================================================================================
        //Buscamos el nuevo modelo y procesa la instalacion en el NAF
        $cpeNafArray = $this->servicioGeneral->buscarElementoEnNaf( $arrayParametros['serieElementoNuevo'], 
                                                                    $arrayParametros['modeloElementoNuevo'], 
                                                                    "PI", 
                                                                    "ActivarServicio");
        $cpeNaf            = $cpeNafArray[0]['status'];
        $codigoArticuloCpe = $cpeNafArray[0]['mensaje'];
        if($cpeNaf == "OK")
        {
            //actualizamos registro en el naf del cpe
            $arrayParametrosNaf = array( 'tipoArticulo'          => 'AF',
                                         'identificacionCliente' => '',
                                         'empresaCod'            => '',
                                         'modeloCpe'             => $arrayParametros['modeloElementoNuevo'],
                                         'serieCpe'              => $arrayParametros['serieElementoNuevo'],
                                         'cantidad'              => '1');
            //...
            $mensajeError = $this->procesaInstalacionElemento($arrayParametrosNaf);
            //...
            if(strlen(trim($mensajeError)) > 0)
            {
                $arrayRespuestaFinal = array("status" => "NAF", "mensaje" => "ERROR ELEMENTO EN NAF: " . $mensajeError);
                return $arrayRespuestaFinal;
            }
        }
        else
        {
            $arrayRespuestaFinal = array('status' => 'NAF', 'mensaje' => $codigoArticuloCpe);
            return $arrayRespuestaFinal;
        }
        
        //Se crea el nuevo CPE/ROUTER nuevo de manera lógica
        $arrayParametros['macElementoNuevo'] = null; //Se coloca null a esta variable para que no replique la mac en todas las interfaces Wan
                                                     //Las mac serán colocadas en base a los campos recibidos de acuerdo a la Wan que requiere
                                                     
        $objElemento                         = $this->servicioGeneral->ingresarElementoClienteTNSinEnlace($arrayParametros);
        
        $arrayRespuestaFinal = array('status'         => 'OK', 
                                     'objElementoCpe' => $objElemento);
        
        return $arrayRespuestaFinal;
    }  
    
    /**     
     * 
     * Método que permite crear información de roseta y Tx a servicios que son migrados y requieren ser regularizados con la información
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0
     * @since 25-10-2016
     * 
     * @param Array $arrayParametros      [
     *                                      objInterfaceOUT                      Interface más próxima enlazada al puerto del cpe a ser cambiado
     *                                      esMigrado                            Indicador de servicios migrados sin data GIS
     *                                      objUltimaMilla                       Objeto referente a la ultima milla del servicios
     *                                      tipoElementoNuevo                    Valor del tipo del modelo del equipo a cambiar
     *                                      idEmpresa                            Empresa de donde se genera la acción
     *                                      usrCreacion                          Usuario de quien genera la acción
     *                                      ipCreacion                           Ip desde donde se genera la acción
     *                                    ]
     * @return Array $arrayRespuestaFinal [
     *                                      status                                OK/ERROR
     *                                      objInterfaceElementoOut               Interface del Tx a ser conectado con el puerto del nuevo CPE
     *                                      objInterfaceElementoClienteConector   Informacion de Roseta nueva en caso que requiera ser creada
     *                                      boolEsRegulado                        Determina si un servicio tuvo regularizacion de data faltante
     *                                    ] 
     */
    private function crearElementoRosetaTxFaltantes($arrayParametros)
    {
        //La interface que se concectara con el nuevo CPE
        $objInterfaceOut               = $arrayParametros['objInterfaceOUT'];
        $objInterfaceElementoOutRoseta = null; //Usado en caso que se requiera regularizar la informacion de bb del cliente
        $esRegulado                    = false; //Variable que ayuda a determinar si un servicio tiene regularizacion de Fibra Directa
        // ===================================================================================================
        // PROCESO PARA REGISTRAR EL ELEMENTO ROSETA, TRANSCEIVER --> SERVICIOS MIGRADOS F.O.
        // ===================================================================================================
        if($arrayParametros['boolEsMigrado'] && //Si es servicio migrado sin data GIS
           $arrayParametros['objUltimaMilla']->getNombreTipoMedio() == 'Fibra Optica' && //Si es Fibra optica 
           $arrayParametros['tipoElementoNuevo'] == 'CPE') //Solo si es CPE
        {
            //ingresar elemento roseta
            $arrayParametrosRoseta = array(
                                            'nombreElementoCliente'         => 'ros-'.$arrayParametros['objServicio']->getPuntoId()->getLogin(),
                                            'nombreModeloElementoCliente'   => "ROS-1234",
                                            'serieElementoCliente'          => "00000",
                                            'objInterfaceElementoVecinoOut' => $arrayParametros['objInterfaceOUT'],
                                            'objUltimaMilla'                => $arrayParametros['objUltimaMilla'],
                                            'objServicio'                   => $arrayParametros['objServicio'],
                                            'intIdEmpresa'                  => $arrayParametros['idEmpresa'],
                                            'usrCreacion'                   => $arrayParametros['usrCreacion'],
                                            'ipCreacion'                    => $arrayParametros['ipCreacion']
                                        );

            $objInterfaceElementoOutRoseta = $this->servicioGeneral->ingresarElementoClienteTN($arrayParametrosRoseta,'ROSETA');

            //Deja en estado conectado la interface del Tx
            $objInterfaceElementoOutRoseta->setEstado('connected');
            $this->emInfraestructura->persist($objInterfaceElementoOutRoseta);
            $this->emInfraestructura->flush();

            //ingresar elemento Tx
            $arrayParametrosTx = array(
                                            'nombreElementoCliente'         => 'trans-'.$arrayParametros['objServicio']->getPuntoId()->getLogin(),
                                            'nombreModeloElementoCliente'   => "TRANSCEIVER TRANS",
                                            'serieElementoCliente'          => "00000",
                                            'objInterfaceElementoVecinoOut' => $objInterfaceElementoOutRoseta,
                                            'objUltimaMilla'                => $arrayParametros['objUltimaMilla'],
                                            'objServicio'                   => $arrayParametros['objServicio'],
                                            'intIdEmpresa'                  => $arrayParametros['idEmpresa'],
                                            'usrCreacion'                   => $arrayParametros['usrCreacion'],
                                            'ipCreacion'                    => $arrayParametros['ipCreacion']
                                        );

            $objInterfaceElementoOutTx = $this->servicioGeneral->ingresarElementoClienteTN($arrayParametrosTx,'TRANSCEIVER');

            $objInterfaceOut           = $objInterfaceElementoOutTx;

            //Deja en estado conectado la interface del Tx
            $objInterfaceElementoOutTx->setEstado('connected');
            $this->emInfraestructura->persist($objInterfaceElementoOutTx);
            $this->emInfraestructura->flush();

            $esRegulado = true;
        }        

        if(is_object($objInterfaceOut))
        {
            $arrayRespuestaFinal = array('status'                                => 'OK', 
                                         'objInterfaceElementoOut'               => $objInterfaceOut,                   //TX                             
                                         'objInterfaceElementoClienteConector'   => $objInterfaceElementoOutRoseta,     //ROSETA nueva
                                         'boolEsRegulado'                        => $esRegulado);                       
        }            
        else
        {
            $arrayRespuestaFinal = array('status'                              => 'ERROR',
                                         'mensaje'                             => 'No existe interface elemento.',
                                         'objInterfaceElementoOut'             => null,
                                         'objInterfaceElementoClienteConector' => null,
                                         'boolEsRegulado'                      => false
                                        );
        }

        return $arrayRespuestaFinal;
    }   
    
    /**
     * 
     * Bloque de código modularizado encargado de realizar el traspaso de información técnica de un equipo a ser cambiado
     * Método sólo será utilizado para cambio de elemento cpe o router
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0
     * @since 25-10-2016
     * 
     * @param Array $arrayParametros [
     *                                  objElementoClienteActual    Objeto referente al Elemento anterior a traspasar informacion de detalle
     *                                  objElementoCpeNuevo         Objeto referente el Elemento nuevo a ser traspasado la información de detalle
     *                                  usrCreacion                 Usuario que genere la acción
     *                                  ipCreacion                  Ip del usuario que genera la acción
     *                               ]
     */
    private function traspasarInformacionTecnicaElemento($arrayParametros)
    {
        // ===================================================================================================
        // SE TRANSFIEREN LOS DETALLES ELEMENTO ACTUAL AL NUEVO Y SE ACTUALIZA LA MAC
        // ===================================================================================================
        $objElementoClienteActual = $arrayParametros['objElementoClienteActual'];
        $objElementoCpeNuevo      = $arrayParametros['objElementoCpeNuevo'];
        
        $objDetElePropiedad = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                      ->findOneBy(array("elementoId"       => $objElementoClienteActual->getId(),
                                                                        "detalleNombre"    => "PROPIEDAD",
                                                                        "estado"           => "Activo"));
        
        $this->serviceUtil->validaObjeto($objDetElePropiedad,'No se encuentra Información de Propiedad de Elemento Anterior');
        
        $objDetElePropiedad->setEstado('Eliminado');
        $this->emInfraestructura->persist($objDetElePropiedad);
        $this->emInfraestructura->flush();  
        // ---------------------------------------------------------------------------------------------------
        $objDetEleGestion = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                    ->findOneBy(array("elementoId"       => $objElementoClienteActual->getId(),
                                                                      "detalleNombre"    => "GESTION REMOTA",
                                                                      "estado"           => "Activo"));
        
        $this->serviceUtil->validaObjeto($objDetEleGestion,'No se encuentra Información de Gestión de Elemento Anterior');
        
        $objDetEleGestion->setEstado('Eliminado');
        $this->emInfraestructura->persist($objDetEleGestion);
        $this->emInfraestructura->flush();  
        // ---------------------------------------------------------------------------------------------------
        $objDetEleAdministra = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                       ->findOneBy(array("elementoId"       => $objElementoClienteActual->getId(),
                                                                         "detalleNombre"    => "ADMINISTRA",
                                                                         "estado"           => "Activo"));
        
        $this->serviceUtil->validaObjeto($objDetEleAdministra,'No se encuentra Información de Administración de Elemento Anterior');
        
        $objDetEleAdministra->setEstado('Eliminado');
        $this->emInfraestructura->persist($objDetEleAdministra);
        $this->emInfraestructura->flush();
        // ---------------------------------------------------------------------------------------------------
        $objDetEleMac = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                ->findOneBy(array("elementoId"       => $objElementoClienteActual->getId(),
                                                                  "detalleNombre"    => "MAC",
                                                                  "estado"           => "Activo"));
                
        if($objDetEleMac)
        {
            $objDetEleMac->setEstado('Eliminado');
            $this->emInfraestructura->persist($objDetEleMac);
            $this->emInfraestructura->flush();  
        }
        // ---------------------------------------------------------------------------------------------------

        $this->servicioGeneral->ingresarDetalleElemento($objElementoCpeNuevo, 'PROPIEDAD', 'ELEMENTO PROPIEDAD DE', 
                                                        $objDetElePropiedad->getDetalleValor(), 
                                                        $arrayParametros['usrCreacion'],
                                                        $arrayParametros['ipCreacion']);

        $this->servicioGeneral->ingresarDetalleElemento($objElementoCpeNuevo, "GESTION REMOTA", "ELEMENTO GESTION REMOTA", 
                                                        $objDetEleGestion->getDetalleValor(), 
                                                        $arrayParametros['usrCreacion'],
                                                        $arrayParametros['ipCreacion']);

        $this->servicioGeneral->ingresarDetalleElemento($objElementoCpeNuevo, "ADMINISTRA", "ELEMENTO ES ADMINISTRADO POR", 
                                                        $objDetEleAdministra->getDetalleValor(), 
                                                        $arrayParametros['usrCreacion'],
                                                        $arrayParametros['ipCreacion']);
    }

    
    /**
     * Función que crea las interfaces y enlaces necesarios para el cambio de un CPE ONT, así como también modifica el estado de 
     * las interfaces y enlaces asociados al CPE ONT que se desea cambiar.
     * Además también es usada para cuando se desea agregar un wifi adicional o se requiere cambiar el wifi adicional cuando se tiene
     * una solicitud de migración
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 27-02-2018
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 21-03-2018  Se genera un numero de tarea en la creacion de la tarea
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 01-03-2018 Se registra tracking del elemento
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.3 29-05-2018 - Se agrega el parametro del id_persona en el registro de la trazabilidad
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.4 18-03-2022 Se agrega eliminación de característica asociada a mac anterior y se crea otra con el valor de la mac nueva en el 
     *                         escenario de cliente con plan PYME que no incluye IP, pero que tiene la IP FIJA como un servicio adicional  
     * 
     */
    public function cambioElementoCpeOntWifiEnlacesInterfacesMd($arrayParametros)
    {
        $objPunto                           = $arrayParametros["objPunto"];
        $intIdServicio                      = $arrayParametros["intIdServicio"];
        $intIdPersona                       = $arrayParametros["intIdPersona"];
        $objServicioProdCaractMacAnterior   = $arrayParametros["objServicioProdCaractMacAnterior"];
        $intIdProductoCaracteristicaMac     = $arrayParametros["intIdProductoCaracteristicaMac"];
        $intIdInterfaceElementoIzq          = $arrayParametros["intIdInterfaceElementoIzq"];
        $intIdInterfaceElementoAnterior     = $arrayParametros["intIdInterfaceElementoAnterior"];
        $intIdElementoAnterior              = $arrayParametros["intIdElementoAnterior"];
        $strSerieElementoNuevo              = $arrayParametros["strSerieElementoNuevo"];
        $strMacElementoNuevo                = $arrayParametros["strMacElementoNuevo"];
        $strModeloElementoNuevo             = $arrayParametros["strModeloElementoNuevo"];
        $strTipoElementoNuevo               = $arrayParametros["strTipoElementoNuevo"];
        $strCodEmpresa                      = $arrayParametros["strCodEmpresa"];
        $strUsrCreacion                     = $arrayParametros["strUsrCreacion"];
        $strIpCreacion                      = $arrayParametros["strIpCreacion"];
        $strAgregarWifi                     = $arrayParametros["strAgregarWifi"] ? $arrayParametros["strAgregarWifi"] : "";
        $strTipoMedioEnlaceNuevo            = $arrayParametros["strTipoMedioEnlaceNuevo"] ? $arrayParametros["strTipoMedioEnlaceNuevo"] : "";
        $strExisteIpWan                     = $arrayParametros["strExisteIpWan"] ? $arrayParametros["strExisteIpWan"] : "";
        $intIdServicioAdicIpWan             = $arrayParametros["intIdServicioAdicIpWan"] ? $arrayParametros["intIdServicioAdicIpWan"] : 0; 
        $objSpcMacIpWan                     = $arrayParametros["objSpcMacIpWan"] ? $arrayParametros["objSpcMacIpWan"] : null;
        $objProductoInternet                = $arrayParametros["objProductoInternet"] ? $arrayParametros["objProductoInternet"] : null;
        $strTipoEnlaceNuevo                 = "PRINCIPAL";
        $strStatus                          = "ERROR";
        $strMensaje                         = "";
        $arrayResultado                     = array();
        $arrayParametrosAuditoria           = array();

        try
        {
            if( is_object($objPunto) && $intIdServicio > 0 && $intIdProductoCaracteristicaMac > 0 && $intIdInterfaceElementoIzq > 0
                && !empty($strSerieElementoNuevo) && !empty($strMacElementoNuevo) && !empty($strModeloElementoNuevo) && !empty($strTipoElementoNuevo)
                && !empty($strCodEmpresa) && !empty($strIpCreacion) 
                && (($strAgregarWifi !== "SI" && $intIdInterfaceElementoAnterior > 0 && $intIdElementoAnterior > 0 
                    && is_object($objServicioProdCaractMacAnterior)) || ($strAgregarWifi === "SI" && !empty($strTipoMedioEnlaceNuevo)))
                )
            {
                $objInterfaceElementoIzq        = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                          ->find($intIdInterfaceElementoIzq);
                $objModeloElementoNuevo         = $this->emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                                                          ->findOneBy(array("nombreModeloElemento"  => $strModeloElementoNuevo,
                                                                                            "estado"                => "Activo"));

                
                if(!is_object($objInterfaceElementoIzq) || !is_object($objModeloElementoNuevo))
                {
                    throw new \Exception("No existe data de las interfaces de los elementos o no existe el modelo del nuevo elemento "
                                         ."Por favor notificar a Sistemas");
                }


                $strLogin       = $objPunto->getLogin();
                $objSector      = $objPunto->getSectorId();
                if(is_object($objSector))
                {
                    $objParroquia   = $objSector->getParroquiaId();
                    if(is_object($objParroquia))
                    {
                        $objParroquiaUbicacion = $this->emInfraestructura->find('schemaBundle:AdmiParroquia', $objParroquia->getId());
                    }
                    else
                    {
                        throw new \Exception("No se ha podido encontrar la parroquia asociada al sector del punto");
                    }
                }

                //Se crea el nuevo elemento
                $objElementoNuevo = new InfoElemento();
                $objElementoNuevo->setNombreElemento($strLogin . "" . $strTipoElementoNuevo);
                $objElementoNuevo->setDescripcionElemento("dispositivo cliente");
                $objElementoNuevo->setModeloElementoId($objModeloElementoNuevo);
                $objElementoNuevo->setSerieFisica($strSerieElementoNuevo);
                $objElementoNuevo->setEstado("Activo");
                $objElementoNuevo->setUsrResponsable($strUsrCreacion);
                $objElementoNuevo->setUsrCreacion($strUsrCreacion);
                $objElementoNuevo->setFeCreacion(new \DateTime('now'));
                $objElementoNuevo->setIpCreacion($strIpCreacion);
                $this->emInfraestructura->persist($objElementoNuevo);
                $this->emInfraestructura->flush();

                //SE REGISTRA EL TRACKING DEL ELEMENTO - NUEVO
                $arrayParametrosAuditoria["strNumeroSerie"]  = $strSerieElementoNuevo;
                $arrayParametrosAuditoria["strEstadoTelcos"] = 'Activo';
                $arrayParametrosAuditoria["strEstadoNaf"]    = 'Instalado';
                $arrayParametrosAuditoria["strEstadoActivo"] = 'Activo';
                $arrayParametrosAuditoria["strUbicacion"]    = 'Cliente';
                $arrayParametrosAuditoria["strCodEmpresa"]   = "18";
                $arrayParametrosAuditoria["strTransaccion"]  = 'Activacion Cliente';
                $arrayParametrosAuditoria["intOficinaId"]    = 0;
                $arrayParametrosAuditoria["strLogin"]        = $strLogin;
                $arrayParametrosAuditoria["strUsrCreacion"]  = $strUsrCreacion;

                $this->serviceInfoElemento->ingresaAuditoriaElementos($arrayParametrosAuditoria);
                ////

                //Se crea el historial del nuevo elemento
                $objHistorialElementoNuevo = new InfoHistorialElemento();
                $objHistorialElementoNuevo->setElementoId($objElementoNuevo);
                $objHistorialElementoNuevo->setEstadoElemento("Activo");
                $objHistorialElementoNuevo->setObservacion("Se ingreso un elemento");
                $objHistorialElementoNuevo->setUsrCreacion($strUsrCreacion);
                $objHistorialElementoNuevo->setFeCreacion(new \DateTime('now'));
                $objHistorialElementoNuevo->setIpCreacion($strIpCreacion);
                $this->emInfraestructura->persist($objHistorialElementoNuevo);
                $this->emInfraestructura->flush();

                //Se crea el registro de la ubicación para el nuevo elemento
                $objUbicacionNuevo = new InfoUbicacion();
                if($objPunto->getLatitud() == null)
                {
                    $objUbicacionNuevo->setLatitudUbicacion(1);
                }
                else
                {
                    $objUbicacionNuevo->setLatitudUbicacion($objPunto->getLatitud());
                }

                if($objPunto->getLongitud() == null)
                {
                    $objUbicacionNuevo->setLongitudUbicacion(1);
                }
                else
                {
                    $objUbicacionNuevo->setLongitudUbicacion($objPunto->getLongitud());
                }
                $objUbicacionNuevo->setDireccionUbicacion($objPunto->getDireccion());
                $objUbicacionNuevo->setAlturaSnm(1.0);
                $objUbicacionNuevo->setParroquiaId($objParroquiaUbicacion);
                $objUbicacionNuevo->setUsrCreacion($strUsrCreacion);
                $objUbicacionNuevo->setFeCreacion(new \DateTime('now'));
                $objUbicacionNuevo->setIpCreacion($strIpCreacion);
                $this->emInfraestructura->persist($objUbicacionNuevo);
                $this->emInfraestructura->flush();

                //Se crea el registro de la empresa elemento ubica para el nuevo elemento
                $objEmpresaElementoUbicaNuevo = new InfoEmpresaElementoUbica();
                $objEmpresaElementoUbicaNuevo->setEmpresaCod($strCodEmpresa);
                $objEmpresaElementoUbicaNuevo->setElementoId($objElementoNuevo);
                $objEmpresaElementoUbicaNuevo->setUbicacionId($objUbicacionNuevo);
                $objEmpresaElementoUbicaNuevo->setUsrCreacion($strUsrCreacion);
                $objEmpresaElementoUbicaNuevo->setFeCreacion(new \DateTime('now'));
                $objEmpresaElementoUbicaNuevo->setIpCreacion($strIpCreacion);
                $this->emInfraestructura->persist($objEmpresaElementoUbicaNuevo);
                $this->emInfraestructura->flush();

                //Se crea el registro de la empresa elemento para el nuevo elemento
                $objEmpresaElementoNuevo = new InfoEmpresaElemento();
                $objEmpresaElementoNuevo->setElementoId($objElementoNuevo);
                $objEmpresaElementoNuevo->setEmpresaCod($strCodEmpresa);
                $objEmpresaElementoNuevo->setEstado("Activo");
                $objEmpresaElementoNuevo->setUsrCreacion($strUsrCreacion);
                $objEmpresaElementoNuevo->setIpCreacion($strIpCreacion);
                $objEmpresaElementoNuevo->setFeCreacion(new \DateTime('now'));
                $this->emInfraestructura->persist($objEmpresaElementoNuevo);
                $this->emInfraestructura->flush();

                if(!empty($strTipoMedioEnlaceNuevo))
                {
                    $objTipoMedioNuevo  = $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')
                                                                  ->findOneBy(array("nombreTipoMedio" => $strTipoMedioEnlaceNuevo));
                }
                
                //Se crean las interfaces para el nuevo elemento de acuerdo al modelo
                $arrayInterfacesModeloElementoNuevo = $this->emInfraestructura->getRepository('schemaBundle:AdmiInterfaceModelo')
                                                                              ->findBy(array("modeloElementoId" => $objModeloElementoNuevo->getId()));
                foreach($arrayInterfacesModeloElementoNuevo as $objInterfaceModeloElementoNuevo)
                {
                    $intCantidadInterfaces  = $objInterfaceModeloElementoNuevo->getCantidadInterface();
                    $strFormato             = $objInterfaceModeloElementoNuevo->getFormatoInterface();

                    for($intIndice = 1; $intIndice <= $intCantidadInterfaces; $intIndice++)
                    {
                        $arrayFormato               = explode("?", $strFormato);
                        $strNombreInterfaceElemento = $arrayFormato[0] . $intIndice;
                        $objInterfaceElementoNuevo  = new InfoInterfaceElemento();
                        $objInterfaceElementoNuevo->setNombreInterfaceElemento($strNombreInterfaceElemento);
                        $objInterfaceElementoNuevo->setElementoId($objElementoNuevo);
                        $objInterfaceElementoNuevo->setEstado("not connect");
                        $objInterfaceElementoNuevo->setUsrCreacion($strUsrCreacion);
                        $objInterfaceElementoNuevo->setFeCreacion(new \DateTime('now'));
                        $objInterfaceElementoNuevo->setIpCreacion($strIpCreacion);
                        $this->emInfraestructura->persist($objInterfaceElementoNuevo);
                        $this->emInfraestructura->flush();
                        //Se conecta la primera interface del nuevo elemento
                        if($intIndice == 1)
                        {
                            if($intIdInterfaceElementoAnterior > 0)
                            {
                                /**
                                 * Verificar si existe el enlace entre la interface del elemento a la izq y la interface del elemento que se 
                                 * desea cambiar
                                 */
                                $objEnlaceInterfaceIzqInterfaceAnterior    = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                                                                    ->findOneBy(array("interfaceElementoIniId"    => 
                                                                                                                      $intIdInterfaceElementoIzq,
                                                                                                                      "interfaceElementoFinId"    =>
                                                                                                                      $intIdInterfaceElementoAnterior,
                                                                                                                      "estado"                    => 
                                                                                                                      "Activo"));
                                if(is_object($objEnlaceInterfaceIzqInterfaceAnterior))
                                {
                                    /**
                                     * Se modifica el estado del enlace a Eliminado entre la interface del elemento izq y la interface del elemento 
                                     * anterior
                                     */
                                    $objTipoMedioNuevo  = $objEnlaceInterfaceIzqInterfaceAnterior->getTipoMedioId();
                                    $strTipoEnlaceNuevo = $objEnlaceInterfaceIzqInterfaceAnterior->getTipoEnlace();
                                    $objEnlaceInterfaceIzqInterfaceAnterior->setEstado("Eliminado");
                                    $this->emInfraestructura->persist($objEnlaceInterfaceIzqInterfaceAnterior);
                                }
                                else
                                {
                                    throw new \Exception("No existe enlace actual entre el elemento der y el elemento que se desea cambiar, "
                                                         ."imposible realizar cambio de elemento. Por favor notificar a Sistemas");
                                }
                            }
                            
                            if(is_object($objTipoMedioNuevo))
                            {
                                //Se crea el nuevo enlace entre la interface del elemento izq y la interface del nuevo elemento
                                $objEnlaceInterfaceIzqInterfaceNueva = new InfoEnlace();
                                $objEnlaceInterfaceIzqInterfaceNueva->setInterfaceElementoIniId($objInterfaceElementoIzq);
                                $objEnlaceInterfaceIzqInterfaceNueva->setInterfaceElementoFinId($objInterfaceElementoNuevo);
                                $objEnlaceInterfaceIzqInterfaceNueva->setTipoMedioId($objTipoMedioNuevo);
                                $objEnlaceInterfaceIzqInterfaceNueva->setTipoEnlace($strTipoEnlaceNuevo);
                                $objEnlaceInterfaceIzqInterfaceNueva->setEstado("Activo");
                                $objEnlaceInterfaceIzqInterfaceNueva->setUsrCreacion($strUsrCreacion);
                                $objEnlaceInterfaceIzqInterfaceNueva->setFeCreacion(new \DateTime('now'));
                                $objEnlaceInterfaceIzqInterfaceNueva->setIpCreacion($strIpCreacion);
                                $this->emInfraestructura->persist($objEnlaceInterfaceIzqInterfaceNueva);
                                
                                $objInterfaceElementoIzq->setEstado("connected");
                                $this->emInfraestructura->persist($objInterfaceElementoIzq);

                                $objInterfaceElementoNuevo->setEstado("connected");
                                $this->emInfraestructura->persist($objInterfaceElementoNuevo);
                                $this->emInfraestructura->flush();
                                $objInterfaceElementoNuevoConnected = $objInterfaceElementoNuevo;
                            }
                            else
                            {
                                throw new \Exception("No se ha podido obtener el tipo medio del nuevo enlace, "
                                                     ."imposible realizar cambio de elemento. Por favor notificar a Sistemas");
                            }
                        }
                    }
                }
                
                if($intIdElementoAnterior > 0)
                {
                    //Se cambia a estado Eliminado todas las interfaces del elemento anterior
                    $arrayInterfacesElementoAnterior  = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                                ->findBy(array("elementoId" => $intIdElementoAnterior));

                    foreach($arrayInterfacesElementoAnterior as $objEliminarInterfaceElementoAnterior)
                    {
                        $objEliminarInterfaceElementoAnterior->setEstado("Eliminado");
                        $objEliminarInterfaceElementoAnterior->setUsrUltMod($strUsrCreacion);
                        $objEliminarInterfaceElementoAnterior->setFeUltMod(new \DateTime('now'));
                        $this->emInfraestructura->persist($objEliminarInterfaceElementoAnterior);
                        $this->emInfraestructura->flush();
                    }
                }
                
                if($intIdInterfaceElementoAnterior > 0)
                {
                    //Se busca si es que existe un enlace activo que tenga como interface elemento ini a la interface del elemento anterior
                    $objEnlaceInterfaceAnteriorInterfaceDer = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                                                      ->findOneBy(array("interfaceElementoIniId"    => 
                                                                                                        $intIdInterfaceElementoAnterior,
                                                                                                        "estado"                    => 
                                                                                                        "Activo"));
                    if(is_object($objEnlaceInterfaceAnteriorInterfaceDer))
                    {
                        //Se obtiene la interface elemento fin que sería la interface del elemento der
                        $objInterfaceElementoDer = $objEnlaceInterfaceAnteriorInterfaceDer->getInterfaceElementoFinId();

                        if(is_object($objInterfaceElementoDer))
                        {
                            $objEnlaceInterfaceAnteriorInterfaceDer->setEstado("Eliminado");
                            $this->emInfraestructura->persist($objEnlaceInterfaceAnteriorInterfaceDer);
                            $this->emInfraestructura->flush();
                            if(is_object($objInterfaceElementoNuevoConnected))
                            {
                                /**
                                 * Se crea el nuevo enlace entre la interface nueva del elemento nuevo y la interface del elemento der
                                 */
                                $objEnlaceInterfaceNuevaInterfaceDer = new InfoEnlace();
                                $objEnlaceInterfaceNuevaInterfaceDer->setInterfaceElementoIniId($objInterfaceElementoNuevoConnected);
                                $objEnlaceInterfaceNuevaInterfaceDer->setInterfaceElementoFinId($objInterfaceElementoDer);
                                $objEnlaceInterfaceNuevaInterfaceDer->setTipoMedioId($objEnlaceInterfaceAnteriorInterfaceDer->getTipoMedioId());
                                $objEnlaceInterfaceNuevaInterfaceDer->setTipoEnlace($objEnlaceInterfaceAnteriorInterfaceDer->getTipoEnlace());
                                $objEnlaceInterfaceNuevaInterfaceDer->setEstado("Activo");
                                $objEnlaceInterfaceNuevaInterfaceDer->setUsrCreacion($strUsrCreacion);
                                $objEnlaceInterfaceNuevaInterfaceDer->setFeCreacion(new \DateTime('now'));
                                $objEnlaceInterfaceNuevaInterfaceDer->setIpCreacion($strIpCreacion);
                                $this->emInfraestructura->persist($objEnlaceInterfaceNuevaInterfaceDer);
                                $this->emInfraestructura->flush();
                            }
                            else
                            {
                                throw new \Exception("No se ha podido crear la interface para el nuevo elemento, "
                                                     ."imposible realizar cambio de elemento. Por favor notificar a Sistemas");
                            }
                        }
                        else
                        {
                            throw new \Exception("No existe la interface fin del enlace de la interface del elemento a cambiar como interface ini, "
                                                 ."imposible realizar cambio de elemento. Por favor notificar a Sistemas");
                        }
                    }
                }
                
                if($intIdElementoAnterior > 0)
                {
                    //Se cambia a estado Eliminado el registro de la empresa elemento asociado con el elemento anterior
                    $objEmpresaElementoAnterior = $this->emInfraestructura->getRepository("schemaBundle:InfoEmpresaElemento")
                                                                                ->findOneBy(array('elementoId'    => $intIdElementoAnterior,
                                                                                                  'empresaCod'    => $strCodEmpresa,
                                                                                                  'estado'        => 'Activo'));
                    if(is_object($objEmpresaElementoAnterior))
                    {
                        $objEmpresaElementoAnterior->setEstado("Eliminado");
                        $this->emInfraestructura->persist($objEmpresaElementoAnterior);
                        $this->emInfraestructura->flush();
                    }

                    //Se cambia a estado Eliminado el registro del elemento anterior
                    $objElementoAnterior  = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                    ->find($intIdElementoAnterior);

                    if(is_object($objElementoAnterior))
                    {
                        $objElementoAnterior->setEstado("Eliminado");
                        $this->emInfraestructura->persist($objElementoAnterior);
                        $this->emInfraestructura->flush();

                        //SE REGISTRA EL TRACKING DEL ELEMENTO - ANTERIOR
                        $arrayParametrosAuditoria["strNumeroSerie"]  = $objElementoAnterior->getSerieFisica();
                        $arrayParametrosAuditoria["strEstadoTelcos"] = 'Eliminado';
                        $arrayParametrosAuditoria["strEstadoNaf"]    = 'Instalado';
                        $arrayParametrosAuditoria["strEstadoActivo"] = 'CambioEquipo';
                        $arrayParametrosAuditoria["strUbicacion"]    = 'EnTransito';
                        $arrayParametrosAuditoria["strCodEmpresa"]   = "18";
                        $arrayParametrosAuditoria["intIdPersona"]    = $intIdPersona;
                        $arrayParametrosAuditoria["strTransaccion"]  = 'Cambio de Elemento';
                        $arrayParametrosAuditoria["intOficinaId"]    = 0;
                        $arrayParametrosAuditoria["strLogin"]        = $strLogin;
                        $arrayParametrosAuditoria["strUsrCreacion"]  = $strUsrCreacion;

                        $this->serviceInfoElemento->ingresaAuditoriaElementos($arrayParametrosAuditoria);

                        //historial del elemento
                        $objHistorialElementoAnterior = new InfoHistorialElemento();
                        $objHistorialElementoAnterior->setElementoId($objElementoAnterior);
                        $objHistorialElementoAnterior->setObservacion("Se elimino el elemento por Cambio de equipo");
                        $objHistorialElementoAnterior->setEstadoElemento("Eliminado");
                        $objHistorialElementoAnterior->setUsrCreacion($strUsrCreacion);
                        $objHistorialElementoAnterior->setFeCreacion(new \DateTime('now'));
                        $objHistorialElementoAnterior->setIpCreacion($strIpCreacion);
                        $this->emInfraestructura->persist($objHistorialElementoAnterior);
                        $this->emInfraestructura->flush();
                    }
                }
                
                //Se modifica la característica de la MAC asociada al servicio a estado eliminado
                if(is_object($objServicioProdCaractMacAnterior))
                {
                    $objServicioProdCaractMacAnterior->setEstado("Eliminado");
                    $objServicioProdCaractMacAnterior->setUsrUltMod($strUsrCreacion);
                    $objServicioProdCaractMacAnterior->setFeUltMod(new \DateTime('now'));
                    $this->emComercial->persist($objServicioProdCaractMacAnterior);
                    $this->emComercial->flush();
                }

                //Se crea la nueva característica asociada al servicio con la nueva MAC
                $objServicioProdCaractMacNueva = new InfoServicioProdCaract();
                $objServicioProdCaractMacNueva->setServicioId($intIdServicio);
                $objServicioProdCaractMacNueva->setProductoCaracterisiticaId($intIdProductoCaracteristicaMac);
                $objServicioProdCaractMacNueva->setValor($strMacElementoNuevo);
                $objServicioProdCaractMacNueva->setEstado("Activo");
                $objServicioProdCaractMacNueva->setUsrCreacion($strUsrCreacion);
                $objServicioProdCaractMacNueva->setFeCreacion(new \DateTime('now'));
                $this->emComercial->persist($objServicioProdCaractMacNueva);
                $this->emComercial->flush();
                
                $strExisteIpWan = $arrayParametros["strExisteIpWan"] ? $arrayParametros["strExisteIpWan"] : "";
                if($strExisteIpWan === "SI")
                {
                    if(is_object($objSpcMacIpWan))
                    {
                        $objSpcMacIpWan->setEstado("Eliminado");
                        $objSpcMacIpWan->setUsrUltMod($strUsrCreacion);
                        $objSpcMacIpWan->setFeUltMod(new \DateTime('now'));
                        $this->emComercial->persist($objSpcMacIpWan);
                        $this->emComercial->flush();
                    }
                    
                    if(is_object($objProductoInternet))
                    {
                        $objCaractMacIpWan      = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                    ->findOneBy(array("descripcionCaracteristica"   => "MAC", 
                                                                                      "estado"                      => "Activo"));
                        if(is_object($objCaractMacIpWan))
                        {
                            $objProdCaractMacInternetIpWan  = $this->emComercial
                                                                   ->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                   ->findOneBy(array("productoId"          => $objProductoInternet->getId(), 
                                                                                     "caracteristicaId"    => $objCaractMacIpWan->getId()));
                            if(is_object($objProdCaractMacInternetIpWan))
                            {
                                $intIdProdCaractMacInternetIpWan = $objProdCaractMacInternetIpWan->getId();
                                $objSpcMacNuevaIpWan = new InfoServicioProdCaract();
                                $objSpcMacNuevaIpWan->setServicioId($intIdServicioAdicIpWan);
                                $objSpcMacNuevaIpWan->setProductoCaracterisiticaId($intIdProdCaractMacInternetIpWan);
                                $objSpcMacNuevaIpWan->setValor($strMacElementoNuevo);
                                $objSpcMacNuevaIpWan->setEstado("Activo");
                                $objSpcMacNuevaIpWan->setUsrCreacion($strUsrCreacion);
                                $objSpcMacNuevaIpWan->setFeCreacion(new \DateTime('now'));
                                $this->emComercial->persist($objSpcMacNuevaIpWan);
                                $this->emComercial->flush();
                            }
                        }
                    }
                }
                $arrayResultado["objElementoNuevo"]                     = $objElementoNuevo;
                $arrayResultado["objInterfaceElementoNuevoConnected"]   = $objInterfaceElementoNuevoConnected;
                
                $strStatus = "OK";
            }
            else
            {
                throw new \Exception("No se han enviado todos los parámetros necesarios para el cambio de elemento, "
                                     ."imposible realizar cambio de elemento. Por favor notificar a Sistemas");
            }
        } 
        catch (\Exception $e) 
        {
            $strMensaje = $e->getMessage();
        }
        $arrayResultado["strStatus"]    = $strStatus;
        $arrayResultado["strMensaje"]   = $strMensaje;
        return $arrayResultado;
    }

    /**
     * Función que actualiza la característica mac a las ips adicionales cuando se realiza un cambio de elemento y el tipo de negocio es PRO
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 17-04-2018
     *
     */
    public function actualizarMacCambioElementoPro($arrayParametros)
    {
        $strIpCreacion      = $arrayParametros["strIpCreacion"];
        $strUsrCreacion     = $arrayParametros["strUsrCreacion"];
        $strTipoElementoCpe = $arrayParametros["strTipoElementoCpe"];
        $objProducto        = $arrayParametros["objProducto"];
        $objServicio        = $arrayParametros["objServicio"];
        $strMacCpe          = $arrayParametros["strMacCpe"];
        $strTipoNegocio     = $arrayParametros["strTipoNegocio"];
        $strCodEmpresa      = $arrayParametros["strCodEmpresa"];
        
        if($strTipoNegocio === 'PRO')
        {
            $arrayProdIp                = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                            ->findBy(array( "nombreTecnico" => "IP",
                                                                            "empresaCod"    => $strCodEmpresa,
                                                                            "estado"        => "Activo"));
            $arrayServiciosPorPunto     = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                            ->findBy(array( "puntoId" => $objServicio->getPuntoId()->getId(), 
                                                                            "estado" => "Activo"));
            $arrayDatosIpAdicionales    = $this->servicioGeneral->getInfoIpsFijaPunto(  $arrayServiciosPorPunto, 
                                                                                        $arrayProdIp, 
                                                                                        $objServicio, 
                                                                                        'Activo', 
                                                                                        'Activo',
                                                                                        $objProducto);
            $intNumIpsFijasAdicionales  = $arrayDatosIpAdicionales['ip_fijas_activas'];
            $arrayIpsAdicionales        = $arrayDatosIpAdicionales['valores'];
            if($intNumIpsFijasAdicionales > 0)
            {
                foreach($arrayIpsAdicionales as $arrayIpAdicional)
                {
                    $strHistorialMac        = "";
                    $intIdServicioAdicional = $arrayIpAdicional['id_servicio'];
                    $objServicioIpAdicional = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicioAdicional);
                    if(is_object($objServicioIpAdicional))
                    {
                        $strCaractMac = 'MAC';
                        $objSpcMac  = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioIpAdicional, 
                                                                                                $strCaractMac, 
                                                                                                $objProducto);
                        if(!is_object($objSpcMac))
                        {
                            $objSpcMac = $this->servicioGeneral->getServicioProductoCaracteristica( $objServicioIpAdicional, 
                                                                                                    'MAC WIFI', 
                                                                                                    $objProducto);
                            if(is_object($objSpcMac))
                            {
                                $strCaractMac = 'MAC WIFI';
                            }
                        }

                        if(is_object($objSpcMac))
                        {
                            $strHistorialMac .= "<br />Mac Anterior: ".$objSpcMac->getValor();
                            $objSpcMac->setEstado('Eliminado');
                            $objSpcMac->setUsrUltMod($strUsrCreacion);
                            $objSpcMac->setFeUltMod(new \DateTime('now'));
                            $this->emComercial->persist($objSpcMac);
                            $this->emComercial->flush();
                        }
                        //Se ingresa nueva característica con la MAC del ont cambiado en los servicios de Ips Adicionales
                        $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicioIpAdicional, $objProducto, $strCaractMac,
                                                                                        $strMacCpe, $strUsrCreacion);
                        
                        $strHistorialMac .= "<br />Mac Nueva: ".$strMacCpe;
                        //Se ingresa historial con cambio de mac por servicio principal
                        $objServicioHistorialIpAdic = new InfoServicioHistorial();
                        $objServicioHistorialIpAdic->setServicioId($objServicioIpAdicional);
                        $objServicioHistorialIpAdic->setObservacion("Se actualiza mac por Cambio de Elemento Cliente ".
                                                                    $strTipoElementoCpe.$strHistorialMac);
                        $objServicioHistorialIpAdic->setEstado($objServicioIpAdicional->getEstado());
                        $objServicioHistorialIpAdic->setUsrCreacion($strUsrCreacion);
                        $objServicioHistorialIpAdic->setFeCreacion(new \DateTime('now'));
                        $objServicioHistorialIpAdic->setIpCreacion($strIpCreacion);
                        $this->emComercial->persist($objServicioHistorialIpAdic);
                        $this->emComercial->flush();
                    }
                }
            }
        }
    }

    /**
     * Función que realiza el proceso de cambio de elemento ont para los servicios con factibilidad en olts ZTE
     * 
     * @param array $arrayParametros [
     *                                  'servicio'          => servicio al que desea realizarse el cambio de equipo  
     *                                  'servicioTecnico'   => servicio técnico 
     *                                  'interfaceElemento' => interface del olt
     *                                  'producto'          => producto internet del servicio
     *                                  'serieCpe'          => serie del nuevo cpe
     *                                  'codigoArticulo'    => modelo del cpe nuevo
     *                                  'macCpe'            => mac del cpe nuevo
     *                                  'tipoElementoCpe'   => tipo del elemento cliente
     *                                  'idEmpresa'         => código de la empresa
     *                                  'usrCreacion'       => usuario de creación
     *                                  'ipCreacion'        => ip de creación
     *                                  'prefijoEmpresa'    => prefijo de la empresa
     *                               ]
     * @return array $arrayResultado [
     *                                  'status'    => OK o ERROR
     *                                  'mensaje'   => mensaje de proceso
     *                               ]
     *
     * @author Versión Inicial
     * @version 1.0
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.1 07-08-2020 - Se valida el response del middleware para cambio de equipo en los servicios TN.
     *                           (Internet Small Business y TelcoHome)
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 01-12-2020 Se agrega verificación de equipo en NAF de acuerdo a validaciones existentes en AFK_PROCESOS.IN_P_PROCESA_INSTALACION,
     *                          antes de enviar el request a middleware, para evitar errores por NAF que obliguen la eliminación de línea pon
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.2 02-12-2020 - Se agrega la validación para obtener la ip y el scope de los servicios TN.
     *
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.3 16-11-2020 Se agregan validación para ejecutar cambio de planes PYME con nuevos parámetros enviados al middleware
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.4 10-11-2021 Se construye el arreglo con la información que se enviará al web service para confirmación de opción de Tn a Middleware
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.5 18-03-2022 Se obtiene la información necesaria para escenario de cliente con plan PYME que no incluye IP, pero que tiene la
     *                         IP FIJA como un servicio adicional y se la envía a función cambioElementoCpeOntWifiEnlacesInterfacesMd que gestiona
     *                         la información de la MAC
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.6 07-02-2022 Se agrega nuevo parámetro para distinguir los servicios que requieren cambio de ont por medio de una solicitud
     *                         agregar equipo
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.6 08-02-2022 - Se valida los modelos de equipos con extender en cambio de modem inmediato.
     * 
     */
    public function cambioElementoZte($arrayParametros) 
    {
        $objServicio                = $arrayParametros['servicio'];
        $objServicioTecnico         = $arrayParametros['servicioTecnico'];
        $objInterfaceOlt            = $arrayParametros['interfaceElemento'];
        $objProductoInternet        = $arrayParametros['producto'];
        $strSerieCpeNuevo           = $arrayParametros['serieCpe'];
        $strModeloCpeNuevo          = $arrayParametros['codigoArticulo'];
        $strMacCpeNuevo             = $arrayParametros['macCpe'];
        $strTipoElementoCpe         = $arrayParametros['tipoElementoCpe'];
        $strCodEmpresa              = $arrayParametros['idEmpresa'];
        $strUsrCreacion             = $arrayParametros['usrCreacion'];
        $strIpCreacion              = $arrayParametros['ipCreacion'];
        $strPrefijoEmpresa          = $arrayParametros['prefijoEmpresa'];
        $strEsCambioOntPorSolAgregarEquipo = $arrayParametros['strEsCambioOntPorSolAgregarEquipo'];
        $strOrigen                  = $arrayParametros['strOrigen'];
        $intNumIpEnPlan             = 0;
        $objProdIpEnPlan            = null;
        $strIpFijaActual            = "";
        $strScopeActual             = "";
        $arrayDatosIpWan            = array();
        $arrayDataConfirmacionTn    = array();
        $strExisteIpWan             = "";
        $intIdServicioAdicIpWan     = 0;
        $objSpcMacIpWan             = null;
        $strOntActualEstaParametrizado = "NO";
        $strOntNuevoEstaParametrizado  = "NO";
        
        try
        {
            if($strEsCambioOntPorSolAgregarEquipo === "SI")
            {
                $strTipoSolicitud   = "SOLICITUD AGREGAR EQUIPO";
                $strEstadoSolicitud = "Asignada";
            }
            else
            {
                $strTipoSolicitud   = "SOLICITUD CAMBIO DE MODEM INMEDIATO";
                $strEstadoSolicitud = "AsignadoTarea";
            }
            $objPersona                 = $objServicio->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId();
            $strIdentificacion          = $objPersona->getIdentificacionCliente();
            $strNombreCliente           = ($objPersona->getRazonSocial()) ? $objPersona->getRazonSocial() :
                                          $objPersona->getNombres()." ".$objPersona->getApellidos();
            $objPunto                   = $objServicio->getPuntoId();
            if($strPrefijoEmpresa == 'TN')
            {
                $objServProdCaracTipoNegocio = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio,
                                                                                                         "Grupo Negocio",
                                                                                                         $objProductoInternet);
                if(is_object($objServProdCaracTipoNegocio))
                {
                    $strValorTipoNegocioProd = $objServProdCaracTipoNegocio->getValor();
                    list($strTipoNegocio)    = explode("TN",$strValorTipoNegocioProd);
                }
                else
                {
                    throw new \Exception("No existe Caracteristica Grupo Negocio");
                }
            }
            else
            {
                $strTipoNegocio         = $objPunto->getTipoNegocioId()->getNombreTipoNegocio();
                
            }

            $objDetalleOltMiddleware    = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                  ->findOneBy(array("elementoId"    => $objServicioTecnico->getElementoId(),
                                                                                    "detalleNombre" => 'MIDDLEWARE',
                                                                                    "detalleValor"  => 'SI',
                                                                                    "estado"        => 'Activo'));
            if(!is_object($objDetalleOltMiddleware))
            {
                throw new \Exception("El olt actual no posee el detalle MIDDLEWARE.");
            }
            
            $objElementoClienteOnt  = $this->emComercial->getRepository('schemaBundle:InfoElemento')
                                                        ->find($objServicioTecnico->getElementoClienteId());
            if(!is_object($objElementoClienteOnt))
            {
                throw new \Exception("No existe el ont del cliente.");
            }
            $strSerieOnt    = $objElementoClienteOnt->getSerieFisica();
            $strModeloCpeActual = $objElementoClienteOnt->getModeloElementoId()->getNombreModeloElemento();
            
            $objSpcMacOnt = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "MAC ONT", $objProductoInternet);
            if(!is_object($objSpcMacOnt))
            {
                throw new \Exception("No existe mac del ont.");
            }
            
            $objSpcIndiceClienteAnterior    = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "INDICE CLIENTE",
                                                                                                        $objProductoInternet);
            if(!is_object($objSpcIndiceClienteAnterior))
            {
                throw new \Exception("No existe característica INDICE CLIENTE.");
            }

            $objSpcSpidAnterior = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "SPID", $objProductoInternet);
            if(!is_object($objSpcSpidAnterior))
            {
                throw new \Exception("No existe característica SPID.");
            }
            
            $objSpcVlanAnterior = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "VLAN", $objProductoInternet);
            if(!is_object($objSpcVlanAnterior))
            {
                throw new \Exception("No existe característica VLAN.");
            }

            $objSpcClientClassAnterior  = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "CLIENT CLASS",
                                                                                                    $objProductoInternet);
            if(!is_object($objSpcClientClassAnterior))
            {
                throw new \Exception("No existe característica CLIENT CLASS.");
            }
            
            $objSpcPackageIdAnterior    = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "PACKAGE ID", $objProductoInternet);
            if(!is_object($objSpcPackageIdAnterior))
            {
                throw new \Exception("No existe característica PACKAGE ID.");
            }

            $objSpcLineProfileNameAnterior  = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "LINE-PROFILE-NAME",
                                                                                                        $objProductoInternet);
            if(!is_object($objSpcLineProfileNameAnterior))
            {
                throw new \Exception("No existe característica LINE-PROFILE-NAME.");
            }
            
            $objSpcCapacidad1 = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "CAPACIDAD1", $objProductoInternet);
            if(!is_object($objSpcCapacidad1))
            {
                throw new \Exception("No existe característica CAPACIDAD1.");
            }
            
            $objSpcCapacidad2 = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "CAPACIDAD2", $objProductoInternet);
            if(!is_object($objSpcCapacidad2))
            {
                throw new \Exception("No existe característica CAPACIDAD2.");
            }
            
            $objCaracteristicaMacOnt    = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                            ->findOneBy(array( "descripcionCaracteristica" => "MAC ONT", "estado" => "Activo"));
            $objProdCaractMacOnt        = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                            ->findOneBy(array(  "productoId"        => $objProductoInternet->getId(), 
                                                                                "caracteristicaId"  => $objCaracteristicaMacOnt->getId()));
            if(!is_object($objProdCaractMacOnt))
            {
                throw new \Exception("No existe característica MAC ONT asociado al producto del servicio.");
            }

            $arrayProdIp    = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                            ->findBy(array( "nombreTecnico" => "IP",
                                                                            "empresaCod"    => $strCodEmpresa,
                                                                            "estado"        => "Activo"));
            $objPlanCab     = $objServicio->getPlanId();
            if(is_object($objPlanCab))
            {
                $objPlanDet = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                                ->findBy(array("planId" => $objPlanCab->getId()));
                for($intIndex = 0; $intIndex < count($objPlanDet); $intIndex++)
                {
                    for($intIndexJ = 0; $intIndexJ < count($arrayProdIp); $intIndexJ++)
                    {
                        if($objPlanDet[$intIndex]->getProductoId() == $arrayProdIp[$intIndexJ]->getId())
                        {
                            $objProdIpEnPlan    = $arrayProdIp[$intIndexJ];
                            $intNumIpEnPlan     = 1;
                            break;
                        }
                    }
                }
            }
            
            $objSpcServiceProfile = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "SERVICE-PROFILE", $objProductoInternet);
            if(!is_object($objSpcServiceProfile))
            {
                $objElementoClienteAnterior = $this->emComercial->getRepository('schemaBundle:InfoElemento')
                                                                ->find($objServicioTecnico->getElementoClienteId());
                if(is_object($objElementoClienteAnterior))
                {
                    $strServiceProfile = $objElementoClienteAnterior->getModeloElementoId()->getNombreModeloElemento();
                    $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicio, $objProductoInternet, "SERVICE-PROFILE",
                                                                                    $strServiceProfile, $strUsrCreacion);
                }
                else
                {
                    throw new \Exception("No existe característica SERVICE-PROFILE.");
                }
            }
            else
            {
                $strServiceProfile = $objSpcServiceProfile->getValor();
            }
            
            $objOlt                     = $objInterfaceOlt->getElementoId();
            $objIpOlt                   = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                                  ->findOneBy(array("elementoId" => $objOlt->getId()));

            $arrayServiciosPunto        = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                            ->findBy(array("puntoId" => $objPunto, "estado" => "Activo"));
            $arrayDatosIpAdicionales    = $this->servicioGeneral->getInfoIpsFijaPunto($arrayServiciosPunto, $arrayProdIp, $objServicio, 
                                                                                      'Activo', 'Activo', $objProductoInternet);
            $intNumIpsAdicionales       = $arrayDatosIpAdicionales['ip_fijas_activas'];

            if ($strPrefijoEmpresa === 'MD' && $strTipoNegocio === 'PYME')
            {
                //OBTENER IPS ADICIONALES
                $arrayParametrosIpWan = array('objPunto'       => $objServicio->getPuntoId(),
                                              'strEmpresaCod'  => $strCodEmpresa,
                                              'strUsrCreacion' => $strUsrCreacion,
                                              'strIpCreacion'  => $strIpCreacion);
                $arrayDatosIpWan      = $this->servicioGeneral
                                             ->getIpFijaWan($arrayParametrosIpWan);
            }

            if($strPrefijoEmpresa == 'TN')
            {
                //obtener ip actual
                $objIpFijaActual = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                  ->findOneBy(array('servicioId'    => $objServicio->getId(),
                                                                    'estado'        => 'Activo'));
                if(is_object($objIpFijaActual))
                {
                    $strIpFijaActual = $objIpFijaActual->getIp();
                    $intNumIpsAdicionales = 1;
                }
                //obtener scope actual
                $objScopeActual  = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, 'SCOPE', $objProductoInternet);
                if(is_object($objScopeActual))
                {
                    $strScopeActual  = $objScopeActual->getValor();
                }
            }
            else if ($strTipoNegocio === 'PYME' && isset($arrayDatosIpWan['strStatus']) && !empty($arrayDatosIpWan['strStatus']) && 
                $strPrefijoEmpresa == 'MD' && $arrayDatosIpWan['strStatus'] === 'OK' && isset($arrayDatosIpWan['strExisteIpWan']) &&
                !empty($arrayDatosIpWan['strExisteIpWan']) &&  $arrayDatosIpWan['strExisteIpWan'] === 'SI')
            {
                $strIpFijaActual = $arrayDatosIpWan['arrayInfoIp']['strIp'];
                $strScopeActual  = $arrayDatosIpWan['arrayInfoIp']['strScope'];
                $strExisteIpWan         = $arrayDatosIpWan['strExisteIpWan'];
                $intIdServicioAdicIpWan = $arrayDatosIpWan['arrayInfoIp']['intIdServicioIp'];
                $objSpcMacIpWan         = $arrayDatosIpWan['arrayInfoIp']['objSpcMac'];
            }
            else if($intNumIpEnPlan === 1)
            {
                $objIpFijaPlan      = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                              ->findOneBy(array("servicioId" => $objServicio->getId(), "estado" => "Activo"));
                $strIpFijaActual    = $objIpFijaPlan->getIp();

                $objSpcScope = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "SCOPE", $objProdIpEnPlan);

                if(!is_object($objSpcScope))
                {
                    //buscar scopes
                    $arrayScopeOlt = $this->emInfraestructura->getRepository('schemaBundle:InfoSubred')
                                                             ->getScopePorIpFija($objIpFijaPlan->getIp(), $objServicioTecnico->getElementoId());

                    if (empty($arrayScopeOlt))
                    {   
                        throw new \Exception("Ip Fija no pertenece a un Scope! <br>Favor Comunicarse con el Dep. Gepon!");
                    }
                    $strScopeActual = $arrayScopeOlt['NOMBRE_SCOPE'];
                }
                else
                {
                    $strScopeActual = $objSpcScope->getValor();
                }
            }
            else if($intNumIpsAdicionales == 1)
            {
                $arrayValoresIpAdicionales  = $arrayDatosIpAdicionales['valores'];
                $strIpFijaActual            = $arrayValoresIpAdicionales[0]['ip'];
                $intIdServicioIpAdicional   = $arrayValoresIpAdicionales[0]['id_servicio'];
                $objServicioIpAdicional     = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicioIpAdicional);
                $objSpcScope                = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioIpAdicional, 
                                                                                                        "SCOPE", 
                                                                                                        $objServicioIpAdicional->getProductoId()
                                                                                                       );
                if(is_object($objSpcScope))
                {
                    $strScopeActual = $objSpcScope->getValor();  
                }
            }

            //se valida que en caso de ser un cambio de modem inmediato
            if ($strTipoSolicitud == "SOLICITUD CAMBIO DE MODEM INMEDIATO")
            {
                //se consulta los modelos de ZTE para extender
                $strNombreParametro         = 'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD';
                $strCampoValor1             = 'MODELOS_EQUIPOS';
                $strCampoValor6             = 'ONT';
                $arrayModelosParametrizados = array();
                $arrayAdmiParametroOnt      = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                              ->get($strNombreParametro,
                                                                    '',
                                                                    '',
                                                                    '',
                                                                    $strCampoValor1,
                                                                    '',
                                                                    '',
                                                                    '',
                                                                    '',
                                                                    $strCodEmpresa,
                                                                    '',
                                                                    $strCampoValor6);
                if(is_array($arrayAdmiParametroOnt) && count($arrayAdmiParametroOnt) > 0)
                {
                    foreach($arrayAdmiParametroOnt as $arrayParametroOnt)
                    {
                        $arrayModelosParametrizados[] = $arrayParametroOnt['valor5'];
                    }
                }
                //comparo modelo nuevo
                if(in_array($strModeloCpeNuevo, $arrayModelosParametrizados))
                {
                    $strOntNuevoEstaParametrizado = "SI";
                }
                //comparo modelo actual
                if(in_array($strModeloCpeActual, $arrayModelosParametrizados))
                {
                    $strOntActualEstaParametrizado = "SI";
                }
                //validar modelos
                if ($strOntActualEstaParametrizado == "SI" && $strOntNuevoEstaParametrizado == "NO")
                {
                    throw new \Exception('No se puede realizar este cambio de equipo, '.
                                         'su plan actual incluye un modelo parametrizado para extender, '.
                                         'no se puede utilizar un nuevo equipo que no sea parametrizado para extender.');
                }
            }

            $arrayVerifOntNaf   = $this->servicioGeneral->buscarEquipoEnNafPorParametros(array( "serieEquipo"           => $strSerieCpeNuevo,
                                                                                                "estadoEquipo"          => "PI",
                                                                                                "tipoArticuloEquipo"    => "AF",
                                                                                                "modeloEquipo"          => $strModeloCpeNuevo));
            if($arrayVerifOntNaf["status"] === "ERROR")
            {
                throw new \Exception($arrayVerifOntNaf["mensaje"]);
            }
            
            $this->actualizarMacCambioElementoPro(array("strIpCreacion"         => $strIpCreacion,
                                                        "strUsrCreacion"        => $strUsrCreacion,
                                                        "strTipoElementoCpe"    => $strTipoElementoCpe,
                                                        "strCodEmpresa"         => $strCodEmpresa,
                                                        "objProducto"           => $objProductoInternet,
                                                        "objServicio"           => $objServicio,
                                                        "strMacCpe"             => $strMacCpeNuevo,
                                                        "strTipoNegocio"        => $strTipoNegocio
                                                        ));
            $arrayDatosMiddleware       = array(
                                                'serial_ont'            => $strSerieOnt,
                                                'mac_ont'               => $objSpcMacOnt->getValor(),
                                                'nombre_olt'            => $objOlt->getNombreElemento(),
                                                'ip_olt'                => $objIpOlt->getIp(),
                                                'puerto_olt'            => $objInterfaceOlt->getNombreInterfaceElemento(),
                                                'modelo_olt'            => $objOlt->getModeloElementoId()->getNombreModeloElemento(),
                                                'gemport'               => '',
                                                'service_profile'       => $strServiceProfile,
                                                'capacidad_up'          => $objSpcCapacidad1->getValor(),
                                                'capacidad_down'        => $objSpcCapacidad2->getValor(),
                                                'line_profile'          => '',
                                                'traffic_table'         => '',
                                                'ont_id'                => $objSpcIndiceClienteAnterior->getValor(),
                                                'service_port'          => $objSpcSpidAnterior->getValor(),
                                                'vlan'                  => '',
                                                'estado_servicio'       => $objServicio->getEstado(),
                                                'ip'                    => $strIpFijaActual,
                                                'scope'                 => $strScopeActual,
                                                'ip_fijas_activas'      => $intNumIpsAdicionales,
                                                'tipo_negocio_actual'   => $strTipoNegocio,
                                                'serial_ont_nueva'      => $strSerieCpeNuevo,
                                                'mac_ont_nueva'         => $strMacCpeNuevo,
                                                'service_profile_nuevo' => $strModeloCpeNuevo
                                          );
            if ($strPrefijoEmpresa === 'MD')
            {
                $arrayRespuestaSeteaInfo = $this->servicioGeneral
                                                ->seteaInformacionPlanesPyme(array("intIdPlan"         => $objServicio->getPlanId()->getId(),
                                                                                   "intIdPunto"        => $objServicio->getPuntoId()->getId(),
                                                                                   "strConservarIp"    => "",
                                                                                   "strTipoNegocio"    => $strTipoNegocio,
                                                                                   "strPrefijoEmpresa" => $strPrefijoEmpresa,
                                                                                   "strUsrCreacion"    => $strUsrCreacion,
                                                                                   "strIpCreacion"     => $strIpCreacion,
                                                                                   "strTipoProceso"    => 'CAMBIAR_ELEMENTO',
                                                                                   "arrayInformacion"  => $arrayDatosMiddleware));
                if($arrayRespuestaSeteaInfo["strStatus"]  === "OK")
                {
                    $arrayDatosMiddleware = $arrayRespuestaSeteaInfo["arrayInformacion"];
                }
                else
                {
                    throw new \Exception("Existieron problemas al recuperar información necesaria ".
                                         "para ejecutar proceso, favor notifique a Sistemas.");
                }
            }
            $arrayPeticionMiddleware    = array(
                                                'empresa'               => $strPrefijoEmpresa,
                                                'nombre_cliente'        => $strNombreCliente,
                                                'login'                 => $objPunto->getLogin(),
                                                'identificacion'        => $strIdentificacion,
                                                'datos'                 => $arrayDatosMiddleware,
                                                'opcion'                => $this->opcion,
                                                'ejecutaComando'        => $this->ejecutaComando,
                                                'usrCreacion'           => $strUsrCreacion,
                                                'ipCreacion'            => $strIpCreacion
                                          );
            $arrayRespuestaMiddleware   = $this->rdaMiddleware->middleware(json_encode($arrayPeticionMiddleware));
            $strStatusActivar           = isset($arrayRespuestaMiddleware['status_activar']) ? $arrayRespuestaMiddleware['status_activar']
                                          : $arrayRespuestaMiddleware['status'];
            $strStatusCancelar          = isset($arrayRespuestaMiddleware['status_cancelar']) ? $arrayRespuestaMiddleware['status_cancelar']
                                          : $arrayRespuestaMiddleware['status'];
            if($strStatusActivar === 'OK' && $strStatusCancelar === 'OK')
            {
                $arrayDatosConfirmacionTn                           = $arrayDatosMiddleware;
                $arrayDatosConfirmacionTn['opcion_confirmacion']    = $this->opcion;
                $arrayDatosConfirmacionTn['respuesta_confirmacion'] = 'ERROR';
                $arrayDataConfirmacionTn    = array('nombre_cliente'    => $strNombreCliente,
                                                    'login'             => $objPunto->getLogin(),
                                                    'identificacion'    => $strIdentificacion,
                                                    'datos'             => $arrayDatosConfirmacionTn,
                                                    'opcion'            => $this->strConfirmacionTNMiddleware,
                                                    'ejecutaComando'    => $this->ejecutaComando,
                                                    'usrCreacion'       => $strUsrCreacion,
                                                    'ipCreacion'        => $strIpCreacion,
                                                    'empresa'           => $strPrefijoEmpresa,
                                                    'statusMiddleware'  => 'OK');
                
                $arrayCpeNaf        = $this->servicioGeneral->buscarElementoEnNaf($strSerieCpeNuevo, $strModeloCpeNuevo , "PI", "ActivarServicio");
                $strStatusCpeNaf    = $arrayCpeNaf[0]['status'];
                $strMensajeCpeNaf   = $arrayCpeNaf[0]['mensaje'];
                if($strStatusCpeNaf === "OK")
                {
                    $arrayParametrosNaf = array(   'tipoArticulo'          => 'AF',
                                                   'identificacionCliente' => '',
                                                   'empresaCod'            => '',
                                                   'modeloCpe'             => $strModeloCpeNuevo,
                                                   'serieCpe'              => $strSerieCpeNuevo,
                                                   'cantidad'              => '1');
                    $strMensajeErrorNaf = $this->procesaInstalacionElemento($arrayParametrosNaf);
                    if(strlen(trim($strMensajeErrorNaf)) > 0)
                    {
                        throw new \Exception("ERROR WIFI NAF: " . $strMensajeErrorNaf);
                    }
                }
                else
                {
                    throw new \Exception($strMensajeCpeNaf);
                }
            }
            else
            {
                if(isset($arrayRespuestaMiddleware['mensaje']))
                {
                    throw new \Exception("Error: ".$arrayRespuestaMiddleware['mensaje']);
                }
                else
                {
                    throw new \Exception("Cancelar: ".$arrayRespuestaMiddleware['mensaje_cancelar']
                                         ." Activar: ".$arrayRespuestaMiddleware['mensaje_activar']);
                }
            }
            $objCaractServiceProfile    = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                            ->findOneBy(array(  "descripcionCaracteristica" => "SERVICE-PROFILE",
                                                                                "estado"                    => "Activo")); 
            if(is_object($objCaractServiceProfile))
            {
                $objProdCaractServiceProfile    = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                    ->findOneBy(array("productoId"          => $objProductoInternet->getId(),
                                                                                      "caracteristicaId"    => $objCaractServiceProfile->getId())
                                                                               );
                if(is_object($objProdCaractServiceProfile))
                {
                    $arrayServProdCaractsServiceProfiles    = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                                ->findBy(array( "servicioId"                => $objServicio->getId(),
                                                                                                "productoCaracterisiticaId" => 
                                                                                                $objProdCaractServiceProfile->getId(),
                                                                                                "estado"                    => 'Activo'));
                    foreach($arrayServProdCaractsServiceProfiles as $objServProdCaractServiceProfile)
                    {
                        $objServProdCaractServiceProfile->setEstado('Eliminado');
                        $objServProdCaractServiceProfile->setUsrUltMod($strUsrCreacion);
                        $objServProdCaractServiceProfile->setFeUltMod(new \DateTime('now'));
                        $this->emComercial->persist($objServProdCaractServiceProfile);
                        $this->emComercial->flush();
                    }
                }
            }
            $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicio, $objProductoInternet, "SERVICE-PROFILE",
                                                                            $strModeloCpeNuevo, $strUsrCreacion);
            $intIdInterfaceElementoSplitter = $objServicioTecnico->getInterfaceElementoConectorId();
            $intIdInterfaceElementoCpeOnt   = $objServicioTecnico->getInterfaceElementoClienteId();
            $intIdElementoCpeOnt            = $objServicioTecnico->getElementoClienteId();
            $objTipoSolicitudCambio = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                        ->findOneBy(array(  "descripcionSolicitud"  => "SOLICITUD CAMBIO EQUIPO", 
                                                                            "estado"                => "Activo"));
            $objSolicitudCambioCpe  = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                        ->findOneBy(array("servicioId"      => $objServicio->getId(), 
                                                                          "tipoSolicitudId" => $objTipoSolicitudCambio, 
                                                                          "estado"          => "AsignadoTarea"));
            if(is_object($objSolicitudCambioCpe))
            {
                $strEstadoDetalleSol    = "AsignadoTarea";
                $objSolicitudCambioCpe->SetEstado("Finalizada");
                $this->emComercial->persist($objSolicitudCambioCpe);
                $this->emComercial->flush();
            }
            else
            {
                $strEstadoDetalleSol    = "Finalizada";
                $objTipoSolicitudCambio = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                            ->findOneBy(array("descripcionSolicitud"    => $strTipoSolicitud,
                                                                              "estado"                  => "Activo"));
                $objSolicitudCambioCpe  = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                            ->findOneBy(array("servicioId"      => $objServicio->getId(), 
                                                                              "tipoSolicitudId" => $objTipoSolicitudCambio, 
                                                                              "estado"          => $strEstadoSolicitud));
                $arrayCaracteristicasSolicitud = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                    ->findBy(array("detalleSolicitudId" => $objSolicitudCambioCpe->getId(), 
                                                                                   "estado"             => $strEstadoSolicitud));
                foreach($arrayCaracteristicasSolicitud as $objCaracteristicaSolicitud)
                {
                    $objCaracteristicaSolicitud->setEstado("Finalizada");
                    $objCaracteristicaSolicitud->setUsrCreacion($strUsrCreacion);
                    $objCaracteristicaSolicitud->setFeCreacion(new \DateTime('now'));
                    $this->emComercial->persist($objCaracteristicaSolicitud);
                    $this->emComercial->flush();
                }
                if($strEsCambioOntPorSolAgregarEquipo === "SI")
                {
                    $objSolicitudCambioCpe->setEstado("Finalizada");
                    $this->emComercial->persist($objSolicitudCambioCpe);
                    $this->emComercial->flush();
                }
                else
                {
                    $objDetalleSolCaractActiva  = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                    ->findOneBy(array("detalleSolicitudId"    => $objSolicitudCambioCpe->getId(), 
                                                                                      "estado"                => "Activo"));
                    if(!is_object($objDetalleSolCaractActiva))
                    {
                        $objSolicitudCambioCpe->setEstado("Finalizada");
                        $this->emComercial->persist($objSolicitudCambioCpe);
                        $this->emComercial->flush();
                    }
                }
            }

            if($objSolicitudCambioCpe->getEstado() === "Finalizada")
            {
                $objTipoSolRetiroEquipo = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                            ->findOneBy(array("descripcionSolicitud" => "SOLICITUD RETIRO EQUIPO", 
                                                                              "estado"               => "Activo"));
                $objSolicitudRetiroEquipo = new InfoDetalleSolicitud();
                $objSolicitudRetiroEquipo->setServicioId($objServicio);
                $objSolicitudRetiroEquipo->setTipoSolicitudId($objTipoSolRetiroEquipo);
                $objSolicitudRetiroEquipo->setEstado("AsignadoTarea");
                $objSolicitudRetiroEquipo->setUsrCreacion($strUsrCreacion);
                $objSolicitudRetiroEquipo->setFeCreacion(new \DateTime('now'));
                $objSolicitudRetiroEquipo->setObservacion("SOLICITA RETIRO DE EQUIPO POR CAMBIO DE MODEM");
                $this->emComercial->persist($objSolicitudRetiroEquipo);
                $this->emComercial->flush();

                $objCaractElementoCliente   = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                ->findOneBy(array('descripcionCaracteristica'   => 'ELEMENTO CLIENTE',
                                                                                  'estado'                      => 'Activo'));

                $arrayCaractSolicitudCambioElemento = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                        ->findBy(array("detalleSolicitudId" => $objSolicitudCambioCpe->getId(),
                                                                                       "caracteristicaId"   => $objCaractElementoCliente,
                                                                                       "estado"             => $strEstadoDetalleSol));

                for($intIndex = 0; $intIndex < count($arrayCaractSolicitudCambioElemento); $intIndex++)
                {
                    $objSolCaractRetiroEquipo = new InfoDetalleSolCaract();
                    $objSolCaractRetiroEquipo->setCaracteristicaId($objCaractElementoCliente);
                    $objSolCaractRetiroEquipo->setDetalleSolicitudId($objSolicitudRetiroEquipo);
                    $objSolCaractRetiroEquipo->setValor($arrayCaractSolicitudCambioElemento[$intIndex]->getValor());
                    $objSolCaractRetiroEquipo->setEstado("AsignadoTarea");
                    $objSolCaractRetiroEquipo->setUsrCreacion($strUsrCreacion);
                    $objSolCaractRetiroEquipo->setFeCreacion(new \DateTime('now'));
                    $this->emComercial->persist($objSolCaractRetiroEquipo);
                    $this->emComercial->flush();
                }

                $objDetalleCambioCpe        = $this->emComercial->getRepository('schemaBundle:InfoDetalle')
                                                                ->findOneBy(array("detalleSolicitudId" => $objSolicitudCambioCpe->getId()));
                $objProcesoRetiroEquipo     = $this->emSoporte->getRepository('schemaBundle:AdmiProceso')
                                                            ->findOneByNombreProceso("SOLICITAR RETIRO EQUIPO");
                $arrayTareasRetiroEquipo    = $this->emSoporte->getRepository('schemaBundle:AdmiTarea')
                                                              ->findTareasActivasByProceso($objProcesoRetiroEquipo->getId());
                $objTareaRetiroEquipo       = $arrayTareasRetiroEquipo[0];

                $objDetalleRetiroEquipo = new InfoDetalle();
                $objDetalleRetiroEquipo->setDetalleSolicitudId($objSolicitudRetiroEquipo->getId());
                $objDetalleRetiroEquipo->setTareaId($objTareaRetiroEquipo);
                $objDetalleRetiroEquipo->setLongitud($objPunto->getLongitud());
                $objDetalleRetiroEquipo->setLatitud($objPunto->getLatitud());
                $objDetalleRetiroEquipo->setPesoPresupuestado(0);
                $objDetalleRetiroEquipo->setValorPresupuestado(0);
                $objDetalleRetiroEquipo->setIpCreacion($strIpCreacion);
                $objDetalleRetiroEquipo->setFeCreacion(new \DateTime('now'));
                $objDetalleRetiroEquipo->setUsrCreacion($strUsrCreacion);
                $this->emSoporte->persist($objDetalleRetiroEquipo);
                $this->emSoporte->flush();

                if(is_object($objDetalleCambioCpe))
                {
                    $objDetalleAsignacionCambioCpe = $this->emComercial->getRepository('schemaBundle:InfoDetalleAsignacion')
                                                                    ->findOneBy(array("detalleId" => $objDetalleCambioCpe->getId()));

                    //asignar los mismos responsables a la solicitud de retiro de equipo
                    $objDetalleAsignacionRetiroEquipo = new InfoDetalleAsignacion();
                    $objDetalleAsignacionRetiroEquipo->setDetalleId($objDetalleRetiroEquipo);
                    $objDetalleAsignacionRetiroEquipo->setAsignadoId($objDetalleAsignacionCambioCpe->getAsignadoId());
                    $objDetalleAsignacionRetiroEquipo->setAsignadoNombre($objDetalleAsignacionCambioCpe->getAsignadoNombre());
                    $objDetalleAsignacionRetiroEquipo->setRefAsignadoId($objDetalleAsignacionCambioCpe->getRefAsignadoId());
                    $objDetalleAsignacionRetiroEquipo->setRefAsignadoNombre($objDetalleAsignacionCambioCpe->getRefAsignadoNombre());
                    $objDetalleAsignacionRetiroEquipo->setPersonaEmpresaRolId($objDetalleAsignacionCambioCpe->getPersonaEmpresaRolId());
                    $objDetalleAsignacionRetiroEquipo->setTipoAsignado("EMPLEADO");
                    $objDetalleAsignacionRetiroEquipo->setIpCreacion($strIpCreacion);
                    $objDetalleAsignacionRetiroEquipo->setFeCreacion(new \DateTime('now'));
                    $objDetalleAsignacionRetiroEquipo->setUsrCreacion($strUsrCreacion);
                    $this->emSoporte->persist($objDetalleAsignacionRetiroEquipo);
                    $this->emSoporte->flush();

                    $objPersonaEmpresaRolUsr = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                 ->find($objDetalleAsignacionCambioCpe->getPersonaEmpresaRolId());
                    if(is_object($objPersonaEmpresaRolUsr))
                    {
                        $intIdPersona = $objPersonaEmpresaRolUsr->getPersonaId()->getId();
                    }
                }

                $objDetalleSolHistorial = new InfoDetalleSolHist();
                $objDetalleSolHistorial->setDetalleSolicitudId($objSolicitudRetiroEquipo);
                $objDetalleSolHistorial->setEstado("AsignadoTarea");
                $objDetalleSolHistorial->setObservacion("GENERACION AUTOMATICA DE SOLICITUD RETIRO DE EQUIPO POR CAMBIO DE MODEM");
                $objDetalleSolHistorial->setUsrCreacion($strUsrCreacion);
                $objDetalleSolHistorial->setFeCreacion(new \DateTime('now'));
                $objDetalleSolHistorial->setIpCreacion($strIpCreacion);
                $this->emComercial->persist($objDetalleSolHistorial);
                $this->emComercial->flush();
                
                $objDetalleSolHistorialCambioCpe = new InfoDetalleSolHist();
                $objDetalleSolHistorialCambioCpe->setDetalleSolicitudId($objSolicitudCambioCpe);
                $objDetalleSolHistorialCambioCpe->setEstado($objSolicitudCambioCpe->getEstado());
                $objDetalleSolHistorialCambioCpe->setObservacion("Finalizacion de cambio de CPE");
                $objDetalleSolHistorialCambioCpe->setUsrCreacion($strUsrCreacion);
                $objDetalleSolHistorialCambioCpe->setFeCreacion(new \DateTime('now'));
                $objDetalleSolHistorialCambioCpe->setIpCreacion($strIpCreacion);
                $this->emComercial->persist($objDetalleSolHistorialCambioCpe);
                $this->emComercial->flush();
            }
            
            $arrayParametrosCambioCpeOnt    = array(
                                                    "objPunto"                          => $objServicio->getPuntoId(),
                                                    "intIdServicio"                     => $objServicio->getId(),
                                                    "intIdPersona"                      => $intIdPersona,
                                                    "objServicioProdCaractMacAnterior"  => $objSpcMacOnt,
                                                    "intIdProductoCaracteristicaMac"    => $objProdCaractMacOnt->getId(),
                                                    "intIdInterfaceElementoIzq"         => $intIdInterfaceElementoSplitter,
                                                    "intIdInterfaceElementoAnterior"    => $intIdInterfaceElementoCpeOnt,
                                                    "intIdElementoAnterior"             => $intIdElementoCpeOnt,
                                                    "strSerieElementoNuevo"             => $strSerieCpeNuevo,
                                                    "strMacElementoNuevo"               => $strMacCpeNuevo,
                                                    "strModeloElementoNuevo"            => $strModeloCpeNuevo,
                                                    "strTipoElementoNuevo"              => "-ont",
                                                    "strCodEmpresa"                     => $strCodEmpresa,
                                                    "strUsrCreacion"                    => $strUsrCreacion,
                                                    "strIpCreacion"                     => $strIpCreacion,
                                                    "strExisteIpWan"                    => $strExisteIpWan,
                                                    "intIdServicioAdicIpWan"            => $intIdServicioAdicIpWan,
                                                    "objSpcMacIpWan"                    => $objSpcMacIpWan,
                                                    "objProductoInternet"               => $objProductoInternet
                                              );

            $arrayCambioCpeOnt              = $this->cambioElementoCpeOntWifiEnlacesInterfacesMd($arrayParametrosCambioCpeOnt);
            $strStatusCambioCpeOnt          = $arrayCambioCpeOnt['strStatus'];
            $strMensajeCambioCpeOnt         = $arrayCambioCpeOnt['strMensaje'];

            if($strStatusCambioCpeOnt === "OK")
            {
                $objElementoNuevoCpeOnt                     = $arrayCambioCpeOnt["objElementoNuevo"];
                $objInterfaceElementoNuevoCpeOntConnected   = $arrayCambioCpeOnt["objInterfaceElementoNuevoConnected"];
                if(is_object($objElementoNuevoCpeOnt) && is_object($objInterfaceElementoNuevoCpeOntConnected))
                {
                    $objServicioTecnico->setInterfaceElementoClienteId($objInterfaceElementoNuevoCpeOntConnected->getId());
                    $objServicioTecnico->setElementoClienteId($objElementoNuevoCpeOnt->getId());
                    $this->emComercial->persist($objServicioTecnico);
                    $this->emComercial->flush();
                }
                else
                {
                    throw new \Exception("No se ha podido obtener correctamente el elemento y la interface cliente nueva");
                }
                if($strEsCambioOntPorSolAgregarEquipo === "SI")
                {
                    $arrayParametroOrigenTelcos = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                  ->getOne( 'PARAMETROS_GENERALES_MOVIL', 
                                                                            '', 
                                                                            '', 
                                                                            '', 
                                                                            'ORIGEN_TELCOS', 
                                                                            $strOrigen, 
                                                                            '', 
                                                                            ''
                                                                           );
                    if(isset($arrayParametroOrigenTelcos) && !empty($arrayParametroOrigenTelcos))
                    {
                        $arrayParamsSolCambioOntPorSolAgregarEquipo = array(   
                                                                            "intIdDetalleSolicitud"   => $objSolicitudCambioCpe->getId(),
                                                                            "strProceso"              => 
                                                                            "SEGUIMIENTO_GENERAL:Solicitud asociada a esta tarea fue finalizada "
                                                                            ."por cambio de ont");
                        $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                ->cerrarTareasPorSolicitud($arrayParamsSolCambioOntPorSolAgregarEquipo);
                    }
                }
            }
            else
            {
                throw new \Exception($strMensajeCambioCpeOnt);
            }
            
            $objServicioHistorial = new InfoServicioHistorial();
            $objServicioHistorial->setServicioId($objServicio);
            $objServicioHistorial->setObservacion("Se Realizo un Cambio de Elemento Cliente " . $strTipoElementoCpe);
            $objServicioHistorial->setEstado($objServicio->getEstado());
            $objServicioHistorial->setUsrCreacion($strUsrCreacion);
            $objServicioHistorial->setFeCreacion(new \DateTime('now'));
            $objServicioHistorial->setIpCreacion($strIpCreacion);
            $this->emComercial->persist($objServicioHistorial);
            $this->emComercial->flush();
            
            $arrayDataConfirmacionTn['datos']['respuesta_confirmacion'] = "OK";
            $strStatus  = "OK";
            $strMensaje = "OK";
        } 
        catch (\Exception $e) 
        {
            $strStatus  = "ERROR";
            $strMensaje = $e->getMessage();
            $this->serviceUtil->insertError("Telcos+",
                                            "InfoCambioElementoService->cambioElementoZte",
                                            $e->getMessage(),
                                            $strUsrCreacion,
                                            $strIpCreacion
                                           );
        }
        $arrayRespuestaFinal[] = array(
            'status'                           => $strStatus,
            'mensaje'                          => $strMensaje,
            'strEquipoActualEstaParametrizado' => $strOntActualEstaParametrizado,
            'strEquipoNuevoEstaParametrizado'  => $strOntNuevoEstaParametrizado,
            "arrayDataConfirmacionTn"          => $arrayDataConfirmacionTn
        );
        return $arrayRespuestaFinal;
    }
    
    /**
     * cambioElementoExtenderDualBand
     * 
     * Service que realiza el cambio del elemento de un Extender Dual Band
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 03-12-2018
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.1 21-06-2019   Se modifica service utilizado para registrar el log de errores de la operación
     * @since 1.0
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 03-02-2021 Se agrega validación de modelo al cambiar un Extender Dual Band y se invoca al web service de middleware
     *                         para realizar el cambio de extender
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.3 25-03-2021 Se valida que solo los modelos de extender parametrizados sean permitidos para un CPE ONT, ejemplo extenders
     *                         permitidos para CPE ONT V5
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 26-04-2021 Se corrige validación que verifica el modelo de extender por el modelo del ont del servicio de Internet
     * 
     * @author Ruben Vera <rhvera@telconet.ec>
     * @version 1.4 04-05-2023 Se agregó el filtro Modelo Olt a la consulta que se hace para verificar los modelos del extender dual band
     * 
     * @author Ruben Vera <rhvera@telconet.ec>
     * @version 1.5 04-05-2023 Se modificó la función para obtener modelos extenders por Ont
     * 
     * @param Array $arrayParametros [ 
     *                                 servicio               => Objeto de la tabla InfoServicio
     *                                 servicioTecnico        => Objeto de la tabla InfoServicioTecnico
     *                                 serieCpe               => Cadena de caracteres con serie de cpe 
     *                                 codigoArticulo         => Cadena de caracteres con modelo de cpe
     *                                 nombreCpe              => Cadena de caracteres con nombre de cpe
     *                                 macCpe                 => Cadena de caracteres con mac de cpe
     *                                 tipoElementoCpe        => Cadena de caracteres con tipo de elemento de cpe 
     *                                 idEmpresa              => Cadena de caracteres con identificador de empresa
     *                                 idElementoCliente      => Numero entero identificador de elemento del cliente
     *                                 usrCreacion            => Cadena de caracteres con usuario de creacion a utilizar
     *                                 ipCreacion             => Cadena de caracteres con ip de creacion a utilizar
     *                                 prefijoEmpresa         => Cadena de caracteres con prefigo de empresa a utilizar
     *                               ]
     * @return Array $arrayRespuestaFinal
     * 
     */
    public function cambioElementoExtenderDualBand($arrayParametros) 
    {
        $objServicio                        = !empty($arrayParametros['servicio'])?$arrayParametros['servicio']:null;
        $objServicioTecnico                 = !empty($arrayParametros['servicioTecnico'])?$arrayParametros['servicioTecnico']:null;
        $strSerieExtenderDualBand           = !empty($arrayParametros['serieCpe'])?$arrayParametros['serieCpe']:"";
        $strModeloExtenderDualBand          = !empty($arrayParametros['codigoArticulo'])?$arrayParametros['codigoArticulo']:"";
        $strTipoElementoExtenderDualBand    = !empty($arrayParametros['tipoElementoCpe'])?$arrayParametros['tipoElementoCpe']:"";
        $strMacExtenderDualBand             = !empty($arrayParametros['macCpe'])?$arrayParametros['macCpe']:"";
        $intIdElementoCliente               = !empty($arrayParametros['idElementoCliente'])?$arrayParametros['idElementoCliente']:0;
        $strEmpresaCod                      = !empty($arrayParametros['idEmpresa'])?$arrayParametros['idEmpresa']:"";
        $strUsrCreacion                     = !empty($arrayParametros['usrCreacion'])?$arrayParametros['usrCreacion']:"";
        $strIpCreacion                      = !empty($arrayParametros['ipCreacion'])?$arrayParametros['ipCreacion']:"";
        $strUltimaMilla                     = '';
        $strTipoArticulo                    = "AF";
        $strIdentificacionCliente           = "";
        $strOntActualEstaParametrizado      = "NO"; // para validar extenders permitidos por ONT por ejemplo: extenders para ONT V5
        $arrayRespuestaFinal                = array();
        $arrayParametrosAuditoria           = array();
        $boolMensajeUsuario                 = false;
        try
        {
            if(!is_object($objServicio))
            {
                $boolMensajeUsuario = true;
                throw new \Exception("No existe información del servicio, favor revisar!");
            }

            if(!is_object($objServicioTecnico))
            {
                $boolMensajeUsuario = true;
                throw new \Exception("No existe información técnica del servicio, favor revisar!");
            }
            $objElementoExtenderDualBand    = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                      ->find($intIdElementoCliente);
            if(!is_object($objElementoExtenderDualBand))
            {
                $boolMensajeUsuario = true;
                throw new \Exception("No existe el elemento Extender Dual Band del cliente, favor revisar!");
            }

            $objTipoMedio = $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')
                                                    ->find($objServicioTecnico->getUltimaMillaId());
            if (is_object($objTipoMedio))
            {
                $strUltimaMilla = $objTipoMedio->getNombreTipoMedio(); 
            }
            
            $objProductoInternet    = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                        ->findOneBy(array(  'empresaCod'    => $strEmpresaCod, 
                                                                            'nombreTecnico' => 'INTERNET',
                                                                            'estado'        => 'Activo'));
            if(!is_object($objProductoInternet))
            {
                $boolMensajeUsuario = true;
                throw new \Exception("No se ha podido obtener el producto INTERNET, favor revisar!");
            }
            
            $objPunto                       = $objServicio->getPuntoId();
            $objPersonaEmpresaRol           = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                ->find($objPunto->getPersonaEmpresaRolId()->getId());
            $objPersona                     = $objPersonaEmpresaRol->getPersonaId();
            $strIdentificacion              = $objPersona->getIdentificacionCliente();
            $strNombreCliente               = $objPersona->__toString();
            $strTipoNegocio                 = $objPunto->getTipoNegocioId()->getNombreTipoNegocio();
            $arrayParamsServInternetValido  = array("intIdPunto"                => $objPunto->getId(),
                                                    "strCodEmpresa"             => $strEmpresaCod,
                                                    "arrayEstadosInternetIn"    => array("Activo"));
            $arrayRespuestaServInternetValido   = $this->servicioGeneral->obtieneServicioInternetValido($arrayParamsServInternetValido);
            $objServicioInternet                = $arrayRespuestaServInternetValido["objServicioInternet"];
            if(!is_object($objServicioInternet))
            {
                $boolMensajeUsuario = true;
                throw new \Exception("No se encontró el servicio de Internet en estado Activo asociado al punto");
            }
            $intIdServicioInternet      = $objServicioInternet->getId();
            $objServicioTecnicoInternet = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                            ->findOneBy(array( "servicioId" => $intIdServicioInternet));
            if(!is_object($objServicioTecnicoInternet))
            {
                $boolMensajeUsuario = true;
                throw new \Exception("No se ha podido obtener la información técnica del servicio de Internet");
            }
            $strModeloElementoOlt           = "";
            $strIpElementoOlt               = "";
            $intIdElementoOlt               = $objServicioTecnicoInternet->getElementoId();
            $intIdInterfaceOlt              = $objServicioTecnicoInternet->getInterfaceElementoId();
            $intIdElementoOnt               = $objServicioTecnicoInternet->getElementoClienteId();
            $objElementoOlt                 = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($intIdElementoOlt);
            $objInterfaceOlt                = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')->find($intIdInterfaceOlt);
            if(is_object($objElementoOlt))
            {
                $strModeloElementoOlt   = $objElementoOlt->getModeloElementoId()->getNombreModeloElemento();
            }
            
            if(!isset($intIdElementoOnt) || empty($intIdElementoOnt))
            {
                throw new \Exception("No se ha podido obtener el id del elemento cliente del servicio de Internet");
            }
            
            $objElementoOnt = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($intIdElementoOnt);
            if(!is_object($objElementoOnt))
            {
                throw new \Exception("No se ha podido obtener el objeto del elemento cliente del servicio de Internet");
            }
            
            //Se obtiene el modelo del CPE ONT actual, asociado al servicio.
            $strModeloCpeOntActual = $objElementoOnt->getModeloElementoId()->getNombreModeloElemento();
            error_log("strModeloCpeOntActual".$strModeloCpeOntActual);
            
            //Se consulta si el ONT actual esta dentro de los parametrizados
            $strNombreParametro         = 'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD';
            $strCampoValor1             = 'MODELOS_EQUIPOS';
            $strCampoValor6             = 'ONT';
            $arrayModelosParametrizados = array(); //por ejemplo modelo: ONT V5 => EG8M8145V5G06
            $arrayAdmiParametroOnt      = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                          ->get($strNombreParametro,
                                                                '',
                                                                '',
                                                                '',
                                                                $strCampoValor1,
                                                                '',
                                                                '',
                                                                '',
                                                                '',
                                                                $strEmpresaCod,
                                                                '',
                                                                $strCampoValor6);

            if(is_array($arrayAdmiParametroOnt) && count($arrayAdmiParametroOnt) > 0)
            {
                foreach($arrayAdmiParametroOnt as $arrayParametroOnt)
                {
                    $arrayModelosParametrizados[] = $arrayParametroOnt['valor5'];
                }
            }
            
            if(in_array($strModeloCpeOntActual, $arrayModelosParametrizados))
            {
                $strOntActualEstaParametrizado = "SI";
            }
            
            //Si el ont esta dentro de los parametrizados, entonces se busca si el nuevo extender es un modelo válido y está dentro de los 
            //permitidos para el ont actual, ejemplo: extenders permitidos para un V5.
            if($strOntActualEstaParametrizado == "SI")
            {
                $arrayVerificaModeloEdb = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                          ->get( 'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD', 
                                                                    '',
                                                                    '',
                                                                    '',
                                                                    'MODELOS_EXTENDERS_POR_ONT',
                                                                    '',
                                                                    '',
                                                                    $strModeloCpeOntActual,
                                                                    $strModeloExtenderDualBand,
                                                                    $strEmpresaCod);

                if(!isset($arrayVerificaModeloEdb) || empty($arrayVerificaModeloEdb))
                {
                    $boolMensajeUsuario = true;
                    throw new \Exception("El modelo ".$strModeloExtenderDualBand. " no es considerado como un Extender,"
                                         . "para el ONT: ".$strModeloCpeOntActual);
                }
            }
            else
            {
                $arrayVerificaModeloEdb = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                          ->getOne( 'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD', 
                                                                    '',
                                                                    '',
                                                                    '',
                                                                    'MODELOS_EQUIPOS',
                                                                    '',
                                                                    $strModeloElementoOlt,
                                                                    'EXTENDER DUAL BAND',
                                                                    $strModeloExtenderDualBand,
                                                                    $strEmpresaCod);

                if(!isset($arrayVerificaModeloEdb) || empty($arrayVerificaModeloEdb))
                {
                    $boolMensajeUsuario = true;
                    throw new \Exception("El modelo ".$strModeloExtenderDualBand." no es considerado como un Extender");
                }
            }
            
            $objIpElementoOlt   = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                          ->findOneBy(array("elementoId" => $intIdElementoOlt));
            if(is_object($objIpElementoOlt))
            {
                $strIpElementoOlt = $objIpElementoOlt->getIp();
            }
            
            $objSpcIndiceCliente    = $this->servicioGeneral ->getServicioProductoCaracteristica(   $objServicioInternet, 
                                                                                                    "INDICE CLIENTE", 
                                                                                                    $objProductoInternet);
            $strIndiceCliente       = "";
            if(is_object($objSpcIndiceCliente))
            {
                $strIndiceCliente   = $objSpcIndiceCliente->getValor();
            }
            
            $strServiceProfile      = "";
            $objSpcServiceProfile   = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioInternet, 
                                                                                                "SERVICE-PROFILE", 
                                                                                                $objProductoInternet);
            if(is_object($objSpcServiceProfile))
            {
                $strServiceProfile = $objSpcServiceProfile->getValor();
            }
            
            $objDetEleMacExtender   = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                               ->findOneBy(array(   'elementoId'    => $objElementoExtenderDualBand->getId(),
                                                                                    'detalleNombre' => 'MAC',
                                                                                    'estado'        => 'Activo'
                                                                                )
                                                                             );
            $strMacExtenderDb = "";
            if(is_object($objDetEleMacExtender))
            {
                $strMacExtenderDb = $objDetEleMacExtender->getDetalleValor();
            }
            $arrayParamsExtenders           = array('intInterfaceElementoConectorId' => $objServicioTecnicoInternet->getInterfaceElementoClienteId(),
                                                    'strTipoSmartWifi'               => 'ExtenderDualBand',
                                                    'arrayData'                      => array());
            $arrayElementosExtenderDualBand = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                      ->getElementosSmartWifiByInterface($arrayParamsExtenders);
            
            $arrayDatosCambioExtender   = array(
                                                'serie_extender'        => $objElementoExtenderDualBand->getSerieFisica(),
                                                'serie_extender_nueva'  => $strSerieExtenderDualBand,
                                                'mac_extender'          => $strMacExtenderDb,
                                                'mac_extender_nueva'    => $strMacExtenderDualBand,
                                                'estado_servicio'       => $objServicio->getEstado(),
                                                'ip_olt'                => $strIpElementoOlt,
                                                'tipo_negocio_actual'   => $strTipoNegocio,
                                                'numero_de_extender'    => count($arrayElementosExtenderDualBand),
                                                'puerto_olt'            => $objInterfaceOlt->getNombreInterfaceElemento(),
                                                'ont_id'                => $strIndiceCliente,
                                                'service_profile'       => $strServiceProfile,
                                                'modelo_olt'            => $strModeloElementoOlt
                                            );

            $arrayDatosMiddleware       = array(
                                                'nombre_cliente'        => $strNombreCliente,
                                                'login'                 => $objPunto->getLogin(),
                                                'identificacion'        => $strIdentificacion,
                                                'datos'                 => $arrayDatosCambioExtender,
                                                'opcion'                => "CAMBIAR_EXTENDER",
                                                'ejecutaComando'        => $this->ejecutaComando,
                                                'usrCreacion'           => $strUsrCreacion,
                                                'ipCreacion'            => $strIpCreacion
                                            );
            $arrayRespuestaMiddleware   = $this->rdaMiddleware->middleware(json_encode($arrayDatosMiddleware));
            $strStatusMiddleware        = $arrayRespuestaMiddleware['status'];
            $strMensajeMiddleware       = $arrayRespuestaMiddleware['mensaje'];
            if($strStatusMiddleware !== "OK")
            {
                $boolMensajeUsuario = true;
                throw new \Exception($strMensajeMiddleware);
            }
            
            $objElementoExtenderDualBand->setEstado("Eliminado");
            $this->emInfraestructura->persist($objElementoExtenderDualBand);
            $this->emInfraestructura->flush();

            //SE REGISTRA EL TRACKING DEL ELEMENTO - ANTERIOR
            $arrayParametrosAuditoria["strNumeroSerie"]  = $objElementoExtenderDualBand->getSerieFisica();
            $arrayParametrosAuditoria["strEstadoTelcos"] = 'Eliminado';
            $arrayParametrosAuditoria["strEstadoNaf"]    = 'Instalado';
            $arrayParametrosAuditoria["strEstadoActivo"] = 'CambioEquipo';
            $arrayParametrosAuditoria["strUbicacion"]    = 'EnTransito';
            $arrayParametrosAuditoria["strCodEmpresa"]   = $strEmpresaCod;
            $arrayParametrosAuditoria["strTransaccion"]  = 'Cambio de Elemento';
            $arrayParametrosAuditoria["intOficinaId"]    = 0;

            //Se consulta el login del cliente
            if(is_object($objServicioTecnico->getServicioId()))
            {
                $objInfoPunto = $this->emInfraestructura->getRepository('schemaBundle:InfoPunto')
                                                        ->find($objServicioTecnico->getServicioId()->getPuntoId()->getId());
                if(is_object($objInfoPunto))
                {
                    $arrayParametrosAuditoria["strLogin"] = $objInfoPunto->getLogin();
                }
            }

            $arrayParametrosAuditoria["strUsrCreacion"] = $strUsrCreacion;

            //historial del elemento
            $objHistorialElemento = new InfoHistorialElemento();
            $objHistorialElemento->setElementoId($objElementoExtenderDualBand);
            $objHistorialElemento->setObservacion("Se elimino el elemento por Cambio de equipo");
            $objHistorialElemento->setEstadoElemento("Eliminado");
            $objHistorialElemento->setUsrCreacion($strUsrCreacion);
            $objHistorialElemento->setFeCreacion(new \DateTime('now'));
            $objHistorialElemento->setIpCreacion($strIpCreacion);
            $this->emInfraestructura->persist($objHistorialElemento);
            $this->emInfraestructura->flush();

            //eliminar puertos del elemento
            $arrayInterfacesElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                               ->findBy(array("elementoId" => $objElementoExtenderDualBand->getId()));

            foreach($arrayInterfacesElemento as $objInterfaceElemento)
            {
                $objInterfaceElemento->setEstado("Eliminado");
                $this->emInfraestructura->persist($objInterfaceElemento);
                $this->emInfraestructura->flush();
            }
            
            //se procede a realizar el ingreso del elemento Extender Dual Band y despacharlo en el NAF
            $arrayExtenderDualBandNaf   = $this->servicioGeneral->buscarElementoEnNaf(  $strSerieExtenderDualBand, 
                                                                                        $strModeloExtenderDualBand, 
                                                                                        "PI", 
                                                                                        "ActivarServicio");
            $strStatusExtenderDualBandNaf   = $arrayExtenderDualBandNaf[0]['status'];
            if($strStatusExtenderDualBandNaf !== "OK")
            {
                $boolMensajeUsuario = true;
                throw new \Exception("ERROR WIFI NAF: " . $arrayExtenderDualBandNaf[0]['mensaje']);
            }
            $strCodigoExtenderDualBandNaf   = "";
            $objInterfaceElementoExtenderDualBand = $this->servicioGeneral
                                                         ->ingresarElementoCliente($objServicio->getPuntoId()->getLogin(), 
                                                                                   $strSerieExtenderDualBand, 
                                                                                   $strModeloExtenderDualBand,
                                                                                   '-'.$objServicio->getId().'-ExtenderDualBand', 
                                                                                   null, 
                                                                                   $strUltimaMilla,
                                                                                   $objServicio, 
                                                                                   $strUsrCreacion, 
                                                                                   $strIpCreacion, 
                                                                                   $strEmpresaCod );
            if(!is_object($objInterfaceElementoExtenderDualBand))
            {
                $boolMensajeUsuario = true;
                throw new \Exception('Se presentaron errores al ingresar el elemento Extender Dual Band.');
            }
            $objInterfaceElementoExtenderDualBand->setEstado("connected");
            $this->emInfraestructura->persist($objInterfaceElementoExtenderDualBand);
            $this->emInfraestructura->flush();
            
            $objElementoNuevoExtenderDualBand = $objInterfaceElementoExtenderDualBand->getElementoId();

            if(!is_object($objElementoNuevoExtenderDualBand))
            {
                $boolMensajeUsuario = true;
                throw new \Exception('No se recuperó correctamente información del elemento Extender Dual Band');
            }
            //historial del servicio
            $objServicioHistorial = new InfoServicioHistorial();
            $objServicioHistorial->setServicioId($objServicio);
            $objServicioHistorial->setObservacion("Se registro el elemento con nombre: ".
                                                  $objElementoNuevoExtenderDualBand->getNombreElemento().
                                                  ", Serie: ".
                                                  $strSerieExtenderDualBand.
                                                  ", Modelo: ".
                                                  $strModeloExtenderDualBand.
                                                  ", Mac: ".
                                                  $strMacExtenderDualBand
                                                 );
            $objServicioHistorial->setEstado($objServicio->getEstado());
            $objServicioHistorial->setUsrCreacion($strUsrCreacion);
            $objServicioHistorial->setFeCreacion(new \DateTime('now'));
            $objServicioHistorial->setIpCreacion($strIpCreacion);
            $this->emComercial->persist($objServicioHistorial);
            $this->emComercial->flush();

            //actualizamos registro en el naf wifi
            $strMensajeError = str_repeat(' ', 1000);                                                                  
            $strSql          = "BEGIN AFK_PROCESOS.IN_P_PROCESA_INSTALACION(:codigoEmpresaNaf, ".
                               ":codigoArticulo, :tipoArticulo, :identificacionCliente, :serieCpe, ".
                               ":cantidad, :pv_mensajeerror); END;";
            $objStmt = $this->emNaf->getConnection()->prepare($strSql);
            $objStmt->bindParam('codigoEmpresaNaf',      $strEmpresaCod);
            $objStmt->bindParam('codigoArticulo',        $strCodigoExtenderDualBandNaf);
            $objStmt->bindParam('tipoArticulo',          $strTipoArticulo);
            $objStmt->bindParam('identificacionCliente', $strIdentificacionCliente);
            $objStmt->bindParam('serieCpe',              $strSerieExtenderDualBand);
            $objStmt->bindParam('cantidad',              intval(1));
            $objStmt->bindParam('pv_mensajeerror',       $strMensajeError);
            $objStmt->execute();
            
            if(strlen(trim($strMensajeError))>0)
            {
                $boolMensajeUsuario = true;
                throw new \Exception("ERROR WIFI NAF: ".$strMensajeError);
            }
            if($objServicio->getPlanId())
            {
                $strEsPlan = "SI";
            }
            else
            {
                $strEsPlan = "NO";
            }
            if($strEsPlan == "NO")
            {
                $objMacExtenderDualBand = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "MAC", 
                                                                                                    $objServicio->getProductoId());

                if(is_object($objMacExtenderDualBand))
                {
                    $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objMacExtenderDualBand, "Eliminado");
                }
                $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio, 
                                                                               $objServicio->getProductoId(), 
                                                                               "MAC", 
                                                                               $strMacExtenderDualBand, 
                                                                               $strUsrCreacion);
            }
                        
            //info_detalle_elemento gestion remota
            $this->servicioGeneral->ingresarDetalleElemento($objElementoNuevoExtenderDualBand,
                                                            "MAC",
                                                            "MAC",
                                                            $strMacExtenderDualBand,
                                                            $strUsrCreacion,
                                                            $strIpCreacion); 
            if($strEsPlan == "SI")
            {
                $arrayParams['intInterfaceElementoConectorId']  = $objServicioTecnico->getInterfaceElementoClienteId();
                $arrayParams['arrayData']                       = array();
                $arrayParams['strBanderaReturn']                = 'INTERFACE';
                $arrayParams['strTipoSmartWifi']                = 'ExtenderDualBand';
                $arrayParams['strRetornaPrimerWifi']            = 'SI';
                $objInterfaceElementoAnteriorExtenderDualBand   = $this->emInfraestructura
                                                                      ->getRepository('schemaBundle:InfoElemento')
                                                                      ->getElementosSmartWifiByInterface($arrayParams);
            }
            else
            {
                $objInterfaceElementoAnteriorExtenderDualBand   = $this->emInfraestructura
                                                                       ->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                       ->find($objServicioTecnico->getInterfaceElementoClienteId());
            }

            if($strEsPlan == "NO")
            {
                //guardar ont en servicio tecnico
                $objServicioTecnico->setElementoClienteId($objElementoNuevoExtenderDualBand->getId());
                $objServicioTecnico->setInterfaceElementoClienteId($objInterfaceElementoExtenderDualBand->getId());
                $this->emComercial->persist($objServicioTecnico);
                $this->emComercial->flush();
            }

            if(is_object($objInterfaceElementoAnteriorExtenderDualBand))
            {
                //elimino enlace
                $objEnlaceCliente   = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                           ->findOneBy(array("interfaceElementoFinId" => $objInterfaceElementoAnteriorExtenderDualBand->getId(),
                                                             "estado"                 => "Activo"));

                //se valida que exista un elemento WIFI relacionado al ONT registrado dentro de los recursos tecnicos del servicio
                if(is_object($objEnlaceCliente))
                {
                    //elimino enlace
                    $objEnlaceCliente->setEstado("Eliminado");
                    $this->emInfraestructura->persist($objEnlaceCliente);
                    $this->emInfraestructura->flush(); 

                    //crear nuevo enlace
                    $objEnlaceNuevo = new InfoEnlace();
                    $objEnlaceNuevo->setInterfaceElementoIniId($objEnlaceCliente->getInterfaceElementoIniId());
                    $objEnlaceNuevo->setInterfaceElementoFinId($objInterfaceElementoExtenderDualBand);
                    $objEnlaceNuevo->setTipoMedioId($objEnlaceCliente->getTipoMedioId());
                    $objEnlaceNuevo->setTipoEnlace("PRINCIPAL");
                    $objEnlaceNuevo->setEstado("Activo");
                    $objEnlaceNuevo->setUsrCreacion($strUsrCreacion);
                    $objEnlaceNuevo->setFeCreacion(new \DateTime('now'));
                    $objEnlaceNuevo->setIpCreacion($strIpCreacion);
                    $this->emInfraestructura->persist($objEnlaceNuevo);
                    $this->emInfraestructura->flush(); 

                    $objEnlaceClienteSiguiente = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                      ->findOneBy(array("interfaceElementoIniId" => 
                                                                        $objInterfaceElementoAnteriorExtenderDualBand->getId(),
                                                                        "estado"                 => "Activo"));

                    //se valida que exista un elemento WIFI relacionado al ONT registrado dentro de los recursos tecnicos del servicio
                    if(is_object($objEnlaceClienteSiguiente))
                    {
                        //elimino enlace
                        $objEnlaceClienteSiguiente->setEstado("Eliminado");
                        $this->emInfraestructura->persist($objEnlaceClienteSiguiente);
                        $this->emInfraestructura->flush(); 

                        //crear nuevo enlace
                        $objEnlaceNuevoSiguiente = new InfoEnlace();
                        $objEnlaceNuevoSiguiente->setInterfaceElementoIniId($objInterfaceElementoExtenderDualBand);
                        $objEnlaceNuevoSiguiente->setInterfaceElementoFinId($objEnlaceClienteSiguiente->getInterfaceElementoFinId());
                        $objEnlaceNuevoSiguiente->setTipoMedioId($objEnlaceClienteSiguiente->getTipoMedioId());
                        $objEnlaceNuevoSiguiente->setTipoEnlace("PRINCIPAL");
                        $objEnlaceNuevoSiguiente->setEstado("Activo");
                        $objEnlaceNuevoSiguiente->setUsrCreacion($strUsrCreacion);
                        $objEnlaceNuevoSiguiente->setFeCreacion(new \DateTime('now'));
                        $objEnlaceNuevoSiguiente->setIpCreacion($strIpCreacion);
                        $this->emInfraestructura->persist($objEnlaceNuevoSiguiente);
                        $this->emInfraestructura->flush(); 
                    }

                    //finalizar solicitud de cambio de modem inmediato y crear solicitud de retiro de equipo
                    $objTipoSolicitudCambio = $this->emComercial
                                                   ->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                   ->findOneBy(array("descripcionSolicitud" => "SOLICITUD CAMBIO EQUIPO", 
                                                                     "estado"               => "Activo"));

                    if(!is_object($objTipoSolicitudCambio))
                    {
                        $boolMensajeUsuario = true;
                        throw new \Exception('No se recuperó correctamente información de la solicitud de cambio de equipo');
                    }

                    $objSolicitudCambioExtenderDualBand = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                ->findOneBy(array("servicioId"      => $objServicio->getId(), 
                                                                                  "tipoSolicitudId" => $objTipoSolicitudCambio, 
                                                                                  "estado"          => "AsignadoTarea"));
                    if(is_object($objSolicitudCambioExtenderDualBand))
                    {
                        $strEstadoDetalleSol    = "AsignadoTarea";
                        $objSolicitudCambioExtenderDualBand->setEstado("Finalizada");
                        $this->emComercial->persist($objSolicitudCambioExtenderDualBand);
                        $this->emComercial->flush();
                    }
                    else
                    {
                        $strEstadoDetalleSol    = "Finalizada";
                        $objTipoSolicitudCambio = $this->emComercial
                                                       ->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                       ->findOneBy(array("descripcionSolicitud" => "SOLICITUD CAMBIO DE MODEM INMEDIATO", 
                                                                         "estado"               => "Activo"));
                        $objSolicitudCambioExtenderDualBand = $this->emComercial
                                                            ->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                            ->findOneBy(array("servicioId"      => $objServicio->getId(), 
                                                                              "tipoSolicitudId" => $objTipoSolicitudCambio, 
                                                                              "estado"          => "AsignadoTarea"));
                        if (is_object($objSolicitudCambioExtenderDualBand))
                        {
                            //eliminar las caracteristicas de la solicitud en estado AsignadoTarea
                            $arrayCaracteristicasSol = $this->emComercial
                                                            ->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                            ->findBy(array("detalleSolicitudId" => $objSolicitudCambioExtenderDualBand->getId(), 
                                                                           "estado"             => "AsignadoTarea"));
                            foreach($arrayCaracteristicasSol as $objCaracteristicaSolicitud)
                            {
                                $objCaracteristicaSolicitud->setEstado($strEstadoDetalleSol);
                                $objCaracteristicaSolicitud->setUsrCreacion($strUsrCreacion);
                                $objCaracteristicaSolicitud->setFeCreacion(new \DateTime('now'));
                                $this->emComercial->persist($objCaracteristicaSolicitud);
                                $this->emComercial->flush();
                            }

                            $arrayCaractSolicitud = $this->emComercial
                                                    ->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findBy(array("detalleSolicitudId" => $objSolicitudCambioExtenderDualBand->getId(), 
                                                                   "estado"             => "Activo"));
                            if(count($arrayCaractSolicitud) == 0)
                            {
                                $objSolicitudCambioExtenderDualBand->setEstado($strEstadoDetalleSol);
                                $this->emComercial->persist($objSolicitudCambioExtenderDualBand);
                                $this->emComercial->flush();
                            }
                        }
                    }

                    if (is_object($objSolicitudCambioExtenderDualBand) &&  $objSolicitudCambioExtenderDualBand->getEstado() == "Finalizada" )
                    {
                        //crear solicitud para retiro de equipo (cpe)
                        $objTipoSolicitud = $this->emComercial
                                                 ->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                 ->findOneBy(array("descripcionSolicitud" => "SOLICITUD RETIRO EQUIPO", 
                                                                   "estado"               => "Activo"));

                        $objDetalleSolicitud = new InfoDetalleSolicitud();
                        $objDetalleSolicitud->setServicioId($objServicio);
                        $objDetalleSolicitud->setTipoSolicitudId($objTipoSolicitud);
                        $objDetalleSolicitud->setEstado("AsignadoTarea");
                        $objDetalleSolicitud->setUsrCreacion($strUsrCreacion);
                        $objDetalleSolicitud->setFeCreacion(new \DateTime('now'));
                        $objDetalleSolicitud->setObservacion("SOLICITA RETIRO DE EQUIPO POR CAMBIO DE MODEM");
                        $this->emComercial->persist($objDetalleSolicitud);
                        $this->emComercial->flush();

                        //crear las caract para la solicitud de retiro de equipo
                        $objCaracteristica = $this->emComercial
                                                  ->getRepository('schemaBundle:AdmiCaracteristica')
                                                  ->findOneBy(array('descripcionCaracteristica'  => 'ELEMENTO CLIENTE',
                                                                    'estado'                     => 'Activo'));


                        $arrayCaractSolCambioElemento = $this
                                                        ->emComercial
                                                        ->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                        ->findBy(array("detalleSolicitudId" => $objSolicitudCambioExtenderDualBand->getId(),
                                                                       "caracteristicaId"   => $objCaracteristica,
                                                                       "estado"             => $strEstadoDetalleSol));

                        foreach($arrayCaractSolCambioElemento as $objCaracteristicaSolicitud)
                        {
                            $objDetSolCaract = new InfoDetalleSolCaract();
                            $objDetSolCaract->setCaracteristicaId($objCaracteristica);
                            $objDetSolCaract->setDetalleSolicitudId($objDetalleSolicitud);
                            $objDetSolCaract->setValor($objCaracteristicaSolicitud->getValor());
                            $objDetSolCaract->setEstado("AsignadoTarea");
                            $objDetSolCaract->setUsrCreacion($strUsrCreacion);
                            $objDetSolCaract->setFeCreacion(new \DateTime('now'));
                            $this->emComercial->persist($objDetSolCaract);
                            $this->emComercial->flush();
                        }

                        //buscar el info_detalle de la solicitud
                        $objDetalleCambioExtenderDualBand   = $this->emComercial
                                                                   ->getRepository('schemaBundle:InfoDetalle')
                                                                   ->findOneBy(array("detalleSolicitudId" => 
                                                                                     $objSolicitudCambioExtenderDualBand->getId()));

                        //obtener tarea
                        $objProceso  = $this->emSoporte
                                            ->getRepository('schemaBundle:AdmiProceso')
                                            ->findOneByNombreProceso("SOLICITAR RETIRO EQUIPO");

                        if(!is_object($objProceso))
                        {
                            $boolMensajeUsuario = true;
                            throw new \Exception('No se recuperó correctamente información de la solicitud de retiro de equipo');
                        }

                        $arrayTareas = $this->emSoporte
                                            ->getRepository('schemaBundle:AdmiTarea')
                                            ->findTareasActivasByProceso($objProceso->getId());
                        $objTarea    = $arrayTareas[0];

                        //grabar nuevo info_detalle para la solicitud de retiro de equipo
                        $objDetalle = new InfoDetalle();
                        $objDetalle->setDetalleSolicitudId($objDetalleSolicitud->getId());
                        $objDetalle->setTareaId($objTarea);
                        $objDetalle->setLongitud($objServicio->getPuntoId()->getLongitud());
                        $objDetalle->setLatitud($objServicio->getPuntoId()->getLatitud());
                        $objDetalle->setPesoPresupuestado(0);
                        $objDetalle->setValorPresupuestado(0);
                        $objDetalle->setIpCreacion($strIpCreacion);
                        $objDetalle->setFeCreacion(new \DateTime('now'));
                        $objDetalle->setUsrCreacion($strUsrCreacion);
                        $this->emSoporte->persist($objDetalle);
                        $this->emSoporte->flush();

                        //buscar la info_detalle_asignación de la solicitud
                        if(is_object($objDetalleCambioExtenderDualBand))
                        {
                            $objDetAsigCambioExtenderDualBand = $this->emComercial
                                                           ->getRepository('schemaBundle:InfoDetalleAsignacion')
                                                           ->findOneBy(array("detalleId" => $objDetalleCambioExtenderDualBand->getId()));

                            if (is_object($objDetAsigCambioExtenderDualBand))
                            {
                                //asignar los mismos responsables a la solicitud de retiro de equipo
                                $objDetalleAsignacion = new InfoDetalleAsignacion();
                                $objDetalleAsignacion->setDetalleId($objDetalle);
                                $objDetalleAsignacion->setAsignadoId($objDetAsigCambioExtenderDualBand->getAsignadoId());
                                $objDetalleAsignacion->setAsignadoNombre($objDetAsigCambioExtenderDualBand->getAsignadoNombre());
                                $objDetalleAsignacion->setRefAsignadoId($objDetAsigCambioExtenderDualBand->getRefAsignadoId());
                                $objDetalleAsignacion->setRefAsignadoNombre($objDetAsigCambioExtenderDualBand->getRefAsignadoNombre());
                                $objDetalleAsignacion->setPersonaEmpresaRolId($objDetAsigCambioExtenderDualBand->getPersonaEmpresaRolId());
                                $objDetalleAsignacion->setTipoAsignado("EMPLEADO");
                                $objDetalleAsignacion->setIpCreacion($strIpCreacion);
                                $objDetalleAsignacion->setFeCreacion(new \DateTime('now'));
                                $objDetalleAsignacion->setUsrCreacion($strUsrCreacion);
                                $this->emSoporte->persist($objDetalleAsignacion);
                                $this->emSoporte->flush();

                                $arrayParametrosAuditoria["intIdPersona"] = $objDetAsigCambioExtenderDualBand->getRefAsignadoId();
                            }
                        }

                        $this->serviceInfoElemento->ingresaAuditoriaElementos($arrayParametrosAuditoria);

                        //crear historial para la solicitud de retiro de equipo
                        $objHistorialSolicitud = new InfoDetalleSolHist();
                        $objHistorialSolicitud->setDetalleSolicitudId($objDetalleSolicitud);
                        $objHistorialSolicitud->setEstado("AsignadoTarea");
                        $objHistorialSolicitud->setObservacion("GENERACION AUTOMATICA DE SOLICITUD RETIRO DE ".
                                                               "EQUIPO POR CAMBIO DE MODEM");
                        $objHistorialSolicitud->setUsrCreacion($strUsrCreacion);
                        $objHistorialSolicitud->setFeCreacion(new \DateTime('now'));
                        $objHistorialSolicitud->setIpCreacion($strIpCreacion);
                        $this->emComercial->persist($objHistorialSolicitud);
                        $this->emComercial->flush();
                    }

                    //historial del servicio
                    $objServicioHistorial = new InfoServicioHistorial();
                    $objServicioHistorial->setServicioId($objServicio);
                    $objServicioHistorial->setObservacion("Se Realizo un Cambio de Elemento Cliente " . $strTipoElementoExtenderDualBand);
                    $objServicioHistorial->setEstado($objServicio->getEstado());
                    $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                    $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                    $objServicioHistorial->setIpCreacion($strIpCreacion);
                    $this->emComercial->persist($objServicioHistorial);
                    $this->emComercial->flush();
                }
            }
                    
            $strStatus  = "OK";
            $strMensaje = "OK";
             
        }
        catch(\Exception $e)
        {
            $strMensaje = $e->getMessage();
            if($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->rollback();
            }

            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }
            
            $this->serviceUtil->insertError('Telcos+', 
                                            'InfoCambioElementoService->cambioElementoExtenderDualBand', 
                                            $strMensaje,
                                            $strUsrCreacion, 
                                            $strIpCreacion
                                           );
            $strStatus  = "ERROR";
            if(!$boolMensajeUsuario)
            {
                $strMensaje = "Se presentaron errores al procesar el cambio de elemento, favor notificar a sistemas!";
            }
        }
        $arrayRespuestaFinal[] = array('status' => $strStatus, 'mensaje' => $strMensaje);
        return $arrayRespuestaFinal;
    }
    
    /**
     * cambiaElementoCpeSdwan
     * 
     * Service que realiza el cambio del elemento CPE al elemento del fortigate.
     *
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.0 21-01-2020
     * 
     * @author Jean Pierre Nazareno Martinez <jnazareno@telconet.ec>
     * @version 1.1 11-10-2021 - Se actualiza el estado de Cancelado por Cancel
     * 
     * @author Leonardo Mero <lemero@telconet.ec>
     * @version 1.2 09-12-2022 - Se llama al serviceInfoServicio de manera global
     *
     * @param Array $arrayParametros [ 
     *                                 idServicio             => Id del servicio del elemento actual a cambiar
     *                                 idServicioNue          => Id servicio del nuevo elemento
     *                                 idProducto             => Id del producto del servicio origen 
     *                                 strTipoOrden           => Bandera para saber si se libera el puerto del cpe
     *                                 login                  => Login del punto referenciado
     *                                 strCambioCpe           => Bandera para saber si se realiza el cambio o solo se actualiza el servicio
     *                                 objServicioNgFire      => Objeto del servicio a remplazar 
     *                                 idEmpresa              => Cadena de caracteres con identificador de empresa
     *                                 strServicio            => Identificador si es un servicio principal o backup
     *                                 usrCreacion            => Cadena de caracteres con usuario de creacion a utilizar
     *                                 ipCreacion             => Cadena de caracteres con ip de creacion a utilizar
     *                                 prefijoEmpresa         => Cadena de caracteres con prefigo de empresa a utilizar
     *                               ]
     * @return Array $arrayRespuestaFinal
     * 
     */
    public function cambiaElementoCpeSdwan($arrayParametros)
    {
        $this->emSoporte->getConnection()->beginTransaction();
        $this->emComercial->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();
        $strMensaje         = 'OK';
        $strEstadoCancel    = 'Cancel';
        $boolEsFibraRuta    = false;
        
        try
        {
            $objServicio = $this->emComercial->getRepository('schemaBundle:infoServicio')->find($arrayParametros['idServicio']);
            if(!is_object($objServicio) || empty($objServicio))
            {
                throw new \Exception('No se encontró el servicio referenciado Sdwan.');
            }
            
            $objSerTec   = $this->emComercial->getRepository('schemaBundle:infoServicioTecnico')
                                                                   ->findOneBy(array(
                                                                   'servicioId' => $arrayParametros['idServicio']));
            if(!is_object($objSerTec) || empty($objSerTec))
            {
                throw new \Exception('No se encontró el servicio tecnico referenciado Sdwan.');
            }
            $objSerTecNuevo = $this->emComercial->getRepository('schemaBundle:infoServicioTecnico')
                                                                   ->findOneBy(array(
                                                                   'servicioId' => $arrayParametros['idServicioNue']));

            if(!is_object($objSerTecNuevo) || empty($objSerTecNuevo))
            {
                throw new \Exception('No se encontró el servicio tecnico Sdwan a migrar.');
            }
            
            if($arrayParametros['strCambioCpe']=="S")
            {

                $objElementoCpeCambiar = $this->cancelarService->getElementoCpeServicioTn($objSerTec);
                if(!is_object($objElementoCpeCambiar) || empty($objElementoCpeCambiar))
                {
                    throw new \Exception('No se encuentra el cpe a migrar del producto Sdwan');
                }

                $objUltimaMilla        = $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')
                                                                      ->find($objSerTec->getUltimaMillaId());
                if(!is_object($objUltimaMilla) || empty($objUltimaMilla))
                {
                    throw new \Exception('No se encuentra el tipo de última milla del elemento.');
                }
                //Obtener la caracteristica TIPO_FACTIBILIDAD para discriminar que sea FIBRA DIRECTA o RUTA
                $objServProdCaractTipoFact = $this->servicioGeneral
                                                  ->getServicioProductoCaracteristica($objServicio,'TIPO_FACTIBILIDAD',$objServicio->getProductoId());

                if(is_object($objServProdCaractTipoFact))
                {
                    if($objServProdCaractTipoFact->getValor() == "RUTA")
                    {
                        $boolEsFibraRuta = true;
                    }
                }
                else
                {
                    if($strUltimaMilla == "Fibra Optica" && $objSerTec->getInterfaceElementoConectorId())
                    {
                        //Si contiene informacion de GIS y no tiene caracteristica
                        $boolEsFibraRuta = true;
                    }
                }

                //Se obtiene la información del objeto interface mas próximo enlazado con el cpe a ser cambiado
                $arrayParametrosPuertoOutVecino['objServicioTecnico'] = $objSerTec;
                $arrayParametrosPuertoOutVecino['objUltimaMilla']     = $objUltimaMilla;
                $arrayParametrosPuertoOutVecino['boolEsFibraRuta']    = $boolEsFibraRuta;
                $arrayInformacionPuertoOutVecino                      = $this->getArrayInformacionPuertoOutVecinoCpe
                                                                               ($arrayParametrosPuertoOutVecino); 

                if($arrayInformacionPuertoOutVecino['status'] == 'ERROR')
                    {
                        $arrayRespuestaFinal[] = array('status'  => 'ERROR',
                                                    'mensaje' => $arrayInformacionPuertoOutVecino['mensaje']);
                        return $arrayRespuestaFinal;
                    }

                    $objInterfaceTransciverOUT = $arrayInformacionPuertoOutVecino['objInterfaceOutVecinoCpe'];

                //==========================================================
                //      HACEMOS EL CAMBIO DEL CPE CON EL TRX
                //==========================================================        
                $objEnlaceElemento = $this->emInfraestructura->getRepository('schemaBundle:infoEnlace')
                                                               ->findOneBy(array(
                                                                "interfaceElementoIniId" => $objInterfaceTransciverOUT->getId()));
                if(!is_object($objEnlaceElemento) || empty($objEnlaceElemento))
                {
                    throw new \Exception('No se encuentra el enlace del elemento a Migrar.');
                }
                $objInterfaceElementoNuevo = $this->emInfraestructura->getRepository('schemaBundle:infoInterfaceElemento')
                                                               ->findOneBy(array(
                                                                "elementoId" => $objSerTecNuevo->getElementoClienteId(),
                                                                "nombreInterfaceElemento" => "wan2",
                                                                "estado" => "connected"));

                if(is_object($objInterfaceElementoNuevo) && !empty($objInterfaceElementoNuevo))
                {
                    $objEnlaceElemento->setInterfaceElementoFinId($objInterfaceElementoNuevo);
                    $this->emInfraestructura->persist($objEnlaceElemento);
                    $this->emInfraestructura->flush(); 
                }
                else
                {
                     throw new \Exception('No se encontró puerto disponible en el elemento, Migración Sdwan.');
                }
                //LIBERAMOS EL PUERTO QUE TENIA ASIGNADO EL SECURITY NG FIREWALL
                if(!empty($arrayParametros['strServicio']) && $arrayParametros['strServicio']=='Firewall')
                {
                    $objInterfaceElementoNGF = $this->emInfraestructura->getRepository('schemaBundle:infoInterfaceElemento')
                                                                ->findOneBy(array(
                                                                 "elementoId" => $objSerTecNuevo->getElementoClienteId(),
                                                                 "nombreInterfaceElemento" => "wan1",
                                                                 "estado" => "connected"));
                    if(is_object($objInterfaceElementoNGF) && !empty($objInterfaceElementoNGF))
                    {
                        //SOLO DESACTIVAR CUANDO TENGA UN SERVICIO NG FIREWALL ACTIVO Y BORRAMOS LA MAC
                        $objInterfaceElementoNGF->setEstado('not connect');
                        $objInterfaceElementoNGF->setMacInterfaceElemento('');
                        $this->emInfraestructura->persist($objInterfaceElementoNGF);
                        $this->emInfraestructura->flush(); 
                    }
                    else
                    {
                         throw new \Exception('No se encuentra el elemento asignado al producto Security Ng Firewall.');
                    }
                }
                
                //==========================================================
                //      VALIDAMOS Y GENERAMOS RETIRO DE EQUIPO
                //==========================================================
                if(is_object($objElementoCpeCambiar) && !empty($objElementoCpeCambiar))
                {
                    $arrayParametrosCpe = array('objServicio'      => $objServicio,
                                                'objElementoCpe'   => $objElementoCpeCambiar,
                                                'objServicioSdwan' => $arrayParametros['idServicioNue']);
                    $booleanCpe         = $this->cancelarService->validarCpePorServicio($arrayParametrosCpe);
                }
                
                if($booleanCpe)
                {
                    //SACAMOS LOS DATOS DEL SERVICIO NUEVO PARA REUTILIZAR LA CUADRILLA EN EL RETIRO
                    $objServicioSdwan = $this->emComercial->getRepository('schemaBundle:infoServicio')->find($arrayParametros['idServicioNue']);
                    if(!is_object($objServicioSdwan) || empty($objServicioSdwan))
                    {
                        throw new \Exception('No se encuentra el servicio Sdwan instalado.');
                    }
                    $objTipoSolicitudP = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                             ->findOneBy(array("descripcionSolicitud" => "SOLICITUD PLANIFICACION", 
                                                                "estado"               => "Activo"));
                    if(!is_object($objTipoSolicitudP) || empty($objTipoSolicitudP))
                    {
                        throw new \Exception('No se encuentra el tipo de solicitud de Planificación del Servicio Sdwan.');
                    }
                    $objDetalleSolicitudSdwan = $this->emComercial->getRepository('schemaBundle:infoDetalleSolicitud')
                                                     ->findOneBy(array("servicioId" => $objServicioSdwan->getId(),
                                                               "tipoSolicitudId" =>  $objTipoSolicitudP->getId()));
                    if(!is_object($objTipoSolicitudP) || empty($objTipoSolicitudP))
                    {
                        throw new \Exception('No se encuentra el detalle de la solicitud de Planificación del Servicio Sdwan.');
                    }
                    $objInfoDetalleSdwan = $this->emComercial->getRepository('schemaBundle:InfoDetalle')
                                                ->findOneBy(array("detalleSolicitudId" => $objDetalleSolicitudSdwan->getId()));
                    if(!is_object($objInfoDetalleSdwan) || empty($objInfoDetalleSdwan))
                    {
                        throw new \Exception('No se encuentra el detalle del Servicio Sdwan.');
                    }

                    $objInfoDetalleAsigSdwan = $this->emSoporte->getRepository('schemaBundle:InfoDetalleAsignacion')
                                                    ->findOneBy(array("detalleId" => $objInfoDetalleSdwan->getId()));

                    if(!is_object($objInfoDetalleAsigSdwan) || empty($objInfoDetalleAsigSdwan))
                    {
                        throw new \Exception('No se encuentra el detalle de la Asignación del Servicio Sdwan.');
                    }
                    $objDetalleColaborador = $this->emSoporte->getRepository('schemaBundle:InfoDetalleColaborador')
                                                  ->findOneBy(array("detalleAsignacionId" => $objInfoDetalleAsigSdwan->getId(),
                                                                "refAsignadoId" => $objInfoDetalleAsigSdwan->getRefAsignadoId()));

                    if(!is_object($objDetalleColaborador) || empty($objDetalleColaborador))
                    {
                        throw new \Exception('No se encuentra el detalle del colaborador Asignado a la activación del Servicio Sdwan.');
                    }
                    $objPersonaEmpresaRolUsr = $this->emComercial->getRepository('schemaBundle:infoPersonaEmpresaRol')
                                                    ->findOneBy(array("personaId" => $objDetalleColaborador->getRefAsignadoId(),
                                                                      "departamentoId" =>  $objDetalleColaborador->getAsignadoId(),
                                                                      "estado"  => "Activo"));

                    if(!is_object($objPersonaEmpresaRolUsr) || empty($objPersonaEmpresaRolUsr))
                    {
                        throw new \Exception('No se encuentra el detalle de la persona asignada a la activación del Servicio Sdwan.');
                    }
                    //RETIRO DE EL EQUIPO//
                    $arrayParams = array();
                    $arrayParams['objServicio']                 = $objServicio;
                    $arrayParams['objElementoCliente']          = $objElementoCpeCambiar;
                    $arrayParams['idPersonaEmpresaRol']         = $objPersonaEmpresaRolUsr->getId();
                    $arrayParams['strMotivoSolicitud']          = 'CAMBIO DE TECNOLOGIA';
                    $arrayParams['usrCreacion']                 = $arrayParametros['usrCreacion'];
                    $arrayParams['ipCreacion']                  = $arrayParametros['ipCreacion'];
                    $arrayParams['strPersonaEmpresaRolId']      = $objInfoDetalleAsigSdwan->getPersonaEmpresaRolId();
                    if(is_object($objInfoDetalleAsigSdwan) && !empty($objInfoDetalleAsigSdwan) && 
                        $objInfoDetalleAsigSdwan->getTipoAsignado()=='CUADRILLA')
                    {
                        $arrayParams['intIdCuadrilla']          = $objInfoDetalleAsigSdwan->getAsignadoId();
                    }
                    $arrayParams['strTipoAsignado']             = $objInfoDetalleAsigSdwan->getTipoAsignado();
                    $arrayParams['intIdPunto']                  = $objServicio->getPuntoId()->getId();
                    $arrayParams['strLogin']                    = $arrayParametros['login'];
                    $arrayParams['strBandResponsableCambioEquipo']          = "S";

                    $this->servicioGeneral->generarSolicitudRetiroEquipo($arrayParams);

                    $arrayRespuestaFinal[] = array('status' => "OK", 'mensaje' => "OK", 'statusCode' => 200);
                }
                else
                {
                    $arrayRespuestaFinal[] = array('status'  => 'OK',
                                                   'mensaje' => 'El CPE tiene mas servicios Asociados, favor validar con Sistemas.',
                                                   'statusCode' => 200);
                }
            }
            
            $objSerTecNuevo->setInterfaceElementoClienteId($objSerTec->getInterfaceElementoClienteId());
            $this->emComercial->persist($objSerTecNuevo);
            $this->emComercial->flush();
            
            $objInfoServicioHistorial = new InfoServicioHistorial();
            $objInfoServicioHistorial->setServicioId($objServicio);
            $objInfoServicioHistorial->setUsrCreacion($arrayParametros['usrCreacion']);
            $objInfoServicioHistorial->setFeCreacion(new \DateTime('now'));
            $objInfoServicioHistorial->setIpCreacion($arrayParametros['ipCreacion']);
            $objInfoServicioHistorial->setEstado($strEstadoCancel);
            $objInfoServicioHistorial->setObservacion('Se cancela por motivo de migración a Tecnología a Sdwan.');
            $this->emComercial->persist($objInfoServicioHistorial);
            $this->emComercial->flush();

            $objServicio->setEstado($strEstadoCancel);
            $this->emComercial->persist($objServicio);
            $this->emComercial->flush();

            if(!empty($arrayParametros['objServicioNgFire']))
            {
                $objServicioNgFire  =   $arrayParametros['objServicioNgFire'];
                //Buscamos el producto de Licenciamiento
                $objProductoLic = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->findOneBy(array(
                                                                                            'descripcionProducto'  => 'SECURITY SECURE SDWAN',
                                                                                            'estado'               => 'Activo',
                                                                                            'empresaCod'           => $arrayParametros['idEmpresa']));
                if(!is_object($objProductoLic) || empty($objProductoLic))
                {
                    throw new \Exception('No se encontró el Producto de Licenciamiento Sdwan.');
                }
                $objServComision = $this->emComercial->getRepository('schemaBundle:InfoServicioComision')
                                                                    ->findOneBy(array(
                                                                                      'servicioId' => $objServicioNgFire->getId(),
                                                                                       'estado' => 'Activo'));
                $objServFireHistori = $this->emComercial->getRepository('schemaBundle:InfoServicioHistorial')
                                                        ->findOneBy(array('servicioId' => $objServicioNgFire->getId(),
                                                                          'estado'     => 'Pendiente'));

                //OBTENEMOS LAS CARACTERISTICAS DEL SECURITY NG FIREWALL
                //PLAN 

                $strServicioProdCaractPl = $this->servicioGeneral->getValorCaracteristicaServicio($objServicioNgFire,
                                                                                                  "SEC PLAN NG FIREWALL",
                                                                                                  "Activo");
                if(!empty($strServicioProdCaractPl))
                {
                    $arrayParametrosDet =   $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->getOne("LICENCIAS_SDWAN", 
                                                                         "COMERCIAL", 
                                                                         "", 
                                                                         "", 
                                                                         $strServicioProdCaractPl, 
                                                                         "", 
                                                                         "",
                                                                         "",
                                                                         "",
                                                                         $arrayParametros['idEmpresa']
                                                                       );
                    
                    if($arrayParametrosDet['valor2']=='SI')
                    {
                        $strPlanFire = 'PLUS';
                    }
                    else
                    {
                        $strPlanFire = $strServicioProdCaractPl;
                    }
                }
                else
                {
                    $strMensaje  = "No se encuentra un Plan asociado al Servicio Security Ng Firewall, Favor Notificar a Sistema";
                    $strPlanFire = 'Sin Plan';

                }

                $strServicioProdCaract = $this->servicioGeneral->getValorCaracteristicaServicio($objServicioNgFire,
                                                                                                    "SEC MODELO FIREWALL",
                                                                                                    "Activo");
                if(!empty($strServicioProdCaract))
                {
                    $strModeloFire = $strServicioProdCaract;
                }
                else
                {
                    $strMensaje    = "No se encuentra registrado un Modelo en el Servicio Security Ng Firewall, Favor Notificar a Sistema";
                    $strModeloFire = 'Sin Equipo';
                }

                if($strPlanFire == 'PLUS')
                {
                    $strDescripcionPresentaFactura = $objProductoLic->getDescripcionProducto().' '.$strPlanFire.' '.$strModeloFire;
                    $arrayCrearServicio         = $arrayParams = array();
                    $arrayCrearServicio['strDescripcionPresentaFactura'] = $strDescripcionPresentaFactura;
                    $arrayCrearServicio['objServicioNgFire']             = $objServicioNgFire;
                    $arrayCrearServicio['objProductoLic']                = $objProductoLic;
                    $arrayCrearServicio['usrCreacion']                   = $arrayParametros['usrCreacion'];
                    $arrayCrearServicio['strPlanFire']                   = $strPlanFire;
                    $arrayCrearServicio['strModeloFire']                 = $strModeloFire;
                    $arrayCrearServicio['objServComision']               = $objServComision;
                    $arrayCrearServicio['objServFireHistori']            = $objServFireHistori;
                    $arrayCrearServicio['ipCreacion']                    = $arrayParametros['ipCreacion'];

                    $arrayRespuestaLicencia = $this->serviceInfoServicio->crearLicenciaSdwan($arrayCrearServicio);
                    $strMensaje = $arrayRespuestaLicencia['strStatus'];
                }
                
                $objInfoServicioHistorial = new InfoServicioHistorial();
                $objInfoServicioHistorial->setServicioId($objServicioNgFire);
                $objInfoServicioHistorial->setUsrCreacion($arrayParametros['usrCreacion']);
                $objInfoServicioHistorial->setFeCreacion(new \DateTime('now'));
                $objInfoServicioHistorial->setIpCreacion($arrayParametros['ipCreacion']);
                $objInfoServicioHistorial->setEstado($strEstadoCancel);
                $objInfoServicioHistorial->setObservacion('Se cambia de Tecnologia a Sdwan.');
                $this->emComercial->persist($objInfoServicioHistorial);
                $this->emComercial->flush();

                $objServicioNgFire->setEstado($strEstadoCancel);
                $this->emComercial->persist($objServicioNgFire);
                $this->emComercial->flush();
            }    
            $arrayRespuestaFinal[] = array('status' => "OK", 'mensaje' => $strMensaje, 'statusCode' => 200);
        }
        catch(\Exception $e)
        {
            $strMensaje = $e->getMessage();
            if($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->rollback();
            }
            if($this->emSoporte->getConnection()->isTransactionActive())
            {
                $this->emSoporte->getConnection()->rollback();
            }
            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }
            
            $this->serviceUtil->insertError('Telcos+', 
                                            'InfoCambioElementoService->cambiaElementoCpeSdwan', 
                                            $strMensaje,
                                            $arrayParametros['usrCreacion'], 
                                            $arrayParametros['ipCreacion']
                                           );
                                
            $arrayRespuestaFinal[] = array('status' => "Error", 'mensaje' => $strMensaje, 'statusCode' => 500);
            return $arrayRespuestaFinal;
        }
        //*DECLARACION DE COMMITS*/
        if ($this->emInfraestructura->getConnection()->isTransactionActive())
        {
            $this->emInfraestructura->getConnection()->commit();
        }

        if ($this->emComercial->getConnection()->isTransactionActive())
        {
            $this->emComercial->getConnection()->commit();
        }
        
        if ($this->emSoporte->getConnection()->isTransactionActive())
        {
            $this->emSoporte->getConnection()->commit();
        }
        
        $this->emInfraestructura->getConnection()->close();
        $this->emComercial->getConnection()->close();
        $this->emSoporte->getConnection()->close();
        return $arrayRespuestaFinal;
    }

    /**
     * Función que realiza el cambio de equipo para los servicios TN bajo la red GPON
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 25-10-2021
     * 
     * @author Leonardo Mero  <lemero@telconet.ec>
     * @version 1.0 02-08-2022  Ingresar registros de ultima modificacion en InfoInterface
     * 
     * @author Jenniffer Mujica  <jmujicfa@telconet.ec>
     * @version 1.0 07-02-2023  Se agrega validación para ont zte por marca y modelo.
     *
     * @param Array $arrayParametros
     *
     * @return Array $arrayResponseTn [][
     *                                    'status'    => estado de respuesta de la operación 'OK' o 'ERROR',
     *                                    'mensaje'   => mensaje de la operación o de error
     *                                  ]
     */
    public function cambioElementoTnGpon($arrayParametros)
    {
        $strCodEmpresa     = $arrayParametros['idEmpresa'];
        $strPrefijoEmpresa = $arrayParametros['prefijoEmpresa'];
        $strUsrCreacion    = $arrayParametros['usrCreacion'];
        $strIpCreacion     = $arrayParametros['ipCreacion'];
        $strMacActual      = $arrayParametros['macElementoNuevo'];
        $strTipoElemento   = "";

        try
        {
            //se realiza la carga y descarga de los Activos.
            $arrayResCarDes = $this->serviceInfoElemento->cargaDescargaActivosCambioEquipo($arrayParametros);
            if (!$arrayResCarDes['status'])
            {
                throw new \Exception($arrayResCarDes['message']);
            }
            //obtengo el servivio
            $objServicio = $arrayParametros['objServicio'];
            if(!is_object($objServicio))
            {
                throw new \Exception("No se encontró el servicio, por favor notificar a Sistemas.");
            }
            $objPunto    = $objServicio->getPuntoId();
            //obtengo el servicio tecnico
            $objServicioTecnico = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                    ->findOneByServicioId($objServicio->getId());
            //obtengo interface actual
            $objInterfaceElementoActual = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                ->find($objServicioTecnico->getInterfaceElementoClienteId());
            //obtengo el producto
            $objProducto = $objServicio->getProductoId();
            if(!is_object($objProducto))
            {
                throw new \Exception("No se encontró el producto del servicio, por favor notificar a Sistemas.");
            }
            //obtengo el detalle de la solicitud
            $objTipoSolicitud    = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                ->findOneBy(array("descripcionSolicitud" => "SOLICITUD CAMBIO DE MODEM INMEDIATO",
                                                                  "estado"               => "Activo"));
            $objDetalleSolicitud = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                        ->findOneBy(array("servicioId"      => $objServicio->getId(),
                                                          "tipoSolicitudId" => $objTipoSolicitud,
                                                           "estado"         => "AsignadoTarea"));
            if(!is_object($objDetalleSolicitud))
            {
                throw new \Exception("No se encontró el detalle de la solicitud, por favor notificar a Sistemas.");
            }
            //obtener elemento actual
            $objElementoClienteActual = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                ->findOneById($arrayParametros['idElementoActual']);
            if(!is_object($objElementoClienteActual))
            {
                throw new \Exception("No se encontró el elemento actual del cliente, por favor notificar a Sistemas.");
            }
            //registrar elemento en el naf y telcos
            $arrayParametros["strOrigen"] = "cambioEquipo";
            $arrayRequestElemento         = $this->registrarElementoNuevoNAF($arrayParametros);
            if(!isset($arrayRequestElemento) || $arrayRequestElemento['status'] != 'OK' || !is_object($arrayRequestElemento['objElementoCpe']))
            {
                throw new \Exception("No pudo ser crear el nuevo elemento del cliente, por favor notificar a Sistemas.");
            }
            //obtener elemento nuevo
            $objElementoCpeNuevo = $arrayRequestElemento['objElementoCpe'];

            //cambio equipo por servicios
            $arrayParametrosCambio = array(
                "objElementoClienteActual" => $objElementoClienteActual,
                "objElementoCpeNuevo"      => $objElementoCpeNuevo,
                "objServicio"              => $objServicio,
                "strMacActual"             => $strMacActual,
                "strCodEmpresa"            => $strCodEmpresa,
                "strPrefijoEmpresa"        => $strPrefijoEmpresa,
                "strUsrCreacion"           => $strUsrCreacion,
                "strIpCreacion"            => $strIpCreacion
            );
            if($objProducto->getNombreTecnico() === "DATOS SAFECITY")
            {
                //validar tipo elemento cpe ont
                $arrayValidModeloElementoOnt = $this->emInfraestructura->getRepository("schemaBundle:AdmiModeloElemento")
                                                        ->createQueryBuilder('s')
                                                        ->join("s.tipoElementoId", "t")
                                                        ->where("t.nombreTipoElemento      = :nombreTipoElemento")
                                                        ->andWhere("s.nombreModeloElemento = :nombreModeloElemento")
                                                        ->andWhere("s.estado               = :estado")
                                                        ->setParameter('nombreTipoElemento',   "CPE ONT")
                                                        ->setParameter('nombreModeloElemento', $arrayParametros['modeloElementoNuevo'])
                                                        ->setParameter('estado',               "Activo")
                                                        ->orderBy('s.id', 'ASC')
                                                        ->getQuery()
                                                        ->getResult();
                if(!isset($arrayValidModeloElementoOnt) || empty($arrayValidModeloElementoOnt) || !is_array($arrayValidModeloElementoOnt))
                {
                    throw new \Exception("ERROR ONT: El elemento ingresado no es de tipo CPE ONT, favor verificar.");
                }
                $strTipoElemento = "ONT";

                //*********VALIDACION MODELO ONT ZTE */
                //parametro modelo ont zte
                $arrayModelosPermitidos = array();

                $arrayParametroModelos = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->get('NUEVA_RED_GPON_TN',
                                                                'COMERCIAL',
                                                                '',
                                                                '',
                                                                'MODELO_ONT_ZTE',
                                                                '',
                                                                '',
                                                                '',
                                                                '',
                                                                $strCodEmpresa);

                if(empty($arrayParametroModelos) || !is_array($arrayParametroModelos))
                {
                    throw new \Exception("No se ha podido obtener el modelo permitido del Ont Zte, por favor notificar a Sistemas.");
                }

                foreach($arrayParametroModelos as $arrayModelos)
                {
                    $arrayModelosPermitidos[] = $arrayModelos['valor2'];
                }

                //se valida marca y modelo de ont
                $strMarcaOntAct = $objElementoClienteActual->getModeloElementoId()->getMarcaElementoId()->getNombreMarcaElemento();

                $strMarcaOntNuevo = $objElementoCpeNuevo->getModeloElementoId()->getMarcaElementoId()->getNombreMarcaElemento();

                $strModeloOnt = $objElementoCpeNuevo->getModeloElementoId()->getNombreModeloElemento();
                
                if($strMarcaOntAct === 'ZTE' && (!in_array($strModeloOnt, $arrayModelosPermitidos)))
                {
                    //validar modelo zte
                    throw new \Exception("ERROR ONT: El elemento ingresado no corresponde al modelo permitido, favor verificar.");

                }

                if($strMarcaOntAct === 'HUAWEI' && $strMarcaOntNuevo != $strMarcaOntAct)
                {
                    throw new \Exception("ERROR ONT: El elemento ingresado no corresponde a la misma tecnologia, favor verificar.");
                }
                //******************************* */

                $arrayResponseTn = $this->cambioElementoOntGponTN($arrayParametrosCambio);
                //se verifica la respuesta
                if($arrayResponseTn[0]['status'] != "OK")
                {
                    throw new \Exception($arrayResponseTn[0]['mensaje']);
                }
            }
            else if($objProducto->getNombreTecnico() === "SAFECITYSWPOE")
            {
                $strTipoElemento = "Switch PoE";
                //validar modelo elemento
                $arrayParametrosModelosSwPoe = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                                ->getOne('PARAMETROS PROYECTO GPON SAFECITY',
                                                                                        'INFRAESTRUCTURA',
                                                                                        '',
                                                                                        'MODELOS_SWITCH_POE',
                                                                                        $arrayParametros['modeloElementoNuevo'],
                                                                                        '',
                                                                                        '',
                                                                                        '',
                                                                                        '',
                                                                                        $strCodEmpresa);
                if(!isset($arrayParametrosModelosSwPoe) || empty($arrayParametrosModelosSwPoe))
                {
                    throw new \Exception("ERROR SW POE: El elemento ingresado no es un modelo de SW POE permitido, favor verificar.");
                }
                //se obtienen las interfaces
                $arrayInterfacesElementoActual = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                        ->findBy(array("elementoId" => $objElementoClienteActual->getId(),
                                                                       "estado"     => "connected"));
                //se recorre las interfaces
                foreach($arrayInterfacesElementoActual as $objInterface)
                {
                    //objeto de la interface nuevo
                    $objInterfaceNuevo  = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                    ->findOneBy(array("elementoId"              => $objElementoCpeNuevo->getId(),
                                                                      "nombreInterfaceElemento" => $objInterface->getNombreInterfaceElemento()));
                    if(!is_object($objInterfaceNuevo))
                    {
                        throw new \Exception("No se pudo obtener las interfaces del elemento nuevo no tienen las mismas ".
                                             "interfaces con el elemento anterior, por favor notificar a Sistemas.");
                    }
                    //recorrer enlaces anteriores
                    $arrayEnlaceAnterior = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                        ->findBy(array('interfaceElementoIniId' => $objInterface->getId(),
                                                                       'estado'                 => 'Activo'));
                    foreach($arrayEnlaceAnterior as $objEnlaceAnterior)
                    {
                        //guardar nuevo enlace
                        $objEnlaceNuevo = new InfoEnlace();
                        $objEnlaceNuevo->setInterfaceElementoIniId($objInterfaceNuevo);
                        $objEnlaceNuevo->setInterfaceElementoFinId($objEnlaceAnterior->getInterfaceElementoFinId());
                        $objEnlaceNuevo->setTipoMedioId($objEnlaceAnterior->getTipoMedioId());
                        $objEnlaceNuevo->setTipoEnlace($objEnlaceAnterior->getTipoEnlace());
                        $objEnlaceNuevo->setEstado("Activo");
                        $objEnlaceNuevo->setUsrCreacion($strUsrCreacion);
                        $objEnlaceNuevo->setFeCreacion(new \DateTime('now'));
                        $objEnlaceNuevo->setIpCreacion($strIpCreacion);
                        $this->emInfraestructura->persist($objEnlaceNuevo);
                        $this->emInfraestructura->flush();
                        //eliminar enlace anterior
                        $objEnlaceAnterior->setEstado('Eliminado');
                        $this->emInfraestructura->persist($objEnlaceAnterior);
                        $this->emInfraestructura->flush();
                    }
                    //seteo estado de la interface nueva
                    $objInterfaceNuevo->setEstado('connected');
                    $this->emInfraestructura->persist($objInterfaceNuevo);
                    $this->emInfraestructura->flush();
                }
            }
            else if($objProducto->getNombreTecnico() === "SAFECITYDATOS")
            {
                $strTipoElemento = "Cámara";
                //verificar tipo elemento
                $objTipoElementoNuevo = $objElementoCpeNuevo->getModeloElementoId()->getTipoElementoId();
                $arrayVerificarTipoElemento = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->get("PARAMETROS PROYECTO GPON SAFECITY",
                                                                        'INFRAESTRUCTURA',
                                                                        'PARAMETROS',
                                                                        'MAPEO TIPOS ELEMENTOS CAMARA',
                                                                        $objTipoElementoNuevo->getNombreTipoElemento(),
                                                                        "",
                                                                        "",
                                                                        "",
                                                                        "",
                                                                        $strCodEmpresa);
                if(empty($arrayVerificarTipoElemento))
                {
                    throw new \Exception("ERROR CAMARA: El tipo elemento ingresado no es permitido, favor verificar.");
                }
                //validar PTZ
                $arrayParametrosModelosPtz = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->get('PARAMETROS PROYECTO GPON SAFECITY',
                                                        'INFRAESTRUCTURA',
                                                        '',
                                                        'MODELOS_CAMARAS_PTZ',
                                                        $objElementoClienteActual->getModeloElementoId()->getNombreModeloElemento(),
                                                        '',
                                                        '',
                                                        '',
                                                        '',
                                                        $strCodEmpresa);
                if(isset($arrayParametrosModelosPtz) && !empty($arrayParametrosModelosPtz) && count($arrayParametrosModelosPtz))
                {
                    $arrayParNuevoModeloPtz = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                        ->get('PARAMETROS PROYECTO GPON SAFECITY',
                                                                            'INFRAESTRUCTURA',
                                                                            '',
                                                                            'MODELOS_CAMARAS_PTZ',
                                                                            $arrayParametros['modeloElementoNuevo'],
                                                                            '',
                                                                            '',
                                                                            '',
                                                                            '',
                                                                            $strCodEmpresa);
                    if(!isset($arrayParNuevoModeloPtz) || empty($arrayParNuevoModeloPtz))
                    {
                        throw new \Exception("ERROR CAMARA: Se debe realizar el cambio de equipo de la cámara con modelos PTZ.");
                    }
                }
            }
            else if($objProducto->getNombreTecnico() === "SAFECITYWIFI")
            {
                $strTipoElemento = "AP";
                //verificar tipo elemento
                $objTipoElementoNuevo = $objElementoCpeNuevo->getModeloElementoId()->getTipoElementoId();
                $arrayVerificarTipoElemento = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->get("PARAMETROS PROYECTO GPON SAFECITY",
                                                                        'INFRAESTRUCTURA',
                                                                        'PARAMETROS',
                                                                        'MAPEO TIPOS ELEMENTOS AP',
                                                                        $objTipoElementoNuevo->getNombreTipoElemento(),
                                                                        "",
                                                                        "",
                                                                        "",
                                                                        "",
                                                                        $strCodEmpresa);
                if(empty($arrayVerificarTipoElemento))
                {
                    throw new \Exception("ERROR AP: El tipo elemento ingresado no es permitido, favor verificar.");
                }
            }
            else
            {
                throw new \Exception("El producto del servicio '".$objProducto->getNombreTecnico()."' para la red GPON_MPLS no está soportado.");
            }

            //finalizar solicitud
            $arrayParams['objServicio']        = $objServicio;
            $arrayParams['strTipoSolicitud']   = "SOLICITUD CAMBIO DE MODEM INMEDIATO";
            $arrayParams['strEstadoSolicitud'] = "AsignadoTarea";
            $arrayParams['strUsrCreacion']     = $strUsrCreacion;
            $arrayParams['strIpCreacion']      = $strIpCreacion;
            $arrayResultadoSolicitud = $this->servicioGeneral->finalizarDetalleSolicitud($arrayParams);
            if($arrayResultadoSolicitud['status'] != "OK")
            {
                throw new \Exception($arrayResultadoSolicitud['mensaje']);
            }
            //obtengo interface nuevo
            $objInterfaceElementoNuevo = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                        ->findOneBy(array("elementoId"              => $objElementoCpeNuevo->getId(),
                                          "nombreInterfaceElemento" => $objInterfaceElementoActual->getNombreInterfaceElemento()));
            //actualizar servicio tecnico
            $objServicioTecnico->setElementoClienteId($objElementoCpeNuevo->getId());
            $objServicioTecnico->setInterfaceElementoClienteId($objInterfaceElementoNuevo->getId());
            $this->emComercial->persist($objServicioTecnico);
            $this->emComercial->flush();
            //actualizar estado de la interface
            $objInterfaceElementoNuevo->setEstado($objInterfaceElementoActual->getEstado());
            $objInterfaceElementoNuevo->setMacInterfaceElemento($strMacActual);
            $this->emInfraestructura->persist($objInterfaceElementoNuevo);
            $this->emInfraestructura->flush();
            //eliminar los puertos del elemento actual
            $arrayInterfacesElementoActual = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                ->getInterfacesByIdElemento($objElementoClienteActual->getId());
            foreach($arrayInterfacesElementoActual as $objInterface)
            {
                //objeto de la interface nuevo
                $objInterfaceNuevo = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                  ->findOneBy(array("elementoId"              => $objElementoCpeNuevo->getId(),
                                                                    "nombreInterfaceElemento" => $objInterface->getNombreInterfaceElemento()));
                //enlace inicial anterior
                $objEnlaceInterno = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                            ->findOneBy(array('interfaceElementoIniId' =>$objInterface->getId(),
                                                                              'estado'                 =>'Activo'));
                if(is_object($objEnlaceInterno))
                {
                    //cambiar estado eliminado del enlace
                    $objEnlaceInterno->setEstado('Eliminado');
                    $this->emInfraestructura->persist($objEnlaceInterno);
                    $this->emInfraestructura->flush();
                }
                //enlace final anterior
                $objEnlaceFinInterno = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                            ->findOneBy(array('interfaceElementoFinId' =>$objInterface->getId(),
                                                                              'estado'                 =>'Activo'));
                if(is_object($objEnlaceFinInterno))
                {
                    //guardar nuevo enlace
                    if(is_object($objInterfaceNuevo))
                    {
                        $objEnlaceFinNuevo = new InfoEnlace();
                        $objEnlaceFinNuevo->setInterfaceElementoIniId($objEnlaceFinInterno->getInterfaceElementoIniId());
                        $objEnlaceFinNuevo->setInterfaceElementoFinId($objInterfaceNuevo);
                        $objEnlaceFinNuevo->setTipoMedioId($objEnlaceFinInterno->getTipoMedioId());
                        $objEnlaceFinNuevo->setTipoEnlace($objEnlaceFinInterno->getTipoEnlace());
                        $objEnlaceFinNuevo->setEstado("Activo");
                        $objEnlaceFinNuevo->setUsrCreacion($strUsrCreacion);
                        $objEnlaceFinNuevo->setFeCreacion(new \DateTime('now'));
                        $objEnlaceFinNuevo->setIpCreacion($strIpCreacion);
                        $this->emInfraestructura->persist($objEnlaceFinNuevo);
                        $this->emInfraestructura->flush();
                    }
                    //cambiar estado eliminado del enlace
                    $objEnlaceFinInterno->setEstado('Eliminado');
                    $this->emInfraestructura->persist($objEnlaceFinInterno);
                    $this->emInfraestructura->flush();
                }
                //cambiar estado eliminado de la interface
                $objInterface->setEstado('Eliminado');
                $objInterface->setUsrUltMod($strUsrCreacion);
                $objInterface->setFeUltMod(new \DateTime('now'));
                $this->emInfraestructura->persist($objInterface);
                $this->emInfraestructura->flush();  
            }
            //cambiar estado eliminado del elemento
            $objElementoClienteActual->setEstado('Eliminado');
            $this->emInfraestructura->persist($objElementoClienteActual);
            $this->emInfraestructura->flush();
            //se obtiene la asignación del cambio de equipo para reasignar a la misma persona el retiro de equipo
            $objDetalle = $this->emComercial->getRepository('schemaBundle:InfoDetalle')
                                            ->findOneBy(array("detalleSolicitudId" => $objDetalleSolicitud->getId()));
            if(!is_object($objDetalle))
            {
                throw new \Exception("No pudo obtener el detalle de la tarea, por favor notificar a Sistemas.");
            }
            //obtener detalle de elemento
            $objDetalleAsignacion = $this->emComercial->getRepository('schemaBundle:InfoDetalleAsignacion')
                                                    ->findOneBy(array("detalleId" => $objDetalle->getId()),
                                                                array("id"        => "DESC"));
            if(!is_object($objDetalleAsignacion))
            {
                throw new \Exception("No pudo obtener el detalle de la asignación de la tarea, por favor notificar a Sistemas.");
            }
            //obtengo la persona empresa rol
            $objPersonaEmpresaRolUsr = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                    ->find($objDetalleAsignacion->getPersonaEmpresaRolId());
            if(!is_object($objPersonaEmpresaRolUsr))
            {
                throw new \Exception("No pudo obtener la persona empresa rol de la tarea, por favor notificar a Sistemas.");
            }
            //parametros para la tarea de retiro de equipo
            $arrayParamsTarea['objServicio']         = $objServicio;
            $arrayParamsTarea['objElementoCliente']  = $objElementoClienteActual;
            $arrayParamsTarea['idPersonaEmpresaRol'] = $objPersonaEmpresaRolUsr->getId();
            $arrayParamsTarea['strMotivoSolicitud']  = 'CAMBIO DE EQUIPO';
            $arrayParamsTarea['usrCreacion']         = $strUsrCreacion;
            $arrayParamsTarea['ipCreacion']          = $strIpCreacion;
            //verificar si hay asignación de tarea
            if(!empty($arrayParametros["idResponsable"]) && isset($arrayParametros["idResponsable"]) && !empty($arrayParametros["tipoResponsable"]))
            {
                $intMotivoId = $objDetalleSolicitud->getMotivoId();
                if(!empty($intMotivoId))
                {
                    $objAdmiMotivo = $this->emComercial->getRepository('schemaBundle:AdmiMotivo')->find($intMotivoId);
                    if(is_object($objAdmiMotivo))
                    {
                        $arrayParametrosAuditoria["strObservacion"] = $objAdmiMotivo->getNombreMotivo();
                    }
                }
                //verificar tipo responsable
                if($arrayParametros["tipoResponsable"] == "C" )
                {
                    $arrayDatos = $this->emComercial->getRepository('schemaBundle:AdmiCuadrilla')
                                                    ->findJefeCuadrilla($arrayParametros["idResponsable"]);
                    if(!isset($arrayDatos) || empty($arrayDatos))
                    {
                        throw new \Exception("No pudo obtener los datos de la cuadrilla para generar la tarea retiro de equipo, ".
                                             "por favor notificar a Sistemas.");
                    }
                    //obtengo la persona empresa rol
                    $objPersonaEmpresaRolUsr = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                 ->find($arrayDatos['idPersonaEmpresaRol']);
                    if(!is_object($objPersonaEmpresaRolUsr))
                    {
                        throw new \Exception("No pudo obtener la persona empresa rol para generar la tarea retiro de equipo, ".
                                             "por favor notificar a Sistemas.");
                    }
                    $arrayParamsTarea["intIdCuadrilla"]  = $arrayParametros["idResponsable"];
                    $arrayParamsTarea["strTipoAsignado"] = "CUADRILLA";
                }
                else if($arrayParametros["tipoResponsable"] == "E" )
                {
                    //obtengo la persona empresa rol
                    $objPersonaEmpresaRolUsr = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                 ->find($arrayParametros['idResponsable']);
                    if(!is_object($objPersonaEmpresaRolUsr))
                    {
                        throw new \Exception("No pudo obtener la persona empresa rol para generar la tarea retiro de equipo, ".
                                             "por favor notificar a Sistemas.");
                    }
                    $arrayParamsTarea["strTipoAsignado"] = "EMPLEADO";
                }
            }
            //se registra el tracking del elmento
            $arrayParametrosAuditoria["intIdPersona"]    = $objPersonaEmpresaRolUsr->getPersonaId()->getId();
            $arrayParametrosAuditoria["strNumeroSerie"]  = $objElementoClienteActual->getSerieFisica();
            $arrayParametrosAuditoria["strEstadoTelcos"] = 'Eliminado';
            $arrayParametrosAuditoria["strEstadoNaf"]    = 'Instalado';
            $arrayParametrosAuditoria["strEstadoActivo"] = 'CambioEquipo';
            $arrayParametrosAuditoria["strUbicacion"]    = 'EnTransito';
            $arrayParametrosAuditoria["strCodEmpresa"]   = "10";
            $arrayParametrosAuditoria["strTransaccion"]  = 'Cambio de Elemento';
            $arrayParametrosAuditoria["intOficinaId"]    = 0;
            $arrayParametrosAuditoria["strLogin"]        = $objPunto->getLogin();
            $arrayParametrosAuditoria["strUsrCreacion"]  = $strUsrCreacion;
            $this->serviceInfoElemento->ingresaAuditoriaElementos($arrayParametrosAuditoria);
            //se genera la solicitu de retiro de equipo
            $arrayParamsTarea["strPersonaEmpresaRolId"]         = $objPersonaEmpresaRolUsr->getId();
            $arrayParamsTarea["intIdPunto"]                     = $objPunto->getId();
            $arrayParamsTarea["strLogin"]                       = $objPunto->getLogin();
            $arrayParamsTarea["strBandResponsableCambioEquipo"] = "S";
            $arrayParamsTarea["booleanTipoRedGpon"]             = true;
            $this->servicioGeneral->generarSolicitudRetiroEquipo($arrayParamsTarea);
            //historial servicio
            $strHistorialPorServicio = "<b>Se realizó un Cambio de Elemento Cliente:</b><br>"
                        . "<b style='color:red'>$strTipoElemento Anterior : </b><br>"
                        . "<b>Nombre: </b> ".$objElementoClienteActual->getNombreElemento()."<br>"
                        . "<b>Serie : </b> ".$objElementoClienteActual->getSerieFisica()."<br>"
                        . "<b>Modelo: </b> ".$objElementoClienteActual->getModeloElementoId()->getNombreModeloElemento()."<br>"
                        . "<b style='color:red'>$strTipoElemento Actual : </b><br>"
                        . "<b>Nombre: </b> ".$objElementoCpeNuevo->getNombreElemento()."<br>"
                        . "<b>Serie : </b> ".$objElementoCpeNuevo->getSerieFisica()."<br>"
                        . "<b>Modelo: </b> ".$objElementoCpeNuevo->getModeloElementoId()->getNombreModeloElemento()."<br>";
            $objServicioHistorial = new InfoServicioHistorial();
            $objServicioHistorial->setServicioId($objServicio);
            $objServicioHistorial->setObservacion($strHistorialPorServicio);
            $objServicioHistorial->setEstado($objServicio->getEstado());
            $objServicioHistorial->setUsrCreacion($strUsrCreacion);
            $objServicioHistorial->setFeCreacion(new \DateTime('now'));
            $objServicioHistorial->setIpCreacion($strIpCreacion);
            $this->emComercial->persist($objServicioHistorial);
            $this->emComercial->flush();
            //seteo respuesta
            $arrayResponseTn[] = array('status'  => 'OK',
                                       'mensaje' => 'Se realizo el cambio de equipo del servicio');
        }
        catch(\Exception $e)
        {
            $this->serviceUtil->insertError('Telcos+', 'cambioElementoTnGpon', $e->getMessage(),
                                            $strUsrCreacion, $strIpCreacion);
            $arrayResponseTn[] = array('status'  => 'ERROR',
                                       'mensaje' => $e->getMessage());
        }
        return $arrayResponseTn;
    }

    /**
     * Función que realiza el cambio de equipo para el servicio DATOS SAFECITY bajo la red GPON
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 25-10-2021
     * 
     * @author Leonardo Mero <lemero@telconet.ec>
     * @version 1.1 02-08-2022  Validacion de servicios activos y actualizacion de MAC ONT en InfoServicoProdCaract
     *
     * @param Array $arrayParametros
     *
     * @return Array $arrayResponseTn [][
     *                                    'status'    => estado de respuesta de la operación 'OK' o 'ERROR',
     *                                    'mensaje'   => mensaje de la operación o de error
     *                                  ]
     */
    public function cambioElementoOntGponTN($arrayParametros)
    {
        $objElementoClienteActual = $arrayParametros['objElementoClienteActual'];
        $objElementoCpeNuevo      = $arrayParametros['objElementoCpeNuevo'];
        $strCodEmpresa            = $arrayParametros['strCodEmpresa'];
        $strPrefijoEmpresa        = $arrayParametros['strPrefijoEmpresa'];
        $strUsrCreacion           = $arrayParametros['strUsrCreacion'];
        $strIpCreacion            = $arrayParametros['strIpCreacion'];
        $strMacActual             = $arrayParametros['strMacActual'];

        try
        {
            //se obtienen las interfaces
            $arrayInterfacesElementoActual = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
            ->findBy(array(
                "elementoId" => $objElementoClienteActual->getId(),
                "estado"     => "connected"
            ));

            //se recorre las interfaces
            foreach ($arrayInterfacesElementoActual as $objInterfaceElementoActual) 
            {
                $strNombreInterface = $objInterfaceElementoActual->getNombreInterfaceElemento();
                //objeto de la interface nuevo
                $objInterfaceElementoNuevo  = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                ->findOneBy(array(
                    "elementoId"                   => $objElementoCpeNuevo->getId(),
                    "nombreInterfaceElemento"      => $strNombreInterface
                ));
                if (!is_object($objInterfaceElementoNuevo)) 
                {
                    throw new \Exception("No se pudo obtener las interfaces del elemento nuevo no tienen las mismas " .
                    "interfaces con el elemento anterior, por favor notificar a Sistemas.");
                }
                //recorrer enlaces anteriores
                $arrayEnlaceAnterior = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                ->findBy(array(
                    'interfaceElementoIniId' => $objInterfaceElementoActual->getId(),
                    'estado'                 => 'Activo'
                ));
                foreach ($arrayEnlaceAnterior as $objEnlaceAnterior) 
                {
                    //guardar nuevo enlace
                    $objEnlaceNuevo = new InfoEnlace();
                    $objEnlaceNuevo->setInterfaceElementoIniId($objInterfaceElementoNuevo);
                    $objEnlaceNuevo->setInterfaceElementoFinId($objEnlaceAnterior->getInterfaceElementoFinId());
                    $objEnlaceNuevo->setTipoMedioId($objEnlaceAnterior->getTipoMedioId());
                    $objEnlaceNuevo->setTipoEnlace($objEnlaceAnterior->getTipoEnlace());
                    $objEnlaceNuevo->setEstado("Activo");
                    $objEnlaceNuevo->setUsrCreacion($strUsrCreacion);
                    $objEnlaceNuevo->setFeCreacion(new \DateTime('now'));
                    $objEnlaceNuevo->setIpCreacion($strIpCreacion);
                    $this->emInfraestructura->persist($objEnlaceNuevo);
                    $this->emInfraestructura->flush();
                    //eliminar enlace anterior
                    $objEnlaceAnterior->setEstado('Eliminado');
                    $this->emInfraestructura->persist($objEnlaceAnterior);
                    $this->emInfraestructura->flush();
                }
                //seteo estado de la interface nueva
                $objInterfaceElementoNuevo->setEstado('connected');
                $this->emInfraestructura->persist($objInterfaceElementoNuevo);
                $this->emInfraestructura->flush();
            }
            //Obtenemos el punto
            $objPunto               = $arrayParametros['objServicio']->getPuntoId();
            //Actualizamos la MAC ONT para el servico activo (DATOS GPON)
            // $arrayEstadosServicio   = array("Activo", "In-Corte", "EnPruebas", "Asignada");
            $arrayNombresTecnico    = array("DATOS SAFECITY", "SAFECITYDATOS", "SAFECITYWIFI");
            $arrayServiciosPunto    = $this->emComercial->getRepository("schemaBundle:InfoServicio")
            ->createQueryBuilder('s')
            ->join("s.productoId", "p")
                ->join("s.puntoId", "pu")
                ->where("pu.id       = :puntoId")
                //->andWhere("s.estado IN (:estados)")
                ->andWhere("p.nombreTecnico IN (:nombresTecnico)")
                ->setParameter('puntoId', $objPunto->getId())
                //->setParameter('estados',  array_values($arrayEstadosServicio))
                ->setParameter('nombresTecnico', array_values($arrayNombresTecnico))
                ->orderBy('s.id', 'ASC')
                ->getQuery()
                ->getResult();
            foreach ($arrayServiciosPunto as $objServicioPunto) 
            {
                //Obtenemos la caracteristica MAC ONT actual
                $objServiceProfileSerPun = $this->servicioGeneral->getServicioProductoCaracteristica(
                    $objServicioPunto,
                    "MAC ONT",
                    $objServicioPunto->getProductoId()
                );

                if (is_object($objServiceProfileSerPun)) 
                {
                    $objServiceProfileSerPun->setEstado('Eliminado');
                    $objServiceProfileSerPun->setUsrUltMod($strUsrCreacion);
                    $objServiceProfileSerPun->setFeUltMod(new \DateTime('now'));
                    $this->emComercial->persist($objServiceProfileSerPun);
                    $this->emComercial->flush();
                    //Ingresamos la nueva característica MAC ONT
                    $this->servicioGeneral->ingresarServicioProductoCaracteristica(
                        $objServicioPunto,
                        $objServicioPunto->getProductoId(),
                        "MAC ONT",
                        $strMacActual,
                        $strUsrCreacion
                    );
                }
            }

            // Verificamos la existencia de puertos en estado conectado
            $boolServicioActivos = count($arrayInterfacesElementoActual) != 0 ? true : false;
            //Validamos que existan servicios conectados (Activos)
            if ($boolServicioActivos) 
            {
                //obtengo el servicio tecnico
                $objServicioTecnicoOnt   = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                    ->findOneByServicioId($arrayParametros['objServicio']->getId());
                //obtengo el elemento
                $objElementoOlt          = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                ->find($objServicioTecnicoOnt->getElementoId());
                //obtengo la caracteristica service profile
                $objServiceProfileActual = $this->servicioGeneral->getServicioProductoCaracteristica(
                    $arrayParametros['objServicio'],
                    "SERVICE-PROFILE",
                    $arrayParametros['objServicio']->getProductoId()
                );
                if (!is_object($objServiceProfileActual)) 
                {
                    throw new \Exception("No es encontró la característica de service profile actual del servicio, " .
                        "por favor notificar a Sistemas.");
                }
                $strServiceProfileActual = $objServiceProfileActual->getValor();
                //obtengo el service-profile
                $strNombreModeloElementoNuevo    = $objElementoCpeNuevo->getModeloElementoId()->getNombreModeloElemento();
                $objDetalleServiceProfileNameOlt = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                    ->findOneBy(array(
                        "detalleNombre" => "SERVICE-PROFILE-NAME",
                        "detalleValor"  => $strNombreModeloElementoNuevo,
                        "elementoId"    => $objElementoOlt->getId()
                    ));
                if (is_object($objDetalleServiceProfileNameOlt)) 
                {
                    $strFormatoServiceProfile     = $objElementoCpeNuevo->getModeloElementoId()->getNombreModeloElemento();
                    //parametro modelo service profile
                    $arrayParametroServiceProfile = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                        ->getOne(
                            "NUEVA_RED_GPON_TN",
                            "COMERCIAL",
                            "",
                            "REEMPLAZAR_FORMATO_SERVICE_PROFILE",
                            $strNombreModeloElementoNuevo,
                            "",
                            "",
                            "",
                            "",
                            $strCodEmpresa
                        );
                    if (!isset($arrayParametroServiceProfile) || empty($arrayParametroServiceProfile)) 
                    {
                        //parametro general service profile
                        $arrayParametroServiceProfile = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                            ->getOne(
                                "NUEVA_RED_GPON_TN",
                                "COMERCIAL",
                                "",
                                "REEMPLAZAR_FORMATO_SERVICE_PROFILE",
                                "FORMATO_GENERAL",
                                "",
                                "",
                                "",
                                "",
                                $strCodEmpresa
                            );
                    }
                    //verificar parametro
                    if (isset($arrayParametroServiceProfile) &&
                        !empty($arrayParametroServiceProfile) && 
                        isset($arrayParametroServiceProfile['valor2']))
                    {
                        $strFormatoServiceProfile = $arrayParametroServiceProfile['valor2'];
                    }
                    $strServiceProfileNuevo = str_replace("XXXXX", $strNombreModeloElementoNuevo, $strFormatoServiceProfile);
                }
                else 
                {
                    throw new \Exception("No se encontró el SERVICE-PROFILE-NAME en el elemento " . $objElementoOlt->getNombreElemento() .
                        " con el modelo Ont " . $strNombreModeloElementoNuevo . " para tipo de red GPON, por favor notificar a Sistemas.");
                }
                //actualizar service profile al resto de servicios
                 $arrayEstadosServicio   = array("Activo", "In-Corte", "EnPruebas", "Asignada");
                 $arrayNombresTecnico    = array("DATOS SAFECITY", "SAFECITYDATOS", "SAFECITYWIFI");
                 $arrayServiciosPunto    = $this->emComercial->getRepository("schemaBundle:InfoServicio")
                     ->createQueryBuilder('s')
                     ->join("s.productoId", "p")
                     ->join("s.puntoId", "pu")
                     ->where("pu.id       = :puntoId")
                     ->andWhere("s.estado IN (:estados)")
                     ->andWhere("p.nombreTecnico IN (:nombresTecnico)")
                     ->setParameter('puntoId', $objPunto->getId())
                     ->setParameter('estados',  array_values($arrayEstadosServicio))
                     ->setParameter('nombresTecnico', array_values($arrayNombresTecnico))
                     ->orderBy('s.id', 'ASC')
                     ->getQuery()
                     ->getResult();
                foreach ($arrayServiciosPunto as $objServicioPunto) 
                {
                    //obtengo la caracteristica service profile
                    $objServiceProfileSerPun = $this->servicioGeneral->getServicioProductoCaracteristica(
                        $objServicioPunto,
                        "SERVICE-PROFILE",
                        $objServicioPunto->getProductoId()
                    );
                    if (is_object($objServiceProfileSerPun)) 
                    {
                        $objServiceProfileSerPun->setEstado('Eliminado');
                        $objServiceProfileSerPun->setUsrUltMod($strUsrCreacion);
                        $objServiceProfileSerPun->setFeUltMod(new \DateTime('now'));
                        $this->emComercial->persist($objServiceProfileSerPun);
                        $this->emComercial->flush();
                        //se ingresa la característica service profile
                        $this->servicioGeneral->ingresarServicioProductoCaracteristica(
                            $objServicioPunto,
                            $objServicioPunto->getProductoId(),
                            "SERVICE-PROFILE",
                            $strServiceProfileNuevo,
                            $strUsrCreacion
                        );
                    }
                }

                //obtengo la persona empresa rol
                $objInfoPersonaEmpresaRol   = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                    ->find($objPunto->getPersonaEmpresaRolId()->getId());
                if (is_object($objInfoPersonaEmpresaRol)) 
                {
                    $strIdentificacion = $objInfoPersonaEmpresaRol->getPersonaId()->getIdentificacionCliente();
                    $strNombreCliente  = $objInfoPersonaEmpresaRol->getPersonaId()->__toString();
                }
                //obtengo la interface elemento
                $objInterfaceElementoOlt = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                    ->find($objServicioTecnicoOnt->getInterfaceElementoId());
                //obtengo la ip del elemento
                $objInfoIpOlt = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                    ->findOneBy(array(
                        "elementoId" => $objElementoOlt->getId(),
                        "estado"     => "Activo"
                    ));
                if (!is_object($objInfoIpOlt)) 
                {
                    throw new \Exception("No se encontró la Ip del elemento " . $objElementoOlt->getNombreElemento() . " para tipo de red GPON, " .
                        "por favor notificar a Sistemas.");
                }
                //obtengo la caracteristica del indice cliente
                $objServCaractIndice = $this->servicioGeneral->getServicioProductoCaracteristica(
                    $arrayParametros['objServicio'],
                    'INDICE CLIENTE',
                    $arrayParametros['objServicio']->getProductoId()
                );
                if (!is_object($objServCaractIndice)) 
                {
                    throw new \Exception("No es encontró la característica del indice del cliente del servicio, " .
                        "por favor notificar a Sistemas.");
                }
                //obtengo la caracteristica mac ont
                $objMacDelServicio  = $this->servicioGeneral->getServicioProductoCaracteristica(
                    $arrayParametros['objServicio'],
                    "MAC ONT",
                    $arrayParametros['objServicio']->getProductoId()
                );
                if (!is_object($objMacDelServicio)) 
                {
                    throw new \Exception("No es encontró la característica de mac ont del servicio, " .
                    "por favor notificar a Sistemas.");
                }
                //obtengo la caracteristica line profile
                $objLineProfileServicio = $this->servicioGeneral->getServicioProductoCaracteristica(
                    $arrayParametros['objServicio'],
                    "LINE-PROFILE-NAME",
                    $arrayParametros['objServicio']->getProductoId()
                );
                if (!is_object($objLineProfileServicio)) 
                {
                    throw new \Exception("No es encontró la característica de mac ont del servicio, " .
                    "por favor notificar a Sistemas.");
                }
                //setear los datos
                $arrayParametrosDatos['login_aux']             = $arrayParametros['objServicio']->getLoginAux();
                $arrayParametrosDatos['serial_ont']            = $objElementoClienteActual->getSerieFisica();
                $arrayParametrosDatos['mac_ont']               = $objMacDelServicio->getValor();
                $arrayParametrosDatos['nombre_olt']            = $objElementoOlt->getNombreElemento();
                $arrayParametrosDatos['ip_olt']                = $objInfoIpOlt->getIp();
                $arrayParametrosDatos['puerto_olt']            = $objInterfaceElementoOlt->getNombreInterfaceElemento();
                $arrayParametrosDatos['ont_id']                = $objServCaractIndice->getValor();
                $arrayParametrosDatos['modelo_olt']            = $objElementoOlt->getModeloElementoId()->getNombreModeloElemento();
                $arrayParametrosDatos['service_profile']       = $strServiceProfileActual;
                $arrayParametrosDatos['line_profile']          = $objLineProfileServicio->getValor();
                $arrayParametrosDatos['estado_servicio']       = "Activado";
                $arrayParametrosDatos['tipo_negocio_actual']   = "CORPORATIVO";
                $arrayParametrosDatos['serial_ont_nuevo']      = $objElementoCpeNuevo->getSerieFisica();
                $arrayParametrosDatos['service_profile_nuevo'] = $strServiceProfileNuevo;
                //datos ws
                $arrayDatosMiddleware = array(
                    'nombre_cliente'       => $strNombreCliente,
                    'login'                => $objPunto->getLogin(),
                    'identificacion'       => $strIdentificacion,
                    'datos'                => $arrayParametrosDatos,
                    'opcion'               => "TN_CAMBIAR_EQUIPO",
                    'ejecutaComando'       => $this->ejecutaComando,
                    'usrCreacion'          => $strUsrCreacion,
                    'ipCreacion'           => $strIpCreacion,
                    'comandoConfiguracion' => $this->ejecutaComando,
                    'empresa'              => $strPrefijoEmpresa,
                );
                $arrayResponseCPE[] = $this->rdaMiddleware->middleware(json_encode($arrayDatosMiddleware));
            } 
            else 
            {
                $arrayResponseCPE[] = array(
                    'status'  => 'OK',
                    'mensaje' => 'OK'
                );
            } //($boolServicioActivos)
        }
        catch (\Exception $ex)
        {
            $this->serviceUtil->insertError('Telcos+', 'cambioElementoOntGponTN', $ex->getMessage(),
                                            $strUsrCreacion, $strIpCreacion);
            $arrayResponseCPE[] = array('status'  => 'ERROR',
                                        'mensaje' => $ex->getMessage());
        }
        return $arrayResponseCPE;
    }

    /**
     * Función que realiza el cambio de equipo para los servicios TN con nombre tecnico Seg Vehiculo
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 04-11-2022
     * 
     * @author Axel Auza <aauza@telconet.ec>
     * @version 1.1 07-06-2023 - Se agrega validación para obtener los elementos por clientes en el producto SEG_VEHICULO
     * 
     * @param Array $arrayParametros
     *
     * @return Array $arrayResponseTn [][
     *                                    'status'    => estado de respuesta de la operación 'OK' o 'ERROR',
     *                                    'mensaje'   => mensaje de la operación o de error
     *                                  ]
     */
    public function cambioElementoTnSegVehiculo($arrayParametros)
    {
        $strCodEmpresa     = $arrayParametros['idEmpresa'];
        $strUsrCreacion    = $arrayParametros['usrCreacion'];
        $strIpCreacion     = $arrayParametros['ipCreacion'];
        $strMacActual      = $arrayParametros['macElementoNuevo'];
        $strTipoElemento   = "";

        try
        {
            //se realiza la carga y descarga de los Activos.
            $arrayResCarDes = $this->serviceInfoElemento->cargaDescargaActivosCambioEquipo($arrayParametros);
            if (!$arrayResCarDes['status'])
            {
                throw new \Exception($arrayResCarDes['message']);
            }
            //obtengo el servivio
            $objServicio = $arrayParametros['objServicio'];
            if(!is_object($objServicio))
            {
                throw new \Exception("No se encontró el servicio, por favor notificar a Sistemas.");
            }
            $objPunto    = $objServicio->getPuntoId();
            //obtengo el servicio tecnico
            $objServicioTecnico = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                    ->findOneByServicioId($objServicio->getId());
            //obtengo interface actual
            $objInterfaceElementoActual = null;
            $intIdInterfaceCliente      = $objServicioTecnico->getInterfaceElementoClienteId();
            if(!empty($intIdInterfaceCliente))
            {
                $objInterfaceElementoActual = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                    ->find($intIdInterfaceCliente);
            }
            //obtengo el producto
            $objProducto = $objServicio->getProductoId();
            if(!is_object($objProducto))
            {
                throw new \Exception("No se encontró el producto del servicio, por favor notificar a Sistemas.");
            }
            //obtener elemento actual
            $objElementoClienteActual = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                ->findOneById($arrayParametros['idElementoActual']);
            if(!is_object($objElementoClienteActual))
            {
                throw new \Exception("No se encontró el elemento actual del cliente, por favor notificar a Sistemas.");
            }
            //verificar elementos
            $strKeyElemento = str_replace("-".$objServicio->getLoginAux(),"",$objElementoClienteActual->getNombreElemento());
            $strKeyElemento = strtoupper(str_replace("-"," ",$strKeyElemento));
            $arrayParElementos  = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->get('PARAMETROS_SEG_VEHICULOS',
                                                'TECNICO',
                                                '',
                                                'ELEMENTOS_PRODUCTO',
                                                $objProducto->getId(),
                                                '',
                                                $strKeyElemento,
                                                '',
                                                '',
                                                $strCodEmpresa,
                                                'valor5',
                                                '',
                                                '',
                                                '',
                                                $objServicio->getPuntoId()->getPersonaEmpresaRolId()->getId());
            if(!isset($arrayParElementos) || empty($arrayParElementos))
            {
                $arrayParElementos  = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                         ->getOne('PARAMETROS_SEG_VEHICULOS',
                                                                  'TECNICO',
                                                                  '',
                                                                  'ELEMENTOS_PRODUCTO',
                                                                  $objProducto->getId(),
                                                                  '',
                                                                  $strKeyElemento,
                                                                  '',
                                                                  '',
                                                                  $strCodEmpresa,
                                                                  'valor5',
                                                                  '',
                                                                  '',
                                                                  '',
                                                                  'GENERAL');
            }
            if(isset($arrayParElementos) && !empty($arrayParElementos))
            {
                $arrayParametros["booleanSinInterface"]    = $arrayParElementos['valor4'] === "S" ? false : true;
                $arrayParametros["booleanInterfaceNotWan"] = $arrayParElementos['valor4'] === "S";
            }
            //registrar elemento en el naf y telcos
            $arrayParametros["strOrigen"] = "cambioEquipo";
            $arrayParametros['nombreElementoCliente'] = $objElementoClienteActual->getNombreElemento();
            $arrayRequestElemento         = $this->registrarElementoNuevoNAF($arrayParametros);
            if(!isset($arrayRequestElemento) || $arrayRequestElemento['status'] != 'OK' || !is_object($arrayRequestElemento['objElementoCpe']))
            {
                throw new \Exception("No pudo ser crear el nuevo elemento del cliente, por favor notificar a Sistemas.");
            }
            //obtener elemento nuevo
            $objElementoCpeNuevo = $arrayRequestElemento['objElementoCpe'];
            //obtengo el detalle de la solicitud
            $objTipoSolicitud    = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                ->findOneBy(array("descripcionSolicitud" => "SOLICITUD CAMBIO DE MODEM INMEDIATO",
                                                                  "estado"               => "Activo"));
            if(!is_object($objTipoSolicitud))
            {
                throw new \Exception("No se encontró el tipo de la solicitud *SOLICITUD CAMBIO DE MODEM INMEDIATO*, ".
                                     "por favor notificar a Sistemas.");
            }
            $objCaractEleCliente = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneBy(array("descripcionCaracteristica" => "ELEMENTO CLIENTE",
                                                                  "estado"                    => "Activo"));
            if(!is_object($objCaractEleCliente))
            {
                throw new \Exception("No se encontró la característica *ELEMENTO CLIENTE*, por favor notificar a Sistemas.");
            }
            $objDetalleSolicitud = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                        ->createQueryBuilder('s')
                                        ->innerJoin('schemaBundle:InfoDetalleSolCaract', 'car', 'WITH', 'car.detalleSolicitudId = s.id')
                                        ->where('s.servicioId = :servicioId')
                                        ->andWhere("s.tipoSolicitudId = :tipoSolicitudId")
                                        ->andWhere("s.estado = :estado")
                                        ->andWhere("car.caracteristicaId = :caracteristicaId")
                                        ->andWhere("car.valor = :valor")
                                        ->andWhere("car.estado = :estado")
                                        ->setParameter('servicioId', $objServicio->getId())
                                        ->setParameter('tipoSolicitudId', $objTipoSolicitud->getId())
                                        ->setParameter('caracteristicaId', $objCaractEleCliente->getId())
                                        ->setParameter('valor', $objElementoClienteActual->getId())
                                        ->setParameter('estado', "AsignadoTarea")
                                        ->setMaxResults(1)
                                        ->getQuery()
                                        ->getOneOrNullResult();
            if(!is_object($objDetalleSolicitud))
            {
                throw new \Exception("No se encontró el detalle de la solicitud, por favor notificar a Sistemas.");
            }
            //obtengo interface nuevo
            if(is_object($objInterfaceElementoActual))
            {
                $objInterfaceElementoNuevo = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                            ->findOneBy(array("elementoId"              => $objElementoCpeNuevo->getId(),
                                              "nombreInterfaceElemento" => $objInterfaceElementoActual->getNombreInterfaceElemento()));
                //actualizar servicio tecnico
                if(is_object($objInterfaceElementoNuevo))
                {
                    $objServicioTecnico->setElementoClienteId($objElementoCpeNuevo->getId());
                    $objServicioTecnico->setInterfaceElementoClienteId($objInterfaceElementoNuevo->getId());
                    $this->emComercial->persist($objServicioTecnico);
                    $this->emComercial->flush();
                    //actualizar estado de la interface
                    $objInterfaceElementoNuevo->setEstado($objInterfaceElementoActual->getEstado());
                    $objInterfaceElementoNuevo->setMacInterfaceElemento($strMacActual);
                    $this->emInfraestructura->persist($objInterfaceElementoNuevo);
                    $this->emInfraestructura->flush();
                }
            }
            //eliminar los puertos del elemento actual
            $arrayInterfacesElementoActual = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                ->getInterfacesByIdElemento($objElementoClienteActual->getId());
            foreach($arrayInterfacesElementoActual as $objInterface)
            {
                //cambiar estado eliminado de la interface
                $objInterface->setEstado('Eliminado');
                $objInterface->setUsrUltMod($strUsrCreacion);
                $objInterface->setFeUltMod(new \DateTime('now'));
                $this->emInfraestructura->persist($objInterface);
                $this->emInfraestructura->flush();  
            }
            //cambiar estado eliminado del elemento
            $objElementoClienteActual->setEstado('Eliminado');
            $this->emInfraestructura->persist($objElementoClienteActual);
            $this->emInfraestructura->flush();
            //eliminar caracteristica de relacion del cliente con elemento
            $objCaracElementoCliente = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                            ->findOneBy(array("descripcionCaracteristica" => "ELEMENTO_CLIENTE_ID",
                                                              "estado"                    => "Activo"));
            if(is_object($objCaracElementoCliente))
            {
                $objProdCaracEleCliente = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                ->findOneBy(array("productoId"       => $objProducto->getId(),
                                                                  "caracteristicaId" => $objCaracElementoCliente->getId(),
                                                                  "estado"           => "Activo"));
                if(is_object($objProdCaracEleCliente))
                {
                    $objServProdCaracEleCliente = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                ->findOneBy(array("servicioId"                => $objServicio->getId(),
                                                                  "productoCaracterisiticaId" => $objProdCaracEleCliente->getId(),
                                                                  "valor"                     => $objElementoClienteActual->getId(),
                                                                  "estado"                    => "Activo"));
                    if(is_object($objServProdCaracEleCliente))
                    {
                        $objServProdCaracEleCliente->setEstado('Eliminado');
                        $objServProdCaracEleCliente->setUsrUltMod($strUsrCreacion);
                        $objServProdCaracEleCliente->setFeUltMod(new \DateTime('now'));
                        $this->emComercial->persist($objServProdCaracEleCliente);
                        $this->emComercial->flush();
                    }
                }
            }
            //insert caracteristica de relacion del cliente con elemento
            $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio,
                                                                           $objProducto,
                                                                           "ELEMENTO_CLIENTE_ID",
                                                                           $objElementoCpeNuevo->getId(),
                                                                           $strUsrCreacion);
            //se obtiene la asignación del cambio de equipo para reasignar a la misma persona el retiro de equipo
            $objDetalle = $this->emComercial->getRepository('schemaBundle:InfoDetalle')
                                            ->findOneBy(array("detalleSolicitudId" => $objDetalleSolicitud->getId()));
            if(!is_object($objDetalle))
            {
                throw new \Exception("No pudo obtener el detalle de la tarea, por favor notificar a Sistemas.");
            }
            //obtener detalle de elemento
            $objDetalleAsignacion = $this->emComercial->getRepository('schemaBundle:InfoDetalleAsignacion')
                                                    ->findOneBy(array("detalleId" => $objDetalle->getId()),
                                                                array("id"        => "DESC"));
            if(!is_object($objDetalleAsignacion))
            {
                throw new \Exception("No pudo obtener el detalle de la asignación de la tarea, por favor notificar a Sistemas.");
            }
            //obtengo la persona empresa rol
            $objPersonaEmpresaRolUsr = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                    ->find($objDetalleAsignacion->getPersonaEmpresaRolId());
            if(!is_object($objPersonaEmpresaRolUsr))
            {
                throw new \Exception("No pudo obtener la persona empresa rol de la tarea, por favor notificar a Sistemas.");
            }
            //parametros para la tarea de retiro de equipo
            $arrayParamsTarea['objServicio']         = $objServicio;
            $arrayParamsTarea['objElementoCliente']  = $objElementoClienteActual;
            $arrayParamsTarea['idPersonaEmpresaRol'] = $objPersonaEmpresaRolUsr->getId();
            $arrayParamsTarea['strMotivoSolicitud']  = 'CAMBIO DE EQUIPO';
            $arrayParamsTarea['usrCreacion']         = $strUsrCreacion;
            $arrayParamsTarea['ipCreacion']          = $strIpCreacion;
            //verificar si hay asignación de tarea
            if(!empty($arrayParametros["idResponsable"]) && isset($arrayParametros["idResponsable"]) && !empty($arrayParametros["tipoResponsable"]))
            {
                $intMotivoId = $objDetalleSolicitud->getMotivoId();
                if(!empty($intMotivoId))
                {
                    $objAdmiMotivo = $this->emComercial->getRepository('schemaBundle:AdmiMotivo')->find($intMotivoId);
                    if(is_object($objAdmiMotivo))
                    {
                        $arrayParametrosAuditoria["strObservacion"] = $objAdmiMotivo->getNombreMotivo();
                    }
                }
                //verificar tipo responsable
                if($arrayParametros["tipoResponsable"] == "C" )
                {
                    $arrayDatos = $this->emComercial->getRepository('schemaBundle:AdmiCuadrilla')
                                                    ->findJefeCuadrilla($arrayParametros["idResponsable"]);
                    if(!isset($arrayDatos) || empty($arrayDatos))
                    {
                        throw new \Exception("No pudo obtener los datos de la cuadrilla para generar la tarea retiro de equipo, ".
                                             "por favor notificar a Sistemas.");
                    }
                    //obtengo la persona empresa rol
                    $objPersonaEmpresaRolUsr = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                 ->find($arrayDatos['idPersonaEmpresaRol']);
                    if(!is_object($objPersonaEmpresaRolUsr))
                    {
                        throw new \Exception("No pudo obtener la persona empresa rol para generar la tarea retiro de equipo, ".
                                             "por favor notificar a Sistemas.");
                    }
                    $arrayParamsTarea["intIdCuadrilla"]  = $arrayParametros["idResponsable"];
                    $arrayParamsTarea["strTipoAsignado"] = "CUADRILLA";
                }
                else if($arrayParametros["tipoResponsable"] == "E" )
                {
                    //obtengo la persona empresa rol
                    $objPersonaEmpresaRolUsr = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                 ->find($arrayParametros['idResponsable']);
                    if(!is_object($objPersonaEmpresaRolUsr))
                    {
                        throw new \Exception("No pudo obtener la persona empresa rol para generar la tarea retiro de equipo, ".
                                             "por favor notificar a Sistemas.");
                    }
                    $arrayParamsTarea["strTipoAsignado"] = "EMPLEADO";
                }
            }
            //finalizar solicitud
            $arrayCarDetSolicitud = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findBy(array("detalleSolicitudId" => $objDetalleSolicitud->getId()));
            foreach($arrayCarDetSolicitud as $objCarDetSolicitud)
            {
                $objCarDetSolicitud->setEstado("Finalizada");
                $this->emComercial->persist($objCarDetSolicitud);
                $this->emComercial->flush();
            }
            $objDetalleSolicitud->setEstado("Finalizada");
            $this->emComercial->persist($objDetalleSolicitud);
            $this->emComercial->flush();
            //crear historial para la solicitud
            $objDetalleSolsHist = new InfoDetalleSolHist();
            $objDetalleSolsHist->setDetalleSolicitudId($objDetalleSolicitud);
            $objDetalleSolsHist->setEstado("Finalizada");
            $objDetalleSolsHist->setObservacion("Se finalizo solicitud.");
            $objDetalleSolsHist->setUsrCreacion($strUsrCreacion);
            $objDetalleSolsHist->setFeCreacion(new \DateTime('now'));
            $objDetalleSolsHist->setIpCreacion($strIpCreacion);
            $this->emComercial->persist($objDetalleSolsHist);
            $this->emComercial->flush();
            //se registra el tracking del elmento
            $arrayParametrosAuditoria["intIdPersona"]    = $objPersonaEmpresaRolUsr->getPersonaId()->getId();
            $arrayParametrosAuditoria["strNumeroSerie"]  = $objElementoClienteActual->getSerieFisica();
            $arrayParametrosAuditoria["strEstadoTelcos"] = 'Eliminado';
            $arrayParametrosAuditoria["strEstadoNaf"]    = 'Instalado';
            $arrayParametrosAuditoria["strEstadoActivo"] = 'CambioEquipo';
            $arrayParametrosAuditoria["strUbicacion"]    = 'EnTransito';
            $arrayParametrosAuditoria["strCodEmpresa"]   = "10";
            $arrayParametrosAuditoria["strTransaccion"]  = 'Cambio de Elemento';
            $arrayParametrosAuditoria["intOficinaId"]    = 0;
            $arrayParametrosAuditoria["strLogin"]        = $objPunto->getLogin();
            $arrayParametrosAuditoria["strUsrCreacion"]  = $strUsrCreacion;
            $this->serviceInfoElemento->ingresaAuditoriaElementos($arrayParametrosAuditoria);
            //se genera la solicitu de retiro de equipo
            $arrayParamsTarea["strPersonaEmpresaRolId"]         = $objPersonaEmpresaRolUsr->getId();
            $arrayParamsTarea["intIdPunto"]                     = $objPunto->getId();
            $arrayParamsTarea["strLogin"]                       = $objPunto->getLogin();
            $arrayParamsTarea["strBandResponsableCambioEquipo"] = "S";
            $arrayParamsTarea["booleanTipoRedGpon"]             = true;
            $this->servicioGeneral->generarSolicitudRetiroEquipo($arrayParamsTarea);
            //historial servicio
            $strHistorialPorServicio = "<b>Se realizó un Cambio de Elemento Cliente:</b><br>"
                        . "<b style='color:red'>$strTipoElemento Anterior : </b><br>"
                        . "<b>Nombre: </b> ".$objElementoClienteActual->getNombreElemento()."<br>"
                        . "<b>Serie : </b> ".$objElementoClienteActual->getSerieFisica()."<br>"
                        . "<b>Modelo: </b> ".$objElementoClienteActual->getModeloElementoId()->getNombreModeloElemento()."<br>"
                        . "<b style='color:red'>$strTipoElemento Actual : </b><br>"
                        . "<b>Nombre: </b> ".$objElementoCpeNuevo->getNombreElemento()."<br>"
                        . "<b>Serie : </b> ".$objElementoCpeNuevo->getSerieFisica()."<br>"
                        . "<b>Modelo: </b> ".$objElementoCpeNuevo->getModeloElementoId()->getNombreModeloElemento()."<br>";
            $objServicioHistorial = new InfoServicioHistorial();
            $objServicioHistorial->setServicioId($objServicio);
            $objServicioHistorial->setObservacion($strHistorialPorServicio);
            $objServicioHistorial->setEstado($objServicio->getEstado());
            $objServicioHistorial->setUsrCreacion($strUsrCreacion);
            $objServicioHistorial->setFeCreacion(new \DateTime('now'));
            $objServicioHistorial->setIpCreacion($strIpCreacion);
            $this->emComercial->persist($objServicioHistorial);
            $this->emComercial->flush();
            //seteo respuesta
            $arrayResponseTn[] = array('status'  => 'OK',
                                       'mensaje' => 'Se realizo el cambio de equipo del servicio');
        }
        catch(\Exception $e)
        {
            $this->serviceUtil->insertError('Telcos+', 'cambioElementoTnSegVehiculo', $e->getMessage(),
                                            $strUsrCreacion, $strIpCreacion);
            $arrayResponseTn[] = array('status'  => 'ERROR',
                                       'mensaje' => $e->getMessage());
        }
        return $arrayResponseTn;
    }

    /**
     * Funcion que permite realizar el cambio de equipo para el producto SAFE ENTRY
     * 
     * @param array $arrayParams
     * @return array [status => 'OK | ERROR'
     *                mensaje]
     * 
     * @author Leonardo Mero <lemero@telconet.ec>
     * @version 1.0 20-09-2022 - Version inicial
     */
    public function cambioElementoTnSafeEntry($arrayParametros)
    {   
        $strCodEmpresa     = $arrayParametros['idEmpresa'];
        $strPrefijoEmpresa = $arrayParametros['prefijoEmpresa'];
        $strUsrCreacion    = $arrayParametros['usrCreacion'];
        $strIpCreacion     = $arrayParametros['ipCreacion'];
        $strMacActual      = $arrayParametros['macElementoNuevo'];
        $strTipoElemento   = "";

        try
        {
            //se realiza la carga y descarga de los Activos.
            $arrayResCarDes = $this->serviceInfoElemento->cargaDescargaActivosCambioEquipo($arrayParametros);
            if (!$arrayResCarDes['status'])
            {
                throw new \Exception($arrayResCarDes['message']);
            }
            //obtengo el servivio
            $objServicio = $arrayParametros['objServicio'];
            if(!is_object($objServicio))
            {
                throw new \Exception("No se encontró el servicio, por favor notificar a Sistemas.");
            }
            $objPunto    = $objServicio->getPuntoId();

            $objCliente  = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                                ->findOneBy(array('id'  => $objPunto->getPersonaEmpresaRolId()->getPersonaId()->getId(),
                                                  'estado' => 'Activo'));
            //obtengo el servicio tecnico
            $objServicioTecnico = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                    ->findOneByServicioId($objServicio->getId());
            //obtengo interface actual
            $objInterfaceElementoActual = null;
            $intIdInterfaceCliente      = $objServicioTecnico->getInterfaceElementoClienteId();
            if(!empty($intIdInterfaceCliente))
            {
                $objInterfaceElementoActual = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                    ->find($intIdInterfaceCliente);
            }
            //obtengo el producto
            $objProducto = $objServicio->getProductoId();
            if(!is_object($objProducto))
            {
                throw new \Exception("No se encontró el producto del servicio, por favor notificar a Sistemas.");
            }
            //obtener elemento actual
            $objElementoClienteActual = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                ->findOneById($arrayParametros['idElementoActual']);
            if(!is_object($objElementoClienteActual))
            {
                throw new \Exception("No se encontró el elemento actual del cliente, por favor notificar a Sistemas.");
            }
            //verificar elementos
            $strKeyElemento = str_replace("-".$objServicio->getLoginAux(),"",$objElementoClienteActual->getNombreElemento());
            $strKeyElemento = strtoupper(str_replace("-"," ",$strKeyElemento));

            $arrayParElementos  = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                     ->getOne('CONFIG ELEMENTOS SAFE ENTRY',
                                                              'TECNICO',
                                                              '',
                                                              'ELEMENTOS_SAFE_ENTRY',
                                                              $objProducto->getNombreTecnico(),
                                                              $strKeyElemento,
                                                              '',
                                                              '',
                                                              '',
                                                              $strCodEmpresa,
                                                              'valor7');
            if(isset($arrayParElementos) && !empty($arrayParElementos))
            {
                $arrayParametros["booleanSinInterface"]    = $arrayParElementos['valor6'] === "S" ? false : true;
                $arrayParametros["booleanInterfaceNotWan"] = $arrayParElementos['valor6'] === "S";
            }
            //registrar elemento en el naf y telcos
            $arrayParametros["strOrigen"] = "cambioEquipo";
            $arrayParametros['nombreElementoCliente'] = $objElementoClienteActual->getNombreElemento();
            $arrayRequestElemento         = $this->registrarElementoNuevoNAF($arrayParametros);
            if(!isset($arrayRequestElemento) || $arrayRequestElemento['status'] != 'OK' || !is_object($arrayRequestElemento['objElementoCpe']))
            {
                throw new \Exception("No pudo ser crear el nuevo elemento del cliente, por favor notificar a Sistemas.");
            }
            //obtener elemento nuevo
            $objElementoCpeNuevo = $arrayRequestElemento['objElementoCpe'];
            //obtengo el detalle de la solicitud
            $objTipoSolicitud    = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                ->findOneBy(array("descripcionSolicitud" => "SOLICITUD CAMBIO DE MODEM INMEDIATO",
                                                                  "estado"               => "Activo"));
            if(!is_object($objTipoSolicitud))
            {
                throw new \Exception("No se encontró el tipo de la solicitud *SOLICITUD CAMBIO DE MODEM INMEDIATO*, ".
                                     "por favor notificar a Sistemas.");
            }
            $objCaractEleCliente = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneBy(array("descripcionCaracteristica" => "ELEMENTO CLIENTE",
                                                                  "estado"                    => "Activo"));
            if(!is_object($objCaractEleCliente))
            {
                throw new \Exception("No se encontró la característica *ELEMENTO CLIENTE*, por favor notificar a Sistemas.");
            }
            $objDetalleSolicitud = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                        ->createQueryBuilder('s')
                                        ->innerJoin('schemaBundle:InfoDetalleSolCaract', 'car', 'WITH', 'car.detalleSolicitudId = s.id')
                                        ->where('s.servicioId = :servicioId')
                                        ->andWhere("s.tipoSolicitudId = :tipoSolicitudId")
                                        ->andWhere("s.estado = :estado")
                                        ->andWhere("car.caracteristicaId = :caracteristicaId")
                                        ->andWhere("car.valor = :valor")
                                        ->andWhere("car.estado = :estado")
                                        ->setParameter('servicioId', $objServicio->getId())
                                        ->setParameter('tipoSolicitudId', $objTipoSolicitud->getId())
                                        ->setParameter('caracteristicaId', $objCaractEleCliente->getId())
                                        ->setParameter('valor', $objElementoClienteActual->getId())
                                        ->setParameter('estado', "AsignadoTarea")
                                        ->setMaxResults(1)
                                        ->getQuery()
                                        ->getOneOrNullResult();
            if(!is_object($objDetalleSolicitud))
            {
                throw new \Exception("No se encontró el detalle de la solicitud, por favor notificar a Sistemas.");
            }
            //Si existe la interface del elemento que se va a cambiar
            if(is_object($objInterfaceElementoActual))
            {
                //Se busca una interface del elemento nueva que no este conectada
                $objInterfaceElementoNuevo = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                             ->findOneBy(array('elementoId' => $objElementoCpeNuevo->getId(),
                                                               'estado'     => 'not connect'),
                                                         array('id'         => 'ASC'));
                //Si no existe la interface para el elemento se crea una nueva
                if(!isset($objInterfaceElementoNuevo))
                {
                    $objInterfaceElementoNuevo = new InfoInterfaceElemento();
                    $objInterfaceElementoNuevo->setElementoId($objElementoCpeNuevo);
                    $objInterfaceElementoNuevo->setNombreInterfaceElemento('eth01');
                    $objInterfaceElementoNuevo->setDescripcionInterfaceElemento('Ethernet');
                    $objInterfaceElementoNuevo->setUsrCreacion($arrayParametros['usrCreacion']);
                    $objInterfaceElementoNuevo->setFeCreacion(new \DateTime('now'));
                    $objInterfaceElementoNuevo->setIpCreacion($arrayParametros['ipCreacion']);
                }
                //actualizar servicio tecnico
                if(is_object($objInterfaceElementoNuevo))
                {
                    $objServicioTecnico->setElementoClienteId($objElementoCpeNuevo->getId());
                    $objServicioTecnico->setInterfaceElementoClienteId($objInterfaceElementoNuevo->getId());
                    $this->emComercial->persist($objServicioTecnico);
                    $this->emComercial->flush();
                    //actualizar estado de la interface
                    $objInterfaceElementoNuevo->setEstado($objInterfaceElementoActual->getEstado());
                    $objInterfaceElementoNuevo->setMacInterfaceElemento($strMacActual);
                    $this->emInfraestructura->persist($objInterfaceElementoNuevo);
                    $this->emInfraestructura->flush();

                    //Registrar la mac del elemento
                    if(!empty($strMacActual) && is_object($objElementoCpeNuevo))
                    {
                        $objDetalleElementoMac = new InfoDetalleElemento();
                        $objDetalleElementoMac->setElementoId($objElementoCpeNuevo->getId());
                        $objDetalleElementoMac->setDetalleNombre('MAC');
                        $objDetalleElementoMac->setDetalleValor($strMacActual);
                        $objDetalleElementoMac->setDetalleDescripcion('Mac del equipo del cliente');
                        $objDetalleElementoMac->setFeCreacion(new \DateTime('now'));
                        $objDetalleElementoMac->setUsrCreacion($strUsrCreacion);
                        $objDetalleElementoMac->setIpCreacion($strIpCreacion);
                        $objDetalleElementoMac->setEstado('Activo');
                        $this->emInfraestructura->persist($objDetalleElementoMac);
                        $this->emInfraestructura->flush();
                    }
                    //Se crean los nuevos enlaces y  se eliminan los actuales
                    $arrayEnlaces = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                         ->findBy(array('estado'                 => 'Activo',
                                                        'interfaceElementoFinId' => $objInterfaceElementoActual->getId()));
                    foreach($arrayEnlaces as $objEnlaceActual)
                    {
                        $objEnlaceActual->setEstado('Eliminado');
                        $this->emInfraestructura->persist($objEnlaceActual);
                        $this->emInfraestructura->flush();

                        $objEnlace = new InfoEnlace();
                        $objEnlace->setInterfaceElementoIniId($objEnlaceActual->getInterfaceElementoIniId());
                        $objEnlace->setInterfaceElementoFinId($objInterfaceElementoNuevo);
                        $objEnlace->setTipoMedioId($objEnlaceActual->getTipoMedioId());
                        $objEnlace->setTipoEnlace('PRINCIPAL');
                        $objEnlace->setEstado('Activo');
                        $objEnlace->setUsrCreacion($strUsrCreacion);
                        $objEnlace->setFeCreacion(new \DateTime('now'));
                        $objEnlace->setIpCreacion($strIpCreacion);
                        $this->emInfraestructura->persist($objEnlace);
                        $this->emInfraestructura->flush();
                    }
                }
            }
            //eliminar los puertos del elemento actual
            $arrayInterfacesElementoActual = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                ->getInterfacesByIdElemento($objElementoClienteActual->getId());
            foreach($arrayInterfacesElementoActual as $objInterface)
            {

                //cambiar estado eliminado de la interface
                $objInterface->setEstado('Eliminado');
                $objInterface->setUsrUltMod($strUsrCreacion);
                $objInterface->setFeUltMod(new \DateTime('now'));
                $this->emInfraestructura->persist($objInterface);
                $this->emInfraestructura->flush();  
            }
            //cambiar estado eliminado del elemento
            $objElementoClienteActual->setEstado('Eliminado');
            $this->emInfraestructura->persist($objElementoClienteActual);
            $this->emInfraestructura->flush();
            //eliminar caracteristica de relacion del cliente con elemento
            $objCaracElementoCliente = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                            ->findOneBy(array("descripcionCaracteristica" => "ELEMENTO_CLIENTE_ID",
                                                              "estado"                    => "Activo"));
            if(is_object($objCaracElementoCliente))
            {
                $objProdCaracEleCliente = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                ->findOneBy(array("productoId"       => $objProducto->getId(),
                                                                  "caracteristicaId" => $objCaracElementoCliente->getId(),
                                                                  "estado"           => "Activo"));
                if(is_object($objProdCaracEleCliente))
                {
                    $objServProdCaracEleCliente = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                ->findOneBy(array("servicioId"                => $objServicio->getId(),
                                                                  "productoCaracterisiticaId" => $objProdCaracEleCliente->getId(),
                                                                  "valor"                     => $objElementoClienteActual->getId(),
                                                                  "estado"                    => "Activo"));
                    if(is_object($objServProdCaracEleCliente))
                    {
                        $objServProdCaracEleCliente->setEstado('Eliminado');
                        $objServProdCaracEleCliente->setUsrUltMod($strUsrCreacion);
                        $objServProdCaracEleCliente->setFeUltMod(new \DateTime('now'));
                        $this->emComercial->persist($objServProdCaracEleCliente);
                        $this->emComercial->flush();
                    }
                }
            }
            //insert caracteristica de relacion del cliente con elemento
            $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio,
                                                                           $objProducto,
                                                                           "ELEMENTO_CLIENTE_ID",
                                                                           $objElementoCpeNuevo->getId(),
                                                                           $strUsrCreacion);
            //se obtiene la asignación del cambio de equipo para reasignar a la misma persona el retiro de equipo
            $objDetalle = $this->emComercial->getRepository('schemaBundle:InfoDetalle')
                                            ->findOneBy(array("detalleSolicitudId" => $objDetalleSolicitud->getId()));
            if(!is_object($objDetalle))
            {
                throw new \Exception("No pudo obtener el detalle de la tarea, por favor notificar a Sistemas.");
            }
            //obtener detalle de elemento
            $objDetalleAsignacion = $this->emComercial->getRepository('schemaBundle:InfoDetalleAsignacion')
                                                    ->findOneBy(array("detalleId" => $objDetalle->getId()),
                                                                array("id"        => "DESC"));
            if(!is_object($objDetalleAsignacion))
            {
                throw new \Exception("No pudo obtener el detalle de la asignación de la tarea, por favor notificar a Sistemas.");
            }
            //obtengo la persona empresa rol
            $objPersonaEmpresaRolUsr = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                    ->find($objDetalleAsignacion->getPersonaEmpresaRolId());
            if(!is_object($objPersonaEmpresaRolUsr))
            {
                throw new \Exception("No pudo obtener la persona empresa rol de la tarea, por favor notificar a Sistemas.");
            }
            //parametros para la tarea de retiro de equipo
            $arrayParamsTarea['objServicio']         = $objServicio;
            $arrayParamsTarea['objElementoCliente']  = $objElementoClienteActual;
            $arrayParamsTarea['idPersonaEmpresaRol'] = $objPersonaEmpresaRolUsr->getId();
            $arrayParamsTarea['strMotivoSolicitud']  = 'CAMBIO DE EQUIPO';
            $arrayParamsTarea['usrCreacion']         = $strUsrCreacion;
            $arrayParamsTarea['ipCreacion']          = $strIpCreacion;
            //verificar si hay asignación de tarea
            if(!empty($arrayParametros["idResponsable"]) && isset($arrayParametros["idResponsable"]) && !empty($arrayParametros["tipoResponsable"]))
            {
                $intMotivoId = $objDetalleSolicitud->getMotivoId();
                if(!empty($intMotivoId))
                {
                    $objAdmiMotivo = $this->emComercial->getRepository('schemaBundle:AdmiMotivo')->find($intMotivoId);
                    if(is_object($objAdmiMotivo))
                    {
                        $arrayParametrosAuditoria["strObservacion"] = $objAdmiMotivo->getNombreMotivo();
                    }
                }
                //verificar tipo responsable
                if($arrayParametros["tipoResponsable"] == "C" )
                {
                    $arrayDatos = $this->emComercial->getRepository('schemaBundle:AdmiCuadrilla')
                                                    ->findJefeCuadrilla($arrayParametros["idResponsable"]);
                    if(!isset($arrayDatos) || empty($arrayDatos))
                    {
                        throw new \Exception("No pudo obtener los datos de la cuadrilla para generar la tarea retiro de equipo, ".
                                             "por favor notificar a Sistemas.");
                    }
                    //obtengo la persona empresa rol
                    $objPersonaEmpresaRolUsr = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                 ->find($arrayDatos['idPersonaEmpresaRol']);
                    if(!is_object($objPersonaEmpresaRolUsr))
                    {
                        throw new \Exception("No pudo obtener la persona empresa rol para generar la tarea retiro de equipo, ".
                                             "por favor notificar a Sistemas.");
                    }
                    $arrayParamsTarea["intIdCuadrilla"]  = $arrayParametros["idResponsable"];
                    $arrayParamsTarea["strTipoAsignado"] = "CUADRILLA";
                }
                else if($arrayParametros["tipoResponsable"] == "E" )
                {
                    //obtengo la persona empresa rol
                    $objPersonaEmpresaRolUsr = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                 ->find($arrayParametros['idResponsable']);
                    if(!is_object($objPersonaEmpresaRolUsr))
                    {
                        throw new \Exception("No pudo obtener la persona empresa rol para generar la tarea retiro de equipo, ".
                                             "por favor notificar a Sistemas.");
                    }
                    $arrayParamsTarea["strTipoAsignado"] = "EMPLEADO";
                }
            }
            //finalizar solicitud
            $arrayCarDetSolicitud = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findBy(array("detalleSolicitudId" => $objDetalleSolicitud->getId()));
            foreach($arrayCarDetSolicitud as $objCarDetSolicitud)
            {
                $objCarDetSolicitud->setEstado("Finalizada");
                $this->emComercial->persist($objCarDetSolicitud);
                $this->emComercial->flush();
            }
            $objDetalleSolicitud->setEstado("Finalizada");
            $this->emComercial->persist($objDetalleSolicitud);
            $this->emComercial->flush();
            //crear historial para la solicitud
            $objDetalleSolsHist = new InfoDetalleSolHist();
            $objDetalleSolsHist->setDetalleSolicitudId($objDetalleSolicitud);
            $objDetalleSolsHist->setEstado("Finalizada");
            $objDetalleSolsHist->setObservacion("Se finalizo solicitud.");
            $objDetalleSolsHist->setUsrCreacion($strUsrCreacion);
            $objDetalleSolsHist->setFeCreacion(new \DateTime('now'));
            $objDetalleSolsHist->setIpCreacion($strIpCreacion);
            $this->emComercial->persist($objDetalleSolsHist);
            $this->emComercial->flush();
            //se registra el tracking del elmento
            $arrayParametrosAuditoria["intIdPersona"]    = $objPersonaEmpresaRolUsr->getPersonaId()->getId();
            $arrayParametrosAuditoria["strNumeroSerie"]  = $objElementoClienteActual->getSerieFisica();
            $arrayParametrosAuditoria["strEstadoTelcos"] = 'Eliminado';
            $arrayParametrosAuditoria["strEstadoNaf"]    = 'Instalado';
            $arrayParametrosAuditoria["strEstadoActivo"] = 'CambioEquipo';
            $arrayParametrosAuditoria["strUbicacion"]    = 'EnTransito';
            $arrayParametrosAuditoria["strCodEmpresa"]   = "10";
            $arrayParametrosAuditoria["strTransaccion"]  = 'Cambio de Elemento';
            $arrayParametrosAuditoria["intOficinaId"]    = 0;
            $arrayParametrosAuditoria["strLogin"]        = $objPunto->getLogin();
            $arrayParametrosAuditoria["strUsrCreacion"]  = $strUsrCreacion;
            $this->serviceInfoElemento->ingresaAuditoriaElementos($arrayParametrosAuditoria);
            //se genera la solicitud de retiro de equipo
            $arrayParamsTarea["strPersonaEmpresaRolId"]         = $objPersonaEmpresaRolUsr->getId();
            $arrayParamsTarea["intIdPunto"]                     = $objPunto->getId();
            $arrayParamsTarea["strLogin"]                       = $objPunto->getLogin();
            $arrayParamsTarea["strBandResponsableCambioEquipo"] = "S";
            $this->servicioGeneral->generarSolicitudRetiroEquipo($arrayParamsTarea);
            //historial servicio
            $strHistorialPorServicio = "<b>Se realizó un Cambio de Elemento Cliente:</b><br>"
                        . "<b style='color:red'>$strTipoElemento Anterior : </b><br>"
                        . "<b>Nombre: </b> ".$objElementoClienteActual->getNombreElemento()."<br>"
                        . "<b>Serie : </b> ".$objElementoClienteActual->getSerieFisica()."<br>"
                        . "<b>Modelo: </b> ".$objElementoClienteActual->getModeloElementoId()->getNombreModeloElemento()."<br>"
                        . "<b style='color:red'>$strTipoElemento Actual : </b><br>"
                        . "<b>Nombre: </b> ".$objElementoCpeNuevo->getNombreElemento()."<br>"
                        . "<b>Serie : </b> ".$objElementoCpeNuevo->getSerieFisica()."<br>"
                        . "<b>Modelo: </b> ".$objElementoCpeNuevo->getModeloElementoId()->getNombreModeloElemento()."<br>";
            $objServicioHistorial = new InfoServicioHistorial();
            $objServicioHistorial->setServicioId($objServicio);
            $objServicioHistorial->setObservacion($strHistorialPorServicio);
            $objServicioHistorial->setEstado($objServicio->getEstado());
            $objServicioHistorial->setUsrCreacion($strUsrCreacion);
            $objServicioHistorial->setFeCreacion(new \DateTime('now'));
            $objServicioHistorial->setIpCreacion($strIpCreacion);
            $this->emComercial->persist($objServicioHistorial);
            $this->emComercial->flush();

            //Se obtienn los detalles de la tarea  de retiro de equipos de bodega
            $arrayParametrosSafe = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getOne('CONFIG TAREAS SAFE ENTRY',
                                                        'COMERCIAL',
                                                        '',
                                                        'CONFIG_TAREA_RETIRO_BODEGA',
                                                        '',
                                                        '',
                                                        '',
                                                        '',
                                                        '',
                                                        $strCodEmpresa);
            
            if(!is_array(($arrayParametrosSafe)))
            {
                throw new Exception('No se ha podido obtener el parametro para configurar la tarea automaticamente');
            }

            $objTarea = $this->emSoporte->getRepository('schemaBundle:AdmiTarea')
                                        ->findOneByNombreTarea($arrayParametrosSafe['valor1']);
            
            if(!isset($objTarea))
            {
                throw new Exception('No se ha encontrado la tarea');
            }
            $objDepartamento = $this->emSoporte->getRepository('schemaBundle:AdmiDepartamento')
                                               ->findOneBy(array('nombreDepartamento' => $arrayParametrosSafe['valor3'],
                                                                 'empresaCod'         => $strCodEmpresa));
            if(!isset($objDepartamento))
            {
                throw new Exception('No se pudo encontar el departamento parametrizado');    
            }
            
            $objInfoPersona =  $this->emComercial->getRepository('schemaBundle:InfoPersona')
                                                 ->findOneBy(array('login'  => $strUsrCreacion,
                                                                   'estado' => 'Activo'));

            //Se crea la tarea
            $arrayParamsTarea = array(
                'strIdEmpresa'          => $strCodEmpresa,
                'strPrefijoEmpresa'     => $strPrefijoEmpresa,
                'strNombreTarea'        => $objTarea->getNombreTarea(),
                'strObservacion'        => $arrayParametrosSafe['valor2'],
                'strNombreDepartamento' => $objDepartamento->getNombreDepartamento(),
                'strEmpleado'           => $objInfoPersona->getNombres().' '.$objInfoPersona->getApellidos() ,
                'strNombreCliente'      => $objCliente->getNombres() ? $objCliente->getNombres().' '.$objCliente->getApellidos()
                                        : $objCliente->getRazonSocial(),
                'strUsrCreacion'        => $strUsrCreacion,
                'strIp'                 => $strIpCreacion,
                'strOrigen'             => 'WEB-TN',
                'strLogin'              => $objPunto->getLogin(),
                'intPuntoId'            => $objPunto->getId(),
                'strValidacionTags'     => 'NO',
                'boolCrearInfoTarea'    => true);
                $arrayRes = $this->serviceSoporte->ingresarTareaInterna($arrayParamsTarea);

            if($arrayRes['status'] !== 'OK')
            {
                throw new Exception($arrayRes['mensaje']);
            }
            //seteo respuesta
            $strStatus = 'OK';
            $strMensaje = 'Se ha realizado el cambio de equipo correctamente';
        }
        catch(\Exception $e)
        {
            $strStatus = 'ERROR';
            $strMensaje = 'No se ha realizado el cambio de equipo';
            $this->serviceUtil->insertError('Telcos+', 'cambioElementoTnSegVehiculo', $e->getMessage(),
                                            $strUsrCreacion, $strIpCreacion);
        }
        return array(array('status' => $strStatus, 'mensaje' => $strMensaje));
    }
}
