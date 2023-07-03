
COMMENT ON COLUMN DB_INFRAESTRUCTURA.INFO_PROCESO_MASIVO_CAB.SOLICITUD_ID
 IS 'ID de referencia con la INFO_DETALLE_SOLICITUD, o  id de referencia de ADMI_CICLO, o ID de referencia de ADMI_MOTIVO
';

COMMENT ON COLUMN DB_INFRAESTRUCTURA.INFO_PROCESO_MASIVO_DET.SOLICITUD_ID IS 'ID de referencia con la INFO_DETALLE_SOLICITUD, 
o ID de referencia con ADMI_GRUPO_PROMOCION';

CREATE SEQUENCE  DB_COMERCIAL.SEQ_ADMI_REGLA_SECUENCIA INCREMENT BY 1 START WITH 1 MAXVALUE 99999999999999999 MINVALUE 1 NOCACHE;

CREATE TABLE DB_COMERCIAL.ADMI_GRUPO_PROMOCION
(
  ID_GRUPO_PROMOCION     NUMBER(11,0) NOT NULL ,
  NOMBRE_GRUPO           VARCHAR2(50 BYTE) NOT NULL,        
  FE_INICIO_VIGENCIA     DATE NOT NULL, 
  FE_FIN_VIGENCIA        DATE NOT NULL,  
  FE_CREACION            TIMESTAMP (6) NOT NULL, 
  USR_CREACION           VARCHAR2(15 BYTE) NOT NULL, 
  IP_CREACION            VARCHAR2(16 BYTE) NOT NULL, 
  FE_ULT_MOD             TIMESTAMP (6), 
  USR_ULT_MOD            VARCHAR2(15 BYTE), 
  IP_ULT_MOD             VARCHAR2(16 BYTE), 
  EMPRESA_COD            VARCHAR2(2 BYTE) NOT NULL, 
  GRUPO_PROMOCION_ID     NUMBER(11,0),       
  ESTADO                 VARCHAR2(15 BYTE) NOT NULL, 		 
  
  CONSTRAINT ADMI_GRUPO_PROMOCION_PK PRIMARY KEY 
  (
    ID_GRUPO_PROMOCION 
  )
  ENABLE 
);

 COMMENT ON COLUMN DB_COMERCIAL.ADMI_GRUPO_PROMOCION.ID_GRUPO_PROMOCION IS 'HACE REFERENCIA AL SECUENCIAL DE LA TABLA';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_GRUPO_PROMOCION.NOMBRE_GRUPO IS 'CAMPO HACE REFERENCIA AL NOMBRE DE GRUPO DE LA PROMOCION';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_GRUPO_PROMOCION.FE_INICIO_VIGENCIA IS 'HACE REFERENCIA A LA FECHA INICIO DE VIGENCIA DEL GRUPO DE LA PROMOCION';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_GRUPO_PROMOCION.FE_FIN_VIGENCIA IS 'HACE REFERENCIA A LA FECHA FIN DE VIGENCIA DEL GRUPO DE LA PROMOCION';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_GRUPO_PROMOCION.FE_CREACION IS 'HACE REFERENCIA A LA FECHA Y HORA DE CREACION DEL GRUPO DE LA PROMOCION';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_GRUPO_PROMOCION.USR_CREACION IS 'HACE REFERENCIA AL USUARIO DE CREACION DEL GRUPO DE LA PROMOCION';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_GRUPO_PROMOCION.IP_CREACION IS 'HACE REFERENCIA IP DE CREACION DEL REGISTRO';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_GRUPO_PROMOCION.FE_ULT_MOD IS 'HACE REFERENCIA A LA FECHA Y HORA DE ULTIMA MODIFICACION 
DEL GRUPO DE LA PROMOCION';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_GRUPO_PROMOCION.USR_ULT_MOD IS 'HACE REFERENCIA AL USUARIO DE ULTIMA MODIFICACION DEL GRUPO DE LA PROMOCION';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_GRUPO_PROMOCION.IP_ULT_MOD IS 'HACE REFERENCIA IP DE ULTIMA MODIFICACION DEL GRUPO DE LA PROMOCION';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_GRUPO_PROMOCION.EMPRESA_COD IS 'CAMPO QUE HACE REFERENCIA AL CODIGO DE EMPRESA: 18: MEGADATOS, 10: TELCONET';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_GRUPO_PROMOCION.GRUPO_PROMOCION_ID IS 'CAMPO QUE HACE REFERENCIA AL ID DE LA TABLA ADMI_GRUPO_PROMOCION 
CUANDO ESTA SE ORIGINA POR UN PROCESO DE CLONACION DE PROMOCION SE ALMACENARA EL ID DEL GRUPO DE SU PROMOCION ORIGEN';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_GRUPO_PROMOCION.ESTADO IS 'HACE REFERENCIA AL ESTADO DEL GRUPO DE LA PROMOCION: Pendiente, Activo, 
Clonado, Inactivo, Baja.';
 
 CREATE SEQUENCE  DB_COMERCIAL.SEQ_ADMI_GRUPO_PROMOCION INCREMENT BY 1 START WITH 1 MAXVALUE 99999999999999999 MINVALUE 1 NOCACHE;

ALTER TABLE DB_COMERCIAL.ADMI_GRUPO_PROMOCION
ADD CONSTRAINT ADMI_GRUPO_PROMOCION_FK1 FOREIGN KEY
(
  EMPRESA_COD 
)
REFERENCES DB_COMERCIAL.INFO_EMPRESA_GRUPO
(
  COD_EMPRESA
)
ENABLE;

CREATE INDEX DB_COMERCIAL.ADMI_GRUPO_PROMOCION_INDEX1 ON DB_COMERCIAL.ADMI_GRUPO_PROMOCION (EMPRESA_COD)   
  TABLESPACE DB_TELCONET ;

CREATE TABLE DB_COMERCIAL.ADMI_GRUPO_PROMOCION_REGLA
(	
  ID_GRUPO_PROMOCION_REGLA  NUMBER(11,0) NOT NULL ,
  GRUPO_PROMOCION_ID        NUMBER(11,0) NOT NULL,       
  CARACTERISTICA_ID         NUMBER(11,0) NOT NULL,   
  VALOR                     VARCHAR2(4000 BYTE) NOT NULL, 
  FE_CREACION               TIMESTAMP (6) NOT NULL, 
  USR_CREACION              VARCHAR2(15 BYTE) NOT NULL, 
  IP_CREACION               VARCHAR2(16 BYTE) NOT NULL, 
  FE_ULT_MOD                TIMESTAMP (6), 
  USR_ULT_MOD               VARCHAR2(15 BYTE), 
  IP_ULT_MOD                VARCHAR2(16 BYTE),  
  SECUENCIA                 NUMBER(11,0),   
  ESTADO                    VARCHAR2(15 BYTE) NOT NULL, 	      	 
  
  CONSTRAINT ADMI_GRUPO_PROMOCION_REGLA_PK PRIMARY KEY 
  (
    ID_GRUPO_PROMOCION_REGLA 
  )
  ENABLE 
);

 COMMENT ON COLUMN DB_COMERCIAL.ADMI_GRUPO_PROMOCION_REGLA.ID_GRUPO_PROMOCION_REGLA IS 'HACE REFERENCIA AL SECUENCIAL DE LA TABLA';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_GRUPO_PROMOCION_REGLA.GRUPO_PROMOCION_ID IS 'CAMPO HACE REFERENCIA AL ID DEL GRUPO DE LA PROMOCION EN
 ADMI_GRUPO_PROMOCION';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_GRUPO_PROMOCION_REGLA.CARACTERISTICA_ID IS 'CAMPO HACE REFERENCIA A LAS REGLAS O CARACTERISTICAS 
GENERALES DEFINIDAS EN EL GRUPO DE UNA PROMOCION';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_GRUPO_PROMOCION_REGLA.VALOR IS 'HACE REFERENCIA AL VALOR DE LA REGLA O CARACTERISTICA DEL GRUPO DE LA PROMOCION';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_GRUPO_PROMOCION_REGLA.FE_CREACION IS 'HACE REFERENCIA A LA FECHA Y HORA DE CREACION DE LA PROMOCION';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_GRUPO_PROMOCION_REGLA.USR_CREACION IS 'HACE REFERENCIA AL USUARIO DE CREACION DEL REGISTRO';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_GRUPO_PROMOCION_REGLA.IP_CREACION IS 'HACE REFERENCIA IP DE CREACION DEL REGISTRO';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_GRUPO_PROMOCION_REGLA.FE_ULT_MOD IS 'HACE REFERENCIA A LA FECHA Y HORA DE ULTIMA MODIFICACION DEL REGISTRO';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_GRUPO_PROMOCION_REGLA.USR_ULT_MOD IS 'HACE REFERENCIA AL USUARIO DE ULTIMA MODIFICACION DEL REGISTRO';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_GRUPO_PROMOCION_REGLA.IP_ULT_MOD IS 'HACE REFERENCIA IP DE ULTIMA MODIFICACION DEL REGISTRO';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_GRUPO_PROMOCION_REGLA.SECUENCIA IS 'HACE REFERENCIA A LA SECUENCIA SEQ_ADMI_REGLA_SECUENCIA GENERADA QUE
 ASOCIA CADA SECTORIZACION: JURISDICCION, CANTON, PARROQUIA, SECTOR, OLT';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_GRUPO_PROMOCION_REGLA.ESTADO IS 'HACE REFERENCIA AL ESTADO DE LA REGLA DEL GRUPO DE LA PROMOCION: 
Pendiente, Activo, Clonado, Inactivo, Baja, Eliminado.';
 
 CREATE SEQUENCE  DB_COMERCIAL.SEQ_ADMI_GRUPO_PROMOCION_REGLA INCREMENT BY 1 START WITH 1 MAXVALUE 99999999999999999 MINVALUE 1 NOCACHE;

ALTER TABLE DB_COMERCIAL.ADMI_GRUPO_PROMOCION_REGLA
ADD CONSTRAINT ADMI_GRUPO_PROMOCION_REGLA_FK1 FOREIGN KEY
(
  GRUPO_PROMOCION_ID 
)
REFERENCES DB_COMERCIAL.ADMI_GRUPO_PROMOCION
(
  ID_GRUPO_PROMOCION
)
ENABLE;

CREATE INDEX DB_COMERCIAL.ADMI_GRUPO_PROMO_REGLA_INDEX1 ON DB_COMERCIAL.ADMI_GRUPO_PROMOCION_REGLA (GRUPO_PROMOCION_ID)   
  TABLESPACE DB_TELCONET ;

ALTER TABLE DB_COMERCIAL.ADMI_GRUPO_PROMOCION_REGLA
ADD CONSTRAINT ADMI_GRUPO_PROMOCION_REGLA_FK2 FOREIGN KEY
(
  CARACTERISTICA_ID 
)
REFERENCES DB_COMERCIAL.ADMI_CARACTERISTICA
(
  ID_CARACTERISTICA
)
ENABLE;

CREATE INDEX DB_COMERCIAL.ADMI_GRUPO_PROMO_REGLA_INDEX2 ON DB_COMERCIAL.ADMI_GRUPO_PROMOCION_REGLA (CARACTERISTICA_ID)   
  TABLESPACE DB_TELCONET ;

CREATE TABLE DB_COMERCIAL.ADMI_TIPO_PROMOCION
(	
  ID_TIPO_PROMOCION      NUMBER(11,0) NOT NULL ,
  GRUPO_PROMOCION_ID     NUMBER(11,0) NOT NULL,       
  CODIGO_TIPO_PROMOCION  VARCHAR2(15 BYTE) NOT NULL, 
  TIPO                   VARCHAR2(50 BYTE) NOT NULL, 
  FE_CREACION            TIMESTAMP (6) NOT NULL, 
  USR_CREACION           VARCHAR2(15 BYTE) NOT NULL, 
  IP_CREACION            VARCHAR2(16 BYTE) NOT NULL, 
  FE_ULT_MOD             TIMESTAMP (6), 
  USR_ULT_MOD            VARCHAR2(15 BYTE), 
  IP_ULT_MOD             VARCHAR2(16 BYTE), 
  TIPO_PROMOCION_ID      NUMBER(11,0),       
  ESTADO                 VARCHAR2(15 BYTE) NOT NULL, 		 
  
  CONSTRAINT ADMI_TIPO_PROMOCION_PK PRIMARY KEY 
  (
    ID_TIPO_PROMOCION 
  )
  ENABLE 
);

 COMMENT ON COLUMN DB_COMERCIAL.ADMI_TIPO_PROMOCION.ID_TIPO_PROMOCION IS 'HACE REFERENCIA AL SECUENCIAL DE LA TABLA';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_TIPO_PROMOCION.GRUPO_PROMOCION_ID IS 'CAMPO HACE REFERENCIA AL ID DE GRUPO DE LA PROMOCION';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_TIPO_PROMOCION.CODIGO_TIPO_PROMOCION IS 'CAMPO HACE REFERENCIA AL CODIGO DEL TIPO DE LA PROMOCION';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_TIPO_PROMOCION.TIPO IS 'CAMPO HACE REFERENCIA AL TIPO DE LA PROMOCION : Descuento en Mensualidad Mix de Planes,
 Descuento en Mensualidad de Planes, Descuento en Mensualidad de Productos, Descuento Total en Mensualidad , Descuento por Ancho de Banda,
 Descuento y Diferido de Instalación';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_TIPO_PROMOCION.FE_CREACION IS 'HACE REFERENCIA A LA FECHA Y HORA DE CREACION DE LA PROMOCION';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_TIPO_PROMOCION.USR_CREACION IS 'HACE REFERENCIA AL USUARIO DE CREACION DE LA PROMOCION';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_TIPO_PROMOCION.IP_CREACION IS 'HACE REFERENCIA IP DE CREACION DEL REGISTRO';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_TIPO_PROMOCION.FE_ULT_MOD IS 'HACE REFERENCIA A LA FECHA Y HORA DE ULTIMA MODIFICACION DE LA PROMOCION';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_TIPO_PROMOCION.USR_ULT_MOD IS 'HACE REFERENCIA AL USUARIO DE ULTIMA MODIFICACION DE LA PROMOCION';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_TIPO_PROMOCION.IP_ULT_MOD IS 'HACE REFERENCIA IP DE ULTIMA MODIFICACION DE LA PROMOCION';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_TIPO_PROMOCION.TIPO_PROMOCION_ID IS 'CAMPO QUE HACE REFERENCIA AL ID DE LA TABLA ADMI_TIPO_PROMOCION 
CUANDO ESTA SE ORIGINA POR UN PROCESO DE CLONACION DE PROMOCION SE ALMACENARA EL ID DE SU PROMOCION ORIGEN';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_TIPO_PROMOCION.ESTADO IS 'HACE REFERENCIA AL ESTADO DE LA PROMOCION: 
Pendiente, Activo, Clonado, Inactivo, Baja.';
 
 CREATE SEQUENCE  DB_COMERCIAL.SEQ_ADMI_TIPO_PROMOCION INCREMENT BY 1 START WITH 1 MAXVALUE 99999999999999999 MINVALUE 1 NOCACHE;

ALTER TABLE DB_COMERCIAL.ADMI_TIPO_PROMOCION
ADD CONSTRAINT ADMI_TIPO_PROMOCION_FK1 FOREIGN KEY
(
  GRUPO_PROMOCION_ID 
)
REFERENCES DB_COMERCIAL.ADMI_GRUPO_PROMOCION
(
  ID_GRUPO_PROMOCION
)
ENABLE;

CREATE INDEX DB_COMERCIAL.ADMI_TIPO_PROMOCION_INDEX1 ON DB_COMERCIAL.ADMI_TIPO_PROMOCION (GRUPO_PROMOCION_ID)   
  TABLESPACE DB_TELCONET ;

CREATE TABLE DB_COMERCIAL.ADMI_TIPO_PROMOCION_REGLA
(
  ID_TIPO_PROMOCION_REGLA   NUMBER(11,0) NOT NULL ,
  TIPO_PROMOCION_ID         NUMBER(11,0) NOT NULL,       
  CARACTERISTICA_ID         NUMBER(11,0) NOT NULL,   
  VALOR                     VARCHAR2(4000 BYTE) NOT NULL, 
  FE_CREACION               TIMESTAMP (6) NOT NULL, 
  USR_CREACION              VARCHAR2(15 BYTE) NOT NULL, 
  IP_CREACION               VARCHAR2(16 BYTE) NOT NULL, 
  FE_ULT_MOD                TIMESTAMP (6), 
  USR_ULT_MOD               VARCHAR2(15 BYTE), 
  IP_ULT_MOD                VARCHAR2(16 BYTE),  
  SECUENCIA                 NUMBER(11,0),   
  ESTADO                    VARCHAR2(15 BYTE) NOT NULL, 	      	 
  
  CONSTRAINT ADMI_TIPO_PROMOCION_REGLA_PK PRIMARY KEY 
  (
    ID_TIPO_PROMOCION_REGLA 
  )
  ENABLE 
);

COMMENT ON COLUMN DB_COMERCIAL.ADMI_TIPO_PROMOCION_REGLA.ID_TIPO_PROMOCION_REGLA IS 'HACE REFERENCIA AL SECUENCIAL DE LA TABLA';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_TIPO_PROMOCION_REGLA.TIPO_PROMOCION_ID IS 'CAMPO HACE REFERENCIA AL ID DEL TIPO DE LA PROMOCION EN 
ADMI_TIPO_PROMOCION';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_TIPO_PROMOCION_REGLA.CARACTERISTICA_ID IS 'CAMPO HACE REFERENCIA A LAS REGLAS O CARACTERISTICAS
 DEFINIDAS EN EL TIPO DE PROMOCION';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_TIPO_PROMOCION_REGLA.VALOR IS 'HACE REFERENCIA AL VALOR DE LA REGLA O CARACTERISTICA DEL TIPO DE LA PROMOCION';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_TIPO_PROMOCION_REGLA.FE_CREACION IS 'HACE REFERENCIA A LA FECHA Y HORA DE CREACION DE LA PROMOCION';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_TIPO_PROMOCION_REGLA.USR_CREACION IS 'HACE REFERENCIA AL USUARIO DE CREACION DEL REGISTRO';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_TIPO_PROMOCION_REGLA.IP_CREACION IS 'HACE REFERENCIA IP DE CREACION DEL REGISTRO';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_TIPO_PROMOCION_REGLA.FE_ULT_MOD IS 'HACE REFERENCIA A LA FECHA Y HORA DE ULTIMA MODIFICACION DEL REGISTRO';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_TIPO_PROMOCION_REGLA.USR_ULT_MOD IS 'HACE REFERENCIA AL USUARIO DE ULTIMA MODIFICACION DEL REGISTRO';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_TIPO_PROMOCION_REGLA.IP_ULT_MOD IS 'HACE REFERENCIA IP DE ULTIMA MODIFICACION DEL REGISTRO';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_TIPO_PROMOCION_REGLA.SECUENCIA IS 'HACE REFERENCIA A LA SECUENCIA SEQ_ADMI_REGLA_SECUENCIA GENERADA 
QUE ASOCIA CADA SECTORIZACION: JURISDICCION, CANTON, PARROQUIA, SECTOR, OLT';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_TIPO_PROMOCION_REGLA.ESTADO IS 'HACE REFERENCIA AL ESTADO DE LA REGLA DEL GRUPO DE LA PROMOCION: 
Pendiente, Activo, Clonado, Inactivo, Baja, Eliminado.';
 
 CREATE SEQUENCE  DB_COMERCIAL.SEQ_ADMI_TIPO_PROMOCION_REGLA INCREMENT BY 1 START WITH 1 MAXVALUE 99999999999999999 MINVALUE 1 NOCACHE;

ALTER TABLE DB_COMERCIAL.ADMI_TIPO_PROMOCION_REGLA
ADD CONSTRAINT ADMI_TIPO_PROMOCION_REGLA_FK1 FOREIGN KEY
(
  TIPO_PROMOCION_ID 
)
REFERENCES DB_COMERCIAL.ADMI_TIPO_PROMOCION
(
  ID_TIPO_PROMOCION
)
ENABLE;

CREATE INDEX DB_COMERCIAL.ADMI_TIPO_PROMO_REGLA_INDEX1 ON DB_COMERCIAL.ADMI_TIPO_PROMOCION_REGLA (TIPO_PROMOCION_ID)   
  TABLESPACE DB_TELCONET ;

ALTER TABLE DB_COMERCIAL.ADMI_TIPO_PROMOCION_REGLA
ADD CONSTRAINT ADMI_TIPO_PROMOCION_REGLA_FK2 FOREIGN KEY
(
  CARACTERISTICA_ID 
)
REFERENCES DB_COMERCIAL.ADMI_CARACTERISTICA
(
  ID_CARACTERISTICA
)
ENABLE;

CREATE INDEX DB_COMERCIAL.ADMI_TIPO_PROMO_REGLA_INDEX2 ON DB_COMERCIAL.ADMI_TIPO_PROMOCION_REGLA (CARACTERISTICA_ID)   
  TABLESPACE DB_TELCONET ;

CREATE TABLE DB_COMERCIAL.ADMI_TIPO_PLAN_PROD_PROMOCION
(	
  ID_TIPO_PLAN_PROD_PROMOCION     NUMBER(11,0) NOT NULL ,
  TIPO_PROMOCION_ID               NUMBER(11,0) NOT NULL,       
  PLAN_ID                         NUMBER(11,0),
  PRODUCTO_ID                     NUMBER(11,0),
  SOLUCION_ID                     NUMBER(11,0),
  PLAN_ID_SUPERIOR                NUMBER(11,0),             
  FE_CREACION                     TIMESTAMP (6) NOT NULL, 
  USR_CREACION                    VARCHAR2(15 BYTE) NOT NULL, 
  IP_CREACION                     VARCHAR2(16 BYTE) NOT NULL, 
  FE_ULT_MOD                      TIMESTAMP (6), 
  USR_ULT_MOD                     VARCHAR2(15 BYTE), 
  IP_ULT_MOD                      VARCHAR2(16 BYTE), 
  ESTADO                          VARCHAR2(15 BYTE) NOT NULL, 	       	 
  
  CONSTRAINT ADMI_TIPO_PLAN_PROD_PROMO_PK PRIMARY KEY 
  (
    ID_TIPO_PLAN_PROD_PROMOCION 
  )
  ENABLE 
);
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_TIPO_PLAN_PROD_PROMOCION.ID_TIPO_PLAN_PROD_PROMOCION IS 'HACE REFERENCIA AL SECUENCIAL DE LA TABLA';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_TIPO_PLAN_PROD_PROMOCION.TIPO_PROMOCION_ID IS 'CAMPO HACE REFERENCIA AL ID DE TIPO DE LA PROMOCION 
EN ADMI_TIPO_PROMOCION';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_TIPO_PLAN_PROD_PROMOCION.PLAN_ID IS 'CAMPO HACE REFERENCIA AL ID DEL PLAN EN INFO_PLAN_CAB AL CUAL 
SE APLICARA LA PROMOCION';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_TIPO_PLAN_PROD_PROMOCION.PRODUCTO_ID IS 'HACE REFERENCIA AL ID DEL PRODUCTO EN ADMI_PRODUCTO AL CUAL 
SE APLICARA LA PROMOCION';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_TIPO_PLAN_PROD_PROMOCION.SOLUCION_ID IS 'HACE REFERENCIA AL ID DE LA SOLUCION';

 COMMENT ON COLUMN DB_COMERCIAL.ADMI_TIPO_PLAN_PROD_PROMOCION.PLAN_ID_SUPERIOR IS 'CAMPO HACE REFERENCIA AL ID DEL PLAN SUPERIOR EN INFO_PLAN_CAB
 EL CUAL SE APLICARA PARA EL TIPO DE PROMOCION POR ANCHO DE BANDA';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_TIPO_PLAN_PROD_PROMOCION.FE_CREACION IS 'HACE REFERENCIA A LA FECHA Y HORA DE CREACION DE LA PROMOCION';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_TIPO_PLAN_PROD_PROMOCION.USR_CREACION IS 'HACE REFERENCIA AL USUARIO DE CREACION DE LA PROMOCION';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_TIPO_PLAN_PROD_PROMOCION.IP_CREACION IS 'HACE REFERENCIA IP DE CREACION DEL REGISTRO';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_TIPO_PLAN_PROD_PROMOCION.FE_ULT_MOD IS 'HACE REFERENCIA A LA FECHA Y HORA DE ULTIMA MODIFICACION 
DE LA PROMOCION';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_TIPO_PLAN_PROD_PROMOCION.USR_ULT_MOD IS 'HACE REFERENCIA AL USUARIO DE ULTIMA MODIFICACION DE LA PROMOCION';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_TIPO_PLAN_PROD_PROMOCION.IP_ULT_MOD IS 'HACE REFERENCIA IP DE ULTIMA MODIFICACION DE LA PROMOCION';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_TIPO_PLAN_PROD_PROMOCION.ESTADO IS 'HACE REFERENCIA AL ESTADO DE LOS PRODUCTOS O PLANES ASOCIADOS 
A LA PROMOCION: Pendiente, Activo, Clonado, Inactivo, Baja, Eliminado.';
 
 CREATE SEQUENCE  DB_COMERCIAL.SEQ_ADMI_TIPO_PLAN_PROD_PROMO INCREMENT BY 1 START WITH 1 MAXVALUE 99999999999999999 MINVALUE 1 NOCACHE;

ALTER TABLE DB_COMERCIAL.ADMI_TIPO_PLAN_PROD_PROMOCION
ADD CONSTRAINT ADMI_TIPO_PLAN_PROD_PROMO_FK1 FOREIGN KEY
(
  TIPO_PROMOCION_ID 
)
REFERENCES DB_COMERCIAL.ADMI_TIPO_PROMOCION
(
  ID_TIPO_PROMOCION
)
ENABLE;

CREATE INDEX DB_COMERCIAL.ADMI_TIPO_PLAN_PROD_PROMO_IDX1 ON DB_COMERCIAL.ADMI_TIPO_PLAN_PROD_PROMOCION (TIPO_PROMOCION_ID)   
  TABLESPACE DB_TELCONET ;

ALTER TABLE DB_COMERCIAL.ADMI_TIPO_PLAN_PROD_PROMOCION
ADD CONSTRAINT ADMI_TIPO_PLAN_PROD_PROMO_FK2 FOREIGN KEY
(
  PLAN_ID 
)
REFERENCES DB_COMERCIAL.INFO_PLAN_CAB
(
  ID_PLAN
)
ENABLE;

CREATE INDEX DB_COMERCIAL.ADMI_TIPO_PLAN_PROD_PROMO_IDX2 ON DB_COMERCIAL.ADMI_TIPO_PLAN_PROD_PROMOCION (PLAN_ID)   
  TABLESPACE DB_TELCONET ;

ALTER TABLE DB_COMERCIAL.ADMI_TIPO_PLAN_PROD_PROMOCION
ADD CONSTRAINT ADMI_TIPO_PLAN_PROD_PROMO_FK3 FOREIGN KEY
(
  PRODUCTO_ID 
)
REFERENCES DB_COMERCIAL.ADMI_PRODUCTO
(
  ID_PRODUCTO
)
ENABLE;

CREATE INDEX DB_COMERCIAL.ADMI_TIPO_PLAN_PROD_PROMO_IDX3 ON DB_COMERCIAL.ADMI_TIPO_PLAN_PROD_PROMOCION (PRODUCTO_ID)   
  TABLESPACE DB_TELCONET ;

ALTER TABLE DB_COMERCIAL.ADMI_TIPO_PLAN_PROD_PROMOCION
ADD CONSTRAINT ADMI_TIPO_PLAN_PROD_PROMO_FK4 FOREIGN KEY
(
  PLAN_ID_SUPERIOR 
)
REFERENCES DB_COMERCIAL.INFO_PLAN_CAB
(
  ID_PLAN
)
ENABLE;

CREATE INDEX DB_COMERCIAL.ADMI_TIPO_PLAN_PROD_PROMO_IDX4 ON DB_COMERCIAL.ADMI_TIPO_PLAN_PROD_PROMOCION (PLAN_ID_SUPERIOR)   
  TABLESPACE DB_TELCONET ;

CREATE TABLE DB_COMERCIAL.ADMI_GRUPO_PROMOCION_HISTO
(
  ID_GRUPO_PROMOCION_HISTO     NUMBER(11,0) NOT NULL ,
  GRUPO_PROMOCION_ID           NUMBER(11,0) NOT NULL ,        
  MOTIVO_ID                    NUMBER(11,0) ,  
  FE_CREACION                  TIMESTAMP (6) NOT NULL, 
  USR_CREACION                 VARCHAR2(15 BYTE) NOT NULL, 
  IP_CREACION                  VARCHAR2(16 BYTE) NOT NULL, 
  OBSERVACION                  VARCHAR2(1500 BYTE), 
  ESTADO                       VARCHAR2(15 BYTE) NOT NULL, 		
  
  CONSTRAINT ADMI_GRUPO_PROMO_HISTO_PK PRIMARY KEY 
  (
    ID_GRUPO_PROMOCION_HISTO 
  )
  ENABLE 
);
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_GRUPO_PROMOCION_HISTO.ID_GRUPO_PROMOCION_HISTO IS 'HACE REFERENCIA AL SECUENCIAL DE LA TABLA';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_GRUPO_PROMOCION_HISTO.GRUPO_PROMOCION_ID IS 'CAMPO HACE REFERENCIA AL ID DEL GRUPO DE LA PROMOCION 
EN ADMI_GRUPO_PROMOCION';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_GRUPO_PROMOCION_HISTO.MOTIVO_ID IS 'CAMPO HACE REFERENCIA AL ID DEL MOTIVO EN CASO CLONACION, DADA DE BAJA, 
INACTIVACION, ELIMINACION DE LA PROMOCION';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_GRUPO_PROMOCION_HISTO.FE_CREACION IS 'HACE REFERENCIA A LA FECHA Y HORA DE CREACION DEL HISTORIAL';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_GRUPO_PROMOCION_HISTO.USR_CREACION IS 'HACE REFERENCIA AL USUARIO DE CREACION DEL HISTORIAL';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_GRUPO_PROMOCION_HISTO.IP_CREACION IS 'HACE REFERENCIA IP DE CREACION DEL REGISTRO';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_GRUPO_PROMOCION_HISTO.OBSERVACION IS 'CAMPO ALMACENA OBSERVACION REFERENTE A LA EDICION DE LA PROMOCION 
O CAMBIO DE ESTADO';
 COMMENT ON COLUMN DB_COMERCIAL.ADMI_GRUPO_PROMOCION_HISTO.ESTADO IS 'HACE REFERENCIA AL CAMBIO DE ESTADO DE LA PROMOCION:
 Pendiente, Activo, Clonado, Inactivo, Baja, Eliminado.';

CREATE SEQUENCE  DB_COMERCIAL.SEQ_ADMI_GRUPO_PROMOCION_HISTO INCREMENT BY 1 START WITH 1 MAXVALUE 99999999999999999 MINVALUE 1 NOCACHE;

ALTER TABLE DB_COMERCIAL.ADMI_GRUPO_PROMOCION_HISTO
ADD CONSTRAINT ADMI_GRUPO_PROMO_HISTO_FK1 FOREIGN KEY
(
  GRUPO_PROMOCION_ID 
)
REFERENCES DB_COMERCIAL.ADMI_GRUPO_PROMOCION
(
  ID_GRUPO_PROMOCION
)
ENABLE;

CREATE INDEX DB_COMERCIAL.ADMI_GRUPO_PROMO_HISTO_IDX1 ON DB_COMERCIAL.ADMI_GRUPO_PROMOCION_HISTO (GRUPO_PROMOCION_ID)   
  TABLESPACE DB_TELCONET ;

ALTER TABLE DB_COMERCIAL.ADMI_GRUPO_PROMOCION_HISTO
ADD CONSTRAINT ADMI_GRUPO_PROMO_HISTO_FK2 FOREIGN KEY
(
  MOTIVO_ID 
)
REFERENCES DB_GENERAL.ADMI_MOTIVO
(
  ID_MOTIVO
)
ENABLE;

CREATE INDEX DB_COMERCIAL.ADMI_GRUPO_PROMO_HISTO_IDX2 ON DB_COMERCIAL.ADMI_GRUPO_PROMOCION_HISTO (MOTIVO_ID)   
  TABLESPACE DB_TELCONET ;

/