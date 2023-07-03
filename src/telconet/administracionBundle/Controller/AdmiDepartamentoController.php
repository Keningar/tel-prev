<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiDepartamento;
use telconet\schemaBundle\Form\AdmiDepartamentoType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response; 
use JMS\SecurityExtraBundle\Annotation\Secure;

class AdmiDepartamentoController extends Controller implements TokenAuthenticatedController
{ 
    /**
    * @Secure(roles="ROLE_47-1")
    */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager('telconet_general');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("47", "1");

        $entities = $em->getRepository('schemaBundle:AdmiDepartamento')->findAll();

        return $this->render('administracionBundle:AdmiDepartamento:index.html.twig', array(
            'item' => $entityItemMenu,
            'departamento' => $entities
        ));
    }
    
    /**
    * @Secure(roles="ROLE_47-6")
    */
    public function showAction($id)
    {
        $peticion = $this->get('request');
        
        $em = $this->getDoctrine()->getManager("telconet_general");
        $emComercial = $this->getDoctrine()->getManager("telconet");
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("47", "1");

        if (null == $departamento = $em->find('schemaBundle:AdmiDepartamento', $id)) {
            throw new NotFoundHttpException('No existe el AdmiDepartamento que se quiere mostrar');
        }
		
		$EmpresaGrupo = "";
		if($departamento && $departamento->getEmpresaCod())
		{
			$InfoEmpresaGrupo = $emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneById($departamento->getEmpresaCod());
			$EmpresaGrupo = ($InfoEmpresaGrupo ? ($InfoEmpresaGrupo->getNombreEmpresa() ? $InfoEmpresaGrupo->getNombreEmpresa()  : '') : '');
		}	
		
        return $this->render('administracionBundle:AdmiDepartamento:show.html.twig', array(
            'item' => $entityItemMenu,
            'departamento'   => $departamento,
			'EmpresaGrupo' => $EmpresaGrupo,
            'flag' =>$peticion->get('flag')
        ));
    }
    
    /**
    * @Secure(roles="ROLE_47-2")
    */
    public function newAction()
    {
        $arrayEmpresas =  $this->retornaArrayEmpresas();
		
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("47", "1");
        $entity = new AdmiDepartamento();
        $form   = $this->createForm(new AdmiDepartamentoType(array('arrayEmpresas'=>$arrayEmpresas)), $entity);

        return $this->render('administracionBundle:AdmiDepartamento:new.html.twig', array(
            'item' => $entityItemMenu,
            'departamento' => $entity,
            'form'   => $form->createView()
        ));
    }
    
    /**
    * @Secure(roles="ROLE_47-3")
    */
    public function createAction()
    {
        $arrayEmpresas =  $this->retornaArrayEmpresas();
		
        $request = $this->get('request');
        $em = $this->get('doctrine')->getManager('telconet_general');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("47", "1");
        $entity  = new AdmiDepartamento();
        $form    = $this->createForm(new AdmiDepartamentoType(array('arrayEmpresas'=>$arrayEmpresas)), $entity);
        
        
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
            
            return $this->redirect($this->generateUrl('admidepartamento_show', array('id' => $entity->getId())));
        }
        
        return $this->render('administracionBundle:AdmiDepartamento:new.html.twig', array(
            'item' => $entityItemMenu,
            'departamento' => $entity,
            'form'   => $form->createView()
        ));
        
    }
    
    /**
    * @Secure(roles="ROLE_47-4")
    */
    public function editAction($id)
    {
        $arrayEmpresas =  $this->retornaArrayEmpresas();
		
        $em = $this->getDoctrine()->getManager("telconet_general");
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("47", "1");

        if (null == $departamento = $em->find('schemaBundle:AdmiDepartamento', $id)) {
            throw new NotFoundHttpException('No existe el AdmiDepartamento que se quiere modificar');
        }

        $formulario =$this->createForm(new AdmiDepartamentoType(array('arrayEmpresas'=>$arrayEmpresas)), $departamento);
//        $formulario->setData($proceso);

        return $this->render('administracionBundle:AdmiDepartamento:edit.html.twig', array(
            'item' => $entityItemMenu,
			'edit_form' => $formulario->createView(),
			'departamento'   => $departamento));
    }
    
    /**
    * @Secure(roles="ROLE_47-5")
    */
    public function updateAction($id)
    {
        $arrayEmpresas =  $this->retornaArrayEmpresas();
		
        $em = $this->getDoctrine()->getManager('telconet_general');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("47", "1");

        $entity = $em->getRepository('schemaBundle:AdmiDepartamento')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AdmiDepartamento entity.');
        }

        $editForm   = $this->createForm(new AdmiDepartamentoType(array('arrayEmpresas'=>$arrayEmpresas)), $entity);

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

            return $this->redirect($this->generateUrl('admidepartamento_show', array('id' => $id)));
        }

        return $this->render('administracionBundle:AdmiDepartamento:edit.html.twig',array(
            'item' => $entityItemMenu,
            'departamento'      => $entity,
            'edit_form'   => $editForm->createView()
        ));
    }
    
    /**
    * @Secure(roles="ROLE_47-8")
    */
    public function deleteAction($id){
//        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

//        $form->bind($request);
		//$user = $this->get('security.context')->getToken()->getUser();
//        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager('telconet_general');
            $entity = $em->getRepository('schemaBundle:AdmiDepartamento')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AdmiDepartamento entity.');
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

        return $this->redirect($this->generateUrl('admidepartamento'));
    }

    /**
    * @Secure(roles="ROLE_47-9")
    */
    public function deleteAjaxAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $request = $this->get('request');
        $parametro = $request->get('param');
        $em = $this->getDoctrine()->getManager("telconet_general");
        
        $array_valor = explode("|",$parametro);
        foreach($array_valor as $id):
            if (null == $entity = $em->find('schemaBundle:AdmiDepartamento', $id)) {
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
    * @Secure(roles="ROLE_47-7")
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
            ->getRepository('schemaBundle:AdmiDepartamento')
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
     * getDepartamentoByEmpresaYAreaAction, Obtiene las los departamentos  por empresa y area.
     * @param  
     * @return type array $objRespuesta
     * @author Sofía Fernández <sfernandez@telconet.ec>
     * @version 1.0 22-12-2017
     */
    public function getDepartamentoByEmpresaYAreaAction()
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
        $arrayParametros['intIdArea']    = $objPeticion->get('intIdArea');
        $arrayParametros['intStart']     = $intStart;
        $arrayParametros['intLimit']     = $intLimit;        
        $arrayDepartamento  = $emGeneral->getRepository('schemaBundle:AdmiDepartamento')
                                        ->getEncontradosDepartamentoByAreaYEmpresaJson($arrayParametros);
        return $arrayDepartamento;
    }
}