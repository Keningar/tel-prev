/**
 * DEBE EJECUTARSE EN DB_INFRAESTRUCTURA
 * ROLLBACK  de tablas temporales
 * @author Anthony Santillan <asantillany@telconet.ec>
 * @version 1.0 14-02-2023.
 */

 DROP TABLE DB_INFRAESTRUCTURA.TEMP_HILO_DET;
 DROP TABLE DB_INFRAESTRUCTURA.TEMP_HILO_CAB;
 DROP TABLE DB_INFRAESTRUCTURA.INFO_ENLACE_SERVICIO_BACKBONE;

/**
 * DEBE EJECUTARSE EN DB_INFRAESTRUCTURA
 * ROLLBACK  de secuencias
 * @author Anthony Santillan <asantillany@telconet.ec>
 * @version 1.0 14-02-2023.
 */

 DROP SEQUENCE SEQ_TEMP_HILO_CAB;
 DROP SEQUENCE SEQ_TEMP_HILO_DET;
 DROP SEQUENCE SEQ_INFO_ENLACE_SERVICIO;