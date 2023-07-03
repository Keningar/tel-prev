/**
 * Documentación para crear parámetros
 * Parámetros de creación en DB_GENERAL.ADMI_PARAMETRO_CAB 
 * y DB_GENERAL.ADMI_PARAMATRO_DET.
 *
 * @author Antonio Ayala <afayala@telconet.ec>
 * @version 1.0 09-12-2021
 */

SET SERVEROUTPUT ON

-- INGRESO DE LA CABECERA DE PARAMETROS DE 'TIPO_DE_CONTENIDO_CASSETTE'
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
        'TIPO_DE_CONTENIDO_CASSETTE',
        'Lista de los tipos de contenido para Cassette',
        'TECNICO',
        'Activo',
        'afayala',
        SYSDATE,
        '127.0.0.1'
);

-- INGRESO LOS DETALLES DE LA CABECERA 'TIPO_DE_CONTENIDO_CASSETTE'
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
        EMPRESA_COD
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'TIPO_DE_CONTENIDO_CASSETTE'
            AND ESTADO = 'Activo'
        ),
        'CASSETTE',
	'CAJA DISPERSION',
        NULL,
        NULL,
	NULL,
        'Activo',
        'afayala',
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
	VALOR3,
	VALOR4,
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
            WHERE NOMBRE_PARAMETRO = 'TIPO_DE_CONTENIDO_CASSETTE'
            AND ESTADO = 'Activo'
        ),
        'CASSETTE',
	'MANGA',
        NULL,
        NULL,
	NULL,
        'Activo',
        'afayala',
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
