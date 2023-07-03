/**
 * Documentación INSERT DE PARÁMETROS DE ESTADOS DE PLANES A CONSIDERAR EN EL CONTRATO DIGITAL PARA EVALUACIÓN DE PROMOCIONES
 * INSERT de parámetros en la estructura  DB_GENERAL.ADMI_PARAMETRO_CAB y DB_GENERAL.ADMI_PARAMETRO_DET.
 *
 * Se insertan parámetros para los estados de los planes a evaluar en promociones.
 *
 * @author Katherine Yager <kyager@telconet.ec>
 * @version 1.0 11-03-2020
 */

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
    'ESTADO_PLAN_CONTRATO',
    'Define los estados considerados para la evaluación de promociones en el contrato digital',
    'COMERCIAL',
    NULL,
    'Activo',
    'kyager',
    SYSDATE,
    '172.17.0.1',
    'kyager',
    SYSDATE,
    '172.17.0.1'
  );
  
--1
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
      WHERE NOMBRE_PARAMETRO = 'ESTADO_PLAN_CONTRATO'
      AND ESTADO             = 'Activo'
    ),
    'ESTADO_PLAN_CONTRATO',
    'Activo',
    NULL,
    NULL,
    NULL,
    'Activo',
    'kyager',
    SYSDATE,
    '172.17.0.1',
    'kyager',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18'
  );
  
  --2 PrePlanificada
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
      WHERE NOMBRE_PARAMETRO = 'ESTADO_PLAN_CONTRATO'
      AND ESTADO             = 'Activo'
    ),
    'ESTADO_PLAN_CONTRATO',
    'Inactivo',
    NULL,
    NULL,
    NULL,
    'Activo',
    'kyager',
    SYSDATE,
    '172.17.0.1',
    'kyager',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18'
  );
  
  COMMIT;
/  
