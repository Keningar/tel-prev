
--ROLLBACK 
DELETE FROM DB_GENERAL.ADMI_MOTIVO WHERE NOMBRE_MOTIVO = 'FISCALIZACIÓN DE CUADRILLAS';

--ROOLBACK 
DELETE FROM DB_GENERAL.admi_parametro_det WHERE VALOR1 = 'PERFIL_FISCALIZAR';

DELETE FROM DB_GENERAL.admi_parametro_det WHERE VALOR1 = 'MOTIVO_REGISTRO_HISTORIAL_CUADRILLA';


COMMIT;
