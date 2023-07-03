/**
 * DEBE EJECUTARSE EN DB_INFRAESTRUCTURA
 * Script para eliminar registros nuevos de tipos de elementos, modelos y elementos
 * @author Jose Daniel Giler <jdgiler@telconet.ec>
 * @version 1.0 15-02-2022 - Versión Inicial.
 */



delete from DB_INFRAESTRUCTURA.INFO_ELEMENTO where USR_CREACION  = 'jdgiler';

delete from DB_INFRAESTRUCTURA.ADMI_MODELO_ELEMENTO where USR_CREACION  = 'jdgiler';

delete from DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO where USR_CREACION  = 'jdgiler';



COMMIT;

/