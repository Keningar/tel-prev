/**
 * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
 * @version 1.0  09-07-2021
 * Se deshabilita JOB_MAPEO_PROMO_CLI_NUEVOS.
 *
 */
BEGIN
      DBMS_SCHEDULER.disable(name=>'"DB_COMERCIAL"."JOB_MAPEO_PROMO_CLI_NUEVOS"', force => TRUE);
END;
/

