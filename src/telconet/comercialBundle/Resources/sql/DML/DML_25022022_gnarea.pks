insert into db_general.admi_gestion_directorios (id_gestion_directorio, codigo_app, codigo_path, 
aplicacion, pais, empresa, modulo, submodulo, estado, fe_creacion, usr_creacion) 
values (db_general.seq_admi_gestion_directorios.nextval, 
(select codigo_app from db_general.admi_gestion_directorios where aplicacion='TelcosWeb' and rownum=1),
(select max(codigo_path)+1 from db_general.admi_gestion_directorios where aplicacion='TelcosWeb'),
'TelcosWeb',
'593',
'MD',
'Financiero',
'ReporteBuro',
'Activo',
sysdate,
'gnarea'
);

insert into db_general.admi_gestion_directorios (id_gestion_directorio, codigo_app, codigo_path, 
aplicacion, pais, empresa, modulo, submodulo, estado, fe_creacion, usr_creacion) 
values (db_general.seq_admi_gestion_directorios.nextval, 
(select codigo_app from db_general.admi_gestion_directorios where aplicacion='TelcosWeb' and rownum=1),
(select max(codigo_path)+1 from db_general.admi_gestion_directorios where aplicacion='TelcosWeb'),
'TelcosWeb',
'593',
'TN',
'Financiero',
'ReporteBuro',
'Activo',
sysdate,
'gnarea'
);


--ADMI_PARAMETRO

insert into db_general.admi_parametro_cab 
(id_parametro, nombre_parametro, descripcion, modulo, proceso, estado, usr_creacion, fe_creacion, ip_creacion)
values (db_general.seq_admi_parametro_cab.nextval, 'REPORTE_BURO', 'CONTIENE LOS PARAMETROS CORRESPONDIENTES QUE SE USAN PARA EL REPORTE DE BURO', 'FINANCIERO',
'REPORTES','Activo', 'gnarea', sysdate, '127.0.0.1');

insert into db_general.admi_parametro_det
(id_parametro_det, parametro_id, descripcion, valor1, valor2, estado, usr_creacion, ip_creacion, fe_creacion)
values (db_general.seq_admi_parametro_det.nextval, (select id_parametro from db_general.admi_parametro_cab where nombre_parametro = 'REPORTE_BURO'),
'RUTA_NFS', 'URL_NFS', 'http://nosites.telconet.ec/microservicios/nfs/procesar', 'Activo', 'gnarea', '127.0.0.1', sysdate);

insert into db_general.admi_parametro_det
(id_parametro_det, parametro_id, 
descripcion, valor1, estado, usr_creacion, ip_creacion, fe_creacion)
values (db_general.seq_admi_parametro_det.nextval, (select id_parametro from db_general.admi_parametro_cab where nombre_parametro = 'REPORTE_BURO'),
'REPORTES_DISPONIBLES', 'BuroCredito', 'Activo', 'gnarea', '127.0.0.1', sysdate);

commit;
/