--=======================================================================
--   Se crea modelo de interface de tipo UNIDADES DE RACK   
--   para los racks RACK 42 U de la marca "APC". El mismo será usado 
--   para la creación de racks.
--=======================================================================

INSERT
INTO DB_INFRAESTRUCTURA.ADMI_INTERFACE_MODELO
  (
    ID_INTERFACE_MODELO,
    MODELO_ELEMENTO_ID,
    TIPO_INTERFACE_ID,
    CANTIDAD_INTERFACE,
    CLASE_INTERFACE,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    FORMATO_INTERFACE
  )
  VALUES
  (
    DB_INFRAESTRUCTURA.SEQ_ADMI_INTERFACE_MODELO.nextval,
    (select id_modelo_elemento
    from DB_INFRAESTRUCTURA.admi_modelo_ELEMENTO
    join DB_INFRAESTRUCTURA.admi_marca_ELEMENTO
    on id_marca_elemento=marca_elemento_id
    where NOMBRE_MARCA_ELEMENTO='APC' and tipo_elemento_id=227)
    ,243
    ,42,
    'Standar',
    'Activo',
    'kyrodriguez',
    SYSDATE,
    'kyrodriguez',
    SYSDATE,
    'U'
  );

--=======================================================================
--   Se crea modelo de interface de tipo UNIDADES DE RACK   
--   para los racks RACK 42 U de la marca "RINORACK". El mismo será usado 
--   para la creación de racks.
--=======================================================================

INSERT
INTO DB_INFRAESTRUCTURA.ADMI_INTERFACE_MODELO
  (
    ID_INTERFACE_MODELO,
    MODELO_ELEMENTO_ID,
    TIPO_INTERFACE_ID,
    CANTIDAD_INTERFACE,
    CLASE_INTERFACE,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    FORMATO_INTERFACE
  )
  VALUES
  (
    DB_INFRAESTRUCTURA.SEQ_ADMI_INTERFACE_MODELO.nextval,
    (select id_modelo_elemento
    from DB_INFRAESTRUCTURA.admi_modelo_ELEMENTO
    join DB_INFRAESTRUCTURA.admi_marca_ELEMENTO
    on id_marca_elemento=marca_elemento_id
    where NOMBRE_MARCA_ELEMENTO='RINORACK' and tipo_elemento_id=227),
    243,
    42,
    'Standar',
    'Activo',
    'kyrodriguez',
    SYSDATE,
    'kyrodriguez',
    SYSDATE,
    'U'
  );
  
--=======================================================================
--   Se crea modelo de interface de tipo UNIDADES DE RACK   
--   para los racks RACK 42 U de la marca "SIEMON". El mismo será usado 
--   para la creación de racks.
--=======================================================================
 
INSERT
INTO DB_INFRAESTRUCTURA.ADMI_INTERFACE_MODELO
  (
    ID_INTERFACE_MODELO,
    MODELO_ELEMENTO_ID,
    TIPO_INTERFACE_ID,
    CANTIDAD_INTERFACE,
    CLASE_INTERFACE,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    FORMATO_INTERFACE
  )
  VALUES
  (
    DB_INFRAESTRUCTURA.SEQ_ADMI_INTERFACE_MODELO.nextval,
    (select id_modelo_elemento
    from DB_INFRAESTRUCTURA.admi_modelo_ELEMENTO
    join DB_INFRAESTRUCTURA.admi_marca_ELEMENTO
    on id_marca_elemento=marca_elemento_id
    where NOMBRE_MARCA_ELEMENTO='SIEMON' and tipo_elemento_id=227)
    ,243
    ,42,
    'Standar',
    'Activo',
    'kyrodriguez',
    SYSDATE,
    'kyrodriguez',
    SYSDATE,
    'U'
  );

commit;
/