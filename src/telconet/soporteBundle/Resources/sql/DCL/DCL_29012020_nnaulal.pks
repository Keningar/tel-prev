--PERMISOS PARA CONSULTAR SEQUENCIALES Y TABLAS PARA ECUCERT
GRANT SELECT ON DB_COMUNICACION.SEQ_INFO_COMUNICACION TO DB_SOPORTE;
GRANT SELECT ON DB_COMUNICACION.SEQ_INFO_DOCUMENTO TO DB_SOPORTE;
GRANT SELECT ON DB_COMUNICACION.SEQ_DOCUMENTO_COMUNICACION TO DB_SOPORTE;

GRANT INSERT,SELECT ON DB_COMUNICACION.INFO_COMUNICACION TO DB_SOPORTE;
GRANT INSERT,SELECT ON DB_COMUNICACION.INFO_DOCUMENTO TO DB_SOPORTE;
GRANT INSERT,SELECT ON DB_COMUNICACION.INFO_DOCUMENTO_COMUNICACION TO DB_SOPORTE;