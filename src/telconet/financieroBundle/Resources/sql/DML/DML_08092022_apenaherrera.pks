/** 
 * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
 * @version 1.0 
 * @since 08-09-2022
 * Se crea DML de configuraciones del Proyecto Tarjetas ABU
 */

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
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
    'PARAM_TARJETAS_ABU',
    'Parámetros definidos para el proceso de Tarjetas ABU ',
    'COMERCIAL',
    NULL,
    'Activo',
    'apenaherrera',
    SYSDATE,
    '127.0.0.1',
    'apenaherrera',
    SYSDATE,
    NULL
  );
  
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_TARJETAS_ABU'
      AND ESTADO             = 'Activo'
    ),
    'MENSAJE_OBS_TARJETAS_ABU',
    'COD_FP_ACTUALIZADA', 'ACTUALIZADO', NULL, NULL, NULL, NULL, NULL, 'Activo', 'apenaherrera', SYSDATE, '127.0.0.1', '18',
    'DESCRIPCION: Mensajes parametrizados para el proceso de tarjetas ABU; VALOR1: Codigo de mensaje a presentar; VALOR2: Mensaje a Presentar');   
  
 INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_TARJETAS_ABU'
      AND ESTADO             = 'Activo'
    ),
    'MENSAJE_OBS_TARJETAS_ABU',
    'COD_CLIENTE', 'CLIENTE NO ENCONTRADO', NULL, NULL, NULL, NULL, NULL, 'Activo', 'apenaherrera', SYSDATE, '127.0.0.1', '18',
    'DESCRIPCION: Mensajes parametrizados para el proceso de tarjetas ABU; VALOR1: Codigo de mensaje a presentar; VALOR2: Mensaje a Presentar');   
  
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_TARJETAS_ABU'
      AND ESTADO             = 'Activo'
    ),
    'MENSAJE_OBS_TARJETAS_ABU',
    'COD_FORMA_PAGO', 'CLIENTE YA NO POSEE FORMA DE PAGO TARJETA', NULL, NULL, NULL, NULL, NULL, 'Activo', 'apenaherrera', SYSDATE, '127.0.0.1', '18',
    'DESCRIPCION: Mensajes parametrizados para el proceso de tarjetas ABU; VALOR1: Codigo de mensaje a presentar; VALOR2: Mensaje a Presentar');   
  
   INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_TARJETAS_ABU'
      AND ESTADO             = 'Activo'
    ),
    'MENSAJE_OBS_TARJETAS_ABU',
    'COD_NUM_TARJ_ANTERIOR', 'NO COINCIDE NUMERO DE TARJETA ANTERIOR', NULL, NULL, NULL, NULL, NULL, 'Activo', 'apenaherrera', SYSDATE, '127.0.0.1', '18',
    'DESCRIPCION: Mensajes parametrizados para el proceso de tarjetas ABU; VALOR1: Codigo de mensaje a presentar; VALOR2: Mensaje a Presentar');   
     
   INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_TARJETAS_ABU'
      AND ESTADO             = 'Activo'
    ),
    'MENSAJE_OBS_TARJETAS_ABU',
    'COD_NUM_TARJ_NUEVO', 'BIN ANTERIOR DE TARJETA NO CORRESPONDE AL BIN DEL NUEVO NUMERO DE TARJETA', NULL, NULL, NULL, NULL, NULL, 'Activo', 'apenaherrera', SYSDATE, '127.0.0.1', '18',
    'DESCRIPCION: Mensajes parametrizados para el proceso de tarjetas ABU; VALOR1: Codigo de mensaje a presentar; VALOR2: Mensaje a Presentar');   
  
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_TARJETAS_ABU'
      AND ESTADO             = 'Activo'
    ),
    'MENSAJE_OBS_TARJETAS_ABU',
    'COD_SIN_ARCHIVO', 'No hay archivo que procesar', NULL, NULL, NULL, NULL, NULL, 'Activo', 'apenaherrera', SYSDATE, '127.0.0.1', '18',
    'DESCRIPCION: Mensajes parametrizados para el proceso de tarjetas ABU; VALOR1: Codigo de mensaje a presentar; VALOR2: Mensaje a Presentar');   
  
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_TARJETAS_ABU'
      AND ESTADO             = 'Activo'
    ),
    'MENSAJE_OBS_TARJETAS_ABU',
    'COD_ERROR_EXT', 'Archivo no cumple con la extension xlsx', NULL, NULL, NULL, NULL, NULL, 'Activo', 'apenaherrera', SYSDATE, '127.0.0.1', '18',
    'DESCRIPCION: Mensajes parametrizados para el proceso de tarjetas ABU; VALOR1: Codigo de mensaje a presentar; VALOR2: Mensaje a Presentar');   
  
   INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_TARJETAS_ABU'
      AND ESTADO             = 'Activo'
    ),
    'MENSAJE_OBS_TARJETAS_ABU',
    'COD_FECHA_CAD_NUEVA', 'ERROR EN FECHA DE CADUCIDAD NUEVA, NO PUEDE SER MENOR  AL SYSDATE (YYYYMM)', NULL, NULL, NULL, NULL, NULL, 'Activo', 'apenaherrera', SYSDATE, '127.0.0.1', '18',
    'DESCRIPCION: Mensajes parametrizados para el proceso de tarjetas ABU; VALOR1: Codigo de mensaje a presentar; VALOR2: Mensaje a Presentar');   
  
   INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_TARJETAS_ABU'
      AND ESTADO             = 'Activo'
    ),
    'MENSAJE_OBS_TARJETAS_ABU',
    'COD_FORMATO', 'ERROR EN VALIDACIÓN COLUMNA X AUSENTE O NO CUMPLE CON EL FORMATO', NULL, NULL, NULL, NULL, NULL, 'Activo', 'apenaherrera', SYSDATE, '127.0.0.1', '18',
    'DESCRIPCION: Mensajes parametrizados para el proceso de tarjetas ABU; VALOR1: Codigo de mensaje a presentar; VALOR2: Mensaje a Presentar');   
  
   INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_TARJETAS_ABU'
      AND ESTADO             = 'Activo'
    ),
    'FORMATO_TARJETAS_ABU',
    'CODIGO_COMERCIO', '10980461', NULL, NULL, NULL, NULL, NULL, 'Activo', 'apenaherrera', SYSDATE, '127.0.0.1', '18',
    'DESCRIPCION: Formato parametrizado del archivo en excel de tarjetas ABU; VALOR1: Formato para la columna del archivo en excel de tarjetas ABU');   
  
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_TARJETAS_ABU'
      AND ESTADO             = 'Activo'
    ),
    'FORMATO_TARJETAS_ABU',
    'IDENTIF_CLIENTE_PR', '19', '0', 'L', NULL, NULL, NULL, 'Activo', 'apenaherrera', SYSDATE, '127.0.0.1', '18',
    'DESCRIPCION: Formato parametrizado del archivo en excel de tarjetas ABU; VALOR1: Formato para la columna del archivo en excel de tarjetas ABU');   
  
   INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_TARJETAS_ABU'
      AND ESTADO             = 'Activo'
    ),
    'FORMATO_TARJETAS_ABU',
    'NUMERO_TARJETA_ANTIGUO', '19', '0', 'L', NULL, NULL, NULL, 'Activo', 'apenaherrera', SYSDATE, '127.0.0.1', '18',
    'DESCRIPCION: Formato parametrizado del archivo en excel de tarjetas ABU; VALOR1: Formato para la columna del archivo en excel de tarjetas ABU');   
  
   INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_TARJETAS_ABU'
      AND ESTADO             = 'Activo'
    ),
    'FORMATO_TARJETAS_ABU',
    'NUMERO_TARJETA_NUEVO', '19', '0', 'L', NULL, NULL, NULL, 'Activo', 'apenaherrera', SYSDATE, '127.0.0.1', '18',
    'DESCRIPCION: Formato parametrizado del archivo en excel de tarjetas ABU; VALOR1: Formato para la columna del archivo en excel de tarjetas ABU');   
  
   INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_TARJETAS_ABU'
      AND ESTADO             = 'Activo'
    ),
    'FORMATO_TARJETAS_ABU',
    'FECHA_CADUCIDAD_NUEVA', 'YYYYMM', NULL, NULL, NULL, NULL, NULL, 'Activo', 'apenaherrera', SYSDATE, '127.0.0.1', '18',
    'DESCRIPCION: Formato parametrizado del archivo en excel de tarjetas ABU; VALOR1: Formato para la columna del archivo en excel de tarjetas ABU');   
  
   INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_TARJETAS_ABU'
      AND ESTADO             = 'Activo'
    ),
    'TAREA_CIERRE_ABU',
    'ORIGEN_TAREA', 'Interno', NULL, NULL, NULL, NULL, NULL, 'Activo', 'apenaherrera', SYSDATE, '127.0.0.1', '18',
    'DESCRIPCION: Parametrizacion de la Tarea generada por el proceso ABU; VALOR1: parametrizacion de la columna de la tarea generada por el proceso ABU');   
  
   INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_TARJETAS_ABU'
      AND ESTADO             = 'Activo'
    ),
    'TAREA_CIERRE_ABU',
    'CLASE_TAREA', 'Notificacion que se origina por el Sistema Telcos', NULL, NULL, NULL, NULL, NULL, 'Activo', 'apenaherrera', SYSDATE, '127.0.0.1', '18',
    'DESCRIPCION: Parametrizacion de la Tarea generada por el proceso ABU; VALOR1: parametrizacion de la columna de la tarea generada por el proceso ABU');   
  
   INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_TARJETAS_ABU'
      AND ESTADO             = 'Activo'
    ),
    'TAREA_CIERRE_ABU',
    'GENERA_TAREA', 'Tarea de Cierre automatico', NULL, NULL, NULL, NULL, NULL, 'Activo', 'apenaherrera', SYSDATE, '127.0.0.1', '18',
    'DESCRIPCION: Parametrizacion de la Tarea generada por el proceso ABU; VALOR1: parametrizacion de la columna de la tarea generada por el proceso ABU');   
  
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_TARJETAS_ABU'
      AND ESTADO             = 'Activo'
    ),
    'TAREA_CIERRE_ABU',
    'OBSERVACION_TAREA', 'Actualizacion generada por carga de archivo (nombre_archivo_abu) ABU', NULL, NULL, NULL, NULL, NULL, 'Activo', 'apenaherrera', SYSDATE, '127.0.0.1', '18',
    'DESCRIPCION: Parametrizacion de la Tarea generada por el proceso ABU; VALOR1: parametrizacion de la columna de la tarea generada por el proceso ABU');   
  
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_TARJETAS_ABU'
      AND ESTADO             = 'Activo'
    ),
    'TAREA_CIERRE_ABU',
    'NOMBRE_TAREA', 'Cambiar forma de pago', NULL, NULL, NULL, NULL, NULL, 'Activo', 'apenaherrera', SYSDATE, '127.0.0.1', '18',
    'DESCRIPCION: Parametrizacion de la Tarea generada por el proceso ABU; VALOR1: parametrizacion de la columna de la tarea generada por el proceso ABU');   
  
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_TARJETAS_ABU'
      AND ESTADO             = 'Activo'
    ),
    'USUARIO_TARJETAS_ABU',
    'USUARIO_ABU', 'telcos-abu', NULL, NULL, NULL, NULL, NULL, 'Activo', 'apenaherrera', SYSDATE, '127.0.0.1', '18',
    'DESCRIPCION: Usuario del proceso TARJETAS ABU; VALOR1: Usuario del proceso TARJETAS ABU');   
  
    
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_TARJETAS_ABU'
      AND ESTADO             = 'Activo'
    ),
    'MENSALE_TARJETAS_ABU',
    'MENSAJE', 'Se ha generado un proceso masivo para actualizacion del archivo ABU, una vez procesado le llegara un email.',
     NULL, NULL, NULL, NULL, NULL, 'Activo', 'apenaherrera', SYSDATE, '127.0.0.1', '18',
    'DESCRIPCION: Mensaje de confirmacion de proceso ABU ejecutado; VALOR1: Mensaje'); 
      
      
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_TARJETAS_ABU'
      AND ESTADO             = 'Activo'
    ),
    'EXTENSION_ARCHIVO_ABU',
    'XLSX', 'xlsx',
     NULL, NULL, NULL, NULL, NULL, 'Activo', 'apenaherrera', SYSDATE, '127.0.0.1', '18',
    'DESCRIPCION: Extension valida para el archivo ABU; VALOR1: extension'); 
  
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
      WHERE NOMBRE_PARAMETRO = 'PARAM_TARJETAS_ABU'     
      AND ESTADO             = 'Activo'
    ),
    'CONFIGURACION NFS',
    'TarjetasAbu', 'TelcosWeb', 'ArchivoTarjetasAbu', NULL, 
    'Activo', 'apenaherrera', SYSDATE, '127.0.0.0',
    NULL, NULL, NULL, NULL, '18', NULL, NULL,
    'Configura los parámetros enviados al NFS donde se almacenarán los archivos de tarjetas ABU'
  );
  
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
     DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_TARJETAS_ABU'     
      AND ESTADO             = 'Activo'
    ),
     'PARAMETRO DE HOST PARA OBTENCION DE ARCHIVO',
     'FILE-HTTP-HOST',
     'http://nosites.telconet.ec/archivos',
     '',
     '',
    'Activo',
    'apenaherrera',
     SYSDATE,
    '127.0.0.1',
    '18',
    'Parametro que contiene el host para la obtencion del archivo al NFS'
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
      WHERE NOMBRE_PARAMETRO = 'PARAM_TARJETAS_ABU'
      AND ESTADO             = 'Activo'
    ),
    'MOTIVO_ACTUALIZACION_ABU',
    'Actualización Automática ABU',
    NULL,
    NULL,
    NULL,
    'Activo',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18'
  );
        
   INSERT INTO DB_GENERAL.ADMI_GESTION_DIRECTORIOS
  (
    ID_GESTION_DIRECTORIO,
    CODIGO_APP,
    CODIGO_PATH,
    APLICACION,
    PAIS,
    EMPRESA,
    MODULO,
    SUBMODULO,
    ESTADO,
    FE_CREACION,
    USR_CREACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_GESTION_DIRECTORIOS.nextval,
    4,
    (SELECT MAX(CODIGO_PATH) +1 FROM DB_GENERAL.ADMI_GESTION_DIRECTORIOS),
    'TelcosWeb',
    '593',
    'MD',
    'Financiero',
    'ArchivoTarjetasAbu',
    'Activo',
    sysdate,
    'apenaherrera'
  );
                     

INSERT
INTO DB_GENERAL.ADMI_MOTIVO   
  (
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
  ) 
  VALUES
 (
   DB_GENERAL.SEQ_ADMI_MOTIVO.NEXTVAL,
   2581,
   'Actualización Automática ABU',
   'Inactivo', 
   'apenaherrera',
   SYSDATE,
   'apenaherrera',
   SYSDATE,
   NULL,
   NULL
  ); 
  
COMMIT;
/

  
 
