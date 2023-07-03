-- Eliminando los detalles del parametro nuevos del proyecto
Delete from DB_GENERAL.ADMI_PARAMETRO_DET
where parametro_id=(select ID_PARAMETRO from DB_GENERAL.ADMI_PARAMETRO_CAB 
		    where nombre_parametro = 'PRODUCTOS ADICIONALES MANUALES');

-- Eliminar la cabecera de los nuevo parametros
Delete from DB_GENERAL.ADMI_PARAMETRO_CAB
where NOMBRE_PARAMETRO='PRODUCTOS ADICIONALES MANUALES';

-- Regresar el estado para el traslado de los extender dual band en activo
UPDATE ADMI_PARAMETRO_DET
SET VALOR5 = 'Activo'
WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB
                          WHERE NOMBRE_PARAMETRO = 'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD')
AND DESCRIPCION = 'Estados de los servicios parametrizados para permitir un traslado en MD'
AND VALOR1 = 'TRASLADO'
AND VALOR2 = 'ESTADOS_SERVICIOS_X_PROD_PERMITIDOS'
AND VALOR3 = 'EXTENDER_DUAL_BAND'
AND VALOR4 = 'PrePlanificada';

-- Eliminar los nuevos parametros de otro proyecto de planificacion simultanea
Delete from DB_GENERAL.ADMI_PARAMETRO_DET
where parametro_id=(select ID_PARAMETRO from DB_GENERAL.ADMI_PARAMETRO_CAB 
		    where nombre_parametro = 'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD')
and DESCRIPCION = 'Relación de solicitudes asociadas a servicios que se gestionan de manera simultánea'
and usr_creacion = 'djreyes';

-- Volver a activar los parametros simultaneos
update ADMI_PARAMETRO_DET
set estado = 'Activo'
where PARAMETRO_ID = (
  SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD')
AND DESCRIPCION = 'Relación de solicitudes asociadas a servicios que se gestionan de manera simultánea'
AND VALOR1 = 'GESTION_PYL_SIMULTANEA'
AND VALOR3 = 'RECHAZAR'
AND VALOR7 = 'RECHAZO_3';

update ADMI_PARAMETRO_DET
set estado = 'Activo'
where PARAMETRO_ID = (
  SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD')
AND DESCRIPCION = 'Relación de solicitudes asociadas a servicios que se gestionan de manera simultánea'
AND VALOR1 = 'GESTION_PYL_SIMULTANEA'
and VALOR3 = 'DETENER'
and VALOR7 = 'DETENCION_3';

update ADMI_PARAMETRO_DET
set estado = 'Activo'
where PARAMETRO_ID = (
  SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD')
AND DESCRIPCION = 'Relación de solicitudes asociadas a servicios que se gestionan de manera simultánea'
AND VALOR1 = 'GESTION_PYL_SIMULTANEA'
and VALOR3 = 'ANULAR'
and VALOR7 = 'ANULACION_3';

COMMIT;
/