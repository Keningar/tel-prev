--=======================================================================
-- Reverso los detalles de parámetros para la parametrización de mensajes en el reenvío de credenciales para el canal del futbol
--=======================================================================

-- REVERSO DE LOS DETALLES DE PARAMETROS DE 'MENSAJES_REENVIO_CREDENCIALES_ECDF'
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'MENSAJES_REENVIO_CREDENCIALES_ECDF'
    );
DELETE DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE
    NOMBRE_PARAMETRO = 'MENSAJES_REENVIO_CREDENCIALES_ECDF';
    
COMMIT;
/