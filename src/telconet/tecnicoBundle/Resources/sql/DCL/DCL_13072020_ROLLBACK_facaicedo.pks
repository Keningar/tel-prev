--=======================================================================
-- Reverso los detalles de parámetros para los estados que deben poseer las tareas para ser generadas en la validación del BW de la interface
--=======================================================================

-- REVERSO DE LOS DETALLES DE PARAMETROS DE 'TIPOS_ELEMENTOS_BW_INTERFACE'
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'ESTADOS_TAREAS_FINALIZADAS_VALIDAR_MAXIMO_BW_INTERFACE'
    );
DELETE DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE
    NOMBRE_PARAMETRO = 'ESTADOS_TAREAS_FINALIZADAS_VALIDAR_MAXIMO_BW_INTERFACE';

COMMIT;
/
