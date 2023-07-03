--app TmComercial
INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'PARAMETROS_ESTRUCTURA_RUTAS_TELCOS'
    ),
    'MAPEO DEL SOURCE CON EL NOMBRE DE LA APP TM COMERCIAL',
    'ec.telconet.telcos.mobile.comercial',
    'TmComercial',
    NULL,
    NULL,
    'Activo',
    'wgaibor',
    SYSDATE,
    '127.0.0.0', 
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
);
--
-- CREACIÓN DEL PARÁMETRO CAB  - NFS_PATH_RAIZ
--
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
    'NFS_PATH_RAIZ',
    'RUTA ORIGEN DEL NFS',
    'COMERCIAL',
    'Activo',
    'wgaibor',
    sysdate,
    '127.0.0.1'
);
--
--NFS1
--
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
    valor6,
    valor7
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'NFS_PATH_RAIZ'
            AND estado = 'Activo'
    ),
    'RUTA ORIGEN DEL NFS',
    'NFS',
    '1',
    NULL,
    NULL,
    'Activo',
    'wgaibor',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL
);
--
-- CREACIÓN DEL PARÁMETRO CAB  - NFS_UMBRAL_NOTIFICACIÓN
--
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
    'NFS_UMBRAL_NOTIFICACION',
    'DESCRIBE EL UMBRAL PARA NOTIFICAR LA POCA DISPONIBILIDAD DEL NFS',
    'COMERCIAL',
    'Activo',
    'wgaibor',
    sysdate,
    '127.0.0.1'
);
--
--UMBRAL PARA NOTIFICAR LA DISPONIBILIDAD DE SEGUIR CREANDO ARCHIVOS EN EL NFS
--
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
    valor6,
    valor7
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'NFS_UMBRAL_NOTIFICACION'
            AND estado = 'Activo'
    ),
    'DESCRIBE EL UMBRAL PARA NOTIFICAR LA POCA DISPONIBILIDAD DEL NFS',
    '20',
    '10',
    NULL,
    NULL,
    'Activo',
    'wgaibor',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL
);
--
-- CREACIÓN DEL PARÁMETRO CAB  - NFS_PLANTILLA_NOTIFICACIÓN
--
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
    'NFS_PLANTILLA_NOTIFICACION',
    'PLANTILLA DE NOTIFICACIÓN PARA EL ENVIO DE CORREO',
    'COMERCIAL',
    'Activo',
    'wgaibor',
    sysdate,
    '127.0.0.1'
);
--
--PLANTILLA DE NOTIFICACIÓN PARA EL ENVIO DE CORREO
--
INSERT
    INTO db_general.admi_parametro_det (
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
        valor6,
        valor7
    ) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'NFS_PLANTILLA_NOTIFICACION'
            AND estado = 'Activo'
    ),
    'PLANTILLA DE NOTIFICACIÓN PARA EL ENVIO DE CORREO',
    q'[<!DOCTYPE html>
<html>
   <head>
      <meta http-equiv=Content-Type content="text/html; charset=utf-8">
      <style type="text/css">
         .negrita {
         font-weight: bold;
         }
      </style>
   </head>
   <body topmargin="0" style="width:600px; padding: 20px;">
      <table border=0 cellspacing=0 cellpadding=0 style='width: 600px;'>
         <tr>
            <td align='center' style='background-color:#e5f2ff;border:1px solid #000000;border-color:#A9E2F3;'>
               <img src='http://images.telconet.net/logo_telconet.png'/>
            </td>
         </tr>
         <tr>
            <td>
               <table border='0' cellspacing='0' cellpadding='0' align='center'
                  style='border-collapse:collapse;border:none; width:550px'>
                  <tr>
                     <td valign='top'>
                        <p class='negrita' style='margin-right:30.8pt;text-align:justify;mso-height-rule:exactly'>
                           <br/>Estimado/a,
                        </p>
                        <p style='margin-right:0.8pt;text-align:justify;mso-height-rule:exactly'><span
                           style='font-size:10.0pt;font-family:"Arial","sans-serif";mso-fareast-font-family:
                           "Times New Roman"'>
                           {{PARRAFO_UNO}}
                           </span>
                        </p>
                        <p style='margin-right:0.8pt;text-align:justify;mso-height-rule:exactly'><span
                           style='font-size:10.0pt;font-family:"Arial","sans-serif";mso-fareast-font-family:
                           "Times New Roman"'>
                           {{PARRAFO_DOS}}
                           </span>
                        </p>
                        <p class=MsoNormal style='text-align:justify;mso-element:frame;mso-element-frame-hspace:
                           7.05pt;mso-element-wrap:around;mso-element-anchor-horizontal:margin;
                           mso-element-top:-11.25pt;mso-height-rule:exactly'><span style='font-size:
                           10.0pt;font-family:"Arial","sans-serif";mso-fareast-font-family:"Times New Roman"'>Atentamente, <br>
                           </span><span style='font-size:10.0pt; font-family:"Arial","sans-serif"; font-weight: bold;'>Telconet
                           </span>
                        </p>
                     </td>
                  </tr>
               </table>
            </td>
         </tr>
      </table>
   </body>
</html>
]',
    'ALERTA DE ESPACIO EN NUEVO FILESERVER',
    'sistemas@telconet.ec',
    'jromero@telconet.ec,sistemas-devops@telconet.ec,wgaibor@telconet.ec',
    'Activo',
    'wgaibor',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL
);
--
-- INGRESAR LA RUTA DE DIRECTORIO DE UNA APP
--

INSERT INTO db_general.ADMI_GESTION_DIRECTORIOS
(ID_GESTION_DIRECTORIO,
CODIGO_APP,
CODIGO_PATH,
APLICACION,
PAIS,
EMPRESA,
MODULO,
SUBMODULO,
ESTADO,
FE_CREACION,
USR_CREACION)
VALUES
(
db_general.SEQ_ADMI_GESTION_DIRECTORIOS.nextval,
1,
1,
'TmComercial',
'593',
'TN',
'Comercial',
'ContratoDigital',
'Activo',
sysdate,
'wgaibor');
--
-- INGRESAR LA RUTA DE DIRECTORIO DE UNA APP
--
INSERT INTO db_general.ADMI_GESTION_DIRECTORIOS
(ID_GESTION_DIRECTORIO,
CODIGO_APP,
CODIGO_PATH,
APLICACION,
PAIS,
EMPRESA,
MODULO,
SUBMODULO,
ESTADO,
FE_CREACION,
USR_CREACION)
VALUES
(
db_general.SEQ_ADMI_GESTION_DIRECTORIOS.nextval,
1,
2,
'TmComercial',
'593',
'MD',
'Comercial',
'ContratoDigital',
'Activo',
sysdate,
'wgaibor');

COMMIT;
/
