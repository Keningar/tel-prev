ALTER TABLE DB_COMERCIAL.ADMI_PLANTILLA_HORARIO_CAB
ADD (CUPO_WEB INTEGER,
     CUPO_MOBILE INTEGER,
     CUPO_TOTAL INTEGER,
     FE_ULT_GENERACION TIMESTAMP);

ALTER TABLE DB_COMERCIAL.ADMI_PLANTILLA_HORARIO_DET
ADD (CUPO_WEB INTEGER,
     CUPO_MOBILE INTEGER);


--------------------------------------------------------
--  DDL for Table INFO_AGENDA_CUPO_CAB
--------------------------------------------------------

  CREATE TABLE DB_COMERCIAL.INFO_AGENDA_CUPO_CAB
   (	"ID_AGENDA_CUPOS" NUMBER,
	"EMPRESA_COD" VARCHAR2(3 BYTE),
	"FECHA_PERIODO" DATE,
	"TOTAL_CUPOS" NUMBER,
	"OBSERVACION" VARCHAR2(1000 BYTE),
	"FE_CREACION" TIMESTAMP (6),
	"USR_CREACION" VARCHAR2(50 BYTE),
	"IP_CREACION" VARCHAR2(20 BYTE),
	"JURISDICCION_ID" NUMBER,
	"PLANTILLA_HORARIO_ID" NUMBER,
	"FE_MODIFICA" TIMESTAMP (6),
	"USR_MODIFICA" VARCHAR2(50 BYTE),
	"ESTADO_REGISTRO" VARCHAR2(20 BYTE) DEFAULT 'Activo'
   ) ;

   COMMENT ON COLUMN "DB_COMERCIAL"."INFO_AGENDA_CUPO_CAB"."ID_AGENDA_CUPOS" IS 'Identificador del registro.';
   COMMENT ON COLUMN "DB_COMERCIAL"."INFO_AGENDA_CUPO_CAB"."FECHA_PERIODO" IS 'Fecha de agenda.';
   COMMENT ON COLUMN "DB_COMERCIAL"."INFO_AGENDA_CUPO_CAB"."TOTAL_CUPOS" IS 'Total de cupos (cupos movil+cupos web).';
   COMMENT ON COLUMN "DB_COMERCIAL"."INFO_AGENDA_CUPO_CAB"."OBSERVACION" IS 'Observación para el registro.';
   COMMENT ON COLUMN "DB_COMERCIAL"."INFO_AGENDA_CUPO_CAB"."FE_CREACION" IS 'Fecha de creación del registro.';
   COMMENT ON COLUMN "DB_COMERCIAL"."INFO_AGENDA_CUPO_CAB"."USR_CREACION" IS 'Usuario de creación del registro.';
   COMMENT ON COLUMN "DB_COMERCIAL"."INFO_AGENDA_CUPO_CAB"."IP_CREACION" IS 'Ip de creación del registro.';
   COMMENT ON COLUMN "DB_COMERCIAL"."INFO_AGENDA_CUPO_CAB"."JURISDICCION_ID" IS 'Id de la jurisdicción a la cual pertenece la configuración.';
   COMMENT ON COLUMN "DB_COMERCIAL"."INFO_AGENDA_CUPO_CAB"."PLANTILLA_HORARIO_ID" IS 'Id de la plantilla de la cual se deriva la configuración.';
   COMMENT ON COLUMN "DB_COMERCIAL"."INFO_AGENDA_CUPO_CAB"."FE_MODIFICA" IS 'Fecha de modificación del registro.';
   COMMENT ON COLUMN "DB_COMERCIAL"."INFO_AGENDA_CUPO_CAB"."USR_MODIFICA" IS 'Usuario de modificación del registro.';
   COMMENT ON COLUMN "DB_COMERCIAL"."INFO_AGENDA_CUPO_CAB"."ESTADO_REGISTRO" IS 'Estado del registro.';
   COMMENT ON TABLE "DB_COMERCIAL"."INFO_AGENDA_CUPO_CAB"  IS 'Tabla de cabecera para el registro de configuraciones de agenda de planificación.';
--------------------------------------------------------
--  DDL for Index INFO_AGENDA_CUPO_CAB_PK
--------------------------------------------------------

  CREATE UNIQUE INDEX "DB_COMERCIAL"."INFO_AGENDA_CUPO_CAB_PK" ON "DB_COMERCIAL"."INFO_AGENDA_CUPO_CAB" ("ID_AGENDA_CUPOS") ;
--------------------------------------------------------
--  DDL for Index IDX_JURISDICCION_ID
--------------------------------------------------------

  CREATE INDEX "DB_COMERCIAL"."IDX_JURISDICCION_ID" ON "DB_COMERCIAL"."INFO_AGENDA_CUPO_CAB" ("JURISDICCION_ID") ;
--------------------------------------------------------
--  DDL for Index IDX_PLANTILLA_HORARIO_ID
--------------------------------------------------------

  CREATE INDEX "DB_COMERCIAL"."IDX_PLANTILLA_HORARIO_ID" ON "DB_COMERCIAL"."INFO_AGENDA_CUPO_CAB" ("PLANTILLA_HORARIO_ID") ;
--------------------------------------------------------
--  DDL for Index INFO_AGENDA_CUPO_CAB_UK1
--------------------------------------------------------

  CREATE UNIQUE INDEX "DB_COMERCIAL"."INFO_AGENDA_CUPO_CAB_UK1" ON "DB_COMERCIAL"."INFO_AGENDA_CUPO_CAB" ("EMPRESA_COD", "FECHA_PERIODO", "PLANTILLA_HORARIO_ID") ;
--------------------------------------------------------
--  Constraints for Table INFO_AGENDA_CUPO_CAB
--------------------------------------------------------

  ALTER TABLE "DB_COMERCIAL"."INFO_AGENDA_CUPO_CAB" ADD CONSTRAINT "INFO_AGENDA_CUPO_CAB_UK1" UNIQUE ("EMPRESA_COD", "FECHA_PERIODO", "PLANTILLA_HORARIO_ID");
  ALTER TABLE "DB_COMERCIAL"."INFO_AGENDA_CUPO_CAB" MODIFY ("ID_AGENDA_CUPOS" NOT NULL ENABLE);
  ALTER TABLE "DB_COMERCIAL"."INFO_AGENDA_CUPO_CAB" MODIFY ("FECHA_PERIODO" NOT NULL ENABLE);
  ALTER TABLE "DB_COMERCIAL"."INFO_AGENDA_CUPO_CAB" MODIFY ("TOTAL_CUPOS" NOT NULL ENABLE);
  ALTER TABLE "DB_COMERCIAL"."INFO_AGENDA_CUPO_CAB" MODIFY ("FE_CREACION" NOT NULL ENABLE);
  ALTER TABLE "DB_COMERCIAL"."INFO_AGENDA_CUPO_CAB" MODIFY ("USR_CREACION" NOT NULL ENABLE);
  ALTER TABLE "DB_COMERCIAL"."INFO_AGENDA_CUPO_CAB" MODIFY ("IP_CREACION" NOT NULL ENABLE);
  ALTER TABLE "DB_COMERCIAL"."INFO_AGENDA_CUPO_CAB" MODIFY ("JURISDICCION_ID" NOT NULL ENABLE);
  ALTER TABLE "DB_COMERCIAL"."INFO_AGENDA_CUPO_CAB" MODIFY ("PLANTILLA_HORARIO_ID" NOT NULL ENABLE);
  ALTER TABLE "DB_COMERCIAL"."INFO_AGENDA_CUPO_CAB" MODIFY ("EMPRESA_COD" NOT NULL ENABLE);
  ALTER TABLE "DB_COMERCIAL"."INFO_AGENDA_CUPO_CAB" MODIFY ("ESTADO_REGISTRO" NOT NULL ENABLE);
  ALTER TABLE "DB_COMERCIAL"."INFO_AGENDA_CUPO_CAB" ADD CONSTRAINT "INFO_AGENDA_CUPO_CAB_PK" PRIMARY KEY ("ID_AGENDA_CUPOS");


  --------------------------------------------------------
--  DDL for Table INFO_AGENDA_CUPO_DET
--------------------------------------------------------

  CREATE TABLE "DB_COMERCIAL"."INFO_AGENDA_CUPO_DET"
   (	"ID_AGENDA_CUPO_DET" NUMBER,
	"AGENDA_CUPO_ID" NUMBER,
	"CUPOS_WEB" NUMBER,
	"CUPOS_MOVIL" NUMBER,
	"TOTAL_CUPOS" NUMBER,
	"HORA_DESDE" TIMESTAMP (6),
	"HORA_HASTA" TIMESTAMP (6),
	"OBSERVACION" VARCHAR2(1000 BYTE),
	"FE_CREACION" TIMESTAMP (6),
	"USR_CREACION" VARCHAR2(50 BYTE),
	"IP_CREACION" VARCHAR2(20 BYTE),
	"FE_MODIFICA" TIMESTAMP (6),
	"USR_MODIFICA" VARCHAR2(50 BYTE),
	"ESTADO_REGISTRO" VARCHAR2(20 BYTE) DEFAULT 'Activo'
   ) ;

   COMMENT ON COLUMN "DB_COMERCIAL"."INFO_AGENDA_CUPO_DET"."ID_AGENDA_CUPO_DET" IS 'Identificador del registro.';
   COMMENT ON COLUMN "DB_COMERCIAL"."INFO_AGENDA_CUPO_DET"."AGENDA_CUPO_ID" IS 'Id de la agenda a la cual pertenece el detalle en la tabla de cabecera.';
   COMMENT ON COLUMN "DB_COMERCIAL"."INFO_AGENDA_CUPO_DET"."CUPOS_WEB" IS 'Número de cupos disponibles para planificar vía web.';
   COMMENT ON COLUMN "DB_COMERCIAL"."INFO_AGENDA_CUPO_DET"."CUPOS_MOVIL" IS 'Número de cupos disponibles para planificar vía movil.';
   COMMENT ON COLUMN "DB_COMERCIAL"."INFO_AGENDA_CUPO_DET"."TOTAL_CUPOS" IS 'Total de cupos (cupos movil+cupos web).';
   COMMENT ON COLUMN "DB_COMERCIAL"."INFO_AGENDA_CUPO_DET"."HORA_DESDE" IS 'Hora y fecha inicio del intervalo de programación.';
   COMMENT ON COLUMN "DB_COMERCIAL"."INFO_AGENDA_CUPO_DET"."HORA_HASTA" IS 'Hora y fecha fin del intervalo de programación.';
   COMMENT ON COLUMN "DB_COMERCIAL"."INFO_AGENDA_CUPO_DET"."OBSERVACION" IS 'Observación para el registro.';
   COMMENT ON COLUMN "DB_COMERCIAL"."INFO_AGENDA_CUPO_DET"."FE_CREACION" IS 'Fecha de creación del registro.';
   COMMENT ON COLUMN "DB_COMERCIAL"."INFO_AGENDA_CUPO_DET"."USR_CREACION" IS 'Usuario de creación del registro.';
   COMMENT ON COLUMN "DB_COMERCIAL"."INFO_AGENDA_CUPO_DET"."IP_CREACION" IS 'Ip de creación del registro.';
   COMMENT ON COLUMN "DB_COMERCIAL"."INFO_AGENDA_CUPO_DET"."FE_MODIFICA" IS 'Fecha de modificación del registro.';
   COMMENT ON COLUMN "DB_COMERCIAL"."INFO_AGENDA_CUPO_DET"."USR_MODIFICA" IS 'Usuario de modificación del registro.';
   COMMENT ON COLUMN "DB_COMERCIAL"."INFO_AGENDA_CUPO_DET"."ESTADO_REGISTRO" IS 'Estado del registro.';
   COMMENT ON TABLE "DB_COMERCIAL"."INFO_AGENDA_CUPO_DET"  IS 'Tabla de detalle para el registro de configuraciones de agenda de planificación.';
--------------------------------------------------------
--  DDL for Index IDX_AGENDA_CUPO_ID
--------------------------------------------------------

  CREATE INDEX "DB_COMERCIAL"."IDX_AGENDA_CUPO_ID" ON "DB_COMERCIAL"."INFO_AGENDA_CUPO_DET" ("AGENDA_CUPO_ID") ;
--------------------------------------------------------
--  DDL for Index IDX_HORA_DESDE
--------------------------------------------------------

  CREATE INDEX "DB_COMERCIAL"."IDX_HORA_DESDE" ON "DB_COMERCIAL"."INFO_AGENDA_CUPO_DET" ("HORA_DESDE");
--------------------------------------------------------
--  DDL for Index IDX_HORA_HASTA
--------------------------------------------------------

  CREATE INDEX "DB_COMERCIAL"."IDX_HORA_HASTA" ON "DB_COMERCIAL"."INFO_AGENDA_CUPO_DET" ("HORA_HASTA") ;
--------------------------------------------------------
--  DDL for Index INFO_AGENDA_CUPO_DET_PK
--------------------------------------------------------

  CREATE UNIQUE INDEX "DB_COMERCIAL"."INFO_AGENDA_CUPO_DET_PK" ON "DB_COMERCIAL"."INFO_AGENDA_CUPO_DET" ("ID_AGENDA_CUPO_DET") ;
--------------------------------------------------------
--  DDL for Trigger AFTER_INFO_AGENDA_CUPO_DET
--------------------------------------------------------

  CREATE OR REPLACE TRIGGER "DB_COMERCIAL"."AFTER_INFO_AGENDA_CUPO_DET"
BEFORE INSERT OR UPDATE OF AGENDA_CUPO_ID,CUPOS_MOVIL,CUPOS_WEB,DESCRIPCION,ESTADO_REGISTRO,FE_CREACION,FE_MODIFICA,HORA_DESDE,
                          HORA_HASTA,ID_AGENDA_CUPO_DET,IP_CREACION,TOTAL_CUPOS,USR_CREACION,USR_MODIFICA
                          ON DB_COMERCIAL.INFO_AGENDA_CUPO_DET
FOR EACH ROW

    /**
    * Documentación para trigger AFTER_INFO_AGENDA_CUPO_CAB
    * Al momento de ingresar un nuevo registro o actualizar uno existente, se actualiza los campos fe_modifica y usr_modifica
    * @author Juan Romero <jromero@telconet.ec>
    * @version 1.0 06-06-2018
    */

BEGIN

    :NEW.FE_MODIFICA    :=SYSDATE;
    :NEW.USR_MODIFICA   :=USER;

EXCEPTION
    WHEN OTHERS THEN
        UTL_MAIL.SEND (sender     => 'notificaciones@telconet.ec',
                       recipients => 'jromero@telconet.ec;',
                       subject    => 'Error generado en el trigger DB_COMERCIAL.AFTER_INFO_AGENDA_CUPO_DET',
                       MESSAGE    => '<p>Ocurrio el siguiente error: ' || SUBSTR(SQLERRM,1,200) || ' - ' || SQLCODE ||' </p>',
                       mime_type => 'text/html; charset=UTF-8' );
END;
/
ALTER TRIGGER "DB_COMERCIAL"."AFTER_INFO_AGENDA_CUPO_DET" ENABLE;
--------------------------------------------------------
--  Constraints for Table INFO_AGENDA_CUPO_DET
--------------------------------------------------------

  ALTER TABLE "DB_COMERCIAL"."INFO_AGENDA_CUPO_DET" ADD CONSTRAINT "INFO_AGENDA_CUPO_DET_PK" PRIMARY KEY ("ID_AGENDA_CUPO_DET");
  ALTER TABLE "DB_COMERCIAL"."INFO_AGENDA_CUPO_DET" MODIFY ("ESTADO_REGISTRO" NOT NULL ENABLE);
  ALTER TABLE "DB_COMERCIAL"."INFO_AGENDA_CUPO_DET" MODIFY ("IP_CREACION" NOT NULL ENABLE);
  ALTER TABLE "DB_COMERCIAL"."INFO_AGENDA_CUPO_DET" MODIFY ("USR_CREACION" NOT NULL ENABLE);
  ALTER TABLE "DB_COMERCIAL"."INFO_AGENDA_CUPO_DET" MODIFY ("FE_CREACION" NOT NULL ENABLE);
  ALTER TABLE "DB_COMERCIAL"."INFO_AGENDA_CUPO_DET" MODIFY ("HORA_HASTA" NOT NULL ENABLE);
  ALTER TABLE "DB_COMERCIAL"."INFO_AGENDA_CUPO_DET" MODIFY ("HORA_DESDE" NOT NULL ENABLE);
  ALTER TABLE "DB_COMERCIAL"."INFO_AGENDA_CUPO_DET" MODIFY ("TOTAL_CUPOS" NOT NULL ENABLE);
  ALTER TABLE "DB_COMERCIAL"."INFO_AGENDA_CUPO_DET" MODIFY ("CUPOS_MOVIL" NOT NULL ENABLE);
  ALTER TABLE "DB_COMERCIAL"."INFO_AGENDA_CUPO_DET" MODIFY ("CUPOS_WEB" NOT NULL ENABLE);
  ALTER TABLE "DB_COMERCIAL"."INFO_AGENDA_CUPO_DET" MODIFY ("AGENDA_CUPO_ID" NOT NULL ENABLE);
  ALTER TABLE "DB_COMERCIAL"."INFO_AGENDA_CUPO_DET" MODIFY ("ID_AGENDA_CUPO_DET" NOT NULL ENABLE);


--------------------------------------------------------
--  DDL for Trigger AFTER_INFO_AGENDA_CUPO_CAB
--------------------------------------------------------

CREATE OR REPLACE TRIGGER "DB_COMERCIAL"."AFTER_INFO_AGENDA_CUPO_CAB"
BEFORE INSERT OR UPDATE OF ESTADO_REGISTRO,FECHA_PERIODO,FE_CREACION,FE_MODIFICA,ID_AGENDA_CUPOS,IP_CREACION,JURISDICCION_ID,
                           OBSERVACION,PLANTILLA_HORARIO_ID,TOTAL_CUPOS,USR_CREACION,USR_MODIFICA
                           ON DB_COMERCIAL.INFO_AGENDA_CUPO_CAB
FOR EACH ROW

    /**
    * Documentación para trigger AFTER_INFO_AGENDA_CUPO_CAB
    * Al momento de ingresar un nuevo registro o actualizar uno existente, se actualiza los campos fe_modifica y usr_modifica
    * @author Juan Romero <jromero@telconet.ec>
    * @version 1.0 06-06-2018
    */

BEGIN
    :NEW.FE_MODIFICA    :=SYSDATE;
    :NEW.USR_MODIFICA   :=USER;
EXCEPTION
    WHEN OTHERS THEN
        UTL_MAIL.SEND (sender     => 'notificaciones@telconet.ec',
                       recipients => 'jromero@telconet.ec;',
                       subject    => 'Error generado en el trigger AFTER_INFO_AGENDA_CUPO_CAB',
                       MESSAGE    => '<p>Ocurrio el siguiente error: ' || SUBSTR(SQLERRM,1,200) || ' - ' || SQLCODE ||' </p>',
                       mime_type => 'text/html; charset=UTF-8' );
END;
/
ALTER TRIGGER "DB_COMERCIAL"."AFTER_INFO_AGENDA_CUPO_CAB" ENABLE;

--------------------------------------------------------
--  DDL for Trigger AFTER_INFO_AGENDA_CUPO_DET
--------------------------------------------------------

  CREATE OR REPLACE TRIGGER "DB_COMERCIAL"."AFTER_INFO_AGENDA_CUPO_DET"
BEFORE INSERT OR UPDATE OF AGENDA_CUPO_ID,CUPOS_MOVIL,CUPOS_WEB,OBSERVACION,ESTADO_REGISTRO,FE_CREACION,FE_MODIFICA,HORA_DESDE,
                          HORA_HASTA,ID_AGENDA_CUPO_DET,IP_CREACION,TOTAL_CUPOS,USR_CREACION,USR_MODIFICA
                          ON DB_COMERCIAL.INFO_AGENDA_CUPO_DET
FOR EACH ROW

    /**
    * Documentación para trigger AFTER_INFO_AGENDA_CUPO_DET
    * Al momento de ingresar un nuevo registro o actualizar uno existente, se actualiza los campos fe_modifica y usr_modifica
    * @author Juan Romero <jromero@telconet.ec>
    * @version 1.0 06-06-2018
    */

BEGIN

    :NEW.FE_MODIFICA    :=SYSDATE;
    :NEW.USR_MODIFICA   :=USER;

EXCEPTION
    WHEN OTHERS THEN
        UTL_MAIL.SEND (sender     => 'notificaciones@telconet.ec',
                       recipients => 'jromero@telconet.ec;',
                       subject    => 'Error generado en el trigger DB_COMERCIAL.AFTER_INFO_AGENDA_CUPO_DET',
                       MESSAGE    => '<p>Ocurrio el siguiente error: ' || SUBSTR(SQLERRM,1,200) || ' - ' || SQLCODE ||' </p>',
                       mime_type => 'text/html; charset=UTF-8' );
END;
/
ALTER TRIGGER "DB_COMERCIAL"."AFTER_INFO_AGENDA_CUPO_DET" ENABLE;


--------------------------------------------------------
--  DDL for Sequence SEQ_INFO_AGENDA_CUPO_CAB
--------------------------------------------------------

   CREATE SEQUENCE  "DB_COMERCIAL"."SEQ_INFO_AGENDA_CUPO_CAB"  MINVALUE 1 MAXVALUE 9999999999999999999999999999 INCREMENT BY 1 START WITH 1 NOCACHE  NOORDER  NOCYCLE ;

   CREATE SEQUENCE  "DB_COMERCIAL"."SEQ_INFO_AGENDA_CUPO_DET"  MINVALUE 1 MAXVALUE 9999999999999999999999999999 INCREMENT BY 1 START WITH 1 NOCACHE  NOORDER  NOCYCLE ;

