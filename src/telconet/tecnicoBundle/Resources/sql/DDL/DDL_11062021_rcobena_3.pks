/**
 * Trigger DB_INFRAESTRUCTURA tabla ADMI_GRUPO_TAG 
 * @author Roberth Cobeña <rcobena@telconet.ec>
 * @version 1.0
 * @since 16/06/2021
 */
CREATE OR REPLACE TRIGGER DB_INFRAESTRUCTURA.ADMI_GRUPO_TAG
  BEFORE UPDATE ON DB_INFRAESTRUCTURA.ADMI_GRUPO_TAG FOR EACH ROW

BEGIN
  :NEW.USR_ULT_MOD := USER;
  :NEW.FE_ULT_MOD := SYSDATE;
END;
/