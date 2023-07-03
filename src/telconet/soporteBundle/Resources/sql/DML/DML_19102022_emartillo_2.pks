/**
 *
 * Insert de parámetros .
 * Creacion de las Tareas
 *
 * @author Emmanuel Fernando Martillo Siavichay <emartillo@telconet.ec>
 * @version 1.0 19-10-2022
 * 
 **/
SET SERVEROUTPUT ON
Declare 
Ln_Idproceso Number(5,0);
BEGIN
  INSERT
  INTO DB_SOPORTE.ADMI_PROCESO
    (
      ID_PROCESO,
      NOMBRE_PROCESO,
      DESCRIPCION_PROCESO,
      ESTADO,
      USR_CREACION,
      USR_ULT_MOD,
      FE_CREACION,
      FE_ULT_MOD,
      VISIBLE
    )
    VALUES
    (
      DB_SOPORTE.SEQ_ADMI_PROCESO.NEXTVAL,
      'SOLICITAR NUEVO SERVICIO NETLIFECAM IN-DOOR',
      'Proceso para Solicitar un Nuevo Servicio de tipo NetlifeCam In-door',
      'Activo',
      'emartillo',
      'emartillo',
      SYSDATE,
      SYSDATE,
      'NO'
    );
  SELECT ID_PROCESO
  INTO Ln_IdProceso
  FROM DB_SOPORTE.ADMI_PROCESO
  WHERE NOMBRE_PROCESO='SOLICITAR NUEVO SERVICIO NETLIFECAM IN-DOOR';
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
      'INSTALACION NETLIFECAM - Servicio Basico de Visualizacion Remota Residencial',
      'Tarea que ejecuta la instalacion del servicio NetlifeCam In-door.',
      'Activo',
      'emartillo',
      'emartillo',
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
      '18',
      'Activo',
      'emartillo',
      SYSDATE
    );
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/

SET SERVEROUTPUT ON
Declare 
Ln_Idproceso Number(5,0);
BEGIN
  INSERT
  INTO DB_SOPORTE.ADMI_PROCESO
    (
      ID_PROCESO,
      NOMBRE_PROCESO,
      DESCRIPCION_PROCESO,
      ESTADO,
      USR_CREACION,
      USR_ULT_MOD,
      FE_CREACION,
      FE_ULT_MOD,
      VISIBLE
    )
    VALUES
    (
      DB_SOPORTE.SEQ_ADMI_PROCESO.NEXTVAL,
      'SOLICITAR NUEVO SERVICIO NETLIFECAM OUTDOOR',
      'Proceso para Solicitar un Nuevo Servicio de tipo NetlifeCam Outdoor',
      'Activo',
      'emartillo',
      'emartillo',
      SYSDATE,
      SYSDATE,
      'NO'
    );
  SELECT ID_PROCESO
  INTO Ln_IdProceso
  FROM DB_SOPORTE.ADMI_PROCESO
  WHERE NOMBRE_PROCESO='SOLICITAR NUEVO SERVICIO NETLIFECAM OUTDOOR';
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
      'INSTALACION NETLIFECAM - Outdoor',
      'Tarea que ejecuta la instalacion del servicio NetlifeCam Outdoor.',
      'Activo',
      'emartillo',
      'emartillo',
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
      '18',
      'Activo',
      'emartillo',
      SYSDATE
    );
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;

/
SET SERVEROUTPUT ON
Declare 
Ln_Idproceso Number(5,0);
BEGIN
   
  SELECT ID_PROCESO
  INTO Ln_IdProceso
  FROM DB_SOPORTE.ADMI_PROCESO
  WHERE NOMBRE_PROCESO='TAREAS DE OPU - REQUERIMIENTO A OPU'
  AND DESCRIPCION_PROCESO = 'TAREAS DE OPU';
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
      'Reubicación de NETLIFECAM - Servicio Básico de Visualización Remota Residencial',
      'Tarea que ejecuta la Reubicación del servicio NetlifeCam In-door',
      'Activo',
      'emartillo',
      'emartillo',
      SYSDATE,
      SYSDATE
    );
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
SET SERVEROUTPUT ON
Declare 
Ln_Idproceso Number(5,0);
BEGIN
  
  SELECT ID_PROCESO
  INTO Ln_IdProceso
  FROM DB_SOPORTE.ADMI_PROCESO
  WHERE NOMBRE_PROCESO='TAREAS DE OPU - REQUERIMIENTO A OPU'
  AND DESCRIPCION_PROCESO = 'TAREAS DE OPU';
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
      'Soporte de NETLIFECAM - Servicio Básico de Visualización Remota Residencial',
      'Tarea que ejecuta soporte para  el servicio NetlifeCam In-door',
      'Activo',
      'emartillo',
      'emartillo',
      SYSDATE,
      SYSDATE
    );
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/

SET SERVEROUTPUT ON
Declare 
Ln_Idproceso Number(5,0);
BEGIN

    
  SELECT ID_PROCESO
  INTO Ln_IdProceso
  FROM DB_SOPORTE.ADMI_PROCESO
  WHERE NOMBRE_PROCESO='TAREAS DE OPU - REQUERIMIENTO A OPU'
  AND DESCRIPCION_PROCESO = 'TAREAS DE OPU';
  
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
      'Reubicación de NETLIFECAM Outdoor',
      'Tarea que ejecuta la Reubicación del servicio NetlifeCam Outdoor',
      'Activo',
      'emartillo',
      'emartillo',
      SYSDATE,
      SYSDATE
    );
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
SET SERVEROUTPUT ON
Declare 
Ln_Idproceso Number(5,0);
BEGIN
  SELECT ID_PROCESO
  INTO Ln_IdProceso
  FROM DB_SOPORTE.ADMI_PROCESO
  WHERE NOMBRE_PROCESO='TAREAS DE OPU - REQUERIMIENTO A OPU'
  AND DESCRIPCION_PROCESO = 'TAREAS DE OPU';
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
      'Soporte de NETLIFECAM Outdoor',
      'Tarea que ejecuta soporte para  el servicio NetlifeCam Outdoor',
      'Activo',
      'emartillo',
      'emartillo',
      SYSDATE,
      SYSDATE
    );
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/