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
  WHERE NOMBRE_PARAMETRO = 'PROYECTO INTEGRACION PARAMOUNT';
  
    delete from db_general.admi_parametro_det where parametro_id = ln_id_param and descripcion = 'TAREA USADA PARA FORMULARIO DE SOPORTE L1';
    delete from db_general.admi_parametro_det where parametro_id = ln_id_param and descripcion = 'DATOS USADOS PARA ENVIO DE CORREO DEL FORMULARIO DE SOPORTE L1';
    delete from db_general.admi_parametro_det where parametro_id = ln_id_param and descripcion = 'EXTENSIONES_PERMITIDAS';
    delete from db_general.admi_parametro_det where parametro_id = ln_id_param and descripcion = 'CATEGORIA-FORMULARIO';
    delete from db_general.admi_parametro_det where parametro_id = ln_id_param and descripcion = 'GRAVEDAD-PROBLEMA';    

  
  COMMIT;
    

  SYS.DBMS_OUTPUT.PUT_LINE('Insertado Correctamente');
EXCEPTION
WHEN OTHERS THEN
  DBMS_OUTPUT.put_line('Error: '||sqlerrm);  
  ROLLBACK; 
END;

/ 
