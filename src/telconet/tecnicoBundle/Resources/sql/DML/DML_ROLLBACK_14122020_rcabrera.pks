/**
 *
 * Se crea script de rollback para el proyecto: Paramount Fase 2: Formulario de Soporte L1
 * @author Richard Cabrera <rcabrera@telconet.ec>
 * @version 1.0 06-12-2020
 */
 DECLARE
  ln_id_param NUMBER := 0;
BEGIN             

  SELECT id_parametro
  INTO ln_id_param
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
  WHERE NOMBRE_PARAMETRO = 'HERRAMIENTA CONSULTA DE TRAZABILIDAD';

    DELETE FROM db_general.admi_parametro_det WHERE PARAMETRO_ID  = ln_id_param;
    DELETE FROM db_general.admi_parametro_CAB WHERE ID_PARAMETRO  = ln_id_param;

  COMMIT;
    

  SYS.DBMS_OUTPUT.PUT_LINE('Insertado Correctamente');
EXCEPTION
WHEN OTHERS THEN
  DBMS_OUTPUT.put_line('Error: '||sqlerrm);  
  ROLLBACK; 
END;

/ 
