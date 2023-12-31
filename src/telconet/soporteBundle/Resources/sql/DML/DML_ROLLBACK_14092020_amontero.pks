DELETE FROM DB_SOPORTE.ADMI_TAREA WHERE  USR_CREACION = 'amontero'  AND PROCESO_ID IN (
  SELECT ID_PROCESO FROM DB_SOPORTE.ADMI_PROCESO
  WHERE NOMBRE_PROCESO IN ('TAREAS DE IPCCL1 - OTROS','TAREAS DE NOC - OTROS', 'TAREAS DE BOC - OTROS', 'TAREAS OTN - RED NACIONAL', 'TAREAS OTN - RED INTERNACIONAL', 'TAREAS OTN - REQUERIMIENTOS ESPECIALES') 
  AND USR_CREACION ='amontero'
);

DELETE FROM DB_SOPORTE.ADMI_PROCESO_EMPRESA
  WHERE USR_CREACION = 'amontero'
  AND PROCESO_ID IN (
    SELECT ID_PROCESO 
    FROM DB_SOPORTE.ADMI_PROCESO 
    WHERE NOMBRE_PROCESO IN ('TAREAS DE IPCCL1 - OTROS','TAREAS DE NOC - OTROS', 'TAREAS DE BOC - OTROS', 'TAREAS OTN - RED NACIONAL', 'TAREAS OTN - RED INTERNACIONAL', 'TAREAS OTN - REQUERIMIENTOS ESPECIALES')
  );

DELETE FROM DB_SOPORTE.ADMI_PROCESO
  WHERE NOMBRE_PROCESO IN ('TAREAS DE IPCCL1 - OTROS','TAREAS DE NOC - OTROS', 'TAREAS DE BOC - OTROS', 'TAREAS OTN - RED NACIONAL', 'TAREAS OTN - RED INTERNACIONAL', 'TAREAS OTN - REQUERIMIENTOS ESPECIALES') 
  AND USR_CREACION ='amontero';

DELETE FROM ADMI_PARAMETRO_DET WHERE USR_CREACION = 'amontero' AND DESCRIPCION = 'CATEGORIAS DE LAS TAREAS'
AND VALOR5 IN (132,133,839,825,115,137,821,126,145);


DELETE FROM DB_GENERAL.ADMI_MOTIVO 
WHERE USR_CREACION = 'amontero' 
AND ID_MOTIVO IN (
    SELECT VALOR4 FROM DB_GENERAL.ADMI_PARAMETRO_DET WHERE USR_CREACION = 'amontero' 
    AND PARAMETRO_ID = (SELECT id_parametro FROM db_general.admi_parametro_cab WHERE nombre_parametro = 'MOTIVOS_CATEGORIA_DE_TAREA') 
    AND VALOR5 IN (132,133,839,825,115,137,821,126,145)
    AND USR_CREACION = 'amontero'
);

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET WHERE USR_CREACION = 'amontero' 
AND PARAMETRO_ID = (SELECT id_parametro FROM db_general.admi_parametro_cab WHERE nombre_parametro = 'MOTIVOS_CATEGORIA_DE_TAREA') 
AND VALOR5 IN (132,133,839,825,115,137,821,126,145)
AND USR_CREACION = 'amontero'
;

COMMIT;

/
