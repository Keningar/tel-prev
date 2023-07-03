SET SERVEROUTPUT ON
/*
 * Script de regularización para la eliminación de solicitudes de planificación asociadas a servicios de Internet en estado Activo, que fueron
 * creadas erróneamente por desarrollo de cableado ethernet
 */
DECLARE
  CURSOR Lc_ServsReguSolPlanifCableado
  IS
    SELECT DISTINCT SERVICIO_INTERNET.ID_SERVICIO AS ID_SERVICIO_INTERNET,
      SOL_PLANIF_CABLEADO_ERROR.ID_DETALLE_SOLICITUD          AS ID_SOL_PLANIF_CABL_ERROR,
      SOL_PLANIF_CABLEADO_ERROR.ESTADO                        AS ESTADO_SOL_PLANIF_CABL_ERROR,
      TO_CHAR(SOL_PLANIF_CABLEADO_ERROR.FE_CREACION, 'DD-MM-YYYY HH24:MM:SS') AS FE_SOL_PLANIF_CABL_ERROR
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
    INNER JOIN DB_COMERCIAL.INFO_DETALLE_SOLICITUD SOL_PLANIF_CABLEADO_ERROR
    ON SOL_PLANIF_CABLEADO_ERROR.SERVICIO_ID = SERVICIO_INTERNET.ID_SERVICIO
    INNER JOIN DB_COMERCIAL.ADMI_TIPO_SOLICITUD TIPO_SOL
    ON TIPO_SOL.ID_TIPO_SOLICITUD = SOL_PLANIF_CABLEADO_ERROR.TIPO_SOLICITUD_ID
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
    AND TIPO_SOL.DESCRIPCION_SOLICITUD         = 'SOLICITUD PLANIFICACION'
    AND SOL_PLANIF_CABLEADO_ERROR.ESTADO IN ('PrePlanificada', 'Planificada', 'Detenido', 'Replanificada', 
                                             'AsignadoTarea', 'Asignada')
    AND SERVICIO_INTERNET.ESTADO = 'Activo';
TYPE Lt_FetchArray
IS
  TABLE OF Lc_ServsReguSolPlanifCableado%ROWTYPE;
  Lt_ServsReguSolPlanifCableado Lt_FetchArray;
  Le_BulkErrors             EXCEPTION;
  PRAGMA                    EXCEPTION_INIT(Le_BulkErrors, -24381);
  Lv_Mensaje                VARCHAR2(4000);
  Lv_Proceso                VARCHAR2(20) := 'Regularizar';
  Ln_IndexCierraTareas      NUMBER;
BEGIN
  IF Lc_ServsReguSolPlanifCableado%ISOPEN THEN
    CLOSE Lc_ServsReguSolPlanifCableado;
  END IF;
  OPEN Lc_ServsReguSolPlanifCableado;
  LOOP
    FETCH Lc_ServsReguSolPlanifCableado BULK COLLECT
    INTO Lt_ServsReguSolPlanifCableado LIMIT 1000;
    FORALL Ln_Index IN 1..Lt_ServsReguSolPlanifCableado.COUNT SAVE EXCEPTIONS
    UPDATE DB_COMERCIAL.INFO_DETALLE_SOLICITUD
    SET ESTADO                 = 'Eliminada'
    WHERE ID_DETALLE_SOLICITUD = Lt_ServsReguSolPlanifCableado(Ln_Index).ID_SOL_PLANIF_CABL_ERROR;
    FORALL Ln_Index IN 1..Lt_ServsReguSolPlanifCableado.COUNT SAVE EXCEPTIONS
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
        Lt_ServsReguSolPlanifCableado(Ln_Index).ID_SOL_PLANIF_CABL_ERROR,
        'Eliminada',
        'La solicitud pasa de estado '
        || Lt_ServsReguSolPlanifCableado(Ln_Index).ESTADO_SOL_PLANIF_CABL_ERROR
        || ' a Eliminada debido a que el servicio ya se encuentra en estado Activo',
        'reguSolCableado',
        SYSDATE,
        '127.0.0.1'
      );
    Ln_IndexCierraTareas := Lt_ServsReguSolPlanifCableado.FIRST;
    WHILE (Ln_IndexCierraTareas IS NOT NULL)
    LOOP
      DB_INFRAESTRUCTURA.INFRK_TRANSACCIONES.INFRP_CIERRA_TAREAS_SOLICITUD( 
        Lt_ServsReguSolPlanifCableado(Ln_IndexCierraTareas).ID_SOL_PLANIF_CABL_ERROR, 
        Lv_Proceso, 
        Lv_Mensaje);
      Ln_IndexCierraTareas := Lt_ServsReguSolPlanifCableado.NEXT(Ln_IndexCierraTareas);
    END LOOP;
    EXIT
  WHEN Lc_ServsReguSolPlanifCableado%NOTFOUND;
  END LOOP;
  CLOSE Lc_ServsReguSolPlanifCableado;
  COMMIT;
  SYS.DBMS_OUTPUT.PUT_LINE('Se han eliminado todos las solicitudes de planificación asociadas a servicios de Internet en estado Activo');
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
--Creación de parámetros con nombres técnicos de productos que se eliminarán al eliminar el servicio de Internet
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
    'Estados de servicio para consultar en un IN de acuerdo al nombre técnico de un producto',
    'ESTADOS_SERVICIOS_IN',
    'EXTENDER_DUAL_BAND',
    'Planificada',
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
    'Estados de servicio para consultar en un IN de acuerdo al nombre técnico de un producto',
    'ESTADOS_SERVICIOS_IN',
    'EXTENDER_DUAL_BAND',
    'Replanificada',
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
    EMPRESA_COD,
    VALOR6
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Modelos parametrizados por tecnología y por equipo',
    'NOMBRES_TECNICOS_ELIMINACION_SIMULTANEA_X_INTERNET',
    'EXTENDER_DUAL_BAND',
    NULL,
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18',
    NULL
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado los nombres técnicos de los servicios a eliminarse cuando se elimine el servicio de Internet');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
/

--Creación de parámetros para equipos V5 con su respectivo extender
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
    EMPRESA_COD,
    VALOR6
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Modelos parametrizados por tecnología y por equipo',
    'MODELOS_EQUIPOS',
    'HUAWEI',
    'MA5608T',
    'ONT V5',
    'EG8M8145V5G06',
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18',
    'ONT'
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
    EMPRESA_COD,
    VALOR6
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
    'K562E',
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18',
    NULL
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
    'Modelos de extender parametrizados por ont',
    'MODELOS_EXTENDERS_POR_ONT',
    'HUAWEI',
    'ONT V5',
    'EG8M8145V5G06',
    'WA8M8011VW09',
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
    'Modelos de extender parametrizados por ont',
    'MODELOS_EXTENDERS_POR_ONT',
    'HUAWEI',
    'ONT V5',
    'EG8M8145V5G06',
    'WA8011V',
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
    'Modelos de extender parametrizados por ont',
    'MODELOS_EXTENDERS_POR_ONT',
    'HUAWEI',
    'ONT V5',
    'EG8M8145V5G06',
    'K562E',
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente los modelos de V5 y modelos parametrizados de extender por onts');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
/

--Se crean nuevos parámetros para los estados de las solicitudes de planificación
DECLARE
  Ln_IdParamsServiciosMd    NUMBER;
  Ln_IdParamModelos         NUMBER;
BEGIN
  SELECT ID_PARAMETRO
  INTO Ln_IdParamsServiciosMd
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='PARAMETROS_ASOCIADOS_A_SERVICIOS_MD';

  UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
  SET VALOR4 = 'PERMITE_CONCATENAR_INFO_EQUIPOS_COORDINAR',
  USR_ULT_MOD = 'mlcruz',
  FE_ULT_MOD = SYSDATE,
  IP_ULT_MOD = '127.0.0.1'
  WHERE PARAMETRO_ID = Ln_IdParamsServiciosMd
  AND VALOR1 = 'ESTADOS_SOLICITUDES_ABIERTAS'
  AND VALOR2 = 'SOLICITUD AGREGAR EQUIPO'
  AND VALOR3 = 'PrePlanificada'
  AND ESTADO = 'Activo';

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
    'Estados válidos de solicitudes de acuerdo a su descripción',
    'ESTADOS_SOLICITUDES_ABIERTAS',
    'SOLICITUD PLANIFICACION',
    'PrePlanificada',
    'PERMITE_CONCATENAR_INFO_EQUIPOS_COORDINAR',
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
    'Estados válidos de solicitudes de acuerdo a su descripción',
    'ESTADOS_SOLICITUDES_ABIERTAS',
    'SOLICITUD PLANIFICACION',
    'Planificada',
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
    'Estados válidos de solicitudes de acuerdo a su descripción',
    'ESTADOS_SOLICITUDES_ABIERTAS',
    'SOLICITUD PLANIFICACION',
    'Detenido',
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
    'Estados válidos de solicitudes de acuerdo a su descripción',
    'ESTADOS_SOLICITUDES_ABIERTAS',
    'SOLICITUD PLANIFICACION',
    'Replanificada',
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
    'Estados válidos de solicitudes de acuerdo a su descripción',
    'ESTADOS_SOLICITUDES_ABIERTAS',
    'SOLICITUD PLANIFICACION',
    'AsignadoTarea',
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
    'Estados válidos de solicitudes de acuerdo a su descripción',
    'ESTADOS_SOLICITUDES_ABIERTAS',
    'SOLICITUD PLANIFICACION',
    'Asignada',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente los estados abiertos para SOLICITUD PLANIFICACION');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
/
--Creación de parámetros para servicios Extenders que deben seguir flujo diferente en el cambio de razón social
DECLARE
  Ln_IdParamsServiciosMd    NUMBER;
  Lv_Valor1Opcion3          VARCHAR2(19) := 'CAMBIO_RAZON_SOCIAL';
  Lv_Valor2EstadosXProdCrs  VARCHAR2(50) := 'ESTADOS_SERVICIOS_X_PROD_FLUJO_PERSONALIZADO';
BEGIN
  SELECT ID_PARAMETRO
  INTO Ln_IdParamsServiciosMd
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='PARAMETROS_ASOCIADOS_A_SERVICIOS_MD';

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
    'EXTENDER_DUAL_BAND',
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
    'EXTENDER_DUAL_BAND',
    'Pendiente',
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
    'EXTENDER_DUAL_BAND',
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
    'EXTENDER_DUAL_BAND',
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
    'EXTENDER_DUAL_BAND',
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
    'EXTENDER_DUAL_BAND',
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
    'EXTENDER_DUAL_BAND',
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
    'EXTENDER_DUAL_BAND',
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
    'Estados de los servicios parametrizados para permitir un cambio de razón social en MD',
    Lv_Valor1Opcion3,
    Lv_Valor2EstadosXProdCrs,
    'EXTENDER_DUAL_BAND',
    'Replanificada',
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

--Creación de parámetros con las relaciones necesarias para la gestión simultánea de PYL
DECLARE
  Ln_IdParamsServiciosMd NUMBER;
BEGIN
  SELECT ID_PARAMETRO
  INTO Ln_IdParamsServiciosMd
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='PARAMETROS_ASOCIADOS_A_SERVICIOS_MD';
  --PLANIFICAR
  /*
   * PLANIFICACION_1
   * Aplica para clientes nuevos, clientes existentes y traslados
   * INTERNET -> SOLICITUD PLANIFICACION
   */
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
    'Relación de solicitudes asociadas a servicios que se gestionan de manera simultánea',
    'GESTION_PYL_SIMULTANEA',
    'SERVICIO_GESTIONADO',
    'PLANIFICAR',
    'ID_PRODUCTO',
    '63',
    'SOLICITUD PLANIFICACION',
    'PLANIFICACION_1',
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18'
  );

  /*
   * PLANIFICACION_1
   * Aplica para clientes nuevos y traslados
   * INTERNET -> SOLICITUD PLANIFICACION
   *             EXTENDER DUAL BAND -> SOLICITUD AGREGAR EQUIPO
   */
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
    'Relación de solicitudes asociadas a servicios que se gestionan de manera simultánea',
    'GESTION_PYL_SIMULTANEA',
    'SERVICIO_GESTIONADO_SIMULTANEAMENTE',
    'PLANIFICAR',
    'ID_PRODUCTO',
    '1232',
    'SOLICITUD AGREGAR EQUIPO',
    'PLANIFICACION_1',
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18'
  );

  /*
   * PLANIFICACION_1
   * Aplica para traslados
   * INTERNET -> SOLICITUD PLANIFICACION
   *             INTERNET -> SOLICITUD AGREGAR EQUIPO por cambio de ont
   */
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
    'Relación de solicitudes asociadas a servicios que se gestionan de manera simultánea',
    'GESTION_PYL_SIMULTANEA',
    'SERVICIO_GESTIONADO_SIMULTANEAMENTE',
    'PLANIFICAR',
    'ID_PRODUCTO',
    '63',
    'SOLICITUD AGREGAR EQUIPO',
    'PLANIFICACION_1',
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18'
  );


  /*
   * PLANIFICACION_2
   * Aplica para clientes existentes y traslados
   * INTERNET -> SOLICITUD AGREGAR EQUIPO por cambio de ont
   */
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
    'Relación de solicitudes asociadas a servicios que se gestionan de manera simultánea',
    'GESTION_PYL_SIMULTANEA',
    'SERVICIO_GESTIONADO',
    'PLANIFICAR',
    'ID_PRODUCTO',
    '63',
    'SOLICITUD AGREGAR EQUIPO',
    'PLANIFICACION_2',
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18'
  );

  /*
   * PLANIFICACION_2
   * Aplica para clientes existentes y traslados
   * INTERNET -> SOLICITUD AGREGAR EQUIPO por cambio de ont
   *             EXTENDER DUAL BAND -> SOLICITUD AGREGAR EQUIPO
   */
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
    'Relación de solicitudes asociadas a servicios que se gestionan de manera simultánea',
    'GESTION_PYL_SIMULTANEA',
    'SERVICIO_GESTIONADO_SIMULTANEAMENTE',
    'PLANIFICAR',
    'ID_PRODUCTO',
    '1232',
    'SOLICITUD AGREGAR EQUIPO',
    'PLANIFICACION_2',
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18'
  );

  /*
   * PLANIFICACION_2
   * Aplica para traslados
   * INTERNET -> SOLICITUD AGREGAR EQUIPO por cambio de ont
   *             INTERNET -> SOLICITUD PLANIFICACION por traslado
   */
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
    'Relación de solicitudes asociadas a servicios que se gestionan de manera simultánea',
    'GESTION_PYL_SIMULTANEA',
    'SERVICIO_GESTIONADO_SIMULTANEAMENTE',
    'PLANIFICAR',
    'ID_PRODUCTO',
    '63',
    'SOLICITUD PLANIFICACION',
    'PLANIFICACION_2',
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18'
  );

  /*
   * PLANIFICACION_3
   * Aplica para clientes nuevos, clientes existentes y traslados
   * EXTENDER DUAL BAND -> SOLICITUD AGREGAR EQUIPO
   */
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
    'Relación de solicitudes asociadas a servicios que se gestionan de manera simultánea',
    'GESTION_PYL_SIMULTANEA',
    'SERVICIO_GESTIONADO',
    'PLANIFICAR',
    'ID_PRODUCTO',
    '1232',
    'SOLICITUD AGREGAR EQUIPO',
    'PLANIFICACION_3',
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18'
  );

  /*
   * PLANIFICACION_3
   * Aplica para clientes nuevos, clientes existentes y traslados
   * EXTENDER DUAL BAND -> SOLICITUD AGREGAR EQUIPO
   *                       EXTENDER DUAL BAND -> SOLICITUD AGREGAR EQUIPO
   */
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
    'Relación de solicitudes asociadas a servicios que se gestionan de manera simultánea',
    'GESTION_PYL_SIMULTANEA',
    'SERVICIO_GESTIONADO_SIMULTANEAMENTE',
    'PLANIFICAR',
    'ID_PRODUCTO',
    '1232',
    'SOLICITUD AGREGAR EQUIPO',
    'PLANIFICACION_3',
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18'
  );

  /*
   * PLANIFICACION_3
   * Aplica para clientes nuevos y traslados
   * EXTENDER DUAL BAND -> SOLICITUD AGREGAR EQUIPO
   *                       INTERNET -> SOLICITUD PLANIFICACION por nuevo o traslado
   */
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
    'Relación de solicitudes asociadas a servicios que se gestionan de manera simultánea',
    'GESTION_PYL_SIMULTANEA',
    'SERVICIO_GESTIONADO_SIMULTANEAMENTE',
    'PLANIFICAR',
    'ID_PRODUCTO',
    '63',
    'SOLICITUD PLANIFICACION',
    'PLANIFICACION_3',
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18'
  );

  /*
   * PLANIFICACION_3
   * Aplica para clientes existentes y traslados
   * EXTENDER DUAL BAND -> SOLICITUD AGREGAR EQUIPO
   *                       INTERNET -> SOLICITUD AGREGAR EQUIPO por cambio de ont
   */
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
    'Relación de solicitudes asociadas a servicios que se gestionan de manera simultánea',
    'GESTION_PYL_SIMULTANEA',
    'SERVICIO_GESTIONADO_SIMULTANEAMENTE',
    'PLANIFICAR',
    'ID_PRODUCTO',
    '63',
    'SOLICITUD AGREGAR EQUIPO',
    'PLANIFICACION_3',
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18'
  );


  --REPLANIFICAR
  /*
   * REPLANIFICACION_1
   * Aplica para clientes nuevos, clientes existentes y traslados
   * INTERNET -> SOLICITUD PLANIFICACION
   */
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
    'Relación de solicitudes asociadas a servicios que se gestionan de manera simultánea',
    'GESTION_PYL_SIMULTANEA',
    'SERVICIO_GESTIONADO',
    'REPLANIFICAR',
    'ID_PRODUCTO',
    '63',
    'SOLICITUD PLANIFICACION',
    'REPLANIFICACION_1',
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18'
  );

  /*
   * REPLANIFICACION_1
   * Aplica para clientes nuevos y traslados
   * INTERNET -> SOLICITUD PLANIFICACION
   *             EXTENDER DUAL BAND -> SOLICITUD AGREGAR EQUIPO
   */
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
    'Relación de solicitudes asociadas a servicios que se gestionan de manera simultánea',
    'GESTION_PYL_SIMULTANEA',
    'SERVICIO_GESTIONADO_SIMULTANEAMENTE',
    'REPLANIFICAR',
    'ID_PRODUCTO',
    '1232',
    'SOLICITUD AGREGAR EQUIPO',
    'REPLANIFICACION_1',
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18'
  );

  /*
   * REPLANIFICACION_1
   * Aplica para traslados
   * INTERNET -> SOLICITUD PLANIFICACION
   *             INTERNET -> SOLICITUD AGREGAR EQUIPO por cambio de ont
   */
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
    'Relación de solicitudes asociadas a servicios que se gestionan de manera simultánea',
    'GESTION_PYL_SIMULTANEA',
    'SERVICIO_GESTIONADO_SIMULTANEAMENTE',
    'REPLANIFICAR',
    'ID_PRODUCTO',
    '63',
    'SOLICITUD AGREGAR EQUIPO',
    'REPLANIFICACION_1',
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18'
  );


  /*
   * REPLANIFICACION_2
   * Aplica para clientes existentes y traslados
   * INTERNET -> SOLICITUD AGREGAR EQUIPO por cambio de ont
   */
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
    'Relación de solicitudes asociadas a servicios que se gestionan de manera simultánea',
    'GESTION_PYL_SIMULTANEA',
    'SERVICIO_GESTIONADO',
    'REPLANIFICAR',
    'ID_PRODUCTO',
    '63',
    'SOLICITUD AGREGAR EQUIPO',
    'REPLANIFICACION_2',
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18'
  );

  /*
   * REPLANIFICACION_2
   * Aplica para clientes existentes y traslados
   * INTERNET -> SOLICITUD AGREGAR EQUIPO por cambio de ont
   *             EXTENDER DUAL BAND -> SOLICITUD AGREGAR EQUIPO
   */
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
    'Relación de solicitudes asociadas a servicios que se gestionan de manera simultánea',
    'GESTION_PYL_SIMULTANEA',
    'SERVICIO_GESTIONADO_SIMULTANEAMENTE',
    'REPLANIFICAR',
    'ID_PRODUCTO',
    '1232',
    'SOLICITUD AGREGAR EQUIPO',
    'REPLANIFICACION_2',
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18'
  );

  /*
   * REPLANIFICACION_2
   * Aplica para traslados
   * INTERNET -> SOLICITUD AGREGAR EQUIPO por cambio de ont
   *             INTERNET -> SOLICITUD PLANIFICACION por traslado
   */
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
    'Relación de solicitudes asociadas a servicios que se gestionan de manera simultánea',
    'GESTION_PYL_SIMULTANEA',
    'SERVICIO_GESTIONADO_SIMULTANEAMENTE',
    'REPLANIFICAR',
    'ID_PRODUCTO',
    '63',
    'SOLICITUD PLANIFICACION',
    'REPLANIFICACION_2',
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18'
  );

  /*
   * REPLANIFICACION_3
   * Aplica para clientes nuevos, clientes existentes y traslados
   * EXTENDER DUAL BAND -> SOLICITUD AGREGAR EQUIPO
   */
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
    'Relación de solicitudes asociadas a servicios que se gestionan de manera simultánea',
    'GESTION_PYL_SIMULTANEA',
    'SERVICIO_GESTIONADO',
    'REPLANIFICAR',
    'ID_PRODUCTO',
    '1232',
    'SOLICITUD AGREGAR EQUIPO',
    'REPLANIFICACION_3',
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18'
  );

  /*
   * REPLANIFICACION_3
   * Aplica para clientes nuevos, clientes existentes y traslados
   * EXTENDER DUAL BAND -> SOLICITUD AGREGAR EQUIPO
   *                       EXTENDER DUAL BAND -> SOLICITUD AGREGAR EQUIPO
   */
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
    'Relación de solicitudes asociadas a servicios que se gestionan de manera simultánea',
    'GESTION_PYL_SIMULTANEA',
    'SERVICIO_GESTIONADO_SIMULTANEAMENTE',
    'REPLANIFICAR',
    'ID_PRODUCTO',
    '1232',
    'SOLICITUD AGREGAR EQUIPO',
    'REPLANIFICACION_3',
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18'
  );

  /*
   * REPLANIFICACION_3
   * Aplica para clientes nuevos y traslados
   * EXTENDER DUAL BAND -> SOLICITUD AGREGAR EQUIPO
   *                       INTERNET -> SOLICITUD PLANIFICACION por nuevo o traslado
   */
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
    'Relación de solicitudes asociadas a servicios que se gestionan de manera simultánea',
    'GESTION_PYL_SIMULTANEA',
    'SERVICIO_GESTIONADO_SIMULTANEAMENTE',
    'REPLANIFICAR',
    'ID_PRODUCTO',
    '63',
    'SOLICITUD PLANIFICACION',
    'REPLANIFICACION_3',
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18'
  );

  /*
   * REPLANIFICACION_3
   * Aplica para clientes existentes y traslados
   * EXTENDER DUAL BAND -> SOLICITUD AGREGAR EQUIPO
   *                       INTERNET -> SOLICITUD AGREGAR EQUIPO por cambio de ont
   */
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
    'Relación de solicitudes asociadas a servicios que se gestionan de manera simultánea',
    'GESTION_PYL_SIMULTANEA',
    'SERVICIO_GESTIONADO_SIMULTANEAMENTE',
    'REPLANIFICAR',
    'ID_PRODUCTO',
    '63',
    'SOLICITUD AGREGAR EQUIPO',
    'REPLANIFICACION_3',
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18'
  );


  --DETENER
  /*
   * DETENCION_1
   * Aplica para clientes nuevos, clientes existentes y traslados
   * INTERNET -> SOLICITUD PLANIFICACION
   */
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
    'Relación de solicitudes asociadas a servicios que se gestionan de manera simultánea',
    'GESTION_PYL_SIMULTANEA',
    'SERVICIO_GESTIONADO',
    'DETENER',
    'ID_PRODUCTO',
    '63',
    'SOLICITUD PLANIFICACION',
    'DETENCION_1',
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18'
  );

  /*
   * DETENCION_1
   * Aplica para clientes nuevos y traslados
   * INTERNET -> SOLICITUD PLANIFICACION
   *             EXTENDER DUAL BAND -> SOLICITUD AGREGAR EQUIPO
   */
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
    'Relación de solicitudes asociadas a servicios que se gestionan de manera simultánea',
    'GESTION_PYL_SIMULTANEA',
    'SERVICIO_GESTIONADO_SIMULTANEAMENTE',
    'DETENER',
    'ID_PRODUCTO',
    '1232',
    'SOLICITUD AGREGAR EQUIPO',
    'DETENCION_1',
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18'
  );

  /*
   * DETENCION_1
   * Aplica para traslados
   * INTERNET -> SOLICITUD PLANIFICACION
   *             INTERNET -> SOLICITUD AGREGAR EQUIPO por cambio de ont
   */
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
    'Relación de solicitudes asociadas a servicios que se gestionan de manera simultánea',
    'GESTION_PYL_SIMULTANEA',
    'SERVICIO_GESTIONADO_SIMULTANEAMENTE',
    'DETENER',
    'ID_PRODUCTO',
    '63',
    'SOLICITUD AGREGAR EQUIPO',
    'DETENCION_1',
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18'
  );

  /*
   * DETENCION_2
   * Aplica para clientes existentes y traslados
   * INTERNET -> SOLICITUD AGREGAR EQUIPO por cambio de ont
   */
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
    'Relación de solicitudes asociadas a servicios que se gestionan de manera simultánea',
    'GESTION_PYL_SIMULTANEA',
    'SERVICIO_GESTIONADO',
    'DETENER',
    'ID_PRODUCTO',
    '63',
    'SOLICITUD AGREGAR EQUIPO',
    'DETENCION_2',
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18'
  );

  /*
   * DETENCION_2
   * Aplica para clientes existentes y traslados
   * INTERNET -> SOLICITUD AGREGAR EQUIPO por cambio de ont
   *             EXTENDER DUAL BAND -> SOLICITUD AGREGAR EQUIPO
   */
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
    'Relación de solicitudes asociadas a servicios que se gestionan de manera simultánea',
    'GESTION_PYL_SIMULTANEA',
    'SERVICIO_GESTIONADO_SIMULTANEAMENTE',
    'DETENER',
    'ID_PRODUCTO',
    '1232',
    'SOLICITUD AGREGAR EQUIPO',
    'DETENCION_2',
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18'
  );

  /*
   * DETENCION_3
   * Aplica para clientes nuevos, clientes existentes y traslados
   * EXTENDER DUAL BAND -> SOLICITUD AGREGAR EQUIPO
   */
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
    'Relación de solicitudes asociadas a servicios que se gestionan de manera simultánea',
    'GESTION_PYL_SIMULTANEA',
    'SERVICIO_GESTIONADO',
    'DETENER',
    'ID_PRODUCTO',
    '1232',
    'SOLICITUD AGREGAR EQUIPO',
    'DETENCION_3',
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18'
  );

  /*
   * DETENCION_3
   * Aplica para clientes existentes y traslados
   * EXTENDER DUAL BAND -> SOLICITUD AGREGAR EQUIPO
   *                       INTERNET -> SOLICITUD AGREGAR EQUIPO por cambio de ont
   */
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
    'Relación de solicitudes asociadas a servicios que se gestionan de manera simultánea',
    'GESTION_PYL_SIMULTANEA',
    'SERVICIO_GESTIONADO_SIMULTANEAMENTE',
    'DETENER',
    'ID_PRODUCTO',
    '63',
    'SOLICITUD AGREGAR EQUIPO',
    'DETENCION_3',
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18'
  );


  --RECHAZAR
  /*
   * RECHAZO_1
   * Aplica para clientes nuevos y traslados
   * INTERNET -> SOLICITUD PLANIFICACION
   */
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
    'Relación de solicitudes asociadas a servicios que se gestionan de manera simultánea',
    'GESTION_PYL_SIMULTANEA',
    'SERVICIO_GESTIONADO',
    'RECHAZAR',
    'ID_PRODUCTO',
    '63',
    'SOLICITUD PLANIFICACION',
    'RECHAZO_1',
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18'
  );

  /*
   * RECHAZO_1
   * Aplica para clientes nuevos y traslados
   * INTERNET -> SOLICITUD PLANIFICACION
   *             EXTENDER DUAL BAND -> SOLICITUD AGREGAR EQUIPO
   */
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
    'Relación de solicitudes asociadas a servicios que se gestionan de manera simultánea',
    'GESTION_PYL_SIMULTANEA',
    'SERVICIO_GESTIONADO_SIMULTANEAMENTE',
    'RECHAZAR',
    'ID_PRODUCTO',
    '1232',
    'SOLICITUD AGREGAR EQUIPO',
    'RECHAZO_1',
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18'
  );

  /*
   * RECHAZO_1
   * Aplica para traslados
   * INTERNET -> SOLICITUD PLANIFICACION
   *             INTERNET -> SOLICITUD AGREGAR EQUIPO por cambio de ont
   */
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
    'Relación de solicitudes asociadas a servicios que se gestionan de manera simultánea',
    'GESTION_PYL_SIMULTANEA',
    'SERVICIO_GESTIONADO_SIMULTANEAMENTE',
    'RECHAZAR',
    'ID_PRODUCTO',
    '63',
    'SOLICITUD AGREGAR EQUIPO',
    'RECHAZO_1',
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18'
  );


  /*
   * RECHAZO_2
   * Aplica para clientes existentes y traslados
   * INTERNET -> SOLICITUD AGREGAR EQUIPO por cambio de ont
   */
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
    'Relación de solicitudes asociadas a servicios que se gestionan de manera simultánea',
    'GESTION_PYL_SIMULTANEA',
    'SERVICIO_GESTIONADO',
    'RECHAZAR',
    'ID_PRODUCTO',
    '63',
    'SOLICITUD AGREGAR EQUIPO',
    'RECHAZO_2',
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18'
  );

  /*
   * RECHAZO_2
   * Aplica para clientes existentes y traslados
   * INTERNET -> SOLICITUD AGREGAR EQUIPO por cambio de ont
   *             EXTENDER DUAL BAND -> SOLICITUD AGREGAR EQUIPO
   */
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
    'Relación de solicitudes asociadas a servicios que se gestionan de manera simultánea',
    'GESTION_PYL_SIMULTANEA',
    'SERVICIO_GESTIONADO_SIMULTANEAMENTE',
    'RECHAZAR',
    'ID_PRODUCTO',
    '1232',
    'SOLICITUD AGREGAR EQUIPO',
    'RECHAZO_2',
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18'
  );

  /*
   * RECHAZO_3
   * Aplica para clientes existentes y traslados
   * EXTENDER DUAL BAND -> SOLICITUD AGREGAR EQUIPO
   */
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
    'Relación de solicitudes asociadas a servicios que se gestionan de manera simultánea',
    'GESTION_PYL_SIMULTANEA',
    'SERVICIO_GESTIONADO',
    'RECHAZAR',
    'ID_PRODUCTO',
    '1232',
    'SOLICITUD AGREGAR EQUIPO',
    'RECHAZO_3',
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18'
  );

  /*
   * RECHAZO_3
   * Aplica para clientes existentes y traslados
   * EXTENDER DUAL BAND -> SOLICITUD AGREGAR EQUIPO
   *                       INTERNET -> SOLICITUD AGREGAR EQUIPO por cambio de ont
   */
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
    'Relación de solicitudes asociadas a servicios que se gestionan de manera simultánea',
    'GESTION_PYL_SIMULTANEA',
    'SERVICIO_GESTIONADO_SIMULTANEAMENTE',
    'RECHAZAR',
    'ID_PRODUCTO',
    '63',
    'SOLICITUD AGREGAR EQUIPO',
    'RECHAZO_3',
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18'
  );


  --ANULAR
  /*
   * ANULACION_1
   * Aplica para clientes nuevos y traslados
   * INTERNET -> SOLICITUD PLANIFICACION
   */
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
    'Relación de solicitudes asociadas a servicios que se gestionan de manera simultánea',
    'GESTION_PYL_SIMULTANEA',
    'SERVICIO_GESTIONADO',
    'ANULAR',
    'ID_PRODUCTO',
    '63',
    'SOLICITUD PLANIFICACION',
    'ANULACION_1',
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18'
  );

  /*
   * ANULACION_1
   * Aplica para clientes nuevos y traslados
   * INTERNET -> SOLICITUD PLANIFICACION
   *             EXTENDER DUAL BAND -> SOLICITUD AGREGAR EQUIPO
   */
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
    'Relación de solicitudes asociadas a servicios que se gestionan de manera simultánea',
    'GESTION_PYL_SIMULTANEA',
    'SERVICIO_GESTIONADO_SIMULTANEAMENTE',
    'ANULAR',
    'ID_PRODUCTO',
    '1232',
    'SOLICITUD AGREGAR EQUIPO',
    'ANULACION_1',
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18'
  );

  /*
   * ANULACION_1
   * Aplica para traslados
   * INTERNET -> SOLICITUD PLANIFICACION
   *             INTERNET -> SOLICITUD AGREGAR EQUIPO por cambio de ont
   */
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
    'Relación de solicitudes asociadas a servicios que se gestionan de manera simultánea',
    'GESTION_PYL_SIMULTANEA',
    'SERVICIO_GESTIONADO_SIMULTANEAMENTE',
    'ANULAR',
    'ID_PRODUCTO',
    '63',
    'SOLICITUD AGREGAR EQUIPO',
    'ANULACION_1',
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18'
  );

  /*
   * ANULACION_2
   * Aplica para clientes existentes y traslados
   * INTERNET -> SOLICITUD AGREGAR EQUIPO por cambio de ont
   */
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
    'Relación de solicitudes asociadas a servicios que se gestionan de manera simultánea',
    'GESTION_PYL_SIMULTANEA',
    'SERVICIO_GESTIONADO',
    'ANULAR',
    'ID_PRODUCTO',
    '63',
    'SOLICITUD AGREGAR EQUIPO',
    'ANULACION_2',
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18'
  );

  /*
   * ANULACION_2
   * Aplica para clientes existentes y traslados
   * INTERNET -> SOLICITUD AGREGAR EQUIPO por cambio de ont
   *             EXTENDER DUAL BAND -> SOLICITUD AGREGAR EQUIPO
   */
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
    'Relación de solicitudes asociadas a servicios que se gestionan de manera simultánea',
    'GESTION_PYL_SIMULTANEA',
    'SERVICIO_GESTIONADO_SIMULTANEAMENTE',
    'ANULAR',
    'ID_PRODUCTO',
    '1232',
    'SOLICITUD AGREGAR EQUIPO',
    'ANULACION_2',
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18'
  );

  /*
   * ANULACION_3
   * Aplica para clientes existentes y traslados
   * EXTENDER DUAL BAND -> SOLICITUD AGREGAR EQUIPO
   */
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
    'Relación de solicitudes asociadas a servicios que se gestionan de manera simultánea',
    'GESTION_PYL_SIMULTANEA',
    'SERVICIO_GESTIONADO',
    'ANULAR',
    'ID_PRODUCTO',
    '1232',
    'SOLICITUD AGREGAR EQUIPO',
    'ANULACION_3',
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18'
  );

  /*
   * ANULACION_3
   * Aplica para clientes existentes y traslados
   * EXTENDER DUAL BAND -> SOLICITUD AGREGAR EQUIPO
   *                       INTERNET -> SOLICITUD AGREGAR EQUIPO por cambio de ont
   */
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
    'Relación de solicitudes asociadas a servicios que se gestionan de manera simultánea',
    'GESTION_PYL_SIMULTANEA',
    'SERVICIO_GESTIONADO_SIMULTANEAMENTE',
    'ANULAR',
    'ID_PRODUCTO',
    '63',
    'SOLICITUD AGREGAR EQUIPO',
    'ANULACION_3',
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado las relaciones entre servicios simultáneos para MD');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
/

INSERT
INTO DB_COMERCIAL.ADMI_CARACTERISTICA
  (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    TIPO
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'MOTIVO_CREACION_SOLICITUD',
    'T',
    'Activo',
    SYSDATE,
    'mlcruz',
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
    TIPO
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'TIPO_ONT_NUEVO',
    'T',
    'Activo',
    SYSDATE,
    'mlcruz',
    'TECNICA'
  );
COMMIT;
/