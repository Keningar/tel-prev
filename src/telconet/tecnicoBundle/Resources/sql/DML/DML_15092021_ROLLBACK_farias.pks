
/**
 * @author Alberto Arias <farias@telconet.ec>
 * @version 1.0
 * @fecha 15-09-2021    
 * En caso de error, se eliminan los estados de Desconfiguración de IP's
 */

--=======================================================================
-- Reverso los detalles de parámetros para los estados de la desconfiguración de IP
--=======================================================================

-- REVERSO DE LOS DETALLES DE PARAMETROS DE 'ESTADO_DESCONFIGURACION_IP'
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'ESTADO_DESCONFIGURACION_IP'
    );
DELETE DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE
    NOMBRE_PARAMETRO = 'ESTADO_DESCONFIGURACION_IP';
    
COMMIT;
/
