<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace telconet\tecnicoBundle\Service;

/**
* AuthorizationFoxService, Service donde se invocará al procedimiento de autorizacion
* @author Sofía Fernández <sfernandez@telconet.ec>          
* @version 1.0 25-06-2018
*/
class AuthorizationFoxService 
{
    private $emComercial;
    private $serviceFox;
    private $strMultiUrnSeparador;
    private $strRatingSuccess;
    private $strRatingError;
    private $strTtlSuccess;
    private $strTtlError;
    private $emgen;
    
    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
       $this->emComercial          = $container->get('doctrine')->getManager('telconet');
       $this->serviceFox           = $container->get('tecnico.FoxPremium');
       $this->strMultiUrnSeparador = $container->getParameter("fox.authorization.multiurn_separator");
       $this->strRatingSuccess     = $container->getParameter("fox.authorization.rating_success");
       $this->strRatingError       = $container->getParameter("fox.authorization.rating_error");
       $this->strTtlSuccess        = $container->getParameter("fox.authorization.ttl_success");
       $this->strTtlError          = $container->getParameter("fox.authorization.ttl_error");
       $this->emgen                = $container->get('doctrine')->getManager('telconet_general');
    }
    
    /**
     * autorizarServicio, funcion que invoca al paquete P_VERIFICA_ESTADO_SERVICIO
     * @param type array $arrayParametros
     * @return type array $arrayRespuesta
     * @author Sofía Fernández <sfernandez@telconet.ec>          
     * @version 1.0 25-06-2018
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.1 11-09-2020
     * Se modifica valores de entrada del paquete.
     * Determina si es FOX, PARAMOUNT O NOGGIN
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 06-08-2021 Se agrega nuevo parámetro al procedimiento de base necesario para servicios que no cuenten con la urn
     * 
     */
    public function autorizarServicio($arrayParametros) 
    { 

        $strCodigoSalida = str_repeat(' ', 200);
        $strMensajeSalida = str_repeat(' ', 200);
        
        $strSql     = 'BEGIN DB_COMERCIAL.COMEK_CONSULTAS.P_VERIFICA_ESTADO_SERVICIO('
                                                                                    . ':intIdSpcSuscriber, '
                                                                                    . ':subscriber_id, '
                                                                                    . ':country_code, '
                                                                                    . ':resource_id, '
                                                                                    . ':strCodigoSalida, '
                                                                                    . ':strMensajeSalida, '
                                                                                    . ':strCodigoUrn, '
                                                                                    . ':strSsid); END;';
        try
        {
            $strStmt = $this->emComercial->getConnection()->prepare($strSql);
            $strStmt->bindParam('intIdSpcSuscriber', $arrayParametros['intIdSpcSuscriber']);
            $strStmt->bindParam('subscriber_id', $arrayParametros['subscriber_id']);
            $strStmt->bindParam('country_code',  $arrayParametros['country_code']);
            $strStmt->bindParam('resource_id',   $arrayParametros['resource_id']);
            $strStmt->bindParam('strCodigoSalida',    $strCodigoSalida);
            $strStmt->bindParam('strMensajeSalida',    $strMensajeSalida);
            $strStmt->bindParam('strCodigoUrn',  $arrayParametros['strCodigoUrn']);
            $strStmt->bindParam('strSsid',       $arrayParametros['strSsid']);
            $strStmt->execute();
        } 
        catch (\Exception $ex) 
        {
            error_log("AuthenticationFoxService->autenticarUsuaruioYContrasena " . $ex->getMessage());
        }
        $arrayRespuesta['strCodigoSalida'] = $strCodigoSalida;
        $arrayRespuesta['strMensajeSalida'] = $strMensajeSalida;
        
        return $arrayRespuesta;

    }

    /**
     * Función que realiza la autorización de una petición Fox.
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0
     * @since 30-11-2018
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.1 11-09-2020
     * Se agrega condicional que determina si es FOX, PARAMOUNT O NOGGIN
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.2
     * Se modifica Metodo autorizacionFox para validar que la urn sean correspondiente a los productos Fox, Paramount y Noggin.
     * @since 07-12-2020
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 07-08-2021 Se modifica programación para realizar validaciones del canal del fútbol
     * 
     * @author Daniel jose <djreyes@telconet.ec>
     * @version 1.4 01-10-2021 - Se modifica proceso de autorizacion para los productos que poseen un codigo urn y tambien un
     *                          tiempo de vigencia de cobertura, al momento que cancelan los serivios
     * 
     */
    public function autorizacionFox($arrayParametros)
    {
        $boolAplicaFlujoProdsExistentes = false;
        $arrayRespuestaFinal            = array();
        $intIdSpcSuscriber              = 0;
        $strNombreTecnicoProd           = "";
        try
        {
            $strBandVigencia = "NO";
            $objServiProducto = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                        ->findOneById($arrayParametros["subscriber_id"]);
            if (empty($objServiProducto))
            {
                throw new \Exception("No existe servicio asociado al subscriber");
            }
            else
            {
                $objProducto = $objServiProducto->getProductoId();
                if (!empty($objProducto))
                {
                    $strEstado = $objServiProducto->getEstado();
                    // Para determinar productos cancelados con resourceId y que deben ir por flujo de vigencia
                    $arrayVigenciaProducto = $this->emgen->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getOne('FLUJO_DE_VIGENCIA_PRODUCTO_TV',//nombre parametro cab
                                                    'COMERCIAL', //modulo cab
                                                    'NOMBRE_TECNICO_PROD_TV',//proceso cab
                                                    'PRODUCTO_RESOURCE_TV', //descripcion det
                                                    $objProducto->getNombreTecnico(),
                                                    '','','','','18'); //empresa
                    if (!empty($arrayVigenciaProducto) && $strEstado == 'Cancel')
                    {
                        $strBandVigencia = "SI";
                    }
                }
            }
            if(isset($arrayParametros['resource_id']) && !empty($arrayParametros['resource_id'])
                && $strBandVigencia == "NO")
            {
                $arrayResourceId     = explode($this->strMultiUrnSeparador, $arrayParametros['resource_id']);
                $arrayResourceId     = array_unique($arrayResourceId);
                $intContador         = 0;
                $arrayRespuestaTotal = array();

                $objParametroDet = $this->emgen->getRepository('schemaBundle:AdmiParametroDet')
                                               ->findOneBy(array('valor1'=> $arrayParametros['resource_id'],
                                                                 'estado'=> 'Activo'));
                if (!is_object($objParametroDet))
                {
                    $arrayRespuestaFinal["rating"]  = $this->strRatingError;
                    $arrayRespuestaFinal["ttl"]     = $this->strTtlError;
                    throw new \Exception("El valor del Canal es incorrecto");
                }
                $strNombreTecnicoProd           = $objParametroDet->getValor2();
                $boolAplicaFlujoProdsExistentes = true;
            }
            else
            {
                $arrayDetsCaractsVerifXSuscribersProd   = $this->emgen->getRepository('schemaBundle:AdmiParametroDet')
                                                                      ->get('CARAC_PRODUCTOS_TV',
                                                                            '',
                                                                            '',
                                                                            '',
                                                                            '',
                                                                            '',
                                                                            '',
                                                                            '',
                                                                            '',
                                                                            '18',
                                                                            '',
                                                                            '',
                                                                            'VERIFICACION_INICIAL_POR_SUSCRIBER');
                if(!isset($arrayDetsCaractsVerifXSuscribersProd) || empty($arrayDetsCaractsVerifXSuscribersProd))
                {
                    $arrayRespuestaFinal["rating"]  = $this->strRatingError;
                    $arrayRespuestaFinal["ttl"]     = $this->strTtlError;
                    throw new \Exception("Existen parámetros vacíos o inválidos");
                }
                
                foreach($arrayDetsCaractsVerifXSuscribersProd as $arrayCaractSuscriberProd)
                {
                    $strNombreTecnicoProdVerif          = $arrayCaractSuscriberProd["valor1"];
                    $strDescripCaractSuscriberProdVerif = $arrayCaractSuscriberProd["valor4"];
                    $arrayParamsGetInfoSuscriber        = array(
                                                                "strNombreTecnicoProd"          => $strNombreTecnicoProdVerif,
                                                                "strDescripcionCaract"          => $strDescripCaractSuscriberProdVerif,
                                                                "strValorCaract"                => $arrayParametros['subscriber_id'],
                                                                "strEstadoSpcEstaParametrizado" => "SI");
                    
                    $arrayRespuestaGetInfoSuscriber     = $this->emComercial->getRepository("schemaBundle:InfoServicio")
                                                                            ->obtieneInfoSpcProductosTv($arrayParamsGetInfoSuscriber);
                    if($arrayRespuestaGetInfoSuscriber['status'] !== "OK")
                    {
                        throw new \Exception("No se ha podido validar el suscriber_id enviado");
                    }
                    $arrayRegistrosGetInfoSuscriber     = $arrayRespuestaGetInfoSuscriber["arrayRegistros"];
                    if(isset($arrayRegistrosGetInfoSuscriber[0]) && !empty($arrayRegistrosGetInfoSuscriber[0]))
                    {
                        $intIdSpcSuscriber      = $arrayRegistrosGetInfoSuscriber[0]["intIdSpc"];
                        $strNombreTecnicoProd   = $arrayRegistrosGetInfoSuscriber[0]["strNombreTecnicoProd"];
                    }
                }
                
                if(!isset($intIdSpcSuscriber) || empty($intIdSpcSuscriber))
                {
                    throw new \Exception("No se ha podido obtener la información asociada al suscriber enviado");
                }
            }
                        
            $arrayProducto = $this->serviceFox->determinarProducto(array('strNombreTecnico'=> $strNombreTecnicoProd));
            if($arrayProducto['Status'] != 'OK')
            {
                if($boolAplicaFlujoProdsExistentes)
                {
                    $arrayRespuestaFinal["rating"]  = $this->strRatingError;
                    $arrayRespuestaFinal["ttl"]     = $this->strTtlError;
                }
                throw new \Exception($arrayProducto['Mensaje']);
            }
            
            $arrayParametros['strSsid']       =  $arrayProducto['strSsid'];
            if($boolAplicaFlujoProdsExistentes)
            {
                foreach ($arrayResourceId as $strResourceId)
                {
                    $strResourceId                  = trim($strResourceId);
                    $arrayRespuesta                 = null;
                    $intContador                   += 1;
                    $arrayParametros['strCodigoUrn']    =  $arrayProducto['strCodigoUrn'];
                    $arrayParametros["resource_id"]     = $strResourceId;
                    $arrayRespuestaAutorizacion     = $this->autorizarServicio($arrayParametros);
                    if ("OK" == $arrayRespuestaAutorizacion['strCodigoSalida'])
                    {
                        $arrayRespuesta['access']      = true;
                        $arrayRespuesta['rating']      = $this->strRatingSuccess;
                        $arrayRespuesta['ttl']         = $this->strTtlSuccess;
                        $arrayRespuesta['resource_id'] = $strResourceId;
                    }
                    else
                    {
                        $arrayError["errorCode"]       = $arrayRespuestaAutorizacion['strCodigoSalida'];
                        $arrayError["details"]         = $arrayRespuestaAutorizacion['strMensajeSalida'];
                        $arrayRespuesta['access']      = false;
                        $arrayRespuesta['rating']      = $this->strRatingError;
                        $arrayRespuesta['ttl']         = $this->strTtlError;
                        $arrayRespuesta['resource_id'] = $strResourceId;
                        $arrayRespuesta['error']       = $arrayError;
                    }
                    $arrayRespuestaFinal[] = $arrayRespuesta;
                }
                //Si sólo es un resource_id, se envía la respuesta por sólo una consulta.
                if (1 == $intContador)
                {
                   $arrayError = $arrayRespuestaFinal[0]['error'];
                   $arrayRespuestaFinal = array('access' => $arrayRespuestaFinal[0]['access'],
                                                'rating' => $arrayRespuestaFinal[0]['rating'],
                                                'ttl'    => $arrayRespuestaFinal[0]['ttl']);
                   if (isset($arrayError))
                   {
                       $arrayRespuestaFinal['error'] = $arrayError;
                   }
                }
            }
            else
            {
                $arrayParametros["intIdSpcSuscriber"] = $intIdSpcSuscriber;
                $arrayRespuestaAutorizacion = $this->autorizarServicio($arrayParametros);
                if ($arrayRespuestaAutorizacion['strCodigoSalida'] === "OK")
                {
                    $arrayRespuestaFinal['access']          = true;
                    $arrayRespuestaFinal['suscriber_id']    = $arrayParametros["subscriber_id"];
                    $arrayRespuestaFinal['country_code']    = $arrayParametros["country_code"];
                }
                else
                {
                    $arrayError["errorCode"]        = $arrayRespuestaAutorizacion['strCodigoSalida'];
                    $arrayError["details"]          = $arrayRespuestaAutorizacion['strMensajeSalida'];
                    $arrayRespuestaFinal['access']  = false;
                    $arrayRespuestaFinal['error']   = $arrayError;
                    
                }
            }
            
        }
        catch(\Exception $e)
        {
            $arrayRespuestaFinal['access']  = false;
            $arrayRespuestaFinal['error']   = array("details" => $e->getMessage());
        }
        return $arrayRespuestaFinal;
    }
}
