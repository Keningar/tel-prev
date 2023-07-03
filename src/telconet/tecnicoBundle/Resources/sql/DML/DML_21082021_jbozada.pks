SET SERVEROUTPUT ON
--Creación de parámetros para Ws Acs que obtiene la información de un cliente MD
DECLARE
  Ln_IdParamsServiciosMd NUMBER;
BEGIN
  SELECT ID_PARAMETRO
  INTO Ln_IdParamsServiciosMd
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='PARAMETROS_ASOCIADOS_A_SERVICIOS_MD';

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
    'Parámetros usados en los web services acs para MD',
    'PARAMETROS_WEB_SERVICES_ACS',
    'INFORMACION_CLIENTE',
    'ESTADOS_PERSONA_EMPRESA_ROL_PERMITIDOS',
    'Activo',
    NULL,
    'Activo',
    'jbozada',
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
    'PARAMETROS_WEB_SERVICES_ACS',
    'INFORMACION_CLIENTE',
    'ESTADOS_SERVICIO_INTERNET_PERMITIDOS',
    'Factible',
    NULL,
    'Activo',
    'jbozada',
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
    'PARAMETROS_WEB_SERVICES_ACS',
    'INFORMACION_CLIENTE',
    'ESTADOS_SERVICIO_INTERNET_PERMITIDOS',
    'PrePlanificada',
    NULL,
    'Activo',
    'jbozada',
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
    'PARAMETROS_WEB_SERVICES_ACS',
    'INFORMACION_CLIENTE',
    'ESTADOS_SERVICIO_INTERNET_PERMITIDOS',
    'EnVerificacion',
    NULL,
    'Activo',
    'jbozada',
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
    'PARAMETROS_WEB_SERVICES_ACS',
    'INFORMACION_CLIENTE',
    'ESTADOS_SERVICIO_INTERNET_PERMITIDOS',
    'EnPruebas',
    NULL,
    'Activo',
    'jbozada',
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
    'PARAMETROS_WEB_SERVICES_ACS',
    'INFORMACION_CLIENTE',
    'ESTADOS_SERVICIO_INTERNET_PERMITIDOS',
    'Cancelado',
    NULL,
    'Activo',
    'jbozada',
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
    'PARAMETROS_WEB_SERVICES_ACS',
    'INFORMACION_CLIENTE',
    'ESTADOS_SERVICIO_INTERNET_PERMITIDOS',
    'Anulado',
    NULL,
    'Activo',
    'jbozada',
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
    'PARAMETROS_WEB_SERVICES_ACS',
    'INFORMACION_CLIENTE',
    'ESTADOS_SERVICIO_INTERNET_PERMITIDOS',
    'Rechazada',
    NULL,
    'Activo',
    'jbozada',
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
    'PARAMETROS_WEB_SERVICES_ACS',
    'INFORMACION_CLIENTE',
    'ESTADOS_SERVICIO_INTERNET_PERMITIDOS',
    'Asignada',
    NULL,
    'Activo',
    'jbozada',
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
    'PARAMETROS_WEB_SERVICES_ACS',
    'INFORMACION_CLIENTE',
    'ESTADOS_SERVICIO_INTERNET_PERMITIDOS',
    'AsignadoTarea',
    NULL,
    'Activo',
    'jbozada',
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
    'PARAMETROS_WEB_SERVICES_ACS',
    'INFORMACION_CLIENTE',
    'ESTADOS_SERVICIO_INTERNET_PERMITIDOS',
    'Eliminado',
    NULL,
    'Activo',
    'jbozada',
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
    'PARAMETROS_WEB_SERVICES_ACS',
    'INFORMACION_CLIENTE',
    'ESTADOS_SERVICIO_INTERNET_PERMITIDOS',
    'Trasladado',
    NULL,
    'Activo',
    'jbozada',
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
    'PARAMETROS_WEB_SERVICES_ACS',
    'INFORMACION_CLIENTE',
    'ESTADOS_SERVICIO_INTERNET_PERMITIDOS',
    'Pendiente',
    NULL,
    'Activo',
    'jbozada',
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
    'PARAMETROS_WEB_SERVICES_ACS',
    'INFORMACION_CLIENTE',
    'ESTADOS_SERVICIO_INTERNET_PERMITIDOS',
    'Cancel',
    NULL,
    'Activo',
    'jbozada',
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
    'PARAMETROS_WEB_SERVICES_ACS',
    'INFORMACION_CLIENTE',
    'ESTADOS_SERVICIO_INTERNET_PERMITIDOS',
    'Inactivo',
    NULL,
    'Activo',
    'jbozada',
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
    'PARAMETROS_WEB_SERVICES_ACS',
    'INFORMACION_CLIENTE',
    'ESTADOS_SERVICIO_INTERNET_PERMITIDOS',
    'Activo',
    NULL,
    'Activo',
    'jbozada',
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
    'PARAMETROS_WEB_SERVICES_ACS',
    'INFORMACION_CLIENTE',
    'ESTADOS_SERVICIO_INTERNET_PERMITIDOS',
    'In-Corte',
    NULL,
    'Activo',
    'jbozada',
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
    'PARAMETROS_WEB_SERVICES_ACS',
    'INFORMACION_CLIENTE',
    'NOMBRES_TECNICOS_INTERNET_PERMITIDOS',
    'INTERNET',
    NULL,
    'Activo',
    'jbozada',
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
    'PARAMETROS_WEB_SERVICES_ACS',
    'INFORMACION_CLIENTE',
    'ESTADOS_ONT_INTERNET_PERMITIDOS',
    'Activo',
    NULL,
    'Activo',
    'jbozada',
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
    'PARAMETROS_WEB_SERVICES_ACS',
    'INFORMACION_CLIENTE',
    'DESCRIPCIONES_FORMA_CONTACTO_TELEFONO_PERMITIDOS',
    'Telefono Movil',
    NULL,
    'Activo',
    'jbozada',
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
    'PARAMETROS_WEB_SERVICES_ACS',
    'INFORMACION_CLIENTE',
    'DESCRIPCIONES_FORMA_CONTACTO_TELEFONO_PERMITIDOS',
    'Telefono Fijo',
    NULL,
    'Activo',
    'jbozada',
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
    'PARAMETROS_WEB_SERVICES_ACS',
    'INFORMACION_CLIENTE',
    'DESCRIPCIONES_FORMA_CONTACTO_TELEFONO_PERMITIDOS',
    'Telefono Movil Claro',
    NULL,
    'Activo',
    'jbozada',
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
    'PARAMETROS_WEB_SERVICES_ACS',
    'INFORMACION_CLIENTE',
    'DESCRIPCIONES_FORMA_CONTACTO_TELEFONO_PERMITIDOS',
    'Telefono Movil Movistar',
    NULL,
    'Activo',
    'jbozada',
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
    'PARAMETROS_WEB_SERVICES_ACS',
    'INFORMACION_CLIENTE',
    'DESCRIPCIONES_FORMA_CONTACTO_TELEFONO_PERMITIDOS',
    'Telefono Movil CNT',
    NULL,
    'Activo',
    'jbozada',
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
    'PARAMETROS_WEB_SERVICES_ACS',
    'INFORMACION_CLIENTE',
    'DESCRIPCIONES_FORMA_CONTACTO_TELEFONO_PERMITIDOS',
    'Telefono Traslado',
    NULL,
    'Activo',
    'jbozada',
    sysdate,
    '127.0.0.1',
    '18'
  );

  SYS.DBMS_OUTPUT.PUT_LINE('Parámetros para el consumo del Ws Acs de información del cliente ingresados correctamente');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/