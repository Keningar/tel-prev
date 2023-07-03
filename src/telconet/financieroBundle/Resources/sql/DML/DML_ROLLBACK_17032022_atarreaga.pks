/**
 * @author Alex Arreaga <atarreaga@telconet.ec>
 * @version 1.0
 * @since 17-03-2022    
 * Se crea la sentencia DML se eliminar los registros para las configuraciones nuevas formas pago.
 */

DELETE  FROM DB_GENERAL.ADMI_BANCO WHERE USR_CREACION = 'atarreaga' AND trunc(FE_CREACION) >'15-MAR-22';

DELETE  FROM DB_FINANCIERO.ADMI_GRUPO_ARCHIVO_DEBITO_DET WHERE USR_CREACION = 'atarreaga' AND trunc(FE_CREACION) >'15-MAR-22';

DELETE  FROM DB_GENERAL.ADMI_BANCO_TIPO_CUENTA WHERE USR_CREACION = 'atarreaga' AND trunc(FE_CREACION) >'15-MAR-22';

--BORRA REGISTROS PARA LAS PROMOCIONES DE LAS FORMAS DE PAGO INGRESADAS
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET WHERE USR_CREACION = 'atarreaga' AND PARAMETRO_ID =(select id_parametro from DB_GENERAL.admi_parametro_cab where NOMBRE_PARAMETRO = 'MAPEO DE PROMOCIONES MENSUAL' AND ESTADO ='Activo');


COMMIT;
