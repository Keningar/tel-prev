-- Creación de la tabla DB_INFRAESTRUCTURA.INFO_ELEMENTO_INSTALACION
CREATE TABLE DB_INFRAESTRUCTURA.INFO_ELEMENTO_INSTALACION
(
    ID_ELEMENTO_INSTALACION     NUMBER  NOT NULL,
    PERSONA_EMPRESA_ROL_ID      NUMBER  NOT NULL,
    PUNTO_ID                    NUMBER  NOT NULL,
    TIPO_ELEMENTO_ID            NUMBER  NOT NULL,
    SERIE_ELEMENTO              VARCHAR2(100),
    ELEMENTO_ID                 NUMBER,
    UBICACION                   VARCHAR2(100),
    PROPIETARIO                 VARCHAR2(300),
    ESTADO                      VARCHAR2(20),
    USR_CREACION                VARCHAR2(20) NOT NULL,
    FE_CREACION                 TIMESTAMP    NOT NULL,
    USR_ULT_MOD                 VARCHAR2(20),
    FE_ULT_MOD                  TIMESTAMP,
    CONSTRAINT INFO_ELEMENTO_INSTALACION_PK PRIMARY KEY(ID_ELEMENTO_INSTALACION)
);

COMMENT ON COLUMN "DB_INFRAESTRUCTURA"."INFO_ELEMENTO_INSTALACION"."ID_ELEMENTO_INSTALACION"    IS 'Identificador del registro';
COMMENT ON COLUMN "DB_INFRAESTRUCTURA"."INFO_ELEMENTO_INSTALACION"."PERSONA_EMPRESA_ROL_ID"     IS 'El id_persona_rol del cliente';
COMMENT ON COLUMN "DB_INFRAESTRUCTURA"."INFO_ELEMENTO_INSTALACION"."PUNTO_ID"                   IS 'El id del punto del cliente';
COMMENT ON COLUMN "DB_INFRAESTRUCTURA"."INFO_ELEMENTO_INSTALACION"."TIPO_ELEMENTO_ID"           IS 'El tipo de elemento que se esta instalando';
COMMENT ON COLUMN "DB_INFRAESTRUCTURA"."INFO_ELEMENTO_INSTALACION"."SERIE_ELEMENTO"             IS 'Serie del elemento que se esta instalando';
COMMENT ON COLUMN "DB_INFRAESTRUCTURA"."INFO_ELEMENTO_INSTALACION"."ELEMENTO_ID"                IS 'El id_elemento que se esta instalando, inicialmente sera NULL';
COMMENT ON COLUMN "DB_INFRAESTRUCTURA"."INFO_ELEMENTO_INSTALACION"."UBICACION"                  IS 'Ubicación donde queda el elemento: NODO, CLIENTE';
COMMENT ON COLUMN "DB_INFRAESTRUCTURA"."INFO_ELEMENTO_INSTALACION"."PROPIETARIO"                IS 'Propietario del elemento: TELCONET, CLIENTE';
COMMENT ON COLUMN "DB_INFRAESTRUCTURA"."INFO_ELEMENTO_INSTALACION"."ESTADO"                     IS 'Estado del registro';
COMMENT ON COLUMN "DB_INFRAESTRUCTURA"."INFO_ELEMENTO_INSTALACION"."USR_CREACION"               IS 'Login del usuario que genera el registro';
COMMENT ON COLUMN "DB_INFRAESTRUCTURA"."INFO_ELEMENTO_INSTALACION"."FE_CREACION"                IS 'Fecha de creación del registro';
COMMENT ON COLUMN "DB_INFRAESTRUCTURA"."INFO_ELEMENTO_INSTALACION"."USR_ULT_MOD"                IS 'Login del usuario que modifica el registro';
COMMENT ON COLUMN "DB_INFRAESTRUCTURA"."INFO_ELEMENTO_INSTALACION"."FE_ULT_MOD"                 IS 'Fecha de modificación del registro';

--------------------------------------------------------
--  DDL for Index IDX_PERSONA_EMPRESA_ROL_ID
--------------------------------------------------------
  CREATE INDEX "DB_INFRAESTRUCTURA"."IDX_PERSONA_EMPRESA_ROL_ID" ON "DB_INFRAESTRUCTURA"."INFO_ELEMENTO_INSTALACION" ("PERSONA_EMPRESA_ROL_ID") ;

--------------------------------------------------------
--  DDL for Index IDX_USR_CREACION
--------------------------------------------------------
  CREATE INDEX "DB_INFRAESTRUCTURA"."IDX_USR_CREACION" ON "DB_INFRAESTRUCTURA"."INFO_ELEMENTO_INSTALACION" ("USR_CREACION");

--------------------------------------------------------
--  DDL for Index IDX_FE_CREACION
--------------------------------------------------------

  CREATE INDEX "DB_INFRAESTRUCTURA"."IDX_FE_CREACION" ON "DB_INFRAESTRUCTURA"."INFO_ELEMENTO_INSTALACION" ("FE_CREACION") ;

--------------------------------------------------------
--  DDL for Index IDX_SERIE_ELEMENTO
--------------------------------------------------------

  CREATE INDEX "DB_INFRAESTRUCTURA"."IDX_SERIE_ELEMENTO" ON "DB_INFRAESTRUCTURA"."INFO_ELEMENTO_INSTALACION" ("SERIE_ELEMENTO") ;

--------------------------------------------------------
--  DDL for sequence SEQ_INFO_ELEMENTO_INSTALACION
--------------------------------------------------------
  CREATE SEQUENCE "DB_INFRAESTRUCTURA"."SEQ_INFO_ELEMENTO_INSTALACION" INCREMENT BY 1 MAXVALUE 9999999999999999999999999999 MINVALUE 1 CACHE 20;
