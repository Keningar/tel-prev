/**
 * Se crea DML para la parametrización en débitos por Emergencia Sanitaria.
 * @author Hector Lozano <hlozano@telconet.ec>
 * @version 1.0 
 * @since 23-05-2020
 */

--PARAMETRO PARA ESCENARIOS_DEBITOS.

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
      WHERE NOMBRE_PARAMETRO = 'ESCENARIOS_DEBITOS'
      AND ESTADO             = 'Activo'
    ),
    'PARAMETRO QUE DEFINE EL ESCENARIO 1 PARA GENERACION DE DEBITOS',
    'ESCENARIO_1',
    'SELECCIÓN FECHA FACT RECURRENTE',
    NULL,
    NULL,
    'Activo',
    'hlozano',
    SYSDATE,
    '172.17.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
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
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'ESCENARIOS_DEBITOS'
      AND ESTADO             = 'Activo'
    ),
    'PARAMETRO QUE DEFINE EL ESCENARIO BASE PARA GENERACION DE DEBITOS(Empresa TN)',
    'ESCENARIO_BASE',
    'SELECCIÓN VALOR TOTAL',
    NULL,
    NULL,
    'Activo',
    'hlozano',
    SYSDATE,
    '172.17.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    '10',
    NULL,
    NULL,
    NULL
  );      

COMMIT;
/
