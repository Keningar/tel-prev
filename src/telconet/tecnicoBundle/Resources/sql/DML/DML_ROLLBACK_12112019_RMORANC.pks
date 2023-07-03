-- Scripts para eliminar información insertada del archivo DML_12112019_RMORANC.pks

-- Eliminando cabecera DIAS_BLOQUEO_BOBINA_DESPACHO parametrizado.
Delete from DB_GENERAL.ADMI_PARAMETRO_DET
where  PARAMETRO_ID = 
(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
WHERE NOMBRE_PARAMETRO='DIAS_BLOQUEO_BOBINA_DESPACHO');

-- Eliminando detalle de cabecera DIAS_BLOQUEO_BOBINA_DESPACHO parametrizado.
Delete from DB_GENERAL.ADMI_PARAMETRO_CAB
where  NOMBRE_PARAMETRO='DIAS_BLOQUEO_BOBINA_DESPACHO';

-- Eliminando cabecera CANTIDAD_BLOQUEO_BOBINA parametrizado.
Delete from DB_GENERAL.ADMI_PARAMETRO_DET
where  PARAMETRO_ID = 
(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
WHERE NOMBRE_PARAMETRO='CANTIDAD_BLOQUEO_BOBINA');

-- Eliminando detalle de cabecera CANTIDAD_BLOQUEO_BOBINA parametrizado.
Delete from DB_GENERAL.ADMI_PARAMETRO_CAB
where  NOMBRE_PARAMETRO='CANTIDAD_BLOQUEO_BOBINA';

--Eliminando cantidad de fibra a utilizar en una instalación
Delete from DB_GENERAL.ADMI_PARAMETRO_DET
where  VALOR1 = 'CANTIDAD_BOBINA_INSTALACION_MD';

--Eliminando número de bobinas a visualizar en tarea de soporte
Delete from DB_GENERAL.ADMI_PARAMETRO_DET
where  VALOR1 = 'NUMERO_BOBINAS_SOPORTE';

--Eliminando estado del número de bobinas a visualizar en tarea de soporte
Delete from DB_GENERAL.ADMI_PARAMETRO_DET
where  VALOR1 = 'ESTADO_NUMERO_BOBINAS_SOPORTE';

--Eliminando número de bobinas a visualizar en tarea de instalación
Delete from DB_GENERAL.ADMI_PARAMETRO_DET
where  VALOR1 = 'NUMERO_BOBINAS_INSTALACION';

--Eliminando estado del número de bobinas a visualizar en tarea de instalación
Delete from DB_GENERAL.ADMI_PARAMETRO_DET
where  VALOR1 = 'ESTADO_NUMERO_BOBINAS_INSTALACION';


commit;
/
