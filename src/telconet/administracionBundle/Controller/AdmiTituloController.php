<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiTitulo;
use telconet\schemaBundle\Form\AdmiTituloType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;
use telconet\schemaBundle\Entity\ReturnResponse;

class AdmiTituloController extends Controller implements TokenAuthenticatedController
{ 
    /**
    * @Secure(roles="ROLE_36-1")
    */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager('telconet');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("36", "1");

        $entities = $em->getRepository('schemaBundle:AdmiTitulo')->findAll();

        return $this->render('administracionBundle:AdmiTitulo:index.html.twig', array(
            'item' => $entityItemMenu,
            'titulo' => $entities
        ));
    }
    
    /**
    * @Secure(roles="ROLE_36-6")
    */
    public function showAction($id)
    {
        $peticion = $this->get('request');
        
        $em = $this->getDoctrine()->getManager("telconet");
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("36", "1");

        if (null == $titulo = $em->find('schemaBundle:AdmiTitulo', $id)) {
            throw new NotFoundHttpException('No existe el AdmiTitulo que se quiere mostrar');
        }

        return $this->render('administracionBundle:AdmiTitulo:show.html.twig', array(
            'item' => $entityItemMenu,
            'titulo'   => $titulo,
            'flag' =>$peticion->get('flag')
        ));
    }
    
    /**
    * @Secure(roles="ROLE_36-2")
    */
    public function newAction()
    {
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("36", "1");
        $entity = new AdmiTitulo();
        $form   = $this->createForm(new AdmiTituloType(), $entity);

        return $this->render('administracionBundle:AdmiTitulo:new.html.twig', array(
            'item' => $entityItemMenu,
            'titulo' => $entity,
            'form'   => $form->createView()
        ));
    }
    
    /**
    * @Secure(roles="ROLE_36-3")
    */
    public function createAction()
    {
        $request = $this->get('request');
        $em = $this->get('doctrine')->getManager('telconet');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("36", "1");
        $entity  = new AdmiTitulo();
        $form    = $this->createForm(new AdmiTituloType(), $entity);        
        $form->bind($request);
        
        if ($form->isValid()) {
            $em->getConnection()->beginTransaction();
            
            $entity->setEstado('Activo');
            $entity->setFeCreacion(new \DateTime('now'));
            $entity->setUsrCreacion($request->getSession()->get('user'));
            $entity->setFeUltMod(new \DateTime('now'));
            $entity->setUsrUltMod($request->getSession()->get('user'));
            
            $em->persist($entity);
            $em->flush();
            
            $em->getConnection()->commit();
            
            return $this->redirect($this->generateUrl('admititulo_show', array('id' => $entity->getId())));
        }
        
        return $this->render('administracionBundle:AdmiTitulo:new.html.twig', array(
            'item' => $entityItemMenu,
            'titulo' => $entity,
            'form'   => $form->createView()
        ));
        
    }
    
    /**
    * @Secure(roles="ROLE_36-4")
    */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager("telconet");
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("36", "1");

        if (null == $titulo = $em->find('schemaBundle:AdmiTitulo', $id)) {
            throw new NotFoundHttpException('No existe el AdmiTitulo que se quiere modificar');
        }

        $formulario =$this->createForm(new AdmiTituloType(), $titulo);
        return $this->render('administracionBundle:AdmiTitulo:edit.html.twig', array(
            'item' => $entityItemMenu,
			'edit_form' => $formulario->createView(),
			'titulo'   => $titulo));
    }
    
    /**
    * @Secure(roles="ROLE_36-5")
    */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("36", "1");
        $entity = $em->getRepository('schemaBundle:AdmiTitulo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AdmiTitulo entity.');
        }

        $editForm   = $this->createForm(new AdmiTituloType(), $entity);
        $request = $this->getRequest();
        $editForm->bind($request);

        if ($editForm->isValid()) {
            /*Para que guarde la fecha y el usuario correspondiente*/
            $entity->setFeUltMod(new \DateTime('now'));
            //$entity->setIdUsuarioModificacion($user->getUsername());
            $entity->setUsrUltMod($request->getSession()->get('user'));
            /*Para que guarde la fecha y el usuario correspondiente*/
			
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admititulo_show', array('id' => $id)));
        }

        return $this->render('administracionBundle:AdmiTitulo:edit.html.twig',array(
            'item' => $entityItemMenu,
            'titulo'      => $entity,
            'edit_form'   => $editForm->createView()
        ));
    }
    
    /**
    * @Secure(roles="ROLE_36-8")
    */
    public function deleteAction($id){
//        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

//        $form->bind($request);
		//$user = $this->get('security.context')->getToken()->getUser();
//        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager('telconet');
            $entity = $em->getRepository('schemaBundle:AdmiTitulo')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AdmiTitulo entity.');
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

        return $this->redirect($this->generateUrl('admititulo'));
    }

    /**
    * @Secure(roles="ROLE_36-9")
    */
    public function deleteAjaxAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $request = $this->get('request');
        $parametro = $request->get('param');
        $em = $this->getDoctrine()->getManager("telconet");
        
        $array_valor = explode("|",$parametro);
        foreach($array_valor as $id):
            if (null == $entity = $em->find('schemaBundle:AdmiTitulo', $id)) {
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
    * @Secure(roles="ROLE_36-7")
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
            ->getManager("telconet")
            ->getRepository('schemaBundle:AdmiTitulo')
            ->generarJson($nombre,$estado,$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }

    /**
     * getAdmiTituloAction, obtiene la informacion de los titulos
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 17-04-2016
     * @since 1.0
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAdmiTituloAction()
    {
        $objRequest         = $this->getRequest();
        $objReturnResponse  = new ReturnResponse();
        $objResponse        = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');
        $strEstado          = $objRequest->get('strEstado');
        $strAppendTitulo = $objRequest->get('strAppendTitulo');

        $emGeneral = $this->getDoctrine()->getManager();

        if(empty($strEstado))
        {
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . 'No se esta enviando el estado de los titulos.');
            $objResponse->setContent(json_encode((array) $objReturnResponse));
            return $objResponse;
        }

        $entityAdmiTitulo = $emGeneral->getRepository('schemaBundle:AdmiTitulo')
                                      ->findBy(array('estado'            => $strEstado),
                                               array('descripcionTitulo' => 'ASC'));
        $arrayAdmiTitulo = array();
        if(!empty($strAppendTitulo))
        {
            $arrayAdmiTitulo[] = array('intIdTitulo'          => 0,
                                'strDescripcionTitulo' => $strAppendTitulo,
                                'strEstado'         => '',
                                'strFeCreacion'    => '',
                                'strUsrCreacion'     => '');
        }
        $intCountTitulo = 0;
        foreach($entityAdmiTitulo as $objAdmiTitulo):
            $intCountTitulo ++;
            $arrayAdmiTitulo[] = array('intIdTitulo'            => $objAdmiTitulo->getId(),
                                       'strDescripcionTitulo'   => $objAdmiTitulo->getDescripcionTitulo(),
                                       'strEstado'              => $objAdmiTitulo->getEstado(),
                                       'strFeCreacion'          => ($objAdmiTitulo->getFeCreacion()) ? 
                                                                   date_format($objAdmiTitulo->getFeCreacion(), "d-m-Y H:i:s") : '',
                                       'strUsrCreacion'         => $objAdmiTitulo->getUsrCreacion());
//           if($intCountTitulo == 50)
//           {
//               break;
//           }
        endforeach;

        $objReturnResponse->setRegistros($arrayAdmiTitulo);
        $objReturnResponse->setTotal($intCountTitulo);
        $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);
        $objResponse->setContent(json_encode((array) $objReturnResponse));

        return $objResponse;
    } //getAdmiTituloAction

}