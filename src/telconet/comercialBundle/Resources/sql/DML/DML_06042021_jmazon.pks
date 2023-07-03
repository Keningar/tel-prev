/**
* @author Jonathan Mazon Sanchez <jmazon@telconet.ec>
* @version 1.0
* @since 06-04-2021
* Se crean parametros para el traslado del producto Extender Duan Band
*/
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
    VALOR6,
    VALOR7,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB
    WHERE NOMBRE_PARAMETRO = 'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD'
    AND ESTADO             = 'Activo'
    ),
    'Estados de los servicios parametrizados para permitir un traslado en MD',
    'TRASLADO',
    'ESTADOS_SERVICIOS_X_PROD_PERMITIDOS',
    'EXTENDER_DUAL_BAND',
    'Pendiente',
    'Activo',
    'jmazon',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    'PrePlanificada',
    '18',
    NULL,
    NULL,
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
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB
    WHERE NOMBRE_PARAMETRO = 'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD'
    AND ESTADO             = 'Activo'
    ),
    'Estados de los servicios parametrizados para permitir un traslado en MD',
    'TRASLADO',
    'ESTADOS_SERVICIOS_X_PROD_PERMITIDOS',
    'EXTENDER_DUAL_BAND',
    'Asignada',
    'Activo',
    'jmazon',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    'PrePlanificada',
    '18',
    NULL,
    NULL,
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
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB
    WHERE NOMBRE_PARAMETRO = 'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD'
    AND ESTADO             = 'Activo'
    ),
    'Estados de los servicios parametrizados para permitir un traslado en MD',
    'TRASLADO',
    'ESTADOS_SERVICIOS_X_PROD_PERMITIDOS',
    'EXTENDER_DUAL_BAND',
    'AsignadoTarea',
    'Activo',
    'jmazon',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    'PrePlanificada',
    '18',
    NULL,
    NULL,
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
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB
    WHERE NOMBRE_PARAMETRO = 'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD'
    AND ESTADO             = 'Activo'
    ),
    'Estados de los servicios parametrizados para permitir un traslado en MD',
    'TRASLADO',
    'ESTADOS_SERVICIOS_X_PROD_PERMITIDOS',
    'EXTENDER_DUAL_BAND',
    'Detenida',
    'Activo',
    'jmazon',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    'PrePlanificada',
    '18',
    NULL,
    NULL,
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
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB
    WHERE NOMBRE_PARAMETRO = 'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD'
    AND ESTADO             = 'Activo'
    ),
    'Estados de los servicios parametrizados para permitir un traslado en MD',
    'TRASLADO',
    'ESTADOS_SERVICIOS_X_PROD_PERMITIDOS',
    'EXTENDER_DUAL_BAND',
    'Detenido',
    'Activo',
    'jmazon',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    'PrePlanificada',
    '18',
    NULL,
    NULL,
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
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB
    WHERE NOMBRE_PARAMETRO = 'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD'
    AND ESTADO             = 'Activo'
    ),
    'Estados de los servicios parametrizados para permitir un traslado en MD',
    'TRASLADO',
    'ESTADOS_SERVICIOS_X_PROD_PERMITIDOS',
    'EXTENDER_DUAL_BAND',
    'PrePlanificada',
    'Activo',
    'jmazon',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    'PrePlanificada',
    '18',
    NULL,
    NULL,
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
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB
    WHERE NOMBRE_PARAMETRO = 'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD'
    AND ESTADO             = 'Activo'
    ),
    'Estados de los servicios parametrizados para permitir un traslado en MD',
    'TRASLADO',
    'ESTADOS_SERVICIOS_X_PROD_PERMITIDOS',
    'EXTENDER_DUAL_BAND',
    'Planificada',
    'Activo',
    'jmazon',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    'PrePlanificada',
    '18',
    NULL,
    NULL,
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
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB
    WHERE NOMBRE_PARAMETRO = 'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD'
    AND ESTADO             = 'Activo'
    ),
    'Estados de los servicios parametrizados para permitir un traslado en MD',
    'TRASLADO',
    'ESTADOS_SERVICIOS_X_PROD_PERMITIDOS',
    'EXTENDER_DUAL_BAND',
    'Replanificada',
    'Activo',
    'jmazon',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    'PrePlanificada',
    '18',
    NULL,
    NULL,
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
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB
    WHERE NOMBRE_PARAMETRO = 'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD'
    AND ESTADO             = 'Activo'
    ),
    'Estados de los servicios parametrizados para permitir un traslado en MD',
    'TRASLADO',
    'ESTADOS_SERVICIOS_X_PROD_PERMITIDOS',
    'EXTENDER_DUAL_BAND',
    'Activo',
    'Activo',
    'jmazon',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    'Pendiente',
    '18',
    NULL,
    NULL,
    NULL
  );
COMMIT;
/