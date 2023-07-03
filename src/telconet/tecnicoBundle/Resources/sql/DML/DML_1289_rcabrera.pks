
DECLARE
  ln_id_param NUMBER := 0;
BEGIN


--Se crea el nuevo parametro asocida al proyecto de subredes por cliente
INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'PARAMETROS PROYECTO SUBREDES BG',
    'PARAMETROS UTILIZADOS EN EL PROYECTO DE SUBREDES PARA EL BANCO GUAYAQUIL',
    'INFRAESTRUCTURA',
    'ASIGNAR RECURSOS DE RED',
    'Activo',
    'rcabrera',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL
  );    

                
  --Se obtiene el id del registro recien creado
  SELECT id_parametro
  INTO ln_id_param
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO = 'PARAMETROS PROYECTO SUBREDES BG';
    

  --Se configuran las subredes
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      ln_id_param,
      'SUBRED ESPECIFICA',
      '200.93.226.144/29',
      '525944', --PERSONA BANCO GUAYAQUIL S.A.
      '577836', --PE
      NULL,
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
      'SUBRED ESPECIFICA',
      '200.93.226.152/29',
      '525944', --PERSONA BANCO GUAYAQUIL S.A.
      '577836', --PE
      NULL,
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
    

  --Se configura el cliente
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      ln_id_param,
      'CLIENTE CONFIGURADO',
      'BANCO GUAYAQUIL S.A.',
      '525944', --ID_PERSONA
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

--Se ingresan nuevas subredes requeridas
INSERT
INTO DB_INFRAESTRUCTURA.INFO_SUBRED VALUES
  (
    DB_INFRAESTRUCTURA.SEQ_INFO_SUBRED.NEXTVAL,
    NULL,
    '200.93.226.144/29',
    '255.255.255.248',
    '200.93.226.145',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    'Activo',
    577836,
    NULL,
    '200.93.226.146',
    '200.93.226.150',
    NULL,
    'WAN',
    'INTMPLS',
    NULL,
    '10',
    NULL,
    NULL,
    'IPv4',
    2
  );

INSERT
INTO DB_INFRAESTRUCTURA.INFO_SUBRED VALUES
  (
    DB_INFRAESTRUCTURA.SEQ_INFO_SUBRED.NEXTVAL,
    NULL,
    '200.93.226.152/29',
    '255.255.255.248',
    '200.93.226.153',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    'Activo',
    577836,
    NULL,
    '200.93.226.154',
    '200.93.226.158',
    NULL,
    'WAN',
    'INTMPLS',
    NULL,
    '10',
    NULL,
    NULL,
    'IPv4',
    3
  );

  COMMIT;















