--=======================================================================
-- Ingreso de la cabecera de parámetro 'VALORES_VRF_TELCONET' que van reemplazar las VRF 
-- por los valores de los detalles de parámetros
--=======================================================================

-- INGRESO DE LA CABECERA DE PARAMETROS 'VALORES_VRF_TELCONET'
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
        'VALORES_VRF_TELCONET', 
        'Valores de las VRF que son reemplazadas por lo detalles de la cabecera', 
        'Activo', 
        'facaicedo', 
        SYSDATE, 
        '127.0.0.1' 
);
-- INGRESO LOS DETALLES DE LA CABECERA DE PARAMETROS 'VALORES_VRF_TELCONET'
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
            WHERE NOMBRE_PARAMETRO = 'VALORES_VRF_TELCONET' 
            AND ESTADO = 'Activo'
        ), 
        'LISTA VALORES', 
        'Telconet', 
        'telconet', 
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
-- INGRESO LOS DETALLES DE LA CABECERA DE PARAMETROS 'VALORES_VRF_TELCONET'
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
            WHERE NOMBRE_PARAMETRO = 'VALORES_VRF_TELCONET' 
            AND ESTADO = 'Activo'
        ), 
        'LISTA VALORES', 
        'telconet_3', 
        'telconet', 
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
