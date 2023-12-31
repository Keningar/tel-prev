/**
 * Rollback para tabla ADMI_PARAMETRO_DET
 * @author Roberth Cobeña <rcobena@telconet.ec>
 * @version 1.0
 * @since 16/06/2021
 */
 
-- Eliminar en la tabla ADMI_PARAMETRO_DET
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'CONFIGURACION_SCOPES')
AND VALOR1 = 'TIPO_SCOPE' AND VALOR2 = 'SCGN' AND VALOR3 = 'Dinamico + CGNAT';

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'CONFIGURACION_SCOPES')
AND VALOR1 = 'TIPO_SCOPE' AND VALOR2 = 'SFP' AND VALOR3 = 'Fijo + CGNAT';

COMMIT;
/


