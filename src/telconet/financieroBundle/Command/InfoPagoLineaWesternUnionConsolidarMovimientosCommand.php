<?php

namespace telconet\financieroBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;
use telconet\financieroBundle\Service\InfoPagoLineaService;
use telconet\financieroBundle\WebService\InfoPagoLineaWesternUnionWSController;

/**
 * Comando para Consolidacion de Movimientos de Pagos en Linea de Western Union.
 * Requiere que exista la ruta '/home/scripts-telcos/{prefijo_empresa}/financiero/logs/InfoPagoLineaActivaEcuadorConsolidarMovimientos/'.
 * Debe crearse dos comandos bash con permisos de ejecucion en /home/scripts-telcos/{prefijo_empresa}/financiero/sources/,
 * uno para ejecutar el comando para la fecha actual y otro para ejecutarlo para la fecha del dia anterior.
 * Comando para fecha actual (InfoPagoLineaActivaEcuadorConsolidarMovimientosHoy.sh):
 * {ruta_telcos}/app/console --env=test financiero:InfoPagoLineaActivaEcuadorConsolidarMovimientos '{prefijo_empresa}' 'today' '{minutos_ventana}' 'file' -v
 * Comando para fecha del dia anterior (InfoPagoLineaActivaEcuadorConsolidarMovimientosAyer.sh):
 * {ruta_telcos}/app/console --env=test financiero:InfoPagoLineaActivaEcuadorConsolidarMovimientos '{prefijo_empresa}' 'yesterday' '{minutos_ventana}' 'file' -v
 * Crontab para ejecutar comando para la fecha actual, cada 5 minutos:
 * * /5 * * * * /home/scripts-telcos/{prefijo_empresa}/financiero/sources/InfoPagoLineaActivaEcuadorConsolidarMovimientos/hoy.sh
 * Crontab para ejecutar comando para la fecha de ayer, cada 10 minutos, pero solo hasta antes del medio dia:
 * * /10 0-11 * * * /home/scripts-telcos/{prefijo_empresa}/financiero/sources/InfoPagoLineaActivaEcuadorConsolidarMovimientos/hoy.sh
 * @author ltama
 */
class InfoPagoLineaWesternUnionConsolidarMovimientosCommand extends ContainerAwareCommand {
    
    protected function configure()
    {
        $this->setName('financiero:InfoPagoLineaActivaEcuadorConsolidarMovimientos')
            ->setDescription('Consolidacion de Movimientos de Pagos en Linea de Western Union')
            ->addArgument('prefijoEmpresa', InputArgument::REQUIRED, 'Indique el prefijo de la empresa')
            ->addArgument('fecha', InputArgument::REQUIRED, 'Indique la fecha de los movimientos')
            ->addArgument('minutos', InputArgument::REQUIRED, 'Indique los minutos de la ventana de espera')
            ->addArgument('format', InputArgument::REQUIRED, 'Indique el formato de salida de los mensajes');
    }
   
   /**
    *
    * Documentacion para el método 'execute'
    *
    * @author Ricardo Coello Quezada <rcoello@telconet.ec>
    * @version 1.1 20-01-2017 - Se agrega validacion al momento de iniciar con el flujo de conciliacion con el objetivo 
    *                           de verificar que solo se encuentre corriendo un proceso a la vez y evitar la duplicidad 
    *                           en Telcos.
    */     
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $usuario = InfoPagoLineaWesternUnionWSController::$USER;
        $contrasena = InfoPagoLineaWesternUnionWSController::$PASSWORD;
        $fecha = $input->getArgument('fecha');
        $fechaMovimientos = new \DateTime($fecha);
        $minutos = $input->getArgument('minutos');
        $fechaVentana = (new \DateTime())->sub(new \DateInterval('PT' . $minutos . 'M'));
        switch ($input->getArgument('prefijoEmpresa'))
        {
            case 'MD':
                $empresaCod = '18';
                $pathEmpresa = 'md';
                break;
            default:
                // empresa no soportada
                return;
        }
        $nombreCanal = InfoPagoLineaWesternUnionWSController::$CANAL;
        $format = $input->getArgument('format');
        if ($format == 'file')
        {
            $file   = '/home/scripts-telcos/' . $pathEmpresa . '/financiero/logs/InfoPagoLineaActivaEcuadorConsolidarMovimientos/' . $fechaMovimientos->format('Y-m-d') . '.log';
            $handle = fopen($file, 'a');
            $output = new StreamOutput($handle);
        }
        
        $output->writeln('>>> INI: ' . (new \DateTime('now'))->format('Y-m-d H:i:s') . ' :: Fecha Movimientos: ' . $fechaMovimientos->format('Y-m-d'));
        $boolExisteProceso = $this->getRunningProccess($this->getName());
        
        if ( !$boolExisteProceso )
        {
            try
            {
                // cliente al web service de Western Union para consolidacion de movimientos
                $wsclient = new \SoapClient('https://www.activaecuador.com/RECActivaTransferSystem.Server.WSinterface/Collection/NETLIFE_Cobranzas.asmx?WSDL');
                // el web service requiere que los parametros sean campos de un objeto, por eso el array anidado
                $wsparams = array(array(
                                'usuario' => $usuario,
                                'contraseña' => $contrasena,
                                'fecha' => $fechaMovimientos->format('Y-m-d'),
                ));
                $wsresult = $wsclient->__soapCall('ConsultaMovimientos', $wsparams);
                $xml = new \SimpleXMLElement($wsresult->ConsultaMovimientosResult->any);

                /* @var $servicePagoLinea InfoPagoLineaService */
                $servicePagoLinea = $this->getContainer()->get ('financiero.InfoPagoLinea');
                if (!empty($xml->NewDataSet->NETLIFE_Cobranzas_VIEW))
                {
                    // iterar los movimientos
                    foreach ($xml->NewDataSet->NETLIFE_Cobranzas_VIEW as $movimiento)
                    {
                        // campos disponibles en el movimiento:
                        //     $movimiento->Fecha;
                        //     $movimiento->Secuencial;
                        //     $movimiento->Cuenta;
                        //     $movimiento->Documento;
                        //     $movimiento->Valor;
                        //     $movimiento->Referencia;
                        //     $movimiento->Tipo_Deuda;
                        //     $movimiento->Estado;
                        //     $movimiento->Mensaje;
                        // formato fecha recibido:
                        //     n: month number (1-12)
                        //     j: day of month (1-31)
                        //     Y: four digit year
                        //     g: 12 hour (1-12)
                        //     i: 2 digit minutes (00-59)
                        //     s: 2 digit seconds (00-59)
                        //     A: AM or PM
                        //     @see http://php.about.com/od/learnphp/ss/php_functions_3.htm
                        $fecha = \DateTime::createFromFormat('n/j/Y g:i:s A', $movimiento->Fecha);
                        $result = "Movimiento Sec:{$movimiento->Secuencial} Estado:{$movimiento->Estado} Fecha:{$fecha->format('Y-m-d H:i:s')} :: ";
                        if ($fecha < $fechaVentana)
                        {
                            switch ($movimiento->Estado)
                            {
                                case 'A': // ACTIVO
                                    // intentar conciliar el pago linea existente correspondiente al movimiento Activo
                                    $result .= $servicePagoLinea->conciliarPagoLinea($nombreCanal, $empresaCod,
                                            (string) $movimiento->Documento, (float) $movimiento->Valor, (string) $movimiento->Secuencial, $fecha);
                                    break;
                                case 'E': // ELIMINADO
                                    // intentar eliminar el pago linea existente correspondiente al movimiento Eliminado
                                    $result .= $servicePagoLinea->eliminarPagoLinea($nombreCanal, $empresaCod,
                                            (string) $movimiento->Documento, (float) $movimiento->Valor, (string) $movimiento->Secuencial, $fecha);
                                    break;
                                default:
                                    // estado inesperado
                                    $result .= 'N/A';
                            }
                        }
                        else
                        {
                            // esperar para conciliar
                            $result .= 'Esperar';
                        }
                        $output->writeln($result);
                    }
                }
            }
            catch (\Exception $e)
            {
                $output->writeln($e->getMessage());
                $output->writeln($e->getTraceAsString());
            }
        }
        else
        {
            $output->writeln('>>> ERROR: ' . (new \DateTime('now'))->format('Y-m-d H:i:s') . ' :: Existe proceso corriendo actualemente.');   
        }    
            
        $output->writeln('>>> FIN: ' . (new \DateTime('now'))->format('Y-m-d H:i:s') . ' :: Fecha Movimientos: ' . $fechaMovimientos->format('Y-m-d'));
        if ($format == 'file')
        {
            fclose($handle);
        }
    }
    
    /**
    *
    * Documentacion para el método 'getRunningProccess'
    *
    * @author Ricardo Coello Quezada <rcoello@telconet.ec>
    * @version 1.1 20-01-2017 - Metodo utilizado para comprobar que solo exista un proceso de conciliacion iniciado,
    *                           en el caso de que exista un solo proceso iniciado continua con el proceso,
    *                           en el caso de que exista mas de un proceso iniciado no continua con el proceso.
    * 
    */     
    public function getRunningProccess( $strNombreProceso ) {
        
        $boolExisteProceso   = true;
        $arrayRespuesta     = array();

        exec('/bin/sh -c "ps aux | grep '. $strNombreProceso .' | grep -v grep | grep -v /bin/sh  | wc -l"', $arrayRespuesta );
        
        if($arrayRespuesta && count($arrayRespuesta) > 0)
        {
            if( $arrayRespuesta[0] == 1 )
            {
                $boolExisteProceso = false;
            }
        }
        
        return $boolExisteProceso;
    }
    
}
