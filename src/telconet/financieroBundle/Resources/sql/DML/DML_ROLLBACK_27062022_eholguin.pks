/**
 * @author Edgar Holguín <eholguin@telconet.ec>
 * @version 1.0
 * @since 28-06-2022    
 */

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET WHERE PARAMETRO_ID = (      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'AUTOMATIZACION PAGOS'
      AND MODULO             = 'FINANCIERO'
      AND ESTADO             = 'Activo') AND DESCRIPCION = 'CONFIGURACION NFS' AND VALOR1 = 'AutomatizacionPagos';

COMMIT;
/
