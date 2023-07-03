set serveroutput on;
 /
DECLARE
  Lv_Mensaje VARCHAR2(2000);
BEGIN 
    DB_COMERCIAL.CMKG_ING_MASIVO_CONTACTOS.P_ING_MASIVO_CONTACTOS(
    Lv_Mensaje => Lv_Mensaje
  );
SYS.DBMS_OUTPUT.PUT_LINE('Lv_Mensaje = ' || Lv_Mensaje);
 
END;
/


