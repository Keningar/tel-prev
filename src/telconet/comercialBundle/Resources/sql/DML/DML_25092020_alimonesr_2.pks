/**
 * Se crea dml para la creacion de parametro para el envio de notificaciones por cambio de datos de facturacion.
 * @author Adrian Limones <alimonesr@telconet.ec>
 * @since 1.0 25-09-2020
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
    IP_CREACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'NOTIFICACION CAMBIO TIPO FACTURACION',
    'PARAMETRO PARA CONFIGURAR LOS PARAMETROS PARA NOTIFICACION CAMBIO TIPO FACTURACION',
    'FINANCIERO',
    'NOTIFICACIONES',
    'Activo',
    'alimonesr',
    SYSDATE,
    '127.0.0.0'
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
      WHERE NOMBRE_PARAMETRO = 'NOTIFICACION CAMBIO TIPO FACTURACION'
      AND MODULO             = 'FINANCIERO'
      AND ESTADO             = 'Activo'
    ),
    'Permite especificar valores para notificacion de cambio de tipo de facturacion',
    'notificaciones_telcos@telconet.ec',
    'NOTIFICACION CAMBIO TIPO FACTURACION',
    '',
    '',
    'Activo',
    'alimonesr',
    SYSDATE,
    '127.0.0.0',
    NULL,
    NULL,
    NULL,
    'NULL',
    '10',
    NULL,
    NULL,
    NULL
  );
  
  commit 
/
