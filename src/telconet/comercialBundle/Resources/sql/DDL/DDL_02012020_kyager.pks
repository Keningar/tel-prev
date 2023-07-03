/**
 * @author Katherine Yager <kyager@telconet.ec>
 * @version 1.0  02-01-2020
 * Se deshabilita JOB_FACT_INS_PTO_ADICIONAL.
 *
 */
BEGIN
      DBMS_SCHEDULER.disable(name=>'"DB_COMERCIAL"."JOB_FACT_INS_PTO_ADICIONAL"', force => TRUE);
END;
/
