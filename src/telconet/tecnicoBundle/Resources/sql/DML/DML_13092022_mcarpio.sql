/**
 * se debe ejecutar en DB_COMUNICACION 
 * Cambio de linea PON
 *Plantilla que se envia cordinador de cuadrilla
 * @author Manue Carpio<mcarpio@telconet.ec>
 * @version 1.0 28-09-2022 - Versión Inicial.
 */

SET SERVEROUTPUT ON
DECLARE
  bada clob:='<!DOCTYPE html>';
  Ln_Idproceso Number(5,0);
BEGIN

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
    'NUEVA_RED_GPON_MPLS_TN',
    'PARAMETROS PARA WS de GDA - Cambio de linea pon',
    'TECNICO',
    'Activo',
    'mcarpio',
    SYSDATE,
    '127.0.0.1'
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
    valor6,
    valor7,
    observacion,
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
            nombre_parametro = 'NUEVA_RED_GPON_MPLS_TN' AND estado = 'Activo'
    ),
    'PARAMETROS PARA WS de GDA - Cambio de linea pon',
    'CAMBIO_LINEA_PON_DATOS_NW',
    'SAFECITYDATOS', --servicio
    'ACTIVAR_TN_L3', --opcion_NW
    'configurarOLT', --opcion
    'activar',       --accion
    'servicios',     --modulo
    'SI', --comandoConfiguracion
    'Parametros de envion al ws de GDA para ejecucion de cambio de linea PON',
    'Activo',
    'mcarpio',
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
    valor4,
    valor5,
    valor6,
    valor7,
    observacion,
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
            nombre_parametro = 'NUEVA_RED_GPON_MPLS_TN' AND estado = 'Activo'
    ),
    'PARAMETROS PARA WS de GDA - Cambio de linea pon',
    'CAMBIO_LINEA_PON_DATOS_SERVICIOS',
    'S', --es_datos
    'N', --tiene_cpe
    'CORPORATIVO', --tipo_negocio_actual
    'S', --tiene_datos
    'S', --tiene_internet
    'TN_CAMBIAR_PUERTO_PON', --opcion
    'Parametros de envion al ws de GDA para ejecucion de cambio de linea PON',
    'Activo',
    'mcarpio',
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
    valor4,
    observacion,
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
            nombre_parametro = 'NUEVA_RED_GPON_MPLS_TN' AND estado = 'Activo'
    ),
    'PARAMETROS PARA WS de GDA - Cambio de linea pon',
    'CAMBIO_LINEA_PON_DATOS_SERVICIOS_2',
    '0', --esquema
    'DESACTIVAR_TN_L3', --desactivar_opcion
    'eliminar',
    'Parametros de envion al ws de GDA para ejecucion de cambio de linea PON',
    'Activo',
    'mcarpio',
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
    valor4,
    observacion,
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
            nombre_parametro = 'NUEVA_RED_GPON_MPLS_TN' AND estado = 'Activo'
    ),
    'PARAMETROS Notificacion correo - region costa',
    'CAMBIO_LINEA_PON_R1_NOTIFICACION',
    'R1', 
    'sjayo@telconet.ec', 
    'Envios de datos de configuración para camaras',
    'Parametros de envio de notificacion para configuracion de camaras',
    'Activo',
    'mcarpio',
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
    valor4,
    observacion,
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
            nombre_parametro = 'NUEVA_RED_GPON_MPLS_TN' AND estado = 'Activo'
    ),
    'PARAMETROS Notificacion correo - region sierra',
    'CAMBIO_LINEA_PON_R2_NOTIFICACION',
    'R2', --esquema
    'sjayo@telconet.ec', --reutilizada
    'Envios de datos de configuración para camaras',
    'Parametros de envio de notificacion para configuracion de camaras',
    'Activo',
    'mcarpio',
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
    valor4,
    valor5,
    observacion,
    estado,
    valor6,
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
            nombre_parametro = 'NUEVA_RED_GPON_MPLS_TN' AND estado = 'Activo'
    ),
    'DATOS_CREAR_TAREA',
    'Cambio de linea pon DATOS GPON SAFE CITY',
    'Se realiza el cambio de linea pon forma correcta', 
    'Interno',
    'Registro Interno',
    'TN',
    '',
    'Activo',
    'ELECTRICO',
    'mcarpio',
    SYSDATE,
    '127.0.0.1',
    10
);

--------------------Pantilla correo--------------------------------

DBMS_LOB.APPEND(bada, ' <html style="margin:0;padding:0" data-lt-installed="true" lang="en">
  <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1"
      ><meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="format-detection" content="telephone=no">
      <title>Asunto</title>
  <style type="text/css">
     @media screen and (max-width: 480px) {
      .mailpoet_button {width:100% !important;}
  }
  @media screen and (max-width: 599px) {
      .mailpoet_header {
          padding: 10px 20px;
      }
      .mailpoet_button {
          width: 100% !important;
          padding: 5px 0 !important;
          box-sizing:border-box !important;
      }
      div, .mailpoet_cols-two, .mailpoet_cols-three {
          max-width: 100% !important;
      }
  }
/* cyrillic-ext */
  @font-face {
  font-family:  ''Montserrat'';
  font-style: normal;
  font-weight: 500;
  font-display: swap;
  src: url(https://fonts.gstatic.com/s/montserrat/v24/JTUHjIg1_i6t8kCHKm4532VJOt5-QNFgpCtZ6Hw0aXpsog.woff2) format( ''woff2'');
  unicode-range: U+0460-052F, U+1C80-1C88, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;
  }
  /* cyrillic */
  @font-face {
  font-family:  ''Montserrat'';
  font-style: normal;
  font-weight: 500;
  font-display: swap;
  src: url(https://fonts.gstatic.com/s/montserrat/v24/JTUHjIg1_i6t8kCHKm4532VJOt5-QNFgpCtZ6Hw9aXpsog.woff2) format( ''woff2'');
  unicode-range: U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;
  }
  /* vietnamese */
  @font-face {
  font-family: ''Montserrat'';
  font-style: normal;
  font-weight: 500;
  font-display: swap;
  src: url(https://fonts.gstatic.com/s/montserrat/v24/JTUHjIg1_i6t8kCHKm4532VJOt5-QNFgpCtZ6Hw2aXpsog.woff2) format( ''woff2'');
  unicode-range: U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+1EA0-1EF9, U+20AB;
  }
  /* latin-ext */
  @font-face {
  font-family:  ''Montserrat'';
  font-style: normal;
  font-weight: 500;
  font-display: swap;
  src: url(https://fonts.gstatic.com/s/montserrat/v24/JTUHjIg1_i6t8kCHKm4532VJOt5-QNFgpCtZ6Hw3aXpsog.woff2) format( ''woff2'');
  unicode-range: U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
  }
  /* latin */
  @font-face {
  font-family:  ''Montserrat'';
  font-style: normal;
  font-weight: 500;
  font-display: swap;
  src: url(https://fonts.gstatic.com/s/montserrat/v24/JTUHjIg1_i6t8kCHKm4532VJOt5-QNFgpCtZ6Hw5aXo.woff2) format( ''woff2'');
  unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
  }
  /* cyrillic-ext */
  @font-face {
  font-family:  ''Montserrat'';
  font-style: normal;
  font-weight: 600;
  font-display: swap;
  src: url(https://fonts.gstatic.com/s/montserrat/v24/JTUHjIg1_i6t8kCHKm4532VJOt5-QNFgpCu173w0aXpsog.woff2) format( ''woff2'');
  unicode-range: U+0460-052F, U+1C80-1C88, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;
  }
  /* cyrillic */
  @font-face {
  font-family:  ''Montserrat'';
  font-style: normal;
  font-weight: 600;
  font-display: swap;
  src: url(https://fonts.gstatic.com/s/montserrat/v24/JTUHjIg1_i6t8kCHKm4532VJOt5-QNFgpCu173w9aXpsog.woff2) format( ''woff2'');
  unicode-range: U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;
  }
  /* vietnamese */
  @font-face {
  font-family:  ''Montserrat'';
  font-style: normal;
  font-weight: 600;
  font-display: swap;
  src: url(https://fonts.gstatic.com/s/montserrat/v24/JTUHjIg1_i6t8kCHKm4532VJOt5-QNFgpCu173w2aXpsog.woff2) format( ''woff2'');
  unicode-range: U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+1EA0-1EF9, U+20AB;
  }
  /* latin-ext */
  @font-face {
  font-family:  ''Montserrat'';
  font-style: normal;
  font-weight: 600;
  font-display: swap;
  src: url(https://fonts.gstatic.com/s/montserrat/v24/JTUHjIg1_i6t8kCHKm4532VJOt5-QNFgpCu173w3aXpsog.woff2) format( ''woff2'');
  unicode-range: U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
  }
  /* latin */
  @font-face {
  font-family:  ''Montserrat'';
  font-style: normal;
  font-weight: 600;
  font-display: swap;
  src: url(https://fonts.gstatic.com/s/montserrat/v24/JTUHjIg1_i6t8kCHKm4532VJOt5-QNFgpCu173w5aXo.woff2) format( ''woff2'');
  unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
  }
  /* cyrillic-ext */
  @font-face {
  font-family:  ''Montserrat'';
  font-style: normal;
  font-weight: 700;
  font-display: swap;
  src: url(https://fonts.gstatic.com/s/montserrat/v24/JTUHjIg1_i6t8kCHKm4532VJOt5-QNFgpCuM73w0aXpsog.woff2) format( ''woff2'');
  unicode-range: U+0460-052F, U+1C80-1C88, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;
  }
  /* cyrillic */
  @font-face {
  font-family:  ''Montserrat'';
  font-style: normal;
  font-weight: 700;
  font-display: swap;
  src: url(https://fonts.gstatic.com/s/montserrat/v24/JTUHjIg1_i6t8kCHKm4532VJOt5-QNFgpCuM73w9aXpsog.woff2) format( ''woff2'');
  unicode-range: U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;
  }
  /* vietnamese */
  @font-face {
  font-family:  ''Montserrat'';
  font-style: normal;
  font-weight: 700;
  font-display: swap;
  src: url(https://fonts.gstatic.com/s/montserrat/v24/JTUHjIg1_i6t8kCHKm4532VJOt5-QNFgpCuM73w2aXpsog.woff2) format( ''woff2'');
  unicode-range: U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+1EA0-1EF9, U+20AB;
  }
  /* latin-ext */
  @font-face {
  font-family:  ''Montserrat'';
  font-style: normal;
  font-weight: 700;
  font-display: swap;
  src: url(https://fonts.gstatic.com/s/montserrat/v24/JTUHjIg1_i6t8kCHKm4532VJOt5-QNFgpCuM73w3aXpsog.woff2) format( ''woff2'');
  unicode-range: U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
  }
  /* latin */
  @font-face {
  font-family:  ''Montserrat'';
  font-style: normal;
  font-weight: 700;
  font-display: swap;
  src: url(https://fonts.gstatic.com/s/montserrat/v24/JTUHjIg1_i6t8kCHKm4532VJOt5-QNFgpCuM73w5aXo.woff2) format( ''woff2'');
  unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
  }
  /* cyrillic-ext */
  @font-face {
  font-family:  ''Roboto'';
  font-style: normal;
  font-weight: 400;
  font-display: swap;
  src: url(https://fonts.gstatic.com/s/roboto/v30/KFOmCnqEu92Fr1Mu72xKOzY.woff2) format( ''woff2'');
  unicode-range: U+0460-052F, U+1C80-1C88, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;
  }
  /* cyrillic */
  @font-face {
  font-family:  ''Roboto'';
  font-style: normal;
  font-weight: 400;
  font-display: swap;
  src: url(https://fonts.gstatic.com/s/roboto/v30/KFOmCnqEu92Fr1Mu5mxKOzY.woff2) format( ''woff2'');
  unicode-range: U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;
  }
  /* greek-ext */
  @font-face {
  font-family:  ''Roboto'';
  font-style: normal;
  font-weight: 400;
  font-display: swap;
  src: url(https://fonts.gstatic.com/s/roboto/v30/KFOmCnqEu92Fr1Mu7mxKOzY.woff2) format( ''woff2'');
  unicode-range: U+1F00-1FFF;
  }
  /* greek */
  @font-face {
  font-family:  ''Roboto'';
  font-style: normal;
  font-weight: 400;
  font-display: swap;
  src: url(https://fonts.gstatic.com/s/roboto/v30/KFOmCnqEu92Fr1Mu4WxKOzY.woff2) format( ''woff2'');
  unicode-range: U+0370-03FF;
  }
  /* vietnamese */
  @font-face {
  font-family:  ''Roboto'';
  font-style: normal;
  font-weight: 400;
  font-display: swap;
  src: url(https://fonts.gstatic.com/s/roboto/v30/KFOmCnqEu92Fr1Mu7WxKOzY.woff2) format( ''woff2'');
  unicode-range: U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+1EA0-1EF9, U+20AB;
  }
  /* latin-ext */
  @font-face {
  font-family:  ''Roboto'';
  font-style: normal;
  font-weight: 400;
  font-display: swap;
  src: url(https://fonts.gstatic.com/s/roboto/v30/KFOmCnqEu92Fr1Mu7GxKOzY.woff2) format( ''woff2'');
  unicode-range: U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
  }
  /* latin */
  @font-face {
  font-family:  ''Roboto'';
  font-style: normal;
  font-weight: 400;
  font-display: swap;
  src: url(https://fonts.gstatic.com/s/roboto/v30/KFOmCnqEu92Fr1Mu4mxK.woff2) format( ''woff2'');
  unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
  }
  /* cyrillic-ext */
  @font-face {
  font-family:  ''Roboto'';
  font-style: normal;
  font-weight: 500;
  font-display: swap;
  src: url(https://fonts.gstatic.com/s/roboto/v30/KFOlCnqEu92Fr1MmEU9fCRc4EsA.woff2) format( ''woff2'');
  unicode-range: U+0460-052F, U+1C80-1C88, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;
  }
  /* cyrillic */
  @font-face {
  font-family:  ''Roboto'';
  font-style: normal;
  font-weight: 500;
  font-display: swap;
  src: url(https://fonts.gstatic.com/s/roboto/v30/KFOlCnqEu92Fr1MmEU9fABc4EsA.woff2) format( ''woff2'');
  unicode-range: U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;
  }
  /* greek-ext */
  @font-face {
  font-family:  ''Roboto'';
  font-style: normal;
  font-weight: 500;
  font-display: swap;
  src: url(https://fonts.gstatic.com/s/roboto/v30/KFOlCnqEu92Fr1MmEU9fCBc4EsA.woff2) format( ''woff2'');
  unicode-range: U+1F00-1FFF;
  }
  /* greek */
  @font-face {
  font-family:  ''Roboto'';
  font-style: normal;
  font-weight: 500;
  font-display: swap;
  src: url(https://fonts.gstatic.com/s/roboto/v30/KFOlCnqEu92Fr1MmEU9fBxc4EsA.woff2) format( ''woff2'');
  unicode-range: U+0370-03FF;
  }
  /* vietnamese */
  @font-face {
  font-family:  ''Roboto'';
  font-style: normal;
  font-weight: 500;
  font-display: swap;
  src: url(https://fonts.gstatic.com/s/roboto/v30/KFOlCnqEu92Fr1MmEU9fCxc4EsA.woff2) format( ''woff2'');
  unicode-range: U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+1EA0-1EF9, U+20AB;
  }
  /* latin-ext */
  @font-face {
  font-family:  ''Roboto'';
  font-style: normal;
  font-weight: 500;
  font-display: swap;
  src: url(https://fonts.gstatic.com/s/roboto/v30/KFOlCnqEu92Fr1MmEU9fChc4EsA.woff2) format( ''woff2'');
  unicode-range: U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
  }
  /* latin */
  @font-face {
  font-family:  ''Roboto'';
  font-style: normal;
  font-weight: 500;
  font-display: swap;
  src: url(https://fonts.gstatic.com/s/roboto/v30/KFOlCnqEu92Fr1MmEU9fBBc4.woff2) format( ''woff2'');
  unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
  }
  /* cyrillic-ext */
  @font-face {
  font-family:  ''Roboto'';
  font-style: normal;
  font-weight: 700;
  font-display: swap;
  src: url(https://fonts.gstatic.com/s/roboto/v30/KFOlCnqEu92Fr1MmWUlfCRc4EsA.woff2) format( ''woff2'');
  unicode-range: U+0460-052F, U+1C80-1C88, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;
  }
  /* cyrillic */
  @font-face {
  font-family:  ''Roboto'';
  font-style: normal;
  font-weight: 700;
  font-display: swap;
  src: url(https://fonts.gstatic.com/s/roboto/v30/KFOlCnqEu92Fr1MmWUlfABc4EsA.woff2) format( ''woff2'');
  unicode-range: U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;
  }
  /* greek-ext */
  @font-face {
  font-family:  ''Roboto'';
  font-style: normal;
  font-weight: 700;
  font-display: swap;
  src: url(https://fonts.gstatic.com/s/roboto/v30/KFOlCnqEu92Fr1MmWUlfCBc4EsA.woff2) format( ''woff2'');
  unicode-range: U+1F00-1FFF;
  }
  /* greek */
  @font-face {
  font-family:  ''Roboto'';
  font-style: normal;
  font-weight: 700;
  font-display: swap;
  src: url(https://fonts.gstatic.com/s/roboto/v30/KFOlCnqEu92Fr1MmWUlfBxc4EsA.woff2) format( ''woff2'');
  unicode-range: U+0370-03FF;
  }
  /* vietnamese */
  @font-face {
  font-family:  ''Roboto'';
  font-style: normal;
  font-weight: 700;
  font-display: swap;
  src: url(https://fonts.gstatic.com/s/roboto/v30/KFOlCnqEu92Fr1MmWUlfCxc4EsA.woff2) format( ''woff2'');
  unicode-range: U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+1EA0-1EF9, U+20AB;
  }
  /* latin-ext */
  @font-face {
  font-family:  ''Roboto'';
  font-style: normal;
  font-weight: 700;
  font-display: swap;
  src: url(https://fonts.gstatic.com/s/roboto/v30/KFOlCnqEu92Fr1MmWUlfChc4EsA.woff2) format( ''woff2'');
  unicode-range: U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
  }
  /* latin */
  @font-face {
  font-family:  ''Roboto'';
  font-style: normal;
  font-weight: 700;
  font-display: swap;
  src: url(https://fonts.gstatic.com/s/roboto/v30/KFOlCnqEu92Fr1MmWUlfBBc4.woff2) format( ''woff2'');
  unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
  }
    .table {
      width: 100%;
       border: 1px solid #000;
       border-collapse: collapse;
       caption-side: top;
     }
  .th, .td {
    width: 25%;
  text-align: left;
  vertical-align: top;
  border: 1px solid #000;
  padding: 0.3em;
  }
  caption {
    padding: 0.3em;
  color: #fff;
  background: #6699CC;
  }
  th {
  background: #E5F2FF;
  }
  td {
  background: #f4f4f4;
  }
  .odd td {
  background: #fff;
  }
  
    </style>
  </head> ');
DBMS_LOB.APPEND(bada,' <body>
    <table align="center" width="100%" cellspacing="0" cellpadding="5">
      <tr>
        <td align="center" style="border:1px solid #6699CC;background-color:#E5F2FF;">
            <img alt=""  src="http://images.telconet.net/others/sit/notificaciones/logo.png"/>
        </td>
      </tr>
      <tr>
        <td style="border:1px solid #6699CC;">
          <table width="100%" cellspacing="0" cellpadding="5">
            <tr>
                <td colspan="2">Estimado personal,</td>
            </tr>
            <tr>
              <td colspan="2">
                El presente correo es para indicarle los siguientes datos de configuración: 
              </td>
            </tr>
            <tr>
              <td colspan="2">
                <hr />
              </td>
            </tr> 
            <tr>
              <td>
                <strong>Cliente:</strong>
              </td>
              <td>{{cliente}}</td>
            </tr>
            <tr>
              <td>
                <strong>Login: </strong>
              </td>
              <td>{{login}}</td>
            </tr>
            <tr>
              <td>
                <strong>OLT: </strong>
              </td>
              <td>{{olt}}</td>
            </tr>
            <tr>
              <td>
                <strong>ONT:</strong>
              </td>
              <td>{{ont}}</td>
            </tr>
            <tr>
              <td colspan="2"><br/></td>
            </tr>
          </table>
          <div">
            <table class="table"  width=''auto'' style=''border:1px solid #000000;border-color:#A9E2F3;'' cellpadding="10">
              <caption>Datos Configuración</caption>
              <thead>
                <tr class="tr" width=''auto'' style=''border:1px solid #000000;border-color:#A9E2F3;'' cellpadding="10">
                  <th class="td" scope="col" class="elements">Login auxiliar</th>
                  <th class="td" scope="col" class="tag">Ip anterior</th>
                  <th class="td" scope="col" class="purpose">Ip nueva</th>
                </tr>
              </thead>
              <tbody>
                <tr class="tr odd">
                  <td class="td">{{login_aux_1}}</td>
                  <td class="td">{{ip_ant_1}}</td>
                  <td class="td">{{ip_nuv_1}}</td>
                </tr>
                <tr class="tr odd">
                  <td class="td">{{login_aux_1}}</td>
                  <td class="td">{{ip_ant_2}}</td>
                  <td class="td">{{ip_nuv_2}}</td>
                </tr>
                <tr class="tr odd">
                  <td class="td">{{login_aux_1}}</td>
                  <td class="td">{{ip_ant_3}}</td>
                  <td class="td">{{ip_nuv_3}}</td>
                </tr>
                <tr class="tr odd">
                  <td class="td">{{login_aux_1}}</td>
                  <td class="td">{{ip_ant_4}}</td>
                  <td class="td">{{ip_nuv_4}}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </td>
      </tr>
      <tr>
          <td>'||chr(38)||'nbsp;
          </td>
      </tr>
    </table>
  </body>
</html> ');
INSERT INTO DB_COMUNICACION.ADMI_PLANTILLA
VALUES(DB_COMUNICACION.SEQ_ADMI_PLANTILLA.nextval,'Notificacion cambio linea pon','SAFECITYCAN','TECNICO',bada,'Activo',sysdate,'mcarpio','','','10');

--------------------------parametros creacion tarea automatica-----------------------------------------------------
INSERT
  INTO DB_SOPORTE.ADMI_PROCESO
    (
      ID_PROCESO,
      NOMBRE_PROCESO,
      DESCRIPCION_PROCESO,
      ESTADO,
      USR_CREACION,
      USR_ULT_MOD,
      FE_CREACION,
      FE_ULT_MOD,
      VISIBLE
    )
    VALUES
    (
      DB_SOPORTE.SEQ_ADMI_PROCESO.NEXTVAL,
      'SOLICITUD CAMBIO DE LINEA PON',
      'Proceso para realizar cambio de lines pon del servicos DATOS GPON SAFE CITY',
      'Activo',
      'mcarpio',
      'mcarpio',
      SYSDATE,
      SYSDATE,
      'NO'
    );
  SELECT ID_PROCESO
  INTO Ln_IdProceso
  FROM DB_SOPORTE.ADMI_PROCESO
  WHERE NOMBRE_PROCESO='SOLICITUD CAMBIO DE LINEA PON';
  INSERT
  INTO DB_SOPORTE.ADMI_TAREA
    (
      ID_TAREA,
      PROCESO_ID,
      NOMBRE_TAREA,
      DESCRIPCION_TAREA,
      ESTADO,
      USR_CREACION,
      USR_ULT_MOD,
      FE_CREACION,
      FE_ULT_MOD
    )
    VALUES
    (
      DB_SOPORTE.SEQ_ADMI_TAREA.NEXTVAL,
      Ln_IdProceso,
      'Cambio de linea pon DATOS GPON SAFE CITY',
      'Tarea que ejecuta la configuracion del servicio SAFE VIDEO ANALYTICS CAM..',
      'Activo',
      'mcarpio',
      'mcarpio',
      SYSDATE,
      SYSDATE
    );
  INSERT
  INTO DB_SOPORTE.ADMI_PROCESO_EMPRESA
    (
      ID_PROCESO_EMPRESA,
      PROCESO_ID,
      EMPRESA_COD,
      ESTADO,
      USR_CREACION,
      FE_CREACION
    )
    VALUES
    (
      DB_SOPORTE.SEQ_ADMI_PROCESO_EMPRESA.NEXTVAL,
      Ln_IdProceso,
      '10',
      'Activo',
      'mcarpio',
      SYSDATE
    );
COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;





