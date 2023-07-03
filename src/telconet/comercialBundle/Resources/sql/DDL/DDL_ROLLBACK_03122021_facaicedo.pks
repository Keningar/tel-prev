
/*
  DROP DEL JOB 'JOB_EJECUTA_PROMOCIONES_BW'
  DROP DEL JOB 'JOB_FINALIZA_PROMOCIONES_BW'
  DROP DEL JOB 'JOB_REPORTE_PROMOCIONES_BW'
  UPDATE DEL JOB 'JOB_PROMOCIONES_BW'
*/
SET SERVEROUTPUT ON;
SET DEFINE OFF;
BEGIN
  DBMS_SCHEDULER.DROP_JOB(job_name => '"DB_COMERCIAL"."JOB_EJECUTA_PROMOCIONES_BW"',
                          defer    => false,
                          force    => true);
  DBMS_OUTPUT.PUT_LINE('JOB_EJECUTA_PROMOCIONES_BW ELIMINADO');
  --
  DBMS_SCHEDULER.DROP_JOB(job_name => '"DB_COMERCIAL"."JOB_FINALIZA_PROMOCIONES_BW"',
                          defer    => false,
                          force    => true);
  DBMS_OUTPUT.PUT_LINE('JOB_FINALIZA_PROMOCIONES_BW ELIMINADO');
  --
  DBMS_SCHEDULER.DROP_JOB(job_name => '"DB_COMERCIAL"."JOB_REPORTE_PROMOCIONES_BW"',
                          defer    => false,
                          force    => true);
  DBMS_OUTPUT.PUT_LINE('JOB_REPORTE_PROMOCIONES_BW ELIMINADO');
  --
  DBMS_SCHEDULER.SET_ATTRIBUTE (
    name         =>  '"DB_COMERCIAL"."JOB_PROMOCIONES_BW"',
    attribute    =>  'job_action',
    value        =>  'DECLARE
                                     Lcl_Mensaje  CLOB;
                                   BEGIN
         DB_COMERCIAL.CMKG_PROMOCIONES_BW.P_PIERDE_PROMO_BW(''18'',''PROM_BW'');
                                       DB_COMERCIAL.CMKG_PROMOCIONES_BW.P_PROCESO_MASIVO_BW(''EXISTENTE'',Lcl_Mensaje);
                                   END;');
  DBMS_OUTPUT.PUT_LINE('JOB_PROMOCIONES_BW ACTUALIZADO...');
EXCEPTION
  WHEN OTHERS THEN
    DBMS_OUTPUT.PUT_LINE('ERROR AL ELIMINAR JOB_EJECUTA_PROMOCIONES_BW');
    DBMS_OUTPUT.PUT_LINE('ERROR AL ELIMINAR JOB_FINALIZA_PROMOCIONES_BW');
    DBMS_OUTPUT.PUT_LINE('ERROR AL ELIMINAR JOB_REPORTE_PROMOCIONES_BW');
END;
/
