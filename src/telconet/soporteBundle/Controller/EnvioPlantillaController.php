<?php

namespace telconet\soporteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\InfoDocumentoComunicacion;
use telconet\schemaBundle\Entity\InfoComunicacion;
use telconet\schemaBundle\Entity\InfoDocumentoComunHistorial;
use telconet\schemaBundle\Entity\InfoDestinatario;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response; 
use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\SecurityExtraBundle\Annotation\Secure;

use \PHPExcel;
use \PHPExcel_IOFactory;
use \PHPExcel_Shared_Date;
use \PHPExcel_Style_NumberFormat;
use \PHPExcel_Worksheet_PageSetup;
use \PHPExcel_CachedObjectStorageFactory;
use \PHPExcel_Settings;

class EnvioPlantillaController extends Controller implements TokenAuthenticatedController
{    
    /**
     * @Secure(roles="ROLE_175-1")
     *
     * Documentacion para el método 'indexAction'
     *
     * Metodo de redireccionamiento para llamar a la pagina index.html.twig
     *
     * @return $request
     * @version 1.0
     *
     * @author: Jorge Guerrero <jguerrerop@telconet.ec>
     * @version 1.1 25-09-2017 Se agrega los permisos de la empresa para MD
     *
     * @author Jorge Guerrero<jguerrerop@telconet.ec>
     * @version 1.2 01-12-2017
     * Se agrega el parametro por empresa configurado en la admi_parametro
     *
     */     
    public function indexAction()
    {
        $request  = $this->get('request');
        $session  = $request->getSession();

        $em = $this->getDoctrine()->getManager('telconet_comunicacion');
        $em_seguridad = $this->getDoctrine()->getManager("telconet_seguridad");            
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("175", "1");
        $entityItemMenuPadre = $entityItemMenu->getItemMenuId(); 
		$session->set('menu_modulo_activo', $entityItemMenuPadre->getNombreItemMenu());
		$session->set('nombre_menu_modulo_activo', $entityItemMenuPadre->getTitleHtml());
		$session->set('id_menu_modulo_activo', $entityItemMenuPadre->getId());
		$session->set('imagen_menu_modulo_activo', $entityItemMenuPadre->getUrlImagen());   
	
	$strPrefijoEmpresa = $session->get('prefijoEmpresa');
        $strEmpresaCod     = $session->get('idEmpresa');

        $arrayParametros['strPrefijoEmpresa'] = $strPrefijoEmpresa;
        $arrayParametros['strEmpresaCod']     = $strEmpresaCod;

        $serviceComercial   = $this->get('comercial.Comercial');
        $strAplicaCiclosFac = $serviceComercial->aplicaCicloFacturacion($arrayParametros);
        
        if ($strAplicaCiclosFac == 'S' )
        {
            $arrayEmpresaPermitida['PERMISOS_EMPRESA'] = true;
        }
        else
        {
            $arrayEmpresaPermitida['PERMISOS_EMPRESA'] = false;
        }

        return $this->render('soporteBundle:EnvioPlantilla:index.html.twig', array(
            'item' => $entityItemMenu,
            'empresa'=>$strPrefijoEmpresa,
            'boolPermisoEmpresa' => $arrayEmpresaPermitida
		));
    }
    
    
   /**
    * gridAction
    *
    * Método encargado de obtener el JSON con los servicios a notificar
    *
    * @return json $respuesta
    *
    * @author Desarrollo Inicial
    * @version 1.0
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.1 27-02-2019
    *
    * @Secure(roles="ROLE_175-7")
    */
    public function gridAction()
    {
	  $respuesta = new Response();
	  $respuesta->headers->set('Content-Type', 'text/json');
	  
	  $peticion = $this->get('request');
	      
	  $emComercial= $this->getDoctrine()->getManager('telconet');
	  $emFinaciero= $this->getDoctrine()->getManager('telconet_financiero');
	  $emInfraestructura = $this->getDoctrine()->getManager("telconet_infraestructura");
	  
	  $empresaCod = $peticion->getSession()->get('prefijoEmpresa');  
	
	  $parametros = array();  
	  
	  $parametros['tipoFiltrosModulo'] = $peticion->query->get('tipoFiltroModulo');   //tecnico, financiero o comercial                
	  
	  if($parametros['tipoFiltrosModulo'] == 'Tecnico')
	  {        		  
		  
		$parametros["oficina"] = $peticion->query->get('oficina') ? $peticion->query->get('oficina') : "";
		$parametros["login2"] = $peticion->query->get('login2') ? $peticion->query->get('login2') : "";
		$parametros["ciudad_punto"] = $peticion->query->get('ciudad_punto') ? $peticion->query->get('ciudad_punto') : "";
		$parametros["direccion_punto"] = $peticion->query->get('direccion_punto') ? $peticion->query->get('direccion_punto') : "";      
		$parametros["estado_punto"] = $peticion->query->get('estado_punto') ? $peticion->query->get('estado_punto') : "";
		$parametros["estado_servicio"] = $peticion->query->get('estado_servicio') ? $peticion->query->get('estado_servicio') : "";
		$parametros["jurisdiccionPe"] = $peticion->query->get('jurisdiccionPe') ? $peticion->query->get('jurisdiccionPe') : "";
		$parametros["nombrePe"] = $peticion->query->get('pe') ? $peticion->query->get('pe') : "";
		$parametros["protocolo"] = $peticion->query->get('protocolo') ? $peticion->query->get('protocolo') : "";
		$parametros["servicio"] = $peticion->query->get('servicio') ? $peticion->query->get('servicio') : "";
		$parametros["ciudad"] = $peticion->query->get('ciudad') ? $peticion->query->get('ciudad') : "";

		$idTipoElemento = $peticion->query->get('tipoElemento') ? $peticion->query->get('tipoElemento') : "";
		$idElemento     = $peticion->query->get('elemento') ? $peticion->query->get('elemento') : "";	
		
		$parametros['tipoElemento'] = $idTipoElemento;
		$parametros['elemento'] = $idElemento;		      		      
		
		$idsElementos = "";
				
		
		if($idElemento && $idElemento!=""){			      
			$idsElementos = "($idElemento)";			     					      			      			
		}else if($idTipoElemento!=""){			      			      
		
			$encontrados = $this->getDoctrine()
					->getManager("telconet_infraestructura")
					->getRepository('schemaBundle:InfoElemento')
					->getElementosPorTipo("Activo",$idTipoElemento,'','',$empresaCod);			      			      
			
			if ($encontrados) {						  
			    
			    $i=0;
			    $idsElementos = "(";
			    
			    foreach ($encontrados as $entidad)
			    {
				if($i < count($encontrados)-1)
				      $idsElementos .= $entidad->getId().",";				      							      
				else $idsElementos .= $entidad->getId();
				
				$i++;
				
			    }
			    $idsElementos .= ")";
			}
		
		}		      		      
		
		$parametros['elementos'] = $idsElementos;		      		      		      		      
													    
	  
	  }
	  else
	  {		  
		  
		$parametros["forma_pago"] = $peticion->query->get('forma_pago') ? $peticion->query->get('forma_pago') : "";
		$parametros["servicios_por"] = $peticion->query->get('servicios_por') ? $peticion->query->get('servicios_por') : "";
		$parametros["producto_plan"] = $peticion->query->get('producto_plan') ? $peticion->query->get('producto_plan') : "";
		$parametros["estado_servicio_comercial"] = $peticion->query->get('estado_servicio_comercial') ? $peticion->query->get('estado_servicio_comercial') : "";
				    
	  }
	  
	  $parametros["empresaCod"] = $peticion->getSession()->get('idEmpresa');
						  
	  $start = $peticion->query->get('start');
	  $limit = $peticion->query->get('limit');	    
      
	  $objJson = $this->getDoctrine()
	      ->getManager()
	      ->getRepository('schemaBundle:InfoDocumento')
	      ->generarJsonEnvioNotificacionPorCriterios($emComercial, $emFinaciero, $emInfraestructura, $parametros,$start,$limit);
	      
	  $respuesta->setContent($objJson);      
	  
	  return $respuesta;
    }     
    
    /**
    * @Secure(roles="ROLE_175-91")
    */  
    public function getElementosAction()
    {
        $respuesta  = new Response();
        $parametros = array();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
	    $queryNombre = $peticion->query->get('query') ? $peticion->query->get('query') : "";
        $nombre = ($queryNombre != '' ? $queryNombre : $peticion->query->get('nombre'));		
        $elemento = $peticion->query->get('elemento');
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        $empresaCod = $peticion->getSession()->get('idEmpresa');                
							
        $em_infraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
                
        $entityTipoElemento = $em_infraestructura->getRepository('schemaBundle:AdmiTipoElemento')->findOneByNombreTipoElemento($elemento);
	
	    $tipoElemento = $entityTipoElemento->getId() ? $entityTipoElemento->getId() : '';

        $parametros["nombre"]       = $nombre;
        $parametros["estado"]       = 'Activo';
        $parametros["tipoElemento"] = $tipoElemento;
        $parametros["codEmpresa"]   = $empresaCod;
        $parametros["start"]        = $start;
        $parametros["limit"]        = $limit;
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:InfoElemento')
            ->generarJsonElementosXTipo($parametros);
        $respuesta->setContent($objJson);
                
        return $respuesta;
    }    
    
    
    public function getElementosPorTipoAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
	$queryNombre = $peticion->query->get('query') ? $peticion->query->get('query') : "";
        $nombre = ($queryNombre != '' ? $queryNombre : $peticion->query->get('nombre'));	
        
        $tipoElemento = $peticion->query->get('tipoElemento');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $empresaCod = $peticion->getSession()->get('prefijoEmpresa');                							     		
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:InfoElemento')
            ->generarJsonElementosPorTipo("Activo", $tipoElemento, $start,$limit,$empresaCod);
        $respuesta->setContent($objJson);
                
        return $respuesta;
    }

   /**
    * enviarMasivosAjaxAction
    *
    * Método encargado de llamar al java que realiza el envio masivo de las notificaciones del módulo de soporte
    *
    * @return json $respuesta
    *
    * @author Desarrollo Inicial
    * @version 1.0
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.1 27-02-2019 - Se agregan filtros adicionales como el: nombre del PE,tipo de servicio,protocolo y la ciudad del punto
    *
    * #@Secure(roles="ROLE_175-524")
    */
   public function enviarMasivosAjaxAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $peticion = $this->get('request');
        
        $em = $this->getDoctrine()->getManager('telconet_soporte');

        $filtros = '';

        $tipoFiltroModulo = $peticion->get('tipoFiltroModulo');

        if($tipoFiltroModulo == 'Tecnico')
        {

            $parametros["oficina"] = $peticion->get('oficina') ? $peticion->get('oficina') : " ";
            $parametros["login2"] = $peticion->get('login2') ? $peticion->get('login2') : " ";
            $parametros["ciudad_punto"] = $peticion->get('ciudad_punto') ? $peticion->get('ciudad_punto') : " ";
            $parametros["direccion_punto"] = $peticion->get('direccion_punto') ? $peticion->get('direccion_punto') : " ";
            $parametros["estado_punto"] = $peticion->get('estado_punto') ? $peticion->get('estado_punto') : " ";
            $parametros["estado_servicio"] = $peticion->get('estado_servicio') ? $peticion->get('estado_servicio') : " ";

            $parametros["tipoElemento"] = $peticion->get('tipoElemento') ? $peticion->get('tipoElemento') : " ";
            $parametros["elemento"] = $peticion->get('elemento') ? $peticion->get('elemento') : " ";

            $parametros["nombre_pe"] = $peticion->get('nombre_pe') ? $peticion->get('nombre_pe') : " ";
            $parametros["protocolo"] = $peticion->get('protocolo') ? $peticion->get('protocolo') : " ";
            $parametros["servicio"] = $peticion->get('servicio') ? $peticion->get('servicio') : " ";
            $parametros["ciudad"] = $peticion->get('ciudad') ? $peticion->get('ciudad') : " ";

            $filtros.= $parametros['oficina'] . '@' . $parametros['login2'] . '@' . $parametros['ciudad_punto'] . '@' . $parametros['direccion_punto']
                . '@' . $parametros['estado_punto'] . '@' . $parametros['estado_servicio'] . '@' . $parametros['tipoElemento']
                . '@' . $parametros['elemento'] . '@' . $parametros['nombre_pe'] . '@' . $parametros['protocolo'] . '@' . $parametros['servicio']
                . '@' . $parametros['ciudad'];
        }
        else if($tipoFiltroModulo == 'Financiero')
        {

            $parametros["oficinas"] = $peticion->get('oficinas') ? $peticion->get('oficinas') : "";
            $parametros["bancos_tarjetas"] = $peticion->get('bancos_tarjetas') ? $peticion->get('bancos_tarjetas') : "";
            $parametros["forma_pago"] = $peticion->get('forma_pago') ? $peticion->get('forma_pago') : "";
            $parametros["tipo_negocio"] = $peticion->get('tipo_negocio') ? $peticion->get('tipo_negocio') : "";
            $parametros["saldo"] = $peticion->get('saldo') ? $peticion->get('saldo') : "";
            $parametros["facturas_abiertas"] = $peticion->get('facturas_abiertas') ? $peticion->get('facturas_abiertas') : "";
            $parametros["estado"] = $peticion->get('estado') ? $peticion->get('estado') : "";
            $parametros["feActivacion"] = $peticion->get('feActivacion') ? $peticion->get('feActivacion') : "";
            $parametros["intCiclo"] = $peticion->get('intCiclo') ? $peticion->get('intCiclo') : "";

            $filtros.= $parametros['oficinas'] . '@' . $parametros['bancos_tarjetas'] . '@' . $parametros['forma_pago'] . '@' . 
                $parametros['tipo_negocio'] . '@' . $parametros['saldo'] . '@' .
                $parametros['facturas_abiertas'] . '@' . $parametros["estado"] . '@' . $parametros["feActivacion"] . '@'. $parametros["intCiclo"];
        }
        else if($tipoFiltroModulo == 'Soporte')
        {
            $idCaso = '';
            if($peticion->get('numeroCaso') && $peticion->get('numeroCaso')!='')
            {
                $objCaso = $em->getRepository("schemaBundle:InfoCaso")->findOneByNumeroCaso($peticion->get('numeroCaso'));  
                if($objCaso)
                {
                    $idCaso = $objCaso->getId();
                }
            }
            $parametros["idCaso"]               = $idCaso;
            $parametros["estadoCaso"]           = $peticion->get('estadoCaso') ? $peticion->get('estadoCaso') : "Asignado";            
            $parametros["ciudadAsignado"]       = $peticion->get('ciudadAsignado') ? $peticion->get('ciudadAsignado') : "";
            $parametros["departamentoAsignado"] = $peticion->get('departamentoAsignado') ? $peticion->get('departamentoAsignado') : "";            

            $filtros.= $parametros['idCaso'] . '@' . $parametros['estadoCaso'] . '@' . 
                       $parametros['ciudadAsignado'] . '@' . $parametros['departamentoAsignado'];
        }
        else
        {

            $parametros["forma_pago"] = $peticion->get('forma_pago') ? $peticion->get('forma_pago') : " ";
            $parametros["servicios_por"] = $peticion->get('servicios_por') ? $peticion->get('servicios_por') : " ";
            $parametros["producto_plan"] = $peticion->get('producto_plan') ? $peticion->get('producto_plan') : " ";
            $parametros["estado_servicio_comercial"] = $peticion->get('estado_servicio_comercial') ? 
                                                       $peticion->get('estado_servicio_comercial') : " ";

            $filtros.= $parametros['forma_pago'] . '@' . $parametros['servicios_por'] . '@' . $parametros['producto_plan'] . '@' . 
                       $parametros['estado_servicio_comercial'];
        }

        $parametros["empresaCod"] = $peticion->getSession()->get('idEmpresa');

        $tipoEnvio = $peticion->get('tipoEnvio'); //Puede ser email, sms o ambos 

        $cmb_plantilla_email = $peticion->get('cmb_plantilla_email');
        $cmb_plantilla_sms = $peticion->get('cmb_plantilla_sms');

        $tiempoEnvio = $peticion->get('tiempoEnvio');
        $fechaComunicacionArray = ($tiempoEnvio == "programado" ? explode('T', $peticion->get('fechaComunicacion')) : false);
        $fechaComunicacion = (($fechaComunicacionArray && count($fechaComunicacionArray) > 0) ? $fechaComunicacionArray[0] : 0);

        $notificaciones = $cmb_plantilla_email . '@' . $cmb_plantilla_sms;

        $formaNotificacion = $peticion->get('tipoNotificacion');
        $puntosSeleccion = '';

        $empresaCod = $peticion->getSession()->get('idEmpresa');
        
        $path = $this->container->getParameter('path_telcos');
        $hostScript = $this->container->getParameter('host_scripts');
        $strPathJava = $this->container->getParameter('path_java_soporte');
        $strScriptPathJava = $this->container->getParameter('path_script_java_soporte');

        $strParametros = $tipoFiltroModulo . "|" . $filtros . "|" . $tipoEnvio . "|" . $fechaComunicacion . "|" . $tiempoEnvio . "|" .
                         $notificaciones . "|" . $peticion->getClientIp() . "|" . $peticion->getSession()->get('user') . "|" .
                         $formaNotificacion . "|" . $puntosSeleccion . "|" . $empresaCod . "|" . $hostScript. "|" .$path;

        $strRutaScript = "/home/scripts-telcos/md/soporte/sources/envio-notificaciones-externas-masivas/dist/EnvioNotificacionesExternasMasivas.jar";
        $strEsperaRespuesta = "NO";

        $strComunicacion = "telcos/app/Resources/scripts/TelcosComunicacionScripts.jar";
        $strLogScript = "/home/telcos/app/Resources/scripts/log/log.txt";

        //Se llama a Script que comunica via SSH
        $strComando = "nohup ".$strPathJava." -jar -Djava.security.egd=file:/dev/./urandom " . $path . $strComunicacion. " '" . $strRutaScript . "' ".
                       " '" . $strParametros . "' '" . $strEsperaRespuesta . "' '" . $hostScript . "' '".
                       $strScriptPathJava."' >> " .$strLogScript. " &";

        shell_exec($strComando);

        if($tiempoEnvio == "programado" && $tiempoEnvio)
        {
            $mensaje = 'La plantilla será enviada el ' . $fechaComunicacion;
        }
        else
        {
            $mensaje = 'La plantilla sera enviada';
        }

        $resultado = json_encode(array('success' => true, 'mensaje' => $mensaje));

        return $respuesta->setContent($resultado);
    }

    //roles="ROLE_175-41"
    /**
    * #@Secure(roles="ROLE_175-524")
    */ 
   public function enviarAjaxAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $peticion = $this->get('request');

        //Envio de Plantillas y SMS a Clientes
        $parametro = $peticion->get('param');   //Contiene los id del Servicio relacionado con el cliente

        $tipoEnvio = $peticion->get('tipoEnvio'); //Puede ser email, sms o ambos

        $cmb_plantilla_email = $peticion->get('cmb_plantilla_email');
        $cmb_plantilla_sms = $peticion->get('cmb_plantilla_sms');

        $tiempoEnvio = $peticion->get('tiempoEnvio');

        $fechaComunicacionArray = ($tiempoEnvio == "programado" ? explode('T', $peticion->get('fechaComunicacion')) : false);
        $fechaComunicacion = (($fechaComunicacionArray && count($fechaComunicacionArray) > 0) ? $fechaComunicacionArray[0] : 0 );

        $em = $this->getDoctrine()->getManager('telconet_soporte');
        $emComunicacion = $this->getDoctrine()->getManager('telconet_comunicacion');
        $emComercial = $this->getDoctrine()->getManager('telconet');

        $em->getConnection()->beginTransaction();
        $emComunicacion->getConnection()->beginTransaction();
        $emComercial->getConnection()->beginTransaction();


        try
        {

            if(($cmb_plantilla_email && $cmb_plantilla_email != "") || ($cmb_plantilla_sms && $cmb_plantilla_sms != ""))
            {


                /*                 ***********************************  Puede venir email, sms o ambos ****************************** */

                if($tipoEnvio == 'sms')
                    $infoDocumento[] = $emComunicacion->getRepository('schemaBundle:InfoDocumento')->find($cmb_plantilla_sms);
                else if($tipoEnvio == 'email')
                    $infoDocumento[] = $emComunicacion->getRepository('schemaBundle:InfoDocumento')->find($cmb_plantilla_email);
                else
                {
                    $infoDocumento[] = $emComunicacion->getRepository('schemaBundle:InfoDocumento')->findOneById($cmb_plantilla_sms);
                    $infoDocumento[] = $emComunicacion->getRepository('schemaBundle:InfoDocumento')->findOneById($cmb_plantilla_email);
                }

                /*                 ************************************************************************************************** */

            }

            $tipoFiltroModulo = ' ';
            $filtros = ' ';
            $notificaciones = '';
            $formaNotificacion = $peticion->get('tipoNotificacion');
            $puntosSeleccion = $parametro;
            $empresaCod = $peticion->getSession()->get('idEmpresa');


            foreach($infoDocumento as $infoD)
            {
                $notificaciones .= $infoD->getId() . '@';
            }
            
            $path = $this->container->getParameter('path_telcos');
            $hostScript = $this->container->getParameter('host_scripts');
            $strPathJava = $this->container->getParameter('path_java_soporte');
            $strScriptPathJava = $this->container->getParameter('path_script_java_soporte');

            $strParametros = $tipoFiltroModulo . "|" . $filtros . "|" . $tipoEnvio . "|" . $fechaComunicacion . "|" . $tiempoEnvio . "|" .
                             $notificaciones . "|" . $peticion->getClientIp() . "|" . $peticion->getSession()->get('user') . "|" .
                             $formaNotificacion . "|" . $puntosSeleccion . "|" . $empresaCod . "|" . $hostScript. "|" .$path;

            $strRutaScript = "/home/scripts-telcos/md/soporte/sources/envio-notificaciones-externas-masivas/dist/EnvioNotificacionesExternasMasivas.jar";
            $strEsperaRespuesta = "NO";

            $strComunicacion = "telcos/app/Resources/scripts/TelcosComunicacionScripts.jar";
            $strLogScript = "/home/telcos/app/Resources/scripts/log/log.txt";
            $strSecurity = "-Djava.security.egd=file:/dev/./urandom";

            $strComando = "nohup ".$strPathJava." -jar " .$strSecurity. " " . $path . $strComunicacion. " '" . $strRutaScript . "' ".
                       " '" . $strParametros . "' '" . $strEsperaRespuesta . "' '" . $hostScript . "' '".
                       $strScriptPathJava."' >> " .$strLogScript. " &";            

            shell_exec($strComando);

            if($tiempoEnvio == "programado" && $tiempoEnvio)
            {
                $mensaje = 'La plantilla será enviada el ' . $fechaComunicacion;
            }
            else
            {
                $mensaje = 'La plantilla sera enviada';
            }

            $resultado = json_encode(array('success' => true, 'mensaje' => $mensaje));
        }
        catch(Exception $e)
        {
            // Rollback the failed transaction attempt				
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
            $emComunicacion->getConnection()->rollback();
            $emComunicacion->getConnection()->close();

            $resultado = json_encode(array('success' => false, 'mensaje' => $e));
        }

        $respuesta->setContent($resultado);
        return $respuesta;
    }

    /**
    * @Secure(roles="ROLE_175-41")
    */ 
    public function getClientesAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $peticion = $this->get('request');
        $nombre = $peticion->get('query');
        
        $start = $peticion->get('start');
        $limit = $peticion->get('limit');
		
        $emGeneral = $this->getDoctrine()->getManager("telconet_general");
        $objJson = $this->getDoctrine()
            ->getManager("telconet")
            ->getRepository('schemaBundle:InfoPersonaEmpresaRol')
            ->findClientesXEmpresa($nombre, "");
                
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
	
	/**
    * @Secure(roles="ROLE_175-165")
    */ 
    public function getOficinasAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $peticion = $this->get('request');
        $nombre = $peticion->get('query');
        
        $empresaCod = $peticion->getSession()->get('idEmpresa');   
        
        $start = $peticion->get('start');
        $limit = $peticion->get('limit');
		
        $emGeneral = $this->getDoctrine()->getManager("telconet_general");
        $objJson = $this->getDoctrine()
            ->getManager("telconet")
            ->getRepository('schemaBundle:InfoOficinaGrupo')
            ->generarJson($emGeneral, $nombre, "Todos", $start,$limit,$empresaCod);
                
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
	
	/**
    * @Secure(roles="ROLE_175-166")
    */ 
    public function getCiclosFacturacionAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $peticion = $this->get('request');
        $nombre = $peticion->get('query');
        
        $start = $peticion->get('start');
        $limit = $peticion->get('limit');
		
        $emGeneral = $this->getDoctrine()->getManager("telconet_general");
        $objJson = $this->getDoctrine()
            ->getManager("telconet_financiero")
            ->getRepository('schemaBundle:AdmiCiclo')
            ->generarJson($nombre, $start,$limit);
                
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
	
    /**
     * @Secure(roles="ROLE_175-167")
     * 
     * Documentacion para el método 'getPlantillasAction'
     *
     * Metodo encargado de obtener el listado de plantillas dependiendo de los parámetros enviados
     *
     * @param array $arrayParametros
     *
     * @return response $respuesta
     *
     * @version 1.0 
     * 
     * @author: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 12-08-2016 Se modifica la consulta para la obtención de las plantillas
     * 
     */
    public function getPlantillasAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        $session = $peticion->getSession();
        
        $empresaCod = $session->get('idEmpresa');
        
        $nombre = $peticion->get('query');
	
        $tipo = $peticion->query->get('tipo');
        
        $parametros = array();
        
        $parametros["tipo"]  =$tipo;
        $parametros["nombre"]=$nombre;
        $parametros["estado"]=$estado;
        $parametros["empresa"]=$empresaCod;
        
        
        $objJson = $this->getDoctrine()
                        ->getManager("telconet_comunicacion")
                        ->getRepository('schemaBundle:InfoDocumento')
                        ->getJSONPlantillas($parametros);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
       /**
      * getPuntosANotificarAction
      *
      * Controlador que obtiene el json de los registros a mostrar en el grid de clientes a notificar                                         
      *
      * @return json con registros 
      *
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.0 11-06-2014
      *
      * @author Jorge Guerrero <jguerrerop@telconet.ec>
      * @version 1.1 25-09-2017 Se agrega el filtro del ciclo de facturación en los parametros
      */
    public function getPuntosANotificarAction() 
    {
	    ini_set('max_execution_time', 7000000);
	    
	    $peticion = $this->get('request');
	    $session = $peticion->getSession();
	    
	    $respuesta = new Response();
	    $respuesta->headers->set('Content-Type', 'text/json');
	    
	    $idEmpresa     = $session->get('idEmpresa');
	    $prefijoEmpresa = $session->get('prefijoEmpresa');
	    
	    $em  = $this->get('doctrine')->getManager('telconet');
	    $emF = $this->get('doctrine')->getManager('telconet_financiero');
		
	    $numFacturasAbiertas = $peticion->query->get('numFacturasAbiertas');
	    $fechaEmisionFactura = $peticion->query->get('fechaEmisionFactura');
	    $valorMontoDeuda     = $peticion->query->get('valorMontoDeuda');
	    $idFormaPago         = $peticion->query->get('idFormaPago');
	    $idsBancosTarjetas   = $peticion->query->get('idsBancosTarjetas');
	    $idsOficinas         = $peticion->query->get('idsOficinas');
	    $estado              = $peticion->query->get('estado');
	    $idTipoNegocio       = $peticion->query->get('idTipoNegocio');
	    $fechaActivacion     = $peticion->query->get('feActivacion') ? explode('T',$peticion->query->get('feActivacion'))[0] : '';
            $intCiclosFacturacion= $peticion->query->get('ciclosFacturacion');
	    
	    $parametros = array();
	
	    $parametros['numFacturasAbiertas'] = $numFacturasAbiertas;
	    $parametros['valorMontoDeuda']     = $valorMontoDeuda;
	    $parametros['idFormaPago']         = $idFormaPago;
	    $parametros['idsBancosTarjetas']   = $idsBancosTarjetas;
	    $parametros['idsOficinas']         = $idsOficinas;
	    $parametros['estado']              = $estado;
	    $parametros['idTipoNegocio']       = $idTipoNegocio;
	    $parametros['fechaActivacion']     = $fechaActivacion;
            $parametros['ciclosFacturacion']   = $intCiclosFacturacion;
	    	    	    
	    $start = $peticion->query->get('start');
	    $limit = $peticion->query->get('limit');	    	   
	    	    
	    $objJson = $em->getRepository('schemaBundle:InfoServicio')
			  ->generarJsonPuntosANotificar($idEmpresa, $parametros , $start , $limit , $idsOficinas , $idsBancosTarjetas);
			  
	    $respuesta->setContent($objJson);
	    
	    return $respuesta;
    }
    
    
      /**
      * getPuntosANotificarPorSoporteAction
      *
      * Controlador que obtiene el json de los registros a mostrar en el grid de clientes a notificar por gestion de Soporte                                        
      *
      * @return json con registros 
      *
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.0 09-010-2014
      */
     public function getPuntosANotificarPorSoporteAction()
    {
        ini_set('max_execution_time', 7000000);

        $peticion = $this->get('request');
        $session = $peticion->getSession();

        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $idEmpresa = $session->get('idEmpresa');

        $em = $this->get('doctrine')->getManager('telconet_soporte');

        $strNumeroCaso      = $peticion->query->get('numeroCaso');
        $strEstadoCaso      = $peticion->query->get('estadoCaso');
        $strEmpresaAsignado = $peticion->query->get('empresaAsignado');
        $intCiudadAsignado  = $peticion->query->get('ciudadAsignado');
        $intDepartAsignado  = $peticion->query->get('departamentoAsignado');

        $arrayParametros = array();
        $caso = '';

        if($strNumeroCaso != "")
        {
            $objInfoCaso = $em->getRepository('schemaBundle:InfoCaso')->findOneByNumeroCaso($strNumeroCaso);
            if($objInfoCaso)
            {
                $caso = $objInfoCaso->getId();
            }
        }

        $arrayParametros['caso']                 = $caso;
        $arrayParametros['estadoCaso']           = $strEstadoCaso;
        $arrayParametros['empresaAsignado']      = $strEmpresaAsignado;
        $arrayParametros['ciudadAsignado']       = $intCiudadAsignado;
        $arrayParametros['departamentoAsignado'] = $intDepartAsignado;
        $arrayParametros['empresaUsuario']       = $idEmpresa;

        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');

        $objJson = $em->getRepository('schemaBundle:InfoCaso')
            ->getJsonClientesNotificarCasos($arrayParametros, $start, $limit);

        $respuesta->setContent($objJson);

        return $respuesta;
    }

    /**
      * exportarConsultaAction
      *
      * Controlador que obtiene se encarga de ejecutar el método de generacion de excel de acuerdo a filtros enviados via POST                                       
      *
      * @return null
      *
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 2.0 17-06-2014
      *
      * @author Jorge Guerrero <jguerrerop@telconet.ec>
      * @version 2.1 29-09-2017 Se agrega los filtros del ciclo de facturacion
      */
     public function exportarConsultaAction()
     {
    
	$respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        $session  = $peticion->getSession();
        
        $em= $this->getDoctrine()->getManager('telconet');                                 		                        
        $emGeneral= $this->getDoctrine()->getManager('telconet_general');
        
        $numFacturasAbiertas = $peticion->get('hid_facturas');	
	$valorMontoDeuda     = $peticion->get('hid_cartera');
	$idFormaPago         = $peticion->get('hid_forma_pago');
	$idsBancosTarjetas   = $peticion->get('hid_bancos');
	$idsOficinas         = $peticion->get('hid_oficinas');
	$estado              = $peticion->get('hid_estado_servicio');
	$idTipoNegocio       = $peticion->get('hid_tipo_negocio');
	$fechaActivacion     = $peticion->get('feActivacion') ? explode('T',$peticion->get('feActivacion'))[0] : '';	
        $intCiclosFacturacion= $peticion->get('hid_ciclos');
	
	$parametros = array();
	
	$parametros['numFacturasAbiertas'] = $numFacturasAbiertas;
	$parametros['valorMontoDeuda']     = $valorMontoDeuda;
	$parametros['idFormaPago']         = $idFormaPago;
	$parametros['idsBancosTarjetas']   = $idsBancosTarjetas;
	$parametros['idsOficinas']         = $idsOficinas;
	$parametros['estado']              = $estado;
	$parametros['idTipoNegocio']       = $idTipoNegocio;
	$parametros['fechaActivacion']     = $fechaActivacion;
        $parametros['ciclosFacturacion']   = $intCiclosFacturacion;
				
	$data = $em->getRepository('schemaBundle:InfoServicio')
		      ->getPuntoANotificar('data',$session->get('idEmpresa'), $parametros);			      		     		     		     
		      
	 $resultado = $data->getArrayResult();		
        
	$this->exportarConsultaTareas($resultado,$session->get('user'),$parametros,$em,$emGeneral);
    
    }
    
      /**
      * exportarConsultaTareas
      *
      * Metodo encargado de realizar la exportación a excel de los clientes a enviar notificaciones                                        
      *
      * @return xls de salida
      *
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.0 13-06-2014
      *
      * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
      * @version 1.1 23-10-2017 
      * Se agrega la columna CICLO_FACTURACION en el excel.
      *
      * @author Luis Cabrera <lcabrera@telconet.ec>
      * @version 1.2 - Se agregan cabeceras a las columnas.
      * @since 22/02/2018
      */
    public function exportarConsultaTareas($data,$user,$parametros,$emComercial,$emGeneral)
    {         	
    
	error_reporting(E_ALL);
                
        $objPHPExcel = new PHPExcel();
       
        $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array( ' memoryCacheSize ' => '1024MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        $objReader = PHPExcel_IOFactory::createReader('Excel5');
                                
        $objPHPExcel = $objReader->load(__DIR__."/../Resources/templatesExcel/templateConsultaNotificaciones.xls");               
                
        $objPHPExcel->getProperties()->setCreator("TELCOS++");
        $objPHPExcel->getProperties()->setLastModifiedBy($user);
        $objPHPExcel->getProperties()->setTitle("Consulta de Clientes a Notificar");
        $objPHPExcel->getProperties()->setSubject("Consulta de Clientes");
        $objPHPExcel->getProperties()->setDescription("Resultado de busqueda de Clientes.");
        $objPHPExcel->getProperties()->setKeywords("Clientes a Notificar");
        $objPHPExcel->getProperties()->setCategory("Reporte");

        $objPHPExcel->getActiveSheet()->setCellValue('B3',$user);

        $objPHPExcel->getActiveSheet()->setCellValue('B4', PHPExcel_Shared_Date::PHPToExcel( gmmktime(0,0,0,date('m'),date('d'),date('Y')) ));
        $objPHPExcel->getActiveSheet()->getStyle('B4')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);
        
        $tipoNegocio = $emComercial->getRepository('schemaBundle:AdmiTipoNegocio')->find($parametros['idTipoNegocio']);
        if($tipoNegocio)$tipoNegocio = $tipoNegocio->getNombreTipoNegocio();else $tipoNegocio = '';
        
        $formaPago   = $emComercial->getRepository('schemaBundle:AdmiFormaPago')->find($parametros['idFormaPago']);
        if($formaPago)$formaPago = $formaPago->getDescripcionFormaPago();else $formaPago = '';
        
        $oficinas = explode(",",$parametros['idsOficinas']);
        $bancos   = explode(",",$parametros['idsBancosTarjetas']);
        
        $nombreOficinas = ''; $nombreBancos = '';
        
        foreach($oficinas as $oficina):	  
	      $oficina = $emComercial->getRepository('schemaBundle:InfoOficinaGrupo')->find($oficina);
	      $nombreOficinas .= $oficina->getNombreOficina().'-';        
        endforeach;                
        
        foreach($bancos as $banco):	
	      if($parametros['idFormaPago'] == 3)
	      {
		    $bco = $emGeneral->getRepository('schemaBundle:AdmiBanco')->find($banco);
		    $nombreBancos .= strtoupper($bco->getDescripcionBanco()).'-';
	      }
	      if($parametros['idFormaPago'] == 10)
	      {
		    $bco = $emGeneral->getRepository('schemaBundle:AdmiTipoCuenta')->find($banco);
		    $nombreBancos .= strtoupper($bco->getDescripcionCuenta()).'-';
	      }
	      
        endforeach; 
        
        $strNombreCiclo = '';
        $emFinanciero   = $this->get('doctrine')->getManager('telconet_financiero');
        if(isset($parametros['ciclosFacturacion']) && !empty($parametros['ciclosFacturacion']))
	    {
		    $objAdmiCiclo = $emFinanciero->getRepository('schemaBundle:AdmiCiclo')->find($parametros['ciclosFacturacion']);
            if(is_object($objAdmiCiclo))
            {
                $strNombreCiclo .= strtoupper($objAdmiCiclo->getNombreCiclo());  
            }		    
	    }
        
        $objPHPExcel->getActiveSheet()->setCellValue('B8' ,''.$parametros['numFacturasAbiertas']?$parametros['numFacturasAbiertas']:'');                 
        $objPHPExcel->getActiveSheet()->setCellValue('B9' ,''.$parametros['valorMontoDeuda']?'> $'.$parametros['valorMontoDeuda']:'> $0');
        $objPHPExcel->getActiveSheet()->setCellValue('B10',''.$tipoNegocio?$tipoNegocio: 'Todos');        		
        $objPHPExcel->getActiveSheet()->setCellValue('B11',''.$parametros['estado']!=''?$parametros['estado']: 'Activo');       
        $objPHPExcel->getActiveSheet()->setCellValue('B12',''.$parametros['fechaActivacion']?$parametros['fechaActivacion']: '-');  
        $objPHPExcel->getActiveSheet()->setCellValue('B13',''.$nombreOficinas?$nombreOficinas: '-');  
        $objPHPExcel->getActiveSheet()->setCellValue('B14',''.$formaPago?$formaPago:'-');  
        $objPHPExcel->getActiveSheet()->setCellValue('B15',''.$nombreBancos?$nombreBancos:'-');  
        $objPHPExcel->getActiveSheet()->setCellValue('B16',''.$strNombreCiclo?$strNombreCiclo:'-');  
                
        $i=19;                    
        
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, "Login");
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, "Nombre Cliente");
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, "Oficina");
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, "Saldo");
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, "Forma Pago");
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, "Tipo Negocio");
        $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, "Fecha Activación");
        $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, "Estado Servicio");
        $i++;

        foreach($data as $cliente):
        						
		
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$cliente['login']);                 
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,$cliente['clienteNombre']);
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$cliente['nombreOficina']);        		
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,'$ '.$cliente['saldo']);       
 		$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$cliente['descripcionFormaPago']);       
 		$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$cliente['nombreTipoNegocio']);  
 		$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,$cliente['feActivacion']?$cliente['feActivacion']:'');  
 		$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,$cliente['estado']);   	
 		
 		$i = $i +1; 		 		
        
        endforeach;

        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('Reporte');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        //Redirect output to a clients web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Consulta_de_Casos_'.date('d_M_Y').'.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    
    
    }

/*
 * getCiclosAction
 *
 * Funcion que retorna los ciclos de facturacion Activos e Inactivos.
 *
 * @author Jorge Guerrero <jguerrerop@telconet.ec>
 * @since 1.0 25-09-2017 Versión Inicial
 *
 * @return array con los ciclos Activos e Inactivos
 */
    //funcion que retorna los tipo de medio
    public function getCiclosAction() 
    {
        $arrayCiclos  = '';
        $objRespuesta = new JsonResponse();
        $emFinanciero = $this->get('doctrine')->getManager('telconet_financiero');
        $objCiclos    = $emFinanciero->getRepository('schemaBundle:AdmiCiclo')->findBy(array(
                        'estado' => array('Activo','Inactivo')));

        foreach ($objCiclos as $objCiclo)
        {
            $arrayCiclos[] = array(
                            'intIdCiclo' => $objCiclo->getId(),
                            'strNombreCiclo' => $objCiclo->getNombreCiclo()
            );
        }

        if (!empty($arrayCiclos))
        {
            $intTotal = count($arrayCiclos);
            $objRespuesta ->setData(array(
                            'intTotal' => $intTotal,
                            'arrayRegistros' => $arrayCiclos
            ));
        }
        else
        {
            $arrayCiclos[] = array();
            $intTotal = 0;
            $objRespuesta ->setData(array(
                            'intTotal' => 0,
                            'arrayRegistros' => $arrayCiclos
            ));
        }

        return $objRespuesta;
    }
}