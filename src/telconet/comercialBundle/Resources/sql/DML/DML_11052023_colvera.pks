SET SERVEROUTPUT ON
--Inserción de parámetros para la restricción de nombres de usuario en servicios
DECLARE
  v_target    NUMBER;
BEGIN

  INSERT INTO DB_GENERAL.admi_parametro_cab (id_parametro,nombre_parametro,descripcion,
    modulo,estado,usr_creacion,fe_creacion,ip_creacion) VALUES 
    (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,'NOMBREUSUARIO_SUBCADENAS_NO_PERMITIDAS',
    'DEFINE EXPRESIONES REGULARES NO PERMITIDAS EN NOMBRES DE USUARIOS CREADOS POR EL SISTEMA',
    'COMERCIAL','Activo','colvera',CURRENT_DATE,'127.0.0.1');
    
    SELECT ID_PARAMETRO INTO v_target
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO='NOMBREUSUARIO_SUBCADENAS_NO_PERMITIDAS';
      
    INSERT INTO DB_GENERAL.admi_parametro_det (id_parametro_det,parametro_id,estado,usr_creacion,
    fe_creacion,ip_creacion,valor1,valor2 
    ) VALUES .*
    (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,v_target,'Activo','colvera',CURRENT_DATE,'127.0.0.1','.*${jndi.*','palabra_reservada');

    INSERT INTO DB_GENERAL.admi_parametro_det (id_parametro_det,parametro_id,estado,usr_creacion,
        fe_creacion,ip_creacion,valor1,valor2
        ) VALUES 
        (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,v_target,'Activo','colvera',CURRENT_DATE,'127.0.0.1','.*jndi:ldap.*','palabra_reservada');  
      
    INSERT INTO DB_GENERAL.admi_parametro_det (id_parametro_det,parametro_id,estado,usr_creacion,
        fe_creacion,ip_creacion,valor1,valor2
        ) VALUES 
        (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,v_target,'Activo','colvera',CURRENT_DATE,'127.0.0.1','.*Jndi:rmi.*','palabra_reservada'); 
        
    INSERT INTO DB_GENERAL.admi_parametro_det (id_parametro_det,parametro_id,estado,usr_creacion,
        fe_creacion,ip_creacion,valor1,valor2
        ) VALUES 
        (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,v_target,'Activo','colvera',CURRENT_DATE,'127.0.0.1','.*Jndi:DNS.*','palabra_reservada');
        
    INSERT INTO DB_GENERAL.admi_parametro_det (id_parametro_det,parametro_id,estado,usr_creacion,
        fe_creacion,ip_creacion,valor1,valor2
        ) VALUES 
        (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,v_target,'Activo','colvera',CURRENT_DATE,'127.0.0.1','.*LDAP}://.*','palabra_reservada');
        
    INSERT INTO DB_GENERAL.admi_parametro_det (id_parametro_det,parametro_id,estado,usr_creacion,
        fe_creacion,ip_creacion,valor1,valor2
        ) VALUES 
        (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,v_target,'Activo','colvera',CURRENT_DATE,'127.0.0.1','.*LDAPS}://.*','palabra_reservada');
        
    INSERT INTO DB_GENERAL.admi_parametro_det (id_parametro_det,parametro_id,estado,usr_creacion,
        fe_creacion,ip_creacion,valor1,valor2
        ) VALUES 
        (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,v_target,'Activo','colvera',CURRENT_DATE,'127.0.0.1','.*DNS}://.*','palabra_reservada');
        
    INSERT INTO DB_GENERAL.admi_parametro_det (id_parametro_det,parametro_id,estado,usr_creacion,
        fe_creacion,ip_creacion,valor1,valor2
        ) VALUES 
        (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,v_target,'Activo','colvera',CURRENT_DATE,'127.0.0.1','.*jndi.*','palabra_reservada');
  
  SYS.DBMS_OUTPUT.PUT_LINE('Se han Insertado los parámetros para restringir los nombres de usuarios en servicios');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;