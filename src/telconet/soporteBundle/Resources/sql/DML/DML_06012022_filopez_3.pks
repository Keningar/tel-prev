
/**
 * DEBE EJECUTARSE EN DB_SOPORTE
 * Eliminar los pendientes existentes, para que se listen solo los nuevos 
 * pendientes que se generan posterior al pase de NOC
 * @author Fernando López <filopez@telconet.ec>
 * @version 1.0 06-01-2022 - Versión Inicial.
 */

--####################################################################################
--RESETEAR REGISTROS DE GESTIÓN DE PENDIENTES
--####################################################################################
--

UPDATE DB_SOPORTE.INFO_ASIGNACION_SOLICITUD SET ESTADO='Eliminado' WHERE TAB_VISIBLE IN (
'GestionPendientesBackbone',
'GestionPendientesRecorridos',
'GestionPendientesMunicipio',
'GestionPendientesTelefonica'
) AND TRUNC(FE_CREACION) <= TRUNC(SYSDATE);
--
--
COMMIT;
/