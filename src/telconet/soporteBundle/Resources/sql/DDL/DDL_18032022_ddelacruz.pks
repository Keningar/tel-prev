/**
 * DEBE EJECUTARSE EN DB_SOPORTE
 * Script para crear tabla y secuencia para respaldar los servicios afectados por un caso, al ser actualizados o eliminados
 * de la estructura principal DB_SOPORTE.INFO_PARTE_AFECTADA
 * @author David De La Cruz <ddelacruz@telconet.ec>
 * @version 1.0 18-03-2022 - Versión Inicial.
 */

CREATE TABLE DB_SOPORTE.INFO_PARTE_AFECTADA_HIST
(
    ID_PARTE_AFECTADA_HIST NUMBER, 
    CRITERIO_AFECTADO_ID NUMBER NOT NULL, 
    DETALLE_ID NUMBER, 
    AFECTADO_ID NUMBER NOT NULL, 
    TIPO_AFECTADO VARCHAR2(20) NOT NULL, 
    AFECTADO_NOMBRE VARCHAR2(80) NOT NULL, 
    AFECTADO_DESCRIPCION VARCHAR2(200), 
    FE_INI_INCIDENCIA TIMESTAMP (6) NOT NULL, 
    FE_FIN_INCIDENCIA TIMESTAMP (6), 
    USR_CREACION VARCHAR2(100 BYTE) NOT NULL, 
    FE_CREACION TIMESTAMP (6) DEFAULT CURRENT_TIMESTAMP NOT NULL, 
    IP_CREACION VARCHAR2(16 BYTE) NOT NULL, 
    AFECTADO_DESCRIPCION_ID NUMBER, 
    ESTADO VARCHAR2(16) NOT NULL,
    USR_ULT_MOD VARCHAR2(100) , 
    FE_ULT_MOD TIMESTAMP (6) , 
    IP_ULT_MOD VARCHAR2(16 BYTE) 	
) ;

COMMENT ON COLUMN DB_SOPORTE.INFO_PARTE_AFECTADA_HIST.ID_PARTE_AFECTADA_HIST IS 'Almacena el identificador secuencial del registro de la parte afectada hist.';
COMMENT ON COLUMN DB_SOPORTE.INFO_PARTE_AFECTADA_HIST.CRITERIO_AFECTADO_ID IS 'Almacena el identificador del criterio de afectacion, referente de la tabla INFO_CRITERIO_AFECTADO';
COMMENT ON COLUMN DB_SOPORTE.INFO_PARTE_AFECTADA_HIST.DETALLE_ID IS 'Almacena el identificador del detalle, referente de la tabla INFO_DETALLE';
COMMENT ON COLUMN DB_SOPORTE.INFO_PARTE_AFECTADA_HIST.AFECTADO_ID IS 'Almacena el identificador del cliente afectado';
COMMENT ON COLUMN DB_SOPORTE.INFO_PARTE_AFECTADA_HIST.TIPO_AFECTADO IS 'Almacena si el afectado es un cliente o un elemento.(CLIENTE - ELEMENTO)';
COMMENT ON COLUMN DB_SOPORTE.INFO_PARTE_AFECTADA_HIST.AFECTADO_NOMBRE IS 'Almacena el nombre del cliente afectado';
COMMENT ON COLUMN DB_SOPORTE.INFO_PARTE_AFECTADA_HIST.AFECTADO_DESCRIPCION IS 'Almacena una descripcion adicional del cliente afectado';
COMMENT ON COLUMN DB_SOPORTE.INFO_PARTE_AFECTADA_HIST.FE_INI_INCIDENCIA IS 'Alamacena la fecha y hora de inicio de la incidencia reportada sobre el elemento.';
COMMENT ON COLUMN DB_SOPORTE.INFO_PARTE_AFECTADA_HIST.FE_FIN_INCIDENCIA IS 'Almacena la fecha y hora de finalizacion de la incidencia sobre el cliente.';
COMMENT ON COLUMN DB_SOPORTE.INFO_PARTE_AFECTADA_HIST.USR_CREACION IS 'Almacena el nombre de usuario que realizo la creacion del registro.';
COMMENT ON COLUMN DB_SOPORTE.INFO_PARTE_AFECTADA_HIST.FE_CREACION IS 'Almacena la fecha en que se realizo la creacion del registro.';
COMMENT ON COLUMN DB_SOPORTE.INFO_PARTE_AFECTADA_HIST.IP_CREACION IS 'Almacena la IP desde la que se realizo la creacion del registro.';
COMMENT ON COLUMN DB_SOPORTE.INFO_PARTE_AFECTADA_HIST.AFECTADO_DESCRIPCION_ID IS 'Almacena el id descripcion adicional del cliente afectado';
COMMENT ON COLUMN DB_SOPORTE.INFO_PARTE_AFECTADA_HIST.ESTADO IS 'Almacena el estado del registro en la tabla principal INFO_PARTE_AFECTADA';
COMMENT ON COLUMN DB_SOPORTE.INFO_PARTE_AFECTADA_HIST.USR_ULT_MOD IS 'Almacena el usuario que realizó la eliminación o actualización del registro en la tabla principal INFO_PARTE_AFECTADA';
COMMENT ON COLUMN DB_SOPORTE.INFO_PARTE_AFECTADA_HIST.FE_ULT_MOD IS 'Almacena la fecha en la que se realizó la eliminación o actualización del registro en la tabla principal INFO_PARTE_AFECTADA';
COMMENT ON COLUMN DB_SOPORTE.INFO_PARTE_AFECTADA_HIST.IP_ULT_MOD IS 'Almacena la ip de donde se realizó la eliminación o actualización del registro en la tabla principal INFO_PARTE_AFECTADA';
COMMENT ON TABLE DB_SOPORTE.INFO_PARTE_AFECTADA_HIST  IS 'Estructura para respaldar los clientes afectados por algún síntoma de un caso que hayan sido actualizados o eliminados de la tabla principal INFO_PARTE_AFECTADA';

CREATE UNIQUE INDEX DB_SOPORTE.INFO_PARTE_AFECTADA_HIST_PK ON DB_SOPORTE.INFO_PARTE_AFECTADA_HIST (ID_PARTE_AFECTADA_HIST) ;

CREATE INDEX DB_SOPORTE.INFO_PARTE_AFECTADA_HIS_IX_1 ON DB_SOPORTE.INFO_PARTE_AFECTADA_HIST (DETALLE_ID) ;
CREATE INDEX DB_SOPORTE.INFO_PARTE_AFECTADA_HIS_IX_2 ON DB_SOPORTE.INFO_PARTE_AFECTADA_HIST (CRITERIO_AFECTADO_ID, DETALLE_ID) ;

CREATE SEQUENCE DB_SOPORTE.SEQ_INFO_PARTE_AFECTADA_HIST INCREMENT BY 1 MAXVALUE 9999999999999999999999999999 MINVALUE 1 NOCACHE;

ALTER TABLE DB_SOPORTE.INFO_TAREA_SEGUIMIENTO MODIFY USR_CREACION VARCHAR2(75);
