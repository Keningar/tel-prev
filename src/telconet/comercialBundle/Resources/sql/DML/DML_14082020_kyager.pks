/**
 * Documentación INSERT DE PARÁMETROS de tipos de clientes a considerar en las promociones
 * INSERT de parámetros en la estructura  DB_GENERAL.ADMI_PARAMETRO_CAB y DB_GENERAL.ADMI_PARAMETRO_DET.
 *
 * Se insertan parámetros para los tipos de clientes a considerar en las promociones.
 *
 * @author Katherine Yager <kyager@telconet.ec>
 * @version 1.0 14-08-2020
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
    'TIPO_CLIENTE_PROM',
    'Define los tipos de clientes a considerar en las promociones',
    'COMERCIAL',
    NULL,
    'Activo',
    'kyager',
    SYSDATE,
    '172.17.0.1',
    'kyager',
    SYSDATE,
    '172.17.0.1'
  );
  
  /*********MENSUALIDAD*******/
  
--1 Mensualidad Nuevo
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
      WHERE NOMBRE_PARAMETRO = 'TIPO_CLIENTE_PROM'
      AND ESTADO             = 'Activo'
    ),
    'TIPO_CLIENTE',
    'MENS',
    'Nuevo',
    'Nuevo',
    NULL,
    'Activo',
    'kyager',
    SYSDATE,
    '172.17.0.1',
    'kyager',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18'
  );
  
  --2 Mensualidad Existente
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
      WHERE NOMBRE_PARAMETRO = 'TIPO_CLIENTE_PROM'
      AND ESTADO             = 'Activo'
    ),
    'TIPO_CLIENTE',
    'MENS',
    'Existente',
    'Existente',
    NULL,
    'Activo',
    'kyager',
    SYSDATE,
    '172.17.0.1',
    'kyager',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18'
  );
  
  
--3 Mensualidad Existente Upgrade
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
      WHERE NOMBRE_PARAMETRO = 'TIPO_CLIENTE_PROM'
      AND ESTADO             = 'Activo'
    ),
    'TIPO_CLIENTE',
    'MENS',
    'Existente Upgrade',
    'Upgrade',
    'CambioPlan',
    'Activo',
    'kyager',
    SYSDATE,
    '172.17.0.1',
    'kyager',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18'
  );
  
--4 Mensualidad Existente Downgrade
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
      WHERE NOMBRE_PARAMETRO = 'TIPO_CLIENTE_PROM'
      AND ESTADO             = 'Activo'
    ),
    'TIPO_CLIENTE',
    'MENS',
    'Existente Downgrade',
    'Downgrade',
    'CambioPlan',
    'Activo',
    'kyager',
    SYSDATE,
    '172.17.0.1',
    'kyager',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18'
  );
  
  
/*********ANCHO DE BANDA*******/

--1 Ancho de Banda Nuevo
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
      WHERE NOMBRE_PARAMETRO = 'TIPO_CLIENTE_PROM'
      AND ESTADO             = 'Activo'
    ),
    'TIPO_CLIENTE',
    'BW',
    'Nuevo',
    'Nuevo',
    NULL,
    'Activo',
    'kyager',
    SYSDATE,
    '172.17.0.1',
    'kyager',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18'
  );
  
  --2 Ancho de Banda Existente
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
      WHERE NOMBRE_PARAMETRO = 'TIPO_CLIENTE_PROM'
      AND ESTADO             = 'Activo'
    ),
    'TIPO_CLIENTE',
    'BW',
    'Existente',
    'Existente',
    NULL,
    'Activo',
    'kyager',
    SYSDATE,
    '172.17.0.1',
    'kyager',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18'
  );
    
 --3 Ancho de Banda Existente Upgrade
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
      WHERE NOMBRE_PARAMETRO = 'TIPO_CLIENTE_PROM'
      AND ESTADO             = 'Activo'
    ),
    'TIPO_CLIENTE',
    'BW',
    'Existente Upgrade',
    'Upgrade',
    'CambioPlan',
    'Activo',
    'kyager',
    SYSDATE,
    '172.17.0.1',
    'kyager',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18'
  );
  
--4 Ancho de Banda Existente Downgrade
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
      WHERE NOMBRE_PARAMETRO = 'TIPO_CLIENTE_PROM'
      AND ESTADO             = 'Activo'
    ),
    'TIPO_CLIENTE',
    'BW',
    'Existente Downgrade',
    'Downgrade',
    'CambioPlan',
    'Activo',
    'kyager',
    SYSDATE,
    '172.17.0.1',
    'kyager',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18'
  );   
    
  COMMIT;
/  

