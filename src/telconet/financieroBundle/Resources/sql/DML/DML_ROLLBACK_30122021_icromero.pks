
/**
 * DEBE EJECUTARSE EN DB_FINANCIERO
 * Script eliminar parametros detalle de FLUJO_ACTA_CANCELACION.
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 25-12-2021 - Version Inicial.
 */
delete from db_general.admi_parametro_det 
where usr_creacion='icromero' 
and parametro_id=(select id_parametro from db_general.admi_parametro_Cab where nombre_parametro='FLUJO_ACTA_CANCELACION');

/**
 * DEBE EJECUTARSE EN DB_FINANCIERO
 * Script eliminar parametros cabecera de FLUJO_ACTA_CANCELACION.
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 25-12-2021 - Version Inicial.
 */
 delete from db_general.admi_parametro_cab where nombre_parametro='FLUJO_ACTA_CANCELACION' and usr_creacion='icromero';

COMMIT;