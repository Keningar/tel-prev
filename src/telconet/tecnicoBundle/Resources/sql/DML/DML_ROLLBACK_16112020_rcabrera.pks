/**
 *
 * Script de rollback para el proyecto Netlife CAM - Corte/Reactivacion
 *	 
 * @author Richard Cabrera <rcabrera@telconet.ec>
 * @version 1.0 16-11-2020
 */

DECLARE
  ln_id_param NUMBER := 0;
BEGIN             


  SELECT id_parametro
  INTO ln_id_param
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO = 'PROYECTO NETLIFECAM';
  
  delete from db_general.admi_parametro_det where parametro_id = ln_id_param and descripcion = 'PRODUCTO INTERNET DEDICADO';
    
  
  COMMIT;
    

  SYS.DBMS_OUTPUT.PUT_LINE('Insertado Correctamente');
EXCEPTION
WHEN OTHERS THEN
  DBMS_OUTPUT.put_line('Error: '||sqlerrm);  
  ROLLBACK; 
END;

/ 
