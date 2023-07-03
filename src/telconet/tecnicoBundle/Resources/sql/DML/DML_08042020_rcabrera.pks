/**
 *
 * Se realiza el script para ingresar los parametros usados en el proyecto utilizado para consultar factibilidad desde el CRM
 * @author Richard Cabrera <rcabrera@telconet.ec>
 * @version 1.0 08-04-2020
 */

DECLARE
  ln_id_param NUMBER := 0;
BEGIN


INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'PARAMETROS PROYECTO CONSULTA FACTIBILIDAD CRM',
    'PARAMETROS UTILIZADOS EN EL PROYECTO QUE PERMITE CONSULTAR LA FACTIBLIDAD DESDE EL CRM',
    'INFRAESTRUCTURA',
    'CONSULTAR FACTIBILIDAD',
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
  WHERE NOMBRE_PARAMETRO = 'PARAMETROS PROYECTO CONSULTA FACTIBILIDAD CRM';
     
    
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
    'LIMITE_CAJAS_MOSTRAR',
    '1',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    10
);    
    

  
  COMMIT;
    

  SYS.DBMS_OUTPUT.PUT_LINE('Insertado Correctamente');
EXCEPTION
WHEN OTHERS THEN
  DBMS_OUTPUT.put_line('Error: '||sqlerrm);  
  ROLLBACK; 
END;

/ 
