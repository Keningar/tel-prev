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
    'DIAS_ESPERA_FACTIBILIDAD_MANUAL',
    'DIAS DE ESPERA PARA UTILIZAR LA FECHA DEL SERVICIO EN FACTIBILIDAD MANUAL',
    'COMERCIAL',
    'Activo',
    'epin',
     SYSDATE,
    '127.0.0.1'
  );

/* DB_GENERAL.ADMI_PARAMETRO_DET */

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION
  )
  VALUES
  (
     DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
     (SELECT ID_PARAMETRO        FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO   = 'DIAS_ESPERA_FACTIBILIDAD_MANUAL' AND ESTADO      = 'Activo'),
     'Dias de espera',
     '15',
    'Activo',
    'gvalenzuela',
     SYSDATE,
    '127.0.0.1'
  );

COMMIT;
/