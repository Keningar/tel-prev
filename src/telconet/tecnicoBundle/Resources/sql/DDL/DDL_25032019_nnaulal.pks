--Creando la tabla que contiene la cabecer de la incidencia ECUCERT
CREATE TABLE DB_SOPORTE.INFO_INCIDENCIA_CAB
(
  ID_INCIDENCIA INTEGER NOT NULL 
, NO_TICKET VARCHAR2(200) NOT NULL 
, FE_TICKET TIMESTAMP NOT NULL 
, CATEGORIA VARCHAR2(200) NOT NULL
, SUBCATEGORIA VARCHAR2(200) NULL
, ESTADO VARCHAR2(200) NOT NULL
, PRIORIDAD VARCHAR2(200) NOT NULL
, SUBJECT VARCHAR2(200) NOT NULL
, TIPO_EVENTO VARCHAR(400) NOT NULL
, INCIDENCIA_REQUEST_ID INTEGER NOT NULL
, NUMERO_REGISTROS INTEGER NOT NULL
, TITULO_CATEGORIA VARCHAR2(200) NOT NULL
, PRIORIDAD_INFRAES VARCHAR2(200) NOT NULL
, NOMBRE_CATEGORIA VARCHAR2(200) NOT NULL
, USR_CREACION VARCHAR2(200) NOT NULL 
, USR_ULT_MOD VARCHAR2(200) NULL 
, FE_CREACION  DATE NOT NULL
, FE_ULT_MOD  DATE NULL
, IP_CREACION VARCHAR2(200) NOT NULL
, IP_ULT_MOD VARCHAR2(200) NULL 
, CONSTRAINT INFO_INCIDENCIA_CAB_PK PRIMARY KEY 
  (
    ID_INCIDENCIA 
  )
  ENABLE 
);

-- Add comments to the columns 
    COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_CAB.ID_INCIDENCIA  is 'Campo para identificar el id secuencial con que se crea la incidencia de ECUCERT ';
    COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_CAB.NO_TICKET  is 'Campo para identificar los tickets de incidencia enviados por ECUCERT';
	COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_CAB.FE_TICKET  is 'Campo para identificar la fecha que se envio el ticket de incidencia de parte de ECUCERT';
	COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_CAB.CATEGORIA  is 'Campo para identificar a que categoria pertenece la incidencia';
	COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_CAB.SUBCATEGORIA  is 'Campo para identificar a que subcategoria pertenece la incidencia';
    COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_CAB.USR_CREACION  is 'Campo para identificar el usuario que creo el registro';
	COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_CAB.USR_ULT_MOD  is 'Campo para identificar el ultimo usuario que modifico el registro';
	COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_CAB.FE_CREACION  is 'Campo para identificar la fecha de creacion del registro';
	COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_CAB.FE_ULT_MOD  is 'Campo para identificar la ultima fecha que se modifico el registro';
	COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_CAB.IP_CREACION  is 'Campo para identificar la ip de origen que crea el registro';
	COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_CAB.IP_ULT_MOD  is 'Campo para identificar la ultima ip que modifico el registro';
	COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_CAB.ESTADO  is 'Campo para identificar si el registro fue procesado';
	COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_CAB.SUBJECT  is 'Campo para identificar lel titulo del correo';
	COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_CAB.PRIORIDAD  is 'Campo para identificar la prioridad de la incidencia';
    COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_CAB.TIPO_EVENTO  is 'Campo para identificar el tipo de evento si es incidencia o vulnerabilidad';
    COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_CAB.INCIDENCIA_REQUEST_ID  is 'Campo para identificar el id donde se guarda el Request enviado por ECUCERT';
    COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_CAB.NUMERO_REGISTROS  is 'Campo para identificar el números de ips que envía ECUCERT para guardarlo en telcos';
    COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_CAB.TITULO_CATEGORIA  is 'Campo para identificar el titulo de categoria';
    COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_CAB.PRIORIDAD_INFRAES  is 'Campo para identificar la prioridad de infraestructura';
    COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_CAB.NOMBRE_CATEGORIA  is 'Campo para identificar el nombre de la categoria';

--Creando la secuencia del estado de la instalacion
CREATE SEQUENCE DB_SOPORTE.SEQ_INFO_INCIDENCIA_CAB INCREMENT BY 1 MAXVALUE 9999999999999999999999999999 MINVALUE 1 NOCACHE;


--Creando la tabla de detalle de incidencia ECUCERT
CREATE TABLE DB_SOPORTE.INFO_INCIDENCIA_DET
(
  ID_DETALLE_INCIDENCIA INTEGER NOT NULL 
, INCIDENCIA_ID INTEGER NOT NULL 
, IP VARCHAR2(200) NOT NULL 
, FE_INCIDENCIA TIMESTAMP NULL 
, IPWAN VARCHAR2(200) NULL 
, PUERTO VARCHAR2(200) NULL 
, IP_DEST VARCHAR2(200) NULL
, IP_CC VARCHAR2(200) NULL
, STATUS VARCHAR2(200) NOT NULL
, CASO_ID INTEGER  NULL
, PERSONA_EMPRESA_ROL_ID INTEGER NULL
, TIPO_USUARIO VARCHAR2(200) NULL
, EMPRESA_ID INTEGER NULL
, COMUNICACION_ID INTEGER NULL
, SERVICIO_ID INTEGER NULL
, ESTADO_GESTION VARCHAR2(200) NOT NULL
, SUB_ESTADO VARCHAR2(200) NULL
, PUERTO_DEST VARCHAR2(200) NULL
, ESCSOC INTEGER NULL
, ESCPE INTEGER NULL
, ESSG INTEGER NULL
, USR_CREACION VARCHAR2(200) NOT NULL 
, USR_ULT_MOD VARCHAR2(200) NULL 
, FE_CREACION DATE NOT NULL
, FE_ULT_MOD DATE NULL
, IP_CREACION VARCHAR2(200) NOT NULL
, IP_ULT_MOD VARCHAR2(200) NULL 
, CONSTRAINT INFO_INCIDENCIA_DET_PK PRIMARY KEY 
  (
    ID_DETALLE_INCIDENCIA 
  )
  ENABLE 
);

-- Add comments to the columns 
    COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_DET.ID_DETALLE_INCIDENCIA  is 'Campo para identificar el id secuencial con que se crea el detalle de la incidencia de ECUCERT ';
    COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_DET.INCIDENCIA_ID  is 'Campo para identificar el id secuencial con que se crea la incidencia de ECUCERT ';
    COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_DET.IP  is 'Campo para identificar la ip publica del cliente';
	COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_DET.IPWAN  is 'Campo para identificar la ip WAN del cliente';
    COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_DET.PUERTO  is 'Campo para identificar el puerto del cliente';
	COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_DET.IP_DEST  is 'Campo para identificar la ip de destino';
	COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_DET.IP_CC  is 'Campo para identificar la ip de quien controla remotamente';
    COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_DET.STATUS  is 'Campo para identificar el estado el estado de la incidencia';
    COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_DET.EMPRESA_ID  is 'Campo para identificar la empresa del usuario si es cliente o infraestructura';
    COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_DET.COMUNICACION_ID  is 'Campo para identificar el número de tarea';
    COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_DET.USR_CREACION  is 'Campo para identificar el usuario que creo el registro';
	COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_DET.USR_ULT_MOD  is 'Campo para identificar el ultimo usuario que modifico el registro';
	COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_DET.FE_CREACION  is 'Campo para identificar la fecha de creacion del registro';
	COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_DET.FE_ULT_MOD  is 'Campo para identificar la ultima fecha que se modifico el registro';
	COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_DET.IP_CREACION  is 'Campo para identificar la ip de origen que crea el registro';
	COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_DET.IP_ULT_MOD  is 'Campo para identificar la ultima ip que modifico el registro';
	COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_DET.CASO_ID  is 'Campo para identificar el caso que se creo';
	COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_DET.PERSONA_EMPRESA_ROL_ID  is 'Campo para identificar al cliente con su rol';
	COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_DET.TIPO_USUARIO  is 'Campo para identificar el tipo de usuario si es cliente o infraestructura';
    COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_DET.SERVICIO_ID  is 'Campo para identificar el servicio del cliente';
    COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_DET.ESTADO_GESTION  is 'Campo para identificar el estado de Gestion de la incidencia por la IP';  
    COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_DET.PUERTO_DEST  is 'Campo para identificar el puerto de destino';  
    COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_DET.SUB_ESTADO  is 'Campo para identificar el sub estado de la incidencia asociada a la IP';  
    COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_DET.FE_INCIDENCIA  is 'Campo para identificar la fecha que se cometió la incidencia de parte de ECUCERT';
    COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_DET.ESCSOC  is 'Campo para identificar si la IP es un cliente CSOC ';
    COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_DET.ESCPE  is 'Campo para identificar si la IP perteneces a un CPE';
    COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_DET.ESSG  is 'Campo para identificar si la Ip es un cliente con seguridad gestionada';

--Creando la secuencia del estado de la instalacion
CREATE SEQUENCE DB_SOPORTE.SEQ_INFO_INCIDENCIA_DET INCREMENT BY 1 MAXVALUE 9999999999999999999999999999 MINVALUE 1 NOCACHE;


--Creando la tabla de notificación de incidencia ECUCERT
CREATE TABLE DB_SOPORTE.INFO_INCIDENCIA_NOTIF
(
  ID_INCIDENCIA_NOTIFICACION INTEGER NOT NULL
, DETALLE_INCIDENCIA_ID INTEGER NOT NULL 
, TIPO_CONTACTO VARCHAR2(400) NULL
, CORREO VARCHAR2(400) NULL
, ESTADO VARCHAR2(400) NOT NULL  
, USR_CREACION VARCHAR2(200) NOT NULL 
, USR_ULT_MOD VARCHAR2(200) NULL 
, FE_CREACION DATE NOT NULL
, FE_ULT_MOD DATE NULL
, IP_CREACION VARCHAR2(200) NOT NULL
, IP_ULT_MOD VARCHAR2(200) NULL 
, CONSTRAINT INFO_INCIDENCIA_NOTIF_PK PRIMARY KEY 
  (
    ID_INCIDENCIA_NOTIFICACION 
  )
  ENABLE 
);

-- Add comments to the columns 
    COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_NOTIF.ID_INCIDENCIA_NOTIFICACION  is 'Campo para identificar el id secuencial de notificación de envio de correo';
    COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_NOTIF.DETALLE_INCIDENCIA_ID  is 'Campo para identificar el id secuencial con que se crea el detale de la incidencia de ECUCERT ';
    COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_NOTIF.CORREO is 'Campo para almacenar el correo electronico del contacto técnico';
    COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_NOTIF.ESTADO  is 'Estado de la notificación';
    COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_NOTIF.USR_CREACION  is 'Campo para identificar el usuario que creo el registro';
	COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_NOTIF.USR_ULT_MOD  is 'Campo para identificar el ultimo usuario que modifico el registro';
	COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_NOTIF.FE_CREACION  is 'Campo para identificar la fecha de creacion del registro';
	COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_NOTIF.FE_ULT_MOD  is 'Campo para identificar la ultima fecha que se modifico el registro';
	COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_NOTIF.IP_CREACION  is 'Campo para identificar la ip de origen que crea el registro';
	COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_NOTIF.IP_ULT_MOD  is 'Campo para identificar la ultima ip que modifico el registro';
    COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_NOTIF.TIPO_CONTACTO  is 'Campo para identificar el tipo de contacto del cliente que se notifico';

--Creando de la secuencia de notificación de incidencia ECUCERT 
CREATE SEQUENCE DB_SOPORTE.SEQ_INFO_INCIDENCIA_NOTIF INCREMENT BY 1 MAXVALUE 9999999999999999999999999999 MINVALUE 1 NOCACHE;


--Creando la tabla de incidencia ECUCERT
    CREATE TABLE DB_SOPORTE.ADMI_INCIDENCIA_PRIORIDAD
    (
      ID_INCIDENCIA_PRIORIDAD INTEGER NOT NULL
    , TIPO_EVENTO VARCHAR2(200) NOT NULL 
    , PRIORIDAD VARCHAR2(200) NOT NULL
    , TIEMPO_HORA VARCHAR2(200) NOT NULL
    , USR_CREACION VARCHAR2(200) NOT NULL 
    , USR_ULT_MOD VARCHAR2(200) NULL 
    , FE_CREACION  DATE NOT NULL
    , FE_ULT_MOD  DATE NULL
    , IP_CREACION VARCHAR2(200) NOT NULL
    , IP_ULT_MOD VARCHAR2(200) NULL 
    , CONSTRAINT ADMI_INCIDENCIA_PRIORIDAD_PK PRIMARY KEY 
      (
        ID_INCIDENCIA_PRIORIDAD 
      )
      ENABLE 
    );

-- Add comments to the columns 
    COMMENT ON COLUMN DB_SOPORTE.ADMI_INCIDENCIA_PRIORIDAD.ID_INCIDENCIA_PRIORIDAD  is 'Campo para identificar el id secuencial con que se crea la incidencia de ECUCERT ';
    COMMENT ON COLUMN DB_SOPORTE.ADMI_INCIDENCIA_PRIORIDAD.TIPO_EVENTO  is 'Campo para identificar el tipo de evento';
    COMMENT ON COLUMN DB_SOPORTE.ADMI_INCIDENCIA_PRIORIDAD.PRIORIDAD  is 'Campo para identificar la prioridad enviada por ECUCERT';
	COMMENT ON COLUMN DB_SOPORTE.ADMI_INCIDENCIA_PRIORIDAD.TIEMPO_HORA  is 'Campo para identificar el tiempo en horas por prioridad para el envio de reporte';
    COMMENT ON COLUMN DB_SOPORTE.ADMI_INCIDENCIA_PRIORIDAD.USR_CREACION  is 'Campo para identificar el usuario que creo el registro';
	COMMENT ON COLUMN DB_SOPORTE.ADMI_INCIDENCIA_PRIORIDAD.USR_ULT_MOD  is 'Campo para identificar el ultimo usuario que modifico el registro';
	COMMENT ON COLUMN DB_SOPORTE.ADMI_INCIDENCIA_PRIORIDAD.FE_CREACION  is 'Campo para identificar la fecha de creacion del registro';
	COMMENT ON COLUMN DB_SOPORTE.ADMI_INCIDENCIA_PRIORIDAD.FE_ULT_MOD  is 'Campo para identificar la ultima fecha que se modifico el registro';
	COMMENT ON COLUMN DB_SOPORTE.ADMI_INCIDENCIA_PRIORIDAD.IP_CREACION  is 'Campo para identificar la ip de origen que crea el registro';
	COMMENT ON COLUMN DB_SOPORTE.ADMI_INCIDENCIA_PRIORIDAD.IP_ULT_MOD  is 'Campo para identificar la ultima ip que modifico el registro';

--Creando la secuencia del estado de la instalacion
CREATE SEQUENCE DB_SOPORTE.SEQ_ADMI_INCIDENCIA_PRIORIDAD INCREMENT BY 1 MAXVALUE 9999999999999999999999999999 MINVALUE 1 NOCACHE;

--Creando la tabla de historial de estados ECUCERT de incidencia ECUCERT
CREATE TABLE DB_SOPORTE.INFO_INCIDENCIA_DET_HIST
(
  ID_INCIDENCIA_DET_HIST INTEGER NOT NULL
, DETALLE_INCIDENCIA_ID INTEGER NOT NULL 
, ESTADO VARCHAR2(400) NOT NULL  
, USR_CREACION VARCHAR2(200) NOT NULL 
, USR_ULT_MOD VARCHAR2(200) NULL 
, FE_CREACION TIMESTAMP NOT NULL
, FE_ULT_MOD DATE NULL
, IP_CREACION VARCHAR2(200) NOT NULL
, IP_ULT_MOD VARCHAR2(200) NULL 
, CONSTRAINT INFO_INCIDENCIA_DET_HIST_PK PRIMARY KEY 
  (
    ID_INCIDENCIA_DET_HIST 
  )
  ENABLE 
);

-- Add comments to the columns 
    COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_DET_HIST.ID_INCIDENCIA_DET_HIST  is 'Campo para identificar el id secuencial de notificación de envio de correo';
    COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_DET_HIST.DETALLE_INCIDENCIA_ID  is 'Campo para identificar el id secuencial con que se crea el detale de la incidencia de ECUCERT ';
    COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_DET_HIST.ESTADO  is 'Estado de la gestión de la incidencia para su historico';
    COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_DET_HIST.USR_CREACION  is 'Campo para identificar el usuario que creo el registro';
	COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_DET_HIST.USR_ULT_MOD  is 'Campo para identificar el ultimo usuario que modifico el registro';
	COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_DET_HIST.FE_CREACION  is 'Campo para identificar la fecha de creacion del registro';
	COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_DET_HIST.FE_ULT_MOD  is 'Campo para identificar la ultima fecha que se modifico el registro';
	COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_DET_HIST.IP_CREACION  is 'Campo para identificar la ip de origen que crea el registro';
	COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_DET_HIST.IP_ULT_MOD  is 'Campo para identificar la ultima ip que modifico el registro';
  
--Creando de la secuencia de notificación de incidencia ECUCERT 
CREATE SEQUENCE DB_SOPORTE.SEQ_INFO_INCIDENCIA_DET_HIST INCREMENT BY 1 MAXVALUE 9999999999999999999999999999 MINVALUE 1 NOCACHE;

--Creando la tabla de notificación de incidencia ECUCERT
CREATE TABLE DB_SOPORTE.INFO_INCIDENCIA_REQUEST
(
  ID_INCIDENCIA_REQUEST INTEGER NOT NULL
, REQUEST CLOB NULL
, USR_CREACION VARCHAR2(200) NOT NULL 
, USR_ULT_MOD VARCHAR2(200) NULL 
, FE_CREACION DATE NOT NULL
, FE_ULT_MOD DATE NULL
, IP_CREACION VARCHAR2(200) NOT NULL
, IP_ULT_MOD VARCHAR2(200) NULL 
, CONSTRAINT INFO_INCIDENCIA_REQUEST PRIMARY KEY 
  (
    ID_INCIDENCIA_REQUEST 
  )
  ENABLE 
);

-- Add comments to the columns 
    COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_REQUEST.ID_INCIDENCIA_REQUEST  is 'Campo para identificar el id secuencial de notificación de envio de correo';
    COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_REQUEST.REQUEST  is 'Request que envia ECUCERT ';
    COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_REQUEST.USR_CREACION  is 'Campo para identificar el usuario que creo el registro';
	COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_REQUEST.USR_ULT_MOD  is 'Campo para identificar el ultimo usuario que modifico el registro';
	COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_REQUEST.FE_CREACION  is 'Campo para identificar la fecha de creacion del registro';
	COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_REQUEST.FE_ULT_MOD  is 'Campo para identificar la ultima fecha que se modifico el registro';
	COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_REQUEST.IP_CREACION  is 'Campo para identificar la ip de origen que crea el registro';
	COMMENT ON COLUMN DB_SOPORTE.INFO_INCIDENCIA_REQUEST.IP_ULT_MOD  is 'Campo para identificar la ultima ip que modifico el registro';

--Creando de la secuencia de notificación de incidencia ECUCERT 
CREATE SEQUENCE DB_SOPORTE.SEQ_INFO_INCIDENCIA_REQUEST INCREMENT BY 1 MAXVALUE 9999999999999999999999999999 MINVALUE 1 NOCACHE;  

--Creación del directorio
CREATE OR REPLACE DIRECTORY ECUCER_DIR as '/backup/repecucert';

/
--Creación del tipo de incidencia
CREATE OR REPLACE TYPE DB_SOPORTE.INCIDENCIA_TYPE AS OBJECT (
  Pv_NoTicket           VARCHAR2(800),
  Pv_FeIncidencia       VARCHAR2(800),
  Pv_Categoria          VARCHAR2(800),
  Pv_SubCategoria       VARCHAR2(800),
  Pv_Estado             VARCHAR2(800),
  Pv_Prioridad          VARCHAR2(800),
  Pv_Subject            VARCHAR2(800),
  Pv_TipoEvento         VARCHAR2(800),
  Pn_IdIncRequest       INTEGER,
  Pn_NumeroRegistro     INTEGER,
  Pv_NombreCategoria    VARCHAR2(800),
  Pv_TituloCategoria    VARCHAR2(800),
  Pv_PrioridadInfra     VARCHAR2(800),
  Pv_UsrCreacion        VARCHAR2(800),
  Pv_IpCreacion         VARCHAR2(800)
);

/
--Creación del tipo del detalle de la incidencia
CREATE OR REPLACE TYPE DB_SOPORTE.INCIDENCIA_DETALLE_TYPE AS OBJECT (
    Ln_IncidenciaId    INTEGER,
    Pv_Ip              VARCHAR2(800),
    Pv_IpWAN           VARCHAR2(800),
    Pv_Puerto          VARCHAR2(800),
    Pv_IpDestino       VARCHAR2(800),
    Pv_IpCC            VARCHAR2(800),
    Pv_Status          VARCHAR2(800),
    Pv_puertoDest      VARCHAR2(800),
    Pv_EstadoGestion   VARCHAR2(800),
    Pv_feIncidenciaIp  VARCHAR2(800),
    Pv_UsrCreacion     VARCHAR2(800),
    Pv_IpCreacion      VARCHAR2(800)
);
/
--Creación del tipo para la actualización del detalle de la incidencia
CREATE OR REPLACE TYPE DB_SOPORTE.INCIDENCIA_ACT_DETALLE_TYPE AS OBJECT (
    Pn_IncidenciaDetId     INTEGER,
    Pn_CasoId              INTEGER,
    Pn_ComunicacionId      INTEGER,
    Pv_PersonaEmpRol       VARCHAR2(800),
    Pv_TipoUsuario         VARCHAR2(800),
    Pn_IdEmpresa           INTEGER,
    Pv_UsrModi             VARCHAR2(800),
    Pv_IpModi              VARCHAR2(800),
    Pn_IdServicio          INTEGER,
    Pn_EsClieCsoc          INTEGER,
    Pn_EsClieSG            INTEGER,
    Pn_EsCPE               INTEGER
);

/
--Creación del tipo para la creación del detalle de la incidencia
CREATE OR REPLACE TYPE DB_SOPORTE.INCIDENCIA_NOT_DETALLE_TYPE AS OBJECT (
    Pv_ipAddress          VARCHAR2(800),
    Pn_IncidenciaIdDet    INTEGER,
    Pv_ipCreacion         VARCHAR2(800),
    Pv_feIncidenciaIp     VARCHAR2(800),
    Pv_user               VARCHAR2(800),
    Pv_puerto             VARCHAR2(800),
    Pv_noTicket           VARCHAR2(800),
    Pv_categoria          VARCHAR2(800),
    Pv_subCategoria       VARCHAR2(800),
    Pv_tipoEvento         VARCHAR2(800),
    Pv_ipDestino          VARCHAR2(800),
    Pv_BandCPE            VARCHAR2(800),
    Pv_statusIn           VARCHAR2(800)
);

/