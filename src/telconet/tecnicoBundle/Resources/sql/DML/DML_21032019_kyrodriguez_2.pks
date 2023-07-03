--=======================================================================
--   Se crea modelo RACK 42 U de la marca "RINORACK" utilizado para rack  
--   de DC en la administración de Racks.
--=======================================================================

INSERT
INTO DB_INFRAESTRUCTURA.ADMI_MODELO_ELEMENTO
  (
    ID_MODELO_ELEMENTO,
    MARCA_ELEMENTO_ID,
    TIPO_ELEMENTO_ID,
    NOMBRE_MODELO_ELEMENTO,
    DESCRIPCION_MODELO_ELEMENTO,
    MTTR,
    UNIDAD_MEDIDA_MTTR,
    MTBF,
    UNIDAD_MEDIDA_MTBF,
    ANCHO_MODELO,
    UNIDAD_MEDIDA_ANCHO,
    LARGO_MODELO,
    UNIDAD_MEDIDA_LARGO,
    ALTO_MODELO,
    UNIDAD_MEDIDA_ALTO,
    PESO_MODELO,
    UNIDAD_MEDIDA_PESO,
    U_RACK,
    CAPACIDAD_ENTRADA,
    UNIDAD_MEDIDA_ENTRADA,
    CAPACIDAD_SALIDA,
    UNIDAD_MEDIDA_SALIDA,
    CAPACIDAD_VA_FABRICA,
    UNIDAD_VA_FABRICA,
    CAPACIDAD_VA_PROMEDIO,
    UNIDAD_VA_PROMEDIO,
    PRECIO_PROMEDIO,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    REQ_APROVISIONAMIENTO
  )
  VALUES
  (
    DB_INFRAESTRUCTURA.seq_ADMI_MODELO_ELEMENTO.nextval,
    (
        select id_marca_elemento
        from DB_INFRAESTRUCTURA.admi_marca_ELEMENTO
        where NOMBRE_MARCA_ELEMENTO='RINORACK'
    ),
    227,
    'RACK 42 U',
    'ESTO ES UN MODELO DE RACK QUE TIENE 42 UNIDADES',
    NULL,
    'DIAS',
    NULL,
    'DIAS',
    NULL,
    'MM',
    NULL,
    'MM',
    NULL,
    'MM',
    NULL,
    'GR',
    1,
    NULL,
    'BPS',
    NULL,
    'BPS',
    NULL,
    'W',
    NULL,
    'W',
    NULL,
    'Activo',
    'kyrodriguez',
    sysdate,
    'kyrodriguez',
    sysdate,
    'SI'
  );

--=======================================================================
--   Se crea modelo RACK 42 U de la marca "SIEMON" utilizado para rack  
--   de DC en la administración de Racks.
--=======================================================================

INSERT
INTO DB_INFRAESTRUCTURA.ADMI_MODELO_ELEMENTO
  (
    ID_MODELO_ELEMENTO,
    MARCA_ELEMENTO_ID,
    TIPO_ELEMENTO_ID,
    NOMBRE_MODELO_ELEMENTO,
    DESCRIPCION_MODELO_ELEMENTO,
    MTTR,
    UNIDAD_MEDIDA_MTTR,
    MTBF,
    UNIDAD_MEDIDA_MTBF,
    ANCHO_MODELO,
    UNIDAD_MEDIDA_ANCHO,
    LARGO_MODELO,
    UNIDAD_MEDIDA_LARGO,
    ALTO_MODELO,
    UNIDAD_MEDIDA_ALTO,
    PESO_MODELO,
    UNIDAD_MEDIDA_PESO,
    U_RACK,
    CAPACIDAD_ENTRADA,
    UNIDAD_MEDIDA_ENTRADA,
    CAPACIDAD_SALIDA,
    UNIDAD_MEDIDA_SALIDA,
    CAPACIDAD_VA_FABRICA,
    UNIDAD_VA_FABRICA,
    CAPACIDAD_VA_PROMEDIO,
    UNIDAD_VA_PROMEDIO,
    PRECIO_PROMEDIO,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    REQ_APROVISIONAMIENTO
  )
  VALUES
  (
    DB_INFRAESTRUCTURA.seq_ADMI_MODELO_ELEMENTO.nextval,
    (
        select id_marca_elemento
        from DB_INFRAESTRUCTURA.admi_marca_ELEMENTO
        where NOMBRE_MARCA_ELEMENTO='SIEMON'
    ),
    227,
    'RACK 42 U',
    'ESTO ES UN MODELO DE RACK QUE TIENE 42 UNIDADES',
    NULL,
    'DIAS',
    NULL,
    'DIAS',
    NULL,
    'MM',
    NULL,
    'MM',
    NULL,
    'MM',
    NULL,
    'GR',
    1,
    NULL,
    'BPS',
    NULL,
    'BPS',
    NULL,
    'W',
    NULL,
    'W',
    NULL,
    'Activo',
    'kyrodriguez',
    sysdate,
    'kyrodriguez',
    sysdate,
    'SI'
  );

--=======================================================================
--   Se crea modelo RACK 42 U de la marca "APC" utilizado para rack  
--   de DC en la administración de Racks.
--=======================================================================

INSERT
INTO DB_INFRAESTRUCTURA.ADMI_MODELO_ELEMENTO
  (
    ID_MODELO_ELEMENTO,
    MARCA_ELEMENTO_ID,
    TIPO_ELEMENTO_ID,
    NOMBRE_MODELO_ELEMENTO,
    DESCRIPCION_MODELO_ELEMENTO,
    MTTR,
    UNIDAD_MEDIDA_MTTR,
    MTBF,
    UNIDAD_MEDIDA_MTBF,
    ANCHO_MODELO,
    UNIDAD_MEDIDA_ANCHO,
    LARGO_MODELO,
    UNIDAD_MEDIDA_LARGO,
    ALTO_MODELO,
    UNIDAD_MEDIDA_ALTO,
    PESO_MODELO,
    UNIDAD_MEDIDA_PESO,
    U_RACK,
    CAPACIDAD_ENTRADA,
    UNIDAD_MEDIDA_ENTRADA,
    CAPACIDAD_SALIDA,
    UNIDAD_MEDIDA_SALIDA,
    CAPACIDAD_VA_FABRICA,
    UNIDAD_VA_FABRICA,
    CAPACIDAD_VA_PROMEDIO,
    UNIDAD_VA_PROMEDIO,
    PRECIO_PROMEDIO,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    REQ_APROVISIONAMIENTO
  )
  VALUES
  (
    DB_INFRAESTRUCTURA.seq_ADMI_MODELO_ELEMENTO.nextval,
    (
        select id_marca_elemento
        from DB_INFRAESTRUCTURA.admi_marca_ELEMENTO
        where NOMBRE_MARCA_ELEMENTO='APC'
    ),
    227,
    'RACK 42 U',
    'ESTO ES UN MODELO DE RACK QUE TIENE 42 UNIDADES',
    NULL,
    'DIAS',
    NULL,
    'DIAS',
    NULL,
    'MM',
    NULL,
    'MM',
    NULL,
    'MM',
    NULL,
    'GR',
    1,
    NULL,
    'BPS',
    NULL,
    'BPS',
    NULL,
    'W',
    NULL,
    'W',
    NULL,
    'Activo',
    'kyrodriguez',
    sysdate,
    'kyrodriguez',
    sysdate,
    'SI'
  );


commit;
/