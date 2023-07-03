/**
 * @author Telcos
 * @version 1.0
 *
 * @author Wilson Quinto <wquinto@telconet.ec>
 * @version 1.0
 * @since 01-05-2021
 * Se llama a otro procedimiento que realiza la anulación de pagos masivo
 *
 */
BEGIN
    BEGIN
        DBMS_SCHEDULER.DROP_JOB(job_name => '"DB_FINANCIERO"."JOB_ANULAR_PAGOS"',
                                defer => false,
                                force => true);
    EXCEPTION
        WHEN OTHERS THEN
            DBMS_OUTPUT.PUT_LINE('El job aún no ha sido creado...');
    END;

    DBMS_SCHEDULER.CREATE_JOB (
            job_name => '"DB_FINANCIERO"."JOB_ANULAR_PAGOS"',
            job_type => 'PLSQL_BLOCK',
            job_action => ' DECLARE
							  Ln_CodSalida NUMBER;
							  Lv_MsjSalida VARCHAR2(4000);
                            BEGIN  
                               DB_FINANCIERO.FNCK_ANULAR_PAGO.P_MASIVO_ANULACION(Ln_CodSalida,Lv_MsjSalida); 
                            END;',
            number_of_arguments => 0,
            start_date => TO_TIMESTAMP_TZ('2021-05-05 21:06:05.000000000 AMERICA/GUAYAQUIL','YYYY-MM-DD HH24:MI:SS.FF TZR'),
            repeat_interval => 'FREQ=MINUTELY;INTERVAL=2',
            end_date => NULL,
            enabled => FALSE,
            auto_drop => FALSE,
            comments => 'JOB QUE REALIZA LA ANULACION DE PAGOS EN ESTADO ASIGNADO, PENDIENTE O CERRADO');

         
     
 
    DBMS_SCHEDULER.SET_ATTRIBUTE( 
             name => '"DB_FINANCIERO"."JOB_ANULAR_PAGOS"', 
             attribute => 'logging_level', value => DBMS_SCHEDULER.LOGGING_OFF);
      
  
    
    DBMS_SCHEDULER.enable(
             name => '"DB_FINANCIERO"."JOB_ANULAR_PAGOS"');

    DBMS_OUTPUT.PUT_LINE('Job creado satisfactoriamente...');
END;
/