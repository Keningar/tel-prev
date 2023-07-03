
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
    'DEFINICION_FOX',
    'S',
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
    'FOX_PREMIUM',
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
    'SSID_FOX',
    'T',
    'Activo',
    SYSDATE,
    'apenaherrera',
    NULL,
    NULL,
    'TECNICA'
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
    'USUARIO_FOX',
    'T',
    'Activo',
    SYSDATE,
    'apenaherrera',
    NULL,
    NULL,
    'TECNICA'
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
    'PASSWORD_FOX',
    'T',
    'Activo',
    SYSDATE,
    'apenaherrera',
    NULL,
    NULL,
    'TECNICA'
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
    'MIGRADO_FOX',
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
    'FOXP',
    'FOX PREMIUM',
    NULL,
    'if( "[DEFINICION_FOX]"=="SD") { PRECIO=12.88 } else if( "[DEFINICION_FOX]"=="HD") { PRECIO=15.46 }',
    0,
    'Activo',
    SYSDATE,
    'apenaherrera',
    '172.17.0.1',
    NULL,
    NULL,
    'NO',
    'NO',
    'NO',
    'NO',
    'FOXPREMIUM',
    NULL,
    'S',
    'NO',
    NULL,
    'Pendiente',
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
     WHERE CODIGO_PRODUCTO='FOXP'
     AND ESTADO='Activo'
    ),
    (
     SELECT ID_CARACTERISTICA 
     FROM DB_COMERCIAL.ADMI_CARACTERISTICA 
     WHERE DESCRIPCION_CARACTERISTICA='DEFINICION_FOX' 
     AND ESTADO='Activo'
    ),
    SYSDATE,
    NULL,
    'apenaherrera',
    NULL,
    'Activo',
    'SI'
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
     WHERE CODIGO_PRODUCTO='FOXP'
     AND ESTADO='Activo'
    ),
    (
     SELECT ID_CARACTERISTICA 
     FROM DB_COMERCIAL.ADMI_CARACTERISTICA 
     WHERE DESCRIPCION_CARACTERISTICA='FOX_PREMIUM' 
     AND ESTADO='Activo'
    ),
    SYSDATE,
    NULL,
    'apenaherrera',
    NULL,
    'Activo',
    'NO'
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
     WHERE CODIGO_PRODUCTO='FOXP'
     AND ESTADO='Activo'
    ),
    (
     SELECT ID_CARACTERISTICA 
     FROM DB_COMERCIAL.ADMI_CARACTERISTICA 
     WHERE DESCRIPCION_CARACTERISTICA='SSID_FOX' 
     AND ESTADO='Activo'
    ),
    SYSDATE,
    NULL,
    'apenaherrera',
    NULL,
    'Activo',
    'NO'
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
     WHERE CODIGO_PRODUCTO='FOXP'
     AND ESTADO='Activo'
    ),
    (
     SELECT ID_CARACTERISTICA 
     FROM DB_COMERCIAL.ADMI_CARACTERISTICA 
     WHERE DESCRIPCION_CARACTERISTICA='USUARIO_FOX' 
     AND ESTADO='Activo'
    ),
    SYSDATE,
    NULL,
    'apenaherrera',
    NULL,
    'Activo',
    'NO'
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
     WHERE CODIGO_PRODUCTO='FOXP'
     AND ESTADO='Activo'
    ),
    (
     SELECT ID_CARACTERISTICA 
     FROM DB_COMERCIAL.ADMI_CARACTERISTICA 
     WHERE DESCRIPCION_CARACTERISTICA='PASSWORD_FOX' 
     AND ESTADO='Activo'
    ),
    SYSDATE,
    NULL,
    'apenaherrera',
    NULL,
    'Activo',
    'NO'
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
     WHERE CODIGO_PRODUCTO='FOXP'
     AND ESTADO='Activo'
    ),
    (
     SELECT ID_CARACTERISTICA 
     FROM DB_COMERCIAL.ADMI_CARACTERISTICA 
     WHERE DESCRIPCION_CARACTERISTICA='MIGRADO_FOX' 
     AND ESTADO='Activo'
    ),
    SYSDATE,
    NULL,
    'apenaherrera',
    NULL,
    'Activo',
    'NO'
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
     WHERE CODIGO_PRODUCTO='FOXP'
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
INTO DB_GENERAL.ADMI_PARAMETRO_CAB
  (
    ID_PARAMETRO,
    NOMBRE_PARAMETRO,
    DESCRIPCION,
    MODULO,
    PROCESO,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'PROD_DEFINICION_FOX',
    'PROD_DEFINICION_FOX',
    'COMERCIAL',
    NULL,
    'Activo',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1'
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
      WHERE NOMBRE_PARAMETRO = 'PROD_DEFINICION_FOX'
      AND ESTADO             = 'Activo'
    ),
    'PROD_DEFINICION_FOX',
    'HD',
    NULL,
    NULL,
    NULL,
    'Activo',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18'
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
      WHERE NOMBRE_PARAMETRO = 'PROD_DEFINICION_FOX'
      AND ESTADO             = 'Activo'
    ),
    'PROD_DEFINICION_FOX',
    'SD',
    NULL,
    NULL,
    NULL,
    'Activo',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18'
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
      WHERE NOMBRE_PARAMETRO = 'INFO_SERVICIO'
      AND PROCESO            = 'ACTIVACION_SERVICIO'
      AND ESTADO             = 'Activo'
    ),
    'ULTIMAS MILLAS PERMITIDAS DEL SERVICIO DE INTERNET PARA EL FLUJO DE FOX PREMIUM',
    'ULTIMAS_MILLAS_INTERNET_FOX_PREMIUM',
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
    '18'
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
      WHERE NOMBRE_PARAMETRO = 'INFO_SERVICIO'
      AND PROCESO            = 'ACTIVACION_SERVICIO'
      AND ESTADO             = 'Activo'
    ),
    'ULTIMAS MILLAS PERMITIDAS DEL SERVICIO DE INTERNET PARA EL FLUJO DE FOX PREMIUM',
    'ULTIMAS_MILLAS_INTERNET_FOX_PREMIUM',
    '3',
    'Cobre',
    'NULL',
    'Activo',
    'apenaherrera',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    'NULL',
    '18'
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
      WHERE NOMBRE_PARAMETRO = 'INFO_SERVICIO'
      AND PROCESO            = 'ACTIVACION_SERVICIO'
      AND ESTADO             = 'Activo'
    ),
    'ESTADOS PERMITIDOS DEL SERVICIO DE INTERNET PARA EL FLUJO DE FOX PREMIUM',
    'ESTADOS_INTERNET_FOX_PREMIUM',
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
    'NULL',
    '18'
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
    'FOXPREMIUM',
    'FOXPREMIUM',
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