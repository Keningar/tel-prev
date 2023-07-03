INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'TELCOGRAPH',
    'TELCOGRAPH',
    'TECNICO',
    NULL,
    'Activo',
    'jbozada',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL
  );

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TELCOGRAPH'),
    'VALOR_FACTURACION',
    '1000.00',
    'ou=telconet,ou=clientes,dc=telconet,dc=net',
    '',
    NULL,
    'Activo',
    'jbozada',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    10
  );

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TELCOGRAPH'),
    'RUTA_LDAP_CLIENTES',
    'ou=telconet,ou=clientes,dc=telconet,dc=net',
    'aplicaciones,appTelcograph',
    '',
    NULL,
    'Activo',
    'jbozada',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    10
  );

COMMIT;


DECLARE
  Ln_Aplicacion_NCam_Id NUMBER;
BEGIN
  --Crear Aplicacion 
  INSERT
  INTO DB_TOKENSECURITY.APPLICATION VALUES
    (
      DB_TOKENSECURITY.SEQ_APPLICATION.NEXTVAL,
      'APP.TELCOGRAPH',
      'ACTIVO',
      30
    );

  -- Obtener id de la aplicacion 
  SELECT id_application
  INTO Ln_Aplicacion_NCam_Id
  FROM DB_TOKENSECURITY.APPLICATION
  WHERE name = 'APP.TELCOGRAPH';

  --Configurar clase TecnicoWSController y relacionarlo con el APP.TELCOGRAPH
  INSERT
  INTO DB_TOKENSECURITY.WEB_SERVICE VALUES
    (
      DB_TOKENSECURITY.SEQ_WEB_SERVICE.nextval,
      'TecnicoWSController',
      'procesarAction',
      1,
      'ACTIVO',
      Ln_Aplicacion_NCam_Id
    );

  --Configurar Usuario/Clave TELCOGRAPH/TELCOGRAPH(sha256)
  INSERT
  INTO DB_TOKENSECURITY.USER_TOKEN VALUES
    (
      DB_TOKENSECURITY.SEQ_USER_TOKEN.nextval,
      'TELCOGRAPH',
      'bb35db2cb968f2c1da76d223e1ea2e51b98f83cdf3af2f169372006fee81910d',
      'Activo',
      Ln_Aplicacion_NCam_Id
    );
  COMMIT;
  SYS.DBMS_OUTPUT.PUT_LINE('Registros insertados Correctamente');
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '||SQLERRM);
  ROLLBACK;
END;
/


Insert into DB_COMERCIAL.ADMI_CARACTERISTICA (ID_CARACTERISTICA,DESCRIPCION_CARACTERISTICA,TIPO_INGRESO,ESTADO,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,TIPO)
values (DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,'PASSWORD_PORTAL','C','Activo',sysdate,'jbozada',null,null,'TECNICA');

Insert into DB_COMERCIAL.ADMI_CARACTERISTICA (ID_CARACTERISTICA,DESCRIPCION_CARACTERISTICA,TIPO_INGRESO,ESTADO,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,TIPO)
values (DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,'HOST_LOGIN_AUX','C','Activo',sysdate,'jbozada',null,null,'TECNICA');

Insert into DB_COMERCIAL.ADMI_CARACTERISTICA (ID_CARACTERISTICA,DESCRIPCION_CARACTERISTICA,TIPO_INGRESO,ESTADO,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,TIPO)
values (DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,'URL_TELCOGRAPH','C','Activo',sysdate,'jbozada',null,null,'TECNICA');

Insert into DB_COMERCIAL.ADMI_CARACTERISTICA (ID_CARACTERISTICA,DESCRIPCION_CARACTERISTICA,TIPO_INGRESO,ESTADO,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,TIPO)
values (DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,'ORGANIZACION_TELCOGRAPH','C','Activo',sysdate,'jbozada',null,null,'TECNICA');

Insert into DB_COMERCIAL.ADMI_CARACTERISTICA (ID_CARACTERISTICA,DESCRIPCION_CARACTERISTICA,TIPO_INGRESO,ESTADO,FE_CREACION,USR_CREACION,FE_ULT_MOD,USR_ULT_MOD,TIPO)
values (DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,'ZBX_ZABBIX_ID','C','Activo',sysdate,'jbozada',null,null,'TECNICA');

COMMIT;




--Insertando nueva plantilla
describe  DB_COMUNICACION.ADMI_PLANTILLA;

--Insertando nueva plantilla
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
    'Notificacion al activar el monitoreo de un servicio enviada al cliente',
    'ACTMONITSERVCLI',
    'TECNICO',
    'Activo',
    CURRENT_TIMESTAMP,
    'jbozada',
    TO_CLOB('
     <html>
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
                                El presente correo es para informarle la activación del monitoreo del servicio detallado a continuación: 
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
                        TO_CLOB('
                        <tr>
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
                                <strong>{{tipoProductoPlan}}:</strong>
                            </td>
                            <td>
				{{ nombreProductoPlan }} 	
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
                            <td>{{ observacion }}</td>
                        </tr>
	    		{% endif %}	
                        <tr>
                            <td>
                                <strong>Estado:</strong>
                            </td>
                            <td><strong><label style="color:red">{{ estadoServicio }}</label></strong></td>
                        </tr>
                        <tr>
                            <td>
                                <strong>IP Host:</strong>
                            </td>
                            <td>
				{{ ipHost }} 	
                            </td>
                        </tr>') ||
                        TO_CLOB('
                        {% if proceso ==''CREDENCIALES'' %}
                        <tr>
                            <td colspan="2">
                                <hr />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align: center;">
                                <strong>Datos de acceso al portal</strong>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <hr />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Url Portal Telcograph:</strong>
                            </td>
                            <td>{{ urlTelcograph }}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Usuario:</strong>
                            </td>
                            <td>{{ usrApp }}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Password:</strong>
                            </td>
                            <td>{{ passApp }}</td>
                        </tr>
	    		{% endif %}
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
		{% if prefijoEmpresa == ''10'' %} 
		<td><strong><font size="2" face="Tahoma">Telconet S.A.</font></strong></p> 
		{% endif %} 
		</td>      
            </tr>  
        </table>
    </body>
</html>
    ')
  );

DECLARE
  LN_PLANTILLA_ID NUMBER;
  LN_ALIAS_ID1    NUMBER;
BEGIN
  SELECT ID_PLANTILLA
  INTO LN_PLANTILLA_ID
  FROM DB_COMUNICACION.ADMI_PLANTILLA
  WHERE CODIGO='ACTMONITSERVCLI';
  SELECT ID_ALIAS
  INTO LN_ALIAS_ID1
  FROM DB_COMUNICACION.ADMI_ALIAS
  WHERE VALOR='jbozada@telconet.ec' ;
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
      LN_ALIAS_ID1,
      LN_PLANTILLA_ID,
      'Activo',
      CURRENT_TIMESTAMP,
      'jbozada',
      'NO'
    );

  SELECT ID_ALIAS
  INTO LN_ALIAS_ID1
  FROM DB_COMUNICACION.ADMI_ALIAS
  WHERE VALOR='fbermeo@telconet.ec' ;
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
      LN_ALIAS_ID1,
      LN_PLANTILLA_ID,
      'Activo',
      CURRENT_TIMESTAMP,
      'jbozada',
      'NO'
    );
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '||SQLERRM);
  ROLLBACK;
END;
/

/* INICIO - SE COLOCAN SCRIPTS QUE YA FUERON EJECUTADOS POR DLOPEZ, SE REGISTRA EN EL VERSIONAMIENTO COMO EVIDENCIA DE LO EJECUTADO,
   NO DEBE VOLVER A SER EJECUTADO EN EL PASE A PRODUCCION. */
/*BEGIN
    --dbms_network_acl_admin.create_acl(acl => 'resolve.xml',description => 'resolve acl', principal =>'TELCONET', is_grant => true, privilege => 'resolve');
    DBMS_NETWORK_ACL_ADMIN.assign_acl (acl          => 'utl_http.xml',
                                       HOST         => 'apps.telconet.ec',
                                       lower_port   => 80,
                                       upper_port   => 80);
    COMMIT;
END;
/

BEGIN
    --dbms_network_acl_admin.create_acl(acl => 'resolve.xml',description => 'resolve acl', principal =>'TELCONET', is_grant => true, privilege => 'resolve');
    DBMS_NETWORK_ACL_ADMIN.assign_acl (acl          => 'utl_http.xml',
                                       HOST         => 'telcos-lb.telconet.ec',
                                       lower_port   => 80,
                                       upper_port   => 80);
    COMMIT;
END;
/
BEGIN
    --dbms_network_acl_admin.create_acl(acl => 'resolve.xml',description => 'resolve acl', principal =>'TELCONET', is_grant => true, privilege => 'resolve');
    DBMS_NETWORK_ACL_ADMIN.assign_acl (acl          => 'utl_http.xml',
                                       HOST         => 'telcos-lb.telconet.ec',
                                       lower_port   => 443,
                                       upper_port   => 443);
    COMMIT;
END;
/

BEGIN
    DBMS_NETWORK_ACL_ADMIN.add_privilege (acl          => 'utl_http.xml',
                                          principal    => 'DB_INFRAESTRUCTURA',
                                          is_grant     => TRUE,
                                          privilege    => 'connect',
                                          start_date   => NULL,
                                          end_date     => NULL);
END;

BEGIN
    DBMS_NETWORK_ACL_ADMIN.add_privilege (acl          => 'utl_http.xml',
                                          principal    => 'DB_INFRAESTRUCTURA',
                                          is_grant     => TRUE,
                                          privilege    => 'resolve',
                                          start_date   => NULL,
                                          end_date     => NULL);
END;
/
COMMIT;*/

/* FIN - SE COLOCAN SCRIPTS QUE YA FUERON EJECUTADOS POR DLOPEZ, SE REGISTRA EN EL VERSIONAMIENTO COMO EVIDENCIA DE LO EJECUTADO,
   NO DEBE VOLVER A SER EJECUTADO EN EL PASE A PRODUCCION. */