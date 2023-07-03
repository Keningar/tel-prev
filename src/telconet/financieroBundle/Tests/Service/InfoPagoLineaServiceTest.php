<?php

namespace telconet\financieroBundle\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

use telconet\financieroBundle\Service\InfoPagoLineaService;
use telconet\schemaBundle\Service\HelloWorldService;
use Symfony\Bundle\FrameworkBundle\Client;
use telconet\schemaBundle\Repository\InfoOficinaGrupoRepository;
use telconet\schemaBundle\Entity\AdmiFormaPago;

class InfoPagoLineaServiceTest extends WebTestCase
{
    /**
     * @var Client
     */
    private $client;

    protected function setUp() {
	    $this->client = static::createClient();
    }

    public function testObtenerConsultaSaldoClientePorIdentificacion()
    {
        /* @var $servicePagoLinea InfoPagoLineaService */
        $servicePagoLinea = $this->client->getContainer()->get('financiero.InfoPagoLinea');
        
        $mapSaldo = $servicePagoLinea->obtenerConsultaSaldoClientePorIdentificacion('09', '0900379819');
        // debe existir resultado
        $this->assertGreaterThan(0, count($mapSaldo));
        // saldo debe ser mayor que cero
        $this->assertGreaterThan(0, $mapSaldo['saldo']);
    }

    public function testFormaPagoLinea()
    {
        /* @var $entityFormaPago AdmiFormaPago */
        $entityFormaPago = $this->client->getContainer()->get('doctrine.orm.telconet_entity_manager')->getRepository('schemaBundle:AdmiFormaPago')->findOneByCodigoFormaPago('PAL');
        $this->assertNotNull($entityFormaPago);
    }
    
    public function testOficinaMatriz()
    {
        /* @var $infoOficinaGrupoRepo InfoOficinaGrupoRepository */
        $infoOficinaGrupoRepo = $this->client->getContainer()->get('doctrine.orm.telconet_entity_manager')->getRepository('schemaBundle:InfoOficinaGrupo');
    
        $datos = $infoOficinaGrupoRepo->getOficinaMatrizPorEmpresa('09');
        $this->assertEquals('TRANSTELCO - Quito', $datos->getNombreOficina());
    
        $datos = $infoOficinaGrupoRepo->getOficinaMatrizPorEmpresa('18');
        $this->assertEquals('MEGADATOS - QUITO', $datos->getNombreOficina());
    
    }
    
}
