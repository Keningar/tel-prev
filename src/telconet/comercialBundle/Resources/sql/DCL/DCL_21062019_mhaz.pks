/**
 * DEBEN EJECUTARSE EN DB_COMERCIAL
 * Scripts para realizar select en INFO_CONTRATO_FORMA_PAGO_HIST
 * desde paquete FNCK_CANCELACION_VOL 
 * @author Madeline Haz <mhaz@telconet.ec>
 * @version 1.0
 * @since 21-06-2019.
 */

GRANT SELECT  ON DB_COMERCIAL.INFO_CONTRATO_FORMA_PAGO_HIST TO DB_FINANCIERO;

COMMIT;
