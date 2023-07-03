--=======================================================================
-- Ingreso los detalles de parámetros para controlar los tiempos máximo de ejecución de los procesos
--=======================================================================

-- INGRESO DE LA CABECERA DE PARAMETROS DE 'CONTROL_PROCESOS_MAXIMO_TIEMPO_EJECUCION'
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
        'CONTROL_PROCESOS_MAXIMO_TIEMPO_EJECUCION',
        'Parámetros para controlar los tiempos máximo de ejecución de los procesos',
        'TECNICO',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);
-- INGRESO LOS DETALLES DE LA CABECERA 'CONTROL_PROCESOS_MAXIMO_TIEMPO_EJECUCION'
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
        IP_CREACION
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'CONTROL_PROCESOS_MAXIMO_TIEMPO_EJECUCION'
            AND ESTADO = 'Activo'
        ),
        'Detalle del tiempo máximo de ejecución del proceso',
        'createOltAction',
        '1000000',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
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
        IP_CREACION
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'CONTROL_PROCESOS_MAXIMO_TIEMPO_EJECUCION'
            AND ESTADO = 'Activo'
        ),
        'Detalle del tiempo máximo de ejecución del proceso',
        'actualizarCaracteristicasHuaweiAjaxAction',
        '1000000',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);

COMMIT;
/
