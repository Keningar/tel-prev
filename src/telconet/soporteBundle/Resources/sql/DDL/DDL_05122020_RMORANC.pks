--TABLA DB_SOPORTE.ADMI_PROGRESOS_TAREA PARA EL FLUJO DE LOS PROGRESOS EN TAREAS GESTIONADAS DESDE TM OPERACIONES.
CREATE TABLE DB_SOPORTE.ADMI_PROGRESOS_TAREA 
(
  ID_PROGRESOS_TAREA NUMBER NOT NULL 
, CODIGO_TAREA VARCHAR2(20) 
, NOMBRE_TAREA VARCHAR2(200) 
, DESCRIPCION_TAREA VARCHAR2(500) 
, ESTADO VARCHAR2(100) 
, USR_CREACION VARCHAR2(20) 
, FE_CREACION DATE DEFAULT SYSDATE 
, IP_CREACION VARCHAR2(15) DEFAULT '127.0.0.1' 
, USR_ULT_MOD VARCHAR2(20) 
, FE_ULT_MOD DATE 
);

COMMENT ON COLUMN DB_SOPORTE.ADMI_PROGRESOS_TAREA.ID_PROGRESOS_TAREA IS 'Indica el id del registro';

COMMENT ON COLUMN DB_SOPORTE.ADMI_PROGRESOS_TAREA.CODIGO_TAREA IS 'Indica el codigo de la tarea';

COMMENT ON COLUMN DB_SOPORTE.ADMI_PROGRESOS_TAREA.NOMBRE_TAREA IS 'Indica el nombre de la tarea';

COMMENT ON COLUMN DB_SOPORTE.ADMI_PROGRESOS_TAREA.DESCRIPCION_TAREA IS 'Indica la descripcion de la tarea';

COMMENT ON COLUMN DB_SOPORTE.ADMI_PROGRESOS_TAREA.USR_CREACION IS 'Indica el usuario de creacion';

COMMENT ON COLUMN DB_SOPORTE.ADMI_PROGRESOS_TAREA.FE_CREACION IS 'Indica fecha de creacion del registro';

COMMENT ON COLUMN DB_SOPORTE.ADMI_PROGRESOS_TAREA.IP_CREACION IS 'Indica la ip de creacion del registro';

COMMENT ON COLUMN DB_SOPORTE.ADMI_PROGRESOS_TAREA.USR_ULT_MOD IS 'Indica el usuario que actualiza el registro';

COMMENT ON COLUMN DB_SOPORTE.ADMI_PROGRESOS_TAREA.FE_ULT_MOD IS 'Indica la fecha de actualizacion del registro';


CREATE SEQUENCE DB_SOPORTE.SEQ_ADMI_PROGRESOS_TAREA INCREMENT BY 1 START WITH 1 MAXVALUE 9999999999999999999999999999 MINVALUE 0 NOCACHE;



