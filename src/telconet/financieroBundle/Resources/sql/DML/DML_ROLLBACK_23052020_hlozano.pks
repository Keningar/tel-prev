/**
 * Se crea DML para eliminar la parametrización del Escenario 2 en débitos por Emergencia Sanitaria.
 * @author Hector Lozano <hlozano@telconet.ec>
 * @version 1.0 
 * @since 23-05-2020
 */

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID = (SELECT CAB.ID_PARAMETRO
                      FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB
                      WHERE CAB.NOMBRE_PARAMETRO = 'ESCENARIOS_DEBITOS')
AND VALOR1         = 'ESCENARIO_1';

COMMIT;
/
