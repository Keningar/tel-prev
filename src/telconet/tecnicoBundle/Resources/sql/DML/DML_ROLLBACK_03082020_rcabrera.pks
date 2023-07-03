/**
 *
 * Se crea script de rollback para el proyecto: TN: INT: TECNICO: Bug: Validación de subredes en el proceso de rutas estaticas para servicios de Internet 
 * @author Richard Cabrera <rcabrera@telconet.ec>
 * @version 1.0 03-08-2020
 */
 DECLARE
  ln_id_param NUMBER := 0;
BEGIN             

  SELECT id_parametro
  INTO ln_id_param
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO = 'MEJORAS PROCESO CREACION DE RUTAS AUTOMATICAS';
  
  delete from db_general.admi_parametro_det where parametro_id = ln_id_param and valor1 = 'validarEnrutamientoPeSubredes'; 
    
  
  COMMIT;
    

  SYS.DBMS_OUTPUT.PUT_LINE('Insertado Correctamente');
EXCEPTION
WHEN OTHERS THEN
  DBMS_OUTPUT.put_line('Error: '||sqlerrm);  
  ROLLBACK; 
END;

/ 
