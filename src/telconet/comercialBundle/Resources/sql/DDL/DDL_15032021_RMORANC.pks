/**
 * Documentación para 'JOB_ACTUALIZA_COORD_MOVIL'
 * Job que ejecuta el procedimiento DB_COMERCIAL.CMKG_FISCALIZA_PREFACTIBILIDAD.P_PROCESA_ACT_COORDENADA_MOVIL.
 *
 * @author Ronny Morán <rmoranc@telconet.ec>
 * @version 1.0 15-03-2021
 */
SET SERVEROUTPUT ON;
SET DEFINE OFF;
BEGIN

  BEGIN
      DBMS_SCHEDULER.DROP_JOB(job_name => '"DB_COMERCIAL"."JOB_ACTUALIZA_COORD_MOVIL"',
                              defer    => false,
                              force    => true);
  EXCEPTION
    WHEN OTHERS THEN
      DBMS_OUTPUT.PUT_LINE('El job aún no ha sido creado...');
  END;

  DBMS_SCHEDULER.CREATE_JOB (
          job_name            => '"DB_COMERCIAL"."JOB_ACTUALIZA_COORD_MOVIL"',
          job_type            => 'PLSQL_BLOCK',
          job_action          => 'DECLARE
                                  BEGIN
        DB_COMERCIAL.CMKG_FISCALIZA_PREFACTIBILIDAD.P_PROCESA_ACT_COORDENADA_MOVIL;
                                  END;',
          number_of_arguments => 0,
          repeat_interval     => 'FREQ=MINUTELY;INTERVAL=5',
          end_date            => NULL,
          enabled             => FALSE,
          auto_drop           => FALSE,
          comments            => 'Job que censa cada 5 minutos el estado de las ordenes de servicio que hayan caído en PreFactibilidad por la funcionalidad de actualización de coordenadas desde el móvil');

  DBMS_SCHEDULER.SET_ATTRIBUTE(name      => '"DB_COMERCIAL"."JOB_ACTUALIZA_COORD_MOVIL"',
                               attribute => 'logging_level', value => DBMS_SCHEDULER.LOGGING_OFF);

  DBMS_SCHEDULER.enable(name => '"DB_COMERCIAL"."JOB_ACTUALIZA_COORD_MOVIL"');

  DBMS_OUTPUT.PUT_LINE('Job creado satisfactoriamente...');

END;
/





