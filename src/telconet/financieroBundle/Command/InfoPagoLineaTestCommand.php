<?php

namespace telconet\financieroBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;

/**
 * Comando para Test de Pagos en Linea.
 * Se debe indicar el codigo de la empresa, el nombre del canal, la identificacion del cliente, el valor a pagar.
 * Si no se indica la opcion -r con la respectiva referencia, se genera una de la forma TEST-yyyymmddhhss,
 * en todo caso la referencia siempre debe comenzar con TEST-.
 * El comando consulta el saldo del cliente, genera el pago en linea (si se ha especificado la opcion -g)
 * y concilia el pago en linea (si se ha especificado opcion -c).
 * Ejecucion:
 * {ruta_telcos}/app/console --env=dev financiero:InfoPagoLineaTest {empresaCod}  {nombreCanal} {identificacionCliente} {valor} -r {referencia} -g -c
 * Ejemplo:
 * /var/www/telcos/app/console --env=dev financiero:InfoPagoLineaTest 18 Transferunion 0912345678 50.45 -g -c
 */
class InfoPagoLineaTestCommand extends ContainerAwareCommand {
    
    protected function configure()
    {
        $this->setName('financiero:InfoPagoLineaTest')
            ->setDescription('Test de Pagos en Linea')
            ->addArgument('empresaCod', InputArgument::REQUIRED, 'Indique el codigo de la empresa')
            ->addArgument('nombreCanal', InputArgument::REQUIRED, 'Indique el nombre del canal de pago en linea')
            ->addArgument('identificacionCliente', InputArgument::REQUIRED, 'Indique la identificacion del cliente')
            ->addArgument('valor', InputArgument::REQUIRED, 'Indique el valor del pago')
            ->addOption('referencia', 'r', InputOption::VALUE_REQUIRED, 'Indique la referencia del pago')
            ->addOption('generar', 'g', InputOption::VALUE_NONE, 'Indique si se generar el pago en linea')
            ->addOption('conciliar', 'c', InputOption::VALUE_NONE, 'Indique si se conciliar el pago en linea');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $empresaCod = $input->getArgument('empresaCod');
        $nombreCanal = $input->getArgument('nombreCanal');
        $identificacionCliente = $input->getArgument('identificacionCliente');
        $valor = $input->getArgument('valor');
        $referencia = $input->getOption('referencia');
        if (!$referencia)
        {
            $referencia = 'TEST-' . (new \DateTime('now'))->format('YmdHis');
        }
        if (substr($referencia, 0, 5) !== "TEST-")
        {
            throw new \Exception('Este comando solo puede usarse para pagos con referencia TEST-');
        }
        $comentario = 'TEST';
        $output->writeln("Argumentos: empresaCod:$empresaCod - nombreCanal:$nombreCanal - identificacionCliente:$identificacionCliente - valor:$valor - referencia:$referencia");
        /* @var $servicePagoLinea \telconet\financieroBundle\Service\InfoPagoLineaService */
        $servicePagoLinea = $this->getContainer()->get('financiero.InfoPagoLinea');
        // obtener datos de consulta de saldo del cliente
        $mapSaldo = $servicePagoLinea->obtenerConsultaSaldoClientePorIdentificacion($empresaCod, $identificacionCliente);
        print_r($mapSaldo);
        if ($input->getOption('generar'))
        {
            $output->writeln('Generando Pago Linea Ref: ' . $referencia);
            $result = $servicePagoLinea->generarPagoLinea($empresaCod, $identificacionCliente, $mapSaldo['numeroContrato'], $nombreCanal, (float) $valor, $referencia, $comentario);
            if (is_null($result))
            {
                // cliente no encontrado, sin contrato o no tiene saldo saldo
                $output->writeln('No existen registros de contrato o deudas del cliente a procesar.');
            }
            else if (is_numeric($result))
            {
                // si se devolvio un numero, es el id de un pago existente
                $output->writeln('Pago ya existe: ' . $result);
            }
            else
            {
                $output->writeln('Pago generado: ' . $result->getId());
            }
        }
        if ($input->getOption('conciliar'))
        {
            $output->writeln('Conciliando Pago Linea Ref: ' . $referencia);
            $result = $servicePagoLinea->conciliarPagoLinea($nombreCanal, $empresaCod, $identificacionCliente, (float) $valor, $referencia, new \DateTime('now'));
            $output->writeln('Resultado Conciliacion: ' . $result);
        }
        $mapSaldo = $servicePagoLinea->obtenerConsultaSaldoClientePorIdentificacion($empresaCod, $identificacionCliente);
        print_r($mapSaldo);
    }
    
}
