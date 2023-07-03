/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Script para crear directorios en NFS donde se almacenarán archivos pendientes de migración del modulo de Soporte
 * @author David De La Cruz <ddelacruz@telconet.ec>
 * @version 1.0 22-06-2021 - Versión Inicial.
 */

declare

ln_codigo_path number;

begin

	select max(codigo_path) max_codigo_path 
	into ln_codigo_path
	from db_general.admi_gestion_directorios s where s.codigo_app = 4;

	insert into DB_GENERAL.ADMI_GESTION_DIRECTORIOS (id_gestion_directorio,codigo_app,codigo_path,aplicacion,pais,empresa,modulo,submodulo,
	estado,fe_creacion,fe_ult_mod,usr_creacion,usr_ult_mod)
	values(db_general.seq_admi_gestion_directorios.nextval,4,ln_codigo_path+1,'TelcosWeb','593','TN','Soporte','Varios',
	'Activo',sysdate,null,'ddelacruz',null);

	insert into DB_GENERAL.ADMI_GESTION_DIRECTORIOS (id_gestion_directorio,codigo_app,codigo_path,aplicacion,pais,empresa,modulo,submodulo,
	estado,fe_creacion,fe_ult_mod,usr_creacion,usr_ult_mod)
	values(db_general.seq_admi_gestion_directorios.nextval,4,ln_codigo_path+2,'TelcosWeb','593','MD','Soporte','Varios',
	'Activo',sysdate,null,'ddelacruz',null);

	insert into DB_GENERAL.ADMI_GESTION_DIRECTORIOS (id_gestion_directorio,codigo_app,codigo_path,aplicacion,pais,empresa,modulo,submodulo,
	estado,fe_creacion,fe_ult_mod,usr_creacion,usr_ult_mod)
	values(db_general.seq_admi_gestion_directorios.nextval,4,ln_codigo_path+3,'TelcosWeb','593','TNP','Soporte','Tareas',
	'Activo',sysdate,null,'ddelacruz',null);

	insert into DB_GENERAL.ADMI_GESTION_DIRECTORIOS (id_gestion_directorio,codigo_app,codigo_path,aplicacion,pais,empresa,modulo,submodulo,
	estado,fe_creacion,fe_ult_mod,usr_creacion,usr_ult_mod)
	values(db_general.seq_admi_gestion_directorios.nextval,4,ln_codigo_path+4,'TelcosWeb','593','TTCO','Soporte','Tareas',
	'Activo',sysdate,null,'ddelacruz',null);

	insert into DB_GENERAL.ADMI_GESTION_DIRECTORIOS (id_gestion_directorio,codigo_app,codigo_path,aplicacion,pais,empresa,modulo,submodulo,
	estado,fe_creacion,fe_ult_mod,usr_creacion,usr_ult_mod)
	values(db_general.seq_admi_gestion_directorios.nextval,4,ln_codigo_path+5,'TelcosWeb','593','TN','Soporte','Noticias',
	'Activo',sysdate,null,'ddelacruz',null);

	insert into DB_GENERAL.ADMI_GESTION_DIRECTORIOS (id_gestion_directorio,codigo_app,codigo_path,aplicacion,pais,empresa,modulo,submodulo,
	estado,fe_creacion,fe_ult_mod,usr_creacion,usr_ult_mod)
	values(db_general.seq_admi_gestion_directorios.nextval,4,ln_codigo_path+6,'TelcosWeb','593','MD','Soporte','Noticias',
	'Activo',sysdate,null,'ddelacruz',null);

	insert into DB_GENERAL.ADMI_GESTION_DIRECTORIOS (id_gestion_directorio,codigo_app,codigo_path,aplicacion,pais,empresa,modulo,submodulo,
	estado,fe_creacion,fe_ult_mod,usr_creacion,usr_ult_mod)
	values(db_general.seq_admi_gestion_directorios.nextval,4,ln_codigo_path+7,'TelcosWeb','593','TN','Soporte','Plantillas',
	'Activo',sysdate,null,'ddelacruz',null);

	insert into DB_GENERAL.ADMI_GESTION_DIRECTORIOS (id_gestion_directorio,codigo_app,codigo_path,aplicacion,pais,empresa,modulo,submodulo,
	estado,fe_creacion,fe_ult_mod,usr_creacion,usr_ult_mod)
	values(db_general.seq_admi_gestion_directorios.nextval,4,ln_codigo_path+8,'TelcosWeb','593','MD','Soporte','Plantillas',
	'Activo',sysdate,null,'ddelacruz',null);

	commit;

end;

/
