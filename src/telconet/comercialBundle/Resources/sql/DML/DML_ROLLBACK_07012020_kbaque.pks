/**
 *
 * Se realiza el script de reverso de los parametros para poder listar 
 * los tipos de autorizaciones que manejará la ventana de Autorización.
 *
 * @author Kevin Baque Puya <kbaque@telconet.ec>
 * @version 1.0 16-06-2021
 */

--Eliminamos el detalle de los parametros
DELETE FROM db_general.admi_parametro_det
WHERE
    parametro_id = (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'RANGO_APROBACION_SOLICITUDES'
    );
--Eliminamos la cabecera.
DELETE FROM db_general.admi_parametro_cab
WHERE
    nombre_parametro = 'RANGO_APROBACION_SOLICITUDES';
--Eliminamos las caracteristicas de los nuevos cargos
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
            ip.login = 'ttopic'
            AND ip.estado = 'Activo'
            AND ier.estado IN (
                'Activo',
                'Modificado'
            )
            AND ier.empresa_cod = 10
            AND arrol.descripcion_rol = 'Gerente General'
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
            AND apd.valor3 = 'GERENTE_GENERAL'
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
            ip.login = 'ikrochin'
            AND ip.estado = 'Activo'
            AND ier.estado IN (
                'Activo',
                'Modificado'
            )
            AND ier.empresa_cod = 10
            AND arrol.descripcion_rol = 'Gerente De Sucursal'
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
            AND apd.valor3 = 'GERENTE_GENERAL_REGIONAL'
    )
    AND estado='Activo'
);
--Eliminamos el detalle de los nuevos cargos
DELETE FROM db_general.admi_parametro_det
WHERE
    valor3 in ('GERENTE_GENERAL_REGIONAL','GERENTE_GENERAL')
    AND parametro_id = (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'GRUPO_ROLES_PERSONAL'
    );

COMMIT;
/