/**
 * DEBE EJECUTARSE EN DB_COMUNICACION
 * Script para eliminar clases de documentos para las imagenes de Noticias y plantillas pendientes de migración del modulo de Soporte
 * @author David De La Cruz <ddelacruz@telconet.ec>
 * @version 1.0 22-06-2021 - Versión Inicial.
 */

delete from DB_COMUNICACION.admi_clase_documento
where NOMBRE_CLASE_DOCUMENTO = 'Notificacion Int. Noticia - Imagen normal'
and USR_CREACION = 'ddelacruz';

delete from DB_COMUNICACION.admi_clase_documento
where NOMBRE_CLASE_DOCUMENTO = 'Notificacion Ext. Plantilla - Imagen normal'
and USR_CREACION = 'ddelacruz';

commit;

/
