--=======================================================================
--   Se crea marca "RINORACK" utilizado para rack de DC en
--   la administración de Racks.
--=======================================================================

INSERT
INTO DB_INFRAESTRUCTURA.ADMI_MARCA_ELEMENTO
  (
    ID_MARCA_ELEMENTO,
    NOMBRE_MARCA_ELEMENTO,
    DESCRIPCION_MARCA_ELEMENTO,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD
  )
  VALUES
  (
    DB_INFRAESTRUCTURA.seq_ADMI_MARCA_ELEMENTO.nextval,
    'RINORACK',
    'RINORACK',
    'Activo',
    'kyrodriguez',
   sysdate,
    NULL,
    NULL
  );

--=======================================================================
--   Se crea marca "SIEMON" utilizado para rack de DC en
--   la administración de Racks.
--=======================================================================

INSERT
INTO DB_INFRAESTRUCTURA.ADMI_MARCA_ELEMENTO
  (
    ID_MARCA_ELEMENTO,
    NOMBRE_MARCA_ELEMENTO,
    DESCRIPCION_MARCA_ELEMENTO,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD
  )
  VALUES
  (
    DB_INFRAESTRUCTURA.seq_ADMI_MARCA_ELEMENTO.nextval,
    'SIEMON',
    'SIEMON',
    'Activo',
    'kyrodriguez',
    sysdate,
    NULL,
    NULL
  );

commit;
/