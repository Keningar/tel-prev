--CREACIÓN DE LAS COLUMNAS MODELO, MAC E IP DE UN ELEMENTO
ALTER TABLE DB_INFRAESTRUCTURA.INFO_ELEMENTO_INSTALACION ADD (
  MODELO_ELEMENTO      	VARCHAR2(50),
  MAC_ELEMENTO      	VARCHAR2(20),
  IP_ELEMENTO      	VARCHAR2(20),
  SERVICIO_ID           NUMBER NULL
);

COMMENT ON COLUMN DB_INFRAESTRUCTURA.INFO_ELEMENTO_INSTALACION.MODELO_ELEMENTO      IS 'Modelo del elemento';
COMMENT ON COLUMN DB_INFRAESTRUCTURA.INFO_ELEMENTO_INSTALACION.MAC_ELEMENTO      	IS 'Mac del elemento';
COMMENT ON COLUMN DB_INFRAESTRUCTURA.INFO_ELEMENTO_INSTALACION.IP_ELEMENTO 			IS 'Ip del elemento';
COMMENT ON COLUMN DB_INFRAESTRUCTURA.INFO_ELEMENTO_INSTALACION.SERVICIO_ID 			IS 'Id del servicio';



COMMIT;