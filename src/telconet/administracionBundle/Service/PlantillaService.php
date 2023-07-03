<?php

namespace telconet\administracionBundle\Service;

/**
 * Clase PlantillaService
 *
 * Clase que maneja funcionales necesarias para el bundle de administración de plantillas
 *
 * @author Edson Franco <efranco@telconet.ec>
 * @version 1.0 10-12-2021
 */    
class PlantillaService 
{

    private $serviceRestClient;
    private $utilService;
    private $strUrlTemplateMs;
    private $strUrlConvertMs;
    private $objContainer;

/**
     * Documentación para el método 'setDependencies'.
     * 
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container 
     * 
     *
     * @author Ivan Romero  <icromero@telconet.ec>
     * @version 1.0 15-12-2021 - Se adiciona los services para el consumo de WS tipo REST
     *                           y se lee del parameter.yml el parámetro ms_template y ms_convert.
     *
     * @since 1.0
     */
    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $objContenedor) 
    {
        $this->utilService                     = $objContenedor->get('schema.Util');
        $this->serviceRestClient               = $objContenedor->get('schema.RestClient');
        $this->strUrlTemplateMs                = $objContenedor->getParameter('ms_core_gen_template_engine_url');
        $this->strUrlConvertMs                = $objContenedor->getParameter('ms_core_gen_convert_docs_url');
        $this->objContainer                    = $objContenedor;
    } 
    /** 
     * Función que consulta plantillas de microservicio ms_template
     * 
     * @author Ivan Romero  <icromero@telconet.ec>
     * @version 1.0 15-12-2021
     * 
     * @param 
     *         array $arrayParametrosContrato
     * @throws Exception
     * @return $arrayResultado
     * 
     */
    public function consultarPlantillasMs($arrayParametrosContrato)
    {
        try
        {
            $objOptions         = array(CURLOPT_SSL_VERIFYPEER => false,
                                        CURLOPT_HTTPHEADER     => array('Content-Type: application/json',
                                                                        'tokencas: ' . $arrayParametrosContrato['token'])
                                       ); 
            
            $arrayResponseJson  = $this->serviceRestClient->getJSON($this->strUrlTemplateMs , $objOptions);
            $strJsonRespuesta   = json_decode($arrayResponseJson['result'],true);
            if(isset($strJsonRespuesta['code']) && $strJsonRespuesta['code']==0 
            && isset($strJsonRespuesta['status'])
            && isset($strJsonRespuesta['message']) )
            {   
                $arrayResponse = array('strStatus' => $strJsonRespuesta['code'],
                                       'strMensaje'=> $strJsonRespuesta['message'],
                                       'objData'=> $strJsonRespuesta['data']);
                $arrayResultado = $arrayResponse;
            }
            else
            {
                $arrayResultado['strStatus']      = "ERROR";
                if(empty($strJsonRespuesta['message']))
                {
                    $arrayResultado['strMensaje']  = "No Existe Conectividad con el WS MS CORE TEMPLATE.";
                }
                else
                {
                    $arrayResultado['strMensaje']  = $strJsonRespuesta['message'];
                }
            }

            return $arrayResultado ;
        }
        catch(\Exception $e)
        {
            $strRespuesta   = "Error al ejecutar el re procesamiento del registro. Favor Notificar a Sistemas".$e->getMessage();
            $arrayResultado = array ('strMensaje'     =>$strRespuesta);
            $this->utilService->insertError('Telcos+',
                                            'PlantillaService.consultarPlantillasMs',
                                            'Error PlantillaService.consultarPlantillasMs:'.$e->getMessage(),
                                            $strUserMod,
                                            $strIpMod); 
            return $arrayResultado;
        }
    }
    
     /** 
     * Función que consulta plantilla de microservicio ms_template
     * 
     * @author Ivan Romero  <icromero@telconet.ec>
     * @version 1.0 15-12-2021
     * 
     * @param 
     *         array $arrayParametrosContrato
     * @throws Exception
     * @return $arrayResultado
     * 
     */
    public function consultarPlantillaMs($arrayParametrosContrato)
    {
        try
        {
            $objOptions         = array(CURLOPT_SSL_VERIFYPEER => false,
                                        CURLOPT_HTTPHEADER     => array('Content-Type: application/json',
                                                                        'tokencas: ' . $arrayParametrosContrato['token'])
                                       ); 
            
            $arrayResponseJson  = $this->serviceRestClient->getJSON($this->strUrlTemplateMs.'/'.
                                $arrayParametrosContrato['codigoPlantilla'] , $objOptions);
            $strJsonRespuesta   = json_decode($arrayResponseJson['result'],true);
            if(isset($strJsonRespuesta['code']) && $strJsonRespuesta['code']==0 
            && isset($strJsonRespuesta['status'])
            && isset($strJsonRespuesta['message']) )
            {   
                $arrayResponse = array('strStatus' => $strJsonRespuesta['code'],
                                       'strMensaje'=> $strJsonRespuesta['message'],
                                       'objData'=> $strJsonRespuesta['data']);
                $arrayResultado = $arrayResponse;
            }
            else
            {
                $arrayResultado['strStatus']      = "ERROR";
                if(empty($strJsonRespuesta['message']))
                {
                    $arrayResultado['strMensaje']  = "No Existe Conectividad con el WS MS CORE TEMPLATE.";
                }
                else
                {
                    $arrayResultado['strMensaje']  = $strJsonRespuesta['message'];
                }
            }

            return $arrayResultado ;
        }
        catch(\Exception $e)
        {
            $strRespuesta   = "Error al ejecutar el re procesamiento del registro. Favor Notificar a Sistemas".$e->getMessage();
            $arrayResultado = array ('strMensaje'     =>$strRespuesta);
            $this->utilService->insertError('Telcos+',
                                            'PlantillaService.consultarPlantillasMs',
                                            'Error PlantillaService.consultarPlantillasMs:'.$e->getMessage(),
                                            $strUserMod,
                                            $strIpMod); 
            return $arrayResultado;
        }
    }

    /** 
     * Función que crear plantillas de microservicio ms_template
     * 
     * @author Ivan Romero  <icromero@telconet.ec>
     * @version 1.0 15-12-2021
     * 
     * @param 
     *         array $arrayParametrosContrato
     * @throws Exception
     * @return $arrayResultado
     * 
     */
    public function crearPlantillaMs($arrayParametrosContrato)
    {
        try
        {
            $objOptions         = array(CURLOPT_SSL_VERIFYPEER => false,
                                        CURLOPT_HTTPHEADER     => array('Content-Type: application/json',
                                                                        'tokencas: ' . $arrayParametrosContrato['token'])
                                       ); 
            $strJsonData        = json_encode($arrayParametrosContrato);
            $arrayResponseJson  = $this->serviceRestClient->postJSON($this->strUrlTemplateMs, $strJsonData , $objOptions);
            $strJsonRespuesta   = json_decode($arrayResponseJson['result'],true);
            if(isset($strJsonRespuesta['code']) && $strJsonRespuesta['code']==0 
            && isset($strJsonRespuesta['status'])
            && isset($strJsonRespuesta['message']) )
            {   
                $arrayResponse = array('strStatus' => $strJsonRespuesta['code'],
                                       'strMensaje'=> $strJsonRespuesta['message'],
                                       'objData'=> $strJsonRespuesta['data']);
                $arrayResultado = $arrayResponse;
            }
            else
            {
                $arrayResultado['strStatus']      = "ERROR";
                if(empty($strJsonRespuesta['message']))
                {
                    $arrayResultado['strMensaje']  = "No Existe Conectividad con el WS MS CORE TEMPLATE.";
                }
                else
                {
                    $arrayResultado['strMensaje']  = $strJsonRespuesta['message'];
                }
            }

            return $arrayResultado ;
        }
        catch(\Exception $e)
        {
            $strRespuesta   = "Error al ejecutar el re procesamiento del registro. Favor Notificar a Sistemas".$e->getMessage();
            $arrayResultado = array ('strMensaje'     =>$strRespuesta);
            $this->utilService->insertError('Telcos+',
                                            'PlantillaService.consultarPlantillasMs',
                                            'Error PlantillaService.consultarPlantillasMs:'.$e->getMessage(),
                                            $strUserMod,
                                            $strIpMod); 
            return $arrayResultado;
        }
    }


    /** 
     * Función que editar plantillas de microservicio ms_template
     * 
     * @author Ivan Romero  <icromero@telconet.ec>
     * @version 1.0 15-12-2021
     * 
     * @param 
     *         array $arrayParametrosContrato
     * @throws Exception
     * @return $arrayResultado
     * 
     */
    public function editarPlantillaMs($arrayParametrosContrato)
    {
        try
        {
            $objOptions         = array(CURLOPT_SSL_VERIFYPEER => false,
                                        CURLOPT_HTTPHEADER     => array('Content-Type: application/json',
                                                                        'tokencas: ' . $arrayParametrosContrato['token'])
                                       ); 
            $strJsonData        = json_encode($arrayParametrosContrato);
            $arrayResponseJson  = $this->serviceRestClient->putJSON($this->strUrlTemplateMs, $strJsonData , $objOptions);
            $strJsonRespuesta   = json_decode($arrayResponseJson['result'],true);
            if(isset($strJsonRespuesta['code']) && $strJsonRespuesta['code']==0 
            && isset($strJsonRespuesta['status'])
            && isset($strJsonRespuesta['message']) )
            {   
                $arrayResponse = array('strStatus' => $strJsonRespuesta['code'],
                                       'strMensaje'=> $strJsonRespuesta['message'],
                                       'objData'=> $strJsonRespuesta['data']);
                $arrayResultado = $arrayResponse;
            }
            else
            {
                $arrayResultado['strStatus']      = "ERROR";
                if(empty($strJsonRespuesta['message']))
                {
                    $arrayResultado['strMensaje']  = "No Existe Conectividad con el WS MS CORE TEMPLATE.";
                }
                else
                {
                    $arrayResultado['strMensaje']  = $strJsonRespuesta['message'];
                }
            }

            return $arrayResultado ;
        }
        catch(\Exception $e)
        {
            $strRespuesta   = "Error al ejecutar el re procesamiento del registro. Favor Notificar a Sistemas".$e->getMessage();
            $arrayResultado = array ('strMensaje'     =>$strRespuesta);
            $this->utilService->insertError('Telcos+',
                                            'PlantillaService.consultarPlantillasMs',
                                            'Error PlantillaService.consultarPlantillasMs:'.$e->getMessage(),
                                            $strUserMod,
                                            $strIpMod); 
            return $arrayResultado;
        }
    }

    /** 
     * Función que elimunar plantillas de microservicio ms_template
     * 
     * @author Ivan Romero  <icromero@telconet.ec>
     * @version 1.0 15-12-2021
     * 
     * @param 
     *         array $arrayParametrosContrato
     * @throws Exception
     * @return $arrayResultado
     * 
     */
    public function eliminarPlantillaMs($arrayParametrosContrato)
    {
        try
        {
            $objOptions         = array(CURLOPT_SSL_VERIFYPEER => false,
                                        CURLOPT_HTTPHEADER     => array('Content-Type: application/json',
                                                                        'tokencas: ' . $arrayParametrosContrato['token'])
                                       ); 
            $strJsonData        = json_encode($arrayParametrosContrato);
            $objParams = array();
            $objParams = array('usuarioCreacion' => $arrayParametrosContrato['usuarioCreacion'],
            'descripcion' => $arrayParametrosContrato['descripcion']);
            
            $arrayResponseJson  = $this->serviceRestClient->deleteJSON($this->strUrlTemplateMs.'/'.
                                  $arrayParametrosContrato['codigoPlantilla'].'?'.http_build_query($objParams), $objOptions);
            $strJsonRespuesta   = json_decode($arrayResponseJson['result'],true);
            if(isset($strJsonRespuesta['code']) && $strJsonRespuesta['code']==0 
            && isset($strJsonRespuesta['status'])
            && isset($strJsonRespuesta['message']) )
            {   
                $arrayResponse = array('strStatus' => $strJsonRespuesta['code'],
                                       'strMensaje'=> $strJsonRespuesta['message'],
                                       'objData'=> $strJsonRespuesta['data']);
                $arrayResultado = $arrayResponse;
            }
            else
            {
                $arrayResultado['strStatus']      = "ERROR";
                if(empty($strJsonRespuesta['message']))
                {
                    $arrayResultado['strMensaje']  = "No Existe Conectividad con el WS MS CORE TEMPLATE.";
                }
                else
                {
                    $arrayResultado['strMensaje']  = $strJsonRespuesta['message'];
                }
            }

            return $arrayResultado ;
        }
        catch(\Exception $e)
        {
            $strRespuesta   = "Error al ejecutar el re procesamiento del registro. Favor Notificar a Sistemas".$e->getMessage();
            $arrayResultado = array ('strMensaje'     =>$strRespuesta);
            $this->utilService->insertError('Telcos+',
                                            'PlantillaService.consultarPlantillasMs',
                                            'Error PlantillaService.consultarPlantillasMs:'.$e->getMessage(),
                                            $strUserMod,
                                            $strIpMod); 
            return $arrayResultado;
        }
    }

    /** 
     * Función que usar plantilla de microservicio ms_template
     * 
     * @author Ivan Romero  <icromero@telconet.ec>
     * @version 1.0 15-12-2021
     * 
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 1.1 06-10-2022 - Se agrega log de errores para identificar si el login tuvo problemas con el consumo del MS.
     * 
     * @param 
     *         array $arrayParametrosContrato
     * @throws Exception
     * @return $arrayResultado
     * 
     */
    public function usarPlantillaMs($arrayParametrosContrato)
    {
        $strLogin   = $arrayParametrosContrato["login"];
        $strIp      = !empty($arrayParametrosContrato['strIpCliente']) ? $arrayParametrosContrato['strIpCliente'] : "127.0.0.1";
        try
        {
            $objOptions         = array(CURLOPT_SSL_VERIFYPEER => false,
                                        CURLOPT_HTTPHEADER     => array('Content-Type: application/json',
                                                                        'tokencas: ' . $arrayParametrosContrato['token'])
                                       ); 

            $strJsonData        = json_encode($arrayParametrosContrato);
            $arrayResponseJson  = $this->serviceRestClient->postJSON($this->strUrlTemplateMs.'/individual', $strJsonData , $objOptions);
            $strJsonRespuesta   = json_decode($arrayResponseJson['result'],true);
            
            if(isset($strJsonRespuesta['code']) && $strJsonRespuesta['code']==0 
            && isset($strJsonRespuesta['status'])
            && isset($strJsonRespuesta['message']) )
            {   
                $arrayResponse = array('strStatus' => $strJsonRespuesta['code'],
                                       'strMensaje'=> $strJsonRespuesta['message'],
                                       'objData'=> $strJsonRespuesta['data']);
                $arrayResultado = $arrayResponse;
            }
            else
            {
                $arrayResultado['strStatus']      = "ERROR";
                if(empty($strJsonRespuesta['message']))
                {
                    $arrayResultado['strMensaje']  = "No Existe Conectividad con el WS MS CORE TEMPLATE.";
                    if (!empty($strLogin))
                    {
                        $this->utilService->insertError('Telcos+',
                                                        'PlantillaService.usarPlantillaMs',
                                                        'ERROR: en el login: '.$strLogin.
                                                        ' - No Existe Conectividad con el WS MS CORE TEMPLATE.',
                                                        'usarPlantillaMs',
                                                        $strIp); 
                    }
                }
                else
                {
                    $arrayResultado['strMensaje']  = $strJsonRespuesta['message'];
                    if (!empty($strLogin))
                    {
                        $this->utilService->insertError('Telcos+',
                                                        'PlantillaService.usarPlantillaMs',
                                                        'ERROR: en el login: '.$strLogin.
                                                        ' - '.$strJsonRespuesta['message'],
                                                        'usarPlantillaMs',
                                                        $strIp);
                    } 
                }
            }

            return $arrayResultado ;
        }
        catch(\Exception $e)
        {
            $strRespuesta   = "Error al ejecutar el re procesamiento del registro. Favor Notificar a Sistemas".$e->getMessage();
            $arrayResultado = array ('strMensaje'     =>$strRespuesta);
            if (!empty($strLogin))
            {
                $this->utilService->insertError('Telcos+',
                                                'PlantillaService.usarPlantillaMs',
                                                'ERROR: en el login: '.$strLogin. ' - ' .$e->getMessage(),
                                                'usarPlantillaMs',
                                                $strIp); 
            }
            return $arrayResultado;
        }
    }
    
    /** 
     * Función que convertir documento de microservicio ms_convert
     * 
     * @author Ivan Romero  <icromero@telconet.ec>
     * @version 1.0 15-12-2021
     * 
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 1.1 06-10-2022 - Se agrega log de errores para identificar si el login tuvo problemas con el consumo del MS.
     * 
     * @param 
     *         array $arrayParametrosContrato
     * @throws Exception
     * @return $arrayResultado
     * 
     */
    public function convertDocsMs($arrayParametrosContrato)
    {
        $strLogin   = $arrayParametrosContrato["login"];
        $strIp      = !empty($arrayParametrosContrato['strIpCliente']) ? $arrayParametrosContrato['strIpCliente'] : "127.0.0.1";
        try
        {
            $objOptions         = array(CURLOPT_SSL_VERIFYPEER => false,
                                        CURLOPT_HTTPHEADER     => array('Content-Type: application/json',
                                                                        'tokencas: ' . $arrayParametrosContrato['token'])
                                       ); 

            $strJsonData        = json_encode($arrayParametrosContrato);
            $arrayResponseJson  = $this->serviceRestClient->postJSON($this->strUrlConvertMs, $strJsonData , $objOptions);
            $strJsonRespuesta   = json_decode($arrayResponseJson['result'],true);
            
            if(isset($strJsonRespuesta['code']) && $strJsonRespuesta['code']==0 
            && isset($strJsonRespuesta['status'])
            && isset($strJsonRespuesta['message']) )
            {   
                $arrayResponse = array('strStatus' => $strJsonRespuesta['code'],
                                       'strMensaje'=> $strJsonRespuesta['message'],
                                       'objData'=> $strJsonRespuesta['data']);
                $arrayResultado = $arrayResponse;
            }
            else
            {
                $arrayResultado['strStatus']      = "ERROR";
                if(empty($strJsonRespuesta['message']))
                {
                    $arrayResultado['strMensaje']  = "No Existe Conectividad con el WS MS CORE TEMPLATE.";
                    if (!empty($strLogin))
                    {
                        $this->utilService->insertError('Telcos+',
                                                        'PlantillaService.convertDocsMs',
                                                        'ERROR: en el login: '.$strLogin.
                                                        ' - No Existe Conectividad con el WS MS CORE TEMPLATE.',
                                                        'convertDocsMs',
                                                        $strIp); 
                    }
                }
                else
                {
                    $arrayResultado['strMensaje']  = $strJsonRespuesta['message'];
                    if (!empty($strLogin))
                    {
                        $this->utilService->insertError('Telcos+',
                                                        'PlantillaService.convertDocsMs',
                                                        'ERROR: en el login: '.$strLogin.
                                                        ' - '.$strJsonRespuesta['message'],
                                                        'convertDocsMs',
                                                        $strIp); 
                    }
                }
            }

            return $arrayResultado ;
        }
        catch(\Exception $e)
        {
            $strRespuesta   = "Error al ejecutar el re procesamiento del registro. Favor Notificar a Sistemas".$e->getMessage();
            $arrayResultado = array ('strMensaje'     =>$strRespuesta);
            if (!empty($strLogin))
            {
                $this->utilService->insertError('Telcos+',
                                                'PlantillaService.convertDocsMs',
                                                'ERROR: en el login: '.$strLogin. ' - ' .$e->getMessage(),
                                                'convertDocsMs',
                                                $strIp); 
            }
            return $arrayResultado;
        }
    }
}
