--ELIMINAMOS DETALLE

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
    AND VALOR1 = 'OP_RESTRINGIDO_AL_VALIDAR_ESTADO_TAREA';

COMMIT;