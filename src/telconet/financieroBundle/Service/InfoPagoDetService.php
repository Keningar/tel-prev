<?php

namespace telconet\financieroBundle\Service;

use telconet\schemaBundle\Entity\InfoDocumentoHistorial;
use telconet\schemaBundle\Entity\InfoPagoDet;
use Symfony\Component\HttpKernel\KernelEvents;

class InfoPagoDetService
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emfinan;
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emcom;
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emGeneral;
    private $serviceUtil;
    
    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
        $this->emfinan = $container->get('doctrine.orm.telconet_financiero_entity_manager');
        $this->emcom = $container->get('doctrine.orm.telconet_entity_manager');
        $this->emGeneral = $container->get('doctrine.orm.telconet_general_entity_manager');
        $this->serviceUtil = $container->get('schema.Util');
    }
    
    
    /**
     * Documentación para la función 'validacionPagosAnticipos'
     * 
     * Función que valida la creación de anticipos, ya sea por la generación de un pago o la creación de anticipo directamente, es válida. Las formas
     * de pago válidas para la creación de un anticipo son las formas de pago depositables y no depositables pero que tienen tipo de forma de pago 
     * 'TARJETA_CREDITO', 'DEBITO' y 'DEPOSITO'.
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 23-03-2017
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.1 07-04-2017 - Se redondean los saldos obtenidos de la factura para validar a dos decimales con los valores ingresados por el
     *                           usuario
     * 
     * @param array $arrayParametros
     * 
     * @return String $strMensaje
     */
    public function validacionPagosAnticipos($arrayParametros)
    {
        $strTipoDocumento  = ( isset($arrayParametros['strTipoDocumento']) && !empty($arrayParametros['strTipoDocumento']) )
                             ? $arrayParametros['strTipoDocumento'] : '';
        $arrayDetallesPago = ( isset($arrayParametros['arrayDetallesPago']) && !empty($arrayParametros['arrayDetallesPago']) )
                             ? $arrayParametros['arrayDetallesPago'] : '';
        $strMensaje        = "OK";

        try
        {
            if( !empty($strTipoDocumento) && !empty($arrayDetallesPago) )
            {
                $floatSaldoFactura            = 0;
                $floatAcumuladorPagosDetalles = 0;
                
                foreach($arrayDetallesPago as $strDetallePago)
                {
                    if( !empty($strDetallePago) )
                    {
                        //Se verifica si viene una coma al inicio de la información del detalle del pago
                        $intPos = strpos($strDetallePago, ',');

                        if( $intPos == 0 )
                        {
                            $strDetallePago = substr_replace($strDetallePago, '', $intPos, 1);
                        }

                        $arrayDetallePago = explode(',', $strDetallePago);

                        $intIdFormaPago        = ( isset($arrayDetallePago[0]) && !empty($arrayDetallePago[0]) ) ? $arrayDetallePago[0] : 0;
                        $intIdFactura          = ( isset($arrayDetallePago[2]) && !empty($arrayDetallePago[2]) ) ? $arrayDetallePago[2] : 0;
                        $floatValorDetallePago = ( isset($arrayDetallePago[9]) && !empty($arrayDetallePago[9]) ) ? $arrayDetallePago[9] : 0;
                        $strNumeroFactura      = ( isset($arrayDetallePago[3]) && !empty($arrayDetallePago[3]) ) ? $arrayDetallePago[3] : '';

                        if( !empty($intIdFormaPago) )
                        {
                            $boolValidarAnticipo = false;

                            if( !empty($intIdFactura) )
                            {
                                //Obtiene el saldo de la factura
                                $arrayParametrosSaldoFac = array('intIdDocumento' => $intIdFactura, 'intReferenciaId' => '');
                                $arrayGetSaldoXFactura   = $this->emfinan->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                ->getSaldosXFactura($arrayParametrosSaldoFac);

                                if( isset($arrayGetSaldoXFactura['strMessageError']) && !empty($arrayGetSaldoXFactura['strMessageError']))
                                {
                                    $strMensaje = 'Error al calcular el saldo de factura: '. $strNumeroFactura;
                                }
                                else
                                {
                                    if( isset($arrayGetSaldoXFactura['intSaldo']) && !empty($arrayGetSaldoXFactura['intSaldo']))
                                    {
                                        $floatSaldoFactura = round(floatval($arrayGetSaldoXFactura['intSaldo']), 2) 
                                                             - round(floatval($floatAcumuladorPagosDetalles), 2);
                                    }//( isset($arrayGetSaldoXFactura['intSaldo']) && !empty($arrayGetSaldoXFactura['intSaldo']))
                                }//( isset($arrayGetSaldoXFactura['strMessageError']) && !empty($arrayGetSaldoXFactura['strMessageError']))...

                                /**
                                 * Se verifica si el saldo de la factura es menor que saldo a pagar para validar el anticipo que va a generar dicho
                                 * detalle
                                 */
                                if( round(floatval($floatSaldoFactura), 2) < round(floatval($floatValorDetallePago), 2) )
                                {
                                    $boolValidarAnticipo = true;
                                }//( $floatSaldoFactura < $floatValorDetallePago )
                                
                                $floatAcumuladorPagosDetalles += $floatValorDetallePago;
                            }
                            else
                            {
                                $boolValidarAnticipo = true;
                            }//( !empty($intIdFactura) )

                            if( $boolValidarAnticipo )
                            {
                                $arrayParametrosValidarAnticipo = array('intIdFormaPago'   => $intIdFormaPago, 
                                                                        'strTipoDocumento' => $strTipoDocumento);

                                $objAdmiFormaPago = $this->emfinan->getRepository("schemaBundle:InfoPagoDet")
                                                         ->validaFormaPagoAntiipo($arrayParametrosValidarAnticipo);

                                if( !is_object($objAdmiFormaPago) )
                                {
                                    $strMensaje = "No se puede ingresar el pago puesto que su forma de pago no es válida para la creación de ".
                                                  "anticipos. Por favor su ayuda cambiando el orden de los detalles ingresados.";
                                    break;
                                }//( !is_object($objAdmiFormaPago) )
                            }//( $boolValidarAnticipo )
                        }
                        else
                        {
                            $strMensaje = 'Un detalle del pago ingresado no tiene forma de pago para validar la creación de anticipos.';
                        }//( !empty($intIdFormaPago) )
                    }//( !empty($strDetallePago) )
                }//foreach($arrayDetallesPago as $strDetallePago)
            }
            else
            {
                $strMensaje = 'Todos los parámetros son requeridos para validar la creación de anticipos.';
            }//( !empty($strTipoDocumento) && !empty($arrayDetallesPago) )
        }
        catch( \Exception $e)
        {
            error_log('[ERROR InfoPagoDetService.validacionPagosAnticipos]: '.$e->getMessage());
            
            throw($e);
        }
        
        return $strMensaje;
    }
    
    
    /**
     * Documentacion: Agregar detalle de pago
     * 
     * Actualizacion: Se incluye cuentaContableId para poder realizar la contabilizacion
     * @version 1.1 16/12/2015
     * Actualizacion: Se valida que el campo fe_deposito tenga data para poder ser ingresada
     * @version 1.2 amontero@telconet.ec 13/06/2016
     * Actualizacion: Se valida que solo si el valor del detalle es mayor a cero, entonces no se graba
     * @version 1.3 amontero@telconet.ec 24/06/2016
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.4 11-09-2016 - Se valida que al guardar el detalle del pago o del anticipo se guarde con el mismo estado que su cabecera
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.4 28-09-2016 - Se identifica el tipo de forma de pago con la variable '$arrayDetalles['strTipoFormaPago']' para hacer un guardado
     *                           más dinámico, este cambio afecta a los pagos, anticipos y anticipos sin cliente (opción usada por MD).
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.5 13-04-2017 - Se redondea el valor obtenido entre la resta del pago y el saldo de la factura para que NO genere anticipos con 
     *                           valor en 0.
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.6 18-02-2019 - Se agrega un registro en la tabla info_error del esquema DB_GENERAL antes y después de cerrar una factura 
     * cuando se realiza un pago, para monitorear el proceso de crear pago.
     * 
     * @param type objeto $objInfoPagoCab objeto con cabecera del pago 
     * @param type Array $arrayDetalles (arreglo con el detalle a grabar)
     * @param type String $objFechaCreacion (fecha cuando se crea el detalle)
     * @param type float $fltValorCabeceraPago (valor de la cabecera del pago)
     * @since 11/09/2014
     * @author amontero@telconet.ec
     * @return array
     */
    public function agregarDetallePago($objInfoPagoCab,$arrayDetalles,$objFechaCreacion,$fltValorCabeceraPago)
    {
        $boolEsDebito           = false;
        $boolEsCheque           = false;
        $boolEsReferencia       = false;
        $boolEsRetencion        = false;
        $entityBancoTipoCuenta  = null;
        $boolCierraFactura      = false;
        $arrayAnticipo          = array();
        $strEstadoPagoCab       = "Pendiente";
        
        if( $objInfoPagoCab && !empty($arrayDetalles) && $objFechaCreacion )
        {
            $strEstadoPagoCab = $objInfoPagoCab->getEstadoPago();
            $strEstadoPagoCab = ( !empty($strEstadoPagoCab) ) ? $strEstadoPagoCab : 'Pendiente';
        }
        else
        {
            throw new \Exception("Debe enviar los parámetros correctos para guardar los detalles del pago");
        }
               
        $objInfoPagoDet = new InfoPagoDet();                
        $objInfoPagoDet->setEstado($strEstadoPagoCab);
        $objInfoPagoDet->setFeCreacion($objFechaCreacion);
        $objInfoPagoDet->setFormaPagoId($arrayDetalles['idFormaPago']);
        
        if( strtoupper(trim($arrayDetalles['strTipoFormaPago'])) == 'CHEQUE' )
        {
            $boolEsCheque = true;
            $objInfoPagoDet->setNumeroCuentaBanco($arrayDetalles['numeroDocumento']);
            $objInfoPagoDet->setNumeroReferencia($arrayDetalles['numeroDocumento']);
            $entityBancoTipoCuenta = $this->emcom->getRepository('schemaBundle:AdmiBancoTipoCuenta')
                ->findBancoTipoCuentaPorBancoPorTipoCuenta($arrayDetalles['idBanco'], $arrayDetalles['idTipoCuenta']);
            $objInfoPagoDet->setBancoTipoCuentaId($entityBancoTipoCuenta->getId());
        }
        elseif( strtoupper(trim($arrayDetalles['strTipoFormaPago'])) == 'DEPOSITO' )
        {
            $boolEsReferencia = true;
            $objInfoPagoDet->setCuentaContableId($arrayDetalles['cuentacontableId']);
            $objInfoPagoDet->setNumeroReferencia($arrayDetalles['numeroDocumento']);
            $objInfoPagoDet->setFeDeposito(new \DateTime($arrayDetalles['fechaDeposito']));
            $objInfoPagoDet->setCuentaContableId($arrayDetalles['cuentaContableId']);
        }
        elseif( strtoupper(trim($arrayDetalles['strTipoFormaPago'])) == 'TARJETA_CREDITO' )
        {
            $boolEsReferencia = true;          
            $entityBancoTipoCuenta = $this->emcom->getRepository('schemaBundle:AdmiBancoTipoCuenta')
                ->findBancoTipoCuentaPorBancoPorTipoCuenta($arrayDetalles['idBanco'], $arrayDetalles['idTipoCuenta']);
            $objInfoPagoDet->setBancoTipoCuentaId($entityBancoTipoCuenta->getId());
            $objInfoPagoDet->setNumeroCuentaBanco($arrayDetalles['numeroReferencia']);
            $objInfoPagoDet->setNumeroReferencia($arrayDetalles['numeroDocumento']);
            $objInfoPagoDet->setCuentaContableId($arrayDetalles['cuentaContableId']);
            if ($arrayDetalles['fechaDeposito'])
            {    
                $objInfoPagoDet->setFeDeposito(new \DateTime($arrayDetalles['fechaDeposito']));            
            }
        }
        elseif( strtoupper(trim($arrayDetalles['strTipoFormaPago'])) == 'DEBITO' )
        {
            $boolEsDebito = true;
            $entityBancoTipoCuenta = $this->emcom->getRepository('schemaBundle:AdmiBancoTipoCuenta')
                ->findBancoTipoCuentaPorBancoPorTipoCuenta($arrayDetalles['idBanco'], $arrayDetalles['idTipoCuenta']);
            $objInfoPagoDet->setBancoTipoCuentaId($entityBancoTipoCuenta->getId());
            $objInfoPagoDet->setNumeroCuentaBanco($arrayDetalles['numeroReferencia']);
            $objInfoPagoDet->setNumeroReferencia($arrayDetalles['numeroDocumento']);
            if($arrayDetalles['fechaDeposito'])
            {    
                $objInfoPagoDet->setFeDeposito(new \DateTime($arrayDetalles['fechaDeposito']));
            }
            $objInfoPagoDet->setCuentaContableId($arrayDetalles['cuentaContableId']);            
        }
        elseif( strtoupper(trim($arrayDetalles['strTipoFormaPago'])) == 'RETENCION' )
        {
            $boolEsRetencion=true;
            $objInfoPagoDet->setNumeroReferencia($arrayDetalles['numeroDocumento']);
            $objInfoPagoDet->setFeDeposito(new \DateTime($arrayDetalles['fechaDeposito']));            
        }
        
        $fltValorPago = $arrayDetalles['valorPago'];


        //SI TIENE FACTURA 
        if($arrayDetalles['idFactura'])
        {
            $objInfoPagoDet->setReferenciaId($arrayDetalles['idFactura']);                
            //SE VERIFICA SI EL PAGO YA CUBRE LA FACTURA y SI ES ASI CREA ARREGLO ANTICIPOs
            $objFactura = $this->emfinan->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                ->find($arrayDetalles['idFactura']);
            $arrayParametrosSend   = array('intIdDocumento'  => $objFactura->getId(), 'intReferenciaId' => '');
            //Obtiene el saldo de la factura
            $arrayGetSaldoXFactura = $this->emfinan->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                ->getSaldosXFactura($arrayParametrosSend);
            if(!empty($arrayGetSaldoXFactura['strMessageError']))
            {
                throw new Exception('Error al calcular el saldo de factura: '. $objFactura->getNumeroFacturaSri());
            }
            else
            {
                $faltaPorPagar=$arrayGetSaldoXFactura['intSaldo'];
            } 
            //SI SALDO DE FACTURA ES MENOR AL PAGO: CREA ANTICIPO Y CIERRA FACTURA
            if($faltaPorPagar < $arrayDetalles['valorPago'])
            {
                $fltValorPago      = $faltaPorPagar;
                $numReferencia     = '';
                $numeroCtaBanco    = '';
                $bancoTipoCuentaId = '';
                $cuentaContableId  = '';
                $fechaDeposito     = '';  
                $diferencia = $arrayDetalles['valorPago'] - $fltValorPago;
                $diferencia = round( $diferencia, 2);
                if($entityBancoTipoCuenta)
                {
                    $bancoTipoCuentaId = $entityBancoTipoCuenta->getId();    
                }
                if($boolEsCheque)
                {
                    $numReferencia    = $arrayDetalles['numeroDocumento'];
                    $numeroCtaBanco   = $arrayDetalles['numeroDocumento'];
                }
                elseif($boolEsReferencia)
                {
                    $numReferencia    = '';
                    $numeroCtaBanco   = $arrayDetalles['numeroReferencia'];
                    $cuentaContableId = $arrayDetalles['cuentaContableId'];
                    $fechaDeposito    = $arrayDetalles['fechaDeposito'];
                }
                elseif($boolEsDebito)
                {
                    $numReferencia    = $arrayDetalles['codigoDebito'];
                    $numeroCtaBanco   = $arrayDetalles['numeroReferencia'];
                    $cuentaContableId = $arrayDetalles['cuentaContableId'];
                    $fechaDeposito    = $arrayDetalles['fechaDeposito'];
                }
                elseif($boolEsRetencion)
                {
                    $numReferencia    = $arrayDetalles['numeroDocumento'];
                    $fechaDeposito    = $arrayDetalles['fechaDeposito'];
                }                
                $arrayAnticipo = 
                    array(
                    'valorAnticipo'     => $diferencia,
                    'bancoTipoCuentaId' => $bancoTipoCuentaId,
                    'numeroReferencia'  => $numReferencia,
                    'numeroCtaBanco'    => $numeroCtaBanco,
                    'cuentaContableId'  => $cuentaContableId,
                    'fechaDeposito'     => $fechaDeposito,    
                    'formaPagoId'       => $arrayDetalles['idFormaPago'],
                    'comentario'        => $arrayDetalles['comentario']);
                $boolCierraFactura = true;
            }
            //SI SALDO DE FACTURA ES IGUAL AL PAGO: CIERRA FACTURA                
            if($faltaPorPagar == $arrayDetalles['valorPago'])
            {
                $boolCierraFactura = true;
            }
            //cierra factura si con el pago se completa la factura
            if($boolCierraFactura)
            {
                $this->serviceUtil->insertError('Telcos+', 
                                                'CREAR_PAGOS_CIERRE_FACTURA', 
                                                'AntesFlush:|IdFact: '.$objFactura->getId().'|BanderaCierre: '.$boolCierraFactura.'|Estado: '
                                                .$objFactura->getEstadoImpresionFact(), 
                                                'hlozano', 
                                                '127.0.0.1' );
                
                $objFactura->setEstadoImpresionFact('Cerrado');
                $this->emfinan->persist($objFactura);
                $this->emfinan->flush();

                //Graba historial de la factura
                $objHistorialFactura = new InfoDocumentoHistorial();
                $objHistorialFactura->setDocumentoId($objFactura);
                $objHistorialFactura->setEstado($objFactura->getEstadoImpresionFact());
                $objHistorialFactura->setFeCreacion(new \DateTime('now'));
                $objHistorialFactura->setUsrCreacion($objInfoPagoCab->getUsrCreacion());
                $this->emfinan->persist($objHistorialFactura);
                $this->emfinan->flush();
                
                $this->serviceUtil->insertError('Telcos+', 
                                                'CREAR_PAGOS_CIERRE_FACTURA', 
                                                'DespuesFlush:|IdFact: '.$objFactura->getId().'|BanderaCierre: '.$boolCierraFactura.'|Estado: '
                                                .$objFactura->getEstadoImpresionFact(), 
                                                'hlozano', 
                                                '127.0.0.1' );
            }
        }
        if ($fltValorPago>0)
        {    
            $objInfoPagoDet->setPagoId($objInfoPagoCab);
            $objInfoPagoDet->setUsrCreacion($objInfoPagoCab->getUsrCreacion());
            $fltValorCabeceraPago = $fltValorCabeceraPago + $fltValorPago;
            $objInfoPagoDet->setValorPago($fltValorPago);
            $objInfoPagoDet->setComentario($arrayDetalles['comentario']);
            $objInfoPagoDet->setDepositado('N');
            $this->emfinan->persist($objInfoPagoDet);
            $this->emfinan->flush();  
        }    
        return array(
            'success'                 => true,
            'arr_anticipo'            => $arrayAnticipo,
            'valorCabeceraPago'       => $fltValorCabeceraPago,
            'intIdPagoDet'            => $objInfoPagoDet->getId()
        );
    }
    
    
    
    
    
    /**
     * Documentacion: Ingresa detalle de pago basado en informacion de otro pago, usando informacion recibida por parametro
     * 
     * @param $arrayParametros[]:
     * @param Object entityPagoDetClonado - objeto que se usara para crear un nuevo detalle de pago con parte de informacion
     * @param string strComentario - comentario que se ingresara en el nuevo detalle de pago
     * @param int intValorPago - valor que se ingresara en el nuevo detalle de pago
     * @param string strUsuario - usuario que se ingresara en el nuevo detalle de pago
     * @param Object entityInfoPagoCab - cabecera del pago
     * @param string strEstado - estado del pago
     * @param int intReferenciaId - id de la factura
     * @param String strContabilizado - Indica si el pago fue contabilizado
     * 
     * @return InfoPagoDet $entityAnticipoDet
     * 
     * @author Andres Montero H. <amontero@telconet.ec>
     * @version 1.0 05/07/2016
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 09-03-2017 - Se agrega validación para que el campo 'CONTABILIZADO' de los detalles de pagos clonados guarden el valor enviado
     *                           como parámetro 'strContabilizado', caso contrario se setea el valor 'N'
     */    
    function ingresaDetalleAnticipoClonado($arrayParametros)
    { 
        $entityAnticipoDet = new InfoPagoDet();
        try
        {
            //CREA LOS DETALLES DEL ANTICIPO
            if ($arrayParametros['strClonar'] == 'N')
            {
                if($arrayParametros['entityPagoDetClonado']->getFeDeposito())
                {
                    $entityAnticipoDet->setFeDeposito($arrayParametros['entityPagoDetClonado']->getFeDeposito());
                }
                $entityAnticipoDet->setValorPago($arrayParametros['intValorPago']);                
                $entityAnticipoDet->setBancoTipoCuentaId($arrayParametros['entityPagoDetClonado']->getBancoTipoCuentaId());
                $entityAnticipoDet->setDepositado($arrayParametros['entityPagoDetClonado']->getDepositado());
                $entityAnticipoDet->setDepositoPagoId($arrayParametros['entityPagoDetClonado']->getDepositoPagoId());
                $entityAnticipoDet->setNumeroReferencia($arrayParametros['entityPagoDetClonado']->getNumeroReferencia());
                $entityAnticipoDet->setNumeroCuentaBanco($arrayParametros['entityPagoDetClonado']->getNumeroCuentaBanco());
                $entityAnticipoDet->setCuentaContableId($arrayParametros['entityPagoDetClonado']->getCuentaContableId());
                $entityAnticipoDet->setFormaPagoId($arrayParametros['entityPagoDetClonado']->getFormaPagoId());
            }
            elseif ($arrayParametros['strClonar'] == 'S')
            {    
                $entityAnticipoDet= clone $arrayParametros['entityPagoDetClonado'];	
                $entityAnticipoDet->setReferenciaId($arrayParametros['intReferenciaId']);
                if($arrayParametros['intValorPago']>0)
                {
                    $entityAnticipoDet->setValorPago($arrayParametros['intValorPago']);
                }               
            }            
            $entityAnticipoDet->setFeCreacion(new \DateTime('now'));
            $entityAnticipoDet->setUsrCreacion($arrayParametros['strUsuario']);        
            $entityAnticipoDet->setEstado($arrayParametros['strEstado']);
            $entityAnticipoDet->setComentario($arrayParametros['strComentario']);
            $entityAnticipoDet->setPagoId($arrayParametros['entityInfoPagoCab']);
            
            if( isset($arrayParametros['strContabilizado']) && !empty($arrayParametros['strContabilizado']) )
            {
                $entityAnticipoDet->setContabilizado($arrayParametros['strContabilizado']);
            }
            else
            {
                $entityAnticipoDet->setContabilizado('N');
            }//( isset($arrayParametros['strContabilizado']) && !empty($arrayParametros['strContabilizado']) )
        }
        catch(\Exception $ex)
        {
            throw $ex;
        }
        return $entityAnticipoDet;
    }    
    /**
    * Documentacion para 'getRetencionesDuplicadas'
    * Funcion que verifica si alguna de las retenciones paametrizads en el proyecto Automatizacion,
    * se encuentran duplicadas en la info_pago e info_pago_automatico
    * @return $Response
    * 
    * @author Gustavo Narea <gnarea@telconet.ec>
    * @version 1.0
    * 
    * @author Edgar Holguín <eholguin@telconet.ec>
    * @version 1.1 08-11-2021 Se agrega validación para existencia de objeto a nivel de INFO_PERSONA     */
    public function getRetencionesDuplicadas($arrayParametros)
    {
        //en los html
        $intIdPersona = $arrayParametros["intIdPersona"];
        //De retenciones-automaticas
        $intIdentificacion = $arrayParametros["intIdentificacion"];
        $intNumDoc = $arrayParametros["intNumRef"];//info_[pago]_cab
        
        $strCodEmpresa = $arrayParametros["strCodEmpresa"];
        $intIdFormaPago = $arrayParametros["intIdFormaPago"];

        $arrayEstadosPago  = array('Activo','Cerrado', 'Pendiente');
        $arrayParametros["arrayEstadosDetallePago"] = $arrayEstadosPago;

        if(!isset($intIdPersona) && !isset($intIdentificacion))
        {
            return "Error: No seteado idPersona/Identificacion";
        }
        
        if(!isset($strCodEmpresa))
        {
            return "Error: No seteado codEmpresa";
        }

        if(!isset($intNumDoc))
        {
            return "Error: No seteado numero Referencia";
        }
        
        //Viene por identificacion
        if(is_null($intIdPersona)) 
        {
            if(!is_null($intIdentificacion))
            {
                $objPersona = $this->emcom->getRepository('schemaBundle:InfoPersona')->
                                findOneBy(array('identificacionCliente'=>$intIdentificacion));

                if(is_object($objPersona))
                {
                    $intIdPersona = $objPersona->getId();
                    $arrayParametros["intIdPersona"] = $intIdPersona;
                    $arrayParametros["intIdentificacion"]=null;
                } 
            }
            else
            {
                return "Error: No hay idPersona ni Identificacion, no hay como encontrar a la persona";
            }
        }

        $objAdmiParametroCab = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                            ->findOneBy(array('nombreParametro' => 'AUTOMATIZACION_RETENCIONES', 
                                                              'estado'          => 'Activo'));
        if(is_object($objAdmiParametroCab))
        {
            $objAdmiParametroDet = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->findBy(array('parametroId' => $objAdmiParametroCab,
                                                                'descripcion' => 'FORMA PAGO',
                                                                'empresaCod'  => $strCodEmpresa,
                                                                'estado'      => 'Activo'));
            if(is_null($objAdmiParametroDet))
            {
                return "No hay forma de Pago para la empresa ".$strCodEmpresa;
            }
            $arrayRetencionFormaPago = array();
            foreach($objAdmiParametroDet as $objRow)
            {
                $objAdmiFormaPago = $this->emfinan->getRepository("schemaBundle:AdmiFormaPago")->
                                                            findOneBy(array('codigoFormaPago'=>$objRow->getValor3()) );
                $intCodigoFormaPago = $objAdmiFormaPago->getId();
                array_push($arrayRetencionFormaPago,$intCodigoFormaPago);
            }
        }
        else
        {
            return "Error: No Forma Pago en AdmiParametroDet";
        }
        //Vemos si el numero_referencia esta en base
        $arrayRetencionesDuplicadas = $this->emfinan->getRepository('schemaBundle:InfoPagoDet')->findRetencion($arrayParametros);
        
        foreach($arrayRetencionesDuplicadas as $arrayPago)
        {
            $intIdFormaPagoTmp = $arrayPago["id_forma_pago"];
            //Viene por Pago (pago puede guardar el numero_referencia con otra RF-RI)
            if(!is_null($intIdFormaPago))
            {
                //Vemos si la forma_pago esta dentro de las parametrizadas
                if($intIdFormaPago==$intIdFormaPagoTmp && in_array($intIdFormaPago, $arrayRetencionFormaPago))
                {
                    {
                        return "El codigo de formaPago pasado esta en base con ese numero_referencia";
                    }
                }
            }
            else
            {
                //Viene por retencion automatica
                if(in_array($intIdFormaPagoTmp, $arrayRetencionFormaPago))
                {
                    return "La referencia esta tomada en base, no se la puede tomar de nuevo";
                }
            }
        }

        //Si no hay en las info-pago busco en la info-pago-automatico
        if(isset($intIdPersona))
        {
            if(is_null($intIdentificacion))
            {
                $objPersona = $this->emcom->getRepository("schemaBundle:InfoPersona")->find($intIdPersona);
                $intIdentificacion = $objPersona->getIdentificacionCliente();
            }
            if(!isset($arrayParametros["arrayDetExistentes"]))
            {
                $arrayDetExistentes = $this->emfinan->getRepository('schemaBundle:InfoPagoAutomaticoDet')
                                                      ->findBy(array('numeroReferencia' => $intNumDoc, 
                                                                     'estado'          => array("Procesado", "Pendiente","Error")));
            }
            else
            {
                $arrayDetExistentes = $arrayParametros["arrayDetExistentes"];
            }

            foreach ($arrayDetExistentes as $objPagoAutDet)
            {
                $intIdPagAutDet        = $objPagoAutDet->getPagoAutomaticoId();
                if($objPagoAutDet->getNumeroReferencia()==$intNumDoc && $objPagoAutDet->getFormaPagoId()==$intIdFormaPago)
                {
                    $objInfoPagoAutCab  = $this->emfinan->getRepository('schemaBundle:InfoPagoAutomaticoCab')
                    ->findOneBy(array('id'        => $intIdPagAutDet,
                                      'estado'    => array("Pendiente","Procesado","Error")));

                    if(is_object($objInfoPagoAutCab) && $objInfoPagoAutCab->getIdentificacionCliente() == $intIdentificacion )
                    {
                        return "Hay pagos automaticos con ese cliente y ese numero_referencia";
                    }
                }
            }
        }
        //Si esta vacio, puede guardar data
        return "";
    }
}
