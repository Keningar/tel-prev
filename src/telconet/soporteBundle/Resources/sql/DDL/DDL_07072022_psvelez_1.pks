/**
 * DEBE EJECUTARSE EN DB_SOPORTE
 * Se modifica campo DISPOSITIVO_ID en tabla INFO_TRACKING_MAP_HIST
 * @author Pedro Velez <psvelez@telconet.ec>
 * @version 1.0 07-01-2022 - Versión Inicial.
 */
ALTER TABLE DB_Soporte.INFO_TRACKING_MAP_HIST
ADD (DISPOSITIVO_ID varchar2(225));

COMMENT ON COLUMN DB_SOPORTE.INFO_TRACKING_MAP_HIST.DISPOSITIVO_ID 
IS 'Serie logica del dispositivo del jefe de cuadrilla';

/
