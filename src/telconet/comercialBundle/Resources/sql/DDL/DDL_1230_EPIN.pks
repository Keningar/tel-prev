ALTER TABLE DB_COMERCIAL.INFO_COTIZACION_CAB ADD (PUNTO_ID NUMBER, EMPRESA_COD VARCHAR2(3), ARCHIVO_DIGITAL VARCHAR2(4000));

ALTER TABLE DB_COMERCIAL.INFO_COTIZACION_DET ADD (PRODUCTO_ID NUMBER, PORCENTAJE_IVA NUMBER(5,2));

CREATE SEQUENCE "DB_COMERCIAL"."SEQ_INFO_COTIZACION_CAB" INCREMENT BY 1 MAXVALUE 9999999999999999999999999999 MINVALUE 1 CACHE 20;

CREATE SEQUENCE "DB_COMERCIAL"."SEQ_INFO_COTIZACION_DET" INCREMENT BY 1 MAXVALUE 9999999999999999999999999999 MINVALUE 1 CACHE 20;

--Creación de la tabla DB_COMERCIAL.CATALOGOS
CREATE TABLE DB_COMERCIAL.ADMI_CATALOGOS
(
  ID_CATALOGOS  NUMBER       NOT NULL,
  COD_EMPRESA     VARCHAR2(3),
  TIPO            VARCHAR2(50) NOT NULL,
  JSON_CATALOGO   CLOB         NOT NULL,
  HASH_CATALOGO   VARCHAR2(250),
  FE_ULT_MOD      DATE,
  CONSTRAINT ADMI_CATALOGOS_PK PRIMARY KEY(ID_CATALOGOS)
);

COMMENT ON COLUMN "DB_COMERCIAL"."ADMI_CATALOGOS"."ID_CATALOGOS"  IS 'Identificador del registro.';
COMMENT ON COLUMN "DB_COMERCIAL"."ADMI_CATALOGOS"."COD_EMPRESA"   IS 'Empresa a la que pertenece el catálogo, si esta en blanco es un catalogo general';
COMMENT ON COLUMN "DB_COMERCIAL"."ADMI_CATALOGOS"."TIPO"          IS 'Tipo de catálogo';
COMMENT ON COLUMN "DB_COMERCIAL"."ADMI_CATALOGOS"."JSON_CATALOGO" IS 'JSON almacenado con la información del catálogo';
COMMENT ON COLUMN "DB_COMERCIAL"."ADMI_CATALOGOS"."HASH_CATALOGO" IS 'HASH del json almacenado.';
COMMENT ON COLUMN "DB_COMERCIAL"."ADMI_CATALOGOS"."FE_ULT_MOD"    IS 'Fecha de modificación del registro';

--------------------------------------------------------
--  DDL for sequence SEQ_INFO_LOG
--------------------------------------------------------
  CREATE SEQUENCE "DB_COMERCIAL"."SEQ_ADMI_CATALOGOS" INCREMENT BY 1 MAXVALUE 9999999999999999999999999999 MINVALUE 1 CACHE 20;

-- Creación de la tabla DB_COMERCIAL.INFO_LOG
CREATE TABLE DB_GENERAL.INFO_LOG
(
   ID_LOG            NUMBER       NOT NULL,
   EMPRESA_COD       VARCHAR2(3)  NOT NULL,
   TIPO_LOG          VARCHAR2(2),
   ORIGEN_LOG        VARCHAR2(100),
   LATITUD           VARCHAR2(50),
   LONGITUD          VARCHAR2(50),
   APLICACION        VARCHAR2(100),
   CLASE             VARCHAR2(300),
   METODO            VARCHAR2(50),
   ACCION            VARCHAR2(100),
   MENSAJE           VARCHAR2(200),
   ESTADO            VARCHAR2(20),
   DESCRIPCION       CLOB,
   IMEI              VARCHAR2(100),
   MODELO            VARCHAR2(100),
   VERSION_APK       VARCHAR2(30),
   VERSION_SO        VARCHAR2(20),
   TIPO_CONEXION     VARCHAR2(10),
   INTENSIDAD_SENAL  VARCHAR2(10),
   PARAMETRO_ENTRADA CLOB,
   USR_CREACION      VARCHAR2(20) NOT NULL,
   FE_CREACION       TIMESTAMP    NOT NULL,
   USR_ULT_MOD       VARCHAR2(20),
   FE_ULT_MOD        TIMESTAMP,
   CONSTRAINT INFO_LOG_PK PRIMARY KEY(ID_LOG)
);

COMMENT ON COLUMN "DB_GENERAL"."INFO_LOG"."ID_LOG"            IS 'Identificador del registro.';
COMMENT ON COLUMN "DB_GENERAL"."INFO_LOG"."EMPRESA_COD"       IS 'Código de la empresa.';
COMMENT ON COLUMN "DB_GENERAL"."INFO_LOG"."TIPO_LOG"          IS 'Tipo de logueo 0) TRACE, 1)ERROR.';
COMMENT ON COLUMN "DB_GENERAL"."INFO_LOG"."ORIGEN_LOG"        IS 'De donde se origina el log, ej: Telcos, TM-Comercial';
COMMENT ON COLUMN "DB_GENERAL"."INFO_LOG"."LATITUD"           IS 'Latitud GPS de la ubicación del equipo';
COMMENT ON COLUMN "DB_GENERAL"."INFO_LOG"."LONGITUD"          IS 'Longitud GPS de la ubicación del equipo';
COMMENT ON COLUMN "DB_GENERAL"."INFO_LOG"."APLICACION"        IS 'Nombre del package de la aplicacion que genera el log';
COMMENT ON COLUMN "DB_GENERAL"."INFO_LOG"."CLASE"             IS 'Nombre de la clase';
COMMENT ON COLUMN "DB_GENERAL"."INFO_LOG"."METODO"            IS 'Nombre del método';
COMMENT ON COLUMN "DB_GENERAL"."INFO_LOG"."ACCION"            IS 'Acción que realiza el método';
COMMENT ON COLUMN "DB_GENERAL"."INFO_LOG"."MENSAJE"           IS 'Mensaje que se muestra al usuario';
COMMENT ON COLUMN "DB_GENERAL"."INFO_LOG"."ESTADO"            IS 'Exitoso o Fallido';
COMMENT ON COLUMN "DB_GENERAL"."INFO_LOG"."DESCRIPCION"       IS 'En caso de ser fallida se debe indicar una descripción de la causa, por excepción se almacena el stacktrace';
COMMENT ON COLUMN "DB_GENERAL"."INFO_LOG"."IMEI"              IS 'Imei del equipo';
COMMENT ON COLUMN "DB_GENERAL"."INFO_LOG"."MODELO"            IS 'Modelo del equipo';
COMMENT ON COLUMN "DB_GENERAL"."INFO_LOG"."VERSION_APK"       IS 'Versión de la aplicación';
COMMENT ON COLUMN "DB_GENERAL"."INFO_LOG"."VERSION_SO"        IS 'Versión del Sistema Operativo';
COMMENT ON COLUMN "DB_GENERAL"."INFO_LOG"."TIPO_CONEXION"     IS 'Tipo de conexión Wifi, EDGE, 3G, LTE, etc.';
COMMENT ON COLUMN "DB_GENERAL"."INFO_LOG"."INTENSIDAD_SENAL"  IS 'Porcentaje de señal que tiene el equipo';
COMMENT ON COLUMN "DB_GENERAL"."INFO_LOG"."PARAMETRO_ENTRADA" IS 'Parametros de entrada y sus valores para poder replicar escenario';
COMMENT ON COLUMN "DB_GENERAL"."INFO_LOG"."USR_CREACION"      IS 'Login del usuario que genera el log';
COMMENT ON COLUMN "DB_GENERAL"."INFO_LOG"."FE_CREACION"       IS 'Fecha de creación del registro';
COMMENT ON COLUMN "DB_GENERAL"."INFO_LOG"."USR_ULT_MOD"       IS 'Login del usuario que modifica el log';
COMMENT ON COLUMN "DB_GENERAL"."INFO_LOG"."FE_ULT_MOD"        IS 'Fecha de modificación del registro';

--------------------------------------------------------
--  DDL for Index IDX_LOG_EMPRESA_COD
--------------------------------------------------------
  CREATE INDEX "DB_GENERAL"."IDX_LOG_EMPRESA_COD" ON "DB_GENERAL"."INFO_LOG" ("EMPRESA_COD") ;

--------------------------------------------------------
--  DDL for Index IDX_USR_CREACION
--------------------------------------------------------
  CREATE INDEX "DB_GENERAL"."IDX_USR_CREACION" ON "DB_GENERAL"."INFO_LOG" ("USR_CREACION");

--------------------------------------------------------
--  DDL for Index IDX_FE_CREACION
--------------------------------------------------------

  CREATE INDEX "DB_GENERAL"."IDX_FE_CREACION" ON "DB_GENERAL"."INFO_LOG" ("FE_CREACION") ;

--------------------------------------------------------
--  DDL for Index IDX_APLICACION
--------------------------------------------------------

  CREATE INDEX "DB_GENERAL"."IDX_APLICACION" ON "DB_GENERAL"."INFO_LOG" ("APLICACION") ;

--------------------------------------------------------
--  DDL for sequence SEQ_INFO_LOG
--------------------------------------------------------
  CREATE SEQUENCE "DB_GENERAL"."SEQ_INFO_LOG" INCREMENT BY 1 MAXVALUE 9999999999999999999999999999 MINVALUE 1 CACHE 20;
/
