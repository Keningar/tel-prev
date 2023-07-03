INSERT
INTO DB_COMERCIAL.ADMI_CARACTERISTICA
  (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    TIPO
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'APWIFI',
    'T',
    'Activo',
    SYSDATE,
    'apenaherrera',
    NULL,
    NULL,
    'COMERCIAL'
  );

INSERT
INTO DB_COMERCIAL.ADMI_CARACTERISTICA
  (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    TIPO
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'RENTA_MENSUAL',
    'T',
    'Activo',
    SYSDATE,
    'apenaherrera',
    NULL,
    NULL,
    'COMERCIAL'
  );

INSERT
INTO DB_COMERCIAL.ADMI_PRODUCTO
  (
    ID_PRODUCTO,
    EMPRESA_COD,
    CODIGO_PRODUCTO,
    DESCRIPCION_PRODUCTO,
    FUNCION_COSTO,
    FUNCION_PRECIO,
    INSTALACION,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    IP_CREACION,
    CTA_CONTABLE_PROD,
    CTA_CONTABLE_PROD_NC,
    ES_PREFERENCIA,
    ES_ENLACE,
    REQUIERE_PLANIFICACION,
    REQUIERE_INFO_TECNICA,
    NOMBRE_TECNICO,
    CTA_CONTABLE_DESC,
    TIPO,
    ES_CONCENTRADOR,
    SOPORTE_MASIVO,
    ESTADO_INICIAL,
    GRUPO,
    COMISION_VENTA,
    COMISION_MANTENIMIENTO,
    USR_GERENTE,
    CLASIFICACION,
    REQUIERE_COMISIONAR,
    SUBGRUPO,
    LINEA_NEGOCIO
  )
  VALUES
  (
   DB_COMERCIAL.SEQ_ADMI_PRODUCTO.NEXTVAL,
    '18',
    'APWIFI',
    'AP WIFI',
    NULL,
    'PRECIO=5.00',
    0,
    'Activo',
    SYSDATE,
    'apenaherrera',
    '127.0.0.1',
    NULL,
    NULL,
    'NO',
    'NO',
    'SI',
    'NO',
    'APWIFI',
    NULL,
    'S',
    'NO',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    'NO',
    NULL,
    NULL
  );

INSERT
INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
  (
    ID_PRODUCTO_CARACTERISITICA,
    PRODUCTO_ID,
    CARACTERISTICA_ID,
    FE_CREACION,
    FE_ULT_MOD,
    USR_CREACION,
    USR_ULT_MOD,
    ESTADO,
    VISIBLE_COMERCIAL
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    (
     SELECT ID_PRODUCTO
     FROM DB_COMERCIAL.ADMI_PRODUCTO 
     WHERE CODIGO_PRODUCTO='APWIFI'
     AND ESTADO='Activo'
    ),
    (
     SELECT ID_CARACTERISTICA 
     FROM DB_COMERCIAL.ADMI_CARACTERISTICA 
     WHERE DESCRIPCION_CARACTERISTICA='APWIFI' 
     AND ESTADO='Activo'
    ),
    SYSDATE,
    NULL,
    'apenaherrera',
    NULL,
    'Activo',
    NULL
  );

INSERT
INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
  (
    ID_PRODUCTO_CARACTERISITICA,
    PRODUCTO_ID,
    CARACTERISTICA_ID,
    FE_CREACION,
    FE_ULT_MOD,
    USR_CREACION,
    USR_ULT_MOD,
    ESTADO,
    VISIBLE_COMERCIAL
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    (
     SELECT ID_PRODUCTO
     FROM DB_COMERCIAL.ADMI_PRODUCTO 
     WHERE CODIGO_PRODUCTO='APWIFI'
     AND ESTADO='Activo'
    ),
    (
     SELECT ID_CARACTERISTICA 
     FROM DB_COMERCIAL.ADMI_CARACTERISTICA 
     WHERE DESCRIPCION_CARACTERISTICA='FACTURACION_UNICA' 
     AND ESTADO='Activo'
    ),
    SYSDATE,
    NULL,
    'apenaherrera',
    NULL,
    'Activo',
    NULL
  );

INSERT
INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
  (
    ID_PRODUCTO_CARACTERISITICA,
    PRODUCTO_ID,
    CARACTERISTICA_ID,
    FE_CREACION,
    FE_ULT_MOD,
    USR_CREACION,
    USR_ULT_MOD,
    ESTADO,
    VISIBLE_COMERCIAL
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    (
     SELECT ID_PRODUCTO
     FROM DB_COMERCIAL.ADMI_PRODUCTO 
     WHERE CODIGO_PRODUCTO='APWIFI'
     AND ESTADO='Activo'
    ),
    (
     SELECT ID_CARACTERISTICA 
     FROM DB_COMERCIAL.ADMI_CARACTERISTICA 
     WHERE DESCRIPCION_CARACTERISTICA='MAC WIFI' 
     AND ESTADO='Activo'
    ),
    SYSDATE,
    NULL,
    'apenaherrera',
    NULL,
    'Activo',
    NULL
  );

INSERT
INTO DB_COMERCIAL.INFO_PRODUCTO_IMPUESTO
  (
    ID_PRODUCTO_IMPUESTO,
    PRODUCTO_ID,
    IMPUESTO_ID,
    PORCENTAJE_IMPUESTO,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    ESTADO
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_INFO_PRODUCTO_IMPUESTO.NEXTVAL,
    (
     SELECT ID_PRODUCTO
     FROM DB_COMERCIAL.ADMI_PRODUCTO 
     WHERE CODIGO_PRODUCTO='APWIFI'
     AND ESTADO='Activo'
    ),
    1,
    12,
    SYSDATE,
    'apenaherrera',
    NULL,
    NULL,
    'Activo'
  );

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
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'INFO_SERVICIO'
      AND PROCESO            = 'ACTIVACION_SERVICIO'
      AND ESTADO             = 'Activo'
    ),
    'ESTADOS PERMITIDOS DEL SERVICIO DE INTERNET PARA EL FLUJO DE APWIFI',
    'ESTADOS_INTERNET_APWIFI',
    'Activo',
    'NULL',
    'NULL',
    'Activo',
    'apenaherrera',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    '18',
    'CONTIENE VALOR1: ESTADOS_INTERNET_APWIFI (DEFINE QUE ESTADOS SERAN CONSIDERADOS 
     PARA VALIDAR EL SERVICIO DE INTERNET EXISTENTE PARA LA CONTRATACION DEL PRODUCTO APWIFI)
     VALOR2: CONTIENE EL ESTADO PERMITIDO PARA EL SERVICIO DE INTERNET (INFO_SERVICIO)'
  );

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
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'INFO_SERVICIO'
      AND PROCESO            = 'ACTIVACION_SERVICIO'
      AND ESTADO             = 'Activo'
    ),
    'ULTIMAS MILLAS PERMITIDAS DEL SERVICIO DE INTERNET PARA EL FLUJO DE APWIFI',
    'ULTIMAS_MILLAS_INTERNET_APWIFI',
    '1',
    'Fibra Optica',
    'NULL',
    'Activo',
    'apenaherrera',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    'NULL',
    '18',
    'CONTIENE VALOR1: ULTIMAS_MILLAS_INTERNET_APWIFI (DEFINE LAS ULTIMAS MILLAS PERMITIDAS PARA EL SERVICIO DE INTERNET PARA 
    EL FLUJO DE CONTRATACION DEL PRODUCTO APWIFI)
    VALOR2: ID_TIPO_MEDIO (ADMI_TIPO_MEDIO) FIBRA OPTICA
    VALOR3: NOMBRE_TIPO_MEDIO (ADMI_TIPO_MEDIO) FIBRA OPTICA'
  );

INSERT
INTO DB_COMERCIAL.ADMI_PRODUCTO
  (
    ID_PRODUCTO,
    EMPRESA_COD,
    CODIGO_PRODUCTO,
    DESCRIPCION_PRODUCTO,
    FUNCION_COSTO,
    FUNCION_PRECIO,
    INSTALACION,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    IP_CREACION,
    CTA_CONTABLE_PROD,
    CTA_CONTABLE_PROD_NC,
    ES_PREFERENCIA,
    ES_ENLACE,
    REQUIERE_PLANIFICACION,
    REQUIERE_INFO_TECNICA,
    NOMBRE_TECNICO,
    CTA_CONTABLE_DESC,
    TIPO,
    ES_CONCENTRADOR,
    SOPORTE_MASIVO,
    ESTADO_INICIAL,
    GRUPO,
    COMISION_VENTA,
    COMISION_MANTENIMIENTO,
    USR_GERENTE,
    CLASIFICACION,
    REQUIERE_COMISIONAR,
    SUBGRUPO,
    LINEA_NEGOCIO
  )
  VALUES
  (
   DB_COMERCIAL.SEQ_ADMI_PRODUCTO.NEXTVAL,
    '18',
    'R_APWIFI',
    'Renta AP WIFI',
    NULL,
    'PRECIO=5.00',
    0,
    'Activo',
    SYSDATE,
    'apenaherrera',
    '127.0.0.1',
    NULL,
    NULL,
    'NO',
    'NO',
    'SI',
    'NO',
    'APWIFI',
    NULL,
    'S',
    'NO',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    'NO',
    NULL,
    NULL
  );

INSERT
INTO DB_COMERCIAL.INFO_PRODUCTO_IMPUESTO
  (
    ID_PRODUCTO_IMPUESTO,
    PRODUCTO_ID,
    IMPUESTO_ID,
    PORCENTAJE_IMPUESTO,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    ESTADO
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_INFO_PRODUCTO_IMPUESTO.NEXTVAL,
    (
     SELECT ID_PRODUCTO
     FROM DB_COMERCIAL.ADMI_PRODUCTO 
     WHERE CODIGO_PRODUCTO='R_APWIFI'
     AND ESTADO='Activo'
    ),
    1,
    12,
    SYSDATE,
    'apenaherrera',
    NULL,
    NULL,
    'Activo'
  );

INSERT
INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
  (
    ID_PRODUCTO_CARACTERISITICA,
    PRODUCTO_ID,
    CARACTERISTICA_ID,
    FE_CREACION,
    FE_ULT_MOD,
    USR_CREACION,
    USR_ULT_MOD,
    ESTADO,
    VISIBLE_COMERCIAL
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    (
     SELECT ID_PRODUCTO
     FROM DB_COMERCIAL.ADMI_PRODUCTO 
     WHERE CODIGO_PRODUCTO='R_APWIFI'
     AND ESTADO='Activo'
    ),
    (
     SELECT ID_CARACTERISTICA 
     FROM DB_COMERCIAL.ADMI_CARACTERISTICA 
     WHERE DESCRIPCION_CARACTERISTICA='APWIFI' 
     AND ESTADO='Activo'
    ),
    SYSDATE,
    NULL,
    'apenaherrera',
    NULL,
    'Activo',
    NULL
  );

INSERT
INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
  (
    ID_PRODUCTO_CARACTERISITICA,
    PRODUCTO_ID,
    CARACTERISTICA_ID,
    FE_CREACION,
    FE_ULT_MOD,
    USR_CREACION,
    USR_ULT_MOD,
    ESTADO,
    VISIBLE_COMERCIAL
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    (
     SELECT ID_PRODUCTO
     FROM DB_COMERCIAL.ADMI_PRODUCTO 
     WHERE CODIGO_PRODUCTO='R_APWIFI'
     AND ESTADO='Activo'
    ),
    (
     SELECT ID_CARACTERISTICA 
     FROM DB_COMERCIAL.ADMI_CARACTERISTICA 
     WHERE DESCRIPCION_CARACTERISTICA='RENTA_MENSUAL' 
     AND ESTADO='Activo'
    ),
    SYSDATE,
    NULL,
    'apenaherrera',
    NULL,
    'Activo',
    NULL
  );

INSERT
INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
  (
    ID_PRODUCTO_CARACTERISITICA,
    PRODUCTO_ID,
    CARACTERISTICA_ID,
    FE_CREACION,
    FE_ULT_MOD,
    USR_CREACION,
    USR_ULT_MOD,
    ESTADO,
    VISIBLE_COMERCIAL
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    (
     SELECT ID_PRODUCTO
     FROM DB_COMERCIAL.ADMI_PRODUCTO 
     WHERE CODIGO_PRODUCTO='R_APWIFI'
     AND ESTADO='Activo'
    ),
    (
     SELECT ID_CARACTERISTICA 
     FROM DB_COMERCIAL.ADMI_CARACTERISTICA 
     WHERE DESCRIPCION_CARACTERISTICA='MAC WIFI' 
     AND ESTADO='Activo'
    ),
    SYSDATE,
    NULL,
    'apenaherrera',
    NULL,
    'Activo',
    NULL
  );

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
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'NOMBRE_TECNICO_PRODUCTO'
      AND PROCESO            = 'NOMBRE_TECNICO_PRODUCTO'
      AND ESTADO             = 'Activo'
    ),
    'APWIFI',
    'APWIFI',
    NULL,
    NULL,
    NULL,
    'Activo',
    'apenaherrera',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    '18'
  );

COMMIT;
