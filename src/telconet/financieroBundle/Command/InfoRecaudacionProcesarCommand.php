<?php

namespace telconet\financieroBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;

/**
 * Comando para procesamiento de Recaudaciones Bancarias en estado Pendiente.
 * Crontab para ejecutar comando cada 5 minutos:
 * * /5 * * * * {ruta_telcos}/app/console --env=dev financiero:InfoRecaudacionProcesar -l -v
 */
class InfoRecaudacionProcesarCommand extends ContainerAwareCommand
{
    
    protected function configure()
    {
        $this->setName('financiero:InfoRecaudacionProcesar')
            ->setDescription('Procesamiento de Recaudaciones Bancarias')
            ->addOption('logfile', 'l', InputOption::VALUE_NONE, 'Indique si se debe guardar los mensajes en un archivo log')
            ->addOption('memory', 'm', InputOption::VALUE_REQUIRED, 'Indique el limite de memoria a usar por el script');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logfile = $input->getOption('logfile');
        if ($logfile)
        {
            // enviar output a un archivo log
            $file   = __DIR__.'/../../../../web/public/uploads/recaudacion_pagos/procesar_recaudaciones_' . (new \DateTime('now'))->format('Ymd') . '.log';
            $handle = fopen($file, 'a');
            $output = new StreamOutput($handle);
        }
        $output->writeln('>>> INI: ' . (new \DateTime('now'))->format('Y-m-d H:i:s'));
        $memory = $input->getOption('memory');
        if ($memory)
        {
            // modificar limite de memoria
            $output->writeln('memory_limit (old): ' . ini_get('memory_limit'));
            ini_set('memory_limit', $memory);
            $output->writeln('memory_limit (new): ' . ini_get('memory_limit'));
        }
        /* @var $serviceInfoRecaudacion \telconet\financieroBundle\Service\InfoRecaudacionService */
        $serviceInfoRecaudacion = $this->getContainer()->get ('financiero.InfoRecaudacion');
        // intentar procesar recaudaciones pendientes
        try
        {
            $result = $serviceInfoRecaudacion->procesarRecaudacionesPendientes($output);
            $output->writeln('Recaudaciones Procesadas: ' . $result);
        }
        catch (\Exception $e)
        {
            $output->writeln($e->getMessage());
            $output->writeln($e->getTraceAsString());
        }
        $output->writeln('>>> FIN: ' . (new \DateTime('now'))->format('Y-m-d H:i:s'));
        if ($logfile)
        {
            fclose($handle);
        }
    }
    
}
