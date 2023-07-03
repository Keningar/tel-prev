
GRANT SELECT ON  DB_COMERCIAL.INFO_PERSONA TO DB_SOPORTE;
GRANT SELECT ON  DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL TO DB_SOPORTE;
GRANT SELECT ON  DB_COMERCIAL.INFO_EMPRESA_ROL TO DB_SOPORTE;
GRANT SELECT ON  DB_COMERCIAL.INFO_OFICINA_GRUPO TO DB_SOPORTE;

GRANT SELECT ON  DB_GENERAL.ADMI_DEPARTAMENTO TO DB_SOPORTE;
GRANT SELECT ON  DB_GENERAL.ADMI_CANTON TO DB_SOPORTE;
GRANT SELECT ON  DB_GENERAL.ADMI_ROL TO DB_SOPORTE;
GRANT SELECT ON  DB_GENERAL.ADMI_TIPO_ROL TO DB_SOPORTE;

CREATE INDEX idx_info_comuncasoid ON DB_COMUNICACION.INFO_COMUNICACION(CASO_ID);
