/**
* DEBE EJECUTARSE EN DB_FINANCIERO.
* Script para generar permisos al esquema DB_BUSPAGOS para la ejecucion de paquete
* FNCK_PAGOS_LINEA perteneciente al esquema DB_FINANCIERO
* @author Javier Hidalgo <jihidalgo@telconet.ec>
* @version 1.0 28-06-2022 - Versión Inicial.
*/
GRANT EXECUTE ON DB_FINANCIERO.FNCK_PAGOS_LINEA TO DB_BUSPAGOS WITH GRANT OPTION;
/