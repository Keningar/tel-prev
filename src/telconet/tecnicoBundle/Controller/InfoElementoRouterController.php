<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
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

use telconet\tecnicoBundle\Resources\util\Util;
use telconet\tecnicoBundle\Service\InfoServicioTecnicoService;
use telconet\schemaBundle\Form\InfoElementoRouterType;

use Symfony\Component\HttpFoundation\Response;

/**
 * Clase utilizada para manejar metodos que permiten realizar acciones de administracion de elementos Routers
 * 
 * @author Francisco Adum <fadum@telconet.ec>
 * @version 1.0 10-12-2015
 */
class InfoElementoRouterController extends Controller
{ 
    /**
     * Funcion que sirve para agregar los permisos y cargar la pantalla de consulta
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 9-12-2015
     */
    public function indexRouterAction()
    {    
        $rolesPermitidos = array();
        //MODULO 316 - ROUTER
        
        if (true === $this->get('security.context')->isGranted('ROLE_316-5'))
        {
            $rolesPermitidos[] = 'ROLE_316-5'; //editar elemento router
        }
        if (true === $this->get('security.context')->isGranted('ROLE_316-8'))
        {
            $rolesPermitidos[] = 'ROLE_316-8'; //eliminar elemento router
        }
        if (true === $this->get('security.context')->isGranted('ROLE_316-6'))
        {
            $rolesPermitidos[] = 'ROLE_316-6'; //ver elemento router
        }
        
        
        return $this->render('tecnicoBundle:InfoElementoRouter:index.html.twig', array(
            'rolesPermitidos' => $rolesPermitidos
        ));
    }
    
    /**
     * Funcion que sirve para crear el formulario para ingresar un nuevo router
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 10-12-2015
     */
    public function newRouterAction()
    {
        $request    = $this->get('request');
        $session    = $request->getSession();
        $empresaId  = $session->get('idEmpresa');
        $entity     = new InfoElemento();
        $form       = $this->createForm(new InfoElementoRouterType(array("empresaId" => $empresaId)), $entity);

        return $this->render('tecnicoBundle:InfoElementoRouter:new.html.twig', 
                             array(
                                    'entity' => $entity,
                                    'form'   => $form->createView()
                                  )
                            );
    }
    
    /**
     * Funcion que sirve para ingresar los datos de un elemento router en la base de datos
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 10-12-2015
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 19-09-2018 - Se agrega la validación de las coordenadas de latitud y longitud de acuerdo al país en sesión
     * 
     */
    public function createRouterAction()
    {
        $peticion               = $this->get('request');
        $session                = $peticion->getSession();
        $em                     = $this->get('doctrine')->getManager('telconet_infraestructura');
        $elementoRouter         = new InfoElemento();
        $form                   = $this->createForm(new InfoElementoRouterType(), $elementoRouter);
        $parametros             = $peticion->request->get('telconet_schemabundle_infoelementoroutertype');
        $nombreElemento         = $parametros['nombreElemento'];
        $ip                     = $parametros['ipElemento'];
        $modeloElementoId       = $parametros['modeloElementoId'];
        $nodoElementoId         = $parametros['nodoElementoId'];
        $intRackElementoId      = $parametros['rackElementoId'];
        $intUnidadRack          = $parametros['unidadRack'];
        $descripcionElemento    = $parametros['descripcionElemento'];
        $strUnidadesOcupadas    = "";
        $intUnidadMaximaU       = 0;
                
        $em->getConnection()->beginTransaction();
        
        try
        {
            $form->bind($peticion);
            
            if ($form->isValid()) 
            {
                //verificar que no se repita la ip
                $ipRepetida = $em->getRepository('schemaBundle:InfoIp')->findOneBy(array("ip" => $ip, "estado" => "Activo"));
                if($ipRepetida)
                {
                    $this->get('session')->getFlashBag()->add('notice', 'Ip ya existe en otro Elemento, favor revisar!');
                    return $this->redirect($this->generateUrl('elementorouter_newRouter'));
                }

                //verificar que el nombre del elemento no se repita
                $elementoRepetido = $em->getRepository('schemaBundle:InfoElemento')
                                       ->findOneBy(array("nombreElemento" => $nombreElemento, "estado" => "Activo"));
                if($elementoRepetido)
                {
                    $this->get('session')->getFlashBag()->add('notice', 'Nombre ya existe en otro Elemento, favor revisar!');
                    return $this->redirect($this->generateUrl('elementorouter_newRouter'));
                }

                $modeloElemento = $em->find('schemaBundle:AdmiModeloElemento', $modeloElementoId);
                
                //verificar que se haya ingresado la posicion del elemento en el rack
                if ( $intRackElementoId === "")
                {
                    $em->getConnection()->rollback();
                    $em->getConnection()->close();
                    $this->get('session')->getFlashBag()->add('notice', 
                                                              'Es obligatorio ingresar la posicion del rack!');
                    return $this->redirect($this->generateUrl('elementorouter_newRouter'));
                }

                $elementoRouter->setNombreElemento($nombreElemento);
                $elementoRouter->setDescripcionElemento($descripcionElemento);
                $elementoRouter->setModeloElementoId($modeloElemento);
                $elementoRouter->setUsrResponsable($session->get('user'));
                $elementoRouter->setUsrCreacion($session->get('user'));
                $elementoRouter->setFeCreacion(new \DateTime('now'));
                $elementoRouter->setIpCreacion($peticion->getClientIp());
                $elementoRouter->setEstado("Activo");
                $em->persist($elementoRouter);
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
                        $interfaceElemento->setElementoId($elementoRouter);
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
                        $em->getConnection()->rollback();
                        $em->getConnection()->close();
                        $this->get('session')->getFlashBag()->add('notice', 
                                                                  'No se puede ubicar el Router en el Rack porque se sobrepasa '
                                                                . 'el tamaño de unidades!');
                        return $this->redirect($this->generateUrl('elementorouter_newRouter'));
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
                            $objRelacionElemento->setElementoIdB($elementoRouter->getId());
                            $objRelacionElemento->setTipoRelacion("CONTIENE");
                            $objRelacionElemento->setObservacion("Rack contiene Router");
                            $objRelacionElemento->setEstado("Activo");
                            $objRelacionElemento->setUsrCreacion($session->get('user'));
                            $objRelacionElemento->setFeCreacion(new \DateTime('now'));
                            $objRelacionElemento->setIpCreacion($peticion->getClientIp());
                            $em->persist($objRelacionElemento);
                        }
                    }
                    if($strUnidadesOcupadas != "")
                    {
                        $em->getConnection()->rollback();
                        $em->getConnection()->close();
                        $this->get('session')
                             ->getFlashBag()
                             ->add('notice', 'No se puede ubicar el Router en el Rack porque estan ocupadas las unidades : ' . $strUnidadesOcupadas);
                        return $this->redirect($this->generateUrl('elementorouter_newRouter'));
                    }
                }
                else
                {
                    //relacion elemento
                    $relacionElemento = new InfoRelacionElemento();
                    $relacionElemento->setElementoIdA($nodoElementoId);
                    $relacionElemento->setElementoIdB($elementoRouter->getId());
                    $relacionElemento->setTipoRelacion("CONTIENE");
                    $relacionElemento->setObservacion("Nodo contiene Router");
                    $relacionElemento->setEstado("Activo");
                    $relacionElemento->setUsrCreacion($session->get('user'));
                    $relacionElemento->setFeCreacion(new \DateTime('now'));
                    $relacionElemento->setIpCreacion($peticion->getClientIp());
                    $em->persist($relacionElemento);
                }


                //ip elemento
                $ipElemento = new InfoIp();
                $ipElemento->setElementoId($elementoRouter->getId());
                $ipElemento->setIp(trim($ip));
                $ipElemento->setVersionIp("IPV4");
                $ipElemento->setEstado("Activo");
                $ipElemento->setUsrCreacion($session->get('user'));
                $ipElemento->setFeCreacion(new \DateTime('now'));
                $ipElemento->setIpCreacion($peticion->getClientIp());
                $em->persist($ipElemento);

                //historial elemento
                $historialElemento = new InfoHistorialElemento();
                $historialElemento->setElementoId($elementoRouter);
                $historialElemento->setEstadoElemento("Activo");
                $historialElemento->setObservacion("Se ingreso un Router");
                $historialElemento->setUsrCreacion($session->get('user'));
                $historialElemento->setFeCreacion(new \DateTime('now'));
                $historialElemento->setIpCreacion($peticion->getClientIp());
                $em->persist($historialElemento);

                //tomar datos nodo
                $nodoEmpresaElementoUbicacion = $em->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                                                  ->findOneBy(array("elementoId" => $nodoElementoId));
                $nodoUbicacion = $em->getRepository('schemaBundle:InfoUbicacion')->find($nodoEmpresaElementoUbicacion->getUbicacionId()->getId());
                
                $serviceInfoElemento        = $this->get("tecnico.InfoElemento");
                $arrayRespuestaCoordenadas  = $serviceInfoElemento->validarLimitesCoordenadasElemento(array(
                                                                                                            "latitudElemento"       => 
                                                                                                            $nodoUbicacion->getLatitudUbicacion(),
                                                                                                            "longitudElemento"      => 
                                                                                                            $nodoUbicacion->getLongitudUbicacion(),
                                                                                                            "msjTipoElemento"       => "del nodo ",
                                                                                                            "msjTipoElementoPadre"  =>
                                                                                                            "que contiene al router ",
                                                                                                            "msjAdicional"          => 
                                                                                                            "por favor regularizar en la "
                                                                                                            ."administración de Nodos"
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
                $ubicacionElemento->setIpCreacion($peticion->getClientIp());
                $em->persist($ubicacionElemento);

                //empresa elemento ubicacion
                $empresaElementoUbica = new InfoEmpresaElementoUbica();
                $empresaElementoUbica->setEmpresaCod($session->get('idEmpresa'));
                $empresaElementoUbica->setElementoId($elementoRouter);
                $empresaElementoUbica->setUbicacionId($ubicacionElemento);
                $empresaElementoUbica->setUsrCreacion($session->get('user'));
                $empresaElementoUbica->setFeCreacion(new \DateTime('now'));
                $empresaElementoUbica->setIpCreacion($peticion->getClientIp());
                $em->persist($empresaElementoUbica);

                //empresa elemento
                $empresaElemento = new InfoEmpresaElemento();
                $empresaElemento->setElementoId($elementoRouter);
                $empresaElemento->setEmpresaCod($session->get('idEmpresa'));
                $empresaElemento->setEstado("Activo");
                $empresaElemento->setUsrCreacion($session->get('user'));
                $empresaElemento->setIpCreacion($peticion->getClientIp());
                $empresaElemento->setFeCreacion(new \DateTime('now'));
                $em->persist($empresaElemento);

                $em->flush();
                $em->getConnection()->commit();
                                
                return $this->redirect($this->generateUrl('elementorouter_showRouter', array('id' => $elementoRouter->getId())));
            }
        }
        catch (\Exception $e) 
        {
            if ($em->getConnection()->isTransactionActive())
            {
                $em->getConnection()->rollback();
                $em->getConnection()->close();
            }
            
            $mensaje = "Error: ".$e->getMessage();
        }
        $this->get('session')
             ->getFlashBag()->add('notice', 'Existieron problemas al procesar la transaccion, favor notificar a Sistemas.\n'.$mensaje);
        return $this->redirect($this->generateUrl('elementorouter_newRouter'));   
    }
    
    /**
     * Funcion que sirve para crear el formulario para editar un router
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 10-12-2015
     */
    public function editRouterAction($id)
    {
        $this->get('session')->getFlashBag()->add('notice', 'No existe opcion de edicion!');
        return $this->redirect($this->generateUrl('elementorouter'));
    }
    
    /**
     * Funcion que sirve para editar los datos de un elemento router en la base de datos
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 10-12-2015
     */
    public function updateRouterAction($id)
    {
        $this->get('session')->getFlashBag()->add('notice', 'No existe opcion de edicion!');
        return $this->redirect($this->generateUrl('elementorouter'));
    }
    
    /**
     * Funcion que sirve para mostrar los datos de un elemento router
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 10-12-2015
     */
    public function showRouterAction($id)
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
        }//(null == $objElemento = $emInfraestructura->find('schemaBundle:InfoElemento', $id))

        return $this->render('tecnicoBundle:InfoElementoRouter:show.html.twig', array(
            'elemento'              => $objElemento,
            'ipElemento'            => $ipElemento,
            'historialElemento'     => $arrayHistorial,
            'ubicacion'             => $ubicacion,
            'jurisdiccion'          => $jurisdiccion,
            'flag'                  => $objRequest->get('flag')
        ));
    }
    
    /**
     * Función que sirve para obtener los routers 
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 8-12-2015
     * @param $peticion [nombreElemento, modeloElemento, marcaElemento, canton, jurisdiccion, estado, tipoElemento]
     * @return $respuesta objJson
     */
    public function getEncontradosRouterAction()
    {
        ini_set('max_execution_time', 3000000);
        
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $session    = $this->get('session');
        $em         = $this->getDoctrine()->getManager('telconet_infraestructura');
        $peticion   = $this->get('request');
        
        $nombreElemento     = $peticion->query->get('nombreElemento');
        $modeloElemento     = $peticion->query->get('modeloElemento');
        $marcaElemento      = $peticion->query->get('marcaElemento');
        $canton             = $peticion->query->get('canton');
        $jurisdiccion       = $peticion->query->get('jurisdiccion');
        $estado             = $peticion->query->get('estado');
        $tipoElemento       = $peticion->query->get('tipoElemento');
        $idEmpresa          = $session->get('idEmpresa');
        $objTipoElemento    = $em->getRepository('schemaBundle:AdmiTipoElemento')->findOneBy(array( "nombreTipoElemento" =>$tipoElemento));
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $arrayParametros = array(
                                    'nombreElemento'    => strtoupper($nombreElemento),
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
        
        $objJson = $this->getDoctrine()->getManager("telconet_infraestructura")
                        ->getRepository('schemaBundle:InfoElemento')
                        ->generarJsonElementosPorParametros($arrayParametros);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /**
     * Funcion que sirve para eliminar los elementos routers
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 8-12-2015
     */
    public function deleteAjaxRouterAction()
    {
        $this->get('session')->getFlashBag()->add('notice', 'No existe opcion de Eliminar un Elemento!');
        return $this->redirect($this->generateUrl('elementorouter'));
    }
    
}