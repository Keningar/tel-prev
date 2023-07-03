-- Eliminamos los nuevos parametros del proyecto
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB
                      WHERE NOMBRE_PARAMETRO = 'PRODUCTOS ADICIONALES AUTOMATICOS');
    
-- Eliminamos la cabecera del nuevo parametro
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE NOMBRE_PARAMETRO = 'PRODUCTOS ADICIONALES AUTOMATICOS';

-- Eliminamos los adicionales que deben aparecer en pantalla activacion de movil
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB
                      WHERE NOMBRE_PARAMETRO = 'ACTIVACION_PRODUCTOS_MEGADATOS')
AND DESCRIPCION = 'ECOMMERCE BASIC' AND VALOR1 = 'KO02';

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB
                      WHERE NOMBRE_PARAMETRO = 'ACTIVACION_PRODUCTOS_MEGADATOS')
AND DESCRIPCION = 'Netlife Assistance Pro' AND VALOR1 = 'KO01';

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB
                      WHERE NOMBRE_PARAMETRO = 'ACTIVACION_PRODUCTOS_MEGADATOS')
AND DESCRIPCION = 'NetlifeAssistance' AND VALOR1 = 'ASSI';

-- Eliminar la nueva caracteristicas de productos konibit
DELETE FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
WHERE CARACTERISTICA_ID = (SELECT ID_CARACTERISTICA from DB_COMERCIAL.ADMI_CARACTERISTICA
						   where DESCRIPCION_CARACTERISTICA = 'ACTIVO KONIBIT');

-- Eliminamos la nueva caracteristica para konibit
DELETE FROM DB_COMERCIAL.ADMI_CARACTERISTICA 
WHERE DESCRIPCION_CARACTERISTICA = 'ACTIVO KONIBIT';

-- Eliminar la nueva plantilla de correo de konibit
DELETE FROM DB_COMUNICACION.ADMI_PLANTILLA 
WHERE NOMBRE_PLANTILLA = 'Notificacion de error en konibit'
AND CODIGO = 'NOT_ERR_KON' AND MODULO = 'TECNICO' AND EMPRESA_COD = 18;

COMMIT;
/