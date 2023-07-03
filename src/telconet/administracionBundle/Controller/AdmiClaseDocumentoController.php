<?php

namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\schemaBundle\Entity\AdmiClaseDocumento;
use telconet\schemaBundle\Form\AdmiClaseDocumentoType;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * AdmiClaseDocumento controller.
 *
 */
class AdmiClaseDocumentoController extends Controller implements TokenAuthenticatedController
{
    /**
     * Lists all AdmiClaseDocumento entities.
     *
     */
    /**
    * @Secure(roles="ROLE_159-1")
    */
    public function indexAction()
    {
        $request  = $this->get('request');
        $session  = $request->getSession();
        
        $em = $this->getDoctrine()->getManager('telconet_comunicacion');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("159", "1");
        
        return $this->render('administracionBundle:AdmiClaseDocumento:index.html.twig', array(
            'item' => $entityItemMenu,
        ));
        
    }

    /**
     * Finds and displays a AdmiClaseDocumento entity.
     *
     */
    /**
    * @Secure(roles="ROLE_159-6")
    */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet_comunicacion');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("159", "1");
        $entity = $em->getRepository('schemaBundle:AdmiClaseDocumento')->find($id);
		
		if (!$entity) {
            throw $this->createNotFoundException('Unable to find AdmiClaseDocumento entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        /*Para la carga de la imagen desde el default controller*/
        //$img_opcion = $adminController->getImgOpcion($em_administracion,'COM-PROS');        
        $parametros=array(
            'item' => $entityItemMenu,
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
			//'acc_relaciondas'=>$acc_relacionadas,
			//'img_opcion_menu'=>$img_opcion
        );
        
        return $this->render('administracionBundle:AdmiClaseDocumento:show.html.twig', $parametros);
    }

    /**
     * Displays a form to create a new AdmiClaseDocumento entity.
     *
     */
    /**
    * @Secure(roles="ROLE_159-2")
    */
    public function newAction()
    {
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("159", "1");
        $entity = new AdmiClaseDocumento();
        $form   = $this->createForm(new AdmiClaseDocumentoType(), $entity);
        
        return $this->render('administracionBundle:AdmiClaseDocumento:new.html.twig', array(
            'item' => $entityItemMenu,
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Creates a new AdmiClaseDocumento entity.
     *
     */
    /**
    * @Secure(roles="ROLE_159-3")
    */
    public function createAction()
    {
		$em = $this->getDoctrine()->getManager('telconet_comunicacion');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("159", "1");
        $entity  = new AdmiClaseDocumento();
        $request = $this->getRequest();
        
        $form    = $this->createForm(new AdmiClaseDocumentoType(), $entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            
            $entity->setEstado("Activo");
            $entity->setFeCreacion(new \DateTime('now'));
            $entity->setUsrCreacion($request->getSession()->get('user'));
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admiclasedocumento_show', array('id' => $entity->getId())));
        }

        $parametros=array(
            'item' => $entityItemMenu,
            'entity' => $entity,
            'form'   => $form->createView()
        );
		
        return $this->render('administracionBundle:AdmiClaseDocumento:new.html.twig', $parametros);
    }

    /**
     * Displays a form to edit an existing AdmiClaseDocumento entity.
     *
     */
    /**
    * @Secure(roles="ROLE_159-4")
    */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet_comunicacion');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("159", "1");
        $entity = $em->getRepository('schemaBundle:AdmiClaseDocumento')->find($id);
		
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AdmiClaseDocumento entity.');
        }
        
        $editForm = $this->createForm(new AdmiClaseDocumentoType(), $entity);
        $deleteForm = $this->createDeleteForm($id);
        $parametros=array(
            'item' => $entityItemMenu,
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView()
        );

        return $this->render('administracionBundle:AdmiClaseDocumento:edit.html.twig',$parametros);
    }

    /**
     * Edits an existing AdmiClaseDocumento entity.
     *
     */
    /**
    * @Secure(roles="ROLE_159-5")
    */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet_comunicacion');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("159", "1");
        $entity = $em->getRepository('schemaBundle:AdmiClaseDocumento')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AdmiClaseDocumento entity.');
        }
        	
        $editForm   = $this->createForm(new AdmiClaseDocumentoType($options), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
                $entity->setEstado("Modificado");
                
                $entity->setFeUltMod(new \DateTime('now'));
                $entity->setUsrUltMod($request->getSession()->get('user'));
                $em->persist($entity);
                $em->flush();
                return $this->redirect($this->generateUrl('admiclasedocumento_edit', array('id' => $id)));
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
		
        return $this->render('administracionBundle:AdmiClaseDocumento:edit.html.twig', $parametros );
    }

    /**
     * Deletes a AdmiClaseDocumento entity.
     *
     */
    /**
    * @Secure(roles="ROLE_159-8")
    */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet_comunicacion');
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->handleRequest($request);

        if ($form->isValid()) {
			
            $entity = $em->getRepository('schemaBundle:AdmiClaseDocumento')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AdmiClaseDocumento entity.');
            }	
            
            $entity->setEstado("Eliminado");
            /*Para que guarde la fecha y el usuario correspondiente*/
            $entity->setFeUltMod(new \DateTime('now'));
            $entity->setUsrUltMod($request->getSession()->get('user'));
            //$em->remove($entity);
			$em->persist($entity);	
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admiclasedocumento'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
    
    /*
     * Llena el grid de consulta.
     */
    /**
    * @Secure(roles="ROLE_159-7")
    */
    public function gridAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
		$queryNombre = $peticion->query->get('query') ? $peticion->query->get('query') : "";
        $nombre = ($queryNombre != '' ? $queryNombre : $peticion->query->get('nombre'));
        $estado = $peticion->query->get('estado');
                        
        if($peticion->query->get('visible')){	  
	    $visible = $peticion->query->get('visible');
	}else $visible='Todos';
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_comunicacion")
            ->getRepository('schemaBundle:AdmiClaseDocumento')
            ->generarJsonEntidades($nombre,$estado,$start,$limit,$visible);
        $respuesta->setContent($objJson);
        
        return $respuesta;
        
        
    }
    
    /**
    * @Secure(roles="ROLE_159-9")
    */
    public function deleteAjaxAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $request = $this->get('request');
        
        $parametro = $request->get('param');
        
        $em = $this->getDoctrine()->getManager("telconet_comunicacion");
        
        $array_valor = explode("|",$parametro);
        foreach($array_valor as $id):
            if (null == $entity = $em->find('schemaBundle:AdmiClaseDocumento', $id)) {
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
