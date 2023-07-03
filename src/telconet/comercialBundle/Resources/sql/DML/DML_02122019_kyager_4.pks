/**
 * Documentación INSERT DE PARÁMETROS DE ESTADOS DE SERVICIOS Y RESTA SYSDATE PARA PROMOCIONES DE INSTALACIÓN.
 * INSERT de parámetros en la estructura  DB_GENERAL.ADMI_PARAMETRO_CAB y DB_GENERAL.ADMI_PARAMETRO_DET.
 *
 * Se insertan parámetros para los estados de los servicios para la evaluación de fechas.
 *
 * @author Katherine Yager <kyager@telconet.ec>
 * @version 1.0 02-12-2019
 */

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
      WHERE NOMBRE_PARAMETRO = 'PROMOCIONES_PARAMETROS_EJECUCION_DE_ALCANCE'
      AND ESTADO             = 'Activo'
    ),
    'NUMERO_DIAS_PROM_INS',
    '0',
    'ALCANCE',
    'Numero de días a considerar para obtener los servicios que se procesarán por las promociones de Instalación',
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

--Estados de servicios para crear las solicitudes en promociones de instalación.

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
    'PROM_INS_SOL_FACT',
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
    'PROM_INS_SOL_FACT',
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
    'PROM_INS_SOL_FACT',
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
    'PROM_INS_SOL_FACT',
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
    'PROM_INS_SOL_FACT',
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
    'PROM_INS_SOL_FACT',
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
    'PROM_INS_SOL_FACT',
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

--7 EnVerificacion
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
    'PROM_INS_SOL_FACT',
    'Activo',
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