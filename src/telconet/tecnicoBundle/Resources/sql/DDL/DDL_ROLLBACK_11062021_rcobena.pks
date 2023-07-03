/**
 * Rollback para tabla ADMI_GRUPO_TAG 
 * @author Roberth Cobeña <rcobena@telconet.ec>
 * @version 1.0
 * @since 16/06/2021
 */
--
DROP TRIGGER DB_INFRAESTRUCTURA.ADMI_GRUPO_TAG;
--
DROP SEQUENCE DB_INFRAESTRUCTURA.SEQ_ADMI_GRUPO_TAG;
--
DROP TABLE DB_INFRAESTRUCTURA.ADMI_GRUPO_TAG;
--
COMMIT;
/