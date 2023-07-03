<?php

namespace telconet\tecnicoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoElemento;
use telconet\schemaBundle\Entity\InfoDetalleElemento;
use telconet\schemaBundle\Entity\AdmiModeloElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElementoUbica;
use telconet\schemaBundle\Entity\InfoInterfaceElemento;
use telconet\schemaBundle\Entity\InfoIp;
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Entity\InfoUbicacion;
use telconet\schemaBundle\Entity\InfoRelacionElemento;
use telconet\schemaBundle\Entity\AdmiParametroDet;
use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoDetalleSolCaract;
use JMS\SecurityExtraBundle\Annotation\Secure;

use telconet\tecnicoBundle\Resources\util\Util;
use telconet\tecnicoBundle\Service\InfoServicioTecnicoService;
use telconet\schemaBundle\Form\InfoElementoSwitchType;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Clase utilizada para manejar metodos que permiten realizar acciones de administracion de elementos Switches
 * 
 * @author Francisco Adum <fadum@telconet.ec>
 * @version 1.0 10-12-2015
 */
class InfoElementoSwitchController extends Controller
{ 
    /**
     * Funcion que sirve para agregar los permisos y cargar la pantalla de consulta
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 9-12-2015
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.1 10-11-2020 - Se agrega los permisos para el control de bw masivo
     */
    public function indexSwitchAction()
    {
        $rolesPermitidos = array();
        //MODULO 315 - SWITCH
        
        if (true === $this->get('security.context')->isGranted('ROLE_315-5'))
        {
            $rolesPermitidos[] = 'ROLE_315-5'; //editar elemento switch
        }
        if (true === $this->get('security.context')->isGranted('ROLE_315-8'))
        {
            $rolesPermitidos[] = 'ROLE_315-8'; //eliminar elemento switch
        }
        if (true === $this->get('security.context')->isGranted('ROLE_315-6'))
        {
            $rolesPermitidos[] = 'ROLE_315-6'; //ver elemento switch
        }
        if( true === $this->get('security.context')->isGranted('ROLE_315-7657'))
        {
            $rolesPermitidos[] = 'ROLE_315-7657'; // VIEW CONTROL BW MASIVO
        }
        if( true === $this->get('security.context')->isGranted('ROLE_315-7638'))
        {
            $rolesPermitidos[] = 'ROLE_315-7638'; // GENERAR CONTROL BW MASIVO
        }

        return $this->render('tecnicoBundle:InfoElementoSwitch:index.html.twig', array(
            'rolesPermitidos' => $rolesPermitidos
        ));
    }
    
    /**
     * Funcion que sirve para crear el formulario para ingresar un nuevo switch
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 10-12-2015
     */
    public function newSwitchAction()
    {
        $request    = $this->get('request');
        $session    = $request->getSession();
        $empresaId  = $session->get('idEmpresa');
        $entity     = new InfoElemento();
        $form       = $this->createForm(new InfoElementoSwitchType(array("empresaId" => $empresaId)), $entity);

        return $this->render('tecnicoBundle:InfoElementoSwitch:new.html.twig', 
                             array(
                                    'entity' => $entity,
                                    'form'   => $form->createView()
                                  )
                            );
    }
    
    /**
     * Funcion que sirve para ingresar los datos de un elemento switch en la base de datos
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 10-12-2015
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 02-08-2016 - Se guarda la información del anillo del switch nuevo
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 19-09-2018 - Se agrega la validación de las coordenadas de latitud y longitud de acuerdo al país en sesión
     */
    public function createSwitchAction()
    {
        $peticion               = $this->get('request');
        $session                = $peticion->getSession();
        $em                     = $this->get('doctrine')->getManager('telconet_infraestructura');
        $elementoSwitch         = new InfoElemento();
        $form                   = $this->createForm(new InfoElementoSwitchType(), $elementoSwitch);
        $parametros             = $peticion->request->get('telconet_schemabundle_infoelementoswitchtype');
        $nombreElemento         = $parametros['nombreElemento'];
        $ip                     = $parametros['ipElemento'];
        $modeloElementoId       = $parametros['modeloElementoId'];
        $nodoElementoId         = $parametros['nodoElementoId'];
        $intRackElementoId      = $parametros['rackElementoId'];
        $intUnidadRack          = $parametros['unidadRack'];
        $descripcionElemento    = $parametros['descripcionElemento'];
        $anillo                 = $parametros['anillo'];
        $strUnidadesOcupadas    = "";
        $intUnidadMaximaU       = 0;
                
        $em->beginTransaction();
        
        try
        {
            $form->bind($peticion);
            
            if ($form->isValid()) 
            {
                //verificar que no se repita la ip
                $ipRepetida = $em->getRepository('schemaBundle:InfoIp')->findOneBy(array("ip" => $ip, "estado" => "Activo"));
                if($ipRepetida)
                {
                    throw new \Exception('Ip ya existe en otro Elemento, favor revisar!');
                }

                //verificar que el nombre del elemento no se repita
                $elementoRepetido = $em->getRepository('schemaBundle:InfoElemento')
                                       ->findOneBy(array("nombreElemento" => $nombreElemento, "estado" => "Activo"));
                if($elementoRepetido)
                {
                    throw new \Exception('Nombre ya existe en otro Elemento, favor revisar!');
                }

                $modeloElemento = $em->find('schemaBundle:AdmiModeloElemento', $modeloElementoId);
                
                //verificar que se haya ingresado la posicion del elemento en el rack
                if ( $intRackElementoId === "")
                {
                    throw new \Exception('Es obligatorio ingresar la posicion del rack!');
                }

                $elementoSwitch->setNombreElemento($nombreElemento);
                $elementoSwitch->setDescripcionElemento($descripcionElemento);
                $elementoSwitch->setModeloElementoId($modeloElemento);
                $elementoSwitch->setUsrResponsable($session->get('user'));
                $elementoSwitch->setUsrCreacion($session->get('user'));
                $elementoSwitch->setFeCreacion(new \DateTime('now'));
                $elementoSwitch->setIpCreacion($peticion->getClientIp());
                $elementoSwitch->setEstado("Activo");
                $em->persist($elementoSwitch);
                $em->flush();
                                
                //buscar el interface Modelo
                $interfaceModelo = $em->getRepository('schemaBundle:AdmiInterfaceModelo')->findBy(array("modeloElementoId" => $modeloElementoId));
                foreach($interfaceModelo as $im)
                {
                    $cantidadInterfaces = $im->getCantidadInterface();
                    $formato            = $im->getFormatoInterface();

                    $start  = 1;
                    $fin    = $cantidadInterfaces;
                    
                    for($i = $start; $i <= $fin; $i++)
                    {
                        $interfaceElemento          = new InfoInterfaceElemento();
                        $format                     = explode("?", $formato);
                        $nombreInterfaceElemento    = $format[0] . $i;
                        $interfaceElemento->setNombreInterfaceElemento($nombreInterfaceElemento);
                        $interfaceElemento->setElementoId($elementoSwitch);
                        $interfaceElemento->setEstado("not connect");
                        $interfaceElemento->setUsrCreacion($session->get('user'));
                        $interfaceElemento->setFeCreacion(new \DateTime('now'));
                        $interfaceElemento->setIpCreacion($peticion->getClientIp());

                        $em->persist($interfaceElemento);
                    }
                }

                //se valida si el elemento contenedor es Rack y se asignan recursos de unidades
                if($intRackElementoId != '')
                {
                    $objElementoRack            = $em->getRepository('schemaBundle:InfoElemento')->find($intRackElementoId);
                    $objInterfaceModeloRack     = $em->getRepository('schemaBundle:AdmiInterfaceModelo')
                                                     ->findOneBy(array("modeloElementoId" => $objElementoRack->getModeloElementoId()));
                    $objElementoUnidadRack      = $em->getRepository('schemaBundle:InfoElemento')->find($intUnidadRack);
                    $intUnidadMaximaU           = (int) $objElementoUnidadRack->getNombreElemento() + 
                                                  (int) $modeloElemento->getURack() - 1;
                    if($intUnidadMaximaU > $objInterfaceModeloRack->getCantidadInterface())
                    {
                        throw new \Exception('No se puede ubicar el Switch en el Rack porque se sobrepasa el tamaño de unidades!');
                    }
                    //obtener todas las unidades del rack
                    $objRelacionesElementoUDRack = $em->getRepository('schemaBundle:InfoRelacionElemento')
                                                       ->findBy(array("elementoIdA" => $intRackElementoId,
                                                                      "estado"      => "Activo")
                                                                  );
                    //se verifica disponibilidad de unidades y se asignan recursos
                    for($t = (int)$objElementoUnidadRack->getNombreElemento(); $t <= $intUnidadMaximaU; $t++)
                    {
                        $intElementoUnidadId     = 0;
                        $objRelacionElementoRack = null;
                        
                        foreach($objRelacionesElementoUDRack as $objRelacionElementoUDRack)
                        {
                            $objElementoUnidadRackDet      = $em->getRepository('schemaBundle:InfoElemento')
                                                                ->find($objRelacionElementoUDRack->getElementoIdB());
                            if ((int)$objElementoUnidadRackDet->getNombreElemento() == $t)
                            {
                                $intElementoUnidadId = $objElementoUnidadRackDet->getId();
                            }
                        }
                        $objRelacionElementoRack = $em->getRepository('schemaBundle:InfoRelacionElemento')
                                                      ->findOneBy(array("elementoIdA"             => $intElementoUnidadId,
                                                                        "estado"                  => "Activo"
                                                                       )
                                                                 );
                        if($objRelacionElementoRack)
                        {
                            if ($strUnidadesOcupadas == "")
                            {
                                $strUnidadesOcupadas = $t;
                            }
                            else
                            {
                                $strUnidadesOcupadas = $strUnidadesOcupadas . " , " . $t;
                            }
                        }
                        else
                        {
                            //relacion elemento
                            $objRelacionElemento = new InfoRelacionElemento();
                            $objRelacionElemento->setElementoIdA($intElementoUnidadId);
                            $objRelacionElemento->setElementoIdB($elementoSwitch->getId());
                            $objRelacionElemento->setTipoRelacion("CONTIENE");
                            $objRelacionElemento->setObservacion("Rack contiene Switch");
                            $objRelacionElemento->setEstado("Activo");
                            $objRelacionElemento->setUsrCreacion($session->get('user'));
                            $objRelacionElemento->setFeCreacion(new \DateTime('now'));
                            $objRelacionElemento->setIpCreacion($peticion->getClientIp());
                            $em->persist($objRelacionElemento);
                        }
                    }
                    if($strUnidadesOcupadas != "")
                    {
                        throw new \Exception('No se puede ubicar el Switch en el Rack porque estan ocupadas las unidades : ' . $strUnidadesOcupadas);
                    }
                }
                else
                {
                    //relacion elemento
                    $relacionElemento = new InfoRelacionElemento();
                    $relacionElemento->setElementoIdA($nodoElementoId);
                    $relacionElemento->setElementoIdB($elementoSwitch->getId());
                    $relacionElemento->setTipoRelacion("CONTIENE");
                    $relacionElemento->setObservacion("Nodo contiene Switch");
                    $relacionElemento->setEstado("Activo");
                    $relacionElemento->setUsrCreacion($session->get('user'));
                    $relacionElemento->setFeCreacion(new \DateTime('now'));
                    $relacionElemento->setIpCreacion($peticion->getClientIp());
                    $em->persist($relacionElemento);
                }


                //ip elemento
                $ipElemento = new InfoIp();
                $ipElemento->setElementoId($elementoSwitch->getId());
                $ipElemento->setIp(trim($ip));
                $ipElemento->setVersionIp("IPV4");
                $ipElemento->setEstado("Activo");
                $ipElemento->setUsrCreacion($session->get('user'));
                $ipElemento->setFeCreacion(new \DateTime('now'));
                $ipElemento->setIpCreacion($peticion->getClientIp());
                $em->persist($ipElemento);

                //historial elemento
                $historialElemento = new InfoHistorialElemento();
                $historialElemento->setElementoId($elementoSwitch);
                $historialElemento->setEstadoElemento("Activo");
                $historialElemento->setObservacion("Se ingreso un Switch");
                $historialElemento->setUsrCreacion($session->get('user'));
                $historialElemento->setFeCreacion(new \DateTime('now'));
                $historialElemento->setIpCreacion($peticion->getClientIp());
                $em->persist($historialElemento);

                //tomar datos nodo
                $nodoEmpresaElementoUbicacion = $em->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                                                  ->findOneBy(array("elementoId" => $nodoElementoId));
                $nodoUbicacion = $em->getRepository('schemaBundle:InfoUbicacion')->find($nodoEmpresaElementoUbicacion->getUbicacionId()->getId());

                //info ubicacion
                $parroquia = $em->find('schemaBundle:AdmiParroquia', $nodoUbicacion->getParroquiaId());
                $serviceInfoElemento        = $this->get("tecnico.InfoElemento");
                $arrayRespuestaCoordenadas  = $serviceInfoElemento->validarLimitesCoordenadasElemento(array(
                                                                                                            "latitudElemento"       => 
                                                                                                            $nodoUbicacion->getLatitudUbicacion(),
                                                                                                            "longitudElemento"      => 
                                                                                                            $nodoUbicacion->getLongitudUbicacion(),
                                                                                                            "msjTipoElemento"       => "del nodo ",
                                                                                                            "msjTipoElementoPadre"  =>
                                                                                                            "que contiene al switch ",
                                                                                                            "msjAdicional"          => 
                                                                                                            "por favor regularizar en la "
                                                                                                            ."administración de Nodos"
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
                $ubicacionElemento->setIpCreacion($peticion->getClientIp());
                $em->persist($ubicacionElemento);

                //empresa elemento ubicacion
                $empresaElementoUbica = new InfoEmpresaElementoUbica();
                $empresaElementoUbica->setEmpresaCod($session->get('idEmpresa'));
                $empresaElementoUbica->setElementoId($elementoSwitch);
                $empresaElementoUbica->setUbicacionId($ubicacionElemento);
                $empresaElementoUbica->setUsrCreacion($session->get('user'));
                $empresaElementoUbica->setFeCreacion(new \DateTime('now'));
                $empresaElementoUbica->setIpCreacion($peticion->getClientIp());
                $em->persist($empresaElementoUbica);

                //empresa elemento
                $empresaElemento = new InfoEmpresaElemento();
                $empresaElemento->setElementoId($elementoSwitch);
                $empresaElemento->setEmpresaCod($session->get('idEmpresa'));
                $empresaElemento->setEstado("Activo");
                $empresaElemento->setUsrCreacion($session->get('user'));
                $empresaElemento->setIpCreacion($peticion->getClientIp());
                $empresaElemento->setFeCreacion(new \DateTime('now'));
                $em->persist($empresaElemento);
                
                //Detalle Elemento se agrega anillo
                $objDetalleElemento = new InfoDetalleElemento();
                $objDetalleElemento->setElementoId($elementoSwitch->getId());
                $objDetalleElemento->setDetalleNombre("ANILLO");
                $objDetalleElemento->setDetalleValor($anillo);
                $objDetalleElemento->setDetalleDescripcion("ANILLO");
                $objDetalleElemento->setUsrCreacion($session->get('user'));
                $objDetalleElemento->setFeCreacion(new \DateTime('now'));
                $objDetalleElemento->setIpCreacion($peticion->getClientIp());
                $objDetalleElemento->setEstado("Activo");
                $em->persist($objDetalleElemento);

                $em->flush();
                $em->commit();
                                
                return $this->redirect($this->generateUrl('elementoswitch_showSwitch', array('id' => $elementoSwitch->getId())));
            }
        }
        catch (\Exception $e) 
        {
            if ($em->getConnection()->isTransactionActive())
            {
                $em->rollback();
            }
            $em->close();
            $mensaje = "Error: ".$e->getMessage();
        }
        $this->get('session')
             ->getFlashBag()->add('notice', 'Existieron problemas al procesar la transaccion, favor notificar a Sistemas.\n'.$mensaje);
        return $this->redirect($this->generateUrl('elementoswitch_newSwitch'));
    }
    
    /**
     * Funcion que sirve para crear el formulario para editar un switch
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 10-12-2015
     */
    public function editSwitchAction($id)
    {
        $this->get('session')->getFlashBag()->add('notice', 'No existe opcion de edicion!');
        return $this->redirect($this->generateUrl('elementoswitch'));
    }
    
    /**
     * Funcion que sirve para editar los datos de un elemento switch en la base de datos
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 10-12-2015
     */
    public function updateSwitchAction($id)
    {
        $this->get('session')->getFlashBag()->add('notice', 'No existe opcion de edicion!');
        return $this->redirect($this->generateUrl('elementoswitch'));
    }
    
    /**
     * Funcion que sirve para mostrar los datos de un elemento switch
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 10-12-2015
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 02-08-2016 - Se agrega el anillo del switch al detalle a mostrar
     */
    public function showSwitchAction($id)
    {
        $objRequest   = $this->get('request');
        $objSession   = $objRequest->getSession();
        $intEmpresaId = $objSession->get('idEmpresa');
        
        $emInfraestructura = $this->getDoctrine()->getManager("telconet_infraestructura");

        if(null == $objElemento = $emInfraestructura->find('schemaBundle:InfoElemento', $id))
        {
            throw new NotFoundHttpException('No existe el Elemento que se quiere mostrar');
        }
        else
        {
            /* @var $datosElemento InfoServicioTecnico */
            $datosElemento = $this->get('tecnico.InfoServicioTecnico');
            //---------------------------------------------------------------------*/
            $respuestaElemento = $datosElemento->obtenerDatosElemento($id, $intEmpresaId);
            
            $ipElemento         = $respuestaElemento['ipElemento'];
            $arrayHistorial     = $respuestaElemento['historialElemento'];
            $ubicacion          = $respuestaElemento['ubicacion'];
            $jurisdiccion       = $respuestaElemento['jurisdiccion'];
            $anillo             = $respuestaElemento['anillo'];
        }//(null == $objElemento = $emInfraestructura->find('schemaBundle:InfoElemento', $id))

        return $this->render('tecnicoBundle:InfoElementoSwitch:show.html.twig', array(
            'elemento'              => $objElemento,
            'ipElemento'            => $ipElemento,
            'historialElemento'     => $arrayHistorial,
            'ubicacion'             => $ubicacion,
            'jurisdiccion'          => $jurisdiccion,
            'anillo'                => $anillo,
            'flag'                  => $objRequest->get('flag')
        ));
    }
    
    /**
     * Función que sirve para obtener los switches 
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 8-12-2015
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 07-06-2016   Se agrega filtro para recuperación de información obligando a ingresar nombre de elemento
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.2 24-05-2017   Se agrega validacion para que cuando el escenario se trate de esquema pseudope busque los SW
     *                           que esten marcados como virtuales
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.3 19-08-2017   Se agrega validacion que soporte busqueda de Switches de acuerdo a un detalle ingresados que ayude
     *                           a segmentar la informacion de estos
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.4 23-02-2018   Se agrega validacion que soporte busqueda de Switches virtuales para flujos de INTERCONEXION de clientes
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.5 18-06-2019   Se agrega validacion que soporte busqueda de Switches virtuales para flujos de INTERCONEXION de clientes
     * 
     * @param $peticion [nombreElemento, modeloElemento, marcaElemento, canton, jurisdiccion, estado, tipoElemento]
     * @return $respuesta objJson
     */
    public function getEncontradosSwitchAction()
    {
        ini_set('max_execution_time', 3000000);
        
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $session    = $this->get('session');
        $em         = $this->getDoctrine()->getManager('telconet_infraestructura');
        $peticion   = $this->get('request');
        $emComercial= $this->getDoctrine()->getManager('telconet');
        $serviceTecnico     = $this->get('tecnico.InfoServicioTecnico');
        $intIdElemento      = $peticion->query->get('idElemento');
        $nombreElemento     = $peticion->query->get('nombreElemento');
        $modeloElemento     = $peticion->query->get('modeloElemento');
        $marcaElemento      = $peticion->query->get('marcaElemento');
        $canton             = $peticion->query->get('canton');
        $jurisdiccion       = $peticion->query->get('jurisdiccion');
        $estado             = $peticion->query->get('estado');
        $tipoElemento       = $peticion->query->get('tipoElemento');
        $strProcesoBusqueda = $peticion->query->get('procesoBusqueda');
        $idEmpresa          = $session->get('idEmpresa');
        $strNomElementoRe   = $peticion->query->get('query');
        $strEsPseudoPe      = $peticion->query->get('esPseudoPe')?$peticion->query->get('esPseudoPe'):'';
        $strBusquedaDetalle = $peticion->query->get('busquedaDetalle')?$peticion->query->get('busquedaDetalle'):'';
        $strNombreDetalle   = $peticion->query->get('nombreDetalle')?$peticion->query->get('nombreDetalle'):'';
        $strValorDetalle    = $peticion->query->get('valorDetalle')?$peticion->query->get('valorDetalle'):'';
        $intIdServicio      = $peticion->query->get('idServicio')?$peticion->query->get('idServicio'):'';
        $objTipoElemento    = $em->getRepository('schemaBundle:AdmiTipoElemento')->findOneBy(array( "nombreTipoElemento" =>$tipoElemento));
        
        $start          = $peticion->query->get('start');
        $limit          = $peticion->query->get('limit');
        $nombreElemento = $nombreElemento?strtoupper($nombreElemento):strtoupper($strNomElementoRe);
        
        $arrayParametros = array();
        
        $boolBusquedaDetalle = false;
        $strNombreTecnico    = '';
        
        if(!empty($intIdServicio))
        {
            $objServicio = $emComercial->getRepository("schemaBundle:InfoServicio")->find($intIdServicio);
            
            if(is_object($objServicio))
            {
                $strNombreTecnico = $objServicio->getProductoId()->getNombreTecnico();
            }
        }
        
        //Se valida si se requiere realizar la busqueda tomando en cuenta la caracteristica elemento
        if(!empty($strEsPseudoPe) && $strEsPseudoPe == 'SI')
        {
            $boolBusquedaDetalle = true;
            $strNombreDetalle    = 'ES_SWITCH_VIRTUAL';
            $strValorDetalle     = 'SI';
        }
        
        if(!empty($strBusquedaDetalle) && $strBusquedaDetalle == 'SI')
        {
            $boolBusquedaDetalle = true;
        }
        
        if($strNombreTecnico == 'CONCINTER')
        {
            $boolBusquedaDetalle = true;
            $strNombreDetalle    = 'ES_SWITCH_VIRTUAL_INTER';
            $strValorDetalle     = 'SI';
        }
        
        //Si no es pseudope o no requiere busqueda por detalle busca los switches creados de manera manual
        if(!$boolBusquedaDetalle)
        {
            //se agrega filtrado de busqueda de información
            if ($strProcesoBusqueda == 'limitado')
            {
                if ($nombreElemento !='')
                {
                    $arrayParametros = array(
                                        'nombreElemento'    => $nombreElemento,
                                        'modeloElemento'    => $modeloElemento,
                                        'marcaElemento'     => $marcaElemento,
                                        'tipoElemento'      => $objTipoElemento->getId(),
                                        'canton'            => $canton,
                                        'jurisdiccion'      => $jurisdiccion,
                                        'estado'            => $estado,
                                        'start'             => $start,
                                        'limit'             => $limit,
                                        'empresa'           => $idEmpresa
                                    );
                }
                else
                {
                    $objJson = '{"total":"0","encontrados":[]}';
                }

            }
            else
            {
                $arrayParametros = array(
                                        'nombreElemento'    => $nombreElemento,
                                        'modeloElemento'    => $modeloElemento,
                                        'marcaElemento'     => $marcaElemento,
                                        'tipoElemento'      => $objTipoElemento->getId(),
                                        'canton'            => $canton,
                                        'jurisdiccion'      => $jurisdiccion,
                                        'estado'            => $estado,
                                        'start'             => $start,
                                        'limit'             => $limit,
                                        'empresa'           => $idEmpresa
                                    );
            }
            
            $objJson =  $this->getDoctrine()->getManager("telconet_infraestructura")
                                            ->getRepository('schemaBundle:InfoElemento')
                                            ->generarJsonElementosPorParametros($arrayParametros);
        }
        else
        {
            if($strNombreDetalle == 'ES_SWITCH_DC' || $strNombreDetalle == 'ES_SWITCH_VIRTUAL_INTER' || $strNombreDetalle == 'ES_SWITCH_VIRTUAL')
            {
                if($strNombreDetalle == 'ES_SWITCH_VIRTUAL_INTER')
                {
                    $strNombreDetalle = 'ES_SWITCH_VIRTUAL';
                }
                //Se cargan para busqueda solo los Switches que esten marcados como virtuales
                $arrayParametros                     = array();
                $arrayParametros['strEstado']        = 'Activo';
                $arrayParametros['strDetalleValor']  = $strValorDetalle;
                $arrayParametros['strDetalleNombre'] = $strNombreDetalle;
                $arrayParametros['strTipoElemento']  = strtoupper($tipoElemento);
                $arrayParametros['strNombreElemento']= $strNomElementoRe;

                $objJson = $this->getDoctrine()->getManager("telconet_infraestructura")
                                               ->getRepository('schemaBundle:InfoElemento')
                                               ->getJsonElementosByDetalleYTipo($arrayParametros);
            }//SWITCH DC DEPENDIENTE DE OTRO SUPERIO - ES_SWITCH_DC_2_NIVEL
            else
            {
                $arrayRelacionElemento = $em->getRepository("schemaBundle:InfoRelacionElemento")->findBy(array('elementoIdA' => $intIdElemento,
                                                                                                               'estado'      => 'Activo'));
                $arrayResultado = array();
                
                if(!empty($arrayRelacionElemento))
                {
                    foreach($arrayRelacionElemento as $objRelacionElemento)
                    {
                        $objElemento = $em->getRepository("schemaBundle:InfoElemento")->find($objRelacionElemento->getElementoIdB());
                        
                        if(is_object($objElemento))
                        {
                            $arrayResultado[] = array('idElemento' => $objElemento->getId(), 'nombreElemento' => $objElemento->getNombreElemento());
                        }
                    }
                }
                
                $objJson = json_encode($arrayResultado);
            }
        }
        
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /**
     * Funcion que sirve para eliminar los elementos switches
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 8-12-2015
     */
    public function deleteAjaxSwitchAction()
    {
        $this->get('session')->getFlashBag()->add('notice', 'No existe opcion de Eliminar un Elemento!');
        return $this->redirect($this->generateUrl('elementoswitch'));
    }
    
    /**
     * Documentación para el método 'ajaxGetSwitchesPorAnilloAction'.
     *
     * Método utilizado para generar el Json de todos los switches relacionados a un anillo enviado
     *
     * @param string idElemento id del elemento de tipos PE
     * @param string numeroAnillo numero del anillo del cual se requeira encontrar los switches del PE     
     *
     * @return JsonResponse [{ 
     *                      'total' : ''
     *                      'data'  : [{
     *                                   'id_elemento':'',
     *                                   'nombre_elemento':'',
     *                                   }]
     *                      }]
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 06-04-2016
    */
    public function ajaxGetSwitchesPorAnilloAction()
    {
        $request      = $this->get('request');
        $respuesta    = new JsonResponse();        
               
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        
        $nombreElemento   = $request->get('nombreElemento');
        $numeroAnillo     = $request->get('numeroAnillo');
        
        /* @var $datosElemento InfoServicioTecnico */
        $serviceTecnico = $this->get('tecnico.InfoServicioTecnico');
        
        $objElemento    = $emInfraestructura->getRepository("schemaBundle:InfoElemento")->findOneByNombreElemento($nombreElemento);        
        $arrayResultado = $emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                       ->getJsonElementosPorAnillo($objElemento,$numeroAnillo,$serviceTecnico);                                
        
        $objJson = json_encode($arrayResultado);
        
        $respuesta->setContent($objJson);
        
        return $respuesta;                
    }
    
    /**
     * Funcion que sirve para obtener los elementos (switches) diferentes al anillo y pe
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 27-04-2016
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.1 1-06-2016
     * Se agrego la llamada al service InfoServicioTecnicoService
     */
    public function ajaxGetSwitchesCambioUmProgramadaAction()
    {
        $request        = $this->get('request');
        $session        = $request->getSession();
        $respuesta      = new JsonResponse();
               
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        
        $tipoElementoIngreso    = $request->get('tipoElementoIngreso');
        $tipoElementoPadre      = $request->get('tipoElementoPadre');
        $estadoElemento         = $request->get('estadoElemento');
        $tipoElementoBusqueda   = $request->get('tipoElementoBusqueda');
        $detalleNombreElemento  = $request->get('detalleNombreElemento');
        $detalleValorElemento   = $request->get('detalleValorElemento');
        $idElementoPadre        = $request->get('idElementoPadre');
        $puntoCoberturaId       = $request->get('puntoCoberturaId');
        $nombreElemento         = $request->get('nombreElemento');
        $codEmpresa             = $session->get('idEmpresa');
        $serviceTecnico         = $this->get('tecnico.InfoServicioTecnico');
        
        $arrayParametros = array(
                                    'tipoElementoIngreso'   => $tipoElementoIngreso,
                                    'tipoElementoPadre'     => $tipoElementoPadre,
                                    'estadoElemento'        => $estadoElemento,
                                    'tipoElementoBusqueda'  => $tipoElementoBusqueda,
                                    'detalleNombreElemento' => $detalleNombreElemento,
                                    'detalleValorElemento'  => $detalleValorElemento,
                                    'idElementoPadre'       => $idElementoPadre,
                                    'puntoCoberturaId'      => $puntoCoberturaId,
                                    'nombreElemento'        => $nombreElemento,
                                    'codEmpresa'            => $codEmpresa,
                                    'serviceTecnico'        => $serviceTecnico,
                                    'login_aux'             => ""
                                );
        
        $objJson = $emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                       ->getJsonElementosParaUmProgramada($arrayParametros);                                
                
        $respuesta->setContent($objJson);
        
        return $respuesta;                
    }
    
    /**
     * Documentación para el método 'ajaxGetCajasPorPuertosDisponibles'.
     *
     * Método utilizado para generar el Json de todas las cajas disponibles para un determinado switch con puertos disponibles
     *
     * @param string idElemento id del elemento de tipos SWITCH       
     *
     * @return JsonResponse [{ 
     *                      'total' : ''
     *                      'data'  : [{
     *                                   'idInterface':'',
     *                                   'nombreInterface':'',
     *                                   }]
     *                      }]
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 06-04-2016
    */
    public function ajaxGetCajasPorPuertosDisponiblesAction()
    {
        $request      = $this->get('request');
        $respuesta    = new JsonResponse();
        
        $arrayElementos = array();
               
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        
        $idElemento       = $request->get('idElemento');
        
        $arrayResultado = $emInfraestructura->getRepository("schemaBundle:InfoElemento")->getResultadoCajaPorPuertoDisponible($idElemento);
        
        if($arrayResultado)
        {
            foreach($arrayResultado as $result)
            {
                $arrayElementos[] = array('idElemento'=>$result['idElemento'],'nombreElemento'=>$result['nombreElemento']);
            }
        }
        
        $objResultado = array(
                              'total' => count($arrayElementos) ,
                              'data'  => $arrayElementos
                             );
        
        $objJson = json_encode($objResultado);
        
        $respuesta->setContent($objJson);
        
        return $respuesta; 
    }        

    /**
     * Documentación para el método 'ajaxGetElementosAction'.
     *
     * Método utilizado para generar el Json de registros de Elementos
     *
     * @param string nombre Nombre del elemento a buscar.
     * @param string tipo Tipo de Elemento a buscar 
     * @param string jurisdiccion Jurisdiccion del elemento a buscar
     *
     * @return JsonResponse [{ 
     *                      'total' : ''
     *                      'data'  : [{
     *                                   'id_elemento':'',
     *                                   'nombre_elemento':'',
     *                                   }]
     *                      }]
     * @author Kenneth Jimenez <kjimenez@telconet.ec>
     * @version 1.0 08-12-2015
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.1 30-05-2017 se aumento que no tome en cuenta los sw virtuales
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.2 26-02-2018 se aumento para que traiga tanto switches fisicos como virtuales para poder realizar la asignacion de vlans
    */
    public function ajaxGetElementosAction()
    {
        $objRequest        = $this->get('request');
        $arrayRespuesta    = new JsonResponse();
        $arrayElementos    = array();
        $arrayParametrosSV = array();
        
        $strNombre       = $objRequest->get('nombre');
        $strTipo         = $objRequest->get('tipo');
        $strJurisdiccion = $objRequest->get('jurisdiccion');
        
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');

        $arrayParametrosSV['strEstado']        = 'Activo';
        $arrayParametrosSV['strDetalleValor']  = 'SI';
        $arrayParametrosSV['strDetalleNombre'] = 'ES_SWITCH_VIRTUAL';
        $arrayParametrosSV['strTipoElemento']  = strtoupper($strTipo);
        $arrayParametrosSV['strNombreElemento']= $strNombre;

        $arrayResultado = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                            ->getArrayElementosByDetalleYTipo($arrayParametrosSV);

        $intTotalV = $arrayResultado['total'];

        foreach($arrayResultado['encontrados'] as $array)
        {
            $arrayElementos[] = array( 
                                           'id_elemento'     => $array['idElemento'],
                                           'nombre_elemento' => $array['nombreElemento']
                                        );
        }

        $arrayParametros['nombre']        = $strNombre;
        $arrayParametros['tipo_elemento'] = $strTipo;
        $arrayParametros['jurisdiccion']  = $strJurisdiccion;
        $arrayParametros['estado']        = "Activo";
        $arrayParametros['switchVirtual'] = "NO";

        $arrayElementosSw = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->getElementosPorParametros($arrayParametros);

        if(!empty($arrayElementosSw))
        {
            foreach($arrayElementosSw as $arrayElemento)
            {
                $arrayElementos[] = array( 
                                            'id_elemento'     => $arrayElemento['idElemento'],
                                            'nombre_elemento' => $arrayElemento['nombreElemento']
                                         );
            }
        }

        $intTotal = count($arrayElementos);
        
        $objResultado = array(
                              'total' => $intTotal + $intTotalV ,
                              'data'  => $arrayElementos
                             );
        
        $objJson = json_encode($objResultado);
        
        $arrayRespuesta->setContent($objJson);
        
        return $arrayRespuesta;
    }  
    
    /**
     * Documentación para el método 'ajaxGetInfoBackboneByElementoAction'.
     *
     * Método utilizado para generar el Json de registros de la informacion de Backbone de un elemento
     *
     * @param string idElemento Nombre del elemento a buscar.
     * @param string tipoElementoPadre Tipo de Elemento a buscar
     *
     * @return JsonResponse [{ 
     *                      'total' : ''
     *                      'data'  : [{
     *                                   'id_elemento':'',
     *                                   'nombre_elemento':'',
     *                                   'anillo':'',
     *                                   'min':'',
     *                                   'max':'',
     *                                   }]
     *                      }]
     * @author Kenneth Jimenez <kjimenez@telconet.ec>
     * @version 1.0 08-12-2015
     *
     * @author Eduardo Plua <eplua@telconet.ec>
     * @version 1.1 26-05-2016 - Se recupera elementoPe desde ws networking
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 17-07-2018 - Se enviá nuevo parámetro al método getPeBySwitch
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.3 23-08-2019 - Se busca si el cliente en sesion esta configurado en el proyecto de MAPEO de VRF.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.4 20-11-2019 - Se agrega lógica en caso de que el tipo elemento sea OLT se consulta otro W.B. de networking para 
     *                           retornar el elemento Pe.
     */
    public function ajaxGetInfoBackboneByElementoAction()
    {
        $emGeneral         = $this->getDoctrine()->getManager('telconet_general');
        $strBandCliente    = "N";
        $request           = $this->get('request');
        $respuesta         = new JsonResponse();
        $arrayParametrosWs = array();
        $idElemento        = $request->get('idElemento');
        $tipoElementoPadre = $request->get('tipoElementoPadre');
        $objSession        = $request->getSession();
        $arrayCliente      = $objSession->get('cliente');
        $strUsrCreacion    = $objSession->get('user') ? $objSession->get('user'):'';
        $strCodEmpresa     = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        //Se verifica si el cliente esta configurado para obtener vlans por vrf
        $arrayValoresParametros = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->getOne('PARAMETROS PROYECTO SEGMENTACION VLAN',
                                                     'INFRAESTRUCTURA',
                                                     'ASIGNAR RECURSOS DE RED',
                                                     'CLIENTE_PERMITIDO',
                                                     $arrayCliente['id_persona_empresa_rol'],
                                                     '',
                                                     '',
                                                     '',
                                                     '',
                                                     '');

        if(isset($arrayValoresParametros["valor1"]) && !empty($arrayValoresParametros["valor1"]))
        {
            $strBandCliente = "S";
        }

        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');

        try
        {
            if(!empty($tipoElementoPadre) && $tipoElementoPadre =="OLT" && !empty($idElemento))
            {
                $arrayParametrosWs["intIdElemento"]  = $idElemento;
                $arrayParametrosWs["strUsrCreacion"] = $strUsrCreacion;
                $objElementoPe                       = $this->get('tecnico.InfoServicioTecnico')->getPeByOlt($arrayParametrosWs);
                $arrayParametrosBackboneGpon         = array('intIdElemento' => $idElemento,
                                                             'objElementoPe' => $objElementoPe,
                                                             'strCodEmpresa' => $strCodEmpresa);
                $objJson                             = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                         ->getJsonInfoBackboneByElementoGpon($arrayParametrosBackboneGpon);
            }
            else
            {
                $arrayParametrosWs["intIdElemento"] = $idElemento;
                $arrayParametrosWs["intIdServicio"] = "";

                $objElementoPe     = $this->get('tecnico.InfoServicioTecnico')->getPeBySwitch($arrayParametrosWs);
                $objJson           = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                       ->getJsonInfoBackboneByElemento($idElemento,$objElementoPe,$strBandCliente);
            }
        }
        catch(\Exception $e)
        {
            $objJson = $e->getMessage();
        }        
        
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }

    /**
     * @Secure(roles="ROLE_315-7657")
     *
     * Documentación para el método 'indexControlBwMasivoAction'.
     *
     * Renderiza la pantalla para listar las ejecuciones del control bw masivo
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 10-11-2020
     *
     * @return Render Pantalla Control Bw Masivo.
     */
    public function indexControlBwMasivoAction()
    {
        $arrayRolesPermitidos = array();
        if($this->get('security.context')->isGranted('ROLE_315-7638'))
        {
            $arrayRolesPermitidos[] = 'ROLE_315-7638'; // GENERAR CONTROL BW MASIVO
        }
        return $this->render('tecnicoBundle:InfoElementoSwitch:indexControlBwMasivo.html.twig', array('rolesPermitidos' => $arrayRolesPermitidos));
    }

    /**
     * @Secure(roles="ROLE_315-7657")
     *
     * Documentación para el método 'viewControlBwMasivoAction'.
     *
     * Renderiza la pantalla para visualizar la ejecución del control bw masivo
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 10-11-2020
     *
     * @param Integer $intIdEjecucion
     * @return Render Pantalla Control Bw Masivo.
     */
    public function viewControlBwMasivoAction($intIdEjecucion)
    {
        $objRequest     = $this->getRequest();
        $objSesion      = $objRequest->getSession();
        $strUsrSesion   = $objSesion->get('user');
        $strIpClient    = $objRequest->getClientIp();
        $serviceUtil    = $this->get('schema.Util');
        $emComercial    = $this->getDoctrine()->getManager('telconet');
        //seteo la fecha de ejecución
        $strFechaEjecucion = '';
        try
        {
            //seteo la fecha de ejecución
            $objSolicitudEjecucion  = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->find($intIdEjecucion);
            //obtengo la caracteristica de la fecha ejecución
            $objCaractFecha         = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneBy(array("descripcionCaracteristica" => 'FECHA_EJECUCION',
                                                                  "estado"                    => "Activo"));
            if( is_object($objSolicitudEjecucion) && is_object($objCaractFecha))
            {
                //obtengo la característica de la fecha ejecución
                $objDetFechaEjec    = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                ->findOneBy(array("detalleSolicitudId" => $objSolicitudEjecucion->getId(), 
                                                                  "caracteristicaId"   => $objCaractFecha->getId(),
                                                                  "estado"             => 'Activo'));
                if(is_object($objDetFechaEjec))
                {
                    $strFechaEjecucion = $objDetFechaEjec->getValor();
                }
            }
        }
        catch (Exception $e)
        {
            $serviceUtil->insertError('Telcos+',
                                      'InfoElementoSwitchController.viewControlBwMasivoAction',
                                      $e->getMessage(),
                                      $strUsrSesion,
                                      $strIpClient
                                     );
        }
        return $this->render('tecnicoBundle:InfoElementoSwitch:viewControlBwMasivo.html.twig', array(
            'intIdEjecucion'    => $intIdEjecucion,
            'strFechaEjecucion' => $strFechaEjecucion
        ));
    }

    /**
     * @Secure(roles="ROLE_315-7638")
     *
     * Documentación para el método 'generarControlBwMasivoAction'.
     *
     * Renderiza la pantalla para generar las ejecuciones del control bw masivo
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 10-11-2020
     *
     * @return Render Generar Pantalla Control Bw Masivo.
     */
    public function generarControlBwMasivoAction()
    {
        $arrayRolesPermitidos = array();
        if($this->get('security.context')->isGranted('ROLE_315-7657'))
        {
            $arrayRolesPermitidos[] = 'ROLE_315-7657'; // VIEW CONTROL BW MASIVO
        }
        return $this->render('tecnicoBundle:InfoElementoSwitch:generarControlBwMasivo.html.twig', array('rolesPermitidos' => $arrayRolesPermitidos));
    }

    /**
     * @Secure(roles="ROLE_315-7657")
     *
     * Documentación para el método 'ajaxGetListaControlBwMasivoAction'.
     *
     * Método que sirve para obtener la lista de ejecuciones del control bw masivo
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 10-11-2020
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 06-05-2022 Se modifica la función para que los archivos adjuntos se suban al NFS
     *
     * @return Response $objResponse - Lista de ejecuciones del control bw masivo
     */
    public function ajaxGetListaControlBwMasivoAction()
    {
        ini_set('max_execution_time', 300000);
        $objRequest     = $this->getRequest();
        $objSesion      = $objRequest->getSession();
        $serviceUtil    = $this->get('schema.Util');
        $emComercial    = $this->getDoctrine()->getManager('telconet');
        $emComunicacion = $this->getDoctrine()->getManager('telconet_comunicacion');
        $strIpClient    = $objRequest->getClientIp();
        $strUsrSesion   = $objSesion->get('user');
        try
        {
            //arreglo de los resultados de las ejecuciones programadas del control bw masivo
            $arrayResultado         = array();
            //obtengo la caracteristica del id del documento
            $objCaractDocumento     = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                            ->findOneBy(array("descripcionCaracteristica" => 'ID_DOCUMENTO',
                                                              "estado"                    => "Activo"));
            //obtengo la caracteristica de la fecha ejecución
            $objCaractFechaEje      = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                            ->findOneBy(array("descripcionCaracteristica" => 'FECHA_EJECUCION',
                                                              "estado"                    => "Activo"));
            //obtengo la caracteristica del total de switch
            $objCaractTotalSwitch   = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                            ->findOneBy(array("descripcionCaracteristica" => 'TOTAL_SWITCH',
                                                              "estado"                    => "Activo"));
            //obtengo la caracteristica del total de interfaces
            $objCarTotalInterfaces  = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                            ->findOneBy(array("descripcionCaracteristica" => 'TOTAL_INTERFACES',
                                                              "estado"                    => "Activo"));
            //obtengo la caracteristica de la fecha inicio
            $objCaractFechaInicio   = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                            ->findOneBy(array("descripcionCaracteristica" => 'FECHA_INICIO',
                                                              "estado"                    => "Activo"));
            //obtengo la caracteristica de la fecha fin
            $objCaractFechaFin      = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                            ->findOneBy(array("descripcionCaracteristica" => 'FECHA_FIN',
                                                              "estado"                    => "Activo"));
            //obtengo el tipo de solicitud de la ejecución
            $objTipoSolicitud       = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                  ->findOneBy(array('descripcionSolicitud' => 'SOLICITUD CONTROL BW AUTOMATICO',
                                                                    'estado'               => 'Activo'));
            //obtengo los datos de las ejecuciones
            $arrayEjecuciones       = $emComercial->getRepository("schemaBundle:InfoDetalleSolicitud")
                                                        ->findBy(array('tipoSolicitudId' => $objTipoSolicitud->getId()));
            foreach($arrayEjecuciones as $objSolEjecucion)
            {
                $strFechaProcesar   = null;
                $strTotalSwitch     = null;
                $strTotalInterface  = null;
                $strFechaInicio     = null;
                $strFechaFin        = null;
                $strRutaArchivo     = null;
                //obtengo la característica del documento
                $objDetDocumento       = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                ->findOneBy(array("detalleSolicitudId" => $objSolEjecucion->getId(), 
                                                                  "caracteristicaId"   => $objCaractDocumento->getId(),
                                                                  "estado"             => 'Activo'));
                if(is_object($objDetDocumento))
                {
                    $objDocumento   = $emComunicacion->getRepository('schemaBundle:InfoDocumento')->find($objDetDocumento->getValor());
                    if(is_object($objDocumento))
                    {
                        $strRutaArchivo = $objDocumento->getUbicacionFisicaDocumento();
                        $boolRutaArchivoNfs = (filter_var($strRutaArchivo, FILTER_VALIDATE_URL) !== false);
                        if(!$boolRutaArchivoNfs && !file_exists($strRutaArchivo))
                        {
                            $strRutaArchivo = null;
                        }
                    }
                }
                //obtengo la característica de la fecha ejecución
                $objDetFechaEjec       = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                ->findOneBy(array("detalleSolicitudId" => $objSolEjecucion->getId(), 
                                                                  "caracteristicaId"   => $objCaractFechaEje->getId(),
                                                                  "estado"             => 'Activo'));
                if(is_object($objDetFechaEjec))
                {
                    $strFechaProcesar  = $objDetFechaEjec->getValor();
                }
                //obtengo la característica del total switch
                $objDetTotalSwitch     = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                ->findOneBy(array("detalleSolicitudId" => $objSolEjecucion->getId(), 
                                                                  "caracteristicaId"   => $objCaractTotalSwitch->getId(),
                                                                  "estado"             => 'Activo'));
                if(is_object($objDetTotalSwitch))
                {
                    $strTotalSwitch    = $objDetTotalSwitch->getValor();
                }
                //obtengo la característica del total interfaces
                $objDetTotalInterface  = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                ->findOneBy(array("detalleSolicitudId" => $objSolEjecucion->getId(), 
                                                                  "caracteristicaId"   => $objCarTotalInterfaces->getId(),
                                                                  "estado"             => 'Activo'));
                if(is_object($objDetTotalInterface))
                {
                    $strTotalInterface = $objDetTotalInterface->getValor();
                }
                //obtengo la característica de la fecha inicio
                $objDetFechaInicio     = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                ->findOneBy(array("detalleSolicitudId" => $objSolEjecucion->getId(), 
                                                                  "caracteristicaId"   => $objCaractFechaInicio->getId(),
                                                                  "estado"             => 'Activo'));
                if(is_object($objDetFechaInicio))
                {
                    $strFechaInicio    = $objDetFechaInicio->getValor();
                }
                //obtengo la característica de la fecha fin
                $objDetFechaFin        = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                ->findOneBy(array("detalleSolicitudId" => $objSolEjecucion->getId(), 
                                                                  "caracteristicaId"   => $objCaractFechaFin->getId(),
                                                                  "estado"             => 'Activo'));
                if(is_object($objDetFechaFin))
                {
                    $strFechaFin       = $objDetFechaFin->getValor();
                }
                //seteo el arreglo
                $arrayResultado[] = array(
                    'id_ejecucion'       => $objSolEjecucion->getId(),
                    'fecha_procesar'     => $strFechaProcesar,
                    'total_sw'           => $strTotalSwitch,
                    'total_interfaces'   => $strTotalInterface,
                    'usuario'            => $objSolEjecucion->getUsrCreacion(),
                    'fecha_creacion'     => $objSolEjecucion->getFeCreacion()->format('Y-m-d H:m:i'),
                    'fecha_inicio'       => $strFechaInicio,
                    'fecha_finalizacion' => $strFechaFin,
                    'estado'             => $objSolEjecucion->getEstado(),
                    'ruta_archivo'       => $strRutaArchivo
                );
            }
            //se formula el json de respuesta
            $strJsonResultado   = '{"total":"' . count($arrayResultado) . '","encontrados":' . json_encode($arrayResultado) . '}';
        }
        catch (\Exception $e)
        {
            $strJsonResultado   = '{"total":"0", "encontrados":[], "error":[' . $e->getMessage() . ']}';
            $serviceUtil->insertError('Telcos+',
                                      'InfoElementoSwitchController.ajaxGetListaControlBwMasivoAction',
                                      $e->getMessage(),
                                      $strUsrSesion,
                                      $strIpClient
                                     );
        }
        $objResponse    = new JsonResponse();
        $objResponse->setContent($strJsonResultado);
        return $objResponse;
    }

    /**
     * @Secure(roles="ROLE_315-7657")
     *
     * Documentación para el método 'ajaxGetDetallesControlBwMasivoAction'.
     *
     * Renderiza la pantalla para visualizar la ejecución del control bw masivo
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 10-11-2020
     * 
     * @author Diego Guamán <deguaman@telconet.ec>
     * @version 1.1 08-06-2023 Se modifica la función mostrar el tipo INFO que son el resultado de
     *                         los procesos excluidos del proceso masivo
     * 
     * @param Integer $intIdEjecucion
     * @return Response $objResponse - Lista de ejecuciones de interfaces
     */
    public function ajaxGetDetallesControlBwMasivoAction()
    {
        ini_set('max_execution_time', 300000);
        $objRequest     = $this->getRequest();
        $objSesion      = $objRequest->getSession();
        $serviceUtil    = $this->get('schema.Util');
        $strIpClient    = $objRequest->getClientIp();
        $strUsrSesion   = $objSesion->get('user');
        $intIdEjecucion = $objRequest->get('intIdEjecucion');

        $emComercial    = $this->getDoctrine()->getManager('telconet');
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');

        //seteo el arreglo de datos de resultado
        $arrayDatosEjecuciones  = array();
        try
        {
            //obtengo la caracteristica del tipo de proceso a ejecutar
            $objCaractTipoProceso       = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                            ->findOneBy(array("descripcionCaracteristica" => 'TIPO_PROCESO',
                                                            "estado"                      => "Activo"));
            //obtengo la caracteristica del historial elemento id
            $objCaractHistorial         = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                            ->findOneBy(array("descripcionCaracteristica" => 'HISTORIAL_ELEMENTO_ID',
                                                              "estado"                    => "Activo"));
            //obtengo la característica de ejecución
            $objAdmiCaractIdEjecucion   = $emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                            ->findOneBy(array("descripcionCaracteristica" => 'ID_EJECUCION',
                                                              "estado"                    => 'Activo'));
            //obtengo la caracteristica de la interface elemento id
            $objCaractInterface         = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                            ->findOneBy(array("descripcionCaracteristica" => 'INTERFACE_ELEMENTO_ID',
                                                              "estado"                    => "Activo"));
            //obtengo la caracteristica de la capacidad uno
            $objCaractCapacidadUno      = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                            ->findOneBy(array("descripcionCaracteristica" => 'CAPACIDAD1',
                                                              "estado"                    => "Activo"));
            //obtengo la caracteristica de la capacidad dos
            $objCaractCapacidadDos      = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                            ->findOneBy(array("descripcionCaracteristica" => 'CAPACIDAD2',
                                                              "estado"                    => "Activo"));
            //obtengo la caracteristica de la capacidad uno anterior
            $objCaractCapaciUnoAnt      = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                            ->findOneBy(array("descripcionCaracteristica" => 'CAPACIDAD1 ANTERIOR',
                                                              "estado"                    => "Activo"));
            //obtengo la caracteristica de la capacidad dos anterior
            $objCaractCapaciDosAnt      = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                            ->findOneBy(array("descripcionCaracteristica" => 'CAPACIDAD2 ANTERIOR',
                                                              "estado"                    => "Activo"));
            //obtengo todas las ejecuciones
            $arrayEjecucionesCaract     = $emComercial->getRepository("schemaBundle:InfoDetalleSolCaract")
                                                ->createQueryBuilder('p')
                                                ->andWhere("p.caracteristicaId  = :caracteristicaId")
                                                ->andWhere("p.valor             = :valor")
                                                ->setParameter('caracteristicaId',   $objAdmiCaractIdEjecucion->getId())
                                                ->setParameter('valor',              $intIdEjecucion)
                                                ->orderBy('p.detalleSolicitudId', 'ASC')
                                                ->getQuery()
                                                ->getResult();
            foreach($arrayEjecucionesCaract as $objEjecucionCaract)
            {
                //seteo las variables
                $strTipoEjecucion      = '';
                $strObservacion        = '';
                $strNombreElemento     = null;
                $strNombreInterface    = null;
                $strCapacidadUnoAnt    = null;
                $strCapacidadDosAnt    = null;
                $strCapacidadUno       = null;
                $strCapacidadDos       = null;
                //obtengo el detalle de la solicitud
                $objEjeSolicitud       = $objEjecucionCaract->getDetalleSolicitudId();
                //seteo el estado de la ejecución
                $strEjeEstado          = $objEjecucionCaract->getEstado();
                //obtengo el tipo de proceso
                $objDetalleTipoProceso = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                ->findOneBy(array("detalleSolicitudId" => $objEjeSolicitud->getId(), 
                                                                  "caracteristicaId"   => $objCaractTipoProceso->getId()));
                if( !is_object($objDetalleTipoProceso) )
                {
                    throw new \Exception("No se encontró el tipo de proceso a ejecutar, por favor notificar a Sistemas.");
                }
                //obtengo el tipo de proceso
                $strTipoProceso        = $objDetalleTipoProceso->getValor();
                if( $strTipoProceso != 'REPORTE_CORREO_BW_MASIVO' )
                {
                    if( $strTipoProceso == 'GENERAR_TAREA_ELEMENTO' || $strTipoProceso == 'GENERAR_TAREA_INTERFACE' )
                    {
                        //seteo el tipo de ejecución
                        $strTipoEjecucion       = 'TAREA INTERNA';
                        //obtengo el detalle del historial
                        $objDetalleHistorial    = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(array("detalleSolicitudId" => $objEjeSolicitud->getId(), 
                                                                      "caracteristicaId"   => $objCaractHistorial->getId()));
                        if( !is_object($objDetalleHistorial) )
                        {
                            throw new \Exception("No se encontró el historial del elemento, por favor notificar a Sistemas.");
                        }
                        //obtengo el historial del elemento
                        $objHistorialElemento   = $emInfraestructura->getRepository('schemaBundle:InfoHistorialElemento')
                                                        ->find($objDetalleHistorial->getValor());
                        if( !is_object($objHistorialElemento) )
                        {
                            throw new \Exception("No se encontró el historial del elemento, por favor notificar a Sistemas.");
                        }
                        $strObservacion         = $objHistorialElemento->getObservacion();
                        //seteo el nombre del elemento
                        $strNombreElemento      = $objHistorialElemento->getElementoId()->getNombreElemento();

                    }
                    if( $strTipoProceso == 'UPGRADE_DOWNGRADE_BW_MASIVO' || $strTipoProceso == 'GENERAR_TAREA_INTERFACE' )
                    {
                        //obtengo el detalle de la interface del elemento
                        $objDetalleInterface    = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                        ->findOneBy(array("detalleSolicitudId" => $objEjeSolicitud->getId(), 
                                                                          "caracteristicaId"   => $objCaractInterface->getId()));
                        if( !is_object($objDetalleInterface) )
                        {
                            throw new \Exception("No se encontró la interface del elemento, por favor notificar a Sistemas.");
                        }
                        //obtengo la interface del elemento
                        $objInfoInterface       = $emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")
                                                                            ->find($objDetalleInterface->getValor());
                        if( !is_object($objInfoInterface) )
                        {
                            throw new \Exception("No se encontró la interface del elemento, por favor notificar a Sistemas.");
                        }
                        //seteo el nombre de la interface
                        $strNombreInterface     = $objInfoInterface->getNombreInterfaceElemento();
                        //seteo el nombre del elemento
                        $strNombreElemento      = $objInfoInterface->getElementoId()->getNombreElemento();
                    }
                    if( $strTipoProceso == 'UPGRADE_DOWNGRADE_BW_MASIVO' )
                    {
                        //obtengo el detalle de la capacidad uno nueva
                        $objEjeCaractCapUno     = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                        ->findOneBy(array("detalleSolicitudId" => $objEjeSolicitud->getId(), 
                                                                          "caracteristicaId"   => $objCaractCapacidadUno->getId()));
                        if( !is_object($objEjeCaractCapUno) )
                        {
                            throw new \Exception("No se encontró la capacidad uno de la interface del elemento, ".
                                                 "por favor notificar a Sistemas.");
                        }
                        //obtengo el detalle de la capacidad dos nueva
                        $objEjeCaractCapDos     = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                        ->findOneBy(array("detalleSolicitudId" => $objEjeSolicitud->getId(), 
                                                                          "caracteristicaId"   => $objCaractCapacidadDos->getId()));
                        if( !is_object($objEjeCaractCapDos) )
                        {
                            throw new \Exception("No se encontró la capacidad dos de la interface del elemento, ".
                                                 "por favor notificar a Sistemas.");
                        }
                        //obtengo el detalle de la capacidad dos anterior
                        $objEjeCaractCapUnoAnt  = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                        ->findOneBy(array("detalleSolicitudId" => $objEjeSolicitud->getId(), 
                                                                          "caracteristicaId"   => $objCaractCapaciUnoAnt->getId()));
                        if( !is_object($objEjeCaractCapUnoAnt) )
                        {
                            throw new \Exception("No se encontró la capacidad uno anterior de la interface del elemento, ".
                                                 "por favor notificar a Sistemas.");
                        }
                        //obtengo el detalle de la capacidad dos anterior
                        $objEjeCaractCapDosAnt = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                        ->findOneBy(array("detalleSolicitudId" => $objEjeSolicitud->getId(), 
                                                                          "caracteristicaId"   => $objCaractCapaciDosAnt->getId()));
                        if( !is_object($objEjeCaractCapDosAnt) )
                        {
                            throw new \Exception("No se encontró la capacidad dos anterior de la interface del elemento, ".
                                                 "por favor notificar a Sistemas.");
                        }
                        //verifico si finalizo la ejecucion
                        if($strEjeEstado == 'Fallo')
                        {
                            $objEjeMasivoDet   = $emInfraestructura->getRepository('schemaBundle:InfoProcesoMasivoDet')
                                                        ->findOneBySolicitudId($objEjeSolicitud->getId());
                            if(is_object($objEjeMasivoDet))
                            {
                                $strObservacion = $objEjeMasivoDet->getObservacion();
                            }
                        }
                        //seteo la variables
                        $strCapacidadUnoAnt = $objEjeCaractCapUnoAnt->getValor();
                        $strCapacidadDosAnt = $objEjeCaractCapDosAnt->getValor();
                        $strCapacidadUno    = $objEjeCaractCapUno->getValor();
                        $strCapacidadDos    = $objEjeCaractCapDos->getValor();
                        //seteo el tipo de ejecución
                        if( $strCapacidadUnoAnt > $strCapacidadUno )
                        {
                            $strTipoEjecucion = 'DOWNGRADE';
                        }
                        else
                        {
                            $strTipoEjecucion = 'UPGRADE';
                        }
                    }
                    if( $strTipoProceso  == 'INFO')
                    {
                        $strTipoEjecucion = 'INFO';

                        //obtengo el detalle del historial
                        $objDetalleHistorial    = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(array("detalleSolicitudId" => $objEjeSolicitud->getId(), 
                                                                      "caracteristicaId"   => $objCaractHistorial->getId()));

                        $strNombreElemento  = "";
                        $strNombreInterface = "";                         
                        if( is_object($objDetalleHistorial) )
                        {
                            //obtengo el historial del elemento
                            $objHistorialElemento   = $emInfraestructura->getRepository('schemaBundle:InfoHistorialElemento')
                            ->find($objDetalleHistorial->getValor());
                            if( is_object($objHistorialElemento) )
                            {
                                $strObservacion         = $objHistorialElemento->getObservacion();
                                //seteo el nombre del elemento
                                $strNombreElemento      = $objHistorialElemento->getElementoId()->getNombreElemento();
                            }
                        }
                        
                        //obtengo el detalle de la interface del elemento
                        $objDetalleInterface    = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                        ->findOneBy(array("detalleSolicitudId" => $objEjeSolicitud->getId(), 
                                                                          "caracteristicaId"   => $objCaractInterface->getId()));
                        if( is_object($objDetalleInterface) )
                        {
                            $objInfoInterface       = $emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")
                                                                            ->find($objDetalleInterface->getValor());
                            if( is_object($objInfoInterface) )
                            {
                                //seteo el nombre de la interface
                                $strNombreInterface     = $objInfoInterface->getNombreInterfaceElemento(); 
                            }
                                               
                        }
                    }
                    //seteo el arreglo
                    $arrayDatosEjecuciones[] = array(
                        'strTipo'            => $strTipoEjecucion,
                        'strNombreElemento'  => $strNombreElemento,
                        'strNombreInterface' => $strNombreInterface,
                        'strCapacidadUnoAnt' => $strCapacidadUnoAnt,
                        'strCapacidadDosAnt' => $strCapacidadDosAnt,
                        'strCapacidadUno'    => $strCapacidadUno,
                        'strCapacidadDos'    => $strCapacidadDos,
                        'strEstado'          => $strEjeEstado,
                        'strObservacion'     => $strObservacion
                    );
                }
            }
            //se formula el json de respuesta
            $strJsonResultado = '{"status":"OK","total":"'.count($arrayDatosEjecuciones).'","registros":'.json_encode($arrayDatosEjecuciones).'}';
        }
        catch (\Exception $e)
        {
            $strJsonResultado = '{"status":"ERROR", "total":"0", "registros":[], "mensaje":"' . $e->getMessage() . '"}';
            $serviceUtil->insertError('Telcos+',
                                      'InfoElementoSwitchController.ajaxGetDetallesControlBwMasivoAction',
                                      $e->getMessage(),
                                      $strUsrSesion,
                                      $strIpClient
                                     );
        }
        $objResponse = new JsonResponse();
        $objResponse->setContent($strJsonResultado);
        return $objResponse;
    }

    /**
     * @Secure(roles="ROLE_315-7638")
     *
     * Documentación para el método 'ajaxValidateControlBwMasivoAction'.
     *
     * Método que sirve para verificar los datos de los switch e interfaces desde el archivo
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 10-11-2020
     *
     * @return Response $objResponse - Lista de switch e interfaces
     */
    public function ajaxValidateControlBwMasivoAction()
    {
        ini_set('max_execution_time', 300000);
        $objRequest     = $this->getRequest();
        $objSesion      = $objRequest->getSession();
        $serviceUtil    = $this->get('schema.Util');
        $strIpClient    = $objRequest->getClientIp();
        $strUsrSesion   = $objSesion->get('user');
        $strTipo        = $objRequest->get('tipo');
        $arrayDatos     = json_decode($objRequest->get('arrayData'));
        $arrayIdSwitchInt  = json_decode($objRequest->get('arrayIdSwitchInt'));
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');

        try
        {
            //arreglo de los resultados
            $arrayResultado = array();
            //arreglo de errores
            $arrayErrores   = array();
            foreach($arrayDatos as $objSwitchInterfaces)
            {
                $strElemento = trim(str_replace(['"',' '],'',$objSwitchInterfaces->elemento));
                $objElemento = $emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                        ->findOneBy(array('nombreElemento' => $strElemento,
                                                          'estado'         => 'Activo'));
                if(is_object($objElemento))
                {
                    $strTipoElemento = $objElemento->getModeloElementoId()->getTipoElementoId()->getNombreTipoElemento();
                    if( $strTipoElemento == 'SWITCH' )
                    {
                        if($strTipo == 'SW' && !in_array($objElemento->getId(), $arrayIdSwitchInt))
                        {
                            $arrayResultado[] = array(
                                'idElemento'  => $objElemento->getId(),
                                'elemento'    => $objElemento->getNombreElemento(),
                                'idInterface' => null,
                                'interface'   => null
                            );
                        }
                        elseif($strTipo == 'INT')
                        {
                            $strInterface = trim(str_replace(['"',' '],'',$objSwitchInterfaces->interface));
                            $objInterfaceElemento = $emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")
                                                            ->createQueryBuilder('p')
                                                            ->where("p.elementoId = :elementoId")
                                                            ->andWhere("p.nombreInterfaceElemento = :nombreInterfaceElemento")
                                                            ->andWhere("p.estado != :estado")
                                                            ->setParameter('elementoId', $objElemento->getId())
                                                            ->setParameter('nombreInterfaceElemento', $strInterface)
                                                            ->setParameter('estado',     'Eliminado')
                                                            ->setMaxResults(1)
                                                            ->getQuery()
                                                            ->getOneOrNullResult();
                            if(is_object($objInterfaceElemento) && !in_array($objInterfaceElemento->getId(), $arrayIdSwitchInt))
                            {
                                $arrayResultado[] = array(
                                    'idElemento'  => $objElemento->getId(),
                                    'elemento'    => $objElemento->getNombreElemento(),
                                    'idInterface' => $objInterfaceElemento->getId(),
                                    'interface'   => $objInterfaceElemento->getNombreInterfaceElemento()
                                );
                            }
                            else
                            {
                                $arrayErrores[] = 'No se encontró la inteface: '.$strElemento.' - '.$strInterface.'.';
                            }
                        }
                    }
                    elseif(!array_key_exists($strElemento,$arrayErrores))
                    {
                        $arrayErrores[$strElemento] = 'Elemento ('.$strElemento.') no es tipo switch.';
                    }
                }
                elseif(!array_key_exists($strElemento,$arrayErrores))
                {
                    $arrayErrores[$strElemento] = 'No se encontró el switch: '.$strElemento.'.';
                }
            }
            $strMensaje = 'Los switch e interfaces fueron agregados correctamente. Puede agregar mas switch e interfaces '.
                          'de forma individual o puede generar el Control BW Automático.';
            if($strTipo == 'SW')
            {
                $strMensaje = 'Los switch fueron agregados correctamente. Puede agregar mas switch de forma individual '.
                              'o puede generar el Control BW Automático.';
            }
            //verifico si hay errores
            if( count($arrayErrores) > 0 )
            {
                $strMensaje .= '<br>Errores:<br>'.implode("<br>", $arrayErrores);
            }
            //se formula el json de respuesta
            $strJsonResultado = '{"status":"OK", "total":"' . count($arrayResultado) . '","data":' . json_encode($arrayResultado) .
                                ', "mensaje":"'.$strMensaje.'"}';
        }
        catch (\Exception $e)
        {
            $strJsonResultado = '{"status":"ERROR", "total":"0", "data":[], "mensaje":"' . $e->getMessage() . '"}';
            $serviceUtil->insertError('Telcos+',
                                      'InfoElementoSwitchController.ajaxValidateControlBwMasivoAction',
                                      $e->getMessage(),
                                      $strUsrSesion,
                                      $strIpClient
                                     );
        }
        $objResponse = new JsonResponse();
        $objResponse->setContent($strJsonResultado);
        return $objResponse;
    }

    /**
     * @Secure(roles="ROLE_315-7638")
     *
     * Documentación para el método 'ajaxGetSwitchControlBwMasivoAction'.
     *
     * Método que sirve para obtener los datos de los switch
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 10-11-2020
     *
     * @return Response $objResponse - Lista de switch
     */
    public function ajaxGetSwitchControlBwMasivoAction()
    {
        $objRequest     = $this->getRequest();
        $objSesion      = $objRequest->getSession();
        $serviceUtil    = $this->get('schema.Util');
        $strIpClient    = $objRequest->getClientIp();
        $strUsrSesion   = $objSesion->get('user');
        $strQuery       = $objRequest->get('query');
        $intLimit       = intval($objRequest->get('limit'));
        $arrayIdSwitch  = json_decode($objRequest->get('arrayIdSwitch'));
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        try
        {
            //arreglo de los resultados
            $arrayResultado = array();
            if( !empty($strQuery) )
            {
                $arrayElementos = $emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                                ->createQueryBuilder('p')
                                                                ->join("p.modeloElementoId", "m")
                                                                ->join("m.tipoElementoId", "t")
                                                                ->where("p.nombreElemento LIKE :nombreElemento")
                                                                ->andWhere("p.estado != :estado")
                                                                ->andWhere("t.nombreTipoElemento = :nombreTipoElemento")
                                                                ->setParameter('nombreTipoElemento', 'SWITCH')
                                                                ->setParameter('nombreElemento',     '%'.$strQuery.'%')
                                                                ->setParameter('estado',             'Eliminado')
                                                                ->orderBy('p.nombreElemento', 'ASC')
                                                                ->setMaxResults($intLimit)
                                                                ->getQuery()
                                                                ->getResult();
                foreach($arrayElementos as $objElemento)
                {
                    if( !in_array($objElemento->getId(), $arrayIdSwitch) )
                    {
                        $arrayResultado[] = array(
                            'idElemento'  => $objElemento->getId(),
                            'elemento'    => $objElemento->getNombreElemento()
                        );
                    }
                }
            }
            //se formula el json de respuesta
            $strJsonResultado = '{"status":"OK", "total":"' . count($arrayResultado) . '","registros":' . json_encode($arrayResultado) . '}';
        }
        catch (\Exception $e)
        {
            $strJsonResultado = '{"status":"ERROR", "total":"0", "registros":[], "error":"' . $e->getMessage() . '"}';
            $serviceUtil->insertError('Telcos+',
                                      'InfoElementoSwitchController.ajaxGetSwitchControlBwMasivoAction',
                                      $e->getMessage(),
                                      $strUsrSesion,
                                      $strIpClient
                                     );
        }
        $objResponse = new JsonResponse();
        $objResponse->setContent($strJsonResultado);
        return $objResponse;
    }

    /**
     * @Secure(roles="ROLE_315-7638")
     *
     * Documentación para el método 'ajaxGetDiasNoPermitidosBwMasivoAction'.
     *
     * Método que sirve para obtener los dias en que no es permitido ejecutar el proceso masivo
     *
     * @author Diego Guamán <deguaman@telconet.ec>
     * @version 1.0 01-06-2023
     *
     * @return Response $objResponse - Lista de dias
     */
    public function ajaxGetDiasNoPermitidosBwMasivoAction()
    {
        $objRequest     = $this->getRequest();
        $objSesion      = $objRequest->getSession();
        $serviceUtil    = $this->get('schema.Util');
        $strIpClient    = $objRequest->getClientIp();
        $strUsrSesion   = $objSesion->get('user');
        $emGeneral      = $this->getDoctrine()->getManager("telconet_general");
        try 
        {
            //obtengo el parámetro cabecera de días no permitidos del control bw masivo
            $objParametroFechaCab   = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
            ->findOneByNombreParametro('DIAS_NO_EJECUCION_CONTROL_BW_INTERFACE');
            //verifico que exista el parámetro cabecera de días no permitidos del control bw masivo
            if (!is_object($objParametroFechaCab))
            {
                throw new \Exception("No se encontró el parámetro de días no permitidos del control bw automático, " .
                "por favor notificar a Sistemas.");
            }
            //obtengo el parámetro de detalles de días no permitidos del control bw masivo
            $objParametroFechaDet   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
            ->findBy(array(
                'parametroId' => $objParametroFechaCab->getId(),
                'estado'      => 'Activo'
            ));

            $arrayDiasNoPermitidos = [];
            if (!empty($objParametroFechaDet)) 
            {
                foreach ($objParametroFechaDet as $objData)
                {
                    $arrayDiasNoPermitidos[] = array('codigo' => $objData->getValor1());
                }
            }

            //se formula el json de respuesta
            $strJsonResultado = '{"status":"OK", "total":"' . 
                count($arrayDiasNoPermitidos) . '","registros":' . json_encode($arrayDiasNoPermitidos) . '}';
        }
        catch (\Exception $e)
        {
            $strJsonResultado = '{"status":"ERROR", "total":"0", "registros":[], "error":"' . $e->getMessage() . '"}';
            $serviceUtil->insertError(
                'Telcos+',
                'InfoElementoSwitchController.ajaxGetDiasNoPermitidosBwMasivoAction',
                $e->getMessage(),
                $strUsrSesion,
                $strIpClient
            );
        }
        $objResponse = new JsonResponse();
        $objResponse->setContent($strJsonResultado);
        return $objResponse;
    }

    /**
     * @Secure(roles="ROLE_315-7638")
     *
     * Documentación para el método 'ajaxGetInterfaceControlBwMasivoAction'.
     *
     * Método que sirve para obtener los datos de las interfaces del switch
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 10-11-2020
     *
     * @return Response $objResponse - Lista de interfaces
     */
    public function ajaxGetInterfaceControlBwMasivoAction()
    {
        $objRequest     = $this->getRequest();
        $objSesion      = $objRequest->getSession();
        $serviceUtil    = $this->get('schema.Util');
        $strIpClient    = $objRequest->getClientIp();
        $strUsrSesion   = $objSesion->get('user');
        $intIdElemento  = $objRequest->get('idElemento');
        $arrayIdInterface  = json_decode($objRequest->get('arrayIdInterface'));
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        try
        {
            //arreglo de los resultados
            $arrayResultado  = array();
            $arrayInterfaces = $emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")
                                                            ->createQueryBuilder('p')
                                                            ->where("p.elementoId = :elementoId")
                                                            ->andWhere("p.estado != :estado")
                                                            ->setParameter('elementoId', $intIdElemento)
                                                            ->setParameter('estado',     'Eliminado')
                                                            ->orderBy('p.nombreInterfaceElemento', 'ASC')
                                                            ->getQuery()
                                                            ->getResult();
            foreach($arrayInterfaces as $objInterface)
            {
                if( !in_array($objInterface->getId(), $arrayIdInterface) )
                {
                    $arrayResultado[] = array(
                        'idInterface' => $objInterface->getId(),
                        'interface'   => $objInterface->getNombreInterfaceElemento()
                    );
                }
            }
            //se formula el json de respuesta
            $strJsonResultado = '{"status":"OK", "total":"' . count($arrayResultado) . '","registros":' . json_encode($arrayResultado) . '}';
        }
        catch (\Exception $e)
        {
            $strJsonResultado = '{"status":"ERROR", "total":"0", "registros":[], "error":"' . $e->getMessage() . '"}';
            $serviceUtil->insertError('Telcos+',
                                      'InfoElementoSwitchController.ajaxGetInterfaceControlBwMasivoAction',
                                      $e->getMessage(),
                                      $strUsrSesion,
                                      $strIpClient
                                    );
        }
        $objResponse = new JsonResponse();
        $objResponse->setContent($strJsonResultado);
        return $objResponse;
    }

    /**
     * @Secure(roles="ROLE_315-7638")
     *
     * Documentación para el método 'ajaxGenerarControlBwMasivoAction'.
     *
     * Método que sirve para generar la solicitud de control bw masivo
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 10-11-2020
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 10-05-2022 Se modifica la función para que el archivo adjunto se suba al NFS
     *
     * @author Diego Guamán <deguaman@telconet.ec>
     * @version 1.2 01-06-2023 Se modifica la función convertir a entero el dia de ejecución
     *
     * @return Response $objResponse - resultado
     */
    public function ajaxGenerarControlBwMasivoAction()
    {
        ini_set('max_execution_time', 300000);
        $objRequest     = $this->getRequest();
        $objResponse    = new Response();
        $objResponse->headers->set('Content-Type', 'text/html');
        $objSesion      = $objRequest->getSession();
        $serviceUtil    = $this->get('schema.Util');
        $strIpClient    = $objRequest->getClientIp();
        $strUsrSesion   = $objSesion->get('user');
        $strCodEmpresa  = $objSesion->get('idEmpresa');
        $emComercial    = $this->getDoctrine()->getManager('telconet');
        $emGeneral      = $this->getDoctrine()->getManager("telconet_general");
        $emComunicacion = $this->getDoctrine()->getManager('telconet_comunicacion');
        $strPathTelcos  = $this->container->getParameter('path_telcos');
        $strFechaMasivo         = $objRequest->get('fecha');
        $strTipoMasivo          = $objRequest->get('tipo');
        $arrayDatosSwInterfaces = json_decode($objRequest->get('arrayDatosSwInterfaces'));
        $strPrefijoEmpresa = $objSesion->get('prefijoEmpresa');

        $emComercial->getConnection()->beginTransaction();
        $emGeneral->getConnection()->beginTransaction();
        $emComunicacion->getConnection()->beginTransaction();
        try
        {
            //verficar si la fecha esta vacío
            if( empty($strFechaMasivo) )
            {
                throw new \Exception("La fecha de ejecución esta vacía.");
            }
            //verficar si la fecha esta vacío
            if( empty($strTipoMasivo) )
            {
                throw new \Exception("El tipo de ejecución esta vacío.");
            }

            //obtengo el parámetro cabecera de días no permitidos del control bw masivo
            $objParametroFechaCab   = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                ->findOneByNombreParametro('DIAS_NO_EJECUCION_CONTROL_BW_INTERFACE');
            //verifico que exista el parámetro cabecera de días no permitidos del control bw masivo
            if( !is_object($objParametroFechaCab) )
            {
                throw new \Exception("No se encontró el parámetro de días no permitidos del control bw automático, ".
                                     "por favor notificar a Sistemas.");
            }
            //obtengo el objeto de la fecha
            $objFechaEjecucion      = new \DateTime($strFechaMasivo);
            $intDiaEjecucion        = intval($objFechaEjecucion->format('d'));
            //obtengo el parámetro de detalles de días no permitidos del control bw masivo
            $objParametroFechaDet   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->findOneBy(array(
                                                    'parametroId' => $objParametroFechaCab->getId(),
                                                    'valor1'      => $intDiaEjecucion,
                                                    'estado'      => 'Activo'
                                                ));
            //verifico que exista el parámetro de detalles de días no permitidos del control bw masivo
            if( is_object($objParametroFechaDet) )
            {
                throw new \Exception("No esta permitido la ejecución del Control Bw Automático en el día ".$objParametroFechaDet->getValor1().".");
            }

            //obtengo el parámetro cabecera de configuración del control bw masivo
            $objParametroDatosCab   = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                ->findOneByNombreParametro('DATOS_WS_NETWORKING_CONTROL_BW_INTERFACE');
            //verifico que exista el parámetro cabecera de configuración del control bw masivo
            if( !is_object($objParametroDatosCab) )
            {
                throw new \Exception("No se encontró el parámetro de configuración del control bw automático, ".
                                     "por favor notificar a Sistemas.");
            }
            //obtengo el parámetro de detalles de configuración del control bw masivo
            $objParametroDatosDet   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->findOneBy(array(
                                                    'parametroId' => $objParametroDatosCab->getId(),
                                                    'estado'      => 'Activo'
                                                ));
            //verifico que exista el parámetro de detalles de configuración del control bw masivo
            if( !is_object($objParametroDatosDet) )
            {
                throw new \Exception("No se encontró el parámetro de configuración del control bw automático, ".
                                     "por favor notificar a Sistemas.");
            }

            //seteo las variables
            $intMaxContador        = 250;
            $intMaximoSwInterfaces = 2000;
            $strRutaFile           = $objParametroDatosDet->getValor5();
            $strLimitesRegistros   = $objParametroDatosDet->getValor6();
            $strCreateFile         = $objParametroDatosDet->getValor7();
            if( !empty($strLimitesRegistros) && strpos($strLimitesRegistros,'-') !== false )
            {
                $arrayLimitesRegistros = explode("-", $strLimitesRegistros);
                if(count($arrayLimitesRegistros) == 2)
                {
                    $intMaxContador        = $arrayLimitesRegistros[0];
                    $intMaximoSwInterfaces = $arrayLimitesRegistros[1];
                }
            }

            //verifico la cantidad de registros
            if(count($arrayDatosSwInterfaces) > 0 && count($arrayDatosSwInterfaces) <= $intMaximoSwInterfaces )
            {
                //obtengo el tipo de solicitud de la ejecución
                $objTipoSolicitud       = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                      ->findOneBy(array('descripcionSolicitud' => 'SOLICITUD CONTROL BW AUTOMATICO',
                                                                        'estado'               => 'Activo'));
                //obtengo la caracteristica del id del documento
                $objCaractDocumento     = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneBy(array("descripcionCaracteristica" => 'ID_DOCUMENTO',
                                                                  "estado"                    => "Activo"));
                //obtengo la caracteristica de la fecha ejecución
                $objCaractFechaEje      = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneBy(array("descripcionCaracteristica" => 'FECHA_EJECUCION',
                                                                  "estado"                    => "Activo"));
                //obtengo la caracteristica del total de switch
                $objCaractTotalSwitch   = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneBy(array("descripcionCaracteristica" => 'TOTAL_SWITCH',
                                                                  "estado"                    => "Activo"));
                //obtengo la caracteristica del total de interfaces
                $objCarTotalInterfaces  = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneBy(array("descripcionCaracteristica" => 'TOTAL_INTERFACES',
                                                                  "estado"                    => "Activo"));
                //obtengo el objeto de la caracteristica por fecha de ejecución
                $objEjecucionVerificar  = $emComercial->getRepository("schemaBundle:InfoDetalleSolCaract")
                                                                ->createQueryBuilder('p')
                                                                ->join("p.detalleSolicitudId", "s")
                                                                ->join("s.tipoSolicitudId", "t")
                                                                ->join("p.caracteristicaId", "c")
                                                                ->where("p.estado    =  :estadoActivo")
                                                                ->andWhere("p.valor  =  :valor")
                                                                ->andWhere("s.estado != :estadoEliminado")
                                                                ->andWhere("t.id     =  :idTipoSolicitud")
                                                                ->andWhere("c.id     =  :idCaracteristica")
                                                                ->setParameter('estadoActivo',     'Activo')
                                                                ->setParameter('valor',            $strFechaMasivo)
                                                                ->setParameter('estadoEliminado',  'Eliminado')
                                                                ->setParameter('idTipoSolicitud',  $objTipoSolicitud->getId())
                                                                ->setParameter('idCaracteristica', $objCaractFechaEje->getId())
                                                                ->setMaxResults(1)
                                                                ->getQuery()
                                                                ->getOneOrNullResult();
                //verifico si existe un detalle de ejecución con esta fecha
                if( is_object($objEjecucionVerificar) )
                {
                    throw new \Exception("No se pudo Generar el Control BW Automático, ya se encuentra programada una ejecución ".
                                         "para la fecha $strFechaMasivo.");
                }

                //verificar el tipo de masivo
                if($strTipoMasivo == 'SW')
                {
                    //obtengo el parámetro cabecera de filtro de switch del control bw masivo
                    $objParametroCabMasivo = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                        ->findOneByNombreParametro('ELEMENTOS_ARRAY_CONTROL_BW_INTERFACE');
                    //verifico que exista el parámetro cabecera de filtro de switch del control bw masivo
                    if( !is_object($objParametroCabMasivo) )
                    {
                        throw new \Exception("No se encontró el parámetro del filtro de switch en el control bw automático, ".
                                             "por favor notificar a Sistemas.");
                    }
                }
                elseif($strTipoMasivo == 'INT')
                {
                    //obtengo el parámetro cabecera de filtro de interfaces del control bw masivo
                    $objParametroCabMasivo = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                        ->findOneByNombreParametro('INTERFACE_ARRAY_CONTROL_BW_INTERFACE');
                    //verifico que exista el parámetro cabecera de filtro de interfaces del control bw masivo
                    if( !is_object($objParametroCabMasivo) )
                    {
                        throw new \Exception("No se encontró el parámetro del filtro de interfaces en el control bw automático, ".
                                             "por favor notificar a Sistemas.");
                    }
                }

                //ingreso la ejecución masiva
                $objSolicitudEjecucion = new InfoDetalleSolicitud();
                $objSolicitudEjecucion->setTipoSolicitudId($objTipoSolicitud);
                $objSolicitudEjecucion->setEstado('Pendiente');
                $objSolicitudEjecucion->setUsrCreacion($strUsrSesion);
                $objSolicitudEjecucion->setFeCreacion(new \DateTime('now'));
                $emComercial->persist($objSolicitudEjecucion);
                $emComercial->flush();

                //guarda la caracteristica de la fecha ejecución
                $objSolCaractFechaEje   = new InfoDetalleSolCaract();
                $objSolCaractFechaEje->setDetalleSolicitudId($objSolicitudEjecucion);
                $objSolCaractFechaEje->setCaracteristicaId($objCaractFechaEje);
                $objSolCaractFechaEje->setEstado("Activo");
                $objSolCaractFechaEje->setUsrCreacion($strUsrSesion);
                $objSolCaractFechaEje->setFeCreacion(new \DateTime('now'));
                $objSolCaractFechaEje->setValor($strFechaMasivo);
                $emComercial->persist($objSolCaractFechaEje);
                $emComercial->flush();

                //seteo las variables para contar la cantidad de switch
                $arrayContSwitch   = array();
                //obtengo los arreglos separados por la cantidad máxima permitida
                $arrayDatosMasivos = array_chunk($arrayDatosSwInterfaces, $intMaxContador);
                //recorro el arreglo
                foreach($arrayDatosMasivos as $arrayDatosSwInt)
                {
                    $strIdsSwitchInterfaces = '';
                    foreach($arrayDatosSwInt as $objDatosSwInt)
                    {
                        if($strTipoMasivo == 'SW')
                        {
                            $intIdMasivo = $objDatosSwInt->idElemento;
                        }
                        elseif($strTipoMasivo == 'INT')
                        {
                            $arrayContSwitch[$objDatosSwInt->idElemento] = 1;
                            $intIdMasivo = $objDatosSwInt->idInterface;
                        }
                        if(empty($strIdsSwitchInterfaces))
                        {
                            $strIdsSwitchInterfaces = $intIdMasivo;
                        }
                        else
                        {
                            $strIdsSwitchInterfaces .= ','.$intIdMasivo;
                        }
                    }
                    //ingreso el filtro del masivo
                    $objParametroDet = new AdmiParametroDet();
                    $objParametroDet->setParametroId($objParametroCabMasivo);
                    $objParametroDet->setDescripcion('LISTA VALORES');
                    $objParametroDet->setValor1($strIdsSwitchInterfaces);
                    $objParametroDet->setValor2($objSolicitudEjecucion->getId());
                    $objParametroDet->setIpCreacion($strIpClient);
                    $objParametroDet->setUsrCreacion($strUsrSesion);
                    $objParametroDet->setFeCreacion(new \DateTime('now'));
                    $objParametroDet->setEstado('Activo');
                    $emGeneral->persist($objParametroDet);
                    $emGeneral->flush();
                }

                //guarda la caracteristica de la cantidad switch e interfaces
                $objSolCaractTotalSwitch = new InfoDetalleSolCaract();
                $objSolCaractTotalSwitch->setDetalleSolicitudId($objSolicitudEjecucion);
                $objSolCaractTotalSwitch->setCaracteristicaId($objCaractTotalSwitch);
                $objSolCaractTotalSwitch->setEstado("Activo");
                $objSolCaractTotalSwitch->setUsrCreacion($strUsrSesion);
                $objSolCaractTotalSwitch->setFeCreacion(new \DateTime('now'));
                $objSolCaractTotalInt    = new InfoDetalleSolCaract();
                $objSolCaractTotalInt->setDetalleSolicitudId($objSolicitudEjecucion);
                $objSolCaractTotalInt->setCaracteristicaId($objCarTotalInterfaces);
                $objSolCaractTotalInt->setEstado("Activo");
                $objSolCaractTotalInt->setUsrCreacion($strUsrSesion);
                $objSolCaractTotalInt->setFeCreacion(new \DateTime('now'));
                if($strTipoMasivo == 'SW')
                {
                    $objSolCaractTotalSwitch->setValor(count($arrayDatosSwInterfaces));
                    $objSolCaractTotalInt->setValor('Todas');
                }
                elseif($strTipoMasivo == 'INT')
                {
                    $objSolCaractTotalSwitch->setValor(count($arrayContSwitch));
                    $objSolCaractTotalInt->setValor(count($arrayDatosSwInterfaces));
                }
                $emComercial->persist($objSolCaractTotalSwitch);
                $emComercial->flush();
                $emComercial->persist($objSolCaractTotalInt);
                $emComercial->flush();

                if($strCreateFile == 'SI')
                {
                    //verifico si hay archivo
                    $objFile = $objRequest->files->get('archivo');
                    if(!empty($objFile) && $objFile->getError() == 0)
                    {
                        $strTipo          = strtoupper($objFile->getClientOriginalExtension());
                        $objTipoDocumento = $emComunicacion->getRepository('schemaBundle:AdmiTipoDocumento')
                                                                 ->findOneByExtensionTipoDocumento($strTipo);
                        if( !is_object($objTipoDocumento) )
                        {
                            throw new \Exception("No existe el tipo de documento $strTipo, por favor notificar a Sistemas.");
                        }

                        //se setea la ruta del archivo
                        $strNombreArchivo = str_replace('-','_',$strFechaMasivo).'_'.$strUsrSesion.'.'.strtolower($strTipo);
                        $strPatch         = $strPathTelcos.$strRutaFile.$strNombreArchivo;
                        
                        $arrayPathAdicional = [];
                        $strFileBase64 = base64_encode(file_get_contents($objFile->getPathName()));
                        $arrayParamNfs = array( 'prefijoEmpresa'       => $strPrefijoEmpresa,
                                                'strApp'               => "TelcosWeb",
                                                'strSubModulo'         => "ControlBwMasivo",
                                                'arrayPathAdicional'   => $arrayPathAdicional,
                                                'strBase64'            => $strFileBase64,
                                                'strNombreArchivo'     => $strNombreArchivo,
                                                'strUsrCreacion'       => $strUsrSesion);
                        $arrayRespNfs = $serviceUtil->guardarArchivosNfs($arrayParamNfs);
                        if(isset($arrayRespNfs) && $arrayRespNfs['intStatus'] == 200)
                        {
                            //inserta el documento
                            $objInfoDocumento = new InfoDocumento();
                            $objInfoDocumento->setTipoDocumentoId($objTipoDocumento);
                            $objInfoDocumento->setNombreDocumento('Adjunto Control Bw Masivo');
                            $objInfoDocumento->setMensaje('Documento que se adjunta en el Control Bw Automático');
                            $objInfoDocumento->setUbicacionFisicaDocumento($arrayRespNfs['strUrlArchivo']);
                            $objInfoDocumento->setUbicacionLogicaDocumento($strNombreArchivo);
                            $objInfoDocumento->setEstado('Activo');
                            $objInfoDocumento->setFeCreacion(new \DateTime('now'));
                            $objInfoDocumento->setFechaDocumento(new \DateTime('now'));
                            $objInfoDocumento->setUsrCreacion($strUsrSesion);
                            $objInfoDocumento->setIpCreacion($strIpClient);
                            $objInfoDocumento->setEmpresaCod($strCodEmpresa);
                            $emComunicacion->persist($objInfoDocumento);
                            $emComunicacion->flush();

                            //guarda la caracteristica del documento
                            $objSolCaractDocumento = new InfoDetalleSolCaract();
                            $objSolCaractDocumento->setDetalleSolicitudId($objSolicitudEjecucion);
                            $objSolCaractDocumento->setCaracteristicaId($objCaractDocumento);
                            $objSolCaractDocumento->setEstado("Activo");
                            $objSolCaractDocumento->setUsrCreacion($strUsrSesion);
                            $objSolCaractDocumento->setFeCreacion(new \DateTime('now'));
                            $objSolCaractDocumento->setValor($objInfoDocumento->getId());
                            $emComercial->persist($objSolCaractDocumento);
                            $emComercial->flush();
                        }
                        else
                        {
                            throw new \Exception('Ocurrió un error al subir archivo al servidor Nfs : '.$arrayRespNfs['strMensaje']);
                        }
                    }
                }
            }
            else
            {
                throw new \Exception("La cantidad de registros es menor a cero o supera el limite de $intMaximoSwInterfaces.");
            }

            //guardar todos los cambios
            if($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->flush();
                $emComercial->getConnection()->commit();
                $emComercial->getConnection()->close();
            }
            if($emGeneral->getConnection()->isTransactionActive())
            {
                $emGeneral->flush();
                $emGeneral->getConnection()->commit();
                $emGeneral->getConnection()->close();
            }
            if($emComunicacion->getConnection()->isTransactionActive())
            {
                $emComunicacion->flush();
                $emComunicacion->getConnection()->commit();
                $emComunicacion->getConnection()->close();
            }

            //se formula el json de respuesta
            $strJsonResultado = '{"success":true,"mensaje":"Se genero correctamente la solicitud de Control Bw Automático"}';
        }
        catch (\Exception $e)
        {
            //realizo el rollback de los cambios
            if($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->rollback();
                $emComercial->getConnection()->close();
            }
            if($emGeneral->getConnection()->isTransactionActive())
            {
                $emGeneral->getConnection()->rollback();
                $emGeneral->getConnection()->close();
            }
            if($emComunicacion->getConnection()->isTransactionActive())
            {
                $emComunicacion->getConnection()->rollback();
                $emComunicacion->getConnection()->close();
            }
            $strJsonResultado = '{"success":false,"mensaje":"' . $e->getMessage() . '"}';
            $serviceUtil->insertError('Telcos+',
                                      'InfoElementoSwitchController.ajaxGenerarControlBwMasivoAction',
                                      $e->getMessage(),
                                      $strUsrSesion,
                                      $strIpClient
                                     );
        }
        $objResponse->setContent($strJsonResultado);
        return $objResponse;
    }
}