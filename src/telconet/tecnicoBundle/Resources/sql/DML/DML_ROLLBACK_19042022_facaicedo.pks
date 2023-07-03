
--Reverso el tipo de la solicitud para procesar las promociones
DELETE FROM DB_COMERCIAL.ADMI_TIPO_SOLICITUD
WHERE DESCRIPCION_SOLICITUD = 'SOLICITUD PROCESAR PROMOCIONES BW MASIVO';

--Reverso la característica del id de la promoción
DELETE FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'ID_GRUPO_PROMOCION';

--Reverso el detalle del estado de la promocion una vez procesado los registros y el tiempo de inicio del job en minutos
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
WHERE
    VALOR1 IN ('ESTADO_REGISTRO','TIEMPO_MINUTO_INICIO_JOB','FORMATO_HISTORIAL_SERVICIO')
    AND PARAMETRO_ID = ( SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PARAMETROS_PROMOCIONES_MASIVAS_BW' );

--Reverso detalle de parametro de los reportes de promociones de ancho banda
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET SET OBSERVACION = NULL
    WHERE PARAMETRO_ID = ( SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB
          WHERE NOMBRE_PARAMETRO = 'PARAMETROS_PROMOCIONES_MASIVAS_BW' AND ESTADO = 'Activo')
    AND DESCRIPCION = 'REPORTE_BW' AND VALOR1 = 'APLICAR';

--Reverso detalle de parametro de los reportes de promociones de ancho banda
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET SET OBSERVACION = NULL
    WHERE PARAMETRO_ID = ( SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB
          WHERE NOMBRE_PARAMETRO = 'PARAMETROS_PROMOCIONES_MASIVAS_BW' AND ESTADO = 'Activo')
    AND DESCRIPCION = 'REPORTE_BW' AND VALOR1 = 'QUITAR';

--reverso tiempo de inicio de la finalizacion de la promocion
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET SET VALOR3 = '35'
    WHERE PARAMETRO_ID = ( SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB
          WHERE NOMBRE_PARAMETRO = 'PARAMETROS_PROMOCIONES_MASIVAS_BW' AND ESTADO = 'Activo')
    AND DESCRIPCION = 'QUITAR' AND VALOR1 = 'RANGO_MINUTOS';

COMMIT;
/