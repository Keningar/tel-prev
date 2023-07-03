<?php

namespace telconet\comercialBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProcesarPromocionesController extends Controller
{
    /**
     * Realiza la ejecucion del Paquete de Promociones:
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 08-07-2014
     * Verifica si existen condiciones de promociones por ejecutarse para el caso de:
     * 1) Promociones por Referidos (Calculo y acumulacion de puntos para obtener descuentos en la facturacion del cliente)
     * 2) Promociones Descuentos en Instalacion por Forma de Pago (Descuento en la factura de instalacion dependiendo la forma de pago
     * aplicable en el contrato )
     * 3) Promociones Planes Home (Precio Preferencial) :15 % de descuento aplica los tres primeros meses en planes HOME 15/3 y HOME 30/6
     * Actualmente solo se encuentra en Produccion ejecutandose Promociones Planes Home esta se ejecuta en el crontab todos los dias a las 9:30pm
     * 30 21 * * * /home/telcos/app/console --env=dev comercial:InfoPromocionesProcesar -v > "/home/scripts-telcos/md/comercial/logs/promociones/log_promociones_$(date +\%Y\%m\%d).txt"
     **/
   public function indexPAction()
    {
        try
        {
            echo "\n";
            echo "\n";
            echo "=============================================\n";
            echo "Ejecucion del Proceso de Promociones \n";
            echo "=============================================\n";
            echo "Inicio:" . date("d-mm-Y H:m:i") . "\n";
            echo "\n";
            echo "Conectandose a la base....\n";
            $dsn = $this->container->getParameter('database_dsn');
            $user_comercial = $this->container->getParameter('user_comercial');
            $passwd_comercial = $this->container->getParameter('passwd_comercial');
            $oci_con = oci_connect($user_comercial, $passwd_comercial, $dsn);
            if($oci_con)
            {
                echo "Se conecto correctamente a la base de datos....\n";
                echo "\n";
                echo "Procesando...\n";
                echo "\n";
                $COD_RET = "";
                $MSG_RET = "";
                $s = oci_parse($oci_con, "begin PROMOCIONES.P_VERIFICARPROMOCIONES(:COD_RET, :MSG_RET); end;");
                oci_bind_by_name($s, ":COD_RET", $COD_RET, 2000);
                oci_bind_by_name($s, ":MSG_RET", $MSG_RET, 2000);
                oci_execute($s);
                oci_commit($oci_con);
                $out_var = "COD_RET: " . $COD_RET . " MSG_RET: " . $MSG_RET;
                if($COD_RET == '0')
                {
                    echo $msg = "Se ejecuto con exito el Paquete Promociones: PROMOCIONES.P_VERIFICARPROMOCIONES: " . $MSG_RET . "\n";
                }
                else
                {
                    echo $msg = "ERROR: Existio un error durante la ejecucion del Paquete Promociones: PROMOCIONES.P_VERIFICARPROMOCIONES: " . $out_var . "\n";
                }
            }
            else
            {
                echo $msg = "ERROR: No logro conectarse a la base de Datos, no se ejecuto paquete: PROMOCIONES.P_VERIFICARPROMOCIONES.\n";
            }
            echo "\n";
            echo "=============================================\n";
            echo "Fin:" . date("d-mm-Y H:m:i") . "\n";
        }
        catch(\Exception $e)
        {
            echo $msg = "ERROR: Existio un error durante la ejecucion del Paquete Promociones: PROMOCIONES.P_VERIFICARPROMOCIONES:  " . $MSG_RET . "\n";
            echo $mensajeError = "Error: " . $e->getMessage();
            echo "\n";
            echo "=============================================\n";
            echo "Fin:" . date("d-mm-Y H:m:i") . "\n";
        }
    }

}
