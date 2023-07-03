--=======================================================================
-- Ingreso los detalles de parámetros para permitir la ejecución del procedimiento para el control del BW de la interface
-- Ingreso los detalles de parámetros para las ejecuciones y progresos para el control del BW de la interface
-- Ingreso los detalles de parámetros para los errores para el control del BW de la interface
-- Ingreso los detalles de parámetros para los json con los id de los elementos para el control del BW de la interface
-- Ingreso los detalles de parámetros para los json con los id de las interfaces para el control del BW de la interface
--=======================================================================

-- INGRESO DE LA CABECERA DE PARAMETROS DE 'EJECUCION_CONTROL_BW_INTERFACE'
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
        'EJECUCION_CONTROL_BW_INTERFACE',
        'Permitir la ejecución del procedimiento para el control del BW de la interface',
        'TECNICO',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);
-- INGRESO LOS DETALLES DE LA CABECERA 'EJECUCION_CONTROL_BW_INTERFACE'
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
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
            WHERE NOMBRE_PARAMETRO = 'EJECUCION_CONTROL_BW_INTERFACE'
            AND ESTADO = 'Activo'
        ),
        'Parametro para permitir la ejecución del procedimiento control BW de la interface',
        'NO',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1',
        (
            SELECT COD_EMPRESA
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
            WHERE PREFIJO = 'TN'
        )
);
-- INGRESO DE LA CABECERA DE PARAMETROS DE 'PROGRESO_CONTROL_BW_INTERFACE'
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
        'PROGRESO_CONTROL_BW_INTERFACE',
        'Listas de las ejecuciones y progresos para el control del BW de la interface',
        'TECNICO',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);
-- INGRESO DE LA CABECERA DE PARAMETROS DE 'ERRORES_CONTROL_BW_INTERFACE'
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
        'ERRORES_CONTROL_BW_INTERFACE',
        'Listas de los errores para el control del BW de la interface',
        'TECNICO',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);
-- INGRESO DE LA CABECERA DE PARAMETROS DE 'ELEMENTOS_ARRAY_CONTROL_BW_INTERFACE'
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
        'ELEMENTOS_ARRAY_CONTROL_BW_INTERFACE',
        'Listas de json con los id de los elementos para el control del BW de la interface',
        'TECNICO',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);
-- INGRESO DE LA CABECERA DE PARAMETROS DE 'INTERFACE_ARRAY_CONTROL_BW_INTERFACE'
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
        'INTERFACE_ARRAY_CONTROL_BW_INTERFACE',
        'Listas de json con los id de las interfaces para el control del BW de la interface',
        'TECNICO',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);
COMMIT;
/
