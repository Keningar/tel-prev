DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET T 
WHERE T.VALOR1 = 'DIAS_BLOQUEO_BOBINA_DESPACHO';

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET T 
WHERE T.VALOR1 = 'CANTIDAD_BLOQUEO_BOBINA';

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET T 
WHERE T.VALOR1 = 'DIAS_ALERTA_BOBINA';

COMMIT;