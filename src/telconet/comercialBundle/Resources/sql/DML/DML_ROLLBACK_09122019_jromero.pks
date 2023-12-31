--Programa anónimo para eliminar los parámetros de envío de pin
DECLARE
  Lv_parametro_CANALES_ENVIO_PIN VARCHAR2(50);
  Lv_PARAMETROS_ENVIO_PIN VARCHAR2(50);
  
BEGIN

  Lv_parametro_CANALES_ENVIO_PIN    := 'CANALES_ENVIO_PIN';
  Lv_PARAMETROS_ENVIO_PIN := 'PARAMETROS_ENVIO_PIN';
  
  delete FROM DB_GENERAL.ADMI_PARAMETRO_DET 
  WHERE PARAMETRO_ID IN (
    SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
    WHERE NOMBRE_PARAMETRO IN (Lv_parametro_CANALES_ENVIO_PIN,Lv_PARAMETROS_ENVIO_PIN));
    
    delete FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
    WHERE NOMBRE_PARAMETRO IN (Lv_parametro_CANALES_ENVIO_PIN,Lv_PARAMETROS_ENVIO_PIN);
    
  COMMIT;
  SYS.DBMS_OUTPUT.PUT_LINE('Reverso exitoso');
exception
  WHEN others THEN
  ROLLBACK;
  SYS.DBMS_OUTPUT.PUT_LINE('Se produjo el siguiente error: '|| SQLERRM);
end;
/
