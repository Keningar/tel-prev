/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Script para realizar select para DB_INFRAESTRUCTURA
 * @author Joseé Bedón Sánchez <jobedon@telconet.ec>
 * @version 1.0 26-11-2019 - Versión Inicial.
 */
GRANT EXECUTE ON DB_GENERAL.GNKG_WEB_SERVICE TO DB_INFRAESTRUCTURA;

/**
 * DEBE EJECUTARSE EN DB_COMERCIAL.
 * Script para realizar select para DB_INFRAESTRUCTURA 
 * @author Joseé Bedón Sánchez <jobedon@telconet.ec>
 * @version 1.0 26-11-2019 - Versión Inicial.
 */
GRANT SELECT ON DB_COMERCIAL.INFO_PUNTO_FORMA_CONTACTO TO DB_INFRAESTRUCTURA;
GRANT EXECUTE ON DB_COMERCIAL.CMKG_REPORTE_APROB_CONTRATOS TO DB_INFRAESTRUCTURA;

/**
 * DEBE EJECUTARSE EN DB_FINANCIERO.
 * Script para realizar select para DB_INFRAESTRUCTURA
 * @author Joseé Bedón Sánchez <jobedon@telconet.ec>
 * @version 1.0 26-11-2019 - Versión Inicial.
 */
GRANT SELECT ON DB_FINANCIERO.ADMI_CICLO TO DB_INFRAESTRUCTURA;
GRANT EXECUTE ON DB_FINANCIERO.FNCK_FACTURACION TO DB_INFRAESTRUCTURA;


/