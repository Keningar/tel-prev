BEGIN
    DBMS_SCHEDULER.CREATE_JOB 
    (
      job_name => '"DB_COMERCIAL"."JOB_GENERA_CUPOS_PLANIFICACION"',
      job_type => 'PLSQL_BLOCK',
      job_action => 'DECLARE
                      Lv_Error VARCHAR2(2000);
                      BEGIN
                          DB_COMERCIAL.CMKG_CUPOS_CUADRILLAS.P_SET_CUPOS_CUADRILLAS(NULL, NULL, Lv_Error) ;
                      END;',
      number_of_arguments => 0,
      start_date => NULL,
      repeat_interval => 'FREQ=DAILY',
      end_date => NULL,
      enabled => FALSE,
      auto_drop => FALSE,
      comments => 'Proceso para generar cupos para planificacion mobile.'
    );

    DBMS_SCHEDULER.SET_ATTRIBUTE
    ( 
       name => '"DB_COMERCIAL"."JOB_GENERA_CUPOS_PLANIFICACION"', 
       attribute => 'logging_level', value => DBMS_SCHEDULER.LOGGING_OFF
    );
    
    DBMS_SCHEDULER.enable
    (
       name => '"DB_COMERCIAL"."JOB_GENERA_CUPOS_PLANIFICACION"'
    );
END;