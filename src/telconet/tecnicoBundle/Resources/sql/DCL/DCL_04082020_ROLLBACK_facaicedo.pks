--=======================================================================
-- Reverso de parametros para la validación de los estados de las interfaces no disponibles o ocupadas ya por servicios
--=======================================================================

-- REVERSO DE LOS DETALLES DE PARAMETROS DE 'ESTADOS_INTERFACES_NO_DISPONIBLES'
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'ESTADOS_INTERFACES_NO_DISPONIBLES'
    );
DELETE DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE
    NOMBRE_PARAMETRO = 'ESTADOS_INTERFACES_NO_DISPONIBLES';

COMMIT;
/
