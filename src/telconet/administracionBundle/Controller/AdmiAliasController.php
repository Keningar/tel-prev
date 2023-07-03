<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiAlias;
use telconet\schemaBundle\Entity\InfoEmpresaGrupo;

use telconet\schemaBundle\Form\AdmiAliasType;
use telconet\schemaBundle\Form\EmpresasType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response; 
use JMS\SecurityExtraBundle\Annotation\Secure;

class AdmiAliasController extends Controller implements TokenAuthenticatedController
{ 
    /**
    * @Secure(roles="ROLE_236-1")
    */
    public function indexAction()
    {
    
	$rolesPermitidos = array();
	if (true === $this->get('security.context')->isGranted('ROLE_236-6'))
	{
	    $rolesPermitidos[] = 'ROLE_236-6';
	}
	if (true === $this->get('security.context')->isGranted('ROLE_236-5'))
	{
	    $rolesPermitidos[] = 'ROLE_236-5';
	}
	if (true === $this->get('security.context')->isGranted('ROLE_236-4'))
	{
	    $rolesPermitidos[] = 'ROLE_236-4';
	}
	if (true === $this->get('security.context')->isGranted('ROLE_236-8'))
	{
	    $rolesPermitidos[] = 'ROLE_236-8';
	}
	if (true === $this->get('security.context')->isGranted('ROLE_236-1'))
	{
	    $rolesPermitidos[] = 'ROLE_236-1';
	}
	if (true === $this->get('security.context')->isGranted('ROLE_236-2'))
	{
	    $rolesPermitidos[] = 'ROLE_236-2';
	}
    
        $em = $this->getDoctrine()->getManager('telconet_general');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("46", "1");
			
       // $entities = $em->getRepository('schemaBundle:AdmiAlias')->findAll();
				
        return $this->render('administracionBundle:AdmiAlias:index.html.twig', array(
            'item' => $entityItemMenu,
            'rolesPermitidos' => $rolesPermitidos  
         //   'alias' => $entities
        ));
    }
    
    /**
    * @Secure(roles="ROLE_236-6")
    */
    public function showAction($id)
    {
        $peticion = $this->get('request');
        
        $em = $this->getDoctrine()->getManager("telconet_comunicacion");
        $emComercial = $this->getDoctrine()->getManager("telconet");
        $emGeneral = $this->getDoctrine()->getManager("telconet_general");
        $emJurisdiccion = $this->getDoctrine()->getManager("telconet_infraestructura");
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("46", "1");

        if (null == $alias = $em->find('schemaBundle:AdmiAlias', $id)) {
            throw new NotFoundHttpException('No existe el AdmiAlias que se quiere mostrar');
        }

	$EmpresaGrupo = "";
	if($alias && $alias->getEmpresaCod())
	{
		$InfoEmpresaGrupo = $emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneById($alias->getEmpresaCod());
		$EmpresaGrupo = ($InfoEmpresaGrupo ? ($InfoEmpresaGrupo->getNombreEmpresa() ? $InfoEmpresaGrupo->getNombreEmpresa()  : '') : '');
	}	
	
	$canton = "";
	if($alias && $alias->getCantonId())
	{
		$admiCanton = $emGeneral->getRepository('schemaBundle:AdmiCanton')->findOneById($alias->getCantonId());
		$canton = ($admiCanton ? ($admiCanton->getNombreCanton() ? $admiCanton->getNombreCanton()  : '') : '');
	}
	
	$departamento = "";
	if($alias && $alias->getDepartamentoId())
	{
		$admiDepart = $emGeneral->getRepository('schemaBundle:AdmiDepartamento')->findOneById($alias->getDepartamentoId());
		$departamento = ($admiDepart ? ($admiDepart->getNombreDepartamento() ? $admiDepart->getNombreDepartamento()  : '') : '');
	}
		
		
        return $this->render('administracionBundle:AdmiAlias:show.html.twig', array(
            'item' => $entityItemMenu,
            'alias'   => $alias,
	    'EmpresaGrupo' => $EmpresaGrupo ,
	    'Jurisdiccion' => $canton,
	    'departamento'=>$departamento
        ));
    }
    
    /**
    * @Secure(roles="ROLE_236-2")
    */
    public function newAction()
    {        		
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("46", "1");
        
        $entity = new AdmiAlias();        
        
        $form   = $this->createForm(new AdmiAliasType(),$entity);        

        return $this->render('administracionBundle:AdmiAlias:new.html.twig', array(
            'item' => $entityItemMenu,
            'alias' => $entity,
            'form'   => $form->createView()            
        ));
    }
    
    /**
    * @Secure(roles="ROLE_236-3")
    */
    public function createAction()
    {        
	$respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $request = $this->get('request');
        
        $em = $this->get('doctrine')->getManager('telconet_comunicacion');
        
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("46", "1");
                                        
        $entity  = new AdmiAlias();        
        $form    = $this->createForm(new AdmiAliasType(), $entity);        
                
        $entity->setEstado('Activo');
        $entity->setFeCreacion(new \DateTime('now'));
        $entity->setUsrCreacion($request->getSession()->get('user'));
        $entity->setFeUltMod(new \DateTime('now'));
        $entity->setUsrUltMod($request->getSession()->get('user'));        
        
        $empresa = $request->get('empresa');                
        $valor   = $request->get('valor');  
        $ciudad = $request->get('ciudad')?$request->get('ciudad'):'';
        $departamento = $request->get('departamento')?$request->get('departamento'):'';
        
        $entity->setEmpresaCod($empresa);
        $entity->setCantonId($ciudad);
        $entity->setDepartamentoId($departamento);
        $entity->setValor($valor);
                
        $em->getConnection()->beginTransaction();
        
        try{
        
	    $em->persist($entity);
            $em->flush();
            $em->getConnection()->commit();
                        
            $resultado = json_encode(array('success'=>true,
					      'id'=>$entity->getId()
					      ));	             
        
        }catch(Exception $e){
        
	      $em->getConnection()->rollback();
	      $em->getConnection()->close();			
	      $resultado = json_encode(array('success'=>false,'mensaje'=>$e));
        }                               
        
        $respuesta->setContent($resultado);
	
        return $respuesta;
        
    }
    
    /**
    * @Secure(roles="ROLE_236-4")
    */
    public function editAction($id)
   {              
		
        $em = $this->getDoctrine()->getManager("telconet_comunicacion");
        $emComercial = $this->getDoctrine()->getManager("telconet");
        
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_general');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("46", "1");

        if (null == $alias = $em->find('schemaBundle:AdmiAlias', $id)) {
            throw new NotFoundHttpException('No existe el AdmiAlias que se quiere modificar');
        }

        $formulario =$this->createForm(new AdmiAliasType(), $alias);                
        
        $entityEmpresas = $emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')->find($alias->getEmpresaCod());
        
        if($alias->getCantonId())
	    $entityJurisdiccion = $emInfraestructura->getRepository('schemaBundle:AdmiCanton')->find($alias->getCantonId());
	else $entityJurisdiccion = null;
	
	 if($alias->getDepartamentoId())
	    $departamento = $emInfraestructura->getRepository('schemaBundle:AdmiDepartamento')->find($alias->getDepartamentoId());
	else $departamento = null;               

        return $this->render('administracionBundle:AdmiAlias:edit.html.twig', array(
            'item' => $entityItemMenu,
	    'form' => $formulario->createView(),
	    'alias'=>$alias,
	    'empresa' => $entityEmpresas,
	    'jurisdiccion'=>$entityJurisdiccion,
	    'departamento'=>$departamento
	    ));
    }
    
    /**
    * @Secure(roles="ROLE_236-5")
    */
    public function updateAction()
    {        
    
	$request = $this->getRequest();   
    
	$respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
		
        $em = $this->getDoctrine()->getManager('telconet_comunicacion');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("46", "1");
        
        $entity = $em->getRepository('schemaBundle:AdmiAlias')->find($request->get('id'));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AdmiAlias entity.');
        }

        $editForm   = $this->createForm(new AdmiAliasType(), $entity);
                           
        $entityEmpresas  = new InfoEmpresaGrupo();
        $form            = $this->createForm(new AdmiAliasType(), $entity);        
                
        $entity->setEstado('Modificado');       
        $entity->setFeUltMod(new \DateTime('now'));
        $entity->setUsrUltMod($request->getSession()->get('user'));        
        
        $empresa = $request->get('empresa');                
        $valor   = $request->get('valor');  
        $jurisdiccion = $request->get('ciudad')?$request->get('ciudad'):'';
        $departamento = $request->get('departamento')?$request->get('departamento'):'';
                
        
        $entity->setEmpresaCod($empresa);
        $entity->setValor($valor);   
        $entity->setCantonId($jurisdiccion);
        $entity->setDepartamentoId($departamento);

        $em->getConnection()->beginTransaction();
        
        try{
        
	    $em->persist($entity);
            $em->flush();
            $em->getConnection()->commit();
                        
            $resultado = json_encode(array('success'=>true,
					      'id'=>$entity->getId()
					      ));	             
        
        }catch(Exception $e){
        
	      $em->getConnection()->rollback();
	      $em->getConnection()->close();			
	      $resultado = json_encode(array('success'=>false,'mensaje'=>$e));
        }                               
        
        $respuesta->setContent($resultado);
	
        return $respuesta;
    }
    
    public function getEmpresasAction(){
    
	  $request = $this->getRequest();   
    
	  $respuesta = new Response();
	  $respuesta->headers->set('Content-Type', 'text/json');	  	  	  	  
	  
	  $objJson = $this->getDoctrine()
            ->getManager("telconet")
            ->getRepository('schemaBundle:InfoEmpresaGrupo')
            ->generarJson('','Activo','','');
                        
	  $respuesta->setContent($objJson);
        
	  return $respuesta;        
    
    }
    
    public function getJurisdiccionXEmpresaAction(){
    
	  $request = $this->getRequest();   
    
	  $respuesta = new Response();
	  $respuesta->headers->set('Content-Type', 'text/json');
	  
	  $paramEmpresa = $request->get('idEmpresa');	
	  	  
	  
	  $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiJurisdiccion')
            ->generarJsonJurisdicciones('',$paramEmpresa,'Eliminado','','');
	  $respuesta->setContent($objJson);
        
	  return $respuesta;
            
    }

    /**
     * Funcion getCiudadesPorEmpresaAction: que consulta las ciudades por empresa
     *
     * @author modificado Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 23-06-2016 Se realiza ajuste por cambios en los parametros de la funcion
     *
     * @version 1.0
     *
     * @return array $respuesta
     */
    public function getCiudadesPorEmpresaAction(){
    
	    $respuesta = new Response();
	    $respuesta->headers->set('Content-Type', 'text/json');
	    
	    $peticion = $this->get('request');
	    $session = $peticion->getSession();
	    $codEmpresa = ($session->get('idEmpresa') ? $session->get('idEmpresa') : ""); 
	    	    
	    $start = $peticion->query->get('start');
	    $limit = $peticion->query->get('limit');	    	    
	    
	    $em = $this->getDoctrine()->getManager("telconet");
	    $paramEmpresa = $peticion->query->get('empresa') ? $peticion->query->get('empresa') : "";	    	    
	    	    		    
	    $objJson = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
	    ->generarJsonCiudadesPorEmpresa($paramEmpresa,"","");
	    
	    $respuesta->setContent($objJson);
	    
	    return $respuesta;        
    
    }
    
    /**
     * Funcion getDepartamentosPorEmpresaYCiudadAction: que consulta los departamentos por empresa y ciudad
     *
     * @author modificado Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 24-06-2016 Se realiza ajustes en los parametros del llamado a la funcion: generarJsonDepartamentosPorCiudadYEmpresa
     *                         por cambios en el grid de tareas, agregar nuevo filtro departamento origen de la tarea
     *
     * @version 1.0
     *
     * @return array $respuesta
     */
     public function getDepartamentosPorEmpresaYCiudadAction(){
    
	    $respuesta = new Response();
	    $respuesta->headers->set('Content-Type', 'text/json');
	    
	    $peticion = $this->get('request');
	    $session = $peticion->getSession();
	    $codEmpresa = ($session->get('idEmpresa') ? $session->get('idEmpresa') : ""); 
	    	    
	    $start = $peticion->query->get('start');
	    $limit = $peticion->query->get('limit');
	    
	    $id_canton = $peticion->query->get('id_canton')?$peticion->query->get('id_canton'):'';
	    
	    $em = $this->getDoctrine()->getManager("telconet");
	    $paramEmpresa = $peticion->query->get('empresa') ? $peticion->query->get('empresa') : "";
	    
	    $nombreDep = $peticion->query->get('query') ? $peticion->query->get('query') : "";
	    	    		    
	    $objJson = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
	    ->generarJsonDepartamentosPorCiudadYEmpresa($paramEmpresa,$id_canton,$nombreDep,"");
	    
	    $respuesta->setContent($objJson);
	    
	   return $respuesta;                    
    
    }
    
    
    /**
    * @Secure(roles="ROLE_236-8")
    */
    public function deleteAction($id){

	  $request = $this->getRequest();

	  $em = $this->getDoctrine()->getManager('telconet_comunicacion');
	  $entity = $em->getRepository('schemaBundle:AdmiAlias')->find($id);

	  if (!$entity) {
	      throw $this->createNotFoundException('Unable to find AdmiAlias entity.');
	  }	  
	  $entity->setEstado('Eliminado');            
	  $entity->setFeUltMod(new \DateTime('now'));            
	  $entity->setUsrUltMod($request->getSession()->get('user'));            
	  $em->persist($entity);			            
	  $em->flush();

	  return $this->redirect($this->generateUrl('admialias'));
    }

    /**
    * @Secure(roles="ROLE_236-9")
    */
    public function deleteAjaxAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $request = $this->get('request');
        $parametro = $request->get('param');
        $em = $this->getDoctrine()->getManager("telconet_comunicacion");
        
        $array_valor = explode("|",$parametro);
        foreach($array_valor as $id):
            if (null == $entity = $em->find('schemaBundle:AdmiAlias', $id)) {
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
        
        return $respuesta;
    }
    
    /*
     * Llena el grid de consulta.
     */
    /**
    * @Secure(roles="ROLE_236-7")
    */
    public function gridAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
	$queryNombre = $peticion->query->get('query') ? $peticion->query->get('query') : "";
        $nombre = ($queryNombre != '' ? $queryNombre : $peticion->query->get('nombre'));
        $estado = $peticion->query->get('estado');
        $empresa = $peticion->query->get('empresa')?$peticion->query->get('empresa'):'';
        $ciudad = $peticion->query->get('ciudad')?$peticion->query->get('ciudad'):'';
        $departamento = $peticion->query->get('departamento')?$peticion->query->get('departamento'):'';
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $em = $this->getDoctrine()->getManager("telconet");
        $emI = $this->getDoctrine()->getManager("telconet_general");
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_comunicacion")
            ->getRepository('schemaBundle:AdmiAlias')
            ->generarJson($nombre,$estado,$empresa,$ciudad,$departamento,$start,$limit,$em,$emI);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    
    
	    
}