--Se actualiza el valor de anillo en los sw: sw1nnitelefonica.telconet.net y sw1nnitelefonicauio.telconet.net
update DB_INFRAESTRUCTURA.info_detalle_elemento set detalle_valor = 'V-R1' where id_detalle_elemento = 2160794;
update DB_INFRAESTRUCTURA.info_detalle_elemento set detalle_valor = 'V-R2' where id_detalle_elemento = 2164283;

/*Se realizan estas configuraciones con el objetivo de mapear el valor de anillos que no son numericos*/
DECLARE
  ln_id_param NUMBER := 0;
BEGIN
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
      'MAPEO_ANILLO_INTERCONEXION',
      'VALORES UTILIZADOS POR ANILLOS QUE ESTAN DEFINIDOS COMO VALORES NO NUMERICOS',
      'TECNICO',
      'GENERAR_VLAN',
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
  WHERE NOMBRE_PARAMETRO = 'MAPEO_ANILLO_INTERCONEXION';

  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      ln_id_param,
      'VALORES DE ANILLO',
      'V-R1',
      '0',
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


  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      ln_id_param,
      'VALORES DE ANILLO',
      'V-R2',
      '0',
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
  
  DBMS_OUTPUT.PUT_LINE('Insertado Correctamente');
EXCEPTION
WHEN OTHERS THEN
  ROLLBACK;
  DBMS_OUTPUT.put_line('Error: '||sqlerrm);
END;

/