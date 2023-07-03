/*
Se configuran las razones sociales que estan solicitando el monitoreo de sus enlaces de datos
*/


DECLARE
  ln_id_param NUMBER := 0;
BEGIN


INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'PROYECTO MONITOREO CLIENTES GRUPO BRAVCO',
    'PARAMETROS UTILIZADOS EN EL PROYECTO MONITOREO CLIENTES GRUPO BRAVCO',
    'INFRAESTRUCTURA',
    'ACTIVAR SERVICIO',
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
  WHERE NOMBRE_PARAMETRO = 'PROYECTO MONITOREO CLIENTES GRUPO BRAVCO';
    

--RAZONES SOCIALES A CONFIGURAR    
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      ln_id_param,
      'RAZON SOCIAL GRUPO BRAVCO', -- descripcion
      'GRUPO BRAVCO S.A.', --valor 1
      '313', --export --valor 2
      '312', --import --valor 3
      'BRAVCO',
      'Activo',
      'rcabrera',
      sysdate,
      '127.0.0.1',
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL      
    );  

    INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      ln_id_param,
      'RAZON SOCIAL GRUPO BRAVCO',
      'BANCO PICHINCHA C.A',
      '313', --export
      '312', --import
      'BRAVCO',
      'Activo',
      'rcabrera',
      sysdate,
      '127.0.0.1',
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL      
    );

    INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      ln_id_param,
      'RAZON SOCIAL GRUPO BRAVCO',
      'OTECEL S . A.',
      NULL, --export
      NULL, --import
      'TELEFONICA',
      'Activo',
      'rcabrera',
      sysdate,
      '127.0.0.1',
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL
    );


  COMMIT;
    

  SYS.DBMS_OUTPUT.PUT_LINE('Insertado Correctamente');
EXCEPTION
WHEN OTHERS THEN
  ROLLBACK;
DBMS_OUTPUT.put_line('Error: '||sqlerrm);  
END;

/




