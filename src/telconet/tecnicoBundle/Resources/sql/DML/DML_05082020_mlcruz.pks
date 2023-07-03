--Creación de parámetros para bancos no permitidos en la consulta de corte masivo
DECLARE
  Ln_IdParamsServiciosMd    NUMBER;
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
    'Valor2: Nombre de dir en la BD, Valor3: Ruta del dir en la BD, Valor4: Ruta del dir en Telcos',
    'DIRECTORIO_ARCHIVOS_PROCESOS_MASIVOS',
    'DIR_PROCESOS_MASIVOS',
    '/app/telcos/reportes/MD/tecnico/procesos_masivos/',
    'telcos/web/public/uploads/MD/tecnico/procesos_masivos/',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente el DIRECTORIO_ARCHIVOS_PROCESOS_MASIVOS');

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
    'Listado de bancos no permitidos en la consulta de corte masivo',
    'CORTE_MASIVO',
    'BANCOS_NO_PERMITIDOS',
    'CUENTA',
    'BANCO BANISI PANAMÁ',
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
    'Listado de bancos no permitidos en la consulta de corte masivo',
    'CORTE_MASIVO',
    'BANCOS_NO_PERMITIDOS',
    'CUENTA',
    'COOP. AHORRO SAN MIGUEL',
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
    'Listado de bancos no permitidos en la consulta de corte masivo',
    'CORTE_MASIVO',
    'BANCOS_NO_PERMITIDOS',
    'CUENTA',
    'BOLIVARIANO PANAMA',
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
    'Listado de bancos no permitidos en la consulta de corte masivo',
    'CORTE_MASIVO',
    'BANCOS_NO_PERMITIDOS',
    'CUENTA',
    'BANCO MERRY LINCH',
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
    'Listado de bancos no permitidos en la consulta de corte masivo',
    'CORTE_MASIVO',
    'BANCOS_NO_PERMITIDOS',
    'CUENTA',
    'BANISI S.A',
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
    'Listado de bancos no permitidos en la consulta de corte masivo',
    'CORTE_MASIVO',
    'BANCOS_NO_PERMITIDOS',
    'CUENTA',
    'BANCO MI VECINO',
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
    'Listado de bancos no permitidos en la consulta de corte masivo',
    'CORTE_MASIVO',
    'BANCOS_NO_PERMITIDOS',
    'CUENTA',
    'COOP. SANTA ROSA',
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
    'Listado de bancos no permitidos en la consulta de corte masivo',
    'CORTE_MASIVO',
    'BANCOS_NO_PERMITIDOS',
    'CUENTA',
    'CHEQUES',
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
    'Listado de bancos no permitidos en la consulta de corte masivo',
    'CORTE_MASIVO',
    'BANCOS_NO_PERMITIDOS',
    'CUENTA',
    'BANCO DEL BARRIO',
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
    'Listado de bancos no permitidos en la consulta de corte masivo',
    'CORTE_MASIVO',
    'BANCOS_NO_PERMITIDOS',
    'TARJETA',
    'BANCO MI VECINO',
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
    'Listado de bancos no permitidos en la consulta de corte masivo',
    'CORTE_MASIVO',
    'BANCOS_NO_PERMITIDOS',
    'TARJETA',
    'BANCO DEL BARRIO',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente los BANCOS_NO_PERMITIDOS en la opción de corte masivo');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
/