<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiInterfaceModelo;
use telconet\schemaBundle\Form\AdmiInterfaceModeloType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Symfony\Component\HttpFoundation\Response;
 
class AdmiInterfaceModeloController extends Controller implements TokenAuthenticatedController
{ 
    /**
    * @Secure(roles="ROLE_129-1")
    */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');

        $entities = $em->getRepository('schemaBundle:AdmiInterfaceModelo')->findAll();

        return $this->render('administracionBundle:AdmiInterfaceModelo:index.html.twig', array(
            'entities' => $entities
        ));
    }
    
    public function ajaxListAllAction()
    {
        $request = $this->getRequest();
        $em = $this->get('doctrine')->getManager('telconet_infraestructura');

        $interfaces = $em->getRepository('schemaBundle:AdmiInterfaceModelo')->findAll();
        $i=1;
        foreach ($interfaces as $interface){
            if($i % 2==0)
                    $class='k-alt';
            else
                    $class='';
            
            $urlVer = $this->generateUrl('admiinterfacemodelo_show', array('id' => $interface->getId()));
            $urlEditar = $this->generateUrl('admiinterfacemodelo_edit', array('id' => $interface->getId()));

            $arreglo[]= array(
                'id'=> $interface->getId(),
                'modeloElementoId'=> $interface->getModeloElementoId()->getNombreModeloElemento(),
                'tipoInterfaceId'=> $interface->getTipoInterfaceId()->getNombreTipoInterface(),
                'estado' => $interface->getEstado(),
                'fechaCreacion'=> strval(date_format($interface->getFeCreacion(),"d/m/Y G:i")),
                'usuarioCreacion'=> $interface->getUsrCreacion(),
                'urlVer'=> $urlVer,
                'urlEditar'=> $urlEditar,
                'clase'=> $class
            );  
            $i++;
        }

        if (empty($arreglo)){
            $arreglo[]= array(
                    'id'=> "",
                    'modeloElementoId'=> "",
                    'tipoInterfaceId'=> "",
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

        if (null == $interfaceInterface = $em->find('schemaBundle:AdmiInterfaceModelo', $id)) {
            throw new NotFoundHttpException('No existe la Interface Modelo que se quiere mostrar');
        }

        return $this->render('administracionBundle:AdmiInterfaceModelo:show.html.twig', array(
            'interfaceModelo'   => $interfaceInterface,
            'flag' =>$peticion->get('flag')
        ));
    }
    
    /**
    * @Secure(roles="ROLE_129-2")
    */
    public function newAction()
    {
        $entity = new AdmiInterfaceModelo();
        $form   = $this->createForm(new AdmiInterfaceModeloType(), $entity);

        return $this->render('administracionBundle:AdmiInterfaceModelo:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }
    
    /**
    * 
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
        $entity  = new AdmiInterfaceModelo();
        $form    = $this->createForm(new AdmiInterfaceModeloType(), $entity);
        
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
            
            return $this->redirect($this->generateUrl('admiinterfacemodelo_show', array('id' => $entity->getId())));
        }
        
//        die("murio");
        return $this->render('administracionBundle:AdmiInterfaceModelo:new.html.twig', array(
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

        if (null == $interface = $em->find('schemaBundle:AdmiInterfaceModelo', $id)) {
            throw new NotFoundHttpException('No existe la Interface Modelo que se quiere modificar');
        }

        $formulario =$this->createForm(new AdmiInterfaceModeloType(), $interface);
//        $formulario->setData($interface);

        return $this->render('administracionBundle:AdmiInterfaceModelo:edit.html.twig', array(
                                    'edit_form' => $formulario->createView(),
                                    'interfaceModelo'   => $interface));
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

        $entity = $em->getRepository('schemaBundle:AdmiInterfaceModelo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AdmiInterfaceModelo entity.');
        }

        $editForm   = $this->createForm(new AdmiInterfaceModeloType(), $entity);

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

            return $this->redirect($this->generateUrl('admiinterfacemodelo_show', array('id' => $id)));
        }

        return $this->render('administracionBundle:AdmiInterfaceModelo:edit.html.twig',array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView()
        ));
    }
    
    /**
    * @author John Vera <javera@telconet.ec 
    * @version 1.1 17-10-2016 Se setea el user de la session
    * 
    * @Secure(roles="ROLE_129-8")
    */
    public function deleteAction($id){
//        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();
        $objSession  = $request->getSession();

//        $form->bind($request);
		//$user = $this->get('security.context')->getToken()->getUser();
//        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager('telconet_infraestructura');
            $entity = $em->getRepository('schemaBundle:AdmiInterfaceModelo')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AdmiInterfaceModelo entity.');
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

        return $this->redirect($this->generateUrl('admiinterfacemodelo'));
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
            if (null == $entity = $em->find('schemaBundle:AdmiInterfaceModelo', $id)) {
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
        
        $modeloElemento = $peticion->query->get('modeloElemento');
        $tipoInterface = $peticion->query->get('tipoInterface');
        $estado = $peticion->query->get('estado');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiInterfaceModelo')
            ->generarJsonInterfacesModelos($modeloElemento,$tipoInterface,$estado,$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    public function getInterfaceModeloAction(){
       $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $modeloElemento = $peticion->query->get('modeloElemento');
        
        $estado = "Todos";
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiInterfaceModelo')
            ->generarJsonInterfacesModelosPorModeloElemento($modeloElemento,$estado,$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
}