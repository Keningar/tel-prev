<?php

namespace telconet\financieroBundle\WebService;

use BeSimple\SoapBundle\ServiceDefinition\Annotation as Soap;
use telconet\schemaBundle\DependencyInjection\BaseWSController;
use telconet\financieroBundle\Service\InfoPagoLineaService;
use telconet\financieroBundle\WebService\InfoPagoLineaWesternUnionResponse\ConsultaSaldosResponse;
use telconet\financieroBundle\WebService\InfoPagoLineaWesternUnionResponse\ConsultaTestResponse;
use telconet\financieroBundle\WebService\InfoPagoLineaWesternUnionResponse\ProcesaPagoResponse;

/**
 * Web Service para pagos en linea de MegaDatos a traves de Western Union
 * @author ltama
 */
class InfoPagoLineaWesternUnionWSController extends BaseWSController {

    public static $USER = 'wucanal';
    public static $PASSWORD = '@C0lL3&tCh4nN3l'; // <![CDATA[@C0lL3&tCh4nN3l]]>
    public static $CANAL = 'Transferunion';
    
    /**
     * Codigo de error en la consulta
     */
    public static $CODE_ERROR = '999';
    
    /**
     * Codigo de que existe la cuenta del cliente
     */
    public static $CODE_EXIST_ACCOUNT = '001';
    
    /**
     * Codigo de que no existe la cuenta del cliente
     */
    public static $CODE_NOT_EXIST_ACCOUNT = '050';
    
    /**
     * Pago Procesado o Reversa Procesada
     */
    public static $CODE_PROCESS_REVERSE = '00';
    
    /**
     * Ya existe secuencial de pago en base
     */
    public static $CODE_EXITS_SEQUENCE = '01';
    
    /**
     * Codigo de error, Error: ' '
     */
    public static $CODE_ERROR_PAY = '99';
    
    public static function confirmarCredenciales($user, $password, $canal) {
        if ($user === self::$USER && $password === self::$PASSWORD && $canal === self::$CANAL) {
            // user, password y canal correctos
            return null;
        } else if ($user === self::$USER && $password !== self::$PASSWORD && $canal === self::$CANAL) {
            return 'Contrasena Incorrecta. Por favor, consulte la contrasena usada con el administrador del servicio.';
        } else if ($user === self::$USER && $password === self::$PASSWORD && $canal !== self::$CANAL) {
            return 'Usted no esta autorizado para utilizar este Web Service. Por favor, consulte su acceso con el administrador del servicio.';
        } else {
            return 'Credenciales Incorrectas. Por favor, consulte las credenciales usadas con el administrador del servicio.';
        }
    }
    
    /**
     * @Soap\Method("notificar")
     * @Soap\Param("message", phpType = "string")
     * @Soap\Result(phpType = "string")
     */
    public function notificarAction($message) {
        $returnValue = $this->get('schema.HelloWorld')->obtenerSaludo($message);
        // se define el valor a retornar como respuesta SOAP
        return $this->soapReturn($returnValue);
    }
    
    /**
     * @Soap\Method("ConsultaTest")
     * @Soap\Param("message", phpType = "string")
     * @Soap\Param("entity", phpType = "telconet\financieroBundle\WebService\InfoPagoLineaWesternUnionResponse\ConsultaTestResponse")
     * @Soap\Result(phpType = "telconet\financieroBundle\WebService\InfoPagoLineaWesternUnionResponse\ConsultaTestResponse")
     */
    public function testAction($message, ConsultaTestResponse $entity) {
        $returnValue = new ConsultaTestResponse ();
//         $returnValue->setRetorno ( $message );
        $returnValue->setNombreCliente('Cliente Test - ' . $entity->getNombreCliente());
        $returnValue->setDocumento('CED:' . $entity->getDocumento());
        $returnValue->setNumeroContrato('001-001-999999');
//         $returnValue->setSaldoAdeudado(99.99);
        return $this->soapReturn($returnValue);
    }
    
    /**
     * @Soap\Method("ConsultaTestArray")
     * @Soap\Param("message", phpType = "string")
     * @Soap\Param("entity", phpType = "telconet\financieroBundle\WebService\InfoPagoLineaWesternUnionResponse\ConsultaTestResponse")
     * @Soap\Result(phpType = "telconet\financieroBundle\WebService\InfoPagoLineaWesternUnionResponse\ConsultaTestResponse[]")
     */
    public function testArrayAction($message, ConsultaTestResponse $entity) {
        $returnArray = array();
        for ($i=0; $i<2; $i++)
        {
        $returnValue = new ConsultaTestResponse ();
//         $returnValue->setRetorno($message);
        $returnValue->setNombreCliente('Cliente Test - ' . $entity->getNombreCliente());
        $returnValue->setDocumento('CED:' . $entity->getDocumento());
        $returnValue->setNumeroContrato('001-001-999999_' . $i);
//         $returnValue->setSaldoAdeudado(99.99);
        $returnArray[] = $returnValue;
        }
        return $this->soapReturn($returnArray);
    }
    
    /**
     * @Soap\Method("ConsultaSaldos")
     * @Soap\Param("cedula", phpType = "string")
     * @Soap\Param("canal", phpType = "string")
     * @Soap\Param("usuario", phpType = "string")
     * @Soap\Param("password", phpType = "string")
     * @Soap\Param("tipo_busqueda", phpType = "string")
     * @Soap\Result(phpType = "telconet\financieroBundle\WebService\InfoPagoLineaWesternUnionResponse\ConsultaSaldosResponse")
     * @return ConsultaSaldosResponse
     * @author ltama
     */
    public function consultaSaldosAction($cedula, $canal, $usuario, $password, $tipo_busqueda) {
        $empresaCod = $this->getParameter('financiero.InfoPagoLineaWesternUnionWS.empresaCod');
        
        // Validar credenciales
        $error = self::confirmarCredenciales($usuario, $password, $canal);
        if (!is_null($error))
        {
            // credenciales no validas
            $returnValue = new ConsultaSaldosResponse();
            $returnValue->setRetorno(self::$CODE_ERROR);
            $returnValue->setError($error);
            return $this->soapReturn($returnValue);
        }
        
        // Validar cedula
        if (strpos($cedula, 'CON') !== false)
        {
            // es un codigo de contrato
            $returnValue = new ConsultaSaldosResponse();
            $returnValue->setRetorno(self::$CODE_ERROR);
            $returnValue->setError('Se ha ingresado un numero de contrato, por favor ingrese el numero de identificacion del cliente.');
            return $this->soapReturn($returnValue);
        }
        
        // Obtener datos de consulta de saldo del cliente, mediante el servicio InfoPagoLineaService
        /* @var $servicePagoLinea InfoPagoLineaService */
        $servicePagoLinea = $this->get ('financiero.InfoPagoLinea');
        $mapSaldo = $servicePagoLinea->obtenerConsultaSaldoClientePorIdentificacion ($empresaCod, $cedula);

        if (count($mapSaldo) <= 0)
        {
            // cliente no encontrado
            $returnValue = new ConsultaSaldosResponse();
            $returnValue->setRetorno(self::$CODE_NOT_EXIST_ACCOUNT);
            return $this->soapReturn($returnValue);
        }
        
        if (is_null($mapSaldo['numeroContrato']))
        {
            // cliente no tiene contrato
            $returnValue = new ConsultaSaldosResponse();
            $returnValue->setRetorno(self::$CODE_ERROR);
            $returnValue->setError('Cliente no tiene numero de contrato.');
            return $this->soapReturn($returnValue);
        }
        
        // Armar respuesta para devolver
        $returnValue = new ConsultaSaldosResponse();
        $returnValue->setRetorno(self::$CODE_EXIST_ACCOUNT);
        $returnValue->setNombreCliente($mapSaldo['nombreCliente']);
        $returnValue->setDocumento($mapSaldo['identificacionCliente']);
        $returnValue->setNumeroContrato($mapSaldo['numeroContrato']);
        $returnValue->setSaldoAdeudado($mapSaldo['saldo']);
        return $this->soapReturn($returnValue);
    }
    
    /**
     * @Soap\Method("ProcesaPago")
     * @Soap\Param("Action", phpType = "string")
     * @Soap\Param("Cuenta", phpType = "string")
     * @Soap\Param("Documento", phpType = "string")
     * @Soap\Param("Valor_Pago", phpType = "float")
     * @Soap\Param("Tipo_Deuda", phpType = "string")
     * @Soap\Param("Secuencial", phpType = "string")
     * @Soap\Param("Fecha_Pago", phpType = "dateTime")
     * @Soap\Param("Forma_Pago", phpType = "string")
     * @Soap\Param("User", phpType = "string")
     * @Soap\Param("Password", phpType = "string")
     * @Soap\Param("Canal_Recaudador", phpType = "string")
     * @Soap\Result(phpType = "telconet\financieroBundle\WebService\InfoPagoLineaWesternUnionResponse\ProcesaPagoResponse")
     * @author ltama
     */
    public function procesaPagoAction($Action, $Cuenta, $Documento, $Valor_Pago, $Tipo_Deuda, $Secuencial, $Fecha_Pago, $Forma_Pago, $User, $Password, $Canal_Recaudador)
    {
        $empresaCod = $this->getParameter('financiero.InfoPagoLineaWesternUnionWS.empresaCod');
        // Validar credenciales
        $error = self::confirmarCredenciales($User, $Password, $Canal_Recaudador);
        if (!is_null($error))
        {
            // credenciales no validas
            $returnValue = new ProcesaPagoResponse();
            $returnValue->setRetorno(self::$CODE_ERROR_PAY);
            $returnValue->setError($error);
            return $this->soapReturn($returnValue);
        }
        /* @var $servicePagoLinea InfoPagoLineaService */
        $servicePagoLinea = $this->get('financiero.InfoPagoLinea');
        // Accion: 1:Registro de Pago - 2: Reverso de Pago
        if ($Action == '1')
        {
            // Obtener datos de consulta de saldo del cliente
            $mapSaldo = $servicePagoLinea->obtenerConsultaSaldoClientePorIdentificacion ($empresaCod, $Documento);
            
            if (is_null($mapSaldo['numeroContrato']))
            {
                // cliente no tiene contrato
                $returnValue = new ProcesaPagoResponse();
                $returnValue->setRetorno(self::$CODE_ERROR_PAY);
                $returnValue->setError('Cliente no tiene numero de contrato.');
                return $this->soapReturn($returnValue);
            }
            if ($mapSaldo['saldo'] <= 0)
            {
                // cliente no tiene saldo
                $returnValue = new ProcesaPagoResponse();
                $returnValue->setRetorno(self::$CODE_ERROR_PAY);
                $returnValue->setError('Cliente no tiene saldo.');
                return $this->soapReturn($returnValue);
            }
            
            // generar pago linea, validar retorno del metodo
            $comentario = "Cuenta:{$Cuenta} - Tipo_Deuda:{$Tipo_Deuda} - Forma_Pago:{$Forma_Pago}";
            try
            {
                $entityPagoLinea = $servicePagoLinea->generarPagoLinea($empresaCod, $Documento, $Cuenta, $Canal_Recaudador, $Valor_Pago, $Secuencial, $comentario);
            }
            catch (\Exception $e)
            {
                // error al generar el pago linea
                $returnValue = new ProcesaPagoResponse();
                $returnValue->setRetorno(self::$CODE_ERROR_PAY);
                $returnValue->setError('Error al generar el pago.');
                return $this->soapReturn($returnValue);
            }
            
            if (is_null($entityPagoLinea))
            {
                // cliente no encontrado, sin contrato o no tiene saldo saldo
                $returnValue = new ProcesaPagoResponse();
                $returnValue->setRetorno(self::$CODE_ERROR_PAY);
                $returnValue->setError('No existen registros de contrato o deudas del cliente a procesar.');
                return $this->soapReturn($returnValue);
            }
            if (is_numeric($entityPagoLinea))
            {
                // si se devolvio un numero, es el id de un pago existente
                $returnValue = new ProcesaPagoResponse();
                $returnValue->setRetorno(self::$CODE_EXITS_SEQUENCE);
                $returnValue->setError('Pago ya existe');
                return $this->soapReturn($returnValue);
            }
            
            // pago generado correctamente
            $returnValue = new ProcesaPagoResponse();
            $returnValue->setRetorno(self::$CODE_PROCESS_REVERSE);
            $returnValue->setObservacion('Pago generado correctamente'); // no se indica numero de factura
            // obtener nuevo saldo
            $mapSaldo = $servicePagoLinea->obtenerConsultaSaldoClientePorIdentificacion($empresaCod, $Documento);
            $returnValue->setSaldo($mapSaldo['saldo']);
            return $this->soapReturn($returnValue);
        }
        else if ($Action == '2')
        {
            // reversar pago linea, validar retorno del metodo
            try
            {
                $mensajeError = $servicePagoLinea->reversarPagoLinea($Canal_Recaudador, $empresaCod, $Documento, $Valor_Pago, $Secuencial, $Fecha_Pago);
            }
            catch (\Exception $e)
            {
                // error al generar el pago linea
                $returnValue = new ProcesaPagoResponse();
                $returnValue->setRetorno(self::$CODE_ERROR_PAY);
                $returnValue->setError('Error al reversar el pago.');
                return $this->soapReturn($returnValue);
            }
            
            if (!empty($mensajeError))
            {
                // pago no reversado
                $returnValue = new ProcesaPagoResponse();
                $returnValue->setRetorno(self::$CODE_ERROR_PAY);
                $returnValue->setError('No es posible reversar el pago: ' . $mensajeError);
                return $this->soapReturn($returnValue);
            }
            
            // pago reversado correctamente
            $returnValue = new ProcesaPagoResponse();
            $returnValue->setRetorno(self::$CODE_PROCESS_REVERSE);
            $returnValue->setObservacion('Pago reversado correctamente');
            // obtener nuevo saldo
            $mapSaldo = $servicePagoLinea->obtenerConsultaSaldoClientePorIdentificacion($empresaCod, $Documento);
            $returnValue->setSaldo($mapSaldo['saldo']);
            return $this->soapReturn($returnValue);
        }
        else
        {
            $returnValue = new ProcesaPagoResponse();
            $returnValue->setRetorno(self::$CODE_ERROR_PAY);
            $returnValue->setError('Accion no valida');
            return $this->soapReturn($returnValue);
        }
    }
}
