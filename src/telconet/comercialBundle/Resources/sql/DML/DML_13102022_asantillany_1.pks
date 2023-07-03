/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Parametro para caracteristica INSTANCIA_ID_ORQ
 * @author Anthony Santillan <asantillany@telconet.ec>
 * @version 1.0 13-10-2022.
 */

 /* PARAMETROS */
/* DB_GENERAL.ADMI_PARAMETRO_CAB */
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB(
  ID_PARAMETRO, 
  NOMBRE_PARAMETRO, 
  DESCRIPCION, 
  MODULO, 
  PROCESO, 
  ESTADO, 
  USR_CREACION, 
  FE_CREACION, 
  IP_CREACION, 
  USR_ULT_MOD, 
  FE_ULT_MOD, 
  IP_ULT_MOD
)
VALUES
(
  DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL, 
  'CARACTERISTICA_DE_LA_INSTANCIA', 
  'PARAMETRO_PARA_VALIDACION', 
  'SOPORTE', 
  'RECHAZAR ANULAR Y CANCELAR TAREA', 
  'Activo', 
  'asantillany', 
  SYSDATE, 
  '127.0.0.1', 
  '', 
  '', 
  ''
);

/* DB_GENERAL.ADMI_PARAMETRO_DET */
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,  
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) 
VALUES 
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
     (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'CARACTERISTICA_DE_LA_INSTANCIA'),
    'CARACTERISTICA_ASOCIADA_A_UN_SERVICIO_CREADO_DESDE_EL_ORQUESTADOR',
    'INSTANCIA_ID_ORQ',   
    'Activo',
    'asantillany',
    SYSDATE,
    '127.0.0.1',
    'TN'
); 

COMMIT;
/
