/**
 * DEBE EJECUTARSE EN DB_FINANCIERO
 * Script eliminar parametros FormasPagoValidaRecurrente y EstadosContratosValidaRecurrente  detalle de DEBITOS_PLANIFICADOS, estos se usan para validacion de debitos planificados
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 14-10-2021 - Version Inicial.
 */
delete from db_general.admi_parametro_det 
where usr_creacion='icromero' 
and descripcion in ('EstadosContratosValidaRecurrente','FormasPagoValidaRecurrente')
and parametro_id=(select id_parametro from db_general.admi_parametro_Cab where nombre_parametro='DEBITOS_PLANIFICADOS');

COMMIT;