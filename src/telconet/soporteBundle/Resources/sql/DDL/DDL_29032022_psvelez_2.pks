/**
 * DEBE EJECUTARSE EN DB_SOPORTE
 * @author Pedro Velez <psvelez@telconet.ec>
 * @version 1.0
 * @since 29-03-2021
 * Script para crear tabla  DB_SOPORTE.INFO_MANTENIMIENTO_PROGRAMADO
 */
CREATE TABLE DB_SOPORTE.INFO_MANTENIMIENTO_PROGRAMADO
( ID_MANTENIMIENTO_PROGRAMADO NUMBER NOT NULL,
  CASO_ID           NUMBER,
  COD_EMPRESA       VARCHAR2(2),
  FECHA_INICIO      DATE,
  FECHA_FIN         DATE,
  TIEMPO_AFECTACION VARCHAR2(5),
  TIPO_AFECTACION   VARCHAR2(20),
  TIPO_NOTIFICACION VARCHAR2(20),
  USR_CREACION      VARCHAR2(50),
  FE_CREACION       DATE,
  IP_CREACION       VARCHAR2(20),
  CONSTRAINT MANTENIMIENTO_PROGRAMADO_PK PRIMARY KEY 
  (
    ID_MANTENIMIENTO_PROGRAMADO 
  )
  ENABLE
);

COMMENT ON COLUMN DB_SOPORTE.INFO_MANTENIMIENTO_PROGRAMADO.ID_MANTENIMIENTO_PROGRAMADO IS 'Secuencia mantenimiento programado';
COMMENT ON COLUMN DB_SOPORTE.INFO_MANTENIMIENTO_PROGRAMADO.CASO_ID IS 'Id del caso';
COMMENT ON COLUMN DB_SOPORTE.INFO_MANTENIMIENTO_PROGRAMADO.COD_EMPRESA IS 'Codigo de empresa';
COMMENT ON COLUMN DB_SOPORTE.INFO_MANTENIMIENTO_PROGRAMADO.FECHA_INICIO IS 'Fecha y hora de inicio para el mantenimiento programado';
COMMENT ON COLUMN DB_SOPORTE.INFO_MANTENIMIENTO_PROGRAMADO.FECHA_FIN IS 'Fecha y hora fin para el mantenimiento programado';
COMMENT ON COLUMN DB_SOPORTE.INFO_MANTENIMIENTO_PROGRAMADO.TIEMPO_AFECTACION IS 'Tiempo en horas del mantenimiento programado';
COMMENT ON COLUMN DB_SOPORTE.INFO_MANTENIMIENTO_PROGRAMADO.TIPO_AFECTACION IS 'Tipo de afectacion del mantenimiento programado';
COMMENT ON COLUMN DB_SOPORTE.INFO_MANTENIMIENTO_PROGRAMADO.TIPO_NOTIFICACION IS 'Tipo de notificacion del mantenimiento programado';
COMMENT ON COLUMN DB_SOPORTE.INFO_MANTENIMIENTO_PROGRAMADO.USR_CREACION IS 'Usuario de creacion del registro';
COMMENT ON COLUMN DB_SOPORTE.INFO_MANTENIMIENTO_PROGRAMADO.FE_CREACION IS 'Fecha de creacion del registro';
COMMENT ON COLUMN DB_SOPORTE.INFO_MANTENIMIENTO_PROGRAMADO.IP_CREACION IS 'IP de creacion del registro';


CREATE SEQUENCE DB_SOPORTE.SEQ_INFO_MANT_PROGRAMADO INCREMENT BY 1 MAXVALUE 9999999999999999999999999999 MINVALUE 1 NOCACHE;

/