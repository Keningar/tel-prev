--SE ELIMINA REGISTRO DE DIRECTORIO CREADO PARA NOTIFICACIONES

DELETE FROM db_general.admi_gestion_directorios
    WHERE aplicacion = 'TelcosWeb'
    AND modulo = 'Administracion'
    AND submodulo = 'Notificaciones'
    AND usr_creacion = 'adorellana';

COMMIT;