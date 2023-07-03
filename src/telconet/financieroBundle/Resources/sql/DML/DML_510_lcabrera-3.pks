
/**
  * @author Luis Cabrera <lcabrera@telconet.ec>
  * @version 1.0 21-11-2017 Se actualizan los débitos actuales correspondientes a MD y se fija el ciclo de facturación del 1 al 30.
  */
    UPDATE DB_FINANCIERO.INFO_DEBITO_GENERAL
        SET
            CICLO_ID = (
                SELECT
                    CICLO.ID_CICLO
                FROM
                    DB_FINANCIERO.ADMI_CICLO CICLO
                WHERE
                    CICLO.NOMBRE_CICLO LIKE '%Ciclo (I) - 1 al 30%'
            )
    WHERE
        ID_DEBITO_GENERAL IN (
            SELECT DISTINCT
                CAB.DEBITO_GENERAL_ID
            FROM
                DB_FINANCIERO.INFO_DEBITO_CAB CAB
            WHERE
                EMPRESA_ID = '18'
        );

/**
  * @author Luis Cabrera <lcabrera@telconet.ec>
  * @version 1.0 21-11-2017 Se insertan los parámetros.
  */
    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB (
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
    ) VALUES (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
        'CICLO_FACTURACION_EMPRESA',
        'DEFINE SI LAS EMPRESAS APLICAN A LOS CICLOS DE FACTURACIÓN',
        'FINANCIERO',
        'CICLO_FACTURACION',
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL
    );


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
        EMPRESA_COD
    ) VALUES (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO
         FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'CICLO_FACTURACION_EMPRESA'
         AND MODULO = 'FINANCIERO'
         AND ESTADO = 'Activo'
         AND PROCESO = 'CICLO_FACTURACION'),
        'MEGADATOS',
        'S',
        'MD',
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
        EMPRESA_COD
    ) VALUES (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO
         FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'CICLO_FACTURACION_EMPRESA'
         AND MODULO = 'FINANCIERO'
         AND ESTADO = 'Activo'
         AND PROCESO = 'CICLO_FACTURACION'),
        'TELCONET',
        'N',
        'TN',
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


/**
  * @author Luis Cabrera <lcabrera@telconet.ec>
  * @version 1.0 29-11-2017 Se insertan los parámetros que aplican a JOB_PROCESA_DEBITO_GENERAL.
  */
    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB (
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
    ) VALUES (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
        'JOB_PROCESA_DEBITO_GENERAL',
        'DEFINE SI LAS EMPRESAS APLICAN AL JOB QUE ANULA LAS CABECERAS DE LOS DÉBITOS',
        'FINANCIERO',
        'DEBITOS',
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL
    );


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
        EMPRESA_COD
    ) VALUES (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO
         FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'JOB_PROCESA_DEBITO_GENERAL'
         AND MODULO = 'FINANCIERO'
         AND ESTADO = 'Activo'
         AND PROCESO = 'DEBITOS'),
        'MEGADATOS',
        0,
        1,
        'Pendiente',
        'Anulado',
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
        EMPRESA_COD
    ) VALUES (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO
         FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'JOB_PROCESA_DEBITO_GENERAL'
         AND MODULO = 'FINANCIERO'
         AND ESTADO = 'Activo'
         AND PROCESO = 'DEBITOS'),
        'MEGADATOS',
        NULL,
        90,
        'Pendiente',
        'Anulado',
        'Inactivo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        NULL,
        '18'
    );

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
        EMPRESA_COD
    ) VALUES (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO
         FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'JOB_PROCESA_DEBITO_GENERAL'
         AND MODULO = 'FINANCIERO'
         AND ESTADO = 'Activo'
         AND PROCESO = 'DEBITOS'),
        'TELCONET',
        0,
        1,
        'Pendiente',
        'Anulado',
        'Inactivo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        NULL,
        '10'
    );

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
        EMPRESA_COD
    ) VALUES (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO
         FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'JOB_PROCESA_DEBITO_GENERAL'
         AND MODULO = 'FINANCIERO'
         AND ESTADO = 'Activo'
         AND PROCESO = 'DEBITOS'),
        'TELCONET',
        NULL,
        90,
        'Pendiente',
        'Anulado',
        'Inactivo',
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