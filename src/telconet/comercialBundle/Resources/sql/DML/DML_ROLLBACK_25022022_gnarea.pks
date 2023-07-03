delete from db_general.admi_gestion_directorios  where submodulo = 'ReporteBuro' and usr_creacion = 'gnarea'; --TN y MD
delete from db_general.admi_parametro_det where parametro_id = (select id_parametro from db_general.admi_parametro_cab where nombre_parametro = 'REPORTE_BURO' AND USR_CREACION = 'gnarea');
delete from db_general.admi_parametro_cab where nombre_parametro = 'REPORTE_BURO' and usr_creacion = 'gnarea';
commit;