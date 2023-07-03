/**
 * Se agregan las características de la tabla INFO_CONSUMO_CLOUD_DET.
 * @author Luis Cabrera
 * @version 1.0
 * @since 13/03/2018
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
    'VM_NAME',
    'T',
    'Activo',
    SYSDATE,
    'lcabrera',
    NULL,
    NULL,
    'FINANCIERO'
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
    'DATE_RANGE',
    'T',
    'Activo',
    SYSDATE,
    'lcabrera',
    NULL,
    NULL,
    'FINANCIERO'
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
    'CPU_USED_COST',
    'N',
    'Activo',
    SYSDATE,
    'lcabrera',
    NULL,
    NULL,
    'FINANCIERO'
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
    'MEMORY_USED_COST',
    'N',
    'Activo',
    SYSDATE,
    'lcabrera',
    NULL,
    NULL,
    'FINANCIERO'
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
    'NETWORK_USED_COST',
    'N',
    'Activo',
    SYSDATE,
    'lcabrera',
    NULL,
    NULL,
    'FINANCIERO'
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
    'STORAGE_USED_COST',
    'N',
    'Activo',
    SYSDATE,
    'lcabrera',
    NULL,
    NULL,
    'FINANCIERO'
);

COMMIT;

/**
 * Parámetros necesarios para la facturación por consumo de Cloudforms.
 * @author Luis Cabrera
 * @version 1.0
 * @since 13/03/2018
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
        'TABLA_CONSUMO_CLOUDFORMS',
        'CONTIENE TODOS LOS VALORES DEL REPORTE CSV A PROCESAR.',
        'FINANCIERO',
        'FACTURACION_CONSUMO',
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
         WHERE NOMBRE_PARAMETRO = 'TABLA_CONSUMO_CLOUDFORMS' 
           AND MODULO = 'FINANCIERO'
           AND PROCESO = 'FACTURACION_CONSUMO'
           AND ESTADO = 'Activo'
        ),
        'VM Name',
        'N',
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
         WHERE NOMBRE_PARAMETRO = 'TABLA_CONSUMO_CLOUDFORMS' 
           AND MODULO = 'FINANCIERO'
           AND PROCESO = 'FACTURACION_CONSUMO'
           AND ESTADO = 'Activo'
        ),
        'Date Range',
        'N',
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
         WHERE NOMBRE_PARAMETRO = 'TABLA_CONSUMO_CLOUDFORMS' 
           AND MODULO = 'FINANCIERO'
           AND PROCESO = 'FACTURACION_CONSUMO'
           AND ESTADO = 'Activo'
        ),
        'CPU Used Cost',
        (SELECT TO_CHAR(ID_CARACTERISTICA)
           FROM DB_COMERCIAL.ADMI_CARACTERISTICA
          WHERE DESCRIPCION_CARACTERISTICA = 'CPU_USED_COST'
            AND ESTADO = 'Activo'
            AND TIPO = 'FINANCIERO'),
        'S',
        'cpuCost',
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
         WHERE NOMBRE_PARAMETRO = 'TABLA_CONSUMO_CLOUDFORMS' 
           AND MODULO = 'FINANCIERO'
           AND PROCESO = 'FACTURACION_CONSUMO'
           AND ESTADO = 'Activo'
        ),
        'Memory Used Cost',
        (SELECT TO_CHAR(ID_CARACTERISTICA)
           FROM DB_COMERCIAL.ADMI_CARACTERISTICA
          WHERE DESCRIPCION_CARACTERISTICA = 'MEMORY_USED_COST'
            AND ESTADO = 'Activo'
            AND TIPO = 'FINANCIERO'),
        'S',
        'memoryCost',
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
         WHERE NOMBRE_PARAMETRO = 'TABLA_CONSUMO_CLOUDFORMS' 
           AND MODULO = 'FINANCIERO'
           AND PROCESO = 'FACTURACION_CONSUMO'
           AND ESTADO = 'Activo'
        ),
        'Network I/O Used Cost',
        (SELECT TO_CHAR(ID_CARACTERISTICA)
           FROM DB_COMERCIAL.ADMI_CARACTERISTICA
          WHERE DESCRIPCION_CARACTERISTICA = 'NETWORK_USED_COST'
            AND ESTADO = 'Activo'
            AND TIPO = 'FINANCIERO'),
        'S',
        'networkCost',
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
         WHERE NOMBRE_PARAMETRO = 'TABLA_CONSUMO_CLOUDFORMS' 
           AND MODULO = 'FINANCIERO'
           AND PROCESO = 'FACTURACION_CONSUMO'
           AND ESTADO = 'Activo'
        ),
        'Storage Used Cost',
        (SELECT TO_CHAR(ID_CARACTERISTICA)
           FROM DB_COMERCIAL.ADMI_CARACTERISTICA
          WHERE DESCRIPCION_CARACTERISTICA = 'STORAGE_USED_COST'
            AND ESTADO = 'Activo'
            AND TIPO = 'FINANCIERO'),
        'S',
        'storageCost',
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
         WHERE NOMBRE_PARAMETRO = 'TABLA_CONSUMO_CLOUDFORMS' 
           AND MODULO = 'FINANCIERO'
           AND PROCESO = 'FACTURACION_CONSUMO'
           AND ESTADO = 'Activo'
        ),
        'License Cost',
        (SELECT TO_CHAR(ID_CARACTERISTICA)
           FROM DB_COMERCIAL.ADMI_CARACTERISTICA
          WHERE DESCRIPCION_CARACTERISTICA = 'LICENSE_COST'
            AND ESTADO = 'Activo'
            AND TIPO = 'FINANCIERO'),
        'S',
        'licenseCost',
        NULL,
        'Activo',
        'arsuarez',
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
         WHERE NOMBRE_PARAMETRO = 'TABLA_CONSUMO_CLOUDFORMS' 
           AND MODULO = 'FINANCIERO'
           AND PROCESO = 'FACTURACION_CONSUMO'
           AND ESTADO = 'Activo'
        ),
        'Total Cost',
        (SELECT TO_CHAR(ID_CARACTERISTICA)
           FROM DB_COMERCIAL.ADMI_CARACTERISTICA
          WHERE DESCRIPCION_CARACTERISTICA = 'TOTAL_COST'
            AND ESTADO = 'Activo'
            AND TIPO = 'FINANCIERO'),
        'S',
        'totalCost',
        NULL,
        'Activo',
        'arsuarez',
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
 * Parámetros necesarios para enviar notificaciones la acción confirmación/cancelación/reactivación del servicio a qué departamento.
 * @author Luis Cabrera
 * @version 1.0
 * @since 23/03/2018
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
        'PRODUCTO_GENERA_TAREA',
        'Descripción:Acción|Valor1:ID_PRODUCTO|Valor2:PERSONA_EMPRESA_ROL_ID|Valor3:alias;notificar|Valor4:TAREA_ID|Valor5:Canton_id',
        'TECNICO',
        'SERVICIO',
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL
    );

--Parámetros para CONFIRMACIÓN DEL SERVICIO
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
         WHERE NOMBRE_PARAMETRO = 'PRODUCTO_GENERA_TAREA' 
           AND MODULO = 'TECNICO'
           AND PROCESO = 'SERVICIO'
           AND ESTADO = 'Activo'
        ),
        'CONFIRMACION'
        ,
        (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
         WHERE EMPRESA_COD = 10
         AND CODIGO_PRODUCTO = 'IAAS'
         AND DESCRIPCION_PRODUCTO= 'CLOUD IAAS PUBLIC'
         AND ESTADO = 'Activo'),
        '616575', --PERSONA_EMPRESA_ROL_ID
        'cobranzas_gye@telconet.ec', --ALIAS DE NOTIFICACIÓN
        '4248',
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        '75', --GYE
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
         WHERE NOMBRE_PARAMETRO = 'PRODUCTO_GENERA_TAREA' 
           AND MODULO = 'TECNICO'
           AND PROCESO = 'SERVICIO'
           AND ESTADO = 'Activo'
        ),
        'CONFIRMACION'
        ,
        (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
         WHERE EMPRESA_COD = 10
         AND CODIGO_PRODUCTO = 'IAAS'
         AND DESCRIPCION_PRODUCTO= 'CLOUD IAAS PUBLIC'
         AND ESTADO = 'Activo'),
        '287344', --PERSONA_EMPRESA_ROL_ID
        'cobranzas_uio@telconet.ec', --ALIAS DE NOTIFICACIÓN
        '4248',
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        '178', --UIO
        '10'
    );

--Parámetros para REACTIVACIÓN DEL SERVICIO
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
         WHERE NOMBRE_PARAMETRO = 'PRODUCTO_GENERA_TAREA' 
           AND MODULO = 'TECNICO'
           AND PROCESO = 'SERVICIO'
           AND ESTADO = 'Activo'
        ),
        'REACTIVACION'
        ,
        (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
         WHERE EMPRESA_COD = 10
         AND CODIGO_PRODUCTO = 'IAAS'
         AND DESCRIPCION_PRODUCTO= 'CLOUD IAAS PUBLIC'
         AND ESTADO = 'Activo'),
        '616575', --PERSONA_EMPRESA_ROL_ID
        'cobranzas_gye@telconet.ec', --ALIAS DE NOTIFICACIÓN
        '4258',
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        '75', --GYE
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
         WHERE NOMBRE_PARAMETRO = 'PRODUCTO_GENERA_TAREA' 
           AND MODULO = 'TECNICO'
           AND PROCESO = 'SERVICIO'
           AND ESTADO = 'Activo'
        ),
        'REACTIVACION'
        ,
        (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
         WHERE EMPRESA_COD = 10
         AND CODIGO_PRODUCTO = 'IAAS'
         AND DESCRIPCION_PRODUCTO= 'CLOUD IAAS PUBLIC'
         AND ESTADO = 'Activo'),
        '287344', --PERSONA_EMPRESA_ROL_ID
        'cobranzas_uio@telconet.ec', --ALIAS DE NOTIFICACIÓN
        '4258',
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        '178', --UIO
        '10'
    );


--Parámetros para CORTE DEL SERVICIO
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
         WHERE NOMBRE_PARAMETRO = 'PRODUCTO_GENERA_TAREA' 
           AND MODULO = 'TECNICO'
           AND PROCESO = 'SERVICIO'
           AND ESTADO = 'Activo'
        ),
        'CORTE'
        ,
        (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
         WHERE EMPRESA_COD = 10
         AND CODIGO_PRODUCTO = 'IAAS'
         AND DESCRIPCION_PRODUCTO= 'CLOUD IAAS PUBLIC'
         AND ESTADO = 'Activo'),
        '616575', --PERSONA_EMPRESA_ROL_ID
        'cobranzas_gye@telconet.ec', --ALIAS DE NOTIFICACIÓN
        '3603',
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        '75', --GYE
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
         WHERE NOMBRE_PARAMETRO = 'PRODUCTO_GENERA_TAREA' 
           AND MODULO = 'TECNICO'
           AND PROCESO = 'SERVICIO'
           AND ESTADO = 'Activo'
        ),
        'CORTE'
        ,
        (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
         WHERE EMPRESA_COD = 10
         AND CODIGO_PRODUCTO = 'IAAS'
         AND DESCRIPCION_PRODUCTO= 'CLOUD IAAS PUBLIC'
         AND ESTADO = 'Activo'),
        '287344', --PERSONA_EMPRESA_ROL_ID
        'cobranzas_uio@telconet.ec', --ALIAS DE NOTIFICACIÓN
        '3603',
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        '178', --UIO
        '10'
    );


--Parámetros para CANCELACION DEL SERVICIO
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
         WHERE NOMBRE_PARAMETRO = 'PRODUCTO_GENERA_TAREA' 
           AND MODULO = 'TECNICO'
           AND PROCESO = 'SERVICIO'
           AND ESTADO = 'Activo'
        ),
        'CANCELACION'
        ,
        (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
         WHERE EMPRESA_COD = 10
         AND CODIGO_PRODUCTO = 'IAAS'
         AND DESCRIPCION_PRODUCTO= 'CLOUD IAAS PUBLIC'
         AND ESTADO = 'Activo'),
        '616575', --PERSONA_EMPRESA_ROL_ID
        'cobranzas_gye@telconet.ec', --ALIAS DE NOTIFICACIÓN
        '4254',
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        '75', --GYE
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
         WHERE NOMBRE_PARAMETRO = 'PRODUCTO_GENERA_TAREA' 
           AND MODULO = 'TECNICO'
           AND PROCESO = 'SERVICIO'
           AND ESTADO = 'Activo'
        ),
        'CANCELACION'
        ,
        (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
         WHERE EMPRESA_COD = 10
         AND CODIGO_PRODUCTO = 'IAAS'
         AND DESCRIPCION_PRODUCTO= 'CLOUD IAAS PUBLIC'
         AND ESTADO = 'Activo'),
        '287344', --PERSONA_EMPRESA_ROL_ID
        'cobranzas_uio@telconet.ec', --ALIAS DE NOTIFICACIÓN
        '4254',
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        '178', --UIO
        '10'
    );


COMMIT;

/