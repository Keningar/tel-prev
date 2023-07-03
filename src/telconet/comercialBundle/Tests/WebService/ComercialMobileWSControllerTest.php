<?php

namespace telconet\comercialBundle\Tests\WebService;

use Symfony\Component\HttpKernel\KernelInterface;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class ComercialMobileWSControllerTest extends WebTestCase
{

    /**
     * @var Client
     */
    private $client;

    protected function setUp() {
	    $this->client = static::createClient();
    }
    
    public function providerLogins()
    {
        return array(
                        array("ltama"),
                        array("jlafuente"),
                        array("nmontesdeoca"),
                        array("jpiguave"),
        );
    }
    
    public function providerCatalogos()
    {
        return array(
                        array(9),
        );
    }

    public function providerEmpresas()
    {
        return array(
                        array("09"),
                        array("18"),
        );
    }
    
    /**
     * @dataProvider providerLogins
     */
    public function testObtenerEmpresas($login)
    {
        $this->markTestSkipped();
        $wsclient = new \SoapClient("https://dev-telcos-developer.telconet.ec/ws/AplicationService");
        $wsparams = array(
            'login' => $login,
        );
        $wsresult = $wsclient->__soapCall("obtenerEmpresas", $wsparams);
        var_dump($wsresult);
        $result = json_decode($wsresult);
        var_dump($result);
        $this->assertNotEquals(0, count($result->arrayEmpresaPersona), "Debe haber al menos 1 empresa para el login dado");
    }

    /**
     * @dataProvider providerLogins
     */
    public function testObtenerEmpresasRepo($login)
    {
        $this->markTestSkipped();
        /* @var $repoPersonaEmpresaRol InfoPersonaEmpresaRolRepository */
        $repoPersonaEmpresaRol = $this->client->getContainer()->get('doctrine')->getManager()->getRepository('schemaBundle:InfoPersonaEmpresaRol');
        $arrayEmpresas = $repoPersonaEmpresaRol->getEmpresasByPersona($login, "Empleado");
        var_dump((object)$arrayEmpresas);
        $this->assertNotEquals(0, count($arrayEmpresas), "Debe haber al menos 1 empresa para el login dado");
    }

    /**
     * @dataProvider providerCatalogos
     */
    public function testObtenerCatalogos($tipo)
    {
        $this->markTestSkipped();
        $wsclient = new \SoapClient("https://dev-telcos-developer.telconet.ec/ws/AplicationService");
        $wsparams = array(
            'tipo' => $tipo,
        );
        $wsresult = $wsclient->__soapCall("obtenerCatalogo", $wsparams);
        var_dump($wsresult);
        $result = json_decode($wsresult);
        var_dump($result);
    }

    /**
     * @dataProvider providerEmpresas
     */
    public function testObtenerProductos($codEmpresa)
    {
        $this->markTestSkipped();
        $wsclient = new \SoapClient("https://dev-telcos-developer.telconet.ec/ws/AplicationService");
        $wsparams = array(
                        'codEmpresa' => $codEmpresa,
        );
        $wsresult = $wsclient->__soapCall("obtenerProductos", $wsparams);
        var_dump($wsresult);
        $result = json_decode($wsresult);
        var_dump($result);
    }
    
}
