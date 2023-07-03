<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiTipoConexion;
use telconet\schemaBundle\Form\AdmiTipoConexionType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Response;

class AdmiTipoConexionController extends Controller
{ 
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');

        $entities = $em->getRepository('schemaBundle:AdmiTipoConexion')->findAll();

        return $this->render('administracionBundle:AdmiTipoConexion:index.html.twig', array(
            'entities' => $entities
        ));
    }
    
    public function showAction($id)
    {
        $peticion = $this->get('request');
        
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");

        if (null == $tipoConexion = $em->find('schemaBundle:AdmiTipoConexion', $id)) {
            throw new NotFoundHttpException('No existe el Tipo de Conexion que se quiere mostrar');
        }

        return $this->render('administracionBundle:AdmiTipoConexion:show.html.twig', array(
            'tipoconexion'   => $tipoConexion,
            'flag' =>$peticion->get('flag')
        ));
    }
    
    public function newAction()
    {
        $entity = new AdmiTipoConexion();
        $form   = $this->createForm(new AdmiTipoConexionType(), $entity);

        return $this->render('administracionBundle:AdmiTipoConexion:new.html.twig', array(
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
        $entity  = new AdmiTipoConexion();
        $form    = $this->createForm(new AdmiTipoConexionType(), $entity);
        
        $entity->setEstado('Activo');
        $entity->setFeCreacion(new \DateTime('now'));
        $entity->setUsrCreacion($objSession->get('user'));
        
        $form->bind($request);
        
        if ($form->isValid()) {
            $em->getConnection()->beginTransaction();
            $em->persist($entity);
            $em->flush();
            $em->getConnection()->commit();
            
            return $this->redirect($this->generateUrl('admitipoconexion_show', array('id' => $entity->getId())));
        }
        
        return $this->render('administracionBundle:AdmiTipoConexion:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
        
    }
    
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");

        if (null == $tipo = $em->find('schemaBundle:AdmiTipoConexion', $id)) {
            throw new NotFoundHttpException('No existe el Tipo de Conexion que se quiere modificar');
        }

        $formulario =$this->createForm(new AdmiTipoConexionType(), $tipo);
//        $formulario->setData($tipo);

        return $this->render('administracionBundle:AdmiTipoConexion:edit.html.twig', array(
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

        $entity = $em->getRepository('schemaBundle:AdmiTipoConexion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AdmiTipoConexion entity.');
        }

        $editForm   = $this->createForm(new AdmiTipoConexionType(), $entity);

        $request = $this->getRequest();
        $objSession  = $request->getSession();

        $editForm->bind($request);

        if ($editForm->isValid()) {
            /*Para que guarde la fecha y el usuario correspondiente*/
            $entity->setFeUltMod(new \DateTime('now'));
            $entity->setUsrUltMod($objSession->get('user'));            
			
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admitipoconexion_show', array('id' => $id)));
        }

        return $this->render('administracionBundle:AdmiTipoConexion:edit.html.twig',array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView()
        ));
    }
    
    
    /**
    * @author John Vera <javera@telconet.ec 
    * @version 1.1 17-10-2016 Se setea el user de la session
    **/
    
    public function deleteAction($id){

        $request = $this->getRequest();
        $objSession  = $request->getSession();

            $em = $this->getDoctrine()->getManager('telconet_infraestructura');
            $entity = $em->getRepository('schemaBundle:AdmiTipoConexion')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AdmiTipoConexion entity.');
            }
            $estado = 'Eliminado';
            $entity->setEstado($estado);
            /*Para que guarde la fecha y el usuario correspondiente*/
            $entity->setFeUltMod(new \DateTime('now'));
            $entity->setUsrUltMod($objSession->get('user'));
			
			$em->persist($entity);	
            $em->flush();

        return $this->redirect($this->generateUrl('admitipoconexion'));
    }

    
    public function deleteAjaxAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $peticion = $this->get('request');
        
        $parametro = $peticion->get('param');
        
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");
        
        $array_valor = explode("|",$parametro);
        foreach($array_valor as $id):
            if (null == $entity = $em->find('schemaBundle:AdmiTipoConexion', $id)) {
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
            ->getRepository('schemaBundle:AdmiTipoConexion')
            ->generarJsonTiposConexiones($nombre,$estado,$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    public function getTiposConexionesAction(){
       $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiTipoConexion')
            ->generarJsonTiposConexiones("","Activo",$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
}