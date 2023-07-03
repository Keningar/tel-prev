--Bloque anónimo para crear un nuevos parametros para corte masivo para la empresa Ecuanet
SET SERVEROUTPUT ON
DECLARE
  Ln_IdParamsServiciosEn    NUMBER;
BEGIN
  SELECT ID_PARAMETRO
  INTO Ln_IdParamsServiciosEn
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='PARAMETROS_ASOCIADOS_A_SERVICIOS_EN';
  
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
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosEn,
    'Listado de tipos de documentos permitidos en la consulta de corte masivo',
    'CORTE_MASIVO',
    'TIPOS_DE_DOCUMENTOS',
    'TODAS',
    'FAC,FACP,NDI',
    NULL,
    'Activo',
    'jpiloso',
    sysdate,
    '127.0.0.1',
    '33'
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
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosEn,
    'Listado de tipos de documentos permitidos en la consulta de corte masivo',
    'CORTE_MASIVO',
    'TIPOS_DE_DOCUMENTOS',
    'Factura Recurrente',
    'FAC',
    'PERMITIDO_RESUMEN_PREVIO',
    'Clientes FC Recurrente',
    '1',
    'Activo',
    'jpiloso',
    sysdate,
    '127.0.0.1',
    '33'
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
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosEn,
    'Listado de tipos de documentos permitidos en la consulta de corte masivo',
    'CORTE_MASIVO',
    'TIPOS_DE_DOCUMENTOS',
    'Factura No Recurrente',
    'FACP',
    'PERMITIDO_RESUMEN_PREVIO',
    'Clientes FC No Recurrente',
    '2',
    'Activo',
    'jpiloso',
    sysdate,
    '127.0.0.1',
    '33'
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
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosEn,
    'Listado de tipos de documentos permitidos en la consulta de corte masivo',
    'CORTE_MASIVO',
    'TIPOS_DE_DOCUMENTOS',
    'NDI',
    'NDI',
    'PERMITIDO_RESUMEN_PREVIO',
    'NDI',
    '3',
    'Activo',
    'jpiloso',
    sysdate,
    '127.0.0.1',
    '33'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente los TIPOS_DE_DOCUMENTOS en la opción de corte masivo');
  
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
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosEn,
    'Número de logines permitidos para cortar por lotes',
    'CORTE_MASIVO',
    'NUM_LOGINES_POR_LOTE',
    '2000',
    'INTERNET',
    NULL,
    'Activo',
    'jpiloso',
    sysdate,
    '127.0.0.1',
    '33'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se ha ingresado correctamente el parámetro NUM_LOGINES_POR_LOTE para la opción de corte masivo');
  
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
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosEn,
    'Listado de ids de formas de pago que muestran cuenta/tarjeta, tipo de cuenta/tarjeta y bancos',
    'CORTE_MASIVO',
    'IDS_FORMAS_PAGO_CUENTA_TARJETA_BANCOS',
    '3',
    NULL,
    NULL,
    'Activo',
    'jpiloso',
    sysdate,
    '127.0.0.1',
    '33'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se ha ingresado correctamente el parámetro IDS_FORMAS_PAGO_CUENTA_TARJETA_BANCOS para la opción de corte masivo');
  
  
 --Insert BANCOS_NO_PERMITIDOS
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
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosEn,
    'Listado de bancos no permitidos en la consulta de corte masivo',
    'CORTE_MASIVO',
    'BANCOS_NO_PERMITIDOS',
    'CUENTA',
    'BANCO BANISI PANAMÁ',
    NULL,
    'Activo',
    'jpiloso',
    sysdate,
    '127.0.0.1',
    '33'
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
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosEn,
    'Listado de bancos no permitidos en la consulta de corte masivo',
    'CORTE_MASIVO',
    'BANCOS_NO_PERMITIDOS',
    'CUENTA',
    'COOP. AHORRO SAN MIGUEL',
    NULL,
    'Activo',
    'jpiloso',
    sysdate,
    '127.0.0.1',
    '33'
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
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosEn,
    'Listado de bancos no permitidos en la consulta de corte masivo',
    'CORTE_MASIVO',
    'BANCOS_NO_PERMITIDOS',
    'CUENTA',
    'BOLIVARIANO PANAMA',
    NULL,
    'Activo',
    'jpiloso',
    sysdate,
    '127.0.0.1',
    '33'
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
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosEn,
    'Listado de bancos no permitidos en la consulta de corte masivo',
    'CORTE_MASIVO',
    'BANCOS_NO_PERMITIDOS',
    'CUENTA',
    'BANCO MERRY LINCH',
    NULL,
    'Activo',
    'jpiloso',
    sysdate,
    '127.0.0.1',
    '33'
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
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosEn,
    'Listado de bancos no permitidos en la consulta de corte masivo',
    'CORTE_MASIVO',
    'BANCOS_NO_PERMITIDOS',
    'CUENTA',
    'BANISI S.A',
    NULL,
    'Activo',
    'jpiloso',
    sysdate,
    '127.0.0.1',
    '33'
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
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosEn,
    'Listado de bancos no permitidos en la consulta de corte masivo',
    'CORTE_MASIVO',
    'BANCOS_NO_PERMITIDOS',
    'CUENTA',
    'BANCO MI VECINO',
    NULL,
    'Activo',
    'jpiloso',
    sysdate,
    '127.0.0.1',
    '33'
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
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosEn,
    'Listado de bancos no permitidos en la consulta de corte masivo',
    'CORTE_MASIVO',
    'BANCOS_NO_PERMITIDOS',
    'CUENTA',
    'COOP. SANTA ROSA',
    NULL,
    'Activo',
    'jpiloso',
    sysdate,
    '127.0.0.1',
    '33'
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
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosEn,
    'Listado de bancos no permitidos en la consulta de corte masivo',
    'CORTE_MASIVO',
    'BANCOS_NO_PERMITIDOS',
    'CUENTA',
    'CHEQUES',
    NULL,
    'Activo',
    'jpiloso',
    sysdate,
    '127.0.0.1',
    '33'
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
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosEn,
    'Listado de bancos no permitidos en la consulta de corte masivo',
    'CORTE_MASIVO',
    'BANCOS_NO_PERMITIDOS',
    'CUENTA',
    'BANCO DEL BARRIO',
    NULL,
    'Activo',
    'jpiloso',
    sysdate,
    '127.0.0.1',
    '33'
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
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosEn,
    'Listado de bancos no permitidos en la consulta de corte masivo',
    'CORTE_MASIVO',
    'BANCOS_NO_PERMITIDOS',
    'TARJETA',
    'BANCO MI VECINO',
    NULL,
    'Activo',
    'jpiloso',
    sysdate,
    '127.0.0.1',
    '33'
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
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosEn,
    'Listado de bancos no permitidos en la consulta de corte masivo',
    'CORTE_MASIVO',
    'BANCOS_NO_PERMITIDOS',
    'TARJETA',
    'BANCO DEL BARRIO',
    NULL,
    'Activo',
    'jpiloso',
    sysdate,
    '127.0.0.1',
    '33'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente los BANCOS_NO_PERMITIDOS en la opción de corte masivo');
  
  Insert into DB_GENERAL.ADMI_PARAMETRO_DET 
	(ID_PARAMETRO_DET,
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
	VALOR9) 
  values 
	(DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
	(select id_parametro from DB_GENERAL.ADMI_PARAMETRO_CAB PARAM_CAB
	WHERE PARAM_CAB.NOMBRE_PARAMETRO = 'EMPRESAS_CORTE_REAC_MASIVO'),
	'Empresas que ejecutarán los procesos masivos de corte',
	'CortarCliente',
	'CortarCliente',
	'EN',
	null,
	'Activo',
	'jpiloso',
	sysdate,
	'127.0.0.1',
	null,
	null,
	null,
	null,
	'33',
	null,
	null,
	null,
	null,
	null);
  
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente EN EMPRESAS_CORTE_REAC_MASIVO en la opción de corte masivo');

INSERT
INTO DB_GENERAL.ADMI_GESTION_DIRECTORIOS
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
    DB_GENERAL.SEQ_ADMI_GESTION_DIRECTORIOS.NEXTVAL,
    4,
    (SELECT MAX(CODIGO_PATH) +1 FROM DB_GENERAL.ADMI_GESTION_DIRECTORIOS WHERE CODIGO_APP=4 AND APLICACION='TelcosWeb'),
    'TelcosWeb',
    '593',
    'EN',
    'Tecnico',
    'CorteMasivo',
    'Activo',
    SYSDATE,
    'jpiloso'
  );
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
/
