<?php

namespace telconet\financieroBundle\Service;

use telconet\schemaBundle\Entity\InfoDocumentoHistorial;
use telconet\schemaBundle\Entity\InfoDocumentoFinancieroCab;
use Symfony\Component\HttpKernel\KernelEvents;
use telconet\schemaBundle\Entity\InfoDocumentoCaracteristica;

class InfoDocumentoFinancieroCabService
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emcom;
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emfinan;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emGeneral;
    
    private $utilService;

    private $serviceEnvioPlantilla;
    
    private $emComunicacion;

    private $emSoporte;

    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
        $this->emcom                 = $container->get('doctrine.orm.telconet_entity_manager');
        $this->emGeneral             = $container->get('doctrine.orm.telconet_general_entity_manager');
        $this->emfinan               = $container->get('doctrine.orm.telconet_financiero_entity_manager');
        $this->emComunicacion        = $container->get('doctrine.orm.telconet_comunicacion_entity_manager');
        $this->utilService           = $container->get('schema.Util');
        $this->serviceEnvioPlantilla = $container->get('soporte.EnvioPlantilla');
        $this->emSoporte             = $container->get('doctrine.orm.telconet_soporte_entity_manager');
    }


    /**
     * Documentación para el método 'restarFechas'
     * 
     * Método que realiza la resta entre dos fechas y retorna la cantidad de días.
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 03-07-2017
     * 
     * @param array $arrayParametros['strFechaInicio' => 'Fecha Inicial', 
     *                               'strFechaFin'     => 'Fecha Final']
     * 
     * @return array $arrayResultados['intCantidadDiasEntreFechas' => 'Cantidad de días transcurridos entre las dos fechas']
     */
    public function restarFechas( $arrayParametros )
    {
        $arrayResultados = array('intCantidadDiasEntreFechas' => 0);
        $strFechaInicio  = ( isset($arrayParametros['strFechaInicio']) && !empty($arrayParametros['strFechaInicio']) )
                            ? $arrayParametros['strFechaInicio'] : '';
        $strFechaFin     = ( isset($arrayParametros['strFechaFin']) && !empty($arrayParametros['strFechaInicio']) )
                            ? $arrayParametros['strFechaFin'] : '';

        try
        {
            if ( !empty($strFechaInicio) && !empty($strFechaFin) )
            {
                $intTimeFechaFin    = strtotime($strFechaFin);
                $intTimeFechaInicio = strtotime($strFechaInicio);

                $intDiffSegundos = $intTimeFechaFin - $intTimeFechaInicio;

                //Convierto Segundos en días 
                $intDiffDias = $intDiffSegundos / 60 / 60 / 24; 

                //Obtengo el valor absoulto de los días (quito el posible signo negativo) 
                $intDiffDias = abs($intDiffDias);

                //quito los decimales a los días de diferencia 
                $intDiffDias = floor($intDiffDias);
                
                $intDiffDias++;
                
                $arrayResultados['intCantidadDiasEntreFechas'] = $intDiffDias;
            }//( !empty($strFechaInicio) && !empty($strFechaFin) )
            else
            {
                throw new \Exception('No se han enviado los parámetros adecuados para realizar la resta respectiva entre dos fechas');
            }// ( !empty($strFechaInicio) && !empty($strFechaFin) )
        }
        catch ( \Exception $e)
        {
            throw ($e);
        }

        return $arrayResultados; 
    }


    /**
     * Documentacion de funcion verificarClienteCompensado
     * 
     * Se verifica si el cliente debe ser compensado
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 27-09-2016
     * 
     * @param array    $arrayParametros[ 'intIdPersonaEmpresaRol', 'intIdOficina', 'strEmpresaCod', 'intIdSectorPunto', 'intIdPuntoFacturacion']
     * @return string  $strEsCompensado
     */ 
    public function verificarClienteCompensado($arrayParametros)
    {
        $strEsCompensado = 'N';
        
        if( !empty($arrayParametros) )
        {
            $strUsrCreacion = ( isset($arrayParametros["strUsrCreacion"]) ? ( !empty($arrayParametros["strUsrCreacion"]) 
                                ? $arrayParametros["strUsrCreacion"] : "telcos" ) : "telcos" );
            $strIpCreacion  = ( isset($arrayParametros["strIpCreacion"]) ? ( !empty($arrayParametros["strIpCreacion"]) 
                                ? $arrayParametros["strIpCreacion"] : "127.0.0.1" ) : "127.0.0.1" );

            try
            {
                $strEsCompensado = $this->emfinan->getRepository("schemaBundle:InfoDocumentoFinancieroCab")->getClienteCompensado($arrayParametros);
            }
            catch(\Exception $ex)
            {
                $this->utilService->insertError( 'Telcos+', 
                                                 'verificarClienteCompensado', 
                                                 $ex->getMessage(), 
                                                 $strUsrCreacion, 
                                                 $strIpCreacion );
            }
        }//( !empty($arrayParametros) )
        
        return $strEsCompensado;
    }
    
    
    /**
     * Documentacion de funcion obtenerHistorialDocumento
     * Obtiene el historial de un documento
     * @param integer $intIdDocumento
     * @since 1.0
     * @author Andres Montero <amontero@telconet.ec>
     * @version 1.0 2016-07-12
     * @return Array $arrayHistorial - retorna arreglo con informacion del historial
     */    
    public function obtenerHistorialDocumento($intIdDocumento)
    {   
        $entityInfoDocumentoFinancieroCab   = $this->emfinan->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($intIdDocumento);      
        
        $entityHistorial = $this->emfinan->getRepository('schemaBundle:InfoDocumentoHistorial')
                                         ->findBy(array('documentoId' => $entityInfoDocumentoFinancieroCab->getId()),array('feCreacion' => 'asc')); 
        $arrayHistorial = array();
        if($entityHistorial)
        {
            $intIndice = 0;
            
            foreach($entityHistorial as $objHistorial)
            {
                if($objHistorial->getMotivoId() != null)
                {
                    $entityMotivo = $this->emcom->getRepository('schemaBundle:AdmiMotivo')->find($objHistorial->getMotivoId());

                    if($entityMotivo)
                    {
                        $strNombreMotivo = $entityMotivo->getNombreMotivo();
                    }
                    else
                    {
                        $strNombreMotivo = "";
                    }
                }
                else
                {
                    $strNombreMotivo = "";
                }
                $arrayHistorial[$intIndice]['motivo']       = $strNombreMotivo;
                $arrayHistorial[$intIndice]['estado']       = $objHistorial->getEstado();
                $arrayHistorial[$intIndice]['fe_creacion']  = strval(date_format($objHistorial->getFeCreacion(), "d/m/Y G:i"));
                $arrayHistorial[$intIndice]['usr_creacion'] = $objHistorial->getUsrCreacion();
                $arrayHistorial[$intIndice]['observacion']  = $objHistorial->getObservacion();

                $intIndice++;
            }
        }   
        return $arrayHistorial;
    }
    
    /**
     * Método que envía notificación al cliente, usuario en sesión, vendedor (servicio o punto), alias asignado.
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0
     * @since 23-04-2018
     */
    public function notificaDocumentoAnulado($arrayParametros)
    {
        //Recupero los parametros
        $objInfoDocFinCab        = $arrayParametros["objInfoDocFinCab"];
        $intIdEmpleado           = $arrayParametros["intIdEmpleado"];
        $intIdEmpresa            = $arrayParametros["intIdEmpresa"];
        $emFinanciero            = $this->emfinan;
        $objInfPersonaFCRepos    = $emFinanciero->getRepository("schemaBundle:InfoPersonaFormaContacto");
        $objInfoDocFinanCabRepos = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab');

        //Obtengo todos los destinatarios y valores necesarios para la notificación
        $arrayRespuesta      = $emFinanciero->getRepository('schemaBundle:InfoPuntoDatoAdicional')
                                  ->getMailTelefonoByPunto(array("intIdPunto" => $objInfoDocFinCab->getPuntoId(),
                                                                 "strTipoDato"=> "MAIL"));
        $strDestinatario     = $arrayRespuesta["strMailFono"];
        $objInfoCompElecRep  = $emFinanciero->getRepository('schemaBundle:InfoComprobanteElectronico');
        $arrayInfoCompElec   = $objInfoCompElecRep->findBy(array("documentoId" => $objInfoDocFinCab->getId()));
        $arrayEmpresaRemite  = $objInfoCompElecRep->obtieneDatosEmpresaComprobantes(
                                                        array("strRuc" => $arrayInfoCompElec[0]->getRuc()));
        $objInfoPuntoRepos   = $emFinanciero->getRepository('schemaBundle:InfoPunto');
        $arrayTitular        = $objInfoPuntoRepos->obtieneTitularPorLogin(array("intPuntoId" => $objInfoDocFinCab->getPuntoId()));
        $arrayDestinatario   = explode(";", $strDestinatario);
        $arrayInfoPersonaFC  = $objInfPersonaFCRepos->getPersonaFormaContactoParaSession($intIdEmpleado, 5);
        $arrayDestinatario[] = $arrayInfoPersonaFC["valor"];
        //Obtengo el vendedor.
        $objInfoPunto        = $objInfoPuntoRepos->find($objInfoDocFinCab->getPuntoId());
        //Obtengo la forma de pago.
        $arrayVendedores     = $emFinanciero->getRepository("schemaBundle:InfoServicio")
                                  ->obtieneVendedorPorDocumentoCab(array("intDocFinanCabId"   => $objInfoDocFinCab->getId(),
                                                                         "intFormaContactoId" => 5,
                                                                         "strEstado"          => "Activo"));
        //Recorro y obtengo los correos de el o los vendedores
        $strVendedores = "";
        foreach($arrayVendedores as $strValor)
        {
            if(isset($strValor["FORMA_CONTACTO"]))
            {
                $strVendedores = $strVendedores . $strValor["FORMA_CONTACTO"] . ";";
            }
        }
        //Si no obtengo correos de vendedores, busco el correos del vendedor del punto
        if(is_null($strVendedores) || $strVendedores == "")
        {
            $strVendedor         = $objInfoPunto->getUsrVendedor();
            $objInfoPersona      = $emFinanciero->getRepository("schemaBundle:InfoPersona")->findBy(array("login" => $strVendedor));
            $intEmpleadoId       = $objInfoPersona[0]->getId();
            $arrayInfoPersonaFC  = $objInfPersonaFCRepos->getPersonaFormaContactoParaSession($intEmpleadoId, 5);
            $arrayDestinatario[] = $arrayInfoPersonaFC["valor"];
        }
        else
        {
            $arrayDestinatario = array_merge($arrayDestinatario, explode(";", $strVendedores));
        }
        $arrayResultado      = $objInfoDocFinanCabRepos->getFormaPagoCliente(
                                    array("intIdPunto"     => $objInfoDocFinCab->getPuntoId(),
                                          "intIdPersonaRol" => $objInfoPunto->getPersonaEmpresaRolId()->getId()));
        
        //Se realiza la notificación por plantilla y alias.
        $this->serviceEnvioPlantilla->generarEnvioPlantilla($arrayEmpresaRemite[0]["NOMBRE_EMPRESA"].' acaba de anular su factura',
                                                            $arrayDestinatario,
                                                            'ANULAR-FACTURA', 
                                                            array("objInfoDocFinCab"   => $objInfoDocFinCab,
                                                                  "strRazonSocial"     => $arrayTitular[0]["TITULAR"],
                                                                  "strEmpresa"         => $arrayEmpresaRemite[0]["NOMBRE_EMPRESA"],
                                                                  "strNumAutorizacion" => $arrayInfoCompElec[0]->getClaveAcceso(),
                                                                  "strFormaPago"       => $arrayResultado["strDescripcionFormaPago"]
                                                            ),
                                                            strval($intIdEmpresa),
                                                            '',
                                                            '',
                                                            null,
                                                            true,
                                                            $arrayEmpresaRemite[0]["CORREO_NOTIFICACION"]);
    }

    /**
     * Función que obtiene las facturas por contrato físico y digital.<br>
     * Sólo aplica la empresa que esté parametrizada.<br>
     * Se obtienen facturas según el parámetro SOLICITUDES_DE_CONTRATO.<br>
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0
     * @since 24-10-2018
     * @return array "strEstado"          =>  El estado de la consulta.<br>
     *               "strMensaje"         =>  El mensaje de la consulta.<br>
     *               "arrayListFacturas"  =>  Facturas obtenidas de la consulta ObjInfoDocumentoFinancieroCab.
     */
    public function obtieneFacturasAGenerarNCxEliminarOS($arrayParametros)
    {
        $emGeneralLocal     = $this->emGeneral;
        $serviceUtil        = $this->utilService;
        $emFinanciero       = $this->emfinan;
        $strEmpresaCod      = $arrayParametros["strEmpresaCod"];
        $intIdPunto         = $arrayParametros["intIdPunto"];
        $intIdServicio      = $arrayParametros["intIdServicio"];
        $arrayListFacturas  = array();
        try
        {
            $strAplicaProceso = $serviceUtil->empresaAplicaProceso(array("strProcesoAccion" => "NC_X_ELIMINAR_ORDEN_SERVICIO",
                                                                         "strEmpresaCod"    => $strEmpresaCod));
            if ('S' == $strAplicaProceso)
            {
                $arrayParametros = array("strNombreParametroCab" => "SOLICITUDES_DE_CONTRATO",
                                         "strEstado"             => array("Activo","Inactivo"),
                                         "strEmpresaCod"         => $strEmpresaCod);
                //Obtiene las características de facturas de instalación por contratos MOVIL/WEB según el parámetro.
                $arrayListParams   = $emGeneralLocal->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->findParametrosDet($arrayParametros);
                foreach($arrayListParams["arrayResultado"] as $arrayDetalle)
                {
                    $arrayCaracteristicas[]  = $arrayDetalle["strValor2"];
                }
                $arrayListFacturas = $emFinanciero->getRepository("schemaBundle:InfoDocumentoFinancieroCab")
                                                  ->getFacturasPorContratoFisicoDigital(array('intIdPunto'           => $intIdPunto,
                                                                                              'intIdServicio'        => $intIdServicio,
                                                                                              'arrayInEstados'       => array('Pendiente',
                                                                                                                              'Activo',
                                                                                                                              'Cerrado'),
                                                                                              'arrayTipoDocumento'   => array('FAC','FACP'),
                                                                                              'arrayCaracteristicas' => $arrayCaracteristicas));
            }
            $arrayRespuesta = array("strEstado"         => "OK",
                                    "strMensaje"        => null,
                                    "arrayListFacturas" => $arrayListFacturas);
        }
        catch (\Exception $objException)
        {
            $arrayRespuesta = array("strEstado"         => "ERROR",
                                    "strMensaje"        => $objException->getMessage(),
                                    "arrayListFacturas" => $arrayListFacturas);
        }
        return $arrayRespuesta;
    }

    /**
     * Función que devuelve la bandera si es permitido crear órdenes de trabajo para un punto.
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0
     * @since 07-01-2019
     *
     * Se modifica el parámetro a la función que obtiene si está pagada la última factura de instalación con las siguientes consideraciones:
     * Se obtienen todos los servicios en estado factible del punto y estos son validados contra las facturas.
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.1
     * @since 22-01-2019
     *
     * @param array $arrayParametros ["intPuntoId"]    => El punto a consultar <br>
     *                               ["strEmpresaCod"] => Empresa en sesión <br>
     * @return array ["status"] => Estado del proceso realizado. En caso de éxito = "OK"<br>
     *               ["message"] => Mensaje en caso de existir algún error. En caso éxito = null <br>
     */
    public function aplicaFlujoOrdenTrabajo($arrayParametros)
    {
        $emFinanciero   = $this->emfinan;
        $emComercial    = $this->emcom;
        $objServiceUtil = $this->utilService;
        try
        {

            $arrayRespuesta = array("status" => "OK", "message" => null);
            //Se obtiene si la empresa aplica o no al flujo de facturas de instalación.
            $arrayParametrosAplicaFact       = array("strProcesoAccion" => "FACTURACION_INSTALACION_PUNTOS_ADICIONALES",
                                                     "strEmpresaCod"    => $arrayParametros["strEmpresaCod"]);
            $strAplicaFacturaInstalacion     = $objServiceUtil->empresaAplicaProceso($arrayParametrosAplicaFact);
            //Si la empresa no aplica al flujo de facturas de instalación, es posible presentar el grid de Orden de trabajo.
            if ("N" == $strAplicaFacturaInstalacion)
            {
                return $arrayRespuesta;
            }

            $objInfoPunto       = $emComercial->getRepository("schemaBundle:InfoPunto")->findById($arrayParametros["intPuntoId"]);
            $arrayListServicios = $emComercial->getRepository("schemaBundle:InfoServicio")
                                              ->findBy(array("estado"=> "Factible", "puntoId" => $objInfoPunto));

            $arrayServicios = array();
            foreach ($arrayListServicios as $objServicio)
            {
                $arrayServicios[] = $objServicio->getId();
            }

            if (count($arrayServicios) == 0)
            {
                return $arrayRespuesta;
            }
            $strFactInstPagada = $emFinanciero->getRepository("schemaBundle:InfoDocumentoFinancieroCab")
                                              ->esUltimaFactInstalacionPagada(array("arrayServicios"     => $arrayServicios,
                                                                                    "strNombreParametro" => "SOLICITUDES_DE_CONTRATO",
                                                                                    "arrayEstadosFact"   => array("Pendiente","Activo","Cerrado")));
            if ("S" == $strFactInstPagada)
            {
                return $arrayRespuesta;
            }

            return array("status" => "ERROR", "message" => "No es posible Preplanificar un servicio si no se ha " .
                                                           " realizado el pago de su factura de instalación.");
        }
        catch (\Exception $objException)
        {
            $objServiceUtil->insertError('Telcos+', 
                                         'aplicaFlujoOrdenTrabajo', 
                                         $objException->getMessage() . " PuntoId: " . $arrayParametros["intPuntoId"],
                                         $arrayParametros["strUsrCreacion"], 
                                         $arrayParametros["strIpCreacion"]);
            return array("status" => "ERROR", "message" => "Ocurrió un error inesperado al validar las facturas de instalación del punto.");
        }
    }

    /**
     * Se obtienen las deudas del cliente.
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0
     * @since 22-01-2019
     *
     */
    public function obtieneDeudasCliente($arrayParametros)
    {
        $objServiceUtil = $this->utilService;
        $emFinanciero   = $this->emfinan;
        $emComercial    = $this->emcom;
        $arrayRespuesta = array("status" => "OK", "message" => null);
        //Se obtiene si la empresa aplica o no a la presentación de deudas de los puntos.
        $arrayParametrosAplicaFact       = array("strProcesoAccion" => "MOSTRAR_DEUDAS_ORDEN_TRABAJO",
                                                 "strEmpresaCod"    => $arrayParametros["strEmpresaCod"]);
        $strAplicaFacturaInstalacion     = $objServiceUtil->empresaAplicaProceso($arrayParametrosAplicaFact);
        //Si la empresa no aplica al flujo.
        if ("N" == $strAplicaFacturaInstalacion)
        {
            $arrayRespuesta["arrayPuntosDeuda"]      = null;
            $arrayRespuesta["strMensajeObservacion"] = null;
            return $arrayRespuesta;
        }

        $arrayListObjInfoPunto = $emComercial->getRepository('schemaBundle:InfoPunto')
                                             ->findByPersonaEmpresaRolId($arrayParametros["intIdPersonaEmpresaRol"]);
        $intValor              = 0;
        $arrayDeudas           = array();
        $strMensajeObservacion = "";
        foreach ($arrayListObjInfoPunto as $objInfoPunto)
        {
            $arraySaldo = $emFinanciero->getRepository('schemaBundle:InfoPagoCab')->obtieneSaldoPorPunto($objInfoPunto->getId());
            if ($arraySaldo[0]['saldo'] > 0)
            {
                $intValor       = $intValor + $arraySaldo[0]['saldo'];
                $arrayDeudas[]  = array("login" => $objInfoPunto->getLogin(),
                                        "deuda" => $arraySaldo[0]['saldo']);

                //Mensaje de observación en caso que el usuario preplanifique un punto con deuda.
                $strMensajeObservacion .= " [Login:" . $objInfoPunto->getLogin() . " , Saldo:$" . $arraySaldo[0]['saldo'] . "]";
            }
        }

        $arrayRespuesta["arrayPuntosDeuda"]      = $arrayDeudas;
        $arrayRespuesta["strMensajeObservacion"] = $strMensajeObservacion ? "El cliente tiene deuda en uno o más puntos:" . $strMensajeObservacion :
                                                                            $strMensajeObservacion;
        return $arrayRespuesta;
    }

    /**
     * guardaResponsableAnulacionFac, guarda los parametros de Responsables de la ventana procesar anulacion en la info_caracteristica
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.0 05-10-2016
     * @param type $arrayParametros Recibe los parametros para realizar el proceso de guardado en torno la info_caracteristica
     * @return string   Retorna true en caso de existo, caso contrario retorna false.
     */
    public function guardaResponsableAnulacionFac($arrayParametros)
    {
        try
        {
            $boolStatus = false;

            if(!empty($arrayParametros["facturaId"]))
            {
                $entityInfoDocumentoFinancieroCab = $this->emfinan->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                         ->find($arrayParametros["facturaId"]);

                if($arrayParametros["strTipoResponsable"] == 'Cliente' || $arrayParametros["strTipoResponsable"] == 'Empresa')
                {
                    $objAdmiCaracteristicaTipoResponsable = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                                 ->findOneBy(array('estado'                     => 'Activo',
                                                                                   'descripcionCaracteristica'  => 'TIPO_RESPONSABLE_FAC'));

                    $objAdmiCaracteristicaResponsable = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                             ->findOneBy(array('estado'                     => 'Activo',
                                                                               'descripcionCaracteristica'  => 'RESPONSABLE_FAC'));

                    if(($objAdmiCaracteristicaTipoResponsable) && ($objAdmiCaracteristicaResponsable))
                    {
                        $objInfoDocumentoCaracteristicaTipo = new InfoDocumentoCaracteristica();
                        $objInfoDocumentoCaracteristicaTipo->setCaracteristicaId($objAdmiCaracteristicaTipoResponsable->getId());
                        $objInfoDocumentoCaracteristicaTipo->setDocumentoId($entityInfoDocumentoFinancieroCab);
                        $objInfoDocumentoCaracteristicaTipo->setEstado('Activo');
                        $objInfoDocumentoCaracteristicaTipo->setFeCreacion(new \DateTime('now'));
                        $objInfoDocumentoCaracteristicaTipo->setIpCreacion($arrayParametros["strIpCreacion"]);
                        $objInfoDocumentoCaracteristicaTipo->setUsrCreacion($arrayParametros["user"]);
                        $objInfoDocumentoCaracteristicaTipo->setValor($arrayParametros["strTipoResponsable"]);
                        $this->emfinan->persist($objInfoDocumentoCaracteristicaTipo);
                        $this->emfinan->flush();

                        $objInfoDocumentoCaracteristicaResponsable = new InfoDocumentoCaracteristica();
                        $objInfoDocumentoCaracteristicaResponsable->setCaracteristicaId($objAdmiCaracteristicaResponsable->getId());
                        $objInfoDocumentoCaracteristicaResponsable->setDocumentoId($entityInfoDocumentoFinancieroCab);
                        $objInfoDocumentoCaracteristicaResponsable->setEstado('Activo');
                        $objInfoDocumentoCaracteristicaResponsable->setFeCreacion(new \DateTime('now'));
                        $objInfoDocumentoCaracteristicaResponsable->setIpCreacion($arrayParametros["strIpCreacion"]);
                        $objInfoDocumentoCaracteristicaResponsable->setUsrCreacion($arrayParametros["user"]);

                        if($arrayParametros["strTipoResponsable"] == 'Cliente')
                        {
                            $objInfoDocumentoCaracteristicaResponsable->setValor($arrayParametros["strClienteResponsable"]);
                        }
                        else
                        {
                            $objInfoDocumentoCaracteristicaResponsable->setValor($arrayParametros["strEmpresaResponsable"]);
                        }

                        $this->emfinan->persist($objInfoDocumentoCaracteristicaResponsable);
                        $this->emfinan->flush();
                        $boolStatus = true;
                    }
                    else
                    {
                        $boolStatus = false;
                    }
                }//( $arrayParametros["strTipoResponsable"] == 'Cliente' || $arrayParametros["strTipoResponsable"] == 'Empresa'  )
            }//(!empty($arrayParametros["facturaId"]))
        }
        catch(\Exception $ex)
        {
            $boolStatus = false;

            $this->utilService->insertError('Telcos+', 
                                            'guardaParamRespAnulacionNC', 
                                            $ex->getMessage(), 
                                            $arrayParametros["user"], 
                                            $arrayParametros["strIpCreacion"]);
        }
        return $boolStatus;
    }//guardaResponsableAnulacionFac
    
    /**
     * getPtosValoresFacturarByContratoId
     *     
     * Método que retorna un array con los puntos y los valores a facturar por instalación y promociones.
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 30-08-2019     
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.1 26-06-2020 Se inicializa array para escenario cuando no existen valores a facturar.
     * 
     * @version 1.2  14-02-2022 Se agrega envío de parametro.strProcesoFacturacion (S-N) para indicar que proceso invoca la consulta de valores a 
     *                          facturar e insertar un log en la tabla DB_GENERAL.INFO_ERROR.
     *  
     * @param   $arrayParametros   [intIdContrato, strCodEmpresa,intFormaPagoId,intTipoCuentaId,intBancoTipoCuentaId]
     * @return  $arrayResultadoJson[intTotal, arrayPtos]
     *
     */
    public function getPtosValoresFacturarByContratoId($arrayParametros)
    {    
        $arrayPuntosValores     = array();
        $arrayPuntosValoresOrig = array();
        $intIdContrato          = $arrayParametros['intIdContrato'];
        $intFormaPagoId         = $arrayParametros['intFormaPagoId'];
        $intTipoCuentaId        = $arrayParametros['intTipoCuentaId'];
        $intBancoTipoCuentaId   = $arrayParametros['intBancoTipoCuentaId'];
        $strProcesoFacturacion  = $arrayParametros['strProcesoFacturacion'];
        
        $boolGeneraFactura      = true;
        try
        {        
            $objInfoContrato            = $this->emcom->getRepository('schemaBundle:InfoContrato')->find($intIdContrato);
            $arrayPuntosValoresFacturar = array();
            if(is_object($objInfoContrato))
            {
                $arrayParametros['idper']      = $objInfoContrato->getPersonaEmpresaRolId()->getId();
                $arrayResultado  = $this->emcom->getRepository('schemaBundle:InfoPunto')->getResultadoFindPtosPorPersonaEmpresaRol($arrayParametros);
                $arrayPuntos     = $arrayResultado['registros'];
                $intTotal        = $arrayResultado['total'];

                foreach($arrayPuntos as  $arrayPunto)
                {
                    $floatValorInst   = str_pad(' ', 30);
                    $strOrigenPto     = str_pad(' ', 30);
                    
                    $arrayServicio = $this->emcom->getRepository('schemaBundle:InfoServicio')
                                                 ->getServicioPreferenciaByPunto(['intIdPunto' => $arrayPunto['id']]);

                    $intIdServicioInternet  = $arrayServicio[0]['ID_SERVICIO'];

                    $intMesesActivo  = str_pad(' ', 30);
                    $strSqlMesesAct  = "BEGIN :intMesesActivo := DB_FINANCIERO.FNCK_CAMBIO_FORMA_PAGO.F_GET_MESES_ACTIVO(:Fn_IdServicio); END;";
                    $objStmtMesesAct = $this->emfinan->getConnection()->prepare($strSqlMesesAct);
                    $objStmtMesesAct->bindParam('Fn_IdServicio' , $intIdServicioInternet);
                    $objStmtMesesAct->bindParam('intMesesActivo' , $intMesesActivo);
                    $objStmtMesesAct->execute(); 
               
                    
                    $intPermanenciaMinima  = str_pad(' ', 30);
                    $strSqlPermanenciaMinima  = "BEGIN :intPermanenciaMinima := DB_FINANCIERO.FNCK_CANCELACION_VOL"
                                                                            . ".F_GET_PERMANENCIA_VIGENTE(:Fv_EmpresaCod,:Fn_IdPunto); END;";
                    $objStmtPermanenciaMinima = $this->emfinan->getConnection()->prepare($strSqlPermanenciaMinima);
                    $objStmtPermanenciaMinima->bindParam('Fv_EmpresaCod' , $arrayParametros['strEmpresaCod']);
                    $objStmtPermanenciaMinima->bindParam('Fn_IdPunto' , $arrayPunto['id']);
                    $objStmtPermanenciaMinima->bindParam('intPermanenciaMinima' , $intPermanenciaMinima);
                    $objStmtPermanenciaMinima->execute();
                    if($intMesesActivo > intval($intPermanenciaMinima))
                    {
                        $boolGeneraFactura = false;
                    }                   
                   
                    if( !$boolGeneraFactura || !isset($intIdServicioInternet))
                    {
                        $arrayDet = array('intIdPto' => intval($arrayPunto['id']),'strLogin' => $arrayPunto['login'],
                                           'floatValorInst'=> 0,'floatSubtotal'=> 0,'strOrigen' => '');
                        if(!in_array($arrayDet,$arrayPuntosValoresFacturar))
                        {
                            $arrayPuntosValoresFacturar[] = $arrayDet;
                        }
                    }
                    else
                    {
                        if(!empty($arrayServicio) && $intIdServicioInternet>0)
                        {                      
                            $strSql   = "BEGIN :floatValorInst := DB_FINANCIERO.FNCK_CAMBIO_FORMA_PAGO.F_GET_VALOR_INST_PROMO(:Fv_EmpresaCod,"
                                      . ":Fn_IdPunto,:Fn_IdServicio,:Fn_IdContrato,:Fn_FormaPagoId,:Fn_TipoCuentaId,:Fn_BancoTipoCuentaId); END;";
                            $objStmt = $this->emfinan->getConnection()->prepare($strSql);              
                            $objStmt->bindParam('Fv_EmpresaCod' , $arrayParametros['strEmpresaCod']);
                            $objStmt->bindParam('Fn_IdPunto' , $arrayPunto['id']);
                            $objStmt->bindParam('Fn_IdServicio' , $intIdServicioInternet);
                            $objStmt->bindParam('Fn_IdContrato' , $intIdContrato);
                            $objStmt->bindParam('Fn_FormaPagoId' , $intFormaPagoId);
                            $objStmt->bindParam('Fn_TipoCuentaId' , $intTipoCuentaId);
                            $objStmt->bindParam('Fn_BancoTipoCuentaId' , $intBancoTipoCuentaId);                       
                            $objStmt->bindParam('floatValorInst' , $floatValorInst);
                            $objStmt->execute();

                            $strSqlOrigenPto = "BEGIN :strOrigenPto:=DB_FINANCIERO.FNCK_CAMBIO_FORMA_PAGO.F_GET_ORIGEN_TRASL_CRS(:Fn_IdPunto);END;";
                            $objStmtOrigenPto = $this->emfinan->getConnection()->prepare($strSqlOrigenPto);
                            $objStmtOrigenPto->bindParam('Fn_IdPunto' , $arrayPunto['id']);
                            $objStmtOrigenPto->bindParam('strOrigenPto' , $strOrigenPto);
                            $objStmtOrigenPto->execute();

                            $arrayOrigenPto = explode('|',$strOrigenPto);
                            
                            if(!empty($arrayOrigenPto))
                            {
                                $strOrigen      = $arrayOrigenPto[0];
                                $intIdPtoOrigen = intval($arrayOrigenPto[1]);

                                if($strOrigen==='Traslado' || $strOrigen==='CRS')
                                {
                                    $objInfoPuntoOrigen   = $this->emfinan->getRepository('schemaBundle:InfoPunto')->find($intIdPtoOrigen);
                                    if(is_object($objInfoPuntoOrigen))
                                    {
                                        $strLoginPtoOrigen = $objInfoPuntoOrigen->getLogin();
                                    }
                                    $floatValorInstOrigen = 0;
                                    $arrayPuntosValoresOrig[] = array(
                                        'intIdPto'           => $intIdPtoOrigen,
                                        'strLogin'           => $strLoginPtoOrigen,
                                        'strOrigen'          => '',
                                        'floatValorInst'     => $floatValorInstOrigen,
                                        'floatSubtotal'      => floatval($floatValorInstOrigen) );
                                }
                                
                            }
                            
                            if($arrayPunto['estado'] != 'Anulado')
                            {
                                $arrayPuntosValoresFacturar[] = array(
                                    'intIdPto'           => intval($arrayPunto['id']),
                                    'strLogin'           => $arrayPunto['login'],
                                    'strOrigen'          => $strOrigen,
                                    'floatValorInst'     => floatval($floatValorInst),
                                    'floatSubtotal'      => floatval($floatValorInst)                                   
                                );
                            }
                            $arrayPuntosValoresFacturar = array_merge($arrayPuntosValoresFacturar,$arrayPuntosValoresOrig);
                            $arrayPuntosValoresOrig     = array();                           
                            
                        }
                    }
                    if(!empty($arrayServicio) && $intIdServicioInternet>0 && isset($strProcesoFacturacion) && $strProcesoFacturacion === 'S')
                    {
                        $intPto                   = $arrayPunto['id'];
                        $floatPorcFormaPagOrigen  = str_pad(' ', 30);
                        $floatPorcFormaPagDestino = str_pad(' ', 30);
                        $floatPorcDescuentoInst   = str_pad(' ', 30);

                        $strSql   = "BEGIN  DB_FINANCIERO.FNCK_CAMBIO_FORMA_PAGO.P_GET_PORCENTAJE_DCTO_INST(:Pv_EmpresaCod,"
                                  . ":Pn_IdPunto,:Pn_IdServicio,:Pn_IdContrato,:Pn_FormaPagoId,:Pn_TipoCuentaId,:Pn_BancoTipoCuentaId,"
                                  . ":Pn_PorcFormaPagOrigen,:Pn_PorcFormaPagDestino,:Pn_PorcDescuentoInst); END;";

                        $objStmt = $this->emfinan->getConnection()->prepare($strSql);

                        $objStmt->bindParam('Pv_EmpresaCod' , $arrayParametros['strEmpresaCod']);
                        $objStmt->bindParam('Pn_IdPunto' , $intPto);
                        $objStmt->bindParam('Pn_IdServicio' , $intIdServicioInternet);
                        $objStmt->bindParam('Pn_IdContrato' , $intIdContrato);
                        $objStmt->bindParam('Pn_FormaPagoId' , $intFormaPagoId);
                        $objStmt->bindParam('Pn_TipoCuentaId' , $intTipoCuentaId);
                        $objStmt->bindParam('Pn_BancoTipoCuentaId' , $intBancoTipoCuentaId);                       
                        $objStmt->bindParam('Pn_PorcFormaPagOrigen' , $floatPorcFormaPagOrigen);
                        $objStmt->bindParam('Pn_PorcFormaPagDestino' , $floatPorcFormaPagDestino);
                        $objStmt->bindParam('Pn_PorcDescuentoInst' , $floatPorcDescuentoInst);                               
                        $objStmt->execute();  

                        if(!isset($floatValorInst))
                        {
                            $floatValorInst = 0;   
                        }
                        if(!isset($floatPorcFormaPagOrigen))
                        {
                            $floatPorcFormaPagOrigen='';
                        }
                        if(!isset($floatPorcFormaPagDestino))
                        {
                            $floatPorcFormaPagDestino='';
                        } 
                        if(!isset($floatPorcDescuentoInst))
                        {
                            $floatPorcDescuentoInst='';
                        }                          
                        $strObservacionLog = 'Se ejecuta facturación por cambio de forma de pago : IdContrato - '.$intIdContrato.
                        ', IdPunto - '.$intPto.' IdServicio - '.$intIdServicioInternet.' ValorInstalacion - '.$floatValorInst.
                        ' MesesActivo - '. $intMesesActivo.' PermanenciaMinima - '.$intPermanenciaMinima.
                        ' PorcFormaPagOrigen - '.$floatPorcFormaPagOrigen.' PorcFormaPagDestino - '.$floatPorcFormaPagDestino.
                        ' PorcDescuentoInst - '.$floatPorcDescuentoInst;                         
                        $this->utilService->insertError('Telcos+',
                                                        'InfoDocumentoFinancieroCabService.getPtosValoresFacturarByContratoId',
                                                        $strObservacionLog,
                                                        'telcosCambioFormaPag',
                                                        $arrayParametros['strIpCliente']);
                        
                    }
                }
                if($intTotal > 0)
                {                  
                    $arrayPuntosValores['intTotal']                = $intTotal;
                    $arrayPuntosValores['arrayPtosValoresFacurar'] = $arrayPuntosValoresFacturar;
                }
            }
        }
        catch (\Exception $ex) 
        {
            error_log("Error en InfoDocumentoFinancieroCabService->getPtosValoresFacturarByContratoId: ". $ex->getMessage());
            $arrayPuntosValores['intTotal']                = 0;
            $arrayPuntosValores['arrayPtosValoresFacurar'] = array();
        }        
        return $arrayPuntosValores;
       
    }

    /**
     * sendEmailClienteByParametros
     *     
     * Método que realiza el envio de un mail según los datos enviados como parámetros.
     *
     * @author  Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 17-03-2020
     * @param   $arrayParametros [intIdPuntod, strCodEmpresa,strParametro,strModulo,strCodigoPlantilla]
     * @return  $strRespuesta
     *
     */
    public function sendEmailClienteByParametros($arrayParametros)
    {
        $intIdPunto         = $arrayParametros['intIdPuntod'];
        $strCodEmpresa      = $arrayParametros['strCodEmpresa'];
        $strParametro       = $arrayParametros['strParametro'];//'CAMB_FORMPAG_HEADERS'
        $strModulo          = $arrayParametros['strModulo'];//'FINANCIERO'
        $strCodigoPlantilla = $arrayParametros['strCodigoPlantilla'];
        $strMensaje         = $arrayParametros['strMensaje'];
        $strTipoData        = 'MAIL';
        $strMimeType        = 'text/html; charset=UTF-8';
        $strMsjError        = str_pad(' ', 100);
        $strEmails          = str_pad(' ', 100);
        $strMessage         = str_pad(' ', 100);
        $strRespuesta       = 'OK';
        try
        {
            $strSql = "BEGIN :strEmails:=DB_FINANCIERO.FNCK_COM_ELECTRONICO.GET_ADITIONAL_DATA_BYPUNTO(:Fn_IdPunto, :Fv_TipoData);END;";
            $objStmtOrigenPto = $this->emfinan->getConnection()->prepare($strSql);
            $objStmtOrigenPto->bindParam('Fn_IdPunto' , $intIdPunto);
            $objStmtOrigenPto->bindParam('Fv_TipoData' , $strTipoData);
            $objStmtOrigenPto->bindParam('strEmails' , $strEmails);
            $objStmtOrigenPto->execute();
     
            $objPlantilla = $this->emComunicacion->getRepository('schemaBundle:AdmiPlantilla')
                                                 ->findOneBy(array('estado'  => 'Activo',
                                                                   'codigo'  => $strCodigoPlantilla));
            
            if(is_object($objPlantilla))
            {
                $strPlantilla = $objPlantilla->getPlantilla();
                $strMessage   = str_replace('strMessage',$strMensaje,$strPlantilla);
                $arrayParametrosDet     = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                          ->getOne($strParametro,
                                                                   $strModulo, 
                                                                   '', '', '', '', '', '', '', 
                                                                   $strCodEmpresa);

                if (isset($arrayParametrosDet['valor2']) && isset($arrayParametrosDet['valor3']))
                {
                    $strRemitente    = $arrayParametrosDet['valor2'];
                    $strSubject      = $arrayParametrosDet['valor3'];
                    $strSqlEnvioMail = "BEGIN DB_FINANCIERO.FNCK_CONSULTS."
                                     . "P_SEND_MAIL(:Pv_From,:Pv_To,:Pv_Subject,:Pv_Message,:Pv_MimeType,:Pv_MsnError);END;";
                    $objStmtEnvioMail = $this->emfinan->getConnection()->prepare($strSqlEnvioMail);
                    $objStmtEnvioMail->bindParam('Pv_From' , $strRemitente);
                    $objStmtEnvioMail->bindParam('Pv_To' , $strEmails);
                    $objStmtEnvioMail->bindParam('Pv_Subject' , $strSubject);
                    $objStmtEnvioMail->bindParam('Pv_Message' , $strMessage);
                    $objStmtEnvioMail->bindParam('Pv_MimeType' , $strMimeType);    
                    $objStmtEnvioMail->bindParam('Pv_MsnError' , $strMsjError);            
                    $objStmtEnvioMail->execute();                
                }                
            }
        } 
        catch (Exception $ex) 
        {
            error_log("Error en InfoDocumentoFinancieroCabService->sendEmailByParametros: ". $ex->getMessage());
            $strRespuesta = 'ERROR';

            $this->utilService->insertError('Telcos+', 
                                            'InfoDocumentoFinancieroCabService.sendEmailByParametros', 
                                            $ex->getMessage(), 
                                            'telcos', 
                                            $arrayParametros['strClientIp']);            
        }
        return $strRespuesta;
    }
   /**
     * sendEmailNotificacionClienteByParametros
     *     
     * Método que realiza el envio de un mail según los datos enviados como parámetros,
     * cuando se cambian datos de facturacion del cliente.
     *
     * @author  Adrian Limones <alimonesr@telconet.ec>
     * @version 1.0 30-07-2020
     * @param   $arrayParametros [intIdPuntod, strCodEmpresa,strParametro,strModulo,strCodigoPlantilla,strEmails,intIdPlantilla]
     * @return  $strRespuesta
     */
 public function sendEmailNotificacionClienteByParametros($arrayParametros)
 {
   
   $strCodEmpresa      = $arrayParametros['strCodEmpresa'];
   $strParametro       = $arrayParametros['strParametro'];//'remitente'
   $strModulo          = $arrayParametros['strModulo'];//'FINANCIERO'
   $strCodigoPlantilla = $arrayParametros['strCodigoPlantilla'];
   $intIdPlantilla     = $arrayParametros['intIdPlantilla'];
   $strPlantilla       = $arrayParametros['strPlantilla'];
   $strTipoData        = 'MAIL';
   $strMimeType        = 'text/html; charset=UTF-8';
   $strMsjError        = str_pad(' ', 100);
   $strRespuesta       = 'OK';
   try
   {
     
    $arrayParametrosDet     = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
    ->getOne($strParametro,
             $strModulo, 
             '', '', '', '', '', '', '', 
             $strCodEmpresa,null);
      $arrayAliasPerRechaza      = $this->emComunicacion->getRepository('schemaBundle:AdmiPlantilla')
               ->getAliasXPlantilla($intIdPlantilla,$strCodEmpresa,"","","NO");
          $strAlias = implode (", ", $arrayAliasPerRechaza);
         
           if (isset($arrayParametrosDet['valor1']) && isset($arrayParametrosDet['valor2']))
           {
              
               $strRemitente    = $arrayParametrosDet['valor1'];
               $strSubject      = $arrayParametrosDet['valor2'];
               $strSqlEnvioMail = "BEGIN DB_FINANCIERO.FNCK_CONSULTS."
                                . "P_SEND_MAIL(:Pv_From,:Pv_To,:Pv_Subject,:Pv_Message,:Pv_MimeType,:Pv_MsnError);END;";
               $objStmtEnvioMail = $this->emfinan->getConnection()->prepare($strSqlEnvioMail);
               $objStmtEnvioMail->bindParam('Pv_From' , $strRemitente);
               $objStmtEnvioMail->bindParam('Pv_To' , $strAlias);
               $objStmtEnvioMail->bindParam('Pv_Subject' , $strSubject);
               $objStmtEnvioMail->bindParam('Pv_Message' , $strPlantilla);
               $objStmtEnvioMail->bindParam('Pv_MimeType' , $strMimeType);    
               $objStmtEnvioMail->bindParam('Pv_MsnError' , $strMsjError);            
               $objStmtEnvioMail->execute();                
              
           }                
      
   } 
   catch (Exception $ex) 
   {
       error_log("Error en InfoDocumentoFinancieroCabService->sendEmailByParametros: ". $ex->getMessage());
       $strRespuesta = 'ERROR';

       $this->utilService->insertError('Telcos+', 
                                       'InfoDocumentoFinancieroCabService.sendEmailByParametros', 
                                       $ex->getMessage(), 
                                       'telcos', 
                                       $arrayParametros['strClientIp']);            
   }

   return $strRespuesta;
  
  }  

  /**
   * Documentación para el método 'obtenerDatosFacturaClon'
   * 
   * Metodo que retorna una lista de parametros usados en la pantalla de creacion de prefacturas y prefacturas proporcionales
   * 
   * @author Gustavo Narea <gnarea@telconet.ec>
   * @version 1.0 09-02-2021
   * 
   * @author Gustavo Narea <gnarea@telconet.ec>
   * @version 1.1 04-05-2022 Se modifica para clonar facturas en estado de AdmiParametroCab
   * 
   * @param array $arrayParametros['idFactura'          => 'ID Factura Padre',
   *                               'strIpCreacion'      => 'IP de cliente',
   *                               'strTipoFacturacion' => 'Mensual/Proporcional',
   *                               'intCodEmpresa'=>'Codigo de la empresa',
   *                                'strNombreOficina',
   *                                'strCopiarFechaFrontEnd'=>'Si necesita clonar la fecha para la parte web',
   *                                'arrayRolesPermitidos'=>'Roles que tiene el usuario',
   *                                'ptoCliente',
   *                                'strFacturaElectronico'
   *                                'strNombrePais' ]  
   * 
   * @version 1.2 16-08-2022 Se agrega limpieza trim en la observacion de la factura padre.
   * 
   */
  public function obtenerDatosFacturaClon($arrayParametros)
  {
    $emFinanciero               = $this->emfinan;
    $emComercial                = $this->emcom;
    $strEmpresaCod              = $arrayParametros["intCodEmpresa"];
    
    $strPrefijoEmpresa          = $arrayParametros['strPrefijoEmpresa'];
    $strUsuario                 = $arrayParametros['strUsuario'];
    $strNombreOficina           = $arrayParametros['strNombreOficina'];

    $strCopiarFechaFrontEnd     = $arrayParametros['strCopiarFechaFrontEnd'];
    $arrayRolesPermitidos       = $arrayParametros['arrayRolesPermitidos'];

    $intIdFactura            = $arrayParametros["idFactura"];
    $strIpCreacion           = $arrayParametros["strIpCreacion"];
    $strTipoFacturacion      = $arrayParametros["strTipoFacturacion"];
    $entityInfoDocumentoFinancieroCab   = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                        ->find($intIdFactura);
    $intPuntoId                 = $entityInfoDocumentoFinancieroCab->getPuntoId();
    
    $objPuntoCliente            = $emComercial->getRepository('schemaBundle:InfoPunto')
                                                ->getPuntoParaSession($intPuntoId);
    $arrayCliente               = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                ->getPersonaParaSession($strEmpresaCod, $objPuntoCliente["id_persona"]);
    
    $arrayPtoCliente        = $arrayParametros["ptoCliente"];
    $intIdOficina           = $arrayParametros["idOficina"] ? $arrayParametros["idOficina"] : 0;
    $strFacturaElectronica  = $arrayParametros["strFacturaElectronico"];
    $strPaisSession         = $arrayParametros["strNombrePais"];

    try
    {
        if( strtoupper($strPaisSession) ==  "ECUADOR" )
        {
            $objAdmiImpuestoIva = $this->emGeneral->getRepository('schemaBundle:AdmiImpuesto')
                                            ->findOneBy( array('tipoImpuesto' => 'IVA', 
                                                               'estado'       => 'Activo') );
        }
        else if ( strtoupper($strPaisSession) ==  "GUATEMALA")
        {
            $objAdmiPais = $this->emGeneral->getRepository("schemaBundle:AdmiPais")->findOneById($intIdPaisSession);
            
            if ( is_object($objAdmiPais) )
            {
                $objAdmiImpuestoIva = $this->emGeneral->getRepository('schemaBundle:AdmiImpuesto')
                                                ->findOneBy( array('tipoImpuesto' => 'IVA_GT', 
                                                                   'estado'        => 'Activo',
                                                                   'paisId'        => $objAdmiPais) );
            }
        }
        else
        {
            $objAdmiPais = $this->emGeneral->getRepository("schemaBundle:AdmiPais")->findOneById($intIdPaisSession);
            if ( is_object($objAdmiPais) )
            {
                $objAdmiImpuestoIva = $this->emGeneral->getRepository('schemaBundle:AdmiImpuesto')
                                                ->findOneBy( array('tipoImpuesto' => 'ITBMS', 
                                                                   'estado'       => 'Activo',
                                                                   'paisId'       => $objAdmiPais) );
            }
        }

        if( $objAdmiImpuestoIva != null )
        {
            $arrayParametros['intIdImpuestoIvaActivo'] = $objAdmiImpuestoIva->getId();
        }
        
        
        $arrayParametros["esElectronica"] = $entityInfoDocumentoFinancieroCab->getEsElectronica();
        $arrayParametros['intIdOficina'] = $intIdOficina;

        $arrayOpcionesHabilitadas = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->get('OPCIONES_HABILITADAS_FINANCIERO', 
                                                'FINANCIERO',
                                                'FACTURACION', 
                                                'FAC', 
                                                '',
                                                '',
                                                '', 
                                                '', 
                                                '', 
                                                $strEmpresaCod);
        foreach ( $arrayOpcionesHabilitadas as $arrayOpcion )
        {
            if ( isset($arrayOpcion['valor1']) && !empty($arrayOpcion['valor1']) && isset($arrayOpcion['valor2'])
                    && !empty($arrayOpcion['valor2']) )
            {
                $arrayParametros[$arrayOpcion['valor1']] = $arrayOpcion['valor2'];
            }
        }
        $boolDisableComboOficina = false;
        $boolPuedeFacturar = true;
        $arrayParametros['floatPorcentajeCompensacion']     = 0;
        $arrayParametros['nombre_tipo_negocio']             = "";
        $arrayParametros['nombre_tipo_negocio_no']          = "";
        $arrayParametros['esCompensado']                    = "";
        $arrayParametros['strPrefijoEmpresa']               = $strPrefijoEmpresa; 
        $arrayParametros['OPCIONES_FECHA_CONSUMO']          = "S";
        $arrayParametros['PRECARGADA_SIN_FRECUENCIA']       = "S";
        $arrayParametros['strMuestraObservacion']           = "S";

        $strNombreOficina   = $emComercial->getRepository('schemaBundle:InfoOficinaGrupo')
                                            ->find($entityInfoDocumentoFinancieroCab->getOficinaId()); 
        $intPuntoId         = $entityInfoDocumentoFinancieroCab->getPuntoId();
        $entityPersona                          = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                                ->find($arrayCliente["id_persona"]);
        if(!$arrayCliente["id_persona"])
        {
            throw new \Exception("No se encuentra el Cliente para sesion o el punto no esta en un estado valido");
        }
        $arrayParametros['strClonarFactura']    = "S";
        $arrayParametros['strNombreOficina']    = $strNombreOficina;
        $arrayParametros["facturaCab"]          = $entityInfoDocumentoFinancieroCab;
        $arrayParametros["strPagaIva"]          = $entityPersona->getPagaIva();

        //valida que un login este en sesion
        if($arrayPtoCliente)
        {
            //Como el punto cliente existe se debe verificar si es pto de facturacion
            $entityPuntoAdicional = $emComercial->getRepository('schemaBundle:InfoPuntoDatoAdicional')->findOneByPuntoId($arrayPtoCliente['id']);

            //verifica que la entidad tenga datos
            if( $entityPuntoAdicional && $entityPuntoAdicional->getEsPadreFacturacion() == 'S' )
            {
                //valida que sea el punto sea padre de facturacion
                $arrayParametros['punto_id']          = $arrayPtoCliente;
                $arrayParametros['cliente']           = $arrayCliente;
            }

            $intIdOficinaClienteSession = ( !empty($arrayCliente['id_oficina']) ) ? $arrayCliente['id_oficina'] : 0;
            if( $strPrefijoEmpresa == 'TN' )
            {
                if( $intIdOficina != $intIdOficinaClienteSession )
                {
                    if(!in_array("ROLE_67-4778", $arrayRolesPermitidos))
                    {
                        $boolPuedeFacturar = false;
                    }
                }//( $intIdOficina != $intIdOficinaClienteSession )
                else
                {
                    if(!in_array("ROLE_67-4778", $arrayRolesPermitidos))
                    {
                        $boolDisableComboOficina = true;
                    }                            
                }
            }
            
            $arrayParametros['rolesPermitidos']             = $arrayRolesPermitidos;

            $arrayParametrosService['intIdSectorPunto']       = ( !empty($arrayPtoCliente['id_sector']) ) ? $arrayPtoCliente['id_sector'] : 0;
            $arrayParametrosService['intIdPuntoFacturacion']  = ( !empty($arrayPtoCliente['id']) ) ? $arrayPtoCliente['id'] : 0;
        
        }
       
        $arrayParametrosService                           = array();
        $arrayParametrosService['strUsrSession']          = $strUsrSession;
        $arrayParametrosService['strIpSession']           = $strIpSession;
        $arrayParametrosService['intIdPersonaEmpresaRol'] = ( !empty($arrayCliente['id_persona_empresa_rol']) ) 
                                                            ? $arrayCliente['id_persona_empresa_rol'] : 0;
        $arrayParametrosService['intIdOficina']           = $intIdOficinaClienteSession;
        $arrayParametrosService['strEmpresaCod']          = $strEmpresaCod;
        $arrayParametros['esCompensado']                  =  $this->verificarClienteCompensado($arrayParametrosService);

        $arrayMeses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", 
                        "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
        $arrayDatosFecha ["array_meses"] = $arrayMeses;
        $arrayDatosFecha ["entityInfoDocumentoFinancieroCab"] = $entityInfoDocumentoFinancieroCab;
        
        if($strTipoFacturacion=="Mensual" && $strCopiarFechaFrontEnd=="S")
        {
            $arrayDatosFecha = $this->obtenerFechaFacturaMensual($arrayDatosFecha);
        }
        else if ($strTipoFacturacion=="Proporcional" && $strCopiarFechaFrontEnd=="S")
        {
            $arrayDatosFecha = $this->obtenerFechaFacturaProporcional($arrayDatosFecha);
        }
        else
        {
            $arrayDatosFecha = [];
        }
        
        $arrayParametros = array_merge($arrayDatosFecha, $arrayParametros);

        $arrayParametros["observacion"]     = preg_replace('/\s+/', ' ', trim($entityInfoDocumentoFinancieroCab->getObservacion()));
        $arrayParametros["idfactura"]       = $entityInfoDocumentoFinancieroCab->getId();
        
        $strOficinaDebeCompensar = "N";
        if( $objParametroCab != null )
        {
            $objParametroDet = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->findOneBy( array( 'estado'      => "Activo",
                                                                'parametroId' => $objParametroCab,
                                                                'valor1'      => $strNombreOficinaClienteSession ) );
            if( $objParametroDet != null )
            {
                $strOficinaDebeCompensar = 'S';
            }
        }
        $arrayParametros['strEmpresaCod']                     = $strEmpresaCod;
        $arrayParametros['boolDisableComboOficina']           = $boolDisableComboOficina;
        $arrayParametros["boolPuedeFacturar"]                 = $boolPuedeFacturar;
        $arrayParametros["booleanPresentarMensajeValidacion"] = false;
        
        if($boolPuedeFacturar)
        {
            $arrayParametros = $emFinanciero->getRepository('schemaBundle:InfoPuntoDatoAdicional')
                                                    ->validaContactoFacturacion($arrayParametros);
        }
  
        $arrayParametros['strPrefijoEmpresa'] = $strPrefijoEmpresa;
        $arrayParametros['numero_de_factura']  = "0";
        $arrayParametros ["strOficinaEsCompensado"] = $strOficinaDebeCompensar;
        
        $arrayRetorno["error"] = 0;
        $arrayRetorno["arrayParametros"] = $arrayParametros;
        return $arrayRetorno;
    }
    catch(\Exception $e)
    { 
        $arrayRetorno["error"] = 1;
        $arrayRetorno["mensaje_error"] = $e->getMessage();
        return $arrayRetorno;
    }
  }
   
   /**
    * Documentación para el método 'obtenerFechaFacturaMensual'
    *
    * Metodo que retorna la fecha de una factura para su uso en la pantalla de creacion de facturas mensuales
    *
    * @author Gustavo Narea <gnarea@telconet.ec>
    * @version 1.0 09-02-2021
    * 
    * @param array $arrayParametros['array_meses'                         => 'Lista de meses en String', 
    *                               'entityInfoDocumentoFinancieroCab'    => 'Entidad Cabecera Documento'] 
    */
    public function obtenerFechaFacturaMensual($arrayParametros)
    {
        $emFinanciero            = $this->emfinan;
        $emComercial             = $this->emcom;
        $arrayMeses                 = $arrayParametros["array_meses"];
        $entityFacturaCab           = $arrayParametros["entityInfoDocumentoFinancieroCab"];
        
        $arrayParametrosRetorno["anio_consumo"]    = $entityFacturaCab->getAnioConsumo();
        $arrayParametrosRetorno["mes_consumo"]     = intval($entityFacturaCab->getMesConsumo());
        
        if($entityFacturaCab->getAnioConsumo() != null && $entityFacturaCab->getMesConsumo() != null)
        {
            $arrayParametrosRetorno["fecha_consumo"] = $arrayMeses[$entityFacturaCab->getMesConsumo()-1] . " " .
                                                    $entityFacturaCab->getAnioConsumo();
        }
        else if($entityFacturaCab->getRangoConsumo())
        {
            // Division del Rango de consumo string en fechas consumidas en js
            $arrayParametrosRetorno["fecha_consumo"] = $entityFacturaCab->getRangoConsumo();
            $strFeConsmo = $entityFacturaCab->getRangoConsumo();
            if(strpos($strFeConsmo,"Del")!==false)//Con dia inicial
            {
                $strFe = str_replace("Del ","", $strFeConsmo);
                $arrayFeInicial = explode(" al ", $strFe);
                $arrayFeExp = explode(" ", $arrayFeInicial[0]);
                $arrayFeExpFin = explode(" ", $arrayFeInicial[1]);
            
                foreach($arrayMeses as $key=>$value)
                {
                    if($arrayFeExp[1] == $value)
                    {
                        $intMesInicial = ($key+1);
                        break;
                    }
                }
                $strMesInicialCeros = str_pad($intMesInicial, 2, "0", STR_PAD_LEFT);
                $strDiaInicial = $arrayFeExp[2]."-".$strMesInicialCeros."-".$arrayFeExp[0];
                
                //Fecha final
                foreach($arrayMeses as $key=>$value)
                {
                    if($arrayFeExpFin[1] == $value)
                    {
                        $intMesFinal = ($key+1);
                        break;
                    }
                }
                $strMesFinalCeros = str_pad($intMesFinal, 2, "0", STR_PAD_LEFT);
                $strDiaFinal = $arrayFeExpFin[2]."-".$strMesFinalCeros."-".$arrayFeExpFin[0];
                $arrayParametrosRetorno["diaInicial"] = $strDiaInicial;
                $arrayParametrosRetorno["diaFinal"] = $strDiaFinal;
                
            }
            else
            {
                $strFe                          = str_replace("De ","", $strFeConsmo);
                $strFeInicial                   = explode(" a ", $strFe);
                $arrayFeExp                     = explode(" ", $strFeInicial[0]);
                $arrayFeExpFin                  = explode(" ", $strFeInicial[1]);
                $arrayParametrosRetorno["mesInicial"]  = $arrayFeExp[0].", ".$arrayFeExp[1];
                $arrayParametrosRetorno["mesFinal"]    = $arrayFeExpFin[0].", ".$arrayFeExpFin[1];
            }
        } 
        //Si no existe Fechas en observacion o en mes/anio consumo
        else
        {
            $entityAdmiCaractMes = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneBy( array("descripcionCaracteristica"=>"CICLO_FACTURADO_MES" ) );
            $entityCaracFacMes = $emFinanciero->getRepository('schemaBundle:InfoDocumentoCaracteristica')
                                                ->findOneBy( array("documentoId"=>$entityFacturaCab, 
                                                                "caracteristicaId"=>$entityAdmiCaractMes->getId()) );
            $entityAdmiCaractAnio  = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                ->findOneBy( array("descripcionCaracteristica"=>"CICLO_FACTURADO_ANIO" ) );
            $entityCaracFacAnio = $emFinanciero->getRepository('schemaBundle:InfoDocumentoCaracteristica')
                                                ->findOneBy( array("documentoId"=>$entityFacturaCab, 
                                                                "caracteristicaId"=>$entityAdmiCaractAnio->getId()) );
            if($entityCaracFacMes)
            {
                $strMes = $entityCaracFacMes->getValor();
                $strAnio = $entityCaracFacAnio->getValor();
            }
            //Si no hay data en anio/mes consumo ni rango consumo, ni caracteristicas, 
            //sacamos la fecha de consumo de la fecha de creacion.
            else
            {
                $strMes =  $entityFacturaCab->getFeCreacion()->format('m');
                $strAnio = $entityFacturaCab->getFeCreacion()->format('Y');
            }
            $objDayLast     = date("d", mktime(0,0,0, intval($strMes)+1, 0, intval($strAnio) ));
            
            $strMesFinal                    = $strAnio."-".$strMes."-".$objDayLast;
            $strMesInicial                  = $strAnio."-".$strMes."-"."01";
            $arrayParametrosRetorno["anio_consumo"]    = $strAnio;
            $arrayParametrosRetorno["mes_consumo"]     = intval($strMes);
        }
        return $arrayParametrosRetorno;
    }

   /**
    * Documentación para el método 'obtenerFechaFacturaProporcional'
    *
    * Metodo que retorna la fecha de una factura para su uso en la pantalla de creacion de facturas proporcionales
    *
    * @author Gustavo Narea <gnarea@telconet.ec>
    * @version 1.0 09-02-2021
    * 
    * @param array $arrayParametros['array_meses'                         => 'Lista de meses en String', 
    *                               'entityInfoDocumentoFinancieroCab'    => 'Entidad Cabecera Documento'] 
    */

    public function obtenerFechaFacturaProporcional($arrayParametros)
    {
        $arrayMeses = $arrayParametros["array_meses"];
        $entityFacturaCab = $arrayParametros["entityInfoDocumentoFinancieroCab"];
        $emFinanciero = $this->emfinan;
        $strBanderaFechaConsumo="N";
        //Primero verificamos si tiene un rango de consumo.
        if($entityFacturaCab->getRangoConsumo())
        {
            $strFeConsmo                                = $entityFacturaCab->getRangoConsumo();
        
            if(strpos($strFeConsmo,"Del")!==false)//Con dia inicial
            {
                $strFe = str_replace("Del ","", $strFeConsmo);

                $arrayFeInicial = explode(" al ", $strFe);

                $arrayFeExp = explode(" ", $arrayFeInicial[0]);
                $arrayFeExpFin = explode(" ", $arrayFeInicial[1]);
            
                foreach($arrayMeses as $key=>$value)
                {
                    if($arrayFeExp[1] == $value)
                    {
                        $intMesInicial = ($key+1);
                        break;
                    }
                }
                $strMesInicialCeros = str_pad($intMesInicial, 2, "0", STR_PAD_LEFT);
                $strDiaInicial = $arrayFeExp[2]."-".$strMesInicialCeros."-".$arrayFeExp[0];
                    
                //Fecha final
                foreach($arrayMeses as $key=>$value)
                {
                    if($arrayFeExpFin[1] == $value)
                    {
                        $intMesFinal = ($key+1);
                        break;
                    }
                }
                $strMesFinalCeros                   = str_pad($intMesFinal, 2, "0", STR_PAD_LEFT);
                $strDiaFinal                        = $arrayFeExpFin[2]."-".$strMesFinalCeros."-".$arrayFeExpFin[0];

                $arrayParametrosRetorno["diaInicial"]      = $strDiaInicial;
                $arrayParametrosRetorno["diaFinal"]        = $strDiaFinal;
            }
        }
        //Si no tiene un rango de consumo, verificamos la menor y mayor fecha entre los detalles
        else
        {
            $arrayDetallesFactura = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroDet')
                                                    ->findBy( array("documentoId"=>$entityFacturaCab->getId()) );
            if($arrayDetallesFactura)
            {
                $arrayMinFechas = array();
                $arrayMaxFechas = array();
                foreach($arrayDetallesFactura as $entityDetalleFactura)
                {
                    $strFechaConsumoTmp = $entityDetalleFactura->getObservacionesFacturaDetalle();
                    $strBusquedaDesde = "desde: ";
                    $strBusquedaHasta = " hasta: ";
                    //Obtenemos la menor y la mayor fecha de los detalles
                    
                    if(strpos($strFechaConsumoTmp,$strBusquedaDesde)!==false && strpos($strFechaConsumoTmp,$strBusquedaHasta)!==false)
                    {
                        $intInicioCorteFechaInicial = strpos($strFechaConsumoTmp, $strBusquedaDesde);
                        $strFechaInicio = substr($strFechaConsumoTmp,$intInicioCorteFechaInicial+strlen($strBusquedaDesde),10);
                        $objFechaInicialTmp = \DateTime::createFromFormat('Y-m-d', $strFechaInicio)->format('Y-m-d');
                        //Si la fecha del detalle es menor, guardamos de referencia esa fecha
                        if($objFechaInicialTmp!==false) 
                        {
                            $arrayMinFechas[] = $objFechaInicialTmp;
                        }
                        $intFinCorteFechaInicial = strpos($strFechaConsumoTmp, $strBusquedaHasta);
                        $intInicioCorteFechaFin = $intFinCorteFechaInicial+strlen($strBusquedaHasta);
                        $strFechaFin = substr($strFechaConsumoTmp, $intInicioCorteFechaFin, 10);
                        $objFechaFinalTmp = \DateTime::createFromFormat('Y-m-d', $strFechaFin)->format('Y-m-d');
                        if($objFechaFinalTmp!==false) 
                        {
                            $arrayMaxFechas[] = $objFechaFinalTmp;
                        }
                        $strBanderaFechaConsumo="S";
                    }
                    
                }//fin de for
                //Comprobamos que se pudo extraer fechas de los detalles
                if(empty($arrayMinFechas) || empty($arrayMaxFechas))
                {
                    $strBanderaFechaConsumo="N";
                }
                else
                {
                    $strBanderaFechaConsumo="S";
                    $arrayParametrosRetorno["diaInicial"]      = min($arrayMinFechas);
                    $arrayParametrosRetorno["diaFinal"]        = max($arrayMaxFechas);
                }
            }
            //Si los detalles de consumo no tienen fecha de rango, el tiempo es el mes.
            if($entityFacturaCab->getAnioConsumo() && $strBanderaFechaConsumo=="N")
            {
                $objMonth       = $entityFacturaCab->getMesConsumo();
                $objYear        = $entityFacturaCab->getAnioConsumo();
                $objDayLast     = date("d", mktime(0,0,0, $objMonth+1, 0, $objYear));
                
                $strMesFinal                    = $entityFacturaCab->getAnioConsumo()."-".$entityFacturaCab->getMesConsumo()."-".$objDayLast;
                $strMesInicial                  = $entityFacturaCab->getAnioConsumo()."-".$entityFacturaCab->getMesConsumo()."-"."01";
                
                $arrayParametrosRetorno["diaInicial"]      = $strMesInicial;
                $arrayParametrosRetorno["diaFinal"]        = $strMesFinal;
            }
        }
        return $arrayParametrosRetorno;
    }


    /**
     * Documentación para el método 'clonarCaracteristicasFactura
     * 
     * Funcion que clona la caracteristicas de la factura padre y 
     * añade la caracteristica de clonacion a la factura clonada
     * 
     * @author Gustavo Narea <gnarea@telconet.ec>
     * @version 1.0 25-04-2022
     */
    public function clonarCaracteristicasFactura($arrayParametros)
    {
        try
        {
            $emFinanciero               = $this->emfinan;
            $emComercial                = $this->emcom;
            $entityInfoDocumentoFinCab  = $arrayParametros["entityInfoDocumentoFinCab"];
            $intIdFactura               = $arrayParametros["intIdFactura"];
            $strDescripcionDet          = $arrayParametros["strDescripcionDetCaract"];
            $strEmpresaCod              = $arrayParametros["strEmpresaCod"];
            $strParametroCab            = "CLONACION DE FACTURAS";
            $strModulo                  = "FINANCIERO";
            $arrayParamEntrada = array();
            $arrayParamEntrada["nombreParametro"] = $strParametroCab;
            $arrayParamEntrada["modulo"] = $strModulo;
            $arrayParamEntrada["descripcion"] = $strDescripcionDet;
            $arrayParamEntrada["empresaCod"] = $strEmpresaCod;
            $arrayParamEntrada['strLlave']   = 'valor1';
            if(isset($strDescripcionDet))
            {
                $arrayCaracteristicasClonacion     = array();
                $arrayCaracteristicasClonacion = $this->emSoporte->getRepository("schemaBundle:AdmiParametroDet")
                                                                    ->getArrayParametrosDetalle($arrayParamEntrada);

                foreach($arrayCaracteristicasClonacion as $strCaracteristicaClonacion)
                {
                    if(!is_null($strCaracteristicaClonacion) && $strCaracteristicaClonacion!='')
                    {
                        $entityCaractClonar = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                    ->findOneBy( array("descripcionCaracteristica"=>$strCaracteristicaClonacion ) );
                        if(is_object($entityCaractClonar))
                        {
                            $entityCaracteristicaAclonar = $emFinanciero->getRepository('schemaBundle:InfoDocumentoCaracteristica')
                                                                        ->findOneBy( array("documentoId"=>$intIdFactura, 
                                                                                        "caracteristicaId"=>$entityCaractClonar->getId()) );
                            //Verificamos si existe la caracteristica en la factura nueva, para cambiamos su valor, sino  creamos la caracteristica
                            $entityCaracteristicaClonarNuevaFac = $emFinanciero->getRepository('schemaBundle:InfoDocumentoCaracteristica')
                                                                                ->findOneBy( array("documentoId"=>$entityInfoDocumentoFinCab, 
                                                                                                "caracteristicaId"=>$entityCaractClonar->getId()) );
                            //Si existe la caracteristica en la nueva fact, solo clonamos los valores de la vieja fac
                            $boolClonCaract = is_object($entityCaracteristicaAclonar);
                            if(is_object($entityCaracteristicaClonarNuevaFac) && $boolClonCaract)
                            {
                                $entityCaracteristicaClonarNuevaFac->setValor($entityCaracteristicaAclonar->getValor());
                                $emFinanciero->persist($entityCaracteristicaClonarNuevaFac);
                                $emFinanciero->flush();
                            }//Sino, creamos la caracteristica
                            else if($boolClonCaract)
                            {
                                $entityFacturaCaracteristica = new InfoDocumentoCaracteristica();
                                $entityFacturaCaracteristica->setCaracteristicaId($entityCaractClonar->getId());
                                $entityFacturaCaracteristica->setDocumentoId($entityInfoDocumentoFinCab);
                                $entityFacturaCaracteristica->setEstado('Activo');
                                $entityFacturaCaracteristica->setFeCreacion(new \DateTime('now'));
                                $entityFacturaCaracteristica->setIpCreacion($strIpCreacion);
                                $entityFacturaCaracteristica->setUsrCreacion($strUsuario);
                                $entityFacturaCaracteristica->setValor($entityCaracteristicaAclonar->getValor());
                                $emFinanciero->persist($entityFacturaCaracteristica);
                                $emFinanciero->flush();
                            }
                        }
                    }
                }
                //Caracteristica de Clonacion
                $entityCaractClonacion = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                    ->findOneBy( array("descripcionCaracteristica"=>"CLONACION_FACTURA" ) );
                if(is_object($entityCaractClonacion))
                {
                    $entityFacturaCaracteristicaClon = new InfoDocumentoCaracteristica();
                    $entityFacturaCaracteristicaClon->setCaracteristicaId($entityCaractClonacion->getId());
                    $entityFacturaCaracteristicaClon->setDocumentoId($entityInfoDocumentoFinCab);
                    $entityFacturaCaracteristicaClon->setEstado('Activo');
                    $entityFacturaCaracteristicaClon->setFeCreacion(new \DateTime('now'));
                    $entityFacturaCaracteristicaClon->setIpCreacion($strIpCreacion);
                    $entityFacturaCaracteristicaClon->setUsrCreacion($strUsuario);
                    $entityFacturaCaracteristicaClon->setValor($intIdFactura);
                    $emFinanciero->persist($entityFacturaCaracteristicaClon);
                    $emFinanciero->flush();
                }
                $entityCaractClonacion2 = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                    ->findOneBy( array("descripcionCaracteristica"=>"NUMERO_FACTURA_PADRE" ) );
                if(is_object($entityCaractClonacion2))
                {
                    $entityFacturaCabPadre = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                            ->find($intIdFactura);
                    if($entityFacturaCabPadre && $entityFacturaCabPadre->getNumeroFacturaSri()!= null 
                        && $entityFacturaCabPadre->getNumeroFacturaSri()!='')
                    {
                        $entityFacturaCaracteristicaClon2 = new InfoDocumentoCaracteristica();
                        $entityFacturaCaracteristicaClon2->setCaracteristicaId($entityCaractClonacion2->getId());
                        $entityFacturaCaracteristicaClon2->setDocumentoId($entityInfoDocumentoFinCab);
                        $entityFacturaCaracteristicaClon2->setEstado('Activo');
                        $entityFacturaCaracteristicaClon2->setFeCreacion(new \DateTime('now'));
                        $entityFacturaCaracteristicaClon2->setIpCreacion($strIpCreacion);
                        $entityFacturaCaracteristicaClon2->setUsrCreacion($strUsuario);
                        $entityFacturaCaracteristicaClon2->setValor($entityFacturaCabPadre->getNumeroFacturaSri());
                        $emFinanciero->persist($entityFacturaCaracteristicaClon2);
                        $emFinanciero->flush();
                    }
                }
                if($emFinanciero->getConnection()->isTransactionActive())
                {
                    $emFinanciero->getConnection()->commit();
                    $arrayRetorno["error"] = 0;
                    $arrayRetorno["mensaje_error"] = null;
                    return $arrayRetorno;
                }
            }
        }catch(\Exception $e)
        { 
            $arrayRetorno["error"] = 1;
            $arrayRetorno["mensaje_error"] = $e->getMessage();
            return $arrayRetorno;
        }
    }

    /**
     * Documentación para el método 'clonarFactura
     * 
     * Funcion que verifica los permisos, estados, verificacion de iva14, ice no facturado en producto clonado, compensacion solidaria,
     * caracteristicas y productos en detalle de factura.
     * Retorna las Datos requeridos en la pantalla newFactura
     * 
     * @author Gustavo Narea <gnarea@telconet.ec>
     * @version 1.0 29-04-2022
     */
    public function clonarFactura($arrayParametrosEntrada)
    {
        $intIdDocumento             = $arrayParametrosEntrada["intIdDocumento"];
        $boolPerfilClonacion        = $arrayParametrosEntrada["boolPerfilClonacion"];
        $strTipoFacturacion         = $arrayParametrosEntrada["strTipoFacturacion"];
        $strIpCreacion              = $arrayParametrosEntrada["strIpCreacion"];
        $arrayRolesPermitidos       = $arrayParametrosEntrada["arrayRolesPermitidos"];

        $strUrlReferida             = $arrayParametrosEntrada["strUrlReferida"];
        $strUrlIndexFactura         = $arrayParametrosEntrada["strUrlIndexFactura"];
        $strUrlFacturacionAutomatica = $arrayParametrosEntrada["strUrlFacturacionAutomatica"];

        //Uso de array-sesion en setSessionByIdPunto 
        $intCodEmpresa              = $arrayParametrosEntrada["idEmpresa"];
        $strPrefijoEmpresa          = $arrayParametrosEntrada['prefijoEmpresa'];
        $strUsuario                 = $arrayParametrosEntrada['user'];
        $strNombreOficina           = $arrayParametrosEntrada['oficina'];
        
        $emFinanciero               = $this->emfinan;
        $emComercial                = $this->emcom;
        
        $entityFacturaCab           = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                    ->find($intIdDocumento);
        
        if(!is_object($entityFacturaCab))
        {
            throw new \Exception("Factura no encontrada");
        }
        $arrayDetalleFactura        = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroDet')
                                                    ->findBy(array("documentoId"=>$entityFacturaCab->getId()) );
        $arrayAlertasClonacion      = array();
        $boolNecesitaEliminarPrefactura     = null;
        if(strpos($strUrlReferida,$strUrlFacturacionAutomatica) !== false ||
        strpos($strUrlReferida,$strUrlFacturacionProporcionalAutomatica) !== false)
        {
            $strDescripcionDetEstados           = "ESTADOS_CLONACION_PREFACTURAS";
            $strMensajeEstadosFactura           = "MENSAJE_ESTADOS_PREFACTURA";

            $strDescripcionDetCaract            = "CARACTERISTICAS_CLONACION_PREFACTURAS"; //Caracteristicas a clonar
            $strNecesitaEliminarPrefactura      = "true";
            $strDescripcionDetMostrarFecha      = "MOSTRAR_FECHA_PREFACTURA_PADRE";
            
            $strDescripcionEmpresasPermitidas   = "CHEQUEO_EMPRESA_CLON_PREFACTURAS";
            $strMensajeEmpresasNoPermitidas     = "MENSAJE_CHEQUEO_EMPRESA_CLON_PREFACTURAS";
        }
        else
        {
            //ESTADOS PERMITIDOS DE LA FACTURA (BACK-END)
            $strDescripcionDetEstados           = "ESTADOS_CLONACION_FACTURAS";
            $strMensajeEstadosFactura           = "MENSAJE_ESTADOS_FACTURA";

            $strDescripcionDetCaract            = "CARACTERISTICAS_CLONACION_FACTURAS"; //Caracteristicas a clonar
            $strNecesitaEliminarPrefactura      = "false";
            $strDescripcionDetMostrarFecha      = "MOSTRAR_FECHA_FACTURA_PADRE";
            
            $strDescripcionEmpresasPermitidas   = "CHEQUEO_EMPRESA_CLON_FACTURAS";
            $strMensajeEmpresasNoPermitidas     = "MENSAJE_CHEQUEO_EMPRESA_CLON_FACTURAS";

            //Para validacion de caracteristicas-productos/servicios especiales
            $strDescripcionDetChequeoProd           = "CHEQUEO_PRODUCTOS";
            $strDescripcionDetMensajeChequeoProd    = "MENSAJE_CHEQUEO_PRODUCTOS";
            $strDescripcionDetChequeoCaract         = "CHEQUEO_CARACTERISTICAS";
            $strDescripcionDetMensajeChequeoCaract  = "MENSAJE_CHEQUEO_CARACTERISTICAS";
            $strDescripcionDetChequeoImpuesto       = "CHEQUEO_IMPUESTOS";
            $strDescripcionDetMensajeChequeoImp     =  "MENSAJE_CHEQUEO_IMPUESTOS";
            $strDescripcionDetMensajeCompensacionSolidaria = "MENSAJE_CHEQUEO_COMPENSACION_SOLIDARIA";
        }
        
        //Revisamos los estados permitidos para poder clonar.
        $strParametroCab            = "CLONACION DE FACTURAS";
        $strModulo                  = "FINANCIERO";
        $arrayParametrosAdmiParam['nombreParametro']      = $strParametroCab;
        $arrayParametrosAdmiParam['modulo']      = $strModulo;
        $arrayParametrosAdmiParam['descripcion'] = $strDescripcionDetEstados;
        $arrayParametrosAdmiParam['empresaCod']           = $intCodEmpresa;
        $arrayParametrosAdmiParam['strLlave']           = 'valor1';
        
        $arrayEstadosPermitidos = $this->emSoporte->getRepository("schemaBundle:AdmiParametroDet")
                                                    ->getArrayParametrosDetalle($arrayParametrosAdmiParam);

        $arrayParametrosAdmiParam['empresaCod']           = null;
        //descripcion = strDescripcionParametro
        $arrayParametrosAdmiParam['descripcion'] = $strMensajeEmpresasNoPermitidas;
        $arrayMensajeEmpresasNoPermitidas = $this->emSoporte->getRepository("schemaBundle:AdmiParametroDet")
                                                            ->getArrayParametrosDetalle($arrayParametrosAdmiParam);
        $arrayParametrosAdmiParam['empresaCod']           = $intCodEmpresa;

        //Extraemos si se necesita copiar la fecha de la factura padre al frontend
        if($strDescripcionDetMostrarFecha)
        {
            $arrayParametrosAdmiParam['descripcion'] = $strDescripcionDetMostrarFecha;
            $arrayCopiarFechaFrontEnd = $this->emSoporte->getRepository("schemaBundle:AdmiParametroDet")
                                                        ->getArrayParametrosDetalle($arrayParametrosAdmiParam);
        }
        
        $arrayParametrosVerificacion = array();

        $arrayParametrosVerificacion["intIdEmpresa"] = $intCodEmpresa;
        $arrayParametrosVerificacion["objInfoDocumentoFinancieroCab"] = $entityFacturaCab; 

        $arrayParametrosVerificacion["strDescripcionEmpresasPermitidas"] = $strDescripcionEmpresasPermitidas;
        $arrayParametrosVerificacion["strDescripcionDetEstados"] = $strDescripcionDetEstados;
        $arrayParametrosVerificacion["boolPerfilClonacion"] = $boolPerfilClonacion;
        
        $arrayParametrosAdmiParam['descripcion'] = "MENSAJE_PERFIL_NO_PERMITIDO";
        $arrayMensajePerfilNoPermitido = $this->emSoporte->getRepository("schemaBundle:AdmiParametroDet")
                                                            ->getArrayParametrosDetalle($arrayParametrosAdmiParam);

        $arrayParametrosAdmiParam['descripcion'] = $strMensajeEstadosFactura;
        $arrayMensajeEstadosNoPermitidos = $this->emSoporte->getRepository("schemaBundle:AdmiParametroDet")
                                                            ->getArrayParametrosDetalle($arrayParametrosAdmiParam);

        $arrayVerificacionPerfiles      = $this->verificarPermisosClonacion($arrayParametrosVerificacion);
        $boolPermisoPersonaClonar       = $arrayVerificacionPerfiles["boolPermisoPersonaClonar"];
        $boolPermisoEmpresaClonar       = $arrayVerificacionPerfiles["boolPermisoEmpresaClonar"];
        $boolEstadoPermitidoFacPadre    = $arrayVerificacionPerfiles["boolPermisoPintarBotonClonar"];

        if(!$boolPermisoEmpresaClonar || !$boolEstadoPermitidoFacPadre || ! $boolPermisoPersonaClonar)
        {
            $arrayParametroSalidaPermiso["error"] = 2;
            if($arrayParametrosEntrada["strTipoFacturacion"]=="Proporcional")
            {
                $arrayParametroSalidaPermiso["redireccion_url"] = "facturasproporcionales_show";
            }
            else
            {
                $arrayParametroSalidaPermiso["redireccion_url"] = "infodocumentofinancierocab_show";
            }
            $arrayParametroSalidaPermisosMsg=array();
            if(!empty($arrayMensajeEmpresasNoPermitidas) && !$boolPermisoEmpresaClonar)
            {
                $arrayParametroSalidaPermisosMsg[] = $arrayMensajeEmpresasNoPermitidas[0];
            }
            if(!empty($arrayMensajeEstadosNoPermitidos) && !$boolEstadoPermitidoFacPadre)
            {
                $arrayParametroSalidaPermisosMsg[] = $arrayMensajeEstadosNoPermitidos[0];
            }
            if(!empty($arrayMensajePerfilNoPermitido) && !$boolPermisoPersonaClonar)
            {
                $arrayParametroSalidaPermisosMsg[] = $arrayMensajePerfilNoPermitido[0];
            }
            $arrayParametroSalidaPermiso["mensaje_error"]  = $arrayParametroSalidaPermisosMsg;
            $arrayParametroSalidaPermiso["arrayParametros"] = array('id' => $intIdDocumento);
            return $arrayParametroSalidaPermiso;
        }

        if(isset($strDescripcionDetMensajeChequeoProd))
        {
            //Extraemos el valor que se le presentara al usuario del producto no deseado.
            $arrayParametrosAdmiParam['descripcion'] = $strDescripcionDetMensajeChequeoProd;
            $arrayMensajeProductosNoPermitidos = $this->emSoporte->getRepository("schemaBundle:AdmiParametroDet")
                                                                    ->getArrayParametrosDetalle($arrayParametrosAdmiParam);
        }
        
        if(isset($strDescripcionDetMensajeChequeoCaract))
        {
            //Extraemos el valor que se le presentara al usuario de las caracteristicas no deseadas.
            $arrayParametrosAdmiParam['descripcion'] = $strDescripcionDetMensajeChequeoCaract;
            $arrayMensajeCaracteristicasNoPermitidas = $this->emSoporte->getRepository("schemaBundle:AdmiParametroDet")
                                                                        ->getArrayParametrosDetalle($arrayParametrosAdmiParam);
        }

        if(isset($strDescripcionDetMensajeChequeoImp))
        {
            //Extraemos el valor que se le presentara al usuario de los impuestos no validos.
            $arrayParametrosAdmiParam['descripcion'] = $strDescripcionDetMensajeChequeoImp;
            $arrayMensajeImpNoPermitidos = $this->emSoporte->getRepository("schemaBundle:AdmiParametroDet")
                                                            ->getArrayParametrosDetalle($arrayParametrosAdmiParam);
        }
        
        if(isset($strDescripcionDetMensajeCompensacionSolidaria))
        {
            //Extraemos el valor que se le presentara al usuario de las caracteristicas no deseadas.
            $arrayParametrosAdmiParam['descripcion'] = $strDescripcionDetMensajeCompensacionSolidaria;
            $arrayMensajeCompensacionSolidaria = $this->emSoporte->getRepository("schemaBundle:AdmiParametroDet")
                                                                    ->getArrayParametrosDetalle($arrayParametrosAdmiParam);
        }

        $arrayProductosNoPermitidos=array();
        if(isset($strDescripcionDetChequeoProd))
        {
            //Extraemos los productos no permitidos
            $arrayParametrosAdmiParam['descripcion'] = $strDescripcionDetChequeoProd;
            $arrayProductosNoPermitidos = $this->emSoporte->getRepository("schemaBundle:AdmiParametroDet")
                                                            ->getArrayParametrosDetalle($arrayParametrosAdmiParam);
        }
        $arrayCaracteristicasNoPermitidos = array();
        if(isset($strDescripcionDetChequeoCaract))
        {
            //Extraemos las caracteristicas no permitidas.
            $arrayParametrosAdmiParam['descripcion'] = $strDescripcionDetChequeoCaract;
            $arrayCaracteristicasNoPermitidos = $this->emSoporte->getRepository("schemaBundle:AdmiParametroDet")
                                                                ->getArrayParametrosDetalle($arrayParametrosAdmiParam);
        }
        $arrayImpsNoPermitidos = array();
        if(isset($strDescripcionDetChequeoImpuesto))
        {
            //Extraemos los impuestos no permitidas.
            $arrayParametrosAdmiParam['descripcion'] = $strDescripcionDetChequeoImpuesto;
            $arrayImpsNoPermitidos = $this->emSoporte->getRepository("schemaBundle:AdmiParametroDet")
                                                        ->getArrayParametrosDetalle($arrayParametrosAdmiParam);
        }

        //Verificamos si los productos configurados en el AdmiParametroCab estan en la factura,
        //para alertar al usuario que existe esos productos en la factura a clonar
        foreach($arrayProductosNoPermitidos as $strProductoNoPermitido)
        {
            $entityProducto = $emComercial->getRepository("schemaBundle:AdmiProducto")
                                        ->findOneBy(array("descripcionProducto"=>$strProductoNoPermitido));
            if($entityProducto)
            {
                $entityInfoDocumentoFinanDet  = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroDet')
                                                                ->findOneBy(
                                                                    array("documentoId"=>$entityFacturaCab->getId(),
                                                                            "productoId"=>$entityProducto->getId()
                                                                        ) );
                if(is_object($entityInfoDocumentoFinanDet))
                {
                    $arrayAlertasClonacion [] = str_replace("%nombre_producto%",$strProductoNoPermitido,
                                                                $arrayMensajeProductosNoPermitidos[0]);
                }
            }
        }
        
        //Verificamos si las caracteristicas configurados en el AdmiParametroCab estan en la factura,
        //para alertar al usuario que existe esas caracteristicas en la factura a clonar
        foreach($arrayCaracteristicasNoPermitidos as $strCaracteristicaNoPermitido)
        {
            $entityCaracteristica = $emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                                ->findOneBy(array("descripcionCaracteristica"=>$strCaracteristicaNoPermitido));
            if(is_object($entityCaracteristica))
            {
                $entityInfoDocumentoCaract  = $emFinanciero->getRepository('schemaBundle:InfoDocumentoCaracteristica')
                                                            ->findOneBy(
                                                                array("documentoId"=>$entityFacturaCab->getId(),
                                                                        "caracteristicaId"=>$entityCaracteristica->getId()
                                                                    ) );
                if(is_object($entityInfoDocumentoCaract))
                {
                    $arrayAlertasClonacion [] = str_replace("%nombre_caracteristica%",$strCaracteristicaNoPermitido,
                                                                $arrayMensajeCaracteristicasNoPermitidas[0]);
                }
            }
        }
        $boolProcesoIce = false;

        $entityIce = $emComercial->getRepository("schemaBundle:AdmiImpuesto")
                                          ->findOneBy(array("descripcionImpuesto"=>"ICE 15%"));
        $arrayIvaChequeado = array();
        //Se verifica que haya iva diferente al del proyecto
        foreach ($arrayDetalleFactura as $entityInfoDocumentoFinancieroDet)
        {
            //Ice 15
            if(!$boolProcesoIce && is_object($entityIce))
            {
                $intIdServicio = $entityInfoDocumentoFinancieroDet->getServicioId();
                if($entityInfoDocumentoFinancieroDet->getProductoId())
                {
                    $intIdProducto = $entityInfoDocumentoFinancieroDet->getProductoId();
                }
                else if($intIdServicio)
                {
                    $entityServicio = $emComercial->getRepository("schemaBundle:InfoServicio")
                                          ->find($intIdServicio);    
                    if($entityServicio && $entityServicio->getProductoId())
                    {
                        $entityProducto = $em->getRepository("schemaBundle:AdmiProducto")->find($entityServicio->getProductoId());
                        $intIdProducto = $entityProducto->getId();   
                    }
                }
                if($intIdProducto)
                {
                    $entityProdActualIce = $emComercial->getRepository("schemaBundle:InfoProductoImpuesto")
                                                ->findOneBy(array("productoId"=>$intIdProducto,
                                                                    "impuestoId"=>$entityIce->getId()));

                    $entityProdAnteriorIce  = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroImp')
                                                                ->findOneBy(array("detalleDocId"=>$entityInfoDocumentoFinancieroDet->getId(),
                                                                        "impuestoId"=>$entityIce->getId()) );
                    //Si el producto SI tenia ICE y ahora NO
                    if(!is_object($entityProdActualIce) && is_object($entityProdAnteriorIce))
                    {
                        $strDescAdmParDetIce = "MENSAJE_IMPUESTO_".$entityIce->getDescripcionImpuesto();
                    
                        $arrayParametrosAdmiParam['descripcion'] = $strDescAdmParDetIce;
                        $arrayMensImp = $this->emSoporte->getRepository("schemaBundle:AdmiParametroDet")
                                                        ->getArrayParametrosDetalle($arrayParametrosAdmiParam);
                        $strMsgValidacion = str_replace("%impuesto_descripcion%",$arrayMensImp[0],$arrayMensajeImpNoPermitidos[0]);
                        $arrayAlertasClonacion [] = $strMsgValidacion;

                        $boolProcesoIce=true;
                    }
                }
            }

            foreach($arrayImpsNoPermitidos as $strImpNoPermitido)
            {
                //Verificamos si hay un IVA que no se permita, configurado en el admiParametroDet,
                //para alertar al usuario que existe ese iva no correspondido en la factura a clonar
                $entityImp = $emComercial->getRepository("schemaBundle:AdmiImpuesto")
                                                    ->findOneBy(array("descripcionImpuesto"=>$strImpNoPermitido));
                if(is_object($entityImp))
                {
                    $entityInfoDocumentoCaract  = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroImp')
                                                                ->findOneBy(
                                                                    array("detalleDocId"=>$entityInfoDocumentoFinancieroDet->getId(),
                                                                            "impuestoId"=>$entityImp->getId()
                                                                        ) );
                    $strDescAdmParDet = "MENSAJE_IMPUESTO_".$strImpNoPermitido;
                    
                    if(is_object($entityInfoDocumentoCaract) && !in_array($strImpNoPermitido,$arrayIvaChequeado))
                    {
                        $arrayParametrosAdmiParam['descripcion'] = $strDescAdmParDet;
                        $arrayMensImp = $this->emSoporte->getRepository("schemaBundle:AdmiParametroDet")
                                                        ->getArrayParametrosDetalle($arrayParametrosAdmiParam);
                        if(!empty($arrayMensImp) && !empty($arrayMensajeImpNoPermitidos))
                        {
                            $strMsgValidacion = str_replace("%impuesto_descripcion%",$arrayMensImp[0],$arrayMensajeImpNoPermitidos[0]);
                            $arrayAlertasClonacion [] = $strMsgValidacion;
                            $arrayIvaChequeado[] = $strImpNoPermitido;
                            break;
                        }
                    }
                }
            }
        }

        //Verificacion de Caracteristica de Compensacion Solidaria
        if(is_object($entityFacturaCab) && $entityFacturaCab->getDescuentoCompensacion()>0 && !empty($arrayMensajeCompensacionSolidaria))
        {
            $arrayAlertasClonacion [] = $arrayMensajeCompensacionSolidaria[0];
        }
    
        $arrayParametros["ptoCliente"]      = $arrayParametrosEntrada['ptoCliente'];
        $arrayParametros["idOficina"]       = $arrayParametrosEntrada['idOficina'] ? $arrayParametrosEntrada['idOficina'] : 0;
        $arrayParametros["strFacturaElectronico"]   = $arrayParametrosEntrada['strFacturaElectronico'];
        $arrayParametros["strNombrePais"]   = $arrayParametrosEntrada['strNombrePais'];

        $arrayParametros["idFactura"]           = $intIdDocumento;
        $arrayParametros["intIdOficinaClonar"]  = $entityFacturaCab->getOficinaId();
        
        $arrayParametros["strIpCreacion"]       = $strIpCreacion;
        $arrayParametros["strPrefijoEmpresa"]   = $strPrefijoEmpresa;
        $arrayParametros["strUsuario"]          = $strUsuario;
        $arrayParametros["strTipoFacturacion"]  = $strTipoFacturacion;
        $arrayParametros["intCodEmpresa"]       = $intCodEmpresa;
        $arrayParametros["strNombreOficina"]    = $strNombreOficina;
        
        if(!empty($arrayCopiarFechaFrontEnd) && $arrayCopiarFechaFrontEnd && $arrayCopiarFechaFrontEnd[0]=="S")
        {
            $arrayParametros["strCopiarFechaFrontEnd"]    = $arrayCopiarFechaFrontEnd[0];
        }
        else
        {
            $arrayParametros["strCopiarFechaFrontEnd"] = "N";
        }
        $arrayParametros["arrayRolesPermitidos"] = $arrayRolesPermitidos;
        $arrayDatosService                      = $this->obtenerDatosFacturaClon($arrayParametros);
        
        $arrayDatosService["arrayAlertasClonacion"] = $arrayAlertasClonacion;
        $arrayDatosService["strNecesitaEliminarPrefactura"] = $strNecesitaEliminarPrefactura;
        $arrayDatosService["strDescripcionDetCaract"] = "$strDescripcionDetCaract";
        
        return $arrayDatosService;
    }


    /**
     * Documentación para el método 'verificarPermisosClonacion
     * 
     * Funcion que verifica si la factura se puede clonar (por motivos de estado-fac, perfil-persona o empresa)
     * 
     * @author Gustavo Narea <gnarea@telconet.ec>
     * @version 1.0 29-04-2022
     */
    public function verificarPermisosClonacion($arrayParametrosEntrada)
    {
        $strParametroCab            = "CLONACION DE FACTURAS";
        $strModulo                  = "FINANCIERO";
        $arrayParametrosSalida      = array();
        $boolPermisoPersonaClonar   = $arrayParametrosEntrada["boolPerfilClonacion"];
        $boolPermisoEmpresaClonar   = false;
        $boolPermisoEstadosClonar   = false;
        //Se ponen los links si son de la empresa seleccionada
        $intIdEmpresa           = $arrayParametrosEntrada["intIdEmpresa"];
        $objInfoDocumentoFinancieroCab      = $arrayParametrosEntrada["objInfoDocumentoFinancieroCab"];

        $strDescripcionEmpresasPermitidas   = $arrayParametrosEntrada["strDescripcionEmpresasPermitidas"];
        $strDescripcionDetEstados           = $arrayParametrosEntrada["strDescripcionDetEstados"];
        
        $arrayParametrosAdmiParam['nombreParametro']      = $strParametroCab;
        $arrayParametrosAdmiParam['modulo']               = $strModulo;
        $arrayParametrosAdmiParam['empresaCod']           = $intIdEmpresa;
        $arrayParametrosAdmiParam['strLlave']             = "valor1";
        $arrayEstadosFacturaPermitidos = array();
        if($strDescripcionDetEstados)
        {
            $arrayParametrosAdmiParam['descripcion'] = $strDescripcionDetEstados;
            $arrayEstadosFacturaPermitidos = $this->emSoporte->getRepository("schemaBundle:AdmiParametroDet")
                                                                ->getArrayParametrosDetalle($arrayParametrosAdmiParam);
        }
        if(is_object($objInfoDocumentoFinancieroCab) 
                && in_array($objInfoDocumentoFinancieroCab->getEstadoImpresionFact(), $arrayEstadosFacturaPermitidos))
        {
            $boolPermisoEstadosClonar = true;
        }
        
        $arrayParametrosAdmiParam['empresaCod']           = null;
        $arrayEmpresasPermitidas = array();
        if($strDescripcionEmpresasPermitidas)
        {
            $arrayParametrosAdmiParam['descripcion'] = $strDescripcionEmpresasPermitidas;
            $arrayEmpresasPermitidas = $this->emSoporte->getRepository("schemaBundle:AdmiParametroDet")
                                                        ->getArrayParametrosDetalle($arrayParametrosAdmiParam);
        }
        $arrayParametrosAdmiParam['empresaCod']           = $intIdEmpresa;
        if(in_array($intIdEmpresa,$arrayEmpresasPermitidas))
        {
            $boolPermisoEmpresaClonar = true;
        }
        $arrayParametrosSalida["boolPermisoPersonaClonar"]      = $boolPermisoPersonaClonar;
        $arrayParametrosSalida["boolPermisoEmpresaClonar"]      = $boolPermisoEmpresaClonar;
        $arrayParametrosSalida["boolPermisoPintarBotonClonar"]  = $boolPermisoEstadosClonar; //Permisos de estado de factura
        return $arrayParametrosSalida;
    }

    /**
     * Genera un string describiendo el rango de consumo dada una fecha inicial y final. 
     * 
     * @author Bryan Fonseca <bfonseca@telconet.ec>
     * @version 1.0 14-04-2023
     */
    // TODO: usar try-catch para cumplir estándares
    private function crearRangoConsumo($arrayParametros, $strTipoConsumo = 'consumoDias')
    {
        $strFechaInicio = $arrayParametros['strFechaInicio'];
        $strFechaFin = $arrayParametros['strFechaFin'];

        $objFechaInicio = $strFechaInicio ? \DateTime::createFromFormat("d-m-Y", $strFechaInicio) : null;
        $objFechaFin    = $strFechaFin ? \DateTime::createFromFormat("d-m-Y", $strFechaFin) : null;

        if ($objFechaInicio == null || $objFechaFin == null)
        {
            return '';
        }

        $strFechaInicialTmp = "";
        $strFechaFinalTmp   = "";

        $strObservacionRangoConsumo = 'De ';

        if($strTipoConsumo == "consumoDias")
        {
            $strObservacionRangoConsumo = "Del ";

            $strFechaInicialTmp .= strftime("%d",$objFechaInicio->getTimestamp())." ";
            $strFechaFinalTmp   .= strftime("%d",$objFechaFin->getTimestamp())." ";
        }

        $strFechaInicialTmp .= ucfirst(strtolower(strftime("%B",$objFechaInicio->getTimestamp())))." ".
                                strftime("%Y",$objFechaInicio->getTimestamp());

        $strFechaFinalTmp .= ucfirst(strtolower(strftime("%B",$objFechaFin->getTimestamp())))." ".
                                strftime("%Y",$objFechaFin->getTimestamp());

        $strObservacionRangoConsumo .= $strFechaInicialTmp;
        $strObservacionRangoConsumo .= ($strTipoConsumo == "consumoDias") ? " al " : " a ";
        $strObservacionRangoConsumo .= $strFechaFinalTmp;

        return $strObservacionRangoConsumo;
    }

    /**
     * Comprueba que la factura es duplicada. 
     * Se considera una factura duplicada si se cumple lo siguiente:
     * - La fecha de consumo (o rango de fechas) es igual a una factura Pendiente o Activa de ese punto
     * - Los servicios coinciden con dicha factura (número de servicios y precio de los mismos)
     * 
     * El valor de idDocumentoFinancieroCab corresponderá a una factura o prefactura.
     * Para determinar si es prefactura, comprobar si la entidad con este ID tiene número de SRI.
     * 
     * @return object [
     *      'esDuplicada' => boolean,
     *      'idDocumentoFinancieroCab' => int
     * ]
     * 
     * @author Bryan Fonseca <bfonseca@telconet.ec>
     * @version 1.0 14-04-2023
     */
    public function esFacturaDuplicada($arrayParametros) 
    {
        $intIdPunto = $arrayParametros['intIdPunto'];
        $arrayServicios = $arrayParametros['arrayServicios'];
        $intMesConsumo = $arrayParametros['intMesConsumo'];
        $intAnioConsumo = $arrayParametros['intAnioConsumo'];
        $arrayFechas = $arrayParametros['arrayFechas'];
        $strSelectedOption = $arrayParametros['strSelectedOption'];
        $strOpcRangoConsumoSelected = $arrayParametros['strOpcRangoConsumoSelected'];

        $boolEsDuplicada = false;
        try 
        {
            // Se filtra el array de servicios de la factura actual por el bug de la precargada agrupada
            // donde se crea un detalle vacío
            $arrayServiciosFiltrado = array();
            foreach ($arrayServicios as $objServicio) 
            {
                if (intval($objServicio->idServicio) !== 0)
                {
                    array_push($arrayServiciosFiltrado, $objServicio);
                }
            }
            $arrayServicios = $arrayServiciosFiltrado;

            // Se consiguen las facturas ordenadas por fecha en orden ascendente (de menos a más recientes)
            $arrayFacturas = $this->emfinan->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                ->getFacturasPorPuntoPorEstado([
                    "intIdPunto" => $intIdPunto,
                    'arrayInEstados'     => ['Activo', 'Pendiente'], // Se consideran estados ['Pendiente', 'Activo']
                    'arrayTipoDocumento' => ['FAC', 'FACP'] // Para las prefacturas se guardan como FACP pero se usa el campo mes de consumo, no rango
                ]);
            
            if (count($arrayFacturas) == 0)
            {
                return false;
            }

            $arrayFacturasFiltradasPorConsumo = array();

            // Se filtra por mes de consumo
            if ($strSelectedOption == "feConsumo") 
            {
                foreach ($arrayFacturas as $objFactura) 
                {
                    $intAnioConsumoFactura = intval($objFactura->getFeEmision()->format('Y'));
                    if (intval($objFactura->getMesConsumo()) === $intMesConsumo && $intAnioConsumoFactura === $intAnioConsumo)
                    {
                        array_push($arrayFacturasFiltradasPorConsumo, $objFactura);
                    }
                }
            }
            else if($strSelectedOption == "rangoConsumo")
            {
                // Se debe filtrar por rango de consumo (rango de días o meses)
                $strRangoConsumo = $this->crearRangoConsumo(
                    array(
                        'strFechaInicio' => $arrayFechas['fechaInicio'],
                        'strFechaFin' => $arrayFechas['fechaFin']
                    ),
                    $strOpcRangoConsumoSelected
                );

                foreach ($arrayFacturas as $objFactura) 
                {
                    if ($objFactura->getRangoConsumo() === $strRangoConsumo)
                    {
                        array_push($arrayFacturasFiltradasPorConsumo, $objFactura);
                    }
                }
            }

            // Si ninguna factura coincide con el mes de consumo (o rango de consumo) seleccionado
            // Se considera que no son duplicadas
            if (count($arrayFacturasFiltradasPorConsumo) == 0)
            {
                return false;
            }

            $boolTieneServiciosIguales = false;
            // Se comprueba si alguna de las facturas tiene los mismos servicios que $arrayServicios,
            // si es así, se considera duplicada
            foreach ($arrayFacturasFiltradasPorConsumo as $objFactura)
            {
                // Se consiguen los servicios de la factura actual
                $arrayDetalleFactura = $this->emfinan->getRepository('schemaBundle:InfoDocumentoFinancieroDet')
                    ->getDetallesDelDocumento([
                        'intIdDocumento' => $objFactura->getId()
                    ]);

                // Se crea una tabla hash con dichos servicios para realizar una búsqueda
                $arrayServiciosTablahash = array();
                foreach ($arrayDetalleFactura as $objServicio)
                {
                    $arrayServiciosTablahash[$objServicio->getServicioId()] = $objServicio->getPrecioVentaFacproDetalle();
                }

                // Si la tabla hash y el array de servicios tienen lengths diferentes, son diferentes
                if (count($arrayServiciosTablahash) !== count($arrayServicios))
                {
                    continue;
                }

                $boolExisteEnTablahash = true;
                // Se compara la tabla hash con el array de servicios de la factura a crear
                foreach ($arrayServicios as $objServicio)
                {
                    $intIdServicio = intval($objServicio->idServicio);
                    if ($arrayServiciosTablahash[$intIdServicio] != $objServicio->precio)
                    {
                        // Si no está en la tabla hash, no son iguales
                        $boolExisteEnTablahash = false;
                        break;
                    }
                }

                // Si todos los servicios de la factura a crear coinciden con todos los de la tabla hash
                if ($boolExisteEnTablahash)
                {
                    $boolTieneServiciosIguales = true;
                    $intIdDocumentoFinancieroCab = $objFactura->getId();
                    break;
                }
            }
            $boolEsDuplicada = $boolTieneServiciosIguales;
        } 
        catch (\Exception $objException) 
        {
            $this->utilService->insertError('Telcos+', 
                                            'esFacturaDuplicada', 
                                            $objException->getMessage(), 
                                            'bfonseca', 
                                            '127.0.0.1');
        }

        return (object)[
            'esDuplicada' => $boolEsDuplicada,
            'idDocumentoFinancieroCab' => $intIdDocumentoFinancieroCab
        ];
    }

}


