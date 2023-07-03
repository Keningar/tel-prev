--DDL PARA AGREGAR LA COLUMNA TIPO
ALTER TABLE DB_SOPORTE.INFO_TAREA_TIEMPO_PARCIAL ADD TIPO VARCHAR2(1);
COMMENT ON COLUMN DB_SOPORTE.INFO_TAREA_TIEMPO_PARCIAL.TIPO IS 'C = CLIENTE, CASO CONTRARIO EMPRESA';

--DDL PARA AGREGAR LA COLUMNA TIEMPO
ALTER TABLE DB_SOPORTE.INFO_TAREA_TIEMPO_PARCIAL ADD TIEMPO NUMBER;
COMMENT ON COLUMN DB_SOPORTE.INFO_TAREA_TIEMPO_PARCIAL.TIEMPO IS 'TIEMPO QUE DEBE DURAR LA PAUSA O REPROGRAMACIÓN';

--DDL PARA AGREGAR LA COLUMNA TIEMPO_TOTAL
ALTER TABLE DB_SOPORTE.INFO_CASO_TIEMPO_ASIGNACION ADD TIEMPO_TOTAL NUMBER;
COMMENT ON COLUMN DB_SOPORTE.INFO_CASO_TIEMPO_ASIGNACION.TIEMPO_TOTAL IS 'TIEMPO TOTAL DEL CASO DESDE QUE SE HABRE HASTA QUE SE CIERRA';

--GRANT PARA QUE EL ESQUEMA DB_SOPORTE PUEDA UTILIZAR LOS MÉTODOS DE ENVIO DE NOTIFICACIÓN DEL ESQUEMA DB_FINANCIERO
GRANT EXECUTE ON DB_FINANCIERO.FNKG_NOTIFICACIONES TO DB_SOPORTE;

/