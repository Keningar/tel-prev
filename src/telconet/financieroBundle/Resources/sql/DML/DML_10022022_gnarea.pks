insert into db_general.admi_gestion_directorios 
(
  id_gestion_directorio, codigo_app, codigo_path, 
  aplicacion, pais, empresa, 
  modulo, submodulo, estado, 
  fe_creacion, usr_creacion)
values
(
  db_general.seq_admi_gestion_directorios.nextval, 
  (select codigo_app from db_general.admi_gestion_directorios where aplicacion = 'TelcosWeb' and rownum=1),
  (select max(codigo_path)+1 from db_general.admi_gestion_directorios where aplicacion = 'TelcosWeb'),
  'TelcosWeb',
  '593',
  'MD',
  'Financiero',
  'ReporteCourier',
  'Activo',
  sysdate,
  'gnarea'
);