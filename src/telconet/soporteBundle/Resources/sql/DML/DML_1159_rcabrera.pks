DECLARE
  ln_id_param NUMBER := 0;
BEGIN

/* Insert utilizados para configurar parámetros utilizados para el envió de notificaciones de casos tipo backbone */
INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'PARAMETROS PROYECTO NORIFICACIONES BACKBONE',
    'PARAMETROS UTILIZADOS EN EL PROYECTO DE NOTIFICACIONES BACKBONE',
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
  WHERE NOMBRE_PARAMETRO = 'PARAMETROS PROYECTO NORIFICACIONES BACKBONE';
    

/* Se configura la razón social utilizada en el envió de notificaciones Backbone */

  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      ln_id_param,
      'RAZON SOCIAL BANCO BOLIVARIANO',
      'Banco Bolivariano C.A.',
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
      NULL
    );         
    
    
  COMMIT;
    

  SYS.DBMS_OUTPUT.PUT_LINE('Insertado Correctamente');
EXCEPTION
WHEN OTHERS THEN
  ROLLBACK;
DBMS_OUTPUT.put_line('Error: '||sqlerrm);  
END;
