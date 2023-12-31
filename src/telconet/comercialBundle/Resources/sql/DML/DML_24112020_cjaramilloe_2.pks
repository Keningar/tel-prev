/**
 *
 * Inserción de parámetros en ADMI_PARAMETRO_CAB Y ADMI_PARAMETROD_DET
 * para código promocional de TM Comercial
 *
 * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
 * @version 1.0 02-06-2020
 * 
 **/

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET( 
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
VALUES (
     DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
     (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PARAMETROS_TM_COMERCIAL' AND ESTADO = 'Activo'),
     'ACTIVA_PAGINACION_PUNTOS',
     'N',
     NULL,
     NULL, 
     NULL, 
     'Activo', 
     'cjaramilloe', 
     SYSDATE, 
     '127.0.0.1', 
     NULL, 
     NULL, 
     NULL, 
     NULL, 
     '18', 
     NULL, 
     NULL, 
     'S: Se activa paginación de puntos en TM Comercial, N: No se activa paginación de puntos en TM Comercial');

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET( 
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
VALUES (
     DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 
     (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PARAMETROS_TM_COMERCIAL' AND ESTADO = 'Activo'),
     'PUNTOS_POR_PAGINA',
     '10',
     NULL,
     NULL,
     NULL,
     'Activo',
     'cjaramilloe',
     SYSDATE,
     '127.0.0.1',
     NULL,
     NULL,
     NULL,
     NULL,
     '18',
     NULL,
     NULL,
     'Cantidad de puntos por página en TM Comercial');

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
VALUES (
     DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
     'PROMOCIONES_APLICABLES_TM_COMERCIAL',
     'CONFIGURACION CODIGO PROMOCIONAL TM COMERCIAL',
     'COMERCIAL',
     'CONTRATO_DIGITAL',
     'Activo',
     'cjaramilloe',
     SYSDATE,
     '127.0.0.1',
     NULL,
     NULL,
     NULL);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
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
VALUES (
     DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
     (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PROMOCIONES_APLICABLES_TM_COMERCIAL' AND ESTADO = 'Activo'),
     'CODIGOPROMO_INSTALACION',
     NULL,
     NULL,
     'PLANES',
     'PROM_INS',
     'Activo',
     'cjaramilloe',
     SYSDATE,
     '127.0.0.1',
     NULL,
     NULL,
     NULL,
     'Cod. promocional por instalación',
     '18',
     'package',
     '1',
     'Valor1: id de los planes concatenados con ; si valor2 es diferente de NULL, Valor2: Operacion INCLUIR_SOLO, EXCLUIR_SOLO o NULL, Valor3: tipo de servicio en telcos, Valor4: Nombre de la promo en movil, Valor6: tipo de servicio en movil,  Valor7: orden de lista de la promo en movil');

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
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
VALUES (
     DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
     (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PROMOCIONES_APLICABLES_TM_COMERCIAL' AND ESTADO = 'Activo'),
     'CODIGOPROMO_MENSUALIDAD',
     NULL,
     NULL,
     'PLANES',
     'PROM_MENS',
     'Activo',
     'cjaramilloe',
     SYSDATE,
     '127.0.0.1',
     NULL,
     NULL,
     NULL,
     'Cod. promocional por mensualidad',
     '18',
     'package',
     '2',
     'Valor1: id de los planes concatenados con ; si valor2 es diferente de NULL, Valor2: Operacion INCLUIR_SOLO, EXCLUIR_SOLO o NULL, Valor3: tipo de servicio en telcos, Valor4: Nombre de la promo en movil, Valor6: tipo de servicio en movil,  Valor7: orden de lista de la promo en movil');

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
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
VALUES (
     DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
     (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PROMOCIONES_APLICABLES_TM_COMERCIAL' AND ESTADO = 'Activo'),
     'CODIGOPROMO_ANCHODEBANDA',
     NULL,
     NULL,
     'PLANES',
     'PROM_BW',
     'Activo',
     'cjaramilloe',
     SYSDATE,
     '127.0.0.1',
     NULL,
     NULL,
     NULL,
     'Cod. promocional por ancho de banda',
     '18',
     'package',
     '3',
     'Valor1: id de los planes concatenados con ; si valor2 es diferente de NULL, Valor2: Operacion INCLUIR_SOLO, EXCLUIR_SOLO o NULL, Valor3: tipo de servicio en telcos, Valor4: Nombre de la promo en movil, Valor6: tipo de servicio en movil,  Valor7: orden de lista de la promo en movil');

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
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
VALUES (
     DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
     (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PROMOCIONES_APLICABLES_TM_COMERCIAL' AND ESTADO = 'Activo'),
     'CODIGOPROMO_INSTALACION',
     NULL,
     NULL,
     'PROD_SERV_ADICIONAL',
     'PROM_INS',
     'Activo',
     'cjaramilloe',
     SYSDATE,
     '127.0.0.1',
     NULL,
     NULL,
     NULL,
     'Cod. promocional por instalación',
     '18',
     'product',
     '1',
     'Valor1: id de los productos concatenados con ; si valor2 es diferente de NULL, Valor2: Operacion INCLUIR_SOLO, EXCLUIR_SOLO o NULL, Valor3: tipo de servicio en telcos, Valor4: Nombre de la promo en movil, Valor6: tipo de servicio en movil,  Valor7: orden de lista de la promo en movil');

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
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
VALUES (
     DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
     (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PROMOCIONES_APLICABLES_TM_COMERCIAL' AND ESTADO = 'Activo'),
     'CODIGOPROMO_MENSUALIDAD',
     NULL,
     NULL,
     'PROD_SERV_ADICIONAL',
     'PROM_MENS',
     'Activo',
     'cjaramilloe',
     SYSDATE,
     '127.0.0.1',
     NULL,
     NULL,
     NULL,
     'Cod. promocional por mensualidad',
     '18',
     'product',
     '2',
     'Valor1: id de los productos concatenados con ; si valor2 es diferente de NULL, Valor2: Operacion INCLUIR_SOLO, EXCLUIR_SOLO o NULL, Valor3: tipo de servicio en telcos, Valor4: Nombre de la promo en movil, Valor6: tipo de servicio en movil,  Valor7: orden de lista de la promo en movil');

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET(
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
VALUES (
     DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
     (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PROMOCIONES_APLICABLES_TM_COMERCIAL' AND ESTADO = 'Activo'),
     'CODIGOPROMO_ANCHODEBANDA',
     NULL,
     NULL,
     'PROD_SERV_ADICIONAL',
     'PROM_BW',
     'Activo',
     'cjaramilloe',
     SYSDATE,
     '127.0.0.1',
     NULL,
     NULL,
     NULL,
     'Cod. promocional por ancho de banda',
     '18',
     'product',
     '3',
     'Valor1: id de los productos concatenados con ; si valor2 es diferente de NULL, Valor2: Operacion INCLUIR_SOLO, EXCLUIR_SOLO o NULL, Valor3: tipo de servicio en telcos, Valor4: Nombre de la promo en movil, Valor6: tipo de servicio en movil,  Valor7: orden de lista de la promo en movil');

COMMIT;
/