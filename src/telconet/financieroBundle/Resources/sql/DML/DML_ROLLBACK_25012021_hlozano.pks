/**
 * @author Hector Lozano <hlozano@telconet.ec>
 * @version 1.0
 * @since 23-04-2020    
 * Se crea reverso de la plantilla del reporte .
 */

DELETE FROM DB_COMUNICACION.INFO_ALIAS_PLANTILLA 
WHERE
PLANTILLA_ID = (
    SELECT ID_PLANTILLA
    FROM DB_COMUNICACION.ADMI_PLANTILLA
    WHERE CODIGO = 'RPT_DOC_RECHAZA'
    AND ESTADO   = 'Activo'
);

--SE ELIMINA PLANTILLA DE CORREO 
DELETE FROM DB_COMUNICACION.ADMI_PLANTILLA 
WHERE 
NOMBRE_PLANTILLA = 'Reporte de NC,pagos,anticipos que estan enlazados a facturas sin gestion' 
AND CODIGO       = 'RPT_DOC_RECHAZA' 
AND MODULO       = 'FINANCIERO'
AND ESTADO       = 'Activo'; 

COMMIT;
/

