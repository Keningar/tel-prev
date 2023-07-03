/**
 * @author Luis Cabrera <lcabrera@telconet.ec>
 * @version 1.0
 * @since 28-01-2019
 * Parámetro que define si una empresa aplica al flujo de origen del punto.
 */
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
        'Flujo de tipo de origen del punto',
        'TIPO_ORIGEN_TECNOLOGIA_PUNTO',
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
        'Flujo de tipo de origen del punto',
        'TIPO_ORIGEN_TECNOLOGIA_PUNTO',
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
 * @author Luis Cabrera <lcabrera@telconet.ec>
 * @version 1.0
 * @since 28-01-2019
 * Insert del parámetro de los tipos de origen de tecnología.
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
        'COMBO_TIPO_ORIGEN_TECNOLOGIA_PUNTO',
        'PARAMETRO LA LISTA A MOSTRARSE EN LA PANTALLA DE CREACIÓN Y MODIFICACIÓN DEL PUNTO. V1=Label a presentar V2=descripcionCaracteristica V3=esFacturable',
        'COMERCIAL',
        'COMBO_TIPO_ORIGEN_TECNOLOGIA_PUNTO',
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
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
         WHERE NOMBRE_PARAMETRO = 'COMBO_TIPO_ORIGEN_TECNOLOGIA_PUNTO'
           AND MODULO = 'COMERCIAL'
           AND PROCESO = 'COMBO_TIPO_ORIGEN_TECNOLOGIA_PUNTO'
           AND ESTADO = 'Activo'
        ),
        'Migración de tecnología (Puntos que no pagan instalación).',
        'Migración de tecnología', --Descripción del combo
        'MIGRACION_TECNOLOGIA', --Característica a insertar
        'N', --Genera factura de instalación
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
 * @since 24-11-2018
 * Característica para determinar EL ORIGEN DEL PUNTO.
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
        'TIPO_ORIGEN_TECNOLOGIA',
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
        'MIGRACION_TECNOLOGIA',
        'N',
        'Activo',
        SYSDATE,
        'lcabrera',
        NULL,
        NULL,
        'COMERCIAL'
    );
COMMIT;
