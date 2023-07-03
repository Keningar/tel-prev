<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiCanton;
use telconet\schemaBundle\Form\AdmiCantonType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response; 
use JMS\SecurityExtraBundle\Annotation\Secure;

class AdmiCantonController extends Controller implements TokenAuthenticatedController
{ 
    /**
     * Funcion que sirve para cargar la pantalla de la administracion de cantones
     * 
     * @version inicial
     * 
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.1 05-08-2016 Se adiciona los roles para permitir la visualizacion de las
     *                          acciones en el grid.
     * @Secure(roles="ROLE_25-1")
     */
    public function indexAction()
    {
        $rolesPermitidos = array();
        if(true === $this->get('security.context')->isGranted('ROLE_25-6'))
        {
            $rolesPermitidos[] = 'ROLE_25-6';
        }
        if(true === $this->get('security.context')->isGranted('ROLE_25-4'))
        {
            $rolesPermitidos[] = 'ROLE_25-4';
        }
        if(true === $this->get('security.context')->isGranted('ROLE_25-8'))
        {
            $rolesPermitidos[] = 'ROLE_25-8';
        }
        if(true === $this->get('security.context')->isGranted('ROLE_25-9'))
        {
            $rolesPermitidos[] = 'ROLE_25-9';
        }
        $em                     = $this->getDoctrine()->getManager('telconet_general');
        $em_seguridad           = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu         = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("25", "1");
        $entities               = $em->getRepository('schemaBundle:AdmiCanton')->findAll();

        return $this->render('administracionBundle:AdmiCanton:index.html.twig', array(
                                                                                      'item'              => $entityItemMenu,
                                                                                      'canton'            => $entities,
                                                                                      'rolesPermitidos'   => $rolesPermitidos));
    }

    /**
    * @Secure(roles="ROLE_25-6")
    */
    public function showAction($id)
    {
        $peticion = $this->get('request');
        
        $em = $this->getDoctrine()->getManager("telconet_general");
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("25", "1");

        if (null == $canton = $em->find('schemaBundle:AdmiCanton', $id)) {
            throw new NotFoundHttpException('No existe el AdmiCanton que se quiere mostrar');
        }

        return $this->render('administracionBundle:AdmiCanton:show.html.twig', array(
            'item' => $entityItemMenu,
            'canton'   => $canton,
            'flag' =>$peticion->get('flag')
        ));
    }
    
    /**
    * @Secure(roles="ROLE_25-2")
    */
    public function newAction()
    {
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("25", "1");
        $entity = new AdmiCanton();
        $form   = $this->createForm(new AdmiCantonType(), $entity);

        return $this->render('administracionBundle:AdmiCanton:new.html.twig', array(
            'item' => $entityItemMenu,
            'canton' => $entity,
            'form'   => $form->createView()
        ));
    }
    
    /**
    * @Secure(roles="ROLE_25-3")
    */
    public function createAction()
    {
        $request = $this->get('request');
        $em = $this->get('doctrine')->getManager('telconet_general');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("25", "1");
        $entity  = new AdmiCanton();
        $form    = $this->createForm(new AdmiCantonType(), $entity);
        
        
        $entity->setEstado('Activo');
        $entity->setFeCreacion(new \DateTime('now'));
        $entity->setUsrCreacion($request->getSession()->get('user'));
        $entity->setFeUltMod(new \DateTime('now'));
        $entity->setUsrUltMod($request->getSession()->get('user'));
        
//        $form->setData($entity);
        
        $form->bind($request);
        
        if ($form->isValid()) {
            $em->getConnection()->beginTransaction();
            $em->persist($entity);
            $em->flush();
            $em->getConnection()->commit();
            
            return $this->redirect($this->generateUrl('admicanton_show', array('id' => $entity->getId())));
        }
        
        return $this->render('administracionBundle:AdmiCanton:new.html.twig', array(
            'item' => $entityItemMenu,
            'canton' => $entity,
            'form'   => $form->createView()
        ));
        
    }
    
    /**
    * @Secure(roles="ROLE_25-4")
    */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager("telconet_general");
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("25", "1");

        if (null == $canton = $em->find('schemaBundle:AdmiCanton', $id)) {
            throw new NotFoundHttpException('No existe el AdmiCanton que se quiere modificar');
        }

        $formulario =$this->createForm(new AdmiCantonType(), $canton);
//        $formulario->setData($proceso);

        return $this->render('administracionBundle:AdmiCanton:edit.html.twig', array(
            'item' => $entityItemMenu,
			'edit_form' => $formulario->createView(),
			'canton'   => $canton));
    }
    
    /**
    * @Secure(roles="ROLE_25-5")
    */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet_general');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("25", "1");
        $entity = $em->getRepository('schemaBundle:AdmiCanton')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AdmiCanton entity.');
        }

        $editForm   = $this->createForm(new AdmiCantonType(), $entity);

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

            return $this->redirect($this->generateUrl('admicanton_show', array('id' => $id)));
        }

        return $this->render('administracionBundle:AdmiCanton:edit.html.twig',array(
            'item' => $entityItemMenu,
            'canton'      => $entity,
            'edit_form'   => $editForm->createView()
        ));
    }
    
    /**
    * @Secure(roles="ROLE_25-8")
    */
    public function deleteAction($id){
//        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

//        $form->bind($request);
		//$user = $this->get('security.context')->getToken()->getUser();
//        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager('telconet_general');
            $entity = $em->getRepository('schemaBundle:AdmiCanton')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AdmiCanton entity.');
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

        return $this->redirect($this->generateUrl('admicanton'));
    }

    /**
    * @Secure(roles="ROLE_25-9")
    */
    public function deleteAjaxAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $request = $this->get('request');
        $parametro = $request->get('param');
        $em = $this->getDoctrine()->getManager("telconet_general");
        
        $array_valor = explode("|",$parametro);
        foreach($array_valor as $id):
            if (null == $entity = $em->find('schemaBundle:AdmiCanton', $id)) {
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
     * 
     * Llena el grid de consulta.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 07-04-2018 Se agrega la consulta por región del usuario en sesión en caso de que sea necesario filtrar por región
     * 
     */
    /**
    * @Secure(roles="ROLE_25-7")
    */
    public function gridAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
		$queryNombre = $peticion->query->get('query') ? $peticion->query->get('query') : "";
        $nombre = ($queryNombre != '' ? $queryNombre : $peticion->query->get('nombre'));
        $estado = $peticion->query->get('estado');
        
        $parametros = array();
        $parametros["idPais"]=$peticion->query->get('idPais') ? $peticion->query->get('idPais') : "";
        $parametros["idRegion"]=$peticion->query->get('idRegion') ? $peticion->query->get('idRegion') : "";	
        $parametros["idProvincia"]=$peticion->query->get('idProvincia') ? $peticion->query->get('idProvincia') : "";
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $strFiltrarPorRegion = $peticion->get('strFiltrarPorRegion');
        if(isset($strFiltrarPorRegion) && !empty($strFiltrarPorRegion) && $strFiltrarPorRegion === "SI")
        {
            $emGeneral              = $this->getDoctrine()->getManager('telconet_general');
            $emComercial            = $this->getDoctrine()->getManager('telconet');
            $objSession             = $this->get('session');
            $intIdOficina           = $objSession->get('idOficina');
            $strRegion              = "";
            $objInfoOficinaGrupo    = $emComercial->getRepository('schemaBundle:InfoOficinaGrupo')->find($intIdOficina);
            
            if(is_object($objInfoOficinaGrupo))
            {
                $intCantonId     = $objInfoOficinaGrupo->getCantonId();
                if($intCantonId > 0)
                {
                    $objCanton  = $emGeneral->getRepository('schemaBundle:AdmiCanton')->find($intCantonId);
                    if(is_object($objCanton))
                    {
                        $strRegion  = $objCanton->getRegion();
                    }
                }
            }
            $parametros["strRegion"] = $strRegion;
            $start  = '';
            $limit  = '';
        }
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_general")
            ->getRepository('schemaBundle:AdmiCanton')
            ->generarJson($parametros, $nombre,$estado,$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
   
    /**
     * Método encargado de obtener todo los cantones registrado en la base de datos.
     *
     * @version 1.0
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 16-07-2018 - Se modifica el método para aumentar el número de registros para visualizar los cantones.
     *
     * @return json
     */
    public function getCantonesAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_general")
            ->getRepository('schemaBundle:AdmiCanton')
            ->generarJson("","","Activo",$start,400);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /**
    * @Secure(roles="ROLE_25-11")
    */
    public function buscarCantonesAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $idJurisdiccion = $peticion->get('idJurisdiccion');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_general")
            ->getRepository('schemaBundle:AdmiCanton')
            ->generarJsonCantonesPorJurisdiccion($idJurisdiccion,"Activo",$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
	
	/**
    * @Secure(roles="ROLE_25-246")
    */
    public function getPaisesAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $peticion = $this->get('request');
        $nombre = $peticion->get('query');
        
        $start = $peticion->get('start');
        $limit = $peticion->get('limit');
		
        $objJson = $this->getDoctrine()
            ->getManager("telconet_general")
            ->getRepository('schemaBundle:AdmiPais')
            ->generarJson($nombre, "Activo", $start, $limit);
                
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
		
	/**
    * @Secure(roles="ROLE_25-247")
    */
    public function getRegionesAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
		
        $peticion = $this->get('request');
        $nombre = $peticion->get('query');
		
        $parametros = array();
        $parametros["idPais"]=$peticion->query->get('idPais') ? $peticion->query->get('idPais') : "";
		
        $start = $peticion->get('start');
        $limit = $peticion->get('limit');
		
        $objJson = $this->getDoctrine()
            ->getManager("telconet_general")
            ->getRepository('schemaBundle:AdmiRegion')
            ->generarJson($parametros, $nombre, "Activo-Todos", $start, $limit);
                
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
		
	/**
    * @Secure(roles="ROLE_25-248")
    */
    public function getProvinciasAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
		
        $peticion = $this->get('request');
        $nombre = $peticion->get('query');
		
        $parametros = array();
        $parametros["idPais"]=$peticion->query->get('idPais') ? $peticion->query->get('idPais') : "";
        $parametros["idRegion"]=$peticion->query->get('idRegion') ? $peticion->query->get('idRegion') : "";	
		
        $start = $peticion->get('start');
        $limit = $peticion->get('limit');
		
        $objJson = $this->getDoctrine()
            ->getManager("telconet_general")
            ->getRepository('schemaBundle:AdmiProvincia')
            ->generarJson($parametros, $nombre, "Activo-Todos", $start, $limit);
                
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }    
    
    /**
     * ajaxBuscarCantonesPorProvinciaAction
     *
     * Metodo encargado de obtener los cantones de acuerdo a la provincia           
     *
     * @return json
     *
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 13-02-2015
     */ 
    public function ajaxBuscarCantonesPorProvinciaAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $peticion = $this->get('request');       

        $idProvincia = $peticion->get('idProvincia');       

        $objJson = $this->getDoctrine()
            ->getManager("telconet_general")
            ->getRepository('schemaBundle:AdmiCanton')
            ->getCantonesPorProvincia($idProvincia,'Activo');
        $respuesta->setContent($objJson);

        return $respuesta;
    }

}