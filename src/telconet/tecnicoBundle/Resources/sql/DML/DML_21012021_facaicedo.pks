--=======================================================================
-- Ingreso la api para generar las tareas internas
-- Ingreso el tipo de solicitud RPA cancelación licencia
-- Ingreso los detalles de parámetros de los id de las marcas de los elementos de los productos para la cancelación del servicio con licencias Fortigate
--=======================================================================

-- INGRESO LA API PARA LA GENERACION DE LA TAREA INTERNA
INSERT INTO DB_TOKENSECURITY.APPLICATION (ID_APPLICATION, NAME, STATUS, EXPIRED_TIME) VALUES
(DB_TOKENSECURITY.SEQ_APPLICATION.NEXTVAL, 'TELCOS_RPA', 'ACTIVO', '30');
INSERT INTO DB_TOKENSECURITY.USER_TOKEN ( ID_USER_TOKEN, USERNAME, PASSWORD, ESTADO, APPLICATION_ID ) VALUES
(DB_TOKENSECURITY.SEQ_USER_TOKEN.NEXTVAL, 'rpaLicencia', '38083C7EE9121E17401883566A148AA5C2E2D55DC53BC4A94A026517DBFF3C6B', 
'Activo', (SELECT ID_APPLICATION FROM DB_TOKENSECURITY.APPLICATION WHERE NAME='TELCOS_RPA'));
INSERT INTO DB_TOKENSECURITY.WEB_SERVICE (ID_WEB_SERVICE,SERVICE,METHOD,GENERATOR,STATUS,ID_APPLICATION) VALUES
(DB_TOKENSECURITY.SEQ_WEB_SERVICE.nextval,'SoporteWSController','procesarAction','1',
'ACTIVO',(SELECT ID_APPLICATION FROM DB_TOKENSECURITY.APPLICATION WHERE NAME = 'TELCOS_RPA'));

-- INGRESO EL TIPO DE SOLICITUD DE 'SOLICITUD RPA CANCELACION LICENCIA'
INSERT INTO DB_COMERCIAL.ADMI_TIPO_SOLICITUD
(
        ID_TIPO_SOLICITUD,
        DESCRIPCION_SOLICITUD,
        ESTADO,
        USR_CREACION,
        FE_CREACION
)
VALUES
(
        DB_COMERCIAL.SEQ_ADMI_TIPO_SOLICITUD.NEXTVAL,
        'SOLICITUD RPA CANCELACION LICENCIA',
        'Activo',
        'facaicedo',
        SYSDATE
);
-- INGRESO DE LA CABECERA DE PARAMETROS DE 'RPA_MARCA_ELEMENTOS_CANCELACION_LICENCIA'
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
        'RPA_MARCA_ELEMENTOS_CANCELACION_LICENCIA',
        'Lista de los id de las marcas de los elementos de los productos para la cancelación del servicio con licencias Fortigate',
        'TECNICO',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);
-- INGRESO LOS DETALLES DE LA CABECERA 'RPA_MARCA_ELEMENTOS_CANCELACION_LICENCIA'
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
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'RPA_MARCA_ELEMENTOS_CANCELACION_LICENCIA'
            AND ESTADO = 'Activo'
        ),
        'LISTA VALORES',
        '1074',
        '4645',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1',
        (
            SELECT COD_EMPRESA
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
            WHERE PREFIJO = 'TN'
        )
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
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'RPA_MARCA_ELEMENTOS_CANCELACION_LICENCIA'
            AND ESTADO = 'Activo'
        ),
        'LISTA VALORES',
        '1258',
        '4645',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1',
        (
            SELECT COD_EMPRESA
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
            WHERE PREFIJO = 'TN'
        )
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
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'RPA_MARCA_ELEMENTOS_CANCELACION_LICENCIA'
            AND ESTADO = 'Activo'
        ),
        'LISTA VALORES',
        '1246',
        '4645',
        'Activo',
        'facaicedo',
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
