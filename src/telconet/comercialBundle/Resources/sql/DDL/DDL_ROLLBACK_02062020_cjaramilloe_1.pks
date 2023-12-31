/**
 *
 * Rollback de creación de la tabla INFO_PERSONA_REPRESENTANTE
 * 
 *
 * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
 * @version 1.0 02-06-2020
 * 
 **/
 
DROP INDEX DB_COMERCIAL.IDX_INFO_PER_REP_PERSONA;
DROP INDEX DB_COMERCIAL.IDX_INFO_PER_REP_REPRESENTANTE;
DROP INDEX DB_COMERCIAL.IDX_INFO_PER_REP_FE_CREACION;
DROP INDEX DB_COMERCIAL.IDX_INFO_PER_REP_ESTADO;

DROP SEQUENCE DB_COMERCIAL.SEQ_INFO_PERSONA_REPRESENTANTE;

DROP TABLE DB_COMERCIAL.INFO_PERSONA_REPRESENTANTE;

COMMIT;
/
