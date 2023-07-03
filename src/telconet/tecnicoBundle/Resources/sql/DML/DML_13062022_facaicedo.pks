
--ingreso de los detalles de parametros para el maximo de caracteres del login para los servicios safecity
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
            WHERE NOMBRE_PARAMETRO = 'NUEVA_RED_GPON_TN'
            AND ESTADO = 'Activo'
        ),
        'MAXIMO_CARACTERES_LOGIN',
        '26',
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

--INSERT CARACTERISTICA 'VRF_VIDEO_SAFECITY'
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA 
( 
        ID_CARACTERISTICA, 
        DESCRIPCION_CARACTERISTICA, 
        TIPO_INGRESO, 
        TIPO,
        ESTADO, 
        USR_CREACION, 
        FE_CREACION
)
VALUES
( 
        DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL, 
        'VRF_VIDEO_SAFECITY', 
        'N', 
        'TECNICO', 
        'Activo', 
        'facaicedo', 
        SYSDATE
);

COMMIT;
/
