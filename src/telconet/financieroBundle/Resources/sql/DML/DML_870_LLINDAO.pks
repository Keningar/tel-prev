
-- Eliminar Motivos Duplicados en parametros generales
DELETE DB_GENERAL.ADMI_PARAMETRO_DET APD
WHERE APD.ID_PARAMETRO_DET IN (5445,5681);
