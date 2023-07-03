
SET DEFINE OFF;
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
    'REP-FOT-TN-INS',
    'COMERCIAL',
    TO_CLOB('<!DOCTYPE html>
        <html>
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <style>
            figure { background-color: #ffffff; max-width: 510pt; } td, th { border: 1px solid rgb(24, 24, 24); text-align: left; max-width: 510pt; } table { max-width: 510pt; height: 100vh; width: 100vw; position: absolute; margin: auto; top: 0; left: 0; } titulos { text-align: center; font-size: 22px; color: rgb(5, 8, 17); } subTitulos { text-align: center; font-size: 20px; color: rgb(5, 8, 17); } contenido { text-align: center; font-size: 15px; color: black; } contenidoTabla { text-align: center; font-size: 18px; color: rgb(42, 42, 42); } firma { margin: 1px; border: 1px solid rgba(0, 0, 0, .1); height: 150px; width: 150px; } figcaption { padding: 1em 0; } th, td { padding: 10px; }
        </style>
        </head>
        <figure class="figure" colspan="100%">
        <table class="table">
            <tbody>
            <tr>
                <td style="height:35pt" colspan="2">
                <center> <img src="{{imagenCabecera}}"> </center>
                </td>
                <td colspan="3">
                <titulos>
                    <center>REPORTE FOTOGR&Aacute;FICO</center>
                </titulos>
                </td>
                <td colspan="3">
                <subTitulos> <strong>CODIGO: FOR OPU 05&nbsp; <br>Ver: 3 (08/06/2021) </subTitulos> </td> </tr> <tr> <td style="height:32pt;" colspan="7"> <h2 class="titulos"> <subtitulos> <strong>INFORME DE TRABAJOS DIARIOS</strong>            <br> <strong>Datos</strong> </subtitulos>
                </h2>
                </td>
            </tr>
            <tr>
                <td class="subTitulos" style="height:12,75pt;" colspan="3">
                <contenido> <strong>FECHA</strong>: </contenido>
                </td>
                <td class="subTitulos" colspan="5">
                <contenido>{{ fecha }}</contenido>
                </td>
            </tr>
            <tr>
                <td style="height:12,75pt; width:195,00pt;" colspan="3">
                <contenido> <strong>CLIENTE</strong>: </contenido>
                </td> {% if datosCliente.NOMBRES|length > 1 %}
                <td colspan="5">
                <contenido>{{ datosCliente.NOMBRES }}</contenido>
                </td> {% else %}
                <td colspan="5">
                <contenido>{{ datosCliente.RAZON_SOCIAL }}</contenido>
                </td> {% endif %} </tr>
            <tr>
                <td style="height:12,75pt;width:195,00pt;" colspan="3">
                <contenido> <strong>LOGIN</strong> </contenido>: </td>
                <td colspan="5">
                <contenido>{{ datosCliente[''LOGIN''] }}</contenido>
                </td>
            </tr>
            <tr>
                <td style="height:12,75pt;width:195,00pt;" colspan="3">
                <contenido> <strong>DIRECCI&Oacute;N</strong>: </contenido>
                </td>
                <td colspan="5">
                <contenido>{{ datosCliente[''DIRECCION''] }}</contenido>
                </td>
            </tr>
            <tr>
                <td style="height:12,75pt;width:195,00pt;" colspan="3">
                <contenido> <strong>COORDENADAS</strong>: </contenido>
                </td>
                <td colspan="5">
                <contenido>{{ datosCliente[''LONGITUD''] }},{{ datosCliente[''LATITUD''] }}</contenido>
                </td>
            </tr>
            <tr>
                <td style="height:12,75pt;width:195,00pt;" colspan="3">
                <contenido> <strong>NOMBRE DEL CONTACTO</strong>: </contenido>
                </td>
                <td colspan="5">
                <contenido> {% if contactoCliente %} {{ contactoCliente[''NOMBRE_CONTACTO''] }} {% else %} NA {% endif %} </contenido>
                </td>
            </tr>') 
        || TO_CLOB('
            <tr>
                <td style="height:12,75pt;width:195,00pt;" colspan="3">
                <contenido> <strong>FORMA DE CONTACTO</strong>: </contenido>
                </td>
                <td colspan="5">
                <contenido> {% for contacto in formaContactoCliente[''registros''] %} <b>{{ contacto[''descripcionFormaContacto''] }}:</b>{{ contacto[''valor''] }} {% endfor
                    %} </contenido>
                </td>
            </tr>
            <tr>
                <td style="height:13,50pt;width:379,50pt;" colspan="7">
                <h3>
                    <titulos>FOTOS</titulos>
                </h3>
                </td>
            </tr> {% set limiteColumna = 0 %} {% for foto in arrayEtiquetasFotos %} {% if limiteColumna == 0 %}
            <tr> {% endif %} {% set limiteColumna = limiteColumna + 1 %}
                <td colspan="4">
                <center>
                    <figcaption>{{foto[''nombre'']}}</figcaption> <img height="250px" width="250px" src="{{foto[''ubicacion'']}}" /> </center>
                </td> {% if limiteColumna == 2 %} </tr> {% set limiteColumna = 0 %} {% endif %} {% endfor %}
            <tr>
                <td colspan="7">
                <contenidoTabla>
                    <p>Esta reporte fotogr&aacute;fico es evidencia de las fotos de la entrega del servicio contratado.</p>
                </contenidoTabla>
                </td>
            </tr>
            <tr>
                <td colspan="4">
                <center> <img height="250px" width="250px" src="{{firmaEmpleado}}" />
                    <figcaption>T&eacute;cnico responsable</figcaption>
                </center>
                </td>
                <td colspan="4">
                <center> <img height="250px" width="250px" src="{{firmaCliente}}" />
                    <figcaption>Firma Cliente</figcaption>
                </center>
                </td>
            </tr>
            <tr>
                <td colspan="100%">
                <contenidoTabla>
                    <p>Grupo Telconet-Documento confidencial. Prohibida su distribuci&oacute;n sin previa autorizaci&oacute;n</p>
                </contenidoTabla>
                </td>
            </tr>
            </tbody>
        </table>
        </figure>
    </html>'),
    'Activo',
    SYSDATE,
    'facaicedo'
);
SET DEFINE ON;

--cabecera de parametros de los reportes fotograficos
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
    'PARAMETROS_REPORTE_FOTOGRAFICO',
    'PARAMETROS_REPORTE_FOTOGRAFICO',
    'TECNICO',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1'
);
--detalles de parametros de los id de los produtcos permitidos en el reporte fotografico
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
            WHERE NOMBRE_PARAMETRO = 'PARAMETROS_REPORTE_FOTOGRAFICO'
            AND ESTADO = 'Activo'
        ),
        'Id del producto permitido en el reporte fotográfico',
        'PRODUCTO_PERMITIDO',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE DESCRIPCION_PRODUCTO = 'MOBILE BUS' AND EMPRESA_COD = '10' AND ESTADO = 'Activo' ),
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
--detalles de parametros de los reportes fotograficos
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
            WHERE NOMBRE_PARAMETRO = 'PARAMETROS_REPORTE_FOTOGRAFICO'
            AND ESTADO = 'Activo'
        ),
        'Código de la plantilla del reporte fotográfico',
        'CODIGO_PLANTILLA',
        'REP-FOT-TN-INS',
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
--detalles de parametros de los reportes fotograficos
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
            WHERE NOMBRE_PARAMETRO = 'PARAMETROS_REPORTE_FOTOGRAFICO'
            AND ESTADO = 'Activo'
        ),
        'Código de la plantilla del reporte fotográfico',
        'REPORTE_IMAGENES',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE DESCRIPCION_PRODUCTO = 'MOBILE BUS' AND EMPRESA_COD = '10' AND ESTADO = 'Activo' ),
        'Placa-Conductor(D)',
        'Foto Frontal del vehículo con el Conductor',
        '1',
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
            WHERE NOMBRE_PARAMETRO = 'PARAMETROS_REPORTE_FOTOGRAFICO'
            AND ESTADO = 'Activo'
        ),
        'Código de la plantilla del reporte fotográfico',
        'REPORTE_IMAGENES',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE DESCRIPCION_PRODUCTO = 'MOBILE BUS' AND EMPRESA_COD = '10' AND ESTADO = 'Activo' ),
        'Mdvr',
        'Foto del Grabador móvil',
        '2',
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
            WHERE NOMBRE_PARAMETRO = 'PARAMETROS_REPORTE_FOTOGRAFICO'
            AND ESTADO = 'Activo'
        ),
        'Código de la plantilla del reporte fotográfico',
        'REPORTE_IMAGENES',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE DESCRIPCION_PRODUCTO = 'MOBILE BUS' AND EMPRESA_COD = '10' AND ESTADO = 'Activo' ),
        'Frontal_c1',
        'Foto Cámara Frontal',
        '3',
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
            WHERE NOMBRE_PARAMETRO = 'PARAMETROS_REPORTE_FOTOGRAFICO'
            AND ESTADO = 'Activo'
        ),
        'Código de la plantilla del reporte fotográfico',
        'REPORTE_IMAGENES',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE DESCRIPCION_PRODUCTO = 'MOBILE BUS' AND EMPRESA_COD = '10' AND ESTADO = 'Activo' ),
        'Conductor_c2',
        'Foto Cámara Interna Chofer',
        '4',
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
            WHERE NOMBRE_PARAMETRO = 'PARAMETROS_REPORTE_FOTOGRAFICO'
            AND ESTADO = 'Activo'
        ),
        'Código de la plantilla del reporte fotográfico',
        'REPORTE_IMAGENES',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE DESCRIPCION_PRODUCTO = 'MOBILE BUS' AND EMPRESA_COD = '10' AND ESTADO = 'Activo' ),
        'Pasajeros_c3',
        'Foto Cámara Interna Pasajeros',
        '5',
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
            WHERE NOMBRE_PARAMETRO = 'PARAMETROS_REPORTE_FOTOGRAFICO'
            AND ESTADO = 'Activo'
        ),
        'Código de la plantilla del reporte fotográfico',
        'REPORTE_IMAGENES',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE DESCRIPCION_PRODUCTO = 'MOBILE BUS' AND EMPRESA_COD = '10' AND ESTADO = 'Activo' ),
        'Posterior_c4',
        'Foto Cámara Posterior',
        '6',
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
            WHERE NOMBRE_PARAMETRO = 'PARAMETROS_REPORTE_FOTOGRAFICO'
            AND ESTADO = 'Activo'
        ),
        'Código de la plantilla del reporte fotográfico',
        'REPORTE_IMAGENES',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE DESCRIPCION_PRODUCTO = 'MOBILE BUS' AND EMPRESA_COD = '10' AND ESTADO = 'Activo' ),
        'Pulsador',
        'Foto Botón antipánico',
        '7',
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
            WHERE NOMBRE_PARAMETRO = 'PARAMETROS_REPORTE_FOTOGRAFICO'
            AND ESTADO = 'Activo'
        ),
        'Código de la plantilla del reporte fotográfico',
        'REPORTE_IMAGENES',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE DESCRIPCION_PRODUCTO = 'MOBILE BUS' AND EMPRESA_COD = '10' AND ESTADO = 'Activo' ),
        'Gps',
        'Foto Antena GPS',
        '8',
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
            WHERE NOMBRE_PARAMETRO = 'PARAMETROS_REPORTE_FOTOGRAFICO'
            AND ESTADO = 'Activo'
        ),
        'Código de la plantilla del reporte fotográfico',
        'REPORTE_IMAGENES',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE DESCRIPCION_PRODUCTO = 'MOBILE BUS' AND EMPRESA_COD = '10' AND ESTADO = 'Activo' ),
        '4G',
        'Foto Antena 3G-4G',
        '9',
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
            WHERE NOMBRE_PARAMETRO = 'PARAMETROS_REPORTE_FOTOGRAFICO'
            AND ESTADO = 'Activo'
        ),
        'Código de la plantilla del reporte fotográfico',
        'REPORTE_IMAGENES',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE DESCRIPCION_PRODUCTO = 'MOBILE BUS' AND EMPRESA_COD = '10' AND ESTADO = 'Activo' ),
        'Wifi',
        'Foto Antena Wifi',
        '10',
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
            WHERE NOMBRE_PARAMETRO = 'PARAMETROS_REPORTE_FOTOGRAFICO'
            AND ESTADO = 'Activo'
        ),
        'Código de la plantilla del reporte fotográfico',
        'REPORTE_IMAGENES',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE DESCRIPCION_PRODUCTO = 'MOBILE BUS' AND EMPRESA_COD = '10' AND ESTADO = 'Activo' ),
        'Monitoreo',
        'Captura del monitoreo activo',
        '11',
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
--detalle de parametro para mostrar o no mostrar el reporte fotográfico
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
            WHERE NOMBRE_PARAMETRO = 'PARAMETROS_REPORTE_FOTOGRAFICO'
            AND ESTADO = 'Activo'
        ),
        'Detalle de parámetro para mostrar o no mostrar el reporte fotográfico',
        'MOSTRAR_REPORTE_FOTOGRAFICO',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE DESCRIPCION_PRODUCTO = 'MOBILE BUS' AND EMPRESA_COD = '10' AND ESTADO = 'Activo' ),
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

--ingreso de la respuesta
INSERT INTO DB_COMUNICACION.ADMI_RESPUESTA (ID_RESPUESTA, RESPUESTA, ESTADO, FE_CREACION, USR_CREACION)
VALUES(DB_COMUNICACION.SEQ_ADMI_RESPUESTA.NEXTVAL,'MOBILE BUS', 'Activo', SYSDATE, 'facaicedo');

INSERT INTO DB_COMUNICACION.ADMI_PREGUNTA_RESPUESTA (ID_PREGUNTA_RESPUESTA, PREGUNTA_ID, RESPUESTA_ID, ESTADO, FE_CREACION, USR_CREACION)
VALUES(DB_COMUNICACION.SEQ_ADMI_PREGUNTA_RESPUESTA.NEXTVAL, 
	(SELECT ID_PREGUNTA FROM DB_COMUNICACION.ADMI_PREGUNTA WHERE PREGUNTA='Servicios Contratados'), 
	(SELECT ID_RESPUESTA FROM DB_COMUNICACION.ADMI_RESPUESTA WHERE RESPUESTA='MOBILE BUS'), 'Activo', SYSDATE, 'facaicedo');

-- INGRESO DE LA CABECERA DE PARAMETROS DE 'PRODUCTO_PLANTILLA_PREGUNTA'
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
        IP_CREACION
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
        'PRODUCTO_PLANTILLA_PREGUNTA',
        'Lista de los parámetros para los productos con preguntas dinámicas por plantillas.',
        'TECNICO',
        'MOBILE OPERACIONES',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);
--Ingresamos los detalle de parámetros para los productos con preguntas dinámicas por plantillas.
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
            WHERE NOMBRE_PARAMETRO = 'PRODUCTO_PLANTILLA_PREGUNTA'
            AND ESTADO = 'Activo'
        ),
        'Lista de detalles de parámetros para los productos con preguntas dinámicas por plantillas.',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE DESCRIPCION_PRODUCTO = 'MOBILE BUS' AND EMPRESA_COD = '10' AND ESTADO = 'Activo' ),
        ( SELECT ID_PLANTILLA FROM DB_COMUNICACION.ADMI_PLANTILLA WHERE CODIGO = 'ACT-ENT-TN-INS' ),
        ( SELECT ID_PREGUNTA FROM DB_COMUNICACION.ADMI_PREGUNTA WHERE PREGUNTA = 'Servicios Contratados' ),
        ( SELECT ID_RESPUESTA FROM DB_COMUNICACION.ADMI_RESPUESTA WHERE RESPUESTA='MOBILE BUS' ),
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

--cabecera de parametros del correo de acta por producto
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
    'CORREO_ACTA_SERVICIO_POR_PRODUCTO',
    'CORREO_ACTA_SERVICIO_POR_PRODUCTO',
    'TECNICO',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1'
);
--detalles de parametros del correo de acta por producto
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
            WHERE NOMBRE_PARAMETRO = 'CORREO_ACTA_SERVICIO_POR_PRODUCTO'
            AND ESTADO = 'Activo'
        ),
        'Detalles del parámetro del correo de acta por producto',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE DESCRIPCION_PRODUCTO = 'MOBILE BUS' AND EMPRESA_COD = '10' AND ESTADO = 'Activo' ),
        'INS-CLI-COR-TN',
        'TELCONET, confirma su requerimiento de Instalación/Activación de servicio. Adjunto Acta de Entrega de Mobile Bus.',
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

COMMIT;
/
