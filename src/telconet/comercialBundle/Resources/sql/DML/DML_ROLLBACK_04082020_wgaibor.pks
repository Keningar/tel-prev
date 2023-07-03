--app TmComercial
DELETE FROM db_general.admi_parametro_det
WHERE
    parametro_id = (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'PARAMETROS_ESTRUCTURA_RUTAS_TELCOS'
    )
    AND valor1 = 'ec.telconet.telcos.mobile.comercial';
--
--NFS1
--
DELETE FROM db_general.admi_parametro_det
WHERE
    parametro_id = (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'NFS_PATH_RAIZ'
    );
--
-- NFS_PATH_RAIZ
--
DELETE FROM db_general.admi_parametro_cab
WHERE
        nombre_parametro = 'NFS_PATH_RAIZ';
--
--UMBRAL PARA NOTIFICAR LA DISPONIBILIDAD DE SEGUIR CREANDO ARCHIVOS EN EL NFS
--
DELETE FROM db_general.admi_parametro_det
WHERE
    parametro_id = (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'NFS_UMBRAL_NOTIFICACION'
    );
--
-- CREACIÓN DEL PARÁMETRO CAB  - NFS_UMBRAL_NOTIFICACIÓN
--
DELETE FROM db_general.admi_parametro_cab
WHERE
        nombre_parametro = 'NFS_UMBRAL_NOTIFICACION';
--
--PLANTILLA DE NOTIFICACIÓN PARA EL ENVIO DE CORREO
--
DELETE FROM db_general.admi_parametro_det
WHERE
    parametro_id = (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'NFS_PLANTILLA_NOTIFICACION'
    );
--
-- CREACIÓN DEL PARÁMETRO CAB  - NFS_PLANTILLA_NOTIFICACIÓN
--
DELETE FROM db_general.admi_parametro_cab
WHERE
        nombre_parametro = 'NFS_PLANTILLA_NOTIFICACION';
--
-- INGRESAR LA RUTA DE DIRECTORIO DE UNA APP
--
DELETE FROM db_general.ADMI_GESTION_DIRECTORIOS
WHERE
        APLICACION = 'TmComercial';

COMMIT;
/