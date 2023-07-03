-- CREACION TABLA 
CREATE TABLE DB_FINANCIERO.INFO_TMP_PRODUCTOS
(
  UUID                  VARCHAR2(60),
  ID_DOCUMENTO          NUMBER(38),
  PUNTO_ID              NUMBER(38),
  OFICINA_ID            NUMBER(38),
  ESTADO_IMPRESION_FACT VARCHAR2(15)
);
-- COMENTARIO TABLA 
COMMENT ON TABLE DB_FINANCIERO.INFO_TMP_PRODUCTOS IS 'TABLA QUE ALMACENA LOS DOCUMENTOS SEGUN LA OFICINA';

-- COMENTARIOS COLUMNAS
COMMENT ON COLUMN DB_FINANCIERO.INFO_TMP_PRODUCTOS.uuid                  IS 'IDENTIFICADOR UNICO DE LA TRANSACCION';
COMMENT ON COLUMN DB_FINANCIERO.INFO_TMP_PRODUCTOS.id_documento          IS 'IDENTIFICADOR UNICO DEL DOCUMENTO A PROCESAR';
COMMENT ON COLUMN DB_FINANCIERO.INFO_TMP_PRODUCTOS.punto_id              IS 'PUNTO ID';
COMMENT ON COLUMN DB_FINANCIERO.INFO_TMP_PRODUCTOS.oficina_id            IS 'CODIGO DE LA OFICINA';
COMMENT ON COLUMN DB_FINANCIERO.INFO_TMP_PRODUCTOS.estado_impresion_fact IS 'ESTADO DE IMPRESION';
/