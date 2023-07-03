/** 
 * @author Alex Arreaga <atarreaga@telconet.ec>
 * @version 1.0 
 * @since 04-10-2022
 * Se crea DML de reverso de configuraciones de mejora de generación de débitos.
 */

--SE ELIMINA DETALLES DE PARAMETROS
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
	WHERE PARAMETRO_ID IN ( SELECT ID_PARAMETRO
	                        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
	                        WHERE NOMBRE_PARAMETRO = 'PARAM_GENERACION_DEBITOS'
	                        AND ESTADO             = 'Activo');

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PARAM_GENERACION_DEBITOS';

COMMIT;
/
