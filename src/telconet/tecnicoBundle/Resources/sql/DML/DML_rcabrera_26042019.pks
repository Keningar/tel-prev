/*Se configura la cadena 'PLAN CLOUD' para que sea validada en la descripción de la factura y asi determinar si se permite pedir la información del equipo de seguridad lógica*/
DECLARE
  ln_id_param NUMBER := 0;
BEGIN
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
      'DESCRIPCION FACTURA SERVICIOS SEGURIDAD LOGICA',
      'PARAMETROS UTILIZADOS EN LA DESCRIPCION DE FACTURA PARA SERVICIOS SEGURIDAD LOGICA',
      'TECNICO',
      'ACTIVACION',
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
  WHERE NOMBRE_PARAMETRO = 'DESCRIPCION FACTURA SERVICIOS SEGURIDAD LOGICA';
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      ln_id_param,
      'CADENAS DE CARACTERES A VALIDAR DENTRO DE LA DESCRIPCION DE LA FACTURA',
      'PLAN CLOUD|plan cloud|',
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
 
