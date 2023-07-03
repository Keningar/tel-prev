/**
 * Documentación para crear parámetros
 * Parámetros de creación en DB_GENERAL.ADMI_PARAMETRO_CAB 
 * y DB_GENERAL.ADMI_PARAMATRO_DET.
 *
 * @author Arcángel Farro <lfarro@telconet.ec>
 * @version 1.0 29-12-2022
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
        'PE_DATACENTER',
        'PE_DATACENTER',
        'PLANIFICACION',
        'Activo',
        'lfarro',
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
            WHERE NOMBRE_PARAMETRO = 'PE_DATACENTER'
            AND ESTADO = 'Activo'
        ),
        'PE_VLANS',
        'pe3asrgyedc.telconet.net',
        NULL,
        NULL,
	NULL,
        'Activo',
        'lfarro',
        SYSDATE,
        '127.0.0.1',
        '10',
        NULL
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
    EMPRESA_COD,
    VALOR7
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'PE_DATACENTER'
            AND ESTADO = 'Activo'
        ),
        'PE_VLANS',
        'pe1asruiodc.telconet.net',
        NULL,
        NULL,
	NULL,
        'Activo',
        'lfarro',
        SYSDATE,
        '127.0.0.1',
        '10',
        NULL
);

COMMIT;
/

