<?php 
namespace telconet\comercialBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/*
 * Clase EnviaRenovacionCommand
 *
 * Clase para la ejecucion de envios de eventos de renovacion
 *
 * @author Jessenia Piloso  <jpiloso@telconet.ec>
 * @version 1.0 19-10-2021 - Se crea comand para la ejecucion de renovación de servicios netlifecam
 *                           después de cumplir con la permanencia minima permitida.
 *                           
*/
class EnviaRenovacionCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('comercial:EnviaRenovacion')
            ->setDescription('Envio de renovación de servicios netlifecam')
        ;
    }
    
    protected function execute(InputInterface $objInput, OutputInterface $objOutput)
    {       
        $objServicioServ  = $this->getContainer()->get('comercial.InfoServicio');

        $objServicioServ->renovNetLifeCam();
        
    } 
    
}
