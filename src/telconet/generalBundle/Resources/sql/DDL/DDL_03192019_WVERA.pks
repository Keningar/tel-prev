--ALTER A LA TABLA DB_COMUNICACION.INFO_DOCUMENTO PARA AGREGAR EL NUMERO DEL REGISTRO CUARILLA HISTORIAL

--ADD/MODIFY COLUMNS
   ALTER TABLE DB_COMUNICACION.INFO_DOCUMENTO ADD CUADRILLA_HISTORIAL_ID NUMBER;
--ADD COMMENTS TO THE COLUMNS
   COMMENT ON COLUMN 
   DB_COMUNICACION.INFO_DOCUMENTO.CUADRILLA_HISTORIAL_ID  IS 'Campo que ayudará a filtrar los documentos de una cuadrilla fiscalizada por el apartado móvil';
