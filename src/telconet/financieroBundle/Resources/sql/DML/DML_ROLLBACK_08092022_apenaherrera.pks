/** 
 * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
 * @version 1.0 
 * @since 08-09-2022 
 * Se crea DML de reverso de configuraciones del Proyecto Tarjetas ABU.
 */

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
	WHERE PARAMETRO_ID IN ( SELECT ID_PARAMETRO
	                        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
	                        WHERE NOMBRE_PARAMETRO = 'PARAM_TARJETAS_ABU'
	                        AND ESTADO             = 'Activo');

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PARAM_TARJETAS_ABU';



DELETE FROM DB_GENERAL.ADMI_MOTIVO
    WHERE
        NOMBRE_MOTIVO = 'Actualización Automática ABU'
        AND USR_CREACION = 'apenaherrera';

COMMIT;
/

