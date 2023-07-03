--Bloque anónimo para crear un nuevo proceso para registro de traslados md
SET SERVEROUTPUT ON
DECLARE
  Ln_IdProceso NUMBER(5,0);
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
      'PROCESOS TAREAS TRASLADO',
      'PROCESOS TAREAS TRASLADO',
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
  WHERE NOMBRE_PROCESO='PROCESOS TAREAS TRASLADO';
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
      'Asignado OT por traslado',
      'Tarea para registrar que se generó el proceso .',
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
  SYS.DBMS_OUTPUT.PUT_LINE('Registros de proceso y tarea ingresados correctamente');
  --INSERTA CARACTERÍSTICA DE ID TAREA TRASLADO
  INSERT
  INTO ADMI_CARACTERISTICA
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
      'ID_TAREA_TRASLADO',
      'T',
      'Activo',
      sysdate,
      'jbozada',
      NULL,
      NULL,
      'COMERCIAL'
    );
  --INSERTA CARACTERÍSTICA DE DIFERENTE TECNOLOGÍA EN FACTIBILIDAD
  INSERT
  INTO ADMI_CARACTERISTICA
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
      'DIFERENTE TECNOLOGIA FACTIBILIDAD',
      'T',
      'Activo',
      sysdate,
      'jbozada',
      NULL,
      NULL,
      'COMERCIAL'
    );
  INSERT
  INTO ADMI_CARACTERISTICA
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
      'ELEMENTO',
      'T',
      'Activo',
      sysdate,
      'jbozada',
      NULL,
      NULL,
      'TECNICO'
    );
  INSERT
  INTO ADMI_CARACTERISTICA
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
      'OBSERVACION',
      'T',
      'Activo',
      sysdate,
      'jbozada',
      NULL,
      NULL,
      'TECNICO'
    );
  INSERT
  INTO ADMI_CARACTERISTICA
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
      'ESTADO',
      'T',
      'Activo',
      sysdate,
      'jbozada',
      NULL,
      NULL,
      'TECNICO'
    );
  INSERT
  INTO ADMI_CARACTERISTICA
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
      'ELEMENTO RETIRO',
      'T',
      'Activo',
      sysdate,
      'jbozada',
      NULL,
      NULL,
      'TECNICO'
    );
  INSERT
  INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
      ID_PRODUCTO_CARACTERISITICA,
      PRODUCTO_ID,
      CARACTERISTICA_ID,
      FE_CREACION,
      USR_CREACION,
      ESTADO,
      VISIBLE_COMERCIAL
    )
    VALUES
    (
      DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
      (SELECT ID_PRODUCTO
      FROM db_comercial.admi_producto
      WHERE descripcion_producto='INTERNET DEDICADO'
      AND EMPRESA_COD           ='18'
      AND ESTADO                ='Activo'
      ),
      (SELECT id_caracteristica
      FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'ELEMENTO RETIRO'
      ),
      CURRENT_TIMESTAMP,
      'jbozada',
      'Activo',
      'NO'
    );
  INSERT
  INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
      ID_PRODUCTO_CARACTERISITICA,
      PRODUCTO_ID,
      CARACTERISTICA_ID,
      FE_CREACION,
      USR_CREACION,
      ESTADO,
      VISIBLE_COMERCIAL
    )
    VALUES
    (
      DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
      (SELECT ID_PRODUCTO
      FROM db_comercial.admi_producto
      WHERE descripcion_producto='INTERNET DEDICADO'
      AND EMPRESA_COD           ='18'
      AND ESTADO                ='Activo'
      ),
      (SELECT id_caracteristica
      FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'DIFERENTE TECNOLOGIA FACTIBILIDAD'
      ),
      CURRENT_TIMESTAMP,
      'jbozada',
      'Activo',
      'NO'
    );
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
      'SOLICITUD REGISTRO ELEMENTOS',
      sysdate,
      'jbozada',
      NULL,
      NULL,
      'Activo',
      NULL,
      NULL,
      NULL
    );
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
      IP_CREACION,
      USR_ULT_MOD,
      FE_ULT_MOD,
      IP_ULT_MOD
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
      'PROCESO_TRASLADO_MD',
      'PARAMETRO PADRE PARA VALORES USADOS EN EL PROYECTO DE TRASLADO.',
      'COMERCIAL',
      'TRASLADO',
      'Activo',
      'jbozada',
      sysdate,
      '127.0.0.1',
      NULL,
      NULL,
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
      ESTADO,
      USR_CREACION,
      FE_CREACION,
      IP_CREACION,
      USR_ULT_MOD,
      FE_ULT_MOD,
      IP_ULT_MOD,
      VALOR5,
      EMPRESA_COD,
      VALOR6,
      VALOR7,
      OBSERVACION
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      (SELECT Id_Parametro
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE Nombre_Parametro='PROCESO_TRASLADO_MD'
      AND estado            ='Activo'
      ),
      'TAREA_AUTOMATICA_TRASLADO',
      '464539',--id persona rol
      'Asignado OT por traslado',
      'PROCESOS TAREAS TRASLADO',
      '',
      'Activo',
      'jbozada',
      sysdate,
      '127.0.0.1',
      NULL,
      NULL,
      NULL,
      'VERONICA CESIBEL VARELA VERA',--USUARIO ASIGNACION
      '18',
      'empleado',
      NULL,
      NULL
    );
  -- Parametros nuevos creados para el proyecto
  -- CAB - Insert para parametrizacion de cabecera adicionales manuales
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
      'PRODUCTOS ADICIONALES MANUALES DIFERENTE TECNOLOGIA',
      'PRODUCTOS Y ESTADOS USADOS PARA LAS ACCIONES DE SERVICIOS ADICIONALES MANUALES DIFERENTE TECNOLOGIA',
      'COMERCIAL',
      'Activo',
      'jbozada',
      SYSDATE,
      '127.0.0.1'
    );
  -- DET 13A Insert para lista de los productos manuales que deben cambiar su estado en el origen
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
      VALOR5,
      EMPRESA_COD,
      VALOR6,
      VALOR7
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      (SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PRODUCTOS ADICIONALES MANUALES DIFERENTE TECNOLOGIA'
      ),
      'Productos manuales que cambian estado en origen',
      '1232',
      '78',
      '1357',
      NULL,
      'Activo',
      'jbozada',
      sysdate,
      '127.0.0.1',
      NULL,
      18,
      NULL,
      NULL
    );
  -- DET 5A Insert para los estados en origen que no cambiaran despues del traslado
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
      VALOR5,
      EMPRESA_COD,
      VALOR6,
      VALOR7
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      (SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PRODUCTOS ADICIONALES MANUALES DIFERENTE TECNOLOGIA'
      ),
      'Estados en origen que no cambiaran a trasladado',
      'Anulado',
      'Rechazada',
      'Eliminado',
      'Cancel',
      'Activo',
      'jbozada',
      sysdate,
      '127.0.0.1',
      'Cancelado',
      18,
      NULL,
      NULL
    );
  -- DET 6A Insert para los tipos de solicitud validos que se cancelaran con el traslado
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
      VALOR5,
      EMPRESA_COD,
      VALOR6,
      VALOR7
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      (SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PRODUCTOS ADICIONALES MANUALES DIFERENTE TECNOLOGIA'
      ),
      'Solicitudes anexas a los servicios adicionales manuales',
      8,175,131,
      NULL,
      'Activo',
      'jbozada',
      sysdate,
      '127.0.0.1',
      NULL,
      18,
      NULL,
      NULL
    );
  -- DET 14A Insert para listar los estados de los servicios que no cerraron tareas en el origen
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
      VALOR5,
      EMPRESA_COD,
      VALOR6,
      VALOR7
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      (SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PRODUCTOS ADICIONALES MANUALES DIFERENTE TECNOLOGIA'
      ),
      'Estado de solicitudes que no cancelaron tareas en traslado',
      'Detenido',
      NULL,
      NULL,
      NULL,
      'Activo',
      'jbozada',
      sysdate,
      '127.0.0.1',
      NULL,
      18,
      NULL,
      NULL
    );

UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
SET VALOR7          = 'EQUIPOS DEFAULT DIFERENTE TECNOLOGIA'
WHERE PARAMETRO_ID IN
  (SELECT ID_PARAMETRO
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE ID_PARAMETRO = 847
  )
AND DESCRIPCION IN ('ROSETA' , 'FUENTE DE PODER', 'FUENTE DE PODER AP CISCO')
AND VALOR1      IN ('ZTE', 'HUAWEI', 'TELLION', 'ADSL', 'CISCO')
AND EMPRESA_COD  = '18';

  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
