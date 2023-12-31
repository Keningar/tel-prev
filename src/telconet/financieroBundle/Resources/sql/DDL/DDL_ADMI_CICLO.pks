ALTER TABLE DB_FINANCIERO.ADMI_CICLO 
ADD (EMPRESA_COD VARCHAR2(2) );

ALTER TABLE DB_FINANCIERO.ADMI_CICLO 
ADD (ESTADO VARCHAR2(15) );

ALTER TABLE DB_FINANCIERO.ADMI_CICLO 
ADD CONSTRAINT ADMI_CICLO_FK1 FOREIGN KEY
(
  EMPRESA_COD 
)
REFERENCES DB_COMERCIAL.INFO_EMPRESA_GRUPO
(
  COD_EMPRESA
)
ENABLE;
CREATE INDEX DB_FINANCIERO.ADMI_CICLO_INDEX1 ON DB_FINANCIERO.ADMI_CICLO (EMPRESA_COD)   
  TABLESPACE DB_TELCONET ;

  
COMMENT ON COLUMN DB_FINANCIERO.ADMI_CICLO.EMPRESA_COD IS 'Almacena el codigo de la empresa';

COMMENT ON COLUMN DB_FINANCIERO.ADMI_CICLO.ESTADO IS 'Almacena el estado del ciclo: Activo, Inactivo, Eliminado';

ALTER TABLE DB_FINANCIERO.ADMI_CICLO  
MODIFY (FE_CREACION TIMESTAMP );


--
--
CREATE TABLE DB_FINANCIERO.ADMI_CICLO_HISTORIAL
   (	ID_CICLO_HISTORIAL NUMBER(11,0) NOT NULL ,
        CICLO_ID NUMBER(11,0) NOT NULL ,
	NOMBRE_CICLO VARCHAR2(50 BYTE), 
	FE_INICIO DATE, 
	FE_FIN DATE, 
	OBSERVACION VARCHAR2(1000), 
	FE_CREACION TIMESTAMP (6), 
	USR_CREACION VARCHAR2(15 BYTE), 
	IP_CREACION VARCHAR2(16 BYTE), 
	EMPRESA_COD VARCHAR2(2 BYTE), 
	ESTADO VARCHAR2(15 BYTE), 		 
  
  CONSTRAINT ADMI_CICLO_HISTORIAL_PK PRIMARY KEY 
  (
    ID_CICLO_HISTORIAL 
  )
  ENABLE 
);
 COMMENT ON COLUMN DB_FINANCIERO.ADMI_CICLO_HISTORIAL.ID_CICLO_HISTORIAL IS 'SECUENCIAL DE LA TABLA';
 COMMENT ON COLUMN DB_FINANCIERO.ADMI_CICLO_HISTORIAL.CICLO_ID IS 'CAMPO HACE REFERENCIA AL ID_CICLO DE LA TABLA ADMI_CICLO';
 COMMENT ON COLUMN DB_FINANCIERO.ADMI_CICLO_HISTORIAL.NOMBRE_CICLO IS 'CAMPO HACE REFERENCIA AL NOMBRE DEL CICLO';
 COMMENT ON COLUMN DB_FINANCIERO.ADMI_CICLO_HISTORIAL.FE_INICIO IS 'FECHA INICIO DEL CICLO';
 COMMENT ON COLUMN DB_FINANCIERO.ADMI_CICLO_HISTORIAL.FE_FIN IS 'FECHA FIN DEL CICLO';
 COMMENT ON COLUMN DB_FINANCIERO.ADMI_CICLO_HISTORIAL.OBSERVACION IS 'CAMPO HACE REFERENCIA A LA MODIFICACION DEL REGISTRO DEL CICLO';
 COMMENT ON COLUMN DB_FINANCIERO.ADMI_CICLO_HISTORIAL.FE_CREACION IS 'FECHA Y HORA DE CREACION DEL REGISTRO';
 COMMENT ON COLUMN DB_FINANCIERO.ADMI_CICLO_HISTORIAL.USR_CREACION IS 'USUARIO DE CREACION DEL REGISTRO';
 COMMENT ON COLUMN DB_FINANCIERO.ADMI_CICLO_HISTORIAL.IP_CREACION IS 'IP DE CREACION DEL REGISTRO';
 COMMENT ON COLUMN DB_FINANCIERO.ADMI_CICLO_HISTORIAL.EMPRESA_COD IS 'CAMPO QUE HACE REFERENCIA AL CODIGO DE EMPRESA';
 COMMENT ON COLUMN DB_FINANCIERO.ADMI_CICLO_HISTORIAL.ESTADO IS 'INDICA EL ESTADO DEL REGISTRO';
 
 CREATE SEQUENCE  DB_FINANCIERO.SEQ_ADMI_CICLO_HISTORIAL INCREMENT BY 1 START WITH 1 MAXVALUE 99999999999999999 MINVALUE 1 NOCACHE;
 
ALTER TABLE DB_FINANCIERO.ADMI_CICLO_HISTORIAL
ADD CONSTRAINT ADMI_CICLO_HISTORIAL_FK1 FOREIGN KEY
(
  CICLO_ID 
)
REFERENCES DB_FINANCIERO.ADMI_CICLO
(
  ID_CICLO
)
ENABLE;

ALTER TABLE DB_FINANCIERO.ADMI_CICLO_HISTORIAL
ADD CONSTRAINT ADMI_CICLO_HISTORIAL_FK2 FOREIGN KEY
(
  EMPRESA_COD 
)
REFERENCES DB_COMERCIAL.INFO_EMPRESA_GRUPO
(
  COD_EMPRESA
)
ENABLE;

CREATE INDEX DB_FINANCIERO.ADMI_CICLO_HISTORIAL_INDEX1 ON DB_FINANCIERO.ADMI_CICLO_HISTORIAL (CICLO_ID)   
  TABLESPACE DB_TELCONET ;
  
CREATE INDEX DB_FINANCIERO.ADMI_CICLO_HISTORIAL_INDEX2 ON DB_FINANCIERO.ADMI_CICLO_HISTORIAL (EMPRESA_COD)   
  TABLESPACE DB_TELCONET ;
