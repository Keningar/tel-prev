/**
 * DEBE EJECUTARSE EN DB_SOPORTE
 * Se crea index
 * @author Katherine Portugal <kportugal@telconet.ec>
 * @version 1.0 22-04-2021 - Versión Inicial.
 */
CREATE INDEX DB_SOPORTE.INFO_CASO_INDEX1 ON DB_SOPORTE.INFO_CASO (TITULO_FIN_HIP ASC);
CREATE INDEX DB_SOPORTE.INFO_CASO_INDEX2 ON DB_SOPORTE.INFO_CASO (FE_CIERRE ASC);

COMMIT;
/
