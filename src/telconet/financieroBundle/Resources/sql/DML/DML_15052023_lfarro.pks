/**
 * Documentación para crear parámetros
 * Parámetros de creación en DB_GENERAL.ADMI_PARAMETRO_CAB 
 * y DB_GENERAL.ADMI_PARAMATRO_DET, para validar en qué e,presa
 *
 * @author Arcángel Farro <lfarro@telconet.ec>
 * @version 1.0 15-05-2023
 */

 --INSERT PARAMETRO PARA CODIGO DE EMPRESA

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
        'EMPRESA_COD_VALIDACION_EST_CTA_PTO',
        'EMPRESA_COD_VALIDACION_EST_CTA_PTO',
        'FINANCIERO',
        'Activo',
        'lfarro',
        SYSDATE,
        '127.0.0.1'
);

-- INGRESO LOS DETALLES DE LA CABECERA 'DISTINCION DE EMPRESA'
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
    EMPRESA_COD,
    VALOR7
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'EMPRESA_COD_VALIDACION_EST_CTA_PTO'
            AND ESTADO = 'Activo'
        ),
        'MEGADATOS',
        'MD',
        NULL,
        NULL,
	NULL,
        'Activo',
        'lfarro',
        SYSDATE,
        '127.0.0.1',
        '18',
        NULL
);

-- INGRESO LOS DETALLES DE LA CABECERA 'DISTINCION DE EMPRESA'
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
    EMPRESA_COD,
    VALOR7
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'EMPRESA_COD_VALIDACION_EST_CTA_PTO'
            AND ESTADO = 'Activo'
        ),
        'ECUANET',
        'EN',
        NULL,
        NULL,
	NULL,
        'Activo',
        'lfarro',
        SYSDATE,
        '127.0.0.1',
        '33',
        NULL
);