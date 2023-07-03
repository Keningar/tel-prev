DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID IN ( SELECT ID_PARAMETRO
                        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
                        WHERE NOMBRE_PARAMETRO = 'PROM_TENTATIVA_MENSAJES'
                        AND ESTADO             = 'Activo');
                        
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE NOMBRE_PARAMETRO = 'PROM_TENTATIVA_MENSAJES'
  AND ESTADO           = 'Activo';

COMMIT;
/