/**
 *
 * Script de rollback para el proyecto Perfiles Factibilidad Nacional
 *	 
 * @author Jonathan Quintana <jiquintana@telconet.ec>
 * @version 1.0 09-08-2022
 */

DECLARE
  ln_id_param NUMBER := 0;
  id_carac NUMBER := 0;
  count_cab NUMBER := 0;
  count_carac NUMBER := 0;
  
BEGIN
  
  SELECT COUNT(*) INTO count_cab FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PERFILES_FACTIBILIDAD_NACIONAL';
  
  IF count_cab > 0 THEN 
    SELECT id_parametro
    INTO ln_id_param
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB
    WHERE NOMBRE_PARAMETRO = 'PERFILES_FACTIBILIDAD_NACIONAL';
  
    DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET WHERE PARAMETRO_ID = ln_id_param;
  END IF;
  
  DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB where NOMBRE_PARAMETRO = 'PERFILES_FACTIBILIDAD_NACIONAL';
  
 
  SELECT COUNT(*) INTO count_carac FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'CONFIGURACION_PERFILES_FACTIBILIDAD';
  
  IF count_carac > 0 THEN
    SELECT ID_CARACTERISTICA
    INTO id_carac
    FROM DB_COMERCIAL.ADMI_CARACTERISTICA
    WHERE DESCRIPCION_CARACTERISTICA = 'CONFIGURACION_PERFILES_FACTIBILIDAD';
    
    DELETE FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC WHERE CARACTERISTICA_ID = id_carac;
  END IF;
 
  DELETE FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'CONFIGURACION_PERFILES_FACTIBILIDAD';
  
  COMMIT;
    

  SYS.DBMS_OUTPUT.PUT_LINE('Insertado Correctamente');
EXCEPTION
WHEN OTHERS THEN
  DBMS_OUTPUT.put_line('Error: '||sqlerrm);  
  ROLLBACK; 
END;

/
