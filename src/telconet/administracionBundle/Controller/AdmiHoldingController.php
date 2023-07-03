<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiParametroCab;
use telconet\schemaBundle\Entity\AdmiParametroDet;
use telconet\schemaBundle\Entity\InfoPersona;

use telconet\schemaBundle\Form\AdmiHoldingType;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response; 
use Symfony\Component\HttpFoundation\JsonResponse; 
use JMS\SecurityExtraBundle\Annotation\Secure;

class AdmiHoldingController extends Controller implements TokenAuthenticatedController
{
    /**
    * @Secure(roles="ROLE_452-1")
    */
    public function indexAction()
    {
    
        $arrayRolesPermitidos = array();

        if (true === $this->get('security.context')->isGranted('ROLE_452-1'))
        {
            $arrayRolesPermitidos[] = 'ROLE_452-1';
        }
        
        $emComercial         = $this->getDoctrine()->getManager('telconet');
        $emSeguridad         = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu      = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("452", "1");

        return $this->render('administracionBundle:AdmiHolding:index.html.twig', array(
            'item' => $entityItemMenu,
            'rolesPermitidos' => $arrayRolesPermitidos
        ));
    }
    
    
    
    /**
    * @Secure(roles="ROLE_452-1")
     * 
     * Documentación para el método 'gridAction'.
     *
     * Método utilizado para llenar el grid de Holding.
     *
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.0 23-10-2020
    */
    public function gridAction()
    {
        $objRespuesta      = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');        
        $objPeticion       = $this->get('request');        
		$objQueryNombre    = $objPeticion->query->get('query') ? $objPeticion->query->get('query') : "";
        $strNombre         = ($objQueryNombre != '' ? $objQueryNombre : $objPeticion->query->get('nombre'));
        $strIdentificacion = $objPeticion->query->get('identificacion');
        $strLogin          = $objPeticion->query->get('login');
        $strEstado         = $objPeticion->query->get('estado');        
        $intStart          = $objPeticion->query->get('start');
        $intLimit          = $objPeticion->query->get('limit');
        $arrayParametro    = array();

        $arrayParametro['NombreRazon']    = $strNombre;
        $arrayParametro['Identificacion'] = $strIdentificacion;
        if($strEstado !=null || $strEstado != 'Todos')
        {
            $arrayParametro['Estado']     = $strEstado;
        }
        $arrayParametro['NombrePara']  = 'HOLDING DE EMPRESAS';        
        $arrayParametro['intStart']    = $intStart;
        $arrayParametro['intLimit']    = $intLimit;        
        $objJson = $this->getDoctrine()->getManager("telconet")->getRepository('schemaBundle:AdmiParametroDet')->generarJsonHolding($arrayParametro);
        $objRespuesta->setContent($objJson);
        return $objRespuesta;
    }   
    
    /**
     * Documentación para el método 'editAction'.
     *
     * Método utilizado para editar un Holding existente.
     *
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.0 23-10-2020
     * 
     * @return Response $objResponse
    */
    public function editAction()
    {
        ini_set('max_execution_time', 9000000);
        
        $strMensaje          = "Error";
        $objRequest          = $this->get('request');      
        $objSession          = $objRequest->getSession();
        $emComercial         = $this->get('doctrine')->getManager();        
        
        $intIdParametroDet   = $objRequest->get('idParametroDet');
        $strIdentificacion   = $objRequest->get('identificacion');
        $strNombre           = $objRequest->get('nombre');
        $strEstado           = $objRequest->get('estado');
        $emGeneral           = $this->get('doctrine')->getManager('telconet_general');
        $serviceUtil         = $this->get('schema.Util');
        $serviceTelcoCrm     = $this->get('comercial.ComercialCrm');

        $emGeneral->getConnection()->beginTransaction();
        
        try
        {
            $objParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->find($intIdParametroDet);
            
            if(is_object($objParametroDet) && !empty($objParametroDet))
            {
                $arrayParametros      = array("strIdentificacion"  => $strIdentificacion,
                                              "strNombreActual"    => $strNombre,
                                              "strNombreAnterior"  => $objParametroDet->getValor1());
                $arrayParametrosWSCrm = array("arrayParametrosCRM" => $arrayParametros,
                                              "strOp"              => 'editHolding',
                                              "strFuncion"         => 'procesar');
                $arrayRespuestaWSCrm  = $serviceTelcoCrm->getRequestCRM($arrayParametrosWSCrm);

                $objParametroDet->setValor1($strNombre);
                $objParametroDet->setValor2($strIdentificacion);
                if(!empty($strEstado) && $strEstado != '')
                {
                    $objParametroDet->setEstado($strEstado);

                }
                $emGeneral->persist($objParametroDet);
                $emGeneral->flush();
                $emGeneral->commit();

                $strMensaje = "Datos Actualizados.";
            }
        }
        catch (\Exception $e) 
        {
            $strMensaje = "Error al Actualizar la Razón Social";
            
            $serviceUtil->insertError('TELCOS', 
                                      'editAction', 
                                      $e->getMessage(), 
                                      $objRequest->getSession()->get('user'), 
                                      $objRequest->getClientIp()
                                     );
            
            if($emGeneral->getConnection()->isTransactionActive())
            {
                $emGeneral->getConnection()->rollback();
                $emGeneral->close();
            }
        }
        
        return new Response($strMensaje);
    }



    /**
    * @Secure(roles="ROLE_452-1")
     * 
     * Documentación para el método 'newAction'.
     *
     * Método utilizado para crear un nuevo holding.
     *
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.0 23-10-2020
    */
    public function newAction()
    {
        $strTipo             = "nuevoHolding";
        $entity              = new AdmiParametroDet();
        $objRequest          = $this->get('request');
        $objSession          = $objRequest->getSession();
        $strPrefijoEmpresa   = $objRequest->getSession()->get('prefijoEmpresa');
        $strUsrCreacion         = $objRequest->getSession()->get('user');
        $intIdPersonaEmpresaRol = $objRequest->getSession()->get('idPersonaEmpresaRol');
        $em             = $this->getDoctrine()->getManager('telconet');

        if($strPrefijoEmpresa == 'TN')
        {
            $arrayResultadoCaracteristicas = $em->getRepository('schemaBundle:InfoPersona')->getCargosPersonas($strUsrCreacion);
            if( !empty($arrayResultadoCaracteristicas) )
            {
                $arrayResultadoCaracteristicas = $arrayResultadoCaracteristicas[0];
                $strTipoPersonal               = $arrayResultadoCaracteristicas['STRCARGOPERSONAL'] ? 
                                                                                        $arrayResultadoCaracteristicas['STRCARGOPERSONAL'] : 'Otros';
            }
            $arrayParametrosVend                          = array();
            $arrayParametrosVend['strPrefijoEmpresa']     = $strPrefijoEmpresa;
            $arrayParametrosVend['strTipoPersonal']       = $strTipoPersonal;
            $arrayParametrosVend['intIdPersonEmpresaRol'] = $intIdPersonaEmpresaRol;
            $arrayParametrosVend['boolHolding']           = true;
            
            $arrayResultadoVendAsignado = $em->getRepository('schemaBundle:InfoPersona')->getVendAsignado($arrayParametrosVend);
            $arrayVendedor = array();
            foreach($arrayResultadoVendAsignado['resultados'] as $arrayLogin)
            {
               $objPersona =  $em->getRepository('schemaBundle:InfoPersona')->findOneBy(array('login'=> $arrayLogin['LOGIN'], 'estado' => 'Activo'));
               if(is_object($objPersona) && !empty($objPersona))
               {
                   $arrayVendedor[$objPersona->getLogin()] = $objPersona->getNombres().' '.$objPersona->getApellidos();
               }
            }
        }
        $entityAdmiParametro = new AdmiParametroDet();
        $objForm             = $this->createForm(new AdmiHoldingType(array('vendedores' => $arrayVendedor)), $entity);    
        $emSeguridad         = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu      = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("418", "1");

        return $this->render('administracionBundle:AdmiHolding:new.html.twig', array(
            'item' => $entityItemMenu,
            'entity' => $entity,
            'form'   => $objForm->createView()
        ));
    }
    
    /**
     * 
     * @Secure(roles="ROLE_452-1")
     * 
     * Documentación para el método 'createAction'.
     *
     * Guarda la base ingresada por el usuario
     *
     * @return view 
     *
     * @version 1.0 Version Inicial
     *
     * @author David Leon <mdleon@telconet.ec>
     */
    public function createAction()
    {
        $objRequest          = $this->get('request');
        $objSession          = $objRequest->getSession();
        $strUserSession      = $objSession->get('user');
        $strDatetimeActual   = new \DateTime('now');
        $strEstadoActivo     = 'Activo';
        $strDescripcion      = 'Registro de Holding';
        $strIpCreacion       = $objRequest->getClientIp();
        $emGeneral           = $this->getDoctrine()->getManager("telconet_general");
        $emSeguridad         = $this->getDoctrine()->getManager('telconet_seguridad');	
        $entityItemMenu      = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("418", "1");		
        $objAdmiParametroDet = new AdmiParametroDet();
        $boolError           = false;
        $intIdEmpresa        = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $serviceUtil         = $this->get('schema.Util');

        $objForm = $this->createForm(new AdmiHoldingType(), $objAdmiParametroDet);        
            
        $emGeneral->getConnection()->beginTransaction();
        
        try
        {
            $arrayParametros = $objRequest->get('admiholdingtype'); 
            
            $strIdentificacion = $arrayParametros['valor2'];
            $strNombre         = $arrayParametros['valor1'];
            $strLogin          = $arrayParametros['valor3'];
            
            $objAdmiParametroCab    = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                ->findOneBy(array(
                                                                   'nombreParametro'   => 'HOLDING DE EMPRESAS',
                                                                   'estado'            => 'Activo'
                                                                )
                                                            );
            if(is_object($objAdmiParametroCab))
            {
                $objAdmiParametroDet->setParametroId($objAdmiParametroCab);            
                $objAdmiParametroDet->setDescripcion($strDescripcion);            
                $objAdmiParametroDet->setValor1($strNombre);
                $objAdmiParametroDet->setValor2($strIdentificacion);
                $objAdmiParametroDet->setValor3($strLogin);
                $objAdmiParametroDet->setEstado($strEstadoActivo);            
                $objAdmiParametroDet->setUsrCreacion($strUserSession);
                $objAdmiParametroDet->setFeCreacion($strDatetimeActual);
                $objAdmiParametroDet->setIpCreacion($strIpCreacion);
                $objAdmiParametroDet->setEmpresaCod($intIdEmpresa);

                $emGeneral->persist($objAdmiParametroDet);                
                $emGeneral->flush();            

                if ($emGeneral->getConnection()->isTransactionActive())
                {                
                    $emGeneral->getConnection()->commit();
                }
            }
        }
        catch (\Exception $ex)
        {
            $boolError = true;
            
            $serviceUtil->insertError('TELCOS', 
                                      'createAction', 
                                      $ex->getMessage(), 
                                      $objRequest->getSession()->get('user'), 
                                      $objRequest->getClientIp()
                                     );

            if($emGeneral->getConnection()->isTransactionActive())
            {
                $emGeneral->getConnection()->rollback();
            }
        }

        if($emGeneral->getConnection()->isTransactionActive())
        {
            $emGeneral->getConnection()->close();
        }
        
        if( $boolError )
        {
            return $this->render('administracionBundle:AdmiHolding:new.html.twig', array(
                                                                                        'item'           => $entityItemMenu,
                                                                                        'caracteristica' => $objAdmiParametroDet,
                                                                                        'form'           => $objForm->createView()
                                                                                       ));
        }
        else
        {            
            return $this->redirect($this->generateUrl('com_admiholding'));
        }
    }
    
    
    /**
     * 
     * @Secure(roles="ROLE_452-1")
     * 
     * Documentación para el método 'deleteAjaxAction'.
     *
     * Cambia de estado a 'Eliminado' de una base seleccionada
     *
     * @version 1.0 Version Inicial
     *
     * @author David Leon <mdleon@telconet.ec>
     */
    public function deleteAjaxAction()
    {
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/plain');        
        $objRequest         = $this->getRequest();
        $emGeneral          = $this->getDoctrine()->getManager("telconet_general");
        $objSession         = $objRequest->getSession();
        $strUserSession     = $objSession->get('user');
        $strDatetimeActual  = new \DateTime('now');
        $strEstadoEliminado = 'Eliminado';
        $strParametro       = $objRequest->get('param');
        $arrayParametro     = explode("|",$strParametro);        
        $intCantidadTotal   = count($arrayParametro);
        $serviceUtil        = $this->get('schema.Util');
        $emGeneral->getConnection()->beginTransaction();
                
        try
        {
            if( $intCantidadTotal>1 )
            {
                $strMensaje = "¡".$intCantidadTotal." registros eliminados de forma exitosa!";
            }
            else
            {
                $strMensaje = "¡El registro se ha eliminado de forma exitosa!";
            }            
            foreach($arrayParametro as $intId)
            {
                if (null == $objAdmiParametroDet = $emGeneral->find('schemaBundle:AdmiParametroDet', $intId))
                {
                    $objRespuesta->setContent("No existe la entidad");
                }
                else
                {
                    if(strtolower($objAdmiParametroDet->getEstado()) != "eliminado")
                    {
                        $objAdmiParametroDet->setEstado($strEstadoEliminado);     
                        $objAdmiParametroDet->setUsrUltMod($strUserSession);
                        $objAdmiParametroDet->setFeUltMod($strDatetimeActual);

                        $emGeneral->persist($objAdmiParametroDet);
                        $emGeneral->flush();
                                                                    
                    }
                    $objRespuesta->setContent($strMensaje);
                }
            }
            
            if ($emGeneral->getConnection()->isTransactionActive())
            {
                $emGeneral->getConnection()->commit();
            }
        }
        catch (\Exception $ex)
        {                    
            $serviceUtil->insertError('TELCOS', 
                                      'deleteAjaxAction', 
                                      $ex->getMessage(), 
                                      $objRequest->getSession()->get('user'), 
                                      $objRequest->getClientIp()
                                     );

            if($emGeneral->getConnection()->isTransactionActive())
            {
                $emGeneral->getConnection()->rollback();
            }
        }

        if($emGeneral->getConnection()->isTransactionActive())
        {
            $emGeneral->getConnection()->close();
        }
        
        return $objRespuesta;
    }
}
