/**
 * Documentación para ROLLBACK_JOB_RPT_CAMBIO_FORMA_PAGO
 * Eliminación de Job que se ejecuta para envío de reporte de cambios de forma de pago.
 * @author Edgar Holguín <eholguin@telconet.ec>
 * @version 1.0
 * @since 27-09-2019  
 */

BEGIN
  DBMS_SCHEDULER.DROP_JOB(job_name => '"DB_FINANCIERO"."JOB_RPT_CAMBIO_FORMA_PAGO"',
                          defer    => false,
                          force    => true);
EXCEPTION
  WHEN OTHERS THEN
    DBMS_OUTPUT.PUT_LINE('ERROR AL ELIMINAR JOB');
END;
/
