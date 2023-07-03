/**
 * DEBE EJECUTARSE EN DB_COMERCIAL.
 * Script eliminar el paquete creado en DB_INFRAESTRUCTURA
 * @author Joseé Bedón Sánchez <jobedon@telconet.ec>
 * @version 1.0 26-11-2019 - Versión Inicial.
 */
DROP PACKAGE DB_INFRAESTRUCTURA.INFRKG_KONIBIT;


/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Script para rollback de permisos en DB_GENERAL
 * @author Joseé Bedón Sánchez <jobedon@telconet.ec>
 * @version 1.0 26-11-2019 - Versión Inicial.
 */
REVOKE EXECUTE ON DB_GENERAL.GNKG_WEB_SERVICE FROM DB_INFRAESTRUCTURA;


/**
 * DEBE EJECUTARSE EN DB_COMERCIAL.
 * Script para rollback de permisos en DB_COMERCIAL
 * @author Joseé Bedón Sánchez <jobedon@telconet.ec>
 * @version 1.0 26-11-2019 - Versión Inicial.
 */
REVOKE SELECT ON DB_COMERCIAL.INFO_PUNTO_FORMA_CONTACTO FROM DB_INFRAESTRUCTURA;
REVOKE EXECUTE ON DB_COMERCIAL.CMKG_REPORTE_APROB_CONTRATOS FROM DB_INFRAESTRUCTURA;

/**
 * DEBE EJECUTARSE EN DB_FINANCIERO.
 * Script para rollback de permisos en DB_FINANCIERO
 * @author Joseé Bedón Sánchez <jobedon@telconet.ec>
 * @version 1.0 26-11-2019 - Versión Inicial.
 */
REVOKE SELECT ON DB_FINANCIERO.ADMI_CICLO FROM DB_INFRAESTRUCTURA;
REVOKE EXECUTE ON DB_FINANCIERO.FNCK_FACTURACION FROM DB_INFRAESTRUCTURA;


/
