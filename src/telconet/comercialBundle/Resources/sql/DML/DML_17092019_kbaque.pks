/**
 *
 * Se realiza la creación de parametros, caracteristicas, plantillas para el proyecto GPON.
 * @author Kevin Baque Puya <kbaque@telconet.ec>
 * @version 1.0 17-09-2019
 */

--Registramos los tipos de Red con los que podrá elegir al momento de crear un servicio.
--Cabecera
INSERT INTO db_general.admi_parametro_cab (
    id_parametro,
    nombre_parametro,
    descripcion,
    modulo,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion
) VALUES (
    db_general.seq_admi_parametro_cab.nextval,
    'PROD_TIPO_RED',
    'PROD_TIPO_RED',
    'COMERCIAL',
    'Activo',
    'kbaque',
    SYSDATE,
    '127.0.0.1'
);
--detalle
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'PROD_TIPO_RED'
    ),
    'PROD_TIPO_RED',
    'GPON',
    'GPON',
    'NO',
    'Activo',
    'kbaque',
    SYSDATE,
    '127.0.0.1',
    10
);
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'PROD_TIPO_RED'
    ),
    'PROD_TIPO_RED',
    'MPLS',
    'MPLS',
    'SI',
    'Activo',
    'kbaque',
    SYSDATE,
    '127.0.0.1',
    10
);
--Registramos la cabecera de parametros para el proyecto.
INSERT INTO db_general.admi_parametro_cab (
    id_parametro,
    nombre_parametro,
    descripcion,
    modulo,
    proceso,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion
) VALUES (
    db_general.seq_admi_parametro_cab.nextval,
    'NUEVA_RED_GPON_TN',
    'Parametros utilizados para el proyecto GPON',
    'COMERCIAL',
    'GPON',
    'Activo',
    'kbaque',
    SYSDATE,
    '127.0.0.1'
);
--Registramos la relación entre el producto y la caracteristica.
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    valor4,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    valor5,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'NUEVA_RED_GPON_TN'
    ),
    'PARAMETRO PARA DEFINIR EL TIPO DE RED GPON DE UN PRODUCTO',
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet Dedicado'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    ),
    (
        SELECT
            descripcion_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet Dedicado'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    ),
    'GPON',
    'S',
    'Activo',
    'kbaque',
    SYSDATE,
    '127.0.0.1',
    'RELACION_PRODUCTO_CARACTERISTICA',
    10
);
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    valor4,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    valor5,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'NUEVA_RED_GPON_TN'
    ),
    'PARAMETRO PARA DEFINIR EL TIPO DE RED GPON DE UN PRODUCTO',
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'L3MPLS'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    ),
    (
        SELECT
            descripcion_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'L3MPLS'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    ),
    'GPON',
    'S',
    'Activo',
    'kbaque',
    SYSDATE,
    '127.0.0.1',
    'RELACION_PRODUCTO_CARACTERISTICA',
    10
);
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    valor4,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    valor5,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'NUEVA_RED_GPON_TN'
    ),
    'PARAMETRO PARA DEFINIR EL TIPO DE RED GPON DE UN PRODUCTO',
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet MPLS'
            AND codigo_producto = 'SISCTN'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Inactivo'
    ),
    (
        SELECT
            descripcion_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet MPLS'
            AND codigo_producto = 'SISCTN'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Inactivo'
    ),
    'GPON',
    'S',
    'Activo',
    'kbaque',
    SYSDATE,
    '127.0.0.1',
    'RELACION_PRODUCTO_CARACTERISTICA',
    10
);
--Registramos el tipo de milla FTTX para los productos con tipo de red GPON.
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    valor4,
    valor5,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'NUEVA_RED_GPON_TN'
    ),
    'UM FTTX',
    (
        SELECT
            nombre_tecnico
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet Dedicado'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    ),
    'FTTx',
    'MD',
    '18',
    'ULTIMA_MILLA_GPON_TN',
    'Activo',
    'kbaque',
    SYSDATE,
    '127.0.0.0',
    '10'
);
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    valor4,
    valor5,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'NUEVA_RED_GPON_TN'
    ),
    'UM FTTX',
    (
        SELECT
            nombre_tecnico
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'L3MPLS'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    ),
    'FTTx',
    'MD',
    '18',
    'ULTIMA_MILLA_GPON_TN',
    'Activo',
    'kbaque',
    SYSDATE,
    '127.0.0.0',
    '10'
);
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    valor4,
    valor5,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'NUEVA_RED_GPON_TN'
    ),
    'UM FTTX',
    (
        SELECT
            nombre_tecnico
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet MPLS'
            AND codigo_producto = 'SISCTN'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Inactivo'
    ),
    'FTTx',
    'MD',
    '18',
    'ULTIMA_MILLA_GPON_TN',
    'Activo',
    'kbaque',
    SYSDATE,
    '127.0.0.0',
    '10'
);
--Se parametriza las ciudades disponibles para elegir el tipo de red al momento de crear un servicio
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    valor4,
    valor5,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'NUEVA_RED_GPON_TN'
    ),
    'PARAMETRO PARA DEFINIR LAS CIUDADES DISPONIBLES',
    (
        SELECT
            id_jurisdiccion
        FROM
            db_infraestructura.admi_jurisdiccion
        WHERE
            nombre_jurisdiccion = 'TELCONET - Guayaquil'
    ),
    (
        SELECT
            nombre_jurisdiccion
        FROM
            db_infraestructura.admi_jurisdiccion
        WHERE
            nombre_jurisdiccion = 'TELCONET - Guayaquil'
    ),
    'GPON',
    'S',
    'CIUDADES_DISPONIBLES',
    'Activo',
    'kbaque',
    SYSDATE,
    '127.0.0.0',
    '10'
);
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    valor4,
    valor5,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'NUEVA_RED_GPON_TN'
    ),
    'PARAMETRO PARA DEFINIR LAS CIUDADES DISPONIBLES',
    (
        SELECT
            id_jurisdiccion
        FROM
            db_infraestructura.admi_jurisdiccion
        WHERE
            nombre_jurisdiccion = 'TELCONET - Quito'
    ),
    (
        SELECT
            nombre_jurisdiccion
        FROM
            db_infraestructura.admi_jurisdiccion
        WHERE
            nombre_jurisdiccion = 'TELCONET - Quito'
    ),
    'GPON',
    'S',
    'CIUDADES_DISPONIBLES',
    'Activo',
    'kbaque',
    SYSDATE,
    '127.0.0.0',
    '10'
);
--Registramos el ancho de banda disponible.
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    valor4,
    valor5,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'NUEVA_RED_GPON_TN'
    ),
    'PARAMETRO PARA DEFINIR LA CANT. DE ANCHO DE BANDA EN MB',
    102400,
    'MB',
    'GPON',
    'S',
    'ANCHO_BANDA_DISPONIBLE',
    'Activo',
    'kbaque',
    SYSDATE,
    '127.0.0.0',
    '10'
);
--Se ingresa la url donde se podrá aprobar la solicitud del servicio con tipo de red MPLS
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    valor4,
    valor5,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'NUEVA_RED_GPON_TN'
    ),
    'PARAMETRO PARA DEFINIR LA URL DE LA SOLICITUD',
    'URL',
    'https://telcos.telconet.ec/comercial/solicitud/solicitudes/',
    'GPON',
    'S',
    'URL_SOLICITUD',
    'Activo',
    'kbaque',
    SYSDATE,
    '127.0.0.0',
    '10'
);
--Registramos el catalago de vlans para el olt.
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    valor5,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'NUEVA_RED_GPON_TN'
    ),
    'CATALOGO DE VLANS DE RED GPON DE UN OLT',
    600,
    898,
    'Activo',
    'kbaque',
    SYSDATE,
    '127.0.0.1',
    'CATALOGO_VLANS_DATOS',
    10
);
--Se crea la caracteristica 'Tipo de Red' para los productos.
INSERT INTO db_comercial.admi_caracteristica (
    id_caracteristica,
    descripcion_caracteristica,
    tipo_ingreso,
    fe_creacion,
    usr_creacion,
    tipo,
    estado
) VALUES (
    db_comercial.seq_admi_caracteristica.nextval,
    'TIPO_RED',
    'T',
    SYSDATE,
    'kbaque',
    'TECNICO',
    'Activo'
);
--Se relaciona la caracteristica Tipo de Red con el producto.
INSERT INTO db_comercial.admi_producto_caracteristica VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet Dedicado'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'TIPO_RED'
    ),
    SYSDATE,
    NULL,
    'kbaque',
    NULL,
    'Activo',
    'SI'
);
INSERT INTO db_comercial.admi_producto_caracteristica VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet MPLS'
            AND codigo_producto = 'SISCTN'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Inactivo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'TIPO_RED'
    ),
    SYSDATE,
    NULL,
    'kbaque',
    NULL,
    'Activo',
    'SI'
);
INSERT INTO db_comercial.admi_producto_caracteristica VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'L3MPLS'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'TIPO_RED'
    ),
    SYSDATE,
    NULL,
    'kbaque',
    NULL,
    'Activo',
    'SI'
);

--Registramos un nuevo tipo de solicitud para los servicios que necesitan aprobación por ser tipo de Red MPLS
INSERT INTO db_comercial.admi_tipo_solicitud (
    id_tipo_solicitud,
    descripcion_solicitud,
    fe_creacion,
    usr_creacion,
    estado
) VALUES (
    db_comercial.seq_admi_tipo_solicitud.nextval,
    'SOLICITUD APROBACION SERVICIO TIPO RED MPLS',
    SYSDATE,
    'kbaque',
    'Activo'
);
INSERT INTO db_general.admi_motivo (
    id_motivo,
    nombre_motivo,
    estado,
    usr_creacion,
    fe_creacion,
    usr_ult_mod,
    fe_ult_mod
) VALUES (
    db_general.seq_admi_motivo.nextval,
    'SOLICITUD AL CREAR SERVICIO CON TIPO DE RED MPLS',
    'Activo',
    'kbaque',
    SYSDATE,
    'kbaque',
    SYSDATE
);

--Se ingresa la plantilla para el envío de correo notificando la aprobación o rechazo de la solicitud
INSERT INTO db_comunicacion.admi_plantilla (
    id_plantilla,
    nombre_plantilla,
    codigo,
    modulo,
    estado,
    fe_creacion,
    usr_creacion,
    plantilla
) VALUES (
    db_comunicacion.seq_admi_plantilla.NEXTVAL,
    'Notificación para los gerentes, subgerentes, asesores y asistentes al aprobar o rechazar un servicio',
    'AprbRchzSolMpls',
    'COMERCIAL',
    'Activo',
    CURRENT_TIMESTAMP,
    'kbaque',
    TO_CLOB(
    '
    <html>
        <head>
        <meta http-equiv=Content-Type content="text/html; charset=UTF-8">
    </head>
    <body>
        <table align="center" width="100%" cellspacing="0" cellpadding="5">
            <tr>
                <td align="center" style="border:1px solid #6699CC;background-color:#E5F2FF;">
                    <img alt=""  src="http://images.telconet.net/others/telcos/logo.png"/>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid #6699CC;">
                    <table width="100%" cellspacing="0" cellpadding="5">
                        <tr>
                            <td colspan="2">Estimados,</td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                Por el presente se notifica {{ accionMail }} de la solicitud correspondiente al servicio detallado a continuación:
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <hr />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align: center;">
                                <strong>Datos del Cliente</strong>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <hr />
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Cliente:</strong></td>
                            <td>{{ cliente }}</td>
                        </tr>
                        <tr>
                            <td><strong>Login:</strong></td>
                            <td>{{ loginPuntoCliente }}</td>
                        </tr>
                        <tr>
                            <td><strong>Jurisdicción:</strong></td>
                            <td>{{ nombreJurisdiccion }}</td>
                        </tr>
                        <tr>
                            <td><strong>Dirección:</strong></td>
                            <td>{{ direccionPuntoCliente }}</td>
                        </tr>
                        <tr>
                            <td><strong>Producto:</strong></td>
                            <td>{{ descripcionProducto }}</td>
                        </tr>
                        <tr>
                            <td><strong>Tipo de Red:</strong></td>
                            <td>{{ tipoRed }}</td>
                        </tr>
                        <tr>
                            <td><strong>Subgerente:</strong></td>
                            <td>{{ subgerente }}</td>
                        </tr>
                        <tr>
                            <td><strong>Vendedor:</strong></td>
                            <td>{{ vendedor }}</td>
                        </tr>
                        <tr>
                            <td><strong>Tipo de Orden:</strong></td>
                            <td>{{ tipoOrden }}</td>
                        </tr>')|| TO_CLOB('
                        <tr>
                            <td><strong>Estado del Servicio:</strong></td>
                            <td><strong>{{ estadoServicio }}</strong></td>
                        </tr>
                        <tr>
                            <td><strong>Fecha de Creación del Servicio:</strong></td>
                            <td>{{ fechaCreacionServicio }}</td>
                        </tr>
                        <tr>
                            <td><strong>Tipo de Solicitud:</strong></td>
                            <td>{{ tipoSolicitud }}</td>
                        </tr>
                        <tr>
                            <td><strong>Estado de Solicitud:</strong></td>
                            <td><strong>{{ estadoSolicitud }}</strong></td>
                        </tr>
                        {% if observacion!='''' %}
                        <tr>
                            <td><strong>Observación:</strong></td>
                            <td>{{ observacion | raw }}</td>
                        </tr>
                    {% endif %}
                        <tr>
                            <td><strong>Solicitud {{ accionUsuario }} por:</strong></td>
                            <td><strong>{{ nombreUsuarioGestion }}</strong></td>
                        </tr>
                        <td colspan="2"><br/></td>
                    </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td></td>
            </tr>
            <tr>
                {% if prefijoEmpresa == ''TN'' %}
                    <td><strong><font size="2" face="Tahoma">Telconet S.A.</font></strong></p>
                {% endif %}
                </td>
            </tr>
        </table>
    </body>
    </html>
    ')
);
COMMIT;
/
