--Borramos nuevos campos de la tabla ADMI_CUADRILLA.PREFERENCIA para aplicar el reverso 

ALTER TABLE DB_SOPORTE.INFO_CUADRILLA_PLANIF_CAB DROP COLUMN AUTORIZA_FINALIZAR;

ALTER TABLE DB_SOPORTE.INFO_CUADRILLA_PLANIF_CAB DROP COLUMN AUTORIZA_ALIMENTACION;

