DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET WHERE PARAMETRO_ID = (select ID_PARAMETRO from db_general.admi_parametro_cab where nombre_parametro='FACTURACION UNICA' AND MODULO='FINANCIERO');
DELETE from db_general.admi_parametro_cab where nombre_parametro='FACTURACION UNICA' AND MODULO='FINANCIERO';
