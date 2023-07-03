/**
 * @author José Alava <jialava@telconet.ec>
 * @version 1.0
 * @since 01-04-2019
 * Se crean las sentencias DML para insertar parámetros
 */

 -- Se inserta una tarea nueva en relación a Coordinación Hosting

INSERT
INTO DB_SOPORTE.ADMI_TAREA VALUES
  (
    DB_SOPORTE.SEQ_ADMI_TAREA.NEXTVAL,
    (SELECT ID_PROCESO FROM DB_SOPORTE.ADMI_PROCESO WHERE NOMBRE_PROCESO = 'PROCESOS DE DATACENTER' AND ESTADO = 'Activo'),
    NULL,
    NULL,
    NULL,
    '1',
    '0',
    'COORDINACION HOSTING',
    'Coordinación de orden de servicios para producto pool de recursos',
    '1',
    'MINUTOS',
    '1',
    '1',
    'Activo',
    'arsuarez',
    SYSDATE,
    'arsuarez',
    SYSDATE,
    NULL,
    NULL,
    NULL,null,null
  );

--Se inserta detalle para Data Center BOC Guayaquil 
INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'HOSTING TAREAS POR DEPARTAMENTO'),
    'COORDINACION HOSTING',
    'GUAYAQUIL',
    'datacenter_gyeboc@telconet.ec',
    'COORDINACION HOSTING',
    'Data Center Boc',
    'Activo',
    'arsuarez',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    10,NULL,NULL,NULL
  );

-- Se inserta detalle para Data Center BOC Quito
  INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'HOSTING TAREAS POR DEPARTAMENTO'),
    'COORDINACION HOSTING',
    'QUITO',
    'datacenter_uioboc@telconet.ec',
    'COORDINACION HOSTING',
    'Data Center Boc',
    'Activo',
    'arsuarez',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    10,NULL,NULL,NULL
  );