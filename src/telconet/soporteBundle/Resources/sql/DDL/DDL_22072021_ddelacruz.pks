/**
 * DEBE EJECUTARSE EN DB_SOPORTE
 * Script para crear tabla y secuencia para registrar los puntos afectados por tareas creadas en 
 * Sistemas externos a Telcos, como Sisred
 * @author David De La Cruz <ddelacruz@telconet.ec>
 * @version 1.0 22-07-2021 - Versión Inicial.
 */

CREATE TABLE DB_SOPORTE.INFO_ACTIV_PUNTO_AFECTADO
(	ID_ACTIV_PUNTO_AFECTADO NUMBER, 
	ACTIVIDAD_ID NUMBER, 
	TITULO_ACTIVIDAD VARCHAR2(700) NOT NULL, 
	MOTIVO_ACTIVIDAD CLOB,
	PUNTO_ID NUMBER NOT NULL, 
	LOGIN_AFECTADO VARCHAR2(60) NOT NULL, 
	DESCRIPCION_AFECTADO VARCHAR2(250),
	RESPONSABLE VARCHAR2(250),
	AREA_RESPONSABLE VARCHAR2(150),
	CONTACTO_NOTIFICACION VARCHAR2(500),
	NOTIFICADO VARCHAR2(1) DEFAULT 'N',
	FECHA_NOTIFICACION TIMESTAMP (6),
	ASUNTO_NOTIFICACION	VARCHAR2(500),
	NOTIFICACION CLOB,
	TIPO_AFECTACION	VARCHAR2(50),
	SERVICIOS_AFECTADOS VARCHAR2(50),
	ORIGEN_ACTIVIDAD VARCHAR2(30),
	FE_INI_ACTIVIDAD TIMESTAMP (6) NOT NULL, 
	FE_FIN_ACTIVIDAD TIMESTAMP (6), 
	ESTADO VARCHAR2(16) NOT NULL,
	USR_CREACION VARCHAR2(100) NOT NULL, 
	FE_CREACION TIMESTAMP (6) DEFAULT CURRENT_TIMESTAMP NOT NULL, 
	IP_CREACION VARCHAR2(16) NOT NULL,
	USR_ULT_MOD VARCHAR2(100) , 
	FE_ULT_MOD TIMESTAMP (6) , 
	IP_ULT_MOD VARCHAR2(16 BYTE) 	
) ;

COMMENT ON COLUMN DB_SOPORTE.INFO_ACTIV_PUNTO_AFECTADO.ID_ACTIV_PUNTO_AFECTADO IS 'Identificador secuencial del registro del punto afectado por la actividad';
COMMENT ON COLUMN DB_SOPORTE.INFO_ACTIV_PUNTO_AFECTADO.ACTIVIDAD_ID IS 'Identificador de la actividad que afecta al punto';
COMMENT ON COLUMN DB_SOPORTE.INFO_ACTIV_PUNTO_AFECTADO.TITULO_ACTIVIDAD IS 'Titulo de la actividad que afecta al punto';
COMMENT ON COLUMN DB_SOPORTE.INFO_ACTIV_PUNTO_AFECTADO.MOTIVO_ACTIVIDAD IS 'Motivo de la actividad que afecta al punto';
COMMENT ON COLUMN DB_SOPORTE.INFO_ACTIV_PUNTO_AFECTADO.PUNTO_ID IS 'Id del punto afectado por la actividad';
COMMENT ON COLUMN DB_SOPORTE.INFO_ACTIV_PUNTO_AFECTADO.LOGIN_AFECTADO IS 'Login del punto afectado por la actividad';
COMMENT ON COLUMN DB_SOPORTE.INFO_ACTIV_PUNTO_AFECTADO.DESCRIPCION_AFECTADO IS 'Descripcion del punto afectado por la actividad';
COMMENT ON COLUMN DB_SOPORTE.INFO_ACTIV_PUNTO_AFECTADO.RESPONSABLE IS 'Responsable de la actividad';
COMMENT ON COLUMN DB_SOPORTE.INFO_ACTIV_PUNTO_AFECTADO.AREA_RESPONSABLE IS 'Area del Responsable de la actividad';
COMMENT ON COLUMN DB_SOPORTE.INFO_ACTIV_PUNTO_AFECTADO.CONTACTO_NOTIFICACION IS 'Contacto(s) para notificacion del punto afectado por la actividad';
COMMENT ON COLUMN DB_SOPORTE.INFO_ACTIV_PUNTO_AFECTADO.NOTIFICADO IS 'Indica si se ha notificado al punto afectado por la actividad: S = Si, N = No';
COMMENT ON COLUMN DB_SOPORTE.INFO_ACTIV_PUNTO_AFECTADO.FECHA_NOTIFICACION IS 'Fecha en que se realizo la notificacion al cliente';
COMMENT ON COLUMN DB_SOPORTE.INFO_ACTIV_PUNTO_AFECTADO.ASUNTO_NOTIFICACION IS 'Asunto de la notificacion enviada al cliente';
COMMENT ON COLUMN DB_SOPORTE.INFO_ACTIV_PUNTO_AFECTADO.NOTIFICACION IS 'Contenido de la notificacion enviada al cliente';
COMMENT ON COLUMN DB_SOPORTE.INFO_ACTIV_PUNTO_AFECTADO.TIPO_AFECTACION IS 'Tipo de Afectacion que implica la actividad';
COMMENT ON COLUMN DB_SOPORTE.INFO_ACTIV_PUNTO_AFECTADO.SERVICIOS_AFECTADOS IS 'Clases de servicios que se afectan por la actividad';
COMMENT ON COLUMN DB_SOPORTE.INFO_ACTIV_PUNTO_AFECTADO.ORIGEN_ACTIVIDAD IS 'Sistema origen de la actividad: Telcos, Sisred, etc.';
COMMENT ON COLUMN DB_SOPORTE.INFO_ACTIV_PUNTO_AFECTADO.FE_INI_ACTIVIDAD IS 'Fecha inicio de la actividad';
COMMENT ON COLUMN DB_SOPORTE.INFO_ACTIV_PUNTO_AFECTADO.FE_FIN_ACTIVIDAD IS 'Fecha fin de la actividad';
COMMENT ON COLUMN DB_SOPORTE.INFO_ACTIV_PUNTO_AFECTADO.ESTADO IS 'Estado de la actividad';
COMMENT ON COLUMN DB_SOPORTE.INFO_ACTIV_PUNTO_AFECTADO.USR_CREACION IS 'Usuario de creacion del registro';
COMMENT ON COLUMN DB_SOPORTE.INFO_ACTIV_PUNTO_AFECTADO.FE_CREACION IS 'Fecha de creacion del registro';
COMMENT ON COLUMN DB_SOPORTE.INFO_ACTIV_PUNTO_AFECTADO.IP_CREACION IS 'Ip de creacion del registro';
COMMENT ON COLUMN DB_SOPORTE.INFO_ACTIV_PUNTO_AFECTADO.USR_ULT_MOD IS 'Usuario ultima modificacion del registro';
COMMENT ON COLUMN DB_SOPORTE.INFO_ACTIV_PUNTO_AFECTADO.FE_ULT_MOD IS 'Fecha ultima modificacion del registro';
COMMENT ON COLUMN DB_SOPORTE.INFO_ACTIV_PUNTO_AFECTADO.IP_ULT_MOD IS 'Ip ultima modificacion del registro';

CREATE UNIQUE INDEX DB_SOPORTE.ID_ACTIV_PUNTO_AFECTADO_PK ON DB_SOPORTE.INFO_ACTIV_PUNTO_AFECTADO (ID_ACTIV_PUNTO_AFECTADO) ;

CREATE INDEX DB_SOPORTE.ID_ACTIV_PUNTO_AFECTADO_IX_1 ON DB_SOPORTE.INFO_ACTIV_PUNTO_AFECTADO (LOGIN_AFECTADO) ;

CREATE INDEX DB_SOPORTE.ID_ACTIV_PUNTO_AFECTADO_IX_2 ON DB_SOPORTE.INFO_ACTIV_PUNTO_AFECTADO (ACTIVIDAD_ID) ;

CREATE INDEX DB_SOPORTE.ID_ACTIV_PUNTO_AFECTADO_IX_3 ON DB_SOPORTE.INFO_ACTIV_PUNTO_AFECTADO (FE_INI_ACTIVIDAD,FE_FIN_ACTIVIDAD) ;

CREATE SEQUENCE DB_SOPORTE.SEQ_INFO_ACTIV_PUNTO_AFECTADO INCREMENT BY 1 MAXVALUE 9999999999999999999999999999 MINVALUE 1 NOCACHE;