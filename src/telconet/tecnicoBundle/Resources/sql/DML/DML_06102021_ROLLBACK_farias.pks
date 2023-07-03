
/**
 * @author Alberto Arias <farias@telconet.ec>
 * @version 1.0
 * @fecha 06-10-2021    
 * En caso de error, se eliminan los tipos de MAC en Desconfiguración de IP's
 */

--=======================================================================
-- Reverso los detalles de parámetros para los tipos de MAC de la desconfiguración de IP
--=======================================================================

-- REVERSO DE LOS DETALLES DE PARAMETROS DE 'TIPO_MAC_DESCONFIGURAR_IP'
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'TIPO_MAC_DESCONFIGURAR_IP'
    );
DELETE DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE
    NOMBRE_PARAMETRO = 'TIPO_MAC_DESCONFIGURAR_IP';
    
COMMIT;
/
