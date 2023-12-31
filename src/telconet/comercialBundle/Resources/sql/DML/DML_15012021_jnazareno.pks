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
VALUES (
     DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
     (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'CONFIGURACION_WS_SD' AND ESTADO = 'Activo'), 
     'BANDERA_NUEVO_CONSUMO_WS', 
     'S',
     null, 
     NULL, 
     NULL, 
     'Activo', 
     'jnazareno', 
     SYSDATE, 
     '127.0.0.1', 
     NULL, 
     NULL, 
     NULL, 
     NULL, 
     '18', 
     NULL, 
     NULL, 
     '');

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
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'CONFIGURACION_WS_SD' and ESTADO = 'Activo'),
    'PARAMSQUERY_NEW', 
    'S3curity', 
    'http://172.24.25.171:8080/CertificadoElectronicoClienteMegadatos_V1/webresources/usuario_pn/emision_pn',
    'p12', 
    '/archivo_cert', 
    'Activo', 
    'jnazareno', 
    sysdate, 
    '127.0.0.1', 
    NULL,
    NULL, 
    NULL, 
    '172.24.25.171', 
    '18', 
    'archivo_cert', 
    'rch#PFX2015', 
    NULL);

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
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'CONFIGURACION_WS_SD' and ESTADO = 'Activo'),
    'PARAMSQUERYJUR_NEW', 
    'S3curity', 
    'http://172.24.25.171:8080/CertificadoElectronicoClienteMegadatos_V1/webresources/usuario_pn/emision_pn',
    'p12', 
    '/archivo_cert', 
    'Activo', 
    'jnazareno', 
    sysdate, 
    '127.0.0.1', 
    NULL,
    NULL, 
    NULL, 
    '172.24.25.171', 
    '18', 
    'archivo_cert', 
    'rch#PFX2015', 
    NULL);
      
COMMIT;