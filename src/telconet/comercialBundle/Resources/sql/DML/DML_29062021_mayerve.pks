/**
 *
 * Se crea parametro para validar la creacion de tarea material excedente
 * @author Mario Ayerve<mayerve@telconet.ec>
 * @version 1.0 25-06-2021
 */

DECLARE
  ln_id_param NUMBER := 0;
BEGIN


INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'CREA_TAREA_EXCEDENTES_MATERIALES',
    'PARAMETROS PARA PERMITIR LA CREACION DE TAREAS EXCEDENTES MATERIALES',
    'COMERCIAL',
    'AUTORIZACIONES',
    'Activo',
    'mayerve',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL
  );               


  SELECT id_parametro
  INTO ln_id_param
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO = 'CREA_TAREA_EXCEDENTES_MATERIALES';  


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
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    ln_id_param,
    'CREA_TAREA_EXCEDENTES_MATERIALES_AUTOMATICA',
    'N',      
    'Activo',
    'mayerve',
    SYSDATE,
    '127.0.0.1',
    10
); 


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
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    ln_id_param,
    'CREA_TAREA_EXCEDENTES_MATERIALES_MANUAL',
    'N',      
    'Activo',
    'mayerve',
    SYSDATE,
    '127.0.0.1',
    10
); 

COMMIT;

EXCEPTION
WHEN OTHERS THEN
  DBMS_OUTPUT.put_line('Error: '||sqlerrm);  
  ROLLBACK; 
END;

/ 
