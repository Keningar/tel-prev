--SE AGREGA CAMPO FE_CAMBIOTURNO EN LA INFO_ASIGNACION_SOLICITUD_HIST
--
ALTER TABLE DB_SOPORTE.INFO_ASIGNACION_SOLICITUD_HIST ADD FE_CAMBIO_TURNO TIMESTAMP(6);
COMMENT ON COLUMN DB_SOPORTE."INFO_ASIGNACION_SOLICITUD_HIST"."FE_CAMBIO_TURNO" IS 'FECHA DE CAMBIO DE TURNO DE LA ASIGNACION PARA EL ESTADO STANDBY';

-- SE CAMBIA A NULLEABLE CAMPO USR_ASIGNADO EN LA INFO_ASIGNACION_SOLICITUD_HIST
--
ALTER TABLE DB_SOPORTE.INFO_ASIGNACION_SOLICITUD_HIST MODIFY (USR_ASIGNADO NULL);

-- SE CAMBIA A NULLEABLE CAMPO USR_ASIGNADO EN LA INFO_ASIGNACION_SOLICITUD
--
ALTER TABLE DB_SOPORTE.INFO_ASIGNACION_SOLICITUD MODIFY (USR_ASIGNADO NULL);
--
--SE AGREGA CAMPO TAB_VISIBLE EN LA TABLA DB_SOPORTE.INFO_ASIGNACION_SOLICITUD
--
ALTER TABLE DB_SOPORTE.INFO_ASIGNACION_SOLICITUD ADD TAB_VISIBLE VARCHAR2(50);
COMMENT ON COLUMN DB_SOPORTE."INFO_ASIGNACION_SOLICITUD"."TAB_VISIBLE" IS 'TAB DONDE ESTARA VISIBLE LA ASIGNACION';
--
/