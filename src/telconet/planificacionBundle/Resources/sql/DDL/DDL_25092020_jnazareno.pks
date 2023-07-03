--SE AGREGA LA COLUMNA AUTORIZA_FINALIZAR EN LA TABLA INFO_CUADRILLA_PLANIF_CAB
--DEL AMBIENTE DB_SOPORTE
ALTER TABLE DB_SOPORTE.INFO_CUADRILLA_PLANIF_CAB ADD (AUTORIZA_FINALIZAR VARCHAR(2) DEFAULT 'N');
COMMENT ON COLUMN DB_SOPORTE.INFO_CUADRILLA_PLANIF_CAB.AUTORIZA_FINALIZAR	IS 'campo para autorizar la finalización de jornada en el movil.';

--SE AGREGA LA COLUMNA AUTORIZA_ALIMENTACION EN LA TABLA INFO_CUADRILLA_PLANIF_CAB
--DEL AMBIENTE DB_SOPORTE
ALTER TABLE DB_SOPORTE.INFO_CUADRILLA_PLANIF_CAB ADD (AUTORIZA_ALIMENTACION VARCHAR(2) DEFAULT 'N');
COMMENT ON COLUMN DB_SOPORTE.INFO_CUADRILLA_PLANIF_CAB.AUTORIZA_ALIMENTACION	IS 'campo para autorizar la alimentacion en el movil.';
