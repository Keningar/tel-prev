/**
 * DEBE EJECUTARSE EN DB_SOPORTE
 * rollback del archivo DDL_07012022_psvelez_01.pks
 * @author Pedro Velez <psvelez@telconet.ec>
 * @version 1.0 07-01-2022 - Versión Inicial.
 */

ALTER TABLE DB_SOPORTE.INFO_TRACKING_MAP_HIST 
DROP COLUMN DISPOSITIVO_ID;

/
