/**
 * Documentación INSERTS ADMI_PARAMETRO_CAB, ADMI_PARAMETRO_DET
 *
 * Nuevos parámetros para homologación de formas de pago TM Comercial.
 *
 * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
 * @version 1.0 28-01-2020
 */

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB (
    ID_PARAMETRO,
    NOMBRE_PARAMETRO,
    DESCRIPCION,
    MODULO,
    PROCESO,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD)
VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'HOMOLOGACION_FORMAS_DE_PAGO',
    'Homologación de formas de pago usadas en app móvil TM Comercial',
    'COMERCIAL',
    'CONTRATO_DIGITAL',
    'Activo',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1');

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
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
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION)
VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    ( SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'HOMOLOGACION_FORMAS_DE_PAGO' ),
    'Categoria EFECTIVO',
    '1',
    '1',
    'NO',
    '3',
    'Activo',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    NULL,
    18,
    NULL,
    NULL,
    'VALOR1: ID_FORMA_PAGO del contrato, VALOR2: ID_FORMA_PAGO homologados concatenado con |, VALOR3: Se valida o no esta categoría de forma de pago, VALOR4: Cantidad de ultimas facturas a revisar');

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
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
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION)
VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    ( SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'HOMOLOGACION_FORMAS_DE_PAGO' ),
    'Categoria DEBITO BANCARIO',
    '3',
    '3',
    'SI',
    '3',
    'Activo',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    NULL,
    18,
    NULL,
    NULL,
    'VALOR1: ID_FORMA_PAGO del contrato, VALOR2: ID_FORMA_PAGO homologados concatenado con |, VALOR3: Se valida o no esta categoría de forma de pago, VALOR4: Cantidad de ultimas facturas a revisar');

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
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
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION)
VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    ( SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'HOMOLOGACION_FORMAS_DE_PAGO'),
    'Categoria TARJETA CREDITO',
    '10',
    '10',
    'SI',
    '3',
    'Activo',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    NULL,
    18,
    NULL,
    NULL,
    'VALOR1: ID_FORMA_PAGO del contrato, VALOR2: ID_FORMA_PAGO homologados concatenado con |, VALOR3: Se valida o no esta categoría de forma de pago, VALOR4: Cantidad de ultimas facturas a revisar');
    
COMMIT;
/