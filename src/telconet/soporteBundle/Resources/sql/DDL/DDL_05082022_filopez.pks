/**
 * DEBE EJECUTARSE EN DB_SOPORTE
 * Script para crear el indice IDX_DETALLE_INCIDENCIA_ID en la talba INFO_INCIDENCIA_NOTIF
 * @author Fernando López <filopez@telconet.ec>
 * @version 1.0 05-08-2022 - Versión Inicial.
 */
--
 CREATE INDEX DB_SOPORTE.IDX_DETALLE_INCIDENCIA_ID ON DB_SOPORTE.INFO_INCIDENCIA_NOTIF (DETALLE_INCIDENCIA_ID);
--
--
COMMIT;
/