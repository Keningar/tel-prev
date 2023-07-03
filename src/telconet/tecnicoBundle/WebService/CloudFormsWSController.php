<?php

namespace telconet\tecnicoBundle\WebService;

use telconet\schemaBundle\DependencyInjection\BaseWSController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of CloudFormsWSController
 *
 * @author arsuarez
 */
class CloudFormsWSController extends BaseWSController
{

    /**
     * Método encargado de procesar las opciones enviadas al WebService de Consumo
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 - 26-07-2018
     * 
     * @param Request $objRequest
     * @return Response
     */
    public function procesarAction(Request $objRequest)
    {
        $arrayDataWs    = json_decode($objRequest->getContent(),true);
        $arrayResponse  = null;
        $objResponse    = new Response();
        $strOpcion      = $arrayDataWs['op'];
        $strToken       = $this->validateGenerateToken($arrayDataWs['token'], $arrayDataWs['source'], $arrayDataWs['user']);
        if(!$strToken)
        {
            return new Response(json_encode(array(
                    'status' => 403,
                    'mensaje' => "token invalido"
                    )
                )
            );
        }
        
        $serviceCloudForms    = $this->get('tecnico.CloudFormsService');
       
        $arrayData =  $arrayDataWs['data'];
        
        if(!empty($strOpcion))
        {
            if($strOpcion == 'guardarConsumoCloudform')
            {
                $arrayResponse          = $serviceCloudForms->guardarConsumoCloudForms($arrayData);
                $arrayResponse['token'] = $strToken;
            }
            else
            {
                $arrayResponse['status']    = $this->status['METODO'];
                $arrayResponse['msj']       = $this->mensaje['METODO'];
            }            
        }
        if(isset($arrayResponse))
        {
            $objResponse = new Response();
            $objResponse->headers->set('Content-Type', 'text/json');
            $objResponse->setContent(json_encode($arrayResponse));
        }
        return $objResponse;
    }
}
