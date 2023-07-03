/**
 * Se realiza parametrización de la empresa ECUANET para la facturación Mensual. 
 * @author Hector Lozano <hlozano@telconet.ec>
 * @version 1.0 
 * @since 24-02-2023
 */

--CARGO REPROCESO DEBITO
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
    OBSERVACION,
    VALOR8,
    VALOR9
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'CARGO REPROCESO DEBITO'
    ),
    'PARAMETROS CONFIGURABLES PARA CARGO POR REPROCESO DE DEBITO',
    '2',
    '1',
    NULL,
    NULL,
    'Inactivo',
    'hlozano',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    (SELECT COD_EMPRESA FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO WHERE NOMBRE_EMPRESA = 'ECUANET'),
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
  ); 
   

/**
 * Se realiza parametrización de la empresa ECUANET para la facturación de Alcance de Cambio de Razón Social.
 * @author Hector Lozano <hlozano@telconet.ec>
 * @version 1.0 
 * @since 24-02-2023
 */

--CICLO_FACTURACION_EMPRESA.
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
    OBSERVACION,
    VALOR8,
    VALOR9
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'CICLO_FACTURACION_EMPRESA'
      AND ESTADO             = 'Activo'
    ),
    'ECUANET',
    'S',
    'EN',
    NULL,
    NULL,
    'Activo',
    'hlozano',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    (SELECT COD_EMPRESA FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO WHERE NOMBRE_EMPRESA = 'ECUANET'),
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
  );   


COMMIT;
/
