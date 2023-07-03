/**
 * @author Edgar Holguín <eholguin@telconet.ec>
 * @version 1.0
 * @since 27-05-2020    
 * Se crean las sentencias DML para  creación de columna DCTO_APLICADO.
 */
ALTER TABLE DB_COMERCIAL.INFO_CONTRATO_FORMA_PAGO_HIST ADD (DCTO_APLICADO NUMBER);

COMMENT ON COLUMN "DB_COMERCIAL"."INFO_CONTRATO_FORMA_PAGO_HIST"."DCTO_APLICADO"  IS 'Porcentaje de descuento aplicado en último cambio de forma de pago.';

/
