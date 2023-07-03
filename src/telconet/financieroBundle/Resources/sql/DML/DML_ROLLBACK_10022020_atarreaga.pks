/**
 * @author Alex Arreaga <atarreaga@telconet.ec>
 * @version 1.0
 * @since 10-02-2020    
 * Se crea DML para reversar característica por edición de valores en nota de crédito.
 */

--SE ELIMINA CARACTERISTICA

DELETE FROM DB_COMERCIAL.ADMI_CARACTERISTICA 
WHERE DESCRIPCION_CARACTERISTICA = 'EDICION_VALORES_NC' 
AND ESTADO = 'Activo' 
AND TIPO   = 'FINANCIERO';

COMMIT;
/
