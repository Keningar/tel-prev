/**
 * @author Javier Hidalgo <jihidalgo@telconet.ec>
 * @version 1.0
 * @since 22-06-2022    
 * Se crea Rollback de DDL para JOB DB_FINANCIERO.DESCARTA_PAGOS_EN_LINEA.
 * Se actualiza frecuencia de ejecucion del JOB. Su ejecucion sera 
 * cada 24 horas.  
 */
BEGIN
  SYS.DBMS_SCHEDULER.DISABLE
    (name => 'DB_FINANCIERO.DESCARTA_PAGOS_EN_LINEA');
END;
/
BEGIN
  SYS.DBMS_SCHEDULER.SET_ATTRIBUTE_NULL
    ( name      => 'DB_FINANCIERO.DESCARTA_PAGOS_EN_LINEA'
     ,attribute => 'SCHEDULE_NAME');
END;
/
BEGIN
  SYS.DBMS_SCHEDULER.SET_ATTRIBUTE_NULL
    ( name      => 'DB_FINANCIERO.DESCARTA_PAGOS_EN_LINEA'
     ,attribute => 'REPEAT_INTERVAL');
END;
/
BEGIN
  SYS.DBMS_SCHEDULER.SET_ATTRIBUTE_NULL
    ( name      => 'DB_FINANCIERO.DESCARTA_PAGOS_EN_LINEA'
     ,attribute => 'EVENT_SPEC');
END;
/
BEGIN
  SYS.DBMS_SCHEDULER.SET_ATTRIBUTE_NULL
    ( name      => 'DB_FINANCIERO.DESCARTA_PAGOS_EN_LINEA'
     ,attribute => 'START_DATE');
END;
/
BEGIN
  SYS.DBMS_SCHEDULER.SET_ATTRIBUTE_NULL
    ( name      => 'DB_FINANCIERO.DESCARTA_PAGOS_EN_LINEA'
     ,attribute => 'END_DATE');
END;
/
BEGIN
  SYS.DBMS_SCHEDULER.SET_ATTRIBUTE
    ( name      => 'DB_FINANCIERO.DESCARTA_PAGOS_EN_LINEA'
     ,attribute => 'START_DATE'
     ,value     => TO_TIMESTAMP_TZ('2016/12/02 22:37:59.684008 America/Guayaquil','yyyy/mm/dd hh24:mi:ss.ff tzr'));
END;
/
BEGIN
  SYS.DBMS_SCHEDULER.SET_ATTRIBUTE
    ( name      => 'DB_FINANCIERO.DESCARTA_PAGOS_EN_LINEA'
     ,attribute => 'REPEAT_INTERVAL'
     ,value     => 'FREQ=DAILY;BYHOUR=0;BYMINUTE=0;BYSECOND=0');
END;
/
BEGIN
  SYS.DBMS_SCHEDULER.ENABLE
    (name => 'DB_FINANCIERO.DESCARTA_PAGOS_EN_LINEA');
END;
/