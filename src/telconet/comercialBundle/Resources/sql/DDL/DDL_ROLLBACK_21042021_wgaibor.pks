/**
 * @author Walther Joao Gaibor <wgaibor@telconet.ec>
 * @version 1.0
 * @since 04-05-2021
 * Se elimina las columnas frecuencia, termino_condicion de la tabla DB_GENERAL.ADMI_PRODUCTO
 */

ALTER TABLE DB_COMERCIAL.ADMI_PRODUCTO DROP COLUMN FRECUENCIA;

ALTER TABLE DB_COMERCIAL.ADMI_PRODUCTO DROP COLUMN TERMINO_CONDICION;

/**
 * @author Walther Joao Gaibor <wgaibor@telconet.ec>
 * @version 1.0
 * @since 04-05-2021
 * Se elimina la tabla DB_COMERCIAL.ADMI_PROD_CARAC_COMPORTAMIENTO
 */

DROP SEQUENCE DB_COMERCIAL.SEQ_ADMI_PROD_CARAC_COMP;

DROP TABLE DB_COMERCIAL.ADMI_PROD_CARAC_COMPORTAMIENTO;
