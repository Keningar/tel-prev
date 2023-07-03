--=======================================================================
-- Reverso del tipo de solicitud para reactivar el servicio del cliente de forma masiva
-- Reverso los detalles de parámetros para los correos que se les enviarán los errores de reactivar masivo del servicio
-- Reverso los detalles de parámetros para los nombres técnicos de los servicios de reactivar masivo
-- Reverso los detalles de parámetros para el máximo de servicios que se pueden agregar para generar el reactivar masivo
-- Reverso los detalles de parámetros para los productos de los servicios no permitidos para generar el reactivar masivo
--=======================================================================

-- REVERSO DE LA CABECERA DE PARAMETROS DE 'SOLICITUD REACTIVAR MASIVO'
DELETE DB_COMERCIAL.ADMI_TIPO_SOLICITUD
WHERE
    DESCRIPCION_SOLICITUD = 'SOLICITUD REACTIVAR MASIVO';
-- REVERSO DE LOS DETALLES DE PARAMETROS DE 'CORREOS_RESPUESTA_CORTE_REACTIVAR_SERVICIO_MASIVO'
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    VALOR1 = 'REACTIVAR_MASIVO' AND
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'CORREOS_RESPUESTA_CORTE_REACTIVAR_SERVICIO_MASIVO'
    );
-- REVERSO DE LOS DETALLES DE PARAMETROS DE 'NOMBRES_TECNICO_SERVICIOS_CORTE_REACTIVAR_MASIVO'
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    VALOR1 = 'REACTIVAR_MASIVO' AND
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'NOMBRES_TECNICO_SERVICIOS_CORTE_REACTIVAR_MASIVO'
    );
-- REVERSO DE LOS DETALLES DE PARAMETROS DE 'MAXIMO_SERVICIOS_AGREGADOS_CORTE_REACTIVAR_MASIVO'
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    VALOR1 = 'REACTIVAR_MASIVO' AND
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'MAXIMO_SERVICIOS_AGREGADOS_CORTE_REACTIVAR_MASIVO'
    );
-- REVERSO DE LOS DETALLES DE PARAMETROS DE 'PRODUCTOS_NO_PERMITIDOS_CORTE_REACTIVAR_MASIVO'
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    VALOR1 = 'REACTIVAR_MASIVO' AND
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'PRODUCTOS_NO_PERMITIDOS_CORTE_REACTIVAR_MASIVO'
    );

COMMIT;
/
