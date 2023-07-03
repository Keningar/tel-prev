/**
 *
 * Se crea script de rollback para el  proyecto mejoras en creacion de rutas automaticas.
 * @author Richard Cabrera <rcabrera@telconet.ec>
 * @version 1.0 10-07-2020
 */
 DECLARE
  ln_id_param NUMBER := 0;
BEGIN             

  delete from db_soporte.admi_tarea where nombre_tarea = 'REVISION INCONSISTENCIA EN SUBRED';

  SELECT id_parametro
  INTO ln_id_param
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO = 'MEJORAS PROCESO CREACION DE RUTAS AUTOMATICAS';
  
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
