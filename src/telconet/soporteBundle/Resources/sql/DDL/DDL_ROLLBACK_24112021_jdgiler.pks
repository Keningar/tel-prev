/**
 * DEBE EJECUTARSE EN DB_SOPORTE
 * Script para eliminar tabla y secuencia creados para registrar los campos de indisponibilidad
 * @author Jose Daniel Giler <jdgiler@telconet.ec>
 * @version 1.0 24-11-2021 - Versión Inicial.
 */

--Borra el trigger que actualiza campos de auditoria
DROP TRIGGER DB_SOPORTE.TRG_INDISPONIBILIDAD;

DROP TABLE DB_SOPORTE.INFO_TAREA_INDISPONIBILIDAD;
DROP SEQUENCE DB_SOPORTE.SEQ_INFO_TAREA_INDISPON;


/