/*************************************************************************************************/
/*************************************************************************************************/
/*****************************************ESQUEMA DB_COMERCIAL************************************/
/*************************************************************************************************/
/*************************************************************************************************/

/**
 * Ejecución de scripts DML para crear productos que se ligan a las solicitudes.
 * @author Luis Cabrera <lcabrera@telconet.ec>
 * @since 13-08-2018
 * @version 1.0
 */
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO (
    id_producto,
    empresa_cod,
    codigo_producto,
    descripcion_producto,
    funcion_costo,
    instalacion,
    estado,
    fe_creacion,
    usr_creacion,
    ip_creacion,
    cta_contable_prod,
    cta_contable_prod_nc,
    es_preferencia,
    es_enlace,
    requiere_planificacion,
    requiere_info_tecnica,
    nombre_tecnico,
    cta_contable_desc,
    tipo,
    es_concentrador,
    soporte_masivo,
    estado_inicial,
    grupo,
    comision_venta,
    comision_mantenimiento,
    usr_gerente,
    clasificacion,
    requiere_comisionar,
    subgrupo,
    linea_negocio
) VALUES (
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO.NEXTVAL,
    '18',
    'SOLVT',
    'VISITA TECNICA',
    NULL,
    0,
    'Inactivo',
    SYSDATE,
    'lcabrera',
    '127.0.0.1',
    NULL,
    NULL,
    'NO',
    'NO',
    'NO',
    'NO',
    'OTROS',
    NULL,
    'S',
    'NO',
    NULL,
    'Pendiente',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    'NO',
    NULL,
    NULL
);


INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO (
    id_producto,
    empresa_cod,
    codigo_producto,
    descripcion_producto,
    funcion_costo,
    instalacion,
    estado,
    fe_creacion,
    usr_creacion,
    ip_creacion,
    cta_contable_prod,
    cta_contable_prod_nc,
    es_preferencia,
    es_enlace,
    requiere_planificacion,
    requiere_info_tecnica,
    nombre_tecnico,
    cta_contable_desc,
    tipo,
    es_concentrador,
    soporte_masivo,
    estado_inicial,
    grupo,
    comision_venta,
    comision_mantenimiento,
    usr_gerente,
    clasificacion,
    requiere_comisionar,
    subgrupo,
    linea_negocio
) VALUES (
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO.NEXTVAL,
    '10',
    'SOLVT',
    'VISITA TECNICA',
    NULL,
    0,
    'Inactivo',
    SYSDATE,
    'lcabrera',
    '127.0.0.1',
    NULL,
    NULL,
    'NO',
    'NO',
    'NO',
    'NO',
    'OTROS',
    NULL,
    'S',
    'NO',
    NULL,
    'Pendiente',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    'NO',
    NULL,
    NULL
);

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO (
    id_producto,
    empresa_cod,
    codigo_producto,
    descripcion_producto,
    funcion_costo,
    instalacion,
    estado,
    fe_creacion,
    usr_creacion,
    ip_creacion,
    cta_contable_prod,
    cta_contable_prod_nc,
    es_preferencia,
    es_enlace,
    requiere_planificacion,
    requiere_info_tecnica,
    nombre_tecnico,
    cta_contable_desc,
    tipo,
    es_concentrador,
    soporte_masivo,
    estado_inicial,
    grupo,
    comision_venta,
    comision_mantenimiento,
    usr_gerente,
    clasificacion,
    requiere_comisionar,
    subgrupo,
    linea_negocio
) VALUES (
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO.NEXTVAL,
    '18',
    'SOLCMI',
    'RETIRO DE EQUIPOS',
    NULL,
    0,
    'Inactivo',
    SYSDATE,
    'lcabrera',
    '127.0.0.1',
    NULL,
    NULL,
    'NO',
    'NO',
    'NO',
    'NO',
    'OTROS',
    NULL,
    'S',
    'NO',
    NULL,
    'Pendiente',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    'NO',
    NULL,
    NULL
);

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO (
    id_producto,
    empresa_cod,
    codigo_producto,
    descripcion_producto,
    funcion_costo,
    instalacion,
    estado,
    fe_creacion,
    usr_creacion,
    ip_creacion,
    cta_contable_prod,
    cta_contable_prod_nc,
    es_preferencia,
    es_enlace,
    requiere_planificacion,
    requiere_info_tecnica,
    nombre_tecnico,
    cta_contable_desc,
    tipo,
    es_concentrador,
    soporte_masivo,
    estado_inicial,
    grupo,
    comision_venta,
    comision_mantenimiento,
    usr_gerente,
    clasificacion,
    requiere_comisionar,
    subgrupo,
    linea_negocio
) VALUES (
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO.NEXTVAL,
    '10',
    'SOLCMI',
    'RETIRO DE EQUIPOS',
    NULL,
    0,
    'Inactivo',
    SYSDATE,
    'lcabrera',
    '127.0.0.1',
    NULL,
    NULL,
    'NO',
    'NO',
    'NO',
    'NO',
    'OTROS',
    NULL,
    'S',
    'NO',
    NULL,
    'Pendiente',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    'NO',
    NULL,
    NULL
);

COMMIT;


/**
 * Se insertan las nuevas solicitudes.
 * @author Luis Cabrera <lcabrera@telconet.ec>
 * @version 1.0
 * @since 28-08-2018
 */
INSERT INTO DB_COMERCIAL.ADMI_TIPO_SOLICITUD (
    ID_TIPO_SOLICITUD,
    DESCRIPCION_SOLICITUD,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    ESTADO,
    TAREA_ID,
    ITEM_MENU_ID,
    PROCESO_ID
) VALUES (
    DB_COMERCIAL.SEQ_ADMI_TIPO_SOLICITUD.NEXTVAL,
    'SOLICITUD FACTURACION RETIRO EQUIPO',
    SYSDATE,
    'lcabrera',
    NULL,
    NULL,
    'Activo',
    NULL,
    NULL,
    NULL
);

INSERT INTO DB_COMERCIAL.ADMI_TIPO_SOLICITUD (
    ID_TIPO_SOLICITUD,
    DESCRIPCION_SOLICITUD,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    ESTADO,
    TAREA_ID,
    ITEM_MENU_ID,
    PROCESO_ID
) VALUES (
    DB_COMERCIAL.SEQ_ADMI_TIPO_SOLICITUD.NEXTVAL,
    'SOLICITUD VISITA TECNICA',
    SYSDATE,
    'lcabrera',
    NULL,
    NULL,
    'Activo',
    NULL,
    NULL,
    NULL
);

INSERT INTO DB_COMERCIAL.ADMI_TIPO_SOLICITUD (
    ID_TIPO_SOLICITUD,
    DESCRIPCION_SOLICITUD,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    ESTADO,
    TAREA_ID,
    ITEM_MENU_ID,
    PROCESO_ID
) VALUES (
    DB_COMERCIAL.SEQ_ADMI_TIPO_SOLICITUD.NEXTVAL,
    'SOLICITUD FACTURACION CONTRATO WEB',
    SYSDATE,
    'lcabrera',
    NULL,
    NULL,
    'Activo',
    NULL,
    NULL,
    NULL
);

COMMIT;

/**
 * @author Luis Cabrera <lcabrera@telconet.ec>
 * @version 1.0
 * @since 30-08-2018
 * Característica para diferenciar el contrato FISICO del DIGITAL.
 */
    INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
        ID_CARACTERISTICA,
        DESCRIPCION_CARACTERISTICA,
        TIPO_INGRESO,
        ESTADO,
        FE_CREACION,
        USR_CREACION,
        FE_ULT_MOD,
        USR_ULT_MOD,
        TIPO
    ) VALUES (
        DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
        'POR_CONTRATO_FISICO',
        'N',
        'Activo',
        SYSDATE,
        'lcabrera',
        NULL,
        NULL,
        'COMERCIAL'
    );

COMMIT;

/**
 * @author Luis Cabrera <lcabrera@telconet.ec>
 * @version 1.0
 * @since 30-08-2018
 * Características necesarias para la facturación de equipos.
 */
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    TIPO
) VALUES (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'CPE ONT TELLION',
    'N',
    'Activo',
    SYSDATE,
    'lcabrera',
    NULL,
    NULL,
    'COMERCIAL'
);

INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    TIPO
) VALUES (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'CPE WIFI TELLION',
    'N',
    'Activo',
    SYSDATE,
    'lcabrera',
    NULL,
    NULL,
    'COMERCIAL'
);

INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    TIPO
) VALUES (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'FUENTE DE PODER',
    'N',
    'Activo',
    SYSDATE,
    'lcabrera',
    NULL,
    NULL,
    'COMERCIAL'
);

INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    TIPO
) VALUES (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'CPE ONT HUAWEI',
    'N',
    'Activo',
    SYSDATE,
    'lcabrera',
    NULL,
    NULL,
    'COMERCIAL'
);

INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    TIPO
) VALUES (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'ROSETA',
    'N',
    'Activo',
    SYSDATE,
    'lcabrera',
    NULL,
    NULL,
    'COMERCIAL'
);

INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    TIPO
) VALUES (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'EQUIPO ADSL',
    'N',
    'Activo',
    SYSDATE,
    'lcabrera',
    NULL,
    NULL,
    'COMERCIAL'
);

INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    TIPO
) VALUES (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'FUENTE DE PODER AP CISCO',
    'N',
    'Activo',
    SYSDATE,
    'lcabrera',
    NULL,
    NULL,
    'COMERCIAL'
);

INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    TIPO
) VALUES (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'EQUIPO AP CISCO',
    'N',
    'Activo',
    SYSDATE,
    'lcabrera',
    NULL,
    NULL,
    'COMERCIAL'
);

INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    TIPO
) VALUES (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'SOLICITUD CAMBIO DE MODEM INMEDIATO',
    'N',
    'Activo',
    SYSDATE,
    'lcabrera',
    NULL,
    NULL,
    'COMERCIAL'
);
COMMIT;

/*************************************************************************************************/
/*************************************************************************************************/
/*****************************************ESQUEMA DB_GENERAL**************************************/
/*************************************************************************************************/
/*************************************************************************************************/

/**
 * @author Luis Cabrera <lcabrera@telconet.ec>
 * @version 1.0
 * @since 08-08-2018
 * Se crean las sentencias DML para insertar parámetros (CAB y DET) relacionados con la facturación de solicitudes.
 */
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
    (
        ID_PARAMETRO,
        NOMBRE_PARAMETRO,
        DESCRIPCION,
        MODULO,
        PROCESO,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        USR_ULT_MOD,
        FE_ULT_MOD,
        IP_ULT_MOD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
        'FACTURACION_SOLICITUDES',
        'PARÁMETROS PARA LA FACTURACIÓN DE SOLICITUDES. V1=NOMBRE_SOLICITUD, V2=PLAN_ID, V3=PRODUCTO_ID, V4=Obs Factura, V5= usr_creacion, V6= Proceso Automático',
        'FINANCIERO',
        'FACTURACION_SOLICITUDES',
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL
    );


--CONTRATO DIGITAL
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
        USR_ULT_MOD,
        FE_ULT_MOD,
        IP_ULT_MOD,
        VALOR5,
        EMPRESA_COD,
        VALOR6
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'FACTURACION_SOLICITUDES' 
           AND MODULO = 'FINANCIERO'
           AND PROCESO = 'FACTURACION_SOLICITUDES'
           AND ESTADO = 'Activo'
        ),
        'Contrato Digital',
        'SOLICITUD INSTALACION GRATIS',
        (SELECT IPC.ID_PLAN
           FROM DB_COMERCIAL.INFO_PLAN_CAB IPC
          WHERE IPC.NOMBRE_PLAN   = 'INSTALACION HOME'
            AND IPC.EMPRESA_COD = '18'
            AND IPC.ESTADO      = 'Activo'), --PLAN_ID
        NULL, --PRODUCTO_ID
        'Facturación por Instalación de Servicio', --OBSERVACION
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        'telcos_contrato',
        '18',
        'N'
    );

    --CONTRATO WEB
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
            USR_ULT_MOD,
            FE_ULT_MOD,
            IP_ULT_MOD,
            VALOR5,
            EMPRESA_COD,
            VALOR6
        )
        VALUES
        (
            DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
            (
            SELECT ID_PARAMETRO
              FROM DB_GENERAL.ADMI_PARAMETRO_CAB
             WHERE NOMBRE_PARAMETRO = 'FACTURACION_SOLICITUDES' 
               AND MODULO = 'FINANCIERO'
               AND PROCESO = 'FACTURACION_SOLICITUDES'
               AND ESTADO = 'Activo'
            ),
            'Contrato Web',
            'SOLICITUD FACTURACION CONTRATO WEB',
            (SELECT IPC.ID_PLAN
               FROM DB_COMERCIAL.INFO_PLAN_CAB IPC
              WHERE IPC.NOMBRE_PLAN   = 'INSTALACION HOME'
                AND IPC.EMPRESA_COD = '18'
                AND IPC.ESTADO      = 'Activo'), --PLAN_ID
            NULL, --PRODUCTO_ID
            'Facturación por Instalación de Servicio', --OBSERVACION
            'Activo',
            'lcabrera',
            SYSDATE,
            '127.0.0.1',
            NULL,
            NULL,
            NULL,
            'telcos_web',
            '18',
            'N' --Proceso automático.
        );

/*INICIO SOLICITUD REQUERIMIENTOS DE CLIENTES*/
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
        USR_ULT_MOD,
        FE_ULT_MOD,
        IP_ULT_MOD,
        VALOR5,
        EMPRESA_COD,
        VALOR6
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'FACTURACION_SOLICITUDES' 
           AND MODULO = 'FINANCIERO'
           AND PROCESO = 'FACTURACION_SOLICITUDES'
           AND ESTADO = 'Activo'
        ),
        'Solicitud Requerimientos de clientes',
        'SOLICITUD REQUERIMIENTOS DE CLIENTES',
        NULL, --PLAN_ID
        NULL, --PRODUCTO_ID
        'Facturación Requerimientos de Clientes', --OBSERVACION
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        'telcos_req_clientes',
        '18',
        'S'
    );

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
        USR_ULT_MOD,
        FE_ULT_MOD,
        IP_ULT_MOD,
        VALOR5,
        EMPRESA_COD,
        VALOR6
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'FACTURACION_SOLICITUDES' 
           AND MODULO = 'FINANCIERO'
           AND PROCESO = 'FACTURACION_SOLICITUDES'
           AND ESTADO = 'Activo'
        ),
        'Solicitud Requerimientos de clientes',
        'SOLICITUD REQUERIMIENTOS DE CLIENTES',
        NULL, --PLAN_ID
        NULL, --PRODUCTO_ID
        'Facturación Requerimientos de Clientes', --OBSERVACION
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        'telcos_req_clientes',
        '10',
        'S'
    );
/*FIN REQUERIMIENTOS DE CLIENTES*/


--SOLICITUD VISITA TECNICA - Visita Técnica MD
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
        USR_ULT_MOD,
        FE_ULT_MOD,
        IP_ULT_MOD,
        VALOR5,
        EMPRESA_COD,
        VALOR6
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'FACTURACION_SOLICITUDES' 
           AND MODULO = 'FINANCIERO'
           AND PROCESO = 'FACTURACION_SOLICITUDES'
           AND ESTADO = 'Activo'
        ),
        'Visita técnica',
        'SOLICITUD VISITA TECNICA',
        NULL, --PLAN_ID
        (SELECT
                ID_PRODUCTO
            FROM
                DB_COMERCIAL.ADMI_PRODUCTO
            WHERE
                EMPRESA_COD = '18'
                AND CODIGO_PRODUCTO = 'SOLVT'
                AND DESCRIPCION_PRODUCTO = 'VISITA TECNICA'
                AND NOMBRE_TECNICO = 'OTROS'), --PRODUCTO_ID
        'Visita técnica por caso # %CASO%', --OBSERVACION
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        'telcos_visitaTecnica',
        '18',
        'S'
    );


--SOLICITUD VISITA TECNICA - Visita Técnica TN
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
        USR_ULT_MOD,
        FE_ULT_MOD,
        IP_ULT_MOD,
        VALOR5,
        EMPRESA_COD,
        VALOR6
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'FACTURACION_SOLICITUDES' 
           AND MODULO = 'FINANCIERO'
           AND PROCESO = 'FACTURACION_SOLICITUDES'
           AND ESTADO = 'Activo'
        ),
        'Visita técnica',
        'SOLICITUD VISITA TECNICA',
        NULL, --PLAN_ID
        NULL, --PRODUCTO_ID
        'Facturación Requerimientos de Clientes', --OBSERVACION
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        'telcos_req_clientes', --USR_CREACION
        '10',
        'S'
    );

--SOLICITUD FACTURACION RETIRO EQUIPO --Cambio de módem inmediato MD
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
        USR_ULT_MOD,
        FE_ULT_MOD,
        IP_ULT_MOD,
        VALOR5,
        EMPRESA_COD,
        VALOR6
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'FACTURACION_SOLICITUDES' 
           AND MODULO = 'FINANCIERO'
           AND PROCESO = 'FACTURACION_SOLICITUDES'
           AND ESTADO = 'Activo'
        ),
        'Cambio de módem inmediato',
        'SOLICITUD FACTURACION RETIRO EQUIPO',
        NULL, --PLAN_ID
        (SELECT
                ID_PRODUCTO
            FROM
                DB_COMERCIAL.ADMI_PRODUCTO
            WHERE
                EMPRESA_COD = '18'
                AND CODIGO_PRODUCTO = 'SOLCMI'
                AND DESCRIPCION_PRODUCTO = 'RETIRO DE EQUIPOS'
                AND NOMBRE_TECNICO = 'OTROS'), --PRODUCTO_ID
        'Equipos', --OBSERVACION
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        'telcos_equipos',
        '18',
        'S'
    );

--SOLICITUD FACTURACION RETIRO EQUIPO --Cambio de módem inmediato TN
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
        USR_ULT_MOD,
        FE_ULT_MOD,
        IP_ULT_MOD,
        VALOR5,
        EMPRESA_COD,
        VALOR6
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'FACTURACION_SOLICITUDES' 
           AND MODULO = 'FINANCIERO'
           AND PROCESO = 'FACTURACION_SOLICITUDES'
           AND ESTADO = 'Activo'
        ),
        'Cambio de módem inmediato',
        'SOLICITUD FACTURACION RETIRO EQUIPO',
        NULL, --PLAN_ID
        (SELECT
                ID_PRODUCTO
            FROM
                DB_COMERCIAL.ADMI_PRODUCTO
            WHERE
                EMPRESA_COD = '10'
                AND CODIGO_PRODUCTO = 'SOLCMI'
                AND DESCRIPCION_PRODUCTO = 'RETIRO DE EQUIPOS'
                AND NOMBRE_TECNICO = 'OTROS'), --PRODUCTO_ID
        'Equipos', --OBSERVACION
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        'telcos_equipos',
        '10',
        'S'
    );


COMMIT;

/**
 * Sentencia DML que actualiza el parámetro existente para habilitar la facturación de visitas móviles para MD.
 * @author Luis Cabrera <lcabrera@telconet.ec>
 * @version 1.0
 * @since 14-08-2018
 */
    UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
    SET
        VALOR1 = 'SI',
        USR_ULT_MOD = 'lcabrera',
        FE_ULT_MOD = SYSDATE,
        IP_ULT_MOD = '127.0.0.1'
    WHERE
        PARAMETRO_ID = (
            SELECT
                CAB.ID_PARAMETRO
            FROM
                DB_GENERAL.ADMI_PARAMETRO_CAB CAB
            WHERE
                CAB.NOMBRE_PARAMETRO = 'FACTURAR_VISITA_DESDE_MOVIL'
                AND CAB.ESTADO = 'Activo'
                AND CAB.MODULO = 'SOPORTE'
        )
        AND ESTADO = 'Activo'
        AND EMPRESA_COD = '18'
        AND VALOR1 = 'NO';

COMMIT;

/**
 * Sentencia DML que actualiza el parámetro existente para modificar el valor por fracción hora a facturar en MD.
 * @author Luis Cabrera <lcabrera@telconet.ec>
 * @version 1.0
 * @since 14-08-2018
 */
    UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
    SET
        VALOR2 = '35',
        USR_ULT_MOD = 'lcabrera',
        FE_ULT_MOD = SYSDATE,
        IP_ULT_MOD = '127.0.0.1'
    WHERE
        PARAMETRO_ID = (
            SELECT
                CAB.ID_PARAMETRO
            FROM
                DB_GENERAL.ADMI_PARAMETRO_CAB CAB
            WHERE
                CAB.NOMBRE_PARAMETRO = 'VALOR_COBRO_POR_VISITA_TAREAS'
                AND CAB.ESTADO = 'Activo'
                AND CAB.MODULO = 'TECNICO'
        )
        AND ESTADO = 'Activo'
        AND EMPRESA_COD = '18'
        AND VALOR1 = 'VALOR_COBRAR_POR_FRACCION';

COMMIT;

/**
 * @author Luis Cabrera <lcabrera@telconet.ec>
 * @version 1.0
 * @since 22-08-2018
 * Se crean las sentencias DML para insertar parámetros (CAB y DET) relacionados con la facturación de cambio de equipos por soporte.
 */
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
    (
        ID_PARAMETRO,
        NOMBRE_PARAMETRO,
        DESCRIPCION,
        MODULO,
        PROCESO,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        USR_ULT_MOD,
        FE_ULT_MOD,
        IP_ULT_MOD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
        'RETIRO_EQUIPOS_SOPORTE',
        'VALORES A FACTURAR EN EL RETIRO DE EQUIPOS POR SOPORTE. DESCRIPCIÓN: EQUIPO, V1=TECNOLOGÍA, V2=PRECIO, V3=CARACTERISTICA_ID, V4=dependientes, v5= S/N presentación en cancelación voluntaria.',
        'FINANCIERO',
        'FACTURACION_RETIRO_EQUIPOS',
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL
    );

--TECNOLOGÍA TELLION CPE ONT
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
        USR_ULT_MOD,
        FE_ULT_MOD,
        IP_ULT_MOD,
        VALOR5,
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'RETIRO_EQUIPOS_SOPORTE'
           AND MODULO = 'FINANCIERO'
           AND PROCESO = 'FACTURACION_RETIRO_EQUIPOS'
           AND ESTADO = 'Activo'
        ),
        'CPE ONT', --NOMBRE DEL EQUIPO
        'TELLION', --TECNOLOGÍA
        85, --PRECIO
        (SELECT ID_CARACTERISTICA
           FROM DB_COMERCIAL.ADMI_CARACTERISTICA
          WHERE DESCRIPCION_CARACTERISTICA = 'CPE ONT TELLION'
            AND ESTADO = 'Activo'
            AND TIPO = 'COMERCIAL'
        ), --CARACTERISTICA_ID
        NULL,
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        'S', --Si se presenta en la plantilla de equipos.
        '18'
    );

--TECNOLOGÍA TELLION CPE
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
        USR_ULT_MOD,
        FE_ULT_MOD,
        IP_ULT_MOD,
        VALOR5,
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'RETIRO_EQUIPOS_SOPORTE'
           AND MODULO = 'FINANCIERO'
           AND PROCESO = 'FACTURACION_RETIRO_EQUIPOS'
           AND ESTADO = 'Activo'
        ),
        'CPE', --NOMBRE DEL EQUIPO
        'TELLION', --TECNOLOGÍA
        85, --PRECIO
        (SELECT ID_CARACTERISTICA
           FROM DB_COMERCIAL.ADMI_CARACTERISTICA
          WHERE DESCRIPCION_CARACTERISTICA = 'CPE ONT TELLION'
            AND ESTADO = 'Activo'
            AND TIPO = 'COMERCIAL'
        ), --CARACTERISTICA_ID
        NULL,
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        NULL,
        '18'
    );

--TECNOLOGÍA TELLION CPE WIFI
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
        USR_ULT_MOD,
        FE_ULT_MOD,
        IP_ULT_MOD,
        VALOR5,
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'RETIRO_EQUIPOS_SOPORTE'
           AND MODULO = 'FINANCIERO'
           AND PROCESO = 'FACTURACION_RETIRO_EQUIPOS'
           AND ESTADO = 'Activo'
        ),
        'CPE WIFI', --NOMBRE DEL EQUIPO
        'TELLION', --TECNOLOGÍA
        40, --PRECIO
        (SELECT ID_CARACTERISTICA
           FROM DB_COMERCIAL.ADMI_CARACTERISTICA
          WHERE DESCRIPCION_CARACTERISTICA = 'CPE WIFI TELLION'
            AND ESTADO = 'Activo'
            AND TIPO = 'COMERCIAL'
        ), --CARACTERISTICA_ID
        NULL,
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        'S', --Si se presenta en la plantilla de equipos.
        '18'
    );

--TECNOLOGÍA TELLION FUENTE DE PODER
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
        USR_ULT_MOD,
        FE_ULT_MOD,
        IP_ULT_MOD,
        VALOR5,
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'RETIRO_EQUIPOS_SOPORTE'
           AND MODULO = 'FINANCIERO'
           AND PROCESO = 'FACTURACION_RETIRO_EQUIPOS'
           AND ESTADO = 'Activo'
        ),
        'FUENTE DE PODER', --NOMBRE DEL EQUIPO
        'TELLION', --TECNOLOGÍA
        10, --PRECIO
        (SELECT ID_CARACTERISTICA
           FROM DB_COMERCIAL.ADMI_CARACTERISTICA
          WHERE DESCRIPCION_CARACTERISTICA = 'FUENTE DE PODER'
            AND ESTADO = 'Activo'
            AND TIPO = 'COMERCIAL'
        ), --CARACTERISTICA_ID
        'D',
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        'S', --Si se presenta en la plantilla de equipos.
        '18'
    );

--TECNOLOGÍA HUAWEI CPE
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
        USR_ULT_MOD,
        FE_ULT_MOD,
        IP_ULT_MOD,
        VALOR5,
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'RETIRO_EQUIPOS_SOPORTE'
           AND MODULO = 'FINANCIERO'
           AND PROCESO = 'FACTURACION_RETIRO_EQUIPOS'
           AND ESTADO = 'Activo'
        ),
        'CPE', --NOMBRE DEL EQUIPO
        'HUAWEI', --TECNOLOGÍA
        125, --PRECIO
        (SELECT ID_CARACTERISTICA
           FROM DB_COMERCIAL.ADMI_CARACTERISTICA
          WHERE DESCRIPCION_CARACTERISTICA = 'CPE ONT HUAWEI'
            AND ESTADO = 'Activo'
            AND TIPO = 'COMERCIAL'
        ), --CARACTERISTICA_ID
        NULL,
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        NULL,
        '18'
    );

--TECNOLOGÍA HUAWEI CPE ONT
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
        USR_ULT_MOD,
        FE_ULT_MOD,
        IP_ULT_MOD,
        VALOR5,
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'RETIRO_EQUIPOS_SOPORTE'
           AND MODULO = 'FINANCIERO'
           AND PROCESO = 'FACTURACION_RETIRO_EQUIPOS'
           AND ESTADO = 'Activo'
        ),
        'CPE ONT', --NOMBRE DEL EQUIPO
        'HUAWEI', --TECNOLOGÍA
        125, --PRECIO
        (SELECT ID_CARACTERISTICA
           FROM DB_COMERCIAL.ADMI_CARACTERISTICA
          WHERE DESCRIPCION_CARACTERISTICA = 'CPE ONT HUAWEI'
            AND ESTADO = 'Activo'
            AND TIPO = 'COMERCIAL'
        ), --CARACTERISTICA_ID
        NULL,
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        'S', --Si se presenta en la plantilla de equipos.
        '18'
    );

--TECNOLOGÍA HUAWEI ROSETA
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
        USR_ULT_MOD,
        FE_ULT_MOD,
        IP_ULT_MOD,
        VALOR5,
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'RETIRO_EQUIPOS_SOPORTE'
           AND MODULO = 'FINANCIERO'
           AND PROCESO = 'FACTURACION_RETIRO_EQUIPOS'
           AND ESTADO = 'Activo'
        ),
        'ROSETA', --NOMBRE DEL EQUIPO
        'HUAWEI', --TECNOLOGÍA
        10, --PRECIO
        (SELECT ID_CARACTERISTICA
           FROM DB_COMERCIAL.ADMI_CARACTERISTICA
          WHERE DESCRIPCION_CARACTERISTICA = 'ROSETA'
            AND ESTADO = 'Activo'
            AND TIPO = 'COMERCIAL'
        ), --CARACTERISTICA_ID
        'D',
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        'S', --Si se presenta en la plantilla de equipos.
        '18'
    );

--TECNOLOGÍA HUAWEI
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
        USR_ULT_MOD,
        FE_ULT_MOD,
        IP_ULT_MOD,
        VALOR5,
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'RETIRO_EQUIPOS_SOPORTE'
           AND MODULO = 'FINANCIERO'
           AND PROCESO = 'FACTURACION_RETIRO_EQUIPOS'
           AND ESTADO = 'Activo'
        ),
        'FUENTE DE PODER', --NOMBRE DEL EQUIPO
        'HUAWEI', --TECNOLOGÍA
        10, --PRECIO
        (SELECT ID_CARACTERISTICA
           FROM DB_COMERCIAL.ADMI_CARACTERISTICA
          WHERE DESCRIPCION_CARACTERISTICA = 'FUENTE DE PODER'
            AND ESTADO = 'Activo'
            AND TIPO = 'COMERCIAL'
        ), --CARACTERISTICA_ID
        'D',
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        'S', --Si se presenta en la plantilla de equipos.
        '18'
    );

--TECNOLOGÍA ADSL
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
        USR_ULT_MOD,
        FE_ULT_MOD,
        IP_ULT_MOD,
        VALOR5,
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'RETIRO_EQUIPOS_SOPORTE'
           AND MODULO = 'FINANCIERO'
           AND PROCESO = 'FACTURACION_RETIRO_EQUIPOS'
           AND ESTADO = 'Activo'
        ),
        'CPE ADSL', --NOMBRE DEL EQUIPO
        'ADSL', --TECNOLOGÍA
        30, --PRECIO
        (SELECT ID_CARACTERISTICA
           FROM DB_COMERCIAL.ADMI_CARACTERISTICA
          WHERE DESCRIPCION_CARACTERISTICA = 'EQUIPO ADSL'
            AND ESTADO = 'Activo'
            AND TIPO = 'COMERCIAL'
        ), --CARACTERISTICA_ID
        NULL,
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        'S', --Si se presenta en la plantilla de equipos.
        '18'
    );

--TECNOLOGÍA ADSL
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
        USR_ULT_MOD,
        FE_ULT_MOD,
        IP_ULT_MOD,
        VALOR5,
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'RETIRO_EQUIPOS_SOPORTE'
           AND MODULO = 'FINANCIERO'
           AND PROCESO = 'FACTURACION_RETIRO_EQUIPOS'
           AND ESTADO = 'Activo'
        ),
        'FUENTE DE PODER', --NOMBRE DEL EQUIPO
        'ADSL', --TECNOLOGÍA
        10, --PRECIO
        (SELECT ID_CARACTERISTICA
           FROM DB_COMERCIAL.ADMI_CARACTERISTICA
          WHERE DESCRIPCION_CARACTERISTICA = 'FUENTE DE PODER'
            AND ESTADO = 'Activo'
            AND TIPO = 'COMERCIAL'
        ), --CARACTERISTICA_ID
        'D',
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        'S', --Si se presenta en la plantilla de equipos.
        '18'
    );

--TECNOLOGÍA CISCO
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
        USR_ULT_MOD,
        FE_ULT_MOD,
        IP_ULT_MOD,
        VALOR5,
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'RETIRO_EQUIPOS_SOPORTE'
           AND MODULO = 'FINANCIERO'
           AND PROCESO = 'FACTURACION_RETIRO_EQUIPOS'
           AND ESTADO = 'Activo'
        ),
        'FUENTE DE PODER AP CISCO', --NOMBRE DEL EQUIPO
        'CISCO', --TECNOLOGÍA
        90, --PRECIO
        (SELECT ID_CARACTERISTICA
           FROM DB_COMERCIAL.ADMI_CARACTERISTICA
          WHERE DESCRIPCION_CARACTERISTICA = 'FUENTE DE PODER AP CISCO'
            AND ESTADO = 'Activo'
            AND TIPO = 'COMERCIAL'
        ), --CARACTERISTICA_ID
        'D',
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        'S', --Si se presenta en la plantilla de equipos.
        '18'
    );

--TECNOLOGÍA CISCO
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
        USR_ULT_MOD,
        FE_ULT_MOD,
        IP_ULT_MOD,
        VALOR5,
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'RETIRO_EQUIPOS_SOPORTE'
           AND MODULO = 'FINANCIERO'
           AND PROCESO = 'FACTURACION_RETIRO_EQUIPOS'
           AND ESTADO = 'Activo'
        ),
        'SMARTWIFI', --NOMBRE DEL EQUIPO
        'CISCO', --TECNOLOGÍA
        300, --PRECIO
        (SELECT ID_CARACTERISTICA
           FROM DB_COMERCIAL.ADMI_CARACTERISTICA
          WHERE DESCRIPCION_CARACTERISTICA = 'EQUIPO AP CISCO'
            AND ESTADO = 'Activo'
            AND TIPO = 'COMERCIAL'
        ), --CARACTERISTICA_ID
        NULL,
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        'S', --Si se presenta en la plantilla de equipos.
        '18'
    );
COMMIT;

/**
 * Scripts DML para la creación del parámetro para determinar si una empresa aplica o no a una determinada acción.
 * @author Luis Cabrera <lcabrera@telconet.ec>
 * @version 1.0
 * @since 30-08-2018
 */
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
    (
        ID_PARAMETRO,
        NOMBRE_PARAMETRO,
        DESCRIPCION,
        MODULO,
        PROCESO,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        USR_ULT_MOD,
        FE_ULT_MOD,
        IP_ULT_MOD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
        'EMPRESA_APLICA_PROCESO',
        'PARÁMETRO QUE DEFINE SI UNA EMPRESA APLICA A UN PARÁMETRO O NO. V1 = PROCESO A VALIDAR V2=S/N',
        'GENERAL',
        'TELCOS',
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL
    );

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
        USR_ULT_MOD,
        FE_ULT_MOD,
        IP_ULT_MOD,
        VALOR5,
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'EMPRESA_APLICA_PROCESO'
           AND MODULO = 'GENERAL'
           AND PROCESO = 'TELCOS'
           AND ESTADO = 'Activo'
        ),
        'Proceso de facturación de SOLICITUD CAMBIO DE MODEM INMEDIATO',
        'FACTURACION_CAMBIO_MODEM_INMEDIATO',
        'S',
        NULL,
        NULL,
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        NULL,
        '18'
    );

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
        USR_ULT_MOD,
        FE_ULT_MOD,
        IP_ULT_MOD,
        VALOR5,
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'EMPRESA_APLICA_PROCESO'
           AND MODULO = 'GENERAL'
           AND PROCESO = 'TELCOS'
           AND ESTADO = 'Activo'
        ),
        'Proceso de facturación de SOLICITUD CAMBIO DE MODEM INMEDIATO',
        'FACTURACION_CAMBIO_MODEM_INMEDIATO',
        'N',
        NULL,
        NULL,
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        NULL,
        '10'
    );

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
        USR_ULT_MOD,
        FE_ULT_MOD,
        IP_ULT_MOD,
        VALOR5,
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'EMPRESA_APLICA_PROCESO'
           AND MODULO = 'GENERAL'
           AND PROCESO = 'TELCOS'
           AND ESTADO = 'Activo'
        ),
        'Proceso que ingresa el número de horas de la visita técnica como característica de la factura',
        'CARACTERISTICA_HORAS_VISITA',
        'N',
        NULL,
        NULL,
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        NULL,
        '18'
    );

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
        USR_ULT_MOD,
        FE_ULT_MOD,
        IP_ULT_MOD,
        VALOR5,
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'EMPRESA_APLICA_PROCESO'
           AND MODULO = 'GENERAL'
           AND PROCESO = 'TELCOS'
           AND ESTADO = 'Activo'
        ),
        'Proceso que ingresa el número de horas de la visita técnica como característica de la factura',
        'CARACTERISTICA_HORAS_VISITA',
        'S',
        NULL,
        NULL,
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        NULL,
        '10'
    );

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
        USR_ULT_MOD,
        FE_ULT_MOD,
        IP_ULT_MOD,
        VALOR5,
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'EMPRESA_APLICA_PROCESO'
           AND MODULO = 'GENERAL'
           AND PROCESO = 'TELCOS'
           AND ESTADO = 'Activo'
        ),
        'Proceso que ingresa el caso como descripción de la factura',
        'CARACTERISTICA_CASO',
        'S',
        NULL,
        NULL,
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        NULL,
        '18'
    );

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
        USR_ULT_MOD,
        FE_ULT_MOD,
        IP_ULT_MOD,
        VALOR5,
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'EMPRESA_APLICA_PROCESO'
           AND MODULO = 'GENERAL'
           AND PROCESO = 'TELCOS'
           AND ESTADO = 'Activo'
        ),
        'Proceso que ingresa el caso como descripción de la factura',
        'CARACTERISTICA_CASO',
        'N',
        NULL,
        NULL,
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        NULL,
        '10'
    );

--NUMERACIÓN AUTOMÁTICA DE NOTA DE CRÉDITO
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
        USR_ULT_MOD,
        FE_ULT_MOD,
        IP_ULT_MOD,
        VALOR5,
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'EMPRESA_APLICA_PROCESO'
           AND MODULO = 'GENERAL'
           AND PROCESO = 'TELCOS'
           AND ESTADO = 'Activo'
        ),
        'Proceso de numeración automática para las Notas de cŕedito por facturas de instalación',
        'NUMERACION_AUTOMATICA_NOTA_CREDITO',
        'S',
        NULL,
        NULL,
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        NULL,
        '18'
    );

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
        USR_ULT_MOD,
        FE_ULT_MOD,
        IP_ULT_MOD,
        VALOR5,
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'EMPRESA_APLICA_PROCESO'
           AND MODULO = 'GENERAL'
           AND PROCESO = 'TELCOS'
           AND ESTADO = 'Activo'
        ),
        'Proceso de numeración automática para las Notas de cŕedito por facturas de instalación',
        'NUMERACION_AUTOMATICA_NOTA_CREDITO',
        'N',
        NULL,
        NULL,
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        NULL,
        '10'
    );

--APLICA NOTA DE CRÉDITO POR ELIMINAR ORDEN DE SERVICIO.
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
        USR_ULT_MOD,
        FE_ULT_MOD,
        IP_ULT_MOD,
        VALOR5,
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'EMPRESA_APLICA_PROCESO'
           AND MODULO = 'GENERAL'
           AND PROCESO = 'TELCOS'
           AND ESTADO = 'Activo'
        ),
        'Proceso de reverso de contrato que crea Notas de créditos al eliminar la orden de servicio',
        'NC_X_ELIMINAR_ORDEN_SERVICIO',
        'S',
        NULL,
        NULL,
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        NULL,
        '18'
    );

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
        USR_ULT_MOD,
        FE_ULT_MOD,
        IP_ULT_MOD,
        VALOR5,
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'EMPRESA_APLICA_PROCESO'
           AND MODULO = 'GENERAL'
           AND PROCESO = 'TELCOS'
           AND ESTADO = 'Activo'
        ),
        'Proceso de reverso de contrato que crea Notas de créditos al eliminar la orden de servicio',
        'NC_X_ELIMINAR_ORDEN_SERVICIO',
        'N',
        NULL,
        NULL,
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        NULL,
        '10'
    );

COMMIT;

/**
 * Script DML que crea el motivo para las solicitudes de Contratos de origen WEB
 * @author Luis Cabrera <lcabrera@telconet.ec>
 * @version 1.0
 * @since 10-10-2018
 */
  INSERT INTO DB_GENERAL.ADMI_MOTIVO
    (
      ID_MOTIVO,
      RELACION_SISTEMA_ID,
      NOMBRE_MOTIVO,
      ESTADO,
      USR_CREACION,
      FE_CREACION,
      USR_ULT_MOD,
      FE_ULT_MOD,
      CTA_CONTABLE,
      REF_MOTIVO_ID
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_MOTIVO.NEXTVAL,
      55,
      'Solicitud Instalacion Por Contrato Web',
      'Activo',
      'lcabrera',
      SYSDATE,
      'lcabrera',
      SYSDATE,
      NULL,
      NULL
    );

COMMIT;


/**
 * @author Luis Cabrera <lcabrera@telconet.ec>
 * @version 1.0
 * @since 10-10-2018
 * Se crean las sentencias DML para insertar parámetros (CAB y DET) relacionados con las solicitudes de contrato/ facturación de instalación.
 */
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
    (
        ID_PARAMETRO,
        NOMBRE_PARAMETRO,
        DESCRIPCION,
        MODULO,
        PROCESO,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        USR_ULT_MOD,
        FE_ULT_MOD,
        IP_ULT_MOD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
        'SOLICITUDES_DE_CONTRATO',
        'SE INGRESAN LOS TIPOS DE CONTRATOS PARA PODER SER FACTURADOS.',
        'COMERCIAL',
        'SOLICITUDES_DE_CONTRATO',
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL
    );


--CONTRATO WEB
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
        USR_ULT_MOD,
        FE_ULT_MOD,
        IP_ULT_MOD,
        VALOR5,
        EMPRESA_COD,
        VALOR6,
        VALOR7
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'SOLICITUDES_DE_CONTRATO' 
           AND MODULO = 'COMERCIAL'
           AND PROCESO = 'SOLICITUDES_DE_CONTRATO'
           AND ESTADO = 'Activo'
        ),
        'Contratos creados desde el Telcos',
        'WEB', --ORIGEN
        'POR_CONTRATO_FISICO', --CARACTERÍSTICA DEL CONTRATO DIGITAL/FISICO
        'Solicitud de facturación de Instalación por creación de contrato WEB', --OBSERVACIÓN PARA INSERTAR EN LA SOLICITUD
        'SOLICITUD FACTURACION CONTRATO WEB', --NOMBRE DE LA SOLICITUD
        'Inactivo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        'Solicitud Instalacion Por Contrato Web',--Nombre Motivo
        '18',
        'telcos_web', --USR_CREACION para escribir los registros en los procesos de solicitud y facturas.
        '7' --Días permitidos para crear factura.
    );

--CONTRATO DIGITAL
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
        USR_ULT_MOD,
        FE_ULT_MOD,
        IP_ULT_MOD,
        VALOR5,
        EMPRESA_COD,
        VALOR6,
        VALOR7
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'SOLICITUDES_DE_CONTRATO' 
           AND MODULO = 'COMERCIAL'
           AND PROCESO = 'SOLICITUDES_DE_CONTRATO'
           AND ESTADO = 'Activo'
        ),
        'Contratos creados desde el Móvil',
        'MOVIL', --CARACTERÍSTICA DEL CONTRATO DIGITAL/FISICO
        'POR_CONTRATO_DIGITAL', --CARACTERÍSTICA DEL CONTRATO DIGITAL/FISICO
        'Solicitud de descuento de Instalación por creación de contrato DIGITAL', --OBSERVACIÓN PARA INSERTAR EN LA SOLICITUD
        'SOLICITUD INSTALACION GRATIS', --NOMBRE DE LA SOLICITUD
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        'Solicitud Instalacion Por Contrato Digital',--Nombre Motivo
        '18',
        'telcos_contrato', --USR_CREACION para escribir los registros en los procesos de solicitud y facturas.
        '2' --Días permitidos para crear factura.
    );

COMMIT;


/**
  * Script de actualización e insert del número de días de vigencias de una factura para MOVIL (update) y para WEB (INSERT)
  * @author Luis Cabrera <lcabrera@telconet.ec>
  * @version 1.0
  * @since 11-10-2018
  */
    --Se actualiza valor1 = días de vigencia para contrato digital
    UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
       SET DESCRIPCION = 'Días de vigencia de una factura por CONTRATO DIGITAL',
           VALOR2 = 'SOLICITUD INSTALACION GRATIS',
           USR_ULT_MOD = 'lcabrera',
           FE_ULT_MOD = SYSDATE,
           IP_ULT_MOD = '127.0.0.1'
     WHERE ESTADO = 'Activo'
       AND PARAMETRO_ID = (SELECT ID_PARAMETRO
                             FROM DB_GENERAL.ADMI_PARAMETRO_CAB
                            WHERE ESTADO         = 'Activo'
                              AND NOMBRE_PARAMETRO = 'DIAS_VIGENCIA_FACTURA')
       AND DESCRIPCION = 'Dias de vigencia de una factura';

    --Se crea el parámetro días de vigencia para contrato web
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
        USR_ULT_MOD,
        FE_ULT_MOD,
        IP_ULT_MOD,
        VALOR5,
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE ESTADO         = 'Activo'
           AND NOMBRE_PARAMETRO = 'DIAS_VIGENCIA_FACTURA'
        ),
        'Días de vigencia de una factura por CONTRATO FISICO',
        '5', --Número de días
        'SOLICITUD FACTURACION CONTRATO WEB', --NOMBRE SOLICITUD
        NULL,
        NULL,
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        NULL,
        '18'
    );

COMMIT;

/**
 * @author Luis Cabrera <lcabrera@telconet.ec>
 * @version 1.0
 * @since 10-10-2018
 * Se crean las sentencias DML para insertar parámetros (CAB y DET) relacionados con la nota de crédito del reverso del contrato.
 */
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
    (
        ID_PARAMETRO,
        NOMBRE_PARAMETRO,
        DESCRIPCION,
        MODULO,
        PROCESO,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        USR_ULT_MOD,
        FE_ULT_MOD,
        IP_ULT_MOD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
        'NOTA_CREDITO_X_CONTRATO',
        'SE INGRESAN VALORES NECESARIOS PARA LA CREACION DE LA NOTA DE CREDITO.',
        'FINANCIERO',
        'NOTA_CREDITO_X_CONTRATO',
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL
    );


--CONTRATO WEB
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
        USR_ULT_MOD,
        FE_ULT_MOD,
        IP_ULT_MOD,
        VALOR5,
        EMPRESA_COD,
        VALOR6
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'NOTA_CREDITO_X_CONTRATO' 
           AND MODULO = 'FINANCIERO'
           AND PROCESO = 'NOTA_CREDITO_X_CONTRATO'
           AND ESTADO = 'Activo'
        ),
        'Contratos creados desde el Telcos Web',
        'WEB', --ORIGEN DEL CONTRATO DIGITAL/FISICO
        'POR_CONTRATO_FISICO', --CARACTERÍSTICA DEL CONTRATO DIGITAL/FISICO
        'FECHA_VIGENCIA', --CARACTERÍSTICA DE LA FECHA DE VIGENCIA DEL CONTRATO
        'Falta de pago de las facturas de instalación', --NOMBRE DEL MOTIVO DE LA N/C
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        'Generación de NC por anulación de contrato web', --Observación de la N/C
        '18',
        'telcos_web' --USR_CREACION para escribir los registros en la factura y N/C.
    );

--CONTRATO DIGITAL
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
        USR_ULT_MOD,
        FE_ULT_MOD,
        IP_ULT_MOD,
        VALOR5,
        EMPRESA_COD,
        VALOR6
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'NOTA_CREDITO_X_CONTRATO' 
           AND MODULO = 'FINANCIERO'
           AND PROCESO = 'NOTA_CREDITO_X_CONTRATO'
           AND ESTADO = 'Activo'
        ),
        'Contratos creados desde el Móvil',
        'MOVIL', --ORIGEN DEL CONTRATO DIGITAL/FISICO
        'POR_CONTRATO_DIGITAL', --CARACTERÍSTICA DEL CONTRATO DIGITAL/FISICO
        'FECHA_VIGENCIA', --CARACTERÍSTICA DE LA FECHA DE VIGENCIA DEL CONTRATO
        'Falta de pago de las facturas de instalación', --NOMBRE DEL MOTIVO DE LA N/C
        'Inactivo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        'Falta de pago de las facturas de instalación', --Observación de la N/C
        '18',
        'telcos_contrato' --USR_CREACION para escribir los registros en la factura y N/C.
    );

COMMIT;

/**
 * @author Luis Cabrera <lcabrera@telconet.ec>
 * @version 1.0
 * @since 10-10-2018
 * Se crean las sentencias DML para insertar parámetros (CAB y DET) relacionados con las visitas técnicas y el número de horas facturadas.
 */
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
    (
        ID_PARAMETRO,
        NOMBRE_PARAMETRO,
        DESCRIPCION,
        MODULO,
        PROCESO,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        USR_ULT_MOD,
        FE_ULT_MOD,
        IP_ULT_MOD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
        'MOVIL_VISITA_FACTURADA',
        'PARAMETRO PARA LA PANTALLA DE VISITAS TÉCNICAS. V1=Etiqueta Facturable V2=S/N Presentación de horas a facturar V3=Valor por defecto',
        'TECNICO',
        'MOVIL_VISITA_FACTURADA',
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL
    );


--TELCONET
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
        USR_ULT_MOD,
        FE_ULT_MOD,
        IP_ULT_MOD,
        VALOR5,
        EMPRESA_COD,
        VALOR6
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'MOVIL_VISITA_FACTURADA'
           AND MODULO = 'TECNICO'
           AND PROCESO = 'MOVIL_VISITA_FACTURADA'
           AND ESTADO = 'Activo'
        ),
        'Visitas técnicas para TN',
        'Facturable', --ETIQUETA FACTURABLE
        'S', --CARACTERÍSTICA DEL CONTRATO DIGITAL/FISICO
        '1', --CARACTERÍSTICA DE LA FECHA DE VIGENCIA DEL CONTRATO
        NULL,
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        NULL,
        '10',
        NULL
    );

--MEGADATOS
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
        USR_ULT_MOD,
        FE_ULT_MOD,
        IP_ULT_MOD,
        VALOR5,
        EMPRESA_COD,
        VALOR6
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'MOVIL_VISITA_FACTURADA'
           AND MODULO = 'TECNICO'
           AND PROCESO = 'MOVIL_VISITA_FACTURADA'
           AND ESTADO = 'Activo'
        ),
        'Visitas técnicas para MD',
        'Visita Facturada', --ETIQUETA FACTURABLE
        'N', --CARACTERÍSTICA DEL CONTRATO DIGITAL/FISICO
        '1', --CARACTERÍSTICA DE LA FECHA DE VIGENCIA DEL CONTRATO
        NULL,
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        NULL,
        '18',
        NULL
    );

COMMIT;

/**
 * @author Luis Cabrera <lcabrera@telconet.ec>
 * @version 1.0
 * @since 10-10-2018
 * Se crea el parámetro para obtener la decripción de la factura en el XML.
 */
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
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
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD,
    OBSERVACION,
    VALOR6
) VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO
       FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'DESCRIPCION_TIPO_FACTURACION'
        AND ESTADO = 'Activo'
        AND MODULO = 'COMERCIAL'
        AND PROCESO = 'FACTURACION'),
    'PARAMETRO DONDE SE CONFIGURA EL DETALLE PARA LA FACTURACION POR VISITA TÉCNICA',
    'telcos_visitaTecnica',
    'VALOR2', --Se llena este valor porque es necesario que exista para la búsqueda.
    'VALOR3', --Se llena este valor porque es necesario que exista para la búsqueda.
    'MD',
    'Activo',
    'lcabrera',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    '18',
    NULL,
    NULL
);

COMMIT;