<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\comercialBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolHisto;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoDetalleSolMaterial;
use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\InfoDocumentoComunicacion;
use telconet\schemaBundle\Entity\InfoComunicacion;
use telconet\schemaBundle\Entity\AdmiMotivo;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

class MaterialesExcedentesController extends Controller implements TokenAuthenticatedController
{ 
    /**
	* @Secure(roles="ROLE_173-1")
	* @author Mario Ayerve E.  <mayerve@telconet.ec>
    * @version 1.1 17-03-2021 
    *          - Se agrego parametro para identificar la empresa.
    */
    public function indexAction()
    {           
        $peticion = $this->get('request');
        $session  = $peticion->getSession();
		
        $em = $this->getDoctrine()->getManager('telconet_general');
		
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("173", "1"); 
        $entityItemMenuPadre = $entityItemMenu->getItemMenuId(); 
		$session->set('menu_modulo_activo', $entityItemMenuPadre->getNombreItemMenu());
		$session->set('nombre_menu_modulo_activo', $entityItemMenuPadre->getTitleHtml());
		$session->set('id_menu_modulo_activo', $entityItemMenuPadre->getId());
		$session->set('imagen_menu_modulo_activo', $entityItemMenuPadre->getUrlImagen());  
       
        return $this->render('comercialBundle:materialesexcedentes:index.html.twig', array(
			 'item' => $entityItemMenu,
			 'empresaCod' => $session->get('prefijoEmpresa')
        ));
    } 
        
    /*
    * Llena el grid de consulta.
	* 
	* Se envian nuevos parametros para solicitud
	*
	* @author Mario Ayerve E.  <mayerve@telconet.ec>
    * @version 1.1 17-03-2021
    */
    /**
    * @Secure(roles="ROLE_173-7")
    */
    public function gridAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $fechaDesdePlanif = explode('T',$peticion->query->get('fechaDesdePlanif'));
        $fechaHastaPlanif = explode('T',$peticion->query->get('fechaHastaPlanif'));
        $fechaDesdeIngOrd = explode('T',$peticion->query->get('fechaDesdeIngOrd'));
        $fechaHastaIngOrd = explode('T',$peticion->query->get('fechaHastaIngOrd'));
        
        $login2 = $peticion->query->get('login2');
        $descripcionPunto = $peticion->query->get('descripcionPunto');
        $vendedor = $peticion->query->get('vendedor');
        $ciudad = $peticion->query->get('ciudad');
        $numOrdenServicio = $peticion->query->get('numOrdenServicio');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $em = $this->getDoctrine()->getManager("telconet");
		$codEmpresa = ($peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa') : "");
        $objJson = $this->getDoctrine()
            ->getManager("telconet")
            ->getRepository('schemaBundle:InfoDetalleSolicitud')
            ->generarJsonPendientesAprobacionSolMatExc($em, $start, $limit, $fechaDesdePlanif[0], $fechaHastaPlanif[0],
														$login2, $descripcionPunto, $vendedor, $ciudad, $numOrdenServicio,$codEmpresa);	
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
        
    /*
     * Llena el gridFactibilidadMaterialesAction de consulta.
     */
    /**
    * @Secure(roles="ROLE_173-89")
    */
    public function gridFactibilidadMaterialesAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $id_solicitud = $peticion->query->get('id_detalle_solicitud');        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $codEmpresa = ($peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa') : "");
        
        $em = $this->getDoctrine()->getManager("telconet");
        $em_soporte = $this->getDoctrine()->getManager("telconet_soporte");
        $em_naf = $this->getDoctrine()->getManager("telconet_naf");
        $em_infraestructura = $this->getDoctrine()->getManager("telconet_infraestructura");
        
		$InfoDetalleSolicitud=$em->getRepository('schemaBundle:InfoDetalleSolicitud')->find($id_solicitud);
        $InfoServicio=$InfoDetalleSolicitud->getServicioId();
        if($InfoServicio->getPlanId() != null)
        {
		    $nombrePlan = $InfoServicio->getPlanId()->getNombrePlan();
		}
		$infoServicioTecnico=$em->getRepository('schemaBundle:InfoServicioTecnico')->findOneByServicioId($InfoServicio->getId());
		$TipoMedio = $em_infraestructura->getRepository('schemaBundle:AdmiTipoMedio')->find($infoServicioTecnico->getUltimaMillaId());
		
		if($TipoMedio->getNombreTipoMedio()=="Cobre"){
			if(strrpos($nombrePlan, "ADSL"))
				$nombreProceso = "SOLICITAR NUEVO SERVICIO COBRE ADSL";
			else
				$nombreProceso = "SOLICITAR NUEVO SERVICIO COBRE VDSL";
		}else	
			$nombreProceso = "SOLICITAR NUEVO SERVICIO RADIO";
			
        $entityProceso = $em_soporte->getRepository('schemaBundle:AdmiProceso')->findOneByNombreProceso($nombreProceso);
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_soporte")
            ->getRepository('schemaBundle:InfoDetalleSolMaterial')
            ->generarJsonFactibilidad($em, $em_naf, $start, $limit, $id_solicitud, $entityProceso->getId(), $codEmpresa);
            // ->generarJsonFactibilidadLikeProceso($em, $em_naf, $start, $limit, $id_solicitud, $nombreProceso, $codEmpresa);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /**
    * @Secure(roles="ROLE_173-90")
    */
    public function getMotivosRechazoAction()
    {
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
		
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
		//cambiar ... no es accionId = 1 sino el de getMotivos
        $entitySeguRelacionSistema = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->findOneBy(array("moduloId"=>173, "accionId"=>90));
		$relacionSistemaId = $entitySeguRelacionSistema->getId() ? $entitySeguRelacionSistema->getId() : 0;     
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_general")
            ->getRepository('schemaBundle:AdmiMotivo')
            ->generarJson("","Activo",$start,$limit, $relacionSistemaId);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
        
    /**
    * @Secure(roles="ROLE_173-94")
    */
    public function rechazarAjaxAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $peticion = $this->get('request');
		$session  = $peticion->getSession();
        $id = $peticion->get('id');
        $id_motivo = $peticion->get('id_motivo');
        $observacion = $peticion->get('observacion');
        
        $em = $this->getDoctrine()->getManager();
        $em_general = $this->getDoctrine()->getManager("telconet_general");
        $em_comunicacion = $this->getDoctrine()->getManager("telconet_comunicacion");

        $entityDetalleSolicitud = $em->getRepository('schemaBundle:InfoDetalleSolicitud')->findOneById($id);
        $entityMotivo = $em_general->getRepository('schemaBundle:AdmiMotivo')->findOneById($id_motivo);
        
        $em->getConnection()->beginTransaction();
        $em_comunicacion->getConnection()->beginTransaction();		
		try {
	        if($entityDetalleSolicitud){     
				$entityServicio=$entityDetalleSolicitud->getServicioId();
				$entityPunto = $em->getRepository('schemaBundle:InfoPunto')->findOneById($entityServicio->getPuntoId());
				$entityPersonaEmpresaRol = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findOneById($entityPunto->getPersonaEmpresaRolId());
				
				// ACTUALIZO EL ESTADO DEL PROSPECTO
				if($entityPersonaEmpresaRol->getEstado()!='Activo'){
				      $entityPersonaEmpresaRol->setEstado('Activo');
				      $em->persist($entityPersonaEmpresaRol);
				      $em->flush();
				      
				      //REGISTRA EN LA TABLA DE PERSONA_EMPRESA_ROL_HISTO
				      $entity_persona_historial = new InfoPersonaEmpresaRolHisto();
				      $entity_persona_historial->setEstado($entityPersonaEmpresaRol->getEstado());
				      $entity_persona_historial->setFeCreacion(new \DateTime('now'));
				      $entity_persona_historial->setIpCreacion($peticion->getClientIp());
				      $entity_persona_historial->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
				      $entity_persona_historial->setUsrCreacion($session->get('user'));
				      $em->persist($entity_persona_historial);
				      $em->flush();
				 } 
				//ACTUALIZO EL ESTADO DEL SERVICIO
				$entityServicio->setEstado("Anulado");
				$em->persist($entityServicio);
				$em->flush();   	
			
				//GUARDAR INFO SERVICIO HISTORIAL
				$entityServicioHistorial = new InfoServicioHistorial();  
				$entityServicioHistorial->setServicioId($entityServicio);	
				$entityServicioHistorial->setIpCreacion($peticion->getClientIp());
				$entityServicioHistorial->setFeCreacion(new \DateTime('now'));
				$entityServicioHistorial->setUsrCreacion($peticion->getSession()->get('user'));	
				$entityServicioHistorial->setEstado('Anulado'); 
				$em->persist($entityServicioHistorial);
				$em->flush();       
			
	            //GUARDAR EL ESTADO DE LA SOLICITUD DE EXCEDENTES DE MATERIALES
				$entityDetalleSolicitud->setMotivoId($id_motivo);
	            $entityDetalleSolicitud->setObservacion($observacion);	
	            $entityDetalleSolicitud->setEstado("Rechazada");
	            $entityDetalleSolicitud->setUsrRechazo($peticion->getSession()->get('user'));		
	            $entityDetalleSolicitud->setFeRechazo(new \DateTime('now'));
	            $em->persist($entityDetalleSolicitud);
	            $em->flush();               
	            
	            //GUARDAR INFO DETALLE SOLICICITUD DE EXCEDENTES DE MATERIALES HISTORIAL
	            $entityDetalleSolHist = new InfoDetalleSolHist();
	            $entityDetalleSolHist->setDetalleSolicitudId($entityDetalleSolicitud);
	            $entityDetalleSolHist->setObservacion($observacion);
	            $entityDetalleSolHist->setMotivoId($id_motivo);
	            $entityDetalleSolHist->setIpCreacion($peticion->getClientIp());
	            $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
	            $entityDetalleSolHist->setUsrCreacion($peticion->getSession()->get('user'));
	            $entityDetalleSolHist->setEstado('Rechazada');
	            $em->persist($entityDetalleSolHist);
	            $em->flush();               
	            
				//ACTUALIZAR EL INFO DETALLE SOLICITUD -- DE FACTIBILIDAD				
				$entityTipoSolicitud =$em->getRepository('schemaBundle:AdmiTipoSolicitud')->findOneByDescripcionSolicitud("SOLICITUD FACTIBILIDAD");
				$entityDetalleSolicitudFactibilidad =$em->getRepository('schemaBundle:InfoDetalleSolicitud')->findUlitmoDetalleSolicitudByIds($entityServicio->getId(), $entityTipoSolicitud->getId());                    
				
				$observacionFact = "Rechazado desde Aprobacion de Materiales. <br><br>" . $observacion;
	            $entityDetalleSolicitudFactibilidad->setMotivoId($id_motivo);
	            $entityDetalleSolicitudFactibilidad->setObservacion($observacionFact);	
	            $entityDetalleSolicitudFactibilidad->setEstado("Rechazada");
	            $entityDetalleSolicitudFactibilidad->setUsrRechazo($peticion->getSession()->get('user'));		
	            $entityDetalleSolicitudFactibilidad->setFeRechazo(new \DateTime('now'));
	            $em->persist($entityDetalleSolicitudFactibilidad); 
	            $em->flush();               
	            
	            //GUARDAR INFO DETALLE SOLICICITUD FACTIBILDIAD HISTORIAL
	            $entityDetalleFactSolHist = new InfoDetalleSolHist();
	            $entityDetalleFactSolHist->setDetalleSolicitudId($entityDetalleSolicitudFactibilidad);
	            $entityDetalleFactSolHist->setObservacion($observacionFact);
	            $entityDetalleFactSolHist->setMotivoId($id_motivo);
	            $entityDetalleFactSolHist->setIpCreacion($peticion->getClientIp());
	            $entityDetalleFactSolHist->setFeCreacion(new \DateTime('now'));
	            $entityDetalleFactSolHist->setUsrCreacion($peticion->getSession()->get('user'));
	            $entityDetalleFactSolHist->setEstado('Rechazada');
	            $em->persist($entityDetalleFactSolHist);
	            $em->flush();              
				
	            //------- COMUNICACIONES --- NOTIFICACIONES 
	            $mensaje = $this->renderView('planificacionBundle:Factibilidad:notificacionMateriales.html.twig', 
	                                        array('detalleSolicitud' => $entityDetalleSolicitud,'motivo'=> $entityMotivo));
	            
	            $asunto  ="Rechazo de Solicitud de Materiales Excedentes de Instalacion #".$entityDetalleSolicitud->getId();
	            
	            $infoDocumento = new InfoDocumento();
	            $infoDocumento->setMensaje($mensaje);
	            $infoDocumento->setEstado('Activo');
	            $infoDocumento->setNombreDocumento($asunto);
	            $infoDocumento->setFeCreacion(new \DateTime('now'));
	            $infoDocumento->setUsrCreacion($peticion->getSession()->get('user'));
	            $infoDocumento->setIpCreacion($peticion->getClientIp());
	            $em_comunicacion->persist($infoDocumento);
	            $em_comunicacion->flush();

	            $infoComunicacion = new InfoComunicacion();
	            $infoComunicacion->setFeCreacion(new \DateTime('now'));
	            $infoComunicacion->setEstado('Activo');
	            $infoComunicacion->setUsrCreacion($peticion->getSession()->get('user'));
	            $infoComunicacion->setIpCreacion($peticion->getClientIp());
	            $em_comunicacion->persist($infoComunicacion);
	            $em_comunicacion->flush();

	            $infoDocumentoComunicacion = new InfoDocumentoComunicacion();
	            $infoDocumentoComunicacion->setComunicacionId($infoComunicacion);
	            $infoDocumentoComunicacion->setDocumentoId($infoDocumento);
	            $infoDocumentoComunicacion->setFeCreacion(new \DateTime('now'));
	            $infoDocumentoComunicacion->setEstado('Activo');
	            $infoDocumentoComunicacion->setUsrCreacion($peticion->getSession()->get('user'));
	            $infoDocumentoComunicacion->setIpCreacion($peticion->getClientIp());
	            $em_comunicacion->persist($infoDocumentoComunicacion);
	            $em_comunicacion->flush();
	                
	            //FALTA PONER DESTINATARIOS.... 
	            
				//ENVIO DE MAIL
				$message = \Swift_Message::newInstance()
					->setSubject($asunto)
					->setFrom('notificaciones_telcos@telconet.ec')
					->setTo('rortega@trans-telco.com')
					->setCc('notificaciones_telcos@telconet.ec')
					->setBody($mensaje,'text/html')
				;
				$this->get('mailer')->send($message);
				
				// ------- COMUNICACIONES --- NOTIFICACIONES 
				$mensaje = $this->renderView('planificacionBundle:Factibilidad:notificacion.html.twig', 
											array('detalleSolicitud' => $entityDetalleSolicitudFactibilidad,'motivo'=> $entityMotivo));
				
				$asunto  ="Aprobacion de Solicitud de Factibilidad de Instalacion #".$entityDetalleSolicitudFactibilidad->getId();
				
				$infoDocumento = new InfoDocumento();
				$infoDocumento->setMensaje($mensaje);
				$infoDocumento->setEstado('Activo');
				$infoDocumento->setNombreDocumento($asunto);
				$infoDocumento->setFeCreacion(new \DateTime('now'));
				$infoDocumento->setUsrCreacion($peticion->getSession()->get('user'));
				$infoDocumento->setIpCreacion($peticion->getClientIp());
				$em_comunicacion->persist($infoDocumento);
				$em_comunicacion->flush();

				$infoComunicacion = new InfoComunicacion();
				$infoComunicacion->setFeCreacion(new \DateTime('now'));
				$infoComunicacion->setEstado('Activo');
				$infoComunicacion->setUsrCreacion($peticion->getSession()->get('user'));
				$infoComunicacion->setIpCreacion($peticion->getClientIp());
				$em_comunicacion->persist($infoComunicacion);
				$em_comunicacion->flush();

				$infoDocumentoComunicacion = new InfoDocumentoComunicacion();
				$infoDocumentoComunicacion->setComunicacionId($infoComunicacion);
				$infoDocumentoComunicacion->setDocumentoId($infoDocumento);
				$infoDocumentoComunicacion->setFeCreacion(new \DateTime('now'));
				$infoDocumentoComunicacion->setEstado('Activo');
				$infoDocumentoComunicacion->setUsrCreacion($peticion->getSession()->get('user'));
				$infoDocumentoComunicacion->setIpCreacion($peticion->getClientIp());
				$em_comunicacion->persist($infoDocumentoComunicacion);
				$em_comunicacion->flush();
					
				// DESTINATARIOS.... 
				$formasContacto = $em->getRepository('schemaBundle:InfoPersona')->getContactosByLoginPersonaAndFormaContacto($entityServicio->getPuntoId()->getUsrVendedor(),'Correo Electronico');
				$to = array();
				$to[] = 'rortega@trans-telco.com';
				$cc = array();
				$cc[] = 'sac@trans-telco.com';
				$cc[] = 'notificaciones_telcos@telconet.ec';
				
				
				if($formasContacto){
					foreach($formasContacto as $formaContacto){
						$to[] = $formaContacto['valor'];
					}
				}
				
				// ENVIO DE MAIL
				$message = \Swift_Message::newInstance()
					->setSubject($asunto)
					->setFrom('notificaciones_telcos@telconet.ec')
					->setTo($to)
					->setCc($cc)
					->setBody($mensaje,'text/html')
				;
				
				$this->get('mailer')->send($message);				
			
				$em->getConnection()->commit();		
				$em_comunicacion->getConnection()->commit();
			
	            $respuesta->setContent("Se rechazo la solicitud de materiales excedentes");                      
	        }
	        else
	            $respuesta->setContent("No existe el detalle de solicitud");
	        
		} catch (\Exception $e) {
            // Rollback the failed transaction attempt
			$respuesta->setContent("No se pudo rechazar ".$e->getMessage());
			
            $em_comunicacion->getConnection()->rollback();
            $em_comunicacion->getConnection()->close();
			
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            //aqu? alg?n mensaje con la excepci?n concatenada
		}
		
        return $respuesta;
    }	    
    
    /**
    * @Secure(roles="ROLE_173-163")
    */
    public function aprobarAjaxAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $peticion = $this->get('request');
		$session  = $peticion->getSession();
        $id = $peticion->get('id');
        $observacion = $peticion->get('observacion');
        
        $em = $this->getDoctrine()->getManager();
		$em_comunicacion = $this->getDoctrine()->getManager("telconet_comunicacion");
        $entityDetalleSolicitud = $em->getRepository('schemaBundle:InfoDetalleSolicitud')->findOneById($id);
        
        $em->getConnection()->beginTransaction();
        $em_comunicacion->getConnection()->beginTransaction();
		
		try {			
	        if($entityDetalleSolicitud){
				$entityServicio=$em->getRepository('schemaBundle:InfoServicio')->findOneById($entityDetalleSolicitud->getServicioId());
				$entityPunto = $em->getRepository('schemaBundle:InfoPunto')->findOneById($entityServicio->getPuntoId());
				$entityPersonaEmpresaRol = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findOneById($entityPunto->getPersonaEmpresaRolId());
				
				// ACTUALIZO EL ESTADO DEL PROSPECTO
				if($entityPersonaEmpresaRol->getEstado()!='Activo'){
				    $entityPersonaEmpresaRol->setEstado('Activo');
				    $em->persist($entityPersonaEmpresaRol);
				    $em->flush();
				    
				    //REGISTRA EN LA TABLA DE PERSONA_EMPRESA_ROL_HISTO
				    $entity_persona_historial = new InfoPersonaEmpresaRolHisto();
				    $entity_persona_historial->setEstado($entityPersonaEmpresaRol->getEstado());
				    $entity_persona_historial->setFeCreacion(new \DateTime('now'));
				    $entity_persona_historial->setIpCreacion($peticion->getClientIp());
				    $entity_persona_historial->setPersonaEmpresaRolId($entityPersonaEmpresaRol);
				    $entity_persona_historial->setUsrCreacion($session->get('user'));
				    $em->persist($entity_persona_historial);
				    $em->flush();
				}
				//ACTUALIZO EL ESTADO DEL SERVICIO
				$entityServicio->setEstado("Factible");
				$em->persist($entityServicio);
				$em->flush();   	
			
				//GUARDAR INFO SERVICIO HISTORIAL
				$entityServicioHistorial = new InfoServicioHistorial();  
				$entityServicioHistorial->setServicioId($entityServicio);	
				$entityServicioHistorial->setIpCreacion($peticion->getClientIp());
				$entityServicioHistorial->setFeCreacion(new \DateTime('now'));
				$entityServicioHistorial->setUsrCreacion($peticion->getSession()->get('user'));	
				$entityServicioHistorial->setEstado('Factible'); 
				$em->persist($entityServicioHistorial);
				$em->flush(); 
				
				//GUARDO LA INFO DETALLE SOLICITUD DE EXCEDENTES DE MATERIALES
	            $entityDetalleSolicitud->setEstado("Aprobada");
				if($observacion)
					$entityDetalleSolicitud->setObservacion($observacion);
	            $em->persist($entityDetalleSolicitud);
	            $em->flush();  
	            
	            //GUARDAR INFO DETALLE SOLICICITUD DE EXCEDENTES DE MATERIALES HISTORIAL
	            $entityDetalleSolHist = new InfoDetalleSolHist();
	            $entityDetalleSolHist->setDetalleSolicitudId($entityDetalleSolicitud);
				if($observacion)
					$entityDetalleSolHist->setObservacion($observacion);	            
	            $entityDetalleSolHist->setIpCreacion($peticion->getClientIp());
	            $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
	            $entityDetalleSolHist->setUsrCreacion($peticion->getSession()->get('user'));
	            $entityDetalleSolHist->setEstado('Aprobada');  
	            $em->persist($entityDetalleSolHist);
	            $em->flush();   
				
				//ACTUALIZAR EL INFO DETALLE SOLICITUD -- DE FACTIBILIDAD				
				$entityTipoSolicitud =$em->getRepository('schemaBundle:AdmiTipoSolicitud')->findOneByDescripcionSolicitud("SOLICITUD FACTIBILIDAD");
				$entityDetalleSolicitudFactibilidad =$em->getRepository('schemaBundle:InfoDetalleSolicitud')->findUlitmoDetalleSolicitudByIds($entityServicio->getId(), $entityTipoSolicitud->getId());                    
				
				$observacionFact = "Aprobacion de Solicitud de excedentes de Materiales.";
	            $entityDetalleSolicitudFactibilidad->setObservacion($observacionFact);	
	            $entityDetalleSolicitudFactibilidad->setEstado("Factible");
				if($observacion)
					$entityDetalleSolicitudFactibilidad->setObservacion($observacion);	            
	            $em->persist($entityDetalleSolicitudFactibilidad); 
	            $em->flush();               
	            
	            //GUARDAR INFO DETALLE SOLICICITUD FACTIBILDIAD HISTORIAL
	            $entityDetalleFactSolHist = new InfoDetalleSolHist();
	            $entityDetalleFactSolHist->setDetalleSolicitudId($entityDetalleSolicitudFactibilidad);
	            $entityDetalleFactSolHist->setObservacion($observacionFact);
	            $entityDetalleFactSolHist->setIpCreacion($peticion->getClientIp());
				if($observacion)
					$entityDetalleFactSolHist->setObservacion($observacion);	            
	            $entityDetalleFactSolHist->setFeCreacion(new \DateTime('now'));
	            $entityDetalleFactSolHist->setUsrCreacion($peticion->getSession()->get('user'));
	            $entityDetalleFactSolHist->setEstado('Factible');
	            $em->persist($entityDetalleFactSolHist);
	            $em->flush();    
	            
				//------- COMUNICACIONES --- NOTIFICACIONES 
	            $mensaje = $this->renderView('planificacionBundle:Factibilidad:notificacionMateriales.html.twig', 
	                                        array('detalleSolicitud' => $entityDetalleSolicitud,'motivo'=> null));
	            
	            $asunto  ="Aprobacion de Solicitud de Materiales Excedentes de Instalacion #".$entityDetalleSolicitud->getId();
	            
	            $infoDocumento = new InfoDocumento();
	            $infoDocumento->setMensaje($mensaje);
	            $infoDocumento->setEstado('Activo');
	            $infoDocumento->setNombreDocumento($asunto);
	            $infoDocumento->setFeCreacion(new \DateTime('now'));
	            $infoDocumento->setUsrCreacion($peticion->getSession()->get('user'));
	            $infoDocumento->setIpCreacion($peticion->getClientIp());
	            $em_comunicacion->persist($infoDocumento);
	            $em_comunicacion->flush();

	            $infoComunicacion = new InfoComunicacion();
	            $infoComunicacion->setFeCreacion(new \DateTime('now'));
	            $infoComunicacion->setEstado('Activo');
	            $infoComunicacion->setUsrCreacion($peticion->getSession()->get('user'));
	            $infoComunicacion->setIpCreacion($peticion->getClientIp());
	            $em_comunicacion->persist($infoComunicacion);
	            $em_comunicacion->flush();

	            $infoDocumentoComunicacion = new InfoDocumentoComunicacion();
	            $infoDocumentoComunicacion->setComunicacionId($infoComunicacion);
	            $infoDocumentoComunicacion->setDocumentoId($infoDocumento);
	            $infoDocumentoComunicacion->setFeCreacion(new \DateTime('now'));
	            $infoDocumentoComunicacion->setEstado('Activo');
	            $infoDocumentoComunicacion->setUsrCreacion($peticion->getSession()->get('user'));
	            $infoDocumentoComunicacion->setIpCreacion($peticion->getClientIp());
	            $em_comunicacion->persist($infoDocumentoComunicacion);
	            $em_comunicacion->flush();
	                
	            //FALTA PONER DESTINATARIOS.... 
	            
				//ENVIO DE MAIL
				$message = \Swift_Message::newInstance()
					->setSubject($asunto)
					->setFrom('notificaciones_telcos@telconet.ec')
					->setTo('rortega@trans-telco.com')
					->setCc('notificaciones_telcos@telconet.ec')
					->setBody($mensaje,'text/html')
				;
				$this->get('mailer')->send($message);
				
				// ------- COMUNICACIONES --- NOTIFICACIONES 
				$mensaje = $this->renderView('planificacionBundle:Factibilidad:notificacion.html.twig', 
											array('detalleSolicitud' => $entityDetalleSolicitudFactibilidad,'motivo'=> null));
				
				$asunto  ="Aprobacion de Solicitud de Factibilidad de Instalacion #".$entityDetalleSolicitudFactibilidad->getId();
				
				$infoDocumento = new InfoDocumento();
				$infoDocumento->setMensaje($mensaje);
				$infoDocumento->setEstado('Activo');
				$infoDocumento->setNombreDocumento($asunto);
				$infoDocumento->setFeCreacion(new \DateTime('now'));
				$infoDocumento->setUsrCreacion($peticion->getSession()->get('user'));
				$infoDocumento->setIpCreacion($peticion->getClientIp());
				$em_comunicacion->persist($infoDocumento);
				$em_comunicacion->flush();

				$infoComunicacion = new InfoComunicacion();
				$infoComunicacion->setFeCreacion(new \DateTime('now'));
				$infoComunicacion->setEstado('Activo');
				$infoComunicacion->setUsrCreacion($peticion->getSession()->get('user'));
				$infoComunicacion->setIpCreacion($peticion->getClientIp());
				$em_comunicacion->persist($infoComunicacion);
				$em_comunicacion->flush();

				$infoDocumentoComunicacion = new InfoDocumentoComunicacion();
				$infoDocumentoComunicacion->setComunicacionId($infoComunicacion);
				$infoDocumentoComunicacion->setDocumentoId($infoDocumento);
				$infoDocumentoComunicacion->setFeCreacion(new \DateTime('now'));
				$infoDocumentoComunicacion->setEstado('Activo');
				$infoDocumentoComunicacion->setUsrCreacion($peticion->getSession()->get('user'));
				$infoDocumentoComunicacion->setIpCreacion($peticion->getClientIp());
				$em_comunicacion->persist($infoDocumentoComunicacion);
				$em_comunicacion->flush();
					
				// DESTINATARIOS.... 
				$formasContacto = $em->getRepository('schemaBundle:InfoPersona')->getContactosByLoginPersonaAndFormaContacto($entityServicio->getPuntoId()->getUsrVendedor(),'Correo Electronico');
				$to = array();
				$to[] = 'rortega@trans-telco.com';
				$cc = array();
				$cc[] = 'sac@trans-telco.com';
				$cc[] = 'notificaciones_telcos@telconet.ec';
				
				
				if($formasContacto){
					foreach($formasContacto as $formaContacto){
						$to[] = $formaContacto['valor'];
					}
				}
				
				// ENVIO DE MAIL
				$message = \Swift_Message::newInstance()
					->setSubject($asunto)
					->setFrom('notificaciones_telcos@telconet.ec')
					->setTo($to)
					->setCc($cc)
					->setBody($mensaje,'text/html')
				;
				
				$this->get('mailer')->send($message);				
			
				$respuesta->setContent("Se Aprobo la Solicitud de Materiales Excedentes con Exito");  
				
				$em->getConnection()->commit();		
				$em_comunicacion->getConnection()->commit();
	        }
	        else
	            $respuesta->setContent("No existe el detalle de solicitud");
	            		
		 } catch (\Exception $e) {
            // Rollback the failed transaction attempt
			$respuesta->setContent("No se pudo aprobar:".$e->getMessage());
			
			$em_comunicacion->getConnection()->rollback();
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            //aqu? alg?n mensaje con la excepci?n concatenada
		}
		
        return $respuesta;
    }
    
    /**    
     * Documentación para el método 'aprobarSolicitudMateriales'.
     *
     * Descripcion: Función que apruba solicitud de excedente de material
     *              actualizando el estado de la solicitud, registra historial
     *              de solicitud y de servicio. Adicional envía mail de notificación
     *              al asesor y crea tarea a factucación para el descuento respectivo.
     * 
     * @author  Karen Rodríguez V. <kyrodriguez@telconet.ec>
     * @version 1.0 18-03-2021     
     * 
     * @param array $arrayParametros[intIdSolicitud     (integer)  =>    Numero de solicitud.
     *                               strObservacion     (string)   =>    Observación de la solicitud.
     *                               strEstado          (string)   =>    Estado de inicio.
     *                               strUsrCreacion     (string)   =>    Usuario de creación.
     *                               strIpCreacion      (string)   =>    Ip de creación.
     * 
     * @author Liseth Candelario <lcandelario@telconet.ec>
     * @version 1.1 14/11/2021  
     * Cambios: Se modifica la función de autorizar la solicitud de excedentes, 
     *          convirtiendola en funciones (que contienen otros procesos) para así poder llamarlas de manera global.
     *          Todas las funciones están en comercial.SolicitudesService.
     *                           
     * @return array $arrayRespuesta
     */
    public function aprobarSolicitudMaterialesAction()
    {
        $objResponse        = new Response();
        $serviceUtil        = $this->get('schema.Util');
        $objResponse->headers->set('Content-Type', 'text/plain');
        $objRequest         = $this->getRequest();
        $objSession         = $objRequest->getSession();
        $intIdSolicitud     = $objRequest->get('idSolicitud'); //Solicitud de excedende
        $intIdServicio      = $objRequest->get('idServicio');
        $strClienteIp       = $objRequest->getClientIp();
        $strObservacion     = $objRequest->get('observacion');
        $strUsrCreacion     = $objSession->get('user');
        $intIdEmpresa       = $objSession->get('idEmpresa');
        $strPrefijoEmpresa  = $objSession->get('prefijoEmpresa');
        $serviceSolicitud   = $this->get('comercial.Solicitudes');
        
        try 
        {
             $arrayParametros = array(
                                    'strObservacion'          => $strObservacion,
                                    'idSolicitudExce'         => $intIdSolicitud,
                                    'idEmpresa'               => $intIdEmpresa,
                                    'codEmpresa'              => $intIdEmpresa,
                                    'usuario'                 => $strUsrCreacion,
                                    'ip'                      => $strClienteIp,
                                    'prefijoEmpresa'          => $strPrefijoEmpresa,
                                    'intIdServicio'           => $intIdServicio
                                    );
            $arrayRespuesta  = $serviceSolicitud->aprobarSolicitudMateriales($arrayParametros);
            if($arrayRespuesta['status'] === 'ERROR')
            {
                throw new \Exception($arrayRespuesta['mensaje'].', notificar a Sistemas');
            }
            else
            {
                $arrayRespuesta['status'] = 'OK';
            }
	} 
        catch (\Exception $e) 
        {
           $serviceUtil->insertError('Telcos+', 
                                     'MaterialesExcedenteController->aprobarSolicitudMaterialesAction', 
                                     $e->getMessage(), 
                                     $objSession->get('user'), 
                                     $strClienteIp
                                    );
            $objResponse = new Response(json_encode(array('mensaje' => $e->getMessage())));
            $objResponse->headers->set('Content-type', 'text/json');
            return $objResponse;
        }
		
        $objResponse = new Response(json_encode(array('mensaje' => $arrayRespuesta['mensaje'],
                                                      'estado'  => $arrayRespuesta['status'])));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    
    /**    
     * Documentación para el método 'rechazarSolicitudMateriales'.
     *
     * Descripcion: Función que rechaza solicitud de excedente de material
     *              actualizando el estado de la solicitud, registra historial
     *              de solicitud y de servicio. Adicional envía mail de notificación
     *              al asesor y crea tarea a factucación para el descuento respectivo.
     * 
     * @author  Karen Rodríguez V. <kyrodriguez@telconet.ec>
     * @version 1.0 18-03-2021     
     * 
     * @param array $arrayParametros[intIdSolicitud     (integer)  =>    Numero de solicitud.
     *                               strObservacion     (string)   =>    Observación de la solicitud.
     *                               strEstado          (string)   =>    Estado de inicio.
     *                               strUsrCreacion     (string)   =>    Usuario de creación.
     *                               strIpCreacion      (string)   =>    Ip de creación.
     * 
     * @author Liseth Candelario <lcandelario@telconet.ec>
     * @version 1.1 14/11/2021
     * 
     * Cambios: Se modifica la función de rechazar la solicitud de excedentes, 
     *          convirtiendola en funciones (que contienen otros procesos) para así poder llamarlas de manera global.
     *          Todas las funciones están en comercial.SolicitudesService.
     * 
     * @return array $arrayRespuesta
     */
    public function rechazarSolicitudMaterialesAction()
    {
        $objResponse        = new Response();
        $serviceUtil        = $this->get('schema.Util');
        $objResponse->headers->set('Content-Type', 'text/plain');
        $objRequest         = $this->getRequest();
        $objSession         = $objRequest->getSession();
        $intIdSolicitud     = $objRequest->get('idSolicitud'); //Solicitud de excedende
        $intIdServicio      = $objRequest->get('idServicio');
        $strClienteIp       = $objRequest->getClientIp();
        $strObservacion     = $objRequest->get('observacion');
        $strUsrCreacion     = $objSession->get('user');
        $intIdEmpresa       = $objSession->get('idEmpresa');
        $strPrefijoEmpresa  = $objSession->get('prefijoEmpresa');
        $intIdMotivo        = $objRequest->get('id_motivo');
        
        $serviceSolicitud   = $this->get('comercial.Solicitudes');
        
        try 
        {	
             $arrayParametros = array(
                                    'strObservacion'          => $strObservacion,
                                    'idSolicitudExce'         => $intIdSolicitud,
                                    'idEmpresa'               => $intIdEmpresa,
                                    'codEmpresa'              => $intIdEmpresa,
                                    'usuario'                 => $strUsrCreacion,
                                    'ip'                      => $strClienteIp,
                                    'prefijoEmpresa'          => $strPrefijoEmpresa,
                                    'intIdServicio'           => $intIdServicio,
                                    'intIdMotivo'             => $intIdMotivo
                                    );
            $arrayRespuesta  = $serviceSolicitud->rechazarSolicitudMateriales($arrayParametros);
            if($arrayRespuesta['status'] === 'ERROR')
            {
                throw new \Exception(' Al rechazar la solicitud, por favor comunicar a Sistemas.<br>'
                                      .$arrayRespuesta['mensaje']);
            }
            else
            {
                $arrayRespuesta['status'] = 'OK';
            }               
	} 
       catch (\Exception $e)
        {
           $serviceUtil->insertError('Telcos+', 
                                     'MaterialesExcedenteController->rechazarSolicitudMaterialesAction', 
                                     $e->getMessage(), 
                                     $objSession->get('user'), 
                                     $strClienteIp
                                    );
            $objResponse = new Response(json_encode(array('mensaje' => 'ERROR'.$e->getMessage())));
            $objResponse->headers->set('Content-type', 'text/json');
            return $objResponse;
        }
		
        $objResponse = new Response(json_encode(array('mensaje' => $arrayRespuesta['mensaje'],
                                                      'estado'  => $arrayRespuesta['status'])));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }


     /**     
     * 
     * Documentación para el método 'getSeguimientoMaterialesExcedentesAction'.
     * 
     * Obtiene los detalles de un determinado mantenimiento del transporte
     * 
     * @return Response.
     * 
     * @author Mario Ayerve <mayerve@telconet.ec>
     * @version 1.0 01-03-2021
     * 
     */ 
    public function getSeguimientoMaterialesExcedentesAction()
    {
        $objRespuesta            = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $objPeticion             = $this->get('request');
	$intIdDetalle            = $objPeticion->query->get('idDetalleSolicitud');
        $emComercial                     = $this->getDoctrine()->getManager();
        $arrayParametros = array();
        $arrayParametros["idDetalleSolicitud"]           = $intIdDetalle;  
        try
        {
            $arrayDSH = $emComercial->getRepository('schemaBundle:InfoDetalleSolHist')
                                ->getDetalleSolicitudHistorial($intIdDetalle);

            foreach ($arrayDSH as $ObjDetalleSolHist)
            {
                $arrayEncontrados[]=array('observacion'  => $ObjDetalleSolHist->getObservacion(),
                                                                 'feCreacion'   => date_format($ObjDetalleSolHist->getFeCreacion(), 'Y-m-d H:i:s'),
                                                                 'usrCreacion'  => $ObjDetalleSolHist->getUsrCreacion(),
                                                                 'estado'       => $ObjDetalleSolHist->getEstado()
                                                                );
            }
        }
        catch(\Excepction $e)
        {
            $arrayEncontrados['error'] = $e->getMessage();
        }
        $objRespuesta->setContent(json_encode($arrayEncontrados));
        return $objRespuesta;
	}
	



     /**
     * 
     * Funcion que sirve para ingresar el seguimiento de Materiales Excedentes
     * 
     * @author Mario Ayerve <mayerve@telconet.ec>
     * @since 1.0
     * @version 1.1 04-03-2021
     *
     */
    public function IngresarSeguimientoMaterialesExcedentesAction()
    {
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        
        $objPeticion        = $this->get('request');
        $objSession         = $objPeticion->getSession();
        $intIdEmpresa       = $objSession->get('idEmpresa');
        $strPrefijoEmpresa  = $objSession->get('prefijoEmpresa');
        $intIdPunto         = $objPeticion->get('id_punto');
        $strLogin	    = $objPeticion->get('login');
        $intIdDetalle       = $objPeticion->get('id_factibilidad');
        $strVendedor	    = $objPeticion->get('vendedor');
        $strProducto	    = $objPeticion->get('producto');
        $strEmpresa	    = $objPeticion->get('empresa');
        $strSeguimiento     = $objPeticion->get('seguimiento');
        $strPantallaDe      = $objPeticion->get('pantallaDe');
        $strUsrCreacion     = $objSession->get('user');
        $strIpCreacion      = $objPeticion->getClientIp();
		
	try
	{
            $arrayParametros = array(
                                        'idEmpresa'     => $intIdEmpresa,
                                        'prefijoEmpresa'=> $strPrefijoEmpresa,
                                        'idPunto'  	=> $intIdPunto,
                                        'login'         => $strLogin,
                                        'idDetalle'     => $intIdDetalle,
                                        'seguimiento'   => $strSeguimiento,
                                        'vendedor'      => $strVendedor,
                                        'producto'      => $strProducto,
                                        'empresa'       => $strEmpresa,
                                        'usrCreacion'   => $strUsrCreacion,
                                        'pantallaDe'    => $strPantallaDe,
                                        'ipCreacion'    => $strIpCreacion
                                    );

            /* @var $serviceSoporte SoporteService */
            $serviceMateriales = $this->get('planificacion.Materiales');
            //---------------------------------------------------------------------*/

            //COMUNICACION CON LA CAPA DE NEGOCIO (SERVICE)-------------------------*/
                    $arrayRespuesta = $serviceMateriales->ingresarSeguimientoMaterialesExcedentes($arrayParametros);
            //----------------------------------------------------------------------*/
	}
	catch(\Exception $e)
	{
		$arrayRespuesta['error'] = $e->getMessage();
	}
        //--------RESPUESTA-----------------------------------------------------*/
        $objResultado = json_encode($arrayRespuesta);
        //----------------------------------------------------------------------*/
        $objRespuesta->setContent($objResultado);
        
        return $objRespuesta;
	}
	

     /**
     * multipleFileUploadAction
     *
     * Metodo encargado de procesar el o los archivos que el usuario desea subir a los casos y tareas
     *
     * @return json con resultado del proceso
     *
     * @author Mario Ayerve <mayerve@telconet.ec>
     * @version 1.0 12-03-2021 
     */ 
    public function multipleFileUploadAction()
    {
        $objRequest     = $this->get('request');
        $objResponse    = new Response();
        $objResponse->headers->set('Content-Type', 'text/html');
        
		$intIdSolicitud        = $objRequest->get('idSolicitud') ? $objRequest->get('idSolicitud'):"N";
		$strOrigenMateriales   = $objRequest->get('origenMateriales') ? $objRequest->get('origenMateriales'):"N";
        $intServicio           = $objRequest->get('servicio') ? $objRequest->get('servicio'): 0;
        $objSession            = $objRequest->getSession();
        $strPrefijoEmpresa     = $objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa') : "";
        $strUser               = $objSession->get('user') ? $objSession->get('user') : "";
        $strIdEmpresa          = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : "";
		
		try
		{
			$arrayArchivos      = $this->getRequest()->files->get('archivos');

			$arrayParametros     = array(
				"idTarea"				=> $intIdSolicitud,
				"origenMateriales"      => $strOrigenMateriales,
				"servicio"              => $intServicio,
				"strPrefijoEmpresa"     => $strPrefijoEmpresa,
				"strUser"               => $strUser,
				"strIdEmpresa"          => $strIdEmpresa,
				"arrayArchivos"         => $arrayArchivos
			);

			$servicePlanifica = $this->get('planificacion.Planificar');
			$arrayRespuesta = $servicePlanifica->guardarAdjuntoImagen($arrayParametros);
			$strResultado   = '{"success": '.$arrayRespuesta['success'].', "respuesta":"'.$arrayRespuesta['mensaje'].'"}';
			$objResponse->setContent($strResultado);
		}
		catch(\Exception $e)
		{
			$objResponse->setContent('{"success": "false", "respuesta":"'.$e->getMessage().'"}');
		}
        return $objResponse;
	}
	
	/**
     * getDocumentosMaterialesExcedAction
     *
     * Metodo encargado de obtener los archivos (imagenes) enrutados a la solicitud
     *
     *
     * @author Mario Ayerve <mayerve@telconet.ec>
     * @version 1.0 12-03-2021 
     */ 
	public function getDocumentosMaterialesExcedAction()
    {
        $strRespuesta 		= new Response();
        $strRespuesta->headers->set('Content-Type', 'text/json');
        $strPeticion        = $this->get('request');
        $objSession         = $strPeticion->getSession();
        $emSoporte     		= $this->getDoctrine()->getManager("telconet");
        $emInfraestructura  = $this->getDoctrine()->getManager("telconet_infraestructura");        
		$intIdServicio      = $strPeticion->get("idServicio");
		

        $arrayParametrosDoc                         = array();
		$arrayParametrosDoc["intIdServicio"]   = $intIdServicio;
		
		try
        {

			$objJson = $emSoporte->getRepository('schemaBundle:InfoDetalleSolMaterial')
					->getVerificaDocumentosMaterialesExcedentes(  $arrayParametrosDoc,
																	$emInfraestructura,
																	$objSession->get('user')
																);
			
			foreach ($objJson['registros'] as $arrayDatosDoc)
			{
				$arrayEncontrados[]=array('idDocumento'     => $arrayDatosDoc['id'],
									'ubicacionLogica' 	   => $arrayDatosDoc['ubicacionLogica'],
									'feCreacion'          => ($arrayDatosDoc["feCreacion"] ? 
															strval(date_format($arrayDatosDoc["feCreacion"],"d-m-Y H:i")) : ""),
									'usrCreacion'         => $arrayDatosDoc['usrCreacion'],
									'linkVerDocumento'    => $arrayDatosDoc['linkVerDocumento'],
									'boolEliminarDocumento'      => "N");
			}	
			$strRespuesta->setContent(json_encode($arrayEncontrados)); 
		}
		catch(\Exception $e)
		{
			$arrayRespuesta['error'] = $e->getMessage() . "- No se puede obtener la imagen";
			$strRespuesta->setContent(json_encode($arrayRespuesta));
		}
        return $strRespuesta;
	}
	


	/**
     * eliminarDocumentoRegistroYArchivoAction
     *
     * Metodo encargado de eliminar el o los archivos (imagenes) enrutados a la solicitud
     *
     *
     * @author Mario Ayerve <mayerve@telconet.ec>
     * @version 1.0 12-03-2021 
     */ 
	public function eliminarDocumentoRegistroYArchivoAction()
    {
        $objResponse    = new Response();
        $objResponse->headers->set('Content-Type', 'text/plain');
        $emComunicacion = $this->getDoctrine()->getManager('telconet_comunicacion');
        $objRequest     = $this->getRequest();
        $objSession     = $objRequest->getSession();
        $strUserSession = $objSession->get('user');
        $strStatus      = "";
        $strMessage     = "";
        
        $intIdDocumento    = $objRequest->get('id');
        
        $emComunicacion->getConnection()->beginTransaction();
        try
        {
            $objDocumento           = $emComunicacion->getRepository('schemaBundle:InfoDocumento')->find($intIdDocumento);
            if (!$objDocumento) 
            {
                throw $this->createNotFoundException('No se ha podido encontrar el documento solicitado');
            }
            
            $objDocumentoRelacion   = $emComunicacion->getRepository('schemaBundle:InfoDocumentoRelacion')->findOneByDocumentoId($intIdDocumento);
            if (!$objDocumentoRelacion) 
            {
                throw $this->createNotFoundException('No se ha podido encontrar la relación del documento con la tarea');
            }
            //Se valida si el usuario del documento es diferente al de sesión
            if ($strUserSession != $objDocumento->getUsrCreacion())
            {
                throw $this->createNotFoundException('No se ha podido eliminar archivo, solo puede eliminar archivos propios');
            }
            $strUbicacionFisica     = $objDocumento->getUbicacionFisicaDocumento();
            $objDocumento->setEstado("Eliminado");
            $objDocumento->setFeCreacion(new \DateTime('now'));
            $objDocumento->setUsrCreacion($strUserSession);
            $emComunicacion->persist($objDocumento);
            $emComunicacion->flush();
            
            $objDocumentoRelacion->setEstado('Eliminado');
            $emComunicacion->persist($objDocumentoRelacion);
            $emComunicacion->flush();
            
            $strPathTelcos  = $this->container->getParameter('path_telcos')."telcos/web";
            
            if(strrpos($strUbicacionFisica, $strPathTelcos) === false) 
            {
                $strUbicacionFisica   = $strPathTelcos."/".$strUbicacionFisica;
            }
            unlink($strUbicacionFisica);
            
            $strMessage .= "Se ha eliminado correctamente el archivo!";
            $strStatus  .= "OK";
            
            $emComunicacion->commit();
        } 
        catch (\Exception $e) 
        {
            if($emComunicacion->getConnection()->isTransactionActive())
            {
                $emComunicacion->rollback();
                $emComunicacion->close();
            }

            $strMessage .= "Ha ocurrido un problema. <br/>Por favor informe a Sistemas.";
            if (strrpos($e->getMessage(),'solo puede eliminar archivos propios'))
            {
                $strMessage = $e->getMessage();
            }
            error_log($strMessage);
        }
        
        $strResultado    = '{"status":"'.$strStatus.'","message":"'.$strMessage.'"}';
        $objResponse->setContent($strResultado); 
        return $objResponse;
    }
}
