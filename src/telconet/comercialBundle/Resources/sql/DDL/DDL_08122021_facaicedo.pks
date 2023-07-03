/**
 * Documentación para 'JOB_PROMOCIONES_BW'
 * Se actualiza el job de promociones para ejecutar el procedimiento 'P_PROCESO_MASIVO_BW' con 3 argumentos.
 */

SET SERVEROUTPUT ON;
SET DEFINE OFF;
BEGIN
    DBMS_SCHEDULER.SET_ATTRIBUTE (
   name         =>  '"DB_COMERCIAL"."JOB_PROMOCIONES_BW"',
   attribute    =>  'job_action',
   value        =>  'DECLARE
                                    Lcl_Mensaje  CLOB;
                                  BEGIN
        DB_COMERCIAL.CMKG_PROMOCIONES_BW.P_PIERDE_PROMO_BW(''18'',''PROM_BW'');
                                      DB_COMERCIAL.CMKG_PROMOCIONES_BW.P_PROCESO_MASIVO_BW(''EXISTENTE'',NULL,''Activo'',Lcl_Mensaje);
                                  END;');
  DBMS_OUTPUT.PUT_LINE('JOB_PROMOCIONES_BW ACTUALIZADO...');

END;
/
