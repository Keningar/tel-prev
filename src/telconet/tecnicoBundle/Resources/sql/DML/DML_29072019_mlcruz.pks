SET SERVEROUTPUT ON
--Se agrega la parametrización con los nombres de los antivirus usados dentro de los planes y como productos adicionales
DECLARE
  Ln_IdParamAntivirus NUMBER(5,0);
BEGIN
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_CAB
    (
      ID_PARAMETRO,
      NOMBRE_PARAMETRO,
      DESCRIPCION,
      ESTADO,
      USR_CREACION,
      FE_CREACION,
      IP_CREACION
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
      'ANTIVIRUS_PLANES_Y_PRODS_MD',
      'Antivirus que actualmente se usa dentro de los planes y como productos adicionales de MD',
      'Activo',
      'mlcruz',
      CURRENT_TIMESTAMP,
      '127.0.0.1'
    );
  SELECT ID_PARAMETRO
  INTO Ln_IdParamAntivirus
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='ANTIVIRUS_PLANES_Y_PRODS_MD'
  AND ESTADO = 'Activo';
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
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamAntivirus,
    'Antivirus que actualmente se usa dentro de los planes y como productos adicionales de MD',
    'NUEVO',
    'KASPERSKY',
    'I. PROTEGIDO MULTI PAID',
    'KISMD',
    'XG8CrRzggVhmSkZxSoU/ro4+tW/6ehn+n3u9pqiRtwo=',
    'PILOTO',
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
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
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamAntivirus,
    'Antivirus que se usaba dentro de los planes y como productos adicionales de MD',
    'ANTERIOR',
    'MCAFEE',
    'I. PROTEGIDO MULTI PAID',
    NULL,
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
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
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamAntivirus,
    'Antivirus que se usaba dentro de los planes y como productos adicionales de MD',
    'ANTERIOR',
    'MCAFEE',
    'I. PROTEGIDO MULTI TRIAL',
    NULL,
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
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
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamAntivirus,
    'Antivirus que se usaba dentro de los planes y como productos adicionales de MD',
    'ANTERIOR',
    'MCAFEE',
    'I. PROTECCION TOTAL TRIAL',
    NULL,
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
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
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamAntivirus,
    'Antivirus que se usaba dentro de los planes y como productos adicionales de MD',
    'ANTERIOR',
    'MCAFEE',
    'I. PROTECCION TOTAL PAID',
    NULL,
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se crearon correctamente los detalles del parámetro ANTIVIRUS_PLANES_Y_PRODS_MD');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
--Se agrega parámetro con con logines parametrizados para el piloto de licencias Kaspersky
DECLARE
BEGIN
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_CAB
    (
      ID_PARAMETRO,
      NOMBRE_PARAMETRO,
      DESCRIPCION,
      ESTADO,
      USR_CREACION,
      FE_CREACION,
      IP_CREACION
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
      'LOGINES_PILOTO_KASPERSKY',
      'Logines de MD permitidos para realizar el piloto de licencias Kaspersky',
      'Activo',
      'mlcruz',
      CURRENT_TIMESTAMP,
      '127.0.0.1'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Se creó el parámetro LOGINES_PILOTO_KASPERSKY');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
--Asociación de nuevas características para licencias Kaspersky
--El producto ya debe tener asociadas las características CANTIDAD DISPOSITIVOS, CORREO ELECTRONICO, TIENE INTERNET, NUMERO REINTENTOS
DECLARE
  Ln_IdProducto                   NUMBER(5,0) := 210;
  Ln_IdCaractAntivirus            NUMBER(5,0);
  Ln_IdCaractSuscriberId          NUMBER(5,0);
  Ln_IdCaractMigracionKaspersky   NUMBER(5,0);
  Ln_IdCaractCodProdKaspersky     NUMBER(5,0);
  Ln_IdCaractCancelLogica         NUMBER(5,0);
  Lv_EstadoActivo               VARCHAR2(6) := 'Activo';
BEGIN
  Ln_IdCaractAntivirus := DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL;
  INSERT
  INTO DB_COMERCIAL.ADMI_CARACTERISTICA
    (
      ID_CARACTERISTICA,
      DESCRIPCION_CARACTERISTICA,
      TIPO_INGRESO,
      ESTADO,
      FE_CREACION,
      USR_CREACION,
      FE_ULT_MOD,
      USR_ULT_MOD,
      TIPO
    )
    VALUES
    (
      Ln_IdCaractAntivirus,
      'ANTIVIRUS',
      'T',
      'Activo',
      SYSDATE,
      'mlcruz',
      NULL,
      NULL,
      'COMERCIAL'
    );
  INSERT
  INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
      ID_PRODUCTO_CARACTERISITICA,
      PRODUCTO_ID,
      CARACTERISTICA_ID,
      FE_CREACION,
      USR_CREACION,
      ESTADO,
      VISIBLE_COMERCIAL
    )
    VALUES
    (
      DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
      Ln_IdProducto,
      Ln_IdCaractAntivirus,
      CURRENT_TIMESTAMP,
      'mlcruz',
      Lv_EstadoActivo,
      'SI'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creación correctamente de la asociación de producto y la característica ANTIVIRUS');

  Ln_IdCaractSuscriberId := DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL;
  INSERT
  INTO DB_COMERCIAL.ADMI_CARACTERISTICA
    (
      ID_CARACTERISTICA,
      DESCRIPCION_CARACTERISTICA,
      TIPO_INGRESO,
      ESTADO,
      FE_CREACION,
      USR_CREACION,
      FE_ULT_MOD,
      USR_ULT_MOD,
      TIPO
    )
    VALUES
    (
      Ln_IdCaractSuscriberId,
      'SUSCRIBER_ID',
      'N',
      'Activo',
      SYSDATE,
      'mlcruz',
      NULL,
      NULL,
      'TECNICA'
    );
  INSERT
  INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
      ID_PRODUCTO_CARACTERISITICA,
      PRODUCTO_ID,
      CARACTERISTICA_ID,
      FE_CREACION,
      USR_CREACION,
      ESTADO,
      VISIBLE_COMERCIAL
    )
    VALUES
    (
      DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
      Ln_IdProducto,
      Ln_IdCaractSuscriberId,
      CURRENT_TIMESTAMP,
      'mlcruz',
      Lv_EstadoActivo,
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creación correctamente de la asociación de producto y la característica SUSCRIBER_ID');

  Ln_IdCaractMigracionKaspersky := DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL;
  INSERT
  INTO DB_COMERCIAL.ADMI_CARACTERISTICA
    (
      ID_CARACTERISTICA,
      DESCRIPCION_CARACTERISTICA,
      TIPO_INGRESO,
      ESTADO,
      FE_CREACION,
      USR_CREACION,
      FE_ULT_MOD,
      USR_ULT_MOD,
      TIPO
    )
    VALUES
    (
      Ln_IdCaractMigracionKaspersky,
      'MIGRADO_A_KASPERSKY',
      'N',
      'Activo',
      SYSDATE,
      'mlcruz',
      NULL,
      NULL,
      'TECNICA'
    );
  INSERT
  INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
      ID_PRODUCTO_CARACTERISITICA,
      PRODUCTO_ID,
      CARACTERISTICA_ID,
      FE_CREACION,
      USR_CREACION,
      ESTADO,
      VISIBLE_COMERCIAL
    )
    VALUES
    (
      DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
      Ln_IdProducto,
      Ln_IdCaractMigracionKaspersky,
      CURRENT_TIMESTAMP,
      'mlcruz',
      Lv_EstadoActivo,
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creación correctamente de la asociación de producto y la característica MIGRADO_A_KASPERSKY');

  INSERT
  INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
      ID_PRODUCTO_CARACTERISITICA,
      PRODUCTO_ID,
      CARACTERISTICA_ID,
      FE_CREACION,
      USR_CREACION,
      ESTADO,
      VISIBLE_COMERCIAL
    )
    VALUES
    (
      DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
      209,
      Ln_IdCaractMigracionKaspersky,
      CURRENT_TIMESTAMP,
      'mlcruz',
      Lv_EstadoActivo,
      'NO'
    );
  INSERT
  INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
      ID_PRODUCTO_CARACTERISITICA,
      PRODUCTO_ID,
      CARACTERISTICA_ID,
      FE_CREACION,
      USR_CREACION,
      ESTADO,
      VISIBLE_COMERCIAL
    )
    VALUES
    (
      DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
      211,
      Ln_IdCaractMigracionKaspersky,
      CURRENT_TIMESTAMP,
      'mlcruz',
      Lv_EstadoActivo,
      'NO'
    );
  INSERT
  INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
      ID_PRODUCTO_CARACTERISITICA,
      PRODUCTO_ID,
      CARACTERISTICA_ID,
      FE_CREACION,
      USR_CREACION,
      ESTADO,
      VISIBLE_COMERCIAL
    )
    VALUES
    (
      DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
      212,
      Ln_IdCaractMigracionKaspersky,
      CURRENT_TIMESTAMP,
      'mlcruz',
      Lv_EstadoActivo,
      'NO'
    );

  Ln_IdCaractCodProdKaspersky := DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL;
  INSERT
  INTO DB_COMERCIAL.ADMI_CARACTERISTICA
    (
      ID_CARACTERISTICA,
      DESCRIPCION_CARACTERISTICA,
      TIPO_INGRESO,
      ESTADO,
      FE_CREACION,
      USR_CREACION,
      FE_ULT_MOD,
      USR_ULT_MOD,
      TIPO
    )
    VALUES
    (
      Ln_IdCaractCodProdKaspersky,
      'CODIGO_PRODUCTO',
      'T',
      'Activo',
      SYSDATE,
      'mlcruz',
      NULL,
      NULL,
      'TECNICA'
    );
  INSERT
  INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
      ID_PRODUCTO_CARACTERISITICA,
      PRODUCTO_ID,
      CARACTERISTICA_ID,
      FE_CREACION,
      USR_CREACION,
      ESTADO,
      VISIBLE_COMERCIAL
    )
    VALUES
    (
      DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
      Ln_IdProducto,
      Ln_IdCaractCodProdKaspersky,
      CURRENT_TIMESTAMP,
      'mlcruz',
      Lv_EstadoActivo,
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creación correctamente de la asociación de producto y la característica CODIGO_PRODUCTO');

  Ln_IdCaractCancelLogica := DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL;
  INSERT
  INTO DB_COMERCIAL.ADMI_CARACTERISTICA
    (
      ID_CARACTERISTICA,
      DESCRIPCION_CARACTERISTICA,
      TIPO_INGRESO,
      ESTADO,
      FE_CREACION,
      USR_CREACION,
      FE_ULT_MOD,
      USR_ULT_MOD,
      TIPO
    )
    VALUES
    (
      Ln_IdCaractCancelLogica,
      'PERMITE_CANCELACION_LOGICA',
      'T',
      'Activo',
      SYSDATE,
      'mlcruz',
      NULL,
      NULL,
      'TECNICA'
    );
  INSERT
  INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
      ID_PRODUCTO_CARACTERISITICA,
      PRODUCTO_ID,
      CARACTERISTICA_ID,
      FE_CREACION,
      USR_CREACION,
      ESTADO,
      VISIBLE_COMERCIAL
    )
    VALUES
    (
      DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
      Ln_IdProducto,
      Ln_IdCaractCancelLogica,
      CURRENT_TIMESTAMP,
      'mlcruz',
      Lv_EstadoActivo,
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creación correctamente de la asociación de producto y la característica PERMITE_CANCELACION_LOGICA');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
DECLARE
  Ln_IdProdCaractAntivirus      NUMBER(5,0);
  Ln_IdProductoIProtegMultiPaid NUMBER(3,0) := 210;
  Lv_EstadoActivo               VARCHAR2(6) := 'Activo';
  Lv_DescripcionAntivirus       VARCHAR2(9) := 'ANTIVIRUS';
  CURSOR Lc_GetPlanesConMcAfee
  IS
    SELECT DISTINCT PLAN_CAB.ID_PLAN, PLAN_CAB.NOMBRE_PLAN, PLAN_DET.ID_ITEM
    FROM DB_COMERCIAL.INFO_PLAN_CAB PLAN_CAB
    INNER JOIN DB_COMERCIAL.INFO_PLAN_DET PLAN_DET
    ON PLAN_CAB.ID_PLAN =  PLAN_DET.PLAN_ID
    INNER JOIN DB_COMERCIAL.ADMI_PRODUCTO PRODUCTO
    ON PRODUCTO.ID_PRODUCTO =  PLAN_DET.PRODUCTO_ID
    WHERE PLAN_CAB.ESTADO = Lv_EstadoActivo
    AND PLAN_DET.ESTADO = Lv_EstadoActivo
    AND PRODUCTO.ID_PRODUCTO = Ln_IdProductoIProtegMultiPaid;
  TYPE Lt_FetchArray IS TABLE OF Lc_GetPlanesConMcAfee%ROWTYPE;
  Lt_PlanesConMcAfee Lt_FetchArray;
  Le_BulkErrors EXCEPTION;
  PRAGMA EXCEPTION_INIT(Le_BulkErrors, -24381);
BEGIN
  SELECT ID_PRODUCTO_CARACTERISITICA
  INTO Ln_IdProdCaractAntivirus
  FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA PROD_CARACT
  INNER JOIN DB_COMERCIAL.ADMI_CARACTERISTICA CARACT
  ON CARACT.ID_CARACTERISTICA = PROD_CARACT.CARACTERISTICA_ID
  WHERE PROD_CARACT.ESTADO = Lv_EstadoActivo
  AND PROD_CARACT.PRODUCTO_ID = Ln_IdProductoIProtegMultiPaid
  AND CARACT.DESCRIPCION_CARACTERISTICA = Lv_DescripcionAntivirus
  AND CARACT.ESTADO = Lv_EstadoActivo;
  IF Lc_GetPlanesConMcAfee%ISOPEN THEN
    CLOSE Lc_GetPlanesConMcAfee;
  END IF;
  OPEN Lc_GetPlanesConMcAfee;
  LOOP
    FETCH Lc_GetPlanesConMcAfee BULK COLLECT INTO Lt_PlanesConMcAfee LIMIT 1000;
 
    FORALL Ln_Index IN 1..Lt_PlanesConMcAfee.COUNT SAVE EXCEPTIONS
    INSERT
    INTO DB_COMERCIAL.INFO_PLAN_PRODUCTO_CARACT
    (
      ID_PLAN_PRODUCTO_CARACT,
      PLAN_DET_ID,
      PRODUCTO_CARACTERISITICA_ID,
      VALOR,
      FE_CREACION,
      USR_CREACION,
      ESTADO
    )
    VALUES
    (
      DB_COMERCIAL.SEQ_INFO_PLAN_PRODUCTO_CARACT.NEXTVAL,
      Lt_PlanesConMcAfee(Ln_Index).ID_ITEM,
      Ln_IdProdCaractAntivirus,
      'KASPERSKY',
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo'
    );
    EXIT WHEN Lc_GetPlanesConMcAfee%NOTFOUND;
  END LOOP;
  CLOSE Lc_GetPlanesConMcAfee;
  COMMIT;
  SYS.DBMS_OUTPUT.PUT_LINE('Se asoció la característica ANTIVIRUS al producto I. PROTEGIDO MULTI PAID dentro de los planes');
EXCEPTION
WHEN Le_BulkErrors THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' 
                            || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' 
                            || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
SET DEFINE OFF;
--Plantilla usada para notificar que no se ha podido cancelar licencias I. PROTEGIDO MULTI PAID
DECLARE
  Ln_IdPlantilla NUMBER(5,0);
  Ln_IdAlias     NUMBER(5,0);
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
      'Notificación enviada al no cancelarse el producto I. PROTEGIDO MULTI PAID',
      'ERRORCANCELIPMP',
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
                                El presente correo es para informarle que no se ha podido cancelar el {{ nombreProducto }} {{ descripcionServicio }}
                                del servicio detallado a continuaci&oacute;n: 
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
                            <td>{{ cliente }}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Login:</strong>
                            </td>
                            <td>{{ login }}</td>
                        </tr>') ||
                        TO_CLOB('<tr>
                            <td>
                                <strong>Jurisdicci&oacute;n:</strong>
                            </td>
                            <td>
                                {{ nombreJurisdiccion }}	
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>{{ tipoServicio }}:</strong>
                            </td>
                            <td>
                                {{ nombreServicio }} 	
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Observaci&oacute;n:</strong>
                            </td>
                            <td>{{ observacion | raw }}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Estado:</strong>
                            </td>
                            <td><strong><label style="color:red">{{ estadoServicio }}</label></strong></td>
                        </tr>') ||
                        TO_CLOB('
                        <tr>
                            <td>
                                <strong>Email:</strong>
                            </td>
                            <td><strong><label style="color:red">{{ correoSuscripcion }}</label></strong></td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Suscriber Id:</strong>
                            </td>
                            <td><strong><label style="color:red">{{ suscriberId }}</label></strong></td>
                        </tr>
                        <tr>
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
  WHERE CODIGO='ERRORCANCELIPMP';
  INSERT
  INTO DB_COMUNICACION.ADMI_ALIAS
    (
      ID_ALIAS,
      VALOR,
      ESTADO,
      EMPRESA_COD,
      CANTON_ID,
      DEPARTAMENTO_ID,
      FE_CREACION,
      USR_CREACION
    )
    VALUES
    (
      DB_COMUNICACION.SEQ_ADMI_ALIAS.NEXTVAL,
      'serviciosuio@netlife.net.ec',
      'Activo',
      '18',
      NULL,
      NULL,
      SYSDATE,
      'mlcruz'
    );
  SELECT ID_ALIAS
  INTO Ln_IdAlias
  FROM DB_COMUNICACION.ADMI_ALIAS
  WHERE VALOR     ='serviciosuio@netlife.net.ec'
  AND ESTADO      = 'Activo'
  AND EMPRESA_COD = '18';
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
      Ln_IdAlias,
      Ln_IdPlantilla,
      'Activo',
      SYSDATE,
      'mlcruz',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Se creó la plantilla correctamente ERRORCANCELIPMP');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' 
                           || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
--Plantilla usada para notificar que no se ha podido activar las licencias I. PROTEGIDO MULTI PAID y se replican los alias de ERROR_MCAFEE
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
      'Notificación enviada al no activarse el producto I. PROTEGIDO MULTI PAID',
      'ERRORACTIVAIPMP',
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
                                El presente correo es para informarle que no se ha podido activar el {{ nombreProducto }} {{ descripcionServicio }}
                                del servicio detallado a continuaci&oacute;n: 
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
                            <td>{{ cliente }}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Login:</strong>
                            </td>
                            <td>{{ login }}</td>
                        </tr>') ||
                        TO_CLOB('<tr>
                            <td>
                                <strong>Jurisdicci&oacute;n:</strong>
                            </td>
                            <td>
                                {{ nombreJurisdiccion }}	
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>{{ tipoServicio }}:</strong>
                            </td>
                            <td>
                                {{ nombreServicio }} 	
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Observaci&oacute;n:</strong>
                            </td>
                            <td>{{ observacion | raw }}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Estado:</strong>
                            </td>
                            <td><strong><label style="color:red">{{ estadoServicio }}</label></strong></td>
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
  WHERE CODIGO='ERRORACTIVAIPMP';
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
      136,--jvillacis@netlife.net.ec
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
      143,--soporte@netlife.net.ec
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
      377,--lbarahona@netlife.net.ec
      Ln_IdPlantilla,
      'Activo',
      SYSDATE,
      'mlcruz',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Se creó la plantilla correctamente ERRORACTIVAIPMP con sus respectivos alias');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' 
                           || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/