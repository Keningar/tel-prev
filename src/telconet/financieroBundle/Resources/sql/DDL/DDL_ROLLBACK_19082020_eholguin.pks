/**
 * @author Edgar Holguín <eholguin@telconet.ec>
 * @version 1.0
 * @since 15-09-2020    
 * Se crea la sentencia DDL para eliminación de tablas.
 */
 
--SE ELIMINA LA TABLA 'ADMI_FORMATO_PAGO_AUTOMATICO'.
DROP TABLE DB_FINANCIERO.ADMI_FORMATO_PAGO_AUTOMATICO;

--SE ELIMINA LA SECUENCIA.
DROP SEQUENCE DB_FINANCIERO.SEQ_ADMI_FORMATO_PAGO_AUT;

--SE ELIMINA LA TABLA 'INFO_PAGO_AUTOMATICO_HIST'.
DROP TABLE DB_FINANCIERO.INFO_PAGO_AUTOMATICO_HIST;

--SE ELIMINA LA SECUENCIA.
DROP SEQUENCE DB_FINANCIERO.SEQ_INFO_PAGO_AUTOMATICO_HIST;

--SE ELIMINA LA TABLA 'INFO_PAGO_AUTOMATICO_DET'.
DROP TABLE DB_FINANCIERO.INFO_PAGO_AUTOMATICO_DET;

--SE ELIMINA LA SECUENCIA.
DROP SEQUENCE DB_FINANCIERO.SEQ_INFO_PAGO_AUTOMATICO_DET;


--SE ELIMINA LA TABLA 'INFO_PAGO_AUTOMATICO_CAB'.
DROP TABLE DB_FINANCIERO.INFO_PAGO_AUTOMATICO_CAB;

--SE ELIMINA LA SECUENCIA.
DROP SEQUENCE DB_FINANCIERO.SEQ_INFO_PAGO_AUTOMATICO_CAB;

COMMIT;
/
