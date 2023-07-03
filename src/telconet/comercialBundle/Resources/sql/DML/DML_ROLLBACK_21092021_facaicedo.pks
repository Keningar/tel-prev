
--REVERSO PARAMETRO PARA AGREGAR EL PORCENTAJE RUTA PARA LOS SERVICIOS SW POE GPON
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET PDE SET PDE.VALOR2 = REPLACE(PDE.VALOR2,',70','') WHERE PDE.ID_PARAMETRO_DET = (
    SELECT DET.ID_PARAMETRO_DET FROM DB_GENERAL.ADMI_PARAMETRO_DET DET WHERE DET.PARAMETRO_ID = (
      SELECT CAB.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB WHERE CAB.NOMBRE_PARAMETRO = 'IDS_PROGRESOS_TAREAS'
    ) AND DET.VALOR1 = 'PROG_INSTALACION_TN_MATERIALES'
);

--Reverso el detalle de parametro para no ingresar fibra en los productos SW POE GPON
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    VALOR1 = ( SELECT DESCRIPCION_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE NOMBRE_TECNICO='SAFECITYSWPOE' AND ESTADO = 'Activo' )
    AND PARAMETRO_ID = ( SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'VISUALIZAR_PANTALLA_FIBRA' );

--Reverso el detalle de los puertos del ONT permitidos para el producto SW POE
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    DESCRIPCION = 'PUERTOS_ONT_PERMITIDOS_POR_PRODUCTO'
    AND VALOR1 = ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE NOMBRE_TECNICO='SAFECITYSWPOE' AND ESTADO = 'Activo' )
    AND PARAMETRO_ID = ( SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PARAMETROS PROYECTO GPON SAFECITY' );

--Reverso el detalle de los modelos de SW POE permitidos
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    DESCRIPCION = 'MODELOS_SWITCH_POE'
    AND PARAMETRO_ID = ( SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PARAMETROS PROYECTO GPON SAFECITY' );

--Reverso el detalle para el proceso y tarea de instalación de wifi
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    DESCRIPCION = 'TAREA DE INSTALACION DEL SERVICIO'
    AND VALOR1 = ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE NOMBRE_TECNICO='SAFECITYSWPOE' AND ESTADO = 'Activo' )
    AND PARAMETRO_ID = ( SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NUEVA_RED_GPON_TN' );

DELETE DB_SOPORTE.ADMI_TAREA
WHERE
    NOMBRE_TAREA = 'INSTALACION SWITCH PoE GPON'
    AND PROCESO_ID = (SELECT ID_PROCESO FROM DB_SOPORTE.ADMI_PROCESO WHERE NOMBRE_PROCESO = 'TAREAS DE ELECTRICO - PROYECTOS SAFE CITY SWITCH PoE');

DELETE DB_SOPORTE.ADMI_PROCESO_EMPRESA
WHERE
    PROCESO_ID = (SELECT ID_PROCESO FROM DB_SOPORTE.ADMI_PROCESO WHERE NOMBRE_PROCESO = 'TAREAS DE ELECTRICO - PROYECTOS SAFE CITY SWITCH PoE');

DELETE DB_SOPORTE.ADMI_PROCESO
WHERE NOMBRE_PROCESO = 'TAREAS DE ELECTRICO - PROYECTOS SAFE CITY SWITCH PoE';

-- REVERSO DEL DETALLE DEL PARAMETROS DE 'PARAMETROS PROYECTO GPON SAFECITY' DEL SERVICIO SW POE
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    DESCRIPCION = 'VALIDAR RELACION SERVICIO ADICIONAL CON DATOS SAFECITY'
    AND VALOR1 = ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE NOMBRE_TECNICO='SAFECITYSWPOE' AND ESTADO = 'Activo' )
    AND PARAMETRO_ID = ( SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PARAMETROS PROYECTO GPON SAFECITY' );

-- REVERSO DEL DETALLE DEL PARAMETROS DE 'CONFIG_PRODUCTO_DATOS_SAFE_CITY' PARA LOS DATOS PRODUCTO PRINCIPAL
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    VALOR1 = 'PRODUCTO_ADICIONAL_SW_POE'
    AND PARAMETRO_ID = ( SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'CONFIG_PRODUCTO_DATOS_SAFE_CITY' );

-- REVERSO DEL DETALLE DEL PARAMETROS DE 'NUEVA_RED_GPON_TN' DEL SERVICIO SW POE
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    DESCRIPCION IN ('CAMBIAR ESTADO TAREA SERVICIO')
    AND VALOR1 = ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE NOMBRE_TECNICO='SAFECITYSWPOE' AND ESTADO = 'Activo' )
    AND PARAMETRO_ID = ( SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NUEVA_RED_GPON_TN' );

-- REVERSO DEL DETALLE DEL PARAMETROS DE 'CONFIG_PRODUCTO_DATOS_SAFE_CITY' DEL SERVICIO SW POE
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    VALOR3 = '[SWITCH PoE GPON]'
    AND VALOR4 = ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE NOMBRE_TECNICO='SAFECITYSWPOE' AND ESTADO = 'Activo' )
    AND PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'CONFIG_PRODUCTO_DATOS_SAFE_CITY'
    );

-- REVERSO DEL DETALLE DEL PARAMETROS DE 'CONFIG_PRODUCTO_DATOS_SAFE_CITY' DEL SERVICIO SW POE
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    VALOR2 = 'COORDINAR_OBSERVACION'
    AND VALOR1 = ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE NOMBRE_TECNICO='SAFECITYSWPOE' AND ESTADO = 'Activo' )
    AND PARAMETRO_ID = ( SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'CONFIG_PRODUCTO_DATOS_SAFE_CITY' );

-- REVERSO DEL DETALLE DEL PARAMETROS DE 'NUEVA_RED_GPON_TN' DEL SERVICIO SW POE
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    VALOR2 IN ('FLUJO_OCULTO','PRODUCTO_REQUERIDO','PRODUCTOS_PERMITIDOS')
    AND VALOR1 = ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE NOMBRE_TECNICO='SAFECITYSWPOE' AND ESTADO = 'Activo' )
    AND PARAMETRO_ID = ( SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NUEVA_RED_GPON_TN' );

-- REVERSO DEL DETALLE DEL PARAMETROS DE 'NUEVA_RED_GPON_TN' DEL SERVICIO SW POE
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    VALOR5 IN ('RELACION_PRODUCTO_CARACTERISTICA','PRODUCTO_NO_PERMITIDO_MPLS')
    AND VALOR1 = ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE NOMBRE_TECNICO='SAFECITYSWPOE' AND ESTADO = 'Activo' )
    AND PARAMETRO_ID = ( SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NUEVA_RED_GPON_TN' );

--ELIMINAR CARACTERISTICAS DEL PRODUCTO
DELETE FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
WHERE PRODUCTO_ID = ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE NOMBRE_TECNICO='SAFECITYSWPOE' AND ESTADO='Activo' AND EMPRESA_COD=10 );

--ELIMINAR CARACTERISTICA 'RELACION_CAMARA_PRINCIPAL'
DELETE FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'RELACION_CAMARA_PRINCIPAL';

--ELIMINAR EL PRODUCTO
DELETE FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE NOMBRE_TECNICO='SAFECITYSWPOE' AND ESTADO='Activo' AND EMPRESA_COD=10;

COMMIT;
/
