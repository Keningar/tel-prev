
--Ingresar el tipo de la solicitud para procesar las promociones
INSERT INTO DB_COMERCIAL.ADMI_TIPO_SOLICITUD
(
        ID_TIPO_SOLICITUD,
        DESCRIPCION_SOLICITUD,
        ESTADO,
        USR_CREACION,
        FE_CREACION
)
VALUES
(
        DB_COMERCIAL.SEQ_ADMI_TIPO_SOLICITUD.NEXTVAL,
        'SOLICITUD PROCESAR PROMOCIONES BW MASIVO',
        'Activo',
        'facaicedo',
        SYSDATE
);

--Ingresar la característica del id de la promoción
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA
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
        'ID_GRUPO_PROMOCION',
        'N',
        'Activo',
        SYSDATE,
        'facaicedo',
        NULL,
        NULL,
        'COMERCIAL'
);

--detalles de parametros del estado de la promocion una vez procesado los registros
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
            WHERE NOMBRE_PARAMETRO = 'PARAMETROS_PROMOCIONES_MASIVAS_BW'
            AND ESTADO = 'Activo'
        ),
        'APLICAR',
        'ESTADO_REGISTRO',
        'Registrada',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1',
        (
            SELECT COD_EMPRESA
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
            WHERE PREFIJO = 'MD'
        )
);

--detalles de parametros para el tiempo de inicio del job en minutos
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
            WHERE NOMBRE_PARAMETRO = 'PARAMETROS_PROMOCIONES_MASIVAS_BW'
            AND ESTADO = 'Activo'
        ),
        'Tiempo de inicio del job en minutos para el ingreso de los historiales de las promociones.',
        'TIEMPO_MINUTO_INICIO_JOB',
        '5',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1',
        (
            SELECT COD_EMPRESA
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
            WHERE PREFIJO = 'MD'
        )
);

--detalles de parametros para los historiales de los servicios
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
            WHERE NOMBRE_PARAMETRO = 'PARAMETROS_PROMOCIONES_MASIVAS_BW'
            AND ESTADO = 'Activo'
        ),
        'APLICAR',
        'FORMATO_HISTORIAL_SERVICIO',
        'Se aplica la promoción de ancho de banda.'
        ||'<br>VIGENCIA: {{FE_INICIO}} hasta {{FE_FIN}}'
        ||'<br>FRANJA HORARIA: {{HORA_INICIO}} a {{HORA_FIN}}'
        ||'<br>Nombre de plan contratado: <b>{{PLAN_ACTUAL}}</b>'
        ||'<br>Line profile de la promoción: <b>{{LINE_PROFILE_PROMO}}</b>',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1',
        (
            SELECT COD_EMPRESA
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
            WHERE PREFIJO = 'MD'
        )
);

--detalles de parametros para los historiales de los servicios
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
            WHERE NOMBRE_PARAMETRO = 'PARAMETROS_PROMOCIONES_MASIVAS_BW'
            AND ESTADO = 'Activo'
        ),
        'QUITAR',
        'FORMATO_HISTORIAL_SERVICIO',
        'Se ejecuta la finalización de la promoción de ancho de banda.'
        ||'<br>Nombre de plan contratado: <b>{{PLAN_ACTUAL}}</b>'
        ||'<br>Line profile de la promoción: <b>{{LINE_PROFILE_PROMO}}</b>',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1',
        (
            SELECT COD_EMPRESA
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
            WHERE PREFIJO = 'MD'
        )
);

--detalles de parametros de los reportes de promociones de ancho banda
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET SET OBSERVACION = 'Se realizó el envió del reporte para el control de olt confirmados por Activación.'
    WHERE PARAMETRO_ID = ( SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB
          WHERE NOMBRE_PARAMETRO = 'PARAMETROS_PROMOCIONES_MASIVAS_BW' AND ESTADO = 'Activo')
    AND DESCRIPCION = 'REPORTE_BW' AND VALOR1 = 'APLICAR';

--detalles de parametros de los reportes de promociones de ancho banda
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET SET OBSERVACION = 'Se realizó el envió del reporte para el control de olt confirmados por Inactivación.'
    WHERE PARAMETRO_ID = ( SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB
          WHERE NOMBRE_PARAMETRO = 'PARAMETROS_PROMOCIONES_MASIVAS_BW' AND ESTADO = 'Activo')
    AND DESCRIPCION = 'REPORTE_BW' AND VALOR1 = 'QUITAR';

--actualizar tiempo de inicio de la finalizacion de la promocion
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET SET VALOR3 = '125'
    WHERE PARAMETRO_ID = ( SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB
          WHERE NOMBRE_PARAMETRO = 'PARAMETROS_PROMOCIONES_MASIVAS_BW' AND ESTADO = 'Activo')
    AND DESCRIPCION = 'QUITAR' AND VALOR1 = 'RANGO_MINUTOS';

COMMIT;
/
