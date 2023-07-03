/**
 * Scpit para eliminación de columna. 
 * @author Edgar Holguín <eholguin@telconet.ec>
 * @version 1.0 26-04-2021 
 */

ALTER TABLE DB_FINANCIERO.INFO_PAGO_DET 
DROP COLUMN REFERENCIA_DET_PAGO_AUT_ID;

ALTER TABLE DB_FINANCIERO.INFO_PAGO_AUTOMATICO_HIST 
DROP COLUMN MOTIVO_ID;
            
/

