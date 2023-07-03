<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiTipoInterface;
use telconet\schemaBundle\Form\AdmiTipoInterfaceType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Symfony\Component\HttpFoundation\Response;

class AdmiTipoInterfaceController extends Controller implements TokenAuthenticatedController
{ 
    /**
    * @Secure(roles="ROLE_129-1")
    */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');

        $entities = $em->getRepository('schemaBundle:AdmiTipoInterface')->findAll();

        return $this->render('administracionBundle:AdmiTipoInterface:index.html.twig', array(
            'entities' => $entities
        ));
    }
    
    
    public function ajaxListAllAction()
    {
        $request = $this->getRequest();
        $em = $this->get('doctrine')->getManager('telconet_infraestructura');

        $tipos = $em->getRepository('schemaBundle:AdmiTipoInterface')->findAll();
        $i=1;
        foreach ($tipos as $tipo){
            if($i % 2==0)
                    $class='k-alt';
            else
                    $class='';
            
            $urlVer = $this->generateUrl('admitipointerface_show', array('id' => $tipo->getId()));
            $urlEditar = $this->generateUrl('admitipointerface_edit', array('id' => $tipo->getId()));

            $arreglo[]= array(
                'id'=> $tipo->getId(),
                'nombreTipoInterface'=> $tipo->getNombreTipoInterface(),
                'conectorInterfaceId'=> $tipo->getConectorInterfaceId()->getNombreConectorInterface(),
                'estado' => $tipo->getEstado(),
                'fechaCreacion'=> strval(date_format($tipo->getFeCreacion(),"d/m/Y G:i")),
                'usuarioCreacion'=> $tipo->getUsrCreacion(),
                'urlVer'=> $urlVer,
                'urlEditar'=> $urlEditar,
                'clase'=> $class
            );  
            $i++;
        }

        if (empty($arreglo)){
            $arreglo[]= array(
                    'id'=> "",
                    'nombreTipoInterface'=> "",
                    'conectorInterface'=> "",
                    'estado' => "",
                    'fechaCreacion'=> "",
                    'usuarioCreacion'=> "",
                    'urlVer'=> "",
                    'urlEditar'=> "",
                    'clase'=> ""
                    );
        }		
        $response = new Response(json_encode($arreglo));
        $response->headers->set('Content-type', 'text/json');		
        return $response;	
    }
    
    /**
    * @Secure(roles="ROLE_129-6")
    */
    public function showAction($id)
    {
        $peticion = $this->get('request');
        
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");

        if (null == $tipoInterface = $em->find('schemaBundle:AdmiTipoInterface', $id)) {
            throw new NotFoundHttpException('No existe el Conector que se quiere mostrar');
        }

        return $this->render('administracionBundle:AdmiTipoInterface:show.html.twig', array(
            'tipoInterface'   => $tipoInterface,
            'flag' =>$peticion->get('flag')
        ));
    }
    
    /**
    * @Secure(roles="ROLE_129-2")
    */
    public function newAction()
    {
        $entity = new AdmiTipoInterface();
        $form   = $this->createForm(new AdmiTipoInterfaceType(), $entity);

        return $this->render('administracionBundle:AdmiTipoInterface:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }
    
    /**
    * @author John Vera <javera@telconet.ec 
    * @version 1.1 17-10-2016 Se setea el user de la session
    * 
    * @Secure(roles="ROLE_129-3")
    */
    public function createAction()
    {
        $request = $this->get('request');
        $objSession  = $request->getSession();
        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        $entity  = new AdmiTipoInterface();
        $form    = $this->createForm(new AdmiTipoInterfaceType(), $entity);
        
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
            
            return $this->redirect($this->generateUrl('admitipointerface_show', array('id' => $entity->getId())));
        }
        
//        die("murio");
        return $this->render('administracionBundle:AdmiTipoInterface:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
        
    }
    
    /**
    * @Secure(roles="ROLE_129-4")
    */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");

        if (null == $tipo = $em->find('schemaBundle:AdmiTipoInterface', $id)) {
            throw new NotFoundHttpException('No existe el Conector que se quiere modificar');
        }

        $formulario =$this->createForm(new AdmiTipoInterfaceType(), $tipo);
//        $formulario->setData($tipo);

        return $this->render('administracionBundle:AdmiTipoInterface:edit.html.twig', array(
                                    'edit_form' => $formulario->createView(),
                                    'tipoInterface'   => $tipo));
    }
    
    /**
    * @author John Vera <javera@telconet.ec 
    * @version 1.1 17-10-2016 Se setea el user de la session
    * 
    * @Secure(roles="ROLE_129-5")
    */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');

        $entity = $em->getRepository('schemaBundle:AdmiTipoInterface')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AdmiTipoInterface entity.');
        }

        $editForm   = $this->createForm(new AdmiTipoInterfaceType(), $entity);

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

            return $this->redirect($this->generateUrl('admitipointerface_show', array('id' => $id)));
        }

        return $this->render('administracionBundle:AdmiTipoInterface:edit.html.twig',array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView()
        ));
    }
    
    /**
    * @Secure(roles="ROLE_129-8")
    * 
    * @author John Vera <javera@telconet.ec 
    * @version 1.1 17-10-2016 Se setea el user de la session
    */
    public function deleteAction($id){
//        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();
        $objSession  = $request->getSession();

//        $form->bind($request);
		//$user = $this->get('security.context')->getToken()->getUser();
//        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager('telconet_infraestructura');
            $entity = $em->getRepository('schemaBundle:AdmiTipoInterface')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AdmiTipoInterface entity.');
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

        return $this->redirect($this->generateUrl('admitipointerface'));
    }
    
    /**
    * @Secure(roles="ROLE_129-9")
    */
    public function deleteAjaxAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $peticion = $this->get('request');
        
        $parametro = $peticion->get('param');
        
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");
        
        $array_valor = explode("|",$parametro);
        foreach($array_valor as $id):
            if (null == $entity = $em->find('schemaBundle:AdmiTipoInterface', $id)) {
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
    
    /**
    * @Secure(roles="ROLE_129-46")
    */
    public function getEncontradosAction(){
       $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
		$queryNombre = $peticion->query->get('query') ? $peticion->query->get('query') : "";
        $nombre = ($queryNombre != '' ? $queryNombre : $peticion->query->get('nombre'));
        $conectorInterface = $peticion->query->get('conectorInterface');
        
        $estado = $peticion->query->get('estado');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiTipoInterface')
            ->generarJsonTiposInterfaces($nombre,$conectorInterface,$estado,$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    public function getTiposInterfacesAction(){
       $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiTipoInterface')
            ->generarJsonTiposInterfaces("","","Activo",$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
}