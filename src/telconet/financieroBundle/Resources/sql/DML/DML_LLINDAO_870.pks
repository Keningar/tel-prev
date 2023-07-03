DECLARE
    V_NO_CIA VARCHAR2(2) := '10';
    V_FECHA  VARCHAR2(11);
    MSG_RET  VARCHAR2(3000);
    Ln_Indice NUMBER;
    Lv_Periodo VARCHAR2(10);
BEGIN
    --
    FOR Ln_Indice IN 1..3 LOOP
      
      CASE 
        WHEN Ln_Indice = 1 THEN
          V_FECHA := '22/02/2018';
        WHEN Ln_Indice = 2 THEN
          V_FECHA := '28/02/2018';
        ELSE
          V_FECHA := '20/03/2018';
      END CASE;
      --
      DB_FINANCIERO.FNKG_CONTABILIZAR_PAGOS_RET.PROCESAR_PAGO_RETENCIONESXDIA
                   ( V_NO_CIA => V_NO_CIA, 
                     V_FECHA => V_FECHA, 
                     MSG_RET => MSG_RET );
      --
      IF MSG_RET IS NOT NULL THEN
        DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR( 
                   'Telcos+', 
                   'SCRIT ANONIMO REPROCESO PAGOS RETENCION', 
                   MSG_RET,
                   NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_FINANCIERO'),
                   SYSDATE, 
                   NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1') );
      END IF;
      --        
    END LOOP;
    --
    DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR( 
                   'Telcos+', 
                   'SCRIT ANONIMO REPROCESO PAGOS RETENCION', 
                   'SE REPROCESO INFORMACION DE 22, 28 FEBERRO 2018 Y 20 MARZO 2018',
                   NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_FINANCIERO'),
                   SYSDATE, 
                   NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1') );

    --
END;
/

