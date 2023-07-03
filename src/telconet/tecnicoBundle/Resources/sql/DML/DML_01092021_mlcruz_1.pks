SET SERVEROUTPUT ON
SET DEFINE OFF
--Creación de nueva plantilla para el envío de correo al crear la tarea por revisión de reporte
DECLARE
  Ln_IdPlantilla NUMBER(5,0);
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
      'NOTIFICACIÓN DE ASIGNACIÓN DE TAREA POR REVISIÓN DE REPORTE DE CORTE MASIVO POR LOTES',
      'TAREACORTEXLOTE',
      'TECNICO',
      '<html>
  <head>
    <meta http-equiv=Content-Type content="text/html; charset=UTF-8">
  </head>
  <body>
    <table align="center" width="100%" cellspacing="0" cellpadding="5">
      <tr>
        <td align="center" style="border:1px solid #6699CC;background-color:#E5F2FF;">
            <img alt=""  src="http://images.telconet.net/others/sit/notificaciones/logo.png"/>
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
                El presente correo es para indicarle que se asigno la siguiente TAREA: 
              </td>
            </tr>
            <tr>
              <td colspan="2">
                <hr />
              </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center;">
                <strong>Actividad # {{NUMERO_TAREA}}</strong>
              </td>
            </tr>
            <tr>
              <td colspan="2">
                <hr />
              </td>
            </tr>   
             <tr>
                <td>
                   <strong>Nombre Proceso:</strong>
                </td>
                <td>
		    {{NOMBRE_PROCESO}}
                </td>
            </tr>
            <tr>
                <td>
                   <strong>Nombre Tarea:</strong>
                </td>
                <td>
		    {{NOMBRE_TAREA}}
                </td>
            </tr>
             <tr>
              <td colspan="2">
                <hr />
              </td>
            </tr>                        
            <tr>
              <td>
                <strong>Fecha de asignacion:</strong>
              </td>
              <td>{{FECHA_ASIGNACION}}</td>
            </tr>
            <tr>
              <td>
                <strong>Usuario que asigna:</strong>
              </td>
              <td>{{USUARIO_ASIGNA}}</td>
            </tr>
            <tr>
              <td>
                <strong>Asignado:</strong>
              </td>
              <td>{{ASIGNADO}}</td>
            </tr>            
            <tr>
              <td>
                <strong>Observacion:</strong>
              </td>
              <td>{{OBSERVACION}}</td>
            </tr>
            <tr>
              <td colspan="2"><br/></td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
          <td>&nbsp;
          </td>
      </tr>
      <tr>
          <td>Por favor revisar la Actividad <a href="https://telcos.telconet.ec/soporte/call_activity/{{NUMERO_TAREA}}/show">{{NUMERO_TAREA}}</a>
          </td>   
      </tr>
      <tr>
          <td><strong><font size="2" face="Tahoma">Telcos + Sistema del Grupo Telconet</font></strong></p>
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
  WHERE CODIGO='TAREACORTEXLOTE';
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
  SYS.DBMS_OUTPUT.PUT_LINE('Se creó la plantilla correctamente TAREACORTEXLOTE');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' 
                            || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
--Creación de nueva plantilla para el envío de correo del reporte generado por el corte masivo por lotes
DECLARE
  Ln_IdPlantilla NUMBER(5,0);
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
      'NOTIFICACIÓN AUTOMÁTICA CON LOS SERVICIOS EJECUTADOS EN EL PROCESO MASIVO DE CORTE POR LOTES',
      'RPTECORTEXLOTES',
      'TECNICO',
      '<html>
<head>
    <meta http-equiv=Content-Type content="text/html; charset=UTF-8">
    <style type="text/css">
        table.cssTable {
            font-family: verdana, arial, sans-serif;
            font-size: 11px;
            color: #333333;
            border-width: 1px;
            border-color: #999999;
            border-collapse: collapse;
        }
        
        table.cssTable th {
            background-color: #c3dde0;
            border-width: 1px;
            padding: 8px;
            border-style: solid;
            border-color: #a9c6c9;
        }
        
        table.cssTable tr {
            background-color: #d4e3e5;
        }
        
        table.cssTable td {
            border-width: 1px;
            padding: 8px;
            border-style: solid;
            border-color: #a9c6c9;
        }
        
        table.cssTblPrincipal {
            font-family: verdana, arial, sans-serif;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <table class="cssTblPrincipal" align="center" width="100%" cellspacing="0" cellpadding="5">
        <tr>
            <td align="center" style="border:1px solid #6699CC;background-color:#E5F2FF;"><img alt="" 
            src="http://images.telconet.net/others/sit/notificaciones/logo.png" /></td>
        </tr>
        <tr>
            <td style="border:1px solid #6699CC;">
                <table width="100%" cellspacing="0" cellpadding="5">
                    <tr>
                        <td colspan="2">Estimado personal,</td>
                    </tr>
                    <tr>
                        <td colspan="2">El presente correo es para indicarle que se finalizó el proceso masivo de corte por lotes</td>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="2">Se adjunta el archivo con el detalle de los clientes procesados.</td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                    </tr>
                </table>
            </td>
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
  WHERE CODIGO='RPTECORTEXLOTES';
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
      394,--informaticos@netlife.net.ec
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
      8,--cobranzasgye@netlife.net.ec
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
      11,--cobranzasuio@netlife.net.ec
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
      363,--mlcruz@telconet.ec
      Ln_IdPlantilla,
      'Activo',
      SYSDATE,
      'mlcruz',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Se creó la plantilla correctamente RPTECORTEXLOTES');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' 
                            || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/