/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Script para eliminar directorios en NFS donde se almacenarían archivos pendientes de migración del modulo de Soporte
 * @author David De La Cruz <ddelacruz@telconet.ec>
 * @version 1.0 22-06-2021 - Versión Inicial.
 */

begin

  delete from DB_GENERAL.ADMI_GESTION_DIRECTORIOS 
  where codigo_app = 4
  and aplicacion = 'TelcosWeb'
  and pais = '593'
  and empresa = 'TN'
  and modulo = 'Soporte'
  and submodulo = 'Varios'
  and estado = 'Activo'
  and usr_creacion = 'ddelacruz';

  delete from DB_GENERAL.ADMI_GESTION_DIRECTORIOS 
  where codigo_app = 4
  and aplicacion = 'TelcosWeb'
  and pais = '593'
  and empresa = 'MD'
  and modulo = 'Soporte'
  and submodulo = 'Varios'
  and estado = 'Activo'
  and usr_creacion = 'ddelacruz';
	
  delete from DB_GENERAL.ADMI_GESTION_DIRECTORIOS 
  where codigo_app = 4
  and aplicacion = 'TelcosWeb'
  and pais = '593'
  and empresa = 'TNP'
  and modulo = 'Soporte'
  and submodulo = 'Tareas'
  and estado = 'Activo'
  and usr_creacion = 'ddelacruz';

  delete from DB_GENERAL.ADMI_GESTION_DIRECTORIOS 
  where codigo_app = 4
  and aplicacion = 'TelcosWeb'
  and pais = '593'
  and empresa = 'TTCO'
  and modulo = 'Soporte'
  and submodulo = 'Tareas'
  and estado = 'Activo'
  and usr_creacion = 'ddelacruz';

  delete from DB_GENERAL.ADMI_GESTION_DIRECTORIOS 
  where codigo_app = 4
  and aplicacion = 'TelcosWeb'
  and pais = '593'
  and empresa = 'TN'
  and modulo = 'Soporte'
  and submodulo = 'Noticias'
  and estado = 'Activo'
  and usr_creacion = 'ddelacruz';

  delete from DB_GENERAL.ADMI_GESTION_DIRECTORIOS 
  where codigo_app = 4
  and aplicacion = 'TelcosWeb'
  and pais = '593'
  and empresa = 'MD'
  and modulo = 'Soporte'
  and submodulo = 'Noticias'
  and estado = 'Activo'
  and usr_creacion = 'ddelacruz';

  delete from DB_GENERAL.ADMI_GESTION_DIRECTORIOS 
  where codigo_app = 4
  and aplicacion = 'TelcosWeb'
  and pais = '593'
  and empresa = 'TN'
  and modulo = 'Soporte'
  and submodulo = 'Plantillas'
  and estado = 'Activo'
  and usr_creacion = 'ddelacruz';

  delete from DB_GENERAL.ADMI_GESTION_DIRECTORIOS 
  where codigo_app = 4
  and aplicacion = 'TelcosWeb'
  and pais = '593'
  and empresa = 'MD'
  and modulo = 'Soporte'
  and submodulo = 'Plantillas'
  and estado = 'Activo'
  and usr_creacion = 'ddelacruz';

  commit;

end;

/
