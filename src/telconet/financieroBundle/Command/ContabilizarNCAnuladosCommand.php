<?php 
namespace telconet\financieroBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use telconet\contabilizacionesBundle\Controller\ContabilizarMasivamenteAnulacionNCController;


class ContabilizarNCAnuladosCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('ncanuladas:contabilizar')
            ->setDescription('Contabilizacion masiva de NC anuladas')
            ->addArgument('name', InputArgument::OPTIONAL, 'Who do you want to greet?')
            ->addOption('yell', null, InputOption::VALUE_NONE, 'If set, the task will yell in uppercase letters')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
	
		
		$nc=new ContabilizarMasivamenteAnulacionNCController();
		$nc->setContainer($this->getContainer());
		$nc->indexAction();
	
	
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
