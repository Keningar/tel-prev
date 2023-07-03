INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'ULTIMAS MILLAS INTERNET Y DATOS'),
    'Concentrador Clientes DC',
    'Fibra Optica',
    'HOSTING',
    NULL,
    NULL,
    'Activo',
    'arsuarez',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    10
  );
  
INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'ULTIMAS MILLAS INTERNET Y DATOS'),
    'Concentrador Clientes DC',
    'UTP',
    'HOUSING',
    NULL,
    NULL,
    'Activo',
    'arsuarez',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    10
  );