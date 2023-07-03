/**
 * @author Anabelle Peñaherrera<apenaherrera@telconet.ec>
 * @version 1.0  07-02-2020
 * Se deshabilita JOB_PIERDE_PROMO_MAPEO.
 *
 */
BEGIN
      DBMS_SCHEDULER.disable(name=>'"DB_COMERCIAL"."JOB_PIERDE_PROMO_MAPEO"', force => TRUE);
END;
/
