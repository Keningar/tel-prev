/**
 * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
 * @version 1.0  09-07-2021
 * Se deshabilita JOB_APLICA_PROMO_MENSUAL.
 *
 */
BEGIN
      DBMS_SCHEDULER.disable(name=>'"DB_COMERCIAL"."JOB_APLICA_PROMO_MENSUAL"', force => TRUE);
END;
/

