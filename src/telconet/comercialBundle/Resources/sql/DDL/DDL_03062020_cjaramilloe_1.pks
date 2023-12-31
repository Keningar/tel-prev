/**
 *
 * Creación de la tabla INFO_PERSONA_REPRESENTANTE cuyo objetivo es mantener
 * una relación entre empresa jurídica y representante legal para contrato
 * digital TM Comercial
 * 
 *
 * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
 * @version 1.0 02-06-2020
 * 
 **/
 
CREATE TABLE "DB_COMERCIAL"."INFO_PERSONA_REPRESENTANTE" 
(   
    ID_PERSONA_REPRESENTANTE     NUMBER NOT NULL ENABLE,
    PERSONA_EMPRESA_ROL_ID       NUMBER NOT NULL ENABLE,
    REPRESENTANTE_EMPRESA_ROL_ID NUMBER NOT NULL ENABLE,
    RAZON_COMERCIAL              VARCHAR2(4000),
    FE_REGISTRO_MERCANTIL        TIMESTAMP(6) NOT NULL ENABLE,
    FE_EXPIRACION_NOMBRAMIENTO   TIMESTAMP(6) NOT NULL ENABLE,
    ESTADO                       VARCHAR2(16 BYTE) NOT NULL ENABLE,
    FE_CREACION                  TIMESTAMP(6) NOT NULL ENABLE,
    USR_CREACION                 VARCHAR2(50 BYTE) NOT NULL ENABLE,
    IP_CREACION                  VARCHAR2(15 BYTE),
    USR_ULT_MOD                  VARCHAR2(50 BYTE),
    FE_ULT_MOD                   TIMESTAMP(6),
    IP_ULT_MOD                   VARCHAR2(15 BYTE),
    OBSERVACION                  VARCHAR2(4000),  
    CONSTRAINT "INFO_PERSONA_REPRESENTANTE_PK" PRIMARY KEY (ID_PERSONA_REPRESENTANTE),
    CONSTRAINT "INFO_PERSONA_REPRESENTANTE_FK1" FOREIGN KEY (PERSONA_EMPRESA_ROL_ID) REFERENCES DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL (ID_PERSONA_ROL) DISABLE,
    CONSTRAINT "INFO_PERSONA_REPRESENTANTE_FK2" FOREIGN KEY (REPRESENTANTE_EMPRESA_ROL_ID) REFERENCES DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL (ID_PERSONA_ROL) DISABLE
);

ALTER TABLE "DB_COMERCIAL"."INFO_PERSONA_REPRESENTANTE" MOVE TABLESPACE "DB_TELCONET";

COMMENT ON COLUMN DB_COMERCIAL."INFO_PERSONA_REPRESENTANTE"."ID_PERSONA_REPRESENTANTE" IS 'ID primaria';
 
COMMENT ON COLUMN DB_COMERCIAL."INFO_PERSONA_REPRESENTANTE"."PERSONA_EMPRESA_ROL_ID" IS 'ID foránea de la persona';

COMMENT ON COLUMN DB_COMERCIAL."INFO_PERSONA_REPRESENTANTE"."REPRESENTANTE_EMPRESA_ROL_ID" IS 'ID foránea del representante legal';

COMMENT ON COLUMN DB_COMERCIAL."INFO_PERSONA_REPRESENTANTE"."RAZON_COMERCIAL" IS 'Razón comercial de la persona jurídica'; 

COMMENT ON COLUMN DB_COMERCIAL."INFO_PERSONA_REPRESENTANTE"."FE_EXPIRACION_NOMBRAMIENTO" IS 'Fecha de expiración del nombramiento del representante legal';

COMMENT ON COLUMN DB_COMERCIAL."INFO_PERSONA_REPRESENTANTE"."FE_REGISTRO_MERCANTIL" IS 'Fecha del registro mercantil de la persona jurídica';

COMMENT ON COLUMN DB_COMERCIAL."INFO_PERSONA_REPRESENTANTE"."ESTADO" IS 'Estado actual del registro';

COMMENT ON COLUMN DB_COMERCIAL."INFO_PERSONA_REPRESENTANTE"."FE_CREACION" IS 'Fecha de creación del registro';

COMMENT ON COLUMN DB_COMERCIAL."INFO_PERSONA_REPRESENTANTE"."USR_CREACION" IS 'Usuario creador del registro';

COMMENT ON COLUMN DB_COMERCIAL."INFO_PERSONA_REPRESENTANTE"."IP_CREACION" IS 'IP creador del registro';

COMMENT ON COLUMN DB_COMERCIAL."INFO_PERSONA_REPRESENTANTE"."FE_ULT_MOD" IS 'Fecha de última modificación del registro';

COMMENT ON COLUMN DB_COMERCIAL."INFO_PERSONA_REPRESENTANTE"."USR_ULT_MOD" IS 'Usuario de última modificación del registro';

COMMENT ON COLUMN DB_COMERCIAL."INFO_PERSONA_REPRESENTANTE"."IP_ULT_MOD" IS 'IP de última modificación del registro'; 

COMMENT ON COLUMN DB_COMERCIAL."INFO_PERSONA_REPRESENTANTE"."OBSERVACION" IS 'Observación del registro'; 

COMMENT ON TABLE DB_COMERCIAL."INFO_PERSONA_REPRESENTANTE"  IS 'TABLA EN LA QUE SE REGISTRA LA RELACIÓN ENTRE EMPRESA 
JURÍDICA Y REPRESENTANTE LEGAL PARA CONTRATO DIGITAL TM COMERCIAL';

CREATE INDEX "DB_COMERCIAL"."IDX_INFO_PER_REP_PERSONA" ON "DB_COMERCIAL"."INFO_PERSONA_REPRESENTANTE" ("PERSONA_EMPRESA_ROL_ID");

CREATE INDEX "DB_COMERCIAL"."IDX_INFO_PER_REP_REPRESENTANTE" ON "DB_COMERCIAL"."INFO_PERSONA_REPRESENTANTE" 
("REPRESENTANTE_EMPRESA_ROL_ID");

CREATE INDEX "DB_COMERCIAL"."IDX_INFO_PER_REP_FE_CREACION" ON "DB_COMERCIAL"."INFO_PERSONA_REPRESENTANTE" ("FE_CREACION");

CREATE INDEX "DB_COMERCIAL"."IDX_INFO_PER_REP_ESTADO" ON "DB_COMERCIAL"."INFO_PERSONA_REPRESENTANTE" ("ESTADO");

CREATE SEQUENCE  "DB_COMERCIAL"."SEQ_INFO_PERSONA_REPRESENTANTE"  MINVALUE 1 MAXVALUE 9999999999999999999999999999 INCREMENT BY 1 START WITH 1 NOCACHE  NOORDER  NOCYCLE;