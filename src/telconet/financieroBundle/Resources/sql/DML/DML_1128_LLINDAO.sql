-- liberacion de deposidos
UPDATE DB_FINANCIERO.INFO_PAGO_DET IPD
SET IPD.DEPOSITO_PAGO_ID = NULL
WHERE IPD.ID_PAGO_DET IN (10798811,10843324,10858181,10845844);

DELETE NAF47_TNET.MIGRA_DOCUMENTO_ASOCIADO MDA
WHERE MDA.DOCUMENTO_ORIGEN_ID IN (10798811,10843324,10858181,10845844)
AND MDA.TIPO_MIGRACION = 'CK'
AND MDA.NO_CIA = '18';

UPDATE NAF47_TNET.MIGRA_ARCKMM MM
SET  MM.MONTO = MM.MONTO - 0.07
WHERE MM.ID_MIGRACION = 6199816
AND MM.NO_CIA = '18';

UPDATE NAF47_TNET.MIGRA_ARCKML ML
SET ML.MONTO = ML.MONTO - 0.07,
    ML.MONTO_DOL = ML.MONTO_DOL - 0.07,
    ML.MONTO_DC = ML.MONTO_DC - 0.07
WHERE ML.MIGRACION_ID = 6199816
AND ML.NO_CIA = '18';

UPDATE NAF47_TNET.MIGRA_ARCKMM MM
SET  MM.MONTO = MM.MONTO - 168
WHERE MM.ID_MIGRACION = 6851116
AND MM.NO_CIA = '18';

UPDATE NAF47_TNET.MIGRA_ARCKML ML
SET ML.MONTO = ML.MONTO - 168,
    ML.MONTO_DOL = ML.MONTO_DOL - 168,
    ML.MONTO_DC = ML.MONTO_DC - 168
WHERE ML.MIGRACION_ID = 6851116
AND ML.NO_CIA = '18';

commit;
