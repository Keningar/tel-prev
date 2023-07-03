/**
 * @author Josselhin Moreira <kjmoreira@telconet.ec>
 * @version 1.0
 * @since 17-06-2019
 * Se crean las sentencias DML para insertar los motivos relacionados para el cambio de Forma de Pago.
 */

--Regularización a Cartera Legal
INSERT INTO DB_GENERAL.ADMI_MOTIVO(
  ID_MOTIVO,
  RELACION_SISTEMA_ID,
  NOMBRE_MOTIVO,
  ESTADO,
  USR_CREACION,
  FE_CREACION,
  USR_ULT_MOD,
  FE_ULT_MOD,
  CTA_CONTABLE,
  REF_MOTIVO_ID
) VALUES(
    DB_GENERAL.SEQ_ADMI_MOTIVO.NEXTVAL,
    '425',
    'Regularizacion a Cartera Legal',
    'Activo',
    'kjmoreira',
    SYSDATE,
    'kjmoreira',
    SYSDATE,
    NULL,
    NULL
);

-- Regularizacion para envío de debito
INSERT INTO DB_GENERAL.ADMI_MOTIVO(
  ID_MOTIVO,
  RELACION_SISTEMA_ID,
  NOMBRE_MOTIVO,
  ESTADO,
  USR_CREACION,
  FE_CREACION,
  USR_ULT_MOD,
  FE_ULT_MOD,
  CTA_CONTABLE,
  REF_MOTIVO_ID
) VALUES(
    DB_GENERAL.SEQ_ADMI_MOTIVO.NEXTVAL,
    '425',
    'Regularizacion para envío de debito',
    'Activo',
    'kjmoreira',
    SYSDATE,
    'kjmoreira',
    SYSDATE,
    NULL,
    NULL
);

-- Mal ingreso de Data
INSERT INTO DB_GENERAL.ADMI_MOTIVO(
  ID_MOTIVO,
  RELACION_SISTEMA_ID,
  NOMBRE_MOTIVO,
  ESTADO,
  USR_CREACION,
  FE_CREACION,
  USR_ULT_MOD,
  FE_ULT_MOD,
  CTA_CONTABLE,
  REF_MOTIVO_ID
) VALUES(
    DB_GENERAL.SEQ_ADMI_MOTIVO.NEXTVAL,
    '425',
    'Mal ingreso de Data',
    'Activo',
    'kjmoreira',
    SYSDATE,
    'kjmoreira',
    SYSDATE,
    NULL,
    NULL
);

-- Evitar suspensión clientes VIP/CANAL
INSERT INTO DB_GENERAL.ADMI_MOTIVO(
  ID_MOTIVO,
  RELACION_SISTEMA_ID,
  NOMBRE_MOTIVO,
  ESTADO,
  USR_CREACION,
  FE_CREACION,
  USR_ULT_MOD,
  FE_ULT_MOD,
  CTA_CONTABLE,
  REF_MOTIVO_ID
) VALUES(
    DB_GENERAL.SEQ_ADMI_MOTIVO.NEXTVAL,
    '425',
    'Evitar suspension clientes VIP/CANAL',
    'Activo',
    'kjmoreira',
    SYSDATE,
    'kjmoreira',
    SYSDATE,
    NULL,
    NULL
);

-- Solicitado por Entidad Financiera
INSERT INTO DB_GENERAL.ADMI_MOTIVO(
  ID_MOTIVO,
  RELACION_SISTEMA_ID,
  NOMBRE_MOTIVO,
  ESTADO,
  USR_CREACION,
  FE_CREACION,
  USR_ULT_MOD,
  FE_ULT_MOD,
  CTA_CONTABLE,
  REF_MOTIVO_ID
) VALUES(
    DB_GENERAL.SEQ_ADMI_MOTIVO.NEXTVAL,
    '425',
    'Solicitado por Entidad Financiera',
    'Activo',
    'kjmoreira',
    SYSDATE,
    'kjmoreira',
    SYSDATE,
    NULL,
    NULL
);

-- Solicitado por el Cliente
INSERT INTO DB_GENERAL.ADMI_MOTIVO(
  ID_MOTIVO,
  RELACION_SISTEMA_ID,
  NOMBRE_MOTIVO,
  ESTADO,
  USR_CREACION,
  FE_CREACION,
  USR_ULT_MOD,
  FE_ULT_MOD,
  CTA_CONTABLE,
  REF_MOTIVO_ID
) VALUES(
    DB_GENERAL.SEQ_ADMI_MOTIVO.NEXTVAL,
    '425',
    'Solicitado por el Cliente',
    'Activo',
    'kjmoreira',
    SYSDATE,
    'kjmoreira',
    SYSDATE,
    NULL,
    NULL
);

----------------------------------------------------------------------------------------------------------------------------------------------------------
-- Inserta Parámetro Motivo por el Cambio de Forma de Pago.
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB(
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
) VALUES(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'MOTIVOS_CAMBIO_FORMA_PAGO',
    'PARAMETRO QUE DEFINE LOS MOTIVOS DEL CAMBIO DE FORMA DE PAGO.',
    'FINANCIERO',
    'FACTURACION',
    'Activo',
    'kjmoreira',
    SYSDATE,
    '172.17.0.1',
    NULL,        
    NULL,
    NULL
);    
-- Detalle.
-- Regularización a Cartera Legal.
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
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
) VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO
     FROM   DB_GENERAL.ADMI_PARAMETRO_CAB
     WHERE  NOMBRE_PARAMETRO = 'MOTIVOS_CAMBIO_FORMA_PAGO'
     AND    MODULO  = 'FINANCIERO'
     AND    PROCESO = 'FACTURACION'
     AND    ESTADO  = 'Activo'),
    'PARAMETRO QUE DEFINE EL MOTIVO DEL CAMBIO DE FORMA DE PAGO',    
    'N',
    'Regularizacion a Cartera Legal',
    NULL,
    NULL,
    'Activo',
    'kjmoreira',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    '18'    
  );

-- Archivo de Débitos.
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
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
) VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO
     FROM   DB_GENERAL.ADMI_PARAMETRO_CAB
     WHERE  NOMBRE_PARAMETRO = 'MOTIVOS_CAMBIO_FORMA_PAGO'
     AND    MODULO  = 'FINANCIERO'
     AND    PROCESO = 'FACTURACION'
     AND    ESTADO  = 'Activo'),
    'PARAMETRO QUE DEFINE EL MOTIVO DEL CAMBIO DE FORMA DE PAGO',    
    'N',
    'Regularizacion para envío de debito',
    NULL,
    NULL,
    'Activo',
    'kjmoreira',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    '18'    
  );

-- Mal ingreso de Data.
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
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
) VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO
     FROM   DB_GENERAL.ADMI_PARAMETRO_CAB
     WHERE  NOMBRE_PARAMETRO = 'MOTIVOS_CAMBIO_FORMA_PAGO'
     AND    MODULO  = 'FINANCIERO'
     AND    PROCESO = 'FACTURACION'
     AND    ESTADO  = 'Activo'),
    'PARAMETRO QUE DEFINE EL MOTIVO DEL CAMBIO DE FORMA DE PAGO',    
    'N',
    'Mal ingreso de Data',
    NULL,
    NULL,
    'Activo',
    'kjmoreira',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    '18'    
  );

-- Evitar suspensión clientes VIP/CANAL.
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
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
) VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO
     FROM   DB_GENERAL.ADMI_PARAMETRO_CAB
     WHERE  NOMBRE_PARAMETRO = 'MOTIVOS_CAMBIO_FORMA_PAGO'
     AND    MODULO  = 'FINANCIERO'
     AND    PROCESO = 'FACTURACION'
     AND    ESTADO  = 'Activo'),
    'PARAMETRO QUE DEFINE EL MOTIVO DEL CAMBIO DE FORMA DE PAGO',    
    'N',
    'Evitar suspension clientes VIP/CANAL',
    NULL,
    NULL,
    'Activo',
    'kjmoreira',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    '18'    
  );

-- Solicitado por Entidad Financiera
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
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
) VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO
     FROM   DB_GENERAL.ADMI_PARAMETRO_CAB
     WHERE  NOMBRE_PARAMETRO = 'MOTIVOS_CAMBIO_FORMA_PAGO'
     AND    MODULO  = 'FINANCIERO'
     AND    PROCESO = 'FACTURACION'
     AND    ESTADO  = 'Activo'),
    'PARAMETRO QUE DEFINE EL MOTIVO DEL CAMBIO DE FORMA DE PAGO',    
    'S',
    'Solicitado por Entidad Financiera',
    NULL,
    NULL,
    'Activo',
    'kjmoreira',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    '18'    
  );

-- Solicitado por el Cliente.
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
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
) VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO
     FROM   DB_GENERAL.ADMI_PARAMETRO_CAB
     WHERE  NOMBRE_PARAMETRO = 'MOTIVOS_CAMBIO_FORMA_PAGO'
     AND    MODULO  = 'FINANCIERO'
     AND    PROCESO = 'FACTURACION'
     AND    ESTADO  = 'Activo'),
    'PARAMETRO QUE DEFINE EL MOTIVO DEL CAMBIO DE FORMA DE PAGO',    
    'S',
    'Solicitado por el Cliente',
    NULL,
    NULL,
    'Activo',
    'mhaz',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    '18'    
  );
COMMIT;
/