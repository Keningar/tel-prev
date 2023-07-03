-- INGRESO DE LA CABECERA DE PARAMETROS DE 'DETALLES_PRODUCTO_DIRECT_LINK_MPLS'
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
(
        ID_PARAMETRO,
        NOMBRE_PARAMETRO,
        DESCRIPCION,
        MODULO,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
        'CONFIG_PRODUCTO_DIRECT_LINK_MPLS',
        'Lista de la configuración del producto DirectLink-MPLS',
        'TECNICO',
        'Activo',
        'afayala',
        SYSDATE,
        '127.0.0.1'
);
-- INGRESO LOS DETALLES DE LA CABECERA 'CONFIG_PRODUCTO_DIRECT_LINK_MPLS'
-- DETALLE NO REQUIERE ENLACE
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
        VALOR3,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        EMPRESA_COD
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'CONFIG_PRODUCTO_DIRECT_LINK_MPLS'
            AND ESTADO = 'Activo'
        ),
        'Lista de la configuración del producto',
        (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO='DIRECTLINK MPLS' AND ESTADO='Activo'),
        'ENLACE_DATOS',
        'NO',
        'Activo',
        'afayala',
        SYSDATE,
        '127.0.0.1',
        (
            SELECT COD_EMPRESA
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
            WHERE PREFIJO = 'TN'
        )
);
-- DETALLE DEL ID DEL PRODUCTO RELACIONADO
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
        VALOR3,
        VALOR4,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        EMPRESA_COD
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'CONFIG_PRODUCTO_DIRECT_LINK_MPLS'
            AND ESTADO = 'Activo'
        ),
        'Lista de la configuración del producto',
        (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO='DIRECTLINK MPLS' AND ESTADO='Activo'),
        'AGREGAR_SERVICIO_RELACION',
        (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO='FastCloud' AND ESTADO='Activo'),
        'FastCloud',
        'Activo',
        'afayala',
        SYSDATE,
        '127.0.0.1',
        (
            SELECT COD_EMPRESA
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
            WHERE PREFIJO = 'TN'
        )
);
-- DETALLE DEL VISIBLE_PRODUCTO_AGREGAR_SERVICIO FAST CLOUD
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
        VALOR3,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        EMPRESA_COD
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'CONFIG_PRODUCTO_DIRECT_LINK_MPLS'
            AND ESTADO = 'Activo'
        ),
        'Lista de la configuración del producto',
        (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO='FastCloud' AND ESTADO='Activo'),
        'VISIBLE_PRODUCTO_AGREGAR_SERVICIO',
        'NO',
        'Activo',
        'afayala',
        SYSDATE,
        '127.0.0.1',
        (
            SELECT COD_EMPRESA
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
            WHERE PREFIJO = 'TN'
        )
);
-- DETALLE DEL ID DEL PRODUCTO SI SE PUEDE ELIMINAR
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
        VALOR3,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        EMPRESA_COD
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'CONFIG_PRODUCTO_DIRECT_LINK_MPLS'
            AND ESTADO = 'Activo'
        ),
        'Lista de la configuración del producto',
        (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO='FastCloud' AND ESTADO='Activo'),
        'OPCION_PRODUCTO_ELIMINAR',
        'NO',
        'Activo',
        'afayala',
        SYSDATE,
        '127.0.0.1',
        (
            SELECT COD_EMPRESA
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
            WHERE PREFIJO = 'TN'
        )
);

-- DETALLE DEL ID DEL PRODUCTO SI SE PUEDE ACTIVAR BOTON POR DEPARTAMENTO
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
        VALOR3,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        EMPRESA_COD
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'CONFIG_PRODUCTO_DIRECT_LINK_MPLS'
            AND ESTADO = 'Activo'
        ),
        'Lista de la configuración del producto',
        (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO='FastCloud' AND ESTADO='Activo'),
        'OPCION_ACTIVAR_POR_DEPARTAMENTOS',
        'OTN',
        'Activo',
        'afayala',
        SYSDATE,
        '127.0.0.1',
        (
            SELECT COD_EMPRESA
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
            WHERE PREFIJO = 'TN'
        )
);

/*
 * ➜ Ingresa una cabecera CARACTERISTICAS_SERVICIOS_CONFIRMACION.
 *
 * ➜ Ingresa el detalle sobre el nuevo parámetro.
 *
 * @author Antonio Ayala <afayala@telconet.ec>
 * @version 1.0 22-09-2020 - Versión Inicial.
 */
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.nextval, 'CARACTERISTICAS_SERVICIO_CONFIRMACION',
        'CARACTERISTICAS DE SERVICIOS PARA CONFIRMACION',
        'TECNICO', 'SERVICIO_CONFIRMACION', 'Activo', 'afayala', SYSDATE,
        '127.0.0.1', null, null, null);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval, (SELECT ID_PARAMETRO
                                                    FROM DB_GENERAL.ADMI_PARAMETRO_CAB
                                                    WHERE NOMBRE_PARAMETRO = 'CARACTERISTICAS_SERVICIO_CONFIRMACION'),
        'CARACTERISTICAS_SERVICIO_CONFIRMACION',
        '[{"PRODUCTO_ID":1295,"DESCRIPCION_PRODUCTO":"FastCloud","OS_AGRUPADAS":true,"TIENE_FLUJO":false,"VALIDA_NAF":true,"HELPER":"Confirmación Servicio FastCloud","REQUIERE_REGISTRO":true,"CARACTERISTICAS_ADICIONALES":[{"ID_PRODUCTO_CARACTERISITICA":12472,"DESCRIPCION_CARACTERISTICA":"NUBE PUBLICA","LABEL":"NUBE PUBLICA","XTYPE":"textfield"},{"ID_PRODUCTO_CARACTERISITICA":12473,"DESCRIPCION_CARACTERISTICA":"IP EQUINIX","LABEL":"IP EQUINIX","XTYPE":"textfield"},{"ID_PRODUCTO_CARACTERISITICA":12474,"DESCRIPCION_CARACTERISTICA":"IP ROUTER NAP","LABEL":"IP ROUTER NAP","XTYPE":"textfield"},{"ID_PRODUCTO_CARACTERISITICA":12475,"DESCRIPCION_CARACTERISTICA":"REDES LAN","LABEL":"REDES LAN","XTYPE":"textarea"}]}]',
        null, null, null, 'Activo', 'afayala',
        SYSDATE, '127.0.0.1', null, null, null, null, '10', null, null,
        null);

-- INGRESO DE LA CABECERA DE PARAMETROS DE 'CREAR_TAREA_INTERNA_SERVICIOS'
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
(
        ID_PARAMETRO,
        NOMBRE_PARAMETRO,
        DESCRIPCION,
        MODULO,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
        'CREAR_TAREA_INTERNA_SERVICIOS',
        'Lista de los responsables donde se va asignar la tarea por producto',
        'TECNICO',
        'Activo',
        'afayala',
        SYSDATE,
        '127.0.0.1'
);

-- INGRESO LOS DETALLES DE LA CABECERA 'CREAR_TAREA_INTERNA_SERVICIOS'
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
        VALOR3,
	VALOR4,
        VALOR5,
	VALOR6,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        EMPRESA_COD
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'CREAR_TAREA_INTERNA_SERVICIOS'
            AND ESTADO = 'Activo'
        ),
        'Usuario que se va a asignar la tarea',
        (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO='DIRECTLINK MPLS' AND ESTADO='Activo'),
        'TAREA ELECTRICO - CABLEADO ESTRUCTURADO',
        'Se activó el servicio DIRECTLINK MPLS. Se debe activar el servicio de FastCloud',
	'SISTEMAS',
	'afayala',
	'2868',
        'Activo',
        'afayala',
        SYSDATE,
        '127.0.0.1',
        (
            SELECT COD_EMPRESA
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
            WHERE PREFIJO = 'TN'
        )
);

-- INGRESO DE LA CABECERA DE PARAMETROS DE 'NO_VISUALIZAR_BOTON_DE_CORTE'
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
(
        ID_PARAMETRO,
        NOMBRE_PARAMETRO,
        DESCRIPCION,
        MODULO,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
        'NO_VISUALIZAR_BOTON_DE_CORTE',
        'Lista de los servicios que no deben visualizar el boton de corte de servicio',
        'TECNICO',
        'Activo',
        'afayala',
        SYSDATE,
        '127.0.0.1'
);

-- INGRESO LOS DETALLES DE LA CABECERA 'NO_VISUALIZAR_BOTON_DE_CORTE'
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        EMPRESA_COD
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'NO_VISUALIZAR_BOTON_DE_CORTE'
            AND ESTADO = 'Activo'
        ),
        'Servicios que no deben tener activo el boton de corte',
        'FastCloud,Cableado Estructurado',
        'Activo',
        'afayala',
        SYSDATE,
        '127.0.0.1',
        (
            SELECT COD_EMPRESA
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
            WHERE PREFIJO = 'TN'
        )
);

-- INGRESO DE LA CABECERA DE PARAMETROS DE 'NO_VISUALIZAR_BOTON_DE_CANCELAR'
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
(
        ID_PARAMETRO,
        NOMBRE_PARAMETRO,
        DESCRIPCION,
        MODULO,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
        'NO_VISUALIZAR_BOTON_DE_CANCELAR',
        'Lista de los servicios que no deben visualizar el boton de cancelar servicio',
        'TECNICO',
        'Activo',
        'afayala',
        SYSDATE,
        '127.0.0.1'
);

-- INGRESO LOS DETALLES DE LA CABECERA 'NO_VISUALIZAR_BOTON_DE_CANCELAR'
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        EMPRESA_COD
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'NO_VISUALIZAR_BOTON_DE_CANCELAR'
            AND ESTADO = 'Activo'
        ),
        'Servicios que no deben tener activo el boton de cancelacion',
        'FastCloud',
        'Activo',
        'afayala',
        SYSDATE,
        '127.0.0.1',
        (
            SELECT COD_EMPRESA
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
            WHERE PREFIJO = 'TN'
        )
);

-- INGRESO DE LA CABECERA DE PARAMETROS DE 'NO_VISUALIZAR_BOTON_DE_REACTIVACION'
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
(
        ID_PARAMETRO,
        NOMBRE_PARAMETRO,
        DESCRIPCION,
        MODULO,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
        'NO_VISUALIZAR_BOTON_DE_REACTIVACION',
        'Lista de los servicios que no deben visualizar el boton de corte de reactivación de servicio',
        'TECNICO',
        'Activo',
        'afayala',
        SYSDATE,
        '127.0.0.1'
);

-- INGRESO LOS DETALLES DE LA CABECERA 'NO_VISUALIZAR_BOTON_DE_REACTIVACION'
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        EMPRESA_COD
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'NO_VISUALIZAR_BOTON_DE_REACTIVACION'
            AND ESTADO = 'Activo'
        ),
        'Servicios que no deben tener activo el boton de reactivación',
        'FastCloud',
        'Activo',
        'afayala',
        SYSDATE,
        '127.0.0.1',
        (
            SELECT COD_EMPRESA
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
            WHERE PREFIJO = 'TN'
        )
);

COMMIT;
/
