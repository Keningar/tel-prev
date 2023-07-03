/**
 * Se crea DML para eliminar la parametrización en Facturación Offline.
 * @author Hector Lozano <hlozano@telconet.ec>
 * @version 1.0 
 * @since 08-01-2021
 */

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID = (SELECT CAB.ID_PARAMETRO
                      FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB
                      WHERE CAB.NOMBRE_PARAMETRO = 'FACTURACION_OFFLINE');


DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE NOMBRE_PARAMETRO = 'FACTURACION_OFFLINE';

COMMIT;
/
