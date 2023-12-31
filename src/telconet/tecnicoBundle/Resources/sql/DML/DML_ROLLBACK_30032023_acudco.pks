/**
 *  
 * Se realiza el script para el proceso de Rollback los parametros que agrupan a los modelos de equipos no regularizados
 * @author Geovanny Cudco <acudco@telconet.ec>
 * @version 1.0 30-03-2023
 */
SET SERVEROUTPUT ON; 
DECLARE
    INT_ID_PARAMETRO NUMBER;
BEGIN
    -- capturo el id del parámetro
    SELECT ID_PARAMETRO 
    INTO INT_ID_PARAMETRO 
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
    WHERE NOMBRE_PARAMETRO='EQUIPOS SIN MODELO'
        AND ESTADO='Activo';
    
    --elimino los elementos hijos    
    DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
    WHERE PARAMETRO_ID = INT_ID_PARAMETRO;
    SYS.DBMS_OUTPUT.PUT_LINE('ELIMINACIÓN EXITOSA DE LOS PARÁMETROS HIJOS');
    
    DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
    WHERE NOMBRE_PARAMETRO='EQUIPOS SIN MODELO'
        AND ESTADO='Activo';
    SYS.DBMS_OUTPUT.PUT_LINE('ELIMINACIÓN EXITOSA DEL PARÁMETRO PADRE');
    
    COMMIT;
    
    EXCEPTION
    WHEN OTHERS THEN
      SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
      ROLLBACK;
      
END;
/