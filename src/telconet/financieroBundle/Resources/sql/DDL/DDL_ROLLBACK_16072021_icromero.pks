  /**
    * Script para cambiar el tamaño del nombre del banco ya que existen nombres con un tamanio superior a 80
    * DEBE EJECUTARSE PARA EL USUARIO DB_GENERAL.
    * @author Ivan Romero <icromero@telconet.ec>
    * @version 1.0 16-07-2021 - Versión inicial
    */

-- modifica tamanio del campo DESCRIPCION_BANCO a  DB_GENERAL.ADMI_BANCO
alter table DB_GENERAL.ADMI_BANCO MODIFY DESCRIPCION_BANCO  varchar2(60);
commit;