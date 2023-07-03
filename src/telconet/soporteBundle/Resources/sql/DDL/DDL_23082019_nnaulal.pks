--Creando la tabla que contiene los seguimientos de ECUCERT
CREATE TABLE DB_SOPORTE.ADMI_SEGUIMIENTO_ECUCERT
(
  ID_SEGUIMIENTO_ECUCERT INTEGER NOT NULL 
, SEGUIMIENTO VARCHAR2(2000) NOT NULL 
, ESTADO VARCHAR2(200) DEFAULT 'A'
, USR_CREACION VARCHAR2(200) NOT NULL 
, USR_ULT_MOD VARCHAR2(200) NULL 
, FE_CREACION  DATE NOT NULL
, FE_ULT_MOD  DATE NULL
, IP_CREACION VARCHAR2(200) NOT NULL
, IP_ULT_MOD VARCHAR2(200) NULL 
, CONSTRAINT ADMI_SEGUIMIENTO_ECUCERT_PK PRIMARY KEY 
  (
    ID_SEGUIMIENTO_ECUCERT 
  )
  ENABLE 
);

-- Add comments to the columns 
  COMMENT ON COLUMN DB_SOPORTE.ADMI_SEGUIMIENTO_ECUCERT.ID_SEGUIMIENTO_ECUCERT  is 'Campo para identificar el id secuencial con que se crea el seguimiento de ECUCERT ';
  COMMENT ON COLUMN DB_SOPORTE.ADMI_SEGUIMIENTO_ECUCERT.SEGUIMIENTO  is 'Campo para identificar el Seguimiento que aparecera en el reporte a ECUCERT (Activo = A e Inactivo = I)';
  COMMENT ON COLUMN DB_SOPORTE.ADMI_SEGUIMIENTO_ECUCERT.ESTADO  is 'Campo para identificar el estado del registro de los seguimientos ECUCERT';
  COMMENT ON COLUMN DB_SOPORTE.ADMI_SEGUIMIENTO_ECUCERT.USR_CREACION  is 'Campo para identificar el usuario que creo el registro';
	COMMENT ON COLUMN DB_SOPORTE.ADMI_SEGUIMIENTO_ECUCERT.USR_ULT_MOD  is 'Campo para identificar el ultimo usuario que modifico el registro';
	COMMENT ON COLUMN DB_SOPORTE.ADMI_SEGUIMIENTO_ECUCERT.FE_CREACION  is 'Campo para identificar la fecha de creacion del registro';
	COMMENT ON COLUMN DB_SOPORTE.ADMI_SEGUIMIENTO_ECUCERT.FE_ULT_MOD  is 'Campo para identificar la ultima fecha que se modifico el registro';
	COMMENT ON COLUMN DB_SOPORTE.ADMI_SEGUIMIENTO_ECUCERT.IP_CREACION  is 'Campo para identificar la ip de origen que crea el registro';
	COMMENT ON COLUMN DB_SOPORTE.ADMI_SEGUIMIENTO_ECUCERT.IP_ULT_MOD  is 'Campo para identificar la ultima ip que modifico el registro';

--Creando la secuencia del estado de la instalacion
CREATE SEQUENCE DB_SOPORTE.SEQ_ADMI_SEGUIMIENTO_ECUCERT INCREMENT BY 1 MAXVALUE 9999999999999999999999999999 MINVALUE 1 NOCACHE;



