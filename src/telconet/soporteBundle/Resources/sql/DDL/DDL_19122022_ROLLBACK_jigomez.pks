/**
 * DEBE EJECUTARSE EN DB_SOPORTE
 * Se modifica campo opcion
 * @author Jorge Gómez Torres <jigomez@telconet.ec>
* @version 1.0 19-12-2022 - Versión Inicial.
 */

ALTER TABLE DB_SOPORTE.INFO_CRITERIO_AFECTADO MODIFY OPCION VARCHAR2(200);

/