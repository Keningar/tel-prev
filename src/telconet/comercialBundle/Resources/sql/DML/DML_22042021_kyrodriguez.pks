
/*Visibilidad de SOLICITUD PLANIFICACION por departamento y proceso y tipo orden*/
INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'VISUALIZACION DATOS POR DEPARTAMENTO'),
    'COORDINAR',
    'Ip Contact Center',
    'T',
    'SOLICITUD PLANIFICACION',
    NULL,
    'Activo',
    'kyrodriguez',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    18,
    NULL,
    NULL,
    'VALOR1: Departamento, VALOR2: Tipo de Orden, VALOR3: Tipo de solicitud'
  );

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'VISUALIZACION DATOS POR DEPARTAMENTO'),
    'COORDINAR',
    'Servicio Al Cliente',
    'T',
    'SOLICITUD PLANIFICACION',
    NULL,
    'Activo',
    'kyrodriguez',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    18,
    NULL,
    NULL,
    'VALOR1: Departamento, VALOR2: Tipo de Orden, VALOR3: Tipo de solicitud'
  );

COMMIT;

/
