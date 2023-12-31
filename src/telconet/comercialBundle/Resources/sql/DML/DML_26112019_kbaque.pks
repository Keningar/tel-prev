/**
 *
 * Se realiza la creación de parametros para poder listar los tipos
 * de autorizaciones que manejará la ventana de Autorización.
 * @author Kevin Baque Puya <kbaque@telconet.ec>
 * @version 1.0 16-12-2019
 */

--Cabecera
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
    (ID_PARAMETRO,NOMBRE_PARAMETRO,DESCRIPCION,MODULO,PROCESO,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
VALUES
    (DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'TIPOS_AUTORIZACIONES',
    'TIPOS DE DESCUENTOS QUE SE PODRAN AUTORIZAR DESDE EL MODULO COMERCIAL GESTION DESCUENTOS',
    'COMERCIAL',
    'AUTORIZACION_DESCUENTOS',
    'Activo',
    'kbaque',
    sysdate,
    '127.0.0.1');

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
    (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,EMPRESA_COD)
VALUES
    (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO='TIPOS_AUTORIZACIONES'),
    'PARAMETRO DE LOS TIPOS DE DESCUENTOS QUE SE AUTORIZAN',
    'Autorización Descuento',
    'AUTORIZACION_DESCUENTO',
    'TN',
    'Activo',
    'kbaque',
    sysdate,
    '127.0.0.1',
    10);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
    (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,EMPRESA_COD)
VALUES
    (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO='TIPOS_AUTORIZACIONES'),
    'PARAMETRO DE LOS TIPOS DE DESCUENTOS QUE SE AUTORIZAN',
    'Autorización Instalación',
    'AUTORIZACION_INSTALACION',
    'TN',
    'Activo',
    'kbaque',
    sysdate,
    '127.0.0.1',
    10);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
    (ID_PARAMETRO_DET,PARAMETRO_ID,DESCRIPCION,VALOR1,VALOR2,VALOR3,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,EMPRESA_COD)
VALUES
    (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO='TIPOS_AUTORIZACIONES'),
    'PARAMETRO DE LOS TIPOS DE DESCUENTOS QUE SE AUTORIZAN',
    'Autorización cambio documento',
    'AUTORIZACION_CAMBIO_DOCUMENTO',
    'TN',
    'Activo',
    'kbaque',
    sysdate,
    '127.0.0.1',
    10);
COMMIT;
/