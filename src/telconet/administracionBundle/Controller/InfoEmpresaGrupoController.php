<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoEmpresaGrupo;
use telconet\schemaBundle\Form\InfoEmpresaGrupoType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

class InfoEmpresaGrupoController extends Controller implements TokenAuthenticatedController
{ 
    /**
    * @Secure(roles="ROLE_37-1")
    */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager('telconet');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("37", "1");

        $entities = $em->getRepository('schemaBundle:InfoEmpresaGrupo')->findAll();

        return $this->render('administracionBundle:InfoEmpresaGrupo:index.html.twig', array(
            'item' => $entityItemMenu,
            'infoempresagrupo' => $entities
        ));
    }
    
    /**
    * @Secure(roles="ROLE_37-6")
    */
    public function showAction($id)
    {
        $peticion = $this->get('request');
        
        $em = $this->getDoctrine()->getManager("telconet");
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("37", "1");

        if (null == $infoempresagrupo = $em->find('schemaBundle:InfoEmpresaGrupo', $id)) {
            throw new NotFoundHttpException('No existe el InfoEmpresaGrupo que se quiere mostrar');
        }

        return $this->render('administracionBundle:InfoEmpresaGrupo:show.html.twig', array(
            'item' => $entityItemMenu,
            'infoempresagrupo'   => $infoempresagrupo,
            'flag' =>$peticion->get('flag')
        ));
    }
    
    /**
    * @Secure(roles="ROLE_37-2")
    */
    public function newAction()
    {
        $arrayEmpresas =  $this->retornaArrayEmpresas();
        
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("37", "1");
        $entity = new InfoEmpresaGrupo();
        $form   = $this->createForm(new InfoEmpresaGrupoType(array('boolEdit'=>false, 'arrayEmpresas'=>$arrayEmpresas)), $entity);

        return $this->render('administracionBundle:InfoEmpresaGrupo:new.html.twig', array(
            'item' => $entityItemMenu,
            'infoempresagrupo' => $entity,
            'form'   => $form->createView()
        ));
    }
    
    /**
    * @Secure(roles="ROLE_37-3")
    */
    public function createAction()
    {
        $arrayEmpresas =  $this->retornaArrayEmpresas();
        
        $request = $this->get('request');
        $em = $this->get('doctrine')->getManager('telconet');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("37", "1");
        $entity  = new InfoEmpresaGrupo();
        $form    = $this->createForm(new InfoEmpresaGrupoType(array('boolEdit'=>false, 'arrayEmpresas'=>$arrayEmpresas)), $entity);
        $form->bind($request);
        
        if ($form->isValid()) {
            $em->getConnection()->beginTransaction();

            $entity->setEstado('Activo');
            $entity->setFeCreacion(new \DateTime('now'));
            $entity->setUsrCreacion($request->getSession()->get('user'));
            $entity->setIpCreacion($request->getClientIp());
            $em->persist($entity);
            $em->flush();
            
            $em->getConnection()->commit();
            
            return $this->redirect($this->generateUrl('infoempresagrupo_show', array('id' => $entity->getId())));
        }
        
        return $this->render('administracionBundle:InfoEmpresaGrupo:new.html.twig', array(
            'item' => $entityItemMenu,
            'infoempresagrupo' => $entity,
            'form'   => $form->createView()
        ));
        
    }
    
    /**
    * @Secure(roles="ROLE_37-4")
    */
    public function editAction($id)
    {
        $arrayEmpresas =  $this->retornaArrayEmpresas();
        
        $em = $this->getDoctrine()->getManager("telconet");
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("37", "1");
		
        if (null == $infoempresagrupo = $em->find('schemaBundle:InfoEmpresaGrupo', $id)) {
            throw new NotFoundHttpException('No existe el InfoEmpresaGrupo que se quiere modificar');
        }

        $formulario =$this->createForm(new InfoEmpresaGrupoType(array('boolEdit'=>true, 'arrayEmpresas'=>$arrayEmpresas)), $infoempresagrupo);
//        $formulario->setData($proceso);

        return $this->render('administracionBundle:InfoEmpresaGrupo:edit.html.twig', array(
            'item' => $entityItemMenu,
			'edit_form' => $formulario->createView(),
			'infoempresagrupo'   => $infoempresagrupo));
    }
    
    /**
    * @Secure(roles="ROLE_37-5")
    */
    public function updateAction($id)
    {
        $arrayEmpresas =  $this->retornaArrayEmpresas();
        
        $em = $this->getDoctrine()->getManager('telconet');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("37", "1");
        $entity = $em->getRepository('schemaBundle:InfoEmpresaGrupo')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoEmpresaGrupo entity.');
        }

        $editForm   = $this->createForm(new InfoEmpresaGrupoType(array('boolEdit'=>true, 'arrayEmpresas'=>$arrayEmpresas)), $entity);
        $request = $this->getRequest();
        $editForm->bind($request);

        if ($editForm->isValid()) {
            
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('infoempresagrupo_show', array('id' => $id)));
        }

        return $this->render('administracionBundle:InfoEmpresaGrupo:edit.html.twig',array(
            'item' => $entityItemMenu,
            'infoempresagrupo'      => $entity,
            'edit_form'   => $editForm->createView()
        ));
    }
    
    /**
    * @Secure(roles="ROLE_37-8")
    */
    public function deleteAction($id){
//        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

//        $form->bind($request);
		//$user = $this->get('security.context')->getToken()->getUser();
//        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager('telconet');
            $entity = $em->getRepository('schemaBundle:InfoEmpresaGrupo')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find InfoEmpresaGrupo entity.');
            }
            $estado = 'Eliminado';
            $entity->setEstado($estado);
			
			$em->persist($entity);
            //$em->remove($entity);
            $em->flush();
//        }

        return $this->redirect($this->generateUrl('infoempresagrupo'));
    }

    /**
    * @Secure(roles="ROLE_37-9")
    */
    public function deleteAjaxAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $peticion = $this->get('request');
        $parametro = $peticion->get('param');
        $em = $this->getDoctrine()->getManager("telconet");
        
        $array_valor = explode("|",$parametro);
        foreach($array_valor as $id):
            if (null == $entity = $em->find('schemaBundle:InfoEmpresaGrupo', $id)) {
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
    * @Secure(roles="ROLE_37-7")
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
            ->getRepository('schemaBundle:InfoEmpresaGrupo')
            ->generarJson($nombre,$estado,$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    public function retornaArrayEmpresas()
    {
        $em_naf = $this->getDoctrine()->getManager("telconet_naf");
        $em = $this->getDoctrine()->getManager();
        
        $arrayIdsEmpresas = array();
        $empresas_ingresadas = $em->getRepository('schemaBundle:InfoEmpresaGrupo')->findAll();
        if($empresas_ingresadas && count($empresas_ingresadas)>0)
        {
            foreach($empresas_ingresadas as $key => $value)
            {
               $arrayIdsEmpresas[] =  $value->getId();
            }
        }
        
        $empresas = $em_naf->getRepository('schemaBundle:VEmpresasGrupo')->cargarEmpresas($arrayIdsEmpresas);
        $arrayEmpresas = false;
        if($empresas && count($empresas)>0)
        {
            foreach($empresas as $key => $valueEmpresa)
            {
                $arrayEmpresa["id"] = $valueEmpresa->getId();
                $arrayEmpresa["nombre"] = $valueEmpresa->getNombre();
                $arrayEmpresas[] = $arrayEmpresa;
            }
        }
        return $arrayEmpresas;
    }
    
    /**
    * @Secure(roles="ROLE_37-24")
    */
    public function ajaxDatosEmpresaAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $peticion = $this->get('request');
        $cod_empresa = $peticion->query->get('cod_empresa');
        
        $em_naf = $this->getDoctrine()->getManager("telconet_naf");
        $empresas = $em_naf->getRepository('schemaBundle:VEmpresasGrupo')->findOneById($cod_empresa);

        if($empresas && count($empresas)>0)
        {
            $num = count($empresas);

            if($num == 0)
            {
                $resultado= array('total' => 0,
                                 'encontrados' => array('id_empresa' => 0 , 'nombre_empresa' => 'Ninguno', 'nombre_largo' => 'Ninguno',
                                                        'razon_social' => 'Ninguno', 'ruc' => 'Ninguno', 'repre' => 'Ninguno',
                                                        'direccion' => 'Ninguno', 'telefono' => 'Ninguno', 'fax' => 'Ninguno',
                                                        'modulo_id' => 0 , 'modulo_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
                $objJson = json_encode($resultado);
            }
            else
            {
                $arr_encontrados[]=array('id_empresa' =>$empresas->getId(),
                                         'nombre_empresa' =>trim($empresas->getNombre()),
                                         'nombre_largo' =>trim($empresas->getNombreLargo()),
                                         'razon_social' =>trim($empresas->getRazonSocial()),
                                         'ruc' =>trim($empresas->getIdTributario()),
                                         'repre' =>trim($empresas->getRepre()),
                                         'direccion' =>trim($empresas->getDireccion()),
                                         'telefono' =>trim($empresas->getTelefono()),
                                         'fax' =>trim($empresas->getFax())
                                        );
                
                $data=json_encode($arr_encontrados);
                $objJson= '{"total":"1","encontrados":'.$data.'}';
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