  -- Insert de usuario para facturación única
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
      OBSERVACION
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'FILTROS DE FACTURACION AUTOMATICA'),
      'Facturacion Unica',
      'FAC',
      'telcos_fact_unica',
      NULL,
      NULL,
      'Activo',
      'hlozano',
      SYSDATE,
      '127.0.0.1',
      NULL,
      NULL,
      NULL,
      NULL,
      '10',
      NULL
    );
  
  -- Insert de solicitud para facturación única por detalle
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
      'SOLICITUD FACTURACION UNICA POR DETALLE',
      SYSDATE,
      'hlozano',
      NULL,
      NULL,
      'Activo',
      NULL,
      NULL,
      NULL
    );

  -- Insert detalle de solicitud en ADMI_PARAMETRO_DET
  -- MD
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
	  OBSERVACION,
	  VALOR6,
	  VALOR7
    )
    VALUES 
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO='FACTURACION_SOLICITUDES'),
      'Facturacion unica por detalle',
      'SOLICITUD FACTURACION UNICA POR DETALLE',
      NULL,
      NULL,
      NULL,
      'Activo',
      'hlozano',
	  SYSDATE,
	  '127.0.0.1',
	  NULL,
	  NULL,
	  NULL,
	  'telcos_facturacion_unica',
	  '18',
	  NULL,
	  'S',
	  NULL
    );
  
  --TN
  Insert 
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
	  OBSERVACION,
	  VALOR6,
	  VALOR7
    )
    VALUES 
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO='FACTURACION_SOLICITUDES'),
      'Facturacion unica por detalle',
      'SOLICITUD FACTURACION UNICA POR DETALLE',
      NULL,
      NULL,
      NULL,
      'Activo',
      'hlozano',
	  SYSDATE,
      '127.0.0.1',
	  NULL,
	  NULL,
	  NULL,
	  'telcos_facturacion_unica',
	  '10',
	  NULL,
	  'S',
	  NULL
    );

  --Inserta parámetro de características no incluidas en ADMI_PARAMETRO_CAB
  Insert 
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
      values 
      (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
        'CARACTERISTICAS_NO_INCLUIDAS',
        'CARACTERISTICAS QUE NO SE INCLUYEN EN FACTURACION UNICA',
        NULL,
        NULL,
        'Activo',
        'hlozano',
        SYSDATE,
	    '127.0.0.1',
	    NULL,
	    NULL,
	    NULL
      );

  --Inserta detalle de parámetro de características no incluidas en ADMI_PARAMETRO_DET
  Insert 
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
	    OBSERVACION,
	    VALOR6,
	    VALOR7
      )
      values 
      (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO ='CARACTERISTICAS_NO_INCLUIDAS'),
        'Caracteristica no incluida NETHOME',
        'NETHOME',
        NULL,
        NULL,
        NULL,
        'Activo',
        'hlozano',
	    SYSDATE,
	    '127.0.0.1',
	    NULL,
	    NULL,
	    NULL,
	    NULL,
	    '18',
	    NULL,
	    NULL,
	    NULL
      );
   
 COMMIT;
   