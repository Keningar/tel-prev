ALTER TABLE DB_SOPORTE.INFO_CUADRILLA_PLANIF_DET ADD VISUALIZAR_MOVIL VARCHAR2(1) DEFAULT 'S';

COMMENT ON COLUMN DB_SOPORTE.INFO_CUADRILLA_PLANIF_DET.VISUALIZAR_MOVIL IS 'S = SI , N = NO , NULL = NO 
Solo si el campo esta en S la tarea se visualizara en el movil';