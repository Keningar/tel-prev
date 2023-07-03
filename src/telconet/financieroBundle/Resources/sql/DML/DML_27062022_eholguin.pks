/**
 * @author Edgar Holguín <eholguin@telconet.ec>
 * @version 1.0
 * @since 27-06-2022    
 * Se crea DML para creación de nuevo parámetro.
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
      WHERE NOMBRE_PARAMETRO = 'AUTOMATIZACION PAGOS'
      AND MODULO             = 'FINANCIERO'
      AND ESTADO             = 'Activo'
    ),
    'CONFIGURACION NFS',
    'AutomatizacionPagos',
    'TelcosWeb',
    'Pagos',
    NULL,
    'Activo',
    'eholguin',
    SYSDATE,
    '127.0.0.0',
    NULL,
    NULL,
    NULL,
    NULL,
    '10',
    NULL,
    NULL,
    'Configura los parámetros enviados al NFS donde se almacenarán los estadod de cuenta.'
  );
  
  
COMMIT;
/
