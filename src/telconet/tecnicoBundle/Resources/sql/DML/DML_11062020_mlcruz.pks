SET SERVEROUTPUT ON
--Script de regularización para la eliminación de solicitudes de migración de servicios que ya no se encuentran en tecnología Tellion
DECLARE
  CURSOR Lc_ServiciosRegulaSolMigracion
  IS
    SELECT DISTINCT SERVICIO_INTERNET.ID_SERVICIO AS ID_SERVICIO_INTERNET,
      SOL_MIGRACION.ID_DETALLE_SOLICITUD          AS ID_SOL_MIGRACION,
      SOL_MIGRACION.ESTADO                        AS ESTADO_SOL_MIGRACION
    FROM DB_COMERCIAL.INFO_SERVICIO SERVICIO_INTERNET
    INNER JOIN DB_COMERCIAL.INFO_PUNTO PUNTO
    ON PUNTO.ID_PUNTO = SERVICIO_INTERNET.PUNTO_ID
    INNER JOIN DB_COMERCIAL.INFO_PLAN_CAB PLAN
    ON PLAN.ID_PLAN = SERVICIO_INTERNET.PLAN_ID
    INNER JOIN DB_COMERCIAL.INFO_PLAN_DET PLAN_DET
    ON PLAN_DET.PLAN_ID = PLAN.ID_PLAN
    INNER JOIN DB_COMERCIAL.ADMI_PRODUCTO PROD_INTERNET_EN_PLAN
    ON PROD_INTERNET_EN_PLAN.ID_PRODUCTO = PLAN_DET.PRODUCTO_ID
    INNER JOIN DB_COMERCIAL.INFO_SERVICIO_TECNICO SERVICIO_TECNICO_INTERNET
    ON SERVICIO_TECNICO_INTERNET.SERVICIO_ID = SERVICIO_INTERNET.ID_SERVICIO
    INNER JOIN DB_COMERCIAL.INFO_DETALLE_SOLICITUD SOL_MIGRACION
    ON SOL_MIGRACION.SERVICIO_ID = SERVICIO_INTERNET.ID_SERVICIO
    INNER JOIN DB_COMERCIAL.ADMI_TIPO_SOLICITUD TIPO_SOL
    ON TIPO_SOL.ID_TIPO_SOLICITUD = SOL_MIGRACION.TIPO_SOLICITUD_ID
    INNER JOIN DB_INFRAESTRUCTURA.INFO_ELEMENTO OLT
    ON OLT.ID_ELEMENTO = SERVICIO_TECNICO_INTERNET.ELEMENTO_ID
    INNER JOIN DB_INFRAESTRUCTURA.ADMI_MODELO_ELEMENTO MODELO_OLT
    ON MODELO_OLT.ID_MODELO_ELEMENTO = OLT.MODELO_ELEMENTO_ID
    INNER JOIN DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO TIPO_OLT
    ON TIPO_OLT.ID_TIPO_ELEMENTO = MODELO_OLT.TIPO_ELEMENTO_ID
    INNER JOIN DB_INFRAESTRUCTURA.ADMI_MARCA_ELEMENTO MARCA_OLT
    ON MARCA_OLT.ID_MARCA_ELEMENTO = MODELO_OLT.MARCA_ELEMENTO_ID
    INNER JOIN DB_INFRAESTRUCTURA.INFO_ELEMENTO ONT
    ON ONT.ID_ELEMENTO = SERVICIO_TECNICO_INTERNET.ELEMENTO_CLIENTE_ID
    INNER JOIN DB_INFRAESTRUCTURA.ADMI_MODELO_ELEMENTO MODELO_ONT
    ON MODELO_ONT.ID_MODELO_ELEMENTO = ONT.MODELO_ELEMENTO_ID
    INNER JOIN DB_INFRAESTRUCTURA.ADMI_MARCA_ELEMENTO MARCA_ONT
    ON MARCA_ONT.ID_MARCA_ELEMENTO             = MODELO_ONT.MARCA_ELEMENTO_ID
    WHERE PROD_INTERNET_EN_PLAN.NOMBRE_TECNICO = 'INTERNET'
    AND PLAN_DET.ESTADO                        = PLAN.ESTADO
    AND PROD_INTERNET_EN_PLAN.EMPRESA_COD      = '18'
    AND TIPO_OLT.NOMBRE_TIPO_ELEMENTO          = 'OLT'
    AND MARCA_OLT.NOMBRE_MARCA_ELEMENTO        = 'HUAWEI'
    AND MARCA_ONT.NOMBRE_MARCA_ELEMENTO        = 'HUAWEI'
    AND TIPO_SOL.DESCRIPCION_SOLICITUD         = 'SOLICITUD MIGRACION'
    AND SOL_MIGRACION.ESTADO                  IN ('In-Corte', 'Pendiente', 'PrePlanificada', 'Planificada', 'Detenido', 'Replanificada', 
                                                  'AsignadoTarea', 'Asignada');
TYPE Lt_FetchArray
IS
  TABLE OF Lc_ServiciosRegulaSolMigracion%ROWTYPE;
  Lt_ServiciosRegulaSolMigracion Lt_FetchArray;
  Le_BulkErrors             EXCEPTION;
  PRAGMA                    EXCEPTION_INIT(Le_BulkErrors, -24381);
  Lv_Mensaje                VARCHAR2(4000);
  Lv_Proceso                VARCHAR2(20) := 'Regularizar';
  Ln_IndexCierraTareas      NUMBER;
BEGIN
  IF Lc_ServiciosRegulaSolMigracion%ISOPEN THEN
    CLOSE Lc_ServiciosRegulaSolMigracion;
  END IF;
  OPEN Lc_ServiciosRegulaSolMigracion;
  LOOP
    FETCH Lc_ServiciosRegulaSolMigracion BULK COLLECT
    INTO Lt_ServiciosRegulaSolMigracion LIMIT 1000;
    FORALL Ln_Index IN 1..Lt_ServiciosRegulaSolMigracion.COUNT SAVE EXCEPTIONS
    UPDATE DB_COMERCIAL.INFO_DETALLE_SOLICITUD
    SET ESTADO                 = 'Eliminada'
    WHERE ID_DETALLE_SOLICITUD = Lt_ServiciosRegulaSolMigracion(Ln_Index).ID_SOL_MIGRACION;
    FORALL Ln_Index IN 1..Lt_ServiciosRegulaSolMigracion.COUNT SAVE EXCEPTIONS
    INSERT
    INTO DB_COMERCIAL.INFO_DETALLE_SOL_HIST
      (
        ID_SOLICITUD_HISTORIAL,
        DETALLE_SOLICITUD_ID,
        ESTADO,
        OBSERVACION,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
      )
      VALUES
      (
        DB_COMERCIAL.SEQ_INFO_DETALLE_SOL_HIST.NEXTVAL,
        Lt_ServiciosRegulaSolMigracion(Ln_Index).ID_SOL_MIGRACION,
        'Eliminada',
        'La solicitud pasa de estado '
        || Lt_ServiciosRegulaSolMigracion(Ln_Index).ESTADO_SOL_MIGRACION
        || ' a Eliminada debido a que el servicio ya no se encuentra bajo tecnología Tellion',
        'regulaSolMigra',
        SYSDATE,
        '127.0.0.1'
      );
    Ln_IndexCierraTareas := Lt_ServiciosRegulaSolMigracion.FIRST;
    WHILE (Ln_IndexCierraTareas IS NOT NULL)
    LOOP
      DB_INFRAESTRUCTURA.INFRK_TRANSACCIONES.INFRP_CIERRA_TAREAS_SOLICITUD( Lt_ServiciosRegulaSolMigracion(Ln_IndexCierraTareas).ID_SOL_MIGRACION, 
                                                                            Lv_Proceso, 
                                                                            Lv_Mensaje);
      Ln_IndexCierraTareas := Lt_ServiciosRegulaSolMigracion.NEXT(Ln_IndexCierraTareas);
    END LOOP;
    EXIT
  WHEN Lc_ServiciosRegulaSolMigracion%NOTFOUND;
  END LOOP;
  CLOSE Lc_ServiciosRegulaSolMigracion;
  COMMIT;
  SYS.DBMS_OUTPUT.PUT_LINE('Se han eliminado todos las solicitudes de migración asociadas a servicios que ya no se encuentran en tecnología Tellion');
EXCEPTION
WHEN Le_BulkErrors THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' 
                            || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' 
                            || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
--Creación de parámetros para servicios de MD
DECLARE
  Ln_IdParamsServiciosMd NUMBER;
BEGIN
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_CAB
    (
      ID_PARAMETRO,
      NOMBRE_PARAMETRO,
      DESCRIPCION,
      ESTADO,
      USR_CREACION,
      FE_CREACION,
      IP_CREACION
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
      'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD',
      'Parámetros para diversas validaciones de planes MD',
      'Activo',
      'mlcruz',
      CURRENT_TIMESTAMP,
      '127.0.0.1'
    );
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
    'Modelos parametrizados por tecnología y por equipo',
    'MODELOS_EQUIPOS',
    'HUAWEI',
    'MA5608T',
    'WIFI DUAL BAND',
    'HS8M8245WG04',
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
    'Modelos parametrizados por tecnología y por equipo',
    'MODELOS_EQUIPOS',
    'HUAWEI',
    'MA5608T',
    'WIFI DUAL BAND',
    'HS8M8245WG06',
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente los modelos de ont Wifi dual band');
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
    'Modelos parametrizados por tecnología y por equipo',
    'MODELOS_EQUIPOS',
    'HUAWEI',
    'MA5608T',
    'EXTENDER DUAL BAND',
    'WA8M8011VW09',
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente los modelos de Extender Dual Band');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
/
SET SERVEROUTPUT ON
--Ejecución de proceso de regularización
DECLARE
BEGIN
  DB_INFRAESTRUCTURA.INFRK_TRANSACCIONES.P_REGULA_EQUIPOS_W_Y_EXTENDER;
  SYS.DBMS_OUTPUT.PUT_LINE('PROCESO DE REGULARIZACIÓN EJECUTADO CORRECTAMENTE');
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
END;
/