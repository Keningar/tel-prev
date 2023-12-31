/**
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0
 * @since 16-07-2021    
 * Se crea la sentencia DML se eliminar los registros para las configuraciones nuevas formas pago
 */

DELETE FROM DB_GENERAL.ADMI_TIPO_CUENTA WHERE USR_CREACION ='icromero';

DELETE FROM DB_FINANCIERO.ADMI_GRUPO_ARCHIVO_DEBITO_DET  WHERE USR_CREACION ='icromero' AND trunc(FE_CREACION) >'14-JUL-21';

DELETE FROM DB_FINANCIERO.ADMI_GRUPO_ARCHIVO_DEBITO_CAB  WHERE USR_CREACION ='icromero' AND TIPO_GRUPO ='NORMAL';

DELETE FROM DB_FINANCIERO.ADMI_NOMBRE_ARCHIVO_EMPRESA  WHERE USR_CREACION ='icromero' AND trunc(FE_CREACION) >'14-JUL-21';

DELETE FROM DB_FINANCIERO.ADMI_FORMATO_DEBITO  WHERE USR_CREACION ='icromero' AND trunc(FE_CREACION) >'14-JUL-21';

DELETE FROM DB_FINANCIERO.ADMI_FORMATO_DEBITO_CARACT  WHERE USR_CREACION ='icromero' AND trunc(FE_CREACION) >'14-JUL-21';

DELETE FROM DB_FINANCIERO.ADMI_BANCO  WHERE USR_CREACION ='icromero' AND trunc(FE_CREACION) >'14-JUL-21';

DELETE FROM DB_GENERAL.ADMI_BANCO_TIPO_CUENTA  WHERE USR_CREACION ='icromero';


































































































































































































































































































































































































































































































































































































































































































