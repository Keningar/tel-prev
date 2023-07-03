DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET DETALLE
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'PARAMETROS_GENERALES_MOVIL'
    )
    AND VALOR1 = 'CANTIDAD_TOKEN_SEGURIDAD';

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET DETALLE
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'PARAMETROS_GENERALES_MOVIL'
    )
    AND VALOR1 = 'TIEMPO_MAXIMO_SESION';

--ELIMINAMOS DETALLE
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET DETALLE
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'MENSAJES_TM_OPERACIONES'
    )
    AND VALOR1 = 'MSG_ERROR_GETARRAYSECURITYTOKENS';

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET DETALLE
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'MENSAJES_TM_OPERACIONES'
    )
    AND VALOR1 = 'MSG_OK_GUARDARIMAGENESSINCRONO';

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET DETALLE
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'MENSAJES_TM_OPERACIONES'
    )
    AND VALOR1 = 'MSG_ERROR_GUARDARIMAGENESSINCRONO';

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET DETALLE
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'MENSAJES_TM_OPERACIONES'
    )
    AND VALOR1 = 'MSG_ERROR_GUARDARPROGRESO';

COMMIT;
