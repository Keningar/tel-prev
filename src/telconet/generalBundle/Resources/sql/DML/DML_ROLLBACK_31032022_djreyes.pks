DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID = (
    SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB
    WHERE NOMBRE_PARAMETRO = 'PARAMETROS_PROMOCIONES_MASIVAS_BW'
    AND ESTADO = 'Activo')
AND DESCRIPCION = 'ENVIAR DETENER'
AND VALOR1 = 'ESTADOS'
AND USR_CREACION = 'djreyes';