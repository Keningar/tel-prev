
    /*
    * Se realiza la inserción de parámetros para el envío de notificación a los Gerente de Producto
    * cuando se crea una cotización desde TelcoCRM.
    * @author Kevin Baque <kbaque@telconet.ec>
    * @version 1.0 25-12-2020
    */
    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        EMPRESA_COD
    ) VALUES (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT
                ID_PARAMETRO
            FROM
                DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE
                NOMBRE_PARAMETRO = 'PARAMETROS_TELCOCRM'
                AND ESTADO = 'Activo'
        ),
        'CORREO_POR_LINEA_NEGOCIO',
        'connectivity',
        'Connectivity@telconet.ec',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        EMPRESA_COD
    ) VALUES (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT
                ID_PARAMETRO
            FROM
                DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE
                NOMBRE_PARAMETRO = 'PARAMETROS_TELCOCRM'
                AND ESTADO = 'Activo'
        ),
        'CORREO_POR_LINEA_NEGOCIO',
        'cloud',
        'cloud@telconet.ec',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        EMPRESA_COD
    ) VALUES (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT
                ID_PARAMETRO
            FROM
                DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE
                NOMBRE_PARAMETRO = 'PARAMETROS_TELCOCRM'
                AND ESTADO = 'Activo'
        ),
        'CORREO_POR_LINEA_NEGOCIO',
        'security',
        'security@telconet.ec',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        EMPRESA_COD
    ) VALUES (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT
                ID_PARAMETRO
            FROM
                DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE
                NOMBRE_PARAMETRO = 'PARAMETROS_TELCOCRM'
                AND ESTADO = 'Activo'
        ),
        'CORREO_POR_LINEA_NEGOCIO',
        'network',
        'network@telconet.ec',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        EMPRESA_COD
    ) VALUES (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT
                ID_PARAMETRO
            FROM
                DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE
                NOMBRE_PARAMETRO = 'PARAMETROS_TELCOCRM'
                AND ESTADO = 'Activo'
        ),
        'CORREO_POR_LINEA_NEGOCIO',
        'collaboration',
        'collaboration@telconet.ec',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        EMPRESA_COD
    ) VALUES (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT
                ID_PARAMETRO
            FROM
                DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE
                NOMBRE_PARAMETRO = 'PARAMETROS_TELCOCRM'
                AND ESTADO = 'Activo'
        ),
        'CORREO_POR_LINEA_NEGOCIO',
        'security',
        'Seguridad.electronica@telconet.ec',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
    );
    COMMIT;
    /