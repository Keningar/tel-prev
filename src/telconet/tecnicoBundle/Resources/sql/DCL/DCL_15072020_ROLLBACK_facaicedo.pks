--=======================================================================
-- Reverso los detalles de parámetros para los datos del ws de networking para el control del BW de la interface
-- Reverso los detalles de parámetros para el rango del intervalo de porcentaje para control del BW de la interface
--=======================================================================

-- REVERSO DE LOS DETALLES DE PARAMETROS DE 'DATOS_WS_NETWORKING_CONTROL_BW_INTERFACE'
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'DATOS_WS_NETWORKING_CONTROL_BW_INTERFACE'
    );
DELETE DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE
    NOMBRE_PARAMETRO = 'DATOS_WS_NETWORKING_CONTROL_BW_INTERFACE';
-- REVERSO DE LOS DETALLES DE PARAMETROS DE 'RANGO_INTERVALO_PORCENTAJE_CONTROL_BW_INTERFACE'
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'RANGO_INTERVALO_PORCENTAJE_CONTROL_BW_INTERFACE'
    );
DELETE DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE
    NOMBRE_PARAMETRO = 'RANGO_INTERVALO_PORCENTAJE_CONTROL_BW_INTERFACE';

COMMIT;
/
