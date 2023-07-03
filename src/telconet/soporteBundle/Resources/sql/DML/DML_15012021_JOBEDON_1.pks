/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Se agregan parametros para controlar creacion de casos
 * @author José Bedón Sánchez <jobedon@telconet.ec>
 * @version 1.0 15-01-2021 - Versión Inicial.
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
    'PARAMETROS_CIERRE_CASO',
    'PARAMETROS PARA DEFINIR BANDERAS DE PROYECTO DETALLE DE TAREAS EN CIERRE DE CASO',
    'SOPORTE',
    'CIERRE_CASO',
    'Activo',
    'jobedon',
    SYSDATE,
    '172.17.0.1',
    'jobedon',
    NULL,
    NULL
  );
COMMIT;

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
    VALOR6
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB
    WHERE NOMBRE_PARAMETRO = 'PARAMETROS_CIERRE_CASO'
    AND MODULO             = 'SOPORTE'
    AND PROCESO            = 'CIERRE_CASO'
    AND ESTADO             = 'Activo'
    ),
    'FECHA_REFERENCIA_CIERRE_CASOS',
    TO_CHAR(SYSDATE,'DD-MM-YYYY'),
    NULL,
    NULL,
    NULL,
    'Activo',
    'jobedon',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    '10',
    NULL
  );
COMMIT;  

/