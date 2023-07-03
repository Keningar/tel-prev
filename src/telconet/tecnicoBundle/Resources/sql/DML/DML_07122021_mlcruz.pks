SET SERVEROUTPUT ON
--Creación de parámetros para la consulta de corte masivo
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
    VALOR6,
    VALOR7,
    OBSERVACION,
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
    'Valor4:Estado pm cab,Valor5:Nuevo estado pm cab,Valor6:Estado pm det,Valo7:Nuevo estado pm det',
    'FINALIZA_PROCESOS_MASIVOS_POR_OPCION',
    'REACTIVACION_INDIVIDUAL_INTERNET',
    'CortarCliente',
    'PorEjecutar',
    'Finalizada',
    'PorEjecutar',
    'In-Corte',
    'Registro actualizado automáticamente por ejecución de la opción Reconectar Cliente desde el grid técnico',
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
    VALOR7,
    OBSERVACION,
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
    'Valor4:Estado pm cab,Valor5:Nuevo estado pm cab,Valor6:Estado pm det,Valo7:Nuevo estado pm det',
    'FINALIZA_PROCESOS_MASIVOS_POR_OPCION',
    'REACTIVACION_INDIVIDUAL_INTERNET',
    'CortarCliente',
    'Pendiente',
    'Finalizada',
    'Pendiente',
    'In-Corte',
    'Registro actualizado automáticamente por ejecución de la opción Reconectar Cliente desde el grid técnico',
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
    VALOR7,
    OBSERVACION,
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
    'Valor4:Estado pm cab,Valor5:Nuevo estado pm cab,Valor6:Estado pm det,Valo7:Nuevo estado pm det',
    'FINALIZA_PROCESOS_MASIVOS_POR_OPCION',
    'REACTIVACION_INDIVIDUAL_INTERNET',
    'CortarCliente',
    'Pendiente',
    'Finalizada',
    'Fallo',
    'In-Corte',
    'Registro actualizado automáticamente por ejecución de la opción Reconectar Cliente desde el grid técnico',
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
    VALOR7,
    OBSERVACION,
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
    'Valor4:Estado pm cab,Valor5:Nuevo estado pm cab,Valor6:Estado pm det,Valo7:Nuevo estado pm det',
    'FINALIZA_PROCESOS_MASIVOS_POR_OPCION',
    'REACTIVACION_INDIVIDUAL_INTERNET',
    'ReconectarCliente',
    'Pendiente',
    'Finalizada',
    'Pendiente',
    'Activo',
    'Registro actualizado automáticamente por ejecución de la opción Reconectar Cliente desde el grid técnico',
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
    VALOR7,
    OBSERVACION,
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
    'Valor4:Estado pm cab,Valor5:Nuevo estado pm cab,Valor6:Estado pm det,Valo7:Nuevo estado pm det',
    'FINALIZA_PROCESOS_MASIVOS_POR_OPCION',
    'REACTIVACION_INDIVIDUAL_INTERNET',
    'ReconectarCliente',
    'Pendiente',
    'Finalizada',
    'Fallo',
    'Activo',
    'Registro actualizado automáticamente por ejecución de la opción Reconectar Cliente desde el grid técnico',
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente los registros parametrizados para dar de baja procesos masivos al reconectar cliente');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
/