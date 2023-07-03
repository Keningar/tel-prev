<?php

namespace telconet\comercialBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoDetalle;
use telconet\schemaBundle\Entity\InfoDetalleAsignacion;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoDetalleSolCaract;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
/**
 * InfoDetalleSolicitud controller.
 *
 */
class SolicitudCambioModemInmediatoController extends Controller implements TokenAuthenticatedController
{
    //Se definen las constantes de la clase.
    const FACTURACION_CAMBIO_MODEM_INMEDIATO  = 'FACTURACION_CAMBIO_MODEM_INMEDIATO';

    private $tipoSolicitud='SOLICITUD CAMBIO DE MODEM INMEDIATO';
    //private $relacion_sistema_id=2561;
    private $relacion_sistema_id=2681;
    
    /**
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.1
     * @since 31-08-2018
     * Se envía a la vista si la empresa en sesión aplica al proceso de facturación para cambio de módem inmediato.
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.2 25-10-2021 - Se valida que si el punto contiene servicios en la red GPON_MPLS
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.3 03-11-2022 - Se agrega la validación para los servicios Seg Vehículo.
     * 
     */
    public function indexAction(){
        $serviceUtil = $this->get('schema.Util');
        $request = $this->getRequest();
        $session  = $request->getSession();
        $puntoIdSesion=null;
        $emComercial = $this->getDoctrine()->getManager('telconet');
        $emfn = $this->getDoctrine()->getManager('telconet_financiero');                
        $ptoCliente_sesion=$session->get('ptoCliente');
        $valor=0;
        $strEsPuntoGpon = "N";
        if($ptoCliente_sesion){  
            $puntoIdSesion=$ptoCliente_sesion['id'];
            $saldoarr=$emfn->getRepository('schemaBundle:InfoPagoCab')->obtieneSaldoPorPunto($puntoIdSesion);
            $valor=$valor+$saldoarr[0]['saldo']; 	                        
            //verificar si el punto es GPON_MPLS
            $serviceTecnico = $this->get('tecnico.InfoServicioTecnico');
            $objPunto = $emComercial->getRepository('schemaBundle:InfoPunto')->find($puntoIdSesion);
            $arrayParametros['objPunto']       = $objPunto;
            $arrayParametros['strParametro']   = "PRODUCTO_PRINCIPAL";
            $arrayParametros['strCodEmpresa']  = $session->get('idEmpresa');
            $arrayParametros['strUsrCreacion'] = $session->get('user');
            $arrayParametros['strIpCreacion']  = $request->getClientIp();
            $arrayResultServicioOnt = $serviceTecnico->getServicioGponPorProducto($arrayParametros);
            if($arrayResultServicioOnt['status'] == "OK" && is_object($arrayResultServicioOnt['objServicio']))
            {
                $strEsPuntoGpon = "S";
            }
            //validar servicios seg vehiculo
            //obtener estados no permitidos
            $arrayEstadosNoPermitidos = array();
            $arrayParametrosEstados   = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->get('PARAMETROS_SEG_VEHICULOS',
                                                            'TECNICO',
                                                            '',
                                                            '',
                                                            'ESTADOS_SERVICIOS_NO_PERMITIDOS_FLUJO',
                                                            '',
                                                            '',
                                                            '',
                                                            '');
            foreach($arrayParametrosEstados as $arrayDetalles)
            {
                $arrayEstadosNoPermitidos[] = $arrayDetalles['valor2'];
            }
            //obtener id del producto
            $arrayParIdProd = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                             ->getOne('PARAMETROS_SEG_VEHICULOS',
                                                                      'TECNICO',
                                                                      '',
                                                                      'PRODUCTO_ID',
                                                                      'MOBILE BUS',
                                                                      '',
                                                                      '',
                                                                      '',
                                                                      '',
                                                                      $session->get('idEmpresa'));
            if(isset($arrayParIdProd) && !empty($arrayParIdProd)
               && isset($arrayParIdProd['valor2']) && !empty($arrayParIdProd['valor2']))
            {
                //obtengo el servicio
                $objServicioSegVeh = $emComercial->getRepository('schemaBundle:InfoServicio')
                                            ->createQueryBuilder('s')
                                            ->where("s.puntoId = :puntoId")
                                            ->andWhere("s.productoId = :productoId")
                                            ->andWhere("s.estado NOT IN (:estados)")
                                            ->setParameter('puntoId', $objPunto->getId())
                                            ->setParameter('productoId', $arrayParIdProd['valor2'])
                                            ->setParameter('estados', array_values($arrayEstadosNoPermitidos))
                                            ->setMaxResults(1)
                                            ->getQuery()
                                            ->getOneOrNullResult();
                if(is_object($objServicioSegVeh))
                {
                    $strEsPuntoGpon = "S";
                }
            }
        }
//        $entities = $em->getRepository('schemaBundle:InfoDetalleSolicitud')->findAll();
	
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("62", "1"); 
        $entityItemMenuPadre = $entityItemMenu->getItemMenuId(); 
		$session->set('menu_modulo_activo', $entityItemMenuPadre->getNombreItemMenu());
		$session->set('nombre_menu_modulo_activo', $entityItemMenuPadre->getTitleHtml());
		$session->set('id_menu_modulo_activo', $entityItemMenuPadre->getId());
		$session->set('imagen_menu_modulo_activo', $entityItemMenuPadre->getUrlImagen());  
		
        $strAplicaFacturacionCambioModem = $serviceUtil->empresaAplicaProceso(array("strProcesoAccion" => self::FACTURACION_CAMBIO_MODEM_INMEDIATO,
                                                                                    "strEmpresaCod"    => $session->get('idEmpresa')));

        return $this->render('comercialBundle:solicitudcambiomodeminmediato:index.html.twig', array(
            'item' => $entityItemMenu,
            'aplicaFacturacionCambioModem' => $strAplicaFacturacionCambioModem,
            'entities' => '',
            'puntoId' => $puntoIdSesion,
            'strEsPuntoGpon'=>$strEsPuntoGpon,
            'deuda'=>$valor
        ));
    }

    /**
     * grabaSolicitudCambioModemInmediatoMD_ajaxAction
     *
     * @return arreglo con listado de documentos
     *
     * @version 1.0
     * 
     * @author Modificado: Duval Medina C. <dmedina@telconet.ec>
     * @version 1.1 2016-06-28 Guardar error en Info_Error y no presentarlo
     *                        Remover variables no usadas
     * 
     * @author Modificado: Jesus Bozada <jbozada@telconet.ec>
     * @version 1.2 10-10-2016 Se agregan parametros utilizados para cambios de equipo de 
     *                         servicios migrados previamente de Tellion a Huawei
     * @since 1.1
     * 
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.3 18-07-2017 Se realizan ajustes para validar por cada servicio  si tiene una solicitud de Demo Activa,se 
     *                         registre el cambio de equipo
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.4 21-12-2017 - En la tabla INFO_DETALLE_ASIGNACION se registra el campo tipo asignado 'EMPLEADO'
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.5 14-01-2019 Se agrega restricción de creación de solicitudes por cambio de modem inmediato para aquellos logines que tenga creada
     *                         una solicitud de agregar equipo con Wifi y Extender Dual Band y se cambia la respuesta que retorna
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.6 05-05-2020 - Se agrega el tipo de solicitud: 'SOLICITUD AGREGAR EQUIPO MASIVO'
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.7 25-10-2021 - Se valida el punto en la red GPON_MPLS y se genera la tarea a la cuadrilla asignada
     */
    public function grabaSolicitudCambioModemInmediatoMD_ajaxAction() 
    {
        $request     = $this->getRequest();
        $session     = $request->getSession();
        $usrCreacion = $session->get('user');
        
        $em        = $this->getDoctrine()->getManager('telconet');
        $emSoporte = $this->getDoctrine()->getManager('telconet_soporte');
        $emGeneral = $this->getDoctrine()->getManager('telconet_general');

        //Obtiene parametros enviados desde el ajax
        $peticion        = $this->get('request');
        $parametro       = $peticion->get('param');
        $array_valor     = explode("|", $parametro);
        $motivoId        = $peticion->get('motivoId');
        $tipoSolicitudId = $peticion->get('ts');
        $tipoDocumento   = $peticion->get('td');
        $valor           = $peticion->get('valor');
        $obs             = $peticion->get('obs');
        $strEquipoHwCpe  = $peticion->get('equipoCpe');
        $strEquipoHwWifi = $peticion->get('equipoWifi');
        $strAgregarWifi  = $peticion->get('agregarWifi');
        $strElementoWifi = $peticion->get('elementoWifi');
        $strEsPuntoGpon  = $peticion->get('strEsPuntoGpon') ? $peticion->get('strEsPuntoGpon') : "N";
        $serviceUtil     = $this->get('schema.Util');
        $boolRespuesta   = false;
        $boolMsjUsuario  = false;
        $strMsjUsuario   = "";

        $em->getConnection()->beginTransaction();
        $emSoporte->getConnection()->beginTransaction();
        try 
        {
            $dato_arr = array();
            foreach ($array_valor as $idServElemt)
            {
                $dato       = explode("@", $idServElemt);
                $dato_arr[] = array($dato[0] * 1,$dato[1]);
            }

            $arr = array();
            foreach($dato_arr as $key => $item)
            {
               $arr[$item[0]][$key] = $item[1];
            }
            ksort($arr, SORT_NUMERIC);
            
            
            //Se obtiene el motivo ingresado
            $objAdmiMotivo = $emGeneral->getRepository('schemaBundle:AdmiMotivo')->find($motivoId);
            
            if(is_object($objAdmiMotivo))
            {
                $strNombreMotivo = $objAdmiMotivo->getNombreMotivo();
            }
            
            //Se obtiene la caracteristica de Cancelacion de Demo
            $objCaractCambioEquipoDemo = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                            ->findOneBy(array("descripcionCaracteristica" => 'Cambio Equipo Demo'));            
		
            $entityTipoSolicitud      = $em->getRepository('schemaBundle:AdmiTipoSolicitud')->find($tipoSolicitudId);
            $entityAdmiCaracteristica = $em->getRepository('schemaBundle:AdmiCaracteristica')->find(360);
            //se recuperan caracteristicas necesarias para cambios de equipos Hw
            $objCaracteristicaHwCpe   = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                           ->findOneBy(
                                                       array('descripcionCaracteristica' => 'EQUIPO HW CPE',
                                                             'estado'                    => 'Activo'
                                                            )
                                                      );
            $serviceUtil->validaObjeto($objCaracteristicaHwCpe, "No existe caracteristica EQUIPO HW CPE.");
            
            $objCaracteristicaHwWifi = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                          ->findOneBy(
                                                      array('descripcionCaracteristica' => 'EQUIPO HW WIFI',
                                                            'estado'                    => 'Activo'
                                                           )
                                                     );
            $serviceUtil->validaObjeto($objCaracteristicaHwWifi, "No existe caracteristica EQUIPO HW WIFI.");
            
            $objCaracteristicaAgregarWifi = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                               ->findOneBy(
                                                           array('descripcionCaracteristica' => 'AGREGAR WIFI',
                                                                 'estado'                    => 'Activo'
                                                                )
                                                          );
            $serviceUtil->validaObjeto($objCaracteristicaAgregarWifi, "No existe caracteristica AGREGAR WIFI.");
            
            $objCaracteristicaElementoWifi = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                               ->findOneBy(
                                                           array('descripcionCaracteristica' => 'ELEMENTO WIFI',
                                                                 'estado'                    => 'Activo'
                                                                )
                                                          );
            $serviceUtil->validaObjeto($objCaracteristicaElementoWifi, "No existe caracteristica ELEMENTO WIFI.");
            
            $objCaractSolicitudMigracion  = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                               ->findOneBy(
                                                           array('descripcionCaracteristica' => 'SOLICITUD MIGRACION HW',
                                                                 'estado'                    => 'Activo'
                                                                )
                                                          );
            $serviceUtil->validaObjeto($objCaractSolicitudMigracion, "No existe caracteristica SOLICITUD MIGRACION HW.");

            //arreglo de los detalles de tarea
            $arrayInfoDetalle = array();
            //$arr contiene los elementos seleccionados agrupados por sus servicios
            //continuamos con el proceso normal
            foreach ($arr as $id => $elementos):
                $boolCambioNoPermitido          = false;
                $strMensajeCambioNoPermitido    = "";
                $entityServicio         = $em->getRepository('schemaBundle:InfoServicio')->find($id);
                if(is_object($entityServicio))
                {
                    $objTipoSolicitudAgregarEquipo  = $em->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                         ->findOneByDescripcionSolicitud('SOLICITUD AGREGAR EQUIPO');
                    $objSolicitudAgregarEquipo      = $em->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                         ->findOneBy(array( "servicioId"        => $entityServicio->getId(),
                                                                            "tipoSolicitudId"   => $objTipoSolicitudAgregarEquipo->getId(),
                                                                            "estado"            => array("PrePlanificada",
                                                                                                         "Detenido",
                                                                                                         "Replanificada",
                                                                                                         "Planificada",
                                                                                                         "Asignada")));

                    $objTipoSolicitudAgregarEquipoMasivo  = $em->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                               ->findOneByDescripcionSolicitud('SOLICITUD AGREGAR EQUIPO MASIVO');

                    $objSolicitudAgregarEquipoMasivo      = $em->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                               ->findOneBy(array("servicioId"        => $entityServicio->getId(),
                                                                                 "tipoSolicitudId"   => $objTipoSolicitudAgregarEquipoMasivo->getId(),
                                                                                 "estado"            => array("PrePlanificada",
                                                                                                              "Detenido",
                                                                                                              "Replanificada",
                                                                                                              "Planificada",
                                                                                                              "Asignada")));

                    if(is_object($objSolicitudAgregarEquipo))
                    {
                        $strEstadoSolicitud             = $objSolicitudAgregarEquipo->getEstado();
                        $objCaracteristicaWifiDualBand  = $em->getRepository("schemaBundle:AdmiCaracteristica")
                                                             ->findOneBy(array('descripcionCaracteristica' => 'WIFI DUAL BAND',
                                                                               'estado'                    => 'Activo'));
                        if(is_object($objCaracteristicaWifiDualBand))
                        {
                            $objDetalleSolCaractWifiDualBand = $em->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                  ->findOneBy(
                                                                                array(
                                                                                      "detalleSolicitudId"  => $objSolicitudAgregarEquipo,
                                                                                      "caracteristicaId"    => $objCaracteristicaWifiDualBand,
                                                                                      "estado"              => $strEstadoSolicitud
                                                                                     )
                                                                             );
                            if(is_object($objDetalleSolCaractWifiDualBand))
                            {
                                $boolCambioNoPermitido          = true;
                                $strMensajeCambioNoPermitido    .= "<br>Realice el Cambio de Wifi Estándar a Wifi Dual Band.";
                            }
                        }
                        
                        $objCaracteristicaExtenderDualBand  = $em->getRepository("schemaBundle:AdmiCaracteristica")
                                                                 ->findOneBy(array( 'descripcionCaracteristica' => 'EXTENDER DUAL BAND',
                                                                                    'estado'                    => 'Activo'));
                        if(is_object($objCaracteristicaExtenderDualBand))
                        {
                            $objDetalleSolCaractExtenderDualBand = $em->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                      ->findOneBy(
                                                                                    array(
                                                                                          "detalleSolicitudId"  => $objSolicitudAgregarEquipo,
                                                                                          "caracteristicaId"    => $objCaracteristicaExtenderDualBand,
                                                                                          "estado"              => $strEstadoSolicitud
                                                                                         )
                                                                                 );
                            if(is_object($objDetalleSolCaractExtenderDualBand))
                            {
                                $boolCambioNoPermitido          = true;
                                $strMensajeCambioNoPermitido    .= "<br>Agregue el equipo Extender Dual Band.";
                            }
                        }
                        
                        if($boolCambioNoPermitido)
                        {
                            $boolMsjUsuario = true;
                            throw new \Exception("<b style='color: red;'>Error: No se ha podido crear la solicitud.</b>"
                                                . "<br>Por favor finalice la gestión de la SOLICITUD AGREGAR EQUIPO asociada al servicio,"
                                                . " ya que aún tiene las siguientes acciones sin finalizar: ".$strMensajeCambioNoPermitido);
                        }
                    }

                    if(is_object($objSolicitudAgregarEquipoMasivo))
                    {
                        $strEstadoSolicitud             = $objSolicitudAgregarEquipoMasivo->getEstado();
                        $objCaracteristicaWifiDualBand  = $em->getRepository("schemaBundle:AdmiCaracteristica")
                                                             ->findOneBy(array('descripcionCaracteristica' => 'WIFI DUAL BAND',
                                                                               'estado'                    => 'Activo'));
                        if(is_object($objCaracteristicaWifiDualBand))
                        {
                            $objDetalleSolCaractWifiDualBand = $em->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                  ->findOneBy(
                                                                                array(
                                                                                      "detalleSolicitudId"  => $objSolicitudAgregarEquipoMasivo,
                                                                                      "caracteristicaId"    => $objCaracteristicaWifiDualBand,
                                                                                      "estado"              => $strEstadoSolicitud
                                                                                     )
                                                                             );
                            if(is_object($objDetalleSolCaractWifiDualBand))
                            {
                                $boolCambioNoPermitido          = true;
                                $strMensajeCambioNoPermitido    .= "<br>Realice el Cambio de Wifi Estándar a Wifi Dual Band.";
                            }
                        }
                        
                        $objCaracteristicaExtenderDualBand  = $em->getRepository("schemaBundle:AdmiCaracteristica")
                                                                 ->findOneBy(array( 'descripcionCaracteristica' => 'EXTENDER DUAL BAND',
                                                                                    'estado'                    => 'Activo'));
                        if(is_object($objCaracteristicaExtenderDualBand))
                        {
                            $objDetalleSolCaractExtenderDualBand = $em->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                      ->findOneBy(
                                                                                    array(
                                                                                          "detalleSolicitudId"  => $objSolicitudAgregarEquipoMasivo,
                                                                                          "caracteristicaId"    => $objCaracteristicaExtenderDualBand,
                                                                                          "estado"              => $strEstadoSolicitud
                                                                                         )
                                                                                 );
                            if(is_object($objDetalleSolCaractExtenderDualBand))
                            {
                                $boolCambioNoPermitido          = true;
                                $strMensajeCambioNoPermitido    .= "<br>Agregue el equipo Extender Dual Band.";
                            }
                        }

                        if($boolCambioNoPermitido)
                        {
                            $boolMsjUsuario = true;
                            throw new \Exception("<b style='color: red;'>Error: No se ha podido crear la solicitud.</b>"
                                                . "<br>Por favor finalice la gestión de la SOLICITUD AGREGAR EQUIPO MASIVO asociada al servicio,"
                                                . " ya que aún tiene las siguientes acciones sin finalizar: ".$strMensajeCambioNoPermitido);
                        }
                    }
                }
                //Grabamos en la tabla Caracteristicas de la solicitud por cada elemento
                foreach($elementos as $elem) 
                {
                    //Se valida si el servicio tiene solicitud de Demo Activa
                    if($strNombreMotivo == "DEMO" && is_object($entityServicio))
                    {                          
                        $arrayEstados                        = array("Pendiente","Aprobada","EnProceso");
                        $arrayParametrosSol["arrayEstados"]  = $arrayEstados;
                        $arrayParametrosSol["strSolicitud"]  = "DEMOS";
                        $arrayParametrosSol["intServicioId"] = $entityServicio->getId();

                        $intSolicitudesDemo = $em->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                 ->getSolicitudDemoAbiertas($arrayParametrosSol);

                        if($intSolicitudesDemo > 0)
                        {
                            //Se obtiene el estado de la Solicitud
                            $strEstadoSolDemo = $em->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                   ->getEstadoSolDemo($arrayParametrosSol);

                            //Validar si el servicio tiene una solicitud de Demos en estado Activa
                            $arrayParametrosDemo["intServicio"]  = $entityServicio->getId();
                            $arrayParametrosDemo["strSolicitud"] = "DEMOS";
                            $arrayParametrosDemo["strEstado"]    = $strEstadoSolDemo;

                            $intSolicitudActiva = $em->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                     ->getSolicitudActivaPorServicio($arrayParametrosDemo);

                            if(($intSolicitudActiva != "") && (is_object($objCaractCambioEquipoDemo)))
                            {
                                $objInfoDetalleSolCaract = $em->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                              ->findOneBy(array("detalleSolicitudId" => $intSolicitudActiva,
                                                                                "caracteristicaId"   => $objCaractCambioEquipoDemo->getId()));

                                if(is_object($objInfoDetalleSolCaract))
                                {
                                    //Actualizo la caracteristica de cambio de equipo
                                    $objInfoDetalleSolCaract->setValor("S");
                                    $objInfoDetalleSolCaract->setFeUltMod(new \DateTime('now'));
                                    $em->persist($objInfoDetalleSolCaract);
                                    $em->flush();
                                }
                            }
                        }
                    }
                                        
                    $entity = new InfoDetalleSolicitud();
                    $entity->setMotivoId($motivoId);                
                    $entity->setServicioId($entityServicio);
                    $entity->setTipoSolicitudId($entityTipoSolicitud);
                    if($tipoDocumento=='v')
                    {
                        $entity->setTipoDocumento('V');
                    }
                    elseif($tipoDocumento=='c')
                    {
                        $entity->setTipoDocumento('C');
                    }
                    $entity->setObservacion($obs);
                    $entity->setPrecioDescuento($valor);
                    $entity->setFeCreacion(new \DateTime('now'));
                    $entity->setUsrCreacion($usrCreacion);
                    $entity->setEstado('AsignadoTarea');
                    $em->persist($entity);
                    $em->flush();


                    $entityCaract= new InfoDetalleSolCaract();
                    $entityCaract->setCaracteristicaId($entityAdmiCaracteristica);
                    $entityCaract->setDetalleSolicitudId($entity);
                    $entityCaract->setValor($elem);
                    $entityCaract->setEstado("AsignadoTarea");
                    $entityCaract->setUsrCreacion($usrCreacion);
                    $entityCaract->setFeCreacion(new \DateTime('now'));
                    $em->persist($entityCaract);
                    $em->flush();
                    //se almacenan caracteristicas necesarias para cambios de equipos Hw
                    if ($strEquipoHwCpe || $strEquipoHwWifi || $strAgregarWifi || $strElementoWifi)
                    {
                        $objSolCaractSolMigracion = new InfoDetalleSolCaract();
                        $objSolCaractSolMigracion->setCaracteristicaId($objCaractSolicitudMigracion);
                        $objSolCaractSolMigracion->setDetalleSolicitudId($entity);
                        $objSolCaractSolMigracion->setValor("SI");
                        $objSolCaractSolMigracion->setEstado("AsignadoTarea");
                        $objSolCaractSolMigracion->setUsrCreacion($usrCreacion);
                        $objSolCaractSolMigracion->setFeCreacion(new \DateTime('now'));
                        $em->persist($objSolCaractSolMigracion);
                        $em->flush();
                    }
                    
                    if($strEquipoHwCpe)
                    {
                        $objSolCaractHwCpe = new InfoDetalleSolCaract();
                        $objSolCaractHwCpe->setCaracteristicaId($objCaracteristicaHwCpe);
                        $objSolCaractHwCpe->setDetalleSolicitudId($entity);
                        $objSolCaractHwCpe->setValor($strEquipoHwCpe);
                        $objSolCaractHwCpe->setEstado("AsignadoTarea");
                        $objSolCaractHwCpe->setUsrCreacion($usrCreacion);
                        $objSolCaractHwCpe->setFeCreacion(new \DateTime('now'));
                        $em->persist($objSolCaractHwCpe);
                        $em->flush();
                    }
                    
                    if($strEquipoHwWifi)
                    {
                        $objSolCaractHwWifi = new InfoDetalleSolCaract();
                        $objSolCaractHwWifi->setCaracteristicaId($objCaracteristicaHwWifi);
                        $objSolCaractHwWifi->setDetalleSolicitudId($entity);
                        $objSolCaractHwWifi->setValor($strEquipoHwWifi);
                        $objSolCaractHwWifi->setEstado("AsignadoTarea");
                        $objSolCaractHwWifi->setUsrCreacion($usrCreacion);
                        $objSolCaractHwWifi->setFeCreacion(new \DateTime('now'));
                        $em->persist($objSolCaractHwWifi);
                        $em->flush();
                    }
                    
                    if ($strAgregarWifi)
                    {
                        $objSolCaractAgregarWifi = new InfoDetalleSolCaract();
                        $objSolCaractAgregarWifi->setCaracteristicaId($objCaracteristicaAgregarWifi);
                        $objSolCaractAgregarWifi->setDetalleSolicitudId($entity);
                        $objSolCaractAgregarWifi->setValor($strAgregarWifi);
                        $objSolCaractAgregarWifi->setEstado("AsignadoTarea");
                        $objSolCaractAgregarWifi->setUsrCreacion($usrCreacion);
                        $objSolCaractAgregarWifi->setFeCreacion(new \DateTime('now'));
                        $em->persist($objSolCaractAgregarWifi);
                        $em->flush();
                    }
                    
                    if ($strElementoWifi)
                    {
                        $objSolCaractAgregarWifi = new InfoDetalleSolCaract();
                        $objSolCaractAgregarWifi->setCaracteristicaId($objCaracteristicaElementoWifi);
                        $objSolCaractAgregarWifi->setDetalleSolicitudId($entity);
                        $objSolCaractAgregarWifi->setValor($strElementoWifi);
                        $objSolCaractAgregarWifi->setEstado("AsignadoTarea");
                        $objSolCaractAgregarWifi->setUsrCreacion($usrCreacion);
                        $objSolCaractAgregarWifi->setFeCreacion(new \DateTime('now'));
                        $em->persist($objSolCaractAgregarWifi);
                        $em->flush();
                    }

                    //Grabamos en la tabla de historial de la solicitud
                    $entityHistorial= new InfoDetalleSolHist();
                    $entityHistorial->setEstado('AsignadoTarea');
                    $entityHistorial->setDetalleSolicitudId($entity);
                    $entityHistorial->setUsrCreacion($usrCreacion);
                    $entityHistorial->setFeCreacion(new \DateTime('now'));
                    $entityHistorial->setIpCreacion($request->getClientIp());
                    $entityHistorial->setMotivoId($motivoId);
                    $entityHistorial->setObservacion($obs);
                    $em->persist($entityHistorial);
                    $em->flush();

                    //verificar si el punto es GPON_MPLS
                    if($strEsPuntoGpon === "S")
                    {
                        $serviceTecnico = $this->get('tecnico.InfoServicioTecnico');
                        $arrayParametros['objServicio']            = $entityServicio;
                        $arrayParametros['emSoporte']              = $emSoporte;
                        $arrayParametros['objDetalleSolicitud']    = $entity;
                        $arrayParametros['strPersonaEmpresaRolId'] = $peticion->get('strPersonaEmpresaRolId');
                        $arrayParametros['intIdCuadrilla']         = $peticion->get('intIdCuadrilla');
                        $arrayParametros['intIdDepartamento']      = $session->get('idDepartamento');
                        $arrayParametros['strCodEmpresa']          = $session->get('idEmpresa');
                        $arrayParametros['strUsrCreacion']         = $session->get('user');
                        $arrayParametros['strIpCreacion']          = $request->getClientIp();
                        $arrayResultTarea = $serviceTecnico->generarTareaCambioEquipoGponTN($arrayParametros);
                        if($arrayResultTarea['status'] != "OK")
                        {
                            throw new \Exception($arrayResultTarea['mensaje']);
                        }
                        //se guarda el detalle de la tarea en el arreglo
                        $arrayInfoDetalle[] = $arrayResultTarea['objDetalle'];
                        //historial de la solicitud
                        $objHistorialSolicitud = new InfoDetalleSolHist();
                        $objHistorialSolicitud->setDetalleSolicitudId($entity);
                        $objHistorialSolicitud->setEstado($entity->getEstado());
                        $objHistorialSolicitud->setObservacion($arrayResultTarea['mensaje']);
                        $objHistorialSolicitud->setUsrCreacion($usrCreacion);
                        $objHistorialSolicitud->setFeCreacion(new \DateTime('now'));
                        $objHistorialSolicitud->setIpCreacion($request->getClientIp());
                        $em->persist($objHistorialSolicitud);
                        $em->flush();
                        //historial de la solicitud
                        $objHistorialServicio = new InfoServicioHistorial();
                        $objHistorialServicio->setServicioId($entityServicio);
                        $objHistorialServicio->setEstado($entityServicio->getEstado());
                        $objHistorialServicio->setObservacion($arrayResultTarea['mensaje']);
                        $objHistorialServicio->setUsrCreacion($usrCreacion);
                        $objHistorialServicio->setFeCreacion(new \DateTime('now'));
                        $objHistorialServicio->setIpCreacion($request->getClientIp());
                        $em->persist($objHistorialServicio);
                        $em->flush();
                    }
                    else
                    {
                        //obtener tarea
                        $entityProceso = $emSoporte->getRepository('schemaBundle:AdmiProceso')
                                                   ->findOneByNombreProceso("SOLICITAR CAMBIO EQUIPO");
                        $entityTareas  = $emSoporte->getRepository('schemaBundle:AdmiTarea')
                                                   ->findTareasActivasByProceso($entityProceso->getId());
                        $entityTarea   = $entityTareas[0];

                        //grabamos soporte.info_detalle
                        $detalle = new InfoDetalle();
                        $detalle->setTareaId($entityTarea);
                        $detalle->setPesoPresupuestado(0);
                        $detalle->setValorPresupuestado(0);
                        $detalle->setDetalleSolicitudId($entity->getId());
                        $detalle->setUsrCreacion($usrCreacion);
                        $detalle->setFeCreacion(new \DateTime('now'));
                        $detalle->setIpCreacion($request->getClientIp());
                        $emSoporte->persist($detalle);
                        $emSoporte->flush();

                        //obtenemos el persona empresa rol del usuario
                        $personaEmpresaRol = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                ->find($session->get('idPersonaEmpresaRol'));

                        //buscamos datos del dept, persona
                        $departamento = $emGeneral->getRepository('schemaBundle:AdmiDepartamento')
                                                  ->find($personaEmpresaRol->getDepartamentoId());
                        $persona = $personaEmpresaRol->getPersonaId();

                        //grabamos soporte.info_detalle_asignacion
                        $detalleAsignacion = new InfoDetalleAsignacion();
                        $detalleAsignacion->setDetalleId($detalle);
                        $detalleAsignacion->setAsignadoId($departamento->getId());
                        $detalleAsignacion->setAsignadoNombre($departamento->getNombreDepartamento());
                        $detalleAsignacion->setRefAsignadoId($persona->getId());
                        $nombre = $persona->getRazonSocial();
                        if($nombre=="")
                        {
                            $nombre = $persona->getNombres()." ".$persona->getApellidos();
                        }
                        $detalleAsignacion->setRefAsignadoNombre($nombre);
                        $detalleAsignacion->setPersonaEmpresaRolId($personaEmpresaRol->getId());
                        $detalleAsignacion->setTipoAsignado("EMPLEADO");
                        $detalleAsignacion->setUsrCreacion($usrCreacion);
                        $detalleAsignacion->setFeCreacion(new \DateTime('now'));
                        $detalleAsignacion->setIpCreacion($request->getClientIp());
                        $emSoporte->persist($detalleAsignacion);
                        $emSoporte->flush();
                    }
                }
            endforeach;
            $boolRespuesta = true;	
            $emSoporte->commit();
            $em->commit();
            //actualizar info tarea
            if(!empty($arrayInfoDetalle) && is_array($arrayInfoDetalle))
            {
                $serviceSoporte = $this->get('soporte.SoporteService');
                foreach($arrayInfoDetalle as $objDetalle)
                {
                    //actualizar info tarea
                    $arrayParametrosInfoTarea['intDetalleId'] = $objDetalle->getId();
                    $arrayParametrosInfoTarea['strUsrUltMod'] = $usrCreacion;
                    $serviceSoporte->actualizarInfoTarea($arrayParametrosInfoTarea);
                }
            }
        }
        catch (\Exception $e) 
        {
            if ($emSoporte->getConnection()->isTransactionActive())
            {
                $emSoporte->getConnection()->rollback();
            }
            if ($em->getConnection()->isTransactionActive())
            {
                $em->getConnection()->rollback();
            }
            $emSoporte->getConnection()->close();
            $em->getConnection()->close();
            $serviceUtil->insertError('Telcos+', 
                                      'SolicitudCambioModemInmediatoController.grabaSolicitudCambioModemInmediatoMD_ajaxAction', 
                                      $e->getMessage(), 
                                      $usrCreacion, 
                                      $request->getClientIp());
            $boolRespuesta = false;
            if($boolMsjUsuario)
            {
                $strMsjUsuario = $e->getMessage();
            }
        }
        $arrayRespuesta = array("boolRespuesta" => $boolRespuesta,
                                "strMsjUsuario" => $strMsjUsuario);
        return $arrayRespuesta;
    } 
    
    /**
     * grabaSolicitudCambioModemInmediato_ajaxAction
     * 
     * @author Modificado: Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 10-10-2016 Se modifica mensaje de respuesta devuelto a usuario
     * @since 1.0 Version Inicial
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 14-01-2019 Se modifica la obtención del resultado de cambio de modem y el mensaje del usuario para mostrarlo en pantalla 
     * 
     * @return Response $respuesta    Objeto Response con mensaje a mostrar al usuario final
     */
    public function grabaSolicitudCambioModemInmediato_ajaxAction() 
    {
        ini_set('max_execution_time', 400000);
        $respuesta     = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $respuesta->setContent("error del Form");
        $arrayRespuestaCambio   = $this->grabaSolicitudCambioModemInmediatoMD_ajaxAction();
        $boolResultado          = $arrayRespuestaCambio["boolRespuesta"];
        if ($boolResultado)
        {
            $respuesta->setContent("Se registro solicitud con exito.");
        }
        else
        {
            if(!empty($arrayRespuestaCambio["strMsjUsuario"]))
            {
                $strMsjUsuario  = $arrayRespuestaCambio["strMsjUsuario"];
            }
            else
            {
                $strMsjUsuario  = "Error al tratar de guardar solicitud. Consulte con el Administrador.";
            }
            $respuesta->setContent($strMsjUsuario);
        }
        return $respuesta;
    } 
    
    /**
     * getMotivos_ajaxAction
     * 
     * Función que obtiene los motivos para las solicitudes de cambio de módem inmediato
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 24-09-2020
     * 
     * @return  Object  $objResponse Objeto donde se almacena el resultado 
     */
    public function getMotivos_ajaxAction()
    {
        $emGeneral                              = $this->get('doctrine')->getManager('telconet_general');
        $emComercial                            = $this->get('doctrine')->getManager('telconet');
        $arrayDataMotivos                       = $emGeneral->getRepository('schemaBundle:AdmiMotivo')->loadMotivos($this->relacion_sistema_id);
        $entityAdmiTipoSolicitud                = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                              ->findByDescripcionSolicitud($this->tipoSolicitud);
        $objRequest                             = $this->getRequest();
        $objSession                             = $objRequest->getSession();
        $strCodEmpresa                          = $objSession->get("idEmpresa") ? $objSession->get("idEmpresa") : '';
        $arrayMotivosParametrizados             = array();
        $arrayParamsMotivosSolCambioModemAut    = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->get( 'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD',
                                                                    '',
                                                                    '',
                                                                    '',
                                                                    'MOTIVOS_CAMBIO_MODEM_INMEDIATO_AUTOMATICO',
                                                                    '',
                                                                    '',
                                                                    '',
                                                                    '',
                                                                    $strCodEmpresa);
        if(isset($arrayParamsMotivosSolCambioModemAut) && !empty($arrayParamsMotivosSolCambioModemAut))
        {
            foreach($arrayParamsMotivosSolCambioModemAut as $arrayMotivoSolCambioModemAut)
            {
                $arrayMotivosParametrizados[] = $arrayMotivoSolCambioModemAut["valor4"];
            }
        }
        
        $arrayMotivos = array();
        foreach($arrayDataMotivos as $objMotivo)
        {
            if(!in_array($objMotivo->getNombreMotivo(), $arrayMotivosParametrizados))
            {
                $arrayMotivos[] = array(
                                            'idMotivo'          => $objMotivo->getId(),
                                            'descripcion'       => $objMotivo->getNombreMotivo(),
                                            'idRelacionSistema' => $objMotivo->getRelacionSistemaId(),
                                            'idTipoSolicitud'   => $entityAdmiTipoSolicitud[0]->getId()
                                        );
            }
        }
        $objResponse = new Response(json_encode(array('motivos' => $arrayMotivos)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
     * 
     * Metodo que consulta los servicios ligados al punto enviado como parametro para efecto de seleccion de cambio de modem inmediato
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 - Se modifica para que bloquee los registros de los servicios que sean dependiente de un mismo cpe y no permita crear mas de
     *                una solicitud redundante sobre un mismo equipo
     * @since 20-10-2016
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.2   Se agrega codigo para mostrar servicios de productos SmartWifi Renta en el grid de elementos disponibles a cambiar
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.3 22-11-2018 Se requiere que aparezca el servicio de COU LINEAS TELEFONIA FIJA en la pantalla de solicitud de cambio de modem inmediato
     * 
     * @since 1.1
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.3   Se agregan validaciones por creación de nuevo producto AP WIFI
     * @since 1.2
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.4  20-12-2018 Se modifica nombre de variable en condición que se utiliza para listar los elementos en el grid de 
     *                          cambio de modem inmediato
     * @since 1.3
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.5 03-12-2018 Se agregan validaciones para producto Extender Dual Band
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.6 25-01-2019 - Se realizan ajustes para ingresar información de los equipos de seguridad lógica
     * @since 1.5
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.7 07-11-2019 | Se realizan ajustes para poder incluir al servicio Wifi Alquiler Equipos.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.8 01-02-2021 Se agregan validaciones para permitir cambiar el Extender Dual Band en los productos W+AP
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.9 25-10-2021 - Se envía el parámetro del tipo de red para validar los servicios en la red GPON_MPLS
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 2.0 03-11-2022 - Se realiza la validación para obtener los elementos de los servicios con producto SEG_VEHICULO.
     * 
     * @author Leonardo Mero <lemero@telconet.ec>
     * @version 2.1 09-12-2022 - Se agrega la validacion para obtener los equipos del producto SAFE ENTRY
     * 
     * @param integer $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getServiciosParaSolicitudCambioModemInmediato_ajaxAction($id) 
    {
        $respuesta                    = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $request                      = $this->getRequest();
        $session                      = $request->getSession();
        $idEmpresa                    = $request->getSession()->get('idEmpresa');
        $prefijoEmpresa               = ($session->get('prefijoEmpresa') ? $session->get('prefijoEmpresa') : "");
        $strTieneSmartApWifi          = "NO";
        $strRegistraEquipo            = "N";
        $servicioTecnicoService       = $this->get('tecnico.InfoServicioTecnico');
        $em                           = $this->get('doctrine')->getManager('telconet');
        $emInfraestructura            = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emGeneral                    = $this->getDoctrine()->getManager('telconet_general');
        $resultado                    = $em->getRepository('schemaBundle:InfoServicio')
                                           ->findServiciosPorEmpresaPorPuntoIdPorEstado($idEmpresa, $id, 'Activo');       
        $serviciosPunto               = $resultado['registros'];
        $objTipoSolicitudCambioEquipo = $em->getRepository('schemaBundle:AdmiTipoSolicitud')
                                           ->findOneByDescripcionSolicitud($this->tipoSolicitud);
        $arrayServicioAfectados       = array();
        $arrayServicios               = array();
         
        //Obtener los elementos de tipo CPE escogidos para realizar el cambio de cpe y determinar que servicios estan configurados en estos
        //esta opcion solo será válidad para servicios de la empresa TN
        if($prefijoEmpresa == 'TN')
        {            
            $arrayParametros               = array();
            $arrayParametros['intIdPunto'] = $id;
            $arrayServicioAfectados        = $em->getRepository('schemaBundle:InfoServicio')
                                                ->getArrayServiciosAfectadosPorCambioEquipo($arrayParametros);
        }        
        
        foreach($arrayServicioAfectados as $serviciosAfectados)
        {
            $arrayServicios[] = $serviciosAfectados['servicio'];
        }
        
        $arrayServiciosPunto = array();
        
        $objCaracteristica = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                ->findOneBy(array("descripcionCaracteristica" => "REGISTRO EQUIPO",
                                                  "estado"                    => "Activo"));
        $objCaractEleCliente = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                ->findOneBy(array("descripcionCaracteristica" => "ELEMENTO CLIENTE",
                                                  "estado"                    => "Activo"));

        foreach ($serviciosPunto as $servicioIt):
            //Verifica si existe ya una solicitud de descuento solicitado y que este pendiente  
            $objDetalleSolicitudCambioEquipo = $em->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                  ->findOneBy(array("servicioId"      => $servicioIt->getId(),
                                                                    "tipoSolicitudId" => $objTipoSolicitudCambioEquipo->getId(),
                                                                    "estado"          => 'AsignadoTarea'));
                    
            $yaFueSolicitada     = 'N';
            $strTieneSmartApWifi = "NO";
            $intIdServicio       = $servicioIt->getId();
            
            if ($objDetalleSolicitudCambioEquipo)
            {
                $yaFueSolicitada='S';
            }
            else
            {               
                if($prefijoEmpresa == 'TN')
                {                  
                    if(in_array($intIdServicio, $arrayServicios))
                    {
                         $yaFueSolicitada='S';
                    }                                       
                }                
            }
            
            //se obtiene todas las solicitudes del cliente, para obtener los id de elementos
            $arrayElementosSol             = array();
            $arrayDetSolicitudCambioEquipo = $em->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                  ->findBy(array("servicioId"      => $servicioIt->getId(),
                                                                 "tipoSolicitudId" => $objTipoSolicitudCambioEquipo->getId(),
                                                                 "estado"          => 'AsignadoTarea'));
            foreach($arrayDetSolicitudCambioEquipo as $objDetSolCambioEquipo)
            {
                $objDetalleSolCaract = $em->getRepository('schemaBundle:InfoDetalleSolCaract')
                                            ->findOneBy(array("detalleSolicitudId" => $objDetSolCambioEquipo->getId(),
                                                              "caracteristicaId"   => $objCaractEleCliente->getId(),
                                                              "estado"             => 'AsignadoTarea'));
                if(is_object($objDetalleSolCaract))
                {
                    $arrayElementosSol[] = $objDetalleSolCaract->getValor();
                }
            }

            $esEnlace            ="NO";
            $idProducto          ='';
            $descripcionProducto ='';
            
            if ($servicioIt->getProductoId())
            {
                $tipo                   = 'producto';
                $esEnlace               = $servicioIt->getProductoId()->getEsEnlace();
                $idProducto             = $servicioIt->getProductoId()->getId();
                $descripcionProducto    = $servicioIt->getProductoId()->getDescripcionProducto();
                $strTieneSmartApWifi    = (($servicioIt->getProductoId()->getNombreTecnico() == "SMARTWIFI" ||
                                           $servicioIt->getProductoId()->getNombreTecnico() == "APWIFI"
                                          ) &&
                                          (strpos($servicioIt->getProductoId()->getDescripcionProducto(), 'Renta') !== false))
                                          || ($servicioIt->getProductoId()->getNombreTecnico() == "EXTENDER_DUAL_BAND")
                                          || ($servicioIt->getProductoId()->getNombreTecnico() == "WDB_Y_EDB")
                                          ?"SI":"NO";
            }
            else if($servicioIt->getPlanId())
            {
                $tipo                = 'plan';
                $idProducto          = $servicioIt->getPlanId()->getId();
                $descripcionProducto = $servicioIt->getPlanId()->getDescripcionPlan(); 
                $productosPlan       = $em->getRepository('schemaBundle:InfoPlanDet')
                                          ->findBy(array('planId'=>$idProducto));

                foreach($productosPlan as $prod)
                {
                    $objProducto = $em->getRepository("schemaBundle:AdmiProducto")
                                      ->find($prod->getProductoId());
                    if(strtoupper($objProducto->getEsEnlace()) == 'SI')
                    {
                        $esEnlace = "SI";
                    }
                    if ($objProducto->getNombreTecnico() == "SMARTWIFI" || $objProducto->getNombreTecnico() == "EXTENDER_DUAL_BAND")
                    {
                        $strTieneSmartApWifi = "SI";
                    }
                }
            }

            if(is_object($objCaracteristica))
            {
                $objProductoCaracteristica = $em->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                ->findOneBy(array("productoId"       => $idProducto,
                                                                  "caracteristicaId" => $objCaracteristica->getId(),
                                                                  "estado"           => "Activo"));
            }

            if(is_object($objProductoCaracteristica))
            {
                $strRegistraEquipo = "S";
            }

            /*Obtengo los parametros de Wifi Alquiler Equipos.*/
            $arrayParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                  ->getOne('VALIDACIONES_WIFI_ALQUILER_EQUIPOS',
                                           'TECNICO',
                                           '',
                                           'PARAMETROS_VALIDACIONES_WIFI_ALQUILER_EQUIPOS',
                                           '',
                                           '',
                                           '',
                                           '',
                                           '',
                                           $idEmpresa);

            /*Decodifico lo que obtuvimos en el paso anterior para poderlo utilizar en la validación a continuación.*/
            $arrayParametrosWAE = json_decode($arrayParametroDet['valor1']);

            /* Ingresa al IF en caso de que sea servicios de tipo enlace ó servicios de productos SmartWifi de MD*/
            if ((strtoupper($esEnlace) == "SI") ||
                ($prefijoEmpresa == 'MD' && $strTieneSmartApWifi == "SI") ||
                (is_object($servicioIt->getProductoId()) &&
                    ($servicioIt->getProductoId()->getDescripcionProducto() == 'COU LINEAS TELEFONIA FIJA' ||
                        $servicioIt->getProductoId()->getDescripcionProducto() == $arrayParametrosWAE->nombreProducto)) ||
                (is_object($servicioIt->getProductoId()) &&
                    ( $servicioIt->getProductoId()->getNombreTecnico() == 'SEG_VEHICULO' ||
                      $servicioIt->getProductoId()->getNombreTecnico() == 'SAFE ENTRY') ) ||
                ($strRegistraEquipo == "S"))
            {
                //obtener característica tipo de red
                $booleanTipoRedGpon = false;
                if(is_object($servicioIt->getProductoId()))
                {
                    $strTipoRed       = "";
                    $objCaractTipoRed = $servicioTecnicoService->getServicioProductoCaracteristica($servicioIt,
                                                            'TIPO_RED',$servicioIt->getProductoId());
                    if(is_object($objCaractTipoRed))
                    {
                        $strTipoRed = $objCaractTipoRed->getValor();
                    }
                    if(!empty($strTipoRed))
                    {
                        $arrayParVerTipoRed = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getOne('NUEVA_RED_GPON_TN',
                                                            'COMERCIAL',
                                                            '',
                                                            'VERIFICAR TIPO RED',
                                                            'VERIFICAR_GPON',
                                                            $strTipoRed,
                                                            '',
                                                            '',
                                                            '');
                        if(isset($arrayParVerTipoRed) && !empty($arrayParVerTipoRed))
                        {
                            $booleanTipoRedGpon = true;
                        }
                    }
                }
                $arrayParametros = array();
                $arrayParametros['idServicio']        = $servicioIt->getId();
                $arrayParametros['idPunto']           = $servicioIt->getPuntoId()->getId();
                $arrayParametros['tipo']              = $tipo;
                $arrayParametros['estadoServicio']    = $servicioIt->getEstado();
                $arrayParametros['fueSolicitada']     = $yaFueSolicitada;
                $arrayParametros['emInfraestructura'] = $emInfraestructura;
                $arrayParametros['serviceTecnico']    = $servicioTecnicoService; 
                $arrayParametros['prefijoEmpresa']    = $prefijoEmpresa;
                $arrayParametros['idEmpresa']         = $idEmpresa;
                $arrayParametros['strTieneSmartWifi'] = $strTieneSmartApWifi;
                $arrayParametros['registraEquipo']    = $strRegistraEquipo;
                $arrayParametros['booleanTipoRedGpon'] = $booleanTipoRedGpon;
                $arrayParametros['arrayElementosSol'] = $arrayElementosSol;

                $arrayResponse = $this->getDoctrine()
                                      ->getManager("telconet")
                                      ->getRepository('schemaBundle:InfoServicio')
                                      ->generarElementosPorServicio($arrayParametros);

                foreach ($arrayResponse as $record)
                {
                    $arrayServiciosPunto[] = $record;
                }

                $strRegistraEquipo = "N";
            }
        endforeach;
        
        $num     = count($arrayServiciosPunto);
        $objJson = '';
        
        if($num == 0)
        {
            $responseData = array('total'       => 1,
                                  'encontrados' => array());
            $objJson      = json_encode($responseData);
        }
        else
        {
            $responseData = json_encode($arrayServiciosPunto);
            $objJson      = '{"total":"' . $num . '","encontrados":' . $responseData . '}';
        }
        
        $respuesta->setContent($objJson);
        return $respuesta;
    }    
    
    public function aprobarSolicitudCambioModemInmediatoAction(){
        return $this->render('comercialBundle:solicitudcambioequipo:aprobarSolicitudCambioEquipo.html.twig', array());
    }    
    
    /*
    * @Secure(roles="ROLE_")
    */
    public function gridAprobarSolicitudCambioModemInmediatoAction(){
		$request = $this->getRequest();
		$request=$this->get('request');
		$session=$request->getSession();
		$idEmpresa=$session->get('idEmpresa');		       
		$fechaDesde=explode('T',$request->get("fechaDesde"));
		$fechaHasta=explode('T',$request->get("fechaHasta"));
                $login=$request->get('login');
		$limit=$request->get("limit");
		$start=$request->get("start");
		$em = $this->get('doctrine')->getManager('telconet');
                //echo "fechaDesde:".$fechaDesde; die;
		//if ((!$fechaDesde[0])&&(!$fechaHasta[0]))
		//{
                //        $datos = $em->getRepository('schemaBundle:InfoDetalleSolicitud')->find30SolicCancelacion($idEmpresa,$this->tipoSolicitud,'Pendiente');
		//}
		//else
		//{
                        $resultado= $em->getRepository('schemaBundle:InfoDetalleSolicitud')->findSolicCancelacionPorCriterios('PrePlanificada',$this->tipoSolicitud,$idEmpresa,$start,$limit,$fechaDesde[0],$fechaHasta[0],$login);
		//}
            $datos = $resultado['registros'];
            $total = $resultado['total'];      
		foreach ($datos as $datos):
				$linkVer = '#';
                            $valor=$datos->getPrecioDescuento();

				$entityMotivo=$em->getRepository('schemaBundle:AdmiMotivo')->find($datos->getMotivoId());
				$producto='';
				if($datos->getServicioId()->getProductoId()){
					$entityProducto=$em->getRepository('schemaBundle:AdmiProducto')->find($datos->getServicioId()->getProductoId()->getId());
					$producto=$entityProducto->getDescripcionProducto();
				}elseif($datos->getServicioId()->getPlanId()){
				//echo $datos->getServicioId()->getPlanId();
					$entityProducto=$em->getRepository('schemaBundle:InfoPlanCab')->find($datos->getServicioId()->getPlanId()->getId());
					$producto=$entityProducto->getNombrePlan();
				}
                                if ($datos->getServicioId()->getPuntoId()->getPersonaEmpresaRolId()){
                                    if ($datos->getServicioId()->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId()->getRazonSocial())
                                        $cliente=$datos->getServicioId()->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId()->getRazonSocial();
                                    else
                                        $cliente=$datos->getServicioId()->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId()->getNombres()." ".
                                        $datos->getServicioId()->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId()->getApellidos();
                                }
                                $tipoDoc='';
                                if(strtoupper($datos->getTipoDocumento())=='V')
                                        $tipoDoc='Venta';
                                elseif(strtoupper($datos->getTipoDocumento())=='C')
                                        $tipoDoc='Cortesia';

				$arreglo[]= array(
				'id'=>$datos->getId(),
				'servicio'=>$producto,
                                'cliente'=>$cliente,    
				'login'=> $datos->getServicioId()->getPuntoId()->getLogin(),
				'motivo'=> $entityMotivo->getNombreMotivo(),
				'valor'=> $valor,
				'tipoDocumento'=> $tipoDoc,                                    
				'observacion'=> $datos->getObservacion(),
				'feCreacion'=> strval(date_format($datos->getFeCreacion(),"d/m/Y G:i")),
				'usrCreacion'=> $datos->getUsrCreacion(),
				'linkVer'=> $linkVer
                 );    
		endforeach;
		if (!empty($arreglo))
            $response = new Response(json_encode(array('total' => $total, 'solicitudes' => $arreglo)));                    

		else
		{
			$arreglo[]= array();
			$response = new Response(json_encode(array('total' => $total, 'solicitudes' => $arreglo)));
		}		
		$response->headers->set('Content-type', 'text/json');
		return $response;
    }    
    
    public function rechazarSolicitudCambioModemInmediatoAjaxAction(){

        $request=$this->getRequest();
        $idEmpresa=$request->getSession()->get('idEmpresa');
        $session  = $request->getSession();         
        $usrCreacion=$session->get('user');		
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');        
        $respuesta->setContent("error del Form");  
        $em = $this->getDoctrine()->getManager('telconet');

        //Obtiene parametros enviados desde el ajax
        $peticion = $this->get('request');
        $parametro = $peticion->get('param');
        $motivoId = $peticion->get('motivoId');
        $array_valor = explode("|",$parametro);       
        
        $em->getConnection()->beginTransaction();
 	try{  
            foreach($array_valor as $id){            
                $entity = $em->getRepository('schemaBundle:InfoDetalleSolicitud')->find($id);
                if (!$entity) {
                        throw $this->createNotFoundException('No se encontro la solicitud buscada');
                }
                $entity->setEstado('Anulado');
                $entity->setUsrRechazo($usrCreacion);
                $entity->setFeRechazo(new \DateTime('now'));                
                $em->persist($entity);
                $em->flush();					

                //Grabamos en la tabla de historial de la solicitud
                $entityHistorial= new InfoDetalleSolHist();
                $entityHistorial->setEstado('Anulado');
                $entityHistorial->setDetalleSolicitudId($entity);
                $entityHistorial->setUsrCreacion($usrCreacion);
                $entityHistorial->setFeCreacion(new \DateTime('now'));
                $entityHistorial->setIpCreacion($request->getClientIp());
                $entityHistorial->setMotivoId($motivoId);
                //$entityHistorial->setObservacion($obs);
                $em->persist($entityHistorial);
                $em->flush();							
            }
             
           $em->getConnection()->commit();   
           $respuesta->setContent("Se anularon las solicitudes con exito.");            
       }       
	catch (\Exception $e) {
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            $respuesta->setContent($e->getMessage());            
	}
       

       return $respuesta;        

    }
    
    public function getMotivosRechazoSolicitudCambioModemInmediato_ajaxAction() {
        /* Modificacion a utilizacion de estados por modulos */
        //$session = $this->get('request')->getSession();
        //$modulo_activo=$session->get("modulo_activo");
        $em = $this->get('doctrine')->getManager('telconet');
        $datos = $em->getRepository('schemaBundle:AdmiMotivo')
		->findMotivosPorModuloPorItemMenuPorAccion('aprobacionsolicitudcambioequipo','','rechazarsolicitudcambioequipoajax');
		$arreglo=array();
    //print_r($datos);die;
    foreach($datos as $valor):
        //print_r($entityAdmiTipoSolicitud[0]->getId());
            $arreglo[] = array(
                'idMotivo' => $valor->getId(),
                'descripcion' => $valor->getNombreMotivo(),
                'idRelacionSistema'=>$valor->getRelacionSistemaId()
            );
    endforeach;
    //die;

        $response = new Response(json_encode(array('motivos' => $arreglo)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }
    
    /**     
     * Documentación para el método 'ajaxInformacionServicioMd'.
     *
     * Método utilizado para recuperar información de un servicio Md, se recupera marca de Olt que aprovisiona internet,
     * variable que indica si tiene una solicitud de migracion Tll-Hw finalizada, variable que indica si tiene un elemento
     * wifi adicional asignado, información de Cpe Wifi Hw e información e Wifi adicional en caso de tenerlo
     *     
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 08-10-2016
     * @since 1.0
     * 
     * @return JsonResponse $objRespuesta 
     *                               [
     *                                  - strStatus                         Estado de ejecución de proceso
     *                                  - strMensaje                        Mensaje de respuesta de ejecución de proceso
     *                                  - strSolicitudMigracionFinalizada   Cadena de caracteres usada para validar si un servicio hw
     *                                                                      tiene una solicitud de migracion en estado finalizada 
     *                                  - strEquipoWifiAdicional            Cadena de caracteres que indica si un servicio hw tiene 
     *                                                                      un equipo adicional wifi conectado
     *                                  - strNombreCpe                      Cadena de caracteres con nombre de Cpe Ont hw
     *                                  - strModeloCpe                      Cadena de caracteres con modelo de Cpe Ont hw
     *                                  - strMarcaCpe                       Cadena de caracteres con marca de Cpe Ont hw
     *                                  - strNombreWifi                     Cadena de caracteres con nombre de wifi adicional hw
     *                                  - strModeloWifi                     Cadena de caracteres con modelo de wifi adicional hw
     *                                  - strMarcaWifi                      Cadena de caracteres con marca de wifi adicional hw
     *                                  - intElementoWifi                   Identificador de elemento wifi adicional hw
     *                               ]
     */
    public function ajaxInformacionServicioMdAction()
    {
        $objRespuesta  = new JsonResponse();
        
        $objRequest    = $this->getRequest();
        $objSession    = $objRequest->getSession();
        $strIpClient   = $objRequest->getClientIp();
        $strUser       = $objSession->get("user");
        $serviceUtil   = $this->get('schema.Util');
        $intIdServicio = $objRequest->get('idServicio');
        try
        {
            $serviceTecnico          = $this->get('comercial.InfoServicio');
            $arrayParametros         = array("intIdServicio" => $intIdServicio);
            $jsonInformacionServicio = $serviceTecnico->getJsonInfoServicioMd($arrayParametros);
            $objRespuesta->setContent($jsonInformacionServicio);
        }
        catch (\Exception $ex)
        {
            $serviceUtil->insertError('Telcos+', 
                                      'SolicitudCambioModemInmediatoController.ajaxInformacionServicioMdAction', 
                                      $ex->getMessage(), 
                                      $strUser, 
                                      $strIpClient);
            $objRespuesta->setContent(json_encode(array('strStatus'  => "ERROR", 
                                                        'strMensaje' => "Se presentaron errores al ejecutar la accion, ".
                                                                        "favor notificar a sistemas.")));
        }
        return $objRespuesta;                                                                                                                  
    }
}
