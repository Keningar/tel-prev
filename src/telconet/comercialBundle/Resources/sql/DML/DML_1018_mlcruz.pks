SET SERVEROUTPUT ON
--Creación de la asociación de la característica NO_TRADICIONAL_FLUJO al producto Ip Small Business
DECLARE
  Ln_IdCaractNoTradicional  NUMBER(5,0);
  Ln_IdCaractTraslado       NUMBER(5,0);
  Ln_IdProdIpSmallBusiness  NUMBER(5,0);
  Lv_EstadoActivo           VARCHAR2(6) := 'Activo';
BEGIN
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractNoTradicional
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='NO_TRADICIONAL_FLUJO'
  AND ESTADO = Lv_EstadoActivo;
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractTraslado
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='TRASLADO'
  AND ESTADO = Lv_EstadoActivo;
  SELECT ID_PRODUCTO
  INTO Ln_IdProdIpSmallBusiness
  FROM DB_COMERCIAL.ADMI_PRODUCTO
  WHERE NOMBRE_TECNICO='IPSB';
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
      Ln_IdProdIpSmallBusiness,
      Ln_IdCaractNoTradicional,
      CURRENT_TIMESTAMP,
      'mlcruz',
      Lv_EstadoActivo,
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creación correctamente de registro Producto IP SMALL BUSINESS Característica NO_TRADICIONAL_FLUJO');
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
      Ln_IdProdIpSmallBusiness,
      Ln_IdCaractTraslado,
      CURRENT_TIMESTAMP,
      'mlcruz',
      Lv_EstadoActivo,
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creación correctamente de registro Producto IP SMALL BUSINESS Característica TRASLADO');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
/
--Insertando nueva plantilla al dar factibilidad a un servicio Small Business con tipo de orden traslado 
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
    'Notificación al dar factibilidad a un servicio Small Business con tipo de orden Traslado',
    'FACTIB_T_SB',
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
                                El presente correo es para informarle la factibilidad del servicio detallado a continuación: 
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
                            <td>{{ loginPuntoCliente }}</td>
                        </tr>') ||
                        TO_CLOB('<tr>
                            <td>
                                <strong>Jurisdicción:</strong>
                            </td>
                            <td>
                                {{ nombreJurisdiccion }}	
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Dirección:</strong>
                            </td>
                            <td>{{ direccionPuntoCliente }}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Producto:</strong>
                            </td>
                            <td>
                                {{ nombreProducto }} 	
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Tipo de Orden:</strong>
                            </td>
                            <td>
                                {{ tipoOrden }} 	
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Fecha de Creación del Servicio:</strong>
                            </td>
                            <td>{{ fechaCreacionServicio }}</td>
                        </tr>
                        {% if observacion!='''' %}
                        <tr>
                            <td>
                                <strong>Observación:</strong>
                            </td>
                            <td>{{ observacion | raw }}</td>
                        </tr>
                        {% endif %}	
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
		<td><strong><font size="2" face="Tahoma">Telconet S.A.</font></strong></p>
		</td>      
            </tr>  
        </table>
    </body>
</html>
    ')
  );
/
SET DEFINE OFF;
DECLARE
  Ln_IdParamNotifSb NUMBER(5,0);
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
      'INFO_NOTIF_FACTIB_T_SB',
      'Información general de la notificación que se enviará al dar factibilidad a un servicio Small Business con tipo de orden traslado',
      'Activo',
      'mlcruz',
      CURRENT_TIMESTAMP,
      '127.0.0.1'
    );

  SELECT ID_PARAMETRO
  INTO Ln_IdParamNotifSb
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='INFO_NOTIF_FACTIB_T_SB'
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
    Ln_IdParamNotifSb,
    'Parámetro con las validaciones de olt para obtener el correspondiente mensaje al dar factibilidad',
    'SI',
    'El traslado de ubicaci&oacute;n del cliente implica una reasignaci&oacute;n de OLT por lo que la direcci&oacute;n IP WAN '
    || 'y posibles direcciones IP p&uacute;blicas adicionales asignadas deber&aacute;n cambiar',
    '',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    '10'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se creó correctamente el detalle del parámetro INFO_NOTIF_FACTIB_T_SB');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;