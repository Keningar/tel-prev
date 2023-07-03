/**
 * Documentación para 'JOB_SEGURIDAD_SDWAN'
 * Job que ejecuta el procedimiento DB_COMERCIAL.P_NOTIFICA_FIN_SEGURIDAD.
 *
 * @author David León <mdleon@telconet.ec>
 * @version 1.0 01-08-2020
 */

SET SERVEROUTPUT ON;
SET DEFINE OFF;
BEGIN

  BEGIN
      DBMS_SCHEDULER.DROP_JOB(job_name => '"DB_COMERCIAL"."JOB_SEGURIDAD_SDWAN"',
                              defer    => false,
                              force    => true);
  EXCEPTION
    WHEN OTHERS THEN
      DBMS_OUTPUT.PUT_LINE('El job aún no ha sido creado...');
  END;

  DBMS_SCHEDULER.CREATE_JOB (
          job_name            => '"DB_COMERCIAL"."JOB_SEGURIDAD_SDWAN"',
          job_type            => 'PLSQL_BLOCK',
          job_action          => 'DECLARE
                                  BEGIN
        DB_COMERCIAL.P_NOTIFICA_FIN_SEGURIDAD;
                                  END;',
          number_of_arguments => 0,
          repeat_interval     => 'FREQ=DAILY;BYHOUR=00;BYMINUTE=0;BYSECOND=0',
          end_date            => NULL,
          enabled             => FALSE,
          auto_drop           => FALSE,
          comments            => 'Job que ejecuta y notifica los servicios de seguridad que estan por terminar');

  DBMS_SCHEDULER.SET_ATTRIBUTE(name      => '"DB_COMERCIAL"."JOB_SEGURIDAD_SDWAN"',
                               attribute => 'logging_level', value => DBMS_SCHEDULER.LOGGING_OFF);

  DBMS_SCHEDULER.enable(name => '"DB_COMERCIAL"."JOB_SEGURIDAD_SDWAN"');

  DBMS_OUTPUT.PUT_LINE('Job creado satisfactoriamente...');

END;
/
