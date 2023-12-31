/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Rollback para eliminar parametros de estados permitidos para puntos y servicios del ws informacionCliente
 *
 * @author José Bedón Sánchez <jobedon@telconet.ec>
 * @version 1.0 20-04-2020 - Versión Inicial.
 */

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET APD WHERE APD.PARAMETRO_ID = (SELECT APC.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB APC WHERE APC.NOMBRE_PARAMETRO = 'EXTRANET_WS_INFOCLIENTE');

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB APC WHERE APC.NOMBRE_PARAMETRO = 'EXTRANET_WS_INFOCLIENTE';

COMMIT;

/