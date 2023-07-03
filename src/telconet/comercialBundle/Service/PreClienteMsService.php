<?php

namespace telconet\comercialBundle\Service;

use Doctrine\ORM\EntityManager;

use telconet\schemaBundle\Entity\InfoPersona;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRol;
use telconet\schemaBundle\Entity\InfoPersonaReferido;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolHisto;
use telconet\schemaBundle\Entity\InfoPersonaFormaContacto;
use telconet\schemaBundle\Entity\InfoPersonaEmpFormaPago;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolCarac;
use telconet\schemaBundle\Entity\AdmiTipoCuenta;
use telconet\schemaBundle\Entity\AdmiBancoTipoCuenta;
use telconet\schemaBundle\Entity\InfoServicio;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\tecnicoBundle\Service\InfoInterfaceElementoService;

class PreClienteMsService 
{
    private $serviceUtil;
    private $serviceRestClient;
 

    private $strUrlPersonaRecomendacion;
    private $strUrlPersonaTarjetaRecomendada;  
    private $strUrlPersonaProspecto;  
    private $strUrlPersonaCrearProspecto;    
 
    private $strUrlPersonaValidarFormaContacto;    

    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $objContainer) 
    {
        $this->serviceUtil                      = $objContainer->get('schema.Util'); 
        $this->serviceRestClient                = $objContainer->get('schema.RestClient');

        $this->strUrlPersonaRecomendacion       = $objContainer->getParameter('ws_ms_recomendacionPersona_url');
        $this->strUrlPersonaTarjetaRecomendada  = $objContainer->getParameter('ws_ms_PersonaTarjetaRecomendada_url');
        $this->strUrlPersonaProspecto           = $objContainer->getParameter('ws_ms_PersonaProspecto_url');
        $this->strUrlPersonaCrearProspecto      = $objContainer->getParameter('ws_ms_personaCrearProspecto_url');
        $this->strUrlPersonaValidarFormaContacto= $objContainer->getParameter('ws_ms_personaValidarFormaContacto_url'); 

    

    }
     
    /**
    * Método que Mostrar data persona prospecto
    *
    * @author Jefferson Carrillo <jacarrillo@telconet.ec>
    * @version 1.0 06-09-2021
    **/ 
    public function wsPersonaProspecto($arrayParametros)  
    {    
        
        $arrayResultado  = array();
        $strIpMod        = $arrayParametros['strClientIp'];
        $strUserMod      = $arrayParametros['strUsrCreacion'];
 
        try 
        {
            $objOptions         = array(
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER     => array(
                    'Content-Type: application/json',
                    'tokencas: ' . $arrayParametros['token']
                )
            );

            $strJsonData        = json_encode($arrayParametros);

            $strUrl =    $this->strUrlPersonaProspecto; 
            $arrayResponseJson  = $this->serviceRestClient->postJSON( $strUrl , $strJsonData, $objOptions); 
            $strJsonRespuesta   = json_decode($arrayResponseJson['result'], true);

            if (
                isset($strJsonRespuesta['code']) && $strJsonRespuesta['code'] == 0
                && isset($strJsonRespuesta['status'])
                && isset($strJsonRespuesta['message'])
            ) 
            {
                $arrayResponse = array(
                    'strStatus' => 'OK',
                    'strMensaje' => $strJsonRespuesta['message'],
                    'objData' => $strJsonRespuesta['data']
                );
                $arrayResultado = $arrayResponse;
            }
             else 
            {
                $arrayResultado['strStatus']       = "ERROR";  
                
                if (!empty($strJsonRespuesta['message']))
                {   
                    $arrayResultado['strMensaje']  = $strJsonRespuesta['message'];  
                    $this->serviceUtil->insertError(
                        'Telcos+',
                        'PreClienteMsService.wsVerificarRecomendaciones',
                        'Error PreClienteMsService.wsVerificarRecomendaciones:' .  $arrayResultado['strMensaje'],
                        $strUserMod,
                        $strIpMod
                    );                 
                } 
                else 
                {
                    $arrayResultado['strMensaje']  = "No Existe Conectividad con el WS ms-core-com-persona.";
                }
            }
        } catch (\Exception $e) 
        { 
            $arrayResultado['strMensaje']=$e->getMessage();
            $this->serviceUtil->insertError(
                'Telcos+',
                'PreClienteMsService.wsPersonaProspecto',
                'Error PreClienteMsService.wsPersonaProspecto:' . $e->getMessage(),
                $strUserMod,
                $strIpMod
            );
        }
        return $arrayResultado;
    }

     
 
    /**
    * Método que Mostrar data tarjeta recomendada
    *
    * @author Jefferson Carrillo <jacarrillo@telconet.ec>
    * @version 1.0 06-09-2021
    **/ 
    public function wsVerificarRecomendaciones($arrayParametros)  
    { 
        $arrayResultado  = array();
        $strIpMod        = $arrayParametros['strClientIp']||  $arrayParametros['ipCreacion'];
        $strUserMod       = $arrayParametros['strUsrCreacion']||  $arrayParametros['usrCreacion']; 
        
        try 
        {
            $objOptions         = array(
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER     => array(
                    'Content-Type: application/json',
                    'tokencas: ' . $arrayParametros['token']
                )
            );

            $strJsonData        = json_encode($arrayParametros);

            $strUrl             = $this->strUrlPersonaTarjetaRecomendada ; 
            $arrayResponseJson  = $this->serviceRestClient->postJSON( $strUrl , $strJsonData, $objOptions); 
            $strJsonRespuesta   = json_decode($arrayResponseJson['result'], true);

            if (
                isset($strJsonRespuesta['code']) && $strJsonRespuesta['code'] == 0
                && isset($strJsonRespuesta['status'])
                && isset($strJsonRespuesta['message'])
            ) 
            {
                $arrayResponse = array(
                    'strStatus' => 'OK',
                    'strMensaje' => $strJsonRespuesta['message'],
                    'objData' => $strJsonRespuesta['data']
                );
                $arrayResultado = $arrayResponse;
            }
             else 
            {
                $arrayResultado['strStatus']       = "ERROR";  
                
                if (!empty($strJsonRespuesta['message']))
                {   
                    $arrayResultado['strMensaje']  = $strJsonRespuesta['message'];   
                     
                    $this->serviceUtil->insertError(
                        'Telcos+',
                        'PreClienteMsService.wsVerificarRecomendaciones',
                        'Error PreClienteMsService.wsVerificarRecomendaciones:' .  $arrayResultado['strMensaje'],
                        $strUserMod,
                        $strIpMod
                    );

                } 
                else 
                {
                    $arrayResultado['strMensaje']  = "No Existe Conectividad con el WS ms-core-com-persona.";
                }
            }
        }
         catch (\Exception $e) 
        { 
            $arrayResultado['strMensaje']=$e->getMessage();
            $this->serviceUtil->insertError(
                'Telcos+',
                'PreClienteMsService.wsVerificarRecomendaciones',
                'Error PreClienteMsService.wsVerificarRecomendaciones:' . $e->getMessage(),
                $strUserMod,
                $strIpMod
            );
        }
        return $arrayResultado;
    }
 

 
    /**
    * Método para consumir el ms para consumir el ms para crear el prospecto
    *
    * @author Jefferson Carrillo <jacarrillo@telconet.ec>
    * @version 1.0 06-09-2021
    **/ 
    public function wsCrearProspecto( $arrayParametros)  
    {
        $arrayResultado  = array();
        $strIpMod               = $arrayParametros['clientIp'];
        $strUserMod             = $arrayParametros['usrCreacion'];
        $strTokenCas            = $arrayParametros['token'];
        try 
        {
            $objOptions         = array(
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER     => array(
                    'Content-Type: application/json',
                    'tokencas: ' . $strTokenCas
                )
            );
    
 
         
            $strUrl             =  $this->strUrlPersonaCrearProspecto;
            $strJsonData        = json_encode($arrayParametros);   
        
            $arrayResponseJson  = $this->serviceRestClient->postJSON( $strUrl , $strJsonData, $objOptions); 
            $strJsonRespuesta   = json_decode($arrayResponseJson['result'], true);

           
            $arrayResultado = array(
                'strStatus' =>  $strJsonRespuesta['status'],
                'strMensaje' => $strJsonRespuesta['message'],
                'objData' => $strJsonRespuesta['data']
            );
          

            if (empty($arrayResultado['strMensaje']))
            {   
                $arrayResultado['strMensaje']  = "No Existe Conectividad con el WS ms-core-com-persona.";                  
            } 

            
        } 
        catch (\Exception $e) 
        {
            $arrayResultado['strMensaje']=$e->getMessage();

            $this->serviceUtil->insertError(
                'Telcos+',
                'PreClienteMsService.wsCrearProspecto',
                'Error PreClienteMsService.wsCrearProspecto:' . $e->getMessage(),
                $strUserMod,
                $strIpMod
            );
        }
        return $arrayResultado;
    }
 


    /**
     * Método para consumir el ms para validar formas de contacto de cliente
     *
     * @author Jefferson Carrillo <jacarrillo@telconet.ec>
     * @version 1.0 10-08-2022
     **/
    public function wsValidarFormaContacto($arrayParametros)
    {
            $arrayResultado = array();
            $strIpMod = $arrayParametros['clientIp'];
            $strUserMod  = $arrayParametros['usrCreacion'];
            $strTokenCas = $arrayParametros['token'];
            try 
            {
                $objOptions = array(
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json',
                        'tokencas: ' . $strTokenCas
                    )
                );
    
    
    
                $strUrl      = $this->strUrlPersonaValidarFormaContacto;
                $strJsonData = json_encode($arrayParametros);
    
                $arrayResponseJson = $this->serviceRestClient->postJSON($strUrl, $strJsonData, $objOptions);
                $strJsonRespuesta = json_decode($arrayResponseJson['result'], true);
    
    
                $arrayResultado = array(
                    'strStatus' => $strJsonRespuesta['status'],
                    'strMensaje' => $strJsonRespuesta['message'],
                    'objData' => $strJsonRespuesta['data']
                );
    
                if (empty($arrayResultado['strMensaje'])) 
                {
                    $arrayResultado['strMensaje'] = "No existe conectividad con el WS ms-core-com-persona.";
                }
    
    
            }
            catch (\Exception $e) 
            {
                $arrayResultado['strMensaje'] = $e->getMessage();
    
                $this->serviceUtil->insertError(
                    'Telcos+',
                    'PreClienteMsService.wsValidarFormaContacto',
                    'Error PreClienteMsService.wsValidarFormaContacto:' . $e->getMessage(),
                    $strUserMod,
                    $strIpMod
                );
            }
            return $arrayResultado;
        }


}
