UPDATE DB_FINANCIERO.ADMI_FORMATO_DEBITO_RESPUESTA
SET COL_ESTADO                    = 0,
  MENSAJE_OK                      = 'PROCESO OK',
  COL_DESCRIPCION_ESTADO          = 0,
  COL_VALOR_ENVIADO               = 3,
  COL_CUENTA                      = 12,
  COL_IDENTIFICACION              = 26,
  COL_VALOR_DEBITADO              = 3,
  FILA_INICIA                     = 1,
  COL_REFERENCIA                  = 12
WHERE ID_FORMATO_DEBITO_RESPUESTA =21;
