
--ELIMINACIÓN DE LA CARACTERÍSTICA EXPORTAR_REPORTE_TAREAS EN CASO QUE ALGÚN SCRIPT FALLE.
DELETE FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'EXPORTAR_REPORTE_TAREAS';

COMMIT;
/
