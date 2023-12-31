 /**
 * @author Ricardo Robles <rrobles@telconet.ec>
 * @version 1.0
 * @since 14-05-2019    
 * Se crea la sentencia DDL para eliminar el campo VALOR_ARCHIVO en la tabla DB_FINANCIERO.INFO_DEBITO_RESPUESTA.
 * Se crea la sentencia DDL para eliminar índice en la tabla DB_FINANCIERO.INFO_DEBITO_RESPUESTA.
 * Se crea la sentencia DDL para eliminar el campo ESTADO_CIERRE en la tabla DB_FINANCIERO.INFO_DEBITO_RESPUESTA.
 * Se crea la sentencia DDL para eliminar el campo OBSERVACION_DESCUADRE en la tabla DB_FINANCIERO.INFO_DEBITO_GENERAL_HISTORIAL.
 */

--SE ELIMINA LA COLUMNA VALOR_ARCHIVO DE LA TABLA INFO_DEBITO_RESPUESTA.
ALTER TABLE DB_FINANCIERO.INFO_DEBITO_RESPUESTA DROP COLUMN VALOR_ARCHIVO;

--SE ELIMINA EN LA TABLA DB_FINANCIERO.INFO_DEBITO_RESPUESTA
DROP INDEX DB_FINANCIERO.INFO_DEBITO_RESPUESTA_INDEX1;

--SE ELIMINA EL CAMPO ESTADO_CIERRE DE LA TABLA INFO_DEBITO_RESPUESTA.
ALTER TABLE DB_FINANCIERO.INFO_DEBITO_RESPUESTA DROP COLUMN ESTADO_CIERRE;

--ELIMINAR CAMPO 'OBSERVACION_DESCUADRE'.
ALTER TABLE DB_FINANCIERO.INFO_DEBITO_GENERAL_HISTORIAL DROP COLUMN OBSERVACION_DESCUADRE;

COMMIT;
/
