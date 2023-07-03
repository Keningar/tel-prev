/**
 * Inserts de parámetros contrato digital persona juridica
 * 
 * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
 * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
 *
 * @version 1.0
 * @since 15-06-2020
 */

--CONFIGURACION PARA CREAR CERTIFICADO CON SD
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
VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'CONFIGURACION_WS_SD' and ESTADO = 'Activo'),
    'PARAMSQUERYJUR', 
    'S3curity', 
    'http://186.5.63.212:8080/CertificadoElectronicoClienteMegadatos/webresources/usuario_rl/emision_rl',
    'p12', 
    '/archivo_cert', 
    'Activo', 
    'jnazareno', 
    sysdate, 
    '127.0.0.1', 
    NULL,
    NULL, 
    NULL, 
    '186.5.63.212', 
    '18', 
    'archivo_cert', 
    'rch#PFX2015', 
    NULL);

--CONFIGURACION PARA CONEXION SFTP CON SD
INSERT INTO DB_SFTP.ADMI_CONFIGURACION(
    ID_CONFIGURACION, 
    NOMBRE, 
    DESCRIPCION, 
    HOST_SFTP, 
    USER_SFTP, 
    CLAVE_SFTP, 
    PUERTO_SFTP, 
    TIME_OUT, 
    USR_CREACION, 
    FE_CREACION, 
    IP_CREACION, 
    USR_ULT_MOD, 
    FE_ULT_MOD, 
    IP_ULT_MOD)
VALUES(
    DB_SFTP.SEQ_ADMI_CONFIGURACION.NEXTVAL, 
    'SECURITYDATA_SERVER', 
    'ACCESO FTP A SECURITY DATA', 
    '186.5.63.212', 
    'archivo_cert', 
    'rch#PFX2015', 
    22, 
    30000, 
    'cjaramilloe',
    SYSDATE, 
    '127.0.0.0', 
    NULL, 
    NULL, 
    NULL);

 COMMIT;
/
