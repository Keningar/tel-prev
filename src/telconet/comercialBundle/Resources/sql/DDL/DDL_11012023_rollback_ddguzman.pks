/**
 *
 * Rollback de creación de la tabla INFO_COORDINADOR_TURNO
 * 
 * @author Daniel Guzmán <ddguzman@telconet.ec>
 * @version 1.0
 * @since 11-01-2023
 * 
 **/

DROP SEQUENCE DB_COMERCIAL.SEQ_INFO_COORDINADOR_TURNO;

DROP TABLE DB_COMERCIAL.INFO_COORDINADOR_TURNO;

COMMIT;
/