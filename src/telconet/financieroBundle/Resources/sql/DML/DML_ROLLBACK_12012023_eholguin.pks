/**
 * @author Edgar Holguín <eholguin@telconet.ec>
 * @version 1.0
 * @since 12-01-2023    
 * Se crea la sentencia DML para eliminación de registros.
 */
 
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET DET WHERE DET.PARAMETRO_ID  = (SELECT CAB.ID_PARAMETRO FROM ADMI_PARAMETRO_CAB CAB WHERE CAB.NOMBRE_PARAMETRO = 'VISUALIZACION LOGS');

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB WHERE CAB.NOMBRE_PARAMETRO = 'VISUALIZACION LOGS';

COMMIT;

/