-- Add/modify columns 
alter table DB_MONITOREO.GPS_MONITOREO add PUBLISH_ID VARCHAR2(225);
-- Add comments to the columns 
comment on column DB_MONITOREO.GPS_MONITOREO.PUBLISH_ID is 'ID utilizado para sustituir al IMEI a partir de la versión 10 de android.';

-- Add/modify columns 
alter table DB_MONITOREO.GPS_ULTIMO_PUNTO add PUBLISH_ID VARCHAR2(225);
-- Add comments to the columns 
comment on column DB_MONITOREO.GPS_ULTIMO_PUNTO.PUBLISH_ID is 'ID utilizado para sustituir al IMEI a partir de la versión 10 de android.';


-- Add/modify columns 
alter table DB_MONITOREO.GPS_MONITOREO add ID_CUADRILLA VARCHAR2(225);
-- Add comments to the columns 
comment on column DB_MONITOREO.GPS_MONITOREO.ID_CUADRILLA is 'ID utilizado para conocer que cuadrilla es responsable de la tablet asignada.';

-- Add/modify columns 
alter table DB_MONITOREO.GPS_ULTIMO_PUNTO add ID_CUADRILLA VARCHAR2(225);
-- Add comments to the columns 
comment on column DB_MONITOREO.GPS_ULTIMO_PUNTO.ID_CUADRILLA is 'ID utilizado para conocer que cuadrilla es responsable de la tablet asignada.';


COMMIT
/