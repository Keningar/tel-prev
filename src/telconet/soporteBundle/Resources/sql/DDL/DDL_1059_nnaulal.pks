--Update a la tabla de admi tarea para el control de fibra

-- Add/modify columns 
   ALTER TABLE DB_SOPORTE.ADMI_TAREA add REQUIERE_FIBRA VARCHAR(1) DEFAULT 'N' NULL;
-- Add comments to the columns 
   COMMENT ON COLUMN DB_SOPORTE.ADMI_TAREA.REQUIERE_FIBRA  is 'Campo para identificar los motivos que requieren fibra para el control de la misma';