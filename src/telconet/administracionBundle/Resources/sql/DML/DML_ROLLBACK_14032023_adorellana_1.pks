--ROLLBACK INSERT DB_GENERAL.ADMI_PARAMETRO_CAB

DELETE FROM db_general.admi_parametro_cab cab
WHERE
        cab.nombre_parametro = 'MENSAJES_ADMIN_NOTIF_PUSH'
    AND cab.modulo = 'ADMINISTRACION';