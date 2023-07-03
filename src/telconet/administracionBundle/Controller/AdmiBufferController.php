<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiBuffer;
use telconet\schemaBundle\Form\AdmiBufferType;
use telconet\schemaBundle\Entity\InfoBufferHilo;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Symfony\Component\HttpFoundation\Response;

class AdmiBufferController extends Controller
{ 
    /**
     * Funcion que sirve para cargar los buffers de manera inicial
     * 
     * @author creado Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24-02-2015
     * @Secure(roles="ROLE_81-1")
     */
    public function indexAction()
    {
        if(true === $this->get('security.context')->isGranted('ROLE_81-1'))
        {
            $rolesPermitidos[] = 'ROLE_81-1'; //index
        }
        if(true === $this->get('security.context')->isGranted('ROLE_81-46'))
        {
            $rolesPermitidos[] = 'ROLE_81-46'; //encontrados
        }
        if(true === $this->get('security.context')->isGranted('ROLE_81-6'))
        {
            $rolesPermitidos[] = 'ROLE_81-6'; //show
        }
        if(true === $this->get('security.context')->isGranted('ROLE_81-2'))
        {
            $rolesPermitidos[] = 'ROLE_81-2'; //new
        }
        if(true === $this->get('security.context')->isGranted('ROLE_81-3'))
        {
            $rolesPermitidos[] = 'ROLE_81-3'; //create
        }
        if(true === $this->get('security.context')->isGranted('ROLE_81-4'))
        {
            $rolesPermitidos[] = 'ROLE_81-4'; //edit
        }
        if(true === $this->get('security.context')->isGranted('ROLE_81-5'))
        {
            $rolesPermitidos[] = 'ROLE_81-5'; //update
        }
        if(true === $this->get('security.context')->isGranted('ROLE_81-8'))
        {
            $rolesPermitidos[] = 'ROLE_81-8'; //delete
        }
        if(true === $this->get('security.context')->isGranted('ROLE_81-9'))
        {
            $rolesPermitidos[] = 'ROLE_81-9'; //delete ajax
        }

        return $this->render('administracionBundle:AdmiBuffer:index.html.twig', array(
                'rolesPermitidos' => $rolesPermitidos
        ));
    }
    
    /**
     * Funcion que sirve para mostrar los datos especificios de un buffer
     * 
     * @author creado Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24-02-2015
     * @Secure(roles="ROLE_81-6")
     */
    public function showAction($id){
        $peticion = $this->get('request');
        
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");

        if (null == $buffer = $em->find('schemaBundle:AdmiBuffer', $id)) {
            throw new NotFoundHttpException('No existe el Buffer que se quiere mostrar');
        }

        return $this->render('administracionBundle:AdmiBuffer:show.html.twig', array(
            'buffer'=> $buffer,
            'flag'  => $peticion->get('flag')
        ));
    }
    
    /**
     * Funcion que sirve para cargar el twig de nuevo buffer
     * 
     * @author creado Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24-02-2015
     * @Secure(roles="ROLE_81-2")
     */
    public function newAction(){
        $entity = new AdmiBuffer();
        $form   = $this->createForm(new AdmiBufferType(), $entity);

        return $this->render('administracionBundle:AdmiBuffer:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }
    
    /**
     * Funcion que sirve para ingresar los datos de un buffer y
     * la relacion buffer - hilos a la base de datos
     * 
     * @author creado Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24-02-2015
     * @Secure(roles="ROLE_81-3")
     */
    public function createAction(){
        $request = $this->get('request');
        $session = $request->getSession();
        $em      = $this->get('doctrine')->getManager('telconet_infraestructura');
        $entity  = new AdmiBuffer();
        $form    = $this->createForm(new AdmiBufferType(), $entity);
        
        $entity->setEstado('Activo');
        $entity->setFeCreacion(new \DateTime('now'));
        $entity->setUsrCreacion($session->get('user'));
        $entity->setFeUltMod(new \DateTime('now'));
        $entity->setUsrUltMod($session->get('user'));
        
        $jsonBufferHilo  = json_decode($request->get('bufferHilo'));
        $arrayBufferHilo = $jsonBufferHilo->bufferHilo;
        $em->getConnection()->beginTransaction();
        try
        {
            $form->bind($request);
        
            if ($form->isValid()) 
            {
                //grabar buffer
                $em->persist($entity);
                $em->flush();
                
                //grabar relacion buffer - hilo
                foreach($arrayBufferHilo as $hilos)
                {
                    $idHilo = $hilos->hiloId;
                    $hilo = $em->find('schemaBundle:AdmiHilo', $idHilo);
                    
                    $bufferHilo = new InfoBufferHilo();
                    $bufferHilo->setBufferId($entity);
                    $bufferHilo->setHiloId($hilo);
                    $bufferHilo->setEmpresaCod($session->get('idEmpresa'));
                    $bufferHilo->setEstado("Activo");
                    $bufferHilo->setUsrCreacion($session->get('user'));
                    $bufferHilo->setFeCreacion(new \DateTime('now'));
                    $bufferHilo->setIpCreacion($request->getClientIp());
                    $em->persist($bufferHilo);
                    $em->flush();
                }
                
            }
        }
        catch(\Exception $e)
        {
            if($em->getConnection()->isTransactionActive())
            {
                $em->getConnection()->rollback();
            }
            $this->get('session')->getFlashBag()->add('notice', 'Ocurrio un error: ' . $e->getMessage() . '!');
            return $this->redirect($this->generateUrl('admibuffer_new'));
        }

        $em->getConnection()->commit();
        return $this->redirect($this->generateUrl('admibuffer_show', array('id' => $entity->getId())));
    }
    
    /**
     * Funcion que sirve para cargar el twig de edicion de un buffer
     * 
     * @author creado Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24-02-2015
     * @param $id   int
     * @Secure(roles="ROLE_81-4")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");

        if(null == $buffer = $em->find('schemaBundle:AdmiBuffer', $id))
        {
            throw new NotFoundHttpException('No existe el Buffer que se quiere modificar');
        }

        $formulario = $this->createForm(new AdmiBufferType(), $buffer);

        return $this->render('administracionBundle:AdmiBuffer:edit.html.twig', array(
                             'edit_form' => $formulario->createView(),
                             'buffer'    => $buffer));
    }

    /**
     * Funcion que sirve para actualizar datos de un buffer y las
     * relaciones de buffer - hilos
     * 
     * @author creado Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24-02-2015
     * @param   $id     int
     * @Secure(roles="ROLE_81-5")
     */
    public function updateAction($id)
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        $em      = $this->getDoctrine()->getManager('telconet_infraestructura');
        $entity  = $em->getRepository('schemaBundle:AdmiBuffer')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se encontro Buffer seleccionado.');
        }

        $editForm        = $this->createForm(new AdmiBufferType(), $entity);        
        $jsonBufferHilo  = json_decode($request->get('bufferHilo'));
        $arrayBufferHilo = $jsonBufferHilo->bufferHilo;
        
        $em->getConnection()->beginTransaction();
        try
        {
            $editForm->bind($request);

            if ($editForm->isValid()) {
                $entity->setFeUltMod(new \DateTime('now'));
                $entity->setUsrUltMod($session->get('user'));
                $em->persist($entity);
                $em->flush();
                
                //grabar relacion buffer - hilo
                foreach($arrayBufferHilo as $hilos)
                {
                    $bufferHiloId = $hilos->bufferHiloId;
                    $idHilo       = $hilos->hiloId;
                    $hilo         = $em->find('schemaBundle:AdmiHilo', $idHilo);
                    
                    if($bufferHiloId)
                    {
                        $bufferHiloObj = $em->getRepository('schemaBundle:InfoBufferHilo')->find($bufferHiloId);
                        $bufferHiloObj->setBufferId($entity);
                        $bufferHiloObj->setHiloId($hilo);
                        $bufferHiloObj->setEmpresaCod($session->get('idEmpresa'));
                        $bufferHiloObj->setEstado("Activo");
                        $bufferHiloObj->setUsrCreacion($session->get('user'));
                        $bufferHiloObj->setFeCreacion(new \DateTime('now'));
                        $bufferHiloObj->setIpCreacion($request->getClientIp());
                        $em->persist($bufferHiloObj);
                    }
                    else
                    {
                        $bufferHilo = new InfoBufferHilo();
                        $bufferHilo->setBufferId($entity);
                        $bufferHilo->setHiloId($hilo);
                        $bufferHilo->setEmpresaCod($session->get('idEmpresa'));
                        $bufferHilo->setEstado("Activo");
                        $bufferHilo->setUsrCreacion($session->get('user'));
                        $bufferHilo->setFeCreacion(new \DateTime('now'));
                        $bufferHilo->setIpCreacion($request->getClientIp());
                        $em->persist($bufferHilo);
                    }                    
                    
                    $em->flush();
                }
            }
        }
        catch(\Exception $e)
        {
            if($em->getConnection()->isTransactionActive())
            {
                $em->getConnection()->rollback();
            }
            $this->get('session')->getFlashBag()->add('notice', 'Ocurrio un error: ' . $e->getMessage() . '!');
            return $this->redirect($this->generateUrl('admibuffer_edit', array('id' => $entity->getId())));
        }

        $em->getConnection()->commit();
        return $this->redirect($this->generateUrl('admibuffer_show', array('id' => $entity->getId())));
    }
    
    /**
     * Funcion que sirve para cambiar de estado a Eliminado de un buffer mediante html
     * 
     * @author creado Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24-02-2015
     * @param   $id     int
     * @Secure(roles="ROLE_81-8")
     */
    public function deleteAction($id)
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        $em = $this->getDoctrine()->getManager('telconet_infraestructura');
        $entity = $em->getRepository('schemaBundle:AdmiBuffer')->find($id);

        if(!$entity)
        {
            throw $this->createNotFoundException('No se encontro Buffer seleccionado.');
        }
        $estado = 'Eliminado';
        $entity->setEstado($estado);
        $entity->setFeUltMod(new \DateTime('now'));
        $entity->setUsrUltMod($session->get('user'));
        $em->getConnection()->beginTransaction();
        try
        {
            //actualizar estado del buffer
            $em->persist($entity);
            $em->flush();

            //actualizar estado del buffer - hilo
            $bufferHiloArray = $em->getRepository('schemaBundle:InfoBufferHilo')->findBy(array('bufferId' => $id, 'estado' => 'Activo'));
            foreach($bufferHiloArray as $bufferHiloObj)
            {
                $bufferHiloObj->setEstado("Eliminado");
                $bufferHiloObj->setUsrCreacion($session->get('user'));
                $bufferHiloObj->setFeCreacion(new \DateTime('now'));
                $bufferHiloObj->setIpCreacion($request->getClientIp());
                $em->persist($bufferHiloObj);
            }
        }
        catch(\Exception $e)
        {
            if($em->getConnection()->isTransactionActive())
            {
                $em->getConnection()->rollback();
            }
            $this->get('session')->getFlashBag()->add('notice', 'Ocurrio un error: ' . $e->getMessage() . '!');
            return $this->redirect($this->generateUrl('admibuffer'));
        }
        $em->getConnection()->commit();
        return $this->redirect($this->generateUrl('admibuffer'));
    }

    /**
     * Funcion que sirve para cambiar de estado a Eliminado de uno o mas buffers mediante ajax
     * 
     * @author creado Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24-02-2015
     * @Secure(roles="ROLE_81-9")
     */
    public function deleteAjaxAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $request    = $this->getRequest();
        $session    = $request->getSession();        
        $parametro  = $request->get('param');        
        $em         = $this->getDoctrine()->getManager("telconet_infraestructura");
        
        $array_valor = explode("|",$parametro);
        $em->getConnection()->beginTransaction();
        try
        {
            foreach($array_valor as $id):
                if(null == $entity = $em->find('schemaBundle:AdmiBuffer', $id))
                {
                    $respuesta->setContent("No existe la entidad");
                }
                else
                {
                    if(strtolower($entity->getEstado()) != "eliminado")
                    {
                        //actualizar estado buffer
                        $entity->setEstado("Eliminado");
                        $entity->setFeUltMod(new \DateTime('now'));
                        $entity->setUsrUltMod($session->get('user'));
                        $em->persist($entity);
                        $em->flush();

                        //actualizar estado del buffer - hilo
                        $bufferHiloArray = $em->getRepository('schemaBundle:InfoBufferHilo')->findBy(array('bufferId' => $id, 'estado' => 'Activo'));
                        foreach($bufferHiloArray as $bufferHiloObj)
                        {
                            $bufferHiloObj->setEstado("Eliminado");
                            $bufferHiloObj->setUsrCreacion($session->get('user'));
                            $bufferHiloObj->setFeCreacion(new \DateTime('now'));
                            $bufferHiloObj->setIpCreacion($request->getClientIp());
                            $em->persist($bufferHiloObj);
                        }
                    }
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
            return $this->redirect($this->generateUrl('admibuffer'));
        }
        $em->getConnection()->commit();
        $respuesta->setContent("Se elimino la entidad");
        return $respuesta;
    }
    
    /**
     * Funcion que sirve para obtener los buffers que se encuentran
     * registrados en la base de datos
     * 
     * @author creado Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24-02-2015
     * @Secure(roles="ROLE_81-46")
     */
    public function getEncontradosAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $colorBuffer  = $peticion->query->get('colorBuffer');
        $numeroBuffer = $peticion->query->get('numeroBuffer');
        $estado       = $peticion->query->get('estado');        
        $start        = $peticion->query->get('start');
        $limit        = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiBuffer')
            ->generarJsonBufferes($numeroBuffer,$colorBuffer,$estado,$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /**
     * Funcion que sirve para obtener la relacion de buffer - hilos
     * 
     * @author creado Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24-02-2015
     * @param   $id     int
     */
    public function getBuffersHilosAction($id)
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion       = $this->get('request');
        $session        = $peticion->getSession();
        $empresaId      = $session->get('idEmpresa');
        $estado         = $peticion->query->get('estado');        
        $start          = $peticion->query->get('start');
        $limit          = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiBuffer')
            ->generarJsonBuffersHilos($id,$empresaId,$estado,$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /**
     * Funcion que sirve para obtener la clase tipo medio
     * por el id de un buffer
     * 
     * @author creado Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24-02-2015
     * @param   $id     int
     */
    public function getClaseTipoMedioPorBufferAction($id)
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion       = $this->get('request');
        $session        = $peticion->getSession();
        $empresaId      = $session->get('idEmpresa');
        $estado         = $peticion->get('estado');        
        $start          = $peticion->query->get('start');
        $limit          = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiBuffer')
            ->generarJsonClaseTipoMedioPorBuffer($id,$empresaId,$estado,$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }


    public function getBufferesAction(){
       $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiBuffer')
            ->generarJsonBufferes("","","Activo",$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /**
     * Funcion que sirve para obtener los buffer de acuerdo a un tipoMedio determinado
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 04-03-2015
     *
     * @return json
     */
    public function ajaxGetBuffersPorTipoMedioAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $peticion           = $this->get('request');
        $session            = $peticion->getSession();
        $empresaId          = $session->get('idEmpresa');
        $estadoBufferHilo   = $peticion->get('estadoBufferHilo');
        $estado             = $peticion->get('estado');
        $tipoMedioId        = $peticion->get('tipoMedioId');

        $objJson = $this->getDoctrine()
                        ->getManager("telconet_infraestructura")
                        ->getRepository('schemaBundle:AdmiBuffer')
                        ->generarJsonBufferPorTipoMedio($tipoMedioId, $empresaId, $estado, $estadoBufferHilo);
        $respuesta->setContent($objJson);

        return $respuesta;
    }

}