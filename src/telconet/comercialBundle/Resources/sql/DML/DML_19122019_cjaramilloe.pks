
/*
* Se realiza la inserción de características y parámetros para cambio masivo de vendedor
* @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
* @version 1.0 19-12-2019
*/

INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
        ID_CARACTERISTICA,
        DESCRIPCION_CARACTERISTICA,
        TIPO_INGRESO,
        ESTADO,
        FE_CREACION,
        USR_CREACION,
        FE_ULT_MOD,
        USR_ULT_MOD,
        TIPO)
VALUES (
        DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
        'SOLICITUD_CAMBIO_MASIVO_VENDEDOR_ORIGEN',
        'T',
        'Activo',
        SYSDATE,
        'cjaramilloe',
        SYSDATE,
        'cjaramilloe',
        'COMERCIAL') ;


INSERT INTO DB_COMERCIAL.ADMI_TIPO_SOLICITUD (
    ID_TIPO_SOLICITUD,
    DESCRIPCION_SOLICITUD,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    ESTADO,
    TAREA_ID,
    ITEM_MENU_ID,
    PROCESO_ID)
VALUES (
    DB_COMERCIAL.SEQ_ADMI_TIPO_SOLICITUD.NEXTVAL,
    'SOLICITUD CAMBIO MASIVO CLIENTES VENDEDOR',
    SYSDATE,
    'cjaramilloe',
    SYSDATE,
    'cjaramilloe',
    'Activo',
    NULL,
    NULL,
    NULL);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
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
VALUES(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    652,
    'Solicitud Cambio Masivo Clientes Vendedor',
    'ROLE_442-1',
    '159',
    NULL,
    NULL,
    'Activo',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    'cjaramilloe',
    SYSDATE,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL);

COMMIT;

/