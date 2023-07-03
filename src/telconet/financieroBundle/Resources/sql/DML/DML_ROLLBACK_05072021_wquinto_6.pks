/**
 * @author Wilson Quinto <wquinto@telconet.ec>
 * @version 1.0
 * @since 29-04-2021    
 * Se crea la sentencia DML se elimina parametros
 */

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET 
where PARAMETRO_ID in(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PARAM_ANULACION_PAGOS') AND VALOR1='FILE-HTTP-HOST';


