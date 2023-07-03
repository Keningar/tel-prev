--Ingreso de los seguimientos en el combo de ECUCERT
DELETE FROM DB_SOPORTE.ADMI_SEGUIMIENTO_ECUCERT WHERE SEGUIMIENTO =
'Cliente solicita que le expliquen la notificación';

DELETE FROM DB_SOPORTE.ADMI_SEGUIMIENTO_ECUCERT WHERE SEGUIMIENTO =
'Cliente indica que no posee la vulnerabilidad';

DELETE FROM DB_SOPORTE.ADMI_SEGUIMIENTO_ECUCERT WHERE SEGUIMIENTO =
'Vulnerabilidad se encuentra en el CPE del cliente';

DELETE FROM DB_SOPORTE.ADMI_SEGUIMIENTO_ECUCERT WHERE SEGUIMIENTO =
'La vulnerabilidad sí está en los activos del cliente';

DELETE FROM DB_SOPORTE.ADMI_SEGUIMIENTO_ECUCERT WHERE SEGUIMIENTO =
'Cliente indica que toma acción';

DELETE FROM DB_SOPORTE.ADMI_SEGUIMIENTO_ECUCERT WHERE SEGUIMIENTO =
'Cliente indica que la información proporcionada no es suficiente';

DELETE FROM DB_SOPORTE.ADMI_SEGUIMIENTO_ECUCERT WHERE SEGUIMIENTO =
'Cliente solicita que se le vuelva a notificar';

COMMIT;

/


