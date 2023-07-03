
-- INGRESO DE LA CABECERA DE PARAMETROS DE 'TIPO_PROCESOS_MASIVOS_TELCOS'
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
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
    'TIPO_PROCESOS_MASIVOS_TELCOS',
    'Parámetros para los tipos de procesos masivos de Telcos',
    'TECNICO',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1'
);
-- INGRESO LOS DETALLES DE LA CABECERA 'TIPO_PROCESOS_MASIVOS_TELCOS'
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
    EMPRESA_COD
)
VALUES
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT ID_PARAMETRO
        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'TIPO_PROCESOS_MASIVOS_TELCOS'
        AND ESTADO = 'Activo'
    ),
    'Detalle del tipo de proceso masivo de Telcos',
    'ReconectarCliente',
    'Reconectar Cliente',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1',
    '10'
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
    EMPRESA_COD
)
VALUES
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT ID_PARAMETRO
        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'TIPO_PROCESOS_MASIVOS_TELCOS'
        AND ESTADO = 'Activo'
    ),
    'Detalle del tipo de proceso masivo de Telcos',
    'CortarCliente',
    'Cortar Cliente',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1',
    '10'
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
    EMPRESA_COD
)
VALUES
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT ID_PARAMETRO
        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'TIPO_PROCESOS_MASIVOS_TELCOS'
        AND ESTADO = 'Activo'
    ),
    'Detalle del tipo de proceso masivo de Telcos',
    'CambioPlanMasivo',
    'Cambio Plan Masivo',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1',
    '10'
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
    EMPRESA_COD
)
VALUES
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT ID_PARAMETRO
        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'TIPO_PROCESOS_MASIVOS_TELCOS'
        AND ESTADO = 'Activo'
    ),
    'Detalle del tipo de proceso masivo de Telcos',
    'CancelarCliente',
    'Cancelar Cliente',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1',
    '10'
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
    EMPRESA_COD
)
VALUES
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT ID_PARAMETRO
        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'TIPO_PROCESOS_MASIVOS_TELCOS'
        AND ESTADO = 'Activo'
    ),
    'Detalle del tipo de proceso masivo de Telcos',
    'ActualizarCaracteristicasOlt',
    'Actualizar Características Olt',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1',
    '10'
);


-- INGRESO DE LA CABECERA DE PARAMETROS DE 'DATOS_TIPO_PROCESO_ACTUALIZAR_CARACT_OLT'
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
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
    'DATOS_TIPO_PROCESO_ACTUALIZAR_CARACT_OLT',
    'Parámetros para los datos del tipo de proceso masivo actualizar características del Olt',
    'TECNICO',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1'
);
-- INGRESO LOS DETALLES DE LA CABECERA 'DATOS_TIPO_PROCESO_ACTUALIZAR_CARACT_OLT'
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
    EMPRESA_COD
)
VALUES
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT ID_PARAMETRO
        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'DATOS_TIPO_PROCESO_ACTUALIZAR_CARACT_OLT'
        AND ESTADO = 'Activo'
    ),
    'Detalles de los datos del tipo de proceso masivo actualizar características del Olt',
    'ActualizarCaracteristicasOlt',
    '841',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1',
    '10'
);

COMMIT;
/
