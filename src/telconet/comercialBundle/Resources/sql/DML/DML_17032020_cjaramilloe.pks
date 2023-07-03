/**
 * Documentación INSERTS ADMI_PARAMETRO_CAB, ADMI_PARAMETRO_DET
 *
 * Nuevo parámetro para estados de objetos en TM Comercial.
 *
 * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
 * @version 1.0 17-03-2020
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
    'ESTADOS_TM_COMERCIAL',
    'ESTADOS DE OBJETOS EN TM COMERCIAL',
    'COMERCIAL',
    'TM_COMERCIAL',
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
    (SELECT t.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB t WHERE t.NOMBRE_PARAMETRO = 'ESTADOS_TM_COMERCIAL' AND t.ESTADO = 'Activo'),
    'ESTADOS DEL PUNTO PARA CONSULTA DE DOCUMENTOS DIGITALES',
    'Activo,Inactivo,In-Corte,In-Temp,migracion_ttco,MigradoOnnet,Trasladado',
    NULL,
    NULL,
    NULL,
    'Activo',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    NULL,
    '18',
    NULL,
    NULL,
    NULL);
COMMIT;
/
