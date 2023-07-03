-- Inserción del parámetro de URL, para utilizar el WebService de Proceso Masivo, con la opción de Reactivar servicios

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
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION
  ) 
  VALUES 
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO ='PROCESO_REACTIVAR_SERVICIOS'),
    'PARAMETROS DEL WEBSERVICES',
    'http://telcos-lb.telconet.ec/rs/tecnico/ws/rest/procesosMasivos',
    'ReactivarServiciosPuntos',
    'application/json',
    'UTF-8',
    'Activo',
    'hlozano',
    SYSDATE,
    '127.0.0.1',
    'hlozano',
    SYSDATE,
    '127.0.0.1',
    null,
    '10',
    null,
    null,
    null
  );

COMMIT;

/