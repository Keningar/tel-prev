
/**
 * Se realiza parametrización de la empresa ECUANET para envío de notificaciones en Facturación Mensual. 
 * @author Hector Lozano <hlozano@telconet.ec>
 * @version 1.0 
 * @since 05-04-2023
 */

--Envío de Notificación.

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
      WHERE NOMBRE_PARAMETRO = 'ENVIO_CORREO'
      AND ESTADO             = 'Activo'
      AND PROCESO            = 'DOCUMENTOS_ELECTRONICOS'
    ),
    'PARAMETRO QUE HABILITA EL ENVIO DE NOTIFICACION POR CORREO LUEGO DEL PROCESO DE FACTURACION MENSUAL',
    'FAC_MASIVA_EN',
    'SI',
    'NO',
    'NO',
    'Activo',
    'hlozano',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
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
      WHERE NOMBRE_PARAMETRO = 'ENVIO_CORREO'
      AND ESTADO             = 'Activo'
      AND PROCESO            = 'DOCUMENTOS_ELECTRONICOS'
    ),
    'DEFINE EL USUARIO QUE ENVIA EL CORREO',
    'FAC_MASIVA_EN_FROM_SUBJECT',
    'notificacionesecuanet@ecuanet.com.ec',
    'Facturación Mensual de Ecuanet',
    NULL,
    'Activo',
    'hlozano',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
  );  



COMMIT;
/
