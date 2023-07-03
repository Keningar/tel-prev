/**
 * @author Katherine Yager <kyager@telconet.ec>
 * @version 1.0
 * @since 03-11-2020
 * Se crean las sentencias DML para reversar configuraciones de la estructura 
 * DB_GENERAL.ADMI_PARAMETRO_CAB y DB_GENERAL.ADMI_PARAMETRO_DET.
 */

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID IN ( SELECT ID_PARAMETRO
                        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
                        WHERE NOMBRE_PARAMETRO = 'ESTADO_PLAN_CONTRATO'
                        AND ESTADO             = 'Activo');

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE NOMBRE_PARAMETRO = 'ESTADO_PLAN_CONTRATO'
  AND ESTADO           = 'Activo';

COMMIT;
/
