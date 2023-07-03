declare
  pd_fechaproceso date;
  pv_proceso varchar2(20) := 'PAGOS';
  pv_nocia varchar2(2) := '18';
begin
  -- SE LEIMINA DATA DE RETENCIONES GENERADAS CON PROCESO AGRUPADO
  DELETE NAF47_TNET.MIGRA_DOCUMENTO_ASOCIADO MDA
  WHERE MDA.NO_CIA = '18'
  AND EXISTS (SELECT NULL
              FROM NAF47_TNET.MIGRA_ARCGAE AE
              WHERE AE.ID_MIGRACION = MDA.MIGRACION_ID
              AND AE.NO_CIA = MDA.NO_CIA
              AND AE.MES = 9
              AND ANO = 2018
              AND EXISTS (SELECT NULL
                          FROM DB_FINANCIERO.ADMI_FORMA_PAGO AFP
                          WHERE AFP.ID_FORMA_PAGO = AE.ID_FORMA_PAGO
                          AND AFP.DESCRIPCION_FORMA_PAGO LIKE '%RETE%'));
  
  DELETE FROM NAF47_TNET.MIGRA_ARCGAL AL
  WHERE AL.NO_CIA = '18'
  AND EXISTS (SELECT NULL
              FROM NAF47_TNET.MIGRA_ARCGAE AE
              WHERE AE.ID_MIGRACION = AL.MIGRACION_ID
              AND AE.NO_CIA = AL.NO_CIA
              AND AE.MES = 9
              AND ANO = 2018
              AND EXISTS (SELECT NULL
                          FROM DB_FINANCIERO.ADMI_FORMA_PAGO AFP
                          WHERE AFP.ID_FORMA_PAGO = AE.ID_FORMA_PAGO
                          AND AFP.DESCRIPCION_FORMA_PAGO LIKE '%RETE%'));
                        
                        
  DELETE NAF47_TNET.MIGRA_ARCGAE AE
  WHERE AE.NO_CIA = '18'
  AND AE.MES = 9
  AND ANO = 2018
  AND EXISTS (SELECT NULL
              FROM DB_FINANCIERO.ADMI_FORMA_PAGO AFP
              WHERE AFP.ID_FORMA_PAGO = AE.ID_FORMA_PAGO
              AND AFP.DESCRIPCION_FORMA_PAGO LIKE '%RETE%');
  --
  COMMIT;
  --
  -- Call the procedure
  FOR Li_Idx IN 15..27 LOOP
    --
    pd_fechaproceso := to_date(Li_Idx ||'/'||to_char(sysdate, 'MM/YYYY'), 'DD/MM/YYYY');   
    --
    db_financiero.p_contabilizacion_automatica(pd_fechaproceso => pd_fechaproceso,
                               pv_proceso => pv_proceso,
                               pv_nocia => pv_nocia);
    --
  END LOOP;
end;
