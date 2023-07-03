/**
 * @author José Candelario <jcandelario@telconet.ec>
 * @version 1.0
 * @since 08-07-2020
 * Se crean las sentencias DML para reversar configuraciones de la estructura 
 * DB_GENERAL.ADMI_PARAMETRO_DET.
 */

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID IN ( SELECT ID_PARAMETRO
                        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
                        WHERE NOMBRE_PARAMETRO = 'DOCUMENTOS_NUM_FACTURA_SRI'
                        AND ESTADO             = 'Activo');
                        
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE NOMBRE_PARAMETRO = 'DOCUMENTOS_NUM_FACTURA_SRI'
  AND ESTADO           = 'Activo';


DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID IN ( SELECT ID_PARAMETRO
                        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
                        WHERE NOMBRE_PARAMETRO = 'ESTADOS_DOC_NUM_FACTURA_SRI'
                        AND ESTADO             = 'Activo');
                        
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE NOMBRE_PARAMETRO = 'ESTADOS_DOC_NUM_FACTURA_SRI'
  AND ESTADO           = 'Activo';

COMMIT;
/