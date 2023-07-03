<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\tecnicoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use telconet\schemaBundle\Entity\InfoDetalleElemento;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElementoUbica;
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Entity\InfoInterfaceElemento;
use telconet\schemaBundle\Entity\InfoIp;
use telconet\schemaBundle\Entity\InfoPunto;
use telconet\schemaBundle\Entity\InfoPuntoDatoAdicional;
use telconet\schemaBundle\Entity\InfoRelacionElemento;
use telconet\schemaBundle\Entity\InfoServicio;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoServicioProdCaract;
use telconet\schemaBundle\Entity\InfoServicioTecnico;
use telconet\schemaBundle\Entity\InfoUbicacion;
use telconet\schemaBundle\Form\InfoElementoRouterClienteType;

/**
 * InfoElementoRouterClienteController
 *
 * logica de negocio de los elementos 
 *
 * @author John Vera <javera@telconet.ec>
 * @version 1.0 09-03-2016
 */

class InfoElementoRouterClienteController extends Controller
{ 
    
    /**
    * indexRouterClienteAction
    * funcion que valida los permisos y renderiza el index de la administración
    *
    * @author John Vera <javera@telconet.ec>
    * @version 1.0 04-05-2016
    */ 
    
    public function indexAction(){
        
        $rolesPermitidos = array();
        
        if (true === $this->get('security.context')->isGranted('ROLE_341-3919'))
        {
                $rolesPermitidos[] = 'ROLE_350-4057'; //NewRouterCliente
        }
        if (true === $this->get('security.context')->isGranted('ROLE_350-4058'))
        {
                $rolesPermitidos[] = 'ROLE_350-4058'; //EditRouterCliente
        }
        if (true === $this->get('security.context')->isGranted('ROLE_350-4077'))
        {
                $rolesPermitidos[] = 'ROLE_350-4077'; //eliminar elemento 
        }
        if (true === $this->get('security.context')->isGranted('ROLE_350-4157'))
        {
                $rolesPermitidos[] = 'ROLE_350-4157'; //ips
        }          
        
        return $this->render('tecnicoBundle:InfoElementoRouterCliente:index.html.twig', array(
                             'rolesPermitidos' => $rolesPermitidos
        ));
    }
    
    /**
    * newAction
    * renderiza el formulario para un ingreso nuevo
    * 
    * @author John Vera <javera@telconet.ec>
    * @version 1.0 14-01-2016
    */ 
    
    public function newAction()
    {
        $request    = $this->get('request');
        $session    = $request->getSession();
        $empresaId  = $session->get('idEmpresa');
        $entity     = new InfoElemento();
        $form       = $this->createForm(new InfoElementoRouterClienteType(array("empresaId"=>$empresaId)), $entity);

        return $this->render('tecnicoBundle:InfoElementoRouterCliente:new.html.twig', array(
                             'entity' => $entity,
                             'form'   => $form->createView()
        ));
    }
    
    /**
    * existenteAction
    * renderiza el formulario para crear una relacion entre el nodo y router existente
    * 
    * @author John Vera <javera@telconet.ec>
    * @version 1.0 22-07-2016
    */ 
    
    public function existenteAction()
    {
        $request    = $this->get('request');
        $session    = $request->getSession();
        $empresaId  = $session->get('idEmpresa');
        $entity     = new InfoElemento();
        $form       = $this->createForm(new InfoElementoRouterClienteType(array("empresaId"=>$empresaId)), $entity);

        return $this->render('tecnicoBundle:InfoElementoRouterCliente:existente.html.twig', array(  'entity' => $entity,
                                                                                                    'form'   => $form->createView()
        ));
    } 
    
    
    /**
     * createExistenteAction
     * función que relaciona el elemento con el nodo wifi
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 22-07-2016
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.1 08-08-2016 correccion del seteo de elemento cliente id en la información técnica
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.2 22-08-2016 Se seteo la cantidad en el registro del servicio a 1
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.3 31-08-2016  activacion de servicio wifi con web service de networking
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.4 20-09-2016  la tabla servicio tecnico al menos debe tener el id del elemento y la interface
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.5 16-11-2016   ingreso de tipo de factibilidad y detalle de interface por servicio
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.6 30-03-2017  cambio de validacion en el ingreso del tipo de factibilidad
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.7 07-04-2017  Se quita el ingreso del detalle interface porque ya se realiza en la activacion
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.8 19-09-2018 - Se agrega la validación de las coordenadas de latitud y longitud de acuerdo al país en sesión
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.9 02-05-2019 - Se agrega funcionalidad para que se agregue la característica "RELACION_INTERNET_WIFI"
     * al concentrador y asi poder relacionarlo con el servicio de internet wifi.
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version  2.0 19-06-2019 - Se modifica definición de variable $canton, para que pueda tomar el valor de Provincias cuando
     *                            sea necesario.
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 2.1 03-12-2019 - Se modifica lógica para que cuando el servicio tradicional, tenga UM 'Radio', los concentradores
     *                           de administración y navegación tengan la misma UM (Radio).
     */
    public function createExistenteAction()
    {
        $request            = $this->get('request');
        $em                 = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emComercial        = $this->get('doctrine')->getManager('telconet');

        $session            = $request->getSession();
        $empresaId          = $session->get('idEmpresa');
        $capacidad          = $request->request->get('capacidad');
        $idElemento         = $request->request->get('id_elemento');
        $idNodoWifi         = $request->request->get('id_nodo');
        $idPunto            = $request->request->get('id_punto');
        $idServicio         = $request->request->get('id_servicio');

        $em->beginTransaction();
        $emComercial->beginTransaction();   
        
        try
        {
            
            $objNodo = $em->getRepository('schemaBundle:InfoElemento')->find($idNodoWifi);
            
            $elemento = $em->getRepository('schemaBundle:InfoElemento')->find($idElemento);
            
            $entityServicioTecnico = $em->getRepository('schemaBundle:InfoServicioTecnico')->findOneByServicioId($idServicio);
            $objUMServTrad = $em->getRepository('schemaBundle:AdmiTipoMedio')
                                ->find($entityServicioTecnico->getUltimaMillaId());

            if(!is_object($entityServicioTecnico))
            {
                throw new Exception('El servicio no tiene elemento información técnica.');
            }
            
            if($idServicio)
            {
                $objServicio = $em->getRepository('schemaBundle:InfoServicio')->find($idServicio);
                if($objServicio)
                {
                    $objProductoServicio = $objServicio->getProductoId();
                    if($objProductoServicio)
                    {
                        $intIdProductoServicio = $objProductoServicio->getId();
                    }
                }
            }
            //relacion elemento
            $relacionElemento = new InfoRelacionElemento();
            
            if($objNodo)
            {            
                $relacionElemento->setElementoIdA($objNodo->getId());
            }
            if($elemento)
            {
                $relacionElemento->setElementoIdB($elemento->getId());
            }
            $relacionElemento->setTipoRelacion("CONTIENE");
            $relacionElemento->setObservacion("Nodo Wifi contiene Router");
            $relacionElemento->setEstado("Activo");
            $relacionElemento->setUsrCreacion($session->get('user'));
            $relacionElemento->setFeCreacion(new \DateTime('now'));
            $relacionElemento->setIpCreacion($request->getClientIp());
            $em->persist($relacionElemento);
            
            //compruebo si tiene ubicacion sino le creo
            $empresaUbicacion = $em->getRepository('schemaBundle:InfoEmpresaElementoUbica')->findOneByElementoId($idElemento);
            
            if(!$empresaUbicacion)
            {
                if($objNodo) 
                {
                    //tomar datos nodo
                    $nodoEmpresaElementoUbicacion = $em->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                                                       ->findOneBy(array("elementoId" => $objNodo->getId()));
                    if($nodoEmpresaElementoUbicacion)
                    {
                        $nodoUbicacion = $em->getRepository('schemaBundle:InfoUbicacion')->find($nodoEmpresaElementoUbicacion->getUbicacionId()->getId());
                    }
                }
                
                if($nodoUbicacion)
                {
                    $serviceInfoElemento        = $this->get("tecnico.InfoElemento");
                    $arrayRespuestaCoordenadas  = $serviceInfoElemento->validarLimitesCoordenadasElemento(array(
                                                                                                        "latitudElemento"       => 
                                                                                                        $nodoUbicacion->getLatitudUbicacion(),
                                                                                                        "longitudElemento"      => 
                                                                                                        $nodoUbicacion->getLongitudUbicacion(),
                                                                                                        "msjTipoElemento"       => "del nodo wifi ",
                                                                                                        "msjTipoElementoPadre"  =>
                                                                                                        "que contiene al router del cliente ",
                                                                                                        "msjAdicional"          => 
                                                                                                        "por favor regularizar en la administración"
                                                                                                        ." de Nodos Wifi"
                                                                                                     ));
                    if($arrayRespuestaCoordenadas["status"] === "ERROR")
                    {
                        throw new \Exception($arrayRespuestaCoordenadas['mensaje']);
                    }
                    //info ubicacion
                    $parroquia = $em->find('schemaBundle:AdmiParroquia', $nodoUbicacion->getParroquiaId());
                    $ubicacionElemento = new InfoUbicacion();
                    $ubicacionElemento->setLatitudUbicacion($nodoUbicacion->getLatitudUbicacion());
                    $ubicacionElemento->setLongitudUbicacion($nodoUbicacion->getLongitudUbicacion());
                    $ubicacionElemento->setDireccionUbicacion($nodoUbicacion->getDireccionUbicacion());
                    $ubicacionElemento->setAlturaSnm($nodoUbicacion->getAlturaSnm());
                    $ubicacionElemento->setParroquiaId($parroquia);
                    $ubicacionElemento->setUsrCreacion($session->get('user'));
                    $ubicacionElemento->setFeCreacion(new \DateTime('now'));
                    $ubicacionElemento->setIpCreacion($request->getClientIp());
                    $em->persist($ubicacionElemento);
                }

                //empresa elemento ubicacion
                $empresaElementoUbica = new InfoEmpresaElementoUbica();
                $empresaElementoUbica->setEmpresaCod($session->get('idEmpresa'));
                $empresaElementoUbica->setElementoId($elemento);
                $empresaElementoUbica->setUbicacionId($ubicacionElemento);
                $empresaElementoUbica->setUsrCreacion($session->get('user'));
                $empresaElementoUbica->setFeCreacion(new \DateTime('now'));
                $empresaElementoUbica->setIpCreacion($request->getClientIp());
                $em->persist($empresaElementoUbica);

                //empresa elemento
                $empresaElemento = new InfoEmpresaElemento();
                $empresaElemento->setElementoId($elemento);
                $empresaElemento->setEmpresaCod($session->get('idEmpresa'));
                $empresaElemento->setEstado("Activo");
                $empresaElemento->setUsrCreacion($session->get('user'));
                $empresaElemento->setIpCreacion($request->getClientIp());
                $empresaElemento->setFeCreacion(new \DateTime('now'));
                $em->persist($empresaElemento);
            }

            //cambiar estado a solicitud
            $objTipoSolicitud = $this->getDoctrine()->getManager()->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                                  ->findOneBy(array('descripcionSolicitud'  => 'SOLICITUD NODO WIFI',
                                                                                    'estado'                => 'Activo'));            
            $objDetalleSolicitud = $em->getRepository('schemaBundle:InfoDetalleSolicitud')
                                      ->findOneBy(array('elementoId' => $objNodo->getId(), 'tipoSolicitudId' => $objTipoSolicitud->getId()));
            $idElementoA = $objDetalleSolicitud->getElementoId();

            $estadoSolicitud = 'PendientePunto';

            //actualizo la solicitud
            $objDetalleSolicitud->setEstado($estadoSolicitud);
            $em->persist($objDetalleSolicitud);
            $em->flush();

            //GUARDAR INFO DETALLE SOLICITUD HISTORIAL
            $objDetalleSolHist = new InfoDetalleSolHist();
            $objDetalleSolHist->setDetalleSolicitudId($objDetalleSolicitud);
            $objDetalleSolHist->setIpCreacion($request->getClientIp());
            $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
            $objDetalleSolHist->setUsrCreacion($request->getSession()->get('user'));
            $objDetalleSolHist->setEstado($estadoSolicitud);
            $em->persist($objDetalleSolHist);
            $em->flush();

            //actualizo el elemento
            $objElemento = $em->getRepository('schemaBundle:InfoElemento')->findOneById($idElementoA);
            $objElemento->setObservacion('Nodo wifi aprobada en la factibilidad por ' . $session->get('user'));
            $objElemento->setEstado("Activo");
            $em->persist($objDetalleSolicitud);
            $em->flush();

            //caracteristica para saber donde esta ubicada la router  (pedestal - edificio)
            $detalle = new InfoDetalleElemento();
            $detalle->setElementoId($elemento->getId());
            $detalle->setDetalleNombre("TIPO ELEMENTO RED");
            $detalle->setDetalleValor("WIFI");
            $detalle->setDetalleDescripcion("Caracteristicas para indicar que es un router de uso Wifi");
            $detalle->setFeCreacion(new \DateTime('now'));
            $detalle->setUsrCreacion($session->get('user'));
            $detalle->setIpCreacion($request->getClientIp());
            $detalle->setEstado('Activo');
            $em->persist($detalle);
            $em->flush();
                        
            //caracteristica para saber donde esta ubicada la router  (pedestal - edificio)
            $detalle1 = new InfoDetalleElemento();
            $detalle1->setElementoId($elemento->getId());
            $detalle1->setDetalleNombre("CAPACIDAD");
            $detalle1->setDetalleValor($capacidad);
            $detalle1->setDetalleDescripcion("Capacidad del elemento en Kb ");
            $detalle1->setFeCreacion(new \DateTime('now'));
            $detalle1->setUsrCreacion($session->get('user'));
            $detalle1->setIpCreacion($request->getClientIp());
            $detalle1->setEstado('Activo');
            $em->persist($detalle1);
            $em->flush();
            
            //caracteristica para saber a que punto está relacionado este NODO WIFI
            $detalle2 = new InfoDetalleElemento();
            $detalle2->setElementoId($objNodo->getId());
            $detalle2->setDetalleNombre("ID_PUNTO");
            $detalle2->setDetalleValor($idPunto);
            $detalle2->setDetalleDescripcion("Indica relacion con el punto. ");
            $detalle2->setFeCreacion(new \DateTime('now'));
            $detalle2->setUsrCreacion($session->get('user'));
            $detalle2->setIpCreacion($request->getClientIp());
            $detalle2->setEstado('Activo');
            $em->persist($detalle2);
            $em->flush();            
            
            //historial elemento
            $historialElemento = new InfoHistorialElemento();
            $historialElemento->setElementoId($elemento);
            $historialElemento->setEstadoElemento("Activo");
            $historialElemento->setObservacion('Se asignó al Nodo Wifi '.$objNodo->getNombreElemento());
            $historialElemento->setUsrCreacion($session->get('user'));
            $historialElemento->setFeCreacion(new \DateTime('now'));
            $historialElemento->setIpCreacion($request->getClientIp());
            $em->persist($historialElemento);
            $em->flush();
            
            $objPunto = $emComercial ->getRepository('schemaBundle:InfoPunto')->find($idPunto);
            
            $ObjProducto = $emComercial ->getRepository('schemaBundle:AdmiProducto')->findOneBy(array('descripcionProducto'=>'L3MPLS', 
                                                                                                      'nombreTecnico'      =>'L3MPLS'));
            $objProdWifi = $emComercial->getRepository('schemaBundle:AdmiProducto')->findOneBy(
                                                                                                array(  'descripcionProducto'=>'INTERNET WIFI',
                                                                                                        'nombreTecnico'=>'INTERNET WIFI'));
            $objServWifi = $emComercial->getRepository('schemaBundle:InfoServicio')
                ->findOneBy(array(
                'puntoId'=>$objPunto->getId(),
                'estado'=>'FactibilidadEnProceso',
                'productoId'=>$objProdWifi->getId()
                ));

            //ingreso tipo de factibilidad DIRECTA
            $objCaracteristicaFact = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                 ->findOneBy(array("descripcionCaracteristica" => 'TIPO_FACTIBILIDAD',
                                                                   "estado"                    => "Activo"));
            $objProdCaractFact = null;
            if(is_object($objCaracteristicaFact))
            {
                if($intIdProductoServicio)
                {
                    $objProdCaractFact = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                     ->findOneBy(array("caracteristicaId" => $objCaracteristicaFact->getId(),
                                                                             "productoId" => $intIdProductoServicio));
                }                
                $objProdCaractFactNew = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                    ->findOneBy(array("caracteristicaId" => $objCaracteristicaFact->getId(),
                                                                            "productoId" => $ObjProducto->getId()));                
                $strValor = '';
                if(is_object($objProdCaractFact))
                {
                    //servicio prod caract
                    $objSpcFact = $emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                              ->findOneBy(array("productoCaracterisiticaId" => $objProdCaractFact->getId(),
                                                                "servicioId"                => $idServicio,
                                                                "estado"                    => "Activo"));
                    if(is_object($objSpcFact))
                    {
                        $strValor = $objSpcFact->getValor();
                    }
                }
                
                $strTipoElemento = '';
                //si el servicio no tiene caracteristica factibilidad, verifico la data tecnica para determinar si es RUTA o DIRECTO
                if($strValor == '')
                {
                    if($entityServicioTecnico->getElementoClienteId())
                    {
                        $objElementoCliente = $em->getRepository('schemaBundle:InfoElemento')
                                                 ->find($entityServicioTecnico->getElementoClienteId());
                        if(is_object($objElementoCliente))
                        {
                            if(is_object($objElementoCliente->getModeloElementoId()))
                            {
                                $objTipoElemento = $objElementoCliente->getModeloElementoId()->getTipoElementoId();
                                if(is_object($objTipoElemento))
                                {
                                    $strTipoElemento = $objTipoElemento->getNombreTipoElemento();
                                }
                            }
                        }
                    }
                    else
                    {
                        throw new Exception('El servicio no tiene elemento cliente.');
                    }

                    //si el servicio tecnico no tiene elemento conector y el elemento cliente id es ROUTER o CPE significa que es directo
                    if(!$entityServicioTecnico->getElementoConectorId() && ($strTipoElemento == 'ROUTER' || $strTipoElemento == 'CPE'))
                    {
                        $strValor = 'DIRECTO';
                    }
                    else
                    {
                        $strValor = 'RUTA';
                    }

                    if(is_object($objProdCaractFact) && is_object($objServicio))
                    {
                        //creo al servicio la spc
                        $objSpcFact = new InfoServicioProdCaract();
                        $objSpcFact->setServicioId($objServicio->getId());
                        $objSpcFact->setProductoCaracterisiticaId($objProdCaractFact->getId());
                        $objSpcFact->setValor($strValor);
                        $objSpcFact->setFeCreacion(new \DateTime('now'));
                        $objSpcFact->setUsrCreacion($session->get('user'));
                        $objSpcFact->setEstado("Activo");
                        $emComercial->persist($objSpcFact);
                        $emComercial->flush();
                    }
                }
            }

            //dependiendo del modelo del nodo verifico la ultima milla
            if($objNodo->getModeloElementoId()->getNombreModeloElemento() == 'BACKBONE')
            {
                $strUltimaMilla = 'UTP';
            }
            else
            {
                $strUltimaMilla = 'FO';
            }

            /*Para los servicios tradicionales que posean UM Radio aplicara la misma UM
            para qe se pueda asignar los recursos de red.*/
            if (is_object($objUMServTrad) && $objUMServTrad->getNombreTipoMedio() == 'Radio')
            {
                $strUltimaMilla = $objUMServTrad->getCodigoTipoMedio();
            }

            $objTipoMedio = $emComercial ->getRepository('schemaBundle:AdmiTipoMedio')->findOneByCodigoTipoMedio($strUltimaMilla);
            
            $objServicio = new InfoServicio();
            $objServicio->setPuntoId($objPunto);
            $objServicio->setProductoId($ObjProducto);
            $objServicio->setEsVenta('N');
            $objServicio->setPrecioVenta(0);
            $objServicio->setCantidad(1);
            $objServicio->setTipoOrden('N');
            $objServicio->setEstado('AsignadoTarea');
            $objServicio->setFrecuenciaProducto(1);
            $objServicio->setDescripcionPresentaFactura('Concentrador L3MPLS Administracion');
            $objServicio->setUsrCreacion($session->get('user'));
            $objServicio->setFeCreacion(new \DateTime('now'));
            $objServicio->setIpCreacion($request->getClientIp());
            $emComercial->persist($objServicio);
            $emComercial->flush(); 
            //si se obtiene los datos del producto característica se inserta en el servicio
            if(is_object($objProdCaractFactNew))
            {
                //inserto la servicio prod caract
                $objServicioProdCaract = new InfoServicioProdCaract();
                $objServicioProdCaract->setServicioId($objServicio->getId());
                $objServicioProdCaract->setProductoCaracterisiticaId($objProdCaractFactNew->getId());
                $objServicioProdCaract->setValor($strValor);
                $objServicioProdCaract->setFeCreacion(new \DateTime('now'));
                $objServicioProdCaract->setUsrCreacion($session->get('user'));
                $objServicioProdCaract->setEstado("Activo");
                $emComercial->persist($objServicioProdCaract);
                $emComercial->flush();
            }            
                      
            //historial del servicio
            $servicioHistorial = new InfoServicioHistorial();
            $servicioHistorial->setServicioId($objServicio);
            $servicioHistorial->setObservacion("Se creo el servicio.");
            $servicioHistorial->setEstado('AsignadoTarea');
            $servicioHistorial->setUsrCreacion($session->get('user'));
            $servicioHistorial->setFeCreacion(new \DateTime('now'));
            $servicioHistorial->setIpCreacion($request->getClientIp());
            $emComercial->persist($servicioHistorial);
            $emComercial->flush();          
            
            $serviceServicioGeneral = $this->get('tecnico.InfoServicioTecnico');            
                       
            //se estableció con el usuario que la capacidad sea 14Kb
            $serviceServicioGeneral->ingresarServicioProductoCaracteristica($objServicio, $ObjProducto, "CAPACIDAD1", '14', 
                                                                            $session->get('user'));
            $serviceServicioGeneral->ingresarServicioProductoCaracteristica($objServicio, $ObjProducto, "CAPACIDAD2", '14', 
                                                                            $session->get('user'));
            // Se agrega la característica que relaciona el concentrador con el servicio de internet wifi.
            $serviceServicioGeneral->ingresarServicioProductoCaracteristica($objServicio,
                                                                            $ObjProducto,
                                                                            "RELACION_INTERNET_WIFI",
                                                                            $objServWifi->getId(),
                                                                            $session->get('user'));
                        
            //obtener jurisdiccion
            $objParroquia = $emComercial->getRepository('schemaBundle:AdmiParroquia')->find($objPunto->getSectorId()->getParroquiaId());
            if($objParroquia)
            {
                /*Si el canton es Guayaquil o Quito, la variable tomará este valor, caso contrario sera Provincias*/
                $strCanton = $objParroquia->getCantonId()->getNombreCanton() == 'GUAYAQUIL'
                       || $objParroquia->getCantonId()->getNombreCanton() == 'QUITO'
                        ? $objParroquia->getCantonId()->getNombreCanton() : 'PROVINCIAS';

                $arrayParametros = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                               ->getOne('ENLACE_DATOS_WIFI', 'TECNICO', '', '', $strCanton, '', '', '', '', $empresaId);
                $objServicioEnlace = null;
                if($arrayParametros['valor2'])
                {
                    $objServicioEnlace = $emComercial->getRepository('schemaBundle:InfoServicio')->findOneByLoginAux($arrayParametros['valor2']);

                    if($objServicioEnlace)
                    {
                        $serviceServicioGeneral->ingresarServicioProductoCaracteristica($objServicio, $ObjProducto, "ENLACE_DATOS", 
                                                                                        $objServicioEnlace->getId(), $session->get('user'));

                    }
                }
            }
            
            $objServicioTecnico  = new InfoServicioTecnico();
            
            if($entityServicioTecnico)
            {
                //la tabla servicio tecnico al menos debe tener el id del elemento y la interface
                if($entityServicioTecnico->getElementoId() && $entityServicioTecnico->getInterfaceElementoId())
                {                
                    $objServicioTecnico->setElementoId($entityServicioTecnico->getElementoId());
                    $objServicioTecnico->setInterfaceElementoId($entityServicioTecnico->getInterfaceElementoId());
                    $objServicioTecnico->setElementoContenedorId($entityServicioTecnico->getElementoContenedorId());
                    $objServicioTecnico->setElementoConectorId($entityServicioTecnico->getElementoConectorId());
                    $objServicioTecnico->setInterfaceElementoConectorId($entityServicioTecnico->getInterfaceElementoConectorId());
                    $objServicioTecnico->setInterfaceElementoClienteId($entityServicioTecnico->getInterfaceElementoClienteId());
                    $objServicioTecnico->setElementoClienteId($entityServicioTecnico->getElementoClienteId());
                }
                else
                {
                    throw new \Exception('El servicio tiene la data técnica incompleta, favor revisar.');                 
                }
            }
            
            
            $objServicioTecnico->setServicioId($objServicio);
            $objServicioTecnico->setTipoEnlace('PRINCIPAL');           
            $objServicioTecnico->setUltimaMillaId($objTipoMedio->getId());
            $emComercial->persist($objServicioTecnico);
            $emComercial->flush();
            
            //creo la solicitud de planificacion y el historial para que pase directo a asignar recursos de red
            $entityTipoSolicitud =$emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')->findOneByDescripcionSolicitud("SOLICITUD PLANIFICACION");

            $entitySolicitud  = new InfoDetalleSolicitud();
            $entitySolicitud->setServicioId($objServicio);
            $entitySolicitud->setTipoSolicitudId($entityTipoSolicitud);	
            $entitySolicitud->setEstado("AsignadoTarea");
            $entitySolicitud->setUsrCreacion($session->get('user'));		
            $entitySolicitud->setFeCreacion(new \DateTime('now'));

            $emComercial->persist($entitySolicitud);
            $emComercial->flush();

            //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
            $entityDetalleSolHist = new InfoDetalleSolHist();
            $entityDetalleSolHist->setDetalleSolicitudId($entitySolicitud);
            $entityDetalleSolHist->setIpCreacion($request->getClientIp());
            $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
            $entityDetalleSolHist->setUsrCreacion($session->get('user'));
            $entityDetalleSolHist->setEstado('AsignadoTarea');  

            $emComercial->persist($entityDetalleSolHist);
            $emComercial->flush(); 
            
            //ingreso el segundo servicio
            $objServicio2 = new InfoServicio();
            $objServicio2->setPuntoId($objPunto);
            $objServicio2->setProductoId($ObjProducto);
            $objServicio2->setEsVenta('N');
            $objServicio2->setPrecioVenta(0);
            $objServicio2->setCantidad(1);
            $objServicio2->setTipoOrden('N');
            $objServicio2->setEstado('AsignadoTarea');
            $objServicio2->setFrecuenciaProducto(1);
            $objServicio2->setDescripcionPresentaFactura('Concentrador L3MPLS Navegacion');
            $objServicio2->setUsrCreacion($session->get('user'));
            $objServicio2->setFeCreacion(new \DateTime('now'));
            $objServicio2->setIpCreacion($request->getClientIp());
            $emComercial->persist($objServicio2);
            $emComercial->flush(); 
            
            //si se obtiene los datos del producto característica se inserta en el servicio
            if(is_object($objProdCaractFactNew))
            {
                //inserto la servicio prod caract
                $objServicioProdCaract = new InfoServicioProdCaract();
                $objServicioProdCaract->setServicioId($objServicio2->getId());
                $objServicioProdCaract->setProductoCaracterisiticaId($objProdCaractFactNew->getId());
                $objServicioProdCaract->setValor($strValor);
                $objServicioProdCaract->setFeCreacion(new \DateTime('now'));
                $objServicioProdCaract->setUsrCreacion($session->get('user'));
                $objServicioProdCaract->setEstado("Activo");
                $emComercial->persist($objServicioProdCaract);
                $emComercial->flush();
            }            
                       
            //historial del servicio
            $servicioHistorial2 = new InfoServicioHistorial();
            $servicioHistorial2->setServicioId($objServicio2);
            $servicioHistorial2->setObservacion("Se creo el servicio.");
            $servicioHistorial2->setEstado('AsignadoTarea');
            $servicioHistorial2->setUsrCreacion($session->get('user'));
            $servicioHistorial2->setFeCreacion(new \DateTime('now'));
            $servicioHistorial2->setIpCreacion($request->getClientIp());
            $emComercial->persist($servicioHistorial2);
            $emComercial->flush(); 
            
            
            $entitySolicitud1  = new InfoDetalleSolicitud();
            $entitySolicitud1->setServicioId($objServicio2);
            $entitySolicitud1->setTipoSolicitudId($entityTipoSolicitud);	
            $entitySolicitud1->setEstado("AsignadoTarea");
            $entitySolicitud1->setUsrCreacion($session->get('user'));		
            $entitySolicitud1->setFeCreacion(new \DateTime('now'));

            $emComercial->persist($entitySolicitud1);
            $emComercial->flush();  

            //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
            $entityDetalleSolHist1 = new InfoDetalleSolHist();
            $entityDetalleSolHist1->setDetalleSolicitudId($entitySolicitud1);
            $entityDetalleSolHist1->setIpCreacion($request->getClientIp());
            $entityDetalleSolHist1->setFeCreacion(new \DateTime('now'));
            $entityDetalleSolHist1->setUsrCreacion($session->get('user'));
            $entityDetalleSolHist1->setEstado('AsignadoTarea');  

            $emComercial->persist($entityDetalleSolHist1);
            $emComercial->flush(); 
            
            $objServicioTecnico2  = new InfoServicioTecnico();
            
            if($entityServicioTecnico)
            {
                $objServicioTecnico2->setElementoId($entityServicioTecnico->getElementoId());
                $objServicioTecnico2->setInterfaceElementoId($entityServicioTecnico->getInterfaceElementoId());
                $objServicioTecnico2->setElementoContenedorId($entityServicioTecnico->getElementoContenedorId());
                $objServicioTecnico2->setElementoConectorId($entityServicioTecnico->getElementoConectorId());
                $objServicioTecnico2->setInterfaceElementoConectorId($entityServicioTecnico->getInterfaceElementoConectorId());
                $objServicioTecnico2->setInterfaceElementoClienteId($entityServicioTecnico->getInterfaceElementoClienteId());
                $objServicioTecnico2->setElementoClienteId($entityServicioTecnico->getElementoClienteId());

            }
                        
            $objServicioTecnico2->setServicioId($objServicio2);
            $objServicioTecnico2->setTipoEnlace('PRINCIPAL');           
            $objServicioTecnico2->setUltimaMillaId($objTipoMedio->getId());
            $emComercial->persist($objServicioTecnico2);
            $emComercial->flush();
            
            $serviceServicioGeneral->ingresarServicioProductoCaracteristica($objServicio2, $ObjProducto, "CAPACIDAD1", '14',
                                                                            $session->get('user'));
            $serviceServicioGeneral->ingresarServicioProductoCaracteristica($objServicio2, $ObjProducto, "CAPACIDAD2", '14', 
                                                                            $session->get('user'));
            // Se agrega la característica que relaciona el concentrador con el servicio de internet wifi.
            $serviceServicioGeneral->ingresarServicioProductoCaracteristica($objServicio2,
                                                                            $ObjProducto,
                                                                            "RELACION_INTERNET_WIFI",
                                                                            $objServWifi->getId(),
                                                                            $session->get('user'));
            
            if($objServicioEnlace)
            {
                $serviceServicioGeneral->ingresarServicioProductoCaracteristica($objServicio2, $ObjProducto, "ENLACE_DATOS", 
                                                                                $objServicioEnlace->getId(), $session->get('user'));

            }
            
            $emComercial->commit();
            $em->commit();
        }
        catch(\Exception $e)
        {
            if($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->rollback();
            }
            $emComercial->close();
            
            if($em->getConnection()->isTransactionActive())
            {
                $em->rollback();
            }
            $em->close();
            $mensajeError = "Error: " . $e->getMessage();
            $this->get('session')->getFlashBag()->add('notice', $mensajeError);
            return $this->redirect($this->generateUrl('elementoRouterCliente_existente'));
        }

        return $this->redirect($this->generateUrl('elementoRouterCliente_show', array('id' => $elemento->getId())));
    }    

    /**
     * createAction
     * función que crea el elemento en la base de datos
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 14-01-2016
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.1 27-06-2016 Se quito validacion para que no se haga masyuscula el nombre
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.2 30-06-2016 se crea info historial, se cambia de producto a l3mpls, se aumenta capcaidad de login, se ingresa enlace de datos, se 
     *                         se validad la ultima milla UTP 
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.3 07-07-2016 correccion al asignar el ENLACE_DATOS a un servicio
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.4 26-07-2016 se aumento en el detalle del elemento el id del punto
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.5 04-08-2016 Se estableció que el conteo de interfaces sea desde 0 y solo sean activas
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.6 22-08-2016 Se seteo la cantidad en el registro del servicio a 1
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.7 24-08-2016 Se implementa mac por interface
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.8 31-08-2016  activacion de servicio wifi con web service de networking
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.9 11-10-2016  Cuando se crea el elemento se lo procesa en el naf
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.0 19-09-2018 - Se agrega la validación de las coordenadas de latitud y longitud de acuerdo al país en sesión
     *
     */
    public function createAction()
    {
        $request = $this->get('request');
        $em                 = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emComercial        = $this->get('doctrine')->getManager('telconet');
        $elemento           = new InfoElemento();
        $form = $this->createForm(new InfoElementoRouterClienteType(), $elemento);
        $parametros         = $request->request->get('telconet_schemabundle_infoelementorouterclientetype');
        $capacidad          = $request->request->get('capacidad');
        $capacidadLogin     = $request->request->get('capacidadLogin');
        $mac                = $request->request->get('mac');
        $session            = $request->getSession();
        $empresaId          = $session->get('idEmpresa');
        $nombreElemento     = $parametros['nombreElemento'];
        $ipElemento         = $parametros['ipElemento'];
        $serieFisica        = $parametros['serieFisica'];
        $modeloElementoId   = $parametros['modeloElementoId'];
        $versionOs          = $parametros['versionOs'];
        $nombreNodoElementoId = $request->request->get('combo_nodos');
        $descripcionElemento = $parametros['descripcionElemento'];

        $em->beginTransaction();
        $emComercial->beginTransaction();
        try
        {
            $serviceServicioGeneral = $this->get('tecnico.InfoServicioTecnico');
            $ObjNodo = $em->getRepository('schemaBundle:InfoElemento')->findOneByNombreElemento($nombreNodoElementoId);

            $modeloElemento = $em->getRepository('schemaBundle:AdmiModeloElemento')->findOneById($modeloElementoId);

            //verificar que el nombre del elemento no se repita
            $elementoRepetido = $em->getRepository('schemaBundle:InfoElemento')
                                   ->findOneBy(array("nombreElemento" => $nombreElemento,
                                                             "estado" => array("Activo", "Pendiente", "Factible", "PreFactibilidad"),
                                                   "modeloElementoId" => $modeloElemento->getId()));

            if($elementoRepetido)
            {
                throw new \Exception('Nombre ya existe en otro Elemento con estado ' . $elementoRepetido->getEstado());
            }
            
            $elemento->setNombreElemento($nombreElemento);
            $elemento->setDescripcionElemento($descripcionElemento);
            $elemento->setModeloElementoId($modeloElemento);
            $elemento->setSerieFisica($serieFisica);
            $elemento->setVersionOs($versionOs);
            $elemento->setUsrResponsable($session->get('user'));
            $elemento->setUsrCreacion($session->get('user'));
            $elemento->setFeCreacion(new \DateTime('now'));
            $elemento->setIpCreacion($request->getClientIp());
            $elemento->setEstado("Activo");

            $em->persist($elemento);
            $em->flush();

            //buscar el interface Modelo
            $interfaceModelo = $em->getRepository('schemaBundle:AdmiInterfaceModelo')->findBy(array("modeloElementoId" => $modeloElementoId,
                                                                                                    "estado"           => "Activo"),
                                                                                              array("cantidadInterface"=> "ASC"));
                        
            if(!$interfaceModelo)
            {
                throw new \Exception('El modelo no tiene registrado interfaces');            
            }
            
            $i = 0;
            $fin = 0;
            foreach($interfaceModelo as $im)
            {
                $objTipoInterface = $em->getRepository('schemaBundle:AdmiTipoInterface')->find($im->getTipoInterfaceId());                
                $cantidadInterfaces = $im->getCantidadInterface();
                $formato = $im->getFormatoInterface();
                
                if($im->getClaseInterface() == 'Modular')
                {
                    $i = 0;
                    $fin = $cantidadInterfaces;
                }
                else
                {
                    $fin = $fin + $cantidadInterfaces;
                }                

                for($i = $i ; $i < $fin; $i++)
                {
                    $interfaceElemento = new InfoInterfaceElemento();
                    $format = explode("?", $formato);
                    $nombreInterfaceElemento = $format[0] . $i;
                    $interfaceElemento->setNombreInterfaceElemento($nombreInterfaceElemento);
                    $interfaceElemento->setDescripcionInterfaceElemento($objTipoInterface->getNombreTipoInterface());
                    $interfaceElemento->setElementoId($elemento);
                    $interfaceElemento->setEstado("not connect");
                    $interfaceElemento->setUsrCreacion($session->get('user'));
                    $interfaceElemento->setFeCreacion(new \DateTime('now'));
                    $interfaceElemento->setIpCreacion($request->getClientIp());
                    
                    if ($objTipoInterface->getNombreTipoInterface() == 'Wan' )
                    {
                        $interfaceElemento->setMacInterfaceElemento($mac);
                    }

                    $em->persist($interfaceElemento);
                }
            }

            //relacion elemento
            $relacionElemento = new InfoRelacionElemento();
            $relacionElemento->setElementoIdA($ObjNodo->getId());
            $relacionElemento->setElementoIdB($elemento->getId());
            $relacionElemento->setTipoRelacion("CONTIENE");
            $relacionElemento->setObservacion("Nodo Wifi contiene Router");
            $relacionElemento->setEstado("Activo");
            $relacionElemento->setUsrCreacion($session->get('user'));
            $relacionElemento->setFeCreacion(new \DateTime('now'));
            $relacionElemento->setIpCreacion($request->getClientIp());
            $em->persist($relacionElemento);

            //cambiar estado a solicitud
            $objTipoSolicitud = $this->getDoctrine()->getManager()->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                              ->findOneBy(array('descripcionSolicitud' => 'SOLICITUD NODO WIFI',
                                                                                'estado' => 'Activo'));            
            $objDetalleSolicitud = $em->getRepository('schemaBundle:InfoDetalleSolicitud')
                                       ->findOneBy(array('elementoId' => $ObjNodo->getId(), 'tipoSolicitudId' => $objTipoSolicitud->getId()));
            $idElementoA = $objDetalleSolicitud->getElementoId();

            $estadoSolicitud = 'PendientePunto';

            //actualizo la solicitud
            $objDetalleSolicitud->setEstado($estadoSolicitud);
            $em->persist($objDetalleSolicitud);
            $em->flush();

            //GUARDAR INFO DETALLE SOLICITUD HISTORIAL
            $objDetalleSolHist = new InfoDetalleSolHist();
            $objDetalleSolHist->setDetalleSolicitudId($objDetalleSolicitud);
            $objDetalleSolHist->setIpCreacion($request->getClientIp());
            $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
            $objDetalleSolHist->setUsrCreacion($request->getSession()->get('user'));
            $objDetalleSolHist->setEstado($estadoSolicitud);
            $em->persist($objDetalleSolHist);
            $em->flush();


            //actualizo el elemento
            $objElemento = $em->getRepository('schemaBundle:InfoElemento')->findOneById($idElementoA);
            $objElemento->setObservacion('Nodo wifi aprobada en la factibilidad por ' . $session->get('user'));
            $objElemento->setEstado("Pre-Servicio");
            $em->persist($objDetalleSolicitud);
            $em->flush();


            //tomar datos nodo
            $nodoEmpresaElementoUbicacion = $em->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                ->findOneBy(array("elementoId" => $ObjNodo->getId()));
            $nodoUbicacion = $em->getRepository('schemaBundle:InfoUbicacion')->find($nodoEmpresaElementoUbicacion->getUbicacionId()->getId());

            //info ubicacion
            $parroquia = $em->find('schemaBundle:AdmiParroquia', $nodoUbicacion->getParroquiaId());
            $serviceInfoElemento        = $this->get("tecnico.InfoElemento");
            $arrayRespuestaCoordenadas  = $serviceInfoElemento->validarLimitesCoordenadasElemento(array(
                                                                                                "latitudElemento"       => 
                                                                                                $nodoUbicacion->getLatitudUbicacion(),
                                                                                                "longitudElemento"      => 
                                                                                                $nodoUbicacion->getLongitudUbicacion(),
                                                                                                "msjTipoElemento"       => "del nodo wifi ",
                                                                                                "msjTipoElementoPadre"  =>
                                                                                                "que contiene al router del cliente ",
                                                                                                "msjAdicional"          => 
                                                                                                "por favor regularizar en la administración"
                                                                                                ." de Nodos Wifi"
                                                                                             ));
            if($arrayRespuestaCoordenadas["status"] === "ERROR")
            {
                throw new \Exception($arrayRespuestaCoordenadas['mensaje']);
            }
            $ubicacionElemento = new InfoUbicacion();
            $ubicacionElemento->setLatitudUbicacion($nodoUbicacion->getLatitudUbicacion());
            $ubicacionElemento->setLongitudUbicacion($nodoUbicacion->getLongitudUbicacion());
            $ubicacionElemento->setDireccionUbicacion($nodoUbicacion->getDireccionUbicacion());
            $ubicacionElemento->setAlturaSnm($nodoUbicacion->getAlturaSnm());
            $ubicacionElemento->setParroquiaId($parroquia);
            $ubicacionElemento->setUsrCreacion($session->get('user'));
            $ubicacionElemento->setFeCreacion(new \DateTime('now'));
            $ubicacionElemento->setIpCreacion($request->getClientIp());
            $em->persist($ubicacionElemento);

            //empresa elemento ubicacion
            $empresaElementoUbica = new InfoEmpresaElementoUbica();
            $empresaElementoUbica->setEmpresaCod($session->get('idEmpresa'));
            $empresaElementoUbica->setElementoId($elemento);
            $empresaElementoUbica->setUbicacionId($ubicacionElemento);
            $empresaElementoUbica->setUsrCreacion($session->get('user'));
            $empresaElementoUbica->setFeCreacion(new \DateTime('now'));
            $empresaElementoUbica->setIpCreacion($request->getClientIp());
            $em->persist($empresaElementoUbica);

            //empresa elemento
            $empresaElemento = new InfoEmpresaElemento();
            $empresaElemento->setElementoId($elemento);
            $empresaElemento->setEmpresaCod($session->get('idEmpresa'));
            $empresaElemento->setEstado("Activo");
            $empresaElemento->setUsrCreacion($session->get('user'));
            $empresaElemento->setIpCreacion($request->getClientIp());
            $empresaElemento->setFeCreacion(new \DateTime('now'));
            $em->persist($empresaElemento);


            //historial elemento
            $historialElemento = new InfoHistorialElemento();
            $historialElemento->setElementoId($elemento);
            $historialElemento->setEstadoElemento("Activo");
            $historialElemento->setObservacion("Se ingreso un router cliente");
            $historialElemento->setUsrCreacion($session->get('user'));
            $historialElemento->setFeCreacion(new \DateTime('now'));
            $historialElemento->setIpCreacion($request->getClientIp());
            $em->persist($historialElemento);
            $em->flush();


            //caracteristica para saber donde esta ubicada la router  (pedestal - edificio)
            $detalle = new InfoDetalleElemento();
            $detalle->setElementoId($elemento->getId());
            $detalle->setDetalleNombre("TIPO ELEMENTO RED");
            $detalle->setDetalleValor("WIFI");
            $detalle->setDetalleDescripcion("Caracteristicas para indicar que es un router de uso Wifi");
            $detalle->setFeCreacion(new \DateTime('now'));
            $detalle->setUsrCreacion($session->get('user'));
            $detalle->setIpCreacion($request->getClientIp());
            $detalle->setEstado('Activo');
            $em->persist($detalle);
            $em->flush();
            
            //caracteristica para saber donde esta ubicada la router  (pedestal - edificio)
            $detalle1 = new InfoDetalleElemento();
            $detalle1->setElementoId($elemento->getId());
            $detalle1->setDetalleNombre("PROPIEDAD");
            $detalle1->setDetalleValor("EMPRESA");
            $detalle1->setDetalleDescripcion("Caracteristicas para indicar la propiedad");
            $detalle1->setFeCreacion(new \DateTime('now'));
            $detalle1->setUsrCreacion($session->get('user'));
            $detalle1->setIpCreacion($request->getClientIp());
            $detalle1->setEstado('Activo');
            $em->persist($detalle1);
            $em->flush();
            
            //caracteristica para saber donde esta ubicada la router  (pedestal - edificio)
            $detalle3 = new InfoDetalleElemento();
            $detalle3->setElementoId($elemento->getId());
            $detalle3->setDetalleNombre("GESTION REMOTA");
            $detalle3->setDetalleValor("SI");
            $detalle3->setDetalleDescripcion("Caracteristicas para indicar si tiene gestion remota");
            $detalle3->setFeCreacion(new \DateTime('now'));
            $detalle3->setUsrCreacion($session->get('user'));
            $detalle3->setIpCreacion($request->getClientIp());
            $detalle3->setEstado('Activo');
            $em->persist($detalle3);
            $em->flush();   
            
            //caracteristica para saber donde esta ubicada la router  (pedestal - edificio)
            $detalle4 = new InfoDetalleElemento();
            $detalle4->setElementoId($elemento->getId());
            $detalle4->setDetalleNombre("ADMINISTRA");
            $detalle4->setDetalleValor("TELCONET-CONFIGURACIÓN");
            $detalle4->setDetalleDescripcion("Caracteristicas para indicar quien administra");
            $detalle4->setFeCreacion(new \DateTime('now'));
            $detalle4->setUsrCreacion($session->get('user'));
            $detalle4->setIpCreacion($request->getClientIp());
            $detalle4->setEstado('Activo');
            $em->persist($detalle4);
            $em->flush();
            
            //caracteristica para saber donde esta ubicada la router  (pedestal - edificio)
            $detalle5 = new InfoDetalleElemento();
            $detalle5->setElementoId($elemento->getId());
            $detalle5->setDetalleNombre("CAPACIDAD");
            $detalle5->setDetalleValor($capacidad);
            $detalle5->setDetalleDescripcion("Capacidad del elemento en Kb ");
            $detalle5->setFeCreacion(new \DateTime('now'));
            $detalle5->setUsrCreacion($session->get('user'));
            $detalle5->setIpCreacion($request->getClientIp());
            $detalle5->setEstado('Activo');
            $em->persist($detalle5);
            $em->flush();              
            
            
            $objTipoNegocio = $emComercial->getrepository('schemaBundle:AdmiTipoNegocio')->findOneByCodigoTipoNegocio('TN');
            $objTipoUbicacion = $emComercial->getrepository('schemaBundle:AdmiTipoUbicacion')->findOneByCodigoTipoUbicacion('ABIE');
            $objInfoPersona = $emComercial->getrepository('schemaBundle:InfoPersona')->findOneByRazonSocial('TELCONET S.A.') ;
            $objRol = $emComercial->getrepository('schemaBundle:AdmiRol')->findOneByDescripcionRol('Cliente');
            $objEmpresaRol = $emComercial->getrepository('schemaBundle:InfoEmpresaRol')->findOneBy(array('empresaCod'=>$empresaId, 
                                                                                                         'rolId' =>$objRol->getId()));
            $objPersonaEmpresaRol = $emComercial->getrepository('schemaBundle:InfoPersonaEmpresaRol')
                                                ->findOneBy(array('empresaRolId' => $objEmpresaRol->getId(),
                                                                     'personaId' => $objInfoPersona->getId()));
            $objSector = $emComercial->getrepository('schemaBundle:AdmiSector')->findOneByParroquiaId($parroquia->getId());
            
            //obtener jurisdiccion 
            $objParroquia = $emComercial->getRepository('schemaBundle:AdmiParroquia')->find($objSector->getParroquiaId());
            
            $arrayJurisccion = $emComercial->getRepository('schemaBundle:AdmiJurisdiccion')
                                           ->getJurisdiccionPorCanton($objParroquia->getCantonId()->getId(), $empresaId, 'Activo');
            
            if(count($arrayJurisccion)==0)
            {
                throw new \Exception('El canton '.$objParroquia->getCantonId()->getNombreCanton().' no tiene Jurisdiccion.');   
            }
            
            $objJurisdiccion = $emComercial->getRepository('schemaBundle:AdmiJurisdiccion')->find($arrayJurisccion[0]->getId());            
            
            //consulto el elemento con comercial
            $objElementoCom = $emComercial->getRepository('schemaBundle:InfoElemento')->find($ObjNodo->getId());
            
            //ingreso nuevo punto para que lo planifiquen
            $objPunto = new InfoPunto();
            $objPunto->setPuntoCoberturaId($objJurisdiccion);            
            $objPunto->setSectorId($objSector); 
            $objPunto->setTipoNegocioId($objTipoNegocio);
            $objPunto->setTipoUbicacionId($objTipoUbicacion);
            $objPunto->setDireccion($nodoUbicacion->getDireccionUbicacion());
            $objPunto->setLongitud($nodoUbicacion->getLongitudUbicacion());
            $objPunto->setLatitud($nodoUbicacion->getLatitudUbicacion());
            $objPunto->setLogin($ObjNodo->getNombreElemento());
            $objPunto->setEstado('Activo');
            $objPunto->setPersonaEmpresaRolId($objPersonaEmpresaRol);     
            $objPunto->setNombrePunto($descripcionElemento);
            $objPunto->setUsrVendedor('telconet');
            $objPunto->setUsrCreacion($session->get('user'));
            $objPunto->setFeCreacion(new \DateTime('now'));
            $objPunto->setIpCreacion($request->getClientIp());
            $emComercial->persist($objPunto);
            $emComercial->flush();
            
            //caracteristica para saber a que punto está relacionado este NODO WIFI
            $detalle6 = new InfoDetalleElemento();
            $detalle6->setElementoId($ObjNodo->getId());
            $detalle6->setDetalleNombre("ID_PUNTO");
            $detalle6->setDetalleValor($objPunto->getId());
            $detalle6->setDetalleDescripcion("Indica relacion con el punto. ");
            $detalle6->setFeCreacion(new \DateTime('now'));
            $detalle6->setUsrCreacion($session->get('user'));
            $detalle6->setIpCreacion($request->getClientIp());
            $detalle6->setEstado('Activo');
            $em->persist($detalle6);
            $em->flush();
            
            $entityInfoPuntoDatoAdicional = new InfoPuntoDatoAdicional();
            $entityInfoPuntoDatoAdicional->setDependeDeEdificio('N');
            $entityInfoPuntoDatoAdicional->setEsPadreFacturacion('N');
            $entityInfoPuntoDatoAdicional->setPuntoId($objPunto);
            $entityInfoPuntoDatoAdicional->setElementoId($objElementoCom);
            $entityInfoPuntoDatoAdicional->setIpCreacion($request->getClientIp());
            $entityInfoPuntoDatoAdicional->setFeCreacion(new \DateTime('now'));
            $entityInfoPuntoDatoAdicional->setUsrCreacion($session->get('user'));
            $emComercial->persist($entityInfoPuntoDatoAdicional);
            $emComercial->flush();

            $ObjProducto = $emComercial ->getRepository('schemaBundle:AdmiProducto')->findOneBy(array('descripcionProducto'=>'L3MPLS', 
                                                                                                      'nombreTecnico'=>'L3MPLS'));
            //dependiendo del modelo del nodo verifico la ultima milla
            if($ObjNodo->getModeloElementoId()->getNombreModeloElemento() == 'BACKBONE')
            {
                $ultimaMilla = 'UTP';
            }
            else
            {
                $ultimaMilla = 'FO';
            }
            $objTipoMedio = $emComercial ->getRepository('schemaBundle:AdmiTipoMedio')->findOneByCodigoTipoMedio($ultimaMilla);
            
            $objServicio = new InfoServicio();
            $objServicio->setPuntoId($objPunto);
            $objServicio->setProductoId($ObjProducto);
            $objServicio->setEsVenta('N');
            $objServicio->setPrecioVenta(0);
            $objServicio->setTipoOrden('N');
            $objServicio->setCantidad(1);
            $objServicio->setEstado('Pre-servicio');
            $objServicio->setFrecuenciaProducto(1);
            $objServicio->setDescripcionPresentaFactura('Concentrador L3MPLS Administracion');
            $objServicio->setObservacion($descripcionElemento);          
            $objServicio->setUsrCreacion($session->get('user'));
            $objServicio->setFeCreacion(new \DateTime('now'));
            $objServicio->setIpCreacion($request->getClientIp());
            $emComercial->persist($objServicio);
            $emComercial->flush(); 
                       
            //historial del servicio
            $servicioHistorial = new InfoServicioHistorial();
            $servicioHistorial->setServicioId($objServicio);
            $servicioHistorial->setObservacion("Se creo el servicio.");
            $servicioHistorial->setEstado('Pre-servicio');
            $servicioHistorial->setUsrCreacion($session->get('user'));
            $servicioHistorial->setFeCreacion(new \DateTime('now'));
            $servicioHistorial->setIpCreacion($request->getClientIp());
            $emComercial->persist($servicioHistorial);
            $emComercial->flush();          
            
                       
            $serviceServicioGeneral->ingresarServicioProductoCaracteristica($objServicio, $ObjProducto, "CAPACIDAD1", $capacidadLogin, 
                                                                            $session->get('user'));
            $serviceServicioGeneral->ingresarServicioProductoCaracteristica($objServicio, $ObjProducto, "CAPACIDAD2", $capacidadLogin, 
                                                                            $session->get('user'));
            // Se agrega característica que relaciona el concentrador con un login para emular un esquema 1.
            $serviceServicioGeneral->ingresarServicioProductoCaracteristica($objServicio,
                                                                            $ObjProducto,
                                                                            "RELACION_INTERNET_WIFI",
                                                                            implode(', ', array('E1 - NODO WIFI', $ObjNodo->getId())),
                                                                            $session->get('user'));
           
            
            //segun la ubicacion del nodo wifi lo enlazo a un servicio de un login
            $canton = $objParroquia->getCantonId()->getNombreCanton();
            $arrayParametros = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                           ->getOne('ENLACE_DATOS_WIFI', 'TECNICO', '', '', $canton, '', '', '', '', $empresaId);
            $objServicioEnlace = null;
            if($arrayParametros['valor2'])
            {
                $objServicioEnlace = $emComercial->getRepository('schemaBundle:InfoServicio')->findOneByLoginAux($arrayParametros['valor2']);

                if($objServicioEnlace)
                {
                    $serviceServicioGeneral->ingresarServicioProductoCaracteristica($objServicio, $ObjProducto, "ENLACE_DATOS", 
                                                                                    $objServicioEnlace->getId(), $session->get('user'));

                }
            }
            
            $objServicioTecnico  = new InfoServicioTecnico();
            $objServicioTecnico->setElementoClienteId($elemento->getId());
            $objServicioTecnico->setServicioId($objServicio);
            $objServicioTecnico->setTipoEnlace('PRINCIPAL');           
            $objServicioTecnico->setUltimaMillaId($objTipoMedio->getId());
            $emComercial->persist($objServicioTecnico);
            $emComercial->flush();
            
            //ingreso el segundo servicio
            $objServicio2 = new InfoServicio();
            $objServicio2->setPuntoId($objPunto);
            $objServicio2->setProductoId($ObjProducto);
            $objServicio2->setEsVenta('N');
            $objServicio2->setPrecioVenta(0);
            $objServicio2->setTipoOrden('N');
            $objServicio2->setCantidad(1);
            $objServicio2->setEstado('Pre-servicio');
            $objServicio2->setFrecuenciaProducto(1);
            $objServicio2->setDescripcionPresentaFactura('Concentrador L3MPLS Navegacion');
            $objServicio2->setObservacion($descripcionElemento);          
            $objServicio2->setUsrCreacion($session->get('user'));
            $objServicio2->setFeCreacion(new \DateTime('now'));
            $objServicio2->setIpCreacion($request->getClientIp());
            $emComercial->persist($objServicio2);
            $emComercial->flush(); 
                       
            //historial del servicio
            $servicioHistorial2 = new InfoServicioHistorial();
            $servicioHistorial2->setServicioId($objServicio2);
            $servicioHistorial2->setObservacion("Se creo el servicio.");
            $servicioHistorial2->setEstado('Pre-servicio');
            $servicioHistorial2->setUsrCreacion($session->get('user'));
            $servicioHistorial2->setFeCreacion(new \DateTime('now'));
            $servicioHistorial2->setIpCreacion($request->getClientIp());
            $emComercial->persist($servicioHistorial2);
            $emComercial->flush(); 
            
            $objServicioTecnico2  = new InfoServicioTecnico();
            $objServicioTecnico2->setElementoClienteId($elemento->getId());
            $objServicioTecnico2->setServicioId($objServicio2);
            $objServicioTecnico2->setTipoEnlace('PRINCIPAL');           
            $objServicioTecnico2->setUltimaMillaId($objTipoMedio->getId());
            $emComercial->persist($objServicioTecnico2);
            $emComercial->flush();
            //el BW de navegación debe ser 0 porque aumentará con el servicio internet wifi
            $serviceServicioGeneral->ingresarServicioProductoCaracteristica($objServicio2, $ObjProducto, "CAPACIDAD1", 0, 
                                                                            $session->get('user'));
            $serviceServicioGeneral->ingresarServicioProductoCaracteristica($objServicio2, $ObjProducto, "CAPACIDAD2", 0, 
                                                                            $session->get('user'));
            // Se agrega característica que relaciona el concentrador con un login para emular un esquema 1.
            $serviceServicioGeneral->ingresarServicioProductoCaracteristica($objServicio2,
                                                                            $ObjProducto,
                                                                            "RELACION_INTERNET_WIFI",
                                                                            implode(', ', array('E1 - NODO WIFI', $ObjNodo->getId())),
                                                                            $session->get('user'));
            
            if($objServicioEnlace)
            {
                $serviceServicioGeneral->ingresarServicioProductoCaracteristica($objServicio2, $ObjProducto, "ENLACE_DATOS", 
                                                                                $objServicioEnlace->getId(), $session->get('user'));

            }
            
            //actualizamos registro en el naf del cpe
            $arrayParametrosNaf = array('tipoArticulo'          => 'AF',
                                        'identificacionCliente' => '',
                                        'empresaCod'            => $empresaId,
                                        'modeloCpe'             => '',
                                        'serieCpe'              => $serieFisica,
                                        'cantidad'              => '1');
            
            $serviceCambioElemento = $this->get('tecnico.InfoCambioElemento');
            $strMensajeError = $serviceCambioElemento->procesaInstalacionElemento($arrayParametrosNaf);
            if(strlen(trim($strMensajeError)) > 0)
            {
                throw new \Exception($strMensajeError);
            }

            $emComercial->commit();
            $em->commit();
        }
        catch(\Exception $e)
        {
            if ($em->getConnection()->isTransactionActive())
            {
                $em->rollback();
            }
            $em->close();
            
            if ($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->rollback();
            }
            $emComercial->close();
            $mensajeError = "Error: " . $e->getMessage();
            $this->get('session')->getFlashBag()->add('notice', $mensajeError);
            return $this->redirect($this->generateUrl('elementoRouterCliente_new'));
        }

        return $this->redirect($this->generateUrl('elementoRouterCliente_show', array('id' => $elemento->getId())));
    }

    /**
    * editAction
    * función que renderiza el formulario para una edición
    *
    * @param integer $id
    * 
    * @author John Vera <javera@telconet.ec>
    * @version 1.0 14-01-2016
    * 
    * @author John Vera <javera@telconet.ec>
    * @version 1.1 27-06-2016 se aumentó la edición de capcidad de elemento
    */     
    public function editAction($id){
        $request    = $this->get('request');
        $session    = $request->getSession();
        $empresaId  = $session->get('idEmpresa');
        $em         = $this->getDoctrine()->getManager("telconet_infraestructura");
        $capacidad  = '';

        if (null == $elemento = $em->find('schemaBundle:InfoElemento', $id)) 
        {
            throw new NotFouncargarDatosRouterClientedHttpException('No existe el elemento que se quiere modificar');
        }
        else
        {            

            $ojRelacionElemento = $em->getRepository('schemaBundle:InfoRelacionElemento')->findOneBy(array('elementoIdB'=>$elemento->getId(),
                                                                                                           'estado' => 'Activo'));
            $objElementoContenedor = $em->getRepository('schemaBundle:InfoElemento')->find($ojRelacionElemento->getElementoIdA());
            if($objElementoContenedor)
            {
                $nombreElementoContenedor = $objElementoContenedor->getNombreElemento() ;
            }
            $infoIp = $em->getRepository('schemaBundle:InfoIp')->findOneByElementoId($elemento->getId());
            if($infoIp)
            {
                $ip = $infoIp->getIp();
            }
            $objDetCapacidad = $em->getRepository('schemaBundle:InfoDetalleElemento')->findOneBy(array('elementoId'     =>$elemento->getId(),
                                                                                                       'detalleNombre'  => 'CAPACIDAD',
                                                                                                       'estado'         => 'Activo'));
            if($objDetCapacidad)
            {
                $capacidad = $objDetCapacidad->getDetalleValor();
            }
        }

        $formulario =$this->createForm(new InfoElementoRouterClienteType(array("empresaId"=>$empresaId)), $elemento);
        
        return $this->render(   'tecnicoBundle:InfoElementoRouterCliente:edit.html.twig', array(
                                'edit_form'                 => $formulario->createView(),
                                'objElemento'               => $elemento,
                                'nombreElementoContenedor'  => $nombreElementoContenedor,
                                'capacidad'                 => $capacidad,
                                'ip'                        => $ip)
                            );
    }
    
   /**
    * updateAction
    * funcion que actualiza el elemento en la BD
    * @param integer $id
    * 
    * @author John Vera <javera@telconet.ec>
    * @version 1.0 14-01-2016
    * 
    * @author John Vera <javera@telconet.ec>
    * @version 1.1 27-06-2016 se quito que el nombre se ponga en mayúculas
    * 
    * @author John Vera <javera@telconet.ec>
    * @version 1.2 18-11-2016 Se valida que cuando no tenga el detalle se lo ingrese en la actualización.
    */     
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');

        $entity = $em->getRepository('schemaBundle:InfoElemento')->find($id);
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoElemento entity.');
        }

        $request            = $this->get('request');
        $session            = $request->getSession();
        $parametros         = $request->request->get('telconet_schemabundle_infoelementorouterclientetype');        
        $nombreElemento     = $parametros['nombreElemento'];
        $descripcionElemento= $parametros['descripcionElemento'];
        $serieFisica        = $parametros['serieFisica'];
        $versionOs          = $parametros['versionOs'];
        $ipElemento         = $request->request->get('ipElemento');
        $strCapacidad       = $request->request->get('capacidad');
        
        $em->getConnection()->beginTransaction();
       
        try
        {
                       
            if($nombreElemento != $entity->getNombreElemento())
            {
                //verificar que el nombre del elemento no se repita
                $elementoRepetido = $em->getRepository('schemaBundle:InfoElemento')
                                       ->findOneBy(array("nombreElemento"   => $nombreElemento,
                                                         "modeloElementoId" => $entity->getModeloElementoId(),
                                                         "estado"           => array("Activo", "Pendiente","Factible","PreFactibilidad")));
                
                if($elementoRepetido)
                {
                    $this->get('session')->getFlashBag()->add('notice', 'Nombre ya existe en otro Elemento con estado '.$elementoRepetido->getEstado());
                    return $this->redirect($this->generateUrl('elementoRouterCliente_edit', array('id' => $entity->getId())));
                }
                else
                {
                    $observacion = 'Nombre: '.$entity->getNombreElemento().'<br>';
                    $entity->setNombreElemento($nombreElemento);

                }
            }
            
            if($entity->getDescripcionElemento() != $descripcionElemento)
            {
                $observacion .= 'Descripcion: '.$entity->getDescripcionElemento().' <br> ';

                $entity->setDescripcionElemento($descripcionElemento);
                $em->persist($entity);
            }
            
            if($entity->getSerieFisica() != $serieFisica)
            {
                $observacion .= 'Serie Fisica: '.$entity->getSerieFisica().' <br> ';

                $entity->setSerieFisica($serieFisica);
                $em->persist($entity);
            }  
            
            if($entity->getVersionOs() != $versionOs)
            {
                $observacion .= 'Version Os: '.$entity->getVersionOs().' <br> ';

                $entity->setVersionOs($versionOs);
                $em->persist($entity);
            } 
            
            if($strCapacidad)
            {
                $objDetCapacidad = $em->getRepository('schemaBundle:InfoDetalleElemento')->findOneBy(array('elementoId'=>$entity->getId(),
                                                                                           'detalleNombre' => 'CAPACIDAD',
                                                                                           'estado' => 'Activo'));

                if(is_object($objDetCapacidad))
                {
                    if($objDetCapacidad->getDetalleValor() != $strCapacidad)
                    {
                        $observacion .= 'Capacidad: ' . $objDetCapacidad->getDetalleValor() . ' <br> ';

                        $objDetCapacidad->setDetalleValor($strCapacidad);
                        $em->persist($objDetCapacidad);
                    }
                }
                else
                {

                    $objDetalleCapacidad = new InfoDetalleElemento();
                    $objDetalleCapacidad->setElementoId($entity->getId());
                    $objDetalleCapacidad->setDetalleNombre("CAPACIDAD");
                    $objDetalleCapacidad->setDetalleValor($strCapacidad);
                    $objDetalleCapacidad->setDetalleDescripcion("Capacidad del elemento en Kb ");
                    $objDetalleCapacidad->setFeCreacion(new \DateTime('now'));
                    $objDetalleCapacidad->setUsrCreacion($session->get('user'));
                    $objDetalleCapacidad->setIpCreacion($request->getClientIp());
                    $objDetalleCapacidad->setEstado('Activo');
                    $em->persist($objDetalleCapacidad);
                    $em->flush();            
                }
            }


            if ($observacion)
            {
                //historial elemento
                $historialElemento = new InfoHistorialElemento();
                $historialElemento->setElementoId($entity);
                $historialElemento->setEstadoElemento("Modificado");
                $historialElemento->setObservacion('Dato Anterior: <br>'.$observacion);
                $historialElemento->setUsrCreacion($session->get('user'));
                $historialElemento->setFeCreacion(new \DateTime('now'));
                $historialElemento->setIpCreacion($request->getClientIp());
                $em->persist($historialElemento);
            }
            
            $em->flush();
            $em->getConnection()->commit();

            return $this->redirect($this->generateUrl('elementoRouterCliente_show', array('id' => $entity->getId())));

        }        
        catch(\Exception $e)
        {
            $em->getConnection()->rollback();
            $mensajeError = "Error: " . $e->getMessage();
            $this->get('session')->getFlashBag()->add('notice', $mensajeError);
            return $this->redirect($this->generateUrl('elementoRouterCliente_show', array('id' => $entity->getId())));
        }
        
    }
    
   /**
    * showAction
    * funcion que renderiza el form en donde aparece la información del elemento
    *
    * @param integer $id
    * 
    * @author John Vera <javera@telconet.ec>
    * @version 1.0 14-01-2016
    */         
    public function showAction($id){
        $peticion = $this->get('request');
        $session  = $peticion->getSession();
        $empresaId=$session->get('idEmpresa');
        
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");

        if(null == $elemento = $em->find('schemaBundle:InfoElemento', $id))
        {
            throw new NotFoundHttpException('No existe el Elemento que se quiere mostrar');
        }
        else
        {
            /* @var $datosElemento InfoServicioTecnico */
            $datosElemento = $this->get('tecnico.InfoServicioTecnico');
            //---------------------------------------------------------------------*/
            $respuestaElemento = $datosElemento->obtenerDatosElemento($id, $empresaId);
            
            //obtenemos la mac y la capacidad
            $objDetalleCapacidad = $em->getRepository('schemaBundle:InfoDetalleElemento')->findOneBy(array('elementoId'    => $id, 
                                                                                                           'estado'        => 'Activo', 
                                                                                                           'detalleNombre' => 'CAPACIDAD'));
            if($objDetalleCapacidad)
            {
                $capacidad = $objDetalleCapacidad->getDetalleValor();
            }
            $objDetalleMac = $em->getRepository('schemaBundle:InfoDetalleElemento')->findOneBy(array('elementoId'      => $id, 
                                                                                                     'estado'          => 'Activo', 
                                                                                                     'detalleNombre'   => 'MAC'));
            if($objDetalleMac)
            {
                $mac = $objDetalleMac->getDetalleValor();
            }            
            
            $ipElemento         = $respuestaElemento['ipElemento'];
            $arrayHistorial     = $respuestaElemento['historialElemento'];
            $ubicacion          = $respuestaElemento['ubicacion'];
            $jurisdiccion       = $respuestaElemento['jurisdiccion'];
        }

        return $this->render('tecnicoBundle:InfoElementoRouterCliente:show.html.twig', array(
            'elemento'          => $elemento,
            'ipElemento'        => $ipElemento,
            'historialElemento' => $arrayHistorial,
            'ubicacion'         => $ubicacion,
            'jurisdiccion'      => $jurisdiccion,
            'mac'               => $mac,
            'capacidad'         => $capacidad,
            'flag'              => $peticion->get('flag')
        ));
    }
    
    /**
    * getEncontrados
    * obtiene todos los registro de este tipo ingresados en la base de datos para mostrarlos en el grid
    *
    * @return json con la data 
    *
    * @author John Vera <javera@telconet.ec>
    * @version 1.0 28-12-2015
    */    
    public function getEncontradosAction(){
        ini_set('max_execution_time', 3000000);
        $respuesta      = new Response();
        $session        = $this->get('session');
        $em             = $this->getDoctrine()->getManager('telconet_infraestructura');        
        $peticion       = $this->get('request');        
        $nombreElemento = $peticion->query->get('nombreElemento');
        $canton         = $peticion->query->get('canton');
        $estado         = $peticion->query->get('estado');
        $idEmpresa      = $session->get('idEmpresa');
        $modeloElemento = $peticion->get('modeloElemento');        
        $start          = $peticion->query->get('start');
        $limit          = $peticion->query->get('limit');
        
        $arrayParametros = array();
        $arrayParametros['start']           = $start;
        $arrayParametros['limit']           = $limit;
        $arrayParametros["idModeloElemento"]= $modeloElemento;
        $arrayParametros["codEmpresa"]      = $idEmpresa;
        $arrayParametros["nombreNodo"]      = $nombreElemento;
        $arrayParametros["idCanton"]        = $canton;
        $arrayParametros["estadoElemento"]  = $estado;
        
        $respuestaSolicitudes = $this->getDoctrine()->getManager("telconet")->getRepository('schemaBundle:InfoElemento')
                                                    ->getJsonRegistrosRouterClientes($arrayParametros);

        $respuesta->setContent($respuestaSolicitudes);

        return $respuesta;
    }
    
   /**
    * cargarDatosAction
    * funcion que retorna un json con los datos del elemento
    *
    * @return json con datos del elemento
    * 
    * @author John Vera <javera@telconet.ec>
    * @version 1.0 14-01-2016
    */ 
    
    public function cargarDatosAction(){
        $respuesta  = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $em         = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emC        = $this->getDoctrine()->getManager('telconet');
        $peticion   = $this->get('request');
        $session    = $peticion->getSession();
        $empresaId  = $session->get('idEmpresa');
        $idElemento = $peticion->get('idElemento');

        $objJson = $this->getDoctrine()->getManager("telconet_infraestructura")->getRepository('schemaBundle:InfoElemento')
                        ->generarJsonCargarDatosCaja($idElemento, $empresaId, $em, $emC);
        $respuesta->setContent($objJson);

        return $respuesta;
    }
    
   /**
    * deleteAjaxAction
    * función que elimina uno o varios elementos
    *
    * @author John Vera <javera@telconet.ec>
    * @version 1.0 14-01-2016
    */     
    public function deleteAjaxAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');

        $request = $this->get('request');
        $session = $request->getSession();
        $peticion = $this->get('request');

        $parametro = $peticion->get('param');

        $em = $this->getDoctrine()->getManager("telconet_infraestructura");
        $em->getConnection()->beginTransaction();

        $array_valor = explode("|", $parametro);

        try
        {
            foreach($array_valor as $id):
                if(null == $entity = $em->find('schemaBundle:InfoElemento', $id))
                {
                    $respuesta->setContent("No existe la entidad");
                }
                else
                {
                    $relacionElemento = $em->getRepository('schemaBundle:InfoRelacionElemento')->findOneBy(array("elementoIdB" => $entity->getId(),
                                                                                                                 "estado" => "Activo"));
                    if($relacionElemento)
                    {
                        //consulto el elemento que lo contiene
                        $elementoContenedorId = $relacionElemento->getElementoIdA();
                        $elementoContenedor = $em->find('schemaBundle:InfoElemento', $elementoContenedorId);

                        //relacion elemento
                        $relacionElemento->setEstado("Eliminado");
                        $relacionElemento->setUsrCreacion($session->get('user'));
                        $relacionElemento->setFeCreacion(new \DateTime('now'));
                        $relacionElemento->setIpCreacion($peticion->getClientIp());
                        $em->persist($relacionElemento);

                        //actualizo el elemento que lo contiene a estado pendiente
                        $elementoContenedor->setEstado('Pendiente');
                        $elementoContenedor->setUsrCreacion($session->get('user'));
                        $elementoContenedor->setFeCreacion(new \DateTime('now'));
                        $elementoContenedor->setIpCreacion($peticion->getClientIp());
                        $em->persist($elementoContenedor);
                    }

                    //elemento
                    $entity->setEstado("Eliminado");
                    $entity->setUsrCreacion($session->get('user'));
                    $entity->setFeCreacion(new \DateTime('now'));
                    $entity->setIpCreacion($peticion->getClientIp());
                    $em->persist($entity);

                    $objDetalleElemento = $em->getRepository('schemaBundle:InfoDetalleElemento')->findBy(array("elementoId" => $id));

                    foreach($objDetalleElemento as $registro)
                    {
                        $registro->setEstado('Eliminado');
                        $em->persist($registro);
                        $em->flush();
                    }

                    //historial elemento
                    $historialElemento = new InfoHistorialElemento();
                    $historialElemento->setElementoId($entity);
                    $historialElemento->setEstadoElemento("Eliminado");
                    $historialElemento->setObservacion("Se elimino el Router Cliente en la administración");
                    $historialElemento->setUsrCreacion($session->get('user'));
                    $historialElemento->setFeCreacion(new \DateTime('now'));
                    $historialElemento->setIpCreacion($peticion->getClientIp());
                    $em->persist($historialElemento);

                    //empresa elemento
                    $empresaElemento = $em->getRepository('schemaBundle:InfoEmpresaElemento')->findOneBy(array("elementoId" => $entity));
                    $empresaElemento->setEstado("Eliminado");
                    $empresaElemento->setObservacion("Se elimino la router ");
                    $empresaElemento->setUsrCreacion($session->get('user'));
                    $empresaElemento->setFeCreacion(new \DateTime('now'));
                    $empresaElemento->setIpCreacion($peticion->getClientIp());
                    $em->persist($empresaElemento);

                    $em->flush();
                    $respuesta->setContent("OK");
                    $mensaje = 'Se eliminó correctamente ' . $entity->getNombreElemento() . $mensaje;
                }
            endforeach;

            $em->getConnection()->commit();
        }
        catch(\Exception $e)
        {

            $em->getConnection()->rollback();
            $mensajeError = "Error: " . $e->getMessage();
            $respuesta->setContent($mensajeError);
        }

        return $respuesta;
    }

    /**
     * getModelosPorTipoElementoAction
     * funcion que obtiene los  modelos por el tipo de elemento
     *
     * @return json
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 14-01-2016
     */
    public function getModelosPorTipoElementoAction()
    {
        $respuesta              = new Response();
        $emInfraestructura      = $this->getDoctrine()->getManager('telconet_infraestructura');
        $peticion               = $this->get('request');
        $tipoElemento           = $peticion->get('tipoElemento');

        $arrayTmpParametros     = array( 'estadoActivo' => 'Activo', 'tipoElemento' => array($tipoElemento) );
        
        $result    = $emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                       ->getJsonModeloElementosByCriterios( $arrayTmpParametros );

        $respuesta->setContent($result);

        return $respuesta;
    }
    
    public function getIpsAction(){
        ini_set('max_execution_time', 3000000);
        $respuesta          = new Response();
        $em                 = $this->getDoctrine()->getManager('telconet');        
        $peticion           = $this->get('request');        
        $idElemento         = $peticion->get('idElemento');        
     
        $jsonRegistros = $em->getRepository('schemaBundle:InfoDetalleElemento')
                                                  ->getJsonIpsPorElemento($idElemento);

        $respuesta->setContent($jsonRegistros);

        return $respuesta;
    }
    
    public function agregarIpAction(){
        ini_set('max_execution_time', 3000000);
        $respuesta      = new Response();
        $session        = $this->get('session');
        $em             = $this->getDoctrine()->getManager('telconet_infraestructura');        
        $peticion       = $this->get('request');        
        $idEmpresa      = $session->get('idEmpresa');
        $idElemento     = $peticion->get('idElemento');
        $ip             = trim($peticion->get('ip'));        
        $vlan           = $peticion->get('vlan');
        
        try
        {
            
            //validar que la ip no este repetida en el elemento
            $ipsElemento = $em->getRepository('schemaBundle:InfoIp')->findBy(array("elementoId" => $idElemento, "estado"  => "Activo"));
            
            if($ipsElemento)
            {
                foreach($ipsElemento as $value)
                {
                    if ($value->getIp() == $ip)
                    {
                        $respuesta->setContent('Ip ya existe en el elemento.');
                        return $respuesta;
                    }
                }
            }
            
            $em->getConnection()->beginTransaction();
            
            //ip elemento
            $objIp = new InfoIp();
            $objIp->setElementoId($idElemento);
            $objIp->setIp(trim($ip));
            $objIp->setVersionIp("IPV4");
            $objIp->setEstado("Activo");
            $objIp->setUsrCreacion($session->get('user'));
            $objIp->setFeCreacion(new \DateTime('now'));
            $objIp->setIpCreacion($peticion->getClientIp());
            $em->persist($objIp);
            
            //ingreso la ip como detalle
            $detalleIp = new InfoDetalleElemento();
            $detalleIp->setElementoId($idElemento);
            $detalleIp->setDetalleNombre("IP");
            $detalleIp->setDetalleValor($objIp->getId());
            $detalleIp->setDetalleDescripcion("Id de la ip relacionada al elemento.");
            $detalleIp->setFeCreacion(new \DateTime('now'));
            $detalleIp->setUsrCreacion($session->get('user'));
            $detalleIp->setIpCreacion($peticion->getClientIp());
            $detalleIp->setEstado('Activo');
            $em->persist($detalleIp);
            $em->flush();
            
            //ingreso la vlan como detalle y la relaciono a la ip
            $detalleVlan = new InfoDetalleElemento();
            $detalleVlan->setElementoId($idElemento);
            $detalleVlan->setDetalleNombre("VLAN");
            $detalleVlan->setDetalleValor($vlan);
            $detalleVlan->setDetalleDescripcion("vlan de la ip.");
            $detalleVlan->setFeCreacion(new \DateTime('now'));
            $detalleVlan->setUsrCreacion($session->get('user'));
            $detalleVlan->setIpCreacion($peticion->getClientIp());
            $detalleVlan->setEstado('Activo');
            $detalleVlan->setParent($detalleIp);
            $em->persist($detalleVlan);
            $em->flush();
            
            $respuesta->setContent("OK");
            $em->getConnection()->commit();
        }
        catch(\Exception $e)
        {
            $em->getConnection()->rollback();
            $mensajeError = "Error: " . $e->getMessage();
            $respuesta->setContent($mensajeError);
        }

        return $respuesta;

    }
    
    public function deleteIpAction(){
        ini_set('max_execution_time', 3000000);
        $respuesta      = new Response();
        $em             = $this->getDoctrine()->getManager('telconet_infraestructura');        
        $peticion       = $this->get('request');        
        $idDetalle      = $peticion->get('idDetalleElemento');
        $idIp           = $peticion->get('idIp');        

        
        try
        {            
            $em->getConnection()->beginTransaction();
            
            //ip elemento
            $objIp=$em->find('schemaBundle:InfoIp', $idIp);
            
            $objIp->setEstado("Eliminado");
            $em->persist($objIp);
            
            //elimino el detalle
            $objdetalle =$em->find('schemaBundle:InfoDetalleElemento', $idDetalle);

            $objdetalle->setEstado('Eliminado');
            $em->persist($objdetalle);
            $em->flush();
                        
            //elimino lo relacionado al detalle
            $objDetalleElemento = $em->getRepository('schemaBundle:InfoDetalleElemento')->findBy(array("parent" => $objdetalle));
            
            foreach($objDetalleElemento as $registro)
            {
                $registro->setEstado('Eliminado');
                $em->persist($registro);
                $em->flush();
            }

            $respuesta->setContent("OK");
            $em->getConnection()->commit();
        }
        catch(\Exception $e)
        {
            $em->getConnection()->rollback();
            $mensajeError = "Error: " . $e->getMessage();
            $respuesta->setContent($mensajeError);
        }

        return $respuesta;
    }    
    
     /**
     * getServiciosAction
     * Obtiene los elementos de los servicios segun el punto
     *
     * @return json
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 26-07-2016
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.1 04-08-2016 Se requiere que en la validacion entre internet dedicado y mpls
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 22-03-2021 Se abre la programacion para servicios Internet SDWAN
     */
    
    public function getServiciosAction(){
        ini_set('max_execution_time', 3000000);
        $respuesta      = new Response();
        $em             = $this->getDoctrine()->getManager('telconet_infraestructura');        
        $peticion       = $this->get('request');        
        $idPunto        = $peticion->get('idPunto');
        $estado         = $peticion->get('estado');        

        
        try
        {            
            $arrayServicios = $em->getRepository('schemaBundle:InfoServicio')->findServiciosByPuntoAndEstado($idPunto, $estado);
            
            foreach($arrayServicios['registros'] as $objServicio)
            {
                if($objServicio->getProductoId()->getNombretecnico() == 'L3MPLS' ||
                    $objServicio->getProductoId()->getNombretecnico() == 'INTMPLS' ||
                    $objServicio->getProductoId()->getNombretecnico() == 'INTERNET SDWAN' ||
                    $objServicio->getProductoId()->getNombretecnico() == 'INTERNET')
                {
                    
                    $objServicioTecnico = $em->getRepository('schemaBundle:InfoServicioTecnico')->findOneByServicioId($objServicio->getId());
                    
                    $serieElemento = '';
                    $modeloElemento = '';
                    if($objServicioTecnico)
                    {
                        $idElementoCliente = $objServicioTecnico->getElementoClienteId();
                        
                        if($idElementoCliente)
                        {                                                        
                            $objElemento = $em->getRepository('schemaBundle:InfoElemento')->find($idElementoCliente);
                            if($objElemento)
                            {
                                $idEquipoCliente = '';
                                $tipoElemento = $objElemento->getModeloElementoId()->getTipoElementoId()->getNombreTipoElemento();
                                //si es un cpe o un router es el indicado
                                if($tipoElemento == 'CPE' || $tipoElemento == 'ROUTER' )
                                {
                                    $idEquipoCliente = $objElemento->getId();
                                }
                                else
                                {
                                    //sino vamos a buscar los elementos hijos hasta llegar al router
                                    $arrayParamRequest = array('interfaceElementoConectorId'=> $objServicioTecnico->getInterfaceElementoClienteId(),
                                                               'tipoElemento'               => 'ROUTER');

                                    $arrayResponse = $em->getRepository("schemaBundle:InfoElemento")
                                                        ->getElementoClienteByTipoElemento($arrayParamRequest);

                                    if($arrayResponse['msg'] == 'FOUND')
                                    {
                                        $idEquipoCliente = $arrayResponse['idElemento'];
                                    }
                                    else
                                    {
                                        $arrayResponse = '';
                                        //sino vamos a buscar los elementos hijos hasta llegar al CPE
                                        $arrayParamRequest = array('interfaceElementoConectorId' => $objServicioTecnico->getInterfaceElementoClienteId(),
                                                                   'tipoElemento' => 'CPE');

                                        $arrayResponse = $em->getRepository("schemaBundle:InfoElemento")
                                                            ->getElementoClienteByTipoElemento($arrayParamRequest);
                                        
                                        if($arrayResponse['msg'] == 'FOUND')
                                        {
                                            $idEquipoCliente = $arrayResponse['idElemento'];
                                        }                                        
                                    }
                                }
                                $objElementoCliente = $em->getRepository('schemaBundle:InfoElemento')->find($idEquipoCliente);
                                if($objElementoCliente)
                                {
                                    $serieElemento = $objElementoCliente->getSerieFisica();
                                    $modeloElemento = $objElementoCliente->getModeloElementoId()->getNombreModeloElemento();   
                                }
                                
                            }                           
                        }
                    }
                    if($idEquipoCliente)
                    {
                        $arryEncontrados[] = array('idElemento'   => $idEquipoCliente,
                                                   'idServicio'   => $objServicio->getId(), 
                                                   'descripcion'  => '<b> Login Aux: </b>'.$objServicio->getLoginAux().'<br>'.
                                                                     '<b> Modelo: </b>'.$modeloElemento.'<br>'.   
                                                                     '<b> Serie: </b>'.$serieElemento);
                    }
                }
            }
            
            $jsonResultado = '{"msg":"OK", "total":"' . count($arryEncontrados) . '","registros":' . json_encode($arryEncontrados) . '}';
            $respuesta->setContent($jsonResultado);

        }
        catch(\Exception $e)
        {
            $mensajeError = "Error: " . $e->getMessage();
            $respuesta->setContent($mensajeError);
        }

        return $respuesta;
    }     
    

}