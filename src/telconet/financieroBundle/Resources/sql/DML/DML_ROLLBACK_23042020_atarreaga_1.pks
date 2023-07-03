/**
 * @author Alex Arreaga <atarreaga@telconet.ec>
 * @version 1.0
 * @since 23-04-2020    
 * Se crea reverso de la plantilla del reporte de documentos diferidos por Emergencia Sanitaria.
 */

DELETE FROM DB_COMUNICACION.INFO_ALIAS_PLANTILLA 
WHERE
PLANTILLA_ID = (
    SELECT ID_PLANTILLA
    FROM DB_COMUNICACION.ADMI_PLANTILLA
    WHERE CODIGO = 'RPT_DIFERIDOS'
    AND ESTADO   = 'Activo'
);

--SE ELIMINA PLANTILLA DE CORREO 
DELETE FROM DB_COMUNICACION.ADMI_PLANTILLA 
WHERE CODIGO     = 'RPT_DIFERIDOS' 
AND MODULO       = 'FINANCIERO'
AND ESTADO       = 'Activo'; 

COMMIT;
/
