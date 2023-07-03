/**
 * DEBE EJECUTARSE EN DB_COMUNICACION
 * Script para crear nuevas clases de documentos para las imagenes de Noticias y plantillas pendientes de migración del modulo de Soporte
 * @author David De La Cruz <ddelacruz@telconet.ec>
 * @version 1.0 22-06-2021 - Versión Inicial.
 */


Insert into DB_COMUNICACION.admi_clase_documento (ID_CLASE_DOCUMENTO,NOMBRE_CLASE_DOCUMENTO,DESCRIPCION_CLASE_DOCUMENTO,ESTADO,USR_CREACION,FE_CREACION,USR_ULT_MOD,FE_ULT_MOD,VISIBLE) 
values (DB_COMUNICACION.SEQ_ADMI_CLASE_DOCUMENTO.nextval,'Notificacion Int. Noticia - Imagen normal','Identificacion de imagenes de tamanio normal subidas para la plantilla de Noticias','Activo','ddelacruz',systimestamp,null,null,'NO');

Insert into DB_COMUNICACION.admi_clase_documento (ID_CLASE_DOCUMENTO,NOMBRE_CLASE_DOCUMENTO,DESCRIPCION_CLASE_DOCUMENTO,ESTADO,USR_CREACION,FE_CREACION,USR_ULT_MOD,FE_ULT_MOD,VISIBLE) 
values (DB_COMUNICACION.SEQ_ADMI_CLASE_DOCUMENTO.nextval,'Notificacion Ext. Plantilla - Imagen normal','Identificacion de imagenes de tamanio normal subidas para la plantilla de Notificaciones externas','Activo','ddelacruz',systimestamp,null,null,'NO');

commit;

/
