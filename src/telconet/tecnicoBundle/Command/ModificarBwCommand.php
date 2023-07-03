<?php

namespace telconet\tecnicoBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;

/**
 *
 * {ruta_telcos}/app/console --env=dev tecnico:ModificaBwMasivo -b "anchoBanda" -i "idCliente"
 * 
 * @author John Vera <javera@telconet.ec>
 * @version 1.0 03-10-2016
 */
class ModificarBwCommand extends ContainerAwareCommand
{
    
    protected function configure()
    {
        $this->setName('tecnico:ModificaBwMasivo')
            ->setDescription('Modificar el Bw masivo de los servicios de un cliente')
            ->addOption('bw', 'b', InputOption::VALUE_REQUIRED, 'Indique el Bw que quiere configurar en todos los puertos')
            ->addOption('idCliente', 'i', InputOption::VALUE_REQUIRED, 'Indique la identificación del cliente');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* @var $serviceInfoElementoWifi \telconet\tecnicoBundle\Service\InfoElementoWifiService */
        $serviceInfoElementoWifi = $this->getContainer()->get ('tecnico.InfoElementoWifi');
        
        try
        {
            $result = $serviceInfoElementoWifi->configurarBwMasivo($input->getOption('bw'), $input->getOption('idCliente'));
            echo $result;
        }
        catch (\Exception $e)
        {
            echo "ERROR EN LA LOGICA DE NEGOCIO, " . $e->getMessage();
        }
    }
    
}
