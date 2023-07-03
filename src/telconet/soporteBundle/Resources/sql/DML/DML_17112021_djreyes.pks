-- SOPORTE TAREA RAPIDA
-- TAR 1 -  Tarea para formulario de goltv
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
    WHERE NOMBRE_PROCESO = 'PROCESO TAREAS IPCC L1'
  ),
  'GOLTV', 'Registro de requerimientos de GolTv', 'Activo',
  'djreyes', sysdate, 'djreyes', sysdate, null, null
);

COMMIT;
/