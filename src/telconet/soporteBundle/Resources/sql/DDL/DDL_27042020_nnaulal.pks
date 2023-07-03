--Se crea la columna para guardar el login adicional

ALTER TABLE DB_SOPORTE.INFO_INCIDENCIA_DET ADD LOGIN_ADICIONAL VARCHAR2(900);
COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_DET.LOGIN_ADICIONAL  is 'Campo la login adicional del cliente';

/
