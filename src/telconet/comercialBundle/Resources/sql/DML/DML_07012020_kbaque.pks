
/**
 *
 * Se realiza la creación de parámetros para el proyecto FLUJO AUTORIZACION DE DESCUENTO
 *
 * @author Kevin Baque Puya <kbaque@telconet.ec>
 * @version 1.0 16-06-2021
 */
 --Registramos los rangos de visualización de solicitudes
--Cabecera
INSERT INTO db_general.admi_parametro_cab (
    id_parametro,
    nombre_parametro,
    descripcion,
    modulo,
    proceso,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion
) VALUES (
    db_general.seq_admi_parametro_cab.nextval,
    'RANGO_APROBACION_SOLICITUDES',
    'PARAMETROS UTILIZADOS PARA EL PROYECTO FLUJO DE AUTORIZACION DE DESCUENTOS',
    'COMERCIAL',
    'ADMINISTRACION_CARGOS_SOLICITUDES',
    'Activo',
    'kbaque',
    SYSDATE,
    '127.0.0.1'
);
--detalle
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    valor4,
    valor5,
    valor6,
    valor7,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'RANGO_APROBACION_SOLICITUDES'
    ),
    'PARAMETRO PARA DEFINIR LIMITE DE PORCENTAJE DE DESCUENTO PARA VISUALIZAR SOLICITUDES',
    '0',
    '10',
    'SUBGERENTE',
    'ES_JEFE',
    'Pendiente-Gerente',
    'Pendiente',
    'SI',
    'Activo',
    'kbaque',
    SYSDATE,
    '127.0.0.1',
    10
);
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    valor4,
    valor5,
    valor6,
    valor7,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'RANGO_APROBACION_SOLICITUDES'
    ),
    'PARAMETRO PARA DEFINIR LIMITE DE PORCENTAJE DE DESCUENTO PARA VISUALIZAR SOLICITUDES',
    '0',
    '30',
    'GERENTE_VENTAS',
    'ES_JEFE',
    'Pendiente-Gerente-General',
    'Pendiente-Gerente',
    'SI',
    'Activo',
    'kbaque',
    SYSDATE,
    '127.0.0.1',
    10
);
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    valor4,
    valor5,
    valor6,
    valor7,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'RANGO_APROBACION_SOLICITUDES'
    ),
    'PARAMETRO PARA DEFINIR LIMITE DE PORCENTAJE DE DESCUENTO PARA VISUALIZAR SOLICITUDES',
    '0',
    '100',
    'GERENTE_GENERAL_REGIONAL',
    'ES_JEFE',
    'Aprobado',
    'Pendiente-Gerente-General',
    'NO',
    'Activo',
    'kbaque',
    SYSDATE,
    '127.0.0.1',
    10
);
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    valor4,
    valor5,
    valor6,
    valor7,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'RANGO_APROBACION_SOLICITUDES'
    ),
    'PARAMETRO PARA DEFINIR LIMITE DE PORCENTAJE DE DESCUENTO PARA VISUALIZAR SOLICITUDES',
    '0',
    '100',
    'GERENTE_GENERAL',
    'ES_JEFE',
    'Aprobado',
    'Pendiente-Gerente-General',
    'SI',
    'Activo',
    'kbaque',
    SYSDATE,
    '127.0.0.1',
    10
);
--Agregamos nuevos cargos 
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    valor4,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'GRUPO_ROLES_PERSONAL'
    ),
    'GERENTE_GENERAL_REGIONAL',
    'NO',
    '1',
    'GERENTE_GENERAL_REGIONAL',
    'ES_JEFE',
    'Activo',
    'kbaque',
    SYSDATE,
    '127.0.0.1',
    10
);
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    valor4,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'GRUPO_ROLES_PERSONAL'
    ),
    'GERENTE_GENERAL',
    'NO',
    '1',
    'GERENTE_GENERAL',
    'ES_JEFE',
    'Activo',
    'kbaque',
    SYSDATE,
    '127.0.0.1',
    10
);
--Agregamos los cargos al ing. Igor Krochin, ing. Topic
INSERT INTO db_comercial.info_persona_empresa_rol_carac (
    id_persona_empresa_rol_caract,
    persona_empresa_rol_id,
    caracteristica_id,
    valor,
    fe_creacion,
    usr_creacion,
    ip_creacion,
    estado
) VALUES (
    db_comercial.seq_info_persona_emp_rol_carac.nextval,
    (
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
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'CARGO_GRUPO_ROLES_PERSONAL'
            AND estado = 'Activo'
    ),
    (
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
    ),
    sysdate,
    'kbaque',
    '127.0.0.1',
    'Activo'
);
INSERT INTO db_comercial.info_persona_empresa_rol_carac (
    id_persona_empresa_rol_caract,
    persona_empresa_rol_id,
    caracteristica_id,
    valor,
    fe_creacion,
    usr_creacion,
    ip_creacion,
    estado
) VALUES (
    db_comercial.seq_info_persona_emp_rol_carac.nextval,
    (
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
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'CARGO_GRUPO_ROLES_PERSONAL'
            AND estado = 'Activo'
    ),
    (
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
    ),
    sysdate,
    'kbaque',
    '127.0.0.1',
    'Activo'
);
COMMIT;
/