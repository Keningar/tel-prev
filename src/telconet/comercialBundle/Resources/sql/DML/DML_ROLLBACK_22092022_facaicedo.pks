
-- REVERSO DE LA CABECERA DE PARAMETROS DE 'PARAMETROS_SEG_VEHICULOS'
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'PARAMETROS_SEG_VEHICULOS'
    );
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE
    NOMBRE_PARAMETRO = 'PARAMETROS_SEG_VEHICULOS';

--REVERSO PARAMETRO PARA AGREGAR EL PORCENTAJE RUTA PARA LOS SERVICIOS SEG VEHICULO
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET PDE SET PDE.VALOR2 = REPLACE(PDE.VALOR2,','||
  (SELECT ID_PROGRESO_PORCENTAJE FROM DB_SOPORTE.INFO_PROGRESO_PORCENTAJE
   WHERE TIPO_PROGRESO_ID = (SELECT ID_TIPO_PROGRESO FROM DB_SOPORTE.ADMI_TIPO_PROGRESO atp WHERE CODIGO = 'INGRESO_MATERIALES')
   AND TAREA_ID = (SELECT ID_TAREA FROM DB_SOPORTE.ADMI_TAREA WHERE NOMBRE_TAREA = 'INSTALACION MOBILE BUS')
   AND ESTADO = 'Activo')
  ,'')
WHERE PDE.ID_PARAMETRO_DET = (
    SELECT DET.ID_PARAMETRO_DET FROM DB_GENERAL.ADMI_PARAMETRO_DET DET WHERE DET.PARAMETRO_ID = (
      SELECT CAB.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB WHERE CAB.NOMBRE_PARAMETRO = 'IDS_PROGRESOS_TAREAS'
    ) AND DET.VALOR1 = 'PROG_INSTALACION_TN_MATERIALES'
  );

--Reverso el detalle para el proceso y tarea de instalación de MobileBus
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    DESCRIPCION = 'TAREA DE INSTALACION DEL SERVICIO'
    AND VALOR1 = ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'MOBILE BUS' AND EMPRESA_COD = '10' AND ESTADO = 'Activo' )
    AND PARAMETRO_ID = ( SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NUEVA_RED_GPON_TN' );

--Reverso de los progresos porcentajes de la tarea
DELETE FROM DB_SOPORTE.INFO_PROGRESO_PORCENTAJE
WHERE
    TAREA_ID = (SELECT ID_TAREA FROM DB_SOPORTE.ADMI_TAREA WHERE NOMBRE_TAREA = 'INSTALACION MOBILE BUS');

--Reverso del tipo progreso FOTO_DESPUES
DELETE FROM DB_SOPORTE.ADMI_TIPO_PROGRESO WHERE CODIGO = 'FOTO_DESPUES';

--REVERSO TAREA
DELETE FROM DB_SOPORTE.ADMI_TAREA
WHERE
    NOMBRE_TAREA = 'INSTALACION MOBILE BUS'
    AND PROCESO_ID = (SELECT ID_PROCESO FROM DB_SOPORTE.ADMI_PROCESO WHERE NOMBRE_PROCESO = 'SOLICITAR NUEVO SERVICIO MOBILE BUS');
DELETE FROM DB_SOPORTE.ADMI_PROCESO_EMPRESA
WHERE
    PROCESO_ID = (SELECT ID_PROCESO FROM DB_SOPORTE.ADMI_PROCESO WHERE NOMBRE_PROCESO = 'SOLICITAR NUEVO SERVICIO MOBILE BUS');
DELETE FROM DB_SOPORTE.ADMI_PROCESO
WHERE NOMBRE_PROCESO = 'SOLICITAR NUEVO SERVICIO MOBILE BUS';

--ELIMINAR CARACTERISTICAS DEL PRODUCTO
DELETE FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
WHERE PRODUCTO_ID = ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'MOBILE BUS' AND EMPRESA_COD = '10' AND ESTADO = 'Activo' );

--ELIMINAR CARACTERISTICA 'PLACA'
DELETE FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'PLACA';

--ELIMINAR CARACTERISTICA 'COOPERATIVA'
DELETE FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'COOPERATIVA';

--ELIMINAR CARACTERISTICA 'TIPO TRANSPORTE'
DELETE FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'TIPO TRANSPORTE';

--ELIMINAR CARACTERISTICA 'NUMERO CELULAR'
DELETE FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'NUMERO CELULAR';

-- REVERSO DE LA CABECERA DE PARAMETROS DE 'PARAMETROS_SEG_VEHICULOS'
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'PROD_TIPO TRANSPORTE'
    );
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE
    NOMBRE_PARAMETRO = 'PROD_TIPO TRANSPORTE';

-- REVERSO DE LA CABECERA DE PARAMETROS DE 'ETIQUETA_FOTO_NOMBRE_TECNICO'
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'ETIQUETA_FOTO_NOMBRE_TECNICO'
    );
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE
    NOMBRE_PARAMETRO = 'ETIQUETA_FOTO_NOMBRE_TECNICO';

--Reverso el detalle para las etiquetas personalizadas por nombre tecnico del producto de MobileBus
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    VALOR7 = ( SELECT NOMBRE_TECNICO FROM DB_COMERCIAL.ADMI_PRODUCTO
               WHERE DESCRIPCION_PRODUCTO = 'MOBILE BUS' AND EMPRESA_COD = '10' AND ESTADO = 'Activo' )
    AND PARAMETRO_ID = ( SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'ETIQUETA_FOTO' );

--Reverso del admin progreso de la tarea
DELETE FROM DB_SOPORTE.ADMI_PROGRESOS_TAREA 
WHERE
    NOMBRE_TAREA = 'INSTALACION_MOBILE_BUS';

--Reverso el detalle para los dispositivos sin mac
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    VALOR1 IN ('CHIP','DVR','DISCO DURO')
    AND PARAMETRO_ID = '1563';

--ELIMINAR EL PRODUCTO
DELETE FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'MOBILE BUS' AND EMPRESA_COD = '10' AND ESTADO = 'Activo';

--REVERSO LOS DETALLES DE LA CABECERA 'VISUALIZAR_PANTALLA_FIBRA'
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    VALOR1 = 'MOBILE BUS'
    AND PARAMETRO_ID = ( SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'VISUALIZAR_PANTALLA_FIBRA' );

-- REVERSO DE LA CABECERA DE PARAMETROS DE 'VALIDAR_PRODUCTO_CARACTERISTICA_REGEX'
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'VALIDAR_PRODUCTO_CARACTERISTICA_REGEX'
    );
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE
    NOMBRE_PARAMETRO = 'VALIDAR_PRODUCTO_CARACTERISTICA_REGEX';

COMMIT;
/
