--Se agrega nuevo campo DATO_ADICIONAL para que se puedan ingresar datos adicionales a la asignación
--
ALTER TABLE "DB_SOPORTE"."INFO_ASIGNACION_SOLICITUD" ADD "DATO_ADICIONAL" VARCHAR2(250);

COMMENT ON COLUMN "DB_SOPORTE"."INFO_ASIGNACION_SOLICITUD"."DATO_ADICIONAL"  IS 'En este campo se almacena informacion adicional para la asignacion';

/
