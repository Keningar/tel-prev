<?php 
namespace telconet\financieroBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use telconet\financieroBundle\Controller\InfoPagoCabController;


class pruebasObtieneServiciosIncorteCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('pagos:pruebasObtieneServiciosIncorte')
            ->setDescription('Pruebas para la reactivacion por pagos')
            ->addArgument('idtipodoc', InputArgument::OPTIONAL, 'Cual es el punto que se desea consultar?')
            ->addOption('yell', null, InputOption::VALUE_NONE, 'If set, the task will yell in uppercase letters')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
	
		
		$controller=new InfoPagoCabController();
		$controller->setContainer($this->getContainer());
		$controller->pruebasObtieneServiciosInCorte($input->getArgument('idtipodoc'));

    }
}