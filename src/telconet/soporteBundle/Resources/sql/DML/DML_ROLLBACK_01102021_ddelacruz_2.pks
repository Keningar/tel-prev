/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Script para eliminar directorio en NFS donde se almacenarían archivos de Portal Cautivo
 * @author David De La Cruz <ddelacruz@telconet.ec>
 * @version 1.0
 * @since 27-09-2021
 */

begin

  delete from DB_GENERAL.ADMI_GESTION_DIRECTORIOS 
  where aplicacion = 'Extranet'
  and pais = '593'
  and empresa = 'TN'
  and modulo = 'General'
  and submodulo = 'Archivos'
  and estado = 'Activo'
  and usr_creacion = 'ddelacruz';

  commit;

end;

/
