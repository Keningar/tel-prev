 /**
 * @author Edgar Holguin <eholguin@telconet.ec>
 * @version 1.0 Script para creación de tabla "DB_FINANCIERO"."ADMI_FORMATO_PAGO_AUT_CARACT"
 * @since 21-04-2022
 */
      
    CREATE TABLE "DB_FINANCIERO"."ADMI_FORMATO_PAGO_AUT_CARACT" 
      (  
        "ID_FORMATO_PAGO_AUT_CARACT" NUMBER NOT NULL,
        "FORMATO_PAGO_AUTOMATICO_ID" NUMBER NOT NULL, 
        "EMPRESA_COD" VARCHAR2(15) NOT NULL, 
        "CARACTERISTICA_ID" NUMBER NOT NULL,
        "VALOR" VARCHAR2(500) NOT NULL,
        "OBSERVACION" VARCHAR2(4000) DEFAULT NULL,
        "ESTADO" VARCHAR2(15) NOT NULL,
        "FE_CREACION" TIMESTAMP (6) NOT NULL,
        "USR_CREACION" VARCHAR2(15) NOT NULL,
        "IP_CREACION"  VARCHAR2(15) DEFAULT NULL,
        "FE_ULT_MOD" TIMESTAMP (6) DEFAULT NULL,
        "USR_ULT_MOD" VARCHAR2(15) DEFAULT NULL,
        "IP_ULT_MOD"  VARCHAR2(15) DEFAULT NULL 	
      ) ;


   COMMENT ON COLUMN "DB_FINANCIERO"."ADMI_FORMATO_PAGO_AUT_CARACT"."ID_FORMATO_PAGO_AUT_CARACT" IS 'SECUENCIAL DE LA TABLA';
   COMMENT ON COLUMN "DB_FINANCIERO"."ADMI_FORMATO_PAGO_AUT_CARACT"."FORMATO_PAGO_AUTOMATICO_ID" IS 'ID REFERENCIA DE LA TABLA ADMI_FORMATO_PAGO_AUTOMATICO';
   COMMENT ON COLUMN "DB_FINANCIERO"."ADMI_FORMATO_PAGO_AUT_CARACT"."CARACTERISTICA_ID" IS 'ID REFERENCIA DE LA TABLA ADMI_CARACTERISTICA';
   COMMENT ON COLUMN "DB_FINANCIERO"."ADMI_FORMATO_PAGO_AUT_CARACT"."EMPRESA_COD" IS 'ID REFERENCIA DE LA EMPRESA ';   
   COMMENT ON COLUMN "DB_FINANCIERO"."ADMI_FORMATO_PAGO_AUT_CARACT"."VALOR" IS 'VALOR DE LA CARACTERISTICA,POSICION DE COLUMNA A LEER';   
   COMMENT ON COLUMN "DB_FINANCIERO"."ADMI_FORMATO_PAGO_AUT_CARACT"."OBSERVACION" IS 'OBSERVACION DE LA CARACTERISTICA';
   COMMENT ON COLUMN "DB_FINANCIERO"."ADMI_FORMATO_PAGO_AUT_CARACT"."ESTADO" IS 'ESTADO DEL REGISTRO';
   COMMENT ON COLUMN "DB_FINANCIERO"."ADMI_FORMATO_PAGO_AUT_CARACT"."FE_CREACION" IS 'FECHA DE CREACION';
   COMMENT ON COLUMN "DB_FINANCIERO"."ADMI_FORMATO_PAGO_AUT_CARACT"."USR_CREACION" IS 'USUARIO DE CREACION';
   COMMENT ON COLUMN "DB_FINANCIERO"."ADMI_FORMATO_PAGO_AUT_CARACT"."IP_CREACION" IS 'IP DE CREACION';
   COMMENT ON COLUMN "DB_FINANCIERO"."ADMI_FORMATO_PAGO_AUT_CARACT"."FE_ULT_MOD" IS 'FECHA DE ULTIMA MODIFICACION';
   COMMENT ON COLUMN "DB_FINANCIERO"."ADMI_FORMATO_PAGO_AUT_CARACT"."USR_ULT_MOD" IS 'USUARIO DE ULTIMA MODIFICACION';
   COMMENT ON COLUMN "DB_FINANCIERO"."ADMI_FORMATO_PAGO_AUT_CARACT"."IP_ULT_MOD" IS 'IP ULTIMA MODIFICACION';
   
--------------------------------------------------------
--  Constraints for Table ADMI_FORMATO_PAGO_AUT_CARACT_
--------------------------------------------------------

ALTER TABLE "DB_FINANCIERO"."ADMI_FORMATO_PAGO_AUT_CARACT" ADD CONSTRAINT "ADMI_FORM_PAG_AUT_CARACT_PK" PRIMARY KEY ("ID_FORMATO_PAGO_AUT_CARACT");


--------------------------------------------------------
--  DDL for Sequence SEQ_ADMI_FORMATO_PAGO_AUT_CARACT
--------------------------------------------------------

CREATE  SEQUENCE "DB_FINANCIERO"."SEQ_ADMI_FORM_PAG_AUT_CARACT"  
    MINVALUE 1 
    MAXVALUE 9999999999999999999999999999 
    INCREMENT BY 1 
    START WITH 1
    NOCACHE; 

CREATE INDEX DB_FINANCIERO.ADMI_FORM_PAGO_AUT_CARACT_IDX1 ON DB_FINANCIERO.ADMI_FORMATO_PAGO_AUT_CARACT (CARACTERISTICA_ID);
CREATE INDEX DB_FINANCIERO.ADMI_FORM_PAGO_AUT_CARACT_IDX2 ON DB_FINANCIERO.ADMI_FORMATO_PAGO_AUT_CARACT (FORMATO_PAGO_AUTOMATICO_ID);


CREATE TABLE "DB_FINANCIERO"."INFO_PAGO_AUTOMATICO_CARACT" 
  (  
    "ID_PAGO_AUTOMATICO_CARACT" NUMBER NOT NULL,
    "DETALLE_PAGO_AUTOMATICO_ID" NUMBER NOT NULL,
    "CARACTERISTICA_ID" NUMBER NOT NULL, 
    "VALOR" VARCHAR2(500) NOT NULL,
    "OBSERVACION" VARCHAR2(4000) DEFAULT NULL,
    "ESTADO" VARCHAR2(15) NOT NULL,
    "FE_CREACION" TIMESTAMP (6) NOT NULL,
    "USR_CREACION" VARCHAR2(15) NOT NULL,
    "IP_CREACION"  VARCHAR2(15) DEFAULT NULL,
    "FE_ULT_MOD" TIMESTAMP (6) DEFAULT NULL,
    "USR_ULT_MOD" VARCHAR2(15) DEFAULT NULL,
    "IP_ULT_MOD"  VARCHAR2(15) DEFAULT NULL 	
  ) ;

   COMMENT ON COLUMN "DB_FINANCIERO"."INFO_PAGO_AUTOMATICO_CARACT"."ID_PAGO_AUTOMATICO_CARACT" IS 'SECUENCIAL DE LA TABLA';
   COMMENT ON COLUMN "DB_FINANCIERO"."INFO_PAGO_AUTOMATICO_CARACT"."DETALLE_PAGO_AUTOMATICO_ID" IS 'ID REFERENCIA DE LA TABLA INFO_PAGO_AUTOMATICO_DET';
   COMMENT ON COLUMN "DB_FINANCIERO"."ADMI_FORMATO_PAGO_AUT_CARACT"."CARACTERISTICA_ID" IS 'ID REFERENCIA DE LA TABLA ADMI_CARACTERISTICA';
   COMMENT ON COLUMN "DB_FINANCIERO"."INFO_PAGO_AUTOMATICO_CARACT"."VALOR" IS 'VALOR DE LA CARACTERISTICA';   
   COMMENT ON COLUMN "DB_FINANCIERO"."INFO_PAGO_AUTOMATICO_CARACT"."OBSERVACION" IS 'OBSERVACION';
   COMMENT ON COLUMN "DB_FINANCIERO"."INFO_PAGO_AUTOMATICO_CARACT"."ESTADO" IS 'ESTADO DEL REGISTRO';
   COMMENT ON COLUMN "DB_FINANCIERO"."INFO_PAGO_AUTOMATICO_CARACT"."FE_CREACION" IS 'FECHA DE CREACION';
   COMMENT ON COLUMN "DB_FINANCIERO"."INFO_PAGO_AUTOMATICO_CARACT"."USR_CREACION" IS 'USUARIO DE CREACION';
   COMMENT ON COLUMN "DB_FINANCIERO"."INFO_PAGO_AUTOMATICO_CARACT"."IP_CREACION" IS 'IP DE CREACION';
   COMMENT ON COLUMN "DB_FINANCIERO"."INFO_PAGO_AUTOMATICO_CARACT"."FE_ULT_MOD" IS 'FECHA DE ULTIMA MODIFICACION';
   COMMENT ON COLUMN "DB_FINANCIERO"."INFO_PAGO_AUTOMATICO_CARACT"."USR_ULT_MOD" IS 'USUARIO DE ULTIMA MODIFICACION';
   COMMENT ON COLUMN "DB_FINANCIERO"."INFO_PAGO_AUTOMATICO_CARACT"."IP_ULT_MOD" IS 'IP ULTIMA MODIFICACION';

--------------------------------------------------------
--  Constraints for Table INFO_PAGO_AUTOMATICO_CARACT
--------------------------------------------------------

ALTER TABLE "DB_FINANCIERO"."INFO_PAGO_AUTOMATICO_CARACT" ADD CONSTRAINT "INFO_PAGO_AUT_CARACT_PK" PRIMARY KEY ("ID_PAGO_AUTOMATICO_CARACT");


--------------------------------------------------------
--  DDL for Sequence SEQ_ADMI_FORMATO_PAGO_AUT_CARACT
--------------------------------------------------------

CREATE  SEQUENCE "DB_FINANCIERO"."SEQ_INFO_PAG_AUTOMATICO_CARACT"  
    MINVALUE 1 
    MAXVALUE 9999999999999999999999999999 
    INCREMENT BY 1 
    START WITH 1
    NOCACHE; 

CREATE INDEX DB_FINANCIERO.INFO_PAGO_AUT_CARACT_IDX1 ON DB_FINANCIERO.INFO_PAGO_AUTOMATICO_CARACT (DETALLE_PAGO_AUTOMATICO_ID);
CREATE INDEX DB_FINANCIERO.INFO_PAGO_AUT_CARACT_IDX2 ON DB_FINANCIERO.INFO_PAGO_AUTOMATICO_CARACT (DETALLE_PAGO_AUTOMATICO_ID,CARACTERISTICA_ID);
CREATE INDEX DB_FINANCIERO.INFO_PAGO_AUT_CARACT_IDX3 ON DB_FINANCIERO.INFO_PAGO_AUTOMATICO_CARACT (CARACTERISTICA_ID);

ALTER TABLE DB_COMERCIAL.INFO_CONTRATO_DATO_ADICIONAL ADD (NOTIFICA_PAGO VARCHAR2(1) DEFAULT 'S');

/  
