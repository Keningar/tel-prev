/**
 * Documentación INSERT DE PARÁMETROS DE ESTADOS DE SERVICIOS PARA EVALUACIÓN FECHAS VIGENCIAS.
 * INSERT de parámetros en la estructura  DB_GENERAL.ADMI_PARAMETRO_CAB y DB_GENERAL.ADMI_PARAMETRO_DET.
 *
 * Se insertan parámetros para los estados de los servicios para la evaluación de fechas.
 *
 * @author Katherine Yager <kyager@telconet.ec>
 * @version 1.0 13-11-2019
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
    'PROM_ESTADO_SERVICIO',
    'Define los estados considerados para el flujo de un servicio en la evalucación de instalación.',
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
      WHERE NOMBRE_PARAMETRO = 'PROM_ESTADO_SERVICIO'
      AND ESTADO             = 'Activo'
    ),
    'PROM_ESTADO_SERVICIO',
    'PROM_INS',
    'Factible',
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
      WHERE NOMBRE_PARAMETRO = 'PROM_ESTADO_SERVICIO'
      AND ESTADO             = 'Activo'
    ),
    'PROM_ESTADO_SERVICIO',
    'PROM_INS',
    'PrePlanificada',
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
  

  --3 Planificada
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
      WHERE NOMBRE_PARAMETRO = 'PROM_ESTADO_SERVICIO'
      AND ESTADO             = 'Activo'
    ),
    'PROM_ESTADO_SERVICIO',
    'PROM_INS',
    'Planificada',
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

 --4 AsignadoTarea
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
      WHERE NOMBRE_PARAMETRO = 'PROM_ESTADO_SERVICIO'
      AND ESTADO             = 'Activo'
    ),
    'PROM_ESTADO_SERVICIO',
    'PROM_INS',
    'AsignadoTarea',
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

 --5 Asignada
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
      WHERE NOMBRE_PARAMETRO = 'PROM_ESTADO_SERVICIO'
      AND ESTADO             = 'Activo'
    ),
    'PROM_ESTADO_SERVICIO',
    'PROM_INS',
    'Asignada',
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


 --5 Replanificada
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
      WHERE NOMBRE_PARAMETRO = 'PROM_ESTADO_SERVICIO'
      AND ESTADO             = 'Activo'
    ),
    'PROM_ESTADO_SERVICIO',
    'PROM_INS',
    'Replanificada',
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


 --6 EnVerificacion
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
      WHERE NOMBRE_PARAMETRO = 'PROM_ESTADO_SERVICIO'
      AND ESTADO             = 'Activo'
    ),
    'PROM_ESTADO_SERVICIO',
    'PROM_INS',
    'EnVerificacion',
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
commit;
/