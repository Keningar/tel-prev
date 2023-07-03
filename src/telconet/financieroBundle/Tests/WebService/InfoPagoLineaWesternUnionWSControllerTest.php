<?php

namespace telconet\financieroBundle\Tests\WebService;

use Symfony\Component\HttpKernel\KernelInterface;

use telconet\financieroBundle\WebService\InfoPagoLineaWesternUnionWSController;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class InfoPagoLineaWesternUnionWSControllerTest extends WebTestCase
{

    /**
     * @var Client
     */
    private $client;

    protected function setUp() {
	    $this->client = static::createClient();
    }
    
    public function providerClientes()
    {
        $contador = 0;
        $usuario = InfoPagoLineaWesternUnionWSController::$USER;
        $contrasena = InfoPagoLineaWesternUnionWSController::$PASSWORD;
        $nombreCanal = InfoPagoLineaWesternUnionWSController::$CANAL;
        $fechaMovimientos = new \DateTime('now');
        
        // cliente al web service de Western Union para consolidacion de movimientos
        $wsclient = new \SoapClient("https://www.activaecuador.com/RECActivaTransferSystem.Server.WSinterface/Collection/NETLIFE_Cobranzas.asmx?WSDL");
        // el web service requiere que los parametros sean campos de un objeto, por eso el array anidado
        $wsparams = array(array(
                        'usuario' => $usuario,
                        'contraseña' => $contrasena,
                        'fecha' => $fechaMovimientos->format('Y-m-d'),
        ));
        $wsresult = $wsclient->__soapCall("ConsultaMovimientos", $wsparams);
        $xml = new \SimpleXMLElement($wsresult->ConsultaMovimientosResult->any);
        
        $arrayClientes = array();
        // iterar los movimientos
        foreach ($xml->NewDataSet->NETLIFE_Cobranzas_VIEW as $movimiento)
        {
            // campos disponibles en el movimiento:
            // $movimiento->Fecha;
            // $movimiento->Secuencial;
            // $movimiento->Cuenta;
            // $movimiento->Documento;
            // $movimiento->Valor;
            // $movimiento->Referencia;
            // $movimiento->Tipo_Deuda;
            // $movimiento->Estado;
            // $movimiento->Mensaje;
            if (strcmp($movimiento->Estado, 'A') !== 0)
            {
                // si el movimiento no esta activo, pasarlo por alto
                // TODO: o evnetualmente deberia anularse el pago linea
                continue;
            }
            // n: month number (1-12)
            // j: day of month (1-31)
            // Y: four digit year
            // g: 12 hour (1-12)
            // i: 2 digit minutes (00-59)
            // s: 2 digit seconds (00-59)
            // A: AM or PM
            // @see http://php.about.com/od/learnphp/ss/php_functions_3.htm
            $fecha = \DateTime::createFromFormat('n/j/Y g:i:s A', $movimiento->Fecha);
            if (++$contador == 1) {
                $arrayClientes[] = array(str_replace('CON-', '', (string) $movimiento->Cuenta), (string) $movimiento->Documento, (float) $movimiento->Valor, (string) $movimiento->Secuencial, $fecha);
            }
        }
        return $arrayClientes;
    }
    
    public function testNotificarAction()
    {
        $wsclient = new \SoapClient("https://dev-telcos-developer.telconet.ec/ws/InfoRecaudacion");
        $wsparams = array('message' => 'mundo');
        $wsresult = $wsclient->__soapCall("notificar", $wsparams);
//         var_dump($wsresult);
        $this->assertContains("Hola mundo", $wsresult);
    }

    public function testConsultaSaldoError()
    {
        $wsclient = new \SoapClient("https://dev-telcos-developer.telconet.ec/ws/InfoRecaudacion", array('exceptions' => true));
        $wsparams = array(
            'cedula' => '1234567890',
            'canal' => 'no-canal',
            'usuario' => 'no-user',
            'password' => 'no-password',
        );
        $wsresult = $wsclient->__soapCall("ConsultaSaldos", $wsparams);
//         var_dump($wsresult);
        $this->assertNotEmpty($wsresult->error);
        $this->assertEquals(InfoPagoLineaWesternUnionWSController::$CODE_ERROR, $wsresult->retorno);
    }
    
    public function testConsultaSaldoNotExists()
    {
        $wsclient = new \SoapClient("https://dev-telcos-developer.telconet.ec/ws/InfoRecaudacion");
        $wsparams = array(
            'cedula' => '1234567890',
            'canal' => InfoPagoLineaWesternUnionWSController::$CANAL,
            'usuario' => InfoPagoLineaWesternUnionWSController::$USER,
            'password' => InfoPagoLineaWesternUnionWSController::$PASSWORD,
        );
        $wsresult = $wsclient->__soapCall("ConsultaSaldos", $wsparams);
//         var_dump($wsresult);
        $this->assertEquals(InfoPagoLineaWesternUnionWSController::$CODE_NOT_EXIST_ACCOUNT, $wsresult->retorno);
    }
    
    /**
     * @dataProvider providerClientes
     */
    public function testConsultaSaldoExists($numeroContrato, $identificacionCliente, $valor, $numeroReferencia, \DateTime $fecha)
    {
        $wsclient = new \SoapClient("https://dev-telcos-developer.telconet.ec/ws/InfoRecaudacion");
        $wsparams = array(
            'cedula' => $identificacionCliente,
            'canal' => InfoPagoLineaWesternUnionWSController::$CANAL,
            'usuario' => InfoPagoLineaWesternUnionWSController::$USER,
            'password' => InfoPagoLineaWesternUnionWSController::$PASSWORD,
        );
        $wsresult = $wsclient->__soapCall("ConsultaSaldos", $wsparams);
//         var_dump($wsresult);
        $this->assertEquals($identificacionCliente, $wsresult->documento);
        $this->assertEmpty($wsresult->error);
        $this->assertEquals($numeroContrato, $wsresult->numeroContrato);
        $this->assertEquals(InfoPagoLineaWesternUnionWSController::$CODE_EXIST_ACCOUNT, $wsresult->retorno);
        $this->assertGreaterThan(0, $wsresult->saldoAdeudado);
    }

    public function testProcesaPagoError()
    {
        $wsclient = new \SoapClient("https://dev-telcos-developer.telconet.ec/ws/InfoRecaudacion", array('exceptions' => FALSE));
        $wsparams = array(
                        'Action' => 'A',
                        'Cuenta' => 'A',
                        'Documento' => 'A',
                        'Valor_Pago' => 10,
                        'Tipo_Deuda' => 'A',
                        'Secuencial' => 'A',
                        'Fecha_Pago' => (new \DateTime('NOW'))->format('Y-m-d'),
                        'Forma_Pago' => 'A',
                        'User' => 'no-user',
                        'Password' => 'no-password',
                        'Canal_Recaudador' => 'no-canal',
        );
        $wsresult = $wsclient->__soapCall("ProcesaPago", $wsparams);
//         var_dump($wsresult);
        $this->assertNotEmpty($wsresult->error);
        $this->assertEquals(InfoPagoLineaWesternUnionWSController::$CODE_ERROR_PAY, $wsresult->retorno);
    }

    /**
     * @dataProvider providerClientes
     */
    public function notestProcesaPagoExist($numeroContrato, $identificacionCliente, $valor, $numeroReferencia, $fecha)
    {
        $wsclient = new \SoapClient("https://dev-telcos-developer.telconet.ec/ws/InfoRecaudacion", array('exceptions' => FALSE));
        $wsparams = array(
                        'Action' => '1',
                        'Cuenta' => $numeroContrato,
                        'Documento' => $identificacionCliente,
                        'Valor_Pago' => $valor,
                        'Tipo_Deuda' => 'FAC',
                        'Secuencial' => $numeroReferencia,
                        'Fecha_Pago' => $fecha->format('Y-m-d'),
                        'Forma_Pago' => 'VEN',
                        'User' => InfoPagoLineaWesternUnionWSController::$USER,
                        'Password' => InfoPagoLineaWesternUnionWSController::$PASSWORD,
                        'Canal_Recaudador' => InfoPagoLineaWesternUnionWSController::$CANAL,
        );
        $wsresult = $wsclient->__soapCall("ProcesaPago", $wsparams);
//         var_dump($wsresult);
        $this->assertNotEmpty($wsresult->error);
        $this->assertEquals(InfoPagoLineaWesternUnionWSController::$CODE_EXITS_SEQUENCE, $wsresult->retorno);
    }

    /**
     * @dataProvider providerClientes
     */
    public function testProcesaPago($numeroContrato, $identificacionCliente, $valor, $numeroReferencia, \DateTime $fecha)
    {
        $wsclient = new \SoapClient("https://dev-telcos-developer.telconet.ec/ws/InfoRecaudacion", array('exceptions' => FALSE));
        $wsparams = array(
                        'Action' => '1',
                        'Cuenta' => $numeroContrato,
                        'Documento' => $identificacionCliente,
                        'Valor_Pago' => $valor,
                        'Tipo_Deuda' => 'FAC',
                        'Secuencial' => $numeroReferencia,
                        'Fecha_Pago' => $fecha->format('Y-m-d'),
                        'Forma_Pago' => 'VEN',
                        'User' => InfoPagoLineaWesternUnionWSController::$USER,
                        'Password' => InfoPagoLineaWesternUnionWSController::$PASSWORD,
                        'Canal_Recaudador' => InfoPagoLineaWesternUnionWSController::$CANAL,
        );
        $wsresult = $wsclient->__soapCall("ProcesaPago", $wsparams);
        echo "Valor: {$valor}\n";
        var_dump($wsresult);
        $this->assertEmpty($wsresult->error);
        $this->assertEquals(InfoPagoLineaWesternUnionWSController::$CODE_PROCESS_REVERSE, $wsresult->retorno);
    }

}
