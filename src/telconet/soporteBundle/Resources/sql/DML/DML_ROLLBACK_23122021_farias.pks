--=======================================================================
-- Reverso los detalles de parámetros para el envío de correos de el canal del futbol
--=======================================================================

-- REVERSO DE LOS DETALLES DE PARAMETROS DE 'CORREOS_ACTUALIZAR_WS_ECDF'
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'CORREOS_ACTUALIZAR_WS_ECDF'
    );
DELETE DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE
    NOMBRE_PARAMETRO = 'CORREOS_ACTUALIZAR_WS_ECDF';
    

-- REVERSO DE LOS DETALLES DE PARAMETROS DE 'TAREA_SOPORTE_ACTUALIZAR_CORREO_ECDF'
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'TAREA_SOPORTE_ACTUALIZAR_CORREO_ECDF'
    );
DELETE DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE
    NOMBRE_PARAMETRO = 'TAREA_SOPORTE_ACTUALIZAR_CORREO_ECDF';
    
COMMIT;
/