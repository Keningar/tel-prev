SET SERVEROUTPUT ON
--Bloque anónimo para crear una nueva tarea para la gestión de cambio de CPE WIFI
DECLARE
  Ln_IdProceso NUMBER(5,0);
BEGIN
  SELECT ID_PROCESO
  INTO Ln_IdProceso
  FROM DB_SOPORTE.ADMI_PROCESO
  WHERE NOMBRE_PROCESO='TAREAS DE IPCCL2 - Small Business';
  INSERT
  INTO DB_SOPORTE.ADMI_TAREA
    (
      ID_TAREA,
      PROCESO_ID,
      NOMBRE_TAREA,
      DESCRIPCION_TAREA,
      ESTADO,
      USR_CREACION,
      USR_ULT_MOD,
      FE_CREACION,
      FE_ULT_MOD
    )
    VALUES
    (
      DB_SOPORTE.SEQ_ADMI_TAREA.NEXTVAL,
      Ln_IdProceso,
      'CAMBIO DE EQUIPO SMALL BUSINESS',
      'Tarea para la gestión del cambio de cpe wifi en un servicio Internet Small Business',
      'Activo',
      'mlcruz',
      'mlcruz',
      SYSDATE,
      SYSDATE
    );
  INSERT
  INTO DB_SOPORTE.ADMI_PROCESO_EMPRESA
    (
      ID_PROCESO_EMPRESA,
      PROCESO_ID,
      EMPRESA_COD,
      ESTADO,
      USR_CREACION,
      FE_CREACION
    )
    VALUES
    (
      DB_SOPORTE.SEQ_ADMI_PROCESO_EMPRESA.NEXTVAL,
      Ln_IdProceso,
      '10',
      'Activo',
      'mlcruz',
      SYSDATE
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Registros de proceso y tarea ingresados correctamente');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
SET VALOR2             = 'MSR900/JF812A'
WHERE ID_PARAMETRO_DET =
  (SELECT ID_PARAMETRO_DET
  FROM DB_GENERAL.ADMI_PARAMETRO_DET DET
  INNER JOIN DB_GENERAL.ADMI_PARAMETRO_CAB CAB
  ON CAB.ID_PARAMETRO        = DET.PARAMETRO_ID
  WHERE CAB.NOMBRE_PARAMETRO = 'MODELOS_CPE_WIFI_ACTIVACION_SB_TELLION'
  AND CAB.ESTADO             = 'Activo'
  AND DET.ESTADO             = 'Activo'
  AND DET.VALOR2             = 'MSR900'
  );
COMMIT;