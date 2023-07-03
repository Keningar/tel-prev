/**
 * Documentación INSERT DE PARÁMETROS DE MOTIVO POR INACTIVACIÓN POR FECHAS DE VIGENCIAS.
 *
 * Se insertan parámetros para el motivo de inactivación por fechas de vigencias.
 *
 * @author Katherine Yager <kyager@telconet.ec>
 * @version 1.0 25-10-2019
 */

--INSERT NUEVO MOTIVO

-- Se crean el motivo para la inactivación por fechas de vigencias.
-- Inactivación automática por fechas de vigencias.
INSERT INTO DB_GENERAL.ADMI_MOTIVO(
  ID_MOTIVO,
  RELACION_SISTEMA_ID,
  NOMBRE_MOTIVO,
  ESTADO,
  USR_CREACION,
  FE_CREACION,
  USR_ULT_MOD,
  FE_ULT_MOD,
  CTA_CONTABLE,
  REF_MOTIVO_ID
) VALUES(
    DB_GENERAL.SEQ_ADMI_MOTIVO.NEXTVAL,
    NULL,
    'Inactivación automática por fechas de vigencias',
    'Activo',
    'kyager',
    SYSDATE,
    'kyager',
    SYSDATE,
    NULL,
    NULL
);  
COMMIT;
/