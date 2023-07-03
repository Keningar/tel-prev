--=======================================================================
--      Grant para envio de notificaciones
--=======================================================================
GRANT EXECUTE ON DB_COMUNICACION.CUKG_TRANSACTIONS TO DB_INFRAESTRUCTURA;

--=======================================================================
--      Grant para creación de tarea
--=======================================================================
GRANT EXECUTE ON DB_SOPORTE.SPKG_INCIDENCIA_ECUCERT TO DB_INFRAESTRUCTURA;

--=======================================================================
--      Grant para registro de errores
--=======================================================================
GRANT EXECUTE ON DB_GENERAL.GNRLPCK_UTIL TO DB_INFRAESTRUCTURA;

--=======================================================================
--      Grant para realizar consultas de información
--=======================================================================
GRANT SELECT ON DB_GENERAL.ADMI_PARROQUIA TO DB_INFRAESTRUCTURA;
GRANT SELECT ON DB_GENERAL.ADMI_CANTON TO DB_INFRAESTRUCTURA;
GRANT SELECT ON DB_GENERAL.ADMI_PARAMETRO_CAB TO DB_INFRAESTRUCTURA;
GRANT SELECT ON DB_GENERAL.ADMI_PARAMETRO_DET TO DB_INFRAESTRUCTURA;

--=======================================================================
--      Grant para obtener plantilla de envio de notificación.
--=======================================================================
GRANT SELECT ON DB_COMUNICACION.ADMI_PLANTILLA TO DB_INFRAESTRUCTURA;

--=======================================================================
--      Grant para consultar información de Contacto
--=======================================================================
GRANT SELECT ON DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL TO DB_INFRAESTRUCTURA;
GRANT SELECT ON DB_COMERCIAL.INFO_PERSONA TO DB_INFRAESTRUCTURA;
GRANT SELECT ON DB_COMERCIAL.INFO_EMPRESA_ROL TO DB_INFRAESTRUCTURA;
GRANT SELECT ON DB_COMERCIAL.ADMI_ROL TO DB_INFRAESTRUCTURA;
GRANT SELECT ON DB_COMERCIAL.ADMI_TIPO_ROL TO DB_INFRAESTRUCTURA;

/