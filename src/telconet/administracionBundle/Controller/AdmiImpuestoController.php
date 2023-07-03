<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiImpuesto;
use telconet\schemaBundle\Form\AdmiImpuestoType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response; 
use JMS\SecurityExtraBundle\Annotation\Secure;

class AdmiImpuestoController extends Controller implements TokenAuthenticatedController
{ 
    /**
    * @Secure(roles="ROLE_44-1")
    */
    public function indexAction()
    {
        //Se agregan roles
        if(true === $this->get('security.context')->isGranted('ROLE_44-4'))
        {
            $strRolesPermitidos[] = 'ROLE_44-4';
        }

        if(true === $this->get('security.context')->isGranted('ROLE_44-6'))
        {
            $strRolesPermitidos[] = 'ROLE_44-6';
        }

        if(true === $this->get('security.context')->isGranted('ROLE_44-8'))
        {
            $strRolesPermitidos[] = 'ROLE_44-8'; 
        }
        
        if(true === $this->get('security.context')->isGranted('ROLE_44-9'))
        {
            $strRolesPermitidos[] = 'ROLE_44-9';  
        }

        $em = $this->getDoctrine()->getManager('telconet_general');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("44", "1");

        $entities = $em->getRepository('schemaBundle:AdmiImpuesto')->findAll();

        return $this->render('administracionBundle:AdmiImpuesto:index.html.twig', array(
            'item' => $entityItemMenu,
            'impuesto' => $entities,
            'rolesPermitidos' => $strRolesPermitidos
        ));
    }
    
    /**
    * @Secure(roles="ROLE_44-6")
    */
    public function showAction($id)
    {

        $peticion = $this->get('request');
        
        $em = $this->getDoctrine()->getManager("telconet_general");
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("44", "1");

        if (null == $impuesto = $em->find('schemaBundle:AdmiImpuesto', $id)) {
            throw new NotFoundHttpException('No existe el AdmiImpuesto que se quiere mostrar');
        }

        return $this->render('administracionBundle:AdmiImpuesto:show.html.twig', array(
            'item' => $entityItemMenu,
            'impuesto'   => $impuesto,
            'flag' =>$peticion->get('flag')
        ));
    }
    
    /**
    * @Secure(roles="ROLE_44-2")
    */
    public function newAction()
    {
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("44", "1");
        $entity = new AdmiImpuesto();
        $form   = $this->createForm(new AdmiImpuestoType(), $entity);

        return $this->render('administracionBundle:AdmiImpuesto:new.html.twig', array(
            'item' => $entityItemMenu,
            'impuesto' => $entity,
            'form'   => $form->createView()
        ));
    }
    
    /**
    * @Secure(roles="ROLE_44-3")
    */
    public function createAction()
    {
        $request = $this->get('request');
        $em = $this->get('doctrine')->getManager('telconet_general');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("44", "1");
        $entity  = new AdmiImpuesto();
        $form    = $this->createForm(new AdmiImpuestoType(), $entity);
        $form->bind($request);
        
        if ($form->isValid()) {
            $em->getConnection()->beginTransaction();
            
            $entity->setEstado('Activo');
            $entity->setFeCreacion(new \DateTime('now'));
            $entity->setUsrCreacion($request->getSession()->get('user'));
            $entity->setFeUltMod(new \DateTime('now'));
            $entity->setUsrUltMod($request->getSession()->get('user'));
            
            $feVigenciaImpuestoPost = ($request->get('feVigenciaImpuestoPost') ? $request->get('feVigenciaImpuestoPost') : date("Y-m-d")) . " 00:00:00";
            $entity->setFechaVigenciaImpuesto(date_create($feVigenciaImpuestoPost));
            
            $em->persist($entity);
            $em->flush();
            $em->getConnection()->commit();
            
            return $this->redirect($this->generateUrl('admiimpuesto_show', array('id' => $entity->getId())));
        }
        
        return $this->render('administracionBundle:AdmiImpuesto:new.html.twig', array(
            'item' => $entityItemMenu,
            'impuesto' => $entity,
            'form'   => $form->createView()
        ));
        
    }
    
    /**
    * @Secure(roles="ROLE_44-4")
    */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager("telconet_general");
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("44", "1");

        if (null == $impuesto = $em->find('schemaBundle:AdmiImpuesto', $id)) {
            throw new NotFoundHttpException('No existe el AdmiImpuesto que se quiere modificar');
        }

        $formulario =$this->createForm(new AdmiImpuestoType(), $impuesto);
//        $formulario->setData($proceso);

        return $this->render('administracionBundle:AdmiImpuesto:edit.html.twig', array(
            'item' => $entityItemMenu,
			'edit_form' => $formulario->createView(),
			'impuesto'   => $impuesto));
    }
    
    /**
    * @Secure(roles="ROLE_44-5")
    */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet_general');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("44", "1");
        $entity = $em->getRepository('schemaBundle:AdmiImpuesto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AdmiImpuesto entity.');
        }

        $editForm   = $this->createForm(new AdmiImpuestoType(), $entity);
        $request = $this->getRequest();
        $editForm->bind($request);

        if ($editForm->isValid()) {
            /*Para que guarde la fecha y el usuario correspondiente*/
            $entity->setFeUltMod(new \DateTime('now'));
            //$entity->setIdUsuarioModificacion($user->getUsername());
            $entity->setUsrUltMod($request->getSession()->get('user'));
            /*Para que guarde la fecha y el usuario correspondiente*/
            
            $feVigenciaImpuestoPost = ($request->get('feVigenciaImpuestoPost') ? $request->get('feVigenciaImpuestoPost') : date("Y-m-d")) . " 00:00:00";
            $entity->setFechaVigenciaImpuesto(date_create($feVigenciaImpuestoPost));
            
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admiimpuesto_show', array('id' => $id)));
        }

        return $this->render('administracionBundle:AdmiImpuesto:edit.html.twig',array(
            'item' => $entityItemMenu,
            'impuesto'      => $entity,
            'edit_form'   => $editForm->createView()
        ));
    }
    
    /**
    * @Secure(roles="ROLE_44-8")
    */
    public function deleteAction($id){
//        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

//        $form->bind($request);
		//$user = $this->get('security.context')->getToken()->getUser();
//        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager('telconet_general');
            $entity = $em->getRepository('schemaBundle:AdmiImpuesto')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AdmiImpuesto entity.');
            }
            $estado = 'Eliminado';
            $entity->setEstado($estado);
            /*Para que guarde la fecha y el usuario correspondiente*/
            $entity->setFeUltMod(new \DateTime('now'));
            //$entity->setIdUsuarioModificacion($user->getUsername());
            $entity->setUsrUltMod($request->getSession()->get('user'));
            /*Para que guarde la fecha y el usuario correspondiente*/
			
			$em->persist($entity);	
            //$em->remove($entity);
            $em->flush();
//        }

        return $this->redirect($this->generateUrl('admiimpuesto'));
    }

    /**
    * @Secure(roles="ROLE_44-9")
    */
    public function deleteAjaxAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $request = $this->get('request');
        $parametro = $request->get('param');
        $em = $this->getDoctrine()->getManager("telconet_general");
        
        $array_valor = explode("|",$parametro);
        foreach($array_valor as $id):
            if (null == $entity = $em->find('schemaBundle:AdmiImpuesto', $id)) {
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
    
    /*
     * Llena el grid de consulta.
     */
    /**
    * @Secure(roles="ROLE_44-7")
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
            ->getManager("telconet_general")
            ->getRepository('schemaBundle:AdmiImpuesto')
            ->generarJson($nombre,$estado,$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
}