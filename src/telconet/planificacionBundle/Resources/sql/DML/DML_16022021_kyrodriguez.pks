  /*
   *************************************************
   *                   PARÁMETRO                   *
   *                Precio de Fibra                *
   *************************************************
  */
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
     'Precio de fibra',
     'Precio de fibra',
     'SOPORTE',
     'Activo',
     'kyrodriguez',
      SYSDATE,
     '127.0.0.1'
    );
    -- FIN PARAMETRO CAB

  --INSERTAMOS LA DETALLE.
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
    (
      ID_PARAMETRO_DET,
      PARAMETRO_ID,
      DESCRIPCION,
      VALOR1,
      VALOR2,
      ESTADO,
      USR_CREACION,
      FE_CREACION,
      IP_CREACION,
      EMPRESA_COD,
      OBSERVACION
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
     (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'Precio de fibra'
      ),
     'Precio de fibra',
     '2',
     null,
     'Activo',
     'kyrodriguez',
     SYSDATE,
     '10.10.10.10',
     '10',
     'VALOR1: descripcion de parámetro, VALOR2: Precio de fibra'
    );

COMMIT;

  /*
   **************************************************
   *                    PARÁMETRO                   *
   * Vertical para proyecto excedente de material   *
   **************************************************
  */
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
     'Vertical para proyecto excedente de material',
     'Vertical para proyecto excedente de material',
     'PLANIFICACIÓN',
     'Activo',
     'kyrodriguez',
      SYSDATE,
     '127.0.0.1'
    );
    -- FIN PARAMETRO CAB

  --INSERTAMOS LA DETALLE.
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
    (
      ID_PARAMETRO_DET,
      PARAMETRO_ID,
      DESCRIPCION,
      VALOR1,
      VALOR2,
      ESTADO,
      USR_CREACION,
      FE_CREACION,
      IP_CREACION,
      EMPRESA_COD,
      OBSERVACION
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
     (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'Vertical para proyecto excedente de material'
      ),
     '',
     'CONNECTIVITY',
     'INTERNET Y DATOS',
     'Activo',
     'kyrodriguez',
     SYSDATE,
     '10.10.10.10',
     '10',
     'VALOR1: Vertical, VALOR2: Grupo'
    );

--INSERTAMOS LA DETALLE.
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
    (
      ID_PARAMETRO_DET,
      PARAMETRO_ID,
      DESCRIPCION,
      VALOR1,
      VALOR2,
      ESTADO,
      USR_CREACION,
      FE_CREACION,
      IP_CREACION,
      EMPRESA_COD,
      OBSERVACION
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
     (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'Vertical para proyecto excedente de material'
      ),
     '',
     'CONNECTIVITY',
     'WIFI',
     'Activo',
     'kyrodriguez',
     SYSDATE,
     '10.10.10.10',
     '10',
     'VALOR1: Vertical, VALOR2: Grupo'
    );

--INSERTAMOS LA DETALLE.
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
    (
      ID_PARAMETRO_DET,
      PARAMETRO_ID,
      DESCRIPCION,
      VALOR1,
      VALOR2,
      ESTADO,
      USR_CREACION,
      FE_CREACION,
      IP_CREACION,
      EMPRESA_COD,
      OBSERVACION
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
     (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'Vertical para proyecto excedente de material'
      ),
     '',
     'CONNECTIVITY',
     'BUSINESS SOLUTIONS',
     'Activo',
     'kyrodriguez',
     SYSDATE,
     '10.10.10.10',
     '10',
     'VALOR1: Vertical, VALOR2: Grupo'
    );

COMMIT;

    /*
   *********************************************
   *                 PARÁMETRO                 *
   *   Validaciones para excedente de material *
   *********************************************
  */
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
     'Validaciones para excedente de material',
     'Validaciones para excedente de material',
     'PLANIFICACIÓN',
     'Activo',
     'kyrodriguez',
      SYSDATE,
     '127.0.0.1'
    );
    -- FIN PARAMETRO CAB

  --INSERTAMOS LA DETALLE.
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
    (
      ID_PARAMETRO_DET,
      PARAMETRO_ID,
      DESCRIPCION,
      VALOR1,
      VALOR2,
      VALOR3,
      ESTADO,
      USR_CREACION,
      FE_CREACION,
      IP_CREACION,
      EMPRESA_COD,
      OBSERVACION
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
     (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'Validaciones para excedente de material'
      ),
     'VALIDACION1',
     'MRC',
     '2.5',
     null,
     'Activo',
     'kyrodriguez',
     SYSDATE,
     '10.10.10.10',
     '10',
     'VALOR1: Nombre de validacion, VALOR2: Nombre de constante, VALOR3: Valor del MRC'
    );

--INSERTAMOS LA DETALLE.
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
      OBSERVACION
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
     (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'Validaciones para excedente de material'
      ),
     'VALIDACION2',
     'MRC POR RUC',
     '5000',
     'SUPERA',
     '5',
     null,
     'Activo',
     'kyrodriguez',
     SYSDATE,
     '10.10.10.10',
     '10',
     'VALOR1: Nombre de validacion, VALOR2: Nombre de constante, VALOR3: Valor tope, VALOR4: condición, VALOR5: Valor a superar,'
    );
    
    
COMMIT;

  /*
   **********************************************
   *                  PARÁMETRO                 *
   *   Cargo que autoriza excedente de material *
   **********************************************
  */
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
     'Cargo que autoriza excedente de material',
     'Cargo que autoriza excedente de material',
     'PLANIFICACIÓN',
     'Activo',
     'kyrodriguez',
      SYSDATE,
     '127.0.0.1'
    );
    -- FIN PARAMETRO CAB

  --INSERTAMOS LA DETALLE.
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
    (
      ID_PARAMETRO_DET,
      PARAMETRO_ID,
      DESCRIPCION,
      VALOR1,
      VALOR2,
      VALOR3,
      ESTADO,
      USR_CREACION,
      FE_CREACION,
      IP_CREACION,
      EMPRESA_COD,
      OBSERVACION
    )
    VALUES
    (
     DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
     (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'Cargo que autoriza excedente de material'
     ),
     'Cargo que recibirá solicitud de excedente de material',
     'Gerente Tecnico Nacional',
     'GERENCIA TECNICA NACIONAL',
     NULL,
     'Activo',
     'kyrodriguez',
     SYSDATE,
     '10.10.10.10',
     '10',
     'VALOR1: Nombre de constante, VALOR3: Nombre del cargo'
    );
/*************************/
/***************************/

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
     'TAREA EXCESO DE MATERIAL',
     NULL,
     'SOPORTE',
     'Activo',
     'kyrodriguez',
      SYSDATE,
     '127.0.0.1'
    );

INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
     WHERE NOMBRE_PARAMETRO = 'TAREA EXCESO DE MATERIAL'),
    'TAREA A FACTURACIÓN',
    'TAREAS SISTEMAS - FINANCIERO',
    'FACTURACION: FACTURAS',
    'Contabilidad Facturacion',
    'Jefe Facturación Nacional',
    'Activo',
    'kyrodriguez',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    10,NULL,NULL,NULL
  );

COMMIT;

/*
  ******************************************************
  *                   PARÁMETRO                        *
  *    Precio de Fibra, Obra civil, otros materiales   *
  ******************************************************
*/


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
    'METRAJE FACTIBILIDAD PRECIO',
    'T',
    'Activo',
    SYSDATE,
    'kyrodriguez',
    NULL,
    NULL,
    'TECNICA'
  );
-- Precio Obra Civil
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
    'OBRA CIVIL PRECIO',
    'T',
    'Activo',
    SYSDATE,
    'kyrodriguez',
    NULL,
    NULL,
    'TECNICA'
  );
--Precio otros materiales
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
    'OTROS MATERIALES PRECIO',
    'T',
    'Activo',
    SYSDATE,
    'kyrodriguez',
    NULL,
    NULL,
    'TECNICA'
  );

COMMIT;
/