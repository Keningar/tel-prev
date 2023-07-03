<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiTareaMaterial;
use telconet\schemaBundle\Form\AdmiTareaMaterialType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

class AdmiTareaMaterialController extends Controller implements TokenAuthenticatedController
{ 
    /**
    * @Secure(roles="ROLE_54-1")
    */
    public function indexAction()
    {
	$rolesPermitidos = array();
	if (true === $this->get('security.context')->isGranted('ROLE_54-2'))
	{
	    $rolesPermitidos[] = 'ROLE_54-2';
	}
	if (true === $this->get('security.context')->isGranted('ROLE_54-4'))
	{
	    $rolesPermitidos[] = 'ROLE_54-4';
	}
	if (true === $this->get('security.context')->isGranted('ROLE_54-6'))
	{
	    $rolesPermitidos[] = 'ROLE_54-6';
	}
	if (true === $this->get('security.context')->isGranted('ROLE_54-8'))
	{
	    $rolesPermitidos[] = 'ROLE_54-8';
	}
	if (true === $this->get('security.context')->isGranted('ROLE_54-9'))
	{
	    $rolesPermitidos[] = 'ROLE_54-9';
	}
	
        $em = $this->getDoctrine()->getManager('telconet_soporte');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("54", "1");

        //$entities = $em->getRepository('schemaBundle:AdmiTareaMaterial')->findAll();

        return $this->render('administracionBundle:AdmiTareaMaterial:index.html.twig', array(
            'item' => $entityItemMenu,
            'rolesPermitidos'=>$rolesPermitidos
        ));
    }
    
    /**
    * @Secure(roles="ROLE_54-6")
    */
    public function showAction($id)
    {
        $peticion = $this->get('request');
        $tareaId = ($peticion->query->get('tareaId'))?$peticion->query->get('tareaId'):"";
		
        $em = $this->getDoctrine()->getManager("telconet_soporte");
        $em_naf = $this->getDoctrine()->getManager("telconet_naf");
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("54", "1");
 
        if (null == $tarea = $em->find('schemaBundle:AdmiTareaMaterial', $id)) {
            throw new NotFoundHttpException('No existe la Tarea Material que se quiere mostrar');
        }
        
        $session = $peticion->getSession();
        $codEmpresa = ($session->get('idEmpresa') ? $session->get('idEmpresa') : "");   
        $prefijoEmpresa = ($session->get('prefijoEmpresa') ? $session->get('prefijoEmpresa') : "");  
		
        $nombreMaterial = "";
        if($tarea->getMaterialCod())
        {    
	    
	    if($prefijoEmpresa == 'MD' ) $codEmpresa = '10';
	    
	    $objMaterial = $em_naf->getRepository('schemaBundle:VArticulosEmpresas')->getOneArticulobyEmpresabyCodigo($codEmpresa, $tarea->getMaterialCod()); 
            //$objMaterial = $em_naf->getRepository('schemaBundle:VArticulosEmpresas')->findOneById($tarea->getMaterialCod());
            $nombreMaterial = $objMaterial ? $objMaterial->getDescripcion() : "";
            
        }
        
        return $this->render('administracionBundle:AdmiTareaMaterial:show.html.twig', array(
            'item' => $entityItemMenu,
            'tareaId' => $tareaId,
            'tareamaterial'   => $tarea,
            'nombreMaterial'   => $nombreMaterial,
            'flag' =>$peticion->get('flag')
        ));
    }
    
    /**
    * @Secure(roles="ROLE_54-2")
    */
    public function newAction()
    {     
	$peticion = $this->get('request');
	$tareaId = $peticion->query->get('tareaId');
		
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("54", "1");  
		$entity = new AdmiTareaMaterial();
		
		if($tareaId){	
			$emSoporte = $this->getDoctrine()->getManager('telconet_soporte');
			$tarea = $emSoporte->getRepository('schemaBundle:AdmiTarea')->findOneById($tareaId);  
			$entity->setTareaId($tarea);
		}else{
			$tareaId = "";
		}
	
	$session = $peticion->getSession();
	
	$codEmpresa = $session->get('idEmpresa');	
		
        $form   = $this->createForm(new AdmiTareaMaterialType(
			    array('codEmpresa'=>$codEmpresa)), $entity);

        return $this->render('administracionBundle:AdmiTareaMaterial:new.html.twig', array(
            'item' => $entityItemMenu,
            'tareaId' => $tareaId,
            'tareamaterial' => $entity,
            'form'   => $form->createView()
        ));
    }
    
    /**
    * @Secure(roles="ROLE_54-3")
    */
    public function createAction()
    {        
        $request = $this->get('request');
        $em = $this->get('doctrine')->getManager('telconet_soporte');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("54", "1");
		
	$peticion = $this->get('request');
        $session = $peticion->getSession();
	$codEmpresa = ($session->get('idEmpresa') ? $session->get('idEmpresa') : ""); 
	
	$entity  = new AdmiTareaMaterial();
        $form    = $this->createForm(new AdmiTareaMaterialType(array('codEmpresa'=>$codEmpresa)), $entity);
        $form->bind($request);
               
	$tareaId = ($peticion->get('tareaId'))?$peticion->get('tareaId'):"";
		
        if ($form->isValid()) {
            $em->getConnection()->beginTransaction();
                    
            $entity->setEmpresaCod($codEmpresa);
            $entity->setEstado('Activo');
            $entity->setFeCreacion(new \DateTime('now'));
            $entity->setUsrCreacion($request->getSession()->get('user'));
            $entity->setFeUltMod(new \DateTime('now'));
            $entity->setUsrUltMod($request->getSession()->get('user'));
        
            $escogido_material = $peticion->get('escogido_material');            
            $entity->setMaterialCod($escogido_material);
            
            $em->persist($entity);
            $em->flush();
            $em->getConnection()->commit();
            
            return $this->redirect($this->generateUrl('admitareamaterial_show', array('id' => $entity->getId(),'tareaId' => $tareaId,)));
        }
        //exit;
        
        return $this->render('administracionBundle:AdmiTareaMaterial:new.html.twig', array(
            'item' => $entityItemMenu,
	    'tareaId' => $tareaId,
            'tareamaterial' => $entity,
            'form'   => $form->createView()
        ));
        
    }
    
    /**
    * @Secure(roles="ROLE_54-4")
    */
    public function editAction($id)
    {
		$peticion = $this->get('request');
		$tareaId = ($peticion->query->get('tareaId'))?$peticion->query->get('tareaId'):"";
		
        $em = $this->getDoctrine()->getManager("telconet_soporte");
        $em_naf = $this->getDoctrine()->getManager("telconet_naf");
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("54", "1");

        if (null == $tarea = $em->find('schemaBundle:AdmiTareaMaterial', $id)) {
            throw new NotFoundHttpException('No existe la Tarea Material que se quiere modificar');
        }
		
        $session = $peticion->getSession();
        
        $codEmpresa = ($session->get('idEmpresa') ? $session->get('idEmpresa') : "");   
        
        $prefijoEmpresa = ($session->get('prefijoEmpresa') ? $session->get('prefijoEmpresa') : "");  
        
        if($prefijoEmpresa == 'MD' ) $empresa = '10';
        else $empresa = $codEmpresa;
        
	$material = $em_naf->getRepository('schemaBundle:VArticulosEmpresas')->getOneArticulobyEmpresabyCodigo($empresa, $tarea->getMaterialCod()); 
		//$material = $em_naf->getRepository('schemaBundle:VArticulosEmpresas')->findOneById($tarea->getMaterialCod());
        $idValueMaterial = $material->getId() . "@@" . $material->getCostoUnitario();
        
        $formulario =$this->createForm(new AdmiTareaMaterialType(array('codEmpresa'=>$codEmpresa)), $tarea);
//        $formulario->setData($tarea);

        return $this->render('administracionBundle:AdmiTareaMaterial:edit.html.twig', array(
            'item' => $entityItemMenu,
            'tareaId' => $tareaId,
	    'edit_form' => $formulario->createView(),
	    'tareamaterial'   => $tarea,
	    'idValueMaterial' => $idValueMaterial));
    }
    
    /**
     * @Secure(roles="ROLE_54-5")
     * 
     * Llena el grid de consulta.
     * 
     * @version 1.0 Version Inicial
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 19-02-2016 - Se corrige que al haber un error en actualizar se envíen los parámetros adecuados para mostrar la
     *                           pantalla de edición inicial.
     * 
     */
    public function updateAction($id)
    {        
        $emSoporte            = $this->getDoctrine()->getManager('telconet_soporte');
        $emNaf                = $this->getDoctrine()->getManager("telconet_naf");
        $emSeguridad          = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu       = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("54", "1");        
        $objAdmiTareaMaterial = $emSoporte->getRepository('schemaBundle:AdmiTareaMaterial')->find($id);
        $objRequest           = $this->get('request');
        $objSession           = $objRequest->getSession();
        $strCodEmpresa        = ($objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : ""); 
        $strPrefijoEmpresa    = ($objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa') : "");  
        
        if (!$objAdmiTareaMaterial)
        {
            throw $this->createNotFoundException('No se encontro el registro en nuestra base de datos.');
        }

        
        $editForm   = $this->createForm(new AdmiTareaMaterialType(array('codEmpresa'=>$strCodEmpresa)), $objAdmiTareaMaterial);
        $editForm->bind($objRequest);
		
        if ($editForm->isValid()) 
        {
            $objAdmiTareaMaterial->setEmpresaCod($strCodEmpresa);
            $objAdmiTareaMaterial->setFeUltMod(new \DateTime('now'));
            $objAdmiTareaMaterial->setUsrUltMod($objSession->get('user'));
            
            $strEscogidoMaterial = $objRequest->get('escogido_material');            
            $objAdmiTareaMaterial->setMaterialCod($strEscogidoMaterial);
            
            $emSoporte->persist($objAdmiTareaMaterial);
            $emSoporte->flush();

            return $this->redirect($this->generateUrl('admitareamaterial_show', array( 'id'      => $id,
                                                                                       'tareaId' => $objAdmiTareaMaterial->getTareaId()->getId() ) ));
        }
        
        
        if($strPrefijoEmpresa == 'MD' )
        {
            $strEmpresa = '10';
        }
        else
        {
            $strEmpresa = $strCodEmpresa;
        }
        
        $objMaterial = $emNaf->getRepository('schemaBundle:VArticulosEmpresas')
                             ->getOneArticulobyEmpresabyCodigo($strEmpresa, $objAdmiTareaMaterial->getMaterialCod()); 
        
        if( $objMaterial )
        {
            $strIdValueMaterial = $objMaterial->getId()."@@".$objMaterial->getCostoUnitario();
        }
        

        return $this->render( 'administracionBundle:AdmiTareaMaterial:edit.html.twig',
                              array(
                                        'item'            => $entityItemMenu,
                                        'tareamaterial'   => $objAdmiTareaMaterial,
                                        'tareaId'         => $objAdmiTareaMaterial->getTareaId()->getId(),
                                        'edit_form'       => $editForm->createView(),
                                        'idValueMaterial' => $strIdValueMaterial
                                    ) );
    }
    
    /**
    * @Secure(roles="ROLE_54-8")
    */
    public function deleteAction($id){
//        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

//        $form->bind($request);
		//$user = $this->get('security.context')->getToken()->getUser();
//        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager('telconet_soporte');
            $entity = $em->getRepository('schemaBundle:AdmiTareaMaterial')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AdmiTareaMaterial entity.');
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

        return $this->redirect($this->generateUrl('admitareamaterial'));
    }

    /**
    * @Secure(roles="ROLE_54-9")
    */
    public function deleteAjaxAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $request = $this->get('request');
        $parametro = $request->get('param');
        $em = $this->getDoctrine()->getManager("telconet_soporte");
        
        $array_valor = explode("|",$parametro);
        foreach($array_valor as $id):
            if (null == $entity = $em->find('schemaBundle:AdmiTareaMaterial', $id)) {
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
    
    public function retornaArrayRoles()
    {
        $em_general = $this->getDoctrine()->getManager("telconet_general");
        $em = $this->getDoctrine()->getManager();
        $Roles = $em_general->getRepository('schemaBundle:AdmiRol')->findByEstado("Activo");
        $arrayRoles = false;
        if($Roles && count($Roles)>0)
        {
            foreach($Roles as $key => $valueRol)
            {
                $arrayRol["id"] = $valueRol->getId();
                $arrayRol["descripcion"] = $valueRol->getDescripcionRol();
                $arrayRoles[] = $arrayRol;
            }
        }
        return $arrayRoles;
    }
    
    /**
     * @Secure(roles="ROLE_54-7")
     * 
     * Llena el grid de consulta.
     * 
     * @version 1.0 Version Inicial
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 17-02-2016 - Se corrige que muestre los materiales asociados a tareas de la empresa en sessión.
     */
    public function gridAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        $idTarea = $peticion->query->get('idTarea');
        $session = $peticion->getSession();
        $em = $this->getDoctrine()->getManager();
        
		$queryNombre = $peticion->query->get('query') ? $peticion->query->get('query') : "";
        $nombre = ($queryNombre != '' ? $queryNombre : $peticion->query->get('nombre'));
        $estado = $peticion->query->get('estado');
        $codEmpresa = ($session->get('idEmpresa') ? $session->get('idEmpresa') : "");   
	$prefijoEmpresa = $peticion->getSession()->get('prefijoEmpresa');
        
        $codEmpresaNaf = $codEmpresa;
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $em_naf = $this->getDoctrine()->getManager("telconet_naf");
        ($estado=="Todos")?$estado="Activo":$estado;
        
		if($idTarea){
			$objJson = $this->getDoctrine()
						->getManager("telconet_soporte")
						->getRepository('schemaBundle:AdmiTareaMaterial')
						->generarJsonByTarea($em_naf, $codEmpresaNaf, $idTarea, $estado, $start, $limit,$prefijoEmpresa);

		}else{
			$objJson = $this->getDoctrine()
						->getManager("telconet_soporte")
						->getRepository('schemaBundle:AdmiTareaMaterial')
						->generarJson($em_naf, $codEmpresaNaf, $nombre, $estado, $start, $limit,$prefijoEmpresa);

		}	
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /**
    * @Secure(roles="ROLE_54-23")
    */
    public function getListadoMaterialesAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
	$peticion = $this->get('request');

	$em = $this->getDoctrine()->getManager();
        $em_naf = $this->getDoctrine()->getManager("telconet_naf");
        
	$codEmpresa = ($peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa') : "");
        $prefijoEmpresa = $peticion->getSession()->get('prefijoEmpresa');
        
        $codEmpresaNaf = $codEmpresa;
        
        if($prefijoEmpresa=="MD"){
	    $empresaTN = $em->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneByPrefijo("TN");
	    $codEmpresaNaf = $empresaTN->getId();
        }
        
        $materiales = $em_naf->getRepository('schemaBundle:VArticulosEmpresas')->getArticulosbyEmpresa($codEmpresaNaf);


        if($materiales && count($materiales)>0)
        {
            $num = count($materiales);
            
            $arr_encontrados[]=array('id_material' =>"0@@0", 'nombre_material' =>"Seleccione un material");
            foreach($materiales as $key => $material)
            {                
                $arr_encontrados[]=array('id_material' =>$material->getId()."@@".$material->getCostoUnitario(),
                                         'nombre_material' =>trim($material->getDescripcion()));
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_material' => "0@@0" , 'nombre_material' => 'Ninguno','modulo_id' => 0 , 'modulo_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
                $objJson = json_encode( $resultado);
            }
            else
            {
                $data=json_encode($arr_encontrados);
                $objJson= '{"total":"'.$num.'","encontrados":'.$data.'}';
            }
        }
        else
        {
            $objJson= '{"total":"0","encontrados":[]}';
        }
        
        $respuesta->setContent($objJson);
        return $respuesta;
    }

}