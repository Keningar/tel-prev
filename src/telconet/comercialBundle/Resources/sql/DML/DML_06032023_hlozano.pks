
/**
 * Se realiza parametrización de la empresa ECUANET para la facturación de Instalación.
 * @author Hector Lozano <hlozano@telconet.ec>
 * @version 1.0 
 * @since 27-02-2023
 */

--SOLICITUD DE INSTALACIÓN.

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
    OBSERVACION,
    VALOR8,
    VALOR9
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'SOLICITUDES_DE_CONTRATO'
      AND ESTADO             = 'Activo'
    ),
    'Contratos creados desde el Móvil',
    'MOVIL',
    'POR_CONTRATO_DIGITAL',
    'Solicitud de descuento de Instalación por creación de contrato DIGITAL',
    'SOLICITUD INSTALACION GRATIS',
    'Activo',
    'hlozano',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    'Solicitud Instalacion Por Contrato Digital',
    (SELECT COD_EMPRESA FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO WHERE NOMBRE_EMPRESA = 'ECUANET'),
    'telcos_contrato',
    '2',
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
    OBSERVACION,
    VALOR8,
    VALOR9
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'SOLICITUDES_DE_CONTRATO'
      AND ESTADO             = 'Activo'
    ),
    'Contratos creados desde el Telcos',
    'WEB',
    'POR_CONTRATO_FISICO',
    'Solicitud de facturación de Instalación por creación de contrato WEB',
    'SOLICITUD FACTURACION CONTRATO WEB',
    'Activo',
    'hlozano',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    'Solicitud Instalacion Por Contrato Web',
    (SELECT COD_EMPRESA FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO WHERE NOMBRE_EMPRESA = 'ECUANET'),
    'telcos_web',
    '7',
    NULL,
    NULL,
    NULL
  );  


--ESTADOS DE SERVICIOS PARA CREAR LAS SOLICITUDES EN PROMOCIONES DE INSTALACIÓN.

-- Factible
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
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PROM_ESTADO_SERVICIO'
      AND ESTADO             = 'Activo'
    ),
    'PROM_ESTADO_SERVICIO',
    'PROM_INS_SOL_FACT',
    'Factible',
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
    (SELECT COD_EMPRESA FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO WHERE NOMBRE_EMPRESA = 'ECUANET')
  );
  
-- PrePlanificada
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
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PROM_ESTADO_SERVICIO'
      AND ESTADO             = 'Activo'
    ),
    'PROM_ESTADO_SERVICIO',
    'PROM_INS_SOL_FACT',
    'PrePlanificada',
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
    (SELECT COD_EMPRESA FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO WHERE NOMBRE_EMPRESA = 'ECUANET')
  );
  

-- Planificada
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
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PROM_ESTADO_SERVICIO'
      AND ESTADO             = 'Activo'
    ),
    'PROM_ESTADO_SERVICIO',
    'PROM_INS_SOL_FACT',
    'Planificada',
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
    (SELECT COD_EMPRESA FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO WHERE NOMBRE_EMPRESA = 'ECUANET')
  );

 --AsignadoTarea
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
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PROM_ESTADO_SERVICIO'
      AND ESTADO             = 'Activo'
    ),
    'PROM_ESTADO_SERVICIO',
    'PROM_INS_SOL_FACT',
    'AsignadoTarea',
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
    (SELECT COD_EMPRESA FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO WHERE NOMBRE_EMPRESA = 'ECUANET')
  );

 --Asignada
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
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PROM_ESTADO_SERVICIO'
      AND ESTADO             = 'Activo'
    ),
    'PROM_ESTADO_SERVICIO',
    'PROM_INS_SOL_FACT',
    'Asignada',
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
    (SELECT COD_EMPRESA FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO WHERE NOMBRE_EMPRESA = 'ECUANET')
  );


 --Replanificada
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
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PROM_ESTADO_SERVICIO'
      AND ESTADO             = 'Activo'
    ),
    'PROM_ESTADO_SERVICIO',
    'PROM_INS_SOL_FACT',
    'Replanificada',
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
    (SELECT COD_EMPRESA FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO WHERE NOMBRE_EMPRESA = 'ECUANET')
  );


 --EnVerificacion
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
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PROM_ESTADO_SERVICIO'
      AND ESTADO             = 'Activo'
    ),
    'PROM_ESTADO_SERVICIO',
    'PROM_INS_SOL_FACT',
    'EnVerificacion',
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
    (SELECT COD_EMPRESA FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO WHERE NOMBRE_EMPRESA = 'ECUANET')
  );

--Activo
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
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PROM_ESTADO_SERVICIO'
      AND ESTADO             = 'Activo'
    ),
    'PROM_ESTADO_SERVICIO',
    'PROM_INS_SOL_FACT',
    'Activo',
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
    (SELECT COD_EMPRESA FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO WHERE NOMBRE_EMPRESA = 'ECUANET')
  );


--SE CREA EL PARÁMETRO DÍAS DE VIGENCIA PARA CONTRATO WEB

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
        (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE ESTADO           = 'Activo'
           AND NOMBRE_PARAMETRO = 'DIAS_VIGENCIA_FACTURA'
        ),
        'Días de vigencia de una factura por CONTRATO FISICO',
        '10', --Número de días
        'SOLICITUD FACTURACION CONTRATO WEB', --NOMBRE SOLICITUD
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
        (SELECT COD_EMPRESA FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO WHERE NOMBRE_EMPRESA = 'ECUANET')
    );

--SE CREA EL PARÁMETRO DÍAS DE VIGENCIA PARA CONTRATO DIGITAL

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
        (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE ESTADO           = 'Activo'
           AND NOMBRE_PARAMETRO = 'DIAS_VIGENCIA_FACTURA'
        ),
        'Días de vigencia de una factura por CONTRATO DIGITAL',
        '5', --Número de días
        'SOLICITUD INSTALACION GRATIS', --NOMBRE SOLICITUD
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
        (SELECT COD_EMPRESA FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO WHERE NOMBRE_EMPRESA = 'ECUANET')
    );


--PARÁMETROS UTILIZADO PARA FACTURACIÓN DE INSTALACIÓN DE PUNTOS ADICIONALES DE LA EMPRESA ECUANET.

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
        (
          SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
          WHERE NOMBRE_PARAMETRO = 'PROMOCIONES_PARAMETROS_EJECUCION_DE_ALCANCE'
          AND ESTADO             = 'Activo'
        ),
        'NUMERO_DIAS_PROCESO_ALCANCE',
        '1',
        'ALCANCE',
        'Numero de días a considerar para la ejecucion del alcance de Promociones en base a las fechas de Inicio de Ciclo de Facturación se restara los dias a considerarse para el proceso de alcance',
        NULL,
        'Activo',
        'hlozano',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        NULL,
        (SELECT COD_EMPRESA FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO WHERE NOMBRE_EMPRESA = 'ECUANET')
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
        (
          SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
          WHERE NOMBRE_PARAMETRO = 'PROMOCIONES_PARAMETROS_EJECUCION_DE_ALCANCE'
          AND ESTADO             = 'Activo'
        ),
        'NUMERO_DIAS_FECHA_PROCESA_ALCANCE',
        '1',
        'ALCANCE',
        'Numero de días a restar a la fecha de procesamiento (Sysdate) con la cual se registrarán los mapeos y aplicaciones de promociones por los procesos de Alcances de ciclo1 y ciclo2',
        NULL,
        'Activo',
        'hlozano',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NUll,
        NULL,
        (SELECT COD_EMPRESA FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO WHERE NOMBRE_EMPRESA = 'ECUANET')
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
        (
          SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
          WHERE NOMBRE_PARAMETRO = 'PROMOCIONES_PARAMETROS_EJECUCION_DE_ALCANCE'
          AND ESTADO             = 'Activo'
        ),
        'NUMERO_DIAS_PROM_INS',
        '11',
        'ALCANCE',
        'Numero de días a considerar para obtener los servicios que se procesarán por las promociones de Instalación',
        NULL,
        'Activo',
        'hlozano',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        NULL,
        (SELECT COD_EMPRESA FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO WHERE NOMBRE_EMPRESA = 'ECUANET')
      );


--SOLICITUD DE INSTALACIÓN PARA PUNTOS ADICIONALES.

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
    OBSERVACION,
    VALOR8,
    VALOR9
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'SOLICITUDES_DE_INSTALACION_X_SERVICIO'
      AND ESTADO             = 'Activo'
    ),
    'Contratos creados desde el Móvil',
    'MOVIL',
    'POR_CONTRATO_DIGITAL',
    'Solicitud de descuento de Instalación por creación de contrato DIGITAL',
    'SOLICITUD INSTALACION GRATIS',
    'Activo',
    'hlozano',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    'Solicitud Instalacion Por Contrato Digital',
    (SELECT COD_EMPRESA FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO WHERE NOMBRE_EMPRESA = 'ECUANET'),
    'telcos_contrato',
    NULL,
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
    OBSERVACION,
    VALOR8,
    VALOR9
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'SOLICITUDES_DE_INSTALACION_X_SERVICIO'
      AND ESTADO             = 'Activo'
    ),
    'Contratos creados desde el Telcos',
    'WEB',
    'POR_CONTRATO_FISICO',
    'Solicitud de facturación de Instalación por creación de contrato WEB',
    'SOLICITUD FACTURACION CONTRATO WEB',
    'Activo',
    'hlozano',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    'Solicitud Instalacion Por Contrato Web',
    (SELECT COD_EMPRESA FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO WHERE NOMBRE_EMPRESA = 'ECUANET'),
    'telcos_web',
    NULL,
    NULL,
    NULL,
    NULL
  );  


--PLAN PARA FACTURAR INSTALACION DEL SERVICIO DE INTERNET DE LA EMPRESA ECUANET

INSERT 
INTO  DB_COMERCIAL.INFO_PLAN_CAB 
(
    ID_PLAN,
    CODIGO_PLAN,
    NOMBRE_PLAN,
    DESCRIPCION_PLAN,
    EMPRESA_COD,
    DESCUENTO_PLAN,
    ESTADO,
    IP_CREACION,
    FE_CREACION,
    USR_CREACION,
    IVA,
    ID_SIT,
    TIPO,
    PLAN_ID,
    CODIGO_INTERNO,
    FE_ULT_MOD,
    USR_ULT_MOD
) 
VALUES 
(
    DB_COMERCIAL.SEQ_INFO_PLAN_CAB.NEXTVAL,
    'INST',
    'INSTALACION HOME',
    'INSTALACION HOME',
    (SELECT COD_EMPRESA FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO WHERE NOMBRE_EMPRESA = 'ECUANET'),
    '0',
    'Activo',
    '127.0.0.1',
    SYSDATE,
    'hlozano',
    'S',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
); 

INSERT 
INTO DB_COMERCIAL.INFO_PLAN_DET 
(
    ID_ITEM,
    PRODUCTO_ID,
    PLAN_ID,
    CANTIDAD_DETALLE,
    COSTO_ITEM,
    PRECIO_ITEM,
    DESCUENTO_ITEM,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    IP_CREACION,
    PRECIO_UNITARIO
) 
VALUES 
(
    DB_COMERCIAL.SEQ_INFO_PLAN_DET.NEXTVAL,
    (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE CODIGO_PRODUCTO='OTROS' AND EMPRESA_COD=33),
    (SELECT ID_PLAN FROM DB_COMERCIAL.INFO_PLAN_CAB WHERE NOMBRE_PLAN='INSTALACION HOME' AND EMPRESA_COD=33),
    '1',
    '0',
    '1',
    '0',
    'Activo',
    SYSDATE,
    'hlozano',
    '127.0.0.1',
    NULL
); 


--PARAMETRO PARA FACTURAR INSTALACION DEL SERVICIO DE INTERNET DE LA EMPRESA ECUANET

--Contrato Digital
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
        (
          SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
          WHERE NOMBRE_PARAMETRO = 'FACTURACION_SOLICITUDES'
          AND ESTADO             = 'Activo'
        ),
        'Contrato Digital',
        'SOLICITUD INSTALACION GRATIS',
        (SELECT IPC.ID_PLAN
           FROM DB_COMERCIAL.INFO_PLAN_CAB IPC
          WHERE IPC.NOMBRE_PLAN   = 'INSTALACION HOME'
            AND IPC.EMPRESA_COD IN (SELECT COD_EMPRESA FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO WHERE NOMBRE_EMPRESA = 'ECUANET')
            AND IPC.ESTADO      = 'Activo'), --PLAN_ID
        NULL, --PRODUCTO_ID
        'Facturación por Instalación de Servicio', --OBSERVACION
        'Activo',
        'hlozano',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        'telcos_contrato',
        (SELECT COD_EMPRESA FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO WHERE NOMBRE_EMPRESA = 'ECUANET'),
        'N'
    );

    --Contrato Web
    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
            (
              SELECT ID_PARAMETRO
              FROM DB_GENERAL.ADMI_PARAMETRO_CAB
              WHERE NOMBRE_PARAMETRO = 'FACTURACION_SOLICITUDES'
              AND ESTADO             = 'Activo'
            ),
            'Contrato Web',
            'SOLICITUD FACTURACION CONTRATO WEB',
            (SELECT IPC.ID_PLAN
               FROM DB_COMERCIAL.INFO_PLAN_CAB IPC
              WHERE IPC.NOMBRE_PLAN   = 'INSTALACION HOME'
                AND IPC.EMPRESA_COD IN (SELECT COD_EMPRESA FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO WHERE NOMBRE_EMPRESA = 'ECUANET')
                AND IPC.ESTADO      = 'Activo'), --PLAN_ID
            NULL, --PRODUCTO_ID
            'Facturación por Instalación de Servicio', --OBSERVACION
            'Activo',
            'hlozano',
            SYSDATE,
            '127.0.0.1',
            NULL,
            NULL,
            NULL,
            'telcos_web',
            (SELECT COD_EMPRESA FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO WHERE NOMBRE_EMPRESA = 'ECUANET'),
            'N' 
        );

COMMIT;
/
