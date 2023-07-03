/*
* Actualizacion de parametro PRODUCTO_MARGINADO_REPORTE para evitqr
* que sea discriminado en reporte de BW.
*
* @author Pablo Pin <ppin@telconet.ec>
* @version 1.0 21-05-2020 - Versión Inicial.
*
*/

UPDATE DB_GENERAL.ADMI_PARAMETRO_CAB
SET ESTADO      = 'Eliminado',
    USR_ULT_MOD = 'ppin',
    FE_ULT_MOD  = SYSDATE
WHERE id_parametro = 633;

COMMIT;
/
