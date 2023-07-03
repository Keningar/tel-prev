<?php

namespace telconet\financieroBundle\WebService;

use telconet\schemaBundle\DependencyInjection\BaseWSController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Documentación para la clase 'FinancieroWSController'.
 *
 * Controlador que maneja todos las peticiones restful del modulo financiero 
 *
 * @author  Kenneth Jimenez <kjimenez@telconet.ec>
 * @since   1.0
 * @version 1.0 01-10-2014
 */
class FinancieroWSController extends BaseWSController
{
    private $sha256MD   = "7abcf6dac49247ef3111e13199b86b909f5ee306b0599c64c5b76b20ca3972a2";
    private $sha256TTCO = "56ce76acc86b70ce2fb6000638dd022ab4d65eb4de524c81f3226c3d65694b7b";
    private $sha256TN   = "57adf80633715e9a5a8353052012b0147136c304989a46f75aba8527544f631a";
    
    /**
    * Documentación para el método 'consultarComprobantesAction'.
    *
    * Método que consulta los comprobantes electrónicos.
    *
    * @param Request $request
    * @return Response $response.
    *
    * @author  Kenneth Jimenez <kjimenez@telconet.ec>
    * @since   1.0
    * @version 1.0 01-10-2014
    * @author  Alexander Samaniego <awsamaniego@telconet.ec>
    * @since   1.1
    * @version 1.0 11-09-2015
    */
    public function consultarComprobantesAction(Request $request)
    {
        try
        {
            //declaracion de variables
            $arrayComprobantes = array();
            $emFinanciero      = $this->getDoctrine()->getManager('telconet_financiero');
            $serviceInfoCompElectronico     = $this->get('financiero.InfoCompElectronico');
            $csrfProvider      = $this->get('form.csrf_provider');
            $em                = $this->getDoctrine()->getManager();
            $data              = json_decode($request->getContent(), true);
            $data['empresa']   = $this->retornaEmpresaId($data['empresa']);
            $objEmpresa        = $em->getRepository('schemaBundle:InfoEmpresaGrupo')->find($data['empresa']);
            $intention         = "consultar-comprobantes-" . $objEmpresa->getPrefijo();
            
            //validacion de token
            if($csrfProvider->isCsrfTokenValid2($intention, $data['token']))
            {
                
                $entityPersonaEmpresaRol = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                              ->findByIdentificacionTipoRolEmpresa($data['identificacion'], ["Cliente"], $data['empresa']);
                //se valida identificacion sea un cliente
                if($entityPersonaEmpresaRol)
                {
                    //Inicializacion de variables
                    $parametros  = array();
                    $idPuntos    = array();
                    $fechaInicio = null;
                    $fechaFin    = null;
                    
                    //mapeo de estados de Facturas
                    $arrayEstadosFact = array();
                    $arrayEstadosFact['Activo']    = "Pendiente de Pago";
                    $arrayEstadosFact['Cerrado']   = "Pagada";
                    $arrayEstadosFact['Rechazado'] = "Rechazado";
                    
                    //mapeo de estados de Notas de Credito
                    $arrayEstadosNc = array();
                    $arrayEstadosNc['Activo']    = "Aplicada";
                    $arrayEstadosNc['Cerrado']   = "Aplicada";
                    $arrayEstadosNc['Anulado']   = "Anulada";
                    $arrayEstadosNc['Rechazado'] = "Rechazada";
                    
                    if($data['fechaInicio'])
                    {
                        $fechaInicio = date("Y/m/d", strtotime($data['fechaInicio']));
                    }
                    if($data['fechaFin'])
                    {
                        $fechaFin = date("Y/m/d", strtotime($data['fechaFin']));
                    }
                    
                    //se obtiene todos los puntos del cliente
                    $arrayPuntos = $em->getRepository('schemaBundle:InfoPunto')->findByPersonaEmpresaRolId($entityPersonaEmpresaRol->getId());
                    
                    foreach($arrayPuntos as $punto)
                    {
                        $idPuntos[] = $punto->getId();
                    }
                    
                    //Inicializacion de parametros para consultar comprobantes
                    $parametros["codigosTipoDocumento"] = array("FACP","NC","FAC");
                    $parametros["estados"]              = array("Activo","Cerrado");
                    $parametros["idEmpresa"]            = $data['empresa'];
                    $parametros["feDesde"]              = $fechaInicio;
                    $parametros["feHasta"]              = $fechaFin;
                    $parametros["puntos"]               = $idPuntos;
                    $parametros["idOficina"]            = "";
                    
                    //consulta de comprobantes
                    $comprobantesElectronicos = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                             ->findComprobantesElectronicos($parametros);
                    
                    //Si existen comprobantes
                    if(count($comprobantesElectronicos)>0){
                        
                        foreach($comprobantesElectronicos as $comprobante):
                            
                            //obtengo xml y pdf del comprobante electrónico
                            $pdf = null;    
                            $xml = null;

                            $arrayComprobantesElectronicos = $serviceInfoCompElectronico->getCompElectronicosPdfXml($comprobante['idDocumento']);
                            if($arrayComprobantesElectronicos)
                            {
                                //se valida que si txt es diferente de vacio, es porque hubo un error
                                if($arrayComprobantesElectronicos['txt']=="")
                                {
                                    if($arrayComprobantesElectronicos['pdf'])
                                        $pdf = base64_encode($arrayComprobantesElectronicos['pdf']);
                                    if($arrayComprobantesElectronicos['xml'])
                                        $xml = base64_encode($arrayComprobantesElectronicos['xml']);    
                                }
                                else
                                {
                                    error_log($arrayComprobantesElectronicos['txt']);
                                }
                            }
                            
                            //Equivalencias de Estados
                            if(strtolower($comprobante['tipoDocumento'])==strtolower("Factura"))
                            {
                                $arrayEstados = $arrayEstadosFact;
                            }
                            else
                            {
                                $arrayEstados = $arrayEstadosNc;
                            }
                            //Se agrega comprobante electrónico al arreglo de comprobantes
                            $arrayComprobantes[] = array(
                                                        'tipo'             => $comprobante["tipoDocumento"],
                                                        'numero'           => $comprobante["numeroFacturaSri"],
                                                        'pdf'              => $pdf,
                                                        'xml'              => $xml,
                                                        'fechaEmision'     => strval(date_format($comprobante["feEmision"], "d/m/Y")),
                                                        'subtotal'         => number_format(round($comprobante["subtotal"], 2), 2),
                                                        'iva'              => number_format(round($comprobante["iva"], 2), 2),
                                                        'descuentos'       => number_format(round($comprobante["descuento"], 2), 2),
                                                        'total'            => number_format(round($comprobante["total"], 2), 2),
                                                        'estado'           => $arrayEstados[$comprobante['estado']],
                                                        'facturasDetalles' => array()
                                                        );
                        

                        endforeach;
                        
                        //se retorna respuesta con el arreglo de los comprobantes
                        return new Response(json_encode(array('comprobantes' => $arrayComprobantes,
                                                              'status'       => 200,
                                                              'mensaje'      => "OK"
                                                              )
                                                       )
                                           );
                    }//if(count($comprobantesElectronicos)>0){
                    else
                    {
                        return new Response(json_encode(array('comprobantes' => $arrayComprobantes,
                                                              'status'       => 201,
                                                              'mensaje'      => "No existen Comprobantes para mostrar."
                                                              )
                                                       )
                                           );
                    }
                    
                }// if($entityPersonaEmpresaRol)
                else
                {
                    return new Response(json_encode(array('comprobantes' => $arrayComprobantes,
                                                          'status'       => 404,
                                                          'mensaje'      => "Identificación ingresada no pertenece a un cliente."
                                                         )
                                                   )
                                       );
                }
            }//if($csrfProvider->isCsrfTokenValid($intention, $data['token']))
            else
            {
                return new Response(json_encode(array('comprobantes' => $arrayComprobantes,
                                                      'status'       => 403,
                                                      'mensaje'      => "No permitido para consultar."
                                                     )
                                               )
                                   );
            }  
        }
        catch(Exception $e)
        {
            return new Response(json_encode(array('comprobantes' => $arrayComprobantes,
                                                  'status'       => 500,
                                                  'mensaje'      => $e->getMessage()
                                                 )
                                           )
                               );
        }
    }
    
    /**
    * Documentación para el método 'retornaEmpresaId'.
    *
    * Método que retorna el id de empresa dado un sha256 del prefijo
    *
    * @param mixed $cadena
    * @return mixed $intEmpresaId.
    *
    * @author  Kenneth Jimenez <kjimenez@telconet.ec>
    * @since   1.0
    * @version 1.0 01-10-2014
    */
    public function retornaEmpresaId($cadena)
    {
        $intEmpresaId = null;
        if($this->sha256MD == $cadena)
        {
            $intEmpresaId = "18";
        }
        if($this->sha256TTCO == $cadena)
        {
            $intEmpresaId = "09";
        }
        if($this->sha256TN == $cadena)
        {
            $intEmpresaId = "10";
        }

        return $intEmpresaId;
    }
  
    
    /**
    * Documentación para el método 'consultarSaldoClienteAction'.
    *
    * Método que consulta el saldo de un cliente.
    *
    * @param Request $request
    * @return JsonResponse $objResponse.
    *
    * @author  Andrés Montero <amontero@telconet.ec>
    * @since   1.0
    * @version 1.0 18-10-2016
    */
    public function consultarSaldoClienteAction(Request $request)
    {
        
        //declaracion de variables
        $servicePagoLinea     = $this->get('financiero.InfoPagoLinea');
        $emFinanciero         = $this->getDoctrine()->getManager('telconet_financiero');
        $emComercial          = $this->getDoctrine()->getManager();
        $objCsrfProvider      = $this->get('form.csrf_provider');
        $arrayData            = json_decode($request->getContent(), true);
        $arrayData['empresa'] = $this->retornaEmpresaId($arrayData['empresa']);
        $strIdentificacion    = $arrayData['identificacion'];
        $objEmpresa           = $emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')->find($arrayData['empresa']);
        $objResponse          = new JsonResponse();
        $strIntention         = "consultar-saldo-cliente-" . $objEmpresa->getPrefijo();

        try
        {            
            //validacion de token
            if($objCsrfProvider->isCsrfTokenValid2($strIntention, $arrayData['token']))
            {
                $entityPersonaEmpresaRol = $emFinanciero->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                        ->findByIdentificacionTipoRolEmpresa($strIdentificacion, ["Cliente"], $objEmpresa->getId());
                //Verifica que el cliente tenga contrato, si no lo tiene termina el metodo con un response
                if (is_object($entityPersonaEmpresaRol))
                {    
                    $arraySaldoCliente = $servicePagoLinea->obtenerConsultaSaldoClientePorIdentificacion($objEmpresa->getId(),$strIdentificacion);
                    
                    if (count($arraySaldoCliente)>0)
                    {    
                        $objResponse->setContent(json_encode(array('saldo'   => round($arraySaldoCliente['saldo'], 2),
                                                                   'status'  => 200,
                                                                   'mensaje' => "OK"
                                                                  )
                                                            )
                                                );
                    }
                    else
                    {
                        $objResponse->setContent(json_encode(array('saldo'   => 0,
                                                                   'status'  => 402,
                                                                   'mensaje' => "No se encontro saldo de cliente."
                                                                  )
                                                            )
                                                );  
                    }    
                }
                else
                {
                    $objResponse->setContent(json_encode(array('saldo'   => 0,
                                                               'status'  => 404,
                                                               'mensaje' => "Identificación ingresada no pertenece a un cliente."
                                                              )
                                                        )
                                            );
                }
            }  
            else
            {
                $objResponse->setContent(json_encode(array('saldo'   => 0,
                                                           'status'  => 403,
                                                           'mensaje' => "No permitido para consultar."
                                                          )
                                                    )
                                        );
            }
        }
        catch(\Exception $e)
        {
            $objResponse->setContent(json_encode(array('saldo'   => 0,
                                                       'status'  => 500,
                                                       'mensaje' => "Ocurrio un error por favor contactarse con Sistemas"
                                                      )
                                                )
                                    );
            error_log("consultarSaldoClienteAction => Error:" . $e->getMessage());
        }
        return $objResponse;
    }
    
    /**
     * Función que sirve para procesar las opciones que vienen mediante petición desde 'RDA' 
     *
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 1.0 13/06/2019
     */
    public function procesarAction(Request $objRequest)
    { 
        $arrayData      = json_decode($objRequest->getContent(),true);
        $strToken       = "";
        $objResponse    = new Response();
        $strOp          = $arrayData['op'];
        
        if($arrayData['source'])
        {        
            $strToken = $this->validateGenerateToken($arrayData['token'], $arrayData['source'], $arrayData['user']);

            if(!$strToken)
            {
                return new Response(json_encode(array('status'  => 403,
                                                      'mensaje' => "Token Inválido"
                                                     )
                                                )
                                   );
            }               
        } 

        if($strOp == 'comprobantes')
        {
            $arrayResponse = $this->obtenerPdfXml($arrayData['data']);    
        }
        else  
        {
            $arrayResponse['status']  = $this->status['METODO'];
            $arrayResponse['mensaje'] = $this->mensaje['METODO'];
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
    
    /**
     * Función que devuelve los comprobantes electrónicos en formato pdf y xml 
     * encriptados en base64.
     *
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 1.0 13/06/2019
     */
    private function obtenerPdfXml($arrayData)
    {
        /* @var $serviceInfoCompElectronico \telconet\financieroBundle\Service\InfoCompElectronicoService */        
        $serviceInfoCompElectronico = $this->get('financiero.InfoCompElectronico');

        $strPdf = null;    
        $strXml = null;
        try
        {           
            if(is_numeric($arrayData['idDocumento']))
            {
                $arrayComprobantesElectronicos = $serviceInfoCompElectronico->getCompElectronicosPdfXml($arrayData['idDocumento']);
                if($arrayComprobantesElectronicos)
                {         
                    if($arrayComprobantesElectronicos['pdf'])
                    {
                        $strPdf = base64_encode($arrayComprobantesElectronicos['pdf']);
                    }
                    if($arrayComprobantesElectronicos['xml'])
                    {
                        $strXml = base64_encode($arrayComprobantesElectronicos['xml']);
                    }

                    $arrayComprobantes = array(                                                 
                                            'pdf'   => $strPdf,
                                            'xml'   => $strXml
                                            );                    

                    $arrayResponse['comprobantes'] = (json_encode($arrayComprobantes));

                    $arrayResponse['status']  = "OK";
                    $arrayResponse['mensaje'] = "OK"; 
                }    
                else
                {
                    $arrayResponse['status']  = "ERROR";
                    $arrayResponse['mensaje'] = "No se obtuvieron los datos.";
                }
            }   
            else
            {
                $arrayResponse['strMensaje'] = 'Error en los parámetros';
            }
        }
        catch (Exception $ex)
        {
            $arrayResponse['strMensaje'] = 'Error: ' . $ex->getMessage();
        }

        return $arrayResponse;
    } 
}
