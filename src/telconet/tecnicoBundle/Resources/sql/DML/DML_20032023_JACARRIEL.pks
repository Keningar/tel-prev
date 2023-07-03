DECLARE
  clob_plantilla1 CLOB;
  clob_plantilla2 CLOB;
BEGIN
  SELECT PLANTILLA INTO clob_plantilla1 FROM DB_COMUNICACION.ADMI_PLANTILLA WHERE CODIGO = 'ENC-INST-EN';
  SELECT PLANTILLA INTO clob_plantilla2 FROM DB_COMUNICACION.ADMI_PLANTILLA WHERE CODIGO = 'ACT-ENT-EN-INS';

  clob_plantilla1 := REPLACE(clob_plantilla1, 'NETLIFE', 'ECUANET');
  
  clob_plantilla2 := REPLACE(clob_plantilla2, 'ó', 'O');
  clob_plantilla2 := REPLACE(clob_plantilla2, '1-700 NETLIFE (638-543)', 'www.ecuanet.ec');
  clob_plantilla2 := REPLACE(clob_plantilla2, 'O al 37-31-300', '72-01-200');
  clob_plantilla2 := REPLACE(clob_plantilla2, 'f9e314', '005ca8');
  clob_plantilla2 := REPLACE(clob_plantilla2, 'f9e314', '005ca8');
  clob_plantilla2 := REPLACE(clob_plantilla2, 'f69e18', '005ca8');
 clob_plantilla2 := REPLACE(clob_plantilla2, 'logo_netlife_big.jpg', 'logo_ecuanet.png');
  clob_plantilla2 := REPLACE(clob_plantilla2, 'Firma Netlife', 'Firma Ecuanet');
 
  UPDATE DB_COMUNICACION.ADMI_PLANTILLA SET PLANTILLA = clob_plantilla1 WHERE CODIGO = 'ENC-INST-EN';
  UPDATE DB_COMUNICACION.ADMI_PLANTILLA SET PLANTILLA = clob_plantilla2 WHERE CODIGO = 'ACT-ENT-EN-INS';
 
  COMMIT;
END;


