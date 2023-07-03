--2. dias de reingreso
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
  VALUES(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT ID_PARAMETRO 
        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'PARAMETROS_REINGRESO_OS_AUTOMATICA'
        AND ESTADO             = 'Activo'
    ),
    'MENSAJE_ERROR_DIAS_REINGRESO',
    'No se creó el servicio mediante Reingreso Automático, motivo Cliente sobrepasa los {DiasPermitidos} días permitidos para Reingreso automático.',
    null,
    NULL,
    NULL,
    'Activo',
    'gnarea',
    sysdate,
    '127.0.0.1',
    'gnarea',
    sysdate,
    '127.0.0.1',
    null,
    '18'
);

--3. devolucion factura
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
  VALUES(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT ID_PARAMETRO 
        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'PARAMETROS_REINGRESO_OS_AUTOMATICA'
        AND ESTADO             = 'Activo'
    ),
    'MENSAJE_ERROR_DEVOLUCION_FACTURA',
    'No se creo el servicio mediante Reingreso Automático, motivo Cliente posee una devolución o no ha pagado la Factura de Instalación.',
    null,
    NULL,
    NULL,
    'Activo',
    'gnarea',
    sysdate,
    '127.0.0.1',
    'gnarea',
    sysdate,
    '127.0.0.1',
    null,
    '18'
);

--4. “Posee Factura de Instalación en estado Activo
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
  VALUES(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT ID_PARAMETRO 
        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'PARAMETROS_REINGRESO_OS_AUTOMATICA'
        AND ESTADO             = 'Activo'
    ),
    'MENSAJE_ERROR_POSEE_FACTURA_INSTALACION',
    'No se creo el servicio por Reingreso Automatico.<br>Motivo Posee Factura de Instalación',
    null,
    NULL,
    NULL,
    'Activo',
    'gnarea',
    sysdate,
    '127.0.0.1',
    'gnarea',
    sysdate,
    '127.0.0.1',
    null,
    '18'
);

--5. No existen anticipos de valor mayor o igual a la factura
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
  VALUES(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT ID_PARAMETRO 
        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'PARAMETROS_REINGRESO_OS_AUTOMATICA'
        AND ESTADO             = 'Activo'
    ),
    'MENSAJE_ERROR_SIN_ANTICIPOS_MAYOR_FACTURA',
    'No existen anticipos de valor mayor o igual a la factura',
    null,
    NULL,
    NULL,
    'Activo',
    'gnarea',
    sysdate,
    '127.0.0.1',
    'gnarea',
    sysdate,
    '127.0.0.1',
    null,
    '18'
);

--6. Forma de pago
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
  VALUES(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT ID_PARAMETRO 
        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'PARAMETROS_REINGRESO_OS_AUTOMATICA'
        AND ESTADO             = 'Activo'
    ),
    'MENSAJE_ERROR_FORMA_PAGO',
    'OS No procede para Reingreso Automático<br>Motivo: Cambio de Forma de Pago',
    null,
    NULL,
    NULL,
    'Activo',
    'gnarea',
    sysdate,
    '127.0.0.1',
    'gnarea',
    sysdate,
    '127.0.0.1',
    null,
    '18'
);


commit;
/
