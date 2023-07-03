DElETE FROM db_infraestructura.info_historial_elemento WHERE USR_CREACION = 'migra-monitoreo';
DElETE FROM db_infraestructura.info_empresa_elemento WHERE USR_CREACION = 'migra-monitoreo';
DElETE FROM db_infraestructura.info_detalle_elemento WHERE USR_CREACION = 'migra-monitoreo';
DElETE FROM db_infraestructura.info_ubicacion WHERE USR_CREACION = 'migra-monitoreo';
DElETE FROM db_infraestructura.info_empresa_elemento_ubica WHERE USR_CREACION = 'migra-monitoreo';
DElETE FROM db_infraestructura.info_elemento WHERE USR_CREACION = 'migra-monitoreo';
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'CONTROL MONITOREO');
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'CONTROL MONITOREO';

delete from db_infraestructura.info_vehiculo_tmp;

alter table DB_INFRAESTRUCTURA.INFO_UBICACION drop column OFICINA_ID;

COMMIT;

/