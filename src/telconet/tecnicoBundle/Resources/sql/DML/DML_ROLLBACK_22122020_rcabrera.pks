delete from db_comercial.admi_producto_caracteristica where producto_id in (select ID_PRODUCTO from admi_producto where descripcion_producto = 'SSID MOVIL');
delete from db_comercial.admi_producto where id_producto = (select ID_PRODUCTO from db_comercial.admi_producto where descripcion_producto = 'SSID MOVIL');

    delete from db_general.admi_parametro_det where parametro_id = (SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PROYECTO SSID MOVIL');

    delete from db_general.admi_parametro_cab where id_parametro = (SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PROYECTO SSID MOVIL');

COMMIT;

/
