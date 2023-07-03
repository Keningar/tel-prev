<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoPersona;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolHisto;
use telconet\schemaBundle\Entity\InfoPersonaFormaContacto;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRol;
use telconet\schemaBundle\Form\InfoPersonaType;
use telconet\schemaBundle\Form\AgenciaType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response; 
use JMS\SecurityExtraBundle\Annotation\Secure;

class AgenciasController extends Controller implements TokenAuthenticatedController
{ 
    /**
    * @Secure(roles="ROLE_183-1")
    */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("183", "1");
			
				
        return $this->render('administracionBundle:Agencias:index.html.twig', array(
            'item' => $entityItemMenu
        ));
    }
    
    /**
    * @Secure(roles="ROLE_183-6")
    */
    public function showAction($id)
    {
        $peticion = $this->get('request');
        
        $em = $this->getDoctrine()->getManager();
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("183", "1");
		
		$personaEmpresaRol = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findOneById($id);

        if (null == $agencia = $em->find('schemaBundle:InfoPersona', $personaEmpresaRol->getPersonaId()->getId())) {
            throw new NotFoundHttpException('No existe la Agencia que se quiere mostrar');
        }
		
        //Obtiene el historial del prospecto(pre-cliente)
        $historial = $em->getRepository('schemaBundle:InfoPersonaEmpresaRolHisto')->findHistorialPorPersonaEmpresaRol($id);
        $ultimoEstado = $em->getRepository('schemaBundle:InfoPersonaEmpresaRolHisto')->findUltimoEstadoPorPersonaEmpresaRol($id);        
        $idUltimoEstado=$ultimoEstado[0]['ultimo'];
		if ($idUltimoEstado){
			$entityUltimoEstado=$em->getRepository('schemaBundle:InfoPersonaEmpresaRolHisto')->find($idUltimoEstado);
			$estado=$entityUltimoEstado->getEstado();
		}
        //Recorre el historial y separa en arreglos cada estado
        $i=0;$creacion=null;$convertido=null;$eliminado=null;$ultMod=null;
		foreach($historial as $dato):
			//echo 'entro';
            if ($i==0){
                $creacion=array('estado'=>$dato->getEstado(),'usrCreacion'=>$dato->getUsrCreacion(),'feCreacion'=>$dato->getFeCreacion(),'ipCreacion'=>$dato->getIpCreacion());
            }
            if($i>0){
                if($dato->getEstado()=='Eliminado'){
                    $eliminado=array('estado'=>$dato->getEstado(),'usrCreacion'=>$dato->getUsrCreacion(),'feCreacion'=>$dato->getFeCreacion(),'ipCreacion'=>$dato->getIpCreacion());   
                }else{
                    $ultMod=array('estado'=>$dato->getEstado(),'usrCreacion'=>$dato->getUsrCreacion(),'feCreacion'=>$dato->getFeCreacion(),'ipCreacion'=>$dato->getIpCreacion());   
                }
            }
            $i++;
        endforeach; 
		

        return $this->render('administracionBundle:Agencias:show.html.twig', array(
            'item' => $entityItemMenu,
            'personaEmpresaRol'   => $personaEmpresaRol,
			'creacion' => $creacion,
			'ultMod' => $ultMod,
			'eliminado' => $eliminado,
            'agencia'   => $agencia,
            'flag' =>$peticion->get('flag')
        ));
    }
    
    /**
    * @Secure(roles="ROLE_183-2")
    */
    public function newAction()
    {
        $request=$this->getRequest();
		
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("183", "1");
		
        $entity = new InfoPersona();
        $form = $this->createForm(new AgenciaType(), $entity);
        $idEmpresa = $request->getSession()->get('idEmpresa');

        return $this->render('administracionBundle:Agencias:new.html.twig', array(
            'item' => $entityItemMenu,
            'agencia' => $entity,
            'form'   => $form->createView()
        ));		
    }
    
    /**
    * @Secure(roles="ROLE_183-3")
    */
    public function createAction()
    {
        $request = $this->getRequest();
		
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("183", "1");
		
        $entity = new InfoPersona();
        $form = $this->createForm(new AgenciaType(), $entity);
        $datos_form = $request->request->get('agenciatype');
        //print_r($datos_form['fechaNacimiento']['year']);die;
        //print_r($request->request->all()); die(); 
        //$session  = $request->getSession();
        //$user = $this->get('security.context')->getToken()->getUser();
        //$usrCreacion=$user->getUsername();
		
        $array_formas_contacto = explode(",", $datos_form['formas_contacto']);
        $a = 0;
        $x = 0;
        for ($i = 0; $i < count($array_formas_contacto); $i++) {
            if ($a == 3) {
                $a = 0;
                $x++;
            }
            if ($a == 1)
                $formas_contacto[$x]['formaContacto'] = $array_formas_contacto[$i];
            if ($a == 2)
                $formas_contacto[$x]['valor'] = $array_formas_contacto[$i];
            $a++;
        }
        //print_r($formas_contacto);
        //die;
        $idEmpresa = $request->getSession()->get('idEmpresa');
        $idOficina = 40;
        $usrCreacion = $request->getSession()->get('user');
        $usrUltMod = $request->getSession()->get('user');
        $estadoI='Inactivo';
		
        $em = $this->getDoctrine()->getManager('telconet');
        $em->getConnection()->beginTransaction();
        try {
            $idPersona = (isset($datos_form['personaid']) ? ($datos_form['personaid']!="" ? $datos_form['personaid'] : "") : "");
			if($idPersona != "")
			{	
		        if (null == $entity = $em->find('schemaBundle:InfoPersona', $idPersona)) {
		            throw new NotFoundHttpException('No existe la Agencia que se quiere modificar');
		        }
			}
			else
			{
				//NUEVO, XQ NO EXISTE PERSONA CON LA IDENTIFICACION INGRESADA...
	            $entity->setFeCreacion(new \DateTime('now'));
	            $entity->setUsrCreacion($usrCreacion);
	            $entity->setIpCreacion($request->getClientIp());
	            $entity->setEstado('Activo');
			}
			
            $entity->setTipoIdentificacion($datos_form['tipoIdentificacion']);
            $entity->setIdentificacionCliente($datos_form['identificacionCliente']);
            //$entity->setTipoEmpresa($datos_form['tipoEmpresa']);
            $entity->setTipoTributario("NAT");
            $entity->setNombres($datos_form['nombres']);
            $entity->setApellidos($datos_form['apellidos']);
            //$entity->setRazonSocial("");
            //$entity->setRepresentanteLegal($datos_form['representanteLegal']);
            $entity->setNacionalidad($datos_form['nacionalidad']);
            $entity->setGenero($datos_form['genero']);
            if($datos_form['estadoCivil']!="") $entity->setEstadoCivil($datos_form['estadoCivil']);
			$entity->setDireccion($datos_form['direccion']);
            //$entity->setDireccionTributaria($datos_form['direccionTributaria']);
            //$entity->setCalificacionCrediticia($datos_form['calificacionCrediticia']);
            $entity->setOrigenProspecto('N');            
			if($datos_form['fechaNacimiento']['year']!= "" && $datos_form['fechaNacimiento']['month']!= "" && $datos_form['fechaNacimiento']['day'] != ""){
				$entity->setFechaNacimiento(date_create($datos_form['fechaNacimiento']['year'] . '-' . $datos_form['fechaNacimiento']['month'] . '-' . $datos_form['fechaNacimiento']['day']));
            }
			$entityAdmiTitulo = $em->getRepository('schemaBundle:AdmiTitulo')->find($datos_form['tituloId']);
            if ($entityAdmiTitulo){   $entity->setTituloId($entityAdmiTitulo); }
            $em->persist($entity);
            $em->flush();
			
			
            //ASIGNA ROL DE PRE-CLIENTE A LA PERSONA
            $entityEmpresaRol = $em->getRepository('schemaBundle:InfoEmpresaRol')->findPorNombreTipoRolPorEmpresa('Agencias', $idEmpresa);
            $entityOficina = $em->getRepository('schemaBundle:InfoOficinaGrupo')->find($idOficina);
			
			$entityPersonaEmpresaRol = null;
			if($idPersona != "")
			{			
				$entityPersonaEmpresaRol = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
											  ->getPersonaEmpresaRolPorPersonaPorTipoRol($idPersona,'Agencias',$idEmpresa);   
			}
			
			if(!$entityPersonaEmpresaRol)
			{
				$entityPersonaEmpresaRol = new InfoPersonaEmpresaRol();
				$entityPersonaEmpresaRol->setEmpresaRolId($entityEmpresaRol);
	            $entityPersonaEmpresaRol->setPersonaId($entity);
	            $entityPersonaEmpresaRol->setOficinaId($entityOficina);
	            $entityPersonaEmpresaRol->setFeCreacion(new \DateTime('now'));
	            $entityPersonaEmpresaRol->setUsrCreacion($usrCreacion);
	            $entityPersonaEmpresaRol->setEstado('Activo');
	            $em->persist($entityPersonaEmpresaRol);
	            $em->flush();			
			}

            //REGISTRA EN LA TABLA DE PERSONA HISTORIAL           
            $entity_persona_historial = new InfoPersonaEmpresaRolHisto();
            $entity_persona_historial->setEstado($entity->getEstado());
            $entity_persona_historial->setFeCreacion(new \DateTime('now'));
            $entity_persona_historial->setIpCreacion($request->getClientIp());
            $entity_persona_historial->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
            $entity_persona_historial->setUsrCreacion($usrCreacion);
            $em->persist($entity_persona_historial);
            $em->flush();
			
			if($idPersona != "")
			{			
	            //PONE ESTADO INACTIVO A TODOS LAS FORMAS DE CONTACTO DE LA PERSONA QUE tengan estado ACTIVO
                /* @var $serviceInfoPersonaFormaContacto \telconet\comercialBundle\Service\InfoPersonaFormaContactoService */
                $serviceInfoPersonaFormaContacto = $this->get('comercial.InfoPersonaFormaContacto');
                $serviceInfoPersonaFormaContacto->inactivarPersonaFormaContactoActivasPorPersona($idPersona, $usrUltMod);
			}
			
            //ReGISTRA LAS FORMAS DE CONTACTO DEL CLIENTE
            for ($i=0;$i < count($formas_contacto);$i++){
                $entity_persona_forma_contacto = new InfoPersonaFormaContacto();
                $entity_persona_forma_contacto->setValor($formas_contacto[$i]["valor"]);
                $entity_persona_forma_contacto->setEstado("Activo");
                $entity_persona_forma_contacto->setFeCreacion(new \DateTime('now'));
                $entityAdmiFormaContacto = $em->getRepository('schemaBundle:AdmiFormaContacto')->findPorDescripcionFormaContacto($formas_contacto[$i]["formaContacto"]);
                $entity_persona_forma_contacto->setFormaContactoId($entityAdmiFormaContacto);
                $entity_persona_forma_contacto->setIpCreacion($request->getClientIp());
                $entity_persona_forma_contacto->setPersonaId($entity);
                $entity_persona_forma_contacto->setUsrCreacion($usrCreacion);
                $em->persist($entity_persona_forma_contacto);
                $em->flush();
            }
            
            $em->getConnection()->commit();
            return $this->redirect($this->generateUrl('agencias_show', array('id' => $entityPersonaEmpresaRol->getId())));
        } catch (\Exception $e) {
            // Rollback the failed transaction attempt
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            //aqu? alg?n mensaje con la excepci?n concatenada
            $entity = $em->getRepository('schemaBundle:InfoPersona')->findClientesPorEmpresaPorEstadoPorNombre('Activo', $idEmpresa, '');
            
			return $this->render('administracionBundle:Agencias:new.html.twig', array(
				'item' => $entityItemMenu,
				'agencia' => $entity,
	            'form'   => $form->createView(),
				//'error' => 'Ocurrio un error interno al tratar de ingresar el cliente. Por favor comuniquese con el Administrador.'
                'error' => $e->getMessage()
			));
        }        
    }
    
    /**
    * @Secure(roles="ROLE_183-4")
    */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("183", "1");

		$personaEmpresaRol = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findOneById($id);

        if (null == $agencia = $em->find('schemaBundle:InfoPersona', $personaEmpresaRol->getPersonaId()->getId())) {
            throw new NotFoundHttpException('No existe la Agencia que se quiere modificar');
        }

        $formulario =$this->createForm(new AgenciaType(), $agencia);
//        $formulario->setData($proceso);

        return $this->render('administracionBundle:Agencias:edit.html.twig', array(
            'item' => $entityItemMenu,
			'edit_form' => $formulario->createView(),
			'agencia'   => $agencia));
    }
    
    /**
    * @Secure(roles="ROLE_183-5")
    */
    public function updateAction($id)
    {		
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("183", "1");
		
        $request=$this->getRequest();
        $em = $this->getDoctrine()->getManager('telconet');
		
		//$personaEmpresaRol = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findOneById($id);
        $entity = $em->getRepository('schemaBundle:InfoPersona')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoPersona entity.');
        }
        $editForm = $this->createForm(new AgenciaType(), $entity);
        
        $datos_form = $request->request->get('agenciatype');
        $array_formas_contacto = explode(",", $datos_form['formas_contacto']);
        $a = 0;$x = 0;
        for ($i = 0; $i < count($array_formas_contacto); $i++) {
            if ($a == 3) {
                $a = 0;
                $x++;
            }
            if ($a == 1)
                $formas_contacto[$x]['formaContacto'] = $array_formas_contacto[$i];
            if ($a == 2)
                $formas_contacto[$x]['valor'] = $array_formas_contacto[$i];
            $a++;
        }   
        //print_r($array_formas_contacto);die;
		
        $idEmpresa = $request->getSession()->get('idEmpresa');
        $idOficina = $request->getSession()->get('idOficina');
        $usrUltMod = $request->getSession()->get('user');
        $estadoI='Inactivo';
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoPersona entity.');
        }
		
        $em->getConnection()->beginTransaction();
        try {
		
            $entity->setTipoIdentificacion($datos_form['tipoIdentificacion']);
            $entity->setIdentificacionCliente($datos_form['identificacionCliente']);
            //$entity->setTipoEmpresa($datos_form['tipoEmpresa']);
            $entity->setTipoTributario("NAT");
            $entity->setNombres($datos_form['nombres']);
            $entity->setApellidos($datos_form['apellidos']);
            //$entity->setRazonSocial("");
            //$entity->setRepresentanteLegal($datos_form['representanteLegal']);
            $entity->setNacionalidad($datos_form['nacionalidad']);
            $entity->setGenero($datos_form['genero']);
            if($datos_form['estadoCivil']!="") $entity->setEstadoCivil($datos_form['estadoCivil']);
			$entity->setDireccion($datos_form['direccion']);
            //$entity->setDireccionTributaria($datos_form['direccionTributaria']);
            //$entity->setCalificacionCrediticia($datos_form['calificacionCrediticia']);
            
			if($datos_form['fechaNacimiento']['year']!= "" && $datos_form['fechaNacimiento']['month']!= "" && $datos_form['fechaNacimiento']['day'] != ""){
				$entity->setFechaNacimiento(date_create($datos_form['fechaNacimiento']['year'] . '-' . $datos_form['fechaNacimiento']['month'] . '-' . $datos_form['fechaNacimiento']['day']));
            }
			
			$entityAdmiTitulo = $em->getRepository('schemaBundle:AdmiTitulo')->find($datos_form['tituloId']);
            if ($entityAdmiTitulo)
                $entity->setTituloId($entityAdmiTitulo);
				
            $entity->setOrigenProspecto('N');
			
			$em->persist($entity);
            $em->flush();
			
            //REGISTRA EN LA TABLA DE PERSONA HISTORIAL
            $personaEmpresaRol=$em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
								  ->getPersonaEmpresaRolPorPersonaPorTipoRol($id,'Agencias',$idEmpresa);    
								  
            $entity_persona_historial = new InfoPersonaEmpresaRolHisto();
            $entity_persona_historial->setEstado($entity->getEstado());
            $entity_persona_historial->setFeCreacion(new \DateTime('now'));
            $entity_persona_historial->setIpCreacion($request->getClientIp());
            $entity_persona_historial->setPersonaEmpresaRolId($personaEmpresaRol);
            $entity_persona_historial->setUsrCreacion($usrUltMod);
            $em->persist($entity_persona_historial);
            $em->flush();

            //PONE ESTADO INACTIVO A TODOS LAS FORMAS DE CONTACTO DE LA PERSONA QUE tengan estado ACTIVO
            /* @var $serviceInfoPersonaFormaContacto \telconet\comercialBundle\Service\InfoPersonaFormaContactoService */
            $serviceInfoPersonaFormaContacto = $this->get('comercial.InfoPersonaFormaContacto');
            $serviceInfoPersonaFormaContacto->inactivarPersonaFormaContactoActivasPorPersona($entity->getId(), $usrUltMod);
            
            //ReGISTRA LAS FORMAS DE CONTACTO DEL CLIENTE
            for ($i=0;$i < count($formas_contacto);$i++){
                $entity_persona_forma_contacto = new InfoPersonaFormaContacto();
                $entity_persona_forma_contacto->setValor($formas_contacto[$i]["valor"]);
                $entity_persona_forma_contacto->setEstado("Activo");
                $entity_persona_forma_contacto->setFeCreacion(new \DateTime('now'));
                $entityAdmiFormaContacto = $em->getRepository('schemaBundle:AdmiFormaContacto')->findPorDescripcionFormaContacto($formas_contacto[$i]["formaContacto"]);
                $entity_persona_forma_contacto->setFormaContactoId($entityAdmiFormaContacto);
                $entity_persona_forma_contacto->setIpCreacion($request->getClientIp());
                $entity_persona_forma_contacto->setPersonaId($entity);
                $entity_persona_forma_contacto->setUsrCreacion($usrUltMod);
                $em->persist($entity_persona_forma_contacto);
                $em->flush();
            }            
            
            $em->getConnection()->commit();
        } catch (\Exception $e) {
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            return $this->redirect($this->generateUrl('agencias_show', array('id' => $personaEmpresaRol->getId())));
        }
        return $this->redirect($this->generateUrl('agencias_show', array('id' => $personaEmpresaRol->getId())));
    }
    
    /**
    * @Secure(roles="ROLE_183-8")
    */
    public function deleteAction($id){
//        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

//        $form->bind($request);
		//$user = $this->get('security.context')->getToken()->getUser();
//        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Agencias entity.');
            }
            $estado = 'Eliminado';
            $entity->setEstado($estado);			
			$em->persist($entity);
            $em->flush();
			
            $entity_persona_historial = new InfoPersonaEmpresaRolHisto();
            $entity_persona_historial->setEstado($estado);
            $entity_persona_historial->setFeCreacion(new \DateTime('now'));
            $entity_persona_historial->setIpCreacion($request->getClientIp());
            $entity_persona_historial->setPersonaEmpresaRolId($entity);
            $entity_persona_historial->setUsrCreacion($request->getSession()->get('user'));
            $em->persist($entity_persona_historial);
            $em->flush();
//        }

        return $this->redirect($this->generateUrl('agencias'));
    }

    /**
    * @Secure(roles="ROLE_183-9")
    */
    public function deleteAjaxAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $request = $this->get('request');
        $parametro = $request->get('param');
        $em = $this->getDoctrine()->getManager();
        
        $array_valor = explode("|",$parametro);
        foreach($array_valor as $id):
            if (null == $entity = $em->find('schemaBundle:InfoPersonaEmpresaRol', $id)) {
                $respuesta->setContent("No existe la entidad");
            }
            else{
				if(strtolower($entity->getEstado()) != "eliminado")
				{
					$estado = 'Eliminado';
					$entity->setEstado($estado);
					$em->persist($entity);
					$em->flush();
					
					$entity_persona_historial = new InfoPersonaEmpresaRolHisto();
					$entity_persona_historial->setEstado($estado);
					$entity_persona_historial->setFeCreacion(new \DateTime('now'));
					$entity_persona_historial->setIpCreacion($request->getClientIp());
					$entity_persona_historial->setPersonaEmpresaRolId($entity);
					$entity_persona_historial->setUsrCreacion($request->getSession()->get('user'));
					$em->persist($entity_persona_historial);
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
    * @Secure(roles="ROLE_183-7")
    */
    public function gridAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $nombres = $peticion->query->get('nombres');
        $apellidos = $peticion->query->get('apellidos');
        $identificacion = $peticion->query->get('identificacion');
        $estado = $peticion->query->get('estado');
		
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager()
            ->getRepository('schemaBundle:InfoPersona')
            ->generarJsonAgencias($nombres, $apellidos, $identificacion, $estado, $start, $limit);
			
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
	
	//ESTA FUNCION ES LLAMADA DESDE INGRESO y EDICION DE CLIENTE,CONTACTO y PRE-CLIENTE
    public function formasContactoGridAction() {
        $request = $this->getRequest();
        $limit = $request->get("limit");
        $page = $request->get("page");
        $start = $request->get("start");
        $personaid = $request->get("personaid");
        $idEmpresa = $request->getSession()->get('idEmpresa');
        $em = $this->get('doctrine')->getManager('telconet');
        //Cuando sea inicio puedo sacar los 30 registros
        $resultado = $em->getRepository('schemaBundle:InfoPersonaFormaContacto')->findPorEstadoPorPersona($personaid, 'Activo', $limit, $page, $start);
        $datos = $resultado['registros'];
        $total = $resultado['total'];
        $i = 1;
        foreach ($datos as $datos):
            $arreglo[] = array(
                'idPersonaFormaContacto' => $datos->getId(),
                'idPersona' => $datos->getPersonaId()->getId(),
                'formaContacto' => $datos->getFormaContactoId()->getDescripcionFormaContacto(),
                'valor' => $datos->getValor()
            );
        endforeach;
        if (!empty($arreglo))
            $response = new Response(json_encode(array('total' => $total, 'personaFormasContacto' => $arreglo)));
        else {
            $arreglo[] = array();
            $response = new Response(json_encode(array('total' => $total, 'personaFormasContacto' => $arreglo)));
        }
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }

	//ESTA FUNCION ES LLAMADA DESDE INGRESO y EDICION DE CLIENTE,CONTACTO y PRE-CLIENTE
    public function formasContactoAjaxAction() {
        /* Modificacion a utilizacion de estados por modulos */
        //$session = $this->get('request')->getSession();
        //$modulo_activo=$session->get("modulo_activo");
        $em = $this->get('doctrine')->getManager('telconet');
        $datos = $em->getRepository('schemaBundle:AdmiFormaContacto')->findFormasContactoPorEstado('Activo');
        foreach ($datos as $datos):
            $arreglo[] = array(
                'id' => $datos->getId(),
                'descripcion' => $datos->getDescripcionFormaContacto()
            );
        endforeach;
        $response = new Response(json_encode(array('formasContacto' => $arreglo)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }
	
	
    public function buscarPersonaPorIdentificacionAjaxAction() {        
        $peticion = $this->get('request');    
        $tipoIdentificacion = $peticion->get('tipoIdentificacion');
        $identificacion = $peticion->get('identificacion');		
		
        $em = $this->get('doctrine')->getManager('telconet');
        $datos = $em->getRepository('schemaBundle:InfoPersona')->findPersonaPorIdentificacion($tipoIdentificacion, $identificacion);
		
		$arreglo = false;	
        foreach ($datos as $datos):
            $arreglo[] = array(
                'id' => $datos->getId(),
                'nombres' => $datos->getNombres(),
                'apellidos' => $datos->getApellidos(),
                'tituloId' => ($datos->getTituloId() ? ($datos->getTituloId()->getId() ? $datos->getTituloId()->getId() : "") : ""),
                'genero' => $datos->getGenero(),
                'estadoCivil' => ($datos->getEstadoCivil() ? $datos->getEstadoCivil() : ""),
                'fechaNacimiento_anio' => ($datos->getFechaNacimiento() ? strval(date_format($datos->getFechaNacimiento(), "Y")) : ""),
                'fechaNacimiento_mes' => ($datos->getFechaNacimiento() ? strval(date_format($datos->getFechaNacimiento(), "m")): ""),
                'fechaNacimiento_dia' => ($datos->getFechaNacimiento() ? strval(date_format($datos->getFechaNacimiento(), "d")): ""),
                'nacionalidad' => $datos->getNacionalidad(),
                'direccion' => $datos->getDireccion()					
            );
        endforeach;
		
        $response = new Response(json_encode(array('persona' => $arreglo)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }
}