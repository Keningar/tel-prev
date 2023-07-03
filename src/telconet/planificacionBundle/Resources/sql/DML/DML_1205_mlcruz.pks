/**
 *Creación de parámetro con el tiempo para rechazo de órdenes de servicio
 */
DECLARE
  Ln_IdParamTiempoRechazo NUMBER(5,0);
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
      'TIEMPOS_RECHAZA_SERVICIOS_DETENIDO',
      'Tiempo parametrizado por empresa para rechazar los servicios en estado Detenido',
      'Activo',
      'mlcruz',
      CURRENT_TIMESTAMP,
      '127.0.0.1'
    );
  SELECT ID_PARAMETRO
  INTO Ln_IdParamTiempoRechazo
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='TIEMPOS_RECHAZA_SERVICIOS_DETENIDO';
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
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamTiempoRechazo,
    'Tiempo establecido para realizar un rechazo de una orden de servicio',
    'TN',
    '30',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    10
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado el parámetro con el tiempo establecido como límite para el rechazo de solicitudes');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
/
SET DEFINE OFF 
--Creación de plantilla y alias que se enviará al realizar el rechazo automático de solicitudes de planificación
DECLARE
  Ln_IdPlantilla NUMBER(5,0);
  Ln_IdAlias     NUMBER(5,0);
BEGIN
  --Plantilla usada para notificar el rechazo automático de solicitudes de planificación
  INSERT
  INTO DB_COMUNICACION.ADMI_PLANTILLA
    (
      ID_PLANTILLA,
      NOMBRE_PLANTILLA,
      CODIGO,
      MODULO,
      PLANTILLA,
      ESTADO,
      FE_CREACION,
      USR_CREACION
    )
    VALUES
    (
      DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,
      'NOTIFICACIÓN AUTOMÁTICA AL REALIZAR RECHAZO DE SERVICIO DETENIDO',
      'RECHZ_SERV_AUT',
      'COMERCIAL',
      '<html>
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
                              <td colspan="2">Estimado personal,</strong></td>
                          </tr>
                          <tr>
                              <td colspan="2">
                                  El presente correo es para informarle que se procedió con el rechazo de los servicios por exceder el tiempo 
                                  de {{ TIEMPOPARAMETRO }} días en estado Detenido. <br>Se adjunta el archivo con los servicios rechazados.
                              </td>
                          </tr>
                      </table>
                  </td>
              </tr>
              <tr>
                  <td> </td>
              </tr>
              <tr>
                <td colspan="2">
                    <p><strong><font size="2" face="Tahoma">Telcos + Sistema del Grupo Telconet</font></strong></p>
                </td>
              </tr> 
          </table>
      </body>
  </html>',
      'Activo',
      CURRENT_TIMESTAMP,
      'mlcruz'
    );
  SELECT ID_PLANTILLA
  INTO Ln_IdPlantilla
  FROM DB_COMUNICACION.ADMI_PLANTILLA
  WHERE CODIGO='RECHZ_SERV_AUT';
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
      'pyl_corporativo@telconet.ec',
      'Activo',
      '10',
      NULL,
      NULL,
      SYSDATE,
      'mlcruz'
    );
  SELECT ID_ALIAS
  INTO Ln_IdAlias
  FROM DB_COMUNICACION.ADMI_ALIAS
  WHERE VALOR     ='pyl_corporativo@telconet.ec'
  AND ESTADO      = 'Activo'
  AND EMPRESA_COD = '10';
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
      DB_COMUNICACION.SEQ_ADMI_ALIAS.NEXTVAL,
      Ln_IdAlias,
      Ln_IdPlantilla,
      'Activo',
      SYSDATE,
      'mlcruz',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Se creó la plantilla correctamente RECHZ_SERV_AUT');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' 
                            || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
INSERT
INTO DB_COMUNICACION.ADMI_PLANTILLA
  (
    ID_PLANTILLA,
    NOMBRE_PLANTILLA,
    CODIGO,
    MODULO,
    PLANTILLA,
    ESTADO,
    FE_CREACION,
    USR_CREACION
  )
  VALUES
  (
    DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,
    'NOTIFICACIÓN AL VENDEDOR CUANDO SE RECHAZA UN SERVICIO DETENIDO',
    'RECHZ_SERV_VEND',
    'COMERCIAL',
    '<html>
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
                            <td colspan="2">Estimado personal:</td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                El presente correo es para informarle el estado del Servicio: 
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
                        </tr>
                        <tr>
                            <td>
                                <strong>Jurisdicci&oacute;n:</strong>
                            </td>
                            <td>{{ JURISDICCION }}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Ciudad:</strong>
                            </td>
                            <td>{{ CIUDAD }}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Producto:</strong>
                            </td>
                            <td>{{ PRODUCTO }}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>&Uacute;ltima fecha de gesti&oacute;n:</strong>
                            </td>
                            <td>{{ FECHA_DETENIDO }}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Observaci&oacute;n:</strong>
                            </td>
                            <td>{{ OBSERVACION_SERVICIO }}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Estado:</strong>
                            </td>
                            <td>{{ ESTADO_SERVICIO }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td> </td>
            </tr>
            <tr>
            	<td colspan="2">
                	<p><strong><font size="2" face="Tahoma">Telcos + Sistema del Grupo Telconet</font></strong></p>
            	</td>
            </tr> 
        </table>
    </body>
</html>',
    'Activo',
    CURRENT_TIMESTAMP,
    'mlcruz'
  );
COMMIT;
/