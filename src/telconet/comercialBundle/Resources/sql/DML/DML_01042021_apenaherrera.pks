
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
      WHERE NOMBRE_PARAMETRO = 'PARAM_FLUJO_ADULTO_MAYOR'
      AND ESTADO             = 'Activo'
    ),
    'TIPO_PERSONA',
    'NAT',
    'NATURAL',
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
      WHERE NOMBRE_PARAMETRO = 'PARAM_FLUJO_ADULTO_MAYOR'
      AND ESTADO             = 'Activo'
    ),
    'TIPO_PLAN',
    'HOME',
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
      WHERE NOMBRE_PARAMETRO = 'PARAM_FLUJO_ADULTO_MAYOR'
      AND ESTADO             = 'Activo'
    ),
    'TIPO_DOCUMENTO',
    'CED',
    'CEDULA',
    'S',
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
      WHERE NOMBRE_PARAMETRO = 'PARAM_FLUJO_ADULTO_MAYOR'
      AND ESTADO             = 'Activo'
    ),
    'TIPO_DOCUMENTO',
    'PLA',
    'PLANILLA',
    'S',
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
      WHERE NOMBRE_PARAMETRO = 'PARAM_FLUJO_ADULTO_MAYOR'
      AND ESTADO             = 'Activo'
    ),
    'CANTIDAD_PERMITIDA_DOCUMENTOS',
    '5',
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
      WHERE NOMBRE_PARAMETRO = 'PARAM_FLUJO_ADULTO_MAYOR'
      AND ESTADO             = 'Activo'
    ),
    'Contiene el mensaje parametrizado por Cambio de Beneficio',
    'MENSAJE_CAMBIO_BENEFICIO',   
    'Cambio de Beneficio Discapacidad a 3era Edad.',
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
      WHERE NOMBRE_PARAMETRO = 'PARAM_FLUJO_ADULTO_MAYOR'
      AND ESTADO             = 'Activo'
    ),
    'Contiene el mensaje parametrizado por validacion de adulto mayor > 65',
    'MENSAJE_VALIDACION_ADULTO_MAYOR',   
    'No es Adulto Mayor.',
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
      WHERE NOMBRE_PARAMETRO = 'PARAM_FLUJO_ADULTO_MAYOR'
      AND ESTADO             = 'Activo'
    ),
    'Contiene el mensaje parametrizado por validacion de planes permitidos Home',
    'MENSAJE_VALIDACION_PLANES_PERMITIDOS',   
    'Plan no permitido para el Beneficio.',
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
      WHERE NOMBRE_PARAMETRO = 'PARAM_FLUJO_ADULTO_MAYOR'
      AND ESTADO             = 'Activo'
    ),
    'Contiene el mensaje parametrizado por validacion de Tipo Tributario',
    'MENSAJE_VALIDACION_TIPO_TRIBUTARIO',   
    'Cliente no es Persona Natural.',
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
      WHERE NOMBRE_PARAMETRO = 'PARAM_FLUJO_ADULTO_MAYOR'
      AND ESTADO             = 'Activo'
    ),
    'Contiene el mensaje parametrizado por actualizacion de Fecha de Nacimiento',
    'MENSAJE_ACTUALIZACION_FECHA_NACIMIENTO',   
    'Se actualizo la Fecha de Nacimiento.',
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
      WHERE NOMBRE_PARAMETRO = 'PARAM_FLUJO_ADULTO_MAYOR'
      AND ESTADO             = 'Activo'
    ),
    'Contiene el mensaje parametrizado por confirmación de Fecha de Nacimiento',
    'MENSAJE_CONFIRMACION_FECHA_NACIMIENTO',   
    'Se confirma Fecha de Nacimiento.',
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
      WHERE NOMBRE_PARAMETRO = 'PARAM_FLUJO_ADULTO_MAYOR'
      AND ESTADO             = 'Activo'
    ),
    'Contiene el mensaje parametrizado por Cancelación de Beneficio',
    'MENSAJE_CANCELACION_BENEFICIO',   
    'Se canceló el beneficio de [strTipoBeneficio] por motivo: strMotivoCancel.',
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
   12231,
   'Disposición de la Empresa',
   'Activo',
   'apenaherrera',
   SYSDATE,
   'apenaherrera',
   SYSDATE,
   NULL,
   NULL
  );

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
   12231,
   'Decisión del Cliente',
   'Activo',
   'apenaherrera',
   SYSDATE,
   'apenaherrera',
   SYSDATE,
   NULL,
   NULL
  );

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
   12231,
   'Ente Regulador',
   'Activo',
   'apenaherrera',
   SYSDATE,
   'apenaherrera',
   SYSDATE,
   NULL,
   NULL
  );

INSERT
INTO DB_GENERAL.ADMI_TIPO_DOCUMENTO_GENERAL  
  (
    ID_TIPO_DOCUMENTO,
    CODIGO_TIPO_DOCUMENTO,
    DESCRIPCION_TIPO_DOCUMENTO,
    ESTADO,
    USR_CREACION,
    IP_CREACION,
    FE_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    VISIBLE,
    PERSONA,
    ELEMENTO,
    MOSTRAR_APP
   )
   VALUES
 (
   DB_GENERAL.SEQ_ADMI_TIPO_DOCUMENT_GENERAL.NEXTVAL,
   'PLA',
   'PLANILLA',
   'Activo',
   'apenaherrera',
   '127.0.0.1',
   SYSDATE,
   null,
   null,
   'S',
   'S',
   'N',
   'S');

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
      WHERE NOMBRE_PARAMETRO = 'PARAM_FLUJO_ADULTO_MAYOR'
      AND ESTADO             = 'Activo'
    ),
    'Contiene el mensaje parametrizado por validación de documentos requeridos',
    'MENSAJE_VALIDACION_DOC_REQUERIDOS',   
    'Se debe cargar los archivos requeridos',
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

COMMIT;
/
