--Creación de directorios para subida de archivos en opciones de consulta de elementos y acta de recepción
INSERT INTO DB_GENERAL.ADMI_GESTION_DIRECTORIOS
(
  ID_GESTION_DIRECTORIO,
  CODIGO_APP,
  CODIGO_PATH,
  APLICACION,
  PAIS,
  EMPRESA,
  MODULO,
  SUBMODULO,
  ESTADO,
  FE_CREACION,
  USR_CREACION
)
VALUES
(
  DB_GENERAL.SEQ_ADMI_GESTION_DIRECTORIOS.nextval,
  4,
  (SELECT MAX(CODIGO_PATH) +1 FROM DB_GENERAL.ADMI_GESTION_DIRECTORIOS WHERE CODIGO_APP=4 AND APLICACION='TelcosWeb'),
  'TelcosWeb',
  '593',
  'TN',
  'Tecnico',
  'AuditoriaElementos',
  'Activo',
  SYSDATE,
  'mlcruz'
);

INSERT INTO DB_GENERAL.ADMI_GESTION_DIRECTORIOS
(
  ID_GESTION_DIRECTORIO,
  CODIGO_APP,
  CODIGO_PATH,
  APLICACION,
  PAIS,
  EMPRESA,
  MODULO,
  SUBMODULO,
  ESTADO,
  FE_CREACION,
  USR_CREACION
)
VALUES
(
  DB_GENERAL.SEQ_ADMI_GESTION_DIRECTORIOS.nextval,
  4,
  (SELECT MAX(CODIGO_PATH) +1 FROM DB_GENERAL.ADMI_GESTION_DIRECTORIOS WHERE CODIGO_APP=4 AND APLICACION='TelcosWeb'),
  'TelcosWeb',
  '593',
  'MD',
  'Tecnico',
  'AuditoriaElementos',
  'Activo',
  SYSDATE,
  'mlcruz'
);

INSERT INTO DB_GENERAL.ADMI_GESTION_DIRECTORIOS
(
  ID_GESTION_DIRECTORIO,
  CODIGO_APP,
  CODIGO_PATH,
  APLICACION,
  PAIS,
  EMPRESA,
  MODULO,
  SUBMODULO,
  ESTADO,
  FE_CREACION,
  USR_CREACION
)
VALUES
(
  DB_GENERAL.SEQ_ADMI_GESTION_DIRECTORIOS.nextval,
  4,
  (SELECT MAX(CODIGO_PATH) +1 FROM DB_GENERAL.ADMI_GESTION_DIRECTORIOS WHERE CODIGO_APP=4 AND APLICACION='TelcosWeb'),
  'TelcosWeb',
  '593',
  'TN',
  'Tecnico',
  'ActasEntregas',
  'Activo',
  SYSDATE,
  'mlcruz'
);

INSERT INTO DB_GENERAL.ADMI_GESTION_DIRECTORIOS
(
  ID_GESTION_DIRECTORIO,
  CODIGO_APP,
  CODIGO_PATH,
  APLICACION,
  PAIS,
  EMPRESA,
  MODULO,
  SUBMODULO,
  ESTADO,
  FE_CREACION,
  USR_CREACION
)
VALUES
(
  DB_GENERAL.SEQ_ADMI_GESTION_DIRECTORIOS.nextval,
  4,
  (SELECT MAX(CODIGO_PATH) +1 FROM DB_GENERAL.ADMI_GESTION_DIRECTORIOS WHERE CODIGO_APP=4 AND APLICACION='TelcosWeb'),
  'TelcosWeb',
  '593',
  'MD',
  'Tecnico',
  'ActasEntregas',
  'Activo',
  SYSDATE,
  'mlcruz'
);

INSERT INTO DB_GENERAL.ADMI_GESTION_DIRECTORIOS
(
  ID_GESTION_DIRECTORIO,
  CODIGO_APP,
  CODIGO_PATH,
  APLICACION,
  PAIS,
  EMPRESA,
  MODULO,
  SUBMODULO,
  ESTADO,
  FE_CREACION,
  USR_CREACION
)
VALUES
(
  DB_GENERAL.SEQ_ADMI_GESTION_DIRECTORIOS.nextval,
  4,
  (SELECT MAX(CODIGO_PATH) +1 FROM DB_GENERAL.ADMI_GESTION_DIRECTORIOS WHERE CODIGO_APP=4 AND APLICACION='TelcosWeb'),
  'TelcosWeb',
  '593',
  'TN',
  'Tecnico',
  'ControlBwMasivo',
  'Activo',
  SYSDATE,
  'mlcruz'
);


COMMIT;
/