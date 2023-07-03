--=======================================================================
-- Reverso de los detalles y cabecera del parametro 'VALORES_VRF_TELCONET'
--=======================================================================

DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'VALORES_VRF_TELCONET'
    );
DELETE DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE
    NOMBRE_PARAMETRO = 'VALORES_VRF_TELCONET';

COMMIT;
/
