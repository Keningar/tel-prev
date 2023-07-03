/**
  * @author Edgar Holguín <eholguin@telconet.ec>
  * @version 1.0 27-08-2019 Se realiza rrollback de los inserts necesarios para proceso de facturación por cambio de forma de pago.
  */


DELETE FROM DB_COMERCIAL.ADMI_TIPO_SOLICITUD
WHERE  DESCRIPCION_SOLICITUD = 'SOLICITUD CAMBIO FORMA PAGO'
AND    ESTADO                = 'Activo';

DELETE 
FROM  DB_COMERCIAL.ADMI_CARACTERISTICA
WHERE DESCRIPCION_CARACTERISTICA = 'CAMBIO_FORMA_PAGO'
AND   ESTADO                     = 'Activo';


DELETE 
FROM  DB_COMERCIAL.ADMI_PRODUCTO
WHERE DESCRIPCION_PRODUCTO = 'CAMBIO_FORMA_PAGO'
AND   ESTADO               = 'Inactivo';


DELETE 
FROM  DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID = (SELECT CAB.ID_PARAMETRO
                      FROM   DB_GENERAL.ADMI_PARAMETRO_CAB CAB
                      WHERE  CAB.NOMBRE_PARAMETRO = 'FACTURACION_SOLICITUDES')
AND   DESCRIPCION  = 'Cambio de Forma de Pago'
AND   ESTADO       = 'Activo'
and   EMPRESA_COD  = '18';

COMMIT;
/
