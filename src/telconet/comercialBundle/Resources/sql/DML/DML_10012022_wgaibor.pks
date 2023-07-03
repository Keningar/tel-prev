/**
 * Documentación INSERT DE PARÁMETROS PARA LA GESTIÓN DE VALIDACIÓN DE IDENTIFICACIÓN.
 * INSERT de parámetros en la estructura  DB_GENERAL.ADMI_PARAMETRO_CAB y DB_GENERAL.ADMI_PARAMETRO_DET.
 *
 * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
 * @version 1.0 14-01-2022
 */

REM INSERTING into DB_GENERAL.ADMI_PARAMETRO_DET
SET DEFINE OFF;

--##############################################################################
--#########################  ADMI_PARAMETRO_CAB  ###############################
--##############################################################################
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
    'VALIDA_IDENTIFICACION_POR_EMPRESA',
    'VALIDACIÓN DE IDENTIFICACIÓN POR EMPRESA',
    'COMERCIAL',
    NULL,
    'Activo',
    'wgaibor',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL);

--##############################################################################
--#########################  ADMI_PARAMETRO_DET  ###############################
--##############################################################################
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
    VALOR6,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB
    WHERE NOMBRE_PARAMETRO = 'VALIDA_IDENTIFICACION_POR_EMPRESA'
    ),
    'VALIDA IDENTIFICACIÓN EMPRESA MD',
    '18',
    'CED',
    'C',
    NULL,
    'Activo',
    'wgaibor',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    'NAT',
    NULL,
    NULL
  );

COMMIT;

/