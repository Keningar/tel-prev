<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiProveedorMedidor;
use telconet\schemaBundle\Form\AdmiProveedorMedidorType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Response;

class AdmiProveedorMedidorController extends Controller
{ 
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');

        $entities = $em->getRepository('schemaBundle:AdmiProveedorMedidor')->findAll();

        return $this->render('administracionBundle:AdmiProveedorMedidor:index.html.twig', array(
            'entities' => $entities
        ));
    }
    
    public function showAction($id)
    {
        $peticion = $this->get('request');
        
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");

        if (null == $proveedorMedidor = $em->find('schemaBundle:AdmiProveedorMedidor', $id)) {
            throw new NotFoundHttpException('No existe el Conector que se quiere mostrar');
        }

        return $this->render('administracionBundle:AdmiProveedorMedidor:show.html.twig', array(
            'proveedorMedidor'   => $proveedorMedidor,
            'flag' =>$peticion->get('flag')
        ));
    }
    
    public function newAction()
    {
        $entity = new AdmiProveedorMedidor();
        $form   = $this->createForm(new AdmiProveedorMedidorType(), $entity);

        return $this->render('administracionBundle:AdmiProveedorMedidor:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }
    /**
    * @author John Vera <javera@telconet.ec 
    * @version 1.1 17-10-2016 Se setea el user de la session
    * 
    **/
    public function createAction()
    {
        $request = $this->get('request');
        $objSession  = $request->getSession();
        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $entity  = new AdmiProveedorMedidor();
        $form    = $this->createForm(new AdmiProveedorMedidorType(), $entity);
        
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
            
            return $this->redirect($this->generateUrl('admiproveedormedidor_show', array('id' => $entity->getId())));
        }
        
//        die("murio");
        return $this->render('administracionBundle:AdmiProveedorMedidor:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
        
    }
    
    /**
    * @author John Vera <javera@telconet.ec 
    * @version 1.1 17-10-2016 Se corrige para que el twig no se caiga y se cambia el nombre del nombre de la variable que va dentro del arreglo 
    *                         para que coincida con la variable que se encuentra en el twig
    **/    
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");

        if (null == $conector = $em->find('schemaBundle:AdmiProveedorMedidor', $id)) {
            throw new NotFoundHttpException('No existe el Conector que se quiere modificar');
        }

        $formulario =$this->createForm(new AdmiProveedorMedidorType(), $conector);
//        $formulario->setData($conector);

        return $this->render('administracionBundle:AdmiProveedorMedidor:edit.html.twig', array(
                                    'edit_form' => $formulario->createView(),
                                    'proveedorMedidor'   => $conector));
    }

    /**
    * @author John Vera <javera@telconet.ec 
    * @version 1.1 17-10-2016 Se setea el user de la session
    **/
    
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');

        $entity = $em->getRepository('schemaBundle:AdmiProveedorMedidor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AdmiProveedorMedidor entity.');
        }

        $editForm   = $this->createForm(new AdmiProveedorMedidorType(), $entity);

        $request = $this->getRequest();
        $objSession  = $request->getSession();

        $editForm->bind($request);

        if ($editForm->isValid()) {
            /*Para que guarde la fecha y el usuario correspondiente*/
            $entity->setFeUltMod(new \DateTime('now'));
            //$entity->setIdUsuarioModificacion($user->getUsername());
            $entity->setUsrUltMod($objSession->get('user'));
            /*Para que guarde la fecha y el usuario correspondiente*/
            $entity->setEstado('Modificado');
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admiproveedormedidor_show', array('id' => $id)));
        }

        return $this->render('administracionBundle:AdmiProveedorMedidor:edit.html.twig',array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView()
        ));
    }
    /**
    * 
    * @author John Vera <javera@telconet.ec 
    * @version 1.1 17-10-2016 Se setea el user de la session
    * 
    **/
    public function deleteAction($id){
//        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();
        $objSession  = $request->getSession();

//        $form->bind($request);
		//$user = $this->get('security.context')->getToken()->getUser();
//        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager('telconet_infraestructura');
            $entity = $em->getRepository('schemaBundle:AdmiProveedorMedidor')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AdmiProveedorMedidor entity.');
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

        return $this->redirect($this->generateUrl('admiproveedormedidor'));
    }

    
    public function deleteAjaxAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $peticion = $this->get('request');
        
        $parametro = $peticion->get('param');
        
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");
        
        $array_valor = explode("|",$parametro);
        foreach($array_valor as $id):
            if (null == $entity = $em->find('schemaBundle:AdmiProveedorMedidor', $id)) {
                $respuesta->setContent("No existe la entidad");
            }
            else{
				if(strtolower($entity->getEstado()) != "eliminado")
				{
					$entity->setEstado("Eliminado");
					$entity->setFeUltMod(new \DateTime('now'));
					$entity->setUsrUltMod($request->getSession()->get('user'));
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
        $estado = $peticion->query->get('estado');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiProveedorMedidor')
            ->generarJsonProveedoresMedidores($nombre,$estado,$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    public function getProveedoresMedidoresAction(){
       $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiProveedorMedidor')
            ->generarJsonProveedoresMedidores("","Activo",$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
}