
    /*
    * Rollback del DML (DML_03072020_kbaque.pks)
    * @author Kevin Baque <kbaque@telconet.ec>
    * @version 1.0 03-07-2020
    */

    --Eliminamos la tarea
    DELETE FROM DB_SOPORTE.ADMI_TAREA T
        WHERE T.NOMBRE_TAREA = 'CREACION DE CTA. CONTABLE PARA PROYECTO'
        AND T.USR_CREACION = 'kbaque';

    --Eliminamos el proceso
    DELETE FROM DB_SOPORTE.ADMI_PROCESO T
        WHERE T.NOMBRE_PROCESO = 'TAREAS CONTABILIDAD - GESTIÓN PROYECTO'
        AND T.USR_CREACION = 'kbaque';

    --Eliminamos la solicitud.
    DELETE FROM DB_COMERCIAL.ADMI_TIPO_SOLICITUD T
        WHERE T.DESCRIPCION_SOLICITUD = 'SOLICITUD DE PROYECTO'
        AND T.USR_CREACION = 'kbaque';

    --Eliminamos el motivo.
    DELETE FROM DB_GENERAL.ADMI_MOTIVO T
        WHERE T.NOMBRE_MOTIVO = 'Proyecto no asignado por G.T.N.'
        AND T.USR_CREACION = 'kbaque';

    DELETE FROM DB_GENERAL.ADMI_MOTIVO T
        WHERE T.NOMBRE_MOTIVO = 'Proyecto requiere PMO'
        AND T.USR_CREACION = 'kbaque';

    DELETE FROM DB_GENERAL.ADMI_MOTIVO T
        WHERE T.NOMBRE_MOTIVO = 'Proyecto requiere PYL'
        AND T.USR_CREACION = 'kbaque';

    DELETE FROM DB_GENERAL.ADMI_MOTIVO T
        WHERE T.NOMBRE_MOTIVO = 'No aplica proyecto'
        AND T.USR_CREACION = 'kbaque';

    DELETE FROM DB_GENERAL.ADMI_MOTIVO T
        WHERE T.NOMBRE_MOTIVO = 'Información incorrecta'
        AND T.USR_CREACION = 'kbaque';

    --Eliminamos el detalle de los parametros.
    DELETE FROM db_general.admi_parametro_det
    WHERE
        parametro_id = (
            SELECT
                id_parametro
            FROM
                db_general.admi_parametro_cab
            WHERE
                nombre_parametro = 'PARAMETROS_TELCOCRM'
        );

    --Eliminamos la cabecera.
    DELETE FROM db_general.admi_parametro_cab
    WHERE
        nombre_parametro = 'PARAMETROS_TELCOCRM'
        AND usr_creacion = 'kbaque';

    DELETE FROM db_comercial.info_persona_empresa_rol_carac 
    WHERE
    (
        persona_empresa_rol_id = (
            SELECT
                iper.id_persona_rol
            FROM
                db_comercial.info_persona               ip
                JOIN db_comercial.info_persona_empresa_rol   iper ON iper.persona_id = ip.id_persona
                JOIN db_comercial.info_empresa_rol           ier ON ier.id_empresa_rol = iper.empresa_rol_id
                JOIN db_comercial.admi_rol                   arrol ON arrol.id_rol = ier.rol_id
            WHERE
                ip.login = 'asegarra'
                AND ip.estado = 'Activo'
                AND ier.estado IN (
                    'Activo',
                    'Modificado'
                )
                AND ier.empresa_cod = 10
                AND arrol.descripcion_rol = 'Jefe Departamental'
                AND iper.estado = 'Activo'
        )
        AND caracteristica_id = (
            SELECT
                id_caracteristica
            FROM
                db_comercial.admi_caracteristica
            WHERE
                descripcion_caracteristica = 'CARGO_GRUPO_ROLES_PERSONAL'
                AND estado = 'Activo'
        )
        AND valor = (
            SELECT
                apd.id_parametro_det
            FROM
                db_general.admi_parametro_cab   apc
                JOIN db_general.admi_parametro_det   apd ON apd.parametro_id = apc.id_parametro
            WHERE
                apc.nombre_parametro = 'GRUPO_ROLES_PERSONAL'
                AND apc.modulo = 'COMERCIAL'
                AND apc.estado = 'Activo'
                AND apd.estado = 'Activo'
                AND apd.valor4 = 'ES_JEFE'
                AND apd.valor3 = 'JEFE_DEPARTAMENTAL_PMO'
        )
        AND estado='Activo'
    );

    DELETE FROM db_comercial.info_persona_empresa_rol_carac 
    WHERE
    (
        persona_empresa_rol_id = (
            SELECT
                iper.id_persona_rol
            FROM
                db_comercial.info_persona               ip
                JOIN db_comercial.info_persona_empresa_rol   iper ON iper.persona_id = ip.id_persona
                JOIN db_comercial.info_empresa_rol           ier ON ier.id_empresa_rol = iper.empresa_rol_id
                JOIN db_comercial.admi_rol                   arrol ON arrol.id_rol = ier.rol_id
            WHERE
                ip.login = 'vcastro'
                AND ip.estado = 'Activo'
                AND ier.estado IN (
                    'Activo',
                    'Modificado'
                )
                AND ier.empresa_cod = 10
                AND arrol.descripcion_rol = 'Jefe Departamental'
                AND iper.estado = 'Activo'
        )
        AND caracteristica_id = (
            SELECT
                id_caracteristica
            FROM
                db_comercial.admi_caracteristica
            WHERE
                descripcion_caracteristica = 'CARGO_GRUPO_ROLES_PERSONAL'
                AND estado = 'Activo'
        )
        AND valor = (
            SELECT
                apd.id_parametro_det
            FROM
                db_general.admi_parametro_cab   apc
                JOIN db_general.admi_parametro_det   apd ON apd.parametro_id = apc.id_parametro
            WHERE
                apc.nombre_parametro = 'GRUPO_ROLES_PERSONAL'
                AND apc.modulo = 'COMERCIAL'
                AND apc.estado = 'Activo'
                AND apd.estado = 'Activo'
                AND apd.valor4 = 'ES_JEFE'
                AND apd.valor3 = 'JEFE_DEPARTAMENTAL_PYL'
        )
        AND estado='Activo'
    );

    DELETE FROM db_general.admi_parametro_det
    WHERE
        parametro_id = (
            SELECT
                id_parametro
            FROM
                db_general.admi_parametro_cab
            WHERE
                nombre_parametro = 'GRUPO_ROLES_PERSONAL'
        )
        and descripcion in('JEFE_DEPARTAMENTAL_PYL','JEFE_DEPARTAMENTAL_PMO');
    COMMIT;
    /