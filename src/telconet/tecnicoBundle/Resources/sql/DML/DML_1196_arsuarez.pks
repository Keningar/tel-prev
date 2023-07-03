--Actualización de la descripción de la región para obtención de subredes
--Antes : Se manejaba R1 y R2
--Ahora : Se manejará COSTA/SIERRA 

UPDATE DB_GENERAL.ADMI_PARAMETRO_DET SET VALOR5 = 'COSTA' WHERE ID_PARAMETRO_DET = 5788;
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET SET VALOR5 = 'SIERRA' WHERE ID_PARAMETRO_DET = 5789;

/
