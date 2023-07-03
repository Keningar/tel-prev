DECLARE
  --
  TYPE Gt_fecha IS TABLE of VARCHAR2(10);
  --
  Lv_EmpresaId    VARCHAR2(2) := '18';
  Lv_Prefijo      VARCHAR2(2) := 'MD';
  Lv_TipoDoc      VARCHAR2(5) := 'FAC';
  Lv_CodDiario    VARCHAR2(5) := 'M_F_1';
  Lv_ActContab    VARCHAR2(1) := 'S';
  Lv_UsuarioProc  VARCHAR2(13) := 'db_financiero';
  Lv_TipoProceso  VARCHAR2(6):= 'MASIVO';
  Lv_MensajeError VARCHAR2(4000);
  --
  Lr_Fechas       Gt_fecha := Gt_fecha('2020-04-15','2020-04-19','2020-04-20','2020-04-24','2020-04-28','2020-04-29','2020-04-30');
  Le_Error        EXCEPTION;
  --
  i PLS_INTEGER;
  --
BEGIN 
  --
  i := Lr_Fechas.FIRST;
  --
  WHILE ( i IS NOT NULL )
    LOOP
    --    
    DB_FINANCIERO.FNCK_TRANSACTION.P_REPROCESAMIENTO_CONTABLE( Lv_EmpresaId,
                                                               Lv_Prefijo,
                                                               Lv_TipoDoc,
                                                               Lv_CodDiario,
                                                               Lv_ActContab,
                                                               Lr_Fechas(i),
                                                               Lv_UsuarioProc,
                                                               Lv_TipoProceso,
                                                               Lv_MensajeError); 
    --
    IF NVL(Lv_MensajeError,'OK') != 'OK' THEN
      RAISE Le_Error;
    ELSE
      dbms_output.put_line('RE-PROCESO CONTABLE '||Lr_Fechas(i)||' EJECUTADO SATISFACTORIAMENTE');
    END IF;
    --
    i := Lr_Fechas.NEXT(i);
    --
  END LOOP;
EXCEPTION
  WHEN Le_Error THEN
    rollback;
    dbms_output.put_line(Lv_MensajeError);
    DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR( 'Telcos+',
                                          'Script-anomino-reproceso-contable-md',
                                          Lv_MensajeError,
                                          NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_FINANCIERO'),
                                          SYSDATE,
                                          NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1'));
  WHEN OTHERS THEN
    rollback;
    dbms_output.put_line(DBMS_UTILITY.FORMAT_ERROR_STACK || ' ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
    DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR( 'Telcos+',
                                          'Script-anomino-reproceso-contable-md',
                                          DBMS_UTILITY.FORMAT_ERROR_STACK || ' ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE,
                                          NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_FINANCIERO'),
                                          SYSDATE,
                                          NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1'));
END;
