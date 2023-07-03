/*
 * @author Edgar Pin Villavicencio <epin@telconet.ec>
 * @version 1.0 16-10-2019 Se realizan los inserts de los productos que se pueden visualizar en el tm-comercial
 */

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
VALUES(DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL, 
       'PRODUCTOS_TM_COMERCIAL', 
       'Productos que se mostrarán en el tm-comercial',
       'COMERCIAL',
       'SERVICIOS',
       'Activo',
       'epin',
       sysdate,
       '127.0.0.1',
       null, null, null);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO
         FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'PRODUCTOS_TM_COMERCIAL'),
        'Renta AP WIFI',
        '18',
        NULL, NULL, NULL,
        'Activo',
        'epin',
        sysdate,
        '127.0.0.1',
        null, null, null, null, null, null, null, null);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO
         FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'PRODUCTOS_TM_COMERCIAL'),
        'Netlife Zone',
        '18',
        NULL, NULL, NULL,
        'Activo',
        'epin',
        sysdate,
        '127.0.0.1',
        null, null, null, null, null, null, null, null);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO
         FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'PRODUCTOS_TM_COMERCIAL'),
        'NetlifeAssistance',
        '18',
        NULL, NULL, NULL,
        'Activo',
        'epin',
        sysdate,
        '127.0.0.1',
        null, null, null, null, null, null, null, null);
       
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO
         FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'PRODUCTOS_TM_COMERCIAL'),
        'NetlifeCloud',
        '18',
        NULL, NULL, NULL,
        'Activo',
        'epin',
        sysdate,
        '127.0.0.1',
        null, null, null, null, null, null, null, null);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO
         FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'PRODUCTOS_TM_COMERCIAL'),
        'NETLIFECAM - Servicio Básico de Visualización Remota Residencial',
        '18',
        NULL, NULL, NULL,
        'Activo',
        'epin',
        sysdate,
        '127.0.0.1',
        null, null, null, null, null, null, null, null);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO
         FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'PRODUCTOS_TM_COMERCIAL'),
        'I. PROTEGIDO MULTI PAID',
        '18',
        NULL, NULL, NULL,
        'Activo',
        'epin',
        sysdate,
        '127.0.0.1',
        null, null, null, null, null, null, null, null);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
VALUES(DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL, 
       'PARAMETROS_TM_COMERCIAL', 
       'Parametros de App Tm-Comercial',
       'COMERCIAL',
       'SERVICIOS',
       'Activo',
       'epin',
       sysdate,
       '127.0.0.1',
       null, null, null);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO
         FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'PARAMETROS_TM_COMERCIAL'),
        'DIAS DE VENCIMIENTO DE SALDO',
        '8',
        '8', NULL, NULL,
        'Activo',
        'epin',
        sysdate,
        '127.0.0.1',
        null, null, null, null, 18, null, null, null);

--Inserto numeraciones de los adendum de servicios

--BORRAR NUMERACIONES CREADAS
DELETE FROM db_comercial.admi_numeracion WHERE CODIGO='CONA';

--CREAR MASIVO DE NUMERACION EN BASE A LAS EXISTENTES DE CONTRATO
INSERT
INTO DB_COMERCIAL.ADMI_NUMERACION
  (
    ID_NUMERACION,
    EMPRESA_ID,
    OFICINA_ID,
    DESCRIPCION,
    CODIGO,
    NUMERACION_UNO,
    NUMERACION_DOS,
    SECUENCIA,
    FE_CREACION,
    USR_CREACION,
    TABLA,
    ESTADO,
    PROCESOS_AUTOMATICOS
  )
select 
DB_COMERCIAL.SEQ_ADMI_NUMERACION.NEXTVAL,
EMPRESA_ID,
OFICINA_ID,
descripcion,
codigo,
NUMERACION_UNO,
NUMERACION_DOS,
secuencia,
fe_creacion,
usr_creacion,
tabla,
estado,
procesos_automaticos
FROM 
(SELECT an.EMPRESA_ID,
  an.OFICINA_ID,
  'Numero de Contrato Adendum '|| iog.NOMBRE_OFICINA AS descripcion,
  'CONA'                AS codigo,
  trim(to_char (an.NUMERACION_UNO,'000')) as NUMERACION_UNO,
  trim(to_char (an.NUMERACION_DOS,'000')) as NUMERACION_DOS,
  1              AS secuencia,
  SYSDATE AS fe_creacion,
  'telcos'       AS usr_creacion,
  'info_adendum' AS tabla,
  'Activo'       AS estado,
  'N'            AS procesos_automaticos
FROM db_comercial.admi_numeracion an
LEFT JOIN DB_COMERCIAL.INFO_OFICINA_GRUPO iog
ON iog.ID_OFICINA   =an.OFICINA_ID
WHERE an.empresa_id = 18
AND (upper(an.descripcion) LIKE 'NUMERACION CONTRATO%' or upper(an.descripcion) LIKE 'NUMERACION DE CONTRATO%') 
and an.codigo = 'CON');

-- inserto plantilla de tmcomercial
INSERT INTO DB_FIRMAELECT.ADM_EMPRESA_PARAMETRO (ID_EMPRESA_PARAMETRO, EMPRESA_ID, CLAVE, VALOR, DESCRIPCION, ES_CONFIG, ES_DEFAULT, ENVIA_POR_MAIL)
VALUES (DB_FIRMAELECT.SEQ_ADM_EMPRESA_PARAMETRO.NEXTVAL, 1, 'adendumMegaDatos', 'adendumMegaDatos', 'Plantilla utilizada para crear los adendums MegaDatos', 'S', 'N', 'S');

INSERT INTO DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA (ID_EMPRESA_PLANTILLA, COD_PLANTILLA, EMPRESA_ID, DESCRIPCION, HTML, ESTADO, PROPIEDADES)
VALUES (DB_FIRMAELECT.SEQ_ADM_EMPRESA_PLANTILLA.NEXTVAL, 'adendumMegaDatos', 1, 'Plantilla de adendum',
'html',
'Activo',
'{
    "pageFormat": "A4",
    "marginTop": "8",
    "marginLeft": "4",
    "marginRight": "8"
}');

commit;
