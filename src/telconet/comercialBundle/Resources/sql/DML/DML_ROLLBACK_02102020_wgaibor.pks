DELETE FROM db_comunicacion.admi_tipo_documento atdc
WHERE atdc.EXTENSION_TIPO_DOCUMENTO = 'p12';
--
DELETE FROM db_general.admi_tipo_documento_general atdg
WHERE ATDG.CODIGO_TIPO_DOCUMENTO = 'CDI';
--
DELETE FROM db_general.admi_parametro_det apdt
WHERE apdt.PARAMETRO_ID = (SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'CONTRATO_ARCHIVOS_NO_VISIBLE'
            AND estado = 'Activo');
--
DELETE FROM db_general.admi_parametro_cab
WHERE nombre_parametro = 'CONTRATO_ARCHIVOS_NO_VISIBLE'
AND estado = 'Activo';
--
DELETE FROM db_general.admi_parametro_det apdt
WHERE apdt.PARAMETRO_ID = (SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'BANDERA_NFS'
            AND estado = 'Activo');
--
DELETE FROM db_general.admi_parametro_cab
WHERE nombre_parametro = 'BANDERA_NFS'
AND estado = 'Activo';
--======================================================

COMMIT;
/
