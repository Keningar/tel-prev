/**
 * Se crea script para eliminar la parametrización en débitos por mejora 
 * en la generación de archivos para envío de débitos.
 *
 * @author Hector Lozano <hlozano@telconet.ec>
 * @version 1.0 
 * @since 18-09-2020
 */

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID = (SELECT CAB.ID_PARAMETRO
                      FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB
                      WHERE CAB.NOMBRE_PARAMETRO = 'EXCLUIR_FACT_INST_DEBITOS');


DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE NOMBRE_PARAMETRO = 'EXCLUIR_FACT_INST_DEBITOS';

COMMIT;
/
