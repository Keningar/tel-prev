SET DEFINE OFF;

-- Actualizar el estado en la tabla DB_GENERAL.ADMI_PARAMETRO_DET para el producto TELCOHOME
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
SET VALOR6 = 'Descuento'
WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
WHERE NOMBRE_PARAMETRO = 'PRODUCTOS_ESPECIALES_UM')
AND VALOR1 = 'TELCOHOME'
AND EMPRESA_COD = '10';

COMMIT;

/