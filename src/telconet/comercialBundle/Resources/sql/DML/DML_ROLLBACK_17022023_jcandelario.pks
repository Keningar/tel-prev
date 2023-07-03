/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Script para revocar parametro detalle para logica de alcance de mapeo y aplicación de promociones por Clientes Nuevos
 * @author José Candelario <jcandelario@telconet.ec>
 * @version 1.0 23-02-2023 - Version Inicial.
 */
 
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID IN ( SELECT ID_PARAMETRO
                        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
                        WHERE NOMBRE_PARAMETRO = 'PROM_HORA_EJECUCION_JOB'
                        AND ESTADO             = 'Activo')
AND DESCRIPCION = 'PROM_HORA_ALCANCE_JOB_CLI_NUEV';

COMMIT;
/
