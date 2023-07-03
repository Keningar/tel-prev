--=======================================================================
--      Se ingresa nuevos motivos
--=======================================================================
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
    CTA_CONTABLE,
    REF_MOTIVO_ID
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_MOTIVO.NEXTVAL,
    '7017',
    'Cambio de Tipo Medio',
    'Activo',
    'wgaibor',
    SYSDATE,
    'wgaibor',
    SYSDATE,
    NULL,
    NULL
  );
--=======================================================================
--      Se ingresa un nuevo tipo de solicitud
--=======================================================================
INSERT
INTO DB_COMERCIAL.ADMI_TIPO_SOLICITUD
  (
    ID_TIPO_SOLICITUD,
    DESCRIPCION_SOLICITUD,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    ESTADO,
    TAREA_ID,
    ITEM_MENU_ID,
    PROCESO_ID
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_TIPO_SOLICITUD.NEXTVAL,
    'SOLICITUD CAMBIO TIPO MEDIO',
    SYSDATE,
    'wgaibor',
    NULL,
    NULL,
    'Activo',
    NULL,
    NULL,
    NULL
  );
--=======================================================================
--      Se ingresa nuevas caracteristicas.
--=======================================================================
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
    'SOLICITUD_CAMBIO_TIPO_MEDIO',
    'N',
    'Activo',
    SYSDATE,
    'wgaibor',
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
    'ID_CAMBIO_TIPO_MEDIO',
    'N',
    'Activo',
    SYSDATE,
    'wgaibor',
    NULL,
    NULL,
    'COMERCIAL'
  );
--=======================================================================
--      Se asocia el producto L3MPLS con la caracteristica de 
--      cambio tipo medio.
--=======================================================================
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
    (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
    WHERE DESCRIPCION_PRODUCTO = 'L3MPLS'
    AND CLASIFICACION = 'DATOS'),
    (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
    WHERE DESCRIPCION_CARACTERISTICA = 'ID_CAMBIO_TIPO_MEDIO'),
    SYSDATE,
    NULL,
    'wgaibor',
    NULL,
    'Activo',
    'NO'
  );
--==========================================================================
--      PRODUCTO:    Internet Dedicado
--==========================================================================
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
    (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
    WHERE DESCRIPCION_PRODUCTO = 'Internet Dedicado'
    AND CLASIFICACION = 'INTERNET'),
    (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
    WHERE DESCRIPCION_CARACTERISTICA = 'ID_CAMBIO_TIPO_MEDIO'),
    SYSDATE,
    NULL,
    'wgaibor',
    NULL,
    'Activo',
    'NO'
  );
--==========================================================================
--      PRODUCTO:    Internet MPLS
--==========================================================================
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
    (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
    WHERE DESCRIPCION_PRODUCTO = 'Internet MPLS'
    AND CLASIFICACION = 'INTERNET'),
    (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
    WHERE DESCRIPCION_CARACTERISTICA = 'ID_CAMBIO_TIPO_MEDIO'),
    SYSDATE,
    NULL,
    'wgaibor',
    NULL,
    'Activo',
    'NO'
  );
--==========================================================================
--      PRODUCTO:    Internet MPLS
--==========================================================================
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
    (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
    WHERE DESCRIPCION_PRODUCTO = 'Concentrador L3MPLS'
    AND CLASIFICACION = 'DATOS'),
    (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
    WHERE DESCRIPCION_CARACTERISTICA = 'ID_CAMBIO_TIPO_MEDIO'),
    SYSDATE,
    NULL,
    'wgaibor',
    NULL,
    'Activo',
    'NO'
  );
--=======================================================================
--      Se crea producto  CTMTN para facturar 
--=======================================================================
INSERT
INTO DB_COMERCIAL.ADMI_PRODUCTO
  (
    ID_PRODUCTO,
    EMPRESA_COD,
    CODIGO_PRODUCTO,
    DESCRIPCION_PRODUCTO,
    FUNCION_COSTO,
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
    FUNCION_PRECIO
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO.NEXTVAL,
    '10',
    'CTMTN',
    'Cambio tipo medio',
    NULL,
    '0',
    'Inactivo',
    sysdate,
    'wgaibor',
    '127.0.0.1',
    '0',
    '0',
    'NO',
    'NO',
    'NO',
    'NO',
    'OTROSSERVICIOS',
    NULL,
    'S',
    'NO',
    'N',
    NULL,
    'OTROS',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    'PRECIO=0'
  );
--=======================================================================
--      Se crea cabecera de parametro para mapear los tipos medios
--=======================================================================
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
    'LISTADO_TIPO_MEDIO',
    'RETORNAR EL LISTADO DE COMBINACIONES DE TIPO MEDIO',
    'TECNICO',
    NULL,
    'Activo',
    'wgaibor',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL
  );

--=======================================================================
--      Se crea detalle para mapear el tipo medio radio
--=======================================================================
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
    (SELECT ID_PARAMETRO
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB
    WHERE NOMBRE_PARAMETRO = 'LISTADO_TIPO_MEDIO'
    ),
    'MAPEAR LOS MEDIOS A LOS CUALES SE PUEDE REALIZAR EL CAMBIO DE TIPO MEDIO RADIO',
    'RAD',
    'FO',
    NULL,
    NULL,
    'Activo',
    'wgaibor',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
  );
--=======================================================================
--      Se crea detalle para mapear el tipo medio utp
--=======================================================================
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
    (SELECT ID_PARAMETRO
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB
    WHERE NOMBRE_PARAMETRO = 'LISTADO_TIPO_MEDIO'
    ),
    'MAPEAR LOS MEDIOS A LOS CUALES SE PUEDE REALIZAR EL CAMBIO DE TIPO MEDIO UTP',
    'UTP',
    'FO,RAD',
    NULL,
    NULL,
    'Activo',
    'wgaibor',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
  );

COMMIT;