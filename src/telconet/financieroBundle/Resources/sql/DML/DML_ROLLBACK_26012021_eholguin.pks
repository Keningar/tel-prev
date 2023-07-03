/**
 * @author Edgar Holguín <eholguin@telconet.ec>
 * @version 1.0
 * @since 26-01-2021    
 * Se crea la sentencia DDL para eliminación de registros.
 */
DELETE FROM DB_FINANCIERO.ADMI_FORMATO_PAGO_AUTOMATICO  WHERE TIPO_ARCHIVO='XLS' AND COL_VALIDA_TIPO='RPT_RET';
COMMIT;
/

