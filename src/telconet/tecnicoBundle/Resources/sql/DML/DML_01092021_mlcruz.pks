SET SERVEROUTPUT ON
--Creación de parámetros usados para el corte masivo de MD
DECLARE
  Ln_IdParamEstadosProcesosDet NUMBER;
BEGIN
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_CAB
    (
      ID_PARAMETRO,
      NOMBRE_PARAMETRO,
      DESCRIPCION,
      MODULO,
      ESTADO,
      USR_CREACION,
      FE_CREACION,
      IP_CREACION
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
      'ESTADOS_PROCESOS_MASIVOS_DET_TELCOS',
      'Parámetros para los estados de los detalles de los procesos masivos de Telcos',
      'TECNICO',
      'Activo',
      'mlcruz',
      CURRENT_TIMESTAMP,
      '127.0.0.1'
    );
  SELECT ID_PARAMETRO
  INTO Ln_IdParamEstadosProcesosDet
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='ESTADOS_PROCESOS_MASIVOS_DET_TELCOS';

  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamEstadosProcesosDet,
    'Estado del detalle del proceso masivo de Telcos',
    'Activo',
    'Activo',
    'Activo',
    'mlcruz',
    SYSDATE,
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
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamEstadosProcesosDet,
    'Estado del detalle del proceso masivo de Telcos',
    'In-Corte',
    'In-Corte',
    'Activo',
    'mlcruz',
    SYSDATE,
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
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamEstadosProcesosDet,
    'Estado del detalle del proceso masivo de Telcos',
    'Pendiente',
    'Pendiente',
    'Activo',
    'mlcruz',
    SYSDATE,
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
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamEstadosProcesosDet,
    'Estado del detalle del proceso masivo de Telcos',
    'PrePendiente',
    'PrePendiente',
    'Activo',
    'mlcruz',
    SYSDATE,
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
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamEstadosProcesosDet,
    'Estado del detalle del proceso masivo de Telcos',
    'Fallo',
    'Fallo',
    'Activo',
    'mlcruz',
    SYSDATE,
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
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamEstadosProcesosDet,
    'Estado del detalle del proceso masivo de Telcos',
    'ExcedioTiempo',
    'Excedio Tiempo',
    'Activo',
    'mlcruz',
    SYSDATE,
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
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamEstadosProcesosDet,
    'Estado del detalle del proceso masivo de Telcos',
    'ErrorCancel',
    'ErrorCancel',
    'Activo',
    'mlcruz',
    SYSDATE,
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
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamEstadosProcesosDet,
    'Estado del detalle del proceso masivo de Telcos',
    'Finalizado',
    'Finalizado',
    'Activo',
    'mlcruz',
    SYSDATE,
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
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamEstadosProcesosDet,
    'Estado del detalle del proceso masivo de Telcos',
    'Cancel',
    'Cancel',
    'Activo',
    'mlcruz',
    SYSDATE,
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
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamEstadosProcesosDet,
    'Estado del detalle del proceso masivo de Telcos',
    'Cancelado',
    'Cancelado',
    'Activo',
    'mlcruz',
    SYSDATE,
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
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamEstadosProcesosDet,
    'Estado del detalle del proceso masivo de Telcos',
    'Activo',
    'Activo',
    'Activo',
    'mlcruz',
    SYSDATE,
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
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamEstadosProcesosDet,
    'Estado del detalle del proceso masivo de Telcos',
    'In-Corte',
    'In-Corte',
    'Activo',
    'mlcruz',
    SYSDATE,
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
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamEstadosProcesosDet,
    'Estado del detalle del proceso masivo de Telcos',
    'Pendiente',
    'Pendiente',
    'Activo',
    'mlcruz',
    SYSDATE,
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
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamEstadosProcesosDet,
    'Estado del detalle del proceso masivo de Telcos',
    'PrePendiente',
    'PrePendiente',
    'Activo',
    'mlcruz',
    SYSDATE,
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
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamEstadosProcesosDet,
    'Estado del detalle del proceso masivo de Telcos',
    'Fallo',
    'Fallo',
    'Activo',
    'mlcruz',
    SYSDATE,
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
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamEstadosProcesosDet,
    'Estado del detalle del proceso masivo de Telcos',
    'ExcedioTiempo',
    'Excedio Tiempo',
    'Activo',
    'mlcruz',
    SYSDATE,
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
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamEstadosProcesosDet,
    'Estado del detalle del proceso masivo de Telcos',
    'ErrorCancel',
    'ErrorCancel',
    'Activo',
    'mlcruz',
    SYSDATE,
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
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamEstadosProcesosDet,
    'Estado del detalle del proceso masivo de Telcos',
    'Finalizado',
    'Finalizado',
    'Activo',
    'mlcruz',
    SYSDATE,
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
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamEstadosProcesosDet,
    'Estado del detalle del proceso masivo de Telcos',
    'Cancel',
    'Cancel',
    'Activo',
    'mlcruz',
    SYSDATE,
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
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamEstadosProcesosDet,
    'Estado del detalle del proceso masivo de Telcos',
    'Cancelado',
    'Cancelado',
    'Activo',
    'mlcruz',
    SYSDATE,
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
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamEstadosProcesosDet,
    'Estado del detalle del proceso masivo de Telcos',
    'PorEjecutar',
    'PorEjecutar',
    'Activo',
    'mlcruz',
    SYSDATE,
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
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamEstadosProcesosDet,
    'Estado del detalle del proceso masivo de Telcos',
    'FalloReintento',
    'FalloReintento',
    'Activo',
    'mlcruz',
    SYSDATE,
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
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamEstadosProcesosDet,
    'Estado del detalle del proceso masivo de Telcos',
    'SinEjecutar',
    'SinEjecutar',
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente los estados de los detalles de los procesos masivos');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
/
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
    'Listado de ids de formas de pago que muestran cuenta/tarjeta, tipo de cuenta/tarjeta y bancos',
    'CORTE_MASIVO',
    'IDS_FORMAS_PAGO_CUENTA_TARJETA_BANCOS',
    '3',
    NULL,
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
    'Listado de tipos de documentos permitidos en la consulta de corte masivo',
    'CORTE_MASIVO',
    'TIPOS_DE_DOCUMENTOS',
    'TODAS',
    'FAC,FACP,NDI',
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
    Ln_IdParamsServiciosMd,
    'Listado de tipos de documentos permitidos en la consulta de corte masivo',
    'CORTE_MASIVO',
    'TIPOS_DE_DOCUMENTOS',
    'Factura Recurrente',
    'FAC',
    'PERMITIDO_RESUMEN_PREVIO',
    'Clientes FC Recurrente',
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
    Ln_IdParamsServiciosMd,
    'Listado de tipos de documentos permitidos en la consulta de corte masivo',
    'CORTE_MASIVO',
    'TIPOS_DE_DOCUMENTOS',
    'Factura No Recurrente',
    'FACP',
    'PERMITIDO_RESUMEN_PREVIO',
    'Clientes FC No Recurrente',
    '2',
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
    'Listado de tipos de documentos permitidos en la consulta de corte masivo',
    'CORTE_MASIVO',
    'TIPOS_DE_DOCUMENTOS',
    'NDI',
    'NDI',
    'PERMITIDO_RESUMEN_PREVIO',
    'NDI',
    '3',
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
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
    Ln_IdParamsServiciosMd,
    'Listado de usuarios de creación permitidos por tipo de documento en la consulta de corte masivo',
    'CORTE_MASIVO',
    'USR_CREACION_IN_POR_TIPO_DE_DOCUMENTO',
    'FAC',
    'telcos',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente los USR_CREACION_IN_POR_TIPO_DE_DOCUMENTO en la opción de corte masivo');

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
    'Listado de usuarios de creación no permitidos por tipo de documento en la consulta de corte masivo',
    'CORTE_MASIVO',
    'USR_CREACION_NOT_IN_POR_TIPO_DE_DOCUMENTO',
    'FACP',
    'telcos',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente los USR_CREACION_NOT_IN_POR_TIPO_DE_DOCUMENTO en la opción de corte masivo');

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
    'Número de logines permitidos para cortar por lotes',
    'CORTE_MASIVO',
    'NUM_LOGINES_POR_LOTE',
    '1000',
    'INTERNET',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
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
    Ln_IdParamsServiciosMd,
    'Listado de estados no permitidos de la INFO_PROCESO_MASIVO_CAB',
    'CORTE_MASIVO',
    'FILTROS_REGISTROS_PM_NO_PERMITIDOS',
    'ESTADOS_INFO_PROCESO_MASIVO_CAB',
    'PorEjecutar',
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
    'Listado de estados no permitidos de la INFO_PROCESO_MASIVO_DET',
    'CORTE_MASIVO',
    'FILTROS_REGISTROS_PM_NO_PERMITIDOS',
    'ESTADOS_INFO_PROCESO_MASIVO_DET',
    'PorEjecutar',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se ha ingresado correctamente los nuevos estados de cortes masivos');

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
    'Listado de estados considerados por ejecutar de la INFO_DETALLE_SOLICITUD',
    'CORTE_MASIVO',
    'FILTROS_REGISTROS_PM_POR_EJECUTAR',
    'ESTADOS_VALIDADOS_INFO_DETALLE_SOLICITUD',
    'PorEjecutar',
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
    'Listado de estados considerados por ejecutar de la INFO_DETALLE_SOLICITUD',
    'CORTE_MASIVO',
    'FILTROS_REGISTROS_PM_POR_EJECUTAR',
    'ESTADOS_VALIDADOS_INFO_DETALLE_SOLICITUD',
    'EnEjecucion',
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
    'Listado de estados considerados por ejecutar de la INFO_DETALLE_SOL_CARACT',
    'CORTE_MASIVO',
    'FILTROS_REGISTROS_PM_POR_EJECUTAR',
    'ESTADOS_VALIDADOS_INFO_DETALLE_SOL_CARACT',
    'PorEjecutar',
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
    'Listado de estados considerados por ejecutar de la INFO_PROCESO_MASIVO_CAB',
    'CORTE_MASIVO',
    'FILTROS_REGISTROS_PM_POR_EJECUTAR',
    'ESTADOS_VALIDADOS_INFO_PROCESO_MASIVO_CAB',
    'PorEjecutar',
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
    'Listado de estados considerados por ejecutar de la INFO_PROCESO_MASIVO_DET',
    'CORTE_MASIVO',
    'FILTROS_REGISTROS_PM_POR_EJECUTAR',
    'ESTADOS_VALIDADOS_INFO_PROCESO_MASIVO_DET',
    'PorEjecutar',
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
    'Listado de estados considerados en ejecución de la INFO_DETALLE_SOLICITUD',
    'CORTE_MASIVO',
    'FILTROS_REGISTROS_PM_EN_EJECUCION',
    'ESTADOS_VALIDADOS_INFO_DETALLE_SOLICITUD',
    'EnEjecucion',
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
    'Listado de estados considerados en ejecución de la INFO_DETALLE_SOL_CARACT',
    'CORTE_MASIVO',
    'FILTROS_REGISTROS_PM_EN_EJECUCION',
    'ESTADOS_VALIDADOS_INFO_DETALLE_SOL_CARACT',
    'EnEjecucion',
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
    'Listado de estados considerados en ejecución de la INFO_PROCESO_MASIVO_CAB',
    'CORTE_MASIVO',
    'FILTROS_REGISTROS_PM_EN_EJECUCION',
    'ESTADOS_VALIDADOS_INFO_PROCESO_MASIVO_CAB',
    'Pendiente',
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
    'Listado de estados considerados en ejecución de la INFO_PROCESO_MASIVO_DET',
    'CORTE_MASIVO',
    'FILTROS_REGISTROS_PM_EN_EJECUCION',
    'ESTADOS_VALIDADOS_INFO_PROCESO_MASIVO_DET',
    'Pendiente',
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
    'Listado de estados considerados en ejecución de la INFO_PROCESO_MASIVO_DET',
    'CORTE_MASIVO',
    'FILTROS_REGISTROS_PM_EN_EJECUCION',
    'ESTADOS_VALIDADOS_INFO_PROCESO_MASIVO_DET',
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
    'Listado de estados considerados en ejecución de la INFO_PROCESO_MASIVO_DET',
    'CORTE_MASIVO',
    'FILTROS_REGISTROS_PM_EN_EJECUCION',
    'ESTADOS_VALIDADOS_INFO_PROCESO_MASIVO_DET',
    'Fallo',
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
    'Listado de estados considerados ejecutados de la INFO_DETALLE_SOLICITUD',
    'CORTE_MASIVO',
    'FILTROS_REGISTROS_PM_VERIFICA_FINALIZACION',
    'ESTADOS_VALIDADOS_INFO_DETALLE_SOLICITUD',
    'EnEjecucion',
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
    'Listado de estados considerados ejecutados de la INFO_DETALLE_SOL_CARACT',
    'CORTE_MASIVO',
    'FILTROS_REGISTROS_PM_VERIFICA_FINALIZACION',
    'ESTADOS_VALIDADOS_INFO_DETALLE_SOL_CARACT',
    'EnEjecucion',
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
    'Listado de estados considerados ejecutados de la INFO_PROCESO_MASIVO_CAB',
    'CORTE_MASIVO',
    'FILTROS_REGISTROS_PM_VERIFICA_FINALIZACION',
    'ESTADOS_VALIDADOS_INFO_PROCESO_MASIVO_CAB',
    'Pendiente',
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
    'Listado de estados considerados ejecutados de la INFO_PROCESO_MASIVO_CAB',
    'CORTE_MASIVO',
    'FILTROS_REGISTROS_PM_VERIFICA_FINALIZACION',
    'ESTADOS_VALIDADOS_INFO_PROCESO_MASIVO_CAB',
    'Finalizada',
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
    'Listado de estados considerados ejecutados de la INFO_PROCESO_MASIVO_DET',
    'CORTE_MASIVO',
    'FILTROS_REGISTROS_PM_VERIFICA_FINALIZACION',
    'ESTADOS_VALIDADOS_INFO_PROCESO_MASIVO_DET',
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
    'Listado de estados considerados ejecutados de la INFO_PROCESO_MASIVO_DET',
    'CORTE_MASIVO',
    'FILTROS_REGISTROS_PM_VERIFICA_FINALIZACION',
    'ESTADOS_VALIDADOS_INFO_PROCESO_MASIVO_DET',
    'Fallo',
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
    'Listado de todos los estados considerados de la INFO_DETALLE_SOL_CARACT para el corte masivo',
    'CORTE_MASIVO',
    'ESTADOS_INFO_DETALLE_SOL_CARACT_TODOS',
    'PorEjecutar',
    NULL,
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
    'Listado de todos los estados considerados de la INFO_DETALLE_SOL_CARACT para el corte masivo',
    'CORTE_MASIVO',
    'ESTADOS_INFO_DETALLE_SOL_CARACT_TODOS',
    'EnEjecucion',
    NULL,
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
    'Listado de todos los estados considerados de la INFO_DETALLE_SOL_CARACT para el corte masivo',
    'CORTE_MASIVO',
    'ESTADOS_INFO_DETALLE_SOL_CARACT_TODOS',
    'Ejecutada',
    NULL,
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
    'Listado de los estados ejecutados considerados de la INFO_DETALLE_SOL_CARACT para el corte masivo',
    'CORTE_MASIVO',
    'ESTADOS_INFO_DETALLE_SOL_CARACT_EJECUTADOS',
    'Ejecutada',
    NULL,
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
    'Listado de formas de pago no permitidas en la consulta de corte masivo',
    'CORTE_MASIVO',
    'FORMAS_PAGO_PERMITIDAS',
    'EFECTIVO',
    NULL,
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
    'Listado de formas de pago no permitidas en la consulta de corte masivo',
    'CORTE_MASIVO',
    'FORMAS_PAGO_PERMITIDAS',
    'DEBITO BANCARIO',
    NULL,
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
    'Listado de formas de pago no permitidas en la consulta de corte masivo',
    'CORTE_MASIVO',
    'FORMAS_PAGO_PERMITIDAS',
    'TRANSFERENCIA',
    NULL,
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
    'Listado de formas de pago no permitidas en la consulta de corte masivo',
    'CORTE_MASIVO',
    'FORMAS_PAGO_PERMITIDAS',
    'CARTERA DEMANDADA',
    NULL,
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
    'Listado de estados considerados para dar de baja de la INFO_DETALLE_SOLICITUD',
    'CORTE_MASIVO',
    'FILTROS_REGISTROS_DAR_DE_BAJA',
    'ESTADOS_VALIDADOS_INFO_DETALLE_SOLICITUD',
    'PorEjecutar',
    'SinEjecutar',
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
    'Listado de estados considerados para dar de baja de la INFO_DETALLE_SOLICITUD',
    'CORTE_MASIVO',
    'FILTROS_REGISTROS_DAR_DE_BAJA',
    'ESTADOS_VALIDADOS_INFO_DETALLE_SOLICITUD',
    'EnEjecucion',
    'ExcedioTiempo',
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
    'Listado de estados considerados para dar de baja de la INFO_DETALLE_SOL_CARACT',
    'CORTE_MASIVO',
    'FILTROS_REGISTROS_DAR_DE_BAJA',
    'ESTADOS_VALIDADOS_INFO_DETALLE_SOL_CARACT',
    'PorEjecutar',
    'SinEjecutar',
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
    'Listado de estados considerados para dar de baja de la INFO_DETALLE_SOL_CARACT',
    'CORTE_MASIVO',
    'FILTROS_REGISTROS_DAR_DE_BAJA',
    'ESTADOS_VALIDADOS_INFO_DETALLE_SOL_CARACT',
    'EnEjecucion',
    'ExcedioTiempo',
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
    'Listado de estados considerados para dar de baja de la INFO_PROCESO_MASIVO_CAB',
    'CORTE_MASIVO',
    'FILTROS_REGISTROS_DAR_DE_BAJA',
    'ESTADOS_VALIDADOS_INFO_PROCESO_MASIVO_CAB',
    'PorEjecutar',
    'SinEjecutar',
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
    'Listado de estados considerados para dar de baja de la INFO_PROCESO_MASIVO_CAB',
    'CORTE_MASIVO',
    'FILTROS_REGISTROS_DAR_DE_BAJA',
    'ESTADOS_VALIDADOS_INFO_PROCESO_MASIVO_CAB',
    'Pendiente',
    'ExcedioTiempo',
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
    'Listado de estados considerados para dar de baja de la INFO_PROCESO_MASIVO_DET',
    'CORTE_MASIVO',
    'FILTROS_REGISTROS_DAR_DE_BAJA',
    'ESTADOS_VALIDADOS_INFO_PROCESO_MASIVO_DET',
    'PorEjecutar',
    'SinEjecutar',
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
    'Listado de estados considerados para dar de baja de la INFO_PROCESO_MASIVO_DET',
    'CORTE_MASIVO',
    'FILTROS_REGISTROS_DAR_DE_BAJA',
    'ESTADOS_VALIDADOS_INFO_PROCESO_MASIVO_DET',
    'Pendiente',
    'ExcedioTiempo',
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
    'Listado de estados considerados para dar de baja de la INFO_PROCESO_MASIVO_DET',
    'CORTE_MASIVO',
    'FILTROS_REGISTROS_DAR_DE_BAJA',
    'ESTADOS_VALIDADOS_INFO_PROCESO_MASIVO_DET',
    'Fallo',
    'FalloReintento',
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
    'Listado de estados considerados para dar de baja de proceso ejecutado de la INFO_DETALLE_SOLICITUD',
    'CORTE_MASIVO',
    'FILTROS_REGISTROS_DAR_DE_BAJA_PROCESO_EJECUTADO',
    'ESTADOS_VALIDADOS_INFO_DETALLE_SOLICITUD',
    'EnEjecucion',
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
    'Listado de estados considerados para dar de baja de proceso ejecutado de la INFO_DETALLE_SOLICITUD',
    'CORTE_MASIVO',
    'FILTROS_REGISTROS_DAR_DE_BAJA_PROCESO_EJECUTADO',
    'ESTADOS_VALIDADOS_INFO_DETALLE_SOLICITUD',
    'Ejecutada',
    'Finalizada',
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
    'Listado de estados considerados para dar de baja de proceso ejecutado de la INFO_DETALLE_SOL_CARACT',
    'CORTE_MASIVO',
    'FILTROS_REGISTROS_DAR_DE_BAJA_PROCESO_EJECUTADO',
    'ESTADOS_VALIDADOS_INFO_DETALLE_SOL_CARACT',
    'Ejecutada',
    'Finalizada',
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
    'Listado de estados considerados para dar de baja de proceso ejecutado de la INFO_PROCESO_MASIVO_CAB',
    'CORTE_MASIVO',
    'FILTROS_REGISTROS_DAR_DE_BAJA_PROCESO_EJECUTADO',
    'ESTADOS_VALIDADOS_INFO_PROCESO_MASIVO_CAB',
    'Pendiente',
    'Finalizada',
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
    'Listado de estados considerados para dar de baja de proceso ejecutado de la INFO_PROCESO_MASIVO_CAB',
    'CORTE_MASIVO',
    'FILTROS_REGISTROS_DAR_DE_BAJA_PROCESO_EJECUTADO',
    'ESTADOS_VALIDADOS_INFO_PROCESO_MASIVO_CAB',
    'Finalizada',
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
    'Listado de estados considerados para el reporte de la INFO_DETALLE_SOL_CARACT',
    'CORTE_MASIVO',
    'FILTROS_REGISTROS_REPORTE',
    'ESTADOS_VALIDADOS_INFO_DETALLE_SOL_CARACT',
    'Finalizada',
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
    'Listado de estados considerados para el reporte de la INFO_DETALLE_SOL_CARACT',
    'CORTE_MASIVO',
    'FILTROS_REGISTROS_REPORTE',
    'ESTADOS_VALIDADOS_INFO_DETALLE_SOL_CARACT',
    'ExcedioTiempo',
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
    'Listado de estados considerados para el reporte de la INFO_DETALLE_SOL_CARACT',
    'CORTE_MASIVO',
    'FILTROS_REGISTROS_REPORTE',
    'ESTADOS_VALIDADOS_INFO_DETALLE_SOL_CARACT',
    'SinEjecutar',
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
    'Listado de estados considerados para el reporte de la INFO_PROCESO_MASIVO_DET',
    'CORTE_MASIVO',
    'FILTROS_REGISTROS_REPORTE',
    'ESTADOS_VALIDADOS_INFO_PROCESO_MASIVO_DET',
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
    'Listado de estados considerados para el reporte de la INFO_PROCESO_MASIVO_DET',
    'CORTE_MASIVO',
    'FILTROS_REGISTROS_REPORTE',
    'ESTADOS_VALIDADOS_INFO_PROCESO_MASIVO_DET',
    'FalloReintento',
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
    'Listado de estados considerados para el reporte de la INFO_PROCESO_MASIVO_DET',
    'CORTE_MASIVO',
    'FILTROS_REGISTROS_REPORTE',
    'ESTADOS_VALIDADOS_INFO_PROCESO_MASIVO_DET',
    'ExcedioTiempo',
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
    'Listado de estados considerados para el reporte de la INFO_PROCESO_MASIVO_DET',
    'CORTE_MASIVO',
    'FILTROS_REGISTROS_REPORTE',
    'ESTADOS_VALIDADOS_INFO_PROCESO_MASIVO_DET',
    'SinEjecutar',
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
    'Listado de estados considerados para el reporte de la INFO_PROCESO_MASIVO_DET',
    'CORTE_MASIVO',
    'PARAMS_CREACION_TAREA_REPORTE',
    'Correo Electronico',
    'REQUERIMIENTO INTERNO',
    'SISTEMAS: TELCOS -FINANCIERO',
    'COBRANZAS: CORTAR SERVICIO',
    'procesosmasivos',
    'Buen día Estimados Favor su ayuda revisando el archivo enviado por correo, ya que cuyos clientes ' ||
    'no pudieron cortarse de manera satisfactoria. ' || 'Atte. Procesos Masivos.',
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
    'Listado de estados considerados para el reporte de la INFO_PROCESO_MASIVO_DET',
    'CORTE_MASIVO',
    'PARAMS_ASIGNACION_TAREA_REPORTE',
    '18',
    'ppillajo',
    'Sistemas',
    'GUAYAQUIL',
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
    'Valor1: Nombre de parámetro, Valor2: Nombre de proceso, Valor3: Remitente, Valor4: Asunto',
    'REMITENTES_Y_ASUNTOS_CORREOS_POR_PROCESO',
    'REPORTE_CORTE_MASIVO_POR_LOTES',
    'notificaciones_telcos@telconet.ec',
    'Reporte de Clientes Fallidos del Corte Tarea #',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente los estados parametrizados para la ejecución por lotes');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
/
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
SET VALOR2 = 'EXPORTAR_ARCHIVO_CAMBIO_PLAN_MASIVO',
USR_ULT_MOD = 'mlcruz',
FE_ULT_MOD = SYSDATE
WHERE PARAMETRO_ID = 1253
AND VALOR1 = 'REMITENTES_Y_ASUNTOS_CORREOS_POR_PROCESO'
AND VALOR2 = 'EXPORTAR_ARCHIVO_CORTE_MASIVO'
AND ESTADO = 'Activo';

INSERT
INTO DB_COMERCIAL.ADMI_TIPO_SOLICITUD
  (
    ID_TIPO_SOLICITUD,
    DESCRIPCION_SOLICITUD,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    ESTADO,
    TAREA_ID,
    ITEM_MENU_ID,
    PROCESO_ID
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_TIPO_SOLICITUD.NEXTVAL,
    'SOLICITUD CORTE MASIVO POR LOTES',
    SYSDATE,
    'mlcruz',
    NULL,
    NULL,
    'Activo',
    NULL,
    NULL,
    NULL
  );
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
    'ID_PROCESO_MASIVO_CAB_FO',
    'N',
    'Activo',
    SYSDATE,
    'mlcruz',
    NULL,
    NULL,
    'TECNICA'
  );
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
    'ID_PROCESO_MASIVO_CAB_CO_RAD',
    'N',
    'Activo',
    SYSDATE,
    'mlcruz',
    NULL,
    NULL,
    'TECNICA'
  );
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
    'ID_PROCESO_MASIVO_CAB_ERROR_UM',
    'N',
    'Activo',
    SYSDATE,
    'mlcruz',
    NULL,
    NULL,
    'TECNICA'
  );
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
    'IDS_FORMAS_PAGO',
    'S',
    'Activo',
    SYSDATE,
    'mlcruz',
    NULL,
    NULL,
    'TECNICA'
  );
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
    'CODIGOS_TIPOS_DOC',
    'S',
    'Activo',
    SYSDATE,
    'mlcruz',
    NULL,
    NULL,
    'TECNICA'
  );

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
    'ENVIO_REPORTE',
    'S',
    'Activo',
    SYSDATE,
    'mlcruz',
    NULL,
    NULL,
    'TECNICA'
  );
COMMIT;
/