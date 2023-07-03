/*
   *************************************************
   *                   PARÁMETRO                   *
   *        PROYECTO EXCEDENTE DE MATERIALES       *
   *    Cobertura máxima de fibra en instalación   *
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
     'Metraje que cubre el precio de instalación',
     '',
     'COMERCIAL',
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
        WHERE NOMBRE_PARAMETRO = 'Metraje que cubre el precio de instalación'
      ),
     'Metraje que cubre el precio de instalación',
     '800',
     null,
     'Activo',
     'kyrodriguez',
     SYSDATE,
     '10.10.10.10',
     '10',
     'VALOR1: descripcion de parámetro, VALOR2: Metros de fibra'
    );

COMMIT;

/
