<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiTipoMedio;
use telconet\schemaBundle\Form\AdmiTipoMedioType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Response;

class AdmiTipoMedioController extends Controller
{ 
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');

        $entities = $em->getRepository('schemaBundle:AdmiTipoMedio')->findAll();

        return $this->render('administracionBundle:AdmiTipoMedio:index.html.twig', array(
            'entities' => $entities
        ));
    }
    
    public function showAction($id)
    {
        $peticion = $this->get('request');
        
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");

        if (null == $tipoMedio = $em->find('schemaBundle:AdmiTipoMedio', $id)) {
            throw new NotFoundHttpException('No existe el Tipo de Medio que se quiere mostrar');
        }

        return $this->render('administracionBundle:AdmiTipoMedio:show.html.twig', array(
            'tipomedio'   => $tipoMedio,
            'flag' =>$peticion->get('flag')
        ));
    }
    
    public function newAction()
    {
        $entity = new AdmiTipoMedio();
        $form   = $this->createForm(new AdmiTipoMedioType(), $entity);

        return $this->render('administracionBundle:AdmiTipoMedio:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }
    
    /**
    * @author John Vera <javera@telconet.ec 
    * @version 1.1 17-10-2016 Se setea el user de la session
    **/    
    public function createAction()
    {
        $request = $this->get('request');
        $objSession  = $request->getSession();
        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $entity  = new AdmiTipoMedio();
        $form    = $this->createForm(new AdmiTipoMedioType(), $entity);
        
        $entity->setEstado('Activo');
        $entity->setFeCreacion(new \DateTime('now'));
        $entity->setUsrCreacion($objSession->get('user'));
        
//        $form->setData($entity);
        
        $form->bind($request);
        
        if ($form->isValid()) {
            $em->getConnection()->beginTransaction();
            $em->persist($entity);
            $em->flush();
            $em->getConnection()->commit();
            
            return $this->redirect($this->generateUrl('admitipomedio_show', array('id' => $entity->getId())));
        }
        
//        die("murio");
        return $this->render('administracionBundle:AdmiTipoMedio:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
        
    }
    
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");

        if (null == $tipo = $em->find('schemaBundle:AdmiTipoMedio', $id)) {
            throw new NotFoundHttpException('No existe el Tipo de Medio que se quiere modificar');
        }

        $formulario =$this->createForm(new AdmiTipoMedioType(), $tipo);
//        $formulario->setData($tipo);

        return $this->render('administracionBundle:AdmiTipoMedio:edit.html.twig', array(
                                    'edit_form' => $formulario->createView(),
                                    'tipo'   => $tipo));
    }
    
    /**
    * @author John Vera <javera@telconet.ec 
    * @version 1.1 17-10-2016 Se setea el user de la session
    **/    
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');

        $entity = $em->getRepository('schemaBundle:AdmiTipoMedio')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AdmiTipoMedio entity.');
        }

        $editForm   = $this->createForm(new AdmiTipoMedioType(), $entity);

        $request = $this->getRequest();
        $objSession  = $request->getSession();

        $editForm->bind($request);

        if ($editForm->isValid()) {
            /*Para que guarde la fecha y el usuario correspondiente*/
            $entity->setFeUltMod(new \DateTime('now'));
            //$entity->setIdUsuarioModificacion($user->getUsername());
            $entity->setUsrUltMod($objSession->get('user'));
            /*Para que guarde la fecha y el usuario correspondiente*/
			
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admitipomedio_show', array('id' => $id)));
        }

        return $this->render('administracionBundle:AdmiTipoMedio:edit.html.twig',array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView()
        ));
    }
    
    /**
    * @author John Vera <javera@telconet.ec 
    * @version 1.1 17-10-2016 Se setea el user de la session
    **/    
    public function deleteAction($id){
//        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();
        $objSession  = $request->getSession();

//        $form->bind($request);
		//$user = $this->get('security.context')->getToken()->getUser();
//        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager('telconet_infraestructura');
            $entity = $em->getRepository('schemaBundle:AdmiTipoMedio')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AdmiTipoMedio entity.');
            }
            $estado = 'Eliminado';
            $entity->setEstado($estado);
            /*Para que guarde la fecha y el usuario correspondiente*/
            $entity->setFeUltMod(new \DateTime('now'));
            //$entity->setIdUsuarioModificacion($user->getUsername());
            $entity->setUsrUltMod($objSession->get('user'));
            /*Para que guarde la fecha y el usuario correspondiente*/
			
			$em->persist($entity);	
            //$em->remove($entity);
            $em->flush();
//        }

        return $this->redirect($this->generateUrl('admitipomedio'));
    }


    /**
    * @author John Vera <javera@telconet.ec 
    * @version 1.1 17-10-2016 Se setea el user de la session
    **/    
    public function deleteAjaxAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $peticion = $this->get('request');
        
        $parametro = $peticion->get('param');
        
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");
        
        $array_valor = explode("|",$parametro);
        foreach($array_valor as $id):
            if (null == $entity = $em->find('schemaBundle:AdmiTipoMedio', $id)) {
                $respuesta->setContent("No existe la entidad");
            }
            else{
				if(strtolower($entity->getEstado()) != "eliminado")
				{
					$entity->setEstado("Eliminado");
					$entity->setFeUltMod(new \DateTime('now'));
					$entity->setUsrUltMod($peticion->getSession()->get('user'));
					$em->persist($entity);
					$em->flush();
                }
				
                $respuesta->setContent("Se elimino la entidad");
            }
        endforeach;
        //        $respuesta->setContent($id);
        
        return $respuesta;
    }
    
    public function getEncontradosAction(){
       $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
		$queryNombre = $peticion->query->get('query') ? $peticion->query->get('query') : "";
        $nombre = ($queryNombre != '' ? $queryNombre : $peticion->query->get('nombre'));
        $codigo = $peticion->query->get('codigo');
        $estado = $peticion->query->get('estado');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiTipoMedio')
            ->generarJsonTiposMedios($codigo,$nombre,$estado,$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    public function getTiposMediosAction(){
       $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiTipoMedio')
            ->generarJsonTiposMedios("","","Activo",$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
}