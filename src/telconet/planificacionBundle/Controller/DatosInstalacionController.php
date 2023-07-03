<?php

namespace telconet\planificacionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation\Secure;

class DatosInstalacionController extends Controller implements TokenAuthenticatedController
{
    /**
	* @Secure(roles="ROLE_192-1")
	*/
    public function indexAction()
    {    
    
    $rolesPermitidos = array();
	
	if (true === $this->get('security.context')->isGranted('ROLE_135-95'))
		{
	$rolesPermitidos[] = 'ROLE_135-95';
	}
	if (true === $this->get('security.context')->isGranted('ROLE_135-94'))
		{
	$rolesPermitidos[] = 'ROLE_135-94';
	}
	
        $em = $this->getDoctrine()->getManager('telconet_general');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("192", "1");
        
        return $this->render('planificacionBundle:DatosInstalacion:index.html.twig', array(
             'item' => $entityItemMenu,
             'rolesPermitidos' => $rolesPermitidos
        ));
    }
	
	public function gridAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');    
        $codEmpresa = ($peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa') : "");
        $fechaDesdePlanif = explode('T',$peticion->query->get('fechaDesdePlanif'));
        $fechaHastaPlanif = explode('T',$peticion->query->get('fechaHastaPlanif'));
        
        $login2 = ($peticion->query->get('login2') ? $peticion->query->get('login2') : "");
        $descripcionPunto = ($peticion->query->get('descripcionPunto') ? $peticion->query->get('descripcionPunto') : "");
        $vendedor = ($peticion->query->get('vendedor') ? $peticion->query->get('vendedor') : "");
        $ciudad = ($peticion->query->get('ciudad') ? $peticion->query->get('ciudad') : "");
        $numOrdenServicio = ($peticion->query->get('numOrdenServicio') ? $peticion->query->get('numOrdenServicio') : "");
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $em_infra = $this->getDoctrine()->getManager("telconet_infraestructura");
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet")
            ->getRepository('schemaBundle:InfoDetalleSolicitud')
            ->generarJsonAsignados($em_infra, $start, $limit, $fechaDesdePlanif[0], $fechaHastaPlanif[0], $login2, '',  
                                         $descripcionPunto, $vendedor, $numOrdenServicio, $ciudad,$codEmpresa);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
	
    /**
     * Funcion que sirve para descargar el pdf
     * de asignacion de recursos de red.
     * 
     * @author Kenneth Jimenez <kjimenez@telconet.ec>
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 7-11-2014
     */
    public function getDatosInstalacionPdfAction()
    {
        $peticion = $this->get('request');
        $session = $peticion->getSession();
        $idEmpresa = $session->get('idEmpresa');
        $idServicio = $peticion->query->get('id_servicio');
        $cliente = $peticion->query->get('cliente');
        $idSolicitud = $peticion->query->get('id_solicitud');

        $datosInstalacion = array();

        $emInfraestructura = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emComercial = $this->get('doctrine')->getManager('telconet');

        $servicio = $emComercial->getRepository('schemaBundle:InfoServicio')->find($idServicio);
        $InfoServicioTecnico = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')->findOneByServicioId($idServicio);
        $productoInternetDedicado = $emComercial->getRepository('schemaBundle:AdmiProducto')
                                    ->findOneBy(array("nombreTecnico" => "INTERNET", "estado" => "Activo", "empresaCod" => $idEmpresa));
        $TipoMedio = $emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')->find($InfoServicioTecnico->getUltimaMillaId());
        $datosInstalacion['tipoMedio'] = $TipoMedio->getNombreTipoMedio();

        $datosInstalacion['numeroOrdenTrabajo'] = $servicio->getOrdenTrabajoId()->getNumeroOrdenTrabajo();
        $datosInstalacion['login']              = $servicio->getPuntoId()->getLogin();
        $datosInstalacion['observacion']        = $servicio->getPuntoId()->getObservacion();
        $datosInstalacion['cliente']            = $cliente;
        $datosInstalacion['direccion']          = $servicio->getPuntoId()->getDireccion();

        $datosInstalacion['contactosTelefonosFijos']            = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                    ->findContactosByLoginAndFormaContacto($datosInstalacion['login'], 'Telefono Fijo');
        $datosInstalacion['contactosTelefonosFijosPunto']       = $emComercial->getRepository('schemaBundle:InfoPuntoFormaContacto')
                                                                    ->findContactosByPunto($datosInstalacion['login'], 'Telefono Fijo');
        $datosInstalacion['contactosTelefonosMovil']            = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                    ->findContactosByLoginAndFormaContacto($datosInstalacion['login'], 'Telefono Movil');
        $datosInstalacion['contactosTelefonoMovilPunto']        = $emComercial->getRepository('schemaBundle:InfoPuntoFormaContacto')
                                                                    ->findContactosByPunto($datosInstalacion['login'], 'Telefono Movil');
        $datosInstalacion['contactosTelefonosMovilClaro']       = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                              ->findContactosByLoginAndFormaContacto($datosInstalacion['login'], 'Telefono Movil Claro');
        $datosInstalacion['contactosTelefonosMovilClaroPunto']  = $emComercial->getRepository('schemaBundle:InfoPuntoFormaContacto')
                                                              ->findContactosByPunto($datosInstalacion['login'], 'Telefono Movil Claro');
        $datosInstalacion['contactosTelefonosMovilMovistar']    = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                           ->findContactosByLoginAndFormaContacto($datosInstalacion['login'], 'Telefono Movil Movistar');
        $datosInstalacion['contactosTelefonosMovilMovistarPunto'] = $emComercial->getRepository('schemaBundle:InfoPuntoFormaContacto')
                                                           ->findContactosByPunto($datosInstalacion['login'], 'Telefono Movil Movistar');
        $datosInstalacion['contactosTelefonosMovilCnt']         = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                ->findContactosByLoginAndFormaContacto($datosInstalacion['login'], 'Telefono Movil CNT');
        $datosInstalacion['contactosTelefonosMovilCntPunto']    = $emComercial->getRepository('schemaBundle:InfoPuntoFormaContacto')
                                                                ->findContactosByPunto($datosInstalacion['login'], 'Telefono Movil CNT');
        $datosInstalacion['contactosCorreos']                   = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                ->findContactosByLoginAndFormaContacto($datosInstalacion['login'], 'Correo Electronico');
        $datosInstalacion['contactosCorreosPunto']              = $emComercial->getRepository('schemaBundle:InfoPuntoFormaContacto')
                                                                ->findContactosByPunto($datosInstalacion['login'], 'Correo Electronico');
        
        $interfaceElementoId = $InfoServicioTecnico->getInterfaceElementoId();

        $interfaceElemento = $emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')->find($interfaceElementoId);
        $datosInstalacion['nombreInterfaceElemento'] = $interfaceElemento->getNombreInterfaceElemento();

        $elementoId = $InfoServicioTecnico->getElementoId();

        $elemento = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($elementoId);
        $datosInstalacion['elemento'] = $elemento->getNombreElemento();

        $modeloElementoId = $elemento->getModeloElementoId();

        $modeloElemento = $emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')->find($modeloElementoId);
        $datosInstalacion['nombreModeloElemento'] = $modeloElemento->getNombreModeloElemento();

        $tipoElementoId = $modeloElemento->getTipoElementoId();

        $tipoElemento = $emInfraestructura->getRepository('schemaBundle:AdmiTipoElemento')->find($tipoElementoId);
        $datosInstalacion['nombreTipoElemento'] = $tipoElemento->getNombreTipoElemento();

        if($datosInstalacion['tipoMedio'] == "Cobre")
        {
            $caracteristicaVci = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                             ->findOneBy(array("descripcionCaracteristica" => "VCI", "estado" => "Activo"));
            $pcVci = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                 ->findOneBy(array("productoId" => $productoInternetDedicado->getId(), 
                                                   "caracteristicaId" => $caracteristicaVci->getId()));
            $ispcVci = $emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                   ->findOneBy(array("servicioId" => $idServicio, "productoCaracterisiticaId" => $pcVci->getId()));
            $datosInstalacion['vci'] = $ispcVci->getValor();
        }
        else
        {
            $datosInstalacion['vci'] = "";
        }

        $datosInstalacion['nombrePlan'] = $servicio->getPlanId()->getNombrePlan();

        $tareasPlanificacion = $this->getDoctrine()
            ->getManager("telconet_soporte")
            ->getRepository('schemaBundle:InfoDetalle')
            ->generarArrayTareasAsignadas($emComercial, "", "", $idSolicitud);

        $tieneTecnico = false;
        foreach($tareasPlanificacion as $tareaPlanificacion)
        {
            if(strpos($tareaPlanificacion['nombre_tarea'], "INSTALACION MODEM") != false)
            {
                $empleadoInst = $emComercial->getRepository('schemaBundle:InfoPersona')
                    ->findOneById(($tareaPlanificacion['ref_id_asignado']) ? 
                                   $tareaPlanificacion['ref_id_asignado'] : $tareaPlanificacion['id_asignado']);

                $datosInstalacion['nombreTecnico'] = $empleadoInst;
                $tieneTecnico = true;
                break;
            }
        }

        if(!$tieneTecnico)
        {
            $datosInstalacion['nombreTecnico'] = "No asignado";
        }

        $datosInstalacion['ipLan'] = "";
        $datosInstalacion['mascaraLan'] = "";
        $datosInstalacion['gatewayLan'] = "";

        $datosInstalacion['ipWan'] = "";
        $datosInstalacion['mascaraWan'] = "";
        $datosInstalacion['gatewayWan'] = "";

        $datosInstalacion['ipPublica'] = "";
        $datosInstalacion['mascaraPublica'] = "";
        $datosInstalacion['gatewayPublica'] = "";

        $datosInstalacion['ipMonitoreo'] = "";
        $datosInstalacion['mascaraMonitoreo'] = "";
        $datosInstalacion['gatewayMonitoreo'] = "";

        $caracteristicaIpLan = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                        ->findOneBy(array("descripcionCaracteristica" => "IP LAN", "estado" => "Activo"));
        $pcIpLan = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                               ->findOneBy(array("productoId" => $productoInternetDedicado->getId(), 
                                                 "caracteristicaId" => $caracteristicaIpLan->getId()));
        $ispcIpLan = $emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                 ->findOneBy(array("servicioId" => $idServicio, 
                                                   "productoCaracterisiticaId" => $pcIpLan->getId()));
        $datosInstalacion['ipLan'] = ($ispcIpLan) ? $ispcIpLan->getValor() : "";

        $caracteristicaMascaraLan = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneBy(array("descripcionCaracteristica" => "MASCARA LAN", "estado" => "Activo"));
        $pcMascaraLan = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                    ->findOneBy(array("productoId" => $productoInternetDedicado->getId(), 
                                                      "caracteristicaId" => $caracteristicaMascaraLan->getId()));
        $ispcMascaraLan = $emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                      ->findOneBy(array("servicioId" => $idServicio, 
                                                        "productoCaracterisiticaId" => $pcMascaraLan->getId()));
        $datosInstalacion['mascaraLan'] = ($ispcMascaraLan) ? $ispcMascaraLan->getValor() : "";

        $caracteristicaGatewayLan = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                            ->findOneBy(array("descripcionCaracteristica" => "GATEWAY LAN", "estado" => "Activo"));
        $pcGatewayLan = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                    ->findOneBy(array("productoId" => $productoInternetDedicado->getId(), 
                                                      "caracteristicaId" => $caracteristicaGatewayLan->getId()));
        $ispcGatewayLan = $emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                      ->findOneBy(array("servicioId" => $idServicio, 
                                                        "productoCaracterisiticaId" => $pcGatewayLan->getId()));
        $datosInstalacion['gatewayLan'] = ($ispcGatewayLan) ? $ispcGatewayLan->getValor() : "";

        $ipsServicio = $emInfraestructura->getRepository("schemaBundle:InfoIp")
                                        ->findBy(array("servicioId" => $idServicio, "estado" => 'Activo'));
        //ips publicas
        $admiProductoIpPublica = $emComercial->getRepository('schemaBundle:AdmiProducto')
            ->findOneBy(array("nombreTecnico" => "IP", "estado" => "Activo", "empresaCod" => $idEmpresa));
        $serviciosIpsPublica = $emComercial->getRepository('schemaBundle:InfoServicio')
                                           ->findBy(array("puntoId" => $servicio->getPuntoId(), 
                                                          "productoId" => $admiProductoIpPublica->getId(), 
                                                          "estado" => "Asignada"));

        foreach($serviciosIpsPublica as $servicioIpPublica)
        {
            $ipsPublicasPunto = $emInfraestructura->getRepository("schemaBundle:InfoIp")
                                                  ->findBy(array("servicioId" => $servicioIpPublica->getId(), "estado" => 'Activo'));

            if($ipsPublicasPunto)
                $ipsServicio = array_merge($ipsServicio, $ipsPublicasPunto);
        }

        if($ipsServicio)
        {
            foreach($ipsServicio as $ipServicio)
            {
                $tipo = $ipServicio->getTipoIp();
                if($tipo == "WAN")
                {
                    $datosInstalacion['ipWan'] = $ipServicio->getIp();
                    $datosInstalacion['mascaraWan'] = $ipServicio->getMascara();
                    $datosInstalacion['gatewayWan'] = $ipServicio->getGateway();
                }
                if($tipo == "PUBLICA")
                {
                    $datosInstalacion['ipPublica'] = $ipServicio->getIp();
                    $datosInstalacion['mascaraPublica'] = $ipServicio->getMascara();
                    $datosInstalacion['gatewayPublica'] = $ipServicio->getGateway();
                }
                if($tipo == "MONITOREO")
                {
                    $datosInstalacion['ipMonitoreo'] = $ipServicio->getIp();
                    $datosInstalacion['mascaraMonitoreo'] = $ipServicio->getMascara();
                    $datosInstalacion['gatewayMonitoreo'] = $ipServicio->getGateway();
                }
            }
        }

        $html = $this->renderView('planificacionBundle:DatosInstalacion:DatosInstalacion.html.twig', 
                                   array('datosInstalacion' => $datosInstalacion));
        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html), 200, array(
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename=DatosInstalacion-' . trim($datosInstalacion['login']) . '.pdf',
            )
        );
    }

}
