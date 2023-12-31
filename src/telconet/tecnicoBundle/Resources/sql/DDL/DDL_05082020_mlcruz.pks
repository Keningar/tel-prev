--Mejora el costo de la vista DB_FINANCIERO.VISTA_FACTURAS_ABIERTAS de 7255 a 7246
CREATE INDEX DB_FINANCIERO.IDX_DOC_FINANCIERO_CAB_1 ON DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB
  (
    TIPO_DOCUMENTO_ID ASC,
    UPPER(ESTADO_IMPRESION_FACT) ASC
  );


--Mejora el costo de la vista DB_COMERCIAL.VISTA_PM_CORTE_FAC_ABI de 2355 a 1955
CREATE INDEX DB_COMERCIAL.IDX_INFO_SERVICIO_1 ON DB_COMERCIAL.INFO_SERVICIO
  (
    PUNTO_ID ASC,
    ESTADO ASC,
    ES_VENTA ASC
  );
/