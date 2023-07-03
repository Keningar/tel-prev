--Creación de parámetros para servicios de TN
DECLARE
  Ln_IdParamsPlanificacionTipos    NUMBER;
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
      'PLANIFICACION_TIPOS',
      'Parámetros para los distintos tipos de planificación',
      'Activo',
      'wgaibor',
      CURRENT_TIMESTAMP,
      '127.0.0.1'
    );
  SELECT ID_PARAMETRO
  INTO Ln_IdParamsPlanificacionTipos
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='PLANIFICACION_TIPOS';
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
    Ln_IdParamsPlanificacionTipos,
    'Estados de los tipos de solicitudes de planificación',
    'SOLICITUD PLANIFICACION',
    'SOLICITUD PLANIFICACION',
    NULL,
    NULL,
    NULL,
    'Activo',
    'wgaibor',
    SYSDATE,
    '127.0.0.1',
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente solicitud SOLICITUD PLANIFICACION');

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
    Ln_IdParamsPlanificacionTipos,
    'Estados de los tipos de solicitudes de planificación',
    'SOLICITUD RETIRO EQUIPO',
    'SOLICITUD RETIRO EQUIPO',
    NULL,
    NULL,
    NULL,
    'Activo',
    'wgaibor',
    SYSDATE,
    '127.0.0.1',
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente solicitud SOLICITUD RETIRO EQUIPO');

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
    Ln_IdParamsPlanificacionTipos,
    'Estados de los tipos de solicitudes de planificación',
    'SOLICITUD CAMBIO EQUIPO',
    'SOLICITUD CAMBIO EQUIPO',
    NULL,
    NULL,
    NULL,
    'Activo',
    'wgaibor',
    SYSDATE,
    '127.0.0.1',
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente solicitud SOLICITUD CAMBIO EQUIPO');
  
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
    Ln_IdParamsPlanificacionTipos,
    'Estados de los tipos de solicitudes de planificación',
    'SOLICITUD MIGRACION',
    'SOLICITUD MIGRACION',
    NULL,
    NULL,
    NULL,
    'Activo',
    'wgaibor',
    SYSDATE,
    '127.0.0.1',
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente solicitud SOLICITUD MIGRACION');
  
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
    Ln_IdParamsPlanificacionTipos,
    'Estados de los tipos de solicitudes de planificación',
    'SOLICITUD AGREGAR EQUIPO',
    'SOLICITUD AGREGAR EQUIPO',
    NULL,
    NULL,
    NULL,
    'Activo',
    'wgaibor',
    SYSDATE,
    '127.0.0.1',
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente solicitud SOLICITUD AGREGAR EQUIPO MASIVO');
  
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
    Ln_IdParamsPlanificacionTipos,
    'Estados de los tipos de solicitudes de planificación',
    'SOLICITUD REUBICACION',
    'SOLICITUD REUBICACION',
    NULL,
    NULL,
    NULL,
    'Activo',
    'wgaibor',
    SYSDATE,
    '127.0.0.1',
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente solicitud SOLICITUD REUBICACION');
  
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
    Ln_IdParamsPlanificacionTipos,
    'Estados de los tipos de solicitudes de planificación',
    'SOLICITUD CAMBIO EQUIPO POR SOPORTE',
    'SOLICITUD CAMBIO EQUIPO POR SOPORTE',
    NULL,
    NULL,
    NULL,
    'Activo',
    'wgaibor',
    SYSDATE,
    '127.0.0.1',
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente solicitud SOLICITUD CAMBIO EQUIPO POR SOPORTE');
  
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
    Ln_IdParamsPlanificacionTipos,
    'Estados de los tipos de solicitudes de planificación',
    'SOLICITUD CAMBIO EQUIPO POR SOPORTE MASIVO',
    'SOLICITUD CAMBIO EQUIPO POR SOPORTE MASIVO',
    NULL,
    NULL,
    NULL,
    'Activo',
    'wgaibor',
    SYSDATE,
    '127.0.0.1',
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente solicitud SOLICITUD CAMBIO EQUIPO POR SOPORTE MASIVO');
  
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
    Ln_IdParamsPlanificacionTipos,
    'Estados de los tipos de solicitudes de planificación',
    'SOLICITUD DE INSTALACION CABLEADO ETHERNET',
    'SOLICITUD DE INSTALACION CABLEADO ETHERNET',
    NULL,
    NULL,
    NULL,
    'Activo',
    'wgaibor',
    SYSDATE,
    '127.0.0.1',
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente solicitud SOLICITUD DE INSTALACION CABLEADO ETHERNET');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
/
--Creación de parámetros para servicios de TN
DECLARE
  Ln_IdParamsPlanificacionTipos    NUMBER;
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
      'PLANIFICACION_ESTADOS',
      'Parámetros para los distintos estados de planificación',
      'Activo',
      'wgaibor',
      CURRENT_TIMESTAMP,
      '127.0.0.1'
    );
  SELECT ID_PARAMETRO
  INTO Ln_IdParamsPlanificacionTipos
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='PLANIFICACION_ESTADOS';
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
    Ln_IdParamsPlanificacionTipos,
    'Estados de planificación',
    'Todos',
    'Todos',
    NULL,
    NULL,
    NULL,
    'Activo',
    'wgaibor',
    SYSDATE,
    '127.0.0.1',
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente estado Todos');

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
    Ln_IdParamsPlanificacionTipos,
    'Estados de planificación',
    'PrePlanificada',
    'PrePlanificada',
    NULL,
    NULL,
    NULL,
    'Activo',
    'wgaibor',
    SYSDATE,
    '127.0.0.1',
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente estado PrePlanificada');

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
    Ln_IdParamsPlanificacionTipos,
    'Estados de planificación',
    'Planificada',
    'Planificada',
    NULL,
    NULL,
    NULL,
    'Activo',
    'wgaibor',
    SYSDATE,
    '127.0.0.1',
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente estado Planificada');
  
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
    Ln_IdParamsPlanificacionTipos,
    'Estados de planificación',
    'Replanificada',
    'Replanificada',
    NULL,
    NULL,
    NULL,
    'Activo',
    'wgaibor',
    SYSDATE,
    '127.0.0.1',
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente estado Replanificada');
  
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
    Ln_IdParamsPlanificacionTipos,
    'Estados de planificación',
    'Rechazada',
    'Rechazada',
    NULL,
    NULL,
    NULL,
    'Activo',
    'wgaibor',
    SYSDATE,
    '127.0.0.1',
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente estado Rechazada');
  
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
    Ln_IdParamsPlanificacionTipos,
    'Estados de planificación',
    'Detenido',
    'Detenido',
    NULL,
    NULL,
    NULL,
    'Activo',
    'wgaibor',
    SYSDATE,
    '127.0.0.1',
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente estado Detenido');
  
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
    Ln_IdParamsPlanificacionTipos,
    'Estados de planificación',
    'AsignadoTarea',
    'AsignadoTarea',
    NULL,
    NULL,
    NULL,
    'Activo',
    'wgaibor',
    SYSDATE,
    '127.0.0.1',
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente estado AsignadoTarea');
  
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
    Ln_IdParamsPlanificacionTipos,
    'Estados de planificación',
    'Asignada',
    'Asignada',
    NULL,
    NULL,
    NULL,
    'Activo',
    'wgaibor',
    SYSDATE,
    '127.0.0.1',
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente estado Asignada');
  
  --
  INSERT INTO db_general.admi_parametro_cab (
    id_parametro,
    nombre_parametro,
    descripcion,
    modulo,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion
  ) VALUES (
      db_general.seq_admi_parametro_cab.nextval,
      'CANT_DIA_MAX_PLANIFICACION',
      'CANTIDAD DE DÍA MAXIMO PARA PLANIFICAR',
      'COMERCIAL',
      'Activo',
      'wgaibor',
      sysdate,
      '127.0.0.1'
  );

--
--CANT_DIA_MAX_PLANIFICACION
--
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    valor4,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    valor5,
    valor6,
    valor7
  ) VALUES (
      db_general.seq_admi_parametro_det.nextval,
      (
          SELECT
              id_parametro
          FROM
              db_general.admi_parametro_cab
          WHERE
              nombre_parametro = 'CANT_DIA_MAX_PLANIFICACION'
              AND estado = 'Activo'
      ),
      'CANTIDAD DE DÍA MAXIMO PARA PLANIFICAR',
      '6',
      NULL,
      NULL,
      NULL,
      'Activo',
      'wgaibor',
      sysdate,
      '127.0.0.1',
      NULL,
      NULL,
      NULL
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente la CANTIDAD DE DÍA MAXIMO PARA PLANIFICAR');

--
  INSERT INTO db_general.admi_parametro_cab (
    id_parametro,
    nombre_parametro,
    descripcion,
    modulo,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion
  ) VALUES (
      db_general.seq_admi_parametro_cab.nextval,
      'TIEMPO_BANDEJA_PLAN_AUTOMATICA',
      'TIEMPO MÁXIMO A MOSTRAR EN LA BANDEJA DE PLANIFICACIÓN AUTOMÁTICA',
      'COMERCIAL',
      'Activo',
      'wgaibor',
      sysdate,
      '127.0.0.1'
  );

--
--CANT_DIA_MAX_PLANIFICACION
--
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    valor4,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    valor5,
    EMPRESA_COD,
    valor6,
    valor7
  ) VALUES (
      db_general.seq_admi_parametro_det.nextval,
      (
          SELECT
              id_parametro
          FROM
              db_general.admi_parametro_cab
          WHERE
              nombre_parametro = 'TIEMPO_BANDEJA_PLAN_AUTOMATICA'
              AND estado = 'Activo'
      ),
      'TIEMPO MÁXIMO A MOSTRAR EN LA BANDEJA DE PLANIFICACIÓN AUTOMÁTICA',
      '30',
      'SOLICITUD PLANIFICACION',
      'PrePlanificada',
      NULL,
      'Activo',
      'wgaibor',
      sysdate,
      '127.0.0.1',
      NULL,
      '18',
      NULL,
      NULL
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente el TIEMPO MÁXIMO A MOSTRAR EN LA BANDEJA DE PLANIFICACIÓN AUTOMÁTICA');

--
  INSERT INTO db_general.admi_parametro_cab (
    id_parametro,
    nombre_parametro,
    descripcion,
    modulo,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion
  ) VALUES (
      db_general.seq_admi_parametro_cab.nextval,
      'PROGRAMAR_MOTIVO_HAL',
      'ID MOTIVOS HAL PROGRAMAR',
      'COMERCIAL',
      'Activo',
      'wgaibor',
      sysdate,
      '127.0.0.1'
  );

--
-- PROGRAMAR_MOTIVO_HAL
--
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    valor4,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    valor5,
    EMPRESA_COD,
    valor6,
    valor7
  ) VALUES (
      db_general.seq_admi_parametro_det.nextval,
      (
          SELECT
              id_parametro
          FROM
              db_general.admi_parametro_cab
          WHERE
              nombre_parametro = 'PROGRAMAR_MOTIVO_HAL'
              AND estado = 'Activo'
      ),
      'ID MOTIVOS HAL PROGRAMAR',
      '2577,2576',
      NULL,
      NULL,
      NULL,
      'Activo',
      'wgaibor',
      sysdate,
      '127.0.0.1',
      NULL,
      '18',
      NULL,
      NULL
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente ID MOTIVOS HAL PROGRAMAR');

  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB (ID_PARAMETRO, NOMBRE_PARAMETRO, DESCRIPCION, MODULO, PROCESO, ESTADO, USR_CREACION, FE_CREACION, IP_CREACION)
  VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL, 'PRODUCTOS QUE NO SE PLANIFICAN', 'DETERMINA LOS PRODUCTOS QUE NO PERMITEN QUE SE HAGA LA PLANIFICACION COMERCIAL', 'COMERCIAL', 'PLANIFICACION_COMERCIAL', 'Activo', 'epin', sysdate, '127.0.0.1' );

  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR3, VALOR4, ESTADO, USR_CREACION, FE_CREACION, IP_CREACION)
  VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, (SELECT ID_PARAMETRO
                                                      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
                                                      WHERE NOMBRE_PARAMETRO = 'PRODUCTOS QUE NO SE PLANIFICAN'
                                                       AND ESTADO = 'Activo'), 'PRODUCTOS QUE NO SE PLANIFICAN', '1332,78', 
                                                       'La solicitud no aplica para Planificación comercial. Se envía la solicitud a PYL.',
                                                       'Se envía la solicitud a Planificación comercial','Activo', 'epin', sysdate, '127.0.0.1'); 
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente PRODUCTOS QUE NO SE PLANIFICAN');

  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;