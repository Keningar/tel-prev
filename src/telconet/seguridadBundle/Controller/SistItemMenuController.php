<?php

namespace telconet\seguridadBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\schemaBundle\Entity\SistItemMenu;
use telconet\schemaBundle\Form\SistItemMenuType;
use Symfony\Component\HttpFoundation\Response;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * SistItemMenu controller.
 *
 */
class SistItemMenuController extends Controller implements TokenAuthenticatedController
{
    /**
    * @Secure(roles="ROLE_4-1")
    */ 
    public function indexAction()
    {
        $request  = $this->get('request');
        $session  = $request->getSession();

        $em = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("4", "1");
        
        return $this->render('seguridadBundle:SistItemMenu:index.html.twig', array(
             'item' => $entityItemMenu
        ));
        
    }

    /**
    * @Secure(roles="ROLE_4-6")
    */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("4", "1");
        $entity = $em->getRepository('schemaBundle:SistItemMenu')->find($id);
		
		if (!$entity) {
            throw $this->createNotFoundException('Unable to find SistItemMenu entity.');
        }

        $deleteForm = $this->createDeleteForm($id);        
        
        $parametros=array(
            'item' => $entityItemMenu,
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
			//'acc_relaciondas'=>$acc_relacionadas,
			//'img_opcion_menu'=>$img_opcion
        );
        
        return $this->render('seguridadBundle:SistItemMenu:show.html.twig', $parametros);
    }

    /**
    * @Secure(roles="ROLE_4-2")
    */ 
    public function newAction()
    {
        $em = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("4", "1");
        $entity = new SistItemMenu();
        $form   = $this->createForm(new SistItemMenuType($options), $entity);
        
        return $this->render('seguridadBundle:SistItemMenu:new.html.twig', array(
            'item' => $entityItemMenu,
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
    * @Secure(roles="ROLE_4-3")
    */ 
    public function createAction()
    {
        $em = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("4", "1");
        $entity  = new SistItemMenu();
        $request = $this->getRequest();
        
        $form    = $this->createForm(new SistItemMenuType($options), $entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            
            $entity->setEstado("Activo");

            /*Para que guarde la fecha y el usuario correspondiente*/
            $entity->setFeCreacion(new \DateTime('now'));
            $entity->setUsrCreacion($request->getSession()->get('user'));
            /*Para que guarde la fecha y el usuario correspondiente*/
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('sistitemmenu_show', array('id' => $entity->getId())));
        }

        $parametros=array(
            'item' => $entityItemMenu,
            'entity' => $entity,
            'form'   => $form->createView()
        );
		
        return $this->render('seguridadBundle:SistItemMenu:new.html.twig', $parametros);
    }

    /**
    * @Secure(roles="ROLE_4-4")
    */ 
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("4", "1");
        $entity = $em->getRepository('schemaBundle:SistItemMenu')->find($id);
		
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SistItemMenu entity.');
        }
        
        $editForm = $this->createForm(new SistItemMenuType(), $entity);
        $deleteForm = $this->createDeleteForm($id);
        $parametros=array(
            'item' => $entityItemMenu,
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView()
        );

        return $this->render('seguridadBundle:SistItemMenu:edit.html.twig',$parametros);
    }

    /**
    * @Secure(roles="ROLE_4-5")
    */ 
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("4", "1");
        $entity = $em->getRepository('schemaBundle:SistItemMenu')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SistItemMenu entity.');
        }
        
        $editForm   = $this->createForm(new SistItemMenuType(), $entity);
        $deleteForm = $this->createDeleteForm($id);
        $request = $this->getRequest();
        $editForm->handleRequest($request);
        
        if ($editForm->isValid()) {
			$entity->setEstado("Modificado");                
			$entity->setFeUltMod(new \DateTime('now'));
			$entity->setUsrUltMod($request->getSession()->get('user'));              
			$em->persist($entity);
			$em->flush();
			return $this->redirect($this->generateUrl('sistitemmenu_edit', array('id' => $id)));
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
		
        return $this->render('seguridadBundle:SistItemMenu:edit.html.twig', $parametros );
    }

    /**
    * @Secure(roles="ROLE_4-8")
    */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet_seguridad');
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->handleRequest($request);

        if ($form->isValid()) {
			
            $entity = $em->getRepository('schemaBundle:SistItemMenu')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find SistItemMenu entity.');
            }
			
            $entity->setEstado("Eliminado");
            /*Para que guarde la fecha y el usuario correspondiente*/
            $entity->setFeUltMod(new \DateTime('now'));
            $entity->setUsrUltMod($request->getSession()->get('user'));
            //$em->remove($entity);
			$em->persist($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('sistitemmenu'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
    
    /**
    * @Secure(roles="ROLE_4-7")
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
            ->getRepository('schemaBundle:SistItemMenu')
            ->generarJsonItemMenu($nombre,$estado,$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
	/**
    * @Secure(roles="ROLE_4-8")
    */    
    public function deleteAjaxAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $request = $this->get('request');
        
        $parametro = $request->get('param');
        
        $em = $this->getDoctrine()->getManager("telconet_seguridad");
        
        $array_valor = explode("|",$parametro);
        foreach($array_valor as $id):
            
            if (null == $entity = $em->find('schemaBundle:SistItemMenu', $id)) {
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
