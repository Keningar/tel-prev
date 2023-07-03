--CREA TABLA DE ASIGNACIONES DE SOLICITUDES
---------------------------------------------
CREATE TABLE "DB_SOPORTE"."INFO_ASIGNACION_SOLICITUD" 
(	
    ID_ASIGNACION_SOLICITUD NUMBER  NOT NULL ENABLE,
    DEPARTAMENTO_ID NUMBER NOT NULL ENABLE,
    REFERENCIA_CLIENTE  VARCHAR2(100 BYTE),
    ORIGEN  VARCHAR2(60 BYTE) NOT NULL ENABLE,
    TIPO_ATENCION VARCHAR2(60 BYTE) NOT NULL ENABLE,
    TIPO_PROBLEMA VARCHAR2(60 BYTE) NOT NULL ENABLE,
    CRITICIDAD VARCHAR2(10 BYTE) NOT NULL ENABLE,
    NOMBRE_REPORTA VARCHAR2(100 BYTE),
    NOMBRE_SITIO VARCHAR2(100 BYTE),
    REFERENCIA_ID VARCHAR2(20),
    EMPRESA_COD VARCHAR2(2 BYTE) NOT NULL ENABLE,
    USR_ASIGNADO   VARCHAR2(50 BYTE) NOT NULL ENABLE,
    DETALLE   VARCHAR2(1500 BYTE) NOT NULL ENABLE,
    USR_CREACION   VARCHAR2(50 BYTE) NOT NULL ENABLE,
    FE_CREACION TIMESTAMP(6) NOT NULL ENABLE,
    IP_CREACION VARCHAR2(15 BYTE),
    USR_ULT_MOD VARCHAR2(50 BYTE),
    FE_ULT_MOD TIMESTAMP(6),
    CAMBIO_TURNO VARCHAR2(1 BYTE) NOT NULL ENABLE,
    ESTADO VARCHAR2(16 BYTE) NOT NULL ENABLE,
    CONSTRAINT "INFO_ASIGNACION_SOLICITUD_PK" PRIMARY KEY ("ID_ASIGNACION_SOLICITUD"),
   CONSTRAINT "INFO_ASIGNACION_SOLICITUD_FK1" FOREIGN KEY (EMPRESA_COD)
       REFERENCES DB_COMERCIAL.INFO_EMPRESA_GRUPO (COD_EMPRESA) DISABLE,
   CONSTRAINT "INFO_ASIGNACION_SOLICITUD_FK2" FOREIGN KEY (DEPARTAMENTO_ID)
       REFERENCES DB_GENERAL.ADMI_DEPARTAMENTO (ID_DEPARTAMENTO) DISABLE
);
 

COMMENT ON COLUMN DB_SOPORTE."INFO_ASIGNACION_SOLICITUD"."ID_ASIGNACION_SOLICITUD" IS 'Id Pk de la tabla';

COMMENT ON COLUMN DB_SOPORTE."INFO_ASIGNACION_SOLICITUD"."DEPARTAMENTO_ID" IS 'id del departamento al que pertenece la asignación';
 
COMMENT ON COLUMN DB_SOPORTE."INFO_ASIGNACION_SOLICITUD"."REFERENCIA_CLIENTE" IS 'Puede registrar login, cedula o ruc del cliente';
 
COMMENT ON COLUMN DB_SOPORTE."INFO_ASIGNACION_SOLICITUD"."ORIGEN" IS 'El origen de la asignación puede ser LLAMADA, CASOS PROACTIVOS, 
TAREAS INTERNAS, CORREO, WHATSAPP';

COMMENT ON COLUMN DB_SOPORTE."INFO_ASIGNACION_SOLICITUD"."TIPO_ATENCION" IS 'El tipo de atención de la asignación puede ser TAREA, CASO';

COMMENT ON COLUMN DB_SOPORTE."INFO_ASIGNACION_SOLICITUD"."TIPO_PROBLEMA" IS 'El tipo del problema de la asignación que puede ser 
CAIDA, INTERMITENCIA';

COMMENT ON COLUMN DB_SOPORTE."INFO_ASIGNACION_SOLICITUD"."CRITICIDAD" IS 'Criticidad de la asignación puede ser MEDIA, ALTA o BAJA';

COMMENT ON COLUMN DB_SOPORTE."INFO_ASIGNACION_SOLICITUD"."NOMBRE_REPORTA" IS 'Nombre de la persona que reporta el problema';

COMMENT ON COLUMN DB_SOPORTE."INFO_ASIGNACION_SOLICITUD"."NOMBRE_SITIO" IS 'nombre del sitio donde se reporta el problema';

COMMENT ON COLUMN DB_SOPORTE."INFO_ASIGNACION_SOLICITUD"."REFERENCIA_ID" IS 'id del caso o id tarea según el campo TIPO_ATENCION';

COMMENT ON COLUMN DB_SOPORTE."INFO_ASIGNACION_SOLICITUD"."EMPRESA_COD" IS 'id de la empresa';

COMMENT ON COLUMN DB_SOPORTE."INFO_ASIGNACION_SOLICITUD"."FE_CREACION" IS 'fecha creacion del registro';

COMMENT ON COLUMN DB_SOPORTE."INFO_ASIGNACION_SOLICITUD"."USR_ASIGNADO" IS 'usuario que fue asignado para que gestione la solicitud';

COMMENT ON COLUMN DB_SOPORTE."INFO_ASIGNACION_SOLICITUD"."DETALLE" IS 'detalle de la asignacion de solicitud';

COMMENT ON COLUMN DB_SOPORTE."INFO_ASIGNACION_SOLICITUD"."USR_CREACION" IS 'usuario que creo el registro';

COMMENT ON COLUMN DB_SOPORTE."INFO_ASIGNACION_SOLICITUD"."IP_CREACION" IS 'Ip de la pc desde donde fue creado el registro';

COMMENT ON COLUMN DB_SOPORTE."INFO_ASIGNACION_SOLICITUD"."CAMBIO_TURNO" IS 'Indica si la asignación esta aun pendiente en el cambio de turno, S o N';

COMMENT ON COLUMN DB_SOPORTE."INFO_ASIGNACION_SOLICITUD"."ESTADO" IS 'Estado de la asignación Pendiente, EnProceso, Finalizado, Eliminado';

COMMENT ON COLUMN DB_SOPORTE."INFO_ASIGNACION_SOLICITUD"."FE_ULT_MOD" IS 'Fecha de ultima modificacion de la tabla';

COMMENT ON COLUMN DB_SOPORTE."INFO_ASIGNACION_SOLICITUD"."USR_ULT_MOD" IS 'Usuario de ultima modificacion de la tabla';
 
COMMENT ON TABLE DB_SOPORTE."INFO_ASIGNACION_SOLICITUD"  IS 'TABLA EN LA QUE SE REGISTRA LAS ASIGNACIONES DE SOLICITUDES 
DE CLIENTES O INTERNOS QUE SON RECEPTADAS POR ADMINISTRADORES DEL DEPARTAMENTO DE SOPORTE (SOLICITADO POR IPCCL1)';


create sequence DB_SOPORTE.SEQ_INFO_ASIGNACION_SOLICITUD
start with 1 
increment by 1 
nomaxvalue;