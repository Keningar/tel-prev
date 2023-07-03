/**
 *
 * Se realiza el script para actualizar los parametros de los elementos pertenecientes a los nodos.
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
    SET VALOR3 = 'UPS',
        USR_ULT_MOD='acudco',
        FE_ULT_MOD=SYSDATE,
        IP_ULT_MOD='127.0.0.1'
    WHERE PARAMETRO_ID = INT_ID_PARAMETRO
        AND VALOR1='UPS';
    SYS.DBMS_OUTPUT.PUT_LINE('ACTUALIZACIÓN EXITOSA DEL PARÁMETRO UPS');
        
    UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
    SET VALOR3 = 'BAT',
        USR_ULT_MOD='acudco',
        FE_ULT_MOD=SYSDATE,
        IP_ULT_MOD='127.0.0.1'
    WHERE PARAMETRO_ID = INT_ID_PARAMETRO
        AND VALOR1='BATERIA';
    SYS.DBMS_OUTPUT.PUT_LINE('ACTUALIZACIÓN EXITOSA DEL PARÁMETRO UPS');
        
    UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
    SET VALOR3 = 'TDP',
        USR_ULT_MOD='acudco',
        FE_ULT_MOD=SYSDATE,
        IP_ULT_MOD='127.0.0.1'
    WHERE PARAMETRO_ID = INT_ID_PARAMETRO
        AND VALOR1='TABLEROS DE PARALELISMO';
    SYS.DBMS_OUTPUT.PUT_LINE('ACTUALIZACIÓN EXITOSA DEL PARÁMETRO TABLEROS DE PARALELISMO');        
        
    UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
    SET VALOR3 = 'TTA',
        USR_ULT_MOD='acudco',
        FE_ULT_MOD=SYSDATE,
        IP_ULT_MOD='127.0.0.1'    
    WHERE PARAMETRO_ID = INT_ID_PARAMETRO
        AND VALOR1='TABLEROS DE TRANSFERENCIA';
    SYS.DBMS_OUTPUT.PUT_LINE('ACTUALIZACIÓN EXITOSA DEL PARÁMETRO TABLEROS DE TRANSFERENCIA');
        
    UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
    SET VALOR3 = 'AACC',
        USR_ULT_MOD='acudco',
        FE_ULT_MOD=SYSDATE,
        IP_ULT_MOD='127.0.0.1'
    WHERE PARAMETRO_ID = INT_ID_PARAMETRO
        AND VALOR1='AIRE ACONDICIONADO';
    SYS.DBMS_OUTPUT.PUT_LINE('ACTUALIZACIÓN EXITOSA DEL PARÁMETRO AIRE ACONDICIONADO');    
             
    UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
    SET VALOR3 = 'GEN',
        USR_ULT_MOD='acudco',
        FE_ULT_MOD=SYSDATE,
        IP_ULT_MOD='127.0.0.1'    
    WHERE PARAMETRO_ID = INT_ID_PARAMETRO
        AND VALOR1='GENERADOR';
    SYS.DBMS_OUTPUT.PUT_LINE('ACTUALIZACIÓN EXITOSA DEL PARÁMETRO: GENERADOR');
    
    UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
    SET VALOR3 = 'RECT',
        USR_ULT_MOD='acudco',
        FE_ULT_MOD=SYSDATE,
        IP_ULT_MOD='127.0.0.1'    
    WHERE PARAMETRO_ID = INT_ID_PARAMETRO
        AND VALOR1='RECTIFICADOR';   
    SYS.DBMS_OUTPUT.PUT_LINE('ACTUALIZACIÓN EXITOSA DEL PARÁMETRO: RECTIFICADOR');
    
    UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
    SET VALOR3 = 'TRAN',
        USR_ULT_MOD='acudco',
        FE_ULT_MOD=SYSDATE,
        IP_ULT_MOD='127.0.0.1'    
    WHERE PARAMETRO_ID = INT_ID_PARAMETRO
        AND VALOR1='TRANSFORMADOR';
    SYS.DBMS_OUTPUT.PUT_LINE('ACTUALIZACIÓN EXITOSA DEL PARÁMETRO: TRANSFORMADOR');    
        
    COMMIT;
    
    EXCEPTION
    WHEN OTHERS THEN
      SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                               || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
      ROLLBACK;
END;
/