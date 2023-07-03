--==========================================================================================
--========================= Insert a la tabla ADMI_PARAMETRO_CAB ======================
--==========================================================================================
INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_CAB
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
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.nextval,
    'NUMERO_SESSION_TNCLIENTE',
    'NÚMERO DE SESSIONES QUE PUEDE TENER UNA RAZON SOCIAL SIMULTANEAMENTE EN LA APP TN CLIENTE.',
    'SOPORTE',
    'Activo',
    'wgaibor',
    SYSDATE,
    '127.0.0.1'
  );

--==========================================================================================
--========================= Insert a la tabla ADMI_PARAMETRO_DET ======================
--==========================================================================================

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
    VALOR5
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT APD.ID_PARAMETRO
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB APD
    WHERE APD.NOMBRE_PARAMETRO = 'NUMERO_SESSION_TNCLIENTE'
    ),
    'NÚMERO DE SESSIONES QUE PUEDE TENER UNA RAZON SOCIAL SIMULTANEAMENTE EN LA APP TN CLIENTE.',
    '5',
    NULL,
    NULL,
    NULL,
    'Activo',
    'wgaibor',
    SYSDATE,
    '127.0.0.1',
    NULL
  );

COMMIT;