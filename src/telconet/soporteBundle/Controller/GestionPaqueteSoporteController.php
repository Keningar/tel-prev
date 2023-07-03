<?php

namespace telconet\soporteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolCarac;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use telconet\schemaBundle\Entity\InfoServicio;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoServicioProdCaract;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class GestionPaqueteSoporteController extends Controller implements TokenAuthenticatedController
{
    public function redirigirAction(Request $objRequest)
    {
        $objSession     = $objRequest->getSession();

        $strIdServicio = $objRequest->get('servicio');
        $strRazonSocial = $objRequest->get('razonSocial');
        $strCliente = $objRequest->get('cliente');

        $arrayParametros = array();


        $strRoute = $this->generateUrl(
            'gestionPaqueteSoporte_configurarLoginServicio',
            array('id' => $strIdServicio,
                  'razonSocial' => $strRazonSocial,
                  'cliente' => $strCliente)
        );

        return $this->redirect($strRoute, 301);
    }

    /**
     * Función que renderiza la pantalla para configurar logines y
     * servicios a un paquete de horas de soporte.
     * @author Jonathan Quintana <jiquintana@telconet.ec>
     * @version 1.0
     * @since 22-12-2022
     */
    public function configurarLoginServicioAction($strId)
    {
        $objPeticion = $this->get('request');
        $objSession = $objPeticion->getSession();

        $strCodEmpresa = ($objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : "");
        $strPrefijoEmpresa = ($objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa') : "");

        $strRol = $objPeticion->get('rol');
        $strNombre = $objPeticion->get('nombre');
        $strRazonSocial = ($objSession->get('cliente')['razon_social']?$objSession->get('cliente')['razon_social']:"");
        $strIdCliente = $objPeticion->get('idCliente');
        $strEsPadre = $objPeticion->get('esPadre');
        $strCliente = $objPeticion->get('cliente');
        $strIdPunto = $objPeticion->get('idPunto');

        return $this->render('soporteBundle:GestionPaqueteSoporte:configurarLoginServicio.html.twig', array('id' => $strId,
        'rol' => $strRol,
        'nombre' => $strNombre,
        'razonSocial' => $strRazonSocial,
        'idCliente' => $strIdCliente,
        'esPadre' => $strEsPadre,
        'cliente' => ($strCliente!= ' ')?$strCliente:$strRazonSocial,
        'idPunto' => $strIdPunto));
    }

    /**
     * Función que renderiza la pantalla para consultar los soportes de
     * un paquete de horas de soporte.
     * @author Jonathan Quintana <jiquintana@telconet.ec>
     * @version 1.0
     * @since 22-12-2022
     */
    public function consultarPaqueteSoporteAction($strId)
    {
        $objPeticion = $this->get('request');
        $objSession = $objPeticion->getSession();
        $strUser = $objSession->get('user');

        $strCodEmpresa = ($objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : "");
        $strPrefijoEmpresa = ($objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa') : "");

        $strIdServicio = $objPeticion->get('idServicio');

        return $this->render(
            'soporteBundle:GestionPaqueteSoporte:consultarPaqueteSoporte.html.twig',
            array('codEmpresa'    => $strCodEmpresa,
                  'id' => $strId,
                  'idServicio' => $strIdServicio,
                  'user' => $strUser)
        );
    }

    /**
     * Función que obtiene los puntos del cliente.
     * @author Jonathan Quintana <jiquintana@telconet.ec>
     * @version 1.0
     * @since 22-12-2022
     * Se agregò la descripciòn del producto
     * @author Liseth Candelario <lcandelario@telconet.ec>
     * @version 1.1
     * @since 06-02-2023
     */
    public function getPuntosClienteAction()
    {
        $objPeticion = $this->get('request');
        $objSession = $objPeticion->getSession();

        $objRespuesta = new Response();

        $strCodEmpresa = ($objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : "");

        $arrayParametros = array();
        $arrayParametros['codEmpresa'] = $strCodEmpresa;
        $arrayParametros['idCliente'] = $objPeticion->get('idCliente');//id_persona -> infoPersona
        $arrayParametros['nombre'] = $objPeticion->get('nombre'); //login -> infoPunto
        $arrayParametros['rol'] = $objPeticion->get('rol'); //descripcion tipo rol cliente, pre-cliente, etc
        $arrayParametros['start'] = $objPeticion->get('start');//comienzo
        $arrayParametros['limit'] = $objPeticion->get('limit');//fin
        $arrayParametros['page'] = $objPeticion->get('page');//numero de paginas
        $arrayParametros['esPadre'] = $objPeticion->get('esPadre');

        $serviceGestionPaqueteSoporte = $this->get('soporte.GestionPaqueteSoporte');

        $arrayPuntosEncontrados = array();
        
        $arrayResultado = $serviceGestionPaqueteSoporte->getPuntosCliente($arrayParametros);
        $objPuntos              = $arrayResultado['registros'];
        $intTotal               = $arrayResultado['total'];
        if($objPuntos)
        {
            foreach($objPuntos as $objPunto)
            {
                 $arrayPuntosEncontrados[] = array('loginP'            => $arrayParametros['nombre'],
                                                   'login'             => $objPunto['login'],
                                                   'direccion'         => $objPunto['direccion'],
                                                   'idPunto'           => $objPunto['id_punto'],
                                                'Descripcion_Producto' => $objPunto['Descripcion_Producto']
                                            );
            }
        }
       
        if (!empty($arrayPuntosEncontrados))
        {
            $arrayPuntosJson    = json_encode($arrayPuntosEncontrados);
            $objJson = '{"total":"' . $intTotal . '","listado":' . $arrayPuntosJson . '}';
        }

        $objRespuesta->setContent($objJson);

        return $objRespuesta;

    }

    /**
     * Función que obtiene los servicios mediante un punto o login.
     * @author Jonathan Quintana <jiquintana@telconet.ec>
     * @version 1.0
     * @since 22-12-2022
     */
    public function getServiciosByPuntoAction()
    {
        $objPeticion = $this->get('request');
        $objSession = $objPeticion->getSession();

        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');

        $strCodEmpresa = ($objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : "10");

        $arrayParametros = array();
        $arrayParametros['idPunto'] = $objPeticion->get('idPunto');
        $arrayParametros['login'] = $objPeticion->get('login');
        $arrayParametros['codEmpresa'] = $strCodEmpresa;
        $arrayParametros['login2'] = $objPeticion->get('login2');
        $arrayParametros['estado'] = $objPeticion->get('estado');
        $arrayParametros['arrayTraslados'] = $objPeticion->get('arrayTraslados');

        $serviceGestionPaqueteSoporte = $this->get('soporte.GestionPaqueteSoporte');

        $arrayPuntosEncontrados = array();
        
        $arrayResultado = $serviceGestionPaqueteSoporte->getServiciosByPunto($arrayParametros);
        $objPuntos              = $arrayResultado['registros'];
        $intTotal               = $arrayResultado['total'];
        if($objPuntos)
        {
            foreach($objPuntos as $objPunto)
            {
                 $arrayPuntosEncontrados[] = array('loginauxods'          => $objPunto['login_aux'],
                                                   'id_servicio_soporte'  => $objPunto['id_servicio'],
                                                   'id_punto_soporte'     => $objPunto['id_punto'],
                                                   'Descripcion_Producto' => $objPunto['producto'],
                                                   'estado_servicio'      => $objPunto['estado']
                                                );
            }
        }
       
        if(!empty($arrayPuntosEncontrados))
        {
            $arrayPuntosJson    = json_encode($arrayPuntosEncontrados);
            $objJson = '{"total":"' . $intTotal . '","listado":' . $arrayPuntosJson . '}';
        }

        $objRespuesta->setContent($objJson);

        return $objRespuesta;
    }

    /**
     * Función que ingresa los servicios asociados en la tabla
     * INFO_PAQUETE_SOPORTE_SERV.
     * @author Jonathan Quintana <jiquintana@telconet.ec>
     * @version 1.0
     * @since 22-12-2022
     */
    public function putServiciosPaqueteSoporteAction()
    {
        $objPeticion = $this->get('request');
        $objSession = $objPeticion->getSession();

        $emSoporte = $this->getDoctrine()->getManager();
        $emComercial    = $this->get('doctrine')->getManager('telconet');
        $serviceTecnico = $this->get('tecnico.InfoServicioTecnico');

        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');

        $strIdServicio = $objPeticion->get('idServicio');

        $strCodEmpresa = ($objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : "10");

        $arrayServicios = array();

        $objServicios = json_decode($objPeticion->get('servicios'));

        $arrayParametros = array();
        $arrayParametros['uuidPaquete'] = $emSoporte->getRepository('schemaBundle:GestionPaqueteSoporte')
        ->obtenerUuidPaquete(intval($strIdServicio))[0]['strUuidPaquete'];
        $arrayParametros['usuario'] = $objSession->get('user');
        $objServicioPadre           = $emSoporte->getRepository('schemaBundle:InfoDetalleSolicitud')
        ->obtenerServicioPorId(intval($strIdServicio));
        $objServicioHistPadre       = $emSoporte->getRepository('schemaBundle:InfoDetalleSolicitud')
        ->obtenerServicioHistPorId(intval($strIdServicio));
        $arrayObjServicioProdCaract = $emSoporte->getRepository('schemaBundle:InfoDetalleSolicitud')
        ->obtenerServicioProdCaractPorId(intval($strIdServicio));

        if($objServicios)
        {
            foreach($objServicios as $objServicio)
            {

                $strPermiteActivar = ($objServicio->permiteactivar)?'S':'N';

                 $arrayServicios[] = array('punto_soporte_id' => $objServicio->punto_soporte_id,
                                           'servicio_soporte_id' => $objServicio->servicio_soporte_id,
                                           'permite_activar_paquete' => $strPermiteActivar,
                                           'estado' => 'Activo');
                $objInfoPunto      = $emSoporte->getRepository('schemaBundle:InfoDetalleSolicitud')
                ->obtenerInfoPuntoPorId($objServicio->punto_soporte_id);
                                           
                $objPaqHorasSop = $emSoporte->getRepository('schemaBundle:InfoDetalleSolicitud')
                ->obtenerServiciosPaqueteHoras($objInfoPunto->getId());

                if (!$objPaqHorasSop)
                {
                    $emComercial->getConnection()->beginTransaction();

                /* Replicación en INFO_SERVICIO */
                $objServicioRep = new InfoServicio();
                $objServicioRep->setTipoOrden($objServicioPadre->getTipoOrden());
                $objServicioRep->setDescripcionPresentaFactura($objServicioPadre->getDescripcionPresentaFactura());
                $objServicioRep->setFeVigencia($objServicioPadre->getFeVigencia());
                $objServicioRep->setEstado($objServicioPadre->getEstado());
                $objServicioRep->setFeCreacion(new \DateTime('now'));
                $objServicioRep->setUsrCreacion('Telcos');
                $objServicioRep->setIpCreacion($objServicioPadre->getIpCreacion());
                $objServicioRep->setObservacion($objServicioPadre->getObservacion());
                $objServicioRep->setUsrVendedor($objServicioPadre->getUsrVendedor());
                $objServicioRep->setOrigen($objServicioPadre->getOrigen());
                $objServicioRep->setPuntoId($objInfoPunto);
                $objServicioRep->setPuntoFacturacionId($objServicioPadre->getPuntoFacturacionId());
                $objServicioRep->setProductoId($objServicioPadre->getProductoId());
                $objServicioRep->setEsVenta($objServicioPadre->getEsVenta());
                $objServicioRep->setCantidad($objServicioPadre->getCantidad());
                $objServicioRep->setPrecioVenta($objServicioPadre->getPrecioVenta());
                $objServicioRep->setCosto($objServicioPadre->getCosto());
                $objServicioRep->setFrecuenciaProducto($objServicioPadre->getFrecuenciaProducto());
                $objServicioRep->setMesesRestantes($objServicioPadre->getMesesRestantes());
                $objServicioRep->setPrecioFormula($objServicioPadre->getPrecioFormula());
                $objServicioRep->setPrecioInstalacion($objServicioPadre->getPrecioInstalacion());
                $emComercial->persist($objServicioRep);
                $serviceTecnico->generarLoginAuxiliar(intval($objServicioRep->getId()));
                $emComercial->persist($objServicioRep);
                $emComercial->flush();

                /* Replicación de INFO_SERVICIO_HISTORIAL de creación*/
                $objServicioHist = new InfoServicioHistorial();
                $objServicioHist->setServicioId($objServicioRep);
                $objServicioHist->setObservacion($objServicioHistPadre->getObservacion());
                $objServicioHist->setEstado($objServicioHistPadre->getEstado());
                $objServicioHist->setUsrCreacion('Telcos');
                $objServicioHist->setFeCreacion(new \DateTime('now'));
                $objServicioHist->setIpCreacion($objServicioHistPadre->getIpCreacion());
                $objServicioHist->setAccion($objServicioHistPadre->getAccion());
                $emComercial->persist($objServicioHist);
                $emComercial->flush();

                /* Replicación de INFO_SERVICIO_PROD_CARACT */
                foreach($arrayObjServicioProdCaract as $objServicioProdCaractPadre)
                {
                    $objServicioProdCaract = new InfoServicioProdCaract();
                    $objServicioProdCaract->setServicioId($objServicioRep->getId());
                    $objServicioProdCaract->setProductoCaracterisiticaId($objServicioProdCaractPadre->getProductoCaracterisiticaId());
                    $objServicioProdCaract->setValor($objServicioProdCaractPadre->getValor());
                    $objServicioProdCaract->setFeCreacion(new \DateTime('now'));
                    $objServicioProdCaract->setUsrCreacion('Telcos');
                    $objServicioProdCaract->setEstado($objServicioProdCaractPadre->getEstado());
                    $emComercial->persist($objServicioProdCaract);
                }

                /* INFO_SERVICIO_HISTORIAL de activación*/
                    $objServicioRep->setEstado('Activo'); //Se activa el recién replicado
                    $objServicioHist = new InfoServicioHistorial();
                    $objServicioHist->setServicioId($objServicioRep);
                    $objServicioHist->setUsrCreacion('Telcos');
                    $objServicioHist->setFeCreacion(new \DateTime('now'));
                    $objServicioHist->setIpCreacion($objServicioHistPadre->getIpCreacion());
                    $objServicioHist->setEstado('Activo');
                    $objServicioHist->setAccion('replicaPaqueteHoras');//$objServicioHistPadre->getAccion()
                    $objServicioHist->setObservacion('Servicio de paquete de horas Réplica');
                    $emComercial->persist($objServicioHist);
                    $emComercial->flush();
                $emComercial->flush();                
        
                try
                {
                    $emComercial->getConnection()->commit();
                                
                    $strResultado = json_encode(array('success'=>true));               
                
                }catch(\Exception $e)
                {
                
                    $emComercial->getConnection()->rollback();
                    $emComercial->getConnection()->close();      
                    $arrayParametros['excepcion'] = json_encode(array('success'=>false,'mensaje'=>$e));
                }
                }
            }
        }

        $arrayParametros['servicios'] = $arrayServicios;


        $serviceGestionPaqueteSoporte = $this->get('soporte.GestionPaqueteSoporte');

        $objRespuesta->setContent(json_encode($serviceGestionPaqueteSoporte->putServiciosPaqueteSoporte($arrayParametros)));

        return $objRespuesta;
    }

    /**
     * Función que obtiene los soportes de un paquete de horas
     * de soporte.
     * @author Jonathan Quintana <jiquintana@telconet.ec>
     * @version 1.0
     * @since 22-12-2022
     * 
     * Se agregò otro valor donde se envìa al tiempo de soporte
     * Se env+ia el nùmero de caso
     * @author Liseth Candelario <lcandelario@telconet.ec>
     * @version 1.1
     * @since 06-02-2023
     */
    public function getSoportesPaqSoporteAction()
    {
        $objPeticion = $this->get('request');
        $objSession = $objPeticion->getSession();

        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        
        $emGeneral    = $this->get('doctrine')->getManager('telconet_general');
        $emSoporte    = $this->get('doctrine')->getManager('telconet_soporte');
        $emComercial  = $this->get('doctrine')->getManager('telconet');  
        $strCodEmpresa  = ($objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : "10");
        
        $arrayParametros['personaEmpresaRolId'] = $objPeticion->get('id');
        
        $arrayParametros['servicioPaqueteId'] = $objPeticion->get('idServicio'); //Tiene que ser el del paquete del cab
        $objParametroDetValProd =   $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                    ->getOne("VALIDA_PRODUCTO_PAQUETE_HORAS_SOPORTE", //nombre parametro cab
                            "SOPORTE", "", 
                            "VALORES QUE AYUDAN A IDENTIFICAR QUE PRODUCTO ES PARA LA MUESTRA DE OPCIONES EN LA VISTA", //descripcion det
                            "", "", "", "", "", $strCodEmpresa
                        );
            
        if ($objParametroDetValProd)
        {
            $strValorProductoPaqHoras             = $objParametroDetValProd['valor1'];
            $objProducto            = $emComercial->getRepository('schemaBundle:AdmiProducto')
                                    ->findOneBy(array("descripcionProducto" => $strValorProductoPaqHoras));
            $objServicio            = $emComercial->getRepository('schemaBundle:InfoServicio')
                                    ->findOneBy(array("id"    => $arrayParametros['servicioPaqueteId']));
            $intIdProducto     = $objProducto->getId();
            $intIdPunto        = $objServicio->getPuntoId()->getId();
            $strLoginPunto     = $objServicio->getPuntoId()->getLogin();
            
            $objPrimerServicio = $emComercial->getRepository('schemaBundle:InfoServicio')
                                        ->findOneBy(array("puntoId"    => $intIdPunto,
                                                        "productoId"   => $intIdProducto
                                                    ), array("feCreacion"     => 'ASC'));
            $intPrimerServicioId        = $objPrimerServicio->getId();
            // Para saber si es replica o no.
            $objServicioReplica     = $emComercial->getRepository('schemaBundle:InfoServicioHistorial')
                                            ->findOneBy(array("servicioId" => $arrayParametros['servicioPaqueteId'],
                                                            "accion"     => "replicaPaqueteHoras"));
            if ($objServicioReplica)
            {
                $objInfoPaqSopServ        = $emSoporte->getRepository('schemaBundle:InfoPaqueteSoporteServ')
                                            ->soporteServPorLogin(array("loginPuntoSoporte"  => $strLoginPunto));

                $intPaqueteSoporteCabId   = $objInfoPaqSopServ[0]['paqueteSoporteCabId'];

                $objInfoPaqueteSoporteCab = $emSoporte->getRepository('schemaBundle:InfoPaqueteSoporteCab')
                            ->soporteCabPorCabId(array("idPaqueteSoporteCab"    => $intPaqueteSoporteCabId));
                $intPrimerServicioId   = $objInfoPaqueteSoporteCab[0]['servicioId'];
            }
        }
        $arrayParametros['servicioPaqueteId'] = $intPrimerServicioId;


        $arrayExtraParams['tareaId'] = $objPeticion->get('tarea_id');
        $arrayExtraParams['loginAuxiliar'] = $objPeticion->get('login_auxiliar');
        $arrayExtraParams['login'] = $objPeticion->get('login');
        $arrayExtraParams['fecha'] = $objPeticion->get('fecha');

        $emSoporte = $this->getDoctrine()->getManager();

        $intIdTipoSolicitud = $emSoporte->getRepository('schemaBundle:InfoDetalleSolicitud')
        ->getIdTipoSolicitud('SOLICITUD ACTUALIZACIÓN HORAS DE SOPORTE')[0]['idTipoSolicitud'];

        $arrayParametros['uuidPaquete'] = $emSoporte->getRepository('schemaBundle:GestionPaqueteSoporte')
        ->obtenerUuidPaquete($arrayParametros['servicioPaqueteId'])[0]['strUuidPaquete'];

        $serviceGestionPaqueteSoporte = $this->get('soporte.GestionPaqueteSoporte');

        $arraySoportes = $serviceGestionPaqueteSoporte->getSoportesPaqueteSoporte($arrayParametros);

        $arrayDatos = $arraySoportes['informacion'];

        $arrayResultados = array();

        foreach($arrayDatos as $objRegistro)
        {
            foreach($objRegistro->soportes as $objSoporte)
            {
                $arrayParametros['servicioId'] = $emSoporte->getRepository('schemaBundle:GestionPaqueteSoporte')
                        ->findIdServicioByNumeroTarea($objSoporte->tarea_id)[0]['servicioId'];

                $strEstadoSolicitud = $emSoporte->getRepository('schemaBundle:InfoDetalleSolicitud')
                        ->findUlitmoDetalleSolicitudByIds(intval($arrayParametros['servicioId']), $intIdTipoSolicitud);

                $strInformacionTarea = $emSoporte->getRepository('schemaBundle:GestionPaqueteSoporte')
                                ->findTipoTareaByNumeroTarea($objSoporte->tarea_id);

                $strEstadoSolicitud = isset($strEstadoSolicitud)?$strEstadoSolicitud->getEstado():'NO EXISTE';


                if ($arrayExtraParams['tareaId'] != '' || $arrayExtraParams['loginAuxiliar'] != ''
                || $arrayExtraParams['login'] != '' || $arrayExtraParams['fecha'] != '')
                {
                    if ($arrayExtraParams['tareaId'] == $objSoporte->tarea_id
                    || $arrayExtraParams['loginAuxiliar'] == $objSoporte->login_auxiliar
                    || $arrayExtraParams['login'] == $objSoporte->login_punto
                    || explode("T",$arrayExtraParams['fecha'])[0] == explode("T", $objSoporte->fecha_inicio)[0])
                    {
                        $arrayResultados[] = array( 'tarea_id' => $objSoporte->tarea_id,
                                            'login_punto' => $objSoporte->login_punto,
                                            'login_auxiliar' => $objSoporte->login_auxiliar,
                                            'motivo_soporte' => $objSoporte->motivo_soporte,
                                            'solucion' => $objSoporte->solucion,
                                            'observacion' => $objSoporte->observacion,
                                            'fecha_inicio' => date("d/m/Y", strtotime($objSoporte->fecha_inicio)+100000),
                                            'fecha_fin' => date("d/m/Y", strtotime($objSoporte->fecha_fin) + 100000),
                                            'minutos_soporte' =>  $objSoporte->minutos_soporte,
                                            'minutos_en_horas' =>  floor($objSoporte->minutos_soporte / 60).':'.
                                            ($objSoporte->minutos_soporte -floor($objSoporte->minutos_soporte / 60) * 60),
                                            'tiempo_soporte' => floor($objSoporte->minutos_soporte / 60).'h '.
                                            ($objSoporte->minutos_soporte -floor($objSoporte->minutos_soporte / 60) * 60) .'minutos',
                                            'tecnico_soporte' => $objSoporte->tecnico_soporte,
                                            'cliente_soporte' => $objRegistro->cliente_soporte,
                                            'estado_solicitud' => $strEstadoSolicitud,
                                            'tipo_tarea'=> $strInformacionTarea[0]['caso_id'],
                                            'nombre_tarea'=> $strInformacionTarea[0]['nombre_tarea']
                                        );
                    }
                }else
                {
                    $arrayResultados[] = array( 'tarea_id' => $objSoporte->tarea_id,
                                            'login_punto' => $objSoporte->login_punto,
                                            'login_auxiliar' => $objSoporte->login_auxiliar,
                                            'motivo_soporte' => $objSoporte->motivo_soporte,
                                            'solucion' => $objSoporte->solucion,
                                            'observacion' => $objSoporte->observacion,
                                            'fecha_inicio' => date("d/m/Y", strtotime($objSoporte->fecha_inicio)+100000),
                                            'fecha_fin' => date("d/m/Y", strtotime($objSoporte->fecha_fin)+100000),
                                            'minutos_soporte' =>  $objSoporte->minutos_soporte,
                                            'minutos_en_horas' =>  floor($objSoporte->minutos_soporte / 60).':'.
                                            ($objSoporte->minutos_soporte -floor($objSoporte->minutos_soporte / 60) * 60),
                                            'tiempo_soporte' => floor($objSoporte->minutos_soporte / 60).'h '.
                                            ($objSoporte->minutos_soporte -floor($objSoporte->minutos_soporte / 60) * 60) .'minutos',
                                            'tecnico_soporte' => $objSoporte->tecnico_soporte,
                                            'cliente_soporte' => $objRegistro->cliente_soporte,
                                            'estado_solicitud' => $strEstadoSolicitud,
                                            'tipo_tarea'=> $strInformacionTarea[0]['caso_id'],
                                            'nombre_tarea'=> $strInformacionTarea[0]['nombre_tarea']
                                        );
                }

                
            }
        }

        if(!empty($arrayResultados))
        {
            $arraySoportesJson    = json_encode($arrayResultados);
            $objJson = '{"total":"' . count($arrayResultados) . '","listado":' . $arrayPuntosJson . '}';
        }

        $objRespuesta->setContent($arraySoportesJson);

        return $objRespuesta;
    }

    /**
     * Función que genera una solicitud de ajuste de tiempo
     * a un soporte del paquete.
     * @author Jonathan Quintana <jiquintana@telconet.ec>
     * @version 1.0
     * @since 22-12-2022
     */
    public function putSolAjusteTiempoSoporteAction()
    {
        $objPeticion = $this->get('request');
        $objSession = $objPeticion->getSession();

        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');

        $emSoporte = $this->getDoctrine()->getManager();

        $arrayParametros['idServicio'] = $objPeticion->get('idServicio');

        $arrayParametros['uuidPaquete'] = $emSoporte->getRepository('schemaBundle:GestionPaqueteSoporte')
        ->obtenerUuidPaquete($arrayParametros['idServicio'])[0]['strUuidPaquete'];
        $arrayParametros['tareaId'] = $objPeticion->get('tareaId');
        $arrayParametros['motivoId'] = $objPeticion->get('motivoId');
        $arrayParametros['minutosSoporte'] = $objPeticion->get('minutosSoporte');
        $arrayParametros['observacion'] = $objPeticion->get('observacion');
        $arrayParametros['usuarioSolicita'] = $objPeticion->get('usuarioSolicita');
    
        $serviceGestionPaqueteSoporte = $this->get('soporte.GestionPaqueteSoporte');

        $arrayRespuesta = $serviceGestionPaqueteSoporte->putSolAjusteTiempoSoporte($arrayParametros);

        $objRespuesta->setContent(json_encode($arrayRespuesta['informacion']));

        return $objRespuesta;
    }

    /**
     * Función que obtiene las solicitudes por punto y por
     * tipo de solicitud.
     * @author Jonathan Quintana <jiquintana@telconet.ec>
     * @version 1.0
     * @since 22-12-2022
     */
    public function getSolicitudesPuntoTipoAction()
    {
        $objPeticion = $this->get('request');
        $objSession = $objPeticion->getSession();

        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');

        $emComercial = $this->getDoctrine()->getManager();

        $arrayParametros = array();
        $arrayParametros['tipo'] = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
        ->getIdTipoSolicitud('SOLICITUD ACTUALIZACIÓN HORAS DE SOPORTE')[0]['idTipoSolicitud'];
        $arrayParametros['login'] = $objSession->get('ptoCliente')['login'];
        $strCliente = $objSession->get('cliente')['razon_social'];

        $arrayParametros['fecha_desde'] = $objPeticion->get('fecha_desde');
        $arrayParametros['fecha_hasta'] = $objPeticion->get('fecha_hasta');

        $serviceGestionPaqueteSoporte = $this->get('soporte.GestionPaqueteSoporte');

        $arraySolicitudesEncontradas = array();   
        
        $arrayResultado = $serviceGestionPaqueteSoporte->getSolicitudesPuntoTipo($arrayParametros);
        $objSolicitudes              = $arrayResultado['registros'];
        $intTotal               = $arrayResultado['total'];
        if($objSolicitudes)
        {
            foreach($objSolicitudes as $objSolicitud)
            {

                $strValor = json_decode($emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                ->getValorPorDetalleSolicitudId($objSolicitud['id_detalle_solicitud'])['valor']);

                $strUuid = $strValor->uuidPaqueteSoporte;
                $strTareaId = $strValor->tareaId;
                $strMinutos = $strValor->minutos;



                $strLoginAux = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                ->getLoginAuxPorIdServicio($objSolicitud['servicio_id'])['loginAux'];


                 $arraySolicitudesEncontradas[] = array('id_detalle_solicitud' => $objSolicitud['id_detalle_solicitud'],
                                                        'servicio_id' => $objSolicitud['servicio_id'],
                                                        'tipo_solicitud_id' => $objSolicitud['tipo_solicitud_id'],
                                                        'motivo_id' => $objSolicitud['motivo_id'],
                                                        'usr_creacion' => $objSolicitud['usr_creacion'],
                                                        'fe_creacion' => date("d/m/Y", strtotime($objSolicitud['fe_creacion']) ),
                                                        'observacion' => $objSolicitud['observacion'],
                                                        'estado' => $objSolicitud['estado'],
                                                        'usr_rechazo' => $objSolicitud['usr_rechazo'],
                                                        'fe_rechazo' => date("d/m/Y", strtotime($objSolicitud['fe_rechazo']) ),
                                                        'fe_ejecucion' => date("d/m/Y", strtotime($objSolicitud['fe_ejecucion'])),
                                                        'login_aux' => $strLoginAux,
                                                        'tarea_id' => $strTareaId,
                                                       // 'minutos' => $strMinutos,
                                                        'minutos' => floor($strMinutos / 60).'h '.
                                                        ($strMinutos -floor($strMinutos / 60) * 60) .'minutos',
                                                        'cliente' => $strCliente,
                                                        'login' => $arrayParametros['login'],
                                                        'detalle_proceso_id' => $objSolicitud['detalle_proceso_id'],
                                                        'nombre_producto' => $objSolicitud['nombre_producto']);

            }
        }
       
        if(!empty($arraySolicitudesEncontradas))
        {            
            $arrayPuntosJson    = json_encode($arraySolicitudesEncontradas);
            $objJson = '{"total":"' . $intTotal . '","listado":' . $arrayPuntosJson . '}';
        }

        $objRespuesta->setContent($objJson);


        return $objRespuesta;
    }

    /**
     * Función que gestiona la aceptación o rechazo de una solicitud
     * de ajuste de tiempo a un soporte del paquete.
     * @author Jonathan Quintana <jiquintana@telconet.ec>
     * @version 1.0
     * @since 22-12-2022
     */
    public function putAprobarSolAjstTiempoSoporteAction()
    {
        $objPeticion = $this->get('request');
        $objSession = $objPeticion->getSession();

        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');

        $serviceGestionPaqueteSoporte = $this->get('soporte.GestionPaqueteSoporte');

        $arrayParametros = array();

        $arrayParametros['idDetalleSolicitud'] = $objPeticion->get('id_detalle_solicitud');
        $arrayParametros['servicioId'] = $objPeticion->get('servicio_id');
        $arrayParametros['tipoSolicitudId'] = $objPeticion->get('tipo_solicitud_id');
        $arrayParametros['motivoId'] = $objPeticion->get('motivo_id');
        $arrayParametros['estado'] = $objPeticion->get('estado');
        $arrayParametros['observacion'] = $objPeticion->get('observacion');
        $arrayParametros['userGestion'] = $objPeticion->get('user_gestion');
        $arrayParametros['feRechazo'] = $objPeticion->get('fe_rechazo');
        $arrayParametros['detalleProcesoId'] = $objPeticion->get('detalle_proceso_id');
        $arrayParametros['feEjecucion'] = $objPeticion->get('fe_ejecucion');

        $arrayResultado = $serviceGestionPaqueteSoporte->putAprobarSolAjstTiempoSoporte($arrayParametros);

        $objRespuesta->setContent(json_encode($arrayResultado));

        return $objRespuesta;
    }
}
