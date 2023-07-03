/**
 * Se crea DML para insertar parámetros de estados de solicitud para el cargo de reproceso
 * de débito. 
 * 
 * @author Alex Arreaga <atarreaga@telconet.ec>
 * @since 1.0 20-05-2021
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
    'PARAM_CARGO_REPROCESO_DEBITOS',
    'Parámetros definidos para el proceso de cargo por reproceso de débito',
    'FINANCIERO',
    'CARGO_REPROCESO_DEBITO',
    'Activo',
    'atarreaga',
    SYSDATE,
    '172.17.0.1',
    'atarreaga',
    SYSDATE,
    '172.17.0.1'
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
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_CARGO_REPROCESO_DEBITOS'
      AND ESTADO             = 'Activo'
    ),
    'ESTADOS_SOLICITUD',
    'Pendiente',
    NULL,
    NULL,
    NULL,
    'Activo',
    'atarreaga',
    SYSDATE,
    '172.17.0.1',
    'atarreaga',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18'
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
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_CARGO_REPROCESO_DEBITOS'
      AND ESTADO             = 'Activo'
    ),
    'ESTADOS_SOLICITUD',
    'Finalizada',
    NULL,
    NULL,
    NULL,
    'Activo',
    'atarreaga',
    SYSDATE,
    '172.17.0.1',
    'atarreaga',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18'
  );

COMMIT;
/
