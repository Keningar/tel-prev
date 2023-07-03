/**
 * @author Luis Cabrera <lcabrera@telconet.ec>
 * @version 1.0
 * @since 10-10-2018
 * Se crean las sentencias DML para insertar parámetros (CAB y DET) relacionados con las visitas técnicas y el número de horas facturadas.
 */
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
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
        'ESTADOS_PUNTO_ADICIONAL',
        'PARAMETRO QUE DEFINE LOS ESTADOS DE UN PUNTO PARA QUE SEA ADICIONAL',
        'COMERCIAL',
        'ESTADOS_PUNTO_ADICIONAL',
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL
    );


--MEGADATOS
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
         WHERE NOMBRE_PARAMETRO = 'ESTADOS_PUNTO_ADICIONAL'
           AND MODULO = 'COMERCIAL'
           AND PROCESO = 'ESTADOS_PUNTO_ADICIONAL'
           AND ESTADO = 'Activo'
        ),
        'Estado para que un punto sea adicional MD',
        'Activo',
        NULL,
        NULL,
        NULL,
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        NULL,
        '18',
        NULL
    );

--MEGADATOS
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
         WHERE NOMBRE_PARAMETRO = 'ESTADOS_PUNTO_ADICIONAL'
           AND MODULO = 'COMERCIAL'
           AND PROCESO = 'ESTADOS_PUNTO_ADICIONAL'
           AND ESTADO = 'Activo'
        ),
        'Estado para que un punto sea adicional TN',
        'Activo',
        NULL,
        NULL,
        NULL,
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        NULL,
        '10',
        NULL
    );

COMMIT;

/**
 * @author Luis Cabrera <lcabrera@telconet.ec>
 * @version 1.0
 * @since 10-10-2018
 * Se crean las sentencias DML para insertar parámetros (CAB y DET) relacionados con las solicitudes de contrato/ facturación de instalación.
 */
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
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
        'SOLICITUDES_DE_INSTALACION_X_SERVICIO',
        'SE INGRESAN LOS TIPOS DE CONTRATOS PARA PODER SER FACTURADOS.',
        'COMERCIAL',
        'SOLICITUDES_DE_INSTALACION_X_SERVICIO',
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL
    );


--CONTRATO WEB
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
        VALOR6,
        VALOR7
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'SOLICITUDES_DE_INSTALACION_X_SERVICIO' 
           AND MODULO = 'COMERCIAL'
           AND PROCESO = 'SOLICITUDES_DE_INSTALACION_X_SERVICIO'
           AND ESTADO = 'Activo'
        ),
        'Contratos creados desde el Telcos',
        'WEB', --ORIGEN
        'POR_CONTRATO_FISICO', --CARACTERÍSTICA DEL CONTRATO DIGITAL/FISICO
        'Solicitud de facturación de Instalación por creación de contrato WEB', --OBSERVACIÓN PARA INSERTAR EN LA SOLICITUD
        'SOLICITUD FACTURACION CONTRATO WEB', --NOMBRE DE LA SOLICITUD
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        'Solicitud Instalacion Por Contrato Web',--Nombre Motivo
        '18',
        'telcos_web', --USR_CREACION para escribir los registros en los procesos de solicitud y facturas.
        NULL --Días permitidos para crear factura.
    );

--CONTRATO DIGITAL
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
        VALOR6,
        VALOR7
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'SOLICITUDES_DE_INSTALACION_X_SERVICIO' 
           AND MODULO = 'COMERCIAL'
           AND PROCESO = 'SOLICITUDES_DE_INSTALACION_X_SERVICIO'
           AND ESTADO = 'Activo'
        ),
        'Contratos creados desde el Móvil',
        'MOVIL', --CARACTERÍSTICA DEL CONTRATO DIGITAL/FISICO
        'POR_CONTRATO_DIGITAL', --CARACTERÍSTICA DEL CONTRATO DIGITAL/FISICO
        'Solicitud de descuento de Instalación por creación de contrato DIGITAL', --OBSERVACIÓN PARA INSERTAR EN LA SOLICITUD
        'SOLICITUD INSTALACION GRATIS', --NOMBRE DE LA SOLICITUD
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        'Solicitud Instalacion Por Contrato Digital',--Nombre Motivo
        '18',
        'telcos_contrato', --USR_CREACION para escribir los registros en los procesos de solicitud y facturas.
        NULL --Días permitidos para crear factura.
    );

COMMIT;


/**
 * @author Luis Cabrera <lcabrera@telconet.ec>
 * @version 1.0
 * @since 24-11-2018
 * Característica para determinar el detalle de una factura detallada.
 */
    INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
        ID_CARACTERISTICA,
        DESCRIPCION_CARACTERISTICA,
        TIPO_INGRESO,
        ESTADO,
        FE_CREACION,
        USR_CREACION,
        FE_ULT_MOD,
        USR_ULT_MOD,
        TIPO
    ) VALUES (
        DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
        'CANTIDAD_DETALLE',
        'N',
        'Activo',
        SYSDATE,
        'lcabrera',
        NULL,
        NULL,
        'FINANCIERO'
    );

COMMIT;

/**
 * @author Luis Cabrera <lcabrera@telconet.ec>
 * @version 1.0
 * @since 24-11-2018
 * Se crean las sentencias DML para insertar parámetros (CAB y DET) relacionados con los planes y sus restricciones para facturación de instalación.
 */
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
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
        'RESTRICCION_PLANES_X_INSTALACION',
        'PARAMETRO QUE DEFINE LOS PLANES QUE TIENEN RESTRICCIONES PARA LA FACTURACIÓN DE INSTALACIÓN V1= EXPRESIÓN REGULAR V2= PORCENTAJE DESCUENTO V3= MATCH PARAMETER DE REGEXP_LIKE ',
        'COMERCIAL',
        'FACTURACION_INSTALACION',
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL
    );


--MEGADATOS
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
         WHERE NOMBRE_PARAMETRO = 'RESTRICCION_PLANES_X_INSTALACION'
           AND MODULO = 'COMERCIAL'
           AND PROCESO = 'FACTURACION_INSTALACION'
           AND ESTADO = 'Activo'
        ),
        'Planes que no pagan instalación (Planes finalizados en "FARMA","EMTN","EMPL"): 100% de descuento',
        '^(.*FARMA$|.*EMTN$|.*EMPL$)',
        'c',
        '100',
        NULL,
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        NULL,
        '18',
        NULL
    );

--MEGADATOS
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
         WHERE NOMBRE_PARAMETRO = 'RESTRICCION_PLANES_X_INSTALACION'
           AND MODULO = 'COMERCIAL'
           AND PROCESO = 'FACTURACION_INSTALACION'
           AND ESTADO = 'Activo'
        ),
        'Planes que siempre pagan instalación (Planes que contienen "PYME EMPRESA"): 0% de descuento',
        '^(.*PYME EMPRESA)',
        'i',
        '0',
        NULL,
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        NULL,
        '18',
        NULL
    );

COMMIT;

/**
 * Parámetros para determinar si una empresa aplica o no a un proceso.
 */
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
         WHERE NOMBRE_PARAMETRO = 'EMPRESA_APLICA_PROCESO'
           AND MODULO = 'GENERAL'
           AND PROCESO = 'TELCOS'
           AND ESTADO = 'Activo'
        ),
        'Proceso de facturación a la instalación para los servicios al cambiar a estado Factible',
        'FACTURACION_INSTALACION_PUNTOS_ADICIONALES',
        'S',
        NULL,
        NULL,
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        NULL,
        '18'
    );

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
         WHERE NOMBRE_PARAMETRO = 'EMPRESA_APLICA_PROCESO'
           AND MODULO = 'GENERAL'
           AND PROCESO = 'TELCOS'
           AND ESTADO = 'Activo'
        ),
        'Proceso de facturación a la instalación para los servicios al cambiar a estado Factible',
        'FACTURACION_INSTALACION_PUNTOS_ADICIONALES',
        'N',
        NULL,
        NULL,
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        NULL,
        '10'
    );

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
         WHERE NOMBRE_PARAMETRO = 'EMPRESA_APLICA_PROCESO'
           AND MODULO = 'GENERAL'
           AND PROCESO = 'TELCOS'
           AND ESTADO = 'Activo'
        ),
        'Restricción para agregar planes de internet en el mismo punto.',
        'RESTRICCION_PLAN_INTERNET_ADICIONAL_X_PUNTO',
        'S',
        NULL,
        NULL,
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        NULL,
        '18'
    );

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
         WHERE NOMBRE_PARAMETRO = 'EMPRESA_APLICA_PROCESO'
           AND MODULO = 'GENERAL'
           AND PROCESO = 'TELCOS'
           AND ESTADO = 'Activo'
        ),
        'Restricción para agregar planes de internet en el mismo punto.',
        'RESTRICCION_PLAN_INTERNET_ADICIONAL_X_PUNTO',
        'N',
        NULL,
        NULL,
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        NULL,
        '10'
    );
COMMIT;

/**
 * @author Luis Cabrera <lcabrera@telconet.ec>
 * @version 1.0
 * @since 10-10-2018
 * Se crean las sentencias DML para insertar parámetros (CAB y DET) relacionados con los estados a considerar para validar una O/S por un plan adicional.
 */
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
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
        'ESTADOS_RESTRICCION_PLANES_ADICIONALES',
        'PARAMETRO QUE DEFINE LOS ESTADOS NO PERMITIDOS EN UN PLAN ADICIONAL EN EL PUNTO.',
        'COMERCIAL',
        'ESTADOS_RESTRICCION_PLANES_ADICIONALES',
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL
    );


--MEGADATOS
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
         WHERE NOMBRE_PARAMETRO = 'ESTADOS_RESTRICCION_PLANES_ADICIONALES'
           AND MODULO = 'COMERCIAL'
           AND PROCESO = 'ESTADOS_RESTRICCION_PLANES_ADICIONALES'
           AND ESTADO = 'Activo'
        ),
        'Estado para que un plan no sea considerado',
        'Eliminado',
        NULL,
        NULL,
        NULL,
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        NULL,
        '18',
        NULL
    );

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
         WHERE NOMBRE_PARAMETRO = 'ESTADOS_RESTRICCION_PLANES_ADICIONALES'
           AND MODULO = 'COMERCIAL'
           AND PROCESO = 'ESTADOS_RESTRICCION_PLANES_ADICIONALES'
           AND ESTADO = 'Activo'
        ),
        'Estado para que un plan no sea considerado',
        'Anulado',
        NULL,
        NULL,
        NULL,
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        NULL,
        '18',
        NULL
    );

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
         WHERE NOMBRE_PARAMETRO = 'ESTADOS_RESTRICCION_PLANES_ADICIONALES'
           AND MODULO = 'COMERCIAL'
           AND PROCESO = 'ESTADOS_RESTRICCION_PLANES_ADICIONALES'
           AND ESTADO = 'Activo'
        ),
        'Estado para que un plan no sea considerado',
        'Trasladado',
        NULL,
        NULL,
        NULL,
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        NULL,
        '18',
        NULL
    );

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
         WHERE NOMBRE_PARAMETRO = 'ESTADOS_RESTRICCION_PLANES_ADICIONALES'
           AND MODULO = 'COMERCIAL'
           AND PROCESO = 'ESTADOS_RESTRICCION_PLANES_ADICIONALES'
           AND ESTADO = 'Activo'
        ),
        'Estado para que un plan no sea considerado',
        'Reubicado',
        NULL,
        NULL,
        NULL,
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        NULL,
        '18',
        NULL
    );

COMMIT;
