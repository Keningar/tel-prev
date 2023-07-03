<?php

namespace telconet\comercialBundle\WebService;

use telconet\schemaBundle\DependencyInjection\BaseWSController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Finder\Finder;
use telconet\schemaBundle\Service\UtilService;
use Symfony\Component\HttpFoundation\File\File;

use telconet\comercialBundle\Service\ComercialCrmCmService;
use telconet\comercialBundle\Service\ComercialCrmService;
use telconet\comercialBundle\Service\OrquestadorService;

class ComercialWSController extends BaseWSController
{
    public function procesarAction(Request $objRequest)
    {
        $arrayData               = json_decode($objRequest->getContent(),true);
        $strToken                = "";
        $strOrigen               = "";
        $objResponse             = new Response();
        $strOp                   = $arrayData['op'];
        $serviceComercialCrmCm   = $this->get('comercial.ComercialCrmCm');
        $serviceComercialCrm     = $this->get('comercial.ComercialCrm');
        $serviceComercialOrq     = $this->get('comercial.Orquestador');

        $objServiceGestionInsp = $this->get('planificacion.GestionarInspeccion');
        $objServiceCoordinInsp = $this->get('planificacion.CoordinarInspeccion');

        try
        {
            $arrayDataOrq = $arrayData['nativeValueParameterList'];
            if(!empty($arrayDataOrq))
            {
                $arrayColum = $this->arrayColumManual($arrayDataOrq, 'parameterName');
                $intKey = array_search('origen', $arrayColum);
                $strOrigen = $arrayData['nativeValueParameterList'][$intKey]['nativeTypeValue'];
            }
            
            if($strOrigen !='orquestador')
            {
                if($arrayData['source'])
                {

                    $strToken = $this->validateGenerateToken($arrayData['token'], $arrayData['source'], $arrayData['user']);
                    if(!$strToken)
                    {
                        return new Response(json_encode(array(
                                'status' => 403,
                                'message' => "token invalido"
                                )
                            )
                        );
                    }
                }
                else
                {
                   return new Response(json_encode(array(
                                'status' => 403,
                                'message' => "token invalido"
                                )
                            )
                        ); 
                }
            }
            else
            {
                $strOp                   = $arrayData['processName'];
            }
            
            if($strOp)
            {
                switch($strOp)
                {
                    case 'flujoServicioCm':
                        $arrayData['data']['aplication'] = $arrayData['source']['name'];
                        $arrayResponse                   = $serviceComercialCrmCm->flujoServicioCm($arrayData['data']);
                        break;
                    case 'historialServicioCm':
                        $arrayData['data']['aplication'] = $arrayData['source']['name'];
                        $arrayResponse                   = $serviceComercialCrmCm->historialServicioCm($arrayData['data']);
                        break;
                    case 'CREAR_SERVICIO':
                        $arrayResponse                   = $serviceComercialOrq->crearServicio($arrayData);
                        break;
                    case 'ACTIVACION':
                        $arrayResponse                   = $serviceComercialOrq->putCrearTareaOrq($arrayData);
                        break;
                    case 'CERRAR_INSTALACION':
                        $arrayResponse                   = $serviceComercialOrq->putCrearSeguimientoOrq($arrayData);
                        break;
                    case 'crearSolicitudInspeccion':
                            $arrayData['data']['aplication'] = $arrayData['source']['name'];
                            $arrayResponse                   = $objServiceGestionInsp->crearSolicitudInspeccion($arrayData['data']);
                            break;
                    case 'finalizarSolicitudInspeccion':
                        $arrayData['data']['aplication'] = $arrayData['source']['name'];
                        $arrayResponse                   = $objServiceCoordinInsp->finalizarSolicitudInspeccion($arrayData['data']);
                        break;
                    default:
                        $arrayResponse['status']  = $this->status['METODO'];
                        $arrayResponse['message'] = $this->mensaje['METODO'];
                }
            }
            $arrayResponseFinal = null;
            if(isset($arrayResponse))
            {  
                    $arrayResponseFinal = $arrayResponse;
                    $arrayResponseFinal['token'] = $strToken;
                    $objResponse = new Response();
                    $objResponse->headers->set('Content-Type', 'text/json');
                    $objResponse->setContent(json_encode($arrayResponseFinal));
            }
            return $objResponse;
        }
        catch(\Exception $ex)
        {
            $strMensaje ="Falló al procesar Action.". $ex->getMessage();
            $this->serviceUtil->insertError('TELCOS+',
                                            'ComercialCrmCm.procesarAction',
                                            $strMensaje,
                                            'Telcos+',
                                            '127.0.0.1');
        }
    }
    
    public function arrayColumManual($arrayDatos, $strColumn)
    {
        $arrayNew = array();
        foreach ($arrayDatos as $row) 
        {
            $arrayNew[] = $row[$strColumn];
        }
        return $arrayNew;
    }
}
