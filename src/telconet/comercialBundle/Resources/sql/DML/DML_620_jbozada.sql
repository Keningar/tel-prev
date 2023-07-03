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
    'SOLICITUD TRASLADO',
    SYSDATE,
    'jbozada',
    NULL,
    NULL,
    'Activo',
    NULL,
    NULL,
    NULL
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
    'SOLICITUD REUBICACION',
    SYSDATE,
    'jbozada',
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
    'ID_PUNTO',
    'N',
    'Activo',
    SYSDATE,
    'jbozada',
    NULL,
    NULL,
    'COMERCIAL'
  );
COMMIT;
INSERT
INTO DB_COMERCIAL.ADMI_PRODUCTO
  (
    ID_PRODUCTO,
    EMPRESA_COD,
    CODIGO_PRODUCTO,
    DESCRIPCION_PRODUCTO,
    FUNCION_COSTO,
    INSTALACION,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    IP_CREACION,
    CTA_CONTABLE_PROD,
    CTA_CONTABLE_PROD_NC,
    ES_PREFERENCIA,
    ES_ENLACE,
    REQUIERE_PLANIFICACION,
    REQUIERE_INFO_TECNICA,
    NOMBRE_TECNICO,
    CTA_CONTABLE_DESC,
    TIPO,
    ES_CONCENTRADOR,
    SOPORTE_MASIVO,
    ESTADO_INICIAL,
    GRUPO,
    COMISION_VENTA,
    COMISION_MANTENIMIENTO,
    USR_GERENTE,
    CLASIFICACION,
    REQUIERE_COMISIONAR,
    SUBGRUPO,
    FUNCION_PRECIO
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO.NEXTVAL,
    '10',
    'TRASTN',
    'Traslado de Servicios',
    NULL,
    '0',
    'Inactivo',
    SYSDATE,
    'jbozada',
    '127.0.0.1',
    '0',
    '0',
    'NO',
    'NO',
    'NO',
    'NO',
    'OTROSSERVICIOS',
    NULL,
    'S',
    'NO',
    'N',
    NULL,
    'OTROS',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    'PRECIO=0'
  );
INSERT
INTO DB_COMERCIAL.ADMI_PRODUCTO
  (
    ID_PRODUCTO,
    EMPRESA_COD,
    CODIGO_PRODUCTO,
    DESCRIPCION_PRODUCTO,
    FUNCION_COSTO,
    INSTALACION,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    IP_CREACION,
    CTA_CONTABLE_PROD,
    CTA_CONTABLE_PROD_NC,
    ES_PREFERENCIA,
    ES_ENLACE,
    REQUIERE_PLANIFICACION,
    REQUIERE_INFO_TECNICA,
    NOMBRE_TECNICO,
    CTA_CONTABLE_DESC,
    TIPO,
    ES_CONCENTRADOR,
    SOPORTE_MASIVO,
    ESTADO_INICIAL,
    GRUPO,
    COMISION_VENTA,
    COMISION_MANTENIMIENTO,
    USR_GERENTE,
    CLASIFICACION,
    REQUIERE_COMISIONAR,
    SUBGRUPO,
    FUNCION_PRECIO
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO.NEXTVAL,
    '10',
    'REUTN',
    'Reubicación de Servicios',
    NULL,
    '0',
    'Inactivo',
    SYSDATE,
    'jbozada',
    '127.0.0.1',
    '0',
    '0',
    'NO',
    'NO',
    'NO',
    'NO',
    'OTROSSERVICIOS',
    NULL,
    'S',
    'NO',
    'N',
    NULL,
    'OTROS',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    'PRECIO=0'
  );
COMMIT;
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
    'NO_TRADICIONAL_FLUJO',
    'N',
    'Activo',
    SYSDATE,
    'jbozada',
    NULL,
    NULL,
    'TECNICA'
  );
COMMIT;
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
    1120,
    (SELECT ID_CARACTERISTICA
    FROM DB_COMERCIAL.ADMI_CARACTERISTICA
    WHERE DESCRIPCION_CARACTERISTICA='NO_TRADICIONAL_FLUJO'
    AND ESTADO                      ='Activo'
    ),
    SYSDATE,
    NULL,
    'jbozada',
    NULL,
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
    FE_ULT_MOD,
    USR_CREACION,
    USR_ULT_MOD,
    ESTADO,
    VISIBLE_COMERCIAL
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    1125,
    (SELECT ID_CARACTERISTICA
    FROM DB_COMERCIAL.ADMI_CARACTERISTICA
    WHERE DESCRIPCION_CARACTERISTICA='NO_TRADICIONAL_FLUJO'
    AND ESTADO                      ='Activo'
    ),
    SYSDATE,
    NULL,
    'jbozada',
    NULL,
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
    FE_ULT_MOD,
    USR_CREACION,
    USR_ULT_MOD,
    ESTADO,
    VISIBLE_COMERCIAL
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    694,
    (SELECT ID_CARACTERISTICA
    FROM DB_COMERCIAL.ADMI_CARACTERISTICA
    WHERE DESCRIPCION_CARACTERISTICA='NO_TRADICIONAL_FLUJO'
    AND ESTADO                      ='Activo'
    ),
    SYSDATE,
    NULL,
    'jbozada',
    NULL,
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
    FE_ULT_MOD,
    USR_CREACION,
    USR_ULT_MOD,
    ESTADO,
    VISIBLE_COMERCIAL
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    678,
    (SELECT ID_CARACTERISTICA
    FROM DB_COMERCIAL.ADMI_CARACTERISTICA
    WHERE DESCRIPCION_CARACTERISTICA='NO_TRADICIONAL_FLUJO'
    AND ESTADO                      ='Activo'
    ),
    SYSDATE,
    NULL,
    'jbozada',
    NULL,
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
    FE_ULT_MOD,
    USR_CREACION,
    USR_ULT_MOD,
    ESTADO,
    VISIBLE_COMERCIAL
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    679,
    (SELECT ID_CARACTERISTICA
    FROM DB_COMERCIAL.ADMI_CARACTERISTICA
    WHERE DESCRIPCION_CARACTERISTICA='NO_TRADICIONAL_FLUJO'
    AND ESTADO                      ='Activo'
    ),
    SYSDATE,
    NULL,
    'jbozada',
    NULL,
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
    FE_ULT_MOD,
    USR_CREACION,
    USR_ULT_MOD,
    ESTADO,
    VISIBLE_COMERCIAL
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    303,
    (SELECT ID_CARACTERISTICA
    FROM DB_COMERCIAL.ADMI_CARACTERISTICA
    WHERE DESCRIPCION_CARACTERISTICA='NO_TRADICIONAL_FLUJO'
    AND ESTADO                      ='Activo'
    ),
    SYSDATE,
    NULL,
    'jbozada',
    NULL,
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
    FE_ULT_MOD,
    USR_CREACION,
    USR_ULT_MOD,
    ESTADO,
    VISIBLE_COMERCIAL
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    374,
    (SELECT ID_CARACTERISTICA
    FROM DB_COMERCIAL.ADMI_CARACTERISTICA
    WHERE DESCRIPCION_CARACTERISTICA='NO_TRADICIONAL_FLUJO'
    AND ESTADO                      ='Activo'
    ),
    SYSDATE,
    NULL,
    'jbozada',
    NULL,
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
    FE_ULT_MOD,
    USR_CREACION,
    USR_ULT_MOD,
    ESTADO,
    VISIBLE_COMERCIAL
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    246,
    (SELECT ID_CARACTERISTICA
    FROM DB_COMERCIAL.ADMI_CARACTERISTICA
    WHERE DESCRIPCION_CARACTERISTICA='NO_TRADICIONAL_FLUJO'
    AND ESTADO                      ='Activo'
    ),
    SYSDATE,
    NULL,
    'jbozada',
    NULL,
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
    FE_ULT_MOD,
    USR_CREACION,
    USR_ULT_MOD,
    ESTADO,
    VISIBLE_COMERCIAL
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    270,
    (SELECT ID_CARACTERISTICA
    FROM DB_COMERCIAL.ADMI_CARACTERISTICA
    WHERE DESCRIPCION_CARACTERISTICA='NO_TRADICIONAL_FLUJO'
    AND ESTADO                      ='Activo'
    ),
    SYSDATE,
    NULL,
    'jbozada',
    NULL,
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
    FE_ULT_MOD,
    USR_CREACION,
    USR_ULT_MOD,
    ESTADO,
    VISIBLE_COMERCIAL
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    274,
    (SELECT ID_CARACTERISTICA
    FROM DB_COMERCIAL.ADMI_CARACTERISTICA
    WHERE DESCRIPCION_CARACTERISTICA='NO_TRADICIONAL_FLUJO'
    AND ESTADO                      ='Activo'
    ),
    SYSDATE,
    NULL,
    'jbozada',
    NULL,
    'Activo',
    'NO'
  );
COMMIT;
DECLARE
BEGIN
  FOR PRODUCTO IN
  ( SELECT * FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE EMPRESA_COD='10'
  )
  LOOP
    DBMS_OUTPUT.PUT_LINE
    (
      PRODUCTO.ID_PRODUCTO
    )
    ;
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
        PRODUCTO.ID_PRODUCTO,
        (SELECT ID_CARACTERISTICA
        FROM DB_COMERCIAL.ADMI_CARACTERISTICA
        WHERE DESCRIPCION_CARACTERISTICA='TRASLADO'
        AND ESTADO                      ='Activo'
        ),
        SYSDATE,
        NULL,
        'jbozada',
        NULL,
        'Activo',
        'NO'
      );
  END LOOP;
  COMMIT;
END;
/
INSERT
INTO DB_COMERCIAL.INFO_PRODUCTO_IMPUESTO
  (
    ID_PRODUCTO_IMPUESTO,
    PRODUCTO_ID,
    IMPUESTO_ID,
    PORCENTAJE_IMPUESTO,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    ESTADO
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_INFO_PRODUCTO_IMPUESTO.NEXTVAL,
    (SELECT ID_PRODUCTO
    FROM DB_COMERCIAL.ADMI_PRODUCTO
    WHERE CODIGO_PRODUCTO='REUTN'
    ),
    '1',
    '12',
    SYSDATE,
    'jbozada',
    SYSDATE,
    'jbozada',
    'Activo'
  );
INSERT
INTO DB_COMERCIAL.INFO_PRODUCTO_IMPUESTO
  (
    ID_PRODUCTO_IMPUESTO,
    PRODUCTO_ID,
    IMPUESTO_ID,
    PORCENTAJE_IMPUESTO,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    ESTADO
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_INFO_PRODUCTO_IMPUESTO.NEXTVAL,
    (SELECT ID_PRODUCTO
    FROM DB_COMERCIAL.ADMI_PRODUCTO
    WHERE CODIGO_PRODUCTO='TRASTN'
    ),
    '1',
    '12',
    SYSDATE,
    'jbozada',
    SYSDATE,
    'jbozada',
    'Activo'
  );
COMMIT;

--Bloque anónimo para crear un nuevo proceso con una nueva tarea para la última milla FTTx
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
      'SOLICITUD REUBICACION',
      'SOLICITUD REUBICACION',
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
  WHERE NOMBRE_PROCESO='SOLICITUD REUBICACION';
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
      'REUBICACION: Reubicar servicios solicitados.',
      'Tarea que ejecuta la reubicación de los servicios solicitados por el cliente.',
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
      '10',
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
DECLARE
  Ln_Aplicacion_NCam_Id NUMBER;
BEGIN
  --Crear Aplicacion 
  INSERT
  INTO DB_TOKENSECURITY.APPLICATION VALUES
    (
      DB_TOKENSECURITY.SEQ_APPLICATION.NEXTVAL,
      'APP.AUTOMATICA',
      'ACTIVO',
      30
    );

  -- Obtener id de la aplicacion 
  SELECT id_application
  INTO Ln_Aplicacion_NCam_Id
  FROM DB_TOKENSECURITY.APPLICATION
  WHERE name = 'APP.AUTOMATICA';

  --Configurar clase Grupo_corteCancelacionTiempo y relacionarlo con el APP.AUTOMATICA
  INSERT
  INTO DB_TOKENSECURITY.WEB_SERVICE VALUES
    (
      DB_TOKENSECURITY.SEQ_WEB_SERVICE.nextval,
      'Grupo_corteCancelacionTiempo',
      'generarToken',
      1,
      'ACTIVO',
      Ln_Aplicacion_NCam_Id
    );

  --Configurar Usuario/Clave AUTOMATICA/NETLIFECAM(sha256)
  INSERT
  INTO DB_TOKENSECURITY.USER_TOKEN VALUES
    (
      DB_TOKENSECURITY.SEQ_USER_TOKEN.nextval,
      'AUTOMATICA',
      'C757F1C355215D450C74EEA20806185DE5A40A06875E73A06E0EB70F56864FA0',
      'Activo',
      Ln_Aplicacion_NCam_Id
    );
  COMMIT;
  SYS.DBMS_OUTPUT.PUT_LINE('Registros insertados Correctamente');
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '||SQLERRM);
  ROLLBACK;
END;
/