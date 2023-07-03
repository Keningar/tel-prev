SET SERVEROUTPUT ON
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
    'Listado de estados no permitidos de la INFO_PROCESO_MASIVO_CAB',
    'CORTE_MASIVO',
    'FILTROS_REGISTROS_PM_NO_PERMITIDOS',
    'ESTADOS_INFO_PROCESO_MASIVO_CAB',
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
    'Listado de estados no permitidos de la INFO_PROCESO_MASIVO_DET',
    'CORTE_MASIVO',
    'FILTROS_REGISTROS_PM_NO_PERMITIDOS',
    'ESTADOS_INFO_PROCESO_MASIVO_DET',
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
    'Listado de estados no permitidos de la INFO_PROCESO_MASIVO_DET',
    'CORTE_MASIVO',
    'FILTROS_REGISTROS_PM_NO_PERMITIDOS',
    'ESTADOS_INFO_PROCESO_MASIVO_DET',
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
    'Listado de tipos de procesos a buscar en la INFO_PROCESO_MASIVO_CAB',
    'CORTE_MASIVO',
    'FILTROS_REGISTROS_PM_NO_PERMITIDOS',
    'TIPOS_PROCESO_INFO_PROCESO_MASIVO_CAB',
    'ReconectarCliente',
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
    'TIPOS_PROCESO_INFO_PROCESO_MASIVO_CAB',
    'CortarCliente',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se crearon los parámetros correctamente para los filtros de los detalles de procesos masivos no permitidos');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' 
                           || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
/*
 * Script de regularización para la eliminación de solicitudes de planificación asociadas a servicios de Internet en estado Activo, que fueron
 * creadas erróneamente por desarrollo de cableado ethernet
 */
DECLARE
  CURSOR Lc_PmDetRegu
  IS
    SELECT DISTINCT PM_DET.ID_PROCESO_MASIVO_DET
    FROM DB_INFRAESTRUCTURA.INFO_PROCESO_MASIVO_CAB PM_CAB
    INNER JOIN DB_INFRAESTRUCTURA.INFO_PROCESO_MASIVO_DET PM_DET
    ON PM_DET.PROCESO_MASIVO_CAB_ID = PM_CAB.ID_PROCESO_MASIVO_CAB
    WHERE PM_CAB.TIPO_PROCESO = 'ReconectarCliente'
    AND PM_CAB.EMPRESA_ID = '18'
    AND PM_CAB.ESTADO = 'Finalizada'
    AND PM_DET.ESTADO = 'Pendiente';
TYPE Lt_FetchArray
IS
  TABLE OF Lc_PmDetRegu%ROWTYPE;
  Lt_PmDetRegu Lt_FetchArray;
  Le_BulkErrors             EXCEPTION;
  PRAGMA                    EXCEPTION_INIT(Le_BulkErrors, -24381);
  Lv_Mensaje                VARCHAR2(4000);
  Lv_Proceso                VARCHAR2(20) := 'Regularizar';
  Ln_IndexCierraTareas      NUMBER;
BEGIN
  IF Lc_PmDetRegu%ISOPEN THEN
    CLOSE Lc_PmDetRegu;
  END IF;
  OPEN Lc_PmDetRegu;
  LOOP
    FETCH Lc_PmDetRegu BULK COLLECT
    INTO Lt_PmDetRegu LIMIT 1000;
    FORALL Ln_Index IN 1..Lt_PmDetRegu.COUNT SAVE EXCEPTIONS
    UPDATE DB_INFRAESTRUCTURA.INFO_PROCESO_MASIVO_DET
    SET ESTADO = 'Eliminado',
    OBSERVACION = 'Detalle eliminado por regularización de procesos masivos asociados a servicios adicionales',
    USR_ULT_MOD = 'regulaPmDetAdic',
    FE_ULT_MOD = SYSDATE
    WHERE ID_PROCESO_MASIVO_DET = Lt_PmDetRegu(Ln_Index).ID_PROCESO_MASIVO_DET;
    EXIT
  WHEN Lc_PmDetRegu%NOTFOUND;
  END LOOP;
  CLOSE Lc_PmDetRegu;
  COMMIT;
  SYS.DBMS_OUTPUT.PUT_LINE('Se han eliminado todos los detalles de reconexión de proceso masivos creados erróneamente');
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
