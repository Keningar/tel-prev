
CREATE TABLE "DB_COMERCIAL"."INFO_SEGUIMIENTO_SERVICIO" 
   (	
   "ID_SEGUIMIENTO_SERVICIO" NUMBER(35,0) NOT NULL , 
	"SERVICIO_ID" NUMBER(35,0) NOT NULL , 
	"ESTADO" VARCHAR2(100), 
	"USR_CREACION" VARCHAR2(20) NOT NULL , 
	"FE_CREACION" TIMESTAMP (6) NOT NULL , 
	"FE_MODIFICACION" TIMESTAMP (6), 
	"IP_CREACION" VARCHAR2(15), 
  "DEPARTAMENTO" VARCHAR2(80),
	"TIEMPO_ESTIMADO" NUMBER(35,0) , 
	"TIEMPO_TRANSCURRIDO" NUMBER(35,0) , 
	"DIAS_TRANSCURRIDO" NUMBER(35,0) ,
	"OBSERVACION" VARCHAR2(3000));
ALTER TABLE "DB_COMERCIAL"."INFO_SEGUIMIENTO_SERVICIO" move tablespace "DB_TELCONET"; 	 
COMMENT ON COLUMN "DB_COMERCIAL"."INFO_SEGUIMIENTO_SERVICIO"."ID_SEGUIMIENTO_SERVICIO" IS 'Id de la tabla de Seguimiento.';
COMMENT ON COLUMN "DB_COMERCIAL"."INFO_SEGUIMIENTO_SERVICIO"."SERVICIO_ID" IS 'Id de la tabla Info_Servicio.';
COMMENT ON COLUMN "DB_COMERCIAL"."INFO_SEGUIMIENTO_SERVICIO"."ESTADO" IS 'Estado correspondiente al servicio.';
COMMENT ON COLUMN "DB_COMERCIAL"."INFO_SEGUIMIENTO_SERVICIO"."USR_CREACION" IS 'Usuario  que Registra la Accion.';
COMMENT ON COLUMN "DB_COMERCIAL"."INFO_SEGUIMIENTO_SERVICIO"."FE_CREACION" IS 'Registra la fecha en la que se realiza la accion.';
COMMENT ON COLUMN "DB_COMERCIAL"."INFO_SEGUIMIENTO_SERVICIO"."FE_MODIFICACION" IS 'Registra la fecha en la que se modifica la accion.';
COMMENT ON COLUMN "DB_COMERCIAL"."INFO_SEGUIMIENTO_SERVICIO"."IP_CREACION" IS 'Registra la Ip con la que se registra la accion.';
COMMENT ON COLUMN "DB_COMERCIAL"."INFO_SEGUIMIENTO_SERVICIO"."DEPARTAMENTO" IS 'Registra el Departamento que atendio el servicio.';
COMMENT ON COLUMN "DB_COMERCIAL"."INFO_SEGUIMIENTO_SERVICIO"."TIEMPO_ESTIMADO" IS 'Tiempo que se estima en realizar la accion.';
COMMENT ON COLUMN "DB_COMERCIAL"."INFO_SEGUIMIENTO_SERVICIO"."TIEMPO_TRANSCURRIDO" IS 'Tiempo que tomo realizar la accion.';
COMMENT ON COLUMN "DB_COMERCIAL"."INFO_SEGUIMIENTO_SERVICIO"."DIAS_TRANSCURRIDO" IS 'Días que tomo realizar la accion.';
COMMENT ON COLUMN "DB_COMERCIAL"."INFO_SEGUIMIENTO_SERVICIO"."OBSERVACION" IS 'Comentario opcional del seguimiento.';

ALTER TABLE "DB_COMERCIAL"."INFO_SEGUIMIENTO_SERVICIO"ADD CONSTRAINT "INFO_SEGUIMIENTO_SERVICIO_PK" PRIMARY KEY ("ID_SEGUIMIENTO_SERVICIO");
ALTER TABLE "DB_COMERCIAL"."INFO_SEGUIMIENTO_SERVICIO"ADD CONSTRAINT "INFO_SEGUIMIENTO_SERVICIO_fk_1" FOREIGN KEY ("SERVICIO_ID")
   REFERENCES "DB_COMERCIAL"."INFO_SERVICIO" ("ID_SERVICIO");
   
CREATE SEQUENCE  "DB_COMERCIAL"."SEQ_INFO_SEGUIMIENTO_SERVICIO"  MINVALUE 1 MAXVALUE 9999999999999999999999999999 INCREMENT BY 1 START WITH 1 NOCACHE  NOORDER  NOCYCLE ;