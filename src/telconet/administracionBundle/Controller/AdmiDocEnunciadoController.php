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

class AdmiDocEnunciadoController extends Controller implements TokenAuthenticatedController
{
 
    /**
    * @Secure(roles="ROLE_487-1")
    */
    public function indexAction()
    {
    
        $arrayRolesPermitidos = array(); 
        
        if (true === $this->get('security.context')->isGranted('ROLE_487-1'))
        {
            $arrayRolesPermitidos[] = 'ROLE_487-1';
        }
        
        $emComercial         = $this->getDoctrine()->getManager('telconet');
        $emSeguridad         = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu      = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("487", "1");
        $objRequest          = $this->get('request');
        $objSession          = $objRequest->getSession();
        $strPrefijoEmpresa   = $objSession->get('prefijoEmpresa');
        $strEmpresaCod       = $objSession->get('idEmpresa');

        $arrayParametrosRespuestaUnica = $this->getDoctrine()->getManager()
        ->getRepository('schemaBundle:AdmiParametroDet')
        ->getOne('ADMIN_POLITICAS_CLAUSULAS',
                                            '',
                                            '',
                                            'NOMBRES_RESPUESTA_UNICA',
                                            '',
                                            '',
                                            '',
                                            '',
                                            '',
                                            $strEmpresaCod);


       if(!empty($arrayParametrosRespuestaUnica))
        {
            $strNombresRespuesta = $arrayParametrosRespuestaUnica['valor1'];
            $arrayNombresRespuestaUnica = explode(",",$strNombresRespuesta);                    
            
        }  


        return $this->render('administracionBundle:AdmiDocEnunciado:index.html.twig', array(
            'item'                  => $entityItemMenu,
            'rolesPermitidos'       => $arrayRolesPermitidos,
            'strPrefijoEmpresa'     => $strPrefijoEmpresa,
            'strListRespuestaUnica' => json_encode($arrayNombresRespuestaUnica)
        ));
    }
    
  
    /**
     * managerAdminProcesosAction, método que realiza operaciones CRUD
     *
     * @author Jefferson Alexy Carrillo <jacarrillo@telconet.ec>
     * @version 1.0 11-10-2022   
     *                    
     * @return Response lista de ADMI_PROCESO.
     */      
    public function managerAdminProcesosAction()
    {

        $serviceRestClient   = $this->get('schema.RestClient');
        $serviceUtil            = $this->get('schema.Util');
        try
        {
        $serviceTokenCas     = $this->get('seguridad.TokenCas');
        $arrayTokenCas       = $serviceTokenCas->generarTokenCas(); 

        $objRequest          = $this->get('request');
        $objSession          = $objRequest->getSession();

        $strUrlBase          = $this->container->getParameter('ws_ms_documento_url');
       
       
        $objParameter        = array();
        $strEmpresaCod       = $objSession->get('idEmpresa');
        $strUsrCreacion      = $objSession->get('user');
        $strMetodo           = $objRequest->get('strMetodo');   
        $strParametros       = $objRequest->get('strParametros');
        $objParameter        = json_decode($strParametros, true); 
        $objParameter['empresaCod']  =  $strEmpresaCod;
        $objParameter['ipCreacion']  = '127.0.0.1'; 

        $arrayOptions       = array(
                                    CURLOPT_SSL_VERIFYPEER => false,
                                    CURLOPT_HTTPHEADER     => array(
                                        'Content-Type: application/json',
                                        'tokencas: ' . $arrayTokenCas['strToken']
                                    )
                                );
                                
        $strRoot= 'admiProceso/'; 
 
        switch ($strMetodo) 
        {
        case 'list':
            $strUrl =  $strUrlBase . $strRoot.'listarPor'; 
            $objParameter['estado']='Activo';   
        break;
        case 'create':
            $strUrl =  $strUrlBase . $strRoot.'crear';  
            $objParameter['usrCreacion'] = $strUsrCreacion;  
        break;
        case 'edit':
            $strUrl =  $strUrlBase . $strRoot.'edit'; 
            $objParameter['usrUltMod']   = $strUsrCreacion; 
        break;
        case 'delete':
            $strUrl =  $strUrlBase . $strRoot.'delete';  
            $objParameter['usrUltMod']   = $strUsrCreacion; 
        break;

        default:
        throw new \Exception('Metodo '. $strMetodo.' no implementado');
        break;
        }                         


 
      
        $arrayResponse      = $serviceRestClient->postJSON( $strUrl, json_encode( $objParameter), $arrayOptions);
        if(!$arrayResponse['result'])
        {
            throw new \Exception($arrayResponse['message']);
        }
        $arrayResultado  = json_decode($arrayResponse['result'], true);

        }
        catch (\Exception $e)
        {
            $arrayResultado['status']  = 'ERROR';
            $arrayResultado['message'] = "Error al ejecutar el proceso ". $strMetodo
            ." de  ms-core-doc-documento. Favor Notificar a Sistemas" . $e->getMessage();

            $serviceUtil->insertError(
                                        'Telcos+',
                                        'AdmiDocEnunciado.managerAdminProcesosAction',
                                        'Error AdmiDocEnunciado.managerAdminProcesosAction:' . $e->getMessage(),
                                        $strUsrCreacion,
                                        '127.0.0.1'
                                    );
        }
        $objResponse        = new Response(json_encode($arrayResultado));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
     * managerAdminDocumentosAction, método que realiza operaciones CRUD
     *
     * @author Jefferson Alexy Carrillo <jacarrillo@telconet.ec>
     * @version 1.0 11-10-2022   
     *                    
     * @return Response lista de ADMI_DOCUMENTOS.
     */      
    public function managerAdminDocumentosAction()
    {

        $serviceRestClient   = $this->get('schema.RestClient');
        $serviceUtil            = $this->get('schema.Util');
        try
        {
        $serviceTokenCas     = $this->get('seguridad.TokenCas');
        $arrayTokenCas       = $serviceTokenCas->generarTokenCas(); 

        $objRequest          = $this->get('request');
        $objSession          = $objRequest->getSession();

        $strUrlBase          = $this->container->getParameter('ws_ms_documento_url'); 
       
       
        $objParameter        = array();
        $strEmpresaCod   = $objSession->get('idEmpresa');
        $strUsrCreacion      = $objSession->get('user');
        $strMetodo           = $objRequest->get('strMetodo');  
        $strParametros       = $objRequest->get('strParametros');
        $objParameter        = json_decode($strParametros, true);
        
        $objParameter['empresaCod']  =  $strEmpresaCod;
        $objParameter['ipCreacion']  = '127.0.0.1'; 

        $arrayOptions       = array(
                                    CURLOPT_SSL_VERIFYPEER => false,
                                    CURLOPT_HTTPHEADER     => array(
                                        'Content-Type: application/json',
                                        'tokencas: ' . $arrayTokenCas['strToken']
                                    )
                                );

                
        $strRoot= 'admiDocumento/'; 
      
        switch ($strMetodo)
         {
        case 'list':
            $strUrl =  $strUrlBase . $strRoot.'listarPor'; 
            $objParameter['estado']='Activo';   
        break;
        case 'create':
            $strUrl =  $strUrlBase . $strRoot.'crear';   
            $objParameter['usrCreacion'] = $strUsrCreacion; 
        break;
        case 'edit':
            $strUrl =  $strUrlBase . $strRoot.'edit'; 
            $objParameter['usrUltMod']   = $strUsrCreacion; 
        break;
        case 'delete':
            $strUrl =  $strUrlBase . $strRoot.'delete';  
            $objParameter['usrUltMod']   = $strUsrCreacion; 
        break;

        default:
        throw new \Exception('Metodo '. $strMetodo.' no implementado');
        break;
        }                          
    
 
      
        $arrayResponse      = $serviceRestClient->postJSON( $strUrl, json_encode( $objParameter), $arrayOptions);
        if(!$arrayResponse['result'])
        {
            throw new \Exception($arrayResponse['message']);
        }
        $arrayResultado     = json_decode($arrayResponse['result'], true);
       

        }
        catch (\Exception $e)
        {
            $arrayResultado['status']  = 'ERROR';
            $arrayResultado['message'] = "Error al ejecutar el proceso ". $strMetodo
            ." de  ms-core-doc-documento. Favor Notificar a Sistemas" . $e->getMessage();

            $serviceUtil->insertError(
                                        'Telcos+',
                                        'AdmiDocEnunciado.managerAdminDocumentosAction',
                                        'Error AdmiDocEnunciado.managerAdminDocumentosAction:' . $e->getMessage(),
                                        $strUsrCreacion,
                                        '127.0.0.1'
                                    );
        }
        $objResponse        = new Response(json_encode($arrayResultado));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    
    /**
     * managerAdminRespuestasAction, método que realiza operaciones CRUD
     *
     * @author Jefferson Alexy Carrillo <jacarrillo@telconet.ec>
     * @version 1.0 11-10-2022   
     *                    
     * @return Response lista de ADMI_RESPUESTA.
     */      
    public function managerAdminRespuestasAction()
    {

        $serviceRestClient   = $this->get('schema.RestClient');
        $serviceUtil            = $this->get('schema.Util');
        try
        {
        $serviceTokenCas     = $this->get('seguridad.TokenCas');
        $arrayTokenCas       = $serviceTokenCas->generarTokenCas(); 

        $objRequest          = $this->get('request');
        $objSession          = $objRequest->getSession();

        $strUrlBase          = $this->container->getParameter('ws_ms_documento_url'); 
       
       
        $objParameter        = array();
        $strEmpresaCod       = $objSession->get('idEmpresa');
        $strUsrCreacion      = $objSession->get('user');
        $strMetodo           = $objRequest->get('strMetodo');  
        $strParametros       = $objRequest->get('strParametros');
        $objParameter        = json_decode($strParametros, true);
        
        $objParameter['empresaCod']  = $strEmpresaCod;
        $objParameter['ipCreacion']  = '127.0.0.1'; 

        $arrayOptions       = array(
                                    CURLOPT_SSL_VERIFYPEER => false,
                                    CURLOPT_HTTPHEADER     => array(
                                        'Content-Type: application/json',
                                        'tokencas: ' . $arrayTokenCas['strToken']
                                    )
                                );


                    

            
        $strRoot= 'admiRespuesta/';  
       
        switch ($strMetodo)
         {
        case 'list':
            $strUrl =  $strUrlBase . $strRoot.'listarPor'; 
            $objParameter['estado']='Activo';   
        break;
        case 'create':
            $strUrl =  $strUrlBase . $strRoot.'crear';   
            $objParameter['usrCreacion'] = $strUsrCreacion; 
        break;
        case 'edit':
            $strUrl =  $strUrlBase . $strRoot.'edit'; 
            $objParameter['usrUltMod']   = $strUsrCreacion; 
        break;
        case 'delete':
            $strUrl =  $strUrlBase . $strRoot.'delete';  
            $objParameter['usrUltMod']   = $strUsrCreacion; 
        break;

        default:
        throw new \Exception('Metodo '. $strMetodo.' no implementado');
        break;
        }     
 
      
        $arrayResponse      = $serviceRestClient->postJSON( $strUrl, json_encode( $objParameter), $arrayOptions);

        if(!$arrayResponse['result'])
        {
            throw new \Exception($arrayResponse['message']);
        }
        $arrayResultado     = json_decode($arrayResponse['result'], true);

        }
        catch (\Exception $e)
        {
            $arrayResultado['status']  = 'ERROR';
            $arrayResultado['message'] = "Error al ejecutar el proceso ". $strMetodo
            ." de  ms-core-doc-documento. Favor Notificar a Sistemas" . $e->getMessage();

            $serviceUtil->insertError(
                                        'Telcos+',
                                        'AdmiDocEnunciado.managerAdminRespuestasAction',
                                        'Error AdmiDocEnunciado.managerAdminRespuestasAction:' . $e->getMessage(),
                                        $strUsrCreacion,
                                        '127.0.0.1'
                                    );
        }
        $objResponse        = new Response(json_encode($arrayResultado));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }


/**
     * managerAdminDocEnunciadoAction, método que realiza operaciones CRUD 
     *
     * @author Jefferson Alexy Carrillo <jacarrillo@telconet.ec>
     * @version 1.0 11-10-2022   
     *                    
     * @return Response lista de ADMI_DOC_ENUNCIADO
     */      
    public function managerAdminDocEnunciadoAction()
    {

        $serviceRestClient   = $this->get('schema.RestClient');
        $serviceUtil            = $this->get('schema.Util');
        try
        {
        $serviceTokenCas     = $this->get('seguridad.TokenCas');
        $arrayTokenCas       = $serviceTokenCas->generarTokenCas(); 

        $objRequest          = $this->get('request');
        $objSession          = $objRequest->getSession();

        $strUrlBase          = $this->container->getParameter('ws_ms_documento_url'); 
       
       
        $objParameter        = array();
        $strEmpresaCod   = $objSession->get('idEmpresa');
        $strUsrCreacion      = $objSession->get('user');
        $strMetodo           = $objRequest->get('strMetodo');  
        $strParametros       = $objRequest->get('strParametros');
        $objParameter        = json_decode($strParametros, true);
        
        $objParameter['empresaCod']  =  $strEmpresaCod;
        $objParameter['ipCreacion']  = '127.0.0.1'; 
        $objParameter['nombreProceso'] ="LinkDatosBancarios";
        $arrayOptions       = array(
                                    CURLOPT_SSL_VERIFYPEER => false,
                                    CURLOPT_HTTPHEADER     => array(
                                        'Content-Type: application/json',
                                        'tokencas: ' . $arrayTokenCas['strToken']
                                    )
                                );


                    

            
        $strRoot= 'administracionPlantilla/';  
        $objParameter['nombreProceso'] ="LinkDatosBancarios";
        switch ($strMetodo) 
        {
        case 'list': 
            $strUrl =   $strUrlBase . $strRoot.'listarPoliticasClausulas'; 
            $objParameter['estado']='Activo';   
            $objParameter['mostrarTodo']="S";   
            $objParameter['usrCreacion'] = $strUsrCreacion; 
        break;
        case 'create':
            $strUrl =  $strUrlBase . $strRoot.'crearPoliticasClausulas';   
            $objParameter['usrCreacion'] = $strUsrCreacion; 
        break;
        case 'edit':
            $strUrl =  $strUrlBase . $strRoot.'editarPoliticasClausulas'; 
            $objParameter['usrUltMod']   = $strUsrCreacion; 
            $objParameter['usrCreacion'] = $strUsrCreacion; 
        break;
        case 'delete':
            $strUrl =  $strUrlBase . $strRoot.'deletePoliticasClausulas';  
            $objParameter['usrUltMod']   = $strUsrCreacion;  
        break;

        default:
        throw new \Exception('Metodo '. $strMetodo.' no implementado');
        break;
        }     
 
 
      
        $arrayResponse      = $serviceRestClient->postJSON( $strUrl, json_encode( $objParameter), $arrayOptions);

        if(!$arrayResponse['result'])
        {
            throw new \Exception($arrayResponse['message']);
        }
        $arrayResultado     = json_decode($arrayResponse['result'], true);


        }
        catch (\Exception $e)
        {
            $arrayResultado['status']  = 'ERROR';
            $arrayResultado['message'] = "Error al ejecutar el proceso ". $strMetodo
            ." de  ms-core-doc-documento. Favor Notificar a Sistemas" . $e->getMessage();

            $serviceUtil->insertError(
                                        'Telcos+',
                                        'AdmiDocEnunciado.managerAdminDocEnunciadoAction',
                                        'Error AdmiDocEnunciado.managerAdminDocEnunciadoAction:' . $e->getMessage(),
                                        $strUsrCreacion,
                                        '127.0.0.1'
                                    );
        }
        $objResponse        = new Response(json_encode($arrayResultado));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }



    /**
     * getGlobalListData,  método que consulta en forma global
     *
     * @author Jefferson Alexy Carrillo <jacarrillo@telconet.ec>
     * @version 1.0 11-10-2022   
     *                    
     * @return Response lista.
     */      
    public function getGlobalListDataAction()
    {

        $serviceRestClient   = $this->get('schema.RestClient');
        $serviceUtil            = $this->get('schema.Util');

        try
        {
        $serviceTokenCas     = $this->get('seguridad.TokenCas');
        $arrayTokenCas       = $serviceTokenCas->generarTokenCas();        
        $objRequest          = $this->get('request');
        $objSession          = $objRequest->getSession();

        $strUrlBase          = $this->container->getParameter('ws_ms_documento_url'); 
       
       
        $objParameter        = array();
        $strEmpresaCod       = $objSession->get('idEmpresa');
        $strUsrCreacion      = $objSession->get('user');
        $strMetodo           = $objRequest->get('strMetodo');  
        $strParametros       = $objRequest->get('strParametros');
        $objParameter        = json_decode($strParametros, true);
        
        $objParameter['usrCreacion'] = $strUsrCreacion; 
        $objParameter['empresaCod']  = $strEmpresaCod;
        $objParameter['ipCreacion']  = '127.0.0.1'; 

        $arrayOptions       = array(
                                    CURLOPT_SSL_VERIFYPEER => false,
                                    CURLOPT_HTTPHEADER     => array(
                                        'Content-Type: application/json',
                                        'tokencas: ' . $arrayTokenCas['strToken']
                                    )
                                );


        switch ($strMetodo)
         {
        case 'listProcDocu':
            $strUrl =  $strUrlBase .'/administracionPlantilla/procesosDocumentos'; 
            $objParameter['estado']='Activo';   
        break;
        case 'listInputPlantilla':
            $strUrl =  $strUrlBase .'/administracionPlantilla/inputPlantilla'; 
            $objParameter['estado']='Activo';   
        break;  
      
        default:
        throw new \Exception('Metodo '. $strMetodo.' no implementado');
        break;
        }                        
 
        $arrayResponse      = $serviceRestClient->postJSON( $strUrl, json_encode( $objParameter), $arrayOptions);
      
        if(!$arrayResponse['result'])
        {
            throw new \Exception($arrayResponse['message']);
        }
        $arrayResultado     = json_decode($arrayResponse['result'], true);


        }
        catch (\Exception $e)
        {
            $arrayResultado['status']  = 'ERROR';
            $arrayResultado['message'] = "Error al ejecutar el proceso ". $strMetodo
            ." de  ms-core-doc-documento. Favor Notificar a Sistemas" . $e->getMessage();

            $serviceUtil->insertError(
                                        'Telcos+',
                                        'AdmiDocEnunciado.managerGlobalListDataAction',
                                        'Error AdmiDocEnunciado.managerGlobalListDataAction:' . $e->getMessage(),
                                        $strUsrCreacion,
                                        '127.0.0.1'
                                    );
        }
        $objResponse        = new Response(json_encode($arrayResultado));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

}
