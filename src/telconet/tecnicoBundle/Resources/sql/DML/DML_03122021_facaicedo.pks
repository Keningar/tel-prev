--insert de la admi plantilla para el porceso de finalizar la activación de la promoción de ancho de banda
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA
(
    ID_PLANTILLA,
    NOMBRE_PLANTILLA,
    CODIGO,
    MODULO,
    PLANTILLA,
    ESTADO,
    FE_CREACION,
    USR_CREACION
)
VALUES
(
    DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,
    'Plantilla de promociones de ancho de banda al finalizar el proceso de activación de la promoción.',
    'PROMBW_PROC',
    'COMERCIAL',
    '<html>
        <head>
        <meta http-equiv=Content-Type content="text/html; charset=UTF-8">
        <style type="text/css">table.cssTable { font-family: verdana,arial,sans-serif;font-size:11px;color:#333333;border-width: 1px;border-color: #999999;border-collapse: collapse;}table.cssTable th {background-color:#c3dde0;border-width: 1px;padding: 8px;border-style: solid;border-color: #a9c6c9;}table.cssTable tr {background-color:#d4e3e5;}table.cssTable td {border-width: 1px;padding: 8px;border-style: solid;border-color: #a9c6c9;}table.cssTblPrincipal{font-family: verdana,arial,sans-serif;font-size:12px;}</style>
        </head>
        <body>
        <table class = "cssTblPrincipal" align="center" width="100%" cellspacing="0" cellpadding="5">
        <tr>
        <td align="center" style="border:1px solid #6699CC;background-color:#E5F2FF;">
        <img alt=""  src="http://images.telconet.net/others/sit/notificaciones/logo.png"/>
        </td>
        </tr>
        <tr><td style="border:1px solid #6699CC;">
            <table width="100%" cellspacing="0" cellpadding="5">
            <tr><td colspan="2">Estimados,</td></tr>
            <tr>
            <td colspan="2">
            El presente correo es para notificar la finalizacion del proceso de activacion de la promocion.
            </td></tr>
            <tr><td colspan="2"><b>Id Promocion:</b> {{ID_PROMOCION}}</td></tr>
            <tr><td colspan="2"><b>Nombre Grupo:</b> {{NOMBRE_GRUPO}}</td></tr>
            <tr><td colspan="2"><hr /></td></tr>
            </table>
        </td></tr>
        <tr><td></td></tr></table></body></html>',
    'Activo',
    SYSDATE,
    'facaicedo'
);
--insert de la admi plantilla para el porceso de finalizar la inactivación de la promoción de ancho de banda
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA
(
    ID_PLANTILLA,
    NOMBRE_PLANTILLA,
    CODIGO,
    MODULO,
    PLANTILLA,
    ESTADO,
    FE_CREACION,
    USR_CREACION
)
VALUES
(
    DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,
    'Plantilla de promociones de ancho de banda al finalizar el proceso de inactivación de la promoción.',
    'PROMBW_DET',
    'COMERCIAL',
    '<html>
        <head>
        <meta http-equiv=Content-Type content="text/html; charset=UTF-8">
        <style type="text/css">table.cssTable { font-family: verdana,arial,sans-serif;font-size:11px;color:#333333;border-width: 1px;border-color: #999999;border-collapse: collapse;}table.cssTable th {background-color:#c3dde0;border-width: 1px;padding: 8px;border-style: solid;border-color: #a9c6c9;}table.cssTable tr {background-color:#d4e3e5;}table.cssTable td {border-width: 1px;padding: 8px;border-style: solid;border-color: #a9c6c9;}table.cssTblPrincipal{font-family: verdana,arial,sans-serif;font-size:12px;}</style>
        </head>
        <body>
        <table class = "cssTblPrincipal" align="center" width="100%" cellspacing="0" cellpadding="5">
        <tr>
        <td align="center" style="border:1px solid #6699CC;background-color:#E5F2FF;">
        <img alt=""  src="http://images.telconet.net/others/sit/notificaciones/logo.png"/>
        </td>
        </tr>
        <tr><td style="border:1px solid #6699CC;">
            <table width="100%" cellspacing="0" cellpadding="5">
            <tr><td colspan="2">Estimados,</td></tr>
            <tr>
            <td colspan="2">
            El presente correo es para notificar la finalizacion del proceso de inactivacion de la promocion.
            </td></tr>
            <tr><td colspan="2"><b>Id Promocion:</b> {{ID_PROMOCION}}</td></tr>
            <tr><td colspan="2"><b>Nombre Grupo:</b> {{NOMBRE_GRUPO}}</td></tr>
            <tr><td colspan="2"><hr /></td></tr>
            </table>
        </td></tr>
        <tr><td></td></tr></table></body></html>',
    'Activo',
    SYSDATE,
    'facaicedo'
);
--insert de la admi plantilla para el reporte de las promociones de ancho de banda
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA
(
    ID_PLANTILLA,
    NOMBRE_PLANTILLA,
    CODIGO,
    MODULO,
    PLANTILLA,
    ESTADO,
    FE_CREACION,
    USR_CREACION
)
VALUES
(
    DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,
    'Plantilla de promociones de ancho de banda el reporte de las promociones.',
    'PROM_BW_REP',
    'COMERCIAL',
    '<html>
        <head>
        <meta http-equiv=Content-Type content="text/html; charset=UTF-8">
        <style type="text/css">table.cssTable { font-family: verdana,arial,sans-serif;font-size:11px;color:#333333;border-width: 1px;border-color: #999999;border-collapse: collapse;}table.cssTable th {background-color:#c3dde0;border-width: 1px;padding: 8px;border-style: solid;border-color: #a9c6c9;}table.cssTable tr {background-color:#d4e3e5;}table.cssTable td {border-width: 1px;padding: 8px;border-style: solid;border-color: #a9c6c9;}table.cssTblPrincipal{font-family: verdana,arial,sans-serif;font-size:12px;}</style>
        </head>
        <body>
        <table class = "cssTblPrincipal" align="center" width="100%" cellspacing="0" cellpadding="5">
        <tr>
        <td align="center" style="border:1px solid #6699CC;background-color:#E5F2FF;">
        <img alt=""  src="http://images.telconet.net/others/sit/notificaciones/logo.png"/>
        </td>
        </tr>
        <tr><td style="border:1px solid #6699CC;">
            <table width="100%" cellspacing="0" cellpadding="5">
            <tr><td colspan="2">Estimados,</td></tr>
            <tr>
            <td colspan="2">
            El presente correo es para notificar el reporte de la promocion.
            </td></tr>
            <tr><td colspan="2"><b>Id Promocion:</b> {{ID_PROMOCION}}</td></tr>
            <tr><td colspan="2"><b>Nombre Grupo:</b> {{NOMBRE_GRUPO}}</td></tr>
            <tr>
            <td colspan="2">
            Se adjunta reporte de OLT´s sin confirmación:
            </td></tr>
            <tr><td colspan="2">Confirmados: {{TOTAL_CONFIRMADO}}</td></tr>
            <tr><td colspan="2">No Confirmados: {{TOTAL_NO_CONFIRMADO}}</td></tr>
            <tr><td colspan="2"><hr /></td></tr>
            </table>
        </td></tr>
        <tr><td></td></tr></table></body></html>',
    'Activo',
    SYSDATE,
    'facaicedo'
);
--insert del alias para el porceso de finalizar la activación de la promoción de ancho de banda
INSERT INTO DB_COMUNICACION.INFO_ALIAS_PLANTILLA
(
    ID_ALIAS_PLANTILLA,
    ALIAS_ID,
    PLANTILLA_ID,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    ES_COPIA
)
VALUES
(
    DB_COMUNICACION.SEQ_INFO_ALIAS_PLANTILLA.NEXTVAL,
    (
        SELECT ID_ALIAS
        FROM DB_COMUNICACION.ADMI_ALIAS
        WHERE VALOR      = 'informaticos@netlife.net.ec'
        AND ESTADO       IN ('Activo','Modificado')
        AND EMPRESA_COD  = '18'
    ),
    (
        SELECT ID_PLANTILLA
        FROM DB_COMUNICACION.ADMI_PLANTILLA
        WHERE CODIGO = 'PROMBW_PROC'
        AND ESTADO   = 'Activo'
    ),
    'Activo',
    SYSDATE,
    'facaicedo',
    'NO'
);
INSERT INTO DB_COMUNICACION.INFO_ALIAS_PLANTILLA
(
    ID_ALIAS_PLANTILLA,
    ALIAS_ID,
    PLANTILLA_ID,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    ES_COPIA
)
VALUES
(
    DB_COMUNICACION.SEQ_INFO_ALIAS_PLANTILLA.NEXTVAL,
    (
        SELECT ID_ALIAS
        FROM DB_COMUNICACION.ADMI_ALIAS
        WHERE VALOR      = 'marketing@netlife.net.ec'
        AND ESTADO       IN ('Activo','Modificado')
        AND EMPRESA_COD  = '18'
    ),
    (
        SELECT ID_PLANTILLA
        FROM DB_COMUNICACION.ADMI_PLANTILLA
        WHERE CODIGO = 'PROMBW_PROC'
        AND ESTADO   = 'Activo'
    ),
    'Activo',
    SYSDATE,
    'facaicedo',
    'NO'
);
INSERT INTO DB_COMUNICACION.INFO_ALIAS_PLANTILLA
(
    ID_ALIAS_PLANTILLA,
    ALIAS_ID,
    PLANTILLA_ID,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    ES_COPIA
)
VALUES
(
    DB_COMUNICACION.SEQ_INFO_ALIAS_PLANTILLA.NEXTVAL,
    (
        SELECT ID_ALIAS
        FROM DB_COMUNICACION.ADMI_ALIAS
        WHERE VALOR      = 'red_acceso@netlife.net.ec'
        AND ESTADO       IN ('Activo','Modificado')
        AND EMPRESA_COD  = '18'
    ),
    (
        SELECT ID_PLANTILLA
        FROM DB_COMUNICACION.ADMI_PLANTILLA
        WHERE CODIGO = 'PROMBW_PROC'
        AND ESTADO   = 'Activo'
    ),
    'Activo',
    SYSDATE,
    'facaicedo',
    'NO'
);
--insert del alias para el porceso de finalizar la inactivación de la promoción de ancho de banda
INSERT INTO DB_COMUNICACION.INFO_ALIAS_PLANTILLA
(
    ID_ALIAS_PLANTILLA,
    ALIAS_ID,
    PLANTILLA_ID,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    ES_COPIA
)
VALUES
(
    DB_COMUNICACION.SEQ_INFO_ALIAS_PLANTILLA.NEXTVAL,
    (
        SELECT ID_ALIAS
        FROM DB_COMUNICACION.ADMI_ALIAS
        WHERE VALOR      = 'informaticos@netlife.net.ec'
        AND ESTADO       IN ('Activo','Modificado')
        AND EMPRESA_COD  = '18'
    ),
    (
        SELECT ID_PLANTILLA
        FROM DB_COMUNICACION.ADMI_PLANTILLA
        WHERE CODIGO = 'PROMBW_DET'
        AND ESTADO   = 'Activo'
    ),
    'Activo',
    SYSDATE,
    'facaicedo',
    'NO'
);
INSERT INTO DB_COMUNICACION.INFO_ALIAS_PLANTILLA
(
    ID_ALIAS_PLANTILLA,
    ALIAS_ID,
    PLANTILLA_ID,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    ES_COPIA
)
VALUES
(
    DB_COMUNICACION.SEQ_INFO_ALIAS_PLANTILLA.NEXTVAL,
    (
        SELECT ID_ALIAS
        FROM DB_COMUNICACION.ADMI_ALIAS
        WHERE VALOR      = 'marketing@netlife.net.ec'
        AND ESTADO       IN ('Activo','Modificado')
        AND EMPRESA_COD  = '18'
    ),
    (
        SELECT ID_PLANTILLA
        FROM DB_COMUNICACION.ADMI_PLANTILLA
        WHERE CODIGO = 'PROMBW_DET'
        AND ESTADO   = 'Activo'
    ),
    'Activo',
    SYSDATE,
    'facaicedo',
    'NO'
);
INSERT INTO DB_COMUNICACION.INFO_ALIAS_PLANTILLA
(
    ID_ALIAS_PLANTILLA,
    ALIAS_ID,
    PLANTILLA_ID,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    ES_COPIA
)
VALUES
(
    DB_COMUNICACION.SEQ_INFO_ALIAS_PLANTILLA.NEXTVAL,
    (
        SELECT ID_ALIAS
        FROM DB_COMUNICACION.ADMI_ALIAS
        WHERE VALOR      = 'red_acceso@netlife.net.ec'
        AND ESTADO       IN ('Activo','Modificado')
        AND EMPRESA_COD  = '18'
    ),
    (
        SELECT ID_PLANTILLA
        FROM DB_COMUNICACION.ADMI_PLANTILLA
        WHERE CODIGO = 'PROMBW_DET'
        AND ESTADO   = 'Activo'
    ),
    'Activo',
    SYSDATE,
    'facaicedo',
    'NO'
);
--insert del alias para el reporte de las promociones de ancho de banda
INSERT INTO DB_COMUNICACION.INFO_ALIAS_PLANTILLA
(
    ID_ALIAS_PLANTILLA,
    ALIAS_ID,
    PLANTILLA_ID,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    ES_COPIA
)
VALUES
(
    DB_COMUNICACION.SEQ_INFO_ALIAS_PLANTILLA.NEXTVAL,
    (
        SELECT ID_ALIAS
        FROM DB_COMUNICACION.ADMI_ALIAS
        WHERE VALOR      = 'informaticos@netlife.net.ec'
        AND ESTADO       IN ('Activo','Modificado')
        AND EMPRESA_COD  = '18'
    ),
    (
        SELECT ID_PLANTILLA
        FROM DB_COMUNICACION.ADMI_PLANTILLA
        WHERE CODIGO = 'PROM_BW_REP'
        AND ESTADO   = 'Activo'
    ),
    'Activo',
    SYSDATE,
    'facaicedo',
    'NO'
);
INSERT INTO DB_COMUNICACION.INFO_ALIAS_PLANTILLA
(
    ID_ALIAS_PLANTILLA,
    ALIAS_ID,
    PLANTILLA_ID,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    ES_COPIA
)
VALUES
(
    DB_COMUNICACION.SEQ_INFO_ALIAS_PLANTILLA.NEXTVAL,
    (
        SELECT ID_ALIAS
        FROM DB_COMUNICACION.ADMI_ALIAS
        WHERE VALOR      = 'marketing@netlife.net.ec'
        AND ESTADO       IN ('Activo','Modificado')
        AND EMPRESA_COD  = '18'
    ),
    (
        SELECT ID_PLANTILLA
        FROM DB_COMUNICACION.ADMI_PLANTILLA
        WHERE CODIGO = 'PROM_BW_REP'
        AND ESTADO   = 'Activo'
    ),
    'Activo',
    SYSDATE,
    'facaicedo',
    'NO'
);
INSERT INTO DB_COMUNICACION.INFO_ALIAS_PLANTILLA
(
    ID_ALIAS_PLANTILLA,
    ALIAS_ID,
    PLANTILLA_ID,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    ES_COPIA
)
VALUES
(
    DB_COMUNICACION.SEQ_INFO_ALIAS_PLANTILLA.NEXTVAL,
    (
        SELECT ID_ALIAS
        FROM DB_COMUNICACION.ADMI_ALIAS
        WHERE VALOR      = 'red_acceso@netlife.net.ec'
        AND ESTADO       IN ('Activo','Modificado')
        AND EMPRESA_COD  = '18'
    ),
    (
        SELECT ID_PLANTILLA
        FROM DB_COMUNICACION.ADMI_PLANTILLA
        WHERE CODIGO = 'PROM_BW_REP'
        AND ESTADO   = 'Activo'
    ),
    'Activo',
    SYSDATE,
    'facaicedo',
    'NO'
);

--cabecera de parametros de las promociones de ancho de banda
INSERT INTO db_general.admi_parametro_cab
(
    id_parametro,
    nombre_parametro,
    descripcion,
    modulo,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion
) 
VALUES
(
    db_general.seq_admi_parametro_cab.nextval,
    'PARAMETROS_PROMOCIONES_MASIVAS_BW',
    'PARAMETROS_PROMOCIONES_MASIVAS_BW',
    'COMERCIAL',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1'
);

--detalles de parametros de las promociones de ancho de banda
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
        'DATOS',
        'PROM_BW',
        'NUEVO',
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
        'DATOS WS',
        'http://fa-middleware.netlife.net.ec/ws/process',
        'SI',
        'SI',
        'SI',
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

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
        'RANGO_MINUTOS',
        '25',
        '65',
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

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
        OBSERVACION,
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
        'DATOS',
        'APLICAR_PROMOCIONES',
        'Se inicio la ejecución de la promoción.',
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

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
        VALOR3,
        VALOR4,
        VALOR5,
        VALOR6,
        VALOR7,
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
        'ESTADOS',
        'Programado',
        'Activo',
        'Pendiente',
        'Procesamiento',
        'Activo',
        'Procesamiento',
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

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
        'RANGO_MINUTOS',
        '25',
        '35',
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

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
        OBSERVACION,
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
        'DATOS',
        'QUITAR_PROMOCIONES',
        'Se ejecuta la finalización de la promoción de ancho de banda.',
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

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
        VALOR3,
        VALOR4,
        VALOR5,
        VALOR6,
        VALOR7,
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
        'ESTADOS',
        'Activo',
        'Activo',
        'Activo',
        'Finalizando',
        'Activo',
        'Finalizando',
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

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
        VALOR3,
        VALOR4,
        VALOR5,
        VALOR6,
        VALOR7,
        OBSERVACION,
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
        'APLICAR_PROMOCIONES',
        'Procesamiento',
        'Activo',
        'Procesamiento',
        'Activo',
        'Activo',
        'Activo',
        'ErrorRda',
        'Se proceso la promoción de ancho de banda.',
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

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
        VALOR3,
        VALOR4,
        VALOR5,
        VALOR6,
        VALOR7,
        OBSERVACION,
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
        'DETENER_PROMOCIONES',
        'Finalizando',
        'Activo',
        'Finalizando',
        'Inactivo',
        'Finalizado',
        'Baja',
        'ErrorRda',
        'Se finalizo la promoción de ancho de banda.',
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
        'CORREO',
        'APLICAR_PROMOCIONES',
        'notificaciones_telcos@telconet.ec',
        'PROMBW_PROC',
        'Promoción por ancho de banda código :ID_PROMOCION (Activación)',
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
        'CORREO',
        'DETENER_PROMOCIONES',
        'notificaciones_telcos@telconet.ec',
        'PROMBW_DET',
        'Promoción por ancho de banda código :ID_PROMOCION (Inactivación)',
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

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
        'REPORTE_BW',
        'DATOS',
        'PROM_BW',
        'notificaciones_telcos@telconet.ec',
        'PROM_BW_REP',
        'ErrorRda',
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

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
        VALOR3,
        VALOR4,
        VALOR5,
        VALOR6,
        VALOR7,
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
        'REPORTE_BW',
        'APLICAR',
        'Procesamiento',
        'Activo',
        'Activo',
        'Reporte: Promoción por ancho de banda código :ID_PROMOCION (Activación)',
        '55',
        '95',
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

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
        VALOR3,
        VALOR4,
        VALOR5,
        VALOR6,
        VALOR7,
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
        'REPORTE_BW',
        'QUITAR',
        'Activo',
        'Inactivo',
        'Baja',
        'Reporte: Promoción por ancho de banda código :ID_PROMOCION (Inactivación)',
        '55',
        '95',
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
            WHERE NOMBRE_PARAMETRO = 'PARAMETROS_PROMOCIONES_MASIVAS_BW'
            AND ESTADO = 'Activo'
        ),
        'MARCAS_PERMITIDAS',
        'HUAWEI',
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
            WHERE NOMBRE_PARAMETRO = 'PARAMETROS_PROMOCIONES_MASIVAS_BW'
            AND ESTADO = 'Activo'
        ),
        'MARCAS_PERMITIDAS',
        'ZTE',
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
            WHERE NOMBRE_PARAMETRO = 'PARAMETROS_PROMOCIONES_MASIVAS_BW'
            AND ESTADO = 'Activo'
        ),
        'TIPOS_ELEMENTOS_PERMITIDOS',
        'OLT',
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
            WHERE NOMBRE_PARAMETRO = 'PARAMETROS_PROMOCIONES_MASIVAS_BW'
            AND ESTADO = 'Activo'
        ),
        'TIPOS_ELEMENTOS_PERMITIDOS',
        'OLT-HUAWEI',
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

COMMIT;
/
