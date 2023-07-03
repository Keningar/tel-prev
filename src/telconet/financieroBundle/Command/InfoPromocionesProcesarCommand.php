<?php 
namespace telconet\financieroBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use telconet\comercialBundle\Controller\ProcesarPromocionesController;

class InfoPromocionesProcesarCommand extends ContainerAwareCommand
{ 
    protected function configure()
    {
        $this
            ->setName('comercial:InfoPromocionesProcesar')
            ->setDescription('Procesar promociones comerciales')
            ->addArgument('name', InputArgument::OPTIONAL, 'Who do you want to greet?')
            ->addOption('yell', null, InputOption::VALUE_NONE, 'If set, the task will yell in uppercase letters')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
	
		
		$procesarController=new ProcesarPromocionesController();
		$procesarController->setContainer($this->getContainer());             
		$procesarController->indexPAction();		        
    }
}
