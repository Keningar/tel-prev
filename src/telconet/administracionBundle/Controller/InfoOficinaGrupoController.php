<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoOficinaGrupo;
use telconet\schemaBundle\Form\InfoOficinaGrupoType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

class InfoOficinaGrupoController extends Controller implements TokenAuthenticatedController
{ 
    /**
    * @Secure(roles="ROLE_39-1")
    */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager('telconet');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("39", "1");

        $entities = $em->getRepository('schemaBundle:InfoOficinaGrupo')->findAll();

        return $this->render('administracionBundle:InfoOficinaGrupo:index.html.twig', array(
            'item' => $entityItemMenu,
            'infooficinagrupo' => $entities
        ));
    }
    
    /**
    * @Secure(roles="ROLE_39-6")
    */
    public function showAction($id)
    {
        $peticion = $this->get('request');
        
        $em = $this->getDoctrine()->getManager("telconet");
        $em_general= $this->getDoctrine()->getManager("telconet_general");
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("39", "1");
   
        if (null == $infooficinagrupo = $em->find('schemaBundle:InfoOficinaGrupo', $id)) {
            throw new NotFoundHttpException('No existe el InfoOficinaGrupo que se quiere mostrar');
        }
        
        $nombreCanton = "";
        if($infooficinagrupo->getCantonId())
        {    
            $objCanton = $em_general->getRepository('schemaBundle:AdmiCanton')->findOneById($infooficinagrupo->getCantonId());
            $nombreCanton = $objCanton ? $objCanton->getNombreCanton() : "";
        }   

        return $this->render('administracionBundle:InfoOficinaGrupo:show.html.twig', array(
            'item' => $entityItemMenu,
            'infooficinagrupo'   => $infooficinagrupo,
            'nombreCanton'   => $nombreCanton,
            'flag' =>$peticion->get('flag')
        ));
    }
    
    /**
     * @Secure(roles="ROLE_39-2")
     * 
     * Documentación para el método 'createAction'.
     * 
     * Método que renderiza la vista para crear una nueva Oficina Grupo.
     * 
     * @return Render Pantalla para agregar nueva Oficina Grupo.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 11-12-2015
     * @since 1.0
     * Se agregó el campo "Número Estab. Sri:" y es obligatorio en el caso [Es Oficina Facturacion = Si].
     */
    public function newAction()
    {
        $arrayCantones =  $this->retornaArrayCantones();
        
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("39", "1");
        $entity = new InfoOficinaGrupo();
        $intIdEmpresa  = $this->get('request')->getSession()->get('idEmpresa');
        $arrayOpciones = array('arrayCantones' => $arrayCantones, 'idEmpresa' => $intIdEmpresa, 'esOfiFact' => 'N');
        $formulario    = $this->createForm(new InfoOficinaGrupoType($arrayOpciones), $entity);        

        return $this->render('administracionBundle:InfoOficinaGrupo:new.html.twig', array(
            'item' => $entityItemMenu,
            'infooficinagrupo' => $entity,
            'form'   => $formulario->createView()
        ));
    }

    /**
     * @Secure(roles="ROLE_39-3")
     * 
     * Documentación para el método 'createAction'.
     * 
     * Método para crear una nueva Oficina Grupo.
     * 
     * @return Render [Show] en guardado exitoso y [New] si el formulario no es válido.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 11-12-2015
     * @since 1.0
     * Se agregó el campo "Número Estab. Sri:" y es obligatorio en el caso [Es Oficina Facturacion = Si].
     */
    public function createAction()
    {
        $arrayCantones =  $this->retornaArrayCantones();
        
        $request = $this->get('request');
        $em = $this->get('doctrine')->getManager('telconet');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("39", "1");
        $entity  = new InfoOficinaGrupo();
        $intIdEmpresa  = $this->get('request')->getSession()->get('idEmpresa');
        $arrayOpciones = array('arrayCantones' => $arrayCantones, 'idEmpresa' => $intIdEmpresa, 'esOfiFact' => 'N');
        $formulario    = $this->createForm(new InfoOficinaGrupoType($arrayOpciones), $entity);        
        $formulario->bind($request);
        
        if ($formulario->isValid()) {
            $em->getConnection()->beginTransaction();
            
            $entity->setEstado('Activo');
            $entity->setFeCreacion(new \DateTime('now'));
            $entity->setUsrCreacion($request->getSession()->get('user'));
            $entity->setIpCreacion($request->getClientIp());
            $em->persist($entity);
            $em->flush();
            
            $em->getConnection()->commit();
            
            return $this->redirect($this->generateUrl('infooficinagrupo_show', array('id' => $entity->getId())));
        }
        
        return $this->render('administracionBundle:InfoOficinaGrupo:new.html.twig', array(
            'item' => $entityItemMenu,
            'infooficinagrupo' => $entity,
            'form'   => $formulario->createView()
        ));
        
    }

    /**
     * @Secure(roles="ROLE_39-4")
     * 
     * Documentación para el método 'editAction'.
     * 
     * Método que renderiza la vista para editar una Oficina Grupo.
     * 
     * @return Response retorna el resultado de la operación
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 11-12-2015
     * @since 1.0
     * Se agregó el campo "Número Estab. Sri:" y es obligatorio en el caso [Es Oficina Facturacion = Si].
     */
    public function editAction($id)
    {
        $arrayCantones =  $this->retornaArrayCantones();
        
        $em = $this->getDoctrine()->getManager("telconet");
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("39", "1");

        if (null == $infoOficinaGrupo = $em->find('schemaBundle:InfoOficinaGrupo', $id)) {
            throw new NotFoundHttpException('No existe el InfoOficinaGrupo que se quiere modificar');
        }
        $intIdEmpresa  = $infoOficinaGrupo->getEmpresaId()->getId();
        $arrayOpciones = array('arrayCantones' => $arrayCantones, 
                               'idEmpresa'     => $intIdEmpresa, 
                               'esOfiFact'     => $infoOficinaGrupo->getEsOficinaFacturacion());
        $formulario    = $this->createForm(new InfoOficinaGrupoType($arrayOpciones), $infoOficinaGrupo);
        return $this->render('administracionBundle:InfoOficinaGrupo:edit.html.twig', array(
            'item' => $entityItemMenu,
			'edit_form' => $formulario->createView(),
			'infooficinagrupo'   => $infoOficinaGrupo));
    }
    
    /**
     * @Secure(roles="ROLE_39-5")
     * 
     * Documentación para el método 'updateAction'.
     * 
     * Método para actualizar una Oficina Grupo.
     * 
     * @return Render [Show] en guardado exitoso y [New] si el formulario no es válido.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 11-12-2015
     * @since 1.0
     * Se agregó el campo "Número Estab. Sri:" y es obligatorio en el caso [Es Oficina Facturacion = Si].
     */
    public function updateAction($id)
    {
        $arrayCantones =  $this->retornaArrayCantones();
       
        $em = $this->getDoctrine()->getManager('telconet');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("39", "1");
        $infoOficinaGrupo = $em->getRepository('schemaBundle:InfoOficinaGrupo')->find($id);

        if (!$infoOficinaGrupo) {
            throw $this->createNotFoundException('Unable to find InfoOficinaGrupo entity.');
        }
        $intIdEmpresa  = $infoOficinaGrupo->getEmpresaId()->getId();
        $arrayOpciones = array('arrayCantones' => $arrayCantones, 
                               'idEmpresa'     => $intIdEmpresa, 
                               'esOfiFact'     => $infoOficinaGrupo->getEsOficinaFacturacion());
        $editForm      = $this->createForm(new InfoOficinaGrupoType($arrayOpciones), $infoOficinaGrupo);
        
        $request = $this->getRequest();
        $editForm->bind($request);
        if ($editForm->isValid()) {
			
            $em->persist($infoOficinaGrupo);
            $em->flush();

            return $this->redirect($this->generateUrl('infooficinagrupo_show', array('id' => $id)));
        }

        return $this->render('administracionBundle:InfoOficinaGrupo:edit.html.twig',array(
            'item' => $entityItemMenu,
            'infooficinagrupo'      => $infoOficinaGrupo,
            'edit_form'   => $editForm->createView()
        ));
    }
    
    /**
    * @Secure(roles="ROLE_39-8")
    */
    public function deleteAction($id){
//        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

//        $form->bind($request);
		//$user = $this->get('security.context')->getToken()->getUser();
//        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager('telconet');
            $entity = $em->getRepository('schemaBundle:InfoOficinaGrupo')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find InfoOficinaGrupo entity.');
            }
            $estado = 'Eliminado';
            $entity->setEstado($estado);
			
			$em->persist($entity);
            //$em->remove($entity);
            $em->flush();
//        }

        return $this->redirect($this->generateUrl('infooficinagrupo'));
    }

    /**
    * @Secure(roles="ROLE_39-9")
    */
    public function deleteAjaxAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $peticion = $this->get('request');
        $parametro = $peticion->get('param');
        $em = $this->getDoctrine()->getManager("telconet");
        
        $array_valor = explode("|",$parametro);
        foreach($array_valor as $id):
            if (null == $entity = $em->find('schemaBundle:InfoOficinaGrupo', $id)) {
                $respuesta->setContent("No existe la entidad");
            }
            else{
				if(strtolower($entity->getEstado()) != "eliminado")
				{
					$entity->setEstado("Eliminado");
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
    * @Secure(roles="ROLE_39-7")
    */
    public function gridAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $em = $this->getDoctrine()->getManager("telconet");
        
        $peticion = $this->get('request');
         $session  = $peticion->getSession();   
        $codEmpresa = ($session->get('idEmpresa') ? $session->get('idEmpresa') : "");
        
	$queryNombre = $peticion->query->get('query') ? $peticion->query->get('query') : "";
        $nombre = ($queryNombre != '' ? $queryNombre : $peticion->query->get('nombre'));
        $estado = $peticion->query->get('estado');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $em_general= $this->getDoctrine()->getManager("telconet_general");
        
	$paramEmpresa = $peticion->query->get('id_param') ? $peticion->query->get('id_param') : "";
	
	if($paramEmpresa!=""){
	      
	      $empresa = $em->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneBy(array('prefijo'=>$paramEmpresa));
	      if($empresa)$codEmpresa = $empresa->getId();
	}
	
	//echo $nombre.' '.$estado.' '.$start.' '.$limit.' '.$codEmpresa;
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet")
            ->getRepository('schemaBundle:InfoOficinaGrupo')
            ->generarJson($em_general, $nombre,$estado,$start,$limit,$codEmpresa);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    
    public function retornaArrayCantones()
    {
        $em_general= $this->getDoctrine()->getManager("telconet_general");
        $em = $this->getDoctrine()->getManager();
        $cantones = $em_general->getRepository('schemaBundle:AdmiCanton')->getRegistros('', '', 'Activo',0,'');//->findByEstado("Activo");
        $arrayCantones = false;
        if($cantones && count($cantones)>0)
        {
            foreach($cantones as $key => $valueCanton)
            {
                $arrayCanton["id"] = $valueCanton->getId();
                $arrayCanton["nombre"] = $valueCanton->getNombreCanton();
                $arrayCantones[] = $arrayCanton;
            }
        }
        return $arrayCantones;
    }

    /**
     * El metodo getOficinasByPrefijoEmpresa retorna las oficinas por empresa en formato json
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 12-11-2014
     * @param  string   $strPrefijoEmpresa  Recibe el prefijo de la empresa
     * @return json     $jsonOficinas       Retorna el json de las oficinas
     */
    public function getOficinasByPrefijoEmpresaJsonAction()
    {
        $objRequest         = $this->get('request');
        $objSession         = $objRequest->getSession();
        $strPrefijoEmpresa  = $objSession->get('prefijoEmpresa');
        $em                 = $this->getDoctrine()->getManager();
        $jsonOficinas       = $em->getRepository('schemaBundle:InfoOficinaGrupo')->getOficinasByPrefijoEmpresaJson($strPrefijoEmpresa, "", "");
        $objRespuesta       = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $objRespuesta->setContent($jsonOficinas);
        return $objRespuesta;
    }//getOficinasByPrefijoEmpresaJsonAction
    
    /**
     * El metodo getOficinasPrincipalesByPrefijoEmpresaJsonAction 
     * retorna las oficinas principales por empresa en formato json
     * @author Andres Montero <amontero@telconet.ec>
     * @version 1.0 01-06-2016
     * @return json     $jsonOficinas       Retorna el json de las oficinas
     */
    public function getOficinasPrincipalesByPrefijoEmpresaJsonAction()
    {
        $objRequest         = $this->get('request');
        $objSession         = $objRequest->getSession();
        $strPrefijoEmpresa  = $objSession->get('prefijoEmpresa');
        $em                 = $this->getDoctrine()->getManager("telconet");
        $jsonOficinas       = $em->getRepository('schemaBundle:InfoOficinaGrupo')->getOficinasPrincipalesByPrefijoEmpresaJson($strPrefijoEmpresa);
        $objRespuesta       = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $objRespuesta->setContent($jsonOficinas);
        return $objRespuesta;
    }//getOficinasByPrefijoEmpresaJsonAction    
}