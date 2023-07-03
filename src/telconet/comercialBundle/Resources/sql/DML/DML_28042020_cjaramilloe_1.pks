/**
 *
 * Inserción y/o actualización de parámetros.
 * Servicios adicionales modificados TM Comercial
 *
 * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
 * @version 1.0 28-04-2020
 * 
 **/

SET DEFINE OFF;

UPDATE DB_COMERCIAL.ADMI_PRODUCTO AP
	SET AP.DESCRIPCION_PRODUCTO = 'Netlife Assistance Pro'
	WHERE AP.DESCRIPCION_PRODUCTO = 'NetlifeAssistance KB' 
	AND AP.EMPRESA_COD='18' 
	AND AP.CODIGO_PRODUCTO='KO01' 
	AND ESTADO = 'Activo';

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
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
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION)
VALUES(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT apc.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB apc WHERE apc.NOMBRE_PARAMETRO = 'MENSAJES_TM_COMERCIAL' AND apc.ESTADO = 'Activo'),
    'RESTRICCION_NO_EMPLEADO',
    'Ud. no posee rol de la empresa Megadatos',
    NULL,
    NULL,
    NULL,
    'Activo',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    NULL,
    '18',
    NULL,
    NULL,
    NULL);


UPDATE
    DB_GENERAL.ADMI_PARAMETRO_DET
SET
    DESCRIPCION = 'Netlife Defense',
    VALOR1 = 'IPMP',
    VALOR2 = 'prodNetlifeDefense',
    VALOR3 = 'I. PROTEGIDO MULTI PAID',
    VALOR4 = 210,
    VALOR5 = 'OTROS',
    IP_CREACION = '127.0.0.1',
    USR_ULT_MOD = 'cjaramilloe',
    FE_ULT_MOD = SYSDATE,
    EMPRESA_COD = '18'
WHERE
    ID_PARAMETRO_DET = 9928;

UPDATE
    DB_GENERAL.ADMI_PARAMETRO_DET
SET
    DESCRIPCION = 'Netlife Zone',
    VALOR1 = 'NL01',
    VALOR2 = 'prodNetlifeZone',
    VALOR3 = 'Netlife Zone',
    VALOR4 = '275',
    VALOR5 = 'NETWIFI',
    USR_ULT_MOD = 'cjaramilloe',
    FE_ULT_MOD = SYSDATE,
    EMPRESA_COD = '18'
WHERE
    ID_PARAMETRO_DET = 9924;

UPDATE
    DB_GENERAL.ADMI_PARAMETRO_DET
SET
    DESCRIPCION = 'Netlife Assistance',
    VALOR1 = 'ASSI',
    VALOR2 = 'prodNetlifeAssistance',
    VALOR3 = 'NetlifeAssistance',
    VALOR4 = '1130',
    VALOR5 = 'OTROS',
    USR_ULT_MOD = 'cjaramilloe',
    FE_ULT_MOD = SYSDATE,
    EMPRESA_COD = '18'
WHERE
    ID_PARAMETRO_DET = 9925;

UPDATE
    DB_GENERAL.ADMI_PARAMETRO_DET
SET
    DESCRIPCION = 'Netcam',
    VALOR1 = 'VISEG',
    VALOR2 = 'prodNetlifeCam',
    VALOR3 = 'NETLIFECAM - Servicio Básico de Visualización Remota Residencial',
    VALOR4 = '78',
    VALOR5 = 'NETLIFECAM',
    ESTADO = 'Activo',
    USR_ULT_MOD = 'cjaramilloe',
    FE_ULT_MOD = SYSDATE,
    EMPRESA_COD = '18'
WHERE
    ID_PARAMETRO_DET = 9927;

UPDATE
    DB_GENERAL.ADMI_PARAMETRO_DET
SET
    DESCRIPCION = 'Netlife Cloud (Microsoft 365 Familia)',
    VALOR1 = '1612',
    VALOR2 = 'prodNetlifeCloud',
    VALOR3 = 'NetlifeCloud',
    VALOR4 = '939',
    VALOR5 = 'OTROS',
    USR_ULT_MOD = 'cjaramilloe',
    FE_ULT_MOD = SYSDATE,
    EMPRESA_COD = '18'
WHERE
    ID_PARAMETRO_DET = 9926;

UPDATE
    DB_GENERAL.ADMI_PARAMETRO_DET
SET
    DESCRIPCION = 'Renta Router Wifi Dual Band',
    VALOR1 = 'WFDB',
    VALOR2 = 'prodRentaWifiDualBand',
    VALOR3 = 'WiFi Dual Band',
    VALOR4 = '1231',
    VALOR5 = 'OTROS',
    USR_ULT_MOD = 'cjaramilloe',
    FE_ULT_MOD = SYSDATE,
    EMPRESA_COD = '18'
WHERE
    ID_PARAMETRO_DET = 9923;


INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
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
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION)
VALUES(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT apc.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB apc WHERE apc.NOMBRE_PARAMETRO = 'PRODUCTOS_TM_COMERCIAL' AND apc.ESTADO = 'Activo'),
    'Constructor Web',
    'KO02',
    'prodEcommerceBasic',
    'ECOMMERCE BASIC',
    '1263',
    'Activo',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    'cjaramilloe',
    SYSDATE,
    NULL,
    'OTROS',
    '18',
    NULL,
    NULL,
    NULL);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
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
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION)
VALUES(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT apc.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB apc WHERE apc.NOMBRE_PARAMETRO = 'PRODUCTOS_TM_COMERCIAL' AND apc.ESTADO = 'Activo'),
    'Cuentas de Correo (5 cuentas adicionales)',
    'CO01',
    'prodCorreo',
    'CUENTAS DE CORREO',
    '1270',
    'Activo',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    'cjaramilloe',
    SYSDATE,
    NULL,
    'OTROS',
    '18',
    NULL,
    NULL,
    NULL);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET,
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
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION)
VALUES(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT apc.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB apc WHERE apc.NOMBRE_PARAMETRO = 'PRODUCTOS_TM_COMERCIAL' AND apc.ESTADO = 'Activo'),
    'Fibra Invisible',
    'NETFIB',
    'prodNetfiber',
    'NETFIBER',
    '1207',
    'Activo',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    'cjaramilloe',
    SYSDATE,
    NULL,
    'OTROS',
    '18',
    NULL,
    NULL,
    NULL);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
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
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION)
VALUES(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT apc.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB apc WHERE apc.NOMBRE_PARAMETRO = 'PRODUCTOS_TM_COMERCIAL' AND apc.ESTADO = 'Activo'),
    'Netlife Assistance Pro',
    'KO01',
    'prodNetlifeAssistanceKB',
    'Netlife Assistance Pro',
    '1262',
    'Activo',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    'cjaramilloe',
    SYSDATE,
    NULL,
    'OTROS',
    '18',
    NULL,
    NULL,
    NULL);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
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
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION)
VALUES(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT apc.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB apc WHERE apc.NOMBRE_PARAMETRO = 'PRODUCTOS_TM_COMERCIAL' AND apc.ESTADO = 'Activo'),
    'Renta AP Extender Dual Band',
    'EXDB',
    'prodRentaExtenderDualBand',
    'Extender Dual Band',
    '1232',
    'Activo',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    'cjaramilloe',
    SYSDATE,
    NULL,
    'OTROS',
    '18',
    NULL,
    NULL,
    NULL);


 INSERT INTO
    DB_GENERAL.ADMI_PARAMETRO_CAB (
    ID_PARAMETRO,
    NOMBRE_PARAMETRO,
    DESCRIPCION,
    MODULO,
    PROCESO,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD)
VALUES(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'CARACTERISTICAS_IGNORADAS_TM_COMERCIAL',
    'Características ignoradas en TM COMERCIAL',
    'COMERCIAL',
    'TM_COMERCIAL',
    'Activo',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB (
    ID_PARAMETRO,
    NOMBRE_PARAMETRO,
    DESCRIPCION,
    MODULO,
    PROCESO,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD)
VALUES(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'CARACTERISTICAS_PROD_ADICIONALES_TM_COMERCIAL',
    'Características de productos adicionales en TM COMERCIAL',
    'COMERCIAL',
    'TM_COMERCIAL',
    'Activo',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL);

 INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
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
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION)
VALUES(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT apc.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB apc WHERE apc.NOMBRE_PARAMETRO = 'CARACTERISTICAS_PROD_ADICIONALES_TM_COMERCIAL' AND apc.ESTADO = 'Activo'),
    'ANTIVIRUS',
    'IPMP',
    '11606',
    'NO',
    'NO',
    'Activo',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    NULL,
    '18',
    NULL,
    'TEXTO',
    NULL);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
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
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION)
VALUES(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT apc.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB apc WHERE apc.NOMBRE_PARAMETRO = 'CARACTERISTICAS_PROD_ADICIONALES_TM_COMERCIAL' AND apc.ESTADO = 'Activo'),
    'CANTIDAD DISPOSITIVOS',
    'IPMP',
    '837',
    'SI',
    'SI',
    'Activo',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    '1;3;5;8',
    '18',
    '3',
    'SELECCIONABLE',
    NULL);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
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
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION)
VALUES(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT apc.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB apc WHERE apc.NOMBRE_PARAMETRO = 'CARACTERISTICAS_PROD_ADICIONALES_TM_COMERCIAL' AND apc.ESTADO = 'Activo'),
    'CAPACIDAD1',
    'KO02',
    '11720',
    'NO',
    'NO',
    'Activo',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    '1;2',
    '18',
    '2',
    'SELECCIONABLE',
NULL);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
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
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION)
VALUES(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT apc.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB apc WHERE apc.NOMBRE_PARAMETRO = 'CARACTERISTICAS_PROD_ADICIONALES_TM_COMERCIAL' AND apc.ESTADO = 'Activo'),
    'CAPACIDAD1',
    'CO01',
    '11797',
    'NO',
    'NO',
    'Activo',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    NULL,
    '18',
    NULL,
    'SELECCIONABLE',
    NULL);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
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
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION)
VALUES(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT apc.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB apc WHERE apc.NOMBRE_PARAMETRO = 'CARACTERISTICAS_PROD_ADICIONALES_TM_COMERCIAL' AND apc.ESTADO = 'Activo'),
    'CAPACIDAD1',
    'KO01',
    '11716',
    'NO',
    'NO',
    'Activo',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    '1;2',
    '18',
    '2',
    'SELECCIONABLE',
NULL);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
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
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION)
VALUES(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT apc.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB apc WHERE apc.NOMBRE_PARAMETRO = 'CARACTERISTICAS_PROD_ADICIONALES_TM_COMERCIAL' AND apc.ESTADO = 'Activo'),
    'CORREO ELECTRONICO',
    'IPMP',
    '838',
    'SI',
    'SI',
    'Activo',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    NULL,
    '18',
    NULL,
    'EMAIL',
NULL);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
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
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION)
VALUES(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT apc.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB apc WHERE apc.NOMBRE_PARAMETRO = 'CARACTERISTICAS_PROD_ADICIONALES_TM_COMERCIAL' AND apc.ESTADO = 'Activo'),
    'ES_GRATIS',
    'EXDB',
    '11339',
    'SI',
    'SI',
    'Activo',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    'SI;NO',
    '18',
    'NO',
    'SELECCIONABLE',
    NULL);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
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
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION)
VALUES(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT apc.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB apc WHERE apc.NOMBRE_PARAMETRO = 'CARACTERISTICAS_PROD_ADICIONALES_TM_COMERCIAL' AND apc.ESTADO = 'Activo'),
    'ES_GRATIS',
    'WFDB',
    '11338',
    'SI',
    'SI',
    'Activo',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    'SI;NO',
    '18',
    'NO',
    'SELECCIONABLE',
    NULL);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
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
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION)
VALUES(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT apc.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB apc WHERE apc.NOMBRE_PARAMETRO = 'CARACTERISTICAS_PROD_ADICIONALES_TM_COMERCIAL' AND apc.ESTADO = 'Activo'),
    'MAC',
    'EXDB',
    '11400',
    'NO',
    'NO',
    'Activo',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    NULL,
    '18',
    NULL,
    'TEXTO',
NULL);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
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
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION)
VALUES(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT apc.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB apc WHERE apc.NOMBRE_PARAMETRO = 'CARACTERISTICAS_PROD_ADICIONALES_TM_COMERCIAL' AND apc.ESTADO = 'Activo'),
    'METRAJE_NETFIBER',
    'NETFIB',
    '10861',
    'NO',
    'NO',
    'Activo',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    NULL,
    '18',
    NULL,
    'NUMERO',
NULL);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
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
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION)
VALUES(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT apc.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB apc WHERE apc.NOMBRE_PARAMETRO = 'CARACTERISTICAS_PROD_ADICIONALES_TM_COMERCIAL' AND apc.ESTADO = 'Activo'),
    'OFFICE',
    '1612',
    '2774',
    'NO',
    'NO',
    'Activo',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    '0;1',
    '18',
    '1',
    'TEXTO',
NULL);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
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
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION)
VALUES(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT apc.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB apc WHERE apc.NOMBRE_PARAMETRO = 'CARACTERISTICAS_PROD_ADICIONALES_TM_COMERCIAL' AND apc.ESTADO = 'Activo'),
    'TIEMPO CONEXION',
    'NL01',
    '967',
    'NO',
    'NO',
    'Activo',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    NULL,
    '18',
    NULL,
    'NUMERO',
    NULL);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
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
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION)
VALUES(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT apc.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB apc WHERE apc.NOMBRE_PARAMETRO = 'CARACTERISTICAS_PROD_ADICIONALES_TM_COMERCIAL' AND apc.ESTADO = 'Activo'),
    'TIENE INTERNET',
    'KO01',
    '11715',
    'SI',
    'SI',
    'Activo',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    'SI;NO',
    '18',
    'SI',
    'SELECCIONABLE',
NULL);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
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
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION)
VALUES(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT apc.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB apc WHERE apc.NOMBRE_PARAMETRO = 'CARACTERISTICAS_PROD_ADICIONALES_TM_COMERCIAL' AND apc.ESTADO = 'Activo'),
    'TIENE INTERNET',
    'KO02',
    '11719',
    'NO',
    'NO',
    'Activo',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    'SI;NO',
    '18',
    'NO',
    'SELECCIONABLE',
    NULL);
                                                                    
INSERT INTO
    DB_GENERAL.ADMI_PARAMETRO_DET (
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
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION)
VALUES(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT apc.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB apc WHERE apc.NOMBRE_PARAMETRO = 'CARACTERISTICAS_PROD_ADICIONALES_TM_COMERCIAL' AND apc.ESTADO = 'Activo'),
    'TIENE INTERNET',
    'IPMP',
    '844',
    'SI',
    'SI',
    'Activo',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    'SI;NO',
    '18',
    'SI',
    'SELECCIONABLE',
    NULL);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
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
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION)
VALUES(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT apc.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB apc WHERE apc.NOMBRE_PARAMETRO = 'CARACTERISTICAS_PROD_ADICIONALES_TM_COMERCIAL' AND apc.ESTADO = 'Activo'),
    'TIENE INTERNET',
    'ASSI',
    '7584',
    'SI',
    'SI',
    'Activo',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    'SI;NO',
    '18',
    'SI',
    'SELECCIONABLE',
    NULL);

INSERT INTO
    DB_GENERAL.ADMI_PARAMETRO_DET (
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
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION)
VALUES(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT apc.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB apc WHERE apc.NOMBRE_PARAMETRO = 'CARACTERISTICAS_PROD_ADICIONALES_TM_COMERCIAL' AND apc.ESTADO = 'Activo'),
    'USUARIO',
    'KO02',
    '11718',
    'NO',
    'NO',
    'Activo',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    NULL,
    '18',
    NULL,
    'TEXTO',
    NULL);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
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
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION)
VALUES(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT apc.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB apc WHERE apc.NOMBRE_PARAMETRO = 'CARACTERISTICAS_PROD_ADICIONALES_TM_COMERCIAL' AND apc.ESTADO = 'Activo'),
    'USUARIO',
    'KO01',
    '11717',
    'NO',
    'NO',
    'Activo',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    'cjaramilloe',
    SYSDATE,
    '127.0.0.1',
    NULL,
    '18',
    NULL,
    'TEXTO',
    NULL);


UPDATE
    DB_FIRMAELECT.ADM_EMP_PLANT_CERT aepc
SET
    aepc.PROPIEDADES = '{"llx":"-100","lly":"15","urx":"100","ury":"35","pagina":"3","textSignature":"","modoPresentacion":"1","firma":"SI"}'
WHERE
    aepc.PLANTILLA_ID = (SELECT t.ID_EMPRESA_PLANTILLA FROM DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA t WHERE t.cod_plantilla = 'contratoMegadatos' AND t.estado = 'Activo') AND
    aepc.TIPO = 'cliente' AND
    aepc.CODIGO = 'FIRMA_CONT_MD_FINAL_CLIENTE';

UPDATE
    DB_FIRMAELECT.ADM_EMP_PLANT_CERT aepc
SET
    aepc.PROPIEDADES = '{"llx":"-100","lly":"15","urx":"100","ury":"35","pagina":"4","textSignature":"","modoPresentacion": "1","firma":"SI"}'
    
WHERE
    aepc.PLANTILLA_ID = (SELECT t.ID_EMPRESA_PLANTILLA FROM DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA t WHERE t.cod_plantilla = 'contratoMegadatos' AND t.estado = 'Activo') AND
    aepc.TIPO = 'cliente' AND
    aepc.CODIGO = 'FIRMA_CONT_MD_FORMA_PAGO';

UPDATE
    DB_FIRMAELECT.ADM_EMP_PLANT_CERT aepc
SET
    aepc.PROPIEDADES = '{"llx":"-100","lly":"15","urx":"100","ury":"35","pagina":"3","textSignature":"","modoPresentacion":"1","firma":"SI"}'
WHERE
    aepc.PLANTILLA_ID = (SELECT t.ID_EMPRESA_PLANTILLA FROM DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA t WHERE t.cod_plantilla = 'contratoMegadatos' AND t.estado = 'Activo') AND
    aepc.TIPO = 'empresa' AND
    aepc.CODIGO = 'FIRMA_CONT_MD_FINAL_EMPRESA';

UPDATE
    DB_FIRMAELECT.ADM_EMP_PLANT_CERT aepc
SET
    aepc.PROPIEDADES = '{"llx":"-70","lly":"-30","urx":"200","ury":"-10","pagina":"1","textSignature":"","modoPresentacion":"1", "firma":"SI"}'
    
WHERE
    aepc.PLANTILLA_ID = (SELECT t.ID_EMPRESA_PLANTILLA FROM DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA t WHERE t.cod_plantilla = 'formularioSecurityData' AND t.estado = 'Activo') AND
    aepc.TIPO = 'cliente' AND
    aepc.CODIGO = 'FIRMA_FORM_SD_CLIENTE';

UPDATE
    DB_FIRMAELECT.ADM_EMP_PLANT_CERT aepc
SET
    aepc.PROPIEDADES = '{"llx":"-80","lly":"15","urx":"150","ury":"35","pagina":"3","textSignature":"","modoPresentacion":"1", "firma":"SI"}'
WHERE
    aepc.PLANTILLA_ID = (SELECT t.ID_EMPRESA_PLANTILLA FROM DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA t WHERE t.cod_plantilla = 'contratoSecurityData' AND t.estado = 'Activo') AND
    aepc.TIPO = 'cliente' AND
    aepc.CODIGO = 'FIRMA_CONT_SD_CLIENTE';

UPDATE
    DB_FIRMAELECT.ADM_EMP_PLANT_CERT aepc
SET
    aepc.PROPIEDADES = '{"llx":"-160","lly":"15","urx":"100","ury":"35","pagina":"3","textSignature":"","modoPresentacion":"1", "firma":"SI"}'
WHERE
    aepc.PLANTILLA_ID = (SELECT t.ID_EMPRESA_PLANTILLA FROM DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA t WHERE t.cod_plantilla = 'contratoSecurityData' AND t.estado = 'Activo') AND
    aepc.TIPO = 'empresa' AND
    aepc.CODIGO = 'FIRMA_CONT_SD_EMPRESA';

UPDATE
    DB_FIRMAELECT.ADM_EMP_PLANT_CERT aepc
SET
    aepc.PROPIEDADES = '{"llx":"-85","lly":"15","urx":"150","ury":"35","pagina":"1","textSignature":"","modoPresentacion":"1","firma":"SI"}'
WHERE
    aepc.PLANTILLA_ID = (SELECT t.ID_EMPRESA_PLANTILLA FROM DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA t WHERE t.cod_plantilla = 'debitoMegadatos' AND t.estado = 'Activo') AND
    aepc.TIPO = 'cliente' AND 
    aepc.CODIGO = 'FIRMA_CONT_MD_AUT_DEBITO';

UPDATE
    DB_FIRMAELECT.ADM_EMP_PLANT_CERT  aepc
SET
    PROPIEDADES = '{"llx":"50","lly":"5","urx":"250","ury":"25","pagina":"1","textSignature":"","modoPresentacion":"1", "firma":"SI"}'
WHERE
    aepc.PLANTILLA_ID = (SELECT t.ID_EMPRESA_PLANTILLA FROM DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA t WHERE t.cod_plantilla = 'pagareMegadatos' AND t.estado = 'Activo') AND
    aepc.TIPO = 'cliente' AND
    aepc.CODIGO = 'FIRMA_CONT_MD_PAGARE';

UPDATE
    DB_FIRMAELECT.ADM_EMP_PLANT_CERT aepc
SET
    aepc.PROPIEDADES = '{"llx":"-100","lly":"15","urx":"100","ury":"35","pagina":"2","textSignature":"","modoPresentacion":"1", "firma":"SI"}'
WHERE
    aepc.PLANTILLA_ID = (SELECT t.ID_EMPRESA_PLANTILLA FROM DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA t WHERE t.cod_plantilla = 'adendumMegaDatos' AND t.estado = 'Activo') AND
    aepc.TIPO = 'cliente' AND 
    aepc.CODIGO = 'FIRMA_ADEN_MD_CLIENTE';

UPDATE
    DB_FIRMAELECT.ADM_EMP_PLANT_CERT aepc
SET
    aepc.PROPIEDADES = '{"llx":"-100","lly":"15","urx":"100","ury":"35","pagina":"2","textSignature":"","modoPresentacion":"1", "firma":"SI"}'
WHERE
    aepc.PLANTILLA_ID = (SELECT t.ID_EMPRESA_PLANTILLA FROM DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA t WHERE t.cod_plantilla = 'adendumMegaDatos' AND t.estado = 'Activo') AND
    aepc.TIPO = 'empresa' AND 
    aepc.CODIGO = 'FIRMA_ADEN_MD_EMPRESA';

COMMIT;
/