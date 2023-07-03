/**
 * @author Kevin Villegas <kmvillegas@telconet.ec>
 * @version 1.0
 * @since 23-08-2022
 * Se agrega la columna ES_NOTIFICADO, para validar las notificaciones
 */

ALTER TABLE DB_FINANCIERO.INFO_PAGO_AUTOMATICO_DET DROP COLUMN ES_NOTIFICADO;

COMMIT;
/