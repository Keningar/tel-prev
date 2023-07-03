<?php

namespace telconet\seguridadBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\schemaBundle\Entity\SistAccion;
use telconet\schemaBundle\Form\SistAccionType;
use Symfony\Component\HttpFoundation\Response;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * SistAccion controller.
 *
 */
class SistAccionController extends Controller implements TokenAuthenticatedController
{
    /**
    * @Secure(roles="ROLE_73-1")
    */ 
    public function indexAction()
    {
        
        $request  = $this->get('request');
        $session  = $request->getSession();
        
        $em = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("73", "1");

        return $this->render('seguridadBundle:SistAccion:index.html.twig', array(
             'item' => $entityItemMenu
        ));
        
    }

    /**
    * @Secure(roles="ROLE_73-6")
    */ 
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("73", "1");
        $entity = $em->getRepository('schemaBundle:SistAccion')->find($id);
		
		if (!$entity) {
            throw $this->createNotFoundException('Unable to find SistAccion entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
		
        $parametros=array(
            'item' => $entityItemMenu,
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
			//'acc_relaciondas'=>$acc_relacionadas,
			//'img_opcion_menu'=>$img_opcion
        );
        
        return $this->render('seguridadBundle:SistAccion:show.html.twig', $parametros);
    }

    /**
    * @Secure(roles="ROLE_73-2")
    */ 
    public function newAction()
    {
        $em = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("73", "1");
        $entity = new SistAccion();
        $form   = $this->createForm(new SistAccionType($options), $entity);
        
        return $this->render('seguridadBundle:SistAccion:new.html.twig', array(
            'item' => $entityItemMenu,
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
    * @Secure(roles="ROLE_73-3")
    */ 
    public function createAction()
    {
        $em = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("73", "1");
        $entity  = new SistAccion();
        $request = $this->getRequest();
        
        $form    = $this->createForm(new SistAccionType(), $entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            
            $entity->setEstado("Activo");
            $entity->setFeCreacion(new \DateTime('now'));
            $entity->setUsrCreacion($request->getSession()->get('user'));
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('sistaccion_show', array('id' => $entity->getId())));
        }

        $parametros=array(
            'item' => $entityItemMenu,
            'entity' => $entity,
            'form'   => $form->createView()
        );
		
        return $this->render('seguridadBundle:SistAccion:new.html.twig', $parametros);
    }

    /**
    * @Secure(roles="ROLE_73-4")
    */ 
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("73", "1");
        $entity = $em->getRepository('schemaBundle:SistAccion')->find($id);
		
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SistAccion entity.');
        }
        
        $editForm = $this->createForm(new SistAccionType(), $entity);
        $deleteForm = $this->createDeleteForm($id);
        $parametros=array(
            'item' => $entityItemMenu,
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView()
        );

        return $this->render('seguridadBundle:SistAccion:edit.html.twig',$parametros);
    }

    /**
    * @Secure(roles="ROLE_73-5")
    */ 
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("73", "1");
        $entity = $em->getRepository('schemaBundle:SistAccion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SistAccion entity.');
        }
		
        $editForm   = $this->createForm(new SistAccionType($options), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
                $entity->setEstado("Modificado");
                
                $entity->setFeUltMod(new \DateTime('now'));
                $entity->setUsrUltMod($request->getSession()->get('user'));
                $em->persist($entity);
                $em->flush();
                return $this->redirect($this->generateUrl('sistaccion_edit', array('id' => $id)));
        }
	

		$parametros=array(
            'item' => $entityItemMenu,
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            //'img_opcion_menu'=>$img_opcion
        );
        
        if ($error)
			$parametros['error']=$error;
		
        return $this->render('seguridadBundle:SistAccion:edit.html.twig', $parametros );
    }

    /**
    * @Secure(roles="ROLE_73-8")
    */ 
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet_seguridad');
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->handleRequest($request);

        if ($form->isValid()) {
			
            $entity = $em->getRepository('schemaBundle:SistAccion')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find SistAccion entity.');
            }
			
            $entity->setEstado("Eliminado");
            /*Para que guarde la fecha y el usuario correspondiente*/
            $entity->setFeUltMod(new \DateTime('now'));
            $entity->setUsrUltMod($request->getSession()->get('user'));
            //$em->remove($entity);
			$em->persist($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('sistaccion'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
    
    /**
    * @Secure(roles="ROLE_73-7")
    */ 
    public function gridAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
		$queryNombre = $peticion->query->get('query') ? $peticion->query->get('query') : "";
        $nombre = ($queryNombre != '' ? $queryNombre : $peticion->query->get('nombre'));
        $estado = $peticion->query->get('estado');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_seguridad")
            ->getRepository('schemaBundle:SistAccion')
            ->generarJsonAccion($nombre,$estado,$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
	/**
    * @Secure(roles="ROLE_73-8")
    */     
    public function deleteAjaxAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $request = $this->get('request');
        
        $parametro = $request->get('param');
        
        $em = $this->getDoctrine()->getManager("telconet_seguridad");
        
        $array_valor = explode("|",$parametro);
        foreach($array_valor as $id):
            if (null == $entity = $em->find('schemaBundle:SistAccion', $id)) {
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
	
}
