ALTER TABLE DB_COMERCIAL.INFO_CONTRATO_FORMA_PAGO_HIST 
ADD (NOMBRE_ARCHIVO_ABU VARCHAR2(200) );

COMMENT ON COLUMN DB_COMERCIAL.INFO_CONTRATO_FORMA_PAGO_HIST.NOMBRE_ARCHIVO_ABU IS 'Campo para almacenar el nombre del archivo de tarjetas ABU';
