/*Se configuran los alias de IPCCL1 y IPCCL2 para el envio de notificaciones de casos a clientes*/

DECLARE
  ln_id_param NUMBER := 0;
BEGIN
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
      'PARAMETROS PROYECTO NOTIFICACIONES CASOS CLIENTE',
      'PARAMETROS UTILIZADOS EN EL PROYECTO DE NOTIFICACIONES CASO CLIENTE',
      'SOPORTE',
      'NOTIFICACIONES',
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
  WHERE NOMBRE_PARAMETRO = 'PARAMETROS PROYECTO NOTIFICACIONES CASOS CLIENTE';
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      ln_id_param,
      'ALIAS_CREAR_CASO_CLIENTE',
      'ipcc_l1_gye@telconet.ec|ipcc_l1_uio@telconet.ec|ipcc_l2_gye@telconet.ec|ipcc_l2_uio@telconet.ec|',
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
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      ln_id_param,
      'ALIAS_CERRAR_CASO_CLIENTE',
      'ipcc_l1_gye@telconet.ec|ipcc_l1_uio@telconet.ec|ipcc_l2_gye@telconet.ec|ipcc_l2_uio@telconet.ec|',
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
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      ln_id_param,
      'ALIAS_SEGUI_CASO_CLIENTE',
      'ipcc_l1_gye@telconet.ec|ipcc_l1_uio@telconet.ec|ipcc_l2_gye@telconet.ec|ipcc_l2_uio@telconet.ec|',
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
