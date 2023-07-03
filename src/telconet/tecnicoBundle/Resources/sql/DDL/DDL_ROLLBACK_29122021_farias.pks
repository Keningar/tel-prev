/*
  DROP DEL JOB 'JOB_CANCELAR_PROD_SUSCRIPCION'
*/
SET SERVEROUTPUT ON;
SET DEFINE OFF;
BEGIN
  DBMS_SCHEDULER.DROP_JOB(job_name => '"DB_INFRAESTRUCTURA"."JOB_CANCELAR_PROD_SUSCRIPCION"',
                          defer    => false,
                          force    => true);
  DBMS_OUTPUT.PUT_LINE('JOB_CANCELAR_PROD_SUSCRIPCION ELIMINADO');
  --
EXCEPTION
  WHEN OTHERS THEN
    DBMS_OUTPUT.PUT_LINE('ERROR AL ELIMINAR JOB_CANCELAR_PROD_SUSCRIPCION');
END;
/