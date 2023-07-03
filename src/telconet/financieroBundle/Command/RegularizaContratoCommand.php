<?php

namespace telconet\financieroBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use telconet\comercialBundle\Controller\InfoContratoController;
use Symfony\Component\Console\Output\StreamOutput;

/**    
 * Documentación para la clase 'RegularizaContratoCommand'.
 *
 * Clase que permite Aprobar o Rechazar los contratos dependiendo de los parámetros enviados por el usuario
 * 
 * @author Edson Franco <efranco@telconet.ec>       
 * @version 1.0 07-08-2016
 */
class RegularizaContratoCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('comercial:RegularizaContratoDigital')
             ->setDescription('Método que aprueba o rechaza los contratos digitales ingresados por los usuarios')
             ->addArgument('strEmpresa', InputArgument::REQUIRED, 'Indique el codigo de la empresa')
             ->addArgument('strRutaLog', InputArgument::REQUIRED, 'Indique la ruta del log que se va a crear')
             ->addArgument('strNombreLog', InputArgument::REQUIRED, 'Indique el nombre del log a crear')
             ->addArgument('strUsrCreacion', InputArgument::REQUIRED, 'Indique el usuario quien va a procesar el command')
             ->addArgument('strIpCreacion', InputArgument::REQUIRED, 'Indique la ip desde donde se procesa el command')
             ->addArgument('strArchivo', InputArgument::REQUIRED, 'Archivo que se va a procesar')
             ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        error_reporting(0);   
        $intContador        = 0;
        $strCodEmpresa      = $input->getArgument('strEmpresa');
        $strRutaLog         = $input->getArgument('strRutaLog');
        $strNombreLog       = $input->getArgument('strNombreLog');
        $strUsrCreacion     = $input->getArgument('strUsrCreacion');
        $strIpCreacion      = $input->getArgument('strIpCreacion');
        $strArchivo         = $input->getArgument('strArchivo');
        $datetimeFeActual   = new \DateTime('now');
        $strFeActual        = $datetimeFeActual->format('Y-m-d');
        $strFeHoraActual    = $datetimeFeActual->format('Y-m-d H:i:s');
        
        $file   = $strNombreLog.'-'.$strFeActual.'.log';
        $handle = fopen($strRutaLog.$file, 'a');
        $output = new StreamOutput($handle);
        
        $output->writeln('=======================================================================');
        $output->writeln('>>> INICIO: '.$strFeHoraActual);
        
        $serviceRegularizaContrato = $this->getContainer()->get ('comercial.RegularizaContratosAdendums');
        
        $intContador = $serviceRegularizaContrato->regularizaContrato($strCodEmpresa, 
                                                                     $strUsrCreacion,
                                                                     $strIpCreacion,
                                                                     $strArchivo, 
                                                                     $output);
        //$output->writeln('Contratos Regularizados: ' . $intContador);
            
            /*case 'rechazar':
                $intContador = $serviceInfoContratoAprob->rechazarContratoDigitalPorNoPagarFactura($strPrefijoEmpresa,
                                                                                                   $strUsrCreacion, 
                                                                                                   $strIpCreacion,
                                                                                                   $output);
                $output->writeln('Contratos Rechazados: ' . $intContador);
                break;*/
            
        
        $datetimeFeFinal = new \DateTime('now');
        $strFeHoraFinal  = $datetimeFeFinal->format('Y-m-d H:i:s');
        
        $output->writeln('>>> FIN: '.$strFeHoraFinal);
        $output->writeln('=======================================================================\n\n');
    }
}

