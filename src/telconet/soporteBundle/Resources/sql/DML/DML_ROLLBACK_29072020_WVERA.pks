--ELIMINANDO VALOR PARAMETRIZADO DE CABECERA
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE ID_PARAMETRO =(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'VALIDACION_ENLACE_TIPOS_IP');

--ELIMINANDO VALOR PARAMETRIZADO DE UNIDAD DE LATENCIA
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE VALOR1 = 'FILTRO_VTIPOS_IP';

COMMIT

/