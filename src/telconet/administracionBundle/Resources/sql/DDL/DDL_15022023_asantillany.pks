/**
 * DEBE EJECUTARSE EN DB_INFRAESTRUCTURA
 * Nuevas tablas temporales para almacenar informacion de un archivo csv
 * @author Anthony Santillan <asantillany@telconet.ec>
 * @version 1.0 14-02-2023.
 */

CREATE TABLE DB_INFRAESTRUCTURA.TEMP_HILO_DET (
    ID_HILO_DET NUMBER,
    HILO_ID_CAB NUMBER,
    ELEMENTO_INTERMEDIO VARCHAR2(100),
    CASSETE VARCHAR2(100),
    LOGIN VARCHAR2(100),
    PUERTO_INICIO VARCHAR2(20),
    PUERTO_FIN VARCHAR2(20),
    PUERTO_INI_ODF VARCHAR2(20),
    PUERTO_FIN_ODF VARCHAR2(20),
    NOMBRE_ODF VARCHAR2(20),
    PUERTO_EQUIPO VARCHAR2(100),
    EQUIPO VARCHAR2(100),
    HILO_FIN VARCHAR2(100),
    COLOR_BUFFER_FIN VARCHAR2(100),
    COLOR_HILO_FIN VARCHAR2(100),
    NOMBRE_RUTA VARCHAR2(100),
    TIPO_RUTA VARCHAR2(100),
    TIPO_FIBRA VARCHAR2(100),
    MODELO_ELEMENTO_CONTENEDOR VARCHAR2(100),
    MODELO_ELEMENTO_CONTENIDO VARCHAR2(100),
    JURISDICCION VARCHAR2(100),
    CANTON VARCHAR2(100),
    PARROQUIA VARCHAR2(100),
    DIRECCION VARCHAR2(100),
    ALTURA_NIVEL_MAR VARCHAR2(100),
    COORDENADAS_ALTITUD VARCHAR2(100),
    COORDENADAS_LONGITUD VARCHAR2(100),
    UBICADO_EN VARCHAR2(100),
    FACTIBILIDAD VARCHAR2(100),
    HILO_DET_ID NUMBER
);

-- DB_INFRAESTRUCTURA.TEMP_HILO_CAB definition
CREATE TABLE DB_INFRAESTRUCTURA.TEMP_HILO_CAB (
    ID_HILO_CAB NUMBER,
    HILO_INI VARCHAR2(100),
    COLOR_BUFFER_INI VARCHAR2(100),
    COLOR_HILO_INI VARCHAR2(100),
    EQUIPO_INI VARCHAR2(100),
    PUERTO_EQUIPO_INI VARCHAR2(100),
    NOMBRE_ODF_INI VARCHAR2(100),
    PUERTO_ODFE_INI VARCHAR2(100),
    PUERTO_ODFE_FIN VARCHAR2(100),
    PUERTO_ODFS_FIN VARCHAR2(100),
    PUERTO_ODFS_INI VARCHAR2(100),
    NOMBRE_ODF_FIN VARCHAR2(100),
    PUERTO_EQUIPO_FIN VARCHAR2(100),
    EQUIPO_FIN VARCHAR2(100),
    HILO_FIN VARCHAR2(100),
    COLOR_BUFFER_FIN VARCHAR2(100),
    COLOR_HILO_FIN VARCHAR2(100),
    NOMBRE_RUTA VARCHAR2(100),
    TIPO_RUTA VARCHAR2(100),
    TIPO_FIBRA VARCHAR2(100)
);

--Creo la secuencia para la tabla TEMP_HILO_CAB
CREATE SEQUENCE "DB_INFRAESTRUCTURA"."SEQ_TEMP_HILO_CAB" INCREMENT BY 1 MAXVALUE 9999999999999999999999999999 MINVALUE 1 CACHE 20;

--Creo la secuencia para la tabla TEMP_HILO_DET
CREATE SEQUENCE "DB_INFRAESTRUCTURA"."SEQ_TEMP_HILO_DET" INCREMENT BY 1 MAXVALUE 9999999999999999999999999999 MINVALUE 1 CACHE 20;

/**
 * DEBE EJECUTARSE EN DB_INFRAESTRUCTURA
 * Nueva tabla para conexion de enlaces con servicios 
 * @author Anthony Santillan <asantillany@telconet.ec>
 * @version 1.0 14-02-2023.
 */


-- DB_INFRAESTRUCTURA.INFO_ENLACE_SERVICIO_BACKBONE definition
CREATE TABLE DB_INFRAESTRUCTURA.INFO_ENLACE_SERVICIO_BACKBONE (
    ID_ENLACE_SERVICIO_BACKCBONE NUMBER PRIMARY KEY,
    ENLACE_ID NUMBER,
    SERVICIO_ID NUMBER,
    LOGIN_AUX VARCHAR2(100),
    ESTADO VARCHAR2(16),
    TIPO_RUTA VARCHAR2(100),
    USR_CREACION VARCHAR2(100) NOT NULL ENABLE,
    USR_MODIFICACION VARCHAR2(100),
    FE_CREACION TIMESTAMP (6) DEFAULT CURRENT_TIMESTAMP NOT NULL ENABLE,
    IP_CREACION VARCHAR2(16) NOT NULL ENABLE
);

--Creo la secuencia para la tabla TEMP_HILO_CAB
CREATE SEQUENCE "DB_INFRAESTRUCTURA"."SEQ_INFO_ENLACE_SERVICIO" INCREMENT BY 1 MAXVALUE 9999999999999999999999999999 MINVALUE 1 CACHE 20;

COMMIT;