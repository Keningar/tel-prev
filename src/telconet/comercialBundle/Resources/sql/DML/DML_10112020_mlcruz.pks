SET SERVEROUTPUT ON
--Creación de parámetros para servicios de TN con los ID_PERSONA_ROL que no deben verificar valor de deuda
DECLARE
  Ln_IdParamsServiciosTn    NUMBER;
  Ln_IdParamsServiciosMd    NUMBER;
  Lv_Valor1Opcion1          VARCHAR2(8) := 'TRASLADO';
  Lv_Valor1Opcion3          VARCHAR2(19) := 'CAMBIO_RAZON_SOCIAL';
  Lv_Valor2EstadosGeneral   VARCHAR2(29) := 'ESTADOS_SERVICIOS_PERMITIDOS';
  Lv_Valor2EstadosXProd     VARCHAR2(36) := 'ESTADOS_SERVICIOS_X_PROD_PERMITIDOS';
  Lv_Valor2EstadosXProdCrs  VARCHAR2(50) := 'ESTADOS_SERVICIOS_X_PROD_FLUJO_PERSONALIZADO';
BEGIN
  SELECT ID_PARAMETRO
  INTO Ln_IdParamsServiciosTn
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='PARAMETROS_ASOCIADOS_A_SERVICIOS_TN';
  SELECT ID_PARAMETRO
  INTO Ln_IdParamsServiciosMd
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='PARAMETROS_ASOCIADOS_A_SERVICIOS_MD';

  --TRASLADO
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
    'Estados de los servicios parametrizados para permitir un traslado en TN',
    Lv_Valor1Opcion1,
    Lv_Valor2EstadosGeneral,
    'Activo',
    NULL,
    NULL,
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
    'Estados de los servicios parametrizados para permitir un traslado en TN',
    Lv_Valor1Opcion1,
    Lv_Valor2EstadosGeneral,
    'In-Corte',
    NULL,
    NULL,
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
    'Estados de los servicios parametrizados para permitir un traslado en MD',
    Lv_Valor1Opcion1,
    Lv_Valor2EstadosGeneral,
    'Activo',
    NULL,
    NULL,
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
    'Estados de los servicios parametrizados para permitir un traslado en MD',
    Lv_Valor1Opcion1,
    Lv_Valor2EstadosXProd,
    'WDB_Y_EDB',
    'PendienteAp',
    'PrePlanificada',
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
    'Estados de los servicios parametrizados para permitir un traslado en MD',
    Lv_Valor1Opcion1,
    Lv_Valor2EstadosXProd,
    'WDB_Y_EDB',
    'Asignada',
    'PrePlanificada',
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
    'Estados de los servicios parametrizados para permitir un traslado en MD',
    Lv_Valor1Opcion1,
    Lv_Valor2EstadosXProd,
    'WDB_Y_EDB',
    'AsignadoTarea',
    'PrePlanificada',
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
    'Estados de los servicios parametrizados para permitir un traslado en MD',
    Lv_Valor1Opcion1,
    Lv_Valor2EstadosXProd,
    'WDB_Y_EDB',
    'Detenida',
    'PrePlanificada',
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
    'Estados de los servicios parametrizados para permitir un traslado en MD',
    Lv_Valor1Opcion1,
    Lv_Valor2EstadosXProd,
    'WDB_Y_EDB',
    'Detenido',
    'PrePlanificada',
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
    'Estados de los servicios parametrizados para permitir un traslado en MD',
    Lv_Valor1Opcion1,
    Lv_Valor2EstadosXProd,
    'WDB_Y_EDB',
    'PrePlanificada',
    'PrePlanificada',
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
    'Estados de los servicios parametrizados para permitir un traslado en MD',
    Lv_Valor1Opcion1,
    Lv_Valor2EstadosXProd,
    'WDB_Y_EDB',
    'Planificada',
    'PrePlanificada',
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
    'Estados de los servicios parametrizados para permitir un traslado en MD',
    Lv_Valor1Opcion1,
    Lv_Valor2EstadosXProd,
    'WDB_Y_EDB',
    'Activo',
    'PendienteAp',
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18'
  );

  --CAMBIO DE RAZÓN SOCIAL
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
    'Nombres técnicos de productos que no deben comparar estados de servicios en el CRS',
    Lv_Valor1Opcion3,
    'NOMBRES_TECNICOS_PRODS_PERMITIDOS_SIN_ACTIVAR',
    'WDB_Y_EDB',
    NULL,
    NULL,
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
    'Estados de los servicios parametrizados para permitir un cambio de razón social en MD',
    Lv_Valor1Opcion3,
    Lv_Valor2EstadosXProdCrs,
    'WDB_Y_EDB',
    'PendienteAp',
    'PrePlanificada',
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
    'Estados de los servicios parametrizados para permitir un cambio de razón social en MD',
    Lv_Valor1Opcion3,
    Lv_Valor2EstadosXProdCrs,
    'WDB_Y_EDB',
    'Asignada',
    'PrePlanificada',
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
    'Estados de los servicios parametrizados para permitir un cambio de razón social en MD',
    Lv_Valor1Opcion3,
    Lv_Valor2EstadosXProdCrs,
    'WDB_Y_EDB',
    'AsignadoTarea',
    'PrePlanificada',
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
    'Estados de los servicios parametrizados para permitir un cambio de razón social en MD',
    Lv_Valor1Opcion3,
    Lv_Valor2EstadosXProdCrs,
    'WDB_Y_EDB',
    'Detenida',
    'PrePlanificada',
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
    'Estados de los servicios parametrizados para permitir un cambio de razón social en MD',
    Lv_Valor1Opcion3,
    Lv_Valor2EstadosXProdCrs,
    'WDB_Y_EDB',
    'Detenido',
    'PrePlanificada',
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
    'Estados de los servicios parametrizados para permitir un cambio de razón social en MD',
    Lv_Valor1Opcion3,
    Lv_Valor2EstadosXProdCrs,
    'WDB_Y_EDB',
    'PrePlanificada',
    'PrePlanificada',
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
    'Estados de los servicios parametrizados para permitir un cambio de razón social en MD',
    Lv_Valor1Opcion3,
    Lv_Valor2EstadosXProdCrs,
    'WDB_Y_EDB',
    'Planificada',
    'PrePlanificada',
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente los parámetros con los estados para traslados y cambio de razón social');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
/
--Se modifica el código de la empresa para el parámetro que almacena el precio del w+ap
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
SET EMPRESA_COD = '18',
USR_ULT_MOD = 'mlcruz',
FE_ULT_MOD = SYSDATE
WHERE ID_PARAMETRO_DET = 12849;
--Se actualizan productos W+AP para que el usuario no se confunda con el que se encuentra Activo
UPDATE DB_COMERCIAL.ADMI_PRODUCTO
SET DESCRIPCION_PRODUCTO = 'Obsoleto-' || DESCRIPCION_PRODUCTO,
ESTADO = 'Inactivo',
NOMBRE_TECNICO = 'OTROS'
WHERE DESCRIPCION_PRODUCTO = 'Wifi DB Premium + Extender DB'
AND ID_PRODUCTO <> 1357;
--Se modifica estado de producto W+AP permitiendo la activación del mismo como un servicio nuevo
UPDATE DB_COMERCIAL.ADMI_PRODUCTO
SET ESTADO = 'Activo'
WHERE ID_PRODUCTO = 1357;
COMMIT;
/