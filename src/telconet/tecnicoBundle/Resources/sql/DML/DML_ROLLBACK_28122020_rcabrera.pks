/**
 *
 * Se crea script de rollback para el proyecto: Quitar Validacion de Internet SDWAN
 * @author Richard Cabrera <rcabrera@telconet.ec>
 * @version  1.0 28-12-2020
 */
 DECLARE
  ln_id_param NUMBER := 0;
BEGIN             

  SELECT id_parametro
  INTO ln_id_param
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
  WHERE NOMBRE_PARAMETRO = 'VALIDACIONES SDWAN';
  
    delete from db_general.admi_parametro_det where parametro_id = ln_id_param;
    delete from db_general.admi_parametro_cab where id_parametro = ln_id_param;

  
  COMMIT;
    

  SYS.DBMS_OUTPUT.PUT_LINE('Insertado Correctamente');
EXCEPTION
WHEN OTHERS THEN
  DBMS_OUTPUT.put_line('Error: '||sqlerrm);  
  ROLLBACK; 
END;

/ 
