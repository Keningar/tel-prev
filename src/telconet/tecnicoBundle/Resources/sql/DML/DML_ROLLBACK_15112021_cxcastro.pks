/**
 *
 * Se crean archivo rollback para el proyecto  TN : INT: Telcos: Nuevo: Cambios en pantalla de activacion producto fastCloud
 *	 
 * @author Christian Castro <cxcastro@telconet.ec>
 * @version 1.0 15-11-2021
 */

DECLARE
  Ln_id_param NUMBER := 0;
  Lv_nombre_parametro VARCHAR2(100) := 'NO_VISUALIZAR_FORM_DATOS_TECNICOS';
BEGIN
  
  
  SELECT id_parametro
  INTO Ln_id_param
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO = Lv_nombre_parametro;
  
  DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET WHERE PARAMETRO_ID = Ln_id_param;
  
  DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE ID_PARAMETRO = Ln_id_param;
  
COMMIT;

EXCEPTION
WHEN OTHERS THEN
  DBMS_OUTPUT.put_line('Error: '||sqlerrm);  
  ROLLBACK; 
END;

/ 
