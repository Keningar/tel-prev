/**
 *
 * ROLLBACK PARA LOS PARAMETROS DEL WS DE TOOLBOX
 *	 
 * @author Jonathan Mazon <jmazon@telconet.ec>
 * @version 1.0 28-06-2021
 */


--PARAMOUNT

DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'CONFIGURACION_WS_CLEAR_CACHE_PARAMOUNT'
                     );

DELETE FROM db_general.admi_parametro_cab 
WHERE nombre_parametro = 'CONFIGURACION_WS_CLEAR_CACHE_PARAMOUNT';


--NOGGIN

DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'CONFIGURACION_WS_CLEAR_CACHE_NOGGIN'
                     );

DELETE FROM db_general.admi_parametro_cab 
WHERE nombre_parametro = 'CONFIGURACION_WS_CLEAR_CACHE_NOGGIN';


COMMIT;

/
