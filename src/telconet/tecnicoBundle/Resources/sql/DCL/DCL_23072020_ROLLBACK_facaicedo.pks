--=======================================================================
-- Reverso los detalles de parámetros para la capacidad de las interfaces de los modelos para la validación del BW máximo de la interface
--=======================================================================

-- REVERSO DE LOS DETALLES DE PARAMETROS DE 'MODELO_INTERFACE_VALIDAR_MAXIMO_BW_INTERFACE'
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'MODELO_INTERFACE_VALIDAR_MAXIMO_BW_INTERFACE'
    );
DELETE DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE
    NOMBRE_PARAMETRO = 'MODELO_INTERFACE_VALIDAR_MAXIMO_BW_INTERFACE';

COMMIT;
/
