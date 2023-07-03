/**
 * PKS para el rollback de los parametros ingresados
 *
 * @author Jean Pierre Nazareno Martinez <jnazareno@telconet.ec>
 * @version 1.0 10-11-2021
 */
	
--ELIMINANDO PARAMETROS INGRESADOS
Delete from DB_GENERAL.ADMI_PARAMETRO_DET
where  VALOR1 IN ('ID_PRODUCTO_SAFECITYDATOS', 'ID_PRODUCTO_SAFECITYSWPOE', 'ID_PRODUCTO_SAFECITYWIFI');
	
--ELIMINANDO PROGRESOS DE TAREA DE PRODUCTO INSTALACION_PRODUCTOS_FTTX
DELETE FROM 
DB_SOPORTE.INFO_PROGRESO_PORCENTAJE 
WHERE TAREA_ID = 2
AND EMPRESA_ID = 10 
AND USR_CREACION = 'jnazareno';

--ELIMINANDO CABECERA DE PROGRESOS DE TAREA DE PRODUCTO INSTALACION_PRODUCTOS_FTTX
DELETE FROM 
DB_SOPORTE.ADMI_PROGRESOS_TAREA
WHERE CODIGO_TAREA = 2
AND USR_CREACION = 'jnazareno'; 

COMMIT;

/
