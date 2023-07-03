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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\SecurityExtraBundle\Annotation\Secure;

class AdmiPuntoAtencionController extends Controller implements TokenAuthenticatedController
{ 
    
    
     /**
    * @Secure(roles="ROLE_459-1")
    */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager('telconet');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("37", "1");

        $entityPuntoAtencion = $em->getRepository('schemaBundle:AdmiPuntoAtencion')->findAll();

        return $this->render('administracionBundle:AdmiPuntoAtencion:index.html.twig', array(
            'item' => $entityItemMenu,
            'admiPuntoAtencion' => $entityPuntoAtencion
        ));
    }
    
    
    /**
     * Función que se encarga de guardar un nuevo punto de atención
     * 
     * @author Ivan Mata <imata@telconet.ec>
     * @version 1.0 04-05-2021
     */
    public function ajaxGuardarPuntoAtencionAction()
    {
        $objRequest         = $this->getRequest();
        $objSession         = $objRequest->getSession();
        $objResponse        = new JsonResponse();
        $arrayResultado     = array();
        $strEmpresaCod      = $objSession->get('idEmpresa');
        $strUsuarioCreacion = $objSession->get('user');
        $strPuntoAtencion   = $objRequest->get("strNombrePuntoAtencion");
        $strIpCreacion      = $objRequest->getClientIp();

        $servicePuntoAtencion  = $this->get('administracion.PuntoAtencion');
        $serviceUtil           = $this->get('schema.Util');
        
        try 
        {
            
            $arrayParametros = array('strEmpresaCod'    => $strEmpresaCod,
                                     'strPuntoAtencion' => $strPuntoAtencion,
                                     'strUsrCreacion'   => $strUsuarioCreacion,
                                     'strIpCreacion'    => $strIpCreacion);
        
            $arrayResultado = $servicePuntoAtencion->guardarPuntoAtencion($arrayParametros);
            
        } 
        catch (Exception $ex) 
        {
            $arrayResultado['strStatus']  = 'ERROR';
            $arrayResultado['strMensaje'] = "Se presento un error al guardar un punto de atencion";
            
            $serviceUtil->insertError( 'Telcos+',
                                       'AdministracionBundle.AdmiPuntoAtencionController.ajaxGuardarPuntoAtencionAction',
                                       $ex->getMessage(),
                                       $strUsuarioCreacion,
                                       $strIpCreacion);
            
        }
        
        $objResponse->setData($arrayResultado);
        return $objResponse;
    }
    
     /**
     * Función que se encarga de llenar el grid con la información del punto de atención
     * 
     * @author Ivan Mata <imata@telconet.ec>
     * @version 1.0 04-05-2021
     */
    public function gridAction()
    {
        
        $objRequest     = $this->getRequest();
        $objSession     = $objRequest->getSession();
        $arrayRespuesta = new Response();
        $arrayRespuesta->headers->set('Content-Type', 'text/json');
        
        $intStart      = $objRequest->get('start');
        $intLimit      = $objRequest->get('limit');
        $strEstado     = $objRequest->get('estado');
        $strNombres    = $objRequest->get('strNombres');
        
        $strEmpresaCod = $objSession->get('idEmpresa');
        
        $arrayParametros  = array("intStart"      => $intStart,
                                  "intLimit"      => $intLimit,
                                  "strEmpresaCod" => $strEmpresaCod,
                                  "strEstado"     => $strEstado,
                                  "strNombres"    => $strNombres);
        
        $objJson = $this->getDoctrine()->getManager("telconet")->getRepository('schemaBundle:AdmiPuntoAtencion')
                        ->generarJsonPuntosAtencion($arrayParametros);
        $arrayRespuesta->setContent($objJson);
        
        return $arrayRespuesta;
    }
   
    
    /**
     * Función que se encarga de redireccionar al boton de agregar nuevo punto de atención
     * 
     * @author Ivan Mata <imata@telconet.ec>
     * @version 1.0 04-05-2021
     */
    public function newAction()
    {
        
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("37", "1");
        
        return $this->render('administracionBundle:AdmiPuntoAtencion:new.html.twig', array(
            'item' => $entityItemMenu
        ));
    }
    
    
    /**
     * Función para cargar información al twig edit
     * 
     * @author Ivan Mata <imata@telconet.ec>
     * @version 1.0 04-05-2021
     */
    public function editAction($intIdPuntoAtencion)
    {
        
        $objRequest           = $this->getRequest();
        $objSession           = $objRequest->getSession();
        $objResponse          = new JsonResponse();
        $arrayResultado       = array();
        $strIpCreacion        = $objRequest->getClientIp();
        $emComercial          = $this->getDoctrine()->getManager('telconet');
        
        
        $objPuntoAtencion  = $emComercial->getRepository('schemaBundle:AdmiPuntoAtencion')
                                         ->findOneBy(array('id'  => $intIdPuntoAtencion));
        
        
        $strNombrePuntoAtencion = $objPuntoAtencion->getNombrePuntoAtencion();
        
        $arrayParametros = array('intIdPuntoAtencion'     => $intIdPuntoAtencion,
                                 'strNombrePuntoAtencion' => $strNombrePuntoAtencion);
        
        
        return $this->render('administracionBundle:AdmiPuntoAtencion:edit.html.twig', $arrayParametros);
        
    }
    
    
    
    /**
     * Función para eliminar un punto de atención.
     * 
     * @author Ivan Mata <imata@telconet.ec>
     * @version 1.0 04-05-2021
     */
    public function deleteAction()
    {
        $objRequest           = $this->getRequest();
        $objSession           = $objRequest->getSession();
        $objResponse          = new JsonResponse();
        $arrayResultado       = array();
        $intIdPuntoAtencion   = $objRequest->get("idPuntoAtencion");
        $strUsuarioCreacion   = $objSession->get('user');
        $strIpCreacion        = $objRequest->getClientIp();
        
        $servicePuntoAtencion  = $this->get('administracion.PuntoAtencion');
        $serviceUtil           = $this->get('schema.Util');
        
        try 
        {
            
            $arrayParametros = array('intIdPuntoAtencion'  => $intIdPuntoAtencion,
                                     'strUsrCreacion'      => $strUsuarioCreacion,
                                     'strIpCreacion'       => $strIpCreacion);
            
            $arrayResultado = $servicePuntoAtencion->eliminarPuntoAtencion($arrayParametros);
            
            
            
        } 
        catch (Exception $ex) 
        {
            $arrayResultado['strStatus']  = 'ERROR';
            $arrayResultado['strMensaje'] = "Se presento un error al eliminar el punto de atención";
            
            $serviceUtil->insertError( 'Telcos+',
                                       'AdministracionBundle.AdmiPuntoAtencionController.deleteAction',
                                       $ex->getMessage(),
                                       $strUsuarioCreacion,
                                       $strIpCreacion);
        }
        
        $objResponse->setData($arrayResultado);
        return $objResponse;
        
        
    }

  
    /**
     * Función para editar un punto de atención.
     * 
     * @author Ivan Mata <imata@telconet.ec>
     * @version 1.0 04-05-2021
     */
    public function ajaxEditarPuntoAtencionAction()
    {
        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $objResponse            = new JsonResponse();
        $arrayResultado         = array();
        $intIdPuntoAtencion     = $objRequest->get("intIdPuntoAtencion");
        $strNombrePuntoAtencion = $objRequest->get("strNombrePuntoAtencion");
        $strUsuarioCreacion     = $objSession->get('user');
        $strIpCreacion          = $objRequest->getClientIp();
        
        $servicePuntoAtencion  = $this->get('administracion.PuntoAtencion');
        $serviceUtil           = $this->get('schema.Util');
        
        try 
        {
            $arrayParametros = array('intIdPuntoAtencion'     => $intIdPuntoAtencion,
                                     'strNombrePuntoAtencion' => $strNombrePuntoAtencion,
                                     'strUsrCreacion'         => $strUsuarioCreacion,
                                     'strIpCreacion'          => $strIpCreacion);
            
            $arrayResultado = $servicePuntoAtencion->editarPuntoAtencion($arrayParametros);
        } 
        catch (Exception $ex) 
        {
            $arrayResultado['strStatus']  = 'ERROR';
            $arrayResultado['strMensaje'] = "Se presento un error al eliminar el punto de atención";
            
            $serviceUtil->insertError( 'Telcos+',
                                       'AdministracionBundle.AdmiPuntoAtencionController.ajaxEditarPuntoAtencionAction',
                                       $ex->getMessage(),
                                       $strUsuarioCreacion,
                                       $strIpCreacion);
        }
        
        $objResponse->setData($arrayResultado);
        return $objResponse;
        
    }

   
    
}
