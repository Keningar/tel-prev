<?php

namespace telconet\financieroBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use telconet\comercialBundle\Controller\InfoContratoController;
use Symfony\Component\Console\Output\StreamOutput;

/**    
 * Documentación para la clase 'ContratoDigitalCommand'.
 *
 * Clase que permite Aprobar o Rechazar los contratos dependiendo de los parámetros enviados por el usuario
 * 
 * @author Edson Franco <efranco@telconet.ec>       
 * @version 1.0 07-08-2016
 * 
 * @author Alex Gómez <algomez@telconet.ec>       
 * @version 1.1 06-03-2023 Se elimina prefijo empresa para soporte multiempresa
 */
class ContratoDigitalCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('comercial:ContratoDigital')
             ->setDescription('Método que aprueba o rechaza los contratos digitales ingresados por los usuarios')
             ->addArgument('strAccion', InputArgument::REQUIRED, 'Indique la acción que desea realizar ("aprobar", o "rechazar")')
             ->addArgument('strRutaLog', InputArgument::REQUIRED, 'Indique la ruta del log que se va a crear')
             ->addArgument('strNombreLog', InputArgument::REQUIRED, 'Indique el nombre del log a crear')
             ->addArgument('strUsrCreacion', InputArgument::REQUIRED, 'Indique el usuario quien va a procesar el command')
             ->addArgument('strIpCreacion', InputArgument::REQUIRED, 'Indique la ip desde donde se procesa el command');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $intContador        = 0;
        $strAccion          = $input->getArgument('strAccion');
        $strRutaLog         = $input->getArgument('strRutaLog');
        $strNombreLog       = $input->getArgument('strNombreLog');
        $strUsrCreacion     = $input->getArgument('strUsrCreacion');
        $strIpCreacion      = $input->getArgument('strIpCreacion');
        $datetimeFeActual   = new \DateTime('now');
        $strFeActual        = $datetimeFeActual->format('Y-m-d');
        $strFeHoraActual    = $datetimeFeActual->format('Y-m-d H:i:s');
        
        $file   = $strNombreLog.'-'.$strFeActual.'.log';
        $handle = fopen($strRutaLog.$file, 'a');
        $output = new StreamOutput($handle);
        
        $output->writeln('=======================================================================');
        $output->writeln('>>> INICIO: '.$strFeHoraActual);
        
        $serviceInfoContratoAprob = $this->getContainer()->get ('comercial.InfoContratoAprob');
        
        $arrayParametros = array();
        $arrayParametros["strUsrCreacion"] = $strUsrCreacion;
        $arrayParametros["strIpCreacion"] = $strIpCreacion;
        
        switch ($strAccion)
        {
            case 'aprobar':          
                $serviceInfoContratoAprob->aprobacionContratoDigitalPorPagoFactura($arrayParametros, 
                                                                                    $output);
                break;
            
            case 'rechazar':
                $intContador = $serviceInfoContratoAprob->rechazarContratoDigitalPorNoPagarFactura($arrayParametros,
                                                                                                   $output);
                $output->writeln('Contratos Rechazados: ' . $intContador);
                break;
            
            default:
                return;
        }
        
        $datetimeFeFinal = new \DateTime('now');
        $strFeHoraFinal  = $datetimeFeFinal->format('Y-m-d H:i:s');
        
        $output->writeln('>>> FIN: '.$strFeHoraFinal);
        $output->writeln('=======================================================================\n\n');
    }
}

