/**
 * @author Javier Hidalgo <jihidalgo@telconet.ec>
 * @version 1.0
 * @since 28-06-2022
 * Se crean las sentencias DCL para reversar permiso de los paquetes utilizados por el
 * proyecto PagosLinea
 */

REVOKE EXECUTE ON DB_FINANCIERO.FNCK_PAGOS_LINEA FROM DB_BUSPAGOS;
/