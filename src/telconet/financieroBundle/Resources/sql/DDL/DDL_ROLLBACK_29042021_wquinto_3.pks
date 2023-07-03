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
    DBMS_SCHEDULER.DROP_JOB(job_name => '"DB_FINANCIERO"."JOB_ANULAR_PAGOS"',
                            defer => false,
                            force => true);
EXCEPTION
    WHEN OTHERS THEN
        DBMS_OUTPUT.PUT_LINE('El job aún no ha sido creado...');
END;
/