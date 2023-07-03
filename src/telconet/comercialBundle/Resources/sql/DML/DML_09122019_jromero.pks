--********************Programa anónimo para la inserción de los parámetros necesarios para el envío de pin en contrato digital*******
DECLARE

  Lv_usuario                    VARCHAR2(10);
  Ln_id_parametro               NUMBER;
  Ln_num_cabeceras              NUMBER;
  Ln_num_detalles               number;
  
  
  Lv_parametro_medios_envio     VARCHAR2(50);
  Lv_detalle_medios_envio_sms   VARCHAR2(50);
  Lv_valor_medios_envio_sms     VARCHAR2(50);
  Lv_valor2_medios_envio_sms    varchar2(10);
  Lv_detalle_medios_envio_mail  VARCHAR2(50);
  Lv_valor_medios_envio_mail    VARCHAR2(50);
  Lv_parametro_envio_pin        VARCHAR2(50);
  Lv_detalle_envio_pin_mensaje  VARCHAR2(50);
  Lv_valor_envio_pin_mensaje    VARCHAR2(500);
  Lv_detalle_envio_pin_asunto   VARCHAR2(50);
  Lv_valor_envio_pin_asunto     varchar2(50);
BEGIN
--inicialización de valores principales
  Ln_num_cabeceras              := 0;
  Ln_num_detalles               := 0;
  
  Lv_usuario                    := 'jromero';
  Lv_parametro_medios_envio     := 'CANALES_ENVIO_PIN';
  Lv_detalle_medios_envio_sms   := 'ENVIO_POR_SMS';
  Lv_valor_medios_envio_sms     := 'SMS';
  Lv_valor2_medios_envio_sms    := 'INFOBIP';
  Lv_detalle_medios_envio_mail  := 'ENVIO_POR_MAIL';
  Lv_valor_medios_envio_mail    := 'MAIL';
  
  Lv_parametro_envio_pin        := 'PARAMETROS_ENVIO_PIN';
  Lv_detalle_envio_pin_mensaje  := 'MENSAJE_ENVIO_PIN';
  Lv_valor_envio_pin_mensaje    := 'Gracias por Elegir a Netlife como su proveedor de Internet.\n El codigo Netlife para validar su firma digital es: ';
  Lv_detalle_envio_pin_asunto   := 'ASUNTO_ENVIO_PIN';
  Lv_valor_envio_pin_asunto     := 'Pin de Instalación';
  
  --*****************************Inserción de parámetros para CANALES_ENVIO_PIN****************************************
  --Inserción de cabecera para parámetro CANALES_ENVIO_PIN
  SELECT DB_GENERAL.seq_admi_parametro_cab.nextval INTO Ln_id_parametro  FROM dual;
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB (ID_PARAMETRO, NOMBRE_PARAMETRO, DESCRIPCION, MODULO, PROCESO, ESTADO, USR_CREACION,
    FE_CREACION, IP_CREACION, USR_ULT_MOD, FE_ULT_MOD, IP_ULT_MOD)
    VALUES(
    Ln_id_parametro, Lv_parametro_medios_envio, 'Contiene los canales de envío para el pin de instalación', 'COMERCIAL', 
    'CONTRATO_DIGITAL', 'Activo', Lv_usuario, SYSDATE,'127.0.0.1', NULL, NULL, NULL
    );
    Ln_num_cabeceras := Ln_num_cabeceras + 1;
  --Inserción de los detalles del parámetro CANALES_ENVIO_PIN
    --Inserción de detalle para envío por sms.
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, ESTADO,
    USR_CREACION, FE_CREACION, IP_CREACION, USR_ULT_MOD, FE_ULT_MOD, IP_ULT_MOD, VALOR5, EMPRESA_COD, VALOR6, VALOR7, OBSERVACION)
    VALUES(
    (DB_GENERAL.seq_admi_parametro_det.nextval), Ln_id_parametro, Lv_detalle_medios_envio_sms, Lv_valor_medios_envio_sms,
    Lv_valor2_medios_envio_sms, NULL, NULL, 'Activo', Lv_usuario, SYSDATE, '127.0.0.1', NULL, NULL, NULL, NULL, 18, NULL, NULL, 
    'En valor2 debe configurarse el nombre del proveedor de envío de sms (INFOBIP o MASSEND)'
    );
    Ln_num_detalles := Ln_num_detalles + 1;
    --Inserción de detalle para envío por mail.
    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, ESTADO,
    USR_CREACION, FE_CREACION, IP_CREACION, USR_ULT_MOD, FE_ULT_MOD, IP_ULT_MOD, VALOR5, EMPRESA_COD, VALOR6, VALOR7, OBSERVACION)
    VALUES(
    (DB_GENERAL.seq_admi_parametro_det.nextval), Ln_id_parametro, Lv_detalle_medios_envio_mail, Lv_valor_medios_envio_mail,
    NULL, NULL, NULL, 'Activo', Lv_usuario, SYSDATE, '127.0.0.1', NULL, NULL, NULL, NULL, 18, NULL, NULL, 
    'null'
    );
     Ln_num_detalles := Ln_num_detalles + 1;
  --*********************************************************************************************************************
  --*****************************Inserción de parámetros para PARAMETROS_ENVIO_PIN****************************************
  --Inserción de cabecera para parámetro CANALES_ENVIO_PIN
  SELECT DB_GENERAL.seq_admi_parametro_cab.nextval INTO Ln_id_parametro  FROM dual;
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB (ID_PARAMETRO, NOMBRE_PARAMETRO, DESCRIPCION, MODULO, PROCESO, ESTADO, USR_CREACION,
    FE_CREACION, IP_CREACION, USR_ULT_MOD, FE_ULT_MOD, IP_ULT_MOD)
    VALUES(
    Ln_id_parametro, Lv_parametro_envio_pin, 'Contiene los parámetros necesarios para el envío del pin', 'COMERCIAL', 
    'CONTRATO_DIGITAL', 'Activo', Lv_usuario, SYSDATE,'127.0.0.1', NULL, NULL, NULL
    );
  Ln_num_cabeceras := Ln_num_cabeceras + 1;
  --Inserción de los detalles del parámetro CANALES_ENVIO_PIN
    --Inserción de detalle para envío por sms.
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, ESTADO,
    USR_CREACION, FE_CREACION, IP_CREACION, USR_ULT_MOD, FE_ULT_MOD, IP_ULT_MOD, VALOR5, EMPRESA_COD, VALOR6, VALOR7, OBSERVACION)
    VALUES(
    (DB_GENERAL.seq_admi_parametro_det.nextval), Ln_id_parametro, Lv_detalle_envio_pin_mensaje, Lv_valor_envio_pin_mensaje,
    null, NULL, NULL, 'Activo', Lv_usuario, SYSDATE, '127.0.0.1', NULL, NULL, NULL, NULL, 18, NULL, NULL, null
    );
     Ln_num_detalles := Ln_num_detalles + 1;
    --Inserción de detalle para envío por mail.
    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (ID_PARAMETRO_DET, PARAMETRO_ID, DESCRIPCION, VALOR1, VALOR2, VALOR3, VALOR4, ESTADO,
    USR_CREACION, FE_CREACION, IP_CREACION, USR_ULT_MOD, FE_ULT_MOD, IP_ULT_MOD, VALOR5, EMPRESA_COD, VALOR6, VALOR7, OBSERVACION)
    VALUES(
    (DB_GENERAL.seq_admi_parametro_det.nextval), Ln_id_parametro, Lv_detalle_envio_pin_asunto, Lv_valor_envio_pin_asunto,
    NULL, NULL, NULL, 'Activo', Lv_usuario, SYSDATE, '127.0.0.1', NULL, NULL, NULL, NULL, 18, NULL, NULL, 'null'
    );
     Ln_num_detalles := Ln_num_detalles + 1;
  --************************************************************************************************************************
    COMMIT;
    SYS.DBMS_OUTPUT.PUT_LINE('Inserción correcta. Parámetros insertados: '|| Ln_num_cabeceras|| '\n Detalles insertados: '||Ln_num_detalles);
exception
  WHEN others THEN
    ROLLBACK;
    SYS.DBMS_OUTPUT.PUT_LINE('Se produjo el siguiente error: '|| SQLERRM);
end;
/
