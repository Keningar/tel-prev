/**
 * @author Wilson Quinto <wquinto@telconet.ec>
 * @version 1.0
 * @since 29-04-2021    
 * Se crea la sentencia DML se añade columna de pago a detalle masivo
 */

ALTER TABLE DB_INFRAESTRUCTURA.INFO_PROCESO_MASIVO_DET ADD PAGO_ID NUMBER;

COMMENT ON COLUMN DB_INFRAESTRUCTURA.INFO_PROCESO_MASIVO_DET.PAGO_ID IS 'ID DE PAGO';