<?php

namespace telconet\tecnicoBundle\Service;

use Doctrine\ORM\EntityManager;
use telconet\schemaBundle\Entity\InfoOrdenTrabajo;
use telconet\schemaBundle\Entity\InfoServicioProdCaract;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

/**
 * Documentación para la clase 'LicenciasMcAfeeService'.
 *
 * Clase utilizada para manejar metodos que permiten realizar la generacion de licencias de productod McAfee
 *
 * @author Jesus Bozada <jbozada@telconet.ec>
 * @version 1.0 05-01-2015
 */
class LicenciasMcAfeeService
{

    private $emComercial;
    private $emInfraestructura;
    private $serviceUtil;
    private $container;
    private $strCaracteresValidos ='/[^A-Za-z0-9ÑÁÉÍÓÚáéíóúñ ]/';

    public function setDependencies(Container $container)
    {
        $this->container         = $container;
        $this->emInfraestructura = $container->get('doctrine')->getManager('telconet_infraestructura');
        $this->emComercial       = $container->get('doctrine')->getManager('telconet');
        $this->serviceUtil       = $container->get('schema.Util');
        $this->serviceTecnico    = $container->get('tecnico.InfoServicioTecnico');
    }

    /**
     * Funcion que sirve para crear Licencia de productos mcAfee Standard a un cliente nuevo
     * @param array $arrayParametros Parametros necesarios para la activacion y cancelacion de suscripciones McAfee
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 05-01-2015
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 28-02-2019 Se agrega registro de excepciones en tablas de error del telcos
     * @since 1.0
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.2 15-03-2019 Se agrega return por error SOAP en consumo de WS de digiway.
     * @since 1.1
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.3 02-04-2019 Se modifica mensaje de respuesta por excepción en todos los catch
     * @since 1.2
     */
    public function operacionesSuscripcionCliente($arrayParametros)
    {
        $strNombre            = $arrayParametros["strNombre"];
        $strApellido          = $arrayParametros["strApellido"];
        $strIdentificacion    = $arrayParametros["strIdentificacion"];
        $strCustomerContextId = $arrayParametros["strCustomerContextId"];
        $strPartnerRef        = $arrayParametros["strPartnerRef"];
        $strPassword          = $arrayParametros["strPassword"];
        $strCorreo            = $arrayParametros["strCorreo"];
        $strMetodo            = $arrayParametros["strMetodo"];
        $intLIC_QTY           = $arrayParametros["intLIC_QTY"];
        $intQTY               = $arrayParametros["intQTY"];
        $strSKU               = $arrayParametros["strSKU"];
        $strSKU2              = $arrayParametros["strSKU2"];
        $strTipoTransaccion   = $arrayParametros["strTipoTransaccion"];
        $strReferencia        = $arrayParametros["strReferencia"];
        ini_set('default_socket_timeout', 400000);
        $arrayRespuesta       = array();
        $strMensajeRespuesta  = "";
        try
        { 
            $strNombre              = preg_replace($this->strCaracteresValidos,'',$strNombre);
            $strApellido            = preg_replace($this->strCaracteresValidos, '', $strApellido);
            $entityAdmiParametroDet = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                  ->findOneBy(array("valor1" => "MCAFEE_MEMBERID",
                                                                    "estado" => "Activo"
                                                                   )
                                                             );
            if(!$entityAdmiParametroDet)
            {
                throw new \Exception("problemas al obtener informacion del producto McAfee.");
            }
            $strMemberId = $entityAdmiParametroDet->getValor2();
            $strUrlWsdl  = $this->container->getParameter('url_wsdl_mcafee');
            $client      = new \SoapClient($strUrlWsdl);
            if (!$strSKU2)
            {
                $arrayProducto = array('DetalleProducto' => array(
                                                                 'LIC_QTY' => $intLIC_QTY,
                                                                 'QTY'     => $intQTY,
                                                                 'SKU'     => $strSKU
                                                                 )
                                      );
            }
            else
            {
                $DetalleProducto[] = array(
                                           'LIC_QTY' => $intLIC_QTY,
                                           'QTY'     => $intQTY,
                                           'SKU'     => $strSKU
                                          );
                $DetalleProducto[] = array(
                                           'LIC_QTY' => $intLIC_QTY,
                                           'QTY'     => $intQTY,
                                           'SKU'     => $strSKU2
                                          );
                $arrayProducto = array('DetalleProducto' => $DetalleProducto);
            }

            $parametro   = array ('suscripcion' => array  (  
                                                           'Cliente'    => array(
                                                                                 'Apellido'          => $strApellido,
                                                                                 'CustomerContextId' => $strCustomerContextId,
                                                                                 'Email'             => $strCorreo,
                                                                                 'Identificacion'    => $strIdentificacion,
                                                                                 'Nombre'            => $strNombre,
                                                                                 'Password'          => $strPassword
                                                                                ),
                                                           'MemberId'    => $strMemberId,
                                                           'PartnerRef'  => $strPartnerRef,
                                                           'Productos'       => $arrayProducto,
                                                           'Ref'             => $strReferencia,
                                                           'TipoTransaccion' => $strTipoTransaccion,
                                                          )
                                 );
            
            if ($strMetodo == 'CrearNuevaSuscripcion')
            {
                $userResult                       = $client->CrearNuevaSuscripcion($parametro);
                $strDetalleRespuesta              = $userResult->CrearNuevaSuscripcionResult;
                
                
            }
            elseif($strMetodo == 'CrearSuscripcionMultidispositivo')
            {
                $userResult                       = $client->CrearSuscripcionMultidispositivo($parametro);
                $strDetalleRespuesta              = $userResult->CrearSuscripcionMultidispositivoResult;
            }
            elseif($strMetodo == 'ActualizarSuscripcion')
            {
                $userResult                       = $client->ActualizarSuscripcion($parametro);
                $strDetalleRespuesta              = $userResult->ActualizarSuscripcionResult;
            }
            elseif($strMetodo == 'ActualizarSuscripcionMultidispositivo')
            {
                $userResult                       = $client->ActualizarSuscripcionMultidispositivo($parametro);
                $strDetalleRespuesta              = $userResult->ActualizarSuscripcionMultidispositivoResult;
            }
            elseif($strMetodo == 'CancelarSuscripcion')
            {
                $userResult                       = $client->CancelarSuscripcion($parametro);
                $strDetalleRespuesta              = $userResult->CancelarSuscripcionResult;
            }
            elseif($strMetodo == 'ActualizarPerfil')
            {
                $userResult                       = $client->ActualizarPerfil($parametro);
                $strDetalleRespuesta              = $userResult->ActualizarPerfilResult;
            }
            
            $estadoTransaccion                = ($strDetalleRespuesta->Estado) ? 'true' : 'false';
            $arrayRespuesta["procesoExitoso"] = $estadoTransaccion;
            $strMensajeRespuesta              = "\n".ucfirst($strDetalleRespuesta->Mensaje)."\n";
            $strMensajeRespuesta             .= "\tDetalle:\n";
            
            if($strDetalleRespuesta->Detalle)
            {
                foreach($strDetalleRespuesta->Detalle as $detalle1)
                {
                    if (is_array($detalle1))
                    {
                        foreach($detalle1 as $detalleRegistro)
                        {
                            $strMensajeRespuesta .= "\t\t".$detalleRegistro."\n";
                        }
                    }
                    else
                    {
                        $strMensajeRespuesta .= "\t\t".$detalle1."\n";
                    }
                }
            }
            $arrayRespuesta["referencia"]       = $strDetalleRespuesta->Reff;
            $arrayRespuesta["mensajeRespuesta"] = $strMensajeRespuesta;
           
            
            
            return $arrayRespuesta;
        
        }
        catch (\SoapFault $objFault)
        {
            $this->serviceUtil->insertError('Telcos+', 
                                            'LicenciasMcAfeeService.operacionesSuscripcionCliente', 
                                            substr($objFault->getTraceAsString(), 0, 3000), 
                                            'telcos', 
                                            '127.0.0.1');
            $arrayRespuesta["mensajeRespuesta"] = "Problemas al ejecutar el WS McAfee (Intermitencia).";
            $arrayRespuesta["procesoExitoso"]   = "false";
            return $arrayRespuesta;
        }
        catch(\Exception $ex)
        {
            error_log("Error excepcion: " . $ex->getMessage());
            $this->serviceUtil->insertError('Telcos+', 
                                            'LicenciasMcAfeeService.operacionesSuscripcionCliente', 
                                            substr($ex->getMessage(), 0, 3000), 
                                            'telcos', 
                                            '127.0.0.1');
            $booleanValidaErrorSoap = strpos($ex->getMessage(), 'SOAP-ERROR');
            if($booleanValidaErrorSoap !== false)
            {
                $arrayRespuesta["mensajeRespuesta"] = " Problemas de conexión con McAfee.";
            }
            else
            {
                $arrayRespuesta["mensajeRespuesta"] = " Problemas al ejecutar Operación McAfee.";
            }
            $arrayRespuesta["procesoExitoso"]   = "false";
            return $arrayRespuesta;
        }
    }
    
    /**
     * Funcion que registra las caracteristicas del producto utilizado en un servicio
     * 
     * @author  Creado: Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 07-01-2015
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 19-12-2018 Se modifica el envío de parámetros a la función
     * 
     * @param array $arrayParametros [  "objServicio"       => objeto del servicio,
     *                                  "objProducto"       => objeto del producto McAfee
     *                                  "strCaracteristica" => descripción de la característica,
     *                                  "strValor"          => valor de la característica,
     *                                  "strUsrCreacion"    => usuario en sesión
     *                               ]
     * @return  array $respuesta
     */
    public function guardaServicioProductoCaracteristicaPorServicio($arrayParametros)
    {
        $objServicio        = $arrayParametros["objServicio"];
        $strCaracteristica  = $arrayParametros["strCaracteristica"];
        $strValor           = $arrayParametros["strValor"];
        $strUsrCreacion     = $arrayParametros["strUsrCreacion"];
            
        try
        {
        
            $entityAdmiCaracteristica   = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                            ->findOneBy(array("descripcionCaracteristica" => $strCaracteristica,
                                                                              "estado"                    => "Activo"
                                                                             )
                                                                       );

            if ($objServicio->getProductoId())
            {
                $entityAdmiProductoCaracteristica   = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                        ->findOneBy(array("productoId"       => $objServicio->getProductoId(),
                                                                                          "caracteristicaId" => $entityAdmiCaracteristica->getId(),
                                                                                          "estado"           => "Activo"
                                                                                         )
                                                                                   );
            }
            else
            {
                if(isset($arrayParametros["objProducto"]) && is_object($arrayParametros["objProducto"]))
                {
                    $intIdProducto = $arrayParametros["objProducto"]->getId();
                }
                else
                {
                    $entityPlanDet  = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                                        ->findOneByPlanId($objServicio->getPlanId()->getId());
                    $intIdProducto  = $entityPlanDet->getProductoId();
                }
                
                $entityAdmiProductoCaracteristica   = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                        ->findOneBy(array("productoId"       => $intIdProducto,
                                                                                          "caracteristicaId" => $entityAdmiCaracteristica->getId(),
                                                                                          "estado"           => "Activo"
                                                                                         )
                                                                                    );

            }

            //Guardar informacion de la caracteristica del producto
            $entityServicioProdCaract = new InfoServicioProdCaract();
            $entityServicioProdCaract->setServicioId($objServicio->getId());
            $entityServicioProdCaract->setProductoCaracterisiticaId($entityAdmiProductoCaracteristica->getId());
            $entityServicioProdCaract->setValor($strValor);
            $entityServicioProdCaract->setEstado('Activo');
            $entityServicioProdCaract->setUsrCreacion($strUsrCreacion);
            $entityServicioProdCaract->setFeCreacion(new \DateTime('now'));
            $this->emComercial->persist($entityServicioProdCaract);
            
            $respuesta = array("status"=> "OK", "mensaje" => "Caracteristica Guardada exitosamente");
            return $respuesta;
        }
        catch(\Exception $ex)
        {
            error_log("error: " . $ex->getMessage());
            $respuesta = array("status"=> "ERROR", "mensaje" => "Problemas al guardar caracteristicas: ".$strCaracteristica);
            return $respuesta;
        }
    }
    
    /**
     * Funcion que retorna información de cliente McAfee
     * 
     * @author  Creado: Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 20-01-2015
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 19-12-2018 Se modifica el envío de parámetros a la función
     * 
     * @param array $arrayParametros [  "objServicio"       => objeto del servicio,
     *                                  "objProducto"       => objeto del producto McAfee
     *                                  "strCaracteristica" => descripción de la característica
     *                               ]
     * @return array $respuesta
     * 
     */
    public function obtenerValorServicioProductoCaracteristicaPorServicio($arrayParametros)
    {
        $strCaracteristica  = $arrayParametros["strCaracteristica"];
        $entityInfoServicio = $arrayParametros["objServicio"];
        try
        {
            $entityAdmiCaracteristica   = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                            ->findOneBy(array("descripcionCaracteristica" => $strCaracteristica,
                                                                              "estado"                    => "Activo"
                                                                             )
                                                                       );

            if ($entityInfoServicio->getProductoId())
            {
                $entityAdmiProductoCaracteristica   = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                        ->findOneBy(array("productoId"       => $entityInfoServicio->getProductoId(),
                                                                                          "caracteristicaId" => $entityAdmiCaracteristica->getId(),
                                                                                          "estado"           => "Activo"
                                                                                         )
                                                                                    );
            }
            else
            {
                if(isset($arrayParametros["objProducto"]) && is_object($arrayParametros["objProducto"]))
                {
                    $intIdProducto = $arrayParametros["objProducto"]->getId();
                }
                else
                {
                    $entityPlanDet  = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                                        ->findOneByPlanId($entityInfoServicio->getPlanId()->getId());
                    $intIdProducto  = $entityPlanDet->getProductoId();
                }
                
                $entityAdmiProductoCaracteristica   = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                        ->findOneBy(array("productoId"       => $intIdProducto,
                                                                                          "caracteristicaId" => $entityAdmiCaracteristica->getId(),
                                                                                          "estado"           => "Activo"
                                                                                         )
                                                                                    );
                
            }

            if ($entityAdmiProductoCaracteristica)
            {
                $entityInfoServicioProdCaract   = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                    ->findOneBy(array("productoCaracterisiticaId" => 
                                                                                      $entityAdmiProductoCaracteristica->getId(),
                                                                                      "servicioId"                => $entityInfoServicio->getId(),
                                                                                      "estado"                    => "Activo"
                                                                                     )
                                                                               );
            }
            else
            {
                $entityInfoServicioProdCaract = null;
            }
            
            $respuesta = array("status"=> "OK", "mensaje" => $entityInfoServicioProdCaract);
            return $respuesta;
        }
        catch(\Exception $ex)
        {
            error_log("error: " . $ex->getMessage());
            $respuesta = array("status"=> "ERROR", "mensaje" => "Problemas al obtener valor de caracteristica.");
            return $respuesta;
        }
        
    }
    
    /**
     * Funcion que genera descuento de planes TRIAL de McAfee
     * 
     * @author  Creado: Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 27-01-2015
     * @param  entityManager $em //identificador del servicio
     * @param  object $infoServicio
     * @param  string $usrCreacion
     * 
    */
    public function generaSolicitudDescuentoMcAfee($em, $infoServicio, $usrCreacion)
    {
        try
        {
            $entityMotivo = $em->getRepository('schemaBundle:AdmiMotivo')
                               ->findOneBy(array("nombreMotivo"=>"Descuento Promocion",
                                                 "estado"=>"Activo"
                                                )
                                          );

            $tipoSolicitud = $em->getRepository('schemaBundle:AdmiTipoSolicitud')
                                 ->findOneBy(array("descripcionSolicitud"=>"SOLICITUD DESCUENTO UNICO",
                                                   "estado"=>"Activo"
                                                  )
                                            );

            if ($entityMotivo && $tipoSolicitud)
            {
                //inserto en la tabla InfoDetalleSolicitud
                $InfoDetalleSolicitud = new InfoDetalleSolicitud();
                $InfoDetalleSolicitud->setServicioId($infoServicio);
                $InfoDetalleSolicitud->setTipoSolicitudId($tipoSolicitud); //tipo de solicitud de descuento
                $InfoDetalleSolicitud->setMotivoId($entityMotivo->getId());
                $InfoDetalleSolicitud->setUsrCreacion($usrCreacion);
                $InfoDetalleSolicitud->setFeCreacion(new \DateTime('now'));
                $InfoDetalleSolicitud->setPorcentajeDescuento(100);
                $InfoDetalleSolicitud->setObservacion("Por promocion de suscripciones de servicio McAfee Trial");
                $InfoDetalleSolicitud->setEstado('Aprobado');
                $em->persist($InfoDetalleSolicitud);

                //se realiza el insert en la tabla de historicos INFO_DETALLE_SOL_HIST
                $InfoDetalleSolHist = new InfoDetalleSolHist();
                $InfoDetalleSolHist->setDetalleSolicitudId($InfoDetalleSolicitud);
                $InfoDetalleSolHist->setMotivoId($entityMotivo->getId());
                $InfoDetalleSolHist->setObservacion("Por promocion de suscripciones de servicio McAfee Trial");
                $InfoDetalleSolHist->setUsrCreacion($usrCreacion);
                $InfoDetalleSolHist->setFeCreacion(new \DateTime('now'));
                $InfoDetalleSolHist->setEstado('Aprobado');
                $em->persist($InfoDetalleSolHist);
            }
            else
            {
                throw new \Exception("problemas al generar descuento al cliente");
            }
            $respuesta = array("status"=> "OK", "mensaje" => "Solicitud generada exitosamente.");
            return $respuesta;
        }
        catch(\Exception $ex)
        {
            error_log("error: " . $ex->getMessage());
            $respuesta = array("status"=> "ERROR", "mensaje" => "Problemas al generar solicitud de descuento.");
            return $respuesta;
        }
    }
    
    /**
     * generaOrdenDeTrabajo
     * 
     * Funcion que genera orden de trabajo de McAfee
     * 
     * @author  Creado: Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 30-01-2015
     * @param  entityManager $em //identificador del servicio
     * @param  string $codEmpresa
     * @param  string $idOficina
     * @param  object $entityPunto
     * @param  string $clientIp
     * @param  string $usrCreacion
     * 
     */
    public function generaOrdenDeTrabajo($em, $codEmpresa, $idOficina, $entityPunto, $clientIp, $usrCreacion)
    {
        try
        {
            $datosNumeracion = $em->getRepository('schemaBundle:AdmiNumeracion')->findByEmpresaYOficina($codEmpresa,$idOficina,'ORD');
            $secuencia_asig = str_pad($datosNumeracion->getSecuencia(),7, '0', STR_PAD_LEFT);
            $numero_de_contrato = $datosNumeracion->getNumeracionUno().'-'.$datosNumeracion->getNumeracionDos().'-'.$secuencia_asig;

            $entityOrdenTrabajo  = new InfoOrdenTrabajo();
            $entityOrdenTrabajo->setPuntoId($entityPunto);
            $entityOrdenTrabajo->setTipoOrden('N');
            $entityOrdenTrabajo->setNumeroOrdenTrabajo($numero_de_contrato);
            $entityOrdenTrabajo->setFeCreacion(new \DateTime('now'));
            $entityOrdenTrabajo->setUsrCreacion($usrCreacion);
            $entityOrdenTrabajo->setIpCreacion($clientIp);
            $entityOrdenTrabajo->setOficinaId($idOficina);
            $entityOrdenTrabajo->setEstado('Activo');
            $em->persist($entityOrdenTrabajo);
            $respuesta = array("status"=> "OK", "mensaje" => $entityOrdenTrabajo);
            return $respuesta;
        }
        catch(\Exception $ex)
        {
            error_log("error: " . $ex->getMessage());
            $respuesta = array("status"=> "ERROR", "mensaje" => "Problemas al generar orden de trabajo.");
            return $respuesta;
        }
    }
    
    /** 
     * obtenerInformacionClienteMcAffe
     * 
     * Funcion que retorna informacion de clientes para suscripciones McAfee
     * 
     * @author  Creado: Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 07-01-2015
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 15-12-2018 Se modifica el envío de parámetros a la función obtenerValorServicioProductoCaracteristicaPorServicio
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.2 02-04-2019 Se modifica la forma de recuperar el correo a utilizar para la activación de suscripciones mcafee
     * @since 1.1
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.3 06-06-2019 Se modifica generación de CCID mediante secuencia de base de datos
     * @since 1.2
     * 
     * @param   array $arrayParametrosMcAfee[   "intIdPersona"      => id de la persona del cliente
     *                                          "intIdServicio"     => id del servicio
     *                                          "strNombrePlan"     => nombre del plan de servicio
     *                                          "strEsActivacion"   => tipo de operación a realizar
     *                                      ]
     * 
     * @return  array $arrayParametros
     * 
     */
    public function obtenerInformacionClienteMcAffe($arrayParametrosMcAfee)
    {
        $arrayParametros         = array();
        $strNombre               = "";
        $strApellido             = "";
        $strIdentificacion       = "";
        $strCorreo               = "";
        $strCantidadDispositivos = "";
        $strSku                  = "";
        $strSku2                 = "";
        $strCcid                 = "";
        $strCustomerContextId    = "";
        $strPartnerRef           = "";
        $strReferencia           = "";
        $idPersona               = $arrayParametrosMcAfee["intIdPersona"];
        $idServicio              = $arrayParametrosMcAfee["intIdServicio"];
        $nombrePlan              = $arrayParametrosMcAfee["strNombrePlan"];
        $esActivacion            = $arrayParametrosMcAfee["strEsActivacion"];
        $objProductoMcAfee       = is_object($arrayParametrosMcAfee["objProductoMcAfee"]) ? $arrayParametrosMcAfee["objProductoMcAfee"] : null;
        $em                      = $this->emComercial;
        $entityInfoPersona       = $em->getRepository('schemaBundle:InfoPersona')->findOneById($idPersona);
        try
        {
            if($entityInfoPersona->getRazonSocial())
            {
                $strNombre   = $entityInfoPersona->getRazonSocial();
                $strApellido = $entityInfoPersona->getRazonSocial();
            }
            else
            {
                $strNombre   = $entityInfoPersona->getNombres();
                $strApellido = $entityInfoPersona->getApellidos();
                
            }
            
            $entityInfoServicio = $em->getRepository('schemaBundle:InfoServicio')
                                     ->find($idServicio);
            $arrayParamsGetSpc  = array("objServicio"       => $entityInfoServicio,
                                        "objProducto"       => $objProductoMcAfee);
            $arrayParamsGetSpc["strCaracteristica"] = "CUSTOMERCONTEXTID";
            $strIdentificacion = $entityInfoPersona->getIdentificacionCliente();
            $respuesta         = $this->obtenerValorServicioProductoCaracteristicaPorServicio($arrayParamsGetSpc);
            if($respuesta["status"] == 'ERROR')
            {
                throw new \Exception($respuesta["mensaje"]);
            }
            $entityInfoServicioProdCaract = $respuesta["mensaje"];
            
            if ($entityInfoServicioProdCaract)
            {
                $strCustomerContextId = $entityInfoServicioProdCaract->getValor();
            }
            else
            {
                $strCustomerContextId = $em->getRepository('schemaBundle:InfoServicio')->getSecuenciaCcid();
            }
            
            $strPartnerRef = "";
            if ( $esActivacion == "NO" )
            {
                $arrayParamsGetSpc["strCaracteristica"] = "PARTNERREF";
                $respuesta = $this->obtenerValorServicioProductoCaracteristicaPorServicio($arrayParamsGetSpc);
                if($respuesta["status"] == 'ERROR')
                {
                    throw new \Exception($respuesta["mensaje"]);
                }
                $entityInfoServicioProdCaract = $respuesta["mensaje"];
                
                if ($entityInfoServicioProdCaract)
                {
                    $strPartnerRef = $entityInfoServicioProdCaract->getValor();
                }
            }
            else
            {
                $strPartnerRef = $em->getRepository('schemaBundle:InfoServicio')->getSecuenciaPartnerRef();
                $arrayParamsGetSpc["strCaracteristica"] = "PARTNERREF";
                $respuesta     = $this->obtenerValorServicioProductoCaracteristicaPorServicio($arrayParamsGetSpc);
                
                if($respuesta["status"] == 'ERROR')
                {
                    throw new \Exception($respuesta["mensaje"]);
                }
                $entityInfoServicioProdCaract = $respuesta["mensaje"];
                
                if ($entityInfoServicioProdCaract)
                {
                    $entityInfoServicioProdCaract->setEstado('Eliminado');
                    $em->persist($entityInfoServicioProdCaract);
                }
            }

            $entityInfoServicioProdCaract = "";
            $arrayParamsGetSpc["strCaracteristica"] = "CORREO ELECTRONICO";
            $respuesta                    = $this->obtenerValorServicioProductoCaracteristicaPorServicio($arrayParamsGetSpc);
            if($respuesta["status"] == 'ERROR')
            {
                throw new \Exception($respuesta["mensaje"]);
            }
            $entityInfoServicioProdCaract = $respuesta["mensaje"];
                
            if ($entityInfoServicioProdCaract)
            {
                $strCorreo = $entityInfoServicioProdCaract->getValor();
            }
            else
            {
                $strCorreo = $this->serviceTecnico->getCorreoDatosEnvioMd(array("intIdPunto"            => 
                                                                                $entityInfoServicio->getPuntoId()->getId(),
                                                                                "strValidaCorreoMcAfee" => 
                                                                                "SI",
                                                                                "strUsrCreacion"       =>
                                                                                'telcos',
                                                                                "strIpCreacion"       =>
                                                                                '127.0.0.1'));
                if (empty($strCorreo))
                {
                    throw new \Exception('No se recuperó ningún correo permitido del cliente para la activación de su suscripción McAfee.');
                }
            }
            
            $entityInfoServicioProdCaract = "";
            $arrayParamsGetSpc["strCaracteristica"] = "CANTIDAD DISPOSITIVOS";
            $respuesta                    = $this->obtenerValorServicioProductoCaracteristicaPorServicio($arrayParamsGetSpc);
            if($respuesta["status"] == 'ERROR')
            {
                throw new \Exception($respuesta["mensaje"]);
            }
            $entityInfoServicioProdCaract = $respuesta["mensaje"];

            if ($entityInfoServicioProdCaract)
            {
                $strCantidadDispositivos = $entityInfoServicioProdCaract->getValor();
            }
            $entityInfoServicioProdCaract = "";
            $arrayParamsGetSpc["strCaracteristica"] = "SKU";
            $respuesta                    = $this->obtenerValorServicioProductoCaracteristicaPorServicio($arrayParamsGetSpc);
            if($respuesta["status"] == 'ERROR')
            {
                throw new \Exception($respuesta["mensaje"]);
            }
            $entityInfoServicioProdCaract = $respuesta["mensaje"];
            
            if ($entityInfoServicioProdCaract)
            {
                $strSku = $entityInfoServicioProdCaract->getValor();
            }
            else
            {
                if ($nombrePlan)
                {
                    $entityAdmiParametroDet = $em->getRepository('schemaBundle:AdmiParametroDet')
                                                         ->findOneBy(array("descripcion" => $nombrePlan,
                                                                           "estado"      => "Activo"
                                                                          )
                                                                    );
                    if (!$entityAdmiParametroDet)
                    {
                        throw new \Exception("problemas al obtener informacion del producto McAfee.");
                    }
                    $strSku  = $entityAdmiParametroDet->getValor2();
                    $strSku2 = $entityAdmiParametroDet->getValor3();
                }
                
            }
            $arrayParamsGetSpc["strCaracteristica"] = "REFERENCIA";
            $respuesta = $this->obtenerValorServicioProductoCaracteristicaPorServicio($arrayParamsGetSpc);
            if($respuesta["status"] == 'ERROR')
            {
                throw new \Exception($respuesta["mensaje"]);
            }
            $entityInfoServicioProdCaract = $respuesta["mensaje"];
            
            if ($entityInfoServicioProdCaract)
            {
                $strReferencia = $entityInfoServicioProdCaract->getValor();
            }
            else
            {
                $strReferencia = "";
            }
            
            $arrayParametros["strNombre"]               = $strNombre;
            $arrayParametros["strApellido"]             = $strApellido;
            $arrayParametros["strIdentificacion"]       = $strIdentificacion;
            $arrayParametros["strCustomerContextId"]    = $strCustomerContextId;
            $arrayParametros["strPartnerRef"]           = $strPartnerRef;
            $arrayParametros["strPassword"]             = $strIdentificacion;
            $arrayParametros["strCorreo"]               = $strCorreo;
            $arrayParametros["strCantidadDispositivos"] = $strCantidadDispositivos;
            $arrayParametros["strSKU"]                  = $strSku;
            $arrayParametros["strSKU2"]                 = $strSku2;
            $arrayParametros["strError"]                = "false";
            $arrayParametros["strReferencia"]           = $strReferencia;
            
            return $arrayParametros;
        }
        catch(\Exception $ex)
        {
            error_log("error: " . $ex->getMessage());
            $arrayParametros["strError"] = "true";
            return $arrayParametros;
        }
    }
    
    /**
     * eliminarCaracteristicasLicenciasMcAfee
     * 
     * Función que sirve para eliminar las características de internet protegido McAfee y devolver string con valores de característica
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 12-08-2019
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 03-10-2019 Se traspasa la función usada en cambio de plan al service de McAfee y se agrega el proceso que realiza esta ejecución
     * 
     * @param $arrayParametros
     * @return String $strValoresCaract
     * @since 1.0
     */
    public function eliminarCaracteristicasLicenciasMcAfee($arrayParametros)
    {
        $objServicio        = $arrayParametros['objServicio'];
        $objProducto        = $arrayParametros['objProducto'];
        $strUsrCreacion     = $arrayParametros['strUsrCreacion'];
        $strValoresCaract   = "";
        $objSpcMcAfeeCorreo = $this->serviceTecnico
                                   ->getServicioProductoCaracteristica($objServicio,
                                                                       "CORREO ELECTRONICO",
                                                                       $objProducto);
        if(is_object($objSpcMcAfeeCorreo))
        {
            $objSpcMcAfeeCorreo->setEstado('Eliminado');
            $objSpcMcAfeeCorreo->setUsrUltMod($strUsrCreacion);
            $objSpcMcAfeeCorreo->setFeUltMod(new \DateTime('now'));
            $this->emComercial->persist($objSpcMcAfeeCorreo);
            $this->emComercial->flush();
            $strValoresCaract .= "CORREO ELECTRONICO: ".$objSpcMcAfeeCorreo->getValor()."<br>";
        }
        $objSpcMcAfeeInternet = $this->serviceTecnico
                                     ->getServicioProductoCaracteristica($objServicio,
                                                                         "TIENE INTERNET",
                                                                         $objProducto);
        if(is_object($objSpcMcAfeeInternet))
        {
            $objSpcMcAfeeInternet->setEstado('Eliminado');
            $objSpcMcAfeeInternet->setUsrUltMod($strUsrCreacion);
            $objSpcMcAfeeInternet->setFeUltMod(new \DateTime('now'));
            $this->emComercial->persist($objSpcMcAfeeInternet);
            $this->emComercial->flush();
            $strValoresCaract .= "TIENE INTERNET: ".$objSpcMcAfeeInternet->getValor()."<br>";
        }
        $objSpcMcAfeeCantidad = $this->serviceTecnico
                                     ->getServicioProductoCaracteristica($objServicio,
                                                                         "CANTIDAD DISPOSITIVOS",
                                                                         $objProducto);
        if(is_object($objSpcMcAfeeCantidad))
        {
            $objSpcMcAfeeCantidad->setEstado('Eliminado');
            $objSpcMcAfeeCantidad->setUsrUltMod($strUsrCreacion);
            $objSpcMcAfeeCantidad->setFeUltMod(new \DateTime('now'));
            $this->emComercial->persist($objSpcMcAfeeCantidad);
            $this->emComercial->flush();
            $strValoresCaract .= "CANTIDAD DISPOSITIVOS: ".$objSpcMcAfeeCantidad->getValor()."<br>";
        }
        $objSpcMcAfeeSku = $this->serviceTecnico
                                ->getServicioProductoCaracteristica($objServicio,
                                                                    "SKU",
                                                                    $objProducto);
        if(is_object($objSpcMcAfeeSku))
        {
            $objSpcMcAfeeSku->setEstado('Eliminado');
            $objSpcMcAfeeSku->setUsrUltMod($strUsrCreacion);
            $objSpcMcAfeeSku->setFeUltMod(new \DateTime('now'));
            $this->emComercial->persist($objSpcMcAfeeSku);
            $this->emComercial->flush();
            $strValoresCaract .= "SKU: ".$objSpcMcAfeeSku->getValor()."<br>";
        }
        $objSpcMcAfeeReintento = $this->serviceTecnico
                                      ->getServicioProductoCaracteristica($objServicio,
                                                                          "NUMERO REINTENTOS",
                                                                          $objProducto);
        if(is_object($objSpcMcAfeeReintento))
        {
            $objSpcMcAfeeReintento->setEstado('Eliminado');
            $objSpcMcAfeeReintento->setUsrUltMod($strUsrCreacion);
            $objSpcMcAfeeReintento->setFeUltMod(new \DateTime('now'));
            $this->emComercial->persist($objSpcMcAfeeReintento);
            $this->emComercial->flush();
            $strValoresCaract .= "NUMERO REINTENTOS: ".$objSpcMcAfeeReintento->getValor()."<br>";
        }
        $objSpcMcAfeePassword = $this->serviceTecnico
                                     ->getServicioProductoCaracteristica($objServicio,
                                                                         "PASSWORD",
                                                                         $objProducto);
        if(is_object($objSpcMcAfeePassword))
        {
            $objSpcMcAfeePassword->setEstado('Eliminado');
            $objSpcMcAfeePassword->setUsrUltMod($strUsrCreacion);
            $objSpcMcAfeePassword->setFeUltMod(new \DateTime('now'));
            $this->emComercial->persist($objSpcMcAfeePassword);
            $this->emComercial->flush();
            $strValoresCaract .= "PASSWORD: ".$objSpcMcAfeePassword->getValor()."<br>";
        }
        $objSpcMcAfeePartner = $this->serviceTecnico
                                    ->getServicioProductoCaracteristica($objServicio,
                                                                        "PARTNERREF",
                                                                        $objProducto);
        if(is_object($objSpcMcAfeePartner))
        {
            $objSpcMcAfeePartner->setEstado('Eliminado');
            $objSpcMcAfeePartner->setUsrUltMod($strUsrCreacion);
            $objSpcMcAfeePartner->setFeUltMod(new \DateTime('now'));
            $this->emComercial->persist($objSpcMcAfeePartner);
            $this->emComercial->flush();
            $strValoresCaract .= "PARTNERREF: ".$objSpcMcAfeePartner->getValor()."<br>";
        }
        $objSpcMcAfeeCcid = $this->serviceTecnico
                                 ->getServicioProductoCaracteristica($objServicio,
                                                                     "CUSTOMERCONTEXTID",
                                                                     $objProducto);
        if(is_object($objSpcMcAfeeCcid))
        {
            $objSpcMcAfeeCcid->setEstado('Eliminado');
            $objSpcMcAfeeCcid->setUsrUltMod($strUsrCreacion);
            $objSpcMcAfeeCcid->setFeUltMod(new \DateTime('now'));
            $this->emComercial->persist($objSpcMcAfeeCcid);
            $this->emComercial->flush();
            $strValoresCaract .= "CUSTOMERCONTEXTID: ".$objSpcMcAfeeCcid->getValor()."<br>";
        }
        $objSpcMcAfeeReferencia = $this->serviceTecnico
                                       ->getServicioProductoCaracteristica($objServicio,
                                                                           "REFERENCIA",
                                                                           $objProducto);
        if(is_object($objSpcMcAfeeReferencia))
        {
            $objSpcMcAfeeReferencia->setEstado('Eliminado');
            $objSpcMcAfeeReferencia->setUsrUltMod($strUsrCreacion);
            $objSpcMcAfeeReferencia->setFeUltMod(new \DateTime('now'));
            $this->emComercial->persist($objSpcMcAfeeReferencia);
            $this->emComercial->flush();
            $strValoresCaract .= "REFERENCIA: ".$objSpcMcAfeeReferencia->getValor()."<br>";
        }
        
        if(!empty($strValoresCaract) && isset($arrayParametros['strProcesoEjecuta']) && !empty($arrayParametros['strProcesoEjecuta']))
        {
            $strValoresCaract = "PROCESO: ".$arrayParametros['strProcesoEjecuta']."<br>".$strValoresCaract;
        }
        return $strValoresCaract;
    }
}

