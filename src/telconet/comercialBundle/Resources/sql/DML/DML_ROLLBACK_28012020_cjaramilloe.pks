/**
 * Documentación DELETE ADMI_PARAMETRO_CAB, ADMI_PARAMETRO_DET
 *
 * Nuevos parámetros para homologación de formas de pago TM Comercial.
 *
 * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
 * @version 1.0 28-01-2020
 */
 
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID IN ( SELECT T.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB T WHERE T.NOMBRE_PARAMETRO = 'HOMOLOGACION_FORMAS_DE_PAGO' )
  AND USR_CREACION     = 'cjaramilloe';
  
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE NOMBRE_PARAMETRO = 'HOMOLOGACION_FORMAS_DE_PAGO'
  AND USR_CREACION     = 'cjaramilloe';

COMMIT;
/