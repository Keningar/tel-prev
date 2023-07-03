
CREATE TABLE DB_COMERCIAL.TEMP_CONTACTOS
   (    ID_CONTACTO            NUMBER(11,0) NOT NULL ,        
        NOMBRES                VARCHAR2(100 BYTE), 
        APELLIDOS              VARCHAR2(100 BYTE), 
        TITULO                 VARCHAR2(100 BYTE), 
        TIPO_CONTACTO          VARCHAR2(100 BYTE), 
        TELEFONO_FIJO          VARCHAR2(100 BYTE),
        TELEFONO_MOVIL1        VARCHAR2(100 BYTE), 
        OPERADORA_MOVIL1       VARCHAR2(100 BYTE), 
        TELEFONO_MOVIL2        VARCHAR2(100 BYTE), 
        OPERADORA_MOVIL2       VARCHAR2(100 BYTE),
        CORREO_ELECTRONICO     VARCHAR2(100 BYTE),	
        LOGIN                  VARCHAR2(60 BYTE),
        OBSERVACION            VARCHAR2(1000), 
        TELEFONO_INTERNACIONAL VARCHAR2(100 BYTE),
  
  CONSTRAINT TEMP_CONTACTOS_PK PRIMARY KEY 
  (
    ID_CONTACTO 
  )
  ENABLE 
);
 CREATE SEQUENCE  DB_COMERCIAL.SEQ_TEMP_CONTACTOS INCREMENT BY 1 START WITH 1 MAXVALUE 99999999999999999 MINVALUE 1 NOCACHE;
 

CREATE INDEX DB_COMERCIAL.TEMP_CONTACTOS_INDEX1 ON DB_COMERCIAL.TEMP_CONTACTOS (TITULO ASC);
CREATE INDEX DB_COMERCIAL.TEMP_CONTACTOS_INDEX2 ON DB_COMERCIAL.TEMP_CONTACTOS (OPERADORA_MOVIL1 ASC);
CREATE INDEX DB_COMERCIAL.TEMP_CONTACTOS_INDEX3 ON DB_COMERCIAL.TEMP_CONTACTOS (LOGIN ASC);
CREATE INDEX DB_COMERCIAL.TEMP_CONTACTOS_INDEX4 ON DB_COMERCIAL.TEMP_CONTACTOS (TIPO_CONTACTO ASC);
