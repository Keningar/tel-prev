/**
 * Documentación para crear tablas temporales
 * Parámetros de creación en DB_INFRAESTRUCTURA.TEMP_RUTA_CAB 
 * y DB_INFRAESTRUCTURA.TEMP_RUTA_DET.
 *
 * @author Antonio Ayala <afayala@telconet.ec>
 * @version 1.0 03-01-2022
 */

CREATE TABLE DB_INFRAESTRUCTURA.TEMP_RUTA_CAB
(
  ID_RUTA_CAB NUMBER NOT NULL 
, NOMBRE_RUTA VARCHAR2(500) NOT NULL 
, DESCRIPCION VARCHAR2(500) NOT NULL 
, TIPO_RUTA VARCHAR2(500) NOT NULL 
, TIPO_INFRAESTRUCTURA VARCHAR2(500) NOT NULL
, PROCESO VARCHAR2(500) NOT NULL
, ELEMENTO_INICIO VARCHAR2(500) NOT NULL
, ELEMENTO_FIN VARCHAR2(500) NOT NULL
, TIPO_FIBRA VARCHAR2(500) NOT NULL
, CONSTRAINT TEMP_RUTA_CAB_PK PRIMARY KEY 
  (
    ID_RUTA_CAB 
  )
  ENABLE 
);

-- Add comments to the columns 
    COMMENT ON COLUMN DB_INFRAESTRUCTURA.TEMP_RUTA_CAB.ID_RUTA_CAB  is 'Campo para identificar el id secuencial con que se crea la tabla temporal';
    COMMENT ON COLUMN DB_INFRAESTRUCTURA.TEMP_RUTA_CAB.NOMBRE_RUTA  is 'Campo para identificar nombre de la ruta';
    COMMENT ON COLUMN DB_INFRAESTRUCTURA.TEMP_RUTA_CAB.DESCRIPCION  is 'Campo para identificar la descripcion de la ruta';
    COMMENT ON COLUMN DB_INFRAESTRUCTURA.TEMP_RUTA_CAB.TIPO_RUTA  is 'Campo para identificar el tipo de Ruta';
    COMMENT ON COLUMN DB_INFRAESTRUCTURA.TEMP_RUTA_CAB.TIPO_INFRAESTRUCTURA  is 'Campo para identificar a que tipo de infraestructura pertenece';
    COMMENT ON COLUMN DB_INFRAESTRUCTURA.TEMP_RUTA_CAB.PROCESO  is 'Campo para identificar el tipo de proceso';
    COMMENT ON COLUMN DB_INFRAESTRUCTURA.TEMP_RUTA_CAB.ELEMENTO_INICIO  is 'Campo para identificar el nombre del elemento inicio';
    COMMENT ON COLUMN DB_INFRAESTRUCTURA.TEMP_RUTA_CAB.ELEMENTO_FIN  is 'Campo para identificar el nombre del elemento fin';
    COMMENT ON COLUMN DB_INFRAESTRUCTURA.TEMP_RUTA_CAB.TIPO_FIBRA  is 'Campo para identificar el tipo de fibra para la ruta';
	
--Creando la secuencia del estado de la instalacion
CREATE SEQUENCE DB_INFRAESTRUCTURA.SEQ_TEMP_RUTA_CAB INCREMENT BY 1 MAXVALUE 9999999999999999999999999999 MINVALUE 1 NOCACHE;


--Creando la tabla de detalle de TEMP_RUTA_DET
CREATE TABLE DB_INFRAESTRUCTURA.TEMP_RUTA_DET
(
  ID_RUTA_DET NUMBER NOT NULL 
, RUTA_ID_CAB NUMBER NOT NULL 
, NOMBRE_ELEMENTO VARCHAR2(500) NOT NULL 
, TIPO_FIBRA VARCHAR2(500) NOT NULL
, TIPO_ELEMENTO VARCHAR2(500) NULL
, DESCRIPCION VARCHAR2(500) NULL
, MODELO_ELEMENTO VARCHAR2(500) NULL
, JURISDICCION VARCHAR2(500) NULL
, CANTON VARCHAR2(500) NULL
, PARROQUIA VARCHAR2(500) NULL
, DIRECCION VARCHAR2(500) NULL
, ALTURA VARCHAR2(500) NULL
, LATITUD VARCHAR2(200) NULL
, LONGITUD VARCHAR2(200) NULL
, UBICADO_EN VARCHAR2(500) NULL
, NIVEL VARCHAR2(200) NULL
, FACTIBILIDAD VARCHAR2(5) NULL
, CONSTRAINT TEMP_RUTA_DET_PK PRIMARY KEY 
  (
    ID_RUTA_DET 
  )
  ENABLE 
);

-- Add comments to the columns 
    COMMENT ON COLUMN DB_INFRAESTRUCTURA.TEMP_RUTA_DET.ID_RUTA_DET  is 'Campo para identificar el id secuencial con que se crea la tabla temporal';
    COMMENT ON COLUMN DB_INFRAESTRUCTURA.TEMP_RUTA_DET.RUTA_ID_CAB  is 'Campo para identificar la relacion con TEMP_RUTA_CAB';
    COMMENT ON COLUMN DB_INFRAESTRUCTURA.TEMP_RUTA_DET.NOMBRE_ELEMENTO  is 'Campo para identificar el nombre de los elementos intermedios';
    COMMENT ON COLUMN DB_INFRAESTRUCTURA.TEMP_RUTA_DET.TIPO_FIBRA  is 'Campo para identificar el tipo de fibra del elemento';
    COMMENT ON COLUMN DB_INFRAESTRUCTURA.TEMP_RUTA_DET.TIPO_ELEMENTO  is 'Campo para identificar el tipo de elemento';
    COMMENT ON COLUMN DB_INFRAESTRUCTURA.TEMP_RUTA_DET.DESCRIPCION  is 'Campo para identificar la descripcion del elemento';
    COMMENT ON COLUMN DB_INFRAESTRUCTURA.TEMP_RUTA_DET.MODELO_ELEMENTO  is 'Campo para identificar el modelo del elemento';
    COMMENT ON COLUMN DB_INFRAESTRUCTURA.TEMP_RUTA_DET.JURISDICCION  is 'Campo para identificar la Jurisdiccion donde pertenece el elemento';
    COMMENT ON COLUMN DB_INFRAESTRUCTURA.TEMP_RUTA_DET.CANTON  is 'Campo para identificar el canton donde pertenece el elemento';
    COMMENT ON COLUMN DB_INFRAESTRUCTURA.TEMP_RUTA_DET.PARROQUIA  is 'Campo para identificar la parroquia donde pertenece el elemento';
    COMMENT ON COLUMN DB_INFRAESTRUCTURA.TEMP_RUTA_DET.DIRECCION  is 'Campo para identificar la direccion donde pertenece el elemento';
    COMMENT ON COLUMN DB_INFRAESTRUCTURA.TEMP_RUTA_DET.ALTURA  is 'Campo para identificar la altura sobre el nivel del mar';
    COMMENT ON COLUMN DB_INFRAESTRUCTURA.TEMP_RUTA_DET.LATITUD  is 'Campo para identificar la latitud donde està ubicado el elemento';
    COMMENT ON COLUMN DB_INFRAESTRUCTURA.TEMP_RUTA_DET.LONGITUD  is 'Campo para identificar la longitud donde està ubicado el elemento';
    COMMENT ON COLUMN DB_INFRAESTRUCTURA.TEMP_RUTA_DET.UBICADO_EN  is 'Campo para identificar donde esta ubicado el elemento';
    COMMENT ON COLUMN DB_INFRAESTRUCTURA.TEMP_RUTA_DET.NIVEL  is 'Campo para identificar el nivel del elemento si es Manga';
    COMMENT ON COLUMN DB_INFRAESTRUCTURA.TEMP_RUTA_DET.FACTIBILIDAD  is 'Campo para identificar si elemento requiere Factibilidad SI o NO';

--Creando la secuencia del estado de la instalacion
CREATE SEQUENCE DB_INFRAESTRUCTURA.SEQ_TEMP_RUTA_DET INCREMENT BY 1 MAXVALUE 9999999999999999999999999999 MINVALUE 1 NOCACHE;

/
