/*Se configuran los clientes que tienen permitido crear rutas estaticas*/
DECLARE
  ln_id_param NUMBER := 0;
BEGIN
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
      'PARAMETROS PROYECTO RUTA ESTATICA CLIENTE',
      'PARAMETROS UTILIZADOS EN EL PROYECTO DE RUTAS ESTATICAS POR CLIENTE',
      'TECNICO',
      'RUTA ESTATICA',
      'Activo',
      'rcabrera',
      sysdate,
      '127.0.0.1',
      NULL,
      NULL,
      NULL
    );
  SELECT id_parametro
  INTO ln_id_param
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO = 'PARAMETROS PROYECTO RUTA ESTATICA CLIENTE';
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      ln_id_param,
      'CLIENTES RUTA ESTATICA',
      '525944|',
      NULL,
      NULL,
      NULL,
      'Activo',
      'rcabrera',
      sysdate,
      '127.0.0.1',
      NULL,
      NULL,
      NULL,
      NULL,
      '10',
      NULL,
      NULL,
      NULL
    );  
  
  COMMIT;
  
  DBMS_OUTPUT.PUT_LINE('Insertado Correctamente');
EXCEPTION
WHEN OTHERS THEN
  ROLLBACK;
  DBMS_OUTPUT.put_line('Error: '||sqlerrm);
END;

/