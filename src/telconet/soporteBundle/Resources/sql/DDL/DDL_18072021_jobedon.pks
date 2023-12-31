/**
 * DEBE EJECUTARSE EN DB_SOPORTE
 * Se modifica campo usr_creacion para nuevos usuarios de cajamarca
 * @author José Bedón Sánchez <jobedon@telconet.ec>
 * @version 1.0 08-07-2021 - Versión Inicial.
 */

ALTER TABLE DB_SOPORTE.INFO_DETALLE_ASIGNACION MODIFY USR_CREACION VARCHAR2(35);
ALTER TABLE DB_SOPORTE.INFO_DETALLE_HISTORIAL MODIFY USR_CREACION VARCHAR2(35);
ALTER TABLE DB_COMUNICACION.INFO_DOCUMENTO MODIFY USR_CREACION VARCHAR2(35);
ALTER TABLE DB_COMUNICACION.INFO_DOCUMENTO MODIFY USR_ULT_MOD VARCHAR2(35);
ALTER TABLE DB_COMUNICACION.INFO_COMUNICACION MODIFY USR_CREACION VARCHAR2(35);
ALTER TABLE DB_COMUNICACION.INFO_DOCUMENTO_COMUNICACION MODIFY USR_CREACION VARCHAR2(35);
ALTER TABLE DB_AUDITOR.INFO_DETALLE_AUDIT MODIFY USR_CREACION VARCHAR2(35);
/