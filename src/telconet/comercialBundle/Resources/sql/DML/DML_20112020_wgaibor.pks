UPDATE DB_GENERAL.ADMI_PARAMETRO_DET APD
SET APD.VALOR1 = 'S'
WHERE PARAMETRO_ID =(
SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB apc 
WHERE APC.NOMBRE_PARAMETRO = 'BANDERA_NFS');

COMMIT;

/