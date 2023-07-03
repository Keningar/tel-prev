/**
 * Documentación DELETE de configuraciones para reverso de parámetros en Ecuanet.
 * y  DB_GENERAL.ADMI_PARAMETRO_DET.
 *
 * @author José Candelario <jcandelario@telconet.ec>
 * @version 1.0 01-03-2023
 */

Delete from DB_GENERAL.ADMI_PARAMETRO_DET where PARAMETRO_ID in (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PROMOCIONES_NUMERO_MESES_EVALUA_FE_CONTRATO' AND ESTADO = 'Activo') 
and empresa_cod = '33' and usr_creacion = 'jcandelario';

Delete from DB_GENERAL.ADMI_PARAMETRO_DET where PARAMETRO_ID in (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PROM_ESTADOS_CONTRATOS' AND ESTADO = 'Activo') 
and empresa_cod = '33' and usr_creacion = 'jcandelario';

Delete from DB_GENERAL.ADMI_PARAMETRO_DET where PARAMETRO_ID in (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PROM_ESTADO_SERVICIO' AND ESTADO = 'Activo') 
and empresa_cod = '33' and usr_creacion = 'jcandelario';

Delete from DB_GENERAL.ADMI_PARAMETRO_DET where PARAMETRO_ID in (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PROM_HORA_EJECUCION_JOB' AND ESTADO = 'Activo') 
and empresa_cod = '33' and usr_creacion = 'jcandelario';

Delete from DB_GENERAL.ADMI_PARAMETRO_DET where PARAMETRO_ID in (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'MAPEO DE PROMOCIONES MENSUAL' AND ESTADO = 'Activo') 
and empresa_cod = '33' and usr_creacion = 'jcandelario';

Delete from DB_GENERAL.ADMI_PARAMETRO_DET where PARAMETRO_ID in (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PROM_SOL_CAMBIOS_TEC' AND ESTADO = 'Activo') 
and empresa_cod = '33' and usr_creacion = 'jcandelario';

COMMIT;
/

