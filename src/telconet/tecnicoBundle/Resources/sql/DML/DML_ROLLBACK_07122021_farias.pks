/**
 * @author Alberto Arias <farias@telconet.ec>
 * @version 1.0
 * @since 07-12-2021    
 * En caso de error, se modifica el valor2 de los parametros
 */

--=======================================================================
-- Reverso los detalles de parámetros para el reenvío de las credenciales del producto ECDF
--=======================================================================

UPDATE DB_GENERAL.ADMI_PARAMETRO_DET pd 
SET pd.VALOR2 = null, 
pd.FE_ULT_MOD = SYSDATE, 
pd.USR_ULT_MOD = 'farias', 
pd.IP_ULT_MOD = '127.0.0.1'
where pd.parametro_id = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB pc
                      WHERE pc.NOMBRE_PARAMETRO = 'COD_PLANTILLA_CORREO_PRODUCTOS_TV' AND pc.MODULO = 'COMERCIAL' AND pc.ESTADO = 'Activo')
AND pd.DESCRIPCION = 'ECDF' AND pd.ESTADO = 'Activo';


/* UPDATE PARA PERMITIR EL REENVIO DE CREDENCIALES DEL PRODUCTO ECDF*/
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET pd 
SET pd.VALOR3 = 'NO', 
pd.FE_ULT_MOD = SYSDATE, 
pd.USR_ULT_MOD = 'farias', 
pd.IP_ULT_MOD = '127.0.0.1'
where pd.parametro_id = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB pc 
                         WHERE pc.NOMBRE_PARAMETRO = 'ENVIO_SMS_POR_PRODUCTO' AND pc.PROCESO = 'ENVIO_SMS' AND pc.ESTADO = 'Activo')
AND pd.VALOR1 = 'NOMBRE_TECNICO' AND pd.VALOR2 = 'ECDF' AND pd.ESTADO = 'Activo';

COMMIT;
/