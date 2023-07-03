
    /*
    * Se realiza la inserción de parámetros para la creación de proyectos de TelcoS+ a TelcoCRM
    * @author Kevin Baque Puya <kbaque@telconet.ec>
    * @version 1.0 11-11-2020
    */

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB (
        ID_PARAMETRO,
        NOMBRE_PARAMETRO,
        DESCRIPCION,
        MODULO,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
    ) VALUES (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
        'PARAMETROS_SOLICITUD_REACTIVACION',
        'PARAMETROS AUXILIARES A SOLICITUD DE REACTIVACION',
        'COMERCIAL',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1'
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
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
    ) VALUES (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT
                ID_PARAMETRO
            FROM
                DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE
                NOMBRE_PARAMETRO = 'PARAMETROS_SOLICITUD_REACTIVACION'
                AND ESTADO = 'Activo'
        ),
        'LISTADO_USUARIOS',
        'ikrochin',
        'Igor Krochin Lapentty',
        'ikrochin@telconet.ec',
        'Asignada',
        'si',
        'USUARIO_GESTION_SOLICITUD',
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
        VALOR3,
        VALOR4,
        VALOR5,
        VALOR6,
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
                NOMBRE_PARAMETRO = 'PARAMETROS_SOLICITUD_REACTIVACION'
                AND ESTADO = 'Activo'
        ),
        'LISTADO_USUARIOS',
        'jgalarza',
        'Javier Alfredo Galarza Benites',
        'jgalarza@telconet.ec',
        'Asignada',
        'si',
        'USUARIO_GESTION_SOLICITUD',
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
        VALOR3,
        VALOR4,
        VALOR5,
        VALOR6,
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
                NOMBRE_PARAMETRO = 'PARAMETROS_SOLICITUD_REACTIVACION'
                AND ESTADO = 'Activo'
        ),
        'LISTADO_USUARIOS',
        'mfranco',
        'Maria Elena Franco Pilalo',
        'jgalarza',
        'Pendiente',
        'no',
        'USUARIO_ASIGNA_SOLICITUD',
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
        VALOR3,
        VALOR4,
        VALOR5,
        VALOR6,
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
                NOMBRE_PARAMETRO = 'PARAMETROS_SOLICITUD_REACTIVACION'
                AND ESTADO = 'Activo'
        ),
        'LISTADO_USUARIOS',
        'imolina',
        'Irene Elizabeth Molina Chavez',
        'ikrochin',
        'Pendiente',
        'no',
        'USUARIO_ASIGNA_SOLICITUD',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
    (
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
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT
                ID_PARAMETRO
            FROM
                DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE
                NOMBRE_PARAMETRO = 'PARAMETROS_SOLICITUD_REACTIVACION'
                AND ESTADO = 'Activo'
        ),
        'TAREA_PROCESO',
        'TAREAS DE COBRANZAS - GESTIÓN COBRANZAS',
        'REACTIVACION CUENTA CANCELADA CON FLUJO',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
    );

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
    (
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
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT
                ID_PARAMETRO
            FROM
                DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE
                NOMBRE_PARAMETRO = 'PARAMETROS_SOLICITUD_REACTIVACION'
                AND ESTADO = 'Activo'
        ),
        'TAREA_PROCESO_INFORMATIVO',
        'TAREAS DE COBRANZAS - GESTIÓN COBRANZAS',
        'REACTIVACION CUENTA CANCELADA SIN FLUJO',
        'Activo',
        'kbaque',
        SYSDATE,
        '127.0.0.1',
        10
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
        'SOLICITUD DE REACTIVACION',
        SYSDATE,
        'kbaque',
        SYSDATE,
        'kbaque',
        'Activo',
        NULL,
        NULL,
        NULL
    );

    INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
        ID_CARACTERISTICA,
        DESCRIPCION_CARACTERISTICA,
        TIPO_INGRESO,
        ESTADO,
        FE_CREACION,
        USR_CREACION,
        TIPO
    ) VALUES (
        DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
        'REFERENCIA_CLIENTE',
        'N',
        'Activo',
        SYSDATE,
        'kbaque',
        'TECNICA'
    );

    INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
        ID_CARACTERISTICA,
        DESCRIPCION_CARACTERISTICA,
        TIPO_INGRESO,
        ESTADO,
        FE_CREACION,
        USR_CREACION,
        TIPO
    ) VALUES (
        DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
        'REFERENCIA_USUARIO',
        'N',
        'Activo',
        SYSDATE,
        'kbaque',
        'TECNICA'
    );

    INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
        ID_CARACTERISTICA,
        DESCRIPCION_CARACTERISTICA,
        TIPO_INGRESO,
        ESTADO,
        FE_CREACION,
        USR_CREACION,
        TIPO
    ) VALUES (
        DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
        'REFERENCIA_USUARIO_COBRANZA',
        'N',
        'Activo',
        SYSDATE,
        'kbaque',
        'TECNICA'
    );

    INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
        ID_CARACTERISTICA,
        DESCRIPCION_CARACTERISTICA,
        TIPO_INGRESO,
        ESTADO,
        FE_CREACION,
        USR_CREACION,
        TIPO
    ) VALUES (
        DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
        'REFERENCIA_TAREA',
        'N',
        'Activo',
        SYSDATE,
        'kbaque',
        'TECNICA'
    );

    INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
        ID_CARACTERISTICA,
        DESCRIPCION_CARACTERISTICA,
        TIPO_INGRESO,
        ESTADO,
        FE_CREACION,
        USR_CREACION,
        TIPO
    ) VALUES (
        DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
        'REFERENCIA_SALDO_P',
        'N',
        'Activo',
        SYSDATE,
        'kbaque',
        'TECNICA'
    );

    INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
        ID_CARACTERISTICA,
        DESCRIPCION_CARACTERISTICA,
        TIPO_INGRESO,
        ESTADO,
        FE_CREACION,
        USR_CREACION,
        TIPO
    ) VALUES (
        DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
        'REFERENCIA_SALDO_R',
        'N',
        'Activo',
        SYSDATE,
        'kbaque',
        'TECNICA'
    );

    INSERT INTO DB_GENERAL.ADMI_MOTIVO (
        ID_MOTIVO,
        RELACION_SISTEMA_ID,
        NOMBRE_MOTIVO,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        USR_ULT_MOD,
        FE_ULT_MOD,
        CTA_CONTABLE
    ) VALUES (
        DB_GENERAL.SEQ_ADMI_MOTIVO.NEXTVAL,
        (
            SELECT
                ID_RELACION_SISTEMA
            FROM
                DB_SEGURIDAD.SEGU_RELACION_SISTEMA
            WHERE
                MODULO_ID = (
                    SELECT
                        ID_MODULO
                    FROM
                        DB_SEGURIDAD.SIST_MODULO
                    WHERE
                        NOMBRE_MODULO = 'admiSolicitudReactivacion'
                )
                AND ACCION_ID = (
                    SELECT
                        ID_ACCION
                    FROM
                        DB_SEGURIDAD.SIST_ACCION
                    WHERE
                        NOMBRE_ACCION = 'index'
                )
        ),
        'PENDIENTE REGULARIZACION COMERCIAL',
        'Activo',
        'kbaque',
        SYSDATE,
        'kbaque',
        SYSDATE,
        NULL
    );

    INSERT INTO DB_GENERAL.ADMI_MOTIVO (
        ID_MOTIVO,
        RELACION_SISTEMA_ID,
        NOMBRE_MOTIVO,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        USR_ULT_MOD,
        FE_ULT_MOD,
        CTA_CONTABLE
    ) VALUES (
        DB_GENERAL.SEQ_ADMI_MOTIVO.NEXTVAL,
        (
            SELECT
                ID_RELACION_SISTEMA
            FROM
                DB_SEGURIDAD.SEGU_RELACION_SISTEMA
            WHERE
                MODULO_ID = (
                    SELECT
                        ID_MODULO
                    FROM
                        DB_SEGURIDAD.SIST_MODULO
                    WHERE
                        NOMBRE_MODULO = 'admiSolicitudReactivacion'
                )
                AND ACCION_ID = (
                    SELECT
                        ID_ACCION
                    FROM
                        DB_SEGURIDAD.SIST_ACCION
                    WHERE
                        NOMBRE_ACCION = 'index'
                )
        ),
        'EN GESTION LEGAL',
        'Activo',
        'kbaque',
        SYSDATE,
        'kbaque',
        SYSDATE,
        NULL
    );

    INSERT INTO DB_GENERAL.ADMI_MOTIVO (
        ID_MOTIVO,
        RELACION_SISTEMA_ID,
        NOMBRE_MOTIVO,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        USR_ULT_MOD,
        FE_ULT_MOD,
        CTA_CONTABLE
    ) VALUES (
        DB_GENERAL.SEQ_ADMI_MOTIVO.NEXTVAL,
        (
            SELECT
                ID_RELACION_SISTEMA
            FROM
                DB_SEGURIDAD.SEGU_RELACION_SISTEMA
            WHERE
                MODULO_ID = (
                    SELECT
                        ID_MODULO
                    FROM
                        DB_SEGURIDAD.SIST_MODULO
                    WHERE
                        NOMBRE_MODULO = 'admiSolicitudReactivacion'
                )
                AND ACCION_ID = (
                    SELECT
                        ID_ACCION
                    FROM
                        DB_SEGURIDAD.SIST_ACCION
                    WHERE
                        NOMBRE_ACCION = 'index'
                )
        ),
        'FALTA DE ACUERDO CON EL CLIENTE',
        'Activo',
        'kbaque',
        SYSDATE,
        'kbaque',
        SYSDATE,
        NULL
    );

    COMMIT;
    /