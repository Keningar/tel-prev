/**
 * DEBE EJECUTARSE EN DB_COMUNICACION
 * Script para reversar la creación de plantilla para bus de pagos de TN
 * @author David De La Cruz <ddelacruz@telconet.ec>
 * @version 1.0 
 * @since 11-04-2022 - Versión Inicial.
 */

DELETE FROM DB_COMUNICACION.ADMI_PLANTILLA
WHERE CODIGO = 'PAL-EXTRANET'
AND MODULO = 'SOPORTE' AND USR_CREACION = 'ddelacruz';

COMMIT;