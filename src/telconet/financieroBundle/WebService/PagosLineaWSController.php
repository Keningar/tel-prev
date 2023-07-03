<?php

namespace telconet\financieroBundle\WebService;

use telconet\schemaBundle\DependencyInjection\BaseWSController;
use telconet\financieroBundle\WebService\InfoPagoLineaResponse\ConsultarSaldoResponse;
use telconet\financieroBundle\WebService\InfoPagoLineaResponse\ProcesarPagoResponse;
use telconet\financieroBundle\WebService\InfoPagoLineaResponse\ReversarPagoResponse;
use telconet\financieroBundle\WebService\InfoPagoLineaResponse\EliminarPagoResponse;
use telconet\financieroBundle\WebService\InfoPagoLineaResponse\ConciliarPagoResponse;
use telconet\financieroBundle\WebService\InfoPagoLineaResponse\AnularPagoResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * Documentacion para la clase 'PagosLineaWSController'.
 *
 * Controlador que contiene los metodos para Pagos en Linea
 *
 * @author  Alexander Samaniego <awsamaniego@telconet.ec>
 * @version 1.0 13-03-2015
 */
class PagosLineaWSController extends BaseWSController
{

    const PROCESS_SUCCESS = '000';
    const NOT_EXIST_ACCOUNT = '001';
    const PAYMENT_FAIL = '002';
    const SERVICE_NOT_AVALIABLE = '003';
    const INVALID_PARAMETERS = '004';
    const DEBT_NOT_FOUND = '005';
    const PARAMETER_NOT_NULL = '006';
    const PAYMENT_AMOUNT_GREATER_THAN_DEBT = '007';
    const PAYMENT_AMOUNT_LESS_THAN_DEBT = '008';
    const UNDEFINED_GATEWAY = '009';
    const PROCESS_ERROR = '010';
    const TRANSACTION_NOT_EXIST = '011';
    const TRANSACTION_REVERSED = '012';
    const INITIAL_TRANSACTION_UNSUCCESSFUL = '013';
    const PAYMENT_UNDEFINED = '014';
    const EMPTY_FIELDS = '015';
    const INVALID_DATE = '016';
    const NOT_FOUND_RECORDS = '017';
    const INVALID_CREDENTIALS = '018';
    const UNDEFINED_ERROR = '019';
    const TOPUP_AMOUNT_INCORRECT = '020';
    const RECONCILIED_PAYMENT = '021';
    const CANCEL_PAYMENT = '022';
    const CONSULTA_PAGO = '100';
    const PROCESAR_PAGO = '200';
    const REVERSAR_PAGO = '300';
    const CONCILIAR_PAGO = '400';
    const ELIMINAR_PAGO = '500';
    const ANULAR_PAGO = '800';

    /**
     * Documentacion para el método 'consultarSaldoAction'.
     * Metodo que retorna el saldo de un cliente consultando por medio de la cedula de indentidad
     *
     * @param  Request   $objRequest    Recibe parametros obligatorios como, canal, identificacionCliente,
     *                                  tipoTransaccion, usuario, clave
     *                                  Codigos tipoTransaccion => (100 => consultarSaldo,
                                                                    200 => procesarPago,
                                                                    300 => reversarPago,
                                                                    400 => conciliarPago,
                                                                    500 => eliminarPago)
     * @return Response  $objResponse.  Retorna un json del objeto ConsultarSaldoResponse
     *
     * @author  Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 13-03-2015
     * @author  Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 24-11-2015
     * @author  Javier Hidalgo <jihidalgo@telconet.ec>
     * @version 1.2 29-05-2022 - Se reemplaza metodo obtenerConsultaSaldoClientePorIdentificacion (version anterior)
     *                           por metodo nuevo obtenerConsultaSaldoPorIdentificacion.
     * @since 1.0
     */
    public function consultarSaldoAction(Request $objRequest)
    {
        $objConsultarSaldoResponse  = new ConsultarSaldoResponse();
        $servicePagoLinea           = $this->get('financiero.InfoPagoLinea');
        $objResponse                = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');
        $arrayRequest               = json_decode($objRequest->getContent(), true);
        $dateFechaActual            = new \DateTime();
        $objLog                     = new Logger('PagosLineaWSController');
        //Valida credenciales, formato json, cliente existente
        $arrayResultValidaJsonCredencialesCliente = $this->validaJsonCredencialesCliente($arrayRequest, self::CONSULTA_PAGO);
        //Valida si existió un error termina el metodo con un response
        if(true === $arrayResultValidaJsonCredencialesCliente['boolResponse'])
        {
            $objConsultarSaldoResponse->setError($arrayResultValidaJsonCredencialesCliente['strMensaje']);
            $objConsultarSaldoResponse->setRetorno($arrayResultValidaJsonCredencialesCliente['strCodigo']);
            $objResponse->setContent(json_encode((array) $objConsultarSaldoResponse));
            return $objResponse;
        }
        //Se obtiene los parametros donde se escribira el log
        $arrayParametroLog['strPathTelcos'] = $this->container->getParameter('path_telcos');
        $arrayParametroLog['strPathLog']    = $this->container->getParameter('financiero.path.pagoLineaLog');
        //Valida que el directorio exista
        $arrayDirPathLogo = $this->validateDirExistCreate($arrayParametroLog);
        //Se inicializa logger
        if(false == $arrayDirPathLogo['boolResponse'])
        {
            $objLog->pushHandler(new StreamHandler($arrayParametroLog['strPathTelcos'].'/'.$arrayParametroLog['strPathLog'].'/'.
                                $dateFechaActual->format('d-M-Y').'.log', Logger::INFO));
            $objLog->addInfo('consultarSaldoAction - Request ['.$arrayRequest['codigoExternoEmpresa'].']', $arrayRequest);
        }
        try
        {
            //Obtiene los datos del cliente (anterior)
            //$arrayDatosCliente = $servicePagoLinea->obtenerConsultaSaldoClientePorIdentificacion($arrayRequest['codigoExternoEmpresa'], 
            //                                                                                     $arrayRequest['identificacionCliente']);
            //Obtiene los datos del cliente (Nuevo)
            $arrayDatosCliente = $servicePagoLinea->obtenerConsultaSaldoPorIdentificacion($arrayRequest);
            //Verifica que el cliente tenga contrato, si no lo tiene termina el metodo con un response
            if(empty($arrayDatosCliente['numeroContrato']))
            {
                $objConsultarSaldoResponse->setError('Error, el cliente ingresado no tiene contrato.');
                $objConsultarSaldoResponse->setRetorno(self::DEBT_NOT_FOUND);
                $objResponse->setContent(json_encode((array) $objConsultarSaldoResponse));
                return $objResponse;
            }
            $objConsultarSaldoResponse->setContrapartida($arrayDatosCliente['identificacionCliente']);
            $objConsultarSaldoResponse->setNombreCliente($arrayDatosCliente['nombreCliente']);
            $objConsultarSaldoResponse->setSaldoAdeudado(round($arrayDatosCliente['saldo'], 2));
            $objConsultarSaldoResponse->setNumeroCobros(1);
            $objConsultarSaldoResponse->setTipoProducto($arrayRequest['tipoProducto']);
            $objConsultarSaldoResponse->setNumeroContrato($arrayDatosCliente['numeroContrato']);
            $objConsultarSaldoResponse->setValorRetener(0.00);
            $objConsultarSaldoResponse->setBaseImponible(0.00);
            $objConsultarSaldoResponse->setPeriodoRecaudacion(date('Y-m-d H:i:s'));
            $objConsultarSaldoResponse->setIdentificacionCliente($arrayDatosCliente['identificacionCliente']);
            $objConsultarSaldoResponse->setRetorno(self::PROCESS_SUCCESS);
            $objConsultarSaldoResponse->setFormaPago($arrayDatosCliente['formaPago']);
            //Se envia a escribir al loger
            if(false == $arrayDirPathLogo['boolResponse'])
            {
                $objLog->addInfo('consultarSaldoAction - Response ['.$arrayRequest['codigoExternoEmpresa'].']', (array) $objConsultarSaldoResponse);
            }
        }
        catch(\Exception $ex)
        {
            $objConsultarSaldoResponse->setError('Error en procesos, mensaje duplicado. ' . $ex->getMessage());
            $objConsultarSaldoResponse->setRetorno(self::PROCESS_ERROR);
            //Se envia a escribir al loger
            if(false == $arrayDirPathLogo['boolResponse'])
            {
                $objLog->addError('consultarSaldoAction '.$arrayRequest['codigoExternoEmpresa'].']', array('Error' => $ex->getMessage()));
            }
        }
        $objResponse->setContent(json_encode((array) $objConsultarSaldoResponse));
        return $objResponse;
    }//consultaSaldoAction


    /**
     * Documentacion para el método 'procesarPagoAction'.
     * Metodo que procesa un pago, inserta el pago en estado pendiente en la tabla INFO_PAGO_LINEA
     *
     * @param  Request   $objRequest    Recibe parametros obligatorios como, canal, identificacionCliente,
     *                                  tipoTransaccion, usuario, clave, numeroContrato, valorPago, secuencialRecaudador
     *                                  Codigos tipoTransaccion => (100 => consultarSaldo,
                                                                    200 => procesarPago,
                                                                    300 => reversarPago,
                                                                    400 => conciliarPago,
                                                                    500 => eliminarPago)
     * @return Response  $objResponse.  Retorna un json del objeto ProcesarPagoResponse
     *
     * @author  Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 13-03-2015
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 06-10-2015
     * @since 1.0
     * @author  Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.2 24-11-2015
     * @since 1.1
     * 
     * @author Jose Bedon Sanchez <jobedon@telconet.ec>
     * @version 1.3 13-03-2020 - Se agrega campo Terminal en el campo comentario del pago en linea para control
     *                           de tarjeta de credito
     * 
     */
    public function procesarPagoAction(Request $objRequest)
    {
        $objResponse                = new Response();
        $objProcesarPagoResponse    = new ProcesarPagoResponse();
        $objResponse->headers->set('Content-Type', 'text/json');
        $arrayRequest               = json_decode($objRequest->getContent(), true);
        $servicePagoLinea           = $this->get('financiero.InfoPagoLinea');
        $objLog                     = new Logger('PagosLineaWSController');
        $dateFechaActual            = new \DateTime();
        try
        {
            //Valida credenciales, formato json, cliente existente
            $arrayResultValidaJsonCredencialesCliente = $this->validaJsonCredencialesCliente($arrayRequest, self::PROCESAR_PAGO);
            //Valida si existió un error termina el metodo con un response
            if(true === $arrayResultValidaJsonCredencialesCliente['boolResponse'])
            {
                $objProcesarPagoResponse->setError($arrayResultValidaJsonCredencialesCliente['strMensaje']);
                $objProcesarPagoResponse->setRetorno($arrayResultValidaJsonCredencialesCliente['strCodigo']);
                $objResponse->setContent(json_encode((array) $objProcesarPagoResponse));
                return $objResponse;
            }
            //Se obtiene los parametros donde se escribira el log
            $arrayParametroLog['strPathTelcos'] = $this->container->getParameter('path_telcos');
            $arrayParametroLog['strPathLog']    = $this->container->getParameter('financiero.path.pagoLineaLog');
            //Valida que el directorio exista
            $arrayDirPathLogo = $this->validateDirExistCreate($arrayParametroLog);
            //Se inicializa logger
            if(false == $arrayDirPathLogo['boolResponse'])
            {
                $objLog->pushHandler(new StreamHandler($arrayParametroLog['strPathTelcos'].'/'.$arrayParametroLog['strPathLog'].'/'.
                                    $dateFechaActual->format('d-M-Y').'.log', Logger::INFO));
                $objLog->addInfo('procesarPagoAction - Request ['.$arrayRequest['codigoExternoEmpresa'].']', $arrayRequest);
            }
            $arrayParametro['INVALID_PARAMETERS']       = self::INVALID_PARAMETERS;
            $arrayParametro['PROCESS_ERROR']            = self::PROCESS_ERROR;
            $arrayParametro['NOT_EXIST_ACCOUNT']        = self::NOT_EXIST_ACCOUNT;
            $arrayParametro['NOT_FOUND_RECORDS']        = self::NOT_FOUND_RECORDS;
            $arrayParametro['DEBT_NOT_FOUND']           = self::DEBT_NOT_FOUND;
            $arrayParametro['SERVICE_NOT_AVALIABLE']    = self::SERVICE_NOT_AVALIABLE;
            $arrayParametro['entityAdmiCanalPagoLinea'] = $arrayResultValidaJsonCredencialesCliente['entityAdmiCanalPagoLinea'];
            $arrayParametro['strUsrCreacion']           = 'telcos_pal';
            $arrayParametro['strCanal']                 = $arrayRequest['canal'];
            $arrayParametro['strNumeroContrato']        = $arrayRequest['numeroContrato'];
            $arrayParametro['intValor']                 = $arrayRequest['valorPago'];
            $arrayParametro['strSecuencialRecaudador']  = $arrayRequest['secuencialRecaudador'];
            $arrayParametro['strCodEmpresa']            = $arrayRequest['codigoExternoEmpresa'];
            $arrayParametro['strIdentificacionCliente'] = $arrayRequest['identificacionCliente'];
            $arrayParametro['dateFechaTransaccion']     = new \DateTime($arrayRequest['fechaTransaccion']);
            $arrayParametro['jsonRequest']              = json_encode($arrayRequest);
            $arrayParametro['strProceso']               = 'procesarPagoAction';
            $arrayParametro['strComentario']            = "Contrato:{$arrayRequest['numeroContrato']} - Tipo_Deuda:{$arrayRequest['tipoDeuda']} -".
                                                          " Forma_Pago1:{$arrayRequest['formaPago1']} - Valor_Pago1:{$arrayRequest['valorPago1']} - "
                                                          . "Forma_Pago2:{$arrayRequest['formaPago2']} - Valor_Pago2:{$arrayRequest['valorPago2']} - "
                                                          . "Tipo_Producto:{$arrayRequest['tipoProducto']} - Terminal:{$arrayRequest['terminal']}";
            //Genera el pago
            $arrayPagoLinea = $servicePagoLinea->generaPagoLinea($arrayParametro);
            //Si existio un error al generar el pago termina el metodo con un response
            if(true === $arrayPagoLinea['boolResponse'])
            {
                //Se envia a escribir al loger
                if(false === $arrayDirPathLogo['boolResponse'])
                {
                    $objLog->addError('procesarPagoAction ['.$arrayRequest['codigoExternoEmpresa'].']', $arrayRequest);
                }
                $objProcesarPagoResponse->setError('Error. '.$arrayPagoLinea['strMensaje']);
                $objProcesarPagoResponse->setRetorno($arrayPagoLinea['strCodigo']);
                $objResponse->setContent(json_encode((array) $objProcesarPagoResponse));
                $objLog->addError('procesarPagoAction - Response ['.$arrayRequest['codigoExternoEmpresa'].']', (array) $objProcesarPagoResponse);
                return $objResponse;
            }
            $arrayParametroCorreo['strIdentificacionCliente'] = $arrayRequest['identificacionCliente'];
            $arrayParametroCorreo['intLimitLengthMail']       = 500;
            $arrayParametroCorreo['strTodos']                 = 'PrimerPunto';
            $arrayParametroCorreo['strTipoDato']              = 'MAIL';
            $arrayParametroCorreo['strDatoDefault']           = 'notificaciones_telcos@telconet.ec';
            $arrayParametroCorreo['arrayEstadoPersona']       = ['Activo', 'Cancelado'];
            $strCorreo                                        = $servicePagoLinea->obtieneMailFonoPorIdentificacionCliente($arrayParametroCorreo);
            $objProcesarPagoResponse->setContrapartida($arrayRequest['numeroContrato']);
            $objProcesarPagoResponse->setNombreCliente($arrayPagoLinea['arrayInfoCliente']['nombreCliente']);
            $objProcesarPagoResponse->setFechaTransaccion($arrayPagoLinea['entityPagoLinea']->getFeCreacion()->format('Y-m-d H:i:s'));
            $objProcesarPagoResponse->setSecuencialEntidadRecaudadora($arrayRequest['secuencialRecaudador']);
            $objProcesarPagoResponse->setSecuencialPagoInterno($arrayPagoLinea['entityPagoLinea']->getId());
            $objProcesarPagoResponse->setNumeroPagos(1);
            $objProcesarPagoResponse->setValorPago($arrayRequest['valorPago']);
            $objProcesarPagoResponse->setObservacion('Exito.');
            $objProcesarPagoResponse->setMensajeVoucher('Exito.');
            $objProcesarPagoResponse->setSaldo($arrayPagoLinea['arrayInfoCliente']['saldo']);
            $objProcesarPagoResponse->setCorreo($strCorreo);
            $objProcesarPagoResponse->setTipoProducto($arrayRequest['tipoProducto']);
            $objProcesarPagoResponse->setRetorno(self::PROCESS_SUCCESS);
            //Se envia a escribir al loger
            if(false == $arrayDirPathLogo['boolResponse'])
            {
                $objLog->addInfo('procesarPagoAction - Response ['.$arrayRequest['codigoExternoEmpresa'].']', (array) $objProcesarPagoResponse);
            }
        }
        catch(\Exception $ex)
        {
            //Se envia a escribir al loger
            if(false == $arrayDirPathLogo['boolResponse'])
            {
                $objLog->addError('procesarPagoAction ['.$arrayRequest['codigoExternoEmpresa'].']', array('Error' => $ex->getMessage()));
            }
            $objProcesarPagoResponse->setError('Error, Indisponibilidad del sistema. Error - ' . $ex->getMessage());
            $objProcesarPagoResponse->setRetorno(self::SERVICE_NOT_AVALIABLE);
        }
        $objResponse->setContent(json_encode((array) $objProcesarPagoResponse));
        return $objResponse;
    }//procesarPagoAction

    /**
     * Documentacion para el método 'reversarPagoAction'.
     * Método que reversa un pago en estado Pendiente, reversa el pago siempre y cuando este en estado Pendiente.
     * El método puede realizar internamente el reverso o reverso y la eliminación de un pago siempre que el parametro accion este declarado 
     * como "REVERSAR_ELIMINAR"
     *
     * @param  Request   $objRequest    Recibe parametros obligatorios como, canal, identificacionCliente,
     *                                  tipoTransaccion, usuario, clave, numeroContrato, valorPago, secuencialRecaudador, fechaTransaccion
     *                                  Codigos tipoTransaccion => (100 => consultarSaldo,
                                                                    200 => procesarPago,
                                                                    300 => reversarPago,
                                                                    400 => conciliarPago,
                                                                    500 => eliminarPago)
     * @return Response  $objResponse.  Retorna un json del objeto ReversarPagoResponse
     *
     * @author  Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 13-03-2015
     * @author  Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 24-11-2015
     * @since 1.0
     */
    public function reversarPagoAction(Request $objRequest)
    {
        $objResponse                = new Response();
        $objReversarPagoResponse    = new ReversarPagoResponse();
        $objResponse->headers->set('Content-Type', 'text/json');
        $arrayRequest               = json_decode($objRequest->getContent(), true);
        $servicePagoLinea           = $this->get('financiero.InfoPagoLinea');
        $objLog                     = new Logger('PagosLineaWSController');
        $dateFechaActual            = new \DateTime();
        //Valida credenciales, formato json, cliente existente
        $arrayResultValidaJsonCredencialesCliente = $this->validaJsonCredencialesCliente($arrayRequest, self::REVERSAR_PAGO);
        //Valida si existió un error termina el metodo con un response
        if(true === $arrayResultValidaJsonCredencialesCliente['boolResponse'])
        {
            $objReversarPagoResponse->setError($arrayResultValidaJsonCredencialesCliente['strMensaje']);
            $objReversarPagoResponse->setRetorno($arrayResultValidaJsonCredencialesCliente['strCodigo']);
            $objResponse->setContent(json_encode((array) $objReversarPagoResponse));
            return $objResponse;
        }
        try
        {
            //Se obtiene los parametros donde se escribira el log
            $arrayParametroLog['strPathTelcos'] = $this->container->getParameter('path_telcos');
            $arrayParametroLog['strPathLog']    = $this->container->getParameter('financiero.path.pagoLineaLog');
            //Valida que el directorio exista
            $arrayDirPathLogo = $this->validateDirExistCreate($arrayParametroLog);
            //Se inicializa logger
            if(false === $arrayDirPathLogo['boolResponse'])
            {
                $objLog->pushHandler(new StreamHandler($arrayParametroLog['strPathTelcos'].'/'.$arrayParametroLog['strPathLog'].'/'.
                                    $dateFechaActual->format('d-M-Y').'.log', Logger::INFO));
                $objLog->addInfo('reversarPagoAction - Request ['.$arrayRequest['codigoExternoEmpresa'].']', $arrayRequest);
            }
            $arrayParametro['TRANSACTION_NOT_EXIST']    = self::NOT_FOUND_RECORDS;
            $arrayParametro['TRANSACTION_REVERSED']     = self::TRANSACTION_REVERSED;
            $arrayParametro['SERVICE_NOT_AVALIABLE']    = self::SERVICE_NOT_AVALIABLE;
            $arrayParametro['RECONCILIED_PAYMENT']      = self::RECONCILIED_PAYMENT;
            $arrayParametro['strCanal']                 = $arrayRequest['canal'];
            $arrayParametro['strUsrUltMod']             = 'telcos_pal';
            $arrayParametro['intValor']                 = $arrayRequest['valorPago'];
            $arrayParametro['strSecuencialRecaudador']  = $arrayRequest['secuencialRecaudador'];
            $arrayParametro['entityAdmiCanalPagoLinea'] = $arrayResultValidaJsonCredencialesCliente['entityAdmiCanalPagoLinea'];
            $arrayParametro['strCodEmpresa']            = $arrayRequest['codigoExternoEmpresa'];
            $arrayParametro['strIdentificacionCliente'] = $arrayRequest['identificacionCliente'];
            $arrayParametro['strAccion']                = $arrayRequest['accion'];
            $arrayParametro['jsonRequest']              = json_encode($arrayRequest);
            $arrayParametro['strProceso']               = 'reversarPagoAction';
            $arrayParametro['dateFechaTransaccion']     = new \DateTime($arrayRequest['fechaTransaccion']);
            //Reversa el pago
            $arrayPagoLinea = $servicePagoLinea->reversaPagoLinea($arrayParametro);
            //Si existio un error al reversar termina el metodo con un response
            if(true === $arrayPagoLinea['boolResponse'])
            {
                //Se envia a escribir al loger
                if(false === $arrayDirPathLogo['boolResponse'])
                {
                    $objLog->addError('reversarPagoAction ['.$arrayRequest['codigoExternoEmpresa'].']', $arrayRequest);
                }
                $objReversarPagoResponse->setError('Error. '.$arrayPagoLinea['strMensaje']);
                $objReversarPagoResponse->setRetorno($arrayPagoLinea['strCodigo']);
                $objResponse->setContent(json_encode((array) $objReversarPagoResponse));
                return $objResponse;
            }
            $objReversarPagoResponse->setContrapartida($arrayRequest['numeroContrato']);
            $objReversarPagoResponse->setNombreCliente($arrayPagoLinea['arrayInfoCliente']['nombreCliente']);
            $objReversarPagoResponse->setFechaTransaccion(date('Y-m-d H:i:s'));
            $objReversarPagoResponse->setSecuencialEntidadRecaudadora($arrayRequest['secuencialRecaudador']);
            $objReversarPagoResponse->setSecuencialPagoInterno($arrayPagoLinea['entityPagoLinea']->getId());
            $objReversarPagoResponse->setNumeroPagos(1);
            $objReversarPagoResponse->setValorTotalReversado($arrayRequest['valorPago']);
            $objReversarPagoResponse->setObservacion('Exito.');
            $objReversarPagoResponse->setMensajeVoucher('Exito.');
            $objReversarPagoResponse->setSaldo($arrayPagoLinea['arrayInfoCliente']['saldo']);
            $objReversarPagoResponse->setRetorno(self::PROCESS_SUCCESS);
            //Se envia a escribir al loger
            if(false === $arrayDirPathLogo['boolResponse'])
            {
                $objLog->addInfo('reversarPagoAction ['.$arrayRequest['codigoExternoEmpresa'].']', (array) $objReversarPagoResponse);
            }
        }
        catch(\Exception $ex)
        {
            //Se envia a escribir al loger
            if(false === $arrayDirPathLogo['boolResponse'])
            {
                $objLog->addError('reversarPagoAction ['.$arrayRequest['codigoExternoEmpresa'].']', array('Error' => $ex->getMessage()));
            }
            $objReversarPagoResponse->setError('Error, Indisponibilidad del sistema. ' . $ex->getMessage());
            $objReversarPagoResponse->setRetorno(self::SERVICE_NOT_AVALIABLE);
        }
        $objResponse->setContent(json_encode((array) $objReversarPagoResponse));
        return $objResponse;
    }//reversarPagoAction

    /**
     * Documentacion para el método 'reversarPagoAction'.
     * Método que reversa un pago en estado Pendiente, reversa el pago siempre y cuando este en estado Pendiente.
     * El método puede realizar internamente el reverso o reverso y la eliminación de un pago siempre que el parametro accion este declarado 
     * como "REVERSAR_ELIMINAR"
     *
     * @param  Request   $objRequest    Recibe parametros obligatorios como, canal, identificacionCliente,
     *                                  tipoTransaccion, usuario, clave, numeroContrato, valorPago, secuencialRecaudador, fechaTransaccion
     *                                  Codigos tipoTransaccion => (100 => consultarSaldo,
                                                                    200 => procesarPago,
                                                                    300 => reversarPago,
                                                                    400 => conciliarPago,
                                                                    500 => eliminarPago)
     * @return Response  $objResponse.  Retorna un json del objeto ReversarPagoResponse
     *
     * @author  Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 13-03-2015
     * @author  Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 24-11-2015
     * @since 1.0
     */
    public function reversarPagoConciliadoAction(Request $objRequest)
    {
        ini_set('max_execution_time', 60);
        $objResponse                = new Response();
        $objReversarPagoResponse    = new ReversarPagoResponse();
        $objResponse->headers->set('Content-Type', 'text/json');
        $arrayRequest               = json_decode($objRequest->getContent(), true);
        $servicePagoLinea           = $this->get('financiero.InfoPagoLinea');
        $objLog                     = new Logger('PagosLineaWSController');
        $objFechaActual             = new \DateTime();


        //Valida credenciales, formato json, cliente existente
        $arrayResultValidaJsonCredencialesCliente = $this->validaJsonCredencialesCliente($arrayRequest, self::REVERSAR_PAGO);
        //Valida si existió un error termina el metodo con un response
        if(true === $arrayResultValidaJsonCredencialesCliente['boolResponse'])
        {
            $objReversarPagoResponse->setError($arrayResultValidaJsonCredencialesCliente['strMensaje']);
            $objReversarPagoResponse->setRetorno($arrayResultValidaJsonCredencialesCliente['strCodigo']);
            $objResponse->setContent(json_encode((array) $objReversarPagoResponse));
            return $objResponse;
        }
        try
        {
            //Se obtiene los parametros donde se escribira el log
            $arrayParametroLog['strPathTelcos'] = $this->container->getParameter('path_telcos');
            $arrayParametroLog['strPathLog']    = $this->container->getParameter('financiero.path.pagoLineaLog');
            //Valida que el directorio exista
            $arrayDirPathLogo = $this->validateDirExistCreate($arrayParametroLog);
            //Se inicializa logger
            if(false === $arrayDirPathLogo['boolResponse'])
            {
                $objLog->pushHandler(new StreamHandler($arrayParametroLog['strPathTelcos'].'/'.$arrayParametroLog['strPathLog'].'/'.
                                    $objFechaActual->format('d-M-Y').'.log', Logger::INFO));
                $objLog->addInfo('reversarPagoConciliadoAction - Request ['.$arrayRequest['codigoExternoEmpresa'].']', $arrayRequest);
            }
            $arrayParametro['TRANSACTION_NOT_EXIST']    = self::NOT_FOUND_RECORDS;
            $arrayParametro['TRANSACTION_REVERSED']     = self::TRANSACTION_REVERSED;
            $arrayParametro['SERVICE_NOT_AVALIABLE']    = self::SERVICE_NOT_AVALIABLE;
            $arrayParametro['RECONCILIED_PAYMENT']      = self::RECONCILIED_PAYMENT;
            $arrayParametro['PROCESS_SUCCESS']          = self::PROCESS_SUCCESS;
            $arrayParametro['PROCESS_ERROR']            = self::PROCESS_ERROR;
            $arrayParametro['strCanal']                 = $arrayRequest['canal'];
            $arrayParametro['strUsrUltMod']             = 'telcos_pal';
            $arrayParametro['intValor']                 = $arrayRequest['valorPago'];
            $arrayParametro['strSecuencialRecaudador']  = $arrayRequest['secuencialRecaudador'];
            $arrayParametro['entityAdmiCanalPagoLinea'] = $arrayResultValidaJsonCredencialesCliente['entityAdmiCanalPagoLinea'];
            $arrayParametro['strCodEmpresa']            = $arrayRequest['codigoExternoEmpresa'];
            $arrayParametro['strIdentificacionCliente'] = $arrayRequest['identificacionCliente'];
            $arrayParametro['strAccion']                = $arrayRequest['accion'];
            $arrayParametro['jsonRequest']              = json_encode($arrayRequest);
            $arrayParametro['strProceso']               = 'reversarPagoAction';
            $arrayParametro['dateFechaTransaccion']     = new \DateTime($arrayRequest['fechaTransaccion']);
            //Reversa el pago
            $arrayPagoLinea = $servicePagoLinea->reversaConciliadoPagoLinea($arrayParametro);
            //Si existio un error al reversar termina el metodo con un response
            if(true === $arrayPagoLinea['boolResponse'])
            {
                //Se envia a escribir al logger
                if(false === $arrayDirPathLogo['boolResponse'])
                {
                    $objLog->addError('reversarPagoConciliadoAction ['.$arrayRequest['codigoExternoEmpresa'].']', $arrayRequest);
                }
                $objReversarPagoResponse->setError('Error. '.$arrayPagoLinea['strMensaje']);
                $objReversarPagoResponse->setRetorno($arrayPagoLinea['strCodigo']);
                $objResponse->setContent(json_encode((array) $objReversarPagoResponse));
                return $objResponse;
            }
            $objReversarPagoResponse->setContrapartida($arrayRequest['numeroContrato']);
            $objReversarPagoResponse->setNombreCliente($arrayPagoLinea['arrayInfoCliente']['nombreCliente']);
            $objReversarPagoResponse->setFechaTransaccion(date('Y-m-d H:i:s'));
            $objReversarPagoResponse->setSecuencialEntidadRecaudadora($arrayRequest['secuencialRecaudador']);
            $objReversarPagoResponse->setSecuencialPagoInterno($arrayPagoLinea['entityPagoLinea']->getId());
            $objReversarPagoResponse->setNumeroPagos(1);
            $objReversarPagoResponse->setValorTotalReversado($arrayRequest['valorPago']);
            $objReversarPagoResponse->setObservacion('Exito.');
            $objReversarPagoResponse->setMensajeVoucher('Exito.');
            $objReversarPagoResponse->setSaldo($arrayPagoLinea['arrayInfoCliente']['saldo']);
            $objReversarPagoResponse->setRetorno(self::PROCESS_SUCCESS);
            //Se envia a escribir al loger
            if(false === $arrayDirPathLogo['boolResponse'])
            {
                $objLog->addInfo('reversarPagoConciliadoAction ['.$arrayRequest['codigoExternoEmpresa'].']', (array) $objReversarPagoResponse);
            }
        }
        catch(\Exception $ex)
        {
            //Se envia a escribir al loger
            if(false === $arrayDirPathLogo['boolResponse'])
            {
                $objLog->addError('reversarPagoConciliadoAction ['.$arrayRequest['codigoExternoEmpresa'].']', array('Error' => $ex->getMessage()));
            }
            $objReversarPagoResponse->setError('Error, Indisponibilidad del sistema. ' . $ex->getMessage());
            $objReversarPagoResponse->setRetorno(self::SERVICE_NOT_AVALIABLE);
        }
        $objResponse->setContent(json_encode((array) $objReversarPagoResponse));
        return $objResponse;
    }//reversarPagoConciliado

    /**
     * Documentacion para el método 'conciliarPagoAction'.
     * Metodo que concilia un pago, concilia el pago siempre y cuando se encuentre en estado Pendiente y este fuera de la ventana de 15 minutos
     *
     * @param  Request   $objRequest    Recibe parametros obligatorios como, canal, identificacionCliente,
     *                                  tipoTransaccion, usuario, clave, secuencialPagoInterno, valorPago, secuencialRecaudador, fechaTransaccion
     *                                  Codigos tipoTransaccion => (100 => consultarSaldo,
                                                                    200 => procesarPago,
                                                                    300 => reversarPago,
                                                                    400 => conciliarPago,
                                                                    500 => eliminarPago )
     * @return Response  $objResponse.  Retorna un json del objeto ConciliarPagoResponse
     *
     * @author  Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 13-03-2015
     * @author  Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 24-11-2015
     * @since 1.0
     */
    public function conciliarPagoAction(Request $objRequest)
    {
        $objResponse                = new Response();
        $objConciliarPagoResponse   = new ConciliarPagoResponse();
        $objResponse->headers->set('Content-Type', 'text/json');
        $arrayRequest               = json_decode($objRequest->getContent(), true);
        $servicePagoLinea           = $this->get('financiero.InfoPagoLinea');
        $objLog                     = new Logger('PagosLineaWSController');
        $dateFechaActual            = new \DateTime();
        try
        {
            //Valida credenciales, formato json, cliente existente
            $arrayResultValidaJsonCredencialesCliente = $this->validaJsonCredencialesCliente($arrayRequest, self::CONCILIAR_PAGO);
            //Valida si existió un error termina el metodo con un response
            if(true === $arrayResultValidaJsonCredencialesCliente['boolResponse'])
            {
                $objConciliarPagoResponse->setError($arrayResultValidaJsonCredencialesCliente['strMensaje']);
                $objConciliarPagoResponse->setRetorno($arrayResultValidaJsonCredencialesCliente['strCodigo']);
                $objResponse->setContent(json_encode((array) $objConciliarPagoResponse));
                return $objResponse;
            }
            //Se obtiene los parametros donde se escribira el log
            $arrayParametroLog['strPathTelcos'] = $this->container->getParameter('path_telcos');
            $arrayParametroLog['strPathLog']    = $this->container->getParameter('financiero.path.pagoLineaLog');
            //Valida que el directorio exista
            $arrayDirPathLogo = $this->validateDirExistCreate($arrayParametroLog);
            //Se inicializa logger
            if(false === $arrayDirPathLogo['boolResponse'])
            {
                $objLog->pushHandler(new StreamHandler($arrayParametroLog['strPathTelcos'].'/'.$arrayParametroLog['strPathLog'].'/'.
                                    $dateFechaActual->format('d-M-Y').'.log', Logger::INFO));
                $objLog->addInfo('conciliarPagoAction - Request ['.$arrayRequest['codigoExternoEmpresa'].']', $arrayRequest);
            }
            $arrayParametro['TRANSACTION_NOT_EXIST']    = self::NOT_FOUND_RECORDS;
            $arrayParametro['PROCESS_ERROR']            = self::PROCESS_ERROR;
            $arrayParametro['SERVICE_NOT_AVALIABLE']    = self::SERVICE_NOT_AVALIABLE;
            $arrayParametro['TRANSACTION_REVERSED']     = self::TRANSACTION_REVERSED;
            $arrayParametro['RECONCILIED_PAYMENT']      = self::RECONCILIED_PAYMENT;
            $arrayParametro['strCanal']                 = $arrayRequest['canal'];
            $arrayParametro['strUsrUltMod']             = 'telcos_pal';
            $arrayParametro['intValor']                 = $arrayRequest['valorPago'];
            $arrayParametro['strSecuencialRecaudador']  = $arrayRequest['secuencialRecaudador'];
            $arrayParametro['strCodEmpresa']            = $arrayRequest['codigoExternoEmpresa'];
            $arrayParametro['entityAdmiCanalPagoLinea'] = $arrayResultValidaJsonCredencialesCliente['entityAdmiCanalPagoLinea'];
            $arrayParametro['strIdentificacionCliente'] = $arrayRequest['identificacionCliente'];
            $arrayParametro['dateFechaTransaccion']     = new \DateTime($arrayRequest['fechaTransaccion']);
            $arrayParametro['jsonRequest']              = json_encode($arrayRequest);
            $arrayParametro['strProceso']               = 'conciliarPagoAction';
            $arrayPagoLinea                             = $servicePagoLinea->conciliaPagoLinea($arrayParametro);
            if(true === $arrayPagoLinea['boolResponse'])
            {
                //Se envia a escribir al loger
                if(false === $arrayDirPathLogo['boolResponse'])
                {
                    $objLog->addError('conciliarPagoAction ['.$arrayRequest['codigoExternoEmpresa'].']', $arrayPagoLinea);
                }
                $objConciliarPagoResponse->setError('Error. '.$arrayPagoLinea['strMensaje']);
                $objConciliarPagoResponse->setRetorno($arrayPagoLinea['strCodigo']);
                $objResponse->setContent(json_encode((array) $objConciliarPagoResponse));
                return $objResponse;
            }
            $objConciliarPagoResponse->setRetorno(self::PROCESS_SUCCESS);
            $objConciliarPagoResponse->setMensaje('Exito.');
            $objConciliarPagoResponse->setSecuencialRecaudador($arrayPagoLinea['entityPagoLinea']->getNumeroReferencia());
            $objConciliarPagoResponse->setSecuencialPagoInterno($arrayPagoLinea['entityPagoLinea']->getId());
            $objConciliarPagoResponse->setFechaConciliacion($arrayPagoLinea['entityPagoLinea']->getFeUltMod()->format('Y-m-d H:i:s'));
            $objConciliarPagoResponse->setFechaTransaccionPago($arrayPagoLinea['entityPagoLinea']->getFeCreacion()->format('Y-m-d H:i:s'));
            //Se envia a escribir al loger
            if(false === $arrayDirPathLogo['boolResponse'])
            {
                $objLog->addInfo('conciliarPagoAction - Response ['.$arrayRequest['codigoExternoEmpresa'].']', (array) $objConciliarPagoResponse);
            }
        }
        catch(\Exception $ex)
        {
            //Se envia a escribir al loger
            if(false === $arrayDirPathLogo['boolResponse'])
            {
                $objLog->addInfo('conciliarPagoAction ['.$arrayRequest['codigoExternoEmpresa'].']', array('Error' => $ex->getMessage()));
            }
            $objConciliarPagoResponse->setError('Error, Indisponibilidad del sistema. ' . $ex->getMessage());
            $objConciliarPagoResponse->setRetorno(self::SERVICE_NOT_AVALIABLE);
        }
        $objResponse->setContent(json_encode((array) $objConciliarPagoResponse));
        return $objResponse;
    }//conciliarPagoAction
    
    /**
     * Documentacion para el método 'eliminarPagoAction'.
     * Metodo que elimina un pago en estado Reversado.
     *
     * @param  Request   $objRequest    Recibe parametros obligatorios como, canal, identificacionCliente,
     *                                  tipoTransaccion, usuario, clave, numeroContrato, valorPago, secuencialRecaudador, fechaTransaccion
     *                                  Codigos tipoTransaccion => (100 => consultarSaldo,
                                                                    200 => procesarPago,
                                                                    300 => reversarPago,
                                                                    400 => conciliarPago,
                                                                    500 => eliminarPago)
     * @return Response  $objResponse.  Retorna un json del objeto EliminarPagoResponse
     *
     * @author  Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 13-03-2015
     * @author  Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 24-11-2015
     * @since 1.0
     */
    public function eliminarPagoAction(Request $objRequest)
    {
        $objResponse                = new Response();
        $objEliminarPagoResponse    = new EliminarPagoResponse();
        $objResponse->headers->set('Content-Type', 'text/json');
        $arrayRequest               = json_decode($objRequest->getContent(), true);
        $servicePagoLinea           = $this->get('financiero.InfoPagoLinea');
        $objLog                     = new Logger('PagosLineaWSController');
        $dateFechaActual            = new \DateTime();
        //Valida credenciales, formato json, cliente existente
        $arrayResultValidaJsonCredencialesCliente = $this->validaJsonCredencialesCliente($arrayRequest, self::ELIMINAR_PAGO);
        if(true === $arrayResultValidaJsonCredencialesCliente['boolResponse'])
        {
            $objEliminarPagoResponse->setError($arrayResultValidaJsonCredencialesCliente['strMensaje']);
            $objEliminarPagoResponse->setRetorno($arrayResultValidaJsonCredencialesCliente['strCodigo']);
            $objResponse->setContent(json_encode((array) $objEliminarPagoResponse));
            return $objResponse;
        }
        try
        {
            //Se obtiene los parametros donde se escribira el log
            $arrayParametroLog['strPathTelcos'] = $this->container->getParameter('path_telcos');
            $arrayParametroLog['strPathLog']    = $this->container->getParameter('financiero.path.pagoLineaLog');
            //Valida que el directorio exista
            $arrayDirPathLogo = $this->validateDirExistCreate($arrayParametroLog);
            //Se inicializa logger
            if(false === $arrayDirPathLogo['boolResponse'])
            {
                $objLog->pushHandler(new StreamHandler($arrayParametroLog['strPathTelcos'].'/'.$arrayParametroLog['strPathLog'].'/'.
                                    $dateFechaActual->format('d-M-Y').'.log', Logger::INFO));
                $objLog->addInfo('eliminarPagoAction - Request ['.$arrayRequest['codigoExternoEmpresa'].']', $arrayRequest);
            }
            $arrayParametro['TRANSACTION_NOT_EXIST']    = self::NOT_FOUND_RECORDS;
            $arrayParametro['PROCESS_ERROR']            = self::PROCESS_ERROR;
            $arrayParametro['SERVICE_NOT_AVALIABLE']    = self::SERVICE_NOT_AVALIABLE;
            $arrayParametro['TRANSACTION_REVERSED']     = self::TRANSACTION_REVERSED;
            $arrayParametro['RECONCILIED_PAYMENT']      = self::RECONCILIED_PAYMENT;
            $arrayParametro['strCanal']                 = $arrayRequest['canal'];
            $arrayParametro['strUsrUltMod']             = 'telcos_pal';
            $arrayParametro['intValor']                 = $arrayRequest['valorPago'];
            $arrayParametro['strSecuencialRecaudador']  = $arrayRequest['secuencialRecaudador'];
            $arrayParametro['strCodEmpresa']            = $arrayRequest['codigoExternoEmpresa'];
            $arrayParametro['strIdentificacionCliente'] = $arrayRequest['identificacionCliente'];
            $arrayParametro['dateFechaTransaccion']     = new \DateTime($arrayRequest['fechaTransaccion']);
            $arrayParametro['entityAdmiCanalPagoLinea'] = $arrayResultValidaJsonCredencialesCliente['entityAdmiCanalPagoLinea'];
            $arrayParametro['jsonRequest']              = json_encode($arrayRequest);
            $arrayParametro['strProceso']               = 'eliminarPagoAction';
            //Reversa el pago, obteniendo un string o null como respuesta
            $arrayPagoLinea = $servicePagoLinea->eliminaPagoLinea($arrayParametro);
            if(true === $arrayPagoLinea['boolResponse'])
            {
                //Se envia a escribir al loger
                if(false === $arrayDirPathLogo['boolResponse'])
                {
                    $objLog->addError('eliminarPagoAction ['.$arrayRequest['codigoExternoEmpresa'].']', $arrayRequest);
                }
                $objEliminarPagoResponse->setError('Error. '.$arrayPagoLinea['strMensaje']);
                $objEliminarPagoResponse->setRetorno($arrayPagoLinea['strCodigo']);
                $objResponse->setContent(json_encode((array) $objEliminarPagoResponse));
                return $objResponse;
            }
            $objEliminarPagoResponse->setContrapartida($arrayRequest['numeroContrato']);
            $objEliminarPagoResponse->setNombreCliente($arrayPagoLinea['arrayInfoCliente']['nombreCliente']);
            $objEliminarPagoResponse->setFechaTransaccion(date('Y-m-d H:i:s'));
            $objEliminarPagoResponse->setSecuencialEntidadRecaudadora($arrayRequest['secuencialRecaudador']);
            $objEliminarPagoResponse->setSecuencialPagoInterno($arrayPagoLinea['entityPagoLinea']->getId());
            $objEliminarPagoResponse->setNumeroPagos(1);
            $objEliminarPagoResponse->setValorTotalReversado($arrayRequest['valorPago']);
            $objEliminarPagoResponse->setObservacion('Exito.');
            $objEliminarPagoResponse->setMensajeVoucher('Exito.');
            $objEliminarPagoResponse->setSaldo($arrayPagoLinea['arrayInfoCliente']['saldo']);
            $objEliminarPagoResponse->setRetorno(self::PROCESS_SUCCESS);
            //Se envia a escribir al loger
            if(false === $arrayDirPathLogo['boolResponse'])
            {
                $objLog->addInfo('eliminarPagoAction - Response ['.$arrayRequest['codigoExternoEmpresa'].']', (array) $objEliminarPagoResponse);
            }
        }
        catch(\Exception $ex)
        {
            //Se envia a escribir al loger
            if(false === $arrayDirPathLogo['boolResponse'])
            {
                $objLog->addError('eliminarPagoAction ['.$arrayRequest['codigoExternoEmpresa'].']', array('Error' => $ex->getMessage()));
            }
            $objEliminarPagoResponse->setError('Error, Indisponibilidad del sistema. ' . $ex->getMessage());
            $objEliminarPagoResponse->setRetorno(self::SERVICE_NOT_AVALIABLE);
        }
        $objResponse->setContent(json_encode((array) $objEliminarPagoResponse));
        return $objResponse;
    }//eliminarPagoAction
    
    /**
     * Documentacion para el método 'anularPagoAction'.
     * Metodo que anula un pago en estado Pendiente.
     *
     * @param  Request   $objRequest    Recibe parametros obligatorios como, canal, identificacionCliente,
     *                                  tipoTransaccion, usuario, clave, numeroContrato, valorPago, secuencialRecaudador, fechaTransaccion
     *                                  Codigos tipoTransaccion => (100 => consultarSaldo,
                                                                    200 => procesarPago,
                                                                    300 => reversarPago,
                                                                    400 => conciliarPago,
                                                                    500 => eliminarPago,
                                                                    800 => anularPago)
     * @return Response  $objResponse.  Retorna un json del objeto EliminarPagoResponse
     *
     * @author  Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 13-03-2015
     */
    public function anularPagoAction(Request $objRequest)
    {
        $objResponse            = new Response();
        $objAnularPagoResponse  = new AnularPagoResponse();
        $objResponse->headers->set('Content-Type', 'text/json');
        $arrayRequest           = json_decode($objRequest->getContent(), true);
        $servicePagoLinea       = $this->get('financiero.InfoPagoLinea');
        $objLog                 = new Logger('PagosLineaWSController');
        $dateFechaActual        = new \DateTime();
        //Valida credenciales, formato json, cliente existente
        $arrayResultValidaJsonCredencialesCliente = $this->validaJsonCredencialesCliente($arrayRequest, self::ANULAR_PAGO);
        if(true === $arrayResultValidaJsonCredencialesCliente['boolResponse'])
        {
            $objAnularPagoResponse->setError($arrayResultValidaJsonCredencialesCliente['strMensaje']);
            $objAnularPagoResponse->setRetorno($arrayResultValidaJsonCredencialesCliente['strCodigo']);
            $objResponse->setContent(json_encode((array) $objAnularPagoResponse));
            return $objResponse;
        }
        try
        {
            //Se obtiene los parametros donde se escribira el log
            $arrayParametroLog['strPathTelcos'] = $this->container->getParameter('path_telcos');
            $arrayParametroLog['strPathLog']    = $this->container->getParameter('financiero.path.pagoLineaLog');
            //Valida que el directorio exista
            $arrayDirPathLogo = $this->validateDirExistCreate($arrayParametroLog);
            //Se inicializa logger
            if(false === $arrayDirPathLogo['boolResponse'])
            {
                $objLog->pushHandler(new StreamHandler($arrayParametroLog['strPathTelcos'].'/'.$arrayParametroLog['strPathLog'].'/'.
                                    $dateFechaActual->format('d-M-Y').'.log', Logger::INFO));
                $objLog->addInfo('anularPagoAction - Request ['.$arrayRequest['codigoExternoEmpresa'].']', $arrayRequest);
            }
            $arrayParametro['TRANSACTION_NOT_EXIST']    = self::TRANSACTION_NOT_EXIST;
            $arrayParametro['SERVICE_NOT_AVALIABLE']    = self::SERVICE_NOT_AVALIABLE;
            $arrayParametro['CANCEL_PAYMENT']           = self::CANCEL_PAYMENT;
            $arrayParametro['PROCESS_ERROR']            = self::PROCESS_ERROR;
            $arrayParametro['strCanal']                 = $arrayRequest['canal'];
            $arrayParametro['strUsrUltMod']             = 'telcos_pal';
            $arrayParametro['strSecuencialRecaudador']  = $arrayRequest['secuencialRecaudador'];
            $arrayParametro['strCodEmpresa']            = $arrayRequest['codigoExternoEmpresa'];
            $arrayParametro['strIdentificacionCliente'] = $arrayRequest['identificacionCliente'];
            $arrayParametro['intValor']                 = $arrayRequest['valorPago'];
            $arrayParametro['entityAdmiCanalPagoLinea'] = $arrayResultValidaJsonCredencialesCliente['entityAdmiCanalPagoLinea'];
            $arrayParametro['jsonRequest']              = json_encode($arrayRequest);
            $arrayParametro['strProceso']               = 'anularPagoAction';
            
            //Envia al service a anular el pago en linea
            $arrayPagoLinea = $servicePagoLinea->anulaPagoLinea($arrayParametro);
            
            //Si existe un error al anular el pago termina el metodo con un response
            if(true === $arrayPagoLinea['boolResponse'])
            {
                //Se envia a escribir al loger
                if(false === $arrayDirPathLogo['boolResponse'])
                {
                    $objLog->addError('anularPagoAction ['.$arrayRequest['codigoExternoEmpresa'].']', $arrayRequest);
                }
                $objAnularPagoResponse->setError('Error. '.$arrayPagoLinea['strMensaje']);
                $objAnularPagoResponse->setRetorno($arrayPagoLinea['strCodigo']);
                $objResponse->setContent(json_encode((array) $objAnularPagoResponse));
                return $objResponse;
            }
            $objAnularPagoResponse->setContrapartida($arrayPagoLinea['arrayInfoCliente']['numeroContrato']);
            $objAnularPagoResponse->setNombreCliente($arrayPagoLinea['arrayInfoCliente']['nombreCliente']);
            $objAnularPagoResponse->setFechaTransaccion($arrayPagoLinea['entityPagoLinea']->getFeUltMod()->format('Y-m-d H:i:s'));
            $objAnularPagoResponse->setSecuencialRecaudador($arrayRequest['secuencialRecaudador']);
            $objAnularPagoResponse->setSecuencialPagoInterno($arrayPagoLinea['entityPagoLinea']->getId());
            $objAnularPagoResponse->setIdentificacionCliente($arrayRequest['identificacionCliente']);
            $objAnularPagoResponse->setMensaje('Se realizo la anulacion con exito.');
            $objAnularPagoResponse->setRetorno(self::PROCESS_SUCCESS);
            //Se envia a escribir al loger
            if(false === $arrayDirPathLogo['boolResponse'])
            {
                $objLog->addInfo('anularPagoAction - Response ['.$arrayRequest['codigoExternoEmpresa'].']', (array) $objAnularPagoResponse);
            }
        }
        catch(\Exception $ex)
        {
            //Se envia a escribir al loger
            if(false === $arrayDirPathLogo['boolResponse'])
            {
                $objLog->addError('anularPagoAction ['.$arrayRequest['codigoExternoEmpresa'].']', array('Error' => $ex->getMessage()));
            }
            $objAnularPagoResponse->setError('Error, Indisponibilidad del sistema. ' . $ex->getMessage());
            $objAnularPagoResponse->setRetorno(self::SERVICE_NOT_AVALIABLE);
        }
        $objResponse->setContent(json_encode((array) $objAnularPagoResponse));
        return $objResponse;
    }//anularPagoAction

    /**
     * validaJsonCredencialesCliente, metodo que valida credenciales, valida que el formato del json que se esté recibiendo 
     * sea el correcto dependiendo el metodo al cual quieran acceder ademas valida que el cliente exista.
     * 
     * @author  Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 13-03-2015
     * @author  Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 20-10-2015
     * @since 1.0
     * 
     * @return array Reptorna un array con un boolean si es falso no ha existido alguna restriccion para acceder a los metodos de pagos en linea
     *               por verdadero no se podra acceder al los metodos de pagos en linea.
     */
    private function validaJsonCredencialesCliente($arrayRequestParametros, $strAccion)
    {
        $emFinanciero                                 = $this->getDoctrine()->getManager('telconet_financiero');
        $emComercial                                  = $this->getDoctrine()->getManager('telconet');
        $emGeneral                                    = $this->getDoctrine()->getManager('telconet_general');
        $arrayResponse                                = array();
        $arrayCliente                                 = array();
        $arrayEstadosClientePer                       = array();
        $arrayResponse['boolResponse']                = false;
        $arrayResponse['strMensaje']                  = '';
        $arrayResponse['strCodigo']                   = '';
        $arrayResponse['entityAdmiCanalPagoLinea']    = '';
        $arrayResponse['arrayInfoPersonaEmpresaRol']  = array();
        $strNombreParametro                           = 'ESTADOS_CLIENTE_CONSULTA_PL';
        $strModulo                                    = 'FINANCIERO';
        $strProceso                                   = 'CLIENTE_PAGOS_EN_LINEA';
        $strDescripcion                               = 'ESTADO_CLIENTE_CONSULTA_SALDOS_PL';
        $intEstadosCliente                            = 0;

        try
        {
            //Valida que los parametros del json sean correctos
            if(false === isset($arrayRequestParametros['codigoExternoEmpresa']) || false === isset($arrayRequestParametros['tipoTransaccion']) ||
               false === isset($arrayRequestParametros['identificacionCliente'])|| false === isset($arrayRequestParametros['canal']) ||
               false === isset($arrayRequestParametros['usuario'])              || false === isset($arrayRequestParametros['clave']))
            {
                $arrayResponse['strMensaje']   = 'Error, no se estan definiendo parametros de entrada.';
                $arrayResponse['strCodigo']    = self::INVALID_PARAMETERS;
                $arrayResponse['boolResponse'] = true;
                return $arrayResponse;
            }
            //Valida que los parametros del json no esten vacios
            if("" === $arrayRequestParametros['codigoExternoEmpresa'] || "" === $arrayRequestParametros['tipoTransaccion'] ||
               "" === $arrayRequestParametros['identificacionCliente']|| "" === $arrayRequestParametros['canal'] ||
               "" === $arrayRequestParametros['usuario']              || "" === $arrayRequestParametros['clave'])
            {
                $arrayResponse['strMensaje']   = 'Error, parametros enviados no pueden ser nulos.';
                $arrayResponse['strCodigo']    = self::EMPTY_FIELDS;
                $arrayResponse['boolResponse'] = true;
                return $arrayResponse;
            }
            //Switch que permite validar el request segun el tipo de transaccion
            switch($arrayRequestParametros['tipoTransaccion'])
            {
                case self::CONSULTA_PAGO : break;
                case self::PROCESAR_PAGO : 
                case self::REVERSAR_PAGO : 
                case self::CONCILIAR_PAGO : 
                case self::ELIMINAR_PAGO : 
                case self::ANULAR_PAGO : 
                    if(false === isset($arrayRequestParametros['secuencialRecaudador']) || false === isset($arrayRequestParametros['valorPago']))
                    {
                         $arrayResponse['strMensaje']   = 'Error, no se estan definiendo parametros de entrada.';
                         $arrayResponse['strCodigo']    = self::INVALID_PARAMETERS;
                         $arrayResponse['boolResponse'] = true;
                         return $arrayResponse;
                    }
                    if("" === $arrayRequestParametros['secuencialRecaudador'] || "" === $arrayRequestParametros['valorPago'])
                    {
                        $arrayResponse['strMensaje']   = 'Error, parametros enviados no pueden ser nulos.';
                        $arrayResponse['strCodigo']    = self::EMPTY_FIELDS;
                        $arrayResponse['boolResponse'] = true;
                        return $arrayResponse;
                    }
                    if(0 >= $arrayRequestParametros['valorPago'])
                    {
                        $arrayResponse['strMensaje']   = 'Error, saldo no puede ser menor o igual a cero.';
                        $arrayResponse['strCodigo']    = self::TOPUP_AMOUNT_INCORRECT;
                        $arrayResponse['boolResponse'] = true;
                        return $arrayResponse;
                    }
                    break;
                default: 
                    $arrayResponse['strMensaje']   = 'Error, Codigo de transaccion no existe.';
                    $arrayResponse['strCodigo']    = self::TRANSACTION_NOT_EXIST;
                    $arrayResponse['boolResponse'] = true;
                    return $arrayResponse;
            }
            //Switch que valida parametros enviados en el json para los metodos reversar, conciliar y eliminar pago
            switch($arrayRequestParametros['tipoTransaccion'])
            {
                case self::CONSULTA_PAGO :
                case self::PROCESAR_PAGO : break;
                case self::REVERSAR_PAGO : 
                case self::CONCILIAR_PAGO : 
                case self::ELIMINAR_PAGO : 
                case self::ANULAR_PAGO :
                    if(false === isset($arrayRequestParametros['fechaTransaccion']))
                    {
                         $arrayResponse['strMensaje']   = 'Error, no se estan definiendo parametros de entrada.';
                         $arrayResponse['strCodigo']    = self::INVALID_PARAMETERS;
                         $arrayResponse['boolResponse'] = true;
                         return $arrayResponse;
                    }
                    if("" === $arrayRequestParametros['fechaTransaccion'])
                    {
                        $arrayResponse['strMensaje']   = 'Error, parametros enviados no pueden ser nulos.';
                        $arrayResponse['strCodigo']    = self::EMPTY_FIELDS;
                        $arrayResponse['boolResponse'] = true;
                        return $arrayResponse;
                    }
                    break;
                default: 
                    $arrayResponse['strMensaje']   = 'Error, Codigo de transaccion no existe.';
                    $arrayResponse['strCodigo']    = self::TRANSACTION_NOT_EXIST;
                    $arrayResponse['boolResponse'] = true;
                    return $arrayResponse;
            }
            //Switch que valida parametros enviados en el json para el metodo procesa pago
            switch($arrayRequestParametros['tipoTransaccion'])
            {
                case self::CONSULTA_PAGO : 
                case self::REVERSAR_PAGO : 
                case self::CONCILIAR_PAGO :
                case self::ANULAR_PAGO :
                case self::ELIMINAR_PAGO : break;
                case self::PROCESAR_PAGO :
                    if(false === isset($arrayRequestParametros['numeroContrato']))
                    {
                         $arrayResponse['strMensaje']   = 'Error, no se estan definiendo parametros de entrada.';
                         $arrayResponse['strCodigo']    = self::INVALID_PARAMETERS;
                         $arrayResponse['boolResponse'] = true;
                         return $arrayResponse;
                    }
                    if("" === $arrayRequestParametros['numeroContrato'])
                    {
                        $arrayResponse['strMensaje']   = 'Error, parametros enviados no pueden ser nulos.';
                        $arrayResponse['strCodigo']    = self::EMPTY_FIELDS;
                        $arrayResponse['boolResponse'] = true;
                        return $arrayResponse;
                    }
                    break;
                default: 
                    $arrayResponse['strMensaje']   = 'Error, Codigo de transaccion no existe.';
                    $arrayResponse['strCodigo']    = self::TRANSACTION_NOT_EXIST;
                    $arrayResponse['boolResponse'] = true;
                    return $arrayResponse;
            }
            //Obtiene el canal de la entidad AdmiCanalPagoLinea buscada por el nombre del canal y el estado
            $entityAdmiCanalPagoLinea = $emFinanciero->getRepository('schemaBundle:AdmiCanalPagoLinea')
                                                     ->findOneBy(array('nombreCanalPagoLinea' => $arrayRequestParametros['canal'],
                                                                       'estadoCanalPagoLinea' => 'Activo'));
            //Si no se encontro un canal retorna un response
            if(!$entityAdmiCanalPagoLinea)
            {
                $arrayResponse['strMensaje']   = 'Error, el canal de pagos no existe.';
                $arrayResponse['strCodigo']    = self::INVALID_PARAMETERS;
                $arrayResponse['boolResponse'] = true;
                return $arrayResponse;
            }
            $arrayResponse['entityAdmiCanalPagoLinea'] = $entityAdmiCanalPagoLinea;
           //Valida las credenciales.
            if($strAccion !== $arrayRequestParametros['tipoTransaccion'] || 
               $arrayRequestParametros['canal']   !== $entityAdmiCanalPagoLinea->getNombreCanalPagoLinea() ||
               $arrayRequestParametros['usuario'] !== $entityAdmiCanalPagoLinea->getUsuarioCanalPagoLinea() || 
               $arrayRequestParametros['clave']   !== $entityAdmiCanalPagoLinea->getClaveCanalPagoLinea())
            {
                $arrayResponse['strMensaje']   = 'Error, credenciales incorrectas.';
                $arrayResponse['strCodigo']    = self::INVALID_CREDENTIALS;
                $arrayResponse['boolResponse'] = true;
                return $arrayResponse;
            }

            $entityInfoPersona = $emComercial->getRepository('schemaBundle:InfoPersona')
                                             ->findOneBy(array('identificacionCliente' => $arrayRequestParametros['identificacionCliente']));


            if ( is_object($entityInfoPersona) ) 
            {
                $arrayAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                   ->get($strNombreParametro, 
                                                         $strModulo, 
                                                         $strProceso, 
                                                         $strDescripcion, 
                                                         '', 
                                                         'SI', 
                                                         '', 
                                                         '', 
                                                         '', 
                                                         $arrayRequestParametros['codigoExternoEmpresa'] );
                
                if($arrayAdmiParametroDet && count($arrayAdmiParametroDet) > 0)
                {
                    foreach($arrayAdmiParametroDet as $arrayParametro)
                    {
                        if (!empty($arrayParametro['valor1']))
                        {
                            $arrayEstadosClientePer[$intEstadosCliente] = $arrayParametro['valor1'];
                            $intEstadosCliente                          = $intEstadosCliente + 1;
                        }
                    }//( $arrayAdmiParametroDet as $arrayParametro )
                }//( $arrayAdmiParametroDet )

                $arrayCliente = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                            ->getPersonaEmpresaRolPorPersonaPorTipoRolEstados($entityInfoPersona->getId(), ['Cliente', 'Pre-cliente'],
                                                                                              $arrayRequestParametros['codigoExternoEmpresa'], 
                                                                                              $arrayEstadosClientePer);
                //Si no ha encontrado un cliente activo o cancelado retorna un response
                if ( empty($arrayCliente) && count($arrayAdmiParametroDet) == 0)          
                {
                    $arrayResponse['strMensaje']   = 'Error, no existe cliente Activo o Cancelado.';
                    $arrayResponse['strCodigo']    = self::NOT_EXIST_ACCOUNT;
                    $arrayResponse['boolResponse'] = true;
                    return $arrayResponse;
                }
                $arrayResponse['arrayInfoPersonaEmpresaRol'] = $arrayCliente;

            }
            else
            {
                //Si no ha encontrado la persona retorna un response
                $arrayResponse['strMensaje']   = 'Error, no existe cliente.';
                $arrayResponse['strCodigo']    = self::NOT_EXIST_ACCOUNT;
                $arrayResponse['boolResponse'] = true;
                return $arrayResponse;
            }
        }
        catch(\Exception $ex)
        {
            $arrayResponse['strMensaje']   = 'Error, '. $ex->getMessage();
            $arrayResponse['strCodigo']    = self::UNDEFINED_ERROR;
            $arrayResponse['boolResponse'] = true;
        }
        return $arrayResponse;
    }//validaJsonCredencialesCliente
    
    /**
     * validateDirExistCreate, metodo que valida si el directorio donde se creará el log de pagos en linea existe
     * si no existe, lo crea
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 13-03-2015
     * @return array Retorna un array con false cuando no ha existido ningun error, true cuando existió un inconveniente
     */
    private function validateDirExistCreate($arrayParametros)
    {
        $arrayResponse                 = array();
        $arrayResponse['boolResponse'] = false;
        $arrayResponse['strMensaje']   = '';
        $arrayResponse['strCodigo']    = '';
        //Realiza el split del path enviado por el separador /
        $arrayPathLog = explode('/', $arrayParametros['strPathLog']);
        try
        {
            //Itera el array construido a partir del string enviado como strPathLog
            foreach($arrayPathLog as $strPathLog):
                $arrayParametros['strPathTelcos'] = $arrayParametros['strPathTelcos'].'/'.$strPathLog;
                //Pregunta si no existe el directorio y envia a crear el directorio
                if(!file_exists($arrayParametros['strPathTelcos']))
                {
                    mkdir($arrayParametros['strPathTelcos'], 0777);
                }
            endforeach;
        }
        catch(\Exception $ex)
        {
            $arrayResponse['boolResponse'] = true;
            $arrayResponse['strMensaje']   = 'Error '. $ex->getMessage();
            $arrayResponse['strCodigo']    = '001';
        }
        return $arrayResponse;
    }//validateDirExistCreate
}
