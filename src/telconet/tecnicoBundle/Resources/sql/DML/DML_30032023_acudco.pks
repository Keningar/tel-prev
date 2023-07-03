/**
 *  
 * Se realiza el script para ingresar los parametros que agrupan a los modelos de equipos no regularizados
 * @author Geovanny Cudco <acudco@telconet.ec>
 * @version 1.0 30-03-2023
 */

SET SERVEROUTPUT ON; 
DECLARE
    INT_ID_PARAMETRO NUMBER;
BEGIN 
    -- INSERCIÓN DEL PARÁMETRO
    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
    VALUES(DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
           'EQUIPOS SIN MODELO',
           'EQUIPOS A INSTALARSE QUE NO TIENEN MODELO',
           'TECNICO',
           'NODO',
           'Activo',
           'acudco',
           sysdate,
           '127.0.0.1',
           NULL,
           NULL,
           NULL);    
    SYS.DBMS_OUTPUT.PUT_LINE('INSERCIÓN EXITOSA DEL PARÁMETRO QUE IDENTIFICA A LOS EQUIPOS SIN MODELO');   
    
    -- CAPTURA DEL ID DEL PARÁMETRO
    SELECT ID_PARAMETRO
    INTO INT_ID_PARAMETRO
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
    WHERE NOMBRE_PARAMETRO = 'EQUIPOS SIN MODELO';  
    
    -- INSERCIÓN DE LOS DETALLES    
    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (id_parametro_det,PARAMETRO_ID,DESCRIPCION,VALOR1,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
    VALUES(DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,INT_ID_PARAMETRO,'IDENTIFICA AL MODELO S/M','S/M','Activo','acudco',SYSDATE,'127.0.0.1');
    SYS.DBMS_OUTPUT.PUT_LINE('INSERCIÓN EXITOSA DEL PARÁMETRO S/M');
    
    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (id_parametro_det,PARAMETRO_ID,DESCRIPCION,VALOR1,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
    VALUES(DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,INT_ID_PARAMETRO,'IDENTIFICA AL MODELO SM','SM','Activo','acudco',SYSDATE,'127.0.0.1');
    SYS.DBMS_OUTPUT.PUT_LINE('INSERCIÓN EXITOSA DEL PARÁMETRO SM');
    
    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (id_parametro_det,PARAMETRO_ID,DESCRIPCION,VALOR1,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
    VALUES(DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,INT_ID_PARAMETRO,'IDENTIFICA AL MODELO N/A','N/A','Activo','acudco',SYSDATE,'127.0.0.1');
    SYS.DBMS_OUTPUT.PUT_LINE('INSERCIÓN EXITOSA DEL PARÁMETRO N/A');
    
    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (id_parametro_det,PARAMETRO_ID,DESCRIPCION,VALOR1,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
    VALUES(DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,INT_ID_PARAMETRO,'IDENTIFICA AL MODELO NA','NA','Activo','acudco',SYSDATE,'127.0.0.1');
    SYS.DBMS_OUTPUT.PUT_LINE('INSERCIÓN EXITOSA DEL PARÁMETRO NA');
    
    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (id_parametro_det,PARAMETRO_ID,DESCRIPCION,VALOR1,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
    VALUES(DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,INT_ID_PARAMETRO,'IDENTIFICA AL MODELO No Aplica','No Aplica','Activo','acudco',SYSDATE,'127.0.0.1');
    SYS.DBMS_OUTPUT.PUT_LINE('INSERCIÓN EXITOSA DEL PARÁMETRO No Aplica');
    
    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (id_parametro_det,PARAMETRO_ID,DESCRIPCION,VALOR1,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
    VALUES(DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,INT_ID_PARAMETRO,'IDENTIFICA AL MODELO -','-','Activo','acudco',SYSDATE,'127.0.0.1');
    SYS.DBMS_OUTPUT.PUT_LINE('INSERCIÓN EXITOSA DEL PARÁMETRO -');
    
    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (id_parametro_det,PARAMETRO_ID,DESCRIPCION,VALOR1,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION)
    VALUES(DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,INT_ID_PARAMETRO,'IDENTIFICA AL MODELO "MODELO"','MODELO','Activo','acudco',SYSDATE,'127.0.0.1');
    SYS.DBMS_OUTPUT.PUT_LINE('INSERCIÓN EXITOSA DEL PARÁMETRO "MODELO');
    
    COMMIT;
        
    EXCEPTION
    WHEN OTHERS THEN
      SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                               || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
      ROLLBACK;
END;
/