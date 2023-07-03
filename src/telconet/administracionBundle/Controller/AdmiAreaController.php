<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\schemaBundle\Entity\AdmiArea;
use telconet\schemaBundle\Form\AdmiAreaType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response; 
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\JsonResponse;

class AdmiAreaController extends Controller implements TokenAuthenticatedController
{ 
    /**
    * @Secure(roles="ROLE_46-1")
    */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager('telconet_general');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("46", "1");
			
        $entities = $em->getRepository('schemaBundle:AdmiArea')->findAll();
				
        return $this->render('administracionBundle:AdmiArea:index.html.twig', array(
            'item' => $entityItemMenu,
            'area' => $entities
        ));
    }
    
    /**
    * @Secure(roles="ROLE_46-6")
    */
    public function showAction($id)
    {
        $peticion = $this->get('request');
        
        $em = $this->getDoctrine()->getManager("telconet_general");
        $emComercial = $this->getDoctrine()->getManager("telconet");
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("46", "1");

        if (null == $area = $em->find('schemaBundle:AdmiArea', $id)) {
            throw new NotFoundHttpException('No existe el AdmiArea que se quiere mostrar');
        }

		$EmpresaGrupo = "";
		if($area && $area->getEmpresaCod())
		{
			$InfoEmpresaGrupo = $emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneById($area->getEmpresaCod());
			$EmpresaGrupo = ($InfoEmpresaGrupo ? ($InfoEmpresaGrupo->getNombreEmpresa() ? $InfoEmpresaGrupo->getNombreEmpresa()  : '') : '');
		}	
		
		
        return $this->render('administracionBundle:AdmiArea:show.html.twig', array(
            'item' => $entityItemMenu,
            'area'   => $area,
			'EmpresaGrupo' => $EmpresaGrupo,
            'flag' =>$peticion->get('flag')
        ));
    }
    
    /**
    * @Secure(roles="ROLE_46-2")
    */
    public function newAction()
    {
        $arrayEmpresas =  $this->retornaArrayEmpresas();
		
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("46", "1");
        $entity = new AdmiArea();
        $form   = $this->createForm(new AdmiAreaType(array('arrayEmpresas'=>$arrayEmpresas)), $entity);

        return $this->render('administracionBundle:AdmiArea:new.html.twig', array(
            'item' => $entityItemMenu,
            'area' => $entity,
            'form'   => $form->createView()
        ));
    }
    
    /**
    * @Secure(roles="ROLE_46-3")
    */
    public function createAction()
    {
        $arrayEmpresas =  $this->retornaArrayEmpresas();
		
        $request = $this->get('request');
        $em = $this->get('doctrine')->getManager('telconet_general');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("46", "1");
        $entity  = new AdmiArea();
        $form    = $this->createForm(new AdmiAreaType(array('arrayEmpresas'=>$arrayEmpresas)), $entity);
        
        
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
            
            return $this->redirect($this->generateUrl('admiarea_show', array('id' => $entity->getId())));
        }
        
        return $this->render('administracionBundle:AdmiArea:new.html.twig', array(
            'item' => $entityItemMenu,
            'area' => $entity,
            'form'   => $form->createView()
        ));
        
    }
    
    /**
    * @Secure(roles="ROLE_46-4")
    */
    public function editAction($id)
    {
        $arrayEmpresas =  $this->retornaArrayEmpresas();
		
        $em = $this->getDoctrine()->getManager("telconet_general");
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("46", "1");

        if (null == $area = $em->find('schemaBundle:AdmiArea', $id)) {
            throw new NotFoundHttpException('No existe el AdmiArea que se quiere modificar');
        }

        $formulario =$this->createForm(new AdmiAreaType(array('arrayEmpresas'=>$arrayEmpresas)), $area);
//        $formulario->setData($proceso);

        return $this->render('administracionBundle:AdmiArea:edit.html.twig', array(
            'item' => $entityItemMenu,
			'edit_form' => $formulario->createView(),
			'area'   => $area));
    }
    
    /**
    * @Secure(roles="ROLE_46-5")
    */
    public function updateAction($id)
    {
        $arrayEmpresas =  $this->retornaArrayEmpresas();
		
        $em = $this->getDoctrine()->getManager('telconet_general');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("46", "1");
        $entity = $em->getRepository('schemaBundle:AdmiArea')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AdmiArea entity.');
        }

        $editForm   = $this->createForm(new AdmiAreaType(array('arrayEmpresas'=>$arrayEmpresas)), $entity);

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

            return $this->redirect($this->generateUrl('admiarea_show', array('id' => $id)));
        }

        return $this->render('administracionBundle:AdmiArea:edit.html.twig',array(
            'item' => $entityItemMenu,
            'area'      => $entity,
            'edit_form'   => $editForm->createView()
        ));
    }
    
    /**
    * @Secure(roles="ROLE_46-8")
    */
    public function deleteAction($id){
//        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

//        $form->bind($request);
		//$user = $this->get('security.context')->getToken()->getUser();
//        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager('telconet_general');
            $entity = $em->getRepository('schemaBundle:AdmiArea')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AdmiArea entity.');
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

        return $this->redirect($this->generateUrl('admiarea'));
    }

    /**
    * @Secure(roles="ROLE_46-9")
    */
    public function deleteAjaxAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $request = $this->get('request');
        $parametro = $request->get('param');
        $em = $this->getDoctrine()->getManager("telconet_general");
        
        $array_valor = explode("|",$parametro);
        foreach($array_valor as $id):
            if (null == $entity = $em->find('schemaBundle:AdmiArea', $id)) {
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
    * @Secure(roles="ROLE_46-7")
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
            ->getRepository('schemaBundle:AdmiArea')
            ->generarJson($nombre,$estado,$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
	
    public function retornaArrayEmpresas()
    {
        $em = $this->getDoctrine()->getManager();
        $empresas = $em->getRepository('schemaBundle:InfoEmpresaGrupo')->getRegistros('', 'Activo', 0,'');//->findByEstado("Activo");
        $arrayEmpresas = false;
        if($empresas && count($empresas)>0)
        {
            foreach($empresas as $key => $valueEmpresa)
            {
                $arrayEmpresa["id"] = $valueEmpresa->getId();
                $arrayEmpresa["nombre"] = $valueEmpresa->getNombreEmpresa();
                $arrayEmpresas[] = $arrayEmpresa;
            }
        }
        return $arrayEmpresas;
    }   
    /**
     * getAreaByEmpresaAction, Obtiene las areas por empresa.
     * @param  
     * @return type array $objRespuesta
     * @author Sofía Fernández <sfernandez@telconet.ec>  
     * @version 1.0 21-12-2017
     */
    public function getAreaByEmpresaAction()
    {
        $emGeneral       = $this->getDoctrine()->getManager('telconet_general');
        $arrayParametros = array();
        $objPeticion     = $this->get('request');
        $intStart        = $objPeticion->query->get('start');
        $intLimit        = $objPeticion->query->get('limit');
        $strIdEmpresa    = $objPeticion->getSession()->get('idEmpresa');
        $strEstado       = 'Activo';        
        $arrayParametros['strEstado']    = $strEstado;
        $arrayParametros['strIdEmpresa'] = $strIdEmpresa;
        $arrayParametros['intStart']     = $intStart;
        $arrayParametros['intLimit']     = $intLimit;        
        $arrayAreas  = $emGeneral->getRepository('schemaBundle:AdmiArea')->getEncontradosAreaByEmpresaJson($arrayParametros);
        return $arrayAreas;
    }
}