/**
 * @author Edgar Holguín <eholguin@telconet.ec>
 * @version 1.0
 * @since 01-02-2021    
 * Se crea la sentencia DML para eliminación de registros.
 */
 
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET DET WHERE DET.PARAMETRO_ID  = (SELECT CAB.ID_PARAMETRO FROM ADMI_PARAMETRO_CAB CAB WHERE CAB.NOMBRE_PARAMETRO = 'AUTOMATIZACION_RETENCIONES');

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB WHERE CAB.NOMBRE_PARAMETRO = 'AUTOMATIZACION_RETENCIONES';

DELETE FROM DB_GENERAL.ADMI_GESTION_DIRECTORIOS WHERE CODIGO_APP = 4 AND APLICACION = 'TelcosWeb' 
AND MODULO = 'Financiero' AND SUBMODULO ='Pagos';

COMMIT;

/