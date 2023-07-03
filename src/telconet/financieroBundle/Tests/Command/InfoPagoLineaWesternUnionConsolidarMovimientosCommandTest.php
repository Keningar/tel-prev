<?php

namespace telconet\financieroBundle\Tests\Command;

use Symfony\Component\HttpKernel\KernelInterface;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use telconet\financieroBundle\Command\InfoPagoLineaWesternUnionConsolidarMovimientosCommand;
use Symfony\Component\Console\Tester\CommandTester;

class InfoPagoLineaWesternUnionConsolidarMovimientosCommandTest extends WebTestCase
{

    /**
     * @var Client
     */
    private $client;

    protected function setUp() {
	    $this->client = static::createClient();
    }
    
    public function testExecute()
    {
        $application = new Application($this->client->getKernel());
        $application->add(new InfoPagoLineaWesternUnionConsolidarMovimientosCommand());
        
        $command = $application->find('financiero:InfoPagoLineaActivaEcuadorConsolidarMovimientos');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array(
                'command' => $command->getName(),
        	    'fecha' => '2013-12-30',
            )
        );
    }
    
}
