INSERT
INTO DB_GENERAL.ADMI_MOTIVO
  (
    ID_MOTIVO,
    RELACION_SISTEMA_ID,
    NOMBRE_MOTIVO,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    CTA_CONTABLE
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_MOTIVO.NEXTVAL,
    null,
    'Actualizacion Ciclo Facturacion',
    'Activo',
    'jguerrerop',
    sysdate,
    'jguerrerop',
    sysdate,
    NULL
  );
COMMIT;
