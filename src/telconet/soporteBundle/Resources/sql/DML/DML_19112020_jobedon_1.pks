/**
 * Configuracion para sintomas e hipotesis categorizados por departamento
 * @author José Bedón Sánchez <jobedon@telconet.ec>
 * @version 1.0 19-11-2020 - Versión Inicial.
 */

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_CAB
  (
    id_parametro,
    nombre_parametro,
    descripcion,
    modulo,
    proceso,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    usr_ult_mod,
    fe_ult_mod,
    ip_ult_mod
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'CATEGORIA_SINTOMA',
    'PARAMETRO USADO PARA LA ESTRUCTURA DE SINTOMAS POR DEPARTAMENTO',
    'TELCOS',
    'SOPORTE',
    'Activo',
    'jobedon',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL
  );

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_CAB
  (
    id_parametro,
    nombre_parametro,
    descripcion,
    modulo,
    proceso,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    usr_ult_mod,
    fe_ult_mod,
    ip_ult_mod
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'CATEGORIA_HIPOTESIS',
    'PARAMETRO USADO PARA LA ESTRUCTURA DE HIPOTESIS POR DEPARTAMENTO',
    'TELCOS',
    'SOPORTE',
    'Activo',
    'jobedon',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL
  );
  
  COMMIT;

/