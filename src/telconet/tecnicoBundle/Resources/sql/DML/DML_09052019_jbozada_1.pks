--=======================================================================
--  Se realiza creación de nuevo tipo de solicitud SOLICITUD VISITA TECNICA POR INSTALACION
--=======================================================================

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
    'SOLICITUD VISITA TECNICA POR INSTALACION',
    SYSDATE,
    'jbozada',
    NULL,
    NULL,
    'Activo',
    NULL,
    NULL,
    NULL
  );

commit;

/

--=======================================================================
--  Se realiza creación de nuevo tipo de solicitud SOLICITUD CAMBIO EQUIPO POR SOPORTE
--=======================================================================

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
    'SOLICITUD CAMBIO EQUIPO POR SOPORTE',
    SYSDATE,
    'jbozada',
    NULL,
    NULL,
    'Activo',
    NULL,
    NULL,
    NULL
  );

commit;

/

SET SERVEROUTPUT ON
--Creación de parámetros con coordenadas por país
DECLARE
  Ln_IdParamCoordElem NUMBER(5,0);
BEGIN
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_CAB
    (
      ID_PARAMETRO,
      NOMBRE_PARAMETRO,
      DESCRIPCION,
      MODULO,
      PROCESO,
      ESTADO,
      USR_CREACION,
      FE_CREACION,
      IP_CREACION
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
      'EQUIPOS_PERMITIDOS_CAMBIO_EQUIPO_POR_SOPORTE',
      'Parametrización de Modelos de equipos permitidos a utilizar en solicitudes de cambios de equipo por soporte',
      'TECNICO',
      'VALIDACION_DE_EQUIPOS',
      'Activo',
      'jbozada',
      CURRENT_TIMESTAMP,
      '127.0.0.1'
    );
  SELECT ID_PARAMETRO
  INTO Ln_IdParamCoordElem
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='EQUIPOS_PERMITIDOS_CAMBIO_EQUIPO_POR_SOPORTE';
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
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamCoordElem,
    'MODELO EQUIPO HW',
    'HS8M8245WG04',
    'DUAL BAND',
    NULL,
    NULL,
    'Activo',
    'jbozada',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
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
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamCoordElem,
    'MODELO EQUIPO HW',
    'EG8M8145V5G06',
    NULL,
    NULL,
    NULL,
    'Activo',
    'jbozada',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    '18'
  );

  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente los parámetros');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;

/


--Bloque anónimo para crear un nuevo proceso con una nueva tarea para la SOLICITUD CAMBIO EQUIPO POR SOPORTE
SET SERVEROUTPUT ON
DECLARE
  Ln_IdProceso NUMBER;
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
      'SOLICITUD CAMBIO EQUIPO POR SOPORTE',
      'SOLICITUD CAMBIO EQUIPO POR SOPORTE',
      'Activo',
      'jbozada',
      'jbozada',
      SYSDATE,
      SYSDATE,
      'NO'
    );
  SELECT ID_PROCESO
  INTO Ln_IdProceso
  FROM DB_SOPORTE.ADMI_PROCESO
  WHERE NOMBRE_PROCESO='SOLICITUD CAMBIO EQUIPO POR SOPORTE';
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
      'FIBRA: INSTALAR EQUIPO',
      'Proceso utilizado para los servicios que deseen realizar la instalación de un equipo.',
      'Activo',
      'jbozada',
      'jbozada',
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
      'jbozada',
      SYSDATE
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Registros insertados correctamente');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/


--Bloque anónimo para crear un registro en el parámetro FACTURACION_SOLICITUDES con el 
--nuevo tipo de solicitud a facturar SOLICITUD VISITA TECNICA POR INSTALACION
SET SERVEROUTPUT ON
DECLARE
  Ln_IdParamCoordElem NUMBER(5,0);
BEGIN

  SELECT APC.ID_PARAMETRO
        INTO Ln_IdParamCoordElem
        FROM  DB_GENERAL.ADMI_PARAMETRO_CAB APC
       WHERE APC.NOMBRE_PARAMETRO = 'FACTURACION_SOLICITUDES' 
         AND MODULO = 'FINANCIERO'
         AND APC.ESTADO = 'Activo';

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
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD,
    VALOR6
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamCoordElem,
    'Visita técnica',
    'SOLICITUD VISITA TECNICA POR INSTALACION',
    NULL,
    '1226',
    'Visita técnica por instalación de equipo',
    'Activo',
    'jbozada',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    'telcos_visitaTecnica',
    '18',
    'S'
  );

  SYS.DBMS_OUTPUT.PUT_LINE('Registros insertados correctamente');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;

/

SET SERVEROUTPUT ON
--Creación de parámetros para facturar visita técnica por instalación de equipos
DECLARE
  Ln_IdParamCoordElem NUMBER(5,0);
BEGIN
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_CAB
    (
      ID_PARAMETRO,
      NOMBRE_PARAMETRO,
      DESCRIPCION,
      MODULO,
      PROCESO,
      ESTADO,
      USR_CREACION,
      FE_CREACION,
      IP_CREACION
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
      'FACTURAR_VISITA_POR_INSTALACION',
      'DETERMINAR SI SE DEBE FACTURAR CUANDO SE REALICE UNA ACTIVACIÓN DE UN EQUIPO',
      'TECNICO',
      'INSTALACION_DE_EQUIPOS',
      'Activo',
      'jbozada',
      CURRENT_TIMESTAMP,
      '127.0.0.1'
    );
  SELECT ID_PARAMETRO
  INTO Ln_IdParamCoordElem
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='FACTURAR_VISITA_POR_INSTALACION';
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
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamCoordElem,
    'EXTENDER_DUAL_BAND',
    'SI',
    '0',
    NULL,
    NULL,
    'Activo',
    'jbozada',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
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
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamCoordElem,
    'WIFI_DUAL_BAND',
    'SI',
    '0',
    NULL,
    NULL,
    'Activo',
    'jbozada',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    '18'
  );

  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente los parámetros');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;

/

--=======================================================================
--   Se crea caracteristica "SINCRONIZAR EXTENDER DUAL BAND" utilizada en
--   el proceso de cambios de equipos del equipo extender dual band.
--=======================================================================

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
    'SINCRONIZAR EXTENDER DUAL BAND',
    'T',
    'Activo',
    SYSDATE,
    'jbozada',
    NULL,
    NULL,
    'TECNICA'
  );
--=======================================================================
--      Se asocia el producto INTERNET con la caracteristica de 
--      sincronizar de extender dual band.
--=======================================================================
INSERT
INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
  (
    ID_PRODUCTO_CARACTERISITICA,
    PRODUCTO_ID,
    CARACTERISTICA_ID,
    FE_CREACION,
    FE_ULT_MOD,
    USR_CREACION,
    USR_ULT_MOD,
    ESTADO,
    VISIBLE_COMERCIAL
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
    WHERE EMPRESA_COD='18' AND NOMBRE_TECNICO='INTERNET' AND ESTADO='Activo'),
    (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
    WHERE DESCRIPCION_CARACTERISTICA = 'SINCRONIZAR EXTENDER DUAL BAND'),
    SYSDATE,
    NULL,
    'jbozada',
    NULL,
    'Activo',
    'NO'
  );

commit;
/