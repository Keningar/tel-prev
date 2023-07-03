
/**
 * DEBE EJECUTARSE EN DB_SOPORTE
 * Script para crear tabla de respaldo con los registros a modificar en proyecto NOC
 * @author Fernando López <filopez@telconet.ec>
 * @version 1.0 06-01-2022 - Versión Inicial.
 */

--####################################################################################
--CREAR TABLA DB_SOPORTE.INFO_ASIGNACION_SOLICITUD_TMP
--####################################################################################
--

CREATE TABLE DB_SOPORTE.INFO_ASIGNACION_SOLICITUD_TMP AS 
(SELECT ID_ASIGNACION_SOLICITUD, DEPARTAMENTO_ID, REFERENCIA_CLIENTE, ORIGEN, TIPO_ATENCION, TIPO_PROBLEMA, CRITICIDAD,
        NOMBRE_REPORTA, NOMBRE_SITIO, REFERENCIA_ID, EMPRESA_COD, USR_ASIGNADO, DETALLE, USR_CREACION, FE_CREACION, 
        IP_CREACION, USR_ULT_MOD, FE_ULT_MOD, CAMBIO_TURNO, ESTADO, DATO_ADICIONAL, OFICINA_ID, ASIGNACION_PADRE_ID, 
        TAB_VISIBLE, TRAMO, HILO_TELEFONICA, CIRCUITO, NOTIFICACION 
        FROM DB_SOPORTE.INFO_ASIGNACION_SOLICITUD WHERE TAB_VISIBLE IN (
        'GestionPendientesBackbone',
        'GestionPendientesRecorridos',
        'GestionPendientesMunicipio',
        'GestionPendientesTelefonica'
        ) 
        AND ESTADO <> 'Eliminado'
        AND TRUNC(FE_CREACION) <= TRUNC(SYSDATE));
--
--
COMMENT ON TABLE DB_SOPORTE.INFO_ASIGNACION_SOLICITUD_TMP IS 'TABLA DE RESPALDO DB_SOPORTE.INFO_ASIGNACION_SOLICITUD, DE LOS REGISTROS ACTUALIZADOS PARA EL PASE DEL PROYECTO TN: INT: Soporte: Nuevo: Migración aplicativo NOC Fase 5';
--
--
COMMIT;
/