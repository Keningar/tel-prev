<?php 
namespace telconet\financieroBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use telconet\financieroBundle\Controller\InfoPagoCabController;


class CorregirRespuestasCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('pagos:corregirRespuestas')
            ->setDescription('Correccion de pagos generados por respuestas de debitos')
            ->addArgument('debitoGen', InputArgument::OPTIONAL, 'Cual es el debito general que se desea consultar?')
            ->addOption('yell', null, InputOption::VALUE_NONE, 'If set, the task will yell in uppercase letters')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
	
		
		$controller=new InfoPagoCabController();
		$controller->setContainer($this->getContainer());
		$controller->corregirRespuestasDebitos($input->getArgument('debitoGen'));

    }
}