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
        'Muestra en la pantalla de Convertir a Orden de Trabajo los puntos que tienen deuda',
        'MOSTRAR_DEUDAS_ORDEN_TRABAJO',
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
        'Muestra en la pantalla de Convertir a Orden de Trabajo los puntos que tienen deuda',
        'MOSTRAR_DEUDAS_ORDEN_TRABAJO',
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