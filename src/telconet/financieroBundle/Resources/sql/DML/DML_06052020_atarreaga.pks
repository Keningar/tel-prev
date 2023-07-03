/**
 * Se actualiza el estado de la característica asociada de débito parciales diarios 
 * para el banco Guayaquil.
 * 
 * @author Alex Arreaga <atarreaga@telconet.ec>
 * @since 1.0 06-05-2020
 */

UPDATE DB_FINANCIERO.ADMI_FORMATO_DEBITO_CARACT 
SET ESTADO = 'Inactivo' 
WHERE 
CARACTERISTICA_ID = (SELECT ID_CARACTERISTICA 
                     FROM DB_COMERCIAL.ADMI_CARACTERISTICA 
                     WHERE DESCRIPCION_CARACTERISTICA = 'DEBITOS PARCIALES DIARIOS')
AND PROCESO = 'SUBIDA'
AND BANCO_TIPO_CUENTA_ID = 3;  

COMMIT;
/
