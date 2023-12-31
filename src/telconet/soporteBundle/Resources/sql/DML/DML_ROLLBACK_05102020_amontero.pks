ALTER TABLE DB_SOPORTE.INFO_CUADRILLA_PLANIF_CAB DROP COLUMN ACTIVIDAD;

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET WHERE PARAMETRO_ID =
(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'EMPRESA_SERVICE_PLANIFICACION')
;
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'EMPRESA_SERVICE_PLANIFICACION';

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET WHERE PARAMETRO_ID =
		 (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PREFERENCIAS_CUADRILLAS_HAL')
AND VALOR2 = 'Retiro Equipo';

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET WHERE PARAMETRO_ID =
		 (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PREFERENCIAS_CUADRILLAS_HAL')
AND VALOR2 = 'Inter Urbanas';

COMMIT;

/
