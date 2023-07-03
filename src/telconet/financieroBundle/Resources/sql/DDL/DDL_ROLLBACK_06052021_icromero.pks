 /**
    * Script para la eliminacion de los campos para el flujo nuevo de Debitos Planificados
    * DEBE EJECUTARSE PARA EL USUARIO DB_FINANCIERO.
    * @author Ivan Romero <icromero@telconet.ec>
    * @version 1.0 06-05-2021 - Versión inicial
    */

-- elimina campo PLANIFICADO a DB_FINANCIERO.INFO_DEBITO_GENERAL
alter table DB_FINANCIERO.INFO_DEBITO_GENERAL DROP PLANIFICADO;

-- elimina campo FE_PLANIFICADO a DB_FINANCIERO.INFO_DEBITO_GENERAL
alter table DB_FINANCIERO.INFO_DEBITO_GENERAL DROP FE_PLANIFICADO;

-- elimina campo PARAMETROS_PLANIFICADO a DB_FINANCIERO.INFO_DEBITO_GENERAL
alter table DB_FINANCIERO.INFO_DEBITO_GENERAL DROP PARAMETROS_PLANIFICADO;

commit;
