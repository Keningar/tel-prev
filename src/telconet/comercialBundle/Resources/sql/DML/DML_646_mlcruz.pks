INSERT
INTO DB_COMERCIAL.INFO_PERSONA
  (
    ID_PERSONA,
    NOMBRES,
    LOGIN,
    ESTADO,
    USR_CREACION,
    FE_CREACION
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_INFO_PERSONA.NEXTVAL,
    'MIGRACION NODOS',
    'migracion_nodos',
    'Activo',
    'mlcruz',
    SYSDATE
  );
COMMIT;