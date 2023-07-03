-----------------------------------------
--ADMI_CARACTERISTICA FECHA_RECAUDACION--
-----------------------------------------
  INSERT
  INTO DB_COMERCIAL.ADMI_CARACTERISTICA
    (
      ID_CARACTERISTICA,
      DESCRIPCION_CARACTERISTICA,
      TIPO_INGRESO,
      ESTADO,
      FE_CREACION,
      USR_CREACION,
      FE_ULT_MOD,
      USR_ULT_MOD,
      TIPO
    )
    VALUES
    (
      DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
      'FECHA_RECAUDACION',
      'T',
      'Activo',
      SYSDATE,
      'telcos',
      SYSDATE,
      'telcos',
      'FINANCIERO'
    );
    
-----------------------------------------
--ADMI_CARACTERISTICA VALOR_RECAUDACION--
-----------------------------------------
  INSERT
  INTO DB_COMERCIAL.ADMI_CARACTERISTICA
    (
      ID_CARACTERISTICA,
      DESCRIPCION_CARACTERISTICA,
      TIPO_INGRESO,
      ESTADO,
      FE_CREACION,
      USR_CREACION,
      FE_ULT_MOD,
      USR_ULT_MOD,
      TIPO
    )
    VALUES
    (
      DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
      'VALOR_RECAUDACION',
      'T',
      'Activo',
      SYSDATE,
      'telcos',
      SYSDATE,
      'telcos',
      'FINANCIERO'
    );    
     
----------------------------------------------------------
--ADMI_CARACTERISTICA  NUMERO DIAS RECAUDACION EXISTENTE--
----------------------------------------------------------

  INSERT
  INTO DB_COMERCIAL.ADMI_CARACTERISTICA
    (
      ID_CARACTERISTICA,
      DESCRIPCION_CARACTERISTICA,
      TIPO_INGRESO,
      ESTADO,
      FE_CREACION,
      USR_CREACION,
      FE_ULT_MOD,
      USR_ULT_MOD,
      TIPO
    )
    VALUES
    (
      DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
      'NUMERO DIAS RECAUDACION EXISTENTE',
      'T',
      'Activo',
      SYSDATE,
      'telcos',
      SYSDATE,
      'telcos',
      'FINANCIERO'
    ); 
   
 COMMIT;
   
  --- script para dar de baja parámetro existente NUMERO DIAS RECAUDACION EXISTENTE
  
  UPDATE DB_GENERAL.ADMI_PARAMETRO_CAB SET ESTADO = 'Inactivo', USR_ULT_MOD='eholguin', FE_ULT_MOD=SYSDATE WHERE ID_PARAMETRO = 541 AND NOMBRE_PARAMETRO='NUMERO DIAS RECAUDACION EXISTENTE';
   

  ----------------------------------------------------------------------------
  --DB_FINANCIERO.ADMI_CANAL_RECAUDACION   Ingreso de canales de recaudación--
  ----------------------------------------------------------------------------  

  --
  --------------------------------------------------------
  --  DB_FINANCIERO.ADMI_CANAL_RECAUDACION BCO GUAYAQUIL
  -------------------------------------------------------- 

 INSERT
  INTO  DB_FINANCIERO.ADMI_CANAL_RECAUDACION (ID_CANAL_RECAUDACION,
                                              EMPRESA_COD,
                                              BANCO_TIPO_CUENTA_ID,
                                              BANCO_CTA_CONTABLE_ID,
                                              NOMBRE_CANAL_RECAUDACION,
                                              DESCRIPCION_CANAL_RECAUDACION,
                                              ESTADO_CANAL_RECAUDACION,
                                              USR_CREACION,
                                              FE_CREACION,
                                              USR_ULT_MOD,
                                              FE_ULT_MOD,
                                              TITULO_HOJA,
                                              FILA_INICIO,
                                              COL_VALIDACION,
                                              COL_VALOR,
                                              COL_IDENTIFICACION,
                                              COL_FECHA,
                                              COL_REFERENCIA,
                                              COL_RESPUESTA,
                                              SEP_IDENTIFICACION,
                                              PAD_IDENTIFICACION,
                                              REM_IDENTIFICACION,
                                              BATCH_SIZE,
                                              COL_NOMBRE)
    VALUES
    (
      DB_FINANCIERO.SEQ_ADMI_CANAL_RECAUDACION.NEXTVAL ,
      '10',
      '21',
      '44',
      'BANCO GUAYAQUIL',
      'TN - BANCO GUAYAQUIL',
      'Activo',
      'eholguin',
      SYSDATE,
      'eholguin',
      SYSDATE,
      'BANCO GUAYAQUIL',
      '2',
      'A',
      'G',
      'C',
      'H',
      'O',
      'W',
      NULL,
      '0',
      NULL,
      '20',
      'D'  
    );
    
  --------------------------------------------------------
  --  DB_FINANCIERO.ADMI_CANAL_RECAUDACION BCO PICHINCHA
  --------------------------------------------------------   
  INSERT
  INTO  DB_FINANCIERO.ADMI_CANAL_RECAUDACION (ID_CANAL_RECAUDACION,
                                              EMPRESA_COD,
                                              BANCO_TIPO_CUENTA_ID,
                                              BANCO_CTA_CONTABLE_ID,
                                              NOMBRE_CANAL_RECAUDACION,
                                              DESCRIPCION_CANAL_RECAUDACION,
                                              ESTADO_CANAL_RECAUDACION,
                                              USR_CREACION,
                                              FE_CREACION,
                                              USR_ULT_MOD,
                                              FE_ULT_MOD,
                                              TITULO_HOJA,
                                              FILA_INICIO,
                                              COL_VALIDACION,
                                              COL_VALOR,
                                              COL_IDENTIFICACION,
                                              COL_FECHA,
                                              COL_REFERENCIA,
                                              COL_RESPUESTA,
                                              SEP_IDENTIFICACION,
                                              PAD_IDENTIFICACION,
                                              REM_IDENTIFICACION,
                                              BATCH_SIZE,
                                              COL_NOMBRE)
    VALUES
    (
      DB_FINANCIERO.SEQ_ADMI_CANAL_RECAUDACION.NEXTVAL ,
      '10',
      '101',
      '49',
      'BANCO PICHINCHA',
      'TN - BANCO PICHINCHA',
      'Activo',
      'eholguin',
      SYSDATE,
      'eholguin',
      SYSDATE,
      'BANCO PICHINCHA',
      '6',
      'A',
      'H',
      'F',
      'N/A',
      'A',
      'O',
      NULL,
      '0',
      NULL,
      '20',
      'L'  
    );    
    
 
 COMMIT;   
 
 --------------------------------------------------------------------------------------
 -- DB_FINANCIERO.ADMI_CANAL_RECAUDACION_CARACT   CARACTERISTICAS CANALES RECAUDACION--
 --------------------------------------------------------------------------------------   
    
  INSERT INTO DB_FINANCIERO.ADMI_CANAL_RECAUDACION_CARACT
  (
      ID_CANAL_RECAUDACION_CARACT,
      CANAL_RECAUDACION_ID,
      EMPRESA_COD,
      CARACTERISTICA_ID,
      VALOR,
      OBSERVACION,
      ESTADO,
      FE_CREACION,
      USR_CREACION,     
      IP_CREACION,
      FE_ULT_MOD,
      USR_ULT_MOD,     
      IP_ULT_MOD
  )
  VALUES
  (
      DB_FINANCIERO.SEQ_ADMI_CANAL_REC_CARACT.NEXTVAL,
      (
	SELECT ID_CANAL_RECAUDACION
	FROM DB_FINANCIERO.ADMI_CANAL_RECAUDACION
	WHERE DESCRIPCION_CANAL_RECAUDACION = 'TN - BANCO GUAYAQUIL'
      ),
      '10',
      (
	SELECT ID_CARACTERISTICA
	FROM DB_COMERCIAL.ADMI_CARACTERISTICA
	WHERE DESCRIPCION_CARACTERISTICA = 'FECHA_RECAUDACION'
	AND TIPO = 'FINANCIERO'
      ),
      'AAAAMMDD',
      'FORMATO FECHA RECAUDACION TN BCO GUAYAQUIL ANIO MES DIA',
      'Activo',
      SYSDATE,     
      'eholguin',
      '127.0.0.1',
      SYSDATE,     
      'eholguin',
      '127.0.0.1'
  );



  INSERT INTO DB_FINANCIERO.ADMI_CANAL_RECAUDACION_CARACT
  (
      ID_CANAL_RECAUDACION_CARACT,
      CANAL_RECAUDACION_ID,
      EMPRESA_COD,
      CARACTERISTICA_ID,
      VALOR,
      OBSERVACION,
      ESTADO,
      FE_CREACION,
      USR_CREACION,     
      IP_CREACION,
      FE_ULT_MOD,
      USR_ULT_MOD,     
      IP_ULT_MOD
  )
  VALUES
  (
      DB_FINANCIERO.SEQ_ADMI_CANAL_REC_CARACT.NEXTVAL,
      (
	SELECT ID_CANAL_RECAUDACION
	FROM DB_FINANCIERO.ADMI_CANAL_RECAUDACION
	WHERE DESCRIPCION_CANAL_RECAUDACION = 'TN - BANCO GUAYAQUIL'
      ),
      '10',
      (
	SELECT ID_CARACTERISTICA
	FROM DB_COMERCIAL.ADMI_CARACTERISTICA
	WHERE DESCRIPCION_CARACTERISTICA = 'VALOR_RECAUDACION'
	AND TIPO = 'FINANCIERO'
      ),
      '8|2',
      'FORMATO E|D PARA EL CAMPO VALOR RECAUDACION PARA TN BCO GUAYAQUIL DONDE E ES NUMERO DIGITOS PARTE ENTERA Y D NUMERO DIGITOS PARTE DECIMAL',
      'Activo',
      SYSDATE,     
      'eholguin',
      '127.0.0.1',
      SYSDATE,     
      'eholguin',
      '127.0.0.1'
  );
  

  INSERT INTO DB_FINANCIERO.ADMI_CANAL_RECAUDACION_CARACT
  (
      ID_CANAL_RECAUDACION_CARACT,
      CANAL_RECAUDACION_ID,
      EMPRESA_COD,
      CARACTERISTICA_ID,
      VALOR,
      OBSERVACION,
      ESTADO,
      FE_CREACION,
      USR_CREACION,     
      IP_CREACION,
      FE_ULT_MOD,
      USR_ULT_MOD,     
      IP_ULT_MOD
  )
  VALUES
  (
      DB_FINANCIERO.SEQ_ADMI_CANAL_REC_CARACT.NEXTVAL,
      (
	SELECT ID_CANAL_RECAUDACION
	FROM DB_FINANCIERO.ADMI_CANAL_RECAUDACION
	WHERE DESCRIPCION_CANAL_RECAUDACION = 'MD - BANCO PICHINCHA'
      ),
      '18',
      (
	SELECT ID_CARACTERISTICA
	FROM DB_COMERCIAL.ADMI_CARACTERISTICA
	WHERE DESCRIPCION_CARACTERISTICA = 'NUMERO DIAS RECAUDACION EXISTENTE'
	AND TIPO = 'FINANCIERO'
      ),
      '7',
      'NUMERO DIAS RECAUDACION EXISTENTE',
      'Activo',
      SYSDATE,     
      'eholguin',
      '127.0.0.1',
      SYSDATE,     
      'eholguin',
      '127.0.0.1'
  );
  

  
  INSERT INTO DB_FINANCIERO.ADMI_CANAL_RECAUDACION_CARACT
  (
      ID_CANAL_RECAUDACION_CARACT,
      CANAL_RECAUDACION_ID,
      EMPRESA_COD,
      CARACTERISTICA_ID,
      VALOR,
      OBSERVACION,
      ESTADO,
      FE_CREACION,
      USR_CREACION,     
      IP_CREACION,
      FE_ULT_MOD,
      USR_ULT_MOD,     
      IP_ULT_MOD
  )
  VALUES
  (
      DB_FINANCIERO.SEQ_ADMI_CANAL_REC_CARACT.NEXTVAL,
      (
	SELECT ID_CANAL_RECAUDACION
	FROM DB_FINANCIERO.ADMI_CANAL_RECAUDACION
	WHERE DESCRIPCION_CANAL_RECAUDACION = 'TN - BANCO GUAYAQUIL'
      ),
      '10',
      (
	SELECT ID_CARACTERISTICA
	FROM DB_COMERCIAL.ADMI_CARACTERISTICA
	WHERE DESCRIPCION_CARACTERISTICA = 'NUMERO DIAS RECAUDACION EXISTENTE'
	AND TIPO = 'FINANCIERO'
      ),
      '7',
      'NUMERO DIAS RECAUDACION EXISTENTE',
      'Activo',
      SYSDATE,     
      'eholguin',
      '127.0.0.1',
      SYSDATE,     
      'eholguin',
      '127.0.0.1'
  );   
  

  INSERT INTO DB_FINANCIERO.ADMI_CANAL_RECAUDACION_CARACT
  (
      ID_CANAL_RECAUDACION_CARACT,
      CANAL_RECAUDACION_ID,
      EMPRESA_COD,
      CARACTERISTICA_ID,
      VALOR,
      OBSERVACION,
      ESTADO,
      FE_CREACION,
      USR_CREACION,     
      IP_CREACION,
      FE_ULT_MOD,
      USR_ULT_MOD,     
      IP_ULT_MOD
  )
  VALUES
  (
      DB_FINANCIERO.SEQ_ADMI_CANAL_REC_CARACT.NEXTVAL,
      (
	SELECT ID_CANAL_RECAUDACION
	FROM DB_FINANCIERO.ADMI_CANAL_RECAUDACION
	WHERE DESCRIPCION_CANAL_RECAUDACION = 'TN - BANCO PICHINCHA'
      ),
      '10',
      (
	SELECT ID_CARACTERISTICA
	FROM DB_COMERCIAL.ADMI_CARACTERISTICA
	WHERE DESCRIPCION_CARACTERISTICA = 'NUMERO DIAS RECAUDACION EXISTENTE'
	AND TIPO = 'FINANCIERO'
      ),
      '7',
      'NUMERO DIAS RECAUDACION EXISTENTE',
      'Activo',
      SYSDATE,     
      'eholguin',
      '127.0.0.1',
      SYSDATE,     
      'eholguin',
      '127.0.0.1'
  );   
  

  --
  --------------------------------------------------------
  --  DML CABECERA ADMI_FORMATO_RECAUDACION BCO GUAYAQUIL
  -------------------------------------------------------- 
  INSERT INTO DB_FINANCIERO.ADMI_FORMATO_RECAUDACION
  (
      ID_FORMATO_RECAUDACION,
      DESCRIPCION,
      TIPO_CAMPO,
      CONTENIDO,
      LONGITUD,
      CARACTER_RELLENO,
      POSICION,
      ESTADO,
      CANAL_RECAUDACION_ID,
      TIPO_DATO,
      ORIENTACION_CARACTER_RELLENO,
      EMPRESA_COD,
      LONGITUD_TOTAL,
      ES_CABECERA,
      FE_CREACION,
      USR_CREACION,     
      IP_CREACION,
      FE_ULT_MOD,
      USR_ULT_MOD,     
      IP_ULT_MOD
  )
  VALUES
  (
      DB_FINANCIERO.SEQ_ADMI_FORMATO_RECAUDACION.NEXTVAL,
      'Tipo registo: Indica el tipo de registro.  01 Encabezado o Control',
      'F',
      '01',
      2,
      ' ',
      1,
      'Activo',
      (
        SELECT ID_CANAL_RECAUDACION
        FROM DB_FINANCIERO.ADMI_CANAL_RECAUDACION
        WHERE DESCRIPCION_CANAL_RECAUDACION = 'TN - BANCO GUAYAQUIL'
      ),
      'A',
      'D',
      '10',
      124,
      'S',
      SYSDATE,     
      'eholguin',
      '127.0.0.1',
      SYSDATE,     
      'eholguin',
      '127.0.0.1'
  );  
  

  INSERT INTO DB_FINANCIERO.ADMI_FORMATO_RECAUDACION
  (
      ID_FORMATO_RECAUDACION,
      DESCRIPCION,
      TIPO_CAMPO,
      CONTENIDO,
      LONGITUD,
      CARACTER_RELLENO,
      POSICION,
      ESTADO,
      CANAL_RECAUDACION_ID,
      TIPO_DATO,
      ORIENTACION_CARACTER_RELLENO,
      EMPRESA_COD,
      LONGITUD_TOTAL, 
      ES_CABECERA,
      FE_CREACION,
      USR_CREACION,     
      IP_CREACION,
      FE_ULT_MOD,
      USR_ULT_MOD,     
      IP_ULT_MOD
  )
  VALUES
  (
      DB_FINANCIERO.SEQ_ADMI_FORMATO_RECAUDACION.NEXTVAL,
      'Identificacion archivo: Es el tipo de proceso: Recadaciones Empresariales archivo de Cobros',
      'F',
      'REC',
      3,
      ' ',
      3,
      'Activo',
      (
        SELECT ID_CANAL_RECAUDACION
        FROM DB_FINANCIERO.ADMI_CANAL_RECAUDACION
        WHERE DESCRIPCION_CANAL_RECAUDACION = 'TN - BANCO GUAYAQUIL'
      ),
      'A',
      'D',
      '10',
      124,
      'S',
      SYSDATE,     
      'eholguin',
      '127.0.0.1',
      SYSDATE,     
      'eholguin',
      '127.0.0.1'
  );   	 
  
  INSERT INTO DB_FINANCIERO.ADMI_FORMATO_RECAUDACION
  (
      ID_FORMATO_RECAUDACION,
      DESCRIPCION,
      TIPO_CAMPO,
      CONTENIDO,
      LONGITUD,
      CARACTER_RELLENO,
      POSICION,
      ESTADO,
      CANAL_RECAUDACION_ID,
      TIPO_DATO,
      ORIENTACION_CARACTER_RELLENO,
      EMPRESA_COD,
      LONGITUD_TOTAL,
      ES_CABECERA,
      FE_CREACION,
      USR_CREACION,     
      IP_CREACION,
      FE_ULT_MOD,
      USR_ULT_MOD,     
      IP_ULT_MOD
  )
  VALUES
  (
      DB_FINANCIERO.SEQ_ADMI_FORMATO_RECAUDACION.NEXTVAL,
      'Codigo banco: Especifica entidad originadora del archivo, Banco de Guayaquil 00017',
      'F',
      '00017',
      5,
      ' ',
      6,
      'Activo',
      (
        SELECT ID_CANAL_RECAUDACION
        FROM DB_FINANCIERO.ADMI_CANAL_RECAUDACION
        WHERE DESCRIPCION_CANAL_RECAUDACION = 'TN - BANCO GUAYAQUIL'
      ),
      'A',
      'D',
      '10',
      124,
      'S',
      SYSDATE,     
      'eholguin',
      '127.0.0.1',
      SYSDATE,     
      'eholguin',
      '127.0.0.1'
  );   	  
  
  
  
  INSERT INTO DB_FINANCIERO.ADMI_FORMATO_RECAUDACION
  (
      ID_FORMATO_RECAUDACION,
      DESCRIPCION,
      TIPO_CAMPO,
      CONTENIDO,
      LONGITUD,
      CARACTER_RELLENO,
      POSICION,
      ESTADO,
      CANAL_RECAUDACION_ID,
      TIPO_DATO,
      ORIENTACION_CARACTER_RELLENO,
      EMPRESA_COD,
      LONGITUD_TOTAL,
      ES_CABECERA,
      FE_CREACION,
      USR_CREACION,     
      IP_CREACION,
      FE_ULT_MOD,
      USR_ULT_MOD,     
      IP_ULT_MOD
  )
  VALUES
  (
      DB_FINANCIERO.SEQ_ADMI_FORMATO_RECAUDACION.NEXTVAL,
      'Cod empresa: Entidad receptora del archivo, codigo empresa, lo suministra el banco.',
      'F',
      'CJ0',
      5,
      ' ',
      11,
      'Activo',
      (
        SELECT ID_CANAL_RECAUDACION
        FROM DB_FINANCIERO.ADMI_CANAL_RECAUDACION
        WHERE DESCRIPCION_CANAL_RECAUDACION = 'TN - BANCO GUAYAQUIL'
      ),
      'A',
      'D',
      '10',
      124,
      'S',
      SYSDATE,     
      'eholguin',
      '127.0.0.1',
      SYSDATE,     
      'eholguin',
      '127.0.0.1'
  ); 
  
  INSERT INTO DB_FINANCIERO.ADMI_FORMATO_RECAUDACION
  (
      ID_FORMATO_RECAUDACION,
      DESCRIPCION,
      TIPO_CAMPO,
      CONTENIDO,
      LONGITUD,
      CARACTER_RELLENO,
      POSICION,
      ESTADO,
      CANAL_RECAUDACION_ID,
      TIPO_DATO,
      ORIENTACION_CARACTER_RELLENO,
      EMPRESA_COD,
      LONGITUD_TOTAL,
      ES_CABECERA,
      FE_CREACION,
      USR_CREACION,     
      IP_CREACION,
      FE_ULT_MOD,
      USR_ULT_MOD,     
      IP_ULT_MOD
  )
  VALUES
  (
      DB_FINANCIERO.SEQ_ADMI_FORMATO_RECAUDACION.NEXTVAL,
      'Contenido archivo: Indica que es un archivo de Cobros o de facturación con novedades',
      'F',
      '01',
      2,
      '0',
      16,
      'Activo',
      (
        SELECT ID_CANAL_RECAUDACION
        FROM DB_FINANCIERO.ADMI_CANAL_RECAUDACION
        WHERE DESCRIPCION_CANAL_RECAUDACION = 'TN - BANCO GUAYAQUIL'
      ),
      'N',
      'I',
      '10',
      124,
      'S',
      SYSDATE,     
      'eholguin',
      '127.0.0.1',
      SYSDATE,     
      'eholguin',
      '127.0.0.1'
  );
  
  INSERT INTO DB_FINANCIERO.ADMI_FORMATO_RECAUDACION
  (
      ID_FORMATO_RECAUDACION,
      DESCRIPCION,
      TIPO_CAMPO,
      CONTENIDO,
      LONGITUD,
      CARACTER_RELLENO,
      POSICION,
      ESTADO,
      CANAL_RECAUDACION_ID,
      TIPO_DATO,
      ORIENTACION_CARACTER_RELLENO,
      EMPRESA_COD,
      LONGITUD_TOTAL,
      ES_CABECERA,
      FE_CREACION,
      USR_CREACION,     
      IP_CREACION,
      FE_ULT_MOD,
      USR_ULT_MOD,     
      IP_ULT_MOD
  )
  VALUES
  (
      DB_FINANCIERO.SEQ_ADMI_FORMATO_RECAUDACION.NEXTVAL,
      'Fecha generacion: Fecha en la cual la Empresa genera el archivo',
      'V',
      'AAAAMMDD',
      8,
      '0',
      18,
      'Activo',
      (
        SELECT ID_CANAL_RECAUDACION
        FROM DB_FINANCIERO.ADMI_CANAL_RECAUDACION
        WHERE DESCRIPCION_CANAL_RECAUDACION = 'TN - BANCO GUAYAQUIL'
      ),
      'N',
      'I',
      '10',
      124,
      'S',
      SYSDATE,     
      'eholguin',
      '127.0.0.1',
      SYSDATE,     
      'eholguin',
      '127.0.0.1'
  );   
  
  INSERT INTO DB_FINANCIERO.ADMI_FORMATO_RECAUDACION
  (
      ID_FORMATO_RECAUDACION,
      DESCRIPCION,
      TIPO_CAMPO,
      CONTENIDO,
      LONGITUD,
      CARACTER_RELLENO,
      POSICION,
      ESTADO,
      CANAL_RECAUDACION_ID,
      TIPO_DATO,
      ORIENTACION_CARACTER_RELLENO,
      EMPRESA_COD,
      LONGITUD_TOTAL,
      ES_CABECERA,
      FE_CREACION,
      USR_CREACION,     
      IP_CREACION,
      FE_ULT_MOD,
      USR_ULT_MOD,     
      IP_ULT_MOD
  )
  VALUES
  (
      DB_FINANCIERO.SEQ_ADMI_FORMATO_RECAUDACION.NEXTVAL,
      'Fecha aplicacion: Fecha en la cual se debe cargar el archivo en el sistema del banco',
      'V',
      'AAAAMMDD',
      8,
      '0',
      26,
      'Activo',
      (
        SELECT ID_CANAL_RECAUDACION
        FROM DB_FINANCIERO.ADMI_CANAL_RECAUDACION
        WHERE DESCRIPCION_CANAL_RECAUDACION = 'TN - BANCO GUAYAQUIL'
      ),
      'N',
      'I',
      '10',
      124,
      'S',
      SYSDATE,     
      'eholguin',
      '127.0.0.1',
      SYSDATE,     
      'eholguin',
      '127.0.0.1'
  );
  
  INSERT INTO DB_FINANCIERO.ADMI_FORMATO_RECAUDACION
  (
      ID_FORMATO_RECAUDACION,
      DESCRIPCION,
      TIPO_CAMPO,
      CONTENIDO,
      LONGITUD,
      CARACTER_RELLENO,
      POSICION,
      ESTADO,
      CANAL_RECAUDACION_ID,
      TIPO_DATO,
      ORIENTACION_CARACTER_RELLENO,
      EMPRESA_COD,
      LONGITUD_TOTAL,
      ES_CABECERA,
      FE_CREACION,
      USR_CREACION,     
      IP_CREACION,
      FE_ULT_MOD,
      USR_ULT_MOD,     
      IP_ULT_MOD
  )
  VALUES
  (
      DB_FINANCIERO.SEQ_ADMI_FORMATO_RECAUDACION.NEXTVAL,
      'Numero total de registros tipo detalle enviados en el archivo.',
      'V',
      'TR',
      8,
      '0',
      34,
      'Activo',
      (
        SELECT ID_CANAL_RECAUDACION
        FROM DB_FINANCIERO.ADMI_CANAL_RECAUDACION
        WHERE DESCRIPCION_CANAL_RECAUDACION = 'TN - BANCO GUAYAQUIL'
      ),
      'N',
      'I',
      '10',
      124,
      'S',
      SYSDATE,     
      'eholguin',
      '127.0.0.1',
      SYSDATE,     
      'eholguin',
      '127.0.0.1'
  );  
  
  INSERT INTO DB_FINANCIERO.ADMI_FORMATO_RECAUDACION
  (
      ID_FORMATO_RECAUDACION,
      DESCRIPCION,
      TIPO_CAMPO,
      CONTENIDO,
      LONGITUD,
      CARACTER_RELLENO,
      POSICION,
      ESTADO,
      CANAL_RECAUDACION_ID,
      TIPO_DATO,
      ORIENTACION_CARACTER_RELLENO,
      EMPRESA_COD,
      LONGITUD_TOTAL,
      ES_CABECERA,
      FE_CREACION,
      USR_CREACION,     
      IP_CREACION,
      FE_ULT_MOD,
      USR_ULT_MOD,     
      IP_ULT_MOD
  )
  VALUES
  (
      DB_FINANCIERO.SEQ_ADMI_FORMATO_RECAUDACION.NEXTVAL,
      'Valor total de todos los registros de cobros. 13 enteros y 2 decimales.',
      'V',
      'VT|13|2',
      15,
      '0',
      42,
      'Activo',
      (
        SELECT ID_CANAL_RECAUDACION
        FROM DB_FINANCIERO.ADMI_CANAL_RECAUDACION
        WHERE DESCRIPCION_CANAL_RECAUDACION = 'TN - BANCO GUAYAQUIL'
      ),
      'N',
      'I',
      '10',
      124,
      'S',
      SYSDATE,     
      'eholguin',
      '127.0.0.1',
      SYSDATE,     
      'eholguin',
      '127.0.0.1'
  ); 
  
  
  INSERT INTO DB_FINANCIERO.ADMI_FORMATO_RECAUDACION
  (
      ID_FORMATO_RECAUDACION,
      DESCRIPCION,
      TIPO_CAMPO,
      CONTENIDO,
      LONGITUD,
      CARACTER_RELLENO,
      POSICION,
      ESTADO,
      CANAL_RECAUDACION_ID,
      TIPO_DATO,
      ORIENTACION_CARACTER_RELLENO,
      EMPRESA_COD,
      LONGITUD_TOTAL,
      ES_CABECERA,
      FE_CREACION,
      USR_CREACION,     
      IP_CREACION,
      FE_ULT_MOD,
      USR_ULT_MOD,     
      IP_ULT_MOD
  )
  VALUES
  (
      DB_FINANCIERO.SEQ_ADMI_FORMATO_RECAUDACION.NEXTVAL,
      'Espacios.  Valores en blanco',
      'F',
      ' ',
      68,
      ' ',
      57,
      'Activo',
      (
        SELECT ID_CANAL_RECAUDACION
        FROM DB_FINANCIERO.ADMI_CANAL_RECAUDACION
        WHERE DESCRIPCION_CANAL_RECAUDACION = 'TN - BANCO GUAYAQUIL'
      ),
      'A',
      'D',
      '10',
      124,
      'S',
      SYSDATE,     
      'eholguin',
      '127.0.0.1',
      SYSDATE,     
      'eholguin',
      '127.0.0.1'
  );   

  --------------------------------------------------------
  --  DML DETALLE ADMI_FORMATO_RECAUDACION BCO GUAYAQUIL
  --------------------------------------------------------   
  INSERT INTO DB_FINANCIERO.ADMI_FORMATO_RECAUDACION
  (
      ID_FORMATO_RECAUDACION,
      DESCRIPCION,
      TIPO_CAMPO,
      CONTENIDO,
      LONGITUD,
      CARACTER_RELLENO,
      POSICION,
      ESTADO,
      CANAL_RECAUDACION_ID,
      TIPO_DATO,
      ORIENTACION_CARACTER_RELLENO,
      EMPRESA_COD,
      LONGITUD_TOTAL,
      ES_CABECERA,
      FE_CREACION,
      USR_CREACION,     
      IP_CREACION,
      FE_ULT_MOD,
      USR_ULT_MOD,     
      IP_ULT_MOD
  )
  VALUES
  (
      DB_FINANCIERO.SEQ_ADMI_FORMATO_RECAUDACION.NEXTVAL,
      'Tipo registo: Indica el tipo de registro.  02 Registro de detalle',
      'F',
      '02',
      2,
      ' ',
      1,
      'Activo',
      (
        SELECT ID_CANAL_RECAUDACION
        FROM DB_FINANCIERO.ADMI_CANAL_RECAUDACION
        WHERE DESCRIPCION_CANAL_RECAUDACION = 'TN - BANCO GUAYAQUIL'
      ),
      'A',
      'D',
      '10',
      124,
      'N',
      SYSDATE,     
      'eholguin',
      '127.0.0.1',
      SYSDATE,     
      'eholguin',
      '127.0.0.1'
  );
  
  INSERT INTO DB_FINANCIERO.ADMI_FORMATO_RECAUDACION
  (
      ID_FORMATO_RECAUDACION,
      DESCRIPCION,
      TIPO_CAMPO,
      CONTENIDO,
      LONGITUD,
      CARACTER_RELLENO,
      POSICION,
      ESTADO,
      CANAL_RECAUDACION_ID,
      TIPO_DATO,
      ORIENTACION_CARACTER_RELLENO,
      EMPRESA_COD,
      LONGITUD_TOTAL,
      ES_CABECERA,
      FE_CREACION,
      USR_CREACION,     
      IP_CREACION,
      FE_ULT_MOD,
      USR_ULT_MOD,     
      IP_ULT_MOD
  )
  VALUES
  (
      DB_FINANCIERO.SEQ_ADMI_FORMATO_RECAUDACION.NEXTVAL,
      'Novedad: Tipo de novedad que afecta al registro.  01: Ingreso.  02: Modificación  03: Cancelar pago.',
      'F',
      '01',
      2,
      ' ',
      3,
      'Activo',
      (
        SELECT ID_CANAL_RECAUDACION
        FROM DB_FINANCIERO.ADMI_CANAL_RECAUDACION
        WHERE DESCRIPCION_CANAL_RECAUDACION = 'TN - BANCO GUAYAQUIL'
      ),
      'A',
      'D',
      '10',
      124,
      'N',
      SYSDATE,     
      'eholguin',
      '127.0.0.1',
      SYSDATE,     
      'eholguin',
      '127.0.0.1'
  );
  
  INSERT INTO DB_FINANCIERO.ADMI_FORMATO_RECAUDACION
  (
      ID_FORMATO_RECAUDACION,
      DESCRIPCION,
      TIPO_CAMPO,
      CONTENIDO,
      LONGITUD,
      CARACTER_RELLENO,
      POSICION,
      ESTADO,
      CANAL_RECAUDACION_ID,
      TIPO_DATO,
      ORIENTACION_CARACTER_RELLENO,
      EMPRESA_COD,
      LONGITUD_TOTAL,
      ES_CABECERA,
      FE_CREACION,
      USR_CREACION,     
      IP_CREACION,
      FE_ULT_MOD,
      USR_ULT_MOD,     
      IP_ULT_MOD
  )
  VALUES
  (
      DB_FINANCIERO.SEQ_ADMI_FORMATO_RECAUDACION.NEXTVAL,
      'Obligacion Cliente: Identificacion u obligacion del cliente ante la empresa.',
      'V',
      'IDT',
      15,
      ' ',
      5,
      'Activo',
      (
        SELECT ID_CANAL_RECAUDACION
        FROM DB_FINANCIERO.ADMI_CANAL_RECAUDACION
        WHERE DESCRIPCION_CANAL_RECAUDACION = 'TN - BANCO GUAYAQUIL'
      ),
      'A',
      'D',
      '10',
      124,
      'N',
      SYSDATE,     
      'eholguin',
      '127.0.0.1',
      SYSDATE,     
      'eholguin',
      '127.0.0.1'
  );
  
  INSERT INTO DB_FINANCIERO.ADMI_FORMATO_RECAUDACION
  (
      ID_FORMATO_RECAUDACION,
      DESCRIPCION,
      TIPO_CAMPO,
      CONTENIDO,
      LONGITUD,
      CARACTER_RELLENO,
      POSICION,
      ESTADO,
      CANAL_RECAUDACION_ID,
      TIPO_DATO,
      ORIENTACION_CARACTER_RELLENO,
      EMPRESA_COD,
      LONGITUD_TOTAL,
      ES_CABECERA,
      FE_CREACION,
      USR_CREACION,     
      IP_CREACION,
      FE_ULT_MOD,
      USR_ULT_MOD,     
      IP_ULT_MOD
  )
  VALUES
  (
      DB_FINANCIERO.SEQ_ADMI_FORMATO_RECAUDACION.NEXTVAL,
      'Nombre: Nombre del propietario del bien o del servicio ante la empresa.',
      'V',
      'NOM',
      40,
      ' ',
      20,
      'Activo',
      (
        SELECT ID_CANAL_RECAUDACION
        FROM DB_FINANCIERO.ADMI_CANAL_RECAUDACION
        WHERE DESCRIPCION_CANAL_RECAUDACION = 'TN - BANCO GUAYAQUIL'
      ),
      'A',
      'D',
      '10',
      124,
      'N',
      SYSDATE,     
      'eholguin',
      '127.0.0.1',
      SYSDATE,     
      'eholguin',
      '127.0.0.1'
  );  
  
  INSERT INTO DB_FINANCIERO.ADMI_FORMATO_RECAUDACION
  (
      ID_FORMATO_RECAUDACION,
      DESCRIPCION,
      TIPO_CAMPO,
      CONTENIDO,
      LONGITUD,
      CARACTER_RELLENO,
      POSICION,
      ESTADO,
      CANAL_RECAUDACION_ID,
      TIPO_DATO,
      ORIENTACION_CARACTER_RELLENO,
      EMPRESA_COD,
      LONGITUD_TOTAL,
      ES_CABECERA,
      FE_CREACION,
      USR_CREACION,     
      IP_CREACION,
      FE_ULT_MOD,
      USR_ULT_MOD,     
      IP_ULT_MOD
  )
  VALUES
  (
      DB_FINANCIERO.SEQ_ADMI_FORMATO_RECAUDACION.NEXTVAL, 
      'Valor cobro: Valor total de la factura.  8 enteros, 2 decimales.',
      'V',
      '8|2',
      10,
      '0',
      60,
      'Activo',
      (
        SELECT ID_CANAL_RECAUDACION
        FROM DB_FINANCIERO.ADMI_CANAL_RECAUDACION
        WHERE DESCRIPCION_CANAL_RECAUDACION = 'TN - BANCO GUAYAQUIL'
      ),
      'N',
      'I',
      '10',
      124,
      'N',
      SYSDATE,     
      'eholguin',
      '127.0.0.1',
      SYSDATE,     
      'eholguin',
      '127.0.0.1'
  );
  
  INSERT INTO DB_FINANCIERO.ADMI_FORMATO_RECAUDACION
  (
      ID_FORMATO_RECAUDACION,
      DESCRIPCION,
      TIPO_CAMPO,
      CONTENIDO,
      LONGITUD,
      CARACTER_RELLENO,
      POSICION,
      ESTADO,
      CANAL_RECAUDACION_ID,
      TIPO_DATO,
      ORIENTACION_CARACTER_RELLENO,
      EMPRESA_COD,
      LONGITUD_TOTAL,
      ES_CABECERA,
      FE_CREACION,
      USR_CREACION,     
      IP_CREACION,
      FE_ULT_MOD,
      USR_ULT_MOD,     
      IP_ULT_MOD
  )
  VALUES
  (
      DB_FINANCIERO.SEQ_ADMI_FORMATO_RECAUDACION.NEXTVAL,
      'Fecha Maxima de pago: Fecha maxima de pago en Banco',
      'V',
      'AAAAMMDD',
      8,
      '0',
      70,
      'Activo',
      (
        SELECT ID_CANAL_RECAUDACION 
        FROM DB_FINANCIERO.ADMI_CANAL_RECAUDACION
        WHERE DESCRIPCION_CANAL_RECAUDACION = 'TN - BANCO GUAYAQUIL'
      ),
      'N',
      'I',
      '10',
      124,
      'N',
      SYSDATE,     
      'eholguin',
      '127.0.0.1',
      SYSDATE,     
      'eholguin',
      '127.0.0.1'
  );
  	
	
  INSERT INTO DB_FINANCIERO.ADMI_FORMATO_RECAUDACION
  (
      ID_FORMATO_RECAUDACION,
      DESCRIPCION,
      TIPO_CAMPO,
      CONTENIDO,
      LONGITUD,
      CARACTER_RELLENO,
      POSICION,
      ESTADO,
      CANAL_RECAUDACION_ID,
      TIPO_DATO,
      ORIENTACION_CARACTER_RELLENO,
      EMPRESA_COD,
      LONGITUD_TOTAL,
      ES_CABECERA,
      FE_CREACION,
      USR_CREACION,     
      IP_CREACION,
      FE_ULT_MOD,
      USR_ULT_MOD,     
      IP_ULT_MOD
  )
  VALUES
  (
      DB_FINANCIERO.SEQ_ADMI_FORMATO_RECAUDACION.NEXTVAL,
      'Valor minimo: Valor de pago mínimo.  8 enteros, 2 decimales.',
      'F',
      '0000000000',
      10,
      '0',
      78,
      'Activo',
      (
        SELECT ID_CANAL_RECAUDACION
        FROM DB_FINANCIERO.ADMI_CANAL_RECAUDACION
        WHERE DESCRIPCION_CANAL_RECAUDACION = 'TN - BANCO GUAYAQUIL'
      ),
      'N',
      'I',
      '10',
      124,
      'N',
      SYSDATE,     
      'eholguin',
      '127.0.0.1',
      SYSDATE,     
      'eholguin',
      '127.0.0.1'
  );
  
  INSERT INTO DB_FINANCIERO.ADMI_FORMATO_RECAUDACION
  (
      ID_FORMATO_RECAUDACION,
      DESCRIPCION,
      TIPO_CAMPO,
      CONTENIDO,
      LONGITUD,
      CARACTER_RELLENO,
      POSICION,
      ESTADO,
      CANAL_RECAUDACION_ID,
      TIPO_DATO,
      ORIENTACION_CARACTER_RELLENO,
      EMPRESA_COD,
      LONGITUD_TOTAL,
      ES_CABECERA,
      FE_CREACION,
      USR_CREACION,     
      IP_CREACION,
      FE_ULT_MOD,
      USR_ULT_MOD,     
      IP_ULT_MOD
  )
  VALUES
  (
      DB_FINANCIERO.SEQ_ADMI_FORMATO_RECAUDACION.NEXTVAL,
      'Valor RET: Valor con Base imponible o con Vlr a retener.Llenar con ceros en caso de no enviar el valor.',
      'F',
      '0000000000',
      10,
      '0',
      88,
      'Activo',
      (
	SELECT ID_CANAL_RECAUDACION
	FROM DB_FINANCIERO.ADMI_CANAL_RECAUDACION
	WHERE DESCRIPCION_CANAL_RECAUDACION = 'TN - BANCO GUAYAQUIL'
      ),
      'N',
      'I',
      '10',
      124,
      'N',
      SYSDATE,     
      'eholguin',
      '127.0.0.1',
      SYSDATE,     
      'eholguin',
      '127.0.0.1'
  );
  
  INSERT INTO DB_FINANCIERO.ADMI_FORMATO_RECAUDACION
  (
      ID_FORMATO_RECAUDACION,
      DESCRIPCION,
      TIPO_CAMPO,
      CONTENIDO,
      LONGITUD,
      CARACTER_RELLENO,
      POSICION,
      ESTADO,
      CANAL_RECAUDACION_ID,
      TIPO_DATO,
      ORIENTACION_CARACTER_RELLENO,
      EMPRESA_COD,
      LONGITUD_TOTAL,
      ES_CABECERA,
      FE_CREACION,
      USR_CREACION,     
      IP_CREACION,
      FE_ULT_MOD,
      USR_ULT_MOD,     
      IP_ULT_MOD
  )
  VALUES
  (
      DB_FINANCIERO.SEQ_ADMI_FORMATO_RECAUDACION.NEXTVAL,
      'Referencia: Podra contener codigo del cliente, nro de cédula, RUC.',
      'V',
      'IDT',
      15,
      ' ',
      98,
      'Activo',
      (
        SELECT ID_CANAL_RECAUDACION
        FROM DB_FINANCIERO.ADMI_CANAL_RECAUDACION
        WHERE DESCRIPCION_CANAL_RECAUDACION = 'TN - BANCO GUAYAQUIL'
      ),
      'A',
      'D',
      '10',
      124,
      'N',
      SYSDATE,     
      'eholguin',
      '127.0.0.1',
      SYSDATE,     
      'eholguin',
      '127.0.0.1'
  );
  
  
  INSERT INTO DB_FINANCIERO.ADMI_FORMATO_RECAUDACION
  (
      ID_FORMATO_RECAUDACION,
      DESCRIPCION,
      TIPO_CAMPO,
      CONTENIDO,
      LONGITUD,
      CARACTER_RELLENO,
      POSICION,
      ESTADO,
      CANAL_RECAUDACION_ID,
      TIPO_DATO,
      ORIENTACION_CARACTER_RELLENO,
      EMPRESA_COD,
      LONGITUD_TOTAL,
      ES_CABECERA,
      FE_CREACION,
      USR_CREACION,     
      IP_CREACION,
      FE_ULT_MOD,
      USR_ULT_MOD,     
      IP_ULT_MOD
  )
  VALUES
  (
      DB_FINANCIERO.SEQ_ADMI_FORMATO_RECAUDACION.NEXTVAL,
      'Periodo: Indica el periodo de recaudacion. Formado por el anio y el mes.',
      'V',
      'AAAAMM',
      6,
      '0',
      113,
      'Activo',
      (
	SELECT ID_CANAL_RECAUDACION
	FROM DB_FINANCIERO.ADMI_CANAL_RECAUDACION
	WHERE DESCRIPCION_CANAL_RECAUDACION = 'TN - BANCO GUAYAQUIL'
      ),
      'N',
      'I',
      '10',
      124,
      'N',
      SYSDATE,     
      'eholguin',
      '127.0.0.1',
      SYSDATE,     
      'eholguin',
      '127.0.0.1'
  );
  
  INSERT INTO DB_FINANCIERO.ADMI_FORMATO_RECAUDACION
  (
      ID_FORMATO_RECAUDACION,
      DESCRIPCION,
      TIPO_CAMPO,
      CONTENIDO,
      LONGITUD,
      CARACTER_RELLENO,
      POSICION,
      ESTADO,
      CANAL_RECAUDACION_ID,
      TIPO_DATO,
      ORIENTACION_CARACTER_RELLENO,
      EMPRESA_COD,
      LONGITUD_TOTAL,
      ES_CABECERA,
      FE_CREACION,
      USR_CREACION,     
      IP_CREACION,
      FE_ULT_MOD,
      USR_ULT_MOD,     
      IP_ULT_MOD
  )
  VALUES
  (
      DB_FINANCIERO.SEQ_ADMI_FORMATO_RECAUDACION.NEXTVAL,
      'Secuencia periodo: Es la secuencia de la Obligación dentro del periodo. Inicia en 01.',
      'V',
      'DD',
      2,
      '0',
      119,
      'Activo',
      (
        SELECT ID_CANAL_RECAUDACION
        FROM DB_FINANCIERO.ADMI_CANAL_RECAUDACION
        WHERE DESCRIPCION_CANAL_RECAUDACION = 'TN - BANCO GUAYAQUIL'
      ),
      'N',
      'I',
      '10',
      124,
      'N',
      SYSDATE,     
      'eholguin',
      '127.0.0.1',
      SYSDATE,     
      'eholguin',
      '127.0.0.1'
  );
  
  
  INSERT INTO DB_FINANCIERO.ADMI_FORMATO_RECAUDACION
  (
      ID_FORMATO_RECAUDACION,
      DESCRIPCION,
      TIPO_CAMPO,
      CONTENIDO,
      LONGITUD,
      CARACTER_RELLENO,
      POSICION,
      ESTADO,
      CANAL_RECAUDACION_ID,
      TIPO_DATO,
      ORIENTACION_CARACTER_RELLENO,
      EMPRESA_COD,
      LONGITUD_TOTAL,
      ES_CABECERA,
      FE_CREACION,
      USR_CREACION,     
      IP_CREACION,
      FE_ULT_MOD,
      USR_ULT_MOD,     
      IP_ULT_MOD
  )
  VALUES
  (
      DB_FINANCIERO.SEQ_ADMI_FORMATO_RECAUDACION.NEXTVAL,
      'Espacios:  Valores en blanco.',
      'F',
      '    ',
      4,
      ' ',
      121,
      'Activo',
      (
	SELECT ID_CANAL_RECAUDACION
	FROM DB_FINANCIERO.ADMI_CANAL_RECAUDACION
	WHERE DESCRIPCION_CANAL_RECAUDACION = 'TN - BANCO GUAYAQUIL'
      ),
      'A',
      'D',
      '10',
      124,
      'N',
      SYSDATE,     
      'eholguin',
      '127.0.0.1',
      SYSDATE,     
      'eholguin',
      '127.0.0.1'
  );
  
  --------------------------------------------------------
  --  DML DETALLES ADMI_FORMATO_RECAUDACION BCO PICHINCHA
  --------------------------------------------------------   
  
  INSERT INTO DB_FINANCIERO.ADMI_FORMATO_RECAUDACION
  (
      ID_FORMATO_RECAUDACION,
      DESCRIPCION,
      TIPO_CAMPO,
      CONTENIDO,
      LONGITUD,
      CARACTER_RELLENO,
      POSICION,
      ESTADO,
      CANAL_RECAUDACION_ID,
      TIPO_DATO,
      ORIENTACION_CARACTER_RELLENO,
      EMPRESA_COD,
      LONGITUD_TOTAL,
      ES_CABECERA,
      FE_CREACION,
      USR_CREACION,     
      IP_CREACION,
      FE_ULT_MOD,
      USR_ULT_MOD,     
      IP_ULT_MOD
  )
  VALUES
  (
      DB_FINANCIERO.SEQ_ADMI_FORMATO_RECAUDACION.NEXTVAL,
      'Codigo orientacion: Indica el Codigo del Servicio. CO para Cobro, PA para Pago',
      'F',
      'CO',
      2,
      ' ',
      1,
      'Activo',
      (
        SELECT ID_CANAL_RECAUDACION
        FROM DB_FINANCIERO.ADMI_CANAL_RECAUDACION
        WHERE DESCRIPCION_CANAL_RECAUDACION = 'TN - BANCO PICHINCHA'
      ),
      'A',
      'D',
      '10',
      174,
      'N',
      SYSDATE,     
      'eholguin',
      '127.0.0.1',
      SYSDATE,     
      'eholguin',
      '127.0.0.1'
  );  
  
  
  INSERT INTO DB_FINANCIERO.ADMI_FORMATO_RECAUDACION
  (
      ID_FORMATO_RECAUDACION,
      DESCRIPCION,
      TIPO_CAMPO,
      CONTENIDO,
      LONGITUD,
      CARACTER_RELLENO,
      POSICION,
      ESTADO,
      CANAL_RECAUDACION_ID,
      TIPO_DATO,
      ORIENTACION_CARACTER_RELLENO,
      EMPRESA_COD,
      LONGITUD_TOTAL,
      ES_CABECERA,
      FE_CREACION,
      USR_CREACION,     
      IP_CREACION,
      FE_ULT_MOD,
      USR_ULT_MOD,     
      IP_ULT_MOD
  )
  VALUES
  (
      DB_FINANCIERO.SEQ_ADMI_FORMATO_RECAUDACION.NEXTVAL,
      'Contrapartida: Identifica el: Codigo de cliente, Numero de Medidor, Numero Contrato, Codigo de Beneficiario.',
      'V',
      'IDT',
      20,
      ' ',
      3,
      'Activo',
      (
        SELECT ID_CANAL_RECAUDACION
        FROM DB_FINANCIERO.ADMI_CANAL_RECAUDACION
        WHERE DESCRIPCION_CANAL_RECAUDACION = 'TN - BANCO PICHINCHA'
      ),
      'A',
      'D',
      '10',
      174,
      'N',
      SYSDATE,     
      'eholguin',
      '127.0.0.1',
      SYSDATE,     
      'eholguin',
      '127.0.0.1'
  );
  
  
  INSERT INTO DB_FINANCIERO.ADMI_FORMATO_RECAUDACION
  (
      ID_FORMATO_RECAUDACION,
      DESCRIPCION,
      TIPO_CAMPO,
      CONTENIDO,
      LONGITUD,
      CARACTER_RELLENO,
      POSICION,
      ESTADO,
      CANAL_RECAUDACION_ID,
      TIPO_DATO,
      ORIENTACION_CARACTER_RELLENO,
      EMPRESA_COD,
      LONGITUD_TOTAL,
      ES_CABECERA,
      FE_CREACION,
      USR_CREACION,     
      IP_CREACION,
      FE_ULT_MOD,
      USR_ULT_MOD,     
      IP_ULT_MOD
  )
  VALUES
  (
      DB_FINANCIERO.SEQ_ADMI_FORMATO_RECAUDACION.NEXTVAL,
      'Moneda: Codigo de Moneda del Movimiento. USD = Dolares',
      'F',
      'USD',
      3,
      ' ',
      23,
      'Activo',
      (
        SELECT ID_CANAL_RECAUDACION
        FROM DB_FINANCIERO.ADMI_CANAL_RECAUDACION
        WHERE DESCRIPCION_CANAL_RECAUDACION = 'TN - BANCO PICHINCHA'
      ),
      'A',
      'D',
      '10',
      174,
      'N',
      SYSDATE,     
      'eholguin',
      '127.0.0.1',
      SYSDATE,     
      'eholguin',
      '127.0.0.1'
  );
  
  INSERT INTO DB_FINANCIERO.ADMI_FORMATO_RECAUDACION
  (
      ID_FORMATO_RECAUDACION,
      DESCRIPCION,
      TIPO_CAMPO,
      CONTENIDO,
      LONGITUD,
      CARACTER_RELLENO,
      POSICION,
      ESTADO,
      CANAL_RECAUDACION_ID,
      TIPO_DATO,
      ORIENTACION_CARACTER_RELLENO,
      EMPRESA_COD,
      LONGITUD_TOTAL,
      ES_CABECERA,
      FE_CREACION,
      USR_CREACION,     
      IP_CREACION,
      FE_ULT_MOD,
      USR_ULT_MOD,     
      IP_ULT_MOD
  )
  VALUES
  (
      DB_FINANCIERO.SEQ_ADMI_FORMATO_RECAUDACION.NEXTVAL,
      'Valor: Valor del Movimiento.',
      'V',
      '11|2',
      13,
      '0',
      26,
      'Activo',
      (
        SELECT ID_CANAL_RECAUDACION
        FROM DB_FINANCIERO.ADMI_CANAL_RECAUDACION
        WHERE DESCRIPCION_CANAL_RECAUDACION = 'TN - BANCO PICHINCHA'
      ),
      'N',
      'I',
      '10',
      124,
      'N',
      SYSDATE,     
      'eholguin',
      '127.0.0.1',
      SYSDATE,     
      'eholguin',
      '127.0.0.1'
  );  
  
  INSERT INTO DB_FINANCIERO.ADMI_FORMATO_RECAUDACION
  (
      ID_FORMATO_RECAUDACION,
      DESCRIPCION,
      TIPO_CAMPO,
      CONTENIDO,
      LONGITUD,
      CARACTER_RELLENO,
      POSICION,
      ESTADO,
      CANAL_RECAUDACION_ID,
      TIPO_DATO,
      ORIENTACION_CARACTER_RELLENO,
      EMPRESA_COD,
      LONGITUD_TOTAL,
      ES_CABECERA,
      FE_CREACION,
      USR_CREACION,     
      IP_CREACION,
      FE_ULT_MOD,
      USR_ULT_MOD,     
      IP_ULT_MOD
  )
  VALUES
  (
      DB_FINANCIERO.SEQ_ADMI_FORMATO_RECAUDACION.NEXTVAL,
      'Forma de Cobro o Pago',
      'F',
      'REC',
      3,
      ' ',
      39,
      'Activo',
      (
        SELECT ID_CANAL_RECAUDACION
        FROM DB_FINANCIERO.ADMI_CANAL_RECAUDACION
        WHERE DESCRIPCION_CANAL_RECAUDACION = 'TN - BANCO PICHINCHA'
      ),
      'A',
      'D',
      '10',
      174,
      'N',
      SYSDATE,     
      'eholguin',
      '127.0.0.1',
      SYSDATE,     
      'eholguin',
      '127.0.0.1'
  );  
  
  INSERT INTO DB_FINANCIERO.ADMI_FORMATO_RECAUDACION
  (
      ID_FORMATO_RECAUDACION,
      DESCRIPCION,
      TIPO_CAMPO,
      CONTENIDO,
      LONGITUD,
      CARACTER_RELLENO,
      POSICION,
      ESTADO,
      CANAL_RECAUDACION_ID,
      TIPO_DATO,
      ORIENTACION_CARACTER_RELLENO,
      EMPRESA_COD,
      LONGITUD_TOTAL,
      ES_CABECERA,
      FE_CREACION,
      USR_CREACION,     
      IP_CREACION,
      FE_ULT_MOD,
      USR_ULT_MOD,     
      IP_ULT_MOD
  )
  VALUES
  (
      DB_FINANCIERO.SEQ_ADMI_FORMATO_RECAUDACION.NEXTVAL,
      'Tipo de cuenta',
      'F',
      '   ',
      3,
      ' ',
      42,
      'Activo',
      (
        SELECT ID_CANAL_RECAUDACION
        FROM DB_FINANCIERO.ADMI_CANAL_RECAUDACION
        WHERE DESCRIPCION_CANAL_RECAUDACION = 'TN - BANCO PICHINCHA'
      ),
      'A',
      'D',
      '10',
      174,
      'N',
      SYSDATE,     
      'eholguin',
      '127.0.0.1',
      SYSDATE,     
      'eholguin',
      '127.0.0.1'
  );  
  
  INSERT INTO DB_FINANCIERO.ADMI_FORMATO_RECAUDACION
  (
      ID_FORMATO_RECAUDACION,
      DESCRIPCION,
      TIPO_CAMPO,
      CONTENIDO,
      LONGITUD,
      CARACTER_RELLENO,
      POSICION,
      ESTADO,
      CANAL_RECAUDACION_ID,
      TIPO_DATO,
      ORIENTACION_CARACTER_RELLENO,
      EMPRESA_COD,
      LONGITUD_TOTAL,
      ES_CABECERA,
      FE_CREACION,
      USR_CREACION,     
      IP_CREACION,
      FE_ULT_MOD,
      USR_ULT_MOD,     
      IP_ULT_MOD
  )
  VALUES
  (
      DB_FINANCIERO.SEQ_ADMI_FORMATO_RECAUDACION.NEXTVAL,
      'Numero de cuenta',
      'F',
      '                    ',
      20,
      '0',
      45,
      'Activo',
      (
        SELECT ID_CANAL_RECAUDACION
        FROM DB_FINANCIERO.ADMI_CANAL_RECAUDACION
        WHERE DESCRIPCION_CANAL_RECAUDACION = 'TN - BANCO PICHINCHA'
      ),
      'N',
      'I',
      '10',
      174,
      'N',
      SYSDATE,     
      'eholguin',
      '127.0.0.1',
      SYSDATE,     
      'eholguin',
      '127.0.0.1'
  );
  
  INSERT INTO DB_FINANCIERO.ADMI_FORMATO_RECAUDACION
  (
      ID_FORMATO_RECAUDACION,
      DESCRIPCION,
      TIPO_CAMPO,
      CONTENIDO,
      LONGITUD,
      CARACTER_RELLENO,
      POSICION,
      ESTADO,
      CANAL_RECAUDACION_ID,
      TIPO_DATO,
      ORIENTACION_CARACTER_RELLENO,
      EMPRESA_COD,
      LONGITUD_TOTAL,
      ES_CABECERA,
      FE_CREACION,
      USR_CREACION,     
      IP_CREACION,
      FE_ULT_MOD,
      USR_ULT_MOD,     
      IP_ULT_MOD
  )
  VALUES
  (
      DB_FINANCIERO.SEQ_ADMI_FORMATO_RECAUDACION.NEXTVAL,
      'Referencia del cobro o pago que se está realizando',
      'V',
      'IDT',
      40,
      ' ',
      65,
      'Activo',
      (
        SELECT ID_CANAL_RECAUDACION
        FROM DB_FINANCIERO.ADMI_CANAL_RECAUDACION
        WHERE DESCRIPCION_CANAL_RECAUDACION = 'TN - BANCO PICHINCHA'
      ),
      'A',
      'D',
      '10',
      174,
      'N',
      SYSDATE,     
      'eholguin',
      '127.0.0.1',
      SYSDATE,     
      'eholguin',
      '127.0.0.1'
  );
  
  
  INSERT INTO DB_FINANCIERO.ADMI_FORMATO_RECAUDACION
  (
      ID_FORMATO_RECAUDACION,
      DESCRIPCION,
      TIPO_CAMPO,
      CONTENIDO,
      LONGITUD,
      CARACTER_RELLENO,
      POSICION,
      ESTADO,
      CANAL_RECAUDACION_ID,
      TIPO_DATO,
      ORIENTACION_CARACTER_RELLENO,
      EMPRESA_COD,
      LONGITUD_TOTAL,
      ES_CABECERA,
      FE_CREACION,
      USR_CREACION,     
      IP_CREACION,
      FE_ULT_MOD,
      USR_ULT_MOD,     
      IP_ULT_MOD
  )
  VALUES
  (
      DB_FINANCIERO.SEQ_ADMI_FORMATO_RECAUDACION.NEXTVAL,
      'Tipo ID Cliente. Tipo de Identificación del beneficiario o deudor',
      'V',
      'TI',
      1,
      ' ',
      105,
      'Activo',
      (
        SELECT ID_CANAL_RECAUDACION
        FROM DB_FINANCIERO.ADMI_CANAL_RECAUDACION
        WHERE DESCRIPCION_CANAL_RECAUDACION = 'TN - BANCO PICHINCHA'
      ),
      'A',
      'D',
      '10',
      174,
      'N',
      SYSDATE,     
      'eholguin',
      '127.0.0.1',
      SYSDATE,     
      'eholguin',
      '127.0.0.1'
  );
  
  INSERT INTO DB_FINANCIERO.ADMI_FORMATO_RECAUDACION
  (
      ID_FORMATO_RECAUDACION,
      DESCRIPCION,
      TIPO_CAMPO,
      CONTENIDO,
      LONGITUD,
      CARACTER_RELLENO,
      POSICION,
      ESTADO,
      CANAL_RECAUDACION_ID,
      TIPO_DATO,
      ORIENTACION_CARACTER_RELLENO,
      EMPRESA_COD,
      LONGITUD_TOTAL,
      ES_CABECERA,
      FE_CREACION,
      USR_CREACION,     
      IP_CREACION,
      FE_ULT_MOD,
      USR_ULT_MOD,     
      IP_ULT_MOD
  )
  VALUES
  (
      DB_FINANCIERO.SEQ_ADMI_FORMATO_RECAUDACION.NEXTVAL,
      'Numero ID Cliente. Numero de Identificación del beneficiario o deudor',
      'V',
      'IDT',
      14,
      ' ',
      106,
      'Activo',
      (
        SELECT ID_CANAL_RECAUDACION
        FROM DB_FINANCIERO.ADMI_CANAL_RECAUDACION
        WHERE DESCRIPCION_CANAL_RECAUDACION = 'TN - BANCO PICHINCHA'
      ),
      'A',
      'D',
      '10',
      174,
      'N',
      SYSDATE,     
      'eholguin',
      '127.0.0.1',
      SYSDATE,     
      'eholguin',
      '127.0.0.1'
  );
  
  INSERT INTO DB_FINANCIERO.ADMI_FORMATO_RECAUDACION
  (
      ID_FORMATO_RECAUDACION,
      DESCRIPCION,
      TIPO_CAMPO,
      CONTENIDO,
      LONGITUD,
      CARACTER_RELLENO,
      POSICION,
      ESTADO,
      CANAL_RECAUDACION_ID,
      TIPO_DATO,
      ORIENTACION_CARACTER_RELLENO,
      EMPRESA_COD,
      LONGITUD_TOTAL,
      ES_CABECERA,
      FE_CREACION,
      USR_CREACION,     
      IP_CREACION,
      FE_ULT_MOD,
      USR_ULT_MOD,     
      IP_ULT_MOD
  )
  VALUES
  (
      DB_FINANCIERO.SEQ_ADMI_FORMATO_RECAUDACION.NEXTVAL,
      'Nombre del Cliente. Nombre del beneficiario o deudor.',
      'V',
      'NOM',
      41,
      ' ',
      120,
      'Activo',
      (
	SELECT ID_CANAL_RECAUDACION
	FROM DB_FINANCIERO.ADMI_CANAL_RECAUDACION
	WHERE DESCRIPCION_CANAL_RECAUDACION = 'TN - BANCO PICHINCHA'
      ),
      'A',
      'D',
      '10',
      174,
      'N',
      SYSDATE,     
      'eholguin',
      '127.0.0.1',
      SYSDATE,     
      'eholguin',
      '127.0.0.1'
  );
  
   COMMIT;
   
  -----------------------------------------------------------------------------------------
  --  ACTUALIZACION DE FORMA DE PAGO DE TRANSFERENCIA A RECAUDACION PARA CLIENTES DE TN----
  -----------------------------------------------------------------------------------------   
  
DECLARE
  CURSOR C_ContratosRec
  IS
	SELECT IPER.ID_PERSONA_ROL, IC.ID_CONTRATO, IC.ESTADO, IC.FORMA_PAGO_ID
	FROM DB_COMERCIAL.INFO_PERSONA                   IPE  
	LEFT JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL  IPER ON IPE.ID_PERSONA              = IPER.PERSONA_ID
	LEFT JOIN DB_COMERCIAL.INFO_OFICINA_GRUPO        IOG  ON IOG.ID_OFICINA              = IPER.OFICINA_ID
	LEFT JOIN DB_COMERCIAL.INFO_CONTRATO             IC   ON IC.PERSONA_EMPRESA_ROL_ID   = IPER.ID_PERSONA_ROL
	LEFT JOIN DB_GENERAL.ADMI_FORMA_PAGO             AFP  ON AFP.ID_FORMA_PAGO           = IC.FORMA_PAGO_ID
	WHERE IOG.EMPRESA_ID           = '10'
	AND AFP.DESCRIPCION_FORMA_PAGO IN ('DEPOSITO',
                                       'DEPOSITO GRUPAL',
                                       'DEPOSITO GRUPAL MESES ANTERIORES',
                                       'TRANSFERENCIA',
                                       'TRANSFERENCIA GRUPAL MESES ANTERIORES',
                                       'TRANSFERENCIA MESES ANTERIORES')
	AND IC.FORMA_PAGO_ID IS NOT NULL;
BEGIN

  FOR Contrato IN C_ContratosRec
  LOOP
    INSERT
    INTO DB_COMERCIAL.INFO_CONTRATO_FORMA_PAGO_HIST
    (
      ID_DATOS_PAGO,
      CONTRATO_ID,
      BANCO_TIPO_CUENTA_ID,
      NUMERO_CTA_TARJETA,
      CODIGO_VERIFICACION,
      TITULAR_CUENTA,
      FE_CREACION,
      FE_ULT_MOD,
      USR_CREACION,
      USR_ULT_MOD,
      ESTADO,
      TIPO_CUENTA_ID,
      IP_CREACION,
      ANIO_VENCIMIENTO,
      MES_VENCIMIENTO,
      CEDULA_TITULAR,
      FORMA_PAGO   
    )
    VALUES
    (
      DB_COMERCIAL.SEQ_INFO_CONTRATO_FORMA_PAGO_H.NEXTVAL,
      Contrato.ID_CONTRATO,
      NULL,
      NULL,
      NULL,
      NULL,
      SYSDATE,
      SYSDATE,
      'telcos',
      'telcos',
      Contrato.ESTADO,
      NULL,
      '127.0.0.1',
      'NULL',
      'NULL',
      'NULL',
      Contrato.FORMA_PAGO_ID
    );
    

    INSERT
    INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_HISTO
    (
      ID_PERSONA_EMPRESA_ROL_HISTO,
      USR_CREACION,
      FE_CREACION,
      IP_CREACION,
      ESTADO,
      PERSONA_EMPRESA_ROL_ID,
      OBSERVACION,
      MOTIVO_ID,
      EMPRESA_ROL_ID,
      OFICINA_ID,
      DEPARTAMENTO_ID,
      CUADRILLA_ID,
      REPORTA_PERSONA_EMPRESA_ROL_ID,
      ES_PREPAGO   
    )
    VALUES
    (
      DB_COMERCIAL.SEQ_INFO_PERSONA_EMPRESA_ROL_H.NEXTVAL,
      'telcos',
      SYSDATE,
      '127.0.0.1',
      'Activo',
       Contrato.ID_PERSONA_ROL,
      'Se procede a actualizar forma de pago. Valor anterior: Transferencia. Valor nuevo: Recaudacion.',
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL
    );

  END LOOP;
  COMMIT;
END;
/  
  UPDATE DB_COMERCIAL.INFO_CONTRATO IC 
  SET   IC.FORMA_PAGO_ID = (SELECT AFP.ID_FORMA_PAGO FROM DB_GENERAL.ADMI_FORMA_PAGO AFP WHERE AFP.CODIGO_FORMA_PAGO = 'REC')
  WHERE IC.ID_CONTRATO IN  (SELECT IC.ID_CONTRATO
			    FROM DB_COMERCIAL.INFO_PERSONA                   IPE  
			    LEFT JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL  IPER ON IPE.ID_PERSONA              = IPER.PERSONA_ID
			    LEFT JOIN DB_COMERCIAL.INFO_OFICINA_GRUPO        IOG  ON IOG.ID_OFICINA              = IPER.OFICINA_ID
			    LEFT JOIN DB_COMERCIAL.INFO_CONTRATO             IC   ON IC.PERSONA_EMPRESA_ROL_ID   = IPER.ID_PERSONA_ROL
			    LEFT JOIN DB_GENERAL.ADMI_FORMA_PAGO             AFP  ON AFP.ID_FORMA_PAGO           = IC.FORMA_PAGO_ID
			    WHERE IOG.EMPRESA_ID           = '10'
			    AND AFP.DESCRIPCION_FORMA_PAGO IN ( 'DEPOSITO',
                                                    'DEPOSITO GRUPAL',
                                                    'DEPOSITO GRUPAL MESES ANTERIORES',
                                                    'TRANSFERENCIA',
                                                    'TRANSFERENCIA GRUPAL MESES ANTERIORES',
                                                    'TRANSFERENCIA MESES ANTERIORES')
			    AND IC.FORMA_PAGO_ID IS NOT NULL);
			    
  COMMIT;