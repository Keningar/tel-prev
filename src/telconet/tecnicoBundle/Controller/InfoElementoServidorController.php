<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\tecnicoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoElemento;
use telconet\schemaBundle\Entity\AdmiModeloElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElementoUbica;
use telconet\schemaBundle\Entity\InfoIp;
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Entity\InfoUbicacion;
use telconet\schemaBundle\Entity\InfoRelacionElemento;
use telconet\schemaBundle\Form\InfoElementoServidorType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Symfony\Component\HttpFoundation\Response;

/**
 * Clase que sirve para la administracion de los elementos Servidor
 * 
 * @author Francisco Adum <fadum@telconet.ec>
 * @version 1.0 19-02-2015
 */
class InfoElementoServidorController extends Controller
{ 
    /**
     * Funcion que sirve para cargar la pagina inicial de la administracion
     * de Elementos del Tipo Servidor
     *
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 19-02-2015
     * @Secure(roles="ROLE_196-1")
     */
    public function indexServidorAction()
    {
        if(true === $this->get('security.context')->isGranted('ROLE_196-1'))
        {
            $rolesPermitidos[] = 'ROLE_196-1'; //index
        }
        if(true === $this->get('security.context')->isGranted('ROLE_196-46'))
        {
            $rolesPermitidos[] = 'ROLE_196-46'; //encontrados
        }
        if(true === $this->get('security.context')->isGranted('ROLE_196-6'))
        {
            $rolesPermitidos[] = 'ROLE_196-6'; //show
        }
        if(true === $this->get('security.context')->isGranted('ROLE_196-2'))
        {
            $rolesPermitidos[] = 'ROLE_196-2'; //new
        }
        if(true === $this->get('security.context')->isGranted('ROLE_196-3'))
        {
            $rolesPermitidos[] = 'ROLE_196-3'; //create
        }
        if(true === $this->get('security.context')->isGranted('ROLE_196-4'))
        {
            $rolesPermitidos[] = 'ROLE_196-4'; //edit
        }
        if(true === $this->get('security.context')->isGranted('ROLE_196-5'))
        {
            $rolesPermitidos[] = 'ROLE_196-5'; //update
        }
        if(true === $this->get('security.context')->isGranted('ROLE_196-8'))
        {
            $rolesPermitidos[] = 'ROLE_196-8'; //delete
        }
        if(true === $this->get('security.context')->isGranted('ROLE_196-9'))
        {
            $rolesPermitidos[] = 'ROLE_196-9'; //delete ajax
        }
        return $this->render('tecnicoBundle:InfoElementoServidor:index.html.twig',array(
            'rolesPermitidos' => $rolesPermitidos
        ));
    }

    /**
     * Funcion que sirve para cargar el formulario de nuevo servidor
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 19-02-2015
     * @Secure(roles="ROLE_196-2")
     */
    public function newServidorAction(){
        $entity = new InfoElemento();
        $form   = $this->createForm(new InfoElementoServidorType(), $entity);

        return $this->render('tecnicoBundle:InfoElementoServidor:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }
    
    /**
     * Funcion que sirve para Crear un elemento Servidor
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 19-02-2015
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 19-09-2018 - Se agrega la validación de las coordenadas de latitud y longitud de acuerdo al país en sesión
     * 
     * @Secure(roles="ROLE_196-3")
     */
    public function createServidorAction()
    {
        $request            = $this->get('request');
        $session            = $request->getSession();
        $em                 = $this->get('doctrine')->getManager('telconet_infraestructura');
        $parametros          = $request->request->get('telconet_schemabundle_infoelementoservidortype');        
        $nombreElemento      = $parametros['nombreElemento'];
        $ipServidor          = $parametros['ipElemento'];        
        $modeloElementoId    = $parametros['modeloElementoId'];
        $parroquiaId         = $parametros['parroquiaId'];
        $alturaSnm           = $parametros['alturaSnm'];
        $longitudUbicacion   = $parametros['longitudUbicacion'];
        $latitudUbicacion    = $parametros['latitudUbicacion'];
        $direccionUbicacion  = $parametros['direccionUbicacion'];
        $descripcionElemento = $parametros['descripcionElemento'];      
        
        $em->beginTransaction();
        try
        {
            $elementoServidor   = new InfoElemento();
            $form               = $this->createForm(new InfoElementoServidorType(), $elementoServidor);
            //verificar nombre repetido
            $objetoRepetido = $em->getRepository('schemaBundle:InfoElemento')
                                 ->findOneBy(array("nombreElemento" => $nombreElemento, "estado" => "Activo"));
            if($objetoRepetido)
            {
                throw new \Exception('Ya existe un elemento con ese NOMBRE, favor revisar!');
            }

            //verificar ip repetida
            $objetoIpRepetido = $em->getRepository('schemaBundle:InfoIp')
                                   ->findOneBy(array("ip" => $ipServidor, "estado" => "Activo"));
            if($objetoIpRepetido)
            {
                throw new \Exception('Ya existe un elemento con esa IP, favor revisar!');
            }

            $modeloElemento = $em->find('schemaBundle:AdmiModeloElemento', $modeloElementoId);
            $elementoServidor->setNombreElemento($nombreElemento);
            $elementoServidor->setDescripcionElemento($descripcionElemento);
            $elementoServidor->setModeloElementoId($modeloElemento);
            $elementoServidor->setEstado('Activo');
            $elementoServidor->setUsrResponsable($session->get('user'));
            $elementoServidor->setUsrCreacion($session->get('user'));
            $elementoServidor->setFeCreacion(new \DateTime('now'));
            $elementoServidor->setIpCreacion($request->getClientIp());  

            $form->handleRequest($request);
            
            $em->persist($elementoServidor);
            $em->flush();

            //ip elemento
            $ipElemento = new InfoIp();
            $ipElemento->setElementoId($elementoServidor->getId());
            $ipElemento->setIp($ipServidor);
            $ipElemento->setEstado("Activo");
            $ipElemento->setVersionIp("IPV4");
            $ipElemento->setUsrCreacion($session->get('user'));
            $ipElemento->setFeCreacion(new \DateTime('now'));
            $ipElemento->setIpCreacion($request->getClientIp());
            $em->persist($ipElemento);

            //historial elemento
            $historialElemento = new InfoHistorialElemento();
            $historialElemento->setElementoId($elementoServidor);
            $historialElemento->setEstadoElemento("Activo");
            $historialElemento->setObservacion("Se ingreso un Servidor");
            $historialElemento->setUsrCreacion($session->get('user'));
            $historialElemento->setFeCreacion(new \DateTime('now'));
            $historialElemento->setIpCreacion($request->getClientIp());
            $em->persist($historialElemento);

            $serviceInfoElemento        = $this->get("tecnico.InfoElemento");
            $arrayRespuestaCoordenadas  = $serviceInfoElemento->validarLimitesCoordenadasElemento(array("latitudElemento"   => $latitudUbicacion,
                                                                                                        "longitudElemento"  => $longitudUbicacion,
                                                                                                        "msjTipoElemento"   => "del servidor "));
            if($arrayRespuestaCoordenadas["status"] === "ERROR")
            {
                throw new \Exception($arrayRespuestaCoordenadas['mensaje']);
            }
            //info ubicacion
            $parroquia = $em->find('schemaBundle:AdmiParroquia', $parroquiaId);
            $ubicacionElemento = new InfoUbicacion();
            $ubicacionElemento->setLatitudUbicacion($latitudUbicacion);
            $ubicacionElemento->setLongitudUbicacion($longitudUbicacion);
            $ubicacionElemento->setDireccionUbicacion($direccionUbicacion);
            $ubicacionElemento->setAlturaSnm($alturaSnm);
            $ubicacionElemento->setParroquiaId($parroquia);
            $ubicacionElemento->setUsrCreacion($session->get('user'));
            $ubicacionElemento->setFeCreacion(new \DateTime('now'));
            $ubicacionElemento->setIpCreacion($request->getClientIp());
            $em->persist($ubicacionElemento);

            //empresa elemento ubicacion
            $empresaElementoUbica = new InfoEmpresaElementoUbica();
            $empresaElementoUbica->setEmpresaCod($session->get('idEmpresa'));
            $empresaElementoUbica->setElementoId($elementoServidor);
            $empresaElementoUbica->setUbicacionId($ubicacionElemento);
            $empresaElementoUbica->setUsrCreacion($session->get('user'));
            $empresaElementoUbica->setFeCreacion(new \DateTime('now'));
            $empresaElementoUbica->setIpCreacion($request->getClientIp());
            $em->persist($empresaElementoUbica);

            //empresa elemento
            $empresaElemento = new InfoEmpresaElemento();
            $empresaElemento->setElementoId($elementoServidor);
            $empresaElemento->setEmpresaCod($session->get('idEmpresa'));
            $empresaElemento->setEstado("Activo");
            $empresaElemento->setUsrCreacion($session->get('user'));
            $empresaElemento->setIpCreacion($request->getClientIp());
            $empresaElemento->setFeCreacion(new \DateTime('now'));
            $em->persist($empresaElemento);

            $em->flush();
            $em->commit();
        }
        catch(\Exception $e)
        {
            if($em->getConnection()->isTransactionActive())
            {
                $em->rollback();
            }
            $em->close();
            $this->get('session')->getFlashBag()->add('notice', 'Ocurrio un error: ' . $e->getMessage() . '!');
            return $this->redirect($this->generateUrl('elementoservidor_newServidor'));
        }
        
        return $this->redirect($this->generateUrl('elementoservidor_showServidor', array('id' => $elementoServidor->getId())));        
    }
    
    /**
     * Funcion que sirve para cargar el formulario de edicion de servidor
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 19-02-2015
     * @Secure(roles="ROLE_196-4")
     */
    public function editServidorAction($id){
        $request    = $this->get('request');
        $session    = $request->getSession();
        $empresaId  = $session->get('idEmpresa');
        $em         = $this->getDoctrine()->getManager("telconet_infraestructura");

        if (null == $elemento = $em->find('schemaBundle:InfoElemento', $id)) {
            throw new NotFoundHttpException('No existe el elemento -servidor- que se quiere modificar');
        }
        else{
            /* @var $datosElemento InfoServicioTecnico */
            $datosElemento = $this->get('tecnico.InfoServicioTecnico');
            //---------------------------------------------------------------------*/
            $respuestaElemento = $datosElemento->obtenerDatosElemento($id, $empresaId);
            
            $ipElemento         = $respuestaElemento['ipElemento'];
            $ubicacion          = $respuestaElemento['ubicacion'];            
            $cantonJurisdiccion = $em->getRepository('schemaBundle:AdmiCantonJurisdiccion')
                                     ->findOneBy(array( "cantonId" =>$ubicacion->getParroquiaId()->getCantonId()->getId()));
        }

        $formulario =$this->createForm(new InfoElementoServidorType(), $elemento);
        
        return $this->render(   'tecnicoBundle:InfoElementoServidor:edit.html.twig', array(
                                'edit_form'             => $formulario->createView(),
                                'servidor'              => $elemento,
                                'ipElemento'            => $ipElemento,
                                'ubicacion'             => $ubicacion,
                                'cantonJurisdiccion'    => $cantonJurisdiccion)
                            );
    }
    
    /**
     * Funcion que sirve para actualizar la informacion de un elemento Servidor
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 19-02-2015
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 19-09-2018 - Se agrega la validación de las coordenadas de latitud y longitud de acuerdo al país en sesión
     * 
     * @Secure(roles="ROLE_196-5")
     */
    public function updateServidorAction($id){
        $em     = $this->getDoctrine()->getManager('telconet_infraestructura');
        $entity = $em->getRepository('schemaBundle:InfoElemento')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoElemento entity.');
        }
        
        $request    = $this->get('request');
        $session    = $request->getSession();
        $parametros = $request->request->get('telconet_schemabundle_infoelementoservidortype');
        
        $nombreElemento         = $parametros['nombreElemento'];
        $descripcionElemento    = $parametros['descripcionElemento'];
        $modeloElementoId       = $parametros['modeloElementoId'];
        $parroquiaId            = $parametros['parroquiaId'];
        $ipElementoId           = $request->request->get('idIpElemento');
        $ipElemento             = $request->request->get('ipElemento');
        $direccionUbicacion     = $request->request->get('direccionUbicacion');
        $longitudUbicacion      = $request->request->get('longitudUbicacion');
        $latitudUbicacion       = $request->request->get('latitudUbicacion');
        $alturaSnm              = $request->request->get('alturaSnm');
        $idUbicacion            = $request->request->get('idUbicacion');
             
        $em->beginTransaction();
        try
        {
            //verificar nombre repetido
            $objetoRepetido = $em->getRepository('schemaBundle:InfoElemento')
                                 ->findOneBy(array("nombreElemento" => $nombreElemento, "estado" => "Activo"));
            if($objetoRepetido && $objetoRepetido->getId()!=$id)
            {
                throw new \Exception('Ya existe un elemento con ese NOMBRE, favor revisar!');
            }

            //verificar ip repetida
            $objetoIpRepetido = $em->getRepository('schemaBundle:InfoIp')
                                   ->findOneBy(array("ip" => $ipElemento, "estado" => "Activo"));
            if($objetoIpRepetido && $objetoIpRepetido->getId()!=$ipElementoId)
            {
                throw new \Exception('Ya existe un elemento con esa IP, favor revisar!');
            }

            $modeloElemento = $em->find('schemaBundle:AdmiModeloElemento', $modeloElementoId);
            //elemento
            $entity->setNombreElemento($nombreElemento);
            $entity->setDescripcionElemento($descripcionElemento);
            $entity->setModeloElementoId($modeloElemento);
            $entity->setUsrResponsable($session->get('user'));
            $entity->setUsrCreacion($session->get('user'));
            $entity->setFeCreacion(new \DateTime('now'));
            $entity->setIpCreacion($request->getClientIp());   
            $em->persist($entity);

            //ip elemento
            $ipElementoObj = $em->getRepository('schemaBundle:InfoIp')->find($ipElementoId);
            $ipElementoObj->setIp($ipElemento);
            $ipElementoObj->setUsrCreacion($session->get('user'));
            $ipElementoObj->setFeCreacion(new \DateTime('now'));
            $ipElementoObj->setIpCreacion($request->getClientIp());
            $em->persist($ipElementoObj);

            //historial elemento
            $historialElemento = new InfoHistorialElemento();
            $historialElemento->setElementoId($entity);
            $historialElemento->setEstadoElemento("Modificado");
            $historialElemento->setObservacion("Se modifico un Servidor");
            $historialElemento->setUsrCreacion($session->get('user'));
            $historialElemento->setFeCreacion(new \DateTime('now'));
            $historialElemento->setIpCreacion($request->getClientIp());
            $em->persist($historialElemento);
            
            $serviceInfoElemento        = $this->get("tecnico.InfoElemento");
            $arrayRespuestaCoordenadas  = $serviceInfoElemento->validarLimitesCoordenadasElemento(array("latitudElemento"   => $latitudUbicacion,
                                                                                                        "longitudElemento"  => $longitudUbicacion,
                                                                                                        "msjTipoElemento"   => "del servidor "));
            if($arrayRespuestaCoordenadas["status"] === "ERROR")
            {
                throw new \Exception($arrayRespuestaCoordenadas['mensaje']);
            }
            //info ubicacion
            $parroquia = $em->find('schemaBundle:AdmiParroquia', $parroquiaId);
            $ubicacionElemento = $em->find('schemaBundle:InfoUbicacion', $idUbicacion);
            $ubicacionElemento->setLatitudUbicacion($latitudUbicacion);
            $ubicacionElemento->setLongitudUbicacion($longitudUbicacion);
            $ubicacionElemento->setDireccionUbicacion($direccionUbicacion);
            $ubicacionElemento->setAlturaSnm($alturaSnm);
            $ubicacionElemento->setParroquiaId($parroquia);
            $ubicacionElemento->setUsrCreacion($session->get('user'));
            $ubicacionElemento->setFeCreacion(new \DateTime('now'));
            $ubicacionElemento->setIpCreacion($request->getClientIp());
            $em->persist($ubicacionElemento);

            $em->flush();
            $em->commit();
        }
        catch(\Exception $e)
        {
            if($em->getConnection()->isTransactionActive())
            {
                $em->rollback();
            }
            $em->close();
            $this->get('session')->getFlashBag()->add('notice', 'Ocurrio un error: ' . $e->getMessage() . '!');
            return $this->redirect($this->generateUrl('elementoservidor_editServidor', array('id' => $entity->getId())));
        }
        
        
        return $this->redirect($this->generateUrl('elementoservidor_showServidor', array('id' => $entity->getId())));
    }
    
    /**
     * Funcion que sirve para cambiar de estado a Eliminado a un elemento servidor mediante html
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 19-02-2015
     * @Secure(roles="ROLE_196-8")
     */
    public function deleteServidorAction($id){
        $request    = $this->get('request');
        $session    = $request->getSession();        
        $em         = $this->getDoctrine()->getManager('telconet_infraestructura');
        $entity     = $em->getRepository('schemaBundle:InfoElemento')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoElemento entity.');
        }
        
        $em->getConnection()->beginTransaction();

        try
        {
            //elemento
            $entity->setEstado("Eliminado");
            $em->persist($entity);

            //ip
            $ipElementoObj = $em->getRepository('schemaBundle:InfoIp')->findOneBy(array("elementoId"=>$entity->getId()));
            $ipElementoObj->setEstado("Eliminado");
            $em->persist($ipElementoObj);

            //historial elemento
            $historialElemento = new InfoHistorialElemento();
            $historialElemento->setElementoId($entity);
            $historialElemento->setEstadoElemento("Eliminado");
            $historialElemento->setObservacion("Se elimino un Servidor");
            $historialElemento->setUsrCreacion($session->get('user'));
            $historialElemento->setFeCreacion(new \DateTime('now'));
            $historialElemento->setIpCreacion($request->getClientIp());
            $em->persist($historialElemento);

            //empresa elemento
            $empresaElemento = $em->getRepository('schemaBundle:InfoEmpresaElemento')->findOneBy(array("elementoId"=>$entity->getId()));
            $empresaElemento->setEstado("Activo");
            $em->persist($empresaElemento);

            $em->flush();
        }
        catch(\Exception $e)
        {
            if($em->getConnection()->isTransactionActive())
            {
                $em->getConnection()->rollback();
            }

            $this->get('session')->getFlashBag()->add('notice', 'Ocurrio un error: ' . $e->getMessage() . '!');
            return $this->redirect($this->generateUrl('elementoservidor'));
        }
        $em->getConnection()->commit();

        return $this->redirect($this->generateUrl('elementoservidor'));
    }
    
    /**
     * Funcion que sirve para cambiar de estado a Eliminado a uno o varios elementos servidor mediante ajax
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 19-02-2015
     * @Secure(roles="ROLE_196-9")
     */
    public function deleteAjaxServidorAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $request    = $this->get('request');
        $session    = $request->getSession();
        
        $parametro = $request->get('param');
        
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");        
        $em->getConnection()->beginTransaction();        
        try
        {
            $array_valor = explode("|",$parametro);
            foreach($array_valor as $id):
                if (null == $entity = $em->find('schemaBundle:InfoElemento', $id)) 
                {
                    $respuesta->setContent("No existe la entidad");
                }
                else
                {
                    //elemento
                    $entity->setEstado("Eliminado");
                    $em->persist($entity);

                    //ip
                    $ipElementoObj = $em->getRepository('schemaBundle:InfoIp')->findOneBy(array("elementoId"=>$entity->getId()));
                    $ipElementoObj->setEstado("Eliminado");
                    $em->persist($ipElementoObj);

                    //historial elemento
                    $historialElemento = new InfoHistorialElemento();
                    $historialElemento->setElementoId($entity);
                    $historialElemento->setEstadoElemento("Eliminado");
                    $historialElemento->setObservacion("Se elimino un Servidor");
                    $historialElemento->setUsrCreacion($session->get('user'));
                    $historialElemento->setFeCreacion(new \DateTime('now'));
                    $historialElemento->setIpCreacion($request->getClientIp());
                    $em->persist($historialElemento);

                    //empresa elemento
                    $empresaElemento = $em->getRepository('schemaBundle:InfoEmpresaElemento')->findOneBy(array("elementoId"=>$entity->getId()));
                    $empresaElemento->setEstado("Activo");
                    $em->persist($empresaElemento);

                    $em->flush();
                    $respuesta->setContent("Se elimino la entidad");
                }
            endforeach;
        }
        catch(\Exception $e)
        {
            if($em->getConnection()->isTransactionActive())
            {
                $em->getConnection()->rollback();
            }

            $this->get('session')->getFlashBag()->add('notice', 'Ocurrio un error: ' . $e->getMessage() . '!');
            return $this->redirect($this->generateUrl('elementoservidor'));
        }
        $em->getConnection()->commit();
        
        return $respuesta;
    }
    
    /**
     * Funcion que sirve para ver informacion de un elemento servidor
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 19-02-2015
     * @Secure(roles="ROLE_196-6")
     */
    public function showServidorAction($id){
        $request    = $this->get('request');
        $session    = $request->getSession();
        $empresaId  = $session->get('idEmpresa');
        
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");

        if (null == $elemento = $em->find('schemaBundle:InfoElemento', $id)) 
        {
            throw new NotFoundHttpException('No existe el Elemento que se quiere mostrar');
        }
        else
        {
            /* @var $datosElemento InfoServicioTecnico */
            $datosElemento = $this->get('tecnico.InfoServicioTecnico');
            //---------------------------------------------------------------------*/
            $respuestaElemento = $datosElemento->obtenerDatosElemento($id, $empresaId);
            
            $ipElemento         = $respuestaElemento['ipElemento'];
            $arrayHistorial     = $respuestaElemento['historialElemento'];
            $ubicacion          = $respuestaElemento['ubicacion'];
            $jurisdiccion       = $respuestaElemento['jurisdiccion'];
        }

        return $this->render('tecnicoBundle:InfoElementoServidor:show.html.twig', array(
            'elemento'              => $elemento,
            'ipElemento'            => $ipElemento,
            'historialElemento'     => $arrayHistorial,
            'ubicacion'             => $ubicacion,
            'jurisdiccion'          => $jurisdiccion
        ));
    }
    
    /**
     * Funcion que sirve para realizar la busqueda de elementos servidores
     * por medio de un filtro
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 19-02-2015
     * @Secure(roles="ROLE_196-46")
     */
    public function getEncontradosServidorAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');
        $tipoElemento = $em->getRepository('schemaBundle:AdmiTipoElemento')
                           ->findOneBy(array( "nombreTipoElemento" =>"SERVIDOR","estado"=>"Activo"));
        
        $peticion = $this->get('request');
        
        $nombreElemento     = $peticion->query->get('nombreElemento');
        $ipElemento         = $peticion->query->get('ipElemento');
        $modeloElemento     = $peticion->query->get('modeloElemento');
        $marcaElemento      = $peticion->query->get('marcaElemento');
        $canton             = $peticion->query->get('canton');
        $jurisdiccion       = $peticion->query->get('jurisdiccion');
        $estado             = $peticion->query->get('estado');
        $start              = $peticion->query->get('start');
        $limit              = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:InfoElemento')
            ->generarJsonServidores($nombreElemento,$ipElemento,$modeloElemento,$marcaElemento,$tipoElemento->getId(),
                                    $canton,$jurisdiccion,$estado,$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /**
     * Funcion que sirve para cargar los datos que se van a usar para la edicion de un 
     * elemento servidor
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 19-02-2015
     */
    public function cargarDatosServidorAction()
    {
        $respuesta  = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $peticion   = $this->get('request');
        $session    = $peticion->getSession();
        $empresaId  = $session->get('idEmpresa');
        $idServidor = $peticion->get('idServidor');
        
        /* @var $datosElemento InfoServicioTecnico */
        $datosElemento = $this->get('tecnico.InfoServicioTecnico');
        //---------------------------------------------------------------------*/
        $respuestaElemento = $datosElemento->obtenerDatosElemento($idServidor, $empresaId);

        $ubicacion          = $respuestaElemento['ubicacion'];
        $jurisdiccion       = $respuestaElemento['jurisdiccion'];
        
        $arr_encontrados[]=array('idElemento'           => $idServidor,
                                 'idCanton'             => $ubicacion->getParroquiaId()->getCantonId()->getId(),
                                 'nombreCanton'         => trim($ubicacion->getParroquiaId()->getCantonId()->getNombreCanton()),
                                 'idJurisdiccion'       => $jurisdiccion->getId(),
                                 'nombreJurisdiccion'   => trim($jurisdiccion->getNombreJurisdiccion()),
                                 'idParroquia'          => $ubicacion->getParroquiaId()->getId(),
                                 'nombreParroquia'      => trim($ubicacion->getParroquiaId()->getNombreParroquia()));
            

        $data=json_encode($arr_encontrados);
        $objJson= '{"total":"1","encontrados":'.$data.'}';

        $respuesta->setContent($objJson);

        return $respuesta;
    }

}