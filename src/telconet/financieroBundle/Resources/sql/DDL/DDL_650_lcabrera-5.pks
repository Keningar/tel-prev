  /**
    * Script para agregar la columna PROCESO que lo identifica.
    * DEBE EJECUTARSE PARA EL USUARIO DB_COMPROBANTES.
    * @author Jorge Guerrero <jguerrerop@telconet.ec>
    * @version 1.0 31-12-2017 - Versión inicial
    */
    ALTER TABLE DB_COMPROBANTES.INFO_DOCUMENTO
    ADD PROCESO VARCHAR2(50);