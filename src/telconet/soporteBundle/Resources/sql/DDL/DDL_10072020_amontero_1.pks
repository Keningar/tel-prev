CREATE TABLE "DB_SOPORTE"."INFO_TAREA" 
(
   ID_INFO_TAREA NUMBER NOT NULL ENABLE,
   NUMERO VARCHAR2(20 BYTE),
   LATITUD VARCHAR2(20 BYTE),
   LONGITUD VARCHAR2(20 BYTE),
   USR_CREACION_DETALLE VARCHAR2(16 BYTE),
   DETALLE_ID_RELACIONADO  NUMBER,
   FE_CREACION_DETALLE TIMESTAMP (6),
   FE_SOLICITADA TIMESTAMP (6),
   OBSERVACION CLOB,
   DETALLE_HIPOTESIS_ID NUMBER,
   TAREA_ID  NUMBER NOT NULL ENABLE,
   NOMBRE_TAREA VARCHAR2(700 BYTE),
   DESCRIPCION_TAREA VARCHAR2(700 BYTE),
   NOMBRE_PROCESO VARCHAR2(700 BYTE),
   PROCESO_ID  NUMBER,
   ASIGNADO_ID  NUMBER,
   ASIGNADO_NOMBRE VARCHAR2(700 BYTE),
   REF_ASIGNADO_ID  NUMBER,
   REF_ASIGNADO_NOMBRE VARCHAR2(700 BYTE),
   PERSONA_EMPRESA_ROL_ID  NUMBER,
   DETALLE_ASIGNACION_ID  NUMBER NOT NULL ENABLE,
   FE_CREACION_ASIGNACION TIMESTAMP (6),
   DEPARTAMENTO_ID  NUMBER,
   TIPO_ASIGNADO VARCHAR2(20 BYTE),
   CANTON_ID  NUMBER,
   ESTADO VARCHAR2(16 BYTE),
   DETALLE_HISTORIAL_ID NUMBER NOT NULL ENABLE,
   FE_CREACION_HIS TIMESTAMP (6),
   USR_CREACION_HIS VARCHAR2(16 BYTE),
   OBSERVACION_HISTORIAL CLOB,
   DEPARTAMENTO_ORIGEN_ID  NUMBER,
   PERSONA_EMPRESA_ROL_ID_HIS NUMBER,
   ASIGNADO_ID_HIS  NUMBER,
   NUMERO_TAREA NUMBER,
   DETALLE_ID  NUMBER NOT NULL ENABLE,
   FE_CREACION TIMESTAMP (6),
   USR_CREACION VARCHAR2(35 BYTE),
   IP_CREACION VARCHAR2(20 BYTE),
   FE_ULT_MOD TIMESTAMP (6),
   USR_ULT_MOD VARCHAR2(35 BYTE),

   CONSTRAINT "INFO_TAREA_PK" PRIMARY KEY ("ID_INFO_TAREA")
   USING INDEX PCTFREE 10 INITRANS 2 MAXTRANS 255 COMPUTE STATISTICS 
   STORAGE(INITIAL 65536 NEXT 1048576 MINEXTENTS 1 MAXEXTENTS 2147483645
   PCTINCREASE 0 FREELISTS 1 FREELIST GROUPS 1 BUFFER_POOL DEFAULT FLASH_CACHE DEFAULT CELL_FLASH_CACHE DEFAULT)
   TABLESPACE "DB_TELCONET"  ENABLE,

   CONSTRAINT "INFO_TAREA_FK2" FOREIGN KEY (DETALLE_ID)
   REFERENCES DB_SOPORTE.INFO_DETALLE (ID_DETALLE) DISABLE,

   CONSTRAINT "INFO_TAREA_FK3" FOREIGN KEY (TAREA_ID)
   REFERENCES DB_SOPORTE.ADMI_TAREA (ID_TAREA) DISABLE,

   CONSTRAINT "INFO_TAREA_FK4" FOREIGN KEY (DETALLE_HIPOTESIS_ID)
   REFERENCES DB_SOPORTE.INFO_DETALLE_HIPOTESIS (ID_DETALLE_HIPOTESIS) DISABLE,

   CONSTRAINT "INFO_TAREA_FK5" FOREIGN KEY (PROCESO_ID)
   REFERENCES DB_SOPORTE.ADMI_PROCESO (ID_PROCESO) DISABLE,

   CONSTRAINT "INFO_TAREA_FK6" FOREIGN KEY (PERSONA_EMPRESA_ROL_ID)
   REFERENCES DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL (ID_PERSONA_ROL) DISABLE,

   CONSTRAINT "INFO_TAREA_FK7" FOREIGN KEY (DETALLE_ASIGNACION_ID)
   REFERENCES DB_SOPORTE.INFO_DETALLE_ASIGNACION (ID_DETALLE_ASIGNACION) DISABLE,

   CONSTRAINT "INFO_TAREA_FK8" FOREIGN KEY (DEPARTAMENTO_ID)
   REFERENCES DB_GENERAL.ADMI_DEPARTAMENTO (ID_DEPARTAMENTO) DISABLE,

   CONSTRAINT "INFO_TAREA_FK9" FOREIGN KEY (CANTON_ID)
   REFERENCES DB_GENERAL.ADMI_CANTON (ID_CANTON) DISABLE,

   CONSTRAINT "INFO_TAREA_FK10" FOREIGN KEY (DETALLE_HISTORIAL_ID)
   REFERENCES DB_SOPORTE.INFO_DETALLE_HISTORIAL (ID_DETALLE_HISTORIAL) DISABLE

)
SEGMENT CREATION IMMEDIATE 
PCTFREE 10 PCTUSED 0 INITRANS 1 MAXTRANS 255 NOCOMPRESS LOGGING
STORAGE(INITIAL 65536 NEXT 1048576 MINEXTENTS 1 MAXEXTENTS 2147483645
PCTINCREASE 0 FREELISTS 1 FREELIST GROUPS 1 BUFFER_POOL DEFAULT FLASH_CACHE DEFAULT CELL_FLASH_CACHE DEFAULT)
TABLESPACE "DB_TELCONET";


COMMENT ON COLUMN DB_SOPORTE.INFO_TAREA.ID_INFO_TAREA IS 'Id Pk de la tabla'; 
COMMENT ON COLUMN DB_SOPORTE.INFO_TAREA.OBSERVACION_HISTORIAL IS 'observación del historial'; 
COMMENT ON COLUMN DB_SOPORTE.INFO_TAREA.OBSERVACION IS 'observación de la tarea';  
COMMENT ON COLUMN DB_SOPORTE.INFO_TAREA.DEPARTAMENTO_ORIGEN_ID IS 'departamento del origen de la tarea'; 
COMMENT ON COLUMN DB_SOPORTE.INFO_TAREA.DETALLE_ID IS 'Id del detalle de la tarea'; 
COMMENT ON COLUMN DB_SOPORTE.INFO_TAREA.LATITUD IS 'Latitud para registro de tarea'; 
COMMENT ON COLUMN DB_SOPORTE.INFO_TAREA.LONGITUD IS 'Longitud para registro de tarea'; 
COMMENT ON COLUMN DB_SOPORTE.INFO_TAREA.USR_CREACION_DETALLE IS 'Id Pk de la tabla'; 
COMMENT ON COLUMN DB_SOPORTE.INFO_TAREA.DETALLE_ID_RELACIONADO IS 'Id del detalle relacionado'; 
COMMENT ON COLUMN DB_SOPORTE.INFO_TAREA.TAREA_ID IS 'Id de la admi_tarea'; 
COMMENT ON COLUMN DB_SOPORTE.INFO_TAREA.NOMBRE_TAREA IS 'nombre de la admi_tarea'; 
COMMENT ON COLUMN DB_SOPORTE.INFO_TAREA.DESCRIPCION_TAREA IS 'descripción de la admi_tarea'; 
COMMENT ON COLUMN DB_SOPORTE.INFO_TAREA.ASIGNADO_ID IS 'id del asignado a la tarea'; 
COMMENT ON COLUMN DB_SOPORTE.INFO_TAREA.ASIGNADO_NOMBRE IS 'nombre del asignado a la tarea'; 
COMMENT ON COLUMN DB_SOPORTE.INFO_TAREA.REF_ASIGNADO_ID IS 'id referencia del asignado a la tarea'; 
COMMENT ON COLUMN DB_SOPORTE.INFO_TAREA.REF_ASIGNADO_NOMBRE IS 'nombre referencia del del asignado a la tarea'; 
COMMENT ON COLUMN DB_SOPORTE.INFO_TAREA.PERSONA_EMPRESA_ROL_ID IS 'persona empresa rol id del asignado'; 
COMMENT ON COLUMN DB_SOPORTE.INFO_TAREA.DEPARTAMENTO_ID IS 'departamento de la tarea';
COMMENT ON COLUMN DB_SOPORTE.INFO_TAREA.ESTADO IS 'Estado de la tarea';
COMMENT ON COLUMN DB_SOPORTE.INFO_TAREA.USR_CREACION IS 'usuario que creo el registro';
COMMENT ON COLUMN DB_SOPORTE.INFO_TAREA.TIPO_ASIGNADO IS 'tipo asignado';
COMMENT ON COLUMN DB_SOPORTE.INFO_TAREA.FE_CREACION_DETALLE IS 'fecha creacion del detalle de la tarea';
COMMENT ON COLUMN DB_SOPORTE.INFO_TAREA.FE_SOLICITADA IS 'fecha solicitada de la tarea';
COMMENT ON COLUMN DB_SOPORTE.INFO_TAREA.FE_CREACION_ASIGNACION IS 'fecha creación de la asignación';
COMMENT ON COLUMN DB_SOPORTE.INFO_TAREA.FE_CREACION IS 'fecha creación';
COMMENT ON COLUMN DB_SOPORTE.INFO_TAREA.NUMERO_TAREA IS 'número de la tarea referencia a info_comunicacion';
COMMENT ON COLUMN DB_SOPORTE.INFO_TAREA.NOMBRE_PROCESO IS 'nombre del proceso referencia a admi_proceso';
COMMENT ON COLUMN DB_SOPORTE.INFO_TAREA.DETALLE_HISTORIAL_ID IS 'id del detalle de historial';
COMMENT ON COLUMN DB_SOPORTE.INFO_TAREA.FE_CREACION_HIS IS 'Fecha de creación de último historial';
COMMENT ON COLUMN DB_SOPORTE.INFO_TAREA.USR_CREACION_HIS IS 'Usuario de creación de último historial';
COMMENT ON COLUMN DB_SOPORTE.INFO_TAREA.OBSERVACION_HISTORIAL IS 'Observación de último historial';
COMMENT ON COLUMN DB_SOPORTE.INFO_TAREA.DEPARTAMENTO_ORIGEN_ID IS 'Departamento origen de la tarea';
COMMENT ON COLUMN DB_SOPORTE.INFO_TAREA.PERSONA_EMPRESA_ROL_ID_HIS IS 'Persona empresa rol id del historial';
COMMENT ON COLUMN DB_SOPORTE.INFO_TAREA.ASIGNADO_ID_HIS IS 'Id del asignado del historial';
COMMENT ON COLUMN DB_SOPORTE.INFO_TAREA.NUMERO_TAREA IS 'Numero de la tarea';
COMMENT ON COLUMN DB_SOPORTE.INFO_TAREA.DETALLE_ID IS 'Id del detalle de la tarea';
COMMENT ON COLUMN DB_SOPORTE.INFO_TAREA.FE_CREACION IS 'Fecha de creación del registro';
COMMENT ON COLUMN DB_SOPORTE.INFO_TAREA.USR_CREACION IS 'Usuario de creación del registro';
COMMENT ON COLUMN DB_SOPORTE.INFO_TAREA.IP_CREACION IS 'ip de creacion del registro';
COMMENT ON COLUMN DB_SOPORTE.INFO_TAREA.FE_ULT_MOD IS 'Fecha última modificación del registro';
COMMENT ON COLUMN DB_SOPORTE.INFO_TAREA.USR_ULT_MOD  IS 'Usuario última modificación del registro';
create sequence DB_SOPORTE.SEQ_INFO_TAREA
start with 1 
increment by 1 
nomaxvalue;

/
