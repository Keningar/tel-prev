/*
 *
 * Se elimina nuevo parametro de mensajes para notificaciones push.
 *	 
 * @author Andrea Orellana <adorellana@telconet.ec>
 * @version 1.0 03-03-2023
 *
*/

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET DETALLE
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'MENSAJES_ADMIN_NOTIF_PUSH'
    )
AND DETALLE.DESCRIPCION IN('NOTI_PUSH_MENSAJE_ELIMINAR','NOTI_PUSH_MENSAJE_CLONAR');

COMMIT;