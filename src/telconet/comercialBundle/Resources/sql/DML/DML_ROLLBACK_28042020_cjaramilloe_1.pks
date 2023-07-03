/**
 *
 * Documentación
 * 
 * Rollback de actualización versión móvil TM Comercial.
 *
 * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
 * @version 1.0 28-04-2020
 *
 **/
 
SET DEFINE OFF;

UPDATE DB_COMERCIAL.ADMI_PRODUCTO AP
	SET AP.DESCRIPCION_PRODUCTO = 'NetlifeAssistance KB'
	WHERE AP.DESCRIPCION_PRODUCTO = 'Netlife Assistance Pro' 
	AND AP.EMPRESA_COD='18' 
	AND AP.CODIGO_PRODUCTO='KO01' 
	AND ESTADO = 'Activo';

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET APT
    WHERE APT.DESCRIPCION = 'RESTRICCION_NO_EMPLEADO'
    AND APT.ESTADO = 'Activo'
    AND APT.EMPRESA_COD = '18'
    AND APT.USR_CREACION = 'cjaramilloe';

UPDATE
    DB_GENERAL.ADMI_PARAMETRO_DET
SET
    DESCRIPCION = 'Renta AP WIFI',
    VALOR1 = '18',
    VALOR2 = NULL,
    VALOR3 = NULL,
    VALOR4 = NULL,
    ESTADO = 'Activo',
    USR_ULT_MOD = NULL,
    FE_ULT_MOD = NULL,
    IP_ULT_MOD = NULL,
    VALOR5 = NULL,
    EMPRESA_COD = NULL,
    VALOR6 = NULL,
    VALOR7 = NULL,
    OBSERVACION = NULL
WHERE
    PARAMETRO_ID = (SELECT APC.ID_PARAMETRO
                        FROM DB_GENERAL.ADMI_PARAMETRO_CAB APC
                        WHERE APC.NOMBRE_PARAMETRO = 'PRODUCTOS_TM_COMERCIAL'
                            AND APC.ESTADO = 'Activo')
    AND ID_PARAMETRO_DET = (SELECT APD.ID_PARAMETRO_DET 
                                FROM DB_GENERAL.ADMI_PARAMETRO_DET APD 
                                WHERE APD.VALOR2 = 'prodRentaWifiDualBand' 
                                    AND APD.USR_ULT_MOD = 'cjaramilloe');

UPDATE
    DB_GENERAL.ADMI_PARAMETRO_DET
SET
    DESCRIPCION = 'Netlife Zone',
    VALOR1 = '18',
    VALOR2 = NULL,
    VALOR3 = NULL,
    VALOR4 = NULL,
    ESTADO = 'Activo',
    USR_ULT_MOD = NULL,
    FE_ULT_MOD = NULL,
    IP_ULT_MOD = NULL,
    VALOR5 = NULL,
    EMPRESA_COD = NULL,
    VALOR6 = NULL,
    VALOR7 = NULL,
    OBSERVACION = NULL
WHERE
    PARAMETRO_ID = (SELECT APC.ID_PARAMETRO
                        FROM DB_GENERAL.ADMI_PARAMETRO_CAB APC
                        WHERE APC.NOMBRE_PARAMETRO = 'PRODUCTOS_TM_COMERCIAL'
                            AND APC.ESTADO = 'Activo')
    AND ID_PARAMETRO_DET = (SELECT APD.ID_PARAMETRO_DET 
                                FROM DB_GENERAL.ADMI_PARAMETRO_DET APD 
                                WHERE APD.VALOR2 = 'prodNetlifeZone' 
                                    AND APD.USR_ULT_MOD = 'cjaramilloe');

UPDATE
    DB_GENERAL.ADMI_PARAMETRO_DET
SET
    DESCRIPCION = 'NetlifeAssistance',
    VALOR1 = '18',
    VALOR2 = NULL,
    VALOR3 = NULL,
    VALOR4 = NULL,
    ESTADO = 'Activo',
    USR_ULT_MOD = NULL,
    FE_ULT_MOD = NULL,
    IP_ULT_MOD = NULL,
    VALOR5 = NULL,
    EMPRESA_COD = NULL,
    VALOR6 = NULL,
    VALOR7 = NULL,
    OBSERVACION = NULL
WHERE
    PARAMETRO_ID = (SELECT APC.ID_PARAMETRO
                        FROM DB_GENERAL.ADMI_PARAMETRO_CAB APC
                        WHERE APC.NOMBRE_PARAMETRO = 'PRODUCTOS_TM_COMERCIAL'
                            AND APC.ESTADO = 'Activo')
    AND ID_PARAMETRO_DET = (SELECT APD.ID_PARAMETRO_DET 
                                FROM DB_GENERAL.ADMI_PARAMETRO_DET APD 
                                WHERE APD.VALOR2 = 'prodNetlifeAssistance' 
                                    AND APD.USR_ULT_MOD = 'cjaramilloe');

UPDATE
    DB_GENERAL.ADMI_PARAMETRO_DET
SET
    DESCRIPCION = 'NetlifeCloud',
    VALOR1 = '18',
    VALOR2 = NULL,
    VALOR3 = NULL,
    VALOR4 = NULL,
    ESTADO = 'Activo',
    USR_ULT_MOD = NULL,
    FE_ULT_MOD = NULL,
    IP_ULT_MOD = NULL,
    VALOR5 = NULL,
    EMPRESA_COD = NULL,
    VALOR6 = NULL,
    VALOR7 = NULL,
    OBSERVACION = NULL
WHERE
    PARAMETRO_ID = (SELECT APC.ID_PARAMETRO
                        FROM DB_GENERAL.ADMI_PARAMETRO_CAB APC
                        WHERE APC.NOMBRE_PARAMETRO = 'PRODUCTOS_TM_COMERCIAL'
                            AND APC.ESTADO = 'Activo')
    AND ID_PARAMETRO_DET = (SELECT APD.ID_PARAMETRO_DET 
                                FROM DB_GENERAL.ADMI_PARAMETRO_DET APD 
                                WHERE APD.VALOR2 = 'prodNetlifeCloud' 
                                    AND APD.USR_ULT_MOD = 'cjaramilloe');

UPDATE
    DB_GENERAL.ADMI_PARAMETRO_DET
SET
    DESCRIPCION = 'NETLIFECAM - Servicio Básico de Visualización Remota Residencial',
    VALOR1 = '18',
    VALOR2 = NULL,
    VALOR3 = NULL,
    VALOR4 = NULL,
    ESTADO = 'Activo',
    USR_ULT_MOD = NULL,
    FE_ULT_MOD = NULL,
    IP_ULT_MOD = NULL,
    VALOR5 = NULL,
    EMPRESA_COD = NULL,
    VALOR6 = NULL,
    VALOR7 = NULL,
    OBSERVACION = NULL
WHERE
    PARAMETRO_ID = (SELECT APC.ID_PARAMETRO
                        FROM DB_GENERAL.ADMI_PARAMETRO_CAB APC
                        WHERE APC.NOMBRE_PARAMETRO = 'PRODUCTOS_TM_COMERCIAL'
                            AND APC.ESTADO = 'Activo')
    AND ID_PARAMETRO_DET = (SELECT APD.ID_PARAMETRO_DET 
                                FROM DB_GENERAL.ADMI_PARAMETRO_DET APD 
                                WHERE APD.VALOR2 = 'prodNetlifeCam' 
                                    AND APD.USR_ULT_MOD = 'cjaramilloe');

UPDATE
    DB_GENERAL.ADMI_PARAMETRO_DET
SET
    DESCRIPCION = 'I. PROTEGIDO MULTI PAID',
    VALOR1 = '18',
    VALOR2 = NULL,
    VALOR3 = NULL,
    VALOR4 = NULL,
    ESTADO = 'Activo',
    USR_ULT_MOD = NULL,
    FE_ULT_MOD = NULL,
    IP_ULT_MOD = NULL,
    VALOR5 = NULL,
    EMPRESA_COD = NULL,
    VALOR6 = NULL,
    VALOR7 = NULL,
    OBSERVACION = NULL
WHERE
    PARAMETRO_ID = (SELECT APC.ID_PARAMETRO
                        FROM DB_GENERAL.ADMI_PARAMETRO_CAB APC
                        WHERE APC.NOMBRE_PARAMETRO = 'PRODUCTOS_TM_COMERCIAL'
                            AND APC.ESTADO = 'Activo')
    AND ID_PARAMETRO_DET = (SELECT APD.ID_PARAMETRO_DET 
                                FROM DB_GENERAL.ADMI_PARAMETRO_DET APD 
                                WHERE APD.VALOR2 = 'prodNetlifeDefense' 
                                    AND APD.USR_ULT_MOD = 'cjaramilloe');

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET APT
WHERE APT.PARAMETRO_ID = (SELECT APC.ID_PARAMETRO
                          FROM DB_GENERAL.ADMI_PARAMETRO_CAB APC
                          WHERE APC.NOMBRE_PARAMETRO = 'PRODUCTOS_TM_COMERCIAL'
                            AND APC.ESTADO = 'Activo')
    AND APT.ESTADO = 'Activo'
    AND APT.EMPRESA_COD = '18'
    AND APT.USR_CREACION = 'cjaramilloe';
    
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB APC
WHERE APC.NOMBRE_PARAMETRO = 'CARACTERISTICAS_IGNORADAS_TM_COMERCIAL'
AND APC.ESTADO = 'Activo'
AND APC.USR_CREACION = 'cjaramilloe';

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET APD
WHERE APD.PARAMETRO_ID = (SELECT APC.ID_PARAMETRO 
                          FROM DB_GENERAL.ADMI_PARAMETRO_CAB APC
                          WHERE APC.NOMBRE_PARAMETRO = 'CARACTERISTICAS_IGNORADAS_TM_COMERCIAL'
                          AND APC.ESTADO = 'Activo'
                          AND APC.USR_CREACION = 'cjaramilloe') 
AND APD.ESTADO = 'Activo'
AND APD.USR_CREACION = 'cjaramilloe';

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB APC
WHERE APC.NOMBRE_PARAMETRO = 'CARACTERISTICAS_PROD_ADICIONALES_TM_COMERCIAL'
AND APC.ESTADO = 'Activo'
AND APC.USR_CREACION = 'cjaramilloe';

UPDATE
    DB_FIRMAELECT.ADM_EMP_PLANT_CERT aepc
SET
    aepc.PROPIEDADES = '{
    "llx": "380",
    "lly": "135",
    "urx": "550",
    "ury": "165",
    "pagina": "3",
    "textSignature": "",
    "modoPresentacion": "1"
}'
WHERE
    aepc.PLANTILLA_ID = (SELECT t.ID_EMPRESA_PLANTILLA FROM DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA t WHERE t.cod_plantilla = 'contratoMegadatos' AND t.estado = 'Activo') AND
    aepc.TIPO = 'cliente' AND
    aepc.CODIGO = 'FIRMA_CONT_MD_FINAL_CLIENTE';

UPDATE
    DB_FIRMAELECT.ADM_EMP_PLANT_CERT aepc
SET
    aepc.PROPIEDADES = '{
    "llx": "90",
    "lly": "637",
    "urx": "240",
    "ury": "657",
    "pagina": "4",
    "textSignature": "",
    "modoPresentacion": "1"
}'
    
WHERE
    aepc.PLANTILLA_ID = (SELECT t.ID_EMPRESA_PLANTILLA FROM DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA t WHERE t.cod_plantilla = 'contratoMegadatos' AND t.estado = 'Activo') AND
    aepc.TIPO = 'cliente' AND
    aepc.CODIGO = 'FIRMA_CONT_MD_FORMA_PAGO';

UPDATE
    DB_FIRMAELECT.ADM_EMP_PLANT_CERT aepc
SET
    aepc.PROPIEDADES = '{
    "llx": "120",
    "lly": "135",
    "urx": "290",
    "ury": "165",
    "pagina": "3",
    "textSignature": "",
    "modoPresentacion": "1"
}'
WHERE
    aepc.PLANTILLA_ID = (SELECT t.ID_EMPRESA_PLANTILLA FROM DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA t WHERE t.cod_plantilla = 'contratoMegadatos' AND t.estado = 'Activo') AND
    aepc.TIPO = 'empresa' AND
    aepc.CODIGO = 'FIRMA_CONT_MD_FINAL_EMPRESA';

UPDATE
    DB_FIRMAELECT.ADM_EMP_PLANT_CERT aepc
SET
    aepc.PROPIEDADES = '{
    "llx": "70",
    "lly": "290",
    "urx": "280",
    "ury": "320",
    "pagina": "1",
    "textSignature": "",
    "modoPresentacion": "1"
}'
    
WHERE
    aepc.PLANTILLA_ID = (SELECT t.ID_EMPRESA_PLANTILLA FROM DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA t WHERE t.cod_plantilla = 'formularioSecurityData' AND t.estado = 'Activo') AND
    aepc.TIPO = 'cliente' AND
    aepc.CODIGO = 'FIRMA_FORM_SD_CLIENTE';

UPDATE
    DB_FIRMAELECT.ADM_EMP_PLANT_CERT aepc
SET
    aepc.PROPIEDADES = '{
    "llx": "280",
    "lly": "350",
    "urx": "450",
    "ury": "380",
    "pagina": "3",
    "textSignature": "",
    "modoPresentacion": "1"
}'
WHERE
    aepc.PLANTILLA_ID = (SELECT t.ID_EMPRESA_PLANTILLA FROM DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA t WHERE t.cod_plantilla = 'contratoSecurityData' AND t.estado = 'Activo') AND
    aepc.TIPO = 'cliente' AND
    aepc.CODIGO = 'FIRMA_CONT_SD_CLIENTE';

UPDATE
    DB_FIRMAELECT.ADM_EMP_PLANT_CERT aepc
SET
    aepc.PROPIEDADES = '{
    "llx": "80",
    "lly": "350",
    "urx": "250",
    "ury": "380",
    "pagina": "3",
    "textSignature": "",
    "modoPresentacion": "1"
}'
WHERE
    aepc.PLANTILLA_ID = (SELECT t.ID_EMPRESA_PLANTILLA FROM DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA t WHERE t.cod_plantilla = 'contratoSecurityData' AND t.estado = 'Activo') AND
    aepc.TIPO = 'empresa' AND
    aepc.CODIGO = 'FIRMA_CONT_SD_EMPRESA';

UPDATE
    DB_FIRMAELECT.ADM_EMP_PLANT_CERT aepc
SET
    aepc.PROPIEDADES = '{
    "llx": "80",
    "lly": "350",
    "urx": "250",
    "ury": "380",
    "pagina": "3",
    "textSignature": "",
    "modoPresentacion": "1"
}'
WHERE
    aepc.PLANTILLA_ID = (SELECT t.ID_EMPRESA_PLANTILLA FROM DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA t WHERE t.cod_plantilla = 'debitoMegadatos' AND t.estado = 'Activo') AND
    aepc.TIPO = 'cliente' AND 
    aepc.CODIGO = 'FIRMA_CONT_MD_AUT_DEBITO';

UPDATE
    DB_FIRMAELECT.ADM_EMP_PLANT_CERT  aepc
SET
    PROPIEDADES = '{"llx": "360","lly": "590","urx": "460","ury": "620","pagina": "1","textSignature": "","modoPresentacion": "1"}'
WHERE
    aepc.PLANTILLA_ID = (SELECT t.ID_EMPRESA_PLANTILLA FROM DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA t WHERE t.cod_plantilla = 'pagareMegadatos' AND t.estado = 'Activo') AND
    aepc.TIPO = 'cliente' AND
    aepc.CODIGO = 'FIRMA_CONT_MD_PAGARE';

UPDATE
    DB_FIRMAELECT.ADM_EMP_PLANT_CERT aepc
SET
    aepc.PROPIEDADES = '{
                "llx": "390",
                "lly": "440",
                "urx": "570",
                "ury": "470",
                "pagina": "2",
                "textSignature": "",
                "modoPresentacion": "1"
            }'
WHERE
    aepc.PLANTILLA_ID = (SELECT t.ID_EMPRESA_PLANTILLA FROM DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA t WHERE t.cod_plantilla = 'adendumMegaDatos' AND t.estado = 'Activo') AND
    aepc.TIPO = 'cliente' AND 
    aepc.CODIGO = 'FIRMA_ADEN_MD_CLIENTE';

UPDATE
    DB_FIRMAELECT.ADM_EMP_PLANT_CERT aepc
SET
    aepc.PROPIEDADES = '{
                "llx": "70",
                "lly": "440",
                "urx":  "250",
                "ury": "470",
                "pagina": "2",
                "textSignature": "",
                "modoPresentacion": "1"
            }'
WHERE
    aepc.PLANTILLA_ID = (SELECT t.ID_EMPRESA_PLANTILLA FROM DB_FIRMAELECT.ADM_EMPRESA_PLANTILLA t WHERE t.cod_plantilla = 'adendumMegaDatos' AND t.estado = 'Activo') AND
    aepc.TIPO = 'empresa' AND 
    aepc.CODIGO = 'FIRMA_ADEN_MD_EMPRESA';

COMMIT;
/