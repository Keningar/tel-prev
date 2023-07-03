/**
 * Se realiza el DROP del JOB para compilarlo nuevamente.
 * @author Luis Cabrera <lcabrera@telconet.ec>
 * @version 1.0 20-10-2017 Versión inicial
 */
BEGIN
  DBMS_SCHEDULER.DROP_JOB (JOB_NAME => 'DB_FINANCIERO.PROCESAR_RECHAZADOS_IVA');
END;
/
