<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;
use telconet\schemaBundle\Entity\InfoPersona;
use telconet\schemaBundle\Form\ClienteType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use telconet\comercialBundle\Service\InfoPersonaFormaContactoService;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolHisto;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
class OposicionController extends Controller implements TokenAuthenticatedController
{
    /**
     * 
     *
     * Documentación para el método 'indexAction'.
     * Muestra la pagina principal del modulo de Oposicion en Derechos del Titular
     *
     * @return Response.
     *
     * @version 1.0 Version Inicial
     *
     * @author Jessenia Piloso <jpiloso@telconet.ec>
     * @version 1.1 19-02-2021
     */
    public function indexAction()
    {
        $objSession = $this->get('request')->getSession();
		
        $objEmSeguridad = $this->getDoctrine()->getManager("telconet_seguridad");       
        $entityItemMenu = $objEmSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("493", "1");    	
		$objSession->set('menu_modulo_activo', $entityItemMenu->getNombreItemMenu());
		$objSession->set('nombre_menu_modulo_activo', $entityItemMenu->getTitleHtml());
		$objSession->set('id_menu_modulo_activo', $entityItemMenu->getId());
		$objSession->set('imagen_menu_modulo_activo', $entityItemMenu->getUrlImagen());
		
        $objEntity = new InfoPersona();

        $objForm = $this->createForm(new ClienteType(), $objEntity);
        return $this->render('administracionBundle:Oposicion:index.html.twig', array(
                    'entity' => $objEntity,
                    'form' => $objForm->createView(),
                    'opcion' => $entityItemMenu->getNombreItemMenu()
                ));
    }


     /**
     * 
     * Documentación para el método 'ajaxEjecutaPortabilidadAction'.
     * 
     * Metodo encargado de enviar correo, generar tarea automatica y guardar bitacora de las 
     * solicitudes de portabilidad del cliente.
     * 
     * @return Response objeto JSON con exito o error de los procesos.
     * 
     * @version 1.0 Version Inicial
     *
     * @author Jessenia Piloso <jpiloso@telconet.ec>
     * @version 1.1 19-02-2021 
     */
    public function ajaxEjecutaOposicionAction()
    {
        
        $objRequest            = $this->getRequest();
        $objSession            = $objRequest->getSession();
        $strUsuario            = $objSession->get('user');
        $strIpCreacion         = $objRequest->getClientIp();
        $strIdEmpresa          = $objSession->get('idEmpresa');
        $strPrefijoEmpresa     = $objSession->get('prefijoEmpresa');
        $strNombresCompletos   = $objSession->get('empleado');
        $strIdPersonaEmpresaRol= $objSession->get('idPersonaEmpresaRol');
        $strCorreo             = trim($objRequest->request->get("correo"));
        $strIdentificacion     = trim($objRequest->request->get("identificacion"));
        $strTipoIdentificacion = trim($objRequest->request->get("tipoidentificacion"));
        $strMetodo             = $objRequest->get('strMetodo'); 
        $strOpcion             = $objRequest->get('strOpcion'); 
        $strParametros         = $objRequest->get('strParametros');
        $serviceUtil           = $this->get('schema.Util');
        $serviceSoporte        = $this->get('soporte.SoporteService');
        $emConexion            = $this->getDoctrine()->getManager('telconet');
        $objConexion           = $this->get('doctrine')->getManager('telconet');
        $serviceUtil           = $this->get('schema.Util');
        $strMensaje            = 'OK';
        $intIdPunto            = null;
        $objConexion->getConnection()->beginTransaction();
        $emConexion->getConnection()->beginTransaction();
        try 
        {   
                 
            if ($strOpcion == 'Oposición')
            {
                $strTareaAutomatica = 'TAREA_AUTOMATICA_OPOSICION';
            }elseif($strOpcion == 'Suspensión de Tratamiento')
            {
                $strTareaAutomatica = 'TAREA_AUTOMATICA_SUSPENSION_TRATAMIENTO';
            }elseif($strOpcion == 'Detención de Suspensión de Tratamiento')
            {
                $strTareaAutomatica = 'TAREA_AUTOMATICA_DETENCION_SUSPENSION_TRATAMIENTO';
            }
            //Tarea automatica
            //Obtiene Parametros de la tarea y el proceso de la tarea Automatica
            $strParamCabTareaAut = 'PROCESOS_DERECHOS_DEL_TITULAR';
            $arrayParamTareaAut  = $objConexion->getRepository('schemaBundle:AdmiParametroDet') 
                                ->get($strParamCabTareaAut, 'COMERCIAL', '',$strTareaAutomatica ,
                                        '', '', '', '', '', $strIdEmpresa, '');

            if( empty($arrayParamTareaAut) )
            {
                throw new \Exception('No existen datos para generar la tarea automatica');
            }

            $arrayInfoPunto = $emConexion->getRepository('schemaBundle:InfoServicio')
                                  ->getInfoPuntoByIdentificacion(array('strDescripcionRol' => 'Cliente',
                                                                       'strDescripcionTipoRol'  => 'Cliente',
                                                                       'strPrefijoEmpresa'  => 'MD',
                                                                       'strIdentificacion'  => $strIdentificacion,
                                                                       'intRowNum'          => 1));

            if(!empty($arrayInfoPunto))
            {
                $intIdPunto = $arrayInfoPunto[0]['idPunto'];
            }
            
            $strObservacioTarea = "Se realizó el proceso de ".$strOpcion." del Titular: "
                                .$strIdentificacion." enviado al correo: ".$strCorreo;
            
            //Se crea la tarea Automatica
            $arrayTarea = $serviceSoporte
                        ->crearTareaCasoSoporte(array (
                        "intIdPersonaEmpresaRol" => $strIdPersonaEmpresaRol,
                        "intIdEmpresa"           => $strIdEmpresa,
                        "strPrefijoEmpresa"      => $strPrefijoEmpresa,
                        "strNombreTarea"         => $arrayParamTareaAut[0]['valor2'],
                        "strNombreProceso"       => $arrayParamTareaAut[0]['valor3'],
                        "strUserCreacion"        => $strUsuario,
                        "strIpCreacion"          => $strIpCreacion,
                        "strObservacionTarea"    => $strObservacioTarea,
                        "strUsuarioAsigna"       => $strNombresCompletos,
                        "strTipoAsignacion"      => $arrayParamTareaAut[0]['valor6'],
                        "strTipoTarea"           => "T",
                        "strTareaRapida"         => "S",
                        "boolAsignarTarea"       => true,
                        "intPuntoId"             => $intIdPunto,
                        "strFechaHoraSolicitada" => null,
                        "strObsHistorial"        => "Tarea fue Finalizada Obs: Tarea Rapida",
                        "strObsSeguimiento"      => "Tarea fue Finalizada Obs: Tarea Rapida",
                        "intFormaContacto"       => 5,
                        "strNombreClaseDocParam" => $arrayParamTareaAut[0]['valor4'],
                        "strConfirmarCommit"     => 'N'));
            
            $objConSoporte = $arrayTarea["objConexionSoporte"]; 
            $objConComunicacion = $arrayTarea["objConexionComuni"];    
            $objConSoporteAsigna = $arrayTarea["objConSoporteAsigna"];     
            $objConComunicaAsigna = $arrayTarea["objConComunicaAsigna"]; 

            if ($arrayTarea['mensaje'] !== 'ok' )
            {
                $objConComunicacion->rollback();
                $objConComunicacion->close();

                $objConSoporte->rollback();
                $objConSoporte->close();
            
                $objConSoporteAsigna->rollback();
                $objConSoporteAsigna->close();

                $objConComunicaAsigna->rollback();
                $objConComunicaAsigna->close();
                throw new \Exception('Ocurrió un error al generar la tarea en la solicitud de '.$strOpcion);
            } 

            //Guardar Respuestas de Politicas y Clausulas
             $arrayRespGuardarPoliticasClausulas = $this->ajaxGuardarRespPoliticaClausulaAction(
                $strParametros,$strMetodo,$strCorreo,$strIdentificacion,$strTipoIdentificacion);
                
                if ($arrayRespGuardarPoliticasClausulas['status']  != 'OK' )
                {
                    $objConComunicacion->rollback();
                    $objConComunicacion->close();
    
                    $objConSoporte->rollback();
                    $objConSoporte->close();
                
                    $objConSoporteAsigna->rollback();
                    $objConSoporteAsigna->close();
    
                    $objConComunicaAsigna->rollback();
                    $objConComunicaAsigna->close();
                    throw new \Exception('Ocurrió un error al Guardar Respuestas de las Politica/Clausula');
                } 

             //Enviar el correo
            $objPersona  = $emConexion->getRepository('schemaBundle:InfoPersona')
            ->findOneBy(array("identificacionCliente" => $strIdentificacion));

            if (!is_object($objPersona))
            {
                $objConComunicacion->rollback();
                $objConComunicacion->close();

                $objConSoporte->rollback();
                $objConSoporte->close();
            
                $objConSoporteAsigna->rollback();
                $objConSoporteAsigna->close();

                $objConComunicaAsigna->rollback();
                $objConComunicaAsigna->close();
                throw new \Exception('Ocurrio un error al Consultar Datos del Cliente');
            } 

            $strRazonSocial = $objPersona->getRazonSocial();
            if (!empty($strRazonSocial))
            {
                $strCliente = $objPersona->getRazonSocial();
            }
            else
            {     
                $strCliente = $objPersona->getNombres().' '.$objPersona->getApellidos();
            }
            error_log($strCliente);
            
            $strMsjEnvioCorreo = $this->getDoctrine()->getManager("telconet")->getRepository('schemaBundle:InfoPersona')
                                    ->enviarCorreoSolicitudLODPD($strCorreo,$strCliente);
            if($strMsjEnvioCorreo != 'OK')
            {   
                $objConComunicacion->rollback();
                $objConComunicacion->close();

                $objConSoporte->rollback();
                $objConSoporte->close();
            
                $objConSoporteAsigna->rollback();
                $objConSoporteAsigna->close();

                $objConComunicaAsigna->rollback();
                $objConComunicaAsigna->close();
                throw new \Exception('Error al enviar el correo de Solicitud de '.$strOpcion);
            }


            //Guardar la bitacora
             $arrayRespuesta =  $serviceUtil->guardarBitacora(array (
                "strIP"                 => $strIpCreacion,
                "strUsuario"            => $strUsuario,
                "strTipoIdentificacion" => $strTipoIdentificacion,
                "strIdentificacion"     => $strIdentificacion,
                "strCorreo"             => $strCorreo,
                "strfechaHoraActualizacion" => date("Y-m-d").'T'.date("H:i:s"),
                "strMetodo"             => $strOpcion
                ));
                
                if ($arrayRespuesta['intStatus'] !== 0)
                {
                    $strObservacion = 'Error al crear el log de trazabilidad: '.$strOpcion.' '
                    .$arrayRespuesta['strMensaje'];
                    
                    //REGISTRA EN LA TABLA DE PERSONA HISTORIAL EL ERROR DE LA BITACORA
                    //OBTIENE CLIENTE   
                    $objPersona = $objConexion->getRepository('schemaBundle:InfoPersona')
                    ->findOneByIdentificacionCliente($strIdentificacion);
    
                    if (is_object($objPersona))
                    {
                        $strIdCliente = $objPersona->getId();
    
                        //OBTIENE InfoPersonaEmpresaRol
                        $arrayInfoPersonaEmpresaRol=$emConexion->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                        ->getPersonaEmpresaRolPorPersonaPorTipoRolTodos($strIdCliente,'Cliente',$strIdEmpresa);
                        foreach ($arrayInfoPersonaEmpresaRol as $objInfoPersonaEmpresaRol):
                                            
                        $entityPersonaHistorial = new InfoPersonaEmpresaRolHisto();
                        $entityPersonaHistorial->setEstado($objInfoPersonaEmpresaRol->getEstado());
                        $entityPersonaHistorial->setFeCreacion(new \DateTime('now'));
                        $entityPersonaHistorial->setIpCreacion($strIpCreacion);
                        $entityPersonaHistorial->setPersonaEmpresaRolId($objInfoPersonaEmpresaRol);
                        $entityPersonaHistorial->setUsrCreacion($strUsuario);
                        $entityPersonaHistorial->setObservacion($strObservacion);
                        endforeach;
                        $emConexion->persist($entityPersonaHistorial);  
                        $emConexion->flush(); 
                    }
                }  
    
                $objConComunicacion->commit();
                $objConSoporte->commit();
         
                $objConSoporteAsigna->commit();
                $objConComunicaAsigna->commit();
    
                //Proceso que graba tarea en INFO_TAREA
                if(!empty($arrayTarea['numeroDetalle']))
                {
                    $arrayParametrosInfoTarea['intDetalleId']   = $arrayTarea['numeroDetalle'];
                    $arrayParametrosInfoTarea['strUsrCreacion'] = $strUsuario;
                    $serviceSoporte->crearInfoTarea($arrayParametrosInfoTarea);
                }
                
            $emConexion->getConnection()->commit(); 
            $objConexion->getConnection()->commit();
            
            $objResponse = new Response(json_encode(array('strMsjObservacion' => $strMensaje,
                                                        'strMsjValidacion' => 'OK')));
           

        } catch (\Exception $e) 
        {
            $objConexion->getConnection()->rollback();
            $objConexion->getConnection()->close();

            $emConexion->getConnection()->rollback();
            $emConexion->getConnection()->close();

            $strMensaje = 'Error General '.$e->getMessage();
            $objResponse = new Response(json_encode(array('strMsjObservacion' => $strMensaje,
                                                        'strMsjValidacion' => 'ERROR')));
            $serviceUtil->insertError(
                                        'Telcos+',
                                        $strOpcion.' '. $strMetodo,
                                        $strOpcion.' '. $strMetodo.' '. $strMensaje,
                                        $strUsuario,
                                        '127.0.0.1'
                                    );
        }
        
        return $objResponse;
        
    }
 /**
     * ajaxGetPoliticasAction, método que realiza la consulta de las politicas y clausulas 
     * del documento Derechos de titular 
     *
     * @author Jessenia Piloso Baque <jpiloso@telconet.ec>
     * @version 1.0 23-12-2022   
     *                    
     * @return Response lista de Politicas y Clausulas
     */ 
    public function ajaxGetPoliticasAction()
    {
        $serviceRestClient   = $this->get('schema.RestClient');
        $serviceUtil         = $this->get('schema.Util');
        try
        {
        $serviceTokenCas     = $this->get('seguridad.TokenCas');
        $arrayTokenCas       = $serviceTokenCas->generarTokenCas(); 

        $objRequest          = $this->get('request');
        $objSession          = $objRequest->getSession();

        $strUrlBase              = $this->container->getParameter('ws_ms_politicaencuesta_url'); 

        $objParameter        = array();
        $arrayDataPoliticas  = array();
        $arrayNewResponse    = array();
        $strUsrCreacion      = $objSession->get('user');
        $strMetodo           = $objRequest->get('strMetodo'); 
       
        $arrayOptions       = array(
                                    CURLOPT_SSL_VERIFYPEER => false,
                                    CURLOPT_HTTPHEADER     => array(
                                        'Content-Type: application/json',
                                        'tokencas: ' . $arrayTokenCas['strToken']
                                    )
                                );

        $arrayAplicaA       = array('Derechos del titular');

        if ($strMetodo=='listPoliticaClausula') 
        {
            $strUrl =  $strUrlBase . 'busquedaPoliticaClausula'; 
            $objParameter['usrCreacion'] = $strUsrCreacion; 
            $objParameter['ipCreacion']  = '127.0.0.1'; 
            $objParameter['arrayAplicaA'] = $arrayAplicaA;
        }
        else 
        {
            throw new \Exception('Metodo '. $strMetodo.' no implementado');
        }                       
        $arrayResponse      = $serviceRestClient->postJSON( $strUrl, json_encode( $objParameter), $arrayOptions);
      
         $arrayResultado     = json_decode($arrayResponse['result'], true);

         if ($arrayResultado['status'] == 'OK')
         {
            foreach($arrayResultado['data'] as $arrayData)
            { 
                if (!empty($arrayData['clausulas']))
                {
                    foreach($arrayData['clausulas'] as $arrayDataClausula)
                    {
                        if (!empty($arrayDataClausula))
                        {
                            $arrayDataPoliticas[] = $arrayDataClausula;
                        }
                    }
                } 
                else
                {
                    $arrayDataPoliticas[] = $arrayData;
                } 
            } 
            $arrayNewResponse['code'] = 0;
            $arrayNewResponse['status'] = 'OK';
            $arrayNewResponse['message'] = 'Transacción realizada correctamente';
            $arrayNewResponse['data'] = $arrayDataPoliticas;
         }
         
  
        if($arrayResultado['status'] == 'ERROR')
        {
            throw new \Exception($arrayResultado['message']);
        }

        }
        catch (\Exception $e)
        {
            $arrayResultado['status']  = 'ERROR';
            $arrayResultado['message'] = "Error al ejecutar el proceso ". $strMetodo
            ." de  ws_ms_politicaencuesta_url. Favor Notificar a Sistemas" . $e->getMessage();

            $serviceUtil->insertError(
                                        'Telcos+',
                                        $strMetodo,
                                        $arrayResultado['message'],
                                        $strUsrCreacion,
                                        '127.0.0.1'
                                    );
        }
    
        $objResponse        = new Response(json_encode($arrayNewResponse));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;

        
    }

    /**
     * ajaxGetPoliticasAction, método que realiza la consulta de las politicas y clausulas 
     * del documento Derechos de titular 
     *
     * @author Jessenia Piloso Baque <jpiloso@telconet.ec>
     * @version 1.0 23-12-2022   
     *                    
     * @return array  Guardar Respuesta de  de Politicas y Clausulas
     */ 
    public function ajaxGuardarRespPoliticaClausulaAction(
        $strParametros,$strMetodo,$strCorreo,$strIdentificacion,$strTipoIdentificacion)
    {
        $serviceRestClient   = $this->get('schema.RestClient');
        $serviceUtil         = $this->get('schema.Util');
 

        try
        {
        $serviceTokenCas     = $this->get('seguridad.TokenCas');
        $arrayTokenCas       = $serviceTokenCas->generarTokenCas(); 
        $objRequest          = $this->get('request');
        $objSession          = $objRequest->getSession();
        $strUrlBase          = $this->container->getParameter('ws_ms_politicaencuesta_url'); 
        $objParameter        = array();
        $objParamGuardarResp = array();
        $strUsrCreacion      = $objSession->get('user');
        $objParameter        = json_decode($strParametros, true);
        $emComercial           = $this->getDoctrine()->getManager('telconet');
        $objPersona            = $emComercial->getRepository('schemaBundle:InfoPersona')
                                 ->findOneBy(array("identificacionCliente" => $strIdentificacion));

        $arrayOptions       = array(
                                    CURLOPT_SSL_VERIFYPEER => false,
                                    CURLOPT_HTTPHEADER     => array(
                                        'Content-Type: application/json',
                                        'tokencas: ' . $arrayTokenCas['strToken']
                                    )
                                    );

        $arrayCorreo = explode(",",$strCorreo);
        for ($intIndex =0; $intIndex < count($arrayCorreo ); $intIndex ++)
            { 
            $arrayContacto[] = array (
                
                    "idFormaContacto" => 5,
                    "valor"     => $arrayCorreo[$intIndex]
                
            );
        }   

        if(is_object($objPersona))
        {                
            $arrayPersona       = array(
                "identificacion"     => $strIdentificacion,
                "tipoIdentificacion" => $strTipoIdentificacion,
                "tipoPersona"        => $objPersona->getTipoTributario(),
                "nombres"            => $objPersona->getNombres(),
                "apellidos"          => $objPersona->getApellidos(),
                "contactos"          => $arrayContacto
            );
        }
            

        if ($strMetodo=='GuardarRespPoliticaClausula') 
        {
            $strUrl =  $strUrlBase . 'guardarEncuesta'; 
            $objParamGuardarResp['usrCreacion'] = $strUsrCreacion; 
            $objParamGuardarResp['documentoEncuesta']  = $objParameter; 
            $objParamGuardarResp['persona'] = $arrayPersona;
        }
        else 
        {
            throw new \Exception('Metodo '. $strMetodo.' no implementado');
        } 
                 
        $arrayResponse      = $serviceRestClient->postJSON( $strUrl, json_encode( $objParamGuardarResp), $arrayOptions);
 
        $arrayResultado     = json_decode($arrayResponse['result'], true);
        if($arrayResultado['status'] == 'ERROR')
        {

            throw new \Exception($arrayResultado['message']);
        }
        }
        catch (\Exception $e)
        {
            $arrayResultado['status']  = 'ERROR';
            $arrayResultado['message'] = "Error al ejecutar el proceso ". $strMetodo
            ." de  ws_ms_politicaencuesta_url. Favor Notificar a Sistemas" . $e->getMessage();

            $serviceUtil->insertError(
                                        'Telcos+',
                                        $strMetodo,
                                        $arrayResultado['message'],
                                        $strUsrCreacion,
                                        '127.0.0.1'
                                    );
        }
      
        
      return $arrayResultado;

    }


}