GRANT SELECT, INSERT, UPDATE ON DB_COMPROBANTES.INFO_DOCUMENTO TO DB_FINANCIERO;
GRANT SELECT ON DB_COMPROBANTES.SEQ_INFO_DOCUMENTO TO DB_FINANCIERO;

--
GRANT SELECT ON DB_COMPROBANTES.ADMI_TIPO_DOCUMENTO TO DB_FINANCIERO;
GRANT SELECT ON DB_COMPROBANTES.ADMI_EMPRESA TO DB_FINANCIERO;
GRANT SELECT ON DB_COMPROBANTES.INFO_TIPO_IDENTIFICACION TO DB_FINANCIERO;
GRANT SELECT, INSERT ON DB_COMPROBANTES.ADMI_USUARIO TO DB_FINANCIERO;
GRANT SELECT, INSERT ON DB_COMPROBANTES.ADMI_USUARIO_EMPRESA TO DB_FINANCIERO;
GRANT SELECT ON DB_COMPROBANTES.SEQ_ADMI_USUARIO TO DB_FINANCIERO;
GRANT SELECT ON DB_COMPROBANTES.SEQ_ADMI_USUARIO_EMPRESA TO DB_FINANCIERO;
