ALTER TABLE DB_FINANCIERO.INFO_DOCUMENTO_CARACTERISTICA 
ADD (DOCUMENTO_CARACTERISTICA_ID NUMBER );

COMMENT ON COLUMN DB_FINANCIERO.INFO_DOCUMENTO_CARACTERISTICA.DOCUMENTO_CARACTERISTICA_ID IS 'PERMITE RELACIONAR CARACTERISTICAS DEPENDIENTES PARA LA EJECUCION DE UN PROCESO';
/
