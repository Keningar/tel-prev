<?php 
namespace telconet\financieroBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use telconet\contabilizacionesBundle\Controller\ContabilizarMasivamenteController;


class ContabilizarCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('pagos:contabilizar')
            ->setDescription('Contabilizacion masiva de pagos')
            ->addArgument('name', InputArgument::OPTIONAL, 'Who do you want to greet?')
            ->addOption('yell', null, InputOption::VALUE_NONE, 'If set, the task will yell in uppercase letters')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
	
		
		$anticiposController=new ContabilizarMasivamenteController();
		$anticiposController->setContainer($this->getContainer());
		$anticiposController->indexAction();
	
	
        /*$name = $input->getArgument('name');
        if ($name) {
            $text = 'Hello '.$name;
        } else {
            $text = 'Hello';
        }

        if ($input->getOption('yell')) {
            $text = strtoupper($text);
        }*/

        //$output->writeln($text);
    }
}