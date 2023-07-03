DROP SEQUENCE DB_SOPORTE.SEQ_NUMERACION_INFO_TAREA;

BEGIN
  dbms_scheduler.drop_job(job_name => '"DB_SOPORTE"."JOB_ACTUALIZA_NUMERO_INFOTAREA"');
END;

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET WHERE PARAMETRO_ID =
(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NUMERACION_INFO_TAREA')
;
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'NUMERACION_INFO_TAREA';

COMMIT;

/
