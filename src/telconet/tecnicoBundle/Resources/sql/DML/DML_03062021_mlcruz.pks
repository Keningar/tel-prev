SET SERVEROUTPUT ON
--Creación de parámetros para Ws que obtiene la información de un cliente MD
DECLARE
  Ln_IdParamsServiciosMd NUMBER;
  Ln_IdParamsServiciosTn NUMBER;
BEGIN
  SELECT ID_PARAMETRO
  INTO Ln_IdParamsServiciosMd
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='PARAMETROS_ASOCIADOS_A_SERVICIOS_MD';

  SELECT ID_PARAMETRO
  INTO Ln_IdParamsServiciosTn
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='PARAMETROS_ASOCIADOS_A_SERVICIOS_TN';

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
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Parámetros usados en los web services para MD',
    'PARAMETROS_WEB_SERVICES',
    'INFORMACION_CLIENTE',
    'APLICATIVOS_WS_TECNICO_PERMITIDOS',
    'ACS',
    'MD',
    '1',
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
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
    Ln_IdParamsServiciosMd,
    'Parámetros usados en los web services para MD',
    'PARAMETROS_WEB_SERVICES',
    'INFORMACION_CLIENTE',
    'TIPOS_ROL_PERMITIDOS',
    'Cliente',
    'Activo',
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
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
    Ln_IdParamsServiciosMd,
    'Parámetros usados en los web services para MD',
    'PARAMETROS_WEB_SERVICES',
    'INFORMACION_CLIENTE',
    'ESTADOS_PERSONA_EMPRESA_ROL_PERMITIDOS',
    'Activo',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
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
    Ln_IdParamsServiciosMd,
    'Parámetros usados en los web services para MD',
    'PARAMETROS_WEB_SERVICES',
    'INFORMACION_CLIENTE',
    'ESTADOS_SERVICIO_PERMITIDOS',
    'Activo',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
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
    Ln_IdParamsServiciosMd,
    'Parámetros usados en los web services para MD',
    'PARAMETROS_WEB_SERVICES',
    'INFORMACION_CLIENTE',
    'ESTADOS_SERVICIO_PERMITIDOS',
    'In-Corte',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
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
    Ln_IdParamsServiciosMd,
    'Parámetros usados en los web services para MD',
    'PARAMETROS_WEB_SERVICES',
    'INFORMACION_CLIENTE',
    'ESTADOS_SERVICIO_PERMITIDOS',
    'EnPruebas',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
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
    Ln_IdParamsServiciosMd,
    'Parámetros usados en los web services para MD',
    'PARAMETROS_WEB_SERVICES',
    'INFORMACION_CLIENTE',
    'ESTADOS_SERVICIO_PERMITIDOS',
    'EnVerificacion',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
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
    Ln_IdParamsServiciosMd,
    'Parámetros usados en los web services para MD',
    'PARAMETROS_WEB_SERVICES',
    'INFORMACION_CLIENTE',
    'ESTADOS_SERVICIO_INTERNET_PERMITIDOS',
    'Activo',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
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
    Ln_IdParamsServiciosMd,
    'Parámetros usados en los web services para MD',
    'PARAMETROS_WEB_SERVICES',
    'INFORMACION_CLIENTE',
    'ESTADOS_SERVICIO_INTERNET_PERMITIDOS',
    'In-Corte',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
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
    Ln_IdParamsServiciosMd,
    'Parámetros usados en los web services para MD',
    'PARAMETROS_WEB_SERVICES',
    'INFORMACION_CLIENTE',
    'ESTADOS_SERVICIO_INTERNET_PERMITIDOS',
    'EnPruebas',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
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
    Ln_IdParamsServiciosMd,
    'Parámetros usados en los web services para MD',
    'PARAMETROS_WEB_SERVICES',
    'INFORMACION_CLIENTE',
    'ESTADOS_SERVICIO_INTERNET_PERMITIDOS',
    'EnVerificacion',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
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
    Ln_IdParamsServiciosMd,
    'Parámetros usados en los web services para MD',
    'PARAMETROS_WEB_SERVICES',
    'INFORMACION_CLIENTE',
    'ESTADOS_ONT_INTERNET_PERMITIDOS',
    'Activo',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
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
    Ln_IdParamsServiciosMd,
    'Parámetros usados en los web services para MD',
    'PARAMETROS_WEB_SERVICES',
    'INFORMACION_CLIENTE',
    'NOMBRES_TECNICOS_INTERNET_PERMITIDOS',
    'INTERNET',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
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
    Ln_IdParamsServiciosMd,
    'Parámetros usados en los web services para MD',
    'PARAMETROS_WEB_SERVICES',
    'INFORMACION_CLIENTE',
    'DESCRIPCIONES_FORMA_CONTACTO_TELEFONO_PERMITIDOS',
    'Telefono Movil',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
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
    Ln_IdParamsServiciosMd,
    'Parámetros usados en los web services para MD',
    'PARAMETROS_WEB_SERVICES',
    'INFORMACION_CLIENTE',
    'DESCRIPCIONES_FORMA_CONTACTO_TELEFONO_PERMITIDOS',
    'Telefono Fijo',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
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
    Ln_IdParamsServiciosMd,
    'Parámetros usados en los web services para MD',
    'PARAMETROS_WEB_SERVICES',
    'INFORMACION_CLIENTE',
    'DESCRIPCIONES_FORMA_CONTACTO_TELEFONO_PERMITIDOS',
    'Telefono Movil Claro',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
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
    Ln_IdParamsServiciosMd,
    'Parámetros usados en los web services para MD',
    'PARAMETROS_WEB_SERVICES',
    'INFORMACION_CLIENTE',
    'DESCRIPCIONES_FORMA_CONTACTO_TELEFONO_PERMITIDOS',
    'Telefono Movil Movistar',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
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
    Ln_IdParamsServiciosMd,
    'Parámetros usados en los web services para MD',
    'PARAMETROS_WEB_SERVICES',
    'INFORMACION_CLIENTE',
    'DESCRIPCIONES_FORMA_CONTACTO_TELEFONO_PERMITIDOS',
    'Telefono Movil CNT',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
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
    Ln_IdParamsServiciosMd,
    'Parámetros usados en los web services para MD',
    'PARAMETROS_WEB_SERVICES',
    'INFORMACION_CLIENTE',
    'DESCRIPCIONES_FORMA_CONTACTO_TELEFONO_PERMITIDOS',
    'Telefono Traslado',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
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
    Ln_IdParamsServiciosMd,
    'Parámetros usados en los web services para MD',
    'PARAMETROS_WEB_SERVICES',
    'INFORMACION_CLIENTE',
    'NUM_MAXIMO_FACTURAS_CONSULTADAS',
    '6',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
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
    Ln_IdParamsServiciosMd,
    'Parámetros usados en los web services para MD',
    'PARAMETROS_WEB_SERVICES',
    'INFORMACION_CLIENTE',
    'NUM_MAXIMO_PAGOS_CONSULTADOS',
    '5',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
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
    VALOR5,
    VALOR6,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosTn,
    'Parámetros usados en los web services para TN',
    'PARAMETROS_WEB_SERVICES',
    'INFORMACION_CLIENTE',
    'APLICATIVOS_WS_TECNICO_PERMITIDOS',
    'ACS',
    'TN',
    '1',
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '10'
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
    Ln_IdParamsServiciosTn,
    'Parámetros usados en los web services para TN',
    'PARAMETROS_WEB_SERVICES',
    'INFORMACION_CLIENTE',
    'TIPOS_ROL_PERMITIDOS',
    'Cliente',
    'Activo',
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '10'
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
    Ln_IdParamsServiciosTn,
    'Parámetros usados en los web services para TN',
    'PARAMETROS_WEB_SERVICES',
    'INFORMACION_CLIENTE',
    'ESTADOS_PERSONA_EMPRESA_ROL_PERMITIDOS',
    'Activo',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '10'
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
    Ln_IdParamsServiciosTn,
    'Parámetros usados en los web services para TN',
    'PARAMETROS_WEB_SERVICES',
    'INFORMACION_CLIENTE',
    'ESTADOS_SERVICIO_PERMITIDOS',
    'Activo',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '10'
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
    Ln_IdParamsServiciosTn,
    'Parámetros usados en los web services para TN',
    'PARAMETROS_WEB_SERVICES',
    'INFORMACION_CLIENTE',
    'ESTADOS_SERVICIO_PERMITIDOS',
    'In-Corte',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '10'
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
    Ln_IdParamsServiciosTn,
    'Parámetros usados en los web services para TN',
    'PARAMETROS_WEB_SERVICES',
    'INFORMACION_CLIENTE',
    'ESTADOS_SERVICIO_PERMITIDOS',
    'EnPruebas',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '10'
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
    Ln_IdParamsServiciosTn,
    'Parámetros usados en los web services para TN',
    'PARAMETROS_WEB_SERVICES',
    'INFORMACION_CLIENTE',
    'ESTADOS_SERVICIO_PERMITIDOS',
    'EnVerificacion',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '10'
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
    Ln_IdParamsServiciosTn,
    'Parámetros usados en los web services para TN',
    'PARAMETROS_WEB_SERVICES',
    'INFORMACION_CLIENTE',
    'ESTADOS_SERVICIO_INTERNET_PERMITIDOS',
    'Activo',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '10'
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
    Ln_IdParamsServiciosTn,
    'Parámetros usados en los web services para TN',
    'PARAMETROS_WEB_SERVICES',
    'INFORMACION_CLIENTE',
    'ESTADOS_SERVICIO_INTERNET_PERMITIDOS',
    'In-Corte',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '10'
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
    Ln_IdParamsServiciosTn,
    'Parámetros usados en los web services para TN',
    'PARAMETROS_WEB_SERVICES',
    'INFORMACION_CLIENTE',
    'ESTADOS_SERVICIO_INTERNET_PERMITIDOS',
    'EnPruebas',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '10'
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
    Ln_IdParamsServiciosTn,
    'Parámetros usados en los web services para TN',
    'PARAMETROS_WEB_SERVICES',
    'INFORMACION_CLIENTE',
    'ESTADOS_SERVICIO_INTERNET_PERMITIDOS',
    'EnVerificacion',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '10'
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
    Ln_IdParamsServiciosTn,
    'Parámetros usados en los web services para TN',
    'PARAMETROS_WEB_SERVICES',
    'INFORMACION_CLIENTE',
    'ESTADOS_ONT_INTERNET_PERMITIDOS',
    'Activo',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '10'
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
    Ln_IdParamsServiciosTn,
    'Parámetros usados en los web services para TN',
    'PARAMETROS_WEB_SERVICES',
    'INFORMACION_CLIENTE',
    'NOMBRES_TECNICOS_INTERNET_PERMITIDOS',
    'INTERNET SMALL BUSINESS',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '10'
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
    Ln_IdParamsServiciosTn,
    'Parámetros usados en los web services para TN',
    'PARAMETROS_WEB_SERVICES',
    'INFORMACION_CLIENTE',
    'NOMBRES_TECNICOS_INTERNET_PERMITIDOS',
    'TELCOHOME',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '10'
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
    Ln_IdParamsServiciosTn,
    'Parámetros usados en los web services para TN',
    'PARAMETROS_WEB_SERVICES',
    'INFORMACION_CLIENTE',
    'DESCRIPCIONES_FORMA_CONTACTO_TELEFONO_PERMITIDOS',
    'Telefono Movil',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '10'
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
    Ln_IdParamsServiciosTn,
    'Parámetros usados en los web services para TN',
    'PARAMETROS_WEB_SERVICES',
    'INFORMACION_CLIENTE',
    'DESCRIPCIONES_FORMA_CONTACTO_TELEFONO_PERMITIDOS',
    'Telefono Fijo',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '10'
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
    Ln_IdParamsServiciosTn,
    'Parámetros usados en los web services para TN',
    'PARAMETROS_WEB_SERVICES',
    'INFORMACION_CLIENTE',
    'DESCRIPCIONES_FORMA_CONTACTO_TELEFONO_PERMITIDOS',
    'Telefono Movil Claro',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '10'
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
    Ln_IdParamsServiciosTn,
    'Parámetros usados en los web services para TN',
    'PARAMETROS_WEB_SERVICES',
    'INFORMACION_CLIENTE',
    'DESCRIPCIONES_FORMA_CONTACTO_TELEFONO_PERMITIDOS',
    'Telefono Movil Movistar',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '10'
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
    Ln_IdParamsServiciosTn,
    'Parámetros usados en los web services para TN',
    'PARAMETROS_WEB_SERVICES',
    'INFORMACION_CLIENTE',
    'DESCRIPCIONES_FORMA_CONTACTO_TELEFONO_PERMITIDOS',
    'Telefono Movil CNT',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '10'
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
    Ln_IdParamsServiciosTn,
    'Parámetros usados en los web services para TN',
    'PARAMETROS_WEB_SERVICES',
    'INFORMACION_CLIENTE',
    'DESCRIPCIONES_FORMA_CONTACTO_TELEFONO_PERMITIDOS',
    'Telefono Traslado',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '10'
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
    Ln_IdParamsServiciosTn,
    'Parámetros usados en los web services para TN',
    'PARAMETROS_WEB_SERVICES',
    'INFORMACION_CLIENTE',
    'NUM_MAXIMO_FACTURAS_CONSULTADAS',
    '6',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '10'
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
    Ln_IdParamsServiciosTn,
    'Parámetros usados en los web services para TN',
    'PARAMETROS_WEB_SERVICES',
    'INFORMACION_CLIENTE',
    'NUM_MAXIMO_PAGOS_CONSULTADOS',
    '5',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '10'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Parámetros para el consumo del Ws de información del cliente ingresados correctamente');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
/