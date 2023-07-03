/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Script para rollback de la creacion de parametros
 * @author Pedro Velez <psvelez@telconet.ec>
 * @version 1.0 09-11-2021 - Versión Inicial.
 */

DELETE DB_GENERAL.ADMI_PARAMETRO_DET S WHERE S.DESCRIPCION = 'CANALES_WS_FORMA_CONTACTO';

DELETE DB_GENERAL.ADMI_PARAMETRO_CAB S WHERE S.NOMBRE_PARAMETRO = 'COMERCIAL_GENERICO';

COMMIT;

/