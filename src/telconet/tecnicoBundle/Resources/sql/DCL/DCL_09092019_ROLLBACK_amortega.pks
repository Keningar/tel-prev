--=======================================================================
--      Reverso de Paquete INKG_MANTENIMIENTO_TORRE
--=======================================================================
DROP PACKAGE INKG_MANTENIMIENTO_TORRE;

--=======================================================================
--      Reverso de los parámetros y los detalles para Mantenimiento de Torres
--=======================================================================
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'MANTENIMIENTO TORRES'
    );

DELETE DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE
    NOMBRE_PARAMETRO = 'MANTENIMIENTO TORRES';

COMMIT;