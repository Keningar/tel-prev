/**
 *
 * Se crean parametros para controlar si unas validaciones de SDWAN aplican o no
 * @author Richard Cabrera <rcabrera@telconet.ec>
 * @version 1.0 28-12-2020
 */

DECLARE
  ln_id_param NUMBER := 0;
BEGIN


INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'VALIDACIONES SDWAN',
    'VALIDACIONES SDWAN',
    'INFRAESTRUCTURA',
    'INTERNET SDWAN',
    'Activo',
    'rcabrera',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL
  );               


  SELECT id_parametro
  INTO ln_id_param
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO = 'VALIDACIONES SDWAN';  



INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    ln_id_param,
    'BANDERA-VALIDAR-VARIOS-SERVICIOS',
    'N',
    'N',
    'S',
    'Activo',
    'rcabrera',
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
