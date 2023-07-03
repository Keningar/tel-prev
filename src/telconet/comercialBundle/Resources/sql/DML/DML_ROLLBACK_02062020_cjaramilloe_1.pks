/**
 * Documentación DELETE en ADMI_PARAMETRO_DET, ADMI_ROL
 *
 * Eliminación parámetros y roles.
 *
 * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
 * @version 1.0 02-06-2020
 */

DELETE FROM DB_GENERAL.ADMI_ROL t WHERE t.DESCRIPCION_ROL = 'Representante Legal Juridico' AND USR_CREACION = 'cjaramilloe';
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET t WHERE t.DESCRIPCION = 'MEDIO_DESCARGA_CERTIFICADO' AND USR_CREACION = 'cjaramilloe';
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET t WHERE t.DESCRIPCION = 'OPERACION_SUBIDA_DOCUMENTOS' AND USR_CREACION = 'cjaramilloe';
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET t WHERE t.DESCRIPCION = 'RUTA_SUBIDA_DOCUMENTOS' AND USR_CREACION = 'cjaramilloe';

 DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET t WHERE t.PARAMETRO_ID  = 
 (SELECT MAX(ID_PARAMETRO) FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'DOCUMENTOS_OBLIGATORIO' AND ESTADO = 'Activo') 
 AND t.DESCRIPCION = 'CEDULA' AND USR_CREACION = 'cjaramilloe';
 
 DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET t WHERE t.PARAMETRO_ID  = 
 (SELECT MAX(ID_PARAMETRO) FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'DOCUMENTOS_OBLIGATORIO' AND ESTADO = 'Activo') 
 AND t.DESCRIPCION = 'CEDULA REVERSO' AND USR_CREACION = 'cjaramilloe';
 
 DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET t WHERE t.PARAMETRO_ID  = 
 (SELECT MAX(ID_PARAMETRO) FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'DOCUMENTOS_OBLIGATORIO' AND ESTADO = 'Activo') 
 AND t.DESCRIPCION = 'FOTO' AND USR_CREACION = 'cjaramilloe';
 
 DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET t WHERE t.PARAMETRO_ID  = 
 (SELECT MAX(ID_PARAMETRO) FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'DOCUMENTOS_OBLIGATORIO' AND ESTADO = 'Activo') 
 AND t.DESCRIPCION = 'RUC' AND USR_CREACION = 'cjaramilloe';
 
 DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET t WHERE t.PARAMETRO_ID  = 
 (SELECT MAX(ID_PARAMETRO) FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'DOCUMENTOS_OBLIGATORIO' AND ESTADO = 'Activo') 
 AND t.DESCRIPCION = 'NOMBRAMIENTO' AND USR_CREACION = 'cjaramilloe';
 
 DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET t WHERE t.PARAMETRO_ID  = 
 (SELECT MAX(ID_PARAMETRO) FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'DOCUMENTOS_OBLIGATORIO' AND ESTADO = 'Activo') 
 AND t.DESCRIPCION = 'FORMA DE PAGO' AND USR_CREACION = 'cjaramilloe';
 
COMMIT;
/