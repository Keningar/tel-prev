--REVOCAR CONSULTA DE SEQUENCIALES PARA ECUCERT
REVOKE SELECT ON DB_COMUNICACION.SEQ_INFO_COMUNICACION FROM DB_SOPORTE;
REVOKE SELECT ON DB_COMUNICACION.SEQ_INFO_DOCUMENTO FROM DB_SOPORTE;
REVOKE SELECT ON DB_COMUNICACION.SEQ_DOCUMENTO_COMUNICACION FROM DB_SOPORTE;

REVOKE INSERT,SELECT ON DB_COMUNICACION.INFO_COMUNICACION FROM DB_SOPORTE;
REVOKE INSERT,SELECT ON DB_COMUNICACION.INFO_DOCUMENTO FROM DB_SOPORTE;
REVOKE INSERT,SELECT ON DB_COMUNICACION.INFO_DOCUMENTO_COMUNICACION FROM DB_SOPORTE;