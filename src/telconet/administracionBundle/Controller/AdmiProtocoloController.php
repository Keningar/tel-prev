<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiProtocolo;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Response;

class AdmiProtocoloController extends Controller
{ 
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');

        $entities = $em->getRepository('schemaBundle:AdmiProtocolo')->findAll();

        return $this->render('administracionBundle:AdmiProtocolo:index.html.twig', array(
            'entities' => $entities
        ));
    }
    
    public function newAction(){
        $entity = new AdmiProtocolo();
        $form   = $this->createForm(new AdmiProtocoloType(), $entity);

        return $this->render('administracionBundle:AdmiProtocolo:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }
    
    public function createAction(){
        $request = $this->get('request');
        $session  = $request->getSession();
        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $entity  = new AdmiProducto();
        $form    = $this->createForm(new AdmiProtocoloType(), $entity);
        
        $entity->setEstado('Activo');
        $entity->setFeCreacion(new \DateTime('now'));
        $entity->setUsrCreacion($session->get('user'));
        $entity->setFeUltMod(new \DateTime('now'));
        $entity->setUsrUltMod($session->get('user'));
        
//        $form->setData($entity);
        
        $form->bind($request);
        
        if ($form->isValid()) {
            $em->getConnection()->beginTransaction();
            $em->persist($entity);
            $em->flush();
            $em->getConnection()->commit();
            
            return $this->redirect($this->generateUrl('admiprotocolo_show', array('id' => $entity->getId())));
        }
        
        return $this->render('administracionBundle:AdmiProtocolo:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
        
    }
    
    public function showAction($id){
        $peticion = $this->get('request');
        
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");

        if (null == $protocolo = $em->find('schemaBundle:AdmiProtocolo', $id)) {
            throw new NotFoundHttpException('No existe el Protocolo que se quiere mostrar');
        }

        return $this->render('administracionBundle:AdmiProtocolo:show.html.twig', array(
            'protocolo'   => $protocolo,
            'flag' =>$peticion->get('flag')
        ));
    }
    
    public function getEncontradosAction(){
       $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
		
        $protocolo = $peticion->query->get('nombreProtocolo');
        $estado = $peticion->query->get('estado');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiProtocolo')
            ->generarJsonProtocolosEncontrados($protocolo,$estado,$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    public function getProtocolosAction(){
       $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiProtocolo')
            ->generarJsonProtocolos("Activo",$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
}