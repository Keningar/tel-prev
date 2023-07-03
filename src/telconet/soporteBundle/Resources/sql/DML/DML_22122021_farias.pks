/**
 * @author Alberto Arias <farias@telconet.ec>
 * @version 1.0
 * @since 22-12-2021    
 * Se crea Tarea para actualizar correo ECDF
 */
-- SOPORTE TAREA RAPIDA
-- TAR 1 -  Tarea para actualizar correo ECDF
INSERT INTO DB_SOPORTE.ADMI_TAREA
(
  ID_TAREA, PROCESO_ID, NOMBRE_TAREA, DESCRIPCION_TAREA, ESTADO,
  USR_CREACION, FE_CREACION, USR_ULT_MOD, FE_ULT_MOD,
  REQUIERE_FIBRA, VISUALIZAR_MOVIL
)
VALUES
(
  DB_SOPORTE.SEQ_ADMI_TAREA.nextval,
  (
    SELECT ID_PROCESO FROM DB_SOPORTE.ADMI_PROCESO
    WHERE NOMBRE_PROCESO = 'IPCCL1 - REQUERIMIENTOS CORREO'
  ),
  'El canal del fútbol', 'Actualizar correo el canal del futbol', 'Activo',
  'farias', sysdate, 'farias', sysdate, null, null
);

COMMIT;
/


