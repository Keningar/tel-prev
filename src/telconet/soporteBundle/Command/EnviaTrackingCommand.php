<?php 
namespace telconet\soporteBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/*
 * Clase EnviaTrackingCommand
 *
 * Clase para la ejecucion de envios de eventos de tareas hacia megadatos
 *
 * @author Pedro Velez  <psvelez@telconet.ec>
 * @version 1.0 19-10-2021 - Se crera comand para la ejecucion asincrona de los envio de eventos de tareas de md 
 *                           
*/
class EnviaTrackingCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('Envia:Tracking')
            ->setDescription('Envio de notificaciones de cambio de estado de tareas hacia Megadatos')
            ->addArgument('strUsrCreacion', InputArgument::OPTIONAL, 'usario creacion')
            ->addArgument('strIpCreacion', InputArgument::OPTIONAL, 'ip creacion')
            ->addArgument('strAccion', InputArgument::OPTIONAL, 'evento de la tarea')
            ->addArgument('intIdDetalle', InputArgument::OPTIONAL, 'id detalle de la tarea')
            ->addArgument('strDispositivoId', InputArgument::OPTIONAL, 'serie logica del dispositivo')
        ;
    }

    protected function execute(InputInterface $objInput, OutputInterface $objOutput)
    {
        $objContenedor = $this->getContainer();
        $objServiceSoporte  = $objContenedor->get('soporte.SoporteService');

        $arrayTraking = array(
            'intIdDetalle'   => $objInput->getArgument('intIdDetalle'),
            'strUsrCreacion' => $objInput->getArgument('strUsrCreacion'),
            'strIpCreacion'  => $objInput->getArgument('strIpCreacion'),
            'strAccion'      => $objInput->getArgument('strAccion'),
            'strDispositivoId' => $objInput->getArgument('strDispositivoId')
            );

        $objServiceSoporte->actualizaSeguimientoCaso($arrayTraking);
        
    } 
    
}
