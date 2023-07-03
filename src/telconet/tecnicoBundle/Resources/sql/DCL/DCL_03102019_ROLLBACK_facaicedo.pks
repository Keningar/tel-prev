--=======================================================================
--      Reverso de los parametros para la validación de actualización de estados de 
--      los servicios de interfaces de los elementos SWITCH o STACKS
--      Reverso de los parametros de estados de interfaces permitidas de elementos SWITCH
--      Reverso de los parametros de cambio de ultima milla por proceso masivo
--      Reverso de las caracteristicas para los Id de las interfaces anterior y nueva de los detalles de solicitud
--      Reverso de los parametros de estados de servicios permitidos para cambio ultima milla
--      Reverso de los parametros para los correos que se les enviarán los errores en el cambio de última milla masivo
--=======================================================================

-- REVERSO LAS CABECERAS Y DETALLES DE PARAMETROS PARA SERVICIOS ACTUALIZACION
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'ESTADOS_SERVICIOS_NO_PERMITIDOS_ACTUALIZACION'
    );
DELETE DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE
    NOMBRE_PARAMETRO = 'ESTADOS_SERVICIOS_NO_PERMITIDOS_ACTUALIZACION';

-- REVERSO LAS CABECERAS Y DETALLES DE PARAMETROS DE ESTADOS PERMITIDOS DE INTERFACES DE ELEMENTOS SWITCH
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'ESTADOS_INTERFACES_PERMITIDAS'
    );
DELETE DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE
    NOMBRE_PARAMETRO = 'ESTADOS_INTERFACES_PERMITIDAS';

-- REVERSO LAS CABECERAS Y DETALLES DE PARAMETROS DE CAMBIO DE ULTIMA MILLA MASIVO
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'CAMBIO_ULTIMA_MILLA_MASIVO'
    );
DELETE DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE
    NOMBRE_PARAMETRO = 'CAMBIO_ULTIMA_MILLA_MASIVO';

-- REVERSO DE LAS CARACTERISTICAS PARA LAS INTERFACES ANTERIOR Y NUEVA DE LOS DETALLES DE SOLICITUD
DELETE DB_COMERCIAL.ADMI_CARACTERISTICA
WHERE
    DESCRIPCION_CARACTERISTICA = 'INTERFACE_ELEMENTO_ANTERIOR_ID';
DELETE DB_COMERCIAL.ADMI_CARACTERISTICA
WHERE
    DESCRIPCION_CARACTERISTICA = 'INTERFACE_ELEMENTO_NUEVA_ID';
DELETE DB_COMERCIAL.ADMI_CARACTERISTICA
WHERE
    DESCRIPCION_CARACTERISTICA = 'INTERFACE_ELEMENTO_NUEVA_NOMBRE';

-- REVERSO LAS CABECERAS Y DETALLES DE PARAMETROS DE ESTADOS PERMITIDOS DE LOS SERVICIOS
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'ESTADOS_SERVICIOS_PERMITIDOS'
    );
DELETE DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE
    NOMBRE_PARAMETRO = 'ESTADOS_SERVICIOS_PERMITIDOS';

-- REVERSO LAS CABECERAS Y DETALLES DE PARAMETROS PARA CORREOS RESPUESTA CAMBIO ULTIMA MILLA MASIVO
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'CORREOS_RESPUESTA_CAMBIO_UM_MASIVO'
    );
DELETE DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE
    NOMBRE_PARAMETRO = 'CORREOS_RESPUESTA_CAMBIO_UM_MASIVO';

COMMIT;
/
