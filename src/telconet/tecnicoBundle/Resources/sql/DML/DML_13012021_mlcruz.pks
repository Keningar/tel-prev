SET SERVEROUTPUT ON
--Creación de parámetros para productos con su respectivo flujo de grupo
DECLARE
  Ln_IdParamsServiciosMd    NUMBER;
BEGIN
  SELECT ID_PARAMETRO
  INTO Ln_IdParamsServiciosMd
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='PARAMETROS_ASOCIADOS_A_SERVICIOS_MD';

  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Nombres técnicos parametrizados para buscar el precio del producto en el detalle PRECIOS_PRODUCTOS',
    'NOMBRES_TECNICOS_PRECIOS_PRODUCTOS_PARAMETRIZADOS',
    'WIFI_DUAL_BAND',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );

  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Nombres técnicos parametrizados para buscar el precio del producto en el detalle PRECIOS_PRODUCTOS',
    'NOMBRES_TECNICOS_PRECIOS_PRODUCTOS_PARAMETRIZADOS',
    'EXTENDER_DUAL_BAND',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );

  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Nombres técnicos parametrizados para buscar el precio del producto en el detalle PRECIOS_PRODUCTOS',
    'NOMBRES_TECNICOS_PRECIOS_PRODUCTOS_PARAMETRIZADOS',
    'WDB_Y_EDB',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );

  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Nombres técnicos parametrizados para buscar el precio del producto en el detalle PRECIOS_PRODUCTOS',
    'NOMBRES_TECNICOS_PRECIOS_PRODUCTOS_PARAMETRIZADOS',
    'PARAMOUNT',
    'SI',
    NULL,
    NULL,
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );

  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Nombres técnicos parametrizados para buscar el precio del producto en el detalle PRECIOS_PRODUCTOS',
    'NOMBRES_TECNICOS_PRECIOS_PRODUCTOS_PARAMETRIZADOS',
    'NOGGIN',
    'SI',
    NULL,
    NULL,
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );

  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Confirmación del servicio NETLIFEPLAY / PARAMOUNT+',
    'PRODUCTOS_POR_FLUJO_DE_GRUPO',
    'FLUJO_GRUPO_TV',
    'PRODS_POR_NOMBRE_TECNICO',
    'PARAMOUNT',
    'PARAMOUNT',
    'PARAMOUNT-NUEVO',
    'SMS_NUEVO_PARAM',
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18',
    'Paramount'
  );
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Confirmación del servicio NETLIFEPLAY / Noggin',
    'PRODUCTOS_POR_FLUJO_DE_GRUPO',
    'FLUJO_GRUPO_TV',
    'PRODS_POR_NOMBRE_TECNICO',
    'NOGGIN',
    'NOGGIN',
    'NOGGIN-NUEVO',
    'SMS_NUEVO_NOGG',
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18',
    'Noggin'
  );
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Valores parametrizados de producto que no se pueden evaluar por función precio',
    'PRECIOS_PRODUCTOS',
    '1320',
    '4.017',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18'
  );
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Valores parametrizados de producto que no se pueden evaluar por función precio',
    'PRECIOS_PRODUCTOS',
    '1321',
    '2.23',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18'
  );
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Información de la key usada para encriptar y desencriptar claves en Telcos',
    'KEY_SECRET_TELCOS',
    'c69555ab183de6672b1ebf6100bbed59186a5d72',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );

  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Información del usuario creado para generar token desde procesos masivos invocados desde la BD',
    'INFO_TOKEN_PROCESOS_MASIVOS_BD',
    'http://apps.telconet.ec/ws/token-security/rest/token/authentication',
    'APP.PROCESOS_MASIVOS_BD_SMS',
    'PROCESOS_MASIVOS_BD_SMS',
    'MASIVOS_BD_SMS',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );

  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    VALOR6,
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Información del usuario creado para generar token desde procesos masivos invocados desde la BD',
    'INFO_CONSUMO_WS_SMS',
    'http://apps.telconet.ec/rs/sms/ws/rest/enviar',
    'https://extranet.netlife.ec/rs/sms/ws/rest/notificar',
    NULL,
    NULL,
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );

  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Estados de servicios adicionales asociados a un producto que han sido facturados al cliente',
    'ESTADOS_SERVICIOS_PROD_ADICIONALES_A_CONSIDERAR',
    'BUSQUEDA_POR_NOMBRE_TECNICO',
    'CAMBIO_PLAN_MASIVO',
    'PARAMOUNT',
    'Activo',
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
  ( 
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Estados de servicios adicionales asociados a un producto que han sido facturados al cliente',
    'ESTADOS_SERVICIOS_PROD_ADICIONALES_A_CONSIDERAR',
    'BUSQUEDA_POR_NOMBRE_TECNICO',
    'CAMBIO_PLAN_MASIVO',
    'NOGGIN',
    'Activo',
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se ha ingresado correctamente los parámetros para el cambio de plan masivo de PARAMOUNT Y NOGGIN');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
/
--Parámetros para token usado desde la opción de envío de SMS POR cambio de plan masivo con productos adicionales
DECLARE
  Ln_IdAplicacion NUMBER;
BEGIN
  --Crear Aplicacion 
  INSERT
  INTO DB_TOKENSECURITY.APPLICATION VALUES
    (
      DB_TOKENSECURITY.SEQ_APPLICATION.NEXTVAL,
      'APP.PROCESOS_MASIVOS_BD_SMS',
      'ACTIVO',
      30
    );

  -- Obtener id de la aplicacion 
  SELECT id_application
  INTO Ln_IdAplicacion
  FROM DB_TOKENSECURITY.APPLICATION
  WHERE name = 'APP.PROCESOS_MASIVOS_BD_SMS';

  --Configurar clase TecnicoWSController y relacionarlo con el APP.PROCESOS_MASIVOS_BD_SMS
  INSERT
  INTO DB_TOKENSECURITY.WEB_SERVICE VALUES
    (
      DB_TOKENSECURITY.SEQ_WEB_SERVICE.nextval,
      'SmsWSController',
      'procesarAction',
      1,
      'ACTIVO',
      Ln_IdAplicacion
    );

  --Configurar Usuario/Clave PROCESOS_MASIVOS_BD_SMS/MASIVOS_BD_SMS(sha256)
  INSERT
  INTO DB_TOKENSECURITY.USER_TOKEN VALUES
    (
      DB_TOKENSECURITY.SEQ_USER_TOKEN.nextval,
      'PROCESOS_MASIVOS_BD_SMS',
      'D4CA3CB9A8C09B2CFC47217580A62FB2F0BA6E599580BB2C3C9EC0B6CA1B9D4F',
      'Activo',
      Ln_IdAplicacion
    );
  COMMIT;
  SYS.DBMS_OUTPUT.PUT_LINE('Registros para el token insertados correctamente');
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '||SQLERRM);
  ROLLBACK;
END;
/
SET DEFINE OFF;
--Plantilla usada para notificar si ocurriera un error al ejecutar el cambio de plan masivo con flujo adicional
DECLARE
  Ln_IdPlantilla NUMBER(5,0);
BEGIN
  INSERT
  INTO DB_COMUNICACION.ADMI_PLANTILLA
    (
      ID_PLANTILLA,
      NOMBRE_PLANTILLA,
      CODIGO,
      MODULO,
      ESTADO,
      FE_CREACION,
      USR_CREACION,
      PLANTILLA
    )
    VALUES
    (
      DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,
      'Notificación enviada al ocurrir un error en el cambio de plan masivo',
      'ERRORCPMGENERAL',
      'TECNICO',
      'Activo',
      CURRENT_TIMESTAMP,
      'mlcruz',
      TO_CLOB('<html>
    <head>
        <meta http-equiv=Content-Type content="text/html; charset=UTF-8">
    </head>
    <body>
        <table align="center" width="100%" cellspacing="0" cellpadding="5">
            <tr>
                <td align="center" style="border:1px solid #6699CC;background-color:#E5F2FF;">
                    <img alt=""  src="http://images.telconet.net/others/telcos/logo.png"/>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid #6699CC;">
                    <table width="100%" cellspacing="0" cellpadding="5">
                        <tr>
                            <td colspan="2">Estimado personal,</td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                El presente correo es para informarle que se ha presentado un error al ejecutar el proceso de cambio de plan masivo: 
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <hr />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align: center;">
                                <strong>Datos Cliente</strong>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <hr />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Cliente:</strong>
                            </td>
                            <td>{{ CLIENTE }}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Login:</strong>
                            </td>
                            <td>{{ LOGIN }}</td>
                        </tr>') ||
                        TO_CLOB('
                        <tr>
                            <td>
                                <strong>Observaci&oacute;n:</strong>
                            </td>
                            <td>{{ OBSERVACION }}</td>
                        </tr>') ||
                        TO_CLOB('<tr>
                            <td colspan="2"><br/></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    
                </td>
            </tr>
            <tr> 
		<td><strong><font size="2" face="Tahoma">MegaDatos S.A.</font></strong></p>
		</td>      
            </tr>  
        </table>
    </body>
</html>
    ')
  );
  SELECT ID_PLANTILLA
  INTO Ln_IdPlantilla
  FROM DB_COMUNICACION.ADMI_PLANTILLA
  WHERE CODIGO='ERRORCPMGENERAL';
  INSERT
  INTO DB_COMUNICACION.INFO_ALIAS_PLANTILLA
    (
      ID_ALIAS_PLANTILLA,
      ALIAS_ID,
      PLANTILLA_ID,
      ESTADO,
      FE_CREACION,
      USR_CREACION,
      ES_COPIA
    )
    VALUES
    (
      DB_COMUNICACION.SEQ_INFO_ALIAS_PLANTILLA.NEXTVAL,
      363,--mlcruz@telconet.ec
      Ln_IdPlantilla,
      'Activo',
      SYSDATE,
      'mlcruz',
      'NO'
    );
  INSERT
  INTO DB_COMUNICACION.INFO_ALIAS_PLANTILLA
    (
      ID_ALIAS_PLANTILLA,
      ALIAS_ID,
      PLANTILLA_ID,
      ESTADO,
      FE_CREACION,
      USR_CREACION,
      ES_COPIA
    )
    VALUES
    (
      DB_COMUNICACION.SEQ_INFO_ALIAS_PLANTILLA.NEXTVAL,
      283,--calidad@netlife.net.ec
      Ln_IdPlantilla,
      'Activo',
      SYSDATE,
      'mlcruz',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Se creó la plantilla correctamente ERRORCPMGENERAL con sus respectivos alias');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' 
                           || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/