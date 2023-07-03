-- Eliminar nueva Tarea para actualizar correo ECDF
DELETE FROM DB_SOPORTE.ADMI_TAREA
WHERE NOMBRE_TAREA = 'El canal del fútbol'
AND DESCRIPCION_TAREA = 'Actualizar correo el canal del futbol'
AND USR_CREACION = 'farias';

COMMIT;
/
