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

class AdmiDocEnunciadoRespController  extends Controller implements TokenAuthenticatedController
{
 
    /**
    * @Secure(roles="ROLE_486-1")
    */
    public function indexAction()
    {
    
        
        $arrayRolesPermitidos = array(); 
        
       if (true === $this->get('security.context')->isGranted('ROLE_486-1'))
         {
            $arrayRolesPermitidos[] = 'ROLE_486-1';
         } 
        
        $emComercial         = $this->getDoctrine()->getManager('telconet');
        $emSeguridad         = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu      = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("486", "1");
        $objRequest          = $this->get('request');
        $objSession          = $objRequest->getSession();
        $strPrefijoEmpresa   = $objSession->get('prefijoEmpresa');
        $strEmpresaCod       = $objSession->get('idEmpresa');
        $strIdentificacionCliente = $objRequest->query->get('identificacionCliente');

        //obtener lista parametrizada de forma de contacto a filtrar
        $arrayFormaContacto =  array();
        $arrayParametrosDet  = $this->getDoctrine()->getManager()
        ->getRepository('schemaBundle:AdmiParametroDet')
        ->getOne('ACEPTACION_CLAUSULA_CONTRATO',
                                            '',
                                            '',
                                            'CONTACTOS FILTRO',
                                            '',
                                            '',
                                            '',
                                            '',
                                            '',
                                            $strEmpresaCod);

        if(!empty($arrayParametrosDet))
        {
            $strIdFormasContacto = $arrayParametrosDet['valor1'];
            $arrayIdFormasContacto = explode("-",$strIdFormasContacto);
                    
            for ($intIndex =0; $intIndex < count($arrayIdFormasContacto ); $intIndex ++)
             { 
                $intIdFormaContacto= $arrayIdFormasContacto[$intIndex ]; 

                $objFormaContacto = $this->getDoctrine()->getManager()
                                        ->getRepository('schemaBundle:AdmiFormaContacto') 
                                        ->findOneById($intIdFormaContacto);
                if(!empty($objFormaContacto))
                {   
                    
                     $arrayFormaContacto[]= array(
                        "id"=> $objFormaContacto->getId(),
                        "nombre"=> $objFormaContacto->getDescripcionFormaContacto()
                     ); 
                } 

            } 
        }  


        return $this->render('administracionBundle:AdmiDocEnunciadoResp:index.html.twig', array(
            'item'               => $entityItemMenu,
            'rolesPermitidos'    => $arrayRolesPermitidos,
            'strPrefijoEmpresa'  => $strPrefijoEmpresa, 
            'strListFormaContacto' =>  json_encode( $arrayFormaContacto), 
            'strIdentificacionCliente'=> $strIdentificacionCliente
        ));
    }
     
/**
     * managerAdminDocEnunciadoRespAction, método que realiza operaciones CRUD  para AdminDocEnunciadoResp
     *
     * @author Jefferson Alexy Carrillo <jacarrillo@telconet.ec>
     * @version 1.0 11-10-2022   
     *                    
     * @return Response lista de ADMI_DOC_ENUNCIADO_RESP.
     */      
    public function managerAdminDocEnunciadoRespAction()
    {

        $serviceRestClient   = $this->get('schema.RestClient');
        $serviceUtil            = $this->get('schema.Util');
        try
        {
        $serviceTokenCas     = $this->get('seguridad.TokenCas');
        $arrayTokenCas       = $serviceTokenCas->generarTokenCas(); 

        $objRequest          = $this->get('request');
        $objSession          = $objRequest->getSession();

        $strUrlBase          = $this->container->getParameter('ws_ms_politicaencuesta_url'); 
       
       
        $objParameter        = array();
        $strEmpresaCod       = $objSession->get('idEmpresa');
        $strUsrCreacion      = $objSession->get('user');
        $strMetodo           = $objRequest->get('strMetodo');  
        $strParametros       = $objRequest->get('strParametros');
        $objParameter        = json_decode($strParametros, true);
        
        $objParameter['empresaCod']  = $strEmpresaCod ;
        $objParameter['ipCreacion']  = '127.0.0.1';  
        $arrayOptions       = array(
                                    CURLOPT_SSL_VERIFYPEER => false,
                                    CURLOPT_HTTPHEADER     => array(
                                        'Content-Type: application/json',
                                        'tokencas: ' . $arrayTokenCas['strToken']
                                    )
                                );

        if ($strMetodo=='listRespuestas') 
        {
            $strUrl =  $strUrlBase . 'busquedaLista'; 
            $objParameter['arrayEstados']=  array('Activo');  
            $objParameter['usrCreacion'] = $strUsrCreacion; 
        }
        else 
        {
            throw new \Exception('Metodo '. $strMetodo.' no implementado');
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
            ." de  ms-comp-security-politica-encuesta. Favor Notificar a Sistemas" . $e->getMessage();

            $serviceUtil->insertError(
                                        'Telcos+',
                                        'AdmiDocEnunciadoResp.managerAdminDocEnunciadoRespAction',
                                        'Error AdmiDocEnunciadoResp.managerAdminDocEnunciadoRespAction:' . $e->getMessage(),
                                        $strUsrCreacion,
                                        '127.0.0.1'
                                    );
        }
        $objResponse        = new Response(json_encode($arrayResultado));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

   
}
