/**
 * DEBE EJECUTARSE EN DB_SOPORTE
 * Script para eliminar tabla y secuencia creada para respaldar los servicios afectados por un caso, al ser actualizados o eliminados
 * de la estructura principal DB_SOPORTE.INFO_PARTE_AFECTADA
 * @author David De La Cruz <ddelacruz@telconet.ec>
 * @version 1.0 18-03-2022 - Versión Inicial.
 */

DROP TABLE DB_SOPORTE.INFO_PARTE_AFECTADA_HIST;
DROP SEQUENCE DB_SOPORTE.SEQ_INFO_PARTE_AFECTADA_HIST;

ALTER TABLE DB_SOPORTE.INFO_TAREA_SEGUIMIENTO MODIFY USR_CREACION VARCHAR2(20);
