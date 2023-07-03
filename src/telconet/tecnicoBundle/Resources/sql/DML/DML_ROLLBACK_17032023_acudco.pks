/**
 *
 * Se realiza el script roolback para actualizar los parametros de los elementos pertenecientes a los nodos.
 * Nota: el campo Valor3 contendrá la abreviatura del elemento.
 * @author Geovanny Cudco <acudco@telconet.ec>
 * @version 1.0 17-03-2023
 */

SET SERVEROUTPUT ON; 
DECLARE
    INT_ID_PARAMETRO NUMBER;
BEGIN

    SELECT ID_PARAMETRO
    INTO INT_ID_PARAMETRO
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
    WHERE NOMBRE_PARAMETRO = 'ELEMENTOS NODOS';  

    UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
    SET VALOR3 = '',
        USR_ULT_MOD='acudco',
        FE_ULT_MOD=SYSDATE,
        IP_ULT_MOD='127.0.0.1'
    WHERE PARAMETRO_ID = INT_ID_PARAMETRO;
    SYS.DBMS_OUTPUT.PUT_LINE('ROLLABACK EXITOSO');             
        
    COMMIT;
    
    EXCEPTION
    WHEN OTHERS THEN
      SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                               || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
      ROLLBACK;
END;
/