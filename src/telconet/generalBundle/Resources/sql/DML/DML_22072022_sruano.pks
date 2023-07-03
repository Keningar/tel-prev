/**
 * Documentación para crear parámetros
 * Parámetros de creación en DB_GENERAL.ADMI_PARAMETRO_CAB 
 * y DB_GENERAL.ADMI_PARAMATRO_DET.
 *
 * @author Steven Ruano <sruano@telconet.ec>
 * @version 1.0 21-11-2022
 *
 */

SET SERVEROUTPUT ON

--INSERT PARAMETRO PARA FACTIBILIDAD_NUEVO_ALGORITMO
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
        'FACTIBILIDAD_NUEVO_ALGORITMO',
        'FACTIBILIDAD_NUEVO_ALGORITMO',
        'TECNICO',
        'Activo',
        'afayala',
        SYSDATE,
        '127.0.0.1'
);

-- INGRESO LOS DETALLES DE LA CABECERA 'FACTIBILIDAD_NUEVO_ALGORITMO'
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
            WHERE NOMBRE_PARAMETRO = 'FACTIBILIDAD_NUEVO_ALGORITMO'
            AND ESTADO = 'Activo'
        ),
        'Bandera para realizar proceso nuevo de Factibilidad',
	'BANDERA_NUEVO_ALGORITMO_FACTIBILIDAD',
        'SI',
        NULL,
	NULL,
        'Activo',
        'afayala',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL
);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'VALIDACION_FACTIBILIDAD_NUEVO_ALGORITMO',
    'PARAMETRO QUE CONTIENE VARIABLES PERMITIDAS PARA EL FLUJO DE NUEVA FACTIBILIDAD.',
    'TECNICO',
    null,
    'Activo',
    'afayala',
    sysdate,
    '127.0.0.1',
    'afayala',
    sysdate,
    '127.0.0.1'
);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'VALIDACION_FACTIBILIDAD_NUEVO_ALGORITMO'
            AND ESTADO = 'Activo'
    ),
    'VALIDACION_FACTIBILIDAD_NUEVO_ALGORITMO',
    'TODAS',
    '["GUAYAQUIL"]',
    'TODOS',
    '["MD"]',
    'Activo',
    'afayala',
    sysdate,
    '127.0.0.1',
    'afayala',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    null,
    null,
    null
);

--INSERT PARAMETRO PARA URL_CALCULO_DISTANCIA
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
        'URL_CALCULO_DISTANCIA',
        'URL_CALCULO_DISTANCIA',
        'TECNICO',
        'Activo',
        'sruano',
        SYSDATE,
        '127.0.0.1'
);

-- INGRESO LOS DETALLES DE LA CABECERA 'URL_CALCULO_DISTANCIA'
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
            WHERE NOMBRE_PARAMETRO = 'URL_CALCULO_DISTANCIA'
            AND ESTADO = 'Activo'
        ),
        'URL para calculo de distancia',
        'http://telcos-ws-ext-lb.telconet.ec/gis/arcgis/rest/services/Nacional/calculoruta/NAServer/Route/solve?f=json&',
        'directionsLanguage=es&',
        'stops=',
        NULL,
        'Activo',
        'sruano',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL
);

COMMIT;
/
