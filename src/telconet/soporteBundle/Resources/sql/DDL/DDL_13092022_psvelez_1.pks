/**
 * DEBE EJECUTARSE EN DB_SOPORTE
 * Se agrega campo CODIGO_TRABAJO,FOTO_TECNICO, CEDULA_TECNICO en tabla INFO_TRACKING_MAP_HIST
 * @author Pedro Velez <psvelez@telconet.ec>
 * @version 1.0 08-09-2022 - Versión Inicial.
 */

ALTER TABLE DB_Soporte.INFO_TRACKING_MAP_HIST ADD (CODIGO_TRABAJO varchar2(15));
ALTER TABLE DB_Soporte.INFO_TRACKING_MAP_HIST ADD (CEDULA_TECNICO varchar2(20));

COMMENT ON COLUMN DB_SOPORTE.INFO_TRACKING_MAP_HIST.CODIGO_TRABAJO 
IS 'Código de trabajo para validacion de tecnicos en puntos de clientes';
COMMENT ON COLUMN DB_SOPORTE.INFO_TRACKING_MAP_HIST.CEDULA_TECNICO 
IS 'Número de identificación del técnico';

/
