    /**
     * DEBEN EJECUTARSE EN DB_COMPROBANTES.
     * Scripts para realizar select y update para DB_COMERCIAL en la MIGRACIÓN DE CORREOS
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0
     * @since 12-04-2018.
     */
    GRANT SELECT,UPDATE ON DB_COMPROBANTES.ADMI_USUARIO TO DB_COMERCIAL;
    GRANT SELECT,UPDATE ON DB_COMPROBANTES.ADMI_USUARIO_EMPRESA TO DB_COMERCIAL;
