<?php

namespace telconet\soporteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\schemaBundle\Form\PlantillaNotificacionExternaType;
use telconet\schemaBundle\Form\PlantillaNotificacionInternaType;
use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\InfoDocumentoComunicacion;
use telconet\schemaBundle\Entity\InfoComunicacion;
use telconet\soporteBundle\Service\PlantillaService;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response; 
use JMS\SecurityExtraBundle\Annotation\Secure;

class PlantillaController extends Controller implements TokenAuthenticatedController
{
    /**
    * @Secure(roles="ROLE_84-1")
    */     
    public function indexAction()
    {
        $request  = $this->get('request');
        $session  = $request->getSession();

        $em = $this->getDoctrine()->getManager('telconet_comunicacion');   
        $em_seguridad = $this->getDoctrine()->getManager("telconet_seguridad");            
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("84", "1");    	
        $entityItemMenuPadre = $entityItemMenu->getItemMenuId(); 
		$session->set('menu_modulo_activo', $entityItemMenuPadre->getNombreItemMenu());
		$session->set('nombre_menu_modulo_activo', $entityItemMenuPadre->getTitleHtml());
		$session->set('id_menu_modulo_activo', $entityItemMenuPadre->getId());
		$session->set('imagen_menu_modulo_activo', $entityItemMenuPadre->getUrlImagen());         

        return $this->render('soporteBundle:Plantilla:index.html.twig', array(
            'item' => $entityItemMenu
		));
    }
    
    /**
    * @Secure(roles="ROLE_84-2")
    */     
    public function newAction()
    {
        $session = $this->get('request')->getSession();
		
        $em_seguridad = $this->getDoctrine()->getManager("telconet_seguridad");            
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("84", "1");  	
        $entityItemMenuPadre = $entityItemMenu->getItemMenuId(); 
		$session->set('menu_modulo_activo', $entityItemMenuPadre->getNombreItemMenu());
		$session->set('nombre_menu_modulo_activo', $entityItemMenuPadre->getTitleHtml());
		$session->set('id_menu_modulo_activo', $entityItemMenuPadre->getId());
		$session->set('imagen_menu_modulo_activo', $entityItemMenuPadre->getUrlImagen());     
		
        $form   = $this->createForm(new PlantillaNotificacionExternaType());
        
        return $this->render('soporteBundle:Plantilla:new.html.twig', array(
            'item' => $entityItemMenu,
            'form'   => $form->createView()
        ));
    }
    
	/**
     * 
     * @author David De La Cruz <ddelacruz@telconet.ec>
     * @version 1.1 
     * @since 06-01-2022 Se agrega reemplazo de caracteres especiales en el texto de la plantilla
     * 
     * @Secure(roles="ROLE_84-3")
     */         
    public function createAction()
    {
        $emComunicacion = $this->getDoctrine()->getManager('telconet_comunicacion');
        $em_seguridad = $this->getDoctrine()->getManager("telconet_seguridad");
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("84", "1");
        $emGeneral= $this->getDoctrine()->getManager('telconet_general');
        
        $request = $this->getRequest();
        $peticion = $this->get('request');
        $sessio = $peticion->getSession();
        $empresaCod = $sessio->get('idEmpresa');
        $strPrefijoEmpresa = $sessio->get('prefijoEmpresa');
        

        $path = $this->container->getParameter('path_telcos');
        $strHost = $this->container->getParameter('host');

        $form = $this->createForm(new PlantillaNotificacionExternaType());
        $form->handleRequest($request);

        if($form->isValid())
        {
            $emComunicacion->getConnection()->beginTransaction();
            // Try and make the transaction
            try
            {
                
                $strApp            = 'TELCOS';
                $strModulo         = 'SOPORTE';
                $strSubModulo      = 'NOTIFICACIONES';
                
                
                $objAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')->findOneBy(array(
                                             'nombreParametro' => 'RUTAS_ARCHIVOS',
                                             'estado'          => 'Activo'
                                            ));
                
                if (!is_object($objAdmiParametroCab)) 
                {
                    throw $this->createNotFoundException('Configuración de rutas no existente');
                }
               
                
                $objRutaSufijo       = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                 ->findOneBy(array(
                                                                'parametroId' => $objAdmiParametroCab,
                                                                'valor1'      => $strApp,
                                                                'valor2'      => $strModulo,
                                                                'valor3'      => $strSubModulo,
                                                                'estado'      => 'Activo'
                                                 ));
                if (!is_object($objRutaSufijo))
                {
                    throw $this->createNotFoundException('Configuración Sufijo de rutas no existente');
                }
                
                $objRutaPrefijo      = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                 ->findOneBy(array(
                                                                'parametroId' => $objAdmiParametroCab,
                                                                'valor2'      => $strPrefijoEmpresa,
                                                                'estado'      => 'Activo'
                                                 ));
                if (!is_object($objRutaPrefijo))
                {
                    throw $this->createNotFoundException('Configuración Prefijo <' . $strPrefijoEmpresa .  '> de rutas no existente');
                }
                
                $objRutaServer       = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                 ->findOneBy(array(
                                                                'parametroId' => $objAdmiParametroCab,
                                                                'valor4'      => $strPrefijoEmpresa,
                                                                'estado'      => 'Activo'
                                                 ));
                
                if (!is_object($objRutaServer))
                {
                    throw $this->createNotFoundException('Configuración de ruta server no existente');
                }
                
                
                $strRutaPrefijo = $objRutaPrefijo->getValor4();
                $strRutaSufijo  = $objRutaSufijo->getValor4();
                $strRutaServer  = $objRutaServer->getValor5();
                
                $strRuta        = $strRutaPrefijo . $strRutaSufijo;
                $strRutaLocal   = "https://".$strHost."/public/uploads/" . $strRuta;
                
                $parametros = $peticion->get('telconet_schemabundle_plantillaNotificacionExternatype');

                $tipo = $parametros['tipo'];  //Se obtiene el tipo de la plantilla obtenido del comboBox
                //$tipoPlantilla=1 -> correo $tipoPlantilla=44 -> sms
                $clase = $emComunicacion->getRepository("schemaBundle:AdmiClaseDocumento")->findOneById($tipo);

                
                $strPlantillaMail = $parametros['plantilla_mail'];
                
                $strMensaje = str_replace($strRutaLocal, $strRutaServer, $strPlantillaMail);

                $strPatronABuscar = '/[^a-zA-Z0-9._\-<>= &\/":$¿?¡!;@]/';
                $strCaracterReemplazo = '';
                $strMensaje = preg_replace($strPatronABuscar,$strCaracterReemplazo,$strMensaje);
                                
                $infoDocumento = new InfoDocumento();
                $infoDocumento->setMensaje($tipo == 1 ? $strMensaje : $parametros['plantilla_sms']);
                $infoDocumento->setNombreDocumento($parametros['nombrePlantilla']);
                $infoDocumento->setClaseDocumentoId($clase);

                $infoDocumento->setFeCreacion(new \DateTime('now'));
                $infoDocumento->setEstado("Activo");
                $infoDocumento->setUsrCreacion($peticion->getSession()->get('user'));
                $infoDocumento->setIpCreacion($peticion->getClientIp());
                $infoDocumento->setEmpresaCod($empresaCod);
                $emComunicacion->persist($infoDocumento);
                $emComunicacion->flush();

                $boolTodoBien = true;

                if($boolTodoBien)
                {
                    $emComunicacion->getConnection()->commit();
                    return $this->redirect($this->generateUrl('plantilla_show', array('id' => $infoDocumento->getId())));
                }
                else
                {
                    $emComunicacion->getConnection()->rollback();
                    $emComunicacion->getConnection()->close();

                    $parametros = array(
                        'item' => $entityItemMenu,
                        'entity' => $infoDocumento,
                        'form' => $form->createView()
                    );

                    return $this->render('soporteBundle:Plantilla:new.html.twig', $parametros);
                }//FIN ERROR
            }
            catch(Exception $e)
            {
                // Rollback the failed transaction attempt
                $emComunicacion->getConnection()->rollback();
                $emComunicacion->getConnection()->close();
            }
        }

        $parametros = array(
            'item' => $entityItemMenu,
            'entity' => $infoDocumento,
            'form' => $form->createView()
        );

        return $this->render('soporteBundle:Plantilla:new.html.twig', $parametros);
    }

    /**
    * @Secure(roles="ROLE_84-6")
    */         
    public function showAction($id)
    {
        $session = $this->get('request')->getSession();
		
        $emComunicacion = $this->getDoctrine()->getManager('telconet_comunicacion');
        $em_seguridad = $this->getDoctrine()->getManager("telconet_seguridad");    
        $emGeneral= $this->getDoctrine()->getManager('telconet_general');        
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("84", "1"); 
        $entityItemMenuPadre = $entityItemMenu->getItemMenuId(); 
		$session->set('menu_modulo_activo', $entityItemMenuPadre->getNombreItemMenu());
		$session->set('nombre_menu_modulo_activo', $entityItemMenuPadre->getTitleHtml());
		$session->set('id_menu_modulo_activo', $entityItemMenuPadre->getId());
		$session->set('imagen_menu_modulo_activo', $entityItemMenuPadre->getUrlImagen());    
	
        
        $strPrefijoEmpresa = $session->get('prefijoEmpresa');
        $strHost           = $this->container->getParameter('host');
        $objDocumento      = new InfoDocumento();
        
        $objDocumento = $emComunicacion->getRepository('schemaBundle:InfoDocumento')->find($id);
	if (!$objDocumento) 
        {
            throw $this->createNotFoundException('No se encuentra la plantilla.');
        }
        
        $strApp            = 'TELCOS';
        $strModulo         = 'SOPORTE';
        $strSubModulo      = 'NOTIFICACIONES';
                
                
        $objAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')->findOneBy(array(
                                  'nombreParametro' => 'RUTAS_ARCHIVOS',
                                  'estado'          => 'Activo'
                                  ));
                
        if (!is_object($objAdmiParametroCab)) 
        {
            throw $this->createNotFoundException('Configuración de rutas no existente');
        }
               
                
        $objRutaSufijo       = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                         ->findOneBy(array(
                                                     'parametroId' => $objAdmiParametroCab,
                                                     'valor1'      => $strApp,
                                                     'valor2'      => $strModulo,
                                                     'valor3'      => $strSubModulo,
                                                     'estado'      => 'Activo'
                                           ));
        if (!is_object($objRutaSufijo))
        {
            throw $this->createNotFoundException('Configuración Sufijo de rutas no existente');
        }
                
        $objRutaPrefijo      = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                         ->findOneBy(array(
                                                     'parametroId' => $objAdmiParametroCab,
                                                     'valor2'      => $strPrefijoEmpresa,
                                                     'estado'      => 'Activo'
                                            ));
        if (!is_object($objRutaPrefijo))
        {
            throw $this->createNotFoundException('Configuración Prefijo <' . $strPrefijoEmpresa .  '> de rutas no existente');
        }
                
        $objRutaServer       = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                         ->findOneBy(array(
                                                     'parametroId' => $objAdmiParametroCab,
                                                      'valor4'      => $strPrefijoEmpresa,
                                                      'estado'      => 'Activo'
                                            ));
                
        if (!is_object($objRutaServer))
        {
            throw $this->createNotFoundException('Configuración de ruta server no existente');
        }
                
        $strRutaPrefijo = $objRutaPrefijo->getValor4();
        $strRutaSufijo  = $objRutaSufijo->getValor4();
        $strRutaServer  = $objRutaServer->getValor5();
                
        $strRuta        = $strRutaPrefijo . $strRutaSufijo;
        $strRutaLocal   = "https://".$strHost."/public/uploads/" . $strRuta;
        
        $strMensaje    = $objDocumento->getMensaje();
                
        $strMensajeFinal = str_replace($strRutaServer,$strRutaLocal,$strMensaje);
        
        $objDocumento->setMensaje($strMensajeFinal);
        
        $parametros=array(
            'item' => $entityItemMenu,
            'documento'   => $objDocumento
        );
        
        return $this->render('soporteBundle:Plantilla:show.html.twig', $parametros);
    }
    
    /**
     * @Secure(roles="ROLE_84-7")
     * 
     * Documentación para el método 'gridAction'.
     * 
     * Obtiene el listado de plantillas filtrado por Tipo, Nombre, Usuario Creación(Login), y estado.
     * 
     * @return Response Listado de Plantillas.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 26-07-2016
     * @since   1.0
     * Se obtiene y envía el nuevo parámetro LOGIN para el filtro del usuario creación
     */
    public function gridAction()
    {
        ini_set('max_execution_time', 99999999);
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        $ssesion = $peticion->getSession();
        
        $codEmpresa = $ssesion->get('idEmpresa');
        
        $intStart  = $peticion->query->get('start');
        $intLimit  = $peticion->query->get('limit');
        $strTipo   = $peticion->query->get('tipo');
        $strNombre = $peticion->query->get('nombre');
        $strLogin  = $peticion->query->get('login');
        $strEstado = $peticion->query->get('estado');
        
        // Obtener listado de plantillas servicio PlantillaService
        /* @var servicioPlantilla PlantillaService */
        $servicePlantilla  = $this->get('soporte.ListaPlantilla');
        $strJsonPlantillas = $servicePlantilla->listarPlantillas($strTipo, $strNombre, $strEstado, $codEmpresa, $intStart, $intLimit, '', $strLogin);
        $respuesta->setContent($strJsonPlantillas);
        return $respuesta;
    }
    
    /**
    * @Secure(roles="ROLE_84-8")
    */         
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet_comunicacion');
        $request = $this->getRequest();

        $entity = $em->getRepository('schemaBundle:InfoComunicacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SistAccion entity.');
        }

        $entity->setEstado("Eliminado");
        $entity->setFeCreacion(new \DateTime('now'));
        $entity->setUsrCreacion($request->getSession()->get('user'));
        $em->flush();

        return $this->redirect($this->generateUrl('callactivity'));
    }
    
    /**
    * @Secure(roles="ROLE_84-8")
    */     
    public function deleteAjaxAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $peticion = $this->get('request');
        
        $parametro = $peticion->get('param');
        
        $em = $this->getDoctrine()->getManager("telconet_comunicacion");
        $array_valor = explode("|",$parametro);
        foreach($array_valor as $id):
            
            if (null == $entity = $em->find('schemaBundle:InfoDocumento', $id)) {
                $respuesta->setContent("No existe la entidad");
            }
            else{
                $entity->setEstado("Eliminado");
                $em->persist($entity);
                
                $em->flush();
                $respuesta->setContent("Se elimino la entidad");
            }
        endforeach;
        //        $respuesta->setContent($id);
        
        return $respuesta;
    }
    
    /**
     * 
     * @author David De La Cruz <ddelacruz@telconet.ec>
     * @version 1.1 
     * @since 06-01-2022 Se agrega reemplazo de caracteres especiales en el texto de la plantilla
     * 
     * @Secure(roles="ROLE_84-4")
     */     
    public function editAction($id)
    {
        $session = $this->get('request')->getSession();
		
        $emComunicacion = $this->getDoctrine()->getManager('telconet_comunicacion');
        $em_seguridad = $this->getDoctrine()->getManager("telconet_seguridad");
        $emGeneral    = $this->getDoctrine()->getManager('telconet_general');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("84", "1");  
        
        $entityItemMenuPadre = $entityItemMenu->getItemMenuId(); 
		$session->set('menu_modulo_activo', $entityItemMenuPadre->getNombreItemMenu());
		$session->set('nombre_menu_modulo_activo', $entityItemMenuPadre->getTitleHtml());
		$session->set('id_menu_modulo_activo', $entityItemMenuPadre->getId());
		$session->set('imagen_menu_modulo_activo', $entityItemMenuPadre->getUrlImagen());   
        
                
        $strPrefijoEmpresa = $session->get('prefijoEmpresa');
        $strHost           = $this->container->getParameter('host');
        $objDocumento      = new InfoDocumento();
                
        $objDocumento = $emComunicacion->getRepository('schemaBundle:InfoDocumento')->find($id);       
        
        if (!$objDocumento) 
        {
            throw $this->createNotFoundException('Unable to find Plantilla entity.');
        }
        
        $strApp            = 'TELCOS';
        $strModulo         = 'SOPORTE';
        $strSubModulo      = 'NOTIFICACIONES';
                
                
        $objAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')->findOneBy(array(
                                  'nombreParametro' => 'RUTAS_ARCHIVOS',
                                  'estado'          => 'Activo'
                                  ));
                
        if (!is_object($objAdmiParametroCab)) 
        {
            throw $this->createNotFoundException('Configuración de rutas no existente');
        }
               
                
        $objRutaSufijo       = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                         ->findOneBy(array(
                                                     'parametroId' => $objAdmiParametroCab,
                                                     'valor1'      => $strApp,
                                                     'valor2'      => $strModulo,
                                                     'valor3'      => $strSubModulo,
                                                     'estado'      => 'Activo'
                                           ));
        if (!is_object($objRutaSufijo))
        {
            throw $this->createNotFoundException('Configuración Sufijo de rutas no existente');
        }
                
        $objRutaPrefijo      = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                         ->findOneBy(array(
                                                     'parametroId' => $objAdmiParametroCab,
                                                     'valor2'      => $strPrefijoEmpresa,
                                                     'estado'      => 'Activo'
                                            ));
        if (!is_object($objRutaPrefijo))
        {
            throw $this->createNotFoundException('Configuración Prefijo <' . $strPrefijoEmpresa .  '> de rutas no existente');
        }
                
        $objRutaServer       = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                         ->findOneBy(array(
                                                     'parametroId' => $objAdmiParametroCab,
                                                      'valor4'      => $strPrefijoEmpresa,
                                                      'estado'      => 'Activo'
                                            ));
                
        if (!is_object($objRutaServer))
        {
            throw $this->createNotFoundException('Configuración de ruta server no existente');
        }
                
        $strRutaPrefijo = $objRutaPrefijo->getValor4();
        $strRutaSufijo  = $objRutaSufijo->getValor4();
        $strRutaServer  = $objRutaServer->getValor5();
                
        $strRuta        = $strRutaPrefijo . $strRutaSufijo;
        $strRutaLocal   = "https://".$strHost."/public/uploads/" . $strRuta;
        
        $strMensaje    = $objDocumento->getMensaje();
                
        $strMensajeFinal = str_replace($strRutaServer,$strRutaLocal,$strMensaje);

        $strPatronABuscar = '/[^a-zA-Z0-9._\-<>= &\/":$¿?¡!;@]/';
        $strCaracterReemplazo = '';
        $strMensajeFinal = preg_replace($strPatronABuscar,$strCaracterReemplazo,$strMensajeFinal);
        
        $objDocumento->setMensaje($strMensajeFinal);
        
        $form = $this->createForm(new PlantillaNotificacionExternaType());
        
        return $this->render('soporteBundle:Plantilla:edit.html.twig', array(
            'item' => $entityItemMenu,
            'form'   => $form->createView(),
            'documento' =>$objDocumento,           
        ));
    }
    
    /**
    * @Secure(roles="ROLE_84-5")
    */     
    public function updateAction($id)
    {    
        $request = $this->getRequest();
        $peticion = $this->get('request');
        $objSesion         = $peticion->getSession();
        $strHost           = $this->container->getParameter('host');
        $strPrefijoEmpresa = $objSesion->get('prefijoEmpresa');
        
        $form    = $this->createForm(new PlantillaNotificacionExternaType());
        $form->handleRequest($request);
		
		$emComunicacion = $this->getDoctrine()->getManager('telconet_comunicacion');
        $em_seguridad = $this->getDoctrine()->getManager("telconet_seguridad");            
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("84", "1"); 
        $emGeneral= $this->getDoctrine()->getManager('telconet_general');
        
        if ($form->isValid()) {
            $emComunicacion->getConnection()->beginTransaction();
            $documento = $emComunicacion->getRepository('schemaBundle:InfoDocumento')->find($id);
            // Try and make the transaction
            try {
                
                $strApp            = 'TELCOS';
                $strModulo         = 'SOPORTE';
                $strSubModulo      = 'NOTIFICACIONES';
                
                
                $objAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')->findOneBy(array(
                                             'nombreParametro' => 'RUTAS_ARCHIVOS',
                                             'estado'          => 'Activo'
                                            ));
                
                if (!is_object($objAdmiParametroCab)) 
                {
                    throw $this->createNotFoundException('Configuración de rutas no existente');
                }
               
                
                $objRutaSufijo       = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                 ->findOneBy(array(
                                                                'parametroId' => $objAdmiParametroCab,
                                                                'valor1'      => $strApp,
                                                                'valor2'      => $strModulo,
                                                                'valor3'      => $strSubModulo,
                                                                'estado'      => 'Activo'
                                                 ));
                if (!is_object($objRutaSufijo))
                {
                    throw $this->createNotFoundException('Configuración Sufijo de rutas no existente');
                }
                
                $objRutaPrefijo      = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                 ->findOneBy(array(
                                                                'parametroId' => $objAdmiParametroCab,
                                                                'valor2'      => $strPrefijoEmpresa,
                                                                'estado'      => 'Activo'
                                                 ));
                if (!is_object($objRutaPrefijo))
                {
                    throw $this->createNotFoundException('Configuración Prefijo <' . $strPrefijoEmpresa .  '> de rutas no existente');
                }
                
                $objRutaServer       = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                 ->findOneBy(array(
                                                                'parametroId' => $objAdmiParametroCab,
                                                                'valor4'      => $strPrefijoEmpresa,
                                                                'estado'      => 'Activo'
                                                 ));
                
                if (!is_object($objRutaServer))
                {
                    throw $this->createNotFoundException('Configuración de ruta server no existente');
                }
                
                
                $strRutaPrefijo = $objRutaPrefijo->getValor4();
                $strRutaSufijo  = $objRutaSufijo->getValor4();
                $strRutaServer  = $objRutaServer->getValor5();
                
                $strRuta        = $strRutaPrefijo . $strRutaSufijo;
                $strRutaLocal   = "https://".$strHost."/public/uploads/" . $strRuta;
                
                
                $parametros = $peticion->get('telconet_schemabundle_plantillaNotificacionExternatype');
                
                $tipo = $parametros['tipo'];  //Se obtiene el tipo de la plantilla obtenido del comboBox
                                               
                $clase = $emComunicacion->getRepository("schemaBundle:AdmiClaseDocumento")->findOneById($tipo);
                
                $strPlantillaMail = $parametros['plantilla_mail'];
                
                $strMensaje = str_replace($strRutaLocal, $strRutaServer, $strPlantillaMail);
                
                $documento->setMensaje($tipo==1?$strMensaje:$parametros['plantilla_sms']);
                $documento->setNombreDocumento($parametros['nombrePlantilla']);
                $documento->setClaseDocumentoId($clase);
                $documento->setEstado("Modificado");
                
                $emComunicacion->persist($documento);
                $emComunicacion->flush();
                
		      $boolTodoBien = true;
		      if($boolTodoBien)
		      {
			      $emComunicacion->getConnection()->commit();
			      return $this->redirect($this->generateUrl('plantilla_show', array('id' => $documento->getId())));
		      }
		      else
		      {
			      $emComunicacion->getConnection()->rollback();
			      $emComunicacion->getConnection()->close();
			      
			      $parametros=array(
				      'item'   => $entityItemMenu,
				      'entity' => $documento,
				      'form'   => $form->createView()
			      );
			      
			      return $this->render('soporteBundle:Plantilla:new.html.twig', $parametros);
		      }//FIN ERROR
				             
            } catch (Exception $e) {
                // Rollback the failed transaction attempt
                $emComunicacion->getConnection()->rollback();
                $emComunicacion->getConnection()->close();
            }
        }

        $parametros=array(
            'item'   => $entityItemMenu,
            'entity' => $documento,
            'form'   => $form->createView()
        );
		
        return $this->render('soporteBundle:Plantilla:new.html.twig', $parametros);
    }
    
    /**
    * Metodo encargado de procesar imagenes que pueden ser incluidas en Plantillas, utilizan el microservicio 
    * de NFS desde la opción Notificaciones > Plantilla > Nueva Plantilla
    *
    * @return json con resultado del proceso
    * 
    * @author David De La Cruz <ddelacruz@telconet.ec>
    * @version 1.1 22-06-2021 Se realizan ajustes para subir archivos al NFS, además se renombran 
    * archivos que tienen caracteres especiales y espacios antes de ser subidos
    * 
    * @Secure(roles="ROLE_84-525")
    */ 
   public function fileUploadAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/html');

        $strPath = $this->container->getParameter("path_telcos") . "telcos/web/public/uploads/imagesPlantilla/";

        $request = $this->getRequest();
        $objSession = $request->getSession();
        
        $strPrefijoEmpresa = $objSession->get('prefijoEmpresa');
        $strUser = $objSession->get('user');
        $strEmpresaCod = $objSession->get('idEmpresa');
        $objServiceUtil = $this->get('schema.Util');        
        $strIp = $request->getClientIp();
        $emComunicacion = $this->getDoctrine()->getManager('telconet_comunicacion');
        
        $file = $request->files;
        $boolEsExito = false;

        $emComunicacion->getConnection()->beginTransaction();

        if($file && count($file) > 0)
        {
            $objArchivo = $file->get('archivo');

            if(isset($objArchivo) && count($objArchivo) > 0)
            {
                $strTipo = $objArchivo->guessExtension(); //extension		
                $strArchivo = $objArchivo->getClientOriginalName();
                $strTamanio = $objArchivo->getClientSize();
                $fltTamanioKb = round(((int)$strTamanio)/1024,2);

                $arrayArchivo = explode('.', $strArchivo);
                $countArray = count($arrayArchivo);
                $nombreArchivo = $arrayArchivo[0];
                $extArchivo = $arrayArchivo[$countArray - 1];

                $prefijo = substr(md5(uniqid(rand())), 0, 6);

                if($strArchivo != "")
                {
                    $strNuevoNombre = $nombreArchivo . "_" . $prefijo . "." . $extArchivo;
                    $strPatronABuscar = '/[^a-zA-Z0-9._-]/';
                    $strCaracterReemplazo = '_';
                    $strNuevoNombre = preg_replace($strPatronABuscar,$strCaracterReemplazo,$strNuevoNombre);
                    
                    $strFile         = base64_encode(file_get_contents($objArchivo));
                    $arrayParamNfs   = array(
                                            'prefijoEmpresa'       => $strPrefijoEmpresa,
                                            'strApp'               => "TelcosWeb",
                                            'arrayPathAdicional'   => [],
                                            'strBase64'            => $strFile,
                                            'strNombreArchivo'     => $strNuevoNombre,
                                            'strUsrCreacion'       => $strUser,
                                            'strSubModulo'         => "Plantillas");
                    
                    $arrayRespNfs = $objServiceUtil->guardarArchivosNfs($arrayParamNfs);

                    if ($arrayRespNfs['intStatus'] == 200 )
                    {
                        $strUrlArchivo = $arrayRespNfs['strUrlArchivo'];

                        $entityClaseDocumento = $emComunicacion->getRepository('schemaBundle:AdmiClaseDocumento')->findOneBy(
                            array('nombreClaseDocumento' => 'Notificacion Ext. Plantilla - Imagen normal'));
                        $objInfoDocumento = new InfoDocumento();
                        $objInfoDocumento->setMensaje("Imagen de tamanio normal para asociar al modulo de Plantillas");
                        $objInfoDocumento->setNombreDocumento($strNuevoNombre);
                        $objInfoDocumento->setClaseDocumentoId($entityClaseDocumento);
                        $objInfoDocumento->setUbicacionLogicaDocumento($strTipo.' '.$fltTamanioKb.'Kb');
                        $objInfoDocumento->setUbicacionFisicaDocumento($strUrlArchivo);
                        $objInfoDocumento->setFeCreacion(new \DateTime('now'));
                        $objInfoDocumento->setEstado("Activo");
                        $objInfoDocumento->setUsrCreacion($strUser);
                        $objInfoDocumento->setIpCreacion($strIp);
                        $objInfoDocumento->setEmpresaCod($strEmpresaCod);
                        $emComunicacion->persist($objInfoDocumento);
                        $emComunicacion->flush();
                        
                        $emComunicacion->getConnection()->commit();
                        $objResultado = '{"success":true,"fileName":"' . $strNuevoNombre . '","fileSize":' . $fltTamanioKb . '}';
                        $boolEsExito = true;

                    }                        
                }
            }//FIN IF ARCHIVO SUBIDO
        }//FIN IF FILES
        if (!$boolEsExito)
        {
            $emComunicacion->getConnection()->rollback();
            $emComunicacion->getConnection()->close();
            $objResultado = '{"success":false,"message":"Ocurrio un error al procesar la imagen en el sistema","fileSize":' . 0 . '}'; 
        }

        $respuesta->setContent($objResultado);
        return $respuesta;
    }

    /**
    * Metodo encargado de enlistar imágenes que pueden ser incluidas en Plantillas, 
    * desde la opción Notificaciones > Plantilla > Nueva Plantilla
    * 
    * @return json con resultado del proceso
    *
    * @author David De La Cruz <ddelacruz@telconet.ec>
    * @version 1.1 22-06-2021 Se realizan ajustes para consultar las imágenes que han sido subidas a NFS
    * desde el módulo de Plantilla
    * 
	* @Secure(roles="ROLE_84-526")
	*/ 	
   public function listarArchivosAction()
   {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $host = $this->container->getParameter('host');
        
        $objRequest        = $this->getRequest();
        $objSession        = $objRequest->getSession();
        $strEmpresaCod = $objSession->get('idEmpresa');
        $emComunicacion = $this->getDoctrine()->getManager('telconet_comunicacion');

        $entityClaseDocumentoNormal = $emComunicacion->getRepository('schemaBundle:AdmiClaseDocumento')
            ->findOneBy(array('nombreClaseDocumento' => 'Notificacion Ext. Plantilla - Imagen normal'));

        $arrayParametros = array('tipo'    => $entityClaseDocumentoNormal->getId(),
                                 'empresa' => $strEmpresaCod,
                                 'estado'  => 'Activo');

        $arrayImagenes = $emComunicacion->getRepository('schemaBundle:InfoDocumento')->getDocumentos($arrayParametros,'','');

        if( $arrayImagenes['total'] > 0 )
        {
            foreach($arrayImagenes['registros'] as $objImagenNormal)
            {
                $strNombreImagenNormal = $objImagenNormal->getNombreDocumento();

                $arrayDetalleImagen = explode(' ', $objImagenNormal->getUbicacionLogicaDocumento());
                $strRutaImagen = $objImagenNormal->getUbicacionFisicaDocumento();
                $strPesoImagen = $arrayDetalleImagen[1];
                $arrayNombreImagen = explode('.', $strNombreImagenNormal);
                $strNombreImagen = $arrayNombreImagen[0];
                $strExtensionImagen = strtoupper($arrayNombreImagen[1]);
                $strExtensionImagenOficial = strtoupper($arrayNombreImagen[1]);

                $arrayImagenesPlantillas[] = array(
                    'name' => $strNombreImagen,
                    'thumb' => $strNombreImagenNormal,
                    'imagen_mini' => $strRutaImagen,
                    'imagen_media' => $strRutaImagen,
                    'host_base' => $host,
                    'url' => $strRutaImagen,
                    'ext' => $strExtensionImagen,
                    'type' => 'image',
                    'extension' => $strExtensionImagenOficial,
                    'dimension' => '-',
                    'peso' => $strPesoImagen,
                );
            }
        }

        $objResultado = json_encode($arrayImagenesPlantillas);

        $respuesta->setContent($objResultado);
        return $respuesta;
    }

    /********************************************************************************/
	//		OBTENER LAS PLANTILLAS DE SMS O CORREO PARA BUSQUEDA
	/*********************************************************************************/
	public function getTipoPlantillaAction(){
	 
	      $respuesta = new Response();
	      $respuesta->headers->set('Content-Type', 'text/json');
	      
	      $peticion = $this->get('request');
	      
	      $queryNombre = $peticion->query->get('query') ? $peticion->query->get('query') : "";
	      $nombre = ($queryNombre != '' ? $queryNombre : $peticion->query->get('nombre'));
	      $estado = $peticion->query->get('estado');
	   
	      $start = $peticion->query->get('start');
	      $limit = $peticion->query->get('limit');
	      
	      $objJson = $this->getDoctrine()
		  ->getManager("telconet_comunicacion")
		  ->getRepository('schemaBundle:AdmiClaseDocumento')
		  ->generarJsonEntidades($nombre,$estado,$start,$limit);
		  
	      $respuesta->setContent($objJson);
	      	     
	      
	      return $respuesta;
	     
	
	}
}