--=======================================================================
-- Reverso los detalles de parámetros para permitir la ejecución del procedimiento para el control del BW de la interface
-- Reverso los detalles de parámetros para las ejecuciones y progresos para el control del BW de la interface
-- Reverso los detalles de parámetros para los errores para el control del BW de la interface
-- Reverso los detalles de parámetros para los json con los id de los elementos para el control del BW de la interface
-- Reverso los detalles de parámetros para los json con los id de las interfaces para el control del BW de la interface
--=======================================================================

-- REVERSO DE LOS DETALLES DE PARAMETROS DE 'EJECUCION_CONTROL_BW_INTERFACE'
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'EJECUCION_CONTROL_BW_INTERFACE'
    );
DELETE DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE
    NOMBRE_PARAMETRO = 'EJECUCION_CONTROL_BW_INTERFACE';
-- REVERSO DE LOS DETALLES DE PARAMETROS DE 'PROGRESO_CONTROL_BW_INTERFACE'
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'PROGRESO_CONTROL_BW_INTERFACE'
    );
DELETE DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE
    NOMBRE_PARAMETRO = 'PROGRESO_CONTROL_BW_INTERFACE';
-- REVERSO DE LOS DETALLES DE PARAMETROS DE 'ERRORES_CONTROL_BW_INTERFACE'
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'ERRORES_CONTROL_BW_INTERFACE'
    );
DELETE DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE
    NOMBRE_PARAMETRO = 'ERRORES_CONTROL_BW_INTERFACE';
-- REVERSO DE LOS DETALLES DE PARAMETROS DE 'ELEMENTOS_ARRAY_CONTROL_BW_INTERFACE'
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'ELEMENTOS_ARRAY_CONTROL_BW_INTERFACE'
    );
DELETE DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE
    NOMBRE_PARAMETRO = 'ELEMENTOS_ARRAY_CONTROL_BW_INTERFACE';
-- REVERSO DE LOS DETALLES DE PARAMETROS DE 'INTERFACE_ARRAY_CONTROL_BW_INTERFACE'
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'INTERFACE_ARRAY_CONTROL_BW_INTERFACE'
    );
DELETE DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE
    NOMBRE_PARAMETRO = 'INTERFACE_ARRAY_CONTROL_BW_INTERFACE';

COMMIT;
/
