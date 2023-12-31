--CREACIÓN DE LA CARACTERISTICA RESPONSABLE_ZONA
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    FE_CREACION,
    USR_CREACION,
    TIPO,
    ESTADO
) VALUES (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'RESPONSABLE_ZONA',
    'T',
     SYSDATE,
    'gvalenzuela',
    'ADMINISTRACION',
    'Activo'
);

/* PARAMETROS */
/* CREACIÓN DEL PARÁMETRO CAB  - REPROGRAMAR_DEPARTAMENTO_HAL*/
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
    'REPROGRAMAR_DEPARTAMENTO_HAL',
    'HABILITACION DEL BOTON REPROGRAMAR PARA LOS DEPARTAMENTOS CONFIGURADOS SI LA TAREA ES HAL',
    'SOPORTE',
    'Activo',
    'gvalenzuela',
     SYSDATE,
    '127.0.0.1'
  );

/* DB_GENERAL.ADMI_PARAMETRO_DET */
/* INSERT DEL DEPARTAMENTO QUE PODRÁ VER EL BOTÓN REPROGRAMAR*/
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
     (SELECT ID_PARAMETRO        FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO   = 'REPROGRAMAR_DEPARTAMENTO_HAL' AND ESTADO      = 'Activo'),
     (SELECT NOMBRE_DEPARTAMENTO FROM DB_GENERAL.ADMI_DEPARTAMENTO WHERE NOMBRE_DEPARTAMENTO = 'Ip Contact Center' AND EMPRESA_COD = 18 AND ESTADO = 'Activo'),
     (SELECT ID_DEPARTAMENTO     FROM DB_GENERAL.ADMI_DEPARTAMENTO WHERE NOMBRE_DEPARTAMENTO = 'Ip Contact Center' AND EMPRESA_COD = 18 AND ESTADO = 'Activo'),
    'Activo',
    'gvalenzuela',
     SYSDATE,
    '127.0.0.1'
  );

COMMIT;
/
