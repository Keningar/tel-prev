<?php
namespace telconet\comercialBundle\Service;
use telconet\schemaBundle\Entity\AdmiGrupoPromocion;
use Symfony\Component\HttpFoundation\Response;
class ConsumoKonibitService 
{ 
    private $emcom;
    private $serviceUtil;
    private $emGeneral;
    private $serviceRestClient;
    private $strUrlConvertMs;
    
    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $objContainer)
    {
        $this->emcom              = $objContainer->get('doctrine.orm.telconet_entity_manager');
        $this->emGeneral          = $objContainer->get('doctrine.orm.telconet_general_entity_manager');
        $this->serviceUtil        = $objContainer->get('schema.Util');
        $this->serviceRestClient  = $objContainer->get('schema.RestClient');
        $this->strUrlConvertMs    = $objContainer->getParameter('ms_comp_api_proveedor_mgnt_url');
    }
    
     /**
     * envioAKonibit
     * 
     * Función que consulta el microservicio Konibit
     *         
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 1.0 04-04-2019           
     * @param array $arrayParametros[]                  
     *              'strToken'         => token generado 
     *              'strUser'          => Usuario responsable
     *              'strIp'            => Ip respondable                
     *              'arrayPropiedades' => json para consumo de MS                      
     * @return $strRespuesta
     */
    public function envioAKonibit($arrayParametros)
    {   
        try
        {

            $objOptions         = array(CURLOPT_SSL_VERIFYPEER => false,
                                        CURLOPT_HTTPHEADER     => array('Content-Type: application/json',
                                                                        'tokencas: ' . $arrayParametros['strToken'])
                                       ); 

            $strJsonData        = json_encode($arrayParametros['arrayPropiedades']);
            error_log("REQUEST KONIBIT---->". $strJsonData);
            $arrayResponseJson  = $this->serviceRestClient->postJSON($this->strUrlConvertMs, $strJsonData , $objOptions);
            $strJsonRespuesta   = json_decode($arrayResponseJson['result'],true);
            if(isset($strJsonRespuesta['code']) && $strJsonRespuesta['code']==0 
            && isset($strJsonRespuesta['status'])
            && isset($strJsonRespuesta['message']) )
            {   
                $arrayResponse  = array('strResultado' => "Ok",
                                        'strMensaje'   => $strJsonRespuesta['message']);
                $arrayResultado = $arrayResponse;
            }
            else
            {
                $arrayResultado['strResultado']       = "ERROR";
                if(empty($strJsonRespuesta['message']))
                {
                    $arrayResultado['strMensaje']  = "No Existe Conectividad con el MS.";
                }
                else
                {
                    $arrayResultado['strMensaje']  = $strJsonRespuesta['message'];
                }
                $this->serviceUtil->insertError('Telcos+',
                                                'CambioRazonSocialKonibitService.envioAKonibit',
                                                'Error CambioRazonSocialKonibitService.envioAKonibit: '.$arrayResultado['strMensaje'],
                                                $arrayParametros['strUser'],
                                                $arrayParametros['strIp']); 
            }
            error_log("RESPONSE KONIBIT---->". json_encode($arrayResultado));
            return $arrayResultado ;
        }
        catch(\Exception $e)
        {
            $strRespuesta   = "Error al consultar el MS. Favor Notificar a Sistemas".$e->getMessage();
            $arrayResultado = array ('strMensaje'     =>$strRespuesta);
            $this->serviceUtil->insertError('Telcos+',
                                            'CambioRazonSocialKonibitService.envioAKonibit',
                                            'Error CambioRazonSocialKonibitService.envioAKonibit: '.$e->getMessage(),
                                            $arrayParametros['strUser'],
                                            $arrayParametros['strIp']); 
            return $arrayResultado;
        }
    }      

}
