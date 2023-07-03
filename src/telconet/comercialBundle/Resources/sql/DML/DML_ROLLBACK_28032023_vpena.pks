/**
 * Script para eliminar los logines auxiliares de los servicios que aplican para paquete de horas de soporte.
 * @author Victor Peña <vpena@telconet.ec>
 * @version 1.0
 * @since 11-04-2023
 */

--LOGINES AUXILIARES
UPDATE DB_COMERCIAL.INFO_SERVICIO 
SET LOGIN_AUX = NULL 
WHERE ID_SERVICIO IN (SELECT SERVICIO_ID FROM DB_COMERCIAL.INFO_SERVICIO_HISTORIAL WHERE USR_CREACION = 'vpena' AND ACCION = 'genLoginAux' AND ESTADO = 'Activo');

--INFO_SERVICIO_HISTORIAL
DELETE FROM DB_COMERCIAL.INFO_SERVICIO_HISTORIAL 
WHERE USR_CREACION = 'vpena' 
AND ACCION = 'genLoginAux' 
AND ESTADO = 'Activo';

COMMIT;

/