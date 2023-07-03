--=======================================================================
-- Ingreso de parametros para la validación de los estados de las interfaces no disponibles o ocupadas ya por servicios
--=======================================================================

-- INGRESO DE LA CABECERA DE PARAMETROS DE 'ESTADOS_INTERFACES_NO_DISPONIBLES'
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB 
( 
        ID_PARAMETRO, 
        NOMBRE_PARAMETRO, 
        DESCRIPCION, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION 
)
VALUES
( 
        DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL, 
        'ESTADOS_INTERFACES_NO_DISPONIBLES', 
        'Estados de las interfaces no disponibles o ocupadas ya por servicios', 
        'Activo', 
        'facaicedo', 
        SYSDATE, 
        '127.0.0.1' 
);
-- INGRESO LOS DETALLES DE LA CABECERA 'ESTADOS_INTERFACES_NO_DISPONIBLES'
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
            WHERE NOMBRE_PARAMETRO = 'ESTADOS_INTERFACES_NO_DISPONIBLES' 
            AND ESTADO = 'Activo'
        ), 
        'LISTA VALORES', 
        'connected', 
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
            WHERE NOMBRE_PARAMETRO = 'ESTADOS_INTERFACES_NO_DISPONIBLES' 
            AND ESTADO = 'Activo'
        ), 
        'LISTA VALORES', 
        'reserved', 
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
            WHERE NOMBRE_PARAMETRO = 'ESTADOS_INTERFACES_NO_DISPONIBLES' 
            AND ESTADO = 'Activo'
        ), 
        'LISTA VALORES', 
        'Ocupado', 
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
            WHERE NOMBRE_PARAMETRO = 'ESTADOS_INTERFACES_NO_DISPONIBLES' 
            AND ESTADO = 'Activo'
        ), 
        'LISTA VALORES', 
        'Factible', 
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
