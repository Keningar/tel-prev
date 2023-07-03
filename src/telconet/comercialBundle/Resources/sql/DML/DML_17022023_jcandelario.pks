/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Script para agregar parametros detalle para logica de alcance de mapeo y aplicación de promociones por Clientes Nuevos
 * @author José Candelario <jcandelario@telconet.ec>
 * @version 1.0 23-02-2023 - Version Inicial.
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
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PROM_HORA_EJECUCION_JOB'
      AND ESTADO             = 'Activo'
    ),
    'PROM_HORA_ALCANCE_JOB_CLI_NUEV',
    ' 21:20:00',
    NULL,
    NULL,
    NULL,
    'Activo',
    'jcandelario',
    SYSDATE,
    '172.17.0.1',
    'jcandelario',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    NULL,
    NULL,
    'Define la hora de ejecución del Job JOB_MAPEO_PROMO_CLI_NUEVOS'
  );

 COMMIT;
/
