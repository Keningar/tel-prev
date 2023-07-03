DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID IN (
              SELECT ID_PARAMETRO
                FROM DB_GENERAL.ADMI_PARAMETRO_CAB
              WHERE NOMBRE_PARAMETRO = 'Razón social para NO solicitar automáticamente el IE');
              
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE ID_PARAMETRO IN (
              SELECT ID_PARAMETRO
                FROM DB_GENERAL.ADMI_PARAMETRO_CAB
              WHERE NOMBRE_PARAMETRO = 'Razón social para NO solicitar automáticamente el IE');

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID IN (
              SELECT ID_PARAMETRO
                FROM DB_GENERAL.ADMI_PARAMETRO_CAB
              WHERE NOMBRE_PARAMETRO = 'Concentradores para solicitar automáticamente los IE');
              
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE ID_PARAMETRO IN (
              SELECT ID_PARAMETRO
                FROM DB_GENERAL.ADMI_PARAMETRO_CAB
              WHERE NOMBRE_PARAMETRO = 'Concentradores para solicitar automáticamente los IE');
COMMIT;
/