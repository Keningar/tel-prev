--=======================================================================
--  Se realiza actualización a nuevo SKU proporcionado por digiway para realizar las nuevas altas de suscripciones McAfee
--=======================================================================

UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
SET VALOR2='1132-61648-1dmma'
WHERE ID_PARAMETRO_DET = 187;

commit;
/