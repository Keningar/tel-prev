/**
 * Se crea DML para eliminar la parametrización de la validación para los procesos de facturación de instalación.
 * @author Hector Lozano <hlozano@telconet.ec>
 * @version 1.0 
 * @since 17-11-2021
 */

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID = (SELECT CAB.ID_PARAMETRO
                      FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB
                      WHERE CAB.NOMBRE_PARAMETRO = 'PROCESOS_FACTURACION_INSTALACION');


DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE NOMBRE_PARAMETRO = 'PROCESOS_FACTURACION_INSTALACION';

COMMIT;
/
