-- Elimina los estados validos para agregar servicios adicionales
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID = (
        SELECT ID_PARAMETRO
        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'VALIDA_PROD_ADICIONAL'
    )
AND DESCRIPCION = 'Estados permitidos para producto cableado ethernet'
AND VALOR1 = 'Factible';

-- Elimina parametro para agregar el nombre de la nueva solicitud
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID = (
        SELECT ID_PARAMETRO
        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'VALIDA_PROD_ADICIONAL'
    )
AND DESCRIPCION = 'Solicitud cableado ethernet'
AND VALOR1 = '1332';

-- Elimina los estados validos de un traslado de servicio
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID = (
        SELECT ID_PARAMETRO
        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'VALIDA_PROD_ADICIONAL'
    )
AND DESCRIPCION = 'Estados permitidos para CE en traslado'
AND VALOR1 = 'PrePlanificada';	

COMMIT;
/