--=======================================================================
-- Reverso de los detalles de parámetros para controlar los tiempos máximo de ejecución de los procesos
--=======================================================================

-- REVERSO DE LA CABECERA DE PARAMETROS DE 'CONTROL_PROCESOS_MAXIMO_TIEMPO_EJECUCION'
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'CONTROL_PROCESOS_MAXIMO_TIEMPO_EJECUCION'
    );
DELETE DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE
    NOMBRE_PARAMETRO = 'CONTROL_PROCESOS_MAXIMO_TIEMPO_EJECUCION';

COMMIT;
/
