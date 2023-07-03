SET SERVEROUTPUT ON
--Creación de nueva plantilla para el envío de correo con el consolidado del archivo procesado para la solicitud de agregar equipo
DECLARE
  Ln_IdPlantilla NUMBER(5,0);
BEGIN
  --Plantilla usada para notificar la solicitud de agregar equipo 
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
      'NOTIFICACIÓN AUTOMÁTICA CON EL DETALLE DE SERVICIOS POR ARCHIVO EN LA SOLICITUD DE AGREGAR EQUIPO',
      'SAESUBIDA',
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
                        <td colspan="2">El presente correo es para indicarle que se procedió a la solicitud de agregar equipos masivamente 
                            con {{NUM_REGISTROS_TOTAL}} registros de clientes.
                            <br>A continuación se presenta el número de registros procesados:</td>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <table class="cssTable" align="center">
                                <tr>
                                    <th># de Registros</th>
                                    <th>Estado</th>
                                    <th>Descripción</th>
                                </tr>
				{{CUERPO_CORREO}}
                            </table>
                        </td>
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
      'afayala'
    );
  SELECT ID_PLANTILLA
  INTO Ln_IdPlantilla
  FROM DB_COMUNICACION.ADMI_PLANTILLA
  WHERE CODIGO='SAESUBIDA';
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
      399,--afayala@telconet.ec
      Ln_IdPlantilla,
      'Activo',
      SYSDATE,
      'afayala',
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
      142,--pyl_uio@telconet.ec
      Ln_IdPlantilla,
      'Activo',
      SYSDATE,
      'afayala',
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
      152,--gtixi@netlife.net.ec
      Ln_IdPlantilla,
      'Activo',
      SYSDATE,
      'afayala',
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
      215,--pyl_gye@telconet.ec
      Ln_IdPlantilla,
      'Activo',
      SYSDATE,
      'afayala',
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
      216,--pyl_uio@telconet.ec
      Ln_IdPlantilla,
      'Activo',
      SYSDATE,
      'afayala',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Se creó la plantilla correctamente SAESUBIDA');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' 
                            || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
