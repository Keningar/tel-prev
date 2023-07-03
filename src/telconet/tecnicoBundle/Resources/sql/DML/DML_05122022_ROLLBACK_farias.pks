/**
 *
 * ROLLBACK PARA LOS PARAMETROS EN LA VALIDACION DE PUERTOS Y TARJETAS DE MODELO OLT
 *	 
 * @author Alberto Arias <farias@telconet.ec>
 * @version 1.0 08-08-2022
 */
 
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'ADMINISTRACION_PUERTO_TARJETA_OLT'
                     );
                     
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE nombre_parametro = 'ADMINISTRACION_PUERTO_TARJETA_OLT';                     


UPDATE DB_GENERAL.ADMI_PARAMETRO_CAB 
SET estado='Inactivo'
WHERE nombre_parametro='ISB_TECNOLOGIAS_NO_PERMITIDAS';


COMMIT;
/