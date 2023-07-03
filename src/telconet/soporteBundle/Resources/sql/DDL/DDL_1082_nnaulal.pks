--Update a la tabla de admi tarea para saber que motivos de finalización de tarea deben visualizar el móvil Operaciones

-- Add/modify columns 
   ALTER TABLE DB_SOPORTE.ADMI_TAREA add VISUALIZAR_MOVIL VARCHAR(1) DEFAULT 'S' NULL;
-- Add comments to the columns 
   COMMENT ON COLUMN DB_SOPORTE.ADMI_TAREA.VISUALIZAR_MOVIL  is 'Campo para saber que motivos de finalización de tarea deben visualizar el móvil Operaciones';