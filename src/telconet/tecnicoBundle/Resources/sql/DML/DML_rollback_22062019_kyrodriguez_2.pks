/*DDL que reversa la ejecución exitosa del DML_07062019_1.pks*/


DELETE FROM DB_GENERAL.admi_parametro_det 
WHERE parametro_id = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TAREA SOLICITUD DE RECURSOS DC') 
AND descripcion = 'TAREA A COMERCIAL'
AND valor1 = 'TAREAS VENTAS'
AND valor2 = 'SOLICITUDES VARIAS (INTERNAS)'
AND estado = 'Activo'
AND usr_creacion = 'kyrodriguez';

DELETE FROM DB_GENERAL.admi_parametro_cab 
WHERE nombre_parametro = 'TAREA SOLICITUD DE RECURSOS DC'
AND modulo = 'SOPORTE'
AND estado = 'Activo'
AND usr_creacion = 'kyrodriguez'

commit;

/