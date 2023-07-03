SET SERVEROUTPUT ON
--Rollback de la inserción de parámetros para la restricción de nombres de usuario en servicios
DECLARE
  v_target    NUMBER := 0;
BEGIN
  BEGIN
    SELECT ID_PARAMETRO INTO v_target
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB
    WHERE NOMBRE_PARAMETRO='NOMBREUSUARIO_SUBCADENAS_NO_PERMITIDAS';
    
    DELETE
    FROM DB_GENERAL.ADMI_PARAMETRO_DET
    WHERE PARAMETRO_ID = v_target
    AND VALOR2 = 'palabra_reservada'
    AND usr_creacion = 'colvera';
    
    DELETE
    FROM DB_GENERAL.admi_parametro_cab 
    WHERE id_parametro = v_target
    AND nombre_parametro = 'NOMBREUSUARIO_SUBCADENAS_NO_PERMITIDAS'
    AND descripcion = 'DEFINE EXPRESIONES REGULARES NO PERMITIDAS EN NOMBRES DE USUARIOS CREADOS POR EL SISTEMA'
    AND modulo = 'COMERCIAL'
    AND usr_creacion = 'colvera';
      
    SYS.DBMS_OUTPUT.PUT_LINE('Se han eliminado los parámetros para restringir los nombres de usuarios en servicios');
    
    COMMIT;
  
  EXCEPTION
    WHEN NO_DATA_FOUND THEN
        DBMS_OUTPUT.PUT_LINE('No se encontró ningún dato en la tabla.');
        ROLLBACK;
    WHEN OTHERS THEN
        SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                               || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
        ROLLBACK;
  END; 
END;