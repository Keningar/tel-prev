/**
 * Documentación DELETE ADMI_PARAMETRO_CAB, ADMI_PARAMETRO_DET
 *
 * Eliminación parámetro para estados de objetos en TM Comercial.
 *
 * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
 * @version 1.0 17-03-2020
 */

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET t WHERE t.DESCRIPCION = 'ESTADOS DEL PUNTO PARA CONSULTA DE DOCUMENTOS DIGITALES' AND USR_CREACION = 'cjaramilloe';
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB t WHERE t.NOMBRE_PARAMETRO = 'ESTADOS_TM_COMERCIAL' AND USR_CREACION = 'cjaramilloe';
COMMIT;
/
