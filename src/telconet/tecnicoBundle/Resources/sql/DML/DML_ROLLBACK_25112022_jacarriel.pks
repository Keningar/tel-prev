--REVERSO PARA EL DETALLE DE POTENCIA EN ACTIVACION DE SERVICIOS DE TRASLADOS CON LA MISMA IP DEL OLT

DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    DESCRIPCION = 'REQUIERE VALIDACION POTENCIA TRASLADO'
    AND PARAMETRO_ID = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'VALIDAR POTENCIA SERVICIO');

COMMIT;