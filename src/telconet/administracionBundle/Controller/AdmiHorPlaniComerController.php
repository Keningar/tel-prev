<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiParametroCab;
use telconet\schemaBundle\Entity\AdmiParametroDet;
use telconet\schemaBundle\Form\AdmiParametroDetType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response; 
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse; 
use JMS\SecurityExtraBundle\Annotation\Secure;

class AdmiHorPlaniComerController extends Controller implements TokenAuthenticatedController
{ 
    /**
    * @Secure(roles="ROLE_482-1")
    */
    public function indexAction()
    {
    

        $emGeneral              = $this->getDoctrine()->getManager("telconet_general");

        $arrayRolesPermitidos = array();


        if (true === $this->get('security.context')->isGranted('ROLE_482-1'))
        {
            $arrayRolesPermitidos[] = 'ROLE_482-1';
        }
       
        $emComercial    = $this->getDoctrine()->getManager('telconet');
        $emSeguridad    = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("482", "1");
        $entityAdmiParametro         = $emComercial->getRepository('schemaBundle:AdmiParametroDet')->findAll();

        $arrayAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
        ->getOne('PLANIFICACION_COMERCIAL_HAL','COMERCIAL','','INCREMENT MIN DE LAS HORAS PLANIFICACION COMERCIAL','','','','','','18');

     

        if ($arrayAdmiParametroDet)
        {
            $strTiempoIntervaloMinuto = $arrayAdmiParametroDet['valor1'];
        }else
        {
            $strTiempoIntervaloMinuto = 15;
        }   
        
        

        $arrayAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
        ->getOne('PLANIFICACION_COMERCIAL_HAL','COMERCIAL','','MIN HORAS PLANIFICACION COMERCIAL','','','','','','18');

        if ($arrayAdmiParametroDet)
        {
            $strMinHora = $arrayAdmiParametroDet['valor1'];
        }else
        {
            $strMinHora = "00:00";
        }   



        $arrayAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
        ->getOne('PLANIFICACION_COMERCIAL_HAL','COMERCIAL','','MAX HORAS PLANIFICACION COMERCIAL','','','','','','18');

        if ($arrayAdmiParametroDet)
        {
            $strMaxHora = $arrayAdmiParametroDet['valor1'];
        }else
        {
            $strMaxHora = "23:59";
        }   

        
        return $this->render('administracionBundle:AdmiHorarioPlanificacion:index.html.twig', array(
            'item' => $entityItemMenu,
            'caracteristica' => $entityAdmiParametro,
            'rolesPermitidos' => $arrayRolesPermitidos,
            'tiempoMinuto' =>  $strTiempoIntervaloMinuto,
            'minHora' =>  $strMinHora,
            'maxHora' =>  $strMaxHora


        ));
    }
    
    /**
     * @Secure(roles="ROLE_482-1")
     * 
     * Documentación para el método 'gridAction'.
     *
     * Muestra la información de una base guardada
     *
     * @return view 
     *
     * @version 1.0 Version Inicial
     *
     * @author Carlos Caguana <ccaguana@telconet.ec>
     */
    public function gridAction()
    {

        $objSession          = $this->get( 'session' );
        $strUsrCreacion      = $objSession->get('user');
        $objPeticion         = $this->get('request');
        $strIpCreacion       = $objPeticion->getClientIp();
        $serviceUtil         = $this->get('schema.Util');
        $arrayData           = array();
        try
        {

            $arrayParametros                     = array();
            $arrayParametros['ipCreacion']       = $strIpCreacion;
            $arrayParametros['usrCreacion']      = $strUsrCreacion;
            $arrayParametros['modulo']           = "Comercial";
            $arrayParametros['estado']           = "Activo";

             $strUrlMsConsulta = $this->container->getParameter('planificacion.comercial.url.getRegistroCalendario');

            $serviceTokenCas = $this->get('seguridad.TokenCas');
            $arrayTokenCas = $serviceTokenCas->generarTokenCas();

            if(empty($arrayTokenCas['strToken']))
            {
                throw new \Exception($arrayTokenCas['strMensaje']); 
            }            

            $arrayRestUpdate[CURLOPT_HTTPHEADER]     = array('Content-Type: application/json',
                                                             'tokencas: '.$arrayTokenCas['strToken']);
            $arrayRestUpdate[CURLOPT_TIMEOUT]        = 900000;
            $serviceRest = $this->get('schema.RestClient');


            $arrayRespuesta = $serviceRest->postJSON($strUrlMsConsulta,json_encode($arrayParametros), $arrayRestUpdate);


            $arrayData = json_decode($arrayRespuesta['result'], true);

            if (empty($arrayData))
            {
                throw new \Exception('ERROR ' . "No se pudo consultar la información con el ms planificación", 1);
            }


            if ($arrayData['status'] != "OK")
            {
                throw new \Exception('ERROR ' . $arrayData['message'], 1);
            }

    
            $arrayData=$arrayData['data'];
        }
        catch(\Exception $e)
        {
             error_log( $e->getMessage());
             
            

             $serviceUtil->insertError('Telcos+',
            'AdmiHorPlaniComerController.gridAction',
             $e->getMessage(),
             $strUserSession,
             $strIpCreacion);       
             
        }


        $objResponse = new Response(json_encode(array('intTotal' => 0, 'data' =>  $arrayData)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }


    
    /**
     * @Secure(roles="ROLE_482-1")
     * Documentación para el método 'saveCalendarioAction'.
     *
     * Metodo que consumo el ms de planificación para guardar la información
     *     
     * @version 1.0 Version Inicial
     *
     * @author Carlos Caguana <ccaguana@telconet.ec>
     */
    public function saveCalendarioAction()
    {
        $strUrlMsSaveCalendario = $this->container->getParameter('planificacion.comercial.url.saveCalendario');
        $objRespuesta   = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/plain');
        $objPeticion       = $this->get('request');
        $serviceUtil       = $this->get('schema.Util');        
        $strValor             = $objPeticion->get('valor');
        $strNombreDia         = $objPeticion->get('nombreDia');
        $strUserSession    = $objPeticion->getSession()->get('user');
        $strIpCreacion     = $objPeticion->getClientIp();

        
    try
    {

        $arrayParametros   = array();
        $arrayParametros['valor']               = $strValor;
        $arrayParametros['nombreDia']           = $strNombreDia;
        $arrayParametros['estado']              = "Activo";
        $arrayParametros['modulo']              = "Comercial";
        $arrayParametros['ipCreacion']          = $strIpCreacion;
        $arrayParametros['usrCreacion']         = $strUserSession;

             
        $serviceTokenCas = $this->get('seguridad.TokenCas');
        $arrayTokenCas = $serviceTokenCas->generarTokenCas();

        if(empty($arrayTokenCas['strToken']))
        {
            throw new \Exception($arrayTokenCas['strMensaje']); 
        }            

        $arrayRestUpdate[CURLOPT_HTTPHEADER]     = array('Content-Type: application/json',
                                                         'tokencas: '.$arrayTokenCas['strToken']);        
        $arrayRestUpdate[CURLOPT_TIMEOUT]        = 900000;
        $serviceRest = $this->get('schema.RestClient');

    
        $arrayRespuesta = $serviceRest->postJSON($strUrlMsSaveCalendario,json_encode($arrayParametros), $arrayRestUpdate);
        
        $arrayData = json_decode($arrayRespuesta['result'], true);

        if (empty($arrayData))
        {
          throw new \Exception('ERROR ' . "No se pudo consultar la información con el ms planificación", 1);
        }

        $objResponse = new Response(json_encode($arrayData));
        $objResponse->headers->set('Content-type', 'text/json');


    }catch(\Exception $ex)
     {

        error_log( $ex->getMessage());

           
            $serviceUtil->insertError('Telcos+',
            'AdmiHorPlaniComerController.saveCalendarioAction',
            $ex->getMessage(),
             $strUserSession,
             $strIpCreacion);


            $arrayData['status']='ERROR';
            $arrayData['message']=$ex->getMessage();
            $objResponse = new Response(json_encode($arrayData));
            $objResponse->headers->set('Content-type', 'text/json');
     }
    
        return $objResponse;
    }




    /**
     * @Secure(roles="ROLE_482-1")
     * Documentación para el método 'saveCalendarioAction'.
     *
     * Metodo que consumo el ms de planificación para inactivar cierta  información
     *     
     * @version 1.0 Version Inicial
     *
     * @author Carlos Caguana <ccaguana@telconet.ec>
     */
    public function inactivarRegistroAction()
    {



        error_log("consumo");
        $strUrlMsInactivar = $this->container->getParameter('planificacion.comercial.url.inactivarRegistroCalendario');
        $objRespuesta   = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/plain');
        $objPeticion       = $this->get('request');
        $serviceUtil       = $this->get('schema.Util');        
        $intIdRegistroCalendario     = $objPeticion->get('idRegistroCalendario');
        $strUserSession    = $objPeticion->getSession()->get('user');
        $strIpCreacion     = $objPeticion->getClientIp();

        
    try
    {

        error_log("entro try");

        $arrayParametros   = array();
        $arrayParametros['idRegistroCalendario']= $intIdRegistroCalendario;
        $arrayParametros['ipCreacion']          = $strIpCreacion;
        $arrayParametros['usrCreacion']         = $strUserSession;

             
        $serviceTokenCas = $this->get('seguridad.TokenCas');
        $arrayTokenCas = $serviceTokenCas->generarTokenCas();

        if(empty($arrayTokenCas['strToken']))
        {
            throw new \Exception($arrayTokenCas['strMensaje']); 
        }            

        $arrayRestUpdate[CURLOPT_HTTPHEADER]     = array('Content-Type: application/json',
                                                         'tokencas: '.$arrayTokenCas['strToken']);        
        $arrayRestUpdate[CURLOPT_TIMEOUT]        = 900000;
        $serviceRest = $this->get('schema.RestClient');

    
        $arrayRespuesta = $serviceRest->postJSON($strUrlMsInactivar,json_encode($arrayParametros), $arrayRestUpdate);
        
        $arrayData = json_decode($arrayRespuesta['result'], true);

        if (empty($arrayData))
        {
          throw new \Exception('ERROR ' . "No se pudo consultar la información con el ms planificación", 1);
        }

        $objResponse = new Response(json_encode($arrayData));
        $objResponse->headers->set('Content-type', 'text/json');


    }catch(\Exception $ex)
     {

        error_log( $ex->getMessage());

           
            $serviceUtil->insertError('Telcos+',
            'AdmiHorPlaniComerController.inactivarRegistroCalendarioAction',
            $ex->getMessage(),
             $strUserSession,
             $strIpCreacion);


            $arrayData['status']='ERROR';
            $arrayData['message']=$ex->getMessage();
            $objResponse = new Response(json_encode($arrayData));
            $objResponse->headers->set('Content-type', 'text/json');
     }
    
        return $objResponse;

    }




    
}