/**
 * Documentación INSERT DE PARÁMETROS DE CONSUMO DE WEB SERVICES DE SECURITY DATA.
 * INSERT de parámetros en la estructura  DB_GENERAL.ADMI_PARAMETRO_CAB y DB_GENERAL.ADMI_PARAMETRO_DET.
 *
 * Se insertan parámetros para saber la configuración que tendrá el consumo para la generación de certificados digitales
 *
 * @author Edgar Pin Villavicencio <epin@telconet.ec>
 * @version 1.0 08-04-2020
 */
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB (ID_PARAMETRO, NOMBRE_PARAMETRO, DESCRIPCION, MODULO, PROCESO, ESTADO, USR_CREACION, FE_CREACION, 
            IP_CREACION , USR_ULT_MOD , FE_ULT_MOD, IP_ULT_MOD)
VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL, 'CONFIGURACION_WS_SD', 
        'CONFIGURACION PARA EL CONSUMO DE WEB SERVICES DE SECURITY DATA PARA LA CREACION DE CERTIFICADOS DIGITALES',
        'COMERCIAL', 'CERTIFICADO', 'Activo', 'epin', sysdate, '127.0.0.1', NULL, NULL, NULL);
    
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, ESTADO, 
                                           USR_CREACION, FE_CREACION, IP_CREACION, USR_ULT_MOD, FE_ULT_MOD, IP_ULT_MOD, VALOR5, 
                                           EMPRESA_COD, VALOR6, VALOR7, OBSERVACION)
VALUES (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO
         FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'CONFIGURACION_WS_SD'),
        'PARAMSQUERY', 'PARAMSQUERY', 'http://186.5.63.212:8080/CertificadoElectronicoClienteMegadatos/webresources/usuario_pn/emision_pn',
        'p12', '/archivo_cert', 'Activo', 'epin', sysdate, '127.0.0.1', NULL, NULL, NULL, '186.5.63.212', '18', 'archivo_cert', 
        'rch#PFX2015', NULL );
