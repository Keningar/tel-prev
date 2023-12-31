ALTER TABLE DB_COMERCIAL.INFO_DASHBOARD_SERVICIO 
ADD (GRUPO VARCHAR2(100) );

ALTER TABLE DB_COMERCIAL.INFO_DASHBOARD_SERVICIO 
ADD (SUBGRUPO VARCHAR2(100) );

ALTER TABLE DB_COMERCIAL.INFO_DASHBOARD_SERVICIO 
ADD (CATEGORIA VARCHAR2(100) );

ALTER TABLE DB_COMERCIAL.INFO_DASHBOARD_SERVICIO 
ADD (ACCION VARCHAR2(100) );

CREATE INDEX DB_COMERCIAL.IDX_IDAS_CATEGORIA ON DB_COMERCIAL.INFO_DASHBOARD_SERVICIO (CATEGORIA);

CREATE INDEX DB_COMERCIAL.IDX_IDAS_GRUPO ON DB_COMERCIAL.INFO_DASHBOARD_SERVICIO (GRUPO);

CREATE INDEX DB_COMERCIAL.IDX_IDAS_ACCION ON DB_COMERCIAL.INFO_DASHBOARD_SERVICIO (ACCION);

CREATE INDEX DB_COMERCIAL.IDX_IDAS_SUBGRUPO ON DB_COMERCIAL.INFO_DASHBOARD_SERVICIO (SUBGRUPO);

COMMENT ON COLUMN DB_COMERCIAL.INFO_DASHBOARD_SERVICIO.GRUPO IS 'GRUPO AL QUE PERTENECE EL PRODUCTO';

COMMENT ON COLUMN DB_COMERCIAL.INFO_DASHBOARD_SERVICIO.SUBGRUPO IS 'SUBGRUPO AL QUE PERTENECE EL PRODUCTO';

COMMENT ON COLUMN DB_COMERCIAL.INFO_DASHBOARD_SERVICIO.CATEGORIA IS 'CATEGORIA A LA QUE PERTENECE EL PRODUCTO';

COMMENT ON COLUMN DB_COMERCIAL.INFO_DASHBOARD_SERVICIO.ACCION IS 'ACCION POR EL CUAL FUE MIGRADO EL SERVICIO AL DASHBOARD';

ALTER TABLE DB_COMERCIAL.INFO_DASHBOARD_SERVICIO 
ADD (MRC NUMBER );

ALTER TABLE DB_COMERCIAL.INFO_DASHBOARD_SERVICIO 
ADD (NRC NUMBER );

ALTER TABLE DB_COMERCIAL.INFO_DASHBOARD_SERVICIO 
ADD (OFICINA_VENDEDOR_ID NUMBER );

CREATE INDEX DB_COMERCIAL.IDX_IDAS_MRC ON DB_COMERCIAL.INFO_DASHBOARD_SERVICIO (MRC);

CREATE INDEX DB_COMERCIAL.IDX_IDAS_NRC ON DB_COMERCIAL.INFO_DASHBOARD_SERVICIO (NRC);

CREATE INDEX DB_COMERCIAL.IDX_IDAS_OFICINA_VENDEDOR ON DB_COMERCIAL.INFO_DASHBOARD_SERVICIO (OFICINA_VENDEDOR_ID);

COMMENT ON COLUMN DB_COMERCIAL.INFO_DASHBOARD_SERVICIO.MRC IS 'MRC DEL SERVICIO';

COMMENT ON COLUMN DB_COMERCIAL.INFO_DASHBOARD_SERVICIO.NRC IS 'NRC DEL SERVICIO';

COMMENT ON COLUMN DB_COMERCIAL.INFO_DASHBOARD_SERVICIO.OFICINA_VENDEDOR_ID IS 'OFICINA DEL VENDEDOR DEL SERVICIO';

ALTER TABLE DB_COMERCIAL.INFO_DASHBOARD_SERVICIO 
ADD (MOTIVO_PADRE_CANCELACION VARCHAR2(300) );

ALTER TABLE DB_COMERCIAL.INFO_DASHBOARD_SERVICIO 
ADD (MOTIVO_CANCELACION VARCHAR2(300) );

CREATE INDEX DB_COMERCIAL.IDX_IDAS_MOTIVO_CANCELACION ON DB_COMERCIAL.INFO_DASHBOARD_SERVICIO (MOTIVO_CANCELACION);

CREATE INDEX DB_COMERCIAL.IDX_IDAS_MOT_PADRE_CANCEL ON DB_COMERCIAL.INFO_DASHBOARD_SERVICIO (MOTIVO_PADRE_CANCELACION);

COMMENT ON COLUMN DB_COMERCIAL.INFO_DASHBOARD_SERVICIO.MOTIVO_PADRE_CANCELACION IS 'MOTIVO PADRE POR EL CUAL SE CANCELO EL SERVICIO';

COMMENT ON COLUMN DB_COMERCIAL.INFO_DASHBOARD_SERVICIO.MOTIVO_CANCELACION IS 'MOTIVO DE CANCELACION SELECCIONADO EN LA SOLICITUD MASIVA';

GRANT EXECUTE ON DB_FINANCIERO.FNCK_CONSULTS TO BI_FINANCIERO;

GRANT SELECT ON DB_FINANCIERO.VISTA_ESTADO_CUENTA_RESUMIDO TO BI_FINANCIERO;

GRANT EXECUTE ON DB_FINANCIERO.FNKG_CARTERA_CLIENTES TO BI_FINANCIERO;

GRANT EXECUTE ON DB_FINANCIERO.FNCK_CONSULTS TO BI_FINANCIERO;