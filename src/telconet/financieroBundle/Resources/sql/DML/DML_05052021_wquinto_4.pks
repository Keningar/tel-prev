/**
 * @author Wilson Quinto <wquinto@telconet.ec>
 * @version 1.0
 * @since 29-04-2021    
 * Se crea la sentencia DML se añade indice a tabla Info Pago para busqueda de excel
 */

 CREATE INDEX "DB_FINANCIERO"."IDX_NUM_PAGO_VAL_TOTAL_EMP_ID" ON "DB_FINANCIERO"."INFO_PAGO_CAB" ("NUMERO_PAGO", "VALOR_TOTAL", "EMPRESA_ID") ;