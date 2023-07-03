/**
 *
 * Se crea script de rollback para el proyecto parametrizar distancia de cajas
 * @author Richard Cabrera <rcabrera@telconet.ec>
 * @version 1.0 03-09-2020
 */
 DECLARE
  ln_id_param NUMBER := 0;
BEGIN             

  SELECT id_parametro
  INTO ln_id_param
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO = 'PROYECTO PARAMETRIZAR DISTANCIA DE CAJAS';
  
  delete from db_general.admi_parametro_det where parametro_id = ln_id_param;
  delete from db_general.ADMI_PARAMETRO_CAB where id_parametro = ln_id_param;   
    
  
  COMMIT;
    

  SYS.DBMS_OUTPUT.PUT_LINE('Insertado Correctamente');
EXCEPTION
WHEN OTHERS THEN
  DBMS_OUTPUT.put_line('Error: '||sqlerrm);  
  ROLLBACK; 
END;

/ 
