--Update a la tabla de progreso tarea

ALTER TABLE DB_SOPORTE.INFO_PROGRESO_TAREA ADD COMUNICACION_ID INT; 

COMMENT ON COLUMN "DB_SOPORTE"."INFO_PROGRESO_TAREA"."COMUNICACION_ID" IS 'Es el numero de la tarea de la Info_Comunicacion';

UPDATE DB_SOPORTE.INFO_PROGRESO_TAREA IV
SET IV.COMUNICACION_ID = 0 
WHERE IV.COMUNICACION_ID is null;
COMMIT;