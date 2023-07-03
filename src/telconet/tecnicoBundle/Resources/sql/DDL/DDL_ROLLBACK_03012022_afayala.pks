/**
 * Documentación para eliminar tablas temporales
 * Parámetros de creación en DB_INFRAESTRUCTURA.TEMP_RUTA_CAB 
 * y DB_INFRAESTRUCTURA.TEMP_RUTA_DET.
 *
 * @author Antonio Ayala <afayala@telconet.ec>
 * @version 1.0 03-01-2022
 */

--Eliminar la secuencia de la tabla TEMP_RUTA_CAB
DROP SEQUENCE DB_INFRAESTRUCTURA.SEQ_TEMP_RUTA_CAB;

--Eliminar la tabla de cabecera TEMP_RUTA_CAB
DROP TABLE DB_INFRAESTRUCTURA.TEMP_RUTA_CAB;

--Eliminar la secuencia de la tabla TEMP_RUTA_DET
DROP SEQUENCE DB_INFRAESTRUCTURA.SEQ_TEMP_RUTA_DET;

--Eliminar la tabla de detalle TEMP_RUTA_DET
DROP TABLE DB_INFRAESTRUCTURA.TEMP_RUTA_DET;
/
