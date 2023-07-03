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
use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolHisto;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
class PortabilidadController extends Controller implements TokenAuthenticatedController
{
    /**
     * 
     *
     * Documentación para el método 'indexAction'.
     * Muestra la pagina principal del modulo de Derechos del Titular en Administracion
     *
     * @return Response.
     *
     * @version 1.0 Version Inicial
     *
     * @author Jessenia Piloso <jpiloso@telconet.ec>
     * @version 1.1 19-02-2021 Muestra la pagina principal del modulo de Derechos del Titular en Administracion
     */
    public function indexAction()
    {
        $objSession = $this->get('request')->getSession();
		
        $objEmSeguridad = $this->getDoctrine()->getManager("telconet_seguridad");       
        $entityItemMenu = $objEmSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("496", "1");    	
		$objSession->set('menu_modulo_activo', $entityItemMenu->getNombreItemMenu());
		$objSession->set('nombre_menu_modulo_activo', $entityItemMenu->getTitleHtml());
		$objSession->set('id_menu_modulo_activo', $entityItemMenu->getId());
		$objSession->set('imagen_menu_modulo_activo', $entityItemMenu->getUrlImagen());
		
        $objEntity = new InfoPersona();
        
        $objForm = $this->createForm(new ClienteType(), $objEntity);
        return $this->render('administracionBundle:Portabilidad:index.html.twig', array(
                    'entity' => $objEntity,
                    'form' => $objForm->createView(),
                    'opcion' => $entityItemMenu->getNombreItemMenu()
                ));
    }

    /**
     * 
     * Documentación para el método 'ajaxValidaCorreoAction'.
     * 
     * Valida el correo ingresado en las pantallas de Derechos del Titular.
     * 
     * @return Response objeto JSON.
     * 
     * @version 1.0 Version Inicial
     *
     * @author Jessenia Piloso <jpiloso@telconet.ec>
     * @version 1.1 19-02-2021 
     */
    public function ajaxValidaCorreoAction()
    {
        $objRequest            = $this->getRequest();
        $strCorreo          = trim($objRequest->request->get("correo"));
        
         $serviceInfoPersonaFormaContacto = $this->get('comercial.InfoPersonaFormaContacto');
        $boolEsEmailValido = $serviceInfoPersonaFormaContacto->esEmailValido($strCorreo);
        if($boolEsEmailValido)
        {
            //Verificando el dominio de mail				
            $arrayMailTemp = explode("@", $strCorreo);
            $strDominioTemp = $arrayMailTemp[1];

            $boolVerificarMailDNS = $serviceInfoPersonaFormaContacto->verificarMailDNS($strDominioTemp);
            if(!$boolVerificarMailDNS)
            {   
                $objResponse = new Response(json_encode(array('strMsjObservacion' => 'Dominio del Correo Electronico Incorrecto,'
                                                            . ' No cumple el formato permitido : ' . $strCorreo, 
                                                              'strMsjValidacion' => 'ERROR')));
               
            }
            else
            {   
                $objResponse = new Response(json_encode(array('strMsjObservacion' => 'Correo Electronico Correcto',
                                                              'strMsjValidacion' => 'OK')));  
                           
            }
        }
        else
        {   
            $objResponse = new Response(json_encode(array('strMsjObservacion' => 'Correo Electronico Incorrecto,'
                                                         . ' No cumple el formato permitido : ' . $strCorreo,
                                                        'strMsjValidacion' => 'ERROR')));
        }

        
        return $objResponse;
        
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
    public function ajaxEjecutaPortabilidadAction()
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
        $serviceUtil           = $this->get('schema.Util');
        $objSoporteService     = $this->get('soporte.SoporteService');
        $objConexion           = $this->get('doctrine')->getManager('telconet');
        $emConexion            = $this->getDoctrine()->getManager('telconet');
  
        $objResponse           = new Response();
        $strMensaje            = 'OK';
        $intIdPunto            = null;
        $objConexion->getConnection()->beginTransaction();
        $emConexion->getConnection()->beginTransaction();
        try 
        {
                
            //Tarea automatica
            //Obtiene Parametros de la tarea y el proceso de la tarea Automatica
            $strParamCabTareaAut = 'PROCESOS_DERECHOS_DEL_TITULAR';
            $arrayParamTareaAut  = $objConexion->getRepository('schemaBundle:AdmiParametroDet')
                                ->get($strParamCabTareaAut, 'COMERCIAL', '', 'TAREA_AUTOMATICA_PORTABILIDAD',
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

            $strObservacioTarea = "Se realizó el proceso de Portabilidad del Titular: "
                                .$strIdentificacion." enviado al correo : ".$strCorreo;
            //Se crea la tarea Automatica
            $arrayTarea = $objSoporteService
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
                throw new \Exception('Ocurrió un error al generar la tarea en la solicitud de portabilidad');
            } 

            //Enviar el correo 
            $strMsjEnvioCorreo = $this->getDoctrine()->getManager("telconet")->getRepository('schemaBundle:InfoPersona')
                                    ->enviarCorreoServiciosPersona($strCorreo,$strIdentificacion);
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
                throw new \Exception('Error al enviar el correo de Solicitud de Portabilidad');
            }

            //Guardar la bitacora
            $arrayRespuesta =  $serviceUtil->guardarBitacora(array (
                "strIP"                 => $strIpCreacion,
                "strUsuario"            => $strUsuario,
                "strTipoIdentificacion" => $strTipoIdentificacion,
                "strIdentificacion"     => $strIdentificacion,
                "strCorreo"             => $strCorreo,
                "strfechaHoraActualizacion" => date("Y-m-d").'T'.date("H:i:s"),
                "strMetodo"             => "PORTABILIDAD"
                ));
            if ($arrayRespuesta['intStatus'] !== 0)
            {
                $strObservacion = 'Error al crear el log de trazabilidad: PORTABILIDAD '
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
                    if (empty($arrayInfoPersonaEmpresaRol))
                    {
                        $arrayInfoPersonaEmpresaRol=$emConexion->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                       ->getPersonaEmpresaRolPorPersonaPorTipoRolTodos($strIdCliente,'Pre-cliente',$strIdEmpresa);
                    }

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
                $objSoporteService->crearInfoTarea($arrayParametrosInfoTarea);
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

            $strMensaje = $e->getMessage();
            $objResponse = new Response(json_encode(array('strMsjObservacion' => $strMensaje,
                                                        'strMsjValidacion' => 'ERROR')));
            $serviceUtil->insertError(
                                    'Telcos+',
                                    'EJECUTA PORTABILIDAD ACTION',
                                    'PORTABILIDAD'. $strMensaje,
                                    $strUsuario,
                                    '127.0.0.1'
            );
        }
        
        return $objResponse;
        
    }
}