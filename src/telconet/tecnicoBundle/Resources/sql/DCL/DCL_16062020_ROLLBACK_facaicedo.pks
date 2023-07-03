--=======================================================================
-- Reverso el tipo de solicitud para el control del BW de la interface
-- Reverso de la caracteristica para el tipo de proceso en la ejecución del masivo
-- Reverso de la caracteristica para el id del historial del elemento
-- Reverso de la caracteristica para el nombre de la ciudad
-- Reverso los detalles de parámetros para los id de los tipos de elementos para el control del BW de la interface
-- Reverso los detalles de parámetros para los id de los clientes para el control del BW de la interface
-- Reverso los detalles de parámetros para los rangos de capacidad de la interface para el control del BW
-- Reverso los detalles de parámetros para los id de los elementos para el control del BW de la interface
-- Reverso los detalles de parámetros para los id de las interfaces para el control del BW de la interface
-- Reverso los detalles de parámetros para los id de las regiones para el control del BW de la interface
-- Reverso los detalles de parámetros para los id de las provincias para el control del BW de la interface
-- Reverso los detalles de parámetros para los id de los cantones para el control del BW de la interface
-- Reverso los detalles de parámetros para los id de las parroquias para el control del BW de la interface
-- Reverso los detalles de parámetros para crear la tarea interna del elemento y la interface para el control del BW de la interface
-- Reverso los detalles del departamento para la tarea interna en el parámetro WEB SERVICE TAREAS
-- Reverso los detalles del nombre de la tarea para la tarea interna en el parámetro WEB SERVICE TAREAS
-- Reverso los detalles de parámetros para los servicios que no deben poseer las interfaces para el control BW
-- Reverso los detalles de parámetros para los correos de reportes de las interfaces para el control BW
--=======================================================================

-- REVERSO DE LA CABECERA DE PARAMETROS DE 'SOLICITUD CONTROL BW MASIVO''
DELETE DB_COMERCIAL.ADMI_TIPO_SOLICITUD
WHERE
    DESCRIPCION_SOLICITUD = 'SOLICITUD CONTROL BW MASIVO';
-- REVERSO LA CARACTERISTICA PARA EL TIPO DE PROCESO
DELETE DB_COMERCIAL.ADMI_CARACTERISTICA
WHERE
    DESCRIPCION_CARACTERISTICA = 'TIPO_PROCESO';
-- REVERSO LA CARACTERISTICA PARA EL ID DEL HISTORIAL DEL ELEMENTO
DELETE DB_COMERCIAL.ADMI_CARACTERISTICA
WHERE
    DESCRIPCION_CARACTERISTICA = 'HISTORIAL_ELEMENTO_ID';
-- REVERSO LA CARACTERISTICA PARA EL NOMBRE DE LA CIUDAD
DELETE DB_COMERCIAL.ADMI_CARACTERISTICA
WHERE
    DESCRIPCION_CARACTERISTICA = 'NOMBRE_CIUDAD';
-- REVERSO DE LOS DETALLES DE PARAMETROS DE 'TIPOS_ELEMENTOS_BW_INTERFACE'
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'TIPOS_ELEMENTOS_BW_INTERFACE'
    );
DELETE DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE
    NOMBRE_PARAMETRO = 'TIPOS_ELEMENTOS_BW_INTERFACE';
-- REVERSO DE LOS DETALLES DE PARAMETROS DE 'CLIENTES_CONTROL_BW_INTERFACE'
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'CLIENTES_CONTROL_BW_INTERFACE'
    );
DELETE DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE
    NOMBRE_PARAMETRO = 'CLIENTES_CONTROL_BW_INTERFACE';
-- REVERSO DE LOS DETALLES DE PARAMETROS DE 'RANGO_CONTROL_BW_INTERFACE'
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'RANGO_CONTROL_BW_INTERFACE'
    );
DELETE DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE
    NOMBRE_PARAMETRO = 'RANGO_CONTROL_BW_INTERFACE';
-- REVERSO DE LOS DETALLES DE PARAMETROS DE 'ELEMENTOS_CONTROL_BW_INTERFACE'
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'ELEMENTOS_CONTROL_BW_INTERFACE'
    );
DELETE DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE
    NOMBRE_PARAMETRO = 'ELEMENTOS_CONTROL_BW_INTERFACE';
-- REVERSO DE LOS DETALLES DE PARAMETROS DE 'INTERFACE_CONTROL_BW_INTERFACE'
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'INTERFACE_CONTROL_BW_INTERFACE'
    );
DELETE DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE
    NOMBRE_PARAMETRO = 'INTERFACE_CONTROL_BW_INTERFACE';
-- REVERSO DE LOS DETALLES DE PARAMETROS DE 'REGIONES_CONTROL_BW_INTERFACE'
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'REGIONES_CONTROL_BW_INTERFACE'
    );
DELETE DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE
    NOMBRE_PARAMETRO = 'REGIONES_CONTROL_BW_INTERFACE';
-- REVERSO DE LOS DETALLES DE PARAMETROS DE 'PROVINCIAS_CONTROL_BW_INTERFACE'
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'PROVINCIAS_CONTROL_BW_INTERFACE'
    );
DELETE DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE
    NOMBRE_PARAMETRO = 'PROVINCIAS_CONTROL_BW_INTERFACE';
-- REVERSO DE LOS DETALLES DE PARAMETROS DE 'CANTONES_CONTROL_BW_INTERFACE'
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'CANTONES_CONTROL_BW_INTERFACE'
    );
DELETE DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE
    NOMBRE_PARAMETRO = 'CANTONES_CONTROL_BW_INTERFACE';
-- REVERSO DE LOS DETALLES DE PARAMETROS DE 'PARROQUIAS_CONTROL_BW_INTERFACE'
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'PARROQUIAS_CONTROL_BW_INTERFACE'
    );
DELETE DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE
    NOMBRE_PARAMETRO = 'PARROQUIAS_CONTROL_BW_INTERFACE';
-- REVERSO DE LOS DETALLES DE PARAMETROS DE 'CREAR_TAREA_INTERNA_CONTROL_BW_INTERFACE'
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'CREAR_TAREA_INTERNA_CONTROL_BW_INTERFACE'
    );
DELETE DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE
    NOMBRE_PARAMETRO = 'CREAR_TAREA_INTERNA_CONTROL_BW_INTERFACE';
-- REVERSO EL DEPARTAMENTO EN EL PARAMETRO DE TAREAS 'WEB SERVICE TAREAS'
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'WEB SERVICE TAREAS' AND MODULO = 'SOPORTE' AND PROCESO = 'TAREAS'
              AND ESTADO = 'Activo'
    )
    AND DESCRIPCION = 'IPCCL2'
    AND VALOR1 = (
        SELECT COD_EMPRESA
        FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
        WHERE PREFIJO = 'TN'
    )
    AND VALOR2 = 'IPCCL2';
-- REVERSO EL NOMBRE DE LA TAREA EN EL PARAMETRO DE TAREAS 'WEB SERVICE TAREAS'
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'WEB SERVICE TAREAS' AND MODULO = 'SOPORTE' AND PROCESO = 'TAREAS'
              AND ESTADO = 'Activo'
    )
    AND DESCRIPCION = 'Tarea de app de Cert'
    AND VALOR1 = 'GESTION CAMBIO DE UM'
    AND VALOR2 = 'GESTION CAMBIO DE UM';
-- REVERSO DE LOS DETALLES DE PARAMETROS DE 'SERVICIOS_NO_PERMITIDOS_CONTROL_BW_INTERFACE'
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'SERVICIOS_NO_PERMITIDOS_CONTROL_BW_INTERFACE'
    );
DELETE DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE
    NOMBRE_PARAMETRO = 'SERVICIOS_NO_PERMITIDOS_CONTROL_BW_INTERFACE';
-- REVERSO DE LOS DETALLES DE PARAMETROS DE 'CORREOS_REPORTE_CONTROL_BW_INTERFACE'
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'CORREOS_REPORTE_CONTROL_BW_INTERFACE'
    );
DELETE DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE
    NOMBRE_PARAMETRO = 'CORREOS_REPORTE_CONTROL_BW_INTERFACE';

COMMIT;
/