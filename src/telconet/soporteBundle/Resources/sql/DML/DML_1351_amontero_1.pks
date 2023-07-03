--ACTUALIZAMOS EL ESTADO DE LA ASIGNACION DE TODAS LAS ASIGNACIONES
--QUE TENGAN ESTADO EnGestion Y QUE EL CASO LIGADO ESTE Cerrado
UPDATE DB_SOPORTE.INFO_ASIGNACION_SOLICITUD
SET ESTADO                                                                     = 'Cerrado'
WHERE TIPO_ATENCION                                                            = 'CASO'
AND ESTADO                                                                     = 'EnGestion'
AND REFERENCIA_ID                                                              IS NOT NULL
AND DB_SOPORTE.SPKG_ASIGNACION_SOLICITUD.F_ESTADO_CASO_POR_CASO(REFERENCIA_ID) = 'Cerrado';

--ACTUALIZAMOS EL ESTADO DE LA ASIGNACION DE TODAS LAS ASIGNACIONES
--QUE TENGAN ESTADO EnGestion Y QUE LA TAREA LIGADA ESTE Finalizada
UPDATE DB_SOPORTE.INFO_ASIGNACION_SOLICITUD
SET ESTADO                                                                       = 'Cerrado'
WHERE TIPO_ATENCION                                                              = 'TAREA'
AND ESTADO                                                                       = 'EnGestion'
AND REFERENCIA_ID                                                                IS NOT NULL
AND DB_SOPORTE.SPKG_ASIGNACION_SOLICITUD.F_ESTADO_TAREA_POR_TAREA(REFERENCIA_ID) = 'Finalizada';

COMMIT;

/