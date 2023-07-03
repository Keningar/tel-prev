-- Eliminar nueva tarea para formulario de soporte
DELETE FROM DB_SOPORTE.ADMI_TAREA
WHERE NOMBRE_TAREA = 'GOLTV'
AND DESCRIPCION_TAREA = 'Registro de requerimientos de GolTv'
AND USR_CREACION = 'djreyes';

COMMIT;
/
