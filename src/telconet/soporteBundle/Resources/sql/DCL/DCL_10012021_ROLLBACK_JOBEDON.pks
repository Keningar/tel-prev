/**
 * DEBE EJECUTARSE EN DB_SOPORTE
 * Se agrega rollback del grant para poder realizar foreign key
 * @author José Bedón Sánchez <jobedon@telconet.ec>
 * @version 1.0 08-02-2021 - Versión Inicial.
 */
REVOKE REFERENCES ON DB_GENERAL.ADMI_MOTIVO FROM DB_SOPORTE;