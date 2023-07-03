    /**
     * DEBEN EJECUTARSE EN DB_COMPROBANTES.
     * Scripts para realizar select y update para DB_FINANCIERO en la FACTURACIÓN OFFLINE
     * @author Jorge Guerrero <jguerrerop@telconet.ec>
     * @version 1.0 31-12-2017 - Versión Inicial.
     */
    grant select,update on DB_COMPROBANTES.INFO_NOTIFICACION to DB_FINANCIERO;
    grant select,update on DB_COMPROBANTES.ADMI_PARAMETRO_CAB to DB_FINANCIERO;
    grant select,update on DB_COMPROBANTES.ADMI_PARAMETRO_DET to DB_FINANCIERO;
    grant select,update on DB_COMPROBANTES.ADMI_USUARIO to DB_FINANCIERO;
    grant select,update on DB_COMPROBANTES.ADMI_USUARIO_EMPRESA to DB_FINANCIERO;
    grant select,update on DB_COMPROBANTES.INFO_DOCUMENTO to DB_FINANCIERO;
    grant select on DB_COMPROBANTES.ADMI_TIPO_DOCUMENTO to DB_FINANCIERO;
    grant select on DB_COMPROBANTES.ADMI_EMPRESA to DB_FINANCIERO;
    grant select on DB_COMPROBANTES.INFO_TIPO_IDENTIFICACION to DB_FINANCIERO;